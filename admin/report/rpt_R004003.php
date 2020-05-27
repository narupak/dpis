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
$search_condition_New = "";
$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
$arr_search_condition[] = "(a.PER_STATUS in (" . implode(", ", $search_per_status) . "))";
$arr_search_condition[] = "(a.LEVEL_NO = '$search_level_no')";

//$arr_search_condition[] = "(p.LEVEL_NO = '$search_level_no')"; /*New*/
//$arr_search_condition[] = "(p.PER_TYPE = $search_per_type)"; /*New*/
//$arr_search_condition[] = "(p.PER_STATUS in (" . implode(", ", $search_per_status) . "))"; /*New*/


$list_type_text = $ALL_REPORT_TITLE;
if (in_array("ALL", $list_type) || !isset($list_type)) { //กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
    
    if ($DEPARTMENT_ID) {
        $arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
        //$arr_search_condition[] = "(p.DEPARTMENT_ID = $DEPARTMENT_ID)";/*New*/
        $list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
    } elseif ($MINISTRY_ID) {
        $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
        if ($select_org_structure == 1)
            $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
        $db_dpis->send_cmd($cmd);
        while ($data = $db_dpis->get_array())
            $arr_org_ref[] = $data[ORG_ID];

        $arr_search_condition[] = "(b.DEPARTMENT_ID in (" . implode($arr_org_ref, ",") . "))";
        //$arr_search_condition[] = "(p.DEPARTMENT_ID in (" . implode($arr_org_ref, ",") . "))";/*New*/
        $list_type_text .= " - $MINISTRY_NAME";
    }elseif ($PROVINCE_CODE) {
        $PROVINCE_CODE = trim($PROVINCE_CODE);
        $arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
        //$arr_search_condition[] = "(trim(org.PV_CODE) = '$PROVINCE_CODE')";/*New*/
        $list_type_text .= " - $PROVINCE_NAME";
    } // end if
} // end if

