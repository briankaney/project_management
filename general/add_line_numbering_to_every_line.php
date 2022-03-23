#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  add_line_numbering_to_every_line.php [options] /path/input_file\n\n";

    print "Examples:\n";
    print "  ./add_line_numbering_to_every_line.php sample_data/directors.txt\n";
    print "  ./add_line_numbering_to_every_line.php -h=2 sample_data/directors.txt\n\n";

    print "  Simple script to add line numbering to each line of a text file.  User specifies the\n";
    print "  input file.  By default, the insertion format is an integer and a single space in front\n";
    print "  the existing line.  Line numbers start with '1'.  Output is captured via redirect.\n\n";

    print "Options:\n";
    print "  Use '-h=3' to specify a number of header lines.  The header section is output but not \n";
    print "  included in the numbering  The default value is zero.\n\n";
    print "  ...add future options for exact formatting...\n\n";

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

  $header = 0;
  for($i=1;$i<=$argc-2;++$i)
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
//   Print the output
//--------------------------------------------------------------------------------------

  for($i=0;$i<$header;++$i) { print "$lines[$i]\n"; }

  $j = 1;
  for($i=$header;$i<$num_lines;++$i)
  {
    $str = sprintf("%d %s",$j,$lines[$i]);
    print "$str\n";
    ++$j;
  }

?>
