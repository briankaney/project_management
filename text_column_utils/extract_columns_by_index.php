#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  extract_columns_by_index.php [options] /path/input_file column_1,column_2, ... ,column_n\n\n";

    print "Examples:\n";
    print "  ./extract_columns_by_index.php sample_data/directors.txt 0,1,2\t\t\t(output whole file, but drop trailing empties)\n"; 
    print "  ./extract_columns_by_index.php sample_data/directors.txt 2,0\t\t\t\t(extract 3rd and 1st columns in that order)\n";
    print "  ./extract_columns_by_index.php sample_data/directors.txt 0,1,0,3\t\t\t(duplicating a column and keep trailing empties)\n";
    print "  ./extract_columns_by_index.php -h=1 sample_data/directors-with-header.txt 2,0\t\t(treat first line as header)\n";
    print "  ./extract_columns_by_index.php -d=comma sample_data/directors.comma.txt 2,0\t\t(use a comma as the delimiter)\n\n";

    print "  The input file should be plain text 'columns' with some delimiter.  The output is also plain text columns with\n";
    print "  the same delimiter.  The output is captured via redirect.  The output is a subset of columns from the original\n";
    print "  file, output in any order.  The last two arguments must be the input file followed by an 'extraction string'.\n";
    print "  The extraction string is a comma delimited list of the column indices in the original file to be extracted.\n";
    print "  Column indices start at 0.  Indices can be repeated to duplicate fields.  NOTE: if the original file has lines\n";
    print "  ending with a trailing column delimiter character, then there is a final column of 'empty' fields present.\n";
    print "  This can be preserved or not by including that last index in the extraction string.\n\n";
  
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

  $extract_index = explode(',',$argv[$argc-1]);
  $num_columns_to_extract = count($extract_index);

  $max_col_requested = 0;
  for($i=0;$i<$num_columns_to_extract;++$i)
  {
    if($extract_index[$i]>$max_col_requested) { $max_col_requested=$extract_index[$i]; }
  }

  $header = ReadArgsForHeaderCount($argv,1,$argc-3);
  $delimiter = ReadArgsForDelimiter($argv,1,$argc-3);
  $delimiter_char = GetDelimiterChar($delimiter);

//--------------------------------------------------------------------------------------
//   Open pointer to input file.  Copy out any header lines.  Then loop through rest of 
//   file and print out the requested column extraction.
//--------------------------------------------------------------------------------------

  $inf = fopen($infile,'r');

  for($i=0;$i<$header;++$i)
  {
    $line = fgets($inf);
    print "$line";
  }

  while( ($line = fgets($inf)) !== false)
  {
    $line = trim($line);  
    $fields = SplitOneLineToFields($line,$delimiter);
    $num_fields = count($fields);

    if($max_col_requested>=$num_fields) { print "\n\nFatal Error:  Max Column Specified Out Of File Bounds\v\n"; exit(-1); }
    
    $out_str = $fields[$extract_index[0]];
    for($j=1;$j<$num_columns_to_extract;++$j)
    {
      $out_str = $out_str.$delimiter_char.$fields[$extract_index[$j]];
    }
    print "$out_str\n";
  }

  fclose($inf);

?>
