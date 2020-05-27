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

		$cmd = " SELECT [����-ʡ��] AS FULLNAME, [��������Ǫҭ�����1] AS SKILL1, [��������Ǫҭ�����2] AS SKILL2, [��������Ǫҭ�����3] AS SKILL3 
						FROM DATASES 
						WHERE [��������Ǫҭ�����1] IS NOT NULL AND [��������Ǫҭ�����1] <> '����к�' AND [��������Ǫҭ�����1] <> '����кآ�����' AND [��������Ǫҭ�����1] <> '����駢�����' ";
//						WHERE [��������Ǫҭ�����1] IS NOT NULL ";
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
					$arr_temp = explode("�鹷ҧ", $SKILL11);
					$SS_NAME = trim($arr_temp[0]);
					$arr_temp = explode("��", $SS_NAME);
					$SS_NAME = trim($arr_temp[0]);
					$SPS_EMPHASIZE = "�鹷ҧ".$arr_temp[1];

					$SS_CODE = "";
					if ($SS_NAME=="��ҹ���ᾷ���ʵ��" || $SS_NAME=="��ҹ���ᾷ����ʵ��" || $SS_NAME=="��ҹᾷ���ʵ��" || $SS_NAME=="��ҹ���ᾷ��" || $SS_NAME=="��ҹ�ѹ�ᾷ���ʵ��" || $SS_NAME=="��ҹ���Ѫ��ʵ��" || $SS_NAME=="��ҹ�Ҹ�ó�آ��ʵ������Է����ʵ����ᾷ��" || $SS_NAME=="��ҹ���ᾷ���ʵ������Ҹ�ó�آ" || $SS_NAME=="��ҹ��Һ����ʵ��" || $SS_NAME=="��ҹ���ᾷ���ʵ�� 1 (��ҹ����á���������Ǫ���� �ѡ�� � �� ���ԡ ����ҷ�Է����ШԵ�Ǫ ��ҸԵ��Է��)" || $SS_NAME=="��ҹᾷ���ʵ�� 3 (��ҹ�Ǫ������ͧ�ѹ �Ҹ�ó�آ �Ǫ������鹿�)" || $SS_NAME=="���ᾷ���ʵ�� 3(��ҹ�Ǫ������ͧ�ѹ �Ҹ�ó�آ �Ǫ������鹿�)" || $SS_NAME=="��ҹ�Ҹ�ó�آ���ҵ������Է����ʵ����ᾷ��" || $SS_NAME=="��ҹ���ᾷ������Ҹ���آ" || $SS_NAME=="��ҹ���ᾷ��(��ҹ����á���������Ǫ���� �ѡ�� �ʵ �� ���ԡ ����ҷ�Է����ШԵ�Ǫ ��Ҹ� ���Է��)") $SS_CODE = "002";
					elseif ($SS_NAME=="��ҹ������ͧ ��û���ͧ" || $SS_NAME=="��ҹ��û���ͧ������ͧ") $SS_CODE = "011";
					elseif ($SS_NAME=="��ҹ����ɵ�" || $SS_NAME=="��ҹ����ɵ� (��ҹ�Թ ���� �ת������ � �������Ǣ�ͧ)" || $SS_NAME=="��ҹ����ɵ� (��ҹ�Թ ���� �ת ������� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ����ɵ� (��ҹ�ѵ�캡 �ѵ���� ������� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ����ɵ� (��ҹ�ѵ�캡 �ѵ���� �������������Ǣ�ͧ)" || $SS_NAME=="��ҹ�ɵ� ��êŻ�зҹ" || $SS_NAME=="��ҹ�����������ɵ� ��д�ҹ�����èѴ����ѵ�پת" || $SS_NAME=="��ҹ�ɵ�/���� ������ѡ�Ҿת") $SS_CODE = "003";
					elseif ($SS_NAME=="��ҹ�ѧ�� ����� �ç�ҹ" || $SS_NAME=="��ҹ�ѧ��������ç�ҹ") $SS_CODE = "009";
					elseif ($SS_NAME=="��ҹ�Է����ʵ��" || $SS_NAME=="��ҹ෤�����") $SS_CODE = "005";
					elseif ($SS_NAME=="��ҹ��ú�����" || $SS_NAME=="��ҹ�����èѴ�����к����ø�áԨ" || $SS_NAME=="��ҹ��ú�������к����ø�áԨ" || $SS_NAME=="��ҹ��ú����èѴ��� ��к����ø�áԨ") $SS_CODE = "016";
					elseif ($SS_NAME=="��ҹ����Թ��ä�ѧ������ҳ" || $SS_NAME=="��ҹ���ɰ��ʵ����С�ä�ѧ" || $SS_NAME=="��ҹ����Թ��С�úѭ��" || $SS_NAME=="��ҹ����Թ ��ä�ѧ ������ҳ �ͧ��ѧ") $SS_CODE = "008";
					elseif ($SS_NAME=="��ҹ���ǡ�����ʵ��" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸� ������ � �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸������� � �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ����ͧ�� 俿�� ����ͧ��� ��������� ��ˡ�� �ص��ˡ���)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸� ��д�ҹ���� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸� ������� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ��� (��ҹ�¸������� � �������Ǣ�ͧ)" || $SS_NAME=="���ǡ���") $SS_CODE = "006";
					elseif ($SS_NAME=="��ҹ��º�����Ἱ" || $SS_NAME=="��ҹ��þѲ�ҷ�Ѿ�ҡúؤ��" || $SS_NAME=="��ҹ����ҧἹ" || $SS_NAME=="��ҹ�ҧἹ�Ѳ��" || $SS_NAME=="��ҹ����ҧἹ��оѲ��") $SS_CODE = "013";
					elseif ($SS_NAME=="��ҹ�صع����Է��" || $SS_NAME=="��ҹ�����ҵ��������Ǵ����" || $SS_NAME=="��ҹ��Ѿ�ҡø����ҵ�����Ҹ�ó�آ") $SS_CODE = "004";
					elseif ($SS_NAME=="��ҹ����֡��" || $SS_NAME=="��ҹ����֡�� ��Ż��ʵ�� ����ѧ����ʵ��" || $SS_NAME=="��ҹ����֡����С���ҧἹ����֡�ҹ͡�к�") $SS_CODE = "001";
					elseif ($SS_NAME=="��ҹʶһѵ¡�����ʵ��") $SS_CODE = "007";
					elseif ($SS_NAME=="��ҹ����" || $SS_NAME=="��ҹ������ʵ��" || $SS_NAME=="��ҹ��Ż���Ѳ����������ʹ�" || $SS_NAME=="��ҹ��Ż�Ѳ����� �����ʹ�") $SS_CODE = "012";
					elseif ($SS_NAME=="��ҹ��þѲ���ص��ˡ���") $SS_CODE = "021";
					elseif ($SS_NAME=="��ҹ��ҧ�����" || $SS_NAME=="��ҹ��õ�ҧ�����") $SS_CODE = "020";
					elseif ($SS_NAME=="��ҹ��Ъ�����ѹ��") $SS_CODE = "017";

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
					$arr_temp = explode("�鹷ҧ", $SKILL12);
					$SS_NAME = trim($arr_temp[0]);
					$arr_temp = explode("��", $SS_NAME);
					$SS_NAME = trim($arr_temp[0]);
					$SPS_EMPHASIZE = "�鹷ҧ".$arr_temp[1];

					$SS_CODE = "";
					if ($SS_NAME=="��ҹ���ᾷ���ʵ��" || $SS_NAME=="��ҹ���ᾷ����ʵ��" || $SS_NAME=="��ҹᾷ���ʵ��" || $SS_NAME=="��ҹ���ᾷ��" || $SS_NAME=="��ҹ�ѹ�ᾷ���ʵ��" || $SS_NAME=="��ҹ���Ѫ��ʵ��" || $SS_NAME=="��ҹ�Ҹ�ó�آ��ʵ������Է����ʵ����ᾷ��" || $SS_NAME=="��ҹ���ᾷ���ʵ������Ҹ�ó�آ" || $SS_NAME=="��ҹ��Һ����ʵ��" || $SS_NAME=="��ҹ���ᾷ���ʵ�� 1 (��ҹ����á���������Ǫ���� �ѡ�� � �� ���ԡ ����ҷ�Է����ШԵ�Ǫ ��ҸԵ��Է��)" || $SS_NAME=="��ҹᾷ���ʵ�� 3 (��ҹ�Ǫ������ͧ�ѹ �Ҹ�ó�آ �Ǫ������鹿�)" || $SS_NAME=="���ᾷ���ʵ�� 3(��ҹ�Ǫ������ͧ�ѹ �Ҹ�ó�آ �Ǫ������鹿�)" || $SS_NAME=="��ҹ�Ҹ�ó�آ���ҵ������Է����ʵ����ᾷ��" || $SS_NAME=="��ҹ���ᾷ������Ҹ���آ") $SS_CODE = "002";
					elseif ($SS_NAME=="��ҹ������ͧ ��û���ͧ" || $SS_NAME=="��ҹ��û���ͧ������ͧ") $SS_CODE = "011";
					elseif ($SS_NAME=="��ҹ����ɵ�" || $SS_NAME=="��ҹ����ɵ� (��ҹ�Թ ���� �ת������ � �������Ǣ�ͧ)" || $SS_NAME=="��ҹ����ɵ� (��ҹ�Թ ���� �ת ������� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ����ɵ� (��ҹ�ѵ�캡 �ѵ���� ������� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ����ɵ� (��ҹ�ѵ�캡 �ѵ���� �������������Ǣ�ͧ)" || $SS_NAME=="��ҹ�ɵ� ��êŻ�зҹ" || $SS_NAME=="��ҹ�����������ɵ� ��д�ҹ�����èѴ����ѵ�پת" || $SS_NAME=="��ҹ�ɵ�/���� ������ѡ�Ҿת") $SS_CODE = "003";
					elseif ($SS_NAME=="��ҹ�ѧ�� ����� �ç�ҹ" || $SS_NAME=="��ҹ�ѧ��������ç�ҹ") $SS_CODE = "009";
					elseif ($SS_NAME=="��ҹ�Է����ʵ��" || $SS_NAME=="��ҹ෤�����") $SS_CODE = "005";
					elseif ($SS_NAME=="��ҹ��ú�����" || $SS_NAME=="��ҹ�����èѴ�����к����ø�áԨ") $SS_CODE = "016";
					elseif ($SS_NAME=="��ҹ����Թ��ä�ѧ������ҳ" || $SS_NAME=="��ҹ���ɰ��ʵ����С�ä�ѧ" || $SS_NAME=="��ҹ����Թ��С�úѭ��" || $SS_NAME=="��ҹ����Թ ��ä�ѧ ������ҳ �ͧ��ѧ") $SS_CODE = "008";
					elseif ($SS_NAME=="��ҹ���ǡ�����ʵ��" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸� ������ � �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸������� � �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ����ͧ�� 俿�� ����ͧ��� ��������� ��ˡ�� �ص��ˡ���)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸� ��д�ҹ���� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸� ������� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ��� (��ҹ�¸������� � �������Ǣ�ͧ)") $SS_CODE = "006";
					elseif ($SS_NAME=="��ҹ��º�����Ἱ" || $SS_NAME=="��ҹ��þѲ�ҷ�Ѿ�ҡúؤ��" || $SS_NAME=="��ҹ����ҧἹ" || $SS_NAME=="��ҹ�ҧἹ�Ѳ��" || $SS_NAME=="��ҹ����ҧἹ��оѲ��") $SS_CODE = "013";
					elseif ($SS_NAME=="��ҹ�صع����Է��" || $SS_NAME=="��ҹ�����ҵ��������Ǵ����" || $SS_NAME=="��ҹ��Ѿ�ҡø����ҵ�����Ҹ�ó�آ") $SS_CODE = "004";
					elseif ($SS_NAME=="��ҹ����֡��" || $SS_NAME=="��ҹ����֡�� ��Ż��ʵ�� ����ѧ����ʵ��" || $SS_NAME=="��ҹ����֡����С���ҧἹ����֡�ҹ͡�к�") $SS_CODE = "001";
					elseif ($SS_NAME=="��ҹʶһѵ¡�����ʵ��") $SS_CODE = "007";
					elseif ($SS_NAME=="��ҹ����") $SS_CODE = "012";
					elseif ($SS_NAME=="��ҹ��þѲ���ص��ˡ���") $SS_CODE = "021";
					elseif ($SS_NAME=="��ҹ���ᾷ���ʵ��") $SS_CODE = "011";
					elseif ($SS_NAME=="��ҹ���ᾷ���ʵ��") $SS_CODE = "011";

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
					$arr_temp = explode("�鹷ҧ", $SKILL13);
					$SS_NAME = trim($arr_temp[0]);
					$arr_temp = explode("��", $SS_NAME);
					$SS_NAME = trim($arr_temp[0]);
					$SPS_EMPHASIZE = "�鹷ҧ".$arr_temp[1];

					$SS_CODE = "";
					if ($SS_NAME=="��ҹ���ᾷ���ʵ��" || $SS_NAME=="��ҹ���ᾷ����ʵ��" || $SS_NAME=="��ҹᾷ���ʵ��" || $SS_NAME=="��ҹ���ᾷ��" || $SS_NAME=="��ҹ�ѹ�ᾷ���ʵ��" || $SS_NAME=="��ҹ���Ѫ��ʵ��" || $SS_NAME=="��ҹ�Ҹ�ó�آ��ʵ������Է����ʵ����ᾷ��" || $SS_NAME=="��ҹ���ᾷ���ʵ������Ҹ�ó�آ" || $SS_NAME=="��ҹ��Һ����ʵ��" || $SS_NAME=="��ҹ���ᾷ���ʵ�� 1 (��ҹ����á���������Ǫ���� �ѡ�� � �� ���ԡ ����ҷ�Է����ШԵ�Ǫ ��ҸԵ��Է��)" || $SS_NAME=="��ҹᾷ���ʵ�� 3 (��ҹ�Ǫ������ͧ�ѹ �Ҹ�ó�آ �Ǫ������鹿�)" || $SS_NAME=="���ᾷ���ʵ�� 3(��ҹ�Ǫ������ͧ�ѹ �Ҹ�ó�آ �Ǫ������鹿�)" || $SS_NAME=="��ҹ�Ҹ�ó�آ���ҵ������Է����ʵ����ᾷ��" || $SS_NAME=="��ҹ���ᾷ������Ҹ���آ") $SS_CODE = "002";
					elseif ($SS_NAME=="��ҹ������ͧ ��û���ͧ" || $SS_NAME=="��ҹ��û���ͧ������ͧ") $SS_CODE = "011";
					elseif ($SS_NAME=="��ҹ����ɵ�" || $SS_NAME=="��ҹ����ɵ� (��ҹ�Թ ���� �ת������ � �������Ǣ�ͧ)" || $SS_NAME=="��ҹ����ɵ� (��ҹ�Թ ���� �ת ������� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ����ɵ� (��ҹ�ѵ�캡 �ѵ���� ������� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ����ɵ� (��ҹ�ѵ�캡 �ѵ���� �������������Ǣ�ͧ)" || $SS_NAME=="��ҹ�ɵ� ��êŻ�зҹ" || $SS_NAME=="��ҹ�����������ɵ� ��д�ҹ�����èѴ����ѵ�پת" || $SS_NAME=="��ҹ�ɵ�/���� ������ѡ�Ҿת") $SS_CODE = "003";
					elseif ($SS_NAME=="��ҹ�ѧ�� ����� �ç�ҹ" || $SS_NAME=="��ҹ�ѧ��������ç�ҹ") $SS_CODE = "009";
					elseif ($SS_NAME=="��ҹ�Է����ʵ��" || $SS_NAME=="��ҹ෤�����") $SS_CODE = "005";
					elseif ($SS_NAME=="��ҹ��ú�����" || $SS_NAME=="��ҹ�����èѴ�����к����ø�áԨ") $SS_CODE = "016";
					elseif ($SS_NAME=="��ҹ����Թ��ä�ѧ������ҳ" || $SS_NAME=="��ҹ���ɰ��ʵ����С�ä�ѧ" || $SS_NAME=="��ҹ����Թ��С�úѭ��" || $SS_NAME=="��ҹ����Թ ��ä�ѧ ������ҳ �ͧ��ѧ") $SS_CODE = "008";
					elseif ($SS_NAME=="��ҹ���ǡ�����ʵ��" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸� ������ � �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸������� � �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ����ͧ�� 俿�� ����ͧ��� ��������� ��ˡ�� �ص��ˡ���)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸� ��д�ҹ���� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸� ������� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ��� (��ҹ�¸������� � �������Ǣ�ͧ)") $SS_CODE = "006";
					elseif ($SS_NAME=="��ҹ��º�����Ἱ" || $SS_NAME=="��ҹ��þѲ�ҷ�Ѿ�ҡúؤ��" || $SS_NAME=="��ҹ����ҧἹ" || $SS_NAME=="��ҹ�ҧἹ�Ѳ��" || $SS_NAME=="��ҹ����ҧἹ��оѲ��") $SS_CODE = "013";
					elseif ($SS_NAME=="��ҹ�صع����Է��" || $SS_NAME=="��ҹ�����ҵ��������Ǵ����" || $SS_NAME=="��ҹ��Ѿ�ҡø����ҵ�����Ҹ�ó�آ") $SS_CODE = "004";
					elseif ($SS_NAME=="��ҹ����֡��" || $SS_NAME=="��ҹ����֡�� ��Ż��ʵ�� ����ѧ����ʵ��" || $SS_NAME=="��ҹ����֡����С���ҧἹ����֡�ҹ͡�к�") $SS_CODE = "001";
					elseif ($SS_NAME=="��ҹʶһѵ¡�����ʵ��") $SS_CODE = "007";
					elseif ($SS_NAME=="��ҹ����") $SS_CODE = "012";
					elseif ($SS_NAME=="��ҹ��þѲ���ص��ˡ���") $SS_CODE = "021";
					elseif ($SS_NAME=="��ҹ���ᾷ���ʵ��") $SS_CODE = "011";
					elseif ($SS_NAME=="��ҹ���ᾷ���ʵ��") $SS_CODE = "011";

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
					$arr_temp = explode("�鹷ҧ", $SKILL14);
					$SS_NAME = trim($arr_temp[0]);
					$arr_temp = explode("��", $SS_NAME);
					$SS_NAME = trim($arr_temp[0]);
					$SPS_EMPHASIZE = "�鹷ҧ".$arr_temp[1];

					$SS_CODE = "";
					if ($SS_NAME=="��ҹ���ᾷ���ʵ��" || $SS_NAME=="��ҹ���ᾷ����ʵ��" || $SS_NAME=="��ҹᾷ���ʵ��" || $SS_NAME=="��ҹ���ᾷ��" || $SS_NAME=="��ҹ�ѹ�ᾷ���ʵ��" || $SS_NAME=="��ҹ���Ѫ��ʵ��" || $SS_NAME=="��ҹ�Ҹ�ó�آ��ʵ������Է����ʵ����ᾷ��" || $SS_NAME=="��ҹ���ᾷ���ʵ������Ҹ�ó�آ" || $SS_NAME=="��ҹ��Һ����ʵ��" || $SS_NAME=="��ҹ���ᾷ���ʵ�� 1 (��ҹ����á���������Ǫ���� �ѡ�� � �� ���ԡ ����ҷ�Է����ШԵ�Ǫ ��ҸԵ��Է��)" || $SS_NAME=="��ҹᾷ���ʵ�� 3 (��ҹ�Ǫ������ͧ�ѹ �Ҹ�ó�آ �Ǫ������鹿�)" || $SS_NAME=="���ᾷ���ʵ�� 3(��ҹ�Ǫ������ͧ�ѹ �Ҹ�ó�آ �Ǫ������鹿�)" || $SS_NAME=="��ҹ�Ҹ�ó�آ���ҵ������Է����ʵ����ᾷ��" || $SS_NAME=="��ҹ���ᾷ������Ҹ���آ") $SS_CODE = "002";
					elseif ($SS_NAME=="��ҹ������ͧ ��û���ͧ" || $SS_NAME=="��ҹ��û���ͧ������ͧ") $SS_CODE = "011";
					elseif ($SS_NAME=="��ҹ����ɵ�" || $SS_NAME=="��ҹ����ɵ� (��ҹ�Թ ���� �ת������ � �������Ǣ�ͧ)" || $SS_NAME=="��ҹ����ɵ� (��ҹ�Թ ���� �ת ������� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ����ɵ� (��ҹ�ѵ�캡 �ѵ���� ������� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ����ɵ� (��ҹ�ѵ�캡 �ѵ���� �������������Ǣ�ͧ)" || $SS_NAME=="��ҹ�ɵ� ��êŻ�зҹ" || $SS_NAME=="��ҹ�����������ɵ� ��д�ҹ�����èѴ����ѵ�پת" || $SS_NAME=="��ҹ�ɵ�/���� ������ѡ�Ҿת") $SS_CODE = "003";
					elseif ($SS_NAME=="��ҹ�ѧ�� ����� �ç�ҹ" || $SS_NAME=="��ҹ�ѧ��������ç�ҹ") $SS_CODE = "009";
					elseif ($SS_NAME=="��ҹ�Է����ʵ��" || $SS_NAME=="��ҹ෤�����") $SS_CODE = "005";
					elseif ($SS_NAME=="��ҹ��ú�����" || $SS_NAME=="��ҹ�����èѴ�����к����ø�áԨ") $SS_CODE = "016";
					elseif ($SS_NAME=="��ҹ����Թ��ä�ѧ������ҳ" || $SS_NAME=="��ҹ���ɰ��ʵ����С�ä�ѧ" || $SS_NAME=="��ҹ����Թ��С�úѭ��" || $SS_NAME=="��ҹ����Թ ��ä�ѧ ������ҳ �ͧ��ѧ") $SS_CODE = "008";
					elseif ($SS_NAME=="��ҹ���ǡ�����ʵ��" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸� ������ � �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸������� � �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ����ͧ�� 俿�� ����ͧ��� ��������� ��ˡ�� �ص��ˡ���)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸� ��д�ҹ���� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸� ������� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ��� (��ҹ�¸������� � �������Ǣ�ͧ)") $SS_CODE = "006";
					elseif ($SS_NAME=="��ҹ��º�����Ἱ" || $SS_NAME=="��ҹ��þѲ�ҷ�Ѿ�ҡúؤ��" || $SS_NAME=="��ҹ����ҧἹ" || $SS_NAME=="��ҹ�ҧἹ�Ѳ��" || $SS_NAME=="��ҹ����ҧἹ��оѲ��") $SS_CODE = "013";
					elseif ($SS_NAME=="��ҹ�صع����Է��" || $SS_NAME=="��ҹ�����ҵ��������Ǵ����" || $SS_NAME=="��ҹ��Ѿ�ҡø����ҵ�����Ҹ�ó�آ") $SS_CODE = "004";
					elseif ($SS_NAME=="��ҹ����֡��" || $SS_NAME=="��ҹ����֡�� ��Ż��ʵ�� ����ѧ����ʵ��" || $SS_NAME=="��ҹ����֡����С���ҧἹ����֡�ҹ͡�к�") $SS_CODE = "001";
					elseif ($SS_NAME=="��ҹʶһѵ¡�����ʵ��") $SS_CODE = "007";
					elseif ($SS_NAME=="��ҹ����") $SS_CODE = "012";
					elseif ($SS_NAME=="��ҹ��þѲ���ص��ˡ���") $SS_CODE = "021";
					elseif ($SS_NAME=="��ҹ���ᾷ���ʵ��") $SS_CODE = "011";
					elseif ($SS_NAME=="��ҹ���ᾷ���ʵ��") $SS_CODE = "011";

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
					$arr_temp = explode("�鹷ҧ", $SKILL15);
					$SS_NAME = trim($arr_temp[0]);
					$arr_temp = explode("��", $SS_NAME);
					$SS_NAME = trim($arr_temp[0]);
					$SPS_EMPHASIZE = "�鹷ҧ".$arr_temp[1];

					$SS_CODE = "";
					if ($SS_NAME=="��ҹ���ᾷ���ʵ��" || $SS_NAME=="��ҹ���ᾷ����ʵ��" || $SS_NAME=="��ҹᾷ���ʵ��" || $SS_NAME=="��ҹ���ᾷ��" || $SS_NAME=="��ҹ�ѹ�ᾷ���ʵ��" || $SS_NAME=="��ҹ���Ѫ��ʵ��" || $SS_NAME=="��ҹ�Ҹ�ó�آ��ʵ������Է����ʵ����ᾷ��" || $SS_NAME=="��ҹ���ᾷ���ʵ������Ҹ�ó�آ" || $SS_NAME=="��ҹ��Һ����ʵ��" || $SS_NAME=="��ҹ���ᾷ���ʵ�� 1 (��ҹ����á���������Ǫ���� �ѡ�� � �� ���ԡ ����ҷ�Է����ШԵ�Ǫ ��ҸԵ��Է��)" || $SS_NAME=="��ҹᾷ���ʵ�� 3 (��ҹ�Ǫ������ͧ�ѹ �Ҹ�ó�آ �Ǫ������鹿�)" || $SS_NAME=="���ᾷ���ʵ�� 3(��ҹ�Ǫ������ͧ�ѹ �Ҹ�ó�آ �Ǫ������鹿�)" || $SS_NAME=="��ҹ�Ҹ�ó�آ���ҵ������Է����ʵ����ᾷ��" || $SS_NAME=="��ҹ���ᾷ������Ҹ���آ") $SS_CODE = "002";
					elseif ($SS_NAME=="��ҹ������ͧ ��û���ͧ" || $SS_NAME=="��ҹ��û���ͧ������ͧ") $SS_CODE = "011";
					elseif ($SS_NAME=="��ҹ����ɵ�" || $SS_NAME=="��ҹ����ɵ� (��ҹ�Թ ���� �ת������ � �������Ǣ�ͧ)" || $SS_NAME=="��ҹ����ɵ� (��ҹ�Թ ���� �ת ������� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ����ɵ� (��ҹ�ѵ�캡 �ѵ���� ������� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ����ɵ� (��ҹ�ѵ�캡 �ѵ���� �������������Ǣ�ͧ)" || $SS_NAME=="��ҹ�ɵ� ��êŻ�зҹ" || $SS_NAME=="��ҹ�����������ɵ� ��д�ҹ�����èѴ����ѵ�پת" || $SS_NAME=="��ҹ�ɵ�/���� ������ѡ�Ҿת") $SS_CODE = "003";
					elseif ($SS_NAME=="��ҹ�ѧ�� ����� �ç�ҹ" || $SS_NAME=="��ҹ�ѧ��������ç�ҹ") $SS_CODE = "009";
					elseif ($SS_NAME=="��ҹ�Է����ʵ��" || $SS_NAME=="��ҹ෤�����") $SS_CODE = "005";
					elseif ($SS_NAME=="��ҹ��ú�����" || $SS_NAME=="��ҹ�����èѴ�����к����ø�áԨ") $SS_CODE = "016";
					elseif ($SS_NAME=="��ҹ����Թ��ä�ѧ������ҳ" || $SS_NAME=="��ҹ���ɰ��ʵ����С�ä�ѧ" || $SS_NAME=="��ҹ����Թ��С�úѭ��" || $SS_NAME=="��ҹ����Թ ��ä�ѧ ������ҳ �ͧ��ѧ") $SS_CODE = "008";
					elseif ($SS_NAME=="��ҹ���ǡ�����ʵ��" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸� ������ � �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸������� � �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ����ͧ�� 俿�� ����ͧ��� ��������� ��ˡ�� �ص��ˡ���)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸� ��д�ҹ���� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸� ������� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ��� (��ҹ�¸������� � �������Ǣ�ͧ)") $SS_CODE = "006";
					elseif ($SS_NAME=="��ҹ��º�����Ἱ" || $SS_NAME=="��ҹ��þѲ�ҷ�Ѿ�ҡúؤ��" || $SS_NAME=="��ҹ����ҧἹ" || $SS_NAME=="��ҹ�ҧἹ�Ѳ��" || $SS_NAME=="��ҹ����ҧἹ��оѲ��") $SS_CODE = "013";
					elseif ($SS_NAME=="��ҹ�صع����Է��" || $SS_NAME=="��ҹ�����ҵ��������Ǵ����" || $SS_NAME=="��ҹ��Ѿ�ҡø����ҵ�����Ҹ�ó�آ") $SS_CODE = "004";
					elseif ($SS_NAME=="��ҹ����֡��" || $SS_NAME=="��ҹ����֡�� ��Ż��ʵ�� ����ѧ����ʵ��" || $SS_NAME=="��ҹ����֡����С���ҧἹ����֡�ҹ͡�к�") $SS_CODE = "001";
					elseif ($SS_NAME=="��ҹʶһѵ¡�����ʵ��") $SS_CODE = "007";
					elseif ($SS_NAME=="��ҹ����") $SS_CODE = "012";
					elseif ($SS_NAME=="��ҹ��þѲ���ص��ˡ���") $SS_CODE = "021";
					elseif ($SS_NAME=="��ҹ���ᾷ���ʵ��") $SS_CODE = "011";
					elseif ($SS_NAME=="��ҹ���ᾷ���ʵ��") $SS_CODE = "011";

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
					$arr_temp = explode("�鹷ҧ", $SKILL16);
					$SS_NAME = trim($arr_temp[0]);
					$arr_temp = explode("��", $SS_NAME);
					$SS_NAME = trim($arr_temp[0]);
					$SPS_EMPHASIZE = "�鹷ҧ".$arr_temp[1];

					$SS_CODE = "";
					if ($SS_NAME=="��ҹ���ᾷ���ʵ��" || $SS_NAME=="��ҹ���ᾷ����ʵ��" || $SS_NAME=="��ҹᾷ���ʵ��" || $SS_NAME=="��ҹ���ᾷ��" || $SS_NAME=="��ҹ�ѹ�ᾷ���ʵ��" || $SS_NAME=="��ҹ���Ѫ��ʵ��" || $SS_NAME=="��ҹ�Ҹ�ó�آ��ʵ������Է����ʵ����ᾷ��" || $SS_NAME=="��ҹ���ᾷ���ʵ������Ҹ�ó�آ" || $SS_NAME=="��ҹ��Һ����ʵ��" || $SS_NAME=="��ҹ���ᾷ���ʵ�� 1 (��ҹ����á���������Ǫ���� �ѡ�� � �� ���ԡ ����ҷ�Է����ШԵ�Ǫ ��ҸԵ��Է��)" || $SS_NAME=="��ҹᾷ���ʵ�� 3 (��ҹ�Ǫ������ͧ�ѹ �Ҹ�ó�آ �Ǫ������鹿�)" || $SS_NAME=="���ᾷ���ʵ�� 3(��ҹ�Ǫ������ͧ�ѹ �Ҹ�ó�آ �Ǫ������鹿�)" || $SS_NAME=="��ҹ�Ҹ�ó�آ���ҵ������Է����ʵ����ᾷ��" || $SS_NAME=="��ҹ���ᾷ������Ҹ���آ") $SS_CODE = "002";
					elseif ($SS_NAME=="��ҹ������ͧ ��û���ͧ" || $SS_NAME=="��ҹ��û���ͧ������ͧ") $SS_CODE = "011";
					elseif ($SS_NAME=="��ҹ����ɵ�" || $SS_NAME=="��ҹ����ɵ� (��ҹ�Թ ���� �ת������ � �������Ǣ�ͧ)" || $SS_NAME=="��ҹ����ɵ� (��ҹ�Թ ���� �ת ������� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ����ɵ� (��ҹ�ѵ�캡 �ѵ���� ������� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ����ɵ� (��ҹ�ѵ�캡 �ѵ���� �������������Ǣ�ͧ)" || $SS_NAME=="��ҹ�ɵ� ��êŻ�зҹ" || $SS_NAME=="��ҹ�����������ɵ� ��д�ҹ�����èѴ����ѵ�پת" || $SS_NAME=="��ҹ�ɵ�/���� ������ѡ�Ҿת") $SS_CODE = "003";
					elseif ($SS_NAME=="��ҹ�ѧ�� ����� �ç�ҹ" || $SS_NAME=="��ҹ�ѧ��������ç�ҹ") $SS_CODE = "009";
					elseif ($SS_NAME=="��ҹ�Է����ʵ��" || $SS_NAME=="��ҹ෤�����") $SS_CODE = "005";
					elseif ($SS_NAME=="��ҹ��ú�����" || $SS_NAME=="��ҹ�����èѴ�����к����ø�áԨ") $SS_CODE = "016";
					elseif ($SS_NAME=="��ҹ����Թ��ä�ѧ������ҳ" || $SS_NAME=="��ҹ���ɰ��ʵ����С�ä�ѧ" || $SS_NAME=="��ҹ����Թ��С�úѭ��" || $SS_NAME=="��ҹ����Թ ��ä�ѧ ������ҳ �ͧ��ѧ") $SS_CODE = "008";
					elseif ($SS_NAME=="��ҹ���ǡ�����ʵ��" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸� ������ � �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸������� � �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ����ͧ�� 俿�� ����ͧ��� ��������� ��ˡ�� �ص��ˡ���)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸� ��д�ҹ���� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸� ������� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ��� (��ҹ�¸������� � �������Ǣ�ͧ)") $SS_CODE = "006";
					elseif ($SS_NAME=="��ҹ��º�����Ἱ" || $SS_NAME=="��ҹ��þѲ�ҷ�Ѿ�ҡúؤ��" || $SS_NAME=="��ҹ����ҧἹ" || $SS_NAME=="��ҹ�ҧἹ�Ѳ��" || $SS_NAME=="��ҹ����ҧἹ��оѲ��") $SS_CODE = "013";
					elseif ($SS_NAME=="��ҹ�صع����Է��" || $SS_NAME=="��ҹ�����ҵ��������Ǵ����" || $SS_NAME=="��ҹ��Ѿ�ҡø����ҵ�����Ҹ�ó�آ") $SS_CODE = "004";
					elseif ($SS_NAME=="��ҹ����֡��" || $SS_NAME=="��ҹ����֡�� ��Ż��ʵ�� ����ѧ����ʵ��" || $SS_NAME=="��ҹ����֡����С���ҧἹ����֡�ҹ͡�к�") $SS_CODE = "001";
					elseif ($SS_NAME=="��ҹʶһѵ¡�����ʵ��") $SS_CODE = "007";
					elseif ($SS_NAME=="��ҹ����") $SS_CODE = "012";
					elseif ($SS_NAME=="��ҹ��þѲ���ص��ˡ���") $SS_CODE = "021";
					elseif ($SS_NAME=="��ҹ���ᾷ���ʵ��") $SS_CODE = "011";
					elseif ($SS_NAME=="��ҹ���ᾷ���ʵ��") $SS_CODE = "011";

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
					$arr_temp = explode("�鹷ҧ", $SKILL17);
					$SS_NAME = trim($arr_temp[0]);
					$arr_temp = explode("��", $SS_NAME);
					$SS_NAME = trim($arr_temp[0]);
					$SPS_EMPHASIZE = "�鹷ҧ".$arr_temp[1];

					$SS_CODE = "";
					if ($SS_NAME=="��ҹ���ᾷ���ʵ��" || $SS_NAME=="��ҹ���ᾷ����ʵ��" || $SS_NAME=="��ҹᾷ���ʵ��" || $SS_NAME=="��ҹ���ᾷ��" || $SS_NAME=="��ҹ�ѹ�ᾷ���ʵ��" || $SS_NAME=="��ҹ���Ѫ��ʵ��" || $SS_NAME=="��ҹ�Ҹ�ó�آ��ʵ������Է����ʵ����ᾷ��" || $SS_NAME=="��ҹ���ᾷ���ʵ������Ҹ�ó�آ" || $SS_NAME=="��ҹ��Һ����ʵ��" || $SS_NAME=="��ҹ���ᾷ���ʵ�� 1 (��ҹ����á���������Ǫ���� �ѡ�� � �� ���ԡ ����ҷ�Է����ШԵ�Ǫ ��ҸԵ��Է��)" || $SS_NAME=="��ҹᾷ���ʵ�� 3 (��ҹ�Ǫ������ͧ�ѹ �Ҹ�ó�آ �Ǫ������鹿�)" || $SS_NAME=="���ᾷ���ʵ�� 3(��ҹ�Ǫ������ͧ�ѹ �Ҹ�ó�آ �Ǫ������鹿�)" || $SS_NAME=="��ҹ�Ҹ�ó�آ���ҵ������Է����ʵ����ᾷ��" || $SS_NAME=="��ҹ���ᾷ������Ҹ���آ") $SS_CODE = "002";
					elseif ($SS_NAME=="��ҹ������ͧ ��û���ͧ" || $SS_NAME=="��ҹ��û���ͧ������ͧ") $SS_CODE = "011";
					elseif ($SS_NAME=="��ҹ����ɵ�" || $SS_NAME=="��ҹ����ɵ� (��ҹ�Թ ���� �ת������ � �������Ǣ�ͧ)" || $SS_NAME=="��ҹ����ɵ� (��ҹ�Թ ���� �ת ������� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ����ɵ� (��ҹ�ѵ�캡 �ѵ���� ������� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ����ɵ� (��ҹ�ѵ�캡 �ѵ���� �������������Ǣ�ͧ)" || $SS_NAME=="��ҹ�ɵ� ��êŻ�зҹ" || $SS_NAME=="��ҹ�����������ɵ� ��д�ҹ�����èѴ����ѵ�پת" || $SS_NAME=="��ҹ�ɵ�/���� ������ѡ�Ҿת") $SS_CODE = "003";
					elseif ($SS_NAME=="��ҹ�ѧ�� ����� �ç�ҹ" || $SS_NAME=="��ҹ�ѧ��������ç�ҹ") $SS_CODE = "009";
					elseif ($SS_NAME=="��ҹ�Է����ʵ��" || $SS_NAME=="��ҹ෤�����") $SS_CODE = "005";
					elseif ($SS_NAME=="��ҹ��ú�����" || $SS_NAME=="��ҹ�����èѴ�����к����ø�áԨ") $SS_CODE = "016";
					elseif ($SS_NAME=="��ҹ����Թ��ä�ѧ������ҳ" || $SS_NAME=="��ҹ���ɰ��ʵ����С�ä�ѧ" || $SS_NAME=="��ҹ����Թ��С�úѭ��" || $SS_NAME=="��ҹ����Թ ��ä�ѧ ������ҳ �ͧ��ѧ") $SS_CODE = "008";
					elseif ($SS_NAME=="��ҹ���ǡ�����ʵ��" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸� ������ � �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸������� � �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ����ͧ�� 俿�� ����ͧ��� ��������� ��ˡ�� �ص��ˡ���)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸� ��д�ҹ���� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ�����ʵ�� (��ҹ�¸� ������� �������Ǣ�ͧ)" || $SS_NAME=="��ҹ���ǡ��� (��ҹ�¸������� � �������Ǣ�ͧ)") $SS_CODE = "006";
					elseif ($SS_NAME=="��ҹ��º�����Ἱ" || $SS_NAME=="��ҹ��þѲ�ҷ�Ѿ�ҡúؤ��" || $SS_NAME=="��ҹ����ҧἹ" || $SS_NAME=="��ҹ�ҧἹ�Ѳ��" || $SS_NAME=="��ҹ����ҧἹ��оѲ��") $SS_CODE = "013";
					elseif ($SS_NAME=="��ҹ�صع����Է��" || $SS_NAME=="��ҹ�����ҵ��������Ǵ����" || $SS_NAME=="��ҹ��Ѿ�ҡø����ҵ�����Ҹ�ó�آ") $SS_CODE = "004";
					elseif ($SS_NAME=="��ҹ����֡��" || $SS_NAME=="��ҹ����֡�� ��Ż��ʵ�� ����ѧ����ʵ��" || $SS_NAME=="��ҹ����֡����С���ҧἹ����֡�ҹ͡�к�") $SS_CODE = "001";
					elseif ($SS_NAME=="��ҹʶһѵ¡�����ʵ��") $SS_CODE = "007";
					elseif ($SS_NAME=="��ҹ����") $SS_CODE = "012";
					elseif ($SS_NAME=="��ҹ��þѲ���ص��ˡ���") $SS_CODE = "021";
					elseif ($SS_NAME=="��ҹ���ᾷ���ʵ��") $SS_CODE = "011";
					elseif ($SS_NAME=="��ҹ���ᾷ���ʵ��") $SS_CODE = "011";

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
//				echo "����-ʡ�� $FULLNAME<br>";
			} 
		} // end while						
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

	}

	if( $command=='POSITION' ) { // ���˹觢���Ҫ���  
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
				else echo "���͵��˹�㹡�ú����çҹ $TEMPMANAGEPOSITION<br>";
			} 

			if ($TEMPLINE) {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_LINE' AND OLD_CODE = '$TEMPLINE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $PL_CODE = trim($data2[NEW_CODE]);
				else echo "���͵��˹����§ҹ $TEMPLINE<br>";
			} else $PL_CODE = "NULL";

			if ($TEMPPOSITIONTYPE) {
				$cmd = " select NEW_CODE from PER_MAP_CODE 
								  where MAP_CODE = 'PER_TYPE' AND OLD_CODE = '$TEMPPOSITIONTYPE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $PT_CODE = trim($data2[NEW_CODE]);
				else echo "���������˹� $TEMPPOSITIONTYPE<br>";
			} else $PT_CODE = "11";
			if ($TEMPPOSITIONTYPE=="��") $PT_CODE = "22"; 
			elseif ($TEMPPOSITIONTYPE=="��") $PT_CODE = "31"; 
			elseif ($TEMPPOSITIONTYPE=="��") $PT_CODE = "32"; 

			$SKILL_CODE = '';
			if ($TEMPSKILL) {
				$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_SKILL' AND OLD_CODE = '$TEMPSKILL' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				if (trim($data2[NEW_CODE])) $SKILL_CODE = trim($data2[NEW_CODE]);
				else echo "�ҢҤ�������Ǫҭ $TEMPSKILL<br>";
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
			if (!$CL_NAME) $CL_NAME = "��Ժѵԡ��";

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

	if( $command=='PERSONAL' ) { // �����Ţ���Ҫ���
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
			if ($TEMPGENDER=='���') $PER_GENDER = 1;
			elseif ($TEMPGENDER=='˭ԧ') $PER_GENDER = 2;
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