<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include ("../php_scripts/function_share.php");

//	echo "order_by=$order_by<br>";

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");
	
	ini_set("max_execution_time", $max_execution_time);
	
	$company_name = "";
	$report_title = trim($report_title);
	$report_code = "";
	
//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
    $unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$orientation='P';
	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	$pdf->SetAutoPageBreak(true,10);
	
	$heading_width[0] = "20";
	$heading_width[1] = "90";
	$heading_width[2] = "70";
	$heading_width[3] = "20";
//	$heading_width[4] = "20";
//	$heading_width[5] = "20";
		
	$heading_text[0] = "$CODE_TITLE";
	$heading_text[1] = "ชื่อกลุ่ม";
	$heading_text[2] = "ระดับกลุ่ม";
//	$heading_text[3] = "รายชื่อ";
//	$heading_text[4] = "สิทธิ์เมนู";
	$heading_text[3] = "ฐานข้อมูล";

	$heading_align = array('C','C','C');
		
	function print_header(){
		global $pdf, $heading_width;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"รหัส",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ชื่อกลุ่ม",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ระดับกลุ่ม",'LTBR',0,'C',1);
//		$pdf->Cell($heading_width[3] ,7,"รายชื่อ",'LTBR',0,'C',1);
//		$pdf->Cell($heading_width[4] ,7,"สิทธิ์เมนู",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ฐานข้อมูล",'LTBR',1,'C',1);
	} // function		

  	if(!$order_by)	$order_by=1;
	if($order_by==1){	//(ค่าเริ่มต้น) ลำดับที่
		$order_str = "group_seq_no $SortType[$order_by], code $SortType[$order_by]";
  	}elseif($order_by==2) {	//รหัส
		$order_str = "code ".$SortType[$order_by];
  	}elseif($order_by==3) {	//ชื่อ
		$order_str = "name_th ".$SortType[$order_by];
  	}elseif($order_by==4) {	//โครงสร้าง
		$order_str = "group_org_structure ".$SortType[$order_by];
	}

	$cmd = "select id,code,name_th,name_en,access_list, group_org_structure, group_active, group_seq_no from user_group order by $order_str";
	$count_data = $db->send_cmd($cmd);
//	echo "$cmd ($count_data)<br>";
	$user_group = (array) null;
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();
		$data_count = 0;
		$i = 0;
		while($data = $db->get_array()) {
			$data = array_change_key_case($data, CASE_LOWER);
			$user_group[$i]["id"] = $data[id];
			$user_group[$i]["code"] = $data[code];
//			echo "$i-".$user_group[$i]["code"]."<br>";
			$user_group[$i]["name_th"] = $data[name_th];
			$user_group[$i]["name_en"] = $data[name_en];
			$user_group[$i]["access_list"] = $data[access_list];
			$user_group[$i]["group_org_structure"] = $data[group_org_structure];
			$user_group[$i]["group_active"] = $data[group_active];
			$user_group[$i]["group_seq_no"] = $data[group_seq_no];
			$i++;
		} // end while

		for($i = 0; $i < $count_data; $i++){
			$data_count++;

			$user_group_id = $user_group[$i]["id"];
			$user_group_code = $user_group[$i]["code"];
			$user_group_name_th = $user_group[$i]["name_th"];
			$user_group_name_en = $user_group[$i]["name_en"];
			$user_group_name = ($user_group_name_th ? $user_group_name_th.($user_group_name_en && $user_group_name_en != $user_group_name_th ? "($user_group_name_en)" : "") : $user_group_name_en);
			$user_group_access_list = $user_group[$i]["access_list"];
			$user_group_group_org_structure = $user_group[$i]["group_org_structure"];
			$user_group_group_active = $user_group[$i]["group_active"];
			$user_group_group_seq_no = $user_group[$i]["group_seq_no"];
			
			$str_user_section = substr( $user_group_access_list , 1 , -1 );
//			echo $str_user_section;
			$arr_user_section = explode( "," , $str_user_section );

			$id = $user_group_id;
			$this_dpisdb_user = "";
			$cmd = " select dpisdb, dpisdb_name, dpisdb_user from user_group where id=$id ";
			$db->send_cmd($cmd);
			if ($data=$db->get_array()) {
				$data = array_change_key_case($data, CASE_LOWER);
				if ($data[dpisdb]==1) $this_dpisdb_user = $data[dpisdb_name];
				elseif ($data[dpisdb]==2) $this_dpisdb_user = $data[dpisdb_user];
			}
			if (!$this_dpisdb_user) $this_dpisdb_user = "-";

		 	if($user_group_group_org_structure==0){ 
				$this_org_structure = "โครงสร้างตามกฏหมาย";
			 }elseif($user_group_group_org_structure==1){ 
			 	$this_org_structure = "โครงสร้างตามมอบหมายงาน";
			 }elseif($user_group_group_org_structure==2){ 
				$this_org_structure = "ตามกฏหมายและมอบหมายงาน";
			}

			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->Cell($heading_width[0], 7, $user_group_code, $border, 0, 'C', 0);
			$pdf->MultiCell($heading_width[1], 7, $user_group_name, $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[2], 7, $this_org_structure, $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[3], 7, $this_dpisdb_user, $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
			for($j=0; $j<=3; $j++){
				$line_start_y = $start_y;		$line_start_x += $heading_width[$j];
				$line_end_y = $max_y;		$line_end_x += $heading_width[$j];
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for $j
			//====================================================

			if(($pdf->h - $max_y - 10) < 15){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < $count_data){
					$pdf->AddPage();
					print_header();
					$max_y = $pdf->y;
				} // end if
			}else{
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
			} // end if
			$pdf->x = $start_x;			$pdf->y = $max_y;
		} // end for $i
	}else{
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();
?>