<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	//if($command == "UPDATE" && trim($POS_ID)){
	if($command == "UPDATE"){
	foreach($ARR_TARGET_LEVEL as $CP_CODE => $TARGET_LEVEL){
			if(trim($TARGET_LEVEL)){
				$cmd = " UPDATE PER_STANDARD_COMPETENCE set TARGET_LEVEL='$TARGET_LEVEL',UPDATE_USER='$SESS_USERID',UPDATE_DATE='$UPDATE_DATE' 							WHERE PL_CODE='$PL_CODE' and LEVEL_NO = '$LEVEL_NO' and ORG_ID = '$ORG_ID' and CP_CODE='$CP_CODE' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$PL_CODE : $LEVEL_NO : $CP_CODE]");
			} // end if
		} // end foreach
?>
<script language="javascript">
window.location.reload();
setTimeout("alert('บันทึกข้อมูลเรียบร้อยแล้ว')",250);
//window.close();
</script>
<?
	}
?>