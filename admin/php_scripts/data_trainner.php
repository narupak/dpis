<?	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "DELETE" && $TRAINNER_ID){
		// ====================================		
		$cmd = " delete from PER_TRAINNER  where TRAINNER_ID=$TRAINNER_ID ";	
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลวิทยากร [ $TRAINNER_ID ]");
	} // end if
?>