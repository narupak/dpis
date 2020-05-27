<?php include("../../php_scripts/connect_database.php");
    include("../php_scripts/pdf_wordarray_thaicut.php");
    include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
    define('FPDF_FONTPATH','../../PDF/font/');
    include ("../../PDF/es_fpdf.php");
    include ("../../PDF/es_pdf_extends_DPIS.php");  
    
    ini_set("max_execution_time", $max_execution_time);
    $db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis_cnt = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis_AB = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	
    $cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_ESIGN_ATT' ";
    $db_dpis2->send_cmd_fast($cmd);
    $data = $db_dpis2->get_array_array();
    $P_ESIGN_ATT = $data[CONFIG_VALUE];

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
	
    function getPIC_SIGN($PER_ID,$PER_CARDNO){
            global $db_dpis , $db_dpis2;

            $PIC_SIGN="";
            //หารูปที่เป็นลายเซ็น
            $cmd = "	select 		*
                                                                      from 		PER_PERSONALPIC
                                                                      where 		PER_ID=$PER_ID and PER_CARDNO = '$PER_CARDNO' and  PIC_SIGN=1
                                                                      order by 	PER_PICSEQ ";


            $count_pic_sign=$db_dpis->send_cmd($cmd);
            if($count_pic_sign>0){	
            $data = $db_dpis->get_array_array();
            $TMP_PIC_SEQ = $data[PER_PICSEQ];
            $current_list .= ((trim($current_list))?",":"") . $TMP_PIC_SEQ;
            $T_PIC_SEQ = substr("000",0,3-strlen("$TMP_PIC_SEQ"))."$TMP_PIC_SEQ";
            $TMP_SERVER = $data[PIC_SERVER_ID];
            $TMP_PIC_SIGN= $data[PIC_SIGN];

            if ($TMP_SERVER) {
                    $cmd1 = " SELECT * FROM OTH_SERVER WHERE SERVER_ID=$TMP_SERVER ";
                    $db_dpis2->send_cmd_fast($cmd1);
                    $data2 = $db_dpis2->get_array_array();
                    $tmp_SERVER_NAME = trim($data2[SERVER_NAME]);
                    $tmp_ftp_server = trim($data2[FTP_SERVER]);
                    $tmp_ftp_username = trim($data2[FTP_USERNAME]);
                    $tmp_ftp_password = trim($data2[FTP_PASSWORD]);
                    $tmp_main_path = trim($data2[MAIN_PATH]);
                    $tmp_http_server = trim($data2[HTTP_SERVER]);
            } else {
                    $TMP_SERVER = 0;
                    $tmp_SERVER_NAME = "";
                    $tmp_ftp_server = "";
                    $tmp_ftp_username = "";
                    $tmp_ftp_password = "";
                    $tmp_main_path = "";
                    $tmp_http_server = "";
            }
            $SIGN_NAME = "";
            if($TMP_PIC_SIGN==1){ $SIGN_NAME = "SIGN"; }
            if (trim($PER_CARDNO) && trim($PER_CARDNO) != "NULL") {
                    $TMP_PIC_NAME = $data[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg".($tmp_SERVER_NAME ? " [".$tmp_SERVER_NAME."]" : "");
                    //$TMP_SHOW_PIC = ($tmp_http_server ? $tmp_http_server."/" : "").$data[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg";
            } else {
                    $TMP_PIC_NAME = $data[PER_PICPATH].$data[PER_GENNAME]."-".$SIGN_NAME.$T_PIC_SEQ.".jpg".($tmp_SERVER_NAME ? " [".$tmp_SERVER_NAME."]" : "");
                    //$TMP_SHOW_PIC = ($tmp_http_server ? $tmp_http_server."/" : "").$data[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg";
            }
            if(file_exists("../".$TMP_PIC_NAME)){
                    $PIC_SIGN = "../".$TMP_PIC_NAME;		//	if($PER_CARDNO && $TMP_PIC_NAME)		$PIC_SIGN = "../../attachments/".$PER_CARDNO."/PER_SIGN/".$TMP_PIC_NAME;
            }
            } //end count	
            return $PIC_SIGN;
    }
	
    if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
    
    $unit="mm";
    $paper_size="A4";
    $lang_code="TH";
    $orientation='P';

    /*function Dbetween($Datestart, $Dateend){
        $Oday = 60*60*24; #วัน
        $Datestart = strtotime($Datestart); #แปลงวันที่เป็น unixtime
        $Dateend = strtotime($Dateend); #แปลงวันที่เป็น unixtime
        $Diffday = round(($Dateend - $Datestart)/$Oday); 
        $arrayDate = array();
        $arrayDate[] = date('Y-m-d',$Datestart);    
        for($x = 1; $x < $Diffday; $x++){
            $arrayDate[] = date('Y-m-d',($Datestart+($Oday*$x)));
        }
        $arrayDate[] = date('Y-m-d',$Dateend);
        return $arrayDate;
    }

    $d = Dbetween('2011-09-28', '2011-10-03');
    var_dump($d);
    #แสดงวันที่เลือก
    echo "จำนวนวันทั้งหมด คือ ".count($d)." วัน <br>";*/

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
    $search_date = $fsearch_date_min;
    //die();
    $arrHdDate = explode("/", $search_date);
    $year = date("Y")+543;
    $today = date("d/m")."/".$year;
   
    
    $show_date = "วันที่ ".($arrHdDate[0] + 0) ." เดือน ". $month_full[($arrHdDate[1] + 0)][TH] ." พ.ศ. ". $arrHdDate[2];
	
    //หาหน่วยงาน
    if($search_org_id || $search_org_ass_id){
        if($select_org_structure==0){ // แบบตามกฏหมาย
            $varORGID= $search_org_id;
            $varORGNAME= $search_org_name;
            if($search_org_id_1){
                $varORGNAME_1= '||'.$search_org_name_1;
            }
        }else{ //แบบมอบหมายงาน
            $varORGID= $search_org_ass_id;
            $varORGNAME= $search_org_ass_name;
            if($search_org_ass_id_1){
                $varORGNAME_1= '||'.$search_org_ass_name_1;
            }
        }
    }else{
             $varORGID= $DEPARTMENT_ID;
             $varORGNAME= $search_org_ass_name;
             $varORGNAME_1 = $search_org_ass_name_1;
    }
    
    $report_title = "บัญชีลงเวลาปฏิบัติราชการของ".$PERSON_TYPE[$search_per_type]."||$show_date $varORGNAME_1||$varORGNAME||$DEPARTMENT_NAME";
    $report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
    $report_code = "R1201";
    include ("es_rpt_pdf_R1201_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
    session_cache_limiter("private");
    session_start();
    
    //$arrFont =array('20','B');
    $pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
    $pdf->Open();
    $pdf->SetFont($font,'',$CH_PRINT_SIZE);
    //$pdf->SetMargins($P_DISLEFT,$P_DISHEAD,$P_DISRIGHT,$P_DISFOOT);
    $pdf->SetMargins($Px_DISLEFT,$Px_DISHEAD,$Px_DISRIGHT);
    //echo $Px_DISLEFT."|".$Px_DISHEAD."|".$Px_DISRIGHT; die();
    $pdf->AliasNbPages();
    $pdf->AutoPageBreak = false;
    
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
    
    $cmd_con = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_SCANTYPE' ";
    $db_dpis->send_cmd_fast($cmd_con);
    $data_con = $db_dpis->get_array_array();
    $SCANTYPE = $data_con[CONFIG_VALUE];
            
    for($idx=0;$idx<count($arrayDateWorkDay);$idx++){
        $arrHdDatex = explode("-", $arrayDateWorkDay[$idx]);
        $search_date = $arrHdDatex[2]."/".$arrHdDatex[1]."/".($arrHdDatex[0]+543);
        
        if($idx>0){
            $arrHdDate = explode("/", $search_date);
            $year = date("Y")+543;
            $today = date("d/m")."/".$year;
            $show_date = "วันที่ ".($arrHdDate[0] + 0) ." เดือน ". $month_full[($arrHdDate[1] + 0)][TH] ." พ.ศ. ". $arrHdDate[2];
        }    
        
        $Arrsearch_date = explode("/", $search_date);
        $varBgnDataEat= ($Arrsearch_date[2]-543).'-'.$Arrsearch_date[1].'-'.$Arrsearch_date[0].' 00:00:00';
        $varToDateEat= ($Arrsearch_date[2]-543).'-'.$Arrsearch_date[1].'-'.$Arrsearch_date[0].' 23:59:59';
        $varDATE_ABSENT= ($Arrsearch_date[2]-543).'-'.$Arrsearch_date[1].'-'.$Arrsearch_date[0];
        $varPerType= $search_per_type;

            if ( $PER_AUDIT_FLAG==1 ){
                    $varOrgStructure = '2';
            }else{
                $varOrgStructure = '1';
                if($select_org_structure==1){$varOrgStructure = '2';}
            }

            $cmd = file_get_contents('../GetWorkTimeByOrgID.sql');	
            $cmd=str_ireplace(":ORG_ID",$varORGID,$cmd);
            $cmd=str_ireplace(":BEGINDATEAT","'".$varBgnDataEat."'",$cmd);
            $cmd=str_ireplace(":TODATEAT","'".$varToDateEat."'",$cmd);
            $cmd=str_ireplace(":LAW_OR_ASS",$varOrgStructure,$cmd);
            $cmd=str_ireplace(":PER_TYPE",$varPerType,$cmd);
            $cmd=str_ireplace(":SCANTYPE",$SCANTYPE,$cmd);
//echo "<pre>".$cmd;
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

            if ($PER_AUDIT_FLAG==1 ){
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
        $db_dpis_cnt->send_cmd($cmd);
        $cnt = $db_dpis_cnt->num_rows();
        $db_dpis2->send_cmd_fast($cmd);
        
        $CurWC_CODE="";
       // echo  "<pre>".$cmd;
        //die();
            //$chkKONG = "";
        if($cnt>0){
            while ($data = $db_dpis2->get_array_array()) {
                if($CurWC_CODE!=$data[WC_CODE].$data[KONG]){ // รอบ
                        if($CurWC_CODE!=""){
                            $pdf->close_tab(""); 
                            $y = $pdf->y;	
                            $h = 6;
                            if ($y+($h*9) > ($pdf->PageBreakTrigger)) {

                                    //$pdf->SetFont($font,'b',$CH_PRINT_SIZE);
                                    $pdf->AddPage();			
                                    $pdf->SetTextColor(0, 0, 0);
                                    $y = $pdf->y;	
                            }

                            //$pdf->SetFont($font,'',$CH_PRINT_SIZE);
                            for ($i=1; $i < 8; $i++)
                                    $pdf->Text(150+43,$y+($h*$i)," คน");
                                    if($cntbWorkFlag_0 > 0){$cntbWorkFlag_0=number_format($cntbWorkFlag_0);}else{$cntbWorkFlag_0="-";}
                                    if($cntbWorkFlag_1 > 0){$cntbWorkFlag_1=number_format($cntbWorkFlag_1);}else{$cntbWorkFlag_1="-";}
                                    if($cntbWorkFlag_2 > 0){$cntbWorkFlag_2=number_format($cntbWorkFlag_2);}else{$cntbWorkFlag_2="-";}
                                    if($cntbWorkFlag_3 > 0){$cntbWorkFlag_3=number_format($cntbWorkFlag_3);}else{$cntbWorkFlag_3="-";}
                                    $pdf->Text(150,$y+$h,$PERSON_TYPE[$search_per_type]."ทั้งหมด");$pdf->Text(150+35,$y+$h,(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($no)):number_format($no)));
                                    $y += $h;
                                    $pdf->Text(150,$y+$h,"ตำแหน่งว่าง");$pdf->Text(150+35,$y+$h,"-");
                                    $y += $h;
                                    $pdf->Text(150,$y+$h,"ยืนตัวมาช่วยราชการ");$pdf->Text(150+35,$y+$h,"-");
                                    $y += $h;
                                    $pdf->Text(150,$y+$h,"มาปฏิบัติราชการ");$pdf->Text(150+35,$y+$h,(($NUMBER_DISPLAY==2)?convert2thaidigit($cntbWorkFlag_0):$cntbWorkFlag_0));
                                    $y += $h;
                                    $pdf->Text(150,$y+$h,"ไปราชการ");$pdf->Text(150+35,$y+$h,(($NUMBER_DISPLAY==2)?convert2thaidigit($cntbWorkFlag_3):$cntbWorkFlag_3));
                                    $y += $h;
                                    $pdf->Text(150,$y+$h,"มาสาย");$pdf->Text(150+35,$y+$h,(($NUMBER_DISPLAY==2)?convert2thaidigit($cntbWorkFlag_1):$cntbWorkFlag_1));
                                    $y += $h;
                                    $pdf->Text(150,$y+$h,"ไม่มาปฏิบัติราชการ");$pdf->Text(150+35,$y+$h,(($NUMBER_DISPLAY==2)?convert2thaidigit($cntbWorkFlag_2):$cntbWorkFlag_2));
                                    $y += $h;
                                    $y += $h;
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

                                            $PIC_SIGN_ALLOW ="";
                                            if($P_ESIGN_ATT=="Y"){
                                                $PIC_SIGN_ALLOW = getPIC_SIGN($SESS_PER_ID,$ALLOW_CARDNO);
                                            }

                                            $y += $h;
                                            $y += $h;
                                            if($PIC_SIGN_ALLOW ==""){
                                                    $pdf->Text(133,$y+$h,"ผู้ตรวจ ...............................................................");
                                                    $y += $h;
                                                    $pdf->Text(145,$y+$h,"(".$ALLOW_PER_NAME.")");
                                            }else{
                                                    $pdf->Image($PIC_SIGN_ALLOW, 145, $y+$h,40,14,"","");
                                                    $y += $h;
                                                    $y += $h;
                                                    $y += $h;
                                                    $y += $h;
                                                    //$y += $h;
                                                    $pdf->Text(145,$y+$h,"(".$ALLOW_PER_NAME.")");

                                            }

                                    }else{

                                            $y += $h;
                                            $y += $h;
                                            $pdf->Text(133,$y+$h,"ผู้ตรวจ ...............................................................");

                                    } //if ( $PER_AUDIT_FLAG==1 ){

                            } // end if($CurWC_CODE!=""){
                        /*------------------*/
                        //$pdf->SetFont($font,'b',$CH_PRINT_SIZE);
                        $pdf->report_title = "บัญชีลงเวลาปฏิบัติราชการของ".$PERSON_TYPE[$search_per_type]."||$show_date $varORGNAME_1||".$data[KONG]."||$DEPARTMENT_NAME";
                        $pdf->AddPage();
                        $pdf->SetTextColor(0, 0, 0);
                        $page_start_x = $pdf->x;
                        $page_start_y = $pdf->y;

                        $head_text1 = implode(",", $heading_text);
                        $head_width1 = implode(",", $heading_width);
                        $head_align1 = implode(",", $heading_align);
                        $col_function = implode(",", $column_function);

                        //$pdf->SetFont($font,'',$CH_PRINT_SIZE);

                        $pdf->Cell(15, 7, "".(($NUMBER_DISPLAY==2)?convert2thaidigit(getWC($db_dpis,$data[WC_CODE])):getWC($db_dpis,$data[WC_CODE]))."", 0, 50, 'L', 0);

                        $result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", $CH_PRINT_SIZE, "b", "000", "EEEEFF", $COLUMN_FORMAT, $col_function);

                        $CurWC_CODE=$data[WC_CODE].$data[KONG];
                        $cntbWorkFlag_0=0;
                        $cntbWorkFlag_1=0;
                        $cntbWorkFlag_2=0;
                        $cntbWorkFlag_3=0;
                        $no=0;
                } // end if($CurWC_CODE!=$data[WC_CODE]){

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

                $arr_data = (array) null;
                $arr_data[] = $no;
                $arr_data[] = getNamePersonal($db_dpis,$data[PER_ID]);
                $arr_data[] = $dbEntTime;
                $arr_data[] = $dbExitTime;
                $arr_data[] = $Comment;
                $data_align = array("C", "L", "C", "C", "C");
                $result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", $CH_PRINT_SIZE, "", "000000", "");
            }

                $pdf->close_tab(""); 
                $y = $pdf->y;	
                $h = 6;
                if ($y+($h*9) > ($pdf->PageBreakTrigger)) {

                        //$pdf->SetFont($font,'b',$CH_PRINT_SIZE);
                        $pdf->AddPage();
                        $pdf->SetTextColor(0, 0, 0);
                        $y = $pdf->y;	
                }

                //$pdf->SetFont($font,'',$CH_PRINT_SIZE);

                for ($i=1; $i < 8; $i++)
                        $pdf->Text(150+43,$y+($h*$i)," คน");

                if($cntbWorkFlag_0 > 0){$cntbWorkFlag_0=number_format($cntbWorkFlag_0);}else{$cntbWorkFlag_0="-";}
                if($cntbWorkFlag_1 > 0){$cntbWorkFlag_1=number_format($cntbWorkFlag_1);}else{$cntbWorkFlag_1="-";}
                if($cntbWorkFlag_2 > 0){$cntbWorkFlag_2=number_format($cntbWorkFlag_2);}else{$cntbWorkFlag_2="-";}
                if($cntbWorkFlag_3 > 0){$cntbWorkFlag_3=number_format($cntbWorkFlag_3);}else{$cntbWorkFlag_3="-";}
                $pdf->Text(150,$y+$h,$PERSON_TYPE[$search_per_type]."ทั้งหมด");$pdf->Text(150+35,$y+$h,(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($no)):number_format($no)));
                $y += $h;
                $pdf->Text(150,$y+$h,"ตำแหน่งว่าง");$pdf->Text(150+35,$y+$h,"-");
                $y += $h;
                $pdf->Text(150,$y+$h,"ยืนตัวมาช่วยราชการ");$pdf->Text(150+35,$y+$h,"-");
                $y += $h;
                $pdf->Text(150,$y+$h,"มาปฏิบัติราชการ");$pdf->Text(150+35,$y+$h,(($NUMBER_DISPLAY==2)?convert2thaidigit($cntbWorkFlag_0):$cntbWorkFlag_0));
                $y += $h;
                $pdf->Text(150,$y+$h,"ไปราชการ");$pdf->Text(150+35,$y+$h,(($NUMBER_DISPLAY==2)?convert2thaidigit($cntbWorkFlag_3):$cntbWorkFlag_3));
                $y += $h;
                $pdf->Text(150,$y+$h,"มาสาย");$pdf->Text(150+35,$y+$h,(($NUMBER_DISPLAY==2)?convert2thaidigit($cntbWorkFlag_1):$cntbWorkFlag_1));
                $y += $h;
                $pdf->Text(150,$y+$h,"ไม่มาปฏิบัติราชการ");$pdf->Text(150+35,$y+$h,(($NUMBER_DISPLAY==2)?convert2thaidigit($cntbWorkFlag_2):$cntbWorkFlag_2));

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

                        $PIC_SIGN_ALLOW ="";
                        if($P_ESIGN_ATT=="Y"){
                                        $PIC_SIGN_ALLOW = getPIC_SIGN($SESS_PER_ID,$ALLOW_CARDNO);
                        }

                        $y += $h;
                        $y += $h;
                        if($PIC_SIGN_ALLOW ==""){
                                $pdf->Text(133,$y+$h,"ผู้ตรวจ ...............................................................");
                                $y += $h;
                                $pdf->Text(145,$y+$h,"(".$ALLOW_PER_NAME.")");
                        }else{
                                $pdf->Image($PIC_SIGN_ALLOW, 145, $y+$h,40,14,"","");
                                $y += $h;
                                $y += $h;
                                $y += $h;
                                $y += $h;
                                //$y += $h;
                                $pdf->Text(145,$y+$h,"(".$ALLOW_PER_NAME.")");

                        }

                }else{

                        $y += $h;
                        $y += $h;
                        $pdf->Text(133,$y+$h,"ผู้ตรวจ ...............................................................");

                }
            //$pdf->report_title = "บัญชีลงเวลาปฏิบัติราชการของ".$PERSON_TYPE[$search_per_type]."||$show_date||".$data[KONG]."||$DEPARTMENT_NAME";
            //$pdf->AddPage();
        }
    }
	
 	

    $pdf->close();
    $fname = "R1201.pdf";
	$pdf->Output($fname,'D');
?>