<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";			//��� 	** include ("../report/rpt_condition3.php");
		$line_join = "b.PL_CODE=g.PL_CODE";
		$line_code = "b.PL_CODE";
		$line_name = "b.PL_NAME";		
		$line_short_name = "PL_SHORTNAME";
		$line_seq="g.PL_SEQ_NO";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
		$line_title=" ��§ҹ";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=g.PN_CODE";
		$line_code = "b.PN_CODE";
		$line_name = "b.PN_NAME";		
		$line_seq="g.PN_SEQ_NO";
		$line_search_code=trim($search_pn_code);
		$line_search_name =trim($search_pn_name);
		$line_title=" ���͵��˹�";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=g.EP_CODE";
		$line_code = "b.EP_CODE";
		$line_name = "b.EP_NAME";		
		$line_seq="g.EP_SEQ_NO";
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=g.TP_CODE";
		$line_code = "b.TP_CODE";
		$line_name = "b.TP_NAME";		
		$line_seq="g.TP_SEQ_NO";
		$line_search_code =trim($search_tp_code);
		$line_search_name =trim($search_tp_name);
		$line_title=" ���͵��˹�";
	} // end if	

	$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";		//��˹����������� ��ͧ�ʴ�����ȴ��·ء���� ����� index �����˹��á��� 0
	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {
		$f_all = true; 
	} else $f_all = false;	

	if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
	if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
	if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
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
			case "COUNTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "d.CT_CODE_EDU as CT_CODE";		

				if($order_by) $order_by .= ", ";
				$order_by .= "d.CT_CODE_EDU";					

				$heading_name .= " $CT_TITLE";
				break;
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "h.ORG_ID_REF as MINISTRY_ID";	
			
				if($order_by) $order_by .= ", ";
				$order_by .= "h.ORG_ID_REF";

				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "h.ORG_SEQ_NO, h.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "h.ORG_SEQ_NO, h.ORG_CODE, a.DEPARTMENT_ID";

				$heading_name .= " $DEPARTMENT_TITLE";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				else if($select_org_structure==1)  $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				else if($select_org_structure==1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID"; 

				$heading_name .= " $ORG_TITLE";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "$line_seq, $line_code as PL_CODE";
					
				if($order_by) $order_by .= ", ";
				$order_by .= "$line_seq, $line_code";
				
				$heading_name .=  $line_title;
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) {
		if($select_org_structure==0){ $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID"; }
		else if($select_org_structure==1){	 $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID"; }
	}
	if(!trim($select_list)) {
		if($select_org_structure==0){ $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID"; }
		else if($select_org_structure==1){ $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";	 } 
	}
	
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_search_condition[] = "(trim(f.EL_TYPE) in ('1', '2', '3', '4'))";	//��������͹�����͹ R0202 �����͡��§ҹ�ӹǹ��ҡѹ
	
	for ($i=0;$i<count($EDU_TYPE);$i++) {
		if($search_edu) { $search_edu.= ' or '; }
		$search_edu.= "d.EDU_TYPE like '%$EDU_TYPE[$i]%' "; 
	}

	$list_type_text = $ALL_REPORT_TITLE;
	include ("../report/rpt_condition3.php");
	
	$company_name = "�ٻẺ����͡��§ҹ : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||�ӹǹ$PERSON_TYPE[$search_per_type] ��ṡ�������ȷ������稡���֡��";
	$report_code = "R0203";

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
		global $heading_name, $PERSON_TYPE[$search_per_type];
		
		$worksheet->set_column(0, 0, 80);
		$worksheet->set_column(1, 1, 10);
		$worksheet->set_column(2, 2, 10);

		$worksheet->write($xlsRow, 0, "����ȷ������稡���֡��", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "�ӹǹ$PERSON_TYPE[$search_per_type]", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "��", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "������", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function		

	function count_person($search_condition, $addition_condition ){
		global $DPISDB, $db_dpis2, $position_table, $position_join;
		global $search_per_type, $search_edu,$select_org_structure;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		
		if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from	(	
								 				(	
													(
														(
															(	
																PER_PERSONAL a 
																left join $position_table b on $position_join
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
												) inner join PER_EDUCLEVEL f on (trim(d.EL_CODE)=trim(f.EL_CODE))
											) left join PER_ORG h on (a.DEPARTMENT_ID=h.ORG_ID)
								 $search_condition  and  ($search_edu)
								 group by	a.PER_ID ";
			}elseif($DPISDB=="oci8"){				
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCLEVEL f, PER_ORG h
								 where		$position_join and b.ORG_ID=c.ORG_ID(+) 
								 					and a.PER_ID=d.PER_ID and ($search_edu) and d.INS_CODE=e.INS_CODE(+) and trim(d.EL_CODE)=trim(f.EL_CODE) and a.DEPARTMENT_ID=h.ORG_ID(+)
													$search_condition
								 group by	a.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from	(	
								 				(
													(
														(
															(	
																PER_PERSONAL a 
																left join $position_table b on $position_join
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
												) inner join PER_EDUCLEVEL f on (trim(d.EL_CODE)=trim(f.EL_CODE))
											) left join PER_ORG h on (a.DEPARTMENT_ID=h.ORG_ID)
								 $search_condition  and ($search_edu)
								 group by	a.PER_ID ";
			} // end if
			if($select_org_structure==1) { 
				$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
				$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			}
			$count_person = $db_dpis2->send_cmd($cmd);
//echo "=== $count_person : $cmd ===<br><hr>";
			//$db_dpis2->show_error(); echo "<hr>";
			if($count_person==1){
				$data = $db_dpis2->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				if($data[count_person] == 0) $count_person = 0;
			}
	return $count_person;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $MINISTRY_ID,$DEPARTMENT_ID,$ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $PN_CODE, $PER_GENDER, $PV_CODE, $EM_CODE, $EL_CODE,$EL_CODE, $EP_CODE, $TP_CODE,$CT_CODE;
		global $line_code;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "COUNTRY" :
						if($CT_CODE){ $arr_addition_condition[] = "(trim(d.CT_CODE_EDU) = '$CT_CODE')";	}
						else{ $arr_addition_condition[] = "(trim(d.CT_CODE_EDU) = '$CT_CODE' or d.CT_CODE_EDU is null)";	}
				break;
				case "MINISTRY" :
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(h.ORG_ID_REF = $MINISTRY_ID)";
				break;
				case "DEPARTMENT" :
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
				break;
				case "ORG" :	
					if($select_org_structure == 0){ 
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}else{
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}
				break;
				case "LINE" :
					if($PL_CODE) $arr_addition_condition[] = "(trim($line_code) = '$PL_CODE')";
					else $arr_addition_condition[] = "(trim($line_code) = '$PL_CODE' or $line_code is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID,$DEPARTMENT_ID,$ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $PN_CODE, $PER_GENDER, $PV_CODE, $EM_CODE, $EL_CODE, $EP_CODE, $EL_CODE, $TP_CODE;
		
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "COUNTRY" :
					$CT_CODE=-1;
				break;
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
					$PN_CODE = -1;
					$EP_CODE = -1;
					$TP_CODE=-1;
				break;
			} // end switch case
		} // end for
	} // function
	
	//�ʴ���ª���˹��§ҹ
	if($DPISDB=="odbc"){
		$cmd = " select			distinct IIf(IsNull(d.CT_CODE_EDU), 0, CLng(d.CT_CODE_EDU)) as CT_CODE, $select_list
						 from	(	
						 				(	
						 					(
												(
													(	
														(
															PER_PERSONAL a 
															left join $position_table b on $position_join
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID )
												) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
											) inner join PER_EDUCLEVEL f on (trim(d.EL_CODE)=trim(f.EL_CODE))
										) left join $line_table g on $line_join
									) left join PER_ORG h on (a.DEPARTMENT_ID=h.ORG_ID)
										$search_condition  and ($search_edu)
						 order by	IIf(IsNull(d.CT_CODE_EDU), 0, CLng(d.CT_CODE_EDU)) ,  $order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct NVL(d.CT_CODE_EDU, 0) as CT_CODE, $select_list
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCLEVEL f, $line_table g , PER_ORG h
						 where		$position_join and b.ORG_ID=c.ORG_ID(+) 
						 					and a.PER_ID=d.PER_ID and ($search_edu) and d.INS_CODE=e.INS_CODE(+) and trim(d.EL_CODE)=trim(f.EL_CODE) and $line_join(+) and a.DEPARTMENT_ID=h.ORG_ID(+) 
											$search_condition
						order by		NVL(d.CT_CODE_EDU, 0) , $order_by "; 
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct d.CT_CODE_EDU as CT_CODE, $select_list
						 from	(	
						 				(	
						 					(
												(
													(	
														(
															PER_PERSONAL a 
															left join $position_table b on $position_join
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID )
												) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
											) inner join PER_EDUCLEVEL f on (trim(d.EL_CODE)=trim(f.EL_CODE))
										) left join $line_table g on $line_join
									) left join PER_ORG h on (a.DEPARTMENT_ID=h.ORG_ID)
										$search_condition  and ($search_edu)
						 order by		d.CT_CODE_EDU , $order_by ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//echo "-> $cmd<br>";  
//$db_dpis->show_error();
	$data_count = 0;
	$GRAND_TOTAL = 0;
	initialize_parameter(0);
	$first_order = 1;	// order ��  0 = COUNTRY �ѧ�����ӹǳ ����� order ��� 1 (MINISTRY) ��͹
	while($data = $db_dpis->get_array()){
		$EL_CODE = trim($data[EL_CODE]);

		if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)) {
			for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "COUNTRY" :
					if($CT_CODE != trim($data[CT_CODE])){
						$CT_CODE = trim($data[CT_CODE]);
						if($CT_CODE != "" && $CT_CODE!=0 && $CT_CODE!=-1){
							$cmd = " select CT_NAME from PER_COUNTRY where trim(CT_CODE)='$CT_CODE' ";
							$db_dpis2->send_cmd($cmd);
						//		$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$CT_NAME = $data2[CT_NAME];
						}else{
							$CT_NAME = "[����кػ����]";
						}
						
						$addition_condition = generate_condition($rpt_order_index);
						
						$arr_content[$data_count][type] = "COUNTRY";
						$arr_content[$data_count][name] = (($CT_CODE)?"�����":"")."$CT_NAME";
						$arr_content[$data_count][count_1] = count_person($search_condition, $addition_condition);
						
						if($rpt_order_index==0){	////----��ͧ��� case �������----//		�Ѻ����ӹǹ�������Ǿ�
							$GRAND_TOTAL += $arr_content[$data_count][count_1];
						} // end if

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
					}
					break;
					
				case "MINISTRY" :
					if ($CTRL_TYPE < 3) {
						if($MINISTRY_ID != trim($data[MINISTRY_ID])){
							$MINISTRY_ID = trim($data[MINISTRY_ID]);
							if($MINISTRY_ID != "" && $MINISTRY_ID != 0 && $MINISTRY_ID != -1){
								$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$MINISTRY_ID ";
								$db_dpis2->send_cmd($cmd);
								//$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$MINISTRY_NAME = $data2[ORG_NAME];
							}else{
								$MINISTRY_NAME = "[����кء�з�ǧ]";
							}
							
							$addition_condition = generate_condition($rpt_order_index);
							
							$arr_content[$data_count][type] = "MINISTRY";
							$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;
							$arr_content[$data_count][count_1] = count_person($search_condition, $addition_condition);
		/*
							if($rpt_order_index==$first_order){
								$GRAND_TOTAL += $arr_content[$data_count][count_1];
							} // end if
		*/

							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
							$data_count++;
						}
					}
					break;
					
				case "DEPARTMENT" :
					if ($CTRL_TYPE < 4) {
						if($DEPARTMENT_ID != trim($data[DEPARTMENT_ID])){
							$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
							if($DEPARTMENT_ID != "" && $DEPARTMENT_ID != 0 && $DEPARTMENT_ID != -1){
								$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
								$db_dpis2->send_cmd($cmd);
								//$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$DEPARTMENT_NAME = $data2[ORG_NAME];
							}else{
								$DEPARTMENT_NAME = "[����кء��]";
							}
							
							$addition_condition = generate_condition($rpt_order_index);
							
							$arr_content[$data_count][type] = "DEPARTMENT";
							$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $DEPARTMENT_NAME;
							$arr_content[$data_count][count_1] = count_person($search_condition, $addition_condition);
		/*
							if($rpt_order_index==$first_order){
								$GRAND_TOTAL += $arr_content[$data_count][count_1];
							} // end if
		*/

							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
							$data_count++;
						}
					}
					break;

				case "ORG" :
					if($ORG_ID != trim($data[ORG_ID])){
						$ORG_ID = trim($data[ORG_ID]);
						if($ORG_ID != "" && $ORG_ID != 0 && $ORG_ID != -1){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
							$ORG_SHORT = $data2[ORG_SHORT];
							if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;
						}else{
							$ORG_NAME = "[����к��ӹѡ�ͧ]";
						}

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						$arr_content[$data_count][count_1] = count_person($search_condition, $addition_condition);
/*
					if($rpt_order_index==$first_order){
						$GRAND_TOTAL += $arr_content[$data_count][count_1];
					} // end if
*/
	
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
					} // end if
				break;
				case "LINE" :
					if($PL_CODE != trim($data[PL_CODE])){
						$PL_CODE = trim($data[PL_CODE]);
						if($PL_CODE != ""){
							if($search_per_type==1){
								$cmd = " select $line_name as PL_NAME, $line_short_name from $line_table b where trim($line_code)='$PL_CODE' ";
							}else{
								$cmd = " select $line_name as PL_NAME from $line_table b where trim($line_code)='$PL_CODE' ";
							}
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$PL_NAME = trim($data2[PL_NAME]);
							if($search_per_type==1){
								$PL_NAME = trim($data2[$line_short_name])?$data2[$line_short_name]:$PL_NAME;
							}
						}else{
							$PL_NAME = "[����к�$PL_TITLE]";
						}
		
						$addition_condition = generate_condition($rpt_order_index);
		
						$arr_content[$data_count][type] = "LINE";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_NAME;
						$arr_content[$data_count][count_1] = count_person($search_condition, $addition_condition);
/*
					if($rpt_order_index==$first_order){
						$GRAND_TOTAL += $arr_content[$data_count][count_1];
					} // end if
*/
	
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
					} // end if
				break;		
			} // end switch case
		} // end for
	  } // end if
	} // end while
	
//	$GRAND_TOTAL = count_person($search_condition, "");
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} // end if

		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$xlsRow++;
		} // end if

		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$COUNT_1 = $arr_content[$data_count][count_1];
			
			if($GRAND_TOTAL) $PERCENT_1 = ($COUNT_1 / $GRAND_TOTAL) * 100;

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME):$NAME), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, ($COUNT_1?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_1)):number_format($COUNT_1)):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2, ($PERCENT_1?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_1,2)):number_format($PERCENT_1, 2)):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
		} // end for
				
		$PERCENT_TOTAL = ($GRAND_TOTAL / $GRAND_TOTAL) * 100;

		$xlsRow++;
		$worksheet->write_string($xlsRow, 0, "���", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
		$worksheet->write_string($xlsRow, 1, ($GRAND_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_TOTAL)):number_format($GRAND_TOTAL)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write_string($xlsRow, 2, ($PERCENT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_TOTAL,2)):number_format($PERCENT_TOTAL, 2)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ����բ����� *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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