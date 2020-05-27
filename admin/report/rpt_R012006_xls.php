<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", "1800");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if(!trim($RPTORD_LIST)){ 
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		elseif($list_type=="PER_COUNTRY" && trim($search_pv_code)!="") $RPTORD_LIST = "PROVINCE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
	//print_r($RPTORD_LIST);

	$select_list = "";		$order_by = "";	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.ORG_ID_REF as MINISTRY_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "c.ORG_ID_REF";

				$heading_name .= " ส่วนราชการ";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.DEPARTMENT_ID";

				$heading_name .= " ส่วนราชการ";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.ORG_ID";

				$heading_name .= " ส่วนราชการ";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.PL_CODE";

				$heading_name .= " สายงาน";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if(!$MINISTRY_ID) $order_by = "c.ORG_ID_REF";
		elseif(!$DEPARTMENT_ID) $order_by = "a.DEPARTMENT_ID";
		else $order_by = "a.ORG_ID";
	} // end if
	if(!trim($select_list)){ 
		if(!$MINISTRY_ID) $select_list = "c.ORG_ID_REF as MINISTRY_ID";
		elseif(!$DEPARTMENT_ID) $select_list = "a.DEPARTMENT_ID";
		else $select_list = "a.ORG_ID";
	} // end if

	$search_condition = "";
	$arr_search_condition[] = "(e.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(e.PER_STATUS = 1)";

	$list_type_text = $ALL_REPORT_TITLE;

	if($list_type == "PER_ORG_TYPE_1"){
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}/**elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if**/
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.OT_CODE), 1)='1')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.OT_CODE), 1, 1)='1'";
	}elseif($list_type == "PER_ORG_TYPE_2"){
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}/**elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if**/
		
		// ส่วนภูมิภาค
		//$list_type_text = "ส่วนภูมิภาค";
		//if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.OT_CODE), 1)='2')";
		//elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.OT_CODE), 1, 1)='2')";
	}elseif($list_type == "PER_LINE"){//จะต้องมีค่าส่งมาถึงจะสร้างรายงานได้
		// ตำแหน่งประเภท และตำแหน่งในสายงาน
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}/**elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if**/
		
		if($search_pt_name){ $list_type_text .=" ตำแหน่งประเภท$search_pt_name / "; }
		if($search_per_type==1){
			$per_name = "ข้าราชการ";
			if(trim($search_pl_code)){
				$search_pl_code = trim($search_pl_code);
				$arr_search_condition[] = "(trim(a.PL_CODE)='$search_pl_code')";
				$list_type_text .= " ตำแหน่งในสายงาน$search_pl_name";
			}
		}/**elseif($search_per_type==2){
			$per_name = "ลูกจ้างประจำ";
			if(trim($search_pn_code)){
				$search_pn_code = trim($search_pn_code);
				$arr_search_condition[] = "(trim(a.PN_CODE)='$search_pn_code')";
				$list_type_text .= " ตำแหน่งในสายงาน$search_pn_name";
			}
		}elseif($search_per_type==3){
			$per_name = "พนักงานราชการ";
			if(trim($search_ep_code)){
				$search_ep_code = trim($search_ep_code);
				$arr_search_condition[] = "(trim(a.EP_CODE)='$search_ep_code')";
				$list_type_text .= " ตำแหน่งในสายงาน$search_ep_name";
			}
		} // end if**/
	}
	/**elseif($list_type == "PER_COUNTRY"){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(a.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}**/
	else{
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}/**elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if**/
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	
	//หาจำนวนข้าราชการ
	if($DPISDB=="odbc"){
			$cmd =" select 	count(e.PER_ID) as count_person
							from (
										(
											(
											(
												EAF_MASTER a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
											) left join EAF_PERSONAL d on (a.EAF_ID=d.EAF_ID)
										) left join PER_PERSONAL e on (d.PER_ID=e.PER_ID)
									) left join  PER_PRENAME f on (trim(e.PN_CODE)=trim(f.PN_CODE))
									$search_condition
						order by 	a.EAF_ID
						";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition); 
		$cmd = "select 		count(e.PER_ID) as count_person
						from 		EAF_MASTER a, PER_ORG b, PER_ORG c,EAF_PERSONAL d,PER_PERSONAL e,PER_PRENAME f
							where 	a.ORG_ID=b.ORG_ID and a.DEPARTMENT_ID=c.ORG_ID
											and a.EAF_ID=d.EAF_ID(+) and d.PER_ID=e.PER_ID(+)
											and trim(e.PN_CODE)=trim(f.PN_CODE) 
											$search_condition
							order by 	a.EAF_ID
						   ";
	}elseif($DPISDB=="mysql"){
			$cmd =" select 	count(e.PER_ID) as count_person
							from (
										(
											(
											(
												EAF_MASTER a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
											) left join EAF_PERSONAL d on (a.EAF_ID=d.EAF_ID)
										) left join PER_PERSONAL e on (d.PER_ID=e.PER_ID)
									) left join  PER_PRENAME f on (trim(e.PN_CODE)=trim(f.PN_CODE))
									$search_condition
						order by 	a.EAF_ID
						";
	} // end if
	$count_data = $db_dpis2->send_cmd($cmd);
	//	$db_dpis2->show_error();
	//  echo "<br>$cmd<br>";

	if($count_data==1){
		$data_dpis2 = $db_dpis2->get_array();
		$data_dpis2 = array_change_key_case($data_dpis2, CASE_LOWER);
		$count_person = $data_dpis2[count_person] ;
	} // end if
	
	$company_name .=" กรม $DEPARTMENT_NAME ";
	$company_name .=" จำนวนข้าราชการผู้มีผลสัมฤทธิ์สูง : $count_person คน";
	
	$report_title = "ภาพรวมการพัฒนาข้าราชการผู้มีผลสัมฤทธิ์สูง จำแนกตามส่วนราชการ ระดับกรม";
	$report_code = "R1206";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);

	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//
	
	$count_position_type=count($ARR_POSITION_TYPE[$search_pt_name]);
	
	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_width, $heading_name;
		global $ARR_LEVEL;
		
		$worksheet->set_column(0, 0, 8);
		$worksheet->set_column(1, 1, 40);
		$worksheet->set_column(2, 2, 50);
		$worksheet->set_column(3, 3, 30);
		$worksheet->set_column(4, 4, 35);
		$worksheet->set_column(5, 5, 35);

		$worksheet->write($xlsRow, 0, "ที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อข้าราชการผู้มีผลสัมฤทธิ์สูง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ตำแหน่งเป้าหมาย", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "ตำแหน่งประเภท", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "ระยะเวลาการพัฒนา", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));

		//แถว 2 ---------------------
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 4, "กรอบ (ปี)", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "พัฒนาแล้ว (ปี)", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
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
			$cmd =" select 	a.EAF_ID, PM_CODE, PL_CODE, LEVEL_NO, PT_CODE, 
								a.ORG_ID, b.ORG_NAME, a.DEPARTMENT_ID, c.ORG_NAME as DEPARTMENT_NAME,c.ORG_ID_REF as MINISTRY_ID,
								a.EAF_NAME, a.EAF_ACTIVE,
								 f.PN_NAME, e.PER_ID, e.PER_NAME, e.PER_SURNAME
							from (
										(
											(
												(
													EAF_MASTER a
													inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
												) inner join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
											) left join EAF_PERSONAL d on (a.EAF_ID=d.EAF_ID)
										) left join PER_PERSONAL e on (d.PER_ID=e.PER_ID)
									) left join PER_PRENAME f on (trim(e.PN_CODE)=trim(f.PN_CODE))
							$search_condition
							order by 	a.EAF_ID
						  ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition); 
		$cmd = "select 		a.EAF_ID, PM_CODE, PL_CODE, e.LEVEL_NO, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, a.DEPARTMENT_ID, c.ORG_NAME as DEPARTMENT_NAME,c.ORG_ID_REF as MINISTRY_ID,
                                            a.EAF_NAME, a.EAF_ACTIVE,
											 f.PN_NAME, e.PER_ID, e.PER_NAME, e.PER_SURNAME
							from 		EAF_MASTER a, PER_ORG b, PER_ORG c,EAF_PERSONAL d,PER_PERSONAL e,PER_PRENAME f
							where 	a.ORG_ID=b.ORG_ID and a.DEPARTMENT_ID=c.ORG_ID
											and a.EAF_ID=d.EAF_ID(+) and d.PER_ID=e.PER_ID(+)
											and trim(e.PN_CODE)=trim(f.PN_CODE) 
											$search_condition
							order by 	a.EAF_ID
						   ";
		}elseif($DPISDB=="mysql"){
			$cmd =" select 	a.EAF_ID, PM_CODE, PL_CODE, LEVEL_NO, PT_CODE, 
								a.ORG_ID, b.ORG_NAME, a.DEPARTMENT_ID, c.ORG_NAME as DEPARTMENT_NAME,c.ORG_ID_REF as MINISTRY_ID,
								a.EAF_NAME, a.EAF_ACTIVE,
								 f.PN_NAME, e.PER_ID, e.PER_NAME, e.PER_SURNAME
							from (
										(
											(
												(
													EAF_MASTER a
													inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
												) inner join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
											) left join EAF_PERSONAL d on (a.EAF_ID=d.EAF_ID)
										) left join PER_PERSONAL e on (d.PER_ID=e.PER_ID)
									) left join PER_PRENAME f on (trim(e.PN_CODE)=trim(f.PN_CODE))
							$search_condition
							order by 	a.EAF_ID
						  ";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
// echo "<br>$cmd<br>";

	$data_count = 0;
	$data_row = 0;
	$GRAND_TOTAL = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		$temp_EAF_ID = trim($data[EAF_ID]);
		$current_list .= ((trim($current_list))?", ":"") . $temp_EAF_ID;
		$ORG_ID = trim($data[ORG_ID]);
		$ORG_NAME = trim($data[ORG_NAME]);
		$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		$DEPARTMENT_NAME = trim($data[DEPARTMENT_NAME]);
		$MINISTRY_ID = trim($data[MINISTRY_ID]);
		$PM_CODE = trim($data[PM_CODE]);
		$PL_CODE = trim($data[PL_CODE]);
		$PT_CODE = trim($data[PT_CODE]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		
		$PN_NAME = trim($data[PN_NAME]);
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);
		$FULLNAME = ($PN_NAME)."$PER_NAME $PER_SURNAME";
		
		//หาตำแหน่งประเภท
		$cmd = "select LEVEL_NAME, LEVEL_SHORTNAME from PER_LEVEL where (LEVEL_ACTIVE=1) and (PER_TYPE = $search_per_type) and (LEVEL_NO='$LEVEL_NO')order by  LEVEL_SEQ_NO,LEVEL_NO";
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

		$EAF_NAME = trim($data[EAF_NAME]);
        $EAF_ACTIVE = trim($data[EAF_ACTIVE]);

		$cmd = " select ORG_NAME from PER_ORG where trim(ORG_ID)='".$MINISTRY_ID."' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$MINISTRY_NAME = $data_dpis2[ORG_NAME];

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

		//หาระยะเวลาการพัฒนา กรอบกี่ปี ***eaf_earning_structure
		if($DPISDB=="odbc"){
			$cmd = " select	SUM(ELS_PERIOD) as SUM_MONTH_PERIOD
						from			EAF_LEARNING_STRUCTURE
						where		EAF_ID=$temp_EAF_ID
						order by 	ELS_SEQ_NO, ELS_LEVEL, ELS_ID 	
						";	
		}elseif($DPISDB=="oci8"){
			$cmd = "select			SUM(ELS_PERIOD) as SUM_MONTH_PERIOD
								from			EAF_LEARNING_STRUCTURE
								where		EAF_ID=$temp_EAF_ID
								order by 	ELS_SEQ_NO, ELS_LEVEL, ELS_ID  
							";						
		}elseif($DPISDB=="mysql"){
			$cmd = " select	SUM(ELS_PERIOD) as SUM_MONTH_PERIOD
						from			EAF_LEARNING_STRUCTURE
						where		EAF_ID=$temp_EAF_ID
						order by 	ELS_SEQ_NO, ELS_LEVEL, ELS_ID 	
						";	
		} // end if
		$db_dpis2->send_cmd($cmd);
		// echo "<br>$cmd<br>";
		//	$db_dpis2->show_error();
		while ($data_dpis2 = $db_dpis2->get_array()) {
				$SUM_ELS_PERIOD = $data_dpis2[SUM_MONTH_PERIOD];

				if($SUM_ELS_PERIOD < 12){
					$SHOW_ELS_PERIOD = "$SUM_ELS_PERIOD เดือน";
				}else{
					$SHOW_ELS_PERIOD = floor($SUM_ELS_PERIOD / 12)." ปี";
					$REMAIN_ELS_PERIOD = $SUM_ELS_PERIOD % 12;
					if($REMAIN_ELS_PERIOD > 0) $SHOW_ELS_PERIOD .= " $REMAIN_ELS_PERIOD เดือน";
				}

				/***$TMP_ELS_ID = $data[ELS_ID];
				$current_list .= ((trim($current_list))?",":"") . $TMP_ELS_ID;
				$ELS_LEVEL = $data[ELS_LEVEL];
				$ELS_PERIOD = $data[ELS_PERIOD];
				
				if($ELS_PERIOD < 12){
					$SHOW_ELS_PERIOD = "$ELS_PERIOD เดือน";
				}else{
					$SHOW_ELS_PERIOD = floor($ELS_PERIOD / 12)." ปี";
					$REMAIN_ELS_PERIOD = $ELS_PERIOD % 12;
					if($REMAIN_ELS_PERIOD > 0) $SHOW_ELS_PERIOD .= " $REMAIN_ELS_PERIOD เดือน";
				}***/
		} //while
		###########
		$data_row++;
		$arr_content[$data_count][sequence] = $data_row;
		$arr_content[$data_count][eaf_name] = $EAF_NAME;
		$arr_content[$data_count][name] = $FULLNAME;
		$arr_content[$data_count][position_type] = $POSITION_TYPE." ".$LEVEL_NAME;
		$arr_content[$data_count][position_lline] = $PL_NAME;
		$arr_content[$data_count][position_mgt] = $PM_NAME;
		$arr_content[$data_count][org_name] = $ORG_NAME;
		$arr_content[$data_count][show_els_period] = $SHOW_ELS_PERIOD;	
		
		//$arr_content[$data_count][org_name] = $MINISTRY_NAME." ".$DEPARTMENT_NAME." ".$ORG_NAME;
		
		$data_count++;
	} // end while
	$GRAND_TOTAL = array_sum($LEVEL_GRAND_TOTAL);
	
	if($count_data){
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			//$xlsRow = $i;
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			for($j=0;$j < 6;$j++)	$worksheet->write($xlsRow, $j+1, "", set_format("xlsFmtTitle", "B", "C", "", 1));	
			//$worksheet->write($xlsRow, (count($ARR_LEVEL)+3), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} // end if
	
		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			for($j=0;$j < 6;$j++)	$worksheet->write($xlsRow, $j+1, "", set_format("xlsFmtTitle", "B", "C", "", 1));	
			//$worksheet->write($xlsRow, (count($ARR_LEVEL)+3), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} // end if
				
		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$COUNT = $arr_content[$data_count][sequence];
			$EAF_NAME=$arr_content[$data_count][eaf_name];
			$PER_NAME=$arr_content[$data_count][name];
			$PER_TYPE=$arr_content[$data_count][position_type];
			$PER_LINE=$arr_content[$data_count][position_lline];
			$PER_MGT=$arr_content[$data_count][position_mgt];
			$ORG_NAME=$arr_content[$data_count][org_name];
			$SHOW_ELS_PERIOD=$arr_content[$data_count][show_els_period];
			
			if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
				$xlsRow++;
				$i=0; $k=0;
				$worksheet->write($xlsRow, 0, "$COUNT", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$PER_NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$EAF_NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "$PER_TYPE", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "$SHOW_ELS_PERIOD", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "-", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			}else{
				$xlsRow++;
				$worksheet->write($xlsRow, 0, "$COUNT", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$PER_NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$EAF_NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "$PER_TYPE", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "$SHOW_ELS_PERIOD", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "-", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			} // end if
		} // end for

	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format_new("xlsFmtTitle", "fontFormat=B&alignment=&border=&isMerge=1&fgColor=&bgColor=&setRotation=0&valignment=&fontSize=&wrapText=1"));
		for($j=1;$j <=5;$j++) $worksheet->write($xlsRow, $j, "", set_format_new("xlsFmtTitle", "fontFormat=B&alignment=&border=&isMerge=1&fgColor=&bgColor=&setRotation=0&valignment=&fontSize=&wrapText=1"));
	} // end if
	
	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_code.xls\"");
	header("Content-Disposition: inline; filename=\"$report_code.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>