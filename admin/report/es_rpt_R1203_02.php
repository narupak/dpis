<?php
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/es_fpdf.php");
	include ("../../PDF/es_pdf_extends_DPIS.php");
	

	ini_set("max_execution_time", $max_execution_time);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	
	
	/*เงื่อนไขการออกรายงาน*/
	/*ประเภทบุคลากร*/
	$con_per_type = "";
	$con_per_type = " and a.PER_TYPE = $search_per_type ";
        
        /* หาสถานะ */
        $con_per_status = "";
        if($search_per_type){
            $con_per_status = "a.PER_STATUS in (". implode(", ", $search_per_status) .")";
        }
	
	/*ตามโครงสร้าง*/
	if($search_per_type==1){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$position_join_god = "orggod.ORG_ID=b.ORG_ID";
		$line_code = "b.PL_CODE";
		$position_no_name = "b.POS_NO_NAME";
		$position_no = "b.POS_NO";
		$line_search_code = trim($search_pl_code);
		$line_search_name = trim($search_pl_name);		
		$line_title = " สายงาน";
		
		$Frm_line = " PER_LINE ";
		$ON_code = "LIN.PL_CODE";
		$line_seq = "LIN.PL_SEQ_NO";
		$line_name = "LIN.PL_NAME";
		
		

	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$position_join_god = "orggod.ORG_ID=b.ORG_ID";
		$line_code = "b.PN_CODE";
		$position_no_name = "b.POEM_NO_NAME";
		$position_no = "b.POEM_NO";
		$line_search_code = trim($search_pn_code);
		$line_search_name = trim($search_pn_name);		
		$line_title = " ชื่อตำแหน่ง";
		
		$Frm_line = " PER_POS_NAME ";
		$ON_code = "LIN.PN_CODE";
		$line_seq = "LIN.PN_SEQ_NO";
		$line_name = "LIN.PN_NAME";

	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$position_join_god = "orggod.ORG_ID=b.ORG_ID";
		$line_code = "b.EP_CODE";
		$position_no_name = "b.POEMS_NO_NAME";
		$position_no = "b.POEMS_NO";
		$line_search_code = trim($search_ep_code);
		$line_search_name = trim($search_ep_name);		
		$line_title = " ชื่อตำแหน่ง";
		
		$Frm_line = " PER_EMPSER_POS_NAME ";
		$ON_code = "LIN.EP_CODE";
		$line_seq = "LIN.EP_SEQ_NO";
		$line_name = "LIN.EP_NAME";

	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$position_join_god = "orggod.ORG_ID=b.ORG_ID";
		$line_code = "b.TP_CODE";
		$position_no_name = "b.POT_NO_NAME";
		$position_no = "b.POT_NO";
		$line_search_code = trim($search_tp_code);
		$line_search_name = trim($search_tp_name);		
		$line_title = " ชื่อตำแหน่ง";
		
		$Frm_line = " PER_TEMP_POS_NAME ";
		$ON_code = "LIN.TP_CODE";
		$line_seq = "LIN.TP_SEQ_NO";
		$line_name = "LIN.TP_NAME";
	} // end if
	
	if($select_org_structure==0 || $PER_AUDIT_FLAG==2) { 
		/*ตามโครงสร้างกฏหมาย*/
		$conTPER_ORG = " LEFT JOIN PER_ORG  c ON(c.ORG_ID=b.ORG_ID)
								     LEFT JOIN PER_ORG  e ON(e.ORG_ID=a.DEPARTMENT_ID) ";
	}else{
		/*ตามโครงสร้างมอบหมายงาน*/
		$conTPER_ORG = " LEFT JOIN PER_ORG_ASS  c ON(c.ORG_ID=a.ORG_ID)
								     LEFT JOIN PER_ORG_ASS  e ON(e.ORG_ID=a.DEPARTMENT_ID) ";
	}
	
	/*สถานที่*/
	/*$con_wl_code = "";
	if(trim($search_wl_code)){
		$con_wl_code = " and PWT.WL_CODE = '$search_wl_code' ";
	}*/
	
	
	/*รอบการปฏิบัติงาน*/
	/*$con_wc_code = "";
	if(trim($search_wc_code)){
		$con_wc_code = " and PWT.WC_CODE ='$search_wc_code' ";
	}*/
	
	
	/*วันที่*/
	function MonthDays($someMonth, $someYear)
	{
		return date("t", strtotime($someYear . "-" . $someMonth . "-01"));
	}
	
	if(trim($search_yearBgn)){
		
		$search_dateBgn = ($search_yearBgn - 543) ."-". substr("0".$search_month,-2) ."-01";
		$search_dateEnd = ($search_yearBgn- 543) ."-". substr("0".$search_month,-2) ."-". MonthDays(substr("0".$search_month,-2),($search_yearBgn-543));
		$show_date = ("1") ." ". $month_full[($search_month + 0)][TH] ." ". $search_yearBgn ." ถึงวันที่ ".MonthDays(substr("0".$search_month,-2),($search_yearBgn-543)) ." ". $month_full[($search_month + 0)][TH] ." ". $search_yearBgn;
	}
	
	$show_date= (($NUMBER_DISPLAY==2)?convert2thaidigit($show_date):$show_date);
	
	/*---------------------------------------*/
	
	
	$search_condition = "";
	//$list_type_text = $ALL_REPORT_TITLE;
	$list_type_text = "";
	/*รูปแบบการออกรายงาน*/
	
	/*ทั้งส่วนราชการ*/
	$conditionDEPARTMENT ="";
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
		if($DEPARTMENT_ID){
			$conditionDEPARTMENT = " AND (a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			//$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1 || $PER_AUDIT_FLAG==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd_fast($cmd);
			while($data = $db_dpis->get_array_array()) $arr_org_ref[] = $data[ORG_ID];

			$conditionDEPARTMENT = " AND (a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			//$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$conditionDEPARTMENT = " AND (trim(c.PV_CODE) = '$PROVINCE_CODE')";
			//$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	
	
	/*สำนัก/กอง*/
	$list_type_texts = "";
        if($select_org_structure==0 || $PER_AUDIT_FLAG==2) {
            if(trim($search_org_id)){ 
                    $search_condition .= " AND  (b.ORG_ID = $search_org_id) ";
                    //$list_type_text .= " - $search_org_name"; ไม่ต้องแสดงมันจะซ้ำกัน
            } // end if
            if(trim($search_org_id_1)){ 
                    $search_condition .= " AND  (b.ORG_ID_1 = $search_org_id_1)";
                    $list_type_texts = " - $search_org_name_1";
            } // end if
        }else{
            if(trim($search_org_ass_id)){ 
                     $search_condition .= " AND  (a.ORG_ID = $search_org_ass_id)";
                    $list_type_text .= " - $search_org_ass_name";
            } // end if
            if(trim($search_org_ass_id_1)){ 
                    $search_condition .= " AND  (a.ORG_ID_1 =  $search_org_ass_id_1)";
                    $list_type_texts = " - $search_org_ass_name_1";
            } // end if
        }
	
	/*ตำแหน่งในสายงาน*/
	if(in_array("PER_LINE", $list_type)){
		if($line_search_code){
			$search_condition .= " AND (trim($line_code)='$line_search_code')";
			$list_type_text .= " - $line_search_name";
		}
	}
	
	/*ส่วนกลาง/ส่วนกลางในภูมิภาค/ส่วนภูมิภาค/ต่างประเทศ*/
	$conOT_CODE="'00'";
	if(in_array("PER_ORG_TYPE_1", $list_type)){
		$list_type_text = " - ส่วนกลาง";
		$conOT_CODE .= ",'01' ";
	}
	
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		$list_type_text .= " - ส่วนกลางในภูมิภาค";
		$conOT_CODE .= ",'02' ";
	}
	
	if(in_array("PER_ORG_TYPE_3", $list_type)){
		$list_type_text .= " - ส่วนภูมิภาค";
		$conOT_CODE .= ",'03' ";
	}
	
	if(in_array("PER_ORG_TYPE_4", $list_type)){
		$list_type_text .= " - ต่างประเทศ";
		$conOT_CODE .= ",'04' ";
	}
	
	if($conOT_CODE != "'00'"){
		$search_condition .= " AND  (trim(c.OT_CODE) in($conOT_CODE)) ";
	}
	
	/*ประเทศ*/
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$search_condition .= " AND (trim(c.CT_CODE) = '$search_ct_code')";
			$list_type_text .= " - $search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$search_condition .= " AND(trim(c.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}
	
	
	
	$POS_NO_VAL1="";
	$PER_NAME_VAL = "";
	$PER_SURNAME_VAL = "";
	$LEVEL_NO_VAL ="";
     if($list_person_type=="SELECT"){
		 if(!$SELECTED_PER_ID){$SELECTED_PER_ID=0;}
        $PERID_VAL = " AND a.PER_ID IN($SELECTED_PER_ID) ";
    }elseif($list_person_type == "CONDITION"){
		
		if(trim($search_pos_no)){ 
				if($search_per_type==1){ 
					$POS_NO_VAL1 = " AND  (b.POS_NO = '$search_pos_no')";
				
				}elseif($search_per_type==2){ 
					$POS_NO_VAL1 = " AND (b.POEM_NO = '$search_pos_no')";
					
				}elseif($search_per_type==3){ 
					$POS_NO_VAL1 = " AND (b.POEMS_NO = '$search_pos_no')";
					
				}elseif($search_per_type==4){ 
					$POS_NO_VAL1 = " AND (b.POT_NO = '$search_pos_no')";	
					
				}

		} // end if
		
		if(trim($search_name)){ 
				$PER_NAME_VAL = " AND (a.PER_NAME like '$search_name%')";	
		} // end if
		
		if(trim($search_surname)){ 
				$PER_SURNAME_VAL = " AND (a.PER_SURNAME like '$search_surname%')";	
		} // end if
		
		
		if($search_per_type==1){
                    if(trim($search_min_level1) & trim($search_max_level1)){ 
                            $search_min_level=$search_min_level1;
                            $search_max_level=$search_max_level1;

                            $LEVEL_NO_VAL = " AND a.LEVEL_NO in (select LEVEL_NO  from PER_LEVEL where PER_TYPE = $search_per_type and
                                                                                            LEVEL_SEQ_NO between 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_min_level') and 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_max_level')
                                                                                            ) ";	
                    }
                }else if($search_per_type==2){        
                    if(trim($search_min_level2) & trim($search_max_level2)){ 
                            $search_min_level=$search_min_level2;
                            $search_max_level=$search_max_level2;

                            $LEVEL_NO_VAL = " AND a.LEVEL_NO in (select LEVEL_NO  from PER_LEVEL where PER_TYPE = $search_per_type and
                                                                                            LEVEL_SEQ_NO between 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_min_level') and 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_max_level')
                                                                                            ) ";	
                    }
                }else if($search_per_type==3){
                    if(trim($search_min_level3) & trim($search_max_level3)){ 
                            $search_min_level=$search_min_level3;
                            $search_max_level=$search_max_level3;

                            $LEVEL_NO_VAL = " AND a.LEVEL_NO in (select LEVEL_NO  from PER_LEVEL where PER_TYPE = $search_per_type and
                                                                                            LEVEL_SEQ_NO between 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_min_level') and 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_max_level')
                                                                                            ) ";	


                    }
                }else if($search_per_type==4){
                    if(trim($search_min_level4) & trim($search_max_level4)){ 
                            $search_min_level=$search_min_level4;
                            $search_max_level=$search_max_level4;

                            $LEVEL_NO_VAL = " AND a.LEVEL_NO in (select LEVEL_NO  from PER_LEVEL where PER_TYPE = $search_per_type and
                                                                                            LEVEL_SEQ_NO between 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_min_level') and 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_max_level')
                                                                                            ) ";
                    }
		}
		
        $PERID_VAL = " ";
	}elseif($list_person_type == "ALL"){
		$PERID_VAL = "  ";
    }

