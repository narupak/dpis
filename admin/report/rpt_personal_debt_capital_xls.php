<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("Sheet1");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;
		global $POS_NO_TITLE, $PM_TITLE, $PL_TITLE, $CL_TITLE, $DEPARTMENT_TITLE, $ORG_TITLE, $ORG_TITLE1, $ORG_TITLE2, 
					$PT_TITLE, $PT_TITLE1, $PT_TITLE2, $LEVEL_TITLE, $ACTIVE_TITLE, $NUMBER_DISPLAY;

		$worksheet->set_column(0, 0, 15);
		$worksheet->set_column(1, 1, 25);
		$worksheet->set_column(2, 2, 25);
		$worksheet->set_column(3, 3, 25);
		$worksheet->set_column(4, 4, 35);
		$worksheet->set_column(5, 5, 25);
		$worksheet->set_column(6, 6, 25);


		
		$worksheet->write($xlsRow, 0, "$POS_NO_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "เลขประจำตัวประชาชน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ชื่อ-สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "สำนัก/กอง ตามกฎหมาย", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "สำนัก/กอง ตามมอบหมายงาน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "เงินสวัสดิการและสโมสร", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));


	} // end if
	
  	if($search_org_id){
		if($select_org_structure==0){
			$arr_search_condition[] = "(c.ORG_ID=$search_org_id)";
		}else if($select_org_structure==1){
			$arr_search_condition[] = "(b.ORG_ID=$search_org_id)";
  		}
		$print_search_condition[] = "$MINISTRY_TITLE : $search_ministry_name";
		$print_search_condition[] = "$DEPARTMENT_TITLE : $search_department_name";
		$print_search_condition[] = "$ORG_TITLE : $search_org_name";
	}elseif($search_department_id){
		$arr_search_condition[] = "(b.DEPARTMENT_ID = $search_department_id)";
		$print_search_condition[] = "$MINISTRY_TITLE : $search_ministry_name";
		$print_search_condition[] = "$DEPARTMENT_TITLE : $search_department_name";
	}elseif($search_ministry_id){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
		$print_search_condition[] = "$MINISTRY_TITLE : $search_ministry_name";
	} // end if


	if(trim($search_per_name))      $arr_search_condition[] = "(b.PER_NAME like '$search_per_name%')";
	if(trim($search_per_surname)) $arr_search_condition[] = "(b.PER_SURNAME like '$search_per_surname%')";
	if(trim($search_per_type)) 	  $arr_search_condition[] = "(b.PER_TYPE = $search_per_type)";
     if(trim($search_cardno)) 		$arr_search_condition[] = "(b.PER_CARDNO = '$search_cardno')";
 	if(trim($search_per_status) < 4) {
		$temp_per_status = $search_per_status - 1;		
		$arr_search_condition[] = "(PER_STATUS = $temp_per_status)";	
	} 
	
	if($temp_per_status == 1){
		$print_search_condition[] = "สถานภาพ : ปกติ";
	}elseif($temp_per_status == 2){
		$print_search_condition[] = "สถานภาพ : พ้นจากส่วนราชการ";
	}elseif($temp_per_status == 3){
		$print_search_condition[] = "สถานภาพ : ทิ้งหมด";
	} 
	
         	if(trim($search_data) < 4) {
		if ($search_data == 1)
			$arr_search_condition[] = "(a.PD_AMOUNT1 > 0)";
		elseif ($search_data == 2) 
			$arr_search_condition[] = "(a.PD_AMOUNT1 = 0)";		
	} 
  	if(trim($search_pos_no))  {	
		if ($search_per_type == 1 || $search_per_type==5)
			$arr_search_condition[] = "(trim(POS_NO) = '$search_pos_no')";
		elseif ($search_per_type == 2) 
			$arr_search_condition[] = "(trim(POEM_NO) = '$search_pos_no')";		
		elseif ($search_per_type == 3) 	
			$arr_search_condition[] = "(trim(POEMS_NO) = '$search_pos_no')";
		elseif ($search_per_type == 4) 	
			$arr_search_condition[] = "(trim(POT_NO) = '$search_pos_no')";
		else if ($search_per_type==0)		//ทั้งหมด
			$arr_search_condition[] = "((trim(POS_NO) = '$search_pos_no') or (trim(POEM_NO) = '$search_pos_no') or (trim(POEMS_NO) = '$search_pos_no') or (trim(POT_NO) = '$search_pos_no')) ";
	}

	$search_condition = "";
	if($DPISDB=="odbc"){ if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);
	} else { if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition); }

	if($DPISDB=="odbc"){
		$cmd =" select 		a.POS_ID, a.POS_NO_NAME, IIf(IsNull(a.POS_NO), 0, CLng(a.POS_NO)) as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
										a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
										c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
										f.PER_STATUS as PER_STATUS4, a.POS_CONDITION, a.POS_REMARK, a.LEVEL_NO, a.PAY_NO, a.POS_SALARY
						from (
									(
										(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) left join PER_PERSONAL c on (a.POS_ID=c.POS_ID and c.PER_TYPE=1 and c.PER_STATUS=1)
										) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and d.PER_TYPE=1 and (d.PER_STATUS=0 or d.PER_STATUS=2))
									) left join PER_PERSONAL e on (a.POS_ID=e.PAY_ID and e.PER_TYPE=1 and e.PER_STATUS=1)
								) left join PER_PERSONAL f on (a.POS_ID=f.PAY_ID and f.PER_TYPE=1 and (f.PER_STATUS=0 or f.PER_STATUS=2))
						$search_condition
						group by a.POS_ID, a.POS_NO_NAME, IIf(IsNull(a.POS_NO), 0, CLng(a.POS_NO)), PM_CODE, PL_CODE, CL_NAME, PT_CODE, a.POS_CONDITION,
										a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, c.PER_STATUS, d.PER_STATUS, 
										e.PER_STATUS, f.PER_STATUS, a.POS_REMARK, a.LEVEL_NO, a.PAY_NO, a.POS_SALARY
						order by a.POS_NO_NAME, IIf(IsNull(a.POS_NO), 0, CLng(a.POS_NO)) ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "	   select		  a.PER_ID, a.UPDATE_USER, a.UPDATE_DATE, a.PD_AMOUNT1,a.PD_AMOUNT2,a.PD_AMOUNT3, 
											b.PER_CARDNO,b.PN_CODE,h.PN_CODE,h.PN_NAME, b.PER_NAME, b.PER_SURNAME, b.PER_STATUS, b.per_type,
											c.ORG_ID,  c.POS_NO,c.PL_CODE,  b.ORG_ID as ORG_ID_ASS,
                     						 i.ORG_ID,i.ORG_NAME,j.ORG_ID,j.ORG_NAME as ORG_NAME_ASS,
                                			 k.pl_name, l.position_level
 							from			PER_DEBTM a, PER_PERSONAL b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, 
												PER_LEVEL f, PER_POS_TEMP g, PER_PRENAME h,PER_ORG i,PER_ORG_ASS j,per_line k,per_level l
							where		a.PER_ID=b.PER_ID (+)
											and b.PN_CODE=h.PN_CODE(+)
											and b.POS_ID=c.POS_ID(+)
											and b.ORG_ID=j.ORG_ID(+)
											and c.ORG_ID=i.ORG_ID(+)
											and b.POEM_ID=d.POEM_ID(+) 
											and c.PL_CODE=k.pl_code(+)
											and b.level_no=l.level_no(+) 
											and b.POEMS_ID=e.POEMS_ID(+) 
											and b.LEVEL_NO=f.LEVEL_NO(+) 
											and b.POT_ID=g.POT_ID(+)
							 $search_condition                          
							order by abs(POS_NO) ASC
											 ";
	}elseif($DPISDB=="mysql"){
		$cmd =" select 		a.POS_ID, a.POS_NO_NAME, a.POS_NO as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
										a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
										c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
										f.PER_STATUS as PER_STATUS4, a.POS_CONDITION, a.POS_REMARK, a.LEVEL_NO, a.PAY_NO, a.POS_SALARY
						from (
									(
										(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) left join PER_PERSONAL c on (a.POS_ID=c.POS_ID and c.PER_TYPE=1 and c.PER_STATUS=1)
										) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and d.PER_TYPE=1 and (d.PER_STATUS=0 or d.PER_STATUS=2))
									) left join PER_PERSONAL e on (a.POS_ID=e.PAY_ID and e.PER_TYPE=1 and e.PER_STATUS=1)
								) left join PER_PERSONAL f on (a.POS_ID=f.PAY_ID  and f.PER_TYPE=1and (f.PER_STATUS=0 or f.PER_STATUS=2))
						$search_condition
						group by a.POS_ID, a.POS_NO_NAME, a.POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, a.POS_CONDITION,
										a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, c.PER_STATUS, d.PER_STATUS, 
										e.PER_STATUS, f.PER_STATUS, a.POS_REMARK, a.LEVEL_NO, a.PAY_NO, a.POS_SALARY
						order by a.POS_NO_NAME, a.POS_NO ";
	} // end if


			
			
	$count_data = $db_dpis->send_cmd($cmd);
	$db_dpis->show_error();
