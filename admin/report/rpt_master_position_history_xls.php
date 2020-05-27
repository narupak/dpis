<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_title");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet, $PL_TITLE, $PM_TITLE, $CL_TITLE, $PT_TITLE1, $DEPARTMENT_TITLE, $ORG_TITLE, $ORG_TITLE1, $ORG_TITLE2;

		$worksheet->set_column(0, 0, 20);
		$worksheet->set_column(1, 1, 25);
		$worksheet->set_column(2, 2, 25);
		$worksheet->set_column(3, 3, 25);
		$worksheet->set_column(4, 4, 25);
		$worksheet->set_column(5, 5, 25);
		$worksheet->set_column(6, 6, 25);
		$worksheet->set_column(7, 7, 25);
		$worksheet->set_column(8, 8, 25);
		
		
		$worksheet->write($xlsRow, 0, "วันที่เปลี่ยนแปลง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "$PL_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "$PM_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "$CL_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "$PT_TITLE1", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "$DEPARTMENT_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "$ORG_TITLE1", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "$ORG_TITLE2", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		
	} // end if

	$cmd = " select * from $table ";
	$db_dpis->send_cmd($cmd);
	$field_list = $db_dpis->list_fields($table);
//	echo "<pre>";		print_r($field_list);		echo "</pre>";
	unset($arr_fields);
	if($DPISDB=="odbc" || $DPISDB=="oci8"){
		for($i=1; $i<=count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	}elseif($DPISDB=="mysql"){
		for($i=0; $i<count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	} // end if

  /*	if(trim($search_code)) $arr_search_condition[] = "(a.$arr_fields[0] like '$search_code%')";
  	if(trim($search_name)) $arr_search_condition[] = "((a.$arr_fields[2] like '%$search_name%') or (a.$arr_fields[3] like '%$search_name%'))";
  	if(trim($search_level)) $arr_search_condition[] = "(a.$arr_fields[1] = '$search_level')";
	if(trim($search_level) == 0 && trim($search_level) !== "00") unset($search_level); 
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);  */
	
	 $search_condition = "";
	$arr_search_condition[] = "(a.POS_ID=$POS_ID)";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);

		if($DPISDB=="odbc"){
	  	 $cmd_his =" select 		a.POS_ID, LEFT(trim(a.POS_DATE), 10) as POS_DATE, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
										a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS
						from 		(
											PER_POS_MOVE a
											inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
										)
										$search_condition
										
						order by a.POS_DATE desc ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition); 
		
		$cmd_his = "
							select 		a.POS_ID, SUBSTR(trim(a.POS_DATE), 1, 10) as POS_DATE, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS
							from 		PER_POS_MOVE a, PER_ORG b
							where 	a.ORG_ID=b.ORG_ID
											$search_condition
							order by a.POS_DATE desc ";
	}elseif($DPISDB=="mysql"){
	  	 $cmd_his =" select 		a.POS_ID, LEFT(trim(a.POS_DATE), 10) as POS_DATE, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
										a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS
						from 		(
											PER_POS_MOVE a
											inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
										)
										$search_condition
						order by a.POS_DATE desc ";
	} // end if

	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$count_data = $db_dpis->send_cmd($cmd_his);
//	$db_dpis->show_error();

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		

		print_header(1);

