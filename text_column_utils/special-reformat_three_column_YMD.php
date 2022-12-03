#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  special-reformat_three_column_YMD.php [options] /path/infile year_col_index\n\n";

    print "Examples:\n";
    print "  ./special-reformat_three_column_YMD.php -d=tab ./label.txt 8\n\n";

    print "  Simple script reads a text column file with three consecutive columns for a year,\n";
    print "  month and day (in that order).  The three columns can start anywhere and the starting\n";
    print "  column index is specified.  The output has the 3 columns replaced by a single\n";
    print "  column in m/d/y format.  Works on huge input files.\n\n";

    print "  The 'special' in the name indicates a util that has fairly specialized details but the\n";
    print "  code is easy to customize.  For instance, if the field order needs to differ from year month\n";
    print "  day or the output format needs to be tweaked.\n\n";

    print "Options:\n";
    print "  Use '-h=3' to specify a number of header lines.  The header section is output, but the extraction action is not\n";
    print "  applied to it.\n\n";

    print "  Default delimiter is the pipe character, but can be changed via '-d=spaces', '-d=comma' or '-d=tab'.  If 'spaces'\n";
    print "  is used file read and write may not be symmetric in that reading counts any number of consecutive spaces as a single\n";
    print "  delimiter, but always uses one single space as a delimiter in the output.\n\n";
    exit(0);
  }

  include 'library_text_columns.php';

//--------------------------------------------------------------------------------------
//   Read the required args and make sure the one that is the input is a file that 
//   exists.  Read optional args for number of header lines and delimiter character.
//--------------------------------------------------------------------------------------

  $infile = $argv[$argc-2];
  $index = $argv[$argc-1];

  if(!file_exists($infile))
  {
    print "\nInput file not found: $infile\n\n";
    exit(0);
  }

  $header = ReadArgsForHeaderCount($argv,1,$argc-3);
  $delimiter = ReadArgsForDelimiter($argv,1,$argc-3);
  $delimiter_char = GetDelimiterChar($delimiter);

//--------------------------------------------------------------------------------------
//  Open file pointer, loop through file and reformat.  
//--------------------------------------------------------------------------------------

  $inf = fopen($infile,'r');

  while( ($line = fgets($inf)) !== false)
  {
    $line = rtrim($line,"\n");  
    $fields = explode($delimiter_char,$line);  
    $num_fields = count($fields);
    
    if($num_fields<$index+2+1) { print "\nFatal Error:  Column index out of bounds for input file.\n\n";  exit(-1); }    

    $year  = $fields[$index];
    $month = $fields[$index+1];
    $day   = $fields[$index+2];

    if($year=="\N")  { $year = ""; }
    if($month=="\N") { $month = ""; }
    if($day=="\N")   { $day = ""; }

    if($month!="") { $month = sprintf("%02d",$month); }
    if($day!="")   { $day   = sprintf("%02d",$day); }
    $fields[$index] = $month."/".$day."/".$year;

    for($i=$index+3;$i<$num_fields;++$i) { $fields[$i-2] = $fields[$i]; }
    array_pop($fields);
    array_pop($fields);

    PrintOneLineOfFieldsAsOutput($fields,$delimiter);
  }

  fclose($inf);

?>
