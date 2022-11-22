#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  grep_by_column.php [options] /path/input_file search_string column_index\n\n";

    print "Examples:\n";
    print "  ./grep_by_column.php -d=tab sample_data/artists.txt Prunes 1\n";
    print "  ./grep_by_column.php -e -d=tab sample_data/artists.txt Prunes 1\n";
    print "  ./grep_by_column.php -d=tab sample_data/artists.txt 'The Electric Prunes' 1\n\n";

    print "  The input file should be plain text 'columns' with some delimiter.  The output will be some subset of lines\n";
    print "  of the original file.  The output is captured via redirect.  The output lines are similar to a what a linux\n";
    print "  'grep' would produce, except only applied to the contents of one column (and with a lot fewer options).\n\n";

    print "Options:\n";
    print "  Use '-h=3' to specify a number of header lines.  The header section is output, but the extraction action is not\n";
    print "  applied to it.\n\n";

    print "  The default delimiter is the pipe character, but can be changed via '-d=spaces', '-d=comma', or '-d=tab'.  If\n";
    print "  'spaces' is used, file read and write may not be symmetric in that reading counts any number of consecutive\n";
    print "  spaces as a single delimiter, but always uses a one space char delimiter in output.\n\n";

    print "  The option '-e' requires an exact full match between the input search string and the contents of the\n";
    print "  target column.  Without this, the default behavior is that the search string need only be contained as\n";
    print "  as a substring in the target column (like 'grep').\n\n";
    exit(0);
  }

  include 'library_text_columns.php';

//--------------------------------------------------------------------------------------
//   Read in command line argumants
//--------------------------------------------------------------------------------------

  $infile = $argv[$argc-3];

  if(!file_exists($infile))
  {
    print "\nInput file not found: $infile\n\n";
    exit(0);
  }

  $search_string = $argv[$argc-2];
  $col_index = $argv[$argc-1];

  $header = ReadArgsForHeaderCount($argv,1,$argc-4);
  $delimiter = ReadArgsForDelimiter($argv,1,$argc-4);
  $delimiter_char = GetDelimiterChar($delimiter);

  $mode = "grep";

  for($i=1;$i<=$argc-4;++$i)
  {
    if($argv[$i]=="-e") { $mode = "exact";   break; }
  }

//--------------------------------------------------------------------------------------
//   Open pointer to input file.  Copy out any header lines.  Then loop through rest of 
//   file and print out the requested column extraction.
//--------------------------------------------------------------------------------------

  $inf = fopen($infile,'r');

  for($i=0;$i<$header;++$i)
  {
    $line = fgets($inf);
    print "$line";
  }

  while( ($line = fgets($inf)) !== false)
  {
    $line = trim($line);  
    $fields = SplitOneLineToFields($line,$delimiter);
    $num_fields = count($fields);

    if($col_index>=$num_fields) { print "\n\nFatal Error:  Max Column Specified Out Of File Bounds\v\n"; exit(-1); }

    if($mode=="grep" && strpos($fields[$col_index],$search_string)!==false) { print "$line\n"; }
    if($mode=="exact" && $search_string==$fields[$col_index]) { print "$line\n"; }
  }

  fclose($inf);

?>
