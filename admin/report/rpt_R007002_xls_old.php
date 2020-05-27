<?
	$time1 = time();

	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R007002_format.php");	// ��˹������е��������Ѻ�������¹�ٻẺ��§ҹ

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($search_per_type==1){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$order_posno_name = "b.POS_NO_NAME";
		$order_posno = "b.POS_NO";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$order_posno_name = "b.POEM_NO_NAME";
		$order_posno = "b.POEM_NO";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$order_posno_name = "b.POEMS_NO_NAME";
		$order_posno = "b.POEMS_NO";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$order_posno_name = "b.POT_NO_NAME";
		$order_posno = "b.POT_NO";
	} // end if
	
	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS = 1)";

	if(trim($search_month)) $show_month = $month_full[($search_month + 0)][TH];			$SHOW_MONTH = str_pad($search_month, 2, "0", STR_PAD_LEFT);		//��Ш���͹  ���������͡��
	if(trim($search_year)){
		$show_year = $search_year;
		$search_year -= 543;
	} // end if

//	�Ѻ�ӹǹ�ѹ�ӡ�� ���������ѹ��ش����� - �ҷԵ�� ����ѹ��ش�Ҫ��� (table )
	$search_date_min = "01/".$SHOW_MONTH."/$show_year";
	$search_date_max = date("t", mktime(0, 0, 0, $search_month, 1, $search_year)) ."/". $SHOW_MONTH ."/$show_year";

	$arr_temp = explode("/", $search_date_min);
	$START_DAY = $arr_temp[0];
	$START_MONTH = $arr_temp[1];
	$START_YEAR = $arr_temp[2] - 543;
	$search_date_min = "$START_YEAR-$START_MONTH-$START_DAY";

	$arr_temp = explode("/", $search_date_max);
	$END_DAY = $arr_temp[0];
	$END_MONTH = $arr_temp[1];
	$END_YEAR = $arr_temp[2] - 543;
	$search_date_max = "$END_YEAR-$END_MONTH-$END_DAY";
	
	$WORK_DAY = 1;
	while($START_YEAR!=$END_YEAR || $START_MONTH!=$END_MONTH || $START_DAY!=$END_DAY){
		$DAY_NUM = date("w", mktime(0, 0, 0, $START_MONTH, $START_DAY, $START_YEAR));

		if($DPISDB=="odbc") 
			$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
		elseif($DPISDB=="oci8") 
			$cmd = " select HOL_NAME from PER_HOLIDAY where SUBSTR(HOL_DATE, 1, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
		elseif($DPISDB=="mysql")
			$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
		$IS_HOLIDAY = $db_dpis->send_cmd($cmd);

		if($DAY_NUM > 0 && $DAY_NUM < 6 && !$IS_HOLIDAY) $WORK_DAY++;			//������ѹ��ش �Ѻ���ѹ�ӡ��
		
		$STARTDATE = date("Y-m-d", mktime(0, 0, 0, $START_MONTH, ($START_DAY + 1), $START_YEAR));
		$arr_temp = explode("-", $STARTDATE);
		$START_DAY = $arr_temp[2];
		$START_MONTH = $arr_temp[1];
		$START_YEAR = $arr_temp[0];
	} // end while

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG", $list_type)){
		$list_type_text = "";
		if($select_org_structure==1){	//����Ѻ�ͺ���§ҹ
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if(trim($search_org_id)){ 
			if($select_org_structure == 0) $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
			else if($select_org_structure == 1)  $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
		} // end if
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//�óշ����� ������������͡ check box list_type ���
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	$report_title = "$DEPARTMENT_NAME||��§ҹ����һ�Ժѵ��Ҫ��âͧ$PERSON_TYPE[$search_per_type]||��Ш���͹ $show_month �.�.$show_year||����ѹ�һ�Ժѵ��Ҫ��� $WORK_DAY �ѹ�ӡ��";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0702";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);

	// ��˹���ҵ�������;���� ��ǹ�����§ҹ
    	$ws_head_line1 = (array) null;
    	$ws_head_line2 = (array) null;
    	$ws_head_line3 = (array) null;
		for($i=0; $i < count($heading_text); $i++) {
			$buff = explode("|", $heading_text[$i]);
			$ws_head_line1[] = $buff[0];
			$ws_head_line2[] = $buff[1];
			$ws_head_line3[] = $buff[2];
		}
		$ws_colmerge_line1 = array(0,0,0,1,1,1,1,1,1,0,1,1,1,1,1,1,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_colmerge_line3 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL");
		$ws_border_line2 = array("LR","LR","LR","TRL","TRL","TRL","TRL","TRL","TRL","RL","TRL","TRL","TRL","TRL","TRL","TRL","RL");
		$ws_border_line3 = array("LBR","LBR","LBR","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		$ws_width = array(5,10,25,8,8,8,8,8,8,8,8,8,8,8,8,8,20);
	// ����á�˹���ҵ�������;���� ��ǹ�����§ҹ	

	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		global $arr_column_map, $arr_column_sel, $arr_column_align;
		global $ws_head_line1, $ws_head_line2, $ws_head_line3,  $ws_colmerge_line1, $ws_colmerge_line2, $ws_colmerge_line3;
		global $ws_border_line1, $ws_border_line2, $ws_border_line3;
		global $ws_headalign_line1, $ws_fontfmt_line1, $ws_width;

		// loop ��˹��������ҧ�ͧ column
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// �����੾�з�����͡����ʴ�
				$worksheet->set_column($colseq, $colseq, $ws_width[$arr_column_map[$i]]);
//				echo "$i-map[".$arr_column_map[$i]."], width=".$ws_width[$arr_column_map[$i]]."<br />";
				$colseq++;
			}
		}
		// loop ����� head ��÷Ѵ��� 1
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// �����੾�з�����͡����ʴ�
				$buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
		// loop ����� head ��÷Ѵ��� 2
		$xlsRow++;
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// �����੾�з�����͡����ʴ�
				$buff = explode("|",doo_merge_cell($ws_head_line2[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
		// loop ����� head ��÷Ѵ��� 3
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) { 	// �����੾�з�����͡����ʴ�
				$worksheet->write($xlsRow, $colseq, $ws_head_line3[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line3[$arr_column_map[$i]], $ws_colmerge_line3[$arr_column_map[$i]]));
				$colseq++;
			}
		}
	} // function		

	function check_is_holiday($SELECT_YEAR,$SELECT_MONTH,$SELECT_DAY){
		global $DPISDB,$db_dpis3;
		
		if($DPISDB=="odbc") 
			$cmd_is_holiday = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$SELECT_YEAR-$SELECT_MONTH-$SELECT_DAY' ";
		elseif($DPISDB=="oci8") 
			$cmd_is_holiday = " select HOL_NAME from PER_HOLIDAY where SUBSTR(HOL_DATE, 1, 10)='$SELECT_YEAR-$SELECT_MONTH-$SELECT_DAY' ";
		elseif($DPISDB=="mysql")
			$cmd_is_holiday = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$SELECT_YEAR-$SELECT_MONTH-$SELECT_DAY' ";
		$IS_HOLIDAY = $db_dpis3->send_cmd($cmd_is_holiday);
				
	return $IS_HOLIDAY;
	}

	function count_abs_days($AB_COUNT,$ABS_STARTDATE,$ABS_ENDDATE){
		global $search_month,$SHOW_MONTH,$search_year, $BKK_FLAG;

		//$ABS_DAY = 0;
		
		$arr_temp = explode("-", $ABS_STARTDATE);
		$START_DAY = $arr_temp[2]+0;
		$START_MONTH = $arr_temp[1];
		$START_YEAR = $arr_temp[0];
		
		$ABS_BETWEENDATE = $search_year."-".$SHOW_MONTH."-01";
		$arr_temp = explode("-", $ABS_BETWEENDATE);
		$BETWEEN_DAY = $arr_temp[2]+0;
		$BETWEEN_MONTH = $arr_temp[1];
		$BETWEEN_YEAR = $arr_temp[0];
		
		$arr_temp = explode("-", $ABS_ENDDATE);
		$END_DAY = $arr_temp[2]+0;
		$END_MONTH = $arr_temp[1];
		$END_YEAR = $arr_temp[0];
		
		$NUM_START_DAYS[$START_MONTH] = get_max_date($START_MONTH, $START_YEAR); 		//�ӹǹ�ѹ��������͹�������
		$NUM_START_ABSENT_DAYS[$START_MONTH] = ($NUM_START_DAYS[$START_MONTH] - $START_DAY);		//�ӹǹ�ѹ��ԧ��͹�������
		
		$NUM_BETWEEN_DAYS[$BETWEEN_MONTH] = get_max_date($BETWEEN_MONTH, $BETWEEN_YEAR); 		//�ӹǹ�ѹ��������͹�ç��ҧ (����Ҫ�ǧ�ҡ���� 2 ��͹)

		//$NUM_END_DAYS[$END_MONTH] = get_max_date($END_MONTH, $END_YEAR);				//�ӹǹ�ѹ��������͹����ش
		//$NUM_END_ABSENT_DAYS[$END_MONTH] = ($NUM_END_DAYS[$END_MONTH] - $END_DAY);				//�ӹǹ�ѹ��ԧ��͹����ش
		$NUM_END_ABSENT_DAYS[$END_MONTH] = $END_DAY; 

//while($START_YEAR!=$END_YEAR || $START_MONTH!=$END_MONTH || $START_DAY!=$END_DAY){		//<<<<<<<<<<<<<<<<<<<<
		if($AB_COUNT==2){	//���Ѻ = ������ҷԵ�� ����ѹ��ش�Ң���		//Numeric representation of the day of the week (0=sunday and 6=sat day)
			if($START_MONTH != $END_MONTH){
				if($SHOW_MONTH==$START_MONTH)	{		//��͹������͡��ҡѺ��͹�������
						//�Ҩӹǹ�ѹ   ( ���ѹ���������� 仨���з�觶֧ MAX ��͹��� )
						$iDateMax = date("t", mktime(0, 0, 0, $START_MONTH, 1, $START_YEAR)); 
						while($START_DAY<= $iDateMax){			//for($iDateMax; $iDateMax >= $START_DAY; $iDateMax--){	$i_DATE = $iDateMax;	
							$i_DATE = $iDateMax--;		//31 30 29   �����ѧ�������樹�֧�ѹ����������
							$DAY_NUM = date("w", mktime(0, 0, 0, $START_MONTH, $i_DATE, $START_YEAR));
							$IS_HOLIDAY = check_is_holiday($START_YEAR,$START_MONTH,str_pad($i_DATE, 2, "0", STR_PAD_LEFT));
							if($DAY_NUM > 0 && $DAY_NUM < 6 && !$IS_HOLIDAY)	$ABS_DAY++;
						} //end while
				}
				if($SHOW_MONTH==$END_MONTH)	{		//��͹������͡��ҡѺ��͹����ش
						//�Ҩӹǹ�ѹ   ( ���ѹ����������  ��� 1 仨���з�觶֧�ѹ�������ش��͹��� )
						$iDateMin = 1;
						for($iDateMin; $iDateMin <= $END_DAY; $iDateMin++){	
							$i_DATE = $iDateMin;	
							$DAY_NUM = date("w", mktime(0, 0, 0, $END_MONTH, $i_DATE, $END_YEAR));
							$IS_HOLIDAY = check_is_holiday($END_YEAR,$END_MONTH,str_pad($i_DATE, 2, "0", STR_PAD_LEFT));
							if($DAY_NUM > 0 && $DAY_NUM < 6 && !$IS_HOLIDAY)	$ABS_DAY++;
						} //end for
					}
				if(($SHOW_MONTH!=$START_MONTH) && ($SHOW_MONTH!=$END_MONTH)){
						//$ABS_DAY = $NUM_BETWEEN_DAYS[$SHOW_MONTH];
						//�Ҩӹǹ�ѹ�ͧ��͹��鹷�����������ѹ��� 1 �֧�ѹ�����͹ 
						$iDateMin = 1;
						$iDateMax = date("t", mktime(0, 0, 0, $BETWEEN_MONTH, 1, $BETWEEN_YEAR)); 	//�ѹ�����͹ ���
						for($iDateMin; $iDateMin <= $iDateMax; $iDateMin++){	
							$i_DATE = $iDateMin;	
							$DAY_NUM = date("w", mktime(0, 0, 0, $BETWEEN_MONTH, $i_DATE, $BETWEEN_YEAR));
							$IS_HOLIDAY = check_is_holiday($BETWEEN_YEAR,$BETWEEN_MONTH,str_pad($i_DATE, 2, "0", STR_PAD_LEFT));
							if($DAY_NUM > 0 && $DAY_NUM < 6 && !$IS_HOLIDAY)	$ABS_DAY++;
						} //end for 
				}
			}else{		//��͹����� = ��͹����ش 
				for($i_DATE=$START_DAY; $i_DATE<=$END_DAY; $i_DATE++){	//����ѹ������ҤԴ���֧�ѹ����ش
					//$ABS_DAY = ($END_DAY - $START_DAY);
					$DAY_NUM = date("w", mktime(0, 0, 0, $START_MONTH, $i_DATE, $START_YEAR));
					$IS_HOLIDAY = check_is_holiday($START_YEAR,$START_MONTH,str_pad($i_DATE, 2, "0", STR_PAD_LEFT));
					if($DAY_NUM > 0 && $DAY_NUM < 6 && !$IS_HOLIDAY)	$ABS_DAY++;
				} //end for
			}
		}else if($AB_COUNT==1){	//�Ѻ = ���͡��ش������ҷԵ�� ����ѹ��ش�Ҫ�����
			if($START_MONTH != $END_MONTH){
				if($SHOW_MONTH==$START_MONTH)	{		$ABS_DAY = $NUM_START_ABSENT_DAYS[$START_MONTH];		}
				if($SHOW_MONTH==$END_MONTH)	{			$ABS_DAY = $NUM_END_ABSENT_DAYS[$END_MONTH];				}
				if(($SHOW_MONTH!=$START_MONTH) && ($SHOW_MONTH!=$END_MONTH)){		//��͹������͡�����������ҧ��ǧ��͹������� �����͹����ش
					$ABS_DAY = ($NUM_BETWEEN_DAYS[$SHOW_MONTH]);
				}
			}else{ 
				$ABS_DAY = ($END_DAY - $START_DAY);
			}
		}
//} //end while
		
	return $ABS_DAY;
	} //end function

	function count_absent($PER_ID){
		global $DPISDB, $db_dpis2;
		global $arr_content, $data_count;
		global $search_year,$search_date_min, $search_date_max, $SHOW_MONTH, $BKK_FLAG;
		//global $search_condition;
		$search_condition="";
		unset($arr_search_condition);

		$ABS_CUT_PERIOD = 0;
/*
		if(trim($search_date_min)){
			if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '$search_date_min')";
			elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(ABS_STARTDATE, 1, 10) >= '$search_date_min')";
			elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '$search_date_min')";
		} // end if
		if(trim($search_date_max)){
			if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) <= '$search_date_max')";
			elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(ABS_STARTDATE, 1, 10) <= '$search_date_max')";
			elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) <= '$search_date_max')";
		} // end if
*/
		// ������鹤����ѹ�Ҩҡ��͹ ���Ҥ� �֧��͹�Ѩ�غѹ�����Ңͧ�չ��
		//####(ABS_STARTDATE) �ѹ����������	//####(ABS_ENDDATE)	�ѹ�������ش
		if($DPISDB=="odbc" || $DPISDB=="mysql")		$column_startdate="LEFT(ABS_STARTDATE, 10)";				$column_enddate="LEFT(ABS_ENDDATE, 10)";
		if($DPISDB=="oci8")												$column_startdate="SUBSTR(ABS_STARTDATE, 1, 10)";		$column_enddate="SUBSTR(ABS_ENDDATE, 1, 10)";
		//����Ѻ��͹ ���Ҥ� ����������Ңͧ�շ��������
		$search_condition_january = "";
		$search_date_minjanuary = $search_year."-01-01";		$search_date_maxjanuary = $search_year."-01-31";
		/*if(trim($search_date_max)==trim($search_date_maxjanuary)){
			$arr_temp = explode("-", $search_date_max);
			$LASTYEAR_JANUARY_DAY = str_pad($arr_temp[2]+0, 2, "0", STR_PAD_LEFT);
			$LASTYEAR_JANUARY_MONTH = $arr_temp[1];
			$LASTYEAR_JANUARY_YEAR = $arr_temp[0]-1;	
			$search_date_lastyear_january = "$LASTYEAR_JANUARY_YEAR-$LASTYEAR_JANUARY_MONTH-$LASTYEAR_JANUARY_DAY";
			$search_condition_january = "$column_startdate > '$search_date_lastyear_january' AND ";
		}*/
		
		$search_condition_begin = "";
		if(trim($search_date_max) == trim($search_date_maxjanuary)){		//���Ҥ�
			$arr_search_condition[] =  "(($column_startdate < '$search_date_min' OR $column_startdate >= '$search_date_min') AND $column_startdate <= '$search_date_max' AND $column_enddate >= '$search_date_min')";
		}else{
			$arr_search_condition[] = "(
			(($column_startdate < '$search_date_minjanuary' OR $column_startdate >= '$search_date_minjanuary') AND $column_startdate <= '$search_date_max') AND 
			($column_enddate >= '$search_date_min')
			)";
		}		
		//  "($search_condition_date OR ($column_startdate >= '$search_date_min' AND $column_enddate <= '$search_date_max'))";	
		if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);
		if ($BKK_FLAG==1) 
			$ab_code = "'1','3','6','12'";			
		else
			$ab_code = "'01','03','04','10'";
		$arr_ab_code = explode(",",$ab_code);
		
		
