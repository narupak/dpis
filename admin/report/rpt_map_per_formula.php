<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends.php");

//	$db_dpis_n = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	// ==== use for testing phase =====
	$db_dpis_n = new connect_db($db_host, $db_name, $db_user, $db_pwd);
	$db2 = new connect_db($db_host, $db_name, $db_user, $db_pwd);
	// ==========================
	ini_set("max_execution_time", $max_execution_time);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "สรุปการเทียบเคียงระดับตำแหน่งข้าราชการ";
	$report_code = "";
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
	
	$heading_width[0] = "10";
	$heading_width[1] = "60";
	$heading_width[2] = "67";
	$heading_width[3] = "31";
	$heading_width[4] = "32";
		
	function print_header(){
		global $pdf, $heading_width;
		
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ชื่อ - นามสกุล",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ชื่อตำแหน่งในสายงาน ระดับ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ระดับตำแหน่ง (ใหม่)",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ประเภทตำแหน่ง (ใหม่)",'LTBR',1,'C',1);
	} // function		
	
	asort($orderby_order);
//	echo "<pre>"; print_r($orderby_order); echo "</pre>";
	$orderby = "";
	foreach($orderby_order as $key => $value){
		if(!trim($value)) continue;
		switch($key){
			case "FULLNAME" :
				if($orderby_type[FULLNAME]=="asc") $orderby .= ($orderby?", ":"")."PER_NAME, PER_SURNAME";
				elseif($orderby_type[FULLNAME]=="desc") $orderby .= ($orderby?", ":"")."PER_NAME desc, PER_SURNAME desc";
				break;
			case "LEVEL_NO" :
				if($orderby_type[LEVEL_NO]=="asc") $orderby .= ($orderby?", ":"")."lpad(LEVEL_NO, 2, '0')";
				elseif($orderby_type[LEVEL_NO]=="desc") $orderby .= ($orderby?", ":"")."lpad(LEVEL_NO, 2, '0') desc";
				break;
			case "PT_CODE" :
				if($orderby_type[PT_CODE]=="asc") $orderby .= ($orderby?", ":"")."PT_CODE";
				elseif($orderby_type[PT_CODE]=="desc") $orderby .= ($orderby?", ":"")."PT_CODE desc";
				break;
			case "PT_CODE_N" :
				if($orderby_type[PT_CODE_N]=="asc") $orderby .= ($orderby?", ":"")."PT_CODE_N";
				elseif($orderby_type[PT_CODE_N]=="desc") $orderby .= ($orderby?", ":"")."PT_CODE_N desc";
				break;
		} // end switch case
	} // end if
	if(!trim($orderby)) $orderby = "PER_NAME, PER_SURNAME";
	if($groupby_org) $orderby = "ORG_ID, ". $orderby;
	
	$cmd = " select 		ORG_ID, PN_CODE, PER_NAME, PER_SURNAME, LEVEL_NO, PT_CODE, PT_CODE_N, PL_CODE
					 from			PER_FORMULA
					 order	by	$orderby
				  ";
	$count_data = $db->send_cmd($cmd);
//	$db->show_error();

	if($count_data){
		$pdf->AutoPageBreak = false;
		$org_count = $data_count = $data_row = 0;
		if(!$groupby_org) print_header();

		while($data = $db->get_array()){
			$data_count++;
			
			if($groupby_org && ($ORG_ID != $data[ORG_ID])){
				if(($pdf->h - $pdf->y - 10) < 25){ 
					$pdf->AddPage();
					$max_y = $pdf->y;
				} // end if
				
				$org_count++;
				$ORG_ID = $data[ORG_ID];

				$cmd = " select count(PER_ID) as count_person from PER_FORMULA where ORG_ID=$ORG_ID ";
				$db2->send_cmd($cmd);		
//				$db2->show_error();		
				$data2 = $db2->get_array();
				$count_person = $data2[count_person];

				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
				if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis->send_cmd($cmd);
				$data_dpis = $db_dpis->get_array();
				$ORG_NAME = $data_dpis[ORG_NAME];
				
				$pdf->SetFont('angsab','',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				if($org_count > 1) $pdf->Cell(200,5,"",0,1,"L");
				$pdf->Cell(200,7,"$org_count. $ORG_NAME",0,1,"L");

				print_header();
				$data_row = 0;
			} // end if	

			$data_row++;			

			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$PN_NAME = trim($data_dpis[PN_NAME]);
			
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$FULLNAME = "$PN_NAME $PER_NAME $PER_SURNAME";
			
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$PT_CODE = trim($data[PT_CODE]);
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$PT_NAME = trim($data_dpis[PT_NAME]);

			$PT_CODE_N = trim($data[PT_CODE_N]);
			$cmd = " select 	a.PT_NAME_N, b.PT_GROUP_NAME
							 from 		PER_TYPE_N a, PER_GROUP_N b 
							 where 	trim(a.PT_GROUP_N)=trim(b.PT_GROUP_N) and PT_CODE_N='".$PT_CODE_N."' ";
			$db_dpis_n->send_cmd($cmd);
			$data_dpis = $db_dpis_n->get_array();
			$PT_NAME_N = $data_dpis[PT_NAME_N];
			$PT_GROUP_N = $data_dpis[PT_GROUP_NAME];

			$PL_CODE = trim($data[PL_CODE]);
			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$PL_NAME = trim($data_dpis[PL_NAME]) . " ". level_no_format($LEVEL_NO) . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?" $PT_NAME":"");

			if($PL_CODE=="011103" && ($LEVEL_NO==6 || $LEVEL_NO==7)) $PL_NAME = " * ".$PL_NAME;
//			if($PL_CODE=="010903" && $LEVEL_NO==7) $PL_NAME = " ** ".$PL_NAME;
			
			$border = "";
			$pdf->SetFont('angsa','',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->Cell($heading_width[0], 7, "$data_row .", $border, 0, 'R', 0);
			$pdf->MultiCell($heading_width[1], 7, "$FULLNAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[2], 7, "$PL_NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
			$pdf->y = $start_y;
			$pdf->Cell($heading_width[3], 7, "$PT_NAME_N", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[4], 7, "$PT_GROUP_N", $border, 0, 'C', 0);

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
			for($i=0; $i<=4; $i++){
				$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
				$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
			//====================================================

			if(($pdf->h - $max_y - 10) < 15){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if(!$groupby_org && $data_count < $count_data){
					$pdf->AddPage();
					print_header();
					$max_y = $pdf->y;
				}elseif($groupby_org && $data_row < $count_person){
					$pdf->AddPage();
					print_header();
					$max_y = $pdf->y;
				}elseif($groupby_org && $data_count < $count_data){
					$pdf->AddPage();
					$max_y = $pdf->y;
				} // end if
			}else{
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
			} // end if
			$pdf->x = $start_x;			$pdf->y = $max_y;
		} // end while
	}else{
		$pdf->SetFont('angsab','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');		
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>