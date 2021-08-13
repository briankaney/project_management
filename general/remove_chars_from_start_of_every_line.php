#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  remove_chars_from_start_of_every_line.php [options] /path/input_file number_to_remove\n\n";

    print "Examples:\n";
    print "  ./remove_chars_from_start_of_every_line.php sample_data/directors.txt 5\n\n";

    print "  Simple script to remove a fixed number of characters from the start of every line of a text file.\n";
    print "  User specifies input file and number of characters to remove.  Capture output via redirect.  Not\n";
    print "  aware of delimiters - removes those too.  If a line is shorter than the number of characters to\n";
    print "  be removed, then a blank line is left.\n\n";

    print "Options:\n";
    print "  Use '-h=3' to specify a number of header lines.  The header section is output but not otherwise acted upon by\n";
    print "  by this script.  The default value is zero.\n\n";

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
  for($i=1;$i<=$argc-3;++$i)
  {
    if(substr($argv[$i],0,3)=="-h=")
    {
      $parts  = explode('=',$argv[$i]);
      $header = $parts[1];
      break;
    }
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
    $lines[$i] = substr($lines[$i],$num_to_remove);
  }

//--------------------------------------------------------------------------------------
//   Print the output
//--------------------------------------------------------------------------------------

  for($i=0;$i<$num_lines;++$i)
  {
    print "$lines[$i]\n";
  }

?>
