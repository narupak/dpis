<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$report_title = trim(iconv("utf-8","tis620",urldecode($report_title)));
	$worksheet = &$workbook->addworksheet('T0201');
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 12);
		$worksheet->set_column(1, 1, 30);
		$worksheet->set_column(2, 2, 15);
		$worksheet->set_column(3, 3, 15);
		$worksheet->set_column(4, 4, 25);
		$worksheet->set_column(5, 5, 10);
		$worksheet->set_column(6, 6, 10);
		$worksheet->set_column(7, 7, 10);
		
		$worksheet->write($xlsRow, 0, "เลขที่ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อ-สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "วัน-เวลาที่เริ่มใช้", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "วัน-เวลาที่สิ้นสุดรอบ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "รอบการมาปฏิบัติราชการ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "บัตร", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "นิ้ว", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "หน้า", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // end if

  $search_per_status = (isset($search_per_status))?  $search_per_status : 2;
  if($search_org_id_1 && !$search_org_id_2 && !$search_org_id_3 && !$search_org_id_4 && !$search_org_id_5){ /* Release 5.1.0.4 */
                    if($select_org_structure==0){ //โครงสร้างตามกฎหมาย
                        $arr_search_condition[] = "(c.ORG_ID_1=$search_org_id_1 or d.ORG_ID_1=$search_org_id_1 or e.ORG_ID_1=$search_org_id_1 or j.ORG_ID_1=$search_org_id_1)";
                    }else if($select_org_structure==1){ //โครงสร้างตามมอบหมายงาน
                        $arr_search_condition[] = "(a.ORG_ID_1=$search_org_id_1)";
                    }
                }elseif($search_org_id_1 && $search_org_id_2 && !$search_org_id_3 && !$search_org_id_4 && !$search_org_id_5){ /* Release 5.1.0.4 */
                    if($select_org_structure==0){ //โครงสร้างตามกฎหมาย
                        $arr_search_condition[] = "(c.ORG_ID_2=$search_org_id_2 or d.ORG_ID_2=$search_org_id_2 or e.ORG_ID_2=$search_org_id_2 or j.ORG_ID_2=$search_org_id_2)";
                    }else if($select_org_structure==1){ //โครงสร้างตามมอบหมายงาน
                        $arr_search_condition[] = "(a.ORG_ID_2=$search_org_id_2)";
                    }
                }elseif($search_org_id_1 && $search_org_id_2 && $search_org_id_3 && !$search_org_id_4 && !$search_org_id_5){ /* Release 5.1.0.4 */
                    if($select_org_structure==0){ //โครงสร้างตามกฎหมาย
                        $arr_search_condition[] = "(c.ORG_ID_3=$search_org_id_3 or d.ORG_ID_3=$search_org_id_3 or e.ORG_ID_3=$search_org_id_3 or j.ORG_ID_3=$search_org_id_3)";
                    }else if($select_org_structure==1){ //โครงสร้างตามมอบหมายงาน
                        $arr_search_condition[] = "(a.ORG_ID_3=$search_org_id_3)";
                    }
                }elseif($search_org_id_1 && $search_org_id_2 && $search_org_id_3 && $search_org_id_4 && !$search_org_id_5){ /* Release 5.1.0.4 */
                    if($select_org_structure==0){ //โครงสร้างตามกฎหมาย
                        $arr_search_condition[] = "(c.ORG_ID_4=$search_org_id_4 or d.ORG_ID_4=$search_org_id_4 or e.ORG_ID_4=$search_org_id_4 or j.ORG_ID_4=$search_org_id_4)";
                    }else if($select_org_structure==1){ //โครงสร้างตามมอบหมายงาน
                        $arr_search_condition[] = "(a.ORG_ID_4=$search_org_id_4)";
                    }
                }elseif($search_org_id_1 && $search_org_id_2 && $search_org_id_3 && $search_org_id_4 && $search_org_id_5){ /* Release 5.1.0.4 */
                    if($select_org_structure==0){ //โครงสร้างตามกฎหมาย
                        $arr_search_condition[] = "(c.ORG_ID_5=$search_org_id_5 or d.ORG_ID_5=$search_org_id_5 or e.ORG_ID_5=$search_org_id_5 or j.ORG_ID_5=$search_org_id_5)";
                    }else if($select_org_structure==1){ //โครงสร้างตามมอบหมายงาน
                        $arr_search_condition[] = "(a.ORG_ID_5=$search_org_id_5)";
                    }
                }elseif($search_org_id){
			if($select_org_structure==0){
				$arr_search_condition[] = "(c.ORG_ID=$search_org_id or d.ORG_ID=$search_org_id or e.ORG_ID=$search_org_id or j.ORG_ID=$search_org_id)";
			}else if($select_org_structure==1){
				$arr_search_condition[] = "(a.ORG_ID=$search_org_id)";
			}
		}elseif($search_department_id){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
		}elseif($search_ministry_id){
			unset($arr_department);
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id and OL_CODE='02' ";
			if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
		}elseif($PROVINCE_CODE){
			$cmd = " select distinct ORG_ID_REF from PER_ORG where PV_CODE='$PROVINCE_CODE' and OL_CODE='03' ";
			if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID_REF];
			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
		} // end if  

	 if(trim($search_per_status) < 5) {
            $temp_per_status = $search_per_status - 1;		
            $arr_search_condition[] = "(a.PER_STATUS = $temp_per_status)";	
     }

   	if(trim($search_name)) $arr_search_condition[] = "(a.PER_NAME like '".trim($search_name)."%' or UPPER(a.PER_ENG_NAME) like UPPER('".trim($search_name)."%'))";
   	 if(trim($search_surname)) $arr_search_condition[] = "(a.PER_SURNAME like '".trim($search_surname)."%' or UPPER(a.PER_ENG_SURNAME) like UPPER('".trim($search_surname)."%'))";

	
    if(trim($search_cardno)) $arr_search_condition[] = "(a.PER_CARDNO ='".trim($search_cardno)."')";
    if(trim($search_offno)) $arr_search_condition[] = "(a.PER_OFFNO ='".trim($search_offno)."')";
    
    if(trim($search_pos_no))  {	
		if ($search_per_type == 1 || $search_per_type==5)
			$arr_search_condition[] = "(trim(c.POS_NO) = '".trim($search_pos_no)."')";
		elseif ($search_per_type == 2) 
			$arr_search_condition[] = "(trim(d.POEM_NO) = '".trim($search_pos_no)."')";		
		elseif ($search_per_type == 3) 	
			$arr_search_condition[] = "(trim(e.POEMS_NO) = '".trim($search_pos_no)."')";
		elseif ($search_per_type == 4) 	
			$arr_search_condition[] = "(trim(j.POT_NO) = '".trim($search_pos_no)."')";
		else if ($search_per_type==0)		//ทั้งหมด
			$arr_search_condition[] = "((trim(c.POS_NO) = '".trim($search_pos_no)."') or (trim(d.POEM_NO) = '".trim($search_pos_no)."') or (trim(e.POEMS_NO) = '".trim($search_pos_no)."') or (trim(j.POT_NO) = '".trim($search_pos_no)."')) ";
	}
    
    if(trim($search_pos_no_name)){
		if ($search_per_type == 1 || $search_per_type==5)
			$arr_search_condition[] = "(trim(c.POS_NO_NAME) like '".trim($search_pos_no_name)."%')";
		elseif ($search_per_type == 2) 
			$arr_search_condition[] = "(trim(d.POEM_NO_NAME) like '".trim($search_pos_no_name)."%')";		
		elseif ($search_per_type == 3) 	
			$arr_search_condition[] = "(trim(e.POEMS_NO_NAME) like '".trim($search_pos_no_name)."%')";
		elseif ($search_per_type == 4) 	
			$arr_search_condition[] = "(trim(j.POT_NO_NAME) like '".trim($search_pos_no_name)."%')";
		else if ($search_per_type==0)		//ทั้งหมด
			$arr_search_condition[] = "((trim(c.POS_NO_NAME) like '".trim($search_pos_no_name)."%') or (trim(d.POEM_NO_NAME) like '".trim($search_pos_no_name)."%') or 
			(trim(e.POEMS_NO_NAME) like '".trim($search_pos_no_name)."%') or (trim(j.POT_NO_NAME) like '".trim($search_pos_no_name)."%')) ";
	}
    
    if(trim($search_pay_no))  $arr_search_condition[] = "(trim(c.POS_NO) = '$search_pay_no' and a.PER_TYPE = 1)";
    if(trim($search_level_no)) $arr_search_condition[] = "(trim(a.LEVEL_NO) = '". trim($search_level_no) ."')";
    if(trim($search_pl_code)) $arr_search_condition[] = "(trim(c.PL_CODE) = '". trim($search_pl_code) ."')";
    if(trim($search_pm_code)) $arr_search_condition[] = "(trim(c.PM_CODE) = '". trim($search_pm_code) ."')";
    if(trim($search_per_type)) 	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
    
    if($search_date_min && $search_date_max){ 
		 $tmpsearch_date_min =  save_date($search_date_min);
         $tmpsearch_date_max =  save_date($search_date_max);
         $arr_search_condition[] = " (   (substr(wch.START_DATE,1,10)  BETWEEN '$tmpsearch_date_min' and '$tmpsearch_date_max') 
         					or  (substr(wch.END_DATE,1,10) BETWEEN '$tmpsearch_date_min' and '$tmpsearch_date_max' ) 
                            or ( '$tmpsearch_date_min'  BETWEEN substr(wch.START_DATE,1,10) and substr(wch.END_DATE,1,10) )
    						or ( '$tmpsearch_date_max'  BETWEEN substr(wch.START_DATE,1,10) and substr(wch.END_DATE,1,10) )
                            )";
	}else if($search_date_min && empty($search_date_max)){ 
		 $tmpsearch_date_min =  save_date($search_date_min);
         $arr_search_condition[] = " ('$tmpsearch_date_min' BETWEEN substr(wch.START_DATE,1,10) and  ( CASE WHEN wch.END_DATE IS NOT NULL THEN substr(wch.END_DATE,1,10) ELSE to_char(sysdate,'yyyy-mm-dd') END) ) ";
    }else if(empty($search_date_min) && $search_date_max){ 
		 $tmpsearch_date_max =  save_date($search_date_max);
         $arr_search_condition[] = " ('$tmpsearch_date_max' BETWEEN substr(wch.START_DATE,1,10) and ( CASE WHEN wch.END_DATE IS NOT NULL THEN substr(wch.END_DATE,1,10) ELSE to_char(sysdate,'yyyy-mm-dd') END) ) ";
    }

     if(trim($search_wc_code)) $arr_search_condition[] = "(wch.WC_CODE ='".trim($search_wc_code)."')";
     
     if($search_per_status_scan==1) {
    	 $arr_search_condition[] = "(TRG.CARD_NO is not null OR TRG.FINGER_COUNT is not null OR TRG.FACE_COUNT is not null )";
     }else if($search_per_status_scan==2) {
     	$arr_search_condition[] = "(TRG.CARD_NO is null AND TRG.FINGER_COUNT is null AND TRG.FACE_COUNT is null )";
     }
     
    
    $search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

	$Tmpsearch_per_status_workcyclehis = "";
    $Tmpsearch_per_status_workcyclehis = " AND wch.PER_ID is not  null
            															AND wch.WH_ID =(select max(WH_ID) as WH_ID FROM PER_WORK_CYCLEHIS WHERE PER_ID=wch.PER_ID) ";
          
	

	$cmd = " select 	wch.WH_ID,wc.WC_NAME,a.PER_TYPE,
				g.PN_SHORTNAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW,
				wch.START_DATE,wch.END_DATE,wch.REMARK,c.POS_NO,a.PER_ID,
				TRG.CARD_NO AS CARD_NO_S,TRG.FINGER_COUNT,TRG.FACE_COUNT,
				d.POEM_NO,e.POEMS_NO,j.POT_NO,a.PER_STATUS
				from  PER_PERSONAL a  
				left join  PER_WORK_CYCLEHIS wch on(a.PER_ID=wch.PER_ID)
				left join PER_WORK_CYCLE wc on(wc.WC_CODE=wch.WC_CODE)
				left join TA_REGISTERUSER TRG on (TRG.PER_ID=wch.PER_ID)
				left join PER_ORG b on(b.ORG_ID=a.ORG_ID) 
				left join PER_POSITION c on(c.POS_ID=a.POS_ID) 
				left join PER_POS_EMP d on(d.POEM_ID=a.POEM_ID) 
				left join PER_POS_EMPSER e on(e.POEMS_ID=a.POEMS_ID) 
				left join PER_LEVEL f on(f.LEVEL_NO=a.LEVEL_NO) 
				left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE) 
				left join PER_MGT h on(h.PM_CODE=c.PM_CODE)
				left join PER_LINE i on(i.PL_CODE=c.PL_CODE)
				left join PER_POS_TEMP j on (j.POT_ID=a.POT_ID)
				where 	1=1 $Tmpsearch_per_status_workcyclehis
							$search_condition 
				order by 	to_number(replace(c.POS_NO,'-','')) ASC, to_number(replace(d.POEM_NO,'-','')) ASC, to_number(replace(e.POEMS_NO,'-','')) ASC, to_number(replace(j.POT_NO,'-','')) ASC ";
