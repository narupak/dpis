<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);
	
	

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";
	$workbook = new writeexcel_workbook($fname);
	$report_title = trim(iconv("utf-8","tis620",urldecode($report_title)));
	$worksheet = &$workbook->addworksheet('T0301');
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


 
    
    //หาว่าอยู่กลุ่ม กจ. กรม หรือไม่--------------------------------
    $cmd4 = "	select	 b.CODE from	user_detail a, user_group b
					where a.group_id=b.id AND a.ID=".$SESS_USERID;
    $db_dpis2->send_cmd($cmd4);
    $data4 = $db_dpis2->get_array();
    if ($data4[CODE]) {
        $NAME_GROUP_HRD = $data4[CODE];
    }else{
        $NAME_GROUP_HRD = "";
    }
    
   
    if ( ($SESS_USERGROUP !=1 && $NAME_GROUP_HRD!='HRD')  ){
		$select_org_structure=1;
        $tCon="(";
        for ($i=0; $i < count($SESS_AuditArray); $i++) {
            if ($i>0)
                $tCon .= " or ";
            $tCon .= "(a.ORG_ID=" .$SESS_AuditArray[$i][0];
            if ($SESS_AuditArray[$i][1] != 0)
                $tCon .= ' and a.ORG_ID_1='. $SESS_AuditArray[$i][1];
            $tCon .= ")";
        }
        $tCon .= ")";
        $arr_search_condition[] = $tCon;
    	
        if($search_org_id && !$search_org_id_1 ){
        	$arr_search_condition[] = "(a.ORG_ID=$search_org_id )";
        }else if($search_org_id && $search_org_id_1){
        	$arr_search_condition[] = "(a.ORG_ID=$search_org_id AND a.ORG_ID_1=$search_org_id_1 )";
        }                                         
    }else{
                
            if($search_org_id_1 && !$search_org_id_2){ /* Release 5.1.0.4 */
                if($select_org_structure==0){ //โครงสร้างตามกฎหมาย
                    $arr_search_condition[] = "(c.ORG_ID_1=$search_org_id_1 or d.ORG_ID_1=$search_org_id_1 or e.ORG_ID_1=$search_org_id_1 or j.ORG_ID_1=$search_org_id_1)";
                }else if($select_org_structure==1){ //โครงสร้างตามมอบหมายงาน
                    $arr_search_condition[] = "(a.ORG_ID_1=$search_org_id_1)";
                }
            }elseif($search_org_id_1 && $search_org_id_2){ /* Release 5.1.0.4 */
                if($select_org_structure==0){ //โครงสร้างตามกฎหมาย
                    $arr_search_condition[] = "(c.ORG_ID_2=$search_org_id_2 or d.ORG_ID_2=$search_org_id_2 or e.ORG_ID_2=$search_org_id_2 or j.ORG_ID_2=$search_org_id_2)";
                }else if($select_org_structure==1){ //โครงสร้างตามมอบหมายงาน
                    $arr_search_condition[] = "(a.ORG_ID_2=$search_org_id_2)";
                }
            
            }elseif($search_org_id){
                if($select_org_structure==0){
                    $arr_search_condition[] = "(c.ORG_ID=$search_org_id or d.ORG_ID=$search_org_id or e.ORG_ID=$search_org_id or j.ORG_ID=$search_org_id)";
                }else if($select_org_structure==1){
                    $arr_search_condition[] = "(a.ORG_ID=$search_org_id)";
                }
            }elseif($search_department_id){
                $arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
            }elseif($search_ministry_id){
                unset($arr_department);
                $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id and OL_CODE='02' ";
                if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
                $db_dpis->send_cmd($cmd);
                while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
                $arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
            }elseif($PROVINCE_CODE){
                $cmd = " select distinct ORG_ID_REF from PER_ORG where PV_CODE='$PROVINCE_CODE' and OL_CODE='03' ";
                if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
                $db_dpis->send_cmd($cmd);
                while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID_REF];
                $arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
            } // end if  
	}
    

    if(trim($search_per_type)) 	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
    
    if($search_date_min && $search_date_max){ 
		 $tmpsearch_date_min =  save_date($search_date_min)." 00:00:00";
         $tmpsearch_date_max =  save_date($search_date_max)." 23:59:59";
         $tmpsearch_date_max1 =  save_date($search_date_max);
         
         $arr_search_condition[] = " (att.TIME_STAMP  BETWEEN to_date('$tmpsearch_date_min','yyyy-mm-dd hh24:mi:ss')   AND to_date('$tmpsearch_date_max','yyyy-mm-dd hh24:mi:ss')) ";
       
	}

    $search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);



		$cmd = " 
					select		 q1.*
                             from (
					select 	DISTINCT
                                        abs.PER_ID,abs.ABS_STARTDATE,abs.ABS_STARTPERIOD,abs.ABS_ENDDATE,abs.ABS_ENDPERIOD,
                                        TO_CHAR(att.TIME_STAMP,'yyyy-mm-dd') AS TIME_STAMP,
										(SELECT TO_CHAR(min(TIME_STAMP),'HH24:MI:SS') 
                                        		FROM PER_TIME_ATTENDANCE  
                 								where PER_ID=abs.PER_ID AND TO_CHAR(TIME_STAMP,'yyyy-mm-dd') =TO_CHAR(att.TIME_STAMP,'yyyy-mm-dd')
                                                ) AS HHII ,
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
                                left join PER_WORK_CYCLEHIS his on ( his.PER_ID=a.PER_ID and (att.TIME_STAMP between to_date(his.START_DATE, 'YYYY-MM-DD hh24:mi:ss') and to_date(his.END_DATE,'YYYY-MM-DD hh24:mi:ss')) )
                                left join PER_WORK_CYCLE l on (l.wc_code=his.wc_code)
                                
                            WHERE abs.AB_CODE NOT IN(10,13) AND att.TIME_STAMP IS NOT NULL
                            $search_condition
                            
                            AND (abs.ABS_ENDPERIOD = 3 or 
                                 (abs.ABS_ENDPERIOD = 1 and att.TIME_STAMP <=
                                  TRUNC(att.TIME_STAMP)+(((to_number(substr(l.End_LateTime,1,2))*60)+to_number(substr(l.End_LateTime,3,2)))/1440)+(59/86400) 
                                 ) or
                                 (abs.ABS_ENDPERIOD = 2 and att.TIME_STAMP >=
                                 
                                    TRUNC(att.TIME_STAMP)+(((to_number(substr(l.End_LateTime,1,2))*60)+to_number(substr(l.End_LateTime,3,2)))/1440)+(59/86400) 
                                 
                                   and ((SELECT max(TIME_STAMP)	FROM PER_TIME_ATTENDANCE  
                                          where PER_ID=abs.PER_ID AND TO_CHAR(TIME_STAMP,'yyyy-mm-dd')=TO_CHAR(att.TIME_STAMP,'yyyy-mm-dd')
                                        ) =
                                        (SELECT min(TIME_STAMP)	FROM PER_TIME_ATTENDANCE  
                                          where PER_ID=abs.PER_ID AND TO_CHAR(TIME_STAMP,'yyyy-mm-dd')=TO_CHAR(att.TIME_STAMP,'yyyy-mm-dd')
                                        )
                                        or
                                        (SELECT max(TIME_STAMP)	FROM PER_TIME_ATTENDANCE  
                                          where PER_ID=abs.PER_ID AND TO_CHAR(TIME_STAMP,'yyyy-mm-dd')=TO_CHAR(att.TIME_STAMP,'yyyy-mm-dd')
                                        ) >= (case when l.wc_code=-1 
                                                then (SELECT min(TIME_STAMP)	FROM PER_TIME_ATTENDANCE  
                                                        where PER_ID=abs.PER_ID AND TO_CHAR(TIME_STAMP,'yyyy-mm-dd')=TO_CHAR(att.TIME_STAMP,'yyyy-mm-dd')
                                                      )+(8/24) 
                                                else TRUNC(att.TIME_STAMP)+NextDay_Exit+((to_number(substr(l.WC_End,1,2))*60)+to_number(substr(l.WC_End,3,2))/1440)
                                             end)
                                      ) 
                                 )
                                )
						 ) q1  ORDER BY q1.TIME_STAMP,q1.HHII
						";

	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();

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
			$TMP_HHII = $data[HHII];

			
			$DATA_FULLNAME_SHOW = trim($data[FULLNAME_SHOW]);

			
			$DATA_AB_NAME = trim($data[AB_NAME]); 

			 
			 $DATA_ABS_STARTDATE = show_date_format($data[ABS_STARTDATE], $DATE_DISPLAY); 

			 
			 $DATA_ABS_ENDDATE = show_date_format($data[ABS_ENDDATE], $DATE_DISPLAY); 

			 
			 $DATA_ABS_STARTPERIOD ="";
			if($data[ABS_STARTPERIOD]=="1"){
				$DATA_ABS_STARTPERIOD =" (ครึ่งเช้า)";
			}elseif($data[ABS_STARTPERIOD]=="2"){
				$DATA_ABS_STARTPERIOD =" (ครึ่งบ่าย)";
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
	header("Content-Type: application/x-msexcel; name=\"T0301.xls\"");
	header("Content-Disposition: inline; filename=\"T0301.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>