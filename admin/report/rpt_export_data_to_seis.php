<?php
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

    //แปลง ตัวเลข อาราบิก to ไทย
    function thainumDigit($chk_num,$num){
        if($chk_num=='1'){
            return str_replace(
                array( "๐" , "๑" , "๒" , "๓" , "๔" , "๕" , "๖" , "๗" , "๘" , "๙" ),
                array( '0' , '1' , '2' , '3' , '4' , '5' , '6' ,'7' , '8' , '9' ),$num
            );
        }else{
            return str_replace(
                array( '0' , '1' , '2' , '3' , '4' , '5' , '6' ,'7' , '8' , '9' ),
                array( "๐" , "๑" , "๒" , "๓" , "๔" , "๕" , "๖" , "๗" , "๘" , "๙" ),$num
            );
        }
    }
    
    include("../../php_scripts/connect_database.php");
    include("../../php_scripts/calendar_data.php");
    include ("../php_scripts/function_share.php");
    include ("../report/rpt_function.php");
        
    $db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis7 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        
    //if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
    // new 
    require_once '../../Excel/eslip/Classes/PHPExcel.php';
    $object = new PHPExcel();
    /*ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);*/
    $font_name = getToFont(3);
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
    
    $report_title = $DOC_NAME;
    $DOC_NAME1 = str_replace("/","_",$DOC_NAME);
    $DOC_NAME2 = str_replace("\\","_",$DOC_NAME1);
    $DOC_NAME3 = str_replace(":","_",$DOC_NAME2);
    $DOC_NAME4 = str_replace("*","_",$DOC_NAME3);
    $DOC_NAME5 = str_replace("|","_",$DOC_NAME4);
    $DOC_NAME6 = str_replace("?","_",$DOC_NAME5);
    $DOC_NAME7 = str_replace('"',"_",$DOC_NAME6);
    $DOC_NAME8 = str_replace('<',"_",$DOC_NAME7);
    $DOC_NAME9 = str_replace('>',"_",$DOC_NAME8);
    
    //$str = preg_replace('/[^A-Za-z0-9-]/','_', $DOC_NAME);
    $report_code = "$DOC_NAME9";
    
    $cmd =  "   SELECT p.* from (
                    SELECT 
                        CASE WHEN b.PER_CARDNO IS NOT NULL THEN b.PER_CARDNO ELSE a.PER_CARDNO END as CARD_NO_PERSON , 
                        c.PN_NAME||b.PER_NAME||' '||b.PER_SURNAME as FULL_NAME, a.POH_LAST_POSITION, a.POH_POS_NO, substr(a.POH_EFFECTIVEDATE,1,10) as POH_EFFECTIVEDATE , 
                        d.MOV_NAME, a.POH_SALARY, a.POH_SALARY_POS, a.POH_DOCNO ,  substr(a.POH_DOCDATE,1,10) as POH_DOCDATE , a.POH_SEQ_NO,'พิมพ์' as PRINT_ON_REPORT, 
                        CASE WHEN a.POH_PM_NAME IS NOT NULL THEN a.POH_PM_NAME ELSE j.PM_NAME END AS POH_PM_NAME ,
                        CASE WHEN 
                            e.PT_NAME IS NOT NULL and e.PT_NAME != 'ทั่วไป' and trim(k.POSITION_LEVEL) >= '6' AND  trim(k.POSITION_LEVEL) <= '9' THEN e.PT_NAME ELSE '' END PT_NAME ,
                        CASE WHEN 
                          a.POH_LEVEL_NO IS NOT NULL AND (a.POH_LEVEL_NO = 'D1' OR a.POH_LEVEL_NO = 'D2' OR a.POH_LEVEL_NO = 'M1' OR a.POH_LEVEL_NO = 'M2') THEN k.POSITION_TYPE||k.POSITION_LEVEL
                        ELSE 
                            CASE WHEN a.POH_LEVEL_NO IS NOT NULL THEN k.POSITION_LEVEL ELSE '' END 
                        END AS POH_LEVEL_NO ,
                        a.POH_ORG1 as ORG_NAME_1, a.POH_ORG2 as ORG_NAME_2, a.POH_ORG3 as ORG_NAME_3, a.POH_UNDER_ORG1, 
                        a.POH_UNDER_ORG2,  a.POH_PL_NAME ,  a.POH_ORG, a.POH_REMARK1||' '||POH_REMARK2 as POH_REMARK , 
                        CASE b.PER_TYPE 
                          WHEN 1 THEN CASE WHEN f.PL_NAME IS NOT NULL THEN f.PL_NAME ELSE a.PL_CODE END
                          WHEN 2 THEN CASE WHEN g.PN_NAME IS NOT NULL THEN g.PN_NAME ELSE a.POH_PL_NAME END
                          WHEN 3 THEN CASE WHEN h.EP_NAME IS NOT NULL THEN h.EP_NAME ELSE a.POH_PL_NAME END
                          WHEN 4 THEN CASE WHEN i.TP_NAME IS NOT NULL THEN i.TP_NAME ELSE a.POH_PL_NAME END
                        ELSE '' END as PL_NAME , a.POH_CMD_SEQ
                    FROM PER_POSITIONHIS   a 
                    LEFT JOIN PER_PERSONAL b ON (a.PER_ID = b.PER_ID)
                    LEFT JOIN PER_PRENAME  c ON (b.PN_CODE = c.PN_CODE)
                    LEFT JOIN PER_MOVMENT  d ON (a.MOV_CODE = d.MOV_CODE)
                    LEFT JOIN PER_TYPE     e ON (a.PT_CODE = e.PT_CODE)
                    LEFT JOIN PER_LINE     f ON (a.PL_CODE = f.PL_CODE)
                    LEFT JOIN PER_POS_NAME g ON (a.PN_CODE = g.PN_CODE)
                    LEFT JOIN PER_EMPSER_POS_NAME  h ON (a.EP_CODE = h.EP_CODE)
                    LEFT JOIN PER_TEMP_POS_NAME    i ON (a.TP_CODE = i.TP_CODE)
                    LEFT JOIN PER_MGT      j ON (a.PM_CODE = j.PM_CODE)
                    LEFT JOIN PER_LEVEL   k ON (a.POH_LEVEL_NO = k.LEVEL_NO)
                    WHERE  trim(a.POH_DOCNO) =  trim('$DOC_NAME')
                ) p ORDER BY p.POH_CMD_SEQ ,p.CARD_NO_PERSON 
            ";
    $db_dpis->send_cmd($cmd);
    $cnt_data = $db_dpis->num_rows();
    $db_dpis1->send_cmd($cmd);
    //echo "<pre>";die($cmd);
   
   if($cnt_data){
        $chk_i=0;
        $data_count=0;
        $TEMP_PER_ID = 0;
        $TEMP_POS_CODE = '';
        $TEMP_ORG_ID = 0;
       
        $object->getActiveSheet()->getColumnDimension('A')->setWidth('20')->setAutoSize(FALSE);
        $object->getActiveSheet()->getColumnDimension('B')->setWidth('25')->setAutoSize(FALSE);
        $object->getActiveSheet()->getColumnDimension('C')->setWidth('10')->setAutoSize(FALSE);
        $object->getActiveSheet()->getColumnDimension('D')->setWidth('11')->setAutoSize(FALSE);
        $object->getActiveSheet()->getColumnDimension('E')->setWidth('15')->setAutoSize(FALSE);
        $object->getActiveSheet()->getColumnDimension('F')->setWidth('25')->setAutoSize(FALSE);
        $object->getActiveSheet()->getColumnDimension('G')->setWidth('14')->setAutoSize(FALSE);
        $object->getActiveSheet()->getColumnDimension('H')->setWidth('16')->setAutoSize(FALSE);
        $object->getActiveSheet()->getColumnDimension('I')->setWidth('20')->setAutoSize(FALSE);
        $object->getActiveSheet()->getColumnDimension('J')->setWidth('15')->setAutoSize(FALSE);
        $object->getActiveSheet()->getColumnDimension('K')->setWidth('20')->setAutoSize(FALSE);
        $object->getActiveSheet()->getColumnDimension('L')->setWidth('15')->setAutoSize(FALSE);
        $object->getActiveSheet()->getColumnDimension('M')->setWidth('30')->setAutoSize(FALSE);
        $object->getActiveSheet()->getColumnDimension('N')->setWidth('30')->setAutoSize(FALSE);
        $object->getActiveSheet()->getColumnDimension('O')->setWidth('25')->setAutoSize(FALSE);
        $object->getActiveSheet()->getColumnDimension('P')->setWidth('20')->setAutoSize(FALSE);
        $object->getActiveSheet()->getColumnDimension('Q')->setWidth('40')->setAutoSize(FALSE);
        $object->getActiveSheet()->getColumnDimension('R')->setWidth('40')->setAutoSize(FALSE);
        $object->getActiveSheet()->getColumnDimension('S')->setWidth('40')->setAutoSize(FALSE);
        $object->getActiveSheet()->getColumnDimension('T')->setWidth('40')->setAutoSize(FALSE);
        $object->getActiveSheet()->getColumnDimension('U')->setWidth('40')->setAutoSize(FALSE);
        $object->getActiveSheet()->getColumnDimension('V')->setWidth('40')->setAutoSize(FALSE);
        $object->getActiveSheet()->getColumnDimension('W')->setWidth('40')->setAutoSize(FALSE);
        $object->getActiveSheet()->getColumnDimension('X')->setWidth('40')->setAutoSize(FALSE);
        
        $object->getActiveSheet()->getStyle('A1:X2')->applyFromArray($styleArray_head1);
        $object->setActiveSheetIndex(0)->mergeCells('A1:X2')->setCellValue('A1', @iconv('TIS-620','UTF-8',"ส่งออกคำสั่งเลขที่ : ".$report_title))->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $object->setActiveSheetIndex(0)->setCellValue('A3', @iconv('TIS-620','UTF-8','เลขประจำตัวประชาชน'))->getStyle('A3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->setActiveSheetIndex(0)->setCellValue('B3',  @iconv("tis-620", "utf-8",'ชื่อ-นามสกุล'))->getStyle('B3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->setActiveSheetIndex(0)->setCellValue('C3',  @iconv("tis-620", "utf-8",'คำสั่งล่าสุด'))->getStyle('C3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->setActiveSheetIndex(0)->setCellValue('D3',  @iconv("tis-620", "utf-8",'เลขที่ตำแหน่ง'))->getStyle('D3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->setActiveSheetIndex(0)->setCellValue('E3',  @iconv("tis-620", "utf-8",'วันที่มีผล'))->getStyle('E3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->setActiveSheetIndex(0)->setCellValue('F3',  @iconv("tis-620", "utf-8",'ประเภทการเคลื่อนไหว'))->getStyle('F3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->setActiveSheetIndex(0)->setCellValue('G3',  @iconv("tis-620", "utf-8",'อัตราเงินเดือน'))->getStyle('G3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->setActiveSheetIndex(0)->setCellValue('H3',  @iconv("tis-620", "utf-8",'เงินประจำตำแหน่ง'))->getStyle('H3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->setActiveSheetIndex(0)->setCellValue('I3',  @iconv("tis-620", "utf-8",'เลขที่คำสั่ง'))->getStyle('I3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->setActiveSheetIndex(0)->setCellValue('J3',  @iconv("tis-620", "utf-8",'วันที่ออกคำสั่ง'))->getStyle('J3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->setActiveSheetIndex(0)->setCellValue('K3',  @iconv("tis-620", "utf-8",'ลำดับที่กรณีวันที่เดียวกัน'))->getStyle('K3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setWrapText(TRUE);
        $object->setActiveSheetIndex(0)->setCellValue('L3',  @iconv("tis-620", "utf-8",'พิมพ์ในรายงาน'))->getStyle('L3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->setActiveSheetIndex(0)->setCellValue('M3',  @iconv("tis-620", "utf-8",'ตำแหน่งในสายงาน'))->getStyle('M3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->setActiveSheetIndex(0)->setCellValue('N3',  @iconv("tis-620", "utf-8",'ตำแหน่งในการบริหาร'))->getStyle('N3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->setActiveSheetIndex(0)->setCellValue('O3',  @iconv("tis-620", "utf-8",'ประเภทตำแหน่งตาม พรบ. 2535'))->getStyle('O3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->setActiveSheetIndex(0)->setCellValue('P3',  @iconv("tis-620", "utf-8",'ระดับของผู้ดำรงตำแหน่ง'))->getStyle('P3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->setActiveSheetIndex(0)->setCellValue('Q3',  @iconv("tis-620", "utf-8",'กระทรวง'))->getStyle('Q3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->setActiveSheetIndex(0)->setCellValue('R3',  @iconv("tis-620", "utf-8",'กรม'))->getStyle('R3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->setActiveSheetIndex(0)->setCellValue('S3',  @iconv("tis-620", "utf-8",'สำนัก / กอง'))->getStyle('S3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->setActiveSheetIndex(0)->setCellValue('T3',  @iconv("tis-620", "utf-8",'ต่ำกว่าสำนัก / กอง 1 ระดับ'))->getStyle('T3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->setActiveSheetIndex(0)->setCellValue('U3',  @iconv("tis-620", "utf-8",'ต่ำกว่าสำนัก / กอง 2 ระดับ'))->getStyle('U3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->setActiveSheetIndex(0)->setCellValue('V3',  @iconv("tis-620", "utf-8",'ตำแหน่ง ก.พ. 7'))->getStyle('V3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->setActiveSheetIndex(0)->setCellValue('W3',  @iconv("tis-620", "utf-8",'ส่วนราชการ  ก.พ. 7'))->getStyle('W3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->setActiveSheetIndex(0)->setCellValue('X3',  @iconv("tis-620", "utf-8",'หมายเหตุ'))->getStyle('X3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        //set font herder color
        $styleArray_head = array(
                'font'  => array(
                'bold'  => TRUE,
                'size'  => '14',
                'name'  => $font_name
        ));
       
        $object->getActiveSheet()->getStyle('A3:X3')->applyFromArray($styleArray_head);

        //set bg header color
        $object->getActiveSheet()->getStyle('A3:X3')->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('c5d9f1');
        //set border header
        $styleArray=array(
            'borders'=>array(
                'allborders'=>array(
                    'style'=>PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $object->getActiveSheet()->getStyle('A3:X3')->applyFromArray($styleArray);
        $object->getActiveSheet()->getDefaultRowDimension()->setRowHeight(23);
        
        $irow = 4;
        $start_irow = 4;
        $SETFONT_B = array();
        while($data = $db_dpis1->get_array()){
            $TEMP_CARD_NO_PERSON = $data[CARD_NO_PERSON];
            $TEMP_FULL_NAME = $data[FULL_NAME];
            $TEMP_POH_LAST_POSITION = ($data[POH_LAST_POSITION]=='N')?'ไม่ใช่':'ใช่';
            $TEMP_POH_POS_NO = $data[POH_POS_NO];
            if($data[POH_EFFECTIVEDATE]){
                $ARR_EFF = explode("-",$data[POH_EFFECTIVEDATE]);
                $TEMP_POH_EFFECTIVEDATE = $ARR_EFF[2].'/'.$ARR_EFF[1].'/'.($ARR_EFF[0]+543);
            }else{
                $TEMP_POH_EFFECTIVEDATE = '';
            }
            $TEMP_MOV_NAME = $data[MOV_NAME];
            $TEMP_POH_SALARY = ($data[POH_SALARY]!='0')?$data[POH_SALARY]:'';
            $TEMP_POH_SALARY_POS = ($data[POH_SALARY_POS]!='0')?$data[POH_SALARY_POS]:'';
            $TEMP_POH_DOCNO = $data[POH_DOCNO];
            if($data[POH_DOCDATE]){
                $ARR_EFF2 = explode("-",$data[POH_DOCDATE]);
                $TEMP_POH_DOCDATE = $ARR_EFF2[2].'/'.$ARR_EFF2[1].'/'.($ARR_EFF2[0]+543);
            }else{
                $TEMP_POH_DOCDATE = '';
            }
            $TEMP_POH_SEQ_NO = $data[POH_SEQ_NO];
            $TEMP_PRINT_ON_REPORT = $data[PRINT_ON_REPORT];
            $TEMP_PL_NAME = $data[PL_NAME];
            $TEMP_POH_PM_NAME = $data[POH_PM_NAME];
            $TEMP_PT_NAME = $data[PT_NAME];
            $TEMP_POH_LEVEL_NO = $data[POH_LEVEL_NO];
            $TEMP_ORG_NAME_1 = $data[ORG_NAME_1];
            $TEMP_ORG_NAME_2 = $data[ORG_NAME_2];
            $TEMP_ORG_NAME_3 = $data[ORG_NAME_3];
            $TEMP_POH_UNDER_ORG1 = $data[POH_UNDER_ORG1];
            $TEMP_POH_UNDER_ORG2 = $data[POH_UNDER_ORG2];
            $TEMP_POH_PL_NAME = $data[POH_PL_NAME];
            $TEMP_POH_ORG = $data[POH_ORG];
            $TEMP_POH_REMARK = $data[POH_REMARK];
            $data_count++;

            $object->getActiveSheet()->setCellValueExplicit('A'.$irow,@iconv("tis-620", "utf-8", "$TEMP_CARD_NO_PERSON"), PHPExcel_Cell_DataType::TYPE_STRING);
            $object->getActiveSheet()->setCellValue('B'.$irow,@iconv("tis-620", "utf-8", "$TEMP_FULL_NAME"));
            $object->getActiveSheet()->setCellValue('C'.$irow,@iconv("tis-620", "utf-8", "$TEMP_POH_LAST_POSITION"));
            $object->getActiveSheet()->setCellValue('D'.$irow,@iconv("tis-620", "utf-8", "$TEMP_POH_POS_NO"));
            $object->getActiveSheet()->setCellValueExplicit('E'.$irow,@iconv("tis-620", "utf-8", "$TEMP_POH_EFFECTIVEDATE"), PHPExcel_Cell_DataType::TYPE_STRING);
            $object->getActiveSheet()->setCellValue('F'.$irow,@iconv("tis-620", "utf-8", "$TEMP_MOV_NAME"));
            $object->getActiveSheet()->setCellValue('G'.$irow,@iconv("tis-620", "utf-8", "$TEMP_POH_SALARY"));            
            $object->getActiveSheet()->setCellValue('H'.$irow,@iconv("tis-620", "utf-8", "$TEMP_POH_SALARY_POS"));
            $object->getActiveSheet()->setCellValue('I'.$irow,@iconv("tis-620", "utf-8", "$TEMP_POH_DOCNO"));
            $object->getActiveSheet()->setCellValue('J'.$irow,@iconv("tis-620", "utf-8", "$TEMP_POH_DOCDATE"));
            $object->getActiveSheet()->setCellValue('K'.$irow,@iconv("tis-620", "utf-8", "$TEMP_POH_SEQ_NO"));
            $object->getActiveSheet()->setCellValue('L'.$irow,@iconv("tis-620", "utf-8", "$TEMP_PRINT_ON_REPORT"));
            $object->getActiveSheet()->setCellValue('M'.$irow,@iconv("tis-620", "utf-8", "$TEMP_PL_NAME"));
            $object->getActiveSheet()->setCellValue('N'.$irow,@iconv("tis-620", "utf-8", "$TEMP_POH_PM_NAME"));
            $object->getActiveSheet()->setCellValue('O'.$irow,@iconv("tis-620", "utf-8", "$TEMP_PT_NAME"));
            $object->getActiveSheet()->setCellValue('P'.$irow,@iconv("tis-620", "utf-8", "$TEMP_POH_LEVEL_NO"));
            $object->getActiveSheet()->setCellValue('Q'.$irow,@iconv("tis-620", "utf-8", "$TEMP_ORG_NAME_1"));
            $object->getActiveSheet()->setCellValue('R'.$irow,@iconv("tis-620", "utf-8", "$TEMP_ORG_NAME_2"));
            $object->getActiveSheet()->setCellValue('S'.$irow,@iconv("tis-620", "utf-8", "$TEMP_ORG_NAME_3"));
            $object->getActiveSheet()->setCellValue('T'.$irow,@iconv("tis-620", "utf-8", "$TEMP_POH_UNDER_ORG1"));
            $object->getActiveSheet()->setCellValue('U'.$irow,@iconv("tis-620", "utf-8", "$TEMP_POH_UNDER_ORG2"));
            $object->getActiveSheet()->setCellValue('V'.$irow,@iconv("tis-620", "utf-8", "$TEMP_POH_PL_NAME"));
            $object->getActiveSheet()->setCellValue('W'.$irow,@iconv("tis-620", "utf-8", "$TEMP_POH_ORG"));
            $object->getActiveSheet()->setCellValue('X'.$irow,@iconv("tis-620", "utf-8", "$TEMP_POH_REMARK"));
            $irow++;
        }
        
        $end_row = ($irow-1);
        $object->getActiveSheet()->getStyle('A'.$start_irow.':X'.$end_row)->applyFromArray($styleArray);
        $object->getActiveSheet()->getStyle('A'.$start_irow.':X'.$end_row)->applyFromArray($styleArray_body);
        $object->getActiveSheet()->getStyle('A'.$start_irow.':X'.$end_row)->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFFFF');
        $object->getActiveSheet()->getStyle('A'.$start_irow.':X'.$end_row)->getFont()->setBold(FALSE);
        
        $object->getActiveSheet()->getStyle('A'.$start_irow.':A'.$end_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $object->getActiveSheet()->getStyle('D'.$start_irow.':D'.$end_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $object->getActiveSheet()->getStyle('E'.$start_irow.':E'.$end_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        
        $object->getActiveSheet()->getStyle('A'.$start_irow.':A'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $object->getActiveSheet()->getStyle('B'.$start_irow.':B'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $object->getActiveSheet()->getStyle('C'.$start_irow.':C'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $object->getActiveSheet()->getStyle('D'.$start_irow.':D'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $object->getActiveSheet()->getStyle('E'.$start_irow.':E'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $object->getActiveSheet()->getStyle('F'.$start_irow.':F'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $object->getActiveSheet()->getStyle('G'.$start_irow.':G'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $object->getActiveSheet()->getStyle('H'.$start_irow.':H'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $object->getActiveSheet()->getStyle('I'.$start_irow.':I'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $object->getActiveSheet()->getStyle('J'.$start_irow.':J'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $object->getActiveSheet()->getStyle('K'.$start_irow.':K'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->getActiveSheet()->getStyle('L'.$start_irow.':L'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->getActiveSheet()->getStyle('M'.$start_irow.':M'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $object->getActiveSheet()->getStyle('N'.$start_irow.':N'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $object->getActiveSheet()->getStyle('O'.$start_irow.':O'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $object->getActiveSheet()->getStyle('P'.$start_irow.':P'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $object->getActiveSheet()->getStyle('Q'.$start_irow.':Q'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $object->getActiveSheet()->getStyle('R'.$start_irow.':R'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $object->getActiveSheet()->getStyle('S'.$start_irow.':S'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $object->getActiveSheet()->getStyle('T'.$start_irow.':T'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $object->getActiveSheet()->getStyle('U'.$start_irow.':U'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $object->getActiveSheet()->getStyle('V'.$start_irow.':V'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $object->getActiveSheet()->getStyle('W'.$start_irow.':W'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $object->getActiveSheet()->getStyle('X'.$start_irow.':X'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    }else{
        //ไม่มีข้อมูล
        $xlsRow++;
        $object->getActiveSheet()->getStyle('A1:B2')->applyFromArray($styleArray_head);
        $object->setActiveSheetIndex(0)->mergeCells('A1:B1')->setCellValue('A1', iconv('TIS-620','UTF-8','**** ไม่พบข้อมูล ****'))->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    }
    //ob_clean();
    $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$report_code.'.xlsx"');
    $object_writer->save('php://output');