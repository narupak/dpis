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
 $data = (array) null;
 
 if ($categories_list) {
	$title = new title($chart_title);
	
	$arr_categories = explode(";",$categories_list);
	$arr_series_caption = explode(";", $series_caption_list);
	$arr_series = explode("|", $series_list);
	$dmin=0; $dmax=0;
	$ii=0;
	for($i=0; $i<count($arr_series); $i++){
		$notnum = false;
		$sub_series = explode(";",$arr_series[$i]);
		for($j=0; $j<count($sub_series); $j++){
//			echo "$i($ii)-$j-".$sub_series[$j].",num:".is_numeric($sub_series[$j])."<br>";
			if (is_numeric($sub_series[$j])) {
				if (intval($sub_series[$j]) == floatval($sub_series[$j])) {
					$data[$ii][$j] = intval($sub_series[$j]);
				} else {
					$data[$ii][$j] = floatval($sub_series[$j]);
				}
				if ($dmax < $data[$ii][$j]) {
					$dmax = $data[$ii][$j];
				} 
				if ($dmin > $data[$ii][$j]) {
					$dmin = $data[$ii][$j];
				}
			} else {
				$notnum = true;
			}
		} // end for $j
		if (!$notnum) $ii++;
	} //for($i=0; $i<count($arr_series); $i++){
	
	$arr_bg_color=array('#FFFFFF','#EBEAE9','#D5FFFF','#E6FFEA','#FEFDD6','#FFFDCC','#FFDDFC');
	$arr_tick_color=array('#000000','#8F0202','#925600','#929200','#009292','#000F92','#920083');
	
	$arr_anime=array('pop-up','explode','mid-slide','drop','fade-in','shrink-in');
	
	for($i=0; $i<count($arr_series_caption); $i++) {
	// color for bar
		$rand_color=random_color();
		$bar[$i] = new bar_cylinder();
		$bar[$i]->set_values( $data[$i] );
//		echo "data[$i]".$data[$i]."<br>";
		$bar[$i]->set_colour($rand_color);
		$bar[$i]->key($arr_series_caption[$i], 12);
		$bar[$i]->set_tooltip( "$arr_series_caption[$i] <br>จำนวน:#val#" );
		$rand_anime = rand(0,5);
		$rand_delay  = rand(10,30)/10;
		$rand_cascade = rand(10,50)/10;
		$bar[$i]->set_on_show(new bar_on_show($arr_anime[$rand_anime], $rand_cascade, $rand_delay));	
	}

	$chart = new open_flash_chart();
	$chart->set_title( $title );
	for($i=0; $i<count($arr_series_caption); $i++) {
		$chart->add_element( $bar[$i] );
	}

	$x_labels = new x_axis_labels();
//	$x_labels->rotate(0);
	$x_labels->set_vertical();
	$x_labels->set_labels($arr_categories);

	for($j=0; $j < count($arr_categories); $j++) {
		if ($j % 2 == 0) {
			$arr_categories[$j]="<br>$arr_categories[$j]";
		} elseif ($j % 3 == 0) {
			$arr_categories[$j]="<br><br>$arr_categories[$j]";
		}
	}

	$rand_tick_color = rand(0,6);

	$x_axis = new x_axis();
	$x_axis->set_3d( 5 );
	$x_axis->colour = $arr_tick_color[$rand_tick_color];
	$x_axis->tick_height(12);
	$x_axis->stroke(2);
	$x_axis->set_labels_from_array($arr_categories);
//	$x_axis->set_grid_colour('#550044');
//	$x_axis->set_labels($x_labels);
	$chart->set_x_axis( $x_axis );

	$y_axis = new y_axis();
	$ystep=ceil(($dmax - $dmin) / 10);
	$y_axis->set_range( $dmin, $dmax+$ystep, $ystep );
	$y_axis->colour = $arr_tick_color[$rand_tick_color];;
	$chart->set_y_axis( $y_axis );

	$rand_bg_color = rand(0,6);
	$chart->set_bg_colour( $arr_bg_color[$rand_bg_color] );

	$mydata = $chart->toPrettyString();
//	echo $mydata;
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

//swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart","550", "300", "9.0.0", "expressInstall.swf",{"get-data":"get_mydata"});
swfobject.embedSWF("../../admin/open-flash-chart.swf", "my_chart","700", "400", "9.0.0");

function ofc_ready() 
{
	//alert("ofc ready...");
}

function open_flash_chart_data()
{ 
	//alert("reading mydata...");
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