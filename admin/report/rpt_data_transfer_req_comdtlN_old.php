<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", 1800);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$cmd = " select		COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
									a.COM_TYPE, COM_CONFIRM, b.COM_DESC
					 from		PER_COMMAND a, PER_COMTYPE b
					 where	COM_ID=$COM_ID and a.COM_TYPE=b.COM_TYPE ";
				
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
		
	$COM_NO = trim($data[COM_NO]);
	$COM_NAME = trim($data[COM_NAME]);
	$COM_DATE = $data[COM_DATE];
	$COM_DATE = show_date_format($COM_DATE,3);
	$COM_NOTE = trim($data[COM_NOTE]);
	$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
	$COM_CONFIRM = trim($data[COM_CONFIRM]);
		
	$COM_TYPE = trim($data[COM_TYPE]);
	$COM_DESC = str_replace("คำสั่ง", "", $data[COM_DESC]);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	
	$report_title = "บัญชีรายละเอียดการ$COM_DESC || แนบท้ายคำสั่ง $DEPARTMENT_NAME ที่ $COM_NO ลงวันที่ $COM_DATE";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "P0203";
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
	$pdf->SetFont($font,'',14);
	$pdf->SetAutoPageBreak(true,10);
	
	if($COM_TYPE=="0101"  || $COM_TYPE=="5011"){	//สอบแข่งขันได้
		$heading_width[0] = "9";
		$heading_width[1] = "40";
		$heading_width[2] = "25";
		$heading_width[3] = "20";
		$heading_width[4] = "9";
		$heading_width[5] = "28";
		$heading_width[6] = "35";
		$heading_width[7] = "30";
		$heading_width[8] = "20";
		$heading_width[9] = "17";
		$heading_width[10] = "17";
		$heading_width[11] = "18";
		$heading_width[12] = "20";

		$heading_text[0] = "ลำดับ|ที่";
		$heading_text[1] = "ชื่อ/นามสกุล (วดป./|เลขประจำตัวประชาชน)";
		$heading_text[2] = "วุฒิ/สาขา/|สถานศึกษา";
		$heading_text[3] = "<**1**>สอบแข่งขันได้|ตำแหน่ง";
		$heading_text[4] = "<**1**>สอบแข่งขันได้|ลำดับ|ที่";
		$heading_text[5] = "<**1**>สอบแข่งขันได้|ประกาศผลการสอบของ";
		$heading_text[6] = "<**2**>ตำแหน่งและส่วนราชการที่บรรจุแต่งตั้ง|ตำแหน่ง/สังกัด";
		$heading_text[7] = "<**2**>ตำแหน่งและส่วนราชการที่บรรจุแต่งตั้ง|ตำแหน่งประเภท";
		$heading_text[8] = "<**2**>ตำแหน่งและส่วนราชการที่บรรจุแต่งตั้ง|ระดับ";
		$heading_text[9] = "<**2**>ตำแหน่งและส่วนราชการที่บรรจุแต่งตั้ง|เลขที่";
		$heading_text[10] = "|เงินเดือน";
		$heading_text[11] = "ตั้งแต่วันที่|";
		$heading_text[12] = "หมายเหตุ|";
		
		$heading_align = array('C','C','C','C','C','C','C','C','C','C','C','C','C');
	}elseif(in_array($COM_TYPE, array("0102", "0103", "0504", "0604", "0704", "0803"))){	//บรรจุผู้ได้รับคัดเลือก
		$heading_width[0] = "10";
		$heading_width[1] = "43";
		$heading_width[2] = "40";
		$heading_width[3] = "44";
		$heading_width[4] = "25";
		$heading_width[5] = "17";
		$heading_width[6] = "17";
		$heading_width[7] = "20";
		$heading_width[8] = "24";
		$heading_width[9] = "50";

		$heading_text[0] = "ลำดับ|ที่";
		$heading_text[1] = "ชื่อ/นามสกุล (วันเดือนปีเกิด|เลขประจำตัวประชาชน)";
		$heading_text[2] = "วุฒิ/สาขา/สถานศึกษา|";
		$heading_text[3] = "<**1**>ตำแหน่งและส่วนราชการที่บรรจุแต่งตั้ง|ตำแหน่ง/สังกัด";
		$heading_text[4] = "<**1**>ตำแหน่งและส่วนราชการที่บรรจุแต่งตั้ง|ตำแหน่งประเภท";
		$heading_text[5] = "<**1**>ตำแหน่งและส่วนราชการที่บรรจุแต่งตั้ง|ระดับ";
		$heading_text[6] = "<**1**>ตำแหน่งและส่วนราชการที่บรรจุแต่งตั้ง|เลขที่";
		$heading_text[7] = "|เงินเดือน";
		$heading_text[8] = "ตั้งแต่วันที่|";
		$heading_text[9] = "หมายเหตุ|";
		
		$heading_align = array('C','C','C','C','C','C','C','C','C','C');
	}elseif(in_array($COM_TYPE, array("แต่งตั้งรักษาราชการแทน","แต่งตั้งให้รักษาการในตำแหน่ง"))){
		if($COM_TYPE == "แต่งตั้งรักษาราชการแทน"){
			$heading_name4="รักษาราชการแทน";
		}elseif($COM_TYPE == "แต่งตั้งให้รักษาการในตำแหน่ง"){
			$heading_name4="รักษาการในตำแหน่ง";
		}
		$heading_width[0] = "10";
		$heading_width[1] = "45";
		$heading_width[2] = "40";
		$heading_width[3] = "25";
		$heading_width[4] = "25";
		$heading_width[5] = "40";
		$heading_width[6] = "25";
		$heading_width[7] = "13";
		$heading_width[8] = "25";
		$heading_width[9] = "25";
		$heading_width[10] = "20";
		$heading_width[11] = "60";

		$heading_text[0] = "ลำดับ|ที่";
		$heading_text[1] = "ชื่อ/นามสกุล|";
		$heading_text[2] = "ตำแหน่งและส่วนราชการ|ตำแหน่ง/สังกัด";
		$heading_text[3] = "ตำแหน่งและส่วนราชการ|ตำแหน่งประเภท";
		$heading_text[4] = "ตำแหน่งและส่วนราชการ|ระดับ";
		$heading_text[5] = "$heading_name4ุ|ตำแหน่ง/สังกัด";
		$heading_text[6] = "$heading_name4ุ|ตำแหน่งประเภท";
		$heading_text[7] = "$heading_name4ุ|ระดับ";
		$heading_text[8] = "$heading_name4ุ|เลขที่";
		$heading_text[9] = "ตั้งแต่วันที่|";
		$heading_text[10] = "ถึงวันที่|";
		$heading_text[11] = "หมายเหตุ|";
		
		$heading_align = array('C','C','C','C','C','C','C','C','C','C','C','C');
	}else if($COM_TYPE=="0302"){   //รับโอนข้าราชการพลเรือนสามัญผู้ได้รับวุฒิเพิ่มขึ้น
/*		$heading_width[0] = "9";
		$heading_width[1] = "23";
		$heading_width[2] = "25";
		$heading_width[3] = "22";
		$heading_width[4] = "18";
		$heading_width[5] = "15";
		$heading_width[6] = "14";
		$heading_width[7] = "22";
		$heading_width[8] = "12";
		$heading_width[9] = "17";
		$heading_width[10] = "23";
		$heading_width[11] = "16";
		$heading_width[12] = "15";	
		$heading_width[13] = "12";
		$heading_width[14] = "14";
		$heading_width[15] = "17";	
		$heading_width[16] = "18";		
		$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ชื่อ/นามสกุล/",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"วุฒิที่ได้รับเพิ่มขึ้น/",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6]) ,7,"ตำแหน่งและส่วนราชการเดิม",'LTBR',0,'C',1);
		$pdf->Cell(($heading_width[7] + $heading_width[8] + $heading_width[9]) ,7,"สอบแข่งขันได้",'LTBR',0,'C',1);
		$pdf->Cell(($heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13] + $heading_width[14]) ,7,"ตำแหน่งและส่วนราชการที่รับโอน",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[15] ,7,"ตั้งแต่วันที่",'LTR',0,'C',1);
		$pdf->Cell($heading_width[16] ,7,"หมายเหตุ",'LTR',1,'C',1);

		//แถวที่ 2
		$pdf->Cell($heading_width[0] ,7,"ที่",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"เลขประจำตัว",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"สถานศึกษา/วันที่",'LBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ตำแหน่ง/สังกัด",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ระดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"เงินเดือน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"ตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"ลำดับที่",'LTR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"ประกาศผล",'LTR',0,'C',1);
		$pdf->Cell($heading_width[10] ,7,"ตำแหน่ง/สังกัด",'LTR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"ตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[12] ,7,"ระดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[13] ,7,"เลขที่",'LTR',0,'C',1);
		$pdf->Cell($heading_width[14] ,7,"เงินเดือน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[15] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[16] ,7,"",'LBR',1,'C',1);
		
		//แถวที่ 3
		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ประชาชน",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"สำเร็จการศึกษา",'LBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ประเภท",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"การสอบของ",'LBR',0,'C',1);
		$pdf->Cell($heading_width[10] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"ประเภท",'LBR',0,'C',1);
		$pdf->Cell($heading_width[12] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[13] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[14] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[15] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[16] ,7,"",'LBR',1,'C',1);
	}else{
				$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LTR',0,'C',1);
				$pdf->Cell($heading_width[1] ,7,"ชื่อ/นามสกุล",'LTR',0,'C',1);
				$pdf->Cell($heading_width[2] ,7,"วุฒิ/สาขา/",'LTR',0,'C',1);
				if(in_array($COM_TYPE, array("0108"))){
					$pdf->Cell(($heading_width[3] + $heading_width[4]) ,7,"ตำแหน่งและส่วนราชการเดิม",'LTBR',0,'C',1);
				}else{
					$pdf->Cell(($heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6]) ,7,"ตำแหน่งและส่วนราชการเดิม",'LTBR',0,'C',1);
				}
		if(in_array($COM_TYPE, array("0105", "0106"))){
			$heading_width[0] = "10";
			$heading_width[1] = "30";
			$heading_width[2] = "35";
			$heading_width[3] = "20";
			$heading_width[4] = "15";
			$heading_width[5] = "17";
			$heading_width[6] = "20";
			$heading_width[7] = "20";
			$heading_width[8] = "30";
			$heading_width[9] = "25";
			$heading_width[10] = "13";
			$heading_width[11] = "17";
			$heading_width[12] = "20";
			$heading_width[13] = "25";
			$heading_width[14] = "20";
			$heading_width[15] = "45";
					if($COM_TYPE=="0105"){
						$heading_name7="ออกจาก";
						$heading_name8="พ้นจากราชการ";
					}elseif($COM_TYPE=="0106"){
						$heading_name7="ออกไปปฏิบัติงาน";
						$heading_name8="พ้นจากการปฏิบัติงาน";
					}
					$pdf->Cell($heading_width[7],7,"$heading_name7",'LTBR',0,'C',1);
					$pdf->Cell($heading_width[8],7,"$heading_name8",'LTBR',0,'C',1);
					$pdf->Cell(($heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12]+$heading_width[13]) ,7,"ตำแหน่งและส่วนราชการที่บรรจุ",'LTBR',0,'C',1);
					$pdf->Cell($heading_width[14] ,7,"ตั้งแต่วันที่",'LTR',0,'C',1);
					$pdf->Cell($heading_width[15] ,7,"หมายเหตุ",'LTR',1,'C',1);
		}elseif(in_array($COM_TYPE, array("0107"))){
			$heading_width[0] = "10";
			$heading_width[1] = "38";
			$heading_width[2] = "30";
			$heading_width[3] = "28";
			$heading_width[4] = "24";
			$heading_width[5] = "15";
			$heading_width[6] = "18";
			$heading_width[7] = "14";
			$heading_width[8] = "25";
			$heading_width[9] = "15";
			$heading_width[10] = "17";
			$heading_width[11] = "17";
			$heading_width[12] = "17";
			$heading_width[13] = "17";
			$heading_width[14] = "40";
					$pdf->Cell($heading_width[7],7,"ลาออกเมื่อ",'LTR',0,'C',1);
					$pdf->Cell(($heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11]) ,7,"ตำแหน่งและส่วนราชการที่บรรจุ",'LTBR',0,'C',1);
					$pdf->Cell($heading_width[12] ,7,"",'LTBR',0,'C',1);
					$pdf->Cell($heading_width[13] ,7,"ตั้งแต่วันที่",'LTR',0,'C',1);
					$pdf->Cell($heading_width[14] ,7,"หมายเหตุ",'LTR',1,'C',1);
		}elseif(in_array($COM_TYPE, array("0108"))){	//บรรจุผู้เคยเป็น พนง.เทศบาล พนง.ส่วนท้องถิ่น หรือ ขรก.อื่นกลับเข้ารับราขการ
			$heading_width[0] = "10";
			$heading_width[1] = "40";
			$heading_width[2] = "33";
			$heading_width[3] = "30";
			$heading_width[4] = "25";
			$heading_width[5] = "17";
			$heading_width[6] = "20";
			$heading_width[7] = "30";
			$heading_width[8] = "25";
			$heading_width[9] = "13";
			$heading_width[10] = "17";
			$heading_width[11] = "20";
			$heading_width[12] = "40";
					$pdf->Cell($heading_width[5],7,"ลาออกเมื่อ",'LTR',0,'C',1);
					$pdf->Cell(($heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10]) ,7,"ตำแหน่งและส่วนราชการที่บรรจุ",'LTBR',0,'C',1);
					$pdf->Cell($heading_width[11] ,7,"ตั้งแต่วันที่",'LTR',0,'C',1);
					$pdf->Cell($heading_width[12] ,7,"หมายเหตุ",'LTR',1,'C',1);
		}elseif(in_array($COM_TYPE, array("0109"))){	//รับโอน พนง. ส่วนท้องถิ่น
			$heading_width[0] = "9";
			$heading_width[1] = "35";
			$heading_width[2] = "30";
			$heading_width[3] = "25";
			$heading_width[4] = "22";
			$heading_width[5] = "15";
			$heading_width[6] = "15";
			$heading_width[7] = "22";
			$heading_width[8] = "22";
			$heading_width[9] = "15";
			$heading_width[10] = "29";
			$heading_width[11] = "25";
			$heading_width[12] = "20";
			$heading_width[13] = "15";
			$heading_width[14] = "15";
			$heading_width[15] = "15";
			$heading_width[16] = "15";
			$heading_width[17] = "20";
					$pdf->Cell(($heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10]),7,"สอบแข่งขันได้",'LTBR',0,'C',1);
					$pdf->Cell(($heading_width[11] + $heading_width[12] + $heading_width[13] + $heading_width[14] + $heading_width[15]) ,7,"ตำแหน่งและส่วนราชการที่รับโอน",'LTBR',0,'C',1);
					$pdf->Cell($heading_width[16] ,7,"ตั้งแต่วันที่",'LTR',0,'C',1);
					$pdf->Cell($heading_width[17] ,7,"หมายเหตุ",'LTR',1,'C',1);
		}else{	//--0104,0301			//โอน
			$heading_width[0] = "10";
			$heading_width[1] = "37";
			$heading_width[2] = "32";
			$heading_width[3] = "26";
			$heading_width[4] = "24";
			$heading_width[5] = "15";
			$heading_width[6] = "17";
			$heading_width[7] = "26";
			$heading_width[8] = "24";
			$heading_width[9] = "15";
			$heading_width[10] = "12";
			$heading_width[11] = "17";
			$heading_width[12] = "18";
			$heading_width[13] = "18";
					$pdf->Cell(($heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11]) ,7,"ตำแหน่งและส่วนราชการที่รับโอน",'LTBR',0,'C',1);
					$pdf->Cell($heading_width[12] ,7,"ตั้งแต่วันที่",'LTR',0,'C',1);
					$pdf->Cell($heading_width[13] ,7,"หมายเหตุ",'LTR',1,'C',1);
		} */
	} // end if
	/*
	function print_header(){
		global $pdf, $heading_width;
		global $COM_TYPE;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		if($COM_TYPE=="0101"  || $COM_TYPE=="5011"){
//		}elseif(in_array($COM_TYPE, array("0102", "0103", "0105", "0106", "0107", "0108", "0504", "0604", "0704", "0803"))){
		}elseif(in_array($COM_TYPE, array("0102", "0103", "0504", "0604", "0704", "0803"))){
		}elseif(in_array($COM_TYPE, array("แต่งตั้งรักษาราชการแทน","แต่งตั้งให้รักษาการในตำแหน่ง"))){
		}else if($COM_TYPE=="0302"){   //รับโอนข้าราชการพลเรือนสามัญผู้ได้รับวุฒิเพิ่มขึ้น
		}else{
				$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LTR',0,'C',1);
				$pdf->Cell($heading_width[1] ,7,"ชื่อ/นามสกุล",'LTR',0,'C',1);
				$pdf->Cell($heading_width[2] ,7,"วุฒิ/สาขา/",'LTR',0,'C',1);
				if(in_array($COM_TYPE, array("0108"))){
					$pdf->Cell(($heading_width[3] + $heading_width[4]) ,7,"ตำแหน่งและส่วนราชการเดิม",'LTBR',0,'C',1);
				}else{
					$pdf->Cell(($heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6]) ,7,"ตำแหน่งและส่วนราชการเดิม",'LTBR',0,'C',1);
				}
				if(in_array($COM_TYPE, array("0105","0106"))){
				}elseif(in_array($COM_TYPE, array("0107"))){
				}elseif(in_array($COM_TYPE, array("0108"))){
				}elseif(in_array($COM_TYPE, array("0109"))){		//รับโอน พนง. ส่วนท้องถิ่น
				}else{	//โอน
				}	

				//-----แถวที่ 2 ------------------------------------------------------------------------
				$pdf->Cell($heading_width[0] ,7,"ที่",'LBR',0,'C',1);
				$pdf->Cell($heading_width[1] ,7," (วันเดือนปีเกิด) ",'LBR',0,'C',1);
				if(in_array($COM_TYPE, array("แต่งตั้งรักษาราชการแทน","แต่งตั้งให้รักษาการในตำแหน่ง"))){
					$pdf->Cell($heading_width[2] ,7,"ตำแหน่ง/สังกัด",'LTBR',0,'C',1);
					$pdf->Cell($heading_width[3] ,7,"ตำแหน่งประเภท",'LTBR',0,'C',1);
					$pdf->Cell($heading_width[4] ,7,"ระดับ",'LTBR',0,'C',1);
					$pdf->Cell($heading_width[5] ,7,"ตำแหน่ง/สังกัด",'LTBR',0,'C',1);
					$pdf->Cell($heading_width[6] ,7,"ตำแหน่งประเภท",'LTBR',0,'C',1);
					$pdf->Cell($heading_width[7] ,7,"ระดับ",'LTBR',0,'C',1);
					$pdf->Cell($heading_width[8] ,7,"เลขที่",'LTBR',0,'C',1);
					$pdf->Cell($heading_width[9] ,7,"",'LBR',0,'C',1);
					$pdf->Cell($heading_width[10] ,7,"",'LBR',0,'C',1);
					$pdf->Cell($heading_width[11] ,7,"",'LBR',0,'C',1);
				}else{				
					if(in_array($COM_TYPE, array("0108"))){
						$pdf->Cell($heading_width[2] ,7,"สถานศึกษา",'LBR',0,'C',1);
						$pdf->Cell($heading_width[3] ,7,"ตำแหน่ง/สังกัด",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[4] ,7,"เงินเดือน",'LTBR',0,'C',1);
					}else{
						$pdf->Cell($heading_width[2] ,7,"สถานศึกษา",'LBR',0,'C',1);
						$pdf->Cell($heading_width[3] ,7,"ตำแหน่ง/สังกัด",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[4] ,7,"ตำแหน่งประเภท",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[5] ,7,"ระดับ",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[6] ,7,"เงินเดือน",'LTBR',0,'C',1);
					}
					//----------
					
					if(in_array($COM_TYPE, array("0105","0106"))){
						if($COM_TYPE=="0105"){
							$heading_name7="ราชการเมื่อ";
							$heading_name8="ทหารเมื่อ";
						}elseif($COM_TYPE=="0106"){
							$heading_name7="ตามมติ ครม.เมื่อ";
							$heading_name8="ตามมติ ครม. เมื่อ";
						}
						$pdf->Cell($heading_width[7] ,7,"$heading_name7",'LBR',0,'C',1);
						$pdf->Cell($heading_width[8] ,7,"$heading_name8",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[9] ,7,"ตำแหน่ง/สังกัด",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[10] ,7,"ตำแหน่งประเภท",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[11] ,7,"ระดับ",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[12] ,7,"เลขที่",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[13] ,7,"เงินเดือน",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[14] ,7,"",'LBR',1,'C',1);
						$pdf->Cell($heading_width[15] ,7,"",'LBR',1,'C',1);
					}elseif(in_array($COM_TYPE, array("0107"))){
						$pdf->Cell($heading_width[7] ,7,"",'LBR',0,'C',1);
						$pdf->Cell($heading_width[8] ,7,"ตำแหน่ง/สังกัด",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[9] ,7,"ตำแหน่งประเภท",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[10] ,7,"ระดับ",'LTBR',0,'C',1);						
						$pdf->Cell($heading_width[11] ,7,"เลขที่",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[12] ,7,"เงินเดือน",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[13] ,7,"",'LBR',0,'C',1);
						$pdf->Cell($heading_width[14] ,7,"",'LBR',1,'C',1);
					}elseif(in_array($COM_TYPE, array("0108"))){
						$pdf->Cell($heading_width[5] ,7,"",'LBR',0,'C',1);
						$pdf->Cell($heading_width[6] ,7,"ตำแหน่ง/สังกัด",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[7] ,7,"ตำแหน่งประเภท",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[8] ,7,"ระดับ",'LTBR',0,'C',1);						
						$pdf->Cell($heading_width[9] ,7,"เลขที่",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[10] ,7,"เงินเดือน",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[11] ,7,"",'LBR',0,'C',1);
						$pdf->Cell($heading_width[12] ,7,"",'LBR',1,'C',1);
					}elseif(in_array($COM_TYPE, array("0109"))){	//รับโอน พนง. ส่วนท้องถิ่น
						$pdf->Cell($heading_width[7] ,7,"ตำแหน่ง",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[8] ,7,"ตำแหน่งประเภท",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[9] ,7,"ลำดับที่",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[10] ,7,"ประกาศผลการสอบของ",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[11] ,7,"ตำแหน่ง/สังกัด",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[12] ,7,"ตำแหน่งประเภท",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[13] ,7,"ระดับ",'LBR',1,'C',1);
						$pdf->Cell($heading_width[14] ,7,"เลขที่",'LBR',1,'C',1);
						$pdf->Cell($heading_width[15] ,7,"เงินเดือน",'LBR',1,'C',1);
						$pdf->Cell($heading_width[16] ,7,"",'LBR',1,'C',1);
						$pdf->Cell($heading_width[17] ,7,"",'LBR',1,'C',1);
					}else{	//โอน
						$pdf->Cell($heading_width[7] ,7,"ตำแหน่ง/สังกัด",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[8] ,7,"ตำแหน่งประเภท",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[9] ,7,"ระดับ",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[10] ,7,"เลขที่",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[11] ,7,"เงินเดือน",'LTBR',0,'C',1);
						$pdf->Cell($heading_width[12] ,7,"",'LBR',0,'C',1);
						$pdf->Cell($heading_width[13] ,7,"",'LBR',1,'C',1);
					}
				}
		} // end if
	} // function		
		*/
	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRENAME_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE,b.PER_RETIREDATE, b.PER_TYPE, b.LEVEL_NO,
											a.EN_CODE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE, a.CMD_AC_NO, a.CMD_ACCOUNT,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	a.CMD_SEQ ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRENAME_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE,b.PER_RETIREDATE, b.PER_TYPE,
											a.EN_CODE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE, a.CMD_AC_NO, a.CMD_ACCOUNT,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	a.CMD_SEQ ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRENAME_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE,b.PER_RETIREDATE, b.PER_TYPE, b.LEVEL_NO,
											a.EN_CODE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE, a.CMD_AC_NO, a.CMD_ACCOUNT,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW__NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	a.CMD_SEQ ";	
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	echo $cmd;
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$PER_ID = $data[PER_ID];
		$PRENAME_CODE = trim($data[PRENAME_CODE]);
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PRENAME_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PN_NAME = $data2[PN_NAME];		
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PER_CARDNO = $data[PER_CARDNO];
		$cardID = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
		$PER_RETIREDATE = show_date_format($data[PER_RETIREDATE],$DATE_DISPLAY);
		$PER_TYPE = $data[PER_TYPE];
		$LEVEL_NO = trim($data[LEVEL_NO]);
		
		$EDU_TYPE = "%1%";
		if($DPISDB=="odbc"){
			$cmd = "	select		b.EN_NAME, c.EM_NAME, d.INS_NAME
								from		( 
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
								where		a.PER_ID = $PER_ID and a.EDU_TYPE like '$EDU_TYPE' ";
		}elseif($DPISDB=="oci8"){
			$cmd = "	select		b.EN_NAME, c.EM_NAME, d.INS_NAME
								from		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d
								where		a.PER_ID = $PER_ID and a.EDU_TYPE like '$EDU_TYPE' and a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and a.INS_CODE=d.INS_CODE(+) ";
		}elseif($DPISDB=="mysql"){
			$cmd = "	select		b.EN_NAME, c.EM_NAME, d.INS_NAME
								from		( 
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
								where		a.PER_ID = $PER_ID and a.EDU_TYPE like '$EDU_TYPE' ";
		} // end if
		$db_dpis2->send_cmd($cmd);
//		$db_dpis->show_error();
		$data2 = $db_dpis2->get_array();
		$EN_NAME = trim($data2[EN_NAME]);
		$EM_NAME = trim($data2[EM_NAME]);
		$INS_NAME = trim($data2[INS_NAME]);
		
		$CMD_LEVEL = trim($data[CMD_LEVEL]);
		$CMD_ACC_NO = trim($data[CMD_AC_NO]);
		$CMD_ACCOUNT = trim($data[CMD_ACCOUNT]);
		if($DPISDB=="mysql"){
			$arr_temp = explode("|", $data[CMD_POSITION]);
		}else{
			$arr_temp = explode("\|", $data[CMD_POSITION]);
		}
		$CMD_POS_NO = $arr_temp[0];
		$CMD_POSITION = $arr_temp[1];

		$CMD_ORG3 = $data[CMD_ORG3];
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_SALARY = $data[CMD_SALARY];
		$CMD_DATE = show_date_format($data[CMD_DATE],$DATE_DISPLAY);
		$CMD_NOTE1 = trim($data[CMD_NOTE1]);
		$CMD_NOTE2 = trim($data[CMD_NOTE2]);

		//--ตำแหน่งและส่วนราชการเดิม
		$cmd = "select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$CMD_LEVEL' ";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$CMD_POSITION_TYPE = trim($data2[POSITION_TYPE]);
		$CMD_LEVEL_NAME = trim($data2[POSITION_LEVEL]);
		
		//--ตำแหน่งและส่วนราชการที่ย้าย
		$cmd = "select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$NEW_LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$NEW_POSITION_TYPE = trim($data2[POSITION_TYPE]);
		$NEW_LEVEL_NAME = trim($data2[POSITION_LEVEL]);

		if($PER_TYPE==1){
			$PL_CODE = trim($data[PL_CODE]);
			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PL_NAME]."\n".$NEW_LEVEL_NAME;
			
			$cmd = " select a.PM_CODE, a.PT_CODE, b.PT_NAME from PER_POSITION a, PER_TYPE b where trim(a.POS_NO)='$CMD_POS_NO' and a.PT_CODE=b.PT_CODE and a.DEPARTMENT_ID = $DEPARTMENT_ID";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_CODE = trim($data2[PT_CODE]);
			$CMD_PT_NAME = trim($data2[PT_NAME]);
			$CMD_PM_CODE = trim($data2[PM_CODE]);
			
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$CMD_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PM_NAME = trim($data2[PM_NAME]);

			$CMD_POSITION = (trim($CMD_PM_CODE)?"$CMD_PM_NAME (":"") . (trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME . (($CMD_PT_NAME != "ทั่วไป" && $CMD_LEVEL >= 6)?"$CMD_PT_NAME":"")):"") . (trim($CMD_PM_CODE)?")":"");

			$POS_ID = $data[POS_ID];
			$cmd = "	select		a.POS_NO, a.POS_NO_NAME, b.PL_NAME, c.ORG_NAME, a.PT_CODE, d.PT_NAME, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE
						from		PER_POSITION a, PER_LINE b, PER_ORG c, PER_TYPE d
						where		a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID and a.PT_CODE=d.PT_CODE ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POS_NO_NAME]).trim($data2[POS_NO]);
			$NEW_PL_NAME = trim($data2[PL_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			$NEW_PT_CODE = trim($data2[PT_CODE]);
			$NEW_PT_NAME = trim($data2[PT_NAME]);
			$NEW_PM_CODE = trim($data2[PM_CODE]);
			
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$NEW_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_PM_NAME = trim($data2[PM_NAME]);
			
//		$NEW_PL_NAME = (trim($NEW_PM_CODE)?"$NEW_PM_NAME (":"") . (trim($NEW_PL_NAME)?(" ".$NEW_PL_NAME ." ". $NEW_LEVEL_NAME. (($NEW_PT_NAME != "ทั่วไป" && $NEW_LEVEL_NO >= 6)?"$NEW_PT_NAME":"")):"") . (trim($NEW_PM_CODE)?")":"");
		$NEW_PL_NAME = (trim($NEW_PM_CODE)?"$NEW_PM_NAME (":"") . (trim($NEW_PL_NAME)?(" ".$NEW_PL_NAME.(($NEW_PT_NAME != "ทั่วไป" && $NEW_LEVEL_NO >= 6)?"$NEW_PT_NAME":"")):"") . (trim($NEW_PM_CODE)?")":"");
		}elseif($PER_TYPE==2){
			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select PN_NAME from PER_POS_NAME where trim(PN_CODE)='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PN_NAME]."\n".$NEW_LEVEL_NAME;

			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME):"";

			$POEM_ID = $data[POEM_ID];
			$cmd = "	select		a.POEM_NO, a.POEM_NO_NAME, b.PN_NAME, c.ORG_NAME
								from		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
								where		a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POEM_NO_NAME]).trim($data2[POEM_NO]);
			$NEW_PL_NAME = trim($data2[PN_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);

//			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". $NEW_LEVEL_NAME):"";
			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME):"";
		}elseif($PER_TYPE==3){
			$EP_CODE = trim($data[EP_CODE]);
			$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE)='$EP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[EP_NAME]."\n".$NEW_LEVEL_NAME;

			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME):"";

			$POEMS_ID = $data[POEMS_ID];
			$cmd = "	select		a.POEMS_NO, a.POEMS_NO_NAME, b.EP_NAME, c.ORG_NAME
								from		PER_POS_EMP a, PER_EMPSER_POS_NAME b, PER_ORG c
								where		a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POEMS_NO_NAME]).trim($data2[POEMS_NO]);
			$NEW_PL_NAME = trim($data2[EP_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);

//			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". $NEW_LEVEL_NAME):"";
			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME):"";
		}elseif($PER_TYPE==4){
			$TP_CODE = trim($data[TP_CODE]);
			$cmd = " select TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE)='$TP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[TP_NAME]."\n".$NEW_LEVEL_NAME;

			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME):"";

			$POT_ID = $data[POT_ID];
			$cmd = "	select		a.POT_NO, a.POT_NO_NAME, b.TP_NAME, c.ORG_NAME
								from		PER_POS_TEMP a, PER_TEMP_POS_NAME b, PER_ORG c
								where		a.POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POT_NO_NAME]).trim($data2[POT_NO]);
			$NEW_PL_NAME = trim($data2[TP_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);

//			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". $NEW_LEVEL_NAME):"";
			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME):"";
		} // end if
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] =(($NUMBER_DISPLAY==2)?convert2thaidigit($data_row):$data_row);
//		if(in_array($COM_TYPE, array("0101", "0102", "0103", "0105", "0106", "0107", "0108", "0504", "0604", "0704", "0803")))
		if(in_array($COM_TYPE, array("0101", "0102", "0103", "0504", "0604", "0704", "0803", "5011")))
			$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME ."\n(". (($NUMBER_DISPLAY==2)?convert2thaidigit( $PER_BIRTHDATE): $PER_BIRTHDATE) .")\n(". (($NUMBER_DISPLAY==2)?convert2thaidigit($cardID):$cardID).")";
		else
