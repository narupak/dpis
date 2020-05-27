<?php
    include("../../php_scripts/connect_database.php");
    include("../../php_scripts/calendar_data.php");
    include ("../php_scripts/function_share.php");
    include ("../report/rpt_function.php");

    require_once "../../Excel/class.writeexcel_workbook.inc.php";
    require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
    //ini_set("max_execution_time", $max_execution_time);
    //set_time_limit(0);
    //set_time_limit(0);
    //ini_set("oci8.statement_cache_size","0");
    ini_set("max_execution_time",0);
    ini_set("memory_limit","9999M"); 
    
    
    
    
	
    $db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    
    $debug=0;//0=close 1=open
    
    /*INDEX BEGIN*/
    if(1==0){
        //PER_POSITION
        $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_POSITION' AND COLUMN_NAME='PL_CODE' ";
        $db_dpis2->send_cmd($cmd);
        $data_index = $db_dpis2->get_array();
        $CNT = trim($data_index[CNT]);
        if($CNT==0){
            $cmd = " CREATE INDEX IDX_PL_CODE ON PER_POSITION (PL_CODE) ";
            $db_dpis2->send_cmd($cmd);
        }
        
        //PER_POSITIONHIS
        $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_POSITIONHIS' AND COLUMN_NAME='LEVEL_NO' ";
        $db_dpis2->send_cmd($cmd);
        $data_index = $db_dpis2->get_array();
        $CNT = trim($data_index[CNT]);
        if($CNT==0){
            $cmd = " CREATE INDEX IDX_LEVEL_NO ON PER_POSITIONHIS (LEVEL_NO) ";
            $db_dpis2->send_cmd($cmd);
        }
        $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_POSITIONHIS' AND COLUMN_NAME='POH_LEVEL_NO' ";
        $db_dpis2->send_cmd($cmd);
        $data_index = $db_dpis2->get_array();
        $CNT = trim($data_index[CNT]);
        if($CNT==0){
            $cmd = " CREATE INDEX IDX_POH_LEVEL_NO ON PER_POSITIONHIS (POH_LEVEL_NO) ";
            $db_dpis2->send_cmd($cmd);
        }
        
        //PER_DECORATION
        $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_DECORATION' AND COLUMN_NAME='DC_TYPE' ";
        $db_dpis2->send_cmd($cmd);
        $data_index = $db_dpis2->get_array();
        $CNT = trim($data_index[CNT]);
        if($CNT==0){
            $cmd = " CREATE INDEX IDX_DC_TYPE ON PER_DECORATION (DC_TYPE) ";
            $db_dpis2->send_cmd($cmd);
        }
        
        //PER_EDUCATE
        $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_EDUCATE' AND COLUMN_NAME='EN_CODE' ";
        $db_dpis2->send_cmd($cmd);
        $data_index = $db_dpis2->get_array();
        $CNT = trim($data_index[CNT]);
        if($CNT==0){
            $cmd = " CREATE INDEX IDX_EN_CODE ON PER_EDUCATE (EN_CODE) ";
            $db_dpis2->send_cmd($cmd);
        }
        $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_EDUCATE' AND COLUMN_NAME='EM_CODE' ";
        $db_dpis2->send_cmd($cmd);
        $data_index = $db_dpis2->get_array();
        $CNT = trim($data_index[CNT]);
        if($CNT==0){
            $cmd = " CREATE INDEX IDX_EM_CODE ON PER_EDUCATE (EM_CODE) ";
            $db_dpis2->send_cmd($cmd);
        }
        
        //PER_EDUCNAME
        $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_EDUCNAME' AND COLUMN_NAME='EL_CODE' ";
        $db_dpis2->send_cmd($cmd);
        $data_index = $db_dpis2->get_array();
        $CNT = trim($data_index[CNT]);
        if($CNT==0){
            $cmd = " CREATE INDEX IDX_EL_CODE ON PER_EDUCNAME (EL_CODE) ";
            $db_dpis2->send_cmd($cmd);
        }
        
        //PER_SPECIAL_SKILL
        $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_SPECIAL_SKILL' AND COLUMN_NAME='SS_CODE' ";
        $db_dpis2->send_cmd($cmd);
        $data_index = $db_dpis2->get_array();
        $CNT = trim($data_index[CNT]);
        if($CNT==0){
            $cmd = " CREATE INDEX IDX_SS_CODE ON PER_SPECIAL_SKILL (SS_CODE) ";
            $db_dpis2->send_cmd($cmd);
        }
        
        
    }
    
    
    
    
    
    
    
    
    /*INDEX END*/
    
    
    
        
    include ("rpt_personal_inquiry_xls_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น
    $arr_filename = array();
    $token = md5(uniqid(rand(), true)); 
    $dir_folder = file_exists("../../Excel/tmp/p0119");
    if($dir_folder==0){
        mkdir("../../Excel/tmp/p0119"); 
    }
	
    $files = glob('../../Excel/tmp/p0119/*'); // get all file names
    foreach($files as $file){ // iterate files
      if(is_file($file))
        unlink($file); // delete file
    }
    
    
	$fname= "../../Excel/tmp/p0119/dpis_".$token."_1.xls";
        $arr_filename[] =$fname;
    
	if($report_title=="P0119")	$report_title = "รายงานสอบถามข้อมูลบุคคล";
	if(!$report_title)	$report_title = "รายงานสอบถามข้อมูลบุคคล";



	$workbook = new writeexcel_workbook($fname);
        
        //====================== SET FORMAT ======================//
        require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
        //====================== SET FORMAT ======================//
        
	$worksheet = &$workbook->addworksheet("$report_title");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน
		$ws_head_line1 = (array) null;
		$ws_head_line2 = (array) null;
		$ws_colmerge_line1 = (array) null;
		$ws_colmerge_line2 = (array) null;
		$ws_border_line1 = (array) null;
		$ws_border_line2 = (array) null;
		$ws_fontfmt_line1 = (array) null;
		$ws_headalign_line1 = (array) null;
		$ws_width = (array) null;
		for($i=0; $i < count($heading_text); $i++) {
			$buff = explode("|", $heading_text[$i]);
			$ws_head_line1[$i] = $buff[0];
			$ws_head_line2[$i] = $buff[1];
			$ws_colmerge_line1[$i] = 0;
			$ws_colmerge_line2[$i] = 0;
			$ws_border_line1[$i] = "TLR";
			$ws_border_line2[$i] = "LBR";
			$ws_fontfmt_line1[$i] = "B";
			$ws_headalign_line1[$i] = "C";
		}
                //die(">>>".$MFA_FLAG);
		if ($MFA_FLAG==1) 
			$ws_width = array(10,25,18,15,25,25,15,20,30,30,30,30,15,15,15,15,15,20,25,25,30,20,25,25,30,20,25,25,30,20,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,30,12,35,25,10,25,25,10,30,30,30,40);
		elseif ($BKK_FLAG==1) 
			$ws_width = array(10,25,18,15,25,25,15,20,30,30,30,15,15,15,15,15,20,25,25,30,20,25,25,30,20,25,25,30,20,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,30,12);
		else
			//$ws_width = array(10,25,18,15,25,25,15,20,30,30,30,30,15,15,15,15,15,20,25,25,30,20,25,25,30,20,25,25,30,20,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,30,12);
                        $ws_width = array(50 /*เลขที่ตำแหน่ง*/,
                            22 /*คำนำหน้าชื่อ(ไทย)*/,
                            30 /*ชื่อ(ไทย)*/,
                            30 /*นามสกุล(ไทย)*/,
                            25 /*คำนำหน้าชื่อ(อังกฤษ)*/,
                            35 /*ชื่อ(อังกฤษ)*/,
                            30 /*นามสกุล(อังกฤษ)*/,
                            30 /*เลขประจำตัวประชาชน*/,
                            15 /*Release 5.1.0.4 Begin แทรก เพศ*/,
                            25 /*วัน/เดือน/ปีเกิด*/,
                            60 /*อายุข้าราชการ   Release 5.2.1.18  */,
                            40 /*ตำแหน่งในสายงาน*/,
                            40 /*ตำแหน่งในการบริหารงาน*/,
                            30 /*กระทรวง*/,
                            30 /*กรม*/,
                            30 /*สำนัก/กอง*/,
                            30 /*ต่ำกว่าสำนัก/กอง 1 ระดับ*/,
                            15 /*ต่ำกว่าสำนัก/กอง 2 ระดับ*/,
                            15 /*เงินเดือน*/,
                            15 /*เงินประจำตำแหน่ง*/,
                            15 /*วันเกษียณอายุ*/,
                            15 /*วันที่เริ่มรับราชการ*/,
                            60 /*อายุราชการ(อายุงาน)*/,
                            20 /*วันที่เข้าส่วนราชการ*/,
                            25 /*ระดับการศึกษา*/,
                            25 /*วุฒิการศึกษา*/,
                            25 /**/,
                            25 /**/,
                            30 /*วุฒิที่ใช้บรรจุสาขาวิชาเอก*/,
                            20 /*วุฒิที่ใช้บรรจุสาขาวิชาเอก*/,
                            25 /*สถาบันการศึกษา*/,
                            25 /*ระดับการศึกษา*/,
                            30 /*วุฒิการศึกษา*/,
                            20 /*วุฒิในตำแหน่งปัจจุบันสาขาวิชาเอก*/,
                            25 /*สถาบันการศึกษา*/,
                            25 /*ระดับการศึกษา*/,
                            30/*วุฒิการศึกษา*/,
                            20/*วุฒิสูงสุดสาขาวิชาเอก*/,
                            12/*สถาบันการศึกษา*/,
                            12/*เครื่องราช ฯ*/,
                            12/*ระดับ 1*/,
							12/*สำนักกอง*/,
                            12/*ระดับ 2*/,
							12/*สำนักกอง*/,
                            12/*ระดับ 3*/,
							12/*สำนักกอง*/,
                            12/*ระดับ 4*/,
							12/*สำนักกอง*/,
                            12/*ระดับ 5*/,
							12/*สำนักกอง*/,
                            12/*ระดับ 6*/,
							12/*สำนักกอง*/,
                            12/*ระดับ 7*/,
							12/*สำนักกอง*/,
                            12/*ระดับ 8*/,
							12/*สำนักกอง*/,
                            12/*ระดับ 9*/,
							12/*สำนักกอง*/,
                            12/*ระดับ 10*/,
							12/*สำนักกอง*/,
                            12/*ระดับ 11*/,
							12/*สำนักกอง*/,
                            12/*ปฏิบัติงาน	ชำนาญงาน*/,
							12/*สำนักกอง*/,
                            12/*อาวุโส*/,
							12/*สำนักกอง*/,
                            12/*ทักษะพิเศษ*/,
							12/*สำนักกอง*/,
                            12/*ปฏิบัติการ	ชำนาญการ*/,
							12/*สำนักกอง*/,
                            12/*ชำนาญการพิเศษ*/,
							12/*สำนักกอง*/,
                            12/*เชี่ยวชาญ*/,
							12/*สำนักกอง*/,
                            12/*ทรงคุณวุฒิ*/,
							12/*สำนักกอง*/,
                            12/*อำนวยการต้น*/,
							12/*สำนักกอง*/,
                            12/*อำนวยการสูง*/,
							12/*สำนักกอง*/,
                            12/*บริหารต้น*/,
							12/*สำนักกอง*/,
                            12/*บริหารสูง*/,
							12/*สำนักกอง*/,
                            12/*วันที่เข้าสู่ระดับปัจจุบัน*/,
                            12/*ฐานในการคำนวณ*/,
                            12/*สำนัก/กองมอบหมายงาน*/,
                            30/*เลขที่แฟ้ม*/,
                            12/**/,
                            12/**/);


	// จบการกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน	

	



// คำนวนเปลียบเทียบค่า $ws_width ใหม่ เทียบกับ $heading_width
//		echo "bf..ws_width=".implode(",",$ws_width)."<br>";
		$sum_hdw = 0;
		$sum_wsw = 0;
		for($h = 0; $h < count($heading_width); $h++) {
			$sum_wsw += $ws_width[$h];	// ws_width ยังไม่ได้ บวก ความกว้าง ตัวที่ถูกตัดเข้าไป
			if ($arr_column_sel[$h]==1) {
				$sum_hdw += $heading_width[$h];
			}
		}
		// บัญญัติไตรยางค์   ยอดรวมความกว้าง column ใน heading_width เทียบกับ ยอดรวมใน ws_width
		//                                แต่ละ column ใน ws_width[$h] = sum(ws_width) /sum(heading_width) * heading_width[$h]
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1) {
				$ws_width[$h] = $sum_wsw / $sum_hdw * $heading_width[$h];
			}
		}
//		echo "af..ws_width=".implode(",",$ws_width)."<br>";
	// จบการเทียบค่าคำนวนเปลียบเทียบค่า $ws_width ใหม่ เทียบกับ $heading_width
	function get_chind_special_skill($db_dpis2,$SS_CODE){
            $cmd = "SELECT SS_NAME 
                    FROM PER_SPECIAL_SKILLGRP
                    WHERE trim(SS_CODE) = trim('$SS_CODE')
                    START WITH REF_CODE IS null
                    CONNECT BY NOCYCLE PRIOR trim(SS_CODE) = trim(REF_CODE)";
            $db_dpis2->send_cmd($cmd);
            $data2 = $db_dpis2->get_array();
            $REF_CODE = trim($data2[SS_NAME]);
            return $REF_CODE;
            
        }
	function print_header(){
		global $worksheet, $xlsRow, $Row;
		global $heading_name, $MINISTRY_TITLE, $DEPARTMENT_TITLE, $ORG_TITLE, $ORG_TITLE1, $ORG_TITLE2, $BKK_FLAG;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width;
		global $ws_head_line1, $ws_head_line2,  $ws_colmerge_line1, $ws_colmerge_line2;
		global $ws_border_line1, $ws_border_line2, $ws_fontfmt_line1, $ws_fontfmt_line2;
		global $ws_headalign_line1, $ws_headalign_line2, $ws_width;

		// loop กำหนดความกว้างของ column
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->set_column($colseq, $colseq, $ws_width[$arr_column_map[$i]]);
//				echo "$i-map[".$arr_column_map[$i]."], width=".$ws_width[$arr_column_map[$i]]."<br />";
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 1
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 2
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) { 	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->write($xlsRow, $colseq, $ws_head_line2[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]]));
				$colseq++;
			}
		}
	} // end if

	if(!isset($search_per_type)) $search_per_type = 1;
	if(!isset($search_per_status) && $command != "SEARCH") $search_per_status = array(1);
	if(!isset($search_per_gender) && $command != "SEARCH") $search_per_gender = array(1, 2);
	if(!isset($search_per_ordain) && $command != "SEARCH") $search_per_ordain = array(0, 1);
	if(!isset($search_per_soldier) && $command != "SEARCH") $search_per_soldier = array(0, 1);
	if ($BKK_FLAG!=1) if(!isset($search_per_member) && $command != "SEARCH") $search_per_member = array(0, 1);
	if(!isset($search_per_punishment) && $command != "SEARCH") $search_per_punishment = array(0, 1);
        if(!isset($search_per_toskill) && $command != "SEARCH") $search_per_toskill = array(0, 1, 2);
	if(!isset($search_per_invest) && $command != "SEARCH") $search_per_invest = array(0, 1);
	if ($BKK_FLAG!=1) if(!isset($search_hip_flag) && $command != "SEARCH") $search_hip_flag = array(0, 1, 2, 3, 4, 5, 6);
	if(!isset($EDU_TYPE) && $command != "SEARCH") $EDU_TYPE = array(1, 2, 3, 4, 5);
	
        
  	if($search_department_id){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
	}elseif($search_ministry_id){
		unset($arr_department);
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id and OL_CODE='02' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	}elseif($PROVINCE_CODE){
		$cmd = " select ORG_ID_REF from PER_ORG where PV_CODE='$PROVINCE_CODE' and OL_CODE='03' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID_REF];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	} // end if
	
	/* ================= 	ข้อมูลทั่วไป    ===================== */
        
        if($search_per_disability){$arr_search_condition[] = "(a.per_disability in (". implode(",", $search_per_disability) ."))";}
  	if(trim($search_per_name)) $arr_search_condition[] = "(a.PER_NAME like '$search_per_name%' or UPPER(a.PER_ENG_NAME) like UPPER('$search_per_name%'))";
  	if(trim($search_per_surname)) $arr_search_condition[] = "(a.PER_SURNAME like '$search_per_surname%' or UPPER(a.PER_ENG_SURNAME) like UPPER('$search_per_surname%'))";
	if(trim($search_pn_code)) $arr_search_condition[] = "(a.PN_CODE='$search_pn_code')";
	if(trim($search_mr_code)) $arr_search_condition[] = "(a.MR_CODE='$search_mr_code')";
  	if(trim($search_per_cardno)) $arr_search_condition[] = "(a.PER_CARDNO like '$search_per_cardno%')";
  	if(trim($search_per_offno)) $arr_search_condition[] = "(a.PER_OFFNO like '$search_per_offno%')";
	if(trim($search_per_type)) $arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	if(trim($search_per_blood)) $arr_search_condition[] = "(trim(a.PER_BLOOD) = '$search_per_blood')";
	if(trim($search_re_code)) $arr_search_condition[] = "(a.RE_CODE='$search_re_code')";
	if(trim($search_pv_code)) $arr_search_condition[] = "(a.PV_CODE='$search_pv_code')";
	if(trim($search_es_code)) $arr_search_condition[] = "(a.ES_CODE='$search_es_code')";
  	if(trim($search_per_file_no)) $arr_search_condition[] = "(a.PER_FILE_NO='$search_per_file_no')";
  	if(trim($search_per_cooperative_no)) $arr_search_condition[] = "(a.PER_COOPERATIVE_NO like '$search_per_cooperative_no%')";
	if(trim($search_per_birthdate_min)){
		$search_per_birthdate_min =  save_date($search_per_birthdate_min);
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_BIRTHDATE, 10) >= '$search_per_birthdate_min')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_BIRTHDATE, 1, 10) >= '$search_per_birthdate_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_BIRTHDATE, 10) >= '$search_per_birthdate_min')";
		$search_per_birthdate_min = show_date_format($search_per_birthdate_min, 1);
	} // end if
	if(trim($search_per_birthdate_max)){
		$search_per_birthdate_max =  save_date($search_per_birthdate_max);
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_BIRTHDATE, 10) <= '$search_per_birthdate_max')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_BIRTHDATE, 1, 10) <= '$search_per_birthdate_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_BIRTHDATE, 10) <= '$search_per_birthdate_max')";
		$search_per_birthdate_max = show_date_format($search_per_birthdate_max, 1);
	} // end if
	if(trim($search_per_age_min)){
//			คิดตามอายุจริง (ถ้ายังไม่ถึงวันเกิดจะไม่นับเป็นปี)
		if ($search_per_age_date) $per_age_date = save_date($search_per_age_date,1);
		else $per_age_date = date("Y-m-d");
		$birthdate_min = date_adjust($per_age_date, "y", ($search_per_age_min * -1));
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.PER_BIRTHDATE), 10) <= '$birthdate_min')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) <= '$birthdate_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(a.PER_BIRTHDATE), 10) <= '$birthdate_min')";

//			คิดเฉพาะปีเกิด กับปีปัจจุบัน
//			$birthyear_min = date("Y") - $search_per_age_min;
//			if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.PER_BIRTHDATE), 4) >= '$birthyear_min')";
//			elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.PER_BIRTHDATE), 1, 4) >= '$birthyear_min')";
//			elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(a.PER_BIRTHDATE), 4) >= '$birthyear_min')";
	} // end if
	if(trim($search_per_age_max)){
//			คิดตามอายุจริง (ถ้ายังไม่ถึงวันเกิดจะไม่นับเป็นปี)
		if ($search_per_age_date) $per_age_date = save_date($search_per_age_date,1);
		else $per_age_date = date("Y-m-d");
		$birthdate_max = date_adjust($per_age_date, "y", ($search_per_age_max * -1));
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.PER_BIRTHDATE), 10) >= '$birthdate_max')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) >= '$birthdate_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(a.PER_BIRTHDATE), 10) >= '$birthdate_max')";

