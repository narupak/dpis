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

	if($list_type == "PER_ORG_TYPE_1"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(c.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(c.DEPARTMENT_ID in (". implode(",", $arr_org_min) ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}/**elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if**/
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(b.OT_CODE), 1)='1')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(b.OT_CODE), 1, 1)='1'";
	}elseif($list_type == "PER_ORG_TYPE_2"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(c.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(c.DEPARTMENT_ID in (". implode(",", $arr_org_min) ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}/**elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if**/
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(b.OT_CODE), 1)='2')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(b.OT_CODE), 1, 1)='2')";
	}elseif($list_type == "PER_LINE"){
		// ตำแหน่งประเภท และตำแหน่งในสายงาน
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(c.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(c.DEPARTMENT_ID in (". implode(",", $arr_org_min) ."))";
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
				$arr_search_condition[] = "(trim(c.PL_CODE)='$search_pl_code')";
				$list_type_text .= " ตำแหน่งในสายงาน$search_pl_name";
			}
		}
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
			$arr_search_condition[] = "(c.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(c.DEPARTMENT_ID in (". implode(",", $arr_org_min) ."))";
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
	//  echo "<br>$cmd<br>";
	// echo "<br>$count_data<br>";

	if($count_position > 0) $company_name .=" จำนวนตำแหน่งเป้าหมาย : $count_position ตำแหน่ง";
	
	$report_title = "การพัฒนาข้าราชการผู้มีผลสัมฤทธิ์สูงตามกรอบการสั่งสมประสบการณ์ของส่วนราชการ";
	$report_code = "R1204";

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
		
		$worksheet->set_column(0, 0, 8);
		$worksheet->set_column(1, 1, 50);
		$worksheet->set_column(2, 2, 25);
		$worksheet->set_column(3, 3, 30);
		$worksheet->set_column(4, 4, 40);
		
		$worksheet->write($xlsRow, 0, "ที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "ตำแหน่งเป้าหมาย", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "ตำแหน่งประเภท", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "จำนวนข้าราชการผู้มีผลสัมฤทธิ์สูง (คน)", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "รายชื่อข้าราชการผู้มีผลสัมฤทธิ์สูง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
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
		$K_EAF_ID = $KEY_EAF[$i];
		
		if (trim($K_EAF_ID)) {
			$data_row++;
			$arr_content[$data_count][type] = "EAF";
			$arr_content[$data_count][sequence] = $data_row;
			$arr_content[$data_count][eaf_id] = $K_EAF_ID;
			$arr_content[$data_count][name] = $ARR_EAF_GROUP[$K_EAF_ID][0]; //ชื่อตน.เป้าหมาย

			//หาตำแหน่งประเภท
			$cmd = "select LEVEL_NAME, LEVEL_SHORTNAME from PER_LEVEL where (LEVEL_ACTIVE=1) and (PER_TYPE = $search_per_type) and (LEVEL_NO='$LEVEL_NO[$K_EAF_ID]')order by  LEVEL_SEQ_NO,LEVEL_NO";
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
	/* print("<pre>");
	print_r($ARR_EAF_GROUP);
	print("</pre>"); */
	
	if($count_data){
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			//$xlsRow = $i;
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			for($j=0;$j < 4;$j++)	$worksheet->write($xlsRow, $j+1, "", set_format("xlsFmtTitle", "B", "C", "", 1));	
			$xlsRow++;
		} // end if
	
		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			for($j=0;$j < 4;$j++)	$worksheet->write($xlsRow, $j+1, "", set_format("xlsFmtTitle", "B", "C", "", 1));	
			$xlsRow++;
		} // end if
				
		print_header();
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$COUNT = $arr_content[$data_count][sequence];
			$EAF_ID=$arr_content[$data_count][eaf_id];
			$EAF_NAME=$arr_content[$data_count][name];
			$PER_TYPE=$arr_content[$data_count][position_type];
			$PER_LINE=$arr_content[$data_count][position_lline];
			$PER_MGT=$arr_content[$data_count][position_mgt];
			$ORG_NAME=$arr_content[$data_count][org_name];
			$COUNT_PERSON=count($ARR_EAF_GROUP[$EAF_ID])-1; //จน.ข้าราชการ
			
			if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
					$xlsRow++;
					if($REPORT_ORDER == "EAF"){
						for($i=1;$i < count($ARR_EAF_GROUP[$EAF_ID]);$i++){
							if($i == 1){
								$worksheet->write_string($xlsRow, 0, "xxxx1 $COUNT", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
								$worksheet->write_string($xlsRow, 1, "$EAF_NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
								$worksheet->write_string($xlsRow, 2, "$PER_TYPE", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
								$worksheet->write($xlsRow, 3, "$COUNT_PERSON", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
							}else{
								$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
								$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
								$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
								$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
							}
							$worksheet->write_string($xlsRow, 4, $i.". ".trim($ARR_EAF_GROUP[$EAF_ID][$i]), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						}//end for
					}//end if
				}else{ //ตรงนี้ มันแสดงไม่ถูก สลับแถว
					$xlsRow++;
					if($REPORT_ORDER == "EAF"){
						for($i=1;$i < count($ARR_EAF_GROUP[$EAF_ID]);$i++){
							if($i == 1){
								$worksheet->write_string($xlsRow, 0, "xxxx2 $COUNT", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
								$worksheet->write_string($xlsRow, 1, "$EAF_NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
								$worksheet->write_string($xlsRow, 2, "$PER_TYPE", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
								$worksheet->write($xlsRow, 3, "$COUNT_PERSON", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
								$worksheet->write_string($xlsRow, 4, $i.". ".trim($ARR_EAF_GROUP[$EAF_ID][$i]), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
							}else{
								$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
								$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
								$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
								$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
								$worksheet->write_string($xlsRow, 4, $i.". ".trim($ARR_EAF_GROUP[$EAF_ID][$i]), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
							}
							//$worksheet->write_string($xlsRow, 4, $i.". ".trim($ARR_EAF_GROUP[$EAF_ID][$i])."+$EAF_ID", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
							/***print("<pre>");
							print_r($ARR_EAF_GROUP);
							print("</pre>");***/
						}//end for
					}
				}//end if

		} // end for
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		for($j=1;$j <4;$j++) $worksheet->write($xlsRow, $j, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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