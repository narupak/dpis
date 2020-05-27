<?php 

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

 //$chart_title="Graph R001002";
 //$categories_list="Group 1;Group 2;Group 3;Group 4;Group 5;Group 6;Group 7";
 //$series_caption_list="Type1;Type2;Type3";
 //$series_list="5;3;7;2;8|9;6;8;1;7|0;2;4;2;5";
 if ($categories_list) {
	$arr_categories = explode(";",$categories_list);
	$arr_series_caption = explode(";", $series_caption_list);
//	echo("series_caption=$series_caption_list<br>");
	$arr_series = explode("|", $series_list);

	$title = new title($chart_title."-".$arr_series_caption[0]);
	$title1 = new title($chart_title."-".$arr_series_caption[1]);
	$title2 = new title($chart_title."-".$arr_series_caption[2]);
	
	for($i=0; $i<count($arr_series); $i++){
//		echo("series[$i]=$arr_series[$i]<br>");
		$sub_series = explode(";",$arr_series[$i]);
		for($j=0; $j<count($sub_series); $j++){
			$series_val = intval($sub_series[$j]);
			$tmp = new pie_value($series_val,"");
			$categories_val = substr($arr_categories[$j],0,30);
			$tmp->set_label("$categories_val-$series_val","#33CCFF",14);
			$data[$i][$j]=$tmp;
			if ($dmax[$i] < intval($sub_series[$j])) {
				$dmax[$i] = intval($sub_series[$j]);
				$maxidx[$i] = $j;
			}
//			echo("data[$i][$j]=$data[$i][$j]<br>");
		}
		$tmp = new pie_value($dmax[$i],"");
		$categories_val=substr($arr_categories[$maxidx[$i]],0,30);
		$tmp->set_label("BIG-$categories_val-$dmax[$i]","#FF653F",20);
		$data[$i][$maxidx[$i]]=$tmp;
	} //for($i=0; $i<count($arr_series); $i++){

	$pie = new pie();
	$pie->set_alpha(0.5);
	$pie->set_start_angle(45);
	$pie->add_animation(new pie_fade());
	$pie->add_animation(new pie_bounce(4));
	$pie->gradient_fill();
	$pie->set_tooltip( "#val# of #total#<br>#percent# of 100%");
	$pie->set_colours( array('#1C9E05','#FF368D','#CC368D','#8844CC','#FF2288','#2C33C5','#1CCC44') );
	$pie->set_values( $data[0] );
	
	$pie1 = new pie();
	$pie1->set_alpha(0.5);
	$pie1->set_start_angle(35);
	$pie1->add_animation(new pie_fade());
	$pie1->add_animation(new pie_bounce(4));
	$pie1->gradient_fill();
	$pie1->set_tooltip( "#val# of #total#<br>#percent# of 100%" );
	$pie1->set_colours(array('#1C9E05','#FF368D','#CC368D','#8844CC','#FF2288','#2C33C5','#1CCC44'));
	$pie1->set_values( $data[1] );
	
	$pie2 = new pie();
	$pie2->set_alpha(0.5);
	$pie2->set_start_angle(55);
	$pie2->add_animation(new pie_fade());
	$pie2->add_animation(new pie_bounce(4));
	$pie2->gradient_fill();
	$pie2->set_tooltip( "#val# of #total#<br>#percent# of 100%" );
	$pie2->set_colours( array('#1C9E05','#FF368D','#CC368D','#8844CC','#FF2288','#2C33C5','#1CCC44') );
	$pie2->set_values( $data[2] );

	$chart = new open_flash_chart();
	$chart->set_title( $title );
	$chart->add_element( $pie );
	$chart1 = new open_flash_chart();
	$chart1->set_title( $title1 );
	$chart1->add_element( $pie1 );
	$chart2 = new open_flash_chart();
	$chart2->set_title( $title2 );
	$chart2->add_element( $pie2 );

	$chart->x_axis = null;
	$mydata = $chart->toPrettyString();
	$mydata1 = $chart1->toPrettyString();
	$mydata2 = $chart2->toPrettyString();
//	echo "$mydata<br>";
//	echo "$mydata1<br>";
//	echo "$mydata2<br>";
  }
?>
<html>
<head>
<script type="text/javascript" src="../../admin/js/json/json2.js"></script>
<script type="text/javascript" src="../../admin/js/swfobject.js"></script>
<script type="text/javascript">

swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart","700", "400", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata"});
swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart1","700", "400", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata1"});
swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart2","700", "400", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata2"});

function ofc_ready() 
{
//	alert("ofc ready...");
}

function get_mydata()
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

var mydata = <? echo $mydata; ?>;
//alert(JSON.stringify(mydata));
var mydata1 = <? echo $mydata1; ?>;
var mydata2 = <? echo $mydata2; ?>;

</script>
</head>

<body style="background-color:transparent;" topmargin="0" leftmargin="0" bottommargin="0">
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
  <tr height="100%"> 
    <td align="center"><div id="my_chart"></div></td>
  </tr>
  <tr height="100%"> 
	<td align="center"><div id="my_chart1"></div></td>
  </tr>
  <tr height="100%"> 
    <td align="center"><div id="my_chart2"></div></td>
  </tr>
  </table>
<!-- <a href="javascript:load_mydata()">display mydata</a> || <a href="javascript:load_mydata1()">display mydata1</a>-->
<script language="JavaScript" type="text/JavaScript" src="footer.js"></script>
  <br>
  <br>
</body>