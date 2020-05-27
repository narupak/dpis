<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$report_title = trim(iconv("utf-8","tis620",urldecode($report_title)));
	if (strlen($report_title) >= 32) {
			$worksheet = &$workbook->addworksheet("Sheet1");
	}else{
			$worksheet = &$workbook->addworksheet("$report_title");
	}
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 10);
		$worksheet->set_column(2, 2, 30);
		$worksheet->set_column(3, 3, 30);
		$worksheet->set_column(4, 4, 30);
		$worksheet->set_column(5, 5, 30);
		$worksheet->set_column(6, 6, 10);
		$worksheet->set_column(7, 7, 15);
		
		$worksheet->write($xlsRow, 0, "ปีงบประมาณ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "เลขที่ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "L", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ชื่อ-สกุล", set_format("xlsFmtTableHeader", "B", "L", "TLR", 0));
		$worksheet->write($xlsRow, 3, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "สำนัก/กองตามกฏหมาย", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "สำนัก/กองตามมอบหมายงาน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "วันลาพักผ่อน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		
	} // end if

	

  	if ($SESS_ORG_STRUCTURE==0 || $SESS_ORG_STRUCTURE==1) $select_org_structure = $SESS_ORG_STRUCTURE;
	if($SESS_PER_TYPE!=0) { $search_per_type = (isset($search_per_type))?  $search_per_type : $SESS_PER_TYPE; }
	$search_per_type = (isset($search_per_type))?  $search_per_type : 1;
	
	if(!$sort_by) $sort_by=1;
	$sort_type = (isset($sort_type))?  $sort_type : "1:asc";
	$arrSort=explode(":",$sort_type);
	$SortType[$arrSort[0]]	=$arrSort[1];
	if(!$order_by) $order_by=1;

  	if($order_by==1){
		$order_str = "PER_NAME $SortType[$order_by], PER_SURNAME $SortType[$order_by]";
  	} elseif($order_by==2) {
		if($DPISDB=="odbc") {
			if ($search_per_type==1 || $search_per_type==5) $order_str = "POS_NO_NAME $SortType[$order_by], iif(isnull(POS_NO),0,POS_NO) $SortType[$order_by]";
			elseif ($search_per_type==2) $order_str = "POEM_NO_NAME $SortType[$order_by], iif(isnull(POEM_NO),0,POEM_NO) $SortType[$order_by]";
			elseif ($search_per_type==3) $order_str = "POEMS_NO_NAME $SortType[$order_by], iif(isnull(POEMS_NO),0,POEMS_NO) $SortType[$order_by]";
			elseif ($search_per_type==4) $order_str = "POT_NO_NAME $SortType[$order_by], iif(isnull(POT_NO),0,POT_NO) $SortType[$order_by]";
		}elseif($DPISDB=="oci8"){ 
			if ($search_per_type==1 || $search_per_type==5) $order_str = "POS_NO_NAME $SortType[$order_by], to_number(replace(POS_NO,'-','')) $SortType[$order_by]";
			elseif ($search_per_type==2) $order_str = "POEM_NO_NAME $SortType[$order_by], to_number(replace(POEM_NO,'-','')) $SortType[$order_by]";
			elseif ($search_per_type==3) $order_str = "POEMS_NO_NAME $SortType[$order_by], to_number(replace(POEMS_NO,'-','')) $SortType[$order_by]";
			elseif ($search_per_type==4) $order_str = "POT_NO_NAME $SortType[$order_by], to_number(replace(POT_NO,'-','')) $SortType[$order_by]";
		}elseif($DPISDB=="mysql"){ 
			if ($search_per_type==1 || $search_per_type==5) $order_str = "POS_NO_NAME $SortType[$order_by], POS_NO+0 $SortType[$order_by]";
			elseif ($search_per_type==2) $order_str = "POEM_NO_NAME $SortType[$order_by], POEM_NO+0 $SortType[$order_by]";
			elseif ($search_per_type==3) $order_str = "POEMS_NO_NAME $SortType[$order_by], POEMS_NO+0 $SortType[$order_by]";
			elseif ($search_per_type==4) $order_str = "POT_NO_NAME $SortType[$order_by], POT_NO+0 $SortType[$order_by]";
		}
  	} elseif($order_by==3){
		if ($search_per_type==1 || $search_per_type==5) $order_str = "c.ORG_ID $SortType[$order_by]";
		elseif ($search_per_type==2) $order_str = "d.ORG_ID $SortType[$order_by]";
		elseif ($search_per_type==3) $order_str = "e.ORG_ID $SortType[$order_by]";
		elseif ($search_per_type==4) $order_str = "g.ORG_ID $SortType[$order_by]";
  	} elseif($order_by==4) {
		$order_str = "VC_DAY $SortType[$order_by]";
	}
  	
	//เข้าสู่เงื่อนไขการ command="SEARCH"
    if($search_org_id){
		if($select_org_structure==0){
			if ($search_per_type==1 || $search_per_type==5) $arr_search_condition[] = "(c.ORG_ID=$search_org_id)";
			elseif ($search_per_type==2) $arr_search_condition[] = "(d.ORG_ID=$search_org_id)";
			elseif ($search_per_type==3) $arr_search_condition[] = "(e.ORG_ID=$search_org_id)";
			elseif ($search_per_type==4) $arr_search_condition[] = "(g.ORG_ID=$search_org_id)";
		}else if($select_org_structure==1){
			$arr_search_condition[] = "(b.ORG_ID=$search_org_id)";
  		}
	}elseif($search_department_id){
		$arr_search_condition[] = "(b.DEPARTMENT_ID = $search_department_id)";
	}elseif($search_ministry_id){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
	} // end if

  	if(trim($search_vc_year))			$arr_search_condition[] = "(a.VC_YEAR = '$search_vc_year')";
	if(trim($search_per_name))      $arr_search_condition[] = "(b.PER_NAME like '$search_per_name%')";
	if(trim($search_per_surname)) $arr_search_condition[] = "(b.PER_SURNAME like '$search_per_surname%')";
	if(trim($search_per_type)) 	  $arr_search_condition[] = "(b.PER_TYPE = $search_per_type)";
	$search_condition = "";
	if($DPISDB=="odbc"){ if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);
	} else { if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition); }

	if($DPISDB=="oci8"){
		$cmd = "	select		a.VC_YEAR, a.PER_ID, a.VC_DAY, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.ORG_ID as ORG_ID_ASS, b.PER_STATUS
								from		PER_VACATION a, PER_PERSONAL b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, 
												PER_LEVEL f, PER_POS_TEMP g 
								where		a.PER_ID=b.PER_ID and b.POS_ID=c.POS_ID(+) and b.POEM_ID=d.POEM_ID(+) and 
												b.POEMS_ID=e.POEMS_ID(+) and b.LEVEL_NO=f.LEVEL_NO(+) and b.POT_ID=g.POT_ID(+)
												$search_condition
								order by 	$order_str ";
	}

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$VC_YEAR = $data[VC_YEAR];
			$PER_ID = $data[PER_ID];
			$PN_CODE = $data[PN_CODE];
			$PER_STATUS = trim($data[PER_STATUS]);
			$VC_DAY = number_format($data[VC_DAY],1);
			$ORG_ID_ASS = $data[ORG_ID_ASS];
			
			$cmd = " select LEVEL_NO, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, POT_ID 
						from 		PER_PERSONAL 
						where 	PER_ID=$PER_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LEVEL_NO = trim($data2[LEVEL_NO]);
			$PER_TYPE = $data2[PER_TYPE];
			$POS_ID = $data2[POS_ID];
			$POEM_ID = $data2[POEM_ID];
			$POEMS_ID = $data2[POEMS_ID];
			$POT_ID = $data2[POT_ID];
			
			$cmd = " select POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LEVEL_NAME = trim($data2[POSITION_LEVEL]);
			
			$POS_NO = "";
			$PL_NAME = "";
			$TMP_ORG_NAME = "";
			if($PER_TYPE == 1){
				$cmd = " select 	a.ORG_ID, d.ORG_NAME, b.PL_NAME, a.PT_CODE, POS_NO_NAME, POS_NO 
								from 		PER_POSITION a, PER_LINE b, PER_ORG d
								where 	a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=d.ORG_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_ORG_NAME = trim($data2[ORG_NAME]);
				$PL_NAME = trim($data2[PL_NAME])?($data2[PL_NAME] ."". level_no_format($LEVEL_NAME) . ((trim($data2[PT_NAME]) != "ทั่วไป" && $LEVEL_NO >= 6)?"$data2[PT_NAME]":"")):" ".level_no_format($LEVEL_NAME);
				$POS_NO_NAME = trim($data2[POS_NO_NAME]);
				if (substr($POS_NO_NAME,0,4)=="กปด.")
					$POS_NO = $POS_NO_NAME." ".trim($data2[POS_NO]);
				else
					$POS_NO = $POS_NO_NAME.trim($data2[POS_NO]);
			}elseif($PER_TYPE == 2){
				$cmd = " select	pl.PN_NAME, po.ORG_NAME, POEM_NO_NAME, POEM_NO    
								from	PER_POS_EMP pp, PER_POS_NAME pl, PER_ORG po 
								where	pp.POEM_ID=$POEM_ID and pp.ORG_ID=po.ORG_ID and pp.PN_CODE=pl.PN_CODE  ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PN_NAME]);
				$TMP_ORG_NAME = trim($data2[ORG_NAME]);
				$PL_NAME = (trim($PL_NAME))? "$PL_NAME $LEVEL_NAME" : "";	
				$POS_NO = trim($data2[POEM_NO_NAME]).trim($data2[POEM_NO]);
			}elseif($PER_TYPE == 3){
				$cmd = " select	pl.EP_NAME, po.ORG_NAME, POEMS_NO_NAME, POEMS_NO   
								from	PER_POS_EMPSER pp, PER_EMPSER_POS_NAME pl, PER_ORG po 
								where	pp.POEMS_ID=$POEMS_ID and pp.ORG_ID=po.ORG_ID and pp.EP_CODE=pl.EP_CODE  ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[EP_NAME]);
				$TMP_ORG_NAME = trim($data2[ORG_NAME]);
				$PL_NAME = (trim($PL_NAME))? "$PL_NAME $LEVEL_NAME" : "";	
				$POS_NO = trim($data2[POEMS_NO_NAME]).trim($data2[POEMS_NO]);
			}elseif($PER_TYPE == 4){
				$cmd = " select	pl.TP_NAME, po.ORG_NAME, POT_NO_NAME, POT_NO    
								from	PER_POS_TEMP pp, PER_TEMP_POS_NAME pl, PER_ORG po 
								where	pp.POT_ID=$POT_ID and pp.ORG_ID=po.ORG_ID and pp.TP_CODE=pl.TP_CODE  ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[TP_NAME]);
				$TMP_ORG_NAME = trim($data2[ORG_NAME]);
				$PL_NAME = (trim($PL_NAME))? "$PL_NAME $LEVEL_NAME" : "";	
				$POS_NO = trim($data2[POT_NO_NAME]).trim($data2[POT_NO]);
			} // end if
			
			
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PN_NAME = $data2[PN_NAME];
			
			$STATUS = "";
			if($PER_STATUS == 2){
				$STATUS = "พ้นจากส่วนราชการ";
			}
			$PER_FULLNAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;
			
			$TMP_ORG_NAME_ASS = "";
			if ($ORG_ID_ASS) {
				$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$ORG_ID_ASS ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_ORG_NAME_ASS = trim($data2[ORG_NAME]);
			}
			
			
	
			

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $VC_YEAR, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $POS_NO, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $PER_FULLNAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $PL_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $TMP_ORG_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, $TMP_ORG_NAME_ASS, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 6, $VC_DAY, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 7, $STATUS, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));

		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "R", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "R", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"P0604.xls\"");
	header("Content-Disposition: inline; filename=\"P0604.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>