<?
	if (!trim($SELECTED_PER_ID)) $SELECTED_PER_ID = $PER_ID;
//	echo "1..search_per_type=$search_per_type , SELECTED_PER_ID=$SELECTED_PER_ID , PER_ID=$PER_ID<br>";
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$IMG_PATH = "../../attachment/pic_personal/";
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	// sort ��Ǣ�ͷ�����͡ ���ͨѴ�ӴѺ����ʴ���͹��ѧ �ͧ $arr_history_name ������͡�����
//	$arr_history_sort = array("EDUCATE", "PUNISHMENT", "NOTPAID", "POSITIONHIS", "", "ABILITY", "SERVICEHIS", "SPECIALSKILLHIS", "DECORATEHIS", "ABSENTHIS"); 
	$arr_history_sort = array("EDUCATE", "PUNISHMENT", "NOTPAID", "POSITIONHIS", "", "ABILITY", "SERVICEHIS", "SPECIALSKILLHIS", "DECORATEHIS", "ABSENTHIS"); 
	// sort ��Ǣ�ͷ�����͡
	
	if (!$HISTORY_LIST) {
		$arr_history_name = $arr_history_sort;
	} else {
		$arr_history_name = explode("|", $HISTORY_LIST);
	}
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE in (". (is_array($search_per_type) ? implode(", ", $search_per_type) : $search_per_type) ."))";

  	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
	}elseif($MINISTRY_ID){
		unset($arr_department);
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID and OL_CODE='02' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	}elseif($PROVINCE_CODE){
		$cmd = " select ORG_ID from PER_ORG where PV_CODE='$PROVINCE_CODE' and OL_CODE='02' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	} // end if

//	if(!trim($select_org_structure)){
		if($select_org_structure==1){	//����Ѻ�ͺ���§ҹ
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if(trim($search_org_id)) $arr_search_condition[] = "(c.ORG_ID = $search_org_id or d.ORG_ID = $search_org_id)";
		if(trim($search_org_id_1)) $arr_search_condition[] = "(c.ORG_ID_1 = $search_org_id_1 or d.ORG_ID_1 = $search_org_id_1)";
		if(trim($search_org_id_2)) $arr_search_condition[] = "(c.ORG_ID_2 = $search_org_id_2 or d.ORG_ID_2 = $search_org_id_2)";
//	}
		
	if($list_type == "SELECT"){
		$arr_search_condition[] = "(a.PER_ID in ($SELECTED_PER_ID))";
	}elseif($list_type == "CONDITION"){
		if(trim($search_pos_no))  {	
			if ($search_per_type == 1 || $search_per_type==5)
				$arr_search_condition[] = "(trim(POS_NO) = '$search_pos_no')";
			elseif ($search_per_type == 2) 
				$arr_search_condition[] = "(trim(POEM_NO) = '$search_pos_no')";		
			elseif ($search_per_type == 3) 	
				$arr_search_condition[] = "(trim(POEMS_NO) = '$search_pos_no')";
			elseif ($search_per_type == 4) 	
				$arr_search_condition[] = "(trim(POT_NO) = '$search_pos_no')";
			else if ($search_per_type==0)		//������
				$arr_search_condition[] = "((trim(POS_NO) = '$search_pos_no') or (trim(POEM_NO) = '$search_pos_no') or (trim(POEMS_NO) = '$search_pos_no') or (trim(POT_NO) = '$search_pos_no')) ";
		}
		if(trim($search_pos_no_name)){
			if ($search_per_type == 1 || $search_per_type==5)
				$arr_search_condition[] = "(trim(POS_NO_NAME) = '$search_pos_no_name')";
			elseif ($search_per_type == 2) 
				$arr_search_condition[] = "(trim(POEM_NO_NAME) = '$search_pos_no_name')";		
			elseif ($search_per_type == 3) 	
				$arr_search_condition[] = "(trim(POEMS_NO_NAME) = '$search_pos_no_name')";
			elseif ($search_per_type == 4) 	
				$arr_search_condition[] = "(trim(POT_NO_NAME) = '$search_pos_no_name')";
			else if ($search_per_type==0)		//������
				$arr_search_condition[] = "((trim(POS_NO_NAME) = '$search_pos_no_name') or (trim(POEM_NO_NAME) = '$search_pos_no_name') or (trim(POEMS_NO_NAME) = '$search_pos_no_name') or (trim(POT_NO_NAME) = '$search_pos_no_name')) ";
		}
		if(trim($search_name)) $arr_search_condition[] = "(a.PER_NAME like '$search_name%')";
		if(trim($search_surname)) $arr_search_condition[] = "(a.PER_SURNAME like '$search_surname%')";
		if(trim($search_min_level)){ 
			if($DPISDB=="odbc") $arr_search_condition[] = "(trim(a.LEVEL_NO) >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="oci8") $arr_search_condition[] = "(trim(a.LEVEL_NO) >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="mysql") $arr_search_condition[] = "(trim(a.LEVEL_NO) >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
		}
		if(trim($search_max_level)){ 
			if($DPISDB=="odbc") $arr_search_condition[] = "(trim(a.LEVEL_NO) <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="oci8") $arr_search_condition[] = "(trim(a.LEVEL_NO) <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="mysql") $arr_search_condition[] = "(trim(a.LEVEL_NO) <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
		}
		$arr_search_condition[] = "(a.PER_STATUS in (". implode(",", $search_per_status) ."))";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	$fname= "rpt_R0040063_rtf.rtf";

//	echo "font=$font , NUMBER_DISPLAY=$NUMBER_DISPLAY<br>";
	if (!$font) $font = "AngsanaUPC";
//	echo "font=$font , NUMBER_DISPLAY=$NUMBER_DISPLAY<br>";
	
	$RTF = new RTF("a4", 750, 500, 500, 500);

	$RTF->set_default_font($font, 14);
	
	$page_ch = 98;

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$heading_width[EDUCATE][0] = "20";		// "40";
	$heading_width[EDUCATE][1] = "10";		// "20";
	$heading_width[EDUCATE][2] = "17";		// "35";
	$heading_width[EDUCATE][3] = "17";		// "35";
	$heading_width[EDUCATE][4] = "12";		// "25";
	$heading_width[EDUCATE][5] = "22";		// "45";
	
	$heading_width[POSITIONHIS][0] = "15";
	$heading_width[POSITIONHIS][1] = "44";      
	$heading_width[POSITIONHIS][2] = "8";
	$heading_width[POSITIONHIS][3] = "8";
	$heading_width[POSITIONHIS][4] = "8";
	$heading_width[POSITIONHIS][5] = "15";

	$heading_width[PUNISHMENT][0] = "22";
	$heading_width[PUNISHMENT][1] = "55";
	$heading_width[PUNISHMENT][2] = "21";

	$heading_width[NOTPAID][0] = "22";
	$heading_width[NOTPAID][1] = "55";
	$heading_width[NOTPAID][2] = "21";
	
	$heading_width[ABILITY][0] = "49";
	$heading_width[ABILITY][1] = "49";
	
	$heading_width[SERVICEHIS][0] = "10";
	$heading_width[SERVICEHIS][1] = "39";
	$heading_width[SERVICEHIS][2] = "10";
	$heading_width[SERVICEHIS][3] = "39";

	$heading_width[SPECIALSKILLHIS][0] = "40";
	$heading_width[SPECIALSKILLHIS][1] = "58";

	$heading_width[DECORATEHIS][0] = "24";
	$heading_width[DECORATEHIS][1] = "22";
	$heading_width[DECORATEHIS][2] = "15";
	$heading_width[DECORATEHIS][3] = "22";
	$heading_width[DECORATEHIS][4] = "15";

	$heading_width[ABSENTHIS][0] = "9";
	$heading_width[ABSENTHIS][1] = "8";
	$heading_width[ABSENTHIS][2] = "8";
	$heading_width[ABSENTHIS][3] = "8";
	$heading_width[ABSENTHIS][4] = "8";
	$heading_width[ABSENTHIS][5] = "8";

function print_header($HISTORY_NAME){
		global $RTF, $heading_width, $SESS_USERID;

		switch($HISTORY_NAME){
			case "EDUCATE" :
				$RTF->open_line();
				$RTF->set_font($font, 14);
				$RTF->color("0");	// 0='BLACK'
				$RTF->cell($RTF->bold(1)."ʶҹ�֡��".$RTF->bold(0), $heading_width[$HISTORY_NAME][0], "center", "25", "TRBL");	
				$RTF->cell($RTF->bold(1)."����� - �֧".$RTF->bold(0), $heading_width[$HISTORY_NAME][1], "center", "25", "TRBL");
				$RTF->cell($RTF->bold(1)."�زԷ�����Ѻ/�Ң�".$RTF->bold(0), $heading_width[$HISTORY_NAME][2], "center", "25", "TRBL");
				$RTF->cell($RTF->bold(1)."ʶҹ���".$RTF->bold(0), $heading_width[$HISTORY_NAME][3], "center", "25", "TRBL");
				$RTF->cell($RTF->bold(1)."����� - �֧".$RTF->bold(0), $heading_width[$HISTORY_NAME][4], "center", "25", "TRBL");
				$RTF->cell($RTF->bold(1)."��ѡ�ٵ�/��蹷��".$RTF->bold(0), $heading_width[$HISTORY_NAME][5], "center", "25", "TRBL");
				$RTF->close_line();
				break;
			case "POSITIONHIS" :
				$RTF->open_line();
				$RTF->set_font($font, 14);
				$RTF->color("0");	// 0='BLACK'
				$RTF->cell($RTF->bold(1)."�ѹ ��͹ ��".$RTF->bold(0), $heading_width[$HISTORY_NAME][0], "center", "25", "TRBL");	
				$RTF->cell($RTF->bold(1)."���˹�".$RTF->bold(0), $heading_width[$HISTORY_NAME][1], "center", "25", "TRBL");
				$RTF->cell($RTF->bold(1)."�Ţ��� ���˹�".$RTF->bold(0), $heading_width[$HISTORY_NAME][2], "center", "25", "TRBL");
				$RTF->cell($RTF->bold(1)."�дѺ".$RTF->bold(0), $heading_width[$HISTORY_NAME][3], "center", "25", "TRBL");
				$RTF->cell($RTF->bold(1)."�ѵ�� �Թ��͹".$RTF->bold(0), $heading_width[$HISTORY_NAME][4], "center", "25", "TRBL");
				$RTF->cell($RTF->bold(1)."�͡�����ҧ�ԧ ".$SESS_USERID.$RTF->bold(0), $heading_width[$HISTORY_NAME][5], "center", "25", "TRBL");
				$RTF->close_line();
				break;
			case "PUNISHMENT" :
				$RTF->open_line();
				$RTF->set_font($font, 14);
				$RTF->color("0");	// 0='BLACK'
				$RTF->cell($RTF->bold(1)."�.�.".$RTF->bold(0), $heading_width[$HISTORY_NAME][0], "center", "25", "TRBL");	
				$RTF->cell($RTF->bold(1)."��¡��".$RTF->bold(0), $heading_width[$HISTORY_NAME][1], "center", "25", "TRBL");
				$RTF->cell($RTF->bold(1)."�͡�����ҧ�ԧ".$RTF->bold(0), $heading_width[$HISTORY_NAME][2], "center", "25", "TRBL");
				$RTF->close_line();
				break;
			case "NOTPAID" :
				$RTF->open_line();
				$RTF->set_font($font, 14);
				$RTF->color("0");	// 0='BLACK'
				$RTF->cell($RTF->bold(1)."�����-�֧".$RTF->bold(0), $heading_width[$HISTORY_NAME][0], "center", "25", "TRBL");	
				$RTF->cell($RTF->bold(1)."��¡��".$RTF->bold(0), $heading_width[$HISTORY_NAME][1], "center", "25", "TRBL");
				$RTF->cell($RTF->bold(1)."�͡�����ҧ�ԧ".$RTF->bold(0), $heading_width[$HISTORY_NAME][2], "center", "25", "TRBL");
				$RTF->close_line();
				break;
			case "ABILITY" :
				$RTF->open_line();
				$RTF->set_font($font, 14);
				$RTF->color("0");	// 0='BLACK'
				$RTF->cell($RTF->bold(1)."��ҹ��������ö�����".$RTF->bold(0), $heading_width[$HISTORY_NAME][0], "center", "25", "TRBL");	
				$RTF->cell($RTF->bold(1)."��������ö�����".$RTF->bold(0), $heading_width[$HISTORY_NAME][1], "center", "25", "TRBL");
				$RTF->close_line();
				break;			
			case "SERVICEHIS" :
				$RTF->open_line();
				$RTF->set_font($font, 14);
				$RTF->color("0");	// 0='BLACK'
				$RTF->cell($RTF->bold(1)."�.�.".$RTF->bold(0), $heading_width[$HISTORY_NAME][0], "center", "25", "TRBL");	
				$RTF->cell($RTF->bold(1)."��¡��".$RTF->bold(0), $heading_width[$HISTORY_NAME][1], "center", "25", "TRBL");
				$RTF->cell($RTF->bold(1)."�.�.".$RTF->bold(0), $heading_width[$HISTORY_NAME][2], "center", "25", "TRBL");
				$RTF->cell($RTF->bold(1)."��¡��".$RTF->bold(0), $heading_width[$HISTORY_NAME][3], "center", "25", "TRBL");
				$RTF->close_line();
				break;			
			case "SPECIALSKILLHIS" :
				$RTF->open_line();
				$RTF->set_font($font, 14);
				$RTF->color("0");	// 0='BLACK'
				$RTF->cell($RTF->bold(1)."��ҹ��������Ǫҭ�����".$RTF->bold(0), $heading_width[$HISTORY_NAME][0], "center", "25", "TRBL");	
				$RTF->cell($RTF->bold(1)."�鹷ҧ".$RTF->bold(0), $heading_width[$HISTORY_NAME][1], "center", "25", "TRBL");
				$RTF->close_line();
				break;			
			case "DECORATEHIS" :
				$RTF->open_line();
				$RTF->set_font($font, 14);
				$RTF->color("0");	// 0='BLACK'
				$RTF->cell($RTF->bold(1)."(1) ���˹� (ʹյ-�Ѩ�غѹ ੾�лշ�����Ѻ ����Ҫ�ҹ����ͧ�Ҫ�)".$RTF->bold(0), $heading_width[$HISTORY_NAME][0], "center", "25", "TRBL");	
				$RTF->cell($RTF->bold(1)."(2) �ӴѺ����ͧ�Ҫ� ������Ѻ ����Ҫ�ҹ���� �ҡ����ͧ仪���٧��� (1)".$RTF->bold(0), $heading_width[$HISTORY_NAME][1], "center", "25", "TRBL");
				$RTF->cell($RTF->bold(1)."(3) �.�.�. ������Ѻ ����Ҫ�ҹ��� (2)".$RTF->bold(0), $heading_width[$HISTORY_NAME][2], "center", "25", "TRBL");
				$RTF->cell($RTF->bold(1)."(4) ����ͧ�Ҫ� ��� (2) �Ѻ�ͺ����� ".$RTF->bold(0), $heading_width[$HISTORY_NAME][3], "center", "25", "TRBL");
				$RTF->cell($RTF->bold(1)."(5) ����ͧ�Ҫ� ��� (2) �觤׹����� ".$RTF->bold(0), $heading_width[$HISTORY_NAME][4], "center", "25", "TRBL");
				$RTF->close_line();
				break;			
			case "ABSENTHIS" :
				$RTF->open_line();
				$RTF->set_font($font, 14);
				$RTF->color("0");	// 0='BLACK'
				$RTF->cell($RTF->bold(1)."�.�.".$RTF->bold(0), $heading_width[$HISTORY_NAME][0], "center", "25", "TRBL");	
				$RTF->cell($RTF->bold(1)."�һ���".$RTF->bold(0), $heading_width[$HISTORY_NAME][1], "center", "25", "TRBL");
				$RTF->cell($RTF->bold(1)."�ҡԨ ��� �ѡ��͹".$RTF->bold(0), $heading_width[$HISTORY_NAME][2], "center", "25", "TRBL");
				$RTF->cell($RTF->bold(1)."���".$RTF->bold(0), $heading_width[$HISTORY_NAME][3], "center", "25", "TRBL");
				$RTF->cell($RTF->bold(1)."�Ҵ �Ҫ���".$RTF->bold(0), $heading_width[$HISTORY_NAME][4], "center", "25", "TRBL");
				$RTF->cell($RTF->bold(1)."�� �֡�� ���".$RTF->bold(0), $heading_width[$HISTORY_NAME][5], "center", "25", "TRBL");
				$RTF->cell($RTF->bold(1)."�.�.".$RTF->bold(0), $heading_width[$HISTORY_NAME][0], "center", "25", "TRBL");	
				$RTF->cell($RTF->bold(1)."�һ���".$RTF->bold(0), $heading_width[$HISTORY_NAME][1], "center", "25", "TRBL");
				$RTF->cell($RTF->bold(1)."�ҡԨ ��� �ѡ��͹".$RTF->bold(0), $heading_width[$HISTORY_NAME][2], "center", "25", "TRBL");
				$RTF->cell($RTF->bold(1)."���".$RTF->bold(0), $heading_width[$HISTORY_NAME][3], "center", "25", "TRBL");
				$RTF->cell($RTF->bold(1)."�Ҵ �Ҫ���".$RTF->bold(0), $heading_width[$HISTORY_NAME][4], "center", "25", "TRBL");
				$RTF->cell($RTF->bold(1)."�� �֡�� ���".$RTF->bold(0), $heading_width[$HISTORY_NAME][5], "center", "25", "TRBL");
				$RTF->close_line();
				break;			
		} // end switch
	} // function
	
	function print_footer($LEVEL_NAME) {
		global $RTF, $heading_width, $max_y, $FULLNAME, $FULL_LEVEL_NAME, $MINISTRY_NAME, $DEPARTMENT_NAME, $page_no;

		$prt_text_footer = "���� ".$FULLNAME."..".$LEVEL_NAME."..".$MINISTRY_NAME."..".$DEPARTMENT_NAME;

		$RTF->open_line();
		$RTF->set_font($font, 10);
		$RTF->color("0");	// 0='BLACK'
		$RTF->cell($RTF->bold(1).$prt_text_footer.$RTF->bold(0), 83, "left", "25", "TBL");	
		$RTF->cell($RTF->bold(1)."˹�� [$page_no]".$RTF->bold(0), 15, "right", "25", "TRB");
		$RTF->close_line();
	} // function footer

	if ($BKK_FLAG==1) {
		if($DPISDB=="odbc"){
			$order_str = "order by		c.POS_NO_NAME, CLng(c.POS_NO), d.POEM_NO_NAME, CLng(d.POEM_NO), 
															e.POEMS_NO_NAME, CLng(e.POEMS_NO), g.POT_NO_NAME, CLng(g.POT_NO) ";
		}elseif($DPISDB=="oci8"){
			$order_str = "order by		c.POS_NO_NAME, to_number(c.POS_NO), d.POEM_NO_NAME, to_number(d.POEM_NO), 
															e.POEMS_NO_NAME, to_number(e.POEMS_NO), g.POT_NO_NAME, to_number(g.POT_NO) ";
		}elseif($DPISDB=="mysql"){
			$order_str = "order by		c.POS_NO_NAME, c.POS_NO+0, d.POEM_NO_NAME, d.POEM_NO+0, e.POEMS_NO_NAME, e.POEMS_NO+0, g.POT_NO_NAME, g.POT_NO+0 ";
		}
	} else {
		$order_str = "order by		a.PER_NAME, a.PER_SURNAME ";
	}

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE, a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_CARDNO, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.DEPARTMENT_ID,
											a.OT_CODE, a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, 
											a.PER_MOTHERSURNAME, c.POS_NO_NAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO_NAME as EMP_POS_NO_NAME, d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO_NAME as EMPSER_POS_NO_NAME, e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2, 
											a.PER_OFFNO, a.PV_CODE, a.PER_ADD1, a.PER_ADD2, 
											g.POT_NO_NAME as TEMP_POS_NO_NAME, g.POT_NO as TEMP_POS_NO, g.TP_CODE as TEMP_PL_CODE, 
											g.ORG_ID as TEMP_ORG_ID, g.ORG_ID_1 as TEMP_ORG_ID_1, g.ORG_ID_2 as TEMP_ORG_ID_2
				 		from		PER_PRENAME b inner join 
				 				(	
									(
										(
											( 	
												(
													PER_PERSONAL a 
													left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
												) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
											) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
										) left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
									) left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)	
									) on (trim(a.PN_CODE)=trim(b.PN_CODE))
						$search_condition
				 		$order_str ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE, a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_CARDNO, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.DEPARTMENT_ID,
											a.OT_CODE, a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, 
											a.PER_MOTHERSURNAME, c.POS_NO_NAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO_NAME as EMP_POS_NO_NAME, d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO_NAME as EMPSER_POS_NO_NAME, e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2, 
											a.PER_OFFNO, a.PV_CODE, a.PER_ADD1, a.PER_ADD2, 
											g.POT_NO_NAME as TEMP_POS_NO_NAME, g.POT_NO as TEMP_POS_NO, g.TP_CODE as TEMP_PL_CODE, 
											g.ORG_ID as TEMP_ORG_ID, g.ORG_ID_1 as TEMP_ORG_ID_1, g.ORG_ID_2 as TEMP_ORG_ID_2
						from			PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, PER_LEVEL f,PER_POS_TEMP g
						where		trim(a.PN_CODE)=trim(b.PN_CODE) and 
											a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+) and a.POT_ID=g.POT_ID(+) and a.LEVEL_NO=f.LEVEL_NO(+)
											$search_condition
						$order_str ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE, a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_CARDNO, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.DEPARTMENT_ID,
											a.OT_CODE, a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, 
											a.PER_MOTHERSURNAME, c.POS_NO_NAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO_NAME as EMP_POS_NO_NAME, d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO_NAME as EMPSER_POS_NO_NAME, e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2, 
											a.PER_OFFNO, a.PV_CODE, a.PER_ADD1, a.PER_ADD2, 
											g.POT_NO_NAME as TEMP_POS_NO_NAME, g.POT_NO as TEMP_POS_NO, g.TP_CODE as TEMP_PL_CODE, 
											g.ORG_ID as TEMP_ORG_ID, g.ORG_ID_1 as TEMP_ORG_ID_1, g.ORG_ID_2 as TEMP_ORG_ID_2
					 	from		PER_PERSONAL a inner join PER_PRENAME b on (trim(a.PN_CODE)=trim(b.PN_CODE))
																  left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
																  left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
																  left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
																  left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
																  left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
						$search_condition
				 		$order_str ";
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd ($count_data)<\br>";
	//$db_dpis->show_error();

	if($count_data){
		$data_count = 0;
		while($data = $db_dpis->get_array()){
			$ARRAY_POH_SAH = (array) null;
			$data_count++;
			$PER_ID = $data[PER_ID];
			$PER_TYPE = $data[PER_TYPE];
			$PER_OFFNO = $data[PER_OFFNO];
			
			if($PER_TYPE==1){
				$POS_ID = $data[POS_ID];
				$POS_NO_NAME = trim($data[POS_NO_NAME]);
				if (substr($POS_NO_NAME,0,4)=="���.")
					$POS_NO = $POS_NO_NAME." ".trim($data[POS_NO]);
				else
					$POS_NO = $POS_NO_NAME.$data[POS_NO];
				$PL_CODE = trim($data[PL_CODE]);
				$PT_CODE = trim($data[PT_CODE]);
				$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
				$ORG_ID = $data[ORG_ID];
				$ORG_ID_1 = $data[ORG_ID_1];
				$ORG_ID_2 = $data[ORG_ID_2];

				$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PL_NAME]);

				$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE) = '$PT_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PT_NAME = trim($data2[PT_NAME]);
			}elseif($PER_TYPE==2){
				$POS_ID = $data[POEM_ID];
				$POS_NO = $data[EMP_POS_NO_NAME].$data[EMP_POS_NO];
				$PL_CODE = trim($data[EMP_PL_CODE]);
				$ORG_ID = $data[EMP_ORG_ID];
				$ORG_ID_1 = $data[EMP_ORG_ID_1];
				$ORG_ID_2 = $data[EMP_ORG_ID_2];

				$cmd = " select PN_NAME from PER_POS_NAME where trim(PN_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PN_NAME]);
			}elseif($PER_TYPE==3){
				$POS_ID = $data[POEMS_ID];
				$POS_NO = $data[EMPSER_POS_NO_NAME].$data[EMPSER_POS_NO];
				$PL_CODE = trim($data[EMPSER_PL_CODE]);
				$ORG_ID = $data[EMPSER_ORG_ID];
				$ORG_ID_1 = $data[EMPSER_ORG_ID_1];
				$ORG_ID_2 = $data[EMPSER_ORG_ID_2];

				$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[EP_NAME]);
			}elseif($PER_TYPE==4){
				$POS_ID = $data[POT_ID];
				$POS_NO = $data[TEMP_POS_NO_NAME].$data[TEMP_POS_NO];
				$PL_CODE = trim($data[TEMP_PL_CODE]);
				$ORG_ID = $data[TEMP_ORG_ID];
				$ORG_ID_1 = $data[TEMP_ORG_ID_1];
				$ORG_ID_2 = $data[TEMP_ORG_ID_2];

				$cmd = " select TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[TP_NAME]);
			} 

			// �����Ż���������Ҫ���
			$OT_CODE = trim($data[OT_CODE]);
			$cmd = " select OT_NAME from PER_OFF_TYPE where trim(OT_CODE)='$OT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$OT_NAME = trim($data_dpis2[OT_NAME]);

			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID = $TMP_DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_NAME = trim($data2[ORG_NAME]);
			$ORG_ID_REF = $data2[ORG_ID_REF];
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_REF ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$MINISTRY_NAME = trim($data2[ORG_NAME]);
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = trim($data2[ORG_NAME]);
			if ($ORG_NAME=="-") $ORG_NAME = "";
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_1 = trim($data2[ORG_NAME]);
			if ($ORG_NAME_1=="-") $ORG_NAME_1 = "";

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_2 ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_2 = trim($data2[ORG_NAME]);
			if ($ORG_NAME_2=="-") $ORG_NAME_2 = "";

			$PV_CODE = trim($data[PV_CODE]);
			$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE = $PV_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PV_NAME = trim($data2[PV_NAME]);
			$PER_ADD1 = trim($data[PER_ADD1]);

			$cmd = " select a.*, b.AP_NAME, c.PV_NAME 
							from PER_ADDRESS a left join PER_AMPHUR b on (a.AP_CODE=b.AP_CODE) left join PER_PROVINCE c on (a.PV_CODE=c.PV_CODE) 
							where PER_ID = $PER_ID and ADR_TYPE=1 ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();

			$PER_ADD1 = $DT_CODE_ADR = $AP_CODE_ADR = $PV_CODE_ADR = "";
			$DT_CODE_ADR = trim($data2[DT_CODE]);
			$cmd = " select DT_NAME from PER_DISTRICT where trim(DT_CODE)='$DT_CODE_ADR' ";
			$db_dpis3->send_cmd($cmd);
			$data3 = $db_dpis3->get_array();
			$DT_NAME_ADR = trim($data3[DT_NAME]);
			if (!$DT_NAME_ADR) $DT_NAME_ADR = $data2[ADR_DISTRICT];
			
			$AP_CODE_ADR = trim($data2[AP_CODE]);
			$cmd = " select AP_NAME from PER_AMPHUR where trim(AP_CODE)='$AP_CODE_ADR' ";
			$db_dpis3->send_cmd($cmd);
			$data3 = $db_dpis3->get_array();
			$AP_NAME_ADR = trim($data3[AP_NAME]);
			
			$PV_CODE_ADR = trim($data2[PV_CODE]);
			$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE_ADR' ";
			$db_dpis3->send_cmd($cmd);
			$data3 = $db_dpis3->get_array();
			$PV_NAME_ADR = trim($data3[PV_NAME]);
				
			if($data2[ADR_VILLAGE]) $PER_ADD1 .= "�����ҹ".$data2[ADR_VILLAGE]." ";
			if($data2[ADR_BUILDING]) $PER_ADD1 .= "�Ҥ��".$data2[ADR_BUILDING]." ";
			if($data2[ADR_NO]) $PER_ADD1 .= "�Ţ��� ".$data2[ADR_NO]." ";
			if($data2[ADR_MOO]) $PER_ADD1 .= "�.".$data2[ADR_MOO]." ";
			if($data2[ADR_SOI]) $PER_ADD1 .= "�.".str_replace("�.","",str_replace("���","",$data2[ADR_SOI]))." ";
			if($data2[ADR_ROAD]) $PER_ADD1 .= "�.".str_replace("�.","",str_replace("���","",$data2[ADR_ROAD]))." ";
			if($DT_NAME_ADR) {
				if ($PV_CODE_ADR=="1000") {
					$PER_ADD1 .= "�ǧ".$DT_NAME_ADR." ";
				} else {
					$PER_ADD1 .= "�.".$DT_NAME_ADR." ";
				}
			}
			if($AP_NAME_ADR) {
				if ($PV_CODE_ADR=="1000") {
					$PER_ADD1 .= "ࢵ".$AP_NAME_ADR." ";
				} else {
					$PER_ADD1 .= "�.".$AP_NAME_ADR." ";
				}
			}
			if($PV_NAME_ADR) {
				if ($PV_CODE_ADR=="1000") {
					$PER_ADD1 .= $PV_NAME_ADR." ";
				} else {
					$PER_ADD1 .= "�.".$PV_NAME_ADR." ";
				}
			}
			if($data2[ADR_POSTCODE]) $PER_ADD1 .= $data2[ADR_POSTCODE]." ";
			if (!$PER_ADD1) $PER_ADD1 = trim($data[PER_ADD1]);

			$cmd = " select a.*, b.AP_NAME, c.PV_NAME 
							from PER_ADDRESS a left join PER_AMPHUR b on (a.AP_CODE=b.AP_CODE) left join PER_PROVINCE c on (a.PV_CODE=c.PV_CODE) 
							where PER_ID = $PER_ID and ADR_TYPE=2 ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();

			$PER_ADD2 = $DT_CODE_ADR = $AP_CODE_ADR = $PV_CODE_ADR = "";
			$DT_CODE_ADR = trim($data2[DT_CODE]);
			$cmd = " select DT_NAME from PER_DISTRICT where trim(DT_CODE)='$DT_CODE_ADR' ";
			$db_dpis3->send_cmd($cmd);
			$data3 = $db_dpis3->get_array();
			$DT_NAME_ADR = trim($data3[DT_NAME]);
			if (!$DT_NAME_ADR) $DT_NAME_ADR = $data2[ADR_DISTRICT];
			
			$AP_CODE_ADR = trim($data2[AP_CODE]);
			$cmd = " select AP_NAME from PER_AMPHUR where trim(AP_CODE)='$AP_CODE_ADR' ";
			$db_dpis3->send_cmd($cmd);
			$data3 = $db_dpis3->get_array();
			$AP_NAME_ADR = trim($data3[AP_NAME]);
			
			$PV_CODE_ADR = trim($data2[PV_CODE]);
			$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE_ADR' ";
			$db_dpis3->send_cmd($cmd);
			$data3 = $db_dpis3->get_array();
			$PV_NAME_ADR = trim($data3[PV_NAME]);
				
			if($data2[ADR_VILLAGE]) $PER_ADD2 .= "�����ҹ".$data2[ADR_VILLAGE]." ";
			if($data2[ADR_BUILDING]) $PER_ADD2 .= "�Ҥ��".$data2[ADR_BUILDING]." ";
			if($data2[ADR_NO]) $PER_ADD2 .= "�Ţ��� ".$data2[ADR_NO]." ";
			if($data2[ADR_MOO]) $PER_ADD2 .= "�.".$data2[ADR_MOO]." ";
			if($data2[ADR_SOI]) $PER_ADD2 .= "�.".str_replace("�.","",str_replace("���","",$data2[ADR_SOI]))." ";
			if($data2[ADR_ROAD]) $PER_ADD2 .= "�.".str_replace("�.","",str_replace("���","",$data2[ADR_ROAD]))." ";
			if($DT_NAME_ADR) {
				if ($PV_CODE_ADR=="1000") {
					$PER_ADD2 .= "�ǧ".$DT_NAME_ADR." ";
				} else {
					$PER_ADD2 .= "�.".$DT_NAME_ADR." ";
				}
			}
			if($AP_NAME_ADR) {
				if ($PV_CODE_ADR=="1000") {
					$PER_ADD2 .= "ࢵ".$AP_NAME_ADR." ";
				} else {
					$PER_ADD2 .= "�.".$AP_NAME_ADR." ";
				}
			}
			if($PV_NAME_ADR) {
				if ($PV_CODE_ADR=="1000") {
					$PER_ADD2 .= $PV_NAME_ADR." ";
				} else {
					$PER_ADD2 .= "�.".$PV_NAME_ADR." ";
				}
			}
			if($data2[ADR_POSTCODE]) $PER_ADD2 .= $data2[ADR_POSTCODE]." ";
			if (!$PER_ADD2) $PER_ADD2 = trim($data[PER_ADD2]);

			$LEVEL_NO = trim($data[LEVEL_NO]);
			$FULL_LEVEL_NAME = trim($data[LEVEL_NAME]);
			$LEVEL_NAME = trim(str_replace("�дѺ","",$data[LEVEL_NAME]));
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$FULLNAME = ($PN_NAME)."$PER_NAME $PER_SURNAME";			
			$PER_CARDNO = $data[PER_CARDNO];

			$arr_img = (array) null;
			$arr_imgsavedate = (array) null;
			$arr_imgshow = (array) null;
