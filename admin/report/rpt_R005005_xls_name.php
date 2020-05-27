<?
        ini_set("max_execution_time",0);
        set_time_limit(0);
        ini_set("memory_limit","512M");
        //ini_set("session.gc_maxlifetime",60*60*24);
        
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	//include ("rpt_R005005_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น
        
        function getToFont($id){
            if($id==1){
                    $fullname	= 'Angsana';
            }else if($id==2){
                    $fullname	= 'Cordia';
            }else if($id==3){
                    $fullname	= 'TH SarabunPSK';
            }else{
                    $fullname	= 'Browallia';
            }
            return $fullname;
        }
        
        require_once '../../Excel/eslip/Classes/PHPExcel.php';
        
        $dir_folder = file_exists("../../Excel/tmp/R0505");
        if($dir_folder==0){
            mkdir("../../Excel/tmp/R0505"); 
        }

        
        $files = glob('../../Excel/tmp/R0505/rpt_R005005_name.xlsx'); // get all file names
        foreach($files as $file){ // iterate files
          if(is_file($file))
            unlink($file); // delete file
        }

/*$logfile = '../../Excel/tmp/R0505/XLOG-' . date('YmdHis')  . '.txt';
    if (1==1) {
        $fp = fopen($logfile, 'a');
        fwrite($fp, "Begin!!!!\r\n");
        fclose($fp);                    
    }*/
        
        
        
        if($MFA_FLAG == 1) {
	$DATE_DISPLAY=2;
	}
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=f.PL_CODE";
		$pl_code = "b.PL_CODE";
		$pl_name = "f.PL_NAME";
		$type_code ="b.PT_CODE";
		$select_type_code =",b.PT_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=f.PN_CODE";
		$pl_code = "b.PN_CODE";
		$pl_name = "f.PN_NAME";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=f.EP_CODE";
		$pl_code = "b.EP_CODE";
		$pl_name = "f.EP_NAME";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=f.TP_CODE";
		$pl_code = "b.TP_CODE";
		$pl_name = "f.TP_NAME";
	} // end if	
	
	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG"); 
//	$search_per_type = 1;

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	$list_type_text = $ALL_REPORT_TITLE;

	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
	}elseif($MINISTRY_ID){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
		if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		$list_type_text .= " - $MINISTRY_NAME";
	}elseif($PROVINCE_CODE){
		$PROVINCE_CODE = trim($PROVINCE_CODE);
		$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
	if(trim($search_org_id)){ 
		if($select_org_structure==0){
			$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
		}else if($select_org_structure==1){
			$arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		}
		$list_type_text = "$search_org_name";
	} // end if
        if(trim($search_year)){ 
            $search_year = $search_year-543;
            $arr_search_condition[] = "MONTHS_BETWEEN(TO_DATE($search_year||'-07-28','yyyy-mm-dd'),TO_DATE(PER_STARTDATE,'yyyy-mm-dd'))/12 >=25";
	} 
	if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);

