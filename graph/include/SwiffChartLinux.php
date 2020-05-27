<?php

// Swiff Chart Generator 3 : PHP Module for Unix

// Copyright (C) 2002-2003, GlobFX Technologies, SARL.
// All Rights Reserved.
// This is UNPUBLISHED PROPRIETARY SOURCE CODE of GlobFX Technologies, SARL.
// The contents of this file may not be disclosed to third parties, copied or
// duplicated in any form, in whole or in part, without the prior written
// permission of GlobFX Technologies, SARL.

/////////////////////////////////////////////////////////////////////////////

// 0
define( 'SCGMID_Quit',          0 );
define( 'SCGMID_GetVersion',    1 );
define( 'SCGMID_LoadStyle',     2 );
define( 'SCGMID_SetStyle',      3 );
define( 'SCGMID_SetWidth',      4 );
define( 'SCGMID_GetWidth',      5 );
define( 'SCGMID_SetHeight',     6 );
define( 'SCGMID_GetHeight',     7 );
define( 'SCGMID_SetCompressed', 8 );
define( 'SCGMID_IsCompressed',  9 );

// 10
define( 'SCGMID_AnimateChart',   10 );
define( 'SCGMID_IsAnimated',     11 );
define( 'SCGMID_SetFrameRate',   12 );
define( 'SCGMID_GetFrameRate',   13 );
define( 'SCGMID_SetLooping',     14 );
define( 'SCGMID_IsLooping',      15 );
define( 'SCGMID_ProtectSWF',     16 );
define( 'SCGMID_IsSWFProtected', 17 );
define( 'SCGMID_SetTitle',       18 );
define( 'SCGMID_GetTitle',       19 );

// 20
define( 'SCGMID_SetSubtitle',             20 );
define( 'SCGMID_GetSubtitle',             21 );
define( 'SCGMID_SetSeparators',           22 );
define( 'SCGMID_GetSeparators',           23 );
define( 'SCGMID_SetLocaleInfo',           24 );
define( 'SCGMID_SetUnicode',              25 );
define( 'SCGMID_SetDataFromTxtFile',      26 );
define( 'SCGMID_SetCategoriesFromString', 27 );
define( 'SCGMID_SetCategoriesFromArray',  28 );
define( 'SCGMID_SetCategory',             29 );

// 30
define( 'SCGMID_GetCategory',                 30 );
define( 'SCGMID_ClearAll',                    31 );
define( 'SCGMID_AddSeries',                   32 );
define( 'SCGMID_SetSeriesCaptionsFromString', 33 );
define( 'SCGMID_SetSeriesCaptionsFromArray',  34 );
define( 'SCGMID_SetSeriesCaption',            35 );
define( 'SCGMID_GetSeriesCaption',            36 );
define( 'SCGMID_GetSeriesCount',              37 );
define( 'SCGMID_GetValuesCount',              38 );
define( 'SCGMID_SetSeriesXValuesFromString',  39 );

// 40
define( 'SCGMID_SetSeriesXValuesFromArray',   40 );
define( 'SCGMID_SetSeriesYValuesFromString',  41 );
define( 'SCGMID_SetSeriesYValuesFromArray',   42 );
define( 'SCGMID_SetSeriesXYValuesFromString', 43 );
define( 'SCGMID_GetSeriesXValue',             44 );
define( 'SCGMID_GetSeriesYValue',             45 );
define( 'SCGMID_SetAxisMinValue',             46 );
define( 'SCGMID_SetAxisMaxValue',             47 );
define( 'SCGMID_SetAxisCrossValue',           48 );
define( 'SCGMID_ResetAxisBounds',             49 );

// 50
define( 'SCGMID_SetAxisTitle',       50 );
define( 'SCGMID_GetAxisTitle',       51 );
define( 'SCGMID_SetOutputFormat',    52 );
define( 'SCGMID_SetJPGQuality',      53 );
define( 'SCGMID_SetPNGCompLevel',    54 );
define( 'SCGMID_ExportAsBinary',     55 );
define( 'SCGMID_ExportAsFile',       56 );
define( 'SCGMID_SetDocumentRoot',    57 );
define( 'SCGMID_GetHTMLTag',         58 );
define( 'SCGMID_GetOutputLocation',  59 );

