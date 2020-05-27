<?php
    include("../../php_scripts/connect_database.php");
    include("../../php_scripts/calendar_data.php");
    include ("../php_scripts/function_share.php");

    include ("../report/rpt_function.php"); 

    ini_set("max_execution_time", 0);
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
    
    $db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    
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
	
    $search_condition_n = "";
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

   // จัดเรียงข้อมูล
   if(!trim($RPTORD_LIST)){ 
           $RPTORD_LIST = "MINISTRY|";
   }
   $arr_rpt_order = explode("|", $RPTORD_LIST);
   /*ปรับใหม่ http://dpis.ocsc.go.th/Service/node/2054*/
    if(!$search_per_status){
        $search_per_status = array(1,0,2);
    }
    $varSTATUS = implode(", ", $search_per_status);
  // $order_by .= " ORDER BY P.PER_NAME, P.PER_SURNAME";
   /*for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
           $REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
           switch($REPORT_ORDER){
                   case "NAME" :
                           $order_by .= " ,P.PER_NAME, P.PER_SURNAME";
                           break;


           } // end switch case
   } // end for*/
    $Arrsearch_date = explode("/", $search_date);
    
    $varBgnDataEat= ($Arrsearch_date[2]-543).'-'.$Arrsearch_date[1].'-'.$Arrsearch_date[0].' 00:00:00';
    $varToDateEat= ($Arrsearch_date[2]-543).'-'.$Arrsearch_date[1].'-'.$Arrsearch_date[0].' 23:59:59';
    $varPerType= $search_per_type;
    $varOrgStructure = 'PER_ORG';
   
    if($select_org_structure==1 || $PER_AUDIT_FLAG==1){$varOrgStructure = 'PER_ORG_ASS';}
	
	//แสดงข้อมูลว่าปิดรอบแล้วหรือยัง
	$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'IS_OPEN_TIMEATT_ES' ";
	$db_dpis->send_cmd_fast($cmd);
	$data = $db_dpis->get_array_array();
	$IS_OPEN_TIMEATT_ES = $data[CONFIG_VALUE];
	$tmpstar= "''";
	if($IS_OPEN_TIMEATT_ES=="OPEN"){ // กรณีมีระบบลงเวลา
		$search_monthapp = $search_month;
		$tmpstar= "(case when (select APPROVE_DATE from PER_WORK_TIME_CONTROL 
								WHERE  CLOSE_YEAR = $search_year
					  			AND CLOSE_MONTH = $search_monthapp
					  			AND DEPARTMENT_ID = (select ORG_ID from PER_PERSONAL where PER_ID=X.PER_ID)) is null then '*' else '' end
						)";
	}
	
	/////////////////////////////////////
	
    //หาช่วงวันที่ 15 / 30
	
    if(strlen($search_month)==1){
        $search_month = '0'.$search_month;
    }
	
	
	
	$varBgnDate = ($search_year-543).'-'.$search_month.'-01';
    
	
    
    //$search_year=($search_year);
    $varBgnDate = ($search_year-543).'-'.$search_month.'-01';
    
    $cmdBtw="SELECT to_char( work_date,'YYYY-MM-DD') AS WORK_DATE,
	CASe WHEN ph.hol_date is not null THEN 1 ELSE 
	 case when TO_CHAR(work_date, 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') in ('SAT', 'SUN') then 1 else 0 end
	 END AS HOLDAY
            FROM (
                SELECT (TO_DATE(:BEGINDATEAT, 'YYYY-MM-DD'))-1+rownum AS WORK_DATE FROM all_objects
                WHERE (TO_DATE(:BEGINDATEAT, 'YYYY-MM-DD'))-1+ rownum <= last_day(TO_DATE(:BEGINDATEAT, 'YYYY-MM-DD'))
              ) tbmain 
              left join per_holiday ph on(SUBSTR(ph.hol_date,1,10)=to_char( tbmain.work_date,'YYYY-MM-DD'))
			  ";
    $cmdBtw=str_ireplace(":BEGINDATEAT","'".$varBgnDate."'",$cmdBtw);
    $cntDay = $db_dpis2->send_cmd($cmdBtw);
	
    //die($cmdBtw);
    $workDateIN = "";
    $comma=",";
    $iloop = 0;
    if($chkmonth==15){$cntDay=15;}
    
    $ArrHol = array();
    while ($dataDAY = $db_dpis2->get_array_array()) {
        $ArrHol[$iloop]=$dataDAY[HOLDAY];
        $iloop++;
        if($iloop==$cntDay){$comma='';}
        $workDateIN .= "'".$dataDAY[WORK_DATE]."'".$comma;
        if($comma==''){break;}
    }
    $ArrworkDateIN = explode(",", $workDateIN);
    //die($workDateIN);
	
	$CON_PER_AUDIT_FLAG="";
	if ( $PER_AUDIT_FLAG==1 ){
		$tCon="(";
		for ($i=0; $i < count($SESS_AuditArray); $i++) {
			if ($i>0)
				$tCon .= " or ";
			$tCon .= "(P.ORG_ID=" .$SESS_AuditArray[$i][0];
			if ($SESS_AuditArray[$i][1] != 0)
				$tCon .= ' and P.ORG_ID_1='. $SESS_AuditArray[$i][1];
			$tCon .= ")";
		}
		$tCon .= ")";
		 $CON_PER_AUDIT_FLAG .= $tCon;
		
		if($search_org_ass_id){
                    $CON_PER_AUDIT_FLAG .= " AND (P.ORG_ID=$search_org_ass_id)";
                    if($search_org_ass_id_1){
                        $CON_PER_AUDIT_FLAG .= " AND (P.ORG_ID_1=$search_org_ass_id_1)";
                    }
		}	
	}else{
            if($select_org_structure==0) {
                if(trim($search_org_id)){
                    $arr_search_condition[] = "(PP.ORG_ID = $search_org_id)";
                    if(trim($search_org_id_1)){ 
                        $arr_search_condition[] = "(PP.ORG_ID_1 = $search_org_id_1)";
                    } // end if
                } // end if
                
            }else{
                if(trim($search_org_ass_id)){ 
                    $arr_search_condition[] = "(P.ORG_ID = $search_org_ass_id)";
                    if(trim($search_org_ass_id_1)){ 
                        $arr_search_condition[] = "(P.ORG_ID_1 = $search_org_ass_id_1)";
                    } // end if
                } // end if
            }
        }
        
        if(count($arr_search_condition)) $search_condition_n  = " and ". implode(" and ", $arr_search_condition);
	
	if ( $PER_AUDIT_FLAG==1 ){
		$CON_PER_AUDIT_FLAG = " AND (".$CON_PER_AUDIT_FLAG.")";
	}
	
	if($select_org_structure==0 || $PER_AUDIT_FLAG==2) { 
		/*ตามโครงสร้างกฏหมาย*/
		if($search_per_type==1){ 
			$POS_NO_FROM=" LEFT JOIN PER_POSITION PP ON (PP.POS_ID=P.POS_ID)";
		}elseif($search_per_type==2){ 
			$POS_NO_FROM=" LEFT JOIN PER_POS_EMP PP ON (PP.POEM_ID=P.POEM_ID)";
		}elseif($search_per_type==3){ 
			$POS_NO_FROM=" LEFT JOIN PER_POS_EMPSER PP ON (PP.POEMS_ID=P.POEMS_ID)";
		}elseif($search_per_type==4){ 
			$POS_NO_FROM="  LEFT JOIN PER_POS_TEMP PP ON (PP.POT_ID=P.POT_ID)";
		}
		
		$conTPER_ORG = " LEFT JOIN PER_ORG  ORG ON(ORG.ORG_ID=PP.ORG_ID) ";
		
	}else{
		/*ตามโครงสร้างมอบหมายงาน*/
		$conTPER_ORG = " LEFT JOIN PER_ORG_ASS  ORG ON(ORG.ORG_ID=P.ORG_ID) ";
	}
    
    $cmdMain ="WITH
                ALLWORKDAY AS
                (
                  SELECT (TO_DATE(:BEGINDATEAT, 'YYYY-MM-DD'))-1+ROWNUM AS WORK_DATE FROM ALL_OBJECTS
                    WHERE (TO_DATE(:BEGINDATEAT, 'YYYY-MM-DD'))-1+ ROWNUM <= LAST_DAY(TO_DATE(:BEGINDATEAT, 'YYYY-MM-DD'))
                )
                ,GetList_ORG_ID As
                (

                  select org_id from PER_ORG o where :LAW_OR_ASS=1 and o.org_active=1 and o.org_id in
                    (select org_id from PER_ORG start with ORG_ID=:ORG_ID CONNECT BY NOCYCLE PRIOR org_id = ORG_ID_REF)  
                  union
                  select org_id from PER_ORG_ASS o where :LAW_OR_ASS=2 and o.org_active=1 and o.org_id in
                    (select org_id from PER_ORG_ASS start with ORG_ID=:ORG_ID CONNECT BY NOCYCLE PRIOR org_id = ORG_ID_REF)  

                )
                ,List_Per_Type as
                (
                  select CAST(:PER_TYPE AS number) PER_TYPE from dual 
                  union select 1 from dual where :PER_TYPE=0
                  union select 2 from dual where :PER_TYPE=0
                  union select 3 from dual where :PER_TYPE=0
                  union select 4 from dual where :PER_TYPE=0

                )
                ,PersonInDepart As
                (
                  /* By Law */
                  select per_id from PER_PERSONAL p
                  LEFT JOIN PER_POSITION pp on (pp.POS_ID=p.POS_ID)
                  where p.PER_STATUS in (:STATUS_IN) and p.per_type=1 and p.per_type in (select per_type from List_Per_Type)
                    and :LAW_OR_ASS=1 and pp.ORG_ID in (select org_id from GetList_ORG_ID)
                  union
                  select per_id from PER_PERSONAL p
                  LEFT JOIN PER_POS_EMP pp on (pp.POEM_ID=p.POEM_ID)
                  where p.PER_STATUS in (:STATUS_IN) and p.per_type=2 and p.per_type in (select per_type from List_Per_Type)
                    and :LAW_OR_ASS=1 and pp.ORG_ID in (select org_id from GetList_ORG_ID)
                  union
                  select per_id from PER_PERSONAL p
                  LEFT JOIN PER_POS_EMPSER pp on (pp.POEMS_ID=p.POEMS_ID)
                  where p.PER_STATUS in (:STATUS_IN) and p.per_type=3 and p.per_type in (select per_type from List_Per_Type)
                    and :LAW_OR_ASS=1 and pp.ORG_ID in (select org_id from GetList_ORG_ID)
                  union
                  select per_id from PER_PERSONAL p
                  LEFT JOIN PER_POS_TEMP pp on (pp.POT_ID=p.POT_ID)
                  where p.PER_STATUS in (:STATUS_IN) and p.per_type=4 and p.per_type in (select per_type from List_Per_Type)
                    and :LAW_OR_ASS=1 and pp.ORG_ID in (select org_id from GetList_ORG_ID)
                  /* By Assignment */
                  union
                  select per_id from PER_PERSONAL p
                  where p.PER_STATUS in (:STATUS_IN) and p.per_type in (select per_type from List_Per_Type)
                    and :LAW_OR_ASS=2 and p.ORG_ID in (select org_id from GetList_ORG_ID)
                )
				, AllWorksOld as
				(
                    SELECT PER_WORK_TIME.PER_ID,TO_CHAR( WORK_DATE,'YYYY-MM-DD') AS WORK_DATE,
					case when (exists (select null from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=PER_WORK_TIME.PER_ID and trim(ab_code) not in ('10','13')and 
									pa.abs_startdate < cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19)))) then '3'|| NVL(TO_CHAR(WORK_FLAG),'*') ||
									  (select trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=PER_WORK_TIME.PER_ID and trim(ab_code) not in ('10','13') and
									pa.abs_startdate < cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19))) || ',0'|| NVL(TO_CHAR(WORK_FLAG),'*') ||'--'
							  else 
					  
								  nvl((select '3'||NVL(TO_CHAR(WORK_FLAG),'*')||trim(ab_code)||',00--' from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=PER_WORK_TIME.PER_ID and abs_startperiod=3 and trim(ab_code) not in ('10','13')and 
									pa.abs_startdate = cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19)))
								  ,
					  
									nvl(
									  (select to_char(abs_startperiod)||NVL(TO_CHAR(WORK_FLAG),'*')||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=PER_WORK_TIME.PER_ID and abs_startperiod<>2 and trim(ab_code) not in ('10','13')and 
									  pa.abs_startdate = cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19))),
										 (
											nvl((select to_char(abs_startperiod)||NVL(TO_CHAR(WORK_FLAG),'*')||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=PER_WORK_TIME.PER_ID and abs_startperiod<>2 and trim(ab_code) not in ('10','13')and 
												  pa.abs_startdate = cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19))),
												nvl((select to_char(abs_endperiod)||NVL(TO_CHAR(WORK_FLAG),'*')||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=PER_WORK_TIME.PER_ID and abs_endperiod<>2 and trim(ab_code) not in ('10','13')and 
												  pa.abs_startdate < cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19))),
												  0||CASE WHEN REMARK is not null THEN '0' ELSE NVL(TO_CHAR(WORK_FLAG),'*') END||'--')
											  )
										)
									  ) || ',' ||
									  nvl(
									  (select to_char(abs_startperiod)||NVL(TO_CHAR(WORK_FLAG),'*')||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=PER_WORK_TIME.PER_ID and abs_startperiod=2 and trim(ab_code) not in ('10','13')and 
									  pa.abs_startdate = cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19))),
										 (
											nvl((select to_char(abs_startperiod)||NVL(TO_CHAR(WORK_FLAG),'*')||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=PER_WORK_TIME.PER_ID and abs_startperiod=2 and trim(ab_code) not in ('10','13')and 
												  pa.abs_startdate = cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19))),
												0||CASE WHEN REMARK is not null THEN '0' ELSE NVL(TO_CHAR(WORK_FLAG),'*') END||'--'
											  )
										)
									  )
					  
									)
						 end  || ('('|| NVL(TO_CHAR(APV_ENTTIME,'HH24:MI'),'XX:XX') || '-' || NVL(TO_CHAR(APV_EXITTIME,'HH24:MI'),'XX:XX') || ')') 
									  AW
										FROM PER_WORK_TIME 
										WHERE 
										  PER_WORK_TIME.PER_ID IN (SELECT * FROM PERSONINDEPART)
					
							  union
										SELECT PER_ID,TO_CHAR( WORK_DATE,'YYYY-MM-DD') AS WORK_DATE, 
					case when (exists (
								select null from TA_SET_EXCEPTPER a  
											where a.PER_ID=x.PER_ID and CANCEL_FLAG=1                       
											and to_char(WORK_DATE,'YYYY-MM-DD') >= a.START_DATE
											and (a.END_DATE is null or to_char(WORK_DATE,'YYYY-MM-DD') <= a.END_DATE)
										  
										  ))
										  then '9,'
					else ''
					end ||
					case when (exists (select null from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=x.PER_ID and trim(ab_code) not in ('10','13')and 
									pa.abs_startdate < cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19)))) then '3*' ||
									  (select trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=x.PER_ID and trim(ab_code) not in ('10','13') and
									pa.abs_startdate < cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19))) || ',0*--'
							  else 
					  
								  nvl((select '30'||trim(ab_code)||',00--' from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=x.PER_ID and abs_startperiod=3 and trim(ab_code) not in ('10','13')and 
									pa.abs_startdate = cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19)))
								  ,
					  
									nvl(
									  (select to_char(abs_startperiod)||'2'||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=x.PER_ID and abs_startperiod<>2 and trim(ab_code) not in ('10','13')and 
									  pa.abs_startdate = cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19))),
										 (
											nvl((select to_char(abs_startperiod)||'2'||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=x.PER_ID and abs_startperiod<>2 and trim(ab_code) not in ('10','13')and 
												  pa.abs_startdate = cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19))),
												nvl((select to_char(abs_endperiod)||'2'||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=x.PER_ID and abs_endperiod<>2 and trim(ab_code) not in ('10','13')and 
												  pa.abs_startdate < cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19))),
												  '02--')
											  )
										)
									  ) || ',' ||
									  nvl(
									  (select to_char(abs_startperiod)||'2'||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=x.PER_ID and abs_startperiod=2 and trim(ab_code) not in ('10','13')and 
									  pa.abs_startdate = cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19))),
										 (
											nvl((select to_char(abs_startperiod)||'2'||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=x.PER_ID and abs_startperiod=2 and trim(ab_code) not in ('10','13')and 
												  pa.abs_startdate = cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(WORK_DATE,'YYYY-MM-DD') as char(19))),
												'02--'
											  )
										)
									  )
					  
									)
						 end || '(XX:XX-XX:XX)'  
					as AW
                    FROM ALLWORKDAY, (SELECT * FROM PERSONINDEPART) x
                    WHERE WORK_DATE not in (select WORK_DATE FROM PER_WORK_TIME 
                                              WHERE PER_WORK_TIME.PER_ID = x.PER_ID
                                            )
						/*and TO_CHAR(WORK_DATE, 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') not in ('SAT','SUN')
              			and not exists (select NULL 
              			from PER_HOLIDAY where HOL_DATE = to_char(WORK_DATE,'YYYY-MM-DD'))*/


						/*union select PER_ID, to_char(x.WORK_DATE,'YYYY-MM-DD') AS WORK_DATE,'9' AW from TA_SET_EXCEPTPER a, ALLWORKDAY x
						where PER_ID IN (SELECT * FROM PERSONINDEPART) and CANCEL_FLAG=1 
						and to_char(x.WORK_DATE,'YYYY-MM-DD') >= a.START_DATE
						and (a.END_DATE is null or to_char(x.WORK_DATE,'YYYY-MM-DD') <= a.END_DATE)
						and TO_CHAR(x.WORK_DATE, 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') not in ('SAT','SUN')
              			and not exists (select NULL 
              			from PER_HOLIDAY where HOL_DATE = to_char(x.WORK_DATE,'YYYY-MM-DD'))*/
						)
						,AllWorks AS 
						(         
						  SELECT per_id,work_date,LISTAGG(AW, ',') WITHIN GROUP (ORDER BY AW) AS AW
						  FROM   AllWorksOld
						  GROUP BY per_id,work_date
						)

                SELECT X.*,P.PER_NAME,PER_SURNAME,ORG.ORG_NAME AS KONG, ".$tmpstar." AS datastar 
				FROM 
                (
                  SELECT * FROM
                  (
				  	select p.per_id, WORK_DATE, 
							case when   TO_CHAR(to_date(AllWorks.WORK_DATE,'YYYY-MM-DD'), 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') not in ('SAT','SUN')
							                   and not exists (select NULL  from PER_HOLIDAY where SUBSTR(HOL_DATE,1,10) = AllWorks.WORK_DATE)
							 then aw
							 else 
							  case when AW ='02--,02--(XX:XX-XX:XX)' or AW ='9,02--,02--(XX:XX-XX:XX)' 
							  				or substr(AW,3,2) in(select trim(AB_CODE) from PER_ABSENTTYPE where AB_COUNT=2)
											or substr(AW,5,2) in(select trim(AB_CODE) from PER_ABSENTTYPE where AB_COUNT=2)
							 then '' else aw end
							end as aw
                    FROM per_personal p
					left join AllWorks on (AllWorks.per_id=p.per_id)
                    WHERE 
                      p.PER_ID IN (SELECT * FROM PERSONINDEPART)
                  )
                  PIVOT 
                  (
                      MIN(AW) 
                      FOR  WORK_DATE IN (".$workDateIN.")
                  )
                ) X
                LEFT JOIN PER_PERSONAL P ON (P.PER_ID=X.PER_ID)
				$POS_NO_FROM
				$conTPER_ORG
				WHERE 1=1
					$CON_PER_AUDIT_FLAG $search_condition_n
                order by ORG.ORG_NAME ASC,P.PER_NAME ASC , P.PER_SURNAME ASC ";
				 
    $org_structure=1;
    if($select_org_structure==1 || $PER_AUDIT_FLAG==1){$org_structure=2;}
    $cmdMain=str_ireplace(":BEGINDATEAT","'".$varBgnDate."'",$cmdMain);
    $cmdMain=str_ireplace(":PER_TYPE",$varPerType,$cmdMain);
    $cmdMain=str_ireplace(":ORG_ID",$varORGID,$cmdMain);
    $cmdMain=str_ireplace(":LAW_OR_ASS",$org_structure,$cmdMain);
    $cmdMain=str_ireplace(":STATUS_IN",$varSTATUS,$cmdMain);
	//echo '<pre>'.$cmdMain; die();
    $db_dpis2->send_cmd_fast($cmdMain);
	
    
    $arrHdDate = explode("/", $search_date);
    $show_date = "วันที่ ".($arrHdDate[0] + 0) ." เดือน ". $month_full[($arrHdDate[1] + 0)][TH] ." พ.ศ. ". $arrHdDate[2];
    
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
	

	$data = $db_dpis2->get_array_array();
	if ($data) {
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
			if($CurKONG!=$data[KONG]){ // รอบ
				//$KongCount++;
				//if ($KongCount > 20) break;
				
				if($CurKONG!=""){
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
				$CurKONG=$data[KONG];
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
			
			$days = (array) null;
			for($idx=0;$idx<count($ArrworkDateIN);$idx++){
	
				$dataAbs = explode(",", $data[$ArrworkDateIN[$idx]]);
				
				$dataAbs1='';
				$dataAbs1_after = "";
				
				if($dataAbs[0]){
					$subAbs = substr($dataAbs[0],0,1);
					if($subAbs==9){ // ผู่ที่ไม่ต้องลงเวลา
						
						$subAbsNo = substr($dataAbs[1],0,1);
						if($subAbsNo==1 || $subAbsNo==2 || $subAbsNo==3 ){
							$dataAbs_chk = substr($dataAbs[1],2,2);
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
					
					
					
						
						if($subAbs==1 || $subAbs==2 || $subAbs==3 ){
							$dataAbs_chk = substr($dataAbs[0],2,2);
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
											->setCellValue('B'.($no+6), iconv('TIS-620','UTF-8',getNamePersonal($db_dpis,$data[PER_ID]).$data[DATASTAR]));
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
    header('Content-Disposition: attachment;filename="R1203.xls"');
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
