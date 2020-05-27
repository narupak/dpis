<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "ตารางแสดงผลการประเมินสมรรถนะของข้าราชการ";
	$report_code = "R0102";
	$orientation='L';

	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$heading_width[0] = "80";
	$heading_width[1] = "20";
	$heading_width[2] = "20";
	$heading_width[3] = "20";
	$heading_width[4] = "20";
	$heading_width[5] = "20";
	$heading_width[6] = "20";

	function print_header(){
		global $pdf, $heading_width;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));

		$pdf->Cell($heading_width[0] ,7,"สมรรถนะ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ระดับเป้าหมาย",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ตนเอง",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ผู้บังคับบัญชา",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"เพื่อนร่วมงาน",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ผู้ใต้บังคับบัญชา",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"สรุปผลการประเมิน",'LTBR',1,'C',1);
	} // function		
	
	$KF_ID=$_GET['KF_ID'];
	$cmd = " select 	KF_CYCLE, KF_START_DATE, KF_END_DATE, PER_ID from PER_KPI_FORM where KF_ID=$KF_ID ";
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$KF_CYCLE = trim($data[KF_CYCLE]);
	$chkKFStartDate = $data[KF_START_DATE];
	$chkKFEndDate = $data[KF_END_DATE];
	if($KF_CYCLE==1){	//ตรวจสอบรอบการประเมิน
		$KF_START_DATE_1 = show_date_format($data[KF_START_DATE], 1);
		$KF_END_DATE_1 = show_date_format($data[KF_END_DATE], 1);
		$KF_YEAR = substr($KF_END_DATE_1, 0, 6);
	}else if($KF_CYCLE==2){
		$KF_START_DATE_2 = show_date_format($data[KF_START_DATE], 1);
		$KF_END_DATE_2 = show_date_format($data[KF_END_DATE], 1);
		$KF_YEAR = substr($KF_END_DATE_2, 0, 6);
	}

	$PER_ID = $data[PER_ID];
	$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, LEVEL_NO, PER_SALARY, PER_TYPE, POS_ID, POEM_ID, POEMS_ID
						 from PER_PERSONAL where	PER_ID=$PER_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PN_CODE = trim($data[PN_CODE]);
	$PER_NAME = trim($data[PER_NAME]);
	$PER_SURNAME = trim($data[PER_SURNAME]);
	$LEVEL_NO = trim($data[LEVEL_NO]);
	$PER_SALARY = trim($data[PER_SALARY]);
	$PER_TYPE = trim($data[PER_TYPE]);
	$POS_ID = trim($data[POS_ID]);
	$POEM_ID = trim($data[POEM_ID]);
	$POEMS_ID = trim($data[POEMS_ID]);
		
	$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PN_NAME = trim($data[PN_NAME]);
		
	$PER_NAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;
		
	$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO)='$LEVEL_NO' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$LEVEL_NAME = trim($data[LEVEL_NAME]);

	if($PER_TYPE==1){
		$cmd = " select 	a.POS_NO, b.PL_NAME, c.ORG_NAME, a.PT_CODE 
						 from 		PER_POSITION a, PER_LINE b, PER_ORG c
						 where	a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POS_NO = $data[POS_NO];
		$PL_NAME = trim($data[PL_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
		$PT_CODE = trim($data[PT_CODE]);
		$cmd = "	select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE'";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PT_NAME = trim($data2[PT_NAME]);
	}elseif($PER_TYPE==2){
		$cmd = " select 	b.PN_NAME, c.ORG_NAME 
						 from 		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
						 where	a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[PN_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
	}elseif($PER_TYPE==3){
		$cmd = " select 	b.EP_NAME, c.ORG_NAME 
						 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
						 where	a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[EP_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
	} // end if

	$report_title = "แสดงผลการประเมินสมรรถนะของ$PERSON_TYPE[$PER_TYPE] $DEPARTMENT_NAME";
	$company_name = "ประจำรอบการประเมินครั้งที่ $KF_CYCLE พ.ศ. $KF_YEAR  $ORG_TITLE $ORG_NAME";
	$company_name1 = "ชื่อผู้รับการประเมิน $PER_NAME  ชื่อตำแหน่งงาน $PL_NAME";
	$report_code = "R0102";

	$data_count = 0;
	$cmd = " SELECT * FROM PER_COMPETENCY_FORM WHERE CF_PER_ID = $PER_ID AND CF_STATUS=1";
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	while($data = $db_dpis->get_array()){
		$U_KF_ID = $data[KF_ID];
		$cmd1 = " SELECT * FROM PER_KPI_FORM WHERE KF_ID=$U_KF_ID ";
		$db_dpis2->send_cmd($cmd1);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$othStartDate=$data2[KF_START_DATE];;
		$othEndDate=$data2[KF_END_DATE];;
		if ($othStartDate == $chkKFStartDate && $othEndDate == $chkKFEndDate) { // if startDate and endDate ตรงกัน
			$CF_TYPE = $data[CF_TYPE];
			$cmd1 = " SELECT *  FROM PER_KPI_COMPETENCE  WHERE KF_ID=$U_KF_ID";
			$db_dpis2->send_cmd($cmd1);
//			$db_dpis2->show_error();
			while($data2 = $db_dpis2->get_array()) {
				$SUB_CP_CODE=$data2[CP_CODE];
				$PC_TARGET_LEVEL = $data2[PC_TARGET_LEVEL];
				$KC_EVALUATE = $data2[KC_EVALUATE];
				$cmd2 = " select CP_NAME, CP_MODEL from PER_COMPETENCE where CP_CODE='$SUB_CP_CODE' ";
				$db_dpis3->send_cmd($cmd2);
//				$db_dpis3->show_error(); 
				$data3 = $db_dpis3->get_array();
				$CP_NAME = $data3[CP_NAME];
				$CP_MODEL = $data3[CP_MODEL];
				$ST_CP_MODEL="";
				if($CP_MODEL==1) $ST_CP_MODEL = "สมรรถนะหลัก";
				elseif($CP_MODEL==2) $ST_CP_MODEL = "สมรรถนะผู้บริหาร";
				elseif($CP_MODEL==3) $ST_CP_MODEL = "สมรรถนะประจำสายงาน";

				$arr_content[$data_count][name] = $CP_NAME;
				$arr_content[$data_count][type] = $ST_CP_MODEL;
				$arr_content[$data_count][$CF_TYPE] = $KC_EVALUATE;
				$arr_content[$data_count][0] = $PC_TARGET_LEVEL;
				$arr_content[$data_count][5] = $arr_content[$data_count][5]+$KC_EVALUATE;
				$data_count++;
			} // end while 
		} // end if startDate and endDate ตรงกัน
	} // end while

	if($export_type=="report"){
		if($count_data){
			$pdf->AutoPageBreak = false;
			print_header();
		
			for($data_count=0; $data_count<count($arr_content); $data_count++){
				$NAME = $arr_content[$data_count][name];							
				$TYPE = $arr_content[$data_count][type];					
			
				$COUNT_0 = $arr_content[$data_count][0];
				$COUNT_1 = $arr_content[$data_count][1];
				$COUNT_2 = $arr_content[$data_count][2];
				$COUNT_3 = $arr_content[$data_count][3];
				$COUNT_4 = $arr_content[$data_count][4];
				$COUNT_5 = $arr_content[$data_count][5];
				$COUNT_TOTAL = ($COUNT_1 + $COUNT_2 + $COUNT_3 + $COUNT_4);

				$border = "";
				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
	
				$pdf->MultiCell($heading_width[0], 7, "$NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[1], 7, ($COUNT_0 && $COUNT_0 > 0 ?number_format($COUNT_0, 2):"-"), $border, 0, 'R', 0);
				$pdf->Cell($heading_width[2], 7, ($COUNT_1 && $COUNT_1 > 0 ?number_format($COUNT_1, 2):"-"), $border, 0, 'R', 0);
				$pdf->Cell($heading_width[3], 7, ($COUNT_2 && $COUNT_2 > 0 ?number_format($COUNT_2, 2):"-"), $border, 0, 'R', 0);
				$pdf->Cell($heading_width[4], 7, ($COUNT_3 && $COUNT_3 > 0 ?number_format($COUNT_3, 2):"-"), $border, 0, 'R', 0);
				$pdf->Cell($heading_width[5], 7, ($COUNT_4 && $COUNT_4 > 0 ?number_format($COUNT_4, 2):"-"), $border, 0, 'R', 0);
				$pdf->Cell($heading_width[6], 7, ($COUNT_5 && $COUNT_5 > 0 ?number_format($COUNT_5, 2):"-"), $border, 0, 'R', 0);
	
				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
				for($i=0; $i<=6; $i++){
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
					if($data_count == (count($arr_content) - 1) || $arr_content[($data_count + 1)][type]=="ORG_REF") $pdf->Line($start_x, $max_y, $pdf->x, $max_y);		
				} // end if
				$pdf->x = $start_x;			$pdf->y = $max_y;
			} // end for
				
		}else{
			///$pdf->AddPage();
			$pdf->SetFont($font,'b','',16);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
		} // end if

		$pdf->close();
		$pdf->Output();	
	}else if($export_type=="graph"){//if($export_type=="report"){
		$arr_content_key = array_keys($arr_content[0]);//print_r($arr_content_key);
		$arr_categories = array();
		for($i=1;$i<count($arr_content);$i++){
//			$arr_categories[$i] = $arr_content[$i][name];
			$arr_categories[$i] = $arr_content[$i][name];
			$arr_series_caption_data[0][] = $arr_content[$i][0];
			$arr_series_caption_data[1][] = $arr_content[$i][1];
			$arr_series_caption_data[2][] = $arr_content[$i][2];
			$arr_series_caption_data[3][] = $arr_content[$i][3];
			$arr_series_caption_data[4][] = $arr_content[$i][4];
			$arr_series_caption_data[5][] = $arr_content[$i][5];
		}
		$arr_series_list[0] = implode(";", $arr_series_caption_data[0])."";
		$arr_series_list[1] = implode(";", $arr_series_caption_data[1])."";
		$arr_series_list[2] = implode(";", $arr_series_caption_data[2])."";
		$arr_series_list[3] = implode(";", $arr_series_caption_data[3])."";
		$arr_series_list[4] = implode(";", $arr_series_caption_data[4])."";
		$arr_series_list[5] = implode(";", $arr_series_caption_data[5])."";

		$chart_title = $report_title;
		$chart_subtitle = $company_name + " " + $company_name1;
		if(!$setWidth){ $setWidth = "$GRAPH_WIDE";}else{ $setWidth = "800";}
		if(!$setHeight){ $setHeight = "$GRAPH_HIGH";}else{$setHeight = "600";}
		$selectedFormat = "SWF";
		$series_caption_list = "ระดับเป้าหมาย;ตนเอง;ผู้บังคับบัญชา;เพื่อนร่วมงาน;ผู้ใต้บังคับบัญชา;สรุปผลการประเมิน";
		$categories_list = implode(";", $arr_categories)."";
		if(strtolower($graph_type)=="pie"){
			$series_list = $GRAND_TOTAL_1[$DEPARTMENT_ID].";".$GRAND_TOTAL_2[$DEPARTMENT_ID].";".$GRAND_TOTAL_3[$DEPARTMENT_ID];
		}else{
			$series_list = implode("|", $arr_series_list);
		}
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