// 60
define( 'SCGMID_GetHTTPContentType', 60 );
define( 'SCGMID_ClearCache',         61 );
define( 'SCGMID_SetCacheName',       62 );
define( 'SCGMID_SetPrivateCacheDir', 63 );
define( 'SCGMID_SetMaxCacheSize',    64 );
define( 'SCGMID_UseCache',           65 );
define( 'SCGMID_SetTTFontsDir',      66 );
define( 'SCGMID_BatchCommands',      67 );
define( 'SCGMID_SetDebugMode',       68 );

/////////////////////////////////////////////////////////////////////////////

$swiffchart_objects= array();
$swiffchart_objects_count= 0;

function swiffchart_register()
{
  global $swiffchart_objects;
  global $swiffchart_objects_count;

  $swiffchart_objects[$swiffchart_objects_count++]= array();

  return $swiffchart_objects_count-1;
}

function swfchart_close( $pipe_pathname, $os, $is )
{
  if( $os )
    {
    $nice_disconnect= true;
    if( $nice_disconnect )
      {
      $quit_cmd= SCGMID_Quit;
      fputs( $os, $quit_cmd."\n" );
      fflush($os);
      fgets( $is, 1024 ); // wait for ack
      }

    fclose($os);
    }

  if( file_exists($pipe_pathname) )
    {
    if( $is )
      pclose($is);
    unlink( $pipe_pathname );
    }
}

function swiffchart_shutdown()
{
  global $swiffchart_objects;
  global $swiffchart_objects_count;

  // in reverse order
  for( $i= $swiffchart_objects_count-1; $i >= 0; --$i )
    {
    $chart= $swiffchart_objects[$i];
    if( ! $chart )
      continue;

    swfchart_close( $chart['pipe_pathname'], $chart['os'], $chart['is'] );

    unset( $swiffchart_objects[$i] );
    }
}

register_shutdown_function("swiffchart_shutdown");

/////////////////////////////////////////////////////////////////////////////

class SwiffChart
{
  var $install_dir;

  var $pipe_pathname;
  var $is; // pipe
  var $os;

  var $object_number;

  var $no_cache;

  function SwiffChart( $_install_path = "" )
    {
    $this->object_number= -1;
    $this->no_cache= true;

    if( empty($_install_path) )
      $this->install_dir= "/usr/local/SwiffChart";
     else
      $this->install_dir= $_install_path;

    $this->start_exe();
//$this->start_server();
    }

  function start_exe()
    {
    if( $this->object_number >= 0 )
      die("Call Release before calling start_exe() again\n");

    global $swiffchart_objects;
    $this->object_number= swiffchart_register();

    $this->pipe_pathname= tempnam("/tmp","swfchart_");
    if( ! $this->pipe_pathname )
      die("Swiffchart: cannot generate temporary pipe name\n");
    if( file_exists($this->pipe_pathname) )
      unlink($this->pipe_pathname);

    $swiffchart_objects[$this->object_number]['pipe_pathname']= $this->pipe_pathname;

    $result= trim(exec("mkfifo -m777 \"".$this->pipe_pathname."\" 2>&1"));
    if( $result[0] == chr(0) )
      $result= ""; // bug in PHP 4.0.0?

    if( $result != "" )
      {
      die("SwiffChart: Unable to create pipe $this->pipe_pathname ($result)\n");
      }

    $exe= "$this->install_dir/swfchart";
//$exe= "/home/kepler/GlobFX/objects/gcc-3.4/Debug/bin/fchartexe.exe";
//$exe= "/home/kepler/GlobFX/objects/gcc-3.2.3/ReleaseU/bin/fchartexe.exe";

    is_executable($exe) || die("Cannot execute $exe\n");

    global $HTTP_SERVER_VARS;
    if( isset($HTTP_SERVER_VARS) && isset($HTTP_SERVER_VARS['DOCUMENT_ROOT']) )
      $document_root= $HTTP_SERVER_VARS['DOCUMENT_ROOT'];
     else
      {
      global $DOCUMENT_ROOT;
      $document_root= $DOCUMENT_ROOT;
      }

    $cmd= "$exe --int";
    $cmd .= " --install-dir '".$this->encodeString($this->install_dir)."'";
    if( ! empty($document_root) )
      $cmd .= " --doc-root '".$this->encodeString($document_root)."'";

    $this->is= popen( "$cmd < ".$this->pipe_pathname, "r" );
//$this->is= popen( "$cmd 2>/dev/null < ".$this->pipe_pathname, "r" );

    if( ! $this->is )
      die("SwiffChart: cannot popen $exe\n");

    $swiffchart_objects[$this->object_number]['is']= $this->is;

    $this->os= fopen( $this->pipe_pathname, "w" );
    if( ! $this->os )
      die("SwiffChart: cannot open ".$this->pipe_pathname."\n");

    $swiffchart_objects[$this->object_number]['os']= $this->os;

    return true;
    }

