<?php include("../../php_scripts/connect_database.php");
    include("../php_scripts/pdf_wordarray_thaicut.php");
    include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
    define('FPDF_FONTPATH','../../PDF/font/');
    include ("../../PDF/es_fpdf.php");
    include ("../../PDF/es_pdf_extends_DPIS.php"); 
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
    
    ini_set("max_execution_time", $max_execution_time);
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
    function getPER_VACATION($PER_ID,$search_date,$AB_COUNT_TOTAL_04,$AB_COUNT_04){
        global $db_dpis;
    }
    
    $unit="mm";
    $paper_size="A4";
    $lang_code="TH";
    $orientation='L';
	
	$search_year_Show = $search_year;
        /* เพิ่มใหม่ */
	if(!$search_per_status){
            $search_per_status = array(1,0,2);
        }
        $varSTATUS = implode(", ", $search_per_status);
        $search_condition_n = "";
	//หาหน่วยงาน
	if($search_org_id || $search_org_ass_id){ 
            if($select_org_structure==0){ // แบบตามกฏหมาย
               $varORGID= $search_org_id;
               $varORGNAME= $search_org_name;
               if($search_org_id_1){
                   $varORGNAME_1= ' - '.$search_org_name_1;
               }
            }else{ //แบบมอบหมายงาน
               $varORGID= $search_org_ass_id;
               $varORGNAME= $search_org_ass_name;
               if($search_org_ass_id_1){
                   $varORGNAME_1= ' - '.$search_org_ass_name_1;
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

	/*---------------------------------------*/
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
	
    
    $arrHdDate = explode("/", $search_date);
    $show_date = "วันที่ ".($arrHdDate[0] + 0) ." เดือน ". $month_full[($arrHdDate[1] + 0)][TH] ." พ.ศ. ". $arrHdDate[2];
    
    $report_title = $DEPARTMENT_NAME."||รายงานการมาปฏิบัติราชการของ".$PERSON_TYPE[$search_per_type]."||ประจำเดือน ".$month_full[($search_month + 0)][TH]." พ.ศ. ". $search_year_Show;
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
    $report_code = "R1204";
    $org_structure="โครงสร้างตามกฎหมาย";
    if($select_org_structure==1 || $PER_AUDIT_FLAG==1){$org_structure="โครงสร้างตามมอบหมายงาน";}
    $company_name ="รูปแบบการออกรายงาน : ".$org_structure." ".$varORGNAME.$varORGNAME_1;
    include ("es_rpt_pdf_R1202_03_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
    session_cache_limiter("private");
    session_start();

    $pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);

    $pdf->Open();
    $pdf->SetFont($font,'',$CH_PRINT_SIZE-3);
    $pdf->SetMargins($Px_DISLEFT,$Px_DISHEAD,$Px_DISRIGHT);
    $pdf->AliasNbPages();
    //$pdf->AddPage();
    $pdf->SetTextColor(0, 0, 0);

    $page_start_x = $pdf->x;
    $page_start_y = $pdf->y;

    $head_text1 = implode(",", $heading_text);
    $head_width1 = implode(",", $heading_width);
    $head_align1 = implode(",", $heading_align);
    $col_function = implode(",", $column_function);

    $pdf->AutoPageBreak = false;

    //$result = $pdf->open_tab($head_text1, $head_width1, 6, "TRHBL", $head_align1, "", $CH_PRINT_SIZE-5, "b", "000", "EEEEFF", $COLUMN_FORMAT, $col_function);
    
    $Arrsearch_date = explode("/", $search_date);
    

    $varBgnDataEat= ($Arrsearch_date[2]-543).'-'.$Arrsearch_date[1].'-'.$Arrsearch_date[0].' 00:00:00';
    $varToDateEat= ($Arrsearch_date[2]-543).'-'.$Arrsearch_date[1].'-'.$Arrsearch_date[0].' 23:59:59';
    $varPerType= $search_per_type;
    $varOrgStructure = 'PER_ORG';
   
    if($select_org_structure==1 || $PER_AUDIT_FLAG==1){$varOrgStructure = 'PER_ORG_ASS';}
    
    //$cmd = file_get_contents('../GetWorkTimeByOrgID.sql');	
    //$cmd=str_ireplace(":ORG_ID",$varORGID,$cmd);
    //$cmd=str_ireplace(":BEGINDATEAT","'".$varBgnDataEat."'",$cmd);
    //$cmd=str_ireplace(":TODATEAT","'".$varToDateEat."'",$cmd);
    //$cmd=str_ireplace(":ORG_TABLE",$varOrgStructure,$cmd);
    //$cmd=str_ireplace(":PER_TYPE",$varPerType,$cmd);
    
    //หาช่วงวันที่ 15 / 30
    if(strlen($search_month)==1){
        $search_month = '0'.$search_month;
    }
    
    //$search_year=($search_year);
    $varBgnDate = $search_year.'-'.$search_month.'-01';
    
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
                  where p.per_status in (:STATUS_IN) and p.per_type=1 and p.per_type in (select per_type from List_Per_Type)
                    and :LAW_OR_ASS=1 and pp.ORG_ID in (select org_id from GetList_ORG_ID)
                  union
                  select per_id from PER_PERSONAL p
                  LEFT JOIN PER_POS_EMP pp on (pp.POEM_ID=p.POEM_ID)
                  where p.per_status in (:STATUS_IN) and p.per_type=2 and p.per_type in (select per_type from List_Per_Type)
                    and :LAW_OR_ASS=1 and pp.ORG_ID in (select org_id from GetList_ORG_ID)
                  union
                  select per_id from PER_PERSONAL p
                  LEFT JOIN PER_POS_EMPSER pp on (pp.POEMS_ID=p.POEMS_ID)
                  where p.per_status in (:STATUS_IN) and p.per_type=3 and p.per_type in (select per_type from List_Per_Type)
                    and :LAW_OR_ASS=1 and pp.ORG_ID in (select org_id from GetList_ORG_ID)
                  union
                  select per_id from PER_PERSONAL p
                  LEFT JOIN PER_POS_TEMP pp on (pp.POT_ID=p.POT_ID)
                  where p.per_status in (:STATUS_IN) and p.per_type=4 and p.per_type in (select per_type from List_Per_Type)
                    and :LAW_OR_ASS=1 and pp.ORG_ID in (select org_id from GetList_ORG_ID)
                  /* By Assignment */
                  union
                  select per_id from PER_PERSONAL p
                  where p.per_status in (:STATUS_IN) and p.per_type in (select per_type from List_Per_Type)
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
						--and TO_CHAR(WORK_DATE, 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') not in ('SAT','SUN')
              			/*and not exists (select NULL 
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
    $db_dpis2->send_cmd_fast($cmdMain);
   //die("<pre>$cmdMain");
    $no=0;
	$no1=0;
    
    $data = $db_dpis2->get_array_array();
	if ($data) {
		do  {
			$AB_COUNT_TOTAL_04=0;
			$SUM_CODE_04=0;
			$TotalCODE_04=0;
			$AB_COUNT_04=0;
			$sum1=0;//01=ลาป่วย
			$sum3=0;//03=ลากิจส่วนตัว
			$sum13=0; //13=ขาดราชการ
			$sum10=0;//10=สาย
			$sum0=0;//0=ทำงาน
			$sum4=0;//04=ลาพักผ่อน
			$sum5=0;//5=ลาอื่นๆ
			if($CurKONG!=$data[KONG]){ // รอบ
				
				if($CurKONG!=""){
	
					$pdf->close_tab(""); 
					$y = $pdf->y;	
					$h = 6;
					if ($y+($h*9) > ($pdf->PageBreakTrigger)) {
						
						//$pdf->SetFont($font,'b',$CH_PRINT_SIZE);
						$pdf->SetFont($font,'',$CH_PRINT_SIZE-3);
						$pdf->AddPage();			
						$pdf->SetTextColor(0, 0, 0);
						$y = $pdf->y;	
					}
					
					$pdf->SetFont($font,'',$CH_PRINT_SIZE-3);
					$pdf->SetFillColor(218,218,218);
					$y += $h;
					$pdf->Rect($Px_DISLEFT,$y,6,7,'FD');
					$pdf->Text(8+$Px_DISLEFT,$y+$h,"= วันหยุดราชการ");
					
					$pdf->Text(60+$Px_DISLEFT,$y+$h,"ป = ลาป่วย");
					$pdf->Text(140+$Px_DISLEFT,$y+$h,"ก = ลากิจส่วนตัว");
					$pdf->Text(210+$Px_DISLEFT,$y+$h,"พ = ลาพักผ่อน");
					
					$y += $h;
					$pdf->Text($Px_DISLEFT,$y+$h,"อบ = ลาอุปสมบทหรือลาไปประกอบพิธีฮัจย์");
					$pdf->Text(60+$Px_DISLEFT,$y+$h,"ตพ = ลาเข้ารับการตรวจเลือกหรือเข้ารับการเตรียมพล");
					$pdf->Text(140+$Px_DISLEFT,$y+$h,"ศ = ลาไปศึกษา ฝึกอบรม ดูงาน หรือปฏิบัติการวิจัย");
					$pdf->Text(210+$Px_DISLEFT,$y+$h,"ปอ = ลาไปปฏิบัติงานในองค์การระหว่างประเทศ");
					
					$y += $h;
					$pdf->Text($Px_DISLEFT,$y+$h,"ค = ลาคลอดบุตร");
					$pdf->Text(60+$Px_DISLEFT,$y+$h,"กบ = ลากิจส่วนตัวเพื่อเลี้ยงดูบุตร");
					$pdf->Text(140+$Px_DISLEFT,$y+$h,"ชภ = ลาไปช่วยเหลือภริยาที่คลอดบุตร");
					$pdf->Text(210+$Px_DISLEFT,$y+$h,"ตส = ลาติดตามคู่สมรส");
					
					$y += $h;
					$pdf->Text($Px_DISLEFT,$y+$h,"ปจ = ลาป่วยจำเป็น");
					$pdf->Text(60+$Px_DISLEFT,$y+$h,"ฟฟ = ลาไปฟื้นฟูสมรรถภาพด้านอาชีพ");
					$pdf->Text(140+$Px_DISLEFT,$y+$h,"ส = สาย");
					$pdf->Text(210+$Px_DISLEFT,$y+$h,"ข = ขาดราชการ");
					
					
					$y += $h;
					$pdf->Text($Px_DISLEFT,$y+$h,"(ช) = ลาเช้า");
					$pdf->Text(60+$Px_DISLEFT,$y+$h,"(บ) = ลาบ่าย");
					
					if($IS_OPEN_TIMEATT_ES=="OPEN"){ // กรณีมีระบบลงเวลา
						$pdf->Text(140+$Px_DISLEFT,$y+$h,"* = ข้อมูลยังไม่ถูกปิดรอบเดือน (ตามมอบหมายงาน)");
					}
					
					
				}
			
				$pdf->SetFont($font,'',$CH_PRINT_SIZE-3);
				$report_titleshow = $DEPARTMENT_NAME."||รายงานการมาปฏิบัติราชการของ".$PERSON_TYPE[$search_per_type]."||ประจำเดือน ".$month_full[($search_month + 0)][TH]." พ.ศ. ". $search_year_Show;
				$report_titleshow = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_titleshow):$report_titleshow);
				$pdf->report_title = $report_titleshow;
				$pdf->company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1 || $PER_AUDIT_FLAG==1 ?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") .$data[KONG]. $varORGNAME_1;
				$pdf->AddPage();
				$pdf->SetTextColor(0, 0, 0);
				
				$head_text1 = implode(",", $heading_text);
				$head_width1 = implode(",", $heading_width);
				$head_align1 = implode(",", $heading_align);
				$col_function = implode(",", $column_function);
				$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", $CH_PRINT_SIZE-5, "b", "000", "EEEEFF", $COLUMN_FORMAT, $col_function);
				
				$sum1=0;//01=ลาป่วย
				$sum3=0;//03=ลากิจส่วนตัว
				$sum13=0; //13=ขาดราชการ
				$sum10=0;//10=สาย
				$sum0=0;//0=ทำงาน
				$sum4=0;//04=ลาพักผ่อน
				$sum5=0;//5=ลาอื่นๆ
				$no=0;
				$no1=0;
				$CurKONG=$data[KONG];
			
			}else{
				
				$no1++;
				if (($no1 % 7) ==0) {
					$pdf->SetFont($font,'',$CH_PRINT_SIZE-3);
					$pdf->AddPage();
					$pdf->print_tab_header();
					$pdf->SetTextColor(0, 0, 0);
					$no1=0;
				}
				
			}
			
			$no++;
			
			$arr_data = (array) null;
			$arr_dataIn = (array) null;
			$arr_dataOut = (array) null;
			$arr_data[] = (($NUMBER_DISPLAY==2)?convert2thaidigit($no):$no);
			$arr_data[] = getNamePersonal($db_dpis,$data[PER_ID]).$data[DATASTAR];
			
			$arr_dataIn[]='';
			$arr_dataIn[]='  เวลาเข้า';
			
			$arr_dataOut[]='';
			$arr_dataOut[]='  เวลาออก';
			
			
			//getPER_VACATION($data[PER_ID],$search_year,$AB_COUNT_TOTAL_04,$AB_COUNT_04);
			// การลาพักผ่อน
			$AS_YEAR = $search_year+543;
			$cmd = " select VC_DAY from PER_VACATION where VC_YEAR='$AS_YEAR'and PER_ID=".$data[PER_ID];
			$db_dpis->send_cmd_fast($cmd);
			$data2 = $db_dpis->get_array_array();
			$AB_COUNT_TOTAL_04 = $data2[VC_DAY]; 	// วันลาพักผ่อนที่ลาได้ทั้งหมดในปีงบประมาณ
			//$AB_COUNT_04 = $AB_COUNT_TOTAL_04 - $SUM_CODE_04 + $ABS_DAY_MFA;		// วันลาสะสมที่เหลือ
			$TMP_START_DATE =  ($search_year-1).'-10-01';
			$TMP_END_DATE =  $search_year.'-09-30';
	
			$cmd = " select sum(ABS_DAY) as abs_day
				from PER_ABSENTHIS 
				where PER_ID=".$data[PER_ID]." and trim(AB_CODE)='04' 
				and ABS_STARTDATE >= '$TMP_START_DATE' and ABS_ENDDATE <= '$TMP_END_DATE' ";
			
			$db_dpis->send_cmd_fast($cmd);
			$data2 = $db_dpis->get_array_array();
			$data2 = array_change_key_case($data2, CASE_LOWER);
			$SUM_CODE_04 = $data2[abs_day]+0; 
			
			$TotalCODE_04 =$AB_COUNT_TOTAL_04-$SUM_CODE_04;
			
			$RectHoliday = array (null);
			$CurRowX = $pdf->GetX();
			$CurRowY = $pdf->GetY();
			for($idx=0;$idx<count($ArrworkDateIN);$idx++){
				if($ArrHol[$idx]==1){ //วันหยุด
					$RectHoliday[] = (substr($ArrworkDateIN[$idx],9,2)+0);
				
				}
				
				
				
				$dataAbs = explode(",", $data[$ArrworkDateIN[$idx]]);
				
				
				
				$dataAbs1='';
				$dataAbs1_after = "";
				
				if($dataAbs[0]){
					$subAbs = substr($dataAbs[0],0,1);
					if($subAbs==9){ // ผู่ที่ไม่ต้องลงเวลา
						$sum0++;
						
						$subAbsNo = substr($dataAbs[1],0,1);
						if($subAbsNo==1 || $subAbsNo==2 || $subAbsNo==3 ){
							$dataAbs_chk = substr($dataAbs[1],2,2);
							if($subAbsNo==1){$subAbs1='(ช)';$subAbs2='';}
							if($subAbsNo==2){$subAbs1='(บ)';$subAbs2='';}
							if($subAbsNo==3){$subAbs1='';$subAbs2='';}
							
							if($dataAbs_chk=='01'){//ลาป่วย
								$dataAbs1=$subAbs2.'ป'.$subAbs1;
								if($subAbsNo==1 || $subAbsNo==2){
									$sum1=$sum1+0.5;
									$sum0=$sum0-0.5;
									
								}else{
									$sum1++;
									$sum0=$sum0-1;
								}
							}elseif($dataAbs_chk=='03'){//ลากิจส่วนตัว
								$dataAbs1=$subAbs2.'ก'.$subAbs1;
								if($subAbsNo==1 || $subAbsNo==2){
									$sum3=$sum3+0.5;
									$sum0=$sum0-0.5;
								}else{
									$sum3++;
									$sum0=$sum0-1;
								}
							}elseif($dataAbs_chk=='04'){//ลาพักผ่อน
								$dataAbs1=$subAbs2.'พ'.$subAbs1;
								if($subAbsNo==1 || $subAbsNo==2){
									$sum4=$sum4+0.5;
									$sum0=$sum0-0.5;
								}else{
									$sum4++;
									$sum0=$sum0-1;
								}
							}elseif($dataAbs_chk=='13'){//ขาดราชการ
								$dataAbs1=''; // ไม่ต้องลงเวลาก็ถือว่าไม่ขาด
							}elseif($dataAbs_chk=='02'){//ลาคลอดบุตร
								$dataAbs1='ค';
								if($subAbsNo==1 || $subAbsNo==2){
									$sum5=$sum5+0.5;
									$sum0=$sum0-0.5;
								}else{
									$sum5++;
									$sum0=$sum0-1;
								}
							}elseif($dataAbs_chk=='05'){//ลาอุปสมบทหรือลาไปประกอบพิธีฮัจย์
								$dataAbs1='อบ';
								if($subAbsNo==1 || $subAbsNo==2){
									$sum5=$sum5+0.5;
									$sum0=$sum0-0.5;
								}else{
									$sum5++;
									$sum0=$sum0-1;
								}
							}elseif($dataAbs_chk=='06'){//ลาเข้ารับการตรวจเลือกหรือเข้ารับการเตรียมพล
								$dataAbs1='ตพ';
								if($subAbsNo==1 || $subAbsNo==2){
									$sum5=$sum5+0.5;
									$sum0=$sum0-0.5;
								}else{
									$sum5++;
									$sum0=$sum0-1;
								}
							}elseif($dataAbs_chk=='07'){//ลาไปศึกษา ฝึกอบรม ดูงาน หรือปฏิบัติการวิจัย
								$dataAbs1='ศ';
								if($subAbsNo==1 || $subAbsNo==2){
									$sum5=$sum5+0.5;
									$sum0=$sum0-0.5;
								}else{
									$sum5++;
									$sum0=$sum0-1;
								}
							}elseif($dataAbs_chk=='08'){//ลาไปปฏิบัติงานในองค์การระหว่างประเทศ
								$dataAbs1='ปอ';
								if($subAbsNo==1 || $subAbsNo==2){
									$sum5=$sum5+0.5;
									$sum0=$sum0-0.5;
								}else{
									$sum5++;
									$sum0=$sum0-1;
								}
							}elseif($dataAbs_chk=='09'){//ลาติดตามคู่สมรส
								$dataAbs1='ตส';
								if($subAbsNo==1 || $subAbsNo==2){
									$sum5=$sum5+0.5;
									$sum0=$sum0-0.5;
								}else{
									$sum5++;
									$sum0=$sum0-1;
								}
							}elseif($dataAbs_chk=='11'){//ลากิจส่วนตัวเพื่อเลี้ยงดูบุตร
								$dataAbs1='กบ';
								if($subAbsNo==1 || $subAbsNo==2){
									$sum5=$sum5+0.5;
									$sum0=$sum0-0.5;
								}else{
									$sum5++;
									$sum0=$sum0-1;
								}
							}elseif($dataAbs_chk=='12'){//ลาป่วยจำเป็น
								$dataAbs1='ปจ';
								if($subAbsNo==1 || $subAbsNo==2){
									$sum5=$sum5+0.5;
									$sum0=$sum0-0.5;
								}else{
									$sum5++;
									$sum0=$sum0-1;
								}
							}elseif($dataAbs_chk=='14'){//ลาไปช่วยเหลือภริยาที่คลอดบุตร
								$dataAbs1='ชภ';
								if($subAbsNo==1 || $subAbsNo==2){
									$sum5=$sum5+0.5;
									$sum0=$sum0-0.5;
								}else{
									$sum5++;
									$sum0=$sum0-1;
								}
							}elseif($dataAbs_chk=='15'){//ลาไปฟื้นฟูสมรรถภาพด้านอาชีพ
								$dataAbs1='ฟฟ';
								if($subAbsNo==1 || $subAbsNo==2){
									$sum5=$sum5+0.5;
									$sum0=$sum0-0.5;
								}else{
									$sum5++;
									$sum0=$sum0-1;
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
								$ArrInOut = explode('(', $dataAbs[1]);
								$ArrInOut2 = explode('-', $ArrInOut[1]);
								$ArrInOut2[1] = str_replace(")","",$ArrInOut2[1]);
								if($subAbs==1 || $subAbs==2 || $subAbs==3 ){
									$dataAbs_chk = substr($dataAbs[2],2,2);
									if($subAbs==1){$subAbs1='(ช)';$subAbs2='';}
									if($subAbs==2){$subAbs1='(บ)';$subAbs2='';}
									if($subAbs==3){$subAbs1='';$subAbs2='';}
									
									if($dataAbs_chk=='01'){//ลาป่วย
										$dataAbs1_after=$XCommar.$subAbs2.'ป'.$subAbs1;
										$sum1=$sum1+0.5;
										$sum0=$sum0-0.5;
									}elseif($dataAbs_chk=='03'){//ลากิจส่วนตัว
										$dataAbs1_after=$XCommar.$subAbs2.'ก'.$subAbs1;
										$sum3=$sum3+0.5;
										$sum0=$sum0-0.5;
									}elseif($dataAbs_chk=='04'){//ลาพักผ่อน
										$dataAbs1_after=$XCommar.$subAbs2.'พ'.$subAbs1;
										$sum4=$sum4+0.5;
										$sum0=$sum0-0.5;
									}elseif($dataAbs_chk=='13'){//ขาดราชการ
										$dataAbs1_after=$XCommar.'';
									}elseif($dataAbs_chk=='02'){//ลาคลอดบุตร
										$dataAbs1_after=$XCommar.'ค';
										$sum5=$sum5+0.5;
										$sum0=$sum0-0.5;
									}elseif($dataAbs_chk=='05'){//ลาอุปสมบทหรือลาไปประกอบพิธีฮัจย์
										$dataAbs1_after=$XCommar.'อบ';
										$sum5=$sum5+0.5;
										$sum0=$sum0-0.5;
									}elseif($dataAbs_chk=='06'){//ลาเข้ารับการตรวจเลือกหรือเข้ารับการเตรียมพล
										$dataAbs1_after=$XCommar.'ตพ';
										$sum5=$sum5+0.5;
										$sum0=$sum0-0.5;
									}elseif($dataAbs_chk=='07'){//ลาไปศึกษา ฝึกอบรม ดูงาน หรือปฏิบัติการวิจัย
										$dataAbs1_after=$XCommar.'ศ';
										$sum5=$sum5+0.5;
										$sum0=$sum0-0.5;
									}elseif($dataAbs_chk=='08'){//ลาไปปฏิบัติงานในองค์การระหว่างประเทศ
										$dataAbs1_after=$XCommar.'ปอ';
										$sum5=$sum5+0.5;
										$sum0=$sum0-0.5;
									}elseif($dataAbs_chk=='09'){//ลาติดตามคู่สมรส
										$dataAbs1_after=$XCommar.'ตส';
										$sum5=$sum5+0.5;
										$sum0=$sum0-0.5;
									}elseif($dataAbs_chk=='11'){//ลากิจส่วนตัวเพื่อเลี้ยงดูบุตร
										$dataAbs1_after=$XCommar.'กบ';
										$sum5=$sum5+0.5;
										$sum0=$sum0-0.5;
									}elseif($dataAbs_chk=='12'){//ลาป่วยจำเป็น
										$dataAbs1_after=$XCommar.'ปจ';
										$sum5=$sum5+0.5;
										$sum0=$sum0-0.5;
									}elseif($dataAbs_chk=='14'){//ลาไปช่วยเหลือภริยาที่คลอดบุตร
										$dataAbs1_after=$XCommar.'ชภ';
										$sum5=$sum5+0.5;
										$sum0=$sum0-0.5;
									}elseif($dataAbs_chk=='15'){//ลาไปฟื้นฟูสมรรถภาพด้านอาชีพ
										$dataAbs1_after=$XCommar.'ฟฟ';
										$sum5=$sum5+0.5;
										$sum0=$sum0-0.5;
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
							if($subAbs==1){$subAbs1='(ช)';$subAbs2='';}
							if($subAbs==2){$subAbs1='(บ)';$subAbs2='';}
							if($subAbs==3){$subAbs1='';$subAbs2='';}
							
							$ArrInF = 0;
							$ArrInL = 0;
							if($dataAbs[1]){
								$ArrInOut_first = explode('-', substr($dataAbs[1],5,11));
								
								if($ArrInOut_first[0] =="XX:XX"){
									$ArrInF = 1;
								}
								
								if($ArrInOut_first[1] =="XX:XX"){
									$ArrInL = 1;
								}
							}
							
							if($dataAbs_chk=='01'){//ลาป่วย
								$dataAbs1=$subAbs2.'ป'.$subAbs1;
								if($subAbs==1 || $subAbs==2){
									$sum1=$sum1+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0+0.5;}
									
								}else{
									$sum1++;
								}
							}elseif($dataAbs_chk=='03'){//ลากิจส่วนตัว
								$dataAbs1=$subAbs2.'ก'.$subAbs1;
								if($subAbs==1 || $subAbs==2){
									$sum3=$sum3+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0+0.5;}
								}else{
									$sum3++;
								}
							}elseif($dataAbs_chk=='04'){//ลาพักผ่อน
								$dataAbs1=$subAbs2.'พ'.$subAbs1;
								if($subAbs==1 || $subAbs==2){
									$sum4=$sum4+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0+0.5;}
								}else{
									$sum4++;
								}
							}elseif($dataAbs_chk=='13'){//ขาดราชการ
								$dataAbs1='ข';/*$subAbs2.'ข'.$subAbs1;*/
								$sum13++;
							}elseif($dataAbs_chk=='02'){//ลาคลอดบุตร
								$dataAbs1='ค';
								if($subAbs==1 || $subAbs==2){
									$sum5=$sum5+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0+0.5;}
								}else{
									$sum5++;
								}
							}elseif($dataAbs_chk=='05'){//ลาอุปสมบทหรือลาไปประกอบพิธีฮัจย์
								$dataAbs1='อบ';
								if($subAbs==1 || $subAbs==2){
									$sum5=$sum5+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0+0.5;}
								}else{
									$sum5++;
								}
							}elseif($dataAbs_chk=='06'){//ลาเข้ารับการตรวจเลือกหรือเข้ารับการเตรียมพล
								$dataAbs1='ตพ';
								if($subAbs==1 || $subAbs==2){
									$sum5=$sum5+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0+0.5;}
								}else{
									$sum5++;
								}
							}elseif($dataAbs_chk=='07'){//ลาไปศึกษา ฝึกอบรม ดูงาน หรือปฏิบัติการวิจัย
								$dataAbs1='ศ';
								if($subAbs==1 || $subAbs==2){
									$sum5=$sum5+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0+0.5;}
								}else{
									$sum5++;
								}
							}elseif($dataAbs_chk=='08'){//ลาไปปฏิบัติงานในองค์การระหว่างประเทศ
								$dataAbs1='ปอ';
								if($subAbs==1 || $subAbs==2){
									$sum5=$sum5+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0+0.5;}
								}else{
									$sum5++;
								}
							}elseif($dataAbs_chk=='09'){//ลาติดตามคู่สมรส
								$dataAbs1='ตส';
								if($subAbs==1 || $subAbs==2){
									$sum5=$sum5+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0+0.5;}
								}else{
									$sum5++;
								}
							}elseif($dataAbs_chk=='11'){//ลากิจส่วนตัวเพื่อเลี้ยงดูบุตร
								$dataAbs1='กบ';
								if($subAbs==1 || $subAbs==2){
									$sum5=$sum5+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0+0.5;}
								}else{
									$sum5++;
								}
							}elseif($dataAbs_chk=='12'){//ลาป่วยจำเป็น
								$dataAbs1='ปจ';
								if($subAbs==1 || $subAbs==2){
									$sum5=$sum5+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0+0.5;}
								}else{
									$sum5++;
								}
							}elseif($dataAbs_chk=='14'){//ลาไปช่วยเหลือภริยาที่คลอดบุตร
								$dataAbs1='ชภ';
								if($subAbs==1 || $subAbs==2){
									$sum5=$sum5+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0+0.5;}
								}else{
									$sum5++;
								}
							}elseif($dataAbs_chk=='15'){//ลาไปฟื้นฟูสมรรถภาพด้านอาชีพ
								$dataAbs1='ฟฟ';
								if($subAbs==1 || $subAbs==2){
									$sum5=$sum5+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0+0.5;}
								}else{
									$sum5++;
								}
							}else{
								$dataAbs1='';
							}
						}else{
							$dataAbs_chkLate = substr($dataAbs[0],1,1);
							if($dataAbs_chkLate==1){
								$dataAbs1='ส';
								$sum10++;
								 $sum0++;
							}elseif($dataAbs_chkLate==2){
								$dataAbs1='ข';
								if(substr($dataAbs[0],0,4)==substr($dataAbs[1],0,4)){
									$sum13++;
								}else{
									$sum13=$sum13+0.5;
								}
							}elseif($dataAbs_chkLate==3){
								$dataAbs1='';
								$sum0++;
							}elseif($dataAbs_chkLate==5){
								$dataAbs1='';
								$sum0++;
							}else{
								$dataAbs1='';
								$sum0++;
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
							$ArrInOut = explode('(', $dataAbs[1]);
							$ArrInOut2 = explode('-', $ArrInOut[1]);
							$ArrInOut2[1] = str_replace(")","",$ArrInOut2[1]);
							if($subAbs==1 || $subAbs==2 || $subAbs==3 ){
								$dataAbs_chk = substr($dataAbs[1],2,2);
								if($subAbs==1){$subAbs1='(ช)';$subAbs2='';}
								if($subAbs==2){$subAbs1='(บ)';$subAbs2='';}
								if($subAbs==3){$subAbs1='';$subAbs2='';}
								
								$ArrInF = 0;
								$ArrInL = 0;
								$ArrInOut_first = explode('-', substr($dataAbs[1],5,11));
								if($ArrInOut_first[0] =="XX:XX"){
									$ArrInF = 1;
								}
								
								if($ArrInOut_first[1] =="XX:XX"){
									$ArrInL = 1;
								}
								
								if($dataAbs_chk=='01'){//ลาป่วย
									$dataAbs1_after=$XCommar.$subAbs2.'ป'.$subAbs1;
									$sum1=$sum1+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0-0.5;}
								}elseif($dataAbs_chk=='03'){//ลากิจส่วนตัว
									$dataAbs1_after=$XCommar.$subAbs2.'ก'.$subAbs1;
									$sum3=$sum3+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0-0.5;}
								}elseif($dataAbs_chk=='04'){//ลาพักผ่อน
									$dataAbs1_after=$XCommar.$subAbs2.'พ'.$subAbs1;
									$sum4=$sum4+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0-0.5;}
								}elseif($dataAbs_chk=='13'){//ขาดราชการ
									$dataAbs1_after=$XCommar.'ข';
									$sum13=$sum13+0.5;
								}elseif($dataAbs_chk=='02'){//ลาคลอดบุตร
									$dataAbs1_after=$XCommar.'ค';
									$sum5=$sum5+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0-0.5;}
								}elseif($dataAbs_chk=='05'){//ลาอุปสมบทหรือลาไปประกอบพิธีฮัจย์
									$dataAbs1_after=$XCommar.'อบ';
									$sum5=$sum5+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0-0.5;}
								}elseif($dataAbs_chk=='06'){//ลาเข้ารับการตรวจเลือกหรือเข้ารับการเตรียมพล
									$dataAbs1_after=$XCommar.'ตพ';
									$sum5=$sum5+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0-0.5;}
								}elseif($dataAbs_chk=='07'){//ลาไปศึกษา ฝึกอบรม ดูงาน หรือปฏิบัติการวิจัย
									$dataAbs1_after=$XCommar.'ศ';
									$sum5=$sum5+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0-0.5;}
								}elseif($dataAbs_chk=='08'){//ลาไปปฏิบัติงานในองค์การระหว่างประเทศ
									$dataAbs1_after=$XCommar.'ปอ';
									$sum5=$sum5+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0-0.5;}
								}elseif($dataAbs_chk=='09'){//ลาติดตามคู่สมรส
									$dataAbs1_after=$XCommar.'ตส';
									$sum5=$sum5+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0-0.5;}
								}elseif($dataAbs_chk=='11'){//ลากิจส่วนตัวเพื่อเลี้ยงดูบุตร
									$dataAbs1_after=$XCommar.'กบ';
									$sum5=$sum5+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0-0.5;}
								}elseif($dataAbs_chk=='12'){//ลาป่วยจำเป็น
									$dataAbs1_after=$XCommar.'ปจ';
									$sum5=$sum5+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0-0.5;}
								}elseif($dataAbs_chk=='14'){//ลาไปช่วยเหลือภริยาที่คลอดบุตร
									$dataAbs1_after=$XCommar.'ชภ';
									$sum5=$sum5+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0-0.5;}
								}elseif($dataAbs_chk=='15'){//ลาไปฟื้นฟูสมรรถภาพด้านอาชีพ
									$dataAbs1_after=$XCommar.'ฟฟ';
									$sum5=$sum5+0.5;
									if($ArrInF==0 && $ArrInL==0){$sum0=$sum0-0.5;}
								}else{
									$dataAbs1_after='';
								}
							}else{
								$dataAbs_chkLate = substr($dataAbs[1],1,1);
								if($dataAbs_chkLate==1){
									$dataAbs1_after=$XCommar.'ส';
									$sum10=$sum10+1;
								}elseif($dataAbs_chkLate==2){
									$dataAbs1_after=$XCommar.'ข';
									$sum13=$sum13+0.5;
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
	
				
				$arr_data[] = $dataAbs1.$dataAbs1_after;
				
				$ArrInShow = "";
				$ArrOutShow = "";
				
				if($subAbs_CHKLoop!=9){
				
					if($dataAbs[1]){
						$ArrInOut_first = explode('-', substr($dataAbs[1],5,11));
						
						if($ArrInOut_first[0] !="XX:XX"){
							$ArrInShow = $ArrInOut_first[0];
						}
						
						if($ArrInOut_first[1] !="XX:XX"){
							$ArrOutShow = $ArrInOut_first[1];
						}
					}
					
				}else{
					
					if($dataAbs[2]){
						$ArrInOut_first = explode('-', substr($dataAbs[2],5,11));
						
						if($ArrInOut_first[0] !="XX:XX"){
							$ArrInShow = $ArrInOut_first[0];
						}
						
						if($ArrInOut_first[1] !="XX:XX"){
							$ArrOutShow = $ArrInOut_first[1];
						}
					}
					
				}
				
				$arr_dataIn[]=$NUMBER_DISPLAY==2?convert2thaidigit($ArrInShow):$ArrInShow;
				$arr_dataOut[]= $NUMBER_DISPLAY==2?convert2thaidigit($ArrOutShow):$ArrOutShow;
			}
			
			$arr_data[] = $sum0<=0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sum0,2)):round($sum0,2));
			$arr_data[] = ($sum1+$sum3+$sum4+$sum5)==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sum1+$sum3+$sum4+$sum5,2)):round($sum1+$sum3+$sum4+$sum5,2));
			$arr_data[] = $sum13==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sum13,2)):round($sum13,2));
			$arr_data[] = $sum10==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sum10,2)):round($sum10,2));
			$arr_data[] = '';
			
			$arr_dataIn[]= '';
			$arr_dataIn[]= '';
			$arr_dataIn[]= '';
			$arr_dataIn[]= '';
			$arr_dataIn[]= '';
			
			$arr_dataOut[]= '';
			$arr_dataOut[]= '';
			$arr_dataOut[]= '';
			$arr_dataOut[]= '';
			$arr_dataOut[]= '';
			
			for($idx=0;$idx<7;$idx++){
				$data_align[]="C";
			}
			$data_align[]="L";
			
			
			$pdf->SetFillColor(218,218,218);
			for ($i=1; $i < sizeof($RectHoliday); $i++) {
				
				$x = $CurRowX + 35.5 + ($RectHoliday[$i] * 6.5);
				$pdf->Rect($x,$CurRowY,6.5,($pdf->GetY()  - $CurRowY+6),'FD');  
				
			}
			$pdf->add_data_tab($arr_data, 6, "TRHBL", $data_align, "", $CH_PRINT_SIZE-5, "", "000000", "");
		

			for ($i=1; $i < sizeof($RectHoliday); $i++) {
				
				$x = $CurRowX + 35.5 + ($RectHoliday[$i] * 6.5);
				$pdf->Rect($x,$CurRowY+6,6.5,($pdf->GetY()  - $CurRowY+6),'FD');  
				
			}
			$pdf->add_data_tab($arr_dataIn, 6, "TRHBL", $data_align, "", $CH_PRINT_SIZE-5, "", "000000", "");//IN
			$pdf->add_data_tab($arr_dataOut, 6, "TRHBL", $data_align, "", $CH_PRINT_SIZE-5, "", "000000", "");//OUT
				
			
			
    	} while ($data = $db_dpis2->get_array_array());
		 //die();
		
	}else{
		$y = $pdf->y;	
		$h = 40;
		$pdf->SetFont($font,'',$CH_PRINT_SIZE-3);
		$pdf->AddPage();
		$pdf->SetTextColor(0, 0, 0);
		$pdf->Text(140,$y+$h,"ไม่พบข้อมูล");
	}

    
    $pdf->close_tab(""); 
	
	$y = $pdf->y;	
	$h = 6;
	if ($y+($h*9) > ($pdf->PageBreakTrigger)) {
		
		//$pdf->SetFont($font,'b',$CH_PRINT_SIZE);
		$pdf->SetFont($font,'',$CH_PRINT_SIZE-3);
		$pdf->AddPage();
		$pdf->SetTextColor(0, 0, 0);
		$y = $pdf->y;	
	}
	
	$pdf->SetFont($font,'',$CH_PRINT_SIZE-3);
	$pdf->SetFillColor(218,218,218);
	//$pdf->SetFillColor(252,251,251);
	$y += $h;
    $pdf->Rect($Px_DISLEFT,$y,6,7,'FD');
	$pdf->Text(8+$Px_DISLEFT,$y+$h,"= วันหยุดราชการ");
	
	$pdf->Text(60+$Px_DISLEFT,$y+$h,"ป = ลาป่วย");
	$pdf->Text(140+$Px_DISLEFT,$y+$h,"ก = ลากิจส่วนตัว");
	$pdf->Text(210+$Px_DISLEFT,$y+$h,"พ = ลาพักผ่อน");
	
	$y += $h;
	$pdf->Text($Px_DISLEFT,$y+$h,"อบ = ลาอุปสมบทหรือลาไปประกอบพิธีฮัจย์");
	$pdf->Text(60+$Px_DISLEFT,$y+$h,"ตพ = ลาเข้ารับการตรวจเลือกหรือเข้ารับการเตรียมพล");
	$pdf->Text(140+$Px_DISLEFT,$y+$h,"ศ = ลาไปศึกษา ฝึกอบรม ดูงาน หรือปฏิบัติการวิจัย");
	$pdf->Text(210+$Px_DISLEFT,$y+$h,"ปอ = ลาไปปฏิบัติงานในองค์การระหว่างประเทศ");
	
	$y += $h;
	$pdf->Text($Px_DISLEFT,$y+$h,"ค = ลาคลอดบุตร");
	$pdf->Text(60+$Px_DISLEFT,$y+$h,"กบ = ลากิจส่วนตัวเพื่อเลี้ยงดูบุตร");
	$pdf->Text(140+$Px_DISLEFT,$y+$h,"ชภ = ลาไปช่วยเหลือภริยาที่คลอดบุตร");
	$pdf->Text(210+$Px_DISLEFT,$y+$h,"ตส = ลาติดตามคู่สมรส");
	
	$y += $h;
	$pdf->Text($Px_DISLEFT,$y+$h,"ปจ = ลาป่วยจำเป็น");
	$pdf->Text(60+$Px_DISLEFT,$y+$h,"ฟฟ = ลาไปฟื้นฟูสมรรถภาพด้านอาชีพ");
	$pdf->Text(140+$Px_DISLEFT,$y+$h,"ส = สาย");
	$pdf->Text(210+$Px_DISLEFT,$y+$h,"ข = ขาดราชการ");
	
	
	$y += $h;
	$pdf->Text($Px_DISLEFT,$y+$h,"(ช) = ลาเช้า");
	$pdf->Text(60+$Px_DISLEFT,$y+$h,"(บ) = ลาบ่าย");
	
	if($IS_OPEN_TIMEATT_ES=="OPEN"){ // กรณีมีระบบลงเวลา
		$pdf->Text(140+$Px_DISLEFT,$y+$h,"* = ข้อมูลยังไม่ถูกปิดรอบเดือน (ตามมอบหมายงาน)");
	}
	
    $pdf->close();
    $fname = "R1204.pdf"; 
	$pdf->Output($fname,'D');	
       
?>