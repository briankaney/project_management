#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  query_column-create_histogram_bin_counts.php [options] /path/input_file column_index\n\n";

    print "Examples:\n";
    print "  ./query_column-create_histogram_bin_counts.php sample_data/directors.txt 2\n";
    print "  ./query_column-create_histogram_bin_counts.php -h=2 sample_data/directors-extra-ints.txt 4\n";
    print "  ./query_column-create_histogram_bin_counts.php -d=comma sample_data/directors.comma.txt 2\n\n";
 
    print "  A utility to take one column in a delimited text column file and make histogram bin counts\n";
    print "  of all the unique strings/values that populate that column. The two required arguments are\n";
    print "  the input filename and the column index (starts with zero). Capture output via a redirect.\n\n";
  
    print "Options:\n";
    print "  Use '-h=2' to specify a number of header lines.  The default is zero.  The header section\n";
    print "  is skipped in the analysis.  The default delimiter is the pipe character, but can be changed\n";
    print "  via '-d=spaces' or '-d=comma'.  One or more consecutive 'spaces' count as one delimiter.\n\n";
    exit(0);
  }

  include 'library_text_columns.php';

//--------------------------------------------------------------------------------------
//   Read in command line args
//--------------------------------------------------------------------------------------

  $infile = $argv[$argc-2];
  $column_index = $argv[$argc-1];

  if(!file_exists($infile))
  {
    print "\nInput file not found: $infile\n\n";
    exit(0);
  }

  $header = ReadArgsForHeaderCount($argv,1,$argc-3);
  $delimiter = ReadArgsForDelimiter($argv,1,$argc-3);

//--------------------------------------------------------------------------------------
//   Read in contents of input file
//--------------------------------------------------------------------------------------

  $lines = file("$infile",FILE_IGNORE_NEW_LINES);
  $fields = SplitLinesToFields($lines,$header,$delimiter);
  $num_lines = count($fields);

  $num_columns = TestFieldCountConsistency($fields);
  TestIndicesLegal($column_index,$num_columns);  //--not finished yet

//--------------------------------------------------------------------------------------
//   Set up array for bin string and count. Seed with the value from the first line.
//--------------------------------------------------------------------------------------

  $bin_name = Array();
  $bin_count = Array();
  
  $bin_name[0] = $fields[0][$column_index];
  $bin_count[0] = 1;
  $num_bins = count($bin_name);

//--------------------------------------------------------------------------------------
//   Step through each line and compare to all the bin strings so far. Either an existing
//   match is found (so increment that count) or a match is not found (add another bin 
//   name and count pair).
//--------------------------------------------------------------------------------------

  for($i=1;$i<$num_lines;++$i)
  {
    $match_found=false;
    for($j=0;$j<$num_bins;++$j)
    {
      if($fields[$i][$column_index]==$bin_name[$j]) { $match_found=true;  ++$bin_count[$j];  break; }
    }

    if($match_found==false)
    {
      $bin_name[$num_bins] = $fields[$i][$column_index];
      $bin_count[$num_bins] = 1;
      ++$num_bins;
    }
  }

//--------------------------------------------------------------------------------------
//   Print output
//--------------------------------------------------------------------------------------

  print "\n$num_lines Total:\n\n";

  for($i=0;$i<$num_bins;++$i)
  {
    printf("%-30s %4d [%2.1f%%]\n",$bin_name[$i],$bin_count[$i],100*$bin_count[$i]/$num_lines);
  }

  print "\n";

?>
