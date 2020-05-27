<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php"); 

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_select = "a.POS_ID";
		$position_join = "a.POS_ID=d.POS_ID";
		$position_status = "a.POS_STATUS";
		$position2 = "a.POS_GET_DATE is not null";
		$position3 = "(trim(a.POS_GET_DATE)='' or a.POS_GET_DATE is null)";
		$line_table = "PER_LINE";
		$line_join = "a.PL_CODE=f.PL_CODE";
		$line_code = "a.PL_CODE";
		$line_name = "PL_NAME";
		$line_short_name = "PL_SHORTNAME";
		$line_seq="f.PL_SEQ_NO";		
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
		$line_title= " สายงาน";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_select = "a.POEM_ID";
		$position_join = "a.POEM_ID=d.POEM_ID";
		$position_status = "a.POEM_STATUS";
		$position2 = "a.POEM_STATUS=1";
		$position3 = "none";
		$line_table = "PER_POS_NAME";
		$line_join = "a.PN_CODE=f.PN_CODE";
		$line_code = "a.PN_CODE";
		$line_name = "PN_NAME";	
		$line_seq="f.POEM_SEQ_NO";
		$line_search_code=trim($search_pn_code);
		$line_search_name=trim($search_pn_name);
		$line_title= " ชื่อตำแหน่ง";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_select = "a.POEMS_ID";
		$position_join = "a.POEMS_ID=d.POEMS_ID";
		$position_status = "a.POEM_STATUS";
		$position2 = "a.POEM_STATUS=1";
		$position3 = "none";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "a.EP_CODE=f.EP_CODE";
		$line_code = "a.EP_CODE";
		$line_name = "EP_NAME";	
		$line_seq="f.POEMS_SEQ_NO";
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);
		$line_title= " ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_select = "a.POT_ID";
		$position_join = "a.POT_ID=d.POT_ID";
		$position_status = "a.POT_STATUS";
		$position2 = "a.POT_STATUS=1";
		$position3 = "none";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "a.TP_CODE=f.TP_CODE";
		$line_code = "a.TP_CODE";
		$line_name = "TP_NAME";	
		$line_seq="f.POT_SEQ_NO";
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
//	print_r($arr_rpt_order);

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){ 
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				//if($select_org_structure==0) $select_list .= "b.ORG_ID_REF as MINISTRY_ID";	
				//elseif($select_org_structure==1) $select_list .= "a.ORG_ID as MINISTRY_ID";	
				$select_list .= "g.ORG_ID as MINISTRY_ID";	
				
				if($order_by) $order_by .= ", ";
				//if($select_org_structure==0) $order_by .= "b.ORG_ID_REF";
				//elseif($select_org_structure==1)  $order_by .= "a.ORG_ID";
				 $order_by .= "g.ORG_ID";

				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				//if($select_org_structure==0) $select_list .= "d.ORG_ID";
				//elseif($select_org_structure==1)  $select_list .= "d.DEPARTMENT_ID";
				$select_list .= "d.ORG_ID as DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				//if($select_org_structure==0) $order_by .= "d.ORG_ID";
				//else if($select_org_structure==1) $order_by .= "d.DEPARTMENT_ID"; 
				$order_by .= "d.ORG_ID";

				$heading_name .= " $DEPARTMENT_TITLE";
				break;
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
				$select_list .= "$line_code as PL_CODE, $line_seq";

				if($order_by) $order_by .= ", ";
				$order_by .= "$line_code, $line_seq";

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
	$arr_search_condition[] = "($position_status=1)";

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

		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
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

		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(b.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
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
		if($line_search_code){
			$arr_search_condition[] = "(trim($line_code)='$line_search_code')";
			$list_type_text .= "$line_search_name";
		}
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
	$company_name = "รูปแบบการออกรายงาน : โครงสร้างตามกฎหมาย - " ."$list_type_text";
	$report_title = "จำนวนตำแหน่งจำแนกตามสถานภาพและลักษณะของตำแหน่ง";
//	if($export_type=="report")	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0101";
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

	$heading_width[0] = "127";
	$heading_width[1] = "20";
	$heading_width[2] = "20";
	$heading_width[3] = "20";
	$heading_width[4] = "20";
	$heading_width[5] = "20";
	$heading_width[6] = "20";
	$heading_width[7] = "20";
	$heading_width[8] = "20";
	//newformat*************************************************************
	 $heading_text[0] = "$heading_name|";
	$heading_text[1] = "<**1**>ถือครอง|ตำแหน่ง";
	$heading_text[2] = "<**1**>ถือครอง|ร้อยละ";
	$heading_text[3] = "<**2**>ว่างมีเงิน|ตำแหน่ง";
	$heading_text[4] = "<**2**>ว่างมีเงิน|ร้อยละ";
	$heading_text[5] = "<**3**>ว่างไม่มีเงิน|ตำแหน่ง";
	$heading_text[6] = "<**3**>ว่างไม่มีเงิน|ร้อยละ";
	$heading_text[7] = "<**4**>รวม|ตำแหน่ง";
	$heading_text[8] = "<**4**>รวม|ร้อยละ";
		
	$heading_align = array('C','C','C','C','C','C','C','C','C');	

	function count_position($position_type, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $DEPARTMENT_ID;
		global $search_per_type, $position_table, $position_select, $position_join, $position_status, $position2, $position3;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		if($position_type == 1){
			// ตำแหน่งมีผู้ถือครอง		
			$search_condition = str_replace(" where ", " and ", $search_condition);
			if($DPISDB=="odbc"){
				$cmd = " select			count($position_select) as count_position
						   from		(
												$position_table a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)														
										) inner join PER_PERSONAL d on ($position_join)
						   where		d.PER_STATUS=1 and d.PER_TYPE=$search_per_type  
										$search_condition
						   group by		$position_select ";
			}elseif($DPISDB=="oci8"){				
				$cmd = " select			count($position_select) as count_position
						   from			$position_table a, PER_ORG b, PER_PERSONAL d
						   where		a.ORG_ID=b.ORG_ID and $position_join and d.PER_STATUS=1 and d.PER_TYPE=$search_per_type
										$search_condition
						   group by		$position_select ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count($position_select) as count_position
						   from		(
												$position_table a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)														
										) inner join PER_PERSONAL d on ($position_join)
						   where		d.PER_STATUS=1 and d.PER_TYPE=$search_per_type 
										$search_condition
						   group by		$position_select ";
			} // end if
		}elseif($position_type == 2){
			// ตำแหน่งว่างมีเงิน
			$cmd = " select 		count(d.PER_ID) as count_person, $position_select 
							 from 			$position_table a, PER_PERSONAL d 
							 where 		$position_join and d.PER_STATUS=1 and d.PER_TYPE=$search_per_type and $position_status=1 
							 group by 	$position_select 
							 having 		count(d.PER_ID) > 0 ";
			$db_dpis2->send_cmd($cmd);
			while($data = $db_dpis2->get_array()) $arr_exclude[] = $data[POS_ID];
			
			$search_condition = str_replace(" where ", " and ", $search_condition);
//				if(count($arr_exclude)) $search_condition .= " and $position_select not in (". implode(",", $arr_exclude) .")";
			if(count($arr_exclude)) $search_condition .= " and $position_select not in ( select distinct $position_select from $position_table a, PER_PERSONAL d where $position_join and d.PER_STATUS=1 and d.PER_TYPE=$search_per_type and $position_status=1 )";

			if($DPISDB=="odbc"){
				$cmd = " select			count($position_select) as count_position
								 from				$position_table a
														inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
								 where		$position2 
													$search_condition
								 group by	$position_select ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select			count($position_select) as count_position
								 from			$position_table a, PER_ORG b
								 where		a.ORG_ID=b.ORG_ID and $position2
													$search_condition
								 group by	$position_select ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count($position_select) as count_position
								 from				$position_table a
														inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
								 where		$position2 
													$search_condition
								 group by	$position_select ";
			} // end if	
		}elseif($position_type == 3){
			// ตำแหน่งว่างไม่มีเงิน
			$cmd = " select 		count(d.PER_ID) as count_person, $position_select 
							 from 			$position_table a, PER_PERSONAL d 
							 where 		$position_join and d.PER_STATUS=1 and d.PER_TYPE=$search_per_type and $position_status=1 
							 group by 	$position_select 
							 having 		count(d.PER_ID) > 0 ";
			$db_dpis2->send_cmd($cmd);
			while($data = $db_dpis2->get_array()) $arr_exclude[] = $data[POS_ID];			
			
			$search_condition = str_replace(" where ", " and ", $search_condition);	
//				if(count($arr_exclude)) $search_condition .= " and $position_select not in (". implode(",", $arr_exclude) .")";
			if(count($arr_exclude)) $search_condition .= " and $position_select not in ( select distinct $position_select from $position_table a, PER_PERSONAL d where $position_join and d.PER_STATUS=1 and d.PER_TYPE=$search_per_type and $position_status=1 )";

			if($DPISDB=="odbc"){
				$cmd = " select			count($position_select) as count_position
								 from				$position_table a
														inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
								 where		$position3
													$search_condition
								 group by	$position_select ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select			count($position_select) as count_position
								 from			$position_table a, PER_ORG b
								 where		a.ORG_ID=b.ORG_ID and $position3
													$search_condition
								 group by	$position_select ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count($position_select) as count_position
								 from				$position_table a
														inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
								 where		$position3
													$search_condition
								 group by	$position_select ";
			} // end if
		} // end if

		$count_position = $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error(); 
		$count_position = 0;
		while ($data = $db_dpis2->get_array()) {
			$data = array_change_key_case($data, CASE_LOWER);
			$count_position = $count_position+$data[count_position];
		}
//echo "$count_position :: $position_type---- $cmd<br>";
		return $count_position;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $line_code;
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

	//แสดงรายชื่อหน่วยงาน
	if($DPISDB=="odbc"){
			$cmd = " select			distinct g.ORG_SEQ_NO, d.ORG_SEQ_NO, $select_list
					   from	 (
									(
										(
												$position_table a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
										) inner join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
									) left join $line_table f on ($line_join)
								) inner join PER_ORG g on (d.ORG_ID_REF=g.ORG_ID)
									$search_condition
					   order by		g.ORG_SEQ_NO, d.ORG_SEQ_NO, $order_by ";
	}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct g.ORG_SEQ_NO, d.ORG_SEQ_NO, $select_list
					   from			$position_table a, PER_ORG b, PER_ORG d, $line_table f, PER_ORG g
					   where		a.ORG_ID=b.ORG_ID and b.ORG_ID_REF=d.ORG_ID and d.ORG_ID_REF=g.ORG_ID and $line_join(+)
									$search_condition
					   order by		g.ORG_SEQ_NO, d.ORG_SEQ_NO, $order_by ";
	}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct g.ORG_SEQ_NO, d.ORG_SEQ_NO, $select_list
					   from	 (
									(
										(
												$position_table a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
										) inner join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
									) left join $line_table f on ($line_join)
								) inner join PER_ORG g on (d.ORG_ID_REF=g.ORG_ID)
									$search_condition
					   order by		g.ORG_SEQ_NO, d.ORG_SEQ_NO, $order_by ";
	}
	$count_data = $db_dpis->send_cmd($cmd);
