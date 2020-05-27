<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd); 
	$db_dpis_AB = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$report_title = trim(iconv("utf-8","tis620",urldecode($report_title)));
	
	

	if(!empty($TMENU)){$TMENU_S=$TMENU;}else{$TMENU_S="T0205";}
	$worksheet = &$workbook->addworksheet($TMENU_S);
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 8);
		$worksheet->set_column(1, 1, 10);
		$worksheet->set_column(2, 2, 10);
		$worksheet->set_column(3, 3, 10);
		$worksheet->set_column(4, 4, 10);
		$worksheet->set_column(5, 5, 10);
		$worksheet->set_column(6, 6, 20);
		$worksheet->set_column(7, 7, 20);
		$worksheet->set_column(8, 8, 20);
		$worksheet->set_column(9, 9, 13);
		$worksheet->set_column(10, 10, 13);
		$worksheet->set_column(11, 11, 20);
		
		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "วันที่ปฏิบัติราชการ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "เวลาเข้า (Process)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "เวลาออก (Process)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "เวลาเข้า (Approved)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "เวลาออก (Approved)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "ชื่อ-สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "สำนัก/กอง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "รอบการลงเวลา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 9, "สถานะการลงเวลา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 10, "สถานะการลา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 11, "ประเภทบุคลากร", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // end if


  


		$cmd = "	select 	org.ORG_NAME,col.CLOSE_YEAR,col.CONTROL_ID,col.CLOSE_MONTH,
                                    TO_CHAR(wt.WORK_DATE,'yyyy-mm-dd') AS WORK_DATE,
                                    TO_CHAR(wt.APV_ENTTIME,'hh24:mi') AS APV_ENTTIME,
                                    TO_CHAR(wt.APV_EXITTIME,'hh24:mi') AS APV_EXITTIME,
                                    psn.PER_TYPE,wt.PER_ID,
                                    pn.PN_SHORTNAME||psn.PER_NAME||' '||psn.PER_SURNAME  AS FULLNAME_SHOW,
                                    c.ORG_ID,d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
                                    j.ORG_ID AS POT_ORG_ID,
                                    wc.WC_NAME,wt.REMARK,wt.WORK_FLAG,
                                    col.CLOSE_DATE,wt.ABSENT_FLAG,
                                    TO_CHAR(wt.SCAN_ENTTIME,'hh24:mi') AS SCAN_ENTTIME,
                                    TO_CHAR(wt.SCAN_EXITTIME,'hh24:mi') AS SCAN_EXITTIME,
									wt.PER_ID,
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
                               end ABSENT
                                from  PER_WORK_TIME_CONTROL col  
                                left join  PER_ORG_ASS org on(org.ORG_ID=col.DEPARTMENT_ID)
                                left join PER_WORK_TIME wt on(wt.CONTROL_ID=col.CONTROL_ID)
                                left join PER_PERSONAL psn on(psn.PER_ID=wt.PER_ID)
                                left join PER_PRENAME pn on(pn.PN_CODE=psn.PN_CODE) 
                                left join PER_POSITION c on(c.POS_ID=psn.POS_ID) 
                              	left join PER_POS_EMP d on(d.POEM_ID=psn.POEM_ID) 
                              	left join PER_POS_EMPSER e on(e.POEMS_ID=psn.POEMS_ID) 
                                left join PER_POS_TEMP j on (j.POT_ID=psn.POT_ID)
                                left join PER_WORK_CYCLE wc on(wc.WC_CODE=wt.WC_CODE)
                                WHERE col.CONTROL_ID=".$_GET['CONTROL_ID']."
			order by 	wt.WORK_DATE ASC ,c.ORG_ID ASC,d.ORG_ID ASC,e.ORG_ID ASC,j.ORG_ID ASC,psn.PER_NAME ASC , psn.PER_SURNAME ASC ";
