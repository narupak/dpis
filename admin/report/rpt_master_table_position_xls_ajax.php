<?php
	
	if($_POST[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_POST[NUMBER_DISPLAY];
	//if($_POST['CH_PRINT_FONT']){ $CH_PRINT_FONT=$_POST['CH_PRINT_FONT']; }
	if($_POST['order_by']){ $order_by=$_POST['order_by']; }
	if($_POST['sort_by']){ $sort_by=$_POST['sort_by']; }
	if($_POST['sort_type']){ $sort_type=$_POST['sort_type']; }
	if($_POST['search_pos_no_name_min']){ $search_pos_no_name_min=$_POST['search_pos_no_name_min']; }
	if($_POST['search_pos_no_min']){ $search_pos_no_min = $_POST['search_pos_no_min']; }
	if($_POST['search_pos_no_name_max']){ $search_pos_no_name_max = $_POST['search_pos_no_name_max']; }
	if($_POST['search_pos_no_max']){ $search_pos_no_max = $_POST['search_pos_no_max']; }
	if($_POST['search_pay_no_min']){ $search_pay_no_min = $_POST['search_pay_no_min']; }
	if($_POST['search_pay_no_max']){ $search_pay_no_max = $_POST['search_pay_no_max']; }
	if($_POST['search_pl_code']){ $search_pl_code = $_POST['search_pl_code']; }
	if($_POST['search_pl_name']){ $search_pl_name = $_POST['search_pl_name']; }
	if($_POST['search_pm_code']){ $search_pm_code = $_POST['search_pm_code']; }
	if($_POST['search_pm_name']){ $search_pm_name = $_POST['search_pm_name']; }
	if($_POST['search_cl_name']){ $search_cl_name = $_POST['search_cl_name']; }
	if($_POST['search_level_no']){ $search_level_no = $_POST['search_level_no']; }
	if($_POST['search_pt_code']){ $search_pt_code = $_POST['search_pt_code']; }
	if($_POST['search_pt_name']){ $search_pt_name = $_POST['search_pt_name']; }
	if($_POST['search_skill_code']){ $search_skill_code=$_POST['search_skill_code']; }
	if($_POST['search_skill_name']){ $search_skill_name=$_POST['search_skill_name']; }
	if($_POST['search_pc_code']){ $search_pc_code=$_POST['search_pc_code']; }
	if($_POST['search_pc_name']){ $search_pc_name=$_POST['search_pc_name']; }
	if($_POST['search_org_id']){ $search_org_id=$_POST['search_org_id']; }	
	if($_POST['search_org_name']){ $search_org_name=$_POST['search_org_name']; }
	if($_POST['search_ministry_name']){ $search_ministry_name= $_POST['search_ministry_name']; }
	if($_POST['search_department_name']){ $search_department_name = $_POST['search_department_name']; }
	if($_POST['search_department_id']){ $search_department_id = $_POST['search_department_id']; }
	if($_POST['search_ministry_id']){ $search_ministry_id = $_POST['search_ministry_id']; }
	if($_POST['search_org_id_1']){ $search_org_id_1 = $_POST['search_org_id_1']; }
	if($_POST['search_org_name_1']){ $search_org_name_1 = $_POST['search_org_name_1']; }
	if($_POST['search_org_id_2']){ $search_org_id_2 = $_POST['search_org_id_2']; }
	if($_POST['search_org_name_2']){ $search_org_name_2 = $_POST['search_org_name_2']; }
	if($_POST['search_org_id_3']){ $search_org_id_3 = $_POST['search_org_id_3']; }
	if($_POST['search_org_name_3']){ $search_org_name_3 = $_POST['search_org_name_3']; }
	if($_POST['search_org_id_4']){ $search_org_id_4 = $_POST['search_org_id_4']; }
	if($_POST['search_org_name_4']){ $search_org_name_4 = $_POST['search_org_name_4']; }
	if($_POST['search_org_id_5']){ $search_org_id_5 = $_POST['search_org_id_5']; }
	if($_POST['search_org_name_5']){ $search_org_name_5 = $_POST['search_org_name_5']; }
	if($_POST['search_ct_code']){ $search_ct_code = $_POST['search_ct_code']; }
	if($_POST['search_ct_name']){ $search_ct_name = $_POST['search_ct_name']; }
	if($_POST['search_pv_code']){ $search_pv_code = $_POST['search_pv_code']; }
	if($_POST['search_pv_name']){ $search_pv_name = $_POST['search_pv_name']; }
	if($_POST['search_ap_code']){ $search_ap_code = $_POST['search_ap_code']; }
	if($_POST['search_ap_name']){ $search_ap_name = $_POST['search_ap_name']; }
	if($_POST['search_ot_code']){ $search_ot_code = $_POST['search_ot_code']; }
	if($_POST['search_ot_name']){ $search_ot_name = $_POST['search_ot_name']; }
	if($_POST['search_pr_code']){ $search_pr_code = $_POST['search_pr_code']; }
	if($_POST['search_pr_name']){ $search_pr_name = $_POST['search_pr_name']; }
	if($_POST['search_pos_date_min']){ $search_pos_date_min = $_POST['search_pos_date_min']; }
	if($_POST['search_pos_date_max']){ $search_pos_date_max = $_POST['search_pos_date_max']; }
	if($_POST['search_pos_change_date_min']){ $search_pos_change_date_min = $_POST['search_pos_change_date_min']; }
	if($_POST['search_pos_change_date_max']){ $search_pos_change_date_max = $_POST['search_pos_change_date_max']; }
	if($_POST['search_pos_vacant_date_min']){ $search_pos_vacant_date_min = $_POST['search_pos_vacant_date_min']; }
	if($_POST['search_pos_vacant_date_max']){ $search_pos_vacant_date_max = $_POST['search_pos_vacant_date_max']; }
	if($_POST['search_pos_salary_min']){ $search_pos_salary_min = $_POST['search_pos_salary_min']; }
	if($_POST['search_pos_salary_max']){ $search_pos_salary_max = $_POST['search_pos_salary_max']; }
	if($_POST['search_pos_situation']){ $search_pos_situation = $_POST['search_pos_situation']; }
	if($_POST['search_pay_situation']){ $search_pay_situation = $_POST['search_pay_situation']; }
	if($_POST['search_pos_status']){ $search_pos_status = $_POST['search_pos_status']; }
	if($_POST['search_pos_reserve']){ $search_pos_reserve = $_POST['search_pos_reserve']; }
	if($_POST['search_pos_reserve1']){ $search_pos_reserve1 = $_POST['search_pos_reserve1']; }
	if($_POST['search_pos_reserve2']){ $search_pos_reserve2 = $_POST['search_pos_reserve2']; }
	if($_POST['search_data']){ $search_data = $_POST['search_data']; }
	if($_POST['search_pv_name']){ $search_pv_name = $_POST['search_pv_name']; }
	if($_POST['search_pv_name']){ $search_pv_name = $_POST['search_pv_name']; }
	if($_POST['search_pv_name']){ $search_pv_name = $_POST['search_pv_name']; }
	if($_POST['search_pv_name']){ $search_pv_name = $_POST['search_pv_name']; }
	if($_GET['report_title']){ $report_title = $_GET['report_title']; }
	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
        // 19/07/2560  15.23
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	ini_set("max_execution_time", 0);
	ini_set("memory_limit","2048M"); 

	/*$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";*/

	function getToFont($id){
		if($id==1){
				$fullname	= 'Angsana';
		}else if($id==2){
				$fullname	= 'Cordia';
		}else if($id==3){
				$fullname	= 'TH SarabunPSK';
		}else{
				$fullname	= 'Browallia';
		}
		return $fullname;
	}
	function convert_TIS620_UTF8($txt){
		return @iconv('TIS-620','UTF-8',$txt);
	}
	
	
	$dir_folder = file_exists("../../Excel/tmp/S0201");
	if($dir_folder==0){
		mkdir("../../Excel/tmp/S0201"); 
	}
	$files = glob('../../Excel/tmp/S0201/rpt_S0201.xlsx'); // get all file names
	foreach($files as $file){ // iterate files
	  if(is_file($file))
		unlink($file); // delete file
	}
	$logfile = '../../Excel/tmp/S0201/XLOG.txt';
        if (1==1) {
            $fp = fopen($logfile, 'a');
            fwrite($fp, "Begin!!!!\r\n".'date :'.date("Y-m-d h:i:sa").'\r\n');
            fclose($fp);                    
        }
	if($DPISDB=="odbc") $POS_NO_NUM = "CLng(POS_NO)";
	elseif($DPISDB=="oci8") $POS_NO_NUM = "to_number(replace(POS_NO,'-',''))";
	elseif($DPISDB=="mysql") $POS_NO_NUM = "POS_NO+0";
	if(!$sort_by) $sort_by=1;
	$sort_type = (isset($sort_type))?  $sort_type : "1:asc";
	$arrSort=explode(":",$sort_type);
	$SortType[$arrSort[0]]	=$arrSort[1];
	if(!$order_by) $order_by=1;
	if($order_by==1){
		$org_cond = "";
		if ($POSITION_NO_CHAR=="Y") $org_cond = ", b.ORG_SEQ_NO $SortType[$order_by], b.ORG_CODE $SortType[$order_by]";
		if($DPISDB=="odbc")	$order_str = "a.DEPARTMENT_ID $SortType[$order_by]".$org_cond.", POS_NO_NAME $SortType[$order_by], iif(isnull(POS_NO),0,$POS_NO_NUM) $SortType[$order_by]";
		if($DPISDB=="oci8")	$order_str = "a.DEPARTMENT_ID $SortType[$order_by]".$org_cond.", POS_NO_NAME $SortType[$order_by], $POS_NO_NUM $SortType[$order_by]";
		if($DPISDB=="mysql")	$order_str = "a.DEPARTMENT_ID $SortType[$order_by]".$org_cond.", POS_NO_NAME $SortType[$order_by], $POS_NO_NUM $SortType[$order_by]";
  	}elseif($order_by==2) {
		$order_str = "PM_NAME $SortType[$order_by]";
  	} elseif($order_by==3){
		$order_str = "PL_NAME $SortType[$order_by]";
  	} elseif($order_by==4) {
		$order_str = "a.CL_NAME $SortType[$order_by]";
	} else 	if($order_by==5){
		$order_str = "a.LEVEL_NO $SortType[$order_by]";
  	}elseif($order_by==6) {
		$order_str = "a.DEPARTMENT_ID $SortType[$order_by], b.ORG_SEQ_NO $SortType[$order_by], b.ORG_CODE $SortType[$order_by], POS_NO_NAME $SortType[$order_by], $POS_NO_NUM $SortType[$order_by]";
  	} elseif($order_by==7){
		$order_str = "b.ORG_NAME $SortType[$order_by]";
  	} elseif($order_by==8) {
		$order_str = "a.ORG_ID_1 $SortType[$order_by]";
	} elseif($order_by==9) {	
		$order_str = "a.POS_SEQ_NO $SortType[$order_by]";
	}		
        if(trim($search_pos_no_name_min)) $arr_search_condition[] = "(trim(POS_NO_NAME) = '". trim($search_pos_no_name_min) ."')";
  	if(trim($search_pos_no_min)){ 
		if($DPISDB=="odbc") $arr_search_condition[] = "(CLng(POS_NO) >= $search_pos_no_min)";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(to_number(replace(POS_NO,'-','')) >= $search_pos_no_min)";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(POS_NO >= $search_pos_no_min)";
	} // end if
        if(trim($search_pos_no_name_max)) $arr_search_condition[] = "(trim(POS_NO_NAME) = '". trim($search_pos_no_name_max) ."')";
  	if(trim($search_pos_no_max)){ 
		if($DPISDB=="odbc") $arr_search_condition[] = "(CLng(POS_NO) <= $search_pos_no_max)";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(to_number(replace(POS_NO,'-','')) <= $search_pos_no_max)";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(POS_NO <= $search_pos_no_max)";
	} // end if
	if(trim($search_pos_no_min) && trim($search_pos_no_max)){
		$print_search_condition[] = convert_TIS620_UTF8("เลขที่ตำแหน่ง").":".convert_TIS620_UTF8($search_pos_no_min).convert_TIS620_UTF8("ถึง").convert_TIS620_UTF8($search_pos_no_max);
	}elseif(trim($search_pos_no_min)){
		$print_search_condition[] = convert_TIS620_UTF8("เลขที่ตำแหน่ง : มากกว่า $search_pos_no_min");
	}elseif(trim($search_pos_no_max)){
		$print_search_condition[] = convert_TIS620_UTF8("เลขที่ตำแหน่ง : น้อยกว่า $search_pos_no_max");
	} // end if
  	if(trim($search_pay_no_min)){ 
		if($DPISDB=="odbc") $arr_search_condition[] = "(CLng(PAY_NO) >= $search_pay_no_min)";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(to_number(replace(PAY_NO,'-','')) >= $search_pay_no_min)";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(PAY_NO+0 >= $search_pay_no_min)";
	} // end if
  	if(trim($search_pay_no_max)){ 
		if($DPISDB=="odbc") $arr_search_condition[] = "(CLng(PAY_NO) <= $search_pay_no_max)";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(to_number(replace(PAY_NO,'-','')) <= $search_pay_no_max)";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(PAY_NO+0 <= $search_pay_no_max)";
	} // end if
	if(trim($search_pl_code)){ 
		$arr_search_condition[] = "(trim(PL_CODE) = '". trim($search_pl_code) ."')";
		$print_search_condition[] = convert_TIS620_UTF8("$PL_TITLE : ").$search_pl_name;
	} // end if
	if(trim($search_pm_code)){ 
		$arr_search_condition[] = "(trim(PM_CODE) = '". trim($search_pm_code) ."')";
		$print_search_condition[] = convert_TIS620_UTF8("$PM_TITLE : ").$search_pm_name;
	} // end if
	if(trim($search_cl_name)){ 
		$arr_search_condition[] = "(trim(CL_NAME) = '". trim($search_cl_name) ."')";
		$print_search_condition[] = convert_TIS620_UTF8("$CL_TITLE : ").$search_cl_name;
	} // end if
	if(trim($search_level_no)){ 
		$arr_search_condition[] = "(trim(LEVEL_NO) = '". trim($search_level_no) ."')";
		$print_search_condition[] = convert_TIS620_UTF8("$LEVEL_TITLE : $search_level_no");
	} // end if
	if(trim($search_pt_code)){ 
		$arr_search_condition[] = "(trim(PT_CODE) = '". trim($search_pt_code) ."')";
		$print_search_condition[] = convert_TIS620_UTF8("$PT_TITLE : ").$search_pt_name;
	} // end if
	if(trim($search_skill_code)){ 
		$arr_search_condition[] = "(trim(SKILL_CODE) = '". trim($search_skill_code) ."')";
		$print_search_condition[] = convert_TIS620_UTF8("$SKILL_TITLE : ").$search_skill_name;
	} // end if
	if(trim($search_pc_code)){ 
		$arr_search_condition[] = "(trim(PC_CODE) = '". trim($search_pc_code) ."')";
		$print_search_condition[] = convert_TIS620_UTF8("$PC_TITLE : ").$search_pc_name;
	} // end if

	if(trim($search_org_id)){ 
		$arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		$print_search_condition[] = convert_TIS620_UTF8($MINISTRY_TITLE)." : ".$search_ministry_name;
		$print_search_condition[] = convert_TIS620_UTF8($DEPARTMENT_TITLE)." : ".$search_department_name;
		$print_search_condition[] = convert_TIS620_UTF8($ORG_TITLE)." : ".$search_org_name;
	}elseif(trim($search_department_id)){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
		$print_search_condition[] = convert_TIS620_UTF8($MINISTRY_TITLE)." : ".$search_ministry_name;
		$print_search_condition[] = convert_TIS620_UTF8($DEPARTMENT_TITLE)." : ".$search_department_name;
	}elseif(trim($search_ministry_id)){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
		$print_search_condition[] = convert_TIS620_UTF8($MINISTRY_TITLE)." : ".$search_ministry_name;
	} // end if
	
	if(trim($search_org_id_1)){ 
		$arr_search_condition[] = "(ORG_ID_1 = $search_org_id_1)";
		$print_search_condition[] = convert_TIS620_UTF8($ORG_TITLE1)." : ".$search_org_name_1;
	} // end if
	if(trim($search_org_id_2)){ 
		$arr_search_condition[] = "(ORG_ID_2 = $search_org_id_2)";
		$print_search_condition[] = convert_TIS620_UTF8($ORG_TITLE2)." : ".$search_org_name_2;
	} // end if
        if(trim($search_org_id_3)){ /*Release 5.1.0.4*/
		$arr_search_condition[] = "(ORG_ID_3 = $search_org_id_3)";
		$print_search_condition[] = convert_TIS620_UTF8($ORG_TITLE3)." : ".$search_org_name_3;
	} 
        if(trim($search_org_id_4)){ /*Release 5.1.0.4*/
		$arr_search_condition[] = "(ORG_ID_4 = $search_org_id_4)";
		$print_search_condition[] = convert_TIS620_UTF8($ORG_TITLE4)." : ".$search_org_name_4;
	} 
        if(trim($search_org_id_5)){ /*Release 5.1.0.4*/
		$arr_search_condition[] = "(ORG_ID_5 = $search_org_id_5)";
		$print_search_condition[] = convert_TIS620_UTF8($ORG_TITLE5)." : ".$search_org_name_5;
	} 
        
        
	if(trim($search_ct_code)){ 
		$arr_search_condition[] = "(trim(b.CT_CODE) = '". trim($search_ct_code) ."')";
		$print_search_condition[] = convert_TIS620_UTF8("ประเทศ : ").$search_ct_name;
	} // end if
	if(trim($search_pv_code)){ 
		$arr_search_condition[] = "(trim(b.PV_CODE) = '". trim($search_pv_code) ."')";
		$print_search_condition[] = convert_TIS620_UTF8("จังหวัด : ").$search_pv_name;
	} // end if
	if(trim($search_ap_code)){
		$arr_search_condition[] = "(trim(b.AP_CODE) = '". trim($search_ap_code) ."')";
		$print_search_condition[] = convert_TIS620_UTF8("อำเภอ : ").$search_ap_name;
	} // end if
	if(trim($search_ot_code)){ 
		$arr_search_condition[] = "(trim(b.OT_CODE) = '". trim($search_ot_code) ."')";
		$print_search_condition[] = convert_TIS620_UTF8("สังกัด : ").$search_ot_name;
	} // end if

	if(trim($search_pr_code)){ 
		$arr_search_condition[] = "(trim(a.PR_CODE) = '". trim($search_pr_code) ."')";
		$print_search_condition[] = convert_TIS620_UTF8("สงวนตำแหน่ง : ").$search_pr_name;
	} // end if

	if(trim($search_pos_date_min)){
		$search_pos_date_min =  save_date($search_pos_date_min);
		if($DPISDB=="odbc") $arr_search_condition[] = "(Mid(POS_DATE, 1, 10) >= '$search_pos_date_min')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(POS_DATE, 1, 10) >= '$search_pos_date_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(Mid(POS_DATE, 1, 10) >= '$search_pos_date_min')";
		$search_pos_date_min = show_date_format($search_pos_date_min, 1);
		$show_search_pos_date_min = show_date_format($search_pos_date_min, 2);
	} // end if
	if(trim($search_pos_date_max)){
		$search_pos_date_max =  save_date($search_pos_date_max);
		if($DPISDB=="odbc") $arr_search_condition[] = "(Mid(POS_DATE, 1, 10) <= '$search_pos_date_max')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(POS_DATE, 1, 10) <= '$search_pos_date_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(Mid(POS_DATE, 1, 10) <= '$search_pos_date_max')";
		$search_pos_date_max = show_date_format($search_pos_date_max, 1);
		$show_search_pos_date_max = show_date_format($search_pos_date_max, 2);
	} // end if
	if(trim($search_pos_date_min) && trim($search_pos_date_max)){
		$print_search_condition[] = convert_TIS620_UTF8("วันที่ ก.พ. กำหนดตำแหน่ง : $show_search_pos_date_min ถึง $show_search_pos_date_max");
	}elseif(trim($search_pos_date_min)){
		$print_search_condition[] = convert_TIS620_UTF8("วันที่ ก.พ. กำหนดตำแหน่ง : ตั้งแต่ $show_search_pos_date_min");
	}elseif(trim($search_pos_date_max)){
		$print_search_condition[] = convert_TIS620_UTF8("วันที่ ก.พ. กำหนดตำแหน่ง : ก่อน $show_search_pos_date_max");
	} // end if
	if(trim($search_pos_change_date_min)){
		$search_pos_change_date_min =  save_date($search_pos_change_date_min);
		if($DPISDB=="odbc") $arr_search_condition[] = "(Mid(POS_CHANGE_DATE, 1, 10) >= '$search_pos_change_date_min')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(POS_CHANGE_DATE, 1, 10) >= '$search_pos_change_date_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(Mid(POS_CHANGE_DATE, 1, 10) >= '$search_pos_change_date_min')";
		$search_pos_change_date_min = show_date_format($search_pos_change_date_min, 1);
		$show_search_pos_change_date_min = show_date_format($search_pos_change_date_min, 2);
	} // end if
	if(trim($search_pos_change_date_max)){
		$search_pos_change_date_max =  save_date($search_pos_change_date_max);
		if($DPISDB=="odbc") $arr_search_condition[] = "(Mid(POS_CHANGE_DATE, 1, 10) <= '$search_pos_change_date_max')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(POS_CHANGE_DATE, 1, 10) <= '$search_pos_change_date_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(Mid(POS_CHANGE_DATE, 1, 10) <= '$search_pos_change_date_max')";
		$search_pos_change_date_max = show_date_format($search_pos_change_date_max, 1);
		$show_search_pos_change_date_max = show_date_format($search_pos_change_date_max, 2);
	} // end if
	if(trim($search_pos_change_date_min) && trim($search_pos_change_date_max)){
		$print_search_condition[] = convert_TIS620_UTF8("วันที่ตำแหน่งเปลี่ยนสถานภาพ : $show_search_pos_change_date_min ถึง $show_search_pos_change_date_max");
	}elseif(trim($search_pos_change_date_min)){
		$print_search_condition[] = convert_TIS620_UTF8("วันที่ตำแหน่งเปลี่ยนสถานภาพ : ตั้งแต่ $show_search_pos_change_date_min");
	}elseif(trim($search_pos_change_date_max)){
		$print_search_condition[] = convert_TIS620_UTF8("วันที่ตำแหน่งเปลี่ยนสถานภาพ : ก่อน $show_search_pos_change_date_max");
	} // end if
	if(trim($search_pos_vacant_date_min)){
		$search_pos_vacant_date_min =  save_date($search_pos_vacant_date_min);
		if($DPISDB=="odbc") $arr_search_condition[] = "(Mid(POS_VACANT_DATE, 1, 10) >= '$search_pos_vacant_date_min')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(POS_VACANT_DATE, 1, 10) >= '$search_pos_vacant_date_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(Mid(POS_VACANT_DATE, 1, 10) >= '$search_pos_vacant_date_min')";
		$search_pos_vacant_date_min = show_date_format($search_pos_vacant_date_min, 1);
		$show_search_pos_vacant_date_min = show_date_format($search_pos_vacant_date_min, 2);
	} // end if
	if(trim($search_pos_vacant_date_max)){
		$search_pos_vacant_date_max =  save_date($search_pos_vacant_date_max);
		if($DPISDB=="odbc") $arr_search_condition[] = "(Mid(POS_VACANT_DATE, 1, 10) <= '$search_pos_vacant_date_max')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(POS_VACANT_DATE, 1, 10) <= '$search_pos_vacant_date_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(Mid(POS_VACANT_DATE, 1, 10) <= '$search_pos_vacant_date_max')";
		$search_pos_vacant_date_max = show_date_format($search_pos_vacant_date_max, 1);
		$show_search_pos_vacant_date_max = show_date_format($search_pos_vacant_date_max, 2);
	} // end if
	if(trim($search_pos_vacant_date_min) && trim($search_pos_vacant_date_max)){
		$print_search_condition[] = convert_TIS620_UTF8("วันที่ตำแหน่งว่าง : $show_search_pos_vacant_date_min ถึง $show_search_pos_vacant_date_max");
	}elseif(trim($search_pos_vacant_date_min)){
		$print_search_condition[] = convert_TIS620_UTF8("วันที่ตำแหน่งว่าง : ตั้งแต่ $show_search_pos_vacant_date_min");
	}elseif(trim($search_pos_vacant_date_max)){
		$print_search_condition[] = convert_TIS620_UTF8("วันที่ตำแหน่งว่าง : ก่อน $show_search_pos_vacant_date_max");
	} // end if
  	if(trim($search_pos_salary_min)) $arr_search_condition[] = "(POS_SALARY >= $search_pos_salary_min)";
  	if(trim($search_pos_salary_max)) $arr_search_condition[] = "(POS_SALARY <= $search_pos_salary_max)";
	if(trim($search_pos_salary_min) && trim($search_pos_salary_max)){
		$print_search_condition[] = convert_TIS620_UTF8("อัตราเงินเดือนถือจ่าย : $search_pos_salary_min ถึง $search_pos_salary_max");
	}elseif(trim($search_pos_salary_min)){
		$print_search_condition[] = convert_TIS620_UTF8("อัตราเงินเดือนถือจ่าย : มากกว่า $search_pos_salary_min");
	}elseif(trim($search_pos_salary_max)){
		$print_search_condition[] = convert_TIS620_UTF8("อัตราเงินเดือนถือจ่าย : น้อยกว่า $search_pos_salary_max");
	} // end if
	if(trim($search_pos_situation) == 1){ 
		$arr_search_condition[] = "(c.PER_STATUS IS NULL)";
		$print_search_condition[] = convert_TIS620_UTF8("สถานภาพของตำแหน่ง : ว่าง");
	} // end if
	if(trim($search_pos_situation) == 2){ 
		$arr_search_condition[] = "(c.PER_STATUS=1)";
		$print_search_condition[] = convert_TIS620_UTF8("สถานภาพของตำแหน่ง : มีคนครอง");
	} // end if
	if(trim($search_pay_situation) == 1){ 
		$arr_search_condition[] = "(e.PER_STATUS IS NULL)";
		$print_search_condition[] = convert_TIS620_UTF8("สถานภาพของถือจ่าย : ว่าง");
	} // end if
	if(trim($search_pay_situation) == 2){ 
		$arr_search_condition[] = "(e.PER_STATUS=1)";
		$print_search_condition[] = "สถานภาพของถือจ่าย : มีคนครอง";
	} // end if

	if(trim($search_pos_status) == 1) $arr_search_condition[] = "(a.POS_STATUS = 1)";
	if(trim($search_pos_status) == 2) $arr_search_condition[] = "(a.POS_STATUS = 2)";
	if($search_pos_status == 0){
		$print_search_condition[] = convert_TIS620_UTF8("สถานะ : ทั้งหมด");
	}elseif($search_pos_status == 1){
		$print_search_condition[] = convert_TIS620_UTF8("สถานะ : ใช้งาน");
	}elseif($search_pos_status == 2){
		$print_search_condition[] = convert_TIS620_UTF8("สถานะ : ยกเลิก");
	} // end if

	
  	if(trim($search_pos_reserve)) $arr_search_condition[] = "(POS_RESERVE IS NOT NULL or POS_RESERVE2 IS NOT NULL)";
  	if(trim($search_pos_reserve1)) $arr_search_condition[] = "(POS_RESERVE like '$search_pos_reserve1%')";
  	if(trim($search_pos_reserve2)) $arr_search_condition[] = "(POS_RESERVE2 like '$search_pos_reserve2%')";

	$check_condition = "";
	if ($search_data==1){ 
		$check_condition = " and (a.LEVEL_NO is NULL) ";
		$print_search_condition[] = convert_TIS620_UTF8("ตรวจสอบข้อมูล : ไม่มีระดับตำแหน่ง");
	}elseif ($search_data==2){
		$check_condition = " and (a.PL_CODE in (select d.PL_CODE from PER_LINE d where a.PL_CODE = d.PL_CODE and a.LEVEL_NO not between LEVEL_NO_MIN and LEVEL_NO_MAX)) ";
		$print_search_condition[] = convert_TIS620_UTF8("ตรวจสอบข้อมูล : ระดับตำแหน่งไม่ตรงกับสายงาน");
	}elseif ($search_data==3){
		$check_condition = " and (a.CL_NAME in (select d.CL_NAME from PER_CO_LEVEL d, PER_PERSONAL e where a.CL_NAME = d.CL_NAME and a.POS_ID = e.POS_ID and e.PER_TYPE = 1 and e.PER_STATUS = 1 and e.LEVEL_NO not between LEVEL_NO_MIN and LEVEL_NO_MAX)) ";
		$print_search_condition[] = convert_TIS620_UTF8("ตรวจสอบข้อมูล : ช่วงระดับตำแหน่งไม่สอดคล้องกับระดับตำแหน่งของข้าราชการ");
	}elseif ($search_data==4){
		$check_condition = " and (a.POS_ID in (select distinct d.POS_ID from PER_PERSONAL d where a.POS_ID = d.POS_ID and d.PER_TYPE = 1 and d.PER_STATUS = 1) and a.DEPARTMENT_ID not in (select distinct d.DEPARTMENT_ID from PER_PERSONAL d where a.POS_ID = d.POS_ID and d.PER_TYPE = 1 and d.PER_STATUS = 1)) ";
		$print_search_condition[] = convert_TIS620_UTF8("ตรวจสอบข้อมูล : สังกัดของตำแหน่งไม่ตรงกับสังกัดของข้าราชการ");
	}
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd =" select 		a.POS_ID, a.DEPARTMENT_ID, a.POS_NO_NAME, IIf(IsNull(a.POS_NO), 0, CLng(a.POS_NO)) as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
										a.ORG_ID, b.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
										c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
										f.PER_STATUS as PER_STATUS4, a.POS_CONDITION, a.POS_REMARK, a.LEVEL_NO, a.PAY_NO, a.POS_SALARY
						from (
									(
										(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) left join PER_PERSONAL c on (a.POS_ID=c.POS_ID and c.PER_TYPE=1 and c.PER_STATUS=1)
										) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and d.PER_TYPE=1 and (d.PER_STATUS=0))
									) left join PER_PERSONAL e on (a.POS_ID=e.PAY_ID and e.PER_TYPE=1 and e.PER_STATUS=1)
								) left join PER_PERSONAL f on (a.POS_ID=f.PAY_ID and f.PER_TYPE=1 and (f.PER_STATUS=0))
						$search_condition $check_condition
						group by a.POS_ID, a.DEPARTMENT_ID, a.POS_NO_NAME, IIf(IsNull(a.POS_NO), 0, CLng(a.POS_NO)), PM_CODE, PL_CODE, CL_NAME, PT_CODE, a.POS_CONDITION,
										a.ORG_ID, b.ORG_SEQ_NO, b.ORG_CODE, b.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, c.PER_STATUS, d.PER_STATUS, 
										e.PER_STATUS, f.PER_STATUS, a.POS_REMARK, a.LEVEL_NO, a.PAY_NO, a.POS_SALARY
						order by $order_str ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "SELECT ROWNUM ,q1.* from (	select 		a.POS_ID, a.DEPARTMENT_ID, a.POS_NO_NAME, to_number(replace(POS_NO,'-','')) as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2,
                                                                                        a.ORG_ID_3,a.ORG_ID_4,a.ORG_ID_5, a.POS_STATUS, 
											c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
											f.PER_STATUS as PER_STATUS4, a.POS_CONDITION, a.POS_REMARK, a.LEVEL_NO, a.PAY_NO, a.POS_SALARY
							from 		PER_POSITION a, PER_ORG b, 
											(select POS_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_TYPE=1 and PER_STATUS=1) c, 
											(select POS_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_TYPE=1 and (PER_STATUS=0)) d,
											(select PAY_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_TYPE=1 and PER_STATUS=1) e, 
											(select PAY_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_TYPE=1 and (PER_STATUS=0)) f
							where 	a.ORG_ID=b.ORG_ID and a.POS_ID=c.POS_ID(+) and a.POS_ID=d.POS_ID(+) and a.POS_ID=e.PAY_ID(+) and a.POS_ID=f.PAY_ID(+)
											$search_condition $check_condition
							group by a.POS_ID, a.DEPARTMENT_ID, a.POS_NO_NAME, to_number(replace(POS_NO,'-','')), PM_CODE, PL_CODE, CL_NAME, PT_CODE, a.POS_CONDITION,
											 a.ORG_ID, b.ORG_SEQ_NO, b.ORG_CODE, b.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2,a.ORG_ID_3,a.ORG_ID_4,a.ORG_ID_5, a.POS_STATUS, c.PER_STATUS, d.PER_STATUS, 
											e.PER_STATUS, f.PER_STATUS, a.POS_REMARK, a.LEVEL_NO, a.PAY_NO, a.POS_SALARY
							order by $order_str )q1 --where  rownum <= 2
							";
	}elseif($DPISDB=="mysql"){
		$cmd =" 
					select 		a.POS_ID, a.DEPARTMENT_ID, a.POS_NO_NAME, a.POS_NO as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
										a.ORG_ID, b.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
										c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
										f.PER_STATUS as PER_STATUS4, a.POS_CONDITION, a.POS_REMARK, a.LEVEL_NO, a.PAY_NO, a.POS_SALARY
						from (
									(
										(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) left join PER_PERSONAL c on (a.POS_ID=c.POS_ID and c.PER_TYPE=1 and c.PER_STATUS=1)
										) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and d.PER_TYPE=1 and (d.PER_STATUS=0))
									) left join PER_PERSONAL e on (a.POS_ID=e.PAY_ID and e.PER_TYPE=1 and e.PER_STATUS=1)
								) left join PER_PERSONAL f on (a.POS_ID=f.PAY_ID  and f.PER_TYPE=1and (f.PER_STATUS=0))
						$search_condition $check_condition
						group by a.POS_ID, a.DEPARTMENT_ID, a.POS_NO_NAME, a.POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, a.POS_CONDITION,
										a.ORG_ID, b.ORG_SEQ_NO, b.ORG_CODE, b.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, c.PER_STATUS, d.PER_STATUS, 
										e.PER_STATUS, f.PER_STATUS, a.POS_REMARK, a.LEVEL_NO, a.PAY_NO, a.POS_SALARY
						order by $order_str   ";
	} // end if
	
	$count_data = $db_dpis->send_cmd($cmd);
        //echo '<pre>'.$cmd ;
	//====================================================================== setup Excel ===========================================================================
	require_once '../../Excel/eslip/Classes/PHPExcel.php';
	$object = new PHPExcel();
	$CH_PRINT_FONT = 3;
	$font_name = getToFont($CH_PRINT_FONT);
	$styleArray_head1 = array(
		'font'  => array(
			'bold'  => TRUE,
			'size'  => '16',
			'name'  => $font_name
	));
	$styleArray_head2 = array(
		'font'  => array(
			'bold'  => TRUE,
			'size'  => '14',
			'name'  => $font_name
	));
	
	$styleArray_head = array(
		'font'  => array(
			'bold'  => FALSE,
			'size'  => '14',
			'name'  => $font_name
	));
	$styleArray_body = array(
		'font'  => array(
			'bold'  => FALSE,
			'size'  => '14',
			'name'  => $font_name
	 ));
	//set border header
	$styleArray=array(
	'borders'=>array(
		'allborders'=>array(
			'style'=>PHPExcel_Style_Border::BORDER_THIN
				)
			)
	);
	//====================================================================== End setup Excel ===========================================================================

	if($count_data){
	//if(1==1){
		
		
		//================================================================== setup Header Excel =======================================================================
		//================================== Header 1 =================================================
		$array_header_col1 = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
		$array_header_col2 = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
		$h1 = array('เลขที่ตำแหน่ง');
		$h2_1 = array('');
		$h1_c = array(count($h2_1));

		$h2 = array('ตำแหน่งในการบริหารงาน');
		$h2_2 = array('');
		$h2_c = array(count($h2_2));

		$h3 = array('ตำแหน่งในสายงาน');
		$h2_3 = array('');
		$h3_c = array(count($h2_3));

		$h4 = array('ช่วงระดับตำแหน่ง');
		$h2_4 = array('');
		$h4_c = array(count($h2_4));

		$h5 = array("ประเภทตำแหน่ง\nตามพรบ. พ.ศ. 2551");
		$h2_5 = array('');
		$h5_c = array(count($h2_5));
		
		if($ORG_SETLEVEL==3){
			$h6 = array('ตามกฏหมาย');
			$h2_6 = array('กรม','สำนัก/กอง','ต่ำกว่าสำนัก/กอง 1 ระดับ','ต่ำกว่าสำนัก/กอง 2 ระดับ','ต่ำกว่าสำนัก/กอง 3 ระดับ');
			$h6_c = array(count($h2_6));
		}else if($ORG_SETLEVEL==4){
			$h6 = array('ตามกฏหมาย');
			$h2_6 = array('กรม','สำนัก/กอง','ต่ำกว่าสำนัก/กอง 1 ระดับ','ต่ำกว่าสำนัก/กอง 2 ระดับ','ต่ำกว่าสำนัก/กอง 3 ระดับ','ต่ำกว่าสำนัก/กอง 4 ระดับ');
			$h6_c = array(count($h2_6));
		}else if($ORG_SETLEVEL==5){
			$h6 = array('ตามกฏหมาย');
			$h2_6 = array('กรม','สำนัก/กอง','ต่ำกว่าสำนัก/กอง 1 ระดับ','ต่ำกว่าสำนัก/กอง 2 ระดับ','ต่ำกว่าสำนัก/กอง 3 ระดับ','ต่ำกว่าสำนัก/กอง 4 ระดับ','ต่ำกว่าสำนัก/กอง 5 ระดับ');
			$h6_c = array(count($h2_6));
		}else{
			$h6 = array('ตามกฏหมาย');
			$h2_6 = array('กรม','สำนัก/กอง','ต่ำกว่าสำนัก/กอง 1 ระดับ','ต่ำกว่าสำนัก/กอง 2 ระดับ');
			$h6_c = array(count($h2_6));
		}
		
		if($ORG_SETLEVEL==3){
			$h7 = array('ตามมอบหมายงาน');
			$h2_7 = array('กรม','สำนัก/กอง','ต่ำกว่าสำนัก/กอง 1 ระดับ','ต่ำกว่าสำนัก/กอง 2 ระดับ','ต่ำกว่าสำนัก/กอง 3 ระดับ');
			$h7_c = array(count($h2_7));
		}else if($ORG_SETLEVEL==4){
			$h7 = array('ตามมอบหมายงาน');
			$h2_7 = array('กรม','สำนัก/กอง','ต่ำกว่าสำนัก/กอง 1 ระดับ','ต่ำกว่าสำนัก/กอง 2 ระดับ','ต่ำกว่าสำนัก/กอง 3 ระดับ','ต่ำกว่าสำนัก/กอง 4 ระดับ');
			$h7_c = array(count($h2_7));
		}else if($ORG_SETLEVEL==5){
			$h7 = array('ตามมอบหมายงาน');
			$h2_7 = array('กรม','สำนัก/กอง','ต่ำกว่าสำนัก/กอง 1 ระดับ','ต่ำกว่าสำนัก/กอง 2 ระดับ','ต่ำกว่าสำนัก/กอง 3 ระดับ','ต่ำกว่าสำนัก/กอง 4 ระดับ','ต่ำกว่าสำนัก/กอง 5 ระดับ');
			$h7_c = array(count($h2_7));
		}else{
			$h7 = array('ตามมอบหมายงาน');
			$h2_7 = array('กรม','สำนัก/กอง','ต่ำกว่าสำนัก/กอง 1 ระดับ','ต่ำกว่าสำนัก/กอง 2 ระดับ');
			$h7_c = array(count($h2_7));
		}

		$h8 = array('ผู้ครองตำแหน่ง');
		$h2_8 = array('');
		$h8_c = array(count($h2_8));

		$h9 = array('วันที่ครองตำแหน่ง');
		$h2_9 = array('');
		$h9_c = array(count($h2_9));

		$h10 = array('ระดับตำแหน่ง');
		$h2_10 = array('');
		$h10_c = array(count($h2_10));

		$h11 = array('รายละเอียดเงื่อนไขตำแหน่ง');
		$h2_11 = array('');
		$h11_c = array(count($h2_11));

		$h12 = array('หมายเหตุ');
		$h2_12 = array('');
		$h12_c = array(count($h2_12));

		$h13 = array('อัตราเงินเดือนถือจ่าย');
		$h2_13 = array('');
		$h13_c = array(count($h2_13));

		$h14 = array('ใช้งาน/ยกเลิก');
		$h2_14 = array('');
		$h14_c = array(count($h2_14));
		
		
		$merge_name_h = array_merge($h1,$h2,$h3,$h4,$h5,$h6,$h7,$h8,$h9,$h10,$h11,$h12,$h13,$h14);
		$merge_name_h2 = array_merge($h2_1,$h2_2,$h2_3,$h2_4,$h2_5,$h2_6,$h2_7,$h2_8,$h2_9,$h2_10,$h2_11,$h2_12,$h2_13,$h2_14);
		$arr_count_h = array_merge($h1_c,$h2_c,$h3_c,$h4_c,$h5_c,$h6_c,$h7_c,$h8_c,$h9_c,$h10_c,$h11_c,$h12_c,$h13_c,$h14_c);
		$h_name_array = $merge_name_h;
		$h_name_array2 = $merge_name_h2;
		//================================== End Header 1 =================================================
		//===================================================================== End setup Header Excel ==================================================================
		$xlscnt = count($print_search_condition);
		$xlsRow_head = 1;
		$xlsRow = $xlsRow_head+$xlscnt;
		$xls_fRow = $xlsRow_head+$xlscnt+2;
		$xlsRow2 =$xlsRow+1;
		$cnt=0;
		
		$xlsRow++;
		$xls_fRow++;
		$xlsRow2++;
		if (1==1) {
			$fp = fopen($logfile, 'a');
			fwrite($fp, "Start!\r\n");

			fclose($fp);                    
		}
		
		//================================================loop header =============================================================
		for ($idx=0; $idx < count($arr_count_h); $idx++) { 
			for ($i=0; $i < ($arr_count_h[$idx]); $i++) { 
				if($i==0){
					$col1 = $array_header_col1[$cnt];
				}
				if($i==($arr_count_h[$idx]-1)){
				   $col2 = $array_header_col1[$cnt]; 
				}
				$cnt++;
				
			}
			$object->getActiveSheet()->getColumnDimension($col1)->setWidth('20')->setAutoSize(FALSE);
			//$object->getActiveSheet()->getColumnDimension($col1)->setAutoSize(TRUE);

			if($idx == 5 || $idx == 6){
				$object->setActiveSheetIndex(0)->mergeCells($col1.$xlsRow.':'.$col2.$xlsRow)->setCellValue($col1.$xlsRow, @iconv("tis-620", "utf-8",$h_name_array[$idx]) )->getStyle($col1.$xlsRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			}else if($idx == 4){
				$object->setActiveSheetIndex(0)->mergeCells($col1.$xlsRow.':'.$col1.$xlsRow2)->setCellValue($col1.$xlsRow, @iconv("tis-620", "utf-8",$h_name_array[$idx]) )->getStyle($col1.$xlsRow)->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			}else{
				$object->setActiveSheetIndex(0)->mergeCells($col1.$xlsRow.':'.$col1.$xlsRow2)->setCellValue($col1.$xlsRow, @iconv("tis-620", "utf-8",$h_name_array[$idx]) )->getStyle($col1.$xlsRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			}
		}

		$object->getActiveSheet()->getRowDimension($xlsRow)->setRowHeight(25);
		for($i=0;$i< count($h_name_array2);$i++){
			//$object->getActiveSheet()->getColumnDimension($array_header_col2[$i])->setAutoSize(TRUE);
			$object->setActiveSheetIndex(0)->setCellValue($array_header_col2[$i].$xlsRow2 , @iconv("tis-620", "utf-8",$h_name_array2[$i]) )->getStyle($array_header_col2[$i].$xlsRow2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			if(($i+1) == count($h_name_array2)){
				$object->getActiveSheet()->getStyle('A'.$xlsRow.':'.$array_header_col2[$i].$xlsRow2)->applyFromArray($styleArray_head2);
				//set bg header color
				$object->getActiveSheet()->getStyle('A'.$xlsRow.':'.$array_header_col2[$i].$xlsRow2)->getFill()
							->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
							->getStartColor()->setARGB('FFFFFF');
				$object->getActiveSheet()->getStyle('A'.$xlsRow.':'.$array_header_col2[$i].$xlsRow2)->applyFromArray($styleArray);
			}
			$end_col = $array_header_col2[$i];
		}
		//================================================End loop header =============================================================
		//================================================ F Header  ==================================================================
		$object->getActiveSheet()->getStyle('A1:'.$end_col.'2')->applyFromArray($styleArray_head1);
		$object->setActiveSheetIndex(0)->mergeCells('A1:'.$end_col.'1')->setCellValue('A1', @iconv('TIS-620','UTF-8',$report_title))->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		foreach($print_search_condition as $show_condition){
			$xlsRow_head++;
			$object->setActiveSheetIndex(0)->setCellValue('A'.$xlsRow_head, $show_condition)->getStyle('A'.$xlsRow_head)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		} // end foreach
		//================================================ End F Header  ==================================================================
		
		//============================================================ data ==========================================================
		//$data = $db_dpis->get_array();
		//while($xlsRow<=20){

		$data_count = $xlsRow+1;
		$idxs_cnt=0;
		while($data = $db_dpis->get_array()){	
			if(1==1){
				echo ".";
				ob_flush(); 
				flush();
			}

			if (1==1) {
				$fp = fopen($logfile, 'a');
				fwrite($fp, "$xlsRow!\r\n");

				fclose($fp);                    
			}
			$data_count++;
			$POS_ID = trim($data[POS_ID]);
			$POS_NO = trim($data[POS_NO_NAME]).trim($data[POS_NO]);
			$PAY_NO = trim($data[PAY_NO]);
			$ORG_ID = trim($data[ORG_ID]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
			$PM_CODE = trim($data[PM_CODE]);
			$PL_CODE = trim($data[PL_CODE]);
			$PT_CODE = trim($data[PT_CODE]);
			$CL_NAME = trim($data[CL_NAME]);
			$ORG_ID_1 = trim($data[ORG_ID_1]);
			$ORG_ID_2 = trim($data[ORG_ID_2]);
                        $ORG_ID_3 = trim($data[ORG_ID_3]);
                        $ORG_ID_4 = trim($data[ORG_ID_4]);
                        $ORG_ID_5 = trim($data[ORG_ID_5]);
                        
			$POS_CONDITION=  substr(trim($data[POS_CONDITION]),0,255) ;
			$POS_SALARY=trim($data[POS_SALARY]);
			$POS_REMARK = substr(trim($data[POS_REMARK]),0,255);
			$LEVEL_NO=trim($data[LEVEL_NO]);
			$POS_STATUS = (trim($data[POS_STATUS])==1)?"ใช้งาน":"ยกเลิก";
			//$POS_STATUS = (trim($data[POS_STATUS])==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";
	
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='".$PM_CODE."' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PM_NAME = $data_dpis2[PM_NAME];
	
			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='".$PL_CODE."' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PL_NAME = $data_dpis2[PL_NAME];
	
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='".$PT_CODE."' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PT_NAME = $data_dpis2[PT_NAME];
			$DEPARTMENT_NAME = "";
			if($DEPARTMENT_ID){
				$cmd = " select ORG_NAME from PER_ORG where OL_CODE='02' and ORG_ID=$DEPARTMENT_ID ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$DEPARTMENT_NAME = $data_dpis2[ORG_NAME];
			}
			$ORG_NAME_1 = "";
			if($ORG_ID_1){
				$cmd = " select ORG_NAME from PER_ORG where OL_CODE='04' and ORG_ID=$ORG_ID_1 ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$ORG_NAME_1 = $data_dpis2[ORG_NAME];
			}
			$ORG_NAME_2 = "";
			if($ORG_ID_2){
				$cmd = " select ORG_NAME from PER_ORG where OL_CODE='05' and ORG_ID=$ORG_ID_2 ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$ORG_NAME_2 = $data_dpis2[ORG_NAME];
			}
                        $ORG_NAME_3 = "";
			if($ORG_ID_3){
				$cmd = " select ORG_NAME from PER_ORG where OL_CODE='06' and ORG_ID=$ORG_ID_3 ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$ORG_NAME_3 = $data_dpis2[ORG_NAME];
			}
                        $ORG_NAME_4 = "";
			if($ORG_ID_4){
				$cmd = " select ORG_NAME from PER_ORG where OL_CODE='07' and ORG_ID=$ORG_ID_4 ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$ORG_NAME_4 = $data_dpis2[ORG_NAME];
			}
                        $ORG_NAME_5 = "";
			if($ORG_ID_5){
				$cmd = " select ORG_NAME from PER_ORG where OL_CODE='08' and ORG_ID=$ORG_ID_5 ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$ORG_NAME_5 = $data_dpis2[ORG_NAME];
			}       
			if (!$LEVEL_NO) {
				$cmd = " select LEVEL_NO_MIN from PER_CO_LEVEL where trim(CL_NAME)='$CL_NAME' ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$LEVEL_NO = $data_dpis2[LEVEL_NO_MIN];
			}
		
			$cmd = "select LEVEL_NAME, POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$LEVEL_NAME = $data_dpis2[LEVEL_NAME];
			$NEW_POSITION_TYPE = $data_dpis2[POSITION_TYPE];
			$POSITION_LEVEL = $data_dpis2[POSITION_LEVEL];

			$arr_temp = explode(" ", $LEVEL_NAME);
			$LEVEL_NAME =  $arr_temp[1];

			$cnt = 0;
			$PER_ID = $FULLNAME = $POH_DATE = "";
                        $DEPARTMENT_NAME_ASS = "";
                        $ORG_NAME_ASS = "";
                        $ORG_NAME_1_ASS = "";
                        $ORG_NAME_2_ASS = "";
                        
			$cmd = "	select PER_ID, PER_STATUS from	PER_PERSONAL where POS_ID=$POS_ID and PER_TYPE = 1 ";
			$db_dpis1->send_cmd($cmd);
			$getCnt=0;  /* Release 5.1.0.8 Begin*/
			while($data1 = $db_dpis1->get_array()){
				$cnt++;
				$PER_ID[$cnt] = trim($data1[PER_ID]);
				$PER_STATUS = $data1[PER_STATUS];
				if($PER_ID[$cnt] && $PER_STATUS != 2){
                                    $getCnt++; /* Release 5.1.0.8 Begin*/
					$cmd = " select 	b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_STATUS, DEPARTMENT_ID_ASS, ORG_ID, 
                                            ORG_ID_1, ORG_ID_2,ORG_ID_3,ORG_ID_4,ORG_ID_5
									 from 		PER_PERSONAL a, PER_PRENAME b 
									 where 	a.PN_CODE=b.PN_CODE and PER_ID=$PER_ID[$cnt] ";
					
                                       // echo $cmd."<br><br>";
                                        $db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
                                        
					/*เดิม*/
                                        /*$FULLNAME[$cnt] = (trim($data_dpis2[PN_NAME])?($data_dpis2[PN_NAME]):"") . $data_dpis2[PER_NAME] ." ". $data_dpis2[PER_SURNAME];
					$FULLNAME[$cnt] .= (($data_dpis2[PER_STATUS]==0)?" (รอบรรจุ)":"");*/
                                        /* Release 5.1.0.8 Begin*/
                                        $FULLNAME[$getCnt] = (trim($data_dpis2[PN_NAME])?($data_dpis2[PN_NAME]):"") . $data_dpis2[PER_NAME] ." ". $data_dpis2[PER_SURNAME];
					$FULLNAME[$getCnt] .= (($data_dpis2[PER_STATUS]==0)?" (รอบรรจุ)":"");
                                        /* Release 5.1.0.8 End*/
					$LEVEL_NO = trim($data_dpis2[LEVEL_NO]);
					$DEPARTMENT_ID_ASS = trim($data_dpis2[DEPARTMENT_ID_ASS]);
					$ORG_ID_ASS = trim($data_dpis2[ORG_ID]);
					$ORG_ID_1_ASS = trim($data_dpis2[ORG_ID_1]);
					$ORG_ID_2_ASS = trim($data_dpis2[ORG_ID_2]);
                                        $ORG_ID_3_ASS = trim($data_dpis2[ORG_ID_3]);
                                        $ORG_ID_4_ASS = trim($data_dpis2[ORG_ID_4]);
                                        $ORG_ID_5_ASS = trim($data_dpis2[ORG_ID_5]);

					$cmd = "select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
					$db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$LEVEL_NAME = $data_dpis2[LEVEL_NAME];
		
					/*$cmd = "select ORG_NAME from PER_ORG where ORG_ID='$DEPARTMENT_ID_ASS'";*//*เดิม*/
					$cmd = "select ORG_NAME from PER_ORG_ASS where ORG_ID='$DEPARTMENT_ID_ASS'";/*Release 5.1.0.4*/
					$db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$DEPARTMENT_NAME_ASS = $data_dpis2[ORG_NAME];
                                        
					/*$cmd = "select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID_ASS'";*//*เดิม*/
					$cmd = "select ORG_NAME from PER_ORG_ASS where ORG_ID='$ORG_ID_ASS'";/*Release 5.1.0.4*/
					$db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$ORG_NAME_ASS = $data_dpis2[ORG_NAME];
		
					/*$cmd = "select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID_1_ASS'";*//*เดิม*/
					$cmd = "select ORG_NAME from PER_ORG_ASS where ORG_ID='$ORG_ID_1_ASS'";/*Release 5.1.0.4*/
					$db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$ORG_NAME_1_ASS = $data_dpis2[ORG_NAME];
		
					/*$cmd = "select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID_2_ASS'";*//*เดิม*/
					$cmd = "select ORG_NAME from PER_ORG_ASS where ORG_ID='$ORG_ID_2_ASS'";/*Release 5.1.0.4*/
					$db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$ORG_NAME_2_ASS = $data_dpis2[ORG_NAME];
                                        
					$cmd = "select ORG_NAME from PER_ORG_ASS where ORG_ID='$ORG_ID_3_ASS'";/*Release 5.1.0.8*/
					$db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$ORG_NAME_3_ASS = $data_dpis2[ORG_NAME];
                                        
					$cmd = "select ORG_NAME from PER_ORG_ASS where ORG_ID='$ORG_ID_4_ASS'";/*Release 5.1.0.8*/
					$db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$ORG_NAME_4_ASS = $data_dpis2[ORG_NAME];
                                        
					$cmd = "select ORG_NAME from PER_ORG_ASS where ORG_ID='$ORG_ID_5_ASS'";/*Release 5.1.0.8*/
					$db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$ORG_NAME_5_ASS = $data_dpis2[ORG_NAME];
                                
					$arr_temp = explode(" ", $LEVEL_NAME);
					$LEVEL_NAME =  $arr_temp[1];
					$POS_SITUATION = 3;
			
					if (!$RPT_N) {
						$cmd = " select MS_SALARY from PER_MGTSALARY where trim(PT_CODE)='$PT_CODE' and trim(LEVEL_NO)='$LEVEL_NO' ";
						$count_mgtsalary = $db_dpis2->send_cmd($cmd);
						$data_dpis2 = $db_dpis2->get_array();
						$POS_MGTSALARY = $data_dpis2[MS_SALARY];
					}

					$cmd = " select POH_EFFECTIVEDATE, POH_POS_NO_NAME, POH_POS_NO, POH_ORG2
									from   PER_POSITIONHIS
									where PER_ID=$PER_ID[$cnt]
									order by POH_EFFECTIVEDATE desc, POH_SEQ_NO DESC ";
					$db_dpis2->send_cmd($cmd);
					while($data_dpis2 = $db_dpis2->get_array()){
						$POH_POS_NO_NAME = trim($data_dpis2[POH_POS_NO_NAME]);
						$POH_POS_NO = trim($data_dpis2[POH_POS_NO]);
						$POH_ORG2 = trim($data_dpis2[POH_ORG2]);
						if ($POH_POS_NO_NAME.$POH_POS_NO==$POS_NO && $POH_ORG2==$DEPARTMENT_NAME) 
							$POH_EFFECTIVEDATE = trim($data_dpis2[POH_EFFECTIVEDATE]);
						else break;
					}
					if ($POH_EFFECTIVEDATE) {	
						//$POH_DATE[$cnt] = show_date_format(substr($POH_EFFECTIVEDATE, 0, 10), 1); /*เดิม*/
                                           $POH_DATE[$getCnt] = show_date_format(substr($POH_EFFECTIVEDATE, 0, 10), 1); /* Release 5.1.0.8 Begin*/
					}
				}else{
					$POS_SITUATION = 1;
				} // end if
			} // end while
                        
			if ($MUNICIPALITY_FLAG==1) $POS_NO = pos_no_format($POS_NO,2);
			
			$xlsx_data1 = (($NUMBER_DISPLAY==2)?convert2thaidigit("$POS_NO"):"$POS_NO");
			$xlsx_data1_c = array($xlsx_data1);
			
			$xlsx_data2 = $PM_NAME;
			$xlsx_data2_c = array($xlsx_data2);

			$xlsx_data3 = $PL_NAME;
			$xlsx_data3_c = array($xlsx_data3);

			$xlsx_data4 = $CL_NAME;
			$xlsx_data4_c = array($xlsx_data4);
			
			$xlsx_data5 = $NEW_POSITION_TYPE;
			$xlsx_data5_c = array($xlsx_data5);
		
			$xlsx_data6 = '';
			$xlsx_data6_0 = '';
			$xlsx_data6_1 = '';
			$xlsx_data6_2 = '';
			$xlsx_data6_3 = '';
			$xlsx_data6_4 = '';
			$xlsx_data6_5 = '';
			if($ORG_SETLEVEL==3){
				$xlsx_data6 = $DEPARTMENT_NAME;
				$xlsx_data6_0 = $ORG_NAME;
				$xlsx_data6_1 = $ORG_NAME_1;
				$xlsx_data6_2 = $ORG_NAME_2;
				$xlsx_data6_3 = $ORG_NAME_3;

				$xlsx_data6_c = array($xlsx_data6,$xlsx_data6_0,$xlsx_data6_1,$xlsx_data6_2,$xlsx_data6_3);
			}else if($ORG_SETLEVEL==4){
				$xlsx_data6 = $DEPARTMENT_NAME;
				$xlsx_data6_0 = $ORG_NAME;
				$xlsx_data6_1 = $ORG_NAME_1;
				$xlsx_data6_2 = $ORG_NAME_2;
				$xlsx_data6_3 = $ORG_NAME_3;
				$xlsx_data6_4 = $ORG_NAME_4;

				$xlsx_data6_c = array($xlsx_data6,$xlsx_data6_0,$xlsx_data6_1,$xlsx_data6_2,$xlsx_data6_3,$xlsx_data6_4);

			}else if($ORG_SETLEVEL==5){
				$xlsx_data6 = $DEPARTMENT_NAME;
				$xlsx_data6_0 = $ORG_NAME;
				$xlsx_data6_1 = $ORG_NAME_1;
				$xlsx_data6_2 = $ORG_NAME_2;
				$xlsx_data6_3 = $ORG_NAME_3;
				$xlsx_data6_4 = $ORG_NAME_4;
				$xlsx_data6_5 = $ORG_NAME_5;

				$xlsx_data6_c = array($xlsx_data6,$xlsx_data6_0,$xlsx_data6_1,$xlsx_data6_2,$xlsx_data6_3,$xlsx_data6_4,$xlsx_data6_5);
			}else{
				$xlsx_data6 = $DEPARTMENT_NAME;
				$xlsx_data6_0 = $ORG_NAME;
				$xlsx_data6_1 = $ORG_NAME_1;
				$xlsx_data6_2 = $ORG_NAME_2;

				$xlsx_data6_c = array($xlsx_data6,$xlsx_data6_0,$xlsx_data6_1,$xlsx_data6_2);
			}
			
			$xlsx_data7 = '';
			$xlsx_data7_0 = '';
			$xlsx_data7_1 = '';
			$xlsx_data7_2 = '';
			$xlsx_data7_3 = '';
			$xlsx_data7_4 = '';
			$xlsx_data7_5 = '';
			if($ORG_SETLEVEL==3){
				$xlsx_data7 = $DEPARTMENT_NAME_ASS;
				$xlsx_data7_0 = $ORG_NAME_ASS;
				$xlsx_data7_1 = $ORG_NAME_1_ASS;
				$xlsx_data7_2 = $ORG_NAME_2_ASS;
				$xlsx_data7_3 = $ORG_NAME_3_ASS;

				$xlsx_data7_c = array($xlsx_data7,$xlsx_data7_0,$xlsx_data7_1,$xlsx_data7_2,$xlsx_data7_3);
			}else if($ORG_SETLEVEL==4){
				$xlsx_data7 = $DEPARTMENT_NAME_ASS;
				$xlsx_data7_0 = $ORG_NAME_ASS;
				$xlsx_data7_1 = $ORG_NAME_1_ASS;
				$xlsx_data7_2 = $ORG_NAME_2_ASS;
				$xlsx_data7_3 = $ORG_NAME_3_ASS;
				$xlsx_data7_4 = $ORG_NAME_4_ASS;

				$xlsx_data7_c = array($xlsx_data7,$xlsx_data7_0,$xlsx_data7_1,$xlsx_data7_2,$xlsx_data7_3,$xlsx_data7_4);
			}else if($ORG_SETLEVEL==5){
				$xlsx_data7 = $DEPARTMENT_NAME_ASS;
				$xlsx_data7_0 = $ORG_NAME_ASS;
				$xlsx_data7_1 = $ORG_NAME_1_ASS;
				$xlsx_data7_2 = $ORG_NAME_2_ASS;
				$xlsx_data7_3 = $ORG_NAME_3_ASS;
				$xlsx_data7_4 = $ORG_NAME_4_ASS;
				$xlsx_data7_5 = $ORG_NAME_5_ASS;

				$xlsx_data7_c = array($xlsx_data7,$xlsx_data7_0,$xlsx_data7_1,$xlsx_data7_2,$xlsx_data7_3,$xlsx_data7_4,$xlsx_data7_5);
			}else{
				$xlsx_data7 = $DEPARTMENT_NAME_ASS;
				$xlsx_data7_0 = $ORG_NAME_ASS;
				$xlsx_data7_1 = $ORG_NAME_1_ASS;
				$xlsx_data7_2 = $ORG_NAME_2_ASS;

				$xlsx_data7_c = array($xlsx_data7,$xlsx_data7_0,$xlsx_data7_1,$xlsx_data7_2);
			}

			$xlsx_data8 = $FULLNAME[1];
			$xlsx_data8_c = array($xlsx_data8);

			$xlsx_data9 = (($NUMBER_DISPLAY==2)?convert2thaidigit("$POH_DATE[1]"):"$POH_DATE[1]");
			$xlsx_data9_c = array($xlsx_data9);

			$xlsx_data10 = $LEVEL_NAME;
			$xlsx_data10_c = array($xlsx_data10);

			$xlsx_data11 = $POS_CONDITION;
			$xlsx_data11_c = array($xlsx_data11);

			$xlsx_data12 = $POS_REMARK;
			$xlsx_data12_c = array($xlsx_data12);

			$xlsx_data13 = (($NUMBER_DISPLAY==2)?convert2thaidigit("$POS_SALARY"):"$POS_SALARY");
			$xlsx_data13_c = array($xlsx_data13);

			$xlsx_data14 = $POS_STATUS;
			$xlsx_data14_c = array($xlsx_data14);
			
			$xlsRow = $data_count;
			$array_merge_data = array_merge($xlsx_data1_c,$xlsx_data2_c,$xlsx_data3_c,$xlsx_data4_c,$xlsx_data5_c,$xlsx_data6_c,$xlsx_data7_c,$xlsx_data8_c,$xlsx_data9_c,$xlsx_data10_c,$xlsx_data11_c,$xlsx_data12_c,$xlsx_data13_c,$xlsx_data14_c);
			//$array_merge_data = array_merge($xlsx_data1_c,$xlsx_data2_c,$xlsx_data3_c,$xlsx_data4_c,$xlsx_data5_c,$xlsx_data6_c,$xlsx_data7_c,$xlsx_data8_c,$xlsx_data9_c,$xlsx_data10_c,$xlsx_data11_c,$xlsx_data12_c,$xlsx_data13_c,$xlsx_data14_c);
			//var_dump($array_merge_data);
			//die();
			$end_row ="";
			for($i=0;$i<count($h_name_array2);$i++){
				if($i==0 || $i==3 || $i==4 || $i==count($h_name_array2)-1){
					$object->getActiveSheet()->setCellValue($array_header_col2[$i].$xlsRow, @iconv('TIS-620','UTF-8',"$array_merge_data[$i]"))->getStyle($array_header_col2[$i].$xlsRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				}else if($i==count($h_name_array2)-2){
					$object->getActiveSheet()->setCellValue($array_header_col2[$i].$xlsRow, @iconv('TIS-620','UTF-8',"$array_merge_data[$i]"))->getStyle($array_header_col2[$i].$xlsRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				}else{
					$object->getActiveSheet()->setCellValue($array_header_col2[$i].$xlsRow, @iconv('TIS-620','UTF-8',"$array_merge_data[$i]"))->getStyle($array_header_col2[$i].$xlsRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				}
				$end_row = $xlsRow;	
			}

			//if (1==1) {
				
			if ($FULLNAME[2]) {
				$data_count++;
				$xlsRow = $data_count;
				for($i=0;$i<count($h_name_array2);$i++){
					if($i==count($h_name_array2)-7){
						$object->getActiveSheet()->setCellValue($array_header_col2[$i].$xlsRow, @iconv('TIS-620','UTF-8',"$FULLNAME[2]"))->getStyle($array_header_col2[$i].$xlsRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					}else if($i==count($h_name_array2)-6){
						$object->getActiveSheet()->setCellValue($array_header_col2[$i].$xlsRow, @iconv('TIS-620','UTF-8',"$POH_DATE[2]"))->getStyle($array_header_col2[$i].$xlsRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					}else{
						$object->getActiveSheet()->setCellValue($array_header_col2[$i].$xlsRow, @iconv('TIS-620','UTF-8',""));
					}
					$end_row = $xlsRow;	
				}
			}	
			if ($FULLNAME[3]) {
				$data_count++;
				$xlsRow = $data_count;
				for($i=0;$i<count($h_name_array2);$i++){
					if($i==count($h_name_array2)-7){
						$object->getActiveSheet()->setCellValue($array_header_col2[$i].$xlsRow, @iconv('TIS-620','UTF-8',"$FULLNAME[3]"))->getStyle($array_header_col2[$i].$xlsRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					}else if($i==count($h_name_array2)-6){
						$object->getActiveSheet()->setCellValue($array_header_col2[$i].$xlsRow, @iconv('TIS-620','UTF-8',"$POH_DATE[3]"))->getStyle($array_header_col2[$i].$xlsRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					}else{
						$object->getActiveSheet()->setCellValue($array_header_col2[$i].$xlsRow, @iconv('TIS-620','UTF-8',""));
					}
					$end_row = $xlsRow;	
				}
			}	
			if ($FULLNAME[4]) {
				$data_count++;
				$xlsRow = $data_count;
				for($i=0;$i<count($h_name_array2);$i++){
					if($i==count($h_name_array2)-7){
						$object->getActiveSheet()->setCellValue($array_header_col2[$i].$xlsRow, @iconv('TIS-620','UTF-8',"$FULLNAME[4]"))->getStyle($array_header_col2[$i].$xlsRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					}else if($i==count($h_name_array2)-6){
						$object->getActiveSheet()->setCellValue($array_header_col2[$i].$xlsRow, @iconv('TIS-620','UTF-8',"$POH_DATE[4]"))->getStyle($array_header_col2[$i].$xlsRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					}else{
						$object->getActiveSheet()->setCellValue($array_header_col2[$i].$xlsRow, @iconv('TIS-620','UTF-8',""));
					}
					$end_row = $xlsRow;	
				}
			}	

			
		}	
		$object->getActiveSheet()->getStyle('A'.$xls_fRow.':'.$end_col.$end_row)->applyFromArray($styleArray);
		$object->getActiveSheet()->getStyle('A'.$xls_fRow.':'.$end_col.$end_row)->applyFromArray($styleArray_body);
		$object->getActiveSheet()->getStyle('A'.$xls_fRow.':'.$end_col.$end_row)->getFill()
					->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
					->getStartColor()->setARGB('FFFFFF');
		//$object->getActiveSheet()->getStyle('A'.$irow.':'.$end_col.$irow)->getFont()->setBold(TRUE);
		
		//============================================================ End data ==========================================================
	}else{
		//ไม่มีข้อมูล
		$xlsRow++;
		$object->getActiveSheet()->getStyle('A1:G2')->applyFromArray($styleArray_head);
		$object->setActiveSheetIndex(0)->mergeCells('A1:G1')->setCellValue('A1', @iconv('TIS-620','UTF-8','**** ไม่พบข้อมูล ****'))->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//$object->setActiveSheetIndex(0)->mergeCells('A2:F2')->setCellValue('A2', @iconv('TIS-620','UTF-8','ประวัติสำหรับเสนอขอพระราชทานเหรียญจักรพรรดิมาลา'))->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	}

	if (1==1) {              
		$fp = fopen($logfile, 'a');

		fwrite($fp, "End\r\n");
		fclose($fp);                    
	}

	$file_name = "../../Excel/tmp/S0201/rpt_S0201.xlsx";
	$objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
	$objWriter->save($file_name);
	unlink($logfile);
	/*if(file_exists($file_name )){
		unlink($logfile);
		echo "<br><a href='".$file_name."'>S0201.xlsx คลิกเพื่อ Download</a><br>";
	}*/
?>