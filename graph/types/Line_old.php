<?php
include("../../admin/graph/fusionchart_files/php/FusionCharts.php");
?>
<HTML>
<HEAD>
	<TITLE>
		--
	</TITLE>
	<?php
	//You need to include the following JS file, if you intend to embed the chart using JavaScript.
	//Embedding using JavaScripts avoids the "Click to Activate..." issue in Internet Explorer
	//When you make your own charts, make sure that the path to this JS file is correct. Else, you 
    //would get JavaScript errors.
	?>	
	<SCRIPT LANGUAGE="Javascript" SRC="../../admin/graph/fusionchart_files/js/FusionCharts.js"></SCRIPT>
	<style type="text/css">
	<!--
	body {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 12px;
	}
	-->
	</style>
</HEAD>
<BODY>

<CENTER>
<?php
	include( $_SERVER['DOCUMENT_ROOT']."/admin/graph/generate_data.php" );
	$type = 'Line';
	include( $_SERVER['DOCUMENT_ROOT']."/graph/types/prepare_format.php" );	

	$data = "<categories>$category_node</categories>";
	$data .= "<dataset seriesName='ทั่วไป' color='0080C0' anchorBorderColor='0080C0'>$data_node</dataset>";
	$data = "<chart caption='".$chart_title."' showValues='1' numberPrefix='$' numVisiblePlot='9'>".$data."</chart>";
	echo renderChart("../../admin/graph/fusionchart_files/swf/ScrollLine2D.swf", "", $data, "LineChart", 650, 500, false, false);
?>
</CENTER>
</BODY>
</HTML>