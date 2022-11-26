#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  query_int_column_min_max.php [options] /path/input_file column_index\n\n";

    print "Examples:\n";
    print "  ./query_int_column_min_max.php -d=tab sample_data/artists.txt 3\n";
    print "  ./query_int_column_min_max.php sample_data/directors.txt 0\n\n";

    print "  The input file should be plain text 'columns' with some delimiter.  The user specifes one\n";
    print "  column to investigate.  If the column contains numbers, this util will report the minimum\n";
    print "  and maximum values.  It also counts the total number of entries and the count of any\n";
    print "  empty or non-numerical entries.\n\n";

    print "  It is easy to customize the code for special cases.  The first version did a count of\n";
    print "  occurrences of '\N' which was used as a missing flag in one use case.  Lines can be added\n";
    print "  or commented out as needed.\n\n";

    print "Options:\n";
    print "  Use '-h=3' to specify a number of header lines.  The header section is output, but the extraction action is not\n";
    print "  applied to it.\n\n";

    print "  The default delimiter is the pipe character, but can be changed via '-d=spaces', '-d=comma', or '-d=tab'.  If\n";
    print "  'spaces' is used, file read and write may not be symmetric in that reading counts any number of consecutive\n";
    print "  spaces as a single delimiter, but always uses a one space char delimiter in output.\n\n";

    exit(0);
  }

  include 'library_text_columns.php';

//--------------------------------------------------------------------------------------
//   Read in command line argumants
//--------------------------------------------------------------------------------------

  $infile = $argv[$argc-2];

  if(!file_exists($infile))
  {
    print "\nInput file not found: $infile\n\n";
    exit(0);
  }

  $col_index = $argv[$argc-1];

  $header = ReadArgsForHeaderCount($argv,1,$argc-3);
  $delimiter = ReadArgsForDelimiter($argv,1,$argc-3);
  $delimiter_char = GetDelimiterChar($delimiter);

  $mode = "grep";

//--------------------------------------------------------------------------------------
//   Open pointer to input file.  Skip any header lines.  Then loop through rest of 
//   file and print out the requested column extraction.
//--------------------------------------------------------------------------------------

  $inf = fopen($infile,'r');

  for($i=0;$i<$header;++$i) { $line = fgets($inf); }

  $min = 999999999;
  $max = -999999999;
  $blank_count = 0;
  $missing_count = 0;
  $non_num_count = 0;

  $i = 0;
  while( ($line = fgets($inf)) !== false)
  {
    $line = rtrim($line,"\n");  
    $fields = SplitOneLineToFields($line,$delimiter);
    $num_fields = count($fields);
    ++$i;

    if($col_index>=$num_fields) { print "\n\nFatal Error:  Max Column Specified Out Of File Bounds\v\n"; exit(-1); }

    $col_value = $fields[$col_index];
    if($col_value=="")   { ++$blank_count;  continue; }
    if($col_value=="\N") { ++$missing_count;  continue; }
    if(!is_numeric($col_value)) { ++$non_num_count;  continue; }

    if($col_value<$min) { $min = $col_value; }
    if($col_value>$max) { $max = $col_value; }
  }

  fclose($inf);

  print "\nTotal: $i\n";
  $percent = sprintf("%3.1f",100*$blank_count/$i);
  print "Empty string [\"\"]: $blank_count [$percent %]\n";
  $percent = sprintf("%3.1f",100*$missing_count/$i);
  print "Missing flag [\"\N\"]: $missing_count [$percent %]\n";
  $percent = sprintf("%3.1f",100*$non_num_count/$i);
  print "Other non-numeric strings: $non_num_count [$percent %]\n";
  print "Min: $min\n";
  print "Max: $max\n\n";

?>
