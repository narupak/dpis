<?	
//	map text data to table columns

	$table = "PER_SALARYHIS";
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
	
	$impfile_head_map = array( "SEQ","NAME","PER_CARDNO", "SAH_ORG", "SAH_POSITION", "POSITION_TYPE", "POSITION_LEVEL", "SAH_POS_NO", "SAH_OLD_SALARY",	"SAH_SALARY_EXTRA", "SAH_SALARY", "SAH_REMARK");
	$impfile_head_title = "";
	$impfile_head_thai = array( "�ӴѺ���","����-���ʡ��","�Ţ��Шӵ�ǻ�ЪҪ�", "�ѧ�Ѵ", "���˹�", "���˹觻�����", "�дѺ���˹�", "�Ţ�����˹�", "�Թ��͹", "��ҵͺ᷹�����", "������Ѻ�Թ��͹", "�����˵�");
	$impfile_exam_map = array( "1","�������� 㨴�","1234567890123", "��ǹ��ҧ", "�ѡ������", "������", "�٧", "1", "69350",	"2131.94", "69810", "(�)");

//	$head_map = array("�Ţ���","����","���ʡ��","Email","ʶҹ�","��","�ѹ�Դ","","");

	$column_map[SAH_ID] = "running";	// 0-running number SAH_ID
	$column_map[PER_ID] = "sql-n-d-select PER_ID from PER_PERSONAL where PER_CARDNO='@3' or '@2' like '%'||PER_NAME||' '||PER_SURNAME^NOTNULL";		// 1- �֧�����Ũҡ sql �繢����Ż����� s = string (���� n = number)
																																											// 		field ����ʴ� �����Ẻ d = disabled ���� e = enabled
																																											// 		�դ�� = PER_ID ����� PER_CARDNO = ���� text file column ��� {1}
	$column_map[SAH_EFFECTIVEDATE] = "screen-s-e-SAH_EFFECTIVEDATE|save_date";		// 2-map �Ѻ column 2 � text ���� excel  SAH_EFFECTIVEDATE
	$column_map[MOV_CODE] = "php-s-e-MOV_CODE";		// 3-map �Ѻ ����� ���ǹ�ͧ php MOV_CODE
	$column_map[SAH_SALARY] = "11";		// 4-map �Ѻ column 11 � text ���� excel SAH_SALARY
	$column_map[SAH_DOCNO] = "screen-s-e-SAH_DOCNO";		// 5-map �Ѻ �����㹨� SAH_DOCNO
	$column_map[SAH_DOCDATE] = "screen-s-e-SAH_DOCDATE|save_date";	// 6-map �Ѻ �����㹨�  SAH_DOCDATE = save_date(SAH_DOCDATE)
	$column_map[SAH_ENDDATE] = "screen-s-e-SAH_ENDDATE|save_date";	// 7-map �Ѻ �����㹨� SAH_ENDDATE = save_date(SAH_ENDDATE)
	$column_map[UPDATE_USER] = "update_user";	// 8-map �Ѻ UPDATE_USER �ҡ�к�
	$column_map[UPDATE_DATE] = "update_date";	// 9-map �Ѻ UPDATE_DATE �ҡ�к�
	$column_map[PER_CARDNO] = "php-s-e-PER_CARDNO";		// 10-map �Ѻ column 1 � text ���� excel  PER_CARDNO
	$column_map[SAH_PERCENT_UP] = "";		// 11-map space SAH_PERCENT_UP
	$column_map[SAH_SALARY_UP] = "";		// 12-mapspace  SAH_SALARY_UP
	//$column_map[] = "10";		// 13-map �Ѻ column 13 � text ���� excel  SAH_SALARY_EXTRA
	$column_map[SAH_SEQ_NO] = "php-s-e-SAH_SEQ_NO";	// 14-map ����� ���ǹ php SAH_SEQ_NO
	$column_map[] = "12";	// 15-map �Ѻ column 9 � text ���� excel  SAH_REMARK
	$column_map[LEVEL_NO] = "sql-s-d-select LEVEL_NO from PER_LEVEL where POSITION_TYPE = @6 and  POSITION_LEVEL = @7^sql-s-d-select LEVEL_NO from PER_LEVEL 
										where LEVEL_NAME = @7 or LEVEL_SHORTNAME = @7 or POSITION_LEVEL = @7";	// 16-map ����� ���ǹ php LEVEL_NO
	$column_map[] = "8";	// 17-map �Ѻ column 3 � text ���� excel  SAH_POS_NO
	$column_map[] = "5";	// 18-map �Ѻ column 4 � text ���� excel  SAH_POSITION
	$column_map[] = "4";	// 19-map ����� ���ǹ php SAH_ORG
	$column_map[EX_CODE] = "php-s-e-EX_CODE";	// 20-map ����� ���ǹ php EX_CODE
	$column_map[] = "8";	// 21-map �Ѻ column 3 (SAH_POS_NO) � text ���� excel  SAH_PAY_NO
	$column_map[] = "";	// 22-map space SAH_SALARY_MIDPOINT
	$column_map[SAH_KF_YEAR] = "php-s-e-SAH_KF_YEAR";	// 23-map ����� ���ǹ php SAH_KF_YEAR
	$column_map[SAH_KF_CYCLE] = "php-s-e-SAH_KF_CYCLE";	// 24-map ����� ���ǹ php SAH_KF_CYCLE
	$column_map[] = "";		// 25-map space SAH_TOTAL_SCORE
	$column_map[SAH_LAST_SALARY] = "php-s-e-SAH_LAST_SALARY";	// 26-map ����� ���ǹ php SAH_LAST_SALARY
	$column_map[SM_CODE] = "php-s-e-SM_CODE";	// 27-map ����� ���ǹ php SM_CODE
	$column_map[SAH_CMD_SEQ] = "php-s-e-SAH_CMD_SEQ";		// 28-map ����� ���ǹ php SAH_CMD_SEQ
	$column_map[] = "";		// 29-map space SAH_ORG_DOPA_CODE
	$column_map[] = "9";		// 30-map space SAH_OLD_SALARY
	$column_map[] = "";		// 31-map space SAH_POS_NO_NAME
	$column_map[] = "";		// 32-map space AUDIT_FLAG
	$column_map[] = "";		// 33-map space SAH_SPECIALIST
	$column_map[] = "";		// 34-map space SAH_REF_DOC
	$column_map[] = "";		// 35-map space SAH_DOCNO_EDIT
	$column_map[] = "";		// 36-map space SAH_DOCDATE_EDIT
	$column_map[] = "";		// 37-map space SAH_REMARK1
	$column_map[] = "";		// 38-map space SAH_REMARK2

	// �� function ����Ѻ ��˹���Ҿ����
	function data_setting_extend($i_data_map, $var_d_in) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port;
		global $search_kf_cycle, $search_budget_year;
		global $PER_SALARY_CURRENT_UPDATE;

		$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port);
	
