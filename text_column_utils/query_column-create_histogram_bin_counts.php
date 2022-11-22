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
    print "  via '-d=spaces', '-d=comma', or '-d=tab'.  One or more consecutive 'spaces' count as one\n";
    print "  delimiter.\n\n";
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
//   Open pointer to input file.  Skip any header lines.
//--------------------------------------------------------------------------------------

  $inf = fopen($infile,'r');

  for($i=0;$i<$header;++$i)
  {
    $line = fgets($inf);
    print "$line";
  }

//--------------------------------------------------------------------------------------
//   Set up array for bin string and count. Seed with the value from the first line.
//--------------------------------------------------------------------------------------

  $bin_name = Array();
  $bin_count = Array();
  $num_bins = 0;

//--------------------------------------------------------------------------------------
//   Step through each line and compare to all the bin strings so far. Either an existing
//   match is found (so increment that count) or a match is not found (add another bin 
//   name and count pair).
//--------------------------------------------------------------------------------------

  $tot = 0;
  while( ($line = fgets($inf)) !== false)
  {
    ++$tot;
    $line = rtrim($line,"\t");  
    $fields = SplitOneLineToFields($line,$delimiter);
    $num_fields = count($fields);

    if($column_index>=$num_fields) { print "\n\nFatal Error:  Max Column Specified Out Of File Bounds\v\n"; exit(-1); }

    $match_found=false;
    for($i=0;$i<$num_bins;++$i)
    {
      if($fields[$column_index]==$bin_name[$i]) { $match_found=true;  ++$bin_count[$i];  break; }
    }

    if($match_found==false)
    {
      $bin_name[$num_bins] = $fields[$column_index];
      $bin_count[$num_bins] = 1;
      ++$num_bins;
    }
  }

  fclose($inf);

//--------------------------------------------------------------------------------------
//   Print output
//--------------------------------------------------------------------------------------

  print "\n$tot Lines Total:\n\n";

  for($i=0;$i<$num_bins;++$i)
  {
    printf("%-30s %4d [%2.1f%%]\n",$bin_name[$i],$bin_count[$i],100*$bin_count[$i]/$tot);
  }

  print "\n";

?>
