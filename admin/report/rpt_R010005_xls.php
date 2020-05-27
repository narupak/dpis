<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R010005_format.php");	// ��˹������е��������Ѻ�������¹�ٻẺ��§ҹ

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = 1)";
	$arr_search_condition[] = "(a.PER_STATUS = 1)";
//	$arr_search_condition[] = "(g.PT_GROUP = '3')";
//	$arr_search_condition[] = "(a.LEVEL_NO in ('M1', 'M2','D2'))";
	$arr_level_no_condi = (array) null;
	foreach ($LEVEL_NO as $search_level_no)
	{
        if ($search_level_no) $arr_level_no_condi[] = "'$search_level_no'";
//		echo "search_level_no=$search_level_no<br>";
	}
	if (count($arr_level_no_condi) > 0) $arr_search_condition[] = "(trim(a.LEVEL_NO) in (".implode(",",$arr_level_no_condi)."))";

//	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  $f_all = true; else $f_all = false;
	
	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {
		$f_all = true; 
		$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
	} else $f_all = false;	
	
	$list_type_text = $ALL_REPORT_TITLE;
	
	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
	} // end if
	$arr_rpt_order_setnew = explode("|", $RPTORD_LIST);
	$arr_rpt_order_tmp_setnew = array_unique($arr_rpt_order_setnew);	//�Ѵ��ҷ���ӡѹ�͡ �����������鹢����ū�ӡѹ 2 ��	������§���˹� index ���� 0 1 2 ...
	foreach($arr_rpt_order_tmp_setnew as $key=>$value){
		$arr_rpt_order[]=$value;
	}
	unset($arr_rpt_order_setnew);	unset($arr_rpt_order_tmp_setnew);

	$select_list = "";
	$group_by = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
		case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "d.ORG_ID_REF as MINISTRY_ID, e.ORG_NAME as MINISTRY_NAME";	

				if($group_by) $group_by .= ", ";
				$group_by .= "d.ORG_ID_REF, e.ORG_NAME "; 
				
				if($order_by) $order_by .= ", ";
				$order_by .= "d.ORG_ID_REF"; 
				
				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "d.ORG_SEQ_NO, d.ORG_CODE, a.DEPARTMENT_ID, d.ORG_NAME as DEPARTMENT_NAME ";

				if($group_by) $group_by .= ", ";
				$group_by .= "d.ORG_SEQ_NO, d.ORG_CODE, a.DEPARTMENT_ID, d.ORG_NAME "; 
				
				if($order_by) $order_by .= ", ";
				$order_by .= "d.ORG_SEQ_NO, d.ORG_CODE, a.DEPARTMENT_ID";

				$heading_name .= " $DEPARTMENT_TITLE";
				break; 
		}
	}

	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ǹ��ҧ
		$list_type_text = "��ǹ��ҧ";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='01')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='01')";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ǹ�����Ҥ
		$list_type_text = "��ǹ�����Ҥ";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='03')";
	}
	if(in_array("PER_ORG", $list_type)){
		$list_type_text = "";
		if($select_org_structure==1){	//����Ѻ�ͺ���§ҹ
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if(trim($search_org_id)){ 
			if($select_org_structure == 0) $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
			else if($select_org_structure == 1)  $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
		} // end if
		if(trim($search_org_id_1)){ 
			if($select_org_structure == 0) $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			else if($select_org_structure == 1)  $arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			if($select_org_structure == 0) $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			else if($select_org_structure == 1)  $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
		} // end if
	}
	if(in_array("PER_LINE", $list_type)){
		// ��§ҹ
		$list_type_text = "";
		if($search_per_type==1 && trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "(trim(b.PL_CODE)='$search_pl_code')";
			$list_type_text .= "$search_pl_name";
		}elseif($search_per_type==2 && trim($search_pn_code)){
			$search_pn_code = trim($search_pn_code);
			$arr_search_condition[] = "(trim(b.PN_CODE)='$search_pn_code')";
			$list_type_text .= "$search_pn_name";
		} // end if
		elseif($search_per_type==3 && trim($search_ep_code)){
			$search_ep_code = trim($search_ep_code);
			$arr_search_condition[] = "(trim(b.EP_CODE)='$search_ep_code')";
			$list_type_text .= "$search_ep_name";
		} // end if
	}
	if(in_array("PER_COUNTRY", $list_type)){
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
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//�óշ����� ������������͡ check box list_type ���
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	
		//���Ҩҡ
		if(trim($search_pt_name)){
			//���������˹�
			$list_type_text .= " ���˹觻�����$search_pt_name";
			$arr_search_condition[] = "(j.LEVEL_NO LIKE '%$search_pt_code%')";
		}
		//���˹����§ҹ
		if($search_per_type==1 && trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "(trim(b.PL_CODE)='$search_pl_code')";
			$list_type_text .= " $PL_TITLE $search_pl_name";
		}elseif($search_per_type==2 && trim($search_pn_code)){
			$search_pn_code = trim($search_pn_code);
			$arr_search_condition[] = "(trim(b.PN_CODE)='$search_pn_code')";
			$list_type_text .= " $POS_EMP_TITLE $search_pn_name";
		} // end if
	} // end if
///	if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "�ٻẺ����͡��§ҹ : $list_type_text";
	if ($BKK_FLAG==1)
		$report_title = "��ª��͹ѡ�����è�ṡ���˹��§ҹ";
	else
		$report_title = "��ª��͹ѡ�����è�ṡ�����ǹ�Ҫ���";
	$report_code = "H05";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	// ��˹���ҵ�������;���� ��ǹ�����§ҹ
	if ($have_remark) {	
    	$ws_head_line1 = array("$MINISTRY_TITLE / $DEPARTMENT_TITLE","$PM_TITLE","$PL_TITLE","���������˹�","�дѺ���˹�","�ѹ�������дѺ","�ѹ��ç���˹�","�����³����","$REMARK_TITLE");
    	$ws_head_line2 = array("���� - ʡ��","","","","","�Ѩ�غѹ","�Ѩ�غѹ","","");
		$ws_colmerge_line1 = array(0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR");
		$ws_border_line2 = array("LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C");
		$ws_width = array(40,35,35,12,12,12,12,10,20);
	} else {
    	$ws_head_line1 = array("$MINISTRY_TITLE / $DEPARTMENT_TITLE","$PM_TITLE","$PL_TITLE","���������˹�","�дѺ���˹�","�ѹ�������дѺ","�ѹ��ç���˹�","�����³����");
    	$ws_head_line2 = array("���� - ʡ��","","","","","�Ѩ�غѹ","�Ѩ�غѹ","");
		$ws_colmerge_line1 = array(0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR");
		$ws_border_line2 = array("LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C");
		$ws_width = array(40,35,35,12,12,12,12,10);
	}
	// ����á�˹���ҵ�������;���� ��ǹ�����§ҹ	

	// �ӹǹ���º��º��� $ws_width ���� ��º�Ѻ $heading_width
//		echo "bf..ws_width=".implode(",",$ws_width)."<br>";
		$sum_hdw = 0;
		$sum_wsw = 0;
		for($h = 0; $h < count($heading_width); $h++) {
			$sum_wsw += $ws_width[$h];	// ws_width �ѧ����� �ǡ �������ҧ ��Ƿ��١�Ѵ����
			if ($arr_column_sel[$h]==1) {
				$sum_hdw += $heading_width[$h];
			}
		}
		// �ѭ�ѵ����ҧ��   �ʹ����������ҧ column � heading_width ��º�Ѻ �ʹ���� ws_width
		//                                ���� column � ws_width[$h] = sum(ws_width) /sum(heading_width) * heading_width[$h]
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1) {
				$ws_width[$h] = $sum_wsw / $sum_hdw * $heading_width[$h];
			}
		}
//		echo "af..ws_width=".implode(",",$ws_width)."<br>";
	// �������º��Ҥӹǹ���º��º��� $ws_width ���� ��º�Ѻ $heading_width
	
	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		global $MINISTRY_TITLE , $DEPARTMENT_TITLE; 
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width;
		global $ws_head_line1, $ws_head_line2;
		global $ws_colmerge_line1;
		global $ws_border_line1, $ws_border_line2;
		global $ws_fontfmt_line1, $ws_headalign_line1, $ws_width;

		// loop ��˹��������ҧ�ͧ column
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// �����੾�з�����͡����ʴ�
				$worksheet->set_column($colseq, $colseq, $ws_width[$arr_column_map[$i]]);
//				echo "$i-map[".$arr_column_map[$i]."], width=".$ws_width[$arr_column_map[$i]]."<br />";
				$colseq++;
			}
		}
		// loop ����� head ��÷Ѵ��� 1
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// �����੾�з�����͡����ʴ�
				$buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
		// loop ����� head ��÷Ѵ��� 2
		$xlsRow++;
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// �����੾�з�����͡����ʴ�
				$buff = explode("|",doo_merge_cell($ws_head_line2[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
	} // function		

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
								b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_SEQ_NO, h.PM_NAME, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, 
								LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE,
								a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, $select_list, m.POSITION_LEVEL, m.POSITION_TYPE, m.LEVEL_SHORTNAME, a.PER_REMARK
				   from			(
										(
											(
												(
													(
														(
															(
																PER_PERSONAL a
																inner join PER_POSITION b on (a.POS_ID=b.POS_ID)
															) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
													) inner join PER_ORG e on (d.ORG_ID_REF=e.ORG_ID)
												) inner join PER_LINE f on (b.PL_CODE=f.PL_CODE)
											) left join PER_MGT h on (b.PM_CODE=h.PM_CODE)
										) left join PER_PRENAME i on (a.PN_CODE=i.PN_CODE)
									) left join PER_LEVEL m on(a.LEVEL_NO=m.LEVEL_NO)
				   where		$search_condition
				   group by		a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, m.POSITION_LEVEL, m.POSITION_TYPE, m.LEVEL_SHORTNAME, b.POS_NO, 
								b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_SEQ_NO, h.PM_NAME, LEFT(trim(a.PER_BIRTHDATE), 10), 
								LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_RETIREDATE), 10),
								a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, $group_by, m.LEVEL_SEQ_NO, a.PER_REMARK
				   order by $order_by, h.PM_SEQ_NO, m.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, 
									a.PER_NAME, a.PER_SURNAME, a.PER_SALARY desc, 
				   				LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_BIRTHDATE), 10) "; 
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace("where", "and", $search_condition);
		$cmd = " select			a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
								b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_SEQ_NO, h.PM_NAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, 
								SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, SUBSTR(trim(a.PER_RETIREDATE), 1, 10) as PER_RETIREDATE,
								a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, $select_list, m.POSITION_LEVEL, m.POSITION_TYPE, m.LEVEL_SHORTNAME, a.PER_REMARK		
				   from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_ORG d, PER_ORG e, PER_LINE f, PER_MGT h, 
				   				PER_PRENAME i, PER_LEVEL m
				   where		a.POS_ID=b.POS_ID and b.ORG_ID=c.ORG_ID and a.DEPARTMENT_ID=d.ORG_ID and d.ORG_ID_REF=e.ORG_ID 
								and b.PL_CODE=f.PL_CODE and b.PM_CODE=h.PM_CODE(+) and a.PN_CODE=i.PN_CODE(+) and a.LEVEL_NO=m.LEVEL_NO(+)
								$search_condition
				   group by		a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, m.POSITION_LEVEL, m.POSITION_TYPE, m.LEVEL_SHORTNAME, b.POS_NO, 
								b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_SEQ_NO, h.PM_NAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), 
								SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_RETIREDATE), 1, 10),
								a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, $group_by, m.LEVEL_SEQ_NO, a.PER_REMARK
				   order by	$order_by, h.PM_SEQ_NO, m.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, 
									NLSSORT(a.PER_NAME,'NLS_SORT=THAI_DICTIONARY'), NLSSORT(a.PER_SURNAME,'NLS_SORT=THAI_DICTIONARY'), a.PER_SALARY desc, 
				   				SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
								b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_SEQ_NO, h.PM_NAME, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, 
								LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE,
								a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, $select_list, m.POSITION_LEVEL, m.POSITION_TYPE, m.LEVEL_SHORTNAME, a.PER_REMARK
				   from			(
										(
											(
												(
													(
														(
															(
																PER_PERSONAL a
																inner join PER_POSITION b on (a.POS_ID=b.POS_ID)
															) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
													) inner join PER_ORG e on (d.ORG_ID_REF=e.ORG_ID)
												) inner join PER_LINE f on (b.PL_CODE=f.PL_CODE)
											) left join PER_MGT h on (b.PM_CODE=h.PM_CODE)
										) left join PER_PRENAME i on (a.PN_CODE=i.PN_CODE)
									)left join PER_LEVEL m on (a.LEVEL_NO=m.LEVEL_NO)
				   where		$search_condition
				   group by		a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, m.POSITION_LEVEL, m.POSITION_TYPE, m.LEVEL_SHORTNAME, b.POS_NO, 
								b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_SEQ_NO, h.PM_NAME, LEFT(trim(a.PER_BIRTHDATE), 10), 
								LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_RETIREDATE), 10),
								a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, $group_by, m.LEVEL_SEQ_NO, a.PER_REMARK
				   order by	$order_by, h.PM_SEQ_NO, m.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, 
									a.PER_NAME, a.PER_SURNAME, a.PER_SALARY desc, 
				   					LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_BIRTHDATE), 10) "; 
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	$MINISTRY_ID = -1;
	$DEPARTMENT_ID = -1;
	$cnt_rpt_order = count($arr_rpt_order);
	while($data = $db_dpis->get_array()){		
		if($MINISTRY_ID != $data[MINISTRY_ID]){
			$MINISTRY_ID = $data[MINISTRY_ID];
			$MINISTRY_NAME = $data[MINISTRY_NAME];
			
//			if($f_all){
			$arr_content[$data_count][type] = "MINISTRY";
			$arr_content[$data_count][name] = $MINISTRY_NAME;

			$data_row = 0;
			$data_count++;
//			}
		} // end if
		
		if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){
			$DEPARTMENT_ID = $data[DEPARTMENT_ID];
			$DEPARTMENT_NAME = $data[DEPARTMENT_NAME];
			
			$arr_content[$data_count][type] = "DEPARTMENT";
			$arr_content[$data_count][name] = str_repeat(" ", (($cnt_rpt_order - 1) * 5)) . $DEPARTMENT_NAME;

			$data_row = 0;
			$data_count++;
		} // end if

		$data_row++;

		$PER_ID = $data[PER_ID];
		$PN_NAME = $data[PN_NAME];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$POSITION_LEVEL = trim($data[POSITION_LEVEL]);
		if ($BKK_FLAG==1)
			$POSITION_TYPE = trim($data[LEVEL_SHORTNAME]);
		else
			$POSITION_TYPE = trim($data[POSITION_TYPE]);
		$PL_CODE = trim($data[PL_CODE]);
		$PL_NAME = $data[PL_NAME];
		$PM_CODE = trim($data[PM_CODE]);
		$PM_NAME = $data[PM_NAME];
		$TMP_ORG_NAME = $data[ORG_NAME];

		$ORG_ID_1 = $data[ORG_ID_1];
		$TMP_ORG_NAME_1 = "";
		if($ORG_ID_1){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_ORG_NAME_1 = $data2[ORG_NAME];
		}
		if ($PM_NAME=="�������Ҫ��èѧ��Ѵ" || $PM_NAME=="�ͧ�������Ҫ��èѧ��Ѵ" || $PM_NAME=="��Ѵ�ѧ��Ѵ" || $PM_NAME=="���˹���ӹѡ�ҹ�ѧ��Ѵ") {
			$PM_NAME .= $TMP_ORG_NAME;
			$PM_NAME = str_replace("�ѧ��Ѵ�ѧ��Ѵ", "�ѧ��Ѵ", $PM_NAME); 
			$PM_NAME = str_replace("�ӹѡ�ҹ�ѧ��Ѵ�ӹѡ�ҹ�ѧ��Ѵ", "�ӹѡ�ҹ�ѧ��Ѵ", $PM_NAME); 
		} elseif ($PM_NAME=="��������") {
			$PM_NAME .= $TMP_ORG_NAME_1;
			$PM_NAME = str_replace("����ͷ��ӡ�û���ͧ�����", "�����", $PM_NAME); 
		}
		
		$PT_CODE = trim($data[PT_CODE]);
		$PT_NAME = $data[PT_NAME];
		
		$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
		if($PER_BIRTHDATE){
			$arr_temp = explode("-", $PER_BIRTHDATE);
			$PER_RETIRE_YEAR = ($arr_temp[0] + 543) + (($arr_temp[1] >= 10)?61:60);
			$PER_BIRTHDATE = show_date_format($PER_BIRTHDATE,$DATE_DISPLAY);
		}

		$PER_SALARY = $data[PER_SALARY];
		$PER_REMARK = trim($data[PER_REMARK]);
		
		// �ѹ�������дѺ�Ѩ�غѹ 
		if ($LEVEL_NO=="M2") $LEVEL_NO_C = "10";
		elseif ($LEVEL_NO=="M1") $LEVEL_NO_C = "09";
		elseif ($LEVEL_NO=="D2") $LEVEL_NO_C = "09";
		else $LEVEL_NO_C = $LEVEL_NO;
		if ($MFA_FLAG==1 && $PM_CODE) 
			$cmd = " select POH_EFFECTIVEDATE
							from   PER_POSITIONHIS a, PER_MOVMENT b
							where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and LEVEL_NO='$LEVEL_NO' and PM_CODE='$PM_CODE' and  
										(MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
							order by POH_EFFECTIVEDATE ";
		else
			$cmd = " select POH_EFFECTIVEDATE
							from   PER_POSITIONHIS a, PER_MOVMENT b
							where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and (LEVEL_NO='$LEVEL_NO' or LEVEL_NO='$LEVEL_NO_C') and 
										(MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
							order by POH_EFFECTIVEDATE ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$LEVEL_EFFECTIVEDATE = show_date_format($data2[POH_EFFECTIVEDATE],$DATE_DISPLAY);

		// �ѹ��ç���˹觻Ѩ�غѹ		
		$cmd = " select POH_EFFECTIVEDATE  
						from PER_POSITIONHIS a, PER_MOVMENT b 
						where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and 
										(MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
						order by POH_EFFECTIVEDATE desc";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POSITION_EFFECTIVEDATE = show_date_format($data2[POH_EFFECTIVEDATE],$DATE_DISPLAY);

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][name] = "$data_row." .str_repeat(" ", 3) . $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][pl_name] = $PL_NAME;
		$arr_content[$data_count][pm_name] = $PM_NAME;
		$arr_content[$data_count][pt_name] = $POSITION_TYPE;
		$arr_content[$data_count][level_name] = $POSITION_LEVEL;
		$arr_content[$data_count][level_effectivedate] = $LEVEL_EFFECTIVEDATE;
		$arr_content[$data_count][position_effectivedate] = $POSITION_EFFECTIVEDATE;
		$arr_content[$data_count][retireyear] = $PER_RETIRE_YEAR;
		$arr_content[$data_count][remark] = $PER_REMARK;
				
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$colshow_cnt=0;		// �Ҩӹǹ column ����ʴ���ԧ
		for($i=0; $i<count($arr_column_sel); $i++){
			if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
		}

		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			for($j=0; $j < $colshow_cnt-1; $j++) 
				$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} //for($i=0; $i<count($arr_title); $i++){

		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			for($j=0; $j < $colshow_cnt-1; $j++) 
				$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} //if($company_name){

		print_header();		

		// ��˹���ҵ�������;���� ��ǹ������
		if ($have_remark) {	
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("L","L","L","C","C","C","C","C","C");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","");
		} else {
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("L","L","L","C","C","C","C","C");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","");
		}
		// ����˹���ҵ�������;���� ��ǹ������

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$PL_NAME = $arr_content[$data_count][pl_name];
			$PM_NAME = $arr_content[$data_count][pm_name];
			$PT_NAME = $arr_content[$data_count][pt_name];
			$POSITION_LEVEL = $arr_content[$data_count][level_name];
			$LEVEL_EFFECTIVEDATE = $arr_content[$data_count][level_effectivedate];
			$POSITION_EFFECTIVEDATE = $arr_content[$data_count][position_effectivedate];
			$RETIREYEAR = $arr_content[$data_count][retireyear];
			$REMARK = $arr_content[$data_count][remark];
			
			if($REPORT_ORDER == "MINISTRY"){
				$ORG_NAME = $arr_content[$data_count][name];
         		$arr_data = (array) null;
				$arr_data[] = "$NAME";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
            	$arr_data[] = "";
            	$arr_data[] = "";
		    	if ($have_remark)  $arr_data[] =  "";

				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
					
				$xlsRow++;
				$colseq=0;
				for($i=0; $i < count($arr_column_map); $i++) {
					if ($arr_column_sel[$arr_column_map[$i]]==1) {
						if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ��ҹ��� map �����º��������
						else $ndata = $arr_data[$arr_column_map[$i]];
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
						$colseq++;
					}
				}
			}elseif($REPORT_ORDER == "DEPARTMENT"){
				if (trim($NAME)) {
					$ORG_NAME = $arr_content[$data_count][name];
					$arr_data = (array) null;
					$arr_data[] = "$NAME";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
	            	$arr_data[] = "";
			    	if ($have_remark)  $arr_data[] =  "";

					$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
						
					$xlsRow++;
					$colseq=0;
					for($i=0; $i < count($arr_column_map); $i++) {
						if ($arr_column_sel[$arr_column_map[$i]]==1) {
							if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ��ҹ��� map �����º��������
							else $ndata = $arr_data[$arr_column_map[$i]];
							$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
							$colseq++;
						}
					}
				}
			}elseif($REPORT_ORDER == "CONTENT"){
		       	$arr_data = (array) null;
				$arr_data[] = $NAME;
				$arr_data[] = $PM_NAME;
				$arr_data[] = $PL_NAME;
				$arr_data[] = $PT_NAME;
				$arr_data[] = $POSITION_LEVEL;
				$arr_data[] = $LEVEL_EFFECTIVEDATE;
				$arr_data[] = $POSITION_EFFECTIVEDATE;
            	$arr_data[] = $RETIREYEAR;
				if ($have_remark) $arr_data[] = $REMARK;

				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
					
				$xlsRow++;
				$colseq=0;
				for($i=0; $i < count($arr_column_map); $i++) {
					if ($arr_column_sel[$arr_column_map[$i]]==1) {
						if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ��ҹ��� map �����º��������
						else $ndata = $arr_data[$arr_column_map[$i]];
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
						$colseq++;
					}
				}
			} // end if
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
		if ($have_remark) 
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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