  function Release()
    {
    $this->close();
    }

  function close()
    {
    if( $this->object_number == -1 )
      return;

    swfchart_close( $this->pipe_pathname, $this->os, $this->is );

    unset($this->pipe_pathname);
    unset($this->os);
    unset($this->is);

    global $swiffchart_objects;
    unset($swiffchart_objects[$this->object_number]);

    $this->object_number= -1;
    }

  ///////////////////////////////////////////////////////////////////////////

  function start_server()
    {
    if( $this->object_number >= 0 )
      die("Call Release before calling start_server() again\n");

    unset($this->pipe_pathname);
    unset($this->os);
    unset($this->is);

    $this->object_number= swiffchart_register();

//    $host= "127.0.0.1";
    $host= "192.168.0.1";
    $port= 9000;
    $timeout= 10;

    $fp = fsockopen($host,$port,$errno,$errstr,$timeout);
    if( ! $fp )
      die("SwiffChart: cannot connect to server: ".$errstr."\n");

    $this->is= $fp;
    $this->os= $fp;

    global $swiffchart_objects;
    $swiffchart_objects[$this->object_number]['is']= $this->is;
    $swiffchart_objects[$this->object_number]['os']= $this->os;
    }

  ///////////////////////////////////////////////////////////////////////////

  function putCmd( $cmd )
    {
    fputs( $this->os, ((int) $cmd)."\n" );
    }
  function putLine( $line )
    {
    fputs( $this->os, $line."\n" );
    }
  function putInteger( $value )
    {
    fputs( $this->os, ((int) $value)."\n" );
    }
  function putDouble( $value )
    {
    fputs( $this->os, ((double) $value)."\n" );
    }
  function putBool( $value )
    {
    fputs( $this->os, $value ? "1\n" : "0\n" );
    }
  function putString( $string )
    {
    $this->putLine( $this->encodeString($string) );
    }

  function putArray( $array )
    {
    if( empty($array) )
      {
      $n= 0;
      }
     else
      {
      ksort($array);
      end($array);
      $n= key($array)+1;
      }

    $this->putInteger($n);
    for( $i= 0; $i < $n; ++$i )
      $this->putString( $array[$i] );
    }

  function putData( $data )
    {
    $this->putInteger( strlen($data) );
    fwrite( $this->os, $data );
    }

  function encodeString( $str )
    {
    return str_replace( '-', '%2D', rawurlencode($str) );
    }
  function decodeString( $str )
    {
    return rawurldecode($str);
    }

  ///////////////////////////////////////////////////////////////////////////

  function getLine()
    {
    $line= "";
    for(;;)
      {
      $buffer= fgets( $this->is, 1024 );
      $len= strlen($buffer);
      if( $len == 0 )
        break;
      $line .= chop($buffer);

      if( strchr($buffer,"\n") )
        break;
      }

    return $line;
    }

  function getString()
    {
    return $this->decodeString( $this->getLine() );
    }
  function getInteger()
    {
    return (int) $this->getLine();
    }
  function getDouble()
    {
    return (double) $this->getLine();
    }
  function getBool()
    {
    return $this->getLine() != "0";
    }

  function getData()
    {
    $size= $this->getInteger();

    if( $size == 0 )
      return "";

    $data= "";
    while( $size > 0 )
      {
      $block= fread( $this->is, $size );
      if( ! $block )
        break;
      $data .= $block;
      $size -= strlen($block);
      }

    return $data;
    }

  ///////////////////////////////////////////////////////////////////////////

  function getAck()
    {
    $ack= $this->getString();
    if( $ack != "0" )
      {
      $error= trim($this->getString());
      if( empty($error) )
        $error= "Unexpected error in acknowledgement\n";
      die( "SwiffChart: ".$error."\n" );
      }
    }