/*---------------------------------------*/
/**/
if(!trim($RPTORD_LIST)){ 
	$RPTORD_LIST = "MINISTRY|";
}
$arr_rpt_order = explode("|", $RPTORD_LIST);
$order_by = "";
$Frm_order_by = "";
$Select_in_order_by = "";
$Select_out_order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){

			/*case "DEPARTMENT" : 
					if($order_by) $order_by .= ", ";
					$order_by .= "xx.DEPARTMENT";
					break;
			case "ORG" :
				$order_by .= ",xx.KONG";
				break; เริ่มอันนี้*/
			case "ORG_1" :
				$Frm_order_by .=" LEFT JOIN PER_ORG  org1 ON(org1.ORG_ID=b.ORG_ID_1) ";
				$order_by .=" ,org1.ORG_SEQ_NO,org1.ORG_ID  ";
				break;
			case "ORG_2" :
				$Frm_order_by .=" LEFT JOIN PER_ORG  org2 ON(org2.ORG_ID=b.ORG_ID_2) ";
				$order_by .=" ,org2.ORG_SEQ_NO,org2.ORG_ID  ";
				break;
			case "PROVINCE" :
				
				$Frm_order_by .=" LEFT JOIN PER_PROVINCE  PVN ON(PVN.PV_CODE=a.PV_CODE) ";
				$order_by .= ",PVN.PV_SEQ_NO,PVN.PV_CODE";
				break;
			case "LINE" :
				$Frm_order_by .=" LEFT JOIN $Frm_line  LIN ON($ON_code=$line_code) ";
				$order_by .= ",$line_seq,$ON_code"; 
				break;
			case "LEVEL" :
				
				$Frm_order_by .=" LEFT JOIN PER_LEVEL  LEV ON(LEV.LEVEL_NO=a.LEVEL_NO) ";
				$order_by .= " ,LEV.LEVEL_SEQ_NO,LEV.LEVEL_NO";
				
				break;
			case "POSNO" :
				
				$order_by .=" ,$position_no_name , to_number(replace($position_no,'-',''))  ";
				break;
			case "NAME" :
				$order_by .= ",a.PER_NAME, a.PER_SURNAME";
				break;
			case "GENDER" :
				$order_by .= ",a.PER_GENDER";
				break;
			case "STRATDATE" :
				$order_by .= ",a.START_DATE";
				break;
			
		} // end switch case
	} // end for
	

