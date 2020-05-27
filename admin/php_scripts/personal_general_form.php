<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/function_bmp.php");
	include("php_scripts/load_per_control.php");
	
	include("../php_scripts/calendar_data.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$IMG_PATH = "../attachment/pic_personal/";	
	if($MAIN_VIEW==1) $VIEW = 1;
	
	switch($CTRL_TYPE){
		case 2 :
			$PROVINCE_CODE = $PROVINCE_CODE;
			$PROVINCE_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			break;
		case 4 :
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			$MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
			$MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$PROVINCE_CODE = $PROVINCE_CODE;
			$PROVINCE_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			break;
		case 4 :
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			$MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
			$MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			break;
		case 5 :
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			$MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
			$MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			$MAIN_ORG_ID = $ORG_ID;
			$MAIN_ORG_NAME = $ORG_NAME;
			break;
	} // end switch case

	$UPDATE_DATE = date("Y-m-d H:i:s");

	$search_condition="";
	$arr_search_condition[] ="PER_ID=$PER_ID";	
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	if($DPISDB=="odbc"){
		$cmd = " select		a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_GENDER, a.PN_CODE, a.PER_NAME, 
											a.PER_SURNAME, a.PER_ENG_NAME, a.PER_ENG_SURNAME, a.RE_CODE, a.OT_CODE, 
											a.PER_BIRTHDATE, a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_OCCUPYDATE, 
											a.PER_RETIREDATE, a.PER_TYPE, a.PER_CARDNO, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, 
											a.PER_DOCNO, a.PER_DOCDATE, a.PER_EFFECTIVEDATE, a.PER_POS_REASON, 
											a.PER_POS_YEAR, a.PER_POS_DOCTYPE, a.PER_POS_DOCNO, a.PER_POS_DOCDATE, a.PER_POS_ORG, 
											a.PER_POS_DESC, a.PER_POS_REMARK, a.PER_BOOK_NO, a.PER_BOOK_DATE, a.PER_POS_ORGMGT, 
											a.PV_CODE, a.PER_START_ORG, a.MOV_CODE, a.PER_STATUS, a.ORG_NAME_WORK,
											c.POS_NO_NAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2, c.LEVEL_NO as POS_LEVEL_NO,
											d.POEM_NO_NAME, d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO_NAME, e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2, 
											g.POT_NO, g.TP_CODE, g.ORG_ID as POT_ORG_ID, g.ORG_ID_1 as POT_ORG_ID_1, g.ORG_ID_2 as POT_ORG_ID_2,
											c.PM_CODE, a.PER_OFFNO, a.ES_CODE, a.PAY_ID, c.CL_NAME
				 		from		PER_PRENAME b inner join 
				 					(
					 					(
											(
												( 	
													(
													PER_PERSONAL a 
													left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
													) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
												) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
											) left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
										) left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
									) on (trim(a.PN_CODE)=trim(b.PN_CODE))
						$search_condition
				 		order by		a.PER_NAME, a.PER_SURNAME ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "select		a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_GENDER, a.PN_CODE, a.PER_NAME, 
											a.PER_SURNAME, a.PER_ENG_NAME, a.PER_ENG_SURNAME, a.RE_CODE, a.OT_CODE, 
											a.PER_BIRTHDATE, a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_OCCUPYDATE, 
											a.PER_RETIREDATE, a.PER_TYPE, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, 
											a.PER_DOCNO, a.PER_DOCDATE, a.PER_EFFECTIVEDATE, a.PER_POS_REASON, 
											a.PER_POS_YEAR, a.PER_POS_DOCTYPE, a.PER_POS_DOCNO, a.PER_POS_DOCDATE, a.PER_POS_ORG, 
											a.PER_POS_DESC, a.PER_POS_REMARK, a.PER_BOOK_NO, a.PER_BOOK_DATE, a.PER_POS_ORGMGT, 
											a.PV_CODE, a.PER_START_ORG, a.MOV_CODE, a.PER_STATUS, a.ORG_NAME_WORK, 
											c.POS_NO_NAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2, c.ORG_ID_3, c.ORG_ID_4, c.ORG_ID_5, c.LEVEL_NO as POS_LEVEL_NO,
											d.POEM_NO_NAME, d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2, d.ORG_ID_3 as EMP_ORG_ID_3, d.ORG_ID_4 as EMP_ORG_ID_4, d.ORG_ID_5 as EMP_ORG_ID_5,
											e.POEMS_NO_NAME, e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2, e.ORG_ID_3 as EMPSER_ORG_ID_3, e.ORG_ID_4 as EMPSER_ORG_ID_4, e.ORG_ID_5 as EMPSER_ORG_ID_5, 
											g.POT_NO, g.TP_CODE, g.ORG_ID as POT_ORG_ID, g.ORG_ID_1 as POT_ORG_ID_1, g.ORG_ID_2 as POT_ORG_ID_2, g.ORG_ID_3 as POT_ORG_ID_3, g.ORG_ID_4 as POT_ORG_ID_4, g.ORG_ID_5 as POT_ORG_ID_5,
											c.PM_CODE, a.PER_OFFNO, a.ES_CODE, a.PAY_ID, c.CL_NAME
						from			PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, PER_LEVEL f, PER_POS_TEMP g
						where		trim(a.PN_CODE)=trim(b.PN_CODE) and 
											a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+) and a.LEVEL_NO=f.LEVEL_NO(+) and a.POT_ID=g.POT_ID(+)
											$search_condition
						order by		a.PER_NAME, a.PER_SURNAME ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select		a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_GENDER, a.PN_CODE, a.PER_NAME, 
											a.PER_SURNAME, a.PER_ENG_NAME, a.PER_ENG_SURNAME, a.RE_CODE, a.OT_CODE,
											a.PER_BIRTHDATE, a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_OCCUPYDATE, 
											a.PER_RETIREDATE, a.PER_TYPE, a.PER_CARDNO, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, 
											a.PER_DOCNO, a.PER_DOCDATE, a.PER_EFFECTIVEDATE, a.PER_POS_REASON, 
											a.PER_POS_YEAR, a.PER_POS_DOCTYPE, a.PER_POS_DOCNO, a.PER_POS_DOCDATE, a.PER_POS_ORG, 
											a.PER_POS_DESC, a.PER_POS_REMARK, a.PER_BOOK_NO, a.PER_BOOK_DATE, a.PER_POS_ORGMGT, 
											a.PV_CODE, a.PER_START_ORG, a.MOV_CODE, a.PER_STATUS, a.ORG_NAME_WORK, 
											c.POS_NO_NAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2, c.LEVEL_NO as POS_LEVEL_NO,
											d.POEM_NO_NAME, d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO_NAME, e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2, 
											g.POT_NO, g.TP_CODE, g.ORG_ID as POT_ORG_ID, g.ORG_ID_1 as POT_ORG_ID_1, g.ORG_ID_2 as POT_ORG_ID_2,
											c.PM_CODE, a.PER_OFFNO, a.ES_CODE, a.PAY_ID, c.CL_NAME
					 	from		PER_PERSONAL a inner join PER_PRENAME b on (trim(a.PN_CODE)=trim(b.PN_CODE))
																  left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
																  left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
																  left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
																  left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
																  left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
						$search_condition
				 		order by  a.PER_NAME, a.PER_SURNAME ";
	}
	//echo "<pre>$cmd<br>";
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$data_count = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$PER_ID = $data[PER_ID];
			$PER_TYPE = $data[PER_TYPE];
			$PAY_ID = $data[PAY_ID];
			$PV_CODE_PER = trim($data[PV_CODE]);
			$PER_DOCNO = trim($data[PER_DOCNO]);
			$PER_POS_REASON = trim($data[PER_POS_REASON]);
			$PER_POS_YEAR = trim($data[PER_POS_YEAR]);
			$PER_POS_DOCTYPE = trim($data[PER_POS_DOCTYPE]);
			$PER_POS_DOCNO = trim($data[PER_POS_DOCNO]);
			$PER_POS_ORG = trim($data[PER_POS_ORG]);
			$PER_POS_DESC = trim($data[PER_POS_DESC]);
			$PER_POS_REMARK = trim($data[PER_POS_REMARK]);
			$PER_BOOK_NO = trim($data[PER_BOOK_NO]);
			$cmd="SELECT * FROM PER_POSITIONHIS WHERE PER_ID = $PER_ID AND  POH_LAST_POSITION = 'Y'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_REMARK = trim($data2[POH_REMARK]);
			$PER_POS_ORGMGT = $POH_REMARK;
			//$PER_POS_ORGMGT = trim($data[PER_POS_ORGMGT]);
			$PER_START_ORG = trim($data[PER_START_ORG]);
			$PER_STATUS = $data[PER_STATUS];
			$ORG_NAME_WORK = trim($data[ORG_NAME_WORK]);
			$CL_NAME = trim($data[CL_NAME]);
			$PER_TYPE_NAME = $PERSON_TYPE[$PER_TYPE];

			if($PER_TYPE==1 || $PER_TYPE == 5){
				$POS_ID = $data[POS_ID];
				$POS_NO_NAME = $data[POS_NO_NAME];
				$POS_NO = $data[POS_NO];
				$PL_CODE = trim($data[PL_CODE]);
				$PT_CODE = trim($data[PT_CODE]);
				$ORG_ID = $data[ORG_ID];
				$ORG_ID_1 = $data[ORG_ID_1];
				$ORG_ID_2 = $data[ORG_ID_2];
                $ORG_ID_3 = $data[ORG_ID_3];
                $ORG_ID_4 = $data[ORG_ID_4];
                $ORG_ID_5 = $data[ORG_ID_5];
                

				$cmd = " select PL_NAME, LG_CODE from PER_LINE where trim(PL_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PL_NAME]);
				$LG_CODE = trim($data2[LG_CODE]);

				$cmd = " select LG_NAME from PER_LINE_GROUP where trim(LG_CODE) = '$LG_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$LG_NAME = trim($data2[LG_NAME]);

				$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE) = '$PT_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PT_NAME = trim($data2[PT_NAME]);
			}elseif($PER_TYPE==2){
				$POS_ID = $data[POEM_ID];
				$POS_NO_NAME = $data[POEM_NO_NAME];
				$POS_NO = $data[EMP_POS_NO];
				$PL_CODE = trim($data[EMP_PL_CODE]);
				$PG_CODE = trim($data[PG_CODE]);
				$ORG_ID = $data[EMP_ORG_ID];
				$ORG_ID_1 = $data[EMP_ORG_ID_1];
				$ORG_ID_2 = $data[EMP_ORG_ID_2];
                $ORG_ID_3 = $data[EMP_ORG_ID_3];
                $ORG_ID_4 = $data[EMP_ORG_ID_4];
                $ORG_ID_5 = $data[EMP_ORG_ID_5];
                
				$cmd = " select PG_CODE, PN_NAME from PER_POS_NAME where trim(PN_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PG_CODE = trim($data2[PG_CODE]);
				$PL_NAME = trim($data2[PN_NAME]); 

				$cmd = " select PG_NAME from PER_POS_GROUP where trim(PG_CODE) = '$PG_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$LG_NAME = trim($data2[PG_NAME]); 
			}elseif($PER_TYPE==3){
				$POS_ID = $data[POEMS_ID];
				$POS_NO_NAME = $data[POEMS_NO_NAME];
				$POS_NO = $data[EMPSER_POS_NO];
				$PL_CODE = trim($data[EMPSER_PL_CODE]);
				$ORG_ID = $data[EMPSER_ORG_ID];
				$ORG_ID_1 = $data[EMPSER_ORG_ID_1];
				$ORG_ID_2 = $data[EMPSER_ORG_ID_2]; 
                $ORG_ID_3 = $data[EMPSER_ORG_ID_3];
                $ORG_ID_4 = $data[EMPSER_ORG_ID_4];
                $ORG_ID_5 = $data[EMPSER_ORG_ID_5];
                
				$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[EP_NAME]);  //ตำแหน่งตามสายงาน
			} elseif($PER_TYPE==4){
				$POS_ID = $data[POT_ID];
				$POS_NO = $data[POT_NO];
				$PL_CODE = trim($data[TP_CODE]);
				//$PG_CODE = trim($data[PG_CODE]);
				$ORG_ID = $data[POT_ORG_ID];
				$ORG_ID_1 = $data[POT_ORG_ID_1];
				$ORG_ID_2 = $data[POT_ORG_ID_2];
                $ORG_ID_3 = $data[POT_ORG_ID_3];
                $ORG_ID_4 = $data[POT_ORG_ID_4];
                $ORG_ID_5 = $data[POT_ORG_ID_5];
                
				$cmd = " select TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[TP_NAME]);  //ตำแหน่งตามสายงาน
			} 
			if ($SESS_DEPARTMENT_NAME!="กรมการปกครอง") $PAY_ID = $POS_ID;

			$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE_PER' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PV_NAME_PER = trim($data2[PV_NAME]);
				
			// สถานะการดำรงตำแหน่ง
			$ES_CODE = trim($data[ES_CODE]);
			$cmd = " select ES_NAME from PER_EMP_STATUS where trim(ES_CODE)='$ES_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ES_NAME = trim($data2[ES_NAME]);
