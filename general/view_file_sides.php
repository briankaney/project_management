#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  view_file_sides.php [options] /path/input_file\n\n";

    print "Examples:\n";
    print "  ./view_file_sides.php sample_data/directors.txt\n";
    print "  ./view_file_sides.php -pos=end -num=10 sample_data/directors.txt\n\n";

    print "  Simple script to print out a fixed number of characters from the start or end of every line\n";
    print "  of a text file.  User specifies input file and number of characters to print.  Capture output \n";
    print "  via redirect.  Useful to scan files with huge lines.  Sort of a 'sideways' version of linux\n";
    print "  'head' or 'tail'.\n\n";

    print "Options:\n";
    print "  Use '-pos=end' to print the last characters from each line.  Printing the start of each line\n";
    print "  is the default.  Use '-num=10' to set the number of characters to print (default is 50).\n\n";

    exit(0);
  }

//--------------------------------------------------------------------------------------
//   Read the required args and make sure the one that is the input is a file that 
//   exists.  Read optional args for number of header lines and delimiter character.
//--------------------------------------------------------------------------------------

  $infile = $argv[$argc-1];

  if(!file_exists($infile))
  {
    print "\nInput file not found: $infile\n\n";
    exit(0);
  }

  $mode = "start";
  $num_keep = 20;
  for($i=1;$i<=$argc-2;++$i)
  {
    if($argv[$i]=="-pos=end") { $mode = "end"; }
    if(substr($argv[$i],0,5)=="-num=")
    {
      $parts  = explode('=',$argv[$i]);
      $num_keep = $parts[1];
    }
  }

//--------------------------------------------------------------------------------------
//   Read in contents of input file
//--------------------------------------------------------------------------------------

  $lines = file("$infile",FILE_IGNORE_NEW_LINES);
  $num_lines = count($lines);

//--------------------------------------------------------------------------------------
//   Print the output
//--------------------------------------------------------------------------------------

  for($i=0;$i<$num_lines;++$i)
  {
    if($mode=="start") { $str = substr($lines[$i],0,$num_keep); }
    if($mode=="end")   { $str = substr($lines[$i],strlen($lines[$i])-$num_keep); }

    print "$str\n";
  }

?>