for($i=0; $i < count($arr_ab_code); $i++){
$cmd = " select		a.AB_CODE, ABS_ID, ABS_DAY, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD,b.AB_COUNT 
						 from 		PER_ABSENTHIS a, PER_ABSENTTYPE b 
						 where	PER_ID=$PER_ID and a.AB_CODE =$arr_ab_code[$i] and a.AB_CODE=b.AB_CODE
						 				$search_condition
						 order by ABS_STARTDATE ";	

		$db_dpis2->send_cmd($cmd);
//		echo "normal abs :  $cmd<hr><hr>";
//		$db_dpis2->show_error();
		while($data2 = $db_dpis2->get_array()){		//	$data2 = $db_dpis2->get_array();
			$ABS_ID = trim($data2[ABS_ID]);
			$AB_CODE = trim($data2[AB_CODE]);
			if ($BKK_FLAG==1) {
				if ($AB_CODE=="1") $AB_CODE = "01";
				elseif ($AB_CODE=="3") $AB_CODE = "03";
				elseif ($AB_CODE=="6") $AB_CODE = "04";
				elseif ($AB_CODE=="12") $AB_CODE = "10";
//				elseif ($AB_CODE=="5") $AB_CODE = "11";
			}
			$AB_COUNT = trim($data2[AB_COUNT]);
			$ABS_DAY = $data2[ABS_DAY];
			$ABS_STARTDATE = substr($data2[ABS_STARTDATE], 0, 10);
			$ABS_STARTPERIOD = $data2[ABS_STARTPERIOD];
			$ABS_ENDDATE = substr($data2[ABS_ENDDATE], 0, 10);
			$ABS_ENDPERIOD = $data2[ABS_ENDPERIOD];
			
			$arr_temp = explode("-", $ABS_STARTDATE);
			$START_DATE = $arr_temp[2]+0;
			$START_MONTH = $arr_temp[1]+0;
			$START_YEAR = $arr_temp[0] - 543;			//???????????/
			if($ABS_STARTPERIOD==1) $START_DATE .= " �";			//�����ѹ ���
			elseif($ABS_STARTPERIOD==2) $START_DATE .= " �";	//�����ѹ ����
			
			$arr_temp = explode("-", $ABS_ENDDATE);
			$END_DATE = $arr_temp[2]+0;
			$END_MONTH = $arr_temp[1]+0;
			$END_YEAR = $arr_temp[0] - 543;			//???????????/
			if($ABS_ENDPERIOD==1) $END_DATE .= " �";				//�����ѹ ���
			elseif($ABS_ENDPERIOD==2) $END_DATE .= " �";		//�����ѹ ���
			
			if($ABS_STARTDATE == $ABS_ENDDATE) $ABS_DURATION[$AB_CODE] = $START_DATE;
			elseif($START_MONTH == $END_MONTH) $ABS_DURATION[$AB_CODE] = $START_DATE ."-". $END_DATE;
			else $ABS_DURATION[$AB_CODE] = "$START_DATE/$START_MONTH-$END_DATE/$END_MONTH";	//��͹����� �Ѻ ��͹����ش ������͹�ѹ

			$AB_CODE_EXIST .= $AB_CODE.",";
			$ABS_ID_EXIST .= $ABS_ID.",";
			
			$ABS_DURATION[$AB_CODE] = $ABS_DURATION[$AB_CODE]?$ABS_DURATION[$AB_CODE]:"";
			$ABS_DURATION_EXIST[$AB_CODE] .= $ABS_DURATION[$AB_CODE].","; 
			
			//$ABS_DAY = $ABS_DAY?$ABS_DAY:"-";
			//$ABS_DAY_EXIST .= $ABS_DAY.","; 
			
			//${"NUM_".$AB_CODE} +=  1;
			//${"NUM_".$AB_CODE."_EXIST"} .= ${"NUM_".$AB_CODE}.",";
			
			$arr_content[$data_count][AB_CODE] = $AB_CODE_EXIST;
			$arr_content[$data_count][("ABS_ID_".$AB_CODE)] = substr($ABS_ID_EXIST,0,-1);
			$arr_content[$data_count][("DATE_".$AB_CODE)] = substr($ABS_DURATION_EXIST[$AB_CODE],0,-1);			//.= (($arr_content[$data_count][("DATE_".$AB_CODE)])?",":"") . $ABS_DURATION;
			//--------------------------------------------------------------------------------------------------------------
			$ABS_DAY_EXIST[$AB_CODE] += count_abs_days($AB_COUNT,$ABS_STARTDATE,$ABS_ENDDATE);		//��Ǩ�ͺ����ҹѺ�ѹ��ش ������ҷԵ��
			// ��Ҥ����ѹ��ѡ
			if(($ABS_STARTPERIOD==1 || $ABS_STARTPERIOD==2) || ($ABS_ENDPERIOD==1 || $ABS_ENDPERIOD==2)){//����Ѻ�� ��֧�ѹ��Ҥ����ѹ����
				$ABS_DAY_EXIST[$AB_CODE] = ($ABS_DAY_EXIST[$AB_CODE] - 0.5);
			}
			//--------------------------------------------------------------------------------------------------------------
			$arr_content[$data_count][("DAY_".$AB_CODE)] = $ABS_DAY_EXIST[$AB_CODE];		//+= $ABS_DAY;		= $ABS_DAY?$ABS_DAY:"-";
			$arr_content[$data_count][("NUM_".$AB_CODE)] += 1; //${"NUM_".$AB_CODE."_EXIST"};
		} // end while
}  //end for

		//----------------�ҡ�����������-------------------------------------------------------------------------
		$cmd = " select		a.AB_CODE, b.AB_NAME,b.AB_COUNT, ABS_ID, ABS_DAY, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD
						 from 		PER_ABSENTHIS a, PER_ABSENTTYPE b
						 where	PER_ID=$PER_ID and a.AB_CODE not in ($ab_code) and a.AB_CODE=b.AB_CODE
						 				$search_condition
						 order by ABS_STARTDATE ";
		$db_dpis2->send_cmd($cmd);
