#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  exec_linux_commands_from_text_file.php [options] input_file\n\n";

    print "Examples:\n";
    print "  ./exec_linux_commands_from_text_file.php sample_data/commands.txt\n";
    print "  ./exec_linux_commands_from_text_file.php -v1 sample_data/commands.txt\n";
    print "  ./exec_linux_commands_from_text_file.php -v2 -r=3,7 sample_data/commands.txt\n\n";

    print "Simple but dangerous script.  Reads a plain text file that should contain one command\n";
    print "line system call per line.  And then runs them all in a loop.  Hang on!\n\n";  

    print "Running only a subset of lines in the input file can also be done.  Any line that starts\n";
    print "with '//' will always be skipped.  But '//' must be right at the start of the line - no\n";
    print "leading white space.  Whole line blocks can also be commented out.  Any line that starts \n";
    print "with '/*' will be skipped and execuction will not resume until after a line starting \n";
    print "with '*/' is found.  Again, these comment marks MUST be at the START of a line to work.\n\n";

    print "There are a couple of switch options available.  A '-v1' or '-v2' changes how verbose the \n";
    print "output will be.  By default, the output is the stdout stream of each command separated only \n";
    print "by a newline character.  Using '-v1' will add a line of '@' characters as an additional \n";
    print "divideer.  And '-v2' also prints out each command before it is executes and adds another \n";
    print" divider line of '-' characters.\n\n";

    print "The '-r' swtich allows a certain range or subset of lines to be run.  The syntax is '-r=a,b' \n";
    print "where 'a' and 'b' are the integer indices of the first and last lines to be executed.  \n";
    print "Indices start with zero and both end points are included so '-r=3,4' would run two lines \n";
    print "(lines 4 and 5).  If a line range is used, the comment marks '//', '/*' and '*/' are \n";
    print "still respected even if a comment block crosses an execution range.  Take care though \n";
    print "as comment lines are part of the line count determining the start and stop indices.\n\n";

    exit(0);
  }

//--------------------------------------------------------------------------------------
//   Make sure the last arg is a file that exists
//--------------------------------------------------------------------------------------

  $infile = $argv[$argc-1];

  if(!file_exists($infile))
  {
    print "\nInput file not found: $infile\n\n";
    exit(0);
  }

//--------------------------------------------------------------------------------------
//    Open command line and read in contents
//--------------------------------------------------------------------------------------

  $lines = file($infile,FILE_IGNORE_NEW_LINES);
  $num_lines = count($lines);

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------

  $verbosity = 0;
  $first_index = 0;
  $last_index = $num_lines-1;

  for($i=1;$i<$argc-1;++$i)
  {
    if($argv[$i]=="-v1") { $verbosity = 1;  continue; }
    if($argv[$i]=="-v2") { $verbosity = 2;  continue; }

    if(substr($argv[$i],0,3)=="-r=")
    {
      $fields = explode('=',$argv[$i]);
      $parts = explode(',',$fields[1]);
      $first_index = intval($parts[0]);
      $last_index = intval($parts[1]);
    }
  }

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------

  $keep_flag = array();
  for($i=0;$i<$num_lines;++$i) { $keep_flag[$i] = 1; }

  $comment_block = false;

  for($i=0;$i<$num_lines;++$i)
  {
    $chars = str_split($lines[$i]);
    if($chars[0]=='/' && $chars[1]=='/') { $keep_flag[$i]=0;  continue; }

    if($chars[0]=='/' && $chars[1]=='*') { $keep_flag[$i]=0;  $comment_block = true;  continue; }
    if($chars[0]=='*' && $chars[1]=='/') { $keep_flag[$i]=0;  $comment_block = false;  continue; }

    if($comment_block==true) { $keep_flag[$i]=0;  continue; }
  }

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------

  if($verbosity==2) { print "\n\n\n"; }
  for($i=0;$i<$num_lines;++$i)
  {
    if($i<$first_index) { continue; }
    if($i>$last_index)  { continue; }
    if($keep_flag[$i]==0) { continue; }

    if($verbosity>0) { print "@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@\n"; }
    if($verbosity==2)
    {
      print "$i) $lines[$i]\n";
      print "----------------------------------------------------------------------\n";
    }
    system("$lines[$i]");
    if($verbosity==2) { print "@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@\n\n\n"; }
  }
  if($verbosity==0 || $verbosity==1) { print "@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@\n"; }
  if($verbosity==2) { print "\n"; }

?>