//			คิดเฉพาะปีเกิด กับปีปัจจุบัน
//			$birthyear_max = date("Y") - $search_per_age_max;
//			if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.PER_BIRTHDATE), 4) <= '$birthyear_max')";
//			elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.PER_BIRTHDATE), 1, 4) <= '$birthyear_max')";
//			elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(a.PER_BIRTHDATE), 4) <= '$birthyear_max')";
	} // end if
	
		if ($search_service_age_min || $search_service_age_max){
			if($search_service_age_min == $search_service_age_max) $search_service_age_max += 1;
			if($search_service_age_min){
				if ($search_service_age_date) $service_age_date = save_date($search_service_age_date,1);
				else $service_age_date = date("Y-m-d");
				$startdate_min = date_adjust($service_age_date, "y", ($search_service_age_min * -1));
				if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.PER_STARTDATE), 10) <= '$startdate_min')";
				elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.PER_STARTDATE), 1, 10) <= '$startdate_min')";
				elseif($DPISDB=="mysql") $arr_search_condition[] = "(SUBSTRING(trim(a.PER_STARTDATE), 1, 10) <= '$startdate_min')";
			} // end if
			if($search_service_age_max){
				if ($search_service_age_date) $service_age_date = save_date($search_service_age_date,1);
				else $service_age_date = date("Y-m-d");
				$startdate_max = date_adjust($service_age_date, "y", ($search_service_age_max * -1));
				if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.PER_STARTDATE), 10) >= '$startdate_max')";
				elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.PER_STARTDATE), 1, 10) >= '$startdate_max')";
				elseif($DPISDB=="mysql") $arr_search_condition[] = "(SUBSTRING(trim(a.PER_STARTDATE), 1, 10) >= '$startdate_max')";
			} // end if
		} // end if
	
  	if(trim($search_per_taxno)) $arr_search_condition[] = "(a.PER_TAXNO like '$search_per_taxno%')";
  	if(trim($search_per_pos_orgmgt)) $arr_search_condition[] = "(a.PER_POS_ORGMGT like '%$search_per_pos_orgmgt%')";
	if(trim($search_ot_code)) $arr_search_condition[] = "(a.OT_CODE='$search_ot_code')";
	if(trim($search_per_startdate_min)){
		$search_per_startdate_min =  save_date($search_per_startdate_min);
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_STARTDATE, 10) >= '$search_per_startdate_min')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_STARTDATE, 1, 10) >= '$search_per_startdate_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_STARTDATE, 10) >= '$search_per_startdate_min')";
		$search_per_startdate_min = show_date_format($search_per_startdate_min, 1);
	} // end if
	if(trim($search_per_startdate_max)){
		$search_per_startdate_max =  save_date($search_per_startdate_max);
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_STARTDATE, 10) <= '$search_per_startdate_max')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_STARTDATE, 1, 10) <= '$search_per_startdate_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_STARTDATE, 10) <= '$search_per_startdate_max')";
		$search_per_startdate_max = show_date_format($search_per_startdate_max, 1);
	} // end if
	if(trim($search_per_occupydate_min)){
		$search_per_occupydate_min =  save_date($search_per_occupydate_min);
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_OCCUPYDATE, 10) >= '$search_per_occupydate_min')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_OCCUPYDATE, 1, 10) >= '$search_per_occupydate_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_OCCUPYDATE, 10) >= '$search_per_occupydate_min')";
		$search_per_occupydate_min = show_date_format($search_per_occupydate_min, 1);
	} // end if
	if(trim($search_per_occupydate_max)){
		$search_per_occupydate_max =  save_date($search_per_occupydate_max);
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_OCCUPYDATE, 10) <= '$search_per_occupydate_max')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_OCCUPYDATE, 1, 10) <= '$search_per_occupydate_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_OCCUPYDATE, 10) <= '$search_per_occupydate_max')";
		$search_per_occupydate_max = show_date_format($search_per_occupydate_max, 1);
	} // end if
	if(count($search_per_punishment)){
		$cmd = " select distinct PER_ID from PER_PUNISHMENT order by PER_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while($data = $db_dpis->get_array()) $arr_punishment[] = $data[PER_ID];
		
		if(in_array(1, $search_per_punishment) && !in_array(0, $search_per_punishment)){
			// เคยรับโทษ
			$arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_punishment) ."))";
		}elseif(!in_array(1, $search_per_punishment) && in_array(0, $search_per_punishment)){
			// ไม่เคยรับโทษ
			$arr_search_condition[] = "(a.PER_ID not in (". implode(",", $arr_punishment) ."))";
		} // end if
//	}else{
//		$arr_search_condition[] = "(a.PER_ID in ())";
	} // end if
	
        if(count($search_per_toskill)){
		if(in_array(1, $search_per_toskill) && !in_array(0, $search_per_toskill) && !in_array(2, $search_per_toskill)){
                $cmd = " select distinct PER_ID from PER_SPECIAL_SKILL WHERE SS_CODE != '-1' order by PER_ID ";
                }else if(!in_array(1, $search_per_toskill) && in_array(0, $search_per_toskill) && !in_array(2, $search_per_toskill)){
                    $cmd = " select distinct PER_ID from PER_SPECIAL_SKILL WHERE SS_CODE = '-1' order by PER_ID ";
                } else if(!in_array(1, $search_per_toskill) && !in_array(0, $search_per_toskill) && in_array(2, $search_per_toskill)){
                    $cmd = " select distinct PER_ID from PER_SPECIAL_SKILL  order by PER_ID ";
                }else if(in_array(1, $search_per_toskill) && in_array(0, $search_per_toskill) && !in_array(2, $search_per_toskill)){
                    $cmd = " select distinct PER_ID from PER_SPECIAL_SKILL WHERE SS_CODE != '-1' or SS_CODE = '-1' order by PER_ID ";
                } else if(in_array(1, $search_per_toskill) && !in_array(0, $search_per_toskill) && in_array(2, $search_per_toskill)){
                    $cmd = " select distinct PER_ID from PER_SPECIAL_SKILL WHERE SS_CODE = '-1'  order by PER_ID ";
                }else if(!in_array(1, $search_per_toskill) && in_array(0, $search_per_toskill) && in_array(2, $search_per_toskill)){
                    $cmd = " select distinct PER_ID from PER_SPECIAL_SKILL WHERE SS_CODE != '-1'  order by PER_ID ";
                }
                $db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_punishment[] = $data[PER_ID];
                
                if(!in_array(1, $search_per_toskill) && !in_array(0, $search_per_toskill) && in_array(2, $search_per_toskill)){ 
                    $arr_search_condition[] = "(a.PER_ID not in (". implode(",", $arr_punishment) ."))";
                }else if(in_array(1, $search_per_toskill) && !in_array(0, $search_per_toskill) && in_array(2, $search_per_toskill)){
                    $arr_search_condition[] = "(a.PER_ID not in (". implode(",", $arr_punishment) ."))";
                }else if(!in_array(1, $search_per_toskill) && in_array(0, $search_per_toskill) && in_array(2, $search_per_toskill)){
                    $arr_search_condition[] = "(a.PER_ID not in (". implode(",", $arr_punishment) ."))";
                }else if(in_array(1, $search_per_toskill) && in_array(0, $search_per_toskill) && in_array(2, $search_per_toskill)){
                   
                }else{
                    $arr_search_condition[] = "(a.PER_ID  in (". implode(",", $arr_punishment) ."))";
                }
//	}else{
//		$arr_search_condition[] = "(a.PER_ID in ())";
	} // end if
        
	if(count($search_per_invest)){
		$cmd = " select distinct PER_ID from PER_INVEST2DTL where PEN_CODE IS NULL order by PER_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while($data = $db_dpis->get_array()) $arr_investigate[] = $data[PER_ID];

		if(in_array(1, $search_per_invest) && !in_array(0, $search_per_invest)){
			// ปัจจุบันกำลังถูกสอบสวน
			$arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_investigate) ."))";
		}elseif(!in_array(1, $search_per_invest) && in_array(0, $search_per_invest)){
			// ปัจจุบันไม่อยู่ระหว่างการสอบสวน
			$arr_search_condition[] = "(a.PER_ID not in (". implode(",", $arr_investigate) ."))";
		} // end if
//	}else{
//		$arr_search_condition[] = "(a.PER_ID in ())";
	} // end if
	if(trim($search_al_code)){
		$cmd = " select distinct PER_ID from PER_ABILITY where AL_CODE='$search_al_code' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_ability[] = $data[PER_ID];
			else $arr_ability2[] = $data[PER_ID];
		}
		
		if (count($arr_ability2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_ability) .")) or (a.PER_ID in (". implode(",", $arr_ability2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_ability) ."))";
	} // end if

	if(trim($search_ss_code)){
		$cmd = " select distinct PER_ID from PER_SPECIAL_SKILL where SS_CODE='$search_ss_code' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_special_skill[] = $data[PER_ID];
			else $arr_special_skill2[] = $data[PER_ID];
		}
		
		if (count($arr_special_skill2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_special_skill) .")) or (a.PER_ID in (". implode(",", $arr_special_skill2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_special_skill) ."))";
	} // end if

         if(trim($LE_CODE)){
        
		$cmd = " select distinct PER_ID from PER_SPECIAL_SKILL where LEVELSKILL_CODE='$LE_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_special_skill_code[] = $data[PER_ID];
			else $arr_special_skill_code2[] = $data[PER_ID];
		}
		
		if (count($arr_special_skill_code2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_special_skill_code) .")) or (a.PER_ID in (". implode(",", $arr_special_skill_code2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_special_skill_code) ."))";
	} // end if
        
	if(trim($search_sps_emphasize)){
		$cmd = " select distinct PER_ID from PER_SPECIAL_SKILL where SPS_EMPHASIZE like '%$search_sps_emphasize%' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_sps_emphasize[] = $data[PER_ID];
			else $arr_sps_emphasize2[] = $data[PER_ID];
		}
		
		if (count($arr_sps_emphasize2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_sps_emphasize) .")) or (a.PER_ID in (". implode(",", $arr_sps_emphasize2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_sps_emphasize) ."))";
	} // end if

	if ($BKK_FLAG!=1) { 
		foreach($search_hip_flag as $hip_flag) if ($hip_flag == '0') $hip_all = "Y";
		if ($hip_all != 'Y') {
			foreach($search_hip_flag as $hip_flag) $arr_search_hip_flag[] = "(a.PER_HIP_FLAG like '%$hip_flag%')";
			$arr_search_condition[] = "(".implode(" or ", $arr_search_hip_flag).")";
		}
	}

	if($search_per_audit_absent_flag)	$arr_search_condition[] = "(a.PER_AUDIT_FLAG =1)";
	if($search_per_probation_flag)	$arr_search_condition[] = "(a.PER_PROBATION_FLAG =1)";
        if($search_per_renew)	$arr_search_condition[] = "(a.PER_RENEW =1)";
	$arr_search_condition[] = "(a.PER_GENDER in (". implode(",", $search_per_gender) ."))";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(",", $search_per_status) ."))";
	$arr_search_condition[] = "(a.PER_ORDAIN in (". implode(",", $search_per_ordain) ."))";
	$arr_search_condition[] = "(a.PER_SOLDIER in (". implode(",", $search_per_soldier) ."))";
	if ($BKK_FLAG!=1) $arr_search_condition[] = "(a.PER_MEMBER in (". implode(",", $search_per_member) ."))";

	/* ================= 	ตำแหน่งปัจจุบัน    ===================== */
	if(trim($search_pos_change_date_min)){
		$search_pos_change_date_min =  save_date($search_pos_change_date_min);
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(b.POS_CHANGE_DATE, 10) >= '$search_pos_change_date_min')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(b.POS_CHANGE_DATE, 1, 10) >= '$search_pos_change_date_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(b.POS_CHANGE_DATE, 10) >= '$search_pos_change_date_min')";
		$search_pos_change_date_min = show_date_format($search_pos_change_date_min, 1);
	} // end if
	if(trim($search_pos_change_date_max)){
		$search_pos_change_date_max =  save_date($search_pos_change_date_max);
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(b.POS_CHANGE_DATE, 10) <= '$search_pos_change_date_max')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(b.POS_CHANGE_DATE, 1, 10) <= '$search_pos_change_date_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(b.POS_CHANGE_DATE, 10) <= '$search_pos_change_date_max')";
		$search_pos_change_date_max = show_date_format($search_pos_change_date_max, 1);
	} // end if
  	if(trim($search_pos_no_name))  {	
		if ($search_per_type == 1 || $search_per_type == 5)
			$arr_search_condition[] = "(b.POS_NO_NAME like '$search_pos_no_name%')";
		elseif ($search_per_type == 2) 
			$arr_search_condition[] = "(c.POEM_NO_NAME like '$search_pos_no_name%')";		
		elseif ($search_per_type == 3) 	
			$arr_search_condition[] = "(d.POEMS_NO_NAME like '$search_pos_no_name%')";
		elseif ($search_per_type == 4) 	
			$arr_search_condition[] = "(e.POT_NO_NAME like '$search_pos_no_name%')";			
	}
  	if(trim($search_pos_no))  {	
		if ($search_per_type == 1 || $search_per_type == 5)
			$arr_search_condition[] = "(b.POS_NO like '$search_pos_no%')";
		elseif ($search_per_type == 2) 
			$arr_search_condition[] = "(c.POEM_NO like '$search_pos_no%')";		
		elseif ($search_per_type == 3) 	
			$arr_search_condition[] = "(d.POEMS_NO like '$search_pos_no%')";			
		elseif ($search_per_type == 4) 	
			$arr_search_condition[] = "(e.POT_NO like '$search_pos_no%')";			
	}
	if(trim($search_pl_code)){
		if($search_per_type == 1 || $search_per_type == 5)
			$arr_search_condition[] = "(b.PL_CODE = '$search_pl_code')";
		elseif($search_per_type == 2)
			$arr_search_condition[] = "(c.PN_CODE = '$search_pl_code')";
		elseif($search_per_type == 3)
			$arr_search_condition[] = "(d.EP_CODE = '$search_pl_code')";
		elseif($search_per_type == 4)
			$arr_search_condition[] = "(e.TP_CODE = '$search_pl_code')";
	} // end if
	if(trim($search_pm_code)) $arr_search_condition[] = "(b.PM_CODE = '$search_pm_code')";
//	if(trim($search_pt_code)) $arr_search_condition[] = "(b.PT_CODE = '$search_pt_code')";
//	if(trim($search_level_no_min)) $arr_search_condition[] = "(trim(a.LEVEL_NO) >= trim('$search_level_no_min'))";
//	if(trim($search_level_no_max)) $arr_search_condition[] = "(trim(a.LEVEL_NO) <= trim('$search_level_no_max'))";
//  อ่านจาก search_level_no_min[] ที่เป็น multiselection
	$arr_level_no_condi = (array) null;
	foreach ($search_level_no_min as $search_level_no)
	{
        if ($search_level_no) $arr_level_no_condi[] = "'$search_level_no'";
//		echo "search_level_no=$search_level_no<br>";
	}
	if (count($arr_level_no_condi) > 0) $arr_search_condition[] = "(trim(a.LEVEL_NO) in (".implode(",",$arr_level_no_condi)."))";
