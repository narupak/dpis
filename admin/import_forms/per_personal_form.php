<?
//	map text data to table columns

	$table = "PER_PERSONAL";
	// ��Ң�ҧ��ҧ ������ҡ���� 1 ��ǨТ�鹴��� |
	$dup_column = "0";	// �Ţ�ӴѺ������ҧ㹰ҹ������ 
	$prime = "0";		// �Ţ�ӴѺ������ҧ㹰ҹ������
	$running = "-1";		// �Ţ�ӴѺ������ҧ㹰ҹ������ �������� running ��� = -1
	
    if (!isset($FixColumn)) $FixColumn = count(explode("|",$dup_column));	// �ʴ� keycolumn ��� fix ����������͹��˹
    if (!isset($showStartColumn)) $showStartColumn = $FixColumn; // ������ʴ���� column
	if (!isset($NumShowColumn)) $NumShowColumn = 8;	// �ӹǹ column ����ʴ�
	
	// ===== ��˹����������� ��Ф���¡����������������ҧ ��ͤ��� �Ѻ ����Ţ
	$DIVIDE_TEXTFILE = "$";
	
	$column_map = (array) null;
	
	// ��� column map �ѹ�ç��� �� �ش���
//	for($i = 0; $i < 79; $i++) {
//		$column_map[] = "$i";	// 0-running number
//	}
	// ���ش
	
//	$impfile_head_map = array("PER_CARDNO","FNAME","LNAME","INS_CODE","INS_DATE","POS_NAME","DEP_NAME");

//	$head_map = array("��Ŵ� 1","��Ŵ� 2","��Ŵ� 3","��Ŵ� 4","��Ŵ� 5","��Ŵ� 6","��Ŵ� 7","","��Ŵ� 9","");

	$column_map = (array) null;
	$column_map[] = "1";
	$column_map[] = "func-s-e-check_per_type(@2)";
	$column_map[] = "sql-s-d-select OT_CODE from PER_ORG_TYPE where OT_NAME='@3'";
	$column_map[] = "sql-s-d-select PN_CODE from PER_PRENAME where PN_NAME='@4'";
	$column_map[] = "5";
	$column_map[] = "6";
	$column_map[] = "7";
	$column_map[] = "8";
	$column_map[] = "sql-n-d-select ORG_ID from PER_ORG where ORG_NAME='@9'";
	$column_map[] = "sql-n-d-select POS_ID from PER_POSITION where POS_NO='@10'";
	$column_map[] = "sql-n-d-select POEM_ID from PER_POS_EMP where POEM_NO='@11'";
	$column_map[] = "sql-s-d-select LEVEL_NO from PER_LEVEL where LEVEL_NAME='@12'";
	$column_map[] = "13";
	$column_map[] = "14";
	$column_map[] = "15";
	$column_map[] = "16";
	$column_map[] = "func-s-e-check_gender(@17)";
	$column_map[] = "sql-s-d-select MR_CODE from PER_MARRIED where MR_NAME='@18'";
	$column_map[] = "19";
	$column_map[] = "20";
	$column_map[] = "21";
	$column_map[] = "func-s-e-check_blood(@22)";
	$column_map[] = "sql-s-d-select RE_CODE from PER_RELIGION where RE_NAME='@23'";
	$column_map[] = "func-s-e-save_date(@24)";
	$column_map[] = "func-s-e-save_date(@25)";
	$column_map[] = "func-s-e-save_date(@26)";
	$column_map[] = "func-s-e-save_date(@27)";
	$column_map[] = "func-s-e-save_date(@28)";
	$column_map[] = "func-s-e-save_date(@29)";
	$column_map[] = "sql-s-d-select PN_CODE from PER_PRENAME where PN_NAME='@30'";
	$column_map[] = "31";
	$column_map[] = "32";
	$column_map[] = "sql-s-d-select PN_CODE from PER_PRENAME where PN_NAME='@33'";
	$column_map[] = "34";
	$column_map[] = "35";
	$column_map[] = "36";
	$column_map[] = "37";
	$column_map[] = "sql-s-d-select PV_CODE from PER_PROVINCE where PV_NAME='@38";
	$column_map[] = "sql-s-d-select MOV_CODE from PER_MOVMENT where MOV_NAME='@39'";
	$column_map[] = "40";
	$column_map[] = "41";
	$column_map[] = "42";
	$column_map[] = "43";
	$column_map[] = "update_user";	// 44-map �Ѻ UPDATE_USER �ҡ�к�
	$column_map[] = "update_date";	// 45-map �Ѻ UPDATE_DATE �ҡ�к�
	$column_map[] = "sql-n-d-select POEMS_ID from PER_POS_EMPSER where POEMS_NO='@46'";
	$column_map[] = "47";
	$column_map[] = "48";
	$column_map[] = "sql-n-d-select ORG_ID from PER_ORG where ORG_NAME='@49'";
	$column_map[] = "50";
	$column_map[] = "51";
	$column_map[] = "52";
	$column_map[] = "53";
	$column_map[] = "54";
	$column_map[] = "55";
	$column_map[] = "56";
	$column_map[] = "57";
	$column_map[] = "58";
	$column_map[] = "59";
	$column_map[] = "60";
	$column_map[] = "61";
	$column_map[] = "62";
	$column_map[] = "63";
	$column_map[] = "64";
	$column_map[] = "65";
	$column_map[] = "66";
	$column_map[] = "67";
	$column_map[] = "68";
	$column_map[] = "func-s-e-save_date(@69)";	// MEMBERDATE
	$column_map[] = "70";
	$column_map[] = "71";
	$column_map[] = "sql-n-d-select ES_CODE from PER_EMP_STATUS where ES_NAME='@72'";
	$column_map[] = "73";
	$column_map[] = "74";
	$column_map[] = "75";
	$column_map[] = "func-s-e-save_date(@76)";	// DOCDATE
	$column_map[] = "77";
	$column_map[] = "78";
	$column_map[] = "79";
	$column_map[] = "80";
	$column_map[] = "81";
	$column_map[] = "82";
	$column_map[] = "83";
	$column_map[] = "84";
	$column_map[] = "func-s-e-save_date(@85)";	// PER_POS_DOCDATE
	$column_map[] = "86";
	$column_map[] = "87";
	$column_map[] = "88";
	$column_map[] = "func-s-e-save_date(@89)";	// BOOKDATE
	$column_map[] = "90";
	$column_map[] = "91";
	$column_map[] = "sql-n-d-select POT_ID from PER_POS_TEMP where POT_NO='@92'";
	$column_map[] = "93";
	$column_map[] = "func-s-e-save_date(@94)";	// UNIONDATE
	$column_map[] = "95";
	$column_map[] = "sql-n-d-select ORG_ID from PER_ORG where ORG_NAME='@96'";		// ORG_ID_1
	$column_map[] = "sql-n-d-select ORG_ID from PER_ORG where ORG_NAME='@97'";		// ORG_ID_2
	$column_map[] = "sql-n-d-select ORG_ID from PER_ORG where ORG_NAME='@98'";		// ORG_ID_3
	$column_map[] = "sql-n-d-select ORG_ID from PER_ORG where ORG_NAME='@99'";		// ORG_ID_4
	$column_map[] = "sql-n-d-select ORG_ID from PER_ORG where ORG_NAME='@100'";	// ORG_ID_5
	$column_map[] = "101";
	$column_map[] = "func-s-e-save_date(@102)";		// UNIONDATE2
	$column_map[] = "103";
	$column_map[] = "func-s-e-save_date(@104)";		// UNOINDATE3
	$column_map[] = "105";
	$column_map[] = "func-s-e-save_date(@106)";		// UNIONDATE4
	$column_map[] = "107";
	$column_map[] = "func-s-e-save_date(@108)";		// UNIONDATE5
	$column_map[] = "109";
	$column_map[] = "110";
	$column_map[] = "111";
	$column_map[] = "sql-n-d-select ORG_ID from PER_ORG where ORG_NAME='@112'";

	// �����Ũҡ form
