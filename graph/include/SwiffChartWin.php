<?php

// Swiff Chart Generator 3 : PHP Module for Windows

// Copyright (C) 2002-2007, GlobFX Technologies, SARL.
// All Rights Reserved.
// This is UNPUBLISHED PROPRIETARY SOURCE CODE of GlobFX Technologies, SARL.
// The contents of this file may not be disclosed to third parties, copied or
// duplicated in any form, in whole or in part, without the prior written
// permission of GlobFX Technologies, SARL.

class SwiffChart
{
  var $com;
  var $no_cache;

  ///////////////////////////////////////////////////////////////////////////

  function SwiffChart()
    {
    // Do not write:
    // $this->com= new COM("SwiffChartObject.ChartObj") || die("...")
    // PHP 5.1 to 5.1.2 would try to convert $this->com to a boolean, requesting
    // the "Value" COM property, which does not exist. This would lead to an
    // COM uncaught exception. Read http://bugs.php.net/bug.php?id=35954

    $this->com= new COM("SwiffChartObject.ChartObj");
    // Do no write "if( $this->com == null ) ..." for the same reason as above.
    if( is_null($this->com) )
      die("Unable to instantiate Swiff Chart Object");

    $document_root= $this->_get_document_root();

    if( !empty($document_root) )
      $this->com->SetDocumentRoot( $document_root );

    $this->no_cache= true;
    }

  ///////////////////////////////////////////////////////////////////////////

  function _get_document_root()
    {
    $document_root= null;

    //global $_SERVER; no need, it is a superglobal
    if( isset($_SERVER) && isset($_SERVER['DOCUMENT_ROOT']) )
      $document_root= $_SERVER['DOCUMENT_ROOT'];
     else
      {
      global $HTTP_SERVER_VARS;
      if( isset($HTTP_SERVER_VARS) && isset($HTTP_SERVER_VARS['DOCUMENT_ROOT']) )
        $document_root= $HTTP_SERVER_VARS['DOCUMENT_ROOT'];
       else
        {
        global $DOCUMENT_ROOT;
        if( isset($DOCUMENT_ROOT) )
          $document_root= $DOCUMENT_ROOT;
        }
      }

    if( !is_string($document_root) )
      $document_root= null;

    return $document_root;
    }

  ///////////////////////////////////////////////////////////////////////////

  function Release()
    {
    return $this->com->Release();
    }

  ///////////////////////////////////////////////////////////////////////////

  function GetVersion()
    {
    return $this->com->GetVersion();
    }

  ///////////////////////////////////////////////////////////////////////////

  function LoadStyle( $style_filename )
    {
    $this->com->LoadStyle( $style_filename );
    }

  function SetStyleData( $style_data )
    {
    $this->com->SetStyleData( $style_data );
    }

  ///////////////////////////////////////////////////////////////////////////

  function SetWidth( $width )
    {
    $this->com->SetWidth( $width );
    }
  function GetWidth()
    {
    return $this->com->GetWidth();
    }
  function SetHeight( $height )
    {
    $this->com->SetHeight( $height );
    }
  function GetHeight()
    {
    return $this->com->GetHeight();
    }

  ///////////////////////////////////////////////////////////////////////////

  function SetCompressed( $compressed )
    {
    $this->com->SetCompressed( $compressed );
    }
  function IsCompressed()
    {
    return $this->com->IsCompressed();
    }
  function CompressSWF( $compressed )
    {
    $this->SetCompressed( $compressed );
    }
  function IsSWFCompressed()
    {
    return $this->IsCompressed();
    }

  function AnimateChart( $animate )
    {
    $this->com->AnimateChart( $animate );
    }

  function IsAnimated()
    {
    return $this->com->IsAnimated();
    }

  function SetFrameRate( $framerate )
    {
    $this->com->SetFrameRate( $framerate );
    }
  function GetFrameRate()
    {
    return $this->com->GetFrameRate();
    }

  function SetLooping( $looping )
    {
    $this->com->SetLooping( $looping );
    }
  function IsLooping()
    {
    return $this->com->IsLooping();
    }

  function ProtectSWF( $swf_protected )
    {
    $this->com->ProtectSWF( $swf_protected );
    }
  function IsSWFProtected()
    {
    return $this->com->IsSWFProtected();
    }

  ///////////////////////////////////////////////////////////////////////////

  function SetTitle( $title )
    {
    $this->com->SetTitle( $title );
    }
  function GetTitle()
    {
    return $this->com->GetTitle();
    }

