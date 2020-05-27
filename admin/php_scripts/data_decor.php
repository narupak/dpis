<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/load_per_control.php");	

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	function cal_budget_year ($now_date, $per_startdate) {
		global $tot_year, $tot_month, $tot_date;
		$tmp_now_date = explode("/", $now_date);
		$tmp_per_startdate = explode("-", $per_startdate);	
		
		$tot_year = $tmp_now_date[0] - $tmp_per_startdate[0];
		$tot_month = $tmp_now_date[1] -  $tmp_per_startdate[1];
		$tot_date = $tmp_now_date[2] -  $tmp_per_startdate[2];	
	}

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

	$tmp_date = explode("/", $DE_DATE);
	$tmp_DE_DATE = ($tmp_date[2]  - 543) ."-". $tmp_date[1] ."-". $tmp_date[0]." "."00:00:00";
	$PER_TYPE = (trim($PER_TYPE))? $PER_TYPE : 1;
	$DC_TYPE = (trim($DC_TYPE))? $DC_TYPE : 1;	
	if ($DC_TYPE == 1) 				$show_DC_TYPE = "ชั้นสายสะพาย";
	elseif ($DC_TYPE == 2) 			$show_DC_TYPE = "ชั้นต่ำกว่าสายสะพาย";	
	elseif ($DC_TYPE == 3) 			$show_DC_TYPE = "เหรียญตรา";	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if (trim($DE_YEAR) && trim($DE_DATE)) {
		// หา DE_ID, DE_CONF 
		$cmd = " select DE_ID, DE_CONF, DEPARTMENT_ID from PER_DECOR where DE_YEAR='$DE_YEAR' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DE_ID = trim($data[DE_ID]);
		$DE_CONF = trim($data[DE_CONF]);
/*
		$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);

		$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = $data[ORG_NAME];
		$MINISTRY_ID = $data[ORG_ID_REF];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];
*/		
		// เก็บ DC_CODE ลง array ตาม DC_TYPE
		$cmd = " select DC_TYPE, DC_CODE from PER_DECORATION order by DC_TYPE desc, DC_ORDER "; 
		$db_dpis->send_cmd($cmd);
		while ( $data = $db_dpis->get_array() ) {
			$tmp_type = $data[DC_TYPE];
			$tmp_code = $data[DC_CODE];
			${"arr_dc_code_bytype$tmp_type"}[] = $tmp_code; 
		}
	}
