<?php
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R008002_xls_format.php");	// ��˹������е��������Ѻ�������¹�ٻẺ��§ҹ

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($search_per_type==1 || $search_per_type==5){ 
		$position_table = "PER_POSITION";
		$position_join = "e.POS_ID=g.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "g.PL_CODE=i.PL_CODE";
		$line_name = "i.PL_NAME";
                
                $PT_CODES = ", g.PT_CODE, j.PT_NAME";
                $TABLE_PT = ", PER_TYPE j";
                $TABLE_JOIN = " and g.PT_CODE=j.PT_CODE(+) ";
                
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "e.POEM_ID=g.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "g.PN_CODE=i.PN_CODE";
		$line_name = "i.PN_NAME";
                
                $PT_CODES = "";
                $TABLE_PT = "";
                $TABLE_JOIN = "";
                
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "e.POEMS_ID=g.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "g.EP_CODE=i.EP_CODE";
		$line_name = "i.EP_NAME";
                
                $PT_CODES = "";
                $TABLE_PT = "";
                $TABLE_JOIN = "";
                
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "e.POT_ID=g.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "g.TP_CODE=i.TP_CODE";
		$line_name = "i.TP_NAME";
                
                $PT_CODES = "";
                $TABLE_PT = "";
                $TABLE_JOIN = "";
                
	} // end if
	
	//$search_per_status[] = 1; /*���*/
        /*Release 5.1.0.3 Begin*/
        if(count($search_per_status)==0){
            $search_per_status[] = -1; //����ա�����͡
        }
        /*Release 5.1.0.3 End*/

	$search_condition = "";
	$arr_search_condition[] = "(e.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(e.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	if($SELECTED_PER_ID){	$arr_search_condition[] = "(a.PER_ID in ($SELECTED_PER_ID))";	}
	if(trim($search_date_min)){
		$arr_temp = explode("/", $search_date_min);
		$search_date_min = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
		$show_date_min = ($arr_temp[0] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];
//		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.INV_DATE), 10) >= '$search_date_min')";
//		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.INV_DATE), 1, 10) >= '$search_date_min')";
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.PUN_STARTDATE), 10) >= '$search_date_min')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.PUN_STARTDATE), 1, 10) >= '$search_date_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(a.PUN_STARTDATE), 10) >= '$search_date_min')";
	} // end if
	if(trim($search_date_max)){
		$arr_temp = explode("/", $search_date_max);
		$search_date_max = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
		$show_date_max = ($arr_temp[0] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];
//		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.INV_DATE), 10) <= '$search_date_max')";
//		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.INV_DATE), 1, 10) <= '$search_date_max')";
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.PUN_ENDDATE), 10) <= '$search_date_max' or a.PUN_ENDDATE is NULL)";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.PUN_ENDDATE), 1, 10) <= '$search_date_max' or a.PUN_ENDDATE is NULL)";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(a.PUN_ENDDATE), 10) <= '$search_date_max' or a.PUN_ENDDATE is NULL)";
	} // end if

	$list_type_text = $ALL_REPORT_TITLE;

	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(e.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
	}elseif($MINISTRY_ID){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
		if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

		$arr_search_condition[] = "(e.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		$list_type_text .= " - $MINISTRY_NAME";
	}elseif($PROVINCE_CODE){
		$PROVINCE_CODE = trim($PROVINCE_CODE);
		$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "�ٻẺ����͡��§ҹ : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||��ª���$PERSON_TYPE[$search_per_type]���١ŧ�ɷҧ�Թ��";
	$report_code = "R0802";

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
	$ws_head_line1 = array("�ӴѺ���","���� - ʡ��","$CARDNO_TITLE","���˹�","$ORG_TITLE","�Ţ��������ͺ�ǹ","�ҹ�����Դ","�óդ����Դ","�Ţ������觷ҧ�Թ��","�ɷ�����Ѻ","�ѹ����ռźѧ�Ѻ��","�����˵�");
	$ws_colmerge_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0);
	$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0);
	$ws_border_line1 = array("TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR");
	$ws_border_line2 = array("LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
	$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B");
	$ws_headalign_line1 = array("CT","CT","CT","CT","CT","CT","CT","CT","CT","CT","CT","CT");
	$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1);
	$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0);
	$ws_width = array(8,30,10,30,30,10,19,19,10,19,19,30);
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
		global $workbook, $worksheet, $xlsRow;
		global $heading_name;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width, $column_function;
		global $ws_head_line1, $ws_head_line2;
		global $ws_colmerge_line1, $ws_colmerge_line2;
		global $ws_border_line1, $ws_border_line2;
		global $ws_wraptext_line1, $ws_rotate_line1;
		global $ws_fontfmt_line1, $ws_headalign_line1, $ws_width;

		 $ws_font_color = array(0,0,0,0,0,0,0,0,0,0,0,0);
		 $ws_fill_color = array(0,0,0,0,0,0,0,0,0,0,0,0);

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
		$result = wswrite_aggregate_merge($workbook, $worksheet, $xlsRow, "xlsFmtTableHeader", $ws_fontfmt_line1, $ws_head_line1, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line1, $ws_colmerge_line1, $ws_headalign_line1, $ws_wraptext_line1, $ws_rotate_line1, $ws_font_color, $ws_fill_color);
		// loop ����� head ��÷Ѵ��� 1
