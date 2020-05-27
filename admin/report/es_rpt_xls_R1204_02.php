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
    function getNameAbsentType($db_dpis,$AbCode){
        $cmd = "SELECT AB_NAME FROM PER_ABSENTTYPE WHERE TRIM(AB_CODE)='$AbCode'";
        $db_dpis->send_cmd_fast($cmd);
        $data2 = $db_dpis->get_array_array();
        $AB_NAME = $data2[AB_NAME];
        return $AB_NAME;
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
            $cmd = " 	select pl.PL_NAME
                        from	PER_POSITION pp, PER_LINE pl, PER_ORG po
                        where	pp.POS_ID=$dbPOS_ID and pp.ORG_ID=po.ORG_ID and pp.PL_CODE=pl.PL_CODE ";
            $db_dpis->send_cmd_fast($cmd);
            $dataPOS = $db_dpis->get_array_array();
            $TMP_PL_NAME = TRIM($dataPOS[PL_NAME]);
        }elseif($perType==2){ //POEM_ID
            $cmd = " 	select pl.PN_NAME 
                        from PER_POS_EMP pp, PER_POS_NAME pl, PER_ORG po 
                        where pp.POEM_ID=$dbPOEM_ID and pp.ORG_ID=po.ORG_ID and pp.PN_CODE=pl.PN_CODE  ";
            $db_dpis->send_cmd_fast($cmd);
            $dataPOS = $db_dpis->get_array_array();
            $TMP_PL_NAME = trim($dataPOS[PN_NAME]);
        }elseif($perType==3){//POEMS_ID
            $cmd = " 	select pl.EP_NAME 
                        from PER_POS_EMPSER pp, PER_EMPSER_POS_NAME pl, PER_ORG po, PER_LEVEL le
                        where pp.POEMS_ID=$dbPOEMS_ID and pp.ORG_ID=po.ORG_ID and pp.EP_CODE=pl.EP_CODE  and pl.LEVEL_NO = le.LEVEL_NO";
            $db_dpis->send_cmd_fast($cmd);
            $dataPOS = $db_dpis->get_array_array();
            $TMP_PL_NAME = trim($dataPOS[EP_NAME]);
        }elseif($perType==4){//POT_ID
            $cmd = " 	select pl.TP_NAME 
                        from	PER_POS_TEMP pp, PER_TEMP_POS_NAME pl, PER_ORG po 
                        where	pp.POT_ID=$dbPOT_ID and pp.ORG_ID=po.ORG_ID and pp.TP_CODE=pl.TP_CODE  ";
            $db_dpis->send_cmd_fast($cmd);
            $dataPOS = $db_dpis->get_array_array();
            $TMP_PL_NAME = trim($dataPOS[TP_NAME]);
        }
        $TMP_LEVEL_NAME='';
        $cmd = " select POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$dbLevelNo' ";
        $db_dpis->send_cmd_fast($cmd);
        $dataLvl = $db_dpis->get_array_array();
        $TMP_LEVEL_NAME = $dataLvl[POSITION_LEVEL];
        return $TMP_PL_NAME.$TMP_LEVEL_NAME;
    }
    
    
    define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
    //date_default_timezone_set('Asia/Bangkok');
    require_once '../../Excel/eslip/Classes/PHPExcel/IOFactory.php';
    
    $objReader = PHPExcel_IOFactory::createReader('Excel5');
    $objPHPExcel = $objReader->load("../../Excel/eslip/templates/R1208_template.xls");
	
	include ("es_font_size.php");
	$objPHPExcel->getActiveSheet()->getStyle('A1:P5')->getFont()->setName(getToFont($CH_PRINT_FONT));
    $objPHPExcel->getActiveSheet()->getStyle('A1:P5')->getFont()->setSize($CH_PRINT_SIZE);
    
    $search_condition_n="";
    //หาหน่วยงาน
    if($search_org_id || $search_org_ass_id){
        if($select_org_structure==0 || $PER_AUDIT_FLAG==2){ // แบบตามกฏหมาย
            $varORGID= " AND PP.ORG_ID=".$search_org_id;
        }else{ //แบบมอบหมายงาน
            $varORGID= " AND P.ORG_ID=".$search_org_ass_id;
        }	
    }else{
        $varORGID=  " AND P.DEPARTMENT_ID =".$DEPARTMENT_ID;
    }

    // จัดเรียงข้อมูล
    if(!trim($RPTORD_LIST)){ 
            $RPTORD_LIST = "MINISTRY|";
    }
    $arr_rpt_order = explode("|", $RPTORD_LIST);
    $order_by = "";
    for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
            $REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
            switch($REPORT_ORDER){
                    case "NAME" :
                            $order_by .= " ,P.PER_NAME, P.PER_SURNAME";
                            break;


            } // end switch case
    } // end for
	
    $PER_TYPE=$search_per_type;
    if($search_per_type){
        $CON_PER_TYPE = " AND P.PER_TYPE=$search_per_type";
    }else{
        $CON_PER_TYPE = "";
    }
    /* หาสถานะ */
    $con_per_status = "";
    if($search_per_type){
        $con_per_status = "P.PER_STATUS in (". implode(", ", $search_per_status) .")";
    }
    if($PER_AUDIT_FLAG==1){
		$LAW_OR_ASS = 2;
	}else if($PER_AUDIT_FLAG==2){
		$LAW_OR_ASS = 1;
	}else{
		$LAW_OR_ASS = $select_org_structure+1;
	}
	
	$POS_NO_VAL1="";
	$POS_NO_VAL2="";
	$POS_NO_VAL3="";
	$POS_NO_VAL4="";
	$PER_NAME_VAL = "";
	$PER_SURNAME_VAL = "";
	$LEVEL_NO_VAL ="";
    if($list_person_type=="SELECT"){
		 if(!$SELECTED_PER_ID){$SELECTED_PER_ID=0;}
        $PERID_VAL = "  AND PER_ID IN($SELECTED_PER_ID) ";
    }elseif($list_person_type == "CONDITION"){
		
		if(trim($search_pos_no)){ 
			if($search_per_type==1){ 
				$POS_NO_VAL1 = " AND  (PP.POS_NO like '$search_pos_no%')";
			}elseif($search_per_type==2){ 
				$POS_NO_VAL2 = " AND (PP.POEM_NO like '$search_pos_no%')";
			}elseif($search_per_type==3){ 
				$POS_NO_VAL3 = " AND (PP.POEMS_NO like '$search_pos_no%')";
			}elseif($search_per_type==4){ 
				$POS_NO_VAL4 = " AND (PP.POT_NO like '$search_pos_no%')";	
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
		
        $PERID_VAL = " AND PER_ID NOT IN(0) ";
    }elseif($list_person_type == "ALL"){
		$PERID_VAL = " AND PER_ID NOT IN(0) ";
    }else{ $PERID_VAL = " AND PER_ID  IN($SESS_PER_ID) ";}
	
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
		

	
    $cmd = "WITH
					PerID_List as
					(
					  select P.per_id 
					  
					  FROM PER_PERSONAL P
					  $POS_NO_FROM
					  $conTPER_ORG
					  WHERE $con_per_status
                                                $CON_PER_AUDIT_FLAG 
                                                $PERID_VAL
                                                $LEVEL_NO_VAL 
                                                $PER_SURNAME_VAL 
                                                $PER_NAME_VAL 
                                                $POS_NO_VAL
                                                $varORGID
                                                $CON_PER_TYPE
                                                $search_condition_n
					),
					AllWorkDay As
					(
					  select * from ( -- LISTDATE, HOL 
						select x.*,(case when TO_CHAR(to_date(x.LISTDATE,'YYYY-MM-DD'), 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') IN ('SAT', 'SUN') then 1 else
						  case when exists (select null from PER_HOLIDAY where SUBSTR(HOL_DATE,1,10) = x.LISTDATE) then 1 else 0 end end) HOL
						from (
						  select TO_CHAR(LISTDATE,'YYYY-MM-DD') LISTDATE from (
							SELECT (TO_DATE((
								select trim(min(ABS_STARTDATE))
									from PER_ABSENTHIS where per_id in (select per_id from PerID_List)
							  ), 'YYYY-MM-DD HH24:MI:SS'))-1+rownum AS LISTDATE FROM all_objects
							 WHERE (TO_DATE((
								select trim(min(ABS_STARTDATE))
									from PER_ABSENTHIS where per_id in (select per_id from PerID_List ) 
							  ), 'YYYY-MM-DD HH24:MI:SS'))-1+ rownum <= TO_DATE((
								select trim(max(ABS_ENDDATE))
									from PER_ABSENTHIS where per_id in (select per_id from PerID_List)
							  ), 'YYYY-MM-DD HH24:MI:SS')
						  )
						) x 
					  )
					)
					select a.*,v.vc_day pakpon_sasom,ORG.ORG_NAME
					from (
					  select s.per_id,s.as_year,s.as_cycle,s.start_date,s.end_date,
						sum(case when AB_CODE='01' then ABS_DAY else 0 end) as SICK_ABS,
						sum(case when AB_CODE='03' then ABS_DAY else 0 end) as KIT_ABS,
						sum(case when AB_CODE='04' then ABS_DAY else 0 end) as PAKPON_ABS,
						sum(case when AB_CODE='10' then ABS_DAY else 0 end) as SAY_ABS,
						sum(case when AB_CODE='13' then ABS_DAY else 0 end) as KHAD_ABS,
						NVL(sum( case when AB_CODE not in ('01        ','03        ','04        ', '07        ','10        ','13        ') then ABS_DAY end),0)  LAOTH
					  from per_absentsum s
					  left join 
					  (
						select h.per_id,LISTDATE,h.AB_CODE
							   ,case when LISTDATE=trim(ABS_STARTDATE) then 
								  case when ABS_STARTPERIOD=3 then 1 else 0.5 end
								else case when LISTDATE=trim(ABS_ENDDATE) then 
										case when ABS_ENDPERIOD=3 then 1 else 0.5 end 
									 else 1 end
							   end abs_day
						from per_absenthis h
						left join PER_ABSENTTYPE t on (t.AB_CODE=h.AB_CODE)
						left join AllWorkDay on (LISTDATE between trim(h.ABS_STARTDATE) and trim(h.ABS_ENDDATE))
						where per_id in (select per_id from PerID_List)
						and ((t.AB_COUNT=2 and HOL=0) or t.AB_COUNT=1)
					  ) l on (l.per_id=s.per_id and (LISTDATE between s.START_DATE and s.END_DATE))
					  where s.per_id in (select per_id from PerID_List)
					  group by s.per_id,as_year,s.as_cycle,s.start_date,s.end_date
					) a
					left join per_vacation v on (v.per_id=a.per_id and v.vc_year=a.as_year)
					left join PER_PERSONAL P ON(P.PER_ID=a.per_id)
					$POS_NO_FROM
					$conTPER_ORG
					where a.as_year BETWEEN $search_yearBgn AND $search_yearEnd 
					order by a.per_id ASC,a.as_year ASC,a.as_cycle ASC ";
    //echo '<pre>'.$cmd; die();
    $db_dpis2->send_cmd_fast($cmd);
    $CurPerId=-1;
    $no=1;
    $CHKno=0;
    $CHKAS_YEAR="";
    $baseRow = 5;
    $r=0;
	$CHKORG_NAME ="";
	$SUMPAKPON_ABS= 0;
    while ($data = $db_dpis2->get_array_array()) {
        
        $row = $baseRow + $r;
		
        if($data[PER_ID]!=$CurPerId){
                $fullname_And_Pos = getNamePersonal($db_dpis,trim($data[PER_ID]));//."ตำแหน่ง ".getPositionName($db_dpis,$data[PER_ID]);
                $RowNum = $no++;
        }else{
                $fullname_And_Pos = "";
                $RowNum = '';
        }
        if($data[AS_YEAR].$data[PER_ID]!=$CHKAS_YEAR){
                $AS_YEAR=$data[AS_YEAR];
        }else{
                $AS_YEAR = "";
        }
        
        $ORG_NAME_SHOW = "";
        if($data[ORG_NAME] !=$CHKORG_NAME ){
            $ORG_NAME_SHOW = $data[ORG_NAME].' '.$DEPARTMENT_NAME;
        }
		
        if($data[AS_CYCLE]==1){
            $DiffDay=$data[PAKPON_SASOM]-$data[PAKPON_ABS]+0;
            $SUMPAKPON_ABS = $data[PAKPON_ABS];
        }else{
            $DiffDay=$data[PAKPON_SASOM]-($data[PAKPON_ABS]+$SUMPAKPON_ABS)+0;
            $SUMPAKPON_ABS=0;
        }
		
        if($data[AS_CYCLE]==1){$xstart ="01/10/";$xend ="31/03/";}else{$xstart ="01/04/";$xend ="30/09/";}
		 
        $START_DATE = $xstart.(substr($data[START_DATE],0,4)+543)." - ".$xend.(substr($data[END_DATE],0,4)+543);
        $objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit($RowNum):$RowNum)))
                                    ->setCellValue('B'.$row, iconv('TIS-620','UTF-8',$ORG_NAME_SHOW))
                                    ->setCellValue('C'.$row, iconv('TIS-620','UTF-8',$fullname_And_Pos))
                                    ->setCellValue('D'.$row, iconv('TIS-620','UTF-8',getPositionName($db_dpis,$data[PER_ID])))
                                    ->setCellValue('E'.$row, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit($AS_YEAR):$AS_YEAR) ))
                                    ->setCellValue('F'.$row, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit($data[AS_CYCLE]):$data[AS_CYCLE])  ))
                                    ->setCellValue('G'.$row, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit($START_DATE):$START_DATE)  ))
                                    ->setCellValue('H'.$row, iconv('TIS-620','UTF-8',$data[PAKPON_SASOM]==0?'': (($NUMBER_DISPLAY==2)?convert2thaidigit(round($data[PAKPON_SASOM],2)):round($data[PAKPON_SASOM],2))  ))
                                    ->setCellValue('I'.$row, iconv('TIS-620','UTF-8',$data[PAKPON_ABS]==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($data[PAKPON_ABS],2)):round($data[PAKPON_ABS],2))   ))
                                    ->setCellValue('J'.$row, iconv('TIS-620','UTF-8',$DiffDay==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($DiffDay,2)):round($DiffDay,2)) ))
                                    ->setCellValue('K'.$row, iconv('TIS-620','UTF-8',$data[SICK_ABS]==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($data[SICK_ABS],2)):round($data[SICK_ABS],2))  ))
                                    ->setCellValue('L'.$row, iconv('TIS-620','UTF-8',$data[KIT_ABS]==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($data[KIT_ABS],2)):round($data[KIT_ABS],2))  ))
                                    ->setCellValue('M'.$row, iconv('TIS-620','UTF-8',$data[LAOTH]==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($data[LAOTH],2)):round($data[LAOTH],2))  ))
                                    ->setCellValue('N'.$row, iconv('TIS-620','UTF-8',$data[SAY_ABS]==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($data[SAY_ABS],2)):round($data[SAY_ABS],2))  ))
                                    ->setCellValue('O'.$row, iconv('TIS-620','UTF-8',$data[KHAD_ABS]==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($data[KHAD_ABS],2)):round($data[KHAD_ABS],2)) ))
                                    ->setCellValue('P'.$row, iconv('TIS-620','UTF-8',""));
        
        $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':P'.$row)->getFill()->getStartColor()->setRGB('FFFFFF');
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);    
		$objPHPExcel->getActiveSheet()->getStyle('C'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);  
		$objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);  
		$objPHPExcel->getActiveSheet()->getStyle('P'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);  

        $CHKAS_YEAR=$data[AS_YEAR].$data[PER_ID];
        $CurPerId=$data[PER_ID];
		$CHKORG_NAME=$data[ORG_NAME];
        $r++;
    }
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="R1208.xls"');
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
