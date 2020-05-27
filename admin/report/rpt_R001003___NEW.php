<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_select = "a.POS_ID";
		$position_join = "a.POS_ID=b.POS_ID"; 
		$position_no= "a.POS_NO";
		$position_no_name= "a.POS_NO_NAME";
		$line_table = "PER_LINE";
		$line_join = "a.PL_CODE=e.PL_CODE";
		$line_code = "a.PL_CODE";
		$line_name = "e.PL_NAME as PL_NAME, e.PL_SHORTNAME as PL_SHORTNAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
		$line_title= " สายงาน";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_select = "a.POEM_ID";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$position_no= "a.POEM_NO";
		$position_no_name= "a.POEM_NO_NAME";
		$line_table = "PER_POS_NAME";
		$line_join = "a.PN_CODE=e.PN_CODE";
		$line_code = "a.PN_CODE";
		$line_name = "e.PN_NAME as PL_NAME";	
		$line_search_code=trim($search_pn_code);
		$line_search_name=trim($search_pn_name);
		$line_title= " ชื่อตำแหน่ง";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_select = "a.POEMS_ID";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$position_no= "a.POEMS_NO";
		$position_no_name= "a.POEMS_NO_NAME";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "a.EP_CODE=e.EP_CODE";
		$line_code = "a.EP_CODE";
		$line_name = "e.EP_NAME as PL_NAME";	
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);
		$line_title= " ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_select = "a.POT_ID";
		$position_join = "a.POT_ID=b.POT_ID";
		$position_no= "a.POT_NO";
		$position_no_name= "a.POT_NO_NAME";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "a.TP_CODE=e.TP_CODE";
		$line_code = "a.TP_CODE";
		$line_name = "e.TP_NAME as PL_NAME";	
		$line_search_code=trim($search_tp_code);
		$line_search_name=trim($search_tp_name);
		$line_title= " ชื่อตำแหน่ง";
	} // end if	
	
	if(!trim($RPTORD_LIST)){ 
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		elseif($list_type=="PER_COUNTRY" && trim($search_pv_code)!="") $RPTORD_LIST = "PROVINCE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE", "CO_LEVEL", "PROVINCE"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "ORG" :
				if($select_list) $select_list .= ", ";
				$select_list .= "b.ORG_SEQ_NO, a.ORG_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.ORG_SEQ_NO, a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.ORG_ID_1";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.ORG_ID_1";

				$heading_name .= " $ORG_TITLE1";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.ORG_ID_2";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.ORG_ID_2";

				$heading_name .= " $ORG_TITLE2";
				break;
			case "CO_LEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.CL_NAME, a.LEVEL_NO";
				
				if($order_by) $order_by .= ", ";
				$order_by .= "a.LEVEL_NO";

				$heading_name .= " $CL_TITLE";
				break;
			case "PROVINCE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "b.PV_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.PV_CODE";

				$heading_name .= " $PV_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		$order_by = "b.ORG_SEQ_NO, a.ORG_ID";
	} // end if
	if(!trim($select_list)){ 
		$select_list = "b.ORG_SEQ_NO, a.ORG_ID";
	} // end if

	$search_condition = "";
	$arr_search_condition[] = "(a.POS_STATUS=1)";

	$list_type_text = $ALL_REPORT_TITLE;

	if($list_type == "PER_ORG_TYPE_1"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		$arr_search_condition[] = "(trim(b.OT_CODE)='01')";
	}elseif($list_type == "PER_ORG_TYPE_2"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลางในภูมิภาค
		$list_type_text = "ส่วนกลางในภูมิภาค";
		$arr_search_condition[] = "(trim(b.OT_CODE)='02')";
	}elseif($list_type == "PER_ORG_TYPE_3"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		$arr_search_condition[] = "(trim(b.OT_CODE)='03')";
	}elseif($list_type == "PER_ORG_TYPE_4"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ต่างประเทศ
		$list_type_text = "ต่างประเทศ";
		$arr_search_condition[] = "(trim(b.OT_CODE)='04')";
	}elseif($list_type == "PER_ORG"){
		$list_type_text = "";
			if(trim($search_org_id)){ 
				$arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
				$list_type_text .= "$search_org_name";
			} // end if				
			if(trim($search_org_id_1)){ 
				$arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1)";
				$list_type_text .= " - $search_org_name_1";
			} // end if
			if(trim($search_org_id_2)){ 
				 $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2)";
				$list_type_text .= " - $search_org_name_2";
			} // end if
	}elseif($list_type == "PER_LINE"){
		// สายงาน
		$list_type_text = "";
		if(trim($line_search_code)){
			$arr_search_condition[] = "(trim($line_code)='$line_search_code')";
			$list_type_text .= $line_search_name;
		} // end if
	}elseif($list_type == "PER_COUNTRY"){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(b.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}else{
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	if($DPISDB=="odbc"){
		$search_condition .= (trim($search_condition)?" and ":" where ") . 
						   " $position_select not in ( select $position_select from $position_table a, PER_PERSONAL b where $position_join and b.PER_STATUS=1 and a.POS_STATUS=1 ) ";
	}elseif($DPISDB=="oci8"){ 
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$search_condition .= " and $position_select not in ( select $position_select from $position_table a, PER_PERSONAL b where $position_join and b.PER_STATUS=1 and a.POS_STATUS=1 ) ";
	}elseif($DPISDB=="mysql"){
		$search_condition .= (trim($search_condition)?" and ":" where ") . 
						   " $position_select not in ( select $position_select from $position_table a, PER_PERSONAL b where $position_join and b.PER_STATUS=1 and a.POS_STATUS=1 ) ";
	} // end if

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายชื่อตำแหน่งว่าง";
	$report_code = "R0103";
	$orientation='L';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$heading_width[0] = "100";
	$heading_width[1] = "25";
	$heading_width[2] = "40";
	$heading_width[3] = "40";
	$heading_width[4] = "30";
	$heading_width[5] = "30";

   $heading_text[0] = "$heading_name|";
	$heading_text[1] = "เลขที่ตำแหน่ง";
	$heading_text[2] = "<**1**>ประเภทตำแหน่ง|".(($NUMBER_DISPLAY==2)?convert2thaidigit('ตาม พรบ.35'):'ตาม พรบ.35');
	$heading_text[3] = "<**1**>ประเภทตำแหน่ง|".(($NUMBER_DISPLAY==2)?convert2thaidigit('ตาม พรบ.51'):'ตาม พรบ.51');
	$heading_text[4] = "อัตราเงินเดือน|ถือจ่าย";
	$heading_text[5] = "วันที่ตำแหน่งว่าง";
		
	$heading_align = array('C','C','C','C','C','C');
/*
	function print_header(){
		global $pdf, $heading_width, $heading_name, $NUMBER_DISPLAY;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,$heading_name,'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"เลขที่",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ประเภทตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ประเภทตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"อัตราเงินเดือน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"วันที่",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ตำแหน่ง",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("ตาม พรบ.35"):"ตาม พรบ.35"),'LBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("ตาม พรบ.51"):"ตาม พรบ.51"),'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ถือจ่าย",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ตำแหน่งว่าง",'LBR',1,'C',1);
	} // function		
*/
	function list_position($search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $arr_rpt_order, $rpt_order_index, $arr_content, $data_count, $position_count,$DATE_DISPLAY;
		global $select_org_structure, $position_table, $position_no,$position_no_name, $line_table, $line_join, $line_code, $line_name;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		// รายชื่อตำแหน่งว่าง
		if($DPISDB=="odbc"){
			$cmd = " select			$line_name, a.CL_NAME, $position_no as POS_NO,$position_no_name as POS_NO_NAME, d.PT_NAME, a.POS_SALARY, a.POS_CHANGE_DATE, f.POSITION_TYPE
					   from		(
										(
											(
												(
												$position_table a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) left join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
										) left join PER_TYPE d on (a.PT_CODE=d.PT_CODE)
									) inner join $line_table e on ($line_join)
								) left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
					   $search_condition
					   order by		$line_code, a.LEVEL_NO, IIf(IsNull($position_no), 0, CLng($position_no)) , $position_no_name ";
		}elseif($DPISDB=="oci8"){
//			$search_condition = str_replace(" where ", " and ", $search_condition);	
			$cmd = " select			$line_name, a.CL_NAME, $position_no as POS_NO,$position_no_name as POS_NO_NAME , d.PT_NAME, a.POS_SALARY, a.POS_CHANGE_DATE, f.POSITION_TYPE
					   from			$position_table a, PER_ORG b, PER_CO_LEVEL c, PER_TYPE d, $line_table e, PER_LEVEL f
					   where		a.ORG_ID=b.ORG_ID and a.CL_NAME=c.CL_NAME(+)
									and a.PT_CODE=d.PT_CODE(+) and $line_join and a.LEVEL_NO=f.LEVEL_NO(+)					 					
									$search_condition
					   order by		$line_code, a.LEVEL_NO, TO_NUMBER($position_no) , $position_no_name";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			$line_name, a.CL_NAME, $position_no as POS_NO,$position_no_name as POS_NO_NAME, d.PT_NAME, a.POS_SALARY, a.POS_CHANGE_DATE, f.POSITION_TYPE
					   from		(
										(
											(
												(
												$position_table a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) left join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
										) left join PER_TYPE d on (a.PT_CODE=d.PT_CODE)
									) inner join $line_table e on ($line_join)
								) left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
					   $search_condition
					   order by		$line_code, a.LEVEL_NO, $position_no , $position_no_name ";
		} // end if
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
 //		echo "$cmd<hr><br>";

		while($data = $db_dpis2->get_array()){
			$position_count++;
//			$PL_NAME = trim($data[PL_SHORTNAME])?$data[PL_SHORTNAME]:$data[PL_NAME];
			$PL_NAME = trim($data[PL_NAME])?$data[PL_NAME]:$data[PL_SHORTNAME];
			$CL_NAME = trim($data[CL_NAME]);
			$POS_NO = trim($data[POS_NO]);
			$POS_NO_NAME = trim($data[POS_NO_NAME]);
			$PT_NAME = trim($data[PT_NAME]);
			$POSITION_TYPE = trim($data[POSITION_TYPE]); 
			$POS_SALARY = number_format($data[POS_SALARY]);
			$POS_CHANGE_DATE = show_date_format($data[POS_CHANGE_DATE],$DATE_DISPLAY);
			
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 5)) . $position_count .". ". $PL_NAME ." ". $CL_NAME;
			$arr_content[$data_count][pos_no] = $POS_NO;
			$arr_content[$data_count][pos_no_name] = $POS_NO_NAME;
			$arr_content[$data_count][pt_name] = $PT_NAME;
			$arr_content[$data_count][pos_salary] = $POS_SALARY;
			$arr_content[$data_count][pos_change_date] = $POS_CHANGE_DATE;	
			$arr_content[$data_count][cmd_position_type] = $POSITION_TYPE;	
			
			$data_count++;			
		} // end while
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order,$select_org_structure;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $CL_NAME, $PV_CODE;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					if($ORG_ID) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
					else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
				break;
				case "ORG_1" :
					if($ORG_ID_1) $arr_addition_condition[] = "(a.ORG_ID_1 = $ORG_ID_1)";
					else $arr_addition_condition[] = "(a.ORG_ID_1 = 0 or a.ORG_ID_1 is null)";
				break;
				case "ORG_2" :
					if($ORG_ID_2) $arr_addition_condition[] = "(a.ORG_ID_2 = $ORG_ID_2)";
					else $arr_addition_condition[] = "(a.ORG_ID_2 = 0 or a.ORG_ID_2 is null)";
				break;
				case "CO_LEVEL" :
					if($CL_NAME) $arr_addition_condition[] = "(trim(a.CL_NAME) = '$CL_NAME')";
					else $arr_addition_condition[] = "(trim(a.CL_NAME) = '$CL_NAME' or a.CL_NAME is null)";
				break;
				case "PROVINCE" :
					if($PV_CODE) $arr_addition_condition[] = "(trim(b.PV_CODE) = '$PV_CODE')";
					else $arr_addition_condition[] = "(trim(b.PV_CODE) = '$PV_CODE' or b.PV_CODE is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $CL_NAME, $PV_CODE;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					$ORG_ID = -1;
				break;
				case "ORG_1" :
					$ORG_ID_1 = -1;
				break;
				case "ORG_2" :
					$ORG_ID_2 = -1;
				break;
				case "CO_LEVEL" :
					$CL_NAME = -1;
				break;
				case "PROVINCE" :
					$PV_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function

	if($DPISDB=="odbc"){
		$cmd = " 	select		distinct $select_list
					from		(
									(
										(
											$position_table a
											inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
										) left join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
									) inner join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
								) inner join PER_ORG g on (d.ORG_ID_REF=g.ORG_ID)
					$search_condition
					order by	$order_by ";
	}elseif($DPISDB=="oci8"){
//		$search_condition = str_replace(" where ", " and ", $search_condition);	
		$cmd = " 	select		distinct $select_list
					from		$position_table a, PER_ORG b, PER_CO_LEVEL c, PER_ORG d, PER_ORG g, PER_LEVEL h
					where		a.ORG_ID=b.ORG_ID and a.CL_NAME=c.CL_NAME(+) and b.ORG_ID_REF=d.ORG_ID and d.ORG_ID_REF=g.ORG_ID and a.LEVEL_NO=h.LEVEL_NO(+)
								$search_condition
					order by	$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " 	select		distinct $select_list
					from		(
									(
										(
											$position_table a
											inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
										) left join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
									) inner join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
								) inner join PER_ORG g on (d.ORG_ID_REF=g.ORG_ID)
					$search_condition
					order by	$order_by ";
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;
	$position_count = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :
					if($ORG_ID != trim($data[ORG_ID])){
						$ORG_ID = trim($data[ORG_ID]);
						if($ORG_ID != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						$arr_content[$data_count][pos_no] = "";
						$arr_content[$data_count][pos_no_name] = "";
						$arr_content[$data_count][pt_name] = "";
						$arr_content[$data_count][pos_salary] = "";
						$arr_content[$data_count][pos_change_date] = "";
						$arr_content[$data_count][cmd_position_type] = "";
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;

						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_position($search_condition, $addition_condition);
					} // end if
				break;

				case "ORG_1" :
					if($ORG_ID_1 != trim($data[ORG_ID_1])){
						$ORG_ID_1 = trim($data[ORG_ID_1]);
						if($ORG_ID_1 != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_1 = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_1";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_1;
						$arr_content[$data_count][pos_no] = "";
						$arr_content[$data_count][pos_no_name] = "";
						$arr_content[$data_count][pt_name] = "";
						$arr_content[$data_count][pos_salary] = "";
						$arr_content[$data_count][pos_change_date] = "";
						$arr_content[$data_count][cmd_position_type] = "";			
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;

						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_position($search_condition, $addition_condition);
					} // end if
				break;
		
				case "ORG_2" :
					if($ORG_ID_2 != trim($data[ORG_ID_2])){
						$ORG_ID_2 = trim($data[ORG_ID_2]);
						if($ORG_ID_2 != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_2 = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_2";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_2;
						$arr_content[$data_count][pos_no] = "";
						$arr_content[$data_count][pos_no_name] = "";
						$arr_content[$data_count][pt_name] = "";
						$arr_content[$data_count][pos_salary] = "";
						$arr_content[$data_count][pos_change_date] = "";
						$arr_content[$data_count][cmd_position_type] = "";				
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;

						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_position($search_condition, $addition_condition);
					} // end if
				break;
		
				case "CO_LEVEL" :
					if($CL_NAME != trim($data[CL_NAME])){
						$CL_NAME = trim($data[CL_NAME]);

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "CO_LEVEL";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . (trim($CL_NAME)?"ระดับ $CL_NAME":"[ไม่ระบุช่วงระดับตำแหน่ง]");
						$arr_content[$data_count][pos_no] = "";
						$arr_content[$data_count][pos_no_name] = "";
						$arr_content[$data_count][pt_name] = "";
						$arr_content[$data_count][pos_salary] = "";
						$arr_content[$data_count][pos_change_date] = "";
						$arr_content[$data_count][cmd_position_type] = "";				
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;

						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_position($search_condition, $addition_condition);
					} // end if
				break;
		
				case "PROVINCE" :
					if($PV_CODE != trim($data[PV_CODE])){
						$PV_CODE = trim($data[PV_CODE]);
						if($PV_CODE != ""){
							$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PV_NAME = $data2[PV_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "PROVINCE";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PV_NAME;
						$arr_content[$data_count][pos_no] = "";
						$arr_content[$data_count][pos_no_name] = "";
						$arr_content[$data_count][pt_name] = "";
						$arr_content[$data_count][pos_salary] = "";
						$arr_content[$data_count][pos_change_date] = "";
						$arr_content[$data_count][cmd_position_type] = "";			
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;

						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_position($search_condition, $addition_condition);
					} // end if
				break;
			} // end switch case
		} // end for
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
if($export_type=="report"){
	if($count_data){
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
		if (!$result) echo "****** error ****** on open table for $table<br>";
		$pdf->AutoPageBreak = false; 
		//print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$POS_NO = $arr_content[$data_count][pos_no];
			$POS_NO_NAME = $arr_content[$data_count][pos_no_name];
			$PT_NAME = $arr_content[$data_count][pt_name];
			$POS_SALARY = $arr_content[$data_count][pos_salary];
			$POS_CHANGE_DATE = $arr_content[$data_count][pos_change_date];
			$CMD_POSITION_TYPE=$arr_content[$data_count][cmd_position_type]; 
				
/****		
     		$border = "";
//			if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
			if($REPORT_ORDER != "CONTENT"){
				$pdf->SetFont($fontb,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			}else{
				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			} // end if

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->MultiCell($heading_width[0], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME):$NAME), $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0];
			$pdf->y = $start_y;
			$pdf->Cell($heading_width[1], 7, $POS_NO_NAME.(($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO), $border, 0, 'C', 0);
			$pdf->Cell($heading_width[2], 7, "$PT_NAME", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[3], 7, "$CMD_POSITION_TYPE", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[4], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($POS_SALARY):$POS_SALARY), $border, 0, 'C', 0);
			$pdf->Cell($heading_width[5], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($POS_CHANGE_DATE):$POS_CHANGE_DATE), $border, 0, 'C', 0);

			

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
			for($i=0; $i<=5; $i++){
				$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
				$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
			//====================================================

			if(($pdf->h - $max_y - 10) < 15){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < (count($arr_content) - 1)){
					$pdf->AddPage();
				//	print_header();
					$max_y = $pdf->y;
				} // end if
			}else{
				if($data_count == (count($arr_content) - 1)) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
			} // end if
			$pdf->x = $start_x;			$pdf->y = $max_y; 
*****/

		if ($NUMBER_DISPLAY==2) {
			$NAME = convert2thaidigit($NAME);
			$POS_NO = "$POS_NO_NAME".convert2thaidigit($POS_NO);
			$POS_SALARY = convert2thaidigit($POS_SALARY);
			$POS_CHANGE_DATE = convert2thaidigit($POS_CHANGE_DATE);
		}

		if($REPORT_ORDER == "CONTENT"){
				$arr_data = (array) null;
				$arr_data[] = "$NAME";
				$arr_data[] = "$POS_NO";
				$arr_data[] = "$PT_NAME";
				$arr_data[] = "$CMD_POSITION_TYPE";
				$arr_data[] = "$POS_SALARY";
				$arr_data[] = "$POS_CHANGE_DATE";
				
				$data_align = array("L", "R", "R", "R", "R", "R");
				
				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "cordia", "14", "", "000000", "");
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		}else{			
				$arr_data = (array) null;
				$arr_data[] = "$NAME";
				$arr_data[] = "$POS_NO";
				$arr_data[] = "$PT_NAME";
				$arr_data[] = "$CMD_POSITION_TYPE";
				$arr_data[] = "$POS_SALARY";
				$arr_data[] = "$POS_CHANGE_DATE";
				
				$data_align = array("L", "R", "R", "R", "R", "R");
				
				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "cordia", "14", "", "000000", "");
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		}	
} // end for				
		
	}else{
		///$pdf->AddPage();
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if
	$pdf->close_tab(""); 

	$pdf->close();
	$pdf->Output();	
	}else if($export_type=="graph"){//if($export_type=="report"){
	
	}
?>