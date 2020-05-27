<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	//if($command == "UPDATE" && trim($POS_ID)){
	if($command == "UPDATE"){
			$cmd = " UPDATE PER_QUESTION_STOCK SET QS_SCORE1= '$QS_SCORE1', QS_SCORE2= '$QS_SCORE2', QS_SCORE3= '$QS_SCORE3', QS_SCORE4= '$QS_SCORE4', QS_SCORE5= '$QS_SCORE5', QS_SCORE6= '$QS_SCORE6' WHERE QS_ID='$QS_ID' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$QS_ID]");
?>
<script language="javascript">
	window.location.reload();
	setTimeout("alert('edit complete')",250);
	//window.close();
</script>
<?
	}//if($command == "UPDATE"){
?>