<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$IMG_PATH = "../../attachment/pic_personal/";	
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	include ("rpt_R010017_format.php");	// ��˹������е��������Ѻ�������¹�ٻẺ��§ҹ

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if(!trim($HISTORY_LIST)){ 
		$HISTORY_LIST="ORG|SPECIALSKILLHIS"; //default
	} // end if
	//$arr_rpt_order = explode("|", $RPTORD_LIST);
	$arr_history_name = explode("|", $HISTORY_LIST);
	
	//echo "<pre>".print_r($arr_history_name)."</pre>";

	//print_r($arr_rpt_order);
	//�¡������͹䢷�����͡
	$select_list = "";		$order_by = "";		$heading_name = "";
	//for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		//$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		//switch($REPORT_ORDER){
	for($history_index=0; $history_index<count($arr_history_name); $history_index++){
		$HISTORY_NAME = $arr_history_name[$history_index];
		switch($HISTORY_NAME){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "e.ORG_ID_REF as MINISTRY_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "e.ORG_ID_REF";

				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID";
				
				$heading_name .= " $DEPARTMENT_TITLE";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure == 0) $select_list .= "d.ORG_SEQ_NO, d.ORG_CODE, c.ORG_ID";
				else if($select_org_structure == 1) $select_list .= "d.ORG_SEQ_NO, d.ORG_CODE, a.ORG_ID";

//				if($order_by) $order_by .= ", ";
//				if($select_org_structure == 0) $order_by .= "d.ORG_SEQ_NO, d.ORG_CODE, c.ORG_ID";
//				else if($select_org_structure == 1) $order_by .= "d.ORG_SEQ_NO, d.ORG_CODE, a.ORG_ID";

//				$heading_name .= " $ORG_TITLE";
				break;
			case "EDUCATE" :
				$heading_name .= " ����ѵԡ���֡�� ";
				break;
			case "SPECIALSKILLHIS" :
				$heading_name .= " ��������Ǫҭ";
				break;
		} // end switch case
	} // end for
	if(!trim($select_list)){ 
		if(!$MINISTRY_ID){ $select_list = "e.ORG_ID_REF as MINISTRY_ID";
		}elseif(!$DEPARTMENT_ID){ 
			$select_list = "e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID";
		}else{
			if($select_org_structure == 0) $select_list = "d.ORG_SEQ_NO, d.ORG_CODE, c.ORG_ID";
			else if($select_org_structure == 1) $select_list = "d.ORG_SEQ_NO, d.ORG_CODE, a.ORG_ID";
		}
	} // end if
	if(!trim($order_by)){ 
		if(!$MINISTRY_ID){ $order_by = "e.ORG_ID_REF";
		}elseif(!$DEPARTMENT_ID){ 
			$order_by = "e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID";
		}else{
			if($select_org_structure == 0) $order_by = "d.ORG_SEQ_NO, d.ORG_CODE, c.ORG_ID";
			else if($select_org_structure == 1)  $order_by = "d.ORG_SEQ_NO, d.ORG_CODE, a.ORG_ID";
		}
	} // end if

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE=$search_per_type)";

	$list_type_text = $ALL_REPORT_TITLE;
	
  	if($MINISTRY_ID){
		/****
		unset($arr_department);
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID and OL_CODE='02' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
		***/
		$arr_search_condition[] = "(e.ORG_ID_REF = $MINISTRY_ID) ";
		$list_type_text .= " $MINISTRY_NAME";
	}
	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$list_type_text .= " $DEPARTMENT_NAME";
	}
	if($PROVINCE_CODE){
		/***
		$cmd = " select ORG_ID from PER_ORG where PV_CODE='$PROVINCE_CODE' and OL_CODE='02' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
		***/
		$PROVINCE_CODE = trim($PROVINCE_CODE);
		$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if

//		if(!trim($select_org_structure)){
		if($select_org_structure==1){	//����Ѻ�ͺ���§ҹ
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if(trim($search_org_id)){
			 if($select_org_structure == 0) $arr_search_condition[] = "(c.ORG_ID = $search_org_id or d.ORG_ID = $search_org_id)";	
			 else if($select_org_structure == 1)  $arr_search_condition[] = "(a.ORG_ID = $search_org_id or a.ORG_ID = $search_org_id)";
			 $list_type_text .= " $search_org_name";
		}
		if(trim($search_org_id_1)){
			 if($select_org_structure == 0) $arr_search_condition[] = "(c.ORG_ID_1 = $search_org_id_1 or d.ORG_ID_1 = $search_org_id_1)";	
			else  if($select_org_structure == 1) $arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1 or a.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " $search_org_name_1";
		}
		if(trim($search_org_id_2)){
			 if($select_org_structure == 0) $arr_search_condition[] = "(c.ORG_ID_2 = $search_org_id_2 or d.ORG_ID_2 = $search_org_id_2)";
			else  if($select_org_structure == 1) $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2 or a.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " $search_org_name_2";
		}
//	}

	if(in_array("SELECT", $list_type) ){
		$arr_search_condition[] = "(a.PER_ID in ($SELECTED_PER_ID))";
	}
	if(in_array("CONDITION", $list_type) ){ //�к����͹�
		$search_level_minname = $search_level_maxname ="";
//		if(trim($search_pos_no)) $arr_search_condition[] = "(c.POS_NO like '$search_pos_no%' or d.POEM_NO like '$search_pos_no%')";
		if(trim($search_pos_no)) $arr_search_condition[] = "(trim(c.POS_NO)='$search_pos_no' or trim(d.POEM_NO)='$search_pos_no')";
		if(trim($search_name)) $arr_search_condition[] = "(a.PER_NAME like '$search_name%')";
		if(trim($search_surname)) $arr_search_condition[] = "(a.PER_SURNAME like '$search_surname%')";
		if(trim($search_min_level)){ 
			if($DPISDB=="odbc") $arr_search_condition[] = "(trim(a.LEVEL_NO) >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="oci8") $arr_search_condition[] = "(trim(a.LEVEL_NO) >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="mysql") $arr_search_condition[] = "(trim(a.LEVEL_NO) >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
		
			$cmd = " select LEVEL_NAME, POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$search_min_level' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$F_POSITION_TYPE = $data[POSITION_TYPE];
			$F_LEVEL_NAME =  $data[POSITION_LEVEL];
			if($F_POSITION_TYPE && $F_LEVEL_NAME){ $search_level_minname="$F_POSITION_TYPE $F_LEVEL_NAME"; }
		}
		if(trim($search_max_level)){ 
			if($DPISDB=="odbc") $arr_search_condition[] = "(trim(a.LEVEL_NO) <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="oci8") $arr_search_condition[] = "(trim(a.LEVEL_NO) <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="mysql") $arr_search_condition[] = "(trim(a.LEVEL_NO) <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
			
			$cmd = " select LEVEL_NAME, POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$search_max_level' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$T_POSITION_TYPE = $data[POSITION_TYPE];
			$T_LEVEL_NAME =  $data[POSITION_LEVEL];
			if($T_POSITION_TYPE && $T_LEVEL_NAME){ 
				if($search_level_minname){	$search_level_maxname=" �֧"; }
				$search_level_maxname.="$T_POSITION_TYPE $T_LEVEL_NAME"; 
			}
		}
		if(trim($search_level_minname) || trim($search_level_maxname)){
			$search_level_name = $search_level_minname.$search_level_maxname;
		}
		if(trim($search_pl_code)){
			$arr_search_condition[] = "(c.PL_CODE ='$search_pl_code')"; 	
			$search_pl_name = $search_pl_name; 
		}
	} // end if
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(",", $search_per_status) ."))";

	//���͡੾����§ҹ �ѡ������ ��йѡ����ͧ <default>
	if(!trim($search_pl_code)){	
		if ($BKK_FLAG==1)
			$arr_search_condition[] = "(c.PL_CODE in ('10109','11001','31004'))";
		else
			$arr_search_condition[] = "(c.PL_CODE in ('010108','010307','510108','510307'))";
		$search_pl_name = "�ѡ������ ��йѡ����ͧ"; 
	}
	//���͡੾�к����õ� ��к������٧ <default>
	if(!trim($search_level_name)){
		$arr_search_condition[] = "(trim(a.LEVEL_NO) in ('M1','M2'))";
		$search_level_name = "�����õ� ��к������٧"; 
	}
	//-----------------------------------------------------------------------------------------------	
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "�ٻẺ����͡��§ҹ : $list_type_text";
	$report_title = "��������§ҹ$search_pl_name ���˹觻�����$search_level_name �ʴ��زԡ���֡����Ф�������Ǫҭ�����";
	$report_code = "H17";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);

	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

 	// ��˹���ҵ�������;���� ��ǹ�����§ҹ
		$ws_width1 = array(60,20,20,20,20,20,20,20);
		$ws_head_line1 = array("��ǹ�Ҫ���","���˹�","�дѺ�ز�","�����ز�","�Ң��Ԫ��͡","ʶҺѹ","�����","�����³");
		$ws_head_line2 = array("����-ʡ��","","����֡��","����֡��","","����֡��","","(1 �.�.)");
		$ws_colmerge_line1 = array(0,0,0,0,0,0,0,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TRL","TRL","TRL","TRL","TRL","TRL","TRL");
		$ws_border_line2 = array("LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B");
		$ws_fontfmt_line2 = array("","","","","","","","");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C");
		$ws_headalign_line2 = array("C","C","C","C","C","C","C","C");
	// ����á�˹���ҵ�������;���� ��ǹ�����§ҹ

	$xlsRow =  0;
	$colseq=0;
	for($i=0; $i < count($ws_width1); $i++) {
		$worksheet->set_column($colseq, $colseq, $ws_width1[$i]);
		$colseq++;
	}

 	function print_header() {
		global $worksheet, $xlsRow;
		global $ws_head_line, $ws_head_line1, $ws_head_line2;
		global $ws_colmerge_line, $ws_colmerge_line1, $ws_colmerge_line2;
		global $ws_width1;
		global $ws_border_line, $ws_border_line1, $ws_border_line2;
		global $ws_fontfmt_line, $ws_fontfmt_line1, $ws_fontfmt_line2;
		global $ws_headalign_line, $ws_headalign_line1, $ws_headalign_line2;
		global $arr_column_sel, $arr_column_map;

			// loop ����� head ��÷Ѵ��� 1
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($ws_width1); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {	// �����੾�з�����͡����ʴ�
					$buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($colseq == $colshow_cnt-1)));
					$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
					$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
					$colseq++;
				}
			}
	
			// loop ����� head ��÷Ѵ��� 2
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($ws_width1); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {	// �����੾�з�����͡����ʴ�
					$buff = explode("|",doo_merge_cell($ws_head_line2[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]], $pgrp, ($colseq == $colshow_cnt-1)));
					$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
					$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
					$colseq++;
				}
			}
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PL_CODE;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :	
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(e.ORG_ID_REF = $MINISTRY_ID)";
				break;
				case "DEPARTMENT" :	
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
				break;
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(c.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(c.ORG_ID = 0 or c.ORG_ID is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}
				break;
				case "LINE" :
					if($PL_CODE) $arr_addition_condition[] = "(trim(a.PL_CODE) = '$PL_CODE')";
					else $arr_addition_condition[] = "(trim(a.PL_CODE) = '$PL_CODE' or a.PL_CODE is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
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

	//�Ҫ�����ǹ�Ҫ��� 
	if($search_per_type==1){
		$select_list.=",c.POS_ID,c.POS_NO,c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2";
		$pos_tb="PER_POSITION";
		$join_tb="a.POS_ID=c.POS_ID";
	}elseif($search_per_type==2){	
		$select_list.=",c.POEM_ID,c.POEM_NO as EMP_POS_NO, c.PN_CODE as EMP_PL_CODE,c.ORG_ID as EMP_ORG_ID, c.ORG_ID_1 as EMP_ORG_ID_1, c.ORG_ID_2 as EMP_ORG_ID_2";
		$pos_tb="PER_POS_EMP";
		$join_tb="a.POEM_ID=c.POEM_ID";
	}elseif($search_per_type==3){ 
		$select_list.=",c.POEMS_ID,c.POEMS_NO as EMPSER_POS_NO, c.EP_CODE as EMPSER_PL_CODE,c.ORG_ID as EMPSER_ORG_ID, c.ORG_ID_1 as EMPSER_ORG_ID_1, c.ORG_ID_2 as EMPSER_ORG_ID_2";
		$pos_tb="PER_POS_EMPSER";
		$join_tb="a.POEMS_ID=c.POEMS_ID";
	}

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE,  LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_TYPE, a.PER_CARDNO,
											$select_list
						 from			PER_PRENAME b inner join 
										(((( 	
										PER_PERSONAL a 
										left join $pos_tb c on ($join_tb) 
										) left join PER_ORG d on (c.ORG_ID=d.ORG_ID)
										) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
										) left join PER_MGT f on (c.PM_CODE=f.PM_CODE)
										) on (trim(a.PN_CODE)=trim(b.PN_CODE))
										$search_condition
						 order by	$order_by, f.PM_SEQ_NO,a.PER_NAME, a.PER_SURNAME ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			a.PER_ID, a.LEVEL_NO, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, 
											SUBSTR(trim(a.PER_RETIREDATE), 1, 10) as PER_RETIREDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,  
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_TYPE, a.PER_CARDNO,
											$select_list
						 from			PER_PERSONAL a, PER_PRENAME b, $pos_tb c,PER_ORG d,PER_ORG e, PER_MGT f
						 where		trim(a.PN_CODE)=trim(b.PN_CODE) and $join_tb(+)
											and c.ORG_ID=d.ORG_ID(+) and a.DEPARTMENT_ID=e.ORG_ID(+) and c.PM_CODE=f.PM_CODE(+)
											$search_condition
						 order by	$order_by, f.PM_SEQ_NO,NLSSORT(a.PER_NAME,'NLS_SORT=THAI_DICTIONARY'), NLSSORT(a.PER_SURNAME,'NLS_SORT=THAI_DICTIONARY') ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE,  LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_TYPE, a.PER_CARDNO,
											$select_list
						 from			PER_PRENAME b inner join 
										(((( 	
										PER_PERSONAL a 
										left join $pos_tb c on ($join_tb) 
										) left join PER_ORG d on (c.ORG_ID=d.ORG_ID)
										) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
										) left join PER_MGT f on (c.PM_CODE=f.PM_CODE)
										) on (trim(a.PN_CODE)=trim(b.PN_CODE))
										$search_condition
						 order by	$order_by, f.PM_SEQ_NO,a.PER_NAME, a.PER_SURNAME ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("c.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "<br>cmd ::: $cmd ($count_data)<br>"; //no rows
	//$db_dpis->show_error();
//################################################

$first_order = 1;	// order ��  0 = COUNTRY �ѧ�����ӹǳ ����� order ��� 1 (MINISTRY) ��͹
if($count_data){	
	$data_count = 0;
	initialize_parameter(0);

		$xlsRow = 0;
		$colshow_cnt=0;		// �Ҩӹǹ column ����ʴ���ԧ
		for($i=0; $i<count($arr_column_sel); $i++){
			if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
		}
		/**$temp_report_title = "$REF_NAME||$NAME||$report_title";
		$arr_title = explode("||", $temp_report_title);**/
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			for($j=0; $j < $colshow_cnt-1; $j++) 
				$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} //for($i=0; $i<count($arr_title); $i++){

		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "C", "", 1));
			for($j=0; $j < $colshow_cnt-1; $j++) 
				$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} //if($company_name){
		
		print_header();

		// ��˹���ҵ�������;���� ��ǹ������
			$ws_headalign_line1 = array("C","C","C","C","C","C","C","C");
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("L","L","L","L","L","L","L","C");
			$wsdata_border_1 = array("","","B","B","B","B","B","B");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","");
		// ����˹���ҵ�������;���� ��ǹ������

	while($data = $db_dpis->get_array()){
		$data_count++;
		$PER_ID = $data[PER_ID];
		$PER_TYPE = $data[PER_TYPE];
	
		if($PER_TYPE==1){
				$POS_ID = $data[POS_ID];
				$POS_NO = $data[POS_NO];
				$PL_CODE = trim($data[PL_CODE]);
				$PT_CODE = trim($data[PT_CODE]);
				$ORG_ID = $data[ORG_ID];
				$ORG_ID_1 = $data[ORG_ID_1];
				$ORG_ID_2 = $data[ORG_ID_2];

				$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PL_NAME]);

				$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE) = '$PT_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PT_NAME = trim($data2[PT_NAME]);
			}elseif($PER_TYPE==2){
				$POS_ID = $data[POEM_ID];
				$POS_NO = $data[EMP_POS_NO];
				$PL_CODE = trim($data[EMP_PL_CODE]);
				$ORG_ID = $data[EMP_ORG_ID];
				$ORG_ID_1 = $data[EMP_ORG_ID_1];
				$ORG_ID_2 = $data[EMP_ORG_ID_2];

				$cmd = " select PN_NAME from PER_POS_NAME where trim(PN_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PN_NAME]);
			}elseif($PER_TYPE==3){
				$POS_ID = $data[POEMS_ID];
				$POS_NO = $data[EMPSER_POS_NO];
				$PL_CODE = trim($data[EMPSER_PL_CODE]);
				$ORG_ID = $data[EMPSER_ORG_ID];
				$ORG_ID_1 = $data[EMPSER_ORG_ID_1];
				$ORG_ID_2 = $data[EMPSER_ORG_ID_2];

				$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[EP_NAME]);
			} 
			$ORG_NAME = "";
			if($ORG_ID){
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$ORG_NAME = trim($data2[ORG_NAME]);
			}
			$ORG_NAME_1 = "";
			if($ORG_ID_1){
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_1 ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$ORG_NAME_1 = trim($data2[ORG_NAME]);
			}
			$ORG_NAME_2 = "";
			if($ORG_ID_2){
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_2 ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$ORG_NAME_2 = trim($data2[ORG_NAME]);
			}
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO = '$LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			//$LEVEL_NAME = trim(str_replace("������", "", $data2[LEVEL_NAME]));
			$LEVEL_NAME = trim($data2[LEVEL_NAME]);
			$POSITION_LEVEL = $data2[POSITION_LEVEL];
			if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
			
			if ($have_pic) $img_file = show_image($PER_ID,1); //1 = $db_dpis2,$db_dpis3 2 = $db_dpis3,$db_dpis4
			$img_file = file_exists($img_file) ? $img_file : "";
			
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$FULLNAME = ($PN_NAME)."$PER_NAME $PER_SURNAME";
			$PER_CARDNO = $data[PER_CARDNO];	
			
			//$img_file = "";
			//if($PER_CARDNO && file_exists($IMG_PATH.$PER_CARDNO.".jpg")) $img_file = $IMG_PATH.$PER_CARDNO.".jpg";			
