<?php
    include("../../php_scripts/connect_database.php");
    include("../../php_scripts/calendar_data.php");
    include ("../php_scripts/function_share.php"); 

    include ("../report/rpt_function.php");
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

//    require_once "../../Excel/class.writeexcel_workbook.inc.php";
//    require_once "../../Excel/class.writeexcel_worksheet.inc.php";

    ini_set("max_execution_time", 0);
    
    $db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis_AB = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    
    function getNamePersonal($db_dpis,$perId){
        $cmd = "SELECT PP.PN_NAME,PPN.PER_NAME,PPN.PER_SURNAME 
                FROM PER_PERSONAL PPN
                LEFT JOIN PER_PRENAME PP ON(PP.PN_CODE=PPN.PN_CODE)
                WHERE PER_ID = $perId ";
        $db_dpis->send_cmd_fast($cmd);
        $data2 = $db_dpis->get_array_array();
        $fullname = $data2[PN_NAME].$data2[PER_NAME].' '.$data2[PER_SURNAME];
        return $fullname;
    }
	
	function getWC($db_dpis,$Id){
        $cmd = "SELECT WC_NAME 
                FROM PER_WORK_CYCLE
                WHERE WC_CODE = '$Id' ";
        $db_dpis->send_cmd_fast($cmd);
        $data2 = $db_dpis->get_array_array();
        $fullname = $data2[WC_NAME];
        return $fullname;
    }

    function CheckPublicHoliday($YYYY_MM_DD){
        global $DPISDB,$db_dpis;
        if($DPISDB=="odbc"){ 
            $cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$YYYY_MM_DD' ";
        }elseif($DPISDB=="oci8"){
            $cmd = " select HOL_NAME from PER_HOLIDAY where SUBSTR(HOL_DATE, 1, 10)='$YYYY_MM_DD' ";
        }elseif($DPISDB=="mysql"){
            $cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$YYYY_MM_DD' ";
        }    
        $IS_HOLIDAY = $db_dpis->send_cmd($cmd);
        if(!$IS_HOLIDAY){
            return false;
        }else{
            return true;
        }
    }

    define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
    //date_default_timezone_set('Asia/Bangkok');
    require_once '../../Excel/eslip/Classes/PHPExcel/IOFactory.php';
    
    $objReader = PHPExcel_IOFactory::createReader('Excel5');
    $objPHPExcel = $objReader->load("../../Excel/eslip/templates/R1201_template.xls");
	
    include ("es_font_size.php");
	
    $objPHPExcel->getActiveSheet()->getStyle('A1:E7')->getFont()->setName(getToFont($CH_PRINT_FONT));
    $objPHPExcel->getActiveSheet()->getStyle('A1:E7')->getFont()->setSize($CH_PRINT_SIZE);
    
      //---------------------------------------------นับวันทำการ-----------------------------------------------------------
    if(trim($search_date_min)){
        $fsearch_date_min = $search_date_min;
        $arr_temp = explode("/", $search_date_min);
        $search_date_min = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
        $show_date_min = ($arr_temp[0] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];
    } // end if

    if(trim($search_date_max)){
        $arr_temp = explode("/", $search_date_max);
        $search_date_max = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
        $show_date_max = ($arr_temp[0] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];
    } // end if
    
    if(trim($search_date_min) && trim($search_date_max)){
        $strStartDate = $search_date_min;//"2011-08-01";
        $strEndDate = $search_date_max;//"2011-08-15";
        //echo $strStartDate.'==='.$strStartDate;
        $intWorkDay = 0;
        $intHoliday = 0;
        $intPublicHoliday = 0;
        $intTotalDay = ((strtotime($strEndDate) - strtotime($strStartDate))/  ( 60 * 60 * 24 )) + 1; 
        $arrayDateWorkDay = array();
        $arrayDateDayOfWeek = array();
        $arrayDatePublicHoliday = array();
        while (strtotime($strStartDate) <= strtotime($strEndDate)) {
            $DayOfWeek = date("w", strtotime($strStartDate));
            if($DayOfWeek == 0 or $DayOfWeek ==6){  // 0 = Sunday, 6 = Saturday;
                $intHoliday++;
                $arrayDateDayOfWeek[] = $strStartDate;
                //echo "$strStartDate = <font color=red>Holiday</font><br>";
            }elseif(CheckPublicHoliday($strStartDate)){
                $intPublicHoliday++;
                $arrayDatePublicHoliday[] = $strStartDate;
                //echo "$strStartDate = <font color=orange>Public Holiday</font><br>";
            }else{
                $intWorkDay++;
                $arrayDateWorkDay[] = $strStartDate;
                //echo "$strStartDate = <b>Work Day</b><br>";
            }
            //$DayOfWeek = date("l", strtotime($strStartDate)); // return Sunday, Monday,Tuesday....
            $strStartDate = date ("Y-m-d", strtotime("+1 day", strtotime($strStartDate)));
        }
    }else{
        $intWorkDay='';
    }
    $WORK_DAY=$intWorkDay;
    //echo '===============================================================================';
    //var_dump($arrayDateWorkDay);
    //--------------------------------------------------------------------------------------------------------
    
    $search_condition_n = "";
    if($list_person_type=="SELECT"){
        if(!$SELECTED_PER_ID){$SELECTED_PER_ID=0;}
         $arr_search_condition[] = "(a.PER_ID in ($SELECTED_PER_ID))";
    }elseif(
        $list_person_type == "ALL"){
        $arr_search_condition[] = " a.PER_ID NOT IN(0) ";
    }else{
        $arr_search_condition[] = "(a.PER_ID in ($SESS_PER_ID))";
    }
    
    //หาหน่วยงาน
    if($search_org_id || $search_org_ass_id){
        if($select_org_structure==0){ // แบบตามกฏหมาย
            $varORGID= $search_org_id;
            $varORGNAME= $search_org_name;
            if($search_org_id_1){
                $varORGNAME_1= ' '.$search_org_name_1;
            }
        }else{ //แบบมอบหมายงาน
            $varORGID= $search_org_ass_id;
            $varORGNAME= $search_org_ass_name;
            if($search_org_ass_id_1){
                $varORGNAME_1= ' '.$search_org_ass_name_1;
            }
        }
    }else{
        $varORGID= $DEPARTMENT_ID;
        $varORGNAME= $search_org_ass_name;
        $varORGNAME_1 = $search_org_ass_name_1;
    }
    
    /*ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);*/
   
    for($idx=0;$idx<count($arrayDateWorkDay);$idx++){
        $arrHdDatex = explode("-", $arrayDateWorkDay[$idx]);
        $search_date = $arrHdDatex[2]."/".$arrHdDatex[1]."/".($arrHdDatex[0]+543);
        
        $arrHdDate = explode("/", $search_date);
        $year = date("Y")+543;
        $today = date("d/m")."/".$year;

        $show_date = "วันที่ ".($arrHdDate[0] + 0) ." เดือน ". $month_full[($arrHdDate[1] + 0)][TH] ." พ.ศ. ". $arrHdDate[2];
      
        $Arrsearch_date = explode("/", $search_date);
        $varBgnDataEat= ($Arrsearch_date[2]-543).'-'.$Arrsearch_date[1].'-'.$Arrsearch_date[0].' 00:00:00';
        $varToDateEat= ($Arrsearch_date[2]-543).'-'.$Arrsearch_date[1].'-'.$Arrsearch_date[0].' 23:59:59';
        $varPerType= $search_per_type;

        if ( $PER_AUDIT_FLAG==1 ){
                    $varOrgStructure = '2';
            }else{
                    $varOrgStructure = '1';
            if($select_org_structure==1){$varOrgStructure = '2';}
            }

            $cmd_con = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_SCANTYPE' ";
            $db_dpis->send_cmd_fast($cmd_con);
            $data_con = $db_dpis->get_array_array();
            $SCANTYPE = $data_con[CONFIG_VALUE];

        $cmd = file_get_contents('../GetWorkTimeByOrgID.sql');	
        $cmd=str_ireplace(":ORG_ID",$varORGID,$cmd);
        $cmd=str_ireplace(":BEGINDATEAT","'".$varBgnDataEat."'",$cmd);
        $cmd=str_ireplace(":TODATEAT","'".$varToDateEat."'",$cmd);
        $cmd=str_ireplace(":LAW_OR_ASS",$varOrgStructure,$cmd);
        $cmd=str_ireplace(":PER_TYPE",$varPerType,$cmd);
            $cmd=str_ireplace(":SCANTYPE",$SCANTYPE,$cmd);

            $search_condition ="";
            if ( $PER_AUDIT_FLAG==1 ){
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
                $search_condition .= $tCon;
                if($search_org_ass_id){
                    $search_condition .= " AND (a.ORG_ID=$varORGID)";
                }
                if($search_org_ass_id_1){
                    $search_condition .= " AND (a.ORG_ID_1=$search_org_ass_id_1)";
                }
            }else{
                if($select_org_structure==0) {
                    if(trim($search_org_id)){ 
                        $arr_search_condition[] = "(PP.ORG_ID = $search_org_id)";
                    } // end if
                    if(trim($search_org_id_1)){ 
                        $arr_search_condition[] = "(PP.ORG_ID_1 = $search_org_id_1)";
                    } // end if
                }else{
                    if(trim($search_org_ass_id)){ 
                        $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
                    } // end if
                    if(trim($search_org_ass_id_1)){ 
                        $arr_search_condition[] = "(a.ORG_ID_1 = $search_org_ass_id_1)";
                    } // end if
                }
            }

            if ( $PER_AUDIT_FLAG==1 ){
                    $search_condition = " AND (".$search_condition.")";
            }
            
            if(count($arr_search_condition)) $search_condition_n  = " and ". implode(" and ", $arr_search_condition);


            if($select_org_structure==0 || $PER_AUDIT_FLAG==2) { 
                    /*ตามโครงสร้างกฏหมาย*/
                    if($search_per_type==1){ 
                            $POS_NO_FROM=" LEFT JOIN PER_POSITION PP ON (PP.POS_ID=a.POS_ID)";
                    }elseif($search_per_type==2){ 
                            $POS_NO_FROM=" LEFT JOIN PER_POS_EMP PP ON (PP.POEM_ID=a.POEM_ID)";
                    }elseif($search_per_type==3){ 
                            $POS_NO_FROM=" LEFT JOIN PER_POS_EMPSER PP ON (PP.POEMS_ID=a.POEMS_ID)";
                    }elseif($search_per_type==4){ 
                            $POS_NO_FROM="  LEFT JOIN PER_POS_TEMP PP ON (PP.POT_ID=a.POT_ID)";
                    }

                    $conTPER_ORG = " LEFT JOIN PER_ORG  ORG ON(ORG.ORG_ID=PP.ORG_ID) ";

            }else{
                    /*ตามโครงสร้างมอบหมายงาน*/
                    $conTPER_ORG = " LEFT JOIN PER_ORG_ASS  ORG ON(ORG.ORG_ID=a.ORG_ID) ";
            }


            $cmd=" select x.*,ORG.ORG_NAME AS KONG
                            from ( ".$cmd."
                            ) x
                            left join per_personal a on(a.PER_ID=x.PER_ID)
                            $POS_NO_FROM
                            $conTPER_ORG
                            WHERE 1=1 $search_condition $search_condition_n
                            order by ORG.ORG_NAME ASC,x.WC_CODE ASC  ,a.PER_NAME ASC , a.PER_SURNAME ASC
                            ";

        $db_dpis2->send_cmd($cmd);

        $baseRow = 7;
        $r=0;
        //echo '[ <pre>'.$cmd.'<br>';
        //die('<pre>'.$cmd);
            $NumTab=0;
            $CurWC_CODE ="";
            $data = $db_dpis2->get_array_array();
            if ($data) {
                    do  {


                            if($CurWC_CODE!=$data[WC_CODE].$data[KONG]){
                                    if($CurWC_CODE!=""){
                                            $clonedSheet->removeRow($no+7);

                                            if($cntbWorkFlag_0 > 0){$cntbWorkFlag_0=number_format($cntbWorkFlag_0);}else{$cntbWorkFlag_0="-";}
                                            if($cntbWorkFlag_1 > 0){$cntbWorkFlag_1=number_format($cntbWorkFlag_1);}else{$cntbWorkFlag_1="-";}
                                            if($cntbWorkFlag_2 > 0){$cntbWorkFlag_2=number_format($cntbWorkFlag_2);}else{$cntbWorkFlag_2="-";}
                                            if($cntbWorkFlag_3 > 0){$cntbWorkFlag_3=number_format($cntbWorkFlag_3);}else{$cntbWorkFlag_3="-";}

                                            $clonedSheet->setCellValue('E'.($no+7+2), iconv('TIS-620','UTF-8',$PERSON_TYPE[$search_per_type].'ทั้งหมด     ' . (($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($no)):number_format($no)) .'   คน'));
                                            $clonedSheet->setCellValue('E'.($no+7+3), iconv('TIS-620','UTF-8','ตำแหน่งว่าง     -   คน'));
                                            $clonedSheet->setCellValue('E'.($no+7+4), iconv('TIS-620','UTF-8','ยืนตัวมาช่วยราชการ       -   คน'));
                                            $clonedSheet->setCellValue('E'.($no+7+5), iconv('TIS-620','UTF-8','มาปฏิบัติราชการ      '. (($NUMBER_DISPLAY==2)?convert2thaidigit($cntbWorkFlag_0):$cntbWorkFlag_0) .'  คน'));
                                            $clonedSheet->setCellValue('E'.($no+7+6), iconv('TIS-620','UTF-8','ไปราชการ      '. (($NUMBER_DISPLAY==2)?convert2thaidigit($cntbWorkFlag_3):$cntbWorkFlag_3) .'  คน'));
                                            $clonedSheet->setCellValue('E'.($no+7+7), iconv('TIS-620','UTF-8','มาสาย     '.  (($NUMBER_DISPLAY==2)?convert2thaidigit($cntbWorkFlag_1):$cntbWorkFlag_1) .'  คน'));
                                            $clonedSheet->setCellValue('E'.($no+7+8), iconv('TIS-620','UTF-8','ไม่มาปฏิบัติราชการ        '. (($NUMBER_DISPLAY==2)?convert2thaidigit($cntbWorkFlag_2):$cntbWorkFlag_2) .'  คน'));
                                            $clonedSheet->setCellValue('D'.($no+7+12), iconv('TIS-620','UTF-8','ผู้ตรวจ.......................................................'));

                                            if ( $PER_AUDIT_FLAG==1 ){
                                                    $cmd ="select g.PN_NAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW,
                                                                    a.PER_CARDNO,a.LEVEL_NO, a.PER_TYPE,mg.PM_NAME,
                                                                    c.PL_CODE, d.PN_CODE, e.EP_CODE,f.POSITION_LEVEL
                                                                    from PER_PERSONAL a 
                                                                    left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE)
                                                                    left join PER_POSITION c on(c.POS_ID=a.POS_ID)  
                                                                    left join PER_MGT mg on(mg.PM_CODE=c.PM_CODE) 
                                                                    left join PER_POS_EMP d on(a.POEM_ID=d.POEM_ID) 
                                                                    left join PER_POS_EMPSER e on(a.POEMS_ID=e.POEMS_ID) 
                                                                    left join PER_LEVEL f on(a.LEVEL_NO=f.LEVEL_NO) 
                                                                    where a.PER_ID = $SESS_PER_ID ";

                                                    $db_dpis->send_cmd_fast($cmd);
                                                    $data_ALLOW = $db_dpis->get_array_array();
                                                    $ALLOW_CARDNO = $data_ALLOW[PER_CARDNO];
                                                    $ALLOW_PER_NAME = $data_ALLOW[FULLNAME_SHOW];
                                                    $clonedSheet->setCellValue('D'.($no+7+13), iconv('TIS-620','UTF-8','                ('.$ALLOW_PER_NAME.')'));	
                                            }else{
                                                    $clonedSheet->setCellValue('D'.($no+7+13), iconv('TIS-620','UTF-8',''));	
                                            }

                                            $clonedSheet->getStyle('E'.($no+7+2).":E".($no+7+12))->getFont()->setName(getToFont($CH_PRINT_FONT));
                                            $clonedSheet->getStyle('E'.($no+7+2).":E".($no+7+12))->getFont()->setSize($CH_PRINT_SIZE);

                                            $clonedSheet->getStyle('D'.($no+7+12))->getFont()->setName(getToFont($CH_PRINT_FONT));
                                            $clonedSheet->getStyle('D'.($no+7+12))->getFont()->setSize($CH_PRINT_SIZE);
                                            $clonedSheet->getStyle('D'.($no+7+13))->getFont()->setName(getToFont($CH_PRINT_FONT));
                                            $clonedSheet->getStyle('D'.($no+7+13))->getFont()->setSize($CH_PRINT_SIZE);


                                    }




                                    $clonedSheet = clone $objPHPExcel->setActiveSheetIndex(0);

                                    if($data[WC_CODE]==-1){
                                            $chkWC_CODE="นับชั่วโมง";
                                    }else{
                                            $chkWC_CODE=$data[WC_CODE];
                                    }

                                    $NumTab++;
                                    $clonedSheet->setTitle(iconv('TIS-620','UTF-8','รอบ '.$chkWC_CODE."_".$NumTab ));
                                    $objPHPExcel->addSheet($clonedSheet);

                                    $CurWC_CODE=$data[WC_CODE].$data[KONG];
                                    $cntbWorkFlag_0=0;
                                    $cntbWorkFlag_1=0;
                                    $cntbWorkFlag_2=0;
                                    $cntbWorkFlag_3=0;
                                    $no=0;



                                    $clonedSheet->setCellValue('A1', iconv('TIS-620','UTF-8','บัญชีลงเวลาปฏิบัติราชการของ'.$PERSON_TYPE[$search_per_type]));
                                    $clonedSheet->setCellValue('A2', iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit($show_date):$show_date)));
                                    $clonedSheet->setCellValue('A3', iconv('TIS-620','UTF-8',$varORGNAME_1." ".$data[KONG]));
                                    $clonedSheet->setCellValue('A4', iconv('TIS-620','UTF-8',$DEPARTMENT_NAME));
                                    $clonedSheet->setCellValue('A5', iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit(getWC($db_dpis,$data[WC_CODE])):getWC($db_dpis,$data[WC_CODE]))  ));




                            }


                            $no++;
                            $dbEntTime='-';
                            if(!empty($data[ENTTIME])){
                                    $dbEntTime=$data[ENTTIME];
                            }
                            $dbExitTime='-';
                            if(!empty($data[EXITTIME])){
                                    $dbExitTime=$data[EXITTIME];
                            }

                            $ARR_ABSENT = explode(",", $data[ABSENT]);

                    $DATA_AB_NAME = "";
                    $DATA_AB_NAME_AFTERNOON = '';

                    if(substr($ARR_ABSENT[0],0,1)==1 || substr($ARR_ABSENT[0],0,1)==2 || substr($ARR_ABSENT[0],0,1)==3){
                            $cmd_AB ="select AB_NAME
                            from PER_ABSENTTYPE
                            where AB_CODE = ".substr($ARR_ABSENT[0],-2);
                            //echo $cmd_AB; die();
                            $db_dpis_AB->send_cmd_fast($cmd_AB);
                            $data_AB_NAME = $db_dpis_AB->get_array_array();
                            $DATA_AB_NAME = $data_AB_NAME[AB_NAME];
                    }
                    if(substr($ARR_ABSENT[1],0,1)==2){
                            $cmd_AB ="select AB_NAME
                            from PER_ABSENTTYPE
                            where AB_CODE = ".substr($ARR_ABSENT[1],-2);
                            //echo $cmd_AB; die();
                            $db_dpis_AB->send_cmd_fast($cmd_AB);
                            $data_AB_NAME = $db_dpis_AB->get_array_array();
                            $DATA_AB_NAME_AFTERNOON = $data_AB_NAME[AB_NAME];
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


                            $dbWorkFlag = $data[WORK_FLAG];
                            $Comment = "";
                            if($dbWorkFlag==0){
                                    if(substr($ARR_ABSENT[0],0,1)==3 || substr($ARR_ABSENT[1],0,1)==3){
                                            $Comment =$dbAbsent;
                                            $cntbWorkFlag_2++;
                                    }else{
                                            if($ARR_ABSENT[0] && $ARR_ABSENT[1]){
                                                    $Comment =$dbAbsent;
                                                    $cntbWorkFlag_2++;
                                            }else{
                                                    if($dbAbsent !=""){$dbAbsent=", ".$dbAbsent;}
                                                    $Comment = "มาปฏิบัติราชการ".$dbAbsent;
                                                    $cntbWorkFlag_0++;
                                            }
                                    }
                            }elseif($dbWorkFlag==1){
                                    if($dbAbsent !=""){$dbAbsent=", ".$dbAbsent;}
                                    if($data[REMARK]){
                                            $Comment = $data[REMARK].$dbAbsent;
                                            $cntbWorkFlag_0++;
                                    }else{
                                            $Comment = "มาสาย".$dbAbsent;
                                            $cntbWorkFlag_1++;
                                    }

                            }elseif($dbWorkFlag==2){
                                    if($data[ABSENT] != "0,0"){
                                            if(substr($ARR_ABSENT[0],0,1)==0 || substr($ARR_ABSENT[1],0,1)==0){
                                                    if($dbAbsent !=""){$dbAbsent=", ".$dbAbsent;}
                                                    $Comment = "ไม่มาปฏิบัติราชการ".$dbAbsent;
                                            }else{
                                                    $Comment = "".$dbAbsent;
                                            }

                                    }else{
                                            $Comment = "ไม่มาปฏิบัติราชการ";
                                    }

                                    $cntbWorkFlag_2++;
                            }elseif($dbWorkFlag==3){
                                    if($dbAbsent !=""){$dbAbsent=", ".$dbAbsent;}
                                    $Comment = "ออกก่อนเวลา".$dbAbsent;
                            }elseif($dbWorkFlag==4){
                                    if($dbAbsent !=""){$dbAbsent=", ".$dbAbsent;}
                                     /* http://dpis.ocsc.go.th/Service/node/1574  เวอร์ชั่น: 5.2.1.12 06/10/2017*/
                                    if($data[WC_CODE] == "-1" && $search_date==$today){
                                        //die($search_date."==".$today);
                                        $Comment = "";
                                    }else{
                                        $Comment = "ไม่ได้ลงเวลา".$dbAbsent;
                                    } 
                                     //$Comment = "ไม่ได้ลงเวลา".$dbAbsent;
                                    /* End เวอร์ชั่น: 5.2.1.12 06/10/2017 */
                            }elseif($dbWorkFlag==5){
                                    if($dbAbsent !=""){$dbAbsent=", ".$dbAbsent;}
                                    $Comment = $data[REMARK].$dbAbsent;
                                    $cntbWorkFlag_3++;
                            }

                            /*$dbAbsent = $data[ABSENT];
                            $dbWorkFlag = $data[WORK_FLAG];
                            $Comment = "";
                            if($dbWorkFlag==0){$Comment = "มาปฏิบัติราชการ";$cntbWorkFlag_0++;}
                            if($dbWorkFlag==1){$Comment = "มาสาย";$cntbWorkFlag_1++;}
                            if($dbWorkFlag==2){$Comment = "ไม่มาปฏิบัติราชการ";$cntbWorkFlag_2++;}
                            if($dbWorkFlag==3){$Comment = "ออกก่อนเวลา";}
                            if($dbWorkFlag==4){$Comment = "ไม่ได้ลงเวลา";}*/

                             $clonedSheet->insertNewRowBefore($no+7,1);
                             $clonedSheet->setCellValue('A'.($no+6), iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit($no):$no) ));
                             $clonedSheet->setCellValue('B'.($no+6),  iconv('TIS-620','UTF-8',getNamePersonal($db_dpis,$data[PER_ID])));
                             $clonedSheet->setCellValue('C'.($no+6), iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit($dbEntTime):$dbEntTime)) );
                             $clonedSheet->setCellValue('D'.($no+6), iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit($dbExitTime):$dbExitTime)) );
                             $clonedSheet->setCellValue('E'.($no+6), iconv('TIS-620','UTF-8',$Comment));

                    } while ($data = $db_dpis2->get_array_array());


                            $clonedSheet->removeRow($no+7);

                            if($cntbWorkFlag_0 > 0){$cntbWorkFlag_0=number_format($cntbWorkFlag_0);}else{$cntbWorkFlag_0="-";}
                            if($cntbWorkFlag_1 > 0){$cntbWorkFlag_1=number_format($cntbWorkFlag_1);}else{$cntbWorkFlag_1="-";}
                            if($cntbWorkFlag_2 > 0){$cntbWorkFlag_2=number_format($cntbWorkFlag_2);}else{$cntbWorkFlag_2="-";}
                            if($cntbWorkFlag_3 > 0){$cntbWorkFlag_3=number_format($cntbWorkFlag_3);}else{$cntbWorkFlag_3="-";}

                            $clonedSheet->setCellValue('E'.($no+7+2), iconv('TIS-620','UTF-8',$PERSON_TYPE[$search_per_type].'ทั้งหมด     ' . (($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($no)):number_format($no)) .'   คน'));
                            $clonedSheet->setCellValue('E'.($no+7+3), iconv('TIS-620','UTF-8','ตำแหน่งว่าง     -   คน'));
                            $clonedSheet->setCellValue('E'.($no+7+4), iconv('TIS-620','UTF-8','ยืนตัวมาช่วยราชการ       -   คน'));
                            $clonedSheet->setCellValue('E'.($no+7+5), iconv('TIS-620','UTF-8','มาปฏิบัติราชการ      '. (($NUMBER_DISPLAY==2)?convert2thaidigit($cntbWorkFlag_0):$cntbWorkFlag_0) .'  คน'));
                            $clonedSheet->setCellValue('E'.($no+7+6), iconv('TIS-620','UTF-8','ไปราชการ      '. (($NUMBER_DISPLAY==2)?convert2thaidigit($cntbWorkFlag_3):$cntbWorkFlag_3) .'  คน'));
                            $clonedSheet->setCellValue('E'.($no+7+7), iconv('TIS-620','UTF-8','มาสาย     '.  (($NUMBER_DISPLAY==2)?convert2thaidigit($cntbWorkFlag_1):$cntbWorkFlag_1) .'  คน'));
                            $clonedSheet->setCellValue('E'.($no+7+8), iconv('TIS-620','UTF-8','ไม่มาปฏิบัติราชการ        '. (($NUMBER_DISPLAY==2)?convert2thaidigit($cntbWorkFlag_2):$cntbWorkFlag_2) .'  คน'));
                            $clonedSheet->setCellValue('D'.($no+7+12), iconv('TIS-620','UTF-8','ผู้ตรวจ.......................................................'));

                            if ( $PER_AUDIT_FLAG==1 ){
                                    $cmd ="select g.PN_NAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW,
                                                    a.PER_CARDNO,a.LEVEL_NO, a.PER_TYPE,mg.PM_NAME,
                                                    c.PL_CODE, d.PN_CODE, e.EP_CODE,f.POSITION_LEVEL
                                                    from PER_PERSONAL a 
                                                    left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE)
                                                    left join PER_POSITION c on(c.POS_ID=a.POS_ID)  
                                                    left join PER_MGT mg on(mg.PM_CODE=c.PM_CODE) 
                                                    left join PER_POS_EMP d on(a.POEM_ID=d.POEM_ID) 
                                                    left join PER_POS_EMPSER e on(a.POEMS_ID=e.POEMS_ID) 
                                                    left join PER_LEVEL f on(a.LEVEL_NO=f.LEVEL_NO) 
                                                    where a.PER_ID = $SESS_PER_ID ";

                                    $db_dpis->send_cmd_fast($cmd);
                                    $data_ALLOW = $db_dpis->get_array_array();
                                    $ALLOW_CARDNO = $data_ALLOW[PER_CARDNO];
                                    $ALLOW_PER_NAME = $data_ALLOW[FULLNAME_SHOW];
                                    $clonedSheet->setCellValue('D'.($no+7+13), iconv('TIS-620','UTF-8','                ('.$ALLOW_PER_NAME.')'));	
                            }else{
                                    $clonedSheet->setCellValue('D'.($no+7+13), iconv('TIS-620','UTF-8',''));	
                            }


                            $clonedSheet->getStyle('E'.($no+7+2).":E".($no+7+12))->getFont()->setName(getToFont($CH_PRINT_FONT));
                            $clonedSheet->getStyle('E'.($no+7+2).":E".($no+7+12))->getFont()->setSize($CH_PRINT_SIZE);

                            $clonedSheet->getStyle('D'.($no+7+12))->getFont()->setName(getToFont($CH_PRINT_FONT));
                            $clonedSheet->getStyle('D'.($no+7+12))->getFont()->setSize($CH_PRINT_SIZE);

                            $clonedSheet->getStyle('D'.($no+7+13))->getFont()->setName(getToFont($CH_PRINT_FONT));
                            $clonedSheet->getStyle('D'.($no+7+13))->getFont()->setSize($CH_PRINT_SIZE);


                            
        }else{
                    $clonedSheet = clone $objPHPExcel->setActiveSheetIndex(0);
                    $value = 1;
                    $clonedSheet->setTitle(iconv('TIS-620','UTF-8',$value));
                    $objPHPExcel->addSheet($clonedSheet);

                    $company_name ="รูปแบบการออกรายงาน : ".$org_structure.$varORGNAME_1." ".$varORGNAME;

                    $clonedSheet->setCellValue('A1', iconv('TIS-620','UTF-8','บัญชีลงเวลาปฏิบัติราชการของ'.$PERSON_TYPE[$search_per_type]));
                    $clonedSheet->setCellValue('A2', iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit($show_date):$show_date)));
                    $clonedSheet->setCellValue('A3', iconv('TIS-620','UTF-8',$varORGNAME_1." ".$varORGNAME));
                    $clonedSheet->setCellValue('A4', iconv('TIS-620','UTF-8',$DEPARTMENT_NAME));

        }
    }
    $objPHPExcel->removeSheetByIndex(0);
    //die('');
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="R1201.xls"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');

?>
