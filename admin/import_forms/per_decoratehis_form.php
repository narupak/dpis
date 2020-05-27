<?
//	map text data to table columns

	$table = "PER_DECORATEHIS";
	$dup_column = "1";	// �Ţ�ӴѺ������ҧ㹰ҹ������
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
	
	$impfile_head_map = array("PER_CARDNO","FNAME","LNAME","INS_CODE","INS_DATE","POS_NAME","DEP_NAME");

	$head_map = array("DEH_ID","PER_ID","����ͧ�Ҫ� ������Ѻ","�ѹ������Ѻ����ͧ�Ҫ�","","","�Ţ��Шӵ�ǻ�ЪҪ�","�Ҫ�Ԩ�ҹ�ມ��","�Ѻ����ͧ�Ҫ�","�׹����ͧ�Ҫ�","�ѹ���׹����ͧ�Ҫ�","�׹������ͧ�Ҫ�/�Թʴ","�ѹ����Ҫ�Ԩ�ҹ�ມ��","�Ţ���˹ѧ��͹���","�ѹ���˹ѧ��͹���","�����˵�","���˹�","�ѧ�Ѵ","��Ѻ����¹�ҹѹ��/��Ѻ�����","����","�͹���","˹��","�ӴѺ","��Ǩ�ͺ����","����","�͹���","˹��","�ӴѺ","�ѹ����Ҫ�Ԩ�ҹ�ມ��");
	$impfile_head_title = "";
	$impfile_head_thai = array( "�Ţ��Шӵ�ǻ�ЪҪ�","����","���ʡ��", "����ͧ�Ҫ� ������Ѻ", "�ѹ������Ѻ����ͧ�Ҫ�", "���͵��˹�", "����˹��§ҹ","����","�͹���","˹��","�ӴѺ","�ѹ����Ҫ�Ԩ�ҹ�ມ��");
	$impfile_exam_map = array( "3100425369745","�ѡ�ҵ�","��觪վ", "�.�.", "12/05/2546", "�ѡ���������º�����Ἱ", "�ӹѡ�ŢҸԡ�ä���Ѱ�����","1","2","3","12","12/06/2546");

	$column_map[DEH_ID] = "running";	// 0-running number DEH_ID
	$column_map[PER_ID] = "sql-s-d-select PER_ID from PER_PERSONAL where PER_CARDNO=@1";	// 1- �֧�����Ũҡ sql �繢����Ż����� n = number (���� s = string)
																																										// 		field ����ʴ� �����Ẻ d = disabled ���� e = enabled
																																										// 		�դ�� = PER_ID ����� PER_CARDNO = ���� text file column ��� {1}
	$column_map[DC_CODE] = "sql-s-e-select DC_CODE from PER_DECORATION where DC_SHORTNAME=@4";	// 2-�ҡ sql ��ҹ���� INS_CODE =>DC_CODE
	$column_map[DEH_DATE] = "func-s-e-save_date(@5)";	// 3-map �Ѻ column 4 � text ���� excel DEH_DATE = save_date($INS_DATE)
	$column_map[UPDATE_USER] = "update_user";	// 82-map �Ѻ UPDATE_USER �ҡ�к�
	$column_map[UPDATE_DATE] = "update_date";	// 83-map �Ѻ UPDATE_DATE �ҡ�к�
	$column_map[PER_CARDNO] = "1";	// 6-map �Ѻ column 1 � text ���� excel PER_CARDNO
	$column_map[DEH_GAZETTE] = "";		// 7-����դ������ DEH_GAZETTE
	$column_map[DEH_RECEIVE_FLAG] = "";		// 8-����դ������ DEH_RECEIVE_FLAG
	$column_map[DEH_RETURN_FLAG] = "";		// 9-����դ������ DEH_RETURN_FLAG
	$column_map[DEH_RETURN_DATE] = "";		// 10-����դ������ DEH_RETURN_DATE
	$column_map[DEH_RETURN_TYPE] = "";		// 11-����դ������ DEH_RETURN_TYPE
	$column_map[DEH_RECEIVE_DATE] = "func-s-e-save_date(@12)";		// 12-����դ������ DEH_RECEIVE_DATE
	$column_map[DEH_BOOK_NO] = "";		// 13-����դ������ DEH_BOOK_NO
	$column_map[DEH_BOOK_DATE] = "";		// 14-����դ������ DEH_BOOK_DATE
	$column_map[DEH_REMARK] = "";	// fmla-s-e-@6+' '+@7 15-formula �����Ũҡ text 6,7 DEH_REMARK = '$POS_NAME(6) $DEP_NAME(7)'
	$column_map[DEH_POSITION] = "6";		// 16-����դ������ DEH_POSITION
	$column_map[DEH_ORG] = "7";		// 17-����դ������ DEH_ORG
	$column_map[DEH_ISSUE] = "";		// 18-����դ������ DEH_ISSUE
	$column_map[DEH_BOOK] = "8";		// 19-����դ������ DEH_BOOK
	$column_map[DEH_PART] = "9";		// 20-����դ������ DEH_PART
	$column_map[DEH_PAGE] = "10";		// 21-����դ������ DEH_PAGE
	$column_map[DEH_ORDER_DECOR] = "11";		// 22-����դ������ DEH_ORDER_DECOR
	$column_map[AUDIT_FLAG] = "";		// 23-����դ������ ��� ������� AUDIT_FLAG
        
        
        
        
        
        /* ���
$column_map[] = "running";	// 0-running number DEH_ID
	$column_map[] = "sql-s-d-select PER_ID from PER_PERSONAL where PER_CARDNO=@1";	// 1- �֧�����Ũҡ sql �繢����Ż����� n = number (���� s = string)
																																										// 		field ����ʴ� �����Ẻ d = disabled ���� e = enabled
																																										// 		�դ�� = PER_ID ����� PER_CARDNO = ���� text file column ��� {1}
	$column_map[] = "sql-s-e-select DC_CODE from PER_DECORATION where DC_SHORTNAME=@4";	// 2-�ҡ sql ��ҹ���� INS_CODE =>DC_CODE
	$column_map[] = "func-s-e-save_date(@5)";	// 3-map �Ѻ column 4 � text ���� excel DEH_DATE = save_date($INS_DATE)
	$column_map[] = "update_user";	// 82-map �Ѻ UPDATE_USER �ҡ�к�
	$column_map[] = "update_date";	// 83-map �Ѻ UPDATE_DATE �ҡ�к�
	$column_map[] = "1";	// 6-map �Ѻ column 1 � text ���� excel PER_CARDNO
	$column_map[] = "";		// 7-����դ������ DEH_GAZETTE
	$column_map[] = "";		// 8-����դ������ DEH_RECEIVE_FLAG
	$column_map[] = "";		// 9-����դ������ DEH_RETURN_FLAG
	$column_map[] = "";		// 10-����դ������ DEH_RETURN_DATE
	$column_map[] = "";		// 11-����դ������ DEH_RETURN_TYPE
	$column_map[] = "";		// 12-����դ������ DEH_RECEIVE_DATE
	$column_map[] = "";		// 13-����դ������ DEH_BOOK_NO
	$column_map[] = "";		// 14-����դ������ DEH_BOOK_DATE
	$column_map[] = "";	// fmla-s-e-@6+' '+@7 15-formula �����Ũҡ text 6,7 DEH_REMARK = '$POS_NAME(6) $DEP_NAME(7)'
	$column_map[] = "6";		// 16-����դ������ DEH_POSITION
	$column_map[] = "7";		// 17-����դ������ DEH_ORG
	$column_map[] = "";		// 18-����դ������ DEH_ISSUE
	$column_map[] = "";		// 19-����դ������ DEH_BOOK
	$column_map[] = "";		// 20-����դ������ DEH_PART
	$column_map[] = "";		// 21-����դ������ DEH_PAGE
	$column_map[] = "";		// 22-����դ������ DEH_ORDER_DECOR
	$column_map[] = "";		// 23-����դ������ ��� ������� AUDIT_FLAG
         */
	
	// �����Ũҡ form
	$scrform[] = "";

?>