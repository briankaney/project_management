#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  combine_file_to_one_line.php /path/input_file\n\n";

    print "Examples:\n";
    print "  ./combine_file_to_one_line.php sample_data/short_lines.txt\n\n";

    print "  Simple script that reads a text file and outputs all the lines in a single output line\n";
    print "  of text.  Capture output via redirect.  Output does not include a final '\\n' character.\n";
    print "  Useful for putting text into a long array listing or a json string.\n\n";

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

/*
  $mode = "start";
  $num_keep = 50;
  for($i=1;$i<=$argc-2;++$i)
  {
    if($argv[$i]=="-pos=end") { $mode = "end"; }
    if(substr($argv[$i],0,5)=="-num=")
    {
      $parts  = explode('=',$argv[$i]);
      $num_keep = $parts[1];
    }
  }
*/

//--------------------------------------------------------------------------------------
//   Read in contents of input file
//--------------------------------------------------------------------------------------

  $lines = file("$infile",FILE_IGNORE_NEW_LINES);
  $num_lines = count($lines);

//--------------------------------------------------------------------------------------
//   Print the output
//--------------------------------------------------------------------------------------

  for($i=0;$i<$num_lines;++$i) { print "$lines[$i]"; }

?>
