<?php 
	include( $_SERVER['DOCUMENT_ROOT']."/admin/graph/generate_data.php" );
	include( $_SERVER['DOCUMENT_ROOT']."/admin/php-ofc-library/open-flash-chart.php");
	include( $_SERVER['DOCUMENT_ROOT']."/graph/types/iso8859ToUTF8.php");

	$chart_title = iso8859_11toUTF8($chart_title);
	$categories_list = iso8859_11toUTF8($categories_list);
	$series_caption_list = iso8859_11toUTF8($series_caption_list);
	
//	echo "***$chart_title<br>";
//	echo "***$categories_list<br>";
//	echo "***$series_caption_list<br>";
//	echo "***$series_list<br>";

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
//			echo("data[$i][$j]=".$data[$i][$j]."<br>");
			if ($dmax < intval($sub_series[$j])) {
				$dmax = intval($sub_series[$j]);
			} 
			if ($dmin > intval($sub_series[$j])) {
				$dmin = intval($sub_series[$j]);
			}
		}
	} //for($i=0; $i<count($arr_series); $i++){
	
	$arr_bg_color=array('#FFFFFF','#EBEAE9','#D5FFFF','#E6FFEA','#FEFDD6','#FFFDCC','#FFDDFC');
	$arr_tick_color=array('#000000','#8F0202','#925600','#929200','#009292','#000F92','#920083');
	
	$arr_anime=array('pop-up','explode','mid-slide','drop','fade-in','shrink-in');
	
	for($i=0; $i<count($arr_series_caption); $i++) {
	// color for bar
		$rand_color=random_color();
		$bar[$i] = new bar_cylinder();
		$bar[$i]->set_values( $data[$i] );
		$bar[$i]->set_colour($rand_color);
		$bar[$i]->key($arr_series_caption[$i], 12);
		$bar[$i]->set_tooltip( "$arr_series_caption[$i] <br>จำนวน:#val#" );
		$rand_anime = rand(0,5);
		$rand_delay  = rand(10,30)/10;
		$rand_cascade = rand(10,50)/10;
		$bar[$i]->set_on_show(new bar_on_show($arr_anime[$rand_anime], $rand_cascade, $rand_delay));	

		$chart[$i] = new open_flash_chart();
		$title = new title($arr_series_caption[$i]);
		$chart[$i]->set_title($title);
		$chart[$i]->add_element( $bar[$i] );

		$x_labels = new x_axis_labels();
//		$x_labels->rotate(0);
		$x_labels->set_vertical();
		$x_labels->set_labels($arr_categories);

		$rand_tick_color = rand(0,6);

		$x_axis = new x_axis();
		$x_axis->set_3d( 5 );
		$x_axis->colour = $arr_tick_color[$rand_tick_color];
		$x_axis->tick_height(12);
		$x_axis->stroke(2);
		$x_axis->set_labels_from_array($arr_categories);
//		$x_axis->set_grid_colour('#550044');
//		$x_axis->set_labels($x_labels);
		$chart[$i]->set_x_axis( $x_axis );

		$y_axis = new y_axis();
		$ystep=ceil(($dmax - $dmin) / 10);
		$y_axis->set_range( $dmin, $dmax+$ystep, $ystep );
		$y_axis->colour = $arr_tick_color[$rand_tick_color];;
		$chart[$i]->set_y_axis( $y_axis );

		$rand_bg_color = rand(0,6);
		$chart[$i]->set_bg_colour( $arr_bg_color[$rand_bg_color] );
		$mydata[$i] = $chart[$i]->toPrettyString();
//		echo "k=$k=>$mydata[$k]<br>";
  	} // end loop $i
  }
  	function random_color() {
		$r=dechex(rand(0,255));
		$g=dechex(rand(0,255));
		$b=dechex(rand(0,255));
		if (strlen($r) < 2) { $r="0$r"; }
		if (strlen($g) < 2) { $g="0$g"; }
		if (strlen($b) < 2) { $b="0$b"; }
		$rand_color='#'.$r.$g.$b.'';
		return $rand_color;
	}
?>
<html>
<head>
<script type="text/javascript" src="../../admin/js/json/json2.js"></script>
<script type="text/javascript" src="../../admin/js/swfobject.js"></script>
<script type="text/javascript">

swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart","700", "400", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata0"});
<? if (count($mydata) > 1) { ?>
	swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart1","700", "400", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata1"});
<? } ?>
<? if (count($mydata) > 2) { ?>
	swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart2","700", "400", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata2"});
<? } ?>
<? if (count($mydata) > 3) { ?>
	swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart3","700", "400", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata3"});
<? } ?>
<? if (count($mydata) > 4) { ?>
	swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart4","700", "400", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata4"});
<? } ?>
<? if (count($mydata) > 5) { ?>
	swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart5","700", "400", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata5"});
