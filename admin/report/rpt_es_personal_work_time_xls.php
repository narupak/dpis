<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	$db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis_AB = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$report_title = trim(iconv("utf-8","tis620",urldecode($report_title)));
	if (strlen($PER_NAME) >= 32) {
			$PER_NAME = substr($PER_NAME,0,31);
			$worksheet = &$workbook->addworksheet($PER_NAME);
	}else{
			$worksheet = &$workbook->addworksheet($PER_NAME);
	}
	
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 15);
		$worksheet->set_column(2, 2, 10);
		$worksheet->set_column(3, 3, 10);
		$worksheet->set_column(4, 4, 25);
		$worksheet->set_column(5, 5, 15);
		$worksheet->set_column(6, 6, 15);
		$worksheet->set_column(7, 7, 45);
		
		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "วันที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "เวลาเข้า", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "เวลาออก", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "รอบเวลา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "สถานะการลงเวลา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "สถานะการลา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "สถานะวัน/คำร้อง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // end if
	
	$cmd_min = "select  to_char(greatest(
			nvl((select trunc(min(TIME_STAMP),'MONTH') from PER_TIME_ATTENDANCE where per_id=$PER_ID), trunc(sysdate)-61),
			nvl((select last_day(to_date(to_char((close_year-543))||to_char(close_month,'00')||'01','YYYYMMDD'))+1 xx
				from (select * from(select close_year, close_month from per_work_time_control where  APPROVE_DATE is not null
				and DEPARTMENT_ID =(select ORG_ID from per_personal where per_id=$PER_ID)
		  order by close_year desc , close_month desc) where rownum=1) x
		), trunc(sysdate)-61)
	),'yyyy-mm-dd hh24:mi:ss') y from dual" ; 
				
	$db_dpis->send_cmd_fast($cmd_min);
	$data_min = $db_dpis->get_array_array();
	$TIME_STAMP_min = $data_min[0];
	
	$xx = $TIME_STAMP_min;
	$yy = date("Y-m-d")." 23:59:59";
	
	$cmd = file_get_contents('../../admin/GetWorkTimeByPerID.sql');	

	$cmd_con = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_SCANTYPE' ";
	$db_dpis->send_cmd_fast($cmd_con);
	$data_con = $db_dpis->get_array_array();
	$SCANTYPE = $data_con[CONFIG_VALUE];
			
	$cmd=str_ireplace(":PER_ID",$PER_ID,$cmd);
	$cmd=str_ireplace(":BEGINDATEAT","'".$xx."'",$cmd);
	$cmd=str_ireplace(":TODATEAT","'".$yy."'",$cmd);
	$cmd=str_ireplace(":SCANTYPE",$SCANTYPE,$cmd);
	$cmd=$cmd." desc ";

	$count_data = $db_dpis4->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$xlsRow = 0;
		
		$worksheet->write($xlsRow, 0, "ข้อมูลประมวลผลการลงเวลาเบื้องต้น", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 1;
		$data_no = 0;
		while($data4 = $db_dpis4->get_array_array()){
			$data_count++;
			$data_no++;
			$DATA_WORK_DATE = show_date_format($data4[WORK_DATE], $DATE_DISPLAY);

			$DATA_ENTTIME = $data4[ENTTIME];
			
			$DATA_EXITTIME = $data4[EXITTIME];
			
			$cmd = " select WC_NAME FROM PER_WORK_CYCLE WHERE WC_CODE=".$data4[WC_CODE];       
			$db_dpis3->send_cmd_fast($cmd);
			$data_cy = $db_dpis3->get_array_array();
			$DATA_LATE_TIME = "";
			if($data4[LATE_TIME]){
				$DATA_LATE_TIME = "+เวลาพิเศษ ".substr($data4[LATE_TIME],0,2).":".substr($data4[LATE_TIME],2,2);
			}
			$DATA_WC_NAME=$data_cy[WC_NAME].$DATA_LATE_TIME;
			
			
			
			$DATA_WORK_FLAG = "";
			if($data4[WORK_FLAG]==1){
				if($data4[REMARK]){
					$DATA_WORK_FLAG = $data4[REMARK];
				}else{
					$DATA_WORK_FLAG = "สาย";
				}
				
				$HIDLATE1++;
			}else if($data4[WORK_FLAG]==2){
				$cmd = " select cl.END_LATETIME
                            FROM  PER_WORK_CYCLEHIS cyh
                            left join PER_WORK_CYCLE cl on(cl.WC_CODE=cyh.WC_CODE) 
                            WHERE cyh.PER_ID =$PER_ID 
                            AND 
                            (sysdate between to_date(cyh.START_DATE,'yyyy-mm-dd hh24:mi:ss') AND case 
							when cyh.END_DATE is not null then to_date(cyh.END_DATE,'yyyy-mm-dd hh24:mi:ss') 
                            else sysdate end ) ";
				$db_dpis->send_cmd($cmd);
				$data_today = $db_dpis->get_array();
				$TODAY_END_LATETIME = trim($data_today[END_LATETIME]); // สิ้นสุดเวลาสาย
				
				if($TODAY_END_LATETIME){
					$WORK_NOW = date("Y-m-d"); // วันที่ปัจจุบัน
					$TODAY_TIME_NOW = date("Hi"); // เวลาปัจจุบัน
					// 0 = ขาด, 2 = ลาบ่าย, 10= ลาเช้า, 12= ลาเช้าและลาบ่าย, 30 = ลาทั้งวัน
					if($data4[ABSENT] !="0,0" && $data4[ABSENT] != "313,0"){
					   if($WORK_NOW==$data4[WORK_DATE]){ 
							if($TODAY_TIME_NOW > $TODAY_END_LATETIME){   
								$DATA_WORK_FLAG = "ขาดราชการ";
							}else{
								$DATA_WORK_FLAG = "";
							}
					   }else{
							$DATA_WORK_FLAG = "ขาดราชการ";
					   }
						
					}else{
						if($WORK_NOW==$data4[WORK_DATE]){ 
							if($TODAY_TIME_NOW > $TODAY_END_LATETIME){   
								$DATA_WORK_FLAG = "ขาดราชการ";
							}else{
								$DATA_WORK_FLAG = "";
							}
					   }else{
							$DATA_WORK_FLAG = "ขาดราชการ";
					   }
					}
					
				 }else{
					$DATA_WORK_FLAG = "ขาดราชการ";
				 }
			}else if($data4[WORK_FLAG]==3){
				if($data4[REMARK]){
					$DATA_WORK_FLAG = $data4[REMARK];
				}else{
					$DATA_WORK_FLAG = "ออกก่อน";
				}
			}else if($data4[WORK_FLAG]==4){
				$WORK_NOW = date("Y-m-d");
				if($data4[WC_CODE]=="-1" && $WORK_NOW==$data4[WORK_DATE]){ 
					$DATA_WORK_FLAG = "";
				}else{
					if($data4[REMARK]){
						$DATA_WORK_FLAG = $data4[REMARK];
					}else{
						$DATA_WORK_FLAG = "ไม่ได้ลงเวลา";
					}
				}
			}else if($data4[WORK_FLAG]==0){
				if($SCANTYPE=="2" && date("Y-m-d")==$data4[WORK_DATE]){ 
					$DATA_EXITTIME = "";
				}
				$DATA_WORK_FLAG = "ปกติ";
			}else if($data4[WORK_FLAG]==5){
				$DATA_WORK_FLAG = $data4[REMARK];
			}
			
			
			$DATA_ABSENT = "";
			$ARR_ABSENT = explode(",", $data4[ABSENT]);
		
			$DATA_AB_NAME = "";
			$DATA_AB_NAME_AFTERNOON = '';
			
			if(substr($ARR_ABSENT[0],0,1)==1 || substr($ARR_ABSENT[0],0,1)==2 || substr($ARR_ABSENT[0],0,1)==3){
				if(substr($ARR_ABSENT[0],-2) != '10' && substr($ARR_ABSENT[0],-2) != '13'){
					$cmd_AB ="select AB_NAME
					from PER_ABSENTTYPE
					where AB_CODE = ".substr($ARR_ABSENT[0],-2);
					//echo $cmd_AB; die();
					$db_dpis_AB->send_cmd_fast($cmd_AB);
					$data_AB_NAME = $db_dpis_AB->get_array_array();
					$DATA_AB_NAME = $data_AB_NAME[AB_NAME];
				}
			}
			if(substr($ARR_ABSENT[1],0,1)==2){
				if(substr($ARR_ABSENT[1],-2) != '10' && substr($ARR_ABSENT[1],-2) != '13'){
					$cmd_AB ="select AB_NAME
					from PER_ABSENTTYPE
					where AB_CODE = ".substr($ARR_ABSENT[1],-2);
					//echo $cmd_AB; die();
					$db_dpis_AB->send_cmd_fast($cmd_AB);
					$data_AB_NAME = $db_dpis_AB->get_array_array();
					$DATA_AB_NAME_AFTERNOON = $data_AB_NAME[AB_NAME];
				}
			}
			
			$dbAbsent = "";
			
			if($data4[ABSENT] !='0,0'){
				if(substr($ARR_ABSENT[0],-2)==10 || substr($ARR_ABSENT[0],-2)==13){
						$dbAbsent = $DATA_AB_NAME;
				}else{
					if(substr($ARR_ABSENT[0],0,1)==3){
						$dbAbsent = $DATA_AB_NAME." (ทั้งวัน)";
					}elseif(substr($ARR_ABSENT[0],0,1)==1){
						$dbAbsent = $DATA_AB_NAME." (ครึ่งเช้า)";
						if(substr($ARR_ABSENT[1],0,1)==2){
							if(substr($ARR_ABSENT[0],0,1)==1){
								$dbAbsent .= ',';
								$dbAbsent .= $DATA_AB_NAME_AFTERNOON." (ครึ่งบ่าย)";
							}
						}
					 }elseif(substr($ARR_ABSENT[0],0,1)==2){
						$dbAbsent = $DATA_AB_NAME." (ครึ่งบ่าย)";
					}elseif(substr($ARR_ABSENT[0],0,1)==0){
						if(substr($ARR_ABSENT[1],0,1)==2){
							$dbAbsent .= $DATA_AB_NAME_AFTERNOON." (ครึ่งบ่าย)";
						}
					}
				 }
			}
			
			$DATA_HOLIDAY = "";
			if($data4[HOLIDAY]==1){
				$DATA_HOLIDAY = "วันหยุด ";
			}
			
			
			/*คำร้อง*/
        
			 $cmd ="select START_FLAG,START_TIME,END_FLAG,END_TIME,
						MEETING_FLAG,SCAN_FLAG,OTH_FLAG,OTH_NOTE,
						REQ_FLAG,REQ_TIME,REQUEST_NOTE,REQ_SPEC,
						REQ_SPEC_NOTE
						from TA_REQUESTTIME
						where PER_ID = $PER_ID AND APPROVE_FLAG=1 AND REQUEST_DATE='".$data4[WORK_DATE]."'";
			$db_dpis_AB->send_cmd($cmd);
			$data_AB = $db_dpis_AB->get_array();
			$Detail_type = "";
			if($data_AB[START_FLAG]==1){
				$Detail_type = "ขอลงเวลาเข้า (".substr($data_AB[START_TIME],0,2).":".substr($data_AB[START_TIME],2,2)." น.) ";
			}
			
			$Detail_type1="";
			if($data_AB[END_FLAG]==1){
				$Detail_type1 = $Detail_type1."ขอลงเวลาออก (".substr($data_AB[END_TIME],0,2).":".substr($data_AB[END_TIME],2,2)." น.) ";
			} 
			
			$Detail_type11=""; 
		   if($data_AB[MEETING_FLAG]==1){
				$Detail_type11 = $Detail_type11."ติดประชุม/สัมมนา/อบรม ";
			}
			
			if($data_AB[SCAN_FLAG]==1){
				$Detail_type11 = $Detail_type11."ลืมสแกน ";
			}
			
			if($data_AB[REQUEST_NOTE]==1){
				$Detail_type11 = $Detail_type11."ลาชั่วโมง ";
			}
			
			if($data_AB[REQ_TIME]==1){
				$Detail_type11 = $Detail_type11."ไปปฏิบัติราชการ ";
			}
			if($data_AB[REQ_SPEC]==1){
				$Detail_type11 = $Detail_type11." ".$data_AB[REQ_SPEC_NOTE]."";
			}
			if($data_AB[OTH_FLAG]==1){
				$Detail_type11 = $Detail_type11." ".$data_AB[OTH_NOTE]." ";
			
			}
				
			$Detail_type2="";
			
			 /*เช็คพ้นจากราชการแล้วหรือยัง*/
        
		   $cmd ="select PER_POSDATE
						from PER_PERSONAL
						where PER_ID = $PER_ID and PER_STATUS=2 ";
			$db_dpis_AB->send_cmd($cmd);
			$data_AB = $db_dpis_AB->get_array();
			
			if($data_AB[PER_POSDATE]){
				 if(substr($data4[WORK_DATE],0,4).substr($data4[WORK_DATE],5,2).substr($data4[WORK_DATE],8,2) >= substr($data_AB[PER_POSDATE],0,4).substr($data_AB[PER_POSDATE],5,2).substr($data_AB[PER_POSDATE],8,2)){
					$DATA_WORK_FLAG = "";
				 }
			}
			
			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $data_no, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $DATA_WORK_DATE, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $DATA_ENTTIME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $DATA_EXITTIME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $DATA_WC_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, $DATA_WORK_FLAG, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 6, $dbAbsent, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 7, $DATA_HOLIDAY.$Detail_type.$Detail_type1.$Detail_type11.$Detail_type2, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"P0121.xls\"");
	header("Content-Disposition: inline; filename=\"P0121.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>