//			$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME ."\n".card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME ."\n(". (($NUMBER_DISPLAY==2)?convert2thaidigit( $PER_BIRTHDATE): $PER_BIRTHDATE) .")\n(". (($NUMBER_DISPLAY==2)?convert2thaidigit($cardID):$cardID).")";
		$arr_content[$data_count][educate] = $EN_NAME . "\n".($EM_NAME?"$EM_NAME":"") ."\n".($INS_NAME?"$INS_NAME":"");
		$arr_content[$data_count][position] = $PL_NAME;	//ตน. และตน.ที่สอบแข่งขันได้
		$arr_content[$data_count][cmd_acc_no] =  (($NUMBER_DISPLAY==2)?convert2thaidigit($CMD_ACC_NO):$CMD_ACC_NO);
		$arr_content[$data_count][cmd_account] = $CMD_ACCOUNT;
		
		$arr_content[$data_count][cmd_position] = $CMD_POSITION;
		$arr_content[$data_count][cmd_position_type] = $CMD_POSITION_TYPE;
		$arr_content[$data_count][cmd_position_level] = $CMD_LEVEL_NAME;
		$arr_content[$data_count][cmd_org3] = $CMD_ORG3;
		$arr_content[$data_count][cmd_old_salary] =  ($CMD_OLD_SALARY?(($NUMBER_DISPLAY==2)?convert2thaidigit(CMD_OLD_SALARY):CMD_OLD_SALARY):"-");  
		$arr_content[$data_count][retire_date] = (($NUMBER_DISPLAY==2)?convert2thaidigit( $PER_RETIREDATE): $PER_RETIREDATE);
	
		$arr_content[$data_count][new_position] = $NEW_PL_NAME." ".($NEW_ORG_NAME?"$NEW_ORG_NAME":"");
		$arr_content[$data_count][new_position_type] = $NEW_POSITION_TYPE;
		$arr_content[$data_count][new_position_level] = $NEW_LEVEL_NAME;
		$arr_content[$data_count][new_pos_no] =(($NUMBER_DISPLAY==2)?convert2thaidigit($NEW_POS_NO):$NEW_POS_NO);
		$arr_content[$data_count][cmd_salary] =  ($CMD_SALARY?(($NUMBER_DISPLAY==2)?convert2thaidigit($CMD_SALARY):$CMD_SALARY):"-");
		
		$arr_content[$data_count][cmd_date] = (($NUMBER_DISPLAY==2)?convert2thaidigit($CMD_DATE):$CMD_DATE);