//			$img_file = "";
//			if($PER_CARDNO && file_exists($IMG_PATH.$PER_CARDNO.".jpg")) $img_file = $IMG_PATH.$PER_CARDNO.".jpg";
			$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID and (PIC_SIGN = 0 or PIC_SIGN is NULL) order by PER_PICSAVEDATE asc ";
//			echo "IMG:$cmd<br>";
			$piccnt = $db_dpis2->send_cmd($cmd);
			if ($piccnt > 0) { 
				while ($data2 = $db_dpis2->get_array()) {
					$TMP_PER_CARDNO = trim($data2[PER_CARDNO]);
					$PER_GENNAME = trim($data2[PER_GENNAME]);
					$PIC_PATH = trim($data2[PER_PICPATH]);
					$PIC_SEQ = trim($data2[PER_PICSEQ]);
					$T_PIC_SEQ = substr("000",0,3-strlen("$PIC_SEQ"))."$PIC_SEQ";
					$PIC_SERVER_ID = trim($data2[PIC_SERVER_ID]);
					$PIC_SHOW = trim($data2[PIC_SHOW]);
					$PER_PICSAVEDATE = trim($data2[PER_PICSAVEDATE]);

					if ($PIC_SHOW == '1') {
						if($PIC_SERVER_ID > 0){
							if($PIC_SERVER_ID==99){		// $PIC_SERVER_ID 99 = ip �ҡ��駤���к� C06				 �� \ 
								// �� # �ó� server ��� ����¹ # ����� \ ������㹡���Ѿ��Ŵ�ٻ
								$PIC_PATH = $IMG_PATH_DISPLAY."#".$PIC_PATH;
								$PIC_PATH = str_replace("#","'",$PIC_PATH);
								$PIC_PATH = addslashes($PIC_PATH);
								$PIC_PATH = str_replace("'","",$PIC_PATH);

								$img_file = $PIC_PATH;
								$arr_img[] = $img_file;
								$arr_imgshow[] = 1;
								$arr_imgsavedate[] = $PER_PICSAVEDATE;	
							}else{  // other server
								$cmd = " select * from OTH_SERVER where SERVER_ID=$PIC_SERVER_ID ";
								if ($db_dpis3->send_cmd($cmd)) { 
									$data3 = $db_dpis3->get_array();
									$PIC_SERVER_NAME = trim($data3[SERVER_NAME]);
									$ftp_server = trim($data3[FTP_SERVER]);
									$ftp_username = trim($data3[FTP_USERNAME]);
									$ftp_password = trim($data3[FTP_PASSWORD]);
									$main_path = trim($data3[MAIN_PATH]);
									$http_server = trim($data3[HTTP_SERVER]);
										if ($http_server) {
											//echo "1.".$http_server."/".$img_file."<br>";
											$fp = @fopen($http_server."/".$img_file, "r");
											if ($fp !== false) $img_file = $http_server."/".$img_file;
											else $img_file=$IMG_PATH."shadow.jpg";
											fclose($fp);
										} else {
					//						echo "2.".$img_file."<br>";
											$img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
										}

										$arr_img[] = $img_file;
										$arr_imgshow[] = 1;
										$arr_imgsavedate[] = $PER_PICSAVEDATE;
									} // end db_dpis3
								}
						}else{ // localhost  $PIC_SERVER_ID == 0
							$img_file =  "../".$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";		//$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
							$arr_img[] = $img_file;
							$arr_imgshow[] = 1;
							$arr_imgsavedate[] = $PER_PICSAVEDATE;
						}
//						echo "1..pic_show=$PIC_SHOW ==>".("../".$img_file)."<br>";
				} // ���੾�� set ��� �ʴ��ٻ (PIC_SHOW=1) ��
	} // end while					
} else { // end if ($piccnt > 0)
	//$img_file="";
	//$img_file=$IMG_PATH."shadow.jpg";
}

