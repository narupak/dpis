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
        $db_dpis->send_cmd($cmd);
        $data2 = $db_dpis->get_array_array();
        $fullname = $data2[PN_NAME].$data2[PER_NAME].' '.$data2[PER_SURNAME];
        return $fullname;
    }
    function getNameAbsentType($db_dpis,$AbCode){
        $cmd = "SELECT AB_NAME FROM PER_ABSENTTYPE WHERE TRIM(AB_CODE)='$AbCode'";
        $db_dpis->send_cmd($cmd);
        $data2 = $db_dpis->get_array_array();
        $AB_NAME = $data2[AB_NAME];
        return $AB_NAME;
    }
	
    function getSALARYHIS($db_dpis,$PER_ID,$SAH_EFFECTIVEDATE){
        $cmd = "SELECT pm.MOV_NAME
						 FROM		PER_SALARYHIS psh, PER_MOVMENT pm 
						 WHERE		psh.PER_ID=$PER_ID 
						 AND substr(psh.SAH_EFFECTIVEDATE,1,10)='$SAH_EFFECTIVEDATE'
						 AND psh.SAH_PERCENT_UP IS NOT NULL
						 and psh.MOV_CODE=pm.MOV_CODE ";
        $db_dpis->send_cmd($cmd);
        $data2 = $db_dpis->get_array_array();
        $MOV_NAME = str_replace("เลื่อนเงินเดือนระดับ","",$data2[MOV_NAME]);
        return $MOV_NAME;
    }
    
    
    define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
    //date_default_timezone_set('Asia/Bangkok');
    require_once '../../Excel/eslip/Classes/PHPExcel/IOFactory.php';
    
    $objReader = PHPExcel_IOFactory::createReader('Excel5');
    $objPHPExcel = $objReader->load("../../Excel/eslip/templates/R1209_template.xls");
    
	include ("es_font_size.php");
	$objPHPExcel->getActiveSheet()->getStyle('A1:I6')->getFont()->setName(getToFont($CH_PRINT_FONT));
    $objPHPExcel->getActiveSheet()->getStyle('A1:I6')->getFont()->setSize($CH_PRINT_SIZE);
	
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', iconv('TIS-620','UTF-8','รายงานประวัติการมาปฏิบัติงานของบุคลากร')) 
            ->setCellValue('A2', iconv('TIS-620','UTF-8','ตั้งแต่ปีงบประมาณ '.(($NUMBER_DISPLAY==2)?convert2thaidigit($search_yearBgn):$search_yearBgn).' ถึงปีงบประมาณ '.(($NUMBER_DISPLAY==2)?convert2thaidigit($search_yearEnd):$search_yearEnd)));

    $search_condition_n="";
    if($search_org_id || $search_org_ass_id){
		 if($select_org_structure==0 || $PER_AUDIT_FLAG==2){ // แบบตามกฏหมาย
                        $ORG_ID= $search_org_id;
                        if($search_org_id_1){
                            $varORGNAME_1= ' - '.$search_org_name_1;
                        }
		 }else{ //แบบมอบหมายงาน
                        $ORG_ID= $search_org_ass_id;
                        if($search_org_ass_id_1){
                           $varORGNAME_1= ' - '.$search_org_ass_name_1;
                        }
		 }	
	}else{
		$ORG_ID = $DEPARTMENT_ID;
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
	
	$POS_NO_VAL1="";
	$POS_NO_FROM="";
	$PER_NAME_VAL = "";
	$PER_SURNAME_VAL = "";
	$LEVEL_NO_VAL ="";
    if($list_person_type=="SELECT"){
		 if(!$SELECTED_PER_ID){$SELECTED_PER_ID=0;}
        $PERID_VAL = "  select per_id from PERSONINDEPART where per_id in($SELECTED_PER_ID) ";
    }elseif($list_person_type == "CONDITION"){
		
		if(trim($search_pos_no)){ 
			if($search_per_type==1){ 
				$POS_NO_VAL1 = " AND  (PP.POS_NO = '$search_pos_no')";
				//$POS_NO_FROM=" LEFT JOIN PER_POSITION PP ON (PP.POS_ID=P.POS_ID)";
			}elseif($search_per_type==2){ 
				$POS_NO_VAL1 = " AND (PP.POEM_NO = '$search_pos_no')";
				//$POS_NO_FROM=" LEFT JOIN PER_POS_EMP PP ON (PP.POEM_ID=P.POEM_ID)";
			}elseif($search_per_type==3){ 
				$POS_NO_VAL1 = " AND (PP.POEMS_NO = '$search_pos_no')";
				//$POS_NO_FROM=" LEFT JOIN PER_POS_EMPSER PP ON (PP.POEMS_ID=P.POEMS_ID)";
			}elseif($search_per_type==4){ 
				$POS_NO_VAL1 = " AND (PP.POT_NO = '$search_pos_no')";	
				//$POS_NO_FROM="  LEFT JOIN PER_POS_TEMP PP ON (PP.POT_ID=P.POT_ID)";
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
		
        $PERID_VAL = " select per_id from PERSONINDEPART ";
	}elseif($list_person_type == "ALL"){
		$PERID_VAL = " select per_id from PERSONINDEPART ";
    }else{$PERID_VAL = "  select per_id from PERSONINDEPART where per_id in($SESS_PER_ID) ";}

    $cmd = file_get_contents('../es_rpt_R1204_03.sql');	
    $cmd=str_ireplace(":LAW_OR_ASS",$LAW_OR_ASS,$cmd);
    $cmd=str_ireplace(":ORG_ID",$ORG_ID,$cmd);
    $cmd=str_ireplace(":PER_TYPE",$PER_TYPE,$cmd);
    $cmd=str_ireplace(":AS_YEAR1",$search_yearBgn,$cmd);
    $cmd=str_ireplace(":AS_YEAR2",$search_yearEnd,$cmd);
    $cmd=str_ireplace(":PERID_VAL",$PERID_VAL,$cmd);
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
		
	
	
	$cmd=" select x.* from (".$cmd.") x 
		left join PER_PERSONAL P ON(P.PER_ID=x.PER_ID)
		$POS_NO_FROM
		$conTPER_ORG
		WHERE 1=1 
		$CON_PER_AUDIT_FLAG
		$PER_NAME_VAL
		$PER_SURNAME_VAL 
		$LEVEL_NO_VAL
		$POS_NO_VAL1
                $search_condition_n
		order by ORG.ORG_NAME ASC ,P.PER_NAME ASC , P.PER_SURNAME ASC ,x.AS_YEAR DESC 
	";
    //die('<pre>'.$cmd);
    $db_dpis2->send_cmd($cmd);
    
    $CurPerId=-1;
    $no=1;    
    $sumAB_CODE_04=0;
    $sumAB_CODE_01=0;
    $sumAB_CODE_03=0;
    $sumAB_CODE_99=0;
    $sumAB_CODE_10=0;
    $baseRow = 6;
    $r=0;
    $CntChk = 0;
  //$diffY= ($search_yearEnd-$search_yearBgn)+1;
   $diffY= ($search_yearEnd-$search_yearBgn)+1;
    while ($data = $db_dpis2->get_array_array()) {
        $row = $baseRow + $r;
        $CntChk++;
        if($data[PER_ID]!=$CurPerId){
                $fullname_And_Pos = getNamePersonal($db_dpis,$data[PER_ID]);//."ตำแหน่ง ".getPositionName($db_dpis,$data[PER_ID]);
                $RowNum = $no++;
        }else{
                $fullname_And_Pos = "";
                 $RowNum = '';
                
        }
        $sumAB_CODE_04+=$data[AB_CODE_04];
        $sumAB_CODE_01+=$data[AB_CODE_01];
        $sumAB_CODE_03+=$data[AB_CODE_03];
        $sumAB_CODE_99+=$data[AB_CODE_99];
        $sumAB_CODE_10+=$data[AB_CODE_10];
		
		$SALARYHIS_Last=getSALARYHIS($db_dpis,$data[PER_ID],($data[AS_YEAR]-544)."-10-01");
		$SALARYHIS_Now=getSALARYHIS($db_dpis,$data[PER_ID],($data[AS_YEAR]-543)."-04-01");
		
		$SALARYHIS ="";
		if($SALARYHIS_Last && $SALARYHIS_Now){
			$SALARYHIS = $SALARYHIS_Last.",".$SALARYHIS_Now;
		}else if($SALARYHIS_Last && !$SALARYHIS_Now){
			$SALARYHIS = $SALARYHIS_Last;
		}else if(!$SALARYHIS_Last && $SALARYHIS_Now){
			$SALARYHIS = $SALARYHIS_Now;
		}
		
         
        $objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit($RowNum):$RowNum) ))
                ->setCellValue('B'.$row, iconv('TIS-620','UTF-8',$fullname_And_Pos))
                ->setCellValue('C'.$row, iconv('TIS-620','UTF-8',$data[AS_YEAR]==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit($data[AS_YEAR]):$data[AS_YEAR]) ))
                ->setCellValue('D'.$row, iconv('TIS-620','UTF-8',$data[AB_CODE_04]==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($data[AB_CODE_04],2)):round($data[AB_CODE_04],2)) ))
                ->setCellValue('E'.$row, iconv('TIS-620','UTF-8',$data[AB_CODE_01]==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($data[AB_CODE_01],2)):round($data[AB_CODE_01],2)) ))
                ->setCellValue('F'.$row, iconv('TIS-620','UTF-8',$data[AB_CODE_03]==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($data[AB_CODE_03],2)):round($data[AB_CODE_03],2)) ))
                ->setCellValue('G'.$row, iconv('TIS-620','UTF-8',$data[AB_CODE_99]==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($data[AB_CODE_99],2)):round($data[AB_CODE_99],2)) ))
                ->setCellValue('H'.$row, iconv('TIS-620','UTF-8',$data[AB_CODE_10]==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($data[AB_CODE_10],2)):round($data[AB_CODE_10],2)) ))
                ->setCellValue('I'.$row, iconv('TIS-620','UTF-8',$SALARYHIS));
       $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':I'.$row)->getFill()->getStartColor()->setRGB('FFFFFF');
       $objPHPExcel->getActiveSheet()->getStyle('B'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	   $objPHPExcel->getActiveSheet()->getStyle('I'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

       if($CntChk==$diffY){

            $r++;
            $row = $baseRow + $r;
            $objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, '')->setCellValue('B'.$row, iconv('TIS-620','UTF-8','รวม'));
            $objPHPExcel->getActiveSheet()->getStyle('B'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, iconv('TIS-620','UTF-8',$sumAB_CODE_04==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sumAB_CODE_04,2)):round($sumAB_CODE_04,2)) ));
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$row, iconv('TIS-620','UTF-8',$sumAB_CODE_01==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sumAB_CODE_01,2)):round($sumAB_CODE_01,2)) ));
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$row, iconv('TIS-620','UTF-8',$sumAB_CODE_03==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sumAB_CODE_03,2)):round($sumAB_CODE_03,2)) ));
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$row, iconv('TIS-620','UTF-8',$sumAB_CODE_99==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sumAB_CODE_99,2)):round($sumAB_CODE_99,2)) ));
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$row, iconv('TIS-620','UTF-8',$sumAB_CODE_10==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sumAB_CODE_10,2)):round($sumAB_CODE_10,2)) ));
            $CntChk=0;
            $sumAB_CODE_04=0;
            $sumAB_CODE_01=0;
            $sumAB_CODE_03=0;
            $sumAB_CODE_99=0;
            $sumAB_CODE_10=0;
       }
        
        
        $CurPerId=$data[PER_ID];
        
        $r++;
        }
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="R1209.xls"');
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
