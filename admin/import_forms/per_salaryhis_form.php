<?
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
//	map text data to table columns
//ini_set('error_reporting', 30719); //�Դ warning
//ini_set('error_reporting', 30711); // �ѧ�Ѻ����ʴ� warning
$table = "PER_SALARYHIS";
// ��Ң�ҧ��ҧ ������ҡ���� 1 ��ǨТ�鹴��� |
$dup_column = "0";	// �Ţ�ӴѺ������ҧ㹰ҹ������
$prime = "0";		// �Ţ�ӴѺ������ҧ㹰ҹ������
$running = "0";		// �Ţ�ӴѺ������ҧ㹰ҹ������ �������� running ��� = -1
//echo "�������Ѻ���Ǥ��� (beta 5.1.0.0 : 15 �.�. 2558)<br>";
//        echo "�������Ѻ���Ǥ��� (beta 5.2.1.14 : 29 �.�. 2560)<br>";
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

$impfile_head_map = array("PER_CARDNO", "NAME", "SAH_POS_NO", "SAH_POSITION", "POSITION_LEVEL", "SAH_OLD_SALARY", "LAYER_SALARY_MAX", "SAH_SALARY_MIDPOINT",	"SAH_TOTAL_SCORE", "SAH_PERCENT_UP", "", "SAH_SALARY_UP",	"SAH_SALARY_EXTRA", "SAH_SALARY_TOTAL", "SAH_SALARY", "AM_NAME", "SAH_REMARK");
$impfile_head_title = "(����Ѻ�١��ҧ��Ш� 㹪�ͧ�š�û����Թ ����͹������ 0.5 ���, 1 ���, 1.5 ���, 2 ���, �Թ�ͺ᷹����� 2% ,�Թ�ͺ᷹����� 4% ���͢�������ѡ�������������͹���)";
$impfile_head_thai = array( "�Ţ��Шӵ�ǻ�ЪҪ�","����-���ʡ��","�Ţ�����˹�", "���͵��˹�", "�дѺ���˹�", "�Թ��͹", "�Թ��͹�٧�ش", "�ҹ㹡�äӹǳ", "��ṹ��û����Թ",	"�����Тͧ�������͹", "�ӹǹ�Թ���ӹǳ��", "�ӹǹ�Թ������Ѻ�������͹��ԧ", "�Թ�ͺ᷹�", "���", "�Թ��͹��ѧ����͹", "�š�û����Թ", "�����˵�","*��Ҥ�ͧ�վ���Ǥ���",  "�ѧ�Ѵ");
$impfile_exam_map = array( "1234567890123","�������� 㨴�","1", "�ѡ������", "�٧<br>�.2", "69350", "69810", "66460", "94.64", "3.9", "2600", "460", "2131.94", "2591.94", "69810", "����<br>0.5 ���", "","1500","");

//	$head_map = array("�Ţ���","����","���ʡ��","Email","ʶҹ�","��","�ѹ�Դ","","");
$head_map = array("0",
    "PER_ID",
    "�ѹ����ռ�", //SAH_EFFECTIVEDATE
    "�������������͹���", //MOV_CODE
    "�ѵ���Թ��͹", //SAH_SALARY
    "�Ţ�������", //SAH_DOCNO
    "�ѹ����͡�����", //SAH_DOCDATE
    "�ѹ�������ش", //SAH_ENDDATE
    "���ʼ����������¹�ŧ������", //UPDATE_USER
    "�ѹ���� ����¹�ŧ������", //UPDATE_DATE
    "�Ţ��Шӵ�ǻ�ЪҪ�", //PER_CARDNO
    "�����繵�������͹", //SAH_PERCENT_UP
    "�Թ��͹�������͹", //SAH_SALARY_UP
    "�Թ�ͺ᷹�����", //SAH_SALARY_EXTRA
    "�ӴѺ���", //SAH_SEQ_NO
    "�����˵�", //SAH_REMARK
    "�дѺ���˹�", //LEVEL_NO
    "�Ţ�����˹�", //SAH_POS_NO
    "���˹�", //SAH_POSITION
    "�ѧ�Ѵ", //SAH_ORG
    "�������Թ", //EX_CODE
    "�Ţ��ͨ���", //SAH_PAY_NO
    "�ҹ㹡�äӹǳ", //SAH_SALARY_MIDPOINT
    "�է�����ҳ", //SAH_KF_YEAR
    "�ͺ��û����Թ", //SAH_KF_CYCLE
    "�š�û����Թ", //SAH_TOTAL_SCORE
    "ʶҹ��Թ��͹����ش", //SAH_LAST_SALARY
    "�ӹǹ����Թ��͹", //SM_CODE
    "�ӴѺ���", //SAH_CMD_SEQ
    "�������", //SAH_ORG_DOPA_CODE
    "�ѵ���Թ��͹���", //SAH_OLD_SALARY
    "�����Ţ�����˹�", //SAH_POS_NO_NAME
    "AUDIT FLAG", //AUDIT_FLAG
    "SPECIALIST", //SAH_SPECIALIST
    "REF DOC", //SAH_REF_DOC
    "DOCNO EDIT", //SAH_DOCNO_EDIT
    "DOCDATE EDIT", //SAH_DOCDATE_EDIT
    "REMARK1", //SAH_REMARK1
    "REMARK1",
    "��Ҥ�ͧ�վ���Ǥ���",
    "�ѧ�Ѵ"); //SAH_REMARK2

