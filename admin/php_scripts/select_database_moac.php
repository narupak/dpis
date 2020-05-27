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
			if ($TRN_REMARK=="เสาร์ 10อาทิตย์11,เสาร์17-อาทิตย์ 18 ก.ย. 2548") {
				$TRN_STARTDATE = "2005-09-10";
				$TRN_ENDDATE = "2005-09-18";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="วันเสาร์ที่ 18-อาทิตย์ที่ 19 ก.พ.2549") {
				$TRN_STARTDATE = "2006-09-18";
				$TRN_ENDDATE = "2006-09-19";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="เม.ย.-ส.ค.51 (เฉพาะวันอังคารและวันพฤหัสบดี 17.00-19.00 น. รวม 50 ชั่วโมง)") {
				$TRN_STARTDATE = "2008-04-00";
				$TRN_ENDDATE = "2008-08-00";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="เม.ย.-ส.ค.51 (เฉพาะวันจันทร์และวันพุธ 17.00-19.00 น. รวม 50 ชั่วโมง)") {
				$TRN_STARTDATE = "2008-04-00";
				$TRN_ENDDATE = "2008-08-00";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="มี.ค.-พ.ค.43") {
				$TRN_STARTDATE = "2000-03-00";
				$TRN_ENDDATE = "2000-05-00";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="พ.ย. 2546") {
				$TRN_STARTDATE = "2003-11-00";
				$TRN_ENDDATE = "2003-11-00";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="ต.ค.-ธ.ค.45") {
				$TRN_STARTDATE = "2002-10-00";
				$TRN_ENDDATE = "2002-12-00";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="ช่วงที่ 1 24-28 เม.ย.2548 ช่วงที่ 2 กลุ่ม 3  29 พ.ค.-1 มิ.ย.49 กลุ่ม 4  19-22 มิย.49") {
				$TRN_STARTDATE = "2006-04-24";
				$TRN_ENDDATE = "2007-06-22";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="9-19 พฤษภาคม 2548") {
				$TRN_STARTDATE = "2005-05-09";
				$TRN_ENDDATE = "2005-05-19";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK=="9-19 ก.ย. 45") {
				$TRN_STARTDATE = "2002-09-09";
				$TRN_ENDDATE = "2002-09-19";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK=="9-13 มี.ค.52") {
				$TRN_STARTDATE = "2009-03-09";
				$TRN_ENDDATE = "2009-03-13";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="9-13 มิ.ย. 40") {
				$TRN_STARTDATE = "1997-06-09";
				$TRN_ENDDATE = "1997-06-13";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="9-13 พ.ค. 40") {
				$TRN_STARTDATE = "1997-05-09";
				$TRN_ENDDATE = "1997-05-13";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="9-13 กรกฎาคม 2550") {
				$TRN_STARTDATE = "2007-07-09";
				$TRN_ENDDATE = "2007-07-13";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="9-13 ก.ย.45") {
				$TRN_STARTDATE = "2002-09-09";
				$TRN_ENDDATE = "2002-09-13";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="9-12 มกราคม 2549") {
				$TRN_STARTDATE = "2006-01-09";
				$TRN_ENDDATE = "2006-01-12";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="9-12 ม.ค.2547") {
				$TRN_STARTDATE = "2004-01-09";
				$TRN_ENDDATE = "2004-01-12";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="9-12 ก.พ. 42") {
				$TRN_STARTDATE = "1999-02-09";
				$TRN_ENDDATE = "1999-02-12";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="9-11,16-18 พ.ค. 40") {
				$TRN_STARTDATE = "1997-05-09";
				$TRN_ENDDATE = "1997-05-18";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="9-11,13 สิงหาคม 2547") {
				$TRN_STARTDATE = "2004-08-09";
				$TRN_ENDDATE = "2004-08-13";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="9-11,13 ส.ค. 47") {
				$TRN_STARTDATE = "2004-08-09";
				$TRN_ENDDATE = "2004-08-13";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="9-11 มีนาคม 2547") {
				$TRN_STARTDATE = "2004-03-09";
				$TRN_ENDDATE = "2004-03-11";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="9-11 มี.ค. 47") {
				$TRN_STARTDATE = "2004-03-09";
				$TRN_ENDDATE = "2004-03-13";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="9-11 มิ.ย. 40") {
				$TRN_STARTDATE = "1997-06-09";
				$TRN_ENDDATE = "1997-06-13";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="9-11 พ.ย. 44") {
				$TRN_STARTDATE = "2001-11-09";
				$TRN_ENDDATE = "2001-11-11";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="9-11 พ.ค.44") {
				$TRN_STARTDATE = "2001-05-09";
				$TRN_ENDDATE = "2001-05-11";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="9-10,12-13 พฤษภาคม 2548") {
				$TRN_STARTDATE = "2005-05-09";
				$TRN_ENDDATE = "2005-05-13";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="9-10 สิงหาคม 2552") {
				$TRN_STARTDATE = "2009-08-09";
				$TRN_ENDDATE = "2009-08-10";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="9-10 มี.ค. 52") {
				$TRN_STARTDATE = "2009-03-09";
				$TRN_ENDDATE = "2009-03-10";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="9-10 มิ.ย. 40") {
				$TRN_STARTDATE = "1997-06-09";
				$TRN_ENDDATE = "1997-06-10";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="9-10 ม.ค.2550") {
				$TRN_STARTDATE = "2007-01-09";
				$TRN_ENDDATE = "2007-01-10";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="9-10 กรกฎาคม 2550") {
				$TRN_STARTDATE = "2007-07-09";
				$TRN_ENDDATE = "2007-07-10";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="9 มิ.ย.-18 ส.ค. 40 (เฉพาะวันจันทร์)") {
				$TRN_STARTDATE = "1997-06-09";
				$TRN_ENDDATE = "1997-08-18";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="9 พ.ย.-1 ธ.ค.43") {
				$TRN_STARTDATE = "2000-11-09";
				$TRN_ENDDATE = "2000-12-01";
				$TRN_DAY = 23;
			} elseif ($TRN_REMARK=="9 ธ.ค.39 - 2 ก.พ.40") {
				$TRN_STARTDATE = "1996-12-09";
				$TRN_ENDDATE = "1997-02-02";
				$TRN_DAY = 56;
			} elseif ($TRN_REMARK=="9 ก.พ.-9 เม.ย.47") {
				$TRN_STARTDATE = "2004-02-09";
				$TRN_ENDDATE = "2004-04-09";
				$TRN_DAY = 61;
			} elseif ($TRN_REMARK=="9 ก.ค.47") {
				$TRN_STARTDATE = "2004-07-09";
				$TRN_ENDDATE = "2004-07-09";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="9 ก.ค.-13 ต.ค. 36") {
				$TRN_STARTDATE = "1993-07-09";
				$TRN_ENDDATE = "1993-10-13";
				$TRN_DAY = 97;
			} elseif ($TRN_REMARK=="9 - 20 มกราคม 2549") {
				$TRN_STARTDATE = "2006-01-09";
				$TRN_ENDDATE = "2006-01-20";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="9 - 11  ส.ค.49") {
				$TRN_STARTDATE = "2006-08-09";
				$TRN_ENDDATE = "2006-08-11";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="8-9 มี.ค. 44") {
				$TRN_STARTDATE = "2001-03-08";
				$TRN_ENDDATE = "2001-03-09";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="8-9 มิ.ย.42") {
				$TRN_STARTDATE = "1999-06-08";
				$TRN_ENDDATE = "1999-06-09";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="8-9 มิ.ย. 52") {
				$TRN_STARTDATE = "2009-06-08";
				$TRN_ENDDATE = "2009-06-09";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="8-9 ก.พ. และ 15 ก.พ.-26 มี.ค.42") {
				$TRN_STARTDATE = "1999-02-08";
				$TRN_ENDDATE = "1999-03-26";
				$TRN_DAY = 42;
			} elseif ($TRN_REMARK=="8-9 ก.ค. 42") {
				$TRN_STARTDATE = "1999-07-08";
				$TRN_ENDDATE = "1999-07-09";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="8-27 พ.ย.43") {
				$TRN_STARTDATE = "2000-11-08";
				$TRN_ENDDATE = "2000-11-27";
				$TRN_DAY = 20;
			} elseif ($TRN_REMARK=="8-12 มี.ค.42") {
				$TRN_STARTDATE = "1999-03-08";
				$TRN_ENDDATE = "1999-03-12";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="8-12 มี.ค. 47") {
				$TRN_STARTDATE = "2004-03-08";
				$TRN_ENDDATE = "2004-03-12";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="8-12 มิ.ย. 52") {
				$TRN_STARTDATE = "2009-06-08";
				$TRN_ENDDATE = "2009-06-12";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="8-12 พ.ค.43") {
				$TRN_STARTDATE = "2000-05-08";
				$TRN_ENDDATE = "2000-05-12";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="8-12 กันยายน 2551") {
				$TRN_STARTDATE = "2008-09-08";
				$TRN_ENDDATE = "2008-09-12";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="8-12 ก.พ.42") {
				$TRN_STARTDATE = "1999-02-08";
				$TRN_ENDDATE = "1999-02-12";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="8-12 ก.ค. 39") {
				$TRN_STARTDATE = "1996-07-08";
				$TRN_ENDDATE = "1996-07-12";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="8-12  มีนาคม 2547") {
				$TRN_STARTDATE = "2004-03-08";
				$TRN_ENDDATE = "2004-03-12";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="8-10 เม.ย. 52") {
				$TRN_STARTDATE = "2009-04-08";
				$TRN_ENDDATE = "2009-04-10";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="8-10 เม.ย. 40") {
				$TRN_STARTDATE = "1997-04-08";
				$TRN_ENDDATE = "1997-04-10";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="8-10 มี.ค. 47") {
				$TRN_STARTDATE = "2004-03-08";
				$TRN_ENDDATE = "2004-03-10";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="8-10 มิถุนายน 2548") {
				$TRN_STARTDATE = "2005-06-08";
				$TRN_ENDDATE = "2005-06-10";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="8-10 พฤษภาคม  2549") {
				$TRN_STARTDATE = "2006-05-08";
				$TRN_ENDDATE = "2006-05-10";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="8-10 พ.ย.42") {
				$TRN_STARTDATE = "1999-11-08";
				$TRN_ENDDATE = "1999-11-10";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="8 ส.ค.43") {
				$TRN_STARTDATE = "2000-08-08";
				$TRN_ENDDATE = "2000-08-08";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="8 ส.ค.-12 ก.ย. 47 (เฉพาะวันเสาร์หรือวันอาทิตย์ สัปดาห์ละ 1 วัน)") {
				$TRN_STARTDATE = "2004-08-08";
				$TRN_ENDDATE = "2004-09-19";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="8 เม.ย.-30 ก.ค.43 (เฉพาะวันเสาร์หรือวันอาทิตย์)") {
				$TRN_STARTDATE = "2000-04-08";
				$TRN_ENDDATE = "2000-07-30";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="8 มิ.ย.-30 ส.ค.42 (อังคาร,พฤหัส 17.30-20.30 น. )") {
				$TRN_STARTDATE = "1999-06-08";
				$TRN_ENDDATE = "1999-08-30";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="8 ม.ค.-4 เม.ย.40") {
				$TRN_STARTDATE = "1997-01-08";
				$TRN_ENDDATE = "1997-04-04";
				$TRN_DAY = 80;
			} elseif ($TRN_REMARK=="8 ม.ค.-1 มี.ค.45") {
				$TRN_STARTDATE = "2002-01-08";
				$TRN_ENDDATE = "2002-03-01";
				$TRN_DAY = 53;
			} elseif ($TRN_REMARK=="8 ก.ย.43") {
				$TRN_STARTDATE = "2000-09-08";
				$TRN_ENDDATE = "2000-09-08";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="7-9 มิถุนายน  2547") {
				$TRN_STARTDATE = "2004-06-07";
				$TRN_ENDDATE = "2004-06-09";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="7-9 มิ.ย.43") {
				$TRN_STARTDATE = "2000-06-07";
				$TRN_ENDDATE = "2000-06-09";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="7-9 มิ.ย. 47") {
				$TRN_STARTDATE = "2004-06-07";
				$TRN_ENDDATE = "2004-06-09";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="7-9 พ.ย. 44") {
				$TRN_STARTDATE = "2001-11-07";
				$TRN_ENDDATE = "2001-11-09";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="7-9 พ.ค.39") {
				$TRN_STARTDATE = "1996-05-07";
				$TRN_ENDDATE = "1996-05-09";
				$TRN_DAY = 3;
 			} elseif ($TRN_REMARK=="7-9 ต.ค. 39") {
				$TRN_STARTDATE = "1996-10-07";
				$TRN_ENDDATE = "1996-10-09";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="7-9 ก.ย.42") {
				$TRN_STARTDATE = "1999-09-07";
				$TRN_ENDDATE = "1999-09-09";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="7-8 เมษายน 2552") {
				$TRN_STARTDATE = "2009-04-07";
				$TRN_ENDDATE = "2009-04-08";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="7-8 เม.ย. 48") {
				$TRN_STARTDATE = "2005-04-07";
				$TRN_ENDDATE = "2005-04-08";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="7-8 พ.ย. 39") {
				$TRN_STARTDATE = "1996-11-07";
				$TRN_ENDDATE = "1996-11-08";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="7-8 ธ.ค.2549") {
				$TRN_STARTDATE = "2006-12-07";
				$TRN_ENDDATE = "2006-12-08";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="7-28 พ.ค.42") {
				$TRN_STARTDATE = "1999-05-07";
				$TRN_ENDDATE = "1999-05-28";
				$TRN_DAY = 22;
			} elseif ($TRN_REMARK=="7-18 มิ.ย.42") {
				$TRN_STARTDATE = "1999-06-07";
				$TRN_ENDDATE = "1999-06-18";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="7-18 มิ.ย.2547") {
				$TRN_STARTDATE = "2004-06-07";
				$TRN_ENDDATE = "2004-06-18";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="7-18 มิ.ย. 47") {
				$TRN_STARTDATE = "2004-06-07";
				$TRN_ENDDATE = "2004-06-18";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="7-17 มีนาคม 2548") {
				$TRN_STARTDATE = "2005-03-07";
				$TRN_ENDDATE = "2005-03-17";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="7-17 มิ.ย.2547") {
				$TRN_STARTDATE = "2004-06-07";
				$TRN_ENDDATE = "2004-06-17";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="7-17 มิ.ย. 47") {
				$TRN_STARTDATE = "2004-06-07";
				$TRN_ENDDATE = "2004-06-17";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="7-12 ก.พ. 42") {
				$TRN_STARTDATE = "1999-02-07";
				$TRN_ENDDATE = "1999-02-12";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="7-11 มีนาคม 2548") {
				$TRN_STARTDATE = "2005-03-07";
				$TRN_ENDDATE = "2005-03-11";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="7-11 มิถุนายน 2547") {
				$TRN_STARTDATE = "2004-06-07";
				$TRN_ENDDATE = "2004-06-11";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="7-11 มิถุนายน  2547") {
				$TRN_STARTDATE = "2004-06-07";
				$TRN_ENDDATE = "2004-06-11";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="7-11 มิ.ย. 47") {
				$TRN_STARTDATE = "2004-06-07";
				$TRN_ENDDATE = "2004-06-11";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="7-11 มิ.ย. 42") {
				$TRN_STARTDATE = "1999-06-07";
				$TRN_ENDDATE = "1999-06-11";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="7-11 กันยายน 2552") {
				$TRN_STARTDATE = "2006-09-07";
				$TRN_ENDDATE = "2006-09-11";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="7-11 ก.ค. 40") {
				$TRN_STARTDATE = "1997-07-07";
				$TRN_ENDDATE = "1997-07-11";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="7-10 พฤศจิกายน 2548") {
				$TRN_STARTDATE = "2005-11-07";
				$TRN_ENDDATE = "2005-11-10";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="7 เม.ย.-12 มิ.ย.41") {
				$TRN_STARTDATE = "1998-04-07";
				$TRN_ENDDATE = "1998-06-12";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="7 มิ.ย.2548 (17.30-19.00)") {
				$TRN_STARTDATE = "2005-06-07";
				$TRN_ENDDATE = "2005-06-07";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="7 ม.ค.-1 เม.ย.34") {
				$TRN_STARTDATE = "1991-01-07";
				$TRN_ENDDATE = "1991-04-01";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="7 ก.พ.-14 มิ.ย. 52") {
				$TRN_STARTDATE = "2009-02-07";
				$TRN_ENDDATE = "2009-06-14";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="6-9 มี.ค. 40") {
				$TRN_STARTDATE = "1997-03-06";
				$TRN_ENDDATE = "1997-03-09";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="6-9 มิ.ย.48") {
				$TRN_STARTDATE = "2005-06-06";
				$TRN_ENDDATE = "2005-06-09";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="6-9 กรกฎาคม 2547") {
				$TRN_STARTDATE = "2004-07-06";
				$TRN_ENDDATE = "2004-07-09";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="6-9 ก.ค. 47") {
				$TRN_STARTDATE = "2004-07-06";
				$TRN_ENDDATE = "2004-07-09";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="6-8 มิ.ย.48") {
				$TRN_STARTDATE = "2005-06-06";
				$TRN_ENDDATE = "2005-06-08";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="6-8 พ.ย. 39") {
				$TRN_STARTDATE = "1996-11-06";
				$TRN_ENDDATE = "1996-11-08";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="6-8 พ.ค. 40") {
				$TRN_STARTDATE = "1997-05-06";
				$TRN_ENDDATE = "1997-05-08";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="6-7,10-11 ก.ค.43") {
				$TRN_STARTDATE = "2000-07-06";
				$TRN_ENDDATE = "2000-07-11";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="6-7 ก.พ.42") {
				$TRN_STARTDATE = "1999-02-06";
				$TRN_ENDDATE = "1999-02-07";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="6-7 ก.ค.43") {
				$TRN_STARTDATE = "2000-07-06";
				$TRN_ENDDATE = "2000-07-07";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="6-7 ก.ค.39") {
				$TRN_STARTDATE = "1996-07-06";
				$TRN_ENDDATE = "1996-07-07";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="6-20 ธ.ค.43") {
				$TRN_STARTDATE = "2000-12-06";
				$TRN_ENDDATE = "2000-12-20";
				$TRN_DAY = 15;
			} elseif ($TRN_REMARK=="6-17 มี.ค.2549") {
				$TRN_STARTDATE = "2006-03-06";
				$TRN_ENDDATE = "2006-03-17";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="6-17 พ.ย.43") {
				$TRN_STARTDATE = "2000-11-06";
				$TRN_ENDDATE = "2000-11-17";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="6-16 มิ.ย.43") {
				$TRN_STARTDATE = "2006-06-06";
				$TRN_ENDDATE = "2006-06-16";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="6-14 ก.ย.42") {
				$TRN_STARTDATE = "2005-09-06";
				$TRN_ENDDATE = "2005-09-14";
				$TRN_DAY = 9;
			} elseif ($TRN_REMARK=="6-11 ส.ค. 43") {
				$TRN_STARTDATE = "2006-08-06";
				$TRN_ENDDATE = "2006-08-11";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="6-10 สิงหาคม 2550") {
				$TRN_STARTDATE = "2007-08-06";
				$TRN_ENDDATE = "2007-08-10";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="6-10 มิถุนายน 2548 ดูงาน 9-10 มิถุนายน") {
				$TRN_STARTDATE = "2005-06-06";
				$TRN_ENDDATE = "2005-06-10";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="6 ส.ค.41") {
				$TRN_STARTDATE = "2004-08-06";
				$TRN_ENDDATE = "2004-08-06";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="6 เม.ย.-12 มิ.ย. 41") {
				$TRN_STARTDATE = "2004-04-06";
				$TRN_ENDDATE = "2004-06-12";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="6 มีนาคม 2552") {
				$TRN_STARTDATE = "2009-03-06";
				$TRN_ENDDATE = "2009-03-06";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="6 ม.ค.-11 เม.ย. 40") {
				$TRN_STARTDATE = "2003-01-06";
				$TRN_ENDDATE = "2003-04-11";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="6 ม.ค. - 14 มี.ค. 40") {
				$TRN_STARTDATE = "2003-01-06";
				$TRN_ENDDATE = "2003-03-14";
				$TRN_DAY = 0;
 			} elseif ($TRN_REMARK=="6 พ.ค.-8 ส.ค. 40") {
				$TRN_STARTDATE = "2003-05-06";
				$TRN_ENDDATE = "2003-08-08";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="6 ธ.ค.48") {
				$TRN_STARTDATE = "2005-12-06";
				$TRN_ENDDATE = "2005-12-06";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="6 ก.ค.-23 ส.ค.48") {
				$TRN_STARTDATE = "2005-07-06";
				$TRN_ENDDATE = "2005-08-23";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="5-9 ส.ค. 39") {
				$TRN_STARTDATE = "1996-08-05";
				$TRN_ENDDATE = "1996-08-09";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="5-9 มี.ค. 44") {
				$TRN_STARTDATE = "2001-08-05";
				$TRN_ENDDATE = "2001-08-09";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="5-9 มิ.ย.43") {
				$TRN_STARTDATE = "2000-06-05";
				$TRN_ENDDATE = "2000-06-09";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="5-9 ม.ค. 52") {
				$TRN_STARTDATE = "2009-01-05";
				$TRN_ENDDATE = "2009-01-09";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="5-9 กันยายน 2548") {
				$TRN_STARTDATE = "2005-09-05";
				$TRN_ENDDATE = "2005-09-09";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="5-8 สิงหาคม 2547") {
				$TRN_STARTDATE = "2004-08-05";
				$TRN_ENDDATE = "2004-08-08";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="5-8 ส.ค. 47") {
				$TRN_STARTDATE = "2004-08-05";
				$TRN_ENDDATE = "2004-08-08";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="5-8 มิ.ย. 40") {
				$TRN_STARTDATE = "1997-06-05";
				$TRN_ENDDATE = "1997-06-08";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="5-7 มิ.ย. 45") {
				$TRN_STARTDATE = "2002-06-05";
				$TRN_ENDDATE = "2002-06-07";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="5-7 กันยายน 2548") {
				$TRN_STARTDATE = "2005-09-05";
				$TRN_ENDDATE = "2005-09-07";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="5-7 กรกฎาคม 2549") {
				$TRN_STARTDATE = "2006-07-05";
				$TRN_ENDDATE = "2006-07-07";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="5-7 ก.ย. 2547") {
				$TRN_STARTDATE = "2004-09-05";
				$TRN_ENDDATE = "2004-09-07";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="5-6 สิงหาคม 2551") {
				$TRN_STARTDATE = "2008-08-05";
				$TRN_ENDDATE = "2008-08-06";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="5-6 ส.ค.42") {
				$TRN_STARTDATE = "1999-08-05";
				$TRN_ENDDATE = "1999-08-06";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="5-6 กุมภาพันธ์ 2549") {
				$TRN_STARTDATE = "2006-02-05";
				$TRN_ENDDATE = "2006-02-06";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="5-16 ม.ค. 41") {
				$TRN_STARTDATE = "1998-01-05";
				$TRN_ENDDATE = "1998-01-16";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="5-16 ก.ค.2547") {
				$TRN_STARTDATE = "2004-07-05";
				$TRN_ENDDATE = "2004-07-16";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="5-16 ก.ค. 47") {
				$TRN_STARTDATE = "2004-07-05";
				$TRN_ENDDATE = "2004-07-16";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="5-15 ก.ค.2547") {
				$TRN_STARTDATE = "2004-07-05";
				$TRN_ENDDATE = "2004-07-15";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="5-15 ก.ค. 47") {
				$TRN_STARTDATE = "2004-07-05";
				$TRN_ENDDATE = "2004-07-15";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="5-15 ก.ค. 47") {
				$TRN_STARTDATE = "2004-07-05";
				$TRN_ENDDATE = "2004-07-15";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="5-13 ก.ค.42") {
				$TRN_STARTDATE = "1999-07-05";
				$TRN_ENDDATE = "1999-07-13";
				$TRN_DAY = 9;
			} elseif ($TRN_REMARK=="5 มิ.ย.-17 ก.ค.47 (วันเสาร์)") {
				$TRN_STARTDATE = "2004-06-05";
				$TRN_ENDDATE = "2004-07-17";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="5 มิ.ย.17 ก.ค.47") {
				$TRN_STARTDATE = "2004-06-05";
				$TRN_ENDDATE = "2004-07-17";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="5 ต.ค. - 11 ธ.ค. 41") {
				$TRN_STARTDATE = "1998-10-05";
				$TRN_ENDDATE = "1998-12-11";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="5 ก.ค.-23 ส.ค.43") {
				$TRN_STARTDATE = "2000-07-05";
				$TRN_ENDDATE = "2000-08-23";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="5 - 10 ส.ค. 2546") {
				$TRN_STARTDATE = "2003-08-05";
				$TRN_ENDDATE = "2003-08-10";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="4-8 ก.ค.2548") {
				$TRN_STARTDATE = "2005-07-04";
				$TRN_ENDDATE = "2005-07-08";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="4-7 ส.ค. 40") {
				$TRN_STARTDATE = "1997-08-04";
				$TRN_ENDDATE = "1997-08-07";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="4-6 มีนาคม 2551") {
				$TRN_STARTDATE = "2008-03-04";
				$TRN_ENDDATE = "2008-03-06";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="4-6  เมษายน 2549") {
				$TRN_STARTDATE = "2006-04-04";
				$TRN_ENDDATE = "2006-04-06";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="4-5 มี.ค. 52") {
				$TRN_STARTDATE = "2009-03-04";
				$TRN_ENDDATE = "2009-03-05";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="4-5 พ.ย. 47") {
				$TRN_STARTDATE = "2004-11-04";
				$TRN_ENDDATE = "2004-11-05";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="4-5 พ.ย. 39") {
				$TRN_STARTDATE = "1996-11-04";
				$TRN_ENDDATE = "1996-11-05";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="4-5 กันยายน 2552") {
				$TRN_STARTDATE = "2009-09-04";
				$TRN_ENDDATE = "2009-09-05";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="4-5 กันยายน 2551") {
				$TRN_STARTDATE = "2008-09-04";
				$TRN_ENDDATE = "2008-09-05";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="4-22 ส.ค. 40") {
				$TRN_STARTDATE = "1997-08-04";
				$TRN_ENDDATE = "1997-08-22";
				$TRN_DAY = 19;
			} elseif ($TRN_REMARK=="4-15 มิ.ย.44") {
				$TRN_STARTDATE = "2001-06-04";
				$TRN_ENDDATE = "2001-06-15";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="4-10 ม.ค.42") {
				$TRN_STARTDATE = "1999-01-04";
				$TRN_ENDDATE = "1999-01-10";
				$TRN_DAY = 7;
			} elseif ($TRN_REMARK=="4-10 ม.ค. 42") {
				$TRN_STARTDATE = "1999-01-04";
				$TRN_ENDDATE = "1999-01-10";
				$TRN_DAY = 7;
			} elseif ($TRN_REMARK=="4 ส.ค.-13 ก.ย.42 (เฉพาะวันจันทร์,พุธ,ศุกร์)") {
				$TRN_STARTDATE = "1999-08-04";
				$TRN_ENDDATE = "1999-09-13";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="4 เมษายน 2551") {
				$TRN_STARTDATE = "2008-04-04";
				$TRN_ENDDATE = "2008-04-04";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="4 เม.ย. - 10 มิ.ย. 43") {
				$TRN_STARTDATE = "2000-04-04";
				$TRN_ENDDATE = "2000-06-10";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="3-8 เมษายน 2548") {
				$TRN_STARTDATE = "2005-04-03";
				$TRN_ENDDATE = "2005-04-08";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="3-7 ส.ค.41") {
				$TRN_STARTDATE = "1998-08-03";
				$TRN_ENDDATE = "1998-08-07";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="3-7 มี.ค. 40") {
				$TRN_STARTDATE = "1997-03-03";
				$TRN_ENDDATE = "1997-03-07";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="3-6 สิงหาคม 2547") {
				$TRN_STARTDATE = "2004-08-03";
				$TRN_ENDDATE = "2004-08-06";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="3-6 ส.ค. 47") {
				$TRN_STARTDATE = "2004-08-03";
				$TRN_ENDDATE = "2004-08-06";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="3-6 มิ.ย. 45") {
				$TRN_STARTDATE = "2002-06-03";
				$TRN_ENDDATE = "2002-06-06";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="3-6 พ.ย. 47") {
				$TRN_STARTDATE = "2004-11-03";
				$TRN_ENDDATE = "2004-11-06";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="3-5 เม.ย.43") {
				$TRN_STARTDATE = "2000-04-03";
				$TRN_ENDDATE = "2000-04-05";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="3-5 เม.ย.39") {
				$TRN_STARTDATE = "1998-04-03";
				$TRN_ENDDATE = "1998-04-05";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="3-5 เม.ย. 45") {
				$TRN_STARTDATE = "2002-04-03";
				$TRN_ENDDATE = "2002-04-05";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="3-5 เม.ย. 44") {
				$TRN_STARTDATE = "2001-04-03";
				$TRN_ENDDATE = "2001-04-05";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="3-5 มี.ค.42") {
				$TRN_STARTDATE = "1999-03-03";
				$TRN_ENDDATE = "1999-03-05";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="3-5 พ.ย.46") {
				$TRN_STARTDATE = "2003-11-03";
				$TRN_ENDDATE = "2003-11-05";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="3-5 กันยายน 2552") {
				$TRN_STARTDATE = "2009-09-03";
				$TRN_ENDDATE = "2009-09-05";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="3-4 ส.ค.42") {
				$TRN_STARTDATE = "1999-08-03";
				$TRN_ENDDATE = "1999-08-04";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="3-4 เม.ย.42") {
				$TRN_STARTDATE = "1999-04-03";
				$TRN_ENDDATE = "1999-04-04";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="3-4 พ.ค. 40") {
				$TRN_STARTDATE = "1997-05-03";
				$TRN_ENDDATE = "1997-05-04";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="3-19 มิ.ย. 39") {
				$TRN_STARTDATE = "1996-06-03";
				$TRN_ENDDATE = "1996-06-19";
				$TRN_DAY = 17;
			} elseif ($TRN_REMARK=="3-19 ก.พ.42") {
				$TRN_STARTDATE = "1999-02-03";
				$TRN_ENDDATE = "1999-02-19";
				$TRN_DAY = 17;
			} elseif ($TRN_REMARK=="3-17 พ.ค.42") {
				$TRN_STARTDATE = "1999-05-03";
				$TRN_ENDDATE = "1999-05-17";
				$TRN_DAY = 15;
			} elseif ($TRN_REMARK=="3-14 มิ.ย. 45") {
				$TRN_STARTDATE = "2002-06-03";
				$TRN_ENDDATE = "2002-06-14";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="3-12 มิ.ย. 39") {
				$TRN_STARTDATE = "1998-06-03";
				$TRN_ENDDATE = "1998-06-12";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK=="31 ส.ค.-3 ก.ย.2547") {
				$TRN_STARTDATE = "2004-08-31";
				$TRN_ENDDATE = "2004-09-03";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="31 ส.ค.-3 ก.ย. 47") {
				$TRN_STARTDATE = "2004-08-31";
				$TRN_ENDDATE = "2004-09-03";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="31 มีนาคม-3 เมษายน 2551") {
				$TRN_STARTDATE = "2008-03-31";
				$TRN_ENDDATE = "2008-04-03";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="31 มีนาคม 2552") {
				$TRN_STARTDATE = "2009-03-31";
				$TRN_ENDDATE = "2009-03-31";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="31 มีนาคม 2548") {
				$TRN_STARTDATE = "2005-03-31";
				$TRN_ENDDATE = "2005-03-31";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="31 ม.ค.-5 ก.พ. 42") {
				$TRN_STARTDATE = "1999-01-31";
				$TRN_ENDDATE = "1999-02-05";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="31 ม.ค.-17 พ.ค. 41") {
				$TRN_STARTDATE = "1998-01-31";
				$TRN_ENDDATE = "1998-05-17";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="30-31 สิงหาคม  1-2 กันยายน 2547") {
				$TRN_STARTDATE = "2004-08-30";
				$TRN_ENDDATE = "2004-09-02";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="30-31 ส.ค.,1-2 ก.ย.2547") {
				$TRN_STARTDATE = "2004-08-30";
				$TRN_ENDDATE = "2004-09-02";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="30-31 ส.ค.,1-2 ก.ย. 47") {
				$TRN_STARTDATE = "2004-08-30";
				$TRN_ENDDATE = "2004-09-02";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="30-31 มี.ค.50") {
				$TRN_STARTDATE = "2007-03-30";
				$TRN_ENDDATE = "2007-03-31";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="30-31 พ.ค.2548") {
				$TRN_STARTDATE = "2005-05-30";
				$TRN_ENDDATE = "2005-05-31";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="30-31 พ.ค., 1-3 มิ.ย. 48") {
				$TRN_STARTDATE = "2005-05-30";
				$TRN_ENDDATE = "2005-06-03";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="30-31 ต.ค. 40") {
				$TRN_STARTDATE = "1997-10-30";
				$TRN_ENDDATE = "1997-10-31";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="30 ส.ค.-3 ก.ย. 47") {
				$TRN_STARTDATE = "2004-08-30";
				$TRN_ENDDATE = "2004-09-03";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="30 ส.ค.-2 ก.ย.2547") {
				$TRN_STARTDATE = "2004-08-30";
				$TRN_ENDDATE = "2004-09-02";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="30 ส.ค.-2 ก.ย. 47") {
				$TRN_STARTDATE = "2004-08-30";
				$TRN_ENDDATE = "2004-09-02";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="30 เม.ย.-4 พ.ค. 44") {
				$TRN_STARTDATE = "2001-04-30";
				$TRN_ENDDATE = "2001-05-04";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="30 เม.ย.-15 พ.ค.48") {
				$TRN_STARTDATE = "2005-04-30";
				$TRN_ENDDATE = "2005-05-15";
				$TRN_DAY = 16;
			} elseif ($TRN_REMARK=="30 มี.ค. -3 เม.ย.52") {
				$TRN_STARTDATE = "2009-03-30";
				$TRN_ENDDATE = "2009-04-03";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="30 มิ.ย.42") {
				$TRN_STARTDATE = "1999-06-30";
				$TRN_ENDDATE = "1999-06-30";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="30 มิ.ย.-3 ก.ค. 40") {
				$TRN_STARTDATE = "1997-06-30";
				$TRN_ENDDATE = "1997-07-03";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="30 พฤษภาคม-2 มิถุนายน 2548") {
				$TRN_STARTDATE = "2005-05-30";
				$TRN_ENDDATE = "2005-06-02";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="30 พ.ย. 43") {
				$TRN_STARTDATE = "2000-11-30";
				$TRN_ENDDATE = "2000-11-30";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="30 พ.ค.-29 ส.ค.2549") {
				$TRN_STARTDATE = "2006-05-30";
				$TRN_ENDDATE = "2006-08-29";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="30 พ.ค.-1 มิ.ย.2548") {
				$TRN_STARTDATE = "2005-05-30";
				$TRN_ENDDATE = "2005-06-01";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="30 กรกฎาคม-1 สิงหาคม 2551") {
				$TRN_STARTDATE = "2008-07-31";
				$TRN_ENDDATE = "2008-08-01";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="30 กรกฎาคม 2552") {
				$TRN_STARTDATE = "2009-07-30";
				$TRN_ENDDATE = "2009-07-30";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="30 ก.ย.-1 ต.ค.43") {
				$TRN_STARTDATE = "2000-09-30";
				$TRN_ENDDATE = "2000-10-01";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="30 ก.ย.- 4 ต.ค. 44") {
				$TRN_STARTDATE = "2001-09-30";
				$TRN_ENDDATE = "2001-10-04";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="3 ส.ค.42") {
				$TRN_STARTDATE = "1999-08-03";
				$TRN_ENDDATE = "1999-08-03";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="3 ส.ค.-16 ก.ย.48") {
				$TRN_STARTDATE = "2005-08-03";
				$TRN_ENDDATE = "2005-09-16";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="3 มิ.ย.47") {
				$TRN_STARTDATE = "2004-06-03";
				$TRN_ENDDATE = "2004-06-03";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="3 มิ.ย.2547") {
				$TRN_STARTDATE = "2004-06-03";
				$TRN_ENDDATE = "2004-06-03";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="3 มิ.ย.-12 ก.ย. 39") {
				$TRN_STARTDATE = "1996-06-03";
				$TRN_ENDDATE = "1996-09-12";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="3 พ.ค.-23 ก.ค.42") {
				$TRN_STARTDATE = "1999-05-03";
				$TRN_ENDDATE = "1999-07-23";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="3 กันยายน 2551") {
				$TRN_STARTDATE = "2008-09-03";
				$TRN_ENDDATE = "2008-09-03";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="3 ก.ค.-6 ก.ย. 45") {
				$TRN_STARTDATE = "2002-07-03";
				$TRN_ENDDATE = "2002-09-06";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="29-31 มีนาคม 2548") {
				$TRN_STARTDATE = "2005-03-29";
				$TRN_ENDDATE = "2005-03-31";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="29-31 มีนาคม 2547") {
				$TRN_STARTDATE = "2004-03-29";
				$TRN_ENDDATE = "2004-03-31";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="29-31 มี.ค. 47") {
				$TRN_STARTDATE = "2004-03-29";
				$TRN_ENDDATE = "2004-03-31";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="29-31 พ.ค. และ 1-2 มิ.ย.43") {
				$TRN_STARTDATE = "2000-05-29";
				$TRN_ENDDATE = "2000-06-02";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="29-30 มี.ค.42") {
				$TRN_STARTDATE = "1999-03-29";
				$TRN_ENDDATE = "1999-03-30";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="29-30 มิถุนายน  1-2 กรกฎาคม 2547") {
				$TRN_STARTDATE = "2004-06-29";
				$TRN_ENDDATE = "2004-07-02";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="29-30 มิ.ย.,1-2 ก.ค. 47") {
				$TRN_STARTDATE = "2004-06-29";
				$TRN_ENDDATE = "2004-07-02";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="29-30 มิ.ย. 52") {
				$TRN_STARTDATE = "2009-06-29";
				$TRN_ENDDATE = "2009-06-30";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="29-30 กรกฎาคม 2551") {
				$TRN_STARTDATE = "2008-07-29";
				$TRN_ENDDATE = "2008-07-30";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="29-30 ก.ย. 42") {
				$TRN_STARTDATE = "1999-09-29";
				$TRN_ENDDATE = "1999-09-30";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="29-30 ก.ค.42") {
				$TRN_STARTDATE = "1999-07-29";
				$TRN_ENDDATE = "1999-07-30";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="29-30 ก.ค. 42") {
				$TRN_STARTDATE = "1999-07-29";
				$TRN_ENDDATE = "1999-07-30";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="29 สิงหาคม - 9 กันยายน 2548") {
				$TRN_STARTDATE = "2005-08-29";
				$TRN_ENDDATE = "2005-09-09";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="29 ส.ค.-9 ก.ย.48") {
				$TRN_STARTDATE = "2005-08-29";
				$TRN_ENDDATE = "2005-09-09";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="29 ส.ค.-1 ก.ย. 43") {
				$TRN_STARTDATE = "2000-08-29";
				$TRN_ENDDATE = "2000-09-01";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="29 เมษายน - 1 พฤษภาคม 2547") {
				$TRN_STARTDATE = "2004-04-29";
				$TRN_ENDDATE = "2004-05-01";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="29 เม.ย.48 (09.00-12.00 น.)") {
				$TRN_STARTDATE = "2005-04-29";
				$TRN_ENDDATE = "2005-04-29";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="29 เม.ย.-1 พ.ค. 47") {
				$TRN_STARTDATE = "2004-04-29";
				$TRN_ENDDATE = "2004-05-01";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="29 เม.ย. - 3 พ.ค. 39") {
				$TRN_STARTDATE = "1996-04-29";
				$TRN_ENDDATE = "1996-05-03";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="29 ม.ค.41") {
				$TRN_STARTDATE = "1998-01-29";
				$TRN_ENDDATE = "1998-01-29";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="29 ม.ค. 47") {
				$TRN_STARTDATE = "2004-01-29";
				$TRN_ENDDATE = "2004-01-29";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="29 พ.ย.2548") {
				$TRN_STARTDATE = "2005-11-29";
				$TRN_ENDDATE = "2005-11-29";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="29 พ.ค.-1 มิ.ย.44") {
				$TRN_STARTDATE = "2001-05-29";
				$TRN_ENDDATE = "2001-06-29";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="29 ก.พ.-10 มี.ค.43") {
				$TRN_STARTDATE = "2000-02-29";
				$TRN_ENDDATE = "2000-03-10";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="29 ก.ค.-9 ส.ค. 45") {
				$TRN_STARTDATE = "2002-07-29";
				$TRN_ENDDATE = "2002-08-09";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28-31 ต.ค.41") {
				$TRN_STARTDATE = "1998-10-28";
				$TRN_ENDDATE = "1998-10-31";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="28-31 ก.ค. 40") {
				$TRN_STARTDATE = "1997-07-28";
				$TRN_ENDDATE = "1997-07-31";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="28-30 เมษายน 2548") {
				$TRN_STARTDATE = "2005-04-30";
				$TRN_ENDDATE = "2005-04-28";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="28-30 เม.ย. 40") {
				$TRN_STARTDATE = "1997-04-30";
				$TRN_ENDDATE = "1997-04-28";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="28-30 มิถุนายน 2547") {
				$TRN_STARTDATE = "2004-06-28";
				$TRN_ENDDATE = "2004-06-30";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="28-30 มิ.ย. 47") {
				$TRN_STARTDATE = "2004-06-28";
				$TRN_ENDDATE = "2004-06-30";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="28-30 มกราคม 2551") {
				$TRN_STARTDATE = "2008-01-30";
				$TRN_ENDDATE = "2008-01-28";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="28-30 พ.ย. 44") {
				$TRN_STARTDATE = "2001-11-28";
				$TRN_ENDDATE = "2001-11-30";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="28-30 กันยายน 2550") {
				$TRN_STARTDATE = "2007-09-28";
				$TRN_ENDDATE = "2007-09-30";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="28-30 กันยายน 2548") {
				$TRN_STARTDATE = "2005-09-28";
				$TRN_ENDDATE = "2005-09-30";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="28-30 ก.ค. 47") {
				$TRN_STARTDATE = "2004-07-28";
				$TRN_ENDDATE = "2004-07-30";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="28-29 เม.ย.48") {
				$TRN_STARTDATE = "2005-04-28";
				$TRN_ENDDATE = "2005-04-29";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="28-29 ม.ค.42") {
				$TRN_STARTDATE = "1999-01-28";
				$TRN_ENDDATE = "1999-01-29";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="28-29 พ.ย.49") {
				$TRN_STARTDATE = "2006-11-28";
				$TRN_ENDDATE = "2006-11-29";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="28-29 พ.ย. 39") {
				$TRN_STARTDATE = "1996-11-28";
				$TRN_ENDDATE = "1996-11-29";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="28-29 พ.ค.52") {
				$TRN_STARTDATE = "2009-05-28";
				$TRN_ENDDATE = "2009-05-29";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="28-29 พ.ค.40") {
				$TRN_STARTDATE = "1997-05-28";
				$TRN_ENDDATE = "1997-05-29";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="28-29 ธ.ค. 39") {
				$TRN_STARTDATE = "1996-12-28";
				$TRN_ENDDATE = "1996-12-29";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="28-29 ต.ค. 39") {
				$TRN_STARTDATE = "1996-10-28";
				$TRN_ENDDATE = "1996-10-29";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="28-29 กรกฎาคม 2548") {
				$TRN_STARTDATE = "2005-07-28";
				$TRN_ENDDATE = "2005-07-29";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="28 เม.ย.-9 พ.ค. 40") {
				$TRN_STARTDATE = "1997-04-28";
				$TRN_ENDDATE = "1997-05-09";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28 เม.ย. - 18 ก.ค. 46") {
				$TRN_STARTDATE = "2003-04-28";
				$TRN_ENDDATE = "2003-07-18";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28 มีนาคม-30 มิถุนายน 2549") {
				$TRN_STARTDATE = "2006-03-28";
				$TRN_ENDDATE = "2006-06-30";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28 มิ.ย.-2 ก.ค. 42") {
				$TRN_STARTDATE = "1999-06-28";
				$TRN_ENDDATE = "1999-07-02";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28 ม.ค.42") {
				$TRN_STARTDATE = "1999-01-28";
				$TRN_ENDDATE = "1999-01-28";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="28 พฤศจิกายน-2 ธันวาคม 2549") {
				$TRN_STARTDATE = "2006-11-28";
				$TRN_ENDDATE = "2006-12-02";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28 พ.ค. - 29 มิ.ย. 44") {
				$TRN_STARTDATE = "2001-05-28";
				$TRN_ENDDATE = "2001-06-29";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28 ต.ค.39 - 7 ก.พ. 40") {
				$TRN_STARTDATE = "1997-10-18";
				$TRN_ENDDATE = "1998-02-07";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28 ต.ค.-1 พ.ย. 39") {
				$TRN_STARTDATE = "1996-10-28";
				$TRN_ENDDATE = "1996-11-01";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28 กุมภาพันธ์ - 4 มีนาคม 2548") {
				$TRN_STARTDATE = "2005-02-28";
				$TRN_ENDDATE = "2005-03-04";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28 ก.พ.-4 มี.ค.48") {
				$TRN_STARTDATE = "2005-02-28";
				$TRN_ENDDATE = "2005-03-04";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28 ก.พ.-3 มี.ค.43") {
				$TRN_STARTDATE = "2000-02-28";
				$TRN_ENDDATE = "2000-03-03";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28 ก.ค.-18 ส.ค. 52") {
				$TRN_STARTDATE = "2009-07-28";
				$TRN_ENDDATE = "2009-08-18";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="28 - 30 พ.ย.48") {
				$TRN_STARTDATE = "2005-11-28";
				$TRN_ENDDATE = "2005-11-30";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="27-31 สิงหาคม, 3-7 กันยายน 2550") {
				$TRN_STARTDATE = "2007-08-27";
				$TRN_ENDDATE = "2007-09-07";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK=="27-31 สิงหาคม 2550") {
				$TRN_STARTDATE = "2007-08-27";
				$TRN_ENDDATE = "2007-08-31";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="27-31 มี.ค.43") {
				$TRN_STARTDATE = "2000-03-27";
				$TRN_ENDDATE = "2000-03-31";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="27-31 มี.ค. 43") {
				$TRN_STARTDATE = "2000-03-27";
				$TRN_ENDDATE = "2000-03-31";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="27-30 เม.ย.2547") {
				$TRN_STARTDATE = "2004-04-27";
				$TRN_ENDDATE = "2004-04-30";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="27-30 เม.ย. 52") {
				$TRN_STARTDATE = "2009-04-27";
				$TRN_ENDDATE = "2009-04-30";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="27-30 มี.ค. 48") {
				$TRN_STARTDATE = "2005-03-27";
				$TRN_ENDDATE = "2005-03-30";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="27-30 มิ.ย. 2543") {
				$TRN_STARTDATE = "2000-06-27";
				$TRN_ENDDATE = "2000-06-30";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="27-30 พ.ย,1 ธ.ค.49") {
				$TRN_STARTDATE = "2006-11-27";
				$TRN_ENDDATE = "2006-12-01";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="27-30 กรกฎาคม 2547") {
				$TRN_STARTDATE = "2004-07-27";
				$TRN_ENDDATE = "2004-07-30";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="27-30 ก.ย.42") {
				$TRN_STARTDATE = "1999-09-27";
				$TRN_ENDDATE = "1999-09-30";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="27-30 ก.ค. 47") {
				$TRN_STARTDATE = "2004-07-27";
				$TRN_ENDDATE = "2004-07-30";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="27-29 ส.ค.42") {
				$TRN_STARTDATE = "1999-08-27";
				$TRN_ENDDATE = "1999-08-29";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="27-29 เมษายน 2548") {
				$TRN_STARTDATE = "2005-04-27";
				$TRN_ENDDATE = "2005-04-29";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="27-29 เม.ย.50") {
				$TRN_STARTDATE = "2007-04-27";
				$TRN_ENDDATE = "2007-04-29";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="27-29 มี.ค.43") {
				$TRN_STARTDATE = "2000-03-27";
				$TRN_ENDDATE = "2000-03-29";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="27-29 มิถุนายน 2550") {
				$TRN_STARTDATE = "2007-06-27";
				$TRN_ENDDATE = "2007-06-29";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="27-29 มิถุนายน 2548") {
				$TRN_STARTDATE = "2005-06-27";
				$TRN_ENDDATE = "2005-06-29";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="27-29 มิ.ย.2548") {
				$TRN_STARTDATE = "2005-06-27";
				$TRN_ENDDATE = "2005-06-29";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="27-29 พ.ย. 39") {
				$TRN_STARTDATE = "1996-11-27";
				$TRN_ENDDATE = "1996-11-29";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="27-29 พ.ค.52") {
				$TRN_STARTDATE = "2009-05-27";
				$TRN_ENDDATE = "2009-05-29";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="27-28 สิงหาคม 2551") {
				$TRN_STARTDATE = "2008-08-27";
				$TRN_ENDDATE = "2008-08-28";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="27-28 พ.ย. 40") {
				$TRN_STARTDATE = "1997-11-27";
				$TRN_ENDDATE = "1997-11-28";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="27-28 พ.ย. 39") {
				$TRN_STARTDATE = "1996-11-27";
				$TRN_ENDDATE = "1996-11-28";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="27-28 ตุลาคม 2550") {
				$TRN_STARTDATE = "2007-10-27";
				$TRN_ENDDATE = "2007-10-28";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="27-28 ก.ย. 42") {
				$TRN_STARTDATE = "1999-09-27";
				$TRN_ENDDATE = "1999-09-28";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="27 ส.ค.-7 ก.ย. 44") {
				$TRN_STARTDATE = "2001-08-27";
				$TRN_ENDDATE = "2001-09-07";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="27 ส.ค.41") {
				$TRN_STARTDATE = "1998-08-27";
				$TRN_ENDDATE = "1998-08-27";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="27 เม.ย.-1 พ.ค. 52") {
				$TRN_STARTDATE = "2009-04-27";
				$TRN_ENDDATE = "2009-05-01";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="27 มิ.ย.-1 ก.ค.48") {
				$TRN_STARTDATE = "2005-06-27";
				$TRN_ENDDATE = "2005-07-01";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="27 ต.ค.,3,10,17,24 พ.ย. และ 1 ธ.ค. 39") {
				$TRN_STARTDATE = "1996-10-27";
				$TRN_ENDDATE = "1996-12-01";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="27 ก.พ.-3 มี.ค.43") {
				$TRN_STARTDATE = "2000-02-27";
				$TRN_ENDDATE = "2000-03-03";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="27 ก.พ.-10 มี.ค.49") {
				$TRN_STARTDATE = "2006-02-27";
				$TRN_ENDDATE = "2006-03-10";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="26-31 พ.ค. 40") {
				$TRN_STARTDATE = "1997-05-26";
				$TRN_ENDDATE = "1997-05-31";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="26-30 ส.ค. 39") {
				$TRN_STARTDATE = "1996-08-26";
				$TRN_ENDDATE = "1996-08-30";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="26-30 เมษายน 2547") {
				$TRN_STARTDATE = "2004-04-26";
				$TRN_ENDDATE = "2004-04-30";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="26-30 เมษายน  2547") {
				$TRN_STARTDATE = "2004-04-26";
				$TRN_ENDDATE = "2004-04-30";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="26-30 เม.ย. 47") {
				$TRN_STARTDATE = "2004-04-26";
				$TRN_ENDDATE = "2004-04-30";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="26-30 มี.ค. 44") {
				$TRN_STARTDATE = "2001-03-26";
				$TRN_ENDDATE = "2001-03-30";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="26-30 ม.ค.47") {
				$TRN_STARTDATE = "2004-01-26";
				$TRN_ENDDATE = "2004-01-30";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="26-30 ม.ค. 52") {
				$TRN_STARTDATE = "2009-01-26";
				$TRN_ENDDATE = "2009-01-30";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="26-30 พ.ค. 40") {
				$TRN_STARTDATE = "1997-05-26";
				$TRN_ENDDATE = "1997-05-30";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="26-30 พ.ค. 2-6 มิ.ย.51") {
				$TRN_STARTDATE = "2008-05-26";
				$TRN_ENDDATE = "2008-06-06";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="26-29 สิงหาคม 2551") {
				$TRN_STARTDATE = "2008-08-26";
				$TRN_ENDDATE = "2008-08-29";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="26-29 ส.ค. 40") {
				$TRN_STARTDATE = "1997-08-26";
				$TRN_ENDDATE = "1997-08-29";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="26-29 มิ.ย.2549") {
				$TRN_STARTDATE = "2006-06-26";
				$TRN_ENDDATE = "2006-06-29";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="26-29 กรกฎาคม 2547") {
				$TRN_STARTDATE = "2006-07-26";
				$TRN_ENDDATE = "2006-07-29";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="26-29 ก.ค.2548") {
				$TRN_STARTDATE = "2007-07-26";
				$TRN_ENDDATE = "2007-07-29";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="26-29 ก.ค. 47") {
				$TRN_STARTDATE = "2006-07-26";
				$TRN_ENDDATE = "2006-07-29";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="26-28 เมษายน 2547") {
				$TRN_STARTDATE = "2006-04-26";
				$TRN_ENDDATE = "2006-04-28";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="26-28 เม.ย. 47") {
				$TRN_STARTDATE = "2004-04-26";
				$TRN_ENDDATE = "2004-04-28";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="26-28 มีนาคม 2552") {
				$TRN_STARTDATE = "2009-03-26";
				$TRN_ENDDATE = "2009-03-28";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="26-28 มี.ค.39") {
				$TRN_STARTDATE = "1996-03-26";
				$TRN_ENDDATE = "1996-03-28";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="26-28 ม.ค.  2552 ") {
				$TRN_STARTDATE = "2009-01-26";
				$TRN_ENDDATE = "2009-01-28";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="26-28 พ.ค. 40") {
				$TRN_STARTDATE = "1997-05-26";
				$TRN_ENDDATE = "1997-05-28";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="26-28 พ.ค. 39") {
				$TRN_STARTDATE = "1996-05-26";
				$TRN_ENDDATE = "1996-05-28";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="26-28 ก.ย. 51") {
				$TRN_STARTDATE = "2008-09-26";
				$TRN_ENDDATE = "2008-09-28";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="26-28  กรกฎาคม 2549") {
				$TRN_STARTDATE = "2006-07-26";
				$TRN_ENDDATE = "2006-07-28";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="26-27 มีนาคม 2552") {
				$TRN_STARTDATE = "2009-03-26";
				$TRN_ENDDATE = "2009-03-27";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="26-27 มิถุนายน 2551") {
				$TRN_STARTDATE = "2008-06-26";
				$TRN_ENDDATE = "2008-06-27";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="26-27 มิ.ย. 40") {
				$TRN_STARTDATE = "1997-06-26";
				$TRN_ENDDATE = "1997-06-27";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="26-27 ธ.ค.2548") {
				$TRN_STARTDATE = "2005-12-26";
				$TRN_ENDDATE = "2005-12-27";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="26-27 ธ.ค. 39") {
				$TRN_STARTDATE = "1996-12-26";
				$TRN_ENDDATE = "1996-12-27";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="26-27 ต.ค. 39") {
				$TRN_STARTDATE = "1996-10-26";
				$TRN_ENDDATE = "1996-10-27";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="2-6, 9-10 มีนาคม 2552") {
				$TRN_STARTDATE = "2009-03-02";
				$TRN_ENDDATE = "2009-03-10";
				$TRN_DAY = 7;
			} elseif ($TRN_REMARK=="26 ส.ค.41") {
				$TRN_STARTDATE = "1998-08-26";
				$TRN_ENDDATE = "1998-08-26";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="2-6 มี.ค. 52") {
				$TRN_STARTDATE = "2009-03-02";
				$TRN_ENDDATE = "2009-03-06";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="2-6 มี.ค. 42") {
				$TRN_STARTDATE = "1999-03-02";
				$TRN_ENDDATE = "1999-03-06";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="26 มี.ค. 42") {
				$TRN_STARTDATE = "1999-03-26";
				$TRN_ENDDATE = "1999-03-26";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="26 ม.ค.-29 ก.พ.43") {
				$TRN_STARTDATE = "2000-01-26";
				$TRN_ENDDATE = "2000-02-29";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="26 พ.ค.-16 ก.ค.42") {
				$TRN_STARTDATE = "1999-05-26";
				$TRN_ENDDATE = "1999-07-16";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="26 ธันวาคม 2550") {
				$TRN_STARTDATE = "2007-12-26";
				$TRN_ENDDATE = "2007-12-26";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="26 กันยายน 2551") {
				$TRN_STARTDATE = "2008-09-26";
				$TRN_ENDDATE = "2008-09-26";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="2-6 กรกฎาคม 2550") {
				$TRN_STARTDATE = "2007-07-02";
				$TRN_ENDDATE = "2007-07-06";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="2-6 ก.ย.45") {
				$TRN_STARTDATE = "2002-09-02";
				$TRN_ENDDATE = "2002-09-06";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="26 ก.ย. 47") {
				$TRN_STARTDATE = "2004-09-26";
				$TRN_ENDDATE = "2004-09-26";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="2-6 ก.พ. 52") {
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
			} elseif ($TRN_REMARK=="25-29 สิงหาคม 2551") {
				$TRN_STARTDATE = "2008-08-25";
				$TRN_ENDDATE = "2008-08-29";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="25-29 ส.ค. 40") {
				$TRN_STARTDATE = "1997-08-25";
				$TRN_ENDDATE = "1997-08-29";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="25-29 เมษายน 2548") {
				$TRN_STARTDATE = "2005-04-25";
				$TRN_ENDDATE = "2005-04-29";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="25-29 เม.ย. 40") {
				$TRN_STARTDATE = "1997-04-25";
				$TRN_ENDDATE = "1997-04-29";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="25-29 มี.ค. 40") {
				$TRN_STARTDATE = "1997-03-25";
				$TRN_ENDDATE = "1997-03-29";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="25-29 ม.ค.41") {
				$TRN_STARTDATE = "1998-01-25";
				$TRN_ENDDATE = "1998-01-29";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="25-29 พ.ย.46") {
				$TRN_STARTDATE = "2003-11-25";
				$TRN_ENDDATE = "2003-11-29";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="25-29 พ.ย.45") {
				$TRN_STARTDATE = "2002-11-25";
				$TRN_ENDDATE = "2002-11-29";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="25-29 พ.ค. 52") {
				$TRN_STARTDATE = "2009-05-25";
				$TRN_ENDDATE = "2009-05-29";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="25-29 กรกฎาคม 2548") {
				$TRN_STARTDATE = "2005-07-25";
				$TRN_ENDDATE = "2005-07-29";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="25-29 ก.ย.43") {
				$TRN_STARTDATE = "2000-09-25";
				$TRN_ENDDATE = "2000-09-29";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="25-28 ส.ค. 41") {
				$TRN_STARTDATE = "1998-08-25";
				$TRN_ENDDATE = "1998-08-28";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="25-28 เมษายน 2548") {
				$TRN_STARTDATE = "2005-04-25";
				$TRN_ENDDATE = "2005-04-28";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="25-28 มิ.ย. 45") {
				$TRN_STARTDATE = "2002-06-25";
				$TRN_ENDDATE = "2002-06-28";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="25-27 เมษายน 2548") {
				$TRN_STARTDATE = "2005-04-25";
				$TRN_ENDDATE = "2005-04-27";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="25-27 มีนาคม 2547") {
				$TRN_STARTDATE = "2004-03-25";
				$TRN_ENDDATE = "2004-03-27";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="25-27 มี.ค. 47") {
				$TRN_STARTDATE = "2004-03-25";
				$TRN_ENDDATE = "2004-03-27";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="25-27 พฤษภาคม 2548") {
				$TRN_STARTDATE = "2005-05-25";
				$TRN_ENDDATE = "2005-05-27";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="25-27 พ.ค.42") {
				$TRN_STARTDATE = "1999-05-25";
				$TRN_ENDDATE = "1999-05-27";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="25-27 พ.ค. 52") {
				$TRN_STARTDATE = "2009-05-25";
				$TRN_ENDDATE = "2009-05-27";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="25-27 พ.ค. 48") {
				$TRN_STARTDATE = "2005-05-25";
				$TRN_ENDDATE = "2005-05-27";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="25-27 พ.ค. 47") {
				$TRN_STARTDATE = "2004-05-25";
				$TRN_ENDDATE = "2004-05-27";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="25-27 ธ.ค.2549") {
				$TRN_STARTDATE = "2006-12-25";
				$TRN_ENDDATE = "2006-12-27";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="25-27 ก.พ.47") {
				$TRN_STARTDATE = "2004-02-25";
				$TRN_ENDDATE = "2004-02-27";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="25-26 มิ.ย. 52") {
				$TRN_STARTDATE = "2009-06-25";
				$TRN_ENDDATE = "2009-06-26";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="25-26 พ.ย. 39") {
				$TRN_STARTDATE = "1996-11-25";
				$TRN_ENDDATE = "1996-11-26";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="25 สิงหาคม 2550") {
				$TRN_STARTDATE = "2007-08-25";
				$TRN_ENDDATE = "2007-08-25";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="25 ส.ค.-11 ก.ย. 51") {
				$TRN_STARTDATE = "2008-08-25";
				$TRN_ENDDATE = "2008-09-11";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="25 มี.ค.-1 เม.ย.42") {
				$TRN_STARTDATE = "1999-03-25";
				$TRN_ENDDATE = "1999-04-01";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="25 มิ.ย.-26 ส.ค.41") {
				$TRN_STARTDATE = "1998-06-25";
				$TRN_ENDDATE = "1998-08-26";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="2-5 มิ.ย. 52") {
				$TRN_STARTDATE = "2009-06-02";
				$TRN_ENDDATE = "2009-06-05";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="2-5 มิ.ย. 40") {
				$TRN_STARTDATE = "1997-06-02";
				$TRN_ENDDATE = "1997-06-05";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="25 ม.ค.-18 มี.ค. 40") {
				$TRN_STARTDATE = "1997-01-25";
				$TRN_ENDDATE = "1997-03-18";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="25 พ.ย.-1 ธ.ค.43") {
				$TRN_STARTDATE = "2000-11-25";
				$TRN_ENDDATE = "2000-12-01";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="25 พ.ค.-8 มิ.ย. 40") {
				$TRN_STARTDATE = "1997-05-25";
				$TRN_ENDDATE = "1997-06-08";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="25 ต.ค.38-19ม.ค.39") {
				$TRN_STARTDATE = "1995-10-25";
				$TRN_ENDDATE = "1996-01-19";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="25 กรกฎาคม 2552") {
				$TRN_STARTDATE = "2009-07-25";
				$TRN_ENDDATE = "2009-07-25";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="2-5 ก.ย. 40") {
				$TRN_STARTDATE = "1997-09-02";
				$TRN_ENDDATE = "1997-09-05";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="25 ก.ค.-21 ก.ย. 44") {
				$TRN_STARTDATE = "2001-07-25";
				$TRN_ENDDATE = "2001-09-21";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="24-28 เม.ย.43") {
				$TRN_STARTDATE = "2000-04-24";
				$TRN_ENDDATE = "2000-04-28";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="24-28 เม.ย. 43") {
				$TRN_STARTDATE = "2000-04-24";
				$TRN_ENDDATE = "2000-04-28";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="24-28 ม.ค.48") {
				$TRN_STARTDATE = "2005-01-24";
				$TRN_ENDDATE = "2005-01-28";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="24-28 พ.ค. 42") {
				$TRN_STARTDATE = "1999-05-24";
				$TRN_ENDDATE = "1999-05-28";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="24-28 ก.ย. 44") {
				$TRN_STARTDATE = "2001-09-24";
				$TRN_ENDDATE = "2001-09-28";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="24-27 สิงหาคม 2547") {
				$TRN_STARTDATE = "2004-08-24";
				$TRN_ENDDATE = "2004-08-27";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="24-27 ส.ค.2547") {
				$TRN_STARTDATE = "2004-08-24";
				$TRN_ENDDATE = "2004-08-27";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="24-27 ส.ค. 47") {
				$TRN_STARTDATE = "2004-08-24";
				$TRN_ENDDATE = "2004-08-27";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="24-27 เม.ย. 44") {
				$TRN_STARTDATE = "2001-04-24";
				$TRN_ENDDATE = "2001-04-27";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="24-27 มีนาคม 2551") {
				$TRN_STARTDATE = "2008-03-24";
				$TRN_ENDDATE = "2008-03-27";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="24-27 ม.ค. 48") {
				$TRN_STARTDATE = "2005-01-24";
				$TRN_ENDDATE = "2005-01-27";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="24-27 พฤษภาคม 2548") {
				$TRN_STARTDATE = "2005-05-24";
				$TRN_ENDDATE = "2005-05-27";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="24-27 พ.ย. 47") {
				$TRN_STARTDATE = "2004-11-24";
				$TRN_ENDDATE = "2004-11-27";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="24-26 เมษายน 2549") {
				$TRN_STARTDATE = "2006-04-24";
				$TRN_ENDDATE = "2006-04-26";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="24-26 มีนาคม 2552") {
				$TRN_STARTDATE = "2009-03-24";
				$TRN_ENDDATE = "2009-03-26";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="24-26 มิ.ย. 52") {
				$TRN_STARTDATE = "2009-06-24";
				$TRN_ENDDATE = "2009-06-26";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="24-26 พ.ย.48") {
				$TRN_STARTDATE = "2005-11-24";
				$TRN_ENDDATE = "2005-11-26";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="24-26 พ.ย. 51") {
				$TRN_STARTDATE = "2008-11-24";
				$TRN_ENDDATE = "2008-11-26";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="24-25 ส.ค. 43") {
				$TRN_STARTDATE = "2000-08-24";
				$TRN_ENDDATE = "2000-08-25";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="24-25 มี.ค. 48") {
				$TRN_STARTDATE = "2005-03-24";
				$TRN_ENDDATE = "2005-03-25";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="24-25 มิ.ย.42") {
				$TRN_STARTDATE = "1999-06-24";
				$TRN_ENDDATE = "1999-06-25";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="24-25 ม.ค.43") {
				$TRN_STARTDATE = "2000-01-24";
				$TRN_ENDDATE = "2000-01-25";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="24-25 พฤษภาคม ,1,3-4 มิถุนายน 2547") {
				$TRN_STARTDATE = "2004-05-24";
				$TRN_ENDDATE = "2004-06-04";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="24-25 พฤศจิกายน 2550") {
				$TRN_STARTDATE = "2007-11-24";
				$TRN_ENDDATE = "2007-11-25";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="24-25 พ.ค. และ 1, 3-4 มิ.ย. 47") {
				$TRN_STARTDATE = "2004-05-24";
				$TRN_ENDDATE = "2004-06-04";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="24-25 พ.ค. 47") {
				$TRN_STARTDATE = "2004-05-24";
				$TRN_ENDDATE = "2004-05-25";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="24-25 ธ.ค.41") {
				$TRN_STARTDATE = "1998-12-24";
				$TRN_ENDDATE = "1998-12-25";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="24-25 กันยายน 2552") {
				$TRN_STARTDATE = "2009-09-24";
				$TRN_ENDDATE = "2009-09-25";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="2-4,6 พฤษภาคม 2548") {
				$TRN_STARTDATE = "2005-05-02";
				$TRN_ENDDATE = "2005-05-06";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="2-4 ส.ค.42") {
				$TRN_STARTDATE = "1999-08-02";
				$TRN_ENDDATE = "1999-08-04";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="24 เม.ย.-5 พ.ค.2549") {
				$TRN_STARTDATE = "2006-04-24";
				$TRN_ENDDATE = "2006-05-05";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="24 เม.ย.-1 พ.ค.41") {
				$TRN_STARTDATE = "1998-04-24";
				$TRN_ENDDATE = "1998-05-01";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="24 มีนาคม-3 เมษายน 2551") {
				$TRN_STARTDATE = "2008-03-24";
				$TRN_ENDDATE = "2008-04-03";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="24 มี.ค.43") {
				$TRN_STARTDATE = "2000-03-24";
				$TRN_ENDDATE = "2000-03-24";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="2-4 มี.ค.2548") {
				$TRN_STARTDATE = "2005-03-02";
				$TRN_ENDDATE = "2005-03-04";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="2-4 มิ.ย. 40") {
				$TRN_STARTDATE = "1997-06-02";
				$TRN_ENDDATE = "1997-06-04";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="24 ม.ค.-4 พ.ค. 37") {
				$TRN_STARTDATE = "1994-01-24";
				$TRN_ENDDATE = "1994-05-04";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="2-4 พฤษภาคม 2548") {
				$TRN_STARTDATE = "2005-05-02";
				$TRN_ENDDATE = "2005-05-04";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="24 พฤษภาคม - 3 มิถุนายน 2548") {
				$TRN_STARTDATE = "2005-05-24";
				$TRN_ENDDATE = "2005-06-03";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="24 พ.ย.,1,8 ธ.ค. 39") {
				$TRN_STARTDATE = "1996-11-24";
				$TRN_ENDDATE = "1996-12-08";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="24 พ.ค.50") {
				$TRN_STARTDATE = "2007-05-24";
				$TRN_ENDDATE = "2007-05-24";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="24 พ.ค.42") {
				$TRN_STARTDATE = "1999-05-24";
				$TRN_ENDDATE = "1999-05-24";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="2-4 พ.ค. 48") {
				$TRN_STARTDATE = "2005-05-02";
				$TRN_ENDDATE = "2005-05-04";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="2-4 กุมภาพันธ์ 2548") {
				$TRN_STARTDATE = "2005-02-02";
				$TRN_ENDDATE = "2005-02-04";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="24 กันยายน 2551") {
				$TRN_STARTDATE = "2008-09-24";
				$TRN_ENDDATE = "2008-09-24";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="24 ก.พ.47") {
				$TRN_STARTDATE = "2004-02-24";
				$TRN_ENDDATE = "2004-02-24";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="2-4 ก.พ. 52") {
				$TRN_STARTDATE = "2009-02-02";
				$TRN_ENDDATE = "2009-02-04";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="24 ก.ค.-11 ก.ย. 44") {
				$TRN_STARTDATE = "2001-07-24";
				$TRN_ENDDATE = "2001-09-11";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="2-4 ก.ค. 40") {
				$TRN_STARTDATE = "1997-07-02";
				$TRN_ENDDATE = "1997-07-04";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="2-4  พฤษภาคม  2549") {
				$TRN_STARTDATE = "2006-05-02";
				$TRN_ENDDATE = "2006-05-04";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="24 - 27 ส.ค. 2546") {
				$TRN_STARTDATE = "2003-08-24";
				$TRN_ENDDATE = "2003-08-27";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="23-27 ส.ค. 47") {
				$TRN_STARTDATE = "2004-08-23";
				$TRN_ENDDATE = "2004-08-27";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="23-27 เม.ย. 44") {
				$TRN_STARTDATE = "2001-04-23";
				$TRN_ENDDATE = "2001-04-27";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="23-27 มีนาคม 2552") {
				$TRN_STARTDATE = "2009-03-23";
				$TRN_ENDDATE = "2009-03-27";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="23-27 มิ.ย. 40") {
				$TRN_STARTDATE = "1997-06-23";
				$TRN_ENDDATE = "1997-06-27";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="23-27 กุมภาพันธ์ 2552") {
				$TRN_STARTDATE = "2009-02-23";
				$TRN_ENDDATE = "2009-02-27";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="23-27 กรกฎาคม 2550") {
				$TRN_STARTDATE = "2007-07-23";
				$TRN_ENDDATE = "2007-07-27";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="23-27 ก.พ. 47") {
				$TRN_STARTDATE = "2004-02-23";
				$TRN_ENDDATE = "2004-02-27";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="23-27 ก.ค. 42") {
				$TRN_STARTDATE = "1999-07-23";
				$TRN_ENDDATE = "1999-07-27";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="23-26 สิงหาคม  2547") {
				$TRN_STARTDATE = "2004-08-23";
				$TRN_ENDDATE = "2004-08-26";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="23-26 ส.ค.2547") {
				$TRN_STARTDATE = "2004-08-23";
				$TRN_ENDDATE = "2004-08-26";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="23-26 ส.ค. 47") {
				$TRN_STARTDATE = "2004-08-23";
				$TRN_ENDDATE = "2004-08-26";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="23-26 มี.ค.2547") {
				$TRN_STARTDATE = "2004-03-23";
				$TRN_ENDDATE = "2004-03-26";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="23-25 ส.ค.43") {
				$TRN_STARTDATE = "2000-08-23";
				$TRN_ENDDATE = "2000-08-25";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="23-25 ส.ค.2549") {
				$TRN_STARTDATE = "2006-08-23";
				$TRN_ENDDATE = "2006-08-25";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="23-25 เม.ย. 44") {
				$TRN_STARTDATE = "2001-04-23";
				$TRN_ENDDATE = "2001-04-25";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="23-25 มี.ค.41") {
				$TRN_STARTDATE = "1998-03-23";
				$TRN_ENDDATE = "1998-03-25";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="23-25 มิ.ย. 40") {
				$TRN_STARTDATE = "1997-06-23";
				$TRN_ENDDATE = "1997-06-25";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="23-25 พ.ย. 47") {
				$TRN_STARTDATE = "2004-11-23";
				$TRN_ENDDATE = "2004-11-25";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="23-25 พ.ย. 44") {
				$TRN_STARTDATE = "2001-11-23";
				$TRN_ENDDATE = "2001-11-25";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="23-25 ก.พ.42") {
				$TRN_STARTDATE = "1999-02-23";
				$TRN_ENDDATE = "1999-02-25";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="23-24 มี.ค.2548") {
				$TRN_STARTDATE = "2005-03-23";
				$TRN_ENDDATE = "2005-03-24";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="23-24 มี.ค. 52") {
				$TRN_STARTDATE = "2009-03-23";
				$TRN_ENDDATE = "2009-03-24";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="23-24 ธ.ค. 39") {
				$TRN_STARTDATE = "1996-12-23";
				$TRN_ENDDATE = "1996-12-24";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="23-24 ก.พ. 52") {
				$TRN_STARTDATE = "2009-02-23";
				$TRN_ENDDATE = "2009-02-24";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="23,29,30 มี.ค. 40") {
				$TRN_STARTDATE = "1997-03-23";
				$TRN_ENDDATE = "1997-03-30";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="23 ส.ค.-17 ก.ย.47") {
				$TRN_STARTDATE = "2004-08-23";
				$TRN_ENDDATE = "2004-09-17";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="2-3 เมษายน 2552") {
				$TRN_STARTDATE = "2009-04-02";
				$TRN_ENDDATE = "2009-04-03";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="23 มี.ค.48") {
				$TRN_STARTDATE = "2005-03-23";
				$TRN_ENDDATE = "2005-03-23";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="2-3 มี.ค. 52") {
				$TRN_STARTDATE = "2009-03-02";
				$TRN_ENDDATE = "2009-03-03";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="2-3 ธันวาคม 2551") {
				$TRN_STARTDATE = "2008-12-02";
				$TRN_ENDDATE = "2008-12-03";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="23 ธ.ค.2547") {
				$TRN_STARTDATE = "2004-12-23";
				$TRN_ENDDATE = "2004-12-23";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="2-3 ต.ค.40") {
				$TRN_STARTDATE = "1997-10-02";
				$TRN_ENDDATE = "1997-10-03";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="2-3 กันยายน 2552") {
				$TRN_STARTDATE = "2009-09-02";
				$TRN_ENDDATE = "2009-09-03";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="2-3 ก.ย.42") {
				$TRN_STARTDATE = "1999-09-02";
				$TRN_ENDDATE = "1999-09-03";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="23 ก.ย.41") {
				$TRN_STARTDATE = "1998-09-23";
				$TRN_ENDDATE = "1998-09-23";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="23 ก.พ.42") {
				$TRN_STARTDATE = "1999-02-23";
				$TRN_ENDDATE = "1999-02-23";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="23 ก.พ.-21 เม.ย.43") {
				$TRN_STARTDATE = "2000-02-23";
				$TRN_ENDDATE = "2000-04-21";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="2-3 ก.ค.46") {
				$TRN_STARTDATE = "2003-07-02";
				$TRN_ENDDATE = "2003-07-03";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="23 ก.ค.42") {
				$TRN_STARTDATE = "1999-07-23";
				$TRN_ENDDATE = "1999-07-23";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="23 ก.ค.-30 ก.ย.41") {
				$TRN_STARTDATE = "1998-07-23";
				$TRN_ENDDATE = "1998-09-30";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="2-25 พ.ค.43") {
				$TRN_STARTDATE = "2000-05-02";
				$TRN_ENDDATE = "2000-05-25";
				$TRN_DAY = 24;
			} elseif ($TRN_REMARK=="22-27 ต.ค. 44") {
				$TRN_STARTDATE = "2001-10-22";
				$TRN_ENDDATE = "2001-10-27";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="22-26 เม.ย.39") {
				$TRN_STARTDATE = "1996-04-22";
				$TRN_ENDDATE = "1996-04-26";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="22-26 ม.ค.50") {
				$TRN_STARTDATE = "2007-01-22";
				$TRN_ENDDATE = "2007-01-26";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="22-26 ม.ค. 39") {
				$TRN_STARTDATE = "1996-01-22";
				$TRN_ENDDATE = "1996-01-26";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="22-26 พ.ค.43") {
				$TRN_STARTDATE = "2000-05-22";
				$TRN_ENDDATE = "2000-05-26";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="22-26 พ.ค. 43") {
				$TRN_STARTDATE = "2000-05-22";
				$TRN_ENDDATE = "2000-05-26";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="22-26 ธ.ค. 51") {
				$TRN_STARTDATE = "2008-12-22";
				$TRN_ENDDATE = "2008-12-26";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="22-26 กันยายน 2551") {
				$TRN_STARTDATE = "2008-09-22";
				$TRN_ENDDATE = "2008-09-26";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="22-26 ก.ย.51") {
				$TRN_STARTDATE = "2008-09-22";
				$TRN_ENDDATE = "2008-09-26";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="22-26 ก.ค.39") {
				$TRN_STARTDATE = "1996-07-22";
				$TRN_ENDDATE = "1996-07-26";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="22-25 ส.ค. 47") {
				$TRN_STARTDATE = "2004-08-22";
				$TRN_ENDDATE = "2004-08-25";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="22-25 ส.ค. 39") {
				$TRN_STARTDATE = "1996-08-22";
				$TRN_ENDDATE = "1996-08-25";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="22-25 มิถุนายน  2547") {
				$TRN_STARTDATE = "2004-06-22";
				$TRN_ENDDATE = "2004-06-25";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="22-25 มิ.ย. 47") {
				$TRN_STARTDATE = "2004-06-22";
				$TRN_ENDDATE = "2004-06-25";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="22-24 สิงหาคม 2548") {
				$TRN_STARTDATE = "2005-08-22";
				$TRN_ENDDATE = "2005-08-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="22-24 มีนาคม 2547") {
				$TRN_STARTDATE = "2004-03-22";
				$TRN_ENDDATE = "2004-03-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="22-24 มี.ค. 47") {
				$TRN_STARTDATE = "2004-03-22";
				$TRN_ENDDATE = "2004-03-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="22-24 มิถุนายน 2548") {
				$TRN_STARTDATE = "2005-06-22";
				$TRN_ENDDATE = "2005-06-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="22-24 มิ.ย.52") {
				$TRN_STARTDATE = "2008-06-22";
				$TRN_ENDDATE = "2008-06-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="22-24 มิ.ย.41") {
				$TRN_STARTDATE = "1998-06-22";
				$TRN_ENDDATE = "1998-06-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="22-24 มิ.ย. 52") {
				$TRN_STARTDATE = "2009-06-22";
				$TRN_ENDDATE = "2009-06-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="22-24 พ.ค.43") {
				$TRN_STARTDATE = "2000-05-22";
				$TRN_ENDDATE = "2000-05-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="22-24 ธ.ค. 51") {
				$TRN_STARTDATE = "2008-12-22";
				$TRN_ENDDATE = "2008-12-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="22-24 ธ.ค. 40") {
				$TRN_STARTDATE = "1997-12-22";
				$TRN_ENDDATE = "1997-12-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="22-24 กันยายน 2551") {
				$TRN_STARTDATE = "2008-09-22";
				$TRN_ENDDATE = "2008-09-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="22-24 ก.ค.41") {
				$TRN_STARTDATE = "1998-07-22";
				$TRN_ENDDATE = "1998-07-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="22-23 สิงหาคม 2552") {
				$TRN_STARTDATE = "2009-08-22";
				$TRN_ENDDATE = "2009-08-23";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="22-23 เม.ย. 40") {
				$TRN_STARTDATE = "1997-04-22";
				$TRN_ENDDATE = "1997-04-23";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="22-23 มี.ค. 44") {
				$TRN_STARTDATE = "2001-03-22";
				$TRN_ENDDATE = "2001-03-23";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="22-23 พ.ค. 40") {
				$TRN_STARTDATE = "1997-05-22";
				$TRN_ENDDATE = "1997-05-23";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="22-23 ธ.ค.2548") {
				$TRN_STARTDATE = "2005-12-22";
				$TRN_ENDDATE = "2005-12-23";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="22-23 ก.ย.41") {
				$TRN_STARTDATE = "1998-09-22";
				$TRN_ENDDATE = "1998-09-23";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="22-23 ก.ย. 41") {
				$TRN_STARTDATE = "1998-09-22";
				$TRN_ENDDATE = "1998-09-23";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="22-23 ก.ค.47") {
				$TRN_STARTDATE = "2004-07-22";
				$TRN_ENDDATE = "2004-07-23";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="22 ส.ค.-8 ก.ย.43") {
				$TRN_STARTDATE = "2000-08-22";
				$TRN_ENDDATE = "2000-09-08";
				$TRN_DAY = 17;
			} elseif ($TRN_REMARK=="22 มี.ค.-15 มิ.ย. 40") {
				$TRN_STARTDATE = "1997-03-22";
				$TRN_ENDDATE = "1997-06-15";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="22 มี .ค. 45") {
				$TRN_STARTDATE = "2002-03-22";
				$TRN_ENDDATE = "2002-03-22";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="22 มิ.ย.-1 ก.ย.41") {
				$TRN_STARTDATE = "1998-06-22";
				$TRN_ENDDATE = "1998-09-01";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="22 ม.ค. - 2 ก.พ. 44") {
				$TRN_STARTDATE = "2001-01-22";
				$TRN_ENDDATE = "2001-02-02";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="22 พ.ค.-25 ส.ค. 38") {
				$TRN_STARTDATE = "1995-05-22";
				$TRN_ENDDATE = "1995-08-25";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="22 พ.ค.-11 ก.ค. 44") {
				$TRN_STARTDATE = "2001-05-22";
				$TRN_ENDDATE = "2001-07-11";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="22 ก.พ.-11 มี.ค.42") {
				$TRN_STARTDATE = "1999-02-22";
				$TRN_ENDDATE = "1999-03-11";
				$TRN_DAY = 17;
			} elseif ($TRN_REMARK=="22 ก.พ.-10 มี.ค.42") {
				$TRN_STARTDATE = "1999-02-22";
				$TRN_ENDDATE = "1999-03-10";
				$TRN_DAY = 16;
			} elseif ($TRN_REMARK=="22 ก.ค. - 9 ส.ค. 39") {
				$TRN_STARTDATE = "1996-07-22";
				$TRN_ENDDATE = "1996-08-09";
				$TRN_DAY = 18;
			} elseif ($TRN_REMARK=="2-16 ธ.ค.42") {
				$TRN_STARTDATE = "1999-12-02";
				$TRN_ENDDATE = "1999-12-16";
				$TRN_DAY = 15;
			} elseif ($TRN_REMARK=="21-26  กรกฎาคม 2549") {
				$TRN_STARTDATE = "2006-07-21";
				$TRN_ENDDATE = "2006-07-26";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="21-25 เม.ย. 40") {
				$TRN_STARTDATE = "1997-04-21";
				$TRN_ENDDATE = "1997-04-25";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="21-25 มีนาคม 2548") {
				$TRN_STARTDATE = "2005-03-21";
				$TRN_ENDDATE = "2005-03-25";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="21-25 มิ.ย.42") {
				$TRN_STARTDATE = "1999-06-21";
				$TRN_ENDDATE = "1999-06-25";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="21-25 พฤศจิกายน 2548") {
				$TRN_STARTDATE = "2005-11-21";
				$TRN_ENDDATE = "2005-11-25";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="21-25 ก.ย.41") {
				$TRN_STARTDATE = "1998-09-21";
				$TRN_ENDDATE = "1998-09-25";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="21-25 ก.พ.43") {
				$TRN_STARTDATE = "2000-02-21";
				$TRN_ENDDATE = "2000-02-25";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="21-24 ก.พ. 48") {
				$TRN_STARTDATE = "2005-02-21";
				$TRN_ENDDATE = "2005-02-24";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="21-23 เมษายน 2548") {
				$TRN_STARTDATE = "2005-04-21";
				$TRN_ENDDATE = "2005-04-23";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="21-23 มิถุนายน 2547") {
				$TRN_STARTDATE = "2004-06-21";
				$TRN_ENDDATE = "2004-06-23";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="21-23 มิ.ย. 52") {
				$TRN_STARTDATE = "2009-06-21";
				$TRN_ENDDATE = "2009-06-23";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="21-23 มิ.ย. 47") {
				$TRN_STARTDATE = "2004-06-21";
				$TRN_ENDDATE = "2004-06-23";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="21-23 มิ.ย. 39") {
				$TRN_STARTDATE = "1996-06-21";
				$TRN_ENDDATE = "1996-06-23";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="21-23 พ.ค. 40") {
				$TRN_STARTDATE = "1997-05-21";
				$TRN_ENDDATE = "1997-05-23";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="21-23 ธ.ค. 42") {
				$TRN_STARTDATE = "1999-12-21";
				$TRN_ENDDATE = "1999-12-23";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="21-22 สิงหาคม 2551") {
				$TRN_STARTDATE = "2008-08-21";
				$TRN_ENDDATE = "2008-08-22";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="21-22 ก.พ.48") {
				$TRN_STARTDATE = "2005-02-21";
				$TRN_ENDDATE = "2005-02-22";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="2-10 ส.ค.42") {
				$TRN_STARTDATE = "1999-08-02";
				$TRN_ENDDATE = "1999-08-10";
				$TRN_DAY = 9;
			} elseif ($TRN_REMARK=="21/02/2548-21/06/2548") {
				$TRN_STARTDATE = "2005-02-21";
				$TRN_ENDDATE = "2005-06-21";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="21,25-28 ก.ค.43") {
				$TRN_STARTDATE = "2000-07-21";
				$TRN_ENDDATE = "2000-07-28";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="21 เมษายน-1 พฤษภาคม 2551") {
				$TRN_STARTDATE = "2008-04-21";
				$TRN_ENDDATE = "2008-05-01";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="21 มี.ค. 40") {
				$TRN_STARTDATE = "1997-03-21";
				$TRN_ENDDATE = "1997-03-21";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="21 มิ.ย.-2ก.ค.2547") {
				$TRN_STARTDATE = "2004-06-21";
				$TRN_ENDDATE = "2004-07-02";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="21 มิ.ย.-2 ก.ค. 47") {
				$TRN_STARTDATE = "2004-06-21";
				$TRN_ENDDATE = "2004-07-02";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="21 มิ.ย.-18 ส.ค.43") {
				$TRN_STARTDATE = "2000-06-21";
				$TRN_ENDDATE = "2000-08-18";
				$TRN_DAY = 28;
			} elseif ($TRN_REMARK=="21 พ.ค.-15 ส.ค.40") {
				$TRN_STARTDATE = "1997-05-21";
				$TRN_ENDDATE = "1997-08-15";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="21 ก.ค.-2 ก.ย.42") {
				$TRN_STARTDATE = "1999-07-21";
				$TRN_ENDDATE = "1999-09-02";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="20-31 ส.ค. 44") {
				$TRN_STARTDATE = "2001-08-20";
				$TRN_ENDDATE = "2001-08-31";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="20-31 มี.ค.2549") {
				$TRN_STARTDATE = "2006-03-20";
				$TRN_ENDDATE = "2006-03-31";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="20-26 มี.ค. 48") {
				$TRN_STARTDATE = "2005-03-20";
				$TRN_ENDDATE = "2005-03-26";
				$TRN_DAY = 7;
			} elseif ($TRN_REMARK=="20-25 กรกฎาคม 2548") {
				$TRN_STARTDATE = "2005-07-20";
				$TRN_ENDDATE = "2005-07-25";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="20-24 สิงหาคม 2550") {
				$TRN_STARTDATE = "2007-08-20";
				$TRN_ENDDATE = "2007-08-24";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="20-24 เม.ย. 52") {
				$TRN_STARTDATE = "2009-04-20";
				$TRN_ENDDATE = "2009-04-24";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="20-24 มี.ค.43") {
				$TRN_STARTDATE = "2000-03-20";
				$TRN_ENDDATE = "2000-03-24";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="20-24 ก.ย. 47") {
				$TRN_STARTDATE = "2004-09-20";
				$TRN_ENDDATE = "2004-09-24";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="20-24  มิถุนายน 2548") {
				$TRN_STARTDATE = "2005-06-20";
				$TRN_ENDDATE = "2005-06-24";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="20-23 เมษายน 2547") {
				$TRN_STARTDATE = "2004-04-20";
				$TRN_ENDDATE = "2004-04-23";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="20-23 มี.ค. 44") {
				$TRN_STARTDATE = "2001-03-20";
				$TRN_ENDDATE = "2001-03-23";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="20-23 กรกฎาคม 2547") {
				$TRN_STARTDATE = "2004-07-20";
				$TRN_ENDDATE = "2004-07-23";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="20-23 ก.ค. 47") {
				$TRN_STARTDATE = "2004-07-20";
				$TRN_ENDDATE = "2004-07-23";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="20-22 สิงหาคม 2551") {
				$TRN_STARTDATE = "2008-08-20";
				$TRN_ENDDATE = "2008-08-22";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="20-22 เมษายน 2548") {
				$TRN_STARTDATE = "2005-04-20";
				$TRN_ENDDATE = "2005-04-22";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="20-22 มิถุนายน 2550") {
				$TRN_STARTDATE = "2007-06-20";
				$TRN_ENDDATE = "2007-06-22";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="20-22 พ.ค.52") {
				$TRN_STARTDATE = "2009-05-20";
				$TRN_ENDDATE = "2009-05-22";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="20-21 พ.ย. 40") {
				$TRN_STARTDATE = "1997-11-20";
				$TRN_ENDDATE = "1997-11-21";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="20 สิงหาคม 2551") {
				$TRN_STARTDATE = "2008-08-20";
				$TRN_ENDDATE = "2008-08-20";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="20 เม.ย.-1 มิ.ย. 40") {
				$TRN_STARTDATE = "1997-04-20";
				$TRN_ENDDATE = "1997-06-01";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="20 เม.ย. - 1 พ.ค. 52") {
				$TRN_STARTDATE = "2009-04-20";
				$TRN_ENDDATE = "2009-05-01";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="20 มี.ค. 41") {
				$TRN_STARTDATE = "1998-03-20";
				$TRN_ENDDATE = "1998-03-20";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="20 มิ.ย.2548") {
				$TRN_STARTDATE = "2005-06-20";
				$TRN_ENDDATE = "2005-06-20";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="20 มิ.ย.-21 ส.ค.44") {
				$TRN_STARTDATE = "2001-06-20";
				$TRN_ENDDATE = "2001-08-21";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="20 พ.ย. 38-28 ก.พ. 39") {
				$TRN_STARTDATE = "1995-11-20";
				$TRN_ENDDATE = "1996-02-28";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="20 พ.ค. 52") {
				$TRN_STARTDATE = "2009-05-20";
				$TRN_ENDDATE = "2009-05-20";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="20 ต.ค.-7 พ.ย. 40") {
				$TRN_STARTDATE = "1997-10-20";
				$TRN_ENDDATE = "1997-11-07";
				$TRN_DAY = 19;
			} elseif ($TRN_REMARK=="20 ก.ย.43") {
				$TRN_STARTDATE = "2000-09-20";
				$TRN_ENDDATE = "2000-09-20";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="2,9,16 มี.ค. 40") {
				$TRN_STARTDATE = "1997-03-02";
				$TRN_ENDDATE = "1997-03-16";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="2 ส.ค.-17 ก.ย.42") {
				$TRN_STARTDATE = "1999-08-02";
				$TRN_ENDDATE = "1999-09-17";
				$TRN_DAY = 47;
			} elseif ($TRN_REMARK=="2 เม.ย.-14 ก.ค. 35") {
				$TRN_STARTDATE = "1992-04-02";
				$TRN_ENDDATE = "1992-07-14";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="2 มิ.ย.-12 ก.ย. 37") {
				$TRN_STARTDATE = "1994-06-02";
				$TRN_ENDDATE = "1994-09-12";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="2 ธ.ค. 39 - 7 ก.พ. 40") {
				$TRN_STARTDATE = "1996-12-02";
				$TRN_ENDDATE = "1997-02-07";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="2 ก.พ. 52") {
				$TRN_STARTDATE = "2009-02-02";
				$TRN_ENDDATE = "2009-02-02";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="2 ก.ค.42") {
				$TRN_STARTDATE = "1999-07-02";
				$TRN_ENDDATE = "1999-07-02";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="19-30 ม.ค. 52") {
				$TRN_STARTDATE = "2009-01-19";
				$TRN_ENDDATE = "2009-01-30";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="19-28 มิ.ย. 39") {
				$TRN_STARTDATE = "1996-06-19";
				$TRN_ENDDATE = "1996-06-28";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK=="19-23 เมษายน  2547") {
				$TRN_STARTDATE = "2004-04-19";
				$TRN_ENDDATE = "2004-04-23";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="19-23 เม.ย.42") {
				$TRN_STARTDATE = "1999-04-19";
				$TRN_ENDDATE = "1999-04-23";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="19-23 เม.ย. 47") {
				$TRN_STARTDATE = "2004-04-19";
				$TRN_ENDDATE = "2004-04-23";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="19-23 ม.ค. 52") {
				$TRN_STARTDATE = "2009-01-19";
				$TRN_ENDDATE = "2009-01-23";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="19-23 พ.ค. 40") {
				$TRN_STARTDATE = "1997-05-19";
				$TRN_ENDDATE = "1997-05-23";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="19-23 ก.ค.42") {
				$TRN_STARTDATE = "1999-07-19";
				$TRN_ENDDATE = "1999-07-23";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="19-23 ก.ค. 47") {
				$TRN_STARTDATE = "2004-07-19";
				$TRN_ENDDATE = "2004-07-23";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="19-23 ก.ค. 42") {
				$TRN_STARTDATE = "1999-07-19";
				$TRN_ENDDATE = "1999-07-23";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="19-22 กรกฎาคม 2547") {
				$TRN_STARTDATE = "2004-07-19";
				$TRN_ENDDATE = "2004-07-22";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="19-22 ก.ค. 47") {
				$TRN_STARTDATE = "2004-07-19";
				$TRN_ENDDATE = "2004-07-22";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="19-21 ส.ค.42") {
				$TRN_STARTDATE = "1999-08-19";
				$TRN_ENDDATE = "1999-08-21";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="19-21 เม.ย.43") {
				$TRN_STARTDATE = "2000-04-19";
				$TRN_ENDDATE = "2000-04-21";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="19-21 มี.ค.39") {
				$TRN_STARTDATE = "1996-03-19";
				$TRN_ENDDATE = "1996-03-21";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="19-21 ม.ค. 52") {
				$TRN_STARTDATE = "2009-01-19";
				$TRN_ENDDATE = "2009-01-21";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="19-20 ส.ค. 42") {
				$TRN_STARTDATE = "1999-08-19";
				$TRN_ENDDATE = "1999-08-20";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="19-20 เม.ย.43") {
				$TRN_STARTDATE = "2000-04-19";
				$TRN_ENDDATE = "2000-04-20";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="19-20 มี.ค.52") {
				$TRN_STARTDATE = "2009-03-19";
				$TRN_ENDDATE = "2009-03-20";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="19-20 กันยายน 2552") {
				$TRN_STARTDATE = "2009-09-19";
				$TRN_ENDDATE = "2009-09-20";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="19 ส.ค.-22 ต.ค.43 (เฉพาะวันอาทิตย์)") {
				$TRN_STARTDATE = "2000-08-19";
				$TRN_ENDDATE = "2000-10-22";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="19 เม.ย.-4 พ.ค.42") {
				$TRN_STARTDATE = "1999-04-19";
				$TRN_ENDDATE = "1999-05-04";
				$TRN_DAY = 16;
			} elseif ($TRN_REMARK=="19 มิ.ย.52") {
				$TRN_STARTDATE = "2009-06-19";
				$TRN_ENDDATE = "2009-06-19";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="19 มิ.ย. 52") {
				$TRN_STARTDATE = "2009-06-19";
				$TRN_ENDDATE = "2009-06-19";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="19 มิ.ย. 43") {
				$TRN_STARTDATE = "2000-06-19";
				$TRN_ENDDATE = "2000-06-19";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="19 ม.ค.-5ก.พ. 52") {
				$TRN_STARTDATE = "2009-01-19";
				$TRN_ENDDATE = "2009-02-05";
				$TRN_DAY = 18;
			} elseif ($TRN_REMARK=="19 ม.ค. 43") {
				$TRN_STARTDATE = "2000-01-19";
				$TRN_ENDDATE = "2000-01-19";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="19 พ.ค. 2547") {
				$TRN_STARTDATE = "2004-05-19";
				$TRN_ENDDATE = "2004-05-19";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="19 ธันวาคม 2551") {
				$TRN_STARTDATE = "2008-12-19";
				$TRN_ENDDATE = "2008-12-19";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="19 ต.ค.-5 พ.ย.42") {
				$TRN_STARTDATE = "1999-10-19";
				$TRN_ENDDATE = "1999-11-05";
				$TRN_DAY = 18;
			} elseif ($TRN_REMARK=="19 ต.ค.-30 พ.ย. 39") {
				$TRN_STARTDATE = "1996-10-19";
				$TRN_ENDDATE = "1996-11-30";
				$TRN_DAY = 43;
			} elseif ($TRN_REMARK=="19 -30 ส.ค. 45") {
				$TRN_STARTDATE = "2002-08-19";
				$TRN_ENDDATE = "2002-08-30";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="18-24 มี.ค. 47") {
				$TRN_STARTDATE = "2004-03-18";
				$TRN_ENDDATE = "2004-03-24";
				$TRN_DAY = 7;
			} elseif ($TRN_REMARK=="18-22 เมษายน 2548") {
				$TRN_STARTDATE = "2005-04-18";
				$TRN_ENDDATE = "2005-04-22";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="18-22 ม.ค.42") {
				$TRN_STARTDATE = "1999-01-18";
				$TRN_ENDDATE = "1999-01-22";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="18-21 ส.ค. 41") {
				$TRN_STARTDATE = "1998-08-18";
				$TRN_ENDDATE = "1998-08-21";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="18-21 พ.ค. 41") {
				$TRN_STARTDATE = "1998-05-18";
				$TRN_ENDDATE = "1998-05-21";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="18-20 สิงหาคม 2552") {
				$TRN_STARTDATE = "2009-08-18";
				$TRN_ENDDATE = "2009-08-20";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="18-20 ส.ค. 47") {
				$TRN_STARTDATE = "2004-08-18";
				$TRN_ENDDATE = "2004-08-20";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="18-20 เมษายน 2548") {
				$TRN_STARTDATE = "2005-04-18";
				$TRN_ENDDATE = "2005-04-20";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="18-20 เม.ย.43") {
				$TRN_STARTDATE = "2000-04-18";
				$TRN_ENDDATE = "2000-04-20";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="18-20 มีนาคม 2547") {
				$TRN_STARTDATE = "2004-03-18";
				$TRN_ENDDATE = "2004-03-20";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="18-20 มี.ค. 47") {
				$TRN_STARTDATE = "2004-03-18";
				$TRN_ENDDATE = "2004-03-20";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="18-20 ม.ค.43") {
				$TRN_STARTDATE = "2000-01-18";
				$TRN_ENDDATE = "2000-01-20";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="18-20 ม.ค. 43") {
				$TRN_STARTDATE = "2000-01-18";
				$TRN_ENDDATE = "2000-01-20";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="18-20 พฤษภาคม 2548") {
				$TRN_STARTDATE = "2005-05-18";
				$TRN_ENDDATE = "2005-05-20";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="18-20 พ.ค.48") {
				$TRN_STARTDATE = "2005-05-18";
				$TRN_ENDDATE = "2005-05-20";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="18-19 และ 25-26 มี.ค. 43") {
				$TRN_STARTDATE = "2000-03-18";
				$TRN_ENDDATE = "2000-03-26";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="18-19 ธ.ค. 40") {
				$TRN_STARTDATE = "1997-12-18";
				$TRN_ENDDATE = "1997-12-19";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="18-19 ก.พ. 52") {
				$TRN_STARTDATE = "2009-02-18";
				$TRN_ENDDATE = "2009-02-19";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="18-19 ก.ค. 44") {
				$TRN_STARTDATE = "2001-07-18";
				$TRN_ENDDATE = "2001-07-19";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="18 ส.ค.-24 ก.ย.42") {
				$TRN_STARTDATE = "1999-08-18";
				$TRN_ENDDATE = "1999-09-24";
				$TRN_DAY = 38;
			} elseif ($TRN_REMARK=="18 ม.ค.-26 ก.พ.42") {
				$TRN_STARTDATE = "1999-01-18";
				$TRN_ENDDATE = "1999-02-26";
				$TRN_DAY = 40;
			} elseif ($TRN_REMARK=="18 ก.ย.-26 ธ.ค.42") {
				$TRN_STARTDATE = "1999-09-18";
				$TRN_ENDDATE = "1999-12-26";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="18 ก.พ.42") {
				$TRN_STARTDATE = "1999-02-18";
				$TRN_ENDDATE = "1999-02-18";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="18 ก.ค.-9 ก.ย. 39") {
				$TRN_STARTDATE = "1996-07-18";
				$TRN_ENDDATE = "1996-09-09";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="18 ก.ค.-6 ก.ย. 44") {
				$TRN_STARTDATE = "2001-07-18";
				$TRN_ENDDATE = "2001-09-06";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="18 ก.ค.-2 ก.ย.2548") {
				$TRN_STARTDATE = "2005-07-18";
				$TRN_ENDDATE = "2005-09-02";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="18 - 23 ส.ค. 2546") {
				$TRN_STARTDATE = "2003-08-18";
				$TRN_ENDDATE = "2003-08-23";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="18 - 20 กรกฎาคม 2548") {
				$TRN_STARTDATE = "2005-07-18";
				$TRN_ENDDATE = "2005-07-20";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="17ม.ค.-8 เม.ย.37") {
				$TRN_STARTDATE = "1994-01-17";
				$TRN_ENDDATE = "1994-04-08";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="17-25 สิงหาคม 2548 ดูงาน 23 - 25 สิงหาคม") {
				$TRN_STARTDATE = "2005-08-17";
				$TRN_ENDDATE = "2005-08-25";
				$TRN_DAY = 9;
			} elseif ($TRN_REMARK=="17-23 มิ.ย.47") {
				$TRN_STARTDATE = "2004-06-17";
				$TRN_ENDDATE = "2004-06-23";
				$TRN_DAY = 7;
			} elseif ($TRN_REMARK=="17-22 ม.ค. 42") {
				$TRN_STARTDATE = "1999-01-17";
				$TRN_ENDDATE = "1999-01-22";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="17-21 พฤษภาคม  2547") {
				$TRN_STARTDATE = "2004-05-17";
				$TRN_ENDDATE = "2004-05-21";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="17-21 พ.ค.42") {
				$TRN_STARTDATE = "1999-05-17";
				$TRN_ENDDATE = "1999-05-21";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="17-21 พ.ค. 47") {
				$TRN_STARTDATE = "2004-05-17";
				$TRN_ENDDATE = "2004-05-21";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="17-20 ส.ค.2547") {
				$TRN_STARTDATE = "2004-08-17";
				$TRN_ENDDATE = "2004-08-20";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="17-20 ส.ค. 47") {
				$TRN_STARTDATE = "2004-08-17";
				$TRN_ENDDATE = "2004-08-20";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="17-20 พฤษภาคม 2548") {
				$TRN_STARTDATE = "2005-05-17";
				$TRN_ENDDATE = "2005-05-20";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="17-20 พ.ย. 47") {
				$TRN_STARTDATE = "2004-11-17";
				$TRN_ENDDATE = "2004-11-20";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="17-19 มี.ค.42") {
				$TRN_STARTDATE = "1999-03-17";
				$TRN_ENDDATE = "1999-03-19";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="17-19 กันยายน 2551") {
				$TRN_STARTDATE = "2008-09-17";
				$TRN_ENDDATE = "2008-09-19";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="17-18 พ.ย.2548") {
				$TRN_STARTDATE = "2005-11-17";
				$TRN_ENDDATE = "2005-11-18";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="17-18 พ.ค.44") {
				$TRN_STARTDATE = "2001-05-17";
				$TRN_ENDDATE = "2001-05-18";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="17-18 ธ.ค.41") {
				$TRN_STARTDATE = "1998-12-17";
				$TRN_ENDDATE = "1998-12-18";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="17-18 ก.พ.2548") {
				$TRN_STARTDATE = "2005-02-17";
				$TRN_ENDDATE = "2005-02-18";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="17,24,31 พ.ค. 40") {
				$TRN_STARTDATE = "1997-05-17";
				$TRN_ENDDATE = "1997-05-31";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="17 ส.ค.43") {
				$TRN_STARTDATE = "2000-08-17";
				$TRN_ENDDATE = "2000-08-17";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="17 ส.ค.-13 ต.ค.41") {
				$TRN_STARTDATE = "1998-08-17";
				$TRN_ENDDATE = "1998-10-13";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="17 มี.ค.-11 เม.ย.40") {
				$TRN_STARTDATE = "1997-03-17";
				$TRN_ENDDATE = "1997-04-11";
				$TRN_DAY = 26;
			} elseif ($TRN_REMARK=="17 ม.ค.43") {
				$TRN_STARTDATE = "2000-01-17";
				$TRN_ENDDATE = "2000-01-17";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="17 กันยายน - 12 ตุลาคม 2550") {
				$TRN_STARTDATE = "2007-09-17";
				$TRN_ENDDATE = "2007-10-12";
				$TRN_DAY = 26;
			} elseif ($TRN_REMARK=="17 ก.ย.2547") {
				$TRN_STARTDATE = "2004-09-17";
				$TRN_ENDDATE = "2004-09-17";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="17 ก.ย. 47") {
				$TRN_STARTDATE = "2004-09-17";
				$TRN_ENDDATE = "2004-09-17";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="17 ก.พ.-9 เม.ย.42") {
				$TRN_STARTDATE = "1999-02-17";
				$TRN_ENDDATE = "1999-04-09";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="16-27 มี.ค. 52") {
				$TRN_STARTDATE = "2009-03-16";
				$TRN_ENDDATE = "2009-03-27";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="16-26 มิ.ย.41") {
				$TRN_STARTDATE = "1998-06-16";
				$TRN_ENDDATE = "1998-06-26";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="16-26 มกราคม 2549") {
				$TRN_STARTDATE = "2006-01-16";
				$TRN_ENDDATE = "2006-01-26";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="16-26 ก.ย. 51") {
				$TRN_STARTDATE = "2008-09-16";
				$TRN_ENDDATE = "2008-09-26";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="16-23 มี.ค.42") {
				$TRN_STARTDATE = "1999-03-16";
				$TRN_ENDDATE = "1999-03-23";
				$TRN_DAY = 8;
			} elseif ($TRN_REMARK=="16-22 ก.พ. 40") {
				$TRN_STARTDATE = "1997-02-16";
				$TRN_ENDDATE = "1997-02-22";
				$TRN_DAY = 7;
			} elseif ($TRN_REMARK=="16-20, 23-24 กุมภาพันธ์ 2552") {
				$TRN_STARTDATE = "2009-02-16";
				$TRN_ENDDATE = "2009-02-24";
				$TRN_DAY = 7;
			} elseif ($TRN_REMARK=="16-20 มีนาคม 2552") {
				$TRN_STARTDATE = "2009-03-16";
				$TRN_ENDDATE = "2009-03-20";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="16-20 มี.ค. 47") {
				$TRN_STARTDATE = "2004-03-16";
				$TRN_ENDDATE = "2004-03-20";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="16-20 พ.ค. 48") {
				$TRN_STARTDATE = "2005-05-16";
				$TRN_ENDDATE = "2005-05-20";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="16-20 ธ.ค. 39") {
				$TRN_STARTDATE = "1996-12-16";
				$TRN_ENDDATE = "1996-12-20";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="16-20 กุมภาพันธ์ 2552") {
				$TRN_STARTDATE = "2009-02-16";
				$TRN_ENDDATE = "2009-02-20";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="16-20 กรกฎาคม 2550") {
				$TRN_STARTDATE = "2007-07-16";
				$TRN_ENDDATE = "2007-07-20";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="16-20 ก.พ.47") {
				$TRN_STARTDATE = "2004-02-16";
				$TRN_ENDDATE = "2004-02-20";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="16-20 ก.พ.41") {
				$TRN_STARTDATE = "1998-02-16";
				$TRN_ENDDATE = "1998-02-20";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="16-20 ก.ค. 44") {
				$TRN_STARTDATE = "2001-07-16";
				$TRN_ENDDATE = "2001-07-20";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="16-20  มีนาคม 2547") {
				$TRN_STARTDATE = "2004-03-16";
				$TRN_ENDDATE = "2004-03-20";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="16-19 สิงหาคม  2547") {
				$TRN_STARTDATE = "2004-08-16";
				$TRN_ENDDATE = "2004-08-19";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="16-19 ส.ค. 47") {
				$TRN_STARTDATE = "2004-08-16";
				$TRN_ENDDATE = "2004-08-19";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="16-19 มี.ค.2547") {
				$TRN_STARTDATE = "2004-03-16";
				$TRN_ENDDATE = "2004-03-19";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="16-19 มิ.ย. 52") {
				$TRN_STARTDATE = "2009-06-16";
				$TRN_ENDDATE = "2009-06-19";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="16-19 พฤษภาคม 2548") {
				$TRN_STARTDATE = "2005-05-16";
				$TRN_ENDDATE = "2005-05-19";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="16-19 พ.ค.2548") {
				$TRN_STARTDATE = "2005-05-16";
				$TRN_ENDDATE = "2005-05-19";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="16-19 ก.พ. 42") {
				$TRN_STARTDATE = "1999-02-16";
				$TRN_ENDDATE = "1999-02-19";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="16-18 ส.ค. 39 *") {
				$TRN_STARTDATE = "1996-08-16";
				$TRN_ENDDATE = "1996-08-18";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="16-18 มิ.ย. 40") {
				$TRN_STARTDATE = "1997-06-16";
				$TRN_ENDDATE = "1997-06-18";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="16-18 พ.ย. 44") {
				$TRN_STARTDATE = "2001-11-16";
				$TRN_ENDDATE = "2001-11-18";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="16-18 ก.พ.48") {
				$TRN_STARTDATE = "2005-02-16";
				$TRN_ENDDATE = "2005-02-18";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="16-18 ก.ค. 40") {
				$TRN_STARTDATE = "1997-07-16";
				$TRN_ENDDATE = "1997-07-18";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="16-17 สิงหาคม 2552") {
				$TRN_STARTDATE = "2009-08-16";
				$TRN_ENDDATE = "2009-08-17";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="16-17 และ 20-25   ธ.ค.42") {
				$TRN_STARTDATE = "1999-12-16";
				$TRN_ENDDATE = "1999-12-25";
				$TRN_DAY = 8;
			} elseif ($TRN_REMARK=="16-17 มีนาคม 2552") {
				$TRN_STARTDATE = "2009-03-16";
				$TRN_ENDDATE = "2009-03-17";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="16-17 มี.ค.43") {
				$TRN_STARTDATE = "2000-03-16";
				$TRN_ENDDATE = "2000-03-17";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="16-17 มี.ค.42") {
				$TRN_STARTDATE = "1999-03-16";
				$TRN_ENDDATE = "1999-03-17";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="16-17 มี.ค. 52") {
				$TRN_STARTDATE = "2009-03-16";
				$TRN_ENDDATE = "2009-03-17";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="16-17 พ.ย.2549") {
				$TRN_STARTDATE = "2006-11-16";
				$TRN_ENDDATE = "2006-11-17";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="16-17 ธันวาคม 2551") {
				$TRN_STARTDATE = "2008-12-16";
				$TRN_ENDDATE = "2008-12-17";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="16-17 ต.ค. 40") {
				$TRN_STARTDATE = "1997-10-16";
				$TRN_ENDDATE = "1997-10-17";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="16 มี.ค.48") {
				$TRN_STARTDATE = "2005-03-16";
				$TRN_ENDDATE = "2005-03-16";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="16 มี.ค.40") {
				$TRN_STARTDATE = "1997-03-16";
				$TRN_ENDDATE = "1997-03-16";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="16 มิ.ย.-2 ก.ค. 40") {
				$TRN_STARTDATE = "1997-06-16";
				$TRN_ENDDATE = "1997-07-02";
				$TRN_DAY = 17;
			} elseif ($TRN_REMARK=="16 ม.ค.-6 มี.ค.44") {
				$TRN_STARTDATE = "2001-01-16";
				$TRN_ENDDATE = "2001-03-06";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="16 ม.ค.-21 เม.ย. 38") {
				$TRN_STARTDATE = "1995-01-16";
				$TRN_ENDDATE = "1995-04-21";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="16 ม.ค.-11 เม.ย.38") {
				$TRN_STARTDATE = "1995-01-16";
				$TRN_ENDDATE = "1995-04-11";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="16 ม.ค. 47") {
				$TRN_STARTDATE = "2004-01-16";
				$TRN_ENDDATE = "2004-01-16";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="16 พ.ค.-5 ส.ค.37") {
				$TRN_STARTDATE = "1994-05-16";
				$TRN_ENDDATE = "1994-08-05";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="16 ก.พ.-3 มี.ค.41") {
				$TRN_STARTDATE = "1998-02-16";
				$TRN_ENDDATE = "1998-03-03";
				$TRN_DAY = 16;
			} elseif ($TRN_REMARK=="16 ก.พ.-12 เม.ย.43") {
				$TRN_STARTDATE = "2000-02-16";
				$TRN_ENDDATE = "2000-04-12";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="16 ก.ค.-8 ต.ค.36") {
				$TRN_STARTDATE = "1993-07-16";
				$TRN_ENDDATE = "1993-10-08";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="16 ก.ค.-11 ก.ย.41") {
				$TRN_STARTDATE = "1998-07-16";
				$TRN_ENDDATE = "1998-09-11";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="16 - 18 ส.ค.49") {
				$TRN_STARTDATE = "2006-08-16";
				$TRN_ENDDATE = "2006-08-18";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="15-26 สิงหาคม 2548") {
				$TRN_STARTDATE = "2005-08-15";
				$TRN_ENDDATE = "2005-08-26";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="15-26 ส.ค.2548") {
				$TRN_STARTDATE = "2005-08-15";
				$TRN_ENDDATE = "2005-08-26";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="15-26 ม.ค.2550") {
				$TRN_STARTDATE = "2007-01-15";
				$TRN_ENDDATE = "2007-01-26";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="15-26 พ.ย.42") {
				$TRN_STARTDATE = "1999-11-15";
				$TRN_ENDDATE = "1999-11-26";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="15-26 ธ.ค.52") {
				$TRN_STARTDATE = "2009-12-15";
				$TRN_ENDDATE = "2009-12-26";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="15-26 ก.พ.42") {
				$TRN_STARTDATE = "1999-02-15";
				$TRN_ENDDATE = "1999-02-26";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="15-19 มี.ค. 47") {
				$TRN_STARTDATE = "2004-03-15";
				$TRN_ENDDATE = "2004-03-19";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="15-19 มิ.ย. 52") {
				$TRN_STARTDATE = "2009-06-15";
				$TRN_ENDDATE = "2009-06-19";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="15-19 กันยายน 2551") {
				$TRN_STARTDATE = "2008-09-15";
				$TRN_ENDDATE = "2008-09-19";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="15-19  มีนาคม 2547") {
				$TRN_STARTDATE = "2004-03-15";
				$TRN_ENDDATE = "2004-03-19";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="15-18,27  มีนาคม 2547") {
				$TRN_STARTDATE = "2004-03-15";
				$TRN_ENDDATE = "2004-03-27";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="15-18 มิถุนายน  2547") {
				$TRN_STARTDATE = "2004-06-15";
				$TRN_ENDDATE = "2004-06-18";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="15-18 มิ.ย. 47") {
				$TRN_STARTDATE = "2004-06-15";
				$TRN_ENDDATE = "2004-06-18";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="15-18 มิ.ย. 42") {
				$TRN_STARTDATE = "1999-06-15";
				$TRN_ENDDATE = "1999-06-18";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="15-18 มิ.ย. 41") {
				$TRN_STARTDATE = "1998-06-15";
				$TRN_ENDDATE = "1998-06-18";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="15-18 ก.พ. 48") {
				$TRN_STARTDATE = "2005-02-15";
				$TRN_ENDDATE = "2005-02-18";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="15-17 มีนาคม 2547") {
				$TRN_STARTDATE = "2004-03-15";
				$TRN_ENDDATE = "2004-03-17";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="15-17 มี.ค. 47") {
				$TRN_STARTDATE = "2004-03-15";
				$TRN_ENDDATE = "2004-03-17";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="15-17 มิ.ย.52") {
				$TRN_STARTDATE = "2009-06-15";
				$TRN_ENDDATE = "2009-06-17";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="15-17 มิ.ย.41") {
				$TRN_STARTDATE = "1998-06-15";
				$TRN_ENDDATE = "1998-06-17";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="15-17 มกราคม 2551") {
				$TRN_STARTDATE = "2008-01-15";
				$TRN_ENDDATE = "2008-01-17";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="15-17 พ.ค. 52") {
				$TRN_STARTDATE = "2009-05-15";
				$TRN_ENDDATE = "2009-05-17";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="15-16 พ.ค. 40") {
				$TRN_STARTDATE = "1997-05-15";
				$TRN_ENDDATE = "1997-05-16";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="15-16 ธ.ค. 51") {
				$TRN_STARTDATE = "2008-12-15";
				$TRN_ENDDATE = "2008-12-16";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="15-16 กันยายน 2552") {
				$TRN_STARTDATE = "2009-09-15";
				$TRN_ENDDATE = "2009-09-16";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="15,22,29 มี.ค. 40") {
				$TRN_STARTDATE = "1997-03-15";
				$TRN_ENDDATE = "1997-03-29";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="15,22 ก.พ. และ 1 มี.ค. 40") {
				$TRN_STARTDATE = "1997-02-15";
				$TRN_ENDDATE = "1997-03-01";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="15,16,22 ก.พ. 40") {
				$TRN_STARTDATE = "1997-02-15";
				$TRN_ENDDATE = "1997-02-22";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="15 สิงหาคม 2550") {
				$TRN_STARTDATE = "2007-08-15";
				$TRN_ENDDATE = "2007-08-15";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="15 มี.ค.-15 มิ.ย. 40") {
				$TRN_STARTDATE = "1997-03-15";
				$TRN_ENDDATE = "1997-06-15";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="1-5 มี.ค. 47") {
				$TRN_STARTDATE = "2004-03-01";
				$TRN_ENDDATE = "2004-03-05";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="1-5 มิ.ย. 52") {
				$TRN_STARTDATE = "2009-06-01";
				$TRN_ENDDATE = "2009-06-05";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="15 ม.ค.-5 มี.ค.45") {
				$TRN_STARTDATE = "2002-01-15";
				$TRN_ENDDATE = "2002-03-05";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="15 ม.ค.-17 เม.ย.33") {
				$TRN_STARTDATE = "1990-01-15";
				$TRN_ENDDATE = "1990-04-17";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="15 พฤษภาคม 2549") {
				$TRN_STARTDATE = "2006-05-15";
				$TRN_ENDDATE = "2006-05-15";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="15 พ.ค.-15 ส.ค.34") {
				$TRN_STARTDATE = "1991-05-15";
				$TRN_ENDDATE = "1991-08-15";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="15 พ.ค.-11 ส.ค.38") {
				$TRN_STARTDATE = "1995-05-15";
				$TRN_ENDDATE = "1995-08-11";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="15 ธ.ค.47") {
				$TRN_STARTDATE = "2004-12-15";
				$TRN_ENDDATE = "2004-12-15";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="15 ธ.ค.40") {
				$TRN_STARTDATE = "1997-12-15";
				$TRN_ENDDATE = "1997-12-15";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="15 ธ.ค.2547") {
				$TRN_STARTDATE = "2004-12-15";
				$TRN_ENDDATE = "2004-12-15";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="15 ต.ค.2547") {
				$TRN_STARTDATE = "2004-10-15";
				$TRN_ENDDATE = "2004-10-15";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="1-5 กันยายน 2551") {
				$TRN_STARTDATE = "2008-09-01";
				$TRN_ENDDATE = "2008-09-05";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="15 ก.พ.-9 เม.ย.42") {
				$TRN_STARTDATE = "1999-02-15";
				$TRN_ENDDATE = "1999-04-09";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="15 ก.พ.-30 พ.ค. 39") {
				$TRN_STARTDATE = "1996-02-15";
				$TRN_ENDDATE = "1996-05-30";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="15 ก.ค.-26 ส.ค.41 (เฉพาะวันจันทร์และวันพุธ)") {
				$TRN_STARTDATE = "1998-07-15";
				$TRN_ENDDATE = "1998-08-26";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="1-5 ก.ค. 45") {
				$TRN_STARTDATE = "2002-07-01";
				$TRN_ENDDATE = "2002-07-05";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="1-5  มีนาคม 2547") {
				$TRN_STARTDATE = "2004-03-01";
				$TRN_ENDDATE = "2004-03-05";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="14-25 มิ.ย. 40") {
				$TRN_STARTDATE = "1997-06-14";
				$TRN_ENDDATE = "1997-06-25";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK=="14-18 มีนาคม 2548") {
				$TRN_STARTDATE = "2005-03-14";
				$TRN_ENDDATE = "2005-03-18";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="14-18 มิ.ย.42") {
				$TRN_STARTDATE = "1999-06-14";
				$TRN_ENDDATE = "1999-06-18";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="14-18 ต.ค. 39") {
				$TRN_STARTDATE = "1996-10-14";
				$TRN_ENDDATE = "1996-10-18";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="14-18 ก.พ.43") {
				$TRN_STARTDATE = "2000-02-14";
				$TRN_ENDDATE = "2000-02-18";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="14-17 ก.ย. 42") {
				$TRN_STARTDATE = "1999-09-14";
				$TRN_ENDDATE = "1999-09-17";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="14-16 มิถุนายน  2547") {
				$TRN_STARTDATE = "2004-06-14";
				$TRN_ENDDATE = "2004-06-16";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="14-16 มิ.ย. 47") {
				$TRN_STARTDATE = "2004-06-14";
				$TRN_ENDDATE = "2004-06-16";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="14-16 พฤศจิกายน 2548") {
				$TRN_STARTDATE = "2005-11-14";
				$TRN_ENDDATE = "2005-11-16";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="14-16 พ.ค. 40") {
				$TRN_STARTDATE = "1997-05-14";
				$TRN_ENDDATE = "1997-05-16";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="14-16 ธันวาคม 2548") {
				$TRN_STARTDATE = "2005-12-14";
				$TRN_ENDDATE = "2005-12-16";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="14-16 ธ.ค. 47") {
				$TRN_STARTDATE = "2004-12-14";
				$TRN_ENDDATE = "2004-12-16";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="14-16 ต.ค. 39") {
				$TRN_STARTDATE = "1996-10-14";
				$TRN_ENDDATE = "1996-10-16";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="14-16 กันยายน 2548") {
				$TRN_STARTDATE = "2005-09-14";
				$TRN_ENDDATE = "2005-09-16";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="14-16 ก.ค. 40") {
				$TRN_STARTDATE = "1997-07-14";
				$TRN_ENDDATE = "1997-07-16";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="14-15 ม.ค.42") {
				$TRN_STARTDATE = "1999-01-14";
				$TRN_ENDDATE = "1999-01-15";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="14-15 ก.ค. 40") {
				$TRN_STARTDATE = "1997-07-14";
				$TRN_ENDDATE = "1997-07-15";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="1-4,8 มีนาคม 2547") {
				$TRN_STARTDATE = "2004-03-01";
				$TRN_ENDDATE = "2004-03-08";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="1-4, 8 มี.ค. 47") {
				$TRN_STARTDATE = "2004-03-01";
				$TRN_ENDDATE = "2004-03-08";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="1-4 ส.ค. 2543") {
				$TRN_STARTDATE = "2000-08-01";
				$TRN_ENDDATE = "2000-08-04";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="14 มีนาคม-16 มิถุนายน 2549") {
				$TRN_STARTDATE = "2006-03-14";
				$TRN_ENDDATE = "2006-06-16";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="14 มิ.ย.-11 ส.ค.43") {
				$TRN_STARTDATE = "2000-06-14";
				$TRN_ENDDATE = "2000-08-11";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="1-4 พ.ค.43") {
				$TRN_STARTDATE = "2000-05-01";
				$TRN_ENDDATE = "2000-05-04";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="1-4 พ.ค. 43") {
				$TRN_STARTDATE = "2000-05-01";
				$TRN_ENDDATE = "2000-05-04";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="14 พ.ค. - 5 ก.ค. 45") {
				$TRN_STARTDATE = "2002-05-14";
				$TRN_ENDDATE = "2002-07-05";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="1-4 ก.พ.43") {
				$TRN_STARTDATE = "2000-02-01";
				$TRN_ENDDATE = "2000-02-04";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="14 ก.พ.-10 พ.ค.39") {
				$TRN_STARTDATE = "1996-02-14";
				$TRN_ENDDATE = "1996-05-10";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="1-4  มี.ค.47") {
				$TRN_STARTDATE = "2004-03-01";
				$TRN_ENDDATE = "2004-03-04";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="1-4  มี.ค.2547") {
				$TRN_STARTDATE = "2004-03-01";
				$TRN_ENDDATE = "2004-03-04";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="13-30 พ.ค. 39") {
				$TRN_STARTDATE = "1996-05-13";
				$TRN_ENDDATE = "1996-05-30";
				$TRN_DAY = 18;
			} elseif ($TRN_REMARK=="13-29 มิ.ย.43") {
				$TRN_STARTDATE = "2000-06-13";
				$TRN_ENDDATE = "2000-06-29";
				$TRN_DAY = 17;
			} elseif ($TRN_REMARK=="13-28 ม.ค. 40") {
				$TRN_STARTDATE = "1997-01-13";
				$TRN_ENDDATE = "1997-01-28";
				$TRN_DAY = 16;
			} elseif ($TRN_REMARK=="13-24 กุมภาพันธ์ 2549") {
				$TRN_STARTDATE = "2006-02-13";
				$TRN_ENDDATE = "2006-02-24";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="13-22 สิงหาคม 2551") {
				$TRN_STARTDATE = "2008-08-13";
				$TRN_ENDDATE = "2008-08-22";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK=="13-22 ก.ค. 41") {
				$TRN_STARTDATE = "1998-07-13";
				$TRN_ENDDATE = "1998-07-22";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK=="13-19 ก.ค. 40") {
				$TRN_STARTDATE = "1997-07-13";
				$TRN_ENDDATE = "1997-07-19";
				$TRN_DAY = 7;
			} elseif ($TRN_REMARK=="13-18 ก.พ. 43") {
				$TRN_STARTDATE = "2000-02-13";
				$TRN_ENDDATE = "2000-02-18";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="13-17 มี.ค.43") {
				$TRN_STARTDATE = "2000-03-13";
				$TRN_ENDDATE = "2000-03-17";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="13-17 มิถุนายน 2548") {
				$TRN_STARTDATE = "2005-06-13";
				$TRN_ENDDATE = "2005-06-17";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="13-17 ธ.ค. 51") {
				$TRN_STARTDATE = "2008-12-13";
				$TRN_ENDDATE = "2008-12-17";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="13-17 กรกฎาคม 2552") {
				$TRN_STARTDATE = "2009-07-13";
				$TRN_ENDDATE = "2009-07-17";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="13-17 ก.ย. 47") {
				$TRN_STARTDATE = "2004-09-13";
				$TRN_ENDDATE = "2004-09-17";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="13-16 พ.ค. 40") {
				$TRN_STARTDATE = "1997-05-13";
				$TRN_ENDDATE = "1997-05-16";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="13-16 กรกฎาคม 2547") {
				$TRN_STARTDATE = "2004-07-13";
				$TRN_ENDDATE = "2004-07-16";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="13-16 ก.ค. 47") {
				$TRN_STARTDATE = "2004-07-13";
				$TRN_ENDDATE = "2004-07-16";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="13-15 สิงหาคม 2551") {
				$TRN_STARTDATE = "2008-08-13";
				$TRN_ENDDATE = "2008-08-15";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="13-15 ส.ค.41") {
				$TRN_STARTDATE = "1998-08-13";
				$TRN_ENDDATE = "1998-08-15";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="13-15 ส.ค. 40") {
				$TRN_STARTDATE = "1997-08-13";
				$TRN_ENDDATE = "1997-08-15";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="13-15 มิถุนายน 2550") {
				$TRN_STARTDATE = "2007-06-13";
				$TRN_ENDDATE = "2007-06-15";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="13-15 ม.ค.42") {
				$TRN_STARTDATE = "1999-01-13";
				$TRN_ENDDATE = "1999-01-15";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="13-15 พฤษภาคม 2548") {
				$TRN_STARTDATE = "2005-05-13";
				$TRN_ENDDATE = "2005-05-15";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="13-15 พ.ค.39") {
				$TRN_STARTDATE = "1996-05-13";
				$TRN_ENDDATE = "1996-05-15";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="13-15 พ.ค. 52") {
				$TRN_STARTDATE = "2009-05-13";
				$TRN_ENDDATE = "2009-05-15";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="13-15 พ.ค. 40") {
				$TRN_STARTDATE = "1997-05-13";
				$TRN_ENDDATE = "1997-05-15";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="13-14 ต.ค. 40") {
				$TRN_STARTDATE = "1997-10-13";
				$TRN_ENDDATE = "1887-10-14";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="13-14 กันยายน 2552") {
				$TRN_STARTDATE = "2009-09-13";
				$TRN_ENDDATE = "2009-09-14";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="13-14 ก.ค. 52") {
				$TRN_STARTDATE = "2009-07-13";
				$TRN_ENDDATE = "2009-07-14";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="13 ส.ค. 2547") {
				$TRN_STARTDATE = "2004-08-13";
				$TRN_ENDDATE = "2004-08-13";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="13 มี.ค.-4 พ.ค.44") {
				$TRN_STARTDATE = "2001-03-13";
				$TRN_ENDDATE = "2001-05-04";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="13 มี.ค.2550") {
				$TRN_STARTDATE = "2007-03-13";
				$TRN_ENDDATE = "2007-03-13";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="13 มี.ค.-17 พ.ค. 45") {
				$TRN_STARTDATE = "2002-03-13";
				$TRN_ENDDATE = "2002-05-17";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="1-3 มิถุนายน 2548") {
				$TRN_STARTDATE = "2005-06-01";
				$TRN_ENDDATE = "2005-06-03";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="13 มิ.ย.-15 ส.ค. 40") {
				$TRN_STARTDATE = "1997-06-13";
				$TRN_ENDDATE = "1997-08-15";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="1-3 พ.ย. 47") {
				$TRN_STARTDATE = "2004-11-01";
				$TRN_ENDDATE = "2004-11-03";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="13 พ.ค.41") {
				$TRN_STARTDATE = "1998-05-13";
				$TRN_ENDDATE = "1998-05-13";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="13 กันยายน 2548") {
				$TRN_STARTDATE = "2005-09-13";
				$TRN_ENDDATE = "2005-09-13";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="13 ก.ย.43") {
				$TRN_STARTDATE = "2000-09-13";
				$TRN_ENDDATE = "2000-09-13";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="12-23 ม.ค. 41") {
				$TRN_STARTDATE = "1998-01-12";
				$TRN_ENDDATE = "1998-01-23";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="12-23 กันยายน 2548") {
				$TRN_STARTDATE = "2005-09-12";
				$TRN_ENDDATE = "2005-09-23";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="12-17 ก.ย. 42") {
				$TRN_STARTDATE = "1999-09-12";
				$TRN_ENDDATE = "1999-09-17";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="12-17 ก.ค.42") {
				$TRN_STARTDATE = "1999-07-12";
				$TRN_ENDDATE = "1999-07-17";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="12-16 ม.ค.47") {
				$TRN_STARTDATE = "2004-01-12";
				$TRN_ENDDATE = "2004-01-16";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="12-16 ม.ค. 52") {
				$TRN_STARTDATE = "2009-01-12";
				$TRN_ENDDATE = "2009-01-16";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="12-16 พ.ค. 40") {
				$TRN_STARTDATE = "1997-05-12";
				$TRN_ENDDATE = "1997-05-16";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="12-16 ก.พ. 44") {
				$TRN_STARTDATE = "2001-02-12";
				$TRN_ENDDATE = "2001-02-16";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="12-16 ก.ค.47") {
				$TRN_STARTDATE = "2004-07-12";
				$TRN_ENDDATE = "2004-07-16";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="12-15 มิ.ย.46") {
				$TRN_STARTDATE = "2003-06-12";
				$TRN_ENDDATE = "2003-06-15";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="12-15 ธ.ค.2549") {
				$TRN_STARTDATE = "2006-12-12";
				$TRN_ENDDATE = "2006-12-15";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="12-15 กรกฎาคม 2547") {
				$TRN_STARTDATE = "2004-07-12";
				$TRN_ENDDATE = "2004-07-15";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="12-15 ก.ค. 47") {
				$TRN_STARTDATE = "2004-07-12";
				$TRN_ENDDATE = "2004-07-15";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="12-14 มี.ค.39") {
				$TRN_STARTDATE = "1996-03-12";
				$TRN_ENDDATE = "1996-03-14";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="12-14 พ.ค.52") {
				$TRN_STARTDATE = "2009-05-12";
				$TRN_ENDDATE = "2009-05-14";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="12-14 ธ.ค. 46") {
				$TRN_STARTDATE = "2003-12-12";
				$TRN_ENDDATE = "2003-12-14";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="12-14 ตุลาคม 2550") {
				$TRN_STARTDATE = "2007-10-12";
				$TRN_ENDDATE = "2007-10-14";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="12-14 กุมภาพันธ์ 2551") {
				$TRN_STARTDATE = "2008-02-12";
				$TRN_ENDDATE = "2008-02-14";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="12-14 กันยายน 2548") {
				$TRN_STARTDATE = "2005-09-12";
				$TRN_ENDDATE = "2005-09-14";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="12-13 มี.ค. 52") {
				$TRN_STARTDATE = "2009-03-12";
				$TRN_ENDDATE = "2009-03-13";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="12-13 ก.ค. 40") {
				$TRN_STARTDATE = "1997-07-12";
				$TRN_ENDDATE = "1997-07-13";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="1-20 ก.พ. 40") {
				$TRN_STARTDATE = "1997-02-01";
				$TRN_ENDDATE = "1997-02-20";
				$TRN_DAY = 20;
			} elseif ($TRN_REMARK=="12,19,26 ต.ค. 39") {
				$TRN_STARTDATE = "1996-10-12";
				$TRN_ENDDATE = "1996-10-26";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="12,19,26  ต.ค.,  2,9,16,23 พ.ย. 39") {
				$TRN_STARTDATE = "1996-10-12";
				$TRN_ENDDATE = "1996-11-23";
				$TRN_DAY = 7;
			} elseif ($TRN_REMARK=="12,15-16 มี.ค. 47") {
				$TRN_STARTDATE = "2004-03-12";
				$TRN_ENDDATE = "2004-03-16";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="1-2 สิงหาคม 2552") {
				$TRN_STARTDATE = "2009-08-01";
				$TRN_ENDDATE = "2009-08-02";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="1-2 สิงหาคม 2548") {
				$TRN_STARTDATE = "2005-08-01";
				$TRN_ENDDATE = "2005-08-02";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="12 มี.ค.-16 พ.ค. 40") {
				$TRN_STARTDATE = "1997-03-12";
				$TRN_ENDDATE = "1997-05-16";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="12 มี.ค.,30 มี.ค.-2 เม.ย.,21 เม.ย.-30 ก.ค.,31 ส.ค.-3 ก.ย. 47") {
				$TRN_STARTDATE = "2004-03-12";
				$TRN_ENDDATE = "2004-09-03";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="12 มี.ค.,23-26 มี.ค.,18-30 ก.ค.,24-27 ส.ค. 47") {
				$TRN_STARTDATE = "2004-03-12";
				$TRN_ENDDATE = "2004-08-27";
				$TRN_DAY = 22;
			} elseif ($TRN_REMARK=="12 มี.ค.,16-19 มี.ค.,18-30 ก.ค.,17-20 ส.ค. 47") {
				$TRN_STARTDATE = "2004-03-12";
				$TRN_ENDDATE = "2004-08-20";
				$TRN_DAY = 22;
			} elseif ($TRN_REMARK=="12 มิ.ย.-7 ก.ค.43") {
				$TRN_STARTDATE = "2000-06-12";
				$TRN_ENDDATE = "2000-07-07";
				$TRN_DAY = 26;
			} elseif ($TRN_REMARK=="12 มิ.ย.-5 ก.ย.33") {
				$TRN_STARTDATE = "1990-06-12";
				$TRN_ENDDATE = "1990-09-05";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="12 มิ.ย.-16 ก.ย.39") {
				$TRN_STARTDATE = "1996-06-12";
				$TRN_ENDDATE = "1996-09-16";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="12 มิ.ย.-12 ต.ค. 40") {
				$TRN_STARTDATE = "1997-06-12";
				$TRN_ENDDATE = "1997-10-12";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="12 มิ.ย. - 12 ส.ค.32") {
				$TRN_STARTDATE = "1989-06-12";
				$TRN_ENDDATE = "1989-08-12";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="1-2 พ.ย.42") {
				$TRN_STARTDATE = "1999-11-01";
				$TRN_ENDDATE = "1999-11-02";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="1-2 พ.ค. 40") {
				$TRN_STARTDATE = "1997-05-01";
				$TRN_ENDDATE = "1997-05-02";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="12 กันยายน 2552") {
				$TRN_STARTDATE = "2009-09-12";
				$TRN_ENDDATE = "2009-09-12";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="1-2 ก.พ. 40") {
				$TRN_STARTDATE = "1997-02-01";
				$TRN_ENDDATE = "1997-02-02";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="1-2 ก.ค.2547") {
				$TRN_STARTDATE = "2004-07-01";
				$TRN_ENDDATE = "2004-07-02";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="12 - 16 ม.ค. 2547") {
				$TRN_STARTDATE = "2004-01-12";
				$TRN_ENDDATE = "2004-01-16";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="1-17 ก.ค. 39") {
				$TRN_STARTDATE = "1996-07-01";
				$TRN_ENDDATE = "1996-07-17";
				$TRN_DAY = 17;
			} elseif ($TRN_REMARK=="1-15 พ.ค. 40") {
				$TRN_STARTDATE = "1997-05-01";
				$TRN_ENDDATE = "1997-05-15";
				$TRN_DAY = 15;
			} elseif ($TRN_REMARK=="11-22 มี.ค.2545") {
				$TRN_STARTDATE = "2002-03-11";
				$TRN_ENDDATE = "2002-03-22";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="11-20 ก.ค.43") {
				$TRN_STARTDATE = "2000-07-11";
				$TRN_ENDDATE = "2000-07-20";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK=="1-12 มิ.ย. 2552") {
				$TRN_STARTDATE = "2009-06-01";
				$TRN_ENDDATE = "2009-06-12";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="1-12 พ.ย.42") {
				$TRN_STARTDATE = "1999-11-01";
				$TRN_ENDDATE = "1999-11-12";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="11-19 ต.ค.42") {
				$TRN_STARTDATE = "1999-10-11";
				$TRN_ENDDATE = "1999-10-19";
				$TRN_DAY = 9;
			} elseif ($TRN_REMARK=="11-16 มี.ค. 39") {
				$TRN_STARTDATE = "1996-03-11";
				$TRN_ENDDATE = "1996-03-16";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="11-15 ส.ค.38") {
				$TRN_STARTDATE = "1995-08-11";
				$TRN_ENDDATE = "1995-08-15";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="11-15 พ.ย.2545") {
				$TRN_STARTDATE = "2002-11-11";
				$TRN_ENDDATE = "2002-11-15";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="11-15 พ.ย. 39") {
				$TRN_STARTDATE = "1996-11-11";
				$TRN_ENDDATE = "1996-11-15";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="11-15 ก.ค.2548") {
				$TRN_STARTDATE = "2005-07-11";
				$TRN_ENDDATE = "2005-07-15";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="11-14 ก.ย.46") {
				$TRN_STARTDATE = "2003-09-11";
				$TRN_ENDDATE = "2003-09-14";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="11-13,18-20 ต.ค. 39") {
				$TRN_STARTDATE = "1996-10-11";
				$TRN_ENDDATE = "1996-10-20";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="11-13 ก.พ. 52") {
				$TRN_STARTDATE = "2009-02-11";
				$TRN_ENDDATE = "2009-02-13";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="11-12, 15-16 ก.ย. 51") {
				$TRN_STARTDATE = "2008-09-11";
				$TRN_ENDDATE = "2008-09-16";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="11-12 มิ.ย. 52") {
				$TRN_STARTDATE = "2009-06-11";
				$TRN_ENDDATE = "2009-06-12";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="11-12 ก.พ. 52") {
				$TRN_STARTDATE = "2009-02-11";
				$TRN_ENDDATE = "2009-02-12";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="1-11 สิงหาคม 2550") {
				$TRN_STARTDATE = "2007-08-01";
				$TRN_ENDDATE = "2007-08-11";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="10-27 ก.พ. 52") {
				$TRN_STARTDATE = "2009-02-10";
				$TRN_ENDDATE = "2009-02-27";
				$TRN_DAY = 18;
			} elseif ($TRN_REMARK=="10-21 พ.ค.47") {
				$TRN_STARTDATE = "2004-05-10";
				$TRN_ENDDATE = "2004-05-21";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="10-21 พ.ค.2547") {
				$TRN_STARTDATE = "2004-05-10";
				$TRN_ENDDATE = "2004-05-21";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="10-21 พ.ค. 47") {
				$TRN_STARTDATE = "2004-05-10";
				$TRN_ENDDATE = "2004-05-21";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK=="10-20 พ.ค.2547") {
				$TRN_STARTDATE = "2004-05-10";
				$TRN_ENDDATE = "2004-05-20";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="10-20 พ.ค. 47") {
				$TRN_STARTDATE = "2004-05-10";
				$TRN_ENDDATE = "2004-05-20";
				$TRN_DAY = 11;
			} elseif ($TRN_REMARK=="10-16 ส.ค.41") {
				$TRN_STARTDATE = "1998-08-10";
				$TRN_ENDDATE = "1998-08-16";
				$TRN_DAY = 7;
			} elseif ($TRN_REMARK=="10-15 ม.ค. 42") {
				$TRN_STARTDATE = "1999-01-10";
				$TRN_ENDDATE = "1999-01-15";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK=="10-14 มี.ค. 40") {
				$TRN_STARTDATE = "1997-03-10";
				$TRN_ENDDATE = "1997-03-14";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="10-14 พฤษภาคม  2547") {
				$TRN_STARTDATE = "2004-05-10";
				$TRN_ENDDATE = "2004-05-14";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="10-14 พ.ค. 47") {
				$TRN_STARTDATE = "2004-05-10";
				$TRN_ENDDATE = "2004-05-14";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK=="10-13 พ.ย. 47") {
				$TRN_STARTDATE = "2004-11-10";
				$TRN_ENDDATE = "2004-11-13";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK=="10-12 มีนาคม 2547") {
				$TRN_STARTDATE = "2004-03-10";
				$TRN_ENDDATE = "2004-03-12";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="10-12 มี.ค. 47") {
				$TRN_STARTDATE = "2004-03-10";
				$TRN_ENDDATE = "2004-03-12";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="10-12 มิ.ย. 52") {
				$TRN_STARTDATE = "2009-06-10";
				$TRN_ENDDATE = "2009-06-12";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="10-12 พ.ย. 40") {
				$TRN_STARTDATE = "1997-11-10";
				$TRN_ENDDATE = "1997-11-12";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="10-12 ก.ย. 51") {
				$TRN_STARTDATE = "2008-09-10";
				$TRN_ENDDATE = "2008-09-12";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="10-12 ก.พ. 40") {
				$TRN_STARTDATE = "1997-02-10";
				$TRN_ENDDATE = "1997-02-12";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="10-11 ส.ค.43") {
				$TRN_STARTDATE = "2000-08-10";
				$TRN_ENDDATE = "2000-08-11";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="10-11 และ 15 มี.ค.42") {
				$TRN_STARTDATE = "1999-03-10";
				$TRN_ENDDATE = "1999-03-15";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="10-11 มี.ค.2548") {
				$TRN_STARTDATE = "2005-03-10";
				$TRN_ENDDATE = "2005-03-11";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="10-11 พฤศจิกายน 2550") {
				$TRN_STARTDATE = "2007-11-10";
				$TRN_ENDDATE = "2007-11-11";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="10-11 ก.ย.41") {
				$TRN_STARTDATE = "1998-09-10";
				$TRN_ENDDATE = "1998-09-11";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK=="10 มี.ค.-4 เม.ย. 40") {
				$TRN_STARTDATE = "1997-03-10";
				$TRN_ENDDATE = "1997-04-04";
				$TRN_DAY = 25;
			} elseif ($TRN_REMARK=="10 พ.ย.49") {
				$TRN_STARTDATE = "2006-11-10";
				$TRN_ENDDATE = "2006-11-10";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="10 ต.ค.38-13 ก.พ.39") {
				$TRN_STARTDATE = "1995-10-10";
				$TRN_ENDDATE = "1996-02-13";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK=="10 - 12 เม.ย. 2549") {
				$TRN_STARTDATE = "2006-04-10";
				$TRN_ENDDATE = "2006-04-12";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK=="1 ส.ค.-10 พ.ย. 35") {
				$TRN_STARTDATE = "1992-08-01";
				$TRN_ENDDATE = "1992-11-10";
				$TRN_DAY = 102;
			} elseif ($TRN_REMARK=="1 เม.ย.50") {
				$TRN_STARTDATE = "2007-04-01";
				$TRN_ENDDATE = "2007-04-01";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="1 มิ.ย.-30 ก.ย.48") {
				$TRN_STARTDATE = "2005-06-01";
				$TRN_ENDDATE = "2005-09-30";
				$TRN_DAY = 122;
			} elseif ($TRN_REMARK=="1 มิ.ย.-29 ก.ค.42") {
				$TRN_STARTDATE = "1999-06-01";
				$TRN_ENDDATE = "1999-07-29";
				$TRN_DAY = 59;
			} elseif ($TRN_REMARK=="1 มิ.ย. 47") {
				$TRN_STARTDATE = "2004-06-01";
				$TRN_ENDDATE = "2004-06-01";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="1 ธ.ค. 42") {
				$TRN_STARTDATE = "1999-12-01";
				$TRN_ENDDATE = "1999-12-01";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK=="1 ก.ค.-28 ก.ย.35") {
				$TRN_STARTDATE = "1992-07-01";
				$TRN_ENDDATE = "1992-09-28";
				$TRN_DAY = 90;
			} elseif ($TRN_REMARK=="00-00-2543") {
				$TRN_STARTDATE = "2000-00-00";
				$TRN_ENDDATE = "2000-00-00";
				$TRN_DAY = 0;
			} elseif ($TRN_REMARK==" ปี 2544 (5 วัน)") {
				$TRN_STARTDATE = "2001-00-00";
				$TRN_ENDDATE = "2001-00-00";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK==" ปี 2543 (2 วัน)") {
				$TRN_STARTDATE = "2000-00-00";
				$TRN_ENDDATE = "2000-00-00";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK==" ปี 2543 (10 วัน)") {
				$TRN_STARTDATE = "2000-00-00";
				$TRN_ENDDATE = "2000-00-00";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK==" ปี 2540 (5 วัน)") {
				$TRN_STARTDATE = "1997-00-00";
				$TRN_ENDDATE = "1997-00-00";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK==" ปี 2540 (3 วัน)") {
				$TRN_STARTDATE = "1997-00-00";
				$TRN_ENDDATE = "1997-00-00";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK==" ปี 2539 (5 วัน)") {
				$TRN_STARTDATE = "1996-00-00";
				$TRN_ENDDATE = "1996-00-00";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK==" ปี 2538 (3 วัน)") {
				$TRN_STARTDATE = "1995-00-00";
				$TRN_ENDDATE = "1995-00-00";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK==" ปี 2538 (2 วัน)") {
				$TRN_STARTDATE = "1995-00-00";
				$TRN_ENDDATE = "1995-00-00";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK==" 6-8 พ.ค. 40") {
				$TRN_STARTDATE = "1997-05-06";
				$TRN_ENDDATE = "1997-05-08";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK==" 5 - 9 พ.ย. 44") {
				$TRN_STARTDATE = "2001-11-05";
				$TRN_ENDDATE = "2001-11-09";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK==" 4 - 13 มิ.ย. 44") {
				$TRN_STARTDATE = "2001-06-04";
				$TRN_ENDDATE = "2001-06-13";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK==" 4 - 10 มิ.ย. 44") {
				$TRN_STARTDATE = "2001-06-04";
				$TRN_ENDDATE = "2001-06-10";
				$TRN_DAY = 7;
			} elseif ($TRN_REMARK==" 26 - 27 ก.ย. 44") {
				$TRN_STARTDATE = "2001-09-26";
				$TRN_ENDDATE = "2001-09-27";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK==" 25 - 29 มิ.ย. 39") {
				$TRN_STARTDATE = "1996-06-25";
				$TRN_ENDDATE = "1996-06-29";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK==" 24 - 25 ก.พ. 44") {
				$TRN_STARTDATE = "2001-02-24";
				$TRN_ENDDATE = "2001-02-25";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK==" 23-26 เม.ย. 52") {
				$TRN_STARTDATE = "2009-04-23";
				$TRN_ENDDATE = "2009-04-26";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK==" 23 - 28 มี.ค. 42") {
				$TRN_STARTDATE = "1999-03-23";
				$TRN_ENDDATE = "1999-03-28";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK==" 23 - 27 ก.ค. 44") {
				$TRN_STARTDATE = "2001-07-23";
				$TRN_ENDDATE = "2001-07-27";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK==" 22 พ.ย. 44") {
				$TRN_STARTDATE = "2001-11-22";
				$TRN_ENDDATE = "2001-11-22";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK==" 22 - 31 มี.ค. 38") {
				$TRN_STARTDATE = "1995-03-22";
				$TRN_ENDDATE = "1995-03-31";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK==" 22 - 26 ก.ค. 39") {
				$TRN_STARTDATE = "1996-07-22";
				$TRN_ENDDATE = "1996-07-26";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK==" 22 - 24 ก.ย. 41") {
				$TRN_STARTDATE = "1998-09-22";
				$TRN_ENDDATE = "1998-09-24";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK==" 21 - 30 พ.ค. 44") {
				$TRN_STARTDATE = "2001-05-21";
				$TRN_ENDDATE = "2001-05-30";
				$TRN_DAY = 12;
			} elseif ($TRN_REMARK==" 2 - 4 ก.ย. 41") {
				$TRN_STARTDATE = "1998-09-02";
				$TRN_ENDDATE = "1998-09-04";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK==" 17 - 22 พ.ย. 39") {
				$TRN_STARTDATE = "1996-11-17";
				$TRN_ENDDATE = "1996-11-22";
				$TRN_DAY = 6;
			} elseif ($TRN_REMARK==" 16 - 19 ก.ค. 44") {
				$TRN_STARTDATE = "2001-07-16";
				$TRN_ENDDATE = "2001-07-19";
				$TRN_DAY = 4;
			} elseif ($TRN_REMARK==" 15 - 19 พ.ค. 38") {
				$TRN_STARTDATE = "1995-05-15";
				$TRN_ENDDATE = "1995-05-19";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK==" 15 - 19 ก.ค. 39") {
				$TRN_STARTDATE = "1996-07-15";
				$TRN_ENDDATE = "1996-07-19";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK==" 13 - 22 มี.ค. 38") {
				$TRN_STARTDATE = "1995-03-13";
				$TRN_ENDDATE = "1995-03-22";
				$TRN_DAY = 10;
			} elseif ($TRN_REMARK==" 11 เม.ย.50 (เวลา 09.00-12.00 น.)") {
				$TRN_STARTDATE = "2007-04-11";
				$TRN_ENDDATE = "2007-04-11";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK==" 11 ก.ย. 51") {
				$TRN_STARTDATE = "2008-09-11";
				$TRN_ENDDATE = "2008-09-11";
				$TRN_DAY = 1;
			} elseif ($TRN_REMARK==" 10 - 14 ส.ค. 42") {
				$TRN_STARTDATE = "1999-08-10";
				$TRN_ENDDATE = "1999-08-14";
				$TRN_DAY = 5;
			} elseif ($TRN_REMARK==" 10 - 12 ก.ค. 39") {
				$TRN_STARTDATE = "1996-07-10";
				$TRN_ENDDATE = "1996-07-12";
				$TRN_DAY = 3;
			} elseif ($TRN_REMARK==" 1 ก.พ. - 16 ก.ค. 39") {
				$TRN_STARTDATE = "1996-07-01";
				$TRN_ENDDATE = "1996-07-16";
				$TRN_DAY = 16;
			} elseif ($TRN_REMARK==" 1 - 2 พ.ค. 40") {
				$TRN_STARTDATE = "1997-05-01";
				$TRN_ENDDATE = "1997-05-02";
				$TRN_DAY = 2;
			} elseif ($TRN_REMARK==" 1 - 15 มิ.ย. 39") {
				$TRN_STARTDATE = "1996-06-01";
				$TRN_ENDDATE = "1996-06-15";
				$TRN_DAY = 15;
			} elseif ($TRN_REMARK==" 1 - 11 ก.ย. 38") {
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
			if ($PER_SURNAME=="วิญญูวิริยะวงศ์") $PER_SURNAME = "วิญญูวิริยวงศ์";
			elseif ($PER_SURNAME=="วงศ์ไพบูลย์วัฒน์") $PER_SURNAME = "วงศ์ไพบูลย์วัฒน";
			elseif ($PER_SURNAME=="ประสูตรแสงจันทร์") $PER_SURNAME = "ประสูตร์แสงจันทร์";
			elseif ($PER_NAME=="ขวัญใจ" && $PER_SURNAME=="ปทุมมิน") $PER_NAME = "ขวัญกัลยา";
			elseif ($PER_NAME=="จันทิรา" && $PER_SURNAME=="โยธการ") $PER_SURNAME = "โยธิการ";
			elseif ($PER_SURNAME=="สันตุฏฐีกุล") $PER_SURNAME = "สันตุฎฐีกุล";
			elseif ($PER_NAME=="จิดาภา" && $PER_SURNAME=="พัฒนอนันตสุข") { $PER_NAME = "อาภา"; $PER_SURNAME = "ศุภราวัฒน์"; }
			elseif ($PER_SURNAME=="ฉัตรธนะสิริเวช") $PER_SURNAME = "ฉัตรธนะสิรเวช";
			elseif ($PER_NAME=="จุฑาทิพย์" && $PER_SURNAME=="ศรีไชยยันต์") $PER_NAME = "จุฑาทิพ";
			elseif ($PER_SURNAME=="ใหญ่สวัสดิ์วงศ์") $PER_SURNAME = "ใหญ่สวัสดิ์วงษ์";
			elseif ($PER_SURNAME=="กฤติมณีกุล") $PER_SURNAME = "กฤตติมณีกุล";
			elseif ($PER_NAME=="ทัศนีย์" && $PER_SURNAME=="โรจน์สันติบุญศิริ") { $PER_NAME = "ริญญาภัทร์"; $PER_SURNAME = "โรจสันติชัยกุล"; }
			elseif ($PER_NAME=="ธัญญนันท์" && $PER_SURNAME=="นุ่มน้อย") $PER_NAME = "ธัญนันท์";
			elseif ($PER_NAME=="ธีระพร" && $PER_SURNAME=="เกิดสินธุ์") $PER_SURNAME = "ปราโมทย์มณีรัตน์";
			elseif ($PER_NAME=="นฤมล" && $PER_SURNAME=="บุญคล้อย") $PER_SURNAME = "หลวงไกร";
			elseif ($PER_NAME=="นุชนารถ" && $PER_SURNAME=="บุญปาลิต") $PER_NAME = "นุชนาถ";
			elseif ($PER_NAME=="บัณฑิตา" && $PER_SURNAME=="มะสาแม") { $PER_NAME = "พัชชานันท์"; $PER_SURNAME = "แซ่จึง"; }
			elseif ($PER_NAME=="ประภาสร" && $PER_SURNAME=="บุญเทียม") $PER_NAME = "ปภาสร";
			elseif ($PER_NAME=="ปรียนาฎ" && $PER_SURNAME=="เทียบรัตน์") $PER_NAME = "ปรียนาฏ";
			elseif ($PER_NAME=="ปรียานุช" && $PER_SURNAME=="วาทิน") $PER_SURNAME = "ธรรมขันธา";
			elseif ($PER_NAME=="ปาริชาติ" && $PER_SURNAME=="ราษฎา") { $PER_NAME = "รชยา"; $PER_SURNAME = "ศิริธรรมศักดา"; }
			elseif ($PER_NAME=="พนิดา" && $PER_SURNAME=="กาญจนวาหะ") $PER_NAME = "ภัทราพรรณ";
			elseif ($PER_NAME=="พรพิมล" && $PER_SURNAME=="ด้วงพูล") $PER_SURNAME = "ด้วงพลู";
			elseif ($PER_SURNAME=="ภานุพงค์วรรธกา") $PER_SURNAME = "ภานุพงศ์วรรธกา";
			elseif ($PER_NAME=="พิมลวรรณ" && $PER_SURNAME=="เกษตรแท่น") { $PER_NAME = "พิมลพรรณ"; $PER_SURNAME = "เกษตรเวทิน"; }
			elseif ($PER_NAME=="ไพฑูรย์" && $PER_SURNAME=="โกเมน") { $PER_NAME = "ไพทูรย์"; $PER_SURNAME = "โกเมนท์"; }
			elseif ($PER_NAME=="ภัทรกร" && $PER_SURNAME=="เทียนศรี") $PER_NAME = "ภัทรภร";
			elseif ($PER_NAME=="ภูมิศักดิ์" && $PER_SURNAME=="จินกสิกิจ") $PER_NAME = "ภูศักดิ์";
			elseif ($PER_NAME=="รัชนี" && $PER_SURNAME=="ปีกแก้ว") $PER_SURNAME = "ปิกแก้ว";
			elseif ($PER_NAME=="ลำพึง" && $PER_SURNAME=="กลิ่นหอม") $PER_SURNAME = "นาวีเสถียร";
			elseif ($PER_NAME=="วรรณิดา" && $PER_SURNAME=="วัชรกาฬ") $PER_SURNAME = "ปัญจะวัฒนาภรณ์";
			elseif ($PER_NAME=="วรารัตน์" && $PER_SURNAME=="ทองเหลือง") $PER_SURNAME = "เกตุพงษ์พันธุ์";
			elseif ($PER_NAME=="วาศิณี" && $PER_SURNAME=="พรทอง") $PER_SURNAME = "ทุยโทชัย";
			elseif ($PER_NAME=="วิมลรัตน์" && $PER_SURNAME=="ปุรินทราภิบาล") $PER_SURNAME = "ปุรินตราภิบาล";
			elseif ($PER_NAME=="วิไลรัตน์" && $PER_SURNAME=="เลิศปองธรรม") $PER_NAME = "พัชชาพร";
			elseif ($PER_NAME=="วิศิษฐ์" && $PER_SURNAME=="ไฝจันทร์") $PER_SURNAME = "ไผ่จันทร์";
			elseif ($PER_NAME=="ศรีสุดา" && $PER_SURNAME=="มณีโชติวงศ์") $PER_NAME = "พัทธนันท์";
			elseif ($PER_NAME=="ศศิมาภรณ์" && $PER_SURNAME=="พันธ์โคตร") $PER_SURNAME = "พันธโคตร";
			elseif ($PER_NAME=="ศิริรัตน์" && $PER_SURNAME=="สุเมธพิพัธน์") $PER_SURNAME = "สุเมธพิพัฒน์";
			elseif ($PER_NAME=="สงกรานต์" && $PER_SURNAME=="ขุนหอม") { $PER_NAME = "ณัฐเมศร์"; $PER_SURNAME = "ชัยศิระศักดิ์"; }
			elseif ($PER_NAME=="สมโชค" && $PER_SURNAME=="หาญกลับ") $PER_SURNAME = "หาญนิวัติกุล";
			elseif ($PER_NAME=="สมศรี" && $PER_SURNAME=="ปุณศิริ") $PER_SURNAME = "ปุญศิริ";
			elseif ($PER_NAME=="สมศรี" && $PER_SURNAME=="อันพิมพ์") $PER_SURNAME = "อ้นพิมพ์";
			elseif ($PER_NAME=="สันติ" && $PER_SURNAME=="โลหะปิยะพรรณ") $PER_NAME = "ธิติ";
			elseif ($PER_NAME=="สุจิตรา" && $PER_SURNAME=="กานต์ธนสุนทร") { $PER_NAME = "นิจชญาน์"; $PER_SURNAME = "ฤทธิรงค์"; }
			elseif ($PER_NAME=="สุพัตรา" && $PER_SURNAME=="ผลรัตนไพลูลย์") $PER_SURNAME = "ผลรัตนไพบูลย์";
			elseif ($PER_NAME=="สุรัติ" && $PER_SURNAME=="มณีโลก") $PER_SURNAME = "มณีโลกย์";
			elseif ($NAME1=="สุริยะจันทร์แสงศรี") { $PER_NAME = "สุริยะ"; $PER_SURNAME = "จันทร์แสงศรี"; }
			elseif ($PER_NAME=="สุรีภรณ์" && $PER_SURNAME=="สุขประเสริฐ") $PER_SURNAME = "สุทธิพันธ์";
			elseif ($PER_NAME=="อมราพร" && $PER_SURNAME=="ชีพสมุทร") $PER_SURNAME = "ชีพสมุทร์";
			elseif ($PER_NAME=="อัจฉรา" && $PER_SURNAME=="เลิศมณีกุลชัย") $PER_SURNAME = "เลิศมณีสกุลชัย";
			elseif ($PER_NAME=="อารมย์" && $PER_SURNAME=="คำสุทธิ") $PER_SURNAME = "คำสุทธิ์";

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