/*		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// �����੾�з�����͡����ʴ�
				$buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}*/
		// loop ����� head ��÷Ѵ��� 2
//		$xlsRow++;
//		$result = wswrite_aggregate_merge($workbook, $worksheet, $xlsRow, "xlsFmtTableDetail", $ws_fontfmt_line2, $ws_head_line2, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line2, $ws_colmerge_line2, $ws_headalign_line1, $ws_wraptext_line1, $ws_rotate_line1, $ws_font_color, $ws_fill_color);
		// loop ����� head ��÷Ѵ��� 2
/*		$xlsRow++;
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// �����੾�з�����͡����ʴ�
				$buff = explode("|",doo_merge_cell($ws_head_line2[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}*/
	} // function		

	if($DPISDB=="odbc"){
			$cmd = " select			e.PER_ID, f.PN_NAME, e.PER_NAME, e.PER_SURNAME, $line_name as PL_NAME, e.LEVEL_NO, h.ORG_NAME,
												d.CR_NAME, c.CRD_NAME, k.PEN_NAME, a.PUN_STARTDATE, e.DEPARTMENT_ID, e.PER_CARDNO, INV_NO, PUN_NO, PUN_REMARK
							 from			(	
							 						(
														(
															(
																(
																	(
																		(
																			
																				PER_PUNISHMENT a 
																				inner join PER_CRIME_DTL c on (a.CRD_CODE=c.CRD_CODE)
																			) inner join PER_CRIME d on (c.CR_CODE=d.CR_CODE)
																		) inner join PER_PERSONAL e on (a.PER_ID=e.PER_ID)
																	) left join PER_PRENAME f on (e.PN_CODE=f.PN_CODE)
																) left join $position_table g on ($position_join)
															) left join PER_ORG h on (g.ORG_ID=h.ORG_ID)
														) left join $line_table i on ($line_join)
													
												) left join PER_PENALTY k on (a.PEN_CODE=k.PEN_CODE)
							 $search_condition
							 order by		LEFT(trim(a.PUN_STARTDATE), 10) ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			e.PER_ID, f.PN_NAME, e.PER_NAME, e.PER_SURNAME, $line_name as PL_NAME, e.LEVEL_NO $PT_CODES , h.ORG_NAME,
												d.CR_NAME, c.CRD_NAME, k.PEN_NAME, a.PUN_STARTDATE, e.DEPARTMENT_ID, e.PER_CARDNO, INV_NO, PUN_NO, PUN_REMARK
							 from			PER_PUNISHMENT a, PER_CRIME_DTL c, PER_CRIME d, PER_PERSONAL e, PER_PRENAME f, 
							 					$position_table g, PER_ORG h, $line_table i $TABLE_PT , PER_PENALTY k
							 where		a.CRD_CODE=c.CRD_CODE and c.CR_CODE=d.CR_CODE and a.PEN_CODE=k.PEN_CODE(+)
							 					and a.PER_ID=e.PER_ID and e.PN_CODE=f.PN_CODE(+)
							 					and $position_join(+) and g.ORG_ID=h.ORG_ID and $line_join $TABLE_JOIN
												$search_condition
							 order by		SUBSTR(trim(a.PUN_STARTDATE), 1, 10) ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			e.PER_ID, f.PN_NAME, e.PER_NAME, e.PER_SURNAME, $line_name as PL_NAME, e.LEVEL_NO, h.ORG_NAME,
												d.CR_NAME, c.CRD_NAME, k.PEN_NAME, a.PUN_STARTDATE, e.DEPARTMENT_ID, e.PER_CARDNO, INV_NO, PUN_NO, PUN_REMARK
							 from			(	
							 						(
														(
															(
																(
																	(
																		(
																			
																				PER_PUNISHMENT a 
																				inner join PER_CRIME_DTL c on (a.CRD_CODE=c.CRD_CODE)
																			) inner join PER_CRIME d on (c.CR_CODE=d.CR_CODE)
																		) inner join PER_PERSONAL e on (a.PER_ID=e.PER_ID)
																	) left join PER_PRENAME f on (e.PN_CODE=f.PN_CODE)
																) left join $position_table g on ($position_join)
															) left join PER_ORG h on (g.ORG_ID=h.ORG_ID)
														) left join $line_table i on ($line_join)
													
												) left join PER_PENALTY k on (a.PEN_CODE=k.PEN_CODE) 
							 $search_condition
							 order by		LEFT(trim(a.PUN_STARTDATE), 10) ";
		}
		
	if($select_org_structure==1) { 
		$cmd = str_replace("g.ORG_ID", "e.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$PER_ID = $data[PER_ID];
		$PN_NAME = $data[PN_NAME];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PL_NAME = $data[PL_NAME];
		$LEVEL_NO = $data[LEVEL_NO];
                
		if($PT_CODES){
                    $PT_CODE = trim($data[PT_CODE]);
                    $PT_NAME =  trim($data[PT_NAME]);
                }else{
                    $PT_CODE = "";
                    $PT_NAME = "";
                }
                
		$ORG_NAME = $data[ORG_NAME];
		$CR_NAME = $data[CR_NAME];
		$CRD_NAME = $data[CRD_NAME];
		$PEN_NAME = $data[PEN_NAME];
		$EFFECTIVE_DATE = show_date_format($data[PUN_STARTDATE],$DATE_DISPLAY);
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$INV_NO = trim($data[INV_NO]);
		$PUN_NO = trim($data[PUN_NO]);
		$PUN_REMARK = trim($data[PUN_REMARK]);
		
		$PER_DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$cmd = "select ORG_NAME from PER_ORG where ORG_ID=$PER_DEPARTMENT_ID";
		$db_dpis3->send_cmd($cmd);
//		$db_dpis->show_error();
		$data3 = $db_dpis3->get_array();
		$PER_DEPARTMENT_NAME = $data3[ORG_NAME];

		if($DEPARTMENT_ID){
			$ORG_PER_NAME = $ORG_NAME;
		}else{
			$ORG_PER_NAME = $PER_DEPARTMENT_NAME." / ".$ORG_NAME;
		}
		
		$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
		$db_dpis3->send_cmd($cmd);
//		$db_dpis->show_error();
		$data3 = $db_dpis3->get_array();
		$LEVEL_NAME=$data3[LEVEL_NAME];
		$POSITION_LEVEL = $data3[POSITION_LEVEL];
		if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = "$data_row.";
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][cardno] = $PER_CARDNO;
		$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "�����" && $LEVEL_NO >= 6)?"$PT_NAME":"");
		$arr_content[$data_count][org_name] = $ORG_PER_NAME;
		$arr_content[$data_count][inv_no] = $INV_NO;
		$arr_content[$data_count][cr_name] = $CR_NAME;
		$arr_content[$data_count][crd_name] = $CRD_NAME;
		$arr_content[$data_count][pun_no] = $PUN_NO;
		$arr_content[$data_count][pen_name] = $PEN_NAME;
		$arr_content[$data_count][effective_date] = $EFFECTIVE_DATE;
		$arr_content[$data_count][remark] = $PUN_REMARK;

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
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("TC","TL","TC","TL","TL","TL","TL","TL","TL","TL","TC","TL");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","","","","");
			$wsdata_wraptext = array(1,1,1,1,1,1,1,1,1,1,1,1);
			$wsdata_rotate = array(0,0,0,0,0,0,0,0,0,0,0,0);

			 $ws_font_color = array(0,0,0,0,0,0,0,0,0,0,0,0);
			 $ws_fill_color = array(0,0,0,0,0,0,0,0,0,0,0,0);
		// ����˹���ҵ�������;���� ��ǹ������

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME_1 = $arr_content[$data_count][name];
			$NAME_2 = $arr_content[$data_count][cardno];
