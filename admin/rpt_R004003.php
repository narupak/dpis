<?

include("../../php_scripts/connect_database.php");
if (!$FLAG_RTF)
    include("../php_scripts/pdf_wordarray_thaicut.php");
include("../../php_scripts/calendar_data.php");
include ("../php_scripts/function_share.php");

if ($FLAG_RTF) {
    include ("rtf_setvar.php"); // กำหนดตัวแปรค่าสี set_of_colors
    require("../../RTF/rtf_class.php");
} else {
    define('FPDF_FONTPATH', '../../PDF/font/');
    include ("../../PDF/fpdf.php");
    include ("../../PDF/pdf_extends_DPIS.php");
}
ini_set("max_execution_time", $max_execution_time);

$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type==1 || $search_per_type==5){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=f.PL_CODE";
		$b_code = "b.PL_CODE";
		$f_name = "f.PL_NAME";
		$f_position = "POS_NO_NAME||POS_NO";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
		$line_title=" สายงาน";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=f.PN_CODE";
		$b_code = "b.PN_CODE";
		$f_name = "f.PN_NAME";
		$f_position = "POEM_NO_NAME||POEM_NO";
		 $line_search_code=trim($search_pn_code);
		$line_search_name =trim($search_pn_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=f.EP_CODE";
		$b_code = "b.EP_CODE";
		$f_name = "f.EP_NAME";
		$f_position = "POEMS_NO_NAME||POEMS_NO";
		 $line_search_code=trim($search_ep_code);
		 $line_search_name=trim($search_ep_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=f.TP_CODE";
		$b_code = "b.TP_CODE";
		$f_name = "f.TP_NAME";
		$f_position = "POT_NO_NAME||POT_NO";
		$line_search_code =trim($search_tp_code);
		$line_search_name =trim($search_tp_name);
		$line_title=" ชื่อตำแหน่ง";
	} // end if

	
if ($_GET[NUMBER_DISPLAY])
    $NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
$search_per_type = 1;
$position_table = "PER_POSITION";
$position_join = "a.POS_ID=b.POS_ID";
$line_table = "PER_LINE";
$line_join = "b.PL_CODE=f.PL_CODE";
$pl_code = "b.PL_CODE";
$pl_name = "f.PL_NAME";
$position_no = "b.POS_NO_NAME, b.POS_NO";
$line_search_code = trim($search_pl_code);
$line_search_name = trim($search_pl_name);

if (in_array("ALL", $list_type) && !$DEPARTMENT_ID) {
    $f_all = true;
    $RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
} else
    $f_all = false;

if (!trim($RPTORD_LIST)) {
   // $RPTORD_LIST = "ORG|";
    if (in_array("PER_ORG", $list_type) && trim($search_org_id_1) == "" && trim($search_org_id_2) == "")
        $RPTORD_LIST .= "ORG_1|";
    if (in_array("PER_ORG", $list_type) && trim($search_org_id_1) != "" && trim($search_org_id_2) == "")
        $RPTORD_LIST .= "ORG_2|";
    if (in_array("PER_ORG", $list_type) && trim($search_org_id_1) != "" && trim($search_org_id_2) != "")
        $RPTORD_LIST .= "LINE|";
} // end if
$arr_rpt_order = explode("|", $RPTORD_LIST);

//print_r($arr_rpt_order);
$select_list = "";
	$order_by = "";
	$heading_name = "";
        
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
                
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "e.ORG_ID_REF as MINISTRY_ID";	
				
				if($order_by) $order_by .= ", ";
				$order_by .= "e.ORG_ID_REF"; 
				
				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID";

				$heading_name .= " $DEPARTMENT_TITLE";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				else if($select_org_structure==1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				else if($select_org_structure==1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID_1";
				else if($select_org_structure==1)  $select_list .= "a.ORG_ID_1";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_1";
				else if($select_org_structure==1) $order_by .= "a.ORG_ID_1";

				$heading_name .= " $ORG_TITLE1";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID_2";
				else if($select_org_structure==1)  $select_list .= "a.ORG_ID_2";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_2";
				else if($select_org_structure==1) $order_by .= "a.ORG_ID_2";

				$heading_name .= " $ORG_TITLE2";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "$b_code as PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= $b_code;
				
				$heading_name .= $line_title;
				break;
		} // end switch case
	} // end for
$search_level_no = trim($search_level_no);
$search_condition = "";
$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
$arr_search_condition[] = "(a.PER_STATUS in (" . implode(", ", $search_per_status) . "))";
$arr_search_condition[] = "(a.LEVEL_NO = '$search_level_no')";
//$arr_search_condition[] = "(i.DC_TYPE in (1,2))";

$list_type_text = $ALL_REPORT_TITLE;

if (in_array("PER_ORG_TYPE_1", $list_type)) {
    if ($DEPARTMENT_ID) {
        $arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
    } elseif ($MINISTRY_ID) {
        $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
        if ($select_org_structure == 1)
            $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
        $db_dpis->send_cmd($cmd);
        while ($data = $db_dpis->get_array())
            $arr_org_ref[] = $data[ORG_ID];

        $arr_search_condition[] = "(b.DEPARTMENT_ID in (" . implode($arr_org_ref, ",") . "))";
    } // end if

    $list_type_text = "ส่วนกลาง";
    $arr_search_condition[] = "(trim(c.OT_CODE)='01')";
}
if (in_array("PER_ORG_TYPE_2", $list_type)) {
    if ($DEPARTMENT_ID) {
        $arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
    } elseif ($MINISTRY_ID) {
        $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
        if ($select_org_structure == 1)
            $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
        $db_dpis->send_cmd($cmd);
        while ($data = $db_dpis->get_array())
            $arr_org_ref[] = $data[ORG_ID];

        $arr_search_condition[] = "(b.DEPARTMENT_ID in (" . implode($arr_org_ref, ",") . "))";
    } // end if

    $list_type_text = "ส่วนกลางในภูมิภาค";
    $arr_search_condition[] = "(trim(c.OT_CODE)='02')";
}
if (in_array("PER_ORG_TYPE_3", $list_type)) {
    if ($DEPARTMENT_ID) {
        $arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
    } elseif ($MINISTRY_ID) {
        $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
        if ($select_org_structure == 1)
            $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
        $db_dpis->send_cmd($cmd);
        while ($data = $db_dpis->get_array())
            $arr_org_ref[] = $data[ORG_ID];

        $arr_search_condition[] = "(b.DEPARTMENT_ID in (" . implode($arr_org_ref, ",") . "))";
    } // end if

    $list_type_text = "ส่วนภูมิภาค";
    $arr_search_condition[] = "(trim(c.OT_CODE)='03')";
}
if (in_array("PER_ORG_TYPE_4", $list_type)) {
    if ($DEPARTMENT_ID) {
        $arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
    } elseif ($MINISTRY_ID) {
        $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
        if ($select_org_structure == 1)
            $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
        $db_dpis->send_cmd($cmd);
        while ($data = $db_dpis->get_array())
            $arr_org_ref[] = $data[ORG_ID];

        $arr_search_condition[] = "(b.DEPARTMENT_ID in (" . implode($arr_org_ref, ",") . "))";
    } // end if
    // ต่างประเทศ
    $list_type_text = "ต่างประเทศ";
    $arr_search_condition[] = "(trim(c.OT_CODE)='04')";
}
if (in_array("PER_ORG", $list_type)) {
    $list_type_text = "";
    if ($select_org_structure == 0) {
        if (trim($search_org_id)) {
            $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
            $list_type_text .= "$search_org_name";
        } // end if
        if (trim($search_org_id_1)) {
            $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
            $list_type_text .= " - $search_org_name_1";
        } // end if
        if (trim($search_org_id_2)) {
            $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
            $list_type_text .= " - $search_org_name_2";
        } // end if
    } else if ($select_org_structure == 1) {
        if (trim($search_org_ass_id)) {
            $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
            $list_type_text .= "$search_org_ass_name";
        } // end if
        if (trim($search_org_ass_id_1)) {
            $arr_search_condition[] = "(a.ORG_ID_1 = $search_org_ass_id_1)";
            $list_type_text .= " - $search_org_ass_name_1";
        } // end if
        if (trim($search_org_ass_id_2)) {
            $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_ass_id_2)";
            $list_type_text .= " - $search_org_ass_name_2";
        } // end if
    }
}
if (in_array("PER_LINE", $list_type)) {
    // สายงาน
    $list_type_text = "";
    if ($line_search_code) {
        $arr_search_condition[] = "(trim($pl_code)='$line_search_code')";
        $list_type_text .= " $line_search_name";
    }
}
if (in_array("PER_COUNTRY", $list_type)) {
    // ประเทศ , จังหวัด
    $list_type_text = "";
    if (trim($search_ct_code)) {
        $search_ct_code = trim($search_ct_code);
        $arr_search_condition[] = "(trim(c.CT_CODE) = '$search_ct_code')";
        $list_type_text .= "$search_ct_name";
    } // end if
    if (trim($search_pv_code)) {
        $search_pv_code = trim($search_pv_code);
        $arr_search_condition[] = "(trim(c.PV_CODE) = '$search_pv_code')";
        $list_type_text .= " - $search_pv_name";
    } // end if
}
if (in_array("ALL", $list_type) || !isset($list_type)) { //กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
    if ($DEPARTMENT_ID) {
        $arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
        $list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
    } elseif ($MINISTRY_ID) {
        $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
        if ($select_org_structure == 1)
            $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
        $db_dpis->send_cmd($cmd);
        while ($data = $db_dpis->get_array())
            $arr_org_ref[] = $data[ORG_ID];

        $arr_search_condition[] = "(b.DEPARTMENT_ID in (" . implode($arr_org_ref, ",") . "))";
        $list_type_text .= " - $MINISTRY_NAME";
    }elseif ($PROVINCE_CODE) {
        $PROVINCE_CODE = trim($PROVINCE_CODE);
        $arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
        $list_type_text .= " - $PROVINCE_NAME";
    } // end if
} // end if
if (count($arr_search_condition))
    $search_condition = " where " . implode(" and ", $arr_search_condition);

$cmd2 = "select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$search_level_no' ";
$db_dpis2->send_cmd($cmd2);
//$db_dpis2->show_error();
$data = $db_dpis2->get_array();
$level_name = $data[LEVEL_NAME];

$company_name = "รูปแบบการออกรายงาน : " . ($select_org_structure == 1 ? "โครงสร้างตามมอบหมายงาน - " : "โครงสร้างตามกฎหมาย - ") . "$list_type_text";
$report_title = "$DEPARTMENT_NAME||รายชื่อ$PERSON_TYPE[$search_per_type]ที่ดำรงตำแหน่งในระดับ " . $level_name . " เรียงตามลำดับอาวุโส";
$report_code = "R0403";
include ("rpt_R004003_format_PDF.php"); // กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
//	session_cache_limiter("nocache");
session_cache_limiter("private");
session_start();

if ($FLAG_RTF) {
//		$sum_w = array_sum($heading_width);
    $sum_w = 0;
    for ($h = 0; $h < count($heading_width); $h++) {
        if ($arr_column_sel[$h] == 1)
            $sum_w += $heading_width[$h];
    }
    for ($h = 0; $h < count($heading_width); $h++) {
        if ($arr_column_sel[$h] == 1)
            $heading_width[$h] = $heading_width[$h] / $sum_w * 100;
    }

    $fname = "rpt_R004003_rtf.rtf";

    //	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
    $paper_size = "a4";
    $orientation = 'L';
    $RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

    $RTF->set_default_font($font, 14);
    //	echo "default font_id::".$RTF->dfl_FontID."<br>";	

    $RTF->set_report_code($report_code);
    $RTF->set_report_title($report_title);
    $RTF->set_company_name($company_name);
} else {
    $unit = "mm";
    $paper_size = "A3";
    $lang_code = "TH";
    $orientation = 'L';

    $pdf = new PDF($orientation, $unit, $paper_size, $lang_code, $company_name, $report_title, $report_code, $heading, $heading_width, $heading_align);

    $pdf->Open();
    $pdf->SetMargins(5, 5, 5);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont($font, '', 14);

    $page_start_x = $pdf->x;
    $page_start_y = $pdf->y;
}


//เป็นค่าจากช่อง เรียงตามข้อมูลหน้า html
$or_der_list ="";
$comma = ", ";
for($i=0; $i<count($RPTORD_ORDER); $i++){
	$or_der_list = $RPTORD_ORDER[$i];
	

	switch($or_der_list){
		case "cur_affective" :
			$oder_by_lis .= "CUR_EFFECTIVEDATE ASC";
			break;
			
		case "poh_affective":
			$oder_by_lis .= $comma."POH_EFFECTIVEDATE ASC";
			break;
			
		case "salary" :
			$oder_by_lis .= $comma."per_salary DESC " ;
			break;
		
		case "start_date" :
			$oder_by_lis .= $comma."PER_STARTDATE ASC" ;
			break;
			
		case "dc_or" :
			$oder_by_lis .= $comma."DC_ORDER  ASC , DEH_DATE ASC" ;
			break;
			
		case "birth_date" :
			$oder_by_lis .= $comma."PER_BIRTHDATE ASC" ;
			break;
	
				
	}
}


function list_person($search_condition, $addition_condition){
       
        global $DPISDB, $db_dpis, $db_dpis2, $db_dpis3,$db_dpis4, $position_table, $position_join, $line_table, $line_join, $f_name, $f_position;
        global $arr_rpt_order, $rpt_order_index, $search_per_type, $arr_content, $data_count, $person_count, $search_budget_year,$DATE_DISPLAY, $DEPARTMENT_ID, $oder_by_lis;
        global $days_per_year, $days_per_month, $seconds_per_day,$select_org_structure;
        global $have_pic,$img_file,$search_level_no,$position_no,$pl_name, $count_data;

        if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;

        //---ต้องมีระดับตำแหน่งของตัวมันเองด้วย ในกรณีเข้ามาถึงก็เป็นระดับตำแหน่งนี้เลย ไม่มีระดับก่อนหน้า
        $L["O1"] = array('04', '03', '02', '01', 'O1');
        $L["O2"] = array('06', '05', '04', 'O1', 'O2');
        $L["O3"] = array('08', '07', '06', 'O2', 'O3');
        $L["O4"] = array('09', '08', 'O3', 'O4');
        $L["K1"] = array('05', '04', '03', 'K1');
        $L["K2"] = array('07', '06', '05', 'K1', 'K2');
        $L["K3"] = array('08', '07', 'K2', 'K3');
        $L["K4"] = array('09', '08', 'K3', 'K4');
        $L["K5"] = array('10', '09', 'K4', 'K5');
        $L["D1"] = array('08', '07', 'D1');
        $L["D2"] = array('09', '08', 'D1', 'D2');
        $L["M1"] = array('09', '08', 'M1');
        $L["M2"] = array('11', '10', '09', 'M1', 'M2');
        //----วน loop ตามระดับตำแหน่ง ที่เลือกมา -------------
	
       
       // for ($i = 0; $i < count($L[$search_level_no]); $i++) {
            
            //$index = $L[$search_level_no][$i];  //$index=level no

		   $LEVEL_NO_1 =$search_level_no;
		   $group_level = "";
		   $group_level_1 = "";
		   $R = ",";
		   $or = "or";
	   
		for ($i = 0; $i < count($L[$search_level_no]); $i++) {
			//$index = $L[$search_level_no][$i]; 
			if($i == count($L[$search_level_no]) -1){
				$R ="";
				$or = "";
			}
			$group_level .= "'".$L[$search_level_no][$i]."'".$R;
			$group_level_1 .= "e.POH_LEVEL_NO='".$L[$search_level_no][$i]."' $or ";	
				
		} 
			 $w_group_level_1 .= "(".$group_level_1.")";
			//echo $group_level."[  ]". $w_group_level_1."[  ]".$LEVEL_NO_1;
		
			 $arrkeep = array();
			 
            if ($DPISDB == "odbc") {
                $cmd = " select		a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no, $pl_name, a.LEVEL_NO, j.LEVEL_NAME, j.POSITION_LEVEL, 
                                                                                                        MAX(e.POH_LEVEL_NO) as LEVEL_NOHIS, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, 
                                                                                                        LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,	MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, 
                                                                                                        MIN(i.DC_ORDER) as DC_ORDER, a.DEPARTMENT_ID
                                                   from		(
                                                                                        (
                                                                                                (
                                                                                                        (
                                                                                                                (
                                                                                                                        (
                                                                                                                                (
                                                                                                                                        (
                                                                                                                                        PER_PERSONAL a 
                                                                                                                                        left join $position_table b on ($position_join) 
                                                                                                                                ) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
                                                                                                                        ) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
                                                                                                                ) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
                                                                                                        ) left join $line_table f on ($line_join)
                                                                                                ) left join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
                                                                                        ) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
                                                                                ) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
                                                                        ) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
                                                                        $search_condition and e.POH_LEVEL_NO='$index' and (e.POH_LEVEL_NO < a.LEVEL_NO)
                                                        group by e.PER_ID,e.POH_LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no, $pl_name, a.LEVEL_NO,
                                                                         j.LEVEL_NAME, j.POSITION_LEVEL, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), 
                                                                        LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE, a.DEPARTMENT_ID
                                                        order by  MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)), a.PER_SALARY desc, MIN(i.DC_ORDER),
                                                                        LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10) ";
            } elseif ($DPISDB == "oci8") {
               // $search_condition = str_replace(" where ", " and ", $search_condition);
                $cmd = " SELECT * FROM (
															SELECT DISTINCT a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO_NAME, b.POS_NO, 
																f.PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, j.POSITION_LEVEL, e.POH_LEVEL_NO as LEVEL_NOHIS, b.PT_CODE, a.PER_SALARY, g.PT_NAME, c.ORG_NAME,a.PER_RETIREDATE,
																SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, 
																SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, 
																SUBSTR(trim(e.POH_EFFECTIVEDATE_LEVEL), 1, 10) as CUR_EFFECTIVEDATE,
																SUBSTR(trim(eff.POH_EFFECTIVEDATE), 1, 10) as POH_EFFECTIVEDATE,
																(SELECT min(deco.dc_order) AS dc_order 
															FROM PER_DECORATEHIS dhis 
															LEFT JOIN PER_DECORATION deco ON(dhis.dc_code=deco.dc_code)
															WHERE deco.DC_TYPE in (1,2) AND  dhis.per_id=a.PER_ID ) AS DC_ORDER, 
															(SELECT min(dhis.DEH_DATE) AS dc_order 
															FROM PER_DECORATEHIS dhis 
															LEFT JOIN PER_DECORATION deco ON(dhis.dc_code=deco.dc_code)
															WHERE deco.DC_TYPE in (1,2) AND  dhis.per_id=a.PER_ID ) AS DEH_DATE,  a.DEPARTMENT_ID	
															from 	PER_PERSONAL a 
															LEFT JOIN PER_POSITION b ON(a.POS_ID=b.POS_ID)
															LEFT JOIN PER_ORG c ON(b.ORG_ID=c.ORG_ID)
															LEFT JOIN PER_PRENAME d ON(a.PN_CODE=d.PN_CODE)
															LEFT JOIN (select * from ( 
																			 select his.POH_EFFECTIVEDATE as POH_EFFECTIVEDATE_LEVEL,his.PER_ID,his.POH_LEVEL_NO , 
																			   row_number() over (partition by his.PER_ID order by his.POH_EFFECTIVEDATE) rn 
																			 from PER_POSITIONHIS his, PER_MOVMENT mov 
																			 where his.MOV_CODE=mov.MOV_CODE and his.POH_LEVEL_NO IN('$LEVEL_NO_1') 
																			 order by his.PER_ID,his.POH_EFFECTIVEDATE 
																		  ) where rn=1 
																  ) e ON(a.PER_ID=e.PER_ID)
															LEFT JOIN (select * from ( 
																			 select his.POH_EFFECTIVEDATE as POH_EFFECTIVEDATE,his.PER_ID,his.POH_LEVEL_NO , 
																			   row_number() over (partition by his.PER_ID order by his.POH_EFFECTIVEDATE) rn 
																			 from PER_POSITIONHIS his, PER_MOVMENT mov 
																			 where his.MOV_CODE=mov.MOV_CODE and his.POH_LEVEL_NO IN($group_level) 
																			 order by his.PER_ID,his.POH_EFFECTIVEDATE 
																		  ) where rn=1 
																  ) eff ON(a.PER_ID=eff.PER_ID)
															LEFT JOIN PER_LINE f ON(b.PL_CODE=f.PL_CODE)
															LEFT JOIN PER_TYPE G ON(b.PT_CODE=g.PT_CODE)
															LEFT JOIN PER_LEVEL j ON(a.LEVEL_NO=j.LEVEL_NO)
															where $w_group_level_1
															  $search_condition
															  
															)  tbmain
															ORDER BY CUR_EFFECTIVEDATE ASC $oder_by_lis ";
															 
				
                //**การเปลี่ยน group by/order by มีผลต่อการแสดงผลที่จะไม่ดึงวันที่เริ่มต้นเข้าสู่ระดับ ของระดับตำแหน่งก่อนหน้าจะเข้าสู่ระดับปัจจุบัน
            } elseif ($DPISDB == "mysql") {
                $cmd = " select		a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no, $pl_name, a.LEVEL_NO, j.LEVEL_NAME, j.POSITION_LEVEL, 
                                                                                                        MAX(e.POH_LEVEL_NO) as LEVEL_NOHIS, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, 
                                                                                                        LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,	MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, 
                                                                                                        MIN(i.DC_ORDER) as DC_ORDER, a.DEPARTMENT_ID
                                                   from		(
                                                                                        (
                                                                                                (
                                                                                                        (
                                                                                                                (
                                                                                                                        (
                                                                                                                                (
                                                                                                                                        (
                                                                                                                                        PER_PERSONAL a 
                                                                                                                                        left join $position_table b on ($position_join) 
                                                                                                                                ) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
                                                                                                                        ) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
                                                                                                                ) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
                                                                                                        ) left join $line_table f on ($line_join)
                                                                                                ) left join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
                                                                                        ) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
                                                                                ) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
                                                                        ) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
                                                                        $search_condition and e.POH_LEVEL_NO='$index' and (e.POH_LEVEL_NO < a.LEVEL_NO)
                                                        group by e.PER_ID,e.POH_LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no, $pl_name, a.LEVEL_NO,
                                                                         j.LEVEL_NAME, j.POSITION_LEVEL, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), 
                                                                        LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE, a.DEPARTMENT_ID
                                                        order by  MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)), a.PER_SALARY desc, MIN(i.DC_ORDER),
                                                                        LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10) ";
            }

            //สร้าง query ใหม่ สำหรับข้อมูล record เดียว
           
            if ($select_org_structure == 1) {
                $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
                $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
            }
      // echo "<pre>$cmd <-----> <br>===============<br>";
            //---สร้าง query ของแต่ละตัว แล้ววนข้อมูลมาอีกครั้ง
            $count_data = $db_dpis->send_cmd($cmd);
           //echo "<pre>".$cmd;
        // $db_dpis->show_error(); echo"<hr>";
            while ($data = $db_dpis->get_array()) {
                if ($PER_ID == $data[PER_ID])
                    continue;
                    
                $person_count++;		

                $PER_ID = $data[PER_ID];
                //---เก็บ PER_ID ของคนนั้นลงใน array ถ้ามีชื่อในระดับตำแหน่งที่เรียงตามลำดับแรกมาแล้ว อันถัดไปไม่นำมาเก็บอีกต่อไปแล้ว
                if (!in_array($PER_ID, $arrkeep)) {
                    $arrkeep[] = $PER_ID;
                    //=================================
                    if (in_array($PER_ID, $ARRALL_PERID_LEVELNO)) {
                        $ARRALL_HAVE_PERID_LEVELNO[] = $PER_ID;
                    }

                   // $ARRSORTLEVEL['per_id'][] = $PER_ID;
				   //$PER_ID 		= $data[PER_ID];
				   $ORG_ID_1 	= $data[ORG_ID_1];
				   $PN_NAME 	= $data[PN_NAME]; 
				   $PER_NAME 	= $data[PER_NAME]; 
				   $PER_SURNAME = $data[PER_SURNAME]; 
				   $POS_NO_NAME = $data[POS_NO_NAME]; 
				   $POS_NO      = $data[POS_NO]; 
				   $PL_NAME		= trim($data[PL_NAME]); 
				   $LEVEL_NO	= $data[LEVEL_NO]; 
				   $LEVEL_NAME	= $data[LEVEL_NAME]; 
				   $POSITION_LEVEL = $data[POSITION_LEVEL]; 
				   $LEVEL_NOHIS	= $data[LEVEL_NOHIS]; 
				   $PT_CODE		= $data[PT_CODE]; 
				   $PER_SALARY	= $data[PER_SALARY]; 
				   $PT_NAME		= $data[PT_NAME]; 
				   $ORG_NAME	= $data[ORG_NAME];
				   $PER_RETIREDATE	= $data[PER_RETIREDATE];
				   $PER_STARTDATE 	= $data[PER_STARTDATE];
				   $PER_BIRTHDATE	= $data[PER_BIRTHDATE]; 
				   $CUR_EFFECTIVEDATE	= $data[CUR_EFFECTIVEDATE];
                                   if($CUR_EFFECTIVEDATE){
                                      $cmd = " select POH_EFFECTIVEDATE
                                from PER_POSITIONHIS a, PER_MOVMENT b
                                where PER_ID=$PER_ID 
                                   and a.MOV_CODE=b.MOV_CODE 
                                    and POH_EFFECTIVEDATE < '$CUR_EFFECTIVEDATE' 
                                     order by POH_EFFECTIVEDATE DESC ";
                                      
                                       $db_dpis2->send_cmd($cmd);
                                       //echo"<pre>".$cmd;
                                        $data2 = $db_dpis2->get_array();
                                        
                                           echo $POH_EFFECTIVEDATEss = show_date_format($data2[POH_EFFECTIVEDATE], $DATE_DISPLAY);
                                          
                                        
                                   }
				   $POH_EFFECTIVEDATE	= $data[POH_EFFECTIVEDATE];
				   $DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
				   $dc_order = $data[dc_order];
				   $PER_WORK = date_difference(date("Y-m-d"), trim($PER_STARTDATE), "full");
				   $PER_STARTDATE2 = show_date_format($PER_STARTDATE, $DATE_DISPLAY);
				   $PER_BIRTHDATE2 = show_date_format($PER_BIRTHDATE, $DATE_DISPLAY);
				   $PER_AGE = date_difference(date("Y-m-d"), trim($PER_BIRTHDATE), "full");
				   $PER_RETIREDATE2 = show_date_format($PER_RETIREDATE, $DATE_DISPLAY);
                    //$PN_NAME 	 ="  ".trim($data[PN_NAME]);
                   // $PER_NAME    = trim($data[PER_NAME]);
                    //$PER_SURNAME = trim($data[PER_SURNAME]);
                   // $POS_NO		 = trim($data[POS_NO_NAME]) . ' ' . trim($data[POS_NO]);
                   // $PL_NAME 	 = trim($data[PL_NAME]);
                   // $LEVEL_NO	 = trim($data[LEVEL_NO]);
                   // $LEVEL_NOHIS = trim($data[LEVEL_NOHIS]);
                   // $ORG_ID_P	 = $data[ORG_ID];
					
					
					
					
                    //$ARRSORTLEVEL['per_sortlevel'][] = $LEVEL_NOHIS[$PER_ID];  //--------

                   // $LEVEL_NAME[$PER_ID] = trim($data[LEVEL_NAME]);
                   // $POSITION_LEVEL[$PER_ID] = trim($data[POSITION_LEVEL]);
                   // $PT_CODE[$PER_ID] = trim($data[PT_CODE]);
                   // $PT_NAME[$PER_ID] = trim($data[PT_NAME]);
                   // $ORG_NAME[$PER_ID] = trim($data[ORG_NAME]);
				   /*
                    if ($ORG_NAME[$PER_ID] == "-")
                        $ORG_NAME[$PER_ID] = "";
                    $DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
                    $ORG_ID_1 = trim($data[ORG_ID_1]);
                    $PER_RETIREDATE = trim($data[PER_RETIREDATE]);
					
					$POH_EFFECTIVEDATE_LEVEL = substr(trim($data[POH_EFFECTIVEDATE_LEVEL]), 0, 10);
					$ARRSORTLEVEL['poh_effectivedate_level'][] = $POH_EFFECTIVEDATE_LEVEL; 
					//echo $PER_ID.'='.$POH_EFFECTIVEDATE_LEVEL.'<pre>';
                   // $POH_EFFECTIVEDATE = substr(trim($data[POH_EFFECTIVEDATE]), 0, 10);
                   // if (trim($POH_EFFECTIVEDATE)) {
                        //เก็บวันเริ่มต้นเข้าสู่ระดับ ที่เป็นระดับตำแหน่งก่อนเลื่อนระดับล่าสุด เพื่อใช้จัดเรียงข้อมูลระดับตำแหน่ง/วันที่ (e.POH_LEVEL_NO และ e.POH_EFFECTIVEDATE)
                       // $PER_EFFECTIVEDATE[$LEVEL_NOHIS][$PER_ID] = $POH_EFFECTIVEDATE;  //$PER_EFFECTIVEDATE[$index][$PER_ID] =$POH_EFFECTIVEDATE;
                        $POH_EFFECTIVEDATE2[$PER_ID] = show_date_format($POH_EFFECTIVEDATE, $DATE_DISPLAY);
                    } // end if	
					*/
                   // $ARRSORTLEVEL['poh_effectivedate'][] = $POH_EFFECTIVEDATE;  //--------

                    //$PER_SALARY[$PER_ID] = trim($data[PER_SALARY]);

                  //  $ARRSORTLEVEL['per_salary'][] = $PER_SALARY[$PER_ID]; //--------

                   // $PER_BIRTHDATE = substr(trim($data[PER_BIRTHDATE]), 0, 10);
                   // if (trim($PER_BIRTHDATE)) {
                        //$PER_AGE[$PER_ID] = round(date_difference(date("Y-m-d"), trim($PER_BIRTHDATE), "year"));  //floor /*เดิม*/
                       // $PER_AGE[$PER_ID] = date_difference(date("Y-m-d"), trim($PER_BIRTHDATE), "full");
                      //  $PER_BIRTHDATE2[$PER_ID] = show_date_format($PER_BIRTHDATE, $DATE_DISPLAY);
                  //  } // end if	

                   // $ARRSORTLEVEL['per_birthdate'][] = $PER_BIRTHDATE; //--------

                    //$PER_STARTDATE = substr(trim($data[PER_STARTDATE]), 0, 10);
                   // if (trim($PER_STARTDATE)) {
                        //$PER_WORK[$PER_ID] = round(date_difference(date("Y-m-d"), trim($PER_STARTDATE), "year"));  //floor/*เดิม*/
                   //     $PER_WORK[$PER_ID] = date_difference(date("Y-m-d"), trim($PER_STARTDATE), "full");  //floor
                    //    $PER_STARTDATE2[$PER_ID] = show_date_format($PER_STARTDATE, $DATE_DISPLAY);
                   // } // end if	

                   // $ARRSORTLEVEL['per_startdate'][] = $PER_STARTDATE; //--------

                   // if (trim($PER_RETIREDATE)) {
                   //     $PER_RETIREDATE2[$PER_ID] = show_date_format($PER_RETIREDATE, $DATE_DISPLAY);
                   // } // end if	

                    if ($DPISDB == "odbc") {
                        $cmd = " select		a.EN_CODE, b.EN_SHORTNAME, c.EM_NAME
                                                                   from		
                                                                                                ( PER_EDUCATE a 
                                                                                        left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
                                                                                                ) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
                                                                   where	a.PER_ID=$PER_ID and (a.EDU_TYPE like '%2%' or a.EDU_TYPE like '%4%')
                                                                   order by 	a.EDU_SEQ ";
                    } elseif ($DPISDB == "oci8") {
                        $cmd = " select		a.EN_CODE, b.EN_SHORTNAME, c.EM_NAME
                                                                   from		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c
                                                                   where	a.PER_ID=$PER_ID and a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and (a.EDU_TYPE like '%2%' or a.EDU_TYPE like '%4%')
                                                                   order by 	a.EDU_SEQ ";
                    } elseif ($DPISDB == "mysql") {
                        $cmd = " select		a.EN_CODE, b.EN_SHORTNAME, c.EM_NAME
                                                                   from		
                                                                                        (  PER_EDUCATE a
                                                                                        left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
                                                                                        )	left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
                                                                   where	a.PER_ID=$PER_ID and (a.EDU_TYPE like '%2%' or a.EDU_TYPE like '%4%')
                                                                   order by 	a.EDU_SEQ ";
                    } // end if
                    //echo $cmd."<br><br><br>";
                    $db_dpis2->send_cmd($cmd);
                    //$db_dpis2->show_error();
                    $PER_EDUCATE[$PER_ID] = "";
                    while ($data2 = $db_dpis2->get_array()) {
                        if ($PER_EDUCATE[$PER_ID])
                            $PER_EDUCATE[$PER_ID] .= ", ";
                        $PER_EDUCATE[$PER_ID] .= trim($data2[EN_SHORTNAME]);
                        if ($EM_NAME[$PER_ID])
                            $EM_NAME[$PER_ID] .= ", ";
                        $EM_NAME[$PER_ID] .= trim($data2[EM_NAME]);
                    } // end while

                    if ($DPISDB == "odbc") {
                        $cmd = " select		a.DC_CODE, b.DC_SHORTNAME, DEH_DATE
                                                                   from		PER_DECORATEHIS a
                                                                                        left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
                                                                   where	a.PER_ID=$PER_ID and DC_TYPE in (1,2)
                                                                   order by 	LEFT(trim(a.DEH_DATE), 10) desc ";
                    } elseif ($DPISDB == "oci8") {
                        $cmd = " select		a.DC_CODE, b.DC_SHORTNAME, DEH_DATE
                                                                   from		PER_DECORATEHIS a, PER_DECORATION b
                                                                   where	a.PER_ID=$PER_ID and DC_TYPE in (1,2) and a.DC_CODE=b.DC_CODE(+)
                                                                   order by 	SUBSTR(trim(a.DEH_DATE), 1, 10) desc ";
                    } elseif ($DPISDB == "mysql") {
                        $cmd = " select		a.DC_CODE, b.DC_SHORTNAME, DEH_DATE
                                                                   from		PER_DECORATEHIS a
                                                                                        left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
                                                                   where	a.PER_ID=$PER_ID and DC_TYPE in (1,2)
                                                                   order by 	LEFT(trim(a.DEH_DATE), 10) desc ";
                    } // end if
                    $db_dpis2->send_cmd($cmd);
                    //	$db_dpis2->show_error();
                    $data2 = $db_dpis2->get_array();
                    $PER_DECORATE[$PER_ID] = trim($data2[DC_SHORTNAME]);
                    $DEH_DATE[$PER_ID] = substr($data2[DEH_DATE], 0, 4) + 543;

                    $ARRSORTLEVEL['per_decorate'][] = $PER_DECORATE[$PER_ID]; //--------
                    $DEPT_NAME[$PER_ID] = "";
                    if ($DEPARTMENT_ID) {
                        $cmd2 = "select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID";
                        $db_dpis3->send_cmd($cmd2);
                        $data_org = $db_dpis3->get_array();
                        $DEPT_NAME[$PER_ID] = trim($data_org[ORG_NAME]);
                    }
                    $ORG_ID_1_NAME[$PER_ID] = "";
                    if ($ORG_ID_1) {
                        $cmd2 = "select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1";
                        if ($select_org_structure == 1)
                            $cmd2 = str_replace("PER_ORG", "PER_ORG_ASS", $cmd2);
                        $db_dpis3->send_cmd($cmd2);
                        $data_org = $db_dpis3->get_array();
                        $ORG_ID_1_NAME[$PER_ID] = trim($data_org[ORG_NAME]);
                        if ($ORG_ID_1_NAME[$PER_ID] == "-")
                            $ORG_ID_1_NAME[$PER_ID] = "";
                    }

                    /*$cmd = " select	 POH_EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and LEVEL_NO='$LEVEL_NO[$PER_ID]' order by POH_EFFECTIVEDATE";
                    $count_data = $db_dpis2->send_cmd($cmd);
                    //	$db_dpis2->show_error();

                    if (!$count_data) {
                        $cmd = " select	 POH_EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and POH_LEVEL_NO='$LEVEL_NO[$PER_ID]' order by POH_EFFECTIVEDATE";
                        $db_dpis2->send_cmd($cmd);
                    }*//*เดิม*/
                     /*Release 5.2.1.6 Begin*/
                   /* $cmd = " select POH_EFFECTIVEDATE
                                from PER_POSITIONHIS a, PER_MOVMENT b
                                where PER_ID=$PER_ID 
                                    and a.MOV_CODE=b.MOV_CODE 
                                    and POH_LEVEL_NO='$LEVEL_NO[$PER_ID]' 
                               order by POH_EFFECTIVEDATE ";

                    $db_dpis2->send_cmd($cmd);
                    /*Release 5.2.1.6 end*/

                    //เอาตน.ที่วันที่ว่าง ไว้ท้ายสุด
                    // เริ่มต้น ให้หาวันที่น้อยที่สุดก่อน แล้วมาหา query ถัดไปของคนนั้น
                   /* $data2 = $db_dpis2->get_array();*/
						
                    //$CURR_EFFECTIVEDATE_SORT[$LEVEL_NOHIS][$PER_ID] = substr(trim($data[POH_EFFECTIVEDATE_LEVEL]), 0, 10);

                    //$ARRSORTLEVEL['curr_effectivedate'][PER_ID] = substr(trim($data[POH_EFFECTIVEDATE_LEVEL]), 0, 10); //--------

                   // $CURR_EFFECTIVEDATE[$PER_ID] = show_date_format(trim($POH_EFFECTIVEDATE_LEVEL), $DATE_DISPLAY);
					
        				//echo "<pre>==>  $LEVEL_NOHIS / $PER_ID = ".substr(trim($data2[POH_EFFECTIVEDATE_LEVEL]), 0, 10);
						
                $data_count++;
                    $arr_content[$data_count][type] = "CONTENT";
                    if ($have_pic && $img_file) {
                        if ($FLAG_RTF)
                            $arr_content[$data_count][image] = "<*img*" . $img_file . ",15*img*>";
                        else
                            $arr_content[$data_count][image] = "<*img*" . $img_file . ",4*img*>";
                    }
                    $arr_content[$data_count][name] = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;
                    $arr_content[$data_count][position] = $PL_NAME;
                    $arr_content[$data_count][level] = $POSITION_LEVEL;
                    if ($BKK_FLAG == 1)
                        $arr_content[$data_count][org] = trim($ORG_NAME );
                    else
                        $arr_content[$data_count][org] = trim( $ORG_NAME);
                    $arr_content[$data_count][posno] = $POS_NO;
                    $arr_content[$data_count][educate] = $PER_EDUCATE[$PER_ID];
                    $arr_content[$data_count][curr_effectivedate] = show_date_format(trim($CUR_EFFECTIVEDATE), $DATE_DISPLAY);
					$arr_content[$data_count][poh_effectivedate] = show_date_format(trim($POH_EFFECTIVEDATE), $DATE_DISPLAY);
                    
                    //หาระดับตำแหน่งนั้น--------------------
                     $cmd2 = "select LEVEL_NAME from PER_LEVEL where LEVEL_NO='" . $LEVEL_NOHIS . "' ";
                    $db_dpis3->send_cmd($cmd2);
                    $levelname = $db_dpis3->get_array();
                    //---------------------------------------------
                    $arr_content[$data_count][per_sortlevel] = $levelname[LEVEL_NAME];    //___$LEVEL_NO?level_no_format($LEVEL_NO):"";
                    $arr_content[$data_count][per_salary] = $PER_SALARY ? number_format($PER_SALARY) : "";
                    $arr_content[$data_count][decorate] = $PER_DECORATE[$PER_ID] . "*Enter* (" . $DEH_DATE[$PER_ID] . ")";
                    /*$arr_content[$data_count][per_startdate] = $PER_STARTDATE2[$PER_ID] . "*Enter* (" . $PER_WORK[$PER_ID] . " ปี)";
                    $arr_content[$data_count][per_birthdate] = $PER_BIRTHDATE2[$PER_ID] . "*Enter* (" . $PER_AGE[$PER_ID] . " ปี)";*//*เดิม*/
                    $arr_content[$data_count][per_startdate] = $PER_STARTDATE2 . "*Enter* (" . $PER_WORK . " )";
                    $arr_content[$data_count][per_birthdate] = $PER_BIRTHDATE2 . "*Enter* (" . $PER_AGE . " )";
                    $arr_content[$data_count][per_retiredate] = $PER_RETIREDATE2;
                    $arr_content[$data_count][em_name] = $EM_NAME[$PER_ID];
                }
                    
                } // end while
            //} //end for
            
            
} //end function
	//print("<pre>");	print_r($arr_content);	print("</pre>");
