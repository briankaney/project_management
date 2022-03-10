#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "   create_flat_mv_batch_from_fl7.php infile\n\n";
    print "Examples:\n";
    print "   ./create_flat_mv_batch_from_fl7.php sample.fl7.txt\n\n";


    print "  Needs work...\n\n";

    print "  Fairly specialized script.  Input a file listing and a file extension\n";
    print "  string and this script creates a text file of commands to move the files\n";
    print "  in the file list (with the specified extension) to a purge staging area.\n";
    print "  The input file list format must be the output of the script\n";
    print "  'ls_listing-convert_to_files.php' with the '-sc=ext' in use.  So the first\n";
    print "  column is the file extension.\n\n";

    print "  Note: This script makes NO actual file changes!  The output is a list of\n";
    print "  commands for use later with the batch processor script:\n";
    print "  'exec_linux_commands_from_text_file.php'.  Running that script does not do\n";
    print "  the removal either, since the commands just move the files to a 'purge_staging'\n";
    print "  sub-directory.  There they can be previewed in various ways and then removed\n";
    print "  manually with a single 'rm'.  In that way the process is fast but avoid\n";
    print "  ever doing a huge automated 'rm' batch.\n\n";  

    print "  Capture output with a redirect.\n\n";

    exit(0);
  }

//--------------------------------------------------------------------------------------
//   Make sure the last arg is a file that exists
//--------------------------------------------------------------------------------------

  $infile = $argv[$argc-1];

  if(!file_exists($infile)) { print "\nInput file not found: $infile\n\n";  exit(0); }

//-----------Sample lines from a 'fl7.txt' format file--------------------------------
//2003-2005/2003-05-16-Thai-WDT-Tarball/public_html/bin/data/|scattergram_heading.ppm|ppm|10266|2003-03-12|23:56:58|-0600|
//2003-2005/2003-05-16-Thai-WDT-Tarball/public_html/bin/data/|Topo-THDomain.ppm|ppm|2292043|2003-05-08|09:04:43|-0500|
//2003-2005/2003-10-AZ-H2-Webpage-Backup/verification/bin/data/|full_qpe_legend.ppm|ppm|180042|2003-04-15|09:25:47|-0500|
//2003-2005/2003-10-AZ-H2-Webpage-Backup/verification/bin/data/|gauge_info_heading.ppm|ppm|19698|2003-03-24|15:30:59|-0600|
//--------------------------------------------------------------------------------------

  $dest = "sample/";

  $lines = file($infile,FILE_IGNORE_NEW_LINES);
  $num_files = count($lines);

  for($i=0;$i<$num_files;++$i)
  {
    $fields = explode('|',$lines[$i]);

    $dir = $fields[0];
    $dir_str = str_replace('/','__',$dir);
    $filename = $fields[1];

    $str = "mv ".$dir.$filename." ".$dest.$filename.".".$dir_str;

    print "$str\n";
  }

?>