$column_map[SAH_ID] = "running";	// 0-running number SAH_ID
$column_map[PER_ID] = "sql-n-d-select PER_ID from PER_PERSONAL where PER_ID='@1'";
//$column_map[PER_ID] = "sql-n-d-select PER_ID from PER_PERSONAL where PER_CARDNO='@1' or '@2' like '%'||PER_NAME||' '||PER_SURNAME^NOTNULL";//"sql-n-d-select PER_ID from PER_PERSONAL where PER_ID=@17";		// 1- �֧�����Ũҡ sql �繢����Ż����� s = string (���� n = number
// 		field ����ʴ� �����Ẻ d = disabled ���� e = enabled
// 		�դ�� = PER_ID ����� PER_CARDNO = ���� text file column ��� {1}
$column_map[SAH_EFFECTIVEDATE] = "screen-s-e-SAH_EFFECTIVEDATE|save_date";		// 2-map �Ѻ column 2 � text ���� excel  SAH_EFFECTIVEDATE
$column_map[MOV_CODE] = "php-s-e-MOV_CODE";		// 3-map �Ѻ ����� ���ǹ�ͧ php MOV_CODE
$column_map[SAH_SALARY] =  "15";                            // ��� http://dpis.ocsc.go.th/Service/node/1605 "php-s-e-SAH_SALARY";                                       // 4-map �Ѻ column 15 � text ���� excel SAH_SALARY
$column_map[SAH_DOCNO] = "screen-s-e-SAH_DOCNO";		// 5-map �Ѻ �����㹨� SAH_DOCNO
$column_map[SAH_DOCDATE] = "screen-s-e-SAH_DOCDATE|save_date";	// 6-map �Ѻ �����㹨�  SAH_DOCDATE = save_date(SAH_DOCDATE)
$column_map[SAH_ENDDATE] = "screen-s-e-SAH_ENDDATE|save_date";	// 7-map �Ѻ �����㹨� SAH_ENDDATE = save_date(SAH_ENDDATE)
$column_map[UPDATE_USER] = "update_user";	// 8-map �Ѻ UPDATE_USER �ҡ�к�
$column_map[UPDATE_DATE] = "update_date";	// 9-map �Ѻ UPDATE_DATE �ҡ�к�
$column_map[PER_CARDNO] = "sql-s-d-select PER_CARDNO from PER_PERSONAL where  PER_ID=@1"; //"php-s-e-PER_CARDNO";		// 10-map �Ѻ column 1 � text ���� excel  PER_CARDNO
$column_map[SAH_PERCENT_UP] = "10";	// 11-map �Ѻ column 10 � text ���� excel  SAH_PERCENT_UP
$column_map[SAH_SALARY_UP] = "12";	// 12-map �Ѻ column 12 � text ���� excel  SAH_SALARY_UP
$column_map[SAH_SALARY_EXTRA] = "13";	// 13-map �Ѻ column 13 � text ���� excel  SAH_SALARY_EXTRA
$column_map[SAH_SEQ_NO] = "php-s-e-SAH_SEQ_NO";	// 14-map ����� ���ǹ php SAH_SEQ_NO
$column_map[SAH_REMARK] = "17";	// 15-map �Ѻ column 17 � text ���� excel  SAH_REMARK
$column_map[LEVEL_NO] = "php-s-e-LEVEL_NO";	// 16-map ����� ���ǹ php LEVEL_NO
$column_map[SAH_POS_NO] = "3";	// 17-map �Ѻ column 3 � text ���� excel  SAH_POS_NO
$column_map[SAH_POSITION] = "4";	// 18-map �Ѻ column 4 � text ���� excel  SAH_POSITION
$column_map[SAH_ORG] = "php-s-e-SAH_ORG";
$column_map[SAH_PAY_NO] = "3";	// 21-map �Ѻ column 3 (SAH_POS_NO) � text ���� excel  SAH_PAY_NO
$column_map[SAH_SALARY_MIDPOINT] = "8";	// 22-map �Ѻ column 8 � text ���� excel SAH_SALARY_MIDPOINT
$column_map[SAH_KF_YEAR] = "php-s-e-SAH_KF_YEAR";	// 23-map ����� ���ǹ php SAH_KF_YEAR
$column_map[SAH_KF_CYCLE] = "php-s-e-SAH_KF_CYCLE";	// 24-map ����� ���ǹ php SAH_KF_CYCLE
$column_map[SAH_TOTAL_SCORE] = "9";	// 25-map �Ѻ column 9 � text ���� excel SAH_TOTAL_SCORE
$column_map[SAH_LAST_SALARY] = "php-s-e-SAH_LAST_SALARY";	// 26-map ����� ���ǹ php SAH_LAST_SALARY
$column_map[SM_CODE] = "php-s-e-SM_CODE";	// 27-map ����� ���ǹ php SM_CODE
$column_map[SAH_CMD_SEQ] = "php-s-e-SAH_CMD_SEQ";		// 28-map ����� ���ǹ php SAH_CMD_SEQ
$column_map[SAH_ORG_DOPA_CODE] = "";		// 29-map space SAH_ORG_DOPA_CODE
$column_map[SAH_OLD_SALARY] = "6";		// 30-map space SAH_OLD_SALARY
$column_map[SAH_POS_NO_NAME] = "";		// 31-map space SAH_POS_NO_NAME
$column_map[AUDIT_FLAG] = "";		// 32-map space AUDIT_FLAG
$column_map[SAH_SPECIALIST] = "";		// 33-map space SAH_SPECIALIST
$column_map[SAH_REF_DOC] = "";		// 34-map space SAH_REF_DOC
$column_map[SAH_DOCNO_EDIT] = "";		// 35-map space SAH_DOCNO_EDIT
$column_map[SAH_DOCDATE_EDIT] = "";		// 36-map space SAH_DOCDATE_EDIT
$column_map[SAH_REMARK1] = "";		// 37-map space SAH_REMARK1
$column_map[SAH_REMARK2] = "";		// 38-map space SAH_REMARK2
$column_map[LIVING_EXPENSES] = "18";		// 39-map space

