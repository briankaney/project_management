#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  count_consecutive_duplicate_lines.php /path/input_file\n\n";

    print "Examples:\n";
    print "  ./count_consecutive_duplicate_lines.php sample_data/venues.txt\n\n";

    print "  Simple script that compares consecutive lines looking for duplicates.  The script steps\n";
    print "  through the file line by line.  Any lines that duplicate in a consecutive block are counted.\n";
    print "  The output is the count for these blocks followed by a space and the line itself.  If there\n";
    print "  are no consecutive lines anywhere, the output would be the same file back but with a '1 '\n";
    print "  prepended to the start of each line.  Duplicates that are not consecutive will not be caught.\n";
    print "  The full utility of this tool is in combination with linux sorting both before and after use.\n";
    print "  Capture output via a redirect.\n\n";

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

//--------------------------------------------------------------------------------------
//   Read in contents of input file
//--------------------------------------------------------------------------------------

  $lines = file("$infile",FILE_IGNORE_NEW_LINES);
  $num_lines = count($lines);

//--------------------------------------------------------------------------------------
//   Do something with file lines
//--------------------------------------------------------------------------------------

  $duplication_count = 1;
  for($i=1;$i<$num_lines;++$i)
  {
    if($lines[$i]==$lines[$i-1]) { ++$duplication_count;  continue; }
    else {
      $str = $duplication_count." ".$lines[$i-1];
      print "$str\n";
      $duplication_count = 1;
    }
  }

  $str = $duplication_count." ".$lines[$num_lines-1];
  print "$str\n";

?>
