<?php

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------

  function ReadArgsForHeaderCount($args,$first_option,$last_option)
  {
    $header = 0;
    for($i=$first_option;$i<=$last_option;++$i)
    {
      if(substr($args[$i],0,3)=="-h=")
      {
        $parts  = explode('=',$args[$i]);
        $header = $parts[1];
        break;
      }
    }
    return $header;
  }

  function ReadArgsForDelimiter($args,$first_option,$last_option)
  {
    $delimiter = "pipe";
    for($i=$first_option;$i<=$last_option;++$i)
    {
      if($args[$i]=="-d=comma")  { $delimiter = "comma";  break; }
      if($args[$i]=="-d=spaces") { $delimiter = "spaces"; break; }
      if($args[$i]=="-d=tab")    { $delimiter = "tab";    break; }
    }
    return $delimiter;
  }

  function GetDelimiterChar($delimiter)
  {
    if($delimiter=="pipe")   { return "|"; }
    if($delimiter=="comma")  { return ","; }
    if($delimiter=="spaces") { return " "; }
    if($delimiter=="tab")    { return "\t"; }
    return "";
  }

//--------------------------------------------------------------------------------------
//   Pass in a line array from a full file read and return a 2D array of column fields
//--------------------------------------------------------------------------------------

  function SplitOneLineToFields($line,$delimiter)
  {
    $fields = Array();

    if($delimiter=="spaces") { $fields = preg_split('/ +/',$line); }
    if($delimiter=="comma")  { $fields = explode(',',$line); }
    if($delimiter=="pipe")   { $fields = explode('|',$line); }
    if($delimiter=="tab")    { $fields = explode("\t",$line); }  // single quotes don't work here

    return $fields;
  }	  
	
  function SplitLinesToFields($lines,$header_len,$delimiter)
  {
    $fields = Array();

    for($i=$header_len;$i<count($lines);++$i)
    {
      if($delimiter=="spaces") { $fields[$i-$header_len] = preg_split('/ +/',$lines[$i]); }
      if($delimiter=="comma")  { $fields[$i-$header_len] = explode(',',$lines[$i]); }
      if($delimiter=="pipe")   { $fields[$i-$header_len] = explode('|',$lines[$i]); }
      if($delimiter=="tab")    { $fields[$i-$header_len] = explode("\t",$lines[$i]); }  // single quotes don't wor here
    }

    return $fields;
  }

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------

  function LineEndingStatus($fields)
  {
    $line_ending_status = "mixed";
    $num_lines = count($fields);

    $found = false;
    for($i=0;$i<$num_lines;++$i) {
      $num_fields = count($fields[$i]);
      if(strlen($fields[$i][$num_fields-1])!=0) { $found = true;  break; }
    }
    if($found==false) { $line_ending_status = "empty"; }

    $found = false;
    for($i=0;$i<$num_lines;++$i) {
      $num_fields = count($fields[$i]);
      if(strlen($fields[$i][$num_fields-1])==0) { $found = true;  break; }
    }
    if($found==false) { $line_ending_status = "empty"; }

    return $line_ending_status;
  }

  function TestFieldCountConsistency($fields)
  {
    $num_columns = count($fields[0]);

    for($i=1;$i<count($fields);++$i)
    {
      if(count($fields[$i])!=$num_columns)
      {
        print "\nFatal error:  column count inconsistency\n";
        exit(0);
      }
    }
    return $num_columns;
  }

  function TestIndicesLegal($column_indices,$num_columns)
  {
/*---  redo,  early code that is off base.  column_indies is not an array in the places I use this.  It is just a number.  And num_columns is another number, they just need to be compared in one line.  An array of column numbers per line could have other uses but this needs to be redone.
    for($i=0;$i<count($column_indices);++$i)
    {
      if($column_indices[$i]<0 || $column_indices[$i]>=$num_columns)
      {
        print "\nColumn indices specified are out of bounds for $num_columns columm file\n";
        exit();
      }
    }
*/
  }

//--------------------------------------------------------------------------------------
//   Go thru input file line by line and redo decimal points on float columns requested
//--------------------------------------------------------------------------------------

  function PrintOneLineOfFieldsAsOutput($fields,$delimiter)
  {
    $num_columns=count($fields);
    $delimiter_char = GetDelimiterChar($delimiter);

    printf("%s",$fields[0]);
    for($i=1;$i<$num_columns;++$i)
    {
      printf("%s%s",$delimiter_char,$fields[$i]);
    }
    print "\n";
  }

  function PrintFieldsAsOutput($fields,$delimiter)
  {
    $num_columns=count($fields[0]);
    for($i=0;$i<count($fields);++$i)
    {
      $delimiter_char = GetDelimiterChar($delimiter);

      printf("%s",$fields[$i][0]);
      for($j=1;$j<$num_columns;++$j)
      {
        printf("%s%s",$delimiter_char,$fields[$i][$j]);
      }
      print "\n";
    }
  }

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------

?>
