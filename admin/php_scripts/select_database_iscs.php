<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$UPDATE_USER = 1;
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if( $command=='MASTER' ) {  // ข้อมูลหลัก 
		$cmd = " DELETE FROM PER_MGT_COMPETENCY_ASSESSMENT ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error() ;

//		$cmd = " SELECT ID, COURSE, DC, SEQ, CODE, DATETEST, DATESIGN, TITLE, NAME, SUR, IDP, CONS, MANC, CUSTOMER,
//						  PLAN, DECISION, THINK, LEADER, ADAP, COMMU, COLLAB, ACCOUNT, RBM, HRM, MEANALL,NS, APPPOST, POSTNUM, 
//						  APPDATE, APPLETNUM, PREPOST, PDNAME FROM NAMEALL ";
//		$cmd = " SELECT COURSE, DC, SEQ, CODE, DATETEST, TODATE, DATESIGN, TITLE, NAME, SUR, IDP, CONS, MANC, CUSTOMER,
//						  PLAN, DECISION, THINK, LEADER, ADAP, COMMU, COLLAB, ACCOUNT, RBM, HRM, MEANALL, NS, APPOST, POSTNUM, 
//						  APPDATE, APPLETNUM, PREPOST, PDNAME, AGE, EDUCATE, MANYEAR, TRAIN, ENTCON, BOSSNAME, BOSSPOST, 
//						  SEX, COMMENT, SORTDAY, ACH, SER, EXP, ING, TWK, LDP, VIS, STO, CLP, SCT, CEO, MEANNEW FROM SAMPLE ";
//		$cmd = " SELECT COURSE, DC, SEQ, CODE, format(DATETEST,'yyyy-mm-dd') as CA_TEST_DATE, TODATE, format(DATESIGN,'yyyy-mm-dd') as CA_APPROVE_DATE, TITLE, NAME, SUR, IDP, 
//						  CONS, MANC, CUSTOMER, PLAN, DECISION, THINK, LEADER, ADAP, COMMU, COLLAB, ACCOUNT, RBM, HRM, MEANALL, NS, PREPOST, 
//						  PDNAME, ACH, SER, EXP, ING, TWK, LDP, VIS, STO, CLP, SCT, CEO, MEANNEW FROM NAMEALL ";
		$cmd = " SELECT COURSE, DC, SEQ, CODE, DATETEST, TODATE, DATESIGN, TITLE, NAME, SUR, IDP, 
						  CONS, MANC, CUSTOMER, PLAN, DECISION, THINK, LEADER, ADAP, COMMU, COLLAB, ACCOUNT, RBM, HRM, MEANALL, NS, APPPOST, POSTNUM, 
						  APPDATE, APPLETNUM, PREPOST, 
						  PDNAME, AGE, EDUCATE, MANYEAR, TRAIN, ENTCON, BOSSNAME, BOSSPOST, 
						  SEX, COMMENT, SORTDAY, ACH, SER, EXP, ING, TWK, LDP, VIS, STO, CLP, SCT, CEO, MEANNEW FROM NAMEALL ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();

		while($data = $db_dpis35->get_array()){
			$CA_ID++;
//			$CA_ID = trim($data[ID]);
			$CA_COURSE = trim($data[COURSE]);
			$ORG_CODE = trim($data[DC]);
			$CA_SEQ = trim($data[SEQ]);
			$CA_CODE = trim($data[CODE]);
			$CA_TEST_DATE = trim($data[DATETEST]);
			if ($CA_TEST_DATE) {
				$TMP_Y = substr($CA_TEST_DATE,0,4) + 600 - 543;
				$TMP_M = substr($CA_TEST_DATE,4,2);
				$TMP_D = substr($CA_TEST_DATE,6,2);
				$CA_TEST_DATE = "$TMP_Y-$TMP_M-$TMP_D";
/*			
				$arr_temp = explode("-", $CA_TEST_DATE);
				$TMP_Y = $arr_temp[0] + 600 - 543;
				$TMP_M = $arr_temp[1];
				$TMP_D = $arr_temp[2];
				$CA_TEST_DATE = "$TMP_Y-$TMP_M-$TMP_D";
			if ($CA_TEST_DATE) {
				$arr_temp = explode(" ", $CA_TEST_DATE);
				$TMP_D = $arr_temp[0];
				$TMP_M = $arr_temp[1];
				$TMP_Y = $arr_temp[2];
				$TMP_DD = str_pad(trim($TMP_D), 2, "0", STR_PAD_LEFT);
				if ($TMP_M=="ม.ค.") $TMP_M = "01";
				elseif ($TMP_M=="ก.พ.") $TMP_MM = "02";
				elseif ($TMP_M=="มี.ค.") $TMP_MM = "03";
				elseif ($TMP_M=="เม.ย.") $TMP_MM = "04";
				elseif ($TMP_M=="พ.ค.") $TMP_MM = "05";
				elseif ($TMP_M=="มิ.ย.") $TMP_MM = "06";
				elseif ($TMP_M=="ก.ค.") $TMP_MM = "07";
				elseif ($TMP_M=="ส.ค.") $TMP_MM = "08";
				elseif ($TMP_M=="ก.ย.") $TMP_MM = "09";
				elseif ($TMP_M=="ต.ค.") $TMP_MM = "10";
				elseif ($TMP_M=="พ.ย.") $TMP_MM = "11";
				elseif ($TMP_M=="ธ.ค.") $TMP_MM = "12";
				$TMP_YY = 2500 + $TMP_Y - 543;
				$CA_TEST_DATE = "$TMP_YY-$TMP_MM-$TMP_DD"; */
			} 
			$CA_APPROVE_DATE = trim($data[DATESIGN]);
			if ($CA_APPROVE_DATE) {
				$TMP_Y = substr($CA_APPROVE_DATE,0,4) + 600 - 543;
				$TMP_M = substr($CA_APPROVE_DATE,4,2);
				$TMP_D = substr($CA_APPROVE_DATE,6,2);
				$CA_APPROVE_DATE = "$TMP_Y-$TMP_M-$TMP_D";
/*			
				$arr_temp = explode("-", $CA_APPROVE_DATE);
				$TMP_Y = $arr_temp[0] + 600 - 543;
				$TMP_M = $arr_temp[1];
				$TMP_D = $arr_temp[2];
				$CA_APPROVE_DATE = "$TMP_Y-$TMP_M-$TMP_D";
			if ($CA_APPROVE_DATE) {
				$arr_temp = explode(" ", $CA_APPROVE_DATE);
				$TMP_D = $arr_temp[0];
				$TMP_M = $arr_temp[1];
				$TMP_Y = $arr_temp[2];
				$TMP_DD = str_pad(trim($TMP_D), 2, "0", STR_PAD_LEFT);
				if ($TMP_M=="ม.ค.") $TMP_M = "01";
				elseif ($TMP_M=="ก.พ.") $TMP_MM = "02";
				elseif ($TMP_M=="มี.ค.") $TMP_MM = "03";
				elseif ($TMP_M=="เม.ย.") $TMP_MM = "04";
				elseif ($TMP_M=="พ.ค.") $TMP_MM = "05";
				elseif ($TMP_M=="มิ.ย.") $TMP_MM = "06";
				elseif ($TMP_M=="ก.ค.") $TMP_MM = "07";
				elseif ($TMP_M=="ส.ค.") $TMP_MM = "08";
				elseif ($TMP_M=="ก.ย.") $TMP_MM = "09";
				elseif ($TMP_M=="ต.ค.") $TMP_MM = "10";
				elseif ($TMP_M=="พ.ย.") $TMP_MM = "11";
				elseif ($TMP_M=="ธ.ค.") $TMP_MM = "12";
				$TMP_YY = 2500 + $TMP_Y - 543;
				$CA_APPROVE_DATE = "$TMP_YY-$TMP_MM-$TMP_DD"; */
			} 
			$PN_NAME = trim($data[TITLE]);
			$CA_NAME = trim($data[NAME]);
			$CA_SURNAME = trim($data[SUR]);
			$CA_CARDNO = trim($data[IDP]);
			$CA_CONSISTENCY = trim($data[CONS]);
			$CA_SCORE_1 = trim($data[MANC]);
			$CA_SCORE_2 = trim($data[CUSTOMER]);
			$CA_SCORE_3 = trim($data[PLAN]);
			$CA_SCORE_4 = trim($data[DECISION]);
			$CA_SCORE_5 = trim($data[THINK]);
			$CA_SCORE_6 = trim($data[LEADER]);
			$CA_SCORE_7 = trim($data[ADAP]);
			$CA_SCORE_8 = trim($data[COMMU]);
			$CA_SCORE_9 = trim($data[COLLAB]);
			$CA_SCORE_10 = trim($data[ACCOUNT]);
			$CA_SCORE_11 = trim($data[RBM]);
			$CA_SCORE_12 = trim($data[HRM]);
			$CA_MEAN = trim($data[MEANALL]);
			$CA_MINISTRY_NAME = trim($data[TEMPPROVINCE]);
			$CA_DEPARTMENT_NAME = trim($data[PDNAME]);
			$CA_ORG_NAME = trim($data[TEMPPROVINCE]);
			$CA_ORG_NAME1 = trim($data[TEMPPROVINCE]);
			$CA_ORG_NAME2 = trim($data[TEMPPROVINCE]);
			$CA_LINE = trim($data[PREPOST]);
			$LEVEL_NO = trim($data[TEMPPROVINCE]);
			$CA_MGT = trim($data[TEMPPROVINCE]);
			$CA_POSITION = trim($data[APPPOST]);
			$CA_POS_NO = trim($data[POSTNUM]);
			$CA_DOC_DATE = trim($data[APPDATE]);
			if ($CA_DOC_DATE) {
				$TMP_Y = substr($CA_DOC_DATE,0,4) + 600 - 543;
				$TMP_M = substr($CA_DOC_DATE,4,2);
				$TMP_D = substr($CA_DOC_DATE,6,2);
				$CA_DOC_DATE = "$TMP_Y-$TMP_M-$TMP_D";
			}
			$CA_DOC_NO = trim($data[APPLETNUM]);
			$CA_NEW_SCORE_1 = trim($data[ACH]);
			$CA_NEW_SCORE_2 = trim($data[SER]);
			$CA_NEW_SCORE_3 = trim($data[EXP]);
			$CA_NEW_SCORE_4 = trim($data[ING]);
			$CA_NEW_SCORE_5 = trim($data[TWK]);
			$CA_NEW_SCORE_6 = trim($data[LDP]);
			$CA_NEW_SCORE_7 = trim($data[VIS]);
			$CA_NEW_SCORE_8 = trim($data[STO]);
			$CA_NEW_SCORE_9 = trim($data[CLP]);
			$CA_NEW_SCORE_10 = trim($data[SCT]);
			$CA_NEW_SCORE_11 = trim($data[CEO]);
			$CA_NEW_MEAN = trim($data[MEANNEW]);
			if (!$CA_NEW_SCORE_1) $CA_NEW_SCORE_1 = "NULL";
			if (!$CA_NEW_SCORE_2) $CA_NEW_SCORE_2 = "NULL";
			if (!$CA_NEW_SCORE_3) $CA_NEW_SCORE_3 = "NULL";
			if (!$CA_NEW_SCORE_4) $CA_NEW_SCORE_4 = "NULL";
			if (!$CA_NEW_SCORE_5) $CA_NEW_SCORE_5 = "NULL";
			if (!$CA_NEW_SCORE_6) $CA_NEW_SCORE_6 = "NULL";
			if (!$CA_NEW_SCORE_7) $CA_NEW_SCORE_7 = "NULL";
			if (!$CA_NEW_SCORE_8) $CA_NEW_SCORE_8 = "NULL";
			if (!$CA_NEW_SCORE_9) $CA_NEW_SCORE_9 = "NULL";
			if (!$CA_NEW_SCORE_10) $CA_NEW_SCORE_10 = "NULL";
			if (!$CA_NEW_SCORE_11) $CA_NEW_SCORE_11 = "NULL";
			if (!$CA_NEW_MEAN) $CA_NEW_MEAN = "NULL";

			$PN_CODE = "";
			if ($PN_NAME=="นพ.") $PN_CODE = "134";
			elseif ($PN_NAME=="พญ.") $PN_CODE = "135";
			elseif ($PN_NAME=="6าง" || $PN_NAME=="นางย") $PN_CODE = "005";
			elseif ($PN_NAME=="นายสาว") $PN_CODE = "004";
			elseif ($PN_NAME=="ว่าที่ร.ต." || $PN_NAME=="ว่าที่ ร้อยตรี") $PN_CODE = "219";
			elseif ($PN_NAME=="*นาย") $PN_CODE = "003";
			elseif ($PN_NAME=="ม.ล") $PN_CODE = "122";
			elseif ($PN_NAME=="ร.ต") $PN_CODE = "218";
			elseif ($PN_NAME=="ร.อ") $PN_CODE = "214";
			elseif ($PN_NAME=="นต.") $PN_CODE = "363";
			elseif ($PN_NAME=="นท.") $PN_CODE = "361";
			elseif ($PN_NAME=="พ.ต.ท.หญิง") $PN_CODE = "968";
			elseif ($PN_NAME=="ร.ต.อ.หญิง") $PN_CODE = "988";
			elseif ($PN_NAME=="สพ.ญ.") $PN_CODE = "139";
			elseif ($PN_NAME=="ศจ.นพ.") $PN_CODE = "132";
			elseif ($PN_NAME=="ผ.ศ.ดร.") $PN_CODE = "990";
			elseif ($PN_NAME=="ศจ.ดร.") $PN_CODE = "165";
			elseif ($PN_NAME=="รศ.นพ.") $PN_CODE = "168";
			elseif ($PN_NAME=="นาวาอากาศตรี นพ.") $PN_CODE = "169";
			elseif ($PN_NAME=="ว่าที่ร้อย") $PN_CODE = "219";

			if ($PN_NAME && !$PN_CODE) {
				$cmd = " select PN_CODE from PER_PRENAME 
								  where PN_NAME = '$PN_NAME' or PN_SHORTNAME = '$PN_NAME' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data1 = $db_dpis->get_array();
					$PN_CODE = trim($data1[PN_CODE]);
				} else {
					echo "$PN_NAME<br>";
				}
			}

			$cmd = " select PER_ID from PER_PERSONAL 
							  where (PER_NAME = '$CA_NAME' and PER_SURNAME = '$CA_SURNAME') or PER_CARDNO = '$CA_CARDNO' ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data) {			
				$data1 = $db_dpis->get_array();
				$PER_ID = $data1[PER_ID];
				$CA_TYPE = 1;
			} else {
				$PER_ID = "NULL";   
				$CA_TYPE = 2;
			}
			if (!$CA_NAME) $CA_NAME = "-";
			if (!$CA_SURNAME) $CA_SURNAME = "-";
			if (!$CA_MEAN) $CA_MEAN = "NULL";

			$cmd = " INSERT INTO PER_MGT_COMPETENCY_ASSESSMENT (CA_ID, CA_COURSE, ORG_CODE, CA_SEQ, CA_CODE, CA_TYPE, PER_ID, CA_TEST_DATE,
							CA_APPROVE_DATE, PN_CODE, CA_NAME, CA_SURNAME, CA_CARDNO, CA_CONSISTENCY, CA_SCORE_1, CA_SCORE_2,
							CA_SCORE_3, CA_SCORE_4, CA_SCORE_5, CA_SCORE_6, CA_SCORE_7, CA_SCORE_8, CA_SCORE_9, CA_SCORE_10,
							CA_SCORE_11, CA_SCORE_12, CA_MEAN, CA_MINISTRY_NAME, CA_DEPARTMENT_NAME, CA_ORG_NAME, CA_ORG_NAME1,
							CA_ORG_NAME2, CA_LINE, LEVEL_NO, CA_MGT, CA_POSITION, CA_POS_NO, CA_DOC_DATE, CA_DOC_NO, CA_NEW_SCORE_1, CA_NEW_SCORE_2, 
							CA_NEW_SCORE_3, CA_NEW_SCORE_4, CA_NEW_SCORE_5, CA_NEW_SCORE_6, CA_NEW_SCORE_7, CA_NEW_SCORE_8, CA_NEW_SCORE_9, 
							CA_NEW_SCORE_10, CA_NEW_SCORE_11, CA_NEW_MEAN, CA_REMARK, UPDATE_USER, UPDATE_DATE)
							VALUES ($CA_ID, $CA_COURSE, '$ORG_CODE', $CA_SEQ, '$CA_CODE', $CA_TYPE, $PER_ID, '$CA_TEST_DATE', '$CA_APPROVE_DATE', '$PN_CODE', 
							'$CA_NAME', '$CA_SURNAME', '$CA_CARDNO', $CA_CONSISTENCY, $CA_SCORE_1, $CA_SCORE_2, $CA_SCORE_3, 
							$CA_SCORE_4, $CA_SCORE_5, $CA_SCORE_6, $CA_SCORE_7, $CA_SCORE_8, $CA_SCORE_9, $CA_SCORE_10,
							$CA_SCORE_11, $CA_SCORE_12, $CA_MEAN, '$CA_MINISTRY_NAME', '$CA_DEPARTMENT_NAME', '$CA_ORG_NAME', 
							'$CA_ORG_NAME1', '$CA_ORG_NAME2', '$CA_LINE', '$LEVEL_NO', '$CA_MGT', '$CA_POSITION', '$CA_POS_NO', '$CA_DOC_DATE', 
							'$CA_DOC_NO', $CA_NEW_SCORE_1, $CA_NEW_SCORE_2, $CA_NEW_SCORE_3, $CA_NEW_SCORE_4, $CA_NEW_SCORE_5, 
							$CA_NEW_SCORE_6, $CA_NEW_SCORE_7, $CA_NEW_SCORE_8, $CA_NEW_SCORE_9, $CA_NEW_SCORE_10,	$CA_NEW_SCORE_11, 
							$CA_NEW_MEAN, '$CA_REMARK', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd<br>";
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
	}

?>