//echo $cmd; die();
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 1;
		$data_num = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_num++;
			
			$DATA_WORK_DATE = show_date_format($data[WORK_DATE], $DATE_DISPLAY);
			
			$DATA_APV_ENTTIME = "";
			 if( $data[APV_ENTTIME]){
				 $DATA_APV_ENTTIME = $data[APV_ENTTIME]." น.";
			 }
		   
			 $DATA_APV_EXITTIME = "";
			 if( $data[APV_EXITTIME]){
				 $DATA_APV_EXITTIME = $data[APV_EXITTIME]." น.";
			 }
			 
			 $DATA_SCAN_ENTTIME = "";
			 if($data[APV_ENTTIME]!=$data[SCAN_ENTTIME]){
				 if( $data[SCAN_ENTTIME]){
					 $DATA_SCAN_ENTTIME = $data[SCAN_ENTTIME]." น.";
				 }
			 }
		   
			 $DATA_SCAN_EXITTIME = "";
			 if($data[APV_EXITTIME]!=$data[SCAN_EXITTIME]){
				 if( $data[SCAN_EXITTIME]){
					 $DATA_SCAN_EXITTIME = $data[SCAN_EXITTIME]." น.";
				 }
			}
			
			$DATA_FULLNAME_SHOW = $data[FULLNAME_SHOW];
			
			$DATA_PER_TYPE = $data[PER_TYPE];
			if($DATA_PER_TYPE==1){ 
				$TMP_PER_TYPE = "ข้าราชการ";
				$TMP_ORG_ID = $data[ORG_ID];
			}elseif($DATA_PER_TYPE==2){ 
				$TMP_PER_TYPE = "ลูกจ้างประจำ";
				$TMP_ORG_ID = $data[EMP_ORG_ID];   
			 } elseif ($DATA_PER_TYPE == 3) {
				$TMP_PER_TYPE = "พนักงานราชการ";
				$TMP_ORG_ID = $data[EMPS_ORG_ID];					
			} elseif ($DATA_PER_TYPE == 4) {
				$TMP_PER_TYPE = "ลูกจ้างชั่วคราว";
				$TMP_ORG_ID = $data[POT_ORG_ID];					
				
			}else{
				$TMP_PER_TYPE = "ลูกจ้างโครงการ";
			}
			
			if($TMP_ORG_ID){
				$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$TMP_ORG_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$DATA_ORG_NAME = $data2[ORG_NAME];
			}
			
			 $DATA_WC_NAME = "";
			 if( $data[WC_NAME]){
				 $DATA_WC_NAME = $data[WC_NAME];
			 }
			 
			 
			 $DATA_WORK_FLAG = "";
        if($data[WORK_FLAG]==1){
        	if($data[REMARK]){
				$DATA_WORK_FLAG = $data[REMARK];
			}else{
				$DATA_WORK_FLAG = "สาย";
			}
        }else if($data[WORK_FLAG]==2){
        	// 0 = ขาด, 2 = ลาบ่าย, 10= ลาเช้า, 12= ลาเช้าและลาบ่าย, 30 = ลาทั้งวัน
        	if($data[ABSENT_FLAG] !=0){
        		$DATA_WORK_FLAG = "";
            }else{
            	$DATA_WORK_FLAG = "ขาดราชการ";
            }
        }else if($data[WORK_FLAG]==3){
        	if($data[REMARK]){
				$DATA_WORK_FLAG = $data[REMARK];
			}else{
				$DATA_WORK_FLAG = "ออกก่อน";
			}
        }else if($data[WORK_FLAG]==4){
			if($data[REMARK]){
				$DATA_WORK_FLAG = $data[REMARK];
			}else{
				$DATA_WORK_FLAG = "ไม่ได้ลงเวลา";
			}
        }else if($data[WORK_FLAG]==0){
        	$DATA_WORK_FLAG = "ปกติ";
		}else if($data[WORK_FLAG]==5){
        	$DATA_WORK_FLAG = $data[REMARK];
        }
		
		$DATA_PER_ID = $data[PER_ID];
		
		$DATA_ABSENT = "";
        $ARR_ABSENT = explode(",", $data[ABSENT]);
	
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
        
        if($data[ABSENT] !='0,0'){
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
			
			
			

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $data_num, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $DATA_WORK_DATE, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $DATA_SCAN_ENTTIME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $DATA_SCAN_EXITTIME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $DATA_APV_ENTTIME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 5, $DATA_APV_EXITTIME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 6, $DATA_FULLNAME_SHOW, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 7, $DATA_ORG_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 8, $DATA_WC_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 9, $DATA_WORK_FLAG, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 10, $DATA_ABSENT, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 11, $TMP_PER_TYPE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
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
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"".$TMENU_S.".xls\"");
	header("Content-Disposition: inline; filename=\"".$TMENU_S.".xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>