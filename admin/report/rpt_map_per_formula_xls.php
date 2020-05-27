<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

//	$db_dpis_n = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	// ==== use for testing phase =====
	$db_dpis_n = new connect_db($db_host, $db_name, $db_user, $db_pwd);
	// ==========================

	$report_title = "��ػ�����º��§�дѺ���˹觢���Ҫ���";

	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	if(!$groupby_org){
		$worksheet = &$workbook->addworksheet("��º��§�дѺ���˹觢���Ҫ���");
		$worksheet->set_margin_right(0.50);
		$worksheet->set_margin_bottom(1.10);
	} // end if

	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 6);			$worksheet->set_column(1, 1, 13);				$worksheet->set_column(2, 2, 19);
		$worksheet->set_column(3, 3, 20);			$worksheet->set_column(4, 4, 20);				$worksheet->set_column(5, 5, 19);
		$worksheet->set_column(6, 6, 20);			$worksheet->set_column(7, 7, 20);				$worksheet->set_column(8, 8, 11);
		$worksheet->set_column(9, 9, 13);			$worksheet->set_column(10, 10, 15);			$worksheet->set_column(11, 11, 17.5);
		$worksheet->set_column(12, 12, 12);		$worksheet->set_column(13, 13, 18);			$worksheet->set_column(14, 14, 35);
		$worksheet->set_column(15, 15, 17);		$worksheet->set_column(16, 16, 25);			$worksheet->set_column(17, 17, 17);
		$worksheet->set_column(18, 18, 18);		$worksheet->set_column(19, 19, 7);			$worksheet->set_column(20, 20, 13);		
		$worksheet->set_column(21, 21, 18);		$worksheet->set_column(22, 22, 30);			$worksheet->set_column(23, 23, 14);
		$worksheet->set_column(24, 24, 30);		$worksheet->set_column(25, 25, 15);			$worksheet->set_column(26, 26, 16);
		$worksheet->set_column(27, 27, 17);		$worksheet->set_column(28, 28, 24);			$worksheet->set_column(29, 29, 15);
		$worksheet->set_column(30, 30, 14);		$worksheet->set_column(31, 31, 11);			$worksheet->set_column(32, 32, 15);
		$worksheet->set_column(33, 33, 14);		$worksheet->set_column(34, 34, 25);			$worksheet->set_column(35, 35, 14);
		$worksheet->set_column(36, 36, 25);		$worksheet->set_column(37, 37, 14);			$worksheet->set_column(38, 38, 25);
		$worksheet->set_column(39, 39, 24);		$worksheet->set_column(40, 40, 30);			$worksheet->set_column(41, 41, 12);
		$worksheet->set_column(42, 42, 20);		$worksheet->set_column(43, 43, 30);			$worksheet->set_column(44, 44, 21);
		$worksheet->set_column(45, 45, 25);		$worksheet->set_column(46, 46, 22);			$worksheet->set_column(47, 47, 20);
		$worksheet->set_column(48, 48, 90);
		
		$worksheet->write($xlsRow, 0, "�ӴѺ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "���ʤӹ�˹�Ҫ���", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "�ӹ�˹�Ҫ��� (Th)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "���� (Th)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "���ʡ�� (Th)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "�ӹ�˹�Ҫ��� (En)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "���� (En)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "���ʡ�� (En)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "�дѺ���˹�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 9, "���������˹�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 10, "�дѺ���˹� (����)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 11, "���������˹� (����)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 12, "�Ţ�����˹�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 13, "���ʵ��˹����§ҹ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 14, "���˹����§ҹ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 15, "���ʻ���������Ҫ���", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 16, "����������Ҫ���", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 17, "�Ţ��Шӵ�ǻ�ЪҪ�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 18, "�Ţ��Шӵ�Ǣ���Ҫ���", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 19, "��", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 20, "�ѹ / ��͹ / �� �Դ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 21, "�����زԡ���֡���٧�ش", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 22, "�زԡ���֡���٧�ش", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 23, "�����Ң��Ԫ��͡", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 24, "�Ң��Ԫ��͡", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 25, "�ѹ�������Ѻ�Ҫ���", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 26, "�ѹ��������ǹ�Ҫ���", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 27, "�ѹ������³�����Ҫ���", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 28, "�ѹ����������дѺ���˹觻Ѩ�غѹ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 29, "�ѹ����˹����˹�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 30, "�ѹ�����˹����Թ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 31, "�ѵ���Թ��͹", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 32, "�Թ��Шӵ��˹�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 33, "����$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 34, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 35, "����$ORG_TITLE1", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 36, "$ORG_TITLE1", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 37, "����$ORG_TITLE2", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 38, "$ORG_TITLE2", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 39, "���ʵ��˹�㹡�ú����çҹ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 40, "���˹�㹡�ú����çҹ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 41, "�дѺ�Ǻ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 42, "�����ҢҤ�������Ǫҭ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 43, "�ҢҤ�������Ǫҭ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 44, "���ʨѧ��Ѵ����ç���˹�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 45, "�ѧ��Ѵ����ç���˹�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 46, "���ʻ���ȷ���ç���˹�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 47, "����ȷ���ç���˹�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 48, "��������ö�����", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // end if

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
	
	$cmd = " select 		LEVEL_NO, PL_CODE, PT_CODE, PT_CODE_N, POS_ID,
										PN_CODE, PER_NAME, PER_SURNAME, PER_ENG_NAME, PER_ENG_SURNAME,
										OT_CODE, PER_CARDNO, PER_OFFNO, PER_GENDER,
										PER_BIRTHDATE, PER_STARTDATE, PER_OCCUPYDATE, PER_RETIREDATE,
										POS_SALARY, POS_MGTSALARY, POS_CHANGE_DATE, POS_DATE, POS_GET_DATE,
										PM_CODE, CL_NAME, SKILL_CODE, ORG_ID, ORG_ID_1, ORG_ID_2, 
										EN_CODE, EM_CODE, PV_CODE, CT_CODE, ABILITY
					 from			PER_FORMULA
					 order	by	$orderby
				  ";
	$count_data = $db->send_cmd($cmd);
//	$db->show_error();

	if($count_data){		
		if(!$groupby_org){
			$xlsRow = 0;
			$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
			for($i=1; $i<=48; $i++){ $worksheet->write($xlsRow, $i, "", set_format("xlsFmtTitle", "B", "C", "", 1)); }
		
			print_header(1);
			$data_count = 1;
		} // end if

		$org_count = $data_count = $data_row = 0;
		while($data = $db->get_array()){
			if($groupby_org && ($ORG_ID != $data[ORG_ID])){
				$org_count++;
				$ORG_ID = $data[ORG_ID];
				$cmd = " select ORG_NAME, ORG_CODE from PER_ORG where ORG_ID=$ORG_ID ";
				if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis->send_cmd($cmd);
				$data_dpis = $db_dpis->get_array();
				$ORG_NAME = $data_dpis[ORG_NAME];
				$ORG_CODE = trim($data_dpis[ORG_CODE]);
				
				$worksheet = &$workbook->addworksheet("$ORG_CODE");
				$worksheet->set_margin_right(0.50);
				$worksheet->set_margin_bottom(1.10);

				$xlsRow = 0;
				$worksheet->write($xlsRow, 0, "$report_title - $ORG_NAME", set_format("xlsFmtTitle", "B", "C", "", 1));
				for($i=1; $i<=48; $i++){ $worksheet->write($xlsRow, $i, "", set_format("xlsFmtTitle", "B", "C", "", 1)); }
		
				print_header(1);
				$data_count = 1;
				$data_row = 0;
			} // end if	

			$data_count++;
			$data_row++;			

			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select PN_NAME, PN_ENG_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$PN_NAME = trim($data_dpis[PN_NAME]);
			$PN_ENG_NAME = trim($data_dpis[PN_ENG_NAME]);
			
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$FULLNAME = "$PN_NAME $PER_NAME $PER_SURNAME";

			$PER_ENG_NAME = trim($data[PER_ENG_NAME]);
			$PER_ENG_SURNAME = trim($data[PER_ENG_SURNAME]);
			$ENG_FULLNAME = "$PN_ENG_NAME $PER_ENG_NAME $PER_ENG_SURNAME";
			
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$POS_ID = trim($data[POS_ID]);
			$cmd = " select POS_NO from PER_POSITION where POS_ID=$POS_ID ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$POS_NO = trim($data_dpis[POS_NO]);

			$PL_CODE = trim($data[PL_CODE]);
			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$PL_NAME = trim($data_dpis[PL_NAME]) . " ". level_no_format($LEVEL_NO) . (($PT_NAME != "�����" && $LEVEL_NO >= 6)?" $PT_NAME":"");

			if($PL_CODE=="011103" && ($LEVEL_NO==6 || $LEVEL_NO==7)) $PL_NAME = " * ".$PL_NAME;
//			if($PL_CODE=="010903" && $LEVEL_NO==7) $PL_NAME = " ** ".$PL_NAME;

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

			$OT_CODE = trim($data[OT_CODE]);
			$cmd = " select OT_NAME from PER_OFF_TYPE where trim(OT_CODE)='$OT_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$OT_NAME = trim($data_dpis[OT_NAME]);
	
			$PER_CARDNO = trim($data[PER_CARDNO]);
			$PER_OFFNO = trim($data[PER_OFFNO]);
			$PER_GENDER = trim($data[PER_GENDER]);
			
			$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE], 1);
			$PER_STARTDATE = show_date_format($data[PER_STARTDATE], 1);
			$PER_OCCUPYDATE = show_date_format($data[PER_OCCUPYDATE], 1);
			$PER_RETIREDATE = show_date_format($data[PER_RETIREDATE], 1);
	
			$POS_SALARY = trim($data[POS_SALARY]);
			$POS_MGTSALARY = trim($data[POS_MGTSALARY]);
			$POS_CHANGE_DATE = show_date_format($data[POS_CHANGE_DATE], 1);
			$POS_DATE = show_date_format($data[POS_DATE], 1);
			$POS_GET_DATE = show_date_format($data[POS_GET_DATE], 1);

			$PM_CODE = trim($data[PM_CODE]);
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$PM_NAME = trim($data_dpis[PM_NAME]);
	
			$CL_NAME = trim($data[CL_NAME]);
			$CL_CODE = $CL_NAME;
	
			$SKILL_CODE = trim($data[SKILL_CODE]);
			$cmd = " select SKILL_NAME from PER_SKILL where trim(SKILL_CODE)='$SKILL_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$SKILL_NAME = trim($data_dpis[SKILL_NAME]);
			
			$ORG_ID_0 = trim($data[ORG_ID]);
			$cmd = " select ORG_NAME, ORG_CODE from PER_ORG where OL_CODE='03' and ORG_ID=$ORG_ID_0 ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$ORG_NAME_0 = trim($data_dpis[ORG_NAME]);
			$ORG_CODE_0 = trim($data_dpis[ORG_CODE]);
			
			$ORG_ID_1 = trim($data[ORG_ID_1]);
			$cmd = " select ORG_NAME, ORG_CODE from PER_ORG where OL_CODE='04' and ORG_ID=$ORG_ID_1 ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$ORG_NAME_1 = trim($data_dpis[ORG_NAME]);
			$ORG_CODE_1 = trim($data_dpis[ORG_CODE]);
	
			$ORG_ID_2 = trim($data[ORG_ID_2]); 
			$cmd = " select ORG_NAME, ORG_CODE from PER_ORG where OL_CODE='05' and ORG_ID=$ORG_ID_2 ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$ORG_NAME_2 = trim($data_dpis[ORG_NAME]);
			$ORG_CODE_2 = trim($data_dpis[ORG_CODE]);
			
			$EN_CODE = trim($data[EN_CODE]);
			$cmd = " select EN_NAME from PER_EDUCNAME where trim(EN_CODE)='$EN_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$EN_NAME = trim($data_dpis[EN_NAME]);
	
			$EM_CODE = trim($data[EM_CODE]);
			$cmd = " select EM_NAME from PER_EDUCMAJOR where trim(EM_CODE)='$EM_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$EM_NAME = trim($data_dpis[EM_NAME]);
	
			$PV_CODE = trim($data[PV_CODE]);
			$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$PV_NAME = trim($data_dpis[PV_NAME]);
	
			$CT_CODE = trim($data[CT_CODE]);
			$cmd = " select CT_NAME from PER_COUNTRY where trim(CT_CODE)='$CT_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$CT_NAME = trim($data_dpis[CT_NAME]);
			
			$ABILITY = trim($data[ABILITY]);

			$xlsRow = $data_count;
			$worksheet->write($xlsRow, 0, "$data_row", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, "$PN_CODE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "$PN_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "$PER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "$PER_SURNAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, "$PN_ENG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 6, "$PER_ENG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 7, "$PER_ENG_SURNAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 8, "$LEVEL_NO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 9, "$PT_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 10, "$PT_NAME_N", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 11, "$PT_GROUP_N", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 12, "$POS_NO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 13, "$PL_CODE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 14, "$PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 15, "$OT_CODE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 16, "$OT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 17, "$PER_CARDNO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 18, "$PER_OFFNO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 19, (($PER_GENDER==1)?"���":(($PER_GENDER==2)?"˭ԧ":"")), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 20, "$PER_BIRTHDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 21, "$EN_CODE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 22, "$EN_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 23, "$EM_CODE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 24, "$EM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 25, "$PER_STARTDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 26, "$PER_OCCUPYDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 27, "$PER_RETIREDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 28, "$POS_CHANGE_DATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 29, "$POS_DATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 30, "$POS_GET_DATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 31, "$POS_SALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 32, "$POS_MGTSALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 33, "$ORG_CODE_0", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 34, "$ORG_NAME_0", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 35, "$ORG_CODE_1", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 36, "$ORG_NAME_1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 37, "$ORG_CODE_2", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 38, "$ORG_NAME_2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 39, "$PM_CODE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 40, "$PM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 41, "$CL_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 42, "$SKILL_CODE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 43, "$SKILL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 44, "$PV_CODE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 45, "$PV_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 46, "$CT_CODE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 47, "$CT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 48, "$ABILITY", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ����բ����� *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		for($i=1; $i<=48; $i++){ $worksheet->write($xlsRow, $i, "", set_format("xlsFmtTitle", "B", "C", "", 1)); }
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_title.xls\"");
	header("Content-Disposition: inline; filename=\"$report_title.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>