function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type,$select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(e.ORG_ID_REF = $MINISTRY_ID)";
				break;
				case "DEPARTMENT" :
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
				break;
				case "ORG" :	
					if($ORG_ID && $ORG_ID!=-1){
						if($select_org_structure==0) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else if($select_org_structure==1) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
					}
				break;
				case "ORG_1" :
					if($ORG_ID_1 && $ORG_ID_1!=-1){
						if($select_org_structure==0) $arr_addition_condition[] = "(b.ORG_ID_1 = $ORG_ID_1)";
						else if($select_org_structure==1) $arr_addition_condition[] = "(a.ORG_ID_1 = $ORG_ID_1)";
					}
				break;
				case "ORG_2" :
					if($ORG_ID_2 && $ORG_ID_2!=-1){
						if($select_org_structure==0) $arr_addition_condition[] = "(b.ORG_ID_2 = $ORG_ID_2)";
						else if($select_org_structure==1) $arr_addition_condition[] = "(a.ORG_ID_2 = $ORG_ID_2)";
					}
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
      
    function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					$MINISTRY_ID = -1;
				break;
				case "DEPARTMENT" :
					$DEPARTMENT_ID = -1;
				break;
				case "ORG" :	
					$ORG_ID = -1;
				break;
				case "ORG_1" :
					$ORG_ID_1 = -1;
				break;
				case "ORG_2" :
					$ORG_ID_2 = -1;
				break;
			} // end switch case
		} // end for
	} // function    
        
        	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list
						 from		(
											(
												PER_PERSONAL a 
												left join $position_table b on ($position_join) 
											) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
										) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_ORG e
						 where			$position_join and b.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=b.DEPARTMENT_ID 
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from		(
											(
												PER_PERSONAL a 
												left join $position_table b on ($position_join) 
											) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
										) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
											$search_condition
						 order by		$order_by ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
        if($REPORT_ORDER){	
	$count_data = $db_dpis4->send_cmd($cmd);
	//echo "<pre>$cmd<br>";
	//die();
//$db_dpis->show_error();
	$data_count = 0;
	$person_count = 0;
	initialize_parameter(0);        
	
        while($data = $db_dpis4->get_array()){
            //echo count($arr_rpt_order)."<br>";

            for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
                        //print_r($arr_rpt_order);
                      
                                        switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID != $data[MINISTRY_ID]){
						$MINISTRY_ID = $data[MINISTRY_ID];
						if($MINISTRY_ID == ""){
							$MINISTRY_NAME = "[ไม่ระบุ$MINISTRY_TITLE]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "MINISTRY";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
                                                
                                                
						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_person($search_condition, $addition_condition);
						$data_count++;
					} // end if
				break;
				case "DEPARTMENT" :
					if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){
						$DEPARTMENT_ID = $data[DEPARTMENT_ID];
						if($DEPARTMENT_ID == ""){
							$DEPARTMENT_NAME = "[ไม่ระบุ$DEPARTMENT_TITLE]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
							$db_dpis2->send_cmd($cmd);
							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "DEPARTMENT";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $DEPARTMENT_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_person($search_condition, $addition_condition);
						$data_count++;
					} // end if
				break;

				case "ORG" : 
					if($ORG_ID != $data[ORG_ID]){
						$ORG_ID = $data[ORG_ID];
                                                  // die( $ORG_ID);
						if($ORG_ID == ""){
							$ORG_NAME = "[ไม่ระบุ$ORG_TITLE]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
						

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_person($search_condition, $addition_condition);
						$data_count++;
					} // end if
				break;
		
				case "ORG_1" :
					if($ORG_ID_1 != $data[ORG_ID_1]){
						$ORG_ID_1 = $data[ORG_ID_1];
						if($ORG_ID_1 == ""){
							$ORG_NAME_1 = "[ไม่ระบุ$ORG_TITLE1]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_1 = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_1";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_1;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_person($search_condition, $addition_condition);
						$data_count++;
					} // end if
				break;
		
				case "ORG_2" :
					if($ORG_ID_2 != $data[ORG_ID_2]){
						$ORG_ID_2 = $data[ORG_ID_2];
						if($ORG_ID_2 == ""){
							$ORG_NAME_2 = "[ไม่ระบุ$ORG_TITLE2]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_2 = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_2";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_2;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_person($search_condition, $addition_condition);
						$data_count++;
					} // end if
				break;		

			} // end switch case
                        
                        
                        
			
		} // end for
                
              
	} // end while
          }else{
                  
         
                                $addition_condition = generate_condition(1);
								//echo 'addition_condition==>'.$addition_condition.'<br>';
                                list_person($search_condition, $addition_condition);  
                       //   echo "<pre>"; print_r($arr_content); echo "</pre>";
                 }//if
	
	
        
        
//	$SORTBY=array('poh_effectivedate'=>'asc','per_salary'=>'desc','per_startdate'=>'asc','per_birthdate'=>'asc','per_decorate'=>'desc');

if ($MFA_FLAG == 1) {
    array_multisort(
	$ARRSORTLEVEL['curr_effectivedate'], SORT_ASC, SORT_STRING, 
	$ARRSORTLEVEL['per_sortlevel'], SORT_NUMERIC, SORT_DESC, 
	$ARRSORTLEVEL['poh_effectivedate'], SORT_ASC, SORT_STRING, 
	$ARRSORTLEVEL['per_salary'], SORT_NUMERIC, SORT_DESC, 
	$ARRSORTLEVEL['per_startdate'], SORT_STRING, SORT_ASC, 
	$ARRSORTLEVEL['per_decorate'], SORT_NUMERIC, SORT_DESC, 
	$ARRSORTLEVEL['per_birthdate'], SORT_STRING, SORT_ASC, 
	$ARRSORTLEVEL['per_id'], SORT_NUMERIC, SORT_ASC
    );
} else {
    /*array_multisort($ARRSORTLEVEL['curr_effectivedate'], SORT_ASC, SORT_STRING, $ARRSORTLEVEL['per_sortlevel'], SORT_NUMERIC, SORT_DESC,  $ARRSORTLEVEL['per_salary'], SORT_NUMERIC, SORT_DESC, $ARRSORTLEVEL['per_decorate'], SORT_NUMERIC, SORT_DESC, $ARRSORTLEVEL['per_startdate'], SORT_STRING, SORT_ASC, $ARRSORTLEVEL['per_birthdate'], SORT_STRING, SORT_ASC, $ARRSORTLEVEL['per_id'], SORT_NUMERIC, SORT_ASC
    );*/
	array_multisort(
	$ARRSORTLEVEL['poh_effectivedate_level'], SORT_STRING,SORT_ASC , 
	$ARRSORTLEVEL['curr_effectivedate'], SORT_STRING,SORT_ASC ,
	$ARRSORTLEVEL['per_salary'], SORT_NUMERIC, SORT_DESC,  
	$ARRSORTLEVEL['per_sortlevel'], SORT_NUMERIC, SORT_DESC, 
	$ARRSORTLEVEL['per_decorate'], SORT_NUMERIC, SORT_DESC, 
	$ARRSORTLEVEL['per_startdate'], SORT_STRING, SORT_ASC, 
	$ARRSORTLEVEL['per_birthdate'], SORT_STRING, SORT_ASC, 
	$ARRSORTLEVEL['per_id'], SORT_NUMERIC, SORT_ASC
    );
}

//แสดงรายการทั้งหมด
$data_count = 0;
$person_count = 0;
//foreach($PER_EFFECTIVEDATE as $key=>$value){		//[$LEVEL_NOHIS][$PER_ID]

//print_r($PER_EFFECTIVEDATE);
//echo "<hr> =>".$count_data."+".$data_count."+".$person_count;
if ($person_count > 0) {
    $count_data = $person_count;
}
 
$head_text1 = implode(",", $heading_text);
$head_width1 = implode(",", $heading_width);
$head_align1 = implode(",", $heading_align);
$col_function = implode(",", $column_function);
if ($FLAG_RTF) {
    $RTF->add_header("", 0, false); // header default
    $RTF->add_footer("", 0, false);  // footer default
    //	echo "$head_text1<br>";
    $tab_align = "center";
    $result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
} else {
    $pdf->AutoPageBreak = false;
    //	echo "$head_text1<br>";
    $result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
}
if (!$result)
    echo "****** error ****** on open table for $table<br>";
   
if ($count_data) {
    $seq = 0;
    
    for ($data_count = 0; $data_count < count($arr_content); $data_count++) {
        if(!$REPORT_ORDER){//กรณีไม่ติก สำนัก/กอง จะไม่มีค่า CONTENT จะไม่มีข้อมูล
            $REPORT_ORDER  = $arr_content[$data_count][type]="CONTENT";
            $seq = $seq - 1;
        }else{
            $REPORT_ORDER = $arr_content[$data_count][type];    
        }
       
        if($arr_content[$data_count][type] == "CONTENT"){
            $seq = $seq + 1;
            $name = $arr_content[$data_count][name];
        }else{
            $name = $arr_content[$data_count][name];
        }
        if ($have_pic && $img_file)
            $IMAGE = $arr_content[$data_count][image];    
        
        $position = $arr_content[$data_count][position];
        $level = $arr_content[$data_count][level];
        $org = $arr_content[$data_count][org];
        $posno = $arr_content[$data_count][posno];
        $educate = $arr_content[$data_count][educate];
        $em_name = $arr_content[$data_count][em_name];
        $poh_effectivedate = $arr_content[$data_count][poh_effectivedate];
        $curr_effectivedate = $arr_content[$data_count][curr_effectivedate];
        $per_sortlevel = $arr_content[$data_count][per_sortlevel];
        $per_salary = $arr_content[$data_count][per_salary];
        $decorate = $arr_content[$data_count][decorate];
        $per_startdate = $arr_content[$data_count][per_startdate];
        $per_birthdate = $arr_content[$data_count][per_birthdate];
        $per_retiredate = $arr_content[$data_count][per_retiredate];

        $arr_data = (array) null;
        if($arr_content[$data_count][type] == "CONTENT"){
            if($seq == '-') $seq ='';//ทำเพื่อ ไม่ให้นับค่าว่าง 
            $arr_data[] = $seq;
            $arr_data[] = $name;
        }else{
           $arr_data[] = "";
           $arr_data[] = $name;
        }
        if ($have_pic && $img_file)
            $arr_data[] = $IMAGE;
        $arr_data[] = $position;
        $arr_data[] = $curr_effectivedate;
        $arr_data[] = $level;
        $arr_data[] = $org;
        $arr_data[] = $posno;
        $arr_data[] = $educate;
        $arr_data[] = $em_name;
        $arr_data[] = $poh_effectivedate;
        $arr_data[] = $per_sortlevel;
        $arr_data[] = $per_salary;
        $arr_data[] = $decorate;
        $arr_data[] = $per_startdate;
        $arr_data[] = $per_birthdate;
        $arr_data[] = $per_retiredate;

        if ($FLAG_RTF){
            $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
        }else{
            if($REPORT_ORDER){
           //print_r($arr_data);
		   //echo "<pre>";
                 if($arr_content[$data_count][type] == "CONTENT"){
                
                    $result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "b", "000000", "");  //RHL  RBL  
                  }else{
                     $result = $pdf->add_data_tab($arr_data, 7, "TRBL", $data_align, "", "14");  //TRBL  
                  }
            }else{
                
                 $result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "b", "000000", "");  //RHL  RBL  
            }
           
           
        }    
        if (!$result)
            echo "****** error ****** add data to table at record count = $data_count <br>";
    } // end for
    if (!$FLAG_RTF)
        $pdf->add_data_tab("", 7, "RHBL", $data_align, "", "12", "", "000000", "");  // เส้นปิดบรรทัด	
}else {
    if ($FLAG_RTF)
        $result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
    else
        $result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
    if (!$result)
        echo "****** error ****** add text line to table at record count = $data_count <br>";
} // end if
if ($FLAG_RTF) {
    $RTF->close_tab();
//			$RTF->close_section(); 

    $RTF->display($fname);
} else {
    $pdf->close_tab("");

    $pdf->close();
    $pdf->Output();
}
?>