//			echo "data_count=$data_count , $NAME_1 , $NAME_2<br>";
			$NAME_3 = $arr_content[$data_count][position];
			$NAME_4 = $arr_content[$data_count][org_name];
			$NAME_5 = $arr_content[$data_count][inv_no];
			$NAME_6 = $arr_content[$data_count][cr_name];
			$NAME_7 = $arr_content[$data_count][crd_name];
			$NAME_8 = $arr_content[$data_count][pun_no];
			$NAME_9 = $arr_content[$data_count][pen_name];
			$NAME_10 = $arr_content[$data_count][effective_date];
			$NAME_11 = $arr_content[$data_count][remark];

			$arr_data = (array) null;
			$arr_data[] = $ORDER;
			$arr_data[] = $NAME_1;
			$arr_data[] = $NAME_2;
			$arr_data[] = $NAME_3;
			$arr_data[] = $NAME_4;
			$arr_data[] = $NAME_5;
			$arr_data[] = $NAME_6;
			$arr_data[] = $NAME_7;
			$arr_data[] = $NAME_8;
			$arr_data[] = $NAME_9;
			$arr_data[] = $NAME_10;
			$arr_data[] = $NAME_11;

			$xlsRow++;
			$result = wswrite_aggregate_merge($workbook, $worksheet, $xlsRow, "xlsFmtTableDetail", $wsdata_fontfmt_2, $arr_data, $column_function, $arr_column_sel, $arr_column_map, $wsdata_border_1, $wsdata_colmerge_1, $wsdata_align_1, $wsdata_wraptext, $wsdata_rotate, $ws_font_color, $ws_fill_color);
/*
			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ��ҹ��� map �����º��������
					else $ndata = $arr_data[$arr_column_map[$i]];
					$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					$colseq++;
				}
			}
*/
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