/********
			if ($PIC_SERVER_ID && $PIC_SERVER_ID > 0) {
				$cmd = " select * from OTH_SERVER where SERVER_ID=$PIC_SERVER_ID ";
				if ($db_dpis2->send_cmd($cmd)) { 
					$data2 = $db_dpis2->get_array();
					$SERVER_NAME = trim($data2[SERVER_NAME]);
					$ftp_server = trim($data2[FTP_SERVER]);
					$ftp_username = trim($data2[FTP_USERNAME]);
					$ftp_password = trim($data2[FTP_PASSWORD]);
					$main_path = trim($data2[MAIN_PATH]);
					$http_server = trim($data2[HTTP_SERVER]);
					if ($http_server) {
						//echo "1.".$http_server."/".$img_file."<br>";
						$fp = @fopen($http_server."/".$img_file, "r");
						if ($fp !== false) $img_file = $http_server."/".$img_file;
						else $img_file=$IMG_PATH."shadow.jpg";
						fclose($fp);
					} else {
//						echo "2.".$img_file."<br>";
						$img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
					}
				} else{
				 	$img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
				}
			} else{ 
				//$img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
				$img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
//				echo "../".$img_file." @@@@ ".file_exists("../".$img_file);
			}

			echo "img_file=$img_file // $PIC_SERVER_ID<br>";
			echo "<pre>";
			print_r($arr_img);
			echo "</pre>";
*********/

			$CHECK_RETIREDATE = "2013-10-01";
			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			if ($BKK_FLAG==1 && $PER_BIRTHDATE<$CHECK_RETIREDATE){
				$PER_RETIREDATE = $PER_BIRTHDATE;
			}else{
				$PER_RETIREDATE = date_adjust($PER_BIRTHDATE,'d',-1);
			} // end if
			$PER_RETIREDATE = date_adjust($PER_RETIREDATE,'y',60);
			if($PER_BIRTHDATE){
				$PER_BIRTHDATE = show_date_format(substr($PER_BIRTHDATE, 0, 10),3);
			} // end if
			
			if($PER_RETIREDATE){
				$PER_RETIREDATE = show_date_format(substr($PER_RETIREDATE, 0, 10),3);
			} // end if
			
			$PER_SALARY = $data[PER_SALARY];
			$PER_MGTSALARY = $data[PER_MGTSALARY];

			//�ѹ��è�
			$PER_STARTDATE = trim($data[PER_STARTDATE]);
			if($PER_STARTDATE){
				$PER_STARTDATE = show_date_format(substr($PER_STARTDATE, 0, 10),3);
			} // end if

			//�ѹ��������ǹ�Ҫ���
			$PER_OCCUPYDATE = trim($data[PER_OCCUPYDATE]);
			if($PER_OCCUPYDATE){
				$PER_OCCUPYDATE = show_date_format(substr($PER_OCCUPYDATE, 0, 10),3);
			} // end if
		
			// =====  ��ǹ�Ҫ��÷���è� =====
			$where = "";
			if ($BKK_FLAG==1) $where = " and POH_ORG1 = '��ا෾��ҹ��' ";
			$cmd = " select 	POH_ORG1, POH_ORG2, POH_ORG3, POH_UNDER_ORG1, POH_UNDER_ORG2  		
							from		PER_POSITIONHIS a, PER_MOVMENT b
							where	PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and MOV_SUB_TYPE=1 $where	
							order by	POH_EFFECTIVEDATE, POH_SEQ_NO ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$POH_ORG1 = trim($data_dpis2[POH_ORG1]);
			$POH_ORG2 = trim($data_dpis2[POH_ORG2]);
			if ($POH_ORG2=="�ӹѡ�ҹࢵ") $POH_ORG2 = "";
			$POH_ORG3 = trim($data_dpis2[POH_ORG3]);
			$POH_UNDER_ORG1 = trim($data_dpis2[POH_UNDER_ORG1]);
			$POH_UNDER_ORG2 = trim($data_dpis2[POH_UNDER_ORG2]);

			// =====  �����źԴ� �����ô� =====
			$cmd = "	select	PN_CODE, FML_NAME, FML_SURNAME
								from		PER_FAMILY
								where	PER_ID=$PER_ID and FML_TYPE=1 ";	
			$db_dpis2->send_cmd($cmd);
	//		$db_dpis2->show_error();
			$data1 = $db_dpis2->get_array();		
			$PN_CODE_F = trim($data1[PN_CODE]);
			$PER_FATHERNAME = $data1[FML_NAME];
			$PER_FATHERSURNAME = $data1[FML_SURNAME];
			if (!$PER_FATHERNAME) {
				$PN_CODE_F = trim($data[PN_CODE_F]);
				$PER_FATHERNAME = trim($data[PER_FATHERNAME]);
				$PER_FATHERSURNAME = trim($data[PER_FATHERSURNAME]);
			}
			$cmd = "select PN_NAME from PER_PRENAME WHERE PN_CODE = $PN_CODE_F ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PN_NAME_F=trim($data_dpis2[PN_NAME]);

			$FATHERNAME = ($PN_NAME_F)."$PER_FATHERNAME $PER_FATHERSURNAME";
		
			$cmd = "	select	PN_CODE, FML_NAME, FML_SURNAME
								from		PER_FAMILY
								where	PER_ID=$PER_ID and FML_TYPE=2 ";	
			$db_dpis2->send_cmd($cmd);
	//		$db_dpis2->show_error();
			$data1 = $db_dpis2->get_array();		
			$PN_CODE_M = trim($data1[PN_CODE]);
			$PER_MOTHERNAME = $data1[FML_NAME];
			$PER_MOTHERSURNAME = $data1[FML_SURNAME];
			if (!$PER_FATHERNAME) {
				$PN_CODE_M = trim($data[PN_CODE_M]);	
				$PER_MOTHERNAME = trim($data[PER_MOTHERNAME]);
				$PER_MOTHERSURNAME = trim($data[PER_MOTHERSURNAME]);
			}
			$cmd = "select PN_NAME from PER_PRENAME WHERE PN_CODE = $PN_CODE_M ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PN_NAME_M=trim($data_dpis2[PN_NAME]);
					
			$MOTHERNAME = ($PN_NAME_M)."$PER_MOTHERNAME $PER_MOTHERSURNAME";

			// =====  �����Ť������ =====
			$SHOW_SPOUSE = "";
			$cmd = "	select 	PN_CODE, MAH_NAME, DV_CODE, MR_CODE 		
							from		PER_MARRHIS 
							where	PER_ID=$PER_ID 	
							order by	MAH_SEQ desc ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PN_CODE = trim($data_dpis2[PN_CODE]);
			$MAH_NAME = trim($data_dpis2[MAH_NAME]);
			$DV_CODE = trim($data_dpis2[DV_CODE]);
			$MR_CODE = trim($data_dpis2[MR_CODE]);
			if (!$DV_CODE && $MR_CODE==2) {
				$cmd = "select PN_NAME from PER_PRENAME WHERE PN_CODE = $PN_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$PN_NAME=trim($data_dpis2[PN_NAME]);
					
				$SHOW_SPOUSE = $PN_NAME.$MAH_NAME;
			}

			if (!$SHOW_SPOUSE) {
				$cmd = "	select PN_NAME, FML_NAME, FML_SURNAME from PER_FAMILY	a, PER_PRENAME b
									where a.PN_CODE = b.PN_CODE(+) and PER_ID=$PER_ID and FML_TYPE = 3 and (MR_CODE is NULL or trim(MR_CODE) not in ('3','4')) ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$SHOW_SPOUSE = trim($data_dpis2[PN_NAME]).trim($data_dpis2[FML_NAME])." ".trim($data_dpis2[FML_SURNAME]);
			}

			// ����ͧ�Ҫ�
			unset($arr_DEH_SHOW);
			if($DPISDB=="odbc"){
				$cmd = " select			b.DC_NAME, b.DC_SHORTNAME, a.DEH_DATE, a.DEH_GAZETTE, a.DEH_RECEIVE_DATE
								 from			PER_DECORATEHIS a, PER_DECORATION b
								 where		a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE 
								 order by		a.DEH_DATE ";							   
			}elseif($DPISDB=="oci8"){
				$cmd = " select			b.DC_NAME, b.DC_SHORTNAME, a.DEH_DATE, a.DEH_GAZETTE, a.DEH_RECEIVE_DATE
								 from			PER_DECORATEHIS a, PER_DECORATION b
								 where		a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE 
								 order by		a.DEH_DATE ";							   
			}elseif($DPISDB=="mysql"){
				$cmd = " select			b.DC_NAME, b.DC_SHORTNAME, a.DEH_DATE, a.DEH_GAZETTE, a.DEH_RECEIVE_DATE
								 from			PER_DECORATEHIS a, PER_DECORATION b
								 where		a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE 
								 order by		a.DEH_DATE ";	
			} // end if
