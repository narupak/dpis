<?php
    include("../../php_scripts/connect_database.php");
    include("../../php_scripts/calendar_data.php");
    include ("../php_scripts/function_share.php");

    include ("../report/rpt_function.php");
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

//    require_once "../../Excel/class.writeexcel_workbook.inc.php";
//    require_once "../../Excel/class.writeexcel_worksheet.inc.php";

    ini_set("max_execution_time", 0);
    
    $db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    
    function getNamePersonal($db_dpis,$perId){
        $cmd = "SELECT PP.PN_NAME,PPN.PER_NAME,PPN.PER_SURNAME 
                FROM PER_PERSONAL PPN
                LEFT JOIN PER_PRENAME PP ON(PP.PN_CODE=PPN.PN_CODE)
                WHERE PER_ID = $perId ";
        $db_dpis->send_cmd($cmd);
        $data2 = $db_dpis->get_array();
        $fullname = $data2[PN_NAME].$data2[PER_NAME].' '.$data2[PER_SURNAME];
        return $fullname;
    }
    define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
    //date_default_timezone_set('Asia/Bangkok');
    require_once '../../Excel/eslip/Classes/PHPExcel/IOFactory.php';
    
    $objReader = PHPExcel_IOFactory::createReader('Excel5');
    $objPHPExcel = $objReader->load("../../Excel/eslip/templates/R1201_template.xls");
    
    $Arrsearch_date = explode("/", $search_date);
    $varORGID= $search_org_id;
    $varBgnDataEat= ($Arrsearch_date[2]-543).'-'.$Arrsearch_date[1].'-'.$Arrsearch_date[0].' 00:00:00';
    $varToDateEat= ($Arrsearch_date[2]-543).'-'.$Arrsearch_date[1].'-'.$Arrsearch_date[0].' 23:59:59';
    $varPerType= $search_per_type;
    $varOrgStructure = '1';
    if($select_org_structure==1){$varOrgStructure = '2';}
    
	$cmd_con = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_SCANTYPE' ";
	$db_dpis->send_cmd($cmd_con);
	$data_con = $db_dpis->get_array();
	$SCANTYPE = $data_con[CONFIG_VALUE];
	
    $cmd = file_get_contents('../GetWorkTimeByOrgID.sql');	
    $cmd=str_ireplace(":ORG_ID",$varORGID,$cmd);
    $cmd=str_ireplace(":BEGINDATEAT","'".$varBgnDataEat."'",$cmd);
    $cmd=str_ireplace(":TODATEAT","'".$varToDateEat."'",$cmd);
    $cmd=str_ireplace(":LAW_OR_ASS",$varOrgStructure,$cmd);
    $cmd=str_ireplace(":PER_TYPE",$varPerType,$cmd);
	$cmd=str_ireplace(":SCANTYPE",$SCANTYPE,$cmd);
    
    $db_dpis2->send_cmd($cmd);
    
    $arrHdDate = explode("/", $search_date);
    $show_date = "�ѹ��� ".($arrHdDate[0] + 0) ." ��͹ ". $month_full[($arrHdDate[1] + 0)][TH] ." �.�. ". $arrHdDate[2];
    if($select_org_structure==0){ // Ẻ���������
		 $varORGID= $search_org_id;
		 $varORGNAME= $search_org_name;
	 }else{ //Ẻ�ͺ���§ҹ
		 $varORGID= $search_org_ass_id;
		  $varORGNAME= $search_org_ass_name;
	}
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', iconv('TIS-620','UTF-8','�ѭ��ŧ���һ�Ժѵ��Ҫ��âͧ'.$PERSON_TYPE[$search_per_type]))
            ->setCellValue('A2', iconv('TIS-620','UTF-8',$show_date))
            ->setCellValue('A3', iconv('TIS-620','UTF-8',$varORGNAME))
            ->setCellValue('A4', iconv('TIS-620','UTF-8',$DEPARTMENT_NAME));
    
    $baseRow = 7;
    $r=0;
    //die('<pre>'.$cmd);
    while ($data = $db_dpis2->get_array()) {
        $dbEntTime='-';
        if(!empty($data[ENTTIME])){
            $dbEntTime=$data[ENTTIME];
        }
        $dbExitTime='-';
        if(!empty($data[EXITTIME])){
            $dbExitTime=$data[EXITTIME];
        }

        $dbAbsent = $data[ABSENT];
        $dbWorkFlag = $data[WORK_FLAG];
        $Comment = "";
        if($dbWorkFlag==0){$Comment = "�һ�Ժѵ��Ҫ���";$cntbWorkFlag_0++;}
        if($dbWorkFlag==1){$Comment = "�����";$cntbWorkFlag_1++;}
        if($dbWorkFlag==2){$Comment = "����һ�Ժѵ��Ҫ���";$cntbWorkFlag_2++;}
        if($dbWorkFlag==3){$Comment = "�͡��͹����";}
        if($dbWorkFlag==4){$Comment = "�����ŧ����";}

        $row = $baseRow + $r;
        
        $objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $r+1)
                                       ->setCellValue('B'.$row, iconv('TIS-620','UTF-8',getNamePersonal($db_dpis,$data[PER_ID])))
                                       ->setCellValue('C'.$row, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit($dbEntTime):$dbEntTime) ))
                                       ->setCellValue('D'.$row, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit($dbExitTime):$dbExitTime) ))
                                       ->setCellValue('E'.$row, iconv('TIS-620','UTF-8',$Comment));
        $objPHPExcel->getActiveSheet()
                    ->getStyle('B'.$row)
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()
                    ->getStyle('E'.$row)
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        
        $objPHPExcel->getActiveSheet()
                    ->getStyle('A'.$row.':E'.$row)
                    ->getFill()
                    ->getStartColor()
                    ->setRGB('FFFFFF');
        $r++;
        
    }
    
    $NextRow=$r+6;
    
    if($cntbWorkFlag_0==0){$cntbWorkFlag_0='-';}
    if($cntbWorkFlag_1==0){$cntbWorkFlag_1='-';}
    if($cntbWorkFlag_2==0){$cntbWorkFlag_2='-';}
    $objPHPExcel->getActiveSheet()->setCellValue('E'.($NextRow+1), '')
                                ->setCellValue('E'.($NextRow+2), iconv('TIS-620','xxx'))
                                ->setCellValue('E'.($NextRow+3), iconv('TIS-620','UTF-8','���˹���ҧ     -   ��'))
                                ->setCellValue('E'.($NextRow+4), iconv('TIS-620','UTF-8','�׹����Ҫ����Ҫ���       -   ��'))
                                ->setCellValue('E'.($NextRow+5), iconv('TIS-620','UTF-8','�һ�Ժѵ��Ҫ���      '.$cntbWorkFlag_0.'  ��'))
                                ->setCellValue('E'.($NextRow+6), iconv('TIS-620','UTF-8','��Ҫ���      -   ��'))
                                ->setCellValue('E'.($NextRow+7), iconv('TIS-620','UTF-8','�����     '.$cntbWorkFlag_1.'  ��'))
                                ->setCellValue('E'.($NextRow+8), iconv('TIS-620','UTF-8','����һ�Ժѵ��Ҫ���        '.$cntbWorkFlag_2.'  ��'))
                                ->setCellValue('D'.($NextRow+12), iconv('TIS-620','UTF-8','����Ǩ (.......................................................)'));
    $objPHPExcel->getActiveSheet()
                    ->getStyle('E'.($NextRow+1).':E'.($NextRow+8))
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    
    
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="��§ҹ�ѭ��ŧ���һ�Ժѵ��Ҫ��âͧ'.$PERSON_TYPE[$search_per_type].'.xls"');
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
