#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "   search_fl7_for_duplicate_files.php [options] infile1 infile2\n\n";
    print "Examples:\n";
    print "   ./search_fl7_for_duplicate_files.php -m=all_time listing1.txt listing2.txt\n";
    print "   ./search_fl7_for_duplicate_files.php -m=min_sec listing.txt -self\n\n";
    print "   ./search_fl7_for_duplicate_files.php -o=dir_only listing.txt -self\n\n";

    print "  Looks for duplicate files between two input file listings.  These file listings must be\n";
    print "  in the 'fl7' format described elsewhere.  A file can be compared to itself by using the\n"; 
    print "  string '-self' in place of the second file name.  It must be in that position so it does\n";
    print "  not act as a true option switch.  Capture output with a redirect.\n\n";

    print "  Duplicates in two files are found by comparing every filename in the first file to every\n";
    print "  filename in the second file.  This loop nesting is slow for big files.  If the same file\n";
    print "  appears only once in each list, it will generate just one match.  But if a file has two\n";
    print "  copies in list one and four copies in list two then a total of eight matches will be returned\n";
    print "  as each source file finds all the targets it matches.  This report redundancy is not suppressed.\n";
    print "  Some, but not all, of this redundancy is reduced for the self search case.  Filenames are not\n";
    print "  compared to themselves, but only to files later in the listing.  In this way a pair of files\n";
    print "  that are the same only report once.  Because 1 finds 2, but 2 never looks for 1 since 2 occurs\n";
    print "  later in the list.  But if there is a triplet of the same file it will generate three reports\n";
    print "  (as 1 finds 2, 1 finds 3, and 2 finds 3).  But this is better than six reports.\n\n";

    print "  The '-m' option sets the comparison mode for time fields.  For a match, the file name and number\n";
    print "  of bytes must always be the same and just that is the default.  If '-m=all_time' used, then the\n";
    print "  full time stamps must also agree.  The option '-m=min_sec' only requires the minute and second\n";
    print "  fields to match.  This was created since files that moved between machines or storage devices\n";
    print "  can have time stamps mis-matched by an hour or a few hours due to time zone/daylight savings time\n";
    print "  issues.  Even if the file was never edited.  This makes the full time matching too aggressive.\n";
    print "  Using just minutes and seconds gets around this issue.  Using just those two fields is then too\n";
    print "  lenient, but with 3,600 min/sec combos in an hour, the extra matches will be few.\n\n"; 

    print "  The '-o' option sets the output format mode.  The default is fairly verbose - it outputs a\n";
    print "  a numbered block with the path and filename of both matching files.  The 'dir_only' just outputs\n";
    print "  the directory only of just the first file in the match.  Just one line per match.\n\n";

    exit(0);
  }

  include 'library_file_management.php';

//--------------------------------------------------------------------------------------
//    Read any command line options
//--------------------------------------------------------------------------------------

  $match_mode = "name_size";
  $output_mode = "default";

  for($i=1;$i<$argc-1;++$i)
  {
    if($argv[$i]=="-m=all_time") { $match_mode  = "name_size_all_time"; }
    if($argv[$i]=="-m=min_sec")  { $match_mode  = "name_size_min_sec";  }
    if($argv[$i]=="-o=dir_only") { $output_mode = "dir_only";  }
  }

//--------------------------------------------------------------------------------------
//   Read the two required args.  Make sure any files requested actually exist.  If the 
//   user input the same file twice, treat it the same as if they used the '-self' option.
//--------------------------------------------------------------------------------------

  $infile1 = $argv[$argc-2];
  $infile2 = $argv[$argc-1];

  if(!file_exists($infile1)) { print "\nInput file not found: $infile1\n\n";  exit(0); }

  if($infile1==$infile2) { $infile2 = "-self"; }
  if($infile2!="-self") {
    if(!file_exists($infile2)) { print "\nInput file not found: $infile2\n\n";  exit(0); }
  }

//--------------------------------------------------------------------------------------
//    Open command line and read in contents
//--------------------------------------------------------------------------------------

//  $output = "pair_list";

