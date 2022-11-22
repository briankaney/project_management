#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  set_columns-float_decimal_places.php [options] /path/input_file column_1,column_2,...,column_n value_1,value_2,...,value_n\n\n";

    print "Examples:\n";
    print "  ./set_columns-float_decimal_places.php -h=1 -d=spaces sample_data/float.txt 0,1,2 7,2,0\n\n";

    print "  Specify some number of column indices that contain floats and set a fixed\n";
    print "  number of decimal places.  Capture output via redirect.  There are three required arguments, the first\n";
    print "  being the input filename. The next two arguments are index strings.  The first such string is to indicate\n";
    print "  the columns to be worked on.  The column indexes start at zero and are comma delimited.  So '0,1,2' means work\n";
    print "  is to be done on the first three columns.  The second string is the resulting number of places after the decimal\n";
    print "  to keep.  So a '4,4,0' would keep 4 places in the columns 0 and 1 and none in column 2.\n\n";

    print "  Use '-h=3' to specify a number of header lines.  The header section is output but the decimal setting operation\n";
    print "  is not applied to it.  The default value is zero.\n\n";

    print "  Default delimiter is the pipe character, but can be changed via '-d=spaces', '-d=comma' or '-d=tab'.  If 'spaces'\n";
    print "  is used file read and write may not be symmetric in that reading counts any number of consecutive spaces as a single\n";
    print "  delimiter, but always uses one a one space delimiter in the output.\n\n";
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

  $decimal_places = explode(',',$argv[$argc-1]);
  $num_decimal_places = count($decimal_places);

  if($num_columns_to_set!=$num_decimal_places)
  {
    print "\nArgument miss-match: $num_columns_to_set column indices, $num_decimal_places decimal place values\n\n";
    exit(0);
  }

  $max_col_requested = $column_index[$num_columns_to_set-1];

  $header = ReadArgsForHeaderCount($argv,1,$argc-4);
  $delimiter = ReadArgsForDelimiter($argv,1,$argc-4);

//--------------------------------------------------------------------------------------
//   Open input file, skip header lines.
//--------------------------------------------------------------------------------------

  $inf = fopen($infile,'r');

  for($i=0;$i<$header;++$i)
  {
    $line = fgets($inf);
    print "$line";
  }

//--------------------------------------------------------------------------------------
//   Loop through input lines, explode into fields, loop through columns, do work
//--------------------------------------------------------------------------------------

  while( ($line = fgets($inf)) !== false)
  {      
    $line = trim($line);  // works without this due to sprintf below - but cleaner this way 
    $fields = SplitOneLineToFields($line,$delimiter);
    $num_fields = count($fields);

    if($max_col_requested>=$num_fields) { print "\n\nFatal Error:  Max Column Specified Out Of File Bounds\v\n"; exit(-1); }

    for($i=0;$i<$num_columns_to_set;++$i)
    {
      $num_digits = $decimal_places[$i]+2;
      $format_str = "%".$num_digits.".".$decimal_places[$i]."f";
      $fields[$column_index[$i]] = sprintf($format_str,$fields[$column_index[$i]]);
    }

    PrintOneLineOfFieldsAsOutput($fields,$delimiter);
  }

  fclose($inf);

?>