<? } ?>
<? if (count($mydata) > 6) { ?>
	swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart6","700", "400", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata6"});
<? } ?>
<? if (count($mydata) > 7) { ?>
	swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart7","700", "400", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata7"});
<? } ?>
<? if (count($mydata) > 8) { ?>
	swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart8","700", "400", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata8"});
<? } ?>
<? if (count($mydata) > 9) { ?>
	swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart9","700", "400", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata9"});
<? } ?>
<? if (count($mydata) > 10) { ?>
	swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart10","700", "400", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata10"});
<? } ?>
<? if (count($mydata) > 11) { ?>
	swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart11","700", "400", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata11"});
<? } ?>
<? if (count($mydata) > 12) { ?>
	swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart12","700", "400", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata12"});
<? } ?>
<? if (count($mydata) > 13) { ?>
	swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart13","700", "400", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata13"});
<? } ?>
<? if (count($mydata) > 14) { ?>
	swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart14","700", "400", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata14"});
<? } ?>
<? if (count($mydata) > 15) { ?>
	swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart15","700", "400", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata15"});
<? } ?>
<? if (count($mydata) > 16) { ?>
	swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart16","700", "400", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata16"});
<? } ?>
<? if (count($mydata) > 17) { ?>
	swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart17","700", "400", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata17"});
<? } ?>
<? if (count($mydata) > 18) { ?>
	swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart18","700", "400", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata18"});
<? } ?>
<? if (count($mydata) > 19) { ?>
	swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart19","700", "400", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata19"});
<? } ?>

function ofc_ready() 
{
//	alert("ofc ready...");
}

function get_mydata0()
{ 
//	alert("reading mydata...");
	return JSON.stringify(mydata);
}
function get_mydata1()
{
//	alert("reading mydata1...");
	return JSON.stringify(mydata1);
}
function get_mydata2()
{
//	alert("reading mydata2...");
	return JSON.stringify(mydata2);
}
function get_mydata3()
{
	//alert("reading mydata3...");
	return JSON.stringify(mydata3);
}
function get_mydata4()
{
	//alert("reading mydata4...");
	return JSON.stringify(mydata4);
}
function get_mydata5()
{
	//alert("reading mydata5...");
	return JSON.stringify(mydata5);
}
function get_mydata6()
{
	//alert("reading mydata6...");
	return JSON.stringify(mydata6);
}
function get_mydata7()
{
	//alert("reading mydata7...");
	return JSON.stringify(mydata7);
}
function get_mydata8()
{
	//alert("reading mydata8...");
	return JSON.stringify(mydata8);
}
function get_mydata9()
{
	//alert("reading mydata9...");
	return JSON.stringify(mydata9);
}
function get_mydata10()
{
	//alert("reading mydata10...");
	return JSON.stringify(mydata10);
}
function get_mydata11()
{
	//alert("reading mydata11...");
	return JSON.stringify(mydata11);
}
function get_mydata12()
{
	//alert("reading mydata12...");
	return JSON.stringify(mydata12);
}
function get_mydata13()
{
	//alert("reading mydata13...");
	return JSON.stringify(mydata13);
}
function get_mydata14()
{
	//alert("reading mydata14...");
	return JSON.stringify(mydata14);
}
function get_mydata15()
{
	//alert("reading mydata15...");
	return JSON.stringify(mydata15);
}
function get_mydata16()
{
	//alert("reading mydata16...");
	return JSON.stringify(mydata16);
}
function get_mydata17()
{
	//alert("reading mydata17...");
	return JSON.stringify(mydata17);
}
function get_mydata18()
{
	//alert("reading mydata18...");
	return JSON.stringify(mydata18);
}
function get_mydata19()
{
	//alert("reading mydata19...");
	return JSON.stringify(mydata19);
}

var mydata = <? echo $mydata[0]; ?>;
<? if (count($mydata) > 1) { ?>
	var mydata1 = <? echo $mydata[1]; } ?>;
<? if (count($mydata) > 2) { ?>
	var mydata2 = <? echo $mydata[2]; } ?>;
<? if (count($mydata) > 3) { ?>
	var mydata3 = <? echo $mydata[3]; } ?>;
<? if (count($mydata) > 4) { ?>
	var mydata4 = <? echo $mydata[4]; } ?>;
<? if (count($mydata) > 5) { ?>
	var mydata5 = <? echo $mydata[5]; } ?>;
<? if (count($mydata) > 6) { ?>
	var mydata6 = <? echo $mydata[6]; } ?>;
<? if (count($mydata) > 7) { ?>
	var mydata7 = <? echo $mydata[7]; } ?>;
<? if (count($mydata) > 8) { ?>
	var mydata8 = <? echo $mydata[8]; } ?>;
<? if (count($mydata) > 9) { ?>
	var mydata9 = <? echo $mydata[9]; } ?>;
