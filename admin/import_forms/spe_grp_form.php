<?	
//	map text data to table columns
//ini_set('error_reporting', 30719); //�Դ warning
 //ini_set('error_reporting', 30711); // �ѧ�Ѻ����ʴ� warning
	$table = "PER_SPECIAL_SKILL";
	// ��Ң�ҧ��ҧ ������ҡ���� 1 ��ǨТ�鹴��� |
	$dup_column = "0";	// �Ţ�ӴѺ������ҧ㹰ҹ������
	$prime = "0";		// �Ţ�ӴѺ������ҧ㹰ҹ������
	$running = "0";		// �Ţ�ӴѺ������ҧ㹰ҹ������ �������� running ��� = -1
//echo "�������Ѻ���Ǥ��� (beta 5.1.0.0 : 15 �.�. 2558)<br>";
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
	
	//$impfile_head_map = array("PER_CARDNO", "NAME", "SAH_POS_NO", "SAH_POSITION", "POSITION_LEVEL", "SAH_OLD_SALARY", "LAYER_SALARY_MAX", "SAH_SALARY_MIDPOINT",	"SAH_TOTAL_SCORE", "SAH_PERCENT_UP", "", "SAH_SALARY_UP",	"SAH_SALARY_EXTRA", "SAH_SALARY_TOTAL", "SAH_SALARY", "AM_NAME", "SAH_REMARK");
	$impfile_head_title = "(����Ѻ�١��ҧ��Ш� 㹪�ͧ�š�û����Թ ����͹������ 0.5 ���, 1 ���, 1.5 ���, 2 ���, �Թ�ͺ᷹����� 2% ,�Թ�ͺ᷹����� 4% ���͢�������ѡ�������������͹���)";
	$impfile_head_thai = array( "�Ţ��Шӵ�ǻ�ЪҪ�","�ӹ�˹�Ҫ���","����","���ʡ��","��з�ǧ", "���", "�ѧ��Ѵ", "���͵��˹����§ҹ", "�дѺ���˹�", "��������������Ǫҭ", "��ҹ��������Ǫҭ(��ҹ��ѡ)",	"��ҹ��������Ǫҭ�����(��ҹ�ͧ)", "�дѺ��������ö", "��������´/��͸Ժ��", "ʶҹ�");
	$impfile_exam_map = array( "1234567890123","���","�����","㨴�","�ӹѡ��¡�Ѱ�����", "�ӹѡ�ҹ �.�.", "�������", "�ѡ�Ԫҡ�ä���������", "�ӹҭ���", "��������Ǫҭ��Ҫ���", "��ҹ�Է����ʵ�����෤�����", "��ҹ�Է����ʵ�����෤�����/����������", "05-����ö�͹������", "�繼������к�����͢��¢ͧ˹��§ҹ....", "N");

//	$head_map = array("�Ţ���","����","���ʡ��","Email","ʶҹ�","��","�ѹ�Դ","","");
        $head_map = array("0",
            "PER_ID",
            "�Ţ��Шӵ�ǻ�ЪҪ�", 
            "��ҹ��������Ǫҭ�����",
            "��������´/��͸Ժ��", 
            "���ʼ����������¹�ŧ������", 
            "�ѹ��������¹�ŧ������", 
            "�����˵�", 
            "ʶҹ�",
            "�ӴѺ���",
            "�дѺ��������ö",
            "��������������Ǫҭ"); 

	$column_map[SPS_ID] = "running";	// 0-running number SAH_ID
	$column_map[PER_ID] = "sql-n-d-select PER_ID from PER_PERSONAL where PER_CARDNO='@3'";
																																											// 		field ����ʴ� �����Ẻ d = disabled ���� e = enabled
																																											// 		�դ�� = PER_ID ����� PER_CARDNO = ���� text file column ��� {1}
	$column_map[PER_CARDNO] = "3";		
	$column_map[SS_CODE] = "13";		
	$column_map[SPS_EMPHASIZE] = "16";		
	$column_map[UPDATE_USER] = "update_user";		
	$column_map[UPDATE_DATE] = "update_date";	
	$column_map[SPS_REMARK] = "";	
	$column_map[AUDIT_FLAG] = "17";	
	$column_map[SPS_SEQ_NO] = "24";	
        $column_map[LEVELSKILL_CODE] = "15";
        $column_map[SPS_FLAG] = "12";
	
	// �� function ����Ѻ ��˹���Ҿ����
?>