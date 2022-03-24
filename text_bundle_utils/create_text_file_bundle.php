#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  create_text_file_bundle.php [options] path/list_file\n\n";

    print "Examples:\n";
    print "  ./create_text_file_bundle.php sample_data/list.txt\n\n";

    print "  Takes a list of files and prints out the contents of the files in sequence.  So\n";
    print "  similar to running a concatentation, but the separate files have a single separator\n";
    print "  line between them in the output.  This can be used to easily undo the concatenation.\n";
    print "  I refer to these files as a 'text file bundle'.\n\n";

    print "  The separator line contains the original file name.  Other utils can search for and\n";
    print "  extract bundle members by index or file name.  The separate line starts with the,\n";
    print "  specific 23 character string of '@@@@@@--@@@@@@--@@@@@@|'.  This string is used as\n";
    print "  the key to knowing where the original files were glued together.  None of the files\n";
    print "  in the list should have lines starting with this exact string or extractions may\n";
    print "  fail later.\n\n";

    print "  Capture output via redirect.  Any files in the file list that are missing will be\n";
    print "  reported at the very end of the output.  The files that are found are still bundled\n";
    print "  so the warning can go unnoticed unless you run a 'tail' on the output.\n\n";

    exit(0);
  }

//--------------------------------------------------------------------------------------
//   Read in command line args
//--------------------------------------------------------------------------------------

  $list_file = $argv[$argc-1];

  if(!file_exists($list_file)) { print "Fatal error: list file $list_file not found\n\n";  exit(0); }

//--------------------------------------------------------------------------------------
//    Read in the list file
//--------------------------------------------------------------------------------------

  $list = file("$list_file",FILE_IGNORE_NEW_LINES);
  $num_list = count($list);

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------

  $missing_list = "";

  for($i=0;$i<$num_list;++$i) {
    if(!file_exists($list[$i])) { $missing_list = $missing_list.",".$list[$i];  continue; }

    print "@@@@@@--@@@@@@--@@@@@@|$list[$i]|\n";

    $lines = file("$list[$i]",FILE_IGNORE_NEW_LINES);
    $num_lines = count($lines);

    for($j=0;$j<$num_lines;++$j) { print "$lines[$j]\n"; }
  }

  if($missing_list!="") { print "\n\n\nWARNING: Missing files on the list $missing_list\n\n\n"; }

?>
