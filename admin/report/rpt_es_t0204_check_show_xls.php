<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$report_title = trim(iconv("utf-8","tis620",urldecode($report_title)));
	$worksheet = &$workbook->addworksheet($report_title);
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 25);
		$worksheet->set_column(1, 1, 25);
		$worksheet->set_column(2, 2, 20);
		$worksheet->set_column(3, 3, 10);
		$worksheet->set_column(4, 4, 10);
		$worksheet->set_column(5, 5, 15);
		
		$worksheet->write($xlsRow, 0, "วัน-เวลาที่สแกน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อ-สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ประเภทการลา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "วันที่เริ่มต้น", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "วันที่สิ้นสุด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "จำนวนวัน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // end if

		$cmd = " 
						select		q1.*
                             from (
						select 	DISTINCT
                                        abs.PER_ID,abs.ABS_STARTDATE,abs.ABS_STARTPERIOD,abs.ABS_ENDDATE,abs.ABS_ENDPERIOD,
                                        TO_CHAR(att.TIME_STAMP,'yyyy-mm-dd') AS TIME_STAMP,
                                        a.PER_TYPE,g.PN_SHORTNAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW,
                                         c.POS_NO,d.POEM_NO,e.POEMS_NO,k.AB_NAME,j.POT_NO,abs.ABS_DAY
                                from  PER_ABSENTHIS  abs 
                                left join PER_TIME_ATTENDANCE att on(att.PER_ID=abs.PER_ID AND
												( TO_CHAR(att.TIME_STAMP,'yyyy-mm-dd')  BETWEEN substr(abs.ABS_STARTDATE,1,10)AND substr(abs.ABS_ENDDATE,1,10)))
                                left join PER_PERSONAL a on(a.PER_ID=abs.PER_ID)
                                left join PER_ORG b on(b.ORG_ID=a.ORG_ID) 
                                left join PER_POSITION c on(c.POS_ID=a.POS_ID) 
                                left join PER_POS_EMP d on(d.POEM_ID=a.POEM_ID) 
                                left join PER_POS_EMPSER e on(e.POEMS_ID=a.POEMS_ID) 
                                left join PER_LEVEL f on(f.LEVEL_NO=a.LEVEL_NO) 
                                left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE) 
                                left join PER_MGT h on(h.PM_CODE=c.PM_CODE)
                                left join PER_LINE i on(i.PL_CODE=c.PL_CODE)
                                left join PER_POS_TEMP j on (j.POT_ID=a.POT_ID)
                                left join PER_ABSENTTYPE k on (k.AB_CODE=abs.AB_CODE)
                                
                            WHERE abs.AB_CODE NOT IN(10,13) AND att.TIME_STAMP IS NOT NULL
                            AND (att.TIME_STAMP  BETWEEN to_date('$BGN','yyyy-mm-dd hh24:mi:ss')   AND to_date('$END','yyyy-mm-dd hh24:mi:ss')) 
                        	AND (a.ORG_ID in (select org_id from PER_ORG_ASS start with ORG_ID in
                             								(select org_id from PER_ORG_ASS where org_id =$ORGID) 
                        									CONNECT BY PRIOR org_id = ORG_ID_REF)  ) 
							) q1  ORDER BY q1.TIME_STAMP	
															";

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$DATA_TIME_STAMP = show_date_format($data[TIME_STAMP], $DATE_DISPLAY);
			$cmd = " SELECT TO_CHAR(min(TIME_STAMP),'HH24:MI:SS') AS HHII FROM PER_TIME_ATTENDANCE  
						where PER_ID=".$data[PER_ID]." AND TO_CHAR(TIME_STAMP,'yyyy-mm-dd') ='".$data[TIME_STAMP]."'";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$TMP_HHII = $data1[HHII];

			
			$DATA_FULLNAME_SHOW = trim($data[FULLNAME_SHOW]);

			
			$DATA_AB_NAME = trim($data[AB_NAME]); 

			 
			 $DATA_ABS_STARTDATE = show_date_format($data[ABS_STARTDATE], $DATE_DISPLAY); 

			 
			 $DATA_ABS_ENDDATE = show_date_format($data[ABS_ENDDATE], $DATE_DISPLAY); 

			 
			 $DATA_ABS_STARTPERIOD ="";
			if($data[ABS_STARTPERIOD]=="1"){
				$DATA_ABS_STARTPERIOD =" (ครึ่งวันเช้า)";
			}elseif($data[ABS_STARTPERIOD]=="2"){
				$DATA_ABS_STARTPERIOD =" (ครึ่งวันบ่าย)";
			}
			if($data[ABS_DAY]!="0.5"){
				$DATA_ABS_STARTPERIOD ="";
			}
			
			$DATA_ABS_DAY = trim(round($data[ABS_DAY],2)).$DATA_ABS_STARTPERIOD;
			
			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $DATA_TIME_STAMP." ".$TMP_HHII, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $DATA_FULLNAME_SHOW, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $DATA_AB_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $DATA_ABS_STARTDATE, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $DATA_ABS_ENDDATE, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 5, $DATA_ABS_DAY, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_title.xls\"");
	header("Content-Disposition: inline; filename=\"$report_title.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>