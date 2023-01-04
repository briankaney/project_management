#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  replace_column_of_indexes_via_lookup_in_another_file.php [options] /path/target_file target_col_index /path/source_file source_col_index source_col_string\n\n";

    print "Examples:\n";
    print "  ./replace_column_of_indexes_via_lookup_in_another_file.php -d=tab ./orig/medium 3 ./orig/medium_format 0 1\n";
    print "  ./replace_column_of_indexes_via_lookup_in_another_file.php -d=tab ./orig/artist_credit_name 2 ./orig/artist_credit 0 2\n";
    print "  ./replace_column_of_indexes_via_lookup_in_another_file.php ./sample_target 1 ./sample_source 0 2\n\n";

    print "  There is a 'target' file.  One of the columns in the target should be an integer index.  That index will be used\n";
    print "  to look up a string in another file, called the source.  That string in the source will replace the index column\n";
    print "  in the target.  For instance, consider the following example.  The second column is an integer which codes for\n";
    print "  the name of the music group.\n\n";

    print "    Underground|203|Reprise|1967|\n";
    print "    Mass In F Minor|203|Reprise|1967|\n";
    print "    Surrealistic Pillow|85|RCA|1967|\n";
    print "    Freak Out|356|Verve|1966|\n\n";

    print "  These groups are found in column 3 of this next file.  The first column is the an integer index that will be used\n";
    print "  to do the lookup.\n\n";

    print "    1|Group|The Beatles|UK|\n";
    print "    2|Group|The Rolling Stones|UK|\n";
    print "    203|Group|The Electric Prunes|US|\n";
    print "    85|Group|Jefferson Airplane|US|\n";
    print "    356|Group|The Mothers|US|\n\n";

    print "  The resulting output will be:\n\n";

    print "    Underground|The Electric Prunes|Reprise|1967|\n";
    print "    Mass In F Minor|The Electric Prunes|Reprise|1967|\n";
    print "    Surrealistic Pillow|Jefferson Airplane|RCA|1967|\n";
    print "    Freak Out|The Mothers|Verve|1966|\n\n";

    print "  The lookup index in the source file must all be integers >= 0.  The maximum interger is first detected and then\n";
    print "  an array with that many elements are created.  The other integers will be used as array indexes, so these must\n";
    print "  be valid for that purpose.  They need not be in numerical order and can have gaps.  The array itself will consist\n";
    print "  of the source strings that go with the indexes.  The target index used for the lookup can contain a mix of types.\n";
    print "  If it's a non-negative integer it will get used as an array index.  But if the column is empty, or some other\n";
    print "  placeholder like '-1' or 'N/A' it will just get skipped and the replacement will not occur.\n\n";

    print "Options:\n";
    print "  Use '-h=3' to specify a number of header lines.  The header section is output, but the extraction action is not\n";
    print "  applied to it.\n\n";

    print "  The default delimiter is the pipe character, but can be changed via '-d=spaces', '-d=comma', or '-d=tab'.  If\n";
    print "  'spaces' is used, file read and write may not be symmetric in that reading counts any number of consecutive\n";
    print "  spaces as a single delimiter, but always uses a one space char delimiter in output.\n\n";
    exit(0);
  }

  include 'library_text_columns.php';
  
//--------------------------------------------------------------------------------------
//   Read in five required command line argumants.  Test the two that are files to make 
//   sure they exist.  Read optional args for number of header lines and delimiter character.
//--------------------------------------------------------------------------------------

  $target_file = $argv[$argc-5];
  $target_col_index = $argv[$argc-4];
  $source_file = $argv[$argc-3];
  $source_col_index = $argv[$argc-2];
  $source_col_string = $argv[$argc-1];

  if(!file_exists($target_file))
  {
    print "\nInput file not found: $target_file\n\n";
    exit(0);
  }

  if(!file_exists($source_file))
  {
    print "\nInput file not found: $source_file\n\n";
    exit(0);
  }

  $header = ReadArgsForHeaderCount($argv,1,$argc-6);
  $delimiter = ReadArgsForDelimiter($argv,1,$argc-6);
  $delimiter_char = GetDelimiterChar($delimiter);

//--------------------------------------------------------------------------------------
//   Open pointer to source file and loop through.  Make sure the file has enough columns
//   to cover the user requested input indexex.  Also make sure all the entries in the 
//   lookup index column are integers and determine the max value of this set..
//--------------------------------------------------------------------------------------

  $src = fopen($source_file,'r');

  $max_array_index = 0;
  while( ($line = fgets($src)) !== false)
  {
    $line = rtrim($line,"\n");  
    $fields = explode($delimiter_char,$line);  
    $num_fields = count($fields);

    if($source_col_index>=$num_fields || $source_col_string>=$num_fields)
    {
      print "\n\nFatal Error:  Column Specified Out Of File Bounds\v\n"; exit(-1);
    }

    $test = $fields[$source_col_index];
    if(!is_numeric($test)) { print "\n\nFatal Error:  Source index is non-numeric\n\n";  exit(-1); }    
    if(intval($test)-floatval($test)!=0) { print "\n\nFatal Error:  Source index is non-integer\n\n";  exit(-1); }
    if($test<0) { print"\n\nFatal Error:  Source index is negative\n\n";  exit(-1); }

    if($fields[$source_col_index]>$max_array_index) { $max_array_index = $fields[$source_col_index]; }
  }

//--------------------------------------------------------------------------------------
//   Set up an array for the strings that go with the index used to lookup replacements.
//   First step thru the file and find the max index needed. Set up an array of blank 
//   strings big enough to include the max.  Rewind the pointer and loop thru file again 
//   reading the strings into the array.  
//--------------------------------------------------------------------------------------

  $lookup_string = array();
  for($i=0;$i<$max_array_index;++$i) { $lookup_string[$i] = ""; }

  rewind($src);
  while( ($line = fgets($src)) !== false)
  {
    $line = rtrim($line,"\n");  
    $fields = explode($delimiter_char,$line);  
    $lookup_string[$fields[$source_col_index]] = $fields[$source_col_string];
  }

  fclose($src);

//--------------------------------------------------------------------------------------
//   Open a pointer to the target file.  Loop thru and replace the target field with the 
//   lookup string.
//--------------------------------------------------------------------------------------

  $targ = fopen($target_file,'r');

  while( ($line = fgets($targ)) !== false)
  {
    $line = rtrim($line,"\n");  
    $fields = explode($delimiter_char,$line);  
    $num_fields = count($fields);

    $test = $fields[$target_col_index];
    if(!is_numeric($test)) { PrintOneLineOfFieldsAsOutput($fields,$delimiter);  continue; }    
    if(intval($test)-floatval($test)!=0) { PrintOneLineOfFieldsAsOutput($fields,$delimiter);  continue; }
    $test = intval($test);
    if($test<0 || $test>=$max_array_index) { PrintOneLineOfFieldsAsOutput($fields,$delimiter);  continue; }
    
    $fields[$target_col_index] = $lookup_string[$fields[$target_col_index]];

    PrintOneLineOfFieldsAsOutput($fields,$delimiter);
  }

  fclose($targ);

?>
