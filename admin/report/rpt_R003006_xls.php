<?php
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

        if($_GET[NUMBER_DISPLAY]){$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];}
	include ("../report/rpt_function.php");
	
	include ("rpt_R003006_format.php");	// ��੾�����ǹ����ŧ COLUMN_FORMAT ��ҹ��
        
        $DEBUG=0;
        if($DEBUG==1){
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        }
        
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_code = "b.PL_CODE";
		$line_name = "b.PL_NAME";
		$line_short_name = "PL_SHORTNAME";
                $line_active = "PL_ACTIVE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "b.PN_CODE";
		$line_name = "b.PN_NAME";
                $line_active = "PN_ACTIVE";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "b.EP_CODE";
		$line_name = "b.EP_NAME";
                $line_active = "EP_ACTIVE";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "b.TP_CODE";
		$line_name = "b.TP_NAME";
                $line_active = "TP_ACTIVE";
	} // end
        
        function get_name_org($org_id,$name,$select_org_structure){
            global $db_dpis2;
            if($name=='��з�ǧ' && $select_org_structure==1){
                $cmd = "WITH p AS (
                            SELECT ORG_ID_REF from PER_ORG WHERE ORG_ID = ".$org_id." AND ORG_ACTIVE = 1 
                        ) , TB_NAME AS (
                            SELECT a.ORG_NAME FROM PER_ORG a, p WHERE a.ORG_ID=p.ORG_ID_REF
                        ) SELECT ORG_NAME FROM TB_NAME";
            }else{
                $cmd  = " SELECT ORG_NAME FROM PER_ORG WHERE ORG_ID = ".$org_id." AND ORG_ACTIVE = 1 ";
            }    
            if($name != '��з�ǧ' && $select_org_structure==1){
                $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
            }
            $db_dpis2->send_cmd($cmd);
            $data = $db_dpis2->get_array();
            if($data[ORG_NAME]){
                return $data[ORG_NAME];
            }else{
                return '����к�'.$name;
            }
        }
        function get_name_line($pl_code,$name){
            global $db_dpis2 ,$line_name ,$line_code , $line_active ,$line_table;
            $cmd  = " SELECT $line_name as PL_NAME from $line_table b where $line_code = ".$pl_code."  and  $line_active = 1 ";
            $db_dpis2->send_cmd($cmd);
            $data = $db_dpis2->get_array();
            if($data[PL_NAME]){
                return $data[PL_NAME];
            }else{
                return '����к�'.$name;
            }
        }
        
	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {
		$f_all = true; 
		$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
        } else {$f_all = false;	}

	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)==""){ $RPTORD_LIST .= "ORG_1|";}
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)==""){ $RPTORD_LIST .= "ORG_2|";}
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!=""){ $RPTORD_LIST .= "LINE|";}
	} // end if
	$arr_rpt_order_setnew = explode("|", $RPTORD_LIST);
	$arr_rpt_order_tmp_setnew = array_unique($arr_rpt_order_setnew);	//�Ѵ��ҷ���ӡѹ�͡ �����������鹢����ū�ӡѹ 2 ��	������§���˹� index ���� 0 1 2 ...
	foreach($arr_rpt_order_tmp_setnew as $key=>$value){
		$arr_rpt_order[]=$value;
	}
	unset($arr_rpt_order_setnew);	unset($arr_rpt_order_tmp_setnew);

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
            $REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
            switch($REPORT_ORDER){
                case "MINISTRY" : 
                    if($select_list){ $select_list .= ", ";}
                    $select_list .= " p.MINISTRY_ID";	

                    if($select_list1){ $select_list1 .= ", ";}
                    $select_list1 .= " NVL(p.MINISTRY_ID,NULL) as MINISTRY_ID";

                    if($order_by){ $order_by .= ", ";}
                    $order_by .= " NVL(p.MINISTRY_ID,-1)";

                    if($group_by){ $group_by .= ", ";}
                    $group_by .= " p.MINISTRY_ID ";
                    $heading_name .= " $MINISTRY_TITLE";
                break; 
                case "DEPARTMENT" : 
                    if($select_list){ $select_list .= ", ";}
                    $select_list .= " p.DEPARTMENT_ID";	

                    if($select_list1){ $select_list1 .= ", ";}
                    $select_list1 .= " NVL(p.DEPARTMENT_ID,NULL) as DEPARTMENT_ID ";

                    if($order_by){ $order_by .= ", ";}
                    $order_by .= " NVL(p.DEPARTMENT_ID,-1)";

                    if($group_by){ $group_by .= ", ";}
                    $group_by .= " p.DEPARTMENT_ID ";
                    $heading_name .= " $DEPARTMENT_TITLE";

                break; 
                case "ORG" :
                    if($select_list){ $select_list .= ", ";}
                    $select_list .= " p.ORG_ID";	

                    if($select_list1){ $select_list1 .= ", ";}
                    $select_list1 .= " NVL(p.ORG_ID,NULL) as ORG_ID ";

                    if($order_by){ $order_by .= ", ";}
                    $order_by .= " NVL(p.ORG_ID,-1)";

                    if($group_by){ $group_by .= ", ";}
                    $group_by .= " p.ORG_ID ";
                    $heading_name .= " $ORG_TITLE";

                break;
                case "ORG_1" :
                    if($select_list){ $select_list .= ", ";}
                    $select_list .= " p.ORG_ID_1 ";	

                    if($select_list1){ $select_list1 .= ", ";}
                    $select_list1 .= " NVL(p.ORG_ID_1,NULL) as ORG_ID_1 ";

                    if($order_by){ $order_by .= ", ";}
                    $order_by .= " NVL(p.ORG_ID_1,-1)";

                    if($group_by){ $group_by .= ", ";}
                    $group_by .= " p.ORG_ID_1 ";
                    $heading_name .= " $ORG_TITLE1";

                break;
                case "ORG_2" :
                    if($select_list){ $select_list .= ", ";}
                    $select_list .= " p.ORG_ID_2 ";	

                    if($select_list1){ $select_list1 .= ", ";}
                    $select_list1 .= " NVL(p.ORG_ID_2,NULL) as ORG_ID_2 ";

                    if($order_by){ $order_by .= ", ";}
                    $order_by .= " NVL(p.ORG_ID_2,-1)";

                    if($group_by){ $group_by .= ", ";}
                    $group_by .= " p.ORG_ID_2 ";
                    $heading_name .= " $ORG_TITLE2";

                break;
                case "LINE" :
                    if($select_list){ $select_list .= ", ";}
                    $select_list .= " p.PL_CODE ";	

                    if($select_list1){ $select_list1 .= ", ";}
                    $select_list1 .= " NVL(p.PL_CODE,NULL) as PL_CODE ";

                    if($order_by){ $order_by .= ", ";}
                    $order_by .= " NVL(p.PL_CODE,-1)";

                    if($group_by){ $group_by .= ", ";}
                    $group_by .= " p.PL_CODE ";
                    $heading_name .= " $line_title";

                break;
            } // end switch case
	} // end for
	if(!trim($select_list)){ 
            $select_list = " p.MINISTRY_ID,p.DEPARTMENT_ID,p.ORG_ID,p.ORG_ID_1,p.ORG_ID_2,p.PL_CODE ";
	} // end if
        
        if(!trim($select_list1)){ 
            $select_list1 = " NVL(p.MINISTRY_ID,NULL) as MINISTRY_ID, NVL(p.DEPARTMENT_ID,NULL) as DEPARTMENT_ID, NVL(p.ORG_ID,NULL) as ORG_ID,NVL(p.ORG_ID_1,NULL) as ORG_ID_1,NVL(p.ORG_ID_2,NULL) as ORG_ID_2, NVL(p.PL_CODE,NULL) as PL_CODE ";
	} // end if
        
	if(!trim($order_by)){ 
            $order_by = " NVL(p.MINISTRY_ID,-1),NVL(p.DEPARTMENT_ID,-1),NVL(p.ORG_ID,-1),NVL(p.ORG_ID_1,-1),NVL(p.ORG_ID_2,-1),NVL(p.PL_CODE,-1) asc ";
	} // end if
        
        if(!trim($group_by)){ 
            $group_by = " p.MINISTRY_ID,p.DEPARTMENT_ID,p.ORG_ID,p.ORG_ID_1,p.ORG_ID_2,p.PL_CODE ";
	} // end if
        $arr_select = explode(",",$select_list);
        $select_list_case = '';
        if($arr_select){
            $select_list_case .= 'case ';
            for($i=0;$i < count($arr_select)+1;$i++){
                $select_list_case .= 'when ';
                    $con_and = 'AND';
                    $str_null = 'is not null';
                    for($j=0;$j < count($arr_select)+1;$j++){
                        if($i<=$j){
                            $str_null = 'is null';
                        }
                        if(count($arr_select) == $i){
                            $str_null = 'is not null';
                        }
                        if(count($arr_select)-1 == $j){
                            $con_and = '';
                            $str_null = '';
                        }$select_list_case .= $arr_select[$j].' '.$str_null.' '.$con_and.' ';
                    }
                if($i==0){
                    $select_list_case .= "then '".$i."' ";    
                }else{
                    $select_list_case .= "then '".$i."|/".$arr_rpt_order[$i-1]."' ";    
                }
            }
            $select_list_case .= 'end';
        }
        //echo $select_list_case;
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";

	$list_type_text = $ALL_REPORT_TITLE;
	include ("../report/rpt_condition3.php");
	
	$company_name = "�ٻẺ����͡��§ҹ : ". ($select_org_structure==1?"�ç���ҧ����ͺ���§ҹ - ":"�ç���ҧ��������� - ") ."$list_type_text";
	$show_budget_year = (($NUMBER_DISPLAY==2)?convert2thaidigit($search_budget_year):$search_budget_year);
	$report_title = "$DEPARTMENT_NAME||�ѵ�ҡ������͡�ͧ$PERSON_TYPE[$search_per_type]㹻է�����ҳ $show_budget_year";
	$report_code = "R0306";

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
        $ws_head_line1 = array("$heading_name","<**1**>�ӹǹ$PERSON_TYPE[$search_per_type]","<**1**>","<**1**>","<**1**>","<**2**>�ѵ�� (������)","<**2**>�ѵ�� (������)");
        $ws_head_line2 = array("","�鹻�","���","�͡","��鹻�","���","�͡");
        $ws_colmerge_line1 = array(0,1,1,1,1,1,1);
        $ws_colmerge_line2 = array(0,0,0,0,0,0,0);
        $ws_border_line1 = array("TLR","TRL","TRL","TRL","TRL","TRL","TRL");
        $ws_border_line2 = array("LBR","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL");
        $ws_fontfmt_line1 = array("B","B","B","B","B","B","B");
        $ws_headalign_line1 = array("C","C","C","C","C","C","C");
        $ws_width = array(80,10,10,10,10,10,10);
	// ����á�˹���ҵ�������;���� ��ǹ�����§ҹ	

	// �ӹǹ���º��º��� $ws_width ���� ��º�Ѻ $heading_width
        //echo "bf..ws_width=".implode(",",$ws_width)."<br>";
        $sum_hdw = 0;
        $sum_wsw = 0;
        for($h = 0; $h < count($heading_width); $h++) {
                $sum_wsw += $ws_width[$h];	// ws_width �ѧ����� �ǡ �������ҧ ��Ƿ��١�Ѵ����
                if ($arr_column_sel[$h]==1) {
                        $sum_hdw += $heading_width[$h];
                }
        }
        // �ѭ�ѵ����ҧ��   �ʹ����������ҧ column � heading_width ��º�Ѻ �ʹ���� ws_width
        // ���� column � ws_width[$h] = sum(ws_width) /sum(heading_width) * heading_width[$h]
        for($h = 0; $h < count($heading_width); $h++) {
                if ($arr_column_sel[$h]==1) {
                        $ws_width[$h] = $sum_wsw / $sum_hdw * $heading_width[$h];
                }
        }
        //echo "af..ws_width=".implode(",",$ws_width)."<br>";
	// �������º��Ҥӹǹ���º��º��� $ws_width ���� ��º�Ѻ $heading_width
	function print_header(){
            global $worksheet, $xlsRow;
            global $heading_name;
            global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width;
            global $ws_head_line1, $ws_head_line2, $ws_colmerge_line1, $ws_colmerge_line2;
            global $ws_border_line1, $ws_border_line2, $ws_fontfmt_line1, $ws_fontfmt_line2;
            global $ws_headalign_line1, $ws_headalign_line2, $ws_width;

            // loop ��˹��������ҧ�ͧ column
            $colseq=0;
            for($i=0; $i < count($ws_width); $i++) {
                if ($arr_column_sel[$arr_column_map[$i]]==1) {	// �����੾�з�����͡����ʴ�
                    $worksheet->set_column($colseq, $colseq, $ws_width[$arr_column_map[$i]]);
//			echo "$i-map[".$arr_column_map[$i]."], width=".$ws_width[$arr_column_map[$i]]."<br />";
                    $colseq++;
                }
            }
            $colshow_cnt = $colseq;
            // loop ����� head ��÷Ѵ��� 1
            $colseq=0;
            $pgrp="";
            for($i=0; $i < count($ws_width); $i++) {
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
            for($i=0; $i < count($ws_width); $i++) {
                if ($arr_column_sel[$arr_column_map[$i]]==1) { 	// �����੾�з�����͡����ʴ�
                    $worksheet->write($xlsRow, $colseq, $ws_head_line2[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]]));
                    $colseq++;
                }
            }
	} // function		
        $search_conditions = str_replace(" where ", " and ", $search_condition);
        
        //����Ѻ���ҡ����ѵ� ��Ѻ������������ http://dpis.ocsc.go.th/Service/node/2853
	$cmd = "
                WITH TB_BEGIN_YEAR AS (
                    SELECT PER_ID FROM PER_PERSONAL WHERE PER_STATUS IN (1) 
                        AND (PER_RETIREDATE != '".(($search_budget_year - 543) - 1)."-10-01' 
                        AND PER_RETIREDATE > '".(($search_budget_year - 543) - 1)."-10-01')
                ) ,  
                TB_IN_YEAR AS (
                    SELECT PER_ID from PER_PERSONAL WHERE (PER_STATUS IN (1,2)) 
                        AND (PER_RETIREDATE != '".(($search_budget_year - 543) - 1)."-10-01' 
                        AND PER_RETIREDATE > '".(($search_budget_year - 543) - 1)."-10-01')
                        AND PER_STARTDATE >= '".(($search_budget_year - 543) - 1)."-10-01' 
                        AND PER_STARTDATE < '".($search_budget_year - 543)."-10-01'
                ) ,  
                TB_OUT_YEAR AS (
                    SELECT PER_ID from PER_PERSONAL WHERE PER_STATUS IN (2) 
                        AND PER_POSDATE >= '".(($search_budget_year - 543) - 1)."-10-01' 
                        AND PER_POSDATE < '".($search_budget_year - 543)."-10-01'
                ) ,  
                p AS (
                    SELECT  p.MINISTRY_ID , p.DEPARTMENT_ID,  NVL(p.ORG_ID,0) AS ORG_ID , NVL(p.ORG_ID_1,0) as ORG_ID_1 ,NVL(p.ORG_ID_2,0) as ORG_ID_2 ,  NVL(p.PL_CODE,0) as PL_CODE ,
                            p.CNT_BEGIN_YEAR, p.CNT_IN_YEAR, p.CNT_OUT_YEAR,((NVL(p.CNT_BEGIN_YEAR,0)+NVL(p.CNT_IN_YEAR,0))-NVL(p.CNT_OUT_YEAR,0)) AS CNT_LAST_YEAR ,
                            (NVL(p.CNT_IN_YEAR,0)+NVL(p.CNT_OUT_YEAR,0)) AS CNT_IN_OUT 
                    FROM (
                        select   g.ORG_ID_REF as MINISTRY_ID, g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID , b.ORG_ID_1 ,b.ORG_ID_2 , $line_code as PL_CODE,  a.PER_ID,
                                (SELECT 1 FROM TB_BEGIN_YEAR x WHERE x.PER_ID = a.PER_ID ) AS CNT_BEGIN_YEAR ,
                                (SELECT 1 FROM TB_IN_YEAR x WHERE x.PER_ID = a.PER_ID ) AS CNT_IN_YEAR ,
                                (SELECT 1 FROM TB_OUT_YEAR x WHERE x.PER_ID = a.PER_ID ) AS CNT_OUT_YEAR 
                        FROM    PER_PERSONAL a, 
                                $position_table b, 
                                PER_ORG c, 
                                PER_ORG g 
                        WHERE   $position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=g.ORG_ID(+)
                                $search_conditions
                            /*ORDER BY g.ORG_ID_REF, g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, a.PER_ID*/
                    ) p			 
                )
                SELECT  p.* , 
                        ( $select_list_case ) AS lv
                FROM (
                    SELECT  
                            $select_list1, p.CNT_BEGIN_YEAR , p.CNT_IN_YEAR , p.CNT_OUT_YEAR , p.CNT_LAST_YEAR  , p.CNT_IN_OUT
                    FROM (
                        SELECT
                            SUM(p.CNT_BEGIN_YEAR) AS CNT_BEGIN_YEAR,SUM(p.CNT_IN_YEAR) AS CNT_IN_YEAR,SUM(p.CNT_OUT_YEAR) AS CNT_OUT_YEAR, SUM(p.CNT_LAST_YEAR) AS CNT_LAST_YEAR , SUM(p.CNT_IN_OUT) AS CNT_IN_OUT ,
                            $select_list
                        FROM p
                        GROUP BY  ROLLUP ($group_by)
                    ) p ORDER BY  $order_by ASC
                ) p
        ";
        if($select_org_structure==1) { 
            $cmd1 = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
            $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd1);
	}
        //echo "<pre>$cmd";
        $db_dpis->send_cmd($cmd);
        $cnt_data = $db_dpis->num_rows();
        $db_dpis1->send_cmd($cmd);
        $AllCountChk = $cnt_data;
        $chk_i=0;
        $space_mis=" ";
        $space_dep=" ";
        $space_org=" ";
        $space_org_1="   ";
        $space_org_2="   ";
        
        if($cnt_data){
            $colshow_cnt=0;// �Ҩӹǹ column ����ʴ���ԧ
            for($i=0; $i<count($arr_column_sel); $i++){
                if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
            }
            $xlsRow = 0;
            $arr_title = explode("||", $report_title);
            for($i=0; $i<count($arr_title); $i++){
                $worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
                for($j=0; $j < $colshow_cnt-1; $j++){ 
                    $worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
                }    
                $xlsRow++;
            }
            if($company_name){
                $worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
                for($j=0; $j < $colshow_cnt-1; $j++){
                    $worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
                }    
                $xlsRow++;
            } //if($company_name){
            print_header();
            // ��˹���ҵ�������;���� ��ǹ������
            $wsdata_fontfmt_1 = array("B","B","B","B","B","B","B");
            $wsdata_align_1 = array("L","R","R","R","R","R","R");
            $wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
            $wsdata_colmerge_1 = array(0,0,0,0,0,0,0);
            $wsdata_fontfmt_2 = array("","","","","","","");
            // ����˹���ҵ�������;���� ��ǹ������
            while($data = $db_dpis1->get_array()){
                $chk_i++;
                if($data[LV] == '0'){
                    $All_begin = ($data[CNT_BEGIN_YEAR])?$data[CNT_BEGIN_YEAR]:'-';
                    $All_in = ($data[CNT_IN_YEAR])?$data[CNT_IN_YEAR]:'-';
                    $All_out = ($data[CNT_OUT_YEAR])?$data[CNT_OUT_YEAR]:'-';
                    $All_last = ($data[CNT_LAST_YEAR])?$data[CNT_LAST_YEAR]:'-';
                    $Alltotalcount = ($data[CNT_IN_OUT])?$data[CNT_IN_OUT]:'-';
                }else{
                    $MINISTRY_ID = $data[MINISTRY_ID];
                    $DEPARTMENT_ID = $data[DEPARTMENT_ID];
                    $ORG_ID = $data[ORG_ID];
                    $ORG_ID_1 = $data[ORG_ID_1];
                    $ORG_ID_2 = $data[ORG_ID_2];
                    $LINE_ID = $data[PL_CODE];
                    $LEVEL_ARR = explode('|/',$data[LV]);
                    $NAME_H = '';

                    if($LEVEL_ARR[1]=='MINISTRY' || $LEVEL_ARR[1]=='DEPARTMENT' || $LEVEL_ARR[1]=='ORG' || $LEVEL_ARR[1]=='ORG_1' || $LEVEL_ARR[1]=='ORG_2'){
                        if($LEVEL_ARR[1]=='MINISTRY'){
                            $chk_NAME = '��з�ǧ';
                            $id_getname=$MINISTRY_ID;
                        }else if($LEVEL_ARR[1]=='DEPARTMENT'){
                            $chk_NAME = '���';
                            $id_getname=$DEPARTMENT_ID;
                        }else if($LEVEL_ARR[1]=='ORG'){
                            $chk_NAME = '�ӹѡ/�ͧ';
                            $id_getname=$ORG_ID;
                        }else if($LEVEL_ARR[1]=='ORG_1'){
                            $chk_NAME = '��ӡ��� �ӹѡ/�ͧ 1 �дѺ';
                            $id_getname=$ORG_ID_1;
                        }else if($LEVEL_ARR[1]=='ORG_2'){
                            $chk_NAME = '��ӡ��� �ӹѡ/�ͧ 2 �дѺ';
                            $id_getname=$ORG_ID_2;
                        }
                        $NAME_H = get_name_org($id_getname,$chk_NAME,$select_org_structure);
                    }else if($LEVEL_ARR[1]=='LINE'){
                        $NAME_H = get_name_line($LINE_ID,'��§ҹ');
                    }

                    $col_main =  str_repeat(" ", $LEVEL_ARR[0]*3).$NAME_H;
                    $count_begin = ($data[CNT_BEGIN_YEAR])?$data[CNT_BEGIN_YEAR]:'-'; //�͹����� �鹻� �ԸդԴ��� �ӹǹ�������ʶҹл��� ����ѧ������³������ѹ��� 1 �� 2561
                    $count_in = ($data[CNT_IN_YEAR])?$data[CNT_IN_YEAR]:'-';  //�͹����� ���  �ԸդԴ��� �ӹǹ�������ʶҹл���/�鹨ҡ��ǹ�Ҫ��� ����ѧ������³���� ������ѹ�����ǹ�Ҫ��� ������ѹ��� 1 �� 61 �֧ 30 �� 62
                    $count_out = ($data[CNT_OUT_YEAR])?$data[CNT_OUT_YEAR]:'-'; //�͹����� �͡ �ԸդԴ��� �ӹǹ�������ʶҹо鹨ҡ��ǹ�Ҫ��ë���ռ������ҧ�ѹ��� 1 �� 61 - 30 �� 62
                    $count_last = ($data[CNT_LAST_YEAR])?$data[CNT_LAST_YEAR]:'-'; //�͹����� ��鹻� ��� �鹻� + ��� - �͡
                    $count_all = ($data[CNT_IN_OUT])?$data[CNT_IN_OUT]:'-';
                    if($Alltotalcount != 0 && $Alltotalcount != '-'){
                        if($count_in != '-'){
                            $per_in = number_format((100*$count_in)/$Alltotalcount,2); //�͹����� ��������� �ԸդԴ��� (100*���/(���+�͡))     //$count_all == ���+�͡
                        } else {
                            $per_in = 0;
                        }
                        if($count_out != '-'){
                            $per_out = number_format((100*$count_out)/$Alltotalcount,2); //�͹����� �������͡  �ԸդԴ��� (100*�͡/(���+�͡)) //$count_all == ���+�͡
                        } else {
                            $per_out = 0;
                        }
                    }else{
                        $per_in = 0;
                        $per_out = 0;
                    }
                    $arr_data = (array) null;
                    $arr_data[] = $col_main;
                    $arr_data[] = $count_begin;
                    $arr_data[] = $count_in;
                    $arr_data[] = $count_out;
                    $arr_data[] = $count_last;
                    $arr_data[] = $per_in;
                    $arr_data[] = $per_out;
                    $arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));	
                    $xlsRow++;
                    $colseq=0;
                    for($i=0; $i < count($arr_column_map); $i++) {
                        if ($arr_column_sel[$arr_column_map[$i]]==1) {
                            if ($arr_aggreg_data[$i]){ $ndata = $arr_aggreg_data[$i];}	// arr_aggreg_data ��ҹ��� map �����º��������
                            else{ $ndata = $arr_data[$arr_column_map[$i]];}
                            if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
                                $worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
                            }else{
                                $worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
                            }    
                            $colseq++;
                        }
                    }
                }
            }
            if($AllCountChk==$chk_i){
                $col_main =  '���';
                $All_begin = $All_begin;
                $All_in = $All_in;
                $All_out = $All_out;
                $All_last = $All_last;
                if($Alltotalcount != 0  && $Alltotalcount != '-'){
                    $Allper_in = number_format((100*$All_in)/$Alltotalcount,2); //�͹����� ��������� �ԸդԴ��� (100*���/(���+�͡))     //$count_all == ���+�͡
                    $Allper_out = number_format((100*$All_out)/$Alltotalcount,2); //�͹����� �������͡  �ԸդԴ��� (100*�͡/(���+�͡)) //$count_all == ���+�͡
                }else{
                    $Allper_in = 0;
                    $Allper_out = 0;
                }
                $arr_data = (array) null;
		$arr_data[] = $col_main;
		$arr_data[] = $All_begin;
		$arr_data[] = $All_in;
		$arr_data[] = $All_out;
		$arr_data[] = $All_last;
		$arr_data[] = $Allper_in;
		$arr_data[] = $Allper_out;

		$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));	
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($arr_column_map); $i++) {
                    if ($arr_column_sel[$arr_column_map[$i]]==1) {
                        if ($arr_aggreg_data[$i]){ $ndata = $arr_aggreg_data[$i];}	// arr_aggreg_data ��ҹ��� map �����º��������
                        else{ $ndata = $arr_data[$arr_column_map[$i]];}
                        $worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
                        $colseq++;
                    }
		}
            }
        } else{
            $xlsRow = 0;
            $worksheet->write($xlsRow, 0, "***** ����բ����� *****", set_format("xlsFmtTitle", "B", "C", "", 1));
            $worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
            $worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
            $worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
            $worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
            $worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
            $worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	}
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