//echo "<pre> $cmd<br>";

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		foreach($print_search_condition as $show_condition){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$show_condition", set_format("xlsFmtTitle", "B", "L", "", 0));
		} // end foreach

		$xlsRow++;
		print_header($xlsRow);
		$data_count = $xlsRow;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$POS_NO = trim($data[POS_NO]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
  			$PER_FULLNAME = trim($data[PN_NAME]."".$data[PER_NAME] ." ". $data[PER_SURNAME]);
  			$POS_LEVEL = trim($data[PL_NAME]. $data[POSITION_LEVEL]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$ORG_NAME_ASS = trim($data[ORG_NAME_ASS]);
			$PD_AMOUNT1 = trim($data[PD_AMOUNT1]);
			$PD_AMOUNT2 = trim($data[PD_AMOUNT2]);
			$PD_AMOUNT3 = trim($data[PD_AMOUNT3]);



		
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
			
			if (!$LEVEL_NO) {
				$cmd = " select LEVEL_NO_MIN from PER_CO_LEVEL where trim(CL_NAME)='$CL_NAME' ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$LEVEL_NO = $data_dpis2[LEVEL_NO_MIN];
			}
		
			$cmd = "select LEVEL_NAME, POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$LEVEL_NAME = $data_dpis2[LEVEL_NAME];
			$NEW_POSITION_TYPE = $data_dpis2[POSITION_TYPE];
			$POSITION_LEVEL = $data_dpis2[POSITION_LEVEL];

			$arr_temp = explode(" ", $LEVEL_NAME);
			$LEVEL_NAME =  $arr_temp[1];
		
			if($PAY_NO && $SESS_DEPARTMENT_NAME=="กรมการปกครอง"){
				if($DPISDB=="odbc"){
					$cmd = "	select		b.PER_ID, b.PER_NAME, b.PER_SURNAME, c.PN_NAME
										from		
													(
														PER_POSITION a
														left join PER_PERSONAL b on a.POS_ID=b.PAY_ID
													) left join PER_PRENAME c on b.PN_CODE=c.PN_CODE
										where		a.PAY_NO=$PAY_NO and PER_TYPE = 1 and PER_STATUS = 1 ";
				}elseif($DPISDB=="oci8"){
					$cmd = "	select		b.PER_ID, b.PER_NAME, b.PER_SURNAME, c.PN_NAME
										from		PER_POSITION a, PER_PERSONAL b, PER_PRENAME c
										where		a.POS_ID=b.PAY_ID(+) and a.PAY_NO=$PAY_NO and b.PN_CODE=c.PN_CODE(+) and PER_TYPE = 1 and PER_STATUS = 1 ";
				}elseif($DPISDB=="mysql"){
					$cmd = "	select		b.PER_ID, b.PER_NAME, b.PER_SURNAME, c.PN_NAME
										from		
													(
														PER_POSITION a
														left join PER_PERSONAL b on a.POS_ID=b.PAY_ID
													) left join PER_PRENAME c on b.PN_CODE=c.PN_CODE
										where		a.PAY_NO=$PAY_NO and PER_TYPE = 1 and PER_STATUS = 1 ";
				} // end if
				$db_dpis2->send_cmd($cmd);
//				echo "$cmd<br>";
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PER_ID_PAY = trim($data2[PER_ID]);
				$PAYNAME = (trim($data2[PN_NAME])?($data2[PN_NAME]):"") . $data2[PER_NAME] ." ". $data2[PER_SURNAME];

				$cmd = " select SAH_EFFECTIVEDATE
								from   PER_SALARYHIS
								where PER_ID=$PER_ID_PAY and SAH_PAY_NO='$PAY_NO'
								order by SAH_EFFECTIVEDATE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PAY_DATE = show_date_format($data2[SAH_EFFECTIVEDATE], 1);
			} // end if

			$cnt = 0;
			$PER_ID = $FULLNAME = $POH_DATE = "";
			$cmd = "	select PER_ID, PER_STATUS from	PER_PERSONAL where POS_ID=$POS_ID and PER_TYPE = 1 ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
			while($data1 = $db_dpis1->get_array()){
				$cnt++;
				$PER_ID[$cnt] = trim($data1[PER_ID]);
				$PER_STATUS = $data1[PER_STATUS];

				if($PER_ID[$cnt] && $PER_STATUS != 2){
					$cmd = " select 	b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_STATUS
									 from 		PER_PERSONAL a, PER_PRENAME b 
									 where 	a.PN_CODE=b.PN_CODE and PER_ID=$PER_ID[$cnt] ";
					$db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$FULLNAME[$cnt] = (trim($data_dpis2[PN_NAME])?($data_dpis2[PN_NAME]):"") . $data_dpis2[PER_NAME] ." ". $data_dpis2[PER_SURNAME];
					$FULLNAME[$cnt] .= (($data_dpis2[PER_STATUS]==0)?" (รอบรรจุ)":"");
					$LEVEL_NO = trim($data_dpis2[LEVEL_NO]);
					$cmd = "select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
					$db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$LEVEL_NAME = $data_dpis2[LEVEL_NAME];
		
					$arr_temp = explode(" ", $LEVEL_NAME);
					$LEVEL_NAME =  $arr_temp[1];
					$POS_SITUATION = 3;
			
					if (!$RPT_N) {
						$cmd = " select MS_SALARY from PER_MGTSALARY where trim(PT_CODE)='$PT_CODE' and trim(LEVEL_NO)='$LEVEL_NO' ";
						$count_mgtsalary = $db_dpis2->send_cmd($cmd);
						$data_dpis2 = $db_dpis2->get_array();
						$POS_MGTSALARY = $data_dpis2[MS_SALARY];
					}

					$cmd = " select POH_EFFECTIVEDATE
									from   PER_POSITIONHIS
									where PER_ID=$PER_ID[$cnt] and POH_POS_NO='$POS_NO'
									order by POH_EFFECTIVEDATE ";
					$db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$POH_DATE[$cnt] = show_date_format($data_dpis2[POH_EFFECTIVEDATE], 1);
				}else{
					$POS_SITUATION = 1;
					if($POS_GET_DATE) $POS_SITUATION = 2;
				} // end if
			} // end while

			$xlsRow = $data_count;
			$worksheet->write($xlsRow, 0, "$POS_NO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
//			$worksheet->write($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit("$POS_NO"):"$POS_NO"), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1,card_no_format($PER_CARDNO), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "$PER_FULLNAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "$POS_LEVEL", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, "$ORG_NAME_ASS", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 6, "$PD_AMOUNT1", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));


			if ($FULLNAME[2]) {
				$data_count++;
				$xlsRow = $data_count;
				$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));

			}

			if ($FULLNAME[3]) {
				$data_count++;
				$xlsRow = $data_count;
				$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			}

			if ($FULLNAME[4]) {
				$data_count++;
				$xlsRow = $data_count;
				$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			}
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