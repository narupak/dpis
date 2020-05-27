<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_bmp.php");
	include("php_scripts/load_per_control.php");

	$IMG_PATH = "../attachment/pic_personal";	
	$success_pic = $error_pic = $total_pic = $found001 = 0;
	$err_text = "";

	if(!isset($SRC_DIR)) $SRC_DIR = $IMG_PATH;

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	if($db_type=="mysql") {
		$CREATE_DATE = "NOW()";
		$CREATE_BY = "'$SESS_USERNAME'";
	} elseif($db_type=="mssql") {
		$CREATE_DATE = "GETDATE()";
		$CREATE_BY = $SESS_USERID;
	} elseif($db_type=="oci8" || $db_type=="odbc") {
		$CREATE_DATE = date("Y-m-d H:i:s");
		$CREATE_DATE = "'$CREATE_DATE'";
		$CREATE_BY = $SESS_USERID;
	}
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$PER_ID_DEPARTMENT_ID = $DEPARTMENT_ID;

	if( $command=='MAP_PER' ) {
		if ($SESS_DEPARTMENT_NAME=="สำนักพระราชวัง") {
			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, CL_NAME)
							  VALUES ('500231', 'นักจัดการงานในพระองค์', 'นักจัดการงานในพระองค์', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ - ชำนาญการพิเศษ') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, CL_NAME)
							  VALUES ('500232', 'เจ้าพนักงานในพระองค์', 'เจ้าพนักงานในพระองค์', 1, $SESS_USERID, '$UPDATE_DATE', 1, 'ปฏิบัติงาน - อาวุโส') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '510108', PL_TYPE = 4 
							  WHERE PL_CODE IN ('000010', '000040', '000060', '000070', '000090', '000150', '2120000011', '2120000014', '2120000015', '2120000016', '2120000026') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); นักบริหาร 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '510308', PL_TYPE = 3 WHERE PL_CODE IN ('000140', '000160', '000390') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ผู้อำนวยการ 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '500231', PL_TYPE = 2 WHERE PL_CODE IN ('000231', '000290', '000300', '2120000017', '2120000018') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); นักจัดการงานในพระองค์ 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '520603', PL_TYPE = 2 WHERE PL_CODE IN ('584579') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); นักวิชาการตรวจสอบภายใน 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '560104', PL_TYPE = 2 WHERE PL_CODE IN ('000050', '000110', '2120000019', '2120000027') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); นายแพทย์ 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '540103', PL_TYPE = 2 WHERE PL_CODE IN ('2120000009', '2120000023') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); นักวิชาการเกษตร 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '510903', PL_TYPE = 2 WHERE PL_CODE IN ('2120000029') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); นักทรัพยากรบุคคล 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '510703', PL_TYPE = 2 WHERE PL_CODE IN ('2120000022') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); นักวิเคราะห์นโยบายและแผน 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '561502', PL_TYPE = 2 WHERE PL_CODE IN ('2120000024') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); พยาบาลเทคนิค 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '580103', PL_TYPE = 2 WHERE PL_CODE IN ('00580') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); นักวิชาการศึกษา 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '500232', PL_TYPE = 1 
							  WHERE PL_CODE IN ('000200', '000210', '000220', '000232', '000240', '000250', '000310', '000320', '2120000028', '2120000020') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); เจ้าพนักงานในพระองค์ 
		} elseif ($SESS_DEPARTMENT_NAME=="สำนักราชเลขาธิการ") {
			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, CL_NAME)
							  VALUES ('501110', 'วิทยากร', 'วิทยากร', 1, $SESS_USERID, '$UPDATE_DATE', 2, 'ปฏิบัติการ-ชำนาญการพิเศษ') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '510108', PL_TYPE = 4 
							  WHERE PL_CODE IN ('001010', '001020', '001030', '001050', '001070', '001500', '2130000001', '2130000002', '2130000121', '2130000123') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); นักบริหาร 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '510308', PL_TYPE = 3 WHERE PL_CODE IN ('000140') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ผู้อำนวยการ 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '510703', PL_TYPE = 2 WHERE PL_CODE IN ('2130000024', '011003') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); นักวิเคราะห์นโยบายและแผน 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '512403', PL_TYPE = 2 WHERE PL_CODE IN ('2130000022') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); นิติกร 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '501110', PL_TYPE = 2 WHERE PL_CODE IN ('2130000041') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); วิทยากร 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '511104', PL_TYPE = 2 WHERE PL_CODE IN ('011103') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); นักจัดการงานทั่วไป 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '581103', PL_TYPE = 2 WHERE PL_CODE IN ('2130000023', '2130000063') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); นักอักษรศาสตร์ 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '520303', PL_TYPE = 2 WHERE PL_CODE IN ('2130000122') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); นักบัญชี 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '520423', PL_TYPE = 2 WHERE PL_CODE IN ('2130000004') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); นักวิชาการเงินและบัญชี 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '511723', PL_TYPE = 2 WHERE PL_CODE IN ('2130000042') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); นักวิชาการพัสดุ 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '581501', PL_TYPE = 1 WHERE PL_CODE IN ('001420', '2130000084') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); เจ้าพนักงานห้องสมุด 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '584301', PL_TYPE = 1 WHERE PL_CODE IN ('001410') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); เจ้าพนักงานจดหมายเหตุ 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '511612', PL_TYPE = 1 WHERE PL_CODE IN ('2130000103') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); เจ้าพนักงานธุรการ 

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '581303', PL_TYPE = 2 WHERE PL_CODE IN ('081303') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); บรรณารักษ์ 

