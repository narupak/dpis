<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if($PER_ID){
		if($DPISDB=="odbc"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_CARDNO ,PER_PERSONAL.DEPARTMENT_ID
					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		} // end if
		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
	} // end if

	if (!get_magic_quotes_gpc()) {
		$EDU_FUND = addslashes(str_replace('"', "&quot;", trim($EDU_FUND)));
		$EDU_INSTITUTE = addslashes(str_replace('"', "&quot;", trim($EDU_INSTITUTE)));
	}else{
		$EDU_FUND = addslashes(str_replace('"', "&quot;", stripslashes(trim($EDU_FUND))));
		$EDU_INSTITUTE = addslashes(str_replace('"', "&quot;", stripslashes(trim($EDU_INSTITUTE))));
	}
//	$EDU_FUND = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EDU_FUND)));
//	$EDU_INSTITUTE = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EDU_INSTITUTE)));
	if (!$EDU_GRADE) $EDU_GRADE = "NULL";
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command=="REORDER"){
		foreach($ARR_EDU_ORDER as $EDU_ID => $EDU_SEQ){
			$cmd = " update PER_EDUCATE set EDU_SEQ='$EDU_SEQ' where EDU_ID=$EDU_ID ";
			$db_dpis->send_cmd($cmd);
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับประวัติการศึกษา [$PER_ID : $PER_NAME]");
	} // end if

	$ARR_EDU_SEQ = (array) null;
	$ARR_EDU_TYPE = (array) null;
	$edu_change_idx = (array) null;
	
	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_EDUCATE set AUDIT_FLAG = 'N' where EDU_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_EDUCATE set AUDIT_FLAG = 'Y' where EDU_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$cmd = " select max(EDU_ID) as max_id from PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$EDU_ID = $data[max_id] + 1;
		
		$cmd = " select EDU_SEQ, EDU_TYPE from PER_EDUCATE where PER_ID=$PER_ID order by EDU_SEQ ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
//			$data = array_change_key_case($data, CASE_LOWER);
			 if ($data[EDU_SEQ] != $EDU_SEQ) {
//				echo "data eduseq=".$data[EDU_SEQ]." != $EDU_SEQ ".$data[EDU_TYPE]."<br>";
				$ARR_EDU_SEQ[] = trim($data[EDU_SEQ]);
				$ARR_EDU_TYPE[] = trim($data[EDU_TYPE]);
			}
		}
		
//		if (trim($EDU_TYPE))		$EDU_TYPE_TXT = "||";
		if (trim($EDU_TYPE))		$EDU_TYPE_TXT = "";
		for ($i=0; $i<count($EDU_TYPE); $i++) {
			//$EDU_TYPE_TXT.=$EDU_TYPE[$i];			// 0 1 2 3 4
			$edu_change_idx[$EDU_TYPE[$i]] = -1; 
			if ($EDU_TYPE[$i] == 1 || $EDU_TYPE[$i] == 2 || $EDU_TYPE[$i] == 4) { // 1.วุฒิที่ใช้บรรจุ  2.วุฒิในตำแหน่งปัจจุบัน  4.วุฒิสูงสุด
				for($edu_i=0; $edu_i < count($ARR_EDU_TYPE); $edu_i++) {
					if (strpos($ARR_EDU_TYPE[$edu_i], $EDU_TYPE[$i]) !== false) {
						$edu_change_idx[$EDU_TYPE[$i]] = $edu_i;
//						break;
					}
				} // end loop for $edu_i
			} else {
				$edu_change_idx[$EDU_TYPE[$i]] = 0;
			}
			$EDU_TYPE_TXT.= "$EDU_TYPE[$i]";		//$EDU_TYPE_TXT.= "$EDU_TYPE[$i]||";
		} // end loop for $i
		$ST_CODE = (trim($ST_CODE))? "'" . $ST_CODE . "'"  : "NULL";
		$CT_CODE = (trim($CT_CODE))? "'" . $CT_CODE . "'"  : "NULL";
		$CT_CODE_EDU = (trim($CT_CODE_EDU))? "'" . $CT_CODE_EDU . "'"  : "NULL";
		$EL_CODE = (trim($EL_CODE))? "'" . $EL_CODE . "'"  : "NULL";
		$EN_CODE = (trim($EN_CODE))? "'" . $EN_CODE . "'"  : "NULL";
		$EM_CODE = (trim($EM_CODE))? "'" . $EM_CODE . "'"  : "NULL";
		$INS_CODE = (trim($INS_CODE))? "'" . $INS_CODE . "'"  : "NULL";	