//								 where		a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE and DC_TYPE != 3
			$count_decoratehis = $db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			if($count_decoratehis){
				$decoratehis_count = 0;
				$DEH_SHOW = "";
				$arr_DEH_SHOW = (array) null;
				while($data2 = $db_dpis2->get_array()){
					$decoratehis_count++;
					$DC_NAME = trim($data2[DC_NAME]);
					$DC_SHORTNAME = trim($data2[DC_SHORTNAME]);
					if ($DC_SHORTNAME) $DC_NAME .= " (".$DC_SHORTNAME.")";
					$DEH_DATE = trim($data2[DEH_DATE]);
					if($DEH_DATE){
						$DEH_DATE = substr($DEH_DATE, 0, 10);
						$arr_temp = explode("-", $DEH_DATE);
						$DEH_YEAR1 = ($arr_temp[0] + 543);
						$DEH_DATE1 = show_date_format($DEH_DATE,3);
					} // end if	
					$DEH_GAZETTE = trim($data2[DEH_GAZETTE]);

					if($DEH_GAZETTE) 
						if (strpos($DEH_GAZETTE,"�Ҫ�Ԩ�ҹ�ມ��") === false)
							$DEH_GAZETTE = "�Ҫ�Ԩ�ҹ�ມ�� $DEH_GAZETTE";
					$DEH_RECEIVE_DATE = trim($data2[DEH_RECEIVE_DATE]);
					if($DEH_RECEIVE_DATE) $DEH_RECEIVE_DATE = "ŧ�ѹ��� ".show_date_format($DEH_RECEIVE_DATE,2);
					if ($DEH_SHOW) {
						$DEH_SHOW = "$DEH_SHOW,  �� $DEH_YEAR1 $DC_NAME";
//						$DEH_SHOW1 = "$DEH_SHOW1, $DC_NAME �Ѻ����Ҫ�ҹ����� $DEH_DATE1 ";
					} else {
						$DEH_SHOW = "�� $DEH_YEAR1 $DC_NAME";
//						$DEH_SHOW1 = "$DC_NAME �Ѻ����Ҫ�ҹ����� $DEH_DATE1 ";
					}
					if ($BKK_FLAG==1)
						$arr_DEH_SHOW[] = "$DEH_DATE1 $DC_NAME \n$DEH_GAZETTE $DEH_RECEIVE_DATE";
					else
						$arr_DEH_SHOW[] = "$DC_NAME �Ѻ����Ҫ�ҹ����� $DEH_DATE1 \n$DEH_GAZETTE $DEH_RECEIVE_DATE";
				} // end while
			} // end if ($count_decoratehis)
			//------------------------------------------------------------------------------------------

			//����Ѻ ��. 7
			$ORG_NAME_1 = "";		$ORG_NAME_2 = "";
			$POH_EFFECTIVEDATE = "";	$PL_NAME = "";	$TMP_PL_NAME = "";
			$LEVEL_NO = "";	$POH_POS_NO = "";	$POH_SALARY = "";
			$SAH_EFFECTIVEDATE = "";	$SAH_SALARY = "";	$MOV_NAME = "";

			$RTF->set_table_font($font, 14);
			$RTF->color("0");	// 0=BLACK
			
			$RTF->open_line();
			$RTF->set_font($font,25);
			$RTF->cell($RTF->color("0").$RTF->bold(1)."11. ������Ѻ�ɷҧ�Թ�� ".$RTF->bold(0), "$page_ch", "center", "16", "TRBL");
			$RTF->close_line();

			print_header("PUNISHMENT");

			$CR_NAME = "";
			$CRD_NAME = "";
			$PUN_STARTDATE = "";
			$PUN_ENDDATE = "";							
							
			if($DPISDB=="odbc"){
				$cmd = " select			c.CR_NAME, b.CRD_NAME, a.PUN_STARTDATE, a.PUN_ENDDATE
								 from			PER_PUNISHMENT a, PER_CRIME_DTL b, PER_CRIME c
								 where		a.PER_ID=$PER_ID and
													a.CRD_CODE=b.CRD_CODE and
													b.CR_CODE=c.CR_CODE
								 order by		a.PUN_STARTDATE ";							   
			}elseif($DPISDB=="oci8"){
				$cmd = " select			c.CR_NAME, b.CRD_NAME, a.PUN_STARTDATE, a.PUN_ENDDATE
								 from			PER_PUNISHMENT a, PER_CRIME_DTL b, PER_CRIME c
								 where		a.PER_ID=$PER_ID and
													a.CRD_CODE=b.CRD_CODE and
													b.CR_CODE=c.CR_CODE
								 order by		a.PUN_STARTDATE ";							   
			}elseif($DPISDB=="mysql"){
				$cmd = " select			c.CR_NAME, b.CRD_NAME, a.PUN_STARTDATE, a.PUN_ENDDATE, PUN_NO, PUN_REF_NO
								 from			PER_PUNISHMENT a, PER_CRIME_DTL b, PER_CRIME c
								 where		a.PER_ID=$PER_ID and
													a.CRD_CODE=b.CRD_CODE and
													b.CR_CODE=c.CR_CODE
								 order by		a.PUN_STARTDATE ";		
			} // end if
			$count_punishmenthis = $db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			if ($count_punishmenthis) {
				$punishmenthis_count = 0;
				while($data2 = $db_dpis2->get_array()){
					$punishmenthis_count++;
					$CR_NAME = trim($data2[CR_NAME]);
					$CRD_NAME = trim($data2[CRD_NAME]);
					$PUN_STARTDATE = show_date_format($data2[PUN_STARTDATE],3);
					$PUN_ENDDATE = show_date_format($data2[PUN_ENDDATE],3);
					$PUN_DURATION = "$PUN_STARTDATE - $PUN_ENDDATE";
					if($PUN_STARTDATE == $PUN_ENDDATE) $PUN_DURATION = "$PUN_STARTDATE";

					$PUN_NO = trim($data2[PUN_NO]);
					$PUN_REF_NO = trim($data2[PUN_REF_NO]);
					$PUN_REF = ($PUN_REF_NO ? $PUN_REF_NO : $PUN_NO);
	
					$RTF->open_line();	
					$RTF->set_font($font,14);
					$RTF->color("0");	// 0=BLACK
					$RTF->cell("$PUN_DURATION", $heading_width[PUNISHMENT][0], "left", "8", "TRBL");
					$RTF->cell("$CRD_NAME", $heading_width[PUNISHMENT][1], "left", "8", "TRBL");
					$RTF->cell("$PUN_REF", $heading_width[PUNISHMENT][2], "left", "8", "TRBL");
					$RTF->close_line();
				} // end while
			} else {
				$RTF->open_line();	
				$RTF->set_font($font,14);
				$RTF->color("0");	// 0=BLACK
				$RTF->cell("", $heading_width[PUNISHMENT][0], "left", "8", "TRBL");
				$RTF->cell("", $heading_width[PUNISHMENT][1], "left", "8", "TRBL");
				$RTF->cell("", $heading_width[PUNISHMENT][2], "left", "8", "TRBL");
				$RTF->close_line();

				$RTF->open_line();	
				$RTF->set_font($font,14);
				$RTF->color("0");	// 0=BLACK
				$RTF->cell("", $heading_width[PUNISHMENT][0], "left", "8", "TRBL");
				$RTF->cell("", $heading_width[PUNISHMENT][1], "left", "8", "TRBL");
				$RTF->cell("", $heading_width[PUNISHMENT][2], "left", "8", "TRBL");
				$RTF->close_line();
			}

			$RTF->open_line();
			$RTF->set_font($font,14);
			$RTF->cell($RTF->color("0").$RTF->bold(1)."12. �ѹ���������Ѻ�Թ��͹ �����ѹ������Ѻ�Թ��͹������ �����ѹ��������Шӻ�Ժѵ�˹�ҷ�������ࢵ������ջ�С���顮��¡���֡".$RTF->bold(0), "$page_ch", "center", "16", "TRBL");
			$RTF->close_line();

			print_header("NOTPAID");
			
			$RTF->open_line();
			$RTF->set_font($font,14);
			$RTF->cell($RTF->color("0").$RTF->bold(1)."".$RTF->bold(0), $heading_width[NOTPAID][0], "center", "8", "LTBR");
			$RTF->cell($RTF->color("0").$RTF->bold(1)."".$RTF->bold(0), $heading_width[NOTPAID][1], "center", "8", "LTBR");
			$RTF->cell($RTF->color("0").$RTF->bold(1).$SESS_USERID.$RTF->bold(0), $heading_width[NOTPAID][2], "center", "8", "TRBL");
			$RTF->close_line();
			$RTF->open_line();
			$RTF->set_font($font,14);
			$RTF->cell($RTF->color("0").$RTF->bold(1)."".$RTF->bold(0), $heading_width[NOTPAID][0], "center", "8", "LTBR");
			$RTF->cell($RTF->color("0").$RTF->bold(1)."".$RTF->bold(0), $heading_width[NOTPAID][1], "center", "8", "LTBR");
			$RTF->cell($RTF->color("0").$RTF->bold(1)."".$RTF->bold(0), $heading_width[NOTPAID][2], "center", "8", "LTBR");
			$RTF->close_line();

			$RTF->open_line();
			$RTF->set_font($font,14);
			$RTF->cell($RTF->color("0").$RTF->bold(1).($MINISTRY_NAME?$MINISTRY_NAME:"").$RTF->bold(0), 32, "left", "8", "LTB");
			$RTF->cell($RTF->color("0").$RTF->bold(1).($DEPARTMENT_NAME?$DEPARTMENT_NAME:"").$RTF->bold(0), 34, "left", "8", "TB");
			$RTF->cell($RTF->color("0").$RTF->bold(1)."$KP7_TITLE".$RTF->bold(0), 32, "right", "8", "TBR");
			$RTF->close_line();

			// �ӹǹ���� fix ��Ҵ�ͧ�Ҿ
			$fix_width = 100;
			if ($img_file) {
				list($width, $height) = getimagesize($img_file); 
				$new_h = $height / $width * $fix_width;
				$new_ratio = floor(100 / $height * $fix_width); 
			}
			  
			$RTF->open_line();
			$RTF->set_font($font,14);
			$RTF->color("1");	// 0=DARKGRAY
			$RTF->cell("1. ����  ".$FULLNAME, "32", "left", "8", "TRBL");
			$RTF->cell("5. ���ͺԴ�  ".($FATHERNAME?$FATHERNAME:"-"), "34", "left", "8", "TRBL");
			$RTF->cellImage(($img_file ? $img_file : ""), "$new_ratio", "32", "center", "8", "LTR");
			$RTF->close_line();

			$RTF->open_line();
			$RTF->set_font($font,14);
			$RTF->color("1");	// 0=DARKGRAY
			$RTF->cell("2. �ѹ ��͹ �� �Դ  ".$PER_BIRTHDATE, "32", "left", "8", "TRBL");
			$RTF->cell("6. ������ô�  ".($MOTHERNAME?$MOTHERNAME:"-"), "34", "left", "8", "TRBL");
			$RTF->cell("", "32", "left", "8", "RL");
			$RTF->close_line();

			$RTF->open_line();
			$RTF->set_font($font,14);
			$RTF->color("1");	// 0=DARKGRAY
			$RTF->cell("3. �ѹ�ú���³���� ".$PER_RETIREDATE, "32", "left", "8", "TRBL");
			$RTF->cell("7. �ѹ��觺�è�  ".$PER_STARTDATE, "34", "left", "8", "TRBL");
			$RTF->cell("", "32", "left", "8", "RL");
			$RTF->close_line();

			$RTF->open_line();
			$RTF->set_font($font,14);
			$RTF->color("1");	// 0=DARKGRAY
			$RTF->cell("4. ���ͤ������ ".($SHOW_SPOUSE?$SHOW_SPOUSE:"-"), "32", "left", "8", "TRBL");
			$RTF->cell("8. �ѹ�������Ժѵ��Ҫ���  ".$PER_STARTDATE, "34", "left", "8", "TRBL");
			$RTF->cell("", "32", "left", "8", "RL");
			$RTF->close_line();

			$RTF->open_line();
			$RTF->set_font($font,14);
			$RTF->color("1");	// 0=DARKGRAY
			$RTF->cell("    �������� ".($PV_NAME?$PV_NAME:"-"), "32", "left", "8", "TRBL");
			$RTF->cell("9. ����������Ҫ��� ".$OT_NAME, "34", "left", "8", "TRBL");
			$RTF->cell("", "32", "left", "8", "LBR");
			$RTF->close_line();

			$RTF->open_line();
			$RTF->set_font($font,14);
			$RTF->color("1");	// 0=DARKGRAY
			$RTF->cell("����ͧ�Ҫ��������ó�", "66", "center", "8", "TRBL");
			$RTF->cell("�Ţ��Шӵ�ǻ�ЪҪ� : ".card_no_format($PER_CARDNO,1), "32", "left", "8", "TRBL");
			$RTF->close_line();

			$RTF->open_line();
			$RTF->set_font($font,14);
			$RTF->color("1");	// 0=DARKGRAY
			$RTF->cell($DEH_SHOW, "66", "center", "8", "TRBL");
			$RTF->cell("�Ţ��Шӵ�Ǣ���Ҫ��� : $PER_OFFNO", "32", "left", "8", "TRBL");
			$RTF->close_line();

			//����Ѻ ��. 7
			$ORG_NAME_1 = "";		$ORG_NAME_2 = "";
			$POH_EFFECTIVEDATE = "";	$PL_NAME = "";	$TMP_PL_NAME = "";
			$LEVEL_NO = "";	$POH_POS_NO = "";	$POH_SALARY = "";
			$SAH_EFFECTIVEDATE = "";	$SAH_SALARY = "";	$MOV_NAME = "";

			for($history_index=0; $history_index<count($arr_history_sort); $history_index++){
				if (in_array($arr_history_sort[$history_index],$arr_history_name)) {
					$HISTORY_NAME = $arr_history_sort[$history_index];
				} else {
					$HISTORY_NAME = "";
				}
				switch($HISTORY_NAME){
					case "EDUCATE" :
					// ੾�� EDUCATE ��� TRAINING
						$RTF->open_line();
						$RTF->set_font($font,14);
						$RTF->color("1");	// 0=DARKGRAY
						$RTF->cell($RTF->bold(1)."10. ����ѵԡ���֡�� �֡ͺ����д٧ҹ".$RTF->bold(0), "$page_ch", "center", "8", "TRBL");
						$RTF->close_line();

						$RTF->open_line();
						$RTF->set_font($font,14);
						$RTF->color("1");	// 0=DARKGRAY
						$RTF->cell("����ѵԡ���֡��", "46", "center", "8", "TRBL");
						$RTF->cell("����ѵԡ�ý֡ͺ����д٧ҹ", "52", "center", "8", "TRBL");
						$RTF->close_line();

						print_header($HISTORY_NAME);
							
						$EDU_PERIOD="";
						$EN_NAME = "";
						$EM_NAME = "";
						$INS_NAME = "";
						$CT_NAME = "";

						if($DPISDB=="odbc"){
							$cmd = " 	select		a.EDU_STARTYEAR, a.EDU_ENDYEAR, a.EDU_INSTITUTE, b.EN_NAME, c.EM_NAME, d.INS_NAME, EDU_HONOR
										from			((PER_EDUCATE a
													left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
													) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
										 where		a.PER_ID=$PER_ID
									 order by		iif(isnull(a.EDU_SEQ),0,a.EDU_SEQ), a.EDU_STARTYEAR, a.EDU_ENDYEAR ";							   
						}elseif($DPISDB=="oci8"){
							$cmd = " select		a.EDU_STARTYEAR, a.EDU_ENDYEAR, a.EDU_INSTITUTE, b.EN_NAME, c.EM_NAME, d.INS_NAME, EDU_HONOR
									from			PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d
									where		a.PER_ID=$PER_ID and a.EN_CODE=b.EN_CODE(+) and 
												a.EM_CODE=c.EM_CODE(+) and a.INS_CODE=d.INS_CODE(+)
									order by		to_number(nvl(a.EDU_SEQ,0)), a.EDU_STARTYEAR, a.EDU_ENDYEAR ";							   
						}elseif($DPISDB=="mysql"){
							$cmd = " 	select		a.EDU_STARTYEAR, a.EDU_ENDYEAR, a.EDU_INSTITUTE, b.EN_NAME, c.EM_NAME, d.INS_NAME, EDU_HONOR
									from			((PER_EDUCATE a
												left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
												) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
									 where		a.PER_ID=$PER_ID
									 order by		a.EDU_SEQ+0, a.EDU_STARTYEAR, a.EDU_ENDYEAR ";			
						} // end if
						$count_educatehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
						$arr_educate = (array) null;
						$row_count = 0;	$edu_count = 0;
						while($data2 = $db_dpis2->get_array()){
							$EDU_STARTYEAR = trim($data2[EDU_STARTYEAR]);
							$EDU_ENDYEAR =  trim($data2[EDU_ENDYEAR]);
							if($EDU_STARTYEAR && $EDU_STARTYEAR != "-" && $EDU_ENDYEAR){
								$EDU_PERIOD = "$EDU_STARTYEAR - $EDU_ENDYEAR";
							}else{
								$EDU_PERIOD = "$EDU_ENDYEAR";
							}
							$arr_educate[$row_count][edu_period] = $EDU_PERIOD;

							$EN_NAME = trim($data2[EN_NAME]);
							$EM_NAME = trim($data2[EM_NAME]);
							if($EM_NAME!=""){ $EM_NAME="($EM_NAME)"; }
							$INS_NAME = trim($data2[INS_NAME]);
							if (!$INS_NAME) $INS_NAME = trim($data2[EDU_INSTITUTE]);
							$EDU_HONOR = trim($data2[EDU_HONOR]);
							if ($EDU_HONOR) {
								if (strpos($EDU_HONOR,"���õԹ���") === false)
									$EN_NAME .= " ���õԹ���$EDU_HONOR";
							}
							$arr_educate[$row_count][en_name] = $EN_NAME;
							$arr_educate[$row_count][em_name] = $EM_NAME;
							$arr_educate[$row_count][ins_name] = $INS_NAME;
							$row_count++;
							$edu_count++;
						} // end while $data2 for EDUCATION
						
						$TRN_DURATION = "";
						$TRN_STARTDATE = "";
						$TRN_ENDDATE = "";
						$TR_NAME = "";
						$TRN_PLACE = "";
						
						if($DPISDB=="odbc"){
							$cmd = " select		a.TRN_STARTDATE, a.TRN_ENDDATE,a.TRN_ORG, b.TR_NAME, a.TRN_PLACE, a. TRN_FUND, a.TRN_NO, 
																a.TRN_COURSE_NAME, a.TRN_DOCNO, a.TRN_DOCDATE
											 from			PER_TRAINING a, PER_TRAIN b
											 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE
											 order by	a.TRN_STARTDATE ";							   
						}elseif($DPISDB=="oci8"){
							$cmd = " select		a.TRN_STARTDATE, a.TRN_ENDDATE, a.TRN_ORG, b.TR_NAME, a.TRN_PLACE , a. TRN_FUND, a.TRN_NO, 
																a.TRN_COURSE_NAME, a.TRN_DOCNO, a.TRN_DOCDATE
											 from			PER_TRAINING a, PER_TRAIN b
											 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE(+)
											 order by	a.TRN_STARTDATE ";							   
						}elseif($DPISDB=="mysql"){
							$cmd = " select		a.TRN_STARTDATE, a.TRN_ENDDATE, a.TRN_ORG, b.TR_NAME, a.TRN_PLACE, a. TRN_FUND, a.TRN_NO, 
																a.TRN_COURSE_NAME, a.TRN_DOCNO, a.TRN_DOCDATE
											 from			PER_TRAINING a, PER_TRAIN b
											 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE
											 order by	a.TRN_STARTDATE ";		
						} // end if
						$count_traininghis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
						$arr_training = (array) null;
						$row_count = 0;	$trn_count = 0;
						while($data2 = $db_dpis2->get_array()){
							$TRN_STARTDATE = show_date_format($data2[TRN_STARTDATE],2);
							$TRN_ENDDATE = show_date_format($data2[TRN_ENDDATE],2);
							if(trim($TRN_STARTDATE) && trim($TRN_ENDDATE)){
								if ($BKK_FLAG==1) { // ���
									$TRN_DURATION = $TRN_STARTDATE." - ".$TRN_ENDDATE;
								} else {
									$TRN_DURATION = $TRN_STARTDATE." - "."\n".$TRN_ENDDATE;
								}
								if($TRN_STARTDATE == $TRN_ENDDATE) $TRN_DURATION = "$TRN_STARTDATE";
							} elseif(trim($TRN_STARTDATE)) {
								$TRN_DURATION = $TRN_STARTDATE;
							} elseif(trim($TRN_ENDDATE)) {
								$TRN_DURATION = $TRN_ENDDATE;
							}
							$arr_training[$row_count][trn_duration]=$TRN_DURATION;
							$TRN_ORG = trim($data2[TRN_ORG]);
							$TR_NAME = trim($data2[TR_NAME]);				
							if (!$TR_NAME || $TR_NAME=="����") $TR_NAME = trim($data2[TRN_COURSE_NAME]);
							if($TR_NAME!=""){	$TR_NAME = str_replace("&quot;",'"',$TR_NAME);		}
							$TRN_PLACE = trim($data2[TRN_PLACE]);
							$TRN_FUND = trim($data2[TRN_FUND]);
							$TRN_NO = trim($data2[TRN_NO]);
							if($TRN_NO && $TR_NAME) $TR_NAME .= " ��蹷�� $TRN_NO";
							$TRN_DOCNO = trim($data2[TRN_DOCNO]);
							$TRN_DOCDATE = show_date_format($data2[TRN_DOCDATE],2);
							if ($TRN_DOCNO && $TRN_DOCDATE) {
								$TR_NAME .= "\n����� ".$TRN_DOCNO." ��. ".$TRN_DOCDATE;
							}
							$arr_training[$row_count][tr_name] = $TR_NAME;
							$arr_training[$row_count][trn_org] = $TRN_ORG;
							$arr_training[$row_count][trn_place] = $TRN_PLACE;
							$arr_training[$row_count][trn_fund] = $TRN_FUND;
							$row_count++;
							$trn_count++;
						} // end while data2 for TRAINING

						for($row_count=0; $row_count < count($arr_content); $row_count++) {

							$border = "TRBL";
							
							$RTF->open_line();
							$RTF->set_font($font,14);
							$RTF->color("1");	// 0=DARKGRAY
							$RTF->cell($arr_content[$row_count][ins_name], $heading_width[$HISTORY_NAME][0], "left", "8", $border);
							$RTF->cell($arr_content[$row_count][edu_period], $heading_width[$HISTORY_NAME][1], "center", "8", $border);
							$RTF->cell($arr_content[$row_count][en_name]."  ".$arr_content[$row_count][em_name], $heading_width[$HISTORY_NAME][2], "left", "8", $border);
							// ����� TRN
							$RTF->cell($arr_content[$row_count][trn_place], $heading_width[$HISTORY_NAME][3], "left", "8", $border);
							$RTF->cell($arr_content[$row_count][trn_duration], $heading_width[$HISTORY_NAME][4], "center", "8", $border);
							$RTF->cell($arr_content[$row_count][tr_name], $heading_width[$HISTORY_NAME][5], "left", "8", $border);
							$RTF->close_line();
						} // end for loop $row_count
							
						// ����� ��ǹ��������������˹��
						//*******************************
						//*******************************
						break;
					case "POSITIONHIS" : //�������ѵ��Ѻ�Ҫ��� + ����͹����Թ��͹��Ҵ��¡ѹ
							$page_no = 1;
							
							$border = "TRBL";
							
							$RTF->new_page();
//							$RTF->add_text("", "left");
		
							$RTF->open_line();
							$RTF->set_font($font,14);
							$RTF->color("1");	// 0=DARKGRAY
							$RTF->cell($RTF->bold(1)."13. ���˹�����ѵ���Թ��͹".$RTF->bold(0), "$page_ch", "center", "8", $border);
							$RTF->close_line();

							print_header($HISTORY_NAME);
							//########################
							//����ѵԡ�ô�ç���˹觢���Ҫ���
							//########################
							$ARR_POSITIONHIS = (array) null;
							if($DPISDB=="odbc"){
								$cmd = " select	a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE,	a.MOV_CODE, 
																d.PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_LEVEL, g.LEVEL_SHORTNAME, a.POH_POS_NO_NAME, a.POH_POS_NO, 
																a.POH_SALARY, a.POH_DOCNO, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME, POH_SPECIALIST, 
																a.POH_DOCDATE, a.UPDATE_USER, a.POH_PL_NAME, a.POH_PM_NAME, a.POH_ORG, a.POH_SEQ_NO, a.POH_ENDDATE, 
																POH_DOCNO_EDIT, POH_DOCDATE_EDIT, POH_REF_DOC, POH_ACTH_SEQ
												   from			(
																		(
																			(
																			PER_POSITIONHIS a
																			left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
																		) left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
																	) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
																) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
												   where		a.PER_ID=$PER_ID
												   order by		a.POH_EFFECTIVEDATE, a.POH_SALARY, a.POH_ENDDATE ";							   
							}elseif($DPISDB=="oci8"){
							 	$cmd = "select	a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE,	a.MOV_CODE, 
																d.PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_LEVEL, g.LEVEL_SHORTNAME, a.POH_POS_NO_NAME, a.POH_POS_NO, 
																a.POH_SALARY, a.POH_DOCNO, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME, POH_SPECIALIST, 
																a.POH_DOCDATE,	a.UPDATE_USER, a.POH_PL_NAME, a.POH_PM_NAME, a.POH_ORG, a.POH_SEQ_NO, a.POH_ENDDATE, 
																POH_DOCNO_EDIT, POH_DOCDATE_EDIT, POH_REF_DOC, POH_ACTH_SEQ
												from		PER_POSITIONHIS a, PER_LINE d, PER_TYPE e, PER_MGT f, PER_LEVEL g
												where	a.PER_ID=$PER_ID and a.PL_CODE=d.PL_CODE(+) and
																a.PT_CODE=e.PT_CODE(+) and a.PM_CODE=f.PM_CODE(+) and 
																a.LEVEL_NO=g.LEVEL_NO(+)
												order by	a.POH_EFFECTIVEDATE, a.POH_SALARY, a.POH_ENDDATE ";
							}elseif($DPISDB=="mysql"){
								$cmd = "  select a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE,	a.MOV_CODE, 
																d.PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_LEVEL, g.LEVEL_SHORTNAME, a.POH_POS_NO_NAME, a.POH_POS_NO, 
																a.POH_SALARY, a.POH_DOCNO, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME, POH_SPECIALIST, 
																a.POH_DOCDATE, a.UPDATE_USER, a.POH_PL_NAME, a.POH_PM_NAME, a.POH_ORG, a.POH_SEQ_NO, a.POH_ENDDATE, 
																POH_DOCNO_EDIT, POH_DOCDATE_EDIT, POH_REF_DOC, POH_ACTH_SEQ
												  from	(
																(
																	(
																		PER_POSITIONHIS a left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
																	) 	left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
																) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
															) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
												   where	a.PER_ID=$PER_ID
												   order by		a.POH_EFFECTIVEDATE, a.POH_SALARY, a.POH_ENDDATE ";
							} // end if
							$count_positionhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_positionhis){
								$last_org = "";
								$positionhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$positionhis_count++;
									$POH_EFFECTIVEDATE = trim(substr($data2[POH_EFFECTIVEDATE],0,10));
									$POH_EFFECTIVEDATE = ($POH_EFFECTIVEDATE?$POH_EFFECTIVEDATE:"-");
									$POH_ENDDATE = trim(substr($data2[POH_ENDDATE],0,10));
									$LEVEL_NO = trim($data2[LEVEL_NO]);
									if ($BKK_FLAG==1)
										$POSITION_LEVEL = trim($data2[LEVEL_SHORTNAME]);
									else
										$POSITION_LEVEL = trim($data2[POSITION_LEVEL]);
									if ($POSITION_LEVEL=="�ӹҭ��þ����") {
										$POSITION_LEVEL = "�ӹҭ���"."\n"."�����";
//										$testpos = str_replace("\n","<BR>",$POSITION_LEVEL);
//										echo "POSITION_LEVEL=$testpos<BR>";
									}
									$POH_SEQ_NO = trim($data2[POH_SEQ_NO]);
									$POH_ACTH_SEQ = trim($data2[POH_ACTH_SEQ]);
//									if( ("$PM_CODE $PL_NAME $LEVEL_NO $POH_POS_NO") != (trim($data2[PM_CODE])." ".trim($data2[PL_NAME])." ".$LEVEL_NO." ".trim($data2[POH_POS_NO]))){
									$PL_NAME = trim($data2[PL_NAME]);
									$LEVEL_NAME = trim(str_replace("�дѺ","",$data2[LEVEL_NAME]));
									if ($POSITION_LEVEL=="1" || $POSITION_LEVEL=="2" || $POSITION_LEVEL=="3" || $POSITION_LEVEL=="4" || $POSITION_LEVEL=="5" || 
										$POSITION_LEVEL=="6" || $POSITION_LEVEL=="7" || $POSITION_LEVEL=="8" || $POSITION_LEVEL=="9" || $POSITION_LEVEL=="10" ||	
										$POSITION_LEVEL=="11")
										$footer_level = trim($data2[LEVEL_NAME]);
									else
										$footer_level = trim($data2[POSITION_LEVEL]);
									$PT_CODE = trim($data2[PT_CODE]);
									$PT_NAME = trim($data2[PT_NAME]);
									$PM_CODE = trim($data2[PM_CODE]);
									$PM_NAME = trim($data2[PM_NAME]);
									$POH_PL_NAME = trim($data2[POH_PL_NAME]);
									$POH_SPECIALIST = trim($data2[POH_SPECIALIST]);
									$POH_DOCNO_EDIT = trim($data2[POH_DOCNO_EDIT]);
									$POH_DOCDATE_EDIT = trim($data2[POH_DOCDATE_EDIT]);
									$POH_REF_DOC = trim($data2[POH_REF_DOC]);
//										if ($LEVEL_NO >= "1" && $LEVEL_NO <= "9") $POH_PL_NAME = $POH_PL_NAME." ".level_no_format($LEVEL_NO);
									$arr_temp = "";
									if (strlen($POH_PL_NAME) > 50) {
										$arr_temp = explode(" ", $POH_PL_NAME);
										if ((strlen($arr_temp[0])+strlen($arr_temp[1])+strlen($arr_temp[2])) < 50) 
											$POH_PL_NAME = $arr_temp[0]." ".$arr_temp[1]." ".$arr_temp[2]."\n".$arr_temp[3]." ".$arr_temp[4]." ".$arr_temp[5]." ".$arr_temp[6]." ".$arr_temp[7];
										elseif ((strlen($arr_temp[0])+strlen($arr_temp[1])) < 50) 
											$POH_PL_NAME = $arr_temp[0]." ".$arr_temp[1]."\n".$arr_temp[2]." ".$arr_temp[3]." ".$arr_temp[4]." ".$arr_temp[5]." ".$arr_temp[6]." ".$arr_temp[7];
										else
											$POH_PL_NAME = $arr_temp[0]."\n".$arr_temp[1]." ".$arr_temp[2]." ".$arr_temp[3]." ".$arr_temp[4]." ".$arr_temp[5]." ".$arr_temp[6]." ".$arr_temp[7];
									}
									$POH_PM_NAME = trim($data2[POH_PM_NAME]);
									if ($POH_PM_NAME) {
										if ($BKK_FLAG==1 && $POH_EFFECTIVEDATE < $PT_DATE) 
											$POH_PL_NAME = $POH_PL_NAME."\n".$POH_PM_NAME;
										else
											$POH_PL_NAME = $POH_PM_NAME."\n".$POH_PL_NAME;
									}
									if ($POH_SPECIALIST) $POH_PL_NAME .= " ($POH_SPECIALIST)";
									$POH_ORG = trim($data2[POH_ORG]);
									$MOV_CODE = trim($data2[MOV_CODE]);
									//�һ������������͹��Ǣͧ����ѵԡ�ô�ç���˹觢���Ҫ���
									$cmd = " select MOV_NAME, MOV_SUB_TYPE from PER_MOVMENT where MOV_CODE='$MOV_CODE' ";
									$db_dpis3->send_cmd($cmd);
									//echo "<br>$cmd<br>";
									//$db_dpis3->show_error();
									$data3 = $db_dpis3->get_array();
									$MOV_NAME = $data3[MOV_NAME];
									$MOV_SUB_TYPE = $data3[MOV_SUB_TYPE];
									if ($MOV_SUB_TYPE==9) {
										$POH_POS_NO = $POSITION_LEVEL = $POH_SALARY = "";
									}
									
									if ($BKK_FLAG==1 && (substr($MOV_NAME,0,12) == "���ͧ��Ժѵ�" || substr($MOV_NAME,0,5) == "��è�"))
										$POH_ORG .= " (���ͧ��Ժѵ�˹�ҷ���Ҫ���)"; 
									if ($BKK_FLAG==1 && (substr($MOV_NAME,0,15) == "�鹷��ͧ��Ժѵ�" || substr($MOV_NAME,0,14) == "�鹨ҡ��÷��ͧ")) {
										$POH_ORG .= " (�鹨ҡ��÷��ͧ��Ժѵ�˹�ҷ���Ҫ���)"; 
										$MOV_NAME = "";
									}
									if ($BKK_FLAG==1 && substr($MOV_NAME,0,11) == "���鹷��ͧ") {
										$POH_ORG .= " (����ҹ��÷��ͧ��Ժѵ�˹�ҷ���Ҫ���)"; 
										$MOV_NAME = "";
									}
									if ($BKK_FLAG==1 && (substr($MOV_NAME,0,8) == "�ѡ�ҡ��" || substr($MOV_NAME,0,14) == "�ѡ���Ҫ���᷹")) {
										$POH_PL_NAME = $MOV_NAME.$POH_PL_NAME; 
										if (substr($MOV_NAME,0,8) == "�ѡ�ҡ��" && $POH_ACTH_SEQ) $POH_ORG .= " (�ӴѺ��� $POH_ACTH_SEQ)";
										$MOV_NAME = "";
									}
									if ($BKK_FLAG==1 && ($MOV_NAME == "��ê����Ҫ���" || $MOV_NAME == "�����Ҫ���")) {
										$POH_ORG = $MOV_NAME.$POH_ORG; 
										if ($POH_ENDDATE) $POH_PL_NAME = "�֧ ".show_date_format($POH_ENDDATE,2);
										$MOV_NAME = "";
									}
									if ($BKK_FLAG==1 && (strpos($MOV_NAME,"���")!==false || strpos($MOV_NAME,"¡��ԡ")!==false)) {
										if ($POH_DOCNO_EDIT) {
											if(trim($POH_DOCDATE_EDIT)){
												$POH_DOCDATE_EDIT = " ��. ".show_date_format(substr($POH_DOCDATE_EDIT, 0, 10),2);
												$POH_PL_NAME = $MOV_NAME.' '.$POH_DOCNO_EDIT.$POH_DOCDATE_EDIT.' '.$POH_PL_NAME;
											} else {
												$POH_PL_NAME = $MOV_NAME.' '.$POH_DOCNO_EDIT.' '.$POH_PL_NAME;
											}
										} else {
											$POH_PL_NAME = $MOV_NAME.' '.$POH_PL_NAME;
										}
										$MOV_NAME = "";
									}
									$arr_temp = "";
									if (strlen($POH_ORG) > 50) {
										$arr_temp = explode(" ", $POH_ORG);
//										if (strpos($POH_ORG,"�ӴѺ���")!==false) echo "1..POH_ORG=$POH_ORG<br>";
//										$POH_ORG = $arr_temp[0]."\n".$arr_temp[1]." ".$arr_temp[2]." ".$arr_temp[3]." ".$arr_temp[4]." ".$arr_temp[5];
										$POH_ORG = $arr_temp[0]."\n".$arr_temp[1];
										if (count($arr_temp) > 2)
											for($iii = 2; $iii < count($arr_temp); $iii++)	$POH_ORG .= " ".$arr_temp[$iii];
//										if (strpos($POH_ORG,"�ӴѺ���")!==false) echo "2..POH_ORG=$POH_ORG<br>";
									}
//										if ($footer_level) $footer_level .= " ".$POH_ORG;
//										echo "footer_level=$footer_level<br>";
									$TMP_PL_NAME = $POH_PL_NAME."\n".$POH_ORG;
//									}
									if ($POH_ORG) $last_org = $POH_ORG;
//									echo "PER_ID=$PER_ID, POH_ORG=$POH_ORG, last_org=$last_org<br>";
									$TMP_PL_NAME = (trim($TMP_PL_NAME)?trim($TMP_PL_NAME):"-");
									$POH_POS_NO_NAME = trim($data2[POH_POS_NO_NAME]);
									if (substr($POH_POS_NO_NAME,0,4)=="���.")
										$POH_POS_NO = $POH_POS_NO_NAME." ".trim($data2[POH_POS_NO]);
									else
										$POH_POS_NO = $POH_POS_NO_NAME.trim($data2[POH_POS_NO]);	
									$POH_POS_NO = ($POH_POS_NO?$POH_POS_NO:"-");
									$POH_SALARY = $data2[POH_SALARY];
//									$POH_DOCNO = (trim($data2[POH_DOCNO]))? $data2[POH_DOCNO] : "-" ;
									$POH_DOCNO = trim($data2[POH_DOCNO]);
									
									$POH_DOCDATE = "";
									if(trim($data2[POH_DOCNO]) && trim($data2[POH_DOCNO]) != "-"){
										if(trim($data2[POH_DOCDATE])){
											$POH_DOCDATE = "��. ".show_date_format(substr($data2[POH_DOCDATE], 0, 10),2);
										}
										$USRNAME = "";
										if($data2[UPDATE_USER]){
											//�֧���ͨҡ ���ҧ user_detail �ͧ mysql
											$cmd1 ="select fullname from user_detail where id=$data2[UPDATE_USER]";
											$db->send_cmd($cmd1);
											//$db->show_error();
											$datausr = $db->get_array();
											$datausr = array_change_key_case($datausr, CASE_LOWER);
											$USRNAME = $datausr[fullname]; 
										}
										if ($BKK_FLAG==1) 
											$POH_DOCNO = $data2[POH_DOCNO]." ".$POH_DOCDATE." ".$POH_REF_DOC;
										else
											$POH_DOCNO = $data2[POH_DOCNO]."\n".$POH_DOCDATE."\n".$USRNAME;
										if (trim($data2[POH_DOCNO]) && trim($data2[POH_DOCNO]) != "-" && strpos($data2[POH_DOCNO],"�����") === false && 
											strpos($data2[POH_DOCNO],"�ѹ�֡") === false && strpos($data2[POH_DOCNO],"˹ѧ���") === false && $MOV_CODE != "12" && $MOV_CODE != "21" && $MOV_CODE != "027")
											$POH_DOCNO = "����� ".$POH_DOCNO;
									}

//									if ($MOV_CODE == "10420") {
									if ($MOV_CODE == "xxxx") {
										$POH_EFFECTIVEDATE = $POH_EFFECTIVEDATE."[2date]\n�֧\n".show_date_format($POH_ENDDATE,2);	//$POH_ENDDATE;
									}
									
									//��ŧ array �ͧ POSTION HIS
									if ($BKK_FLAG==1) {
										if ($MOV_NAME) $DOC_NO = $MOV_NAME."\n".$POH_DOCNO;
										else $DOC_NO = $POH_DOCNO;
										$ARR_POSITIONHIS[$PER_ID][] = array(
																						'DATE'=>$POH_EFFECTIVEDATE,
																						'SEQ'=>$POH_SEQ_NO,
																						'MOVE'=>$MOV_NAME,
																						'POS_NAME'=>$TMP_PL_NAME,
																						'POS_NO'=>$POH_POS_NO,
																						'LEVEL'=>$POSITION_LEVEL,
																						'FOOTERLEVEL'=>$footer_level,
																						'SALARY'=>$POH_SALARY,
																						'SALARY1'=>$POH_SALARY,
																						'ORG'=>$POH_ORG,
																						'DOC_NO'=>$DOC_NO);
									} else {
										$ARR_POSITIONHIS[$PER_ID][] = array(
																						'DATE'=>$POH_EFFECTIVEDATE,
																						'SEQ'=>$POH_SEQ_NO,
																						'MOVE'=>$MOV_NAME,
																						'POS_NAME'=>$TMP_PL_NAME,
																						'POS_NO'=>$POH_POS_NO,
																						'LEVEL'=>$POSITION_LEVEL,
																						'FOOTERLEVEL'=>$footer_level." ".$last_org,
																						'SALARY'=>$POH_SALARY,
																						'SALARY1'=>$POH_SALARY,
																						'ORG'=>$POH_ORG,
																						'DOC_NO'=>$POH_DOCNO);
									}
									$ARR_POSCHECK[$PER_ID][DOC_NO][] = $data2[POH_DOCNO];
									$ARR_POSCHECK[$PER_ID][DOC_DATE][] = $data2[POH_DOCDATE];
									$ARR_POSCHECK[$PER_ID][MOVE_CODE][] = $MOV_CODE;
								} // end while
							} //end if 
	
							//########################
							//����ѵԡ������͹����Թ��͹
							//########################
							$ARR_SALARYHIS = (array) null;
							if($DPISDB=="odbc"){
								$cmd = " select			LEFT(b.SAH_EFFECTIVEDATE,10) as SAH_EFFECTIVEDATE, c.MOV_NAME, b.SAH_SALARY, 
																	b.SAH_DOCNO, b.SAH_DOCDATE, b.UPDATE_USER, b.SAH_SEQ_NO, b.LEVEL_NO, b.SAH_POSITION, 
																	b.SAH_ORG, b.SM_CODE, b.SAH_PAY_NO, b.SAH_POS_NO_NAME, b.SAH_POS_NO, b.SAH_PERCENT_UP, 
																	b.SAH_SALARY_EXTRA, SAH_SPECIALIST, SAH_DOCNO_EDIT, SAH_DOCDATE_EDIT, SAH_REF_DOC
												 from			PER_SALARYHIS b
												 inner join 	PER_MOVMENT c	 on	b.MOV_CODE=c.MOV_CODE					
												 where		b.PER_ID=$PER_ID and b.MOV_CODE!='1901'
												 order by		b.SAH_SALARY, b.SAH_EFFECTIVEDATE ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select		 SUBSTR(b.SAH_EFFECTIVEDATE,1,10) as SAH_EFFECTIVEDATE, c.MOV_NAME, b.SAH_SALARY, 
																	b.SAH_DOCNO, b.SAH_DOCDATE, b.UPDATE_USER, b.SAH_SEQ_NO, b.LEVEL_NO, b.SAH_POSITION, 
																	b.SAH_ORG, b.SM_CODE, b.SAH_PAY_NO, b.SAH_POS_NO_NAME, b.SAH_POS_NO, b.SAH_PERCENT_UP, 
																	b.SAH_SALARY_EXTRA, SAH_SPECIALIST, SAH_DOCNO_EDIT, SAH_DOCDATE_EDIT, SAH_REF_DOC
												from			PER_SALARYHIS b, PER_MOVMENT c
												where		b.PER_ID=$PER_ID and b.MOV_CODE=c.MOV_CODE and b.MOV_CODE!='1901' 
												order by		b.SAH_SALARY, b.SAH_EFFECTIVEDATE ";		   					   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			LEFT(b.SAH_EFFECTIVEDATE,10) as SAH_EFFECTIVEDATE, c.MOV_NAME, b.SAH_SALARY, 
																	b.SAH_DOCNO, b.SAH_DOCDATE, b.UPDATE_USER, b.SAH_SEQ_NO, b.LEVEL_NO, b.SAH_POSITION, 
																	b.SAH_ORG, b.SM_CODE, b.SAH_PAY_NO, b.SAH_POS_NO_NAME, b.SAH_POS_NO, b.SAH_PERCENT_UP, 
																	b.SAH_SALARY_EXTRA, SAH_SPECIALIST, SAH_DOCNO_EDIT, SAH_DOCDATE_EDIT, SAH_REF_DOC
												 from			PER_SALARYHIS b  inner join 	PER_MOVMENT c	 on	b.MOV_CODE=c.MOV_CODE
												 where			b.PER_ID=$PER_ID and b.MOV_CODE!='1901'
												 order by		b.SAH_SALARY, b.SAH_EFFECTIVEDATE ";
							} // end if
							$count_salaryhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							//echo "<br>$cmd<br>";
							if($count_salaryhis){
								$salaryhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$salaryhis_count++;
									$SAH_EFFECTIVEDATE = trim($data2[SAH_EFFECTIVEDATE]);
									$SAH_EFFECTIVEDATE = ($SAH_EFFECTIVEDATE?$SAH_EFFECTIVEDATE:"-");
									$MOV_NAME = trim($data2[MOV_NAME]);		
									$MOV_NAME = (trim($MOV_NAME)?trim($MOV_NAME):"");
									$SAH_SALARY = $data2[SAH_SALARY];
									$SAH_SALARY1 = (float)$data2[SAH_SALARY];
									$SAH_SEQ_NO = $data2[SAH_SEQ_NO];
									$LEVEL_NO = $data2[LEVEL_NO];

									$cmd = " select POSITION_LEVEL, LEVEL_SHORTNAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
									$db_dpis3->send_cmd($cmd);
									//echo "<br>$cmd<br>";
									//$db_dpis3->show_error();
									$data3 = $db_dpis3->get_array();
									if ($BKK_FLAG==1)
										$POSITION_LEVEL = $data3[LEVEL_SHORTNAME];
									else
										$POSITION_LEVEL = $data3[POSITION_LEVEL];
									if ($POSITION_LEVEL=="�ӹҭ��þ����") {
										$POSITION_LEVEL = "�ӹҭ���\n�����";
//										$testpos = str_replace("\n","<BR>",$POSITION_LEVEL);
//										echo "POSITION_LEVEL=$testpos<BR>";
									}
									
									$SAH_POSITION = $data2[SAH_POSITION];
									if ($LEVEL_NO >= "01" && $LEVEL_NO <= "11" && strpos($SAH_POSITION,"0") === false && strpos($SAH_POSITION,"1") === false && 
										strpos($SAH_POSITION,"2") === false && strpos($SAH_POSITION,"3") === false && strpos($SAH_POSITION,"4") === false && strpos($SAH_POSITION,"5") === false && 
										strpos($SAH_POSITION,"6") === false && strpos($SAH_POSITION,"7") === false && strpos($SAH_POSITION,"8") === false && strpos($SAH_POSITION,"9") === false) 
										$SAH_POSITION .= $POSITION_LEVEL;
									$SAH_ORG = $data2[SAH_ORG];
									$SM_CODE = trim($data2[SM_CODE]);
									$SAH_SPECIALIST = trim($data2[SAH_SPECIALIST]);
									$SAH_DOCNO_EDIT = trim($data2[SAH_DOCNO_EDIT]);
									$SAH_DOCDATE_EDIT = trim($data2[SAH_DOCDATE_EDIT]);
									$SAH_REF_DOC = trim($data2[SAH_REF_DOC]);
									if ($SAH_SPECIALIST) $SAH_POSITION .= " ($SAH_SPECIALIST)";
									if ($BKK_FLAG==1 && (strpos($MOV_NAME,"���")!==false || strpos($MOV_NAME,"¡��ԡ")!==false)) {
										if ($SAH_DOCNO_EDIT) {
											if(trim($SAH_DOCDATE_EDIT)){
												$SAH_DOCDATE_EDIT = " ��. ".show_date_format(substr($SAH_DOCDATE_EDIT, 0, 10),2);
												$MOV_NAME .= ' '.$SAH_DOCNO_EDIT.$SAH_DOCDATE_EDIT;
											} else {
												$MOV_NAME .= ' '.$SAH_DOCNO_EDIT;
											}
										} 
//										if ($SAH_EFFECTIVEDATE < $PT_DATE) $SAH_POSITION = $MOV_NAME.' '.$SAH_POSITION;
									} 
									if ($BKK_FLAG==1 && $SAH_EFFECTIVEDATE >= $PT_DATE) {
										$SAH_ORG = "";
										if (strpos($MOV_NAME,"���")!==false || strpos($MOV_NAME,"¡��ԡ")!==false) {
											$SAH_POSITION = $MOV_NAME;
											$MOV_NAME = "";
										} elseif (substr($MOV_NAME,0,13)=="��Ѻ�Թ��͹" || $MOV_NAME=="��Ѻ����ѭ���Թ��͹����" || substr($MOV_NAME,0,18)=="��Ѻ�ѵ���Թ��͹") {
											$SAH_POSITION = "��Ѻ�ѵ���Թ��͹";
										} elseif ($MOV_NAME=="����͹����Թ��͹" || $MOV_NAME=="����͹�Թ��͹" || $MOV_NAME=="����͹��� 1 ���" || $MOV_NAME=="����͹��� 0.5 ���" || 
											$MOV_NAME=="����͹��� 1.5 ���" || $MOV_NAME=="����͹��� 1 ���" || $MOV_NAME=="����͹����Թ��͹����Ҫ���") {
											$SAH_POSITION = "����͹�Թ��͹";
											$MOV_NAME = "";
										} elseif ($MOV_NAME=="Ŵ��� 1 ���" || $MOV_NAME=="Ŵ����ҡ���� 1 ���") {
											$SAH_POSITION = "Ŵ����Թ��͹";
										} elseif ($MOV_NAME=="���Ѻ�Թ�ͺ᷹�����" || $MOV_NAME=="�Թ�ͺ᷹����� 2%" || $MOV_NAME=="�Թ�ͺ᷹����� 4%") {
											$SAH_POSITION = "������";
										} elseif ($MOV_NAME=="���������͹���") {
											$SAH_POSITION = "���������͹�Թ��͹";
										} else {
											$SAH_POSITION = $MOV_NAME;
											$MOV_NAME = "";
										}
									}
									$SAH_PAY_NO = $data2[SAH_PAY_NO];
									if (!$SAH_PAY_NO) $SAH_PAY_NO = $data2[SAH_POS_NO];
									$SAH_POS_NO_NAME = trim($data2[SAH_POS_NO_NAME]);
									if (substr($SAH_POS_NO_NAME,0,4)=="���.")
										$SAH_PAY_NO = $SAH_POS_NO_NAME." ".$SAH_PAY_NO;
									else
										$SAH_PAY_NO = $SAH_POS_NO_NAME.$SAH_PAY_NO;
									$SAH_PERCENT_UP = $data2[SAH_PERCENT_UP];		
									$SAH_SALARY_EXTRA = $data2[SAH_SALARY_EXTRA];		
									if ($SAH_PERCENT_UP) $MOV_NAME = $MOV_NAME . " " . number_format($SAH_PERCENT_UP, 2) . " %";		
									if (strpos($MOV_NAME,"������") !== false && $SAH_SALARY_EXTRA > 0) $MOV_NAME = $MOV_NAME . " " . number_format($SAH_SALARY_EXTRA, 2);	

									$cmd = " select SM_NAME, SM_FACTOR from PER_SALARY_MOVMENT where SM_CODE='$SM_CODE' ";
									$db_dpis3->send_cmd($cmd);
									$data3 = $db_dpis3->get_array();
									$SM_NAME = trim($data3[SM_NAME]);
									$SM_FACTOR = $data3[SM_FACTOR] + 0;
//									if ($SM_NAME && $SM_FACTOR != 0) $MOV_NAME .= " ($SM_NAME)";
									if ($BKK_FLAG==1) {
										if ($SM_NAME=="����͹��� 1 ���") $SAH_SALARY .= "\n˹�觢��";
										elseif ($SM_NAME=="����͹��� 1.5 ���") $SAH_SALARY .= "\n˹�觢�鹤���";
										elseif ($SM_NAME=="����͹��� 2 ���") $SAH_SALARY .= "\n�ͧ���";
//										echo "SALARY=$SAH_SALARY<br>";
									} else {
										if ($SM_NAME) $MOV_NAME .= " ($SM_NAME)";
									}
									$ARR_POSCHECK[$PER_ID][DOC_NO][] = $data2[POH_DOCNO];
									$ARR_POSCHECK[$PER_ID][DOC_DATE][] = $POH_DOCDATE;
									$ARR_POSCHECK[$PER_ID][MOVE_CODE][] = $MOV_CODE;
									$SAH_DOCNO = "";
									if (trim($data2[SAH_DOCNO]) == "-") {
										if(trim($data2[SAH_DOCDATE]) && trim($data2[SAH_DOCDATE])!="-"){
											$SAH_DOCNO = "��. ".show_date_format(substr($data2[SAH_DOCDATE], 0, 10),2);
										}
										$SAH_DOCNO = "";
									}elseif(trim($data2[SAH_DOCNO])){
										if(trim($data2[SAH_DOCDATE]) && trim($data2[SAH_DOCDATE])!="-"){
											$SAH_DOCDATE = "��. ".show_date_format(substr($data2[SAH_DOCDATE], 0, 10),2);
										}
										$USRNAME = "";
										if($data2[UPDATE_USER]){
											//�֧���ͨҡ ���ҧ user_detail �ͧ mysql
											$cmd1 ="select fullname from user_detail where id=$data2[UPDATE_USER]";
											$db->send_cmd($cmd1);
											//	$db->show_error();
											$datausr = $db->get_array();
											$datausr = array_change_key_case($datausr, CASE_LOWER);
											$USRNAME = $datausr[fullname];
										}
										if ($BKK_FLAG==1) 
											$SAH_DOCNO = $data2[SAH_DOCNO]." ".$SAH_DOCDATE;
										else
											$SAH_DOCNO = $data2[SAH_DOCNO]."\n".$SAH_DOCDATE."\n".$USRNAME;
										if (trim($data2[SAH_DOCNO]) && trim($data2[SAH_DOCNO]) != "-" && strpos($data2[SAH_DOCNO],"�����") === false && 
											strpos($data2[SAH_DOCNO],"�ѹ�֡") === false && strpos($data2[SAH_DOCNO],"˹ѧ���") === false)
											$SAH_DOCNO = "����� ".$SAH_DOCNO;
									}

									$cmd = " select POSITION_LEVEL, LEVEL_SHORTNAME, LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
									$db_dpis3->send_cmd($cmd);
									//echo "<br>$cmd<br>";
									//$db_dpis3->show_error();
									$data3 = $db_dpis3->get_array();
									if ($BKK_FLAG==1)
										$POSITION_LEVEL = $data3[LEVEL_SHORTNAME];
									else
										$POSITION_LEVEL = $data3[POSITION_LEVEL];
									if ($POSITION_LEVEL=="�ӹҭ��þ����") {
										$POSITION_LEVEL = "�ӹҭ���"."\n"."�����";
//										$testpos = str_replace("\n","<BR>",$POSITION_LEVEL);
//										echo "POSITION_LEVEL=$testpos<BR>";
									}
									$TMP_PL_NAME = trim($MOVE_NAME);

									if ($POSITION_LEVEL=="1" || $POSITION_LEVEL=="2" || $POSITION_LEVEL=="3" || $POSITION_LEVEL=="4" || $POSITION_LEVEL=="5" || 
										$POSITION_LEVEL=="6" || $POSITION_LEVEL=="7" || $POSITION_LEVEL=="8" || $POSITION_LEVEL=="9" || $POSITION_LEVEL=="10" ||	
										$POSITION_LEVEL=="11")
										$footer_level = $data3[LEVEL_NAME];
									else
										$footer_level = $POSITION_LEVEL;
//									echo "2..footer_level=$footer_level<br>";

									$flag_dup = false;
//									echo "(".trim($data2[SAH_DOCNO])." && ".trim($data2[SAH_DOCDATE]).")<br>";
									if (trim($data2[SAH_DOCNO]) && trim($data2[SAH_DOCNO])!="-" && trim($data2[SAH_DOCDATE]) && trim($data2[SAH_DOCDATE])!="-") {
										$key = array_search($data2[SAH_DOCNO], $ARR_POSCHECK[$PER_ID][DOC_NO]); 
										if ($key !== false) { // ����� DOC_NO ����͹�ѹ
											if ($ARR_POSCHECK[$PER_ID][DOC_DATE][$key] == $data2[SAH_DOCDATE]) { // ��ж���� doc_date ����͹�ѹ
	//											if ($ARR_POSCHECK[$PER_ID][MOVE_CODE][$key] == "") { // ��ж���� move_code=
													$flag_dup = true;
													if (!$ARR_POSITIONHIS[$PER_ID][$key][SEQ]) 
														$ARR_POSITIONHIS[$PER_ID][$key][SEQ] = $SAH_SEQ_NO;
													if (!$ARR_POSITIONHIS[$PER_ID][$key][MOVE]) 
														$ARR_POSITIONHIS[$PER_ID][$key][MOVE] = $MOV_NAME;
													if (!$ARR_POSITIONHIS[$PER_ID][$key][POS_NAME]) 
														$ARR_POSITIONHIS[$PER_ID][$key][POS_NAME] = $SAH_POSITION;
													if (!$ARR_POSITIONHIS[$PER_ID][$key][POS_NO]) 
														$ARR_POSITIONHIS[$PER_ID][$key][POS_NO] = $SAH_PAY_NO;
													if (!$ARR_POSITIONHIS[$PER_ID][$key][LEVEL]) 
														$ARR_POSITIONHIS[$PER_ID][$key][LEVEL] = $POSITION_LEVEL;
													if (!$ARR_POSITIONHIS[$PER_ID][$key][FOOTERLEVEL]) 
														$ARR_POSITIONHIS[$PER_ID][$key][FOOTERLEVEL] = $footer_level;
													if (!$ARR_POSITIONHIS[$PER_ID][$key][SALARY]) 
														$ARR_POSITIONHIS[$PER_ID][$key][SALARY] = $SAH_SALARY;
													if (!$ARR_POSITIONHIS[$PER_ID][$key][SALARY1]) 
														$ARR_POSITIONHIS[$PER_ID][$key][SALARY1] = $SAH_SALARY1;
	//											} // end if check movecode
											} // end if check doc_date
										} // end if check doc_no
									} // end if (trim($data2[SAH_DOCNO]) && trim($data2[SAH_DOCDATE]))
									if (!$flag_dup) { // ��������
									//��ŧ array �ͧ SALARYHIS
										if ($BKK_FLAG==1) {
											if ($MOV_NAME) $DOC_NO = $MOV_NAME."\n".$SAH_DOCNO;
											else $DOC_NO = $SAH_DOCNO;
											$ARR_SALARYHIS[$PER_ID][] = array(
																							'DATE'=>$SAH_EFFECTIVEDATE,
																							'SEQ'=>$SAH_SEQ_NO,
																							'MOVE'=>$SAH_POSITION."\n".$SAH_ORG,
																							'POS_NAME'=>$TMP_PL_NAME,
																							'POS_NO'=>$SAH_PAY_NO,
																							'LEVEL'=>$POSITION_LEVEL,
																							'FOOTERLEVEL'=>$footer_level,
																							'SALARY'=>$SAH_SALARY,
																							'SALARY1'=>$SAH_SALARY1,
																							'ORG'=>$SAH_ORG,
																							'DOC_NO'=>$DOC_NO);																			
										} else {
											$ARR_SALARYHIS[$PER_ID][] = array(
																							'DATE'=>$SAH_EFFECTIVEDATE,
																							'SEQ'=>$SAH_SEQ_NO,
																							'MOVE'=>$MOV_NAME,
																							'POS_NAME'=>$TMP_PL_NAME,
																							'POS_NO'=>$SAH_PAY_NO,
																							'LEVEL'=>$POSITION_LEVEL,
																							'FOOTERLEVEL'=>$footer_level,
																							'SALARY'=>$SAH_SALARY,
																							'SALARY1'=>$SAH_SALARY1,
																							'ORG'=>$SAH_ORG,
																							'DOC_NO'=>$SAH_DOCNO);																			
										}
									} // end if !$flag_dup 
								} // end while
							}// end if

							//######################################
							//�������ѵԡ�ô�ç���˹� + �������͹����Թ��͹
							//######################################
							//array_multisort($ARR_POSITIONHIS[$PER_ID], SORT_ASC, $ARR_SALARYHIS[$PER_ID], SORT_ASC);
							$cnt_pos=count($ARR_POSITIONHIS[$PER_ID]);
							$cnt_sal=count($ARR_SALARYHIS[$PER_ID]);
							if ($cnt_pos > 0 && $cnt_sal > 0)
								$ARRAY_POH_SAH[$PER_ID] = array_merge_recursive($ARR_POSITIONHIS[$PER_ID] , $ARR_SALARYHIS[$PER_ID]);
							else if ($cnt_pos > 0)
								$ARRAY_POH_SAH[$PER_ID] = $ARR_POSITIONHIS[$PER_ID];
							else if ($cnt_sal > 0)
								$ARRAY_POH_SAH[$PER_ID] = $ARR_SALARYHIS[$PER_ID];
							else
								$ARRAY_POH_SAH[$PER_ID] = (array) null;
							// dum
//							echo "PER_ID=$PER_ID cnt-position-his=".count($ARR_POSITIONHIS[$PER_ID])." ,  cnt-salary-his=".count($ARR_SALARYHIS[$PER_ID]).",   cnt-merge=".count($ARRAY_POH_SAH[$PER_ID])."<br>";
							// dum
							unset($ARR_POSITIONHIS);
							unset($ARR_SALARYHIS);

							// ���§������ ����ѹ��� / �Թ��͹ ������ҡ
							for($in=0; $in < count($ARRAY_POH_SAH[$PER_ID]); $in++){
								//�纤���ѹ���
								$DATE_HIS[] = array('DATE'=>$ARRAY_POH_SAH[$PER_ID][$in]['DATE'],
															'SEQ'=>$ARRAY_POH_SAH[$PER_ID][$in]['SEQ'],
															'MOVE'=>$ARRAY_POH_SAH[$PER_ID][$in]['MOVE'],
															'POS_NAME'=>$ARRAY_POH_SAH[$PER_ID][$in]['POS_NAME'],
															'POS_NO'=>$ARRAY_POH_SAH[$PER_ID][$in]['POS_NO'],
															'LEVEL'=>$ARRAY_POH_SAH[$PER_ID][$in]['LEVEL'],
															'FOOTERLEVEL'=>$ARRAY_POH_SAH[$PER_ID][$in]['FOOTERLEVEL'],
															'SALARY'=>$ARRAY_POH_SAH[$PER_ID][$in]['SALARY'],
															'SALARY1'=>$ARRAY_POH_SAH[$PER_ID][$in]['SALARY1'],
															'ORG'=>$ARRAY_POH_SAH[$PER_ID][$in]['ORG'],
															'DOC_NO'=>$ARRAY_POH_SAH[$PER_ID][$in]['DOC_NO']);
							} // end for
							unset($ARRAY_POH_SAH);
							$DATE = (array) null;
							$SEQ = (array) null;
							$LEVEL = (array) null;
							$FOOTERLEVEL = (array) null;
							$SALARY1 = (array) null;
							foreach ($DATE_HIS as $key => $value) {		//�óշ���ѹ������ǡѹ ���ͧ����Թ��͹���¡����ʴ���͹
								$DATE[$key]  = $value['DATE'];
								$SEQ[$key]  = $value['SEQ'];
//								$MOVE[$key]  = $value['MOVE'];
//								$POS_NAME[$key] = $value['POS_NAME'];
//								$POS_NO[$key]  = $value['POS_NO'];
								$LEVEL[$key]  = $value['LEVEL'];
								$FOOTERLEVEL[$key]  = $value['FOOTERLEVEL'];
//								$SALARY[$key] = $value['SALARY'];
								$SALARY1[$key] = $value['SALARY1'];
//								$DOC_NO[$key]  = $value['DOC_NO'];
//								echo "bf.....$PER_ID-->$key-->DATE=".$DATE[$key]." | SALARY1=".$SALARY1[$key]." | SEQ=".$SEQ[$key]." | LEVEL=".$LEVEL[$key]." | FOOTERLEVEL=".$FOOTERLEVEL[$key]."<br>";
							} // end foreach
//							$msort_result = array_multisort($DATE, SORT_ASC, $SALARY1, SORT_NUMERIC, SORT_ASC, $DATE_HIS);
							$msort_result = array_multisort($DATE, SORT_ASC, $SEQ, SORT_NUMERIC, SORT_ASC, $DATE_HIS);
//							$msort_result = array_multisort($DATE, SORT_ASC, $DATE_HIS);
//							echo "************************* msort_result=$msort_result  (count(DATE_HIS)=".count($DATE_HIS).")<br>";
							foreach ($DATE_HIS as $key => $value) {		// loop ������� echo ��� ���͵�Ǩ�ͺ��ѧ sort ����
								$DATE[$key]  = $value['DATE'];
								$SEQ[$key]  = $value['SEQ'];
//								$MOVE[$key]  = $value['MOVE'];
//								$POS_NAME[$key] = $value['POS_NAME'];
//								$POS_NO[$key]  = $value['POS_NO'];
								$LEVEL[$key]  = $value['LEVEL'];
								$FOOTERLEVEL[$key]  = $value['FOOTERLEVEL'];
								$SALARY[$key] = $value['SALARY'];
//								$SALARY1[$key] = $value['SALARY'];
//								$DOC_NO[$key]  = $value['DOC_NO'];
//								echo "af.....$PER_ID-->$key-->DATE=".$DATE[$key]." | SALARY=".$SALARY[$key]." | SEQ=".$SEQ[$key]." | LEVEL=".$LEVEL[$key]." | FOOTERLEVEL=".$FOOTERLEVEL[$key]."<br>";
							} // end foreach
							$POH_SAH_HIS[$PER_ID]=$DATE_HIS;
							unset($DATE_HIS);

							//��ǹ�ʴ������� ��ѧ�ҡ�Ѵ���§����������
							if(is_array($POH_SAH_HIS) && !empty($POH_SAH_HIS)){
							$count_positionhis=count($POH_SAH_HIS[$PER_ID]);
							$positionhis_count=$first_line-1;
							//����������� line �Ҥ���ʴ������� 
							if(!isset($get_line) || $get_line==""){		$get_line=$count_positionhis;		}
							$linenum = ceil($pdf->y / 7);  // ����������ǡ���÷Ѵ
							$linecnt = 0;
							for($in=0; $in < $count_positionhis; $in++){
									$positionhis_count++;
									$linecnt++;
									if($POH_SAH_HIS[$PER_ID][$in]['DATE']){
										$DATE_POH_SAH = show_date_format(substr($POH_SAH_HIS[$PER_ID][$in]['DATE'], 0, 10),3);
									}

									//���дѺ���˹� (1,2,3,4,5,6,7,8,9);
									$cmd = "select LEVEL_NAME, POSITION_TYPE from PER_LEVEL where LEVEL_NO='".$POH_SAH_HIS[$PER_ID][$in]['LEVEL']."' order by LEVEL_NO";
									//echo "<br>$cmd<br>";
									$db_dpis2->send_cmd($cmd);
//									$db_dpis2->show_error();
									$data2 = $db_dpis2->get_array();
									$LEVEL_NAME1 = trim($data2[LEVEL_NAME]);
									$POSITION_TYPE = trim($data2[POSITION_TYPE]);
									$arr_temp = explode(" ", $LEVEL_NAME1);
									//�Ҫ����дѺ���˹� 
									$LEVEL_NAME1 ="";
									if(strstr($arr_temp[1], '�дѺ') == TRUE) {
										$LEVEL_NAME1 =  str_replace("�дѺ", "", $arr_temp[1]);
									}else{
										$LEVEL_NAME1 =  $arr_temp[1];
									}
									
									//��˹����͵��˹� -----------------------
									if(trim($POH_SAH_HIS[$PER_ID][$in]['POS_NAME'])){		//����Ѻ�������͹��Ǣͧ ��.
										$f_pos_color = "blue";
										$POH_SAH_HIS[$PER_ID][$in]['POS_NAME'] = $POH_SAH_HIS[$PER_ID][$in]['POS_NAME']." ".$LEVEL_NAME1;	
									}else if(trim($POH_SAH_HIS[$PER_ID][$in]['MOVE'])){		//����Ѻ�������͹����Թ��͹
										$f_pos_color = "black";
										$POH_SAH_HIS[$PER_ID][$in]['POS_NAME'] =  $POH_SAH_HIS[$PER_ID][$in]['MOVE'];
									}

									$sub_doc = explode("\n", $POH_SAH_HIS[$PER_ID][$in]['DOC_NO']);
									$prt_doc_line = count($sub_doc);
									$prt_doc_line = $prt_doc_line - (!trim($sub_doc[$prt_doc_line-1]) ? 1 -  (!trim($sub_doc[$prt_doc_line-2]) ? 1 : 0) : 0);
										$dlen = strlen($POH_SAH_HIS[$PER_ID][$in]['POS_NAME']);
//										echo "bf-".$POH_SAH_HIS[$PER_ID][$in]['POS_NAME']."[".$dlen."]<br>";
//										$temptext = $pdf->thaiCutLinePDF($POH_SAH_HIS[$PER_ID][$in]['POS_NAME'], $heading_width[POSITIONHIS][1], "\n");
										$temptext = $POH_SAH_HIS[$PER_ID][$in]['POS_NAME'];
										$sub_pos = explode("\n", $temptext);
									$sub_level = explode("\n", $POH_SAH_HIS[$PER_ID][$in]['LEVEL']);
									$prt_level_line = count($sub_level);
									$prt_pos_line = 0;
									$prt_pos_line = count($sub_pos);
									$prt_pos_line = $prt_pos_line - (!trim($sub_pos[$prt_pos_line-1]) ? 1 -  (!trim($sub_doc[$prt_doc_line-2]) ? 1 : 0) : 0);
									$prt_max_line = ($prt_doc_line > $prt_pos_line ? $prt_doc_line : $prt_pos_line);
									$prt_max_line = ($prt_level_line > $prt_max_line ? $prt_level_line : $prt_max_line);
//									if (((($linenum + $prt_max_line) * 7) + 14) > $pdf->h) {
									// $linenum = �ӹǹ��÷Ѵ������������ + $prt_doc_line = �ӹǹ��÷Ѵ���о��������Ѻ��¡�ù�� ���Ǥٳ���� 7 ��ͤ����٧�ͧ��÷Ѵ
									//  ���Ǻǡ���� 7 ���������͢ͺ��ҧ 1 ��÷Ѵ (��Ҩ���� 2 ��÷Ѵ�� �ǡ���� 14)
//										print_footer($last_footer_level);
//										$RTF->new_page();
//									}
									
									$last_footer_level = $POH_SAH_HIS[$PER_ID][$in]['FOOTERLEVEL'];
									
//									for($sub_line_i = 0; 	$sub_line_i < $prt_max_line; $sub_line_i++) {
										$linenum++;
										$border = (($sub_line_i == ($prt_max_line-1) && $sub_line_i == 0) ? "LTBR" : ($sub_line_i == 0 ? "LTR" : ($sub_line_i == ($prt_max_line-1) ? "LBR" : "LR")));

										$RTF->open_line();
										$RTF->set_font($font,14);
										$RTF->color("1");	// 0=DARKGRAY
										$RTF->cell(($DATE_POH_SAH?$DATE_POH_SAH:""), $heading_width[$HISTORY_NAME][0], "left", "8", $border);
//										$RTF->cell(($prt_pos ? $prt_pos : ""), $heading_width[$HISTORY_NAME][1], "left", "8", $border);
										$RTF->cell($POH_SAH_HIS[$PER_ID][$in]['POS_NAME'], $heading_width[$HISTORY_NAME][1], "left", "8", $border);
										$RTF->cell(($POH_SAH_HIS[$PER_ID][$in]['POS_NO']?$POH_SAH_HIS[$PER_ID][$in]['POS_NO']:""), $heading_width[$HISTORY_NAME][2], "center", "8", $border);
//										$RTF->cell(($prt_level ? $prt_level : ""), $heading_width[$HISTORY_NAME][3], "center", "8", $border);
										$RTF->cell($POH_SAH_HIS[$PER_ID][$in]['LEVEL'], $heading_width[$HISTORY_NAME][3], "center", "8", $border);
										$salary_text = ($POH_SAH_HIS[$PER_ID][$in]['SALARY']?number_format($POH_SAH_HIS[$PER_ID][$in]['SALARY']):"-");
										$RTF->cell($salary_text, $heading_width[$HISTORY_NAME][4], "center", "8", $border);
//										$RTF->cell(($prt_doc ? $prt_doc : ""), $heading_width[$HISTORY_NAME][5], "left", "8", $border);
										$RTF->cell($POH_SAH_HIS[$PER_ID][$in]['DOC_NO'], $heading_width[$HISTORY_NAME][5], "left", "8", $border);
										$RTF->close_line();
//									}
							}
							if($in<=0){	
								$RTF->open_line();
								$RTF->set_font($font,16);
								$RTF->color("1");	// 0=DARKGRAY
								$RTF->cell($RTF->bold(1)."********** ����բ����� **********".$RTF->bold(0), "$page_ch", "center", "8", "LBR");
								$RTF->close_line();
							} else {
								$page_no++;
								print_footer($last_footer_level);
							} // end if	
						} // end if(is_array($POH_SAH_HIS) && !empty($POH_SAH_HIS))
						break;
					case "ABILITY" :
							$RTF->new_page();

//							$RTF->add_text("", "left");

							$RTF->open_line();
							$RTF->set_font($font,14);
							$RTF->color("1");	// 0=DARKGRAY
							$RTF->cell($RTF->bold(1).($history_index)."  ��������ö����� ".$RTF->bold(0), "$page_ch", "left", "8", "TRBL");
							$RTF->close_line();

							$AL_NAME = "";
							$ABI_DESC = "";

							$cmd = " select		b.AL_NAME, a.ABI_DESC
											 from			PER_ABILITY a, PER_ABILITYGRP b
											 where		a.PER_ID=$PER_ID and a.AL_CODE=b.AL_CODE
											 order by	a.ABI_ID ";							   
							$count_abilityhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_abilityhis){
								$abilityhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$abilityhis_count++;
									$AL_NAME = trim($data2[AL_NAME]);
									$ABI_DESC = trim($data2[ABI_DESC]);

									$border = "RL";

									$RTF->open_line();
									$RTF->set_font($font,14);
									$RTF->color("1");	// 0=DARKGRAY
									$RTF->cell("��ҹ:$AL_NAME ��������ö�����:$ABI_DESC", $heading_width[$HISTORY_NAME][0]+$heading_width[$HISTORY_NAME][1], "left", "8", $border);
									$RTF->close_line();
								} // end while
								$RTF->open_line();
								$RTF->set_font($font,14);
								$RTF->color("1");	// 0=DARKGRAY
								$RTF->cell("", $heading_width[$HISTORY_NAME][0]+$heading_width[$HISTORY_NAME][1], "left", "8", "RBL");
								$RTF->close_line();
							}else{
								$RTF->open_line();
								$RTF->set_font($font,14);
								$RTF->color("1");	// 0=DARKGRAY
								$RTF->cell("********** ����բ����� **********", "$page_ch", "center", "8", "LBR");
								$RTF->close_line();
							} // end if
						break;
					case "SERVICEHIS" :
							$RTF->new_page();

