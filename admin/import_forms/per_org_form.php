<?
//	map text data to table columns

	$table = "PER_ORG";
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
	$column_map[] = "1";	// 0
	$column_map[] = "2";	// 1
	$column_map[] = "3";	// 2
	$column_map[] = "4";	// 3
	$column_map[] = "sql-s-d-select OL_CODE from PER_ORG_LEVEL where OL_NAME='@5'";	// 4 - �֧�����Ũҡ sql �繢����Ż����� s = string (���� n = number)
																																										// 		field ����ʴ� �����Ẻ d = disabled ���� e = enabled
																																										// 		�դ�� = OL_CODE ����� OL_NAME = ���� text file column ��� {5}
	$column_map[] = "sql-s-d-select OT_CODE from PER_OFF_TYPE where OT_NAME='@6'";		// 5
	$column_map[] = "7";	// 6
	$column_map[] = "8";	// 7
	$column_map[] = "9";	// 8
	$column_map[] = "sql-s-d-select AP_CODE from PER_AMPHUR where AP_NAME='@10'";	// 10
	$column_map[] = "sql-s-d-select PV_CODE from PER_PROVINCE where PV_NAME='@11'";
	$column_map[] = "sql-s-d-select CT_CODE from PER_COUNTRY where CT_NAME='@12'";
	$column_map[] = "13";
	$column_map[] = "14";
	$column_map[] = "sql-n-d-select ORG_ID from PER_ORG where ORG_NAME='@15'";
	$column_map[] = "16";
	$column_map[] = "update_user";	// 17-map �Ѻ UPDATE_USER �ҡ�к�
	$column_map[] = "update_date";	// 18-map �Ѻ UPDATE_DATE �ҡ�к�
	$column_map[] = "19";
	$column_map[] = "20";
	$column_map[] = "sql-n-d-select ORG_ID from PER_ORG where ORG_NAME='@21'";
	$column_map[] = "22";
	$column_map[] = "23";
	$column_map[] = "24";
	$column_map[] = "25";
	$column_map[] = "sql-s-d-select DT_CODE from PER_DISTRICT where DT_NAME='@26'";
	$column_map[] = "sql-s-d-select MG_CODE from PER_MINISTRY_GROUP where MG_NAME='@27'";
	$column_map[] = "sql-s-d-select PG_CODE from PER_POS_GROUP where PG_NAME='@28'";

	// �����Ũҡ form
/*	$screenform[] = "selectlistfix^value=1|����Ҫ���|selected^name=sel_pertype^label=�������ؤ��ҡ� : ";	// text  radio  checkbox  selectlist  datecalendar
	$screenform[] = "text^value=func|thisyear^size=10^name=txt_year^label=�է�����ҳ : ";
	$screenform[] = "radio^value=1|���駷�� 1|checked,2|���駷�� 2^name=radio_part";
	$screenform[] = "checkbox^value=1|�繻���ѵԡ���Ѻ�Թ��͹����ش|checked^name=chk_lastdate^onclick=check_adate^+";	// + ��͡ѹ�Ѻ��¡�öѴ�
	$screenform[] = "textcalandar^size=10^name=txtc_lastdate^label=[ �ѹ�������ش ] :���";
	$screenform[] = "text^value=^size=20^name=txt_docu^label=������Ţ��� : ";
	$screenform[] = "textcalandar^size=10^name=txtc_docudate^label=�ѹ����������ŧ���㹤���� : ";
*/
?>