//		$PER_CARDNO = (trim($PER_CARDNO))? "'$PER_CARDNO'" : "NULL";
		
		// เอา edu_type 1 2 4  ที่มีซ้ำใน record อื่น ออก...
		for ($i=0; $i<count($EDU_TYPE); $i++) {
			$out_edu_type = false;
			if ($EDU_TYPE[$i] == 1 || $EDU_TYPE[$i] == 2 || $EDU_TYPE[$i] == 4) { // 1.วุฒิที่ใช้บรรจุ  2.วุฒิในตำแหน่งปัจจุบัน  4.วุฒิสูงสุด
				if ($edu_change_idx[$EDU_TYPE[$i]] > -1) { // กรณีที่ มี edu_type วุฒิที่ใช้บรรจุ ใน seq อื่น
					//$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]] = str_replace("".$EDU_TYPE[$i]."||","",$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]]); // เอา edu_type=1 2 4 ออกจาก edu_type ที่ edu_seq=$edu_change_idx
					$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]] = str_replace($EDU_TYPE[$i],"",$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]]); 
					$out_edu_type = true;
				}
			}
			if ($out_edu_type) {
//				echo "($EDU_TYPE[$i]) af upd-".$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]]."<br>";
				$cmd = " update PER_EDUCATE set EDU_TYPE='".$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]]."',
																		 UPDATE_USER=$SESS_USERID, 
																		 UPDATE_DATE='$UPDATE_DATE'
								where PER_ID=$PER_ID and EDU_SEQ='".$ARR_EDU_SEQ[$edu_change_idx[$EDU_TYPE[$i]]]."'
					 		";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		} // end for loop $i
		
		$EDU_ENDDATE = save_date($EDU_ENDDATE); 
		$EDU_BOOK_DATE = save_date($EDU_BOOK_DATE); 

		$cmd = " insert into PER_EDUCATE	(EDU_ID, PER_ID, EDU_SEQ, EDU_STARTYEAR, EDU_ENDYEAR, ST_CODE, CT_CODE, 
				EDU_FUND, EN_CODE, EM_CODE, INS_CODE, EDU_TYPE, PER_CARDNO, UPDATE_USER, UPDATE_DATE,
				EL_CODE, EDU_ENDDATE, EDU_GRADE, EDU_HONOR, EDU_BOOK_NO, EDU_BOOK_DATE, EDU_REMARK, 
				EDU_INSTITUTE, CT_CODE_EDU)
				values ($EDU_ID, $PER_ID, '$EDU_SEQ', '$EDU_STARTYEAR', '$EDU_ENDYEAR', $ST_CODE, $CT_CODE, 
				'$EDU_FUND', $EN_CODE, $EM_CODE, $INS_CODE, '$EDU_TYPE_TXT', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE',
				$EL_CODE, '$EDU_ENDDATE', $EDU_GRADE, '$EDU_HONOR', '$EDU_BOOK_NO', '$EDU_BOOK_DATE', '$EDU_REMARK', 
				'".save_quote($EDU_INSTITUTE)."', $CT_CODE_EDU) ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการศึกษา [$PER_ID : $PER_NAME : $EN_CODE]");
		$ADD_NEXT = 1;
	} // end if
	
	
	if($command=="UPDATE" && $PER_ID && $EDU_ID){
		$cmd = " select EDU_SEQ, EDU_TYPE from PER_EDUCATE where PER_ID=$PER_ID order by EDU_SEQ ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
//			$data = array_change_key_case($data, CASE_LOWER);
			 if ($data[EDU_SEQ] != $EDU_SEQ) {
//				echo "data eduseq=".$data[EDU_SEQ]." != $EDU_SEQ ".$data[EDU_TYPE]."<br>";
				$ARR_EDU_SEQ[] = trim($data[EDU_SEQ]);
				$ARR_EDU_TYPE[] = trim($data[EDU_TYPE]);
			}
		}
		
		//if (trim($EDU_TYPE))		$EDU_TYPE_TXT = "||";
		if (trim($EDU_TYPE))		$EDU_TYPE_TXT = "";
		for ($i=0; $i<count($EDU_TYPE); $i++) {
			$edu_change_idx[$EDU_TYPE[$i]] = -1; 
			if ($EDU_TYPE[$i] == 1 || $EDU_TYPE[$i] == 2 || $EDU_TYPE[$i] == 4) { // 1.วุฒิที่ใช้บรรจุ  2.วุฒิในตำแหน่งปัจจุบัน  4.วุฒิสูงสุด
				for($edu_i=0; $edu_i < count($ARR_EDU_TYPE); $edu_i++) {
					if (strpos($ARR_EDU_TYPE[$edu_i], $EDU_TYPE[$i]) !== false) {
						$edu_change_idx[$EDU_TYPE[$i]] = $edu_i;
//						break;
					}
				} // end loop for $edu_i
			} else {
				$edu_change_idx[$EDU_TYPE[$i]] = 0;
			}
			$EDU_TYPE_TXT.= "$EDU_TYPE[$i]";		//$EDU_TYPE_TXT.= "$EDU_TYPE[$i]||";
		} // end loop for $i
