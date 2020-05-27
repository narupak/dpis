<?
//	map text data to table columns

	$table = "PER_POSITIONHIS";
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
	
	$al_null = "<span class='label_alert'>*</span>"; //�Ф��������͡������Դ�����ҧ
	$impfile_head_map = array("POH_ID","PER_ID","POH_EFFECTIVEDATE","MOV_CODE","POH_ENDDATE","POH_DOCNO","POH_DOCDATE","POH_POS_NO","PM_CODE","LEVEL_NO","PL_CODE","PN_CODE","PT_CODE","CT_CODE","PV_CODE","AP_CODE","POH_ORGMGT","ORG_ID_1","ORG_ID_2","ORG_ID_3","POH_UNDER_ORG1","POH_UNDER_ORG2","POH_ASS_ORG","POH_ASS_ORG1","POH_ASS_ORG2","POH_SALARY","POH_SALARY_POS","POH_REMARK","UPDATE_USER","UPDATE_DATE","PER_CARDNO","EP_CODE","POH_ORG1","POH_ORG2","POH_ORG3","POH_ORG_TRANSFER","POH_ORG","POH_PM_NAME","POH_PL_NAME","POH_SEQ_NO","POH_LAST_POSITION","POH_CMD_SEQ","POH_ISREAL","POH_ORG_DOPA_CODE","ES_CODE","POH_LEVEL_NO","TP_CODE","POH_UNDER_ORG3","POH_UNDER_ORG4","POH_UNDER_ORG5","POH_ASS_ORG3","POH_ASS_ORG4","POH_ASS_ORG5","POH_REMARK1","POH_REMARK2","POH_POS_NO_NAME","POH_DOCNO_EDIT","POH_DOCDATE_EDIT","AUDIT_FLAG","POH_SPECIALIST","POH_REF_DOC","POH_ACTH_SEQ","POH_ASS_DEPARTMENT");
	$head_map = array("���ʻ���ѵԴ�ç���˹�","���ʺؤ�ҡ�","�ѹ�����Ҵ�ç���˹�","�������������͹���","�ѹ�������ش��ô�ç���˹�","�Ţ�������","�ѹ����͡�����","�Ţ�����˹�","���˹�㹡�ú����çҹ","�дѺ���˹�","���˹����§ҹ","���͵��˹��١��ҧ��Ш�","���˹觻�����","�����","�ѧ��Ѵ","�����","���˹觷ҧ�����÷���˹��繡������","��з�ǧ","���","�ӹѡ/�ͧ","��ӡ����ӹѡ/�ͧ 1 �дѺ","��ӡ����ӹѡ/�ͧ 2 �дѺ","�ӹѡ/�ͧ ����ͺ���§ҹ","��ӡ����ӹѡ/�ͧ 1 �дѺ ����ͺ���§ҹ","��ӡ����ӹѡ/�ͧ 2 �дѺ ����ͺ���§ҹ","�ѵ���Թ��͹","�Թ��Шӵ��˹�","�����˵�","���ʼ����������¹�ŧ������","�ѹ���� ����¹�ŧ������","�Ţ��Шӵ�ǻ�ЪҪ�","���͵��˹觾�ѡ�ҹ�Ҫ���","���͡�з�ǧ","���͡��","�����ӹѡ/�ͧ","��ǹ�Ҫ��÷���Ѻ�͹/����͹","��������� (��͹�����͹)","���͵��˹�㹡�ú����çҹ","���͵��˹����§ҹ","�ӴѺ���ó��ѹ������ǡѹ","��ç���˹�����ش","�ӴѺ���㹺ѭ��Ṻ���¤����","��ç���˹�","�������","ʶҹС�ô�ç���˹�","�дѺ���˹�","���͵��˹��١��ҧ���Ǥ���","��ӡ����ӹѡ/�ͧ 3 �дѺ","��ӡ����ӹѡ/�ͧ 4 �дѺ","��ӡ����ӹѡ/�ͧ 5 �дѺ","��ӡ����ӹѡ/�ͧ 3 �дѺ ����ͺ���§ҹ","��ӡ����ӹѡ/�ͧ 4 �дѺ ����ͺ���§ҹ","��ӡ����ӹѡ/�ͧ 5 �дѺ ����ͺ���§ҹ","�����˵� 1","�����˵� 2","�����Ţ�����˹�","POH_DOCNO_EDIT","POH_DOCDATE_EDIT","��Ǩ�ͺ","POH_SPECIALIST","POH_REF_DOC","POH_ACTH_SEQ","POH_ASS_DEPARTMENT");
	$impfile_head_title = "";
	$impfile_head_thai = array( "$al_null �Ţ��Шӵ�ǻ�ЪҪ�","$al_null ����-���ʡ��","$al_null �ѹ�����Ҵ�ç���˹�","$al_null �������������͹���","�ѹ�������ش��ô�ç���˹�","�Ţ�������","�ѹ����͡�����","�Ţ�����˹�","���˹�㹡�ú����çҹ","�дѺ���˹�","���˹����§ҹ","���͵��˹��١��ҧ��Ш�","���˹觻�����","$al_null �����","�ѧ��Ѵ","�����","$al_null ���˹觷ҧ�����÷���˹��繡������","��з�ǧ","���","�ӹѡ/�ͧ","��ӡ����ӹѡ/�ͧ 1 �дѺ","��ӡ����ӹѡ/�ͧ 2 �дѺ","�ӹѡ/�ͧ ����ͺ���§ҹ","��ӡ����ӹѡ/�ͧ 1 �дѺ ����ͺ���§ҹ","��ӡ����ӹѡ/�ͧ 2 �дѺ ����ͺ���§ҹ","$al_null �ѵ���Թ��͹","$al_null �Թ��Шӵ��˹�","�����˵�","���͵��˹觾�ѡ�ҹ�Ҫ���","���͡�з�ǧ","���͡��","�����ӹѡ/�ͧ","��ǹ�Ҫ��÷���Ѻ�͹/����͹","��������� (��͹�����͹)","���͵��˹�㹡�ú����çҹ","���͵��˹����§ҹ","�ӴѺ���ó��ѹ������ǡѹ","��ç���˹�����ش","�ӴѺ���㹺ѭ��Ṻ���¤����","��ç���˹�","�������","ʶҹС�ô�ç���˹�","�дѺ���˹�","���͵��˹��١��ҧ���Ǥ���","��ӡ����ӹѡ/�ͧ 3 �дѺ","��ӡ����ӹѡ/�ͧ 4 �дѺ","��ӡ����ӹѡ/�ͧ 5 �дѺ","��ӡ����ӹѡ/�ͧ 3 �дѺ ����ͺ���§ҹ","��ӡ����ӹѡ/�ͧ 4 �дѺ ����ͺ���§ҹ","��ӡ����ӹѡ/�ͧ 5 �дѺ ����ͺ���§ҹ","�����˵� 1","�����˵� 2","�����Ţ�����˹�","POH_DOCNO_EDIT","POH_DOCDATE_EDIT","��Ǩ�ͺ","POH_SPECIALIST","POH_REF_DOC","POH_ACTH_SEQ","POH_ASS_DEPARTMENT");
	$impfile_exam_map = array( "3100425369745","�������� 㨴�","31/12/2555","����","31/12/2555","12/2555", "31/12/2555", "1234","�ͧ�ŢҸԡ��","�дѺ 4","�ѡ����ͧ","��ѡ�ҹ�������������","�����","��","��ا෾��ҹ��","���ͧ","��","�ӹѡ��¡�Ѱ�����","�ӹѡ�ҹ �.�.","��ǹ��ҧ","���º����÷����","�����������","����� ���.","���������֡����н֡ͺ��","�����ҧἹ��к�����","29800","21000","���Ѻ�Թ������͹��1000�ҷ","�ѡ�Ѵ��çҹ�����","�ӹѡ��¡�Ѱ�����","�ӹѡ�ҹ �.�.","�ͧ��ҧ","����Ҫ�ѳ��","��ǹ��ҧ �ӹѡ�ҹ��.","����֡�ҡ�����","�Եԡ�","1","��","1","��ԧ","4567","�ç������˹�","�дѺ 5","�ѡ�ѧ��ʧ������","����������ӹ�¡��","�������������ѹ��","�ѡ���������º�����Ἱ","������ҹ�ӹ�¡��","������ҹ��������ѹ��","�ѡ��������Ἱ�ҹ","���˹��ѧ����繻Ѩ�غѹ","���˹��͡���Ѻ����¹","��/2555","POH_DOCNO_EDIT","POH_DOCDATE_EDIT","AUDIT_FLAG","POH_SPECIALIST","POH_REF_DOC","POH_ACTH_SEQ","POH_ASS_DEPARTMENT");

	$column_map = (array) null;
	$column_map[] = "running";
	$column_map[] = "sql-n-d-select PER_ID from PER_PERSONAL where PER_CARDNO='@1' or '@2' like '%'||PER_NAME||' '||PER_SURNAME ^NOTNULL";		// 2- �֧�����Ũҡ sql �繢����Ż����� n = number (���� s = string)																																					// 		field ����ʴ� �����Ẻ d = disabled ���� e = enabled																															// 		�դ�� = ORG_ID ����� ORG_NAME = ���� text file column ��� {2};
	$column_map[] = "func-s-e-save_date(@3)^NOTNULL";
	$column_map[] = "sql-s-d-select MOV_CODE from PER_MOVMENT where MOV_NAME=@4^NOTNULL";
	$column_map[] = "func-s-e-save_date(@5)";
	$column_map[] = "6";
	$column_map[] = "func-s-e-save_date(@7)";
	$column_map[] = "8";
	$column_map[] = "sql-s-d-select PM_CODE from PER_MGT where PM_NAME=@9";
	$column_map[] = "sql-s-d-select LEVEL_NO from PER_LEVEL where LEVEL_NAME=@10";
	$column_map[] = "sql-s-d-select PL_CODE from PER_LINE where PL_NAME=@11";
	$column_map[] = "sql-s-d-select PN_CODE from PER_POS_NAME where PN_NAME=@12";
	$column_map[] = "sql-s-d-select PT_CODE from PER_TYPE where PT_NAME=@13";
	$column_map[] = "sql-s-d-select CT_CODE from PER_COUNTRY where CT_NAME=@14^NOTNULL";
	$column_map[] = "sql-s-d-select PV_CODE from PER_PROVINCE where PV_NAME=@15";
	$column_map[] = "sql-s-d-select AP_CODE from PER_AMPHUR where AP_NAME=@16";
	$column_map[] = "func-n-e-check_1_2(@17)";		// 30-map ����� ���ǹ php SAH_CMD_SEQ
	$column_map[] = "sql-s-d-select ORG_ID from PER_ORG where ORG_NAME=@18";
	$column_map[] = "sql-s-d-select ORG_ID from PER_ORG where ORG_NAME=@19";
	$column_map[] = "sql-s-d-select ORG_ID from PER_ORG where ORG_NAME=@20";
	$column_map[] = "21";	
	$column_map[] = "22";	
	$column_map[] = "23";	
	$column_map[] = "24";
	$column_map[] = "25";
	$column_map[] = "26"; 
	$column_map[] = "27";
	$column_map[] = "28";
	$column_map[] = "update_user";
	$column_map[] = "update_date";
	$column_map[] = "1";	
	$column_map[] = "sql-s-d-select EP_CODE from PER_EMPSER_POS_NAME where EP_NAME=@29";	
	$column_map[] = "30";	
	$column_map[] = "31";
	$column_map[] = "32";
	$column_map[] = "33";
	$column_map[] = "34";
	$column_map[] = "35";
	$column_map[] = "36";
	$column_map[] = "37";
	$column_map[] = "func-s-e-check_Y_N(@38)";
	$column_map[] = "39";
	$column_map[] = "func-s-e-check_Y_N(@40)";
	$column_map[] = "41";
	$column_map[] = "sql-s-d-select ES_CODE from PER_EMP_STATUS where ES_NAME=@42";
	$column_map[] = "sql-s-d-select LEVEL_NO from PER_LEVEL where LEVEL_NAME=@43";
	$column_map[] = "sql-s-d-select TP_CODE from PER_TEMP_POS_NAME where TP_NAME=@44"; 
	$column_map[] = "45";
	$column_map[] = "46";
	$column_map[] = "47";
	$column_map[] = "48";
	$column_map[] = "49";
	$column_map[] = "50";
	$column_map[] = "51";
	$column_map[] = "52";
	$column_map[] = "53";
	$column_map[] = "54";
	$column_map[] = "func-s-e-save_date(@55)";
	$column_map[] = "func-s-e-check_Y_N(@56)";
	$column_map[] = "57";
	$column_map[] = "58";
	$column_map[] = "59";
	$column_map[] = "60";
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