//		echo "other abs :  $cmd<hr>";
//		$db_dpis2->show_error();
		$data_row = 0;		$ABS_CUT_PERIOD = 0;
		while($data2 = $db_dpis2->get_array()){
			$data_row++;
			if($data_row > 1){ 
				$data_count++;
				
				$arr_content[$data_count][type] = "CONTINUE";
			} // end if
 
 			$AB_COUNT = trim($data2[AB_COUNT]);
			$ABS_DAY = $data2[ABS_DAY];
			$AB_NAME = trim($data2[AB_NAME]);

			$ABS_STARTDATE = substr($data2[ABS_STARTDATE], 0, 10);
			$ABS_STARTPERIOD = $data2[ABS_STARTPERIOD];
			$ABS_ENDDATE = substr($data2[ABS_ENDDATE], 0, 10);
			$ABS_ENDPERIOD = $data2[ABS_ENDPERIOD];
			
			$arr_temp = explode("-", $ABS_STARTDATE);
			$START_DATE = $arr_temp[2]+0;
			$START_MONTH = $arr_temp[1]+0;
			$START_YEAR = $arr_temp[0] - 543;			//???????????/
			if($ABS_STARTPERIOD==1) $START_DATE .= " �";		//�����ѹ ���
			elseif($ABS_STARTPERIOD==2) $START_DATE .= " �";	//�����ѹ ����
			
			$arr_temp = explode("-", $ABS_ENDDATE);
			$END_DATE = $arr_temp[2]+0;
			$END_MONTH = $arr_temp[1]+0;
			$END_YEAR = $arr_temp[0] - 543;			//???????????/
			if($ABS_ENDPERIOD==1) $END_DATE .= " �";	//�����ѹ ���
			elseif($ABS_ENDPERIOD==2) $END_DATE .= " �";	//�����ѹ ����

			if($ABS_STARTDATE == $ABS_ENDDATE) $ABS_DURATION = $START_DATE;
			elseif($START_MONTH == $END_MONTH) $ABS_DURATION = $START_DATE ."-". $END_DATE;
			else $ABS_DURATION = "$START_DATE/$START_MONTH-$END_DATE/$END_MONTH";	//��͹����� �Ѻ ��͹����ش ������͹�ѹ
/* jerd
			//----------------�ҡ�����������-------------------------------------------------------------------------
			$ABS_DAY += count_abs_days($AB_COUNT,$ABS_STARTDATE,$ABS_ENDDATE);
			// ��Ҥ����ѹ��ѡ
			if(($ABS_STARTPERIOD==1 || $ABS_STARTPERIOD==2) || ($ABS_ENDPERIOD==1 || $ABS_ENDPERIOD==2)){//����Ѻ�� ��֧�ѹ��Ҥ����ѹ����
				$ABS_DAY = ($ABS_DAY - 0.5);
			}
			//---------------------------------------------------------------------------------------------------------------
*/
			$arr_content[$data_count][DATE_OTHER] = $ABS_DURATION;
			$arr_content[$data_count][DAY_OTHER] = $ABS_DAY?$ABS_DAY:"-";
			$arr_content[$data_count][DETAIL_OTHER] = $AB_NAME;
			
			//$data_count++;
		} // end while
	} // function
	
	if($DPISDB=="odbc"){
		$cmd = " select			e.ORG_ID_REF as MINISTRY_ID, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, 
												b.ORG_ID, c.ORG_NAME, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME,$order_posno,$order_posno_name
						 from		(
											(
												(
													PER_PERSONAL a
													inner join $position_table b on ($position_join)
												) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
										) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
						 $search_condition
						 order by		e.ORG_ID_REF, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, $order_posno_name, iif(isnull($order_posno),0,$order_posno), a.PER_NAME , a.PER_SURNAME ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace("where", "and", $search_condition);
		$cmd = " select			e.ORG_ID_REF as MINISTRY_ID, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, 
												b.ORG_ID, c.ORG_NAME, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME,$order_posno,$order_posno_name
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, PER_ORG e
						 where		$position_join and b.ORG_ID=c.ORG_ID and a.PN_CODE=d.PN_CODE(+) and a.DEPARTMENT_ID=e.ORG_ID(+)
											$search_condition
						 order by		e.ORG_ID_REF, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, $order_posno_name, TO_NUMBER($order_posno), a.PER_NAME, a.PER_SURNAME ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			e.ORG_ID_REF as MINISTRY_ID, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, 
												b.ORG_ID, c.ORG_NAME, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME,$order_posno,$order_posno_name
						 from		(
											(
												(
													PER_PERSONAL a
													inner join $position_table b on ($position_join)
												) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
										) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
						 $search_condition
						 order by		e.ORG_ID_REF, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, $order_posno_name, $order_posno+0, a.PER_NAME, a.PER_SURNAME ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	} 
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "cmd=$cmd ($count_data)<br>";
//	$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	$ORG_ID = -1;
	$sheet_limit = 10;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";
	
	$arr_file = (array) null;
	$f_new = false;
	$fname= "../../Excel/tmp/rpt_R007002_xls";
	$fname1 = $fname.".xls";
	$fnum = 0; $fnumtext="";
	$workbook = new writeexcel_workbook($fname1);

	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.set_format.php";	// use set_format(set of parameter) funtion , help is in file
	require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
	//====================== SET FORMAT ======================//

	while($data = $db_dpis->get_array()){		
		if($ORG_ID != $data[ORG_ID]){
			$ORG_ID = $data[ORG_ID];
			$ORG_NAME = $data[ORG_NAME];
			if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;
			
			$arr_content[$data_count][type] = "ORG";
			$arr_content[$data_count][name] = $ORG_NAME;

			$data_row = 0;
			$data_count++;
		} // end if
		
		$data_row++;

		$PER_ID = $data[PER_ID];
		$PN_NAME = $data[PN_NAME];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];

		if($search_per_type==1) { $POS_NO = trim($data[POS_NO_NAME]).' '.trim($data[POS_NO]); }
		if($search_per_type==2) { $POS_NO = trim($data[POEM_NO_NAME]).trim($data[POEM_NO]); }
		if($search_per_type==3) { $POS_NO  = trim($data[POEMS_NO_NAME]).trim($data[POEMS_NO]); }
		if($search_per_type==4) { $POS_NO  = trim($data[POT_NO_NAME]).trim($data[POT_NO]); }

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][pos_no] = $POS_NO;
		$arr_content[$data_count][per_id] = $PER_ID;
		$arr_content[$data_count][data_row] = "$data_row";
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		
		count_absent($PER_ID);
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$colshow_cnt=0;		// �Ҩӹǹ column ����ʴ���ԧ
		for($i=0; $i<count($arr_column_sel); $i++){
			if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
		}

		// ��˹���ҵ�������;���� ��ǹ������
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("C","C","L","L","R","R","L","R","R","R","L","R","L","R","L","R","L");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","","","","","","","","","");
		// ����˹���ҵ�������;���� ��ǹ������
		
//		$pdf->report_title = "$DEPARTMENT_NAME||��§ҹ����һ�Ժѵ��Ҫ��âͧ$PERSON_TYPE[$search_per_type] $NAME||��Ш���͹ $show_month �.�. ".(($NUMBER_DISPLAY==2)?convert2thaidigit($show_year):$show_year)." ||����ѹ�һ�Ժѵ��Ҫ���".(($NUMBER_DISPLAY==2)?convert2thaidigit($WORK_DAY):$WORK_DAY)." �ѹ�ӡ��";

//		print_header();

		$sheet_name = "sheet";
		$data_i = 0;
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$DATA_ROW = $arr_content[$data_count][data_row];
			$POS_NO = $arr_content[$data_count][pos_no];
			$PER_ID = $arr_content[$data_count][per_id];
			$NAME = $arr_content[$data_count][name];
			$DATE_01 = $arr_content[$data_count][DATE_01];
			$DAY_01 = $arr_content[$data_count][DAY_01];
			$NUM_01 = $arr_content[$data_count][NUM_01];
			$DATE_03 = $arr_content[$data_count][DATE_03];
			$DAY_03 = $arr_content[$data_count][DAY_03];
			$NUM_03 = $arr_content[$data_count][NUM_03];
			$NUM_TOT = $NUM_01 + $NUM_03;
			$DATE_10 = $arr_content[$data_count][DATE_10];
			$DAY_10 = $arr_content[$data_count][DAY_10];
			$DATE_04 = $arr_content[$data_count][DATE_04];
			$DAY_04 = $arr_content[$data_count][DAY_04];
			$DATE_11 = $arr_content[$data_count][DATE_11];
			$DAY_11 = $arr_content[$data_count][DAY_11];
			$DATE_OTHER = $arr_content[$data_count][DATE_OTHER];
			$DAY_OTHER = $arr_content[$data_count][DAY_OTHER];
			$DETAIL_OTHER = $arr_content[$data_count][DETAIL_OTHER];

			// �礨��������� $data_limit
			if ($data_count > 0 && ($data_i == $file_limit)) {
//				echo "$data_count>>$xls_fname>>$fname1<br>";
				$workbook->close();
				$arr_file[] = $fname1;
	
				$fnum++;
				$fname1=$fname."_$fnum.xls";
				$workbook = new writeexcel_workbook($fname1);
	
				//====================== SET FORMAT ======================//
				require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
				//====================== SET FORMAT ======================//
	
				$f_new = true;
			};
			// �礨��������� $data_limit
			if($REPORT_ORDER == "ORG" || ($data_count > 0 && ($data_i  % $data_limit) == 0) || $f_new){
				if ($f_new) {
					$sheet_name = "sheet";
					$sheet_no=0; $sheet_no_text="";
					if($data_count > 0) $count_org_ref++;
					$data_i = 0;
					$f_new = false;
				} else if (($data_count > 0 && ($data_i % $data_limit) == 0) || $REPORT_ORDER == "ORG") {
					$sheet_no++;
					if ($sheet_no > $sheet_limit) {
						$workbook->close();
						$arr_file[] = $fname1;
			
						$fnum++;
						$fname1=$fname."_$fnum.xls";
						$workbook = new writeexcel_workbook($fname1);
			
						//====================== SET FORMAT ======================//
						require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
						//====================== SET FORMAT ======================//

						$sheet_name = "sheet";
						$sheet_no=0; $sheet_no_text="";
						if($data_count > 0) $count_org_ref++;
						$data_i = 0;
					} else {
						$sheet_no_text = "_$sheet_no";
					}
				}

//				echo "data_i=$data_i-f_new=$f_new-REPORT_ORDER=$REPORT_ORDER-$sheet_name".($count_org_ref?"_$count_org_ref":"").$sheet_no_text."";
				
				$worksheet = &$workbook->addworksheet("$sheet_name".($count_org_ref?"_$count_org_ref":"").$sheet_no_text);
				$worksheet->set_margin_right(0.50);
				$worksheet->set_margin_bottom(1.10);

				$xlsRow = 0;
				$arr_title = explode("||", $report_title);
				for($i=0; $i<count($arr_title); $i++){
					$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
					for($j=0; $j < $colshow_cnt-1; $j++) 
						$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$xlsRow++;
				} //for($i=0; $i<count($arr_title); $i++){
		
				if($company_name){
					$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
					for($j=0; $j < $colshow_cnt-1; $j++) 
						$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$xlsRow++;
				} //if($company_name){
				
				print_header();

			} // end if

			if($REPORT_ORDER == "ORG"){
				$xlsRow = 0;
				$arr_title = explode("||", $report_title);
				for($i=0; $i<count($arr_title); $i++){
					$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
					for($j=0; $j < $colshow_cnt-1; $j++) 
						$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$xlsRow++;
				} //for($i=0; $i<count($arr_title); $i++){
		
				if($company_name){
					$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
					for($j=0; $j < $colshow_cnt-1; $j++) 
						$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$xlsRow++;
				} //if($company_name){
				
				print_header();		

				$arr_data = (array) null;
				$arr_data[] = "<**1**>$NAME";
				$arr_data[] = "<**1**>$NAME";
				$arr_data[] = "<**1**>$NAME";

				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
					
				$xlsRow++;
				$colseq=0;
				for($i=0; $i < count($arr_column_map); $i++) {
					if ($arr_column_sel[$arr_column_map[$i]]==1) {
						if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ��ҹ��� map �����º��������
						else $ndata = $arr_data[$arr_column_map[$i]];
						$buff = explode("|",doo_merge_cell($ndata, $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
						$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
						$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_2[$arr_column_map[$i]], "L", $border, $merge));
						$colseq++;
					}
				}
			}elseif($REPORT_ORDER == "CONTENT"){
				$arr_data = (array) null;
				$arr_data[] = $DATA_ROW;
				$arr_data[] = $POS_NO;
				$arr_data[] = $NAME;
				$arr_data[] = $DATE_01;
				$arr_data[] = $DAY_01;
				$arr_data[] = $NUM_01;
				$arr_data[] = $DATE_03;
				$arr_data[] = $DAY_03;
				$arr_data[] = $NUM_03;
				$arr_data[] = $NUM_TOT;
				$arr_data[] = $DATE_10;
				$arr_data[] = $DAY_10;
				$arr_data[] = $DATE_04;
				$arr_data[] = $DAY_04;
				$arr_data[] = $DATE_OTHER;
				$arr_data[] = $DAY_OTHER;
				$arr_data[] = $DETAIL_OTHER;

//				echo "...........DATA_ROW=$DATA_ROW<br>";
				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
					
				$xlsRow++;
				$colseq=0;
				for($i=0; $i < count($arr_column_map); $i++) {
					if ($arr_column_sel[$arr_column_map[$i]]==1) {
						if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ��ҹ��� map �����º��������
						else $ndata = $arr_data[$arr_column_map[$i]];
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
						$colseq++;
					}
				}
			}elseif($REPORT_ORDER == "CONTINUE"){
				$arr_data = (array) null;
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = $DATE_OTHER;
				$arr_data[] = $DAY_OTHER;
				$arr_data[] = $DETAIL_OTHER;

				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
					
				$xlsRow++;
				$colseq=0;
				for($i=0; $i < count($arr_column_map); $i++) {
					if ($arr_column_sel[$arr_column_map[$i]]==1) {
						if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ��ҹ��� map �����º��������
						else $ndata = $arr_data[$arr_column_map[$i]];
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
						$colseq++;
					}
				}
			} // end if
			$data_i++;
		} // end for				
	}else{
		$xlsRow = 0;
		$arr_data = (array) null;
		$arr_data[] = "<**1**>***** ����բ����� *****";
		$arr_data[] = "<**1**>***** ����բ����� *****";
		$arr_data[] = "<**1**>***** ����բ����� *****";
		$arr_data[] = "<**1**>***** ����բ����� *****";
		$arr_data[] = "<**1**>***** ����բ����� *****";
		$arr_data[] = "<**1**>***** ����բ����� *****";
		$arr_data[] = "<**1**>***** ����բ����� *****";
		$arr_data[] = "<**1**>***** ����բ����� *****";
		$arr_data[] = "<**1**>***** ����բ����� *****";
		$arr_data[] = "<**1**>***** ����բ����� *****";
		$arr_data[] = "<**1**>***** ����բ����� *****";
		$arr_data[] = "<**1**>***** ����բ����� *****";
		$arr_data[] = "<**1**>***** ����բ����� *****";
		$arr_data[] = "<**1**>***** ����բ����� *****";
		$arr_data[] = "<**1**>***** ����բ����� *****";
		$arr_data[] = "<**1**>***** ����բ����� *****";
		$arr_data[] = "<**1**>***** ����բ����� *****";

		$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
			
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($arr_column_map); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {
				if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ��ҹ��� map �����º��������
				else $ndata = $arr_data[$arr_column_map[$i]];
				$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
				$colseq++;
			}
		}
	} // end if

	$workbook->close();
	$arr_file[] = $fname1;

	ini_set("max_execution_time", 30);
	
	include("../current_location.html");

	if (count($arr_file) > 0) {
		echo "<BR>";
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "��� Excel ������ҧ��<BR><br>";
		for($i_file = 0; $i_file < count($arr_file); $i_file++) {
			echo "---->".($i_file+1).":<a href=\"".$arr_file[$i_file]."\">".$arr_file[$i_file]."</a><br>";
		}
	}
	echo "<BR>";
	$time2 = time();
	$tdiff = $time2 - $time1;
	$s = $tdiff % 60;	// �Թҷ�
	$m = floor($tdiff / 60);	// �ҷ�
	$h = floor($m / 60);	// ��.
	$m = $m % 60;	// �ҷ�
	$show_lap = ($h?"$h ��. ":"").($m?"$m �ҷ� ":"")."$s �Թҷ�";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "�����:".date("d-m-Y h:i:s",$time1)." ��:".date("d-m-Y h:i:s",$time2)." ������ $show_lap [$tdiff]<br>";
	echo "<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "����÷ӧҹ<br>";
?>