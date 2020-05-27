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
	$type = 'Bar';
	include( $_SERVER['DOCUMENT_ROOT']."/graph/types/prepare_format.php" );	

	$data = "<chart caption='".$chart_title."' bgColor='F1F1F1' showValues='1' canvasBorderThickness='1' canvasBorderColor='999999' plotFillAngle='330' plotBorderColor='999999' showAlternateVGridColor='1' divLineAlpha='0'>".$data_node."</chart>";

	echo renderChart("../../admin/graph/fusionchart_files/swf/Bar2D.swf", "", $data, "LineChart", 650, 2000, false, false);
?>
</CENTER>
</BODY>
</HTML>