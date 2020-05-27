<?php 
  include( $_SERVER['DOCUMENT_ROOT']."/admin/graph/generate_data.php" );
  require( $_SERVER['DOCUMENT_ROOT']."/graph/include/SwiffChartWin.php" );
  
  if($categories_list){
  	
	  $chart= new SwiffChart;
//	  $chart->SetTTFontsDir("/var/www/html/ttfonts");
	
	  $sep= ';';
	  $ignoremultseparators= true;
	  $chart->SetSeparators($sep, $ignoremultseparators);
	
	  $categories = $categories_list;
	  $chart->SetCategoriesFromString($categories);

	  $arr_series_caption = explode(";", $series_caption_list);
	  $arr_series = explode("|", $series_list);
	  for($i=0; $i<count($arr_series_caption); $i++){
	  	$series = $arr_series[$i];		

		$chart->SetSeriesValuesFromString($i, $series);
		$chart->SetSeriesCaption($i, $arr_series_caption[$i]);
	  } // for
	
	  // Set the chart title
	  $chart->SetTitle($chart_title);
	  $chart->SetSubtitle($chart_subtitle);
	  
	  // Set chart size
	  $chart->SetWidth($setWidth);
	  $chart->SetHeight($setHeight);
	
	  // Set Vertical Axis Value	  
	  if(isset($setAxisYMin) && $setAxisYMin != "") $chart->SetAxisMinValue(1, $setAxisYMin);
	  if(isset($setAxisYMax) && $setAxisYMax != "") $chart->SetAxisMaxValue(1, $setAxisYMax);
	  
	  // Apply a Column style
	  // The chart type is stored in the style file (*.scs)
	  $chart->LoadStyle( $style );
		  
	  $chart->SetLooping( false );
	
	  $chart->SetOutputFormat( $selectedFormat );
	  $chart_res= $chart->GetHTMLTag();
  }
?>
<body style="background-color:transparent;" topmargin="0" leftmargin="0" bottommargin="0">
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
  <?if(!$categories_list){?>
  <tr> 
    <td align="center"><b>No data to display.</b></td>
  </tr>
  <tr> 
    <td align="center">&nbsp;</td>
  </tr>
  <?}else{?>
  <tr height="100%"> 
    <td align="center"><?=$chart_res?></td>
  </tr>
  <!--tr> 
    <td align="center">&nbsp;</td>
  </tr-->
  <?}?>
  <?if(!$show_url){?>
  <!--tr> 
    <td align="center"><input type="button" name="close" value="Close" onClick="self.close()"></td>
  </tr-->
  <?}?>
</table>
<?if($categories_list){?>
<script language="JavaScript" type="text/JavaScript" src="footer.js"></script>
<?}?>
</body>