#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  query_empty_columns.php [options] /path/input_file\n\n";

    print "Examples:\n";
    print "  ./query_empty_columns.php sample_data/show.txt\n";
    print "  ./query_empty_columns.php -ignore-last sample_data/show.txt\n\n";
 
    print "  A utility to look for empty fields in a delimited text column file.  The last argument\n";
    print "  is the input file.  Capture output via a redirect.\n\n";
  
    print "Options:\n";
    print "  Use '-h=2' to specify a number of header lines.  The default is zero.  The header section\n";
    print "  is skipped in the analysis.  The default delimiter is the pipe character, but can be changed\n";
    print "  via '-d=comma'.  Space delimited files can't support 'empty' fields.  The option '-ignore-last'\n";
    print "  can be used to skip any reports of missing fields in the last field.\n\n";
    exit(0);
  }

  include 'library_text_columns.php';

//--------------------------------------------------------------------------------------
//   Read in command line args
//--------------------------------------------------------------------------------------

  $infile = $argv[$argc-1];

  if(!file_exists($infile))
  {
    print "\nInput file not found: $infile\n\n";
    exit(0);
  }

  $header = ReadArgsForHeaderCount($argv,1,$argc-2);
  $delimiter = ReadArgsForDelimiter($argv,1,$argc-2);
  $delimiter_char = GetDelimiterChar($delimiter);

  $mode = "all";

  for($i=1;$i<=$argc-2;++$i)
  {
    if($argv[$i]=="-ignore-last") { $mode = "ignore";   break; }
  }

//--------------------------------------------------------------------------------------
//   Read in contents of input file
//--------------------------------------------------------------------------------------

  $lines = file("$infile",FILE_IGNORE_NEW_LINES);
  $fields = SplitLinesToFields($lines,$header,$delimiter);
  $num_lines = count($fields);

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------

  print "\n";
  for($i=0;$i<$num_lines;++$i) {
    $num_fields = count($fields[$i]);

    $empties_found = false;
    for($j=0;$j<$num_fields;++$j) {
      if(strlen($fields[$i][$j])==0) {
        if($j==$num_fields-1 && $mode=="ignore") { continue; }

        if($empties_found==false) {
          $str = sprintf("Line %d empties: %d ",$i+1,$j);
          print "Line $i empties: ";
          }
        $empties_found = true;
        print "$j ";
      }
    }
    if($empties_found==true) { print "\n"; }
  }
  print "\n";

//--------------------------------------------------------------------------------------

?>
