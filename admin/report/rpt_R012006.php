<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", "1800");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if(!trim($RPTORD_LIST)){ 
	if($select_org_structure==1){	//����Ѻ�ͺ���§ҹ
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
		if(in_array("PER_COUNTRY", $list_type) && trim($search_pv_code)!="") $RPTORD_LIST .= "PROVINCE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
	//print_r($RPTORD_LIST);

	$select_list = "";		$order_by = "";	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.ORG_ID_REF as MINISTRY_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "c.ORG_ID_REF";

				$heading_name .= " ��ǹ�Ҫ���";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.DEPARTMENT_ID";

				$heading_name .= " ��ǹ�Ҫ���";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.ORG_ID";

				$heading_name .= " ��ǹ�Ҫ���";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.PL_CODE";

				$heading_name .= " ��§ҹ";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if(!$MINISTRY_ID) $order_by = "c.ORG_ID_REF";
		elseif(!$DEPARTMENT_ID) $order_by = "a.DEPARTMENT_ID";
		else $order_by = "a.ORG_ID";
	} // end if
	if(!trim($select_list)){ 
		if(!$MINISTRY_ID) $select_list = "c.ORG_ID_REF as MINISTRY_ID";
		elseif(!$DEPARTMENT_ID) $select_list = "a.DEPARTMENT_ID";
		else $select_list = "a.ORG_ID";
	} // end if

	$search_condition = "";
	$arr_search_condition[] = "(e.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(e.PER_STATUS = 1)";

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}/**elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if**/
		
		// ��ǹ��ҧ
		$list_type_text = "��ǹ��ҧ";
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.OT_CODE), 1)='1')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.OT_CODE), 1, 1)='1'";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}/**elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if**/
		
		// ��ǹ�����Ҥ
		//$list_type_text = "��ǹ�����Ҥ";
		//if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.OT_CODE), 1)='2')";
		//elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.OT_CODE), 1, 1)='2')";
	}
	if(in_array("PER_LINE", $list_type)){//�е�ͧ�դ�����Ҷ֧�����ҧ��§ҹ��
		// ���˹觻����� ��е��˹����§ҹ
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}/**elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if**/
		
		if($search_pt_name){ $list_type_text .=" ���˹觻�����$search_pt_name / "; }
		if($search_per_type==1){
			$per_name = "����Ҫ���";
			if(trim($search_pl_code)){
				$search_pl_code = trim($search_pl_code);
				$arr_search_condition[] = "(trim(a.PL_CODE)='$search_pl_code')";
				$list_type_text .= " ���˹����§ҹ$search_pl_name";
			}
		}/**elseif($search_per_type==2){
			$per_name = "�١��ҧ��Ш�";
			if(trim($search_pn_code)){
				$search_pn_code = trim($search_pn_code);
				$arr_search_condition[] = "(trim(a.PN_CODE)='$search_pn_code')";
				$list_type_text .= " ���˹����§ҹ$search_pn_name";
			}
		}elseif($search_per_type==3){
			$per_name = "��ѡ�ҹ�Ҫ���";
			if(trim($search_ep_code)){
				$search_ep_code = trim($search_ep_code);
				$arr_search_condition[] = "(trim(a.EP_CODE)='$search_ep_code')";
				$list_type_text .= " ���˹����§ҹ$search_ep_name";
			}
		} // end if**/
	}
	/**elseif($list_type == "PER_COUNTRY"){
		// ����� , �ѧ��Ѵ
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(a.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}**/
	if(in_array("ALL", $list_type) || !isset($list_type)){	//�óշ����� ������������͡ check box list_type ���
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}/**elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if**/
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "�ٻẺ����͡��§ҹ : $list_type_text";
	
	//�Ҩӹǹ����Ҫ���
	if($DPISDB=="odbc"){
			$cmd =" select 	count(e.PER_ID) as count_person
							from (
										(
											(
											(
												EAF_MASTER a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
											) left join EAF_PERSONAL d on (a.EAF_ID=d.EAF_ID)
										) left join PER_PERSONAL e on (d.PER_ID=e.PER_ID)
									) left join  PER_PRENAME f on (trim(e.PN_CODE)=trim(f.PN_CODE))
									$search_condition
						order by 	a.EAF_ID
						";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition); 
		$cmd = "select 		count(e.PER_ID) as count_person
						from 		EAF_MASTER a, PER_ORG b, PER_ORG c,EAF_PERSONAL d,PER_PERSONAL e,PER_PRENAME f
							where 	a.ORG_ID=b.ORG_ID and a.DEPARTMENT_ID=c.ORG_ID
											and a.EAF_ID=d.EAF_ID(+) and d.PER_ID=e.PER_ID(+)
											and trim(e.PN_CODE)=trim(f.PN_CODE) 
											$search_condition
							order by 	a.EAF_ID
						   ";
	}elseif($DPISDB=="mysql"){
			$cmd =" select 	count(e.PER_ID) as count_person
							from (
										(
											(
											(
												EAF_MASTER a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
											) left join EAF_PERSONAL d on (a.EAF_ID=d.EAF_ID)
										) left join PER_PERSONAL e on (d.PER_ID=e.PER_ID)
									) left join  PER_PRENAME f on (trim(e.PN_CODE)=trim(f.PN_CODE))
									$search_condition
						order by 	a.EAF_ID
						";
	} // end if
	$count_data = $db_dpis2->send_cmd($cmd);
	//	$db_dpis2->show_error();
	//  echo "<br>$cmd<br>";

	if($count_data==1){
		$data_dpis2 = $db_dpis2->get_array();
		$data_dpis2 = array_change_key_case($data_dpis2, CASE_LOWER);
		$count_person = $data_dpis2[count_person] ;
	} // end if

	$company_name .=" ��� $DEPARTMENT_NAME ";
	$company_name .=" �ӹǹ����Ҫ��ü���ռ����ķ����٧ : $count_person ��";
	
	$report_title = "�Ҿ�����þѲ�Ң���Ҫ��ü���ռ����ķ����٧ ��ṡ�����ǹ�Ҫ��� �дѺ���";
	$report_code = "R1206";
	$orientation='P';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('angsa','',12);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$heading_width[0] = "8";
	$heading_width[1] = "55";
	$heading_width[2] = "55";
	$heading_width[3] = "35";
	$heading_width[4] = "30";
	$heading_width[5] = "20";
	/*$heading_width[1] = "28";
	$heading_width[2] = "28";
	$heading_width[3] = "22";
	$heading_width[4] = "15";
	$heading_width[5] = "18";
	$heading_width[6] = "28";
	$heading_width[7] = "29";
	$heading_width[8] = "28";*/

	function print_header(){
		global $pdf, $heading_width, $heading_name;
		
		$pdf->SetFont('angsa','',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"���",'LTR',0,'L',1);
		$pdf->Cell($heading_width[1] ,7,"���͢���Ҫ���",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"���˹��������",'LTR',0,'C',1);			
		$pdf->Cell($heading_width[3] ,7,"���˹�",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] + $heading_width[5],7,"�������ҡ�þѲ��",'LTBR',1,'C',1);
		/*$pdf->Cell($heading_width[6] ,7,"��鹷ҧ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"��������´��ͺ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"��û����Թ",'LTR',1,'C',1);*/
	
		//�Ƿ�� 2
		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'L',1);
		$pdf->Cell($heading_width[1] ,7,"����ռ����ķ����٧",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LBR',0,'C',1);	
		$pdf->Cell($heading_width[3] ,7,"������",'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"��ͺ (��)",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"�Ѳ������ (��)",'LTBR',1,'C',1);
		/*$pdf->Cell($heading_width[6] ,7,"�����ع���¹",'LBR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"�����������ʺ��ó�",'LBR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"�š�û�Ժѵԧҹ",'LBR',1,'C',1);*/
	
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

	//�ʴ���ͺ�����������ʺ��ó�
	if($DPISDB=="odbc"){
			$cmd =" select 	a.EAF_ID, PM_CODE, PL_CODE, LEVEL_NO, PT_CODE, 
								a.ORG_ID, b.ORG_NAME, a.DEPARTMENT_ID, c.ORG_NAME as DEPARTMENT_NAME,c.ORG_ID_REF as MINISTRY_ID,
								a.EAF_NAME, a.EAF_ACTIVE,
								 f.PN_NAME, e.PER_ID, e.PER_NAME, e.PER_SURNAME
							from (
										(
											(
												(
													EAF_MASTER a
													inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
												) inner join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
											) left join EAF_PERSONAL d on (a.EAF_ID=d.EAF_ID)
										) left join PER_PERSONAL e on (d.PER_ID=e.PER_ID)
									) left join PER_PRENAME f on (trim(e.PN_CODE)=trim(f.PN_CODE))
							$search_condition
							order by 	a.EAF_ID
						  ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition); 
		$cmd = "select 		a.EAF_ID, PM_CODE, PL_CODE, e.LEVEL_NO, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, a.DEPARTMENT_ID, c.ORG_NAME as DEPARTMENT_NAME,c.ORG_ID_REF as MINISTRY_ID,
                                            a.EAF_NAME, a.EAF_ACTIVE,
											 f.PN_NAME, e.PER_ID, e.PER_NAME, e.PER_SURNAME
							from 		EAF_MASTER a, PER_ORG b, PER_ORG c,EAF_PERSONAL d,PER_PERSONAL e,PER_PRENAME f
							where 	a.ORG_ID=b.ORG_ID and a.DEPARTMENT_ID=c.ORG_ID
											and a.EAF_ID=d.EAF_ID(+) and d.PER_ID=e.PER_ID(+)
											and trim(e.PN_CODE)=trim(f.PN_CODE) 
											$search_condition
							order by 	a.EAF_ID
						   ";
		}elseif($DPISDB=="mysql"){
			$cmd =" select 	a.EAF_ID, PM_CODE, PL_CODE, LEVEL_NO, PT_CODE, 
								a.ORG_ID, b.ORG_NAME, a.DEPARTMENT_ID, c.ORG_NAME as DEPARTMENT_NAME,c.ORG_ID_REF as MINISTRY_ID,
								a.EAF_NAME, a.EAF_ACTIVE,
								 f.PN_NAME, e.PER_ID, e.PER_NAME, e.PER_SURNAME
							from (
										(
											(
												(
													EAF_MASTER a
													inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
												) inner join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
											) left join EAF_PERSONAL d on (a.EAF_ID=d.EAF_ID)
										) left join PER_PERSONAL e on (d.PER_ID=e.PER_ID)
									) left join PER_PRENAME f on (trim(e.PN_CODE)=trim(f.PN_CODE))
							$search_condition
							order by 	a.EAF_ID
						  ";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//  echo "<br>$cmd<br>";

	$data_count = 0;
	$data_row = 0;
	$GRAND_TOTAL = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		$temp_EAF_ID = trim($data[EAF_ID]);
		$current_list .= ((trim($current_list))?", ":"") . $temp_EAF_ID;
		$ORG_ID = trim($data[ORG_ID]);
		$ORG_NAME = trim($data[ORG_NAME]);
		$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		$DEPARTMENT_NAME = trim($data[DEPARTMENT_NAME]);
		$MINISTRY_ID = trim($data[MINISTRY_ID]);
		$PM_CODE = trim($data[PM_CODE]);
		$PL_CODE = trim($data[PL_CODE]);
		$PT_CODE = trim($data[PT_CODE]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		
		$PN_NAME = trim($data[PN_NAME]);
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);
		$FULLNAME = ($PN_NAME)."$PER_NAME $PER_SURNAME";
		
		//�ҵ��˹觻�����
		$cmd = "select LEVEL_NAME, LEVEL_SHORTNAME from PER_LEVEL where (LEVEL_ACTIVE=1) and (PER_TYPE = $search_per_type) and (LEVEL_NO='$LEVEL_NO')order by  LEVEL_SEQ_NO,LEVEL_NO";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		
		$arr_temp = explode(" ", trim($data_dpis2[LEVEL_NAME]));
		//�Ҫ��͵��˹觻�����
		if($search_per_type==1){
			$POSITION_TYPE = str_replace("������", "", $arr_temp[0]);
		}elseif($search_per_type==2){
			$POSITION_TYPE = $arr_temp[0];
		}elseif($search_per_type==3){
			$POSITION_TYPE = str_replace("������ҹ", "", $arr_temp[0]);
		}
		//�Ҫ����дѺ���˹�
		//$arr_temp[1]=str_replace("�дѺ", "", $arr_temp[1]);
		$LEVEL_NAME =  trim($arr_temp[1]);						

		$EAF_NAME = trim($data[EAF_NAME]);
        $EAF_ACTIVE = trim($data[EAF_ACTIVE]);

		$cmd = " select ORG_NAME from PER_ORG where trim(ORG_ID)='".$MINISTRY_ID."' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$MINISTRY_NAME = $data_dpis2[ORG_NAME];

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

		//���������ҡ�þѲ�� ��ͺ���� ***eaf_earning_structure
		if($DPISDB=="odbc"){
			$cmd = " select	SUM(ELS_PERIOD) as SUM_MONTH_PERIOD
						from			EAF_LEARNING_STRUCTURE
						where		EAF_ID=$temp_EAF_ID
						order by 	ELS_SEQ_NO, ELS_LEVEL, ELS_ID 	
						";	
		}elseif($DPISDB=="oci8"){
			$cmd = "select			SUM(ELS_PERIOD) as SUM_MONTH_PERIOD
								from			EAF_LEARNING_STRUCTURE
								where		EAF_ID=$temp_EAF_ID
								order by 	ELS_SEQ_NO, ELS_LEVEL, ELS_ID  
							";						
		}elseif($DPISDB=="mysql"){
			$cmd = " select	SUM(ELS_PERIOD) as SUM_MONTH_PERIOD
						from			EAF_LEARNING_STRUCTURE
						where		EAF_ID=$temp_EAF_ID
						order by 	ELS_SEQ_NO, ELS_LEVEL, ELS_ID 	
						";	
		} // end if
		$db_dpis2->send_cmd($cmd);
		// echo "<br>$cmd<br>";
		//	$db_dpis2->show_error();
		while ($data_dpis2 = $db_dpis2->get_array()) {
				$SUM_ELS_PERIOD = $data_dpis2[SUM_MONTH_PERIOD];

				if($SUM_ELS_PERIOD < 12){
					$SHOW_ELS_PERIOD = "$SUM_ELS_PERIOD ��͹";
				}else{
					$SHOW_ELS_PERIOD = floor($SUM_ELS_PERIOD / 12)." ��";
					$REMAIN_ELS_PERIOD = $SUM_ELS_PERIOD % 12;
					if($REMAIN_ELS_PERIOD > 0) $SHOW_ELS_PERIOD .= " $REMAIN_ELS_PERIOD ��͹";
				}

				/***$TMP_ELS_ID = $data[ELS_ID];
				$current_list .= ((trim($current_list))?",":"") . $TMP_ELS_ID;
				$ELS_LEVEL = $data[ELS_LEVEL];
				$ELS_PERIOD = $data[ELS_PERIOD];
				
				if($ELS_PERIOD < 12){
					$SHOW_ELS_PERIOD = "$ELS_PERIOD ��͹";
				}else{
					$SHOW_ELS_PERIOD = floor($ELS_PERIOD / 12)." ��";
					$REMAIN_ELS_PERIOD = $ELS_PERIOD % 12;
					if($REMAIN_ELS_PERIOD > 0) $SHOW_ELS_PERIOD .= " $REMAIN_ELS_PERIOD ��͹";
				}***/
		} //while
		###########
		$data_row++;
		$arr_content[$data_count][sequence] = $data_row;
		$arr_content[$data_count][eaf_name] = $EAF_NAME;
		$arr_content[$data_count][name] = $FULLNAME;
		$arr_content[$data_count][position_type] = $POSITION_TYPE." ".$LEVEL_NAME;
		$arr_content[$data_count][position_lline] = $PL_NAME;
		$arr_content[$data_count][position_mgt] = $PM_NAME;
		$arr_content[$data_count][org_name] = $ORG_NAME;
		$arr_content[$data_count][show_els_period] = $SHOW_ELS_PERIOD;	
		
		//$arr_content[$data_count][org_name] = $MINISTRY_NAME." ".$DEPARTMENT_NAME." ".$ORG_NAME;
		
		$data_count++;
	} // end while
	$GRAND_TOTAL = array_sum($LEVEL_GRAND_TOTAL);
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$COUNT = $arr_content[$data_count][sequence];
			$EAF_NAME=$arr_content[$data_count][eaf_name];
			$PER_NAME=$arr_content[$data_count][name];
			$PER_TYPE=$arr_content[$data_count][position_type];
			$PER_LINE=$arr_content[$data_count][position_lline];
			$PER_MGT=$arr_content[$data_count][position_mgt];
			$ORG_NAME=$arr_content[$data_count][org_name];
			$SHOW_ELS_PERIOD=$arr_content[$data_count][show_els_period];
			
			$border = "";
			if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0){
				$pdf->SetFont('angsa','',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			}else{
				$pdf->SetFont('angsa','',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			} // end if

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->MultiCell($heading_width[0], 7, "$COUNT", $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0];
			$pdf->y = $start_y;
			
			$pdf->MultiCell($heading_width[1], 7, "$PER_NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			
			$pdf->MultiCell($heading_width[2], 7, "$EAF_NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[3], 7, "$PER_TYPE", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[4], 7, "$SHOW_ELS_PERIOD", $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[5], 7, "-", $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5];
			$pdf->y = $start_y;
			/*$pdf->Cell($heading_width[6], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[7], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[8], 7, "", $border, 0, 'C', 0);*/
			
			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
			//for($i=0; $i<=9; $i++){
			for($i=0; $i<=6; $i++){
				$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
				$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
			//====================================================

			//if(($pdf->h - $max_y - 10) < 9){ 
			if(($pdf->h - $max_y - 10) < 6){ 
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
	}else{
		$pdf->SetFont('angsa','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ����բ����� **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();		

?>