//		echo "((((((data=".implode(",",$i_data_map)."<br>";
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
			$PER_CARDNO = str_replace(" ","",$var3);
			$PER_CARDNO = str_replace("-","",$var3);
			$SAH_KF_CYCLE = $search_kf_cycle;
			$SAH_KF_YEAR = $search_budget_year;
			if($SAH_KF_CYCLE == 1) $SAH_EFFECTIVEDATE = ($search_budget_year-543) . "-04-01";
			elseif($SAH_KF_CYCLE == 2) $SAH_EFFECTIVEDATE = ($search_budget_year-543) . "-10-01";
		
//			$SAH_EFFECTIVEDATE = save_date($SAH_EFFECTIVEDATE);
			$tmp_date = explode("-", $SAH_EFFECTIVEDATE);
			// 86400 �Թҷ� = 1 �ѹ
			$before_cmd_date = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2), $tmp_date[0]) - 86400);
			$before_cmd_date = date("Y-m-d", $before_cmd_date);
		
//			$SAH_DOCDATE =  save_date($SAH_DOCDATE);
//			$SAH_ENDDATE =  save_date($SAH_ENDDATE);

			$SM_CODE = "";

			if (!$MOV_CODE) $MOV_CODE = "21520";
			if (!$EX_CODE) $EX_CODE = "024";
			if (!$SAH_PERCENT_UP) $SAH_PERCENT_UP = "NULL";
			if (!$SAH_SALARY_UP) $SAH_SALARY_UP = "NULL";
			if (!$SAH_SALARY_EXTRA) $SAH_SALARY_EXTRA = "NULL";
			if (!$SAH_SALARY_MIDPOINT) $SAH_SALARY_MIDPOINT = "NULL";
			$SAH_SEQ_NO = (trim($SAH_SEQ_NO))? $SAH_SEQ_NO : 1;		
			if (!$SAH_KF_CYCLE) $SAH_KF_CYCLE = "NULL";
			if (!$SAH_TOTAL_SCORE) $SAH_TOTAL_SCORE = "NULL";
			if (!$SAH_CMD_SEQ) $SAH_CMD_SEQ = "NULL";

			// mai edit 29 09 54
			if (!$SAH_LAST_SALARY) $SAH_LAST_SALARY = "N";
			if (!$PER_SALARY_CURRENT_UPDATE) $PER_SALARY_CURRENT_UPDATE = "N";

			$cmd = " select PER_ID from PER_PERSONAL where PER_CARDNO = '$PER_CARDNO' or '$NAME' like '%'||PER_NAME||' '||PER_SURNAME ";
			$count_data = $db_dpis1->send_cmd($cmd);
			if ($count_data) {			
				$data1 = $db_dpis1->get_array();
				$PER_ID = $data1[PER_ID];
			}
//			$SAH_DOCDATE = show_date_format($SAH_DOCDATE, 1);
//			$SAH_ENDDATE = show_date_format($SAH_ENDDATE, 1);
				
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
				$cmd = " select PER_ID from PER_PERSONAL where PER_CARDNO = '$PER_CARDNO' ";
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
					//echo "<br>";
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
					//echo "$cmd<br>";
*/	
					if ($PER_SALARY_CURRENT_UPDATE=='Y' ){
						$cmd = " update PER_PERSONAL set PER_SALARY = $SAH_SALARY , MOV_CODE = '$MOV_CODE' ,
										PER_DOCNO = '$SAH_DOCNO' , PER_DOCDATE = '$SAH_DOCDATE' where PER_ID = $tmp_PER_ID ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
					}

					$SAH_ID++;
				} // else echo "$PER_CARDNO $NAME<br>";
				//echo "<meta http-equiv=\"refresh\" content=\"0;URL=select_database_excel.html?excel_msg=".$excel_msg."\">";	
			
		/* ^ ^ ^ ^ ^ ^ ^  ��ǹ�ͧ���������е����ͧ��� ^ ^ ^ ^ ^ ^ ^ ^*/
		
	}

?>