//			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '520603', PL_TYPE = 2 WHERE PL_CODE IN ('584579', '020603') ";
//			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); นักวิชาการตรวจสอบภายใน 

//			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '510903', PL_TYPE = 2 WHERE PL_CODE IN ('2120000029', '011403') ";
//			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); นักทรัพยากรบุคคล 

//			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '532423', PL_TYPE = 2 WHERE PL_CODE IN ('032423') ";
//			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); นักวิชาการเผยแพร่ 

//			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '581203', PL_TYPE = 2 WHERE PL_CODE IN ('081203') ";
//			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); นักจดหมายเหตุ 

		}
	}

	if( $command=='EMPSER' ) {
		include("alter_table_empser.php");
	} // end if

	if( $command=='MAP_EMP' ) {
		include("alter_table_map_emp.php");
	} // end if

	if( $command=='MENU' ) {
		include("alter_table_menu.php");
	} // end if

	if( $command=='COMPETENCY' ) {
		include("alter_table_competency.php");
	} // end if

	if( $command=='LAYER_UP' ) {
		include("alter_table_layer_up.php");
	} // end if

	if( $command=='LAYER_NEW' ) {
		include("alter_table_layer_new.php");
	} // end if

	if( $command=='E_HR' ) {
		include("alter_table_e_hr.php");
	} // end if

	if( $command=='SALARYHIS' ){
// ปรับปรุงประวัติการรับเงินเดือน
		$cmd = " SELECT SAH_ID, PER_ID, SAH_EFFECTIVEDATE, LEVEL_NO, SAH_SALARY, SAH_OLD_SALARY, 
						  SAH_SALARY_UP, SAH_LAST_SALARY, SAH_POSITION, SAH_ORG, SAH_POS_NO, SAH_PAY_NO, MOV_CODE 
						  FROM PER_SALARYHIS 
						  ORDER BY PER_ID, SAH_EFFECTIVEDATE, SAH_SALARY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$SAH_ID = $data[SAH_ID];
			$PER_ID = $data[PER_ID];
			$SAH_EFFECTIVEDATE = trim($data[SAH_EFFECTIVEDATE]);
			$old_LEVEL_NO = trim($data[LEVEL_NO]);
			$old_SAH_SALARY = $data[SAH_SALARY];
			$old_SAH_OLD_SALARY = $data[SAH_OLD_SALARY];
			$SAH_LAST_SALARY = trim($data[SAH_LAST_SALARY]);
			if (!$SAH_LAST_SALARY) $SAH_LAST_SALARY = "N";
			$old_SAH_POSITION = trim($data[SAH_POSITION]);
			$old_SAH_ORG = trim($data[SAH_ORG]);
			$old_SAH_POS_NO = trim($data[SAH_POS_NO]);
			$old_SAH_PAY_NO = trim($data[SAH_PAY_NO]);
			$MOV_CODE = trim($data[MOV_CODE]);
/*
//			if (!$old_SAH_OLD_SALARY && $old_SAH_SALARY) {
				if ($MOV_CODE=="21420" || $MOV_CODE=="21430")
					$cmd = " SELECT SAH_SALARY FROM PER_SALARYHIS 
									  WHERE PER_ID = $PER_ID AND SAH_EFFECTIVEDATE < '$SAH_EFFECTIVEDATE' AND 
									  MOV_CODE NOT IN ('21420', '21430') AND SAH_ID != $SAH_ID
									  ORDER BY SAH_EFFECTIVEDATE DESC, SAH_SEQ_NO DESC, SAH_SALARY DESC ";
				else
					$cmd = " SELECT SAH_SALARY FROM PER_SALARYHIS 
									  WHERE PER_ID = $PER_ID AND SAH_EFFECTIVEDATE <= '$SAH_EFFECTIVEDATE' AND 
									  MOV_CODE NOT IN ('21420', '21430') AND SAH_SALARY <= $old_SAH_SALARY AND SAH_ID != $SAH_ID
									  ORDER BY SAH_EFFECTIVEDATE DESC, SAH_SEQ_NO DESC, SAH_SALARY DESC ";
				$db_dpis1->send_cmd($cmd);
				$data_dpis1 = $db_dpis1->get_array();
				$SAH_OLD_SALARY = trim($data_dpis1[SAH_SALARY]);
//				if (!$old_SAH_OLD_SALARY && $old_SAH_SALARY && $SAH_LAST_SALARY=="N") {
				if (($MOV_CODE=="21420" || $MOV_CODE=="21430") && $SAH_OLD_SALARY > $old_SAH_SALARY) {
					$SAH_SALARY_EXTRA = $old_SAH_SALARY;
					$cmd = " UPDATE PER_SALARYHIS SET SAH_OLD_SALARY = $SAH_OLD_SALARY, SAH_SALARY_EXTRA = $SAH_SALARY_EXTRA 
									  WHERE SAH_ID = $SAH_ID ";
				} else {
					$SAH_SALARY_UP = $old_SAH_SALARY - $SAH_OLD_SALARY;
					$cmd = " UPDATE PER_SALARYHIS SET SAH_OLD_SALARY = $SAH_OLD_SALARY, SAH_SALARY_UP = $SAH_SALARY_UP 
									  WHERE SAH_ID = $SAH_ID ";
				}
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
//				}
*/
				$cmd = " SELECT POH_PL_NAME, PL_CODE, PM_CODE, LEVEL_NO, POH_POS_NO, POH_ORG , 
								  POH_ORG1, POH_ORG2, POH_ORG3, POH_UNDER_ORG1, POH_UNDER_ORG2 
								  FROM PER_POSITIONHIS WHERE PER_ID = $PER_ID AND POH_EFFECTIVEDATE <= '$SAH_EFFECTIVEDATE'
								  ORDER BY POH_EFFECTIVEDATE DESC, POH_SEQ_NO DESC ";
				$db_dpis1->send_cmd($cmd);
				$data_dpis1 = $db_dpis1->get_array();
				$POH_PL_NAME = trim($data_dpis1[POH_PL_NAME]);
				$PL_CODE = trim($data_dpis1[PL_CODE]);
				$PM_CODE = trim($data_dpis1[PM_CODE]);
				$LEVEL_NO = trim($data_dpis1[LEVEL_NO]);
				$POH_POS_NO = trim($data_dpis1[POH_POS_NO]);
				$POH_ORG = trim($data_dpis1[POH_ORG]);
				$POH_ORG1 = trim($data_dpis1[POH_ORG1]);
				$POH_ORG2 = trim($data_dpis1[POH_ORG2]);
				$POH_ORG3 = trim($data_dpis1[POH_ORG3]);
				$POH_UNDER_ORG1 = trim($data_dpis1[POH_UNDER_ORG1]);
				$POH_UNDER_ORG2 = trim($data_dpis1[POH_UNDER_ORG2]);

				$cmd = " UPDATE PER_SALARYHIS SET LEVEL_NO = '$LEVEL_NO' WHERE SAH_ID = $SAH_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();

//				if (!$old_SAH_POSITION) {
//					$cmd = " UPDATE PER_SALARYHIS SET SAH_POSITION = '$SAH_POSITION' WHERE SAH_ID = $SAH_ID ";
//					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
//				} 
//				if (!$old_SAH_ORG) {
//					$cmd = " UPDATE PER_SALARYHIS SET SAH_ORG = '$SAH_ORG' WHERE SAH_ID = $SAH_ID ";
//					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
//				} 
//			}

		} // end while						
	} // end if

	if( $command=='OLDSALARY' ){
// แก้เงินเดือน
/*		$cmd = " SELECT PER_ID, POS_ID FROM PER_PERSONAL WHERE PER_TYPE = 1 AND PER_STATUS = 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$PER_ID = $data[PER_ID];
			$POS_ID = $data[POS_ID];

			$cmd = " SELECT SAH_ID, SAH_SALARY FROM PER_SALARYHIS WHERE PER_ID = $PER_ID
							  ORDER BY SAH_SALARY DESC, SAH_EFFECTIVEDATE DESC, SAH_SEQ_NO DESC ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$SAH_ID = trim($data_dpis1[SAH_ID]);
			$SAH_SALARY = trim($data_dpis1[SAH_SALARY]);

			if ($SAH_SALARY) {
				$cmd = " UPDATE PER_PERSONAL SET PER_SALARY = $SAH_SALARY WHERE PER_ID = $PER_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();

				$cmd = " UPDATE PER_POSITION SET POS_SALARY = $SAH_SALARY WHERE POS_ID = $POS_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();

				$cmd = " UPDATE PER_SALARYHIS SET SAH_LAST_SALARY = 'Y' WHERE SAH_ID = $SAH_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			}
		} // end while					
*/		
/*
		$cmd = " SELECT SAH_ID, a.PER_ID, SAH_EFFECTIVEDATE, SAH_SALARY, SAH_LAST_SALARY
						  FROM PER_SALARYHIS a, PER_PERSONAL b
						  WHERE a.PER_ID = b.PER_ID and PER_TYPE = 1 and PER_STATUS = 1 and a.MOV_CODE = '1901'
						  ORDER BY a.PER_ID, SAH_EFFECTIVEDATE DESC, SAH_CMD_SEQ DESC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$SAH_ID = $data[SAH_ID];
			$PER_ID = $data[PER_ID];
			$SAH_EFFECTIVEDATE = trim($data[SAH_EFFECTIVEDATE]);
			$old_SAH_SALARY = $data[SAH_SALARY];
			$SAH_LAST_SALARY = trim($data[SAH_LAST_SALARY]);

			$cmd = " SELECT SAH_EFFECTIVEDATE FROM PER_SALARYHIS 
							  WHERE PER_ID = $PER_ID AND SAH_EFFECTIVEDATE >= '$SAH_EFFECTIVEDATE' AND SAH_ID != $SAH_ID ";
			$count_data = $db_dpis1->send_cmd($cmd);
			if (!$count_data) {
				if ($SAH_LAST_SALARY = "N") {
					$cmd = " UPDATE PER_SALARYHIS SET SAH_LAST_SALARY = 'N' WHERE PER_ID = $PER_ID ";
					$db_dpis1->send_cmd($cmd);

					$cmd = " UPDATE PER_SALARYHIS SET SAH_LAST_SALARY = 'Y' WHERE SAH_ID = $SAH_ID ";
					echo "$PER_ID - $cmd<br>";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				}
				$cmd = " SELECT SAH_SALARY FROM PER_SALARYHIS 
								  WHERE PER_ID = $PER_ID AND SAH_EFFECTIVEDATE < '$SAH_EFFECTIVEDATE' AND SAH_ID != $SAH_ID AND MOV_CODE != '1901'
								  ORDER BY SAH_EFFECTIVEDATE DESC, SAH_SEQ_NO DESC, SAH_SALARY DESC ";
				$db_dpis1->send_cmd($cmd);
				$data_dpis1 = $db_dpis1->get_array();
				$SAH_SALARY = trim($data_dpis1[SAH_SALARY]);
				if ($SAH_SALARY > $old_SAH_SALARY) {
					$cmd = " UPDATE PER_SALARYHIS SET SAH_SALARY = $SAH_SALARY WHERE SAH_ID = $SAH_ID ";
					echo "$PER_ID - $cmd<br>";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				}
			} // end if
		} // end while						
*/
		$cmd = " CREATE INDEX IDX_TEMP ON PER_SALARYHIS (SAH_PAY_NO) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT POS_ID, POS_NO FROM PER_POSITION WHERE POS_SALARY = 0  AND POS_STATUS = 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$POS_ID = $data[POS_ID];
			$POS_NO = $data[POS_NO];

			$cmd = " SELECT SAH_SALARY FROM PER_SALARYHIS a, PER_PERSONAL b 
							  WHERE a.PER_ID = b.PER_ID AND SAH_PAY_NO = '$POS_NO' 
							  ORDER BY SAH_EFFECTIVEDATE DESC, SAH_SEQ_NO DESC ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$SAH_SALARY = trim($data_dpis1[SAH_SALARY]);

			if ($SAH_SALARY) {
				$cmd = " UPDATE PER_POSITION SET POS_SALARY = $SAH_SALARY WHERE POS_ID = $POS_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				//echo "$cmd<br>";
			}
		} // end while					
		$cmd = " DROP INDEX IDX_TEMP ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	} // end if

	if( $command=='UPDATESAL' ){
// ปรับปรุงประวัติการรับเงินเดือน
		$cmd = " UPDATE PER_SALARYHIS SET SAH_ENDDATE = NULL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT SAH_ID, PER_ID, SAH_EFFECTIVEDATE, SAH_ENDDATE, MOV_CODE, SAH_DOCNO, SAH_DOCDATE FROM PER_SALARYHIS 
						  ORDER BY PER_ID, SAH_EFFECTIVEDATE DESC, SAH_SEQ_NO DESC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$SAH_ID = $data[SAH_ID];
			$PER_ID = $data[PER_ID];
			$SAH_EFFECTIVEDATE = trim($data[SAH_EFFECTIVEDATE]);
			$SAH_ENDDATE = trim($data[SAH_ENDDATE]);
			$MOV_CODE = trim($data[MOV_CODE]);
			$SAH_DOCNO = trim($data[SAH_DOCNO]);
			$SAH_DOCDATE = trim($data[SAH_DOCDATE]);

			if ($PER_ID != $TEMP_ID) {
				$TEMP_ID = $PER_ID;

				$cmd = " SELECT PER_DOCDATE FROM PER_PERSONAL WHERE PER_ID = $PER_ID AND PER_STATUS = 1 ";
				$db_dpis1->send_cmd($cmd);
				$data_dpis1 = $db_dpis1->get_array();
				$PER_DOCDATE = trim($data_dpis1[PER_DOCDATE]);

				if ($SAH_DOCDATE > $PER_DOCDATE) {
					$cmd = " UPDATE PER_PERSONAL SET MOV_CODE = '$MOV_CODE', PER_DOCNO = '$SAH_DOCNO', PER_DOCDATE = '$SAH_DOCDATE' 
									WHERE PER_ID = $PER_ID AND PER_STATUS = 1 ";
					$db_dpis1->send_cmd($cmd);
				}
			} elseif (!$SAH_ENDDATE) {
				if ($TEMP_ENDDATE < $SAH_EFFECTIVEDATE)	$TEMP_ENDDATE = $SAH_EFFECTIVEDATE;
				$cmd = " UPDATE PER_SALARYHIS SET SAH_ENDDATE = '$TEMP_ENDDATE' WHERE SAH_ID = $SAH_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			} elseif ($SAH_ENDDATE) {
				if ($TEMP_ENDDATE < $SAH_EFFECTIVEDATE){
					//echo "$TEMP_ENDDATE-$SAH_EFFECTIVEDATE<br>";
					$TEMP_ENDDATE = $SAH_EFFECTIVEDATE;
					$cmd = " UPDATE PER_SALARYHIS SET SAH_ENDDATE = '$TEMP_ENDDATE' WHERE SAH_ID = $SAH_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				}
			} // end if
			$tmp_date = explode("-", $SAH_EFFECTIVEDATE);
			$TEMP_ENDDATE = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2),		$tmp_date[0]) - 86400);
			$TEMP_ENDDATE = date("Y-m-d", $TEMP_ENDDATE);
		} // end while						
	} // end if

	if( $command=='UPDATEPOS' ){
// ปรับปรุงประวัติการดำรงตำแหน่ง
		$cmd = " UPDATE PER_POSITIONHIS SET POH_ENDDATE = NULL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT POH_ID, PER_ID, POH_EFFECTIVEDATE, POH_ENDDATE, MOV_CODE, POH_DOCNO, POH_DOCDATE FROM PER_POSITIONHIS 
						  ORDER BY PER_ID, POH_EFFECTIVEDATE DESC, POH_SEQ_NO desc ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$POH_ID = $data[POH_ID];
			$PER_ID = $data[PER_ID];
			$POH_EFFECTIVEDATE = trim($data[POH_EFFECTIVEDATE]);
			$POH_ENDDATE = trim($data[POH_ENDDATE]);
			$MOV_CODE = trim($data[MOV_CODE]);
			$POH_DOCNO = trim($data[POH_DOCNO]);
			$POH_DOCDATE = trim($data[POH_DOCDATE]);

			$cmd = " SELECT SAH_SALARY FROM PER_SALARYHIS 
							WHERE PER_ID = $PER_ID AND SAH_EFFECTIVEDATE <= '$POH_EFFECTIVEDATE'
							ORDER BY SAH_EFFECTIVEDATE DESC, SAH_SEQ_NO DESC, SAH_SALARY DESC ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$SAH_SALARY = $data_dpis1[SAH_SALARY];

			if ($SAH_SALARY) {
				$cmd = " UPDATE PER_POSITIONHIS SET POH_SALARY = $SAH_SALARY 	WHERE POH_ID = $POH_ID ";
				$db_dpis1->send_cmd($cmd);
			}

			//echo "$POH_ID - $PER_ID - $POH_EFFECTIVEDATE - $POH_ENDDATE<br>"; 
			if ($PER_ID != $TEMP_ID) {
				$TEMP_ID = $PER_ID;

/*				$cmd = " SELECT PER_DOCDATE FROM PER_PERSONAL WHERE PER_ID = $PER_ID AND PER_STATUS = 1 ";
				$db_dpis1->send_cmd($cmd);
				$data_dpis1 = $db_dpis1->get_array();
				$PER_DOCDATE = trim($data_dpis1[PER_DOCDATE]);

				if ($POH_DOCDATE > $PER_DOCDATE) {
					$cmd = " UPDATE PER_PERSONAL SET MOV_CODE = '$MOV_CODE', PER_DOCNO = '$POH_DOCNO', PER_DOCDATE = '$POH_DOCDATE' 
									WHERE PER_ID = $PER_ID AND PER_STATUS = 1 ";
					$db_dpis1->send_cmd($cmd);
				}  */
			} elseif (!$POH_ENDDATE) {
				if ($TEMP_ENDDATE < $POH_EFFECTIVEDATE)	$TEMP_ENDDATE = $POH_EFFECTIVEDATE;
				$cmd = " UPDATE PER_POSITIONHIS SET POH_ENDDATE = '$TEMP_ENDDATE' WHERE POH_ID = $POH_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			} elseif ($POH_ENDDATE) {
				if ($TEMP_ENDDATE < $POH_EFFECTIVEDATE){
					//echo "$TEMP_ENDDATE-$POH_EFFECTIVEDATE<br>";
					$TEMP_ENDDATE = $POH_EFFECTIVEDATE;
					$cmd = " UPDATE PER_POSITIONHIS SET POH_ENDDATE = '$TEMP_ENDDATE' WHERE POH_ID = $POH_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				}
			} // end if
			$tmp_date = explode("-", $POH_EFFECTIVEDATE);
			$TEMP_ENDDATE = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2),		$tmp_date[0]) - 86400);
			$TEMP_ENDDATE = date("Y-m-d", $TEMP_ENDDATE);
		} // end while			
	} // end if

	if( $command=='UPDATEMOV' ){
		$cmd = " SELECT PER_ID FROM PER_PERSONAL WHERE PER_STATUS = 1 AND PER_DOCNO = 'อด.470/2553' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$PER_ID = $data[PER_ID];

			$cmd = " SELECT SAH_EFFECTIVEDATE, MOV_CODE, SAH_DOCNO, SAH_DOCDATE FROM PER_SALARYHIS 
							WHERE PER_ID = $PER_ID 
							ORDER BY SAH_EFFECTIVEDATE DESC, SAH_SEQ_NO DESC, SAH_SALARY DESC ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$SAH_EFFECTIVEDATE = trim($data_dpis1[SAH_EFFECTIVEDATE]);
			$SAH_MOV_CODE = trim($data_dpis1[MOV_CODE]);
			$SAH_DOCNO = trim($data_dpis1[SAH_DOCNO]);
			$SAH_DOCDATE = trim($data_dpis1[SAH_DOCDATE]);

			$cmd = " SELECT POH_EFFECTIVEDATE, MOV_CODE, POH_DOCNO, POH_DOCDATE FROM PER_POSITIONHIS 
							WHERE PER_ID = $PER_ID 
							ORDER BY POH_EFFECTIVEDATE DESC, POH_SEQ_NO DESC ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$POH_EFFECTIVEDATE = trim($data_dpis1[POH_EFFECTIVEDATE]);
			$POH_MOV_CODE = trim($data_dpis1[MOV_CODE]);
			$POH_DOCNO = trim($data_dpis1[POH_DOCNO]);
			$POH_DOCDATE = trim($data_dpis1[POH_DOCDATE]);

			if ($SAH_EFFECTIVEDATE > $POH_EFFECTIVEDATE) {
				$MOV_CODE = $SAH_MOV_CODE;
				$PER_DOCNO = $SAH_DOCNO;
				$PER_DOCDATE = $SAH_DOCDATE;
			} else {
				$MOV_CODE = $POH_MOV_CODE;
				$PER_DOCNO = $POH_DOCNO;
				$PER_DOCDATE = $POH_DOCDATE;
			}
			$cmd = " UPDATE PER_PERSONAL SET MOV_CODE = '$MOV_CODE', PER_DOCNO = '$PER_DOCNO', PER_DOCDATE = '$PER_DOCDATE' 
							WHERE PER_ID = $PER_ID ";
			$db_dpis1->send_cmd($cmd);
//			echo "$SAH_EFFECTIVEDATE > $POH_EFFECTIVEDATE $cmd<br>";
		} // end while			

