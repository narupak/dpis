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
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
    
    function getNamePersonal($db_dpis,$perId){
        global $PER_STARTDATE,$month_full;
        $cmd = "SELECT PP.PN_NAME,PPN.PER_NAME,PPN.PER_SURNAME,PER_STARTDATE 
                FROM PER_PERSONAL PPN
                LEFT JOIN PER_PRENAME PP ON(PP.PN_CODE=PPN.PN_CODE)
                WHERE PER_ID = $perId ";
        $db_dpis->send_cmd_fast($cmd);
        $data2 = $db_dpis->get_array_array();
        $fullname = $data2[PN_NAME].$data2[PER_NAME].' '.$data2[PER_SURNAME];
        $Arrdate = explode("-", trim($data2[PER_STARTDATE]));
        $PER_STARTDATE = trim($Arrdate[2]+0).' '.($month_full[($Arrdate[1] + 0)][TH]).' '.($Arrdate[0]+543);
        return $fullname;
    }
    function getPositionName($db_dpis,$perId){
        $cmd = "SELECT POS_ID,POEM_ID ,POEMS_ID,POT_ID,PER_TYPE,LEVEL_NO
                FROM PER_PERSONAL 
                WHERE PER_ID = $perId ";
        $db_dpis->send_cmd_fast($cmd);
        $data2 = $db_dpis->get_array_array();
        $dbPOS_ID = $data2[POS_ID];
        $dbPOEM_ID = $data2[POEM_ID];
        $dbPOEMS_ID = $data2[POEMS_ID];
        $dbPOT_ID = $data2[POT_ID];
        $TMP_PL_NAME='';
        $perType=$data2[PER_TYPE];
        $dbLevelNo=$data2[LEVEL_NO];
        if($perType==1){//POS_ID
            $cmd = " 	select	pl.PL_NAME,po.ORG_NAME
                        from	PER_POSITION pp, PER_LINE pl, PER_ORG po
                        where	pp.POS_ID=$dbPOS_ID and pp.ORG_ID=po.ORG_ID and pp.PL_CODE=pl.PL_CODE ";
            $db_dpis->send_cmd_fast($cmd);
            $dataPOS = $db_dpis->get_array_array();
            $TMP_PL_NAME = TRIM($dataPOS[PL_NAME]);
            $TMP_ORG_NAME = trim($dataPOS[ORG_NAME]);
        }elseif($perType==2){ //POEM_ID
            $cmd = " 	select	pl.PN_NAME,po.ORG_NAME 
                        from PER_POS_EMP pp, PER_POS_NAME pl, PER_ORG po 
                        where pp.POEM_ID=$dbPOEM_ID and pp.ORG_ID=po.ORG_ID and pp.PN_CODE=pl.PN_CODE  ";
            $db_dpis->send_cmd_fast($cmd);
            $dataPOS = $db_dpis->get_array_array();
            $TMP_PL_NAME = trim($dataPOS[PN_NAME]);
            $TMP_ORG_NAME = trim($dataPOS[ORG_NAME]);
        }elseif($perType==3){//POEMS_ID
            $cmd = " 	select	pl.EP_NAME ,po.ORG_NAME
                        from PER_POS_EMPSER pp, PER_EMPSER_POS_NAME pl, PER_ORG po, PER_LEVEL le
                        where pp.POEMS_ID=$dbPOEMS_ID and pp.ORG_ID=po.ORG_ID and pp.EP_CODE=pl.EP_CODE  and pl.LEVEL_NO = le.LEVEL_NO";
            $db_dpis->send_cmd_fast($cmd);
            $dataPOS = $db_dpis->get_array_array();
            $TMP_PL_NAME = trim($dataPOS[EP_NAME]);
            $TMP_ORG_NAME = trim($dataPOS[ORG_NAME]);
        }elseif($perType==4){//POT_ID
            $cmd = " 	select pl.TP_NAME ,po.ORG_NAME
                        from	PER_POS_TEMP pp, PER_TEMP_POS_NAME pl, PER_ORG po 
                        where	pp.POT_ID=$dbPOT_ID and pp.ORG_ID=po.ORG_ID and pp.TP_CODE=pl.TP_CODE  ";
            $db_dpis->send_cmd_fast($cmd);
            $dataPOS = $db_dpis->get_array_array();
            $TMP_PL_NAME = trim($dataPOS[TP_NAME]);
            $TMP_ORG_NAME = trim($dataPOS[ORG_NAME]);
        }
        $TMP_LEVEL_NAME='';
        $cmd = " select POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$dbLevelNo' ";
        $db_dpis->send_cmd_fast($cmd);
        $dataLvl = $db_dpis->get_array_array();
        $TMP_LEVEL_NAME = $dataLvl[POSITION_LEVEL];
        
        
        return $TMP_PL_NAME.$TMP_LEVEL_NAME." หน่วยงาน ".$TMP_ORG_NAME;
    }
    function getAddress($db_dpis,$perId){
        $PER_ADD2="";
        $cmd = " select ADR_NO, ADR_ROAD, ADR_SOI, ADR_MOO, ADR_VILLAGE, ADR_BUILDING, ADR_DISTRICT, DT_CODE, 
                 AP_CODE, PV_CODE, ADR_HOME_TEL, ADR_OFFICE_TEL, ADR_FAX, ADR_MOBILE, ADR_EMAIL, ADR_POSTCODE 
                from PER_ADDRESS where PER_ID=$perId and ADR_TYPE=1 ";
        $db_dpis->send_cmd_fast($cmd);
        $data_dpis1 = $db_dpis->get_array_array();
        
        $DT_CODE_ADR = trim($data_dpis1[DT_CODE]);
        $cmd = " select DT_NAME from PER_DISTRICT where trim(DT_CODE)='$DT_CODE_ADR' ";
        $db_dpis->send_cmd_fast($cmd);
        $data_dpis2 = $db_dpis->get_array_array();
        $DT_NAME_ADR = trim($data_dpis2[DT_NAME]);
        if (!$DT_NAME_ADR) $DT_NAME_ADR = $data_dpis1[ADR_DISTRICT];

        $AP_CODE_ADR = trim($data_dpis1[AP_CODE]);
        $cmd = " select AP_NAME from PER_AMPHUR where trim(AP_CODE)='$AP_CODE_ADR' ";
        $db_dpis->send_cmd_fast($cmd);
        $data_dpis2 = $db_dpis->get_array_array();
        $AP_NAME_ADR = trim($data_dpis2[AP_NAME]);

        $PV_CODE_ADR = trim($data_dpis1[PV_CODE]);
        $cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE_ADR' ";
        $db_dpis->send_cmd_fast($cmd);
        $data_dpis2 = $db_dpis->get_array_array();
        $PV_NAME_ADR = trim($data_dpis2[PV_NAME]);
                
        if($data_dpis1[ADR_VILLAGE]) $PER_ADD2 .= "หมู่บ้าน".$data_dpis1[ADR_VILLAGE]." ";
        if($data_dpis1[ADR_BUILDING]) $PER_ADD2 .= "อาคาร".$data_dpis1[ADR_BUILDING]." ";
        if($data_dpis1[ADR_NO]) $PER_ADD2 .= "เลขที่ ".$data_dpis1[ADR_NO]." ";
        if($data_dpis1[ADR_MOO]) $PER_ADD2 .= "ม.".$data_dpis1[ADR_MOO]." ";
        if($data_dpis1[ADR_SOI]) $PER_ADD2 .= "ซ.".str_replace("ซ.","",str_replace("ซอย","",$data_dpis1[ADR_SOI]))." ";
        if($data_dpis1[ADR_ROAD]) $PER_ADD2 .= "ถ.".str_replace("ถ.","",str_replace("ถนน","",$data_dpis1[ADR_ROAD]))." ";
        if($DT_NAME_ADR) {
            if ($PV_CODE_ADR=="1000") {
                $PER_ADD2 .= "แขวง".$DT_NAME_ADR." ";
            } else {
                $PER_ADD2 .= "ต.".$DT_NAME_ADR." ";
            }
        }
        if($AP_NAME_ADR) {
            if ($PV_CODE_ADR=="1000") {
                $PER_ADD2 .= "เขต".$AP_NAME_ADR." ";
            } else {
                $PER_ADD2 .= "อ.".$AP_NAME_ADR." ";
            }
        }
        if($PV_NAME_ADR) {
            if ($PV_CODE_ADR=="1000") {
                $PER_ADD2 .= $PV_NAME_ADR." ";
            } else {
                $PER_ADD2 .= "จ.".$PV_NAME_ADR." ";
            }
        }
        return $PER_ADD2;
    }
	
	
	function countofyear($db_dpis3,$PER_ID,$AS_YEAR){
		$cmdcnt ="select  NVL(sum(q1.XSUM_DIFF1),0) AS XSUM_DIFF1  from (
									WITH
										AllWorkDay As
										(
										  select * from (
											select x.*,(case when TO_CHAR(TO_DATE(x.LISTDATE,'YYYY-MM-DD'), 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') IN ('SAT', 'SUN') then 1 else
											  case when exists (select null from PER_HOLIDAY where SUBSTR(HOL_DATE,1,10) = x.LISTDATE) then 1 else 0 end end) HOL
											from (
											  select to_char(LISTDATE,'YYYY-MM-DD') LISTDATE from (
												SELECT (TO_DATE((LEAST((select trim(min(ABS_STARTDATE)) from PER_ABSENTHIS where ABS_STARTDATE is not null and PER_ID=$PER_ID),
													  (select min(START_DATE) from PER_ABSENTSUM where START_DATE is not null and PER_ID=$PER_ID))
												), 'YYYY-MM-DD'))-1+rownum AS LISTDATE FROM all_objects
												 WHERE (TO_DATE((LEAST((select trim(min(ABS_STARTDATE)) from PER_ABSENTHIS where ABS_STARTDATE is not null and PER_ID=$PER_ID),
													  (select min(START_DATE) from PER_ABSENTSUM where START_DATE is not null and PER_ID=$PER_ID))
												), 'YYYY-MM-DD HH24:MI:SS'))-1+ rownum <= TO_DATE(GREATEST (
														(select trim(max(ABS_STARTDATE)) from PER_ABSENTHIS where ABS_STARTDATE is not null and PER_ID=$PER_ID),
														(select max(START_DATE) from PER_ABSENTSUM where START_DATE is not null and PER_ID=$PER_ID)), 'YYYY-MM-DD')
											  )
											) x 
										  )
										)
										select AS_YEAR,AS_CYCLE,START_DATE,END_DATE,
										  sum(ABS_DAY_MFA) ABS_DAY_MFA,
										  sum(case when AB_TYPE='04' then abs_day+ab_code_04 else 0 end) as PAKPON_ABS,
										  XSUM1,
										  XSUM1-sum(case when AB_TYPE='04' then ABS_DAY+AB_CODE_04 else 0 end) AS XSUM_DIFF1,
										  LAG(XSUM1-sum(case when AB_TYPE='04' then ABS_DAY+AB_CODE_04 else 0 end), 1, 0) 
										  OVER (ORDER BY AS_YEAR desc, AS_CYCLE asc) AS XSUM_DIFF2
	
										from (
										   select tb1.AS_YEAR,tb1.AS_CYCLE,tb1.START_DATE,tb1.END_DATE,tb1.AB_TYPE,sum(tb1.ABS_DAY) ABS_DAY,sum(tb1.ABS_DAY_MFA) ABS_DAY_MFA,
											NVL(asum.ab_code_04,0) AS ab_code_04,
											CASE WHEN tb1.AS_CYCLE=1
											THEN (select v.VC_DAY from PER_VACATION v where v.VC_YEAR=tb1.AS_YEAR and v.PER_ID=$PER_ID)
											ELSE 0
											END AS XSUM1
										   from ( 
	
											select s.AS_YEAR,s.AS_CYCLE,s.START_DATE,s.END_DATE,t.AB_TYPE,h.ABS_DAY,ABS_DAY_MFA
											from PER_ABSENTSUM s
											left join PER_ABSENTHIS h on (h.PER_ID=s.PER_ID
											  and (trim(h.ABS_STARTDATE) >= trim(s.START_DATE) and trim(h.ABS_ENDDATE) <= trim(s.END_DATE)))
											left join PER_ABSENTTYPE t on (t.AB_CODE=h.AB_CODE)
											where s.PER_ID=$PER_ID and t.AB_TYPE='04'
	
											union all
	
											select  AS_YEAR,AS_CYCLE,START_DATE,END_DATE,AB_TYPE,ABS_DAY ABS_DAY,ABS_DAY_MFA
											from (
											  select t.AB_TYPE,case when LISTDATE=trim(ABS_STARTDATE) then 
																  case when ABS_STARTPERIOD=3 then 1 else 0.5 end
																else case when LISTDATE=trim(ABS_ENDDATE) then 
																		case when ABS_ENDPERIOD=3 then 1 else 0.5 end
																	  else 
																		1
																	 end
															   end abs_day,ABS_DAY_MFA,s.START_DATE,s.END_DATE,s.AS_CYCLE,s.AS_YEAR
											  from PER_ABSENTSUM s
											  left join PER_ABSENTHIS h on (h.PER_ID=s.PER_ID
												and ((trim(h.ABS_STARTDATE) < trim(s.START_DATE) and trim(h.ABS_ENDDATE) between trim(s.START_DATE) and trim(s.END_DATE)) or
													 (trim(h.ABS_ENDDATE) > trim(s.END_DATE) and trim(h.ABS_STARTDATE) between trim(s.START_DATE) and trim(s.END_DATE))) or 
													 (trim(h.ABS_STARTDATE) < trim(s.START_DATE) and trim(h.ABS_ENDDATE) > trim(s.END_DATE))
													 )
											  left join PER_ABSENTTYPE t on (t.AB_CODE=h.AB_CODE and h.PER_ID=s.PER_ID)
											  left join AllWorkDay on (h.PER_ID=s.PER_ID and ((t.AB_COUNT=2 and HOL=0) or (t.AB_COUNT=1 and (HOL=0 or HOL=1)) ) 
													and LISTDATE between trim(h.ABS_STARTDATE) and trim(h.ABS_ENDDATE) and LISTDATE between trim(s.START_DATE) and trim(s.END_DATE))
											  where s.PER_ID= $PER_ID and t.AB_TYPE='04'
											)
										  ) tb1
										  LEFT JOIN PER_ABSENTSUM asum 
											ON(asum.as_year=tb1.as_year and asum.as_cycle=tb1.as_cycle and asum.per_id=$PER_ID)
										  group by tb1.AS_YEAR,tb1.AS_CYCLE,tb1.START_DATE,tb1.END_DATE,tb1.AB_TYPE,
											asum.ab_code_04
										) 
										group by AS_YEAR,AS_CYCLE,START_DATE,END_DATE,XSUM1
								)  q1 WHERE q1.as_year=$AS_YEAR ";
				$db_dpis3->send_cmd($cmdcnt);
				$data3 = $db_dpis3->get_array();
				$TMP_CNTOFYEAR = $data3[XSUM_DIFF1]; 
				//echo "<pre>".$cmdcnt;
				//die();
		return $TMP_CNTOFYEAR;
	}

    
    $unit="mm";
    $paper_size="A4";
    $lang_code="TH";
    $orientation='L';
    

    $report_title = "สถิติการลาของ".$PERSON_TYPE[$search_per_type]."||".$DEPARTMENT_NAME;
    $report_code = "R1210";
    include ("es_rpt_pdf_R1204_04_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
    session_cache_limiter("private");
    session_start();
    
    $company_name="";
    $pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
    
    $pdf->Open();
    $pdf->SetFont($font,'',$CH_PRINT_SIZE-4);
    $pdf->SetMargins($Px_DISLEFT,$Px_DISHEAD,$Px_DISRIGHT);
    $pdf->AliasNbPages();
    $pdf->SetTextColor(0, 0, 0);
    
    $search_condition_n = "";
    $varORGNAME_1 = "";
    if($search_org_id || $search_org_ass_id){
        if($select_org_structure==0 || $PER_AUDIT_FLAG==2){ // แบบตามกฏหมาย
            $ORG_ID= $search_org_id;
            if($search_org_id_1){
                $varORGNAME_1= ' '.$search_org_name_1;
            }
        }else{ //แบบมอบหมายงาน
            $ORG_ID= $search_org_ass_id;
            if($search_org_ass_id_1){
                $varORGNAME_1= ' '.$search_org_ass_name_1;
            }
        }
    }else{
            $ORG_ID= $DEPARTMENT_ID;
    }
    $PER_TYPE=$search_per_type;
    /* เพิ่มใหม่ 2018/08/22 */
    $PER_STATUS = "";
    if($search_per_status){
        $PER_STATUS = "P.PER_STATUS in (". implode(", ", $search_per_status) .")";
    }else{
        $PER_STATUS = "P.PER_STATUS in (0,1,2)";
    }
    
    if($PER_AUDIT_FLAG==1){
		$LAW_OR_ASS = 2;
	}else if($PER_AUDIT_FLAG==2){
		$LAW_OR_ASS = 1;
	}else{
		$LAW_OR_ASS = $select_org_structure+1;
	}
   
   
    $cmd = file_get_contents('../es_rpt_R1204_04.sql');	
	
	$POS_NO_VAL1="";
	$POS_NO_FROM="";
	$PER_NAME_VAL = "";
	$PER_SURNAME_VAL = "";
	$LEVEL_NO_VAL ="";
     if($list_person_type=="SELECT"){
		 if(!$SELECTED_PER_ID){$SELECTED_PER_ID=0;}
        $PERID_VAL = " SELECT * FROM PERSONINDEPART WHERE PER_ID IN($SELECTED_PER_ID) ";
    }elseif($list_person_type == "CONDITION"){
		
		if(trim($search_pos_no)){ 
				if($search_per_type==1){ 
					$POS_NO_VAL1 = " AND  (PP.POS_NO = '$search_pos_no')";
				}elseif($search_per_type==2){ 
					$POS_NO_VAL1 = " AND (PP.POEM_NO = '$search_pos_no')";
				}elseif($search_per_type==3){ 
					$POS_NO_VAL1 = " AND (PP.POEMS_NO = '$search_pos_no')";
				}elseif($search_per_type==4){ 
					$POS_NO_VAL1 = " AND (PP.POT_NO = '$search_pos_no')";	
				}

		} // end if
		
		if(trim($search_name)){ 
				$PER_NAME_VAL = " AND (P.PER_NAME like '$search_name%')";	
		} // end if
		
		if(trim($search_surname)){ 
				$PER_SURNAME_VAL = " AND (P.PER_SURNAME like '$search_surname%')";	
		} // end if
		
		
		if($search_per_type==1){
                    if(trim($search_min_level1) & trim($search_max_level1)){ 
                            $search_min_level=$search_min_level1;
                            $search_max_level=$search_max_level1;

                            $LEVEL_NO_VAL = " AND P.LEVEL_NO in (select LEVEL_NO  from PER_LEVEL where PER_TYPE = $search_per_type and
                                                                                            LEVEL_SEQ_NO between 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_min_level') and 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_max_level')
                                                                                            ) ";	
                    }
                }else if($search_per_type==2){        
                    if(trim($search_min_level2) & trim($search_max_level2)){ 
                            $search_min_level=$search_min_level2;
                            $search_max_level=$search_max_level2;

                            $LEVEL_NO_VAL = " AND P.LEVEL_NO in (select LEVEL_NO  from PER_LEVEL where PER_TYPE = $search_per_type and
                                                                                            LEVEL_SEQ_NO between 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_min_level') and 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_max_level')
                                                                                            ) ";	
                    }
                }else if($search_per_type==3){
                    if(trim($search_min_level3) & trim($search_max_level3)){ 
                            $search_min_level=$search_min_level3;
                            $search_max_level=$search_max_level3;

                            $LEVEL_NO_VAL = " AND P.LEVEL_NO in (select LEVEL_NO  from PER_LEVEL where PER_TYPE = $search_per_type and
                                                                                            LEVEL_SEQ_NO between 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_min_level') and 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_max_level')
                                                                                            ) ";	


                    }
                }else if($search_per_type==4){
                    if(trim($search_min_level4) & trim($search_max_level4)){
                            $search_min_level=$search_min_level4;
                            $search_max_level=$search_max_level4;

                            $LEVEL_NO_VAL = " AND P.LEVEL_NO in (select LEVEL_NO  from PER_LEVEL where PER_TYPE = $search_per_type and
                                                                                            LEVEL_SEQ_NO between 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_min_level') and 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_max_level')
                                                                                            ) ";
                    }
                }
		
        $PERID_VAL = " SELECT * FROM PERSONINDEPART ";
	}elseif($list_person_type == "ALL"){
		$PERID_VAL = " SELECT * FROM PERSONINDEPART ";
    }else{ $PERID_VAL = " SELECT * FROM PERSONINDEPART WHERE PER_ID IN($SESS_PER_ID) "; }
   
   
    $cmd=str_ireplace(":YEARBGN","'".(($search_yearBgn-543)-1)."-10-01'",$cmd);
    $cmd=str_ireplace(":YEAREND","'".($search_yearBgn-543)."-09-30'",$cmd);
    $cmd=str_ireplace(":CONDITION_PER_ID",$PERID_VAL,$cmd);

    $cmd=str_ireplace(":LAW_OR_ASS",$LAW_OR_ASS,$cmd);
    $cmd=str_ireplace(":PER_TYPE",$PER_TYPE,$cmd);
    $cmd=str_ireplace(":ORG_ID",$ORG_ID,$cmd);
    /* เพิ่มใหม่ 2018/08/22 */
    $cmd=str_ireplace(":PER_STATUS",$PER_STATUS,$cmd);
	
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
	}elseif($PER_AUDIT_FLAG==2){
		$CON_PER_AUDIT_FLAG .= " AND P.PER_ID=$SESS_PER_ID ";
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
	
	$cmd=" select x.*,P.PER_STARTDATE from (".$cmd.") x 
		left join PER_PERSONAL P ON(P.PER_ID=x.PER_ID)
		$POS_NO_FROM
		$conTPER_ORG
		WHERE x.m is not null
		$CON_PER_AUDIT_FLAG
		$PER_NAME_VAL
		$PER_SURNAME_VAL 
		$LEVEL_NO_VAL
		$POS_NO_VAL1
                $search_condition_n
		order by ORG.ORG_NAME ASC ,P.PER_NAME ASC , P.PER_SURNAME ASC ,x.m ASC 
	";
        
    //die('<pre>'.$cmd);
    $db_dpis2->send_cmd_fast($cmd);
    $CurPerId=-1;
    $page_start_x = $pdf->x;
    $page_start_y = $pdf->y;

    $head_text1 = implode(",", $heading_text);
    $head_width1 = implode(",", $heading_width);
    $head_align1 = implode(",", $heading_align);
    $col_function = implode(",", $column_function);
    $pdf->AutoPageBreak = false;
    $PerIdName="";
    while ($data = $db_dpis2->get_array_array()) {
        $unit="mm";
        $paper_size="A4";
        $lang_code="TH";
        $orientation='L';
        $pdf->SetFont($font,'',$CH_PRINT_SIZE-4);
        $pdf->SetTextColor(0, 0, 0);
        
        $pdf->AddPage();
        $page_start_x = $pdf->x;
        $page_start_y = $pdf->y;

        $head_text1 = implode(",", $heading_text);
        $head_width1 = implode(",", $heading_width);
        $head_align1 = implode(",", $heading_align);
        $col_function = implode(",", $column_function);
        $pdf->AutoPageBreak = false;
       
        $pdf->SetFont($font,'',$CH_PRINT_SIZE-4);
        $pdf->Cell(20, 7, getNamePersonal($db_dpis,$data[0])." ตำแหน่ง ".getPositionName($db_dpis,$data[0]).$varORGNAME_1, 0, 50, 'L', 0);
        $pdf->Cell(20, 7, "เข้ารับราชการเมื่อ ".(($NUMBER_DISPLAY==2)?convert2thaidigit($PER_STARTDATE):$PER_STARTDATE)." ".(($NUMBER_DISPLAY==2)?convert2thaidigit(getAddress($db_dpis,$data[0])):getAddress($db_dpis,$data[0])), 0, 50, 'L', 0);
        $pdf->Cell(20, 7, "ปีงบประมาณ ".(($NUMBER_DISPLAY==2)?convert2thaidigit($search_yearBgn):$search_yearBgn), 0, 50, 'L', 0);

		$CurRowX = $pdf->GetX();
		$CurRowY = $pdf->GetY();
        

        $result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", $CH_PRINT_SIZE-4, "b", "000", "EEEEFF", $COLUMN_FORMAT, $col_function);

		$pdf->Line($CurRowX,$CurRowY,$CurRowX+$head_width1,$CurRowY+7);
		
		
		$data_align = array("L","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","L");    
        $data_alignSum = array("R","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","L");
        $data_alignSum2 = array("R","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","L");
        $loop=0;
        
        /**/
       // การลาพักผ่อน ยอดยกมา
        $AS_YEAR = $search_yearBgn-1;
        

        if($PerIdName!=$data[0]){
			$TotalCODE_04 = countofyear($db_dpis3,$data[0],$AS_YEAR); 
		//	echo "<pre>".$TotalCODE_04."<br>";
		}
		
		$PerIdName=$data[0];
		
		if(date('m') >=10){$YEARFurter=date('Y');}else{$YEARFurter=date('Y')-1;}
		
		//echo "<pre>".$TotalCODE_04."<br>";
		
		if( ($AS_YEAR-543) > $YEARFurter){
			$TotalCODE_04=0;
		}else{
			$CHECK_DATE = (($AS_YEAR-543)-1).'-10-01';
			$CHKPER_STARTDATE = (substr($data[PER_STARTDATE],0,4)-1).substr($data[PER_STARTDATE],5,2).substr($data[PER_STARTDATE],8,2);
			$CHKPER_STARTDATEL = (substr($data[PER_STARTDATE],0,4)-1)."-".substr($data[PER_STARTDATE],5,2)."-".substr($data[PER_STARTDATE],8,2);
			//echo $CHKPER_STARTDATE."|".$CHECK_DATE."<br>";
			if( $CHKPER_STARTDATE <= (($AS_YEAR-543)-1).'1001' ){
				$AGE_DIFF = date_difference($CHECK_DATE, $CHKPER_STARTDATEL, "full");/* เชค ว่า ฟังชันนี้คำนวณอย่างไร*/
				$arr_temp = explode(" ", $AGE_DIFF);
				$Governor_Age = ($arr_temp[0]*10000)+($arr_temp[2]*100)+(($arr_temp[4]*1));
				if($Governor_Age>=100000){ 
					if($TotalCODE_04>20){$TotalCODE_04=20;}
				}elseif($Governor_Age>600){
					if($TotalCODE_04>10){$TotalCODE_04=10;}
				}
			}else{
				$TotalCODE_04=0;
			}
		}
        
        
        /*ยอดปีนี้*/
		//echo $CHKPER_STARTDATENow."|".$CHECK_DATENowF."<br>";
		//echo $CHECK_DATENow."|".$CHKPER_STARTDATENow."<br>";
		//echo ($AS_YEAR-543)."|".$YEARFurter."<br>";
		if( ($AS_YEAR-543) > $YEARFurter){
			
			$AB_COUNT_TOTAL_04NOW=0;
		}elseif( ($AS_YEAR-543) == $YEARFurter){
				$AS_YEARNow = $search_yearBgn;
				$CHECK_DATENow = (($AS_YEARNow-543)-1)."-".date('m-d');
				$CHECK_DATENowF = (($AS_YEARNow-543)-1).date('md');
				$CHKPER_STARTDATENow = (substr($data[PER_STARTDATE],0,4))."-".substr($data[PER_STARTDATE],5,2)."-".substr($data[PER_STARTDATE],8,2);
				$CHKPER_STARTDATENowF = (substr($data[PER_STARTDATE],0,4)).substr($data[PER_STARTDATE],5,2).substr($data[PER_STARTDATE],8,2);
				//echo $CHECK_DATENow."|".$CHKPER_STARTDATENow."<br>";
				if( $CHKPER_STARTDATENowF <= $CHECK_DATENowF){
					$AGE_DIFF = date_difference($CHECK_DATENow, $CHKPER_STARTDATENow, "full");/* เชค ว่า ฟังชันนี้คำนวณอย่างไร*/
					//echo $AGE_DIFF."<br>";
					$arr_temp = explode(" ", $AGE_DIFF);
					$Governor_Age = ($arr_temp[0]*10000)+($arr_temp[2]*100)+(($arr_temp[4]*1));
					$AB_COUNT_TOTAL_04NOW = 0;
					//echo $Governor_Age."<br>";
					if($Governor_Age>600){
						$AB_COUNT_TOTAL_04NOW = 10;
					}else{
						$AB_COUNT_TOTAL_04NOW = 0;
					}
				}else{
					$AB_COUNT_TOTAL_04NOW = 0;
				}
	
		}else{
				$YearNows=date('Y')+543;
				$AS_YEARNow = $search_yearBgn;
				//echo $search_yearBgn."|".$YearNows."<br>";
				//echo ($AS_YEAR-543)."|".$YEARFurter."<br>";
				if($search_yearBgn >= $YearNows ){
					if( ($AS_YEAR-543) == $YEARFurter){
						$CHECK_DATENow = (($AS_YEARNow-543)-1)."-".date('m-d');
						$CHECK_DATENowF = (($AS_YEARNow-543)-1).date('md');
						$CHKPER_STARTDATENow = (substr($data[PER_STARTDATE],0,4)-1)."-".substr($data[PER_STARTDATE],5,2)."-".substr($data[PER_STARTDATE],8,2);
						$CHKPER_STARTDATENowF = (substr($data[PER_STARTDATE],0,4)-1).substr($data[PER_STARTDATE],5,2).substr($data[PER_STARTDATE],8,2);
					}elseif( ($AS_YEAR-543) > $YEARFurter){
						$CHECK_DATENow = (($AS_YEARNow-543))."-".date('m-d');
						$CHECK_DATENowF = (($AS_YEARNow-543)).date('md');
						$CHKPER_STARTDATENow = substr($data[PER_STARTDATE],0,4)."-".substr($data[PER_STARTDATE],5,2)."-".substr($data[PER_STARTDATE],8,2);
					    $CHKPER_STARTDATENowF = substr($data[PER_STARTDATE],0,4).substr($data[PER_STARTDATE],5,2).substr($data[PER_STARTDATE],8,2);
					}else{
						$CHECK_DATENow = (($AS_YEARNow-543)-1).'-10-01';
						$CHECK_DATENowF = (($AS_YEARNow-543)-1).'1001';
						$CHKPER_STARTDATENow = (substr($data[PER_STARTDATE],0,4)-1)."-".substr($data[PER_STARTDATE],5,2)."-".substr($data[PER_STARTDATE],8,2);
						$CHKPER_STARTDATENowF = (substr($data[PER_STARTDATE],0,4)-1).substr($data[PER_STARTDATE],5,2).substr($data[PER_STARTDATE],8,2);
						
					}
					
				}else{
					$CHECK_DATENow = (($AS_YEARNow-543)-1).'-10-01';
					$CHECK_DATENowF = (($AS_YEARNow-543)-1).'1001';
					$CHKPER_STARTDATENow = (substr($data[PER_STARTDATE],0,4)-1)."-".substr($data[PER_STARTDATE],5,2)."-".substr($data[PER_STARTDATE],8,2);
					$CHKPER_STARTDATENowF = (substr($data[PER_STARTDATE],0,4)-1).substr($data[PER_STARTDATE],5,2).substr($data[PER_STARTDATE],8,2);
				}
		
				//echo $CHECK_DATENow."|".$CHKPER_STARTDATENow."<br>";
				//echo $CHKPER_STARTDATENowF."|".$CHECK_DATENowF."<br>";
				if( $CHKPER_STARTDATENowF <= $CHECK_DATENowF){
					$AGE_DIFF = date_difference($CHECK_DATENow, $CHKPER_STARTDATENow, "full");/* เชค ว่า ฟังชันนี้คำนวณอย่างไร*/
					//echo $AGE_DIFF."<br>";
					$arr_temp = explode(" ", $AGE_DIFF);
					$Governor_Age = ($arr_temp[0]*10000)+($arr_temp[2]*100)+(($arr_temp[4]*1));
					$AB_COUNT_TOTAL_04NOW = 0;
					//echo $Governor_Age."<br>";
					if($Governor_Age>600){
						$AB_COUNT_TOTAL_04NOW = 10;
					}else{
						$AB_COUNT_TOTAL_04NOW = 0;
					}
				}else{
					$AB_COUNT_TOTAL_04NOW = 0;
				}
				
		}
        
        $SumAB_COUNT = $AB_COUNT_TOTAL_04NOW+$TotalCODE_04;
       /**/ 
        $TotalsumSick=0;
        $TotalsumLaKid=0;
        $TotalsumPak=0;
        $TotalsumSai=0;
        $TotalsumKad=0;
        
        $TotalsumSick6=0;
        $TotalsumLaKid6=0;
        $TotalsumPak6=0;
        $TotalsumSai6=0;
        $TotalsumKad6=0;
        /**/
        $curYY=0;
        do {
             $arr_data = (array) null; //$month_full[($Arrdate[1] + 0)][TH]
             $arrMMYYYY = explode('-', $data[1]);
             $YY = substr(($arrMMYYYY[0]+543),2,2);
             $txtYY = '';
             if($curYY!=$YY){
                 $txtYY = '-'.$YY;
             }
             $arr_data[] = $month_full[($arrMMYYYY[1] + 0)][TH].$txtYY;//เดือน
             $curYY = $YY;
            $sumSick=0;
            $sumLaKid=0;
            $sumPak=0;
            $sumSai=0;
            $sumKad=0;

            $RectHoliday = array (null);
            $CurRowX = $pdf->GetX();
            $CurRowY = $pdf->GetY();
            
             for($idx=2;$idx<33;$idx++){
                 
				 $ArrData = explode(',',$data[$idx]);
                // if(strlen($data[$idx])==1){
                     if($ArrData[0]=='1' ){
                        $RectHoliday[] = ($idx-2); 
					 }
                     //}else{
                        // $arr_data[] ='';
                     //}
                // }else{
					 
					$arr_dataF = "";
                    
					
					if($ArrData[1]=='2'){  // กรณีขาด
						$sumKad++;
						
						$ChkTime = explode('|',$ArrData[2]);
						if(substr($ChkTime[0],0,1)!='3'){ //มีการลา
						
							if($ChkTime[0]!='000'){ // ข้อมูลเช้า
								$ChkAbsentType=substr($ChkTime[0],1,2); // ประเภทการลา
								if($ChkAbsentType=='01'){
									$arr_dataF .='/ป ข';
									$sumSick = $sumSick+0.5; 
									$sumKad= $sumKad-0.5; 
								}elseif($ChkAbsentType=='03'){
									$arr_dataF .='/ก ข';
									$sumLaKid = $sumLaKid+0.5; 
									$sumKad= $sumKad-0.5; 
								}elseif($ChkAbsentType=='04'){
									$arr_dataF .='/พ ข';
									$sumPak = $sumPak+0.5; 
									$sumKad= $sumKad-0.5; 
								}
							}
							
							if($ChkTime[1]!='000'){ // ข้อมูลบ่าย
								$ChkAbsentType=substr($ChkTime[1],1,2); // ประเภทการลา
								if($ChkAbsentType=='01'){
									$arr_dataF .='ข ป/';
									$sumSick = $sumSick+0.5; 
									$sumKad= $sumKad-0.5; 
								}elseif($ChkAbsentType=='03'){
									$arr_dataF .='ข ก/';
									$sumLaKid = $sumLaKid+0.5; 
									$sumKad= $sumKad-0.5; 
								}elseif($ChkAbsentType=='04'){
									$arr_dataF .='ข พ/';
									$sumPak = $sumPak+0.5; 
									$sumKad= $sumKad-0.5; 
								}
							}
							
							
						}else{
							$arr_dataF .='ข';
						}
						
					}else if($ArrData[1]=='1'){  // กรณีสาย
						$sumSai++;
						
						$ChkTime = explode('|',$ArrData[2]);
						if(substr($ChkTime[0],0,1)!='3'){ //มีการลา
						
							if($ChkTime[0]!='000'){ // ข้อมูลเช้า
								$ChkAbsentType=substr($ChkTime[0],1,2); // ประเภทการลา
								if($ChkAbsentType=='01'){
									$arr_dataF .='/ป ส';
									$sumSick = $sumSick+0.5; 
								}elseif($ChkAbsentType=='03'){
									$arr_dataF .='/ก ส';
									$sumLaKid = $sumLaKid+0.5; 
								}elseif($ChkAbsentType=='04'){
									$arr_dataF .='/พ ส';
									$sumPak = $sumPak+0.5; 
								}
							}
							
							if($ChkTime[1]!='000'){ // ข้อมูลบ่าย
								$ChkAbsentType=substr($ChkTime[1],1,2); // ประเภทการลา
								if($ChkAbsentType=='01'){
									$arr_dataF .='ส ป/';
									$sumSick = $sumSick+0.5; 
								}elseif($ChkAbsentType=='03'){
									$arr_dataF .='ส ก/';
									$sumLaKid = $sumLaKid+0.5; 
								}elseif($ChkAbsentType=='04'){
									$arr_dataF .='ส พ/';
									$sumPak = $sumPak+0.5; 
								}
							}
							
							
						}else{
							$arr_dataF .='ส';
						}
					}else if($ArrData[1]=='0'){  // กรณีลาและมาทำงานปกติ
						
						$ChkTime = explode('|',$ArrData[2]);
						if(substr($ChkTime[0],0,1)!='3'){ //มีการลาเช้า-บ่าย
						
							if($ChkTime[0]!='000'){ // ข้อมูลเช้า
								$ChkAbsentType=substr($ChkTime[0],1,2); // ประเภทการลา
								if($ChkAbsentType=='01'){
									$arr_dataF .='/ป';
									$sumSick = $sumSick+0.5; 
								}elseif($ChkAbsentType=='03'){
									$arr_dataF .='/ก';
									$sumLaKid = $sumLaKid+0.5; 
								}elseif($ChkAbsentType=='04'){
									$arr_dataF .='/พ';
									$sumPak = $sumPak+0.5; 
								}
							}
							
							if($ChkTime[1]!='000'){ // ข้อมูลบ่าย
								$ChkAbsentType=substr($ChkTime[1],1,2); // ประเภทการลา
								if($ChkAbsentType=='01'){
									if($ChkTime[0]!='000'){ $arr_dataF .=' ';}
									$arr_dataF .='ป/';
									$sumSick = $sumSick+0.5; 
								}elseif($ChkAbsentType=='03'){
									if($ChkTime[0]!='000'){ $arr_dataF .=' ';}
									$arr_dataF .='ก/';
									$sumLaKid = $sumLaKid+0.5; 
								}elseif($ChkAbsentType=='04'){
									if($ChkTime[0]!='000'){ $arr_dataF .=' ';}
									$arr_dataF .='พ/';
									$sumPak = $sumPak+0.5; 
								}
							}
							
							
						}else{  // ลาทั้งวัน
						
							if($ChkTime[0]!='000'){ // ข้อมูลเช้า
								$ChkAbsentType=substr($ChkTime[0],1,2); // ประเภทการลา
								if($ChkAbsentType=='01'){
									$arr_dataF .='ป';
									$sumSick++; 
								}elseif($ChkAbsentType=='03'){
									$arr_dataF .='ก';
									$sumLaKid++; 
								}elseif($ChkAbsentType=='04'){
									$arr_dataF .='พ';
									$sumPak++; 
								}
							}
						}
						
					}else if($ArrData[1]=='*'){  // กรณีไม่ลงเวลา หรือบุคคลไม่ต้องลงเวลา
						
						$ChkTime = explode('|',$ArrData[2]);
						if(substr($ChkTime[0],0,1)!='3'){ //มีการลาเช้า-บ่าย
						
							if($ChkTime[0]!='000'){ // ข้อมูลเช้า
								$ChkAbsentType=substr($ChkTime[0],1,2); // ประเภทการลา
								if($ChkAbsentType=='01'){
									$arr_dataF .='/ป';
									$sumSick = $sumSick+0.5; 
								}elseif($ChkAbsentType=='03'){
									$arr_dataF .='/ก';
									$sumLaKid = $sumLaKid+0.5; 
								}elseif($ChkAbsentType=='04'){
									$arr_dataF .='/พ';
									$sumPak = $sumPak+0.5; 
								}
								
							}
							
							if($ChkTime[1]!='000'){ // ข้อมูลบ่าย
								$ChkAbsentType=substr($ChkTime[1],1,2); // ประเภทการลา
								if($ChkAbsentType=='01'){
									if($ChkTime[0]!='000'){ $arr_dataF .=' ';}
									$arr_dataF .='ป/';
									$sumSick = $sumSick+0.5; 
								}elseif($ChkAbsentType=='03'){
									if($ChkTime[0]!='000'){ $arr_dataF .=' ';}
									$arr_dataF .='ก/';
									$sumLaKid = $sumLaKid+0.5; 
								}elseif($ChkAbsentType=='04'){
									if($ChkTime[0]!='000'){ $arr_dataF .=' ';}
									$arr_dataF .='พ/';
									$sumPak = $sumPak+0.5; 
								}
							}
							
							
						}else{  // ลาทั้งวัน
						
							if($ChkTime[0]!='000'){ // ข้อมูลเช้า
								$ChkAbsentType=substr($ChkTime[0],1,2); // ประเภทการลา
								if($ChkAbsentType=='01'){
									$arr_dataF .='ป';
									$sumSick++; 
								}elseif($ChkAbsentType=='03'){
									$arr_dataF .='ก';
									$sumLaKid++; 
								}elseif($ChkAbsentType=='04'){
									$arr_dataF .='พ';
									$sumPak++; 
								}elseif($ChkAbsentType=='10'){
									$arr_dataF .='ส';
									$sumSai++; 
								}elseif($ChkAbsentType=='13'){
									$arr_dataF .='ข';
									$sumKad++; 
								}
							}
						}
						
					}else{
						$arr_dataF .='';
					}
					
                    
					
					$arr_data[] =$arr_dataF;
                    
                //}
             }
			 
			 
            $TotalsumSick+=$sumSick;
            $TotalsumLaKid+=$sumLaKid;
            $TotalsumPak+=$sumPak;
            $TotalsumSai+=$sumSai;
            $TotalsumKad+=$sumKad;
            
            $TotalsumSick6+=$sumSick;
            $TotalsumLaKid6+=$sumLaKid;
            $TotalsumPak6+=$sumPak;
            $TotalsumSai6+=$sumSai;
            $TotalsumKad6+=$sumKad;
            
            $arr_data[] = ($sumSick==0)?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sumSick,2). ' '):round($sumSick,2). ' ');
            $arr_data[] = ($sumLaKid==0)?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sumLaKid,2). ' '):round($sumLaKid,2). ' ');
            $arr_data[] = ($sumPak==0)?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sumPak,2). ' '):round($sumPak,2). ' ');
            $arr_data[] = ($sumSai==0)?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sumSai,2). ' '):round($sumSai,2). ' ');
            $arr_data[] = ($sumKad==0)?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sumKad,2). ' '):round($sumKad,2). ' ');
            // var_dump($data);die();
			
			$pdf->SetFillColor(218,218,218);
            for ($i=1; $i < sizeof($RectHoliday); $i++) {
              $x = $CurRowX + 25 + ($RectHoliday[$i] * 6);
              $pdf->Rect($x,$CurRowY,6,7,'FD');   
            }
			
            $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", $CH_PRINT_SIZE-4, "", "000000", "");
            
			

            
            
            /*แทรก ครั้งปีแรก*/
            if($loop==5 ){//6
                $arr_dataRowSum = (array) null;
                for($day=1;$day<=32;$day++){
                    $arr_dataRowSum[] = (($NUMBER_DISPLAY==2)?convert2thaidigit('<**1**>ยอดรวมตั้งแต่เดือนตุลาคม-เดือนมีนาคม(1)'):'<**1**>ยอดรวมตั้งแต่เดือนตุลาคม-เดือนมีนาคม(1)');
                }
                $arr_dataRowSum[] = ($TotalsumSick6==0)?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($TotalsumSick6,2). ' '):round($TotalsumSick6,2). ' ');
                $arr_dataRowSum[] = ($TotalsumLaKid6==0)?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($TotalsumLaKid6,2). ' '):round($TotalsumLaKid6,2). ' ');
                $arr_dataRowSum[] = ($TotalsumPak6==0)?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($TotalsumPak6,2). ' '):round($TotalsumPak6,2). ' ');
                $arr_dataRowSum[] = ($TotalsumSai6==0)?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($TotalsumSai6,2). ' '):round($TotalsumSai6,2). ' ');
                $arr_dataRowSum[] = ($TotalsumKad6==0)?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($TotalsumKad6,2). ' '):round($TotalsumKad6,2). ' ');
                $arr_dataRowSum[] = '';
                
                
               

                $pdf->add_data_tab($arr_dataRowSum, 7, "TRHBL", $data_alignSum, "", $CH_PRINT_SIZE-4, "", "000000", "");
                $TotalsumSick6=0;
                $TotalsumLaKid6=0;
                $TotalsumPak6=0;
                $TotalsumSai6=0;
                $TotalsumKad6=0;
            }
            $loop++;
        } while ($loop<12 && ($data = $db_dpis2->get_array_array()));
        
        $arr_dataRowSum2 = (array) null;
        for($day=1;$day<=32;$day++){
            $arr_dataRowSum2[] = (($NUMBER_DISPLAY==2)?convert2thaidigit('<**2**>ยอดรวมตั้งแต่เดือนเมษายน-เดือนกันยายน(2)'):'<**2**>ยอดรวมตั้งแต่เดือนเมษายน-เดือนกันยายน(2)');
        }
        $arr_dataRowSum2[] = ($TotalsumSick6==0)?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($TotalsumSick6,2). ' '):round($TotalsumSick6,2). ' ');
        $arr_dataRowSum2[] = ($TotalsumLaKid6==0)?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($TotalsumLaKid6,2). ' '):round($TotalsumLaKid6,2). ' ');
        $arr_dataRowSum2[] = ($TotalsumPak6==0)?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($TotalsumPak6,2). ' '):round($TotalsumPak6,2). ' ');
        $arr_dataRowSum2[] = ($TotalsumSai6==0)?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($TotalsumSai6,2). ' '):round($TotalsumSai6,2). ' ');
        $arr_dataRowSum2[] = ($TotalsumKad6==0)?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($TotalsumKad6,2). ' '):round($TotalsumKad6,2). ' ');
        
        $result = $pdf->add_data_tab($arr_dataRowSum2, 7, "TRHBL", $data_alignSum2, "", $CH_PRINT_SIZE-4, "b", "000000", "");
        
        $arr_dataRowSum3 = (array) null;
        for($day=1;$day<=32;$day++){
            $arr_dataRowSum3[] = (($NUMBER_DISPLAY==2)?convert2thaidigit('<**3**>ยอดรวมทั้งปี(1)+(2)'):'<**3**>ยอดรวมทั้งปี(1)+(2)');
        }
        $arr_dataRowSum3[] = ($TotalsumSick==0)?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($TotalsumSick,2). ' '):round($TotalsumSick,2). ' ');
        $arr_dataRowSum3[] = ($TotalsumLaKid==0)?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($TotalsumLaKid,2). ' '):round($TotalsumLaKid,2). ' ');
        $arr_dataRowSum3[] = ($TotalsumPak==0)?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($TotalsumPak,2). ' '):round($TotalsumPak,2). ' ');
        $arr_dataRowSum3[] = ($TotalsumSai==0)?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($TotalsumSai,2). ' '):round($TotalsumSai,2). ' ');
        $arr_dataRowSum3[] = ($TotalsumKad==0)?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($TotalsumKad,2). ' '):round($TotalsumKad,2). ' ');
        $arr_dataRowSum3[] = '';
        $data_align = array("R","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","L");
        $pdf->add_data_tab($arr_dataRowSum3, 7, "TRHBL", $data_align, "", $CH_PRINT_SIZE-4, "b", "000000", "");
        
        $pdf->close_tab("");
        
        $pdf->SetFillColor(218,218,218);
        $pdf->Rect(95+$Px_DISLEFT,160+$Px_DISHEAD,6,7,'FD');
        $pdf->Text($Px_DISLEFT,165+$Px_DISHEAD,(($NUMBER_DISPLAY==2)?convert2thaidigit("**ลาป่วยและลากิจ รวมกันไม่เกิน 45 วันต่อปี (ครึ่งปีไม่เกิน 23 วัน)"):"**ลาป่วยและลากิจ รวมกันไม่เกิน 45 วันต่อปี (ครึ่งปีไม่เกิน 23 วัน)"));
        $pdf->Text(103+$Px_DISLEFT,165+$Px_DISHEAD,"= วันหยุดราชการ");
        $pdf->Text(128+$Px_DISLEFT,165+$Px_DISHEAD,"ป = ลาป่วย");
        $pdf->Text(148+$Px_DISLEFT,165+$Px_DISHEAD,"ก = ลากิจ");
        $pdf->Text(168+$Px_DISLEFT,165+$Px_DISHEAD,"พ = ลาพักผ่อน");
		
		$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_NUMLATETIMES' ";
		$db_dpis->send_cmd_fast($cmd);
		$data_s = $db_dpis->get_array_array();
		$TMP_P_NUMLATETIMES = $data_s[CONFIG_VALUE];
		
		$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_NUMLATEPER' ";
		$db_dpis->send_cmd_fast($cmd);
		$data_s = $db_dpis->get_array_array();
		if($data_s[CONFIG_VALUE]==1){
			$TMP_P_NUMLATEPER = "ปีงบประมาณ";
		}else{
			$TMP_P_NUMLATEPER = "รอบการประเมิน";
		}
		
        $pdf->Text($Px_DISLEFT,170+$Px_DISHEAD,(($NUMBER_DISPLAY==2)?convert2thaidigit("  มาสายไม่เกิน ".$TMP_P_NUMLATETIMES." ครั้ง/".$TMP_P_NUMLATEPER):"  มาสายไม่เกิน ".$TMP_P_NUMLATETIMES." ครั้ง/".$TMP_P_NUMLATEPER));
        $pdf->Text(128+$Px_DISLEFT,170+$Px_DISHEAD,"ส = สาย");
        $pdf->Text(148+$Px_DISLEFT,170+$Px_DISHEAD,"ข = ขาด");
    
        $pdf->Rect(221,3+$Px_DISHEAD,70,35);
        $pdf->Text(243,10+$Px_DISHEAD,"วันลาพักผ่อนสะสม");
        $pdf->Text(228,15+$Px_DISHEAD,"ยอดยกมา");$pdf->Text(228+25,15+$Px_DISHEAD,(($NUMBER_DISPLAY==2)?convert2thaidigit($TotalCODE_04. ' '):$TotalCODE_04. ' '));$pdf->Text(228+50,15+$Px_DISHEAD,"วัน");
        $pdf->Text(228,20+$Px_DISHEAD,"ปีนี้");$pdf->Text(228+25,20+$Px_DISHEAD,(($NUMBER_DISPLAY==2)?convert2thaidigit($AB_COUNT_TOTAL_04NOW. ' '):$AB_COUNT_TOTAL_04NOW. ' '));$pdf->Text(228+50,20+$Px_DISHEAD,"วัน");
        $pdf->Text(228,25+$Px_DISHEAD,"รวมเป็น");$pdf->Text(228+25,25+$Px_DISHEAD,(($NUMBER_DISPLAY==2)?convert2thaidigit($SumAB_COUNT. ' '):$SumAB_COUNT. ' '));$pdf->Text(228+50,25+$Px_DISHEAD,"วัน");
        $pdf->Text(228,30+$Px_DISHEAD,(($NUMBER_DISPLAY==2)?convert2thaidigit("รับราชการเกิน 10 ปี ยอดยกมาได้ 20 วัน"):"รับราชการเกิน 10 ปี ยอดยกมาได้ 20 วัน"));
        $pdf->Text(228,35+$Px_DISHEAD,(($NUMBER_DISPLAY==2)?convert2thaidigit("รับราชการไม่ถึง 10 ปี ยอดยกมาได้ 10 วัน"):"รับราชการไม่ถึง 10 ปี ยอดยกมาได้ 10 วัน"));
    }
    
    $pdf->close();
    $fname = "R1210.pdf";
    $pdf->Output($fname,'D');

?>