/*
			$PER_RETIREDATE = trim($data[PER_RETIREDATE]);
			if($PER_RETIREDATE){
				$arr_temp = explode("-", substr($PER_RETIREDATE, 0, 10));
				$PER_RETIREDATE = ($arr_temp[2] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
			} // end if
*/
			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			if($PER_BIRTHDATE){
				$PER_BIRTHDATE = show_date_format($PER_BIRTHDATE,$DATE_DISPLAY);
			} // end if

			//���ѹ���³����
			$PER_RETIREDATE = show_date_format($data[PER_RETIREDATE],$DATE_DISPLAY);
			$PER_SALARY = $data[PER_SALARY];
			$PER_MGTSALARY = $data[PER_MGTSALARY];
			$PER_STARTDATE = show_date_format($data[PER_STARTDATE],$DATE_DISPLAY);

		for($history_index=0; $history_index<count($arr_history_name); $history_index++){
				$HISTORY_NAME = $arr_history_name[$history_index];
				switch($HISTORY_NAME){
					case "MINISTRY" :
							if($MINISTRY_ID != trim($data[MINISTRY_ID])){ 
								$MINISTRY_ID = trim($data[MINISTRY_ID]);
								if($MINISTRY_ID != ""){
									$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
									if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
									$db_dpis2->send_cmd($cmd);
		//							$db_dpis2->show_error();
									$data2 = $db_dpis2->get_array();
									$MINISTRY_NAME = $data2[ORG_NAME];
								} // end if

								$xlsRow++;
								$colseq=0;
								for($i=0; $i < count($ws_width1); $i++) {
									if ($i==0)
										$worksheet->write($xlsRow, $colseq, "$MINISTRY_NAME", set_format("xlsFmtTitle", "B", "L", "", 0));
									else
										$worksheet->write($xlsRow, $colseq, "", set_format("xlsFmtTitle", "B", "L", "", 0));
						//				echo "$xlsRow $colseq ($i)-> $report_title<br>";
									$colseq++;
								}
							} // end if
					break;
					
					case "DEPARTMENT" :	
							if($DEPARTMENT_ID != trim($data[DEPARTMENT_ID])){ //���
								$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
								if($DEPARTMENT_ID != ""){
									$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
									if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
									$db_dpis2->send_cmd($cmd);
		//							$db_dpis2->show_error();
									$data2 = $db_dpis2->get_array();
									$DEPARTMENT_NAME = $data2[ORG_NAME];
								} // end if

								$xlsRow++;
								$colseq=0;
								for($i=0; $i < count($ws_width1); $i++) {
									if ($i==0)
										$worksheet->write($xlsRow, $colseq, "$DEPARTMENT_NAME", set_format("xlsFmtTitle", "B", "L", "", 0));
									else
										$worksheet->write($xlsRow, $colseq, "", set_format("xlsFmtTitle", "B", "L", "", 0));
						//				echo "$xlsRow $colseq ($i)-> $report_title<br>";
									$colseq++;
								}
							} // end if
					break;
	
				case "ORG" :
						if($ORG_ID != trim($data[ORG_ID])){
							$ORG_ID = trim($data[ORG_ID]);
							if($ORG_ID != ""){
								$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
								if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
								$db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_NAME = $data2[ORG_NAME];
							} // end if
	
							$xlsRow++;
							$colseq=0;
							for($i=0; $i < count($ws_width1); $i++) {
								if ($i==0)
									$worksheet->write($xlsRow, $colseq, "$ORG_NAME", set_format("xlsFmtTitle", "B", "L", "", 0));
								else
									$worksheet->write($xlsRow, $colseq, "", set_format("xlsFmtTitle", "B", "L", "", 0));
					//				echo "$xlsRow $colseq ($i)-> $report_title<br>";
								$colseq++;
							}
//							echo "ORG_NAME=$ORG_NAME<br>";
						} // end if
				break;
			
				case "EDUCATE" :
							$border = "";
							$arr_data = (array) null;
							$arr_data[] = $data_count." ".$FULLNAME;	//	$NAME;
							$arr_data[] = (trim($PL_NAME)?($PL_NAME ." (". $POSITION_LEVEL . (($PT_NAME != "�����" && $LEVEL_NO >= 6)?"$PT_NAME":"")):" ".$LEVEL_NAME).")";
/*	
								if($have_pic && $img_file){
									$fix_width = 50; 
									$RTF->open_line();
									$image_x = ($imgx + (($image_w+2) * $j) + 1);
									list($width, $height) = getimagesize($img_file); 
									$new_h = $height / $width * $fix_width;
									$new_ratio = floor(100 / $height * $fix_width); 
//									$fsnum = substr($RTF->_font_size(),3);
//									$w_chr = floor($fix_width / $fsnum);
									$w_chr = 7;
									$RTF->open_line();
									$RTF->set_font($font,14);
									$RTF->color("1");	// 0=DARKGRAY
//									$image_x = ($pdf->x + 170);		$image_y = ($pdf->y - 33);		$image_w = 25;			$image_h = 35;
//									$pdf->Image($img_file, $image_x, $image_y, $image_w, $image_h);
									$RTF->cellImage(($img_file ? $img_file : ""), "$new_ratio", "$w_chr", "left", "8", "");
									$RTF->close_line();
								} // end if
*/
							$EN_NAME = "";	$EM_NAME = "";	$INS_NAME = "";	$CT_NAME = "";
//							$where = " and a.EDU_TYPE like '%2%'";
//							if ($BKK_FLAG==1) $where = " and a.EL_CODE in ('001', '002', '003', '005') ";
//							else $where = " and a.EL_CODE in ('40', '50', '60', '70', '80', '90') ";
							$where = " and f.EL_TYPE > '1' ";
							if($DPISDB=="odbc"){
								$cmd = " 	select		b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME, f.EL_NAME, a.EDU_INSTITUTE, a.CT_CODE_EDU
									 	from			((((PER_EDUCATE a
													left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
													) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
													) left join PER_COUNTRY e on (d.CT_CODE=e.CT_CODE)
												)left join PER_EDUCLEVEL f on (trim(a.EL_CODE)=trim(f.EL_CODE))
										 where		a.PER_ID=$PER_ID $where
										 order by		iif(isnull(a.EDU_SEQ),0,a.EDU_SEQ) desc, a.EDU_ENDYEAR desc ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select		b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME, f.EL_NAME, a.EDU_INSTITUTE, a.CT_CODE_EDU
										from			PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d, PER_COUNTRY e,PER_EDUCLEVEL f
										where		a.PER_ID=$PER_ID and
													a.EN_CODE=b.EN_CODE(+) and 
													a.EM_CODE=c.EM_CODE(+) and 
													trim(a.EL_CODE)=trim(f.EL_CODE(+)) and
													a.INS_CODE=d.INS_CODE(+) and 
													d.CT_CODE=e.CT_CODE(+) $where
										order by		to_number(nvl(a.EDU_SEQ,0)) desc, a.EDU_ENDYEAR desc ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " 	select		b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME, f.EL_NAME, a.EDU_INSTITUTE, a.CT_CODE_EDU
									 	from			((((PER_EDUCATE a
													left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
													) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
													) left join PER_COUNTRY e on (d.CT_CODE=e.CT_CODE)
												)left join PER_EDUCLEVEL f on (trim(a.EL_CODE)=trim(f.EL_CODE))
										 where		a.PER_ID=$PER_ID $where
										 order by		a.EDU_SEQ+0 desc, a.EDU_ENDYEAR desc ";			
							} // end if
							
							$count_educatehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_educatehis){
								$educatehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$educatehis_count++;
									$EN_NAME = trim($data2[EN_NAME]);
									$EM_NAME = trim($data2[EM_NAME]);
									$EL_NAME = trim($data2[EL_NAME]);
									$INS_NAME = trim($data2[INS_NAME]);
									$CT_NAME = trim($data2[CT_NAME]);
									if (!$INS_NAME) $INS_NAME = trim($data2[EDU_INSTITUTE]);
									if (!$CT_NAME) {
										$CT_CODE_EDU = trim($data2[CT_CODE_EDU]);
										$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE_EDU' ";
										$db_dpis1->send_cmd($cmd);
			//							$db_dpis1->show_error();
										$data1 = $db_dpis1->get_array();
										$CT_NAME = $data1[CT_NAME];
									}

									if ($educatehis_count==2) {	// ��ҧ��Ҫ��� ���˹��������ͧ�ʴ�㹺�÷Ѵ��� 2 ��е�� � �
										$arr_data = (array) null;
										$arr_data[] = "";		// �ѧ�Ѵ/����
										$arr_data[] = "";		// ���˹�
									}
									$arr_data[] = $EL_NAME;	// �дѺ�ز�|����֡��
									$arr_data[] = $EN_NAME;	// �����ز�|����֡��
									$arr_data[] = $EM_NAME;	// �Ң��Ԫ��͡|
									$arr_data[] = $INS_NAME;	// ʶҺѹ|����֡��
									$arr_data[] = $CT_NAME;	// �����
									$arr_data[] = $PER_RETIREDATE;	// �����³|(1 �.�.)

//									$data_align = array("L", "L", "C", "C", "C", "C", "C", "C");
//									$style = array("B", "B", "", "", "", "", "", "");
									$a_border = array("", "", "B", "B", "B", "B", "B", "B");

									$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
									$xlsRow++;
									$colseq=0;
									for($i=0; $i < count($arr_column_map); $i++) {
										if ($arr_column_sel[$arr_column_map[$i]]==1) {
											if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ��ҹ��� map �����º��������
											else $ndata = $arr_data[$arr_column_map[$i]];
											$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $a_border[$i], $wsdata_colmerge_1[$arr_column_map[$i]]));
											$colseq++;
										}
									}
									$data_count++;
								} // end while
							}else{
								$xlsRow++;
								$colseq=0;
								for($i=0; $i < count($ws_width1); $i++) {
									if ($i==0)
										$worksheet->write($xlsRow, $colseq, "********** ����բ����� **********", set_format("xlsFmtTitle", "B", "C", "", 1));
									else
										$worksheet->write($xlsRow, $colseq, "", set_format("xlsFmtTitle", "B", "C", "", 1));
						//				echo "$xlsRow $colseq ($i)-> $report_title<br>";
									$colseq++;
								}
							} // end if
					break;
						
					case "SPECIALSKILLHIS" :  //��������Ǫҭ�����
							$SS_NAME = "";
							$SPS_EMPHASIZE = "";
							
							if($DPISDB=="odbc"){
								$cmd = " select			b.SS_NAME, a.SPS_EMPHASIZE
												 from			PER_SPECIAL_SKILL a, PER_SPECIAL_SKILLGRP b
												 where		a.PER_ID=$PER_ID and
																	a.SS_CODE=b.SS_CODE
												 order by		a.SPS_ID ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			b.SS_NAME, a.SPS_EMPHASIZE
												 from			PER_SPECIAL_SKILL a, PER_SPECIAL_SKILLGRP b
												 where		a.PER_ID=$PER_ID and
																	a.SS_CODE=b.SS_CODE
												 order by		a.SPS_ID ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			b.SS_NAME, a.SPS_EMPHASIZE
												 from			PER_SPECIAL_SKILL a, PER_SPECIAL_SKILLGRP b
												 where		a.PER_ID=$PER_ID and
																	a.SS_CODE=b.SS_CODE
												 order by		a.SPS_ID ";		
							} // end if
							$count_specialskillhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_specialskillhis){
								$xlsRow++;
								$colseq=0;
								for($i=0; $i < count($ws_width1); $i++) {
									if ($i==0)
										$worksheet->write($xlsRow, $colseq, "��������Ǫҭ�����", set_format("xlsFmtTitle", "B", "L", "", 0));
									else
										$worksheet->write($xlsRow, $colseq, "", set_format("xlsFmtTitle", "B", "L", "", 0));
						//				echo "$xlsRow $colseq ($i)-> $report_title<br>";
									$colseq++;
								}
								$specialskillhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$specialskillhis_count++;
									$SS_NAME = trim($data2[SS_NAME]);
									$SPS_EMPHASIZE = trim($data2[SPS_EMPHASIZE]);
									$specialskillhis = "     ".$SS_NAME." ".$SPS_EMPHASIZE;
									
									$border = "";
									$xlsRow++;
									$colseq=0;
									for($i=0; $i < count($ws_width1); $i++) {
										if ($i==0)
											$worksheet->write($xlsRow, $colseq, "$specialskillhis", set_format("xlsFmtTitle", "", "L", "", 0));
										else
											$worksheet->write($xlsRow, $colseq, "", set_format("xlsFmtTitle", "", "L", "", 0));
							//				echo "$xlsRow $colseq ($i)-> $report_title<br>";
										$colseq++;
									}
								} // end while
							}else{
								$xlsRow++;
								$colseq=0;
								for($i=0; $i < count($ws_width1); $i++) {
									if ($i==0)
										$worksheet->write($xlsRow, $colseq, "********** ����բ����� **********", set_format("xlsFmtTitle", "B", "C", "", 1));
									else
										$worksheet->write($xlsRow, $colseq, "", set_format("xlsFmtTitle", "B", "C", "", 1));
						//				echo "$xlsRow $colseq ($i)-> $report_title<br>";
									$colseq++;
								}
							} // end if
						break;
				} // end switch
			} // end for
			//if($data_count < $count_data) $pdf->AddPage();
		} // end while

	}else{
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($ws_width1); $i++) {
			if ($i==0)
				$worksheet->write($xlsRow, $colseq, "********** ����բ����� **********", set_format("xlsFmtTitle", "B", "C", "", 1));
			else
				$worksheet->write($xlsRow, $colseq, "", set_format("xlsFmtTitle", "B", "C", "", 1));
//				echo "$xlsRow $colseq ($i)-> $report_title<br>";
			$colseq++;
		}
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