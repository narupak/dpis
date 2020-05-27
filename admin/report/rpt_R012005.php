<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", "1800");
	
	$IMG_PATH = "../../attachment/pic_personal/";	
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$arr_history_name = explode("|", $HISTORY_LIST);
	//print_r($arr_history_name);
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE=$search_per_type)";

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(e.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	
		// ��ǹ��ҧ
		$list_type_text = "��ǹ��ҧ";
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.OT_CODE), 1)='1')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.OT_CODE), 1, 1)='1'";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(e.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		// ��ǹ�����Ҥ
		$list_type_text = "��ǹ�����Ҥ";
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.OT_CODE), 1)='2')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.OT_CODE), 1, 1)='2')";
	}
	if(in_array("PER_LINE", $list_type)){
		// ���˹觻����� ��е��˹����§ҹ
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(e.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			 $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		if($search_pt_name){ $list_type_text .=" ���˹觻�����$search_pt_name / "; }
		if($search_per_type==1){
			$per_name = "����Ҫ���";
			if(trim($search_pl_code)){
				$search_pl_code = trim($search_pl_code);
				///$arr_search_condition[] = "(trim(d.PL_CODE)='$search_pl_code')"; //����Ѻ���ҵ��˹����§ҹ�ͧ����Ҫ���
				$arr_search_condition[] = "(trim(c.PL_CODE)='$search_pl_code')"; //����Ѻ���ҵ��˹����§ҹ�ͧ��ͺ EAF
				$list_type_text .= " ���˹����§ҹ$search_pl_name";
			}
		}
	}
	if(in_array("PER_COUNTRY", $list_type)){ //PER_ORG
		// ����� , �ѧ��Ѵ
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(e.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//�óշ����� ������������͡ check box list_type ���
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(e.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
		
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "�ٻẺ����͡��§ҹ : $list_type_text";
	
	$report_title = "��ͺ�����������ʺ��ó���ºؤ�Ţͧ����Ҫ��ü���ռ����ķ����٧";
	$report_code = "R1205";
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
	$pdf->SetFont('angsa','',14);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$heading_width[EAF][0] = "40";
	$heading_width[EAF][1] = "32";
	$heading_width[EAF][2] = "25";
	$heading_width[EAF][3] = "10";
	$heading_width[EAF][4] = "25";
	$heading_width[EAF][5] = "25";
	$heading_width[EAF][6] = "28";
	$heading_width[EAF][7] = "18";
	
	function print_header($HISTORY_NAME){
		global $pdf, $heading_width;
		
		$pdf->SetFont('angsa','',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		//$pdf->Cell(100,2,"",0,1,'C');

		switch($HISTORY_NAME){
			case "EAF" :
				$pdf->Cell($heading_width[EAF][0] ,7,"˹��§ҹ",'LTR',0,'C',1);
				$pdf->Cell($heading_width[EAF][1] ,7,"�������",'LTR',0,'C',1);
				$pdf->Cell($heading_width[EAF][2] ,7,"��Ǻ觪��ĵԡ���",'LTR',0,'C',1);			
				$pdf->Cell($heading_width[EAF][3] ,7,"����",'LTR',0,'C',1);
				$pdf->Cell($heading_width[EAF][4] ,7,"����͹�ҹ",'LTR',0,'C',1);
				$pdf->Cell($heading_width[EAF][5] ,7,"��䡡�þѲ��",'LTR',0,'C',1);
				$pdf->Cell($heading_width[EAF][6] + $heading_width[EAF][7] ,7,"�š�û�Ժѵԧҹ",'LTBR',1,'C',1);
			
				//�Ƿ�� 2
				$pdf->Cell($heading_width[EAF][0] ,7,"",'LBR',0,'L',1);
				$pdf->Cell($heading_width[EAF][1] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[EAF][2] ,7,"",'LBR',0,'C',1);	
				$pdf->Cell($heading_width[EAF][3] ,7,"����",'LBR',0,'C',1);
				$pdf->Cell($heading_width[EAF][4] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[EAF][5] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[EAF][6] ,7,"(��ҹ/����ҹ/��º�͹)",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[EAF][7] ,7,"��ṹ",'LTBR',1,'C',1);
			break;	
		} // end switch case
	} // function
	
	function print_personal_header($PER_FULLNAME,$PER_POSITION,$EAF_NAME,$SUM_EPS_PERIOD){
				global $pdf, $heading_width;
				
				//�ʴ���������´����Ҫ���
				$pdf->SetFont('angsa','',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				$pdf->Cell(85,7,"����  ".$PER_FULLNAME,0,1,"L");
			
				$pdf->Cell(100,7,$pdf->Text_print_optimize("���˹�  ".$PER_POSITION, 100),0,0,"L");
				$pdf->Cell(100,7,"���˹�������� ".($EAF_NAME?$EAF_NAME:"-"),0,1,"L");
				$pdf->Cell(150,7,"�������ҡ�þѲ�ҵ����ͺ�����������ʺ��ó� ". ($SUM_EPS_PERIOD?$SUM_EPS_PERIOD:"-"),0,1,"L");
		
				$pdf->AutoPageBreak = false;
	}
	
	//�Ң����Ţ���Ҫ���
	if($DPISDB=="odbc"){
		$cmd = "select			a.PN_CODE, a.PER_NAME, a.PER_SURNAME,a.DEPARTMENT_ID, a.POS_ID,a.LEVEL_NO, a.PER_CARDNO,
											b.EP_ID, b.EP_YEAR, b.PER_ID, b.PER_ID_REVIEW, b.PER_ID_REVIEW1, b.PER_ID_REVIEW2, c.EAF_ID,c.EAF_NAME,
											d.POS_NO, d.PM_CODE, d.PL_CODE, d.PT_CODE, d.ORG_ID as PER_ORG, d.ORG_ID_1 as PER_ORG1, d.ORG_ID_2 as PER_ORG2
						 from			PER_PERSONAL a, EAF_PERSONAL  b, EAF_MASTER c, PER_POSITION d, PER_ORG e
						 where		a.PER_ID=b.PER_ID and b.EAF_ID=c.EAF_ID and a.POS_ID=d.POS_ID(+) and a.DEPARTMENT_ID=e.ORG_ID(+)
											$search_condition
						order by 		b.EP_YEAR, a.PER_NAME, a.PER_SURNAME 
						";			
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "select			a.PN_CODE, a.PER_NAME, a.PER_SURNAME,a.DEPARTMENT_ID, a.POS_ID,a.LEVEL_NO, a.PER_CARDNO,
											b.EP_ID, b.EP_YEAR, b.PER_ID, b.PER_ID_REVIEW, b.PER_ID_REVIEW1, b.PER_ID_REVIEW2, c.EAF_ID,c.EAF_NAME,
											d.POS_NO, d.PM_CODE, d.PL_CODE, d.PT_CODE, d.ORG_ID as PER_ORG, d.ORG_ID_1 as PER_ORG1, d.ORG_ID_2 as PER_ORG2
						 from			PER_PERSONAL a, EAF_PERSONAL  b, EAF_MASTER c, PER_POSITION d, PER_ORG e
						 where		a.PER_ID=b.PER_ID(+) and b.EAF_ID=c.EAF_ID(+) and a.POS_ID=d.POS_ID(+) and a.DEPARTMENT_ID=e.ORG_ID(+)
											$search_condition
						order by 		b.EP_YEAR, a.PER_NAME, a.PER_SURNAME 
						";			
	}elseif($DPISDB=="mysql"){
		$cmd = "select			a.PN_CODE, a.PER_NAME, a.PER_SURNAME,a.DEPARTMENT_ID, a.POS_ID,a.LEVEL_NO, a.PER_CARDNO,
											b.EP_ID, b.EP_YEAR, b.PER_ID, b.PER_ID_REVIEW, b.PER_ID_REVIEW1, b.PER_ID_REVIEW2, c.EAF_ID,c.EAF_NAME,
											d.POS_NO, d.PM_CODE, d.PL_CODE, d.PT_CODE, d.ORG_ID as PER_ORG, d.ORG_ID_1 as PER_ORG1, d.ORG_ID_2 as PER_ORG2
						 from			PER_PERSONAL a, EAF_PERSONAL  b, EAF_MASTER c, PER_POSITION d, PER_ORG e
						 where		a.PER_ID=b.PER_ID and b.EAF_ID=c.EAF_ID and a.POS_ID=d.POS_ID(+) and a.DEPARTMENT_ID=e.ORG_ID(+)
											$search_condition
						order by 		b.EP_YEAR, a.PER_NAME, a.PER_SURNAME 
						";				
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "<br>$cmd<br>";
//	$db_dpis->show_error();

	if($count_data){
		$data_count = 0;
		$CHK_EPS_PERIOD = array();
		while($data = $db_dpis->get_array()){
			$data_count++;
			$TMP_EP_ID = $data[EP_ID];
			$current_list .= ((trim($current_list))?",":"") . $TMP_EP_ID;
			$EP_YEAR = $data[EP_YEAR];
			$PN_CODE = $data[PN_CODE];
			$PER_ID = trim($data[PER_ID]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			
			$TMP_EAF_ID = trim($data[EAF_ID]);
			$EAF_NAME = $data[EAF_NAME];
			$DEPARTMENT_ID = $data[DEPARTMENT_ID];
			
			$POS_ID = $data[POS_ID];
			$POS_NO = $data[POS_NO];
			$PM_CODE = trim($data[PM_CODE]);
			$PL_CODE = trim($data[PL_CODE]);
			$PT_CODE = trim($data[PT_CODE]);
			
			$PER_ORG_ID = $data[PER_ORG];
			$PER_ORG_ID1 = $data[PER_ORG1];
			$PER_ORG_ID2 = $data[PER_ORG2];
			
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PN_NAME = $data2[PN_NAME];
			
			$PER_FULLNAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;
			
			$cmd = " select a.ORG_ID, b.ORG_NAME from PER_POSITION a, PER_ORG b where a.POS_ID=$POS_ID and a.ORG_ID=b.ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_ID = $data2[ORG_ID];
			$ORG_NAME = $data2[ORG_NAME];
			
			//�ͧ����Ҫ������Ф�
			$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$MINISTRY_ID = $data2[ORG_ID_REF];
			$DEPARTMENT_NAME = $data2[ORG_NAME];
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$MINISTRY_NAME = $data2[ORG_NAME];

			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE) = '$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PL_NAME = trim($data2[PL_NAME]);

			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE) = '$PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PT_NAME = trim($data2[PT_NAME]);
			
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
//			$db_dpis2->show_error();
			$PM_NAME = trim($data2[PM_NAME]);

			$LEVEL_NO = trim($data[LEVEL_NO]);
			$PER_CARDNO = $data[PER_CARDNO];
			$img_file = "";
			if($PER_CARDNO && file_exists($IMG_PATH.$PER_CARDNO.".jpg")) $img_file = $IMG_PATH.$PER_CARDNO.".jpg";			

			//�ҵ��˹觻�����
			$cmd = "select LEVEL_NAME, LEVEL_SHORTNAME from PER_LEVEL where (LEVEL_ACTIVE=1) and (PER_TYPE = $search_per_type) and (LEVEL_NO='$LEVEL_NO')order by  LEVEL_SEQ_NO,LEVEL_NO";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			
			$arr_temp = explode(" ", trim($data2[LEVEL_NAME]));
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
			if(!trim($LEVEL_NAME)){	$LEVEL_NAME =   level_no_format($LEVEL_NO); }
			$PER_POSITION = (trim($PM_NAME)?$PM_NAME:"") . (trim($PL_NAME)?($PL_NAME ." ". $LEVEL_NAME . (($PT_NAME != "�����" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"�дѺ ".$LEVEL_NAME);

			//***************************************************************
			//�������������㹡�þѲ�ҡ�ͺ����
			//***************************************************************
			if($DPISDB=="odbc"){
				$cmd = "select	 	SUM(EPS_PERIOD) as SUM_MONTH_PERIOD
								from			EAF_PERSONAL_STRUCTURE
								where		EP_ID=$TMP_EP_ID
								order by 	EPS_SEQ_NO, EPS_LEVEL, EPS_ID
								";
			}elseif($DPISDB=="oci8"){
				$cmd = "select	 	SUM(EPS_PERIOD) as SUM_MONTH_PERIOD
								from			EAF_PERSONAL_STRUCTURE
								where		EP_ID=$TMP_EP_ID
								order by 	EPS_SEQ_NO, EPS_LEVEL, EPS_ID
								";
			}elseif($DPISDB=="mysql"){
				$cmd = "select	 	SUM(EPS_PERIOD) as SUM_MONTH_PERIOD
								from			EAF_PERSONAL_STRUCTURE
								where		EP_ID=$TMP_EP_ID
								order by 	EPS_SEQ_NO, EPS_LEVEL, EPS_ID
								";
			} // end if
			$count_period = $db_dpis2->send_cmd($cmd);
			// echo "<br>$cmd<br>";
			//	$db_dpis2->show_error();
			
			if($count_period){
				$data2 = $db_dpis2->get_array();
				$SUM_MONTH_PERIOD = $data2[SUM_MONTH_PERIOD];
				if($SUM_MONTH_PERIOD < 12){
					$SUM_EPS_PERIOD = "$SUM_MONTH_PERIOD ��͹";
				}else{
					$SUM_EPS_PERIOD = floor($SUM_MONTH_PERIOD / 12)." ��";
					$REMAIN_EPS_PERIOD = $SUM_MONTH_PERIOD % 12;
					if($REMAIN_EPS_PERIOD > 0) $SUM_EPS_PERIOD .= " $REMAIN_EPS_PERIOD ��͹";
				}
				 //�ʴ������Ţ���Ҫ���
				print_personal_header($PER_FULLNAME,$PER_POSITION,$EAF_NAME,$SUM_EPS_PERIOD);
			///}//end count_period

				for($history_index=0; $history_index<count($arr_history_name); $history_index++){
					$HISTORY_NAME = $arr_history_name[$history_index];
					switch($HISTORY_NAME){
						case "EAF" :
								if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
								$pdf->SetFont('angsa','',14);
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
								$pdf->Cell(200,7,($history_index + 1)."  �����š�ͺ�����������ʺ��ó� ",0,1,"L");
								print_header($HISTORY_NAME);
	
								//�ʴ���������´��ͺ�����������ʺ��ó� case
								if($DPISDB=="odbc"){
									$cmd = "select a.EPS_ID, EPS_LEVEL, EPS_PERIOD, MINISTRY_ID, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2,
															EPK_ID, EPK_NAME,EPK_BEHAVIOR,EPK_COACH,EPK_JOB 
													from EAF_PERSONAL_STRUCTURE a, EAF_PERSONAL_KNOWLEDGE b 
													where a.EPS_ID=b.EPS_ID and a.EP_ID=$TMP_EP_ID
													order by b.EPS_ID 
													";
								}elseif($DPISDB=="oci8"){
									$cmd = "select a.EPS_ID, EPS_LEVEL, EPS_PERIOD, MINISTRY_ID, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2,
															EPK_ID, EPK_NAME,EPK_BEHAVIOR,EPK_COACH,EPK_JOB 
													from EAF_PERSONAL_STRUCTURE a, EAF_PERSONAL_KNOWLEDGE b 
													where a.EPS_ID=b.EPS_ID(+) and a.EP_ID=$TMP_EP_ID
													order by b.EPS_ID 
													";
								}elseif("mysql"){
									$cmd = "select a.EPS_ID, EPS_LEVEL, EPS_PERIOD, MINISTRY_ID, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2,
															EPK_ID, EPK_NAME,EPK_BEHAVIOR,EPK_COACH,EPK_JOB 
													from EAF_PERSONAL_STRUCTURE a, EAF_PERSONAL_KNOWLEDGE b 
													where a.EPS_ID=b.EPS_ID and a.EP_ID=$TMP_EP_ID
													order by b.EPS_ID 
													";
								 }// end if
								$count_eaf = $db_dpis2->send_cmd($cmd);
								//echo "<br>$cmd<br>";
								//$db_dpis2->show_error();
								
								if($count_eaf){
									$eaf_count = 0;
									while($data2 = $db_dpis2->get_array()){
										$eaf_count++;
										$TMP_EPS_ID = $data2[EPS_ID];
										$current_list .= ((trim($current_list))?",":"") . $TMP_EPS_ID;
										$EPS_LEVEL = $data2[EPS_LEVEL];
										switch($EPS_LEVEL){
											case 1 :
												$EPS_LEVEL = "�дѺ��鹰ҹ";
												break;
											case 2 :
												$EPS_LEVEL = "�дѺ�٧";
												break;
											case 3 :
												$EPS_LEVEL = "�дѺ������";
												break;
										} // switch case
										
										$TMP_EPK_ID = $data2[EPK_ID];
										$current_list .= ((trim($current_list))?",":"") . $TMP_EPK_ID;
										$EPK_NAME = $data2[EPK_NAME];
										$EPK_BEHAVIOR = $data2[EPK_BEHAVIOR];
										$EPK_COACH = $data2[EPK_COACH];
										$EPK_TRAIN = $data2[EPK_TRAIN];
										$EPK_JOB = $data2[EPK_JOB];
										$EPS_PERIOD[$TMP_EPK_ID] = $data2[EPS_PERIOD];
										
										//�Ѵ�������ҷ���ӡѹ�͡ ������ EPS_ID ���ǡѹ
										if (!in_array($TMP_EPS_ID, $CHK_EPS_PERIOD)) {
											$CHK_EPS_PERIOD[]=$TMP_EPS_ID;

											if($EPS_PERIOD[$TMP_EPK_ID] < 12){
												$SHOW_EPS_PERIOD[$TMP_EPK_ID] = "$EPS_PERIOD[$TMP_EPK_ID] ��͹";
											}else{
												$SHOW_EPS_PERIOD[$TMP_EPK_ID] = floor($EPS_PERIOD[$TMP_EPK_ID] / 12)." ��";
												$REMAIN_EPS_PERIOD[$TMP_EPK_ID] = $EPS_PERIOD[$TMP_EPK_ID] % 12;
												if($REMAIN_EPS_PERIOD[$TMP_EPK_ID] > 0) $SHOW_EPS_PERIOD[$TMP_EPK_ID] .= " $REMAIN_EPS_PERIOD[$TMP_EPK_ID] ��͹";
											}
										}else{
											$SHOW_EPS_PERIOD[$TMP_EPK_ID] = "";
										}
										
										//˹��§ҹ���֡�͹
										$MINISTRY_ID = $data2[MINISTRY_ID];
										$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
										$db_dpis3->send_cmd($cmd);
										$data_dpis3 = $db_dpis3->get_array();
										$MINISTRY_NAME_E = $data_dpis3[ORG_NAME];
									
										$DEPARTMENT_ID = $data2[DEPARTMENT_ID];
										$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
										$db_dpis3->send_cmd($cmd);
										$data_dpis3 = $db_dpis3->get_array();
										$DEPARTMENT_NAME_E  = $data_dpis3[ORG_NAME];
									
										$ORG_ID = $data2[ORG_ID];
										$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
										$db_dpis3->send_cmd($cmd);
										$data_dpis3 = $db_dpis3->get_array();
										$ORG_NAME_E  = $data_dpis3[ORG_NAME];
										
										$ORG_ID_1 = $data2[ORG_ID_1];
										$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
										$db_dpis3->send_cmd($cmd);
										$data_dpis3 = $db_dpis3->get_array();
										$ORG_NAME_1_E  = $data_dpis3[ORG_NAME];
									
										$ORG_ID_2 = $data2[ORG_ID_2];
										$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
										$db_dpis3->send_cmd($cmd);
										$data_dpis3 = $db_dpis3->get_array();
										$ORG_NAME_2_E  = $data_dpis3[ORG_NAME];
								
										//��˹���ҵ����
										$ORG_NAME_EAF = $ORG_NAME_E." ".$DEPARTMENT_NAME_E." ".$MINISTRY_NAME_E;  //�������˹��§ҹ
										$KNOWLEDGE = $EPK_NAME;
										$BEHAVIOR = $EPK_BEHAVIOR;
										$COACH = $EPK_COACH;
										$DEVELOPE = $EPK_JOB;         
										$RESULT = "";
										$MARK = "";
	
										$border = "";
										$pdf->SetFont('angsa','',12);
										$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
										
										$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
									
										$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$ORG_NAME_EAF", $border, "L");
										if($pdf->y > $max_y) $max_y = $pdf->y;
										$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
										$pdf->y = $start_y;
										
										$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$KNOWLEDGE", $border, "L");
										if($pdf->y > $max_y) $max_y = $pdf->y;
										$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
										$pdf->y = $start_y;
										
										$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$BEHAVIOR", $border, "L");
										if($pdf->y > $max_y) $max_y = $pdf->y;
										$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
										$pdf->y = $start_y;
										
										$pdf->MultiCell($heading_width[$HISTORY_NAME][3], 7, "$SHOW_EPS_PERIOD[$TMP_EPK_ID]", $border, "L");
										if($pdf->y > $max_y) $max_y = $pdf->y;
										$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3];
										$pdf->y = $start_y;
										
										$pdf->MultiCell($heading_width[$HISTORY_NAME][4], 7, "$COACH", $border, "L");
										if($pdf->y > $max_y) $max_y = $pdf->y;
										$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3] + $heading_width[$HISTORY_NAME][4];
										$pdf->y = $start_y;
										
										$pdf->MultiCell($heading_width[$HISTORY_NAME][5], 7, "$DEVELOPE", $border, "L");
										if($pdf->y > $max_y) $max_y = $pdf->y;
										$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3] + $heading_width[$HISTORY_NAME][4] + $heading_width[$HISTORY_NAME][5];
										$pdf->y = $start_y;
										
										$pdf->Cell($heading_width[$HISTORY_NAME][6], 7, $RESULT, $border, 0, 'C', 0);
										$pdf->Cell($heading_width[$HISTORY_NAME][7], 7, $MARK, $border, 0, 'C', 0);
					
											//================= Draw Border Line ====================
											$line_start_y = $start_y;		$line_start_x = $start_x;
											$line_end_y = $max_y;		$line_end_x = $start_x;
											$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
						
											for($i=0; $i<=8; $i++){
												$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
												$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
												$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
											} // end for
											//====================================================
					
										if(($pdf->h - $max_y - 8) < 15){ 
											$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
											if($eaf_count < $count_eaf){
												$pdf->AddPage();
												print_header($HISTORY_NAME);
												$max_y = $pdf->y;
											} // end if
										}else{
											if($eaf_count == $count_eaf) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										} // end if
										$pdf->x = $start_x;			$pdf->y = $max_y;
									} // end while
								}else{ //count_eaf
									$pdf->SetFont('angsa','',16);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									$pdf->Cell(200,10,"********** ����բ����� **********","LBR",1,'C');
								} // end if
							break;
					} // end switch
				} // end for
				if($data_count < $count_data) $pdf->AddPage();
		}//end count_period		
	} // end while
}else{
		$pdf->SetFont('angsa','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ����բ����� **********",0,1,'C');
} // end if

	$pdf->close();
	$pdf->Output();
?>