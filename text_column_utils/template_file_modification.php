#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  template_file_modification.php [options] other_args\n\n";

    print "Examples:\n";
    print "  ./template_file_modification.php example args\n\n";

    print "  Explain what this script does here.\n\n";

//--modify options as needed.
    print "Options:\n";
    print "  Use '-h=3' to specify a number of header lines.  The header section is output but not otherwise acted upon by\n";
    print "  by this script.  The default value is zero.\n\n";

    print "  Default delimiter is the pipe character, but can be changed via '-d=spaces' or '-d=comma'.  If 'spaces' is used\n";
    print "  file read and write may not be symmetric in that reading counts any number of consecutive spaces as a single\n";
    print "  delimiter, but always uses one a one space delimiter in the output.\n\n";
    exit(0);
  }

  include 'library_text_columns.php';

//--------------------------------------------------------------------------------------
//   Read the required args and make sure the one that is the input is a file that 
//   exists.  Read optional args for number of header lines and delimiter character.
//--------------------------------------------------------------------------------------

      //--adjust 'X' as needed below:
  $infile = $argv[$argc-1];
//  $infile = $argv[$argc-X];

  if(!file_exists($infile))
  {
    print "\nInput file not found: $infile\n\n";
    exit(0);
  }

  $header = ReadArgsForHeaderCount($argv,1,$argc-2);
  $delimiter = ReadArgsForDelimiter($argv,1,$argc-2);
//  $header = ReadArgsForHeaderCount($argv,1,$argc-X-1);
//  $delimiter = ReadArgsForDelimiter($argv,1,$argc-X-1);

       //--add other args and processing as needed

//--------------------------------------------------------------------------------------
//   Read in contents of input file
//--------------------------------------------------------------------------------------

//---$fields will be a 2d array of lines and columns.

  $lines = file("$infile",FILE_IGNORE_NEW_LINES);
  $fields = SplitLinesToFields($lines,$header,$delimiter);
  $num_lines = count($fields);

  $num_columns = TestFieldCountConsistency($fields);

//--------------------------------------------------------------------------------------
//   Do something with file lines
//--------------------------------------------------------------------------------------

/*
  for($i=0;$i<$num_lines;++$i)
  {
  }
*/

//--------------------------------------------------------------------------------------
//   Print the output
//--------------------------------------------------------------------------------------

  for($i=0;$i<$header;++$i) { print "$lines[$i]\n"; }
  PrintFieldsAsOutput($fields,$delimiter);

?>
