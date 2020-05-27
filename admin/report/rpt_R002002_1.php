<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", "1800");
	
	for ($i=0;$i<count($EDU_TYPE);$i++) {
	 	if($search_edu) { $search_edu.= ' or '; }
		$search_edu.= "d.EDU_TYPE like '%$EDU_TYPE[$i]%' "; 
	} 

	if(!trim($RPTORD_LIST)){ 
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		elseif($list_type=="PER_COUNTRY" && trim($search_pv_code)!="") $RPTORD_LIST = "PROVINCE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.ORG_ID_REF as MINISTRY_ID";	

				if($order_by) $order_by .= ", ";
				$order_by .= "c.ORG_ID_REF";

				$heading_name .= " ��з�ǧ";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.DEPARTMENT_ID";
				elseif($select_org_structure==1) $select_list .= "a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.DEPARTMENT_ID";
				elseif($select_org_structure==1) $order_by .= "a.DEPARTMENT_ID";

				$heading_name .= " ���";
				break;
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID";
				elseif($select_org_structure==1) $select_list .= "a.ORG_ID";
				
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID";
				elseif($select_org_structure==1) $order_by .= "a.ORG_ID";

				$heading_name .= " �ӹѡ/�ͧ";
				break;
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID_1";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_1";

				$heading_name .= " ����";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID_2";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_2";

				$heading_name .= " �ҹ";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				if($search_per_type==1) $select_list .= "b.PL_CODE";
				elseif($search_per_type==2) $select_list .= "b.PN_CODE";
				elseif($search_per_type==3) $select_list .= "b.EP_CODE";

				if($order_by) $order_by .= ", ";
				if($search_per_type==1) $order_by .= "b.PL_CODE";
				elseif($search_per_type==2) $order_by .= "b.PN_CODE";
				elseif($search_per_type==3) $order_by .= "b.EP_CODE";

				if($search_per_type==1) $heading_name .= " ��§ҹ";
				elseif($search_per_type==2) $heading_name .= " ���͵��˹�";
				elseif($search_per_type==3) $heading_name .= " ���͵��˹�";
				break;
			case "LEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.LEVEL_NO, g.LEVEL_NAME";
				
				if($order_by) $order_by .= ", ";
				$order_by .= "a.LEVEL_NO, g.LEVEL_NAME";

				$heading_name .= " �дѺ���˹�";
				break;
			case "SEX" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.PER_GENDER";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.PER_GENDER";

				$heading_name .= " ��";
				break;
			case "PROVINCE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.PV_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "c.PV_CODE";

				$heading_name .= " �ѧ��Ѵ";
				break;
			case "COUNTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.CT_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "c.CT_CODE";

				$heading_name .= " �����";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
		if($select_org_structure==0)  $order_by = "b.ORG_ID";
		else if($select_org_structure==1)  $order_by = "a.ORG_ID";
	}
	if(!trim($select_list)){
		if($select_org_structure==0) $select_list = "b.ORG_ID";
		else if($select_org_structure==1)  $select_list = "a.ORG_ID";
	}

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_search_condition[] = "(trim(f.EL_CODE) in ('05', '10', '20', '30', '40', '60', '80'))";
	
	$list_type_text = "�����ǹ�Ҫ���";

	if($list_type == "PER_ORG_TYPE_1"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(c.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ǹ��ҧ
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

			$arr_search_condition[] = "(c.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ǹ��ҧ������Ҥ
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

			$arr_search_condition[] = "(c.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ǹ�����Ҥ
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

			$arr_search_condition[] = "(c.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ��ҧ�����
		$list_type_text = "��ҧ�����";
		$arr_search_condition[] = "(trim(c.OT_CODE)='04')";
	}elseif($list_type == "PER_ORG"){
		// �ӹѡ/�ͧ , ���� , �ҹ
		$list_type_text = "";
		if(trim($search_org_id)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
			else if($select_org_structure==1) $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
		} // end if
		if(trim($search_org_id_1)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
		} // end if
	}elseif($list_type == "PER_LINE"){
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
		}elseif($search_per_type==3 && trim($search_ep_code)){
			$search_ep_code = trim($search_ep_code);
			$arr_search_condition[] = "(trim(b.EP_CODE)='$search_ep_code')";
			$list_type_text .= "$search_ep_name";
		} // end if
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
		// �����ǹ�Ҫ���
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(c.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	if($export_type=="report"){
		include ("../report/rpt_condition3.php");
	}else if($export_type=="graph"){
		include ("../../admin/report/rpt_condition3.php");	//���͹䢷���ͧ����ʴ���
	}	
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "�ٻẺ����͡��§ҹ : $list_type_text";
	if($search_per_type==1) $report_title = "$DEPARTMENT_NAME||�ӹǹ����Ҫ��è�ṡ����дѺ����֡��";
	elseif($search_per_type==2) $report_title = "$DEPARTMENT_NAME||�ӹǹ�١��ҧ��ШӨ�ṡ����дѺ����֡��";
	elseif($search_per_type==3) $report_title = "$DEPARTMENT_NAME||�ӹǹ��ѡ�ҹ�Ҫ��è�ṡ����дѺ����֡��";
	$report_code = "R0202";
	$orientation='L';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('angsa','',14);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$heading_width[0] = "87";
	$heading_width[1] = "20";
	$heading_width[2] = "20";

	function print_header(){
		global $pdf, $heading_width, $heading_name;
		
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"����ȷ������稡���֡��",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[1]+$heading_width[2]) ,7,"��ӡ��һ.���",'LTBR',0,'C',1);
		$pdf->Cell(($heading_width[1]+$heading_width[2]) ,7,"�.���",'LTBR',0,'C',1);
		$pdf->Cell(($heading_width[1]+$heading_width[2]) ,7,"�.�",'LTBR',0,'C',1);
		$pdf->Cell(($heading_width[1]+$heading_width[2]) ,7,"�.�͡",'LTBR',0,'C',1);
		$pdf->Cell(($heading_width[1]+$heading_width[2]) ,7,"���",'LTBR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		for($i=0; $i<4; $i++){
			$pdf->Cell($heading_width[1] ,7,"�ӹǹ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[2] ,7,"������",'LTBR',0,'C',1);
		} // end if
		$pdf->Cell($heading_width[1] ,7,"�ӹǹ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"������",'LTBR',1,'C',1);
	} // function		

	function count_person($education_level, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $search_per_type, $search_edu;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		if($education_level == 1) $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(f.EL_CODE) in ('05', '10', '20', '30'))";
		elseif($education_level == 2) $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(f.EL_CODE) in ('40'))"; 
		elseif($education_level == 3) $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(f.EL_CODE) in ('60'))";
		elseif($education_level == 4) $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(f.EL_CODE) in ('80'))";

		if($search_per_type==1){
			// ����Ҫ���
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
								 						(
															(
																(	
																	PER_PERSONAL a 
																	left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
														) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
													) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
								 $search_condition  and ($search_edu)
								 group by	a.PER_ID
							   ";
			}elseif($DPISDB=="oci8"){				
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCNAME f
								 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) 
								 					and a.PER_ID=d.PER_ID and ($search_edu) and d.INS_CODE=e.INS_CODE(+) and d.EN_CODE=f.EN_CODE
													$search_condition
								 group by	a.PER_ID
							   ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
								 						(
															(
																(	
																	PER_PERSONAL a 
																	left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID )
														) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
													) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
								 $search_condition  and ($search_edu)
								 group by	a.PER_ID
							   ";
			} // end if
		}elseif($search_per_type==2){
			// �١��ҧ
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															(	
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
														) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
													) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
								 $search_condition  and ($search_edu)
								 group by	a.PER_ID
							   ";
			}elseif($DPISDB=="oci8"){				



				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCNAME f
								 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+) 
								 					and a.PER_ID=d.PER_ID and ($search_edu) and d.INS_CODE=e.INS_CODE(+) and d.EN_CODE=f.EN_CODE
													$search_condition
								 group by	a.PER_ID
							   ";
							   
							 
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															(	
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
														) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
													) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
								 $search_condition  and ($search_edu)
								 group by	a.PER_ID
							   ";
			} // end if
		} // end if
		elseif($search_per_type==3){
			// ��ѡ�ҹ�Ҫ���
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															(	
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
														) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
													) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
								 $search_condition  and ($search_edu)
								 group by	a.PER_ID
							   ";
			}elseif($DPISDB=="oci8"){				
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCNAME f
								 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+) 
								 					and a.PER_ID=d.PER_ID and ($search_edu) and d.INS_CODE=e.INS_CODE(+) and d.EN_CODE=f.EN_CODE
													$search_condition
								 group by	a.PER_ID
							   ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															(	
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
														) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
													) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
								 $search_condition  and ($search_edu)
								 group by	a.PER_ID
							   ";
			} // end if
		} // end if
		if($select_org_structure==1) $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);   //##############	
		$count_person = $db_dpis2->send_cmd($cmd);
		//echo "$cmd<hr>";
