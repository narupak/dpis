<?php include("../../php_scripts/connect_database.php");
    include("../php_scripts/pdf_wordarray_thaicut.php");
    include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
    define('FPDF_FONTPATH','../../PDF/font/');
    include ("../../PDF/es_fpdf.php");
    include ("../../PDF/es_pdf_extends_DPIS.php");  
    
    ini_set("max_execution_time", $max_execution_time);
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
    //echo getNamePersonal($db_dpis2,14508);
    
    $unit="mm";
    $paper_size="A4";
    $lang_code="TH";
    $orientation='P';
    

    $report_title = "รายงานประวัติการมาปฏิบัติงานของบุคลากร||ตั้งแต่ปีงบประมาณ ".$search_yearBgn." ถึงปีงบประมาณ ".$search_yearEnd;
   $report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
    $report_code = "R1209";
    include ("es_rpt_pdf_R1204_03_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
    session_cache_limiter("private");
    session_start();
    
    $company_name="";
    $pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
    
    $pdf->Open();
    $pdf->SetFont($font,'',$CH_PRINT_SIZE);
    $pdf->SetMargins($Px_DISLEFT,$Px_DISHEAD,$Px_DISRIGHT);
	$pdf->AliasNbPages();
	$pdf->SetTextColor(0, 0, 0);
        
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
    $page_start_x = $pdf->x;
    $page_start_y = $pdf->y;

    $head_text1 = implode(",", $heading_text);
    $head_width1 = implode(",", $heading_width);
    $head_align1 = implode(",", $heading_align);
    $col_function = implode(",", $column_function);
    $pdf->AutoPageBreak = false;
    $no=0;    
    $sumAB_CODE_04=0;
    $sumAB_CODE_01=0;
    $sumAB_CODE_03=0;
    $sumAB_CODE_99=0;
    $sumAB_CODE_10=0;
    while ($data = $db_dpis2->get_array_array()) {
        $no++;
        if($CurPerId!=$data[PER_ID]){
            if($CurPerId!=0){
                $no=1; 
                $arr_dataSum = (array) null;
                $arr_dataSum[] = '<**1**>รวม';
                $arr_dataSum[] = '<**1**>รวม';
                $arr_dataSum[] = $sumAB_CODE_04==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sumAB_CODE_04,2)." "):round($sumAB_CODE_04,2)." ");
                $arr_dataSum[] = $sumAB_CODE_01==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sumAB_CODE_01,2)." "):round($sumAB_CODE_01,2)." ");
                $arr_dataSum[] = $sumAB_CODE_03==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sumAB_CODE_03,2)." "):round($sumAB_CODE_03,2)." ");
                $arr_dataSum[] = $sumAB_CODE_99==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sumAB_CODE_99,2)." "):round($sumAB_CODE_99,2)." ");
                $arr_dataSum[] = $sumAB_CODE_10==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sumAB_CODE_10,2)." "):round($sumAB_CODE_10,2)." ");
                $arr_dataSum[] = '';
                $data_align = array("C","C","C","C","C","C","C","L");
                $result = $pdf->add_data_tab($arr_dataSum, 7, "TRHBL", $data_align, "", $CH_PRINT_SIZE, "", "000000", "");
                $sumAB_CODE_04=0;
                $sumAB_CODE_01=0;
                $sumAB_CODE_03=0;
                $sumAB_CODE_99=0;
                $sumAB_CODE_10=0;
    
                $pdf->close_tab(""); 
                $pdf->SetFont($font,'',$CH_PRINT_SIZE);
                $pdf->AddPage();
                $page_start_x = $pdf->x;
                $page_start_y = $pdf->y;

                $head_text1 = implode(",", $heading_text);
                $head_width1 = implode(",", $heading_width);
                $head_align1 = implode(",", $heading_align);
                $col_function = implode(",", $column_function);
            }
            $pdf->SetFont($font,'',$CH_PRINT_SIZE);
            $pdf->Cell(0, 7, "ชื่อ ".getNamePersonal($db_dpis,$data[PER_ID]), 0, 1, 'L', 0);
            $CurPerId=$data[PER_ID];
            //$Lastcnt=0;
            $result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", $CH_PRINT_SIZE, "b", "000", "EEEEFF", $COLUMN_FORMAT, $col_function);
        }
        
        $arr_data = (array) null;
        $arr_data[] = (($NUMBER_DISPLAY==2)?convert2thaidigit($no):$no);
        $arr_data[] = $data[AS_YEAR]==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit($data[AS_YEAR]):$data[AS_YEAR]);
        $arr_data[] = $data[AB_CODE_04]==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($data[AB_CODE_04],2)." "):round($data[AB_CODE_04],2)." ");
        $arr_data[] = $data[AB_CODE_01]==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($data[AB_CODE_01],2)." "):round($data[AB_CODE_01],2)." ");
        $arr_data[] = $data[AB_CODE_03]==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($data[AB_CODE_03],2)." "):round($data[AB_CODE_03],2)." ");
        $arr_data[] = $data[AB_CODE_99]==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($data[AB_CODE_99],2)." "):round($data[AB_CODE_99],2)." ");
        $arr_data[] = $data[AB_CODE_10]==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($data[AB_CODE_10],2)." "):round($data[AB_CODE_10],2)." ");
        
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

        $arr_data[] = $SALARYHIS;
        $data_align = array("C","C","C","C","C","C","C","L");
        $result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", $CH_PRINT_SIZE, "", "000000", "");
        
        $sumAB_CODE_04+=$data[AB_CODE_04];
        $sumAB_CODE_01+=$data[AB_CODE_01];
        $sumAB_CODE_03+=$data[AB_CODE_03];
        $sumAB_CODE_99+=$data[AB_CODE_99];
        $sumAB_CODE_10+=$data[AB_CODE_10];
    }
    
    $arr_dataSum = (array) null;
    $arr_dataSum[] = '<**1**>รวม';
    $arr_dataSum[] = '<**1**>รวม';
    $arr_dataSum[] = $sumAB_CODE_04==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sumAB_CODE_04,2)." "):round($sumAB_CODE_04,2)." ");
    $arr_dataSum[] = $sumAB_CODE_01==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sumAB_CODE_01,2)." "):round($sumAB_CODE_01,2)." ");
    $arr_dataSum[] = $sumAB_CODE_03==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sumAB_CODE_03,2)." "):round($sumAB_CODE_03,2)." ");
    $arr_dataSum[] = $sumAB_CODE_99==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sumAB_CODE_99,2)." "):round($sumAB_CODE_99,2)." ");
    $arr_dataSum[] = $sumAB_CODE_10==0?'':(($NUMBER_DISPLAY==2)?convert2thaidigit(round($sumAB_CODE_10,2)." "):round($sumAB_CODE_10,2)." ");
    $arr_dataSum[] = '';
    $data_align = array("C","C","C","C","C","C","C","L");
    $result = $pdf->add_data_tab($arr_dataSum, 7, "TRHBL", $data_align, "", $CH_PRINT_SIZE, "", "000000", "");
    
    
    $pdf->close_tab("");
    $pdf->close();
    $fname = "R1209.pdf";
	$pdf->Output($fname,'D');
   
   
?>