  function cmd_v_v( $cmd )
    {
    $this->putCmd($cmd);
    fflush($this->os);

    $this->getAck();
    }

  function cmd_v_s( $cmd )
    {
    $this->putCmd($cmd);
    fflush($this->os);

    $str= $this->getString();

    $this->getAck();

    return $str;
    }

  function cmd_v_b( $cmd )
    {
    $this->putCmd($cmd);
    fflush($this->os);

    $value= $this->getBool();

    $this->getAck();

    return $value;
    }

  function cmd_s_v( $cmd, $value )
    {
    $this->putCmd($cmd);
    $this->putString($value);
    fflush($this->os);

    $this->getAck();
    }

  function cmd_v_i( $cmd )
    {
    $this->putCmd($cmd);
    fflush($this->os);

    $value= $this->getInteger();

    $this->getAck();

    return $value;
    }

  function cmd_b_v( $cmd, $value )
    {
    $this->putCmd($cmd);
    $this->putBool($value);
    fflush($this->os);

    $this->getAck();
    }

  function cmd_i_s( $cmd, $value )
    {
    $this->putCmd($cmd);
    $this->putInteger($value);
    fflush($this->os);

    $str= $this->getString();

    $this->getAck();

    return $str;
    }

  function cmd_is_s( $cmd, $index, $value )
    {
    $this->putCmd($cmd);
    $this->putInteger($index);
    $this->putString($value);
    fflush($this->os);

    $str= $this->getString();

    $this->getAck();

    return $str;
    }

  function cmd_is_v( $cmd, $index, $value )
    {
    $this->putCmd($cmd);
    $this->putInteger($index);
    $this->putString($value);
    fflush($this->os);

    $this->getAck();
    }

  function cmd_i_v( $cmd, $value )
    {
    $this->putCmd($cmd);
    $this->putInteger($value);
    fflush($this->os);

    $this->getAck();
    }

  function cmd_ii_d( $cmd, $value1, $value2 )
    {
    $this->putCmd($cmd);
    $this->putInteger($value1);
    $this->putInteger($value2);
    fflush($this->os);

    $value= $this->getDouble();

    $this->getAck();

    return $value;
    }

  function cmd_id_v( $cmd, $index, $value )
    {
    $this->putCmd($cmd);
    $this->putInteger($index);
    $this->putDouble($value);
    fflush($this->os);

    $this->getAck();
    }

  ///////////////////////////////////////////////////////////////////////////

  function GetVersion()
    {
    return $this->cmd_v_s(SCGMID_GetVersion);
    }

  ///////////////////////////////////////////////////////////////////////////

  function LoadStyle( $style_filename )
    {
    $this->cmd_s_v(SCGMID_LoadStyle,$style_filename);
    }

  function SetStyle( $style_data )
    {
    $this->putCmd(SCGMID_SetStyle);
    $this->putLine($style_data);
    fflush($this->os);

    $this->getAck();
    }

  ///////////////////////////////////////////////////////////////////////////

  function SetWidth( $width )
    {
    $this->cmd_i_v(SCGMID_SetWidth,$width);
    }
  function GetWidth()
    {
    return $this->cmd_v_i(SCGMID_GetWidth);
    }
  function SetHeight( $height )
    {
    $this->cmd_i_v(SCGMID_SetHeight,$height);
    }
  function GetHeight()
    {
    return $this->cmd_v_i(SCGMID_GetHeight);
    }

  ///////////////////////////////////////////////////////////////////////////

  function SetCompressed( $compressed )
    {
    $this->cmd_b_v(SCGMID_SetCompressed,$compressed);
    }
  function IsCompressed()
    {
    return $this->cmd_v_b(SCGMID_IsCompressed);
    }
  function CompressSWF( $compressed )
    {
    $this->SetCompressed($compressed);
    }
  function IsSWFCompressed()
    {
    return $this->IsCompressed();
    }

  function AnimateChart( $animate )
    {
    $this->cmd_b_v(SCGMID_AnimateChart,$animate);
    }
  function IsAnimated()
    {
    return $this->cmd_v_b(SCGMID_IsAnimated);
    }