/*
echo "<pre>";
print_r(${"arr_dc_code_bytype1"});
print_r(${"arr_dc_code_bytype2"});
print_r(${"arr_dc_code_bytype3"});
echo "</pre>";
*/

	// เพิ่มเติมข้าราชการ/ลูกจ้าง ที่สมควรได้รับเครื่องราชฯ
	if( $command == "ADD" ){

		$cmd = " insert into PER_DECORDTL 
				(DE_ID, DC_CODE, PER_ID, UPDATE_USER, UPDATE_DATE, PER_CARDNO) 
				VALUES 
				($DE_ID, '$DC_CODE', $PER_ID, $SESS_USERID, '$UPDATE_DATE', '$PER_CARDNO') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลผู้สมควรได้รับเครื่องราชฯ [".trim($DE_ID)." : ".$DC_CODE." : ".$PER_ID."]");
		$command = "VIEW";			
	}

	// แก้ไขข้าราชการ/ลูกจ้าง ที่สมควรได้รับเครื่องราชฯ
	if( $command == "UPDATE" ) {
		$cmd = " 	update PER_DECORDTL set  
							DC_CODE='$DC_CODE', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
						where DE_ID=$DE_ID and PER_ID=$PER_ID   ";
		$db_dpis->send_cmd($cmd);
		/* $db_dpis->show_error();
		echo "<HR>";
		echo $DE_ID; */

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลผู้สมควรได้รับเครื่องราชฯ [".trim($DE_ID)." : ".$DC_CODE." : ".$PER_ID."]");
		$command = "VIEW";	
	}

	// ลบข้าราชการ/ลูกจ้าง ที่สมควรได้รับเครื่องราชฯ
	if($command == "DELETE" ){
		$cmd = " delete from PER_DECORDTL where DE_ID=$DE_ID and PER_ID=$PER_ID";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลผู้สมควรได้รับเครื่องราชฯ [".trim($DE_ID)." : ".$DC_CODE." : ".$PER_ID."]");
		$command = "VIEW";			
	}
	

	// ====================================================
	// ประมวลผลผู้ที่สมควรได้รับเครื่องราช
	if ( $command == "BEFORE_EXAMINE" ) {
		// ตรวจสอบว่ามีการตรวจสอบปีกับวันที่ได้รับเครื่องราชฯ ซ้ำหรือไม่
		$cmd = " select DE_YEAR, DE_CONF from PER_DECOR where DE_YEAR='$DE_YEAR' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		if (trim($data[DE_YEAR]) && trim($data[DE_CONF])==0) {
			$submit_examine = "
				document.getElementById('obj_uploading').style.display = 'none';

				if (confirm('พบว่ามีการตรวจสอบผู้สมควรได้รับเครื่องราชฯ ปี พ.ศ. $DE_YEAR แล้ว ต้องการตรวจสอบแทนที่หรือไม่?' )) { 
					form1.command.value='EXAMINE'; 
					
					document.getElementById('obj_uploading').style.display = 'block';
					document.getElementById('obj_uploading').style.top = document.body.scrollTop + ((document.body.clientHeight / 2) + 5);
					document.getElementById('obj_uploading').style.left = document.body.scrollLeft  + ((document.body.clientWidth / 2) - 80);
					document.getElementById('obj_uploading').style.visibility = 'visible';
					
					form1.submit();
				} else {
					form1.PER_TYPE[0].checked = true;
					form1.DC_TYPE[0].checked = true;
					form1.command.value = 'VIEW';
					form1.submit();
				} ";
		} elseif (trim($data[DE_YEAR]) && trim($data[DE_CONF])==1) {				
			$submit_examine = "alert('ไม่สามารถตรวจสอบผู้สมควรได้รับเครื่องราชฯ ปี พ.ศ. $DE_YEAR ได้ เนื่องจากได้ยืนยันข้อมูลแล้ว')";
			$command = "VIEW";
		} else {
			$command = "EXAMINE";
		}
	}	// end if 


	// ====================================================
	if ($command == "EXAMINE") {
		// ลบข้อมูลผู้ได้รับเครื่องราชฯ table PER_DECOR, PER_DECORDTL 
		if (trim($DE_ID))	{
			$cmd = " delete from PER_DECORDTL where DE_ID=$DE_ID ";
			$db_dpis->send_cmd($cmd);
			$cmd = " update PER_DECOR set DE_DATE='$tmp_DE_DATE', DE_CONF=0, 
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
						  where DE_ID=$DE_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} else {
			// insert ข้อมูลเข้า table PER_DECOR
			$cmd = " select max(DE_ID) as max_id from PER_DECOR ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);		
			$DE_ID = $data[max_id] + 1;
			$cmd = " insert into PER_DECOR (DE_ID, DE_YEAR, DE_DATE, DE_CONF, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE) 
						 values ($DE_ID, '$DE_YEAR', '$tmp_DE_DATE', 0, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();			
		}

		// 1=ข้าราชการ, 2=ลูกจ้างประจำ, 3=พนักงานราชการ
		for ($PER_TYPE=1; $PER_TYPE<=3; $PER_TYPE++) {
			// เคลียร์ค่า array ที่แปรผันตาม LEVEL_NO
			$cmd = " select distinct(LEVEL_NO) from PER_DECORCOND order by LEVEL_NO ";
			$db_dpis2->send_cmd($cmd);		
			while ( $data2 = $db_dpis2->get_array() ) {
				$tmp_LEVEL_NO = $data2[LEVEL_NO];				
				unset(${"arr_DC_TYPE$tmp_LEVEL_NO"}, 	${"arr_DC_CODE$tmp_LEVEL_NO"}, ${"arr_DC_CODE_OLD$tmp_LEVEL_NO"}, ${"arr_DCON_TIME1$tmp_LEVEL_NO"}, ${"arr_DCON_TIME2$tmp_LEVEL_NO"}, ${"arr_DCON_TIME3$tmp_LEVEL_NO"}); 
			}

			if ($PER_TYPE==1) {	
				$include_file = "data_decor_process.php";
				$tmp_LEVEL_NO = "";
			} elseif ($PER_TYPE==2) {
				$include_file = "data_decor_process.php";
				$tmp_LEVEL_NO = "03";
			} elseif ($PER_TYPE==2) {
				$include_file = "data_decor_process.php";
				$tmp_LEVEL_NO = "03";				
			}
				
			// select ข้อมูลเครื่องราชฯ แต่ละระดับเข้า array
			$cmd = " 	select 		b.LEVEL_NO, a.DC_CODE, a.DC_TYPE, DCON_TIME1, DCON_TIME2, DC_CODE_OLD, DCON_TIME3  
					from 		PER_DECORATION a, PER_DECORCOND b 
					where 		b.DCON_TYPE=$PER_TYPE and a.DC_CODE=b.DC_CODE 
					order by		DC_TYPE desc, DC_ORDER ";
			$amount_data = $db_dpis2->send_cmd($cmd);
			//echo "$cmd<br>";
			//	if ($amount_data > 0)  	echo "$cmd<br>";
			$k = 0;
			while ( $data2 = $db_dpis2->get_array() ) {
				$tmp_LEVEL_NO = trim($data2[LEVEL_NO]);
				$tmp_DC_TYPE = trim($data2[DC_TYPE]);
				$tmp_DC_CODE = trim($data2[DC_CODE]);	
	
				${"arr_DC_TYPE$tmp_LEVEL_NO"}[] = $tmp_DC_TYPE;
				${"arr_DC_CODE$tmp_LEVEL_NO"}[] = $tmp_DC_CODE;
				${"arr_DC_CODE_OLD$tmp_LEVEL_NO"}[$tmp_DC_TYPE][$tmp_DC_CODE] = trim($data2[DC_CODE_OLD]);
				${"arr_DCON_TIME1$tmp_LEVEL_NO"}[$tmp_DC_TYPE][$tmp_DC_CODE] = trim($data2[DCON_TIME1]) + 0;
				${"arr_DCON_TIME2$tmp_LEVEL_NO"}[$tmp_DC_TYPE][$tmp_DC_CODE] = trim($data2[DCON_TIME2]) + 0;
				${"arr_DCON_TIME3$tmp_LEVEL_NO"}[$tmp_DC_TYPE][$tmp_DC_CODE] = trim($data2[DCON_TIME3]) + 0;	
				$k++;
			} // end while 
	

			// select ข้าราชการ/ลูกจ้าง แต่ละคนขึ้นมาประมวลผลผู้สมควรได้รับเครื่องราชฯ
			$cmd = "	select 		PER_ID, LEVEL_NO, PER_STARTDATE, PER_CARDNO,    
								PER_NAME, PER_SURNAME 
					from 		PER_PERSONAL 
					where		PER_STATUS=1 and PER_TYPE=$PER_TYPE 
					order by   	LEVEL_NO ";
			$count_tmp = $db_dpis->send_cmd($cmd);
			//echo "$cmd<br>";		
		//	$db_dpis->show_error();		
			while ( $data = $db_dpis->get_array() ) {
				$tmp_PER_ID = $data[PER_ID];
				$tmp_PER_CARDNO = $data[PER_CARDNO];
				$tmp_LEVEL_NO = (trim($data[LEVEL_NO]))? trim($data[LEVEL_NO]) : $tmp_LEVEL_NO;
				$tmp_PER_STARTDATE = substr($data[PER_STARTDATE], 0, 10);
				$tmp_PER_NAME = $data[PER_NAME] ." ". $data[PER_SURNAME];
				
				include ("$include_file");		// ประมวลผลว่าสมควรได้รับเครื่องราชฯ หรือไม่
			}	//  	end while 	
		} 	//	end for($PER_TYPE)	
		$command = "VIEW";	
		$PER_TYPE = 1;			
	}		// 	if ($command == "EXAMINE") {


	// =================== START :: ยืนยันข้อมูลผู้สมควรได้รับเครื่องราชฯ =================
	if ($command == "CONFIRM_DATA") {
		$cmd = " update PER_DECOR set DE_CONF=1, UPDATE_DATE='$UPDATE_DATE' where DE_ID=$DE_ID ";
		$db_dpis->send_cmd($cmd);
		
		// ยืนยันข้อมูลแล้ว write ลง PER_DECORATEHIS
		// เลือก max ของ PER_DECORATEHIS
		$cmd = " select max(DEH_ID) as max_id from PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$DEH_ID = $data[max_id];
		
		$temp_date = explode("/", $DE_DATE);
		$TMP_DE_DATE = ($temp_date[2] - 543) ."-". $temp_date[1] . "-". $temp_date[0];
		// เลือกข้อมูลที่ต้องการยืนยันจาก ปี พ.ศ. และวันที่ได้รับเครื่องราชฯ
		$cmd = " select DC_CODE, PER_ID, PER_CARDNO from PER_DECORDTL where DE_ID=$DE_ID ";
		$db_dpis->send_cmd($cmd);
		while ($data = $db_dpis->get_array()) {
			$DEH_ID += 1;
			$cmd = " insert into PER_DECORATEHIS
							(DEH_ID, PER_ID, DC_CODE, DEH_DATE, UPDATE_USER, UPDATE_DATE, PER_CARDNO, DEH_GAZETTE)
							values 
							($DEH_ID, $data[PER_ID], '$data[DC_CODE]', '$TMP_DE_DATE', $SESS_USERID, '$UPDATE_DATE', '$data[PER_CARDNO]', '') ";
			$db_dpis1->send_cmd($cmd);							
			//echo "$cmd<br>";
			//$db_dpis1->show_error();
		}	// end while 
		$command = "VIEW";
	}
	// =================== END :: ยืนยันข้อมูลผู้สมควรได้รับเครื่องราชฯ =================	

	if($UPD || $VIEW){
		$cmd = " 	select 	b.PER_ID, PN_CODE, PER_NAME, PER_SURNAME, b.DC_CODE, DC_NAME 
						from 		PER_DECOR a, PER_DECORDTL b, PER_PERSONAL c, PER_DECORATION d 
						where 	a.DE_ID=$DE_ID and b.PER_ID=$PER_ID and b.DC_CODE='$DC_CODE' and 
									b.DC_CODE=d.DC_CODE and b.PER_ID=c.PER_ID "; 
									//echo $cmd;
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$db_dpis->show_error();
		$PN_CODE = $data[PN_CODE];
		$PER_NAME = "";				
		$cmd = "select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
		
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PER_NAME = $data2[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];		
	   // $db_dpis->show_error();

		$DC_CODE = $data[DC_CODE];
		$DC_NAME = $data[DC_NAME];
	} // end if


	if( $command != "BEFORE_EXAMINE" &&  $command != "EXAMINE" && $command != "VIEW" ){
		$DE_ID = "";
		$DE_YEAR = "";
		$DE_DATE = "";
		$PER_TYPE = 1;
		$DC_TYPE = 1;
	} // end if		
	
	if (!$UPD && !$DEL) {
		$PER_NAME = "";
		$PER_ID = "";
		$DC_NAME = "";	
		$DC_CODE = "";
	}	// end if 
?>