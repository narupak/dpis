<?
//	map text data to table columns

	$table = "PER_PROVINCE";
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
	$column_map[] = "2";
	$column_map[] = "3";
	$column_map[] = "4";
	$column_map[] = "5";
	$column_map[] = "6";
	$column_map[] = "7";
	$column_map[] = "8";

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
