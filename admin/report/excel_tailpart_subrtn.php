<?
	ini_set("max_execution_time", 30);
	
//	echo "1.menu_level=$MENU_ID_LV0, $MENU_ID_LV1, $MENU_ID_LV2, $MENU_ID_LV3 [$HIDE_HEADER]<br>";
	$HIDE_HEADER="";
	include("../current_location.html");

	if (count($arr_file) > 0) {
		echo "<BR>";
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "��� Excel ������ҧ��<BR><br>";
		for($i_file = 0; $i_file < count($arr_file); $i_file++) {
			echo "---->".($i_file+1).":<a href=\"".$arr_file[$i_file]."\">".$arr_file[$i_file]."</a><br>";
		}
	}
	echo "<BR>";
	$time2 = time();
	$tdiff = $time2 - $time1;
	$s = $tdiff % 60;	// �Թҷ�
	$m = floor($tdiff / 60);	// �ҷ�
	$h = floor($m / 60);	// ��.
	$m = $m % 60;	// �ҷ�
	$show_lap = ($h?"$h ��. ":"").($m?"$m �ҷ� ":"")."$s �Թҷ�";
	echo "<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "�ӹǹ������ $count_data ��¡��<br>";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "�����:".date("d-m-Y h:i:s",$time1)." ��:".date("d-m-Y h:i:s",$time2)." ������ $show_lap [$tdiff]<br>";
	echo "<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "����÷ӧҹ<br>";
?>