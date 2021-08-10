#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  set_columns-pad_int_leading_zeros.php [options] /path/input_file column_1,column_2,...,column_n value_1,value_2,...,value_n\n\n";

    print "Examples:\n";
    print "  ./set_columns-pad_int_leading_zeros.php -h=2 sample_data/directors-extra-ints.txt 0,1 2,3\n\n";

    print "  Specify some number of column indices that contain integers and reset them to a specified fixed length\n"; 
    print "  by padding with leading zeros.  Capture output via redirect.  There are three required arguments, the first\n";
    print "  being the input filename. The next two arguments are index strings.  The first such string is to indicate the\n";
    print "  columns to be worked on.  The column indexes start at zero and are comma delimited.  So '0,1' means work is\n";
    print "  to be done on the first two columns.  The second string is the resulting number of places for these integer\n";
    print "  values.  So a '2,3,' would pad integers to 2 places in column 0 and 3 places in column 1.\n\n";

    print "Options:\n";
    print "  Use '-h=3' to specify a number of header lines.  The header section is output, but the extraction action is not\n";
    print "  applied to it.\n\n";

    print "  Default delimiter is the pipe character, but can be changed via '-d=spaces' or '-d=comma'.  If 'spaces' is used\n";
    print "  file read and write may not be symmetric in that reading counts any number of consecutive spaces as a single\n";
    print "  delimiter, but always uses one single space as a delimiter in the output.\n\n";
    exit(0);
  }

  include 'library_text_columns.php';

//--------------------------------------------------------------------------------------
//   Read in command line argumants and do some testing
//--------------------------------------------------------------------------------------

  $infile = $argv[$argc-3];

  if(!file_exists($infile))
  {
    print "\nInput file not found: $infile\n\n";
    exit(0);
  }

  $column_index = explode(',',$argv[$argc-2]);
  $num_columns_to_set = count($column_index);

  $digits = explode(',',$argv[$argc-1]);
  $num_digits_to_set = count($digits);

  if($num_columns_to_set!=$num_digits_to_set)
  {
    print "\nArgument miss-match: $num_columns_to_set column indices, $num_digits_to_set digit values\n\n";
    exit(0);
  }

  $header = ReadArgsForHeaderCount($argv,1,$argc-4);
  $delimiter = ReadArgsForDelimiter($argv,1,$argc-4);

//--------------------------------------------------------------------------------------
//   Read in contents of input file
//--------------------------------------------------------------------------------------

  $lines = file("$infile",FILE_IGNORE_NEW_LINES);
  $fields = SplitLinesToFields($lines,$header,$delimiter);
  $num_lines = count($fields);

  $num_columns = TestFieldCountConsistency($fields);
  TestIndicesLegal($column_index,$num_columns);

//--------------------------------------------------------------------------------------
//   Go thru input file line by line and add leading zero for integer columns requested
//--------------------------------------------------------------------------------------

  for($i=0;$i<$num_lines;++$i)
  {
    for($j=0;$j<$num_columns_to_set;++$j)
    {
      $format_str = "%0".$digits[$j]."d";
      $fields[$i][$column_index[$j]] = sprintf($format_str,$fields[$i][$column_index[$j]]);
    }
  }

//--------------------------------------------------------------------------------------
//   Print the output
//--------------------------------------------------------------------------------------

  for($i=0;$i<$header;++$i) { print "$lines[$i]\n"; }
  PrintFieldsAsOutput($fields,$delimiter);

?>
