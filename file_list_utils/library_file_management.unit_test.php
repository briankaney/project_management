#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)  {
    print "\n\nUsage:\n";
    print "  library_file_management.unit_test.php function input_arg1 input_arg2 ...\n\n";

    print "Examples:\n";
    print "  ./library_file_management.unit_test.php printIntWithCommas 4530027\n\n";

    print "  ./library_file_management.unit_test.php getPathFromFullFilename public_html/sci/index.html\n";
    print "  ./library_file_management.unit_test.php getPathFromFullFilename '~/public_html/sci/index.html'\n";
    print "  ./library_file_management.unit_test.php getPathFromFullFilename /home1/bkaney/public_html/sci/index.html\n\n";

    print "  ./library_file_management.unit_test.php getNameFromFullFilename /home1/bkaney/public_html/sci/index.html\n";
    print "  ./library_file_management.unit_test.php getRootNameFromFullFilename /home1/bkaney/public_html/sci/index.html\n";
    print "  ./library_file_management.unit_test.php getExtFromFullFilename /home1/bkaney/public_html/sci/index.html\n";
    print "  ./library_file_management.unit_test.php getExtFromFullFilename /home1/bkaney/public_html/sci/index.test.html\n\n";

    print "  ./library_file_management.unit_test.php getMonthValueFromString Dec array_index\n";
    print "  ./library_file_management.unit_test.php getMonthValueFromString December integer\n";
    print "  ./library_file_management.unit_test.php getMonthValueFromString Jun string\n\n";

    print "  Unit testing for the functions contained in the library of the same name.  Note the use of \"'\" in some\n";
    print "  cases to escape special chararters.  None of the input args should contain spaces.  If the filename uses\n";
    print "  multiple '.' chars, then only the last is considered to divide the filename into 'root part' and 'extension'.\n\n";
    exit(0);
    }

  include 'library_file_management.php';

//--------------------------------------------------------------------------------------
//   Read in command line args, no use of argv or argc past this point.
//--------------------------------------------------------------------------------------

  $function_name = $argv[1];

  $input_arg = array();
  for($i=0;$i<$argc-2;++$i) { $input_arg[$i] = $argv[2+$i]; }

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------

  if($function_name=="printIntWithCommas") {
    $str = printIntWithCommas($input_arg[0]);
    print "\n$str\n\n";
    exit(0);
  }

  if($function_name=="getPathFromFullFilename") {
    $str = getPathFromFullFilename($input_arg[0]);
    print "\n$str\n\n";
    exit(0);
  }

  if($function_name=="getNameFromFullFilename") {
    $str = getNameFromFullFilename($input_arg[0]);
    print "\n$str\n\n";
    exit(0);
  }

  if($function_name=="getRootNameFromFullFilename") {
    $str = getRootNameFromFullFilename($input_arg[0]);
    print "\n$str\n\n";
    exit(0);
  }

  if($function_name=="getExtFromFullFilename") {
    $str = getExtFromFullFilename($input_arg[0]);
    print "\n$str\n\n";
    exit(0);
  }

  if($function_name=="getMonthValueFromString") {
    $str = getMonthValueFromString($input_arg[0],$input_arg[1]);
    print "\n$str\n\n";
    exit(0);
  }

//--------------------------------------------------------------------------------------
//  If you are still here, then the function name input was never found.  Print message.
//--------------------------------------------------------------------------------------

  print "\nFunction name input is not valid: $function_name\n\n";

?>