//							$RTF->add_text("", "left");
							
							$RTF->open_line();
							$RTF->set_font($font,14);
							$RTF->color("1");	// 0=DARKGRAY
							$RTF->cell($RTF->bold(1).($history_index)."  ����ѵԡ�û�Ժѵ��Ҫ��þ���� ".$RTF->bold(0), "$page_ch", "left", "8", "TRBL");
							$RTF->close_line();

							print_header($HISTORY_NAME);

							$SRH_STARTDATE = "";
							$SV_NAME = "";
							$SRH_DOCNO = "";
							$SRH_NOTE = "";
							
							$cmd = " select		a.SRH_STARTDATE, b.SV_NAME, a.SRH_DOCNO, a.SRH_NOTE, c.ORG_NAME, d.SRT_NAME, SRH_ORG, SRH_SRT_NAME
											 from			PER_SERVICEHIS a, PER_SERVICE b, PER_ORG c, PER_SERVICETITLE d
											 where		a.PER_ID=$PER_ID and a.SV_CODE=b.SV_CODE and a.ORG_ID=c.ORG_ID(+) and a.SRT_CODE=d.SRT_CODE(+)
											 order by	a.SRH_STARTDATE ";							   
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$count_servicehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_servicehis){
								$servicehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$servicehis_count++;
									if ($servicehis_count % 2 == 0) {
										$SRH_STARTDATE2 = show_date_format($data2[SRH_STARTDATE],$DATE_DISPLAY);
										$SV_NAME2 = trim($data2[SV_NAME]);
										$SRH_DOCNO2 = trim($data2[SRH_DOCNO]);
										if ($SRH_DOCNO2) $SRH_DOCNO2 = "�͡��� : $SRH_DOCNO2";
										$SRH_NOTE2 = trim($data2[SRH_NOTE]);
										$SRH_ORG2 = trim($data2[ORG_NAME]);
										if (!$SRH_ORG2) $SRH_ORG2 = trim($data2[SRH_ORG]);
										$SRT_NAME2 = trim($data2[SRT_NAME]);
										if (!$SRT_NAME2) $SRT_NAME2 = trim($data2[SRH_SRT_NAME]);
									} else {
										$SRH_STARTDATE1 = show_date_format($data2[SRH_STARTDATE],$DATE_DISPLAY);
										$SV_NAME1 = trim($data2[SV_NAME]);
										$SRH_DOCNO1 = trim($data2[SRH_DOCNO]);
										if ($SRH_DOCNO1) $SRH_DOCNO1 = "�͡��� : $SRH_DOCNO1";
										$SRH_NOTE1 = trim($data2[SRH_NOTE]);
										$SRH_ORG1 = trim($data2[ORG_NAME]);
										if (!$SRH_ORG1) $SRH_ORG1 = trim($data2[SRH_ORG]);
										$SRT_NAME1 = trim($data2[SRT_NAME]);
										if (!$SRT_NAME1) $SRT_NAME1 = trim($data2[SRH_SRT_NAME]);
									}

									if ($servicehis_count % 2 == 0) {
										$border = "TRBL";

										$RTF->open_line();
										$RTF->set_font($font,14);
										$RTF->color("1");	// 0=DARKGRAY
										$RTF->cell("$SRH_STARTDATE1", $heading_width[$HISTORY_NAME][0], "left", "8", $border);
										$RTF->cell("������ : $SV_NAME1   ��Ǣ���Ҫ��þ���� : $SRT_NAME1  �͡��� : $SRH_DOCNO1", $heading_width[$HISTORY_NAME][1], "left", "8", $border);
										$RTF->cell("$SRH_STARTDATE2", $heading_width[$HISTORY_NAME][2], "left", "8", $border);
										$RTF->cell("������ : $SV_NAME2   ��Ǣ���Ҫ��þ���� : $SRT_NAME2  �͡��� : $SRH_DOCNO2", $heading_width[$HISTORY_NAME][3], "left", "8", $border);
										$RTF->close_line();
									} // end if ($servicehis_count % 2 == 0)
								} // end while
							}else{
								$RTF->open_line();
								$RTF->set_font($font,14);
								$RTF->color("1");	// 0=DARKGRAY
								$RTF->cell("********** ����բ����� **********", "$page_ch", "center", "8", "LBR");
								$RTF->close_line();
							} // end if
						break;
					case "SPECIALSKILLHIS" :
							$RTF->new_page();