/*	$screenform[] = "selectlistfix^value=1|����Ҫ���|selected^name=sel_pertype^label=�������ؤ��ҡ� : ";	// text  radio  checkbox  selectlist  datecalendar
	$screenform[] = "text^value=func|thisyear^size=10^name=txt_year^label=�է�����ҳ : ";
	$screenform[] = "radio^value=1|���駷�� 1|checked,2|���駷�� 2^name=radio_part";
	$screenform[] = "checkbox^value=1|�繻���ѵԡ���Ѻ�Թ��͹����ش|checked^name=chk_lastdate^onclick=check_adate^+";	// + ��͡ѹ�Ѻ��¡�öѴ�
	$screenform[] = "textcalandar^size=10^name=txtc_lastdate^label=[ �ѹ�������ش ] :���";
	$screenform[] = "text^value=^size=20^name=txt_docu^label=������Ţ��� : ";
	$screenform[] = "textcalandar^size=10^name=txtc_docudate^label=�ѹ����������ŧ���㹤���� : ";
*/

	function check_per_type($pertype) {
		if ($pertype>="1" && $pertype<="5") {
			return $pertype;
		} else if ($pertype=="����Ҫ���") {
			return "1";
		} else if ($pertype=="��ѡ�ҹ�Ҫ���") {
			return "2";
		} else if ($pertype=="�١��ҧ���Ǥ���") {
			return "3";
		} else {
			return "**** �������١��ͧ *****";
		}
	}
	
	function check_gender($gender) {
		if ($gender=="M" || $gender=="F") {
			return $gender;
		} else if ($gender=="���") {
			return "M";
		} else if ($gender=="˭ԧ") {
			return "F";
		} else {
			return "**** �������١��ͧ *****";
		}
	}

	function check_blood($blood) {
		$blood = strtoupper($blood);
		if ($blood=="AB" || $blood=="A" || $blood=="B" || $blood=="O") {
			return $blood;
		} else {
			return "**** ������١��ͧ *****";
		}
	}
	
?>
