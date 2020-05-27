<?php
    include("../../php_scripts/connect_database.php");
    include("../../php_scripts/calendar_data.php");
    include ("../php_scripts/function_share.php");

    include ("../report/rpt_function.php"); 
    ini_set("max_execution_time", 0);
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

    $db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd); 
	$db_dpis_AB = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$search_year_ex = explode("/",$TIME_STAMP_START);
	$search_year = $search_year_ex[2];
	if(!$sort_by) $sort_by=1;
    if(!$sort_type) $sort_type="1:desc";
    $arrSort=explode(":",$sort_type);
    $SortType[$arrSort[0]]	=$arrSort[1];
    if(!$order_by) $order_by=1;
    
    define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
    //date_default_timezone_set('Asia/Bangkok');
    require_once '../../Excel/eslip/Classes/PHPExcel/IOFactory.php';

    $objReader = PHPExcel_IOFactory::createReader('Excel5');
    if($chkmonth==15){
		if($NUMBER_DISPLAY==2){
       		 $objPHPExcel = $objReader->load("../../Excel/eslip/templates/R1203_template15ColTH.xls");
		}else{
			$objPHPExcel = $objReader->load("../../Excel/eslip/templates/R1203_template15Col.xls");
		}
    }else{
        if($NUMBER_DISPLAY==2){
       		 $objPHPExcel = $objReader->load("../../Excel/eslip/templates/R1203_template31ColTH.xls");
		}else{
			$objPHPExcel = $objReader->load("../../Excel/eslip/templates/R1203_template31Col.xls");
		}
    }
	
	/*if ($objPHPExcel == NULL || !isset($objPHPExcel)) {
		die("xxxxx");
	}*/
	
    include ("es_font_size.php");
	$objPHPExcel->getActiveSheet()->getStyle('A1:AO7')->getFont()->setName(getToFont($CH_PRINT_FONT));
    $objPHPExcel->getActiveSheet()->getStyle('A1:AO7')->getFont()->setSize($CH_PRINT_SIZE-3);
        
	
        
	/*$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont(getToFont($CH_PRINT_FONT))->setSize($CH_PRINT_SIZE);
	$objPHPExcel->getActiveSheet()->getStyle('A1'.':A4')->getFont(getToFont($CH_PRINT_FONT))->setSize($CH_PRINT_SIZE); 
	$objPHPExcel->getActiveSheet()->getStyle('A5'.':Y5')->getFont(getToFont($CH_PRINT_FONT))->setSize($CH_PRINT_SIZE); 
	$objPHPExcel->getActiveSheet()->getStyle('A6'.':Y6')->getFont(getToFont($CH_PRINT_FONT))->setSize($CH_PRINT_SIZE); 
	$objPHPExcel->getActiveSheet()->getStyle('A7'.':Y7')->getFont(getToFont($CH_PRINT_FONT))->setSize($CH_PRINT_SIZE);*/
	
      //หาว่าอยู่กลุ่ม กจ. กรม หรือไม่--------------------------------
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

	if($SELECTED_PER_ID){
		$arr_search_condition[] = "a.PER_ID in ($SELECTED_PER_ID)";	
	}
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

	$count_data = $db_dpis2->send_cmd_fast($cmd);
