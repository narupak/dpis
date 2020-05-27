<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");
	

	ini_set("max_execution_time", $max_execution_time);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	
	
	/*เงื่อนไขการออกรายงาน*/
	/*ประเภทบุคลากร*/
	$con_per_type = "";
	$con_per_type = " and a.PER_TYPE = $search_per_type ";
	
	/*ตามโครงสร้าง*/
	if($search_per_type==1){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_code = "b.PL_CODE";
		$position_no_name = "b.POS_NO_NAME";
		$position_no = "b.POS_NO";
		$line_search_code = trim($search_pl_code);
		$line_search_name = trim($search_pl_name);		
		$line_title = " สายงาน";
		
		$Frm_line = " PER_LINE ";
		$ON_code = "LIN.PL_CODE";
		$line_seq = "LIN.PL_SEQ_NO";
		

	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_code = "b.PN_CODE";
		$position_no_name = "b.POEM_NO_NAME";
		$position_no = "b.POEM_NO";
		$line_search_code = trim($search_pn_code);
		$line_search_name = trim($search_pn_name);		
		$line_title = " ชื่อตำแหน่ง";
		
		$Frm_line = " PER_POS_NAME ";
		$ON_code = "LIN.PN_CODE";
		$line_seq = "LIN.PN_SEQ_NO";

	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_code = "b.EP_CODE";
		$position_no_name = "b.POEMS_NO_NAME";
		$position_no = "b.POEMS_NO";
		$line_search_code = trim($search_ep_code);
		$line_search_name = trim($search_ep_name);		
		$line_title = " ชื่อตำแหน่ง";
		
		$Frm_line = " PER_EMPSER_POS_NAME ";
		$ON_code = "LIN.EP_CODE";
		$line_seq = "LIN.EP_SEQ_NO";

	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_code = "b.TP_CODE";
		$position_no_name = "b.POT_NO_NAME";
		$position_no = "b.POT_NO";
		$line_search_code = trim($search_tp_code);
		$line_search_name = trim($search_tp_name);		
		$line_title = " ชื่อตำแหน่ง";
		
		$Frm_line = " PER_TEMP_POS_NAME ";
		$ON_code = "LIN.TP_CODE";
		$line_seq = "LIN.TP_SEQ_NO";
	} // end if
	
	if($select_org_structure==0) { 
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
	$search_date1 = date("Y-m-d");
	$arr_tempBgn = explode("-", $search_date1);
	$arr_tempEnd = explode("-", $search_date1);
	$show_date = ($arr_tempBgn[0] + 0) ." ". $month_full[($arr_tempBgn[1] + 0)][TH] ." ". $arr_tempBgn[2] ." ถึงวันที่ ".($arr_tempEnd[0] + 0) ." ". $month_full[($arr_tempEnd[1] + 0)][TH] ." ". $arr_tempEnd[2];



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
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$conditionDEPARTMENT = " AND (a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			//$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$conditionDEPARTMENT = " AND (trim(c.PV_CODE) = '$PROVINCE_CODE')";
			//$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	
	
	/*สำนัก/กอง*/
	
	if(in_array("PER_ORG", $list_type)){
		
		if($select_org_structure==0) {
			if(trim($search_org_id)){ 
				$search_condition .= " AND  (b.ORG_ID = $search_org_id) ";
				//$list_type_text .= " - $search_org_name"; ไม่ต้องแสดงมันจะซ้ำกัน
			} // end if
			if(trim($search_org_id_1)){ 
				$search_condition .= " AND  (b.ORG_ID_1 = $search_org_id_1)";
				$list_type_text .= " - $search_org_name_1";
			} // end if
			if(trim($search_org_id_2)){ 
				 $search_condition .= " AND  (b.ORG_ID_2 = $search_org_id_2)";
				$list_type_text .= " - $search_org_name_2";
			} // end if
		}else if($select_org_structure==1) {
			if(trim($search_org_ass_id)){ 
				 $search_condition .= " AND  (a.ORG_ID = $search_org_ass_id)";
				$list_type_text .= " - $search_org_ass_name";
			} // end if
			if(trim($search_org_ass_id_1)){ 
				$search_condition .= " AND  (a.ORG_ID =  $search_org_ass_id_1)";
				$list_type_text .= " - $search_org_ass_name_1";
			} // end if
			if(trim($search_org_ass_id_2)){ 
				$search_condition .= " AND  (a.ORG_ID =  $search_org_ass_id_2)";
				$list_type_text .= " - $search_org_ass_name_2";
			} // end if
		}
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
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "รายงานการมาปฏิบัติราชการของบุคลากรประจำวัน แบบที่ 1";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "es_rpt_R1203_01";
	include ("es_rpt_R1203_01_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
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
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);

	if($DPISDB=="oci8"){
		$Tmp_DateBgn=($search_year-543)."-".substr("0".$search_month , -2)."-01";
		$Tmp_DateEnd=($search_year-543)."-".substr("0".$search_month , -2)."-".date("t", strtotime($search_year-543 . "-" . $search_month . "-01"));
		
		$ConREMARK =" case when sum(decode( AB_CODE, '02        ', ABS_DAY )) is null
					        then
					        '' else
									 (select ABS_REMARK FROM PER_ABSENTHIS WHERE
                    ('$Tmp_DateBgn' >= ABS_STARTDATE AND '$Tmp_DateBgn' <= ABS_ENDDATE OR
							'$Tmp_DateEnd' >= ABS_STARTDATE AND '$Tmp_DateEnd' <= ABS_ENDDATE OR
							ABS_STARTDATE >= '$Tmp_DateBgn' AND ABS_STARTDATE <= '".$Tmp_DateEnd."9'
					OR
							ABS_ENDDATE >= '$Tmp_DateBgn' AND ABS_ENDDATE <= '".$Tmp_DateEnd."9'
						   )
                    AND PER_ID=x.PER_ID AND AB_CODE='02        ' AND ROWNUM =1 ) end  ";
					
		$Tmp_RE_OTH = "(select 
               
                case when (select AB_COUNT from PER_ABSENTTYPE where AB_CODE='02        ' AND ROWNUM =1 ) = 1  then 
                    case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then ',01ช'
                         when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then ',01บ'
                         when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then ',01'
                    else
                      case when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then ',01ช'
                           when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then ',01บ'
                           when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then ',01'
                           when '$Tmp_DateBgn' > aa.ABS_STARTDATE AND '$Tmp_DateBgn' < aa.ABS_ENDDATE  then ''
                           when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then '-01ช'
                           when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then '-01บ'
                           when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then '-01'
                      end
                    end 
                 
                else

                  case when (select HOL_DATE from PER_HOLIDAY where HOL_DATE='$Tmp_DateBgn' AND ROWNUM =1 ) is null then
                      case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then ',01ช'
                         when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then ',01บ'
                         when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then ',01'
                      else
                        case when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then ',01ช'
                             when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then ',01บ'
                             when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then ',01'
                             when '$Tmp_DateBgn' > aa.ABS_STARTDATE AND '$Tmp_DateBgn' < aa.ABS_ENDDATE  then ''
                             when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then '-01ช'
                             when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then '-01บ'
                             when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then '-01'
                        end
                      end
                  else
                    ''
                  end
                  
                end
                
                  
                FROM PER_ABSENTHIS aa 
                WHERE aa.AB_CODE = '02        '
                     AND aa.PER_ID=x.PER_ID AND ('$Tmp_DateBgn' BETWEEN aa.ABS_STARTDATE and aa.ABS_ENDDATE) 
                     AND ROWNUM =1 )
			
		
		";
		
		$Tmp_SUM_OTH =" 
				      
				      NVL((select   
                
                      case when (select AB_COUNT from PER_ABSENTTYPE where AB_CODE='02        ' ) = 1  then 
                       NVL(
                          case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                               when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                               when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then 1
                          else
                            case when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                 when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                 when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then 1
                                 when '$Tmp_DateBgn' > aa.ABS_STARTDATE AND '$Tmp_DateBgn' < aa.ABS_ENDDATE  then 1
                                 when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then 0.5
                                 when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then 0.5
                                 when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then 1
                            end
                          end 
                            ,0)

                      else
                          case when (select HOL_DATE from PER_HOLIDAY where HOL_DATE='$Tmp_DateBgn' AND ROWNUM =1 ) is null then 
                              NVL(
                                case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                     when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                     when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then 1
                                else
                                  case when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                       when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                       when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then 1
                                       when '$Tmp_DateBgn' > aa.ABS_STARTDATE AND '$Tmp_DateBgn' < aa.ABS_ENDDATE  then 1
                                       when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then 0.5
                                       when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then 0.5
                                       when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then 1
                                  end
                                end 
                                  ,0)
                          else
                            0
                          end
                      end
                

                FROM PER_ABSENTHIS aa 
                WHERE aa.AB_CODE='02        ' 
                     AND aa.PER_ID=x.PER_ID AND ('$Tmp_DateBgn' BETWEEN aa.ABS_STARTDATE and aa.ABS_ENDDATE) 
                     AND ROWNUM =1 ),0)";
					
		$cmd = "SELECT AB_CODE FROM PER_ABSENTTYPE WHERE AB_CODE not in ('01        ','03        ','04        ', '10        ', '02        ') ";
		$count_page_data = $db_dpis->send_cmd($cmd);
		$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
			
			/*หมายเหตุ*/
			$ConREMARK .= " || case when sum(decode( AB_CODE, '".$data[AB_CODE]."', ABS_DAY )) is null
					        then
					        '' else
									 (select ABS_REMARK FROM PER_ABSENTHIS WHERE
                    ('$Tmp_DateBgn' >= ABS_STARTDATE AND '$Tmp_DateBgn' <= ABS_ENDDATE OR
							'$Tmp_DateEnd' >= ABS_STARTDATE AND '$Tmp_DateEnd' <= ABS_ENDDATE OR
							ABS_STARTDATE >= '$Tmp_DateBgn' AND ABS_STARTDATE <= '".$Tmp_DateEnd."9'
					OR
							ABS_ENDDATE >= '$Tmp_DateBgn' AND ABS_ENDDATE <= '".$Tmp_DateEnd."9'
						   )
                    AND PER_ID=x.PER_ID AND AB_CODE='".$data[AB_CODE]."' AND ROWNUM =1 ) end  ";
					
			/*ลาอื่นๆ*/
			$Tmp_RE_OTH .= " || (select 
				   
					case when (select AB_COUNT from PER_ABSENTTYPE where AB_CODE='".$data[AB_CODE]."' AND ROWNUM =1 ) = 1  then 
						case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then ',01ช'
							 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then ',01บ'
							 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then ',01'
						else
						  case when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then ',01ช'
							   when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then ',01บ'
							   when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then ',01'
							   when '$Tmp_DateBgn' > aa.ABS_STARTDATE AND '$Tmp_DateBgn' < aa.ABS_ENDDATE  then ''
							   when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then '-01ช'
							   when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then '-01บ'
							   when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then '-01'
						  end
						end 
					 
					else
	
					  case when (select HOL_DATE from PER_HOLIDAY where HOL_DATE='$Tmp_DateBgn' AND ROWNUM =1 ) is null then
						  case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then ',01ช'
							 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then ',01บ'
							 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then ',01'
						  else
							case when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then ',01ช'
								 when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then ',01บ'
								 when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then ',01'
								 when '$Tmp_DateBgn' > aa.ABS_STARTDATE AND '$Tmp_DateBgn' < aa.ABS_ENDDATE  then ''
								 when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then '-01ช'
								 when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then '-01บ'
								 when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then '-01'
							end
						  end
					  else
						''
					  end
					  
					end
					
					  
					FROM PER_ABSENTHIS aa 
					WHERE aa.AB_CODE = '".$data[AB_CODE]."'
						 AND aa.PER_ID=x.PER_ID AND ('$Tmp_DateBgn' BETWEEN aa.ABS_STARTDATE and aa.ABS_ENDDATE) 
						 AND ROWNUM =1 ) ";
						 
				$Tmp_SUM_OTH .=" 
				      
				      NVL((select   
                
                      case when (select AB_COUNT from PER_ABSENTTYPE where AB_CODE='".$data[AB_CODE]."' ) = 1  then 
                       NVL(
                          case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                               when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                               when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then 1
                          else
                            case when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                 when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                 when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then 1
                                 when '$Tmp_DateBgn' > aa.ABS_STARTDATE AND '$Tmp_DateBgn' < aa.ABS_ENDDATE  then 1
                                 when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then 0.5
                                 when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then 0.5
                                 when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then 1
                            end
                          end 
                            ,0)

                      else
                          case when (select HOL_DATE from PER_HOLIDAY where HOL_DATE='$Tmp_DateBgn' AND ROWNUM =1 ) is null then 
                              NVL(
                                case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                     when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                     when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then 1
                                else
                                  case when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                       when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                       when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then 1
                                       when '$Tmp_DateBgn' > aa.ABS_STARTDATE AND '$Tmp_DateBgn' < aa.ABS_ENDDATE  then 1
                                       when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then 0.5
                                       when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then 0.5
                                       when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then 1
                                  end
                                end 
                                  ,0)
                          else
                            0
                          end
                      end
                

                FROM PER_ABSENTHIS aa 
                WHERE aa.AB_CODE='".$data[AB_CODE]."' 
                     AND aa.PER_ID=x.PER_ID AND ('$Tmp_DateBgn' BETWEEN aa.ABS_STARTDATE and aa.ABS_ENDDATE) 
                     AND ROWNUM =1 ),0)";

		}
		
		/******************************/
		
		/* ป่วย*/
		$Tmp_RE_SICK = " (select 
               
                case when (select AB_COUNT from PER_ABSENTTYPE where AB_CODE='01        ' ) = 1  then
                    case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then ',01ช'
                         when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then ',01บ'
                         when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then ',01'
                    else
                      case when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then ',01ช'
                           when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then ',01บ'
                           when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then ',01'
                           when '$Tmp_DateBgn' > aa.ABS_STARTDATE AND '$Tmp_DateBgn' < aa.ABS_ENDDATE  then ',01'
                           when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then ',01ช'
                           when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then ',01บ'
                           when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then ',01'
                      end
                    end 
                 
                else

                  case when (select HOL_DATE from PER_HOLIDAY where HOL_DATE='$Tmp_DateBgn' AND ROWNUM =1 ) is null then 
                      case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then ',01ช'
                         when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then ',01บ'
                         when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then ',01'
                      else
                        case when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then ',01ช'
                             when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then ',01บ'
                             when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then ',01'
                             when '$Tmp_DateBgn' > aa.ABS_STARTDATE AND '$Tmp_DateBgn' < aa.ABS_ENDDATE  then ',01'
                             when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then ',01ช'
                             when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then ',01บ'
                             when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then ',01'
                        end
                      end
                  else
                    ''
                  end
                  
                end
                
                  
                FROM PER_ABSENTHIS aa 
                WHERE aa.AB_CODE='01        ' 
                     AND aa.PER_ID=x.PER_ID AND ('$Tmp_DateBgn' BETWEEN aa.ABS_STARTDATE and aa.ABS_ENDDATE) 
                     AND ROWNUM =1 ) ";
					 
		/* กิจ*/
		$Tmp_RE_LAKIT = " (select 
               
                case when (select AB_COUNT from PER_ABSENTTYPE where AB_CODE='03        ' ) = 1  then
                    case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then ',01ช'
                         when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then ',01บ'
                         when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then ',01'
                    else
                      case when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then ',01ช'
                           when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then ',01บ'
                           when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then ',01'
                           when '$Tmp_DateBgn' > aa.ABS_STARTDATE AND '$Tmp_DateBgn' < aa.ABS_ENDDATE  then ',01'
                           when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then ',01ช'
                           when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then ',01บ'
                           when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then ',01'
                      end
                    end 
                 
                else

                  case when (select HOL_DATE from PER_HOLIDAY where HOL_DATE='$Tmp_DateBgn' AND ROWNUM =1 ) is null then 
                      case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then ',01ช'
                         when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then ',01บ'
                         when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then ',01'
                      else
                        case when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then ',01ช'
                             when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then ',01บ'
                             when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then ',01'
                             when '$Tmp_DateBgn' > aa.ABS_STARTDATE AND '$Tmp_DateBgn' < aa.ABS_ENDDATE  then ',01'
                             when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then ',01ช'
                             when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then ',01บ'
                             when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then ',01'
                        end
                      end
                  else
                    ''
                  end
                  
                end
                
                  
                FROM PER_ABSENTHIS aa 
                WHERE aa.AB_CODE='03        ' 
                     AND aa.PER_ID=x.PER_ID AND ('$Tmp_DateBgn' BETWEEN aa.ABS_STARTDATE and aa.ABS_ENDDATE) 
                     AND ROWNUM =1 ) ";
					 
		/* สาย*/
		$Tmp_RE_LATE = " (select 
               
                case when (select AB_COUNT from PER_ABSENTTYPE where AB_CODE='10        ' ) = 1  then
                    case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then ',01ช'
                         when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then ',01บ'
                         when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then ',01'
                    else
                      case when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then ',01ช'
                           when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then ',01บ'
                           when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then ',01'
                           when '$Tmp_DateBgn' > aa.ABS_STARTDATE AND '$Tmp_DateBgn' < aa.ABS_ENDDATE  then ',01'
                           when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then ',01ช'
                           when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then ',01บ'
                           when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then ',01'
                      end
                    end 
                 
                else

                  case when (select HOL_DATE from PER_HOLIDAY where HOL_DATE='$Tmp_DateBgn' AND ROWNUM =1 ) is null then 
                      case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then ',01ช'
                         when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then ',01บ'
                         when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then ',01'
                      else
                        case when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then ',01ช'
                             when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then ',01บ'
                             when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then ',01'
                             when '$Tmp_DateBgn' > aa.ABS_STARTDATE AND '$Tmp_DateBgn' < aa.ABS_ENDDATE  then ',01'
                             when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then ',01ช'
                             when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then ',01บ'
                             when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then ',01'
                        end
                      end
                  else
                    ''
                  end
                  
                end
                
                  
                FROM PER_ABSENTHIS aa 
                WHERE aa.AB_CODE='10        ' 
                     AND aa.PER_ID=x.PER_ID AND ('$Tmp_DateBgn' BETWEEN aa.ABS_STARTDATE and aa.ABS_ENDDATE) 
                     AND ROWNUM =1 ) ";
					 
		/* พักผ่อน*/
		$Tmp_RE_PAKPON = " (select 
               
                case when (select AB_COUNT from PER_ABSENTTYPE where AB_CODE='04        ' ) = 1  then
                    case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then ',01ช'
                         when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then ',01บ'
                         when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then ',01'
                    else
                      case when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then ',01ช'
                           when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then ',01บ'
                           when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then ',01'
                           when '$Tmp_DateBgn' > aa.ABS_STARTDATE AND '$Tmp_DateBgn' < aa.ABS_ENDDATE  then ',01'
                           when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then ',01ช'
                           when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then ',01บ'
                           when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then ',01'
                      end
                    end 
                 
                else

                  case when (select HOL_DATE from PER_HOLIDAY where HOL_DATE='$Tmp_DateBgn' AND ROWNUM =1 ) is null then 
                      case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then ',01ช'
                         when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then ',01บ'
                         when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then ',01'
                      else
                        case when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then ',01ช'
                             when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then ',01บ'
                             when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then ',01'
                             when '$Tmp_DateBgn' > aa.ABS_STARTDATE AND '$Tmp_DateBgn' < aa.ABS_ENDDATE  then ',01'
                             when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then ',01ช'
                             when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then ',01บ'
                             when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then ',01'
                        end
                      end
                  else
                    ''
                  end
                  
                end
                
                  
                FROM PER_ABSENTHIS aa 
                WHERE aa.AB_CODE='04        ' 
                     AND aa.PER_ID=x.PER_ID AND ('$Tmp_DateBgn' BETWEEN aa.ABS_STARTDATE and aa.ABS_ENDDATE) 
                     AND ROWNUM =1 ) ";
					 
		$TotalLoop = date("t", strtotime($search_year-543 . "-" . $search_month . "-01"));
		$Tmp_LoopBgn=($search_year-543)."-".substr("0".$search_month , -2);
		
		$Tmp_SUM_SICK =" 
				      
				      NVL((select   
                
                      case when (select AB_COUNT from PER_ABSENTTYPE where AB_CODE='01        ' ) = 1  then 
                       NVL(
                          case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                               when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                               when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then 1
                          else
                            case when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                 when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                 when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then 1
                                 when '$Tmp_DateBgn' > aa.ABS_STARTDATE AND '$Tmp_DateBgn' < aa.ABS_ENDDATE  then 1
                                 when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then 0.5
                                 when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then 0.5
                                 when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then 1
                            end
                          end 
                            ,0)

                      else
                          case when (select HOL_DATE from PER_HOLIDAY where HOL_DATE='$Tmp_DateBgn' AND ROWNUM =1 ) is null then 
                              NVL(
                                case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                     when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                     when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then 1
                                else
                                  case when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                       when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                       when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then 1
                                       when '$Tmp_DateBgn' > aa.ABS_STARTDATE AND '$Tmp_DateBgn' < aa.ABS_ENDDATE  then 1
                                       when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then 0.5
                                       when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then 0.5
                                       when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then 1
                                  end
                                end 
                                  ,0)
                          else
                            0
                          end
                      end
                

                FROM PER_ABSENTHIS aa 
                WHERE aa.AB_CODE='01        ' 
                     AND aa.PER_ID=x.PER_ID AND ('$Tmp_DateBgn' BETWEEN aa.ABS_STARTDATE and aa.ABS_ENDDATE) 
                     AND ROWNUM =1 ),0)";
					 
					 
		$Tmp_SUM_LAKIT =" 
				      
				      NVL((select   
                
                      case when (select AB_COUNT from PER_ABSENTTYPE where AB_CODE='03        ' ) = 1  then 
                       NVL(
                          case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                               when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                               when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then 1
                          else
                            case when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                 when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                 when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then 1
                                 when '$Tmp_DateBgn' > aa.ABS_STARTDATE AND '$Tmp_DateBgn' < aa.ABS_ENDDATE  then 1
                                 when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then 0.5
                                 when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then 0.5
                                 when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then 1
                            end
                          end 
                            ,0)

                      else
                          case when (select HOL_DATE from PER_HOLIDAY where HOL_DATE='$Tmp_DateBgn' AND ROWNUM =1 ) is null then 
                              NVL(
                                case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                     when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                     when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then 1
                                else
                                  case when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                       when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                       when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then 1
                                       when '$Tmp_DateBgn' > aa.ABS_STARTDATE AND '$Tmp_DateBgn' < aa.ABS_ENDDATE  then 1
                                       when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then 0.5
                                       when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then 0.5
                                       when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then 1
                                  end
                                end 
                                  ,0)
                          else
                            0
                          end
                      end
                

                FROM PER_ABSENTHIS aa 
                WHERE aa.AB_CODE='03        ' 
                     AND aa.PER_ID=x.PER_ID AND ('$Tmp_DateBgn' BETWEEN aa.ABS_STARTDATE and aa.ABS_ENDDATE) 
                     AND ROWNUM =1 ),0)";
					 
		$Tmp_SUM_LATE =" 
				      
				      NVL((select   
                
                      case when (select AB_COUNT from PER_ABSENTTYPE where AB_CODE='10        ' ) = 1  then 
                       NVL(
                          case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                               when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                               when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then 1
                          else
                            case when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                 when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                 when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then 1
                                 when '$Tmp_DateBgn' > aa.ABS_STARTDATE AND '$Tmp_DateBgn' < aa.ABS_ENDDATE  then 1
                                 when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then 0.5
                                 when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then 0.5
                                 when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then 1
                            end
                          end 
                            ,0)

                      else
                          case when (select HOL_DATE from PER_HOLIDAY where HOL_DATE='$Tmp_DateBgn' AND ROWNUM =1 ) is null then 
                              NVL(
                                case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                     when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                     when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then 1
                                else
                                  case when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                       when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                       when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then 1
                                       when '$Tmp_DateBgn' > aa.ABS_STARTDATE AND '$Tmp_DateBgn' < aa.ABS_ENDDATE  then 1
                                       when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then 0.5
                                       when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then 0.5
                                       when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then 1
                                  end
                                end 
                                  ,0)
                          else
                            0
                          end
                      end
                

                FROM PER_ABSENTHIS aa 
                WHERE aa.AB_CODE='10        ' 
                     AND aa.PER_ID=x.PER_ID AND ('$Tmp_DateBgn' BETWEEN aa.ABS_STARTDATE and aa.ABS_ENDDATE) 
                     AND ROWNUM =1 ),0)";
					 
		$Tmp_SUM_PAKPON =" 
				      
				      NVL((select   
                
                      case when (select AB_COUNT from PER_ABSENTTYPE where AB_CODE='04        ' ) = 1  then 
                       NVL(
                          case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                               when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                               when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then 1
                          else
                            case when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                 when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                 when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then 1
                                 when '$Tmp_DateBgn' > aa.ABS_STARTDATE AND '$Tmp_DateBgn' < aa.ABS_ENDDATE  then 1
                                 when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then 0.5
                                 when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then 0.5
                                 when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then 1
                            end
                          end 
                            ,0)

                      else
                          case when (select HOL_DATE from PER_HOLIDAY where HOL_DATE='$Tmp_DateBgn' AND ROWNUM =1 ) is null then 
                              NVL(
                                case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                     when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                     when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then 1
                                else
                                  case when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                       when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                       when '$Tmp_DateBgn' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then 1
                                       when '$Tmp_DateBgn' > aa.ABS_STARTDATE AND '$Tmp_DateBgn' < aa.ABS_ENDDATE  then 1
                                       when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then 0.5
                                       when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then 0.5
                                       when '$Tmp_DateBgn' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then 1
                                  end
                                end 
                                  ,0)
                          else
                            0
                          end
                      end
                

                FROM PER_ABSENTHIS aa 
                WHERE aa.AB_CODE='04        ' 
                     AND aa.PER_ID=x.PER_ID AND ('$Tmp_DateBgn' BETWEEN aa.ABS_STARTDATE and aa.ABS_ENDDATE) 
                     AND ROWNUM =1 ),0)";

		for($i=2; $i<=date("t", strtotime($search_year-543 . "-" . $search_month . "-01")); $i++){
			$ii=substr("0".$i , -2);
			/* ป่วย*/
			$Tmp_RE_SICK .= " || (select 
				   
					case when (select AB_COUNT from PER_ABSENTTYPE where AB_CODE='01        ' ) = 1  then
						case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then ',".$ii."ช'
							 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then ',".$ii."บ'
							 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then ',".$ii."'
						else
						  case when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then ',".$ii."ช'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then ',".$ii."บ'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then ',".$ii."'
							   when '".$Tmp_LoopBgn."-".$ii."' > aa.ABS_STARTDATE AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then ',".$ii."'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then ',".$ii."ช'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then ',".$ii."บ'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then ',".$ii."'
							   when ".$ii." = $TotalLoop AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then '-".$TotalLoop."'
						  end
						end 
					 
					else
	
					  case when (select HOL_DATE from PER_HOLIDAY where HOL_DATE='".$Tmp_LoopBgn."-".$ii."' AND ROWNUM =1 ) is null then 
						  case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then ',".$ii."ช'
							 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then ',".$ii."บ'
							 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then ',".$ii."'
						  else
							case when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then ',".$ii."ช'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then ',".$ii."บ'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then ',".$ii."'
								 when '".$Tmp_LoopBgn."-".$ii."' > aa.ABS_STARTDATE AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then ',".$ii."'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then ',".$ii."ช'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then ',".$ii."บ'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then ',".$ii."'
								 when ".$ii." = $TotalLoop AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then '-".$TotalLoop."'
							end
						  end
					  else
						''
					  end
					  
					end
					
					  
					FROM PER_ABSENTHIS aa 
					WHERE aa.AB_CODE='01        ' 
						 AND aa.PER_ID=x.PER_ID AND ('".$Tmp_LoopBgn."-".$ii."' BETWEEN aa.ABS_STARTDATE and aa.ABS_ENDDATE) 
						 AND ROWNUM =1 ) ";
						 
				$Tmp_SUM_SICK .=" 
				      +
				      NVL((select   
                
                      case when (select AB_COUNT from PER_ABSENTTYPE where AB_CODE='01        ' ) = 1  then 
                       NVL(
                          case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                               when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                               when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then 1
                          else
                            case when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then 1
                                 when '".$Tmp_LoopBgn."-".$ii."' > aa.ABS_STARTDATE AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then 1
                                 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then 0.5
                                 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then 0.5
                                 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then 1
                            end
                          end 
                            ,0)

                      else
                          case when (select HOL_DATE from PER_HOLIDAY where HOL_DATE='".$Tmp_LoopBgn."-".$ii."' AND ROWNUM =1 ) is null then 
                              NVL(
                                case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                     when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                     when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then 1
                                else
                                  case when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                       when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                       when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then 1
                                       when '".$Tmp_LoopBgn."-".$ii."' > aa.ABS_STARTDATE AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then 1
                                       when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then 0.5
                                       when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then 0.5
                                       when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then 1
                                  end
                                end 
                                  ,0)
                          else
                            0
                          end
                      end
                

                FROM PER_ABSENTHIS aa 
                WHERE aa.AB_CODE='01        ' 
                     AND aa.PER_ID=x.PER_ID AND ('".$Tmp_LoopBgn."-".$ii."' BETWEEN aa.ABS_STARTDATE and aa.ABS_ENDDATE) 
                     AND ROWNUM =1 ),0)";
					  
				/*********/
				
				/* กิจ*/
				$Tmp_RE_LAKIT .= " || (select 
				   
					case when (select AB_COUNT from PER_ABSENTTYPE where AB_CODE='03        ' ) = 1  then
						case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then ',".$ii."ช'
							 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then ',".$ii."บ'
							 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then ',".$ii."'
						else
						  case when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then ',".$ii."ช'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then ',".$ii."บ'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then ',".$ii."'
							   when '".$Tmp_LoopBgn."-".$ii."' > aa.ABS_STARTDATE AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then ',".$ii."'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then ',".$ii."ช'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then ',".$ii."บ'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then ',".$ii."'
							   when ".$ii." = $TotalLoop AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then '-".$TotalLoop."'
						  end
						end 
					 
					else
	
					  case when (select HOL_DATE from PER_HOLIDAY where HOL_DATE='".$Tmp_LoopBgn."-".$ii."' AND ROWNUM =1 ) is null then 
						  case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then ',".$ii."ช'
							 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then ',".$ii."บ'
							 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then ',".$ii."'
						  else
							case when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then ',".$ii."ช'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then ',".$ii."บ'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then ',".$ii."'
								 when '".$Tmp_LoopBgn."-".$ii."' > aa.ABS_STARTDATE AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then ',".$ii."'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then ',".$ii."ช'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then ',".$ii."บ'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then ',".$ii."'
								 when ".$ii." = $TotalLoop AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then '-".$TotalLoop."'
							end
						  end
					  else
						''
					  end
					  
					end
					
					  
					FROM PER_ABSENTHIS aa 
					WHERE aa.AB_CODE='03        ' 
						 AND aa.PER_ID=x.PER_ID AND ('".$Tmp_LoopBgn."-".$ii."' BETWEEN aa.ABS_STARTDATE and aa.ABS_ENDDATE) 
						 AND ROWNUM =1 ) ";
						 
				$Tmp_SUM_LAKIT .=" 
				      +
				      NVL((select   
                
                      case when (select AB_COUNT from PER_ABSENTTYPE where AB_CODE='03        ' ) = 1  then 
                       NVL(
                          case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                               when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                               when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then 1
                          else
                            case when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then 1
                                 when '".$Tmp_LoopBgn."-".$ii."' > aa.ABS_STARTDATE AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then 1
                                 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then 0.5
                                 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then 0.5
                                 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then 1
                            end
                          end 
                            ,0)

                      else
                          case when (select HOL_DATE from PER_HOLIDAY where HOL_DATE='".$Tmp_LoopBgn."-".$ii."' AND ROWNUM =1 ) is null then 
                              NVL(
                                case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                     when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                     when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then 1
                                else
                                  case when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                       when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                       when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then 1
                                       when '".$Tmp_LoopBgn."-".$ii."' > aa.ABS_STARTDATE AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then 1
                                       when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then 0.5
                                       when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then 0.5
                                       when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then 1
                                  end
                                end 
                                  ,0)
                          else
                            0
                          end
                      end
                

                FROM PER_ABSENTHIS aa 
                WHERE aa.AB_CODE='03        ' 
                     AND aa.PER_ID=x.PER_ID AND ('".$Tmp_LoopBgn."-".$ii."' BETWEEN aa.ABS_STARTDATE and aa.ABS_ENDDATE) 
                     AND ROWNUM =1 ),0)";
					  
				/*********/
				
				/* สาย*/
				$Tmp_RE_LATE .= " || (select 
				   
					case when (select AB_COUNT from PER_ABSENTTYPE where AB_CODE='10        ' ) = 1  then
						case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then ',".$ii."ช'
							 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then ',".$ii."บ'
							 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then ',".$ii."'
						else
						  case when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then ',".$ii."ช'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then ',".$ii."บ'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then ',".$ii."'
							   when '".$Tmp_LoopBgn."-".$ii."' > aa.ABS_STARTDATE AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then ',".$ii."'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then ',".$ii."ช'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then ',".$ii."บ'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then ',".$ii."'
							   when ".$ii." = $TotalLoop AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then '-".$TotalLoop."'
						  end
						end 
					 
					else
	
					  case when (select HOL_DATE from PER_HOLIDAY where HOL_DATE='".$Tmp_LoopBgn."-".$ii."' AND ROWNUM =1 ) is null then 
						  case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then ',".$ii."ช'
							 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then ',".$ii."บ'
							 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then ',".$ii."'
						  else
							case when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then ',".$ii."ช'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then ',".$ii."บ'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then ',".$ii."'
								 when '".$Tmp_LoopBgn."-".$ii."' > aa.ABS_STARTDATE AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then ',".$ii."'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then ',".$ii."ช'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then ',".$ii."บ'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then ',".$ii."'
								 when ".$ii." = $TotalLoop AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then '-".$TotalLoop."'
							end
						  end
					  else
						''
					  end
					  
					end
					
					  
					FROM PER_ABSENTHIS aa 
					WHERE aa.AB_CODE='10        ' 
						 AND aa.PER_ID=x.PER_ID AND ('".$Tmp_LoopBgn."-".$ii."' BETWEEN aa.ABS_STARTDATE and aa.ABS_ENDDATE) 
						 AND ROWNUM =1 ) ";
						 
				$Tmp_SUM_LATE .=" 
				      +
				      NVL((select   
                
                      case when (select AB_COUNT from PER_ABSENTTYPE where AB_CODE='10        ' ) = 1  then 
                       NVL(
                          case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                               when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                               when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then 1
                          else
                            case when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then 1
                                 when '".$Tmp_LoopBgn."-".$ii."' > aa.ABS_STARTDATE AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then 1
                                 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then 0.5
                                 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then 0.5
                                 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then 1
                            end
                          end 
                            ,0)

                      else
                          case when (select HOL_DATE from PER_HOLIDAY where HOL_DATE='".$Tmp_LoopBgn."-".$ii."' AND ROWNUM =1 ) is null then 
                              NVL(
                                case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                     when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                     when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then 1
                                else
                                  case when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                       when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                       when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then 1
                                       when '".$Tmp_LoopBgn."-".$ii."' > aa.ABS_STARTDATE AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then 1
                                       when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then 0.5
                                       when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then 0.5
                                       when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then 1
                                  end
                                end 
                                  ,0)
                          else
                            0
                          end
                      end
                

                FROM PER_ABSENTHIS aa 
                WHERE aa.AB_CODE='10        ' 
                     AND aa.PER_ID=x.PER_ID AND ('".$Tmp_LoopBgn."-".$ii."' BETWEEN aa.ABS_STARTDATE and aa.ABS_ENDDATE) 
                     AND ROWNUM =1 ),0)";
					  
				/*********/
				
				/* พักผ่อน*/
				$Tmp_RE_PAKPON .= " || (select 
				   
					case when (select AB_COUNT from PER_ABSENTTYPE where AB_CODE='04        ' ) = 1  then
						case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then ',".$ii."ช'
							 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then ',".$ii."บ'
							 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then ',".$ii."'
						else
						  case when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then ',".$ii."ช'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then ',".$ii."บ'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then ',".$ii."'
							   when '".$Tmp_LoopBgn."-".$ii."' > aa.ABS_STARTDATE AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then ',".$ii."'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then ',".$ii."ช'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then ',".$ii."บ'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then ',".$ii."'
							   when ".$ii." = $TotalLoop AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then '-".$TotalLoop."'
						  end
						end 
					 
					else
	
					  case when (select HOL_DATE from PER_HOLIDAY where HOL_DATE='".$Tmp_LoopBgn."-".$ii."' AND ROWNUM =1 ) is null then 
						  case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then ',".$ii."ช'
							 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then ',".$ii."บ'
							 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then ',".$ii."'
						  else
							case when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then ',".$ii."ช'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then ',".$ii."บ'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then ',".$ii."'
								 when '".$Tmp_LoopBgn."-".$ii."' > aa.ABS_STARTDATE AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then ',".$ii."'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then ',".$ii."ช'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then ',".$ii."บ'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then ',".$ii."'
								 when ".$ii." = $TotalLoop AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then '-".$TotalLoop."'
							end
						  end
					  else
						''
					  end
					  
					end
					
					  
					FROM PER_ABSENTHIS aa 
					WHERE aa.AB_CODE='04        ' 
						 AND aa.PER_ID=x.PER_ID AND ('".$Tmp_LoopBgn."-".$ii."' BETWEEN aa.ABS_STARTDATE and aa.ABS_ENDDATE) 
						 AND ROWNUM =1 ) ";
						 
				$Tmp_SUM_PAKPON .=" 
				      +
				      NVL((select   
                
                      case when (select AB_COUNT from PER_ABSENTTYPE where AB_CODE='04        ' ) = 1  then 
                       NVL(
                          case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                               when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                               when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then 1
                          else
                            case when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then 1
                                 when '".$Tmp_LoopBgn."-".$ii."' > aa.ABS_STARTDATE AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then 1
                                 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then 0.5
                                 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then 0.5
                                 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then 1
								 
                            end
                          end 
                            ,0)

                      else
                          case when (select HOL_DATE from PER_HOLIDAY where HOL_DATE='".$Tmp_LoopBgn."-".$ii."' AND ROWNUM =1 ) is null then 
                              NVL(
                                case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                     when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                     when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then 1
                                else
                                  case when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then 0.5
                                       when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then 0.5
                                       when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then 1
                                       when '".$Tmp_LoopBgn."-".$ii."' > aa.ABS_STARTDATE AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then 1
                                       when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then 0.5
                                       when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then 0.5
                                       when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then 1
                                  end
                                end 
                                  ,0)
                          else
                            0
                          end
                      end
                

                FROM PER_ABSENTHIS aa 
                WHERE aa.AB_CODE='04        ' 
                     AND aa.PER_ID=x.PER_ID AND ('".$Tmp_LoopBgn."-".$ii."' BETWEEN aa.ABS_STARTDATE and aa.ABS_ENDDATE) 
                     AND ROWNUM =1 ),0)";
					  
				/*********/
				
				/*ลาอื่นๆ*/
				$cmd = "SELECT AB_CODE FROM PER_ABSENTTYPE WHERE AB_CODE not in ('01        ','03        ','04        ', '10        ') ";
				$count_page_data = $db_dpis->send_cmd($cmd);
				$db_dpis->show_error();
				while ($data = $db_dpis->get_array()) {
					
					$Tmp_RE_OTH .= " || (select 
				   
					case when (select AB_COUNT from PER_ABSENTTYPE where AB_CODE='".$data[AB_CODE]."' ) = 1  then
						case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then ',".$ii."ช'
							 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then ',".$ii."บ'
							 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then ',".$ii."'
						else
						  case when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then ',".$ii."ช'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then ',".$ii."บ'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then ',".$ii."'
							   when '".$Tmp_LoopBgn."-".$ii."' > aa.ABS_STARTDATE AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then ',".$ii."'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then ',".$ii."ช'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then ',".$ii."บ'
							   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then ',".$ii."'
							   when ".$ii." = $TotalLoop AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then '-".$TotalLoop."'
						  end
						end 
					 
					else
	
					  case when (select HOL_DATE from PER_HOLIDAY where HOL_DATE='".$Tmp_LoopBgn."-".$ii."' AND ROWNUM =1 ) is null then 
						  case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then ',".$ii."ช'
							 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then ',".$ii."บ'
							 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then ',".$ii."'
						  else
							case when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then ',".$ii."ช'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then ',".$ii."บ'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then ',".$ii."'
								 when '".$Tmp_LoopBgn."-".$ii."' > aa.ABS_STARTDATE AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then ',".$ii."'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then ',".$ii."ช'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then ',".$ii."บ'
								 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then ',".$ii."'
								 when ".$ii." = $TotalLoop AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then '-".$TotalLoop."'
							end
						  end
					  else
						''
					  end
					  
					end
					
					  
					FROM PER_ABSENTHIS aa 
					WHERE aa.AB_CODE='".$data[AB_CODE]."'
						 AND aa.PER_ID=x.PER_ID AND ('".$Tmp_LoopBgn."-".$ii."' BETWEEN aa.ABS_STARTDATE and aa.ABS_ENDDATE) 
						 AND ROWNUM =1 ) ";
						 
						 
					$Tmp_SUM_OTH .=" 
								  +
								  NVL((select   
							
								  case when (select AB_COUNT from PER_ABSENTTYPE where AB_CODE='".$data[AB_CODE]."' ) = 1  then 
								   NVL(
									  case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then 0.5
										   when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then 0.5
										   when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then 1
									  else
										case when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then 0.5
											 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then 0.5
											 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then 1
											 when '".$Tmp_LoopBgn."-".$ii."' > aa.ABS_STARTDATE AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then 1
											 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then 0.5
											 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then 0.5
											 when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then 1
										end
									  end 
										,0)
			
								  else
									  case when (select HOL_DATE from PER_HOLIDAY where HOL_DATE='".$Tmp_LoopBgn."-".$ii."' AND ROWNUM =1 ) is null then 
										  NVL(
											case when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=1  then 0.5
												 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=2  then 0.5
												 when aa.ABS_STARTDATE=aa.ABS_ENDDATE AND aa.ABS_STARTPERIOD=3  then 1
											else
											  case when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=1  then 0.5
												   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=2  then 0.5
												   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_STARTDATE AND aa.ABS_STARTPERIOD=3  then 1
												   when '".$Tmp_LoopBgn."-".$ii."' > aa.ABS_STARTDATE AND '".$Tmp_LoopBgn."-".$ii."' < aa.ABS_ENDDATE  then 1
												   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=1  then 0.5
												   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=2  then 0.5
												   when '".$Tmp_LoopBgn."-".$ii."' = aa.ABS_ENDDATE AND aa.ABS_ENDPERIOD=3  then 1
											  end
											end 
											  ,0)
									  else
										0
									  end
								  end
							
			
							FROM PER_ABSENTHIS aa 
							WHERE aa.AB_CODE='".$data[AB_CODE]."'
								 AND aa.PER_ID=x.PER_ID AND ('".$Tmp_LoopBgn."-".$ii."' BETWEEN aa.ABS_STARTDATE and aa.ABS_ENDDATE) 
								 AND ROWNUM =1 ),0)";
					
				}
			
			
			
			
		}
		
		$Con_RE_SICK=" (SUBSTR( ".$Tmp_RE_SICK." ,2) ) ";
		$Con_SUM_SICK=" ( ".$Tmp_SUM_SICK." ) ";
		
		$Con_RE_LAKIT=" (SUBSTR( ".$Tmp_RE_LAKIT." ,2) ) ";
		$Con_SUM_LAKIT=" ( ".$Tmp_SUM_LAKIT." ) ";
		
		$Con_RE_LATE=" (SUBSTR( ".$Tmp_RE_LATE." ,2) ) ";
		$Con_SUM_LATE=" ( ".$Tmp_SUM_LATE." ) ";
		
		$Con_RE_PAKPON=" (SUBSTR( ".$Tmp_RE_PAKPON." ,2) ) ";
		$Con_SUM_PAKPON=" ( ".$Tmp_SUM_PAKPON." ) ";
		
		$Con_RE_OTH=" (SUBSTR( ".$Tmp_RE_OTH." ,2) ) ";
		$Con_SUM_OTH=" ( ".$Tmp_SUM_OTH." ) ";
		/**************************/
		
		
		
		$cmd = " SELECT
					  e.ORG_NAME AS DEPARTMENT, c.ORG_NAME AS KONG,
					  PN.PN_NAME,a.PER_NAME,a.PER_SURNAME,
					  xx.*
					
					FROM(
					select PER_ID,
          			$Con_RE_SICK AS RE_SICK,
            	    $Con_SUM_SICK AS SUM_SICK,
                    count( decode( AB_CODE, '01        ', ABS_DAY ) ) CNT_SICK,
					$Con_RE_LAKIT AS RE_LAKIT,
					$Con_SUM_LAKIT AS SUM_LAKIT,
					count( decode( AB_CODE, '03        ', ABS_DAY ) ) CNT_LAKIT,
            		count( decode( AB_CODE, '01        ', ABS_DAY ) ) + 
					count( decode( AB_CODE, '03        ', ABS_DAY ) ) CNT_SICK_LAKIT,
					$Con_RE_LATE AS RE_LATE,
					$Con_SUM_LATE AS SUM_LATE,
					$Con_RE_PAKPON AS RE_PAKPON,
					$Con_SUM_PAKPON AS SUM_PAKPON,
				    $Con_RE_OTH AS RE_OTH,
					$Con_RE_OTH AS SUM_OTH,
					$ConREMARK AS REMARK
					from (
					select distinct
					a.PER_ID,
					b.AB_CODE, b.ABS_DAY,b.ABS_STARTDATE,b.ABS_ENDDATE
					from
						PER_PERSONAL a,
						 (select PER_ID,AB_CODE, ABS_DAY,ABS_STARTDATE,ABS_ENDDATE  from 
					PER_ABSENTHIS
						where
						   ('$Tmp_DateBgn' >= ABS_STARTDATE AND '$Tmp_DateBgn' <= ABS_ENDDATE OR
							'$Tmp_DateEnd' >= ABS_STARTDATE AND '$Tmp_DateEnd' <= ABS_ENDDATE OR
							ABS_STARTDATE >= '$Tmp_DateBgn' AND ABS_STARTDATE <= '".$Tmp_DateEnd."9'
					OR
							ABS_ENDDATE >= '$Tmp_DateBgn' AND ABS_ENDDATE <= '".$Tmp_DateEnd."9'
						   )
						 ) b
					where
						  (a.PER_STATUS = 1) 
						  $con_per_type
						  $conditionDEPARTMENT
					AND
						 a.PER_ID=b.PER_ID(+)
					) x
					
					group by PER_ID
					
					)xx
					 LEFT JOIN PER_PERSONAL a ON(a.PER_ID=xx.PER_ID)
					 LEFT JOIN PER_PRENAME PN ON(PN.PN_CODE=a.PN_CODE)
					 LEFT JOIN $position_table b ON($position_join)
					 $conTPER_ORG
					 $Frm_order_by
					 WHERE 1=1 
								 $search_condition
					
					ORDER BY e.ORG_SEQ_NO,e.ORG_NAME,
							 c.ORG_SEQ_NO,c.ORG_NAME 
							 $order_by
		 ";


	}

   //echo "<pre>\n";
	//echo $cmd; die();
	//$count_data = $db_dpis->send_cmd($cmd);
	//echo "<pre>\n";
	//$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	$chkKONG = "";
	while($data = $db_dpis->get_array()){		
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
		$arr_content[$data_count][name] = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$arr_content[$data_count][LSICK] = $data[LSICK];
		$arr_content[$data_count][LSICKCNT] = $data[LSICKCNT];		
		$arr_content[$data_count][LAKIT] = $data[LAKIT];	
		$arr_content[$data_count][LAKITCNT] = $data[LAKITCNT];	
		$arr_content[$data_count][LSICK_LAKIT] = $data[LSICK_LAKIT];	
		$arr_content[$data_count][LSICK_LAKITCNT] = $data[LSICK_LAKITCNT];	
		$arr_content[$data_count][LATE] = $data[LATE];	
		$arr_content[$data_count][PAKPON] = $data[PAKPON];
		$arr_content[$data_count][PAKPONCNT] = $data[PAKPONCNT];	
		$arr_content[$data_count][LAOTH] = $data[LAOTH];	
		$arr_content[$data_count][REMARK] = $data[REMARK];

		$data_count++;
	} // end while
	
	//echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);

		$pdf->AutoPageBreak = false; 
