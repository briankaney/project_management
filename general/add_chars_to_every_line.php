#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  add_chars_to_every_line.php [options] /path/input_file /path/added_char_file\n\n";

    print "Examples:\n";
    print "  ./add_chars_to_every_line.php sample_data/directors.txt sample_data/one_line-one_space.txt\n";
    print "  ./add_chars_to_every_line.php -pos=end sample_data/directors.txt sample_data/one_line-redirect.txt\n";
    print "  ./add_chars_to_every_line.php -h=2 sample_data/directors.txt sample_data/one_line-one_space.txt\n\n";

    print "  Simple script to add a fixed string to the start or end of every line of a text file.\n";
    print "  User specifies the input file.  The string to be added should be the sole contents of \n";
    print "  another user specified file.  That file is expected to contain only a single line (but \n";
    print "  if it has more, the first is used and others ignored).  Output is captured via redirect.\n\n";

    print "Options:\n";
    print "  Use '-h=3' to specify a number of header lines.  The header section is output but not \n";
    print "  otherwise acted upon by this script.  The default value is zero.  Use '-pos=end' to \n";
    print "  append the added string to the end of each line.  Prepending to the start of each line \n";
    print "  is the default.\n\n";

    exit(0);
  }

//--------------------------------------------------------------------------------------
//   Read the required args and make sure the one that is the input is a file that 
//   exists.  Read optional args for number of header lines and delimiter character.
//--------------------------------------------------------------------------------------

  $infile = $argv[$argc-2];
  $string_file = $argv[$argc-1];

  if(!file_exists($infile))
  {
    print "\nInput file not found: $infile\n\n";
    exit(0);
  }

  if(!file_exists($string_file))
  {
    print "\nInput file not found: $string_file\n\n";
    exit(0);
  }

  $header = 0;
  $add_mode = "start";
  for($i=1;$i<=$argc-3;++$i)
  {
    if(substr($argv[$i],0,3)=="-h=")
    {
      $parts  = explode('=',$argv[$i]);
      $header = $parts[1];
      break;
    }

    if($argv[$i]=="-pos=end") { $add_mode = "end"; }
  }

//--------------------------------------------------------------------------------------
//   Read in contents of input file
//--------------------------------------------------------------------------------------

  $lines = file("$infile",FILE_IGNORE_NEW_LINES);
  $num_lines = count($lines);

  $string = file("$string_file",FILE_IGNORE_NEW_LINES);

//--------------------------------------------------------------------------------------
//   Do something with file lines
//--------------------------------------------------------------------------------------

  for($i=$header;$i<$num_lines;++$i)
  {
    if($add_mode=="start") { $lines[$i] = $string[0].$lines[$i]; }
    if($add_mode=="end")   { $lines[$i] = $lines[$i].$string[0]; }
  }

//--------------------------------------------------------------------------------------
//   Print the output
//--------------------------------------------------------------------------------------

  for($i=0;$i<$num_lines;++$i)
  {
    print "$lines[$i]\n";
  }

?>
