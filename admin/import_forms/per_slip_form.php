<?
//	map text data to table columns

	$table = "PER_SLIP";
	$dup_column = "2|3|4";	// �Ţ�ӴѺ������ҧ㹰ҹ������
	$prime = "0";		// �Ţ�ӴѺ������ҧ㹰ҹ������
	$running = "0";		// �Ţ�ӴѺ������ҧ㹰ҹ������ �������� running ��� = -1
	
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
	
//	$impfile_head_map = array("","","","","","","");	
	$head_map = array("SLIP_ID", "PER_ID", "�շ������Թ��͹","��͹�������Թ��͹","�Ţ��Шӵ�ǻ�ЪҪ�","�ӹ�˹�Ҫ���","����","���ʡ��","����˹��§ҹ�дѺ���","����˹��§ҹ�дѺ�ӹѡ / �ͧ","���ʸ�Ҥ��","���͸�Ҥ��","�����ҢҸ�Ҥ��","�����ҢҸ�Ҥ��","�Ţ���ѭ�ո�Ҥ��(�ѭ���Թ��͹)","�Թ��͹ / ��Ҩ�ҧ��Ш�","�Թ��͹���ԡ / ��Ҩ�ҧ��Шӵ��ԡ","�Թ ���.","�Թ ���. ���ԡ","�.�.�./���ԡ","�.�.�./���ԡ","�.�.�./���ԡ","�.�.�./���ԡ","ʻ�./���ԡ","���./���ԡ","�.�.�.���.","�.�.�.���. ���ԡ","�.�.8-8�.","�.�.8-8�. ���ԡ","�.�.�.1-7/���ԡ","�.�.�.�./���ԡ","�����Һ�ҹ/���ԡ","��������ͺص�/���ԡ","����֡�Һص�/���ԡ","�Թ�ҧ���/�Թ��ҷ��","�����Թ������¡�÷�� 1","�ӹǹ�Թ������¡�÷�� 1","�����Թ������¡�÷�� 2","�ӹǹ�Թ������¡�÷�� 2","�����Թ������¡�÷�� 3","�ӹǹ�Թ������¡�÷�� 3","�����Թ������¡�÷�� 4","�ӹǹ�Թ������¡�÷�� 4","�Թ������� �","����Ѻ�����͹","����/���ԡ","�Թ������ͷ�����������","������ ? �Թ����ˡó�","�Թ������͡���֡��","���./���ԡ","���.��ǹ����/���ԡ","�.�.�.(���.)","�.�.�.(��.)","�.�.�.","�Թ��� ��.","����ҧ��","�Թ���¡�׹","����Ҹ�óٻ���","�Թ���ʴԡ�������","��Ҭһ��Ԩ","��. ʧ������","�����Թ�ѡ��¡�÷�� 1","�ӹǹ�Թ�ѡ��¡�÷�� 1","�����Թ�ѡ��¡�÷�� 2","�ӹǹ�Թ�ѡ��¡�÷�� 2","�����Թ�ѡ��¡�÷�� 3","�ӹǹ�Թ�ѡ��¡�÷�� 3","�����Թ�ѡ��¡�÷�� 4","�ӹǹ�Թ�ѡ��¡�÷�� 4","�����Թ�ѡ��¡�÷�� 5","�ӹǹ�Թ�ѡ��¡�÷�� 5","�����Թ�ѡ��¡�÷�� 6","�ӹǹ�Թ�ѡ��¡�÷�� 6","�����Թ�ѡ��¡�÷�� 7","�ӹǹ�Թ�ѡ��¡�÷�� 7","�����Թ�ѡ��¡�÷�� 8","�ӹǹ�Թ�ѡ��¡�÷�� 8","�ԹŴ/�ѡ����","������·����͹","�Ѻ�ط��","�ѹ/��͹/�վ.�. ����͡˹ѧ����Ѻ�ͧ");
	$data_type = array("s","s","s","s","s","s","s","s","s","s","s","s","s","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","s","n.2","s","n.2","s","n.2","s","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","s","n.2","s","n.2","s","n.2","s","n.2","s","n.2","s","n.2","s","n.2","s","n.2","n.2","n.2","n.2","d");	// ��੾�з�����Ҩҡ text file

	$column_map[SLIP_ID] = "running";	// 0-running number
	$column_map[PER_ID] = "sql-s-d-select PER_ID from PER_PERSONAL where  PER_ID=@3";	// /*���*/ "sql-s-d-select PER_ID from PER_PERSONAL where PER_STATUS=1 AND PER_CARDNO=@3";// 1- �֧�����Ũҡ sql �繢����Ż����� n = number (���� s = string)
																																										// 		field @3 ����ʴ� �����Ẻ d = disabled ���� e = enabled
																																										// 		�դ�� = PER_ID ����� PER_CARDNO = ���� text file column ��� @3
	$column_map[SLIP_YEAR] = "1";	// 2-map �Ѻ column 1 � text ���� excel
	$column_map[SLIP_MONTH] = "2";	// 3-map �Ѻ column 2 � text ���� excel
	$column_map[PER_CARDNO] = "sql-s-d-select PER_CARDNO from PER_PERSONAL where  PER_ID=@3";	///*���*/ 3 // 4-map �Ѻ column 3 � text ���� excel
	$column_map[PN_NAME] = "sql-s-d-select PN_NAME from PER_PRENAME where PN_NAME=@4";	// 5-map �Ѻ column 4 ��Ǩ�礴��� sql �ش���
	$column_map[PER_NAME] = "5";	// 6-map �Ѻ column 5 � text ���� excel
	$column_map[PER_SURNAME] = "6";	// 7-map �Ѻ column 6 � text ���� excel
	$column_map[DEPARTMENT_NAME] = "7";	// 8-map �Ѻ column 7 � text ���� excel
	$column_map[ORG_NAME] = "8";	// 9-map �Ѻ column 8 � text ���� excel
	$column_map[BANK_CODE] = "9";	// 10-map �Ѻ column 9 � text ���� excel
	$column_map[BANK_NAME] = "10";	// 11-map �Ѻ column 10 � text ���� excel
	$column_map[BRANCH_CODE] = "11";	// 12-map �Ѻ column 11 � text ���� excel
	$column_map[BRANCH_NAME] = "12";	// 13-map �Ѻ column 12 � text ���� excel
	$column_map[PER_BANK_ACCOUNT] = "13";	// 14-map �Ѻ column 13 � text ���� excel
	$column_map[INCOME_01] = "14";	// 15-map �Ѻ column 14 � text ���� excel
	$column_map[INCOME_02] = "15";	// 16-map �Ѻ column 15 � text ���� excel
	$column_map[INCOME_03] = "16";	// 17-map �Ѻ column 16 � text ���� excel
	$column_map[INCOME_04] = "17";	// 18-map �Ѻ column 17 � text ���� excel
	$column_map[INCOME_05] = "18";	// 19-map �Ѻ column 18 � text ���� excel
	$column_map[INCOME_06] = "19";	// 20-map �Ѻ column 19 � text ���� excel
	$column_map[INCOME_07] = "20";	// 21-map �Ѻ column 20 � text ���� excel
	$column_map[INCOME_08] = "21";	// 22-map �Ѻ column 21 � text ���� excel
	$column_map[INCOME_09] = "22";	// 23-map �Ѻ column 22 � text ���� excel
	$column_map[INCOME_10] = "23";	// 24-map �Ѻ column 23 � text ���� excel
	$column_map[INCOME_11] = "24";	// 25-map �Ѻ column 24 � text ���� excel
	$column_map[INCOME_12] = "25";	// 26-map �Ѻ column 25 � text ���� excel
	$column_map[INCOME_13] = "26";	// 27-map �Ѻ column 26 � text ���� excel
	$column_map[INCOME_14] = "27";	// 28-map �Ѻ column 27 � text ���� excel
	$column_map[INCOME_15] = "28";	// 29-map �Ѻ column 28 � text ���� excel
	$column_map[INCOME_16] = "29";	// 30-map �Ѻ column 29 � text ���� excel
	$column_map[INCOME_17] = "30";	// 31-map �Ѻ column 30 � text ���� excel
	$column_map[INCOME_18] = "31";	// 32-map �Ѻ column 31 � text ���� excel
	$column_map[INCOME_19] = "32";	// 33-map �Ѻ column 32 � text ���� excel
	$column_map[INCOME_20] = "33";	// 34-map �Ѻ column 33 � text ���� excel
	$column_map[INCOME_NAME_01] = "34";	// 35-map �Ѻ column 34 � text ���� excel
	$column_map[EXTRA_INCOME_01] = "35";	// 36-map �Ѻ column 35 � text ���� excel
	$column_map[INCOME_NAME_02] = "36";	// 37-map �Ѻ column 36 � text ���� excel
	$column_map[EXTRA_INCOME_02] = "37";	// 38-map �Ѻ column 37 � text ���� excel
	$column_map[INCOME_NAME_03] = "38";	// 39-map �Ѻ column 38 � text ���� excel
	$column_map[EXTRA_INCOME_03] = "39";	// 40-map �Ѻ column 39 � text ���� excel
	$column_map[INCOME_NAME_04] = "40";	// 41-map �Ѻ column 40 � text ���� excel
	$column_map[EXTRA_INCOME_04] = "41";	// 42-map �Ѻ column 41 � text ���� excel
	$column_map[OTHER_INCOME] = "42";	// 43-map �Ѻ column 42 � text ���� excel
	$column_map[TOTAL_INCOME] = "43";	// 44-map �Ѻ column 43 � text ���� excel
	$column_map[DEDUCT_01] = "44";	// 45-map �Ѻ column 44 � text ���� excel
	$column_map[DEDUCT_02] = "45";	// 46-map �Ѻ column 45 � text ���� excel
	$column_map[DEDUCT_03] = "46";	// 47-map �Ѻ column 46 � text ���� excel
	$column_map[DEDUCT_04] = "47";	// 48-map �Ѻ column 47 � text ���� excel
	$column_map[DEDUCT_05] = "48";	// 49-map �Ѻ column 48 � text ���� excel
	$column_map[DEDUCT_06] = "49";	// 50-map �Ѻ column 49 � text ���� excel
	$column_map[DEDUCT_07] = "50";	// 51-map �Ѻ column 50 � text ���� excel
	$column_map[DEDUCT_08] = "51";	// 52-map �Ѻ column 51 � text ���� excel
	$column_map[DEDUCT_09] = "52";	// 53-map �Ѻ column 52 � text ���� excel
	$column_map[DEDUCT_10] = "53";	// 54-map �Ѻ column 53 � text ���� excel
	$column_map[DEDUCT_11] = "54";	// 55-map �Ѻ column 54 � text ���� excel
	$column_map[DEDUCT_12] = "55";	// 56-map �Ѻ column 55 � text ���� excel
	$column_map[DEDUCT_13] = "56";	// 57-map �Ѻ column 56 � text ���� excel
	$column_map[DEDUCT_14] = "57";	// 58-map �Ѻ column 57 � text ���� excel
	$column_map[DEDUCT_15] = "58";	// 59-map �Ѻ column 58 � text ���� excel
	$column_map[DEDUCT_16] = "59";	// 60-map �Ѻ column 59 � text ���� excel
	$column_map[DEDUCT_NAME_01] = "60";	// 61-map �Ѻ column 60 � text ���� excel
	$column_map[EXTRA_DEDUCT_01] = "61";	// 62-map �Ѻ column 61 � text ���� excel
	$column_map[DEDUCT_NAME_02] = "62";	// 63-map �Ѻ column 62 � text ���� excel
	$column_map[EXTRA_DEDUCT_02] = "63";	// 64-map �Ѻ column 63 � text ���� excel
	$column_map[DEDUCT_NAME_03] = "64";	// 65-map �Ѻ column 64 � text ���� excel
	$column_map[EXTRA_DEDUCT_03] = "65";	// 66-map �Ѻ column 65 � text ���� excel
	$column_map[DEDUCT_NAME_04] = "66";	// 67-map �Ѻ column 66 � text ���� excel
	$column_map[EXTRA_DEDUCT_04] = "67";	// 68-map �Ѻ column 67 � text ���� excel
	$column_map[DEDUCT_NAME_05] = "68";	// 69-map �Ѻ column 68 � text ���� excel
	$column_map[EXTRA_DEDUCT_05] = "69";	// 70-map �Ѻ column 69 � text ���� excel
	$column_map[DEDUCT_NAME_06] = "70";	// 71-map �Ѻ column 70 � text ���� excel
	$column_map[EXTRA_DEDUCT_06] = "71";	// 72-map �Ѻ column 71 � text ���� excel
	$column_map[DEDUCT_NAME_07] = "72";	// 73-map �Ѻ column 72 � text ���� excel
	$column_map[EXTRA_DEDUCT_07] = "73";	// 74-map �Ѻ column 73 � text ���� excel
	$column_map[DEDUCT_NAME_08] = "74";	// 75-map �Ѻ column 74 � text ���� excel
	$column_map[EXTRA_DEDUCT_08] = "75";	// 76-map �Ѻ column 75 � text ���� excel
	$column_map[OTHER_DEDUCT] = "76";	// 77-map �Ѻ column 76 � text ���� excel
	$column_map[TOTAL_DEDUCT] = "77";	// 78-map �Ѻ column 77 � text ���� excel
	$column_map[NET_INCOME] = "78";	// 79-map �Ѻ column 78 � text ���� excel
	$column_map[APPROVE_DATE] = "79";	// 80-map �Ѻ column 79 � text ���� excel
	$column_map[UPDATE_USER] = "update_user";	// 82-map �Ѻ UPDATE_USER �ҡ�к�
	$column_map[UPDATE_DATE] = "update_date";	// 83-map �Ѻ UPDATE_DATE �ҡ�к�
	$column_map[AUDIT_FLAG] = "";	// 84-����դ������ ��� �������

?>