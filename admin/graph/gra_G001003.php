<?
	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", "1800");
	
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
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
				if($select_org_structure==0) $select_list .= "b.ORG_SEQ_NO, a.ORG_ID";
				elseif($select_org_structure==1) $select_list .= "e.ORG_SEQ_NO, d.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_SEQ_NO, a.ORG_ID";
				elseif($select_org_structure==1) $order_by .= "e.ORG_SEQ_NO, d.ORG_ID";

				$heading_name .= " สำนัก/กอง";
				break;
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "a.ORG_ID_1";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "a.ORG_ID_1";

				$heading_name .= " ฝ่าย";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "a.ORG_ID_2";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "a.ORG_ID_2";

				$heading_name .= " งาน";
				break;
			case "CO_LEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.CL_NAME, c.LEVEL_NO_MIN";
				
				if($order_by) $order_by .= ", ";
				$order_by .= "c.LEVEL_NO_MIN";

				$heading_name .= " ช่วงระดับตำแหน่ง";
				break;
			case "PROVINCE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "b.PV_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.PV_CODE";

				$heading_name .= " จังหวัด";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if($select_org_structure==0) $order_by = "b.ORG_SEQ_NO, a.ORG_ID";
		elseif($select_org_structure==1) $order_by = "e.ORG_SEQ_NO, d.ORG_ID";
	} // end if
	if(!trim($select_list)){ 
		if($select_org_structure==0) $select_list = "b.ORG_SEQ_NO, a.ORG_ID";
		elseif($select_org_structure==1) $select_list = "e.ORG_SEQ_NO, d.ORG_ID";
	} // end if

	$search_condition = "";
	$arr_search_condition[] = "(a.POS_STATUS=1)";

	$list_type_text = "ทั้งส่วนราชการ";

	if($list_type == "PER_ORG_TYPE_1"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='01')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.OT_CODE)='01')";
	}elseif($list_type == "PER_ORG_TYPE_2"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลางในภูมิภาค
		$list_type_text = "ส่วนกลางในภูมิภาค";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='02')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.OT_CODE)='02')";
	}elseif($list_type == "PER_ORG_TYPE_3"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.OT_CODE)='03')";
	}elseif($list_type == "PER_ORG_TYPE_4"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ต่างประเทศ
		$list_type_text = "ต่างประเทศ";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='04')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.OT_CODE)='04')";
	}elseif($list_type == "PER_ORG"){
		// สำนัก/กอง , ฝ่าย , งาน
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
		if(trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "(trim(a.PL_CODE)='$search_pl_code')";
			$list_type_text .= "$search_pl_name";
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
		// ทั้งส่วนราชการ
		if($DEPARTMENT_ID){
			//if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_REF = $DEPARTMENT_ID)";
			//elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF = $DEPARTMENT_ID)";
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	if($DPISDB=="odbc"){
		$search_condition .= (trim($search_condition)?" and ":" where ") . 
						   " a.POS_ID not in ( select a.POS_ID from PER_POSITION a, PER_PERSONAL b where a.POS_ID=b.POS_ID and b.PER_STATUS=1 and a.POS_STATUS=1 ) ";
	}elseif($DPISDB=="oci8"){ 
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$search_condition .= " and a.POS_ID not in ( select a.POS_ID from PER_POSITION a, PER_PERSONAL b where a.POS_ID=b.POS_ID and b.PER_STATUS=1 and a.POS_STATUS=1 ) ";
	}elseif($DPISDB=="mysql"){
		$search_condition .= (trim($search_condition)?" and ":" where ") . 
						   " a.POS_ID not in ( select a.POS_ID from PER_POSITION a, PER_PERSONAL b where a.POS_ID=b.POS_ID and b.PER_STATUS=1 and a.POS_STATUS=1 ) ";
	} // end if

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
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
	$pdf->SetFont('angsa','',14);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$heading_width[0] = "127";
	$heading_width[1] = "25";
	$heading_width[2] = "50";
	$heading_width[3] = "30";
	$heading_width[4] = "25";
	$heading_width[5] = "30";

	function print_header(){
		global $pdf, $heading_width, $heading_name;
		
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"อัตรา",'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,$heading_name,'LR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"เลขที่",'LR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ประเภทตำแหน่ง",'LR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ประเภทตำแหน่ง",'LR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"เงินเดือน",'LR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"วันที่",'LR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ตำแหน่ง",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ตาม พรบ.35",'LBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ตาม พรบ.51",'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ถือจ่าย",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ตำแหน่งว่าง",'LBR',1,'C',1);
	} // function		

	function list_position($search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $arr_rpt_order, $rpt_order_index, $arr_content, $data_count, $position_count, $month_abbr;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		// รายชื่อตำแหน่งว่าง
			
		if($DPISDB=="odbc"){
			$cmd = " select			e.PL_SHORTNAME, e.PL_NAME, a.CL_NAME, a.POS_NO, d.PT_NAME, a.POS_SALARY, a.POS_CHANGE_DATE, f.LEVEL_NAME
					   from			(
										(
											(
												(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
										) inner join PER_TYPE d on (a.PT_CODE=d.PT_CODE)
									) inner join PER_LINE e on (a.PL_CODE=e.PL_CODE)
								) inner join PER_LEVEL f on (c.LEVEL_NO_MIN=f.LEVEL_NO)
					   $search_condition
					   order by		a.PL_CODE, c.LEVEL_NO_MIN, IIf(IsNull(a.POS_NO), 0, CInt(a.POS_NO))
				   	";
		}elseif($DPISDB=="oci8"){
//			$search_condition = str_replace(" where ", " and ", $search_condition);	
			$cmd = " select			e.PL_SHORTNAME, e.PL_NAME, a.CL_NAME, a.POS_NO, d.PT_NAME, a.POS_SALARY, a.POS_CHANGE_DATE, f.LEVEL_NAME
					   from			PER_POSITION a, PER_ORG b, PER_CO_LEVEL c, PER_TYPE d, PER_LINE e, PER_LEVEL f
					   where		a.ORG_ID=b.ORG_ID and a.CL_NAME=c.CL_NAME
									and a.PT_CODE=d.PT_CODE and a.PL_CODE=e.PL_CODE and c.LEVEL_NO_MIN=f.LEVEL_NO					 					
									$search_condition
					   order by		a.PL_CODE, c.LEVEL_NO_MIN, TO_NUMBER(a.POS_NO)
				   	";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			e.PL_SHORTNAME, e.PL_NAME, a.CL_NAME, a.POS_NO, d.PT_NAME, a.POS_SALARY, a.POS_CHANGE_DATE, f.LEVEL_NAME
					   from			(
										(
											(
												(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
										) inner join PER_TYPE d on (a.PT_CODE=d.PT_CODE)
									) inner join PER_LINE e on (a.PL_CODE=e.PL_CODE)
								) inner join PER_LEVEL f on (c.LEVEL_NO_MIN=f.LEVEL_NO)
					   $search_condition
					   order by		a.PL_CODE, c.LEVEL_NO_MIN, a.POS_NO
				   	";
		} // end if
		
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		//echo "$cmd<hr><br>";
		while($data = $db_dpis2->get_array()){
			$position_count++;
//			$PL_NAME = trim($data[PL_SHORTNAME])?$data[PL_SHORTNAME]:$data[PL_NAME];
			$PL_NAME = trim($data[PL_NAME])?$data[PL_NAME]:$data[PL_SHORTNAME];
			$CL_NAME = trim($data[CL_NAME]);
			$POS_NO = trim($data[POS_NO]);
			$PT_NAME = trim($data[PT_NAME]);
			$LEVEL_NAME = trim($data[LEVEL_NAME]); //echo "$LEVEL_NAME<hr><br>";
			$POS_SALARY = number_format($data[POS_SALARY]);
			$POS_CHANGE_DATE = trim($data[POS_CHANGE_DATE]);
			if($POS_CHANGE_DATE){
				$arr_temp = explode("-", substr($POS_CHANGE_DATE, 0, 10));
				$POS_CHANGE_DATE = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
			} // end if
			
		$arr_temp = explode(" ", $LEVEL_NAME);
		//หาชื่อระดับตำแหน่ง 
		$CMD_LEVEL_NAME =  $arr_temp[1];
		//หาชื่อตำแหน่งประเภท
		
			$CMD_POSITION_TYPE = str_replace("ประเภท", "", $arr_temp[0]);
		
		//unset($LEVEL_NAME);
		//echo "$CMD_POSITION_TYPE<hr><br>";

			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 5)) . $position_count .". ". $PL_NAME ." ". $CL_NAME;
			$arr_content[$data_count][pos_no] = $POS_NO;
			$arr_content[$data_count][pt_name] = $PT_NAME;
			$arr_content[$data_count][pos_salary] = $POS_SALARY;
			$arr_content[$data_count][pos_change_date] = $POS_CHANGE_DATE;	
			$arr_content[$data_count][cmd_position_type] = $CMD_POSITION_TYPE;	
				
			$data_count++;			
		} // end while
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order;
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

	if($select_org_structure == 0){
		if($DPISDB=="odbc"){
			$cmd = " 	select		distinct $select_list
						from		(
										(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
										) inner join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
									) inner join PER_ORG g on (d.ORG_ID_REF=g.ORG_ID)
						$search_condition
						order by	$order_by
					";
		}elseif($DPISDB=="oci8"){
	//		$search_condition = str_replace(" where ", " and ", $search_condition);	
			$cmd = " 	select		distinct $select_list
						from		PER_POSITION a, PER_ORG b, PER_CO_LEVEL c, PER_ORG d, PER_ORG g, PER_LEVEL h
						where		a.ORG_ID=b.ORG_ID and a.CL_NAME=c.CL_NAME and b.ORG_ID_REF=d.ORG_ID and d.ORG_ID_REF=g.ORG_ID and c.LEVEL_NO_MIN=h.LEVEL_NO
									$search_condition
						order by	$order_by
					";
		}elseif($DPISDB=="mysql"){
			$cmd = " 	select		distinct $select_list
						from		(
										(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
										) inner join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
									) inner join PER_ORG g on (d.ORG_ID_REF=g.ORG_ID)
						$search_condition
						order by	$order_by
					";
		}
	}elseif($select_org_structure == 1){
		if($DPISDB=="odbc"){
			$cmd = " 	select		distinct $select_list
						from		(
										(
											(
												(
													(
														PER_POSITION a
														inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
												) inner join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
											) inner join PER_ORG_ASS e on (d.ORG_ID=e.ORG_ID)
										) inner join PER_ORG_ASS f on (e.ORG_ID_REF=f.ORG_ID)
									) inner join PER_ORG_ASS g on (f.ORG_ID_REF=g.ORG_ID)
						$search_condition
						order by	$order_by
					";
		}elseif($DPISDB=="oci8"){
	//		$search_condition = str_replace(" where ", " and ", $search_condition);	
			$cmd = " 	select		distinct $select_list
						from		PER_POSITION a, PER_ORG b, PER_CO_LEVEL c, PER_PERSONAL d, PER_ORG_ASS e, PER_ORG_ASS f, PER_ORG_ASS g
						where		a.ORG_ID=b.ORG_ID and a.CL_NAME=c.CL_NAME and a.POS_ID=d.POS_ID and d.ORG_ID=e.ORG_ID
									and e.ORG_ID_REF=f.ORG_ID and f.ORG_ID_REF=g.ORG_ID
									$search_condition
						order by	$order_by
					";
		}elseif($DPISDB=="mysql"){
			$cmd = " 	select		distinct $select_list
						from		(
										(
											(
												(
													(
														PER_POSITION a
														inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
												) inner join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
											) inner join PER_ORG_ASS e on (d.ORG_ID=e.ORG_ID)
										) inner join PER_ORG_ASS f on (e.ORG_ID_REF=f.ORG_ID)
									) inner join PER_ORG_ASS g on (f.ORG_ID_REF=g.ORG_ID)
						$search_condition
						order by	$order_by
					";
		}
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = 0;
	$position_count = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :
					if($ORG_ID != $data[ORG_ID]){
						$ORG_ID = $data[ORG_ID];
						if($ORG_ID == ""){
							$ORG_NAME = "[ไม่ระบุสำนัก/กอง]";
						}else{
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
					if($ORG_ID_1 != $data[ORG_ID_1]){
						$ORG_ID_1 = $data[ORG_ID_1];
						if($ORG_ID_1 == ""){
							$ORG_NAME_1 = "[ไม่ระบุฝ่าย]";
						}else{
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
					if($ORG_ID_2 != $data[ORG_ID_2]){
						$ORG_ID_2 = $data[ORG_ID_2];
						if($ORG_ID_2 == ""){
							$ORG_NAME_2 = "[ไม่ระบุงาน]";
						}else{
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
						if($PV_CODE == ""){
							$PV_NAME = "[ไม่ระบุจังหวัด]";
						}else{
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
	
	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
?>