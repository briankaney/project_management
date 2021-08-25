#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)  {
    print "\n\nUsage:\n";
    print "  two_files-interleave_lines.php /path/input_file1 /path/input_file2\n\n";
    print "Examples:\n";
    print "  ./two_files-interleave_lines.php sample_data/directors.txt sample_data/venues.txt\n";
    print "  ./two_files-interleave_lines.php sample_data/directors.txt sample_data/directors.txt\n";
    print "  ./two_files-interleave_lines.php sample_data/one_line-divider.txt sample_data/directors.txt\n\n";

    print "  By 'interleave' lines we mean to output the lines from the two inputs files in an\n";
    print "  alternating fashion.  That is, if we have two files\n\n";
    print "      File1             File2\n";
    print "        line_A            line_a\n";
    print "        line_B            line_b\n";
    print "        line_C            line_c\n\n";
    print "  the interleaved file will be\n\n";
    print "        line_A\n";
    print "        line_a\n";
    print "        line_B\n";
    print "        line_b\n";
    print "        line_C\n";
    print "        line_c\n\n";
    print "  The two files need only be plain text of any length.  The lines from the first file arg will\n";
    print "  lead off the first line in the output.  The two input files can be the same (thereby, just\n";
    print "  doubling each line).  No other changes to file lines occur, such as white space or delimiters.\n";
    print "  If the files have different numbers of lines, then the output just reverts to the file lines\n";
    print "  of the longer file when the shorter one runs out.\n\n";
    print "  There is one important special case.  If either of the input files contains just a single line,\n";
    print "  then that is handled differently.  Say one file has 12 lines and the other has just 1 line.  The\n";
    print "  file with the one line will be treated as if it was 12 duplicates of that line.  For example\n\n";
    print "      File1             File2\n";
    print "        line_A            one_line\n";
    print "        line_B\n";
    print "        line_C\n\n";
    print "  the interleaved file will be\n\n";
    print "        line_A\n";
    print "        one_line\n";
    print "        line_B\n";
    print "        one_line\n";
    print "        line_C\n";
    print "        one_line\n\n";
    print "  Whether the first or second arg is the one line file determines which one leads in the output.\n\n";
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

  if($num_lines1==0 || $num_lines2==0) { print "Error: One of the input files existed but had no lines\n"; }

//--------------------------------------------------------------------------------------
//   If just one of the input files has only a single line, then fill out the lines array
//   with duplicates to match the length of the longer input file.
//--------------------------------------------------------------------------------------

  if($num_lines1==1 && $num_lines2>1)
  {
    for($i=1;$i<$num_lines2;++$i) { $lines1[$i] = $lines1[0]; }
    $num_lines1 = $num_lines2;
  }

  if($num_lines2==1 && $num_lines1>1)
  {
    for($i=1;$i<$num_lines1;++$i) { $lines2[$i] = $lines2[0]; }
    $num_lines2 = $num_lines1;
  }

//--------------------------------------------------------------------------------------
//   Let 'num_lines' be the bigger of the two separate line counts.
//--------------------------------------------------------------------------------------

  if($num_lines1>=$num_lines2) { $num_lines = $num_lines1; }
  else                         { $num_lines = $num_lines2; }

//--------------------------------------------------------------------------------------
//   Loop thru 'num_lines'.  While both line1 and line2 arrays have members output the 
//   alternating line pair.  When either array runs out, just output the remaining line by itself.
//--------------------------------------------------------------------------------------

  for($i=0;$i<$num_lines;++$i) {
    if($i>=$num_lines1) { $out_str = $lines2[$i]; }
    if($i>=$num_lines2) { $out_str = $lines1[$i]; }
    if($i<$num_lines1 && $i<$num_lines2) { $out_str = $lines1[$i]."\n".$lines2[$i]; }

    print "$out_str\n";
  }

?>