if (in_array("PER_ORG_TYPE_1", $list_type)) {
    if ($DEPARTMENT_ID) {
        $arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
        //$arr_search_condition[] = "(p.DEPARTMENT_ID = $DEPARTMENT_ID)"; /*New*/
    } elseif ($MINISTRY_ID) {
        $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
        if ($select_org_structure == 1)
            $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
        $db_dpis->send_cmd($cmd);
        while ($data = $db_dpis->get_array())
            $arr_org_ref[] = $data[ORG_ID];

        $arr_search_condition[] = "(b.DEPARTMENT_ID in (" . implode($arr_org_ref, ",") . "))";
        //$arr_search_condition[] = "(p.DEPARTMENT_ID in (" . implode($arr_org_ref, ",") . "))"; /*New*/
    } // end if

    $list_type_text = "ส่วนกลาง";
    $arr_search_condition[] = "(trim(c.OT_CODE)='01')";
    //$arr_search_condition[] = "(trim(org.OT_CODE)='01')"; /*New*/
    
}
if (in_array("PER_ORG_TYPE_2", $list_type)) {
    if ($DEPARTMENT_ID) {
        $arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
        //$arr_search_condition[] = "(p.DEPARTMENT_ID = $DEPARTMENT_ID)";/*New*/
    } elseif ($MINISTRY_ID) {
        $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
        if ($select_org_structure == 1)
            $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
        $db_dpis->send_cmd($cmd);
        while ($data = $db_dpis->get_array())
            $arr_org_ref[] = $data[ORG_ID];

        $arr_search_condition[] = "(b.DEPARTMENT_ID in (" . implode($arr_org_ref, ",") . "))";
        //$arr_search_condition[] = "(p.DEPARTMENT_ID in (" . implode($arr_org_ref, ",") . "))";/*New*/
    } // end if

    $list_type_text = "ส่วนกลางในภูมิภาค";
    $arr_search_condition[] = "(trim(c.OT_CODE)='02')";
    //$arr_search_condition[] = "(trim(org.OT_CODE)='02')";/*New*/
}
if (in_array("PER_ORG_TYPE_3", $list_type)) {
    if ($DEPARTMENT_ID) {
        $arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
        //$arr_search_condition[] = "(p.DEPARTMENT_ID = $DEPARTMENT_ID)";/*New*/
    } elseif ($MINISTRY_ID) {
        $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
        if ($select_org_structure == 1)
            $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
        $db_dpis->send_cmd($cmd);
        while ($data = $db_dpis->get_array())
            $arr_org_ref[] = $data[ORG_ID];

        $arr_search_condition[] = "(b.DEPARTMENT_ID in (" . implode($arr_org_ref, ",") . "))";
        //$arr_search_condition[] = "(p.DEPARTMENT_ID in (" . implode($arr_org_ref, ",") . "))";/*New*/
    } // end if

    $list_type_text = "ส่วนภูมิภาค";
    $arr_search_condition[] = "(trim(c.OT_CODE)='03')";
    //$arr_search_condition[] = "(trim(org.OT_CODE)='03')";/*New*/
}
if (in_array("PER_ORG_TYPE_4", $list_type)) {
    if ($DEPARTMENT_ID) {
        $arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
        //$arr_search_condition[] = "(p.DEPARTMENT_ID = $DEPARTMENT_ID)";/*New*/
    } elseif ($MINISTRY_ID) {
        $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
        if ($select_org_structure == 1)
            $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
        $db_dpis->send_cmd($cmd);
        while ($data = $db_dpis->get_array())
            $arr_org_ref[] = $data[ORG_ID];

        $arr_search_condition[] = "(b.DEPARTMENT_ID in (" . implode($arr_org_ref, ",") . "))";
        //$arr_search_condition[] = "(p.DEPARTMENT_ID in (" . implode($arr_org_ref, ",") . "))";/*New*/
    } // end if
    // ต่างประเทศ
    $list_type_text = "ต่างประเทศ";
    $arr_search_condition[] = "(trim(c.OT_CODE)='04')";
    //$arr_search_condition[] = "(trim(org.OT_CODE)='04')";/*New*/
}
if (in_array("PER_ORG", $list_type)) {
    $list_type_text = "";
    if ($select_org_structure == 0) {
        if (trim($search_org_id)) {
            $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
            //$arr_search_condition[] = "(pos.ORG_ID = $search_org_id)";/*New*/
            $list_type_text .= "$search_org_name";
        } // end if
        if (trim($search_org_id_1)) {
            $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
            //$arr_search_condition[] = "(pos.ORG_ID_1 = $search_org_id_1)";/*New*/
            $list_type_text .= " - $search_org_name_1";
        } // end if
        if (trim($search_org_id_2)) {
            $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
            //$arr_search_condition[] = "(pos.ORG_ID_2 = $search_org_id_2)";/*New*/
            $list_type_text .= " - $search_org_name_2";
        } // end if
    } else if ($select_org_structure == 1) {
        if (trim($search_org_ass_id)) {
            $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
            //$arr_search_condition[] = "(p.ORG_ID = $search_org_ass_id)";/*New*/
            $list_type_text .= "$search_org_ass_name";
        } // end if
        if (trim($search_org_ass_id_1)) {
            $arr_search_condition[] = "(a.ORG_ID_1 = $search_org_ass_id_1)";
            //$arr_search_condition[] = "(p.ORG_ID_1 = $search_org_ass_id_1)";/*New*/
            $list_type_text .= " - $search_org_ass_name_1";
        } // end if
        if (trim($search_org_ass_id_2)) {
            $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_ass_id_2)";
            //$arr_search_condition[] = "(p.ORG_ID_2 = $search_org_ass_id_2)";/*New*/
            $list_type_text .= " - $search_org_ass_name_2";
        } // end if
    }
}
if (in_array("PER_LINE", $list_type)) {
    // สายงาน
    $list_type_text = "";
    if ($line_search_code) {
        //$arr_search_condition[] = "(trim($pl_code)='$line_search_code')";
        $arr_search_condition[] = "(trim(pl_code)='$line_search_code')";/*New*/
        
        $list_type_text .= " $line_search_name";
    }
}
if (in_array("PER_COUNTRY", $list_type)) {
    // ประเทศ , จังหวัด
    $list_type_text = "";
    if (trim($search_ct_code)) {
        $search_ct_code = trim($search_ct_code);
        $arr_search_condition[] = "(trim(c.CT_CODE) = '$search_ct_code')";
        //$arr_search_condition[] = "(trim(org.CT_CODE) = '$search_ct_code')";/*New*/
        $list_type_text .= "$search_ct_name";
    } // end if
    if (trim($search_pv_code)) {
        $search_pv_code = trim($search_pv_code);
        $arr_search_condition[] = "(trim(c.PV_CODE) = '$search_pv_code')";
        //$arr_search_condition[] = "(trim(org.PV_CODE) = '$search_pv_code')";/*New*/
        $list_type_text .= " - $search_pv_name";
    } // end if
}

