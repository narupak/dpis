<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd); 
	$db_dpis_AB = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if(!$sort_by) $sort_by=1;
    if(!$sort_type) $sort_type="1:desc";
    $arrSort=explode(":",$sort_type);
    $SortType[$arrSort[0]]	=$arrSort[1];
    if(!$order_by) $order_by=1;
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$report_title = trim(iconv("utf-8","tis620",urldecode($report_title)));
	$worksheet = &$workbook->addworksheet('T0303_Preview');
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 8);
		$worksheet->set_column(1, 1, 13);
		$worksheet->set_column(2, 2, 8);
		$worksheet->set_column(3, 3, 8);
		$worksheet->set_column(4, 4, 25);
		$worksheet->set_column(5, 5, 25);
		$worksheet->set_column(6, 6, 12);
		$worksheet->set_column(7, 7, 12);
		$worksheet->set_column(8, 8, 12);
		$worksheet->set_column(9, 9, 35);
		
		$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "วันที่ปฏิบัติราชการ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "เวลาเข้า", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "เวลาออก", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "ชื่อ-สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "สำนัก/กองตามกฏหมาย", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "รอบการลงเวลา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "สถานะการลงเวลา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "สถานะการลา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 9, "คำร้อง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // end if


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
    

   
    
    
    if(empty($TIME_STAMP_START)){ 
    	$TIME_STAMP_START =  date ("d", strtotime("-1 day", strtotime(date("Y-m-d"))))."/".date("m")."/".(date("Y")+543); 
        
    }
   
   
   
    if(empty($TIME_STAMP_END)){ 
    	$TIME_STAMP_END =  date ("d", strtotime("-1 day", strtotime(date("Y-m-d"))))."/".date("m")."/".(date("Y")+543); 
        
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
        	//$arr_search_condition[] = "(a.ORG_ID=$search_org_id AND a.ORG_ID_1=$search_org_id_1 )";
        }
       

        }else{
        	if($search_org_id_1){ /* Release 5.1.0.4 */
                if($select_org_structure==0){ //โครงสร้างตามกฎหมาย

                    $arr_search_condition[] = "(c.ORG_ID_1=$search_org_id_1 or d.ORG_ID_1=$search_org_id_1 or e.ORG_ID_1=$search_org_id_1 or j.ORG_ID_1=$search_org_id_1)";
                }else if($select_org_structure==1){ //โครงสร้างตามมอบหมายงาน
                    $arr_search_condition[] = "(a.ORG_ID_1=$search_org_id_1)";
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
    
    
    if(trim($search_name)) $arr_search_condition[] = "(a.PER_NAME like '".trim($search_name)."%' or UPPER(a.PER_ENG_NAME) like UPPER('".trim($search_name)."%'))";
   	if(trim($search_surname)) $arr_search_condition[] = "(a.PER_SURNAME like '".trim($search_surname)."%' or UPPER(a.PER_ENG_SURNAME) like UPPER('".trim($search_surname)."%'))";


   // if(trim($search_time_att))  $arr_search_condition[] = "(att.TA_CODE='$search_time_att')";
    
    if($SEARCH_WORK_FLAG!=""){
             if($SEARCH_WORK_FLAG=="0") {
                $arr_search_condition[] = "(x.WORK_FLAG = 0 OR ( x.WORK_FLAG =2 AND x.ABSENT !='313,0' AND x.ABSENT !='313' AND x.ABSENT !='0,0' AND x.ABSENT !='0' ))";
            }else{
            	$arr_search_condition[] = "(x.WORK_FLAG=1 OR  ( (x.WORK_FLAG =2 AND x.ABSENT ='0') OR (x.WORK_FLAG =2 AND x.ABSENT ='0,0') OR (x.WORK_FLAG =2 AND x.ABSENT ='313,0') OR (x.WORK_FLAG =2 AND x.ABSENT ='313')) OR x.WORK_FLAG=3 OR x.WORK_FLAG=4 OR x.WORK_FLAG=5)";
            }
    }
  	
    
    if($TIME_STAMP_START & $TIME_STAMP_END){
    	$YMD_TIME_START = (substr($TIME_STAMP_START,6,4)-543)."-".substr($TIME_STAMP_START,3,2)."-".substr($TIME_STAMP_START,0,2);
    	$YMD_TIME_END = (substr($TIME_STAMP_END,6,4)-543)."-".substr($TIME_STAMP_END,3,2)."-".substr($TIME_STAMP_END,0,2);
    	

                                              
   }else if($TIME_STAMP_START & !$TIME_STAMP_END){
    	$YMD_TIME_START = (substr($TIME_STAMP_START,6,4)-543)."-".substr($TIME_STAMP_START,3,2)."-".substr($TIME_STAMP_START,0,2);
        $YMD_TIME_END = (substr($TIME_STAMP_START,6,4)-543)."-".substr($TIME_STAMP_START,3,2)."-".substr($TIME_STAMP_START,0,2);
    	
    }else if(!$TIME_STAMP_START & $TIME_STAMP_END){
    	$YMD_TIME_START = (substr($TIME_STAMP_END,6,4)-543)."-".substr($TIME_STAMP_END,3,2)."-".substr($TIME_STAMP_END,0,2);
    	$YMD_TIME_END = (substr($TIME_STAMP_END,6,4)-543)."-".substr($TIME_STAMP_END,3,2)."-".substr($TIME_STAMP_END,0,2);
    }

	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

		$cmd_con = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_SCANTYPE' ";
		$db_dpis->send_cmd($cmd_con);
		$data_con = $db_dpis->get_array();
		$SCANTYPE = $data_con[CONFIG_VALUE];
		
		if($select_org_structure==0){
			$LAW_OR_ASS=1;
		}else{
			$LAW_OR_ASS=2;
		}
		
		if($search_org_id){
			$conORG_ID = $search_org_id;
		}else{
			$conORG_ID = $search_department_id;
		}

		$cmd = file_get_contents('../GetWorkTimeByOrgID.sql');	
        $cmd=str_ireplace(":BEGINDATEAT","'".$YMD_TIME_START." 00:00:00'",$cmd);
        $cmd=str_ireplace(":TODATEAT","'".$YMD_TIME_END." 23:59:59'",$cmd);
        $cmd=str_ireplace(":ORG_ID","'".$conORG_ID."'",$cmd);
        $cmd=str_ireplace(":LAW_OR_ASS",$LAW_OR_ASS,$cmd);
        $cmd=str_ireplace(":PER_TYPE",0,$cmd);	
        $cmd=str_ireplace(":SCANTYPE",$SCANTYPE,$cmd);
		
		if($order_by==1){	//(ค่าเริ่มต้น)
       		$order_str = "ORDER BY q1.WORK_DATE ".$SortType[$order_by].",q1.ORG_ID ".$SortType[$order_by].",q1.EMP_ORG_ID ".$SortType[$order_by].",q1.EMPS_ORG_ID ".$SortType[$order_by].",q1.POT_ORG_ID ".$SortType[$order_by].",q1.FULLNAME_SHOW ".$SortType[$order_by];
        }else if($order_by==2){	//
            $order_str = "ORDER BY q1.FULLNAME_SHOW ".$SortType[$order_by];
        } elseif($order_by==3) {	//สำนัก/กอง
            $order_str = "ORDER BY q1.ORG_ID ".$SortType[$order_by].",q1.EMP_ORG_ID ".$SortType[$order_by].",q1.EMPS_ORG_ID ".$SortType[$order_by].",q1.POT_ORG_ID ".$SortType[$order_by];
        } elseif($order_by==4) {	//รอบฯ
            $order_str = "ORDER BY q1.WC_NAME ".$SortType[$order_by];
        }
		
		$cmd = " 
		select  q1.* from (  
		select  distinct TO_CHAR(x.WORK_DATE,'yyyy-mm-dd') AS WORK_DATE ,
        x.PER_ID,x.WC_CODE,x.ENTTIME,x.EXITTIME,x.HOLIDAY,
        x.ABSENT,x.WORK_FLAG,x.REMARK,a.PER_TYPE,
        g.PN_NAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW,
        c.ORG_ID,d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
         j.ORG_ID AS POT_ORG_ID,wc.WC_NAME
        from ( ".$cmd."
        
        ) x
        left join per_personal a on(a.PER_ID=x.PER_ID)
        left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE) 
        left join PER_POSITION c on(c.POS_ID=a.POS_ID) 
        left join PER_POS_EMP d on(d.POEM_ID=a.POEM_ID) 
        left join PER_POS_EMPSER e on(e.POEMS_ID=a.POEMS_ID) 
        left join PER_POS_TEMP j on (j.POT_ID=a.POT_ID)
        left join PER_WORK_CYCLE  wc on(wc.WC_CODE=x.WC_CODE) 
        left join PER_TIME_ATTENDANCE att  on(att.PER_ID=x.PER_ID)
        WHERE 1=1 $search_condition 
		 )  q1
					$order_str
					";
//echo "<pre>".$cmd;
//die();
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 1;
		$data_row = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;
			
			$DATA_WORK_DATE = show_date_format($data[WORK_DATE], $DATE_DISPLAY);
			$DATA_SCAN_ENTTIME = $data[ENTTIME];
        	$DATA_SCAN_EXITTIME = $data[EXITTIME];
			$DATA_FULLNAME_SHOW= $data[FULLNAME_SHOW];
			
			$DATA_PER_ID = $data[PER_ID];
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
				
			} 
					
			if($TMP_ORG_ID){
				$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$TMP_ORG_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$DATA_ORG_NAME = $data2[ORG_NAME];
			}
			
			$DATA_WC_NAME= $data[WC_NAME];
			
			$DATA_WORK_FLAG = "";
			if($data[WORK_FLAG]==1){
				if($data[REMARK]){
					$DATA_WORK_FLAG = $data[REMARK];
				}else{
					$DATA_WORK_FLAG = "สาย";
				}

			}else if($data[WORK_FLAG]==2){
				$cmd = " select cl.END_LATETIME
                            FROM  PER_WORK_CYCLEHIS cyh
                            left join PER_WORK_CYCLE cl on(cl.WC_CODE=cyh.WC_CODE) 
                            WHERE cyh.PER_ID =$DATA_PER_ID 
                            AND 
                            (sysdate between to_date(cyh.START_DATE,'yyyy-mm-dd hh24:mi:ss') AND case 
							when cyh.END_DATE is not null then to_date(cyh.END_DATE,'yyyy-mm-dd hh24:mi:ss') 
                            else sysdate end ) ";
				$db_dpis2->send_cmd($cmd);
				$data_today = $db_dpis2->get_array();
				$TODAY_END_LATETIME = trim($data_today[END_LATETIME]); // สิ้นสุดเวลาสาย
				
				if($TODAY_END_LATETIME){
					$WORK_NOW = date("Y-m-d"); // วันที่ปัจจุบัน
					$TODAY_TIME_NOW = date("Hi"); // เวลาปัจจุบัน
					// 0 = ขาด, 2 = ลาบ่าย, 10= ลาเช้า, 12= ลาเช้าและลาบ่าย, 30 = ลาทั้งวัน
					if($data[ABSENT] !="0,0" && $data[ABSENT] != "313,0"){
					   if($WORK_NOW==$data[WORK_DATE]){ 
							if($TODAY_TIME_NOW > $TODAY_END_LATETIME){   
								$DATA_WORK_FLAG = "ขาดราชการ";
							}else{
								$DATA_WORK_FLAG = "";
							}
					   }else{
							$DATA_WORK_FLAG = "ขาดราชการ";
					   }
						
					}else{
						if($WORK_NOW==$data[WORK_DATE]){ 
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
			
			$dbAbsent ="";
			if($data[ABSENT] !='0,0'){
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
			/*คำร้อง*/
        
			 $cmd ="select START_FLAG,START_TIME,END_FLAG,END_TIME,
						MEETING_FLAG,SCAN_FLAG,OTH_FLAG,OTH_NOTE,
						REQ_FLAG,REQ_TIME,REQUEST_NOTE,REQ_SPEC,
						REQ_SPEC_NOTE
						from TA_REQUESTTIME
						where PER_ID = $DATA_PER_ID AND APPROVE_FLAG=1 AND REQUEST_DATE='".$data[WORK_DATE]."'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$Detail_type = "";
			if($data2[START_FLAG]==1){
				$Detail_type = "ขอลงเวลาเข้า (".substr($data2[START_TIME],0,2).":".substr($data2[START_TIME],2,2)." น.) ";
			}
			
			$Detail_type1="";
			if($data2[END_FLAG]==1){
				$Detail_type1 = $Detail_type1."ขอลงเวลาออก (".substr($data2[END_TIME],0,2).":".substr($data2[END_TIME],2,2)." น.) ";
			} 
			
			$Detail_type11=""; 
		   if($data2[MEETING_FLAG]==1){
				$Detail_type11 = $Detail_type11."ติดประชุม/สัมมนา/อบรม ";
			}
			
			if($data2[SCAN_FLAG]==1){
				$Detail_type11 = $Detail_type11."ลืมสแกน ";
			}
			
			if($data2[REQUEST_NOTE]==1){
					$Detail_type11 = $Detail_type11."ลาชั่วโมง ";
				}
				
				if($data2[REQ_TIME]==1){
					$Detail_type11 = $Detail_type11."ไปปฏิบัติราชการ ";
				}
		
				if($data2[REQ_SPEC]==1){
					$Detail_type11 = $Detail_type11." ".$data2[REQ_SPEC_NOTE]."";
				}
		
			if($data2[OTH_FLAG]==1){
				$Detail_type11 = $Detail_type11." ".$data2[OTH_NOTE]." ";
			
			}
				
			$Detail_type2="";
			


			
			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $data_row, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $DATA_WORK_DATE, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $DATA_SCAN_ENTTIME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $DATA_SCAN_EXITTIME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $DATA_FULLNAME_SHOW, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, $DATA_ORG_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 6, $DATA_WC_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 7, $DATA_WORK_FLAG, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 8, $dbAbsent, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 9, $Detail_type.$Detail_type1.$Detail_type11.$Detail_type2, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
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
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"T0303_Preview.xls\"");
	header("Content-Disposition: inline; filename=\"T0303_Preview.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>