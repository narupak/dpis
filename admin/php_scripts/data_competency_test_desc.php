<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	$table = "PER_COMPETENCY_DTL";
	$UPDATE_DATE = date("Y-m-d H:i:s");
	//echo("command - ".$command);
	if($command == "UPDATE" ){
		foreach($ARR_QS_SEQ as $QS_ID => $QS_SEQ){
				if(trim($QS_SEQ)){
					$cmd = " UPDATE PER_COMPETENCY_DTL set SEQ_NO=$QS_SEQ, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
									WHERE CPT_CODE = '$CPT_CODE' and QS_ID =$QS_ID ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$CPT_CODE : $QS_ID]");
					} // end if
				} // end foreach
?>
<script language="javascript">
window.location.reload();
setTimeout("alert('edit complete')",250);
//window.close();
</script>
<?
			}
?>