//							$RTF->add_text("", "left");

							$RTF->open_line();
							$RTF->set_font($font,14);
							$RTF->color("1");	// 0=DARKGRAY
							$RTF->cell($RTF->bold(1).($history_index)."  ��¡����� � ������ ".$RTF->bold(0), "$page_ch", "left", "8", "TRBL");
							$RTF->close_line();

							$SS_NAME = "";
							$SPS_EMPHASIZE = "";
							
							$cmd = " select		b.SS_NAME, a.SPS_EMPHASIZE
											 from			PER_SPECIAL_SKILL a, PER_SPECIAL_SKILLGRP b
											 where		a.PER_ID=$PER_ID and a.SS_CODE=b.SS_CODE
											 order by	a.SPS_ID ";							   
							$count_specialskillhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_specialskillhis){
								$specialskillhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$specialskillhis_count++;
									$SS_NAME = trim($data2[SS_NAME]);
									$SPS_EMPHASIZE = trim($data2[SPS_EMPHASIZE]);
				
									$border = "RL";

									$RTF->open_line();
									$RTF->set_font($font,14);
									$RTF->color("1");	// 0=DARKGRAY
									$RTF->cell("��������ö����� : $SS_NAME, $SPS_EMPHASIZE", $heading_width[$HISTORY_NAME][0]+$heading_width[$HISTORY_NAME][1], "left", "8", $border);
									$RTF->close_line();

								} // end while
								$RTF->open_line();
								$RTF->set_font($font,14);
								$RTF->color("1");	// 0=DARKGRAY
								$RTF->cell("", $heading_width[$HISTORY_NAME][0]+$heading_width[$HISTORY_NAME][1], "left", "8", "RBL");
								$RTF->close_line();
							}else{
								$RTF->open_line();
								$RTF->set_font($font,14);
								$RTF->color("1");	// 0=DARKGRAY
								$RTF->cell("********** ����բ����� **********", "$page_ch", "center", "8", "LBR");
								$RTF->close_line();
							} // end if
						break;
					case "DECORATEHIS" :
							$RTF->new_page();