  function SetFrameRate( $frame_rate )
    {
    $this->cmd_i_v(SCGMID_SetFrameRate,$frame_rate);
    }
  function GetFrameRate()
    {
    return $this->cmd_v_i(SCGMID_GetFrameRate);
    }

  function SetLooping( $looping )
    {
    $this->cmd_b_v(SCGMID_SetLooping,$looping);
    }
  function IsLooping()
    {
    return $this->cmd_v_b(SCGMID_IsLooping);
    }

  function ProtectSWF( $swf_protected )
    {
    $this->cmd_b_v(SCGMID_ProtectSWF,$swf_protected);
    }
  function IsSWFProtected()
    {
    return $this->cmd_v_b(SCGMID_IsSWFProtected);
    }

  ///////////////////////////////////////////////////////////////////////////

  function SetTitle( $title )
    {
    $this->cmd_s_v(SCGMID_SetTitle,$title);
    }
  function GetTitle()
    {
    return $this->cmd_v_s(SCGMID_GetTitle);
    }

  function SetSubtitle( $subtitle )
    {
    $this->cmd_s_v(SCGMID_SetSubtitle,$subtitle);
    }
  function GetSubtitle()
    {
    return $this->cmd_v_s(SCGMID_GetSubtitle);
    }

  ///////////////////////////////////////////////////////////////////////////

  function SetSeparators( $separators, $ignore_multiple_sep )
    {
    $this->putCmd(SCGMID_SetSeparators);
    $this->putString($separators);
    $this->putBool($ignore_multiple_sep);
    fflush($this->os);

    $this->getAck();
    }

  function GetSeparators()
    {
    return $this->cmd_v_s(SCGMID_GetSeparators);
    }

  function SetLocaleInfo( $type, $data )
    {
    $this->cmd_is_s(SCGMID_SetLocaleInfo,$type,$data);
    }

  ///////////////////////////////////////////////////////////////////////////

  function SetUnicode( $unicode )
    {
    $this->cmd_b_v(SCGMID_SetUnicode,$unicode);
    }

  function SetDataFromTxtFile( $filename, $series_in_column, $titles_in_first_row, $titles_in_first_column )
    {
    $this->putCmd(SCGMID_SetDataFromTxtFile);
    $this->putString($filename);
    $this->putBool($series_in_column);
    $this->putBool($titles_in_first_row);
    $this->putBool($titles_in_first_column);
    fflush($this->os);

    $this->getAck();
    }

  function SetDataFromQuery()
    {
    if( isset($_SERVER) && isset($_SERVER['QUERY_STRING']) )
      $qs= $_SERVER['QUERY_STRING'];
     else
      {
      global $QUERY_STRING;
      $qs= $QUERY_STRING;
      }

    $separators= $this->GetSeparators();

    $n_series=0;
    $qa= split("&",$qs);
    foreach($qa as $vv)
      {
      $vv2= split("=",$vv);
      if( count($vv2) >= 2 )
        list($var,$val)= $vv2;
       else
        {
        $var= $vv2[0];
        $val= "";
        }

      $var= urldecode($var);
      $val= urldecode($val);

      if( strcasecmp($var,"animate") == 0 )
        {
        $animate= ($val == "0" ||
                   strcasecmp($val,"no") == 0 ||
                   strcasecmp($val,"false") == 0) ? false : true;
        $this->AnimateChart($animate);
        continue;
        }

      if( strcasecmp($var,"width") == 0 )
        {
        $this->SetWidth($val);
        continue;
        }

      if( strcasecmp($var,"height") == 0 )
        {
        $this->SetHeight($val);
        continue;
        }

      if( strcasecmp($var,"fps") == 0 )
        {
        $this->SetFrameRate($val);
        continue;
        }

      if( strcasecmp($var,"title") == 0 )
        {
        $this->SetTitle($val);
        continue;
        }

      if( strcasecmp($var,"categories") == 0 )
        {
        $this->SetCategoriesFromString($val);
        continue;
        }

      if( strcasecmp($var,"captions") == 0 )
        {
        $this->SetSeriesCaptionsFromString($val);
        continue;
        }

      if( preg_match("/^seriesxy/i",$var) )
        {
        $sva= split( '['.$separators.']', $val);
        $series_values= "";
        for( $i= 0; $i < count($sva); ++$i )
          $series_values .= (($i==0) ? "" : $separators[0]) . $sva[$i];

        //$this->AddSeries();
        $this->SetSeriesXYValuesFromString($n_series,$series_values);
        $n_series++;
        continue;
        }

      if( preg_match("/^series/i",$var) )
        {
        $sva= split( '['.$separators.']', $val);
        $series_values= "";
        for( $i= 0; $i < count($sva); ++$i )
          $series_values .= (($i==0) ? "" : $separators[0]) . $sva[$i];

        //$this->AddSeries();
        $this->SetSeriesValuesFromString($n_series,$series_values);
        $n_series++;
        continue;
        }
      }
    }