//	echo "arr_level_no_condi = (".implode(",",$arr_level_no_condi).")<br>";
	if(trim($search_per_salary_min)) $arr_search_condition[] = "(a.PER_SALARY >= $search_per_salary_min)";
	if(trim($search_per_salary_max)) $arr_search_condition[] = "(a.PER_SALARY <= $search_per_salary_max)";
	if(trim($search_mov_code)){
		$cmd = " select 	distinct a.PER_ID 
						 from 		PER_PERSONAL a, PER_POSITIONHIS b
						 where	a.PER_ID=b.PER_ID and (trim(a.MOV_CODE) = trim('$search_mov_code') or trim(b.MOV_CODE) = trim('$search_mov_code')) ";
		$db_dpis->send_cmd($cmd);

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_movement[] = $data[PER_ID];
			elseif ($count < 2000) $arr_movement2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_movement3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_movement4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_movement5[] = $data[PER_ID];
			else $arr_movement6[] = $data[PER_ID];
		}
		
		if (count($arr_movement6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_movement) .")) or (a.PER_ID in (". implode(",", $arr_movement2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_movement3) .")) or (a.PER_ID in (". implode(",", $arr_movement4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_movement5) .")) or (a.PER_ID in (". implode(",", $arr_movement6) .")))";
		elseif (count($arr_movement5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_movement) .")) or (a.PER_ID in (". implode(",", $arr_movement2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_movement3) .")) or (a.PER_ID in (". implode(",", $arr_movement4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_movement5) .")))";
		elseif (count($arr_movement4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_movement) .")) or (a.PER_ID in (". implode(",", $arr_movement2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_movement3) .")) or (a.PER_ID in (". implode(",", $arr_movement4) .")))";
		elseif (count($arr_movement3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_movement) .")) or (a.PER_ID in (". implode(",", $arr_movement2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_movement3) .")))";
		elseif (count($arr_movement2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_movement) .")) or (a.PER_ID in (". implode(",", $arr_movement2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_movement) ."))";
	} // end if

	if(trim($search_cur_poh_docno)){
		$cmd = " select distinct PER_ID from PER_POSITIONHIS where trim(POH_DOCNO)='$search_cur_poh_docno' ";
		$db_dpis->send_cmd($cmd);

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_cur_docno[] = $data[PER_ID];
			elseif ($count < 2000) $arr_cur_docno2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_cur_docno3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_cur_docno4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_cur_docno5[] = $data[PER_ID];
			else $arr_cur_docno6[] = $data[PER_ID];
		}
		
		if (count($arr_cur_docno6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_cur_docno) .")) or (a.PER_ID in (". implode(",", $arr_cur_docno2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_cur_docno3) .")) or (a.PER_ID in (". implode(",", $arr_cur_docno4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_cur_docno5) .")) or (a.PER_ID in (". implode(",", $arr_cur_docno6) .")))";
		elseif (count($arr_cur_docno5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_cur_docno) .")) or (a.PER_ID in (". implode(",", $arr_cur_docno2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_cur_docno3) .")) or (a.PER_ID in (". implode(",", $arr_cur_docno4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_cur_docno5) .")))";
		elseif (count($arr_cur_docno4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_cur_docno) .")) or (a.PER_ID in (". implode(",", $arr_cur_docno2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_cur_docno3) .")) or (a.PER_ID in (". implode(",", $arr_cur_docno4) .")))";
		elseif (count($arr_cur_docno3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_cur_docno) .")) or (a.PER_ID in (". implode(",", $arr_cur_docno2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_cur_docno3) .")))";
		elseif (count($arr_cur_docno2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_cur_docno) .")) or (a.PER_ID in (". implode(",", $arr_cur_docno2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_cur_docno) ."))";
	} 

	if(trim($search_pos_ot_code)) {
		$cmd = " select 	distinct a.PER_ID 
						 from 		PER_PERSONAL a, PER_POSITION b, PER_ORG c
						 where	a.POS_ID=b.POS_ID and b.ORG_ID = c.ORG_ID and c.OT_CODE='$search_pos_ot_code' ";
		$db_dpis->send_cmd($cmd);

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_ot_code[] = $data[PER_ID];
			elseif ($count < 2000) $arr_ot_code2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_ot_code3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_ot_code4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_ot_code5[] = $data[PER_ID];
			else $arr_ot_code6[] = $data[PER_ID];
		}
		
		if (count($arr_ot_code6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_ot_code) .")) or (a.PER_ID in (". implode(",", $arr_ot_code2) .")) or 
																														(a.PER_ID in (". implode(",", $arr_ot_code3) .")) or (a.PER_ID in (". implode(",", $arr_ot_code4) .")) or 
																														(a.PER_ID in (". implode(",", $arr_ot_code5) .")) or (a.PER_ID in (". implode(",", $arr_ot_code6) .")))";
		elseif (count($arr_ot_code5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_ot_code) .")) or (a.PER_ID in (". implode(",", $arr_ot_code2) .")) or 
																														(a.PER_ID in (". implode(",", $arr_ot_code3) .")) or (a.PER_ID in (". implode(",", $arr_ot_code4) .")) or 
																														(a.PER_ID in (". implode(",", $arr_ot_code5) .")))";
		elseif (count($arr_ot_code4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_ot_code) .")) or (a.PER_ID in (". implode(",", $arr_ot_code2) .")) or 
																														(a.PER_ID in (". implode(",", $arr_ot_code3) .")) or (a.PER_ID in (". implode(",", $arr_ot_code4) .")))";
		elseif (count($arr_ot_code3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_ot_code) .")) or (a.PER_ID in (". implode(",", $arr_ot_code2) .")) or 
																														(a.PER_ID in (". implode(",", $arr_ot_code3) .")))";
		elseif (count($arr_ot_code2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_ot_code) .")) or (a.PER_ID in (". implode(",", $arr_ot_code2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_ot_code) ."))";
	}

	if(trim($search_org_id)){ 
		if($SESS_ORG_STRUCTURE==1 || $select_org_structure==1){	
			$arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		}else{	
			if($search_per_type == 1 || $search_per_type == 5)
				$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
			elseif($search_per_type == 2)
				$arr_search_condition[] = "(c.ORG_ID = $search_org_id)";
			elseif($search_per_type == 3)
				$arr_search_condition[] = "(d.ORG_ID = $search_org_id)";
			elseif($search_per_type == 4)
				$arr_search_condition[] = "(e.ORG_ID = $search_org_id)";
		}
	} // end if
	if(trim($search_org_id_1)){ 
		if($SESS_ORG_STRUCTURE==1 || $select_org_structure==1){	
			$arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1)";
		}else{	
			if($search_per_type == 1 || $search_per_type == 5)
				$arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			elseif($search_per_type == 2)
				$arr_search_condition[] = "(c.ORG_ID_1 = $search_org_id_1)";
			elseif($search_per_type == 3)
				$arr_search_condition[] = "(d.ORG_ID_1 = $search_org_id_1)";
			elseif($search_per_type == 4)
				$arr_search_condition[] = "(e.ORG_ID_1 = $search_org_id_1)";
		}
	} // end if
	if(trim($search_org_id_2)){ 
		if($SESS_ORG_STRUCTURE==1 || $select_org_structure==1){	
			$arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2)";
		}else{	
			if($search_per_type == 1 || $search_per_type == 5)
				$arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			elseif($search_per_type == 2)
				$arr_search_condition[] = "(c.ORG_ID_2 = $search_org_id_2)";
			elseif($search_per_type == 3)
				$arr_search_condition[] = "(d.ORG_ID_2 = $search_org_id_2)";
			elseif($search_per_type == 4)
				$arr_search_condition[] = "(e.ORG_ID_2 = $search_org_id_2)";
		}
	} // end if
	if(trim($search_pc_code)) $arr_search_condition[] = "(b.PC_CODE = '$search_pc_code')";
	if(trim($search_sg_code)){
		$cmd = " select SKILL_CODE from PER_SKILL where SG_CODE='$search_sg_code' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_skill[] = $data[SKILL_CODE];
		
		$arr_search_condition[] = "(b.SKILL_CODE in (". implode(",", $arr_skill) ."))";
	} // end if
	if(trim($search_skill_code)) $arr_search_condition[] = "(b.SKILL_CODE = '$search_skill_code')";

	/* ================= 	ประวัติการดำรงตำแหน่ง    ===================== */
	if(trim($search_poh_effectivedate_min)){
		$search_poh_effectivedate_min =  save_date($search_poh_effectivedate_min);
		if($DPISDB=="odbc") $arr_search_positionhis_condition[] = "(LEFT(POH_EFFECTIVEDATE, 10) >= '$search_poh_effectivedate_min')";
		elseif($DPISDB=="oci8") $arr_search_positionhis_condition[] = "(SUBSTR(POH_EFFECTIVEDATE, 1, 10) >= '$search_poh_effectivedate_min')";
		elseif($DPISDB=="mysql") $arr_search_positionhis_condition[] = "(LEFT(POH_EFFECTIVEDATE, 10) >= '$search_poh_effectivedate_min')";
		$search_poh_effectivedate_min = show_date_format($search_poh_effectivedate_min, 1);
	} // end if
	if(trim($search_poh_effectivedate_max)){
		$search_poh_effectivedate_max =  save_date($search_poh_effectivedate_max);
		if($DPISDB=="odbc") $arr_search_positionhis_condition[] = "(LEFT(POH_EFFECTIVEDATE, 10) <= '$search_poh_effectivedate_max')";
		elseif($DPISDB=="oci8") $arr_search_positionhis_condition[] = "(SUBSTR(POH_EFFECTIVEDATE, 1, 10) <= '$search_poh_effectivedate_max')";
		elseif($DPISDB=="mysql") $arr_search_positionhis_condition[] = "(LEFT(POH_EFFECTIVEDATE, 10) <= '$search_poh_effectivedate_max')";
		$search_poh_effectivedate_max = show_date_format($search_poh_effectivedate_max, 1);
	} // end if
	if(trim($search_poh_pos_no_name)) $arr_search_positionhis_condition[] = "(trim(POH_POS_NO_NAME) = '$search_poh_pos_no_name')";
	if(trim($search_poh_pos_no)) $arr_search_positionhis_condition[] = "(trim(POH_POS_NO) = '$search_poh_pos_no')";
	if(trim($search_poh_pl_code)){
		if($search_per_type == 1 || $search_per_type == 5) $arr_search_positionhis_condition[] = "(PL_CODE = '$search_poh_pl_code')";
		elseif($search_per_type == 2) $arr_search_positionhis_condition[] = "(PN_CODE = '$search_poh_pl_code')";
		elseif($search_per_type == 3) $arr_search_positionhis_condition[] = "(EP_CODE = '$search_poh_pl_code')";
		elseif($search_per_type == 4) $arr_search_positionhis_condition[] = "(TP_CODE = '$search_poh_pl_code')";
	} // end if
	if(trim($search_poh_pm_code)) $arr_search_positionhis_condition[] = "(PM_CODE = '$search_poh_pm_code')";
	if(trim($search_poh_pt_code)) $arr_search_positionhis_condition[] = "(PT_CODE = '$search_poh_pt_code')";
	if(trim($search_poh_mov_code)) $arr_search_positionhis_condition[] = "(MOV_CODE = '$search_poh_mov_code')";
	if(trim($search_poh_docno)) $arr_search_positionhis_condition[] = "(trim(POH_DOCNO) = '$search_poh_docno')";
	
        //if(trim($search_poh_ot_code)) $arr_search_positionhis_condition[] = "(OT_CODE = '$search_poh_ot_code')";
         $valsearch_poh_ot_nameV2= implode(",", $search_poh_ot_nameV2);
       //  echo $valsearch_poh_ot_nameV2;
        $valsearch_poh_ot_nameV2= str_replace("]", "'",str_replace("[", "'", $valsearch_poh_ot_nameV2)) ;
        //echo $valsearch_poh_ot_nameV2.'<<';
        if(trim($valsearch_poh_ot_nameV2)) $arr_search_positionhis_condition[] = "(OT_NAME1 in ($valsearch_poh_ot_nameV2) 
                                                                                OR OT_NAME2 in ($valsearch_poh_ot_nameV2)
                                                                                OR OT_NAME3 in ($valsearch_poh_ot_nameV2) )"; /*ปรับให้เลือกได้มากกว่า 1*/
        
	if(trim($search_poh_org_name)) $arr_search_positionhis_condition[] = "(POH_ORG3 = '$search_poh_org_name')";
	if(trim($search_poh_org_name_1)) $arr_search_positionhis_condition[] = "(trim(POH_UNDER_ORG1) = trim('$search_poh_org_name_1'))";
	if(trim($search_poh_org_name_2)) $arr_search_positionhis_condition[] = "(trim(POH_UNDER_ORG2) = trim('$search_poh_org_name_2'))";
	if(trim($search_poh_ass_department_name)) $arr_search_positionhis_condition[] = "(POH_ASS_DEPARTMENT = '$search_poh_ass_department_name')";
	if(trim($search_poh_ass_org_name)) $arr_search_positionhis_condition[] = "(POH_ASS_ORG = '$search_poh_ass_org_name')";
	if(trim($search_poh_ass_org_name_1)) $arr_search_positionhis_condition[] = "(trim(POH_ASS_ORG1) = trim('$search_poh_ass_org_name_1'))";
	if(trim($search_poh_ass_org_name_2)) $arr_search_positionhis_condition[] = "(trim(POH_ASS_ORG2) = trim('$search_poh_ass_org_name_2'))";
	if(trim($search_poh_remark)) $arr_search_positionhis_condition[] = "(trim(POH_REMARK) like '%$search_poh_remark%')";
	
	if(count($arr_search_positionhis_condition)){
		if(trim($search_poh_ministry_name)) $arr_search_positionhis_condition[] = "(POH_ORG1 = '$search_poh_ministry_name')";
		if(trim($search_poh_department_name)) $arr_search_positionhis_condition[] = "(POH_ORG2 = '$search_poh_department_name')";
		$cmd = " select distinct PER_ID from PER_POSITIONHIS where ". implode(" and ", $arr_search_positionhis_condition) ." order by PER_ID";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_positionhis[] = $data[PER_ID];
			elseif ($count < 2000) $arr_positionhis2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_positionhis3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_positionhis4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_positionhis5[] = $data[PER_ID];
			elseif ($count < 6000) $arr_positionhis6[] = $data[PER_ID];
			elseif ($count < 7000) $arr_positionhis7[] = $data[PER_ID];
			elseif ($count < 8000) $arr_positionhis8[] = $data[PER_ID];
			elseif ($count < 9000) $arr_positionhis9[] = $data[PER_ID];
			elseif ($count < 10000) $arr_positionhis10[] = $data[PER_ID];
			elseif ($count < 11000) $arr_positionhis11[] = $data[PER_ID];
			elseif ($count < 12000) $arr_positionhis12[] = $data[PER_ID];
			elseif ($count < 13000) $arr_positionhis13[] = $data[PER_ID];
			elseif ($count < 14000) $arr_positionhis14[] = $data[PER_ID];
			elseif ($count < 15000) $arr_positionhis15[] = $data[PER_ID];
			else $arr_positionhis16[] = $data[PER_ID];
		}
		
		if (count($arr_positionhis16)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
                    (a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
                    (a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
                    (a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
                    (a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
                    (a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")) or 
                    (a.PER_ID in (". implode(",", $arr_positionhis13) .")) or (a.PER_ID in (". implode(",", $arr_positionhis14) .")) or 
                    (a.PER_ID in (". implode(",", $arr_positionhis15) .")) or (a.PER_ID in (". implode(",", $arr_positionhis16) .")))";
		elseif (count($arr_positionhis15)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis13) .")) or (a.PER_ID in (". implode(",", $arr_positionhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis15) .")))";
		elseif (count($arr_positionhis14)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis13) .")) or (a.PER_ID in (". implode(",", $arr_positionhis14) .")))";
		elseif (count($arr_positionhis13)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis13) .")))";
		elseif (count($arr_positionhis12)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")))";
		elseif (count($arr_positionhis11)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")))";
		elseif (count($arr_positionhis10)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")))";
		elseif (count($arr_positionhis9)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")))";
		elseif (count($arr_positionhis8)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")))";
		elseif (count($arr_positionhis7)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")))";
		elseif (count($arr_positionhis6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")))";
		elseif (count($arr_positionhis5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis5) .")))";
		elseif (count($arr_positionhis4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")))";
		elseif (count($arr_positionhis3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis3) .")))";
		elseif (count($arr_positionhis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_positionhis) ."))";
	} // end if
	
	/* ================= 	ประวัติการรับเงินเดือน    ===================== */
	if(trim($search_sah_effectivedate_min)){
		$search_sah_effectivedate_min =  save_date($search_sah_effectivedate_min);
		if($DPISDB=="odbc") $arr_search_salaryhis_condition[] = "(LEFT(SAH_EFFECTIVEDATE, 10) >= '$search_sah_effectivedate_min')";
		elseif($DPISDB=="oci8") $arr_search_salaryhis_condition[] = "(SUBSTR(SAH_EFFECTIVEDATE, 1, 10) >= '$search_sah_effectivedate_min')";
		elseif($DPISDB=="mysql") $arr_search_salaryhis_condition[] = "(LEFT(SAH_EFFECTIVEDATE, 10) >= '$search_sah_effectivedate_min')";
		$search_sah_effectivedate_min = show_date_format($search_sah_effectivedate_min, 1);
	} // end if
	if(trim($search_sah_effectivedate_max)){
		$search_sah_effectivedate_max =  save_date($search_sah_effectivedate_max);
		if($DPISDB=="odbc") $arr_search_salaryhis_condition[] = "(LEFT(SAH_EFFECTIVEDATE, 10) <= '$search_sah_effectivedate_max')";
		elseif($DPISDB=="oci8") $arr_search_salaryhis_condition[] = "(SUBSTR(SAH_EFFECTIVEDATE, 1, 10) <= '$search_sah_effectivedate_max')";
		elseif($DPISDB=="mysql") $arr_search_salaryhis_condition[] = "(LEFT(SAH_EFFECTIVEDATE, 10) <= '$search_sah_effectivedate_max')";
		$search_sah_effectivedate_max = show_date_format($search_sah_effectivedate_max, 1);
	} // end if
	if(trim($search_sah_docno)) $arr_search_salaryhis_condition[] = "(trim(SAH_DOCNO) = '$search_sah_docno')";
	if(trim($search_sah_pos_no)) $arr_search_salaryhis_condition[] = "(trim(SAH_POS_NO) = '$search_sah_pos_no')";
	if(trim($search_sah_pl_code)){
		if($search_per_type == 1 || $search_per_type == 5) $arr_search_salaryhis_condition[] = "(PL_CODE = '$search_sah_pl_code')";
		elseif($search_per_type == 2) $arr_search_salaryhis_condition[] = "(PN_CODE = '$search_sah_pl_code')";
		elseif($search_per_type == 3) $arr_search_salaryhis_condition[] = "(EP_CODE = '$search_sah_pl_code')";
		elseif($search_per_type == 4) $arr_search_salaryhis_condition[] = "(TP_CODE = '$search_sah_pl_code')";
	} // end if
	if(trim($search_sah_pm_code)) $arr_search_salaryhis_condition[] = "(PM_CODE = '$search_sah_pm_code')";
	if(trim($search_sah_org_id)) $arr_search_salaryhis_condition[] = "(ORG_ID_3 = $search_sah_org_id)";
	if(trim($search_sah_org_name_1)) $arr_search_salaryhis_condition[] = "(trim(SAH_UNDER_ORG1) = trim('$search_sah_org_name_1'))";
	if(trim($search_sah_org_name_2)) $arr_search_salaryhis_condition[] = "(trim(SAH_UNDER_ORG2) = trim('$search_sah_org_name_2'))";
//	if(trim($search_sah_level_no_min)) $arr_search_salaryhis_condition[] = "(trim(SAH_LEVEL_NO) >= trim('$search_sah_level_no_min'))";
//	if(trim($search_sah_level_no_max)) $arr_search_salaryhis_condition[] = "(trim(SAH_LEVEL_NO) <= trim('$search_sah_level_no_max'))";
	//  อ่านจาก search_level_no_min[] ที่เป็น multiselection
	if(trim($search_sah_level_no_min)) {
		$arr_level_no_condi = (array) null;
		foreach ($search_sah_level_no_min as $search_level_no)
		{
			if ($search_level_no) $arr_level_no_condi[] = "'$search_level_no'";
	//		echo "search_level_no=$search_level_no<br>";
		}
		if (count($arr_level_no_condi) > 0) $arr_search_salaryhis_condition[] = "(trim(SAH_LEVEL_NO) in (".implode(",",$arr_level_no_condi)."))";
//		echo "sah  arr_level_no_condi = (".implode(",",$arr_level_no_condi).")<br>";
	}
	if(trim($search_sah_salary_min)) $arr_search_salaryhis_condition[] = "(SAH_SALARY >= $search_sah_salary_min)";
	if(trim($search_sah_salary_max)) $arr_search_salaryhis_condition[] = "(SAH_SALARY <= $search_sah_salary_max)";
	if(trim($search_sah_mov_code)) $arr_search_salaryhis_condition[] = "(MOV_CODE = '$search_sah_mov_code')";
	
	if(count($arr_search_salaryhis_condition)){
		$cmd = " select distinct PER_ID from PER_SALARYHIS where ". implode(" and ", $arr_search_salaryhis_condition) ." order by PER_ID";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_salaryhis[] = $data[PER_ID];
			elseif ($count < 2000) $arr_salaryhis2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_salaryhis3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_salaryhis4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_salaryhis5[] = $data[PER_ID];
			elseif ($count < 6000) $arr_salaryhis6[] = $data[PER_ID];
			elseif ($count < 7000) $arr_salaryhis7[] = $data[PER_ID];
			elseif ($count < 8000) $arr_salaryhis8[] = $data[PER_ID];
			elseif ($count < 9000) $arr_salaryhis9[] = $data[PER_ID];
			elseif ($count < 10000) $arr_salaryhis10[] = $data[PER_ID];
			elseif ($count < 11000) $arr_salaryhis11[] = $data[PER_ID];
			elseif ($count < 12000) $arr_salaryhis12[] = $data[PER_ID];
			elseif ($count < 13000) $arr_salaryhis13[] = $data[PER_ID];
			elseif ($count < 14000) $arr_salaryhis14[] = $data[PER_ID];
			elseif ($count < 15000) $arr_salaryhis15[] = $data[PER_ID];
			elseif ($count < 16000) $arr_salaryhis16[] = $data[PER_ID];
			elseif ($count < 17000) $arr_salaryhis17[] = $data[PER_ID];
			elseif ($count < 18000) $arr_salaryhis18[] = $data[PER_ID];
			elseif ($count < 19000) $arr_salaryhis19[] = $data[PER_ID];
			else $arr_salaryhis20[] = $data[PER_ID];
		}
		
		if (count($arr_salaryhis20)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis17) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis18) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis19) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis20) .")))";
		elseif (count($arr_salaryhis19)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis17) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis18) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis19) .")))";
		elseif (count($arr_salaryhis18)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis17) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis18) .")))";
		elseif (count($arr_salaryhis17)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis17) .")))";
		elseif (count($arr_salaryhis16)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")))";
		elseif (count($arr_salaryhis15)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")))";
		elseif (count($arr_salaryhis14)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")))";
		elseif (count($arr_salaryhis13)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")))";
		elseif (count($arr_salaryhis12)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")))";
		elseif (count($arr_salaryhis11)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")))";
		elseif (count($arr_salaryhis10)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")))";
		elseif (count($arr_salaryhis9)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")))";
		elseif (count($arr_salaryhis8)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")))";
		elseif (count($arr_salaryhis7)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")))";
		elseif (count($arr_salaryhis6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")))";
		elseif (count($arr_salaryhis5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis5) .")))";
		elseif (count($arr_salaryhis4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")))";
		elseif (count($arr_salaryhis3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis3) .")))";
		elseif (count($arr_salaryhis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_salaryhis) ."))";
	} // end if
	
	/* ================= 	ประวัติการศึกษา    ===================== */
	$search_edu = "";
	if(trim($search_el_code)) $arr_search_educatehis_condition[] = "(trim(a.EL_CODE) = trim('$search_el_code'))";
	if(trim($search_en_code)) $arr_search_educatehis_condition[] = "(a.EN_CODE = '$search_en_code')";
	if(trim($search_em_code)) $arr_search_educatehis_condition[] = "(a.EM_CODE = '$search_em_code')";
	if(trim($search_ins_code)) $arr_search_educatehis_condition[] = "(trim(a.INS_CODE) = trim('$search_ins_code') or trim(a.EDU_INSTITUTE) = trim('$search_ins_name'))";
	if(trim($search_edu_institute)) $arr_search_educatehis_condition[] = "(trim(b.INS_NAME) = trim('$search_edu_institute') or trim(a.EDU_INSTITUTE) = trim('$search_edu_institute'))";
	if(trim($search_ins_ct_code)) $arr_search_educatehis_condition[] = "(trim(a.CT_CODE_EDU) = trim('$search_ins_ct_code'))";
	if(trim($search_st_code)) $arr_search_educatehis_condition[] = "(a.ST_CODE = '$search_st_code')";
	if(trim($search_edu_ct_code)) $arr_search_educatehis_condition[] = "(a.CT_CODE = '$search_edu_ct_code')";
	if(trim($search_edu_endyear_min)) $arr_search_educatehis_condition[] = "(a.EDU_ENDYEAR >= '$search_edu_endyear_min')";
	if(trim($search_edu_endyear_max)) $arr_search_educatehis_condition[] = "(a.EDU_ENDYEAR <= '$search_edu_endyear_max')";

	if(count($arr_search_educatehis_condition)){
		for ($i=0;$i<count($EDU_TYPE);$i++) {
			if($search_edu) { $search_edu.= ' or '; }
			$search_edu.= "a.EDU_TYPE like '%$EDU_TYPE[$i]%' "; 
		} 
		if ($search_edu) $arr_search_educatehis_condition[] = "(".$search_edu.")";
		$cmd = " select 	distinct a.PER_ID 
						 from 		PER_EDUCATE a, PER_INSTITUTE b
						 where 	a.INS_CODE=b.INS_CODE(+)
						 				and ". implode(" and ", $arr_search_educatehis_condition) ." 
						 order by a.PER_ID ";
               
		$db_dpis->send_cmd($cmd);
                 if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_educatehis[] = $data[PER_ID];
			elseif ($count < 2000) $arr_educatehis2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_educatehis3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_educatehis4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_educatehis5[] = $data[PER_ID];
			elseif ($count < 6000) $arr_educatehis6[] = $data[PER_ID];
			elseif ($count < 7000) $arr_educatehis7[] = $data[PER_ID];
			elseif ($count < 8000) $arr_educatehis8[] = $data[PER_ID];
			elseif ($count < 9000) $arr_educatehis9[] = $data[PER_ID];
			elseif ($count < 10000) $arr_educatehis10[] = $data[PER_ID];
			elseif ($count < 11000) $arr_educatehis11[] = $data[PER_ID];
			elseif ($count < 12000) $arr_educatehis12[] = $data[PER_ID];
			elseif ($count < 13000) $arr_educatehis13[] = $data[PER_ID];
			elseif ($count < 14000) $arr_educatehis14[] = $data[PER_ID];
			elseif ($count < 15000) $arr_educatehis15[] = $data[PER_ID];
			else $arr_educatehis16[] = $data[PER_ID];
		}
		
		if (count($arr_educatehis16)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis11) .")) or (a.PER_ID in (". implode(",", $arr_educatehis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis13) .")) or (a.PER_ID in (". implode(",", $arr_educatehis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis15) .")) or (a.PER_ID in (". implode(",", $arr_educatehis16) .")))";
		elseif (count($arr_educatehis15)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis11) .")) or (a.PER_ID in (". implode(",", $arr_educatehis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis13) .")) or (a.PER_ID in (". implode(",", $arr_educatehis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis15) .")))";
		elseif (count($arr_educatehis14)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis11) .")) or (a.PER_ID in (". implode(",", $arr_educatehis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis13) .")) or (a.PER_ID in (". implode(",", $arr_educatehis14) .")))";
		elseif (count($arr_educatehis13)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis11) .")) or (a.PER_ID in (". implode(",", $arr_educatehis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis13) .")))";
		elseif (count($arr_educatehis12)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis11) .")) or (a.PER_ID in (". implode(",", $arr_educatehis12) .")))";
		elseif (count($arr_educatehis11)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis11) .")))";
		elseif (count($arr_educatehis10)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")))";
		elseif (count($arr_educatehis9)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")))";
		elseif (count($arr_educatehis8)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")))";
		elseif (count($arr_educatehis7)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")))";
		elseif (count($arr_educatehis6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")))";
		elseif (count($arr_educatehis5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_educatehis5) .")))";
		elseif (count($arr_educatehis4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")))";
		elseif (count($arr_educatehis3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_educatehis3) .")))";
		elseif (count($arr_educatehis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_educatehis) ."))";
	} // end if

	/* ================= 	ประวัติการอบรม/ดูงาน    ===================== */
	$search_trn = "";
	if(trim($search_trn_startyear)){ 
		$trn_startyear = $search_trn_startyear - 543;
		if($DPISDB=="odbc") $arr_search_trainhis_condition[] = "(LEFT(TRN_STARTDATE, 4) >= '$trn_startyear')";
		elseif($DPISDB=="oci8") $arr_search_trainhis_condition[] = "(SUBSTR(TRN_STARTDATE, 1, 4) >= '$trn_startyear')";
		elseif($DPISDB=="mysql") $arr_search_trainhis_condition[] = "(LEFT(TRN_STARTDATE, 4) >= '$trn_startyear')";
	} // endif
	if(trim($search_trn_endyear)){
		$trn_endyear = $search_trn_endyear - 543;
		if($DPISDB=="odbc") $arr_search_trainhis_condition[] = "(LEFT(TRN_ENDDATE, 4) <= '$trn_endyear')";
		elseif($DPISDB=="oci8") $arr_search_trainhis_condition[] = "(SUBSTR(TRN_ENDDATE, 1, 4) <= '$trn_endyear')";
		elseif($DPISDB=="mysql") $arr_search_trainhis_condition[] = "(LEFT(TRN_ENDDATE, 4) <= '$trn_endyear')";
	} // end if
	if(trim($search_tr_code)) $arr_search_trainhis_condition[] = "(TR_CODE = '$search_tr_code')";
	if(trim($search_trn_course_name)) $arr_search_trainhis_condition[] = "(TRN_COURSE_NAME like '$search_trn_course_name%')";
	if(trim($search_trn_no)) $arr_search_trainhis_condition[] = "(TRN_NO = '$search_trn_no')";
	if(trim($search_tr_ct_code)) $arr_search_trainhis_condition[] = "(CT_CODE = '$search_tr_ct_code')";
	if(trim($search_trn_fund)) $arr_search_trainhis_condition[] = "(TRN_FUND like '$search_trn_fund%')";
	if(trim($search_fund_ct_code)) $arr_search_trainhis_condition[] = "(CT_CODE_FUND = '$search_fund_ct_code')";

	if(count($arr_search_trainhis_condition)){
		for ($i=0;$i<count($TRN_TYPE);$i++) {
			if($search_trn) { $search_trn.= ' or '; }
			$search_trn.= "TRN_TYPE like '%$TRN_TYPE[$i]%' "; 
		} 
		if ($search_trn) $arr_search_trainhis_condition[] = "(".$search_trn.")";
		$cmd = " select distinct PER_ID from PER_TRAINING where ". implode(" and ", $arr_search_trainhis_condition) ." order by PER_ID";
                
		$db_dpis->send_cmd($cmd);
                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_trainhis[] = $data[PER_ID];
			elseif ($count < 2000) $arr_trainhis2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_trainhis3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_trainhis4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_trainhis5[] = $data[PER_ID];
			elseif ($count < 6000) $arr_trainhis6[] = $data[PER_ID];
			elseif ($count < 7000) $arr_trainhis7[] = $data[PER_ID];
			elseif ($count < 8000) $arr_trainhis8[] = $data[PER_ID];
			elseif ($count < 9000) $arr_trainhis9[] = $data[PER_ID];
			elseif ($count < 10000) $arr_trainhis10[] = $data[PER_ID];
			elseif ($count < 11000) $arr_trainhis11[] = $data[PER_ID];
			elseif ($count < 12000) $arr_trainhis12[] = $data[PER_ID];
			elseif ($count < 13000) $arr_trainhis13[] = $data[PER_ID];
			elseif ($count < 14000) $arr_trainhis14[] = $data[PER_ID];
			elseif ($count < 15000) $arr_trainhis15[] = $data[PER_ID];
			else $arr_trainhis16[] = $data[PER_ID];
		}
		
		if (count($arr_trainhis16)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis11) .")) or (a.PER_ID in (". implode(",", $arr_trainhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis13) .")) or (a.PER_ID in (". implode(",", $arr_trainhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis15) .")) or (a.PER_ID in (". implode(",", $arr_trainhis16) .")))";
		elseif (count($arr_trainhis15)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis11) .")) or (a.PER_ID in (". implode(",", $arr_trainhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis13) .")) or (a.PER_ID in (". implode(",", $arr_trainhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis15) .")))";
		elseif (count($arr_trainhis14)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis11) .")) or (a.PER_ID in (". implode(",", $arr_trainhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis13) .")) or (a.PER_ID in (". implode(",", $arr_trainhis14) .")))";
		elseif (count($arr_trainhis13)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis11) .")) or (a.PER_ID in (". implode(",", $arr_trainhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis13) .")))";
		elseif (count($arr_trainhis12)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis11) .")) or (a.PER_ID in (". implode(",", $arr_trainhis12) .")))";
		elseif (count($arr_trainhis11)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis11) .")))";
		elseif (count($arr_trainhis10)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")))";
		elseif (count($arr_trainhis9)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")))";
		elseif (count($arr_trainhis8)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")))";
		elseif (count($arr_trainhis7)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")))";
		elseif (count($arr_trainhis6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")))";
		elseif (count($arr_trainhis5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_trainhis5) .")))";
		elseif (count($arr_trainhis4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")))";
		elseif (count($arr_trainhis3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_trainhis3) .")))";
		elseif (count($arr_trainhis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_trainhis) ."))";
	} // end if

	/* ================= 	ความดีความชอบ   ===================== */
	if(trim($search_rew_code)) $arr_search_rewardhis_condition[] = "(REW_CODE = '$search_rew_code')";
	if(trim($search_reh_year_min)){ 
                $search_reh_year_minx = $search_reh_year_min-543;
		if($DPISDB=="odbc") $arr_search_rewardhis_condition[] = "(LEFT(REH_DATE, 4) >= '$search_reh_year_minx')";
		elseif($DPISDB=="oci8") $arr_search_rewardhis_condition[] = "(SUBSTR(REH_DATE, 1, 4) >= '$search_reh_year_minx')";
		elseif($DPISDB=="mysql") $arr_search_rewardhis_condition[] = "(LEFT(REH_DATE, 4) >= '$search_reh_year_minx')";
	} // endif
	if(trim($search_reh_year_max)){
                $search_reh_year_maxx = $search_reh_year_max-543;
		if($DPISDB=="odbc") $arr_search_rewardhis_condition[] = "(LEFT(REH_DATE, 4) <= '$search_reh_year_maxx')";
		elseif($DPISDB=="oci8") $arr_search_rewardhis_condition[] = "(SUBSTR(REH_DATE, 1, 4) <= '$search_reh_year_maxx')";
		elseif($DPISDB=="mysql") $arr_search_rewardhis_condition[] = "(LEFT(REH_DATE, 4) <= '$search_reh_year_maxx')";
	} // end if

	if(count($arr_search_rewardhis_condition)){
		$cmd = " select distinct PER_ID from PER_REWARDHIS where ". implode(" and ", $arr_search_rewardhis_condition) ." order by PER_ID";
                
		$db_dpis->send_cmd($cmd);
                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_rewardhis[] = $data[PER_ID];
			else $arr_rewardhis2[] = $data[PER_ID];
		}
		
		if (count($arr_rewardhis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_rewardhis) .")) or (a.PER_ID in (". implode(",", $arr_rewardhis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_rewardhis) ."))";
	} // end if

	/* ================= 	ราชการพิเศษ    ===================== */
	if(trim($search_srh_startdate)){
		$search_srh_startdate =  save_date($search_srh_startdate);
		if($DPISDB=="odbc") $arr_search_servicehis_condition[] = "(LEFT(SRH_STARTDATE, 10) >= '$search_srh_startdate')";
		elseif($DPISDB=="oci8") $arr_search_servicehis_condition[] = "(SUBSTR(SRH_STARTDATE, 1, 10) >= '$search_srh_startdate')";
		elseif($DPISDB=="mysql") $arr_search_servicehis_condition[] = "(LEFT(SRH_STARTDATE, 10) >= '$search_srh_startdate')";
		$search_srh_startdate = show_date_format($search_srh_startdate, 1);
	} // end if
	if(trim($search_srh_enddate)){
		$search_srh_enddate =  save_date($search_srh_enddate);
		if($DPISDB=="odbc") $arr_search_servicehis_condition[] = "(LEFT(SRH_ENDDATE, 10) <= '$search_srh_enddate')";
		elseif($DPISDB=="oci8") $arr_search_servicehis_condition[] = "(SUBSTR(SRH_ENDDATE, 1, 10) <= '$search_srh_enddate')";
		elseif($DPISDB=="mysql") $arr_search_servicehis_condition[] = "(LEFT(SRH_ENDDATE, 10) <= '$search_srh_enddate')";
		$search_srh_enddate = show_date_format($search_srh_enddate, 1);
	} // end if
	if(trim($search_sv_code)) $arr_search_servicehis_condition[] = "(SV_CODE = '$search_sv_code')";
	if(trim($search_srt_code)) $arr_search_servicehis_condition[] = "(SRT_CODE = '$search_srt_code')";
	if(trim($search_srh_org_id)) $arr_search_servicehis_condition[] = "(ORG_ID = $search_srh_org_id)";

	if(count($arr_search_servicehis_condition)){
		$cmd = " select distinct PER_ID from PER_SERVICEHIS where ". implode(" and ", $arr_search_servicehis_condition) ." order by PER_ID";
                
		$db_dpis->send_cmd($cmd);
                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_servicehis[] = $data[PER_ID];
			else $arr_servicehis2[] = $data[PER_ID];
		}
		
		if (count($arr_servicehis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_servicehis) .")) or (a.PER_ID in (". implode(",", $arr_servicehis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_servicehis) ."))";
	} // end if

	/* ================= 	การลา    ===================== */
	if(trim($search_abs_startdate)){
		$search_abs_startdate =  save_date($search_abs_startdate);
		if($DPISDB=="odbc") $arr_search_absenthis_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '$search_abs_startdate')";
		elseif($DPISDB=="oci8") $arr_search_absenthis_condition[] = "(SUBSTR(ABS_STARTDATE, 1, 10) >= '$search_abs_startdate')";
		elseif($DPISDB=="mysql") $arr_search_absenthis_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '$search_abs_startdate')";
		$search_abs_startdate = show_date_format($search_abs_startdate, 1);
	} // end if
	if(trim($search_abs_enddate)){
		$search_abs_enddate =  save_date($search_abs_enddate);
		if($DPISDB=="odbc") $arr_search_absenthis_condition[] = "(LEFT(ABS_ENDDATE, 10) <= '$search_abs_enddate')";
		elseif($DPISDB=="oci8") $arr_search_absenthis_condition[] = "(SUBSTR(ABS_ENDDATE, 1, 10) <= '$search_abs_enddate')";
		elseif($DPISDB=="mysql") $arr_search_absenthis_condition[] = "(LEFT(ABS_ENDDATE, 10) <= '$search_abs_enddate')";
		$search_abs_enddate = show_date_format($search_abs_enddate, 1);
	} // end if
	if(trim($search_ab_code)) $arr_search_absenthis_condition[] = "(AB_CODE = '$search_ab_code')";

	if(count($arr_search_absenthis_condition)){
		$cmd = " select distinct PER_ID from PER_ABSENTHIS where ". implode(" and ", $arr_search_absenthis_condition) ." order by PER_ID";
                
		$db_dpis->send_cmd($cmd);
                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_absenthis[] = $data[PER_ID];
			elseif ($count < 2000) $arr_absenthis2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_absenthis3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_absenthis4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_absenthis5[] = $data[PER_ID];
			elseif ($count < 6000) $arr_absenthis6[] = $data[PER_ID];
			elseif ($count < 7000) $arr_absenthis7[] = $data[PER_ID];
			elseif ($count < 8000) $arr_absenthis8[] = $data[PER_ID];
			elseif ($count < 9000) $arr_absenthis9[] = $data[PER_ID];
			elseif ($count < 10000) $arr_absenthis10[] = $data[PER_ID];
			elseif ($count < 11000) $arr_absenthis11[] = $data[PER_ID];
			elseif ($count < 12000) $arr_absenthis12[] = $data[PER_ID];
			elseif ($count < 13000) $arr_absenthis13[] = $data[PER_ID];
			elseif ($count < 14000) $arr_absenthis14[] = $data[PER_ID];
			elseif ($count < 15000) $arr_absenthis15[] = $data[PER_ID];
			else $arr_absenthis16[] = $data[PER_ID];
		}
		
		if (count($arr_absenthis16)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis5) .")) or (a.PER_ID in (". implode(",", $arr_absenthis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis7) .")) or (a.PER_ID in (". implode(",", $arr_absenthis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis9) .")) or (a.PER_ID in (". implode(",", $arr_absenthis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis11) .")) or (a.PER_ID in (". implode(",", $arr_absenthis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis13) .")) or (a.PER_ID in (". implode(",", $arr_absenthis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis15) .")) or (a.PER_ID in (". implode(",", $arr_absenthis16) .")))";
		elseif (count($arr_absenthis15)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis5) .")) or (a.PER_ID in (". implode(",", $arr_absenthis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis7) .")) or (a.PER_ID in (". implode(",", $arr_absenthis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis9) .")) or (a.PER_ID in (". implode(",", $arr_absenthis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis11) .")) or (a.PER_ID in (". implode(",", $arr_absenthis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis13) .")) or (a.PER_ID in (". implode(",", $arr_absenthis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis15) .")))";
		elseif (count($arr_absenthis14)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis5) .")) or (a.PER_ID in (". implode(",", $arr_absenthis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis7) .")) or (a.PER_ID in (". implode(",", $arr_absenthis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis9) .")) or (a.PER_ID in (". implode(",", $arr_absenthis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis11) .")) or (a.PER_ID in (". implode(",", $arr_absenthis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis13) .")) or (a.PER_ID in (". implode(",", $arr_absenthis14) .")))";
		elseif (count($arr_absenthis13)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis5) .")) or (a.PER_ID in (". implode(",", $arr_absenthis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis7) .")) or (a.PER_ID in (". implode(",", $arr_absenthis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis9) .")) or (a.PER_ID in (". implode(",", $arr_absenthis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis11) .")) or (a.PER_ID in (". implode(",", $arr_absenthis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis13) .")))";
		elseif (count($arr_absenthis12)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis5) .")) or (a.PER_ID in (". implode(",", $arr_absenthis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis7) .")) or (a.PER_ID in (". implode(",", $arr_absenthis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis9) .")) or (a.PER_ID in (". implode(",", $arr_absenthis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis11) .")) or (a.PER_ID in (". implode(",", $arr_absenthis12) .")))";
		elseif (count($arr_absenthis11)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis5) .")) or (a.PER_ID in (". implode(",", $arr_absenthis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis7) .")) or (a.PER_ID in (". implode(",", $arr_absenthis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis9) .")) or (a.PER_ID in (". implode(",", $arr_absenthis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis11) .")))";
		elseif (count($arr_absenthis10)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis5) .")) or (a.PER_ID in (". implode(",", $arr_absenthis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis7) .")) or (a.PER_ID in (". implode(",", $arr_absenthis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis9) .")) or (a.PER_ID in (". implode(",", $arr_absenthis10) .")))";
		elseif (count($arr_absenthis9)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis5) .")) or (a.PER_ID in (". implode(",", $arr_absenthis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis7) .")) or (a.PER_ID in (". implode(",", $arr_absenthis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis9) .")))";
		elseif (count($arr_absenthis8)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis5) .")) or (a.PER_ID in (". implode(",", $arr_absenthis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis7) .")) or (a.PER_ID in (". implode(",", $arr_absenthis8) .")))";
		elseif (count($arr_absenthis7)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis5) .")) or (a.PER_ID in (". implode(",", $arr_absenthis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_absenthis7) .")))";
		elseif (count($arr_absenthis6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_absenthis5) .")) or (a.PER_ID in (". implode(",", $arr_absenthis6) .")))";
		elseif (count($arr_absenthis5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_absenthis5) .")))";
		elseif (count($arr_absenthis4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_absenthis3) .")) or (a.PER_ID in (". implode(",", $arr_absenthis4) .")))";
		elseif (count($arr_absenthis3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_absenthis3) .")))";
		elseif (count($arr_absenthis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_absenthis) .")) or (a.PER_ID in (". implode(",", $arr_absenthis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_absenthis) ."))";
	} // end if

	/* ================= 	การลาศึกษาต่อ    ===================== */
	if(trim($search_abs_startdate)){
		$search_abs_startdate =  save_date($search_abs_startdate);
		if($DPISDB=="odbc") $arr_search_scholar_condition[] = "(LEFT(SC_STARTDATE, 10) >= '$search_abs_startdate')";
		elseif($DPISDB=="oci8") $arr_search_scholar_condition[] = "(SUBSTR(SC_STARTDATE, 1, 10) >= '$search_abs_startdate')";
		elseif($DPISDB=="mysql") $arr_search_scholar_condition[] = "(LEFT(SC_STARTDATE, 10) >= '$search_abs_startdate')";
		$search_abs_startdate = show_date_format($search_abs_startdate, 1);
	} // end if
	if(trim($search_abs_enddate)){
		$search_abs_enddate =  save_date($search_abs_enddate);
		if($DPISDB=="odbc") $arr_search_scholar_condition[] = "(LEFT(SC_ENDDATE, 10) <= '$search_abs_enddate')";
		elseif($DPISDB=="oci8") $arr_search_scholar_condition[] = "(SUBSTR(SC_ENDDATE, 1, 10) <= '$search_abs_enddate')";
		elseif($DPISDB=="mysql") $arr_search_scholar_condition[] = "(LEFT(SC_ENDDATE, 10) <= '$search_abs_enddate')";
		$search_abs_enddate = show_date_format($search_abs_enddate, 1);
	} // end if
	if(trim($search_sc_el_code)) $arr_search_scholar_condition[] = "(trim(a.EL_CODE) = trim('$search_sc_el_code'))";
	if(trim($search_sc_en_code)) $arr_search_scholar_condition[] = "(a.EN_CODE = '$search_sc_en_code')";
	if(trim($search_sc_em_code)) $arr_search_scholar_condition[] = "(a.EM_CODE = '$search_sc_em_code')";
	if(trim($search_sc_ins_code)) $arr_search_scholar_condition[] = "(trim(a.INS_CODE) = trim('$search_sc_ins_code') or trim(a.SC_INSTITUTE) = trim('$search_sc_ins_name'))";
	if(trim($search_sc_institute)) $arr_search_scholar_condition[] = "(trim(b.INS_NAME) = trim('$search_sc_institute') or trim(a.SC_INSTITUTE) = trim('$search_sc_institute'))";
	if(trim($search_sc_ins_ct_code)) $arr_search_scholar_condition[] = "(trim(b.CT_CODE) = trim('$search_sc_ins_ct_code'))";
	if(trim($search_sc_st_code)) $arr_search_scholar_condition[] = "(a.ST_CODE = '$search_sc_st_code')";
	if(trim($search_sc_ct_code)) $arr_search_scholar_condition[] = "(a.CT_CODE = '$search_sc_ct_code')";
	if(trim($search_sc_endyear_min)) $arr_search_scholar_condition[] = "(substr(a.SC_FINISHDATE,1,4) >= '$search_sc_endyear_min')";
	if(trim($search_sc_endyear_max)) $arr_search_scholar_condition[] = "(substr(a.SC_FINISHDATE,1,4) <= '$search_sc_endyear_max')";

	if(count($arr_search_scholar_condition)){
		$cmd = " select distinct PER_ID from PER_SCHOLAR where ". implode(" and ", $arr_search_scholar_condition) ." order by PER_ID";
                
		$db_dpis->send_cmd($cmd);
                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_scholar[] = $data[PER_ID];
			else $arr_scholar2[] = $data[PER_ID];
		}
		
		if (count($arr_scholar2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_scholar) .")) or (a.PER_ID in (". implode(",", $arr_scholar2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_scholar) ."))";
	} // end if

	/* ================= 	เครื่องราชฯ    ===================== */
	if(trim($search_deh_startdate)){
		$search_deh_startdate =  save_date($search_deh_startdate);
		if($DPISDB=="odbc") $arr_search_decoratehis_condition[] = "(LEFT(DEH_DATE, 10) >= '$search_deh_startdate')";
		elseif($DPISDB=="oci8") $arr_search_decoratehis_condition[] = "(SUBSTR(DEH_DATE, 1, 10) >= '$search_deh_startdate')";
		elseif($DPISDB=="mysql") $arr_search_decoratehis_condition[] = "(LEFT(DEH_DATE, 10) >= '$search_deh_startdate')";
		$search_deh_startdate = show_date_format($search_deh_startdate, 1);
	} // end if
	if(trim($search_deh_enddate)){
		$search_deh_enddate =  save_date($search_deh_enddate);
		if($DPISDB=="odbc") $arr_search_decoratehis_condition[] = "(LEFT(DEH_DATE, 10) <= '$search_deh_enddate')";
		elseif($DPISDB=="oci8") $arr_search_decoratehis_condition[] = "(SUBSTR(DEH_DATE, 1, 10) <= '$search_deh_enddate')";
		elseif($DPISDB=="mysql") $arr_search_decoratehis_condition[] = "(LEFT(DEH_DATE, 10) <= '$search_deh_enddate')";
		$search_deh_enddate = show_date_format($search_deh_enddate, 1);
	} // end if
	if(trim($search_dc_code)) $arr_search_decoratehis_condition[] = "(DC_CODE = '$search_dc_code')";

	if(count($arr_search_decoratehis_condition)){
		$cmd = " select distinct PER_ID from PER_DECORATEHIS where ". implode(" and ", $arr_search_decoratehis_condition) ." order by PER_ID";
                
		$db_dpis->send_cmd($cmd);
                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_decoratehis[] = $data[PER_ID];
			elseif ($count < 2000) $arr_decoratehis2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_decoratehis3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_decoratehis4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_decoratehis5[] = $data[PER_ID];
			elseif ($count < 6000) $arr_decoratehis6[] = $data[PER_ID];
			elseif ($count < 7000) $arr_decoratehis7[] = $data[PER_ID];
			elseif ($count < 8000) $arr_decoratehis8[] = $data[PER_ID];
			elseif ($count < 9000) $arr_decoratehis9[] = $data[PER_ID];
			elseif ($count < 10000) $arr_decoratehis10[] = $data[PER_ID];
			elseif ($count < 11000) $arr_decoratehis11[] = $data[PER_ID];
			elseif ($count < 12000) $arr_decoratehis12[] = $data[PER_ID];
			elseif ($count < 13000) $arr_decoratehis13[] = $data[PER_ID];
			elseif ($count < 14000) $arr_decoratehis14[] = $data[PER_ID];
			elseif ($count < 15000) $arr_decoratehis15[] = $data[PER_ID];
			else $arr_decoratehis16[] = $data[PER_ID];
		}
		
		if (count($arr_decoratehis16)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis5) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis7) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis9) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis11) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis13) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis15) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis16) .")))";
		elseif (count($arr_decoratehis15)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis5) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis7) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis9) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis11) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis13) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis15) .")))";
		elseif (count($arr_decoratehis14)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis5) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis7) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis9) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis11) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis13) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis14) .")))";
		elseif (count($arr_decoratehis13)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis5) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis7) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis9) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis11) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis13) .")))";
		elseif (count($arr_decoratehis12)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis5) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis7) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis9) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis11) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis12) .")))";
		elseif (count($arr_decoratehis11)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis5) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis7) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis9) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis11) .")))";
		elseif (count($arr_decoratehis10)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis5) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis7) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis9) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis10) .")))";
		elseif (count($arr_decoratehis9)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis5) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis7) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis9) .")))";
		elseif (count($arr_decoratehis8)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis5) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis7) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis8) .")))";
		elseif (count($arr_decoratehis7)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis5) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_decoratehis7) .")))";
		elseif (count($arr_decoratehis6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_decoratehis5) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis6) .")))";
		elseif (count($arr_decoratehis5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_decoratehis5) .")))";
		elseif (count($arr_decoratehis4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_decoratehis3) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis4) .")))";
		elseif (count($arr_decoratehis3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_decoratehis3) .")))";
		elseif (count($arr_decoratehis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_decoratehis) .")) or (a.PER_ID in (". implode(",", $arr_decoratehis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_decoratehis) ."))";
	} // end if

	/* ======================================================== */

	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);
	if($DPISDB=="oci8") $search_condition = str_replace(" where ", " and ", $search_condition);

	$org_cond = "";
	if ($POSITION_NO_CHAR=="Y") $org_cond = ", f.ORG_SEQ_NO $SortType[$order_by], f.ORG_CODE $SortType[$order_by]";
	if($order_by==1){	// เลขที่ตำแหน่ง
		if($search_per_type==1){
			$order_str = "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID $SortType[$order_by]".$org_cond.", b.POS_NO_NAME $SortType[$order_by] ,to_number(replace(b.POS_NO,'-','')) $SortType[$order_by] ";
		}elseif($search_per_type==2){
			$order_str = "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID $SortType[$order_by]".$org_cond.", c.POEM_NO_NAME $SortType[$order_by] ,to_number(replace(c.POEM_NO,'-','')) $SortType[$order_by] ";
		}elseif($search_per_type==3){
			$order_str = "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID $SortType[$order_by]".$org_cond.", d.POEMS_NO_NAME $SortType[$order_by] ,to_number(replace(d.POEMS_NO,'-','')) $SortType[$order_by] ";
		} elseif($search_per_type==4){
			$order_str = "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID $SortType[$order_by]".$org_cond.", e.POT_NO_NAME $SortType[$order_by] ,to_number(replace(e.POT_NO,'-','')) $SortType[$order_by] ";
		}
  	}elseif($order_by==2) {	//ชื่อ-สกุล
		if($DPISDB=="oci8"){
			$order_str = "NLSSORT(a.PER_NAME,'NLS_SORT=THAI_DICTIONARY') $SortType[$order_by], NLSSORT(a.PER_SURNAME,'NLS_SORT=THAI_DICTIONARY') $SortType[$order_by] ";
		}else{
			$order_str = "a.PER_NAME $SortType[$order_by] ,a.PER_SURNAME $SortType[$order_by] ";
		}
  	} elseif($order_by==3) {	//ตำแหน่งในการบริหารงาน
		$order_str = "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID, h.PM_SEQ_NO, i.LEVEL_SEQ_NO $SortType[$order_by], a.LEVEL_NO $SortType[$order_by], 
									NLSSORT(a.PER_NAME,'NLS_SORT=THAI_DICTIONARY'), NLSSORT(a.PER_SURNAME,'NLS_SORT=THAI_DICTIONARY'), a.PER_SALARY $SortType[$order_by], 
				   				SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10)";
  	}elseif($order_by==4) {	//ตำแหน่งในสายงาน
		if($search_per_type==1){
			$order_str = "b.PL_CODE $SortType[$order_by] ";
		}elseif($search_per_type==2){
			$order_str = "c.PN_CODE $SortType[$order_by]";
		}elseif($search_per_type==3){
			$order_str = "d.EP_CODE $SortType[$order_by]";
		} elseif($search_per_type==4){
			$order_str = "e.TP_CODE $SortType[$order_by]";
		}
  	} elseif($order_by==5) {	//ระดับตำแหน่ง
		$order_str = "a.LEVEL_NO ".$SortType[$order_by];
	} elseif($order_by==6){	//สำนัก / กอง
		$order_str = "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID $SortType[$order_by]".$org_cond.", b.ORG_ID $SortType[$order_by]";
  	}

	if($DPISDB=="odbc"){	
		if($search_per_type==1){
			$cmd = " select 	a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME,a.PER_ENG_NAME,a.PER_ENG_SURNAME, a.LEVEL_NO, a.PER_TYPE, a.ORG_ID as ORG_ID_ASS, a.PER_FILE_NO, 
											a.POS_ID, b.POS_NO_NAME, b.POS_NO, b.PL_CODE, b.PM_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.PT_CODE, a.PER_CARDNO,
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_BIRTHDATE, a.PER_RETIREDATE, a.PER_STARTDATE, a.PER_OCCUPYDATE
							 from 		(	
												(
							 						PER_PERSONAL a
													left join PER_POSITION b on (a.POS_ID=b.POS_ID)	
												)	left join PER_ORG f on (b.ORG_ID=f.ORG_ID)
											)	left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							$search_condition
							 order by $order_str  ";
				if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd); }
		}elseif($search_per_type==2){
			$cmd = " select 	a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME,a.PER_ENG_NAME,a.PER_ENG_SURNAME, a.LEVEL_NO, a.PER_TYPE, a.ORG_ID as ORG_ID_ASS, a.PER_FILE_NO, 
											a.POEM_ID as POS_ID, c.POEM_NO_NAME as POS_NO_NAME, c.POEM_NO as POS_NO, c.PN_CODE as PL_CODE, c.ORG_ID, c.ORG_ID_1, 
											a.PER_CARDNO,a.PER_SALARY, a.PER_MGTSALARY, a.PER_BIRTHDATE, a.PER_RETIREDATE, a.PER_STARTDATE, a.PER_OCCUPYDATE
							 from 		(	
												(
													PER_PERSONAL a
													left join PER_POS_EMP c on (a.POEM_ID=c.POEM_ID)	
												)	left join PER_ORG f on (c.ORG_ID=f.ORG_ID)
											)	left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							$search_condition
							 order by $order_str  ";
						if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("c.ORG_ID", "a.ORG_ID", $cmd); }
		}elseif($search_per_type==3){
			$cmd = " select 	a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME,a.PER_ENG_NAME,a.PER_ENG_SURNAME, a.LEVEL_NO, a.PER_TYPE, a.ORG_ID as ORG_ID_ASS, a.PER_FILE_NO, 
											a.POEMS_ID as POS_ID, d.POEMS_NO_NAME as POS_NO_NAME, d.POEMS_NO as POS_NO, d.EP_CODE as PL_CODE, d.ORG_ID, d.ORG_ID_1,  
											a.PER_CARDNO,a.PER_SALARY, a.PER_MGTSALARY, a.PER_BIRTHDATE,a.PER_RETIREDATE, a.PER_STARTDATE, a.PER_OCCUPYDATE
							 from 		(	
												(
													PER_PERSONAL a
													left join PER_POS_EMPSER d on (a.POEMS_ID=d.POEMS_ID)	
												)	left join PER_ORG f on (d.ORG_ID=f.ORG_ID)
											)	left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							$search_condition
							 order by $order_str  ";
					if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("d.ORG_ID", "a.ORG_ID", $cmd); }
		} elseif($search_per_type==4){
			$cmd = " select 	a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME,a.PER_ENG_NAME,a.PER_ENG_SURNAME, a.LEVEL_NO, a.PER_TYPE, a.ORG_ID as ORG_ID_ASS, a.PER_FILE_NO, 
											a.POT_ID as POS_ID, e.POT_NO_NAME as POS_NO_NAME, e.POT_NO as POS_NO, e.TP_CODE as PL_CODE, e.ORG_ID, e.ORG_ID_1,  
											a.PER_CARDNO,a.PER_SALARY, a.PER_MGTSALARY, a.PER_BIRTHDATE,a.PER_RETIREDATE, a.PER_STARTDATE, a.PER_OCCUPYDATE
							 from 		(	
												(
													PER_PERSONAL a
													left join PER_POS_TEMP e on (a.POT_ID=e.POT_ID)	
												)	left join PER_ORG f on (e.ORG_ID=f.ORG_ID)
											)	left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							$search_condition
							 order by $order_str  ";
					if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("e.ORG_ID", "a.ORG_ID", $cmd); }
		} // end if
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		if($search_per_type==1){
			$cmd = " select a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME,a.PER_ENG_NAME,a.PER_ENG_SURNAME, a.LEVEL_NO, a.PER_TYPE,a.PER_STATUS, a.ORG_ID as ORG_ID_ASS, a.PER_FILE_NO, 
                                    a.POS_ID, b.POS_NO_NAME, b.POS_NO, b.PL_CODE, b.PM_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.PT_CODE, a.PER_CARDNO,
                                    a.PER_SALARY, a.PER_MGTSALARY, a.PER_RETIREDATE, a.PER_BIRTHDATE, a.PER_POSDATE, a.PER_STARTDATE, a.PER_OCCUPYDATE,
                                    a.PER_GENDER ,a.PER_MOBILE,a.PER_EMAIL ,a.PER_OFFNO
                                from PER_PERSONAL a, PER_POSITION b, PER_ORG f, PER_ORG g, PER_MGT h, PER_LEVEL i
                                where a.POS_ID=b.POS_ID(+) and a.DEPARTMENT_ID=g.ORG_ID 
                                    and b.ORG_ID=f.ORG_ID(+) 
                                    and b.PM_CODE=h.PM_CODE(+) 
                                    and a.LEVEL_NO=i.LEVEL_NO(+) 
                                        $search_condition 
                                order by $order_str ";
                    if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd); }
		}elseif($search_per_type==2){
			$cmd = " select a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME,a.PER_ENG_NAME,a.PER_ENG_SURNAME, a.LEVEL_NO, a.PER_TYPE,a.PER_STATUS, a.ORG_ID as ORG_ID_ASS, a.PER_FILE_NO, 
                                    a.POEM_ID as POS_ID, c.POEM_NO_NAME as POS_NO_NAME, c.POEM_NO as POS_NO, c.PN_CODE as PL_CODE, c.ORG_ID, c.ORG_ID_1, 
                                    a.PER_CARDNO,a.PER_SALARY, a.PER_MGTSALARY, a.PER_BIRTHDATE, a.PER_RETIREDATE,a.PER_POSDATE,a.PER_STARTDATE, a.PER_OCCUPYDATE,
                                    a.PER_GENDER ,a.PER_MOBILE,a.PER_EMAIL  ,a.PER_OFFNO
                                from PER_PERSONAL a, PER_POS_EMP c, PER_ORG f, PER_ORG g 
                                where a.POEM_ID=c.POEM_ID(+) and a.DEPARTMENT_ID=g.ORG_ID and c.ORG_ID=f.ORG_ID(+) 
                                    $search_condition 
                                order by $order_str ";
                    if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("c.ORG_ID", "a.ORG_ID", $cmd); }
		}elseif($search_per_type==3){
			$cmd = " select a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME,a.PER_ENG_NAME,a.PER_ENG_SURNAME, a.LEVEL_NO, a.PER_TYPE,a.PER_STATUS, a.ORG_ID as ORG_ID_ASS, a.PER_FILE_NO, 
                                    a.POEMS_ID as POS_ID, d.POEMS_NO_NAME as POS_NO_NAME, d.POEMS_NO as POS_NO, d.EP_CODE as PL_CODE, d.ORG_ID, d.ORG_ID_1, 
                                    a.PER_CARDNO,a.PER_SALARY, a.PER_MGTSALARY, a.PER_BIRTHDATE, a.PER_RETIREDATE,a.PER_POSDATE,a.PER_STARTDATE, a.PER_OCCUPYDATE,
                                    a.PER_GENDER ,a.PER_MOBILE,a.PER_EMAIL  ,a.PER_OFFNO
                                 from PER_PERSONAL a, PER_POS_EMPSER d, PER_ORG f, PER_ORG g 
                                 where a.POEMS_ID=d.POEMS_ID(+) and a.DEPARTMENT_ID=g.ORG_ID and d.ORG_ID=f.ORG_ID(+) 
                                 $search_condition 
                                 order by $order_str ";
                        if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("d.ORG_ID", "a.ORG_ID", $cmd); }
		} elseif($search_per_type==4){
			$cmd = " select a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME,a.PER_ENG_NAME,a.PER_ENG_SURNAME, a.LEVEL_NO, a.PER_TYPE,a.PER_STATUS, a.ORG_ID as ORG_ID_ASS, a.PER_FILE_NO, 
                                    a.POT_ID as POS_ID, e.POT_NO_NAME as POS_NO_NAME, e.POT_NO as POS_NO, e.TP_CODE as PL_CODE, e.ORG_ID, e.ORG_ID_1, 
                                    a.PER_CARDNO,a.PER_SALARY, a.PER_MGTSALARY, a.PER_BIRTHDATE, a.PER_RETIREDATE,a.PER_POSDATE,a.PER_STARTDATE, a.PER_OCCUPYDATE,
                                    a.PER_GENDER ,a.PER_MOBILE,a.PER_EMAIL  ,a.PER_OFFNO
                                from PER_PERSONAL a, PER_POS_TEMP e, PER_ORG f, PER_ORG g 
                                where a.POT_ID=e.POT_ID(+) 
                                    and a.DEPARTMENT_ID=g.ORG_ID 
                                    and e.ORG_ID=f.ORG_ID(+) 
                                    $search_condition 
                                order by $order_str ";
                        if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("e.ORG_ID", "a.ORG_ID", $cmd); }
		} // end if
	}elseif($DPISDB=="mysql"){	
		if($search_per_type==1){
			$cmd = " select 	a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME,a.PER_ENG_NAME,a.PER_ENG_SURNAME, a.LEVEL_NO, a.PER_TYPE, a.ORG_ID as ORG_ID_ASS, a.PER_FILE_NO, 
											a.POS_ID, b.POS_NO_NAME, b.POS_NO, b.PL_CODE, b.PM_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.PT_CODE, a.PER_CARDNO,
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_BIRTHDATE, a.PER_RETIREDATE, a.PER_STARTDATE, a.PER_OCCUPYDATE
							 from 		(	
												(
													PER_PERSONAL a
													left join PER_POSITION b on (a.POS_ID=b.POS_ID)	
												)	left join PER_ORG f on (b.ORG_ID=f.ORG_ID)
											)	left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							$search_condition
							 order by $order_str  ";
					if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd); }
		}elseif($search_per_type==2){
			$cmd = " select 	a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME,a.PER_ENG_NAME,a.PER_ENG_SURNAME, a.LEVEL_NO, a.PER_TYPE, a.ORG_ID as ORG_ID_ASS, a.PER_FILE_NO, 
											a.POEM_ID as POS_ID, c.POEM_NO_NAME as POS_NO_NAME, c.POEM_NO as POS_NO, c.PN_CODE as PL_CODE, c.ORG_ID, c.ORG_ID_1,  
											a.PER_CARDNO,a.PER_SALARY, a.PER_MGTSALARY, a.PER_BIRTHDATE, a.PER_RETIREDATE, a.PER_STARTDATE, a.PER_OCCUPYDATE
							 from 		(	
												(
													PER_PERSONAL a
													left join PER_POS_EMP c on (a.POEM_ID=c.POEM_ID)	
												)	left join PER_ORG f on (c.ORG_ID=f.ORG_ID)
											)	left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							$search_condition
							 order by $order_str  ";
					if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("c.ORG_ID", "a.ORG_ID", $cmd); }
		}elseif($search_per_type==3){
			$cmd = " select 	a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME,a.PER_ENG_NAME,a.PER_ENG_SURNAME, a.LEVEL_NO, a.PER_TYPE, a.ORG_ID as ORG_ID_ASS, a.PER_FILE_NO, 
											a.POEMS_ID as POS_ID, d.POEMS_NO_NAME as POS_NO_NAME, d.POEMS_NO as POS_NO, d.EP_CODE as PL_CODE, d.ORG_ID, d.ORG_ID_1, 
											a.PER_CARDNO,a.PER_SALARY, a.PER_MGTSALARY, a.PER_BIRTHDATE, a.PER_RETIREDATE, a.PER_STARTDATE, a.PER_OCCUPYDATE
							 from 		(	
												(
													PER_PERSONAL a
													left join PER_POS_EMPSER d on (a.POEMS_ID=d.POEMS_ID)	
												)	left join PER_ORG f on (d.ORG_ID=f.ORG_ID)
											)	left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							$search_condition
							 order by $order_str  ";
				if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("d.ORG_ID", "a.ORG_ID", $cmd); }
		}elseif($search_per_type==4){
			$cmd = " select 	a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME,a.PER_ENG_NAME,a.PER_ENG_SURNAME, a.LEVEL_NO, a.PER_TYPE, a.ORG_ID as ORG_ID_ASS, a.PER_FILE_NO, 
											a.POT_ID as POS_ID, e.POT_NO_NAME as POS_NO_NAME, d.POT_NO as POS_NO, d.TP_CODE as PL_CODE, e.ORG_ID, e.ORG_ID_1, 
											a.PER_CARDNO,a.PER_SALARY, a.PER_MGTSALARY, a.PER_BIRTHDATE, a.PER_RETIREDATE, a.PER_STARTDATE, a.PER_OCCUPYDATE
							 from 		(	
												(
													PER_PERSONAL a
													left join PER_POS_TEMP e on (a.POT_ID=e.POT_ID)	
												)	left join PER_ORG f on (e.ORG_ID=f.ORG_ID)
											)	left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							$search_condition
							 order by $order_str  ";
				if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("e.ORG_ID", "a.ORG_ID", $cmd); }
		} // end if
	} // end if
        
        
        
	$count_data = $db_dpis->send_cmd($cmd);
        if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
//	$db_dpis->show_error();
	

	if($count_data){
		$colshow_cnt=0;		// หาจำนวน column ที่แสดงจริง
		for($i=0; $i<count($arr_column_sel); $i++){
			if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
		}

		$xlsRow = 0;
		/**$temp_report_title = "$REF_NAME||$NAME||$report_title";
		$arr_title = explode("||", $temp_report_title);**/
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			for($j=0; $j < $colshow_cnt-1; $j++) 
				$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} //for($i=0; $i<count($arr_title); $i++){

		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			for($j=0; $j < $colshow_cnt-1; $j++) 
				$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} //if($company_name){
		
		print_header();

		// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
                
		if ($MFA_FLAG==1) {
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");
		} elseif ($BKK_FLAG==1) {
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");
		} else {
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");
		}
		$wsdata_align_1 = $data_align;
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		$data_count = 1;
		$rowscounter = 1;/*Release 5.2.1.10*/
                $flush_cnt=0;/*Release 5.2.1.10*/
                
                //ob_start();/*Release 5.2.1.10*/
                
		while($data = $db_dpis->get_array()){
			$data_count++;

			$PER_ID = trim($data[PER_ID]);
			$PER_TYPE = $data[PER_TYPE];
			$PER_FILE_NO = trim($data[PER_FILE_NO]);
			$POS_ID = $data[POS_ID];
			$POS_NO_NAME = trim($data[POS_NO_NAME]);
			if (substr($POS_NO_NAME,0,4)=="กปด.")
				$POS_NO = $POS_NO_NAME." ".trim($data[POS_NO]);
			else
				$POS_NO = $POS_NO_NAME.trim($data[POS_NO]);
			$PL_CODE = $data[PL_CODE];
			$PM_CODE = $data[PM_CODE];
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$PER_NAME = $data[PER_NAME];
			$PER_SALARY = trim($data[PER_SALARY]);
			//$PER_SALARY = number_format($PER_SALARY);
			$PER_SALARY = $PER_SALARY;
			
			$PER_MGTSALARY = trim($data[PER_MGTSALARY]);
			//$PER_MGTSALARY = number_format($PER_MGTSALARY);
			
			$PER_SURNAME = $data[PER_SURNAME];
			$FULLNAME = "$PER_NAME $PER_SURNAME";
                        
                        $PER_ENG_NAME=$data[PER_ENG_NAME];
                        $PER_ENG_SURNAME=$data[PER_ENG_SURNAME];
                        
			$CARDNO = $data[PER_CARDNO];
			
			$PER_RETIREDATE = show_date_format($data[PER_RETIREDATE],$DATE_DISPLAY);
			$BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
                        
                        /*http://dpis.ocsc.go.th/Service/node/1716*/
                        $PER_STARTDATE=$data[PER_STARTDATE];
                        $today = date("Y-m-d");
                       $PER_STATUS = $data[PER_STATUS];
            
                       if($PER_STATUS == 2){
                           $today = $data[PER_POSDATE];
                           
                           if(!$data[PER_POSDATE]){//ถ้าไม่มีวันที่พ้นจากส่วนราชการ ให้หยิบวันที่มีผลจากประวัติการดำรงตำแหน่ง ที่มีประเภทเคลื่อนไหว เป็น เกษียณอายุ, เกษียณอายุก่อนกำหนด
                           $cmd = "SELECT a.per_id,a.POH_EFFECTIVEDATE FROM PER_POSITIONHIS a, PER_MOVMENT b WHERE 
                                        a.per_id = $PER_ID 
                                        and a.mov_code = b.mov_code  
                                        and b.MOV_SUB_TYPE in (92,93)
                                  ORDER BY a.POH_EFFECTIVEDATE DESC";
                               $db_dpis2->send_cmd($cmd);
                               $data1 = $db_dpis2->get_array();
                               $today = $data1[POH_EFFECTIVEDATE];
                
                                   if(!$data1[POH_EFFECTIVEDATE]){//ถ้าประวัติไม่มีประเภทการเคลื่อนไหว เกษียณอายุ, เกษียณอายุก่อนกำหนด ให้หยิบวันที่มีผลล่าสุดของประวัติการดำรงตำแหน่งมาคำนวน
                                        $cmd = "SELECT max(POH_EFFECTIVEDATE) as POH_EFFECTIVEDATE FROM PER_POSITIONHIS where   per_id = $PER_ID 
                                                ORDER BY POH_EFFECTIVEDATE DESC";
                                       $db_dpis2->send_cmd($cmd);
                                       $data2 = $db_dpis2->get_array();
                                       $today = $data2[POH_EFFECTIVEDATE];
                                   }
                           }
                       }
            
                        $DATE_DIFF = date_difference($today, $PER_STARTDATE, "full"); /*Release 5.2.1.18*/
                        
                        $MyAge = date_difference($today, $data[PER_BIRTHDATE], "full");
                        
                        /*Release 5.1.0.4 Begin*/
                        $PER_GENDER=$data[PER_GENDER];
                        if(!empty($PER_GENDER)){
                            if($PER_GENDER=="1"){
                               $PER_GENDER="ชาย"; 
                            }else{
                               $PER_GENDER="หญิง"; 
                            }
                        }else{
                            $PER_GENDER="";
                        }
                        $PER_MOBILE=$data[PER_MOBILE];
                        $PER_EMAIL=$data[PER_EMAIL];
                        /*Release 5.1.0.4 End*/
                        $PER_OFFNO=$data[PER_OFFNO];

                                
                        
			$PER_TYPE = trim($data[PER_TYPE]);
			$STARTDATE = show_date_format($data[PER_STARTDATE],$DATE_DISPLAY);
			$OCCUPYDATE = show_date_format($data[PER_OCCUPYDATE],$DATE_DISPLAY);
			
			$cmd = "select LEVEL_NAME, POSITION_TYPE from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
                        
			$db_dpis2->send_cmd($cmd);
                        if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
			$data2=$db_dpis2->get_array();
			$LEVEL_NAME=trim($data2[LEVEL_NAME]);
			$POSITION_TYPE=trim($data2[POSITION_TYPE]);
			
			$SALARY_MIDPOINT = "";
			$cmd = " select LAYER_TYPE, a.POS_NO, a.PL_CODE from PER_POSITION a, PER_LINE b where POS_ID = $POS_ID and a.PL_CODE = b.PL_CODE ";
                        
			$db_dpis2->send_cmd($cmd);
                        if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
			$data2 = $db_dpis2->get_array();
			$LAYER_TYPE = $data2[LAYER_TYPE] + 0;

			$cmd = " select 	LAYER_SALARY_MAX, LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, 
							LAYER_EXTRA_MIDPOINT,LAYER_EXTRA_MIDPOINT1,LAYER_EXTRA_MIDPOINT2, LAYER_SALARY_FULL
							from	PER_LAYER 
							where LAYER_TYPE=0 and LEVEL_NO='$LEVEL_NO' and LAYER_NO=0 ";
                        
			$db_dpis2->send_cmd($cmd);
                        if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
			$data2 = $db_dpis2->get_array();
			$LAYER_SALARY_MAX = $data2[LAYER_SALARY_MAX];
                        
                        /* Release 5.2.1.20 http://dpis.ocsc.go.th/Service/node/1918*/ 
                        //if ($LAYER_TYPE==1 && ($LEVEL_NO == "O3" || $LEVEL_NO == "K5") && $data[PER_SALARY] <= $LAYER_SALARY_MAX) {
			if ($LAYER_TYPE==1 && ($LEVEL_NO == "O3" || $LEVEL_NO == "K5") && $data[PER_SALARY] < $data[LAYER_SALARY_FULL]) {
				$LAYER_SALARY_MIDPOINT = $data2[LAYER_EXTRA_MIDPOINT] + 0;
				$LAYER_SALARY_MIDPOINT1 = $data2[LAYER_EXTRA_MIDPOINT1] + 0;
				$LAYER_SALARY_MIDPOINT2 = $data2[LAYER_EXTRA_MIDPOINT2] + 0;
			} else {
				$LAYER_SALARY_MIDPOINT = $data2[LAYER_SALARY_MIDPOINT] + 0;
				$LAYER_SALARY_MIDPOINT1 = $data2[LAYER_SALARY_MIDPOINT1] + 0;
				$LAYER_SALARY_MIDPOINT2 = $data2[LAYER_SALARY_MIDPOINT2] + 0;
			}
			if ($data[PER_SALARY] >= $LAYER_SALARY_MIDPOINT) $SALARY_MIDPOINT = number_format($LAYER_SALARY_MIDPOINT2);
			else $SALARY_MIDPOINT = number_format($LAYER_SALARY_MIDPOINT1);
			
			//$cmd = " select	POH_EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and LEVEL_NO='$LEVEL_NO' order by	POH_EFFECTIVEDATE ";
                        
                        $cmd = " select MIN(POH_EFFECTIVEDATE) as POH_EFFECTIVEDATE 
                                from PER_POSITIONHIS where PER_ID=$PER_ID and trim(POH_LEVEL_NO)='$LEVEL_NO'
                                and trim(mov_code) not IN(SELECT trim(mov_code) FROM per_movment WHERE mov_code IN('110','11010','11020')) "; /* http://dpis.ocsc.go.th/Service/node/1865ไม่หยิบประเภทความเคลื่ีอนยไวที่่เป้น รักษาราชการแทน*/
                        
			$db_dpis2->send_cmd($cmd);
                        if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
		//	$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$CURR_EFFECTIVEDATE = show_date_format(trim($data2[POH_EFFECTIVEDATE]),$DATE_DISPLAY);
			
			//============  หาเครื่องราชฯ===================
			
			if($DPISDB=="odbc"){
				$cmd = " select		a.DC_CODE, b.DC_SHORTNAME,a.DEH_DATE 
								 from		PER_DECORATEHIS a
												left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
								 where	a.PER_ID=$PER_ID and b.DC_TYPE in(1,2) 
								 order by LEFT(trim(a.DEH_DATE), 10) desc ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select		a.DC_CODE, b.DC_SHORTNAME,a.DEH_DATE 
								 from		PER_DECORATEHIS a, PER_DECORATION b
								 where	a.DC_CODE=b.DC_CODE(+) and a.PER_ID=$PER_ID and b.DC_TYPE in(1,2) 
								 order by a.DEH_DATE desc "; /*SUBSTR(trim(a.DEH_DATE), 1, 10) desc*/ /*tune qry v.1*/
			}elseif($DPISDB=="mysql"){
				$cmd = " select		a.DC_CODE, b.DC_SHORTNAME,a.DEH_DATE 
								 from		PER_DECORATEHIS a
												left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
								 where	a.PER_ID=$PER_ID and b.DC_TYPE in(1,2) 
								 order by LEFT(trim(a.DEH_DATE), 10) desc ";
			} // end if
			$db_dpis2->send_cmd($cmd);
                        if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
	//		$db_dpis2->show_error();
			$data4 = $db_dpis2->get_array();
			$DECORATE = trim($data4[DC_SHORTNAME]);
                        $DEH_YEAR="";
                        if(!empty($data4[DEH_DATE])){
                            $DEH_DATE_ARR = explode("-",trim($data4[DEH_DATE]));
                            $DEH_YEAR = $DEH_DATE_ARR[0]+543;
                        }
                        
                        
			
			//====================================================== การศึกษา =====================================================
			$EL_NAME1 = $EN_NAME1 = $EM_NAME1 =  $INS_NAME1 = $EL_NAME2 = $EN_NAME2 = $EM_NAME2 =  $INS_NAME2 = 
			$EL_NAME4 = $EN_NAME4 = $EM_NAME4 =  $INS_NAME4 = "";
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
			}elseif($DPISDB=="oci8"){
                            
                                /*วุฒิในตำแหน่งปัจจุบัน*/
				$cmd = "select b.EN_NAME, d.INS_NAME, c.EL_NAME, e.EM_NAME, EDU_INSTITUTE,
                                            a.CT_CODE_EDU ,a.EDU_ENDYEAR ,a.ST_CODE
                                        from PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_INSTITUTE d, PER_EDUCMAJOR e
                                        where a.PER_ID='$PER_ID' 
                                            and a.EDU_TYPE like '%2%' 
                                            and a.en_code=b.en_code(+) 
                                            and a.ins_code=d.ins_code(+) 
                                            and b.EL_CODE=c.EL_CODE(+) 
                                            and a.EM_CODE=e.EM_CODE(+)";
				$db_dpis2->send_cmd($cmd);
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
		//		$db_dpis2->show_error();
		//		echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME2 = trim($data4[EN_NAME]);
				$INS_NAME2 = trim($data4[INS_NAME]);
				$EL_NAME2 = trim($data4[EL_NAME]);
				$EM_NAME2 = trim($data4[EM_NAME]);
                                $CT_CODE_EDU2 = trim($data4[CT_CODE_EDU]);
                                $EDU_ENDYEAR2 = trim($data4[EDU_ENDYEAR]);
                                $ST_CODE2= trim($data4[ST_CODE]);
                                 /*ประเทศที่สำเร็จการศึกษา*/
                                $cmd =" select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE_EDU2' ";
                                
                                $db_dpis2->send_cmd($cmd);
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
                                $dataC = $db_dpis2->get_array();
                                $CT_NAME_EDU2 = $dataC[CT_NAME];
                                /*ประเภททุน*/
                                $cmd =" select ST_NAME from PER_SCHOLARTYPE where ST_CODE='$ST_CODE2' ";
                                
                                $db_dpis2->send_cmd($cmd);
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
                                $dataC = $db_dpis2->get_array();
                                $ST_NAME2 = $dataC[ST_NAME];
                                
                                
				if (!$INS_NAME2) $INS_NAME2 = trim($data4[EDU_INSTITUTE]);
				
                                /*วุฒิที่ใช้บรรจุ*/
				$cmd = "select b.EN_NAME, d.INS_NAME, c.EL_NAME, e.EM_NAME, EDU_INSTITUTE,
                                            a.CT_CODE_EDU ,a.EDU_ENDYEAR ,a.ST_CODE
                                        from PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_INSTITUTE d, PER_EDUCMAJOR e
					where a.PER_ID='$PER_ID' 
                                            and a.EDU_TYPE like '%1%' 
                                            and a.en_code=b.en_code(+) 
                                            and a.ins_code=d.ins_code(+) 
                                            and b.EL_CODE=c.EL_CODE(+) 
                                            and a.EM_CODE=e.EM_CODE(+)";
				//die($cmd);
                                
				$db_dpis2->send_cmd($cmd);
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
				$data4 = $db_dpis2->get_array();
				$EN_NAME1 = trim($data4[EN_NAME]);
				$INS_NAME1 = trim($data4[INS_NAME]);
				$EL_NAME1 = trim($data4[EL_NAME]);		
				$EM_NAME1 = trim($data4[EM_NAME]);
                                $CT_CODE_EDU1 = trim($data4[CT_CODE_EDU]);
                                $EDU_ENDYEAR1 = trim($data4[EDU_ENDYEAR]);
                                $ST_CODE1= trim($data4[ST_CODE]);
                                
                                /*ประเทศที่สำเร็จการศึกษา*/
                                $cmd =" select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE_EDU1' ";
                                
                                $db_dpis2->send_cmd($cmd);
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
                                $dataC = $db_dpis2->get_array();
                                $CT_NAME_EDU1 = $dataC[CT_NAME];
                                /*ประเภททุน*/
                                $cmd =" select ST_NAME from PER_SCHOLARTYPE where ST_CODE='$ST_CODE1' ";
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
                                $db_dpis2->send_cmd($cmd);
                                $dataC = $db_dpis2->get_array();
                                $ST_NAME1 = $dataC[ST_NAME];
                                
                                
                                
				if (!$INS_NAME1) $INS_NAME1 = trim($data4[EDU_INSTITUTE]);
                                /*วุฒิสูงสุด*/
				$cmd = "select b.EN_NAME, d.INS_NAME, c.EL_NAME, e.EM_NAME, EDU_INSTITUTE, 
                                            a.CT_CODE_EDU ,a.EDU_ENDYEAR ,a.ST_CODE
                                        from PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_INSTITUTE d, PER_EDUCMAJOR e
                                        where a.PER_ID='$PER_ID' 
                                            and a.EDU_TYPE like '%4%' 
                                            and a.en_code=b.en_code(+) 
                                            and a.ins_code=d.ins_code(+) 
                                            and b.EL_CODE=c.EL_CODE(+) 
                                            and a.EM_CODE=e.EM_CODE(+)";
										
				$db_dpis2->send_cmd($cmd);
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
			//	$db_dpis2->show_error();
			//	echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME4 = trim($data4[EN_NAME]);
				$INS_NAME4 = trim($data4[INS_NAME]);
				$EL_NAME4 = trim($data4[EL_NAME]);		
				$EM_NAME4 = trim($data4[EM_NAME]);
                                $CT_CODE_EDU4 = trim($data4[CT_CODE_EDU]);
                                $EDU_ENDYEAR4 = trim($data4[EDU_ENDYEAR]);
                                $ST_CODE4= trim($data4[ST_CODE]);
                                
                                 /*ประเทศที่สำเร็จการศึกษา*/
                                $cmd =" select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE_EDU4' ";
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
                                $db_dpis2->send_cmd($cmd);
                                $dataC = $db_dpis2->get_array();
                                $CT_NAME_EDU4 = $dataC[CT_NAME];
                                /*ประเภททุน*/
                                $cmd =" select ST_NAME from PER_SCHOLARTYPE where ST_CODE='$ST_CODE4' ";
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
                                $db_dpis2->send_cmd($cmd);
                                $dataC = $db_dpis2->get_array();
                                $ST_NAME4 = $dataC[ST_NAME];
                                
                                
                                
				if (!$INS_NAME4) $INS_NAME4 = trim($data4[EDU_INSTITUTE]);
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
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
		//		$db_dpis2->show_error();
		//		echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME2 = trim($data4[EN_NAME]);
				$INS_NAME2 = trim($data4[INS_NAME]);
				$EL_NAME2 = trim($data4[EL_NAME]);
				$EM_NAME2 = trim($data4[EM_NAME]);
				if (!$INS_NAME2) $INS_NAME2 = trim($data4[EDU_INSTITUTE]);
				
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
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
			//	$db_dpis2->show_error();
			//	echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME1 = trim($data4[EN_NAME]);
				$INS_NAME1 = trim($data4[INS_NAME]);
				$EL_NAME1 = trim($data4[EL_NAME]);		
				$EM_NAME1 = trim($data4[EM_NAME]);
                                
				if (!$INS_NAME1) $INS_NAME1 = trim($data4[EDU_INSTITUTE]);
				
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
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
			//	$db_dpis2->show_error();
			//	echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME4 = trim($data4[EN_NAME]);
				$INS_NAME4 = trim($data4[INS_NAME]);
				$EL_NAME4 = trim($data4[EL_NAME]);		
				$EM_NAME4 = trim($data4[EM_NAME]);		 
				if (!$INS_NAME4) $INS_NAME4 = trim($data4[EDU_INSTITUTE]);
			}  
                        /*ด้านความเชี่ยวชาญพิเศษ , เน้นทาง*/
                         $cmd ="  select 
                                    case when skgrp.ref_code is null then skgrp.ss_name else 
                                      (select ss_name FROM PER_SPECIAL_SKILLGRP where trim(ss_code)=trim(skgrp.ref_code))  
                                      end as name_main ,
                                       sp_skmin.spmin_title,
                                      case when (skgrp.ss_code is not null and skgrp.ref_code is null ) then null else skgrp.SS_NAME end as name_sub ,
                                      sk.SPS_EMPHASIZE ,sk.SPS_SEQ_NO
                                  from PER_SPECIAL_SKILL sk 
                                  left join PER_SPECIAL_SKILLGRP skgrp ON(trim(skgrp.ss_code)=trim(sk.ss_code))
                                  left join per_mapping_skillmin skmin on(trim(skgrp.ss_code)=trim(skmin.ss_code))
                                  left join per_special_skillmin sp_skmin  on(sp_skmin.spmin_code=skmin.spmin_code)
                                  where sk.PER_ID = $PER_ID
                                            ORDER BY sk.SPS_SEQ_NO asc ";
                                         //  die("<pre>".$cmd);
                                    $db_dpis2->send_cmd($cmd);
                                    if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
                                    $no = $SPS_SEQ_NO;
                                    $NAME_MAIN="";
                                    $SPMIN_TITLE="";
                                    $NAME_SUB="";
                                    $SPS_EMPHASIZE="";
                                 while ($dataSP = $db_dpis2->get_array()) {
                                        $num_no = $dataSP [SPS_SEQ_NO];
                                        $NAME_MAIN.=$num_no.".".trim($dataSP[NAME_MAIN])."\n";
                                        $SPMIN_TITLE.=$num_no.".".trim($dataSP[SPMIN_TITLE])."\n";
                                        $NAME_SUB.=$num_no.".".trim($dataSP[NAME_SUB])."\n";
                                        $SPS_EMPHASIZE.=$num_no.".".trim($dataSP[SPS_EMPHASIZE])."\n";
                                        $no++;    
                               }
                            //  echo "ลำดับ => ".$num_no;
                        /*----*/
                        
			//============ ระดับตำแหน่ง ===========	
			if ($PER_TYPE == 1) {	
				$arr_temp = explode(" ", $LEVEL_NAME);  
				if ($arr_temp[1]) $LEVEL_NAME =  $arr_temp[1]; 
			}
			$PN_CODE = trim($data[PN_CODE]);
			if ($PN_CODE) {
				$cmd = "	select PN_NAME, PN_SHORTNAME,PN_ENG_NAME from PER_PRENAME where PN_CODE='$PN_CODE'";
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PN_NAME = $data2[PN_NAME];
				$PN_SHORTNAME = $data2[PN_SHORTNAME];
                                $PN_ENG_NAME = $data2[PN_ENG_NAME];
			}
					
			$PL_NAME = "";
			if ($PER_TYPE == 1) {
				$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = $data2[PL_NAME];
				
				$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE' ";
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PM_NAME = $data2[PM_NAME];
				
				$PT_CODE = trim($data[PT_CODE]);
				$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PT_NAME = trim($data2[PT_NAME]);

				$PL_NAME = (trim($PL_NAME))? ("$PL_NAME ". (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?" $PT_NAME":"")) : "";
			} elseif ($PER_TYPE == 2) {
				$cmd = " select PN_NAME as PL_NAME from PER_POS_NAME where PN_CODE='$PL_CODE' ";
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = $data2[PL_NAME];
				$PL_NAME = (trim($PL_NAME))? "$PL_NAME " : "";
			} elseif ($PER_TYPE == 3) {
				$cmd = " select EP_NAME as PL_NAME from PER_EMPSER_POS_NAME where EP_CODE='$PL_CODE' ";
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = $data2[PL_NAME];
				$PL_NAME = (trim($PL_NAME))? "$PL_NAME ": "";
			}	elseif ($PER_TYPE == 4) {
				$cmd = " select TP_NAME as PL_NAME from PER_TEMP_POS_NAME where TP_CODE='$PL_CODE' ";
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = $data2[PL_NAME];
				$PL_NAME = (trim($PL_NAME))? "$PL_NAME ": "";
			}	
				
			$ORG_ID = $data[ORG_ID];
			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID ";
                        if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = $data2[ORG_NAME];
			$department = $data2[ORG_ID_REF];
			
			$ORG_ID_1 = $data[ORG_ID_1];
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
                        if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_1_NAME = $data2[ORG_NAME];
			
			$ORG_ID_2 = $data[ORG_ID_2];
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
                        if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_2_NAME = $data2[ORG_NAME];
			
			$cmd = "select ORG_NAME, ORG_ID_REF from PER_ORG where org_id = $department ";
                        if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();				
			$DEPARTMENT_NAME = $data2[ORG_NAME];
			$ministry = $data2[ORG_ID_REF];
			
			$cmd = "select ORG_NAME from PER_ORG where org_id = $ministry ";
                        if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();				
			$MINISTRY_NAME  = $data2[ORG_NAME];
			
			$ORG_ID_ASS = $data[ORG_ID_ASS];
			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG_ASS where ORG_ID=$ORG_ID_ASS ";
                        if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_ASS = $data2[ORG_NAME];
			
			// === หาวันที่มีผล
			$level = array( "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "O1", "O2", "O3", "O4", "K1", "K2", "K3", "K4", "K5", "D1", "D2", "M1", "M2" );
			for ( $i=0; $i<count($level); $i++ ) { 
				$PL_NAME_O[$level[$i]] = "";
                                $LEVEL_DATE[$level[$i]] = "";
								$ORG_O[$level[$i]] = "";
                                /*เดิม*/
                                /*$cmd = " select POH_EFFECTIVEDATE
                                                from   PER_POSITIONHIS
                                                where PER_ID=$PER_ID and LEVEL_NO='$level[$i]'
                                                order by POH_EFFECTIVEDATE ";*/
                                /*Release 5.1.0.6 Begin*/
                                //if($PM_CODE){
                                    /*$cmd = " select POH_EFFECTIVEDATE
                                            from   PER_POSITIONHIS
                                            where PER_ID=$PER_ID and LEVEL_NO='$level[$i]' and PM_CODE='$PM_CODE' 
                                            order by POH_EFFECTIVEDATE ";*/
                                //}else{
                                    /* เดิม $cmd = " select POH_EFFECTIVEDATE
                                                from   PER_POSITIONHIS
                                                where PER_ID=$PER_ID and POH_LEVEL_NO='$level[$i]'
                                                order by POH_EFFECTIVEDATE "; */
                                //}
				 /*Release 5.1.0.6 End*/
                                 
                                 /*Release 5.2.1.7*/
                                $cmd = " select ph.POH_EFFECTIVEDATE,pl.PL_NAME,ph.POH_ORG3
                                                from   PER_POSITIONHIS ph,PER_LINE pl
                                                where PER_ID=$PER_ID and POH_LEVEL_NO='$level[$i]' and ph.PL_CODE = pl.PL_CODE
                                                order by POH_EFFECTIVEDATE ";
                                                
                                /*Release 5.2.1.7 End*/
				$db_dpis2->send_cmd($cmd);
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
				$data2 = $db_dpis2->get_array();
				if (trim($data2[POH_EFFECTIVEDATE])) {	
                    $PL_NAME_O[$level[$i]] = $data2[PL_NAME];
					$LEVEL_DATE[$level[$i]] = show_date_format($data2[POH_EFFECTIVEDATE],$DATE_DISPLAY);
					$ORG_O[$level[$i]]=trim($data2[POH_ORG3]);
				}
			} // end for

			if ($search_poh_mov_code || $search_poh_docno) {
				if ($search_poh_mov_code) 
					$cmd = " select	MOV_CODE, POH_DOCNO, POH_POS_NO, POH_PL_NAME, POH_PM_NAME, POH_LEVEL_NO, POH_ORG2, POH_ORG3, POH_ASS_ORG, POH_REMARK
									from PER_POSITIONHIS 
									where PER_ID=$PER_ID and MOV_CODE='$search_poh_mov_code' 
									order by	POH_EFFECTIVEDATE desc ";
				elseif ($search_poh_docno) 
					$cmd = " select	MOV_CODE, POH_DOCNO, POH_POS_NO, POH_PL_NAME, POH_PM_NAME, POH_LEVEL_NO, POH_ORG2, POH_ORG3, POH_ASS_ORG, POH_REMARK
									from PER_POSITIONHIS 
									where PER_ID=$PER_ID and POH_DOCNO='$search_poh_docno' 
									order by	POH_EFFECTIVEDATE desc ";
				$db_dpis2->send_cmd($cmd);
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
			//	$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$POH_MOV_CODE = trim($data2[MOV_CODE]);
				$POH_DOCNO = trim($data2[POH_DOCNO]);
				$POH_POS_NO = trim($data2[POH_POS_NO]);
				$POH_PL_NAME = trim($data2[POH_PL_NAME]);
				$POH_PM_NAME = trim($data2[POH_PM_NAME]);
				$POH_LEVEL_NO = trim($data2[POH_LEVEL_NO]);
				$POH_ORG2 = trim($data2[POH_ORG2]);
				$POH_ORG3 = trim($data2[POH_ORG3]);
				$POH_ASS_ORG = trim($data2[POH_ASS_ORG]);
				$POH_REMARK = trim($data2[POH_REMARK]);

				$cmd = " select MOV_NAME from PER_MOVMENT where MOV_CODE='$POH_MOV_CODE' ";
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POH_MOV_NAME = $data2[MOV_NAME];
				
				$cmd = " select POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$POH_LEVEL_NO' ";
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POH_LEVEL_NAME = $data2[POSITION_LEVEL];
				
			}
                        
                        if($PER_ID){
                              // ความเชี่ยวชาญพิเศษ และ เน้นทาง col 1                                      
                                 $cmd = "  select * from (
                                                select rownum rnum, q1.* from ( 
                                                        select 	 	b.PER_ID, c.SS_NAME, SPS_EMPHASIZE, b.SS_CODE, b.LEVELSKILL_CODE, (case when b.AUDIT_FLAG is null or b.AUDIT_FLAG='N' then '0' else b.AUDIT_FLAG end) as AUDIT_FLAG,b.SPS_FLAG,c.REF_CODE
                                                        from 		PER_SPECIAL_SKILL b 
                                                                        left join PER_SPECIAL_SKILLGRP c on(b.SS_CODE = c.SS_CODE)
                                                        where 		PER_ID=$PER_ID   
                                                        ORDER BY         (case when b.AUDIT_FLAG is null or b.AUDIT_FLAG='N' then '0' else b.AUDIT_FLAG end)  DESC, b.SPS_SEQ_NO  asc  
                                                 )  q1
                                        ) where rnum =1 ";

                                $db_dpis2->send_cmd($cmd);
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
                                $data2 = $db_dpis2->get_array();
                                $SS_CHK_CODE = trim($data2[SS_CODE]);
                                $REF_CHK_CODE = trim($data2[REF_CODE]);
                                $AUDIT_FLAG = trim($data2[AUDIT_FLAG]);
                                
                                if($SS_CHK_CODE === "-1"){
                                    $SS_CODE = "";
                                    $REF_CODE = "";//trim($data2[SS_NAME]);
                                    $SPS_EMPHASIZE_M = "";
                                    $LEVELSKILL_CODE = "";
                                    $SPS_FLAG = "";
                                }else{
                                    if($REF_CHK_CODE == ""){
                                        $SS_CODE = trim($data2[SS_NAME]);
                                        $REF_CODE = "";
                                    }else{
                                        $SS_CODE = trim($data2[SS_NAME]);
                                        $SS_REF = trim($data2[REF_CODE]);
                                        $REF_CODE = get_chind_special_skill($db_dpis2,$SS_REF);//trim($data2[SS_NAME]);
                                    }
                                    
                                    if($AUDIT_FLAG === "N"){
                                        $SPS_EMPHASIZE_M = "*".trim($data2[SPS_EMPHASIZE]);
                                    }else{
                                        $SPS_EMPHASIZE_M = $AUDIT_FLAG.trim($data2[SPS_EMPHASIZE]);
                                    }
                                    $SPS_FLAG_C = trim($data2[SPS_FLAG]);
                                    if($SPS_FLAG_C == '1'){
                                        $SPS_FLAG = $SPS_FLAG_C."-ความเชี่ยวชาญในงานราชการ";
                                    }else if($SPS_FLAG_C == '2'){
                                         $SPS_FLAG = $SPS_FLAG_C."-ความเชี่ยวชาญอื่น ๆ";
                                    }else{
                                         $SPS_FLAG = "";
                                    }
                                    $LEVELSKILL_CODE = trim($data2[LEVELSKILL_CODE]);
                                    
                                }
                                $cmd = "   select LEVELSKILL_NAME
                                           from 		PER_LEVELSKILL
                                           where LEVELSKILL_CODE = $LEVELSKILL_CODE";

                                $db_dpis2->send_cmd($cmd);
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
                                $data2 = $db_dpis2->get_array();
                                if($SS_CHK_CODE === "-1"){
                                    $LEVELSKILL_NAME = "";
                                }else{
                                    $LEVELSKILL_NAME = $data2[LEVELSKILL_NAME];
                                }
                                
                                // ความเชี่ยวชาญพิเศษ และ เน้นทาง col 2  
                                $cmd = "  select * from (
                                                select rownum rnum2, q2.* from ( 
                                                        select 	 	b.PER_ID, c.SS_NAME as SS_NAME2, SPS_EMPHASIZE as SPS_EMPHASIZE2, b.SS_CODE as SS_CODE2, b.LEVELSKILL_CODE as LEVELSKILL_CODE2,  (case when b.AUDIT_FLAG is null or b.AUDIT_FLAG='N' then '0' else b.AUDIT_FLAG end) as AUDIT_FLAG2,b.SPS_FLAG as SPS_FLAG2,c.REF_CODE as REF_CODE2
                                                        from 		PER_SPECIAL_SKILL b 
                                                                        left join PER_SPECIAL_SKILLGRP c on(b.SS_CODE = c.SS_CODE)
                                                        where 		PER_ID=$PER_ID  
                                                        ORDER BY         (case when b.AUDIT_FLAG is null or b.AUDIT_FLAG='N' then '0' else b.AUDIT_FLAG end)  DESC, b.SPS_SEQ_NO  asc   
                                                 )  q2
                                        ) where rnum2 =2 ";
                                //echo '<pre>';
                                //die($cmd);
                                $db_dpis2->send_cmd($cmd);
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
                                $data2 = $db_dpis2->get_array();
                                $SS_CHK_CODE2 = trim($data2[SS_CODE2]);
                                $REF_CHK_CODE2 = trim($data2[REF_CODE2]);
                                $AUDIT_FLAG2 = trim($data2[AUDIT_FLAG2]);
                                
                                if($SS_CHK_CODE2 === "-1"){
                                    $SS_CODE2 = "";
                                    $REF_CODE2 = "";
                                    $SPS_EMPHASIZE_M2 = "";
                                    $LEVELSKILL_CODE2 = "";
                                    $SPS_FLAG2 = "";
                                }else{
                                     if($REF_CHK_CODE2 == ""){
                                        $SS_CODE2 = trim($data2[SS_NAME2]);
                                        $REF_CODE2 = "";
                                    }else{
                                        $SS_CODE2 = trim($data2[SS_NAME2]);
                                        $SS_REF2 = trim($data2[REF_CODE2]);
                                        $REF_CODE2 = get_chind_special_skill($db_dpis2,$SS_REF2);
                                    }
                                
                                    
                                    if($AUDIT_FLAG2 === "N"){
                                        $SPS_EMPHASIZE_M2 = "*".trim($data2[SPS_EMPHASIZE2]);
                                    }else{
                                        $SPS_EMPHASIZE_M2 = $AUDIT_FLAG2.trim($data2[SPS_EMPHASIZE2]);
                                    }
                                    $SPS_FLAG_C2 = trim($data2[SPS_FLAG2]);
                                    if($SPS_FLAG_C2 == '1'){
                                        $SPS_FLAG2 = $SPS_FLAG_C2."-ความเชี่ยวชาญในงานราชการ";
                                    }else if($SPS_FLAG_C2 == '2'){
                                         $SPS_FLAG2 = $SPS_FLAG_C2."-ความเชี่ยวชาญอื่น ๆ";
                                    }else{
                                         $SPS_FLAG2 = "";
                                    }
                                    $LEVELSKILL_CODE2 = trim($data2[LEVELSKILL_CODE2]);   
                                }
                                $cmd = "   select LEVELSKILL_NAME AS LEVELSKILL_NAME2
                                           from 		PER_LEVELSKILL
                                           where LEVELSKILL_CODE = $LEVELSKILL_CODE2";
                                           
                                $db_dpis2->send_cmd($cmd);
                                if($debug==1){ echo __LINE__.'<pre>'.$cmd.'<br>========================================<br>';}
                                $data2 = $db_dpis2->get_array();
                                 if($SS_CHK_CODE2 === "-1"){
                                    $LEVELSKILL_NAME = "";
                                }else{
                                    $LEVELSKILL_NAME2 = $data2[LEVELSKILL_NAME2];
                                }
                                
                        }
			
			$arr_data = (array) null;
			$arr_data[] = $POS_NO;
			$arr_data[] = $PN_NAME;//(($PN_SHORTNAME)?$PN_SHORTNAME:$PN_NAME).$PER_NAME." ".$PER_SURNAME;
                        
                        $arr_data[]= $PER_NAME;
                        $arr_data[]= $PER_SURNAME;
                        
                        $arr_data[]= $PN_ENG_NAME;
                        $arr_data[]= $PER_ENG_NAME;
                        $arr_data[]= $PER_ENG_SURNAME;
                        
			$arr_data[] = card_no_format($CARDNO,$CARD_NO_DISPLAY);
                        $arr_data[] = $PER_GENDER;/*Release 5.1.0.4 Begin แทรก เพศ*/
			$arr_data[] = $BIRTHDATE;
                        
                        
                        $arr_data[] = $MyAge; /*'ปี/เดือน/วัน , อายุข้าราชการ' Release 5.2.1.18*/
                        
			$arr_data[] = $PL_NAME;
			$arr_data[] = $PM_NAME;
			$arr_data[] = $POSITION_TYPE;
			$arr_data[] = $LEVEL_NAME;
			if ($BKK_FLAG!=1) $arr_data[] = $MINISTRY_NAME;
                        
			$arr_data[] = $DEPARTMENT_NAME;
			$arr_data[] = $ORG_NAME;
			$arr_data[] = $ORG_1_NAME;
			$arr_data[] = $ORG_2_NAME;
			$arr_data[] = $PER_SALARY;
			$arr_data[] = $PER_MGTSALARY;
			$arr_data[] = $PER_RETIREDATE;
			$arr_data[] = $STARTDATE;
                        $arr_data[] = $DATE_DIFF;
			$arr_data[] = $OCCUPYDATE;
			$arr_data[] = $EL_NAME1;
			$arr_data[] = $EN_NAME1;
                        $arr_data[] = $EM_NAME1;
			$arr_data[] = $INS_NAME1;
                        $arr_data[] = $CT_NAME_EDU1;/*Release 5.1.0.4 Begin*/
                        $arr_data[] = $EDU_ENDYEAR1;/*Release 5.1.0.4 Begin*/
                        $arr_data[] = $ST_NAME1;/*Release 5.1.0.4 Begin*/
                        
			$arr_data[] = $EL_NAME2;
			$arr_data[] = $EN_NAME2;
			$arr_data[] = $EM_NAME2;
			$arr_data[] = $INS_NAME2;
                        $arr_data[] = $CT_NAME_EDU2;/*Release 5.1.0.4 Begin*/
                        $arr_data[] = $EDU_ENDYEAR2;/*Release 5.1.0.4 Begin*/
                        $arr_data[] = $ST_NAME2;/*Release 5.1.0.4 Begin*/
                        
			$arr_data[] = $EL_NAME4;
			$arr_data[] = $EN_NAME4;
			$arr_data[] = $EM_NAME4;
			$arr_data[] = $INS_NAME4;
                        $arr_data[] = $CT_NAME_EDU4;//$CT_NAME_EDU;/*Release 5.1.0.4 Begin*/
                        $arr_data[] = $EDU_ENDYEAR4;//$EDU_ENDYEAR;/*Release 5.1.0.4 Begin*/
                        $arr_data[] = $ST_NAME4;/*Release 5.1.0.4 Begin*/
                        $arr_data[] = $NAME_MAIN;
                        $arr_data[] = $SPMIN_TITLE;
                        $arr_data[] = $NAME_SUB;
                        $arr_data[] = $SPS_EMPHASIZE;
                        
                       
                        
            $arr_data[] = $DEH_YEAR;
			$arr_data[] = $DECORATE;
            $arr_data[] = $PL_NAME_O["01"];
			$arr_data[] = $LEVEL_DATE["01"];
			$arr_data[] = $ORG_O["01"];
            $arr_data[] = $PL_NAME_O["02"];
			$arr_data[] = $LEVEL_DATE["02"];
			$arr_data[] = $ORG_O["02"];
            $arr_data[] = $PL_NAME_O["03"];
			$arr_data[] = $LEVEL_DATE["03"]; /**/
			$arr_data[] = $ORG_O["03"];
            $arr_data[] = $PL_NAME_O["04"];
			$arr_data[] = $LEVEL_DATE["04"];
			$arr_data[] = $ORG_O["04"];
            $arr_data[] = $PL_NAME_O["05"];
			$arr_data[] = $LEVEL_DATE["05"];
			$arr_data[] = $ORG_O["05"];
            $arr_data[] = $PL_NAME_O["06"];
			$arr_data[] = $LEVEL_DATE["06"];
			$arr_data[] = $ORG_O["06"];
            $arr_data[] = $PL_NAME_O["07"];
			$arr_data[] = $LEVEL_DATE["07"];
			$arr_data[] = $ORG_O["07"];
            $arr_data[] = $PL_NAME_O["08"];
			$arr_data[] = $LEVEL_DATE["08"];
			$arr_data[] = $ORG_O["08"];
            $arr_data[] = $PL_NAME_O["09"];
			$arr_data[] = $LEVEL_DATE["09"];
			$arr_data[] = $ORG_O["09"];
            $arr_data[] = $PL_NAME_O["10"];
			$arr_data[] = $LEVEL_DATE["10"];
			$arr_data[] = $ORG_O["10"];
            $arr_data[] = $PL_NAME_O["11"];
			$arr_data[] = $LEVEL_DATE["11"];
			$arr_data[] = $ORG_O["11"];
            $arr_data[] = $PL_NAME_O["O1"];
            $arr_data[] = $LEVEL_DATE["O1"];
			$arr_data[] = $ORG_O["O1"];
            $arr_data[] = $PL_NAME_O["O2"];
			$arr_data[] = $LEVEL_DATE["O2"];
			$arr_data[] = $ORG_O["O2"];
            $arr_data[] = $PL_NAME_O["O3"];
			$arr_data[] = $LEVEL_DATE["O3"];
			$arr_data[] = $ORG_O["O3"];
            $arr_data[] = $PL_NAME_O["O4"];
			$arr_data[] = $LEVEL_DATE["O4"];
			$arr_data[] = $ORG_O["O4"];
            $arr_data[] = $PL_NAME_O["K1"];
			$arr_data[] = $LEVEL_DATE["K1"];
			$arr_data[] = $ORG_O["K1"];
            $arr_data[] = $PL_NAME_O["K2"];
			$arr_data[] = $LEVEL_DATE["K2"];
			$arr_data[] = $ORG_O["K2"];
            $arr_data[] = $PL_NAME_O["K3"];
			$arr_data[] = $LEVEL_DATE["K3"];
			$arr_data[] = $ORG_O["K3"];
            $arr_data[] = $PL_NAME_O["K4"];
			$arr_data[] = $LEVEL_DATE["K4"];
			$arr_data[] = $ORG_O["K4"];
            $arr_data[] = $PL_NAME_O["K5"];
			$arr_data[] = $LEVEL_DATE["K5"];
			$arr_data[] = $ORG_O["K5"];
            $arr_data[] = $PL_NAME_O["D1"];
			$arr_data[] = $LEVEL_DATE["D1"];
			$arr_data[] = $ORG_O["D1"];
            $arr_data[] = $PL_NAME_O["D2"];
			$arr_data[] = $LEVEL_DATE["D2"];
			$arr_data[] = $ORG_O["D2"];
            $arr_data[] = $PL_NAME_O["M1"];
			$arr_data[] = $LEVEL_DATE["M1"];
			$arr_data[] = $ORG_O["M1"];
            $arr_data[] = $PL_NAME_O["M2"];
			$arr_data[] = $LEVEL_DATE["M2"];
			$arr_data[] = $ORG_O["M2"];
			$arr_data[] = $CURR_EFFECTIVEDATE;
			$arr_data[] = $SALARY_MIDPOINT;
			$arr_data[] = $ORG_NAME_ASS;
			$arr_data[] = $PER_FILE_NO;
                        
            $arr_data[] = $PER_MOBILE;/*Release 5.1.0.4 */
            $arr_data[] = $PER_EMAIL;/*Release 5.1.0.4 */
            $arr_data[] = $PER_OFFNO;/*Release 5.1.0.9 */
                        
			if($REF_CODE){
				$arr_data[] = $REF_CODE;/*Release 5.2.1.7 */
				$arr_data[] = $SS_CODE;/*Release 5.2.1.7 */
			}else{
				$arr_data[] = $SS_CODE;/*Release 5.2.1.7 */
				$arr_data[] = $REF_CODE;/*Release 5.2.1.7 */
			}
			
			$arr_data[] = $SPS_EMPHASIZE_M;/*Release 5.2.1.7 */
			$arr_data[] = $LEVELSKILL_NAME;/*Release 5.2.1.7 */
			$arr_data[] = $SPS_FLAG;/*Release 5.2.1.7 */
			
			if($REF_CODE2){
				$arr_data[] = $REF_CODE2;/*Release 5.2.1.7 */
				$arr_data[] = $SS_CODE2;/*Release 5.2.1.7 */
			}else{
				$arr_data[] = $SS_CODE2;/*Release 5.2.1.7 */
				$arr_data[] = $REF_CODE2;/*Release 5.2.1.7 */
			}
			
			$arr_data[] = $SPS_EMPHASIZE_M2;/*Release 5.2.1.7 */
			$arr_data[] = $LEVELSKILL_NAME2;/*Release 5.2.1.7 */
			$arr_data[] = $SPS_FLAG2;/*Release 5.2.1.7 */
                             
                        
			if ($MFA_FLAG == 1) { 
				$arr_data[] = $POH_MOV_NAME;
				$arr_data[] = $POH_DOCNO;
				$arr_data[] = $POH_POS_NO;
				$arr_data[] = $POH_PL_NAME;
				$arr_data[] = $POH_PM_NAME;
				$arr_data[] = $POH_LEVEL_NAME;
				$arr_data[] = $POH_ORG2;
				$arr_data[] = $POH_ORG3;
				$arr_data[] = $POH_ASS_ORG;
				$arr_data[] = $POH_REMARK;
			}
                        
                        //set_time_limit(0);
                        
                        if(1==1){
                            ob_clean();

                            if($rowscounter>1000){   
                                /*$active_sheet++;
                                $worksheet = &$workbook->addworksheet($report_title."_".$active_sheet);
                                $worksheet->set_margin_right(0.50);
                                $worksheet->set_margin_bottom(1.10);
                                $rowscounter = 1;
                                $xlsRow=1;*/

                                //ob_end_flush ();

                                $workbook->close();
                                unset($workbook);
                               // unset($arr_data);// ปิดเพราะเมื่อออกรายงาน เเล้วเริ่มขึ้นไฟล์ที่ 2 เเล้วคนหายไป อ้างอิงจาก เคสhttp://dpis.ocsc.go.th/Service/node/1900 by KoNg
                               // unset($arr_aggreg_data);

                                if(empty($active_sheet)){$active_sheet=1;}
                                echo "สำเร็จ file DPIS_P0119_PART_".$active_sheet.".xls :". date('Y-m-d H:i').'<br>';
                                flush();
                                ob_flush();
                                sleep(1);


                                $rowscounter = 1;
                                $xlsRow=1;
                                $active_sheet++;
                                //$token = md5(uniqid(rand(), true)); 
                                $fname= "../../Excel/tmp/p0119/dpis_".$token."_".$active_sheet.".xls";
                                $arr_filename[] =$fname;

                                $workbook = new writeexcel_workbook($fname);
                                //====================== SET FORMAT ======================//
                                require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
                                //====================== SET FORMAT ======================//
                                $worksheet = &$workbook->addworksheet("$report_title");
                                $worksheet->set_margin_right(0.50);
                                $worksheet->set_margin_bottom(1.10);
                                //====================== SET FORMAT ======================//
                                require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
                                //====================== SET FORMAT ======================//
                                
                                
                                $colshow_cnt=0;		// หาจำนวน column ที่แสดงจริง
                                for($i=0; $i<count($arr_column_sel); $i++){
                                        if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
                                }

                                $xlsRow = 0;
                                /**$temp_report_title = "$REF_NAME||$NAME||$report_title";
                                $arr_title = explode("||", $temp_report_title);**/
                                $arr_title = explode("||", $report_title);
                                for($i=0; $i<count($arr_title); $i++){
                                        $worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
                                        for($j=0; $j < $colshow_cnt-1; $j++) 
                                                $worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
                                        $xlsRow++;
                                } //for($i=0; $i<count($arr_title); $i++){

                                if($company_name){
                                        $worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
                                        for($j=0; $j < $colshow_cnt-1; $j++) 
                                                $worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
                                        $xlsRow++;
                                } //if($company_name){

                                print_header();

                                // กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล

                                if ($MFA_FLAG==1) {
                                        $wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
                                        $wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
                                        $wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
                                        $wsdata_fontfmt_2 = array("","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");
                                } elseif ($BKK_FLAG==1) {
                                        $wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
                                        $wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
                                        $wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
                                        $wsdata_fontfmt_2 = array("","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");
                                } else {
                                        $wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
			                            $wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			                            $wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			                            $wsdata_fontfmt_2 = array("","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");
                                }
                                $wsdata_align_1 = $data_align;

                            }
                        }
                        
                        
                        
			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));

			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					$colseq++;
				}
			}  
                        $rowscounter++;/*Release 5.2.1.10*/
                        if($rowscounter>1000){
                            $worksheet->write($xlsRow+3,3, "***** วันที่ออกรายงาน : ".date('d').date('/m/').(date('Y')+543)."  *****", set_format("xlsFmtTitle", "B", "L", "", 1));
                        }   
                        //if($debug==1){die('<br>End.....');}
		} // end while
                if($colseq>0){
                    $worksheet->write($xlsRow+3,3, "***** วันที่ออกรายงาน : ".date('d').date('/m/').(date('Y')+543)."  *****", set_format("xlsFmtTitle", "B", "L", "", 1));
                }
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
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 22, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 23, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 24, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 25, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 26, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 27, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 28, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 29, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 30, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 31, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 32, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 33, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 34, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 35, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 36, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 37, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 38, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 39, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 40, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 41, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 42, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 43, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 44, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 45, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 46, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 47, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 48, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 49, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 50, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 51, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 52, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 53, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 54, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 55, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 56, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 57, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 58, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 59, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 60, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 61, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 62, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 63, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 64, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 65, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 66, "", set_format("xlsFmtTitle", "B", "C", "", 1));               
        $worksheet->write($xlsRow, 67, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 68, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 69, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 70, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 71, "", set_format("xlsFmtTitle", "B", "C", "", 1));              
        $worksheet->write($xlsRow, 72, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 73, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 74, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 75, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 76, "", set_format("xlsFmtTitle", "B", "C", "", 1));                
        $worksheet->write($xlsRow, 77, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 78, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 79, "", set_format("xlsFmtTitle", "B", "C", "", 1));           
        $worksheet->write($xlsRow, 80, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 81, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 82, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 83, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 84, "", set_format("xlsFmtTitle", "B", "C", "", 1));         
        $worksheet->write($xlsRow, 85, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 86, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 87, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		if ($BKK_FLAG!=1) $worksheet->write($xlsRow, 63, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		if ($MFA_FLAG == 1) { 
			$worksheet->write($xlsRow, 59, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 60, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 61, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 62, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 63, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 64, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 65, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 66, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 67, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 68, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		}
	} // end if

	$workbook->close();
        
       
	
	/*header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_title.xls\"");
	header("Content-Disposition: inline; filename=\"$report_title.xls\"");
        
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);*/
        
        
       // echo "<a href='../../Excel/tmp/p0119/".$fname."'>คลิกเพื่อ Download</a>";
$cnt = count($arr_filename);
for($idx=0;$idx<=$cnt;$idx++){
    if(!empty($arr_filename[$idx])){
        echo "<a href='".$arr_filename[$idx]."'>".$arr_filename[$idx]." คลิกเพื่อ Download</a><br>";
    }
    //echo $arr_filename[$idx].'<br>';
    //$fh_xls=fopen($arr_filename[$idx], "rb");
    //fpassthru($fh_xls);
    //fclose($fh_xls);
    //unlink($arr_filename[$idx]);
}        
/*        
die();        
class FlxZipArchive extends ZipArchive {
    
    public function addDir($location, $name) {
        $this->addEmptyDir($name);
        $this->addDirDo($location, $name);
     } // EO addDir;

    private function addDirDo($location, $name) {
        $name .= '/';         $location .= '/';
      // Read all Files in Dir
        $dir = opendir ($location);
        while ($file = readdir($dir))    {
            if ($file == '.' || $file == '..') continue;
          // Rekursiv, If dir: FlxZipArchive::addDir(), else ::File();
            $do = (filetype( $location . $file) == 'dir') ? 'addDir' : 'addFile';
            $this->$do($location . $file, $name . $file);
        }
    }

} 
$the_folder="../../Excel/tmp/p0119/";
$zip_file_name="P0119_ZIP.zip";




$za = new FlxZipArchive;
$res = $za->open($zip_file_name, ZipArchive::CREATE);
if($res === TRUE)    {
    $za->addDir($the_folder, basename($the_folder)); $za->close();
}else {echo 'Could not create a zip archive';}
if(file_exists($zip_file_name)){
    // push to download the zip
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
     
    header('Content-type: application/zip; name="'.$zip_file_name.'"');
    header('Content-Disposition: attachment; filename="'.$zip_file_name.'"');
    
    $fh=fopen($zip_file_name, "rb");
    fpassthru($fh);
    fclose($fh);
    
    unlink($zip_file_name);
    
    
    
        $cnt = count($arr_filename);
        for($idx=0;$idx<=$cnt;$idx++){
            //echo $arr_filename[$idx].'<br>';
            $fh_xls=fopen($arr_filename[$idx], "rb");
            fpassthru($fh_xls);
            fclose($fh_xls);
            unlink($arr_filename[$idx]);
        }
    
   
   
    
}
*/        
        
       
?>