//-----------Sample lines from a 'files.txt' format file--------------------------------
// Sorting/|BackupLog.xlsx|5611358|2019-02-01|10:54:15|-0600|
// Sorting/Archive-Data-2018-08-02/Bounds-FindSource/|aleutian1802pts.txt|40694|2004-01-19|19:11:52|-0600|
// Sorting/Archive-Data-2018-08-02/Bounds-FindSource/|clipperton_island2pts.txt|928|2003-06-01|20:13:44|-0500|
//--------------------------------------------------------------------------------------
//../2005-03-AmazonStuff/bin/|ncdump||156836|2003-11-06|21:26:11|-0600|
//../2005-03-AmazonStuff/public_html/Amazon/|contents.cgi|cgi|4410|2003-11-17|16:54:18|-0600|
//../2005-03-AmazonStuff/public_html/Amazon/|cover.html|html|1904|2003-11-17|17:12:42|-0600|
//../2005-03-AmazonStuff/public_html/Amazon/|index.html|html|438|2003-11-16|16:17:16|-0600|
//../2005-03-AmazonStuff/public_html/Amazon/|process_request.cgi|cgi|1269|2003-11-17|17:34:25|-0600|
//../2005-03-AmazonStuff/public_html/Amazon/cgi-bin/|autoest_daily_map.cgi|cgi|2673|2003-11-12|22:15:55|-0600|
//--------------------------------------------------------------------------------------

  $lines = file($infile1,FILE_IGNORE_NEW_LINES);
  $num_files1 = count($lines);

  $paths1 = array();
  $names1 = array();
  $num_bytes1 = array();
  $file_date1 = array();
  $file_time1 = array();
  $time_compare_str1 = array();

  for($i=0;$i<$num_files1;++$i) {
    $fields = explode('|',$lines[$i]);

    $paths1[$i] = $fields[0]; 
    $names1[$i] = $fields[1]; 
    $num_bytes1[$i] = $fields[3];
    $file_date1[$i] = $fields[4];
    $file_time1[$i] = $fields[5];

    if($match_mode=="name_size")          { $time_compare_str1[$i] = ""; }
    if($match_mode=="name_size_all_time") { $time_compare_str1[$i] = $file_date1[$i]."|".$file_time1[$i]; }
    if($match_mode=="name_size_min_sec")  {
      $parts = explode(':',$file_time1[$i]);
      $time_compare_str1[$i] = $parts[1].":".$parts[2];
    }
  }

  if($infile2=="-self") {
    $k = 0;
    for($i=0;$i<$num_files1;++$i) {
      for($j=$i+1;$j<$num_files1;++$j) {
        if($names1[$i]==$names1[$j] && $num_bytes1[$i]==$num_bytes1[$j] && $time_compare_str1[$i]==$time_compare_str1[$j]) {

          if($output_mode=="dir_only") { $str = $paths1[$i]; }

          if($output_mode=="default")
          {
            $str = $k."\n  ".$paths1[$i].$names1[$i]." ".$num_bytes1[$i]." ".$file_date1[$i]." ".$file_time1[$i].
                   "\n  ".$paths1[$j].$names1[$j]." ".$num_bytes1[$j]." ".$file_date1[$j]." ".$file_time1[$i];
          }

          print "$str\n";
          ++$k;
        }
      }
    }
  }


//        if($names[$i]==$names[$j] && $num_bytes[$i]==$num_bytes[$j] && $file_time[$i]==$file_time[$j]) {
//        if($names[$i]==$names[$j] && $num_bytes[$i]==$num_bytes[$j] && $file_min[$i]==$file_min[$j] && $file_sec[$i]==$file_sec[$j]) {













//--------------------------------------------------------------------------------------
/*
  $lines = file($infile2,FILE_IGNORE_NEW_LINES);
  $num_files2 = count($lines);

  $paths2 = array();
  $names2 = array();
  $num_bytes2 = array();
  $file_date2 = array();
  $file_time2 = array();
  $file_min2  = array();
  $file_sec2  = array();

  for($i=0;$i<$num_files2;++$i) {
    $fields = explode('|',$lines[$i]);

    $paths2[$i] = $fields[0]; 
    $names2[$i] = $fields[1]; 
    $num_bytes2[$i] = $fields[3];
    $file_date2[$i] = $fields[4];
    $file_time2[$i] = $fields[5];


    $parts = explode(':',$file_time2[$i]);
    $file_min2[$i] = $parts[1];
    $file_sec2[$i] = $parts[2];
  }

//--------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------

  $k=0;
  for($i=0;$i<$num_files1;++$i)
  {
    for($j=0;$j<$num_files2;++$j)
    {
      if($names1[$i]==$names2[$j] && $num_bytes1[$i]==$num_bytes2[$j] && $file_date1[$i]==$file_date2[$j] && $file_min1[$i]==$file_min2[$j] && 
                  $file_sec1[$i]==$file_sec2[$j])
      {
//        $str = $k."\n  ".$paths1[$i].$names1[$i]." ".$num_bytes1[$i]." ".$file_date1[$i]."\n  ".$paths2[$j].$names2[$j]." ".$num_bytes2[$j]." ".$file_date2[$j];
        $str = $names1[$i]."|".$paths1[$i]."|".$num_bytes1[$i]."|".$file_date1[$i]."\n".$names2[$j]."|".$paths2[$j]."|".$num_bytes2[$j]."|".$file_date2[$j];

        print "$str\n";

        ++$k;
      }
    }
  }
*/
?>
