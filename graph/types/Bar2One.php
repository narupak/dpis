<?php 
	include( $_SERVER['DOCUMENT_ROOT']."/admin/graph/generate_data.php" );
	include( $_SERVER['DOCUMENT_ROOT']."/admin/php-ofc-library/open-flash-chart.php");
	include( $_SERVER['DOCUMENT_ROOT']."/graph/types/iso8859ToUTF8.php");

	$chart_title=iso8859_11toUTF8($chart_title);
	$categories_list=iso8859_11toUTF8($categories_list);
	$series_caption_list=iso8859_11toUTF8($series_caption_list);
//	$series_list=$series_list;
	
//	echo "***$chart_title<br>";
//	echo "***$categories_list<br>";
//	echo "***$series_caption_list<br>";
//	echo "***$series_list<br>";

	$animation_1 = isset($_GET['animation_1'])?$_GET['animation_1']:'pop-up';
	$delay_1        = isset($_GET['delay_1'])?$_GET['delay_1']:1;
	$cascade_1   = isset($_GET['cascade_1'])?$_GET['cascade_1']:4;

 //$chart_title="Graph R001002";
 $categories_list="Group 1;Group 2;Group 3;Group 4;Group 5;Group 6;Group 7";
 //$series_caption_list="Type1;Type2;Type3";
 //$series_list="5;3;7;2;8|9;6;8;1;7|0;2;4;2;5";
 if ($categories_list) {
	$title = new title($chart_title);
	
	$arr_categories = explode(";",$categories_list);
	$arr_series_caption = explode(";", $series_caption_list);
//	echo("series_caption=$series_caption_list<br>");
	$arr_series = explode("|", $series_list);
	$dmin=0; $dmax=10;
	for($i=0; $i<count($arr_series); $i++){
//		echo("series[$i]=$arr_series[$i]<br>");
		$sub_series = explode(";",$arr_series[$i]);
		for($j=0; $j<count($sub_series); $j++){
			$data[$i][$j] = intval($sub_series[$j]);
//			echo("data[$i][$j]=$data[$i][$j]<br>");
			if ($dmax < intval($sub_series[$j])) {
				$dmax = intval($sub_series[$j]);
			} 
			if ($dmin > intval($sub_series[$j])) {
				$dmin = intval($sub_series[$j]);
			}
		}
	} //for($i=0; $i<count($arr_series); $i++){

	$bar = new bar_cylinder();
	$bar->set_values( $data[0]);
	$bar->set_colour('#ff0000');
	$bar->key($arr_series_caption[0], 12);
	$bar->set_tooltip( "$arr_series_caption[0] <br>จำนวน:#val#" );
//	$bonshow = new bar_on_show('fade-in', 2, 4);
//	$bar->set_on_show($bonshow);	
	
	$bar1 = new bar_cylinder();
	$bar1->set_values( $data[1]);
	$bar1->set_colour('#00ff00');
	$bar1->key($arr_series_caption[1], 12);
	$bar1->set_tooltip( "$arr_series_caption[1] <br>จำนวน:#val#" );
//	$bonshow1 = new bar_on_show('fade-in', 2, 4);
//	$bar1->set_on_show($bonshow);	
	
	$bar2 = new bar_cylinder();
	$bar2->set_values( $data[2]);
	$bar2->set_colour('#0000ff');
	$bar2->key($arr_series_caption[2], 12);
	$bar2->set_tooltip( "$arr_series_caption[2] <br>จำนวน:#val#" );
//	$bar2->set_on_show(new bar_on_show($animation_1, $cascade_1, $delay_1));	

	$chart = new open_flash_chart();
	$chart->set_title( $title );
	$chart->add_element( $bar );
	$chart->add_element( $bar1 );
	$chart->add_element( $bar2 );

	$x_labels = new x_axis_labels();
	$x_labels->rotate(20);
	$x_labels->set_labels($arr_categories);

	$x_axis = new x_axis();
	$x_axis->set_3d( 5 );
	$x_axis->colour = '#d0d0d0';
	//$x_axis->set_labels_from_array($arr_categories);
	$x_axis->set_labels($x_labels);
	$chart->set_x_axis( $x_axis );

	$y_axis = new y_axis();
	$y_axis->set_range( $dmin, $dmax, ceil(($dmax - $dmin) / 10) );
	$chart->set_y_axis( $y_axis );

	$mydata = $chart->toPrettyString();
//	echo $mydata;
  }
?>
<html>
<head>
<script type="text/javascript" src="../../admin/js/json/json2.js"></script>
<script type="text/javascript" src="../../admin/js/swfobject.js"></script>
<script type="text/javascript">

//swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart","550", "300", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata"});
swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart","700", "400", "9.0.0");

function ofc_ready() 
{
	alert("ofc ready...");
}

function open_flash_chart_data()
{ 
	alert("reading mydata...");
	return JSON.stringify(mydata);
}

function load_mydata()
{
	tmp = findSWF("my_chart");
	x = tmp.load(JSON.stringify(mydata));
}

function findSWF(movieName)
{
	if (navigator.appName.indexOf("Microsoft")!= -1) {
		return window[movieName];
	} else {
		return document[movieName];
	}
}

var mydata = <? echo $mydata; ?>
//alert(JSON.stringify(mydata));

</script>
</head>

<body style="background-color:transparent;" topmargin="0" leftmargin="0" bottommargin="0">
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
  <tr height="100%"> 
    <td align="center"><div id="my_chart"></div></td>
  </tr>
  </table>
<script language="JavaScript" type="text/JavaScript" src="footer.js"></script>
</body>