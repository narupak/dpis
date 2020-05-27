<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if(!trim($RPTORD_LIST)){ 
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		elseif($list_type=="PER_COUNTRY" && trim($search_pv_code)!="") $RPTORD_LIST = "PROVINCE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
	$arr_rpt_order = array("POEMNO"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "POEMNO" :
				if($select_list) $select_list .= ", ";

				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") {
					$select_list .= "a.POEM_NO_NAME, IIf(IsNull(a.POEM_NO), 0 , CLng(a.POEM_NO)) as POEM_NO";				
					$order_by .= "a.POEM_NO_NAME, IIf(IsNull(a.POEM_NO), 0 , CLng(a.POEM_NO))";
				}
				if($DPISDB=="oci8") {
					$select_list .= "a.POEM_NO_NAME, a.POEM_NO";
					$order_by .= "a.POEM_NO_NAME, to_number(replace(a.POEM_NO,'-',''))";
				}elseif($DPISDB=="mysql"){
					$select_list .= "a.POEM_NO_NAME, a.POEM_NO";				
					$order_by .= "a.POEM_NO_NAME, a.POEM_NO";
				}

				$heading_name .= " เลขที่ตำแหน่ง";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) $order_by = "a.POEM_NO_NAME, a.POEM_NO";
	if(!trim($select_list)) $select_list = "a.POEM_NO_NAME, a.POEM_NO";

	$search_condition = "";
	$arr_search_condition[] = "(a.POEM_STATUS=1)";

	$list_type_text = $ALL_REPORT_TITLE;

	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
	}elseif($MINISTRY_ID){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		$list_type_text .= " - $MINISTRY_NAME";
	}elseif($PROVINCE_CODE){
		$PROVINCE_CODE = trim($PROVINCE_CODE);
		$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	$todate = date("Y-m-d");
	$show_date = show_date_format($todate, 3);

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "แบบ คปร.9 - พลเรือน ส่วนราชการ : $list_type_text";
	$report_title = "แบบสำรวจข้อมูลลูกจ้างประจำที่จ้างด้วยเงินงบประมาณ งบบุคลากร ณ วันที่ ". ($show_date?(($NUMBER_DISPLAY==2)?convert2thaidigit($show_date):$show_date):"-");
	$report_code = "คปร.9";
	$orientation='L';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
//	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$heading_width[0] = "7";
	$heading_width[1] = "25";
	$heading_width[2] = "20";
	$heading_width[3] = "30";
	$heading_width[4] = "20";
	$heading_width[5] = "8";
	$heading_width[6] = "5";
	$heading_width[7] = "5";
	$heading_width[8] = "5";
	$heading_width[9] = "5";
	$heading_width[10] = "5";
	$heading_width[11] = "5";
	$heading_width[12] = "15";
	$heading_width[13] = "20";
	$heading_width[14] = "8";
	$heading_width[15] = "13";
	$heading_width[16] = "16";
	$heading_width[17] = "16";
	$heading_width[18] = "20";
	$heading_width[19] = "14";
	$heading_width[20] = "12";
	$heading_width[21] = "17";

	function print_header(){
		global $pdf, $heading_width, $heading_name,$ORG_TITLE;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"(1)",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[1]+$heading_width[2]) ,7,"(2)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"(3)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"(4)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"(5)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[6]+$heading_width[7]+$heading_width[8] ,7,"(6)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[9]+$heading_width[10]+$heading_width[11] ,7,"(7)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[12] ,7,"(8)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[13] ,7,"(9)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[14] ,7,"(10)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[15] ,7,"(11)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[16] ,7,"(12)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[17] ,7,"(13)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[18] ,7,"(14)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[19] ,7,"(15)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[20] ,7,"(16)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[21] ,7,"(17)",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[1]+$heading_width[2],7,"ชื่อส่วนราชการ",'LR',0,'C',1);
		$pdf->Cell($heading_width[3],7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"เลข",'LR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[6] + $heading_width[7] + $heading_width[8],7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[9] + $heading_width[10] + $heading_width[11] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[12],7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[13],7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[14] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[15],7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[16],7,"",'LR',0,'C',1);		
		$pdf->Cell($heading_width[17],7,"พื้นที่",'LR',0,'C',1);
		$pdf->Cell($heading_width[18],7,"วุฒิการศึกษา/",'LR',0,'C',1);
		$pdf->Cell($heading_width[19] ,7,"อัตรา",'LR',0,'C',1);
		$pdf->Cell($heading_width[20],7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[21],7,"",'LR',1,'C',1);		

		$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LR',0,'C',1);
		$pdf->Cell($heading_width[1] + $heading_width[2],7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[3],7,"ชื่อ-สกุล",'LR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ประจำตัว",'LR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"เพศ",'LR',0,'C',1);
		$pdf->Cell($heading_width[6] + $heading_width[7] + $heading_width[8],7,"เกิด",'LR',0,'C',1);
		$pdf->Cell($heading_width[9] + $heading_width[10] + $heading_width[11] ,7,"บรรจุ",'LR',0,'C',1);
		$pdf->Cell($heading_width[12] ,7,"หมวด",'LR',0,'C',1);
		$pdf->Cell($heading_width[13] ,7,"ชื่อตำแหน่ง",'LR',0,'C',1);
		$pdf->Cell($heading_width[14] ,7,"ชั้น",'LR',0,'C',1);
		$pdf->Cell($heading_width[15],7,"ตำแหน่ง",'LR',0,'C',1);
		$pdf->Cell($heading_width[16] ,7,"ส่วนกลาง",'LR',0,'C',1);
		$pdf->Cell($heading_width[17] ,7,"ปฏิบัติงาน",'LBR',0,'C',1);
		$pdf->Cell($heading_width[18] ,7,"ประกาศนียบัตร",'LR',0,'C',1);
		$pdf->Cell($heading_width[19] ,7,"เงินเดือน",'LR',0,'C',1);
		$pdf->Cell($heading_width[20] ,7,"ปีที่",'LR',0,'C',1);
		$pdf->Cell($heading_width[21] ,7,"หมายเหตุ",'LR',1,'C',1);
		
		$pdf->Cell($heading_width[0] ,7,"ที่",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"$ORG_TITLE",'TLBR',0,'C',1);	
		$pdf->Cell($heading_width[2] ,7,"เขต/แขวง/ศูนย์",'TLBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ประชาชน",'LBR',0,'C',1);	
		$pdf->Cell($heading_width[5] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"ว",'TLBR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"ด",'TLBR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"ป",'TLBR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"ว",'TLBR',0,'C',1);
		$pdf->Cell($heading_width[10] ,7,"ด",'TLBR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"ป",'TLBR',0,'C',1);
		$pdf->Cell($heading_width[12] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[13] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[14] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[15] ,7,"เลขที่",'LBR',0,'C',1);
		$pdf->Cell($heading_width[16] ,7,"/ภูมิภาค",'LBR',0,'C',1);
		$pdf->Cell($heading_width[17] ,7,"(จังหวัด)",'LBR',0,'C',1);
		$pdf->Cell($heading_width[18] ,7,"เฉพาะทาง",'LBR',0,'C',1);
		$pdf->Cell($heading_width[19] ,7,"ปัจจุบัน",'LBR',0,'C',1);
		$pdf->Cell($heading_width[20] ,7,"เกษียณ",'LBR',0,'C',1);
		$pdf->Cell($heading_width[21] ,7,"",'LBR',1,'C',1);
	} // function		

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order;
		global $POEM_NO;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "POEMNO" :	
					if($POEM_NO) $arr_addition_condition[] = "(trim(a.POEM_NO) = '$POEM_NO')";
					else $arr_addition_condition[] = "(trim(a.POEM_NO) = '$POEM_NO' or a.POEM_NO is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $POEM_NO;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "POEMNO" :	
					$POEM_NO = -1;
				break;
			} // end switch case
		} // end for
	} // function

	//สำหรับลูกจ้างประจำ
	if($DPISDB=="odbc"){
		$cmd = " select			distinct 
											b.ORG_ID_REF, $select_list, a.POEM_ID, b.ORG_NAME, c.PN_NAME, c.PG_CODE,  f.PV_NAME, g.OT_NAME
						 from			(
												(
													(
															PER_POS_EMP a
															inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) inner join PER_POS_NAME c on (a.PN_CODE=c.PN_CODE)
												) left join PER_PROVINCE f on (b.PV_CODE=f.PV_CODE)
											) left join PER_ORG_TYPE g on (b.OT_CODE=g.OT_CODE)
						 $search_condition
						 order by		b.ORG_ID_REF, $order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);	
		$cmd = " select			distinct 
											b.ORG_ID_REF, $select_list, a.POEM_ID, b.ORG_NAME, c.PN_NAME, c.PG_CODE,  f.PV_NAME, g.OT_NAME
						 from			 PER_POS_EMP a, PER_ORG b, PER_POS_NAME c,PER_PROVINCE f, PER_ORG_TYPE g
						 where		a.ORG_ID=b.ORG_ID and a.PN_CODE=c.PN_CODE(+)
						 					and b.PV_CODE=f.PV_CODE(+) and b.OT_CODE=g.OT_CODE(+)
											$search_condition
						 order by		b.ORG_ID_REF, $order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct 
											b.ORG_ID_REF, $select_list, a.POEM_ID, b.ORG_NAME, c.PN_NAME, c.PG_CODE,  f.PV_NAME, g.OT_NAME
						 from			(
												(
														(
																 PER_POS_EMP a
																inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
															) inner join PER_POS_NAME c on (a.PN_CODE=c.PN_CODE)
												) left join PER_PROVINCE f on (b.PV_CODE=f.PV_CODE)
											) left join PER_ORG_TYPE g on (b.OT_CODE=g.OT_CODE)
						 $search_condition
						 order by		b.ORG_ID_REF, $order_by ";
	} // end if
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	initialize_parameter(0);
	$ORG_ID_REF = -1;
	while($data = $db_dpis->get_array()){
		if($ORG_ID_REF != $data[ORG_ID_REF]){
			$ORG_ID_REF = $data[ORG_ID_REF];
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_REF ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_REF = $data2[ORG_NAME];

			$arr_content[$data_count][type] = "ORG_REF";
			$arr_content[$data_count][org_id_ref] = $ORG_ID_REF;
			$arr_content[$data_count][org_name_ref] = $ORG_NAME_REF;
			
			$data_count++;
		} // end if
		
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "POEMNO" :
					if($POEM_NO != trim($data[POEM_NO_NAME]).trim($data[POEM_NO])){
						$POEM_NO = trim($data[POEM_NO_NAME]).trim($data[POEM_NO]);

						$addition_condition = generate_condition($rpt_order_index);

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						if($rpt_order_index == (count($arr_rpt_order) - 1)){	
							$data_row++;
							$POEM_ID = $data[POEM_ID];

							$ORG_NAME = trim($data[ORG_NAME]);
							$ORG_NAME2 = "";	$LEVEL_PNNAME="";

							$arr_pn_name = explode("ชั้น",trim($data[PN_NAME]));
							$PN_NAME = trim($arr_pn_name[0]);
							if(trim($arr_pn_name[1])){ $LEVEL_PNNAME = trim($arr_pn_name[1]); }

							$OT_NAME = trim($data[OT_NAME]);
							$PV_NAME = trim($data[PV_NAME]);
							$PG_CODE = trim($data[PG_CODE]);
						
							$cmd = " select PG_NAME from PER_POS_GROUP where trim(PG_CODE)='".$PG_CODE."' ";
							$db_dpis2->send_cmd($cmd);
							$data_dpis2 = $db_dpis2->get_array();
							$PG_NAME = $data_dpis2[PG_NAME];

							//หาข้อมูลส่วนตัว
							$cmd = " select 	PER_ID, PER_GENDER, PER_BIRTHDATE, PER_RETIREDATE,PER_STARTDATE, LEVEL_NO, PER_SALARY,PER_MGTSALARY, PER_NAME , PER_SURNAME,PER_CARDNO
											 from		PER_PERSONAL
											 where	POEM_ID=$POEM_ID and PER_TYPE=2 and PER_STATUS=1 ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PER_ID = $data2[PER_ID];
							$PER_GENDER = $data2[PER_GENDER];
							$PER_NAME = $data2[PER_NAME];
							$PER_SURNAME = $data2[PER_SURNAME];
							$PER_CARDNO = $data2[PER_CARDNO];
							$PER_BIRTHDATE = substr(trim($data2[PER_BIRTHDATE]), 0, 10);
							$BIRTHDATE_D = $BIRTHDATE_M = $BIRTHDATE_Y = $RETIREDATE_Y = "";
							if($PER_BIRTHDATE){
								$arr_temp = explode("-", $PER_BIRTHDATE);
								$BIRTHDATE_D = trim($arr_temp[2]);
								$BIRTHDATE_M = trim($arr_temp[1]);
								$BIRTHDATE_Y = substr(($arr_temp[0] + 543), -2);
							} // end if
							$PER_STARTDATE = substr(trim($data2[PER_STARTDATE]), 0, 10);
							$STARTDATE_D = $STARTDATE_M = $STARTDATE_Y = "";
							if($PER_STARTDATE){
								$arr_temp = explode("-", $PER_STARTDATE);
								$STARTDATE_D = trim($arr_temp[2]);
								$STARTDATE_M = trim($arr_temp[1]);
								$STARTDATE_Y = substr(($arr_temp[0] + 543), -2);
							} // end if
							$PER_RETIREDATE = substr(trim($data2[PER_RETIREDATE]), 0, 10);
							if($PER_RETIREDATE){
								$arr_temp = explode("-", $PER_RETIREDATE);
								$RETIREDATE_Y = ($arr_temp[0] + 543);
							}
							$LEVEL_NO = trim($data2[LEVEL_NO]);
							$PER_SALARY = $data2[PER_SALARY];
							$PER_MGTSALARY = $data2[PER_MGTSALARY];
							
							$cmd="select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
							$db_dpis2->send_cmd($cmd);
							$data_level = $db_dpis2->get_array();
							$LEVEL_NAME=$data_level[LEVEL_NAME];
							
							$EDU_TYPE="";		$EL_NAME = "";			$EM_NAME = "";		$EN_SHORTNAME="";		$EN_NAME="";
							//หาข้อมูลการศึกษาเลือก วุฒิสูงสุด ถ้าไม่มีเอาวุฒิในตน.ปัจจุบันมา
							if($PER_ID){
								if($DPISDB=="odbc"){
									$cmd = " select 	a.EDU_TYPE,c.EL_NAME,d.EM_NAME,b.EN_SHORTNAME,b.EN_NAME
											 from ( 	
											 			(
															PER_EDUCATE a
															left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
														) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
													) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE)
											 where	a.PER_ID=$PER_ID and (a.EDU_TYPE like '%4%' or a.EDU_TYPE like '%2%') ";									
								}elseif($DPISDB=="oci8"){
									$cmd = " select 	a.EDU_TYPE,c.EL_NAME ,d.EM_NAME,b.EN_SHORTNAME,b.EN_NAME
													 from 		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c,PER_EDUCMAJOR d
													 where	a.PER_ID=$PER_ID and  (a.EDU_TYPE like '%4%' or a.EDU_TYPE like '%2%')  and 
													 a.EN_CODE=b.EN_CODE(+) and b.EL_CODE=c.EL_CODE(+) and a.EM_CODE=d.EM_CODE(+) ";						
								}elseif($DPISDB=="mysql"){
									$cmd = " select 	a.EDU_TYPE,c.EL_NAME ,d.EM_NAME,b.EN_SHORTNAME,b.EN_NAME
											 from ( 	
											 			(
																PER_EDUCATE a
																left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
															) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
														) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE)
											 where	a.PER_ID=$PER_ID and  (a.EDU_TYPE like '%4%' or a.EDU_TYPE like '%2%') ";	
								} // end if
								$db_dpis2->send_cmd($cmd);
								$data2 = $db_dpis2->get_array();
								
								$EDU_TYPE = trim($data2[EDU_TYPE]);
								$EL_NAME = trim($data2[EL_NAME]);
								$EM_NAME = trim($data2[EM_NAME]);		//สาขา
								$EN_SHORTNAME =  trim($data2[EN_SHORTNAME]);
								$EN_NAME =  trim($data2[EN_NAME]);		//ชื่อวุฒิ
							} // end if
							if(trim($EL_NAME)){
								$ED_NAME = $EL_NAME;
							}
							/* if(trim($EM_NAME)){
								if(trim($ED_NAME)){
									$ED_NAME .= " / ";
								}
								$ED_NAME .= $EM_NAME;
							} */
							if(trim($EN_NAME)){		//$EN_SHORTNAME
								if(trim($ED_NAME)){
									$ED_NAME .= " / ";
								}
								$ED_NAME .= $EN_NAME;		//$EN_SHORTNAME;
							}

							$arr_content[$data_count][type] = "CONTENT";
							$arr_content[$data_count][order] = $data_row;
							$arr_content[$data_count][pos_no] = $POEM_NO;
							$arr_content[$data_count][org_name] = $ORG_NAME;
							$arr_content[$data_count][org_name2] = $ORG_NAME2;
							$arr_content[$data_count][pg_name] = $PG_NAME;
							$arr_content[$data_count][pn_name] = "$PN_NAME";
							$arr_content[$data_count][level_pnname] = $LEVEL_PNNAME?$LEVEL_PNNAME:"-";
							$arr_content[$data_count][ot_name] = $OT_NAME;
							$arr_content[$data_count][pv_name] = $PV_NAME;
							$arr_content[$data_count][per_name] = $PER_NAME;
							$arr_content[$data_count][per_surname] = $PER_SURNAME;
							$arr_content[$data_count][per_cardno] = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
							$arr_content[$data_count][per_gender] = ($PER_GENDER==1?"ชาย":($PER_GENDER==2?"หญิง":"ว่าง"));
							$arr_content[$data_count][birthdate_d] = $BIRTHDATE_D;
							$arr_content[$data_count][birthdate_m] = $BIRTHDATE_M;
							$arr_content[$data_count][birthdate_y] = $BIRTHDATE_Y;
							$arr_content[$data_count][startdate_d] = $STARTDATE_D;
							$arr_content[$data_count][startdate_m] = $STARTDATE_M;
							$arr_content[$data_count][startdate_y] = $STARTDATE_Y;
							$arr_content[$data_count][retiredate_y] = $RETIREDATE_Y;
							$arr_content[$data_count][level_no] = $LEVEL_NAME;
							$arr_content[$data_count][per_salary] = ($PER_SALARY?number_format($PER_SALARY):"");
							$arr_content[$data_count][per_mgtsalary] = ($PER_MGTSALARY?number_format($PER_MGTSALARY):"");
							$arr_content[$data_count][ed_name] = $ED_NAME;

							$data_count++;														
						} // end if
					} // end if
				break;
			} // end switch case
		} // end for
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
//		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$POEM_NO = $arr_content[$data_count][pos_no];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$ORG_NAME2 = $arr_content[$data_count][org_name2];
			$PG_NAME = $arr_content[$data_count][pg_name];
			$PN_NAME = $arr_content[$data_count][pn_name];
			$LEVEL_PNNAME = $arr_content[$data_count][level_pnname];
			$OT_NAME = $arr_content[$data_count][ot_name];
			$PV_NAME = $arr_content[$data_count][pv_name];
			$PER_NAME = $arr_content[$data_count][per_name];
			$PER_SURNAME = $arr_content[$data_count][per_surname];
			$PER_CARDNO = $arr_content[$data_count][per_cardno];
			$PER_GENDER = $arr_content[$data_count][per_gender];
			$BIRTHDATE_D = $arr_content[$data_count][birthdate_d];
			$BIRTHDATE_M = $arr_content[$data_count][birthdate_m];
			$BIRTHDATE_Y = $arr_content[$data_count][birthdate_y];
			$STARTDATE_D = $arr_content[$data_count][startdate_d];
			$STARTDATE_M = $arr_content[$data_count][startdate_m];
			$STARTDATE_Y = $arr_content[$data_count][startdate_y];
			$RETIREDATE_Y = $arr_content[$data_count][retiredate_y];
			$LEVEL_NO = $arr_content[$data_count][level_no];
			$PER_SALARY = $arr_content[$data_count][per_salary];
			$PER_MGTSALARY = $arr_content[$data_count][per_mgtsalary];
			$ED_NAME = $arr_content[$data_count][ed_name];
			
			if($REPORT_ORDER == "ORG_REF"){
				$ORG_ID_REF = $arr_content[$data_count][org_id_ref];
				$ORG_NAME_REF = $arr_content[$data_count][org_name_ref];

				//$pdf->report_title = "$ORG_NAME_REF||$report_title";
				$pdf->report_title = "$report_title";
				$pdf->AddPage();
				print_header();
			}else{
				$border = "";
				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
	
				$pdf->Cell($heading_width[0], 7,(($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER), $border, 0, 'C', 0);
				$pdf->MultiCell($heading_width[1], 7, "$ORG_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[2], 7, "$ORG_NAME2", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[3], 7, "$PER_NAME   $PER_SURNAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[4], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($PER_CARDNO):$PER_CARDNO), $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[5], 7, "$PER_GENDER", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[6], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($BIRTHDATE_D):$BIRTHDATE_D), $border, 0, 'C', 0);
				$pdf->Cell($heading_width[7], 7,(($NUMBER_DISPLAY==2)?convert2thaidigit($BIRTHDATE_M):$BIRTHDATE_M), $border, 0, 'C', 0);
				$pdf->Cell($heading_width[8], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($BIRTHDATE_Y):$BIRTHDATE_Y), $border, 0, 'C', 0);
				$pdf->Cell($heading_width[9], 7,  (($NUMBER_DISPLAY==2)?convert2thaidigit($STARTDATE_D):$STARTDATE_D),  $border, 0, 'C', 0);
				$pdf->Cell($heading_width[10], 7,(($NUMBER_DISPLAY==2)?convert2thaidigit($STARTDATE_M):$STARTDATE_M),  $border, 0, 'C', 0);
				$pdf->Cell($heading_width[11], 7,  (($NUMBER_DISPLAY==2)?convert2thaidigit($STARTDATE_Y):$STARTDATE_Y),$border, 0, 'C', 0);
				$pdf->MultiCell($heading_width[12], 7, "$PG_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[13], 7, "$PN_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12]+ $heading_width[13];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[14], 7, "$LEVEL_PNNAME", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[15], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($POEM_NO):$POEM_NO), $border, 0, 'C', 0);
				$pdf->MultiCell($heading_width[16], 7, "$OT_NAME", $border, "C");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13] + $heading_width[14] + $heading_width[15] + $heading_width[16] ;
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[17], 7, $PV_NAME?$PV_NAME:"-", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13] + $heading_width[14] + $heading_width[15] + $heading_width[16] + $heading_width[17];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[18], 7, $ED_NAME?$ED_NAME:"-", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13] + $heading_width[14] + $heading_width[15] + $heading_width[16] + $heading_width[17] + $heading_width[18];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[19], 7,(($NUMBER_DISPLAY==2)?convert2thaidigit($PER_SALARY):$PER_SALARY),  $border, 0, 'L', 0);
				$pdf->Cell($heading_width[20], 7,(($NUMBER_DISPLAY==2)?convert2thaidigit($RETIREDATE_Y):$RETIREDATE_Y), $border, 0, 'C', 0);
				$pdf->Cell($heading_width[21], 7, "", $border, 0, 'L', 0);
	
				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
				for($i=0; $i<=21; $i++){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================
	
				if(($pdf->h - $max_y - 10) < 22){ 
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
			} // end if
		} // end for	
		$pdf->Cell(285,7,"ผู้ให้ข้อมูล.................................................................โทร..................................................",0,1,'L');
	}else{
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();		
?>