// �� function ����Ѻ ��˹���Ҿ����
function data_setting_extend($i_data_map, $var_d_in) {
    global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port;
    global $search_kf_cycle, $search_budget_year,$locat_work,$search_per_type;
    global $PER_SALARY_CURRENT_UPDATE;

    $db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port);
    $db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port);
    $db_dpis6 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port);


//		echo "data=".implode(",",$i_data_map)."<br>";
    // �����ŷ������ż����Ǿ����ŧ�ҹ������
    for($i = 0; $i < count($i_data_map); $i++) {
        $data_stru = explode("=",$i_data_map[$i]);
        global $$data_stru[1];		// ���͵��������͹���� column 㹰ҹ������
        $$data_stru[1] = $data_stru[2];	// �����ҡ�Ѵ��õ�� column_map ���������
    }

    // �����Ŵ��������������
    for($i = 0; $i < count($var_d_in); $i++) {
        $data_org = explode("=",$var_d_in[$i]);
        $$data_org[0] = $data_org[1];	// ���������Ŵ�����ŧ㹵���Ū��� varn
    }

    /* v v v v v v v v v v ��ǹ�ͧ���������е����ͧ��� v v v v v v v v v v*/
    if(!isset($search_budget_year)){
        if(date("Y-m-d") <= date("Y")."-10-01") $search_budget_year = date("Y") + 543;
        else $search_budget_year = (date("Y") + 543) + 1;
    } // end if
    //$PER_CARDNO = str_replace(" ","",$var1);
    //$PER_CARDNO = str_replace("-","",$var1);
    $SAH_KF_CYCLE = $search_kf_cycle;
    $SAH_KF_YEAR = $search_budget_year;
    if($SAH_KF_CYCLE == 1) $SAH_EFFECTIVEDATE = ($search_budget_year-543) . "-04-01";
    elseif($SAH_KF_CYCLE == 2) $SAH_EFFECTIVEDATE = ($search_budget_year-543) . "-10-01";

    $tmp_date = explode("-", $SAH_EFFECTIVEDATE);
    // 86400 �Թҷ� = 1 �ѹ

    $before_cmd_date = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2), intval($tmp_date[0])) - 86400);
    $before_cmd_date = date("Y-m-d", $before_cmd_date);