//	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	if($MFA_FLAG == 1)
	$report_title = "ประวัติสำหรับเสนอขอพระราชทานเหรียญจักรพรรดิมาลา/เหรียญจักรมาลา";
	else
	$report_title = "$DEPARTMENT_NAME||ประวัติสำหรับเสนอขอพระราชทานเหรียญจักรพรรดิมาลา";
	
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0505";
	$report_code = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_code):$report_code);
	
        
        
        
        $ws_head_line1 = array("วันเดือนปี","ตำแหน่ง","หน่วยงาน","อายุ","เงินเดือน","หมายเหตุ");
        $ws_head_line2 = array("ที่รับราชการ","","","","","");
        $ws_width = array(10,30,45,10,10,60);
        // new 
        $object = new PHPExcel();
        /*
            $object->setActiveSheetIndex(0);
            $object->getActiveSheet()->getDefaultRowDimension()->setRowHeight(23);
            $object->getActiveSheet()->getColumnDimension('C')->setAutoSize(TRUE);
            $object->getActiveSheet()->getColumnDimension('D')->setAutoSize(TRUE);
            $object->getActiveSheet()->getColumnDimension('C')->setAutoSize(TRUE);
            $object->getActiveSheet()->getColumnDimension('E')->setAutoSize(TRUE);
            $object->getActiveSheet()->getColumnDimension('F')->setAutoSize(TRUE);
            
            $column = 1;
                for($excel_row=2;$excel_row<=10;$excel_row++){

                    $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, "1");
                    $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, "ม.ค.");
                    $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, "15000");
                    $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, "100");
                    $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, "-");
                    $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, "500");

                     $excel_row++;
                }     
        */    
            //$object->getActiveSheet()->getStyle('F5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //ตัวอย่างการเซตสี
            //$object->getActiveSheet()->getStyle('B5:M5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('C6E0B4');
           //$object->getActiveSheet()->getStyle('A3')->getFont()->setBold(TRUE)->getColor()->setRGB($set_font_color_content);
           
           $cmd = "    
                            SELECT  a.PER_ID, d.PN_NAME||a.PER_NAME||' '||a.PER_SURNAME as name, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, a.LEVEL_NO, a.DEPARTMENT_ID, b.ORG_ID , c.ORG_NAME , $pl_code as PL_CODE, $pl_name as PL_NAME , $pl_name||g.POSITION_LEVEL AS POS_NAME $select_type_code ,SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE 
                            from    PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, $line_table f, PER_LEVEL g
                            WHERE   $position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+) and $line_join(+)  AND a.PER_ID NOT IN (SELECT PER_ID FROM per_decoratehis where dc_code = '61') and a.LEVEL_NO = g.LEVEL_NO
                                    $search_condition
                            order by g.LEVEL_SEQ_NO        
                       
                    ";
                    //echo "<pre>";
                    //die($cmd);
       if($select_org_structure==1) { 
                $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
                $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
        }
        /*echo '.';
        ob_flush(); 
        flush();*/
        $count_data = $db_dpis->send_cmd($cmd);
        $db_dpis1->send_cmd($cmd);
        //$count_data = 1;
        
        $font_name = getToFont($CH_PRINT_FONT);
        $styleArray_head1 = array(
            'font'  => array(
                'bold'  => TRUE,
                'size'  => '16',
                'name'  => $font_name
        ));
        $styleArray_head = array(
            'font'  => array(
                'bold'  => FALSE,
                'size'  => '14',
                'name'  => $font_name
        ));
        $styleArray_body = array(
            'font'  => array(
                'bold'  => FALSE,
                'size'  => '14',
                'name'  => $font_name
         ));
        //set border header
        $styleArray=array(
        'borders'=>array(
            'allborders'=>array(
                'style'=>PHPExcel_Style_Border::BORDER_THIN
                    )
                )
        );
        
        if($count_data){
                
                
                $xlsRow++;
                $object->getActiveSheet()->getStyle('A1:F2')->applyFromArray($styleArray_head1);
                $object->setActiveSheetIndex(0)->mergeCells('A1:F1')->setCellValue('A1', iconv('TIS-620','UTF-8','สำนักงานศาลยุติธรรม'))->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $object->setActiveSheetIndex(0)->mergeCells('A2:F2')->setCellValue('A2', iconv('TIS-620','UTF-8','ประวัติสำหรับเสนอขอพระราชทานเหรียญจักรพรรดิมาลา'))->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                
                $name = "";
                $POSITION_HIS_Before ="";
                $ORG_HIS_Before ="";
                $data_count = 0;
             
                $idx_count = 0;
                $count_tab = 1;
                $xlsRow = 3;
//$logfile = 'XLOG-' . date('YmdHis')  . '.txt';    
   /* if (1==1) {
        $fp = fopen($logfile, 'a');
        fwrite($fp, "Start!\r\n");
        fwrite($fp,"max_execution_time=".ini_get('max_execution_time')."\r\n");
        fwrite($fp,"session.gc_maxlifetime=".ini_get("session.gc_maxlifetime")."\r\n");
        
        fclose($fp);                    
    }*/


$person = 0;                

                while($data = $db_dpis->get_array()){  
                    
                    //if($data_count%10==0){
                       // echo ".";
                        //ob_flush(); 
                        //flush();
                    //}       
    /*if (1==1) {
        $person++;                
        $fp = fopen($logfile, 'a');

        fwrite($fp, 'Data:' . $data_count . '   Excel row:' . $xlsRow . "\r\n");
        fclose($fp);                    
    }*/
                    //$PER_ID = $data[PER_ID];
                    $PER_NAME = $data[NAME];
                    $PER_POS_NAME = $data[POS_NAME];
                    //ชื่อสำนัก / กอง
                    $ORG_NAME = $data[ORG_NAME];
                    //ชื่อกรม
                    $DEPARTMENT_ID = $data[DEPARTMENT_ID];
                    $cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID";
                    if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                    $db_dpis2->send_cmd($cmd);
                    $data2 = $db_dpis2->get_array();
                    $DEPARTMENT_NAME = $data2[ORG_NAME];
                    //ชื่อกระทรวง
                    $REF_ORG_ID_REF = $data2[ORG_ID_REF];
                    $cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$REF_ORG_ID_REF ";
                    if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                    $db_dpis2->send_cmd($cmd);
                    $data2 = $db_dpis2->get_array();
                    $MINISTRY_NAME = $data2[ORG_NAME];

                    $PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
                    if($PER_BIRTHDATE){
                            $arr_temp = explode("-", $PER_BIRTHDATE);
                            $PER_BIRTHDATE_D = $arr_temp[2] + 0;
                            $PER_BIRTHDATE_M = $month_full[($arr_temp[1] + 0)][TH];
                            $PER_BIRTHDATE_Y = $arr_temp[0] + 543;
                    } // end if

                    $PER_STARTDATE = trim($data[PER_STARTDATE]);
                    $COMPLETE_CONDDATE = date_adjust($PER_STARTDATE, "year", 25);
                    $COMPLETE_CONDDATE = date_adjust($COMPLETE_CONDDATE,'d',-1);
                    if($COMPLETE_CONDDATE){
                            $arr_temp = explode("-", $COMPLETE_CONDDATE);
                            $COMPLETE_CONDDATE_D = $arr_temp[2] + 0;
                            $COMPLETE_CONDDATE_M = $month_full[($arr_temp[1] + 0)][TH];
                            $COMPLETE_CONDDATE_Y = $arr_temp[0] + 543;
                    } // end if
                    
                    if($PER_NAME!=$name){
			$idx_count++;
									
                        if($name!=""){
                              
                            
                            $xlsRow++;
                            $object->setActiveSheetIndex(0)->mergeCells('A'.$xlsRow.':F'.$xlsRow);
                            $xlsRow++;
                            $object->getActiveSheet()->getStyle('A'.$xlsRow.':F'.$xlsRow)->applyFromArray($styleArray_body);
                            $object->getActiveSheet()->mergeCells('D'.$xlsRow.':F'.$xlsRow)->setCellValue('D'.$xlsRow, iconv('TIS-620','UTF-8',("ลงชื่อ  ". str_repeat(".", 70))))->getStyle('D'.$xlsRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $xlsRow++;
                            $object->getActiveSheet()->getStyle('A'.$xlsRow.':F'.$xlsRow)->applyFromArray($styleArray_body);
                            $object->getActiveSheet()->mergeCells('D'.$xlsRow.':F'.$xlsRow)->setCellValue('D'.$xlsRow, iconv('TIS-620','UTF-8',"$name"))->getStyle('D'.$xlsRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $xlsRow++;
                            $object->setActiveSheetIndex(0)->mergeCells('A'.$xlsRow.':F'.$xlsRow);
                            /*if($idx_count>=1){
                                break;
                            }*/
                               
                        }
                        $xlsRow++;
                        $object->getActiveSheet()->getColumnDimension('A')->setWidth('25')->setAutoSize(FALSE);
                        $object->getActiveSheet()->getColumnDimension('B')->setWidth('30')->setAutoSize(FALSE);
                        $object->getActiveSheet()->getColumnDimension('C')->setWidth('60')->setAutoSize(FALSE);
                        $object->getActiveSheet()->getColumnDimension('D')->setWidth('10')->setAutoSize(FALSE);
                        $object->getActiveSheet()->getColumnDimension('E')->setWidth('10')->setAutoSize(FALSE);
                        $object->getActiveSheet()->getColumnDimension('F')->setWidth('60')->setAutoSize(FALSE);
                        $object->getActiveSheet()->getRowDimension($xlsRow)->setRowHeight(37.5);
                        
                        $object->getActiveSheet()->getStyle('A'.$xlsRow.':F'.$xlsRow)->applyFromArray($styleArray_body);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(0, $xlsRow, iconv('TIS-620','UTF-8',"ชื่อ  $PER_NAME"));
                        $object->getActiveSheet()->setCellValueByColumnAndRow(3, $xlsRow, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit("ตำแหน่ง  $PER_POS_NAME"):"ตำแหน่ง  $PER_POS_NAME")));
                        $xlsRow++;
                        $object->getActiveSheet()->getStyle('A'.$xlsRow.':F'.$xlsRow)->applyFromArray($styleArray_body);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(0, $xlsRow, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit("$ORG_TITLE  $ORG_NAME"):"$ORG_TITLE  $ORG_NAME")));
                        $object->getActiveSheet()->setCellValueByColumnAndRow(3, $xlsRow, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit("$DEPARTMENT_TITLE  $DEPARTMENT_NAME"):"$DEPARTMENT_TITLE  $DEPARTMENT_NAME")));
                        $xlsRow++;
                        $object->getActiveSheet()->getStyle('A'.$xlsRow.':F'.$xlsRow)->applyFromArray($styleArray_body);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(0, $xlsRow, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit("$MINISTRY_TITLE  $MINISTRY_NAME"):"$MINISTRY_TITLE  $MINISTRY_NAME")));
                        $object->getActiveSheet()->setCellValueByColumnAndRow(3, $xlsRow, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit("เกิดวันที่  $PER_BIRTHDATE_D  เดือน  $PER_BIRTHDATE_M  พ.ศ.  $PER_BIRTHDATE_Y"):"เกิดวันที่  $PER_BIRTHDATE_D  เดือน  $PER_BIRTHDATE_M  พ.ศ.  $PER_BIRTHDATE_Y")));
                        $xlsRow++;
                        $object->getActiveSheet()->getStyle('A'.$xlsRow.':F'.$xlsRow)->applyFromArray($styleArray_body);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(0, $xlsRow, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit("รับราชการมาครบ 25 ปี  เมื่อวันที่  $COMPLETE_CONDDATE_D  เดือน  $COMPLETE_CONDDATE_M  พ.ศ.  $COMPLETE_CONDDATE_Y"):"รับราชการมาครบ 25 ปี  เมื่อวันที่  $COMPLETE_CONDDATE_D  เดือน  $COMPLETE_CONDDATE_M  พ.ศ.  $COMPLETE_CONDDATE_Y")));
                        $xlsRow++;
                       
                        //print_header($worksheet);
                        $name = $PER_NAME;

                    }   
                    $data_count++;

                }
               
                $xlsRow++;
                $object->setActiveSheetIndex(0)->mergeCells('A'.$xlsRow.':F'.$xlsRow);
                $xlsRow++;
                $object->getActiveSheet()->getStyle('A'.$xlsRow.':F'.$xlsRow)->applyFromArray($styleArray_body);
                $object->getActiveSheet()->mergeCells('D'.$xlsRow.':F'.$xlsRow)->setCellValue('D'.$xlsRow, iconv('TIS-620','UTF-8',("ลงชื่อ  ". str_repeat(".", 70))))->getStyle('D'.$xlsRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $xlsRow++;
                $object->getActiveSheet()->getStyle('A'.$xlsRow.':F'.$xlsRow)->applyFromArray($styleArray_body);
                $object->getActiveSheet()->mergeCells('D'.$xlsRow.':F'.$xlsRow)->setCellValue('D'.$xlsRow, iconv('TIS-620','UTF-8',"$name"))->getStyle('D'.$xlsRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $xlsRow++;
                //die();
            
        }else{
            //ไม่มีข้อมูล
            $xlsRow++;
            $object->getActiveSheet()->getStyle('A1:F2')->applyFromArray($styleArray_head);
            $object->setActiveSheetIndex(0)->mergeCells('A1:F1')->setCellValue('A1', iconv('TIS-620','UTF-8','**** ไม่พบข้อมูล ****'))->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //$object->setActiveSheetIndex(0)->mergeCells('A2:F2')->setCellValue('A2', iconv('TIS-620','UTF-8','ประวัติสำหรับเสนอขอพระราชทานเหรียญจักรพรรดิมาลา'))->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }

    /*if (1==1) {
        $person++;                
        $fp = fopen($logfile, 'a');

        fwrite($fp, "End\r\n");
        fclose($fp);                    
    }*/


        
        //ob_clean();
       /* $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="R0505.xlsx"');
        $object_writer->save('php://output');*/
        
        //$objPHPExcel->setActiveSheetIndex(0);
        
        $file_name = "../../Excel/tmp/R0505/rpt_R005005_name.xlsx";
        $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        $objWriter->save($file_name);
     
        
        if(file_exists($file_name )){
            unlink($logfile);
            echo "<a href='".$file_name."'>R0505_name.xlsx คลิกเพื่อ Download</a><br>";
        }
        
?>
   