//	$db_dpis->show_error();
echo "<pre>".$cmd;
die();	

 $workDateIN = "";
    $comma=",";
    $iloop = 0;
    
    $ArrHol = array();
    while ($data = $db_dpis2->get_array_array()) {
        $ArrHol[$iloop]=$data[HOLDAY];
        $iloop++;
        if($iloop==$cntDay){$comma='';}
        $workDateIN .= "'".$data[WORK_DATE]."'".$comma;
		$data_absent .= $data[ABSENT];
        if($comma==''){break;}
		
    }
    $ArrworkDateIN = explode(",", $workDateIN);
	
   /* $org_structure=1;
    if($select_org_structure==1 || $PER_AUDIT_FLAG==1){$org_structure=2;}
    $cmdMain=str_ireplace(":BEGINDATEAT","'".$varBgnDate."'",$cmdMain);
    $cmdMain=str_ireplace(":PER_TYPE",$varPerType,$cmdMain);
    $cmdMain=str_ireplace(":ORG_ID",$varORGID,$cmdMain);
    $cmdMain=str_ireplace(":LAW_OR_ASS",$org_structure,$cmdMain);
    $cmdMain=str_ireplace(":STATUS_IN",$varSTATUS,$cmdMain);
	//echo '<pre>'.$cmdMain; 
	//die();
    $db_dpis2->send_cmd_fast($cmdMain);
	*/
    
    /*$arrHdDate_str = explode("/", $search_date);
	$arrHdDate_end = explode("/", $search_date);
    $show_date = "วันที่ ".($arrHdDate[0] + 0) ." เดือน ". $month_full[($arrHdDate[1] + 0)][TH] ." พ.ศ. ". $arrHdDate[2];*/
    
    $org_structure="โครงสร้างตามกฎหมาย";
    if($select_org_structure==1 || $PER_AUDIT_FLAG==1){$org_structure="โครงสร้างตามมอบหมายงาน";}

    $ArrCol = array('C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG');
    $baseRow = 7;
	$CurKONG="";
	
	
	$no = 0;
	$nox = 1;
	$KongCount = 0;
    //die('<pre>'.$cmd);
	$styleArrayHoliday = array(
                                'font'  => array(
                                    'bold' => true,
                                    'color' => array('rgb' => 'DDDDDD'),
                                    'size'  => 12,
                                    'name' => 'Wingdings'
                                ));
								
	$styleArray1 = array(
                                'font'  => array(
                                    'bold' => true,
                                    'color' => array('rgb' => 'DDDDDD'),
                                    'size'  => 12,
                                    'name' => 'Wingdings'
                                ));
	/*ท้ายตาราง*/							
	$HolidayStr1 = iconv('TIS-620','UTF-8',' = วันหยุดราชการ');
	$HolidayStr2 = iconv('TIS-620','UTF-8','ป = ลาป่วย');
	$HolidayStr3 = iconv('TIS-620','UTF-8','ก = ลากิจส่วนตัว');
	$HolidayStr4 = iconv('TIS-620','UTF-8','พ = ลาพักผ่อน');
	$HolidayStr5 = iconv('TIS-620','UTF-8','อบ = ลาอุปสมบทหรือลาไปประกอบพิธีฮัจย์');
	$HolidayStr6 = iconv('TIS-620','UTF-8','ตพ = ลาเข้ารับการตรวจเลือกหรือเข้ารับการเตรียมพล');
	$HolidayStr7 = iconv('TIS-620','UTF-8','ศ = ลาไปศึกษา ฝึกอบรม ดูงาน หรือปฏิบัติการวิจัย');
	$HolidayStr8 = iconv('TIS-620','UTF-8','ปอ = ลาไปปฏิบัติงานในองค์การระหว่างประเทศ');
	$HolidayStr9 = iconv('TIS-620','UTF-8','ค = ลาคลอดบุตร');
	$HolidayStr10 = iconv('TIS-620','UTF-8','กบ = ลากิจส่วนตัวเพื่อเลี้ยงดูบุตร');
	$HolidayStr11 = iconv('TIS-620','UTF-8','ชภ = ลาไปช่วยเหลือภริยาที่คลอดบุตร');
	$HolidayStr12 = iconv('TIS-620','UTF-8','ตส = ลาติดตามคู่สมรส');
	$HolidayStr13 = iconv('TIS-620','UTF-8','ปจ = ลาป่วยจำเป็น');
	$HolidayStr14 = iconv('TIS-620','UTF-8','ฟฟ = ลาไปฟื้นฟูสมรรถภาพด้านอาชีพ');
	$HolidayStr15 = iconv('TIS-620','UTF-8','ส = สาย');
	$HolidayStr16 = iconv('TIS-620','UTF-8','ข = ขาดราชการ');
	$HolidayStr17 = iconv('TIS-620','UTF-8','(ช) = ลาเช้า');
	$HolidayStr18 = iconv('TIS-620','UTF-8','(บ) = ลาบ่าย');
	$HolidayStr19 = iconv('TIS-620','UTF-8','* = ข้อมูลยังไม่ถูกปิดรอบเดือน (ตามมอบหมายงาน)');
	/*ประเภทการลา*/
	
	$Val_Absent1 = iconv('TIS-620','UTF-8','(ช)');
	$Val_Absent2 = iconv('TIS-620','UTF-8','(บ)');
	$Val_Absent3 = iconv('TIS-620','UTF-8','ป');
	$Val_Absent4 = iconv('TIS-620','UTF-8','ค');
	$Val_Absent5 = iconv('TIS-620','UTF-8','ก');
	$Val_Absent6 = iconv('TIS-620','UTF-8','พ');
	$Val_Absent7 = iconv('TIS-620','UTF-8','ข');
	$Val_Absent8 = iconv('TIS-620','UTF-8','อบ');
	$Val_Absent9 = iconv('TIS-620','UTF-8','ตพ');
	$Val_Absent10 = iconv('TIS-620','UTF-8','ศ');
	$Val_Absent11 = iconv('TIS-620','UTF-8','ปอ');
	$Val_Absent12 = iconv('TIS-620','UTF-8','ตส');
	$Val_Absent13 = iconv('TIS-620','UTF-8','กบ');
	$Val_Absent14 = iconv('TIS-620','UTF-8','ปจ');
	$Val_Absent15 = iconv('TIS-620','UTF-8','ชภ');
	$Val_Absent16 = iconv('TIS-620','UTF-8','ฟฟ');
	$Val_Absent17 = iconv('TIS-620','UTF-8','ส');

		
	if ($count_data) {
		
		do  {
			$sum1=0;//01=ลาป่วย
			$sum3=0;//03=ลากิจส่วนตัว
			$sum13=0; //13=ขาดราชการ
			$sum10=0;//10=สาย
			$sum4=0;//04=ลาพักผ่อน
			$AB_COUNT_TOTAL_04=0;
			$SUM_CODE_04=0;
			$TotalCODE_04=0;
			$AB_COUNT_04=0;
			//die("xxxx"."|".$data1[FULLNAME_SHOW]);
			if(1==1){ // รอบ
				//$KongCount++;
				//if ($KongCount > 20) break;
				
				if($CurKONG!=""){
					//die("xxxx1");
					$clonedSheet->removeRow($no+7);
					$clonedSheet->setCellValue('C'.($no+7+2), iconv('TIS-620','UTF-8','n'))->getStyle('C'.($no+7+2))->applyFromArray($styleArray1);
					$clonedSheet->setCellValue('D'.($no+7+2), $HolidayStr1);
					$clonedSheet->setCellValue('O'.($no+7+2), $HolidayStr2);  
					$clonedSheet->setCellValue('AC'.($no+7+2), $HolidayStr3);  
					$clonedSheet->setCellValue('AM'.($no+7+2), $HolidayStr4); 
					
					$clonedSheet
									->getStyle('C'.($no+7+2))
									->getAlignment()
									->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					
					$clonedSheet->setCellValue('C'.($no+7+4), $HolidayStr5);
					$clonedSheet->setCellValue('O'.($no+7+4), $HolidayStr6);  
					$clonedSheet->setCellValue('AC'.($no+7+4), $HolidayStr7);  
					$clonedSheet->setCellValue('AM'.($no+7+4), $HolidayStr8); 
					
					$clonedSheet->setCellValue('C'.($no+7+6), $HolidayStr9);
					$clonedSheet->setCellValue('O'.($no+7+6), $HolidayStr10);  
					$clonedSheet->setCellValue('AC'.($no+7+6), $HolidayStr11);  
					$clonedSheet->setCellValue('AM'.($no+7+6), $HolidayStr12); 
					
					$clonedSheet->setCellValue('C'.($no+7+8), $HolidayStr13);
					$clonedSheet->setCellValue('O'.($no+7+8), $HolidayStr14);  
					$clonedSheet->setCellValue('AC'.($no+7+8), $HolidayStr15);  
					$clonedSheet->setCellValue('AM'.($no+7+8), $HolidayStr16); 		
				
					$clonedSheet->setCellValue('C'.($no+7+10), $HolidayStr17);  
					$clonedSheet->setCellValue('O'.($no+7+10), $HolidayStr18);  
					
					if($IS_OPEN_TIMEATT_ES=="OPEN"){ // กรณีมีระบบลงเวลา
						$clonedSheet->setCellValue('AC'.($no+7+10), $HolidayStr19);  
					}
					
					
				}
				
				$clonedSheet = clone $objPHPExcel->setActiveSheetIndex(0);
				$value = $nox++;
				$clonedSheet->setTitle(iconv('TIS-620','UTF-8',$value));
				$objPHPExcel->addSheet($clonedSheet);
	
				$company_name ="รูปแบบการออกรายงาน : ".$org_structure." ".$data[KONG].$varORGNAME_1;
				$clonedSheet
						->setCellValue('A1', iconv('TIS-620','UTF-8',$DEPARTMENT_NAME))
						->setCellValue('A2', iconv('TIS-620','UTF-8','รายงานการมาปฏิบัติราชการของ'.$PERSON_TYPE[$varPerType]))
						->setCellValue('A3', iconv('TIS-620','UTF-8','ประจำเดือน '.$month_full[($search_month + 0)][TH].' พ.ศ. '.  (($NUMBER_DISPLAY==2)?convert2thaidigit($search_year):$search_year) ))
						->setCellValue('A4', iconv('TIS-620','UTF-8',$company_name))
						->setCellValue('AL6', iconv('TIS-620','UTF-8','งบ'.(($NUMBER_DISPLAY==2)?convert2thaidigit($search_year):$search_year) ));
				
				
				$sum1=0;//01=ลาป่วย
				$sum3=0;//03=ลากิจส่วนตัว
				$sum13=0; //13=ขาดราชการ
				$sum10=0;//10=สาย
				$sum4=0;//04=ลาพักผ่อน
				$AB_COUNT_TOTAL_04=0;
				$SUM_CODE_04=0;
				$TotalCODE_04=0;
				$AB_COUNT_04=0;
				$no = 0;
				
				//$CurKONG=$data1[FULLNAME_SHOW];
			}
			
			$no++;
			
			// การลาพักผ่อน
			$AS_YEAR = $search_year;
			$cmd = " select VC_DAY from PER_VACATION where VC_YEAR='$AS_YEAR'and PER_ID=".$data[PER_ID];
			$db_dpis->send_cmd_fast($cmd);
			$data2 = $db_dpis->get_array_array();
			$AB_COUNT_TOTAL_04 = $data2[VC_DAY]; 	// วันลาพักผ่อนที่ลาได้ทั้งหมดในปีงบประมาณ
			//$AB_COUNT_04 = $AB_COUNT_TOTAL_04 - $SUM_CODE_04 + $ABS_DAY_MFA;		// วันลาสะสมที่เหลือ
			$TMP_START_DATE =  (($search_year-543)-1).'-10-01';
			$TMP_END_DATE =  ($search_year-543).'-09-30';

			$cmd = " select sum(ABS_DAY) as abs_day
				from PER_ABSENTHIS 
				where PER_ID=".$data[PER_ID]." and trim(AB_CODE)='04' 
				and ABS_STARTDATE >= '$TMP_START_DATE' and ABS_ENDDATE <= '$TMP_END_DATE' ";
			//die($cmd);
			$db_dpis->send_cmd_fast($cmd);
			$data2 = $db_dpis->get_array_array();
			$data2 = array_change_key_case($data2, CASE_LOWER);
			$SUM_CODE_04 = $data2[abs_day]+0; 
			
			$TotalCODE_04 =$AB_COUNT_TOTAL_04-$SUM_CODE_04;
			//$ArrworkDateIN = 
			$days = (array) null;
			//print_r($ArrworkDateIN);	
			for($idx=0;$idx<count($ArrworkDateIN);$idx++){
				$dataAbs="";
				$dataAbs = explode(",", $data_absent);
				print_r($dataAbs);
				echo "";
				$dataAbs1='';
				$dataAbs1_after = "";	
				if($dataAbs[0]){
					$subAbs = substr($dataAbs[0],0,1);
					if($subAbs==9){ // ผู่ที่ไม่ต้องลงเวลา
						$subAbsNo = substr($dataAbs[0],0,1);
						if($subAbsNo==1 || $subAbsNo==2 || $subAbsNo==3 ){
							$dataAbs_chk = substr($dataAbs[0],-2);
							if($subAbsNo==1){$subAbs1=$Val_Absent1;$subAbs2='';}
							if($subAbsNo==2){$subAbs1=$Val_Absent2;$subAbs2='';}
							if($subAbsNo==3){$subAbs1='';$subAbs2='';}
							
							if($dataAbs_chk=='01'){//ลาป่วย
								$dataAbs1=$subAbs2.$Val_Absent3.$subAbs1;
								if($subAbsNo==1 || $subAbsNo==2){
									$sum1=$sum1+0.5;
									
									
								}else{
									$sum1++;
									
								}
							}elseif($dataAbs_chk=='03'){//ลากิจส่วนตัว
								$dataAbs1=$subAbs2.$Val_Absent5.$subAbs1;
								if($subAbsNo==1 || $subAbsNo==2){
									$sum3=$sum3+0.5;
									
								}else{
									$sum3++;
									
								}
							}elseif($dataAbs_chk=='04'){//ลาพักผ่อน
								$dataAbs1=$subAbs2.$Val_Absent6.$subAbs1;
								if($subAbsNo==1 || $subAbsNo==2){
									$sum4=$sum4+0.5;
									
								}else{
									$sum4++;
									
								}
							}elseif($dataAbs_chk=='13'){//ขาดราชการ
								$dataAbs1=''; // ไม่ต้องลงเวลาก็ถือว่าไม่ขาด
							}elseif($dataAbs_chk=='02'){//ลาคลอดบุตร
								$dataAbs1=$Val_Absent4;
								if($subAbsNo==1 || $subAbsNo==2){
									$sum5=$sum5+0.5;
									
								}else{
									$sum5++;
									
								}
							}elseif($dataAbs_chk=='05'){//ลาอุปสมบทหรือลาไปประกอบพิธีฮัจย์
								$dataAbs1=$Val_Absent8;
								if($subAbsNo==1 || $subAbsNo==2){
									$sum5=$sum5+0.5;
									
								}else{
									$sum5++;
									
								}
							}elseif($dataAbs_chk=='06'){//ลาเข้ารับการตรวจเลือกหรือเข้ารับการเตรียมพล
								$dataAbs1=$Val_Absent9;
								if($subAbsNo==1 || $subAbsNo==2){
									$sum5=$sum5+0.5;
									
								}else{
									$sum5++;
									
								}
							}elseif($dataAbs_chk=='07'){//ลาไปศึกษา ฝึกอบรม ดูงาน หรือปฏิบัติการวิจัย
								$dataAbs1=$Val_Absent10;
								if($subAbsNo==1 || $subAbsNo==2){
									$sum5=$sum5+0.5;
									
								}else{
									$sum5++;
									
								}
							}elseif($dataAbs_chk=='08'){//ลาไปปฏิบัติงานในองค์การระหว่างประเทศ
								$dataAbs1=$Val_Absent11;
								if($subAbsNo==1 || $subAbsNo==2){
									$sum5=$sum5+0.5;
									
								}else{
									$sum5++;
									
								}
							}elseif($dataAbs_chk=='09'){//ลาติดตามคู่สมรส
								$dataAbs1=$Val_Absent12;
								if($subAbsNo==1 || $subAbsNo==2){
									$sum5=$sum5+0.5;
									
								}else{
									$sum5++;
									
								}
							}elseif($dataAbs_chk=='11'){//ลากิจส่วนตัวเพื่อเลี้ยงดูบุตร
								$dataAbs1=$Val_Absent13;
								if($subAbsNo==1 || $subAbsNo==2){
									$sum5=$sum5+0.5;
									
								}else{
									$sum5++;
									
								}
							}elseif($dataAbs_chk=='12'){//ลาป่วยจำเป็น
								$dataAbs1=$Val_Absent14;
								if($subAbsNo==1 || $subAbsNo==2){
									$sum5=$sum5+0.5;
									
								}else{
									$sum5++;
									
								}
							}elseif($dataAbs_chk=='14'){//ลาไปช่วยเหลือภริยาที่คลอดบุตร
								$dataAbs1=$Val_Absent15;
								if($subAbsNo==1 || $subAbsNo==2){
									$sum5=$sum5+0.5;
									
								}else{
									$sum5++;
									
								}
							}elseif($dataAbs_chk=='15'){//ลาไปฟื้นฟูสมรรถภาพด้านอาชีพ
								$dataAbs1=$Val_Absent16;
								if($subAbsNo==1 || $subAbsNo==2){
									$sum5=$sum5+0.5;
									
								}else{
									$sum5++;
									
								}
							}else{
								$dataAbs1='';
							}
						}else{
							$dataAbs_chkLate = substr($dataAbs[1],1,1);
							if($dataAbs_chkLate==1){
								$dataAbs1='';
							}elseif($dataAbs_chkLate==2){
								$dataAbs1=''; // ไม่ต้องลงเวลาก็ถือว่าไม่ขาด
							}elseif($dataAbs_chkLate==3){
								$dataAbs1='';
							}elseif($dataAbs_chkLate==5){
								$dataAbs1='';
							}else{
								$dataAbs1='';
							}
						}
						
						if($dataAbs[2]){
							$subAbs_chk0 = substr($dataAbs[1],0,1);
							$subAbs_chk1 = substr($dataAbs[2],0,1);
							$XCommar= "";
							if($subAbs_chk0 != $subAbs_chk1){
								$XCommar= " ";
							}
							if($subAbs_chk0 != $subAbs_chk1){
								$subAbs = substr($dataAbs[2],0,1);
								if($subAbs==1 || $subAbs==2 || $subAbs==3 ){
									$dataAbs_chk = substr($dataAbs[2],2,2);
									if($subAbs==1){$subAbs1=$Val_Absent1;$subAbs2='';}
									if($subAbs==2){$subAbs1=$Val_Absent2;$subAbs2='';}
									if($subAbs==3){$subAbs1='';$subAbs2='';}
									
									if($dataAbs_chk=='01'){//ลาป่วย
										$dataAbs1_after=$XCommar.$subAbs2.$Val_Absent3.$subAbs1;
										$sum1=$sum1+0.5;
										
									}elseif($dataAbs_chk=='03'){//ลากิจส่วนตัว
										$dataAbs1_after=$XCommar.$subAbs2.$Val_Absent5.$subAbs1;
										$sum3=$sum3+0.5;
										
									}elseif($dataAbs_chk=='04'){//ลาพักผ่อน
										$dataAbs1_after=$XCommar.$subAbs2.$Val_Absent6.$subAbs1;
										$sum4=$sum4+0.5;
										
									}elseif($dataAbs_chk=='13'){//ขาดราชการ
										$dataAbs1_after=$XCommar.'';
									}elseif($dataAbs_chk=='02'){//ลาคลอดบุตร
										$dataAbs1_after=$XCommar.$Val_Absent4;
										$sum5=$sum5+0.5;
										
									}elseif($dataAbs_chk=='05'){//ลาอุปสมบทหรือลาไปประกอบพิธีฮัจย์
										$dataAbs1_after=$XCommar.$Val_Absent8;
										$sum5=$sum5+0.5;
										
									}elseif($dataAbs_chk=='06'){//ลาเข้ารับการตรวจเลือกหรือเข้ารับการเตรียมพล
										$dataAbs1_after=$XCommar.$Val_Absent9;
										$sum5=$sum5+0.5;
										
									}elseif($dataAbs_chk=='07'){//ลาไปศึกษา ฝึกอบรม ดูงาน หรือปฏิบัติการวิจัย
										$dataAbs1_after=$XCommar.$Val_Absent10;
										$sum5=$sum5+0.5;
										
									}elseif($dataAbs_chk=='08'){//ลาไปปฏิบัติงานในองค์การระหว่างประเทศ
										$dataAbs1_after=$XCommar.$Val_Absent11;
										$sum5=$sum5+0.5;
										
									}elseif($dataAbs_chk=='09'){//ลาติดตามคู่สมรส
										$dataAbs1_after=$XCommar.$Val_Absent12;
										$sum5=$sum5+0.5;
										
									}elseif($dataAbs_chk=='11'){//ลากิจส่วนตัวเพื่อเลี้ยงดูบุตร
										$dataAbs1_after=$XCommar.$Val_Absent13;
										$sum5=$sum5+0.5;
										
									}elseif($dataAbs_chk=='12'){//ลาป่วยจำเป็น
										$dataAbs1_after=$XCommar.$Val_Absent14;
										$sum5=$sum5+0.5;
										
									}elseif($dataAbs_chk=='14'){//ลาไปช่วยเหลือภริยาที่คลอดบุตร
										$dataAbs1_after=$XCommar.$Val_Absent15;
										$sum5=$sum5+0.5;
										
									}elseif($dataAbs_chk=='15'){//ลาไปฟื้นฟูสมรรถภาพด้านอาชีพ
										$dataAbs1_after=$XCommar.$Val_Absent16;
										$sum5=$sum5+0.5;
										
									}else{
										$dataAbs1_after='';
									}
								}else{
									$dataAbs_chkLate = substr($dataAbs[2],1,1);
									if($dataAbs_chkLate==1){
										$dataAbs1_after='';
									}elseif($dataAbs_chkLate==2){
										$dataAbs1_after='';
									}elseif($dataAbs_chkLate==3){
										$dataAbs1_after='';
									}elseif($dataAbs_chkLate==5){
										$dataAbs1_after='';
									}else{
										$dataAbs1_after='';
									}
								}
										
							}
	
						}
						

						
						
					}else{ // คนที่ต้องลงเวลา

						$dataAbs_chk="";
						if($subAbs==1 || $subAbs==2 || $subAbs==3 ){
							// print_r($dataAbs);
							$dataAbs_chk = substr($dataAbs[0],-2);
							//echo $dataAbs_chk."|";
							if($subAbs==1){$subAbs1=$Val_Absent1;$subAbs2='';}
							if($subAbs==2){$subAbs1=$Val_Absent2;$subAbs2='';}
							if($subAbs==3){$subAbs1='';$subAbs2='';}
							
							if($dataAbs_chk=='01'){//ลาป่วย
								$dataAbs1=$subAbs2.$Val_Absent3.$subAbs1;
								if($subAbs==1 || $subAbs==2){
									$sum1=$sum1+0.5;
									
									
								}else{
									$sum1++;
								}
							}elseif($dataAbs_chk=='03'){//ลากิจส่วนตัว
								$dataAbs1=$subAbs2.$Val_Absent5.$subAbs1;
								if($subAbs==1 || $subAbs==2){
									$sum3=$sum3+0.5;
									
								}else{
									$sum3++;
								}
							}elseif($dataAbs_chk=='04'){//ลาพักผ่อน
								$dataAbs1=$subAbs2.$Val_Absent6.$subAbs1;
								if($subAbs==1 || $subAbs==2){
									$sum4=$sum4+0.5;
									
								}else{
									$sum4++;
								}
							}elseif($dataAbs_chk=='13'){//ขาดราชการ
								$dataAbs1=$Val_Absent7;/*$subAbs2.'ข'.$subAbs1;*/
								$sum13++;
							}elseif($dataAbs_chk=='02'){//ลาคลอดบุตร
								$dataAbs1=$Val_Absent4;
								if($subAbs==1 || $subAbs==2){
									$sum5=$sum5+0.5;
									
								}else{
									$sum5++;
								}
							}elseif($dataAbs_chk=='05'){//ลาอุปสมบทหรือลาไปประกอบพิธีฮัจย์
								$dataAbs1=$Val_Absent8;
								if($subAbs==1 || $subAbs==2){
									$sum5=$sum5+0.5;
									
								}else{
									$sum5++;
								}
							}elseif($dataAbs_chk=='06'){//ลาเข้ารับการตรวจเลือกหรือเข้ารับการเตรียมพล
								$dataAbs1=$Val_Absent9;
								if($subAbs==1 || $subAbs==2){
									$sum5=$sum5+0.5;
									
								}else{
									$sum5++;
								}
							}elseif($dataAbs_chk=='07'){//ลาไปศึกษา ฝึกอบรม ดูงาน หรือปฏิบัติการวิจัย
								$dataAbs1=$Val_Absent10;
								if($subAbs==1 || $subAbs==2){
									$sum5=$sum5+0.5;
									
								}else{
									$sum5++;
								}
							}elseif($dataAbs_chk=='08'){//ลาไปปฏิบัติงานในองค์การระหว่างประเทศ
								$dataAbs1=$Val_Absent11;
								if($subAbs==1 || $subAbs==2){
									$sum5=$sum5+0.5;
									
								}else{
									$sum5++;
								}
							}elseif($dataAbs_chk=='09'){//ลาติดตามคู่สมรส
								$dataAbs1=$Val_Absent12;
								if($subAbs==1 || $subAbs==2){
									$sum5=$sum5+0.5;
									
								}else{
									$sum5++;
								}
							}elseif($dataAbs_chk=='11'){//ลากิจส่วนตัวเพื่อเลี้ยงดูบุตร
								$dataAbs1=$Val_Absent13;
								if($subAbs==1 || $subAbs==2){
									$sum5=$sum5+0.5;
									
								}else{
									$sum5++;
								}
							}elseif($dataAbs_chk=='12'){//ลาป่วยจำเป็น
								$dataAbs1=$Val_Absent14;
								if($subAbs==1 || $subAbs==2){
									$sum5=$sum5+0.5;
									
								}else{
									$sum5++;
								}
							}elseif($dataAbs_chk=='14'){//ลาไปช่วยเหลือภริยาที่คลอดบุตร
								$dataAbs1=$Val_Absent15;
								if($subAbs==1 || $subAbs==2){
									$sum5=$sum5+0.5;
									
								}else{
									$sum5++;
								}
							}elseif($dataAbs_chk=='15'){//ลาไปฟื้นฟูสมรรถภาพด้านอาชีพ
								$dataAbs1=$Val_Absent16;
								if($subAbs==1 || $subAbs==2){
									$sum5=$sum5+0.5;
									
								}else{
									$sum5++;
								}
							}else{
								$dataAbs1='';
							}
							
						}else{
							$dataAbs_chkLate = substr($dataAbs[0],1,1);
							if($dataAbs_chkLate==1){
								$dataAbs1=$Val_Absent17;
								$sum10++;
								 
							}elseif($dataAbs_chkLate==2){
								$dataAbs1=$Val_Absent7;
								if(substr($dataAbs[0],0,4)==substr($dataAbs[1],0,4)){
									$sum13++;
								}else{
									$sum13=$sum13+0.5;
								}
							}elseif($dataAbs_chkLate==3){
								$dataAbs1='';
								
							}elseif($dataAbs_chkLate==5){
								$dataAbs1='';
								
							}else{
								$dataAbs1='';
								
							}
						}
						
						
					}
				}
				
				
				//เอาไว้เช็คค่า
				$subAbs_CHKLoop = 9;
				if($dataAbs[0]){
					$subAbs_CHKLoop = substr($dataAbs[0],0,1);
				}
				
				
				if($subAbs_CHKLoop!=9){
					if($dataAbs[1]){
						$subAbs_chk0 = substr($dataAbs[0],0,1);
						$subAbs_chk1 = substr($dataAbs[1],0,1);
						$XCommar= "";
						if($subAbs_chk0 != $subAbs_chk1){
							$XCommar= " ";
						}
						if($subAbs_chk0 != $subAbs_chk1){
							$subAbs = substr($dataAbs[1],0,1);
							if($subAbs==1 || $subAbs==2 || $subAbs==3 ){
								$dataAbs_chk = substr($dataAbs[1],2,2);
								if($subAbs==1){$subAbs1=$Val_Absent1;$subAbs2='';}
								if($subAbs==2){$subAbs1=$Val_Absent2;$subAbs2='';}
								if($subAbs==3){$subAbs1='';$subAbs2='';}
								
								if($dataAbs_chk=='01'){//ลาป่วย
									$dataAbs1_after=$XCommar.$subAbs2.$Val_Absent3.$subAbs1;
									$sum1=$sum1+0.5;
									
								}elseif($dataAbs_chk=='03'){//ลากิจส่วนตัว
									$dataAbs1_after=$XCommar.$subAbs2.$Val_Absent5.$subAbs1;
									$sum3=$sum3+0.5;
									
								}elseif($dataAbs_chk=='04'){//ลาพักผ่อน
									$dataAbs1_after=$XCommar.$subAbs2.$Val_Absent6.$subAbs1;
									$sum4=$sum4+0.5;
									
								}elseif($dataAbs_chk=='13'){//ขาดราชการ
									$dataAbs1_after=$XCommar.$Val_Absent7;
									$sum13=$sum13+0.5;
								}elseif($dataAbs_chk=='02'){//ลาคลอดบุตร
									$dataAbs1_after=$XCommar.$Val_Absent4;
									$sum5=$sum5+0.5;
									
								}elseif($dataAbs_chk=='05'){//ลาอุปสมบทหรือลาไปประกอบพิธีฮัจย์
									$dataAbs1_after=$XCommar.$Val_Absent8;
									$sum5=$sum5+0.5;
									
								}elseif($dataAbs_chk=='06'){//ลาเข้ารับการตรวจเลือกหรือเข้ารับการเตรียมพล
									$dataAbs1_after=$XCommar.$Val_Absent9;
									$sum5=$sum5+0.5;
									
								}elseif($dataAbs_chk=='07'){//ลาไปศึกษา ฝึกอบรม ดูงาน หรือปฏิบัติการวิจัย
									$dataAbs1_after=$XCommar.$Val_Absent10;
									$sum5=$sum5+0.5;
									
								}elseif($dataAbs_chk=='08'){//ลาไปปฏิบัติงานในองค์การระหว่างประเทศ
									$dataAbs1_after=$XCommar.$Val_Absent11;
									$sum5=$sum5+0.5;
									
								}elseif($dataAbs_chk=='09'){//ลาติดตามคู่สมรส
									$dataAbs1_after=$XCommar.$Val_Absent12;
									$sum5=$sum5+0.5;
									
								}elseif($dataAbs_chk=='11'){//ลากิจส่วนตัวเพื่อเลี้ยงดูบุตร
									$dataAbs1_after=$XCommar.$Val_Absent13;
									$sum5=$sum5+0.5;
									
								}elseif($dataAbs_chk=='12'){//ลาป่วยจำเป็น
									$dataAbs1_after=$XCommar.$Val_Absent14;
									$sum5=$sum5+0.5;
									
								}elseif($dataAbs_chk=='14'){//ลาไปช่วยเหลือภริยาที่คลอดบุตร
									$dataAbs1_after=$XCommar.$Val_Absent15;
									$sum5=$sum5+0.5;
									
								}elseif($dataAbs_chk=='15'){//ลาไปฟื้นฟูสมรรถภาพด้านอาชีพ
									$dataAbs1_after=$XCommar.$Val_Absent16;
									$sum5=$sum5+0.5;
									
								}else{
									$dataAbs1_after='';
								}
							}else{
								$dataAbs_chkLate = substr($dataAbs[1],1,1);
								if($dataAbs_chkLate==1){
									$dataAbs1_after=$XCommar.$Val_Absent17;
									$sum10=$sum10+1;
								}elseif($dataAbs_chkLate==2){
									if(substr($dataAbs[0],0,1)==3) {
										$dataAbs1_after='';
									}else{
										$dataAbs1_after=$XCommar.$Val_Absent7;
										$sum13=$sum13+0.5;
									}
									
								}elseif($dataAbs_chkLate==3){
									$dataAbs1_after='';
								}elseif($dataAbs_chkLate==5){
									$dataAbs1_after='';
								}else{
									$dataAbs1_after='';
								}
							}
									
						}

					}
					
				} // endif($subAbs_CHKLoop!=9){
	
				
				$days[] = $dataAbs1.$dataAbs1_after;
			}
			
			
			$clonedSheet->insertNewRowBefore($no+7,1);
			
			$clonedSheet->setCellValue('A'.($no+6), iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit($no):$no)) )
											->setCellValue('B'.($no+6), iconv('TIS-620','UTF-8',$data[FULLNAME_SHOW]));
			 $clonedSheet
						->getStyle('B'.($no+6))
						->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			
			for($idx=0;$idx<count($ArrworkDateIN);$idx++){
				
				if($ArrHol[$idx]==1){ //วันหยุด
					//$clonedSheet->setCellValue($ArrCol[$idx].($no+6), iconv('TIS-620','UTF-8','n'.$days[$idx]))->getStyle($ArrCol[$idx].($no+6))->applyFromArray($styleArrayHoliday);
					$clonedSheet->setCellValue($ArrCol[$idx].($no+6), 'n'.$days[$idx])->getStyle($ArrCol[$idx].($no+6))->applyFromArray($styleArrayHoliday);
				}else{
					//$clonedSheet->setCellValue($ArrCol[$idx].($no+6), iconv('TIS-620','UTF-8',$days[$idx]));
					$clonedSheet->setCellValue($ArrCol[$idx].($no+6), $days[$idx]);
					
				}
				
				
				
			}
			if(count($ArrworkDateIN)>15){
				
				/*$clonedSheet->setCellValue('AH'.($no+6), iconv('TIS-620','UTF-8',$sum1==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sum1,2)):round($sum1,2))))
											->setCellValue('AI'.($no+6), iconv('TIS-620','UTF-8',$sum3==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sum3,2)):round($sum3,2))))
											->setCellValue('AJ'.($no+6), iconv('TIS-620','UTF-8',$sum13==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sum13,2)):round($sum13,2))))
											->setCellValue('AK'.($no+6), iconv('TIS-620','UTF-8',$sum10==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sum10,2)):round($sum10,2))))
											->setCellValue('AL'.($no+6), iconv('TIS-620','UTF-8',$sum4==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sum4,2)):round($sum4,2))))
											->setCellValue('AM'.($no+6), iconv('TIS-620','UTF-8',''));*/
				$clonedSheet->setCellValue('AH'.($no+6), $sum1==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sum1,2)):round($sum1,2)))
											->setCellValue('AI'.($no+6),$sum3==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sum3,2)):round($sum3,2)))
											->setCellValue('AJ'.($no+6), $sum13==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sum13,2)):round($sum13,2)))
											->setCellValue('AK'.($no+6), $sum10==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sum10,2)):round($sum10,2)))
											->setCellValue('AL'.($no+6), $sum4==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sum4,2)):round($sum4,2)))
											->setCellValue('AM'.($no+6),'');
				$clonedSheet
						->getStyle('A'.($no+6).':AM'.($no+6))
						->getFill()
						->getStartColor()
						->setRGB('FFFFFF');
			}else{
				
				
				/*$clonedSheet->setCellValue('R'.($no+6), iconv('TIS-620','UTF-8',$sum1==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sum1,2)):round($sum1,2))))
											->setCellValue('S'.($no+6), iconv('TIS-620','UTF-8',$sum3==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sum3,2)):round($sum3,2))))
											->setCellValue('T'.($no+6), iconv('TIS-620','UTF-8',$sum13==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sum13,2)):round($sum13,2))))
											->setCellValue('U'.($no+6), iconv('TIS-620','UTF-8',$sum10==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sum10,2)):round($sum10,2))))
											->setCellValue('V'.($no+6), iconv('TIS-620','UTF-8',$sum4==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sum4,2)):round($sum4,2))))
											->setCellValue('W'.($no+6), iconv('TIS-620','UTF-8',''));*/
											
				$clonedSheet->setCellValue('R'.($no+6), $sum1==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sum1,2)):round($sum1,2)))
											->setCellValue('S'.($no+6), $sum3==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sum3,2)):round($sum3,2)))
											->setCellValue('T'.($no+6), $sum13==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sum13,2)):round($sum13,2)))
											->setCellValue('U'.($no+6), $sum10==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sum10,2)):round($sum10,2)))
											->setCellValue('V'.($no+6), $sum4==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sum4,2)):round($sum4,2)))
											->setCellValue('W'.($no+6), '');
				$clonedSheet
						->getStyle('A'.($no+6).':W'.($no+6))
						->getFill()
						->getStartColor()
						->setRGB('FFFFFF');
			}
			 $clonedSheet->getRowDimension(($no+6))->setRowHeight(15);
			//echo  getNamePersonal($db_dpis,$data[PER_ID]).$data[KONG]."<br>";
	
		} while ($data = $db_dpis2->get_array_array());
		
	}else{
		$clonedSheet = clone $objPHPExcel->setActiveSheetIndex(0);
			$value = 1;
			$clonedSheet->setTitle(iconv('TIS-620','UTF-8',$value));
			$objPHPExcel->addSheet($clonedSheet);

			$company_name ="รูปแบบการออกรายงาน : ".$org_structure." ".$varORGNAME.$varORGNAME_1;
			$clonedSheet
					->setCellValue('A1', iconv('TIS-620','UTF-8',$DEPARTMENT_NAME))
					->setCellValue('A2', iconv('TIS-620','UTF-8','รายงานการมาปฏิบัติราชการของ'.$PERSON_TYPE[$varPerType]))
					->setCellValue('A3', iconv('TIS-620','UTF-8','ประจำเดือน '.$month_full[($search_month + 0)][TH].' พ.ศ. '.  (($NUMBER_DISPLAY==2)?convert2thaidigit($search_year):$search_year) ))
					->setCellValue('A4', iconv('TIS-620','UTF-8',$company_name))
					->setCellValue('AL6', iconv('TIS-620','UTF-8','งบ'.(($NUMBER_DISPLAY==2)?convert2thaidigit($search_year):$search_year) ));
			
	
	}
	
	$clonedSheet->removeRow($no+7);
	
	$clonedSheet->getStyle('A'.($no+7+2).':AL7'.($no+7+2))->getFont()->setName(getToFont($CH_PRINT_FONT));
    $clonedSheet->getStyle('A'.($no+7+2).':AL7'.($no+7+2))->getFont()->setSize($CH_PRINT_SIZE-3);
	
	$clonedSheet->setCellValue('C'.($no+7+2), iconv('TIS-620','UTF-8','n'))->getStyle('C'.($no+7+2))->applyFromArray($styleArray1);
	$clonedSheet->setCellValue('D'.($no+7+2), $HolidayStr1);
	$clonedSheet->setCellValue('O'.($no+7+2), $HolidayStr2);  
	$clonedSheet->setCellValue('AC'.($no+7+2), $HolidayStr3);  
	$clonedSheet->setCellValue('AM'.($no+7+2), $HolidayStr4); 
	
	$clonedSheet
					->getStyle('C'.($no+7+2))
					->getAlignment()
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	
	$clonedSheet->setCellValue('C'.($no+7+4), $HolidayStr5);
	$clonedSheet->setCellValue('O'.($no+7+4), $HolidayStr6);  
	$clonedSheet->setCellValue('AC'.($no+7+4), $HolidayStr7);  
	$clonedSheet->setCellValue('AM'.($no+7+4), $HolidayStr8); 
	
	$clonedSheet->setCellValue('C'.($no+7+6), $HolidayStr9);
	$clonedSheet->setCellValue('O'.($no+7+6), $HolidayStr10);  
	$clonedSheet->setCellValue('AC'.($no+7+6), $HolidayStr11);  
	$clonedSheet->setCellValue('AM'.($no+7+6), $HolidayStr12); 
	
	$clonedSheet->setCellValue('C'.($no+7+8), $HolidayStr13);
	$clonedSheet->setCellValue('O'.($no+7+8), $HolidayStr14);  
	$clonedSheet->setCellValue('AC'.($no+7+8), $HolidayStr15);  
	$clonedSheet->setCellValue('AM'.($no+7+8), $HolidayStr16); 		

	$clonedSheet->setCellValue('C'.($no+7+10), $HolidayStr17);  
	$clonedSheet->setCellValue('O'.($no+7+10), $HolidayStr18);  
	
	if($IS_OPEN_TIMEATT_ES=="OPEN"){ // กรณีมีระบบลงเวลา
		$clonedSheet->setCellValue('AC'.($no+7+10), $HolidayStr19);  
	}

	$objPHPExcel->removeSheetByIndex(0);
	
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="R1212.xls"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter ->setpreCalculateFormulas(false);
    $objWriter->save('php://output');

?>