//			$SAH_DOCDATE =  save_date($SAH_DOCDATE);
//			$SAH_ENDDATE =  save_date($SAH_ENDDATE);

    $SM_CODE = "";
    $cmd = " select MOV_CODE from PER_MOVMENT where MOV_NAME = '$var16' ";
    $count_data = $db_dpis2->send_cmd($cmd);
    if ($count_data) {
        $data2 = $db_dpis2->get_array();
        $MOV_CODE = $data2[MOV_CODE];
    } else {
        if ($var16=="����") $MOV_CODE = "21345";
        elseif ($var16=="���ҡ") $MOV_CODE = "21335";
        elseif ($var16=="��") $MOV_CODE = "21325";
        elseif ($var16=="����") $MOV_CODE = "21315";
        elseif ($var16=="�Ҵ�Ҫ���") $MOV_CODE = "22374";
        elseif ($var16=="��û�Ѻ��ا" || $var16=="��Ѻ��ا" || $var16=="��ͧ��Ѻ��ا") $MOV_CODE = "21375";
        elseif ($var16=="�������Ѻ���ͺ��") $MOV_CODE = "22384";
        elseif ($var16=="����֡������Ѻ�Թ��͹") $MOV_CODE = "22383";
        elseif ($var16=="���֡��") $MOV_CODE = "22383";
        elseif ($var16=="ͺ����ҧ�����") $MOV_CODE = "22384";
        elseif ($var16=="��觾ѡ�Ҫ���") $MOV_CODE = "22377";
        elseif ($var16=="����������͹����Թ��͹") $MOV_CODE = "213";
        elseif ($var16=="����͹����Թ��͹ 0.5 ���" || $var16=="0.5 ���" || $var16=="0.5" || $var16=="0.50" || $SAH_REMARK=="0.5 ���") {
            $MOV_CODE = "21310";
            $SM_CODE = "1";
        } elseif ($var16=="����͹����Թ��͹ 1 ���" || $var16=="1 ���" || $var16=="1" || $SAH_REMARK=="1 ���") {
            $MOV_CODE = "21320";
            $SM_CODE = "2";
        } elseif ($var16=="����͹����Թ��͹ 1.5 ���" || $var16=="1.5 ���" || $var16=="1.5" || $SAH_REMARK=="1.5 ���") {
            $MOV_CODE = "21330";
            $SM_CODE = "3";
        } elseif ($var16=="����͹����Թ��͹ 2 ���" || $var16=="2 ���" || $var16=="2" || $SAH_REMARK=="2 ���") {
            $MOV_CODE = "21340";
            $SM_CODE = "4";
        } elseif ($var16=="�Թ��ҵͺ᷹����������� 2" || $var16=="�Թ�ͺ᷹����� 2%" || $var16=="2%" || $SAH_REMARK=="�Թ�ͺ᷹����� 2%") {
            $MOV_CODE = "21420";
            $SM_CODE = "5";
        } elseif ($var16=="�Թ��ҵͺ᷹����������� 2" || $var16=="�Թ�ͺ᷹����� 4%" || $var16=="4%" || $SAH_REMARK=="�Թ�ͺ᷹����� 4%") {
            $MOV_CODE = "21430";
            $SM_CODE = "17";
        } else  $MOV_CODE = "21375";
    }

    $var5 = str_replace("� 1", "�1", $var5);
    $var5 = str_replace("� 2", "�2", $var5);
    $var5 = str_replace("� 1", "�1", $var5);
    $var5 = str_replace("� 2", "�2", $var5);
    $var5 = str_replace("� 3", "�3", $var5);
    $var5 = str_replace("� 4", "�4", $var5);
    $var5 = str_replace("� 1", "�1", $var5);
    $var5 = str_replace("� 2", "�2", $var5);
    $var5 = str_replace("� 3", "�3", $var5);
    $var5 = str_replace("� 4", "�4", $var5);
    $var5 = str_replace("� 1", "�1", $var5);
    $var5 = str_replace("� 2", "�2", $var5);
    $var5 = str_replace("� 3", "�3", $var5);

    if ($var5=="��Ժѵԧҹ") $LEVEL_NO = "O1";
    elseif ($var5=="�ӹҭ�ҹ") $LEVEL_NO = "O2";
    elseif ($var5=="������") $LEVEL_NO = "O3";
    elseif ($var5=="�ѡ�о����") $LEVEL_NO = "O4";
    elseif ($var5=="��Ժѵԡ��") $LEVEL_NO = "K1";
    elseif ($var5=="�ӹҭ���") $LEVEL_NO = "K2";
    elseif ($var5=="�ӹҭ��þ����") $LEVEL_NO = "K3";
    elseif ($var5=="����Ǫҭ") $LEVEL_NO = "K4";
    elseif ($var5=="�ç�س�ز�") $LEVEL_NO = "K5";
    elseif ($var5=="�ӹ�¡�õ�") $LEVEL_NO = "D1";
    elseif ($var5=="�ӹ�¡���٧") $LEVEL_NO = "D2";
    elseif ($var5=="�����õ�") $LEVEL_NO = "M1";
    elseif ($var5=="�������٧") $LEVEL_NO = "M2";
    elseif ($var5=="��") {
        // Release 5.0.0.43 Begin
        if (strpos($SAH_POSITION,'�ӹ�¡��') !== false) {
            $LEVEL_NO = "D1";
        }else{
            $LEVEL_NO = "M1"; //�ѡ������
        }
        // Release 5.0.0.43 end.

        //���
        /*if (substr($SAH_POSITION,0,9)=="�ѡ������") $LEVEL_NO = "M1";
        else $LEVEL_NO = "D1"; */

    } elseif ($var5=="�٧") {
        // Release 5.0.0.43 Begin
        if (strpos($SAH_POSITION,'�ӹ�¡��') !== false) {
            $LEVEL_NO = "D2";
        }else{
            $LEVEL_NO = "M2"; //�ѡ������
        }
        // Release 5.0.0.43 end.

        //���
        /*if (substr($SAH_POSITION,0,9)=="�ѡ������") $LEVEL_NO = "M2";
        else $LEVEL_NO = "D2";*/
    } else {
        $cmd = " select LEVEL_NO from PER_LEVEL 
									  where LEVEL_NAME = '$var5' or  LEVEL_SHORTNAME = '$var5' or  POSITION_LEVEL = '$var5' ";
        $count_data = $db_dpis2->send_cmd($cmd);
        if ($count_data) {
            $data2 = $db_dpis2->get_array();
            $LEVEL_NO = $data2[LEVEL_NO];
        }
    }

    if (!$MOV_CODE) $MOV_CODE = "213";
    if (!$EX_CODE) $EX_CODE = "024";
    if (!$SAH_PERCENT_UP) $SAH_PERCENT_UP = "NULL";
    if (!$SAH_SALARY_UP) $SAH_SALARY_UP = "NULL";
    if (!$SAH_SALARY_EXTRA) $SAH_SALARY_EXTRA = "NULL";
    if (!$SAH_SALARY_MIDPOINT) $SAH_SALARY_MIDPOINT = "NULL";
    $SAH_SEQ_NO = (trim($SAH_SEQ_NO))? $SAH_SEQ_NO : 1;
    if (!$SAH_KF_CYCLE) $SAH_KF_CYCLE = "NULL";
    if (!$SAH_TOTAL_SCORE) $SAH_TOTAL_SCORE = "NULL";
    if (!$SAH_CMD_SEQ) $SAH_CMD_SEQ = "NULL";
    if (!$SAH_OLD_SALARY) $SAH_OLD_SALARY = "NULL";

    if (!$SAH_SALARY){
        $SAH_SALARY = $SAH_OLD_SALARY + $SAH_SALARY_UP;
    }else {
        $SAH_SALARY = $SAH_SALARY;
    }
    //echo "=>>>[".$SAH_SALARY."]"."=[".$SAH_OLD_SALARY."]"."+[".$SAH_SALARY_UP."]<br>";

    // mai edit 29 09 54
    if (!$SAH_LAST_SALARY) $SAH_LAST_SALARY = "N";
    if (!$PER_SALARY_CURRENT_UPDATE) $PER_SALARY_CURRENT_UPDATE = "N";
    if(!$locat_work)$locat_work="N";


    /*$cmd = " select PER_ID from PER_PERSONAL where PER_CARDNO = '$PER_CARDNO' or '$NAME' like '%'||PER_NAME||' '||PER_SURNAME  order by PER_TYPE";
    $count_data = $db_dpis1->send_cmd($cmd);
    if ($count_data) {
        $data1 = $db_dpis1->get_array();
        $PER_ID = $data1[PER_ID];
    }*/
     //$PER_ID = $var1;

