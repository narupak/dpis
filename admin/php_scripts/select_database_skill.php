<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$UPDATE_USER = 1;
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$where = "";

	if ($search_org_id) {
		$where_per_id = " (SELECT PER_ID FROM PER_PERSONAL a, PER_POSITION b WHERE a.POS_ID = b.POS_ID and 
											b.DEPARTMENT_ID = $search_department_id and b.ORG_ID = $search_org_id) ";
		$where_org_id = " (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id and ORG_ID = $search_org_id) ";
	} else {
		$where_per_id = " (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id) ";
		$where_org_id = " (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
	}

	switch($CTRL_TYPE){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			break;
	} // end switch case

	if( $command=='MASTER' ) {  
		$cmd = " select max(SPS_ID) as MAX_ID from PER_SPECIAL_SKILL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT [ชื่อ-สกุล] AS FULLNAME, [ความเชี่ยวชาญพิเศษ1] AS SKILL1, [ความเชี่ยวชาญพิเศษ2] AS SKILL2, [ความเชี่ยวชาญพิเศษ3] AS SKILL3 
						FROM DATASES 
						WHERE [ความเชี่ยวชาญพิเศษ1] IS NOT NULL AND [ความเชี่ยวชาญพิเศษ1] <> 'ไม่ระบุ' AND [ความเชี่ยวชาญพิเศษ1] <> 'ไม่ระบุข้อมูล' AND [ความเชี่ยวชาญพิเศษ1] <> 'ไม่แจ้งข้อมูล' ";
//						WHERE [ความเชี่ยวชาญพิเศษ1] IS NOT NULL ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$FULLNAME = trim($data[FULLNAME]);
			$arr_temp = explode("(", $FULLNAME);
			$FULLNAME = $arr_temp[0];
			$SKILL1 = trim($data[SKILL1]);
			$arr_temp = explode(",", $SKILL1);
			$SKILL11 = $arr_temp[0];
			$SKILL12 = $arr_temp[1];
			$SKILL13 = $arr_temp[2];
			$SKILL14 = $arr_temp[3];
			$SKILL15 = $arr_temp[4];
			$SKILL16 = $arr_temp[5];
			$SKILL17 = $arr_temp[6];
			$SKILL18 = $arr_temp[7];
			$SKILL2 = trim($data[SKILL2]);
			if ($SKILL2) {
				$arr_temp = explode(",", $SKILL2);
			}
			$SKILL3 = trim($data[SKILL3]);

			$cmd = " select PER_ID, PER_CARDNO from PER_PERSONAL where PER_NAME||' '||PER_SURNAME = '$FULLNAME' ";
			$count_data = $db_dpis2->send_cmd($cmd);

			if ($count_data) {			
				$data2 = $db_dpis2->get_array();
				$PER_ID = $data2[PER_ID];
				$PER_CARDNO = trim($data2[PER_CARDNO]);
				if ($SKILL11) {
					$arr_temp = explode("เน้นทาง", $SKILL11);
					$SS_NAME = trim($arr_temp[0]);
					$arr_temp = explode("เน้น", $SS_NAME);
					$SS_NAME = trim($arr_temp[0]);
					$SPS_EMPHASIZE = "เน้นทาง".$arr_temp[1];

					$SS_CODE = "";
					if ($SS_NAME=="ด้านการแพทยศาสตร์" || $SS_NAME=="ด้านการแพทย์ศาสตร์" || $SS_NAME=="ด้านแพทยศาสตร์" || $SS_NAME=="ด้านการแพทย์" || $SS_NAME=="ด้านทันตแพทยศาสตร์" || $SS_NAME=="ด้านเภสัชศาสตร์" || $SS_NAME=="ด้านสาธารณสุขศาสตร์และวิทยาศาสตร์การแพทย์" || $SS_NAME=="ด้านการแพทยศาสตร์และสาธารณสุข" || $SS_NAME=="ด้านพยาบาลศาสตร์" || $SS_NAME=="ด้านการแพทยศาสตร์ 1 (ด้านอายุรกรรมกุมารเวชกรรม จักษุ โต ศอ นาสิก ประสาทวิทยาและจิตเวช พยาธิตจวิทยา)" || $SS_NAME=="ด้านแพทยศาสตร์ 3 (ด้านเวชกรรมป้องกัน สาธารณสุข เวชกรรมฟื้นฟู)" || $SS_NAME=="การแพทยศาสตร์ 3(ด้านเวชกรรมป้องกัน สาธารณสุข เวชกรรมฟื้นฟู)" || $SS_NAME=="ด้านสาธารณสุขศาสาตร์และวิทยาศาสตร์การแพทย์" || $SS_NAME=="ด้านการแพทย์และสาธารสุข" || $SS_NAME=="ด้านการแพทย์(ด้านอายุรกรรมกุมารเวชกรรม จักษุ โสต ศอ นาสิก ประสาทวิทยาและจิตเวช พยาธิ ตจวิทยา)") $SS_CODE = "002";
					elseif ($SS_NAME=="ด้านการเมือง การปกครอง" || $SS_NAME=="ด้านการปกครองการเมือง") $SS_CODE = "011";
					elseif ($SS_NAME=="ด้านการเกษตร" || $SS_NAME=="ด้านการเกษตร (ด้านดิน ปุ๋ย พืชและอื่น ๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านการเกษตร (ด้านดิน ปุ๋ย พืช และอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านการเกษตร (ด้านสัตว์บก สัตว์น้ำ และอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านการเกษตร (ด้านสัตว์บก สัตว์น้ำ และอื่นๆที่เกี่ยวข้อง)" || $SS_NAME=="ด้านเกษตร การชลประทาน" || $SS_NAME=="ด้านส่งเสริมการเกษตร และด้านบริหารจัดการศัตรูพืช" || $SS_NAME=="ด้านเกษตร/ข้าว การอารักขาพืช") $SS_CODE = "003";
					elseif ($SS_NAME=="ด้านสังคม ชุมชน แรงงาน" || $SS_NAME=="ด้านสังคมชุมชนแรงงาน") $SS_CODE = "009";
					elseif ($SS_NAME=="ด้านวิทยาศาสตร์" || $SS_NAME=="ด้านเทคโนโลยี") $SS_CODE = "005";
					elseif ($SS_NAME=="ด้านการบริหาร" || $SS_NAME=="ด้านบริหารจัดการและบริหารธุรกิจ" || $SS_NAME=="ด้านการบริหารและบริหารธุรกิจ" || $SS_NAME=="ด้านการบริหารจัดการ และบริหารธุรกิจ") $SS_CODE = "016";
					elseif ($SS_NAME=="ด้านการเงินการคลังงบประมาณ" || $SS_NAME=="ด้านเศรษฐศาสตร์และการคลัง" || $SS_NAME=="ด้านการเงินและการบัญชี" || $SS_NAME=="ด้านการเงิน การคลัง งบประมาณ กองคลัง") $SS_CODE = "008";
					elseif ($SS_NAME=="ด้านวิศวกรรมศาสตร์" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธา และอื่น ๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธาและอื่น ๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านเครื่องกล ไฟฟ้า เหมืองแร่ ปิโตรเลียม โลหการ อุตสาหกรรม)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธา และด้านอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธา และอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรม (ด้านโยธาและอื่น ๆ ที่เกี่ยวขัอง)" || $SS_NAME=="วิศวกรรม") $SS_CODE = "006";
					elseif ($SS_NAME=="ด้านนโยบายและแผน" || $SS_NAME=="ด้านการพัฒนาทรัพยากรบุคคล" || $SS_NAME=="ด้านการวางแผน" || $SS_NAME=="ด้านวางแผนพัฒนา" || $SS_NAME=="ด้านการวางแผนและพัฒนา") $SS_CODE = "013";
					elseif ($SS_NAME=="ด้านอุตุนิยมวิทยา" || $SS_NAME=="ด้านธรรมชาติและสิ่งแวดล้อม" || $SS_NAME=="ด้านทรัพยากรธรรมชาติและสาธารณสุข") $SS_CODE = "004";
					elseif ($SS_NAME=="ด้านการศึกษา" || $SS_NAME=="ด้านการศึกษา ศิลปศาสตร์ และสังคมศาสตร์" || $SS_NAME=="ด้านการศึกษาและการวางแผนการศึกษานอกระบบ") $SS_CODE = "001";
					elseif ($SS_NAME=="ด้านสถาปัตยกรรมศาสตร์") $SS_CODE = "007";
					elseif ($SS_NAME=="ด้านภาษา" || $SS_NAME=="ด้านภาษาศาสตร์" || $SS_NAME=="ด้านศิลปวววัฒนธรรมและศาสนา" || $SS_NAME=="ด้านศิลปวัฒนธรรม และศาสนา") $SS_CODE = "012";
					elseif ($SS_NAME=="ด้านการพัฒนาอุตสาหกรรม") $SS_CODE = "021";
					elseif ($SS_NAME=="ด้านต่างประเทส" || $SS_NAME=="ด้านการต่างประเทศ") $SS_CODE = "020";
					elseif ($SS_NAME=="ด้านประชาสัมพันธ์") $SS_CODE = "017";

					$cmd = " select SS_CODE from PER_SPECIAL_SKILLGRP where SS_NAME = '$SS_NAME' ";
					$count_data = $db_dpis2->send_cmd($cmd);
					if ($count_data || $SS_CODE) {			
						$data2 = $db_dpis2->get_array();
						$SS_CODE = trim($data2[SS_CODE]);
						$cmd = " INSERT INTO PER_SPECIAL_SKILL (SPS_ID, PER_ID, PER_CARDNO, SS_CODE, SPS_EMPHASIZE, SPS_REMARK, UPDATE_USER, UPDATE_DATE)
										VALUES ($MAX_ID, $PER_ID, '$PER_CARDNO', '$SS_CODE', '$SPS_EMPHASIZE', '$SKILL11', $UPDATE_USER, '$UPDATE_DATE') ";
		//				$db_dpis->send_cmd($cmd);
		//				$db_dpis->show_error();
//						echo "$cmd<br>";
						$MAX_ID++;
					} else {
						echo "$SS_NAME<br>";
					} 
				}
				if ($SKILL12) {
					$arr_temp = explode("เน้นทาง", $SKILL12);
					$SS_NAME = trim($arr_temp[0]);
					$arr_temp = explode("เน้น", $SS_NAME);
					$SS_NAME = trim($arr_temp[0]);
					$SPS_EMPHASIZE = "เน้นทาง".$arr_temp[1];

					$SS_CODE = "";
					if ($SS_NAME=="ด้านการแพทยศาสตร์" || $SS_NAME=="ด้านการแพทย์ศาสตร์" || $SS_NAME=="ด้านแพทยศาสตร์" || $SS_NAME=="ด้านการแพทย์" || $SS_NAME=="ด้านทันตแพทยศาสตร์" || $SS_NAME=="ด้านเภสัชศาสตร์" || $SS_NAME=="ด้านสาธารณสุขศาสตร์และวิทยาศาสตร์การแพทย์" || $SS_NAME=="ด้านการแพทยศาสตร์และสาธารณสุข" || $SS_NAME=="ด้านพยาบาลศาสตร์" || $SS_NAME=="ด้านการแพทยศาสตร์ 1 (ด้านอายุรกรรมกุมารเวชกรรม จักษุ โต ศอ นาสิก ประสาทวิทยาและจิตเวช พยาธิตจวิทยา)" || $SS_NAME=="ด้านแพทยศาสตร์ 3 (ด้านเวชกรรมป้องกัน สาธารณสุข เวชกรรมฟื้นฟู)" || $SS_NAME=="การแพทยศาสตร์ 3(ด้านเวชกรรมป้องกัน สาธารณสุข เวชกรรมฟื้นฟู)" || $SS_NAME=="ด้านสาธารณสุขศาสาตร์และวิทยาศาสตร์การแพทย์" || $SS_NAME=="ด้านการแพทย์และสาธารสุข") $SS_CODE = "002";
					elseif ($SS_NAME=="ด้านการเมือง การปกครอง" || $SS_NAME=="ด้านการปกครองการเมือง") $SS_CODE = "011";
					elseif ($SS_NAME=="ด้านการเกษตร" || $SS_NAME=="ด้านการเกษตร (ด้านดิน ปุ๋ย พืชและอื่น ๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านการเกษตร (ด้านดิน ปุ๋ย พืช และอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านการเกษตร (ด้านสัตว์บก สัตว์น้ำ และอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านการเกษตร (ด้านสัตว์บก สัตว์น้ำ และอื่นๆที่เกี่ยวข้อง)" || $SS_NAME=="ด้านเกษตร การชลประทาน" || $SS_NAME=="ด้านส่งเสริมการเกษตร และด้านบริหารจัดการศัตรูพืช" || $SS_NAME=="ด้านเกษตร/ข้าว การอารักขาพืช") $SS_CODE = "003";
					elseif ($SS_NAME=="ด้านสังคม ชุมชน แรงงาน" || $SS_NAME=="ด้านสังคมชุมชนแรงงาน") $SS_CODE = "009";
					elseif ($SS_NAME=="ด้านวิทยาศาสตร์" || $SS_NAME=="ด้านเทคโนโลยี") $SS_CODE = "005";
					elseif ($SS_NAME=="ด้านการบริหาร" || $SS_NAME=="ด้านบริหารจัดการและบริหารธุรกิจ") $SS_CODE = "016";
					elseif ($SS_NAME=="ด้านการเงินการคลังงบประมาณ" || $SS_NAME=="ด้านเศรษฐศาสตร์และการคลัง" || $SS_NAME=="ด้านการเงินและการบัญชี" || $SS_NAME=="ด้านการเงิน การคลัง งบประมาณ กองคลัง") $SS_CODE = "008";
					elseif ($SS_NAME=="ด้านวิศวกรรมศาสตร์" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธา และอื่น ๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธาและอื่น ๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านเครื่องกล ไฟฟ้า เหมืองแร่ ปิโตรเลียม โลหการ อุตสาหกรรม)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธา และด้านอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธา และอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรม (ด้านโยธาและอื่น ๆ ที่เกี่ยวขัอง)") $SS_CODE = "006";
					elseif ($SS_NAME=="ด้านนโยบายและแผน" || $SS_NAME=="ด้านการพัฒนาทรัพยากรบุคคล" || $SS_NAME=="ด้านการวางแผน" || $SS_NAME=="ด้านวางแผนพัฒนา" || $SS_NAME=="ด้านการวางแผนและพัฒนา") $SS_CODE = "013";
					elseif ($SS_NAME=="ด้านอุตุนิยมวิทยา" || $SS_NAME=="ด้านธรรมชาติและสิ่งแวดล้อม" || $SS_NAME=="ด้านทรัพยากรธรรมชาติและสาธารณสุข") $SS_CODE = "004";
					elseif ($SS_NAME=="ด้านการศึกษา" || $SS_NAME=="ด้านการศึกษา ศิลปศาสตร์ และสังคมศาสตร์" || $SS_NAME=="ด้านการศึกษาและการวางแผนการศึกษานอกระบบ") $SS_CODE = "001";
					elseif ($SS_NAME=="ด้านสถาปัตยกรรมศาสตร์") $SS_CODE = "007";
					elseif ($SS_NAME=="ด้านภาษา") $SS_CODE = "012";
					elseif ($SS_NAME=="ด้านการพัฒนาอุตสาหกรรม") $SS_CODE = "021";
					elseif ($SS_NAME=="ด้านการแพทยศาสตร์") $SS_CODE = "011";
					elseif ($SS_NAME=="ด้านการแพทยศาสตร์") $SS_CODE = "011";

					$cmd = " select SS_CODE from PER_SPECIAL_SKILLGRP where SS_NAME = '$SS_NAME' ";
					$count_data = $db_dpis2->send_cmd($cmd);
					if ($count_data || $SS_CODE) {			
						$data2 = $db_dpis2->get_array();
						$SS_CODE = trim($data2[SS_CODE]);
						$cmd = " INSERT INTO PER_SPECIAL_SKILL (SPS_ID, PER_ID, PER_CARDNO, SS_CODE, SPS_EMPHASIZE, SPS_REMARK, UPDATE_USER, UPDATE_DATE)
										VALUES ($MAX_ID, $PER_ID, '$PER_CARDNO', '$SS_CODE', '$SPS_EMPHASIZE', '$SKILL11', $UPDATE_USER, '$UPDATE_DATE') ";
		//				$db_dpis->send_cmd($cmd);
		//				$db_dpis->show_error();
//						echo "$cmd<br>";
						$MAX_ID++;
					} else {
						echo "$SS_NAME<br>";
					} 
				}
				if ($SKILL13) {
					$arr_temp = explode("เน้นทาง", $SKILL13);
					$SS_NAME = trim($arr_temp[0]);
					$arr_temp = explode("เน้น", $SS_NAME);
					$SS_NAME = trim($arr_temp[0]);
					$SPS_EMPHASIZE = "เน้นทาง".$arr_temp[1];

					$SS_CODE = "";
					if ($SS_NAME=="ด้านการแพทยศาสตร์" || $SS_NAME=="ด้านการแพทย์ศาสตร์" || $SS_NAME=="ด้านแพทยศาสตร์" || $SS_NAME=="ด้านการแพทย์" || $SS_NAME=="ด้านทันตแพทยศาสตร์" || $SS_NAME=="ด้านเภสัชศาสตร์" || $SS_NAME=="ด้านสาธารณสุขศาสตร์และวิทยาศาสตร์การแพทย์" || $SS_NAME=="ด้านการแพทยศาสตร์และสาธารณสุข" || $SS_NAME=="ด้านพยาบาลศาสตร์" || $SS_NAME=="ด้านการแพทยศาสตร์ 1 (ด้านอายุรกรรมกุมารเวชกรรม จักษุ โต ศอ นาสิก ประสาทวิทยาและจิตเวช พยาธิตจวิทยา)" || $SS_NAME=="ด้านแพทยศาสตร์ 3 (ด้านเวชกรรมป้องกัน สาธารณสุข เวชกรรมฟื้นฟู)" || $SS_NAME=="การแพทยศาสตร์ 3(ด้านเวชกรรมป้องกัน สาธารณสุข เวชกรรมฟื้นฟู)" || $SS_NAME=="ด้านสาธารณสุขศาสาตร์และวิทยาศาสตร์การแพทย์" || $SS_NAME=="ด้านการแพทย์และสาธารสุข") $SS_CODE = "002";
					elseif ($SS_NAME=="ด้านการเมือง การปกครอง" || $SS_NAME=="ด้านการปกครองการเมือง") $SS_CODE = "011";
					elseif ($SS_NAME=="ด้านการเกษตร" || $SS_NAME=="ด้านการเกษตร (ด้านดิน ปุ๋ย พืชและอื่น ๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านการเกษตร (ด้านดิน ปุ๋ย พืช และอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านการเกษตร (ด้านสัตว์บก สัตว์น้ำ และอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านการเกษตร (ด้านสัตว์บก สัตว์น้ำ และอื่นๆที่เกี่ยวข้อง)" || $SS_NAME=="ด้านเกษตร การชลประทาน" || $SS_NAME=="ด้านส่งเสริมการเกษตร และด้านบริหารจัดการศัตรูพืช" || $SS_NAME=="ด้านเกษตร/ข้าว การอารักขาพืช") $SS_CODE = "003";
					elseif ($SS_NAME=="ด้านสังคม ชุมชน แรงงาน" || $SS_NAME=="ด้านสังคมชุมชนแรงงาน") $SS_CODE = "009";
					elseif ($SS_NAME=="ด้านวิทยาศาสตร์" || $SS_NAME=="ด้านเทคโนโลยี") $SS_CODE = "005";
					elseif ($SS_NAME=="ด้านการบริหาร" || $SS_NAME=="ด้านบริหารจัดการและบริหารธุรกิจ") $SS_CODE = "016";
					elseif ($SS_NAME=="ด้านการเงินการคลังงบประมาณ" || $SS_NAME=="ด้านเศรษฐศาสตร์และการคลัง" || $SS_NAME=="ด้านการเงินและการบัญชี" || $SS_NAME=="ด้านการเงิน การคลัง งบประมาณ กองคลัง") $SS_CODE = "008";
					elseif ($SS_NAME=="ด้านวิศวกรรมศาสตร์" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธา และอื่น ๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธาและอื่น ๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านเครื่องกล ไฟฟ้า เหมืองแร่ ปิโตรเลียม โลหการ อุตสาหกรรม)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธา และด้านอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธา และอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรม (ด้านโยธาและอื่น ๆ ที่เกี่ยวขัอง)") $SS_CODE = "006";
					elseif ($SS_NAME=="ด้านนโยบายและแผน" || $SS_NAME=="ด้านการพัฒนาทรัพยากรบุคคล" || $SS_NAME=="ด้านการวางแผน" || $SS_NAME=="ด้านวางแผนพัฒนา" || $SS_NAME=="ด้านการวางแผนและพัฒนา") $SS_CODE = "013";
					elseif ($SS_NAME=="ด้านอุตุนิยมวิทยา" || $SS_NAME=="ด้านธรรมชาติและสิ่งแวดล้อม" || $SS_NAME=="ด้านทรัพยากรธรรมชาติและสาธารณสุข") $SS_CODE = "004";
					elseif ($SS_NAME=="ด้านการศึกษา" || $SS_NAME=="ด้านการศึกษา ศิลปศาสตร์ และสังคมศาสตร์" || $SS_NAME=="ด้านการศึกษาและการวางแผนการศึกษานอกระบบ") $SS_CODE = "001";
					elseif ($SS_NAME=="ด้านสถาปัตยกรรมศาสตร์") $SS_CODE = "007";
					elseif ($SS_NAME=="ด้านภาษา") $SS_CODE = "012";
					elseif ($SS_NAME=="ด้านการพัฒนาอุตสาหกรรม") $SS_CODE = "021";
					elseif ($SS_NAME=="ด้านการแพทยศาสตร์") $SS_CODE = "011";
					elseif ($SS_NAME=="ด้านการแพทยศาสตร์") $SS_CODE = "011";

					$cmd = " select SS_CODE from PER_SPECIAL_SKILLGRP where SS_NAME = '$SS_NAME' ";
					$count_data = $db_dpis2->send_cmd($cmd);
					if ($count_data || $SS_CODE) {			
						$data2 = $db_dpis2->get_array();
						$SS_CODE = trim($data2[SS_CODE]);
						$cmd = " INSERT INTO PER_SPECIAL_SKILL (SPS_ID, PER_ID, PER_CARDNO, SS_CODE, SPS_EMPHASIZE, SPS_REMARK, UPDATE_USER, UPDATE_DATE)
										VALUES ($MAX_ID, $PER_ID, '$PER_CARDNO', '$SS_CODE', '$SPS_EMPHASIZE', '$SKILL11', $UPDATE_USER, '$UPDATE_DATE') ";
		//				$db_dpis->send_cmd($cmd);
		//				$db_dpis->show_error();
//						echo "$cmd<br>";
						$MAX_ID++;
					} else {
						echo "$SS_NAME<br>";
					} 
				}
				if ($SKILL14) {
					$arr_temp = explode("เน้นทาง", $SKILL14);
					$SS_NAME = trim($arr_temp[0]);
					$arr_temp = explode("เน้น", $SS_NAME);
					$SS_NAME = trim($arr_temp[0]);
					$SPS_EMPHASIZE = "เน้นทาง".$arr_temp[1];

					$SS_CODE = "";
					if ($SS_NAME=="ด้านการแพทยศาสตร์" || $SS_NAME=="ด้านการแพทย์ศาสตร์" || $SS_NAME=="ด้านแพทยศาสตร์" || $SS_NAME=="ด้านการแพทย์" || $SS_NAME=="ด้านทันตแพทยศาสตร์" || $SS_NAME=="ด้านเภสัชศาสตร์" || $SS_NAME=="ด้านสาธารณสุขศาสตร์และวิทยาศาสตร์การแพทย์" || $SS_NAME=="ด้านการแพทยศาสตร์และสาธารณสุข" || $SS_NAME=="ด้านพยาบาลศาสตร์" || $SS_NAME=="ด้านการแพทยศาสตร์ 1 (ด้านอายุรกรรมกุมารเวชกรรม จักษุ โต ศอ นาสิก ประสาทวิทยาและจิตเวช พยาธิตจวิทยา)" || $SS_NAME=="ด้านแพทยศาสตร์ 3 (ด้านเวชกรรมป้องกัน สาธารณสุข เวชกรรมฟื้นฟู)" || $SS_NAME=="การแพทยศาสตร์ 3(ด้านเวชกรรมป้องกัน สาธารณสุข เวชกรรมฟื้นฟู)" || $SS_NAME=="ด้านสาธารณสุขศาสาตร์และวิทยาศาสตร์การแพทย์" || $SS_NAME=="ด้านการแพทย์และสาธารสุข") $SS_CODE = "002";
					elseif ($SS_NAME=="ด้านการเมือง การปกครอง" || $SS_NAME=="ด้านการปกครองการเมือง") $SS_CODE = "011";
					elseif ($SS_NAME=="ด้านการเกษตร" || $SS_NAME=="ด้านการเกษตร (ด้านดิน ปุ๋ย พืชและอื่น ๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านการเกษตร (ด้านดิน ปุ๋ย พืช และอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านการเกษตร (ด้านสัตว์บก สัตว์น้ำ และอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านการเกษตร (ด้านสัตว์บก สัตว์น้ำ และอื่นๆที่เกี่ยวข้อง)" || $SS_NAME=="ด้านเกษตร การชลประทาน" || $SS_NAME=="ด้านส่งเสริมการเกษตร และด้านบริหารจัดการศัตรูพืช" || $SS_NAME=="ด้านเกษตร/ข้าว การอารักขาพืช") $SS_CODE = "003";
					elseif ($SS_NAME=="ด้านสังคม ชุมชน แรงงาน" || $SS_NAME=="ด้านสังคมชุมชนแรงงาน") $SS_CODE = "009";
					elseif ($SS_NAME=="ด้านวิทยาศาสตร์" || $SS_NAME=="ด้านเทคโนโลยี") $SS_CODE = "005";
					elseif ($SS_NAME=="ด้านการบริหาร" || $SS_NAME=="ด้านบริหารจัดการและบริหารธุรกิจ") $SS_CODE = "016";
					elseif ($SS_NAME=="ด้านการเงินการคลังงบประมาณ" || $SS_NAME=="ด้านเศรษฐศาสตร์และการคลัง" || $SS_NAME=="ด้านการเงินและการบัญชี" || $SS_NAME=="ด้านการเงิน การคลัง งบประมาณ กองคลัง") $SS_CODE = "008";
					elseif ($SS_NAME=="ด้านวิศวกรรมศาสตร์" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธา และอื่น ๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธาและอื่น ๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านเครื่องกล ไฟฟ้า เหมืองแร่ ปิโตรเลียม โลหการ อุตสาหกรรม)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธา และด้านอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธา และอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรม (ด้านโยธาและอื่น ๆ ที่เกี่ยวขัอง)") $SS_CODE = "006";
					elseif ($SS_NAME=="ด้านนโยบายและแผน" || $SS_NAME=="ด้านการพัฒนาทรัพยากรบุคคล" || $SS_NAME=="ด้านการวางแผน" || $SS_NAME=="ด้านวางแผนพัฒนา" || $SS_NAME=="ด้านการวางแผนและพัฒนา") $SS_CODE = "013";
					elseif ($SS_NAME=="ด้านอุตุนิยมวิทยา" || $SS_NAME=="ด้านธรรมชาติและสิ่งแวดล้อม" || $SS_NAME=="ด้านทรัพยากรธรรมชาติและสาธารณสุข") $SS_CODE = "004";
					elseif ($SS_NAME=="ด้านการศึกษา" || $SS_NAME=="ด้านการศึกษา ศิลปศาสตร์ และสังคมศาสตร์" || $SS_NAME=="ด้านการศึกษาและการวางแผนการศึกษานอกระบบ") $SS_CODE = "001";
					elseif ($SS_NAME=="ด้านสถาปัตยกรรมศาสตร์") $SS_CODE = "007";
					elseif ($SS_NAME=="ด้านภาษา") $SS_CODE = "012";
					elseif ($SS_NAME=="ด้านการพัฒนาอุตสาหกรรม") $SS_CODE = "021";
					elseif ($SS_NAME=="ด้านการแพทยศาสตร์") $SS_CODE = "011";
					elseif ($SS_NAME=="ด้านการแพทยศาสตร์") $SS_CODE = "011";

					$cmd = " select SS_CODE from PER_SPECIAL_SKILLGRP where SS_NAME = '$SS_NAME' ";
					$count_data = $db_dpis2->send_cmd($cmd);
					if ($count_data || $SS_CODE) {			
						$data2 = $db_dpis2->get_array();
						$SS_CODE = trim($data2[SS_CODE]);
						$cmd = " INSERT INTO PER_SPECIAL_SKILL (SPS_ID, PER_ID, PER_CARDNO, SS_CODE, SPS_EMPHASIZE, SPS_REMARK, UPDATE_USER, UPDATE_DATE)
										VALUES ($MAX_ID, $PER_ID, '$PER_CARDNO', '$SS_CODE', '$SPS_EMPHASIZE', '$SKILL11', $UPDATE_USER, '$UPDATE_DATE') ";
		//				$db_dpis->send_cmd($cmd);
		//				$db_dpis->show_error();
//						echo "$cmd<br>";
						$MAX_ID++;
					} else {
						echo "$SS_NAME<br>";
					} 
				}
				if ($SKILL15) {
					$arr_temp = explode("เน้นทาง", $SKILL15);
					$SS_NAME = trim($arr_temp[0]);
					$arr_temp = explode("เน้น", $SS_NAME);
					$SS_NAME = trim($arr_temp[0]);
					$SPS_EMPHASIZE = "เน้นทาง".$arr_temp[1];

					$SS_CODE = "";
					if ($SS_NAME=="ด้านการแพทยศาสตร์" || $SS_NAME=="ด้านการแพทย์ศาสตร์" || $SS_NAME=="ด้านแพทยศาสตร์" || $SS_NAME=="ด้านการแพทย์" || $SS_NAME=="ด้านทันตแพทยศาสตร์" || $SS_NAME=="ด้านเภสัชศาสตร์" || $SS_NAME=="ด้านสาธารณสุขศาสตร์และวิทยาศาสตร์การแพทย์" || $SS_NAME=="ด้านการแพทยศาสตร์และสาธารณสุข" || $SS_NAME=="ด้านพยาบาลศาสตร์" || $SS_NAME=="ด้านการแพทยศาสตร์ 1 (ด้านอายุรกรรมกุมารเวชกรรม จักษุ โต ศอ นาสิก ประสาทวิทยาและจิตเวช พยาธิตจวิทยา)" || $SS_NAME=="ด้านแพทยศาสตร์ 3 (ด้านเวชกรรมป้องกัน สาธารณสุข เวชกรรมฟื้นฟู)" || $SS_NAME=="การแพทยศาสตร์ 3(ด้านเวชกรรมป้องกัน สาธารณสุข เวชกรรมฟื้นฟู)" || $SS_NAME=="ด้านสาธารณสุขศาสาตร์และวิทยาศาสตร์การแพทย์" || $SS_NAME=="ด้านการแพทย์และสาธารสุข") $SS_CODE = "002";
					elseif ($SS_NAME=="ด้านการเมือง การปกครอง" || $SS_NAME=="ด้านการปกครองการเมือง") $SS_CODE = "011";
					elseif ($SS_NAME=="ด้านการเกษตร" || $SS_NAME=="ด้านการเกษตร (ด้านดิน ปุ๋ย พืชและอื่น ๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านการเกษตร (ด้านดิน ปุ๋ย พืช และอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านการเกษตร (ด้านสัตว์บก สัตว์น้ำ และอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านการเกษตร (ด้านสัตว์บก สัตว์น้ำ และอื่นๆที่เกี่ยวข้อง)" || $SS_NAME=="ด้านเกษตร การชลประทาน" || $SS_NAME=="ด้านส่งเสริมการเกษตร และด้านบริหารจัดการศัตรูพืช" || $SS_NAME=="ด้านเกษตร/ข้าว การอารักขาพืช") $SS_CODE = "003";
					elseif ($SS_NAME=="ด้านสังคม ชุมชน แรงงาน" || $SS_NAME=="ด้านสังคมชุมชนแรงงาน") $SS_CODE = "009";
					elseif ($SS_NAME=="ด้านวิทยาศาสตร์" || $SS_NAME=="ด้านเทคโนโลยี") $SS_CODE = "005";
					elseif ($SS_NAME=="ด้านการบริหาร" || $SS_NAME=="ด้านบริหารจัดการและบริหารธุรกิจ") $SS_CODE = "016";
					elseif ($SS_NAME=="ด้านการเงินการคลังงบประมาณ" || $SS_NAME=="ด้านเศรษฐศาสตร์และการคลัง" || $SS_NAME=="ด้านการเงินและการบัญชี" || $SS_NAME=="ด้านการเงิน การคลัง งบประมาณ กองคลัง") $SS_CODE = "008";
					elseif ($SS_NAME=="ด้านวิศวกรรมศาสตร์" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธา และอื่น ๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธาและอื่น ๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านเครื่องกล ไฟฟ้า เหมืองแร่ ปิโตรเลียม โลหการ อุตสาหกรรม)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธา และด้านอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธา และอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรม (ด้านโยธาและอื่น ๆ ที่เกี่ยวขัอง)") $SS_CODE = "006";
					elseif ($SS_NAME=="ด้านนโยบายและแผน" || $SS_NAME=="ด้านการพัฒนาทรัพยากรบุคคล" || $SS_NAME=="ด้านการวางแผน" || $SS_NAME=="ด้านวางแผนพัฒนา" || $SS_NAME=="ด้านการวางแผนและพัฒนา") $SS_CODE = "013";
					elseif ($SS_NAME=="ด้านอุตุนิยมวิทยา" || $SS_NAME=="ด้านธรรมชาติและสิ่งแวดล้อม" || $SS_NAME=="ด้านทรัพยากรธรรมชาติและสาธารณสุข") $SS_CODE = "004";
					elseif ($SS_NAME=="ด้านการศึกษา" || $SS_NAME=="ด้านการศึกษา ศิลปศาสตร์ และสังคมศาสตร์" || $SS_NAME=="ด้านการศึกษาและการวางแผนการศึกษานอกระบบ") $SS_CODE = "001";
					elseif ($SS_NAME=="ด้านสถาปัตยกรรมศาสตร์") $SS_CODE = "007";
					elseif ($SS_NAME=="ด้านภาษา") $SS_CODE = "012";
					elseif ($SS_NAME=="ด้านการพัฒนาอุตสาหกรรม") $SS_CODE = "021";
					elseif ($SS_NAME=="ด้านการแพทยศาสตร์") $SS_CODE = "011";
					elseif ($SS_NAME=="ด้านการแพทยศาสตร์") $SS_CODE = "011";

					$cmd = " select SS_CODE from PER_SPECIAL_SKILLGRP where SS_NAME = '$SS_NAME' ";
					$count_data = $db_dpis2->send_cmd($cmd);
					if ($count_data || $SS_CODE) {			
						$data2 = $db_dpis2->get_array();
						$SS_CODE = trim($data2[SS_CODE]);
						$cmd = " INSERT INTO PER_SPECIAL_SKILL (SPS_ID, PER_ID, PER_CARDNO, SS_CODE, SPS_EMPHASIZE, SPS_REMARK, UPDATE_USER, UPDATE_DATE)
										VALUES ($MAX_ID, $PER_ID, '$PER_CARDNO', '$SS_CODE', '$SPS_EMPHASIZE', '$SKILL11', $UPDATE_USER, '$UPDATE_DATE') ";
		//				$db_dpis->send_cmd($cmd);
		//				$db_dpis->show_error();
//						echo "$cmd<br>";
						$MAX_ID++;
					} else {
						echo "$SS_NAME<br>";
					} 
				}
				if ($SKILL16) {
					$arr_temp = explode("เน้นทาง", $SKILL16);
					$SS_NAME = trim($arr_temp[0]);
					$arr_temp = explode("เน้น", $SS_NAME);
					$SS_NAME = trim($arr_temp[0]);
					$SPS_EMPHASIZE = "เน้นทาง".$arr_temp[1];

					$SS_CODE = "";
					if ($SS_NAME=="ด้านการแพทยศาสตร์" || $SS_NAME=="ด้านการแพทย์ศาสตร์" || $SS_NAME=="ด้านแพทยศาสตร์" || $SS_NAME=="ด้านการแพทย์" || $SS_NAME=="ด้านทันตแพทยศาสตร์" || $SS_NAME=="ด้านเภสัชศาสตร์" || $SS_NAME=="ด้านสาธารณสุขศาสตร์และวิทยาศาสตร์การแพทย์" || $SS_NAME=="ด้านการแพทยศาสตร์และสาธารณสุข" || $SS_NAME=="ด้านพยาบาลศาสตร์" || $SS_NAME=="ด้านการแพทยศาสตร์ 1 (ด้านอายุรกรรมกุมารเวชกรรม จักษุ โต ศอ นาสิก ประสาทวิทยาและจิตเวช พยาธิตจวิทยา)" || $SS_NAME=="ด้านแพทยศาสตร์ 3 (ด้านเวชกรรมป้องกัน สาธารณสุข เวชกรรมฟื้นฟู)" || $SS_NAME=="การแพทยศาสตร์ 3(ด้านเวชกรรมป้องกัน สาธารณสุข เวชกรรมฟื้นฟู)" || $SS_NAME=="ด้านสาธารณสุขศาสาตร์และวิทยาศาสตร์การแพทย์" || $SS_NAME=="ด้านการแพทย์และสาธารสุข") $SS_CODE = "002";
					elseif ($SS_NAME=="ด้านการเมือง การปกครอง" || $SS_NAME=="ด้านการปกครองการเมือง") $SS_CODE = "011";
					elseif ($SS_NAME=="ด้านการเกษตร" || $SS_NAME=="ด้านการเกษตร (ด้านดิน ปุ๋ย พืชและอื่น ๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านการเกษตร (ด้านดิน ปุ๋ย พืช และอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านการเกษตร (ด้านสัตว์บก สัตว์น้ำ และอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านการเกษตร (ด้านสัตว์บก สัตว์น้ำ และอื่นๆที่เกี่ยวข้อง)" || $SS_NAME=="ด้านเกษตร การชลประทาน" || $SS_NAME=="ด้านส่งเสริมการเกษตร และด้านบริหารจัดการศัตรูพืช" || $SS_NAME=="ด้านเกษตร/ข้าว การอารักขาพืช") $SS_CODE = "003";
					elseif ($SS_NAME=="ด้านสังคม ชุมชน แรงงาน" || $SS_NAME=="ด้านสังคมชุมชนแรงงาน") $SS_CODE = "009";
					elseif ($SS_NAME=="ด้านวิทยาศาสตร์" || $SS_NAME=="ด้านเทคโนโลยี") $SS_CODE = "005";
					elseif ($SS_NAME=="ด้านการบริหาร" || $SS_NAME=="ด้านบริหารจัดการและบริหารธุรกิจ") $SS_CODE = "016";
					elseif ($SS_NAME=="ด้านการเงินการคลังงบประมาณ" || $SS_NAME=="ด้านเศรษฐศาสตร์และการคลัง" || $SS_NAME=="ด้านการเงินและการบัญชี" || $SS_NAME=="ด้านการเงิน การคลัง งบประมาณ กองคลัง") $SS_CODE = "008";
					elseif ($SS_NAME=="ด้านวิศวกรรมศาสตร์" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธา และอื่น ๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธาและอื่น ๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านเครื่องกล ไฟฟ้า เหมืองแร่ ปิโตรเลียม โลหการ อุตสาหกรรม)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธา และด้านอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธา และอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรม (ด้านโยธาและอื่น ๆ ที่เกี่ยวขัอง)") $SS_CODE = "006";
					elseif ($SS_NAME=="ด้านนโยบายและแผน" || $SS_NAME=="ด้านการพัฒนาทรัพยากรบุคคล" || $SS_NAME=="ด้านการวางแผน" || $SS_NAME=="ด้านวางแผนพัฒนา" || $SS_NAME=="ด้านการวางแผนและพัฒนา") $SS_CODE = "013";
					elseif ($SS_NAME=="ด้านอุตุนิยมวิทยา" || $SS_NAME=="ด้านธรรมชาติและสิ่งแวดล้อม" || $SS_NAME=="ด้านทรัพยากรธรรมชาติและสาธารณสุข") $SS_CODE = "004";
					elseif ($SS_NAME=="ด้านการศึกษา" || $SS_NAME=="ด้านการศึกษา ศิลปศาสตร์ และสังคมศาสตร์" || $SS_NAME=="ด้านการศึกษาและการวางแผนการศึกษานอกระบบ") $SS_CODE = "001";
					elseif ($SS_NAME=="ด้านสถาปัตยกรรมศาสตร์") $SS_CODE = "007";
					elseif ($SS_NAME=="ด้านภาษา") $SS_CODE = "012";
					elseif ($SS_NAME=="ด้านการพัฒนาอุตสาหกรรม") $SS_CODE = "021";
					elseif ($SS_NAME=="ด้านการแพทยศาสตร์") $SS_CODE = "011";
					elseif ($SS_NAME=="ด้านการแพทยศาสตร์") $SS_CODE = "011";

					$cmd = " select SS_CODE from PER_SPECIAL_SKILLGRP where SS_NAME = '$SS_NAME' ";
					$count_data = $db_dpis2->send_cmd($cmd);
					if ($count_data || $SS_CODE) {			
						$data2 = $db_dpis2->get_array();
						$SS_CODE = trim($data2[SS_CODE]);
						$cmd = " INSERT INTO PER_SPECIAL_SKILL (SPS_ID, PER_ID, PER_CARDNO, SS_CODE, SPS_EMPHASIZE, SPS_REMARK, UPDATE_USER, UPDATE_DATE)
										VALUES ($MAX_ID, $PER_ID, '$PER_CARDNO', '$SS_CODE', '$SPS_EMPHASIZE', '$SKILL11', $UPDATE_USER, '$UPDATE_DATE') ";
		//				$db_dpis->send_cmd($cmd);
		//				$db_dpis->show_error();
//						echo "$cmd<br>";
						$MAX_ID++;
					} else {
						echo "$SS_NAME<br>";
					} 
				}
				if ($SKILL17) {
					$arr_temp = explode("เน้นทาง", $SKILL17);
					$SS_NAME = trim($arr_temp[0]);
					$arr_temp = explode("เน้น", $SS_NAME);
					$SS_NAME = trim($arr_temp[0]);
					$SPS_EMPHASIZE = "เน้นทาง".$arr_temp[1];

					$SS_CODE = "";
					if ($SS_NAME=="ด้านการแพทยศาสตร์" || $SS_NAME=="ด้านการแพทย์ศาสตร์" || $SS_NAME=="ด้านแพทยศาสตร์" || $SS_NAME=="ด้านการแพทย์" || $SS_NAME=="ด้านทันตแพทยศาสตร์" || $SS_NAME=="ด้านเภสัชศาสตร์" || $SS_NAME=="ด้านสาธารณสุขศาสตร์และวิทยาศาสตร์การแพทย์" || $SS_NAME=="ด้านการแพทยศาสตร์และสาธารณสุข" || $SS_NAME=="ด้านพยาบาลศาสตร์" || $SS_NAME=="ด้านการแพทยศาสตร์ 1 (ด้านอายุรกรรมกุมารเวชกรรม จักษุ โต ศอ นาสิก ประสาทวิทยาและจิตเวช พยาธิตจวิทยา)" || $SS_NAME=="ด้านแพทยศาสตร์ 3 (ด้านเวชกรรมป้องกัน สาธารณสุข เวชกรรมฟื้นฟู)" || $SS_NAME=="การแพทยศาสตร์ 3(ด้านเวชกรรมป้องกัน สาธารณสุข เวชกรรมฟื้นฟู)" || $SS_NAME=="ด้านสาธารณสุขศาสาตร์และวิทยาศาสตร์การแพทย์" || $SS_NAME=="ด้านการแพทย์และสาธารสุข") $SS_CODE = "002";
					elseif ($SS_NAME=="ด้านการเมือง การปกครอง" || $SS_NAME=="ด้านการปกครองการเมือง") $SS_CODE = "011";
					elseif ($SS_NAME=="ด้านการเกษตร" || $SS_NAME=="ด้านการเกษตร (ด้านดิน ปุ๋ย พืชและอื่น ๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านการเกษตร (ด้านดิน ปุ๋ย พืช และอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านการเกษตร (ด้านสัตว์บก สัตว์น้ำ และอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านการเกษตร (ด้านสัตว์บก สัตว์น้ำ และอื่นๆที่เกี่ยวข้อง)" || $SS_NAME=="ด้านเกษตร การชลประทาน" || $SS_NAME=="ด้านส่งเสริมการเกษตร และด้านบริหารจัดการศัตรูพืช" || $SS_NAME=="ด้านเกษตร/ข้าว การอารักขาพืช") $SS_CODE = "003";
					elseif ($SS_NAME=="ด้านสังคม ชุมชน แรงงาน" || $SS_NAME=="ด้านสังคมชุมชนแรงงาน") $SS_CODE = "009";
					elseif ($SS_NAME=="ด้านวิทยาศาสตร์" || $SS_NAME=="ด้านเทคโนโลยี") $SS_CODE = "005";
					elseif ($SS_NAME=="ด้านการบริหาร" || $SS_NAME=="ด้านบริหารจัดการและบริหารธุรกิจ") $SS_CODE = "016";
					elseif ($SS_NAME=="ด้านการเงินการคลังงบประมาณ" || $SS_NAME=="ด้านเศรษฐศาสตร์และการคลัง" || $SS_NAME=="ด้านการเงินและการบัญชี" || $SS_NAME=="ด้านการเงิน การคลัง งบประมาณ กองคลัง") $SS_CODE = "008";
					elseif ($SS_NAME=="ด้านวิศวกรรมศาสตร์" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธา และอื่น ๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธาและอื่น ๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านเครื่องกล ไฟฟ้า เหมืองแร่ ปิโตรเลียม โลหการ อุตสาหกรรม)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธา และด้านอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรมศาสตร์ (ด้านโยธา และอื่นๆ ที่เกี่ยวข้อง)" || $SS_NAME=="ด้านวิศวกรรม (ด้านโยธาและอื่น ๆ ที่เกี่ยวขัอง)") $SS_CODE = "006";
					elseif ($SS_NAME=="ด้านนโยบายและแผน" || $SS_NAME=="ด้านการพัฒนาทรัพยากรบุคคล" || $SS_NAME=="ด้านการวางแผน" || $SS_NAME=="ด้านวางแผนพัฒนา" || $SS_NAME=="ด้านการวางแผนและพัฒนา") $SS_CODE = "013";
					elseif ($SS_NAME=="ด้านอุตุนิยมวิทยา" || $SS_NAME=="ด้านธรรมชาติและสิ่งแวดล้อม" || $SS_NAME=="ด้านทรัพยากรธรรมชาติและสาธารณสุข") $SS_CODE = "004";
					elseif ($SS_NAME=="ด้านการศึกษา" || $SS_NAME=="ด้านการศึกษา ศิลปศาสตร์ และสังคมศาสตร์" || $SS_NAME=="ด้านการศึกษาและการวางแผนการศึกษานอกระบบ") $SS_CODE = "001";
					elseif ($SS_NAME=="ด้านสถาปัตยกรรมศาสตร์") $SS_CODE = "007";
					elseif ($SS_NAME=="ด้านภาษา") $SS_CODE = "012";
					elseif ($SS_NAME=="ด้านการพัฒนาอุตสาหกรรม") $SS_CODE = "021";
					elseif ($SS_NAME=="ด้านการแพทยศาสตร์") $SS_CODE = "011";
					elseif ($SS_NAME=="ด้านการแพทยศาสตร์") $SS_CODE = "011";

					$cmd = " select SS_CODE from PER_SPECIAL_SKILLGRP where SS_NAME = '$SS_NAME' ";
					$count_data = $db_dpis2->send_cmd($cmd);
					if ($count_data || $SS_CODE) {			
						$data2 = $db_dpis2->get_array();
						$SS_CODE = trim($data2[SS_CODE]);
						$cmd = " INSERT INTO PER_SPECIAL_SKILL (SPS_ID, PER_ID, PER_CARDNO, SS_CODE, SPS_EMPHASIZE, SPS_REMARK, UPDATE_USER, UPDATE_DATE)
										VALUES ($MAX_ID, $PER_ID, '$PER_CARDNO', '$SS_CODE', '$SPS_EMPHASIZE', '$SKILL11', $UPDATE_USER, '$UPDATE_DATE') ";
		//				$db_dpis->send_cmd($cmd);
		//				$db_dpis->show_error();
//						echo "$cmd<br>";
						$MAX_ID++;
					} else {
						echo "$SS_NAME<br>";
					} 
				}
			} else {
//				echo "ชื่อ-สกุล $FULLNAME<br>";
			} 
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

	}

	if( $command=='POSITION' ) { // ตำแหน่งข้าราชการ  
		$table = array(	"per_personal" );
		for ( $i=0; $i<count($table); $i++ ) { 
			$cmd = " delete from $table[$i] where per_id in $where_per_id ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
		} // end for

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_POSITION' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		if ($search_org_id) 
			$cmd = " DELETE FROM PER_POSITION WHERE DEPARTMENT_ID = $search_department_id and ORG_ID = $search_org_id ";
		else
			$cmd = " DELETE FROM PER_POSITION WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT ORG_ID FROM PER_ORG  WHERE ORG_NAME = '$search_department_name' AND OL_CODE = '02' ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		$data = $db_dpis35->get_array();
		$DEPT_ID = $data[ORG_ID] + 0;
		if ($DEPT_ID==0) $DEPT_ID = $search_department_id;

		$cmd = " select max(POS_ID) as MAX_ID from PER_POSITION ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		if ($search_org_id) $where = " AND DIVISIONNAME = '$search_org_name' ";
		if(strtoupper($dpis35db_name)=="D03004" || strtoupper($dpis35db_name)=="D21002")
			$cmd = " SELECT DIVISIONNAME, TEMPPOSITIONNO, TEMPLINE, TEMPPOSITIONTYPE, TEMPLEVEL, 
							TEMPMANAGEPOSITION, TEMPSKILL, TEMPORGANIZETYPE, TEMPPROVINCE, TEMPPOSITIONSTATUS, 	TEMPPRENAME, TEMPFIRSTNAME, TEMPLASTNAME, TEMPSALARY, TEMPPOSITIONSALARY 
							FROM DPIS ORDER BY TEMPPOSITIONNO ";
		elseif(strtoupper($dpis35db_name)=="D03007")
			$cmd = " SELECT DIVISIONNAME, TEMPPOSITIONNO, TEMPLINE, TEMPPOSITIONTYPE, TEMPLEVEL, 
							TEMPMANAGEPOSITION, TEMPSKILL, TEMPORGANIZETYPE, TEMPPROVINCE, TEMPPOSITIONSTATUS, 	TEMPPRENAME, TEMPFIRSTNAME, TEMPLASTNAME, TEMPSALARY, TEMPPOSITIONSALARY, OLDLEVELRAN 
							FROM DPIS ORDER BY TEMPPOSITIONNO ";
		elseif($search_pv_code)
			$cmd = " SELECT DIVISIONNAME, TEMPPOSITIONNO, TEMPLINE, TEMPPOSITIONTYPE, TEMPLEVEL, 
							TEMPMANAGEPOSITION, TEMPSKILL, TEMPORGANIZETYPE, TEMPPROVINCE, TEMPPOSITIONSTATUS, 	TEMPPRENAME, TEMPFIRSTNAME, 	TEMPLASTNAME, TEMPSALARY, TEMPPOSITIONSALARY
							FROM DPIS WHERE TEMPPOSITIONNO IS NOT NULL $where AND TEMPPROVINCE = '$search_pv_name' ORDER BY TEMPPOSITIONNO ";
		else {
			$cmd = " SELECT DIVISIONNAME, TEMPPOSITIONNO, TEMPLINE, TEMPPOSITIONTYPE, TEMPLEVEL, 
							TEMPMANAGEPOSITION, TEMPSKILL, TEMPORGANIZETYPE, TEMPPROVINCE, TEMPPOSITIONSTATUS, 	TEMPPRENAME, TEMPFIRSTNAME, TEMPLASTNAME, TEMPSALARY, TEMPPOSITIONSALARY, TEMPCLNAME 
							FROM DPIS WHERE TEMPPOSITIONNO IS NOT NULL $where ORDER BY TEMPPOSITIONNO ";
			$count_data = $db_dpis35->send_cmd($cmd);
			if (!$count_data)
				$cmd = " SELECT DIVISIONNAME, TEMPPOSITIONNO, TEMPLINE, TEMPPOSITIONTYPE, TEMPLEVEL, 
								TEMPMANAGEPOSITION, TEMPSKILL, TEMPORGANIZETYPE, TEMPPROVINCE, TEMPPOSITIONSTATUS, 	TEMPPRENAME, TEMPFIRSTNAME, TEMPLASTNAME, TEMPSALARY, TEMPPOSITIONSALARY
								FROM DPIS WHERE TEMPPOSITIONNO IS NOT NULL $where ORDER BY TEMPPOSITIONNO ";
		}
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_POSITION++;
			$DIVISIONNAME = trim($data[DIVISIONNAME]);
			$POS_NO = $data[TEMPPOSITIONNO] + 0;
			$TEMPORGANIZETYPE = trim($data[TEMPORGANIZETYPE]);
			$TEMPMANAGEPOSITION = trim($data[TEMPMANAGEPOSITION]);
			$TEMPLINE = trim($data[TEMPLINE]);
			$LEVEL_NO = trim($data[TEMPLEVEL]);
			$POS_SALARY = $data[TEMPSALARY] + 0;
			$POS_MGTSALARY = $data[TEMPPOSITIONSALARY] + 0;
			$TEMPSKILL = trim($data[TEMPSKILL]);
			$TEMPPOSITIONTYPE = trim($data[TEMPPOSITIONTYPE]);
			$TEMPPOSITIONSTATUS = $data[TEMPPOSITIONSTATUS];
			if(strtoupper($dpis35db_name)=="D03007")
				$CL_NAME = $data[OLDLEVELRAN];
			else
				$CL_NAME = $data[TEMPCLNAME];
			$TEMPPRENAME = trim($data[TEMPPRENAME]);
			$TEMPFIRSTNAME = trim($data[TEMPFIRSTNAME]);
			$TEMPLASTNAME = trim($data[TEMPLASTNAME]);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$DIVISIONNAME' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_ID = $data2[NEW_CODE] + 0;
			$ORG_ID_1 = 'NULL';
			$ORG_ID_2 = 'NULL';

			$PM_CODE = '';
			if ($TEMPMANAGEPOSITION) {
				$cmd = " select NEW_CODE from PER_MAP_CODE 
								  where MAP_CODE = 'PER_MGT' AND OLD_CODE = '$TEMPMANAGEPOSITION' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $PM_CODE = trim($data2[NEW_CODE]);
				else echo "ชื่อตำแหน่งในการบริหารงาน $TEMPMANAGEPOSITION<br>";
			} 

			if ($TEMPLINE) {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_LINE' AND OLD_CODE = '$TEMPLINE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $PL_CODE = trim($data2[NEW_CODE]);
				else echo "ชื่อตำแหน่งในสายงาน $TEMPLINE<br>";
			} else $PL_CODE = "NULL";

			if ($TEMPPOSITIONTYPE) {
				$cmd = " select NEW_CODE from PER_MAP_CODE 
								  where MAP_CODE = 'PER_TYPE' AND OLD_CODE = '$TEMPPOSITIONTYPE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $PT_CODE = trim($data2[NEW_CODE]);
				else echo "ประเภทตำแหน่ง $TEMPPOSITIONTYPE<br>";
			} else $PT_CODE = "11";
			if ($TEMPPOSITIONTYPE=="ชช") $PT_CODE = "22"; 
			elseif ($TEMPPOSITIONTYPE=="บก") $PT_CODE = "31"; 
			elseif ($TEMPPOSITIONTYPE=="บส") $PT_CODE = "32"; 

			$SKILL_CODE = '';
			if ($TEMPSKILL) {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_SKILL' AND OLD_CODE = '$TEMPSKILL' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $SKILL_CODE = trim($data2[NEW_CODE]);
				else echo "สาขาความเชี่ยวชาญ $TEMPSKILL<br>";
			} 

				$POS_PER_NAME = $TEMPPRENAME . $TEMPFIRSTNAME . ' ' . $TEMPLASTNAME;
/*			} else {
				$cmd = " SELECT LEVEL_NO_MAX FROM PER_CO_LEVEL WHERE CL_NAME = '$CL_NAME' ";
				$count_data = $db_dpis352->send_cmd($cmd);
				//$db_dpis352->show_error();
				$data2 = $db_dpis352->get_array();
				$LEVEL_NO = trim($data2[LEVEL_NO_MAX]);
			} */
			if(strtoupper($dpis35db_name)=="D15003" || strtoupper($dpis35db_name)=="D10006" || strtoupper($dpis35db_name)=="D03005") {
				if ($LEVEL_NO=="S1" || $LEVEL_NO=="SES1") $LEVEL_NO = "M1";
				elseif ($LEVEL_NO=="S2" || $LEVEL_NO=="SES2") $LEVEL_NO = "M2";
				elseif ($LEVEL_NO=="M1") $LEVEL_NO = "D1";
				elseif ($LEVEL_NO=="M2") $LEVEL_NO = "D2"; 
			}

			if(strtoupper($dpis35db_name)=="D03004" || strtoupper($dpis35db_name)=="D21002") $CL_NAME = $LEVEL_NO;
			$cmd = " SELECT LV_NAME FROM PER_NEW_LEVEL WHERE LV_DESCRIPTION = '$LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[LV_NAME])) $LEVEL_NO = trim($data2[LV_NAME]);
			if ($LEVEL_NO=="o1") $LEVEL_NO = "O1";
			elseif ($LEVEL_NO=="o2") $LEVEL_NO = "O2";
			elseif ($LEVEL_NO=="o3" || $LEVEL_NO=="O3-1") $LEVEL_NO = "O3";
			elseif ($LEVEL_NO=="o4") $LEVEL_NO = "O4";

			$POS_STATUS = 1;

			if ($PM_CODE && $PM_CODE != 'NULL') $PM_CODE = "'$PM_CODE'";
			else $PM_CODE = "NULL";
			if ($SKILL_CODE && $SKILL_CODE != 'NULL') $SKILL_CODE = "'$SKILL_CODE'";
			else $SKILL_CODE = "NULL";
			if (!$CL_NAME) $CL_NAME = "ปฏิบัติการ";

			$cmd = " INSERT INTO PER_POSITION (POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, 
							PM_CODE, PL_CODE, CL_NAME, POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
							VALUES ($MAX_ID, $ORG_ID, '$POS_NO', '01', $ORG_ID_1, $ORG_ID_2, $PM_CODE, 
							'$PL_CODE', '$CL_NAME', $POS_SALARY, $POS_MGTSALARY, $SKILL_CODE, '$PT_CODE', $POS_STATUS, $UPDATE_USER, '$UPDATE_DATE', $search_department_id) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
//			if ($MAX_ID == 5981) echo "$cmd<br>";

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_POSITION', '$POS_NO', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			$MAX_ID++;
		} // end while						

		if ($search_org_id) 
			$cmd = " select count(POS_ID) as COUNT_NEW from PER_POSITION where DEPARTMENT_ID = $search_department_id and ORG_ID = $search_org_id ";
		else
			$cmd = " select count(POS_ID) as COUNT_NEW from PER_POSITION where DEPARTMENT_ID = $search_department_id ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_POSITION - $PER_POSITION - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
	} // end if

	if( $command=='PERSONAL' ) { // ข้อมูลข้าราชการ
		$table = array(	"per_personal" );
		for ( $i=0; $i<count($table); $i++ ) { 
			$cmd = " delete from $table[$i] where per_id in $where_per_id ";
			$db_dpis1->send_cmd($cmd);
			$db_dpis1->show_error();
		} // end for

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_PERSONAL' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;

		$cmd = " select max(PER_ID) as MAX_ID from PER_PERSONAL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		if ($search_org_id) $where = " AND DIVISIONNAME = '$search_org_name' ";
		if($search_pv_code)
			$cmd = " SELECT TEMPPRENAME, TEMPFIRSTNAME, TEMPLASTNAME, TEMPPOSITIONNO, TEMPLEVEL, TEMPSALARY, 
							TEMPPOSITIONSALARY, TEMPGENDER, TEMPCARDNO, TEMPBIRTHDATE, TEMPSTARTDATE, TEMPPROVINCE, 
							TEMPMOVEMENTTYPE 
							FROM DPIS WHERE TEMPFIRSTNAME IS NOT NULL $where AND TEMPPROVINCE = '$search_pv_name' ORDER BY TEMPPOSITIONNO ";
		else
			$cmd = " SELECT TEMPPRENAME, TEMPFIRSTNAME, TEMPLASTNAME, TEMPPOSITIONNO, TEMPLEVEL, TEMPSALARY, 
							TEMPPOSITIONSALARY, TEMPGENDER, TEMPCARDNO, TEMPBIRTHDATE, TEMPSTARTDATE, TEMPPROVINCE, 
							TEMPMOVEMENTTYPE 
							FROM DPIS WHERE TEMPFIRSTNAME IS NOT NULL $where ORDER BY TEMPPOSITIONNO ";
		$db_dpis35->send_cmd($cmd);
		//$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_PERSONAL++;
			$PER_TYPE = 1;
			$OT_CODE = '01';
			$TEMPPRENAME = trim($data[TEMPPRENAME]);
			$PER_NAME = trim($data[TEMPFIRSTNAME]);
			$PER_SURNAME = trim($data[TEMPLASTNAME]);
			$TEMPPOSITIONNO = $data[TEMPPOSITIONNO] + 0;
			$LEVEL_NO = trim($data[TEMPLEVEL]);
			$PER_SALARY = $data[TEMPSALARY] + 0;
			$PER_MGTSALARY = $data[TEMPPOSITIONSALARY] + 0;
			$TEMPGENDER = trim($data[TEMPGENDER]);
			if ($TEMPGENDER=='ชาย') $PER_GENDER = 1;
			elseif ($TEMPGENDER=='หญิง') $PER_GENDER = 2;
			$PER_CARDNO = trim($data[TEMPCARDNO]);
			$PER_BIRTHDATE = trim($data[TEMPBIRTHDATE]);
			$PER_STARTDATE = trim($data[TEMPSTARTDATE]);
			$TEMPPROVINCE = trim($data[TEMPPROVINCE]);
			$TEMPMOVEMENTTYPE = trim($data[TEMPMOVEMENTTYPE]);
			$PER_BIRTHDATE =  save_date($PER_BIRTHDATE);
			$PER_STARTDATE =  save_date($PER_STARTDATE);
			$temp_date = explode("/", trim($PER_STARTDATE));

			$cmd = " select NEW_CODE from PER_MAP_CODE 
							  where MAP_CODE = 'PER_PRENAME' AND OLD_CODE = '$TEMPPRENAME' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PN_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE 
							  where MAP_CODE = 'PER_POSITION' AND OLD_CODE = '$TEMPPOSITIONNO' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$POS_ID = $data2[NEW_CODE] + 0;

			$cmd = " select NEW_CODE from PER_MAP_CODE 
							  where MAP_CODE = 'PER_PROVINCE' AND OLD_CODE = '$TEMPPROVINCE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $PV_CODE = trim($data2[NEW_CODE]);

			$cmd = " select NEW_CODE from PER_MAP_CODE 
							  where MAP_CODE = 'PER_MOVMENT' AND OLD_CODE = '$TEMPMOVEMENTTYPE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			if (trim($data2[NEW_CODE])) $MOV_CODE = trim($data2[NEW_CODE]);
			else $MOV_CODE = "101";

			$cmd = " INSERT INTO PER_PERSONAL (PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, 
							PER_SURNAME, PER_ENG_NAME, PER_ENG_SURNAME, ORG_ID, POS_ID, POEM_ID, LEVEL_NO, PER_ORGMGT, 
							PER_SALARY, PER_MGTSALARY, PER_SPSALARY, PER_GENDER, MR_CODE, PER_CARDNO, PER_OFFNO, 
							PER_TAXNO, PER_BLOOD, RE_CODE, PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, 
							PER_OCCUPYDATE, PER_POSDATE, PER_SALDATE, PN_CODE_F, PER_FATHERNAME, PER_FATHERSURNAME, 
							PN_CODE_M, PER_MOTHERNAME, PER_MOTHERSURNAME, PER_ADD1, PER_ADD2, PV_CODE, MOV_CODE, 
							PER_ORDAIN, PER_SOLDIER, PER_MEMBER, PER_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, 
							APPROVE_PER_ID, REPLACE_PER_ID, ABSENT_FLAG, POEMS_ID, PER_HIP_FLAG, PER_CERT_OCC, 
							LEVEL_NO_SALARY)
							VALUES ($MAX_ID, $PER_TYPE, '$OT_CODE', '$PN_CODE', '$PER_NAME', '$PER_SURNAME', 
							NULL, NULL, NULL, $POS_ID, NULL, '$LEVEL_NO', 0, $PER_SALARY, $PER_MGTSALARY, 0, $PER_GENDER, '1', 
							'$PER_CARDNO', NULL, NULL, NULL, NULL, '$PER_BIRTHDATE', NULL,	'$PER_STARTDATE', '$PER_STARTDATE', 
							NULL, NULL, NULL,	NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$PV_CODE', '$MOV_CODE', NULL, NULL, NULL, 1, 
							$UPDATE_USER, '$UPDATE_DATE', $search_department_id, NULL, NULL, NULL, NULL, NULL, NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd1 = " SELECT PER_ID FROM PER_PERSONAL WHERE PER_ID = $MAX_ID ";
			$count_data = $db_dpis->send_cmd($cmd1);
			if (!$count_data) echo "$cmd<br>";

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_PERSONAL', '$POS_ID', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$MAX_ID++;
		} // end while				

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_PERSONAL where per_id in $where_per_id ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_PERSONAL - $PER_PERSONAL - $COUNT_NEW<br>";

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
	} // end if

?>