//		$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

		return $count_person;
	} // function count_person

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $select_org_structure;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $LEVEL_NO, $LEVEL_NAME , $PER_GENDER, $PV_CODE, $EL_CODE, $EM_CODE, $EP_CODE;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}else if($select_org_structure==1){
						if($ORG_ID) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or b.ORG_ID is null)";
					}
				break;
				case "ORG_1" :
					if($select_org_structure==0){
						if($ORG_ID_1) $arr_addition_condition[] = "(b.ORG_ID_1 = $ORG_ID_1)";
						else $arr_addition_condition[] = "(b.ORG_ID_1 = 0 or b.ORG_ID_1 is null)";
					}
				break;
				case "ORG_2" :
					if($select_org_structure==0){
						if($ORG_ID_2) $arr_addition_condition[] = "(b.ORG_ID_2 = $ORG_ID_2)";
						else $arr_addition_condition[] = "(b.ORG_ID_2 = 0 or b.ORG_ID_2 is null)";
					}
				break;
				case "LINE" :
					if($PL_CODE) $arr_addition_condition[] = "(trim(b.PL_CODE) = '$PL_CODE')";
					else $arr_addition_condition[] = "(trim(b.PL_CODE) = '$PL_CODE' or b.PL_CODE is null)";
				break;
				case "LEVEL" :
					if($LEVEL_NO){ 
						if($DPISDB=="odbc") $arr_addition_condition[] = "(a.LEVEL_NO = '". str_pad($LEVEL_NO, 2, "0", STR_PAD_LEFT) ."')";
						elseif($DPISDB=="oci8") $arr_addition_condition[] = "(a.LEVEL_NO = '". str_pad($LEVEL_NO, 2, "0", STR_PAD_LEFT) ."')";
						elseif($DPISDB=="mysql") $arr_addition_condition[] = "(a.LEVEL_NO = '". str_pad($LEVEL_NO, 2, "0", STR_PAD_LEFT) ."')";
					}else{ 
						if($DPISDB=="odbc") $arr_addition_condition[] = "(trim(a.LEVEL_NO) = '' or a.LEVEL_NO is null)";
						elseif($DPISDB=="oci8") $arr_addition_condition[] = "(trim(a.LEVEL_NO) = '' or a.LEVEL_NO is null)";
						elseif($DPISDB=="mysql") $arr_addition_condition[] = "(trim(a.LEVEL_NO) = '' or a.LEVEL_NO is null)";
					} // end if
				break;
				case "SEX" :
					if($PER_GENDER) $arr_addition_condition[] = "(a.PER_GENDER = $PER_GENDER)";
					else $arr_addition_condition[] = "(a.PER_GENDER = 0 or a.PER_GENDER is null)";
				break;
				case "PROVINCE" :
					if($PV_CODE) $arr_addition_condition[] = "(trim(a.PV_CODE) = '$PV_CODE')";
					else $arr_addition_condition[] = "(trim(a.PV_CODE) = '$PV_CODE' or a.PV_CODE is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function  generate_condition
	
	function initialize_parameter($current_index){
		global $arr_rpt_order, $table, $field;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $LEVEL_NO, $LEVEL_NAME , $PER_GENDER, $PV_CODE, $EL_CODE, $EM_CODE, $EP_CODE;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					$ORG_ID = -1;
				break;
				case "ORG_1" :
					$ORG_ID_1 = -1;
				break;
				case "ORG_2" :
					$ORG_ID_2 = -1;
				break;
				case "LINE" :
					$PL_CODE = -1;
				break;
				case "LEVEL" :
					$LEVEL_NO = -1;
				break;
				case "SEX" :
					$PER_GENDER = -1;
				break;
				case "PROVINCE" :
					$PV_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function Initialize_parameter

	if($search_per_type==1){
		// ����Ҫ���
		if($DPISDB=="odbc"){
			$cmd = " select			distinct IIf(IsNull(e.CT_CODE), 0, CInt(e.CT_CODE)) as CT_CODE
							 from			(
													(
														(
															(	
																PER_PERSONAL a 
																left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
												) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
												$search_condition  and ($search_edu)
							 order by		IIf(IsNull(e.CT_CODE), 0, CInt(e.CT_CODE))
						   ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct NVL(e.CT_CODE, 0) as CT_CODE
							 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCNAME f
							 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) 
							 					and a.PER_ID=d.PER_ID and ($search_edu) and d.INS_CODE=e.INS_CODE(+) and d.EN_CODE=f.EN_CODE
												$search_condition
							 order by		NVL(e.CT_CODE, 0)
						   ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list
							 from			(
							 						(
														(
															(
																(	
																	PER_PERSONAL a 
																	left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
														) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
													) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
												) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
			 				 $search_condition  and ($search_edu)
							 order by $order_by
						   ";
		}
	}elseif($search_per_type==2){
		// �١��ҧ
		if($DPISDB=="odbc"){
			$cmd = " select			distinct IIf(IsNull(e.CT_CODE), 0, CInt(e.CT_CODE)) as CT_CODE
							 from			(
													(
														(
															(	
																PER_PERSONAL a 
																left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
													) left join PER_EDUCATE e on (d.INS_CODE=e.INS_CODE)
												) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
												$search_condition  and ($search_edu)
							 order by		IIf(IsNull(e.CT_CODE), 0, CInt(e.CT_CODE))
						   ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct NVL(e.CT_CODE, 0) as CT_CODE
							 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCNAME f
							 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+) 
							 					and a.PER_ID=d.PER_ID and ($search_edu) and d.INS_CODE=e.INS_CODE(+) and d.EN_CODE=f.EN_CODE
												$search_condition
							 order by		NVL(e.CT_CODE, 0)
						   ";
						   /*
						   $cmd = " select			distinct NVL(e.CT_CODE, 0) as CT_CODE
							 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCNAME f
							 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) 
							 					and a.PER_ID=d.PER_ID and d.EDU_TYPE like '%||2||%' and d.INS_CODE=e.INS_CODE(+) and d.EN_CODE=f.EN_CODE
												$search_condition
							 order by		NVL(e.CT_CODE, 0)
						   
						   */
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list
							 from			(
							 						(
														(
															(
																(	
																	PER_PERSONAL a 
																	left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
														) left join PER_EDUCATE e on (d.INS_CODE=e.INS_CODE)
													) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
												) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
							 $search_condition  and ($search_edu)
							 order by $order_by
						   ";
		}
	} // end if
	elseif($search_per_type==3){
		// ��ѡ�ҹ�Ҫ���
		if($DPISDB=="odbc"){
			$cmd = " select			distinct IIf(IsNull(e.CT_CODE), 0, CInt(e.CT_CODE)) as CT_CODE
							 from			(
													(
														(
															(	
																PER_PERSONAL a 
																left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
													) left join PER_EDUCATE e on (d.INS_CODE=e.INS_CODE)
												) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
												$search_condition  and ($search_edu)
							 order by		IIf(IsNull(e.CT_CODE), 0, CInt(e.CT_CODE))
						   ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct NVL(e.CT_CODE, 0) as CT_CODE
							 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCNAME f
							 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+) 
							 					and a.PER_ID=d.PER_ID and ($search_edu) and d.INS_CODE=e.INS_CODE(+) and d.EN_CODE=f.EN_CODE
												$search_condition
							 order by		NVL(e.CT_CODE, 0)
						   ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list
							 from			(
							 						(
														(
															(
																(	
																	PER_PERSONAL a 
																	left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID )
														) left join PER_EDUCATE e on (d.INS_CODE=e.INS_CODE)
													) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
												) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
							$search_condition  and ($search_edu)
							 order by		$order_by
						   ";
		}
	} // end if
	if($select_org_structure==1) $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);   //##############	
	$count_data = $db_dpis->send_cmd($cmd);
	//echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = 0;
	for($i=1; $i<=4; $i++) ${"GRAND_TOTAL_".$i} = 0;
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "COUNTRY" :
					if($CT_CODE != $data[CT_CODE]){
						$CT_CODE = $data[CT_CODE];
						if($CT_CODE == ""){
							$ORG_NAME = "[����к��ӹѡ/�ͧ]";
							$ORG_SHORT = "[����к��ӹѡ/�ͧ]";
						}else{
							$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE=$CT_CODE ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$CT_NAME = $data2[CT_NAME];
							$CT_SHORT = $CT_NAME;
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "COUNTRY";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $CT_NAME;
						$arr_content[$data_count][short_name] = $CT_SHORT;
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][sum_per] = count_person(0, $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=4; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
					} // end if
				break;

				case "ORG" :
					if($ORG_ID != $data[ORG_ID]){
						$ORG_ID = $data[ORG_ID];
						if($ORG_ID == ""){
							$ORG_NAME = "[����к��ӹѡ/�ͧ]";
							$ORG_SHORT = "[����к��ӹѡ/�ͧ]";
						}else{
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
							$ORG_SHORT = $data2[ORG_SHORT];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						$arr_content[$data_count][short_name] = $ORG_SHORT;
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][sum_per] = count_person(0, $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=4; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
					} // end if
				break;
		
				case "ORG_1" :
					if($ORG_ID_1 != $data[ORG_ID_1]){
						$ORG_ID_1 = $data[ORG_ID_1];
						if($ORG_ID_1 == ""){
							$ORG_NAME_1 = "[����кؽ���]";
							$ORG_SHORT_1 = "[����кؽ���]";
						}else{
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_1 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_1 = $data2[ORG_NAME];
							$ORG_SHORT_1 = $data2[ORG_SHORT];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_1";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_1;
						$arr_content[$data_count][short_name] = $ORG_SHORT_1;
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][sum_per] = count_person(0, $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=4; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "ORG_2" :
					if($ORG_ID_2 != $data[ORG_ID_2]){
						$ORG_ID_2 = $data[ORG_ID_2];
						if($ORG_ID_2 == ""){
							$ORG_NAME_2 = "[����кاҹ]";
							$ORG_SHORT_2 = "[����кاҹ]";
						}else{
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_2 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_2 = $data2[ORG_NAME];
							$ORG_SHORT_2 = $data2[ORG_SHORT];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_2";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_2;
						$arr_content[$data_count][short_name] = $ORG_SHORT_2;
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][sum_per] = count_person(0, $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=4; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "LINE" :
					if($PL_CODE != trim($data[PL_CODE])){
						$PL_CODE = trim($data[PL_CODE]);
						if($PL_CODE == ""){
							$PL_NAME = "[����к���§ҹ]";
						}else{
							$cmd = " select PL_NAME, PL_SHORTNAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PL_NAME = $data2[PL_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "LINE";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_NAME;
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][sum_per] = count_person(0, $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=4; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "LEVEL" :
					if($LEVEL_NO != trim($data[LEVEL_NO])){
						$LEVEL_NO = trim($data[LEVEL_NO]);
						$LEVEL_NAME = trim($data[LEVEL_NAME]);

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "LEVEL";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . (trim($LEVEL_NAME)?"".level_no_format($LEVEL_NAME):"[����к��дѺ���˹�]");
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][sum_per] = count_person(0, $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=4; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "SEX" :
					if($PER_GENDER != trim($data[PER_GENDER])){
						$PER_GENDER = trim($data[PER_GENDER]);
						if(!$PER_GENDER) $GENDER_NAME = "[����к���]";
						elseif($PER_GENDER==1) $GENDER_NAME = "���";
						elseif($PER_GENDER==2) $GENDER_NAME = "˭ԧ";

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "SEX";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $GENDER_NAME;
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][sum_per] = count_person(0, $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=4; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;

				case "PROVINCE" :
					if($PV_CODE != trim($data[PV_CODE])){
						$PV_CODE = trim($data[PV_CODE]);
						if($PV_CODE == ""){
							$PV_NAME = "[����кبѧ��Ѵ]";
						}else{
							$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PV_NAME = $data2[PV_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "PROVINCE";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PV_NAME;
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][sum_per] = count_person(0, $search_condition, $addition_condition);

						if($rpt_order_index == 0){
							for($i=1; $i<=4; $i++) ${"GRAND_TOTAL_".$i} += $arr_content[$data_count]["count_".$i];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;

			} // end switch case
		} // end for
	} // end while
	
	$GRAND_TOTAL = $GRAND_TOTAL_1 + $GRAND_TOTAL_2 + $GRAND_TOTAL_3 + $GRAND_TOTAL_4;

if($export_type=="report"){		
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$COUNT_1 = $arr_content[$data_count][count_1];
			$COUNT_2 = $arr_content[$data_count][count_2];
			$COUNT_3 = $arr_content[$data_count][count_3];
			$COUNT_4 = $arr_content[$data_count][count_4];
			$COUNT_TOTAL = $COUNT_1 + $COUNT_2 + $COUNT_3 + $COUNT_4;
			
			$PERCENT_1 = $PERCENT_2 = $PERCENT_3 = $PERCENT_4 = $PERCENT_TOTAL = 0;
			if($COUNT_TOTAL){ 
				$PERCENT_1 = ($COUNT_1 / $COUNT_TOTAL) * 100;
				$PERCENT_2 = ($COUNT_2 / $COUNT_TOTAL) * 100;
				$PERCENT_3 = ($COUNT_3 / $COUNT_TOTAL) * 100;
				$PERCENT_4 = ($COUNT_4 / $COUNT_TOTAL) * 100;
			} // end if
			if($GRAND_TOTAL) $PERCENT_TOTAL = ($COUNT_TOTAL / $GRAND_TOTAL) * 100;

			$border = "";
			$pdf->SetFont('angsa','',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->MultiCell($heading_width[0], 7, "$NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0];
			$pdf->y = $start_y;
			$pdf->Cell($heading_width[1], 7, ($COUNT_1?number_format($COUNT_1):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[2], 7, ($PERCENT_1?number_format($PERCENT_1, 2):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[1], 7, ($COUNT_2?number_format($COUNT_2):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[2], 7, ($PERCENT_2?number_format($PERCENT_2, 2):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[1], 7, ($COUNT_3?number_format($COUNT_3):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[2], 7, ($PERCENT_3?number_format($PERCENT_3, 2):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[1], 7, ($COUNT_4?number_format($COUNT_4):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[2], 7, ($PERCENT_4?number_format($PERCENT_4, 2):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[1], 7, ($COUNT_TOTAL?number_format($COUNT_TOTAL):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[2], 7, ($PERCENT_TOTAL?number_format($PERCENT_TOTAL, 2):"-"), $border, 0, 'R', 0);

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
			for($i=0; $i<=10; $i++){
				if($i==0){
					$line_start_y = $start_y;		$line_start_x += $heading_width[0];
					$line_end_y = $max_y;		$line_end_x += $heading_width[0];
				}elseif(($i % 2) == 1){
					$line_start_y = $start_y;		$line_start_x += $heading_width[1];
					$line_end_y = $max_y;		$line_end_x += $heading_width[1];
				}elseif(($i % 2) == 0){
					$line_start_y = $start_y;		$line_start_x += $heading_width[2];
					$line_end_y = $max_y;		$line_end_x += $heading_width[2];
				} // end if
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
			//====================================================

			if(($pdf->h - $max_y - 10) < 15){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < (count($arr_content) - 1)){
					$pdf->AddPage();
					print_header();
					$max_y = $pdf->y;
				} // end if
			}else{
				if($data_count == (count($arr_content) - 1)) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);		
			} // end if
			$pdf->x = $start_x;			$pdf->y = $max_y;
		} // end for
				
		$PERCENT_TOTAL_1 = $PERCENT_TOTAL_2 = $PERCENT_TOTAL_3 = $PERCENT_TOTAL_4 = 0;
		if($GRAND_TOTAL){ 
			$PERCENT_TOTAL_1 = ($GRAND_TOTAL_1 / $GRAND_TOTAL) * 100;
			$PERCENT_TOTAL_2 = ($GRAND_TOTAL_2 / $GRAND_TOTAL) * 100;
			$PERCENT_TOTAL_3 = ($GRAND_TOTAL_3 / $GRAND_TOTAL) * 100;
			$PERCENT_TOTAL_4 = ($GRAND_TOTAL_4 / $GRAND_TOTAL) * 100;
		} // end if
		$PERCENT_TOTAL = ($GRAND_TOTAL / $GRAND_TOTAL) * 100;

		$border = "LTBR";
		$pdf->SetFont('angsab','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

		$pdf->MultiCell($heading_width[0], 7, "���", $border, "C");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width[0];
		$pdf->y = $start_y;
		$pdf->Cell($heading_width[1], 7, ($GRAND_TOTAL_1?number_format($GRAND_TOTAL_1):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[2], 7, ($PERCENT_TOTAL_1?number_format($PERCENT_TOTAL_1, 2):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[1], 7, ($GRAND_TOTAL_2?number_format($GRAND_TOTAL_2):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[2], 7, ($PERCENT_TOTAL_2?number_format($PERCENT_TOTAL_2, 2):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[1], 7, ($GRAND_TOTAL_3?number_format($GRAND_TOTAL_3):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[2], 7, ($PERCENT_TOTAL_3?number_format($PERCENT_TOTAL_3, 2):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[1], 7, ($GRAND_TOTAL_4?number_format($GRAND_TOTAL_4):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[2], 7, ($PERCENT_TOTAL_4?number_format($PERCENT_TOTAL_4, 2):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[1], 7, ($GRAND_TOTAL?number_format($GRAND_TOTAL):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[2], 7, ($PERCENT_TOTAL?number_format($PERCENT_TOTAL, 2):"-"), $border, 1, 'R', 0);
	}else{
		$pdf->SetFont('angsab','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ����բ����� **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();		
	}else if($export_type=="graph"){//if($export_type=="report"){
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
//	echo "<pre>"; print_r($arr_rpt_order); echo "</pre>";
	$arr_content_key = array_keys($arr_content[0]);//print_r($arr_content_key);
	$arr_categories = array();
	for($i=0;$i<count($arr_content);$i++){
//		if($arr_content[$i][type]==$arr_rpt_order[0]){
		$arr_categories[$i] = trim($short_name=="y")?(trim($arr_content[$i][short_name])?$arr_content[$i][short_name]:$arr_content[$i][name]):$arr_content[$i][name];
			for($j=2;$j<count($arr_content_key);$j++){
				$arr_series_caption_data[$j][] = $arr_content[$i][$arr_content_key[$j]];
				}//for($j=2;$j<count($arr_content_key);$j++){
//			}//if($arr_content[$i][type]==$arr_rpt_order[0]){
		}//for($i=0;$i<count($arr_content);$i++){
//	echo "<pre>"; print_r($arr_series_caption_data); echo "</pre>";
	for($j=2;$j<count($arr_content_key);$j++){
		$arr_series_list[$j] = implode(";", $arr_series_caption_data[$j]).";".${"GRAND_TOTAL_".($j-1)};
		}
	
	$chart_title = $report_title;
	$chart_subtitle = $company_name;
	if(!$setWidth){ $setWidth = "$GRAPH_WIDE";}else{ $setWidth = "800";}
	if(!$setHeight){ $setHeight = "$GRAPH_HIGH";}else{$setHeight = "600";}
	$selectedFormat = "SWF";
	$series_caption_list = "��ӡ��һ.���;�.���;�.�;�.�͡";
	$categories_list = implode(";", $arr_categories).";���";
	if(strtolower($graph_type)=="pie"){
		$series_list = $GRAND_TOTAL_1.";".$GRAND_TOTAL_2.";".$GRAND_TOTAL_3.";".$GRAND_TOTAL_4;
		}else{
		$series_list = implode("|", $arr_series_list);
		}
	//echo($series_list);
	switch( strtolower($graph_type) ){
		case "column" :
			$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Column/style/column.scs";
			break;
		case "bar" :
			$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Bar/style/bar.scs";
			break;
		case "line" :
			$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Line/style/line.scs";
			break;
		case "pie" :
			$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Pie/style/pie.scs";
			break;
		} //switch( strtolower($graph_type) ){
	}//}else if($export_type=="graph"){
?>