//				$SAH_DOCDATE = show_date_format($SAH_DOCDATE, 1);
//				$SAH_ENDDATE = show_date_format($SAH_ENDDATE, 1);

    /*http://dpis.ocsc.go.th/Service/node/2271*/
    if($locat_work == 'Y'){

        if($search_per_type==1){
            $pos_from  = "PER_POSITION ";
            $pos_where = "POS_ID=POS_ID and POS_STATUS = 1";
            $pos_no = "POS_NO";
        }elseif($search_per_type==2){
            $pos_from  = "PER_POS_EMP";
            $pos_where = "POEM_ID=POEM_ID and POEM_STATUS=1";
            $pos_no = "b.POEM_NO";
        }elseif($search_per_type==3){
            $pos_from  = "PER_POS_EMPSER";
            $pos_where = "POEMS_ID=POEMS_ID and POEM_STATUS=1";
            $pos_no = "POEMS_NO";
        }elseif($search_per_type==4){
            $pos_from  = "PER_POS_TEMP";
            $pos_where = "POT_ID=POT_ID and  POT_STATUS=1";
            $pos_no = "POT_NO";
        }
		
        if(is_numeric($var3)){
            $cmd = "select DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2
                from  $pos_from
                where $pos_no = '$var3' and $pos_where";
            $db_dpis1->send_cmd($cmd);
            $data = $db_dpis1->get_array();
            $DEPARTMENT_ID = $data[DEPARTMENT_ID];
            $ORG_ID_1   = $data[ORG_ID];
            $ORG_ID_2 = $data[ORG_ID_1];
            $ORG_ID_3 = $data[ORG_ID_2];

           // echo "<pre>".$DEPARTMENT_ID."| ".$ORG_ID_1." |".$ORG_ID_2." |".$ORG_ID_3;
            $CMD_ORG2 = $CMD_ORG3 = $CMD_ORG4 = $CMD_ORG5 = $CMD_ORG6 = $CMD_ORG7 = $CMD_ORG8 = "";
            $cmd1 = " select a.ORG_ID, a.ORG_NAME from PER_ORG a where a.ORG_ID IN ('$DEPARTMENT_ID', '$ORG_ID_1', '$ORG_ID_2', '$ORG_ID_3') ";
            $db_dpis6->send_cmd($cmd1);
            while ( $data2 = $db_dpis6->get_array() ) {
                $temp_id = trim($data2[ORG_ID]);
                $CMD_ORG2 = ($temp_id == $DEPARTMENT_ID)?  trim($data2[ORG_NAME]) : $CMD_ORG2;
                $CMD_ORG3 = ($temp_id == $ORG_ID_1)?  trim($data2[ORG_NAME]) : $CMD_ORG3;
                $CMD_ORG4 = ($temp_id == $ORG_ID_2)?  trim($data2[ORG_NAME]) : $CMD_ORG4;
                $CMD_ORG5 = ($temp_id == $ORG_ID_3)?  trim($data2[ORG_NAME]) : $CMD_ORG5;
            }
            $cmd = " select OT_CODE from PER_ORG where ORG_ID=$ORG_ID_1 ";
            $db_dpis2->send_cmd($cmd);
			//echo "<pre>".$cmd;
            $data2 = $db_dpis2->get_array();
            $OT_CODE = trim($data2[OT_CODE]);
            if ($OT_CODE == "03"){
                if (!$CMD_ORG5 && !$CMD_ORG4 && $search_department_name=="�����û���ͧ"){
                    $ORG_NAME_WORK = "���ӡ�û���ͧ".$CMD_ORG3." ".$CMD_ORG3;
                }else{
                    $ORG_NAME_WORK = trim($CMD_ORG5." ".$CMD_ORG4." ".$CMD_ORG3);
                }
            }elseif ($OT_CODE == "01"){
                $ORG_NAME_WORK = trim($CMD_ORG5." ".$CMD_ORG4." ".$CMD_ORG3." ".$CMD_ORG2);
            }else{
                $ORG_NAME_WORK = trim($CMD_ORG4." ".$CMD_ORG3);
            }
            $SAH_ORG=$ORG_NAME_WORK;
        }
    }else{
        $SAH_ORG = $var19;

    }


    /* ^ ^ ^ ^ ^ ^ ^  ��ǹ�ͧ���������е����ͧ��� ^ ^ ^ ^ ^ ^ ^ ^*/

    for($i = 0; $i < count($i_data_map); $i++) {
        $data_stru = explode("=",$i_data_map[$i]);
        $i_data_map[$i] = $data_stru[0]."=".$data_stru[1]."=".$$data_stru[1];
    }

    return $i_data_map;
}

