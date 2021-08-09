#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  needs a redo...    ls_listing-create_file_list_for_dir.php in_dir\n\n";

    print "Examples:\n";
    print "  ./ls_listing-create_file_list_for_dir.php path/MyDir\n\n";

    print "  Input a text file captured from running a linux directory listing.  Specifically,\n";
    print "  a 'ls -R -l --full-time' version of the command.  Script parses through the\n";
    print "  input, which is in directory blocks with separating lines, and creates a simple\n";
    print "  output list of one line per file with information in '|' delimited columns.\n";
    print "  The pipe character allows for file names with spaces to be preserved.\n\n";

    print "  The default listing has columns for path, name, file size, file date , file time,\n";
    print "  and time zone shift.  But there are a couple other options.  The option '-sp=#'\n";
    print "  (shave path) can removed a fixed number of characters from the start of the path\n";
    print "  name.  For instance, if every single file path starts with '/mnt/c/User/...', then\n";
    print "  to save space a portion could be chopped from the start.  The value given is the\n";
    print "  number of characters to remove.  The '-sc=size' (start column) option will just\n";
    print "  reorder the columns so the the file size is moved to the front.  Handy for running a\n";
    print "  'sort -n' to get an output ordered by file size.  The option '-sc=date' is similar\n";
    print "  but puts the date field first.\n\n";

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

  $response = shell_exec("ls -R -a -l --full-time $in_dir");
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
    print "$paths[$i]|$names[$i]|$ext|$num_bytes[$i]|$file_date[$i]|$file_time[$i]|$zone_shift[$i]|\n";
  }


/*-------sample output from the 'ls -R -l --full-time' command--------------------------
../2009-2011/2009-07-13-us-qvs-draw-code:
total 0
drwxrwxrwx 1 bkaney bkaney 4096 2021-07-19 10:42:24.001409800 -0500 .
drwxrwxrwx 1 bkaney bkaney 4096 2021-07-23 10:14:27.446462000 -0500 ..
drwxrwxrwx 1 bkaney bkaney 4096 2021-07-20 10:11:40.976952900 -0500 draw_code

../2009-2011/2009-07-13-us-qvs-draw-code/draw_code:
total 864
drwxrwxrwx 1 bkaney bkaney  4096 2021-07-20 10:11:40.976952900 -0500 .
drwxrwxrwx 1 bkaney bkaney  4096 2021-07-19 10:42:24.001409800 -0500 ..
drwxrwxrwx 1 bkaney bkaney  4096 2008-08-16 12:05:21.000000000 -0500 animation_frames
-rwxrwxrwx 1 bkaney bkaney 16413 2008-06-18 23:16:38.000000000 -0500 draw_diff_binary_prod_map.cc
--------------------------------------------------------------------------------------*/

?>