//echo "ORG_ID =".$ORG_ID." |ORG_ID_1 =".$ORG_ID_1." |ORG_ID_2 =".$ORG_ID_2."| ORG_ID_3 =".$ORG_ID_3."| ORG_ID_4 =".$ORG_ID_4."| ORG_ID_5 =".$ORG_ID_5;
			$ORG_NAME = show_org_name($ORG_ID, $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, $ORG_ID_4, $ORG_ID_5);

			if($PER_TYPE==1 || $PER_TYPE == 5){
				$PM_CODE = $data[PM_CODE];	
				$cmd = " select PM_NAME from PER_MGT where PM_CODE = '$PM_CODE' ";
				$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PM_NAME = trim($data2[PM_NAME]);
			}
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$cmd = " select POSITION_TYPE, POSITION_LEVEL, LEVEL_NAME, LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$POSITION_TYPE = trim($data2[POSITION_TYPE]);
			$LEVEL_NAME = $data2[LEVEL_NAME];
			$LEVEL_SEQ_NO = $data2[LEVEL_SEQ_NO];
			
			$POS_LEVEL_NO = $data[POS_LEVEL_NO];	
			$cmd = " select POSITION_TYPE, POSITION_LEVEL, LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$POS_LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if ($PER_TYPE==1 || $PER_TYPE==5) {
				$POS_POSITION_TYPE = trim($data2[POSITION_TYPE]);
				$POS_LEVEL_NAME = $POS_POSITION_TYPE . " ระดับ" . $CL_NAME;
			} else{
				$POS_LEVEL_NAME = $LEVEL_NAME;
			}
			
		// กำหนด การแสดงรูปภาพ หาจาก DB
			$next_search_pic = 0;
                        /*เดิม*/
			//$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID  order by PER_PICSAVEDATE asc ";
                        /*---------------------*/
                        
                        /*Release 5.1.0.4 Begin*/
                       $cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID AND PIC_SHOW='1' AND (PIC_SIGN <>1 or PIC_SIGN is null) ";
                        /*Release 5.1.0.4 END*/
                        
			$piccnt = $db_dpis2->send_cmd($cmd);
			if ($piccnt > 0) { 
				while ($data2 = $db_dpis2->get_array()) {
					$PIC_SHOW = trim($data2[PIC_SHOW]);
					$TMP_PER_CARDNO = trim($data2[PER_CARDNO]);
					$PER_GENNAME = trim($data2[PER_GENNAME]);
					$PIC_PATH = trim($data2[PER_PICPATH]);
					$PIC_SEQ = trim($data2[PER_PICSEQ]);
					$T_PIC_SEQ = substr("000",0,3-strlen("$PIC_SEQ"))."$PIC_SEQ";
					$PIC_SERVER_ID = trim($data2[PIC_SERVER_ID]);
				
$next_search_pic = 1;					
if($next_search_pic==1){
					if ($PIC_SHOW == '1') {		// เฉพาะที่ กำหนด แสดงรูปภาพ เท่านั้น
						if($PIC_SERVER_ID > 0){
							if($PIC_SERVER_ID==99){		// $PIC_SERVER_ID 99 = ip จากตั้งค่าระบบ C06				 ใช้ \ 
								// หา # กรณี server อื่น เปลี่ยน # ให้เป็น \ เพื่อใช้ในการอัพโหลดรูป
								$PIC_PATH = $IMG_PATH_DISPLAY."#".$PIC_PATH;
								$PIC_PATH = str_replace("#","'",$PIC_PATH);
								$PIC_PATH = addslashes($PIC_PATH);
								$PIC_PATH = str_replace("'","",$PIC_PATH);
							
								$img_file = $PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
								$arr_img[] = $img_file;
								$arr_imgshow[] = 1;
								$PER_PICSAVEDATE = trim($data2[PER_PICSAVEDATE]);
							}else{  // other server
								$cmd = " select * from OTH_SERVER where SERVER_ID=$PIC_SERVER_ID ";
								if ($db_dpis2->send_cmd($cmd)) { 
									$data2 = $db_dpis2->get_array();
									$SERVER_NAME = trim($data2[SERVER_NAME]);
									$ftp_server = trim($data2[FTP_SERVER]);
									$ftp_username = trim($data2[FTP_USERNAME]);
									$ftp_password = trim($data2[FTP_PASSWORD]);
									$main_path = trim($data2[MAIN_PATH]);
									$http_server = trim($data2[HTTP_SERVER]);
										if ($http_server) {
											//echo "1.".$http_server."/".$img_file."<br>";
											$fp = @fopen($http_server."/".$img_file, "r");
											if ($fp !== false) $img_file = $http_server."/".$img_file;
											else $img_file=$IMG_PATH."shadow.jpg";
											fclose($fp);
										} else {
					//						echo "2.".$img_file."<br>";
											$img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
										}
									} // end db_dpis2
							}
						}else{ // localhost  $PIC_SERVER_ID == 0
							$img_file = $PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
                                                        $img_unfile = $IMG_PATH."shadow.jpg";
							$arr_img[] = "../".$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
							$arr_imgshow[] = 1;
							$PER_PICSAVEDATE = trim($data2[PER_PICSAVEDATE]);
						}
					} else { // PIC_SHOW==1
						/*
						$arr_img[] = "../".$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
						$arr_imgsavedate[] = trim($data2[PER_PICSAVEDATE]);
						$arr_imgshow[] = 0;
						*/
					}
} // end if($next_search_pic==1)
				} // end while loop
			} else { // $piccnt
				//$img_file="";
				$img_file=$IMG_PATH."shadow.jpg";
			}
		
			//echo "($PIC_PATH) -> img_file=$img_file // $PIC_SERVER_ID<br>";

			if ($PER_POS_ORGMGT) $LEVEL_NAME; //$LEVEL_NAME .= " (".$PER_POS_ORGMGT.")"; 
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$FULLNAME = ($PN_NAME)."$PER_NAME $PER_SURNAME";
			$PER_CARDNO = $data[PER_CARDNO];	
			$PER_OFFNO = $data[PER_OFFNO];	

			$today = date("Y-m-d");
			$CHECK_RETIREDATE = "2013-10-01";
			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			/*เดิม=======================================================*/
                        $PER_RETIREDATE_60 = date_adjust($PER_BIRTHDATE,'y',60);
                        //echo $PER_BIRTHDATE.'=>'.$PER_RETIREDATE_60;
			$AGE_DIFF = date_difference($today, $PER_BIRTHDATE, "full");
			if($PER_BIRTHDATE){
                                $PER_BIRTHDATE_v2=$PER_BIRTHDATE; /*Release 5.2.1.8 */
				$PER_BIRTHDATE = show_date_format(substr($PER_BIRTHDATE, 0, 10),$DATE_DISPLAY);
                                $PER_RETIREDATE_60 = date_adjust($PER_RETIREDATE_60,'d',-1);
                                $PER_RETIREDATE_60 = show_date_format(substr($PER_RETIREDATE_60, 0, 10),$DATE_DISPLAY);
			}else{
                        $PER_RETIREDATE_60="-";
                        } // end if
                        /*=======================================================*/
                        
                        /*Release 5.2.1.8 . Begin http://dpis.ocsc.go.th/Service/node/1439*/
                        $PER_RETIREDATE_60_v2 ='-';
                        if($PER_BIRTHDATE_v2){
                            $cmd = " SELECT to_char( (to_date( '".$PER_BIRTHDATE_v2."','YYYY-MM-DD') + interval '60'  YEAR)- '1','YYYY-MM-DD') AS IDATE60 FROM dual";
                            $db_dpis2->send_cmd($cmd);
                            $data2 = $db_dpis2->get_array();
                            $PER_RETIREDATE_60_v2 =show_date_format(trim($data2[IDATE60]),$DATE_DISPLAY) ;
                        }
                        $PER_RETIREDATE_60=$PER_RETIREDATE_60_v2;
                        /*Release 5.2.1.8 . End*/
			
		if ($BKK_FLAG==1) {
				if ($PER_BIRTHDATE<$CHECK_RETIREDATE){
					$PER_RETIREDATE = $PER_RETIREDATE;
				}else{
					$PER_RETIREDATE = date_adjust($PER_RETIREDATE,'d',-1);
				}
			}else{
				$PER_RETIREDATE = trim($data[PER_RETIREDATE]);
                               
			} // end if
			if($PER_RETIREDATE){
				$PER_RETIREDATE = show_date_format(substr($PER_RETIREDATE, 0, 10),$DATE_DISPLAY);
			} // end if
			
			$PER_DOCDATE = trim($data[PER_DOCDATE]);
			if($PER_DOCDATE){
				$PER_DOCDATE = show_date_format(substr($PER_DOCDATE, 0, 10),$DATE_DISPLAY);
			} // end if

			$PER_EFFECTIVEDATE = trim($data[PER_EFFECTIVEDATE]);
			if($PER_EFFECTIVEDATE){
				$PER_EFFECTIVEDATE = show_date_format(substr($PER_EFFECTIVEDATE, 0, 10),$DATE_DISPLAY);
			} // end if

			$PER_POS_DOCDATE = trim($data[PER_POS_DOCDATE]);
			if($PER_POS_DOCDATE){
				$PER_POS_DOCDATE = show_date_format(substr($PER_POS_DOCDATE, 0, 10),$DATE_DISPLAY);
			} // end if

			$PER_BOOK_DATE = trim($data[PER_BOOK_DATE]);
			if($PER_BOOK_DATE){
				$PER_BOOK_DATE = show_date_format(substr($PER_BOOK_DATE, 0, 10),$DATE_DISPLAY);
			} // end if

			$PER_SALARY = number_format($data[PER_SALARY],2);
			$PER_SPSALARY = $PER_MGTSALARY = $PER_TOTALSALARY = $EXH_SEQ = $EX_SEQ = "";
			$cmd = " select EX_NAME, EXH_AMT	from PER_EXTRAHIS a, PER_EXTRATYPE b
							where trim(a.EX_CODE)=trim(b.EX_CODE) and PER_ID=$PER_ID and EXH_ACTIVE = 1 and 
							(EXH_ENDDATE is NULL or EXH_ENDDATE >= '$UPDATE_DATE')
							order by EX_SEQ_NO, b.EX_CODE ";
			$db_dpis2->send_cmd($cmd);
			while($data2 = $db_dpis2->get_array()){
				$EXH_SEQ++;
				$EX_NAME = trim($data2[EX_NAME]);
				$EXH_AMT = $data2[EXH_AMT];
				$PER_TOTALSALARY += $EXH_AMT;
				$PER_SPSALARY .= $EXH_SEQ.". ประเภท : ".$EX_NAME."  จำนวนเงิน : ".number_format($data2[EXH_AMT],2)."  บาท<br>";
			}

			if ($PER_TYPE=="1" || $PER_TYPE==5) {
				$cmd = " select EX_NAME, PMH_AMT from PER_POS_MGTSALARYHIS a, PER_EXTRATYPE b 
								  where trim(a.EX_CODE)=trim(b.EX_CODE) and PER_ID=$PER_ID and PMH_ACTIVE = 1 and 
								(PMH_ENDDATE is NULL or PMH_ENDDATE >= '$UPDATE_DATE')
								  order by EX_SEQ_NO, b.EX_CODE ";
				$db_dpis2->send_cmd($cmd);
				while($data2 = $db_dpis2->get_array()){
					$EX_SEQ++;
					$EX_NAME = trim($data2[EX_NAME]);
					$PMH_AMT = $data2[PMH_AMT];
					$PER_TOTALSALARY += $PMH_AMT;
					$PER_MGTSALARY .= $EX_SEQ.". ประเภท : ".$EX_NAME."  จำนวนเงิน : ".number_format($data2[PMH_AMT],2)."  บาท<br>";
				}
				$PER_TOTALSALARY = number_format($PER_TOTALSALARY,2);
			}

			$cmd = " select TIME_DAY, TIMEH_MINUS from PER_TIMEHIS a, PER_TIME b 
							  where trim(a.TIME_CODE)=trim(b.TIME_CODE) and PER_ID=$PER_ID ";
			$db_dpis2->send_cmd($cmd);
			while($data2 = $db_dpis2->get_array()){
				$TIME_DAY += $data2[TIME_DAY] - $data2[TIMEH_MINUS];
			}
			if ($TIME_DAY) $TIME_DAY .= " วัน";
                        
                        
                        /*Release 5.1.0.6 Begin*/
                        /*$cmd = " SELECT sum(oo_day) AS OO_DAY FROM PER_OTHER_OCCUPATION WHERE per_id=$PER_ID";
			$db_dpis2->send_cmd($cmd);
			while($data2 = $db_dpis2->get_array()){
				$OO_DAY = $data2[OO_DAY];
			}
			if (!empty($OO_DAY)){
                            $today= date('Y-m-d',strtotime("-".$OO_DAY." day"))."<br>";/*แนวทาง
                            $OO_DAY = $OO_DAY." วัน";
                        };*/
                        /*Release 5.1.0.6 End*/
                        

			//วันบรรจุ
			$PER_STARTDATE = trim($data[PER_STARTDATE]);
			$today1 = date_adjust($today, "d", $TIME_DAY );
                        
                        
			$DATE_DIFF = date_difference($today, $PER_STARTDATE, "full");
                        
                        
                        //echo date_difference('2016-01-11', $PER_STARTDATE, "full")."<br>";
                        
			$TOTAL_TIME = date_difference($today1, $PER_STARTDATE, "full");
			if($PER_STARTDATE){
				$PER_STARTDATE = show_date_format(substr($PER_STARTDATE, 0, 10),$DATE_DISPLAY);
			} // end if

			$PER_OCCUPYDATE = trim($data[PER_OCCUPYDATE]);
			if($PER_OCCUPYDATE){
				$PER_OCCUPYDATE = show_date_format(substr($PER_OCCUPYDATE, 0, 10),$DATE_DISPLAY);
			} // end if
	
			   /*เดิม*/
                        /*if ($MFA_FLAG==1 && $PM_CODE)
			    $cmd = " select POH_EFFECTIVEDATE
                                         from PER_POSITIONHIS a, PER_MOVMENT b
                                         where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE 
                                               and LEVEL_NO='$LEVEL_NO' and PM_CODE='$PM_CODE' 
                                               and (MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
                                         order by POH_EFFECTIVEDATE ";
                           
			else
				$cmd = " select POH_EFFECTIVEDATE
                                         from PER_POSITIONHIS a, PER_MOVMENT b
                                         where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE 
                                             and LEVEL_NO='$LEVEL_NO' 
                                             and (MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
                                        order by POH_EFFECTIVEDATE ";*/
                        /*Release 5.1.0.6 Begin*/
                        $cmd = " select POH_EFFECTIVEDATE
                                         from PER_POSITIONHIS a, PER_MOVMENT b
                                         where PER_ID=$PER_ID 
                                             and a.MOV_CODE=b.MOV_CODE 
                                             and POH_LEVEL_NO='$LEVEL_NO' 
                                        order by POH_EFFECTIVEDATE ";
                        /*Release 5.1.0.6 end*/
                        
			//echo $cmd;
                        $db_dpis2->send_cmd($cmd);
                        
			$data2 = $db_dpis2->get_array();
			$POH_EFFECTIVEDATE = trim($data2[POH_EFFECTIVEDATE]);
			if ($POH_EFFECTIVEDATE) {	
				$POS_UP_DATE = show_date_format(substr($POH_EFFECTIVEDATE, 0, 10),$DATE_DISPLAY);
			}

			$cmd = " select POH_EFFECTIVEDATE, a.LEVEL_NO
							from   PER_POSITIONHIS a, PER_MOVMENT b, PER_LEVEL c
							where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and a.LEVEL_NO=c.LEVEL_NO and c.LEVEL_SEQ_NO < $LEVEL_SEQ_NO and 
										(MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
							order by LEVEL_SEQ_NO desc, POH_EFFECTIVEDATE ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_EFFECTIVEDATE = trim($data2[POH_EFFECTIVEDATE]);
			$LEVEL_NO_C = level_no_format($data2[LEVEL_NO]);
/*			if ($LEVEL_NO_C == "D1") $LEVEL_NO_C = "อำนวยการต้น";
			elseif ($LEVEL_NO_C == "D2") $LEVEL_NO_C = "อำนวยการสูง";
			elseif ($LEVEL_NO_C == "M1") $LEVEL_NO_C = "บริหารต้น";
			elseif ($LEVEL_NO_C == "M2") $LEVEL_NO_C = "บริหารสูง";
			elseif ($LEVEL_NO_C > "11") {
				$cmd = " select POSITION_LEVEL from PER_LEVEL where LEVEL_NO = '$LEVEL_NO_C' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$LEVEL_NO_C = trim($data2[POSITION_LEVEL]);
			} */
			if ($POH_EFFECTIVEDATE) {	
				$POS_C_DATE = show_date_format(substr($POH_EFFECTIVEDATE, 0, 10),$DATE_DISPLAY);
			}

//			if ($ES_CODE=="05" || $ES_CODE=="06") {
				$cmd = " select POH_EFFECTIVEDATE, POH_ENDDATE, POH_ORG
								from   PER_POSITIONHIS
								where PER_ID=$PER_ID and POH_LAST_POSITION='Y' and (ES_CODE='05' or ES_CODE='06') ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POH_EFFECTIVEDATE = trim($data2[POH_EFFECTIVEDATE]);
				$POH_ENDDATE = trim($data2[POH_ENDDATE]);
				$POH_ORG = trim($data2[POH_ORG]);
				if ($POH_EFFECTIVEDATE) {	
					$HELP_START_DATE = show_date_format(substr($POH_EFFECTIVEDATE, 0, 10),$DATE_DISPLAY);
				}
				if ($POH_ENDDATE) {	
					$HELP_END_DATE = show_date_format(substr($POH_ENDDATE, 0, 10),$DATE_DISPLAY);
				}
//			}

			if ($PER_STATUS==2) {
				$cmd = " select MOV_CODE,POH_DOCNO,POH_DOCDATE, POH_EFFECTIVEDATE from PER_POSITIONHIS
								where PER_ID=$PER_ID
								order by POH_EFFECTIVEDATE desc, POH_SEQ_NO DESC ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$MOV_CODE = trim($data2[MOV_CODE]);
                                $POH_DOCNO = trim($data2[POH_DOCNO]);
                                $POH_DOCDATE = show_date_format(substr(trim($data2[POH_DOCDATE]), 0, 10),$DATE_DISPLAY);
                                $PERPOH_EFFECTIVEDATE   = show_date_format(substr(trim($data2[POH_EFFECTIVEDATE]), 0, 10),$DATE_DISPLAY);
				$cmd = " select MOV_NAME from PER_MOVMENT where trim(MOV_CODE)='$MOV_CODE' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$MOV_NAME = trim($data1[MOV_NAME]);
                                
			}

			$cmd = " select POH_EFFECTIVEDATE, POH_POS_NO_NAME, POH_POS_NO, POH_ORG2
							from   PER_POSITIONHIS
							where PER_ID=$PER_ID
							order by POH_EFFECTIVEDATE desc, POH_SEQ_NO DESC,POH_ID desc ";
                         /*Release 5.1.0.4 Begin*/
                        /* เพิ่ม ,POH_ID desc เพื่อหยิบรายการล่าสุด*/
                        /**/
                        //echo $cmd;
			$db_dpis2->send_cmd($cmd);
			while($data2 = $db_dpis2->get_array()){
				$POH_POS_NO_NAME = trim($data2[POH_POS_NO_NAME]);
				$POH_POS_NO = trim($data2[POH_POS_NO]);
				$POH_ORG2 = trim($data2[POH_ORG2]);
				if ($MFA_FLAG==1) {
					if ($POH_POS_NO_NAME==$POS_NO_NAME && $POH_POS_NO==$POS_NO) 
						$POH_EFFECTIVEDATE = trim($data2[POH_EFFECTIVEDATE]);
					else break;
				} else {
					if ($POH_POS_NO_NAME==$POS_NO_NAME && $POH_POS_NO==$POS_NO && $POH_ORG2==$DEPARTMENT_NAME) 
						$POH_EFFECTIVEDATE = trim($data2[POH_EFFECTIVEDATE]);
					else break;
				}
			}
                        
			if ($POH_EFFECTIVEDATE) {	
				$POH_DATE = show_date_format(substr($POH_EFFECTIVEDATE, 0, 10),$DATE_DISPLAY);
			}
			if ($PER_TYPE==1 || $PER_TYPE==5) {
				if ($PAY_ID) {			
					$cmd = " select 	POS_NO 
							from 	PER_POSITION where POS_ID=$PAY_ID  ";
					$db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$PAY_NO = trim($data_dpis2[POS_NO]);
				}
	
				if($DPISDB=="odbc"){
					$cmd = "	select	b.PER_ID
										from		PER_POSITION a
														left join PER_PERSONAL b on a.POS_ID=b.PAY_ID
										where	a.PAY_NO=$PAY_NO and b.PER_NAME='$PER_NAME' and b.PER_SURNAME='$PER_SURNAME' and PER_TYPE = 1 and PER_STATUS = 1 ";
				}elseif($DPISDB=="oci8"){
					$cmd = "	select	b.PER_ID
										from		PER_POSITION a, PER_PERSONAL b
										where	a.POS_ID=b.PAY_ID(+) and a.PAY_NO=$PAY_NO and b.PER_NAME='$PER_NAME' and b.PER_SURNAME='$PER_SURNAME' and PER_TYPE = 1 and PER_STATUS = 1 ";
				}elseif($DPISDB=="mysql"){
					$cmd = "	select	b.PER_ID
										from		PER_POSITION a
														left join PER_PERSONAL b on a.POS_ID=b.PAY_ID
										where	a.PAY_NO=$PAY_NO and b.PER_NAME='$PER_NAME' and b.PER_SURNAME='$PER_SURNAME' and PER_TYPE = 1 and PER_STATUS = 1 ";
				} // end if
				$db_dpis2->send_cmd($cmd);
//				echo "$cmd<br>";
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PER_ID_PAY = trim($data2[PER_ID]);

				if ($PER_ID_PAY) {
					$cmd = " select SAH_PAY_NO, SAH_EFFECTIVEDATE
									from   PER_SALARYHIS
									where PER_ID=$PER_ID_PAY
									order by SAH_EFFECTIVEDATE DESC, SAH_SEQ_NO DESC ";
					$db_dpis2->send_cmd($cmd);
					while($data2 = $db_dpis2->get_array()){
						$SAH_PAY_NO = trim($data2[SAH_PAY_NO]);
						$SAH_EFFECTIVEDATE = trim($data2[SAH_EFFECTIVEDATE]);
						if ($SAH_PAY_NO==$PAY_NO) {
							$PAY_DATE = show_date_format(substr($SAH_EFFECTIVEDATE, 0, 10),$DATE_DISPLAY);
						} else break;
					}
				}
	
				$cmd = " select PM_CODE, PL_CODE, LEVEL_NO, ORG_ID, ORG_ID_1, ORG_ID_2, CL_NAME
								from   PER_POSITION
								where POS_NO='$PAY_NO' ";
				$db_dpis2->send_cmd($cmd);
	
				$data2 = $db_dpis2->get_array();
				$PM_CODE = trim($data2[PM_CODE]);
				$PL_CODE = trim($data2[PL_CODE]);
				$PAY_LEVEL_NO = trim($data2[LEVEL_NO]);
				$ORG_ID = $data2[ORG_ID];
				$ORG_ID_1 = $data2[ORG_ID_1];
				$ORG_ID_2 = $data2[ORG_ID_2];
				$PAY_CL_NAME = trim($data2[CL_NAME]);

				$cmd = " select OT_CODE, ORG_NAME from PER_ORG where ORG_ID = $ORG_ID ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PAY_OT_CODE = trim($data2[OT_CODE]);
				$PAY_ORG_NAME = trim($data2[ORG_NAME]);
			
				$PAY_ORG_NAME_1 = "";
				if ($ORG_ID_1) {
					$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_1 ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					$PAY_ORG_NAME_1 = trim($data2[ORG_NAME]);
				}

				$PAY_ORG_NAME_2 = "";
				if ($ORG_ID_2) {
					$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_2 ";
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					$PAY_ORG_NAME_2 = trim($data2[ORG_NAME]);
				}

				$PAY_ORG_NAME = trim($PAY_ORG_NAME_2." ".$PAY_ORG_NAME_1." ".$PAY_ORG_NAME);
				if ($PAY_OT_CODE == "01" && $DEPARTMENT_NAME=="กรมการปกครอง") $PAY_ORG_NAME = trim($PAY_ORG_NAME." ".$DEPARTMENT_NAME);

				$cmd = " select PM_NAME from PER_MGT where PM_CODE = '$PM_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PAY_PM_NAME = trim($data2[PM_NAME]);

				$cmd = " select PL_NAME from PER_LINE where PL_CODE = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PAY_PL_NAME = trim($data2[PL_NAME]);

				$cmd = " select POSITION_TYPE, POSITION_LEVEL, LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$PAY_LEVEL_NO' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PAY_POSITION_TYPE = trim($data2[POSITION_TYPE]);
				$PAY_LEVEL_NAME = trim($data2[LEVEL_NAME]);
				$PAY_LEVEL_NAME = $PAY_POSITION_TYPE . " ระดับ" . $PAY_CL_NAME;
			} // endif ($PER_TYPE=="1") 

			//หาชื่ออังกฤษ
			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select PN_ENG_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$PN_ENG_NAME = trim($data_dpis1[PN_ENG_NAME]);
			$PER_ENG_NAME = trim($data[PER_ENG_NAME]);
			$PER_ENG_SURNAME = trim($data[PER_ENG_SURNAME]);
			$FULLENGNAME = ($PN_ENG_NAME)."$PER_ENG_NAME $PER_ENG_SURNAME";
			//เพศ
			$PER_GENDER = trim($data[PER_GENDER]);
			if ($PER_GENDER==1) $PER_GENDER = "ชาย";
			elseif ($PER_GENDER==2) $PER_GENDER = "หญิง";

			//ศาสนา
			$RE_CODE = trim($data[RE_CODE]);
			if($RE_CODE){
				$cmd = " select RE_NAME from PER_RELIGION where RE_ACTIVE=1 and RE_CODE='$RE_CODE' ";
				$db_dpis1->send_cmd($cmd);
				$data_dpis1 = $db_dpis1->get_array();
				$PER_RE_NAME = trim($data_dpis1[RE_NAME]);
			}
			//ประเภทข้าราชการ
			$OT_CODE = trim($data[OT_CODE]);
			$cmd = " select OT_NAME from PER_OFF_TYPE where trim(OT_CODE)='$OT_CODE' ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$OT_NAME = trim($data_dpis1[OT_NAME]);

			$PER_RACE = "ไทย";
			$EDUCATE1 = $EDUCATE2 = $EDUCATE3 = "";
			if($DPISDB=="odbc") {
				$cmd = "select b.EN_NAME, d.INS_NAME, c.EL_NAME, e.EM_NAME, EDU_INSTITUTE
									from (
												(
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.en_code=b.en_code)	
													)left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												)left join PER_INSTITUTE d on (a.ins_code=d.ins_code)
											)left join PER_EDUCMAJOR e on (a.em_code=e.em_code)
									where  a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%'	"	;
				$db_dpis2->send_cmd($cmd);
		//	$db_dpis2->show_error();
		//		echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME2 = trim($data4[EN_NAME]);
				$INS_NAME2 = trim($data4[INS_NAME]);
				$EL_NAME2 = trim($data4[EL_NAME]);
				$EM_NAME2 = trim($data4[EM_NAME]);
				if (!$INS_NAME2) $INS_NAME2 = trim($data4[EDU_INSTITUTE]);
				$EDUCATE2 = $EN_NAME2 . " " . $EM_NAME2 . " " . $INS_NAME2;
				
				$cmd = "select b.EN_NAME, d.INS_NAME, c.EL_NAME, e.EM_NAME, EDU_INSTITUTE
									from (
												(
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.en_code=b.en_code)	
													)left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												)left join PER_INSTITUTE d on (a.ins_code=d.ins_code)
											)left join PER_EDUCMAJOR e on (a.em_code=e.em_code)
									where  a.PER_ID=$PER_ID and a.EDU_TYPE like '%1%'	";
										
				$db_dpis2->send_cmd($cmd);
			//	$db_dpis2->show_error();
			//	echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME1 = trim($data4[EN_NAME]);
				$INS_NAME1 = trim($data4[INS_NAME]);
				$EL_NAME1 = trim($data4[EL_NAME]);		
				$EM_NAME1 = trim($data4[EM_NAME]);
				if (!$INS_NAME1) $INS_NAME1 = trim($data4[EDU_INSTITUTE]);
				$EDUCATE1 = $EN_NAME1 . " " . $EM_NAME1 . " " . $INS_NAME1;
				
				$cmd = "select b.EN_NAME, d.INS_NAME, c.EL_NAME, e.EM_NAME, EDU_INSTITUTE
									from (
												(
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.en_code=b.en_code)	
													)left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												)left join PER_INSTITUTE d on (a.ins_code=d.ins_code)
											)left join PER_EDUCMAJOR e on (a.em_code=e.em_code)
									where  a.PER_ID=$PER_ID and a.EDU_TYPE like '%4%'	";
										
				$db_dpis2->send_cmd($cmd);
			//	$db_dpis2->show_error();
			//	echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME4 = trim($data4[EN_NAME]);
				$INS_NAME4 = trim($data4[INS_NAME]);
				$EL_NAME4 = trim($data4[EL_NAME]);		
				$EM_NAME4 = trim($data4[EM_NAME]);
				if (!$INS_NAME4) $INS_NAME4 = trim($data4[EDU_INSTITUTE]);
				$EDUCATE3 = $EN_NAME4 . " " . $EM_NAME4 . " " . $INS_NAME4;
			}elseif($DPISDB=="oci8"){
				$cmd = "select   b.EN_NAME, d.INS_NAME, c.EL_NAME, e.EM_NAME, EDU_INSTITUTE
										from PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_INSTITUTE d, PER_EDUCMAJOR e
										where  a.PER_ID='$PER_ID' and a.EDU_TYPE like '%2%' and a.en_code=b.en_code(+) and 
											a.ins_code=d.ins_code(+) and b.EL_CODE=c.EL_CODE(+) and a.EM_CODE=e.EM_CODE(+)";
										
				$db_dpis2->send_cmd($cmd);
		//		$db_dpis2->show_error();
		//		echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME2 = trim($data4[EN_NAME]);
				$INS_NAME2 = trim($data4[INS_NAME]);
				$EL_NAME2 = trim($data4[EL_NAME]);
				$EM_NAME2 = trim($data4[EM_NAME]);
				if (!$INS_NAME2) $INS_NAME2 = trim($data4[EDU_INSTITUTE]);
				$EDUCATE2 = $EN_NAME2 . " " . $EM_NAME2 . " " . $INS_NAME2;
				
				$cmd = "select   b.EN_NAME, d.INS_NAME, c.EL_NAME, e.EM_NAME, EDU_INSTITUTE
										from PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_INSTITUTE d, PER_EDUCMAJOR e
										where  a.PER_ID='$PER_ID' and a.EDU_TYPE like '%1%' and a.en_code=b.en_code(+) and 
											a.ins_code=d.ins_code(+) and b.EL_CODE=c.EL_CODE(+) and a.EM_CODE=e.EM_CODE(+)";
										
				$db_dpis2->send_cmd($cmd);
			//	$db_dpis2->show_error();
			//	echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME1 = trim($data4[EN_NAME]);
				$INS_NAME1 = trim($data4[INS_NAME]);
				$EL_NAME1 = trim($data4[EL_NAME]);		
				$EM_NAME1 = trim($data4[EM_NAME]);
				if (!$INS_NAME1) $INS_NAME1 = trim($data4[EDU_INSTITUTE]);
				$EDUCATE1 = $EN_NAME1 . " " . $EM_NAME1 . " " . $INS_NAME1;
			
				$cmd = "select   b.EN_NAME, d.INS_NAME, c.EL_NAME, e.EM_NAME, EDU_INSTITUTE
										from PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_INSTITUTE d, PER_EDUCMAJOR e
										where  a.PER_ID='$PER_ID' and a.EDU_TYPE like '%4%' and a.en_code=b.en_code(+) and 
											a.ins_code=d.ins_code(+) and b.EL_CODE=c.EL_CODE(+) and a.EM_CODE=e.EM_CODE(+)";
										
				$db_dpis2->send_cmd($cmd);
			//	$db_dpis2->show_error();
			//	echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME4 = trim($data4[EN_NAME]);
				$INS_NAME4 = trim($data4[INS_NAME]);
				$EL_NAME4 = trim($data4[EL_NAME]);		
				$EM_NAME4 = trim($data4[EM_NAME]);
				if (!$INS_NAME4) $INS_NAME4 = trim($data4[EDU_INSTITUTE]);
				$EDUCATE3 = $EN_NAME4 . " " . $EM_NAME4 . " " . $INS_NAME4;
			}elseif($DPISDB=="mysql"){
				$cmd = "select b.EN_NAME, d.INS_NAME, c.EL_NAME, e.EM_NAME, EDU_INSTITUTE
									from (
												(
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.en_code=b.en_code)	
													)left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												)left join PER_INSTITUTE d on (a.ins_code=d.ins_code)
											)left join PER_EDUCMAJOR e on (a.em_code=e.em_code)
									where  a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%'	"	;
										
				$db_dpis2->send_cmd($cmd);
		//		$db_dpis2->show_error();
		//		echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME2 = trim($data4[EN_NAME]);
				$INS_NAME2 = trim($data4[INS_NAME]);
				$EL_NAME2 = trim($data4[EL_NAME]);
				$EM_NAME2 = trim($data4[EM_NAME]);
				if (!$INS_NAME2) $INS_NAME2 = trim($data4[EDU_INSTITUTE]);
				$EDUCATE2 = $EN_NAME2 . " " . $EM_NAME2 . " " . $INS_NAME2;
				
				$cmd = "select b.EN_NAME, d.INS_NAME, c.EL_NAME, e.EM_NAME, EDU_INSTITUTE
									from (
												(
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.en_code=b.en_code)	
													)left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												)left join PER_INSTITUTE d on (a.ins_code=d.ins_code)
											)left join PER_EDUCMAJOR e on (a.em_code=e.em_code)
									where  a.PER_ID='$PER_ID' and a.EDU_TYPE like '%2%'	"	;
										
				$db_dpis2->send_cmd($cmd);
			//	$db_dpis2->show_error();
			//	echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME1 = trim($data4[EN_NAME]);
				$INS_NAME1 = trim($data4[INS_NAME]);
				$EL_NAME1 = trim($data4[EL_NAME]);		
				$EM_NAME1 = trim($data4[EM_NAME]);
				if (!$INS_NAME1) $INS_NAME1 = trim($data4[EDU_INSTITUTE]);
				$EDUCATE1 = $EN_NAME1 . " " . $EM_NAME1 . " " . $INS_NAME1;
				
				$cmd = "select b.EN_NAME, d.INS_NAME, c.EL_NAME, e.EM_NAME, EDU_INSTITUTE
									from (
												(
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.en_code=b.en_code)	
													)left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												)left join PER_INSTITUTE d on (a.ins_code=d.ins_code)
											)left join PER_EDUCMAJOR e on (a.em_code=e.em_code)
									where  a.PER_ID='$PER_ID' and a.EDU_TYPE like '%4%'	"	;
										
				$db_dpis2->send_cmd($cmd);
			//	$db_dpis2->show_error();
			//	echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME4 = trim($data4[EN_NAME]);
				$INS_NAME4 = trim($data4[INS_NAME]);
				$EL_NAME4 = trim($data4[EL_NAME]);		
				$EM_NAME4 = trim($data4[EM_NAME]);		 
				if (!$INS_NAME4) $INS_NAME4 = trim($data4[EDU_INSTITUTE]);
				$EDUCATE3 = $EN_NAME4 . " " . $EM_NAME4 . " " . $INS_NAME4;
			}
		} // end loop while $data
	} //end count_data
?>