// �� function ����Ѻ ��˹���Ҿ����
function data_save_extend($i_data_map, $var_d_in) {
    global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port;
    global $search_kf_cycle, $search_budget_year;
    global $PER_SALARY_CURRENT_UPDATE;

    $db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port);
    $db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port);

//		echo "data=".implode(",",$i_data_map)."<br>";
    // �����ŷ������ż����Ǿ����ŧ�ҹ������
    for($i = 0; $i < count($i_data_map); $i++) {
        $data_stru = explode("=",$i_data_map[$i]);
        global $$data_stru[1];		// ���͵��������͹���� column 㹰ҹ������
        $$data_stru[1] = $data_stru[2];	// �����ҡ�Ѵ��õ�� column_map ���������
    }
    // �����Ŵ��������������
    for($i = 0; $i < count($var_d_in); $i++) {
        $data_org = explode("=",$var_d_in[$i]);
        $$data_org[0] = $data_org[1];	// ���������Ŵ�����ŧ㹵���Ū��� varn
    }

    /* v v v v v v v v v v ��ǹ�ͧ���������е����ͧ��� v v v v v v v v v v*/
    $cmd = " select PER_ID from PER_PERSONAL where PER_ID = $var1 ";
    $count_data = $db_dpis1->send_cmd($cmd);

    if ($count_data) {
        $data1 = $db_dpis1->get_array();
        $tmp_PER_ID = $data1[PER_ID];

        // update and insert into PER_SALARYHIS
        $cmd = " select SAH_ID, SAH_EFFECTIVEDATE from PER_SALARYHIS where PER_ID=$tmp_PER_ID 
									order by SAH_EFFECTIVEDATE desc, SAH_SEQ_NO desc, SAH_SALARY desc, SAH_DOCNO desc ";
        $db_dpis2->send_cmd($cmd);
        $data2 = $db_dpis2->get_array();
        $tmp_SAH_ID = trim($data2[SAH_ID]);
        $tmp_SAH_EFFECTIVEDATE = trim($data2[SAH_EFFECTIVEDATE]);
        $cmd = " update PER_SALARYHIS set SAH_ENDDATE='$before_cmd_date', 
									UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'			
									where SAH_ID=$tmp_SAH_ID";

        $db_dpis2->send_cmd($cmd);
        //$db_dpis2->show_error();

        if ( $SAH_LAST_SALARY == "Y" ) { // ��Ǩ�ͺ��Ҷ����¡�÷����������¡������ش��������¡�÷�������� SAH_LAST_SALARY ='N'
            $cmd = " update PER_SALARYHIS set SAH_LAST_SALARY='N' 		
									where PER_ID=$tmp_PER_ID";
            $db_dpis2->send_cmd($cmd);
            //$db_dpis2->show_error();
        }
		//echo "<pre>".$cmd;
       // echo "<br>";
        /*					echo "</br>������ $NAME";
                            echo "</br>����繻���ѵ��Թ��͹����ش : ".$SAH_LAST_SALARY;
                            echo "</br>�ѹ�������ش : ".$SAH_ENDDATE;
                            echo "</br>����Ѻ��ا�Թ��͹�Ѩ�غѹ : ".$PER_SALARY_CURRENT_UPDATE;
                            echo "</br>XLS �Թ��͹��͹����͹ : ".$PER_SALARY;
                            echo "</br>XLS �Թ��͹��ѧ����͹ : ".$SAH_SALARY;
                            $cmd = " insert into PER_SALARYHIS (SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY,
                                            SAH_DOCNO, SAH_DOCDATE, SAH_ENDDATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE,
                                            SAH_PERCENT_UP, SAH_SALARY_UP, SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK,
                                            LEVEL_NO, SAH_POS_NO, SAH_POSITION, SAH_ORG, EX_CODE, SAH_PAY_NO, SAH_SALARY_MIDPOINT,
                                            SAH_KF_YEAR, SAH_KF_CYCLE, SAH_TOTAL_SCORE, SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ, SAH_OLD_SALARY)
                                            values ($SAH_ID, $PER_ID, '$SAH_EFFECTIVEDATE', '$MOV_CODE', $SAH_SALARY, '$SAH_DOCNO',
                                            '$SAH_DOCDATE', '$SAH_ENDDATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE',
                                            $SAH_PERCENT_UP, $SAH_SALARY_UP, $SAH_SALARY_EXTRA, $SAH_SEQ_NO, '$SAH_REMARK',
                                            '$LEVEL_NO', '$SAH_POS_NO', '$SAH_POSITION', '$SAH_ORG', '$EX_CODE', '$SAH_POS_NO',
                                            $SAH_SALARY_MIDPOINT, '$SAH_KF_YEAR', $SAH_KF_CYCLE, $SAH_TOTAL_SCORE,
                                            '$SAH_LAST_SALARY', '$SM_CODE', $SAH_CMD_SEQ, $PER_SALARY) ";
                            $db_dpis1->send_cmd($cmd);
                            //$db_dpis1->show_error();
                            echo "$cmd<br>";
        */
        if ($PER_SALARY_CURRENT_UPDATE=='Y' ){
            $cmd = " update PER_PERSONAL set PER_SALARY = $SAH_SALARY , MOV_CODE = '$MOV_CODE' ,
										PER_DOCNO = '$SAH_DOCNO' , PER_DOCDATE = '$SAH_DOCDATE' where PER_ID = $tmp_PER_ID ";
            $db_dpis1->send_cmd($cmd);
            //$db_dpis1->show_error();
			
			$cmd = " select pos_id from  PER_PERSONAL where PER_ID = $tmp_PER_ID and per_type = 1 and per_status = 1";
            $db_dpis2->send_cmd($cmd);
			$data = $db_dpis2 ->get_array();
			
			if($data[POS_ID]){
				$cmd = " update per_position set pos_salary = $SAH_SALARY  where POS_ID = $data[POS_ID] ";
				$db_dpis2->send_cmd($cmd);
				//echo $cmd."<br>";
			}
        }

        $SAH_ID++;
    } // else echo "$PER_CARDNO $NAME<br>";
    //echo "<meta http-equiv=\"refresh\" content=\"0;URL=select_database_excel.html?excel_msg=".$excel_msg."\">";

    /* ^ ^ ^ ^ ^ ^ ^  ��ǹ�ͧ���������е����ͧ��� ^ ^ ^ ^ ^ ^ ^ ^*/

}

?>