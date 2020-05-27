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

		$worksheet->set_column(0, 0, 8);
		$worksheet->set_column(1, 1, 10);
		$worksheet->set_column(2, 2, 8);
		$worksheet->set_column(3, 3, 8);
		$worksheet->set_column(4, 4, 15);
		$worksheet->set_column(5, 5, 10);
		$worksheet->set_column(6, 6, 10);
		$worksheet->set_column(7, 7, 10);
		$worksheet->set_column(8, 8, 15);
		
		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "วันที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "เวลาเข้า", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "เวลาออก", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "รอบเวลา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "สถานะการลงเวลา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "สถานะการลา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "สถานะวัน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // end if
	
	if(!$search_date_min){
    	$bgnbackMonth= date('Y-m',strtotime('-1 month'));
        $search_date_min= "01/".substr($bgnbackMonth,5,2)."/".(substr($bgnbackMonth,0,4)+543);
    }
    
    if(!$search_date_max){
        $bgnbackMonth= date('Y-m',strtotime('-1 month'))."-01";
        $MonthEND=date('Y-m',strtotime('-1 month'))."-".date("t",strtotime($bgnbackMonth));
        $search_date_max= substr($MonthEND,8,2)."/".substr($MonthEND,5,2)."/".(substr($MonthEND,0,4)+543);
        
    }
	
	if($search_date_min && $search_date_max){ 
                 $tmpsearch_date_min =  save_date($search_date_min);
                 $tmpsearch_date_max =  save_date($search_date_max);
                 $condition= " AND ( wt.WORK_DATE BETWEEN  to_date('$tmpsearch_date_min 00:00:00','yyyy-mm-dd hh24:mi:ss')  and to_date('$tmpsearch_date_max  23:59:59','yyyy-mm-dd hh24:mi:ss')) ";
            }else if($search_date_min && empty($search_date_max)){ 
                 $tmpsearch_date_min =  save_date($search_date_min);
                 $condition = " AND ( wt.WORK_DATE BETWEEN  to_date('$tmpsearch_date_min 00:00:00','yyyy-mm-dd hh24:mi:ss') and to_date('$tmpsearch_date_min  23:59:59','yyyy-mm-dd hh24:mi:ss')) ";
            }else if(empty($search_date_min) && $search_date_max){ 
                 $tmpsearch_date_max =  save_date($search_date_max);
                 $condition = " AND ( wt.WORK_DATE BETWEEN  to_date('$tmpsearch_date_max 00:00:00','yyyy-mm-dd hh24:mi:ss') and to_date('$tmpsearch_date_max  23:59:59','yyyy-mm-dd hh24:mi:ss')) ";
    		}
	
	
	$cmd = " select  TO_CHAR(wt.WORK_DATE,'yyyy-mm-dd') AS WORK_DATE,
				TO_CHAR(wt.APV_ENTTIME,'hh24:mi') AS APV_ENTTIME,
				TO_CHAR(wt.APV_EXITTIME,'hh24:mi') AS APV_EXITTIME,cl.WC_NAME,wt.ABSENT_FLAG,
				wt.WORK_FLAG,wt.HOLIDAY_FLAG,wt.REMARK,
				TO_CHAR(wt.SCAN_ENTTIME,'hh24:mi') AS SCAN_ENTTIME,
				TO_CHAR(wt.SCAN_EXITTIME,'hh24:mi') AS SCAN_EXITTIME,
				case when (exists (select null from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=wt.PER_ID and trim(ab_code) not in ('10','13')and 
					  pa.abs_startdate < cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19)))) then '3'||
						(select trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=wt.PER_ID and trim(ab_code) not in ('10','13') and
					  pa.abs_startdate < cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19))) || ',0'
				else 
		
					nvl((select '3'||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=wt.PER_ID and abs_startperiod=3 and trim(ab_code) not in ('10','13')and 
					  pa.abs_startdate = cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19)))
					,
		
					  nvl(
						(select to_char(abs_startperiod)||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=wt.PER_ID and abs_startperiod<>2 and /*trim(ab_code) not in ('10','13')and */
						pa.abs_startdate = cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19))),
						   (
							  nvl((select to_char(abs_startperiod)||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=wt.PER_ID and abs_startperiod<>2 and trim(ab_code) not in ('10','13')and 
									pa.abs_startdate = cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19))),
								  nvl((select to_char(abs_endperiod)||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=wt.PER_ID and abs_endperiod<>2 and trim(ab_code) not in ('10','13')and 
									pa.abs_startdate < cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19))),
									0)
								)
						  )
						) || ',' ||
						nvl(
						(select to_char(abs_startperiod)||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=wt.PER_ID and abs_startperiod=2 and trim(ab_code) not in ('10','13')and 
						pa.abs_startdate = cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19))),
						   (
							  nvl((select to_char(abs_startperiod)||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=wt.PER_ID and abs_startperiod=2 and trim(ab_code) not in ('10','13')and 
									pa.abs_startdate = cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19))),
								  0
								)
						  )
						)
		
					  )
		   end ABSENT,ctl.CLOSE_DATE
				
				from PER_WORK_TIME wt 
				left join PER_WORK_CYCLE cl on(cl.WC_CODE=wt.WC_CODE) 
				left join PER_WORK_TIME_CONTROL ctl on(ctl.CONTROL_ID=wt.CONTROL_ID) 
				WHERE wt.PER_ID =$PER_ID  
				$condition
			   order by  wt.WORK_DATE ASC ";

	$count_data = $db_dpis4->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$xlsRow = 0;
		
		$worksheet->write($xlsRow, 0, "ข้อมูลประวัติการลงเวลา", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 1;
		$data_no = 0;
		while($data4 = $db_dpis4->get_array()){
			$data_count++;
			$data_no++;
			$DATA_WORK_DATE = show_date_format($data4[WORK_DATE], $DATE_DISPLAY);
        
        
			if($data4[APV_ENTTIME]){
				$DATA_ENTTIME = $data4[APV_ENTTIME];
			}else{
				//$DATA_ENTTIME = $data4[SCAN_ENTTIME];
				$DATA_ENTTIME = "";
			}
			
			
			if($data4[APV_EXITTIME]){
				$DATA_EXITTIME = $data4[APV_EXITTIME];
			}else{
				//$DATA_EXITTIME = $data4[SCAN_EXITTIME];
				$DATA_EXITTIME = "";
			}

			
			$DATA_WC_NAME = $data4[WC_NAME]; 
			
			
			$DATA_WORK_FLAG = "";
			if($data4[CLOSE_DATE]){
				if($data4[WORK_FLAG]==1){
					if($data4[REMARK]){
						$DATA_WORK_FLAG = $data4[REMARK];
					}else{
						$DATA_WORK_FLAG = "สาย";
					}
					$HIDLATE1++;
				}else if($data4[WORK_FLAG]==2){
					// 0 = ขาด, 2 = ลาบ่าย, 10= ลาเช้า, 12= ลาเช้าและลาบ่าย, 30 = ลาทั้งวัน
					/*if($data4[ABSENT_FLAG] !=0){
						$DATA_WORK_FLAG = "";
					}else{
						$DATA_WORK_FLAG = "ขาดราชการ";
					}*/
					if($data4[ABSENT_FLAG] !=0){
						
						if(substr($ARR_ABSENT[0],0,1)==0 || substr($ARR_ABSENT[1],0,1)==0){
							$DATA_WORK_FLAG = "ขาดราชการ";
						}else{
							$DATA_WORK_FLAG = "";
						} 
						
					}else{
						$DATA_WORK_FLAG = "ขาดราชการ";
					}
				}else if($data4[WORK_FLAG]==3){
					if($data4[REMARK]){
						$DATA_WORK_FLAG = "ปกติ";
					}else{
						$DATA_WORK_FLAG = "ออกก่อน";
					}
				}else if($data4[WORK_FLAG]==4){
					if($data4[REMARK]){
						$DATA_WORK_FLAG = "ปกติ";
					}else{
						$DATA_WORK_FLAG = "ไม่ได้ลงเวลา";
					}
				}else if($data4[WORK_FLAG]==0){
					$DATA_WORK_FLAG = "ปกติ";
				}else if($data4[WORK_FLAG]==5){
                	$DATA_WORK_FLAG = "ปกติ";
				}
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
					$db_dpis_AB->send_cmd($cmd_AB);
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
					$db_dpis_AB->send_cmd($cmd_AB);
					$data_AB_NAME = $db_dpis_AB->get_array_array();
					$DATA_AB_NAME_AFTERNOON = $data_AB_NAME[AB_NAME];
				}
			}
			
			if($data4[ABSENT] !='0,0'){
				if(substr($ARR_ABSENT[0],-2)==10 || substr($ARR_ABSENT[0],-2)==13){
						$DATA_ABSENT = $DATA_AB_NAME;
				} 
				else {
					if(substr($ARR_ABSENT[0],0,1)==3){
							$DATA_ABSENT = $DATA_AB_NAME." (ทั้งวัน)";
					} 
					else {
						if(substr($ARR_ABSENT[0],0,1)==1){
								$DATA_ABSENT = $DATA_AB_NAME." (ครึ่งเช้า)";
						} 
						if(substr($ARR_ABSENT[1],0,1)==2){
							if(substr($ARR_ABSENT[0],0,1)==1)
								$DATA_ABSENT .= ',';
							$DATA_ABSENT .= $DATA_AB_NAME_AFTERNOON." (ครึ่งบ่าย)";
						} 
					}
				}
			}
			
			
			$DATA_HOLIDAY = "";
			if($data4[HOLIDAY_FLAG]==1){
				$DATA_HOLIDAY = "วันหยุด";
			}

			
			$DATA_REMARK = "";
			if($data4[REMARK]){
				$DATA_REMARK = $data4[REMARK];
			}

			
			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $data_no, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $DATA_WORK_DATE, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $DATA_ENTTIME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $DATA_EXITTIME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $DATA_WC_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, $DATA_WORK_FLAG, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 6, $DATA_ABSENT, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 7, $DATA_HOLIDAY, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 8, $DATA_REMARK, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
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
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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