<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_select = "a.POS_ID";
		$position_join = "a.POS_ID=b.POS_ID";
		$position_no= "a.POS_NO";
		$line_table = "PER_LINE";
		$line_join = "a.PL_CODE=e.PL_CODE";
		$line_code = "a.PL_CODE";
		$line_name = "PL_NAME as PL_NAME, PL_SHORTNAME as PL_SHORTNAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
		$line_title= " สายงาน";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_select = "a.POEM_ID";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$position_no= "a.POEM_NO";
		$line_table = "PER_POS_NAME";
		$line_join = "a.PN_CODE=e.PN_CODE";
		$line_code = "a.PN_CODE";
		$line_name = "PN_NAME as PL_NAME";	
		$line_search_code=trim($search_pn_code);
		$line_search_name=trim($search_pn_name);
		$line_title= " ชื่อตำแหน่ง";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_select = "a.POEMS_ID";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$position_no= "a.POEMS_NO";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "a.EP_CODE=e.EP_CODE";
		$line_code = "a.EP_CODE";
		$line_name = "EP_NAME as PL_NAME";	
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);
		$line_title= " ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_select = "a.POT_ID";
		$position_join = "a.POT_ID=b.POT_ID";
		$position_no= "a.POT_NO";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "a.TP_CODE=e.TP_CODE";
		$line_code = "a.TP_CODE";
		$line_name = "TP_NAME as PL_NAME";	
		$line_search_code=trim($search_tp_code);
		$line_search_name=trim($search_tp_name);
		$line_title= " ชื่อตำแหน่ง";
	} // end if	

	if($list_type=="ALL") $RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
	if(!trim($RPTORD_LIST)){ 
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		elseif($list_type=="PER_COUNTRY" && trim($search_pv_code)!="") $RPTORD_LIST = "PROVINCE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE", "CO_LEVEL", "PROVINCE"); 
	//print_r($arr_rpt_order);

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
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "$line_code as PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= $line_code;

				$heading_name .= " $line_title";
				break;
			case "CO_LEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.CL_NAME, c.LEVEL_NO_MIN";
				
				if($order_by) $order_by .= ", ";
				$order_by .= "c.LEVEL_NO_MIN";

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
			$list_type_text .= "$line_search_name";
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
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "รายงานจำนวนตำแหน่ง จำแนกตามประเภทตำแหน่ง";
//	if($export_type=="report")	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0104";
	$orientation='L';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetFont($font,'',14);
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$heading_align = array('C','C','C','C','C','C');

	include ("rpt_R001004_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน

	function count_position($position_type, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;		
		global $select_org_structure, $DEPARTMENT_ID;
		global $position_table, $position_select;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		$search_condition = str_replace(" where ", " and ", $search_condition);

		//กำหนดประเภทตำแหน่งเพื่อหา จำนวนตำแหน่ง
		if($position_type == 1){ //==> ตำแหน่ง ทั่วไป
			$search_level = 'O';
		}elseif($position_type == 2){ //==> ตำแหน่ง วิชาการ
			$search_level = 'K';
		}elseif($position_type == 3){ //==> ตำแหน่ง อำนวยการ
			$search_level = 'D';
		}elseif($position_type == 4){ //==> ตำแหน่ง บริหาร
			$search_level = 'M';
		}

		if($DPISDB=="odbc"){
			$cmd = " select	 	count($position_select ) as count_position
					   from		(
										(
											$position_table a
											inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
									) inner join PER_TYPE d on (a.PT_CODE=d.PT_CODE)
					   where	(LEFT(trim(a.LEVEL_NO),1)='$search_level') and a.DEPARTMENT_ID=$DEPARTMENT_ID
									$search_condition ";
		}elseif($DPISDB=="oci8"){
				$cmd = " select		count($position_select ) as count_position
							 from			$position_table a, PER_ORG b, PER_TYPE d
							 where		a.ORG_ID=b.ORG_ID and a.PT_CODE=d.PT_CODE
												and SUBSTR(trim(a.LEVEL_NO),1,1)='$search_level' and a.DEPARTMENT_ID=$DEPARTMENT_ID
							$search_condition ";
		}elseif($DPISDB=="mysql"){
				$cmd = " select			count($position_select ) as count_position
					   from			(
										(
											$position_table a
											inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
									) inner join PER_TYPE d on (a.PT_CODE=d.PT_CODE)
					   where	(LEFT(trim(a.LEVEL_NO), 1)='$search_level') and a.DEPARTMENT_ID=$DEPARTMENT_ID
									$search_condition ";
		} // end if
		$db_dpis2->send_cmd($cmd);
		//$db_dpis2->show_error();
		//echo "<br>$cmd<br>";
		$data = $db_dpis2->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$count_position = $data[count_position] + 0;
	return $count_position;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $select_org_structure;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $CL_NAME, $PV_CODE, $line_code;
				
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
				case "LINE" :
					if($PL_CODE) $arr_addition_condition[] = "(trim($line_code) = '$PL_CODE')";
					else $arr_addition_condition[] = "(trim($line_code) = '$PL_CODE' or $line_code is null)";
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
				case "LINE" :
					$PL_CODE = -1;
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

	//แสดงรายชื่อหน่วยงานราชการ
		if($DPISDB=="odbc"){
			$cmd = " select			distinct g.ORG_SEQ_NO, g.ORG_ID as MINISTRY_ID, d.ORG_SEQ_NO, d.ORG_ID as DEPARTMENT_ID, $select_list
					   from			(
											(
												$position_table a 
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
										) inner join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
									) inner join PER_ORG g on (d.ORG_ID_REF=g.ORG_ID)
									$search_condition
					   order by		g.ORG_SEQ_NO, g.ORG_ID, d.ORG_SEQ_NO, d.ORG_ID, $order_by ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct g.ORG_SEQ_NO, g.ORG_ID as MINISTRY_ID, d.ORG_SEQ_NO, d.ORG_ID as DEPARTMENT_ID, $select_list
					   from			$position_table a, PER_ORG b, PER_ORG d, PER_ORG g
					   where		a.ORG_ID=b.ORG_ID and b.ORG_ID_REF=d.ORG_ID and d.ORG_ID_REF=g.ORG_ID
									$search_condition
					   order by		g.ORG_SEQ_NO, g.ORG_ID, d.ORG_SEQ_NO, d.ORG_ID, $order_by ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct g.ORG_SEQ_NO, g.ORG_ID as MINISTRY_ID, d.ORG_SEQ_NO, d.ORG_ID as DEPARTMENT_ID, $select_list
					   from			(
											(
												$position_table a 
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
										) inner join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
									) inner join PER_ORG g on d.ORG_ID_REF=g.ORG_ID)
									$search_condition
					   order by		g.ORG_SEQ_NO, g.ORG_ID, d.ORG_SEQ_NO, d.ORG_ID, $order_by ";
		}
	
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	//echo $cmd."<hr>";
	$data_count = 0;
	initialize_parameter(0);
	$DEPARTMENT_ID = -1;
	while($data = $db_dpis->get_array()){

		if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){	
			if($data_count > 1){
				$GRAND_TOTAL_1[$DEPARTMENT_ID] = count_position(1, $search_condition, ""); //ทั่วไป
				$GRAND_TOTAL_2[$DEPARTMENT_ID] = count_position(2, $search_condition, ""); //วิชาการ
				$GRAND_TOTAL_3[$DEPARTMENT_ID] = count_position(3, $search_condition, ""); //อำนวยการ
				$GRAND_TOTAL_4[$DEPARTMENT_ID] = count_position(4, $search_condition, ""); //บริหาร
				
				$GRAND_TOTAL[$DEPARTMENT_ID] = $GRAND_TOTAL_1[$DEPARTMENT_ID] + $GRAND_TOTAL_2[$DEPARTMENT_ID] + $GRAND_TOTAL_3[$DEPARTMENT_ID] + $GRAND_TOTAL_4[$DEPARTMENT_ID]; //รวม
			} // end if
			
			$MINISTRY_ID = $data[MINISTRY_ID];
			$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$MINISTRY_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$MINISTRY_NAME = $data2[ORG_NAME];

			$DEPARTMENT_ID = $data[DEPARTMENT_ID];			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_NAME = $data2[ORG_NAME];
			$DEPARTMENT_ORG_SHORT = $data2[ORG_SHORT];

			$arr_content[$data_count][type] = "DEPARTMENT";
			$arr_content[$data_count][id] = $DEPARTMENT_ID;
			$arr_content[$data_count][ref_name] = $MINISTRY_NAME;
			$arr_content[$data_count][name] = $DEPARTMENT_NAME;
			$arr_content[$data_count][short_name] = $DEPARTMENT_ORG_SHORT;
			$arr_content[$data_count][count_1] = count_position(1, $search_condition, ""); //ทั่วไป
			$arr_content[$data_count][count_2] = count_position(2, $search_condition, ""); //วิชาการ
			$arr_content[$data_count][count_3] = count_position(3, $search_condition, ""); //อำนวยการ
			$arr_content[$data_count][count_4] = count_position(4, $search_condition, ""); //บริหาร
			$data_count++;
		} // end if
		
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :
					if($ORG_ID != trim($data[ORG_ID])){
						$ORG_ID = trim($data[ORG_ID]);
						if($ORG_ID != ""){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
							$ORG_SHORT = $data2[ORG_SHORT];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						$arr_content[$data_count][short_name] = $ORG_SHORT;
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_position(4, $search_condition, $addition_condition);
						if($rpt_order_index == 0){
							$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3[$DEPARTMENT_ID] += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4[$DEPARTMENT_ID] += $arr_content[$data_count][count_4];
						} // end if

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "ORG_1" :
					if($ORG_ID_1 != trim($data[ORG_ID_1])){
						$ORG_ID_1 = trim($data[ORG_ID_1]);
						if($ORG_ID_1 != ""){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_1 ";
							$db_dpis2->send_cmd($cmd);
//						$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_1 = $data2[ORG_NAME];
							$ORG_SHORT_1 = $data2[ORG_SHORT];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_1";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_1;
						$arr_content[$data_count][short_name] = $ORG_SHORT_1;
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_position(4, $search_condition, $addition_condition);
						if($rpt_order_index == 0){
							$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3[$DEPARTMENT_ID] += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4[$DEPARTMENT_ID] += $arr_content[$data_count][count_4];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "ORG_2" :
					if($ORG_ID_2 != trim($data[ORG_ID_2])){
						$ORG_ID_2 = trim($data[ORG_ID_2]);
						if($ORG_ID_2 != ""){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_2 ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_2 = $data2[ORG_NAME];
							$ORG_SHORT_2 = $data2[ORG_SHORT];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_2";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_2;
						$arr_content[$data_count][short_name] = $ORG_SHORT_2;
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_position(4, $search_condition, $addition_condition);
						if($rpt_order_index == 0){
							$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3[$DEPARTMENT_ID] += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4[$DEPARTMENT_ID] += $arr_content[$data_count][count_4];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "LINE" :
					if($PL_CODE != trim($data[PL_CODE])){
						$PL_CODE = trim($data[PL_CODE]);
						if($PL_CODE != ""){
							$cmd = " select $line_name from $line_table a where trim($line_code)='$PL_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PL_NAME = trim($data2[PL_SHORTNAME])?$data2[PL_SHORTNAME]:$data2[PL_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "LINE";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_NAME;
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_position(4, $search_condition, $addition_condition);
						if($rpt_order_index == 0){
							$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3[$DEPARTMENT_ID] += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4[$DEPARTMENT_ID] += $arr_content[$data_count][count_4];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "CO_LEVEL" :
					if($CL_NAME != trim($data[CL_NAME])){
						$CL_NAME = trim($data[CL_NAME]);

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "CO_LEVEL";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . (trim($CL_NAME)?"ระดับ $CL_NAME":"[ไม่ระบุช่วงระดับตำแหน่ง]");
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_position(4, $search_condition, $addition_condition);
						if($rpt_order_index == 0){
							$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3[$DEPARTMENT_ID] += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4[$DEPARTMENT_ID] += $arr_content[$data_count][count_4];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
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
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_position(4, $search_condition, $addition_condition);
						if($rpt_order_index == 0){
							$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3[$DEPARTMENT_ID] += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4[$DEPARTMENT_ID] += $arr_content[$data_count][count_4];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for
	} // end while
	
	$GRAND_TOTAL_1[$DEPARTMENT_ID] = count_position(1, $search_condition, "");
	$GRAND_TOTAL_2[$DEPARTMENT_ID] = count_position(2, $search_condition, "");
	$GRAND_TOTAL_3[$DEPARTMENT_ID] = count_position(3, $search_condition, "");
	$GRAND_TOTAL_4[$DEPARTMENT_ID] = count_position(4, $search_condition, "");
	
	$GRAND_TOTAL[$DEPARTMENT_ID] = $GRAND_TOTAL_1[$DEPARTMENT_ID] + $GRAND_TOTAL_2[$DEPARTMENT_ID] + $GRAND_TOTAL_3[$DEPARTMENT_ID] + $GRAND_TOTAL_4[$DEPARTMENT_ID];

//	echo "<pre>"; print_r($arr_content); echo "</pre>";  

//new**********************************************
	if($export_type=="report"){
		$pdf->AutoPageBreak = false; 
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
		if (!$result) echo "****** error ****** on open table for $table<br>";
	//	print_header();
		
      if($count_data){
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$COUNT_1 = $arr_content[$data_count][count_1];
			$COUNT_2 = $arr_content[$data_count][count_2];
			$COUNT_3 = $arr_content[$data_count][count_3];
			$COUNT_4 = $arr_content[$data_count][count_4];
			$COUNT_TOTAL = $COUNT_1 + $COUNT_2 + $COUNT_3 + $COUNT_4;
			
			$PERCENT_1 = $PERCENT_2 = $PERCENT_3 = $PERCENT_4 = $PERCENT_TOTAL = 0;
			if($COUNT_TOTAL){ 
				$PERCENT_1 = ($COUNT_1 / $COUNT_TOTAL) * 100;
				$PERCENT_2 = ($COUNT_2 / $COUNT_TOTAL) * 100;
				$PERCENT_3 = ($COUNT_3 / $COUNT_TOTAL) * 100;
				$PERCENT_4 = ($COUNT_4 / $COUNT_TOTAL) * 100;
			} // end if
			if($GRAND_TOTAL[$DEPARTMENT_ID]) $PERCENT_TOTAL = ($COUNT_TOTAL / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;


			if($REPORT_ORDER == "DEPARTMENT"){
				if($data_count > 0){
					$PERCENT_TOTAL_1 = $PERCENT_TOTAL_2 = $PERCENT_TOTAL_3 = $PERCENT_TOTAL_4 = 0;
					if($GRAND_TOTAL[$DEPARTMENT_ID]){ 
						$PERCENT_TOTAL_1 = ($GRAND_TOTAL_1[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;
						$PERCENT_TOTAL_2 = ($GRAND_TOTAL_2[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;
						$PERCENT_TOTAL_3 = ($GRAND_TOTAL_3[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;
						$PERCENT_TOTAL_4 = ($GRAND_TOTAL_4[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;
					} // end if
					$PERCENT_TOTAL = ($GRAND_TOTAL[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;

					$arr_data = (array) null;
					$arr_data[] = "รวม";
					$arr_data[] = $GRAND_TOTAL_1[$DEPARTMENT_ID];
					$arr_data[] = $GRAND_TOTAL_2[$DEPARTMENT_ID];
					$arr_data[] = $GRAND_TOTAL_3[$DEPARTMENT_ID];
					$arr_data[] = $GRAND_TOTAL_4[$DEPARTMENT_ID];
					$arr_data[] = $GRAND_TOTAL[$DEPARTMENT_ID];

					$data_align = array("L", "C", "C", "C", "C", "C");
	
					$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "000000", "");
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				} // end if
					
				$DEPARTMENT_ID = $arr_content[$data_count][id];
				//$REF_NAME = $arr_content[$data_count][ref_name];		
				$NAME = $arr_content[$data_count][name];							

				$arr_data = (array) null;
				$arr_data[] = $NAME;
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";

				$data_align = array("L", "C", "C", "C", "C", "C");

				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "000000", "");
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			}else{
				$arr_data = (array) null;
				$arr_data[] = $NAME;
				$arr_data[] = $COUNT_1;
				$arr_data[] = $COUNT_2;
				$arr_data[] = $COUNT_3;
				$arr_data[] = $COUNT_4;
				$arr_data[] = $COUNT_TOTAL;

				$data_align = array("L", "C", "C", "C", "C", "C");

				if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
					$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "000000", "");
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				}else{
					$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				} // end if
			} // end if
		} // end for
			
		//รวมของแต่ละหน่วยงาน				
		$PERCENT_TOTAL_1 = $PERCENT_TOTAL_2 = $PERCENT_TOTAL_3 =  $PERCENT_TOTAL_4 = 0;
		if($GRAND_TOTAL[$DEPARTMENT_ID]){ 
			$PERCENT_TOTAL_1 = ($GRAND_TOTAL_1[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;
			$PERCENT_TOTAL_2 = ($GRAND_TOTAL_2[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;
			$PERCENT_TOTAL_3 = ($GRAND_TOTAL_3[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;
			$PERCENT_TOTAL_4 = ($GRAND_TOTAL_4[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;
		} // end if
		$PERCENT_TOTAL = ($GRAND_TOTAL[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;

		$arr_data = (array) null;
		$arr_data[] = "รวม";
		$arr_data[] = $GRAND_TOTAL_1[$DEPARTMENT_ID];
		$arr_data[] = $GRAND_TOTAL_2[$DEPARTMENT_ID];
		$arr_data[] = $GRAND_TOTAL_3[$DEPARTMENT_ID];
		$arr_data[] = $GRAND_TOTAL_4[$DEPARTMENT_ID];
		$arr_data[] = $GRAND_TOTAL[$DEPARTMENT_ID];

		$data_align = array("L", "C", "C", "C", "C", "C");

		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		if(count($GRAND_TOTAL) > 1){
			$PERCENT_TOTAL_1 = $PERCENT_TOTAL_2 = $PERCENT_TOTAL_3 = $PERCENT_TOTAL_4 = 0;
			if(array_sum($GRAND_TOTAL)){ 
				$PERCENT_TOTAL_1 = (array_sum($GRAND_TOTAL_1) / array_sum($GRAND_TOTAL)) * 100;
				$PERCENT_TOTAL_2 = (array_sum($GRAND_TOTAL_2) / array_sum($GRAND_TOTAL)) * 100;
				$PERCENT_TOTAL_3 = (array_sum($GRAND_TOTAL_3) / array_sum($GRAND_TOTAL)) * 100;
				$PERCENT_TOTAL_4 = (array_sum($GRAND_TOTAL_4) / array_sum($GRAND_TOTAL)) * 100;
			} // end if
			$PERCENT_TOTAL = (array_sum($GRAND_TOTAL) / array_sum($GRAND_TOTAL)) * 100;

			$arr_data = (array) null;
			$arr_data[] = "รวมทั้งสิ้น";
			$arr_data[] = array_sum($GRAND_TOTAL_1);
			$arr_data[] = array_sum($GRAND_TOTAL_2);
			$arr_data[] = array_sum($GRAND_TOTAL_3);
			$arr_data[] = array_sum($GRAND_TOTAL_4);
			$arr_data[] = array_sum($GRAND_TOTAL);

			$data_align = array("L", "C", "C", "C", "C", "C");

			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "FF0000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end if
	  }else{//if($count_data){
			$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	  } // end if
	  $pdf->close_tab(""); 

	  $pdf->close();
	  $pdf->Output();	
	}else if($export_type=="graph"){//if($export_type=="report"){

		$arr_content_map = array("name","count_1","count_2","count_3","count_4","total"); // ชื่อ column ใน content ที่ map ให้ตรงกับ head pdf
		$arr_series_caption = array("","ทั่วไป","วิชาการ","อำนวยการ","บริหาร",""); // หัวที่ต้องการเฉพาะออก กราฟ map ให้ตรงกับ head ของ pdf
		
//		echo "<pre>"; print_r($arr_content); echo "</pre>";
		$arr_content_key = array_keys($arr_content[0]);	//print_r($arr_content_key);
		$arr_categories = array();
		$arr_series_caption_list = array(); 
		$f_first = true;
		for($i=0;$i<count($arr_content);$i++){
			$arr_categories[$i] = trim($short_name=="y")?(trim($arr_content[$i][short_name])?$arr_content[$i][short_name]:$arr_content[$i][name]):$arr_content[$i][name];
//			echo "$i  categories=".$arr_categories[$i]."<br>";
			$cntseq=0;
			for($j = 0; $j < count($arr_content_map); $j++) {
				if ($arr_column_sel[$arr_column_map[$j]]==1 && strpos($arr_content_map[$arr_column_map[$j]],"count")!==false) {
					$arr_series_caption_data[$cntseq][] = $arr_content[$i][$arr_content_map[$arr_column_map[$j]]];
//					echo "data $i, $j, map=".$arr_content_map[$arr_column_map[$j]].", content=".$arr_content[$i][$arr_content_map[$arr_column_map[$j]]]."<br>";
					if ($f_first) $arr_series_caption_list[] = $arr_series_caption[$arr_column_map[$j]];
//					if ($f_first) echo "caption (j:$j)=".$arr_series_caption[$arr_content_map[$arr_column_map[$j]]]."  contentname=".$arr_content_map[$arr_column_map[$j]]."  mapseq=".$arr_column_map[$j]."<br>";
					$cntseq++;
				}
			}
			$f_first=false;	// check สำหรับรอบแรกเท่านั้น
//			$arr_series_caption_data[0][] = $arr_content[$i][count_1];
//			$arr_series_caption_data[1][] = $arr_content[$i][count_2];
//			$arr_series_caption_data[2][] = $arr_content[$i][count_3];
//			$arr_series_caption_data[3][] = $arr_content[$i][count_4];
		}
		$series_caption_list = implode(";",$arr_series_caption_list);
		$arr_series_list[0] = implode(";", $arr_series_caption_data[0])."";
		$arr_series_list[1] = implode(";", $arr_series_caption_data[1])."";
		$arr_series_list[2] = implode(";", $arr_series_caption_data[2])."";
		$arr_series_list[3] = implode(";", $arr_series_caption_data[3])."";
//		echo "0-".$arr_series_list[0]."  1-".$arr_series_list[1]."  2-".$arr_series_list[2]." ===$series_caption_list<br>";
	
		$chart_title = $report_title;
		$chart_subtitle = $company_name;
		if(!$setWidth){ $setWidth = "$GRAPH_WIDE";}else{ $setWidth = "800";}
		if(!$setHeight){ $setHeight = "$GRAPH_HIGH";}else{$setHeight = "600";}
		$selectedFormat = "SWF";
		$categories_list = implode(";", $arr_categories)."";
		if(strtolower($graph_type)=="pie"){
			$series_list = $GRAND_TOTAL_1[$DEPARTMENT_ID].";".$GRAND_TOTAL_2[$DEPARTMENT_ID].";".$GRAND_TOTAL_3[$DEPARTMENT_ID].";".$GRAND_TOTAL_4[$DEPARTMENT_ID];
		}else{
			$series_list = implode("|", $arr_series_list);
		}
		switch( strtolower($graph_type) ){
			case "column" :
				$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Column/style/column.scs";
				break;
			case "bar" :
				$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Bar/style/bar.scs";
				break;
			case "line" :
				$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Line/style/line.scs";
				break;
			case "pie" :
				$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Pie/style/pie.scs";
				break;
		} //switch( strtolower($graph_type) ){
	}//}else if($export_type=="graph"){
?>