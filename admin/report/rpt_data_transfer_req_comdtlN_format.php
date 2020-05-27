<?
//	fix head for P0203
//	echo "COM_TYPE=$COM_TYPE<br>";
 
	if($COM_TYPE=="0101" || $COM_TYPE=="5011"){	//สอบแข่งขันได้
	    if (!$FLAG_RTF) {
		$heading_width[0] = "9";
		$heading_width[1] = "40";
		$heading_width[2] = "30";
		$heading_width[3] = "20";
		$heading_width[4] = "9";
		$heading_width[5] = "28";
		$heading_width[6] = "35";
		$heading_width[7] = "20";
		$heading_width[8] = "20";
		$heading_width[9] = "17";
		$heading_width[10] = "17";
		$heading_width[11] = "18";
		$heading_width[12] = "25";
		}	else if ($FLAG_RTF) {
		$heading_width[0] = "4";
		$heading_width[1] = "12";
		$heading_width[2] = "10";
		$heading_width[3] = "6";
		$heading_width[4] = "8";
		$heading_width[5] = "6";
		$heading_width[6] = "14";
		$heading_width[7] = "10";
		$heading_width[8] = "10";
		$heading_width[9] = "4";
		$heading_width[10] = "6";
		$heading_width[11] = "10";
		$heading_width[12] = "10";
		}
		if($BKK_FLAG==1) {
			$heading_text[0] = "|ลำดับ|ที่";
			$heading_text[1] = "|".$FULLNAME_HEAD."|";
			$heading_text[2] = "|วุฒิ/สาขา|";
			$heading_text[3] = "<**1**>สอบแข่งขันได้|ตำแหน่ง";
			$heading_text[4] = "<**1**>สอบแข่งขันได้|ลำดับ|ที่";
			$heading_text[5] = "<**1**>สอบแข่งขันได้|ประกาศผลการสอบ";
			$heading_text[6] = "<**2**>ตำแหน่งและสังกัดที่บรรจุ|ตำแหน่ง/สังกัด";
			$heading_text[7] = "<**2**>ตำแหน่งและสังกัดที่บรรจุ|ตำแหน่งประเภท";
			$heading_text[8] = "<**2**>ตำแหน่งและสังกัดที่บรรจุ|ระดับ";
			$heading_text[9] = "<**2**>ตำแหน่งและสังกัดที่บรรจุ|เลขที่";
			$heading_text[10] = "|เงินเดือน|";
		} else {
			$heading_text[0] = "ลำดับ|ที่";
			$heading_text[1] = $FULLNAME_HEAD."|(วันเดือนปีเกิด)";
			if ($CARDNO_FLAG==1) $heading_text[1] .= "|$CARDNO_TITLE";
			$heading_text[2] = "วุฒิ/สาขา/|สถานศึกษา";
			$heading_text[3] = "<**1**>สอบแข่งขันได้|ตำแหน่ง";
			$heading_text[4] = "<**1**>สอบแข่งขันได้|ลำดับ|ที่";
			$heading_text[5] = "<**1**>สอบแข่งขันได้|ประกาศผลการสอบของ";
			$heading_text[6] = $COM_HEAD_02."ที่บรรจุแต่งตั้ง|ตำแหน่ง/สังกัด";
			$heading_text[7] = $COM_HEAD_02."ที่บรรจุแต่งตั้ง|ตำแหน่งประเภท";
			$heading_text[8] = $COM_HEAD_02."ที่บรรจุแต่งตั้ง|ระดับ";
			$heading_text[9] = $COM_HEAD_02."ที่บรรจุแต่งตั้ง|เลขที่";
			$heading_text[10] = $COM_HEAD_02."ที่บรรจุแต่งตั้ง|เงินเดือน";
		}
		$heading_text[11] = "|ตั้งแต่วันที่|";
		$heading_text[12] = "|หมายเหตุ|";

		$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[10] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[11] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[12] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");

		$heading_align = array('C','C','C','C','C','C','C','C','C','C','C','C','C');

		$data_align = array("C","L","L","L","C","L","L","C","C","C","R","C","L");
	}elseif(in_array($COM_TYPE, array("0102", "0103", "0504", "0604", "0704", "0803","5012","5013"))){	//บรรจุผู้ได้รับคัดเลือก
		$heading_width[0] = "10";
		$heading_width[1] = "43";
		$heading_width[2] = "40";
		$heading_width[3] = "44";
		$heading_width[4] = "20";
		$heading_width[5] = "17";
		$heading_width[6] = "17";
		$heading_width[7] = "20";
		$heading_width[8] = "24";
		$heading_width[9] = "50";

		$heading_text[0] = "ลำดับ|ที่";
		$heading_text[1] = $FULLNAME_HEAD."|(วันเดือนปีเกิด)";
		if ($CARDNO_FLAG==1) $heading_text[1] .= "|$CARDNO_TITLE";
		$heading_text[2] = "วุฒิ/สาขา/สถานศึกษา|";
		$heading_text[3] = $COM_HEAD_01."ที่บรรจุ|ตำแหน่ง/สังกัด";
		$heading_text[4] = $COM_HEAD_01."ที่บรรจุ|ตำแหน่งประเภท";
		$heading_text[5] = $COM_HEAD_01."ที่บรรจุ|ระดับ";
		$heading_text[6] = $COM_HEAD_01."ที่บรรจุ|เลขที่";
		$heading_text[7] = $COM_HEAD_01."ที่บรรจุ|เงินเดือน";
		$heading_text[8] = "|ตั้งแต่วันที่|";
		$heading_text[9] = "|หมายเหตุ|";

		$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");

		$heading_align = array('C','C','C','C','C','C','C','C','C','C');

		$data_align = array("C","L","L","L","C","C","C","R","C","L");
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
		$heading_text[1] = $FULLNAME_HEAD."|";
		$heading_text[2] = $COM_HEAD_01."|ตำแหน่ง/สังกัด";
		$heading_text[3] = $COM_HEAD_01."|ตำแหน่งประเภท";
		$heading_text[4] = $COM_HEAD_01."|ระดับ";
		$heading_text[5] = "<**2**>$heading_name4ุ|ตำแหน่ง/สังกัด";
		$heading_text[6] = "<**2**>$heading_name4ุ|ตำแหน่งประเภท";
		$heading_text[7] = "<**2**>$heading_name4ุ|ระดับ";
		$heading_text[8] = "<**2**>$heading_name4ุ|เลขที่";
		$heading_text[9] = "|ตั้งแต่วันที่|";
		$heading_text[10] = "|ถึงวันที่|";
		$heading_text[11] = "|หมายเหตุ|";

		$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[10] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[11] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");

		$heading_align = array('C','C','C','C','C','C','C','C','C','C','C','C');

		$data_align = array("C","L","C","C","L","L","L","C","L","R","C","L");
	}elseif($COM_TYPE=="0302" || $COM_TYPE=="5032"){  //รับโอนข้าราชการพลเรือนสามัญผู้ได้รับวุฒิเพิ่มขึ้น
		$heading_width[0] = "5";
		$heading_width[1] = "35";
		$heading_width[2] = "35";
		$heading_width[3] = "40";
		$heading_width[4] = "15";
		$heading_width[5] = "10";
		$heading_width[6] = "10";
		$heading_width[7] = "30";
		$heading_width[8] = "10";
		$heading_width[9] = "15";
		$heading_width[10] = "30";
		$heading_width[11] = "15";
		$heading_width[12] = "10";
		$heading_width[13] = "10";
		$heading_width[14] = "10";
		$heading_width[15] = "15";
		$heading_width[16] = "60";

		$heading_text[0] = "ลำดับ|ที่";
		$heading_text[1] = $FULLNAME_HEAD;
		if ($CARDNO_FLAG==1) $heading_text[1] .= "/|$CARDNO_TITLE";
		$heading_text[2] = "วุฒิที่ได้รับเพิ่มขึ้น/|สถานศึกษา/วันที่สำเร็จการศึกษา";
		$heading_text[3] = $COM_HEAD_01."เดิม|ตำแหน่ง/สังกัด";
		$heading_text[4] = $COM_HEAD_01."เดิม|ตำแหน่งประเภท";
		$heading_text[5] = $COM_HEAD_01."เดิม|ระดับ";
		$heading_text[6] = $COM_HEAD_01."เดิม|เงินเดือน";
		$heading_text[7] = "<**2**>สอบแข่งขันได้|ตำแหน่ง";
		$heading_text[8] = "<**2**>สอบแข่งขันได้|ลำดับที่";
		$heading_text[9] = "<**2**>สอบแข่งขันได้|ประกาศผลการสอบของ";
		$heading_text[10] = $COM_HEAD_03."ที่รับโอน|ตำแหน่ง/สังกัด";
		$heading_text[11] = $COM_HEAD_03."ที่รับโอน|ตำแหน่งประเภท";
		$heading_text[12] = $COM_HEAD_03."ที่รับโอน|ระดับ";
		$heading_text[13] = $COM_HEAD_03."ที่รับโอน|เลขที่";
		$heading_text[14] = $COM_HEAD_03."ที่รับโอน|เงินเดือน";
		$heading_text[15] = "|ตั้งแต่วันที่|";
		$heading_text[16] = "|หมายเหตุ|";

		$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[10] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[11] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[12] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[13] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[14] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[15] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[16] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");

		$heading_align = array('C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C');

		$data_align = array("C", "L", "L", "L", "C", "C", "R", "L", "R", "L", "L", "C", "C", "C", "R", "C", "L");
	}else{ //########
		$heading_width = (array) null;
		$heading_width[0] = 9;
		$heading_width[1] = 35;
		if(in_array($COM_TYPE, array("0105", "0106", "5016", "5017"))){
			$heading_width[2] = 25;
			$heading_width[3] = 25;
			$heading_width[4] = 15;
			$heading_width[5] = 15;
			$heading_width[6] = 15;
			$heading_width[7] = 18;
			$heading_width[8] = 18;
			$heading_width[9] = 25;
			$heading_width[10] = 15;
			$heading_width[11] = 15;
			$heading_width[12] = 10;
			$heading_width[13] = 15;
			$heading_width[14] = 18;
			$heading_width[15] = 18;
		}elseif(in_array($COM_TYPE, array("0107","5018"))){
			$heading_width[2] = 30;
			$heading_width[3] = 30;
			$heading_width[4] = 15;
			$heading_width[5] = 15;
			$heading_width[6] = 15;
			$heading_width[7] = 20;
			$heading_width[8] = 30;
			$heading_width[9] = 15;
			$heading_width[10] = 15;
			$heading_width[11] = 10;
			$heading_width[12] = 15;
			$heading_width[13] = 20;
			$heading_width[14] = 20;
		}elseif(in_array($COM_TYPE, array("0108","5019"))){
			$heading_width[2] = 30;
			$heading_width[3] = 30;
			$heading_width[4] = 15;
			$heading_width[5] = 15;
			$heading_width[6] = 10;
			$heading_width[7] = 15;
			$heading_width[8] = 30;
			$heading_width[9] = 15;
			$heading_width[10] = 15;
			$heading_width[11] = 10;
			$heading_width[12] = 15;
			$heading_width[13] = 20;
			$heading_width[14] =30;
		}elseif(in_array($COM_TYPE, array("0109","5015"))){	//รับโอน พนง. ส่วนท้องถิ่น
			$heading_width[2] = 30;
			$heading_width[3] = 30;
			$heading_width[4] = 15;
			$heading_width[5] = 15;
			$heading_width[6] = 30;
			$heading_width[7] = 25;
			$heading_width[8] = 14;
			$heading_width[9] = 33;
			$heading_width[10] = 30;
			$heading_width[11] = 40;
			$heading_width[12] = 14;
			$heading_width[13] = 15;
			$heading_width[14] = 20;
			$heading_width[15] = 15;
			$heading_width[16] = 20;
			$heading_width[17] = 100;
		}elseif(in_array($COM_TYPE, array("0303","5033"))){	//รับโอนข้าราชการพลเรือนสามัญมาดำรงตำแหน่งในระดับที่สูงขึ้น
			$heading_width[2] = 30;
			$heading_width[3] = 30;
			$heading_width[4] = 15;
			$heading_width[5] = 15;
			$heading_width[6] = 15;
			$heading_width[7] = 15;
			$heading_width[8] = 20;
			$heading_width[9] = 30;
			$heading_width[10] = 15;
			$heading_width[11] = 15;
			$heading_width[12] = 10;
			$heading_width[13] = 15;
			$heading_width[14] = 20;
			$heading_width[15] = 30;
		}else{	//--0104,0301			//โอน ขาด 0304
			$heading_width[2] = 30;
			$heading_width[3] = 30;
			$heading_width[4] = 15;
			$heading_width[5] = 15;
			$heading_width[6] = 15;
			$heading_width[7] = 30;
			$heading_width[8] = 15;
			$heading_width[9] = 15;
			$heading_width[10] = 10;
			$heading_width[11] = 15;
			$heading_width[12] = 20;
			$heading_width[13] = 30;
		}
		$head_line1 = (array) null;
		$head_line1[] = "ลำดับ";
		$head_line1[] = $FULLNAME_HEAD;
		if ($BKK_FLAG==1)
			$head_line1[] = "วุฒิ/สาขา";
		else
			$head_line1[] = "วุฒิ/สาขา/";
		if(in_array($COM_TYPE, array("0108","5019"))){
			$head_line1[] = $COM_HEAD_01."เดิม";
			$head_line1[] = $COM_HEAD_01."เดิม";
			$head_line1[] = $COM_HEAD_01."เดิม";
			$head_line1[] = $COM_HEAD_01."เดิม";
		}else{
			$head_line1[] = $COM_HEAD_02."เดิม";
			$head_line1[] = $COM_HEAD_02."เดิม";
			$head_line1[] = $COM_HEAD_02."เดิม";
			if($MFA_FLAG==1 && in_array($COM_TYPE, array("0301","5031","0303","5033"))) {
				$head_line1[] = $COM_HEAD_02."เดิม";
			}
			$head_line1[] = $COM_HEAD_02."เดิม";
		}
		if(in_array($COM_TYPE, array("0105", "0106", "5016", "5017"))){
			if($COM_TYPE=="0105" || $COM_TYPE=="5016"){
				$heading_name7="ออกจาก";
				$heading_name8="พ้นจากราชการ";
			}elseif($COM_TYPE=="0106" || $COM_TYPE=="5017"){
				$heading_name7="ออกไปปฏิบัติงาน";
				$heading_name8="พ้นจากการปฏิบัติงาน";
			}
			$head_line1[] = "$heading_name7";
			$head_line1[] = "$heading_name8";
			$head_line1[] = $COM_HEAD_03."ที่บรรจุ";
			$head_line1[] = $COM_HEAD_03."ที่บรรจุ";
			$head_line1[] = $COM_HEAD_03."ที่บรรจุ";
			$head_line1[] = $COM_HEAD_03."ที่บรรจุ";
			$head_line1[] = $COM_HEAD_03."ที่บรรจุ";
			$head_line1[] = "";
			$head_line1[] = "";
		}elseif(in_array($COM_TYPE, array("0107","5018"))){
			$head_line1[] = "";
			$head_line1[] = $COM_HEAD_03."ที่บรรจุ";
			$head_line1[] = $COM_HEAD_03."ที่บรรจุ";
			$head_line1[] = $COM_HEAD_03."ที่บรรจุ";
			$head_line1[] = $COM_HEAD_03."ที่บรรจุ";
			$head_line1[] = $COM_HEAD_03."ที่บรรจุ";
			$head_line1[] = "";
			$head_line1[] = "";
		}elseif(in_array($COM_TYPE, array("0108","5019"))){			
			$head_line1[] = "";
			$head_line1[] = $COM_HEAD_03."ที่บรรจุ";
			$head_line1[] = $COM_HEAD_03."ที่บรรจุ";
			$head_line1[] = $COM_HEAD_03."ที่บรรจุ";
			$head_line1[] = $COM_HEAD_03."ที่บรรจุ";
			$head_line1[] = $COM_HEAD_03."ที่บรรจุ";
			$head_line1[] = "";
			$head_line1[] = "";
		}elseif(in_array($COM_TYPE, array("0109","5015"))){
			$head_line1[] = "<**3**>สอบแข่งขันได้";
			$head_line1[] = "<**3**>สอบแข่งขันได้";
			$head_line1[] = "<**3**>สอบแข่งขันได้";
			$head_line1[] = "<**3**>สอบแข่งขันได้";
			$head_line1[] = "<**4**>ตำแหน่งและส่วนราชการที่รับโอน";
			$head_line1[] = "<**4**>ตำแหน่งและส่วนราชการที่รับโอน";
			$head_line1[] = "<**4**>ตำแหน่งและส่วนราชการที่รับโอน";
			$head_line1[] = "<**4**>ตำแหน่งและส่วนราชการที่รับโอน";
			$head_line1[] = "<**4**>ตำแหน่งและส่วนราชการที่รับโอน";
			$head_line1[] = "";
			$head_line1[] = "";
		}elseif(in_array($COM_TYPE, array("0303","5033"))){
			$head_line1[] = "ดำรงตำแหน่ง";
			$head_line1[] = $COM_HEAD_03."ที่รับโอนมาเลื่อน";
			$head_line1[] = $COM_HEAD_03."ที่รับโอนมาเลื่อน";
			$head_line1[] = $COM_HEAD_03."ที่รับโอนมาเลื่อน";
			$head_line1[] = $COM_HEAD_03."ที่รับโอนมาเลื่อน";
			$head_line1[] = $COM_HEAD_03."ที่รับโอนมาเลื่อน";
			$head_line1[] = "";
			$head_line1[] = "";
		}else{
			$head_line1[] = $COM_HEAD_03."ที่รับโอน";
			$head_line1[] = $COM_HEAD_03."ที่รับโอน";
			$head_line1[] = $COM_HEAD_03."ที่รับโอน";
			$head_line1[] = $COM_HEAD_03."ที่รับโอน";
			$head_line1[] = $COM_HEAD_03."ที่รับโอน";
			$head_line1[] = "";
			$head_line1[] = "";
		}

		//แถวที่ 2 ------------------------------
		$head_line2 = (array) null;
		$column_function = (array) null;
		$heading_align = (array) null;
		$data_align = (array) null;
		$head_line2[] = "ที่";		$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
		if ($BKK_FLAG==1)
			$head_line2[] = "";	
		else
			$head_line2[] = "(วัน เดือน ปีเกิด)";	
		$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");		$heading_align[] = "C";	$data_align[] = "C";
		if(in_array($COM_TYPE, array("แต่งตั้งรักษาราชการแทน","แต่งตั้งให้รักษาการในตำแหน่ง"))){
			$head_line2[] = "ตำแหน่ง/สังกัด";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			$head_line2[] = "ตำแหน่งประเภท";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			$head_line2[] = "ระดับ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			$head_line2[] = "ตำแหน่ง/สังกัด";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			$head_line2[] = "ตำแหน่งประเภท";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			$head_line2[] = "ระดับ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			$head_line2[] = "เลขที่";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");		$heading_align[] = "C";	$data_align[] = "C";
			$head_line2[] = "";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			$head_line2[] = "";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			$head_line2[] = "";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
		}else{	//
			if ($BKK_FLAG==1)
				$head_line2[] = "";	
			else
				$head_line2[] = "สถานศึกษา";	
			$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			$head_line2[] = "ตำแหน่ง/สังกัด";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			$head_line2[] = "ตำแหน่งประเภท";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			$head_line2[] = "ระดับ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			if($MFA_FLAG==1 && in_array($COM_TYPE, array("0301","5031","0303","5033"))) {
				$head_line2[] = "เลขที่";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "R";
			}
			$head_line2[] = "เงินเดือน";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "R";
			
			if(in_array($COM_TYPE, array("0105","0106", "5016", "5017"))){
				if($COM_TYPE=="0105" || $COM_TYPE=="5016"){
					$heading_name7="ราชการเมื่อ";
					$heading_name8="ทหารเมื่อ";
				}elseif($COM_TYPE=="0106" || $COM_TYPE=="5017"){
					$heading_name7="ตามมติ ครม.เมื่อ";
					$heading_name8="ตามมติ ครม. เมื่อ";
				}
				$head_line2[] = "$heading_name7";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "$heading_name8";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "ตำแหน่ง/สังกัด";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "ตำแหน่งประเภท";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "ระดับ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "เลขที่";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "เงินเดือน";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "R";
				$head_line2[] = "ตั้งแต่วันที่";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "หมายเหตุ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			}elseif(in_array($COM_TYPE, array("0107","5018"))){
				$head_line2[] = "ลาออกเมื่อ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "ตำแหน่ง/สังกัด";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "ตำแหน่งประเภท";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "ระดับ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "เลขที่";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "เงินเดือน";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "R";
				$head_line2[] = "ตั้งแต่วันที่";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "หมายเหตุ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			}elseif(in_array($COM_TYPE, array("0108","5019"))){
				$head_line2[] = "ลาออกเมื่อ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "ตำแหน่ง/สังกัด";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "ตำแหน่งประเภท";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "ระดับ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "เลขที่";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "เงินเดือน";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "R";
				$head_line2[] = "ตั้งแต่วันที่";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "หมายเหตุ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			}elseif(in_array($COM_TYPE, array("0109","5015"))){
				$head_line2[] = "ตำแหน่ง";		$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "ตำแหน่งประเภท";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "ลำดับที่";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "ประกาศผลการสอบของ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "ตำแหน่ง/สังกัด";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "ตำแหน่งประเภท";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "ระดับ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "เลขที่";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "เงินเดือน";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "R";
				$head_line2[] = "ตั้งแต่วันที่";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "หมายเหตุ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			}elseif(in_array($COM_TYPE, array("0303","5033"))){
				$head_line2[] = "ในระดับ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "ตำแหน่ง/สังกัด";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "ตำแหน่งประเภท";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "ระดับ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "เลขที่";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "เงินเดือน";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "R";
				$head_line2[] = "ตั้งแต่วันที่";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "หมายเหตุ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			}else{
				$head_line2[] = "ตำแหน่ง/สังกัด";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "ตำแหน่งประเภท";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "ระดับ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "เลขที่";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "เงินเดือน";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "R";
				$head_line2[] = "ตั้งแต่วันที่";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "หมายเหตุ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			}
		}
		$heading_text = (array) null;
		for($k=0; $k < count($head_line1); $k++) {
			$heading_text[] = $head_line1[$k]."|".$head_line2[$k];
		}
	}
	// function ประเภท aggregate มี  SUM, AVG, PERC ตามแนวบรรทัด (ROW)
	// 									SUM-columnindex1-columnindex2-....  Ex SUM-1-3-4-7 หมายถึง ผลรวมของ column ที่ 1,3,4 และ 7
	// 									AVG-columnindex1-columnindex2-....  Ex AVG-1-3-4-7 หมายถึง ค่าเฉลี่ยของ column ที่ 1,3,4 และ 7
	// 									PERCn-columnindex1-columnindex2-....  Ex PERCn-1-3-4-7 n คือ column ที่ เป็นเป้าหมายเปรียบเทียบร้อยละ (ค่า 1 ค่า ใน column 1 3 4 7) 
	//																												ถ้าไม่ใส่ค่า n ก็คือ ค่าสรุป (100%) PERC3-1-3-4-7 ก็คือ ค่าใน column ที่ 3 เป็นร้อยละเท่าใดของผลรวม column 1,3,4,7
	//	function ประเภท รูปแบบ (format) ซึ่งจะต้องตามหลัง function ประเภท aggregate เสมอ (ถ้ามี)
	//									TNUM-n คือ เปลี่ยนเป็นตัวเลขของไทย n คือ จำนวนทศนิยม ถ้ามีค่าเป็น ศูนย์ จะแสดงเป็น -
	//									TNUM0-n คือ เปลี่ยนเป็นตัวเลขของไทย n คือ จำนวนทศนิยม ถ้ามีค่าเป็น ศูนย์ จะแสดงเป็น 0
	//									ENUM-n คือ แสดงเป็นเลขอาราบิค n คือ จำนวนทศนิยม ถ้ามีค่าเป็น ศูนย์ จะแสดงเป็น -
	//									ENUM0-n คือ แสดงเป็นเลขอาราบิค n คือ จำนวนทศนิยม ถ้ามีค่าเป็น ศูนย์ จะแสดงเป็น 0
	//
