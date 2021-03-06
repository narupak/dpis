<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=f.PL_CODE";
		$pl_code = "b.PL_CODE";
		$pl_name = "f.PL_NAME";
		$position_no = "b.POS_NO_NAME, b.POS_NO";
		$line_search_code = trim($search_pl_code);
		$line_search_name = trim($search_pl_name);
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=f.PN_CODE";
		$pl_code = "b.PN_CODE";
		$pl_name = "f.PN_NAME";
		$position_no = "b.POEM_NO_NAME, b.POEM_NO";
		$line_search_code = trim($search_pn_code);
		$line_search_name = trim($search_pn_name);
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=f.EP_CODE";
		$pl_code = "b.EP_CODE";
		$pl_name = "f.EP_NAME";
		$position_no = "b.POEMS_NO_NAME, b.POEMS_NO";
		$line_search_code = trim($search_ep_code);
		$line_search_name = trim($search_ep_name);
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=f.TP_CODE";
		$pl_code = "b.TP_CODE";
		$pl_name = "f.TP_NAME";
		$position_no = "b.POT_NO_NAME, b.POT_NO";
		$line_search_code = trim($search_tp_code);
		$line_search_name = trim($search_tp_name);
	} // end if
	
	if(!trim($RPTORD_LIST)){ 
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
	$search_level_no = trim($search_level_no);
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_search_condition[] = "(rtrim(a.LEVEL_NO) = '$search_level_no')";
	$arr_search_condition[] = "(i.DC_TYPE in (1,2))";

	$list_type_text = $ALL_REPORT_TITLE;

	if($list_type == "PER_ORG_TYPE_1"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		$list_type_text = "��ǹ��ҧ";
		$arr_search_condition[] = "(trim(c.OT_CODE)='01')";
	}elseif($list_type == "PER_ORG_TYPE_2"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		$list_type_text = "��ǹ��ҧ������Ҥ";
		$arr_search_condition[] = "(trim(c.OT_CODE)='02')";
	}elseif($list_type == "PER_ORG_TYPE_3"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		$list_type_text = "��ǹ�����Ҥ";
		$arr_search_condition[] = "(trim(c.OT_CODE)='03')";
	}elseif($list_type == "PER_ORG_TYPE_4"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		// ��ҧ�����
		$list_type_text = "��ҧ�����";
		$arr_search_condition[] = "(trim(c.OT_CODE)='04')";
	}elseif($list_type == "PER_ORG"){
		$list_type_text = "";
		if($select_org_structure==0) {
			if(trim($search_org_id)){ 
				$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
				$list_type_text .= "$search_org_name";
			} // end if
			if(trim($search_org_id_1)){ 
				$arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
				$list_type_text .= " - $search_org_name_1";
			} // end if
			if(trim($search_org_id_2)){ 
				 $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
				$list_type_text .= " - $search_org_name_2";
			} // end if
		}else if($select_org_structure==1) {
			if(trim($search_org_ass_id)){ 
				 $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
				$list_type_text .= "$search_org_ass_name";
			} // end if
			if(trim($search_org_ass_id_1)){ 
				$arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id_1)";
				$list_type_text .= " - $search_org_ass_name_1";
			} // end if
			if(trim($search_org_ass_id_2)){ 
				 $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id_2)";
				$list_type_text .= " - $search_org_ass_name_2";
			} // end if
		}
	}elseif($list_type == "PER_LINE"){
		// ��§ҹ
		$list_type_text = "";
		if($line_search_code){
			$arr_search_condition[] = "(trim($pl_code)='$line_search_code')";
			$list_type_text .= " $line_search_name";
		}
	}elseif($list_type == "PER_COUNTRY"){
		// ����� , �ѧ��Ѵ
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(c.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}else{
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);	

	$cmd2="select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$search_level_no' ";
	$db_dpis2->send_cmd($cmd2);
	//$db_dpis2->show_error();
	$data=$db_dpis2->get_array();
	$level_name=$data[LEVEL_NAME];

	$company_name = "�ٻẺ����͡��§ҹ : ". ($select_org_structure==1?"�ç���ҧ����ͺ���§ҹ - ":"�ç���ҧ��������� - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||��ª���$PERSON_TYPE[$search_per_type]����ç���˹���дѺ ". $level_name ." ���§����ӴѺ������";
	$report_code = "R0403";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		
		$worksheet->set_column(0, 0, 5);
		$worksheet->set_column(1, 1, 30);
		$worksheet->set_column(2, 2, 25);
		$worksheet->set_column(3, 3, 12);
		$worksheet->set_column(4, 4, 30);
		$worksheet->set_column(5, 5, 45);
		$worksheet->set_column(6, 6, 8);
		$worksheet->set_column(7, 7, 30);
		$worksheet->set_column(8, 8, 25);
		$worksheet->set_column(9, 9, 12);
		$worksheet->set_column(10, 10, 10);
		$worksheet->set_column(11, 11, 10);
		$worksheet->set_column(12, 12, 10);
		$worksheet->set_column(13, 13, 18);
		$worksheet->set_column(14, 14, 18);
		$worksheet->set_column(15, 15, 12);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "�ӴѺ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "���� - ʡ��", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "���˹�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "�ѹ�������дѺ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "�дѺ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "�ѧ�Ѵ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "�Ţ���", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "�ز�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "�Ң��Ԫ��͡", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 9, "�ѹ�������дѺ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 10, "�дѺ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 11, "�Թ��͹", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 12, "����ͧ�Ҫ�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));	
		$worksheet->write($xlsRow, 13, "�ѹ��è�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 14, "�ѹ��͹���Դ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 15, "�ѹ���³", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "�Ѩ�غѹ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 6, "���˹�", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 13, "(�����Ҫ���)", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 14, "(���ص��)", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // function		

	//---��ͧ���дѺ���˹觢ͧ����ѹ�ͧ���� 㹡ó�����Ҷ֧�����дѺ���˹觹����� ������дѺ��͹˹��
	$L["O1"]=array('04','03','02','01','O1');
	$L["O2"]=array('06','05','04','O1','O2');
	$L["O3"]=array('08','07','06','O2','O3');
	$L["O4"]=array('09','08','O3','O4');
	$L["K1"]=array('05','04','03','K1');		
	$L["K2"]=array('07','06','05','K1','K2');
	$L["K3"]=array('08','07','K2','K3');
	$L["K4"]=array('09','08','K3','K4');
	$L["K5"]=array('10','09','K4','K5');
	$L["D1"]=array('08','07','D1');
	$L["D2"]=array('09','08','D1','D2');
	$L["M1"]=array('09','08','M1');
	$L["M2"]=array('11','10','09','M1','M2');
	//----ǹ loop ����дѺ���˹� ������͡�� -------------
	$arrkeep = array();
	for($i=0; $i < count($L[$search_level_no]); $i++){	
		$index=$L[$search_level_no][$i];		//$index=level no
	if($DPISDB=="odbc"){
			$cmd = " select		a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no, $pl_name, a.LEVEL_NO, j.LEVEL_NAME, j.POSITION_LEVEL, 
												MAX(e.POH_LEVEL_NO) as LEVEL_NOHIS, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, 
												LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,	MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, 
												MAX(i.DC_ORDER) as DC_ORDER, a.DEPARTMENT_ID
					   from		(
										(
											(
												(
													(
														(
															(
																(
																PER_PERSONAL a 
																left join $position_table b on ($position_join) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join $line_table f on ($line_join)
											) left join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
										) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
									) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
								) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
								$search_condition and e.POH_LEVEL_NO='$index' and (e.POH_LEVEL_NO < a.LEVEL_NO)
						group by e.PER_ID,e.POH_LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no, $pl_name, a.LEVEL_NO,
								 j.LEVEL_NAME, j.POSITION_LEVEL, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), 
								LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE, a.DEPARTMENT_ID
						order by  MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)),a.PER_SALARY desc,MAX(i.DC_ORDER) desc,
								LEFT(trim(a.PER_STARTDATE),10),LEFT(trim(a.PER_BIRTHDATE),10) ";   
	}elseif($DPISDB=="oci8"){	
			$search_condition = str_replace(" where ", " and ", $search_condition);	
			$cmd = " select 	distinct e.PER_ID, a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no, $pl_name, a.LEVEL_NO, j.LEVEL_NAME, j.POSITION_LEVEL, 
											MAX(e.POH_LEVEL_NO) as LEVEL_NOHIS, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, 
											SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, a.PER_RETIREDATE,MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, 
											MAX(i.DC_ORDER) as DC_ORDER, a.DEPARTMENT_ID
								from 	PER_PERSONAL a,  $position_table b, PER_ORG c, PER_PRENAME d, PER_POSITIONHIS e, $line_table f, PER_TYPE g, PER_DECORATEHIS h, PER_DECORATION i, PER_LEVEL j 
								where $position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+) 
											and a.PER_ID=e.PER_ID(+) and $line_join(+) 
											and b.PT_CODE=g.PT_CODE(+) and a.PER_ID=h.PER_ID(+) and a.LEVEL_NO=j.LEVEL_NO(+) 
								$search_condition and e.POH_LEVEL_NO='$index' 											
								 group by e.PER_ID,e.POH_LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no, $pl_name, a.LEVEL_NO,
											 j.LEVEL_NAME, j.POSITION_LEVEL, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10), 
											SUBSTR(trim(a.PER_BIRTHDATE), 1, 10),a.PER_RETIREDATE, a.DEPARTMENT_ID
								 order by MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)),a.PER_SALARY desc,MAX(i.DC_ORDER) desc,
											SUBSTR(trim(a.PER_STARTDATE), 1, 10),SUBSTR(trim(a.PER_BIRTHDATE), 1, 10)  "; 
								//**�������¹ group by/order by �ռŵ�͡���ʴ��ŷ������֧�ѹ�����������������дѺ �ͧ�дѺ���˹觡�͹˹�Ҩ��������дѺ�Ѩ�غѹ
								/*	order by MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)),a.PER_SALARY desc,MAX(i.DC_ORDER) desc,
											SUBSTR(trim(a.PER_STARTDATE), 1, 10),SUBSTR(trim(a.PER_BIRTHDATE), 1, 10)  ";  	*/
	}elseif($DPISDB=="mysql"){
			$cmd = " select		a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no, $pl_name, a.LEVEL_NO, j.LEVEL_NAME, j.POSITION_LEVEL, 
												MAX(e.POH_LEVEL_NO) as LEVEL_NOHIS, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, 
												LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,	MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, 
												MAX(i.DC_ORDER) as DC_ORDER, a.DEPARTMENT_ID
					   from		(
										(
											(
												(
													(
														(
															(
																(
																PER_PERSONAL a 
																left join $position_table b on ($position_join) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join $line_table f on ($line_join)
											) left join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
										) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
									) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
								) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
								$search_condition and e.POH_LEVEL_NO='$index' and (e.POH_LEVEL_NO < a.LEVEL_NO)
						group by e.PER_ID,e.POH_LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no, $pl_name, a.LEVEL_NO,
								 j.LEVEL_NAME, j.POSITION_LEVEL, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), 
								LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE, a.DEPARTMENT_ID
						order by  MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)),a.PER_SALARY desc,MAX(i.DC_ORDER) desc,
								LEFT(trim(a.PER_STARTDATE),10),LEFT(trim(a.PER_BIRTHDATE),10) ";   
	}		
	//���ҧ query ���� ����Ѻ������ record ����
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		//$cmd = str_replace("b.ORG_ID_1", "a.ORG_ID_1", $cmd);
		//$cmd = str_replace("b.ORG_ID_2", "a.ORG_ID_2", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
//echo "$cmd<br>===============<br>";
		//---���ҧ query �ͧ���е�� ����ǹ���������ա����
		$count_data = $db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()){		
			if($PER_ID == $data[PER_ID]) continue;
			//$person_count++;		

			$PER_ID = $data[PER_ID];
			//---�� PER_ID �ͧ�����ŧ� array ����ժ�����дѺ���˹觷�����§����ӴѺ�á������ �ѹ�Ѵ����������ա��������
			if(!in_array($PER_ID,$arrkeep)){
				$arrkeep[] = $PER_ID;
				//=================================
				if(in_array($PER_ID,$ARRALL_PERID_LEVELNO)){
					$ARRALL_HAVE_PERID_LEVELNO[] = $PER_ID;
				}
				
				$ARRSORTLEVEL['per_id'][] = $PER_ID;
				
				$PN_NAME[$PER_ID] = trim($data[PN_NAME]);
				$PER_NAME[$PER_ID] = trim($data[PER_NAME]);
				$PER_SURNAME[$PER_ID] = trim($data[PER_SURNAME]);
				$POS_NO[$PER_ID] = trim($data[POS_NO_NAME]).trim($data[POS_NO]);
				$PL_NAME[$PER_ID] = trim($data[PL_NAME]);
				$LEVEL_NO[$PER_ID] = trim($data[LEVEL_NO]);
				$LEVEL_NOHIS[$PER_ID] = trim($data[LEVEL_NOHIS]);
				
				$ARRSORTLEVEL['per_sortlevel'][] = $LEVEL_NOHIS[$PER_ID];		//--------
				
				$LEVEL_NAME[$PER_ID] = trim($data[LEVEL_NAME]);
				$PT_CODE[$PER_ID] = trim($data[PT_CODE]);
				$PT_NAME[$PER_ID] = trim($data[PT_NAME]);
				$ORG_NAME[$PER_ID] = trim($data[ORG_NAME]);
				if ($ORG_NAME[$PER_ID]=="-") $ORG_NAME[$PER_ID] = "";
				$DEPARTMENT_ID=trim($data[DEPARTMENT_ID]);
				$ORG_ID_1=trim($data[ORG_ID_1]);
				$PER_RETIREDATE=trim($data[PER_RETIREDATE]);
				$POH_EFFECTIVEDATE = substr(trim($data[POH_EFFECTIVEDATE]), 0, 10);
				if(trim($POH_EFFECTIVEDATE)){
					//���ѹ��������������дѺ ������дѺ���˹觡�͹����͹�дѺ����ش ������Ѵ���§�������дѺ���˹�/�ѹ��� (e.POH_LEVEL_NO ��� e.POH_EFFECTIVEDATE)
					$PER_EFFECTIVEDATE[$LEVEL_NOHIS][$PER_ID] =$POH_EFFECTIVEDATE;		//$PER_EFFECTIVEDATE[$index][$PER_ID] =$POH_EFFECTIVEDATE;
					$POH_EFFECTIVEDATE2[$PER_ID] = show_date_format($POH_EFFECTIVEDATE,$DATE_DISPLAY);
				} // end if	
					
				$ARRSORTLEVEL['poh_effectivedate'][] = $POH_EFFECTIVEDATE;		//--------
					
				$PER_SALARY[$PER_ID] = trim($data[PER_SALARY]);
				
				$ARRSORTLEVEL['per_salary'][] = $PER_SALARY[$PER_ID];	//--------
				
				$PER_BIRTHDATE = substr(trim($data[PER_BIRTHDATE]), 0, 10);
				if(trim($PER_BIRTHDATE)){
					$PER_AGE[$PER_ID]=round(date_difference(date("Y-m-d"), trim($PER_BIRTHDATE), "year"));		//floor
					$PER_BIRTHDATE2[$PER_ID] = show_date_format($PER_BIRTHDATE,$DATE_DISPLAY);
				} // end if	
					
				$ARRSORTLEVEL['per_birthdate'][] = $PER_BIRTHDATE;	//--------
					
				$PER_STARTDATE = substr(trim($data[PER_STARTDATE]), 0, 10);
				if(trim($PER_STARTDATE)){
					$PER_WORK[$PER_ID]=round(date_difference(date("Y-m-d"), trim($PER_STARTDATE), "year"));		//floor
					$PER_STARTDATE2[$PER_ID] = show_date_format($PER_STARTDATE,$DATE_DISPLAY);
				} // end if	
				
				$ARRSORTLEVEL['per_startdate'][] = $PER_STARTDATE;	//--------
				
				if(trim($PER_RETIREDATE)){
					$PER_RETIREDATE2[$PER_ID] = show_date_format($PER_RETIREDATE,$DATE_DISPLAY);
				} // end if	
			
				if($DPISDB=="odbc"){
					$cmd = " select		a.EN_CODE, b.EN_SHORTNAME, c.EM_NAME
							   from		
											( PER_EDUCATE a 
										left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
											) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
							   where	a.PER_ID=$PER_ID and (a.EDU_TYPE like '%2%' or a.EDU_TYPE like '%3%')
							   order by 	a.EDU_SEQ ";
				}elseif($DPISDB=="oci8"){
					$cmd = " select		a.EN_CODE, b.EN_SHORTNAME, c.EM_NAME
							   from		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c
							   where	a.PER_ID=$PER_ID and a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and (a.EDU_TYPE like '%2%' or a.EDU_TYPE like '%3%')
							   order by 	a.EDU_SEQ ";
				}elseif($DPISDB=="mysql"){
					$cmd = " select		a.EN_CODE, b.EN_SHORTNAME, c.EM_NAME
							   from		
										(   PER_EDUCATE a
										left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
										)	left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
							   where	a.PER_ID=$PER_ID and (a.EDU_TYPE like '%2%' or a.EDU_TYPE like '%3%')
							   order by 	a.EDU_SEQ ";
				} // end if
				$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
				$PER_EDUCATE[$PER_ID] = "";
				while($data2 = $db_dpis2->get_array()){
					if($PER_EDUCATE[$PER_ID]) $PER_EDUCATE[$PER_ID] .= ", ";
					$PER_EDUCATE[$PER_ID] .= trim($data2[EN_SHORTNAME]);
					$EM_NAME[$PER_ID] = trim($data2[EM_NAME]);
				} // end while
			
				if($DPISDB=="odbc"){
					$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
							   from		PER_DECORATEHIS a
										left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
							   where	a.PER_ID=$PER_ID and DC_TYPE not in (3)
							   order by 	LEFT(trim(a.DEH_DATE), 10) desc ";
				}elseif($DPISDB=="oci8"){
					$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
							   from		PER_DECORATEHIS a, PER_DECORATION b
							   where	a.PER_ID=$PER_ID and DC_TYPE not in (3) and a.DC_CODE=b.DC_CODE(+)
							   order by 	SUBSTR(trim(a.DEH_DATE), 1, 10) desc ";
				}elseif($DPISDB=="mysql"){
					$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
							   from		PER_DECORATEHIS a
										left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
							   where	a.PER_ID=$PER_ID and DC_TYPE not in (3)
							   order by 	LEFT(trim(a.DEH_DATE), 10) desc ";
				} // end if
				$db_dpis2->send_cmd($cmd);
			//	$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PER_DECORATE[$PER_ID] = trim($data2[DC_SHORTNAME]);
				
				$ARRSORTLEVEL['per_decorate'][] = $PER_DECORATE[$PER_ID];	//--------
				
				$cmd2="select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID";
				$db_dpis3->send_cmd($cmd2);
				$data_org=$db_dpis3->get_array();
				$DEPARTMENT_NAME[$PER_ID]=trim($data_org[ORG_NAME]);

				$cmd2="select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis3->send_cmd($cmd2);
				$data_org=$db_dpis3->get_array();
				$ORG_ID_1_NAME[$PER_ID]=trim($data_org[ORG_NAME]);
				if ($ORG_ID_1_NAME[$PER_ID]=="-") $ORG_ID_1_NAME[$PER_ID] = "";

				$cmd = " select	 POH_EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and LEVEL_NO='$LEVEL_NO[$PER_ID]' order by POH_EFFECTIVEDATE";		
				$db_dpis2->send_cmd($cmd);
			//	$db_dpis2->show_error();
	
	
				//��ҵ�.����ѹ�����ҧ �������ش
				// ������� ������ѹ�����·���ش��͹ �������� query �Ѵ仢ͧ�����
				$data2 = $db_dpis2->get_array();
				$CURR_EFFECTIVEDATE_SORT[$LEVEL_NOHIS][$PER_ID] = substr(trim($data2[POH_EFFECTIVEDATE]), 0, 10);
				
				$ARRSORTLEVEL['curr_effectivedate'][] = substr(trim($data2[POH_EFFECTIVEDATE]), 0, 10);	//--------
				
				$CURR_EFFECTIVEDATE[$PER_ID] = show_date_format(trim($data2[POH_EFFECTIVEDATE]),$DATE_DISPLAY);
//				echo "==>  $LEVEL_NOHIS / $PER_ID = ".substr(trim($data2[POH_EFFECTIVEDATE]), 0, 10);
			} 
		} // end while
	} //end for
	