  function SetSubtitle( $title )
    {
    $this->com->SetSubtitle( $title );
    }
  function GetSubtitle()
    {
    return $this->com->GetSubtitle();
    }

  ///////////////////////////////////////////////////////////////////////////

  function SetSeparators( $separators, $ignore_multiple_sep )
    {
    $this->com->SetSeparators( $separators, $ignore_multiple_sep );
    }
  function GetSeparators()
    {
    return $this->com->GetSeparators();
    }

  function SetLocaleInfo( $type, $data )
    {
    $this->com->SetLocaleInfo( $type, $data );
    }

  ///////////////////////////////////////////////////////////////////////////

  function SetUnicode( $unicode )
    {
    $this->com->SetUnicode( $unicode );
    }

  function SetDataFromTxtFile( $filename, $series_in_column, $titles_in_first_row, $titles_in_first_column )
    {
    $this->com->SetDataFromTxtFile( $filename, $series_in_column, $titles_in_first_row, $titles_in_first_column );
    }

  function SetDataFromQuery()
    {
    if( array_key_exists("QUERY_STRING", $_SERVER))
      $qs= $_SERVER["QUERY_STRING"];
     else
      $qs= "";

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
        $sva= split( '['.$separators.']' ,$val);
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
        $sva= split( '['.$separators.']' ,$val);
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
    $this->com->SetCategoriesFromString( $categories );
    }

  function SetCategoriesFromArray( $array )
    {
    if( ! empty($array) )
      {
      ksort($array);
      end($array);
      $n= key($array)+1;

      for( $i= 0; $i < $n; ++$i )
        if( ! isset($array[$i]) )
          $array[$i]= '';
      ksort($array);
      }

    $this->com->SetCategoriesFromArray( $array );
    }

  function GetCategory( $cat_index )
    {
    return $this->com->GetCategory( $cat_index );
    }

  ///////////////////////////////////////////////////////////////////////////

  function ClearAll()
    {
    $this->com->ClearAll();
    }

  // for compatibility only
  function AddSeries()
    {}

  function SetSeriesCaptionsFromString( $captions )
    {
    $this->com->SetSeriesCaptionsFromString( $captions );
    }

  function SetSeriesCaptionsFromArray( $array )
    {
    if( ! empty($array) )
      {
      ksort($array);
      end($array);
      $n= key($array)+1;

      for( $i= 0; $i < $n; ++$i )
        if( ! isset($array[$i]) )
          $array[$i]= '';
      ksort($array);
      }

    $this->com->SetSeriesCaptionsFromArray( $array );
    }

  function SetSeriesCaption( $series_index, $caption )
    {
    $this->com->SetSeriesCaption($series_index,$caption);
    }

  function GetSeriesCaption( $series_index )
    {
    return $this->com->GetSeriesCaption( $series_index );
    }

  function GetSeriesCount()
    {
    return $this->com->GetSeriesCount();
    }

  function GetValuesCount()
    {
    return $this->com->GetValuesCount();
    }

  function SetSeriesXValuesFromString( $series_index, $values )
    {
    $this->com->SetSeriesXValuesFromString( $series_index, $values );
    }

  function SetSeriesXValuesFromArray( $series_index, $array )
    {
    $this->com->SetSeriesXValuesFromArray( $series_index, $array );
    }

  function SetSeriesYValuesFromString( $series_index, $values )
    {
    $this->com->SetSeriesYValuesFromString( $series_index, $values );
    }

  function SetSeriesYValuesFromArray( $series_index, $array )
    {
    $this->com->SetSeriesYValuesFromArray( $series_index, $array );
    }

  function SetSeriesZValuesFromString( $series_index, $values )
    {
    $this->com->SetSeriesZValuesFromString( $series_index, $values );
    }

  function SetSeriesZValuesFromArray( $series_index, $array )
    {
    $this->com->SetSeriesZValuesFromArray( $series_index, $array );
    }

  function SetSeriesValuesFromString( $series_index, $values )
    {
    $this->com->SetSeriesValuesFromString( $series_index, $values );
    }

  function SetSeriesValuesFromArray( $series_index, $array )
    {
    if( ! empty($array) )
      {
      ksort($array);
      end($array);
      $n= key($array)+1;

      for( $i= 0; $i < $n; ++$i )
        if( ! isset($array[$i]) )
          $array[$i]= '';
      ksort($array);
      }

    $this->com->SetSeriesValuesFromArray( $series_index, $array );
    }