//	echo "COM_TYPE=$COM_TYPE, heading_text=".implode(",",$heading_text)."<br>";
	
	$total_head_width = 0;
	for($iii=0; $iii < count($heading_width); $iii++) 	$total_head_width += (int)$heading_width[$iii];
	
	if (!$COLUMN_FORMAT) {	// ต้องกำหนดเป็น element ให้อยู่ใน form1 ด้วย  <input type="hidden" name="COLUMN_FORMAT" value="<?=$COLUMN_FORMAT
		$arr_column_map = (array) null;
		$arr_column_sel = (array) null;
		for($i=0; $i < count($heading_text); $i++) {
			$arr_column_map[] = $i;		// link index ของ head 
			$arr_column_sel[] = 1;			// 1=แสดง	0=ไม่แสดง   ถ้าไม่มีการปรับแต่งรายงาน ก็เป็นแสดงหมด
		}
		$arr_column_width = $heading_width;	// ความกว้าง
		$arr_column_align = $data_align;		// align
		$COLUMN_FORMAT = implode(",",$arr_column_map)."|".implode(",",$arr_column_sel)."|".implode(",",$arr_column_width)."|".implode(",",$arr_column_align);
	} else {
		$arrbuff = explode("|",$COLUMN_FORMAT);
		$arr_column_map = explode(",",$arrbuff[0]);		// index ของ head เริ่มต้น
		$arr_column_sel = explode(",",$arrbuff[1]);	// 1=แสดง	0=ไม่แสดง
		$arr_column_width = explode(",",$arrbuff[2]);	// ความกว้าง
		$heading_width = $arr_column_width;	// ความกว้าง
		$arr_column_align = explode(",",$arrbuff[3]);		// align
	}
	
	$total_show_width = 0;
	for($iii=0; $iii < count($arr_column_width); $iii++) 	if ($arr_column_sel[$iii]==1) $total_show_width += (int)$arr_column_width[$iii];

?>