if (count($arr_search_condition))
    $search_condition = " where " . implode(" and ", $arr_search_condition);

if (count($arr_search_condition))
    $search_condition_New= " where " . implode(" and ", $arr_search_condition);



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
    $paper_size = array(300,650);//"A3";
    $lang_code = "TH";
    $orientation = 'L';

    $pdf = new PDF($orientation, $unit, $paper_size, $lang_code, $company_name, $report_title, $report_code, $heading, $heading_width, $heading_align);

    $pdf->Open();
    $pdf->SetMargins(5, 5, 5);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont($font, '', 12);
    
    

    $page_start_x = $pdf->x;
    $page_start_y = $pdf->y;
}


//เป็นค่าจากช่อง เรียงตามข้อมูลหน้า html
$or_der_list ="";
$comma = ", ";
//$oder_by_lis = " beg_cur_level_date ";
$oder_by_lis = " DATE_CUR_LEVEL,CUR_LEVEL_NO_4ORDER DESC,CUR_POH_EFFECTIVEDATE_4ORDER ";
for($i=0; $i<count($RPTORD_ORDER); $i++){
    $or_der_list = $RPTORD_ORDER[$i];
    switch($or_der_list){

        case "salary" : /*เงินเดือนปัจจุบัน*/
            //$oder_by_lis .= $comma."per_salary DESC " ;
            $oder_by_lis .= $comma." PER_SALARY desc " ;/*New*/
            break;
        case "start_date" :/*อายุราชการ*/
            //$oder_by_lis .= $comma."PER_STARTDATE ASC" ;
            $oder_by_lis .= $comma." PER_STARTDATE  " ;/*New*/
            break;
        case "dc_or" :/*เครื่องราชอิสริยาภรณ์*/
            //$oder_by_lis .= $comma."DC_ORDER  ASC , DEH_DATE ASC" ;
            $oder_by_lis .= $comma." DC_ORDER,DC_DATE " ;/*New*/
            break;
        case "birth_date" : /*อายุข้าราชการ*/
            //$oder_by_lis .= $comma."PER_BIRTHDATE ASC" ;
            $oder_by_lis .= $comma." PER_BIRTHDATE  " ;/*New*/
            break;
    }
}