//							$RTF->add_text("", "left");

							$RTF->open_line();
							$RTF->set_font($font,14);
							$RTF->color("1");	// 0=DARKGRAY
							$RTF->cell($RTF->bold(1).($history_index)."  ��¡����� � ������ ".$RTF->bold(0), "$page_ch", "left", "8", "TRBL");
							$RTF->close_line();
//							$RTF->new_line();

							// �ǻ���ѵ��ٻ
							$imgcnt = count($arr_img);
							if ($imgcnt > 0) {
								$start_pic = 0;
								if  ($imgcnt > 7) $start_pic = $imgcnt - 7;	// ���ʴ����٧�ش 7 �Ҿ ������Ѻ�ҡ�Ҿ����شŧ�� 7 �Ҿ
								else $imgcnt = 7;
								$fix_width = 120; 
								$RTF->open_line();
								for($i=$start_pic; $i  < $imgcnt; $i++) {
									if($arr_img[$i]){
										$image_x = ($imgx + (($image_w+2) * $j) + 1);
										list($width, $height) = getimagesize($arr_img[$i]); 
										$new_h = $height / $width * $fix_width;
										$new_ratio = floor(100 / $height * $fix_width); 
										$fsnum = substr($RTF->_font_size(),3);
										$w_chr = floor($fix_width / $fsnum);
										$w_chr = 14;
//										$RTF->add_Image($arr_img[$i], "$new_ratio", "center");
//										echo "new_ratio=$new_ratio, w_chr=$w_chr<br>";
										$RTF->cellImage($arr_img[$i], "$new_ratio", "$w_chr", "center", 0, "TRBL");
										$j++;
									} // end if	
								}							
								$RTF->close_line();
								$j = 0;
								$RTF->open_line();
								for($i=$start_pic; $i  < $imgcnt; $i++) {
									if($arr_img[$i]){
										$RTF->set_font($font,12);
										$fsnum = substr($RTF->_font_size(),3);
										$w_chr = floor($fix_width / $fsnum);
										$w_chr = 14;
//										echo "w_chr=$w_chr, RTF->_font_size()=$fsnum<br>";
										if ($arr_imgshow[$i]==1){  
											$RTF->color("16");	 
											$RTF->cell($RTF->bold(1)."�ٻ��� ".($i+1).$RTF->bold(0), "$w_chr", "center", "8", "TRL");
										} else {  
											$RTF->color("0");  
											$RTF->cell("�ٻ��� ".($i+1), "$w_chr", "center", "8", "TRL");
										}
										$j++;
									} // end if			
								}
								$RTF->close_line();
								$j = 0;
								$RTF->open_line();
								for($i=$start_pic; $i  < $imgcnt; $i++) {
									if($arr_img[$i]){
										$RTF->set_font($font,12);
										$fsnum = substr($RTF->_font_size(),3);
										$w_chr = floor($fix_width / $fsnum);
										$w_chr = 14;
										if ($arr_imgshow[$i]==1){  
											$RTF->color("16");	 
											$RTF->cell($RTF->bold(1)."�.�. ".substr($arr_imgsavedate[$i],0,4).$RTF->bold(0), "$w_chr", "center", "8", "RBL");
										} else {  
											$RTF->color("0");  
											$RTF->cell("�.�. ".substr($arr_imgsavedate[$i],0,4), "$w_chr", "center", "8", "RBL");
										}
										$j++;
									} // end if			
								}
								$RTF->close_line();
							}	// if ($imgcnt > 0)
							// ������ѵ��ٻ

							$RTF->new_page();

