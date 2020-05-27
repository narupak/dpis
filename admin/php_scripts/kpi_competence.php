<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

        
        
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
		case 6 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$search_org_id_1 = $ORG_ID_1;
			$search_org_name_1 = $ORG_NAME_1;
			break;
	} // end switch case

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
//		$$arr_fields[$i]=str_replace("'","''",$$arr_fields[$i]);
//		if($UPD){
//			$$arr_fields[$i]=str_replace("\"","&quot;",$$arr_fields[$i]);
//			}

	$UPDATE_DATE = date("Y-m-d H:i:s");
//echo $command;
	
	if ($command == "SETASSESSMENT") {
		$setflagassessment =  implode(",",$list_assessment_id);
		$cmd = " update PER_COMPETENCE set CP_ASSESSMENT = 'N' where CP_CODE in (".stripslashes($current_list).") and DEPARTMENT_ID = $DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
//echo $cmd;
		$cmd = " update PER_COMPETENCE set CP_ASSESSMENT = 'Y' where CP_CODE in (".stripslashes($setflagassessment).") and DEPARTMENT_ID = $DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
//echo $cmd;
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��駤�ҡ�û����Թ���ö��");
	}
        if($command == "SET_DEF_WEIGHT"){
            foreach($DEF_WEIGHT as $CP_CODE => $DEF_WEIGHT_VAL){
                if(empty($DEF_WEIGHT_VAL)){
                    $DEF_WEIGHT_VAL='NULL';
                }
                $cmd = " update PER_COMPETENCE set DEF_WEIGHT=$DEF_WEIGHT_VAL where CP_CODE=$CP_CODE ";
                $db_dpis->send_cmd($cmd);
            }
            insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��ҹ��˹ѡ��駵� (������)");
        }
	
	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
		$cmd = " update PER_COMPETENCE set CP_ACTIVE = 0 where CP_CODE in (".stripslashes($current_list).") and DEPARTMENT_ID = $DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$cmd = " update PER_COMPETENCE set CP_ACTIVE = 1 where CP_CODE in (".stripslashes($setflagshow).") and DEPARTMENT_ID = $DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��駤�ҡ����ҹ������");
	}
	
	if (!$CP_ENG_NAME) $CP_ENG_NAME = $CP_NAME;
	if($command == "ADD" && trim($CP_CODE)){
		$cmd = " select CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE  
				  from PER_COMPETENCE where CP_CODE='$CP_CODE' and DEPARTMENT_ID = $DEPARTMENT_ID ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
                //echo $cmd.'<br>';
		if($count_duplicate <= 0){
                    $DEF_WEIGHT_VAL=$txt_DEF_WEIGHT;
                    if(empty($txt_DEF_WEIGHT)){$DEF_WEIGHT_VAL='NULL';}
			$cmd = " insert into PER_COMPETENCE (CP_CODE, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, CP_ASSESSMENT, DEPARTMENT_ID,DEF_WEIGHT) 
							  values ('$CP_CODE', '$CP_NAME', '$CP_ENG_NAME', '$CP_MEANING', $CP_MODEL, $CP_ACTIVE, $SESS_USERID, 
							  '$UPDATE_DATE', '$CP_ASSESSMENT', $DEPARTMENT_ID,$DEF_WEIGHT_VAL) ";
			$db_dpis->send_cmd($cmd);
                        //echo $cmd;
//			$db_dpis->show_error();

			if ($CP_MODEL==1 || $CP_MODEL==3) {
				$level = array(	"O1", "O2", "O3", "O4", "K1", "K2", "K3", "K4", "K5", "D1", "D2", "M1", "M2" );
				$target = array(	1, 1, 2, 3, 1, 2, 3, 4, 5, 3, 4, 4, 5 );
				for ( $i=0; $i<count($level); $i++ ) { 
					if ($level[$i]=="O1" && substr($CP_CODE,0,1)=="3") $TARGET_LEVEL = 0; else $TARGET_LEVEL = $target[$i]; 
					$cmd = " INSERT INTO PER_STANDARD_COMPETENCE (LEVEL_NO, CP_CODE, TARGET_LEVEL, UPDATE_USER, 
									UPDATE_DATE)
									VALUES ('$level[$i]', '$CP_CODE', $TARGET_LEVEL,  $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} // end for
			} // end if
			
			if ($CP_MODEL==2) {
				$level = array(	"D1", "D2", "M1", "M2" );
				$target = array(	1, 2, 3, 4 );
				for ( $i=0; $i<count($level); $i++ ) { 
					$cmd = " INSERT INTO PER_STANDARD_COMPETENCE (LEVEL_NO, CP_CODE, TARGET_LEVEL, UPDATE_USER, 
									UPDATE_DATE)
									VALUES ('$level[$i]', '$CP_CODE', $target[$i],  $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} // end for
			} // end if
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $menu_detail ���������� [$CP_CODE : $DEPARTMENT_ID : $CP_NAME : $CP_MEANING : $CP_MODEL]");
				$success_sql = "���������� [$CP_CODE : $DEPARTMENT_ID : $CP_NAME : $CP_MEANING : $CP_MODEL]���º��������"; //��˹����ǹ�ͧ php
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "���ʢ����ū�� [$data[CP_CODE] $data[CP_NAME] $data[CP_MEANING]";
		} // endif
	}

	if($command == "UPDATE" && trim($CP_CODE)){
            $DEF_WEIGHT_VAL=$txt_DEF_WEIGHT;
            if(empty($txt_DEF_WEIGHT)){$DEF_WEIGHT_VAL='NULL';}
		$cmd = " update PER_COMPETENCE set 
								CP_CODE='$CP_CODE', 
								CP_NAME='$CP_NAME', 
								CP_ENG_NAME='$CP_ENG_NAME', 
								CP_MEANING='$CP_MEANING', 
								CP_MODEL=$CP_MODEL, 
								CP_ACTIVE=$CP_ACTIVE, 
								CP_ASSESSMENT='$CP_ASSESSMENT', 
                                                                DEF_WEIGHT=$DEF_WEIGHT_VAL,
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE' 
							where CP_CODE='$CP_CODE' and DEPARTMENT_ID = $DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢢����� [$CP_CODE : $DEPARTMENT_ID : $CP_NAME : $CP_MEANING : $CP_MODEL]");
	}
	
	if($command == "DELETE" && trim($CP_CODE)){
		$cmd = " select CP_NAME from PER_COMPETENCE where CP_CODE='$CP_CODE' and DEPARTMENT_ID = $DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CP_NAME = $data[CP_NAME];
		
		$cmd = " delete from PER_COMPETENCE where CP_CODE='$CP_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź������ [$CP_CODE : $DEPARTMENT_ID : $CP_NAME : $CP_MEANING : $CP_MODEL]");
	}
	
	if($UPD){
		$cmd = " select CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE, CP_ASSESSMENT, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE,DEF_WEIGHT 
				  from PER_COMPETENCE where CP_CODE='$CP_CODE' and DEPARTMENT_ID = $DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CP_NAME = $data[CP_NAME];
		$CP_ENG_NAME = $data[CP_ENG_NAME];
		$CP_MEANING = $data[CP_MEANING];
		$CP_MODEL = $data[CP_MODEL];
		$CP_ACTIVE = $data[CP_ACTIVE];
		$CP_ASSESSMENT = $data[CP_ASSESSMENT];
                $DEF_WEIGHT = $data[DEF_WEIGHT];
                
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		
		$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = $data[ORG_NAME];
		$MINISTRY_ID = $data[ORG_ID_REF];

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];
//		for($i=0;$i<sizeof($arr_fields);$i++){
//			$$arr_fields[$i]=str_replace("\"","&quot;",$$arr_fields[$i]);
//			}
		} // end if
	
	if( $command=='GENDATA' ) {
		$cmd = " SELECT DEPARTMENT_ID FROM PER_COMPETENCE ";
		$count_data = $db_dpis->send_cmd($cmd);

		if (!$count_data) {
			$cmd = " DROP TABLE PER_COMPETENCE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_COMPETENCE(
				CP_CODE VARCHAR(3) NOT NULL,	
				DEPARTMENT_ID INTEGER,
				CP_NAME VARCHAR(100) NOT NULL,	
				CP_ENG_NAME VARCHAR(100) NOT NULL,	
				CP_MEANING MEMO NULL,	
				CP_MODEL SINGLE NOT NULL,
				CP_ASSESSMENT CHAR(1) NULL,
				CP_ACTIVE SINGLE NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCE PRIMARY KEY (CP_CODE, DEPARTMENT_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_COMPETENCE(
				CP_CODE VARCHAR2(3) NOT NULL,	
				DEPARTMENT_ID NUMBER(10),
				CP_NAME VARCHAR2(100) NOT NULL,	
				CP_ENG_NAME VARCHAR2(100) NOT NULL,	
				CP_MEANING VARCHAR2(1000) NULL,	
				CP_MODEL NUMBER(1) NOT NULL,
				CP_ASSESSMENT CHAR(1) NULL,
				CP_ACTIVE NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCE PRIMARY KEY (CP_CODE, DEPARTMENT_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_COMPETENCE(
				CP_CODE VARCHAR(3) NOT NULL,	
				DEPARTMENT_ID INTEGER(10),
				CP_NAME VARCHAR(100) NOT NULL,	
				CP_ENG_NAME VARCHAR(100) NOT NULL,	
				CP_MEANING TEXT NULL,	
				CP_MODEL SMALLINT(1) NOT NULL,
				CP_ACTIVE SMALLINT(1) NOT NULL,
				CP_ASSESSMENT CHAR(1) NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCE PRIMARY KEY (CP_CODE, DEPARTMENT_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		}

		if ($DEPARTMENT_ID) $arr_dept[] = $DEPARTMENT_ID;
		elseif($CTRL_TYPE==1 || $CTRL_TYPE==2 || $CTRL_TYPE==3 || $BKK_FLAG==1){
//			$cmd = " SELECT DISTINCT DEPARTMENT_ID FROM PER_POS_EMPSER WHERE DEPARTMENT_ID > 1 AND POEM_STATUS = 1 ";
			$cmd = " SELECT DISTINCT a.DEPARTMENT_ID FROM PER_POSITION a, PER_ORG b 
							WHERE a.DEPARTMENT_ID = b.ORG_ID and a.DEPARTMENT_ID > 1 and b.OL_CODE = '02' and POS_STATUS = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()) $arr_dept[] = $data[DEPARTMENT_ID];
		}else $arr_dept[] = $DEPARTMENT_ID;

		if ($DEPARTMENT_ID)
			$cmd = " DELETE FROM PER_COMPETENCE_LEVEL WHERE DEPARTMENT_ID = $DEPARTMENT_ID ";
		else
			$cmd = " DELETE FROM PER_COMPETENCE_LEVEL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($DEPARTMENT_ID)
			$cmd = " DELETE FROM PER_COMPETENCE WHERE DEPARTMENT_ID = $DEPARTMENT_ID ";
		else
			$cmd = " DELETE FROM PER_COMPETENCE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($DEPARTMENT_ID)
			$cmd = " DELETE FROM PER_STANDARD_COMPETENCE WHERE DEPARTMENT_ID = $DEPARTMENT_ID ";
		else
			$cmd = " DELETE FROM PER_STANDARD_COMPETENCE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($DEPARTMENT_ID)
			$cmd = " DELETE FROM PER_TYPE_COMPETENCE WHERE DEPARTMENT_ID = $DEPARTMENT_ID ";
		else
			$cmd = " DELETE FROM PER_TYPE_COMPETENCE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		for ( $j=0; $j<count($arr_dept); $j++ ) { 
			if($BKK_FLAG==1){
				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('101', $arr_dept[$j], '�س�������¸���', 'Integrity', '��ä�ͧ����л�оĵԻ�ԺѵԵ��١��ͧ�����ѡ�س�������¸���  �դ����ӹ֡����Ѻ�Դ�ͺ��͵��ͧ ���˹�˹�ҷ��  ��ʹ���ԪҪվ�ͧ�����͸�ç�ѡ���ѡ����������Ҫվ����Ҫ���', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], '��ú�ԡ�÷���', 'Service Mind', '�ĵԡ�������ʴ��֧�������� ���������� ���������㹡������ԡ�û�ЪҪ� ����ط���������͵ͺʹͧ������ͧ��âͧ����Ѻ��ԡ��', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('103', $arr_dept[$j], '�����觼����ķ���', 'Achievement Motivation', '����������� ��û�Ժѵ��Ҫ�������Դ�����ķ������ҵðҹ��Фس�Ҿ�ҹ����˹�  ���/��������Թ�ҡ�ҵðҹ����˹�', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('104', $arr_dept[$j], '��÷ӧҹ�繷��', 'Teamwork', '��÷ӧҹ���վĵԡ����������ö�ӧҹ�����ѹ�Ѻ��������  ����ö�Ѻ�ѧ�����Դ��繢ͧ��Ҫԡ㹷��  �������¹���ҡ������  ����դ�������ö㹡�����ҧ����ѡ������ѹ��Ҿ�Ѻ��Ҫԡ', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('105', $arr_dept[$j], '����������������Ǫҭ㹧ҹ�Ҫվ', 'Expertise', '�����ǹ����  ʹ���������;Ѳ���ѡ��Ҿ  ��������������ö�ͧ��㹡�û�Ժѵԧҹ�Ҫ���  ���¡���֡��  �鹤����Ҥ������  �Ѳ�ҵ��ͧ���ҧ������ͧ  �ա������ѡ�Ѳ�� ��Ѻ��ا����ء�����������ԧ�Ԫҡ�����෤����յ�ҧ � ��ҡѺ��û�Ժѵԧҹ����Դ�����ķ���', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} else {
				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('101', $arr_dept[$j], '�����觼����ķ���', 'Achievement Motivation', '���������蹨л�Ժѵ�˹�ҷ���Ҫ���������������Թ�ҵðҹ��������� ���ҵðҹ����Ҩ�繼š�û�Ժѵԧҹ����ҹ�Ңͧ���ͧ ����ࡳ���Ѵ�����ķ�������ǹ�Ҫ��á�˹���� �ա����ѧ��������֧������ҧ��ä�Ѳ�Ҽŧҹ���͡�кǹ��û�Ժѵԧҹ���������·���ҡ��з�ҷ�ª�Դ����Ҩ������ռ�������ö��з����ҡ�͹', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], '��ԡ�÷���', 'Service Mind', '����������Ф����������ͧ����Ҫ���㹡������ԡ�õ�ͻ�ЪҪ� ����Ҫ��� ����˹��§ҹ���� �������Ǣ�ͧ', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('103', $arr_dept[$j], '����������������Ǫҭ㹧ҹ�Ҫվ', 'Expertise', '����ʹ������ ����� ��������������ö�ͧ��㹡�û�Ժѵ�˹�ҷ���Ҫ��� ���¡���֡�� �鹤��� ��оѲ�ҵ��ͧ���ҧ������ͧ ������ö����ء�����������ԧ�Ԫҡ�����෤����յ�ҧ� ��ҡѺ��û�Ժѵ��Ҫ�������Դ�����ķ���', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('104', $arr_dept[$j], '����ִ���㹤����١��ͧ�ͺ���� ��Ш��¸���', 'Integrity', '��ô�ç����л�оĵԻ�Ժѵ����ҧ�١��ͧ���������駵�������� �س���� ����Һ�ó����ԪҪվ ��Ш���Ң���Ҫ��������ѡ���ѡ��������觤����繢���Ҫ���', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('105', $arr_dept[$j], '��÷ӧҹ�繷��', 'Teamwork', '�������㨷��зӧҹ�����Ѻ������ ����ǹ˹�觢ͧ��� ˹��§ҹ ������ǹ�Ҫ��� �¼�黯Ժѵ��հҹ�����Ҫԡ �����繵�ͧ�հҹ����˹�ҷ�� �����駤�������ö㹡�����ҧ����ѡ������ѹ��Ҿ�Ѻ��Ҫԡ㹷��', 1, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('201', $arr_dept[$j], '����м���', 'Leadership', '��������ö ���ͤ������㨷����Ѻ��㹡���繼��Ӣͧ����� ��˹���ȷҧ ������� �Ըա�÷ӧҹ �������Ժѵԧҹ�����ҧ�Һ��� �������Է���Ҿ��к�����ѵ�ػ��ʧ��ͧ��ǹ�Ҫ���', 2, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('202', $arr_dept[$j], '����·�ȹ�', 'Visioning', '��������ö㹡�á�˹���ȷҧ ��áԨ ���������¡�÷ӧҹ���Ѵਹ ��Ф�������ö㹡�����ҧ���������ç��������������áԨ������ѵ�ػ��ʧ��', 2, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('203', $arr_dept[$j], '����ҧ���ط���Ҥ�Ѱ', 'Strategic Orientation', '������������·�ȹ���й�º���Ҥ�Ѱ�������ö���һ���ء����㹡�á�˹����ط��ͧ��ǹ�Ҫ�����', 2, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('204', $arr_dept[$j], '�ѡ��Ҿ���͹ӡ�û�Ѻ����¹', 'Change Leadership', '��������ö㹡�á�е�� ���ͼ�ѡ�ѹ˹��§ҹ�����û�Ѻ����¹����繻���ª�� ����֧�������������������Ѻ��� ���� ��д��Թ�������û�Ѻ����¹����Դ��鹨�ԧ', 2, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('205', $arr_dept[$j], '��äǺ������ͧ', 'Self Control', '��������ö㹡�äǺ�����������оĵԡ����ʶҹ��ó����Ҩ�ж١������ ����༪ԭ˹�ҡѺ����������Ե� ���͵�ͧ�ӧҹ���������С��ѹ ����֧����ʹ��ʹ��������������ʶҹ��ó����ͤ������´���ҧ������ͧ', 2, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('206', $arr_dept[$j], '����͹�ҹ��С���ͺ���§ҹ', 'Coaching and Empowering Others', '�������㨷����������������¹������͡�þѲ�Ҽ������������Ǩ��֧�дѺ������������Ҩ� ����ö�ͺ����˹�ҷ������Ѻ�Դ�ͺ�������������з��еѴ�Թ�㹡�û�Ժѵ�˹�ҷ���Ҫ��âͧ����', 2, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('301', $arr_dept[$j], '��äԴ��������', 'Analytical Thinking', '��÷Ӥ������������������ʶҹ��ó� ����繻ѭ�� �ǤԴ�¡���¡��л�����͡����ǹ����� ���ͷ��Т�鹵͹ ����֧��èѴ��Ǵ�������ҧ���к�����º ���º��º�������ҧ� ����ö�ӴѺ�����Ӥѭ ��ǧ���� �˵���м� ����ҷ��仢ͧ�óյ�ҧ���', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('302', $arr_dept[$j], '����ͧ�Ҿͧ�����', 'Conceptual Thinking', '��äԴ��ԧ�ѧ������ �ͧ�Ҿͧ����� �¡�èѺ����� ��ػ�ٻẺ ������§���ͻ���ء���Ƿҧ�ҡʶҹ��ó� ������ ���ͷ�ȹе�ҧ � �����繡�ͺ�����Դ�����ǤԴ����', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('303', $arr_dept[$j], '���������оѲ�Ҽ�����', 'Caring Others', '����������е��㨷���������� ��Ѻ��ا��оѲ�������������ѡ��Ҿ �������آ���з�駷ҧ�ѭ�� ��ҧ��� �Ե� ��з�ȹ��Է������ҧ����׹�Թ���ҡ�ͺ�ͧ��û�Ժѵ�˹�ҷ��', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('304', $arr_dept[$j], '�����觡�õ���ӹҨ˹�ҷ��', 'Holding People Accountable', '��áӡѺ�����������蹻�ԺѵԵ���ҵðҹ �� ����º ��ͺѧ�Ѻ ��������ӹҨ��������� ���͵�����˹�˹�ҷ�� ��áӡѺ���Ź�� ��������֧����͡������»��Է���仨��֧������ӹҨ��������¡Ѻ����ҽ׹', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('305', $arr_dept[$j], '����׺�����Ң�����', 'Information Seeking', '����������ԧ�֡������ǧ�Ң���������ǡѺʶҹ��ó� ������ѧ ����ѵԤ������� ����繻ѭ�� ��������ͧ��ǵ�ҧ� �������Ǣ�ͧ���ͨ��繻���ª��㹡�û�Ժѵԧҹ', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('306', $arr_dept[$j], '�������㨢��ᵡ��ҧ�ҧ�Ѳ�����', 'Cultural Sensitivity', '����Ѻ���֧���ᵡ��ҧ�ҧ�Ѳ����� �������ö����ء��������� �������ҧ����ѹ��Ҿ�����ҧ�ѹ��', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('307', $arr_dept[$j], '�������㨼�����', 'Interpersonal Understanding', '��������ö㹡���Ѻ�ѧ������㨤������µç ��������ὧ �����Դ ��ʹ������зҧ������ͧ�����Դ��ʹ���', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('308', $arr_dept[$j], '��������ͧ�������к��Ҫ���', 'Organizational Awareness', '��������ö㹡�����㨤�������ѹ���ԧ�ӹҨ��������� ����ӹҨ�������繷ҧ��� �ͧ��âͧ�����ͧ�����蹷������Ǣ�ͧ���ͻ���ª��㹡�û�Ժѵ�˹�ҷ���������������� �����駤�������ö���ФҴ��ó�����ҹ�º���Ҥ�Ѱ ������ҧ������ͧ ���ɰ�Ԩ �ѧ�� ෤����� ��ʹ���˵ء�ó� �����Դ��� ���ռŵ��ͧ������ҧ��', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('309', $arr_dept[$j], '��ô��Թ����ԧ�ء', 'Proactiveness', '��������繻ѭ�������͡�ʾ������駨Ѵ����ԧ�ء�Ѻ�ѭ�ҹ�����Ҩ ���������ͧ�� ������ҧ�����ͷ�� �������͡�ʹ������Դ����ª���ͧҹ ��ʹ����äԴ����������ҧ��ä������ ����ǡѺ�ҹ���� ������ѭ�� ��ͧ�ѹ�ѭ�� �������ҧ�͡�ʴ���', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('310', $arr_dept[$j], '��õ�Ǩ�ͺ�����١��ͧ�����кǹ�ҹ', 'Concern for Order', '�������㨷��л�Ժѵԧҹ���١��ͧ �ú��ǹ ����鹤����Ѵਹ�ͧ���ҷ ˹�ҷ�� ���Ŵ��ͺ����ͧ����Ҩ�Դ�ҡ��Ҿ�Ǵ���� �µԴ��� ��Ǩ�ͺ��÷ӧҹ���͢����� ��ʹ���Ѳ���к���õ�Ǩ�ͺ���ͤ����١��ͧ�ͧ��кǹ�ҹ', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('311', $arr_dept[$j], '��������㹵��ͧ', 'Self Confidence', '��������㹤�������ö �ѡ��Ҿ ��С�õѴ�Թ㨢ͧ�����л�Ժѵԧҹ������ؼ� �������͡�Ըշ���ջ���Է���Ҿ㹡�û�Ժѵԧҹ ������䢻ѭ��������������ǧ', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('312', $arr_dept[$j], '�����״���蹼�͹�ù', 'Flexibility', '��������ö㹡�û�Ѻ��� ��л�Ժѵԧҹ�����ҧ�ջ���Է���Ҿ�ʶҹ��ó���С�����������ҡ���� ���¤�������֧�������Ѻ������繷��ᵡ��ҧ ��л�Ѻ����¹�Ըա�������ʶҹ��ó�����¹�', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('313', $arr_dept[$j], '��ŻС��������è٧�', 'Communication & Influencing', '��������ö�������ͤ������¡����¹ �ٴ �������͵�ҧ� ���������������� ����Ѻ ���ʹѺʹع�����Դ�ͧ��', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('314', $arr_dept[$j], '�ع�����Ҿ�ҧ��Ż�', 'Aesthetic Quality', '�����Һ������ö�������繤س��Ңͧ�ҹ��Żз�����͡�ѡɳ�����ô��ͧ�ҵ� ����֧�ҹ��Ż���� � ��й��һ���ء��㹡�����ҧ��ä�ҹ��ŻТͧ����', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('315', $arr_dept[$j], '�����١�ѹ����յ����ǹ�Ҫ���', 'Organizational Commitment', '�Ե�ӹ֡���ͤ������㨷����ʴ��͡��觾ĵԡ�������ʹ���ͧ�Ѻ������ͧ��� ���������¢ͧ��ǹ�Ҫ��� �ִ��ͻ���ª��ͧ��ǹ�Ҫ����繷���駡�͹����ª����ǹ���', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, DEPARTMENT_ID, CP_NAME, CP_ENG_NAME, CP_MEANING, CP_MODEL, CP_ASSESSMENT, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('316', $arr_dept[$j], '������ҧ����ѹ��Ҿ', 'Relationship Building', '���ҧ�����ѡ������ѹ��Ҿ�ѹ�Ե� ���ͤ�������ѹ����������ҧ�������Ǣ�ͧ�Ѻ�ҹ', 3, 'Y', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			if($BKK_FLAG==1)
				$code = array(	"101", "102", "103", "104", "105" );
			else
				$code = array(	"101", "102", "103", "104", "105", "201", "202", "203", "204", "205", "206", "301", "302", "303", "304", "305", "306", 
												"307", "308", "309", "310", "311", "312", "313", "314", "315", "316" );
			for ( $i=0; $i<count($code); $i++ ) { 
				if($BKK_FLAG==1)
					$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
									  UPDATE_DATE)
									  VALUES ('$code[$i]', $arr_dept[$j], 0, '����ʴ����ö�д�ҹ������ҧ�Ѵਹ', NULL, $SESS_USERID, '$UPDATE_DATE') ";
				else
					$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
									  UPDATE_DATE)
									  VALUES ('$code[$i]', $arr_dept[$j], 0, '����ʴ����ö�д�ҹ��� �����ʴ����ҧ���Ѵਹ', NULL, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end for

			if($BKK_FLAG==1) {
				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('101', $arr_dept[$j], 1, '�����ѵ���ب�Ե', '��Ժѵ�˹�ҷ����¤�������� �����ѵ���ب�Ե �١��ͧ��駵����ѡ������ ���¸����������º�Թ��
�ʴ������Դ��繢ͧ�������ѡ�ԪҪվ���ҧ�Դ�µç仵ç��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('101', $arr_dept[$j], 2, '���Ѩ�����Ͷ����', '�ѡ���Ҩ� ���Ѩ�����Ͷ���� �ٴ���ҧ�÷����ҧ��� ���Դ��͹��ҧ���¡�����鵹�ͧ
�ըԵ�ӹ֡��Ф����Ҥ�����㹤����繢���Ҫ��� �ط���ç����ç㨼�ѡ�ѹ�����áԨ��ѡ�ͧ�����˹��§ҹ����ؼ�����ʹѺʹع���������þѲ�һ���Ȫҵ�����ѧ����', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('101', $arr_dept[$j], 3, '��ä�ͧ�����դ����Ѻ�Դ�ͺ��͵��ͧ��е�͵��˹�˹�ҷ��', '�ִ������ѡ�����Ш���Һ�ó�ͧ�ԪҪվ  ������§ູ����ͤ�����ͼŻ���ª����ǹ��
������Ф����آʺ�µ�ʹ�������֧�����ǹ�����ͧ͢��ͺ����  ����������áԨ�˹�ҷ�����ķ�������Ӥѭ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('101', $arr_dept[$j], 4, '��ç�����١��ͧ', '��ç�����١��ͧ �׹��Ѵ�Էѡ��Ż���ª����Ъ������§�ͧ˹��§ҹ����ʶҹ��ó����Ҩ���ҧ�����Ӻҡ����
�Ѵ�Թ��˹�ҷ�� ��Ժѵ��Ҫ��ô��¤����١��ͧ ����� �繸������Ţͧ��û�Ժѵ��Ҩ���ҧ�ѵ�����͡�ͤ������֧����������������Ǣ�ͧ�������»���ª��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('101', $arr_dept[$j], 5, '�ط�ȵ����ͼ�ا�����صԸ���', '��ç�����١��ͧ �׹��Ѵ�Էѡ��Ż���ª����Ъ������§�ͧ˹��§ҹ �к��Ҫ������ͻ���Ȫҵ� ����ʶҹ��ó����Ҩ����§��ͤ�����蹤�㹵��˹�˹�ҷ���çҹ �����Ҩ�դ�������§�������µ�ͪ��Ե', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], 1, '����ԡ�÷�����Ե�', '�����й���Ф�µԴ�������ͧ�������ͼ���Ѻ��ԡ���դӶ��  ������¡��ͧ  ���͢����ͧ���¹��ҧ �������ǡѺ��áԨ�ͧ˹��§ҹ
����ԡ�ô����Ѹ�����������ѹ��  ������ҧ������зѺ������Ѻ��ԡ��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], 2, '������â�������Ѵਹ', '������â�����  �������  �������  �ͧ��ú�ԡ�÷��Ѵਹ�Ѻ����Ѻ��ԡ�����ʹ�������ԡ��
��������Ѻ��ԡ�÷�Һ�����׺˹��㹡�ô��Թ����ͧ  ���͢�鹵͹�ҹ��ҧ � �������ԡ������
����ҹ�ҹ����˹��§ҹ��СѺ˹��§ҹ�������Ǣ�ͧ����������Ѻ��ԡ�����Ѻ��ԡ�÷�������ͧ����Ǵ����', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], 3, '���㨪��������', '�Ѻ�繸���  ��ѭ���������Ƿҧ��䢻ѭ�ҷ���Դ��������Ѻ��ԡ�����ҧ�Ǵ���� ���� ���������§  �������  ���ͻѴ����
��´���������Ѻ��ԡ�����Ѻ�����֧���  ��йӢ�͢Ѵ��ͧ� � ����Դ��� (�����) 仾Ѳ�ҡ������ԡ��������觢��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], 4, '����������ʴ�����', '�ط��������������Ѻ��ԡ��  ��੾������ͼ���Ѻ��ԡ�û��ʺ�����ҡ�Ӻҡ  ��  ���������Ф����������繡óվ����㹡������ԡ�����ͪ�����ѭ����������Ѻ��ԡ��
�����������  �������  �����������Ǣ�ͧ�Ѻ�ҹ�����ѧ����ԡ������  ����繻���ª�������Ѻ��ԡ�������Ҽ���Ѻ��ԡ�è���������֧��������Һ�ҡ�͹
����ԡ�÷���Թ�����Ҵ��ѧ��дѺ�����', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], 5, '���㨤�����ͧ��÷�����ԧ�ͧ����Ѻ��ԡ��', '���㨤����������ͤ�����ͧ��÷�����ԧ�ͧ����Ѻ��ԡ��  ���/������������ǧ�Ң�������зӤ�����������ǡѺ�����������ͤ�����ͧ��÷�����ԧ�ͧ����Ѻ��ԡ��
�����йӷ���繻���ª�������Ѻ��ԡ�����͵ͺʹͧ�����������ͤ�����ͧ��÷�����ԧ�ͧ����Ѻ��ԡ��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], 6, '�����ҧἹ�繷���֡�ҷ�����Ѻ��ԡ���ҧ�', '�����繼Ż���ª������Դ��鹡Ѻ����Ѻ��ԡ����������  �������ö����¹�ŧ�Ըա�����͢�鹵͹�������ԡ��  ����������Ѻ��ԡ�������ª���٧�ش
��ԺѵԵ��繷���֡�ҷ�����Ѻ��ԡ������ҧ�  ��ʹ������ǹ����㹡�õѴ�Թ㨢ͧ����Ѻ��ԡ��
����ö�����������ǹ��Ƿ���Ҩᵡ��ҧ仨ҡ�Ըա�����͢�鹵͹������Ѻ��ԡ�õ�ͧ��� ��������ʹ���ͧ�Ѻ��������  �ѭ��  �͡�� ��� �����繻���ª�����ҧ���ԧ�����������������Ѻ��ԡ��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('103', $arr_dept[$j], 1, '�ʴ�������������С�е������鹷��л�Ժѵ��Ҫ�������', '��������Ժѵ��Ҫ��õ��˹�ҷ��������ж١��ͧ
�դ����ҹ�ʹ�� ��ѹ����������еç�������
�դ����Ѻ�Դ�ͺ㹧ҹ �������ö�觧ҹ������˹��������ҧ�١��ͧ
�ʴ��͡��ҵ�ͧ��û�Ժѵԧҹ�����բ�� �����ʴ����������ԧ��Ѻ��ا�Ѳ������ͻ��ʺ������˵ط��������Դ����٭�����������͹����Է���Ҿ㹧ҹ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('103', $arr_dept[$j], 2, '����ö��˹��ҵðҹ�����������㹡�û�Ժѵԧҹ�ͧ���ͧ�������������ķ������������', '���蹵Դ�������Ѵ�š�û�Ժѵԧҹ�ͧ������ࡳ���赹��˹�����ͧ �������١�����蹺ѧ�Ѻ
��˹�����������͢�鹵͹㹡�÷ӧҹ�ͧ���������ö�����������·����ѧ�Ѻ�ѭ�ҡ�˹�����������¢ͧ˹��§ҹ/���/�ͧ����Ѻ�Դ�ͺ
�դ��������´�ͺ�ͺ��������Ǩ��Ҥ����١��ͧ�ͧ�ҹ���͢����ŷ���Ѻ�Դ�ͺ�������������ŷ���դس�Ҿ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('103', $arr_dept[$j], 3, '��Ѻ��ا��оѲ�ҡ�û�Ժѵԧҹ����ջ���Է���Ҿ���', '����¹�ŧ��оѲ�ҵ��ͧ����Ҩ����֧��÷ӧҹ��բ�� ���Ǣ�� �ջ���Է���Ҿ�ҡ��� �����ա�������س�Ҿ�ͧ�ҹ����
�ʹ����ͷ��ͧ�Ըա�����͢�鹵͹�ӧҹẺ��������͹���§�ç���������ջ���Է���Ҿ�ҡ���������������������·����ѧ�Ѻ�ѭ�ҡ�˹�����������¢ͧ˹��§ҹ/���/�ͧ����Ѻ�Դ�ͺ
�Ѳ�����ͻ�Ѻ����¹�к������Ըա�÷ӧҹ�ͧ˹��§ҹ�������������ķ����������ջ���Է���Ҿ�٧���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('103', $arr_dept[$j], 4, '��˹�Ἱ��л�Ժѵ������������������·���ҷ��', '��˹�������·���ҷ�����������ҡ����¡�дѺ�����ķ����������բ�鹡��Ҽŧҹ������ҧ�����Ѵ
ŧ��͡�зӡ�þѲ���к� ��鹵͹ �Ըա�û�Ժѵ��Ҫ���������������ҵðҹ���ͼ����ķ�����ⴴ�����ᵡ��ҧ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('103', $arr_dept[$j], 5, '�Ѵ�Թ����ա�äӹǳ����������ªѴਹ �������ͧ��ú�����������', '�Ѵ�Թ� �¡����дѺ�����Ӥѭ�ͧ�ҹ��ҧ � �˹�ҷ�� �¤Դ�ӹǳ��������·����Դ������ҧ�Ѵਹ (�� ����Ƕ֧��þԨ�ó����º��º����ª�������Ҫ������ͻ�ЪҪ������Ѻ���������������º��º�Ѻ�鹷ع������¨��·���Ѱ��ͧ�����)
�����èѴ�����з����������з�Ѿ�ҡ�������������ª���٧�ش�����áԨ�ͧ˹��§ҹ���Ҵ��ó����', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('104', $arr_dept[$j], 1, '��Ժѵ�˹�ҷ�����ǹ�ͧ��������������ǧ', '�ӧҹ���ǹ��赹���Ѻ�ͺ����������� ʹѺʹع��õѴ�Թ�㹡����
��§ҹ�����Ҫԡ��Һ�����׺˹�Ңͧ��ô��Թ�ҹ㹡���� ���͢��������� ����繻���ª���͡�÷ӧҹ���ҧ������ͧ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('104', $arr_dept[$j], 2, '�١�Ե�����������', '���ҧ����ѹ����ҡѺ������㹡�������
����ö��Ѻ�����ҡѺʶҹ��ó���С�����ҹ�����ҡ����㹢�л�Ժѵԧҹ�����ҧ�ջ���Է���Ҿ
���������������������������͡Ѻ������㹷����С�����ҹ���´�
����Ƕ֧���͹�����ҹ��ԧ���ҧ��ä�', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('104', $arr_dept[$j], 3, '�Ѻ�ѧ���������л���ҹ����ѹ��', '�Ѻ�ѧ������繢ͧ��Ҫԡ㹷�� �������¹���ҡ����������֧�����ѧ�Ѻ�ѭ����м�������ҹ
�����Ť����Դ��繵�ҧ� �����Сͺ��õѴ�Թ������ҧἹ�ҹ�����ѹ㹷��
����ҹ��������������ѹ��Ҿ�ѹ��㹷������ʹѺʹع��÷ӧҹ�����ѹ����ջ���Է���Ҿ��觢��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('104', $arr_dept[$j], 4, '�����ѧ㨫�觡ѹ��Сѹ', '����Ǫ�蹪������ѧ����͹�����ҹ�����ҧ��ԧ�
�ʴ�������˵��ԡĵ� ��������������������͹�����ҹ������˵ب���������ͧ�����ͧ��
�ѡ���Ե��Ҿ�ѹ�աѺ���͹�����ҹ���ͪ�������͡ѹ����е�ҧ� ���ҹ���������ǧ�繻���ª������ǹ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('104', $arr_dept[$j], 5, '�����ѧ���ҧ�������Ѥ��㹷��', '��������������Ѥ���繹��˹������ǡѹ㹷�� �����ӹ֧�֧�����ͺ�������ͺ��ǹ��
���»���ҹ������� ���ͤ��������䢢�͢Ѵ��駷���Դ���㹷��
����ҹ����ѹ�����������ѭ���ѧ㨢ͧ������������ѧ�ѹ㹡�û�Ժѵ���áԨ�˭���µ�ҧ� ������ؼ�', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('105', $arr_dept[$j], 1, '�դ���ʹ��������Ң��Ҫվ�ͧ��', '��е�������㹡���֡���Ҥ������ ʹ�෤��������ͧ������������� ��Ң��Ҫվ�ͧ��
���蹷��ͧ�Ըա�÷ӧҹẺ�������;Ѳ�һ���Է���Ҿ��Ф�������������ö�ͧ��������觢��
�Դ���෤�����ͧ������������� �������� ���¡���׺�鹢����Ũҡ���觵�ҧ� �����繻���ª���͡�û�Ժѵ��Ҫ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('105', $arr_dept[$j], 2, '�ͺ�����ҷѹ�˵ء�ó����෤���������� ��������', '�ͺ�����ҷѹ෤���������ͧ������������� ��Ң��Ҫվ�ͧ����з������Ǣ�ͧ �����Ҩ�ռš�з���͡�û�Ժѵ�˹�ҷ��ͧ��
�Դ���������Է�ҡ�÷��ѹ���� ���෤����շ������Ǣ�ͧ�Ѻ�ҹ���ҧ������ͧ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('105', $arr_dept[$j], 3, '�Ӥ������ �Է�ҡ�� ����෤���������� ������֡���һ�Ѻ��Ѻ��÷ӧҹ', '���㨻������ѡ� ����Ӥѭ ��мš�з��ͧ�Է�ҡ�õ�ҧ����ҧ�֡���
����ö���Ԫҡ�� ������� ����෤���������� �һ���ء����㹡�û�Ժѵԧҹ��
����������������� �������� ��������繻���ª������Ӥѭ�ͧͧ�������� ෤���������� �����觼š�з���ͧҹ�ͧ���͹Ҥ�', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('105', $arr_dept[$j], 4, '�ѡ����л���ء���������������Ǫҭ����ԧ�֡����ԧ���ҧ���ҧ������ͧ�����������', '�դ�������������ǪҭẺ���Է�ҡ�� �������ö�Ӥ������任�Ѻ����黯Ժѵ����ԧ
����ö�Ӥ�������ԧ��óҡ�âͧ�����㹡�����ҧ����·�ȹ����͡�û�Ժѵԧҹ�͹Ҥ�
�ǹ�����Ҥ������������Ǣ�ͧ�Ѻ�ҹ����ԧ�֡����ԧ���ҧ���ҧ������ͧ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('105', $arr_dept[$j], 5, '���ҧ�Ѳ�������觡�����¹������͡�þѲ���ͧ���', 'ʹѺʹع����Դ����ҡ����觡�þѲ�Ҥ�������Ǫҭ�ͧ���  ���¡�èѴ��÷�Ѿ�ҡ�  ����ͧ����ػ�ó�������͵�͡�þѲ��
�����ʹѺʹع  ����  ������ռ���ʴ��͡�֧�������㨷��оѲ�Ҥ�������Ǫҭ㹧ҹ
������·�ȹ�㹡�������繻���ª��ͧ෤�����  ͧ��������  �����Է�ҡ������ � ��͡�û�Ժѵԧҹ�͹Ҥ�  ���ʹѺʹع�����������ա�ù� �һ���ء�����˹��§ҹ���ҧ������ͧ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} else {
				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('101', $arr_dept[$j], 1, '�ʴ�����������㹡�û�Ժѵ�˹�ҷ���Ҫ�������', '�������ӧҹ�˹�ҷ�����١��ͧ
		��������Ժѵԧҹ����������稵����˹�����
		�ҹ�ʹ�� ��ѹ��������㹡�÷ӧҹ
		�ʴ��͡��ҵ�ͧ��÷ӧҹ�����բ��
		�ʴ����������ԧ��Ѻ��ا�Ѳ���������繤����٭���� �������͹����Է���Ҿ㹧ҹ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('101', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 �������ö�ӧҹ��ŧҹ���������·���ҧ���', '��˹��ҵðҹ �����������㹡�÷ӧҹ���������ŧҹ����
		�Դ��� ��л����Թ�ŧҹ�ͧ�� ����º��§�Ѻࡳ���ҵðҹ 
		�ӧҹ����������·����ѧ�Ѻ�ѭ�ҡ�˹� ����������¢ͧ˹��§ҹ����Ѻ�Դ�ͺ 
		�դ��������´�ͺ�ͺ ������� ��Ǩ��Ҥ����١��ͧ ���������ҹ����դس�Ҿ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('101', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 �������ö��Ѻ��ا�Ըա�÷ӧҹ���������ŧҹ����ջ���Է���Ҿ�ҡ��觢��', '��Ѻ��ا�Ըա�÷������ӧҹ��բ�� ���Ǣ�� �դس�Ҿ�բ�� �ջ���Է���Ҿ�ҡ��� ���ͷ�������Ѻ��ԡ�þ֧����ҡ���
		�ʹ����ͷ��ͧ�Ըա�÷ӧҹẺ������Ҵ��Ҩз����ҹ�ջ���Է���Ҿ�ҡ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('101', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 �������ö��˹�������� �����駾Ѳ�ҧҹ ���������ŧҹ���ⴴ�� ����ᵡ��ҧ���ҧ�չ���Ӥѭ', '��˹�������·���ҷ�� ���������ҡ ���������ŧҹ���ա���������ҧ�����Ѵ
		�Ѳ���к� ��鹵͹ �Ըա�÷ӧҹ ���������ŧҹ���ⴴ�� ����ᵡ��ҧ������ռ��㴷����ҡ�͹', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('101', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 ��С��ҵѴ�Թ� �����ҡ�õѴ�Թ㨹�鹨��դ�������§ �����������������¢ͧ˹��§ҹ ������ǹ�Ҫ���', '�Ѵ�Թ��� ���ա�äӹǳ������������ҧ�Ѵਹ ��д��Թ��� ��������Ҥ�Ѱ��л�ЪҪ������ª���٧�ش
		�����èѴ�����з�������� ��ʹ����Ѿ�ҡ� ������������ª���٧�ش�����áԨ�ͧ˹��§ҹ�������ҧἹ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], 1, '����ö����ԡ�÷�����Ѻ��ԡ�õ�ͧ�������¤�������', '����ú�ԡ�÷�����Ե� ���Ҿ 
		�������� ������� ���١��ͧ �Ѵਹ�����Ѻ��ԡ��
		��������Ѻ��ԡ�÷�Һ�����׺˹��㹡�ô��Թ����ͧ ���͢�鹵͹�ҹ��ҧ� �������ԡ������ 
		����ҹ�ҹ����˹��§ҹ ���˹��§ҹ��蹷������Ǣ�ͧ ����������Ѻ��ԡ�����Ѻ��ԡ�÷�������ͧ����Ǵ����', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 ��Ъ�����ѭ����������Ѻ��ԡ��', '�Ѻ�繸��� ������ѭ���������Ƿҧ��䢻ѭ�ҷ���Դ��������Ѻ��ԡ�����ҧ�Ǵ����  ���������§ ������� ���ͻѴ����
		����������Ѻ��ԡ�����Ѻ�����֧��� ��йӢ�͢Ѵ��ͧ�� 㹡������ԡ�� 仾Ѳ�ҡ������ԡ��������觢��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 �������ԡ�÷���Թ�����Ҵ��ѧ ����ͧ���������ͤ������������ҧ�ҡ', '������������Ѻ��ԡ���繾���� ���ͪ�����ѭ����������Ѻ��ԡ��
		�������� ������� �������Ǣ�ͧ�Ѻ�ҹ�����ѧ����ԡ������ ����繻���ª�������Ѻ��ԡ�� �����Ҽ���Ѻ��ԡ�è���������֧ ��������Һ�ҡ�͹
		���ʹ��Ըա��㹡������ԡ�÷�����Ѻ��ԡ�è����Ѻ����ª���٧�ش', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 ��������������ԡ�÷��ç���������ͧ��÷�����ԧ�ͧ����Ѻ��ԡ����', '���� ���;������Ӥ������㨴����Ըա�õ�ҧ� ��������ԡ����ç���������ͧ��÷�����ԧ�ͧ����Ѻ��ԡ��
		�����йӷ���繻���ª�������Ѻ��ԡ�� ���͵ͺʹͧ�����������ͤ�����ͧ��÷�����ԧ�ͧ����Ѻ��ԡ��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('102', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 �������ԡ�÷���繻���ª�����ҧ���ԧ��������Ѻ��ԡ��', '�Դ�֧�Ż���ª��ͧ����Ѻ��ԡ���������� ��о������������¹�Ը����͢�鹵͹�������ԡ�� ���ͻ���ª���٧�ش�ͧ����Ѻ��ԡ��
		�繷���֡�ҷ������ǹ����㹡�õѴ�Թ㨷�����Ѻ��ԡ������ҧ�
		����ö��������繷��ᵡ��ҧ�ҡ�Ըա�� ���͢�鹵͹������Ѻ��ԡ�õ�ͧ�������ʹ���ͧ�Ѻ�������� �ѭ�� �͡�� �����繻���ª�����ҧ���ԧ�ͧ����Ѻ��ԡ��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('103', $arr_dept[$j], 1, '�ʴ�����ʹ���еԴ��������������� ��Ң��Ҫվ�ͧ�� ���ͷ������Ǣ�ͧ', '�֡���Ҥ������ ʹ�෤��������ͧ������������� ��Ң��Ҫվ�ͧ�� 
		�Ѳ�Ҥ�������������ö�ͧ��������觢�� 
		�Դ���෤����� ��Ф����������� �������ʹ��¡���׺�鹢����Ũҡ���觵�ҧ� �����繻���ª���͡�û�Ժѵ��Ҫ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('103', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 ����դ��������Ԫҡ�� ���෤���������� ��Ң��Ҫվ�ͧ��', '�ͺ����෤���������ͧ������������� ��Ң��Ҫվ�ͧ�� ���ͷ������Ǣ�ͧ ����Ҩ�ռš�з���͡�û�Ժѵ�˹�ҷ���Ҫ��âͧ�� 
		�Ѻ���֧������Է�ҡ�÷��ѹ���� �������Ǣ�ͧ�Ѻ�ҹ�ͧ�� ���ҧ������ͧ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('103', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 �������ö�Ӥ������ �Է�ҡ�� ����෤���������� �һ�Ѻ��Ѻ��û�Ժѵ�˹�ҷ���Ҫ���', '����ö���Ԫҡ�� ������� ����෤���������� �һ���ء����㹡�û�Ժѵ�˹�ҷ���Ҫ����� 
		����ö��䢻ѭ�ҷ���Ҩ�Դ�ҡ��ù�෤�������������㹡�û�Ժѵ�˹�ҷ���Ҫ�����', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('103', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 ����֡�� �Ѳ�ҵ��ͧ����դ������ ��Ф�������Ǫҭ㹧ҹ�ҡ��� �����ԧ�֡ ����ԧ���ҧ���ҧ������ͧ', '�դ�������������Ǫҭ�����ͧ������ѡɳ������Է�ҡ�� �������ö�Ӥ������任�Ѻ�������ҧ���ҧ��ҧ 
		����ö�Ӥ�������ԧ��óҡ�âͧ�����㹡�����ҧ����·�ȹ� ���͡�û�Ժѵԧҹ�͹Ҥ�', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('103', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 ���ʹѺʹع��÷ӧҹ�ͧ�����ǹ�Ҫ��÷���鹤�������Ǫҭ��Է�ҡ�ô�ҹ��ҧ�', 'ʹѺʹع����Դ����ҡ����觡�þѲ�Ҥ�������Ǫҭ�ͧ��� ���¡�èѴ��÷�Ѿ�ҡ� ����ͧ��� �ػ�ó�������͵�͡�þѲ��
		�����èѴ��������ǹ�Ҫ��ù�෤����� ������� �����Է�ҡ������� ����㹡�û�Ժѵ�˹�ҷ���Ҫ���㹧ҹ���ҧ������ͧ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('104', $arr_dept[$j], 1, '�դ����ب�Ե', '��Ժѵ�˹�ҷ����¤����ب�Ե ������͡��Ժѵ� �١��ͧ��������� ����Թ�¢���Ҫ���
		�ʴ������Դ��繵����ѡ�ԪҪվ���ҧ�ب�Ե', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('104', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 ������Ѩ�����Ͷ����', '�ѡ�ҤӾٴ ���Ѩ�� ������Ͷ���� 
		�ʴ�����ҡ��֧�����ըԵ�ӹ֡㹤����繢���Ҫ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('104', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 ����ִ������ѡ���', '�ִ������ѡ��� ����Һ�ó����ԪҪվ ��Ш���Ң���Ҫ���������§ູ����ͤ�����ͼŻ���ª�� �����Ѻ�Դ ����Ѻ�Դ�ͺ
		������Ф����آ��ǹ�� ��������Դ����ª����ҧ�Ҫ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('104', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 ����׹��Ѵ���ͤ����١��ͧ', '�׹��Ѵ���ͤ����١��ͧ����觾Էѡ��Ż���ª��ͧ�ҧ�Ҫ��� ��鵡�����ʶҹ��ó����Ҩ�ҡ�Ӻҡ
		���ҵѴ�Թ� ��Ժѵ�˹�ҷ���Ҫ��ô��¤����١��ͧ �繸��� ����Ҩ��ͤ������֧�������������»���ª��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('104', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 ����ط�ȵ����ͤ����صԸ���', '�׹��Ѵ�Էѡ��Ż���ª����Ъ������§�ͧ����Ȫҵ�����ʶҹ��ó����Ҩ����§��ͤ�����蹤�㹵��˹�˹�ҷ���çҹ �����Ҩ����§��µ�ͪ��Ե', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('105', $arr_dept[$j], 1, '��˹�ҷ��ͧ��㹷����������', 'ʹѺʹع��õѴ�Թ㨢ͧ��� ��зӧҹ���ǹ��赹���Ѻ�ͺ���� 
		��§ҹ�����Ҫԡ��Һ�����׺˹�Ңͧ��ô��Թ�ҹ�ͧ��㹷��
		�������� ����繻���ª���͡�÷ӧҹ�ͧ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('105', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 ����������������㹡�÷ӧҹ�Ѻ���͹�����ҹ', '���ҧ����ѹ��  ��ҡѺ������㹡�������
		������������͡Ѻ������㹷�����´�
		����Ƕ֧���͹�����ҹ��ԧ���ҧ��ä�����ʴ��������������ѡ��Ҿ�ͧ���͹������� ��駵��˹������Ѻ��ѧ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('105', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 ��л���ҹ����������ͧ͢��Ҫԡ㹷��', '�Ѻ�ѧ������繢ͧ��Ҫԡ㹷�� ����������¹���ҡ������ 
		�Ѵ�Թ������ҧἹ�ҹ�����ѹ㹷���ҡ�����Դ��繢ͧ���͹�������
		����ҹ��������������ѹ��Ҿ�ѹ��㹷�� ����ʹѺʹع��÷ӧҹ�����ѹ����ջ���Է���Ҿ��觢��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('105', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 ���ʹѺʹع ������������͹������� �������ҹ���ʺ���������', '¡��ͧ ��������ѧ����͹����������ҧ��ԧ� 
		������������������͡�������͹������� �������ա����ͧ��
		�ѡ���Ե��Ҿ�ѹ�աѺ���͹������� ���ͪ�������͡ѹ����е�ҧ����ҹ�����', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('105', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 �������ö�ӷ����黯Ժѵ���áԨ�����������', '��������ҧ�������Ѥ��㹷�� �����ӹ֧�����ͺ�������ͺ��ǹ�� 
		������� ������䢢�͢Ѵ��駷���Դ���㹷��
		����ҹ����ѹ�� ���ҧ��ѭ���ѧ㨢ͧ������ͻ�Ժѵ���áԨ�ͧ��ǹ�Ҫ���������ؼ�', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('201', $arr_dept[$j], 1, '���Թ��û�Ъ�������Ф���駢�����ä�������µ�ʹ', '���Թ��û�Ъ�������仵������º ���� �ѵ�ػ��ʧ�� ������ҵ�ʹ���ͺ���§ҹ�����ؤ��㹡������
		�駢�����������������Ѻ�š�з��ҡ��õѴ�Թ��Ѻ��Һ�������� ��������١��˹�����ͧ��з�
		͸Ժ���˵ؼ�㹡�õѴ�Թ������������Ǣ�ͧ��Һ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('201', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 ����繼���㹡�÷ӧҹ�ͧ�����������ӹҨ���ҧ�صԸ���', '���������С�зӡ���������������Ժѵ�˹�ҷ�������ҧ�������Է���Ҿ
		��˹�������� ��ȷҧ���Ѵਹ �Ѵ������ҹ������͡���������СѺ�ҹ ���͡�˹��Ըա�÷��з���������ӧҹ��բ�� 
		�Ѻ�ѧ�����Դ��繢ͧ������
		���ҧ��ѭ���ѧ�㹡�û�Ժѵԧҹ 
		��ԺѵԵ����Ҫԡ㹷�����¤����صԸ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('201', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 �������ô�����Ъ�������ͷ���ҹ', '�繷���֡����Ъ�������ͷ���ҹ 
		����ͧ����ҹ ��Ъ������§�ͧ��ǹ�Ҫ��� 
		�Ѵ�Һؤ�ҡ� ��Ѿ�ҡ� ���͢����ŷ���Ӥѭ��������ҹ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('201', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 ��л�оĵԵ����Ѻ�繼���', '��˹�����������ԺѵԻ�Шӡ������л�оĵԵ�����㹡�ͺ�ͧ����������ԺѵԹ��
		��оĵԻ�ԺѵԵ���Ẻ���ҧ����
		�ִ��ѡ������Ժ��㹡�û���ͧ�����ѧ�Ѻ�ѭ��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('201', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 ��йӷ���ҹ����������ѹ��Ԩ������Ǣͧͧ���', '����ö���㨤�������ҧ�ç�ѹ����������ҹ�Դ��������㹡�û�Ժѵ���áԨ������������ǧ
		�����繡������¹�ŧ�͹Ҥ� ���������·�ȹ�㹡�����ҧ���ط�������Ѻ��͡Ѻ�������¹�ŧ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('202', $arr_dept[$j], 1, '��������������·�ȹ�ͧͧ���', '��� �����������ö͸Ժ������������������ҧҹ��������������Ǣ�ͧ���͵ͺʹͧ�������·�ȹ�ͧ��ǹ�Ҫ������ҧ��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('202', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 ��Ъ��·�����������������������·�ȹ�ͧͧ���', '͸Ժ������������������������·�ȹ����������¡�÷ӧҹ�ͧ˹��§ҹ������Ҿ����ͧ��ǹ�Ҫ����� 
		�š����¹����������֧�Ѻ�ѧ�����Դ��繢ͧ���������ͻ�Сͺ��á�˹�����·�ȹ�', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('202', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 ��� ���ҧ�ç�٧������������㨷��л�ԺѵԵ������·�ȹ�', '������������Ҫԡ㹷���Դ����������С�е������鹷��л�Ժѵ�˹�ҷ���Ҫ������͵ͺʹͧ�������·�ȹ� 
		���ӻ�֡���й�����Ҫԡ㹷���֧�Ƿҧ㹡�÷ӧҹ���ִ�������·�ȹ����������¢ͧͧ������Ӥѭ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('202', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 ��С�˹���º������ʹ���ͧ�Ѻ����·�ȹ�ͧ��ǹ�Ҫ���', '���������С�˹���º������� ���͵ͺʹͧ��͡�ù�����·�ȹ�������������', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('202', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 ��С�˹�����·�ȹ�ͧ��ǹ�Ҫ�������ʹ���ͧ�Ѻ����·�ȹ��дѺ�����', '��˹�����·�ȹ� ������� ��з�ȷҧ㹡�û�Ժѵ�˹�ҷ��ͧ��ǹ�Ҫ������������������·�ȹ����ʹ���ͧ�Ѻ����·�ȹ��дѺ����� 
		�Ҵ��ó�����һ���Ȩ����Ѻ�š�з����ҧ�èҡ�������¹�ŧ������������¹͡', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('203', $arr_dept[$j], 1, '���������㨹�º����������áԨ�Ҥ�Ѱ ����դ���������§�Ѻ˹�ҷ������Ѻ�Դ�ͺ�ͧ˹��§ҹ���ҧ��', '���㨹�º�� ��áԨ �����駡��ط��ͧ�Ҥ�Ѱ�����ǹ�Ҫ��� �������ѹ�� ������§�Ѻ��áԨ�ͧ˹��§ҹ��赹�����Ѻ�Դ�ͺ���ҧ��
		����ö��������ѭ�� �ػ��ä�����͡�ʢͧ˹��§ҹ��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('203', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 ��йӻ��ʺ��ó��һ���ء����㹡�á�˹����ط����', '����ء������ʺ��ó�㹡�á�˹����ط��ͧ˹��§ҹ��赹�����Ѻ�Դ�ͺ����ʹ���ͧ�Ѻ���ط���Ҥ�Ѱ�� 
		�����������������к��Ҫ����һ�Ѻ���ط�������������Ѻʶҹ��ó�������¹�ŧ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('203', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 ��йӷ�ɮ������ǤԴ�Ѻ��͹����㹡�á�˹����ط��', '����ء�����ɮ� �����ǤԴ�Ѻ��͹ 㹡�äԴ��оѲ������������͡��ط��ͧ˹��§ҹ��赹�����Ѻ�Դ�ͺ
		����ء���Ƿҧ��ԺѵԷ����ʺ��������� (Best Practice) ���ͼš���Ԩ�µ�ҧ� �ҡ�˹�Ἱ�ҹ�ԧ���ط���˹��§ҹ��赹�����Ѻ�Դ�ͺ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('203', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 ��� ��˹����ط�����ʹ���ͧ�Ѻʶҹ��ó��ҧ� ����Դ���', '�����Թ����ѧ������ʶҹ��ó� ����� ���ͻѭ�ҷҧ���ɰ�Ԩ �ѧ�� ������ͧ���㹻���� ���ͧ͢�š���ͧ�Ҿ��ѡɳ�ͧ����� ������㹡�á�˹� ���ط���Ҥ�Ѱ������ǹ�Ҫ���
		�Ҵ��ó�ʶҹ��ó��͹Ҥ� ��С�˹����ط������ʹ���ͧ�Ѻʶҹ��ó��ҧ� �����Դ�������������ؾѹ��Ԩ�ͧ��ǹ�Ҫ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('203', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 ��к�óҡ��ͧ����������������㹡�á�˹����ط���Ҥ�Ѱ', '������� ���ҧ��ä� ��к�óҡ��ͧ������������㹡�á�˹����ط���Ҥ�Ѱ �¾Ԩ�óҨҡ��Ժ���Ҿ���
		��Ѻ����¹��ȷҧ�ͧ���ط��㹡�þѲ�һ�������ҧ������ͧ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('204', $arr_dept[$j], 1, '��繤������繢ͧ��û�Ѻ����¹', '��繤������繢ͧ��û�Ѻ����¹ ��л�Ѻ�ĵԡ�������Ἱ��÷ӧҹ����ʹ���ͧ�Ѻ�������¹�ŧ���
		�����������Ѻ�֧�������繢ͧ��û�Ѻ����¹ ������¹��������������ö��Ѻ����Ѻ��� ����¹�ŧ�����', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('204', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 �������ö�������������㨡�û�Ѻ����¹�����Դ���', '��������������������㨶֧����������л���ª��ͧ�������¹�ŧ��� 
		ʹѺʹع����������㹡�û�Ѻ����¹ͧ��� ���������ʹ����Ըա���������ǹ����㹡�û�Ѻ����¹�ѧ�����', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('204', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 ��С�е�� ������ҧ�ç�٧�����������繤����Ӥѭ�ͧ��û�Ѻ����¹', '��е�� ������ҧ�ç�٧�����������繤����Ӥѭ�ͧ��û�Ѻ����¹ ��������Դ���������ç�����
		���º��º�����������觷�軯Ժѵ�����㹻Ѩ�غѹ�Ѻ��觷�������¹�ŧ仹��ᵡ��ҧ�ѹ������Ӥѭ���ҧ��
		���ҧ������������Դ����������ѧ�������Ѻ�������¹�ŧ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('204', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 ����ҧἹ�ҹ���������Ѻ��û�Ѻ����¹�ͧ���', '�ҧἹ���ҧ���к���Ъ�������繻���ª��ͧ��û�Ѻ����¹
		�����Ἱ ��еԴ�����ú����á������¹�ŧ���ҧ��������', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('204', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 ��м�ѡ�ѹ����Դ��û�Ѻ����¹���ҧ�ջ���Է���Ҿ', '��ѡ�ѹ����û�Ѻ����¹����ö���Թ������ҧ�Һ�����л��ʺ���������
		���ҧ��ѭ���ѧ� ��Ф����������㹡�âѺ����͹����Դ��û�Ѻ����¹���ҧ�ջ���Է���Ҿ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('205', $arr_dept[$j], 1, '����ʴ��ĵԡ����������������', '����ʴ��ĵԡ������������Ҿ��������������㹷ءʶҹ��ó�', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('205', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 ��ФǺ��������������ʶҹ��ó��������ҧ��', '�����ҷѹ������ͧ���ͧ��ФǺ��������ҧ������� ���Ҩ��ա����§�ҡʶҹ��ó�������§��͡���Դ�����ع�ç��� �����Ҩ����¹��Ǣ��ʹ��� ������ش�ѡ���Ǥ�������ʧ�ʵ�������', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('205', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 �������ö����·��Ҩ� ���ͻ�Ժѵԧҹ���������ҧʧ� ������������з��١������', '����֡��֧�����ع�ç�ҧ������������ҧ���ʹ��� ���͡�û�Ժѵԧҹ �� �����ø �����Դ��ѧ ���ͤ������ѹ ������ʴ��͡���ж١������ ���ѧ������ö��Ժѵԧҹ���������ҧʧ�
		����ö���͡���Ըա���ʴ��͡���������������������Դ����ԧź��駵�͵��ͧ��м�����', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('205', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 ��ШѴ��ä������´�����ҧ�ջ���Է���Ҿ', '����ö�Ѵ��áѺ�������´���ͼŷ���Ҩ�Դ��鹨ҡ���С��ѹ�ҧ�����������ҧ�ջ���Է���Ҿ
		����ء�����Ըա��੾�е� �����ҧἹ��ǧ˹�����ͨѴ��áѺ�������´��Ф������ѹ�ҧ��������Ҵ��������Ҩ��Դ���
		�����èѴ���������ͧ�������ҧ�ջ���Է���Ҿ����Ŵ�������´�ͧ���ͧ���ͼ�������ҹ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('205', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 �����Ҫ����������¤�������', '�ЧѺ�������ع�ç ���¡�þ������Ӥ������������䢷����˵آͧ�ѭ�� �����駺�Ժ���лѨ����Ǵ������ҧ� 
		�ʶҹ��ó���֧���´�ҡ���ѧ����ö�Ǻ���������ͧ���ͧ�� ����֧����餹���� ����������ʧ�ŧ��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('206', $arr_dept[$j], 1, '�͹�ҹ���������й�����ǡѺ�Ըջ�Ժѵԧҹ', '�͹�ҹ���¡�������й����ҧ�����´ ���ʹ��¡���ҸԵ�Ըջ�Ժѵԧҹ
		��������觢����� �������觷�Ѿ�ҡ����� ������㹡�þѲ�ҡ�û�Ժѵԧҹ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE,, DEPARTMENT_ID CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('206', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 ��е��㨾Ѳ�Ҽ����ѧ�Ѻ�ѭ��������ѡ��Ҿ', '����ö���ӻ�֡�Ҫ�����Ƿҧ㹡�þѲ���������������ʹ���л�Ѻ��ا��ʹ������Ŵŧ
		����͡�ʼ����ѧ�Ѻ�ѭ�����ʴ��ѡ��Ҿ�������ҧ��������㹡�û�Ժѵԧҹ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('206', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 ����ҧἹ��������͡�ʼ����ѧ�Ѻ�ѭ���ʴ���������ö㹡�÷ӧҹ', '�ҧἹ㹡�þѲ�Ҽ����ѧ�Ѻ�ѭ�ҷ�������������������� 
		�ͺ���§ҹ���������� ����������͡�ʼ����ѧ�Ѻ�ѭ�ҷ������Ѻ��ý֡ͺ�� ���;Ѳ�����ҧ������������ʹѺʹع������¹��� 
		�ͺ����˹�ҷ������Ѻ�Դ�ͺ��дѺ�Ѵ�Թ��������ѧ�Ѻ�ѭ���繺ҧ����ͧ����������͡����������������� ���ͺ����èѴ��ô��µ��ͧ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('206', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 �������ö������䢻ѭ�ҷ�����ػ��ä��͡�þѲ���ѡ��Ҿ�ͧ�����ѧ�Ѻ�ѭ��', '����ö��Ѻ����¹��ȹ����������繻Ѩ��¢Ѵ��ҧ��þѲ���ѡ��Ҿ�ͧ�����ѧ�Ѻ�ѭ��
		����ö���㨶֧���˵���觾ĵԡ����ͧ���кؤ�� ���͹����繻Ѩ���㹡�þѲ���ѡ��Ҿ�ͧ�����ѧ�Ѻ�ѭ����', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('206', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 ��з������ǹ�Ҫ������к�����͹�ҹ��С���ͺ����˹�ҷ������Ѻ�Դ�ͺ', '���ҧ ���ʹѺʹع����ա���͹�ҹ����ա���ͺ����˹�ҷ������Ѻ�Դ�ͺ���ҧ���к����ǹ�Ҫ��� 
		���ҧ ���ʹѺʹع������Ѳ�������觡�����¹������ҧ������ͧ���ǹ�Ҫ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('301', $arr_dept[$j], 1, '�¡��л���繻ѭ�� ���ͧҹ�͡����ǹ�����', '�¡��лѭ���͡����¡�����ҧ���������������§�ӴѺ�����Ӥѭ
		�ҧἹ�ҹ��ᵡ����繻ѭ���͡����ǹ� �����繡Ԩ������ҧ� ��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('301', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 ������㨤�������ѹ���鹾�鹰ҹ�ͧ�ѭ�� ���ͧҹ', '�к��˵���м� �����ʶҹ��ó��ҧ� ��
		�кآ�ʹբ�����¢ͧ����繵�ҧ���
		�ҧἹ�ҹ�¨Ѵ���§�ҹ ���͡Ԩ������ҧ����ӴѺ�����Ӥѭ���ͤ�����觴�ǹ��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('301', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 ������㨤�������ѹ����Ѻ��͹ �ͧ�ѭ�� ���ͧҹ', '������§�˵ػѨ��·��Ѻ��͹�ͧ����ʶҹ��ó� �����˵ء�ó�
		�ҧἹ�ҹ�¡�˹��Ԩ���� ��鹵͹��ô��Թ�ҹ��ҧ� ����ռ������Ǣ�ͧ���½��������ҧ�ջ���Է���Ҿ �������ö�Ҵ��ó�����ǡѺ�ѭ�� �����ػ��ä����Ҩ�Դ�����', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('301', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 �������ö�������� �����ҧἹ�ҹ���Ѻ��͹��', '���㨻���繻ѭ����дѺ�������ö�¡����˵ػѨ���������§�Ѻ��͹���������´ �������ö���������������ѹ��ͧ�ѭ�ҡѺʶҹ��ó�˹��� �� 
		�ҧἹ�ҹ���Ѻ��͹�¡�˹��Ԩ���� ��鹵͹��ô��Թ�ҹ��ҧ�����˹��§ҹ���ͼ������Ǣ�ͧ���½��� ����֧�Ҵ��ó�ѭ�� �ػ��ä ����ҧ�Ƿҧ��û�ͧ�ѹ��������ǧ˹��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('301', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 �����෤�Ԥ ����ٻẺ��ҧ� 㹡�á�˹�Ἱ�ҹ ���͢�鹵͹��÷ӧҹ  ����������ҧ���͡����Ѻ��û�ͧ�ѹ ������䢻ѭ�� ����Դ���', '��෤�Ԥ��������������������㹡���¡��л���繻ѭ�ҷ��Ѻ��͹����ǹ�
		��෤�Ԥ�������������ҡ�����ٻẺ�����ҷҧ���͡ 㹡����ѭ�� ����֧�Ԩ�óҢ�ʹբ�����¢ͧ�ҧ���͡���зҧ
		�ҧἹ�ҹ���Ѻ��͹�¡�˹��Ԩ���� ��鹵͹��ô��Թ�ҹ��ҧ� �����˹��§ҹ���ͼ������Ǣ�ͧ���½��� �Ҵ��ó�ѭ�� �ػ��ä �Ƿҧ��û�ͧ�ѹ��� �������ʹ��зҧ���͡��Т�ʹբ������������', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('302', $arr_dept[$j], 1, '�顮��鹰ҹ�����', '�顮��鹰ҹ ��ѡࡳ�� �������ѭ�ӹ֡㹡���кػ���繻ѭ�� ������ѭ��㹧ҹ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('302', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 ��л���ء������ʺ��ó�', '�кض֧����������§�ͧ������ ����� ��Ф������ú��ǹ�ͧ��������
		����ء������ʺ��ó�㹡���кػ���繻ѭ��������ѭ��㹧ҹ��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('302', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 ��л���ء���ɮ������ǤԴ�Ѻ��͹', '����ء�����ɮ� �ǤԴ���Ѻ��͹ ����������ʹյ㹡���к�������ѭ�ҵ��ʶҹ��ó� ���㹺ҧ�ó� �ǤԴ��������Ѻʶҹ��ó��Ҩ�������觺觺͡�֧��������Ǣ�ͧ������§�ѹ��¡���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('302', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 ���͸Ժ�¢����� ����ʶҹ��ó����դ�������ҡ�Ѻ��͹������������', '����ö͸Ժ�¤����Դ ����ʶҹ��ó���Ѻ��͹�������������ö������
		����ö�ѧ����������� ��ػ�ǤԴ ��ɮ� ͧ��������  ���Ѻ��͹����������§�������繻���ª���ͧҹ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('302', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 ��ФԴ������� ���ҧ��ä�ͧ������������', '������� ���ҧ��ä� ��д�ɰ�Դ�� ����֧����ö���ʹ��ٻẺ �Ըա������ͧ���������������Ҩ����»�ҡ��ҡ�͹', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('303', $arr_dept[$j], 1, '��������������Ӥѭ㹡�����������оѲ�Ҽ�����', 'ʹѺʹع�������蹾Ѳ���ѡ��Ҿ�����آ���з�駷ҧ�ѭ�� ��ҧ��� �Ե㨷��� 
		�ʴ��������������Ҽ��������ѡ��Ҿ���оѲ�ҵ��ͧ������觢����', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('303', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 ����͹���������й����;Ѳ�������������ѡ��Ҿ�������آ���з�駷ҧ�ѭ�� ��ҧ��� �Ե����ͷ�ȹ��Է���', '�ҸԵ ���������й�����ǡѺ��û�ԺѵԵ� ���;Ѳ���ѡ��Ҿ �آ�������ͷ�ȹ��Է������ҧ����׹
		�����蹷���ʹѺʹع �ª�������觢����� ���ͷ�Ѿ�ҡ÷����繵�͡�þѲ�Ңͧ������', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('303', $arr_dept[$j], 3, '�ʴ����ö�з�� 2 �������㹡������˵ؼŻ�Сͺ����й� ��������ǹʹѺʹع㹡�þѲ�Ҽ�����', '����Ƿҧ��������͸Ժ���˵ؼŻ�Сͺ �����������������������ö�Ѳ���ѡ��Ҿ�آ�������ͷ�ȹ��Է������ҧ����׹��
		�����������ա���š����¹������¹������ͻ��ʺ��ó�  ���������������͡������·ʹ ������¹����Ըա�þѲ���ѡ��Ҿ������������ҧ�آ�������ͷ�ȹ��Է������ҧ����׹
		ʹѺʹع�����ػ�ó� ����ͧ��� �����Ըա����Ҥ��Ժѵ�������������������ҵ�����ö�Ѳ���ѡ��Ҿ �آ�������ͷ�ȹ��Է������ҧ����׹����ջ���Է���Ҿ�٧�ش��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('303', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 ��еԴ���������ӵԪ��������������þѲ�����ҧ������ͧ', '�Դ����š�þѲ�Ңͧ���������������ӵԪ����������������Դ��þѲ�����ҧ������ͧ
		�����йӷ����������Ѻ�ѡɳ�੾�� ���;Ѳ���ѡ��Ҿ �آ�������ͷ�ȹ��Է��բͧ���кؤ��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('303', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 �������鹡�þѲ�Ҩҡ�ҡ�ͧ�ѭ�� ���ͤ�����ͧ��÷�����ԧ', '�������Ӥ������㨻ѭ�����ͤ�����ͧ��÷�����ԧ�ͧ������ �����������ö�Ѵ���Ƿҧ㹡�þѲ���ѡ��Ҿ �آ���� ���ͷ�ȹ��Է������ҧ����׹��
		�鹤��� ���ҧ��ä��Ըա������� 㹡�þѲ���ѡ��Ҿ �آ�������ͷ�ȹ��Է��� ��觵ç�Ѻ�ѭ�����ͤ�����ͧ��÷�����ԧ�ͧ������', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('304', $arr_dept[$j], 1, '�������зӡ��� � ����ҵðҹ �� ����º ��ͺѧ�Ѻ', '�������зӡ��� �  ����ҵðҹ  �� ����º ��ͺѧ�Ѻ 
		�ͺ���§ҹ���������´�ҧ��ǹ�������蹴��Թ���᷹�� ������鵹�ͧ��Ժѵԧҹ������˹�˹�ҷ�����ҡ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('304', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 ��С�˹��ͺࢵ��ͨӡѴ㹡�á�зӡ����', '����ʸ�Ӣͧ͢������ ���������˵��������������仵���ҵðҹ �� ����º ��ͺѧ�Ѻ  
		��˹��ѡɳ��ԧ�ĵԡ��������Ƿҧ��Ժѵ�˹�ҷ���Ҫ���������ҵðҹ 
		���ҧ���͹������������蹻�ԺѵԵ����������������º', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('304', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 ����������Ѻ�ҵðҹ ���ͻ�Ѻ��ا��û�Ժѵԧҹ���բ��', '��˹��ҵðҹ㹡�û�Ժѵԧҹ ���ᵡ��ҧ �����٧��� 
		�������Ѻ��ا��û�Ժѵԧҹ�����仵���ҵðҹ �� ����º ��ͺѧ�Ѻ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('304', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 ��еԴ����Ǻ�����黯ԺѵԵ���ҵðҹ �� ����º ��ͺѧ�Ѻ', '�Դ��� �Ǻ��� ��Ǩ�ͺ˹��§ҹ������áӡѺ������黯ԺѵԵ���ҵðҹ �� ����º ��ͺѧ�Ѻ
		��͹����Һ��ǧ˹�����ҧ�Ѵਹ�֧�ŷ����Դ��鹨ҡ�����軯ԺѵԵ���ҵðҹ �� ����º ��ͺѧ�Ѻ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('304', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 ��д��Թ���㹡óշ���ա�û�Ժѵ������仵���ҵðҹ ���͢Ѵ��͡����� �� ����º ��ͺѧ�Ѻ', '���Ը�༪ԭ˹�����ҧ�Դ�µç仵ç��㹡óշ���ջѭ�� �����ա�û�ԺѵԷ�������仵���ҵðҹ ���͢Ѵ��͡����� �� ����º ��ͺѧ�Ѻ 
		���Թ��������仵�����������ҧ��觤�Ѵ �óշ���ա�û�Ժѵ������仵���ҵðҹ ���͢Ѵ��͡����� �� ����º ��ͺѧ�Ѻ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('305', $arr_dept[$j], 1, '�Ң���������ͧ��', '������ŷ�������� �����Ҩҡ���觢����ŷ������������
		������������Ǣ�ͧ�µç��������������', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('305', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 ����׺���Ф��Ң�����', '�׺���Ф��Ң����Ŵ����Ըա�÷���ҡ������§��õ�駤Ӷ����鹰ҹ
		�׺���Ф��Ң����Ũҡ��������Դ�Ѻ�˵ء�ó���������ͧ����ҡ����ش', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('305', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 �����ǧ�Ң������ԧ�֡', '��駤Ӷ���ԧ�֡㹻���繷������Ǣ�ͧ���ҧ������ͧ�������Ңͧʶҹ��ó� �˵ء�ó� ����繻ѭ�� ���ͤ鹾��͡�ʷ����繻���ª���͡�û�Ժѵԧҹ����
		��ǧ�Ң����Ŵ��¡���ͺ����ҡ���������������� ����������˹�ҷ������Ǣ�ͧ�µç�����ͧ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('305', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 ����׺�鹢��������ҧ���к�', '�ҧἹ�红��������ҧ���к� 㹪�ǧ���ҷ���˹�
		�׺�鹢����Ũҡ���觢����ŷ��ᵡ��ҧ�ҡ�óջ��Ը������·���� 
		���Թ����Ԩ�� �����ͺ�������������红����Ũҡ˹ѧ��;���� �Ե���� �к��׺���������෤��������ʹ�� ��ʹ�����觢��������� ���ͻ�Сͺ��÷��Ԩ��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('305', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 ����ҧ�к�����׺�� �����Ң��������ҧ������ͧ', '�ҧ�к�����׺�� �����駡���ͺ�������������׺�鹢����� �������������ŷ��ѹ�˵ء�ó� ���ҧ������ͧ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('306', $arr_dept[$j], 1, '��繤س��Ңͧ�Ѳ����������������ʹ��Ѳ������ͧ������', '�Ҥ�������Ѳ������ͧ�� ��з����繤س������ʹ㨷������¹����Ѳ������ͧ������
		����Ѻ������ҧ�ҧ�Ѳ����� ������ٶ١�Ѳ����������Ҵ��¡���
		��Ѻ����¹�ĵԡ�������ʹ���ͧ�Ѻ��Ժ��ҧ�Ѳ������������¹�', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('306', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 ������� �����駻�Ѻ�������ʹ���ͧ�Ѻ�Ѳ���������', '��������ҷ ������ ��ʹ������������ԺѵԢͧ�Ѳ��������ᵡ��ҧ ��о�������Ѻ�������ʹ���ͧ
		������ô����Ըա�� ������ ��ж��¤ӷ����������Ѻ�Ѳ������ͧ������', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('306', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 ���������Ѳ�������ҧ� ���ҧ�֡��� �������ʴ��͡�����ҧ��������Ѻʶҹ��ó�', '���㨺�Ժ� ��й���Ӥѭ�ͧ�Ѳ�������ҧ�
		�����ҡ�ҹ�ҧ�Ѳ��������ᵡ��ҧ�ѹ�з���������ԸդԴ�ͧ������
		���Ѵ�Թ�����蹨ҡ����ᵡ��ҧ�ҧ�Ѳ����� ���ͧ�������Ӥ������� �����������ö�ӧҹ�����ѹ��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('306', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 ������ҧ�������Ѻ㹤���ᵡ��ҧ�ҧ�Ѳ�����', '���ҧ�������Ѻ������餹��ҧ�Ѳ����� ��������ѹ�������ѹ��
		������� ���ʹѺʹع��÷ӧҹ�����ѹ �������ҧ����ѹ��Ҿ�����ҧ����� ���������ҧ�Ѳ���������ҧ�ѹ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('306', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 ��л�Ѻ��ҷ� �������Ըա�÷ӧҹ����ʹ���ͧ�Ѻ��Ժ��ҧ�Ѳ�����', '�ҷҧ�ЧѺ��;Ծҷ�����ҧ�Ѳ��������ᵡ��ҧ �¾���������ҹ��л�йջ�й�����¤�������������Ѳ��������ҧ�֡���
		��Ѻ����¹���ط�� ��ҷ� �����������ʹ���ͧ�Ѻ�Ѳ��������ᵡ��ҧ ���ͻ���ҹ����ª�������ҧ��������������ҧ�Ѳ���������ҧ�ѹ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('307', $arr_dept[$j], 1, '���㨤������·������蹵�ͧ����������', '���㨤������·������蹵�ͧ���������� ����ö�Ѻ㨤��� ��ػ����������ͧ�����١��ͧ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('307', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 ��������������������֡��ФӾٴ', '���㨷�駤���������й���ԧ������ �ҡ����ѧࡵ ��˹�� ��ҷҧ ���͹�����§ �ͧ �����Դ��ʹ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('307', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 ������㨤�������ὧ㹡����� ��ҷҧ �Ӿٴ ���͹�����§', '���㨤������·��������ʴ��͡���ҧ�Ѵਹ㹡����� ��ҷҧ �Ӿٴ ���͹�����§
		���㨤����Դ �����ѧ�� ���ͤ�������֡�ͧ������ �����ʴ��͡��§��硹���  
		����ö�к��ѡɳй�������ͨش�����ҧ����ҧ˹�觢ͧ�����Դ��ʹ�����', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('307', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 ������㨡��������÷�駷���繤Ӿٴ ��Ф�������ὧ㹡��������áѺ��������', '���㨹�¢ͧ�ĵԡ��� ������ ��Ф�������֡�ͧ������ 
		��������㨹������繻���ª��㹡�ü١�Ե� �Ӥ������ѡ ���͵Դ��ͻ���ҹ�ҹ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('307', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 ����������˵آͧ�ĵԡ���������', '���㨶֧���˵آͧ�ĵԡ��� ���ͻѭ�� ��ʹ������Ңͧ�ç�٧�������Ƿ�������Դ�ĵԡ����ͧ������ 
		���㨾ĵԡ����ͧ������ ������ö�͡�֧�ش��͹ �ش�� ����ѡɳй���� �ͧ����������ҧ�١��ͧ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('308', $arr_dept[$j], 1, '�����ç���ҧͧ���', '�����ç���ҧͧ��� ��¡�úѧ�Ѻ�ѭ�� �� ����º ��º�� ��Т�鹵͹��û�Ժѵԧҹ ���ͻ���ª��㹡�û�Ժѵ�˹�ҷ���Ҫ��������ҧ�١��ͧ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('308', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 ������㨤�������ѹ���ԧ�ӹҨ�������繷ҧ���', '��������ѹ��Ҿ���ҧ����繷ҧ��������ҧ�ؤ���ͧ��� �Ѻ�����Ҽ������ӹҨ�Ѵ�Թ����ͼ������Է�Ծŵ�͡�õѴ�Թ���дѺ��ҧ� ��йӤ������㨹���������ª������觼����ķ���ͧͧ������Ӥѭ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('308', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 ��������Ѳ�����ͧ���', '���㨻��ླջ�Ժѵ� ��ҹ��� ����Ѳ������ͧ����ͧ��÷������Ǣ�ͧ �����������Ըա�������������ջ���Է���Ҿ ���ͻ���ª��㹡�û�Ժѵ�˹�ҷ���Ҫ���
		���㨢�ͨӡѴ�ͧͧ��� �����������Ҩ��з�����������Ҩ��з�������ؼ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('308', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 ������㨤�������ѹ��ͧ����պ��ҷ�Ӥѭ�ͧ���', '�Ѻ���֧��������ѹ���ԧ�ӹҨ�ͧ����պ��ҷ�Ӥѭ�ͧ��� ���ͻ���ª��㹡�ü�ѡ�ѹ��áԨ���˹�ҷ���Ѻ�Դ�ͺ����Դ����Է�Լ�', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('308', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 ����������˵ؾ�鹰ҹ�ͧ�ĵԡ���ͧ���', '�������˵ؾ�鹰ҹ�ͧ�ĵԡ���ͧ����˹��§ҹ�ͧ����Тͧ�Ҥ�Ѱ����� ��ʹ���ѭ�� ����͡�ʷ�������� ��йӤ������㨹���ҢѺ����͹��û�Ժѵԧҹ���ǹ��赹�����Ѻ�Դ�ͺ�������ҧ���к�
		���㨻���繻ѭ�ҷҧ������ͧ ���ɰ�Ԩ �ѧ�� ������������¹͡����ȷ���ռš�з���͹�º���Ҥ�Ѱ�����áԨ�ͧͧ��� �����ŧ�ԡĵ����͡�� ��˹��ش�׹��з�ҷյ����áԨ�˹�ҷ�������ҧ�ʹ���ͧ�����������觻���ª��ͧ�ҵ����Ӥѭ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('309', $arr_dept[$j], 1, '��繻ѭ�������͡������������ŧ��ʹ��Թ���', '�����繻ѭ�� �ػ��ä ������Ը����������ͪ�� 
		�������͡�����������ͷ��й��͡�ʹ���������ª��㹧ҹ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('309', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 ��ШѴ��ûѭ��੾��˹�������˵��ԡĵ', 'ŧ��ͷѹ��������Դ�ѭ��੾��˹������������ԡĵ� ���Ҩ���������ͧ�� ��������ͷ��
		��䢻ѭ�����ҧ��觴�ǹ 㹢�з�褹��ǹ�˭����������ʶҹ��ó���������ѭ�Ҥ��������ͧ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('309', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 �������������ǧ˹�� �������ҧ�͡�� ������ա����§�ѭ���������', '�Ҵ��ó��������������ǧ˹���������ҧ�͡�� ������ա����§�ѭ�ҷ���Ҩ�Դ������������� 
		���ͧ���Ըա�÷���š����㹡����䢻ѭ���������ҧ��ä������������Դ����ǧ�Ҫ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('309', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 �������������ǧ˹�� �������ҧ�͡�� ������ա����§�ѭ�ҷ���Ҩ�Դ�������лҹ��ҧ', '�Ҵ��ó��������������ǧ˹���������ҧ�͡�� ������ա����§�ѭ�ҷ���Ҩ�Դ���������лҹ��ҧ 
		�Դ�͡��ͺ�������Ըա�÷���š����������ҧ��ä�㹡����䢻ѭ�ҷ��Ҵ��Ҩ��Դ����͹Ҥ�', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('309', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 �������������ǧ˹�� �������ҧ�͡�� ������ա����§�ѭ�ҷ���Ҩ�Դ�����������', '�Ҵ��ó��������������ǧ˹���������ҧ�͡�� ������ա����§�ѭ�ҷ���Ҩ�Դ������͹Ҥ� 
		���ҧ����ҡ�Ȣͧ��äԴ�����������Դ����˹��§ҹ��С�е��������͹�����ҹ�ʹͤ����Դ�����㹡�÷ӧҹ ������ѭ���������ҧ�͡����������', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('310', $arr_dept[$j], 1, '��ͧ��ä����١��ͧ �Ѵਹ㹧ҹ ����ѡ�ҡ� ����º', '��ͧ����������� ��к��ҷ㹡�û�Ժѵԧҹ �դ����١��ͧ �Ѵਹ  
		��������Դ����������º���Ҿ�Ǵ�����ͧ��÷ӧҹ 
		��ԺѵԵ���� ����º ��Т�鹵͹ ����˹� ���ҧ��觤�Ѵ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('310', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 ��е�Ǩ�ҹ�����١��ͧ�ͧ�ҹ��赹�Ѻ�Դ�ͺ', '��Ǩ�ҹ�ҹ�˹�ҷ�� �����Ѻ�Դ�ͺ���ҧ�����´ ���ͤ����١��ͧ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('310', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 ��д��Ť����١��ͧ�ͧ�ҹ��駢ͧ����м����蹷������㹤����Ѻ�Դ�ͺ�ͧ��', '��Ǩ�ͺ�����١��ͧ�ͧ�ҹ�˹�ҷ������Ѻ�Դ�ͺ�ͧ���ͧ
		��Ǩ�ͺ�����١��ͧ�ҹ�ͧ������ ����ӹҨ˹�ҷ�����˹��¡����� �� ����º ��ͺѧ�Ѻ �������Ǣ�ͧ 
		��Ǩ�����١��ͧ�����鹵͹��С�кǹ�ҹ��駢ͧ���ͧ��м����� ����ӹҨ˹�ҷ��
		�ѹ�֡��������´�ͧ�Ԩ����㹧ҹ��駢ͧ���ͧ��Тͧ������ ���ͤ����١��ͧ�ͧ�ҹ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('310', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 ��е�Ǩ�ͺ�����١��ͧ����֧�س�Ҿ�ͧ�����������ç���', '��Ǩ�ͺ��������´�����׺˹�Ңͧ�ç��õ����˹����� 
		��Ǩ�ͺ�����١��ͧ �ú��ǹ ��Фس�Ҿ�ͧ������ 
		����ö�кآ�ͺ����ͧ���͢����ŷ������ �������������ú��ǹ���ͤ����١��ͧ�ͧ�ҹ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('310', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 ��оѲ���к���õ�Ǩ�ͺ�����١��ͧ�ͧ��кǹ�ҹ', '�Ѳ���к���õ�Ǩ�ͺ ���ͤ����١��ͧ�����鹵͹ ��������س�Ҿ�ͧ������', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('311', $arr_dept[$j], 1, '��Ժѵԧҹ�����ӹҨ˹�ҷ��������ͧ�ա�áӡѺ����', '��Ժѵԧҹ�����Ҩ����ͧ�ա�áӡѺ�������Դ
		�Ѵ�Թ��ͧ�����áԨ�����ͺࢵ�ӹҨ˹�ҷ���Ѻ�Դ�ͺ�ͧ��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('311', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 ��л�Ժѵԧҹ�˹�ҷ�����ҧ����', '���ҵѴ�Թ�����ͧ��������Ҷ١��ͧ�����˹�ҷ�� �����ռ�������繴��������ҧ����
		�ʴ��͡���ҧ����㹡�û�Ժѵ�˹�ҷ����������ʶҹ��ó����դ��������͹', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('311', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 �������㹤�������ö�ͧ��', '�������㹤�������������ö ����ѡ��Ҿ�ͧ����Ҩ�����ö��Ժѵ�˹�ҷ�������ʺ���������
		�ʴ������������ҧ�Դ��㹡�õѴ�Թ����ͤ�������ö�ͧ��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('311', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 �������㹡�÷ӧҹ����ҷ��', '�ͺ�ҹ����ҷ�¤�������ö 
		�ʴ������Դ��繢ͧ������������繴��¡Ѻ���ѧ�Ѻ�ѭ�� ���ͼ�����ӹҨ �����ʶҹ��ó���Ѵ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('311', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 ������㨷ӧҹ����ҷ���ҡ��С����ʴ��ش�׹�ͧ��', '��������Ѻ���һ�Ժѵԧҹ����ҷ�� �����դ�������§�٧
		�����׹��Ѵ༪ԭ˹�ҡѺ���ѧ�Ѻ�ѭ�����ͼ�����ӹҨ 
		�����ʴ��ش�׹�ͧ�����ҧ�ç仵ç��㹻���繷���������Ӥѭ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('312', $arr_dept[$j], 1, '�դ������ͧ���㹡�û�Ժѵԧҹ', '��Ѻ�����ҡѺ��Ҿ��÷ӧҹ����ҡ�Ӻҡ �������������ӹ�µ�͡�û�Ժѵԧҹ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('312', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 �������Ѻ�������繷��е�ͧ��Ѻ����¹', '����Ѻ������㨤�����繢ͧ������ 
		���㨷�������¹�����Դ ��ȹ��� ��������Ѻ����������', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('312', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 ������Ԩ�ó�ҹ㹡�û�Ѻ�顮����º', '���Ԩ�ó�ҹ㹡�û�Ѻ�顮����º�����������Ѻʶҹ��ó� ���ͼ�����稢ͧ�ҹ ����ѵ�ػ��ʧ��ͧ˹��§ҹ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('312', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 ��л�Ѻ����¹�Ըա�ô��Թ�ҹ', '��Ѻ����¹�Ըա�û�Ժѵԧҹ �����ҡѺʶҹ��ó� ���ͺؤ�����ѧ��������������� 
		��Ѻ��鹵͹��÷ӧҹ ������������Է���Ҿ�ͧ˹��§ҹ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('312', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 ��л�Ѻ����¹Ἱ���ط��', '��ѺἹ�ҹ ������� �����ç��� ���������������Ѻʶҹ��ó�੾��˹�� 
		��Ѻ����¹�ç���ҧ ���͡�кǹ�ҹ �繡��੾�С�� ��������Ѻ�Ѻʶҹ��ó�੾��˹��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('313', $arr_dept[$j], 1, '���ʹ͢����� ���ͤ���������ҧ�ç仵ç��', '���ʹ� ������ ���ͤ���������ҧ�ç仵ç�� ���ѧ�����Ѻ�ٻẺ��ù��ʹ͵������ʹ�����дѺ�ͧ���ѧ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('313', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 ����������������鹵�㹡�è٧�', '���ʹ͢����� ������� ����� ���͵�����ҧ��Сͺ����ա����������ҧ�ͺ�ͺ ���������������� ����Ѻ ���ʹѺʹع�����Դ�ͧ��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('313', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 ��л�Ѻ�ٻẺ��ù��ʹ����ͨ٧�', '��Ѻ�ٻẺ��ù��ʹ������������Ѻ����ʹ�����дѺ�ͧ���ѧ 
		�Ҵ��ó�֧�Ţͧ��ù��ʹ� ��Фӹ֧�֧�Ҿ�ѡɳ�ͧ���ͧ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('313', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 �������ŻС�è٧�', '�ҧἹ��ù��ʹ��¤Ҵ��ѧ��Ҩ�����ö�٧��������蹤���µ��
		��Ѻ���Т�鹵͹�ͧ���������� ���ʹ� ��Ш٧������������Ѻ���ѧ���С���� �����������
		�Ҵ��ó���о���������Ѻ��͡Ѻ��ԡ����ҷء�ٻẺ�ͧ���ѧ����Ҩ�Դ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('313', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 �������ط��Ѻ��͹㹡�è٧�', '��ǧ�Ҽ��ʹѺʹع ������������㹡�ü�ѡ�ѹ�ǤԴ Ἱ�ҹ�ç��� ������ķ����
		������������ǡѺ�Ե�Է����Ū� ����繻���ª��㹡��������è٧�', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('314', $arr_dept[$j], 1, '�Һ���㹧ҹ��Ż�', '��繤س���㹧ҹ��ŻТͧ�ҵ������Ż����� ���ʴ������ѡ����ǧ�˹㹧ҹ��Ż�
		ʹ㨷�������ǹ����㹡�����¹���  �Դ��� �������ҧ�ҹ��Ż�ᢹ���ҧ� 
		�֡���������ҧ�����ӹҭ㹧ҹ��ŻТͧ�����ҧ��������', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('314', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 ��������ٻẺ��ҧ� �ͧ�ҹ��Ż�', '�¡��Ф���ᵡ��ҧ�ͧ�ҹ��Ż��ٻẺ��ҧ� ���͸Ժ�����������Ѻ���֧�س��Ңͧ�ҹ��Ż�����ҹ����
		�����ٻẺ��Шش�蹢ͧ�ҹ��Ż��ٻẺ��ҧ� ��й����㹧ҹ��ŻТͧ����
		����ö���·ʹ�س�����ԧ��Ż���������Դ���͹��ѡ���ǧ���ҧ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('314', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 ��л���ء��㹡�����ҧ��ä�ҹ��Ż�', '���Է�ԾŢͧ�ҹ��Ż��ؤ���µ�ҧ� �����ç�ѹ����㹡�����ҧ��ä�ҹ��ŻТͧ��
		����ء����������л��ʺ��ó�㹧ҹ��Ż�����㹡�����ҧ��ä�ҹ��ŻТͧ��', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('314', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 ������ҧ�ç�ѹ��������赹�ͧ��м�������', '����ء��س�������ѡɳ��蹢ͧ�ҹ��Ż��ؤ��ҧ� ����㹡���ѧ��ä�ŧҹ ������ç�ѹ�������������Դ�Ե�ӹ֡㹡��͹��ѡ��ҹ��Ż�
		����ʵ��ҧ��Ż�����ᢹ��Ҽ����ҹ �������ҧ��ä�ŧҹ���ᵡ��ҧ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('314', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 ����ѧ��ä�ҹ��Żз�����͡�ѡɳ�੾�е�', '�ѧ��ä�ҹ��Żз�����͡�ѡɳ�੾�е�����繷������Ѻ �����Ҩ��繡���ѧ��ä�ҹ������ ����͹��ѡ������觧ҹ��Żд�����', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('315', $arr_dept[$j], 1, '��ԺѵԵ�����ǹ˹�觢ͧ��ǹ�Ҫ���', '��þ��ж�ͻ�ԺѵԵ��ẺἹ��и���������ԺѵԢͧ��ǹ�Ҫ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('315', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 ����ʴ������ѡ�յ����ǹ�Ҫ���', '�ʴ������֧�����Ф����Ҥ����㨷������ǹ˹�觢ͧ��ǹ�Ҫ���
		����ǹ���ҧ�Ҿ�ѡɳ���Ъ������§�������ǹ�Ҫ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('315', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 �������ǹ����㹡�ü�ѡ�ѹ�ѹ��Ԩ�ͧ��ǹ�Ҫ���', '����ǹ����㹡��ʹѺʹع�ѹ��Ԩ�ͧ��ǹ�Ҫ��è�������������
		�Ѵ�ӴѺ������觴�ǹ���ͤ����Ӥѭ�ͧ�ҹ�������ѹ��Ԩ�ͧ��ǹ�Ҫ��ú�����������', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('315', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 ����ִ��ͻ���ª��ͧ��ǹ�Ҫ����繷����', '�ִ��ͻ���ª��ͧ��ǹ�Ҫ�������˹��§ҹ�繷���� ��͹���ФԴ�֧����ª��ͧ�ؤ�����ͤ�����ͧ��âͧ���ͧ
		�׹��Ѵ㹡�õѴ�Թ㨷���繻���ª������ǹ�Ҫ��� �����ҡ�õѴ�Թ㨹���Ҩ���ռ���͵�ҹ�����ʴ����������繴��¡���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('315', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 �������������ͻ���ª��ͧ��ǹ�Ҫ���', '������л���ª��������鹢ͧ˹��§ҹ��赹�Ѻ�Դ�ͺ ���ͻ���ª��������Ǣͧ��ǹ�Ҫ��������
		�����������������Ǽ��������������л���ª����ǹ�����ͻ���ª��ͧ��ǹ�Ҫ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('316', $arr_dept[$j], 1, '���ҧ�����ѡ�ҡ�õԴ��͡Ѻ������ͧ����Ǣ�ͧ�Ѻ�ҹ', '���ҧ�����ѡ�ҡ�õԴ��͡Ѻ������ͧ����Ǣ�ͧ�Ѻ�ҹ���ͻ���ª��㹧ҹ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('316', $arr_dept[$j], 2, '�ʴ����ö���дѺ��� 1 ������ҧ�����ѡ�Ҥ�������ѹ����աѺ������ͧ����Ǣ�ͧ�Ѻ�ҹ���ҧ���Դ', '���ҧ�����ѡ�Ҥ�������ѹ����աѺ������ͧ����Ǣ�ͧ�Ѻ�ҹ���ҧ���Դ
		��������ҧ�Ե��Ҿ�Ѻ���͹�����ҹ ����Ѻ��ԡ�� ���ͼ�����', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('316', $arr_dept[$j], 3, '�ʴ����ö���дѺ��� 2 ������ҧ�����ѡ�ҡ�õԴ�������ѹ��ҧ�ѧ��', '��������Ԩ������������ա�õԴ�������ѹ��ҧ�ѧ���Ѻ������ͧ����Ǣ�ͧ�Ѻ�ҹ
		��������Ԩ�����ҧ�ѧ���ǧ���ҧ���ͻ���ª��㹧ҹ', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('316', $arr_dept[$j], 4, '�ʴ����ö���дѺ��� 3 ������ҧ�����ѡ�Ҥ�������ѹ��ѹ�Ե�', '���ҧ�����ѡ���Ե��Ҿ�����ѡɳ��繤�������ѹ��㹷ҧ��ǹ����ҡ���', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, DEPARTMENT_ID, CL_NO, CL_NAME, CL_MEANING, UPDATE_USER, 
								  UPDATE_DATE)
								  VALUES ('316', $arr_dept[$j], 5, '�ʴ����ö���дѺ��� 4 ����ѡ�Ҥ�������ѹ��ѹ�Ե���������', '�ѡ�Ҥ�������ѹ��ѹ�Ե���������ҧ������ͧ ����Ҩ��������ա�õԴ�������ѹ��㹧ҹ�ѹ���ǡ��� ���ѧ�Ҩ���͡�ʷ��еԴ�������ѹ��㹧ҹ���ա�͹Ҥ�', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				if ($BKK_FLAG==1) {
					$level = array(	"O1", "O2", "O3", "O4", "K1", "K2", "K3", "K4", "K5", "D1", "D2", "M1", "M2" );
					$target = array(	1, 1, 2, 3, 1, 2, 3, 3, 3, 3, 3, 3, 3 );
				} else {
					$level = array(	"O1", "O2", "O3", "O4", "K1", "K2", "K3", "K4", "K5", "D1", "D2", "M1", "M2", "E1", "E2", "E3", "E4", "E5", "E7", "S1", "S2", "S3" );
					$target = array(	1, 1, 2, 3, 1, 2, 3, 4, 5, 3, 4, 4, 5, 1, 1, 2, 3, 4, 1, 3, 4, 5 );
				}
				for ( $i=0; $i<count($level); $i++ ) { 
					$cmd = " SELECT CP_CODE FROM PER_COMPETENCE WHERE DEPARTMENT_ID = $arr_dept[$j] AND CP_MODEL in (1,3) ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
					while($data2 = $db_dpis2->get_array()){
						$CP_CODE = trim($data2[CP_CODE]);
						if ($level[$i]=="O1" && substr($CP_CODE,0,1)=="3") $TARGET_LEVEL = 0; else $TARGET_LEVEL = $target[$i]; 
						$cmd = " INSERT INTO PER_STANDARD_COMPETENCE (LEVEL_NO, CP_CODE, DEPARTMENT_ID, TARGET_LEVEL, UPDATE_USER, 
										UPDATE_DATE)
										VALUES ('$level[$i]', '$CP_CODE', $arr_dept[$j], $TARGET_LEVEL,  $SESS_USERID, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
					} // end while
				} // end for
				
				if ($BKK_FLAG!=1) {
					$level = array(	"D1", "D2", "M1", "M2" );
					$target = array(	1, 2, 3, 4 );
					for ( $i=0; $i<count($level); $i++ ) { 
						$cmd = " SELECT CP_CODE FROM PER_COMPETENCE WHERE DEPARTMENT_ID = $arr_dept[$j] AND CP_MODEL = 2 ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();
						while($data2 = $db_dpis2->get_array()){
							$CP_CODE = trim($data2[CP_CODE]);
							$cmd = " INSERT INTO PER_STANDARD_COMPETENCE (LEVEL_NO, CP_CODE, DEPARTMENT_ID, TARGET_LEVEL, UPDATE_USER, 
											UPDATE_DATE)
											VALUES ('$level[$i]', '$CP_CODE', $arr_dept[$j], $target[$i],  $SESS_USERID, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
						} // end while
					} // end for
				}	// end if	
			} // end for
		} // end while
	}

	if( (!$UPD && !$DEL && !$err_text) ){
		$CP_CODE = "";
		$CP_NAME = "";
		$CP_ENG_NAME = "";
		$CP_MEANING = "";
		$CP_MODEL = 1;
		$CP_ASSESSMENT = 'Y';
                $DEF_WEIGHT="";
		$CP_ACTIVE = 1;
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>