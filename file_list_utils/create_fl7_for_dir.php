#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  create_fl7_for_dir.php input_dir\n\n";

    print "Examples:\n";
    print "  ./create_fl7_for_dir.php ./\n\n";

    print "  A system call is made to a recursive linux directory listing command.  Specifically, the\n";
    print "  a 'ls -R -A -l --full-time' version of the command.  This script parses through the resulting\n";
    print "  text and outputs a file with some format modifications.  The linux system call output might\n";
    print "  look something like:\n\n";
 
    print "--------------------------------------------------------\n";   
    print "sample/:\n";
    print "total 8\n";
    print "-rwxr-xr-x 1 bkaney bkaney 4242 2022-03-04 14:35:38.007652700 -0600 one.txt\n";
    print "drwxr-xr-x 1 bkaney bkaney 4096 2022-03-04 14:38:45.981728400 -0600 work\n\n";
    print "sample/work:\n";
    print "total 16\n";
    print "drwxr-xr-x 1 bkaney bkaney 4096 2022-03-04 14:38:18.936968300 -0600 sub\n";
    print "-rwxr-xr-x 1 bkaney bkaney 5164 2022-03-04 14:36:13.645950000 -0600 three.jpg\n";
    print "-rwxr-xr-x 1 bkaney bkaney 4134 2022-03-04 14:36:23.485516000 -0600 two.png\n\n";
    print "sample/work/sub:\n";
    print "total 16\n";
    print "-rwxr-xr-x 1 bkaney bkaney 4949 2022-03-04 14:37:57.519683400 -0600 five.txt\n";
    print "-rwxr-xr-x 1 bkaney bkaney 6118 2022-03-04 14:37:57.521892800 -0600 four.txt\n";
    print "--------------------------------------------------------\n\n";   

    print "  The '-R' switchs means recursive, the '-A' means include all hidden and system entries (except\n";
    print "  not '.' and '..') and '-l' refers to a longer listing.  The system output is broken into blocks\n";
    print "  for each sub-directory.  The output of this script 'flattens' that out so each and every line is\n";
    print "  a unique file.  An empty sub-directory will not appear in the output at all.\n\n";

    print "  The final output has 7 '|' delimited columns ('fl7' refers to file lines seven column output\n";
    print "  The pipe character usage allows for file names with spaces to be preserved.  The sample linux\n";
    print "  output above is parsed into the following:\n\n";

    print "--------------------------------------------------------\n";   
    print "sample/|one.txt|txt|4242|2022-03-04|14:35:38|-0600|\n";
    print "sample/work/|three.jpg|jpg|5164|2022-03-04|14:36:13|-0600|\n";
    print "sample/work/|two.png|png|4134|2022-03-04|14:36:23|-0600|\n";
    print "sample/work/sub/|five.txt|txt|4949|2022-03-04|14:37:57|-0600|\n";
    print "sample/work/sub/|four.txt|txt|6118|2022-03-04|14:37:57|-0600|\n";
    print "--------------------------------------------------------\n\n";   

    print "  The columns in order are the full directory path, file name, filename extension, file size,\n";
    print "  file date, file time (with the fractional seconds truncated), and the time zone shift.\n";
    print "  The separate column for the file extension is so that the file can be sorted by that field.\n";
    print "  This script can take a while for a huge file tree.  Capture output via redirect.\n\n";

    exit(0);
  }

  include 'library_file_management.php';

//--------------------------------------------------------------------------------------
//   Read the one command line arg needed.
//--------------------------------------------------------------------------------------

  $in_dir = $argv[1];

//--------------------------------------------------------------------------------------
//   Run the dir listing system command.  Parse the response into a string array.
//--------------------------------------------------------------------------------------

  $response = shell_exec("ls -R -A -l --full-time $in_dir");
  $lines = explode(PHP_EOL,$response);
  $num_lines = count($lines);

//--------------------------------------------------------------------------------------
//   Set up arrays for the output fields.  Step through the dir listing strings to fill these arrays.
//--------------------------------------------------------------------------------------

  $paths = array();
  $names = array();
  $num_bytes = array();
  $file_date = array();
  $file_time = array();
  $zone_shift = array();

  $k=0;     //---counter for the new array defined above and being filled below
  for($i=0;$i<$num_lines;++$i) {

    if($i<$num_lines-1) {
      if(substr($lines[$i+1],0,5)=="total") {
        $fields = explode(':',$lines[$i]);
        $current_block_dir = $fields[0]."/";
      }
    }

    if(substr($lines[$i],0,1)=="-") {
      $fields = preg_split('/ +/',$lines[$i]);

      $num_bytes[$k] = $fields[4];
      $file_date[$k] = $fields[5];
      $str = explode('.',$fields[6]);
      $file_time[$k] = $str[0];
      $zone_shift[$k] = $fields[7];

      $paths[$k] = $current_block_dir; 

      $names[$k] = $fields[8]; 
      for($j=9;$j<count($fields);++$j) { $names[$k] = $names[$k]." ".$fields[$j]; }

      ++$k;
    }
  }

  $num_files = count($names);

//--------------------------------------------------------------------------------------
//   Print out the file listing fields in a 7 column text line.
//--------------------------------------------------------------------------------------

  for($i=0;$i<$num_files;++$i) {
    $ext = getExtFromFullFilename($names[$i]);
    $paths[$i] = str_replace("//","/",$paths[$i]);
    print "$paths[$i]|$names[$i]|$ext|$num_bytes[$i]|$file_date[$i]|$file_time[$i]|$zone_shift[$i]|\n";
  }

?>
