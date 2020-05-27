<? 
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

		
	
	
	
	if($command=="DELETE"){

				
				$cmd = " select WC_CODE,PER_ID
								 from PER_WORK_CYCLEHIS where WH_ID=$HIDWH_ID";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$del_WC_CODE = $data[WC_CODE];
				$del_PER_ID = $data[PER_ID];
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูลกำหนดรอบการมาปฏิบัติราชการ [$del_PER_ID :$HIDWH_ID : $del_WC_CODE ]");

				$cmd = " delete from PER_WORK_CYCLEHIS	 where WH_ID= $HIDWH_ID ";
				$db_dpis->send_cmd($cmd);

				echo "<script>window.location='../admin/es_search_scan_person_his.html?PerSonID=$PerSonID&MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";

 	} // end if*/
	
	
	
  
?>