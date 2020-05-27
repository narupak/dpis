<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd); 
	if(empty($search_abs_approve)){ $search_abs_approve="0";}
	   if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
		require("../../RTF/rtf_class.php");
	} else	 {
		define('FPDF_FONTPATH','../../PDF/font/');
		include ("../../PDF/fpdf.php");
		include ("../../PDF/pdf_extends_DPIS.php");
	}
	
	ini_set("max_execution_time", $max_execution_time);
	
	$report_title = trim(iconv("utf-8","tis620",urldecode($report_title)));
	$report_code = "";
	

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	if ($FLAG_RTF) {
		$sum_w = array_sum($heading_width);
		for($h = 0; $h < count($heading_width); $h++) {
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}
		$fname= $report_title.".rtf";
	//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
		$paper_size="a4";
		$orientation='L';
		$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);
		$RTF->set_default_font($font, 10);
	//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

		$RTF->set_report_code($report_code);
		$RTF->set_report_title($report_title);
		$RTF->set_company_name($company_name);
		} else {
			$fname= "T0204.pdf";
    $unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$orientation='L';
	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	$pdf->SetAutoPageBreak(true,10);
	}
	if ($FLAG_RTF) {
		$heading_width[0] = "11";
		$heading_width[1] = "17";
		$heading_width[2] = "35";
		$heading_width[3] = "20";
		$heading_width[4] = "20";
		$heading_width[5] = "15";
		$heading_width[6] = "15";
		$heading_width[7] = "50";
		$heading_width[8] = "15";
		$heading_width[9] = "15";
		$heading_width[10] = "35";
		$heading_width[11] = "35";
	}else{
		$heading_width[0] = "11";
		$heading_width[1] = "17";
		$heading_width[2] = "35";
		$heading_width[3] = "20";
		$heading_width[4] = "20";
		$heading_width[5] = "15";
		$heading_width[6] = "15";
		$heading_width[7] = "50";
		$heading_width[8] = "15";
		$heading_width[9] = "15";
		$heading_width[10] = "35";
		$heading_width[11] = "35";
	}	
	$heading_text[0] = "ลำดับที่";
	$heading_text[1] = "ประเภท";
	$heading_text[2] = "ชื่อ-สกุล";
	$heading_text[3] = "วันที่ยื่นคำร้อง";
	$heading_text[4] = "วันที่ขออนุญาต";
	$heading_text[5] = "ขอลงเวลาเข้า";
	$heading_text[6] = "ขอลงเวลาออก";
	$heading_text[7] = "เหตุผล";
	$heading_text[8] = "ความเห็น(ชั้นต้น)";
	$heading_text[9] = "อนุมัติ";
	$heading_text[10] = "ชื่อผู้อนุมัติ";
	$heading_text[11] = "ประเภทคำร้อง";

	
		
	$heading_align = array('C','C','C','C','C','C','C','C','C','C','C','C');
		
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
		}else if($search_Request_Type ==5){
				$search_condition .= " AND (A.REQ_TIME =1)";
		}else if($search_Request_Type ==6){
			$search_condition .= " AND (A.REQ_SPEC =1)";
	}
    	
    }
    

    
	if(trim($search_per_name)) $search_condition .= " AND (B.PER_NAME like '$search_per_name%')";
	if(trim($search_per_surname)) $search_condition .= " AND (B.PER_SURNAME like '$search_per_surname%')";


		$cmd = "	SELECT A.REC_ID,A.PER_ID,A.PER_TYPE,B.PN_CODE,B.PER_NAME,B.PER_SURNAME,A.REQUEST_DATE,A.START_FLAG,A.START_TIME,
					    A.END_FLAG,A.END_TIME,A.REQ_FLAG,A.REQ_TIME,A.ALLOW_FLAG,A.ALLOW_USER,A.APPROVE_FLAG,A.APPROVE_USER,A.REQ_SPEC_NOTE,
                        A.REQ_SPEC,A.SUBMITTED_DATE,A.MEETING_FLAG,A.REQ_STATUS,A.CREATE_USER,
					    A.REQUEST_NOTE,A.REQUEST_TYPE,A.SCAN_FLAG,A.OTH_FLAG,
						A.OTH_NOTE,B.PER_STATUS,B.PER_CARDNO
						FROM  TA_REQUESTTIME A,PER_PERSONAL B,  PER_POSITION D, 
						PER_POS_EMP E, PER_POS_EMPSER F, PER_POS_TEMP G 
						WHERE A.PER_ID=B.PER_ID (+) 
						AND B.POS_ID=D.POS_ID(+) 
						AND B.POEM_ID=E.POEM_ID(+) 
						AND B.POEMS_ID=F.POEMS_ID(+) 
						AND B.POT_ID=G.POT_ID(+)
						$search_condition
			order by 	A.SUBMITTED_DATE DESC ,A.CREATE_DATE DESC ";