//echo "<pre>".$cmd; die();
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$DATA_POS_NO = trim($data[POS_NO]).trim($data[POEM_NO]).trim($data[POEMS_NO]).trim($data[POT_NO]);
			if($data[PER_STATUS] == 2){		//พ้นจากส่วนราชการ
                 $DATA_POS_NO = "";
            } // end if	
			$DATA_FULLNAME_SHOW = trim($data[FULLNAME_SHOW]);
			$DATA_START_DATE = show_date_format(substr($data[START_DATE],0,10), $DATE_DISPLAY)." ".substr($data[START_DATE],11,5);
			$DATA_END_DATE ="";
			if($data[END_DATE]){
				$DATA_END_DATE = show_date_format(substr($data[END_DATE],0,10), $DATE_DISPLAY)." ".substr($data[END_DATE],11,5);
			}
			$DATA_WC_NAME = trim($data[WC_NAME]);
			
			$CARD_NO_S = ($data[CARD_NO_S]=="" || $data[CARD_NO_S]=="0")?"../images/checkbox_blank.bmp":"../images/true.bmp";
			$FINGER_COUNT = ($data[FINGER_COUNT]=="" || $data[FINGER_COUNT]=="0")?"../images/checkbox_blank.bmp":"../images/true.bmp";
			$FACE_COUNT = ($data[FACE_COUNT]=="" || $data[FACE_COUNT]=="0")?"../images/checkbox_blank.bmp":"../images/true.bmp";
			
			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $DATA_POS_NO, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $DATA_FULLNAME_SHOW, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $DATA_START_DATE, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $DATA_END_DATE, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $DATA_WC_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, 5, $CARD_NO_S, 35, 4, 1, 0.8);
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, 6, $FINGER_COUNT, 35, 4, 1, 0.8);
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, 7, $FACE_COUNT, 35, 4, 1, 0.8);
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
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"T0201.xls\"");
	header("Content-Disposition: inline; filename=\"T0201.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>