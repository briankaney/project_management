#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  find_unique_files_in_dir.php in_dir\n\n";

    print "Examples:\n";
    print "  ./find_unique_files_in_dir.php path/MyDir\n\n";


print "  Needs work\n\n";

    print "  Input a text file captured from running a linux directory listing.  Specifically,\n";
    print "  a 'ls -R -l --full-time' version of the command.  Script parses through the\n";
    print "  input, which is in directory blocks with separating lines, and creates a simple\n";
    print "  output list of one line per file with information in '|' delimited columns.\n";
    print "  The pipe character allows for file names with spaces to be preserved.\n\n";

    print "  The default listing has columns for path, name, file size, file date , file time,\n";
    print "  and time zone shift.  But there are a couple other options.  The option '-sp=#'\n";
    print "  'sort -n' to get an output ordered by file size.  The option '-sc=date' is similar\n";
    print "  but puts the date field first.\n\n";

    exit(0);
  }

//--------------------------------------------------------------------------------------
//   Read the one command line arg needed.
//--------------------------------------------------------------------------------------

  $in_dir = $argv[1];

//--------------------------------------------------------------------------------------
//   Run the dir listing system command.  Parse the response into a string array.
//--------------------------------------------------------------------------------------

  $response = shell_exec("ls -l $in_dir");
  $lines = explode(PHP_EOL,$response);
  array_shift($lines);
  array_pop($lines);
  $num_lines = count($lines);

//--------------------------------------------------------------------------------------
//   Set up array for filenames and extract from ls output.
//--------------------------------------------------------------------------------------

  $filenames = array();
  $status = array();

  for($i=0;$i<$num_lines;++$i)
  {
    $fields = preg_split('/ +/',$lines[$i]);
    $filenames[$i] = $fields[8];
    $status[$i] = "unknown";
  }

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------

  for($i=0;$i<$num_lines;++$i)
  {
    $str = $i." ".$in_dir.$filenames[$i];
    print "$str\n";
  }


//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------

//  for($i=0;$i<1;++$i)
  for($i=0;$i<$num_lines;++$i)
  {
    if($status[$i]=="unknown") { $status[$i] = "orig"; }
    if($status[$i]=="copy")    { continue; }

    $copy_members_str = "";
    for($j=$i+1;$j<$num_lines;++$j)
    {
      $command = "diff -q ".$in_dir.$filenames[$i]." ".$in_dir.$filenames[$j];
      $response = shell_exec($command);

      $lines = explode(PHP_EOL,$response);

//$cnt = strlen($lines[0]);
//print "$j $cnt\n";

//      if(strlen($lines[0])==0)
      if(strpos($lines[0],"differ")===false)    //  doesn't work, unsure why, if searched str is zero length it doesn't return 'false'
      {
        $status[$j] = "copy";
        $copy_members_str = $copy_members_str.$j.",";
      }
    }
    if($status[$i]=="orig") { print "$i is original; copies: $copy_members_str\n"; }
  }

/*
  for($i=0;$i<$num_lines;++$i)
  {
    print "$i = $status[$i]\n";
  }
*/





/*
    if($i<$num_lines-1) {
      if(substr($lines[$i+1],0,5)=="total") {
        $fields = explode(':',$lines[$i]);
        $current_block_dir = $fields[0]."/";
      }
    }

    if(substr($lines[$i],0,1)=="-") {

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
*/

/*-------sample output from the 'ls -R -l --full-time' command--------------------------
total 456
-rwxrwxrwx 1 bkaney bkaney 124992 May 22  2013 Legend_WBT.ppm.2005-ref_data__2016-08-RefData-best-pre-tile-slippy-map-era__legends__
-rwxrwxrwx 1 bkaney bkaney 117024 Dec 17  2006 Legend_WBT.ppm.2006-2008__2007-nmqxrt-webpage-Work__Work__resources__bin__legends__
-rwxrwxrwx 1 bkaney bkaney 100578 Apr 22  2011 Legend_WINDS_600MB.ppm.2012-2014__2012-12-12-srp-nssl4-shutdown__draw_code__ref_data__legends__
--------------------------------------------------------------------------------------*/

?>