//echo "<pre>".$cmd;
//die();

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	    $head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		if ($FLAG_RTF) {
			$RTF->add_header("", 0, false);	// header default
			$RTF->add_footer("", 0, false);		// footer default
			
	//		echo "$head_text1<br>";
		$tab_align = "center";
		$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, "", false, $tab_align);
		} else {
		$pdf->AutoPageBreak = false; 
	    $result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
		          }
	if (!$result) echo "****** error ****** on open table for $table<br>";

	if($count_data){
		$data_num = 0;
		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;			
			$data_num++;
			$arr_data = (array) null;
			
			$arr_data[] = $data_num;
			
			if($data[PER_TYPE]==1) $TMP_PER_TYPE="ข้าราชการ";
	  		else  if($data[PER_TYPE] ==2) $TMP_PER_TYPE= "ลูกจ้างประจำ";
	  		else  if($data[PER_TYPE] ==3) $TMP_PER_TYPE= "พนักงานราชการ";
	  		else  if($data[PER_TYPE] ==4) $TMP_PER_TYPE= "ลูกจ้างชั่วคราว";
			$arr_data[] = $TMP_PER_TYPE;
			
			$TMP_PER_NAME = $data[PER_NAME];
			$TMP_PER_SURNAME = $data[PER_SURNAME];
	
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$data[PN_CODE]' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_PN_NAME = $data2[PN_NAME];
			$TMP_PER_NAME = $TMP_PN_NAME.$TMP_PER_NAME." ".$TMP_PER_SURNAME;
			$arr_data[] = $TMP_PER_NAME;
			
			$dbSUBMITTED_DATE= show_date_format($data[SUBMITTED_DATE], $DATE_DISPLAY);
			$arr_data[] = $dbSUBMITTED_DATE;
			
			$dbREQUEST_DATE= show_date_format($data[REQUEST_DATE], $DATE_DISPLAY);   
			$arr_data[] = $dbREQUEST_DATE;
			
			$START_TIME="";
			$END_TIME="";
			if($data[START_TIME]){
				$START_TIME = substr($data[START_TIME],0,2).":".substr($data[START_TIME],2,2);
			} 
			$arr_data[] = $START_TIME;
			if($data[END_TIME]){
				$END_TIME = substr($data[END_TIME],0,2).":".substr($data[END_TIME],2,2);
			}
			$arr_data[] = $END_TIME;

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

			
			$arr_data[] = substr($Detail_type11,0,-2);
			
			if($data[ALLOW_FLAG]==1){
				//$DATA_ALLOW_FLAG = "true";
				$DATA_ALLOW_FLAG = "รับรอง";
			}else if($data[ALLOW_FLAG]==2){
				//$DATA_ALLOW_FLAG = "false";
				$DATA_ALLOW_FLAG = "ไม่รับรอง";
			}else{
				//$DATA_ALLOW_FLAG = "checkbox_blank";
				$DATA_ALLOW_FLAG = "";
			}
			//$arr_data[] = "<*img*../images/".$DATA_ALLOW_FLAG.".jpg*img*>";
			$arr_data[] = $DATA_ALLOW_FLAG;

			if($data[APPROVE_FLAG]==1){
				//$DATA_APPROVE_FLAG = "true";
				$DATA_APPROVE_FLAG = "อนุมัติ";
			}else if($data[APPROVE_FLAG]==2){
				//$DATA_APPROVE_FLAG = "false";
				$DATA_APPROVE_FLAG = "ไม่อนุมัติ";
			}else{
				//$DATA_APPROVE_FLAG = "checkbox_blank";
				$DATA_APPROVE_FLAG = "";
			}
			//$arr_data[] = "<*img*../images/".$DATA_APPROVE_FLAG.".jpg*img*>";
			$arr_data[] = $DATA_APPROVE_FLAG;
			
			$DATA_APPROVE_USER = $data[APPROVE_USER];
			$cmd ="select g.PN_SHORTNAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW
					from PER_PERSONAL a 
					left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE) 
					where PER_ID = $DATA_APPROVE_USER ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DATA_APPROVE_NAME = $data2[FULLNAME_SHOW];
			$arr_data[] = $DATA_APPROVE_NAME;
			
			
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
				

			$arr_data[] = $MSO_FLAG;

			$data_align = array("C", "L", "L", "C", "C", "C", "C", "L", "C", "C", "L", "L");
			  if ($FLAG_RTF)
			 $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		    else	
			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end while
	}else{
	      if ($FLAG_RTF){
				$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		  }else{
				$arr_data = (array) null;
				$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
				$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
				$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
				$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
				$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
				$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
				$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
				$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
				$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
        }
		$data_align = array("C", "L", "C", "C", "C", "C", "C", "C", "C", "C");
	    if ($FLAG_RTF)
	    $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "16", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		else	
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "16", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
	} // end if
	  if ($FLAG_RTF) {
			$RTF->close_tab(); 
//			$RTF->close_section(); 
			$RTF->display($fname);
		} else {
			$pdf->close_tab(""); 
			$pdf->close();
			$pdf->Output($fname,'D');	
		}
	ini_set("max_execution_time", 30);
?>