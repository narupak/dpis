<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd); 
	if(empty($search_abs_approve)){ $search_abs_approve="0";}
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$report_title = trim(iconv("utf-8","tis620",urldecode($report_title)));
	$worksheet = &$workbook->addworksheet('T0204');
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 8);//ลำดับที่
		$worksheet->set_column(1, 1, 12);//ประเภท
		$worksheet->set_column(2, 2, 20);//ชื่อ-สกุล
		$worksheet->set_column(3, 3, 11);//วันที่ยื่นคำร้อง
		$worksheet->set_column(4, 4, 11);//วันที่ขออนุญาต
		$worksheet->set_column(5, 5, 11);//ขอลงเวลาเข้า
		$worksheet->set_column(6, 6, 11);//ขอลงเวลาออก
		$worksheet->set_column(7, 7, 24);//รอบเวลา
		$worksheet->set_column(8, 8, 30);//สำนัก/กอง
		$worksheet->set_column(9, 9, 60);//เหตุผล
		$worksheet->set_column(10, 10, 8);//ความเห็น
		$worksheet->set_column(11, 11, 8);//อนุมัติ
		$worksheet->set_column(12, 12, 20);//ชื่ออนุมัติ
		$worksheet->set_column(13, 13, 30);//ประเภทคำร้อง
		
		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ประเภท", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ชื่อ-สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "วันที่ยื่นคำร้อง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "วันที่ขออนุญาต", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "ขอลงเวลาเข้า", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "ขอลงเวลาออก", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "รอบการมาปฏิบัติราชการ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "สำนัก/กอง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 9, "เหตุผล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 10, "ความเห็น(ชั้นต้น)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 11, "อนุมัติ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 12, "ชื่อผู้อนุมัติ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 13, "ประเภทคำร้อง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // end if


  /*---------------------------------------------------------------*/
    //	กำหนดค่า default timezone		//phpinfo();
	function set_default_timezone($timezone){
		if (version_compare(phpversion(), '5', '>=')){
			if(function_exists('date_default_timezone_set')) { 
				$result_set = date_default_timezone_set($timezone); 	// PHP  >= 5.1.0
				//echo date_default_timezone_get();	// PHP  >= 5.1.0
			} 
		}else{		// < version 5
			$result_set = ini_set('date.timezone',$timezone);
		}
	return $result_set;
	}
	
	set_default_timezone('Asia/Bangkok');	// ทำเวลาให้เป็นเวลาโซนที่กำหนด

    switch($CTRL_TYPE){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			$PROVINCE_CODE = $PROVINCE_CODE;
			$PROVINCE_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			$MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
			$MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){								
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			$PROVINCE_CODE = $PROVINCE_CODE;
			$PROVINCE_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			$MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
			$MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			$MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
			$MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			$MAIN_ORG_ID = $ORG_ID;
			$MAIN_ORG_NAME = $ORG_NAME;
			break;
		case 6 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$search_org_id_1 = $ORG_ID_1;
			$search_org_name_1 = $ORG_NAME_1;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			$MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
			$MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			$MAIN_ORG_ID = $ORG_ID;
			$MAIN_ORG_NAME = $ORG_NAME;
			break;
	} // end switch case
    
    

    //หาว่าอยู่กลุ่ม กจ. กรม หรือไม่--------------------------------
    $cmd4 = "	select	 b.CODE from	user_detail a, user_group b
					where a.group_id=b.id AND a.ID=".$SESS_USERID;
    $db_dpis2->send_cmd($cmd4);
    $data4 = $db_dpis2->get_array();
    if ($data4[CODE]) {
        $NAME_GROUP_HRD = $data4[CODE];
    }else{
        $NAME_GROUP_HRD = "";
    }
    
    /*ดูสิทธิ์เป็นผู้ตรวจสอบการลาหรือไม่*/
    
   
     
    if ( ($SESS_USERGROUP !=1 && $NAME_GROUP_HRD!='HRD') && count($SESS_AuditArray) == 0  ){
            $PER_AUDIT_FLAG=0;
    }else if ( ($SESS_USERGROUP !=1 && $NAME_GROUP_HRD!='HRD') && count($SESS_AuditArray) > 0  ){
            $PER_AUDIT_FLAG=1;
    }
    
    
     if($NAME_GROUP_HRD=="HRD" || $SESS_USERGROUP ==1){
     	  if(!$Submit3 && !$image22){
             $select_org_structure=1;
          }
     }else{
        if ( ($SESS_USERGROUP !=1 && $NAME_GROUP_HRD!='HRD') && $PER_AUDIT_FLAG==1 ){
            $select_org_structure=1;
        }
     }
    
    $search_condition ="";
    if ($SESS_USERGROUP !=1 && $NAME_GROUP_HRD!='HRD'){
		 if ( ($SESS_USERGROUP !=1 && $NAME_GROUP_HRD!='HRD') && $PER_AUDIT_FLAG==1 ){
            $Consearch ="";
            $tCon="(";
            for ($i=0; $i < count($SESS_AuditArray); $i++) {
                if ($i>0)
                    $tCon .= " or ";
                $tCon .= "(B.ORG_ID=" .$SESS_AuditArray[$i][0];
                $tCon .= ")";
            }
            $tCon .= ")";
            $Consearch .= " or (".$tCon.") ";
            
         	 $search_condition .= " AND ( 1=1 AND A.PER_ID = $SESS_PER_ID or  A.ALLOW_USER = $SESS_PER_ID or A.APPROVE_USER = $SESS_PER_ID or A.CREATE_USER=$SESS_USERID ".$Consearch.")";
             
         }else{
         		if ($SESS_PER_ID ){
                    if($search_onlyme_flag==1){
                        $search_condition .= "  AND  (2=2 AND A.PER_ID = $SESS_PER_ID)";
                        
                    }else{
                        $search_condition .= "  AND  (3=3 AND A.PER_ID = $SESS_PER_ID or  A.ALLOW_USER = $SESS_PER_ID or A.APPROVE_USER = $SESS_PER_ID)";
                    	
                    }
                }
         
         }
                
    
    }
    

    

    if($SESS_USERGROUP==1 || $NAME_GROUP_HRD=='HRD'){	
    	if($search_org_id){
            if($select_org_structure==0)		$search_condition .= "  AND  (D.ORG_ID=$search_org_id or E.ORG_ID=$search_org_id or F.ORG_ID=$search_org_id or G.ORG_ID=$search_org_id)";
            if($select_org_structure==1)		$search_condition .= "  AND (B.ORG_ID=$search_org_id)";			
        }elseif($search_department_id){
            $search_condition .= "  AND (B.DEPARTMENT_ID = $search_department_id)";
        }elseif($search_ministry_id){
            $cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$search_ministry_id ";
            $db_dpis->send_cmd($cmd);
            while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
            $search_condition .= "  AND (B.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
        } // end if
    
    }else if($PER_AUDIT_FLAG==1){
    	
    	if($search_org_id){
            $search_condition .= " AND (B.ORG_ID=$search_org_id)";	
        }
    }	



	

	if ($search_per_type !=0){
		$search_condition .= " AND (A.PER_TYPE = $search_per_type)";
	}
	
	if ($search_abs_startdate && $search_abs_enddate) {
		$temp_date = explode("/", $search_abs_startdate);
		$temp_start = ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0];
		$temp_date = explode("/", $search_abs_enddate);
		$temp_end = ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0];		
		$search_condition .= " AND (A.REQUEST_DATE >= '$temp_start' and A.REQUEST_DATE <= '$temp_end')";
    }else if ($search_abs_startdate && !$search_abs_enddate) {
    	$temp_date = explode("/", $search_abs_startdate);
		$temp_start = ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0];	
		$search_condition .= " AND (A.REQUEST_DATE = '$temp_start' )";
     }else if (!$search_abs_startdate && $search_abs_enddate) {
    	$temp_date = explode("/", $search_abs_enddate);
		$temp_end = ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0];	
		$search_condition .= " AND (A.REQUEST_DATE = '$temp_end' )";
	}
    
    if ($search_SUBMITTED_STARTDATE && $search_SUBMITTED_ENDDATE) {
		$temp_date = explode("/", $search_SUBMITTED_STARTDATE);
		$temp_start = ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0];
		$temp_date = explode("/", $search_SUBMITTED_ENDDATE);
		$temp_end = ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0];		
		$search_condition .= " AND (A.SUBMITTED_DATE >= '$temp_start' and A.SUBMITTED_DATE <= '$temp_end')";
    }else if ($search_SUBMITTED_STARTDATE && !$search_SUBMITTED_ENDDATE) {
    	$temp_date = explode("/", $search_SUBMITTED_STARTDATE);
		$temp_start = ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0];	
		$search_condition .= " AND (A.SUBMITTED_DATE = '$temp_start' )";
     }else if (!$search_SUBMITTED_STARTDATE && $search_SUBMITTED_ENDDATE) {
    	$temp_date = explode("/", $search_SUBMITTED_ENDDATE);
		$temp_end = ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0];	
		$search_condition .= " AND (A.SUBMITTED_DATE = '$temp_end' )";
	}

	
    if($search_abs_approve==0){
        $search_condition .= " AND (A.APPROVE_FLAG = 0 or A.APPROVE_FLAG is null)";
    } else if($search_abs_approve !=0 && $search_abs_approve !=4){
        $search_condition .= " AND (A.APPROVE_FLAG =$search_abs_approve)";
    }
    
    
    if($search_Request_Type !=0){
        if($search_Request_Type ==1){
				$search_condition .= " AND (A.MEETING_FLAG =1)";
		}else if($search_Request_Type ==2){
				$search_condition .= " AND (A.SCAN_FLAG =1)";
		}else if($search_Request_Type ==3){
				$search_condition .= " AND (A.OTH_FLAG =1)";
		}else if($search_Request_Type ==4){
				$search_condition .= " AND (A.REQUEST_NOTE =1)";
		}else if($search_Request_Type ==6){
				$search_condition .= " AND (A.REQ_SPEC =1)";
		}else if($search_Request_Type ==5){
			$search_condition .= " AND (A.REQ_TIME =1)";
	}
    	
    }
    

    
	if(trim($search_per_name)) $search_condition .= " AND (B.PER_NAME like '$search_per_name%')";
	if(trim($search_per_surname)) $search_condition .= " AND (B.PER_SURNAME like '$search_per_surname%')";


		$cmd = "	SELECT A.REC_ID,A.PER_ID,A.PER_TYPE,B.PN_CODE,B.PER_NAME,B.PER_SURNAME,A.REQUEST_DATE,A.START_FLAG,A.START_TIME,
					    A.END_FLAG,A.END_TIME,A.REQ_FLAG,A.REQ_TIME,A.ALLOW_FLAG,A.ALLOW_USER,A.APPROVE_FLAG,A.APPROVE_USER,
					    A.SUBMITTED_DATE,A.MEETING_FLAG,A.REQ_STATUS,A.CREATE_USER,A.REQ_SPEC_NOTE,A.REQ_SPEC,
					    A.REQUEST_NOTE,A.REQUEST_TYPE,A.SCAN_FLAG,A.OTH_FLAG,A.WC_CODE,
						A.OTH_NOTE,B.PER_STATUS,B.PER_CARDNO,B.ORG_ID as ORG_ID_ASS, D.ORG_ID
						FROM  TA_REQUESTTIME A,PER_PERSONAL B,  PER_POSITION D, 
						PER_POS_EMP E, PER_POS_EMPSER F, PER_POS_TEMP G 
						WHERE A.PER_ID=B.PER_ID (+) 
						AND B.POS_ID=D.POS_ID(+) 
						AND B.POEM_ID=E.POEM_ID(+) 
						AND B.POEMS_ID=F.POEMS_ID(+) 
						AND B.POT_ID=G.POT_ID(+)
						$search_condition
			order by 	A.SUBMITTED_DATE DESC ,A.CREATE_DATE DESC ";

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 1;
		$data_num = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_num++;
			
			if($data[PER_TYPE]==1) $TMP_PER_TYPE="ข้าราชการ";
	  		else  if($data[PER_TYPE] ==2) $TMP_PER_TYPE= "ลูกจ้างประจำ";
	  		else  if($data[PER_TYPE] ==3) $TMP_PER_TYPE= "พนักงานราชการ";
	  		else  if($data[PER_TYPE] ==4) $TMP_PER_TYPE= "ลูกจ้างชั่วคราว";
			

			  $cmd ="select WC_NAME from PER_WORK_CYCLE where WC_CODE =  $data[WC_CODE]";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$WC_NAME = $data2[WC_NAME];
			if($WC_NAME==""){
				$WC_NAME = 'รอบที่ 2 (08:30 - 16:30 น.)';
			}

			$ORG_ID_ASS = $data[ORG_ID_ASS];
			$ORG_ID = $data[ORG_ID];

			if($select_org_structure==0){
				$cmd = "SELECT ORG_NAME FROM PER_ORG WHERE ORG_ID = $ORG_ID";//ตามกฎหมาย
			} 

			if($select_org_structure==1){
				$cmd = "SELECT ORG_NAME FROM PER_ORG_ASS WHERE ORG_ID = $ORG_ID_ASS";//ตามมอบหมาย
			}
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = $data2[ORG_NAME]; 
			
			  //WC_CODE  
			$TMP_PER_NAME = $data[PER_NAME];
			$TMP_PER_SURNAME = $data[PER_SURNAME];
	
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$data[PN_CODE]' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_PN_NAME = $data2[PN_NAME];
			$TMP_PER_NAME = $TMP_PN_NAME.$TMP_PER_NAME." ".$TMP_PER_SURNAME;

			$dbSUBMITTED_DATE= show_date_format($data[SUBMITTED_DATE], $DATE_DISPLAY);
			$dbREQUEST_DATE= show_date_format($data[REQUEST_DATE], $DATE_DISPLAY);
			$START_TIME="";
			$END_TIME="";
			if($data[START_TIME]){
				$START_TIME = substr($data[START_TIME],0,2).":".substr($data[START_TIME],2,2);
			} 
			if($data[END_TIME]){
				$END_TIME = substr($data[END_TIME],0,2).":".substr($data[END_TIME],2,2);
			}
			$Detail_type = "";
			if($data[START_FLAG]==1){
				//$Detail_type = "ขอลงเวลาเข้า";
			}
			
			$Detail_type1="";
			if($data[END_FLAG]==1){
				//$Detail_type1 = $Detail_type1.$comma."ขอลงเวลาออก";
			} 
			
			$Detail_type11=""; 
		   if($data[MEETING_FLAG]==1){
				$Detail_type11 = $Detail_type11."ติดประชุม/สัมมนา/อบรม, ";
			}
			
			if($data[SCAN_FLAG]==1){
				$Detail_type11 = $Detail_type11."ลืมสแกน, ";
			}
			
			if($data[REQUEST_NOTE]==1){
				$Detail_type11 = $Detail_type11."ลาชั่วโมง, ";
			}
			
			if($data[REQ_TIME]==1){
				$Detail_type11 = $Detail_type11."ขอสแกนออกไปปฏิบัติราชการ, ";
			}
			
			if($data[REQ_SPEC]==1){
				$Detail_type11 = $Detail_type11."Work from Home เพื่อปฏิบัติงาน ".$data[REQ_SPEC_NOTE].", ";
			}

			if($data[OTH_FLAG]==1){
				$Detail_type11 = $Detail_type11." อื่นๆ (ระบุ) ".$data[OTH_NOTE].", ";
			
			}
				
			
			
			$Detail_type2="";
			if($data[REQ_FLAG]==1){
				$Detail_type2 = "ขอออกก่อน".$data[REQUEST_NOTE];
				$END_TIME = substr($data[REQ_TIME],0,2).":".substr($data[REQ_TIME],2,2);
			}
			$Detail_type2="";
			/*if($data[ALLOW_FLAG]==1){
				$DATA_ALLOW_FLAG = "../images/true.bmp";
			}else if($data[ALLOW_FLAG]==2){
				$DATA_ALLOW_FLAG = "../images/false.bmp";
			}else{
				$DATA_ALLOW_FLAG = "../images/checkbox_blank.bmp";
			}*/
			
			if($data[ALLOW_FLAG]==1){
				$DATA_ALLOW_FLAG = "รับรอง";
			}else if($data[ALLOW_FLAG]==2){
				$DATA_ALLOW_FLAG = "ไม่รับรอง";
			}else{
				$DATA_ALLOW_FLAG = "";
			}
			
			/*if($data[APPROVE_FLAG]==1){
				$DATA_APPROVE_FLAG = "../images/true.bmp";
			}else if($data[APPROVE_FLAG]==2){
				$DATA_APPROVE_FLAG = "../images/false.bmp";
			}else{
				$DATA_APPROVE_FLAG = "../images/checkbox_blank.bmp";
			}*/
			
			if($data[APPROVE_FLAG]==1){
				$DATA_APPROVE_FLAG = "อนุมัติ";
			}else if($data[APPROVE_FLAG]==2){
				$DATA_APPROVE_FLAG = "ไม่อนุมัติ";
			}else{
				$DATA_APPROVE_FLAG = "";
			}
			
			$MSO_FLAG="xxxxxx";
			
			if($data[MEETING_FLAG]==1 ){
				$MSO_FLAG.=", ติดประชุม/สัมมนา/อบรม";
			}
			
			if($data[SCAN_FLAG]==1 ){
				$MSO_FLAG.=", ลืมสแกน";
			}
			
			if($data[REQUEST_NOTE]==1 ){
				$MSO_FLAG.=", ลาชั่วโมง";
			}
			
			if($data[REQ_TIME]==1 ){
				$MSO_FLAG.=", ไปปฏิบัติราชการ";
			}
			if($data[REQ_SPEC]==1 ){
				$MSO_FLAG.=", Work from Home เพื่อปฏิบัติงาน  ";
			}
			if($data[OTH_FLAG]==1 ){
				$MSO_FLAG.=", อื่นๆ";
			}
			
			if($MSO_FLAG!="xxxxxx"){
				$MSO_FLAG = str_replace('xxxxxx, ', '', $MSO_FLAG);
			}else{
				$MSO_FLAG="";
			}

			
			$DATA_APPROVE_USER = $data[APPROVE_USER];
			$cmd ="select g.PN_SHORTNAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW
					from PER_PERSONAL a 
					left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE) 
					where PER_ID = $DATA_APPROVE_USER ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DATA_APPROVE_NAME = $data2[FULLNAME_SHOW];
			
			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $data_num, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $TMP_PER_TYPE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $TMP_PER_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $dbSUBMITTED_DATE, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $dbREQUEST_DATE, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 5, $START_TIME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 6, $END_TIME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 7, $WC_NAME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 8, $ORG_NAME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 9, substr($Detail_type11,0,-2), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 10, $DATA_ALLOW_FLAG, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 11, $DATA_APPROVE_FLAG, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 12, $DATA_APPROVE_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 13, $MSO_FLAG, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"T0204.xls\"");
	header("Content-Disposition: inline; filename=\"T0204.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>