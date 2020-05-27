<?
	ini_set("max_execution_time", 30);
	
//	echo "1.menu_level=$MENU_ID_LV0, $MENU_ID_LV1, $MENU_ID_LV2, $MENU_ID_LV3 [$HIDE_HEADER]<br>";
	$HIDE_HEADER="";
	include("../current_location.html");

	if (count($arr_file) > 0) {
		echo "<BR>";
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "แฟ้ม Excel ที่สร้างมี<BR><br>";
		for($i_file = 0; $i_file < count($arr_file); $i_file++) {
			echo "---->".($i_file+1).":<a href=\"".$arr_file[$i_file]."\">".$arr_file[$i_file]."</a><br>";
		}
	}
	echo "<BR>";
	$time2 = time();
	$tdiff = $time2 - $time1;
	$s = $tdiff % 60;	// วินาที
	$m = floor($tdiff / 60);	// นาที
	$h = floor($m / 60);	// ชม.
	$m = $m % 60;	// นาที
	$show_lap = ($h?"$h ชม. ":"").($m?"$m นาที ":"")."$s วินาที";
	echo "<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "จำนวนข้อมูล $count_data รายการ<br>";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "เริ่ม:".date("d-m-Y h:i:s",$time1)." จบ:".date("d-m-Y h:i:s",$time2)." ใช้เวลา $show_lap [$tdiff]<br>";
	echo "<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "จบการทำงาน<br>";
?>