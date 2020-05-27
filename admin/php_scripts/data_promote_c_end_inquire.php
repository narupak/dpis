<?
	include("../php_scripts/connect_database.php");
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	if (trim($PRO_YEAR))	 {
		$tmp_pro_year = $PRO_YEAR - 543;
		/*
		$tmp_pro_year_start = ($PRO_YEAR - 3) - 543;	
		$tmp_pro_year_end = ($PRO_YEAR - 2) - 543;*/
		$tmp_pro_year_start = ($PRO_YEAR) - 543;	
		$tmp_pro_year_end = ($PRO_YEAR ) - 543;
	}
	/***if ($DPISDB=="odbc") {
		$where_effectivedate = " LEFT(d.POH_EFFECTIVEDATE,10) >= '$tmp_pro_year_start-10-01' and LEFT(d.POH_EFFECTIVEDATE,10) <= '$tmp_pro_year_end-09-30' and	";
	} elseif ($DPISDB=="oci8") {
		$where_effectivedate = " SUBSTR(d.POH_EFFECTIVEDATE,1,10) >= '$tmp_pro_year_start-10-01' and SUBSTR(d.POH_EFFECTIVEDATE,1,10) <= '$tmp_pro_year_end-09-30' and ";
	}elseif($DPISDB=="mysql"){
		$where_effectivedate = " SUBSTRING(d.POH_EFFECTIVEDATE,10) >= '$tmp_pro_year_start-10-01' and SUBSTRING(d.POH_EFFECTIVEDATE,10) <= '$tmp_pro_year_end-09-30' and	";
	}***/
	if ($DPISDB=="odbc") {
		$where_effectivedate = " LEFT(d.POH_EFFECTIVEDATE,10) >= '$tmp_pro_year_start-09-30' and LEFT(d.POH_EFFECTIVEDATE,10) <= '$tmp_pro_year_end-10-01' and	";
	} elseif ($DPISDB=="oci8") {
		$where_effectivedate = " SUBSTR(d.POH_EFFECTIVEDATE,1,10) >= '$tmp_pro_year_start-09-30' and SUBSTR(d.POH_EFFECTIVEDATE,1,10) <= '$tmp_pro_year_end-10-01' and ";
	}elseif($DPISDB=="mysql"){
		$where_effectivedate = " SUBSTRING(d.POH_EFFECTIVEDATE,10) >= '$tmp_pro_year_start-09-30' and SUBSTRING(d.POH_EFFECTIVEDATE,10) <= '$tmp_pro_year_end-10-01' and	";
	}
//echo "ปีงบประมาณ $PRO_YEAR :: $tmp_pro_year_start-10-01 - $tmp_pro_year_end-09-30<br>";

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($command == "SAVE" && trim($PRO_YEAR)){
		$cmd = " delete from PER_PROMOTE_C where PRO_YEAR = '$tmp_pro_year_start' and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		
		
		$cmd = "	select	b.PER_ID, b.PER_CARDNO 
				from		PER_POSITIONHIS a, PER_PERSONAL b, PER_POSITION c, PER_POSITIONHIS d   
				where	a.MOV_CODE in ('101', '10110', '10120', '10130', '10140', '105', '10510', '10520')  and 
						((a.LEVEL_NO='01' and b.LEVEL_NO='02') or (a.LEVEL_NO='02' and b.LEVEL_NO='03') or (a.LEVEL_NO='03' and b.LEVEL_NO='04') or (a.LEVEL_NO='04' and b.LEVEL_NO='04')) 
						and $where_effectivedate 
						       PER_TYPE=1 and PER_STATUS=1 and a.PER_ID=b.PER_ID and b.PER_ID=d.PER_ID and b.POS_ID=c.POS_ID and b.LEVEL_NO=d.LEVEL_NO
						and b.DEPARTMENT_ID=$DEPARTMENT_ID						
				group by 	b.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, a.LEVEL_NO, b.LEVEL_NO, b.POS_ID, 
						c.PL_CODE, b.PER_SALARY, c.ORG_ID, c.ORG_ID_1, b.PER_CARDNO, c.PT_CODE  ";	
		$db_dpis->send_cmd($cmd);
		while ( $data = $db_dpis->get_array() ) {
			$PER_ID_tmp = $data[PER_ID];
			$PER_CARDNO_tmp = $data[PER_CARDNO];
			$cmd = " 	insert into PER_PROMOTE_C 
						(PRO_YEAR, PRO_TYPE, PER_ID, UPDATE_USER, UPDATE_DATE, PER_CARDNO, DEPARTMENT_ID) 
					VALUES 
						('$PRO_YEAR', 2, $PER_ID_tmp, $SESS_USERID, '$UPDATE_DATE', '$PER_CARDNO_tmp', $DEPARTMENT_ID) ";
			$db_dpis1->send_cmd($cmd);
			//echo "$cmd<br>";
		}

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > บันทึกข้อมูลข้าราชการมีคุณสมบัติได้เลื่อนระดับควบปลาย [$DEPARTMENT_ID : ".$tmp_pro_year_start." : ".$PER_ID."]");
		$alert_success_save = "alert('บันทึกรายชื่อข้าราชการได้เลื่อนระดับควบปลายเรียบร้อยแล้ว')";
	}	
?>