/*		$cmd = " SELECT RECORD_ID, ORG_NAME
						  FROM HR_EMPLOY_RECORD
						  WHERE TRIM(ORG_NAME) > ' '
						  ORDER BY TRIM(ORG_NAME) ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
		while($data = $db_att->get_array()){
			$RECORD_ID = $data[RECORD_ID];
			$ORG_NAME = trim($data[ORG_NAME]);
			$ORG_NAME = str_replace("\n", "  ", $ORG_NAME);
			$ORG_NAME = str_replace("\r", "  ", $ORG_NAME);
			$ORG_NAME = str_replace("\n\r", "  ", $ORG_NAME);
			$ORG_NAME = str_replace("\r\n", "  ", $ORG_NAME);
			$cmd = " UPDATE HR_EMPLOY_RECORD SET  ORG_NAME = '$ORG_NAME' WHERE RECORD_ID = $RECORD_ID ";
			$db_att1->send_cmd($cmd);
//			$db_att1->show_error();
		} // end while			
*/
	} // end if

	if( $command=='JETHRO' ) {
		include("alter_table_jethro.php");
	} // end if การประเมินค่างาน

	if( $command=='EAF' ) {
		include("alter_table_eaf.php");
	} // end if กรอบการสั่งสมประสบการณ์

?>