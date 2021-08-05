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

    print "  The default delimiter is the pipe character, but can be changed via '-d=spaces' or '-d=comma'.  If 'spaces' is\n";
    print "  used file read and write may not be symmetric in that reading counts any number of consecutive spaces as a single\n";
    print "  delimiter, but always uses a one space char delimiter in output.\n\n";
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

  $header = ReadArgsForHeaderCount($argv,1,$argc-3);
  $delimiter = ReadArgsForDelimiter($argv,1,$argc-3);
  $delimiter_char = GetDelimiterChar($delimiter);

//--------------------------------------------------------------------------------------
//   Read in contents of input file, extract all column fields, test for consistent
//   column counts, and finally test user input column indices for being in legal range
//--------------------------------------------------------------------------------------

  $lines = file("$infile",FILE_IGNORE_NEW_LINES);
  $fields = SplitLinesToFields($lines,$header,$delimiter);
  $num_lines = count($fields);

  $num_columns = TestFieldCountConsistency($fields);
  TestIndicesLegal($extract_index,$num_columns);

//--------------------------------------------------------------------------------------
//   Go thru input file line by line and extract info for output
//--------------------------------------------------------------------------------------

  for($i=0;$i<$header;++$i) { print "$lines[$i]\n"; }

  for($i=0;$i<$num_lines;++$i)
  {
    $out_str = $fields[$i][$extract_index[0]];
    for($j=1;$j<$num_columns_to_extract;++$j)
    {
      $out_str = $out_str.$delimiter_char.$fields[$i][$extract_index[$j]];
    }
  print "$out_str\n";
  }

?>