//		$arr_content[$data_count][cmd_note] = $CMD_NOTE1 . ($CMD_NOTE2?("\n".$CMD_NOTE2):"");
		$arr_content[$data_count][cmd_note1] = (($NUMBER_DISPLAY==2)?convert2thaidigit($CMD_NOTE1):$CMD_NOTE1);
		$arr_content[$data_count][cmd_note2] = (($NUMBER_DISPLAY==2)?convert2thaidigit($CMD_NOTE2):$CMD_NOTE2);
		
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
//		echo "$head_text1<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
		if (!$result) echo "****** error ****** on open table for $table<br>";

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$EDUCATE = $arr_content[$data_count][educate];
			$POSITION = $arr_content[$data_count][position];
			$CMD_ACC_NO = $arr_content[$data_count][cmd_acc_no];
			$CMD_ACCOUNT = $arr_content[$data_count][cmd_account];
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
			$CMD_POSITION_TYPE = $arr_content[$data_count][cmd_position_type];
			$CMD_LEVEL_NAME=$arr_content[$data_count][cmd_position_level];
			$CMD_ORG3 = $arr_content[$data_count][cmd_org3];
			$CMD_OLD_SALARY = $arr_content[$data_count][cmd_old_salary];
			$PER_RETIREDATE = $arr_content[$data_count][retire_date];
			$NEW_POSITION = $arr_content[$data_count][new_position];
			$NEW_POSITION_TYPE = $arr_content[$data_count][new_position_type];
			$NEW_POSITION_LEVEL = $arr_content[$data_count][new_position_level];
			$NEW_LEVEL_NAME = $arr_content[$data_count][new_position_level];
			$NEW_POS_NO = $arr_content[$data_count][new_pos_no];
			$CMD_SALARY = $arr_content[$data_count][cmd_salary];
			$CMD_DATE = $arr_content[$data_count][cmd_date];
