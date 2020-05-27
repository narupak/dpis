<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if(!$DEPARTMENT_ID && $_GET[DEPARTMENT_ID])	$DEPARTMENT_ID = $_GET[DEPARTMENT_ID];
	
	$cmd = "  select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$LEVEL_NAME = $data[LEVEL_NAME];

	if($command == "UPDATE"){
		foreach($ARR_TARGET_LEVEL as $CP_CODE => $TARGET_LEVEL){
			if(trim($TARGET_LEVEL)){
				$cmd = " UPDATE PER_STANDARD_COMPETENCE set 
									TARGET_LEVEL=$TARGET_LEVEL,
									UPDATE_USER=$SESS_USERID,
									UPDATE_DATE='$UPDATE_DATE' 
									WHERE LEVEL_NO = '$LEVEL_NO' and CP_CODE='$CP_CODE' and DEPARTMENT_ID =  $DEPARTMENT_ID ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$LEVEL_NO : $CP_CODE]");
			} // end if
		} // end foreach
?>
<script language="javascript">
//window.location.reload();
//setTimeout("alert('บันทึกข้อมูลเรียบร้อยแล้ว')",250);
//window.close();
</script>
<?
	}
?>