/*------------------------------------------*/
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1 || $PER_AUDIT_FLAG==1 ?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R1206";
	include ("es_rpt_R1203_02_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	//$orientation='L';
	$orientation='L';

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
	$pdf->Open();
	//$pdf->SetMargins(20,5,17);
	$pdf->SetFont($font,'',$CH_PRINT_SIZE);
    $pdf->SetMargins($Px_DISLEFT,$Px_DISHEAD,$Px_DISRIGHT);
	$pdf->AliasNbPages();
	$pdf->SetTextColor(0, 0, 0);

	if($DPISDB=="oci8"){
		$CON_PER_AUDIT_FLAG="";
		if ( $PER_AUDIT_FLAG==1 ){
			$tCon="(";
			for ($i=0; $i < count($SESS_AuditArray); $i++) {
				if ($i>0)
					$tCon .= " or ";
				$tCon .= "(a.ORG_ID=" .$SESS_AuditArray[$i][0];
				if ($SESS_AuditArray[$i][1] != 0)
					$tCon .= ' and a.ORG_ID_1='. $SESS_AuditArray[$i][1];
				$tCon .= ")";
			}
			$tCon .= ")";
			 $CON_PER_AUDIT_FLAG .= $tCon;
			
			if($search_org_ass_id){
                            $CON_PER_AUDIT_FLAG .= " AND (a.ORG_ID=$search_org_ass_id)";
                            if($search_org_ass_id_1){
                                $CON_PER_AUDIT_FLAG .= " AND (a.ORG_ID_1=$search_org_ass_id_1)";
                            }
			}
		}
		if ( $PER_AUDIT_FLAG==1 ){
			$CON_PER_AUDIT_FLAG = " AND (".$CON_PER_AUDIT_FLAG.")";
		}elseif($PER_AUDIT_FLAG==2){
			$CON_PER_AUDIT_FLAG .= " AND a.PER_ID=$SESS_PER_ID ";
		}

		$cmd = " with AllDay As
						(
						  SELECT (TO_DATE(substr('$search_dateBgn 00:00:00',1,10), 'YYYY-MM-DD'))-1+rownum AS WORK_DATE FROM all_objects
							WHERE (TO_DATE(substr('$search_dateBgn 00:00:00',1,10), 'YYYY-MM-DD'))-1+ rownum <= last_day(TO_DATE(substr('$search_dateBgn 00:00:00',1,10), 'YYYY-MM-DD'))
						)
						,Work as
						(
						  SELECT PER_ID,WORK_DATE,APV_ENTTIME,APV_EXITTIME,WC_CODE,WORK_FLAG,ABSENT_FLAG,HOLIDAY_FLAG,SCAN_ENTTIME,REMARK AS WORK_REMARK
						  FROM PER_WORK_TIME 
						  WHERE 
						  WORK_DATE BETWEEN  to_date('$search_dateBgn 00:00:00','yyyy-mm-dd hh24:mi:ss') 
								        and to_date('$search_dateEnd 23:59:59','yyyy-mm-dd hh24:mi:ss')
						  
						)
						,Per_AllDay as
						(
						  SELECT distinct w.PER_ID,a.WORK_DATE
						  from (select distinct per_id from Work) w
						  left join AllDay a on (1=1)
						)
						,PER_WORK_TIME_ALL as
						(
						  select * from Work
						  
						  union
						  SELECT w.PER_ID,w.WORK_DATE,null,null,null,null,null,3,null,null
						  from Per_AllDay w
						  where not exists (select null from Work a where a.per_id=w.PER_ID and a.WORK_DATE=w.WORK_DATE)
						)
		
		
						SELECT
					  e.ORG_NAME AS DEPARTMENT, c.ORG_NAME AS KONG,
					  orggod.ORG_NAME AS KONG_GOD,
					  $ON_code AS PL_NAME,
					  PN.PN_NAME,a.PER_NAME,a.PER_SURNAME,a.PER_CARDNO,
					  TO_CHAR(wt.WORK_DATE,'yyyy-mm-dd') AS WORK_DATE,
					wt.WC_CODE,
					TO_CHAR(wt.APV_ENTTIME,'hh24:mi') AS APV_ENTTIME,
					TO_CHAR(wt.APV_EXITTIME,'hh24:mi') AS APV_EXITTIME,
					
					(CASE WHEN wt.WORK_FLAG=2 AND wt.ABSENT_FLAG=10   THEN 0.5
                			  WHEN wt.WORK_FLAG=2 AND wt.ABSENT_FLAG=2   THEN 0.5
							  WHEN wt.WORK_FLAG=2 AND wt.ABSENT_FLAG=0   THEN 1
          				      ELSE 0 END) KHAD,
					
					  (CASE WHEN wt.ABSENT_FLAG=30 OR wt.ABSENT_FLAG=12  THEN 1 ELSE 
					  CASE WHEN wt.ABSENT_FLAG=0 THEN 0 ELSE
						  CASE WHEN 
						  (select sum(1) from PER_ABSENTHIS h,PER_ABSENTTYPE t
							where h.PER_ID=wt.PER_ID and h.AB_CODE=t.AB_CODE and t.AB_COUNT=1 and h.ABS_ENDPERIOD=3
								  AND cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19)) between h.ABS_STARTDATE and h.ABS_ENDDATE
						  )>0 THEN 1
						  ELSE  0.5 END END END)  as LA,
						CASE WHEN wt.ABSENT_FLAG=10 OR wt.ABSENT_FLAG=12 OR wt.WORK_REMARK is not null  THEN 0 ELSE
					  	trunc((wt.apv_enttime - 
        							case when cl.WorkCycle_Type=1 then
        							  TRUNC(wt.WORK_DATE)+cl.NextDay_OnTime+((to_number(substr(cl.WC_Start,1,2))*60)+to_number(substr(cl.WC_Start,3,2)))/1440
        							 else   
        							  TRUNC(wt.WORK_DATE)+((to_number(substr(cl.On_Time,1,2))*60)+to_number(substr(cl.On_Time,3,2)))/1440
        							end
        								 )*1440) END  AS SAY,
					  (select NUM_HRS from TA_PER_OT 
						  where PER_ID=wt.PER_ID AND OT_DATE=TO_CHAR(wt.WORK_DATE,'yyyy-mm-dd') 
								AND HOLYDAY_FLAG=0 and APPROVE_FLAG=1 ) AS OTWORKDAY,
					  (select NUM_HRS from TA_PER_OT 
						  where PER_ID=wt.PER_ID AND OT_DATE=TO_CHAR(wt.WORK_DATE,'yyyy-mm-dd') 
								AND HOLYDAY_FLAG=1 and APPROVE_FLAG=1 ) AS OTHOLIDAY,
					  case when (wt.HOLIDAY_FLAG=3 and hl.HOL_NAME is null) or wt.HOLIDAY_FLAG=1 then 'วันหยุดประจำสัปดาห์' else hl.HOL_NAME end REMARK, 
					  a.PER_ID
					
					from PER_PERSONAL a
					 LEFT JOIN PER_WORK_TIME_ALL wt  ON(a.PER_ID=wt.PER_ID)
					 LEFT JOIN PER_WORK_CYCLE cl on(cl.WC_CODE=wt.WC_CODE)
					 LEFT JOIN PER_PRENAME PN ON(PN.PN_CODE=a.PN_CODE)
					 LEFT JOIN PER_HOLIDAY hl on (SUBSTR(hl.HOL_DATE,1,10)=to_char(wt.WORK_DATE,'YYYY-MM-DD'))
					 LEFT JOIN $position_table b ON($position_join)
					 LEFT JOIN PER_ORG  orggod ON($position_join_god)
					 LEFT JOIN $Frm_line LIN ON($ON_code=$line_code)
					 left join PER_TIME_ATTENDANCE pt on (pt.PER_ID=wt.PER_ID and (pt.TIME_STAMP=wt.SCAN_ENTTIME or pt.TIME_ADJUST=wt.SCAN_ENTTIME))
          			 left join PER_TIME_ATT pta on (pta.TA_CODE=pt.TA_CODE)
          			 left join PER_WORK_LATE pwl on (pwl.WL_CODE=pta.WL_CODE and pwl.WC_CODE=wt.WC_CODE and pwl.WORK_DATE=to_char(wt.WORK_DATE,'YYYY-MM-DD'))
					 $conTPER_ORG
					 $Frm_order_by
					 WHERE $con_per_status
								AND wt.PER_ID IS NOT NULL
								$CON_PER_AUDIT_FLAG
								$conditionDEPARTMENT 
								$search_condition
								$PERID_VAL
								$POS_NO_VAL1
								$PER_NAME_VAL
								$PER_SURNAME_VAL
								$LEVEL_NO_VAL
					
					ORDER BY e.ORG_SEQ_NO,e.ORG_NAME,
							 c.ORG_SEQ_NO,c.ORG_NAME,
							 orggod.ORG_NAME,
							 
							 $line_name
							 ,a.PER_NAME||a.PER_SURNAME,wt.work_date
		 ";
	}
	
	/* เอาไว้ก่อน
	
	trunc((wt.apv_enttime - (case when pwl.Late_Time is not null then wt.WORK_DATE+((to_number(substr(pwl.Late_Time,1,2))*60)+
                                    to_number(substr(pwl.Late_Time,3,2)))/1440 
						  else (
							case when cl.WorkCycle_Type=1 then
							  TRUNC(wt.WORK_DATE)+cl.NextDay_OnTime+((to_number(substr(cl.WC_Start,1,2))*60)+to_number(substr(cl.WC_Start,3,2)))/1440
							 else   
							  TRUNC(wt.WORK_DATE)+((to_number(substr(cl.On_Time,1,2))*60)+to_number(substr(cl.On_Time,3,2)))/1440
							end
								)
						  end) )*1440)  AS SAY,
	
	*/
	