<? if (count($mydata) > 10) { ?>
	var mydata10 = <? echo $mydata[10]; } ?>;
<? if (count($mydata) > 11) { ?>
	var mydata11 = <? echo $mydata[11]; } ?>;
<? if (count($mydata) > 12) { ?>
	var mydata12 = <? echo $mydata[12]; } ?>;
<? if (count($mydata) > 13) { ?>
	var mydata13 = <? echo $mydata[13]; } ?>;
<? if (count($mydata) > 14) { ?>
	var mydata14 = <? echo $mydata[14]; } ?>;
<? if (count($mydata) > 15) { ?>
	var mydata15 = <? echo $mydata[15]; } ?>;
<? if (count($mydata) > 16) { ?>
	var mydata16 = <? echo $mydata[16]; } ?>;
<? if (count($mydata) > 17) { ?>
	var mydata17 = <? echo $mydata[17]; } ?>;
<? if (count($mydata) > 18) { ?>
	var mydata18 = <? echo $mydata[18]; } ?>;
<? if (count($mydata) > 19) { ?>
	var mydata19 = <? echo $mydata[19]; } ?>;

</script>
</head>

<body style="background-color:transparent;" topmargin="0" leftmargin="0" bottommargin="0">
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
  <tr height="100%"> 
    <td align="center"><div id="my_chart"></div></td>
  </tr>
<? if (count($mydata) > 1) { ?>  
  <tr height="100%"> 
	<td align="center"><div id="my_chart1"></div></td>
  </tr>
<? } ?>
<? if (count($mydata) > 2) { ?>  
  <tr height="100%"> 
	<td align="center"><div id="my_chart2"></div></td>
  </tr>
<? } ?>
<? if (count($mydata) > 3) { ?>  
  <tr height="100%"> 
	<td align="center"><div id="my_chart3"></div></td>
  </tr>
<? } ?>
<? if (count($mydata) > 4) { ?>  
  <tr height="100%"> 
	<td align="center"><div id="my_chart4"></div></td>
  </tr>
<? } ?>
<? if (count($mydata) > 5) { ?>  
  <tr height="100%"> 
	<td align="center"><div id="my_chart5"></div></td>
  </tr>
<? } ?>
<? if (count($mydata) > 6) { ?>  
  <tr height="100%"> 
	<td align="center"><div id="my_chart6"></div></td>
  </tr>
<? } ?>
<? if (count($mydata) > 7) { ?>  
  <tr height="100%"> 
	<td align="center"><div id="my_chart7"></div></td>
  </tr>
<? } ?>
<? if (count($mydata) > 8) { ?>  
  <tr height="100%"> 
	<td align="center"><div id="my_chart8"></div></td>
  </tr>
<? } ?>
<? if (count($mydata) > 9) { ?>  
  <tr height="100%"> 
	<td align="center"><div id="my_chart9"></div></td>
  </tr>
<? } ?>
<? if (count($mydata) > 10) { ?>  
  <tr height="100%"> 
	<td align="center"><div id="my_chart10"></div></td>
  </tr>
<? } ?>
<? if (count($mydata) > 11) { ?>  
  <tr height="100%"> 
	<td align="center"><div id="my_chart11"></div></td>
  </tr>
<? } ?>
<? if (count($mydata) > 12) { ?>  
  <tr height="100%"> 
	<td align="center"><div id="my_chart12"></div></td>
  </tr>
<? } ?>
<? if (count($mydata) > 13) { ?>  
  <tr height="100%"> 
	<td align="center"><div id="my_chart13"></div></td>
  </tr>
<? } ?>
<? if (count($mydata) > 14) { ?>  
  <tr height="100%"> 
	<td align="center"><div id="my_chart14"></div></td>
  </tr>
<? } ?>
<? if (count($mydata) > 15) { ?>  
  <tr height="100%"> 
	<td align="center"><div id="my_chart15"></div></td>
  </tr>
<? } ?>
<? if (count($mydata) > 16) { ?>  
  <tr height="100%"> 
	<td align="center"><div id="my_chart16"></div></td>
  </tr>
<? } ?>
<? if (count($mydata) > 17) { ?>  
  <tr height="100%"> 
	<td align="center"><div id="my_chart17"></div></td>
  </tr>
<? } ?>
<? if (count($mydata) > 18) { ?>  
  <tr height="100%"> 
	<td align="center"><div id="my_chart18"></div></td>
  </tr>
<? } ?>
<? if (count($mydata) > 19) { ?>  
  <tr height="100%"> 
	<td align="center"><div id="my_chart19"></div></td>
  </tr>
<? } ?>
  </table>
<!-- <a href="javascript:load_mydata()">display mydata</a> || <a href="javascript:load_mydata1()">display mydata1</a>-->
<script language="JavaScript" type="text/JavaScript" src="footer.js"></script>
  <br>
  <br>
</body>