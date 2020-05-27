<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", "1800");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if(!trim($RPTORD_LIST)){ 
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
		if(in_array("PER_COUNTRY", $list_type) && trim($search_pv_code)!="") $RPTORD_LIST .= "PROVINCE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
	//print_r($RPTORD_LIST);

	$select_list = "";		$order_by = "";	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
				$db_dpis->send_cmd($cmd);
				while($data = $db_dpis->get_array()) $arr_org_min[] = $data[ORG_ID];
			
				if($select_list) $select_list .= ", ";
				$select_list .= "e.ORG_ID_REF";

				if($order_by) $order_by .= ", ";
				$order_by .= "e.ORG_ID_REF";

				$heading_name .= " ส่วนราชการ";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "c.DEPARTMENT_ID";

				$heading_name .= " ส่วนราชการ";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.ORG_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "c.ORG_ID";

				$heading_name .= " ส่วนราชการ";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "c.PL_CODE";

				$heading_name .= " สายงาน";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if(!$MINISTRY_ID) $order_by = "e.ORG_ID_REF";
		elseif(!$DEPARTMENT_ID) $order_by = "c.DEPARTMENT_ID";
		else $order_by = "c.ORG_ID";
	} // end if
	if(!trim($select_list)){ 
		if(!$MINISTRY_ID) $select_list = "e.ORG_ID_REF";
		elseif(!$DEPARTMENT_ID) $select_list = "c.DEPARTMENT_ID";
		else $select_list = "c.ORG_ID";
	} // end if

	$search_condition = "";
	$arr_search_condition[] = "(c.EAF_ACTIVE = 1)";
	$arr_search_condition[] = "(b.PER_TYPE = 1)";
	$arr_search_condition[] = "(b.PER_STATUS = 1)";	

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(c.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(c.DEPARTMENT_ID in (". implode(",", $arr_org_min) ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(b.OT_CODE), 1)='1')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(b.OT_CODE), 1, 1)='1'";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(c.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(c.DEPARTMENT_ID in (". implode(",", $arr_org_min) ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(b.OT_CODE), 1)='2')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(b.OT_CODE), 1, 1)='2')";
	}
	if(in_array("PER_LINE", $list_type)){
		// ตำแหน่งประเภท และตำแหน่งในสายงาน
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(c.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(c.DEPARTMENT_ID in (". implode(",", $arr_org_min) ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		if($search_pt_name){ $list_type_text .=" ตำแหน่งประเภท$search_pt_name / "; }
		if($search_per_type==1){
			$per_name = "ข้าราชการ";
			if(trim($search_pl_code)){
				$search_pl_code = trim($search_pl_code);
				$arr_search_condition[] = "(trim(c.PL_CODE)='$search_pl_code')";
				$list_type_text .= " ตำแหน่งในสายงาน$search_pl_name";
			}
		}
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(d.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(c.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(c.DEPARTMENT_ID in (". implode(",", $arr_org_min) ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";

	//----------------------------------------------------------------------------------------------------------------------------------------------------------
	//หาจำนวนตำแหน่งเป้าหมาย
	if($DPISDB=="odbc"){
		$cmd = "select distinct a.EAF_ID,c.EAF_ID
						from EAF_PERSONAL a, PER_PERSONAL b, EAF_MASTER c ,PER_ORG d, PER_ORG e 
						where a.PER_ID=b.PER_ID and a.EAF_ID=c.EAF_ID 
									and c.ORG_ID=d.ORG_ID and c.DEPARTMENT_ID=e.ORG_ID 
							$search_condition
						group by a.EAF_ID,c.EAF_ID,a.PER_ID,b.PER_ID
					  	";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "select distinct a.EAF_ID,c.EAF_ID
						from EAF_PERSONAL a, PER_PERSONAL b, EAF_MASTER c ,PER_ORG d, PER_ORG e 
						where a.PER_ID=b.PER_ID and a.EAF_ID=c.EAF_ID 
									and c.ORG_ID=d.ORG_ID and c.DEPARTMENT_ID=e.ORG_ID 
							$search_condition
						group by a.EAF_ID,c.EAF_ID,a.PER_ID,b.PER_ID
					  	";
	}elseif($DPISDB=="mysql"){
		$cmd = "select distinct a.EAF_ID,c.EAF_ID
						from EAF_PERSONAL a, PER_PERSONAL b, EAF_MASTER c ,PER_ORG d, PER_ORG e 
						where a.PER_ID=b.PER_ID and a.EAF_ID=c.EAF_ID 
									and c.ORG_ID=d.ORG_ID and c.DEPARTMENT_ID=e.ORG_ID 
							$search_condition
						group by a.EAF_ID,c.EAF_ID,a.PER_ID,b.PER_ID
					  	";
	} // end if
	$count_position = $db_dpis2->send_cmd($cmd);
	//	$db_dpis2->show_error();
	//	echo "<br>$cmd<br>";
	// echo "<br>$count_data<br>";

	if($count_position > 0) $company_name .=" จำนวนตำแหน่งเป้าหมาย : $count_position ตำแหน่ง";
	
	$report_title = "การพัฒนาข้าราชการผู้มีผลสัมฤทธิ์สูงตามกรอบการสั่งสมประสบการณ์ของส่วนราชการ";
	$report_code = "R1204";
	$orientation='P';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('angsa','',12);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$heading_width[0] = "8";
	$heading_width[1] = "60";
	$heading_width[2] = "40";
	$heading_width[3] = "40";
	$heading_width[4] = "55";

	function print_header(){
		global $pdf, $heading_width, $heading_name;
		global $ARR_LEVEL;
		
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ที่",'LTR',0,'L',1);
		$pdf->Cell($heading_width[1] ,7,"ตำแหน่งเป้าหมาย",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ตำแหน่งประเภท",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"จำนวนข้าราชการ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"รายชื่อข้าราชการ",'LTR',1,'C',1);
	
		//แถวที่ 2
		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'L',1);
		$pdf->Cell($heading_width[1] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ผู้มีผลสัมฤทธิ์สูง (คน)",'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ผู้มีผลสัมฤทธิ์สูง",'LBR',1,'C',1);	
	} // function		

	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PL_CODE;
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
				case "LINE" :
					$PL_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function

	//แสดงกรอบการสั่งสมประสบการณ์
	if($DPISDB=="odbc"){
		$cmd = "select	distinct a.EAF_ID,c.EAF_ID, c.EAF_NAME,a.EP_ID, a.EP_YEAR,a.PER_ID, a.PER_ID_REVIEW, a.PER_ID_REVIEW1, a.PER_ID_REVIEW2, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.DEPARTMENT_ID as PER_DEPARTMENT_ID, b.POS_ID ,c.LEVEL_NO as LEVEL_NO, c.PT_CODE,$select_list
						from		EAF_PERSONAL a, PER_PERSONAL b, EAF_MASTER c ,PER_ORG d, PER_ORG e
						where		a.PER_ID=b.PER_ID and a.EAF_ID=c.EAF_ID 
									and c.ORG_ID=d.ORG_ID and c.DEPARTMENT_ID=e.ORG_ID 
									$search_condition
						group by a.EAF_ID,c.EAF_ID, c.EAF_NAME,a.EP_ID, a.EP_YEAR,a.PER_ID, a.PER_ID_REVIEW, a.PER_ID_REVIEW1, a.PER_ID_REVIEW2, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.DEPARTMENT_ID, b.POS_ID ,c.LEVEL_NO, c.PT_CODE,$select_list
						order by 	c.EAF_NAME,a.EP_YEAR, b.PER_NAME, b.PER_SURNAME,
						$order_by
					  	";
	}elseif($DPISDB=="oci8"){ 
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "select	distinct a.EAF_ID,c.EAF_ID, c.EAF_NAME,a.EP_ID, a.EP_YEAR,a.PER_ID, a.PER_ID_REVIEW, a.PER_ID_REVIEW1, a.PER_ID_REVIEW2, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.DEPARTMENT_ID as PER_DEPARTMENT_ID, b.POS_ID ,c.LEVEL_NO as LEVEL_NO, c.PT_CODE,$select_list
						from		EAF_PERSONAL a, PER_PERSONAL b, EAF_MASTER c ,PER_ORG d, PER_ORG e
						where		a.PER_ID=b.PER_ID and a.EAF_ID=c.EAF_ID 
									and c.ORG_ID=d.ORG_ID and c.DEPARTMENT_ID=e.ORG_ID 
									$search_condition
						group by a.EAF_ID,c.EAF_ID, c.EAF_NAME,a.EP_ID, a.EP_YEAR,a.PER_ID, a.PER_ID_REVIEW, a.PER_ID_REVIEW1, a.PER_ID_REVIEW2, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.DEPARTMENT_ID, b.POS_ID ,c.LEVEL_NO, c.PT_CODE,$select_list
						order by 	c.EAF_NAME,a.EP_YEAR, b.PER_NAME, b.PER_SURNAME,
					  	$order_by
						";
	}elseif($DPISDB=="mysql"){
		$cmd = "select	distinct a.EAF_ID,c.EAF_ID, c.EAF_NAME,a.EP_ID, a.EP_YEAR,a.PER_ID, a.PER_ID_REVIEW, a.PER_ID_REVIEW1, a.PER_ID_REVIEW2, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.DEPARTMENT_ID as PER_DEPARTMENT_ID, b.POS_ID ,c.LEVEL_NO as LEVEL_NO, c.PT_CODE,$select_list
						from		EAF_PERSONAL a, PER_PERSONAL b, EAF_MASTER c ,PER_ORG d, PER_ORG e
						where		a.PER_ID=b.PER_ID and a.EAF_ID=c.EAF_ID 
									and c.ORG_ID=d.ORG_ID and c.DEPARTMENT_ID=e.ORG_ID 
									$search_condition
						group by a.EAF_ID,c.EAF_ID, c.EAF_NAME,a.EP_ID, a.EP_YEAR,a.PER_ID, a.PER_ID_REVIEW, a.PER_ID_REVIEW1, a.PER_ID_REVIEW2, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.DEPARTMENT_ID, b.POS_ID ,c.LEVEL_NO, c.PT_CODE,$select_list
						order by 	c.EAF_NAME,a.EP_YEAR, b.PER_NAME, b.PER_SURNAME,
					  	$order_by
						";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "<br>$cmd<br>";
// echo "<br>$count_data<br>";

	$data_count = 0;
	$data_row = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		$TMP_EAF_ID = $data[EAF_ID];
        $EAF_NAME = $data[EAF_NAME];

		$TMP_EP_ID = $data[EP_ID];
		$current_list .= ((trim($current_list))?",":"") . $TMP_EP_ID;
		$EP_YEAR = $data[EP_YEAR];
		$PN_CODE = $data[PN_CODE];
		$PT_CODE = trim($data[PT_CODE]);
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);
        $DEPARTMENT_ID = $data[PER_DEPARTMENT_ID]; 
        $POS_ID = $data[POS_ID];
		$LEVEL_NO[$TMP_EAF_ID] = trim($data[LEVEL_NO]);
        
		$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PN_NAME = $data2[PN_NAME];
        
		$PER_FULLNAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;
		
		//สร้าง array เพื่อเก็บข้อมูลรายชื่อข้าราชการที่มีผลสัมฤทธิ์สูง
		$ARR_EAF_GROUP[$TMP_EAF_ID][0] = $EAF_NAME;				//ชื่อกรอบ
		$ARR_EAF_GROUP[$TMP_EAF_ID][] =  $PER_FULLNAME;  //ชื่อข้าราชการ
     
        $cmd = " select a.ORG_ID, b.ORG_NAME from PER_POSITION a, PER_ORG b where a.POS_ID=$POS_ID and a.ORG_ID=b.ORG_ID ";
        $db_dpis2->send_cmd($cmd);
        $data2 = $db_dpis2->get_array();
        $ORG_ID = $data2[ORG_ID];
        $ORG_NAME = $data2[ORG_NAME];
        
		//หน่วยงานของข้าราชการที่มีผลสัมฤทธิ์สูง
        $cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
        $db_dpis2->send_cmd($cmd);
        $data2 = $db_dpis2->get_array();
        $MINISTRY_ID = $data2[ORG_ID_REF];
        $DEPARTMENT_NAME = $data2[ORG_NAME];

        $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
        $db_dpis2->send_cmd($cmd);
        $data2 = $db_dpis2->get_array();
        $MINISTRY_NAME = $data2[ORG_NAME];
		
		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='".$PT_CODE."' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PT_NAME = $data_dpis2[PT_NAME];
		
		unset($ARR_USER_AUTH);
		if(trim($data[PER_ID])) $ARR_USER_AUTH[] = $data[PER_ID];
		if(trim($data[PER_ID_REVIEW])) $ARR_USER_AUTH[] = $data[PER_ID_REVIEW];
		if(trim($data[PER_ID_REVIEW1])) $ARR_USER_AUTH[] = $data[PER_ID_REVIEW1];
		if(trim($data[PER_ID_REVIEW2])) $ARR_USER_AUTH[] = $data[PER_ID_REVIEW2];
	} // end while

	$KEY_EAF = array_unique(array_keys($ARR_EAF_GROUP));
	for($i=0; $i < count($KEY_EAF); $i++){
		$EAF_ID = $KEY_EAF[$i];
		
		if (trim($EAF_ID)) {
			$data_row++;
			$arr_content[$data_count][type] = "EAF";
			$arr_content[$data_count][sequence] = $data_row;
			$arr_content[$data_count][eaf_id] = $EAF_ID;
			$arr_content[$data_count][name] = $ARR_EAF_GROUP[$EAF_ID][0]; //ชื่อตน.เป้าหมาย

			//หาตำแหน่งประเภท
			$cmd = "select LEVEL_NAME, LEVEL_SHORTNAME from PER_LEVEL where (LEVEL_ACTIVE=1) and (PER_TYPE = $search_per_type) and (LEVEL_NO='$LEVEL_NO[$EAF_ID]')order by  LEVEL_SEQ_NO,LEVEL_NO";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
				
			$arr_temp = explode(" ", trim($data_dpis2[LEVEL_NAME]));
			//หาชื่อตำแหน่งประเภท
			if($search_per_type==1){
				$POSITION_TYPE = str_replace("ประเภท", "", $arr_temp[0]);
			}elseif($search_per_type==2){
				$POSITION_TYPE = $arr_temp[0];
			}elseif($search_per_type==3){
				$POSITION_TYPE = str_replace("กลุ่มงาน", "", $arr_temp[0]);
			}
			//หาชื่อระดับตำแหน่ง
			//$arr_temp[1]=str_replace("ระดับ", "", $arr_temp[1]);
			$LEVEL_NAME =  trim($arr_temp[1]);						
			$arr_content[$data_count][position_type] = $POSITION_TYPE." ".$LEVEL_NAME;	 
	
			$data_count++;
		}//end if	
	}//end for

	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$COUNT = $arr_content[$data_count][sequence];
			$EAF_ID=$arr_content[$data_count][eaf_id] ;
			$EAF_NAME=$arr_content[$data_count][name];
			$PER_TYPE=$arr_content[$data_count][position_type];
			$PER_LINE=$arr_content[$data_count][position_lline];
			$PER_MGT=$arr_content[$data_count][position_mgt];
			$ORG_NAME=$arr_content[$data_count][org_name];
			$COUNT_PERSON=count($ARR_EAF_GROUP[$EAF_ID])-1; //จน.ข้าราชการ

			$border = "";
			if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0){
				$pdf->SetFont('angsa','',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			}else{
				$pdf->SetFont('angsa','',12);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			} // end if

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			if($REPORT_ORDER == "EAF"){
				$pdf->MultiCell($heading_width[0], 7, "$COUNT", $border, "C");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0];
				$pdf->y = $start_y;
				$space = "";
				for($i=1;$i < count($ARR_EAF_GROUP[$EAF_ID]);$i++){
						if($i == 1){
							$pdf->MultiCell($heading_width[1], 7, "$EAF_NAME", $border, "L");
							if($pdf->y > $max_y) $max_y = $pdf->y;
							$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
							$pdf->y = $start_y;
							$pdf->Cell($heading_width[2], 7, "$PER_TYPE", $border, 0, 'L', 0);
							$pdf->Cell($heading_width[3], 7, "$COUNT_PERSON", $border, 0, 'C', 0);
						}else{
							$space = str_repeat(" ", 12);
							$pdf->Cell($heading_width[1], 7, "", $border, 0, 'L', 0);
							$pdf->Cell($heading_width[2], 7, "", $border, 0, 'L', 0);
							$pdf->Cell($heading_width[3], 7, "", $border, 0, 'C', 0);
						}
						$pdf->MultiCell($heading_width[4], 7, $space.$i.". ".trim($ARR_EAF_GROUP[$EAF_ID][$i]), $border,"L"); //แสดงรายชื่อข้าราชการทั้งหมด
				}
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4];
				$pdf->y = $start_y;
					
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
					
				if(($pdf->h - $max_y - 10) < 5){ 
					if($data_count < (count($arr_content) - 1)){
						$pdf->AddPage();
						print_header();
						$max_y = $pdf->y;
					} // end if
				}else{
					if($data_count == (count($arr_content) - 1)) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);		
				} // end if
				$pdf->x = $start_x;			$pdf->y = $max_y;
			}
		} // end for
	}else{
		$pdf->SetFont('angsa','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if	

	$pdf->close();
	$pdf->Output();		
?>