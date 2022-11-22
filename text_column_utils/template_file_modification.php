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

    print "  Default delimiter is the pipe character, but can be changed via '-d=spaces', '-d=comma', or '-d=tab'.  If\n";
    print "  'spaces' is used file read and write may not be symmetric in that reading counts any number of consecutive\n";
    print "  spaces as a single delimiter, but always uses one a one space delimiter in the output.\n\n";
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
//   Open pointer to input file
//--------------------------------------------------------------------------------------

  $inf = fopen($infile,'r');

//--------------------------------------------------------------------------------------
//   Skip header lines if desired.  Or print to output.
//--------------------------------------------------------------------------------------
  
  for($i=0;$i<$header;++$i)
  {
    $line = fgets($inf);
    print "$line";
  }

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------

  while( ($line = fgets($inf)) !== false)
  {
    $line = rtrim($line,"\t");  
    $fields = SplitOneLineToFields($line,$delimiter);
    $num_fields = count($fields);

//--------------------------------------------------------------------------------------
//   Do something with file line. Maybe print something.s
//--------------------------------------------------------------------------------------

    PrintOneLineOfFieldsAsOutput($fields,$delimiter);
  }

  fclose($inf);

?>