//echo $oder_by_lis;
//die();
function list_person($search_condition, $addition_condition){
       
        global $DPISDB, $db_dpis, $db_dpis2, $db_dpis3,$db_dpis4, $position_table, $position_join, $line_table, $line_join, $f_name, $f_position;
        global $arr_rpt_order, $rpt_order_index, $search_per_type, $arr_content, $data_count, $person_count, $search_budget_year,$DATE_DISPLAY, $DEPARTMENT_ID, $oder_by_lis;
        global $days_per_year, $days_per_month, $seconds_per_day,$select_org_structure;
        global $have_pic,$img_file,$search_level_no,$position_no,$pl_name, $count_data;
        global $search_condition_New ,$search_per_status,$RPTORD_ORDER_1;

        if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
        
        //echo '>>'.$search_condition.'<br>';

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
		   $group_level_1 = "";
		   $or = "or";
		for ($i = 0; $i < count($L[$search_level_no]); $i++) {
		
			if($i == count($L[$search_level_no]) -1){
				$R ="";
				$or = "";
			}
			$group_level_1 .= "e.POH_LEVEL_NO='".$L[$search_level_no][$i]."' $or ";	 // where
			
			//$group_level .= "'".$cut_grp[$i]."'".$R;		
		} 
			 $w_group_level_1 .= "(".$group_level_1.")";
			//echo $w_group_level_1;
			
//==========================================================			

	   $group_level = "";
		$R = ",";	   
	   $num_x =  count($L[$search_level_no]);
	   $cut_grp = $L[$search_level_no];
 unset($cut_grp[$num_x-1]);	
		
		for($dx=0; $dx<count($cut_grp); $dx++){

			if($dx == count($cut_grp)-1){
			$R = "";	
			}
			$group_level .= "'".$cut_grp[$dx]."'".$R;
		}	
		
//==========================================================				
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
                
                    $StrFindArr=array('a.','b.','c.');
                    $StrReplaceArr=array('P.','POS.','ORG.');
                    $search_condition_New=  str_replace($StrFindArr, $StrReplaceArr, $search_condition_New);
                    
                    $search_conditionORG=  str_replace($StrFindArr, $StrReplaceArr, $search_condition);
                    
                    
                    $cmd="WITH PERSONLIST AS (
                            SELECT DISTINCT PER_ID 
                            FROM PER_PERSONAL P
                            WHERE (P.LEVEL_NO = '".$search_level_no."') 
                              AND (P.PER_TYPE = 1) 
                              AND (P.PER_STATUS IN (" . implode(", ", $search_per_status) . ")) 
                          ),ALLPOSTHIS AS (
                            SELECT DISTINCT P.PER_ID,P.POH_LEVEL_NO AS LEVEL_NO,P.POH_EFFECTIVEDATE
                              ,(SELECT LEVEL_SEQ_NO FROM PER_LEVEL PL WHERE P.POH_LEVEL_NO=PL.LEVEL_NO) LEVEL_SEQ_NO
                              ,(SELECT POSITION_LEVEL FROM PER_LEVEL PL WHERE P.POH_LEVEL_NO=PL.LEVEL_NO) POSITION_LEVEL
                            FROM PER_POSITIONHIS P
                            WHERE  P.PER_ID IN (SELECT PER_ID FROM PERSONLIST) 
                              AND P.POH_LEVEL_NO IS NOT NULL 
                              AND P.POH_EFFECTIVEDATE IS NOT NULL
                          ),ALLLEVEL AS (
                            SELECT DISTINCT PER_ID,MAX(LEVEL_SEQ_NO) LEVEL_SEQ_NO
                              ,MAX(LEVEL_NO) LEVEL_NO
                              ,MAX(POH_EFFECTIVEDATE) POH_EFFECTIVEDATE
                            FROM (
                                  SELECT DISTINCT PER_ID,LEVEL_SEQ_NO,LEVEL_NO,
                                    ( SELECT MIN(POH_EFFECTIVEDATE) POH_EFFECTIVEDATE 
                                      FROM ALLPOSTHIS H 
                                      WHERE (H.PER_ID=A.PER_ID  
                                        AND H.LEVEL_NO=A.LEVEL_NO)
                                    ) POH_EFFECTIVEDATE
                                  FROM ALLPOSTHIS A
                            ) GROUP BY PER_ID,LEVEL_NO
                          ),CURLEVEL AS(
                            SELECT DISTINCT PER_ID,MIN(LEVEL_SEQ_NO) LEVEL_SEQ_NO,MIN(LEVEL_NO) LEVEL_NO,MIN(POH_EFFECTIVEDATE) POH_EFFECTIVEDATE
                            FROM (
                                  SELECT DISTINCT PER_ID,LEVEL_SEQ_NO,LEVEL_NO,POH_EFFECTIVEDATE
                                  FROM ALLPOSTHIS A
                                  WHERE LEVEL_NO=(SELECT LEVEL_NO FROM PER_PERSONAL P WHERE P.PER_ID=A.PER_ID)
                            ) GROUP BY PER_ID,LEVEL_SEQ_NO,LEVEL_NO--,POH_EFFECTIVEDATE
                          ),ALLLEVELBEFORECURLEVEL AS(
                            SELECT * FROM ALLLEVEL MINUS SELECT * FROM CURLEVEL WHERE POH_EFFECTIVEDATE ='2008-12-11'
                          ),CURLEVEL4ORDER /*LEVELBEFORECURLEVEL*/ AS(
                            SELECT C.PER_ID,C.LEVEL_SEQ_NO,C.LEVEL_NO,C.POH_EFFECTIVEDATE
                            FROM (SELECT C.PER_ID,C.LEVEL_SEQ_NO,C.LEVEL_NO,C.POH_EFFECTIVEDATE, ROW_NUMBER() OVER (PARTITION BY PER_ID ORDER BY LEVEL_SEQ_NO DESC) AS SEQNUM
                                  FROM ALLLEVELBEFORECURLEVEL C 
                                  WHERE POH_EFFECTIVEDATE <>'2008-12-11' 
                                    AND POH_EFFECTIVEDATE<(SELECT POH_EFFECTIVEDATE FROM CURLEVEL X WHERE X.PER_ID=C.PER_ID)
                            ) C
                            WHERE SEQNUM = 1
                          ),B4CURLEVEL /*LEVELB4LEVELBEFORECURLEVEL*/ AS(
                            SELECT C.PER_ID,C.LEVEL_SEQ_NO,C.LEVEL_NO,C.POH_EFFECTIVEDATE
                            FROM (SELECT C.PER_ID,C.LEVEL_SEQ_NO,C.LEVEL_NO,C.POH_EFFECTIVEDATE, ROW_NUMBER() OVER (PARTITION BY PER_ID ORDER BY LEVEL_SEQ_NO DESC) AS SEQNUM
                                  FROM ALLLEVELBEFORECURLEVEL C 
                                  WHERE POH_EFFECTIVEDATE <>'2008-12-11' 
                                    AND POH_EFFECTIVEDATE<(SELECT POH_EFFECTIVEDATE FROM CURLEVEL X WHERE X.PER_ID=C.PER_ID)

                            ) C
                            WHERE SEQNUM = 2
                          ),TMPLEVEL AS(
                            SELECT DISTINCT A.PER_ID
                                 ,(SELECT MAX(LEVEL_NO) FROM CURLEVEL C WHERE C.PER_ID=A.PER_ID) CUR_LEVEL_NO
                                 ,(SELECT MAX(POH_EFFECTIVEDATE) FROM CURLEVEL C WHERE C.PER_ID=A.PER_ID) DATE_CUR_LEVEL
                                 ,CASE WHEN (SELECT POH_EFFECTIVEDATE FROM CURLEVEL C WHERE C.PER_ID=A.PER_ID) > '2008-12-11' 
                                  THEN
                                    (SELECT LEVEL_SEQ_NO FROM CURLEVEL C WHERE C.PER_ID=A.PER_ID)
                                  ELSE
                                    (SELECT NVL(MAX(A1.LEVEL_SEQ_NO),0) FROM CURLEVEL4ORDER A1 WHERE A1.PER_ID=A.PER_ID)
                                  END CUR_LEVEL_SEQ_NO_4ORDER
                                 ,CASE WHEN (SELECT POH_EFFECTIVEDATE FROM CURLEVEL C WHERE C.PER_ID=A.PER_ID) > '2008-12-11' 
                                  THEN
                                    (SELECT LEVEL_NO FROM CURLEVEL C WHERE C.PER_ID=A.PER_ID)
                                  ELSE
                                    (SELECT NVL(MAX(A1.LEVEL_NO),'**') FROM CURLEVEL4ORDER A1 WHERE A1.PER_ID=A.PER_ID)
                                  END CUR_LEVEL_NO_4ORDER
                                 ,CASE WHEN (SELECT POH_EFFECTIVEDATE FROM CURLEVEL C WHERE C.PER_ID=A.PER_ID) > '2008-12-11' 
                                  THEN
                                    (SELECT POH_EFFECTIVEDATE FROM CURLEVEL C WHERE C.PER_ID=A.PER_ID)
                                  ELSE
                                    (SELECT MAX(A1.POH_EFFECTIVEDATE) FROM CURLEVEL4ORDER A1 WHERE A1.PER_ID=A.PER_ID)
                                  END CUR_POH_EFFECTIVEDATE_4ORDER
                                  ,CASE WHEN (SELECT POH_EFFECTIVEDATE FROM CURLEVEL C WHERE C.PER_ID=A.PER_ID) > '2008-12-11' 
                                  THEN
                                    (SELECT MAX(A1.LEVEL_NO) FROM CURLEVEL4ORDER A1 WHERE A1.PER_ID=A.PER_ID)
                                  ELSE
                                    (SELECT MAX(A1.LEVEL_NO) FROM B4CURLEVEL A1 WHERE A1.PER_ID=A.PER_ID)
                                  END B4_LEVEL_NO
                                  ,CASE WHEN (SELECT POH_EFFECTIVEDATE FROM CURLEVEL C WHERE C.PER_ID=A.PER_ID) > '2008-12-11' 
                                  THEN
                                    (SELECT MAX(A1.POH_EFFECTIVEDATE) FROM CURLEVEL4ORDER A1 WHERE A1.PER_ID=A.PER_ID)
                                  ELSE
                                    (SELECT MAX(A1.POH_EFFECTIVEDATE) FROM B4CURLEVEL A1 WHERE A1.PER_ID=A.PER_ID)
                                  END B4_POH_EFFECTIVEDATE
                            FROM CURLEVEL A 
                            WHERE A.LEVEL_NO IS NOT NULL
                          )
                           , DECORATEHIS as (
                            SELECT min(dc_order) dc_order,p.per_id
                            from PERSONLIST p 
                            left join PER_DECORATEHIS dh on (dh.per_id=p.per_id)
                            left join PER_DECORATION  d on (d.dc_code=dh.dc_code and (DC_TYPE = 1 or DC_TYPE = 2))
                            group by p.per_id
                          )
                          select * from (
                          SELECT  TB.*,
                            POS.ORG_ID_1 ,PN.PN_NAME,P.PER_NAME,P.PER_SURNAME ,POS.POS_NO,
                            (SELECT PL_NAME FROM PER_LINE PL WHERE PL.PL_CODE=(SELECT PP.PL_CODE FROM PER_POSITION PP WHERE (PP.POS_ID=P.POS_ID))) PL_NAME,
                            LVL.POSITION_LEVEL,P.PER_SALARY,ORG.ORG_NAME AS SANG_GUD,
                            ORG_1.ORG_NAME AS SANG_GUD_1,
                            (SELECT min(dc_order) from PER_DECORATION  where dc_code in 
                                  (select dc_code from PER_DECORATEHIS dh where dh.per_id=p.per_id) 
                                  and (DC_TYPE = 1 or DC_TYPE = 2)
                                ) DC_ORDER
                              , SUBSTR(trim((select min(dch.deh_date) from PER_DECORATEHIS dch where dch.per_id=p.per_id and dch.dc_code = 
                                 (select min(dc_code) from PER_DECORATION where dc_order =
                                    (SELECT min(dc_order) from PER_DECORATION  where dc_code in 
                                      (select distinct dc_code from PER_DECORATEHIS dh where dh.per_id=p.per_id) 
                                      and (DC_TYPE = 1 or DC_TYPE = 2)
                                )))), 1, 10) AS  DC_DATE,
                            SUBSTR(trim(PER_RETIREDATE), 1, 10) AS PER_RETIREDATE,
                            SUBSTR(trim(PER_STARTDATE), 1, 10) AS PER_STARTDATE,
                            SUBSTR(trim(PER_BIRTHDATE), 1, 10) AS PER_BIRTHDATE
                          FROM TMPLEVEL TB 
                          LEFT JOIN PER_PERSONAL P ON(P.PER_ID=TB.PER_ID)
                          LEFT JOIN PER_PRENAME PN ON(PN.PN_CODE=P.PN_CODE)
                          LEFT JOIN PER_POSITION POS ON(P.POS_ID=POS.POS_ID)
                          LEFT JOIN PER_ORG ORG ON(POS.ORG_ID=ORG.ORG_ID)
                          LEFT JOIN PER_ORG ORG_1 ON(POS.ORG_ID_1=ORG_1.ORG_ID)
                          LEFT JOIN PER_LEVEL LVL ON(P.LEVEL_NO=LVL.LEVEL_NO)
                          LEFT JOIN PER_EDUCATE EDU ON(P.PER_ID=EDU.PER_ID AND EDU.EDU_TYPE LIKE '%2%')
                          LEFT JOIN PER_EDUCNAME EDUNAME ON(EDUNAME.EN_CODE=EDU.EN_CODE)
                          LEFT JOIN  PER_EDUCMAJOR MAJ ON(MAJ.EM_CODE=EDU.EM_CODE) 
                           left join DECORATEHIS dh on (dh.per_id=TB.per_id) 
                          ".$search_condition_New.$search_conditionORG." 
                          ) tbmain ORDER BY ".$oder_by_lis;
                  
                
               
               
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
            //echo 'select_org_structure:'.$select_org_structure;
            if ($select_org_structure == 1) {
                //$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
                $cmd = str_replace("POS.ORG_ID", "P.ORG_ID", $cmd);/*New*/
                $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
            }
           
            //---สร้าง query ของแต่ละตัว แล้ววนข้อมูลมาอีกครั้ง
            $count_data = $db_dpis->send_cmd($cmd);
           
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
                    /*New Begin*/
                    $ORG_ID_1 	= $data[ORG_ID_1];
                    $PN_NAME 	= $data[PN_NAME]; 
                    $PER_NAME 	= $data[PER_NAME]; 
                    $PER_SURNAME = $data[PER_SURNAME]; 
                    $POS_NO      = $data[POS_NO]; 
                    $PL_NAME    = trim($data[PL_NAME]); 
                    $POSITION_LEVEL = $data[POSITION_LEVEL]; 
                    $PER_SALARY	= $data[PER_SALARY]; 
                    
                    $ORG_NAME	= $data[SANG_GUD];
                    $ORG_NAME_1	= $data[SANG_GUD_1];
                    
                    
                    
                    if($RPTORD_ORDER_1=="ORG"){
                        $strorgname=$ORG_NAME_1;
                    }else{
                        if(!empty($ORG_NAME_1)){
                            $ORG_NAME_1=" / ".$ORG_NAME_1;
                        }
                        $strorgname=$ORG_NAME.$ORG_NAME_1;
                    }
                    //$ORG_NAME	= $data[SANG_GUD].$strorgname;
                    $ORG_NAME	= $strorgname;
                    
                    $PER_RETIREDATE	= $data[PER_RETIREDATE];
                    $PER_STARTDATE 	= $data[PER_STARTDATE];
                    $PER_BIRTHDATE	= $data[PER_BIRTHDATE]; 
                    
                    
                    $BEG_CUR_LEVEL_DATE= $data[CUR_POH_EFFECTIVEDATE_4ORDER]; //CUR_POH_EFFECTIVEDATE_4ORDER  เตรียมเปลี่ยบน
                    $BEG_CUR_LEVEL_NO= $data[CUR_LEVEL_NO_4ORDER]; //CUR_LEVEL_NO_4ORDER  เตรียมเปลี่ยบน
                    if( (intval($BEG_CUR_LEVEL_NO)>=1 && intval($BEG_CUR_LEVEL_NO)<=9) && trim($PTCODE) !='ทั่วไป' ){
                        $BEG_CUR_LEVEL_NO=intval($BEG_CUR_LEVEL_NO).$PTCODE;
                    }else{
                        $cmd = "select POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$BEG_CUR_LEVEL_NO' ";
                                $db_dpis2->send_cmd($cmd);
                               // echo"<pre>".$cmd;
                                $datalvl = $db_dpis2->get_array();
                        $BEG_CUR_LEVEL_NO=trim($datalvl[POSITION_LEVEL]);
                    }
                    
                    
                    
                    $CUR_EFFECTIVEDATE	= $data[DATE_CUR_LEVEL]; // DATE_CUR_LEVEL  เตรียมเปลี่ยบน
                    $BEG_BEFORE_LEVEL_NO = $data[B4_LEVEL_NO]; //B4_LEVEL_NO เตรียมเปลี่ยบน
                    $PTCODE = $data[PTCODE];
                    if( (intval($BEG_BEFORE_LEVEL_NO)>=1 && intval($BEG_BEFORE_LEVEL_NO)<=9) && trim($PTCODE) !='ทั่วไป' ){
                        $TMP_POSITION_LEVEL=intval($BEG_BEFORE_LEVEL_NO).$PTCODE;
                    }else{
                        
                        $cmd = "select POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$BEG_BEFORE_LEVEL_NO' ";
                                $db_dpis2->send_cmd($cmd);
                               // echo"<pre>".$cmd;
                                $datalvl = $db_dpis2->get_array();
                        $TMP_POSITION_LEVEL=trim($datalvl[POSITION_LEVEL]);
                    }
                    
                    $POH_EFFECTIVEDATE	= $data[B4_POH_EFFECTIVEDATE]; //B4_POH_EFFECTIVEDATE   เตรียมเปลี่ยบน
                    $POH_EFFECTIVEDATE_N = show_date_format($POH_EFFECTIVEDATE, $DATE_DISPLAY);
                    //$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
                    //$DC_ORDER = $data[DC_ORDER];
                    $PER_WORK = date_difference(date("Y-m-d"), trim($PER_STARTDATE), "full");
                    $PER_STARTDATE2 = show_date_format($PER_STARTDATE, $DATE_DISPLAY);
                    $PER_BIRTHDATE2 = show_date_format($PER_BIRTHDATE, $DATE_DISPLAY);
                    $PER_AGE = date_difference(date("Y-m-d"), trim($PER_BIRTHDATE), "full");
                    $PER_RETIREDATE2 = show_date_format($PER_RETIREDATE, $DATE_DISPLAY);
                    /*New End*/
				   

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

                   
						
                $data_count++;
                    $arr_content[$data_count][type] = "CONTENT";
					if ($have_pic) $img_file = show_image($PER_ID,1);
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
                    
                    $arr_content[$data_count][beg_cur_level_no] = $BEG_CUR_LEVEL_NO;
                    $arr_content[$data_count][beg_cur_level_date] = show_date_format(trim($BEG_CUR_LEVEL_DATE), $DATE_DISPLAY);
                    
                    
                    
                    $arr_content[$data_count][per_sortlevel] = $TMP_POSITION_LEVEL;    //___$LEVEL_NO?level_no_format($LEVEL_NO):"";
                    $arr_content[$data_count][poh_effectivedate] = $POH_EFFECTIVEDATE_N;
                    
                    $arr_content[$data_count][curr_effectivedate] = show_date_format(trim($CUR_EFFECTIVEDATE), $DATE_DISPLAY);
                    
                    
                    
                    //หาระดับตำแหน่งนั้น--------------------
