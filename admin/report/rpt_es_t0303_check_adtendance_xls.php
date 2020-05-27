<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd); 


	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$report_title = trim(iconv("utf-8","tis620",urldecode($report_title)));
	$worksheet = &$workbook->addworksheet('T0303');
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 25);
		$worksheet->set_column(2, 2, 20);
		$worksheet->set_column(3, 3, 20);
		$worksheet->set_column(4, 4, 25);
		$worksheet->set_column(5, 5, 25);
		$worksheet->set_column(6, 6, 30);
		$worksheet->set_column(7, 7, 25);
		$worksheet->set_column(8, 8, 15);
		
		$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "วัน-เวลาที่สแกน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "วันที่สแกน (ฐานข้อมูล)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "เวลาที่สแกน (ฐานข้อมูล)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "เครื่องบันทึกเวลา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "ชื่อ-สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "สำนัก/กอง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "รอบฯ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "เวลาที่ปรับแก้", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
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
    

    
    //-ถ้าไม่ใช่กลุ่ม admin กับกลุ่ม กจ. กรม จะเอาหน่วยงานตามหมอบหมายงาน
 
   /* if ( ($SESS_USERGROUP !=1 && $NAME_GROUP_HRD!='HRD') ){
    	$select_org_structure=1;
        $PER_AUDIT_FLAG=1;
        if(empty($Submit1) && empty($image2) ){
           
            $search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
            
           if($SESS_AuditArray[0][0] <>0){

                $cmd_ass = " select ORG_ID,ORG_NAME from PER_ORG_ASS where ORG_ID = ".$SESS_AuditArray[0][0]; 
                $db_dpis2->send_cmd($cmd_ass);
                $data_ass = $db_dpis2->get_array();
                $search_org_name = $data_ass[ORG_NAME];
                $search_org_id = $data_ass[ORG_ID];
            }
            
            if($SESS_AuditArray[0][1] <>0){
				 $cmd_l1 = " select ORG_ID,ORG_NAME from PER_ORG_ASS where ORG_ID=".$SESS_AuditArray[0][1]; 
                $db_dpis2->send_cmd($cmd_l1);
                $data_l1 = $db_dpis2->get_array();
                $search_org_name_1 = $data_l1[ORG_NAME];
                $search_org_id_1 = $data_l1[ORG_ID];
             }
            
            

            
            
        }
        
    }*/
    
    
    if(empty($TIME_STAMP_START)){ 
    	$TIME_STAMP_START =  date("d/m")."/".(date("Y")+543); 
        
    }
   // $TIME_STAMP_END =  date("t", strtotime(date("Y-m-d")))."/".date("m")."/".(date("Y")+543); 
   
    if(empty($TIME_STAMP_END)){ 
    	$TIME_STAMP_END =  date("d/m")."/".(date("Y")+543); 
        
    }
    

    if ( ($SESS_USERGROUP !=1 && $NAME_GROUP_HRD!='HRD')  ){
			if(count($SESS_AuditArray)>0){
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
            	$arr_search_condition[] = "(att.PER_ID=$SESS_PER_ID)";
                
                $cmd = "	select PER_NAME,PER_SURNAME from PER_PERSONAL where PER_ID=$SESS_PER_ID ";
                $db_dpis2->send_cmd($cmd);
				$data_name = $db_dpis2->get_array();
                $search_name =$data_name[PER_NAME];
                $search_surname =$data_name[PER_SURNAME];	
                
                
            }
       

        }else{
        	if($search_org_id_1){ /* Release 5.1.0.4 */
                if($select_org_structure==0){ //โครงสร้างตามกฎหมาย
                    $arr_search_condition[] = "(b.ORG_ID_1=$search_org_id_1 or c.ORG_ID_1=$search_org_id_1 or d.ORG_ID_1=$search_org_id_1 or j.ORG_ID_1=$search_org_id_1)";
                }else if($select_org_structure==1){ //โครงสร้างตามมอบหมายงาน
                    $arr_search_condition[] = "(a.ORG_ID_1=$search_org_id_1)";
                }
            
            }elseif($search_org_id){
                if($select_org_structure==0){
                    $arr_search_condition[] = "(b.ORG_ID=$search_org_id or c.ORG_ID=$search_org_id or d.ORG_ID=$search_org_id or j.ORG_ID=$search_org_id)";
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


    if(trim($search_time_att))  $arr_search_condition[] = "(tat.TA_CODE='$search_time_att')";
  	
    
    if($TIME_STAMP_START & $TIME_STAMP_END){
    	$YMD_TIME_START = (substr($TIME_STAMP_START,6,4)-543)."-".substr($TIME_STAMP_START,3,2)."-".substr($TIME_STAMP_START,0,2);
    	$YMD_TIME_END = (substr($TIME_STAMP_END,6,4)-543)."-".substr($TIME_STAMP_END,3,2)."-".substr($TIME_STAMP_END,0,2);
    	
        $arr_search_condition[] = " (att.TIME_STAMP between to_date('$YMD_TIME_START 00:00:00','yyyy-mm-dd hh24:mi:ss') 
                                              AND to_date('$YMD_TIME_END 23:59:59','yyyy-mm-dd hh24:mi:ss')) ";
                                              
   }else if($TIME_STAMP_START & !$TIME_STAMP_END){
    	$YMD_TIME_START = (substr($TIME_STAMP_START,6,4)-543)."-".substr($TIME_STAMP_START,3,2)."-".substr($TIME_STAMP_START,0,2);
    	
        $arr_search_condition[] = " (att.TIME_STAMP between to_date('$YMD_TIME_START 00:00:00','yyyy-mm-dd hh24:mi:ss') 
                                              AND to_date('$YMD_TIME_START 23:59:59','yyyy-mm-dd hh24:mi:ss')) ";
    }else if(!$TIME_STAMP_START & $TIME_STAMP_END){
    	$YMD_TIME_END = (substr($TIME_STAMP_END,6,4)-543)."-".substr($TIME_STAMP_END,3,2)."-".substr($TIME_STAMP_END,0,2);
    	$arr_search_condition[] = " (att.TIME_STAMP between to_date('$YMD_TIME_END 00:00:00','yyyy-mm-dd hh24:mi:ss') 
                                              AND to_date('$YMD_TIME_END 23:59:59','yyyy-mm-dd hh24:mi:ss')) ";
    }

	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);



		$cmd = " select  att.PER_ID,TO_CHAR(att.TIME_STAMP,'yyyy-mm-dd') AS TIME_STAMP1,
                      TO_CHAR(att.TIME_STAMP,'HH24:MI:SS')  AS ATT_STARTTIME,
                    g.PN_SHORTNAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW,
                     a.POEM_ID, a.POEMS_ID, a.PER_TYPE ,b.ORG_ID,a.POS_ID,
                    b.ORG_ID AS POS_ORG,c.ORG_ID AS POEM_ORG,d.ORG_ID AS POEMS_ORG,
                    b.POS_NO,c.POEM_NO,d.POEMS_NO,
                    b.PL_CODE, c.PN_CODE, d.EP_CODE,j.TP_CODE,j.ORG_ID AS POT_ORG_ID,
                    tat.TA_NAME ,wc.WC_NAME,att.TIME_STAMP,wc.WC_CODE,wc.WC_START,
                    TO_CHAR(att.TIME_ADJUST,'yyyy-mm-dd') AS DATE_ADJUST,
					TO_CHAR(att.TIME_ADJUST,'HH24:MI') AS TIME_ADJUST
                    from PER_TIME_ATTENDANCE att 
                    left join PER_TIME_ATT tat on(tat.TA_CODE=att.TA_CODE) 
                    left join per_personal a on(a.PER_ID=att.PER_ID) 
                    left join PER_POSITION b on(b.POS_ID=a.POS_ID) 
                    left join PER_POS_EMP c on(c.POEM_ID=a.POEM_ID) 
                    left join PER_POS_EMPSER d on(d.POEMS_ID=a.POEMS_ID) 
                    left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE) 
                    left join PER_POS_TEMP j on (j.POT_ID=a.POT_ID)
                    left join PER_WORK_CYCLEHIS cyh on(att.PER_ID=cyh.PER_ID 
                            AND att.TIME_STAMP
                            between to_date(cyh.START_DATE,'yyyy-mm-dd hh24:mi:ss')  
                              AND case when cyh.END_DATE is not null then to_date(cyh.END_DATE,'yyyy-mm-dd hh24:mi:ss') 
                            else sysdate end )
                    left join PER_WORK_CYCLE  wc on(wc.WC_CODE=cyh.WC_CODE) 
                    WHERE 1=1 $search_condition  
					ORDER BY att.TIME_STAMP,a.PER_NAME,a.PER_SURNAME
					";

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

		print_header(1);
		$data_count = 1;
		$data_row = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;
			
			$TIME_STAMP_STR = "";
			$TIME_STAMP_STR1 = "";
			if ($data[TIME_STAMP1]) { 
				//$TIME_STAMP_STR  = show_date_format($data[TIME_STAMP1], $DATE_DISPLAY);
				$TIME_STAMP_STR  = substr($data[TIME_STAMP1],8,2)."/".substr($data[TIME_STAMP1],5,2)."/".(substr($data[TIME_STAMP1],0,4)+543);
				$TIME_STAMP_STR1  = (substr($data[TIME_STAMP1],0,4))."-".substr($data[TIME_STAMP1],5,2)."-".substr($data[TIME_STAMP1],8,2);
				//$TIME_STAMP_STR1  = substr($data[TIME_STAMP1],8,2)."/".substr($data[TIME_STAMP1],5,2)."/".(substr($data[TIME_STAMP1],0,4));
			} 
			
			$DATA_att_starttime = "";
			if ($data[ATT_STARTTIME]) { 
				$DATA_att_starttime = $data[ATT_STARTTIME];
			}

			
			$DATA_TA_NAME = $data[TA_NAME];

			
			$FULLNAME_SHOW = $data[FULLNAME_SHOW];

			
			$DATA_PER_TYPE = $data[PER_TYPE];
			if($DATA_PER_TYPE==1){ 
					$TMP_POS_NO = trim($data[POS_NO]);
					$TMP_POS_NO_NAME = trim($data[POS_NO_NAME]);
					$TMP_PER_TYPE = "ข้าราชการ";
					$TMP_ORG_ID = trim($data[ORG_ID]);
					$TMP_ORG_ID_1 = trim($data[ORG_ID_1]);
					$TMP_PL_CODE = trim($data[PL_CODE]);
					$TMP_PT_CODE = trim($data[PT_CODE]);
					$TMP_PM_CODE = trim($data[PM_CODE]);
	
					$cmd = " select PL_NAME from PER_LINE where PL_CODE='$TMP_PL_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$DATA_PL_NAME = $data2[PL_NAME];
					
					$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$TMP_PT_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$TMP_PT_NAME = $data2[PT_NAME];
					
					$cmd = " select PM_NAME from PER_MGT where PM_CODE='$TMP_PM_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$TMP_PM_NAME = $data2[PM_NAME];
					
					if ($RPT_N)
						$TMP_POSITION = (trim($TMP_PM_NAME) ?"$TMP_PM_NAME (":"") . (trim($TMP_PL_NAME)? "$TMP_PL_NAME$POSITION_LEVEL" : "") . (trim($TMP_PM_NAME) ?")":"");
					else
						$TMP_POSITION = (trim($TMP_PM_NAME) ?"$TMP_PM_NAME (":"") . (trim($TMP_PL_NAME)?($TMP_PL_NAME ." ". level_no_format($TMP_LEVEL_NO) . (($TMP_PT_NAME != "ทั่วไป" && $TMP_LEVEL_NO >= 6)?"$TMP_PT_NAME":"")):"") . (trim($TMP_PM_NAME) ?")":"");
				}elseif($DATA_PER_TYPE==2){ 
					$TMP_POS_NO = $data[POEM_NO];
					$TMP_POS_NO_NAME = $data[POEM_NO_NAME];
					$TMP_PER_TYPE = "ลูกจ้างประจำ";
					$TMP_ORG_ID = trim($data[POEM_ORG]);
					$TMP_PL_CODE = $data[PN_CODE];
	
					$cmd = " select PN_NAME from PER_POS_NAME where PN_CODE='$TMP_PL_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$DATA_PL_NAME = $data2[PN_NAME];
	
					$TMP_POSITION = $DATA_PL_NAME;
				} elseif ($DATA_PER_TYPE == 3) {
					$TMP_POS_NO = $data[POEMS_NO];
					$TMP_POS_NO_NAME = $data[POEMS_NO_NAME];
					$TMP_PER_TYPE = "พนักงานราชการ";
					$TMP_ORG_ID = trim($data[POEMS_ORG]);					
					$TMP_PL_CODE = $data[EP_CODE];
	
					$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE='$TMP_PL_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$DATA_PL_NAME = $data2[EP_NAME];
	
					$TMP_POSITION = $DATA_PL_NAME;
								
				} elseif ($DATA_PER_TYPE == 4) {
					$TMP_POS_NO = $data[POT_NO];
					$TMP_POS_NO_NAME = $data[POT_NO_NAME];
					$TMP_PER_TYPE = "ลูกจ้างชั่วคราว";
					$TMP_ORG_ID = trim($data[POT_ORG_ID]);					
					$TMP_PL_CODE = $data[TP_CODE];
	
					$cmd = " select TP_NAME from PER_TEMP_POS_NAME where TP_CODE='$TMP_PL_CODE' ";
					$db_dpis2->send_cmd($cmd);
					//echo $cmd;
					$data2 = $db_dpis2->get_array();
					$DATA_PL_NAME = $data2[TP_NAME];
	
					$TMP_POSITION = $DATA_PL_NAME; 
				} // end if
				
			   /*echo "|".$TMP_ORG_ID."|"."<br>";*/
				if($TMP_ORG_ID){
					$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$TMP_ORG_ID ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$DATA_ORG_NAME = $data2[ORG_NAME];
				}
				
				
				$DATA_WC_NAME = $data[WC_NAME];

				
				$DATA_TIME_ADJUST = "";
				if($data[DATE_ADJUST]){
					$DATA_TIME_ADJUST = show_date_format($data[DATE_ADJUST], $DATE_DISPLAY)." ".$data[TIME_ADJUST]." น.";
				}


			
			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $data_row, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $TIME_STAMP_STR." ".$DATA_att_starttime."", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $TIME_STAMP_STR1, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $DATA_att_starttime, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $DATA_TA_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, $FULLNAME_SHOW, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 6, $DATA_ORG_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 7, $DATA_WC_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 8, $DATA_TIME_ADJUST, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
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
	header("Content-Type: application/x-msexcel; name=\"T0303.xls\"");
	header("Content-Disposition: inline; filename=\"T0303.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>