  function SetSeriesXYValuesFromString( $series_index, $values )
    {
    $this->com->SetSeriesXYValuesFromString( $series_index, $values );
    }

  function GetSeriesXValue( $series_index, $value_index )
    {
    return $this->com->GetSeriesXValue( $series_index, $value_index);
    }

  function GetSeriesYValue( $series_index, $value_index )
    {
    return $this->com->GetSeriesYValue( $series_index, $value_index );
    }

  function GetSeriesZValue( $series_index, $value_index )
    {
    return $this->com->GetSeriesZValue( $series_index, $value_index );
    }

  function GetSeriesValue( $series_index, $value_index )
    {
    return $this->com->GetSeriesValue( $series_index, $value_index );
    }

  ///////////////////////////////////////////////////////////////////////////

  function SetSeriesColor( $series_index, $color )
    {
    $this->com->SetSeriesColor($series_index,$color);
    }

  function SetSeriesValueColor( $series_index, $value_index, $color )
    {
    $this->com->SetSeriesValueColor($series_index,$value_index,$color);
    }

  function SetSeriesTrendlineColor( $series_index, $color )
    {
    $this->com->SetSeriesTrendlineColor($series_index,$color);
    }

  ///////////////////////////////////////////////////////////////////////////

  function SetAxisMinValue( $axis_number, $value )
    {
    $this->com->SetAxisMinValue( $axis_number, $value );
    }

  function SetAxisMaxValue( $axis_number, $value )
    {
    $this->com->SetAxisMaxValue( $axis_number, $value );
    }

  function SetAxisCrossValue( $axis_number, $value )
    {
    $this->com->SetAxisCrossValue( $axis_number, $value );
    }

  function ResetAxisBounds( $axis_number )
    {
    $this->com->ResetAxisBounds( $axis_number );
    }

  function SetAxisTitle( $axis_number, $title )
    {
    $this->com->SetAxisTitle($axis_number,$title);
    }

  function GetAxisTitle( $axis_number )
    {
    return $this->com->GetAxisTitle( $axis_number );
    }

  ///////////////////////////////////////////////////////////////////////////

  function SetOutputFormat( $output_format )
    {
    $this->com->SetOutputFormat( $output_format );
    }

  function SetJPGQuality( $quality )
    {
    $this->com->SetJPGQuality( $quality );
    }

  function SetPNGCompLevel( $comp_level )
    {
    $this->com->SetPNGCompLevel( $comp_level );
    }

  ///////////////////////////////////////////////////////////////////////////

  function ExportAsBinary()
    {
    return base64_decode($this->com->ExportAsBase64());
    }

  function ExportAsBase64()
    {
    return $this->com->ExportAsBase64();
    }

  function ExportAsFile( $filename )
    {
    $this->com->ExportAsFile( $filename );
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
    if( !is_string($document_root) )
      return;

    if( !empty($document_root) )
      $this->com->SetDocumentRoot( $document_root );
    }

  function SetAppRootUrl( $app_root_url )
    {
    if( !is_string($app_root_url) )
      return;

    if( $app_root_url != null )
      $this->com->SetAppRootUrl( $app_root_url );
    }

  function GetHTMLTag()
    {
    return $this->com->GetHTMLTag();
    }

  function GetOutputLocation()
    {
    return $this->com->GetOutputLocation();
    }

  function GetHTTPContentType()
    {
    return $this->com->GetHTTPContentType();
    }

  ///////////////////////////////////////////////////////////////////////////

  function ClearCache()
    {
    $this->com->ClearCache();
    }

  function SetCacheName( $cache_name )
    {
    return $this->com->SetCacheName( $cache_name );
    }

  function SetPrivateCacheDir( $private_cache_dir )
    {
    return $this->com->SetPrivateCacheDir( $private_cache_dir );
    }

  function SetMaxCacheSize( $max_cache_size )
    {
    return $this->com->SetMaxCacheSize( $max_cache_size );
    }

  function UseCache( $use_cache )
    {
    $this->com->UseCache( $use_cache );
    }

  ///////////////////////////////////////////////////////////////////////////

  function BatchCommands( $commands )
    {
    $this->com->BatchCommands( $commands );
    }

  function SetDebugMode( $debug_mode )
    {
    $this->com->SetDebugMode( $debug_mode );
    }

}; // class SwiffChart

/////////////////////////////////////////////////////////////////////////////

?>
