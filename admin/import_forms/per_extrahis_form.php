<?
//	map text data to table columns

	$table = "PER_EXTRAHIS";
	// ��Ң�ҧ��ҧ ������ҡ���� 1 ��ǨТ�鹴��� |
	$dup_column = "0";	// �Ţ�ӴѺ������ҧ㹰ҹ������ 
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
	$al_null = "<span class='label_alert'>*</span>"; //������ҧ
	$impfile_head_map = array("EXH_ID","PER_ID","EXH_EFFECTIVEDATE","EX_CODE","EXH_AMT","EXH_ENDDATE","UPDATE_USER","UPDATE_DATE","PER_CARDNO","EXH_ORG_NAME","EXH_SALARY","EXH_REMARK","EXH_DOCNO","EXH_DOCDATE","EXH_ACTIVE","AUDIT_FLAG");
	$head_map = array("�����Թ���������","���ʺؤ�ҡ�","�ѹ����ռ�","�������Թ���������","�ӹǹ�Թ","�֧�ѹ���","���ʼ��ӡ������¹������","�ѹ��������������¹������","�Ţ��Шӵ�ǻ�ЪҪ�","˹��§ҹ����͡�����","�ѵ���Թ��͹","�����˵�","������Ţ���","�ѹ����͡�����","�����ҹ","��Ǩ�ͺ");
	$impfile_head_title = "";
	$impfile_head_thai = array( "$al_null �Ţ��Шӵ�ǻ�ЪҪ�","$al_null ����-���ʡ��","$al_null �ѹ����ռ�","$al_null �������Թ���������","$al_null �ӹǹ�Թ","�֧�ѹ���","˹��§ҹ����͡�����","�ѵ���Թ��͹","�����˵�","������Ţ���","�ѹ����͡�����","��ҹ/¡��ԡ");
	$impfile_exam_map = array( "3100425369745","�������� 㨴�","31/12/2555","�Թ���¡ѹ���", "99999", "31/12/2555", "�ѡ���������º�����Ἱ", "99999","�͡�õ�Ǩ�ͺ","99/2555","31/12/2555","��ҹ");

	$column_map = (array) null;
	$column_map[] = "running";
	$column_map[] = "sql-n-d-select PER_ID from PER_PERSONAL where PER_CARDNO='@1' or '@2' like '%'||PER_NAME||' '||PER_SURNAME ^NOTNULL";																																					// 		field ����ʴ� �����Ẻ d = disabled ���� e = enabled																															// 		�դ�� = ORG_ID ����� ORG_NAME = ���� text file column ��� {2};
	$column_map[] = "func-s-e-save_date(@3)";
	$column_map[] = "sql-s-d-select EX_CODE from PER_EXTRATYPE where EX_NAME=@4^NOTNULL";
	$column_map[] = "5";  
	$column_map[] = "func-s-e-save_date(@6)";
	$column_map[] = "update_user";
	$column_map[] = "update_date";
	$column_map[] = "1";
	$column_map[] = "7";
	$column_map[] = "8";
	$column_map[] = "9";
	$column_map[] = "10";
	$column_map[] = "func-s-e-save_date(@11)";
	$column_map[] = "func-n-e-check_0_1(@12)";
	$column_map[] = "func-s-e-check_Y_N(@13)";
	// �����Ũҡ form
/*	$screenform[] = "selectlistfix^value=1|����Ҫ���|selected^name=sel_pertype^label=�������ؤ��ҡ� : ";	// text  radio  checkbox  selectlist  datecalendar
	$screenform[] = "text^value=func|thisyear^size=10^name=txt_year^label=�է�����ҳ : ";
	$screenform[] = "radio^value=1|���駷�� 1|checked,2|���駷�� 2^name=radio_part";
	$screenform[] = "checkbox^value=1|�繻���ѵԡ���Ѻ�Թ��͹����ش|checked^name=chk_lastdate^onclick=check_adate^+";	// + ��͡ѹ�Ѻ��¡�öѴ�
	$screenform[] = "textcalandar^size=10^name=txtc_lastdate^label=[ �ѹ�������ش ] :���";
	$screenform[] = "text^value=^size=20^name=txt_docu^label=������Ţ��� : ";
	$screenform[] = "textcalandar^size=10^name=txtc_docudate^label=�ѹ����������ŧ���㹤���� : ";
	
*/	
	include("function_share_form.php");
	
?>
