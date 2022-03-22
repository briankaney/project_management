<?php

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------

  function printIntWithCommas($value) {
    $print_val = "";
    $first_seg = "true";

    for($i=10;$i>=0;--$i) {   //  $segment breaks $value into three digit chunks from high to low
      $segment = intval(floor($value/pow(10,$i*3)));
      if($segment==0) continue;  //  nothing happens until we get to the first non-zero chunk 

      if($first_seg=="true") {  //  we need to pad leading zero on all but first non-zero chunk
        $segment_str = sprintf("%d",$segment);
        $first_seg="false";
        }
      else { $segment_str = sprintf("%03d",$segment); }

      $print_val = $print_val.$segment_str;
      if($i>0) $print_val = $print_val.",";  //  trailing ',' on all but the last chunk

      $value = $value - $segment*pow(10,$i*3);
    }

    return $print_val;
  }

//--------------------------------------------------------------------------------------
//  The next functions take a path/filename string and return the path portion up to 
//  the actual filename.  It will always include a final '/'.  The start of the path 
//  can be any style, that is all of the following are okay:
//     sci/code_repository/generate_image.js
//     ../code_repository/generate_image.js
//     ~/sci/code_repository/generate_image.js
//     /sci/code_repository/generate_image.js
//  In each case the output will be the same string without the 'generate_image.js' end.
//--------------------------------------------------------------------------------------

  function getPathFromFullFilename($filename) {
    $parts = preg_split('/\//',$filename);
    $num_parts = count($parts);

    $ret_str = "";
    for($i=0;$i<$num_parts-1;++$i) {
      $ret_str = $ret_str.$parts[$i]."/";
      }
    return $ret_str;
  }

//  This returns just the filename ('generate_image.js' in the above example).

  function getNameFromFullFilename($filename) {
    $parts = preg_split('/\//',$filename);
    $num_parts = count($parts);

    return $parts[$num_parts-1];
  }

//  This returns just the filename without final extension ('generate_image' in the above example).

  function getRootNameFromFullFilename($filename) {
//redo this one like the next function that follows.  use getNameFromFullname above first.  that code is just repeated in the first two lines here.

    $parts = preg_split('/\//',$filename);
    $num_parts = count($parts);

    $pieces = preg_split('/\./',$parts[$num_parts-1]);
    $num_pieces = count($pieces);

    $ret_str = substr($parts[$num_parts-1],0,strlen($parts[$num_parts-1]) - strlen($pieces[$num_pieces-1]) - 1);

    return $ret_str;
  }

//  This returns just the final file extension ('js' in the above example).

  function getExtFromFullFilename($filename) {
    $name = getNameFromFullFilename($filename);

    if(strpos($name,".")===false) {  return ""; }

    $pieces = preg_split('/\./',$name);
    $num_pieces = count($pieces);

    return $pieces[$num_pieces-1];
  }

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------

  function getMonthValueFromString($str,$mode) {
    if($str=="Jan" || $str=="January")  { $ret_value = 0; }
    if($str=="Feb" || $str=="Febuary")  { $ret_value = 1; }
    if($str=="Mar" || $str=="March")    { $ret_value = 2; }
    if($str=="Apr" || $str=="April")    { $ret_value = 3; }
    if($str=="May")                     { $ret_value = 4; }
    if($str=="Jun" || $str=="June")     { $ret_value = 5; }
    if($str=="Jul" || $str=="July")     { $ret_value = 6; }
    if($str=="Aug" || $str=="August")   { $ret_value = 7; }
    if($str=="Sep" || $str=="Sept" || $str=="September")   { $ret_value = 8; }
    if($str=="Oct" || $str=="October")  { $ret_value = 9; }
    if($str=="Nov" || $str=="November") { $ret_value = 10; }
    if($str=="Dec" || $str=="December") { $ret_value = 11; }
    if($mode=="array_index") { return $ret_value; }
    if($mode=="integer") { return $ret_value+1; }
    if($mode=="string") { return sprintf("%02d",$ret_value+1); }
  }

  function getTimeStampFromThreeColumnDirListing($col1,$col2,$col3) {
  }

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------

  function createFlatname($dir,$filename,$style) {
    if(substr($dir,0,1)==="~") { $dir = substr($dir,1); }

    $dir_str = str_replace('/','__',$dir);

    if($style=="dir_first")  { $ret_str = $dir_str."--".$filename; }
    if($style=="file_first") { $ret_str = $filename."--".$dir_str; }
    return $ret_str;
  }

  function getFileFromFlatname($flat,$style) {
    $index = strpos($flat,'--');

    if($style=="dir_first")  { $ret_str = substr($flat,$index+2); }
    if($style=="file_first") { $ret_str = substr($flat,0,$index); }
    return $ret_str;
  }

  function getDirFromFlatname($flat,$style) {
    $index = strpos($flat,'--');

    if($style=="dir_first")  { $ret_str = str_replace('__','/',substr($flat,0,$index)); }
    if($style=="file_first") { $ret_str = str_replace('__','/',substr($flat,$index+2)); }
    return $ret_str;
  }

//--------------------------------------------------------------------------------------

?>