//					 			AND wt.PER_ID NOT IN(select PER_ID FROM TA_SET_EXCEPTPER WHERE CANCEL_FLAG=1)
// echo "<pre>\n";
//  echo $cmd;
//   die();
	//echo $cmd;
	$db_dpis->send_cmd_fast($cmd);
	//echo "<pre>\n";
	//$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	$data_rowP = 0;
	$chkKONG = "";
	while($data = $db_dpis->get_array_array()){		
		if($chkKONG != $data[KONG]){
			$chkKONG = $data[KONG];
			
			$arr_content[$data_count][type] = "ORG";
			$arr_content[$data_count][KONG] = $data[KONG];

			$data_row = 0;
			$data_count++;
		} // end if
		
		$data_row++;

		$arr_content[$data_count][type] = "CONTENT";
		

		$arr_content[$data_count][ORDER] = $data_row; 
		$arr_content[$data_count][name] = $data[PER_CARDNO]." ".$data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME]." ". $data[PL_NAME]." ". $data[KONG_GOD];
		$arr_content[$data_count][WORK_DATE] = substr($data[WORK_DATE],8,2)."/".substr($data[WORK_DATE],5,2)."/".(substr($data[WORK_DATE],0,4)+543);
		$arr_content[$data_count][WC_CODE] = (($NUMBER_DISPLAY==2)?convert2thaidigit($data[WC_CODE]):$data[WC_CODE]);		
		$arr_content[$data_count][APV_ENTTIME] = $data[APV_ENTTIME]=="" && $data[APV_EXITTIME]==""? "" :$data[APV_ENTTIME]."-".$data[APV_EXITTIME];	
		
		if($data[REMARK]){$KHAD="";}else{$KHAD=$data[KHAD];}
		if($data[REMARK] && $data[LA] !='1'){$LA="";}else{$LA=$data[LA];}
		$arr_content[$data_count][KHAD] = $KHAD<=0 ? '':round($KHAD,2);	
		$arr_content[$data_count][LA] = $LA<=0 ? '':round($LA,2);	
		$arr_content[$data_count][SAY] = $data[SAY]<=0 || $data[REMARK]!=""? '':round($data[SAY],2);	
		$arr_content[$data_count][OTWORKDAY] = $data[OTWORKDAY]<=0 ? '':round($data[OTWORKDAY],2); 	
		$arr_content[$data_count][OTHOLIDAY] = $data[OTHOLIDAY]<=0 ? '':round($data[OTHOLIDAY],2);
		$arr_content[$data_count][REMARK] = $data[REMARK];

		$data_count++;
	} // end while
	
	//echo "<pre>"; print_r($arr_content); echo "</pre>";
	$count_data = $db_dpis->send_cmd($cmd);
	if($count_data){
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);

		$pdf->AutoPageBreak = false; 