//							$RTF->add_text("", "left");

							$RTF->open_line();
							$RTF->set_font($font,14);
							$RTF->color("1");	// 0=DARKGRAY
							$RTF->cell("1. ����  ".$FULLNAME."   �ѹ ��͹ �� �Դ  ".$PER_BIRTHDATE, "$page_ch", "left", "8", "TRBL");
							$RTF->close_line();

							$RTF->open_line();
							$RTF->set_font($font,14);
							$RTF->color("1");	// 0=DARKGRAY
							$RTF->cell("2. �������Ѩ�غѹ ".($PER_ADD1 ? $PER_ADD1 : $PER_ADD2), "$page_ch", "left", "8", "TRBL");
							$RTF->close_line();

							$RTF->open_line();
							$RTF->set_font($font,14);
							$RTF->color("1");	// 0=DARKGRAY
							$RTF->cell($RTF->bold(1)."3. ����ͧ�Ҫ��������ó� �ѹ������Ѻ �ѹ�觤׹ �������͡�����ҧ�ԧ".$RTF->bold(0), "$page_ch", "left", "8", "TRBL");
							$RTF->close_line();

							for($i  = 0; $i < count($arr_DEH_SHOW); $i+=2) {
								if ($i==0 && $i >= count($arr_DEH_SHOW)-2) {
									$border = "TRBL";
								} else if ($i==0) {
									$border = "TRL";
								} else if ($i >= count($arr_DEH_SHOW)-2) {
									$border = "RBL";
								} else {
									$border = "RL";
								}
								$RTF->open_line();
								$RTF->set_font($font,14);
								$RTF->color("1");	// 0=DARKGRAY
								$RTF->cell($arr_DEH_SHOW[$i], "49", "left", "8", $border);
								$RTF->cell($arr_DEH_SHOW[$i+1], "49", "left", "8", $border);
								$RTF->close_line();
							} // end for loop
						break;
					case "ABSENTHIS" :
							$RTF->new_page();

//							$RTF->add_text("", "left");
							
							$RTF->open_line();
							$RTF->set_font($font,14);
							$RTF->color("1");	// 0=DARKGRAY
							$RTF->cell($RTF->bold(1)."4. �ӹǹ�ѹ����ش �Ҵ�Ҫ��� ����� ".$RTF->bold(0), "$page_ch", "left", "8", "TRBL");
							$RTF->close_line();

							print_header($HISTORY_NAME);
							
							$cmd = " select		AS_YEAR, START_DATE, END_DATE, AB_CODE_01, AB_CODE_02, AB_CODE_03, AB_CODE_04, AB_CODE_05, AB_CODE_06, 
																AB_CODE_07, AB_CODE_08, AB_CODE_09, AB_CODE_10, AB_CODE_11, AB_CODE_12, AB_CODE_13, AB_CODE_14, AB_CODE_15
											 from			PER_ABSENTSUM 
											 where		PER_ID=$PER_ID
											 order by	AS_YEAR ";							   
							$count_absentsum = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$arr_AB_NAME = (array) null;
							if($count_absentsum){
								$absentsum_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$absentsum_count++;
									$AS_YEAR = trim($data2[AS_YEAR]);
//									echo "AS_YEAR=$AS_YEAR<br>";
									$AB_CODE_01 = $data2[AB_CODE_01];
									$AB_CODE_02 = $data2[AB_CODE_02];
									$AB_CODE_03 = $data2[AB_CODE_03];
									$AB_CODE_04 = $data2[AB_CODE_04];
									$AB_CODE_05 = $data2[AB_CODE_05];
									$AB_CODE_06 = $data2[AB_CODE_06];
									$AB_CODE_07 = $data2[AB_CODE_07];
									$AB_CODE_08 = $data2[AB_CODE_08];
									$AB_CODE_09 = $data2[AB_CODE_09];
									$AB_CODE_10 = $data2[AB_CODE_10];
									$AB_CODE_11 = $data2[AB_CODE_11];
									$AB_CODE_12 = $data2[AB_CODE_12];
									$AB_CODE_13 = $data2[AB_CODE_13];
									$AB_CODE_14 = $data2[AB_CODE_14];
									$AB_CODE_15 = $data2[AB_CODE_15];

									$code = array(	"01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15" );
									for ( $i=0; $i<count($code); $i++ ) { 
										$cmd = " select sum(ABS_DAY) as abs_day from PER_ABSENTHIS 
														where PER_ID=$PER_ID and AB_CODE='$code[$i]' and ABS_STARTDATE >= '$data2[START_DATE]' and ABS_ENDDATE <= '$data2[END_DATE]' ";
										$db_dpis3->send_cmd($cmd);
//										echo $cmd;
										$data3 = $db_dpis3->get_array();
										$data3 = array_change_key_case($data3, CASE_LOWER);
										if ($code[$i]=="01") $AB_CODE_01 += $data3[abs_day];
										elseif ($code[$i]=="02") $AB_CODE_02 += $data3[abs_day];
										elseif ($code[$i]=="03") $AB_CODE_03 += $data3[abs_day];
										elseif ($code[$i]=="04") $AB_CODE_04 += $data3[abs_day];
										elseif ($code[$i]=="05") $AB_CODE_05 += $data3[abs_day];
										elseif ($code[$i]=="06") $AB_CODE_06 += $data3[abs_day];
										elseif ($code[$i]=="07") $AB_CODE_07 += $data3[abs_day];
										elseif ($code[$i]=="08") $AB_CODE_08 += $data3[abs_day];
										elseif ($code[$i]=="09") $AB_CODE_09 += $data3[abs_day];
										elseif ($code[$i]=="10") $AB_CODE_10 += $data3[abs_day];
										elseif ($code[$i]=="11") $AB_CODE_11 += $data3[abs_day];
										elseif ($code[$i]=="12") $AB_CODE_12 += $data3[abs_day];
										elseif ($code[$i]=="13") $AB_CODE_13 += $data3[abs_day];
										elseif ($code[$i]=="14") $AB_CODE_14 += $data3[abs_day];
										elseif ($code[$i]=="15") $AB_CODE_15 += $data3[abs_day];
									}

									if (!$arr_AB_NAME[$AS_YEAR][$ABIDX]) $arr_AB_NAME[$ABS_YEAR][$ABIDX]=0;
									$arr_AB_NAME[$AS_YEAR][0] += $AB_CODE_01 + $AB_CODE_12;
									$arr_AB_NAME[$AS_YEAR][1] += $AB_CODE_03; 
									$arr_AB_NAME[$AS_YEAR][2] += $AB_CODE_04; 
									$arr_AB_NAME[$AS_YEAR][3] += $AB_CODE_10;
									$arr_AB_NAME[$AS_YEAR][4] += $AB_CODE_13;
									$arr_AB_NAME[$AS_YEAR][5] += $AB_CODE_07;
									$arr_AB_NAME[$AS_YEAR][6] += $AB_CODE_02;
									$arr_AB_NAME[$AS_YEAR][7] += $AB_CODE_05;
									$arr_AB_NAME[$AS_YEAR][8] += $AB_CODE_06;
									$arr_AB_NAME[$AS_YEAR][9] += $AB_CODE_08;
									$arr_AB_NAME[$AS_YEAR][10] += $AB_CODE_09;
									$arr_AB_NAME[$AS_YEAR][11] += $AB_CODE_11;
									$arr_AB_NAME[$AS_YEAR][12] += $AB_CODE_14;
									$arr_AB_NAME[$AS_YEAR][13] += $AB_CODE_15;
								}

								$border = "";
								$j = 1;
								foreach($arr_AB_NAME as $year => $sub_arr) {
									if ($j % 2!=0) {
										$RTF->open_line();
										$RTF->set_font($font,14);
										$RTF->color("1");	// 0=DARKGRAY
									}
									$RTF->cell("$year", $heading_width[$HISTORY_NAME][0], "center", "8", "TRBL");
									for($abidx = 0; $abidx < 5; $abidx++) {
										$RTF->cell($sub_arr[$abidx], $heading_width[$HISTORY_NAME][$abidx+1], "center", "8", "TRBL");
										if ($j % 2==0 && $abidx==4) {
											$RTF->close_line();
										}
									}	// end for $abidx
									$j++;
								}	// foreach
								if ($j % 2==0) {
									$RTF->cell("", $heading_width[$HISTORY_NAME][0], "center", "8", "TRBL");
									for($abidx = 0; $abidx < 5; $abidx++) {
										$RTF->cell("", $heading_width[$HISTORY_NAME][$abidx+1], "center", "8", "TRBL");
										if ($abidx==4) {
											$RTF->close_line();
										}
									} // end for loop
								} // if ($j % 2==0)
							}else{
								$RTF->open_line();
								$RTF->set_font($font,16);
								$RTF->color("1");	// 0=DARKGRAY
								$RTF->cell($RTF->bold(1)."********** ����բ����� **********".$RTF->bold(0), "$page_ch", "center", "8", "RBL");
								$RTF->close_line();
							} // end if
						break;
				} // end switch
			} // end for
			
			if($data_count < $count_data) 	 $RTF->new_page();
		} // end while
	}else{
		$RTF->set_table_font($font, 14);
		$RTF->color("0");	// 0=BLACK
		
		$RTF->open_line();
//		$RTF->set_font($font,16);
		$RTF->color("1");	// 0=DARKGRAY
		$RTF->cell($RTF->bold(1)."********** ����բ����� **********".$RTF->bold(0), "$page_ch", "center", "8", "RBL");
		$RTF->close_line();
	} // end if

	$RTF->display($fname);

	ini_set("max_execution_time", 30);
?>