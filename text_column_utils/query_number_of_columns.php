#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  query_number_of_columns.php [options] /path/input_file\n\n";

    print "Examples:\n";
    print "  ./query_number_of_columns.php sample_data/show.txt\n";
    print "  ./query_number_of_columns.php -h=1 sample_data/show.txt\n";
    print "  ./query_number_of_columns.php -mode=raw sample_data/show.txt\n";
    print "  ./query_number_of_columns.php -d=comma -mode=raw_numbered sample_data/directors.comma.txt\n\n";
 
    print "  A utility to interrogate the number of fields in a delimited text column file.  The last\n";
    print "  arguments is the input file.  Capture output via a redirect.\n\n";
  
    print "Options:\n";
    print "  Use '-h=2' to specify a number of header lines.  The default is zero.  The header section\n";
    print "  is skipped in the analysis.  The default delimiter is the pipe character, but can be changed\n";
    print "  via '-d=spaces' or '-d=comma'.  One or more consecutive 'spaces' count as one delimiter.\n";
    print "  The default output mode is for the field count values for every line to be accumulated into bins\n";
    print "  and then a summary is printed of the bin results.  But '-mode=raw' can be used to instead get\n";
    print "  a simple list of the number of fields on each line.  And '-mode=raw_numbered' is similar but\n";
    print "  with line numbers added.\n\n";
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

  $mode = "stats";

  for($i=1;$i<=$argc-2;++$i)
  {
    if($argv[$i]=="-mode=raw")          { $mode = "raw";   break; }
    if($argv[$i]=="-mode=raw_numbered") { $mode = "raw_numbered"; break; }
  }

//--------------------------------------------------------------------------------------
//   Read in contents of input file
//--------------------------------------------------------------------------------------

  $lines = file("$infile",FILE_IGNORE_NEW_LINES);
  $num_lines = count($lines);

//--------------------------------------------------------------------------------------
//   Fill array with the column count in each non-header line
//--------------------------------------------------------------------------------------

//  redo with this:  $fields = SplitLinesToFields($lines,$header_len,$delimiter), what follows is older and needlessly complex.
  $num_cols = Array();
  for($i=$header;$i<$num_lines;++$i)
  {
    if($delimiter=="spaces") { $columns = preg_split('/ +/',$lines[$i]); }
    if($delimiter=="comma")  { $columns = explode(',',$lines[$i]); }
    if($delimiter=="pipe")   { $columns = explode('|',$lines[$i]); }
    $num_cols[$i-$header] = count($columns);
  }

//--------------------------------------------------------------------------------------
//   Get max, min, and bin counts of the column counts from the last step
//--------------------------------------------------------------------------------------

  $max_col = 0;
  $min_col = 999999;
  for($i=0;$i<$num_lines-$header;++$i)
  {
    if($num_cols[$i]>$max_col) { $max_col = $num_cols[$i]; }
    if($num_cols[$i]<$min_col) { $min_col = $num_cols[$i]; }
  }

  $bin_count = Array();
  $num_bins = $max_col-$min_col+1;
  for($i=0;$i<$num_bins;++$i) { $bin_count[$i] = 0; }
  for($i=0;$i<$num_lines-$header;++$i) { ++$bin_count[$num_cols[$i]-$min_col]; }

//--------------------------------------------------------------------------------------
//   Get max, min, and bin counts of the column counts from the last step
//--------------------------------------------------------------------------------------

  if($mode == "raw") {
    print "\n";
    for($i=0;$i<$num_lines-$header;++$i) { print "$num_cols[$i]\n"; }
    print "\n";
  }

  if($mode == "raw_numbered") {
    print "\n";
    for($i=0;$i<$num_lines-$header;++$i)
    {
      $str = sprintf("Line(s) %3d : %4d columns\n",$i+1,$num_cols[$i]);
      print "$str";
    }
    print "\n";
  }

  if($mode == "stats") {
    print "\n";
    for($i=0;$i<$num_bins;++$i)
    {
      if($bin_count[$i]==0) { continue; }
      $str = sprintf("%-4d line(s) have %4d columns\n",$bin_count[$i],$min_col+$i);
      print "$str";
    }
    print "\n";
  }

?>