//	print("<pre>");	print_r($CURR_EFFECTIVEDATE);	print("</pre>");

//	$SORTBY=array('poh_effectivedate'=>'asc','per_salary'=>'desc','per_startdate'=>'asc','per_birthdate'=>'asc','per_decorate'=>'desc');

	array_multisort($ARRSORTLEVEL['curr_effectivedate'], SORT_ASC, SORT_STRING,
              					$ARRSORTLEVEL['per_sortlevel'], SORT_NUMERIC, SORT_DESC,
              					$ARRSORTLEVEL['poh_effectivedate'], SORT_ASC, SORT_STRING,
              					$ARRSORTLEVEL['per_salary'], SORT_NUMERIC, SORT_DESC,
              					$ARRSORTLEVEL['per_decorate'], SORT_NUMERIC, SORT_DESC,
              					$ARRSORTLEVEL['per_startdate'], SORT_STRING, SORT_DESC,
              					$ARRSORTLEVEL['per_birthdate'], SORT_STRING, SORT_DESC,
              					$ARRSORTLEVEL['per_id'], SORT_NUMERIC, SORT_ASC
								);

	//�ʴ���¡�÷�����
	$data_count = 0;
	$person_count = 0;
	//foreach($PER_EFFECTIVEDATE as $key=>$value){		//[$LEVEL_NOHIS][$PER_ID]
	foreach($ARRSORTLEVEL['per_id'] as $key=>$value){
				$PER_ID=trim($value);
					
					$person_count++;		
					$arr_content[$data_count][type] = "CONTENT";
					$arr_content[$data_count][name] = $PN_NAME[$PER_ID].$PER_NAME[$PER_ID]." ". $PER_SURNAME[$PER_ID];
					$arr_content[$data_count][position] = $PL_NAME[$PER_ID];
					$arr_content[$data_count][level] = $LEVEL_NAME[$PER_ID] . (($PT_NAME[$PER_ID] != "�����" && $LEVEL_NO[$PER_ID] >= 6)?"$PT_NAME[$PER_ID]":"");
					if ($BKK_FLAG==1)
						$arr_content[$data_count][org] = trim($ORG_ID_1_NAME[$PER_ID] ." ". $ORG_NAME[$PER_ID] ." ". $DEPARTMENT_NAME[$PER_ID]);
					else
						$arr_content[$data_count][org] = trim($ORG_ID_1_NAME[$PER_ID] ." ". $ORG_NAME[$PER_ID]);
					$arr_content[$data_count][posno] = $POS_NO[$PER_ID];
					$arr_content[$data_count][educate] = $PER_EDUCATE[$PER_ID];
					$arr_content[$data_count][poh_effectivedate] = $POH_EFFECTIVEDATE2[$PER_ID];	
					$arr_content[$data_count][curr_effectivedate] = $CURR_EFFECTIVEDATE[$PER_ID];	
					//���дѺ���˹觹��--------------------
					$cmd2="select LEVEL_NAME from PER_LEVEL where LEVEL_NO='".$LEVEL_NOHIS[$PER_ID]."' ";
					$db_dpis3->send_cmd($cmd2);
					$levelname=$db_dpis3->get_array();
					//---------------------------------------------
					$arr_content[$data_count][per_sortlevel]=$levelname[LEVEL_NAME];				//___$LEVEL_NO?level_no_format($LEVEL_NO):"";
					$arr_content[$data_count][per_salary] = $PER_SALARY[$PER_ID]?number_format($PER_SALARY[$PER_ID]):"";
					$arr_content[$data_count][decorate] = $PER_DECORATE[$PER_ID];
					$arr_content[$data_count][per_startdate] = $PER_STARTDATE2[$PER_ID]."\n (".$PER_WORK[$PER_ID]." ��)";
					$arr_content[$data_count][per_birthdate] = $PER_BIRTHDATE2[$PER_ID]."\n (".$PER_AGE[$PER_ID]." ��)";	
					$arr_content[$data_count][per_retiredate]=$PER_RETIREDATE2[$PER_ID];
					$arr_content[$data_count][em_name]=$EM_NAME[$PER_ID];
					$data_count++;
	} //end foreach
	if($person_count>0){	$count_data=$person_count;	}
	if($count_data){
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$xlsRow = $i;
			$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if

		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if

		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$seq = $data_count + 1;
			$name = $arr_content[$data_count][name];
			$position = $arr_content[$data_count][position];
			$level = $arr_content[$data_count][level];
			$org = $arr_content[$data_count][org];
			$posno = $arr_content[$data_count][posno];
			$educate = $arr_content[$data_count][educate];
			$em_name = $arr_content[$data_count][em_name];
			$poh_effectivedate = $arr_content[$data_count][poh_effectivedate];
			$curr_effectivedate = $arr_content[$data_count][curr_effectivedate];
			$per_sortlevel = $arr_content[$data_count][per_sortlevel];
			$per_salary = $arr_content[$data_count][per_salary];
			$decorate = $arr_content[$data_count][decorate];
			$per_startdate = $arr_content[$data_count][per_startdate];
			$per_birthdate = $arr_content[$data_count][per_birthdate];
			$per_retiredate=$arr_content[$data_count][per_retiredate];
						
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($seq):$seq), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, (($NUMBER_DISPLAY==2)?convert2thaidigit($name):$name), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2, (($NUMBER_DISPLAY==2)?convert2thaidigit($position):$position), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 3, (($NUMBER_DISPLAY==2)?convert2thaidigit($poh_effectivedate):$poh_effectivedate), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 4, (($NUMBER_DISPLAY==2)?convert2thaidigit($level):$level), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 5, (($NUMBER_DISPLAY==2)?convert2thaidigit($org):$org), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 6, (($NUMBER_DISPLAY==2)?convert2thaidigit($posno):$posno), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($educate):$educate), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 8, (($NUMBER_DISPLAY==2)?convert2thaidigit($em_name):$em_name), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 9, (($NUMBER_DISPLAY==2)?convert2thaidigit($poh_effectivedate):$poh_effectivedate), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 10, (($NUMBER_DISPLAY==2)?convert2thaidigit($per_sortlevel):$per_sortlevel), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 11, (($NUMBER_DISPLAY==2)?convert2thaidigit($per_salary):$per_salary), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 12, (($NUMBER_DISPLAY==2)?convert2thaidigit($decorate):$decorate), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 13, (($NUMBER_DISPLAY==2)?convert2thaidigit($per_startdate):$per_startdate), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 14, (($NUMBER_DISPLAY==2)?convert2thaidigit($per_birthdate):$per_birthdate), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 15, (($NUMBER_DISPLAY==2)?convert2thaidigit($per_retiredate):$per_retiredate), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
		} // end for				
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ����բ����� *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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