//		echo "<br>$cmd<br>";
	$db_dpis->show_error();
	$data_count = 0;
	initialize_parameter(0);
	$MINISTRY_ID = -1;		$DEPARTMENT_ID_INI = -1;
	while($data = $db_dpis->get_array()){
		$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		if($MINISTRY_ID != $data[MINISTRY_ID]){
			$MINISTRY_ID =  trim($data[MINISTRY_ID]);
			$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$MINISTRY_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$MINISTRY_NAME = $data2[ORG_NAME];
			$MINISTRY_SHORT = $data2[ORG_SHORT];

			//if($rpt_order_index == 0){
				$GRAND_TOTAL1[$DEPARTMENT_ID] = count_position(1, $search_condition, "");
				$GRAND_TOTAL2[$DEPARTMENT_ID] = count_position(2, $search_condition, "");
				$GRAND_TOTAL3[$DEPARTMENT_ID] = count_position(3, $search_condition, "");
			//}
			$GRAND_TOTAL_MINISTRY[$DEPARTMENT_ID] = ($GRAND_TOTAL1[$DEPARTMENT_ID] + $GRAND_TOTAL2[$DEPARTMENT_ID] + $GRAND_TOTAL3[$DEPARTMENT_ID]);
			$rpt_order_index_1 = 0;
			$arr_content[$data_count][type] = "MINISTRY";
			$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index_1 * 5)) .$MINISTRY_NAME;
			$arr_content[$data_count][short_name] = str_repeat(" ", ($rpt_order_index_1 * 5)) .$MINISTRY_SHORT;
			$arr_content[$data_count][id] = $DEPARTMENT_ID;
			$arr_content[$data_count][count_1] = $GRAND_TOTAL1[$DEPARTMENT_ID];
			$arr_content[$data_count][count_2] = $GRAND_TOTAL2[$DEPARTMENT_ID];
			$arr_content[$data_count][count_3] = $GRAND_TOTAL3[$DEPARTMENT_ID];

		$data_count++;
		} // end if\
		if($DEPARTMENT_ID_INI != $DEPARTMENT_ID){
			$DEPARTMENT_ID_INI = trim($DEPARTMENT_ID);
			//if($rpt_order_index == 0){
				$GRAND_TOTAL1[$DEPARTMENT_ID] = count_position(1, $search_condition, "");
				$GRAND_TOTAL2[$DEPARTMENT_ID] = count_position(2, $search_condition, "");
				$GRAND_TOTAL3[$DEPARTMENT_ID] = count_position(3, $search_condition, "");
			//}
			$GRAND_TOTAL[$DEPARTMENT_ID] = ($GRAND_TOTAL1[$DEPARTMENT_ID] + $GRAND_TOTAL2[$DEPARTMENT_ID] + $GRAND_TOTAL3[$DEPARTMENT_ID]);
	
			$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_NAME = $data2[ORG_NAME];
			$DEPARTMENT_SHORT = $data2[ORG_SHORT];

			$rpt_order_index_1 = 1;
			$arr_content[$data_count][type] = "DEPARTMENT";
			$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index_1 * 5)) .$DEPARTMENT_NAME;
			$arr_content[$data_count][short_name] = str_repeat(" ", ($rpt_order_index_1 * 5)) .$DEPARTMENT_SHORT;
			$arr_content[$data_count][id] = $DEPARTMENT_ID;
			$arr_content[$data_count][count_1] = $GRAND_TOTAL1[$DEPARTMENT_ID];
			$arr_content[$data_count][count_2] = $GRAND_TOTAL2[$DEPARTMENT_ID];
			$arr_content[$data_count][count_3] = $GRAND_TOTAL3[$DEPARTMENT_ID];

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
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) .$ORG_NAME;
						$arr_content[$data_count][short_name] = str_repeat(" ", ($rpt_order_index * 5)) .$ORG_SHORT;
						$arr_content[$data_count][id] = $DEPARTMENT_ID;
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						if($rpt_order_index == 0){
							$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3[$DEPARTMENT_ID] += $arr_content[$data_count][count_3];
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
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_1 = $data2[ORG_NAME];
							$ORG_SHORT_1 = $data2[ORG_SHORT];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_1";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_1;
						$arr_content[$data_count][short_name] = $ORG_SHORT_1;
						$arr_content[$data_count][id] = $DEPARTMENT_ID;
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						if($rpt_order_index == 0){
							$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3[$DEPARTMENT_ID] += $arr_content[$data_count][count_3];
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
						 $arr_content[$data_count][id] = $DEPARTMENT_ID;
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						if($rpt_order_index == 0){
							$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3[$DEPARTMENT_ID] += $arr_content[$data_count][count_3];
							} //if($rpt_order_index == 0){
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "LINE" :
					if($PL_CODE != trim($data[PL_CODE])){
						$PL_CODE = trim($data[PL_CODE]);
						if($PL_CODE != ""){
							if(trim($line_short_name))	$line_name = $line_name.",".$line_short_name;
							$cmd = " select $line_name from $line_table a where trim($line_code)='$PL_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PL_NAME = $data2[$line_name];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "LINE";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_NAME;
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][id] = $DEPARTMENT_ID;
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						if($rpt_order_index == 0){
							$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3[$DEPARTMENT_ID] += $arr_content[$data_count][count_3];
							} //if($rpt_order_index == 0){
			
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
						$arr_content[$data_count][id] = $DEPARTMENT_ID;
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						if($rpt_order_index == 0){
							$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3[$DEPARTMENT_ID] += $arr_content[$data_count][count_3];
							} //if($rpt_order_index == 0){
			
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
						$arr_content[$data_count][id] = $DEPARTMENT_ID;
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						if($rpt_order_index == 0){
							$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3[$DEPARTMENT_ID] += $arr_content[$data_count][count_3];
							} //if($rpt_order_index == 0){
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for
} // end while

  //new************************************
if($export_type=="report"){	                                    
	if($count_data){
			$pdf->AutoPageBreak = false;
			$head_text1 = implode(",", $heading_text);
			$head_width1 = implode(",", $heading_width);
			$head_align1 = implode(",", $heading_align);
	//		echo "$head_text1<br>";
			$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
			if (!$result) echo "****** error ****** on open table for $table<br>";
//		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];	
			$DEPARTMENT_ID = $arr_content[$data_count][id];	

			if($REPORT_ORDER == "MINISTRY" || $REPORT_ORDER == "DEPARTMENT"){ 
				$COUNT_1 = $arr_content[$data_count][count_1];
				$COUNT_2 = $arr_content[$data_count][count_2];
				$COUNT_3 = $arr_content[$data_count][count_3];

				if($DEPARTMENT_ID!=""){	
					$PERCENT_TOTAL1 = $PERCENT_TOTAL2 = $PERCENT_TOTAL3 =  0;
					if($GRAND_TOTAL[$DEPARTMENT_ID]){ 
						$PERCENT_TOTAL1 = ($GRAND_TOTAL1[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;
						$PERCENT_TOTAL2 = ($GRAND_TOTAL2[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;
						$PERCENT_TOTAL3 = ($GRAND_TOTAL3[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;
						$GRAND_TOTAL[$DEPARTMENT_ID] = ($GRAND_TOTAL1[$DEPARTMENT_ID]+$GRAND_TOTAL2[$DEPARTMENT_ID]+$GRAND_TOTAL3[$DEPARTMENT_ID]); //MMMMM
						$PERCENT_TOTAL = ($GRAND_TOTAL[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;
					} // end if
				}
			}else{
				$COUNT_1 = $arr_content[$data_count][count_1];
				$COUNT_2 = $arr_content[$data_count][count_2];
				$COUNT_3 = $arr_content[$data_count][count_3];
			}
			$COUNT_TOTAL = ($COUNT_1 + $COUNT_2 + $COUNT_3);
			$PERCENT_1 = $PERCENT_2 = $PERCENT_3 = $PERCENT = 0;
			if($COUNT_TOTAL){ 
				$PERCENT_1 = ($COUNT_1 / $COUNT_TOTAL) * 100;
				$PERCENT_2 = ($COUNT_2 / $COUNT_TOTAL) * 100;
				$PERCENT_3 = ($COUNT_3 / $COUNT_TOTAL) * 100;
				if($GRAND_TOTAL[$DEPARTMENT_ID]) $PERCENT = ($COUNT_TOTAL / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;
			} // end if
			//-------------------------------------------------------------------------------------------------------
		
			if($REPORT_ORDER == "MINISTRY" || $REPORT_ORDER == "DEPARTMENT"){ 
						$border = "";
						if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
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
						$pdf->Cell($heading_width[1], 7, ($COUNT_1?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_1)):number_format($COUNT_1)):"-"), $border, 0, 'R', 0);
						$pdf->Cell($heading_width[2], 7, ($PERCENT_1?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_1,2)):number_format($PERCENT_1, 2)):"-"), $border, 0, 'R', 0);
						$pdf->Cell($heading_width[3], 7, ($COUNT_2?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_2)):number_format($COUNT_2)):"-"), $border, 0, 'R', 0);
						$pdf->Cell($heading_width[4], 7, ($PERCENT_2?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_2,2)):number_format($PERCENT_2, 2)):"-"), $border, 0, 'R', 0);
						$pdf->Cell($heading_width[5], 7, ($COUNT_3?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_3)):number_format($COUNT_3)):"-"), $border, 0, 'R', 0);
						$pdf->Cell($heading_width[6], 7, ($PERCENT_3?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_3,2)):number_format($PERCENT_3, 2)):"-"), $border, 0, 'R', 0);
						$pdf->Cell($heading_width[7], 7, ($COUNT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_TOTAL)):number_format($COUNT_TOTAL)):"-"), $border, 0, 'R', 0);
						$pdf->Cell($heading_width[8], 7, ($PERCENT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_TOTAL,2)):number_format($PERCENT_TOTAL, 2)):"-"), $border, 0, 'R', 0);
			
						//================= Draw Border Line ====================
						$line_start_y = $start_y;		$line_start_x = $start_x;
						$line_end_y = $max_y;		$line_end_x = $start_x;
						$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
						
						for($i=0; $i<=8; $i++){
							$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
							$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
							$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
						} // end for
						//====================================================
			
						if(($pdf->h - $max_y - 10) < 22){ 
							$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
							if($data_count < (count($arr_content) - 1)){
								$pdf->AddPage();
							//	print_header();
								$max_y = $pdf->y;
							} // end if
						}else{
							if($data_count == (count($arr_content) - 1) || $arr_content[($data_count + 1)][type]=="ORG_REF") $pdf->Line($start_x, $max_y, $pdf->x, $max_y);		
						} // end if
						$pdf->x = $start_x;			$pdf->y = $max_y;
			}else{			
				$border = "";
				if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
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
				$pdf->Cell($heading_width[1], 7, ($COUNT_1?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_1)):number_format($COUNT_1)):"-"), $border, 0, 'R', 0);
				$pdf->Cell($heading_width[2], 7, ($PERCENT_1?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_1,2)):number_format($PERCENT_1, 2)):"-"), $border, 0, 'R', 0);
				$pdf->Cell($heading_width[3], 7, ($COUNT_2?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_2)):number_format($COUNT_2)):"-"), $border, 0, 'R', 0);
				$pdf->Cell($heading_width[4], 7, ($PERCENT_2?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_2,2)):number_format($PERCENT_2, 2)):"-"), $border, 0, 'R', 0);
				$pdf->Cell($heading_width[5], 7, ($COUNT_3?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_3)):number_format($COUNT_3)):"-"), $border, 0, 'R', 0);
				$pdf->Cell($heading_width[6], 7, ($PERCENT_3?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_3,2)):number_format($PERCENT_3, 2)):"-"), $border, 0, 'R', 0);
				$pdf->Cell($heading_width[7], 7, ($COUNT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_TOTAL)):number_format($COUNT_TOTAL)):"-"), $border, 0, 'R', 0);
				$pdf->Cell($heading_width[8], 7, ($PERCENT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_TOTAL,2)):number_format($PERCENT_TOTAL, 2)):"-"), $border, 0, 'R', 0);
	
				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
				for($i=0; $i<=8; $i++){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================
	
				if(($pdf->h - $max_y - 10) < 22){ 
					$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
					if($data_count < (count($arr_content) - 1)){
						$pdf->AddPage();
					//	print_header();
						$max_y = $pdf->y;
					} // end if
				}else{
					if($data_count == (count($arr_content) - 1) || $arr_content[($data_count + 1)][type]=="ORG_REF") $pdf->Line($start_x, $max_y, $pdf->x, $max_y);		
				} // end if
				$pdf->x = $start_x;			$pdf->y = $max_y;
} //end if 
} // end for

		//print_r($GRAND_TOTAL);
		//echo count($GRAND_TOTAL);
		//สรุปผลรวมยอดรวมทั้งหมด
		if(count($GRAND_TOTAL) > 1){
			$PERCENT_TOTAL_1 = $PERCENT_TOTAL_2 = $PERCENT_TOTAL_3 = 0;
			if(array_sum($GRAND_TOTAL)){ 
				$PERCENT_TOTAL_1 = (array_sum($GRAND_TOTAL1) / array_sum($GRAND_TOTAL)) * 100;
				$PERCENT_TOTAL_2 = (array_sum($GRAND_TOTAL2) / array_sum($GRAND_TOTAL)) * 100;
				$PERCENT_TOTAL_3 = (array_sum($GRAND_TOTAL3) / array_sum($GRAND_TOTAL)) * 100;
				$PERCENT_TOTAL = (array_sum($GRAND_TOTAL) / array_sum($GRAND_TOTAL)) * 100;
			} // end if

			$border = "LTBR";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("FF"),hexdec("00"),hexdec("00"));
			$pdf->SetFillColor(hexdec("FF"),hexdec("00"),hexdec("00"));
			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
			$pdf->MultiCell($heading_width[0], 7, "รวมทั้งสิ้น", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0];
			$pdf->y = $start_y;
			$pdf->Cell($heading_width[1], 7, (array_sum($GRAND_TOTAL_1)?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($array_sum($GRAND_TOTAL_1))):number_format(array_sum($GRAND_TOTAL_1))):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[2], 7, ($PERCENT_TOTAL_1?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_TOTAL_1,2)):number_format($PERCENT_TOTAL_1, 2)):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[3], 7, (array_sum($GRAND_TOTAL_2)?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($array_sum($GRAND_TOTAL_2))):number_format(array_sum($GRAND_TOTAL_2))):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[4], 7, ($PERCENT_TOTAL_2?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_TOTAL_2,2)):number_format($PERCENT_TOTAL_2, 2)):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[5], 7, (array_sum($GRAND_TOTAL_3)?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($array_sum($GRAND_TOTAL_3))):number_format(array_sum($GRAND_TOTAL_3))):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[6], 7, ($PERCENT_TOTAL_3?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_TOTAL_3,2)):number_format($PERCENT_TOTAL_3, 2)):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[7], 7, (array_sum($GRAND_TOTAL)?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($array_sum($GRAND_TOTAL))):number_format(array_sum($GRAND_TOTAL))):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[8], 7, ($PERCENT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_TOTAL,2)):number_format($PERCENT_TOTAL, 2)):"-"), $border, 1, 'R', 0);

			//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
				for($i=0; $i<=8; $i++){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================

				if(($pdf->h - $max_y - 10) < 15){ 
					if($data_count < (count($arr_content) - 1)){
						$pdf->AddPage();
				//		print_header();
						$max_y = $pdf->y;
					} // end if
				}else{
					if($data_count == (count($arr_content) - 1)) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				} // end if
				$pdf->x = $start_x;			$pdf->y = $max_y;
		} // end if
	}else{
		///$pdf->AddPage();
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();
}else if($export_type=="graph"){
	$arr_content_key = array_keys($arr_content[0]);//print_r($arr_content_key);
	$arr_categories = array();
	for($i=0;$i<count($arr_content);$i++){
//		echo "arr_content[$i][type](".$arr_content[$i][type].")==arr_rpt_order[".(count($arr_rpt_order)-1)."](".$arr_rpt_order[count($arr_rpt_order)-1].")<br>";
		if($arr_content[$i][type]==$arr_rpt_order[count($arr_rpt_order)-1]){
			$arr_categories[$i] = trim($short_name=="y")?(trim($arr_content[$i][short_name])?$arr_content[$i][short_name]:$arr_content[$i][name]):$arr_content[$i][name];
//			$arr_categories[$i] = $arr_content[$i][short_name];
			$arr_series_caption_data[0][] = $arr_content[$i][count_1];
			$arr_series_caption_data[1][] = $arr_content[$i][count_2];
			$arr_series_caption_data[2][] = $arr_content[$i][count_3];
//			echo "content ".$arr_categories[$i]."(".$arr_content[$i][count_1].",".$arr_content[$i][count_2].",".$arr_content[$i][count_3]."<br>";
		}//if($arr_content[$i][type]==$arr_rpt_order[0]){
	}//print_r($arr_categories);
	$arr_series_list[0] = implode(";", $arr_series_caption_data[0])."";
	$arr_series_list[1] = implode(";", $arr_series_caption_data[1])."";
	$arr_series_list[2] = implode(";", $arr_series_caption_data[2])."";
	$chart_title = $report_title;
	$chart_subtitle = $company_name;
	if(!$setWidth){ $setWidth = "$GRAPH_WIDE";}else{ $setWidth = "800";}
	if(!$setHeight){ $setHeight = "$GRAPH_HIGH";}else{$setHeight = "600";}
	$selectedFormat = "SWF";
	$series_caption_list = "ถือครอง;ว่างมีเงิน;ว่างไม่มีเงิน";
	$categories_list = implode(";", $arr_categories)."";
	if(strtolower($graph_type)=="pie"){
		$series_list = $GRAND_TOTAL_1[$DEPARTMENT_ID].";".$GRAND_TOTAL_2[$DEPARTMENT_ID].";".$GRAND_TOTAL_3[$DEPARTMENT_ID];
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