  ///////////////////////////////////////////////////////////////////////////

  function SetCategoriesFromString( $categories )
    {
    $this->cmd_s_v(SCGMID_SetCategoriesFromString,$categories);
    }
  function SetCategoriesFromArray( $categories )
    {
    $this->putCmd(SCGMID_SetCategoriesFromArray);
    $this->putArray($categories);
    fflush($this->os);

    $this->getAck();
    }

  function SetCategory( $cat_index, $category )
    {
    $this->cmd_is_v(SCGMID_SetCategory,$cat_index,$category);
    }

  function GetCategory( $cat_index )
    {
    return $this->cmd_i_s(SCGMID_GetCategory,$cat_index);
    }

  ///////////////////////////////////////////////////////////////////////////

  function ClearAll()
    {
    $this->cmd_v_v(SCGMID_ClearAll);
    }

  // for compatility only
  function AddSeries()
    {}

  function SetSeriesCaptionsFromString( $captions )
    {
    $this->cmd_s_v(SCGMID_SetSeriesCaptionsFromString,$captions);
    }

  function SetSeriesCaptionsFromArray( $captions )
    {
    $this->putCmd(SCGMID_SetCategoriesFromArray);
    $this->putArray($captions);
    fflush($this->os);

    $this->getAck();
    }

  function SetSeriesCaption( $series_index, $caption )
    {
    $this->cmd_is_v(SCGMID_SetSeriesCaption,$series_index,$caption);
    }

  function GetSeriesCaption( $series_index )
    {
    return $this->cmd_i_s(SCGMID_GetSeriesCaption,$series_index);
    }

  function GetSeriesCount()
    {
    return $this->cmd_v_i(SCGMID_GetSeriesCount);
    }

  function GetValuesCount()
    {
    return $this->cmd_v_i(SCGMID_GetValuesCount);
    }

  function SetSeriesXValuesFromString( $series_index, $values )
    {
    $this->cmd_is_v(SCGMID_SetSeriesXValuesFromString,$series_index,$values);
    }

  function SetSeriesXValuesFromArray( $series_index, $values )
    {
    $this->putCmd(SCGMID_SetSeriesXValuesFromArray);
    $this->putInteger($series_index);
    $this->putArray($values);
    fflush($this->os);

    $this->getAck();
    }

  function SetSeriesYValuesFromString( $series_index, $values )
    {
    $this->cmd_is_v(SCGMID_SetSeriesYValuesFromString,$series_index,$values);
    }

  function SetSeriesYValuesFromArray( $series_index, $values )
    {
    $this->putCmd(SCGMID_SetSeriesYValuesFromArray);
    $this->putInteger($series_index);
    $this->putArray($values);
    fflush($this->os);

    $this->getAck();
    }

  function SetSeriesValuesFromString( $series_index, $values )
    {
    $this->SetSeriesYValuesFromString( $series_index, $values );
    }

  function SetSeriesValuesFromArray( $series_index, $values )
    {
    $this->SetSeriesYValuesFromArray( $series_index, $values );
    }

  function SetSeriesXYValuesFromString( $series_index, $values )
    {
    $this->cmd_is_v(SCGMID_SetSeriesXYValuesFromString,$series_index,$values);
    }

  function GetSeriesXValue( $series_index, $value_index )
    {
    return $this->cmd_ii_d(SCGMID_GetSeriesXValue,$series_index,$value_index);
    }

  function GetSeriesYValue( $series_index, $value_index )
    {
    return $this->cmd_ii_d(SCGMID_GetSeriesYValue,$series_index,$value_index);
    }

  function GetSeriesValue( $series_index, $value_index )
    {
    return $this->GetSeriesYValue( $series_index, $value_index );
    }