//		echo "$head_text1<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function, true);
		
		if (!$result) echo "****** error ****** on open table for $table<br>";
		$footKONG = "";
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$KONG = $arr_content[$data_count][KONG];
			if($REPORT_ORDER == "ORG"){
				/*
				ยังไม่โอเค
				$pdf->Cell(200, 7, "", "T", 1, 'L', 0);
				$pdf->Cell(0, 7, $PERSON_TYPE[$search_per_type]."ทั้งหมด "." 999 "." คน", $border, 1, 'R', 0);
				$pdf->Cell(0, 7, "ตำแหน่งว่าง "." 999 "." คน", $border, 1, 'R', 0);
				$pdf->Cell(0, 7, "ยืมตัวมาช่วยราชการ "." 999 "." คน",$border, 1, 'R', 0);
				$pdf->Cell(0, 7, "มาปฏิบัติราชการ "." 999 "." คน", $border, 1, 'R', 0);
				$pdf->Cell(0, 7, "ไปราชการ "." 999 "." คน",$border, 1, 'R', 0);
				$pdf->Cell(0, 7, "มาสาย "." 999 "." คน",$border, 1, 'R', 0);
				$pdf->Cell(0, 7, "ไม่มาปฏิบัติราชการ "." 999 "." คน",$border, 1, 'R', 0);
				$pdf->Cell(0, 7, "ผู้ตรวจ". str_repeat(".", 70), $border, 1, 'R', 0);*/
				$pdf->report_title = "รายงานการมาปฏิบัติราชการของบุคลากรประจำวัน แบบที่ 1||$DEPARTMENT_NAME||รายงานการมาปฏิบัติราชการของ$PERSON_TYPE[$search_per_type]||ตั้งแต่วันที่ $show_date";
				$pdf->company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$KONG $list_type_text";
	
				$pdf->AddPage();
	//				print_header();
				$pdf->print_tab_header();
				
			}elseif($REPORT_ORDER == "CONTENT"){
				
				$arr_data = (array) null;

				$arr_data[] = $arr_content[$data_count][ORDER];
				$arr_data[] = $arr_content[$data_count][name];
				$arr_data[] = $arr_content[$data_count][LSICK];
				$arr_data[] = $arr_content[$data_count][LSICKCNT];
				$arr_data[] = $arr_content[$data_count][LAKIT];
				$arr_data[] = $arr_content[$data_count][LAKITCNT];
				$arr_data[] = $arr_content[$data_count][LSICK_LAKIT];
				$arr_data[] = $arr_content[$data_count][LSICK_LAKITCNT];
				$arr_data[] = $arr_content[$data_count][LATE];
				$arr_data[] = $arr_content[$data_count][PAKPON];
				$arr_data[] = $arr_content[$data_count][PAKPONCNT];
				$arr_data[] = $arr_content[$data_count][LAOTH];
				$arr_data[] = $arr_content[$data_count][REMARK];

				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				
				
			} // end if			
		} // end for
	}else{
			$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "L", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	

	$pdf->close_tab(""); 

	$pdf->close();
	$fname = "รายงานการมาปฏิบัติราชการของบุคลากรประจำวัน แบบที่ 1.pdf";
	$pdf->Output($fname,'D');	
	//$pdf->Output();
?>