//		echo "$head_text1<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", $CH_PRINT_SIZE, "b", "000", "EEEEFF", $COLUMN_FORMAT, $col_function, true);
		
		if (!$result) echo "****** error ****** on open table for $table<br>";
		$footKONG = "";
		$CurName = '';
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$KONG = $arr_content[$data_count][KONG];
			if($REPORT_ORDER == "ORG"){

				$pdf->report_title = "$DEPARTMENT_NAME||รายงานการมาปฏิบัติราชการของ$PERSON_TYPE[$search_per_type]||ตั้งแต่วันที่ $show_date";
				$pdf->company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1 || $PER_AUDIT_FLAG==1 ?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$KONG"."$list_type_texts";
				$pdf->SetFont($font,'',$CH_PRINT_SIZE);
				$pdf->AddPage();
	//				print_header();
				$pdf->print_tab_header();
				
			}elseif($REPORT_ORDER == "CONTENT"){
				
				$arr_data = (array) null;

				$arr_data[] = "";
				$arr_data[] = $arr_content[$data_count][WORK_DATE];
				$arr_data[] = $arr_content[$data_count][WC_CODE];
				$arr_data[] = $arr_content[$data_count][APV_ENTTIME];
				$arr_data[] = $arr_content[$data_count][KHAD];
				$arr_data[] = $arr_content[$data_count][LA];
				$arr_data[] = $arr_content[$data_count][SAY];
				$arr_data[] = $arr_content[$data_count][OTWORKDAY];
				$arr_data[] = $arr_content[$data_count][OTHOLIDAY];
				$arr_data[] = $arr_content[$data_count][REMARK];
				
				/**/
				$arr_data2 = (array) null;
				$data_align2 = array('L');
				/**/
				if($CurName!=$arr_content[$data_count][name]){
					$data_rowP++;
					for($idx=0;$idx<=10;$idx++){
					     $arr_data2[] = '<**1**>'.$data_rowP."/".$arr_content[$data_count][name];
					}
					
					$result = $pdf->add_data_tab($arr_data2, 7, "TRHBL", $data_align2, "", $CH_PRINT_SIZE, "", "000000", "");		//TRHBL<br>
				}
				
				
				
				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", $CH_PRINT_SIZE, "", "000000", "");		//TRHBL

				$CurName=$arr_content[$data_count][name];
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				
				
			} // end if			
		} // end for
		
	}else{
			$pdf->report_title = "$DEPARTMENT_NAME||รายงานการมาปฏิบัติราชการของ$PERSON_TYPE[$search_per_type]||ตั้งแต่วันที่ $show_date";
			$pdf->company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1 || $PER_AUDIT_FLAG==1 ?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text"."$list_type_texts";
			$pdf->SetFont($font,'',$CH_PRINT_SIZE);
			$pdf->AddPage();
			$pdf->print_tab_header();
			$pdf->Text(140,40,"ไม่พบข้อมูล");
	} // end if
	

	$pdf->close_tab(""); 

	$pdf->close();
	$fname = "R1206.pdf";
	$pdf->Output($fname,'D');	
	//$pdf->Output();
?>