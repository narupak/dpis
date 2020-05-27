<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$UPDATE_USER = 1;
	$UPDATE_DATE = date("Y-m-d H:i:s");

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
	} // end switch case

	if( $command=='CONVERT' ) {   
		$cmd = " DELETE FROM PER_TRAINING WHERE UPDATE_USER = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_TRAIN WHERE UPDATE_USER = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(TRN_ID) as MAX_ID from PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT TYPE, ORGMARK, a.COURSEID, COURSENAME, CLASS as TRN_NO, ORGANIZER, DURATION, YEAR, PLACE, FEE, NOTE, TNAME, TPOST, b.NAME1	
						  FROM COURSE a, TRANSCOURSE b 
						  WHERE a.COURSEID = b.COURSEID
						  ORDER BY a.COURSEID ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
		while($data = $db_dpis35->get_array()){
			$PER_TRAINING++;
			$TYPE = trim($data[TYPE]);
			$ORGMARK = trim($data[ORGMARK]);
			$TR_CODE = trim($data[COURSEID]);
			$TR_NAME = trim($data[COURSENAME]);
			$TRN_NO = $data[TRN_NO];
			$TRN_ORG = trim($data[ORGANIZER]);
			$TRN_REMARK = $data[DURATION];
			$YEAR = $data[YEAR];
			$TRN_PLACE = trim($data[PLACE]);
			$FEE = $data[FEE];
			$NOTE = trim($data[NOTE]);
			$TNAME = trim($data[TNAME]);
			$TPOST = trim($data[TPOST]);
			$NAME1 = trim($data[NAME1]);
			$NAME1 = str_replace("     ", " ", trim($NAME1));
			$NAME1 = str_replace("   ", " ", trim($NAME1));
			$NAME1 = str_replace("  ", " ", trim($NAME1));
			$temp_array = explode(" ",$NAME1);
			$PER_NAME = $temp_array[0];
			$PER_SURNAME = $temp_array[1];
			if ($temp_array[2]) $PER_SURNAME .= " " . $temp_array[2];
			if ($temp_array[3]) $PER_SURNAME .= " " . $temp_array[3];
			$TRN_STARTDATE = $TRN_ENDDATE = ($YEAR - 543) . "-00-00";
// select distinct duration from course order by duration desc
			if ($TRN_REMARK=="����� 10�ҷԵ��11,�����17-�ҷԵ�� 18 �.�. 2548") {
				$TRN_STARTDATE = "2005-09-10";
				$TRN_ENDDATE = "2005-09-18";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="�ѹ������� 18-�ҷԵ���� 19 �.�.2549") {
				$TRN_STARTDATE = "2006-09-18";
				$TRN_ENDDATE = "2006-09-19";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="��.�.-�.�.51 (੾���ѹ�ѧ�������ѹ����ʺ�� 17.00-19.00 �. ��� 50 �������)") {
				$TRN_STARTDATE = "2008-04-00";
				$TRN_ENDDATE = "2008-08-00";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="��.�.-�.�.51 (੾���ѹ�ѹ�������ѹ�ظ 17.00-19.00 �. ��� 50 �������)") {
				$TRN_STARTDATE = "2008-04-00";
				$TRN_ENDDATE = "2008-08-00";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="��.�.-�.�.43") {
				$TRN_STARTDATE = "2000-03-00";
				$TRN_ENDDATE = "2000-05-00";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="�.�. 2546") {
				$TRN_STARTDATE = "2003-11-00";
				$TRN_ENDDATE = "2003-11-00";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="�.�.-�.�.45") {
				$TRN_STARTDATE = "2002-10-00";
				$TRN_ENDDATE = "2002-12-00";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="��ǧ��� 1 24-28 ��.�.2548 ��ǧ��� 2 ����� 3  29 �.�.-1 ��.�.49 ����� 4  19-22 ���.49") {
				$TRN_STARTDATE = "2006-04-24";
				$TRN_ENDDATE = "2007-06-22";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="9-19 ����Ҥ� 2548") {
				$TRN_STARTDATE = "2005-05-09";
				$TRN_ENDDATE = "2005-05-19";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK=="9-19 �.�. 45") {
				$TRN_STARTDATE = "2002-09-09";
				$TRN_ENDDATE = "2002-09-19";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK=="9-13 ��.�.52") {
				$TRN_STARTDATE = "2009-03-09";
				$TRN_ENDDATE = "2009-03-13";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="9-13 ��.�. 40") {
				$TRN_STARTDATE = "1997-06-09";
				$TRN_ENDDATE = "1997-06-13";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="9-13 �.�. 40") {
				$TRN_STARTDATE = "1997-05-09";
				$TRN_ENDDATE = "1997-05-13";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="9-13 �á�Ҥ� 2550") {
				$TRN_STARTDATE = "2007-07-09";
				$TRN_ENDDATE = "2007-07-13";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="9-13 �.�.45") {
				$TRN_STARTDATE = "2002-09-09";
				$TRN_ENDDATE = "2002-09-13";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="9-12 ���Ҥ� 2549") {
				$TRN_STARTDATE = "2006-01-09";
				$TRN_ENDDATE = "2006-01-12";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="9-12 �.�.2547") {
				$TRN_STARTDATE = "2004-01-09";
				$TRN_ENDDATE = "2004-01-12";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="9-12 �.�. 42") {
				$TRN_STARTDATE = "1999-02-09";
				$TRN_ENDDATE = "1999-02-12";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="9-11,16-18 �.�. 40") {
				$TRN_STARTDATE = "1997-05-09";
				$TRN_ENDDATE = "1997-05-18";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="9-11,13 �ԧ�Ҥ� 2547") {
				$TRN_STARTDATE = "2004-08-09";
				$TRN_ENDDATE = "2004-08-13";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="9-11,13 �.�. 47") {
				$TRN_STARTDATE = "2004-08-09";
				$TRN_ENDDATE = "2004-08-13";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="9-11 �չҤ� 2547") {
				$TRN_STARTDATE = "2004-03-09";
				$TRN_ENDDATE = "2004-03-11";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="9-11 ��.�. 47") {
				$TRN_STARTDATE = "2004-03-09";
				$TRN_ENDDATE = "2004-03-13";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="9-11 ��.�. 40") {
				$TRN_STARTDATE = "1997-06-09";
				$TRN_ENDDATE = "1997-06-13";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="9-11 �.�. 44") {
				$TRN_STARTDATE = "2001-11-09";
				$TRN_ENDDATE = "2001-11-11";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="9-11 �.�.44") {
				$TRN_STARTDATE = "2001-05-09";
				$TRN_ENDDATE = "2001-05-11";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="9-10,12-13 ����Ҥ� 2548") {
				$TRN_STARTDATE = "2005-05-09";
				$TRN_ENDDATE = "2005-05-13";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="9-10 �ԧ�Ҥ� 2552") {
				$TRN_STARTDATE = "2009-08-09";
				$TRN_ENDDATE = "2009-08-10";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="9-10 ��.�. 52") {
				$TRN_STARTDATE = "2009-03-09";
				$TRN_ENDDATE = "2009-03-10";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="9-10 ��.�. 40") {
				$TRN_STARTDATE = "1997-06-09";
				$TRN_ENDDATE = "1997-06-10";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="9-10 �.�.2550") {
				$TRN_STARTDATE = "2007-01-09";
				$TRN_ENDDATE = "2007-01-10";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="9-10 �á�Ҥ� 2550") {
				$TRN_STARTDATE = "2007-07-09";
				$TRN_ENDDATE = "2007-07-10";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="9 ��.�.-18 �.�. 40 (੾���ѹ�ѹ���)") {
				$TRN_STARTDATE = "1997-06-09";
				$TRN_ENDDATE = "1997-08-18";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="9 �.�.-1 �.�.43") {
				$TRN_STARTDATE = "2000-11-09";
				$TRN_ENDDATE = "2000-12-01";
				$TRN_DAY = 23;
			} elseif ($TRN_REMARK=="9 �.�.39 - 2 �.�.40") {
				$TRN_STARTDATE = "1996-12-09";
				$TRN_ENDDATE = "1997-02-02";
				$TRN_DAY = 56;
			} elseif ($TRN_REMARK=="9 �.�.-9 ��.�.47") {
				$TRN_STARTDATE = "2004-02-09";
				$TRN_ENDDATE = "2004-04-09";
				$TRN_DAY = 61;
			} elseif ($TRN_REMARK=="9 �.�.47") {
				$TRN_STARTDATE = "2004-07-09";
				$TRN_ENDDATE = "2004-07-09";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="9 �.�.-13 �.�. 36") {
				$TRN_STARTDATE = "1993-07-09";
				$TRN_ENDDATE = "1993-10-13";
				$TRN_DAY = 97;
			} elseif ($TRN_REMARK=="9 - 20 ���Ҥ� 2549") {
				$TRN_STARTDATE = "2006-01-09";
				$TRN_ENDDATE = "2006-01-20";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="9 - 11  �.�.49") {
				$TRN_STARTDATE = "2006-08-09";
				$TRN_ENDDATE = "2006-08-11";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="8-9 ��.�. 44") {
				$TRN_STARTDATE = "2001-03-08";
				$TRN_ENDDATE = "2001-03-09";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="8-9 ��.�.42") {
				$TRN_STARTDATE = "1999-06-08";
				$TRN_ENDDATE = "1999-06-09";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="8-9 ��.�. 52") {
				$TRN_STARTDATE = "2009-06-08";
				$TRN_ENDDATE = "2009-06-09";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="8-9 �.�. ��� 15 �.�.-26 ��.�.42") {
				$TRN_STARTDATE = "1999-02-08";
				$TRN_ENDDATE = "1999-03-26";
				$TRN_DAY = 42;
			} elseif ($TRN_REMARK=="8-9 �.�. 42") {
				$TRN_STARTDATE = "1999-07-08";
				$TRN_ENDDATE = "1999-07-09";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="8-27 �.�.43") {
				$TRN_STARTDATE = "2000-11-08";
				$TRN_ENDDATE = "2000-11-27";
				$TRN_DAY = 20;
			} elseif ($TRN_REMARK=="8-12 ��.�.42") {
				$TRN_STARTDATE = "1999-03-08";
				$TRN_ENDDATE = "1999-03-12";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="8-12 ��.�. 47") {
				$TRN_STARTDATE = "2004-03-08";
				$TRN_ENDDATE = "2004-03-12";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="8-12 ��.�. 52") {
				$TRN_STARTDATE = "2009-06-08";
				$TRN_ENDDATE = "2009-06-12";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="8-12 �.�.43") {
				$TRN_STARTDATE = "2000-05-08";
				$TRN_ENDDATE = "2000-05-12";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="8-12 �ѹ��¹ 2551") {
				$TRN_STARTDATE = "2008-09-08";
				$TRN_ENDDATE = "2008-09-12";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="8-12 �.�.42") {
				$TRN_STARTDATE = "1999-02-08";
				$TRN_ENDDATE = "1999-02-12";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="8-12 �.�. 39") {
				$TRN_STARTDATE = "1996-07-08";
				$TRN_ENDDATE = "1996-07-12";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="8-12  �չҤ� 2547") {
				$TRN_STARTDATE = "2004-03-08";
				$TRN_ENDDATE = "2004-03-12";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="8-10 ��.�. 52") {
				$TRN_STARTDATE = "2009-04-08";
				$TRN_ENDDATE = "2009-04-10";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="8-10 ��.�. 40") {
				$TRN_STARTDATE = "1997-04-08";
				$TRN_ENDDATE = "1997-04-10";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="8-10 ��.�. 47") {
				$TRN_STARTDATE = "2004-03-08";
				$TRN_ENDDATE = "2004-03-10";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="8-10 �Զع�¹ 2548") {
				$TRN_STARTDATE = "2005-06-08";
				$TRN_ENDDATE = "2005-06-10";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="8-10 ����Ҥ�  2549") {
				$TRN_STARTDATE = "2006-05-08";
				$TRN_ENDDATE = "2006-05-10";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="8-10 �.�.42") {
				$TRN_STARTDATE = "1999-11-08";
				$TRN_ENDDATE = "1999-11-10";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="8 �.�.43") {
				$TRN_STARTDATE = "2000-08-08";
				$TRN_ENDDATE = "2000-08-08";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="8 �.�.-12 �.�. 47 (੾���ѹ����������ѹ�ҷԵ�� �ѻ������ 1 �ѹ)") {
				$TRN_STARTDATE = "2004-08-08";
				$TRN_ENDDATE = "2004-09-19";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="8 ��.�.-30 �.�.43 (੾���ѹ����������ѹ�ҷԵ��)") {
				$TRN_STARTDATE = "2000-04-08";
				$TRN_ENDDATE = "2000-07-30";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="8 ��.�.-30 �.�.42 (�ѧ���,����� 17.30-20.30 �. )") {
				$TRN_STARTDATE = "1999-06-08";
				$TRN_ENDDATE = "1999-08-30";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="8 �.�.-4 ��.�.40") {
				$TRN_STARTDATE = "1997-01-08";
				$TRN_ENDDATE = "1997-04-04";
				$TRN_DAY = 80;
			} elseif ($TRN_REMARK=="8 �.�.-1 ��.�.45") {
				$TRN_STARTDATE = "2002-01-08";
				$TRN_ENDDATE = "2002-03-01";
				$TRN_DAY = 53;
			} elseif ($TRN_REMARK=="8 �.�.43") {
				$TRN_STARTDATE = "2000-09-08";
				$TRN_ENDDATE = "2000-09-08";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="7-9 �Զع�¹  2547") {
				$TRN_STARTDATE = "2004-06-07";
				$TRN_ENDDATE = "2004-06-09";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="7-9 ��.�.43") {
				$TRN_STARTDATE = "2000-06-07";
				$TRN_ENDDATE = "2000-06-09";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="7-9 ��.�. 47") {
				$TRN_STARTDATE = "2004-06-07";
				$TRN_ENDDATE = "2004-06-09";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="7-9 �.�. 44") {
				$TRN_STARTDATE = "2001-11-07";
				$TRN_ENDDATE = "2001-11-09";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="7-9 �.�.39") {
				$TRN_STARTDATE = "1996-05-07";
				$TRN_ENDDATE = "1996-05-09";
				$TRN_DAY = 3;
 			} elseif ($TRN_REMARK=="7-9 �.�. 39") {
				$TRN_STARTDATE = "1996-10-07";
				$TRN_ENDDATE = "1996-10-09";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="7-9 �.�.42") {
				$TRN_STARTDATE = "1999-09-07";
				$TRN_ENDDATE = "1999-09-09";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="7-8 ����¹ 2552") {
				$TRN_STARTDATE = "2009-04-07";
				$TRN_ENDDATE = "2009-04-08";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="7-8 ��.�. 48") {
				$TRN_STARTDATE = "2005-04-07";
				$TRN_ENDDATE = "2005-04-08";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="7-8 �.�. 39") {
				$TRN_STARTDATE = "1996-11-07";
				$TRN_ENDDATE = "1996-11-08";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="7-8 �.�.2549") {
				$TRN_STARTDATE = "2006-12-07";
				$TRN_ENDDATE = "2006-12-08";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="7-28 �.�.42") {
				$TRN_STARTDATE = "1999-05-07";
				$TRN_ENDDATE = "1999-05-28";
				$TRN_DAY = 22;
			} elseif ($TRN_REMARK=="7-18 ��.�.42") {
				$TRN_STARTDATE = "1999-06-07";
				$TRN_ENDDATE = "1999-06-18";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="7-18 ��.�.2547") {
				$TRN_STARTDATE = "2004-06-07";
				$TRN_ENDDATE = "2004-06-18";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="7-18 ��.�. 47") {
				$TRN_STARTDATE = "2004-06-07";
				$TRN_ENDDATE = "2004-06-18";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="7-17 �չҤ� 2548") {
				$TRN_STARTDATE = "2005-03-07";
				$TRN_ENDDATE = "2005-03-17";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="7-17 ��.�.2547") {
				$TRN_STARTDATE = "2004-06-07";
				$TRN_ENDDATE = "2004-06-17";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="7-17 ��.�. 47") {
				$TRN_STARTDATE = "2004-06-07";
				$TRN_ENDDATE = "2004-06-17";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="7-12 �.�. 42") {
				$TRN_STARTDATE = "1999-02-07";
				$TRN_ENDDATE = "1999-02-12";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="7-11 �չҤ� 2548") {
				$TRN_STARTDATE = "2005-03-07";
				$TRN_ENDDATE = "2005-03-11";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="7-11 �Զع�¹ 2547") {
				$TRN_STARTDATE = "2004-06-07";
				$TRN_ENDDATE = "2004-06-11";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="7-11 �Զع�¹  2547") {
				$TRN_STARTDATE = "2004-06-07";
				$TRN_ENDDATE = "2004-06-11";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="7-11 ��.�. 47") {
				$TRN_STARTDATE = "2004-06-07";
				$TRN_ENDDATE = "2004-06-11";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="7-11 ��.�. 42") {
				$TRN_STARTDATE = "1999-06-07";
				$TRN_ENDDATE = "1999-06-11";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="7-11 �ѹ��¹ 2552") {
				$TRN_STARTDATE = "2006-09-07";
				$TRN_ENDDATE = "2006-09-11";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="7-11 �.�. 40") {
				$TRN_STARTDATE = "1997-07-07";
				$TRN_ENDDATE = "1997-07-11";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="7-10 ��Ȩԡ�¹ 2548") {
				$TRN_STARTDATE = "2005-11-07";
				$TRN_ENDDATE = "2005-11-10";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="7 ��.�.-12 ��.�.41") {
				$TRN_STARTDATE = "1998-04-07";
				$TRN_ENDDATE = "1998-06-12";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="7 ��.�.2548 (17.30-19.00)") {
				$TRN_STARTDATE = "2005-06-07";
				$TRN_ENDDATE = "2005-06-07";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="7 �.�.-1 ��.�.34") {
				$TRN_STARTDATE = "1991-01-07";
				$TRN_ENDDATE = "1991-04-01";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="7 �.�.-14 ��.�. 52") {
				$TRN_STARTDATE = "2009-02-07";
				$TRN_ENDDATE = "2009-06-14";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="6-9 ��.�. 40") {
				$TRN_STARTDATE = "1997-03-06";
				$TRN_ENDDATE = "1997-03-09";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="6-9 ��.�.48") {
				$TRN_STARTDATE = "2005-06-06";
				$TRN_ENDDATE = "2005-06-09";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="6-9 �á�Ҥ� 2547") {
				$TRN_STARTDATE = "2004-07-06";
				$TRN_ENDDATE = "2004-07-09";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="6-9 �.�. 47") {
				$TRN_STARTDATE = "2004-07-06";
				$TRN_ENDDATE = "2004-07-09";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="6-8 ��.�.48") {
				$TRN_STARTDATE = "2005-06-06";
				$TRN_ENDDATE = "2005-06-08";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="6-8 �.�. 39") {
				$TRN_STARTDATE = "1996-11-06";
				$TRN_ENDDATE = "1996-11-08";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="6-8 �.�. 40") {
				$TRN_STARTDATE = "1997-05-06";
				$TRN_ENDDATE = "1997-05-08";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="6-7,10-11 �.�.43") {
				$TRN_STARTDATE = "2000-07-06";
				$TRN_ENDDATE = "2000-07-11";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="6-7 �.�.42") {
				$TRN_STARTDATE = "1999-02-06";
				$TRN_ENDDATE = "1999-02-07";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="6-7 �.�.43") {
				$TRN_STARTDATE = "2000-07-06";
				$TRN_ENDDATE = "2000-07-07";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="6-7 �.�.39") {
				$TRN_STARTDATE = "1996-07-06";
				$TRN_ENDDATE = "1996-07-07";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="6-20 �.�.43") {
				$TRN_STARTDATE = "2000-12-06";
				$TRN_ENDDATE = "2000-12-20";
				$TRN_DAY = 15;
			} elseif ($TRN_REMARK=="6-17 ��.�.2549") {
				$TRN_STARTDATE = "2006-03-06";
				$TRN_ENDDATE = "2006-03-17";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="6-17 �.�.43") {
				$TRN_STARTDATE = "2000-11-06";
				$TRN_ENDDATE = "2000-11-17";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="6-16 ��.�.43") {
				$TRN_STARTDATE = "2006-06-06";
				$TRN_ENDDATE = "2006-06-16";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="6-14 �.�.42") {
				$TRN_STARTDATE = "2005-09-06";
				$TRN_ENDDATE = "2005-09-14";
				$TRN_DAY = 9;
			} elseif ($TRN_REMARK=="6-11 �.�. 43") {
				$TRN_STARTDATE = "2006-08-06";
				$TRN_ENDDATE = "2006-08-11";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="6-10 �ԧ�Ҥ� 2550") {
				$TRN_STARTDATE = "2007-08-06";
				$TRN_ENDDATE = "2007-08-10";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="6-10 �Զع�¹ 2548 �٧ҹ 9-10 �Զع�¹") {
				$TRN_STARTDATE = "2005-06-06";
				$TRN_ENDDATE = "2005-06-10";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="6 �.�.41") {
				$TRN_STARTDATE = "2004-08-06";
				$TRN_ENDDATE = "2004-08-06";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="6 ��.�.-12 ��.�. 41") {
				$TRN_STARTDATE = "2004-04-06";
				$TRN_ENDDATE = "2004-06-12";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="6 �չҤ� 2552") {
				$TRN_STARTDATE = "2009-03-06";
				$TRN_ENDDATE = "2009-03-06";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="6 �.�.-11 ��.�. 40") {
				$TRN_STARTDATE = "2003-01-06";
				$TRN_ENDDATE = "2003-04-11";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="6 �.�. - 14 ��.�. 40") {
				$TRN_STARTDATE = "2003-01-06";
				$TRN_ENDDATE = "2003-03-14";
				$TRN_DAY = 0;
 			} elseif ($TRN_REMARK=="6 �.�.-8 �.�. 40") {
				$TRN_STARTDATE = "2003-05-06";
				$TRN_ENDDATE = "2003-08-08";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="6 �.�.48") {
				$TRN_STARTDATE = "2005-12-06";
				$TRN_ENDDATE = "2005-12-06";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="6 �.�.-23 �.�.48") {
				$TRN_STARTDATE = "2005-07-06";
				$TRN_ENDDATE = "2005-08-23";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="5-9 �.�. 39") {
				$TRN_STARTDATE = "1996-08-05";
				$TRN_ENDDATE = "1996-08-09";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="5-9 ��.�. 44") {
				$TRN_STARTDATE = "2001-08-05";
				$TRN_ENDDATE = "2001-08-09";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="5-9 ��.�.43") {
				$TRN_STARTDATE = "2000-06-05";
				$TRN_ENDDATE = "2000-06-09";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="5-9 �.�. 52") {
				$TRN_STARTDATE = "2009-01-05";
				$TRN_ENDDATE = "2009-01-09";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="5-9 �ѹ��¹ 2548") {
				$TRN_STARTDATE = "2005-09-05";
				$TRN_ENDDATE = "2005-09-09";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="5-8 �ԧ�Ҥ� 2547") {
				$TRN_STARTDATE = "2004-08-05";
				$TRN_ENDDATE = "2004-08-08";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="5-8 �.�. 47") {
				$TRN_STARTDATE = "2004-08-05";
				$TRN_ENDDATE = "2004-08-08";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="5-8 ��.�. 40") {
				$TRN_STARTDATE = "1997-06-05";
				$TRN_ENDDATE = "1997-06-08";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="5-7 ��.�. 45") {
				$TRN_STARTDATE = "2002-06-05";
				$TRN_ENDDATE = "2002-06-07";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="5-7 �ѹ��¹ 2548") {
				$TRN_STARTDATE = "2005-09-05";
				$TRN_ENDDATE = "2005-09-07";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="5-7 �á�Ҥ� 2549") {
				$TRN_STARTDATE = "2006-07-05";
				$TRN_ENDDATE = "2006-07-07";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="5-7 �.�. 2547") {
				$TRN_STARTDATE = "2004-09-05";
				$TRN_ENDDATE = "2004-09-07";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="5-6 �ԧ�Ҥ� 2551") {
				$TRN_STARTDATE = "2008-08-05";
				$TRN_ENDDATE = "2008-08-06";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="5-6 �.�.42") {
				$TRN_STARTDATE = "1999-08-05";
				$TRN_ENDDATE = "1999-08-06";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="5-6 ����Ҿѹ�� 2549") {
				$TRN_STARTDATE = "2006-02-05";
				$TRN_ENDDATE = "2006-02-06";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="5-16 �.�. 41") {
				$TRN_STARTDATE = "1998-01-05";
				$TRN_ENDDATE = "1998-01-16";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="5-16 �.�.2547") {
				$TRN_STARTDATE = "2004-07-05";
				$TRN_ENDDATE = "2004-07-16";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="5-16 �.�. 47") {
				$TRN_STARTDATE = "2004-07-05";
				$TRN_ENDDATE = "2004-07-16";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="5-15 �.�.2547") {
				$TRN_STARTDATE = "2004-07-05";
				$TRN_ENDDATE = "2004-07-15";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="5-15 �.�. 47") {
				$TRN_STARTDATE = "2004-07-05";
				$TRN_ENDDATE = "2004-07-15";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="5-15 �.�. 47") {
				$TRN_STARTDATE = "2004-07-05";
				$TRN_ENDDATE = "2004-07-15";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="5-13 �.�.42") {
				$TRN_STARTDATE = "1999-07-05";
				$TRN_ENDDATE = "1999-07-13";
				$TRN_DAY = 9;
			} elseif ($TRN_REMARK=="5 ��.�.-17 �.�.47 (�ѹ�����)") {
				$TRN_STARTDATE = "2004-06-05";
				$TRN_ENDDATE = "2004-07-17";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="5 ��.�.17 �.�.47") {
				$TRN_STARTDATE = "2004-06-05";
				$TRN_ENDDATE = "2004-07-17";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="5 �.�. - 11 �.�. 41") {
				$TRN_STARTDATE = "1998-10-05";
				$TRN_ENDDATE = "1998-12-11";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="5 �.�.-23 �.�.43") {
				$TRN_STARTDATE = "2000-07-05";
				$TRN_ENDDATE = "2000-08-23";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="5 - 10 �.�. 2546") {
				$TRN_STARTDATE = "2003-08-05";
				$TRN_ENDDATE = "2003-08-10";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="4-8 �.�.2548") {
				$TRN_STARTDATE = "2005-07-04";
				$TRN_ENDDATE = "2005-07-08";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="4-7 �.�. 40") {
				$TRN_STARTDATE = "1997-08-04";
				$TRN_ENDDATE = "1997-08-07";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="4-6 �չҤ� 2551") {
				$TRN_STARTDATE = "2008-03-04";
				$TRN_ENDDATE = "2008-03-06";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="4-6  ����¹ 2549") {
				$TRN_STARTDATE = "2006-04-04";
				$TRN_ENDDATE = "2006-04-06";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="4-5 ��.�. 52") {
				$TRN_STARTDATE = "2009-03-04";
				$TRN_ENDDATE = "2009-03-05";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="4-5 �.�. 47") {
				$TRN_STARTDATE = "2004-11-04";
				$TRN_ENDDATE = "2004-11-05";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="4-5 �.�. 39") {
				$TRN_STARTDATE = "1996-11-04";
				$TRN_ENDDATE = "1996-11-05";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="4-5 �ѹ��¹ 2552") {
				$TRN_STARTDATE = "2009-09-04";
				$TRN_ENDDATE = "2009-09-05";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="4-5 �ѹ��¹ 2551") {
				$TRN_STARTDATE = "2008-09-04";
				$TRN_ENDDATE = "2008-09-05";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="4-22 �.�. 40") {
				$TRN_STARTDATE = "1997-08-04";
				$TRN_ENDDATE = "1997-08-22";
				$TRN_DAY = 19;
			} elseif ($TRN_REMARK=="4-15 ��.�.44") {
				$TRN_STARTDATE = "2001-06-04";
				$TRN_ENDDATE = "2001-06-15";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="4-10 �.�.42") {
				$TRN_STARTDATE = "1999-01-04";
				$TRN_ENDDATE = "1999-01-10";
				$TRN_DAY = 7;
			} elseif ($TRN_REMARK=="4-10 �.�. 42") {
				$TRN_STARTDATE = "1999-01-04";
				$TRN_ENDDATE = "1999-01-10";
				$TRN_DAY = 7;
			} elseif ($TRN_REMARK=="4 �.�.-13 �.�.42 (੾���ѹ�ѹ���,�ظ,�ء��)") {
				$TRN_STARTDATE = "1999-08-04";
				$TRN_ENDDATE = "1999-09-13";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="4 ����¹ 2551") {
				$TRN_STARTDATE = "2008-04-04";
				$TRN_ENDDATE = "2008-04-04";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="4 ��.�. - 10 ��.�. 43") {
				$TRN_STARTDATE = "2000-04-04";
				$TRN_ENDDATE = "2000-06-10";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="3-8 ����¹ 2548") {
				$TRN_STARTDATE = "2005-04-03";
				$TRN_ENDDATE = "2005-04-08";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="3-7 �.�.41") {
				$TRN_STARTDATE = "1998-08-03";
				$TRN_ENDDATE = "1998-08-07";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="3-7 ��.�. 40") {
				$TRN_STARTDATE = "1997-03-03";
				$TRN_ENDDATE = "1997-03-07";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="3-6 �ԧ�Ҥ� 2547") {
				$TRN_STARTDATE = "2004-08-03";
				$TRN_ENDDATE = "2004-08-06";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="3-6 �.�. 47") {
				$TRN_STARTDATE = "2004-08-03";
				$TRN_ENDDATE = "2004-08-06";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="3-6 ��.�. 45") {
				$TRN_STARTDATE = "2002-06-03";
				$TRN_ENDDATE = "2002-06-06";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="3-6 �.�. 47") {
				$TRN_STARTDATE = "2004-11-03";
				$TRN_ENDDATE = "2004-11-06";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="3-5 ��.�.43") {
				$TRN_STARTDATE = "2000-04-03";
				$TRN_ENDDATE = "2000-04-05";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="3-5 ��.�.39") {
				$TRN_STARTDATE = "1998-04-03";
				$TRN_ENDDATE = "1998-04-05";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="3-5 ��.�. 45") {
				$TRN_STARTDATE = "2002-04-03";
				$TRN_ENDDATE = "2002-04-05";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="3-5 ��.�. 44") {
				$TRN_STARTDATE = "2001-04-03";
				$TRN_ENDDATE = "2001-04-05";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="3-5 ��.�.42") {
				$TRN_STARTDATE = "1999-03-03";
				$TRN_ENDDATE = "1999-03-05";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="3-5 �.�.46") {
				$TRN_STARTDATE = "2003-11-03";
				$TRN_ENDDATE = "2003-11-05";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="3-5 �ѹ��¹ 2552") {
				$TRN_STARTDATE = "2009-09-03";
				$TRN_ENDDATE = "2009-09-05";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="3-4 �.�.42") {
				$TRN_STARTDATE = "1999-08-03";
				$TRN_ENDDATE = "1999-08-04";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="3-4 ��.�.42") {
				$TRN_STARTDATE = "1999-04-03";
				$TRN_ENDDATE = "1999-04-04";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="3-4 �.�. 40") {
				$TRN_STARTDATE = "1997-05-03";
				$TRN_ENDDATE = "1997-05-04";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="3-19 ��.�. 39") {
				$TRN_STARTDATE = "1996-06-03";
				$TRN_ENDDATE = "1996-06-19";
				$TRN_DAY = 17;
			} elseif ($TRN_REMARK=="3-19 �.�.42") {
				$TRN_STARTDATE = "1999-02-03";
				$TRN_ENDDATE = "1999-02-19";
				$TRN_DAY = 17;
			} elseif ($TRN_REMARK=="3-17 �.�.42") {
				$TRN_STARTDATE = "1999-05-03";
				$TRN_ENDDATE = "1999-05-17";
				$TRN_DAY = 15;
			} elseif ($TRN_REMARK=="3-14 ��.�. 45") {
				$TRN_STARTDATE = "2002-06-03";
				$TRN_ENDDATE = "2002-06-14";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="3-12 ��.�. 39") {
				$TRN_STARTDATE = "1998-06-03";
				$TRN_ENDDATE = "1998-06-12";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK=="31 �.�.-3 �.�.2547") {
				$TRN_STARTDATE = "2004-08-31";
				$TRN_ENDDATE = "2004-09-03";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="31 �.�.-3 �.�. 47") {
				$TRN_STARTDATE = "2004-08-31";
				$TRN_ENDDATE = "2004-09-03";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="31 �չҤ�-3 ����¹ 2551") {
				$TRN_STARTDATE = "2008-03-31";
				$TRN_ENDDATE = "2008-04-03";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="31 �չҤ� 2552") {
				$TRN_STARTDATE = "2009-03-31";
				$TRN_ENDDATE = "2009-03-31";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="31 �չҤ� 2548") {
				$TRN_STARTDATE = "2005-03-31";
				$TRN_ENDDATE = "2005-03-31";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="31 �.�.-5 �.�. 42") {
				$TRN_STARTDATE = "1999-01-31";
				$TRN_ENDDATE = "1999-02-05";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="31 �.�.-17 �.�. 41") {
				$TRN_STARTDATE = "1998-01-31";
				$TRN_ENDDATE = "1998-05-17";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="30-31 �ԧ�Ҥ�  1-2 �ѹ��¹ 2547") {
				$TRN_STARTDATE = "2004-08-30";
				$TRN_ENDDATE = "2004-09-02";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="30-31 �.�.,1-2 �.�.2547") {
				$TRN_STARTDATE = "2004-08-30";
				$TRN_ENDDATE = "2004-09-02";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="30-31 �.�.,1-2 �.�. 47") {
				$TRN_STARTDATE = "2004-08-30";
				$TRN_ENDDATE = "2004-09-02";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="30-31 ��.�.50") {
				$TRN_STARTDATE = "2007-03-30";
				$TRN_ENDDATE = "2007-03-31";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="30-31 �.�.2548") {
				$TRN_STARTDATE = "2005-05-30";
				$TRN_ENDDATE = "2005-05-31";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="30-31 �.�., 1-3 ��.�. 48") {
				$TRN_STARTDATE = "2005-05-30";
				$TRN_ENDDATE = "2005-06-03";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="30-31 �.�. 40") {
				$TRN_STARTDATE = "1997-10-30";
				$TRN_ENDDATE = "1997-10-31";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="30 �.�.-3 �.�. 47") {
				$TRN_STARTDATE = "2004-08-30";
				$TRN_ENDDATE = "2004-09-03";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="30 �.�.-2 �.�.2547") {
				$TRN_STARTDATE = "2004-08-30";
				$TRN_ENDDATE = "2004-09-02";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="30 �.�.-2 �.�. 47") {
				$TRN_STARTDATE = "2004-08-30";
				$TRN_ENDDATE = "2004-09-02";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="30 ��.�.-4 �.�. 44") {
				$TRN_STARTDATE = "2001-04-30";
				$TRN_ENDDATE = "2001-05-04";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="30 ��.�.-15 �.�.48") {
				$TRN_STARTDATE = "2005-04-30";
				$TRN_ENDDATE = "2005-05-15";
				$TRN_DAY = 16;
			} elseif ($TRN_REMARK=="30 ��.�. -3 ��.�.52") {
				$TRN_STARTDATE = "2009-03-30";
				$TRN_ENDDATE = "2009-04-03";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="30 ��.�.42") {
				$TRN_STARTDATE = "1999-06-30";
				$TRN_ENDDATE = "1999-06-30";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="30 ��.�.-3 �.�. 40") {
				$TRN_STARTDATE = "1997-06-30";
				$TRN_ENDDATE = "1997-07-03";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="30 ����Ҥ�-2 �Զع�¹ 2548") {
				$TRN_STARTDATE = "2005-05-30";
				$TRN_ENDDATE = "2005-06-02";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="30 �.�. 43") {
				$TRN_STARTDATE = "2000-11-30";
				$TRN_ENDDATE = "2000-11-30";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="30 �.�.-29 �.�.2549") {
				$TRN_STARTDATE = "2006-05-30";
				$TRN_ENDDATE = "2006-08-29";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="30 �.�.-1 ��.�.2548") {
				$TRN_STARTDATE = "2005-05-30";
				$TRN_ENDDATE = "2005-06-01";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="30 �á�Ҥ�-1 �ԧ�Ҥ� 2551") {
				$TRN_STARTDATE = "2008-07-31";
				$TRN_ENDDATE = "2008-08-01";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="30 �á�Ҥ� 2552") {
				$TRN_STARTDATE = "2009-07-30";
				$TRN_ENDDATE = "2009-07-30";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="30 �.�.-1 �.�.43") {
				$TRN_STARTDATE = "2000-09-30";
				$TRN_ENDDATE = "2000-10-01";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="30 �.�.- 4 �.�. 44") {
				$TRN_STARTDATE = "2001-09-30";
				$TRN_ENDDATE = "2001-10-04";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="3 �.�.42") {
				$TRN_STARTDATE = "1999-08-03";
				$TRN_ENDDATE = "1999-08-03";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="3 �.�.-16 �.�.48") {
				$TRN_STARTDATE = "2005-08-03";
				$TRN_ENDDATE = "2005-09-16";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="3 ��.�.47") {
				$TRN_STARTDATE = "2004-06-03";
				$TRN_ENDDATE = "2004-06-03";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="3 ��.�.2547") {
				$TRN_STARTDATE = "2004-06-03";
				$TRN_ENDDATE = "2004-06-03";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="3 ��.�.-12 �.�. 39") {
				$TRN_STARTDATE = "1996-06-03";
				$TRN_ENDDATE = "1996-09-12";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="3 �.�.-23 �.�.42") {
				$TRN_STARTDATE = "1999-05-03";
				$TRN_ENDDATE = "1999-07-23";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="3 �ѹ��¹ 2551") {
				$TRN_STARTDATE = "2008-09-03";
				$TRN_ENDDATE = "2008-09-03";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="3 �.�.-6 �.�. 45") {
				$TRN_STARTDATE = "2002-07-03";
				$TRN_ENDDATE = "2002-09-06";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="29-31 �չҤ� 2548") {
				$TRN_STARTDATE = "2005-03-29";
				$TRN_ENDDATE = "2005-03-31";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="29-31 �չҤ� 2547") {
				$TRN_STARTDATE = "2004-03-29";
				$TRN_ENDDATE = "2004-03-31";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="29-31 ��.�. 47") {
				$TRN_STARTDATE = "2004-03-29";
				$TRN_ENDDATE = "2004-03-31";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="29-31 �.�. ��� 1-2 ��.�.43") {
				$TRN_STARTDATE = "2000-05-29";
				$TRN_ENDDATE = "2000-06-02";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="29-30 ��.�.42") {
				$TRN_STARTDATE = "1999-03-29";
				$TRN_ENDDATE = "1999-03-30";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="29-30 �Զع�¹  1-2 �á�Ҥ� 2547") {
				$TRN_STARTDATE = "2004-06-29";
				$TRN_ENDDATE = "2004-07-02";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="29-30 ��.�.,1-2 �.�. 47") {
				$TRN_STARTDATE = "2004-06-29";
				$TRN_ENDDATE = "2004-07-02";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="29-30 ��.�. 52") {
				$TRN_STARTDATE = "2009-06-29";
				$TRN_ENDDATE = "2009-06-30";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="29-30 �á�Ҥ� 2551") {
				$TRN_STARTDATE = "2008-07-29";
				$TRN_ENDDATE = "2008-07-30";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="29-30 �.�. 42") {
				$TRN_STARTDATE = "1999-09-29";
				$TRN_ENDDATE = "1999-09-30";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="29-30 �.�.42") {
				$TRN_STARTDATE = "1999-07-29";
				$TRN_ENDDATE = "1999-07-30";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="29-30 �.�. 42") {
				$TRN_STARTDATE = "1999-07-29";
				$TRN_ENDDATE = "1999-07-30";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="29 �ԧ�Ҥ� - 9 �ѹ��¹ 2548") {
				$TRN_STARTDATE = "2005-08-29";
				$TRN_ENDDATE = "2005-09-09";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="29 �.�.-9 �.�.48") {
				$TRN_STARTDATE = "2005-08-29";
				$TRN_ENDDATE = "2005-09-09";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="29 �.�.-1 �.�. 43") {
				$TRN_STARTDATE = "2000-08-29";
				$TRN_ENDDATE = "2000-09-01";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="29 ����¹ - 1 ����Ҥ� 2547") {
				$TRN_STARTDATE = "2004-04-29";
				$TRN_ENDDATE = "2004-05-01";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="29 ��.�.48 (09.00-12.00 �.)") {
				$TRN_STARTDATE = "2005-04-29";
				$TRN_ENDDATE = "2005-04-29";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="29 ��.�.-1 �.�. 47") {
				$TRN_STARTDATE = "2004-04-29";
				$TRN_ENDDATE = "2004-05-01";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="29 ��.�. - 3 �.�. 39") {
				$TRN_STARTDATE = "1996-04-29";
				$TRN_ENDDATE = "1996-05-03";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="29 �.�.41") {
				$TRN_STARTDATE = "1998-01-29";
				$TRN_ENDDATE = "1998-01-29";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="29 �.�. 47") {
				$TRN_STARTDATE = "2004-01-29";
				$TRN_ENDDATE = "2004-01-29";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="29 �.�.2548") {
				$TRN_STARTDATE = "2005-11-29";
				$TRN_ENDDATE = "2005-11-29";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="29 �.�.-1 ��.�.44") {
				$TRN_STARTDATE = "2001-05-29";
				$TRN_ENDDATE = "2001-06-29";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="29 �.�.-10 ��.�.43") {
				$TRN_STARTDATE = "2000-02-29";
				$TRN_ENDDATE = "2000-03-10";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="29 �.�.-9 �.�. 45") {
				$TRN_STARTDATE = "2002-07-29";
				$TRN_ENDDATE = "2002-08-09";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28-31 �.�.41") {
				$TRN_STARTDATE = "1998-10-28";
				$TRN_ENDDATE = "1998-10-31";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="28-31 �.�. 40") {
				$TRN_STARTDATE = "1997-07-28";
				$TRN_ENDDATE = "1997-07-31";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="28-30 ����¹ 2548") {
				$TRN_STARTDATE = "2005-04-30";
				$TRN_ENDDATE = "2005-04-28";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="28-30 ��.�. 40") {
				$TRN_STARTDATE = "1997-04-30";
				$TRN_ENDDATE = "1997-04-28";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="28-30 �Զع�¹ 2547") {
				$TRN_STARTDATE = "2004-06-28";
				$TRN_ENDDATE = "2004-06-30";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="28-30 ��.�. 47") {
				$TRN_STARTDATE = "2004-06-28";
				$TRN_ENDDATE = "2004-06-30";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="28-30 ���Ҥ� 2551") {
				$TRN_STARTDATE = "2008-01-30";
				$TRN_ENDDATE = "2008-01-28";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="28-30 �.�. 44") {
				$TRN_STARTDATE = "2001-11-28";
				$TRN_ENDDATE = "2001-11-30";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="28-30 �ѹ��¹ 2550") {
				$TRN_STARTDATE = "2007-09-28";
				$TRN_ENDDATE = "2007-09-30";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="28-30 �ѹ��¹ 2548") {
				$TRN_STARTDATE = "2005-09-28";
				$TRN_ENDDATE = "2005-09-30";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="28-30 �.�. 47") {
				$TRN_STARTDATE = "2004-07-28";
				$TRN_ENDDATE = "2004-07-30";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="28-29 ��.�.48") {
				$TRN_STARTDATE = "2005-04-28";
				$TRN_ENDDATE = "2005-04-29";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="28-29 �.�.42") {
				$TRN_STARTDATE = "1999-01-28";
				$TRN_ENDDATE = "1999-01-29";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="28-29 �.�.49") {
				$TRN_STARTDATE = "2006-11-28";
				$TRN_ENDDATE = "2006-11-29";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="28-29 �.�. 39") {
				$TRN_STARTDATE = "1996-11-28";
				$TRN_ENDDATE = "1996-11-29";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="28-29 �.�.52") {
				$TRN_STARTDATE = "2009-05-28";
				$TRN_ENDDATE = "2009-05-29";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="28-29 �.�.40") {
				$TRN_STARTDATE = "1997-05-28";
				$TRN_ENDDATE = "1997-05-29";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="28-29 �.�. 39") {
				$TRN_STARTDATE = "1996-12-28";
				$TRN_ENDDATE = "1996-12-29";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="28-29 �.�. 39") {
				$TRN_STARTDATE = "1996-10-28";
				$TRN_ENDDATE = "1996-10-29";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="28-29 �á�Ҥ� 2548") {
				$TRN_STARTDATE = "2005-07-28";
				$TRN_ENDDATE = "2005-07-29";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="28 ��.�.-9 �.�. 40") {
				$TRN_STARTDATE = "1997-04-28";
				$TRN_ENDDATE = "1997-05-09";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28 ��.�. - 18 �.�. 46") {
				$TRN_STARTDATE = "2003-04-28";
				$TRN_ENDDATE = "2003-07-18";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28 �չҤ�-30 �Զع�¹ 2549") {
				$TRN_STARTDATE = "2006-03-28";
				$TRN_ENDDATE = "2006-06-30";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28 ��.�.-2 �.�. 42") {
				$TRN_STARTDATE = "1999-06-28";
				$TRN_ENDDATE = "1999-07-02";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28 �.�.42") {
				$TRN_STARTDATE = "1999-01-28";
				$TRN_ENDDATE = "1999-01-28";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="28 ��Ȩԡ�¹-2 �ѹ�Ҥ� 2549") {
				$TRN_STARTDATE = "2006-11-28";
				$TRN_ENDDATE = "2006-12-02";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28 �.�. - 29 ��.�. 44") {
				$TRN_STARTDATE = "2001-05-28";
				$TRN_ENDDATE = "2001-06-29";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28 �.�.39 - 7 �.�. 40") {
				$TRN_STARTDATE = "1997-10-18";
				$TRN_ENDDATE = "1998-02-07";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28 �.�.-1 �.�. 39") {
				$TRN_STARTDATE = "1996-10-28";
				$TRN_ENDDATE = "1996-11-01";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28 ����Ҿѹ�� - 4 �չҤ� 2548") {
				$TRN_STARTDATE = "2005-02-28";
				$TRN_ENDDATE = "2005-03-04";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28 �.�.-4 ��.�.48") {
				$TRN_STARTDATE = "2005-02-28";
				$TRN_ENDDATE = "2005-03-04";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28 �.�.-3 ��.�.43") {
				$TRN_STARTDATE = "2000-02-28";
				$TRN_ENDDATE = "2000-03-03";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28 �.�.-18 �.�. 52") {
				$TRN_STARTDATE = "2009-07-28";
				$TRN_ENDDATE = "2009-08-18";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28 - 30 �.�.48") {
				$TRN_STARTDATE = "2005-11-28";
				$TRN_ENDDATE = "2005-11-30";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="27-31 �ԧ�Ҥ�, 3-7 �ѹ��¹ 2550") {
				$TRN_STARTDATE = "2007-08-27";
				$TRN_ENDDATE = "2007-09-07";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK=="27-31 �ԧ�Ҥ� 2550") {
				$TRN_STARTDATE = "2007-08-27";
				$TRN_ENDDATE = "2007-08-31";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="27-31 ��.�.43") {
				$TRN_STARTDATE = "2000-03-27";
				$TRN_ENDDATE = "2000-03-31";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="27-31 ��.�. 43") {
				$TRN_STARTDATE = "2000-03-27";
				$TRN_ENDDATE = "2000-03-31";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="27-30 ��.�.2547") {
				$TRN_STARTDATE = "2004-04-27";
				$TRN_ENDDATE = "2004-04-30";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="27-30 ��.�. 52") {
				$TRN_STARTDATE = "2009-04-27";
				$TRN_ENDDATE = "2009-04-30";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="27-30 ��.�. 48") {
				$TRN_STARTDATE = "2005-03-27";
				$TRN_ENDDATE = "2005-03-30";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="27-30 ��.�. 2543") {
				$TRN_STARTDATE = "2000-06-27";
				$TRN_ENDDATE = "2000-06-30";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="27-30 �.�,1 �.�.49") {
				$TRN_STARTDATE = "2006-11-27";
				$TRN_ENDDATE = "2006-12-01";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="27-30 �á�Ҥ� 2547") {
				$TRN_STARTDATE = "2004-07-27";
				$TRN_ENDDATE = "2004-07-30";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="27-30 �.�.42") {
				$TRN_STARTDATE = "1999-09-27";
				$TRN_ENDDATE = "1999-09-30";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="27-30 �.�. 47") {
				$TRN_STARTDATE = "2004-07-27";
				$TRN_ENDDATE = "2004-07-30";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="27-29 �.�.42") {
				$TRN_STARTDATE = "1999-08-27";
				$TRN_ENDDATE = "1999-08-29";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="27-29 ����¹ 2548") {
				$TRN_STARTDATE = "2005-04-27";
				$TRN_ENDDATE = "2005-04-29";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="27-29 ��.�.50") {
				$TRN_STARTDATE = "2007-04-27";
				$TRN_ENDDATE = "2007-04-29";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="27-29 ��.�.43") {
				$TRN_STARTDATE = "2000-03-27";
				$TRN_ENDDATE = "2000-03-29";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="27-29 �Զع�¹ 2550") {
				$TRN_STARTDATE = "2007-06-27";
				$TRN_ENDDATE = "2007-06-29";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="27-29 �Զع�¹ 2548") {
				$TRN_STARTDATE = "2005-06-27";
				$TRN_ENDDATE = "2005-06-29";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="27-29 ��.�.2548") {
				$TRN_STARTDATE = "2005-06-27";
				$TRN_ENDDATE = "2005-06-29";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="27-29 �.�. 39") {
				$TRN_STARTDATE = "1996-11-27";
				$TRN_ENDDATE = "1996-11-29";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="27-29 �.�.52") {
				$TRN_STARTDATE = "2009-05-27";
				$TRN_ENDDATE = "2009-05-29";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="27-28 �ԧ�Ҥ� 2551") {
				$TRN_STARTDATE = "2008-08-27";
				$TRN_ENDDATE = "2008-08-28";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="27-28 �.�. 40") {
				$TRN_STARTDATE = "1997-11-27";
				$TRN_ENDDATE = "1997-11-28";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="27-28 �.�. 39") {
				$TRN_STARTDATE = "1996-11-27";
				$TRN_ENDDATE = "1996-11-28";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="27-28 ���Ҥ� 2550") {
				$TRN_STARTDATE = "2007-10-27";
				$TRN_ENDDATE = "2007-10-28";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="27-28 �.�. 42") {
				$TRN_STARTDATE = "1999-09-27";
				$TRN_ENDDATE = "1999-09-28";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="27 �.�.-7 �.�. 44") {
				$TRN_STARTDATE = "2001-08-27";
				$TRN_ENDDATE = "2001-09-07";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="27 �.�.41") {
				$TRN_STARTDATE = "1998-08-27";
				$TRN_ENDDATE = "1998-08-27";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="27 ��.�.-1 �.�. 52") {
				$TRN_STARTDATE = "2009-04-27";
				$TRN_ENDDATE = "2009-05-01";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="27 ��.�.-1 �.�.48") {
				$TRN_STARTDATE = "2005-06-27";
				$TRN_ENDDATE = "2005-07-01";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="27 �.�.,3,10,17,24 �.�. ��� 1 �.�. 39") {
				$TRN_STARTDATE = "1996-10-27";
				$TRN_ENDDATE = "1996-12-01";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="27 �.�.-3 ��.�.43") {
				$TRN_STARTDATE = "2000-02-27";
				$TRN_ENDDATE = "2000-03-03";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="27 �.�.-10 ��.�.49") {
				$TRN_STARTDATE = "2006-02-27";
				$TRN_ENDDATE = "2006-03-10";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="26-31 �.�. 40") {
				$TRN_STARTDATE = "1997-05-26";
				$TRN_ENDDATE = "1997-05-31";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="26-30 �.�. 39") {
				$TRN_STARTDATE = "1996-08-26";
				$TRN_ENDDATE = "1996-08-30";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="26-30 ����¹ 2547") {
				$TRN_STARTDATE = "2004-04-26";
				$TRN_ENDDATE = "2004-04-30";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="26-30 ����¹  2547") {
				$TRN_STARTDATE = "2004-04-26";
				$TRN_ENDDATE = "2004-04-30";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="26-30 ��.�. 47") {
				$TRN_STARTDATE = "2004-04-26";
				$TRN_ENDDATE = "2004-04-30";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="26-30 ��.�. 44") {
				$TRN_STARTDATE = "2001-03-26";
				$TRN_ENDDATE = "2001-03-30";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="26-30 �.�.47") {
				$TRN_STARTDATE = "2004-01-26";
				$TRN_ENDDATE = "2004-01-30";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="26-30 �.�. 52") {
				$TRN_STARTDATE = "2009-01-26";
				$TRN_ENDDATE = "2009-01-30";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="26-30 �.�. 40") {
				$TRN_STARTDATE = "1997-05-26";
				$TRN_ENDDATE = "1997-05-30";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="26-30 �.�. 2-6 ��.�.51") {
				$TRN_STARTDATE = "2008-05-26";
				$TRN_ENDDATE = "2008-06-06";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="26-29 �ԧ�Ҥ� 2551") {
				$TRN_STARTDATE = "2008-08-26";
				$TRN_ENDDATE = "2008-08-29";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="26-29 �.�. 40") {
				$TRN_STARTDATE = "1997-08-26";
				$TRN_ENDDATE = "1997-08-29";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="26-29 ��.�.2549") {
				$TRN_STARTDATE = "2006-06-26";
				$TRN_ENDDATE = "2006-06-29";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="26-29 �á�Ҥ� 2547") {
				$TRN_STARTDATE = "2006-07-26";
				$TRN_ENDDATE = "2006-07-29";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="26-29 �.�.2548") {
				$TRN_STARTDATE = "2007-07-26";
				$TRN_ENDDATE = "2007-07-29";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="26-29 �.�. 47") {
				$TRN_STARTDATE = "2006-07-26";
				$TRN_ENDDATE = "2006-07-29";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="26-28 ����¹ 2547") {
				$TRN_STARTDATE = "2006-04-26";
				$TRN_ENDDATE = "2006-04-28";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="26-28 ��.�. 47") {
				$TRN_STARTDATE = "2004-04-26";
				$TRN_ENDDATE = "2004-04-28";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="26-28 �չҤ� 2552") {
				$TRN_STARTDATE = "2009-03-26";
				$TRN_ENDDATE = "2009-03-28";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="26-28 ��.�.39") {
				$TRN_STARTDATE = "1996-03-26";
				$TRN_ENDDATE = "1996-03-28";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="26-28 �.�.  2552 ") {
				$TRN_STARTDATE = "2009-01-26";
				$TRN_ENDDATE = "2009-01-28";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="26-28 �.�. 40") {
				$TRN_STARTDATE = "1997-05-26";
				$TRN_ENDDATE = "1997-05-28";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="26-28 �.�. 39") {
				$TRN_STARTDATE = "1996-05-26";
				$TRN_ENDDATE = "1996-05-28";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="26-28 �.�. 51") {
				$TRN_STARTDATE = "2008-09-26";
				$TRN_ENDDATE = "2008-09-28";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="26-28  �á�Ҥ� 2549") {
				$TRN_STARTDATE = "2006-07-26";
				$TRN_ENDDATE = "2006-07-28";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="26-27 �չҤ� 2552") {
				$TRN_STARTDATE = "2009-03-26";
				$TRN_ENDDATE = "2009-03-27";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="26-27 �Զع�¹ 2551") {
				$TRN_STARTDATE = "2008-06-26";
				$TRN_ENDDATE = "2008-06-27";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="26-27 ��.�. 40") {
				$TRN_STARTDATE = "1997-06-26";
				$TRN_ENDDATE = "1997-06-27";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="26-27 �.�.2548") {
				$TRN_STARTDATE = "2005-12-26";
				$TRN_ENDDATE = "2005-12-27";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="26-27 �.�. 39") {
				$TRN_STARTDATE = "1996-12-26";
				$TRN_ENDDATE = "1996-12-27";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="26-27 �.�. 39") {
				$TRN_STARTDATE = "1996-10-26";
				$TRN_ENDDATE = "1996-10-27";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="2-6, 9-10 �չҤ� 2552") {
				$TRN_STARTDATE = "2009-03-02";
				$TRN_ENDDATE = "2009-03-10";
				$TRN_DAY = 7;
			} elseif ($TRN_REMARK=="26 �.�.41") {
				$TRN_STARTDATE = "1998-08-26";
				$TRN_ENDDATE = "1998-08-26";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="2-6 ��.�. 52") {
				$TRN_STARTDATE = "2009-03-02";
				$TRN_ENDDATE = "2009-03-06";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="2-6 ��.�. 42") {
				$TRN_STARTDATE = "1999-03-02";
				$TRN_ENDDATE = "1999-03-06";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="26 ��.�. 42") {
				$TRN_STARTDATE = "1999-03-26";
				$TRN_ENDDATE = "1999-03-26";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="26 �.�.-29 �.�.43") {
				$TRN_STARTDATE = "2000-01-26";
				$TRN_ENDDATE = "2000-02-29";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="26 �.�.-16 �.�.42") {
				$TRN_STARTDATE = "1999-05-26";
				$TRN_ENDDATE = "1999-07-16";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="26 �ѹ�Ҥ� 2550") {
				$TRN_STARTDATE = "2007-12-26";
				$TRN_ENDDATE = "2007-12-26";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="26 �ѹ��¹ 2551") {
				$TRN_STARTDATE = "2008-09-26";
				$TRN_ENDDATE = "2008-09-26";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="2-6 �á�Ҥ� 2550") {
				$TRN_STARTDATE = "2007-07-02";
				$TRN_ENDDATE = "2007-07-06";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="2-6 �.�.45") {
				$TRN_STARTDATE = "2002-09-02";
				$TRN_ENDDATE = "2002-09-06";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="26 �.�. 47") {
				$TRN_STARTDATE = "2004-09-26";
				$TRN_ENDDATE = "2004-09-26";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="2-6 �.�. 52") {
				$TRN_STARTDATE = "2009-02-02";
				$TRN_ENDDATE = "2009-02-06";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="00-00-2542") {
				$TRN_STARTDATE = "1999-00-00";
				$TRN_ENDDATE = "1999-00-00";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="00-00-2539") {
				$TRN_STARTDATE = "1996-00-00";
				$TRN_ENDDATE = "1996-00-00";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="25-29 �ԧ�Ҥ� 2551") {
				$TRN_STARTDATE = "2008-08-25";
				$TRN_ENDDATE = "2008-08-29";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="25-29 �.�. 40") {
				$TRN_STARTDATE = "1997-08-25";
				$TRN_ENDDATE = "1997-08-29";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="25-29 ����¹ 2548") {
				$TRN_STARTDATE = "2005-04-25";
				$TRN_ENDDATE = "2005-04-29";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="25-29 ��.�. 40") {
				$TRN_STARTDATE = "1997-04-25";
				$TRN_ENDDATE = "1997-04-29";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="25-29 ��.�. 40") {
				$TRN_STARTDATE = "1997-03-25";
				$TRN_ENDDATE = "1997-03-29";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="25-29 �.�.41") {
				$TRN_STARTDATE = "1998-01-25";
				$TRN_ENDDATE = "1998-01-29";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="25-29 �.�.46") {
				$TRN_STARTDATE = "2003-11-25";
				$TRN_ENDDATE = "2003-11-29";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="25-29 �.�.45") {
				$TRN_STARTDATE = "2002-11-25";
				$TRN_ENDDATE = "2002-11-29";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="25-29 �.�. 52") {
				$TRN_STARTDATE = "2009-05-25";
				$TRN_ENDDATE = "2009-05-29";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="25-29 �á�Ҥ� 2548") {
				$TRN_STARTDATE = "2005-07-25";
				$TRN_ENDDATE = "2005-07-29";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="25-29 �.�.43") {
				$TRN_STARTDATE = "2000-09-25";
				$TRN_ENDDATE = "2000-09-29";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="25-28 �.�. 41") {
				$TRN_STARTDATE = "1998-08-25";
				$TRN_ENDDATE = "1998-08-28";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="25-28 ����¹ 2548") {
				$TRN_STARTDATE = "2005-04-25";
				$TRN_ENDDATE = "2005-04-28";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="25-28 ��.�. 45") {
				$TRN_STARTDATE = "2002-06-25";
				$TRN_ENDDATE = "2002-06-28";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="25-27 ����¹ 2548") {
				$TRN_STARTDATE = "2005-04-25";
				$TRN_ENDDATE = "2005-04-27";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="25-27 �չҤ� 2547") {
				$TRN_STARTDATE = "2004-03-25";
				$TRN_ENDDATE = "2004-03-27";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="25-27 ��.�. 47") {
				$TRN_STARTDATE = "2004-03-25";
				$TRN_ENDDATE = "2004-03-27";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="25-27 ����Ҥ� 2548") {
				$TRN_STARTDATE = "2005-05-25";
				$TRN_ENDDATE = "2005-05-27";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="25-27 �.�.42") {
				$TRN_STARTDATE = "1999-05-25";
				$TRN_ENDDATE = "1999-05-27";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="25-27 �.�. 52") {
				$TRN_STARTDATE = "2009-05-25";
				$TRN_ENDDATE = "2009-05-27";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="25-27 �.�. 48") {
				$TRN_STARTDATE = "2005-05-25";
				$TRN_ENDDATE = "2005-05-27";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="25-27 �.�. 47") {
				$TRN_STARTDATE = "2004-05-25";
				$TRN_ENDDATE = "2004-05-27";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="25-27 �.�.2549") {
				$TRN_STARTDATE = "2006-12-25";
				$TRN_ENDDATE = "2006-12-27";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="25-27 �.�.47") {
				$TRN_STARTDATE = "2004-02-25";
				$TRN_ENDDATE = "2004-02-27";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="25-26 ��.�. 52") {
				$TRN_STARTDATE = "2009-06-25";
				$TRN_ENDDATE = "2009-06-26";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="25-26 �.�. 39") {
				$TRN_STARTDATE = "1996-11-25";
				$TRN_ENDDATE = "1996-11-26";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="25 �ԧ�Ҥ� 2550") {
				$TRN_STARTDATE = "2007-08-25";
				$TRN_ENDDATE = "2007-08-25";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="25 �.�.-11 �.�. 51") {
				$TRN_STARTDATE = "2008-08-25";
				$TRN_ENDDATE = "2008-09-11";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="25 ��.�.-1 ��.�.42") {
				$TRN_STARTDATE = "1999-03-25";
				$TRN_ENDDATE = "1999-04-01";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="25 ��.�.-26 �.�.41") {
				$TRN_STARTDATE = "1998-06-25";
				$TRN_ENDDATE = "1998-08-26";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="2-5 ��.�. 52") {
				$TRN_STARTDATE = "2009-06-02";
				$TRN_ENDDATE = "2009-06-05";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="2-5 ��.�. 40") {
				$TRN_STARTDATE = "1997-06-02";
				$TRN_ENDDATE = "1997-06-05";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="25 �.�.-18 ��.�. 40") {
				$TRN_STARTDATE = "1997-01-25";
				$TRN_ENDDATE = "1997-03-18";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="25 �.�.-1 �.�.43") {
				$TRN_STARTDATE = "2000-11-25";
				$TRN_ENDDATE = "2000-12-01";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="25 �.�.-8 ��.�. 40") {
				$TRN_STARTDATE = "1997-05-25";
				$TRN_ENDDATE = "1997-06-08";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="25 �.�.38-19�.�.39") {
				$TRN_STARTDATE = "1995-10-25";
				$TRN_ENDDATE = "1996-01-19";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="25 �á�Ҥ� 2552") {
				$TRN_STARTDATE = "2009-07-25";
				$TRN_ENDDATE = "2009-07-25";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="2-5 �.�. 40") {
				$TRN_STARTDATE = "1997-09-02";
				$TRN_ENDDATE = "1997-09-05";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="25 �.�.-21 �.�. 44") {
				$TRN_STARTDATE = "2001-07-25";
				$TRN_ENDDATE = "2001-09-21";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="24-28 ��.�.43") {
				$TRN_STARTDATE = "2000-04-24";
				$TRN_ENDDATE = "2000-04-28";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="24-28 ��.�. 43") {
				$TRN_STARTDATE = "2000-04-24";
				$TRN_ENDDATE = "2000-04-28";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="24-28 �.�.48") {
				$TRN_STARTDATE = "2005-01-24";
				$TRN_ENDDATE = "2005-01-28";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="24-28 �.�. 42") {
				$TRN_STARTDATE = "1999-05-24";
				$TRN_ENDDATE = "1999-05-28";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="24-28 �.�. 44") {
				$TRN_STARTDATE = "2001-09-24";
				$TRN_ENDDATE = "2001-09-28";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="24-27 �ԧ�Ҥ� 2547") {
				$TRN_STARTDATE = "2004-08-24";
				$TRN_ENDDATE = "2004-08-27";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="24-27 �.�.2547") {
				$TRN_STARTDATE = "2004-08-24";
				$TRN_ENDDATE = "2004-08-27";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="24-27 �.�. 47") {
				$TRN_STARTDATE = "2004-08-24";
				$TRN_ENDDATE = "2004-08-27";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="24-27 ��.�. 44") {
				$TRN_STARTDATE = "2001-04-24";
				$TRN_ENDDATE = "2001-04-27";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="24-27 �չҤ� 2551") {
				$TRN_STARTDATE = "2008-03-24";
				$TRN_ENDDATE = "2008-03-27";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="24-27 �.�. 48") {
				$TRN_STARTDATE = "2005-01-24";
				$TRN_ENDDATE = "2005-01-27";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="24-27 ����Ҥ� 2548") {
				$TRN_STARTDATE = "2005-05-24";
				$TRN_ENDDATE = "2005-05-27";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="24-27 �.�. 47") {
				$TRN_STARTDATE = "2004-11-24";
				$TRN_ENDDATE = "2004-11-27";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="24-26 ����¹ 2549") {
				$TRN_STARTDATE = "2006-04-24";
				$TRN_ENDDATE = "2006-04-26";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="24-26 �չҤ� 2552") {
				$TRN_STARTDATE = "2009-03-24";
				$TRN_ENDDATE = "2009-03-26";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="24-26 ��.�. 52") {
				$TRN_STARTDATE = "2009-06-24";
				$TRN_ENDDATE = "2009-06-26";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="24-26 �.�.48") {
				$TRN_STARTDATE = "2005-11-24";
				$TRN_ENDDATE = "2005-11-26";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="24-26 �.�. 51") {
				$TRN_STARTDATE = "2008-11-24";
				$TRN_ENDDATE = "2008-11-26";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="24-25 �.�. 43") {
				$TRN_STARTDATE = "2000-08-24";
				$TRN_ENDDATE = "2000-08-25";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="24-25 ��.�. 48") {
				$TRN_STARTDATE = "2005-03-24";
				$TRN_ENDDATE = "2005-03-25";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="24-25 ��.�.42") {
				$TRN_STARTDATE = "1999-06-24";
				$TRN_ENDDATE = "1999-06-25";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="24-25 �.�.43") {
				$TRN_STARTDATE = "2000-01-24";
				$TRN_ENDDATE = "2000-01-25";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="24-25 ����Ҥ� ,1,3-4 �Զع�¹ 2547") {
				$TRN_STARTDATE = "2004-05-24";
				$TRN_ENDDATE = "2004-06-04";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="24-25 ��Ȩԡ�¹ 2550") {
				$TRN_STARTDATE = "2007-11-24";
				$TRN_ENDDATE = "2007-11-25";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="24-25 �.�. ��� 1, 3-4 ��.�. 47") {
				$TRN_STARTDATE = "2004-05-24";
				$TRN_ENDDATE = "2004-06-04";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="24-25 �.�. 47") {
				$TRN_STARTDATE = "2004-05-24";
				$TRN_ENDDATE = "2004-05-25";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="24-25 �.�.41") {
				$TRN_STARTDATE = "1998-12-24";
				$TRN_ENDDATE = "1998-12-25";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="24-25 �ѹ��¹ 2552") {
				$TRN_STARTDATE = "2009-09-24";
				$TRN_ENDDATE = "2009-09-25";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="2-4,6 ����Ҥ� 2548") {
				$TRN_STARTDATE = "2005-05-02";
				$TRN_ENDDATE = "2005-05-06";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="2-4 �.�.42") {
				$TRN_STARTDATE = "1999-08-02";
				$TRN_ENDDATE = "1999-08-04";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="24 ��.�.-5 �.�.2549") {
				$TRN_STARTDATE = "2006-04-24";
				$TRN_ENDDATE = "2006-05-05";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="24 ��.�.-1 �.�.41") {
				$TRN_STARTDATE = "1998-04-24";
				$TRN_ENDDATE = "1998-05-01";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="24 �չҤ�-3 ����¹ 2551") {
				$TRN_STARTDATE = "2008-03-24";
				$TRN_ENDDATE = "2008-04-03";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="24 ��.�.43") {
				$TRN_STARTDATE = "2000-03-24";
				$TRN_ENDDATE = "2000-03-24";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="2-4 ��.�.2548") {
				$TRN_STARTDATE = "2005-03-02";
				$TRN_ENDDATE = "2005-03-04";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="2-4 ��.�. 40") {
				$TRN_STARTDATE = "1997-06-02";
				$TRN_ENDDATE = "1997-06-04";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="24 �.�.-4 �.�. 37") {
				$TRN_STARTDATE = "1994-01-24";
				$TRN_ENDDATE = "1994-05-04";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="2-4 ����Ҥ� 2548") {
				$TRN_STARTDATE = "2005-05-02";
				$TRN_ENDDATE = "2005-05-04";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="24 ����Ҥ� - 3 �Զع�¹ 2548") {
				$TRN_STARTDATE = "2005-05-24";
				$TRN_ENDDATE = "2005-06-03";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="24 �.�.,1,8 �.�. 39") {
				$TRN_STARTDATE = "1996-11-24";
				$TRN_ENDDATE = "1996-12-08";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="24 �.�.50") {
				$TRN_STARTDATE = "2007-05-24";
				$TRN_ENDDATE = "2007-05-24";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="24 �.�.42") {
				$TRN_STARTDATE = "1999-05-24";
				$TRN_ENDDATE = "1999-05-24";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="2-4 �.�. 48") {
				$TRN_STARTDATE = "2005-05-02";
				$TRN_ENDDATE = "2005-05-04";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="2-4 ����Ҿѹ�� 2548") {
				$TRN_STARTDATE = "2005-02-02";
				$TRN_ENDDATE = "2005-02-04";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="24 �ѹ��¹ 2551") {
				$TRN_STARTDATE = "2008-09-24";
				$TRN_ENDDATE = "2008-09-24";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="24 �.�.47") {
				$TRN_STARTDATE = "2004-02-24";
				$TRN_ENDDATE = "2004-02-24";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="2-4 �.�. 52") {
				$TRN_STARTDATE = "2009-02-02";
				$TRN_ENDDATE = "2009-02-04";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="24 �.�.-11 �.�. 44") {
				$TRN_STARTDATE = "2001-07-24";
				$TRN_ENDDATE = "2001-09-11";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="2-4 �.�. 40") {
				$TRN_STARTDATE = "1997-07-02";
				$TRN_ENDDATE = "1997-07-04";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="2-4  ����Ҥ�  2549") {
				$TRN_STARTDATE = "2006-05-02";
				$TRN_ENDDATE = "2006-05-04";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="24 - 27 �.�. 2546") {
				$TRN_STARTDATE = "2003-08-24";
				$TRN_ENDDATE = "2003-08-27";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="23-27 �.�. 47") {
				$TRN_STARTDATE = "2004-08-23";
				$TRN_ENDDATE = "2004-08-27";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="23-27 ��.�. 44") {
				$TRN_STARTDATE = "2001-04-23";
				$TRN_ENDDATE = "2001-04-27";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="23-27 �չҤ� 2552") {
				$TRN_STARTDATE = "2009-03-23";
				$TRN_ENDDATE = "2009-03-27";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="23-27 ��.�. 40") {
				$TRN_STARTDATE = "1997-06-23";
				$TRN_ENDDATE = "1997-06-27";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="23-27 ����Ҿѹ�� 2552") {
				$TRN_STARTDATE = "2009-02-23";
				$TRN_ENDDATE = "2009-02-27";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="23-27 �á�Ҥ� 2550") {
				$TRN_STARTDATE = "2007-07-23";
				$TRN_ENDDATE = "2007-07-27";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="23-27 �.�. 47") {
				$TRN_STARTDATE = "2004-02-23";
				$TRN_ENDDATE = "2004-02-27";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="23-27 �.�. 42") {
				$TRN_STARTDATE = "1999-07-23";
				$TRN_ENDDATE = "1999-07-27";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="23-26 �ԧ�Ҥ�  2547") {
				$TRN_STARTDATE = "2004-08-23";
				$TRN_ENDDATE = "2004-08-26";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="23-26 �.�.2547") {
				$TRN_STARTDATE = "2004-08-23";
				$TRN_ENDDATE = "2004-08-26";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="23-26 �.�. 47") {
				$TRN_STARTDATE = "2004-08-23";
				$TRN_ENDDATE = "2004-08-26";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="23-26 ��.�.2547") {
				$TRN_STARTDATE = "2004-03-23";
				$TRN_ENDDATE = "2004-03-26";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="23-25 �.�.43") {
				$TRN_STARTDATE = "2000-08-23";
				$TRN_ENDDATE = "2000-08-25";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="23-25 �.�.2549") {
				$TRN_STARTDATE = "2006-08-23";
				$TRN_ENDDATE = "2006-08-25";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="23-25 ��.�. 44") {
				$TRN_STARTDATE = "2001-04-23";
				$TRN_ENDDATE = "2001-04-25";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="23-25 ��.�.41") {
				$TRN_STARTDATE = "1998-03-23";
				$TRN_ENDDATE = "1998-03-25";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="23-25 ��.�. 40") {
				$TRN_STARTDATE = "1997-06-23";
				$TRN_ENDDATE = "1997-06-25";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="23-25 �.�. 47") {
				$TRN_STARTDATE = "2004-11-23";
				$TRN_ENDDATE = "2004-11-25";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="23-25 �.�. 44") {
				$TRN_STARTDATE = "2001-11-23";
				$TRN_ENDDATE = "2001-11-25";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="23-25 �.�.42") {
				$TRN_STARTDATE = "1999-02-23";
				$TRN_ENDDATE = "1999-02-25";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="23-24 ��.�.2548") {
				$TRN_STARTDATE = "2005-03-23";
				$TRN_ENDDATE = "2005-03-24";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="23-24 ��.�. 52") {
				$TRN_STARTDATE = "2009-03-23";
				$TRN_ENDDATE = "2009-03-24";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="23-24 �.�. 39") {
				$TRN_STARTDATE = "1996-12-23";
				$TRN_ENDDATE = "1996-12-24";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="23-24 �.�. 52") {
				$TRN_STARTDATE = "2009-02-23";
				$TRN_ENDDATE = "2009-02-24";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="23,29,30 ��.�. 40") {
				$TRN_STARTDATE = "1997-03-23";
				$TRN_ENDDATE = "1997-03-30";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="23 �.�.-17 �.�.47") {
				$TRN_STARTDATE = "2004-08-23";
				$TRN_ENDDATE = "2004-09-17";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="2-3 ����¹ 2552") {
				$TRN_STARTDATE = "2009-04-02";
				$TRN_ENDDATE = "2009-04-03";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="23 ��.�.48") {
				$TRN_STARTDATE = "2005-03-23";
				$TRN_ENDDATE = "2005-03-23";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="2-3 ��.�. 52") {
				$TRN_STARTDATE = "2009-03-02";
				$TRN_ENDDATE = "2009-03-03";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="2-3 �ѹ�Ҥ� 2551") {
				$TRN_STARTDATE = "2008-12-02";
				$TRN_ENDDATE = "2008-12-03";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="23 �.�.2547") {
				$TRN_STARTDATE = "2004-12-23";
				$TRN_ENDDATE = "2004-12-23";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="2-3 �.�.40") {
				$TRN_STARTDATE = "1997-10-02";
				$TRN_ENDDATE = "1997-10-03";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="2-3 �ѹ��¹ 2552") {
				$TRN_STARTDATE = "2009-09-02";
				$TRN_ENDDATE = "2009-09-03";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="2-3 �.�.42") {
				$TRN_STARTDATE = "1999-09-02";
				$TRN_ENDDATE = "1999-09-03";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="23 �.�.41") {
				$TRN_STARTDATE = "1998-09-23";
				$TRN_ENDDATE = "1998-09-23";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="23 �.�.42") {
				$TRN_STARTDATE = "1999-02-23";
				$TRN_ENDDATE = "1999-02-23";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="23 �.�.-21 ��.�.43") {
				$TRN_STARTDATE = "2000-02-23";
				$TRN_ENDDATE = "2000-04-21";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="2-3 �.�.46") {
				$TRN_STARTDATE = "2003-07-02";
				$TRN_ENDDATE = "2003-07-03";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="23 �.�.42") {
				$TRN_STARTDATE = "1999-07-23";
				$TRN_ENDDATE = "1999-07-23";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="23 �.�.-30 �.�.41") {
				$TRN_STARTDATE = "1998-07-23";
				$TRN_ENDDATE = "1998-09-30";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="2-25 �.�.43") {
				$TRN_STARTDATE = "2000-05-02";
				$TRN_ENDDATE = "2000-05-25";
				$TRN_DAY = 24;
			} elseif ($TRN_REMARK=="22-27 �.�. 44") {
				$TRN_STARTDATE = "2001-10-22";
				$TRN_ENDDATE = "2001-10-27";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="22-26 ��.�.39") {
				$TRN_STARTDATE = "1996-04-22";
				$TRN_ENDDATE = "1996-04-26";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="22-26 �.�.50") {
				$TRN_STARTDATE = "2007-01-22";
				$TRN_ENDDATE = "2007-01-26";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="22-26 �.�. 39") {
				$TRN_STARTDATE = "1996-01-22";
				$TRN_ENDDATE = "1996-01-26";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="22-26 �.�.43") {
				$TRN_STARTDATE = "2000-05-22";
				$TRN_ENDDATE = "2000-05-26";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="22-26 �.�. 43") {
				$TRN_STARTDATE = "2000-05-22";
				$TRN_ENDDATE = "2000-05-26";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="22-26 �.�. 51") {
				$TRN_STARTDATE = "2008-12-22";
				$TRN_ENDDATE = "2008-12-26";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="22-26 �ѹ��¹ 2551") {
				$TRN_STARTDATE = "2008-09-22";
				$TRN_ENDDATE = "2008-09-26";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="22-26 �.�.51") {
				$TRN_STARTDATE = "2008-09-22";
				$TRN_ENDDATE = "2008-09-26";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="22-26 �.�.39") {
				$TRN_STARTDATE = "1996-07-22";
				$TRN_ENDDATE = "1996-07-26";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="22-25 �.�. 47") {
				$TRN_STARTDATE = "2004-08-22";
				$TRN_ENDDATE = "2004-08-25";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="22-25 �.�. 39") {
				$TRN_STARTDATE = "1996-08-22";
				$TRN_ENDDATE = "1996-08-25";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="22-25 �Զع�¹  2547") {
				$TRN_STARTDATE = "2004-06-22";
				$TRN_ENDDATE = "2004-06-25";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="22-25 ��.�. 47") {
				$TRN_STARTDATE = "2004-06-22";
				$TRN_ENDDATE = "2004-06-25";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="22-24 �ԧ�Ҥ� 2548") {
				$TRN_STARTDATE = "2005-08-22";
				$TRN_ENDDATE = "2005-08-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="22-24 �չҤ� 2547") {
				$TRN_STARTDATE = "2004-03-22";
				$TRN_ENDDATE = "2004-03-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="22-24 ��.�. 47") {
				$TRN_STARTDATE = "2004-03-22";
				$TRN_ENDDATE = "2004-03-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="22-24 �Զع�¹ 2548") {
				$TRN_STARTDATE = "2005-06-22";
				$TRN_ENDDATE = "2005-06-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="22-24 ��.�.52") {
				$TRN_STARTDATE = "2008-06-22";
				$TRN_ENDDATE = "2008-06-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="22-24 ��.�.41") {
				$TRN_STARTDATE = "1998-06-22";
				$TRN_ENDDATE = "1998-06-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="22-24 ��.�. 52") {
				$TRN_STARTDATE = "2009-06-22";
				$TRN_ENDDATE = "2009-06-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="22-24 �.�.43") {
				$TRN_STARTDATE = "2000-05-22";
				$TRN_ENDDATE = "2000-05-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="22-24 �.�. 51") {
				$TRN_STARTDATE = "2008-12-22";
				$TRN_ENDDATE = "2008-12-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="22-24 �.�. 40") {
				$TRN_STARTDATE = "1997-12-22";
				$TRN_ENDDATE = "1997-12-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="22-24 �ѹ��¹ 2551") {
				$TRN_STARTDATE = "2008-09-22";
				$TRN_ENDDATE = "2008-09-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="22-24 �.�.41") {
				$TRN_STARTDATE = "1998-07-22";
				$TRN_ENDDATE = "1998-07-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="22-23 �ԧ�Ҥ� 2552") {
				$TRN_STARTDATE = "2009-08-22";
				$TRN_ENDDATE = "2009-08-23";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="22-23 ��.�. 40") {
				$TRN_STARTDATE = "1997-04-22";
				$TRN_ENDDATE = "1997-04-23";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="22-23 ��.�. 44") {
				$TRN_STARTDATE = "2001-03-22";
				$TRN_ENDDATE = "2001-03-23";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="22-23 �.�. 40") {
				$TRN_STARTDATE = "1997-05-22";
				$TRN_ENDDATE = "1997-05-23";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="22-23 �.�.2548") {
				$TRN_STARTDATE = "2005-12-22";
				$TRN_ENDDATE = "2005-12-23";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="22-23 �.�.41") {
				$TRN_STARTDATE = "1998-09-22";
				$TRN_ENDDATE = "1998-09-23";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="22-23 �.�. 41") {
				$TRN_STARTDATE = "1998-09-22";
				$TRN_ENDDATE = "1998-09-23";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="22-23 �.�.47") {
				$TRN_STARTDATE = "2004-07-22";
				$TRN_ENDDATE = "2004-07-23";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="22 �.�.-8 �.�.43") {
				$TRN_STARTDATE = "2000-08-22";
				$TRN_ENDDATE = "2000-09-08";
				$TRN_DAY = 17;
			} elseif ($TRN_REMARK=="22 ��.�.-15 ��.�. 40") {
				$TRN_STARTDATE = "1997-03-22";
				$TRN_ENDDATE = "1997-06-15";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="22 �� .�. 45") {
				$TRN_STARTDATE = "2002-03-22";
				$TRN_ENDDATE = "2002-03-22";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="22 ��.�.-1 �.�.41") {
				$TRN_STARTDATE = "1998-06-22";
				$TRN_ENDDATE = "1998-09-01";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="22 �.�. - 2 �.�. 44") {
				$TRN_STARTDATE = "2001-01-22";
				$TRN_ENDDATE = "2001-02-02";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="22 �.�.-25 �.�. 38") {
				$TRN_STARTDATE = "1995-05-22";
				$TRN_ENDDATE = "1995-08-25";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="22 �.�.-11 �.�. 44") {
				$TRN_STARTDATE = "2001-05-22";
				$TRN_ENDDATE = "2001-07-11";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="22 �.�.-11 ��.�.42") {
				$TRN_STARTDATE = "1999-02-22";
				$TRN_ENDDATE = "1999-03-11";
				$TRN_DAY = 17;
			} elseif ($TRN_REMARK=="22 �.�.-10 ��.�.42") {
				$TRN_STARTDATE = "1999-02-22";
				$TRN_ENDDATE = "1999-03-10";
				$TRN_DAY = 16;
			} elseif ($TRN_REMARK=="22 �.�. - 9 �.�. 39") {
				$TRN_STARTDATE = "1996-07-22";
				$TRN_ENDDATE = "1996-08-09";
				$TRN_DAY = 18;
			} elseif ($TRN_REMARK=="2-16 �.�.42") {
				$TRN_STARTDATE = "1999-12-02";
				$TRN_ENDDATE = "1999-12-16";
				$TRN_DAY = 15;
			} elseif ($TRN_REMARK=="21-26  �á�Ҥ� 2549") {
				$TRN_STARTDATE = "2006-07-21";
				$TRN_ENDDATE = "2006-07-26";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="21-25 ��.�. 40") {
				$TRN_STARTDATE = "1997-04-21";
				$TRN_ENDDATE = "1997-04-25";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="21-25 �չҤ� 2548") {
				$TRN_STARTDATE = "2005-03-21";
				$TRN_ENDDATE = "2005-03-25";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="21-25 ��.�.42") {
				$TRN_STARTDATE = "1999-06-21";
				$TRN_ENDDATE = "1999-06-25";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="21-25 ��Ȩԡ�¹ 2548") {
				$TRN_STARTDATE = "2005-11-21";
				$TRN_ENDDATE = "2005-11-25";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="21-25 �.�.41") {
				$TRN_STARTDATE = "1998-09-21";
				$TRN_ENDDATE = "1998-09-25";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="21-25 �.�.43") {
				$TRN_STARTDATE = "2000-02-21";
				$TRN_ENDDATE = "2000-02-25";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="21-24 �.�. 48") {
				$TRN_STARTDATE = "2005-02-21";
				$TRN_ENDDATE = "2005-02-24";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="21-23 ����¹ 2548") {
				$TRN_STARTDATE = "2005-04-21";
				$TRN_ENDDATE = "2005-04-23";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="21-23 �Զع�¹ 2547") {
				$TRN_STARTDATE = "2004-06-21";
				$TRN_ENDDATE = "2004-06-23";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="21-23 ��.�. 52") {
				$TRN_STARTDATE = "2009-06-21";
				$TRN_ENDDATE = "2009-06-23";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="21-23 ��.�. 47") {
				$TRN_STARTDATE = "2004-06-21";
				$TRN_ENDDATE = "2004-06-23";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="21-23 ��.�. 39") {
				$TRN_STARTDATE = "1996-06-21";
				$TRN_ENDDATE = "1996-06-23";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="21-23 �.�. 40") {
				$TRN_STARTDATE = "1997-05-21";
				$TRN_ENDDATE = "1997-05-23";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="21-23 �.�. 42") {
				$TRN_STARTDATE = "1999-12-21";
				$TRN_ENDDATE = "1999-12-23";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="21-22 �ԧ�Ҥ� 2551") {
				$TRN_STARTDATE = "2008-08-21";
				$TRN_ENDDATE = "2008-08-22";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="21-22 �.�.48") {
				$TRN_STARTDATE = "2005-02-21";
				$TRN_ENDDATE = "2005-02-22";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="2-10 �.�.42") {
				$TRN_STARTDATE = "1999-08-02";
				$TRN_ENDDATE = "1999-08-10";
				$TRN_DAY = 9;
			} elseif ($TRN_REMARK=="21/02/2548-21/06/2548") {
				$TRN_STARTDATE = "2005-02-21";
				$TRN_ENDDATE = "2005-06-21";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="21,25-28 �.�.43") {
				$TRN_STARTDATE = "2000-07-21";
				$TRN_ENDDATE = "2000-07-28";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="21 ����¹-1 ����Ҥ� 2551") {
				$TRN_STARTDATE = "2008-04-21";
				$TRN_ENDDATE = "2008-05-01";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="21 ��.�. 40") {
				$TRN_STARTDATE = "1997-03-21";
				$TRN_ENDDATE = "1997-03-21";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="21 ��.�.-2�.�.2547") {
				$TRN_STARTDATE = "2004-06-21";
				$TRN_ENDDATE = "2004-07-02";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="21 ��.�.-2 �.�. 47") {
				$TRN_STARTDATE = "2004-06-21";
				$TRN_ENDDATE = "2004-07-02";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="21 ��.�.-18 �.�.43") {
				$TRN_STARTDATE = "2000-06-21";
				$TRN_ENDDATE = "2000-08-18";
				$TRN_DAY = 28;
			} elseif ($TRN_REMARK=="21 �.�.-15 �.�.40") {
				$TRN_STARTDATE = "1997-05-21";
				$TRN_ENDDATE = "1997-08-15";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="21 �.�.-2 �.�.42") {
				$TRN_STARTDATE = "1999-07-21";
				$TRN_ENDDATE = "1999-09-02";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="20-31 �.�. 44") {
				$TRN_STARTDATE = "2001-08-20";
				$TRN_ENDDATE = "2001-08-31";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="20-31 ��.�.2549") {
				$TRN_STARTDATE = "2006-03-20";
				$TRN_ENDDATE = "2006-03-31";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="20-26 ��.�. 48") {
				$TRN_STARTDATE = "2005-03-20";
				$TRN_ENDDATE = "2005-03-26";
				$TRN_DAY = 7;
			} elseif ($TRN_REMARK=="20-25 �á�Ҥ� 2548") {
				$TRN_STARTDATE = "2005-07-20";
				$TRN_ENDDATE = "2005-07-25";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="20-24 �ԧ�Ҥ� 2550") {
				$TRN_STARTDATE = "2007-08-20";
				$TRN_ENDDATE = "2007-08-24";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="20-24 ��.�. 52") {
				$TRN_STARTDATE = "2009-04-20";
				$TRN_ENDDATE = "2009-04-24";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="20-24 ��.�.43") {
				$TRN_STARTDATE = "2000-03-20";
				$TRN_ENDDATE = "2000-03-24";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="20-24 �.�. 47") {
				$TRN_STARTDATE = "2004-09-20";
				$TRN_ENDDATE = "2004-09-24";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="20-24  �Զع�¹ 2548") {
				$TRN_STARTDATE = "2005-06-20";
				$TRN_ENDDATE = "2005-06-24";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="20-23 ����¹ 2547") {
				$TRN_STARTDATE = "2004-04-20";
				$TRN_ENDDATE = "2004-04-23";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="20-23 ��.�. 44") {
				$TRN_STARTDATE = "2001-03-20";
				$TRN_ENDDATE = "2001-03-23";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="20-23 �á�Ҥ� 2547") {
				$TRN_STARTDATE = "2004-07-20";
				$TRN_ENDDATE = "2004-07-23";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="20-23 �.�. 47") {
				$TRN_STARTDATE = "2004-07-20";
				$TRN_ENDDATE = "2004-07-23";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="20-22 �ԧ�Ҥ� 2551") {
				$TRN_STARTDATE = "2008-08-20";
				$TRN_ENDDATE = "2008-08-22";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="20-22 ����¹ 2548") {
				$TRN_STARTDATE = "2005-04-20";
				$TRN_ENDDATE = "2005-04-22";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="20-22 �Զع�¹ 2550") {
				$TRN_STARTDATE = "2007-06-20";
				$TRN_ENDDATE = "2007-06-22";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="20-22 �.�.52") {
				$TRN_STARTDATE = "2009-05-20";
				$TRN_ENDDATE = "2009-05-22";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="20-21 �.�. 40") {
				$TRN_STARTDATE = "1997-11-20";
				$TRN_ENDDATE = "1997-11-21";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="20 �ԧ�Ҥ� 2551") {
				$TRN_STARTDATE = "2008-08-20";
				$TRN_ENDDATE = "2008-08-20";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="20 ��.�.-1 ��.�. 40") {
				$TRN_STARTDATE = "1997-04-20";
				$TRN_ENDDATE = "1997-06-01";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="20 ��.�. - 1 �.�. 52") {
				$TRN_STARTDATE = "2009-04-20";
				$TRN_ENDDATE = "2009-05-01";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="20 ��.�. 41") {
				$TRN_STARTDATE = "1998-03-20";
				$TRN_ENDDATE = "1998-03-20";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="20 ��.�.2548") {
				$TRN_STARTDATE = "2005-06-20";
				$TRN_ENDDATE = "2005-06-20";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="20 ��.�.-21 �.�.44") {
				$TRN_STARTDATE = "2001-06-20";
				$TRN_ENDDATE = "2001-08-21";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="20 �.�. 38-28 �.�. 39") {
				$TRN_STARTDATE = "1995-11-20";
				$TRN_ENDDATE = "1996-02-28";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="20 �.�. 52") {
				$TRN_STARTDATE = "2009-05-20";
				$TRN_ENDDATE = "2009-05-20";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="20 �.�.-7 �.�. 40") {
				$TRN_STARTDATE = "1997-10-20";
				$TRN_ENDDATE = "1997-11-07";
				$TRN_DAY = 19;
			} elseif ($TRN_REMARK=="20 �.�.43") {
				$TRN_STARTDATE = "2000-09-20";
				$TRN_ENDDATE = "2000-09-20";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="2,9,16 ��.�. 40") {
				$TRN_STARTDATE = "1997-03-02";
				$TRN_ENDDATE = "1997-03-16";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="2 �.�.-17 �.�.42") {
				$TRN_STARTDATE = "1999-08-02";
				$TRN_ENDDATE = "1999-09-17";
				$TRN_DAY = 47;
			} elseif ($TRN_REMARK=="2 ��.�.-14 �.�. 35") {
				$TRN_STARTDATE = "1992-04-02";
				$TRN_ENDDATE = "1992-07-14";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="2 ��.�.-12 �.�. 37") {
				$TRN_STARTDATE = "1994-06-02";
				$TRN_ENDDATE = "1994-09-12";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="2 �.�. 39 - 7 �.�. 40") {
				$TRN_STARTDATE = "1996-12-02";
				$TRN_ENDDATE = "1997-02-07";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="2 �.�. 52") {
				$TRN_STARTDATE = "2009-02-02";
				$TRN_ENDDATE = "2009-02-02";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="2 �.�.42") {
				$TRN_STARTDATE = "1999-07-02";
				$TRN_ENDDATE = "1999-07-02";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="19-30 �.�. 52") {
				$TRN_STARTDATE = "2009-01-19";
				$TRN_ENDDATE = "2009-01-30";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="19-28 ��.�. 39") {
				$TRN_STARTDATE = "1996-06-19";
				$TRN_ENDDATE = "1996-06-28";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK=="19-23 ����¹  2547") {
				$TRN_STARTDATE = "2004-04-19";
				$TRN_ENDDATE = "2004-04-23";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="19-23 ��.�.42") {
				$TRN_STARTDATE = "1999-04-19";
				$TRN_ENDDATE = "1999-04-23";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="19-23 ��.�. 47") {
				$TRN_STARTDATE = "2004-04-19";
				$TRN_ENDDATE = "2004-04-23";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="19-23 �.�. 52") {
				$TRN_STARTDATE = "2009-01-19";
				$TRN_ENDDATE = "2009-01-23";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="19-23 �.�. 40") {
				$TRN_STARTDATE = "1997-05-19";
				$TRN_ENDDATE = "1997-05-23";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="19-23 �.�.42") {
				$TRN_STARTDATE = "1999-07-19";
				$TRN_ENDDATE = "1999-07-23";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="19-23 �.�. 47") {
				$TRN_STARTDATE = "2004-07-19";
				$TRN_ENDDATE = "2004-07-23";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="19-23 �.�. 42") {
				$TRN_STARTDATE = "1999-07-19";
				$TRN_ENDDATE = "1999-07-23";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="19-22 �á�Ҥ� 2547") {
				$TRN_STARTDATE = "2004-07-19";
				$TRN_ENDDATE = "2004-07-22";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="19-22 �.�. 47") {
				$TRN_STARTDATE = "2004-07-19";
				$TRN_ENDDATE = "2004-07-22";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="19-21 �.�.42") {
				$TRN_STARTDATE = "1999-08-19";
				$TRN_ENDDATE = "1999-08-21";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="19-21 ��.�.43") {
				$TRN_STARTDATE = "2000-04-19";
				$TRN_ENDDATE = "2000-04-21";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="19-21 ��.�.39") {
				$TRN_STARTDATE = "1996-03-19";
				$TRN_ENDDATE = "1996-03-21";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="19-21 �.�. 52") {
				$TRN_STARTDATE = "2009-01-19";
				$TRN_ENDDATE = "2009-01-21";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="19-20 �.�. 42") {
				$TRN_STARTDATE = "1999-08-19";
				$TRN_ENDDATE = "1999-08-20";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="19-20 ��.�.43") {
				$TRN_STARTDATE = "2000-04-19";
				$TRN_ENDDATE = "2000-04-20";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="19-20 ��.�.52") {
				$TRN_STARTDATE = "2009-03-19";
				$TRN_ENDDATE = "2009-03-20";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="19-20 �ѹ��¹ 2552") {
				$TRN_STARTDATE = "2009-09-19";
				$TRN_ENDDATE = "2009-09-20";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="19 �.�.-22 �.�.43 (੾���ѹ�ҷԵ��)") {
				$TRN_STARTDATE = "2000-08-19";
				$TRN_ENDDATE = "2000-10-22";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="19 ��.�.-4 �.�.42") {
				$TRN_STARTDATE = "1999-04-19";
				$TRN_ENDDATE = "1999-05-04";
				$TRN_DAY = 16;
			} elseif ($TRN_REMARK=="19 ��.�.52") {
				$TRN_STARTDATE = "2009-06-19";
				$TRN_ENDDATE = "2009-06-19";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="19 ��.�. 52") {
				$TRN_STARTDATE = "2009-06-19";
				$TRN_ENDDATE = "2009-06-19";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="19 ��.�. 43") {
				$TRN_STARTDATE = "2000-06-19";
				$TRN_ENDDATE = "2000-06-19";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="19 �.�.-5�.�. 52") {
				$TRN_STARTDATE = "2009-01-19";
				$TRN_ENDDATE = "2009-02-05";
				$TRN_DAY = 18;
			} elseif ($TRN_REMARK=="19 �.�. 43") {
				$TRN_STARTDATE = "2000-01-19";
				$TRN_ENDDATE = "2000-01-19";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="19 �.�. 2547") {
				$TRN_STARTDATE = "2004-05-19";
				$TRN_ENDDATE = "2004-05-19";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="19 �ѹ�Ҥ� 2551") {
				$TRN_STARTDATE = "2008-12-19";
				$TRN_ENDDATE = "2008-12-19";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="19 �.�.-5 �.�.42") {
				$TRN_STARTDATE = "1999-10-19";
				$TRN_ENDDATE = "1999-11-05";
				$TRN_DAY = 18;
			} elseif ($TRN_REMARK=="19 �.�.-30 �.�. 39") {
				$TRN_STARTDATE = "1996-10-19";
				$TRN_ENDDATE = "1996-11-30";
				$TRN_DAY = 43;
			} elseif ($TRN_REMARK=="19 -30 �.�. 45") {
				$TRN_STARTDATE = "2002-08-19";
				$TRN_ENDDATE = "2002-08-30";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="18-24 ��.�. 47") {
				$TRN_STARTDATE = "2004-03-18";
				$TRN_ENDDATE = "2004-03-24";
				$TRN_DAY = 7;
			} elseif ($TRN_REMARK=="18-22 ����¹ 2548") {
				$TRN_STARTDATE = "2005-04-18";
				$TRN_ENDDATE = "2005-04-22";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="18-22 �.�.42") {
				$TRN_STARTDATE = "1999-01-18";
				$TRN_ENDDATE = "1999-01-22";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="18-21 �.�. 41") {
				$TRN_STARTDATE = "1998-08-18";
				$TRN_ENDDATE = "1998-08-21";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="18-21 �.�. 41") {
				$TRN_STARTDATE = "1998-05-18";
				$TRN_ENDDATE = "1998-05-21";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="18-20 �ԧ�Ҥ� 2552") {
				$TRN_STARTDATE = "2009-08-18";
				$TRN_ENDDATE = "2009-08-20";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="18-20 �.�. 47") {
				$TRN_STARTDATE = "2004-08-18";
				$TRN_ENDDATE = "2004-08-20";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="18-20 ����¹ 2548") {
				$TRN_STARTDATE = "2005-04-18";
				$TRN_ENDDATE = "2005-04-20";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="18-20 ��.�.43") {
				$TRN_STARTDATE = "2000-04-18";
				$TRN_ENDDATE = "2000-04-20";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="18-20 �չҤ� 2547") {
				$TRN_STARTDATE = "2004-03-18";
				$TRN_ENDDATE = "2004-03-20";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="18-20 ��.�. 47") {
				$TRN_STARTDATE = "2004-03-18";
				$TRN_ENDDATE = "2004-03-20";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="18-20 �.�.43") {
				$TRN_STARTDATE = "2000-01-18";
				$TRN_ENDDATE = "2000-01-20";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="18-20 �.�. 43") {
				$TRN_STARTDATE = "2000-01-18";
				$TRN_ENDDATE = "2000-01-20";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="18-20 ����Ҥ� 2548") {
				$TRN_STARTDATE = "2005-05-18";
				$TRN_ENDDATE = "2005-05-20";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="18-20 �.�.48") {
				$TRN_STARTDATE = "2005-05-18";
				$TRN_ENDDATE = "2005-05-20";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="18-19 ��� 25-26 ��.�. 43") {
				$TRN_STARTDATE = "2000-03-18";
				$TRN_ENDDATE = "2000-03-26";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="18-19 �.�. 40") {
				$TRN_STARTDATE = "1997-12-18";
				$TRN_ENDDATE = "1997-12-19";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="18-19 �.�. 52") {
				$TRN_STARTDATE = "2009-02-18";
				$TRN_ENDDATE = "2009-02-19";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="18-19 �.�. 44") {
				$TRN_STARTDATE = "2001-07-18";
				$TRN_ENDDATE = "2001-07-19";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="18 �.�.-24 �.�.42") {
				$TRN_STARTDATE = "1999-08-18";
				$TRN_ENDDATE = "1999-09-24";
				$TRN_DAY = 38;
			} elseif ($TRN_REMARK=="18 �.�.-26 �.�.42") {
				$TRN_STARTDATE = "1999-01-18";
				$TRN_ENDDATE = "1999-02-26";
				$TRN_DAY = 40;
			} elseif ($TRN_REMARK=="18 �.�.-26 �.�.42") {
				$TRN_STARTDATE = "1999-09-18";
				$TRN_ENDDATE = "1999-12-26";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="18 �.�.42") {
				$TRN_STARTDATE = "1999-02-18";
				$TRN_ENDDATE = "1999-02-18";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="18 �.�.-9 �.�. 39") {
				$TRN_STARTDATE = "1996-07-18";
				$TRN_ENDDATE = "1996-09-09";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="18 �.�.-6 �.�. 44") {
				$TRN_STARTDATE = "2001-07-18";
				$TRN_ENDDATE = "2001-09-06";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="18 �.�.-2 �.�.2548") {
				$TRN_STARTDATE = "2005-07-18";
				$TRN_ENDDATE = "2005-09-02";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="18 - 23 �.�. 2546") {
				$TRN_STARTDATE = "2003-08-18";
				$TRN_ENDDATE = "2003-08-23";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="18 - 20 �á�Ҥ� 2548") {
				$TRN_STARTDATE = "2005-07-18";
				$TRN_ENDDATE = "2005-07-20";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="17�.�.-8 ��.�.37") {
				$TRN_STARTDATE = "1994-01-17";
				$TRN_ENDDATE = "1994-04-08";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="17-25 �ԧ�Ҥ� 2548 �٧ҹ 23 - 25 �ԧ�Ҥ�") {
				$TRN_STARTDATE = "2005-08-17";
				$TRN_ENDDATE = "2005-08-25";
				$TRN_DAY = 9;
			} elseif ($TRN_REMARK=="17-23 ��.�.47") {
				$TRN_STARTDATE = "2004-06-17";
				$TRN_ENDDATE = "2004-06-23";
				$TRN_DAY = 7;
			} elseif ($TRN_REMARK=="17-22 �.�. 42") {
				$TRN_STARTDATE = "1999-01-17";
				$TRN_ENDDATE = "1999-01-22";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="17-21 ����Ҥ�  2547") {
				$TRN_STARTDATE = "2004-05-17";
				$TRN_ENDDATE = "2004-05-21";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="17-21 �.�.42") {
				$TRN_STARTDATE = "1999-05-17";
				$TRN_ENDDATE = "1999-05-21";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="17-21 �.�. 47") {
				$TRN_STARTDATE = "2004-05-17";
				$TRN_ENDDATE = "2004-05-21";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="17-20 �.�.2547") {
				$TRN_STARTDATE = "2004-08-17";
				$TRN_ENDDATE = "2004-08-20";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="17-20 �.�. 47") {
				$TRN_STARTDATE = "2004-08-17";
				$TRN_ENDDATE = "2004-08-20";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="17-20 ����Ҥ� 2548") {
				$TRN_STARTDATE = "2005-05-17";
				$TRN_ENDDATE = "2005-05-20";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="17-20 �.�. 47") {
				$TRN_STARTDATE = "2004-11-17";
				$TRN_ENDDATE = "2004-11-20";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="17-19 ��.�.42") {
				$TRN_STARTDATE = "1999-03-17";
				$TRN_ENDDATE = "1999-03-19";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="17-19 �ѹ��¹ 2551") {
				$TRN_STARTDATE = "2008-09-17";
				$TRN_ENDDATE = "2008-09-19";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="17-18 �.�.2548") {
				$TRN_STARTDATE = "2005-11-17";
				$TRN_ENDDATE = "2005-11-18";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="17-18 �.�.44") {
				$TRN_STARTDATE = "2001-05-17";
				$TRN_ENDDATE = "2001-05-18";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="17-18 �.�.41") {
				$TRN_STARTDATE = "1998-12-17";
				$TRN_ENDDATE = "1998-12-18";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="17-18 �.�.2548") {
				$TRN_STARTDATE = "2005-02-17";
				$TRN_ENDDATE = "2005-02-18";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="17,24,31 �.�. 40") {
				$TRN_STARTDATE = "1997-05-17";
				$TRN_ENDDATE = "1997-05-31";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="17 �.�.43") {
				$TRN_STARTDATE = "2000-08-17";
				$TRN_ENDDATE = "2000-08-17";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="17 �.�.-13 �.�.41") {
				$TRN_STARTDATE = "1998-08-17";
				$TRN_ENDDATE = "1998-10-13";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="17 ��.�.-11 ��.�.40") {
				$TRN_STARTDATE = "1997-03-17";
				$TRN_ENDDATE = "1997-04-11";
				$TRN_DAY = 26;
			} elseif ($TRN_REMARK=="17 �.�.43") {
				$TRN_STARTDATE = "2000-01-17";
				$TRN_ENDDATE = "2000-01-17";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="17 �ѹ��¹ - 12 ���Ҥ� 2550") {
				$TRN_STARTDATE = "2007-09-17";
				$TRN_ENDDATE = "2007-10-12";
				$TRN_DAY = 26;
			} elseif ($TRN_REMARK=="17 �.�.2547") {
				$TRN_STARTDATE = "2004-09-17";
				$TRN_ENDDATE = "2004-09-17";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="17 �.�. 47") {
				$TRN_STARTDATE = "2004-09-17";
				$TRN_ENDDATE = "2004-09-17";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="17 �.�.-9 ��.�.42") {
				$TRN_STARTDATE = "1999-02-17";
				$TRN_ENDDATE = "1999-04-09";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="16-27 ��.�. 52") {
				$TRN_STARTDATE = "2009-03-16";
				$TRN_ENDDATE = "2009-03-27";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="16-26 ��.�.41") {
				$TRN_STARTDATE = "1998-06-16";
				$TRN_ENDDATE = "1998-06-26";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="16-26 ���Ҥ� 2549") {
				$TRN_STARTDATE = "2006-01-16";
				$TRN_ENDDATE = "2006-01-26";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="16-26 �.�. 51") {
				$TRN_STARTDATE = "2008-09-16";
				$TRN_ENDDATE = "2008-09-26";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="16-23 ��.�.42") {
				$TRN_STARTDATE = "1999-03-16";
				$TRN_ENDDATE = "1999-03-23";
				$TRN_DAY = 8;
			} elseif ($TRN_REMARK=="16-22 �.�. 40") {
				$TRN_STARTDATE = "1997-02-16";
				$TRN_ENDDATE = "1997-02-22";
				$TRN_DAY = 7;
			} elseif ($TRN_REMARK=="16-20, 23-24 ����Ҿѹ�� 2552") {
				$TRN_STARTDATE = "2009-02-16";
				$TRN_ENDDATE = "2009-02-24";
				$TRN_DAY = 7;
			} elseif ($TRN_REMARK=="16-20 �չҤ� 2552") {
				$TRN_STARTDATE = "2009-03-16";
				$TRN_ENDDATE = "2009-03-20";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="16-20 ��.�. 47") {
				$TRN_STARTDATE = "2004-03-16";
				$TRN_ENDDATE = "2004-03-20";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="16-20 �.�. 48") {
				$TRN_STARTDATE = "2005-05-16";
				$TRN_ENDDATE = "2005-05-20";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="16-20 �.�. 39") {
				$TRN_STARTDATE = "1996-12-16";
				$TRN_ENDDATE = "1996-12-20";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="16-20 ����Ҿѹ�� 2552") {
				$TRN_STARTDATE = "2009-02-16";
				$TRN_ENDDATE = "2009-02-20";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="16-20 �á�Ҥ� 2550") {
				$TRN_STARTDATE = "2007-07-16";
				$TRN_ENDDATE = "2007-07-20";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="16-20 �.�.47") {
				$TRN_STARTDATE = "2004-02-16";
				$TRN_ENDDATE = "2004-02-20";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="16-20 �.�.41") {
				$TRN_STARTDATE = "1998-02-16";
				$TRN_ENDDATE = "1998-02-20";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="16-20 �.�. 44") {
				$TRN_STARTDATE = "2001-07-16";
				$TRN_ENDDATE = "2001-07-20";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="16-20  �չҤ� 2547") {
				$TRN_STARTDATE = "2004-03-16";
				$TRN_ENDDATE = "2004-03-20";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="16-19 �ԧ�Ҥ�  2547") {
				$TRN_STARTDATE = "2004-08-16";
				$TRN_ENDDATE = "2004-08-19";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="16-19 �.�. 47") {
				$TRN_STARTDATE = "2004-08-16";
				$TRN_ENDDATE = "2004-08-19";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="16-19 ��.�.2547") {
				$TRN_STARTDATE = "2004-03-16";
				$TRN_ENDDATE = "2004-03-19";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="16-19 ��.�. 52") {
				$TRN_STARTDATE = "2009-06-16";
				$TRN_ENDDATE = "2009-06-19";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="16-19 ����Ҥ� 2548") {
				$TRN_STARTDATE = "2005-05-16";
				$TRN_ENDDATE = "2005-05-19";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="16-19 �.�.2548") {
				$TRN_STARTDATE = "2005-05-16";
				$TRN_ENDDATE = "2005-05-19";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="16-19 �.�. 42") {
				$TRN_STARTDATE = "1999-02-16";
				$TRN_ENDDATE = "1999-02-19";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="16-18 �.�. 39 *") {
				$TRN_STARTDATE = "1996-08-16";
				$TRN_ENDDATE = "1996-08-18";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="16-18 ��.�. 40") {
				$TRN_STARTDATE = "1997-06-16";
				$TRN_ENDDATE = "1997-06-18";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="16-18 �.�. 44") {
				$TRN_STARTDATE = "2001-11-16";
				$TRN_ENDDATE = "2001-11-18";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="16-18 �.�.48") {
				$TRN_STARTDATE = "2005-02-16";
				$TRN_ENDDATE = "2005-02-18";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="16-18 �.�. 40") {
				$TRN_STARTDATE = "1997-07-16";
				$TRN_ENDDATE = "1997-07-18";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="16-17 �ԧ�Ҥ� 2552") {
				$TRN_STARTDATE = "2009-08-16";
				$TRN_ENDDATE = "2009-08-17";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="16-17 ��� 20-25   �.�.42") {
				$TRN_STARTDATE = "1999-12-16";
				$TRN_ENDDATE = "1999-12-25";
				$TRN_DAY = 8;
			} elseif ($TRN_REMARK=="16-17 �չҤ� 2552") {
				$TRN_STARTDATE = "2009-03-16";
				$TRN_ENDDATE = "2009-03-17";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="16-17 ��.�.43") {
				$TRN_STARTDATE = "2000-03-16";
				$TRN_ENDDATE = "2000-03-17";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="16-17 ��.�.42") {
				$TRN_STARTDATE = "1999-03-16";
				$TRN_ENDDATE = "1999-03-17";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="16-17 ��.�. 52") {
				$TRN_STARTDATE = "2009-03-16";
				$TRN_ENDDATE = "2009-03-17";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="16-17 �.�.2549") {
				$TRN_STARTDATE = "2006-11-16";
				$TRN_ENDDATE = "2006-11-17";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="16-17 �ѹ�Ҥ� 2551") {
				$TRN_STARTDATE = "2008-12-16";
				$TRN_ENDDATE = "2008-12-17";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="16-17 �.�. 40") {
				$TRN_STARTDATE = "1997-10-16";
				$TRN_ENDDATE = "1997-10-17";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="16 ��.�.48") {
				$TRN_STARTDATE = "2005-03-16";
				$TRN_ENDDATE = "2005-03-16";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="16 ��.�.40") {
				$TRN_STARTDATE = "1997-03-16";
				$TRN_ENDDATE = "1997-03-16";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="16 ��.�.-2 �.�. 40") {
				$TRN_STARTDATE = "1997-06-16";
				$TRN_ENDDATE = "1997-07-02";
				$TRN_DAY = 17;
			} elseif ($TRN_REMARK=="16 �.�.-6 ��.�.44") {
				$TRN_STARTDATE = "2001-01-16";
				$TRN_ENDDATE = "2001-03-06";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="16 �.�.-21 ��.�. 38") {
				$TRN_STARTDATE = "1995-01-16";
				$TRN_ENDDATE = "1995-04-21";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="16 �.�.-11 ��.�.38") {
				$TRN_STARTDATE = "1995-01-16";
				$TRN_ENDDATE = "1995-04-11";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="16 �.�. 47") {
				$TRN_STARTDATE = "2004-01-16";
				$TRN_ENDDATE = "2004-01-16";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="16 �.�.-5 �.�.37") {
				$TRN_STARTDATE = "1994-05-16";
				$TRN_ENDDATE = "1994-08-05";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="16 �.�.-3 ��.�.41") {
				$TRN_STARTDATE = "1998-02-16";
				$TRN_ENDDATE = "1998-03-03";
				$TRN_DAY = 16;
			} elseif ($TRN_REMARK=="16 �.�.-12 ��.�.43") {
				$TRN_STARTDATE = "2000-02-16";
				$TRN_ENDDATE = "2000-04-12";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="16 �.�.-8 �.�.36") {
				$TRN_STARTDATE = "1993-07-16";
				$TRN_ENDDATE = "1993-10-08";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="16 �.�.-11 �.�.41") {
				$TRN_STARTDATE = "1998-07-16";
				$TRN_ENDDATE = "1998-09-11";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="16 - 18 �.�.49") {
				$TRN_STARTDATE = "2006-08-16";
				$TRN_ENDDATE = "2006-08-18";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="15-26 �ԧ�Ҥ� 2548") {
				$TRN_STARTDATE = "2005-08-15";
				$TRN_ENDDATE = "2005-08-26";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="15-26 �.�.2548") {
				$TRN_STARTDATE = "2005-08-15";
				$TRN_ENDDATE = "2005-08-26";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="15-26 �.�.2550") {
				$TRN_STARTDATE = "2007-01-15";
				$TRN_ENDDATE = "2007-01-26";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="15-26 �.�.42") {
				$TRN_STARTDATE = "1999-11-15";
				$TRN_ENDDATE = "1999-11-26";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="15-26 �.�.52") {
				$TRN_STARTDATE = "2009-12-15";
				$TRN_ENDDATE = "2009-12-26";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="15-26 �.�.42") {
				$TRN_STARTDATE = "1999-02-15";
				$TRN_ENDDATE = "1999-02-26";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="15-19 ��.�. 47") {
				$TRN_STARTDATE = "2004-03-15";
				$TRN_ENDDATE = "2004-03-19";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="15-19 ��.�. 52") {
				$TRN_STARTDATE = "2009-06-15";
				$TRN_ENDDATE = "2009-06-19";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="15-19 �ѹ��¹ 2551") {
				$TRN_STARTDATE = "2008-09-15";
				$TRN_ENDDATE = "2008-09-19";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="15-19  �չҤ� 2547") {
				$TRN_STARTDATE = "2004-03-15";
				$TRN_ENDDATE = "2004-03-19";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="15-18,27  �չҤ� 2547") {
				$TRN_STARTDATE = "2004-03-15";
				$TRN_ENDDATE = "2004-03-27";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="15-18 �Զع�¹  2547") {
				$TRN_STARTDATE = "2004-06-15";
				$TRN_ENDDATE = "2004-06-18";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="15-18 ��.�. 47") {
				$TRN_STARTDATE = "2004-06-15";
				$TRN_ENDDATE = "2004-06-18";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="15-18 ��.�. 42") {
				$TRN_STARTDATE = "1999-06-15";
				$TRN_ENDDATE = "1999-06-18";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="15-18 ��.�. 41") {
				$TRN_STARTDATE = "1998-06-15";
				$TRN_ENDDATE = "1998-06-18";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="15-18 �.�. 48") {
				$TRN_STARTDATE = "2005-02-15";
				$TRN_ENDDATE = "2005-02-18";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="15-17 �չҤ� 2547") {
				$TRN_STARTDATE = "2004-03-15";
				$TRN_ENDDATE = "2004-03-17";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="15-17 ��.�. 47") {
				$TRN_STARTDATE = "2004-03-15";
				$TRN_ENDDATE = "2004-03-17";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="15-17 ��.�.52") {
				$TRN_STARTDATE = "2009-06-15";
				$TRN_ENDDATE = "2009-06-17";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="15-17 ��.�.41") {
				$TRN_STARTDATE = "1998-06-15";
				$TRN_ENDDATE = "1998-06-17";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="15-17 ���Ҥ� 2551") {
				$TRN_STARTDATE = "2008-01-15";
				$TRN_ENDDATE = "2008-01-17";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="15-17 �.�. 52") {
				$TRN_STARTDATE = "2009-05-15";
				$TRN_ENDDATE = "2009-05-17";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="15-16 �.�. 40") {
				$TRN_STARTDATE = "1997-05-15";
				$TRN_ENDDATE = "1997-05-16";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="15-16 �.�. 51") {
				$TRN_STARTDATE = "2008-12-15";
				$TRN_ENDDATE = "2008-12-16";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="15-16 �ѹ��¹ 2552") {
				$TRN_STARTDATE = "2009-09-15";
				$TRN_ENDDATE = "2009-09-16";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="15,22,29 ��.�. 40") {
				$TRN_STARTDATE = "1997-03-15";
				$TRN_ENDDATE = "1997-03-29";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="15,22 �.�. ��� 1 ��.�. 40") {
				$TRN_STARTDATE = "1997-02-15";
				$TRN_ENDDATE = "1997-03-01";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="15,16,22 �.�. 40") {
				$TRN_STARTDATE = "1997-02-15";
				$TRN_ENDDATE = "1997-02-22";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="15 �ԧ�Ҥ� 2550") {
				$TRN_STARTDATE = "2007-08-15";
				$TRN_ENDDATE = "2007-08-15";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="15 ��.�.-15 ��.�. 40") {
				$TRN_STARTDATE = "1997-03-15";
				$TRN_ENDDATE = "1997-06-15";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="1-5 ��.�. 47") {
				$TRN_STARTDATE = "2004-03-01";
				$TRN_ENDDATE = "2004-03-05";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="1-5 ��.�. 52") {
				$TRN_STARTDATE = "2009-06-01";
				$TRN_ENDDATE = "2009-06-05";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="15 �.�.-5 ��.�.45") {
				$TRN_STARTDATE = "2002-01-15";
				$TRN_ENDDATE = "2002-03-05";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="15 �.�.-17 ��.�.33") {
				$TRN_STARTDATE = "1990-01-15";
				$TRN_ENDDATE = "1990-04-17";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="15 ����Ҥ� 2549") {
				$TRN_STARTDATE = "2006-05-15";
				$TRN_ENDDATE = "2006-05-15";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="15 �.�.-15 �.�.34") {
				$TRN_STARTDATE = "1991-05-15";
				$TRN_ENDDATE = "1991-08-15";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="15 �.�.-11 �.�.38") {
				$TRN_STARTDATE = "1995-05-15";
				$TRN_ENDDATE = "1995-08-11";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="15 �.�.47") {
				$TRN_STARTDATE = "2004-12-15";
				$TRN_ENDDATE = "2004-12-15";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="15 �.�.40") {
				$TRN_STARTDATE = "1997-12-15";
				$TRN_ENDDATE = "1997-12-15";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="15 �.�.2547") {
				$TRN_STARTDATE = "2004-12-15";
				$TRN_ENDDATE = "2004-12-15";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="15 �.�.2547") {
				$TRN_STARTDATE = "2004-10-15";
				$TRN_ENDDATE = "2004-10-15";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="1-5 �ѹ��¹ 2551") {
				$TRN_STARTDATE = "2008-09-01";
				$TRN_ENDDATE = "2008-09-05";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="15 �.�.-9 ��.�.42") {
				$TRN_STARTDATE = "1999-02-15";
				$TRN_ENDDATE = "1999-04-09";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="15 �.�.-30 �.�. 39") {
				$TRN_STARTDATE = "1996-02-15";
				$TRN_ENDDATE = "1996-05-30";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="15 �.�.-26 �.�.41 (੾���ѹ�ѹ�������ѹ�ظ)") {
				$TRN_STARTDATE = "1998-07-15";
				$TRN_ENDDATE = "1998-08-26";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="1-5 �.�. 45") {
				$TRN_STARTDATE = "2002-07-01";
				$TRN_ENDDATE = "2002-07-05";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="1-5  �չҤ� 2547") {
				$TRN_STARTDATE = "2004-03-01";
				$TRN_ENDDATE = "2004-03-05";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="14-25 ��.�. 40") {
				$TRN_STARTDATE = "1997-06-14";
				$TRN_ENDDATE = "1997-06-25";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK=="14-18 �չҤ� 2548") {
				$TRN_STARTDATE = "2005-03-14";
				$TRN_ENDDATE = "2005-03-18";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="14-18 ��.�.42") {
				$TRN_STARTDATE = "1999-06-14";
				$TRN_ENDDATE = "1999-06-18";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="14-18 �.�. 39") {
				$TRN_STARTDATE = "1996-10-14";
				$TRN_ENDDATE = "1996-10-18";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="14-18 �.�.43") {
				$TRN_STARTDATE = "2000-02-14";
				$TRN_ENDDATE = "2000-02-18";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="14-17 �.�. 42") {
				$TRN_STARTDATE = "1999-09-14";
				$TRN_ENDDATE = "1999-09-17";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="14-16 �Զع�¹  2547") {
				$TRN_STARTDATE = "2004-06-14";
				$TRN_ENDDATE = "2004-06-16";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="14-16 ��.�. 47") {
				$TRN_STARTDATE = "2004-06-14";
				$TRN_ENDDATE = "2004-06-16";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="14-16 ��Ȩԡ�¹ 2548") {
				$TRN_STARTDATE = "2005-11-14";
				$TRN_ENDDATE = "2005-11-16";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="14-16 �.�. 40") {
				$TRN_STARTDATE = "1997-05-14";
				$TRN_ENDDATE = "1997-05-16";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="14-16 �ѹ�Ҥ� 2548") {
				$TRN_STARTDATE = "2005-12-14";
				$TRN_ENDDATE = "2005-12-16";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="14-16 �.�. 47") {
				$TRN_STARTDATE = "2004-12-14";
				$TRN_ENDDATE = "2004-12-16";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="14-16 �.�. 39") {
				$TRN_STARTDATE = "1996-10-14";
				$TRN_ENDDATE = "1996-10-16";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="14-16 �ѹ��¹ 2548") {
				$TRN_STARTDATE = "2005-09-14";
				$TRN_ENDDATE = "2005-09-16";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="14-16 �.�. 40") {
				$TRN_STARTDATE = "1997-07-14";
				$TRN_ENDDATE = "1997-07-16";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="14-15 �.�.42") {
				$TRN_STARTDATE = "1999-01-14";
				$TRN_ENDDATE = "1999-01-15";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="14-15 �.�. 40") {
				$TRN_STARTDATE = "1997-07-14";
				$TRN_ENDDATE = "1997-07-15";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="1-4,8 �չҤ� 2547") {
				$TRN_STARTDATE = "2004-03-01";
				$TRN_ENDDATE = "2004-03-08";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="1-4, 8 ��.�. 47") {
				$TRN_STARTDATE = "2004-03-01";
				$TRN_ENDDATE = "2004-03-08";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="1-4 �.�. 2543") {
				$TRN_STARTDATE = "2000-08-01";
				$TRN_ENDDATE = "2000-08-04";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="14 �չҤ�-16 �Զع�¹ 2549") {
				$TRN_STARTDATE = "2006-03-14";
				$TRN_ENDDATE = "2006-06-16";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="14 ��.�.-11 �.�.43") {
				$TRN_STARTDATE = "2000-06-14";
				$TRN_ENDDATE = "2000-08-11";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="1-4 �.�.43") {
				$TRN_STARTDATE = "2000-05-01";
				$TRN_ENDDATE = "2000-05-04";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="1-4 �.�. 43") {
				$TRN_STARTDATE = "2000-05-01";
				$TRN_ENDDATE = "2000-05-04";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="14 �.�. - 5 �.�. 45") {
				$TRN_STARTDATE = "2002-05-14";
				$TRN_ENDDATE = "2002-07-05";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="1-4 �.�.43") {
				$TRN_STARTDATE = "2000-02-01";
				$TRN_ENDDATE = "2000-02-04";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="14 �.�.-10 �.�.39") {
				$TRN_STARTDATE = "1996-02-14";
				$TRN_ENDDATE = "1996-05-10";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="1-4  ��.�.47") {
				$TRN_STARTDATE = "2004-03-01";
				$TRN_ENDDATE = "2004-03-04";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="1-4  ��.�.2547") {
				$TRN_STARTDATE = "2004-03-01";
				$TRN_ENDDATE = "2004-03-04";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="13-30 �.�. 39") {
				$TRN_STARTDATE = "1996-05-13";
				$TRN_ENDDATE = "1996-05-30";
				$TRN_DAY = 18;
			} elseif ($TRN_REMARK=="13-29 ��.�.43") {
				$TRN_STARTDATE = "2000-06-13";
				$TRN_ENDDATE = "2000-06-29";
				$TRN_DAY = 17;
			} elseif ($TRN_REMARK=="13-28 �.�. 40") {
				$TRN_STARTDATE = "1997-01-13";
				$TRN_ENDDATE = "1997-01-28";
				$TRN_DAY = 16;
			} elseif ($TRN_REMARK=="13-24 ����Ҿѹ�� 2549") {
				$TRN_STARTDATE = "2006-02-13";
				$TRN_ENDDATE = "2006-02-24";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="13-22 �ԧ�Ҥ� 2551") {
				$TRN_STARTDATE = "2008-08-13";
				$TRN_ENDDATE = "2008-08-22";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK=="13-22 �.�. 41") {
				$TRN_STARTDATE = "1998-07-13";
				$TRN_ENDDATE = "1998-07-22";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK=="13-19 �.�. 40") {
				$TRN_STARTDATE = "1997-07-13";
				$TRN_ENDDATE = "1997-07-19";
				$TRN_DAY = 7;
			} elseif ($TRN_REMARK=="13-18 �.�. 43") {
				$TRN_STARTDATE = "2000-02-13";
				$TRN_ENDDATE = "2000-02-18";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="13-17 ��.�.43") {
				$TRN_STARTDATE = "2000-03-13";
				$TRN_ENDDATE = "2000-03-17";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="13-17 �Զع�¹ 2548") {
				$TRN_STARTDATE = "2005-06-13";
				$TRN_ENDDATE = "2005-06-17";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="13-17 �.�. 51") {
				$TRN_STARTDATE = "2008-12-13";
				$TRN_ENDDATE = "2008-12-17";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="13-17 �á�Ҥ� 2552") {
				$TRN_STARTDATE = "2009-07-13";
				$TRN_ENDDATE = "2009-07-17";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="13-17 �.�. 47") {
				$TRN_STARTDATE = "2004-09-13";
				$TRN_ENDDATE = "2004-09-17";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="13-16 �.�. 40") {
				$TRN_STARTDATE = "1997-05-13";
				$TRN_ENDDATE = "1997-05-16";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="13-16 �á�Ҥ� 2547") {
				$TRN_STARTDATE = "2004-07-13";
				$TRN_ENDDATE = "2004-07-16";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="13-16 �.�. 47") {
				$TRN_STARTDATE = "2004-07-13";
				$TRN_ENDDATE = "2004-07-16";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="13-15 �ԧ�Ҥ� 2551") {
				$TRN_STARTDATE = "2008-08-13";
				$TRN_ENDDATE = "2008-08-15";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="13-15 �.�.41") {
				$TRN_STARTDATE = "1998-08-13";
				$TRN_ENDDATE = "1998-08-15";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="13-15 �.�. 40") {
				$TRN_STARTDATE = "1997-08-13";
				$TRN_ENDDATE = "1997-08-15";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="13-15 �Զع�¹ 2550") {
				$TRN_STARTDATE = "2007-06-13";
				$TRN_ENDDATE = "2007-06-15";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="13-15 �.�.42") {
				$TRN_STARTDATE = "1999-01-13";
				$TRN_ENDDATE = "1999-01-15";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="13-15 ����Ҥ� 2548") {
				$TRN_STARTDATE = "2005-05-13";
				$TRN_ENDDATE = "2005-05-15";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="13-15 �.�.39") {
				$TRN_STARTDATE = "1996-05-13";
				$TRN_ENDDATE = "1996-05-15";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="13-15 �.�. 52") {
				$TRN_STARTDATE = "2009-05-13";
				$TRN_ENDDATE = "2009-05-15";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="13-15 �.�. 40") {
				$TRN_STARTDATE = "1997-05-13";
				$TRN_ENDDATE = "1997-05-15";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="13-14 �.�. 40") {
				$TRN_STARTDATE = "1997-10-13";
				$TRN_ENDDATE = "1887-10-14";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="13-14 �ѹ��¹ 2552") {
				$TRN_STARTDATE = "2009-09-13";
				$TRN_ENDDATE = "2009-09-14";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="13-14 �.�. 52") {
				$TRN_STARTDATE = "2009-07-13";
				$TRN_ENDDATE = "2009-07-14";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="13 �.�. 2547") {
				$TRN_STARTDATE = "2004-08-13";
				$TRN_ENDDATE = "2004-08-13";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="13 ��.�.-4 �.�.44") {
				$TRN_STARTDATE = "2001-03-13";
				$TRN_ENDDATE = "2001-05-04";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="13 ��.�.2550") {
				$TRN_STARTDATE = "2007-03-13";
				$TRN_ENDDATE = "2007-03-13";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="13 ��.�.-17 �.�. 45") {
				$TRN_STARTDATE = "2002-03-13";
				$TRN_ENDDATE = "2002-05-17";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="1-3 �Զع�¹ 2548") {
				$TRN_STARTDATE = "2005-06-01";
				$TRN_ENDDATE = "2005-06-03";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="13 ��.�.-15 �.�. 40") {
				$TRN_STARTDATE = "1997-06-13";
				$TRN_ENDDATE = "1997-08-15";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="1-3 �.�. 47") {
				$TRN_STARTDATE = "2004-11-01";
				$TRN_ENDDATE = "2004-11-03";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="13 �.�.41") {
				$TRN_STARTDATE = "1998-05-13";
				$TRN_ENDDATE = "1998-05-13";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="13 �ѹ��¹ 2548") {
				$TRN_STARTDATE = "2005-09-13";
				$TRN_ENDDATE = "2005-09-13";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="13 �.�.43") {
				$TRN_STARTDATE = "2000-09-13";
				$TRN_ENDDATE = "2000-09-13";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="12-23 �.�. 41") {
				$TRN_STARTDATE = "1998-01-12";
				$TRN_ENDDATE = "1998-01-23";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="12-23 �ѹ��¹ 2548") {
				$TRN_STARTDATE = "2005-09-12";
				$TRN_ENDDATE = "2005-09-23";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="12-17 �.�. 42") {
				$TRN_STARTDATE = "1999-09-12";
				$TRN_ENDDATE = "1999-09-17";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="12-17 �.�.42") {
				$TRN_STARTDATE = "1999-07-12";
				$TRN_ENDDATE = "1999-07-17";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="12-16 �.�.47") {
				$TRN_STARTDATE = "2004-01-12";
				$TRN_ENDDATE = "2004-01-16";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="12-16 �.�. 52") {
				$TRN_STARTDATE = "2009-01-12";
				$TRN_ENDDATE = "2009-01-16";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="12-16 �.�. 40") {
				$TRN_STARTDATE = "1997-05-12";
				$TRN_ENDDATE = "1997-05-16";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="12-16 �.�. 44") {
				$TRN_STARTDATE = "2001-02-12";
				$TRN_ENDDATE = "2001-02-16";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="12-16 �.�.47") {
				$TRN_STARTDATE = "2004-07-12";
				$TRN_ENDDATE = "2004-07-16";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="12-15 ��.�.46") {
				$TRN_STARTDATE = "2003-06-12";
				$TRN_ENDDATE = "2003-06-15";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="12-15 �.�.2549") {
				$TRN_STARTDATE = "2006-12-12";
				$TRN_ENDDATE = "2006-12-15";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="12-15 �á�Ҥ� 2547") {
				$TRN_STARTDATE = "2004-07-12";
				$TRN_ENDDATE = "2004-07-15";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="12-15 �.�. 47") {
				$TRN_STARTDATE = "2004-07-12";
				$TRN_ENDDATE = "2004-07-15";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="12-14 ��.�.39") {
				$TRN_STARTDATE = "1996-03-12";
				$TRN_ENDDATE = "1996-03-14";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="12-14 �.�.52") {
				$TRN_STARTDATE = "2009-05-12";
				$TRN_ENDDATE = "2009-05-14";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="12-14 �.�. 46") {
				$TRN_STARTDATE = "2003-12-12";
				$TRN_ENDDATE = "2003-12-14";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="12-14 ���Ҥ� 2550") {
				$TRN_STARTDATE = "2007-10-12";
				$TRN_ENDDATE = "2007-10-14";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="12-14 ����Ҿѹ�� 2551") {
				$TRN_STARTDATE = "2008-02-12";
				$TRN_ENDDATE = "2008-02-14";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="12-14 �ѹ��¹ 2548") {
				$TRN_STARTDATE = "2005-09-12";
				$TRN_ENDDATE = "2005-09-14";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="12-13 ��.�. 52") {
				$TRN_STARTDATE = "2009-03-12";
				$TRN_ENDDATE = "2009-03-13";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="12-13 �.�. 40") {
				$TRN_STARTDATE = "1997-07-12";
				$TRN_ENDDATE = "1997-07-13";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="1-20 �.�. 40") {
				$TRN_STARTDATE = "1997-02-01";
				$TRN_ENDDATE = "1997-02-20";
				$TRN_DAY = 20;
			} elseif ($TRN_REMARK=="12,19,26 �.�. 39") {
				$TRN_STARTDATE = "1996-10-12";
				$TRN_ENDDATE = "1996-10-26";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="12,19,26  �.�.,  2,9,16,23 �.�. 39") {
				$TRN_STARTDATE = "1996-10-12";
				$TRN_ENDDATE = "1996-11-23";
				$TRN_DAY = 7;
			} elseif ($TRN_REMARK=="12,15-16 ��.�. 47") {
				$TRN_STARTDATE = "2004-03-12";
				$TRN_ENDDATE = "2004-03-16";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="1-2 �ԧ�Ҥ� 2552") {
				$TRN_STARTDATE = "2009-08-01";
				$TRN_ENDDATE = "2009-08-02";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="1-2 �ԧ�Ҥ� 2548") {
				$TRN_STARTDATE = "2005-08-01";
				$TRN_ENDDATE = "2005-08-02";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="12 ��.�.-16 �.�. 40") {
				$TRN_STARTDATE = "1997-03-12";
				$TRN_ENDDATE = "1997-05-16";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="12 ��.�.,30 ��.�.-2 ��.�.,21 ��.�.-30 �.�.,31 �.�.-3 �.�. 47") {
				$TRN_STARTDATE = "2004-03-12";
				$TRN_ENDDATE = "2004-09-03";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="12 ��.�.,23-26 ��.�.,18-30 �.�.,24-27 �.�. 47") {
				$TRN_STARTDATE = "2004-03-12";
				$TRN_ENDDATE = "2004-08-27";
				$TRN_DAY = 22;
			} elseif ($TRN_REMARK=="12 ��.�.,16-19 ��.�.,18-30 �.�.,17-20 �.�. 47") {
				$TRN_STARTDATE = "2004-03-12";
				$TRN_ENDDATE = "2004-08-20";
				$TRN_DAY = 22;
			} elseif ($TRN_REMARK=="12 ��.�.-7 �.�.43") {
				$TRN_STARTDATE = "2000-06-12";
				$TRN_ENDDATE = "2000-07-07";
				$TRN_DAY = 26;
			} elseif ($TRN_REMARK=="12 ��.�.-5 �.�.33") {
				$TRN_STARTDATE = "1990-06-12";
				$TRN_ENDDATE = "1990-09-05";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="12 ��.�.-16 �.�.39") {
				$TRN_STARTDATE = "1996-06-12";
				$TRN_ENDDATE = "1996-09-16";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="12 ��.�.-12 �.�. 40") {
				$TRN_STARTDATE = "1997-06-12";
				$TRN_ENDDATE = "1997-10-12";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="12 ��.�. - 12 �.�.32") {
				$TRN_STARTDATE = "1989-06-12";
				$TRN_ENDDATE = "1989-08-12";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="1-2 �.�.42") {
				$TRN_STARTDATE = "1999-11-01";
				$TRN_ENDDATE = "1999-11-02";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="1-2 �.�. 40") {
				$TRN_STARTDATE = "1997-05-01";
				$TRN_ENDDATE = "1997-05-02";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="12 �ѹ��¹ 2552") {
				$TRN_STARTDATE = "2009-09-12";
				$TRN_ENDDATE = "2009-09-12";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="1-2 �.�. 40") {
				$TRN_STARTDATE = "1997-02-01";
				$TRN_ENDDATE = "1997-02-02";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="1-2 �.�.2547") {
				$TRN_STARTDATE = "2004-07-01";
				$TRN_ENDDATE = "2004-07-02";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="12 - 16 �.�. 2547") {
				$TRN_STARTDATE = "2004-01-12";
				$TRN_ENDDATE = "2004-01-16";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="1-17 �.�. 39") {
				$TRN_STARTDATE = "1996-07-01";
				$TRN_ENDDATE = "1996-07-17";
				$TRN_DAY = 17;
			} elseif ($TRN_REMARK=="1-15 �.�. 40") {
				$TRN_STARTDATE = "1997-05-01";
				$TRN_ENDDATE = "1997-05-15";
				$TRN_DAY = 15;
			} elseif ($TRN_REMARK=="11-22 ��.�.2545") {
				$TRN_STARTDATE = "2002-03-11";
				$TRN_ENDDATE = "2002-03-22";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="11-20 �.�.43") {
				$TRN_STARTDATE = "2000-07-11";
				$TRN_ENDDATE = "2000-07-20";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK=="1-12 ��.�. 2552") {
				$TRN_STARTDATE = "2009-06-01";
				$TRN_ENDDATE = "2009-06-12";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="1-12 �.�.42") {
				$TRN_STARTDATE = "1999-11-01";
				$TRN_ENDDATE = "1999-11-12";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="11-19 �.�.42") {
				$TRN_STARTDATE = "1999-10-11";
				$TRN_ENDDATE = "1999-10-19";
				$TRN_DAY = 9;
			} elseif ($TRN_REMARK=="11-16 ��.�. 39") {
				$TRN_STARTDATE = "1996-03-11";
				$TRN_ENDDATE = "1996-03-16";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="11-15 �.�.38") {
				$TRN_STARTDATE = "1995-08-11";
				$TRN_ENDDATE = "1995-08-15";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="11-15 �.�.2545") {
				$TRN_STARTDATE = "2002-11-11";
				$TRN_ENDDATE = "2002-11-15";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="11-15 �.�. 39") {
				$TRN_STARTDATE = "1996-11-11";
				$TRN_ENDDATE = "1996-11-15";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="11-15 �.�.2548") {
				$TRN_STARTDATE = "2005-07-11";
				$TRN_ENDDATE = "2005-07-15";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="11-14 �.�.46") {
				$TRN_STARTDATE = "2003-09-11";
				$TRN_ENDDATE = "2003-09-14";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="11-13,18-20 �.�. 39") {
				$TRN_STARTDATE = "1996-10-11";
				$TRN_ENDDATE = "1996-10-20";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="11-13 �.�. 52") {
				$TRN_STARTDATE = "2009-02-11";
				$TRN_ENDDATE = "2009-02-13";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="11-12, 15-16 �.�. 51") {
				$TRN_STARTDATE = "2008-09-11";
				$TRN_ENDDATE = "2008-09-16";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="11-12 ��.�. 52") {
				$TRN_STARTDATE = "2009-06-11";
				$TRN_ENDDATE = "2009-06-12";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="11-12 �.�. 52") {
				$TRN_STARTDATE = "2009-02-11";
				$TRN_ENDDATE = "2009-02-12";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="1-11 �ԧ�Ҥ� 2550") {
				$TRN_STARTDATE = "2007-08-01";
				$TRN_ENDDATE = "2007-08-11";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="10-27 �.�. 52") {
				$TRN_STARTDATE = "2009-02-10";
				$TRN_ENDDATE = "2009-02-27";
				$TRN_DAY = 18;
			} elseif ($TRN_REMARK=="10-21 �.�.47") {
				$TRN_STARTDATE = "2004-05-10";
				$TRN_ENDDATE = "2004-05-21";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="10-21 �.�.2547") {
				$TRN_STARTDATE = "2004-05-10";
				$TRN_ENDDATE = "2004-05-21";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="10-21 �.�. 47") {
				$TRN_STARTDATE = "2004-05-10";
				$TRN_ENDDATE = "2004-05-21";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="10-20 �.�.2547") {
				$TRN_STARTDATE = "2004-05-10";
				$TRN_ENDDATE = "2004-05-20";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="10-20 �.�. 47") {
				$TRN_STARTDATE = "2004-05-10";
				$TRN_ENDDATE = "2004-05-20";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="10-16 �.�.41") {
				$TRN_STARTDATE = "1998-08-10";
				$TRN_ENDDATE = "1998-08-16";
				$TRN_DAY = 7;
			} elseif ($TRN_REMARK=="10-15 �.�. 42") {
				$TRN_STARTDATE = "1999-01-10";
				$TRN_ENDDATE = "1999-01-15";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="10-14 ��.�. 40") {
				$TRN_STARTDATE = "1997-03-10";
				$TRN_ENDDATE = "1997-03-14";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="10-14 ����Ҥ�  2547") {
				$TRN_STARTDATE = "2004-05-10";
				$TRN_ENDDATE = "2004-05-14";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="10-14 �.�. 47") {
				$TRN_STARTDATE = "2004-05-10";
				$TRN_ENDDATE = "2004-05-14";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="10-13 �.�. 47") {
				$TRN_STARTDATE = "2004-11-10";
				$TRN_ENDDATE = "2004-11-13";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="10-12 �չҤ� 2547") {
				$TRN_STARTDATE = "2004-03-10";
				$TRN_ENDDATE = "2004-03-12";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="10-12 ��.�. 47") {
				$TRN_STARTDATE = "2004-03-10";
				$TRN_ENDDATE = "2004-03-12";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="10-12 ��.�. 52") {
				$TRN_STARTDATE = "2009-06-10";
				$TRN_ENDDATE = "2009-06-12";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="10-12 �.�. 40") {
				$TRN_STARTDATE = "1997-11-10";
				$TRN_ENDDATE = "1997-11-12";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="10-12 �.�. 51") {
				$TRN_STARTDATE = "2008-09-10";
				$TRN_ENDDATE = "2008-09-12";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="10-12 �.�. 40") {
				$TRN_STARTDATE = "1997-02-10";
				$TRN_ENDDATE = "1997-02-12";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="10-11 �.�.43") {
				$TRN_STARTDATE = "2000-08-10";
				$TRN_ENDDATE = "2000-08-11";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="10-11 ��� 15 ��.�.42") {
				$TRN_STARTDATE = "1999-03-10";
				$TRN_ENDDATE = "1999-03-15";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="10-11 ��.�.2548") {
				$TRN_STARTDATE = "2005-03-10";
				$TRN_ENDDATE = "2005-03-11";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="10-11 ��Ȩԡ�¹ 2550") {
				$TRN_STARTDATE = "2007-11-10";
				$TRN_ENDDATE = "2007-11-11";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="10-11 �.�.41") {
				$TRN_STARTDATE = "1998-09-10";
				$TRN_ENDDATE = "1998-09-11";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="10 ��.�.-4 ��.�. 40") {
				$TRN_STARTDATE = "1997-03-10";
				$TRN_ENDDATE = "1997-04-04";
				$TRN_DAY = 25;
			} elseif ($TRN_REMARK=="10 �.�.49") {
				$TRN_STARTDATE = "2006-11-10";
				$TRN_ENDDATE = "2006-11-10";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="10 �.�.38-13 �.�.39") {
				$TRN_STARTDATE = "1995-10-10";
				$TRN_ENDDATE = "1996-02-13";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="10 - 12 ��.�. 2549") {
				$TRN_STARTDATE = "2006-04-10";
				$TRN_ENDDATE = "2006-04-12";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="1 �.�.-10 �.�. 35") {
				$TRN_STARTDATE = "1992-08-01";
				$TRN_ENDDATE = "1992-11-10";
				$TRN_DAY = 102;
			} elseif ($TRN_REMARK=="1 ��.�.50") {
				$TRN_STARTDATE = "2007-04-01";
				$TRN_ENDDATE = "2007-04-01";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="1 ��.�.-30 �.�.48") {
				$TRN_STARTDATE = "2005-06-01";
				$TRN_ENDDATE = "2005-09-30";
				$TRN_DAY = 122;
			} elseif ($TRN_REMARK=="1 ��.�.-29 �.�.42") {
				$TRN_STARTDATE = "1999-06-01";
				$TRN_ENDDATE = "1999-07-29";
				$TRN_DAY = 59;
			} elseif ($TRN_REMARK=="1 ��.�. 47") {
				$TRN_STARTDATE = "2004-06-01";
				$TRN_ENDDATE = "2004-06-01";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="1 �.�. 42") {
				$TRN_STARTDATE = "1999-12-01";
				$TRN_ENDDATE = "1999-12-01";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="1 �.�.-28 �.�.35") {
				$TRN_STARTDATE = "1992-07-01";
				$TRN_ENDDATE = "1992-09-28";
				$TRN_DAY = 90;
			} elseif ($TRN_REMARK=="00-00-2543") {
				$TRN_STARTDATE = "2000-00-00";
				$TRN_ENDDATE = "2000-00-00";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK==" �� 2544 (5 �ѹ)") {
				$TRN_STARTDATE = "2001-00-00";
				$TRN_ENDDATE = "2001-00-00";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK==" �� 2543 (2 �ѹ)") {
				$TRN_STARTDATE = "2000-00-00";
				$TRN_ENDDATE = "2000-00-00";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK==" �� 2543 (10 �ѹ)") {
				$TRN_STARTDATE = "2000-00-00";
				$TRN_ENDDATE = "2000-00-00";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK==" �� 2540 (5 �ѹ)") {
				$TRN_STARTDATE = "1997-00-00";
				$TRN_ENDDATE = "1997-00-00";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK==" �� 2540 (3 �ѹ)") {
				$TRN_STARTDATE = "1997-00-00";
				$TRN_ENDDATE = "1997-00-00";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK==" �� 2539 (5 �ѹ)") {
				$TRN_STARTDATE = "1996-00-00";
				$TRN_ENDDATE = "1996-00-00";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK==" �� 2538 (3 �ѹ)") {
				$TRN_STARTDATE = "1995-00-00";
				$TRN_ENDDATE = "1995-00-00";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK==" �� 2538 (2 �ѹ)") {
				$TRN_STARTDATE = "1995-00-00";
				$TRN_ENDDATE = "1995-00-00";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK==" 6-8 �.�. 40") {
				$TRN_STARTDATE = "1997-05-06";
				$TRN_ENDDATE = "1997-05-08";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK==" 5 - 9 �.�. 44") {
				$TRN_STARTDATE = "2001-11-05";
				$TRN_ENDDATE = "2001-11-09";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK==" 4 - 13 ��.�. 44") {
				$TRN_STARTDATE = "2001-06-04";
				$TRN_ENDDATE = "2001-06-13";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK==" 4 - 10 ��.�. 44") {
				$TRN_STARTDATE = "2001-06-04";
				$TRN_ENDDATE = "2001-06-10";
				$TRN_DAY = 7;
			} elseif ($TRN_REMARK==" 26 - 27 �.�. 44") {
				$TRN_STARTDATE = "2001-09-26";
				$TRN_ENDDATE = "2001-09-27";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK==" 25 - 29 ��.�. 39") {
				$TRN_STARTDATE = "1996-06-25";
				$TRN_ENDDATE = "1996-06-29";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK==" 24 - 25 �.�. 44") {
				$TRN_STARTDATE = "2001-02-24";
				$TRN_ENDDATE = "2001-02-25";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK==" 23-26 ��.�. 52") {
				$TRN_STARTDATE = "2009-04-23";
				$TRN_ENDDATE = "2009-04-26";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK==" 23 - 28 ��.�. 42") {
				$TRN_STARTDATE = "1999-03-23";
				$TRN_ENDDATE = "1999-03-28";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK==" 23 - 27 �.�. 44") {
				$TRN_STARTDATE = "2001-07-23";
				$TRN_ENDDATE = "2001-07-27";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK==" 22 �.�. 44") {
				$TRN_STARTDATE = "2001-11-22";
				$TRN_ENDDATE = "2001-11-22";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK==" 22 - 31 ��.�. 38") {
				$TRN_STARTDATE = "1995-03-22";
				$TRN_ENDDATE = "1995-03-31";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK==" 22 - 26 �.�. 39") {
				$TRN_STARTDATE = "1996-07-22";
				$TRN_ENDDATE = "1996-07-26";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK==" 22 - 24 �.�. 41") {
				$TRN_STARTDATE = "1998-09-22";
				$TRN_ENDDATE = "1998-09-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK==" 21 - 30 �.�. 44") {
				$TRN_STARTDATE = "2001-05-21";
				$TRN_ENDDATE = "2001-05-30";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK==" 2 - 4 �.�. 41") {
				$TRN_STARTDATE = "1998-09-02";
				$TRN_ENDDATE = "1998-09-04";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK==" 17 - 22 �.�. 39") {
				$TRN_STARTDATE = "1996-11-17";
				$TRN_ENDDATE = "1996-11-22";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK==" 16 - 19 �.�. 44") {
				$TRN_STARTDATE = "2001-07-16";
				$TRN_ENDDATE = "2001-07-19";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK==" 15 - 19 �.�. 38") {
				$TRN_STARTDATE = "1995-05-15";
				$TRN_ENDDATE = "1995-05-19";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK==" 15 - 19 �.�. 39") {
				$TRN_STARTDATE = "1996-07-15";
				$TRN_ENDDATE = "1996-07-19";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK==" 13 - 22 ��.�. 38") {
				$TRN_STARTDATE = "1995-03-13";
				$TRN_ENDDATE = "1995-03-22";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK==" 11 ��.�.50 (���� 09.00-12.00 �.)") {
				$TRN_STARTDATE = "2007-04-11";
				$TRN_ENDDATE = "2007-04-11";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK==" 11 �.�. 51") {
				$TRN_STARTDATE = "2008-09-11";
				$TRN_ENDDATE = "2008-09-11";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK==" 10 - 14 �.�. 42") {
				$TRN_STARTDATE = "1999-08-10";
				$TRN_ENDDATE = "1999-08-14";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK==" 10 - 12 �.�. 39") {
				$TRN_STARTDATE = "1996-07-10";
				$TRN_ENDDATE = "1996-07-12";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK==" 1 �.�. - 16 �.�. 39") {
				$TRN_STARTDATE = "1996-07-01";
				$TRN_ENDDATE = "1996-07-16";
				$TRN_DAY = 16;
			} elseif ($TRN_REMARK==" 1 - 2 �.�. 40") {
				$TRN_STARTDATE = "1997-05-01";
				$TRN_ENDDATE = "1997-05-02";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK==" 1 - 15 ��.�. 39") {
				$TRN_STARTDATE = "1996-06-01";
				$TRN_ENDDATE = "1996-06-15";
				$TRN_DAY = 15;
			} elseif ($TRN_REMARK==" 1 - 11 �.�. 38") {
				$TRN_STARTDATE = "1995-09-01";
				$TRN_ENDDATE = "1995-09-11";
				$TRN_DAY = 11;
			} 
			if (!$TRN_DAY) $TRN_DAY = "NULL";

			$cmd = " select TR_CODE from PER_TRAIN where TR_NAME = '$COURSENAME' ";
			$count_data = $db_dpis->send_cmd($cmd);

			if (!$count_data) {			
				$cmd = " INSERT INTO PER_TRAIN (TR_CODE, TR_TYPE, TR_NAME, TR_ACTIVE, UPDATE_USER, UPDATE_DATE)
								VALUES ('$TR_CODE', 1, '$TR_NAME', 1, $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}

			$PER_ID = "";
			if ($PER_SURNAME=="�ԭ��������ǧ��") $PER_SURNAME = "�ԭ�������ǧ��";
			elseif ($PER_SURNAME=="ǧ��侺�����Ѳ��") $PER_SURNAME = "ǧ��侺�����Ѳ�";
			elseif ($PER_SURNAME=="����ٵ��ʧ�ѹ���") $PER_SURNAME = "����ٵ���ʧ�ѹ���";
			elseif ($PER_NAME=="��ѭ�" && $PER_SURNAME=="�����Թ") $PER_NAME = "��ѭ�����";
			elseif ($PER_NAME=="�ѹ����" && $PER_SURNAME=="�¸���") $PER_SURNAME = "�¸ԡ��";
			elseif ($PER_SURNAME=="�ѹ�د�ա��") $PER_SURNAME = "�ѹ�خ�ա��";
			elseif ($PER_NAME=="�Դ���" && $PER_SURNAME=="�Ѳ�͹ѹ��آ") { $PER_NAME = "����"; $PER_SURNAME = "������Ѳ��"; }
			elseif ($PER_SURNAME=="�ѵø�������Ǫ") $PER_SURNAME = "�ѵø������Ǫ";
			elseif ($PER_NAME=="�رҷԾ��" && $PER_SURNAME=="������ѹ��") $PER_NAME = "�رҷԾ";
			elseif ($PER_SURNAME=="�˭����ʴ��ǧ��") $PER_SURNAME = "�˭����ʴ��ǧ��";
			elseif ($PER_SURNAME=="�ĵ���ա��") $PER_SURNAME = "�ĵ����ա��";
			elseif ($PER_NAME=="��ȹ���" && $PER_SURNAME=="�è���ѹ�Ժح����") { $PER_NAME = "�ԭ���ѷ��"; $PER_SURNAME = "�è�ѹ�Ԫ�¡��"; }
			elseif ($PER_NAME=="�ѭ��ѹ��" && $PER_SURNAME=="��������") $PER_NAME = "�ѭ�ѹ��";
			elseif ($PER_NAME=="���о�" && $PER_SURNAME=="�Դ�Թ���") $PER_SURNAME = "������������ѵ��";
			elseif ($PER_NAME=="����" && $PER_SURNAME=="�ح�����") $PER_SURNAME = "��ǧ��";
			elseif ($PER_NAME=="�ت��ö" && $PER_SURNAME=="�ح���Ե") $PER_NAME = "�ت�Ҷ";
			elseif ($PER_NAME=="�ѳ�Ե�" && $PER_SURNAME=="������") { $PER_NAME = "�Ѫ�ҹѹ��"; $PER_SURNAME = "��֧"; }
			elseif ($PER_NAME=="�������" && $PER_SURNAME=="�ح����") $PER_NAME = "�����";
			elseif ($PER_NAME=="���¹Ү" && $PER_SURNAME=="��º�ѵ��") $PER_NAME = "���¹ү";
			elseif ($PER_NAME=="����ҹت" && $PER_SURNAME=="�ҷԹ") $PER_SURNAME = "�����ѹ��";
			elseif ($PER_NAME=="���Ԫҵ�" && $PER_SURNAME=="��ɮ�") { $PER_NAME = "ê��"; $PER_SURNAME = "���Ը����ѡ��"; }
			elseif ($PER_NAME=="��Դ�" && $PER_SURNAME=="�ҭ������") $PER_NAME = "�ѷ�Ҿ�ó";
			elseif ($PER_NAME=="�þ���" && $PER_SURNAME=="��ǧ���") $PER_SURNAME = "��ǧ���";
			elseif ($PER_SURNAME=="�ҹؾ�����ø��") $PER_SURNAME = "�ҹؾ�����ø��";
			elseif ($PER_NAME=="������ó" && $PER_SURNAME=="�ɵ���") { $PER_NAME = "���ž�ó"; $PER_SURNAME = "�ɵ��ǷԹ"; }
			elseif ($PER_NAME=="侱����" && $PER_SURNAME=="����") { $PER_NAME = "侷����"; $PER_SURNAME = "������"; }
			elseif ($PER_NAME=="�ѷá�" && $PER_SURNAME=="��¹���") $PER_NAME = "�ѷ���";
			elseif ($PER_NAME=="�����ѡ���" && $PER_SURNAME=="�Թ��ԡԨ") $PER_NAME = "���ѡ���";
			elseif ($PER_NAME=="�Ѫ��" && $PER_SURNAME=="�ա���") $PER_SURNAME = "�ԡ���";
			elseif ($PER_NAME=="�Ӿ֧" && $PER_SURNAME=="�������") $PER_SURNAME = "�����ʶ���";
			elseif ($PER_NAME=="��óԴ�" && $PER_SURNAME=="�Ѫá��") $PER_SURNAME = "�ѭ���Ѳ���ó�";
			elseif ($PER_NAME=="����ѵ��" && $PER_SURNAME=="�ͧ����ͧ") $PER_SURNAME = "ࡵؾ���ѹ���";
			elseif ($PER_NAME=="���Գ�" && $PER_SURNAME=="�÷ͧ") $PER_SURNAME = "���ⷪ��";
			elseif ($PER_NAME=="�����ѵ��" && $PER_SURNAME=="���Թ����Ժ��") $PER_SURNAME = "���Թ����Ժ��";
			elseif ($PER_NAME=="�����ѵ��" && $PER_SURNAME=="���Ȼͧ����") $PER_NAME = "�Ѫ�Ҿ�";
			elseif ($PER_NAME=="����ɰ�" && $PER_SURNAME=="佨ѹ���") $PER_SURNAME = "��ѹ���";
			elseif ($PER_NAME=="����ش�" && $PER_SURNAME=="���⪵�ǧ��") $PER_NAME = "�ѷ��ѹ��";
			elseif ($PER_NAME=="������ó�" && $PER_SURNAME=="�ѹ��⤵�") $PER_SURNAME = "�ѹ�⤵�";
			elseif ($PER_NAME=="�����ѵ��" && $PER_SURNAME=="������ԾѸ��") $PER_SURNAME = "������ԾѲ��";
			elseif ($PER_NAME=="ʧ��ҹ��" && $PER_SURNAME=="�ع���") { $PER_NAME = "�Ѱ�����"; $PER_SURNAME = "��������ѡ���"; }
			elseif ($PER_NAME=="��⪤" && $PER_SURNAME=="�ҭ��Ѻ") $PER_SURNAME = "�ҭ���ѵԡ��";
			elseif ($PER_NAME=="�����" && $PER_SURNAME=="�س����") $PER_SURNAME = "�ح����";
			elseif ($PER_NAME=="�����" && $PER_SURNAME=="�ѹ�����") $PER_SURNAME = "�鹾����";
			elseif ($PER_NAME=="�ѹ��" && $PER_SURNAME=="���л��о�ó") $PER_NAME = "�Ե�";
			elseif ($PER_NAME=="�بԵ��" && $PER_SURNAME=="�ҹ�츹�ع��") { $PER_NAME = "�Ԩ��ҹ�"; $PER_SURNAME = "ķ��ç��"; }
			elseif ($PER_NAME=="�ؾѵ��" && $PER_SURNAME=="���ѵ�������") $PER_SURNAME = "���ѵ�侺����";
			elseif ($PER_NAME=="���ѵ�" && $PER_SURNAME=="����š") $PER_SURNAME = "����š��";
			elseif ($NAME1=="�����Шѹ����ʧ���") { $PER_NAME = "������"; $PER_SURNAME = "�ѹ����ʧ���"; }
			elseif ($PER_NAME=="�����ó�" && $PER_SURNAME=="�آ������԰") $PER_SURNAME = "�ط�Ծѹ��";
			elseif ($PER_NAME=="���Ҿ�" && $PER_SURNAME=="�վ��ط�") $PER_SURNAME = "�վ��ط��";
			elseif ($PER_NAME=="�Ѩ���" && $PER_SURNAME=="������ա�Ū��") $PER_SURNAME = "�������ʡ�Ū��";
			elseif ($PER_NAME=="������" && $PER_SURNAME=="���ط��") $PER_SURNAME = "���ط���";

			$cmd = " select PER_ID, PER_CARDNO from PER_PERSONAL where PER_NAME = '$PER_NAME' and PER_SURNAME = '$PER_SURNAME' ";
			$count_data = $db_dpis->send_cmd($cmd);

			if ($count_data) {		
				$data_dpis = $db_dpis->get_array();
				$PER_ID = $data_dpis[PER_ID];
				$PER_CARDNO = $data_dpis[PER_CARDNO];
			} else	echo "$PER_NAME-$PER_SURNAME<br>";

			$cmd = " INSERT INTO PER_TRAINING (TRN_ID, PER_ID, TRN_TYPE, TR_CODE, TRN_NO, TRN_STARTDATE, TRN_ENDDATE, 
							TRN_ORG, TRN_PLACE, CT_CODE, TRN_FUND,	CT_CODE_FUND, UPDATE_USER, UPDATE_DATE, PER_CARDNO, TRN_DAY, TRN_REMARK)
							VALUES ($MAX_ID, $PER_ID, 1, '$TR_CODE', '$TRN_NO', '$TRN_STARTDATE', '$TRN_ENDDATE', 
							'$TRN_ORG', '$TRN_PLACE', '$CT_CODE', '$TRN_FUND', '$CT_CODE_FUND', $UPDATE_USER, '$UPDATE_DATE', 
							'$PER_CARDNO', $TRN_DAY, '$TRN_REMARK') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$MAX_ID++; 
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_TRAINING WHERE UPDATE_USER = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_NEW = $data[COUNT_NEW] + 0;
		echo "PER_TRAINING - $PER_TRAINING - $COUNT_NEW<br>";

	} // end if

?>