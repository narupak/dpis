<?php
    include("../../php_scripts/connect_database.php");
    include("../../php_scripts/calendar_data.php");
    include ("../php_scripts/function_share.php");
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

    include ("../report/rpt_function.php");

    ini_set("max_execution_time", 0);
    
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
    
    define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
    //date_default_timezone_set('Asia/Bangkok');
    require_once '../../Excel/eslip/Classes/PHPExcel/IOFactory.php';
    
    $objReader = PHPExcel_IOFactory::createReader('Excel5');
    $objPHPExcel = $objReader->load("../../Excel/eslip/templates/R1207_template.xls");
	
	include ("es_font_size.php");
	$objPHPExcel->getActiveSheet()->getStyle('A1:E5')->getFont()->setName(getToFont($CH_PRINT_FONT));
    $objPHPExcel->getActiveSheet()->getStyle('A1:E5')->getFont()->setSize($CH_PRINT_SIZE);
    $show_date = "ตั้งแต่วันที่ 1 ตุลาคม ". ($search_yearBgn-1) ." ถึงวันที่ 30 กันยายน ".$search_yearEnd;
    $show_date = (($NUMBER_DISPLAY==2)?convert2thaidigit($show_date):$show_date);
    $report_title = "รายงานการมาปฏิบัติราชการของบุคลากร||".$show_date;
    
    $search_condition_n = "";
    //หาหน่วยงาน
    if($search_org_id || $search_org_ass_id){
        if($select_org_structure==0 || $PER_AUDIT_FLAG==2){ // แบบตามกฏหมาย
                $varORGID= $search_org_id;
        }else{ //แบบมอบหมายงาน
                $varORGID= $search_org_ass_id;
        }
    }else{
        $varORGID= $DEPARTMENT_ID;
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
	$POS_NO_VAL2="";
	$POS_NO_VAL3="";
	$POS_NO_VAL4="";
	$PER_NAME_VAL = "";
	$PER_SURNAME_VAL = "";
	$LEVEL_NO_VAL ="";
    if($list_person_type=="SELECT"){
		 if(!$SELECTED_PER_ID){$SELECTED_PER_ID=0;}
        $PERID_VAL = "  PER_ID IN($SELECTED_PER_ID) ";
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

        $PERID_VAL = " PER_ID NOT IN(0) ";
	}elseif($list_person_type == "ALL"){
		$PERID_VAL = " PER_ID NOT IN(0) ";
    }else{ $PERID_VAL = " PER_ID  IN($SESS_PER_ID) ";}
   
    
    $cmd = file_get_contents('../es_rpt_pdf_R1204_01.sql');	
    $cmd=str_ireplace(":ORG_ID",$varORGID,$cmd);
    $cmd=str_ireplace(":FISCAL_YEAR1",($search_yearBgn-543),$cmd);
    $cmd=str_ireplace(":FISCAL_YEAR2",($search_yearEnd-543),$cmd);
    $cmd=str_ireplace(":LAW_OR_ASS",$LAW_OR_ASS,$cmd);
    $cmd=str_ireplace(":PER_TYPE",$PER_TYPE,$cmd);
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
		order by ORG.ORG_NAME ASC ,P.PER_NAME ASC , P.PER_SURNAME ASC ,x.FISCAL_YEAR DESC,x.AB_CODE ASC 
	";
	
	
    //die('<pre>'.$cmd);
    $db_dpis2->send_cmd($cmd); 
    
    $show_date = "ตั้งแต่วันที่ 1 เดือน ตุลาคม ". (($NUMBER_DISPLAY==2)?convert2thaidigit($search_yearBgn-1):$search_yearBgn-1) ." ถึงวันที่ 30 กันยายน ".(($NUMBER_DISPLAY==2)?convert2thaidigit($search_yearEnd):$search_yearEnd);
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', iconv('TIS-620','UTF-8','รายงานการมาปฏิบัติราชการของบุคลากร'))
            ->setCellValue('A2', iconv('TIS-620','UTF-8',$show_date));

    $CurPerId=-1;
    $FISCAL_YEAR = "";
    $CHKFISCAL_YEAR = "";
    $NamePersonal="";
    $baseRow = 5;
    $r=0;
    while ($data = $db_dpis2->get_array_array()) {
        $row = $baseRow + $r;
        if($data[FISCAL_YEAR].$data[PER_ID]!=$CHKFISCAL_YEAR){
                $FISCAL_YEAR = (($NUMBER_DISPLAY==2)?convert2thaidigit($data[FISCAL_YEAR]+543):$data[FISCAL_YEAR]+543);
        }else{
                $FISCAL_YEAR = "";
        }
        if($data[PER_ID]!=$CurPerId){
                $NamePersonal = getNamePersonal($db_dpis,$data[PER_ID]);
        }else{
                $NamePersonal = "";
        }
        
        
        $objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, iconv('TIS-620','UTF-8',$NamePersonal))
                ->setCellValue('B'.$row, iconv('TIS-620','UTF-8',$FISCAL_YEAR))
                ->setCellValue('C'.$row, iconv('TIS-620','UTF-8',getNameAbsentType($db_dpis,trim($data[AB_CODE]))))
                ->setCellValue('D'.$row, iconv('TIS-620','UTF-8', (($NUMBER_DISPLAY==2)?convert2thaidigit($data[ABS_DAY]):$data[ABS_DAY]) ))
                ->setCellValue('E'.$row, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit($data[COMMENTDATE]):$data[COMMENTDATE]) ));

        $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':E'.$row)
                                        ->getFill()
                                        ->getStartColor()
                                        ->setRGB('FFFFFF');
        $objPHPExcel->getActiveSheet()->getStyle('A'.$row)
                ->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$row)
                ->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $objPHPExcel->getActiveSheet()->getStyle('E'.$row)->getAlignment()->setWrapText(true)
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT); 
        
        
        
        $CHKFISCAL_YEAR=$data[FISCAL_YEAR].$data[PER_ID];
        $CurPerId=$data[PER_ID];

        $r++;
    }
    
	$objPHPExcel->getActiveSheet()->removeRow($row+1);
	
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="R1207.xls"');
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