//		echo "idx=$edu_change_idx<br>";
		$ST_CODE = (trim($ST_CODE))? "'" . $ST_CODE . "'"  : "NULL";
		$CT_CODE = (trim($CT_CODE))? "'" . $CT_CODE . "'"  : "NULL";
		$CT_CODE_EDU = (trim($CT_CODE_EDU))? "'" . $CT_CODE_EDU . "'"  : "NULL";
		$EL_CODE = (trim($EL_CODE))? "'" . $EL_CODE . "'"  : "NULL";
		$EN_CODE = (trim($EN_CODE))? "'" . $EN_CODE . "'"  : "NULL";
		$EM_CODE = (trim($EM_CODE))? "'" . $EM_CODE . "'"  : "NULL";
		$INS_CODE = (trim($INS_CODE))? "'" . $INS_CODE . "'"  : "NULL";		
//		$PER_CARDNO = (trim($PER_CARDNO))? "'$PER_CARDNO'" : "NULL";

		// เอา edu_type 1 2 4  ที่มีซ้ำใน record อื่น ออก...
		for ($i=0; $i<count($EDU_TYPE); $i++) {
			$out_edu_type = false;
			if ($EDU_TYPE[$i] == 1 || $EDU_TYPE[$i] == 2 || $EDU_TYPE[$i] == 4) { // 1.วุฒิที่ใช้บรรจุ  2.วุฒิในตำแหน่งปัจจุบัน 3.วุฒิอื่น ๆ   4.วุฒิสูงสุด
				if ($edu_change_idx[$EDU_TYPE[$i]] > -1) { // กรณีที่ มี edu_type วุฒิที่ใช้บรรจุ ใน seq อื่น
					if($EDU_TYPE[$i] == 4){//ถ้าวุฒิสูงสุดซ้ำ ให้อัพเดทวุฒิเก่าที่ซ้ำนั้น เป็นวุฒิอื่นๆ 29/11/2010
						//$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]] = str_replace("".$EDU_TYPE[$i]."||","3||",$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]]); // เอา edu_type=1 2 4 ออกจาก edu_type ท
						$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]] = str_replace($EDU_TYPE[$i],3,$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]]);
					}else{
						//$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]] = str_replace("".$EDU_TYPE[$i]."||","",$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]]); // เอา edu_type=1 2 4 ออกจาก edu_type ที่ edu_seq=$edu_change_idx
						$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]] = str_replace($EDU_TYPE[$i],"",$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]]); 
					} //end else
					$out_edu_type = true;
				}
			} //end for

			if ($out_edu_type) {
//				echo "($EDU_TYPE[$i]) af upd-".$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]]."<br>";
				$cmd = " update PER_EDUCATE set EDU_TYPE='".$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]]."',
																		 UPDATE_USER=$SESS_USERID, 
																		 UPDATE_DATE='$UPDATE_DATE'
								where PER_ID=$PER_ID and EDU_SEQ='".$ARR_EDU_SEQ[$edu_change_idx[$EDU_TYPE[$i]]]."'
					 		";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo $cmd;
			}
		} // end for loop $i
		
		$EDU_ENDDATE = save_date($EDU_ENDDATE); 
		$EDU_BOOK_DATE = save_date($EDU_BOOK_DATE); 
		
		$cmd = " update PER_EDUCATE set
						EDU_SEQ='$EDU_SEQ', 
						EDU_STARTYEAR='$EDU_STARTYEAR', 
						EDU_ENDYEAR='$EDU_ENDYEAR', 
						ST_CODE=$ST_CODE, 
						CT_CODE=$CT_CODE,
						EDU_FUND='$EDU_FUND', 
						EN_CODE=$EN_CODE, 
						EM_CODE=$EM_CODE, 
						INS_CODE=$INS_CODE, 
						EDU_TYPE='$EDU_TYPE_TXT',
						PER_CARDNO='$PER_CARDNO', 
						EL_CODE=$EL_CODE, 
						EDU_ENDDATE='$EDU_ENDDATE',
						EDU_GRADE=$EDU_GRADE, 
						EDU_HONOR='$EDU_HONOR', 
						EDU_BOOK_NO='$EDU_BOOK_NO', 
						EDU_BOOK_DATE='$EDU_BOOK_DATE', 
						EDU_REMARK='$EDU_REMARK', 
						EDU_INSTITUTE='".save_quote($EDU_INSTITUTE)."', 
						CT_CODE_EDU=$CT_CODE_EDU,
						UPDATE_USER=$SESS_USERID, 
						UPDATE_DATE='$UPDATE_DATE'
					where EDU_ID=$EDU_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการศึกษา [$PER_ID : $PER_NAME : $EN_CODE]");
	} // end if
	
	if($command=="DELETE" && $PER_ID && $EDU_ID){
		$cmd = " select EN_CODE from PER_EDUCATE where EDU_ID=$EDU_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EN_CODE = $data[EN_CODE];
		
		$cmd = " delete from PER_EDUCATE where EDU_ID=$EDU_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการศึกษา [$PER_ID : $PER_NAME : $EN_CODE]");
	} // end if

	if(($UPD && $PER_ID && $EDU_ID) || ($VIEW && $PER_ID && $EDU_ID)){
		$cmd = "	select	EDU_SEQ, EDU_STARTYEAR, EDU_ENDYEAR, EDU_FUND, EDU_TYPE, ST_CODE, CT_CODE, 
											EN_CODE, EM_CODE, INS_CODE, UPDATE_USER, UPDATE_DATE, EL_CODE, EDU_ENDDATE, 
											EDU_GRADE, EDU_HONOR, EDU_BOOK_NO, EDU_BOOK_DATE, EDU_REMARK, EDU_INSTITUTE, CT_CODE_EDU
							from		PER_EDUCATE
							where	EDU_ID=$EDU_ID ";	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EDU_SEQ = $data[EDU_SEQ];
		$EDU_STARTYEAR = $data[EDU_STARTYEAR];
		$EDU_ENDYEAR = $data[EDU_ENDYEAR];
		$EDU_FUND = $data[EDU_FUND];
		$EDU_ENDDATE = show_date_format(trim($data[EDU_ENDDATE]), 1);
		$EDU_GRADE = $data[EDU_GRADE];
		$EDU_HONOR = trim($data[EDU_HONOR]);
		$EDU_BOOK_NO = trim($data[EDU_BOOK_NO]);
		$EDU_BOOK_DATE = show_date_format(trim($data[EDU_BOOK_DATE]), 1);
		$EDU_REMARK = trim($data[EDU_REMARK]);
		$EDU_INSTITUTE = stripslashes(trim($data[EDU_INSTITUTE]));
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		$EDU_TYPE_tmp = (array) null;
		$temp_edutype = $data[EDU_TYPE];
		//$EDU_TYPE_tmp = explode("||", $temp_edutype);
		for ($i=1; $i<6; $i++) {		// index = EDU_TYPE(1 - 5)
			$indx=($i-1);
			if(strstr($temp_edutype,"$i"))		$EDU_TYPE_tmp[$indx]=$i;		//echo "($temp_edutype) $indx  - $i<br>";
		} //end for
		
		/*for ($i=0; $i<count($EDU_TYPE_tmp)-1; $i++) {
			if ($EDU_TYPE_tmp[$i] == 1)		$EDU_TYPE_tmp1 = 1 ;
			elseif ($EDU_TYPE_tmp[$i] == 2)		$EDU_TYPE_tmp2 = 2 ;
			elseif ($EDU_TYPE_tmp[$i] == 3)		$EDU_TYPE_tmp3 = 3 ;
			elseif ($EDU_TYPE_tmp[$i] == 4)		$EDU_TYPE_tmp4 = 4 ;
			elseif ($EDU_TYPE_tmp[$i] == 5)		$EDU_TYPE_tmp5 = 5 ;
		}*/
//		print_r($EDU_TYPE_tmp);

		$ST_NAME = $CT_NAME = $CT_NAME_EDU = $EL_NAME = $EN_NAME = $EM_NAME = $INS_NAME = $INS_COUNTRY = "";
		$ST_CODE = $data[ST_CODE];
		if($ST_CODE){
			$cmd = " select ST_NAME from PER_SCHOLARTYPE where ST_CODE='$ST_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ST_NAME = $data2[ST_NAME];
		} // end if

		$CT_CODE = $data[CT_CODE];
		if($CT_CODE){
			$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CT_NAME = $data2[CT_NAME];
		} // end if

		$CT_CODE_EDU = $data[CT_CODE_EDU];
		if($CT_CODE_EDU){
			$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE_EDU' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CT_NAME_EDU = $data2[CT_NAME];
		} // end if

		$EL_CODE = $data[EL_CODE];
		if($EL_CODE){
			$cmd = " select EL_NAME from PER_EDUCLEVEL where EL_CODE='$EL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$EL_NAME = $data2[EL_NAME];
		} // end if

		$EN_CODE = $data[EN_CODE];
		if($EN_CODE){
			$cmd = " select EN_NAME from PER_EDUCNAME where EN_CODE='$EN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$EN_NAME = $data2[EN_NAME];
		} // end if

		$EM_CODE = $data[EM_CODE];
		if($EM_CODE){
			$cmd = " select EM_NAME from PER_EDUCMAJOR where EM_CODE='$EM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$EM_NAME = $data2[EM_NAME];
		} // end if

		$INS_CODE = $data[INS_CODE];
		if($INS_CODE){
			if($DPISDB=="odbc"){
				$cmd = " select 	INS.INS_NAME, CT.CT_NAME
								 from 		PER_INSTITUTE as INS
												left join PER_COUNTRY as CT on (INS.CT_CODE=CT.CT_CODE)
								 where 	INS.INS_CODE='$INS_CODE' ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select 	PER_INSTITUTE.INS_NAME, PER_COUNTRY.CT_NAME
								 from 		PER_INSTITUTE, PER_COUNTRY
								 where 	PER_INSTITUTE.CT_CODE=PER_COUNTRY.CT_CODE(+) and PER_INSTITUTE.INS_CODE='$INS_CODE' ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select 	INS.INS_NAME, CT.CT_NAME
								 from 		PER_INSTITUTE as INS
												left join PER_COUNTRY as CT on (INS.CT_CODE=CT.CT_CODE)
								 where 	INS.INS_CODE='$INS_CODE' ";
			} // end if
			
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$INS_NAME = $data2[INS_NAME];
			$INS_COUNTRY = $data2[CT_NAME];
		} // end if
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($EDU_ID);
		unset($EDU_SEQ);
		unset($EDU_STARTYEAR);
		unset($EDU_ENDYEAR);
		unset($EDU_FUND);
		unset($EDU_TYPE);
		unset($ST_CODE);
		unset($ST_NAME);
		unset($CT_CODE);
		unset($CT_NAME);
		unset($CT_CODE_EDU);
		unset($CT_NAME_EDU);
		unset($EN_CODE);
		unset($EN_NAME);
		unset($EM_CODE);
		unset($EM_NAME);
		unset($INS_CODE);
		unset($INS_NAME);
		$INS_COUNTRY = "ไทย";
		unset($EL_CODE);
		unset($EL_NAME);
		unset($EDU_ENDDATE);
		unset($EDU_GRADE);
		unset($EDU_HONOR);
		unset($EDU_BOOK_NO);
		unset($EDU_BOOK_DATE);
		unset($EDU_REMARK);
		unset($EDU_INSTITUTE);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>