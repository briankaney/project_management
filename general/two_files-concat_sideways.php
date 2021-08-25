#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)  {
    print "\n\nUsage:\n";
    print "  two_files-concat_sideways.php /path/input_file1 /path/input_file2\n\n";
    print "Examples:\n";
    print "  ./two_files-concat_sideways.php sample_data/directors.txt sample_data/venues.txt\n\n";

    print "  To 'concat_sideways' is my own terminolgy.  The usual file concatenation is a 'vertical' \n";
    print "  merger of lines.  That is, if we have two files\n\n";
    print "      File1             File2\n";
    print "        line_A            line_a\n";
    print "        line_B            line_b\n";
    print "        line_C            line_c\n\n";
    print "  the concatenated file will be\n\n";
    print "        line_A\n";
    print "        line_B\n";
    print "        line_C\n";
    print "        line_a\n";
    print "        line_b\n";
    print "        line_c\n\n";
    print "  But to concatenate sideways, the two files are 'glued' together horizontally.  Like this\n\n";
    print "        line_Aline_a\n";
    print "        line_Bline_b\n";
    print "        line_Cline_c\n\n";
    print "  The two files need only be plain text of any length.  The lines from the first file arg will\n";
    print "  be placed first in the output.  The two input files can be the same (hence, sideways concatenated\n";
    print "  with itself).  Apart from line ending characters, the line pair from the two files is run together\n";
    print "  with no addition or removal of white space, etc.  If the files have different numbers of lines, \n";
    print "  the missing lines from the shorter file are just treated as empty strings, so the output just has \n";
    print "  the remaining lines of the longer file.\n\n";
    print "  Capture the output via a redirect.\n\n";

    exit(0);
  }

//--------------------------------------------------------------------------------------
//   Read in command line argumants and do some testing
//--------------------------------------------------------------------------------------

  $infile1 = $argv[$argc-2];
  $infile2 = $argv[$argc-1];

  if(!file_exists($infile1)) {
    print "\nInput file not found: $infile1\n\n";
    exit(0);
  }

  if(!file_exists($infile2)) {
    print "\nInput file not found: $infile2\n\n";
    exit(0);
  }

//--------------------------------------------------------------------------------------
//   Read in contents of input file
//--------------------------------------------------------------------------------------

  $lines1 = file("$infile1",FILE_IGNORE_NEW_LINES);
  $num_lines1 = count($lines1);

  $lines2 = file("$infile2",FILE_IGNORE_NEW_LINES);
  $num_lines2 = count($lines2);

//--------------------------------------------------------------------------------------
//   Let 'num_lines' be the bigger of the two separate line counts.
//--------------------------------------------------------------------------------------

  if($num_lines1>=$num_lines2) { $num_lines = $num_lines1; }
  else                         { $num_lines = $num_lines2; }

//--------------------------------------------------------------------------------------
//   Loop thru 'num_lines'.  While both line1 and line2 arrays have members output the 
//   merged line.  When either array runs out, just output the remaining line by itself.
//--------------------------------------------------------------------------------------

  for($i=0;$i<$num_lines;++$i) {
    if($i>=$num_lines1) { $out_str = $lines2[$i]; }
    if($i>=$num_lines2) { $out_str = $lines1[$i]; }
    if($i<$num_lines1 && $i<$num_lines2) { $out_str = $lines1[$i].$lines2[$i]; }

    print "$out_str\n";
  }

?>
