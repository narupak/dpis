<?
//	map text data to table columns

	$table = "PER_TAXHIS";
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
	$head_map = array("SLIP_ID", "PER_ID", "�����շ�����","�ѹ ��͹ �վ.�. ����͡˹ѧ����Ѻ�ͧ�","�Ţ���","�Ţ��Шӵ�Ǽ�����������ҡ�","����˹��§ҹ","�������","�Ţ��Шӵ�ǻ�ЪҪ�","�Ţ��Шӵ�Ǽ�����������ҡ�","����-���ʡ��","�������","�ӴѺ���","Ẻ�����¡�������ѡ����","�ӹǹ�Թ������","���շ���ѡ��й������","����Թ����������","������շ���ѡ��й������","������շ���ѡ��й������ (����ѡ��)","�Թ�������������","�Թ����������","�������Թ");
	$data_type = array("s", "s", "s", "s", "s", "s", "s", "s", "s", "s", "s", "n.2", "n.2", "n.2", "n.2", "s", "s", "n.2", "s", "s", "s", "s");	// ��੾�з�����Ҩҡ text file

	$column_map[STAX_ID] = "running";	// 0-running number
	$column_map[PER_ID] = "sql-s-d-select PER_ID from PER_PERSONAL where PER_CARDNO=@5";	// 1- �֧�����Ũҡ sql �繢����Ż����� n = number (���� s = string)
      																																									// 		�դ�� = PER_ID ����� PER_CARDNO = ���� text file column ��� @3
	$column_map[TAX_YEAR]  = "11";	// 2-map �Ѻ column 1 � text ���� excel
	$column_map[TAX_DATE]  = "20";	// 3-map �Ѻ column 2 � text ���� excel
	$column_map[TAX_NUM]   = "1";	// 4-map �Ѻ column 3 � text ���� excel
	$column_map[ORGTAX_NO] = "3";	// 5-map �Ѻ column 4 ��Ǩ�礴��� sql �ش���
	$column_map[ORG_NAME]  = "2";	// 6-map �Ѻ column 5 � text ���� excel
	$column_map[ORG_ADDR]  = "4";	// 7-map �Ѻ column 6 � text ���� excel
	$column_map[PER_CARDNO]= "5";	// 8-map �Ѻ column 7 � text ���� excel
	$column_map[PER_TAXNO] = "6";	// 9-map �Ѻ column 8 � text ���� excel
	$column_map[PER_NAME]  = "7";	// 10-map �Ѻ column 9 � text ���� excel
	$column_map[PER_ADDR]  = "8";	// 11-map �Ѻ column 10 � text ���� excel
	$column_map[SEQ_NO]    = "9";	// 12-map �Ѻ column 11 � text ���� excel
	$column_map[FORMTAX_TYPE] = "10";	// 13-map �Ѻ column 12 � text ���� excel
	$column_map[INCOME]     = "12";	// 14-map �Ѻ column 13 � text ���� excel
	$column_map[TAX_INCOME] = "13";	// 15-map �Ѻ column 14 � text ���� excel
	$column_map[NET_INCOME] = "14";	// 16-map �Ѻ column 15 � text ���� excel
	$column_map[NETTAX_INCOME] = "15";	// 17-map �Ѻ column 16 � text ���� excel
	$column_map[NETTAX_CHAR]   = "16";	// 18-map �Ѻ column 17 � text ���� excel
	$column_map[NETSAVING_TYPE]= "17";	// 19-map �Ѻ column 18 � text ���� excel
	$column_map[NET_SAVING]    = "18";	// 20-map �Ѻ column 19 � text ���� excel
	$column_map[TAX_TYPE]      = "19";	// 21-map �Ѻ column 20 � text ���� excel

?>