//                     $cmd2 = "select LEVEL_NAME from PER_LEVEL where LEVEL_NO='" . $LEVEL_NOHIS . "' ";
//                    $db_dpis3->send_cmd($cmd2);
//                    $levelname = $db_dpis3->get_array();
                    //---------------------------------------------
                    
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
        
        
        $beg_cur_level_no=$arr_content[$data_count][beg_cur_level_no];
        $beg_cur_level_date= $arr_content[$data_count][beg_cur_level_date];
                    
                    
        $per_sortlevel = $arr_content[$data_count][per_sortlevel];//ระดับก่อนหน้า
        $poh_effectivedate = $arr_content[$data_count][poh_effectivedate];//วันที่ระดับก่อนหน้า
        
        
        $curr_effectivedate = $arr_content[$data_count][curr_effectivedate];
        $per_salary = $arr_content[$data_count][per_salary];
        $decorate = $arr_content[$data_count][decorate];
        $per_startdate = $arr_content[$data_count][per_startdate];
        $per_birthdate = $arr_content[$data_count][per_birthdate];
        $per_retiredate = $arr_content[$data_count][per_retiredate];

        $arr_data = (array) null;
        if($arr_content[$data_count][type] == "CONTENT"){
            if($seq == '-') $seq ='';//ทำเพื่อ ไม่ให้นับค่าว่าง 
            $arr_data[] = $seq;
            //$arr_data[] = $name;
        }else{
			$arr_data[] ="";
		}
        
        if ($have_pic && $img_file){
            $arr_data[] = $IMAGE;
		}
		$arr_data[] = $name;
        $arr_data[] = $position;
        $arr_data[] = $curr_effectivedate;
        $arr_data[] = $level;
        $arr_data[] = $org;
        $arr_data[] = $posno;
        $arr_data[] = $educate;
        $arr_data[] = $em_name;
        
        $arr_data[] = $beg_cur_level_no;
        $arr_data[] = $beg_cur_level_date;
       
        
        $arr_data[] = $per_sortlevel;
        $arr_data[] = $poh_effectivedate;
        
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