  ///////////////////////////////////////////////////////////////////////////

  function SetAxisMinValue( $axis_number, $min_value )
    {
    $this->cmd_id_v(SCGMID_SetAxisMinValue,$axis_number,$min_value);
    }

  function SetAxisMaxValue( $axis_number, $max_value )
    {
    $this->cmd_id_v(SCGMID_SetAxisMaxValue,$axis_number,$max_value);
    }

  function SetAxisCrossValue( $axis_number, $cross_value )
    {
    $this->cmd_id_v(SCGMID_SetAxisCrossValue,$axis_number,$cross_value);
    }

  function ResetAxisBounds( $axis_number )
    {
    $this->cmd_i_v(SCGMID_ResetAxisBounds,$axis_number);
    }

  function SetAxisTitle( $axis_number, $title )
    {
    $this->cmd_is_v(SCGMID_SetAxisTitle,$axis_number,$title);
    }

  function GetAxisTitle( $axis_number )
    {
    return $this->cmd_i_s(SCGMID_GetAxisTitle,$axis_number);
    }

  ///////////////////////////////////////////////////////////////////////////

  function SetOutputFormat( $format )
    {
    $this->cmd_s_v(SCGMID_SetOutputFormat,$format);
    }

  function SetJPGQuality( $quality )
    {
    $this->cmd_i_v(SCGMID_SetJPGQuality,$quality);
    }

  function SetPNGCompLevel( $complevel )
    {
    $this->cmd_i_v(SCGMID_SetPNGCompLevel,$complevel);
    }

  ///////////////////////////////////////////////////////////////////////////

  function ExportAsBinary()
    {
    $this->putCmd(SCGMID_ExportAsBinary);
    fflush($this->os);

    $data= $this->getData();

    $this->getAck();

    return $data;
    }

  function ExportAsBase64()
    {
    return base64_encode( $this->ExportAsBinary() );
    }

  function ExportAsFile( $filename )
    {
    $this->cmd_s_v(SCGMID_ExportAsFile,$filename);
    }

  function ExportAsResponse()
    {
    $content_type= $this->GetHTTPContentType();
    $data= $this->ExportAsBinary();

    Header( "Content-Type: $content_type" );
    if( $this->no_cache )
      {
      Header("Expires: Mon, 01 Jan 1990 00:00:00 GMT");
      Header("Pragma: no-cache");
      }
    echo $data;
    }

  ///////////////////////////////////////////////////////////////////////////

  function SetDocumentRoot( $document_root )
    {
    $this->cmd_s_v(SCGMID_SetDocumentRoot,$document_root);
    }

  function GetHTMLTag()
    {
    return $this->cmd_v_s(SCGMID_GetHTMLTag);
    }

  function GetOutputLocation()
    {
    return $this->cmd_v_s(SCGMID_GetOutputLocation);
    }

  function GetHTTPContentType()
    {
    return $this->cmd_v_s(SCGMID_GetHTTPContentType);
    }

  ///////////////////////////////////////////////////////////////////////////

  function ClearCache()
    {
    $this->cmd_v_v(SCGMID_ClearCache);
    }

  function SetCacheName( $cache_name )
    {
    $this->cmd_s_v(SCGMID_SetCacheName,$cache_name);
    }

  function SetPrivateCacheDir( $private_cache_dir )
    {
    $this->cmd_s_v(SCGMID_SetPrivateCacheDir,$private_cache_dir);
    }

  function SetMaxCacheSize( $max_cache_size )
    {
    $this->cmd_i_v(SCGMID_SetMaxCacheSize,$max_cache_size);
    }

  function UseCache( $use_cache )
    {
    $this->cmd_b_v(SCGMID_UseCache,$use_cache);
    }

  function SetTTFontsDir( $ttf_dir )
    {
    $this->cmd_s_v(SCGMID_SetTTFontsDir,$ttf_dir);
    }

  function BatchCommands( $commands )
    {
    $this->putCmd(SCGMID_BatchCommands);
    $this->putData($commands);
    fflush($this->os);

    $this->getAck();
    }

  function SetDebugMode( $debug_mode )
    {
    $this->cmd_i_v(SCGMID_SetDebugMode,$debug_mode);
    }

}; // class SwiffChart

/////////////////////////////////////////////////////////////////////////////

?>
