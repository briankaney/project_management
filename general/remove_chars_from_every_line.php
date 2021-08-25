#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  remove_chars_from_every_line.php [options] /path/input_file number_to_remove\n\n";

    print "Examples:\n";
    print "  ./remove_chars_from_every_line.php sample_data/directors.txt 5\n";
    print "  ./remove_chars_from_every_line.php -pos=end sample_data/directors.txt 1\n\n";

    print "  Simple script to remove a fixed number of characters from the start or end of every line of \n";
    print "  a text file.  User specifies input file and number of characters to remove.  Capture output \n";
    print "  via redirect.  Not aware of delimiters - removes those too.  If a line is shorter than the \n";
    print "  number of characters to be removed, then a blank line is left.\n\n";

    print "Options:\n";
    print "  Use '-h=3' to specify a number of header lines.  The header section is output but not otherwise \n";
    print "  acted upon by this script.  The default value is zero.  Use '-pos=end' to remove characters \n";
    print "  from the end of each line.  Removing from the start of each line is the default.\n\n";

    exit(0);
  }

//--------------------------------------------------------------------------------------
//   Read the required args and make sure the one that is the input is a file that 
//   exists.  Read optional args for number of header lines and delimiter character.
//--------------------------------------------------------------------------------------

  $infile = $argv[$argc-2];

  if(!file_exists($infile))
  {
    print "\nInput file not found: $infile\n\n";
    exit(0);
  }

  $num_to_remove = $argv[$argc-1];

  $header = 0;
  $remove_mode = "start";
  for($i=1;$i<=$argc-3;++$i)
  {
    if(substr($argv[$i],0,3)=="-h=")
    {
      $parts  = explode('=',$argv[$i]);
      $header = $parts[1];
      break;
    }
    if($argv[$i]=="-pos=end") { $remove_mode = "end"; }
  }

//--------------------------------------------------------------------------------------
//   Read in contents of input file
//--------------------------------------------------------------------------------------

  $lines = file("$infile",FILE_IGNORE_NEW_LINES);
  $num_lines = count($lines);

//--------------------------------------------------------------------------------------
//   Do something with file lines
//--------------------------------------------------------------------------------------

  for($i=$header;$i<$num_lines;++$i)
  {
    if($remove_mode=="start") { $lines[$i] = substr($lines[$i],$num_to_remove); }
    if($remove_mode=="end")   { $lines[$i] = substr($lines[$i],0,strlen($lines[$i])-$num_to_remove); }
  }

//--------------------------------------------------------------------------------------
//   Print the output
//--------------------------------------------------------------------------------------

  for($i=0;$i<$num_lines;++$i)
  {
    print "$lines[$i]\n";
  }

?>