//			$CMD_NOTE = $arr_content[$data_count][cmd_note];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
			
			$arr_data = (array) null;
			if($COM_TYPE=="0101" || $COM_TYPE=="5011"){
				$arr_data[] =  "$ORDER";
				$arr_data[] = "$NAME";
				$arr_data[] = "$EDUCATE";
				$arr_data[] = "$POSITION";
				$arr_data[] = "$CMD_ACC_NO";
				$arr_data[] = "$CMD_ACCOUNT";
				$arr_data[] = "$NEW_POSITION";
				$arr_data[] = "$NEW_POSITION_TYPE";
				$arr_data[] = "$NEW_POSITION_LEVEL";
				$arr_data[] = "$NEW_POS_NO";
				$arr_data[] = "$CMD_SALARY";
				$arr_data[] = "$CMD_DATE";
				$arr_data[] = "$CMD_NOTE1";

				$data_align = array("C", "L", "L", "L", "C", "L", "L", "R", "L", "R", "R", "C", "L");
				
				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "cordia", "14", "", "000000", "");
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

				if($CMD_NOTE2){
					$arr_data = (array) null;
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";

					$data_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
				
					$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "cordia", "14", "", "000000", "");
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				}
//			}elseif(in_array($COM_TYPE, array("0102", "0103", "0105", "0106", "0107", "0108", "0504", "0604", "0704", "0803"))){
			}elseif(in_array($COM_TYPE, array("0102", "0103", "0504", "0604", "0704", "0803"))){
				$arr_data[] = "$ORDER";
				$arr_data[] = "$NAME";
				$arr_data[] = "$EDUCATE";
				$arr_data[] = "$NEW_POSITION";
				$arr_data[] = "$NEW_POSITION_TYPE";
				$arr_data[] = "$NEW_POSITION_LEVEL";
				$arr_data[] = "$NEW_POS_NO";
				$arr_data[] = "$CMD_SALARY";
				$arr_data[] = "$CMD_DATE";
				$arr_data[] = "$CMD_NOTE1";

				$data_align = array("C", "L", "L", "L", "R", "R", "R", "R", "C", "L");
				
				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "cordia", "14", "", "000000", "");
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

				if($CMD_NOTE2){
					$arr_data = (array) null;
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";

					$data_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
				
					$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "cordia", "14", "", "000000", "");
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				}
			}elseif(in_array($COM_TYPE, array("แต่งตั้งรักษาราชการแทน","แต่งตั้งให้รักษาการในตำแหน่ง"))){
				$arr_data[] = "$ORDER";
				$arr_data[] = "$NAME";
				$arr_data[] = "$CMD_POSITION";
				$arr_data[] = "$CMD_POSITION_TYPE";
				$arr_data[] = "$CMD_LEVEL_NAME";
				$arr_data[] = "$NEW_POSITION";
				$arr_data[] = "$NEW_POSITION_TYPE";
				$arr_data[] = "$NEW_POSITION_LEVEL";
				$arr_data[] = "$CMD_ACC_NO";
				$arr_data[] = "$CMD_SALARY";
				$arr_data[] = "$CMD_DATE";
				$arr_data[] = "$CMD_NOTE1";

				$data_align = array("C", "L", "L", "L", "L", "L", "C", "C", "C", "L", "L", "L");
				
				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "cordia", "14", "", "000000", "");
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

				if($CMD_NOTE2){
					$arr_data = (array) null;
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";

					$data_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
				
					$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "cordia", "14", "", "000000", "");
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				}
			}elseif($COM_TYPE=="0302"){  //รับโอนข้าราชการพลเรือนสามัญผู้ได้รับวุฒิเพิ่มขึ้น
				$arr_data[] = "$ORDER";
				$arr_data[] = "$NAME";
				$arr_data[] = "$EDUCATE";
				$arr_data[] = "$$CMD_POSITION\n$CMD_ORG3";
				$arr_data[] = "$CMD_POSITION_TYPE";
				$arr_data[] = "$CMD_LEVEL_NAME";
				$arr_data[] = "$CMD_OLD_SALARY";
				$arr_data[] = "$POSITION";
				$arr_data[] = "$CMD_ACC_NO";
				$arr_data[] = "$CMD_ACCOUNT";
				$arr_data[] = "$NEW_POSITION";
				$arr_data[] = "$NEW_POSITION_TYPE";
				$arr_data[] = "$NEW_LEVEL_NAME";
				$arr_data[] = "$NEW_POS_NO";
				$arr_data[] = "$CMD_SALARY";
				$arr_data[] = "$CMD_DATE";
				$arr_data[] = "$CMD_NOTE1";

				$data_align = array("C", "L", "L", "L", "C", "C", "R", "L", "R", "L", "L", "C", "C", "C", "R", "C", "L");
				
				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "cordia", "14", "", "000000", "");
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

				if($CMD_NOTE2){
					$arr_data = (array) null;
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";

					$data_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
				
					$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "cordia", "14", "", "000000", "");
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				}
			}else{
				$arr_data[] = "$ORDER";
				$arr_data[] = "$NAME";
				$arr_data[] = "$EDUCATE";
				$arr_data[] = "$CMD_POSITION";
				if(in_array($COM_TYPE, array("0108"))){
					$arr_data[] = "$CMD_OLD_SALARY";
				}else{
					$arr_data[] = "$CMD_POSITION_TYPE";
					$arr_data[] = "$CMD_LEVEL_NAME";
					$arr_data[] = "$CMD_OLD_SALARY";
				}
				if(in_array($COM_TYPE, array("0105","0106"))){
					if($COM_TYPE=="0105"){
						$PER_DATE="วันพ้นทหาร";
					}elseif($COM_TYPE=="0106"){
						$PER_DATE="วันปฏิบัติงาน";
					}
					$arr_data[] = "$PER_RETIREDATE";
					$arr_data[] = "$PER_DATE";
					$arr_data[] = "$NEW_POSITION";
					$arr_data[] = "$NEW_POSITION_TYPE";
					$arr_data[] = "$NEW_POSITION_LEVEL";
					$arr_data[] = "$NEW_POS_NO";
					$arr_data[] = "$CMD_SALARY";
					$arr_data[] = "$CMD_DATE";
					$arr_data[] = "$CMD_NOTE1";
				
					$data_align = array("C", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "R", "L", "R", "C", "L");
				}elseif(in_array($COM_TYPE, array("0107"))){
					$arr_data[] = "$PER_RETIREDATE";
					$arr_data[] = "$NEW_POSITION";
					$arr_data[] = "$NEW_POSITION_TYPE";
					$arr_data[] = "$NEW_POSITION_LEVEL";
					$arr_data[] = "$NEW_POS_NO";
					$arr_data[] = "$CMD_SALARY";
					$arr_data[] = "$CMD_DATE";
					$arr_data[] = "$CMD_NOTE1";
				
					$data_align = array("C", "L", "L", "L", "L", "L", "L", "L", "L", "L", "R", "L", "R", "C", "L");
				}elseif(in_array($COM_TYPE, array("0108"))){
					$arr_data[] = "$PER_RETIREDATE";
					$arr_data[] = "$NEW_POSITION";
					$arr_data[] = "$NEW_POSITION_TYPE";
					$arr_data[] = "$NEW_POSITION_LEVEL";
					$arr_data[] = "$NEW_POS_NO";
					$arr_data[] = "$CMD_SALARY";
					$arr_data[] = "$CMD_DATE";
					$arr_data[] = "$CMD_NOTE1";
				
					$data_align = array("C", "L", "L", "L", "L", "L", "L", "L", "R", "L", "R", "C", "L");
				}elseif(in_array($COM_TYPE, array("0109"))){		//รับโอน พนง. ส่วนท้องถิ่น
					$arr_data[] = "$POSITION";
					$arr_data[] = "$$CMD_POSITION\n$CMD_ORG3";
					$arr_data[] = "$CMD_ACC_NO";
					$arr_data[] = "$CMD_ACCOUNT";
					$arr_data[] = "$NEW_POSITION";
					$arr_data[] = "$NEW_POSITION_TYPE";
					$arr_data[] = "$NEW_POSITION_LEVEL";
					$arr_data[] = "$NEW_POS_NO";
					$arr_data[] = "$CMD_SALARY";
					$arr_data[] = "$CMD_DATE";
					$arr_data[] = "$CMD_NOTE1";
				
					$data_align = array("C", "L", "L", "L", "L", "L", "L", "L", "L", "R", "L", "L", "L", "L", "L", "R", "C", "L");
				}else{		//โอน
					$arr_data[] = "$NEW_POSITION";
					$arr_data[] = "$NEW_POSITION_TYPE";
					$arr_data[] = "$NEW_POSITION_LEVEL";
					$arr_data[] = "$NEW_POS_NO";
					$arr_data[] = "$CMD_SALARY";
					$arr_data[] = "$CMD_DATE";
					$arr_data[] = "$CMD_NOTE1";
				
					$data_align = array("C", "L", "L", "L", "L", "L", "L", "L", "L", "R", "C", "R", "C", "L");
				}	 //end else
				
				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "cordia", "14", "", "000000", "");
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

				if($CMD_NOTE2){
					$arr_data = (array) null;
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";

					$data_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
				
					$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "cordia", "14", "", "000000", "");
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				}
			} // end if
		} // end for
		$pdf->close_tab(""); 
		
		if($COM_NOTE){
			$head_text1 = "";
			$head_width1 = "25,257";
			$head_align1 = "L,L";
			$result = $pdf->open_tab($head_text1, $head_width1, 7, "", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
			if (!$result) echo "****** error ****** on open table for $table<br>";
			
			$arr_data = (array) null;
			$arr_data[] = "หมายเหตุ : ";
			$arr_data[] = "$COM_NOTE";

			$data_align = array("L", "L");
				
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "cordia", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

			$pdf->close_tab(""); 
		} // end if
	}else{
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>