//=========STR หา ตน.ปัจจุบัน =====================//

	$data1 = $db_dpis->get_array();
	$POS_ID_CUR = trim($data1[POS_ID]); //****
	if($DPISDB=="odbc"){
		if($SESS_ORG_STRUCTURE==1){		//มอบหมายงาน
			$cmd1 =" select 		a.POS_ID, a.DEPARTMENT_ID, iif(isnull(POS_NO),0,CLng(POS_NO) as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
											c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
											f.PER_STATUS as PER_STATUS4, a.LEVEL_NO
							from  (
									 (
										(
											(
												(
													PER_POSITION a
													inner join PER_ORG_ASS b on (a.ORG_ID=b.ORG_ID)
												) left join PER_PERSONAL c on (a.POS_ID=c.POS_ID and c.PER_STATUS=1)
											) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and (d.PER_STATUS=0 or d.PER_STATUS=2))
										) left join PER_PERSONAL e on (a.POS_ID=e.PAY_ID and e.PER_STATUS=1)
									) left join PER_PERSONAL f on (a.POS_ID=f.PAY_ID and (f.PER_STATUS=0 or f.PER_STATUS=2))
									) left join PER_PERSONAL g on (a.POS_ID=g.POS_ID)
							where (a.POS_ID = $POS_ID_CUR)
							group by a.POS_ID, a.DEPARTMENT_ID, iif(isnull(POS_NO),0,CLng(POS_NO)), PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
											c.PER_STATUS, d.PER_STATUS, e.PER_STATUS, f.PER_STATUS, a.LEVEL_NO
							order by a.DEPARTMENT_ID, iif(isnull(POS_NO),0,CLng(POS_NO)) ";
		}else{//ตามกฎหมาย
			$cmd1 =" select 		a.POS_ID, a.DEPARTMENT_ID, iif(isnull(POS_NO),0,CLng(POS_NO)) as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
											c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
											f.PER_STATUS as PER_STATUS4, a.LEVEL_NO
							from (
										(
											(
												(
													PER_POSITION a
													inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
												) left join PER_PERSONAL c on (a.POS_ID=c.POS_ID and c.PER_STATUS=1)
											) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and (d.PER_STATUS=0 or d.PER_STATUS=2))
										) left join PER_PERSONAL e on (a.POS_ID=e.PAY_ID and e.PER_STATUS=1)
									) left join PER_PERSONAL f on (a.POS_ID=f.PAY_ID and (f.PER_STATUS=0 or f.PER_STATUS=2))
							where (a.POS_ID = $POS_ID_CUR)
							group by a.POS_ID, a.DEPARTMENT_ID, iif(isnull(POS_NO),0,CLng(POS_NO)), PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
											c.PER_STATUS, d.PER_STATUS, e.PER_STATUS, f.PER_STATUS, a.LEVEL_NO
							order by a.DEPARTMENT_ID, iif(isnull(POS_NO),0,CLng(POS_NO)) ";
		}
	}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition); 
			if($SESS_ORG_STRUCTURE==1){		//มอบหมายงาน   
				$cmd1 = "select 	a.POS_ID, a.DEPARTMENT_ID, to_number(replace(POS_NO,'-','')) as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											g.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
											c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
											f.PER_STATUS as PER_STATUS4, a.LEVEL_NO
								from 	PER_POSITION a, PER_ORG_ASS b, PER_PERSONAL g,
										(select POS_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_STATUS=1) c, 
										(select POS_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_STATUS=0 or PER_STATUS=2) d, 
										(select PAY_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_STATUS=1) e, 
										(select PAY_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_STATUS=0 or PER_STATUS=2) f
								where 	a.ORG_ID=b.ORG_ID and a.POS_ID=c.POS_ID(+) and a.POS_ID=d.POS_ID(+) and a.POS_ID=e.PAY_ID(+) and a.POS_ID=f.PAY_ID(+)
												and a.POS_ID=g.POS_ID(+)
												and (a.POS_ID = $POS_ID_CUR)
								group by a.POS_ID, a.DEPARTMENT_ID, to_number(replace(POS_NO,'-','')), PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, c.PER_STATUS, 
											d.PER_STATUS, e.PER_STATUS, f.PER_STATUS, a.LEVEL_NO, g.ORG_ID
								order by a.DEPARTMENT_ID, to_number(replace(POS_NO,'-','')) ";
			}else{	//ตามกฎหมาย 
				$cmd1 = "select 	a.POS_ID, a.DEPARTMENT_ID, to_number(replace(POS_NO,'-','')) as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
											c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
											f.PER_STATUS as PER_STATUS4, a.LEVEL_NO
								from 	PER_POSITION a, PER_ORG b, 
										(select POS_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_STATUS=1) c, 
										(select POS_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_STATUS=0 or PER_STATUS=2) d, 
										(select PAY_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_STATUS=1) e, 
										(select PAY_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_STATUS=0 or PER_STATUS=2) f
								where 	a.ORG_ID=b.ORG_ID and a.POS_ID=c.POS_ID(+) and a.POS_ID=d.POS_ID(+) and a.POS_ID=e.PAY_ID(+) and a.POS_ID=f.PAY_ID(+)
											and (a.POS_ID = $POS_ID_CUR)
								group by a.POS_ID, a.DEPARTMENT_ID, to_number(replace(POS_NO,'-','')), PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, c.PER_STATUS, 
											d.PER_STATUS, e.PER_STATUS, f.PER_STATUS, a.LEVEL_NO
								order by a.DEPARTMENT_ID, to_number(replace(POS_NO,'-','')) ";
			}			
		}elseif($DPISDB=="mysql"){
			$search_condition = str_replace(" where ", " and ", $search_condition); 
			if($SESS_ORG_STRUCTURE==1){		//มอบหมายงาน
				$cmd1 =" select 		a.POS_ID, a.DEPARTMENT_ID, POS_NO+0 as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
											c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
											f.PER_STATUS as PER_STATUS4, a.LEVEL_NO
							from (
									(
										(
											(
												(
													PER_POSITION a
													inner join PER_ORG_ASS b on (a.ORG_ID=b.ORG_ID)
												) left join PER_PERSONAL c on (a.POS_ID=c.POS_ID and c.PER_STATUS=1)
											) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and (d.PER_STATUS=0 or d.PER_STATUS=2))
										) left join PER_PERSONAL e on (a.POS_ID=e.PAY_ID and e.PER_STATUS=1)
									) left join PER_PERSONAL f on (a.POS_ID=f.PAY_ID and (f.PER_STATUS=0 or f.PER_STATUS=2))
									) left join PER_PERSONAL g on (a.POS_ID=g.POS_ID)
							where 	a.ORG_ID=b.ORG_ID	and (a.POS_ID = $POS_ID_CUR)
							group by a.POS_ID, a.DEPARTMENT_ID, POS_NO+0, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
											c.PER_STATUS, d.PER_STATUS, e.PER_STATUS, f.PER_STATUS, a.LEVEL_NO
							order by a.DEPARTMENT_ID, POS_NO+0";
			}else{	//ตามกฎหมาย
				$cmd1 =" select 		a.POS_ID, a.DEPARTMENT_ID, POS_NO+0 as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
											c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
											f.PER_STATUS as PER_STATUS4, a.LEVEL_NO
							from (
										(
											(
												(
													PER_POSITION a
													inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
												) left join PER_PERSONAL c on (a.POS_ID=c.POS_ID and c.PER_STATUS=1)
											) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and (d.PER_STATUS=0 or d.PER_STATUS=2))
										) left join PER_PERSONAL e on (a.POS_ID=e.PAY_ID and e.PER_STATUS=1)
									) left join PER_PERSONAL f on (a.POS_ID=f.PAY_ID and (f.PER_STATUS=0 or f.PER_STATUS=2))
							where 	a.ORG_ID=b.ORG_ID	and (a.POS_ID = $POS_ID_CUR)
							group by a.POS_ID, a.DEPARTMENT_ID, POS_NO+0, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
											c.PER_STATUS, d.PER_STATUS, e.PER_STATUS, f.PER_STATUS, a.LEVEL_NO
							order by a.DEPARTMENT_ID, POS_NO+0";
			}
	} // end if
		$db_dpis->send_cmd($cmd1);
		$data2 = $db_dpis->get_array();
		//$db_dpis->show_error();
		$POS_NO_CUR = trim($data2[POS_NO]);
		$ORG_ID_CUR = trim($data2[ORG_ID]);
		$ORG_NAME_CUR = trim($data2[ORG_NAME]);
		$ORG_ID_REF_CUR = trim($data2[ORG_ID_REF]);
		$PM_CODE_CUR = trim($data2[PM_CODE]);
		$PL_CODE_CUR = trim($data2[PL_CODE]);
		$PT_CODE_CUR = trim($data2[PT_CODE]);
		$CL_NAME_CUR = trim($data2[CL_NAME]);
		$ORG_ID_1_CUR = trim($data2[ORG_ID_1]);
		$ORG_ID_2_CUR = trim($data2[ORG_ID_2]);
		$POS_STATUS_CUR = trim($data2[POS_STATUS]);
		$LEVEL_NO_CUR = trim($data2[LEVEL_NO]);

		$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='".$PM_CODE_CUR."' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PM_NAME_CUR = $data_dpis2[PM_NAME];

		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='".$PL_CODE_CUR."' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PL_NAME_CUR = $data_dpis2[PL_NAME];
		if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง" && !$PM_NAME_CUR) $PM_NAME_CUR = $PL_NAME_CUR;

		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='".$PT_CODE_CUR."' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PT_NAME_CUR = $data_dpis2[PT_NAME];

		$cmd = "select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO_CUR'";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$NEW_POSITION_TYPE_CUR = $data_dpis2[POSITION_TYPE];
		$LEVEL_NAME_CUR = $data_dpis2[POSITION_LEVEL];

		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='02' and ORG_ID=$ORG_ID_REF_CUR ";
		if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_REF_NAME_CUR = $data_dpis2[ORG_NAME];

		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='04' and ORG_ID=$ORG_ID_1_CUR ";
		if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME_1_CUR = $data_dpis2[ORG_NAME];

		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='05' and ORG_ID=$ORG_ID_2_CUR ";
		if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME_2_CUR = $data_dpis2[ORG_NAME];
		//-----------------------
		$worksheet->write_string(2, 0, "ปัจจุบัน", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
		$worksheet->write(2, 1,$PL_NAME_CUR, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
		$worksheet->write(2, 2, $PM_NAME_CUR, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
		$worksheet->write(2, 3, $CL_NAME_CUR, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
		$worksheet->write(2, 4, $PT_NAME_CUR, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
		$worksheet->write(2, 5, $ORG_REF_NAME_CUR, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
		$worksheet->write(2, 6, $ORG_NAME_CUR, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
		$worksheet->write(2, 7,$ORG_NAME_1_CUR, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
		$worksheet->write(2, 8, $ORG_NAME_2_CUR, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
	//===============	END CUR =================//
	//===============	STR HIS ==================//
		$data_count = 2;
		$db_dpis->send_cmd($cmd_his);  //###
		while($data = $db_dpis->get_array()){
		$data_count++;
		$POS_ID = trim($data[POS_ID]);
		$show_POS_DATE = show_date_format($data[POS_DATE], 1);

		$ORG_ID = trim($data[ORG_ID]);
		$ORG_NAME = trim($data[ORG_NAME]);
		$ORG_ID_REF = trim($data[ORG_ID_REF]);
		$PM_CODE = trim($data[PM_CODE]);
		$PL_CODE = trim($data[PL_CODE]);
		$PT_CODE = trim($data[PT_CODE]);
		$CL_NAME = trim($data[CL_NAME]);
		$ORG_ID_1 = trim($data[ORG_ID_1]);
		$ORG_ID_2 = trim($data[ORG_ID_2]);
		$POS_STATUS = trim($data[POS_STATUS]);
		if($POS_STATUS==1) $POS_STATUS = "ว่างไม่มีเงิน";
		elseif($POS_STATUS==2) $POS_STATUS = "ว่างมีเงิน";
		elseif($POS_STATUS==3) $POS_STATUS = "มีคนครอง";

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

		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='02' and ORG_ID=$ORG_ID_REF ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_REF_NAME = $data_dpis2[ORG_NAME];

		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='04' and ORG_ID=$ORG_ID_1 ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME_1 = $data_dpis2[ORG_NAME];

		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='05' and ORG_ID=$ORG_ID_2 ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME_2 = $data_dpis2[ORG_NAME];

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $show_POS_DATE, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1,$PL_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $PM_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $CL_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $PT_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, $ORG_REF_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 6, $ORG_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 7,$ORG_NAME_1, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 8, $ORG_NAME_2, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			
		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_title.xls\"");
	header("Content-Disposition: inline; filename=\"$report_title.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>