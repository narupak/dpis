<?php
    include("../../php_scripts/connect_database.php");
    include("../../php_scripts/calendar_data.php");
    include ("../php_scripts/function_share.php"); 

    include ("../report/rpt_function.php");

    ini_set("max_execution_time", 0);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
    
    $db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    
    function num2wordsThai($num){     
		$num=str_replace(",","",$num);  
		$num_decimal=explode(".",$num);  
		$num=$num_decimal[0];  
		$returnNumWord;     
		$lenNumber=strlen($num);     
		$lenNumber2=$lenNumber-1;     
		$kaGroup=array("","สิบ","ร้อย","พัน","หมื่น","แสน","ล้าน","สิบ","ร้อย","พัน","หมื่น","แสน","ล้าน");     
		$kaDigit=array("","หนึ่ง","สอง","สาม","สี่","ห้า","หก","เจ็ด","แปด","เก้า");     
		$kaDigitDecimal=array("ศูนย์","หนึ่ง","สอง","สาม","สี่","ห้า","หก","เจ็ด","แปด","เก้า");     
		$ii=0;     
		for($i=$lenNumber2;$i>=0;$i--){     
			$kaNumWord[$i]=substr($num,$ii,1);     
			$ii++;     
		}     
		$ii=0;     
		for($i=$lenNumber2;$i>=0;$i--){     
			if(($kaNumWord[$i]==2 && $i==1) || ($kaNumWord[$i]==2 && $i==7)){     
				$kaDigit[$kaNumWord[$i]]="ยี่";     
			}else{     
				if($kaNumWord[$i]==2){     
					$kaDigit[$kaNumWord[$i]]="สอง";          
				}     
				if(($kaNumWord[$i]==1 && $i<=2 && $i==0) || ($kaNumWord[$i]==1 && $lenNumber>6 && $i==6)){     
					if($kaNumWord[$i+1]==0){     
						$kaDigit[$kaNumWord[$i]]="หนึ่ง";        
					}else{     
						$kaDigit[$kaNumWord[$i]]="เอ็ด";         
					}     
				}elseif(($kaNumWord[$i]==1 && $i<=2 && $i==1) || ($kaNumWord[$i]==1 && $lenNumber>6 && $i==7)){     
					$kaDigit[$kaNumWord[$i]]="";     
				}else{     
					if($kaNumWord[$i]==1){     
						$kaDigit[$kaNumWord[$i]]="หนึ่ง";     
					}     
				}     
			}     
			if($kaNumWord[$i]==0){     
				if($i!=6){  
					$kaGroup[$i]="";     
				}  
			}     
			$kaNumWord[$i]=substr($num,$ii,1);     
			$ii++;     
			$returnNumWord.=$kaDigit[$kaNumWord[$i]].$kaGroup[$i];     
		}        
		if(isset($num_decimal[1])){  
			$returnNumWord.="จุด";  
			for($i=0;$i<strlen($num_decimal[1]);$i++){  
					$returnNumWord.=$kaDigitDecimal[substr($num_decimal[1],$i,1)];    
			}  
		}         
		return $returnNumWord;     
	} 
    
	
    
    define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
    //date_default_timezone_set('Asia/Bangkok');
    require_once '../../Excel/eslip/Classes/PHPExcel/IOFactory.php';
	
    
    $objReader = PHPExcel_IOFactory::createReader('Excel5');
	
	
	if($NUMBER_DISPLAY==2){
		$objPHPExcel = $objReader->load("../../Excel/eslip/templates/R1211_templateTH.xls");
	}else{
		
		$objPHPExcel = $objReader->load("../../Excel/eslip/templates/R1211_template.xls");

	}
	
	
	
	include ("es_font_size.php");
	$objPHPExcel->getActiveSheet()->getStyle('A1:AM7')->getFont()->setName(getToFont($CH_PRINT_FONT));
    $objPHPExcel->getActiveSheet()->getStyle('A1:AM7')->getFont()->setSize($CH_PRINT_SIZE-4);
	
	if($search_org_ass_id_1){
		//หาหน่วยงาน
		 if($select_org_structure==0){ // แบบตามกฏหมาย
			 // เดิม $varORGNAME= $search_org_name." ";
			 // เดิม $varorg_id= $search_org_id." ";
              $varORGNAME= $search_org_ass_name." ".$search_org_ass_name_1." ";
			  $varorg_id= $search_org_ass_id;
			  $varorg_id_1= $search_org_ass_id_1;
		 }else{ //แบบมอบหมายงาน
			  $varORGNAME= $search_org_ass_name." ".$search_org_ass_name_1." ";
			  $varorg_id= $search_org_ass_id;
			  $varorg_id_1= $search_org_ass_id_1;
		 }
		 
		$CHKsearch_org_id=$DEPARTMENT_ID;
		$CHKDEPARTMENT_ID=$varorg_id;
		$CHKORG_LOWER1=$varorg_id_1;
		$CHKORG_LOWER2=-1;
		$CHKORG_LOWER3=-1;
		
		//echo "1<br>";
		
	}else if($search_org_ass_id){
		//หาหน่วยงาน
		 if($select_org_structure==0){ // แบบตามกฏหมาย
			 // เดิม $varORGNAME= $search_org_name." ";
			 // เดิม $varorg_id= $search_org_id." ";
               $varORGNAME= $search_org_ass_name." ";
			  $varorg_id= $search_org_ass_id;
		 }else{ //แบบมอบหมายงาน
			  $varORGNAME= $search_org_ass_name." ";
			  $varorg_id= $search_org_ass_id;
		 }
		 
		$CHKsearch_org_id=$DEPARTMENT_ID;
		$CHKDEPARTMENT_ID=$varorg_id;
		$CHKORG_LOWER1=-1;
		$CHKORG_LOWER2=-1;
		$CHKORG_LOWER3=-1;
		
		//echo "1<br>";
		
	}else{
		//echo "2<br>";
		$CHKsearch_org_id=$DEPARTMENT_ID;
		$CHKDEPARTMENT_ID=-1;
		$CHKORG_LOWER1=-1;
		$CHKORG_LOWER2=-1;
		$CHKORG_LOWER3=-1;
		$varORGNAME= "";
		
	}
    
    $org_structure="โครงสร้างตามกฎหมาย";
    if($select_org_structure==1){$org_structure="โครงสร้างตามมอบหมายงาน";}
    $company_name ="รูปแบบการออกรายงาน : ".$org_structure." ".$varORGNAME;

    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', iconv('TIS-620','UTF-8','หลักฐานการเบิกจ่ายเงินตอบแทนการปฏิบัติงานนอกเวลา'))
            ->setCellValue('A2', iconv('TIS-620','UTF-8','ส่วนราชการ'.$varORGNAME.$DEPARTMENT_NAME.' ประจำเดือน '.$month_full[($search_month + 0)][TH].' พ.ศ. '.(($NUMBER_DISPLAY==2)?convert2thaidigit($search_yearBgn):$search_yearBgn) ))
            ->setCellValue('A3', iconv('TIS-620','UTF-8',''));

    $Arrsearch_date = explode("/", $search_date);
    
    $varORGID= $search_org_id;
    $varBgnDataEat= ($Arrsearch_date[2]-543).'-'.$Arrsearch_date[1].'-'.$Arrsearch_date[0].' 00:00:00';
    $varToDateEat= ($Arrsearch_date[2]-543).'-'.$Arrsearch_date[1].'-'.$Arrsearch_date[0].' 23:59:59';
    $varPerType= $search_per_type;
    $varOrgStructure = 'PER_ORG';
   
    if($select_org_structure==1){$varOrgStructure = 'PER_ORG_ASS';}
    
    
    //หาช่วงวันที่ 15 / 30
    if(strlen($search_month)==1){
        $search_month = '0'.$search_month;
    }
    
    //$search_year=($search_year);
    $mk_data=mktime(0, 0, 0, substr("0".$search_month,-2), '01', ($search_yearBgn-543));
	$start = date("Y-m-d", $mk_data);

	$bgnbackMonth= ($search_yearBgn-543)."-".substr("0".$search_month,-2)."-01";
	$end=($search_yearBgn-543)."-".substr("0".$search_month,-2)."-".date("t",strtotime($bgnbackMonth));
	
    
    $cmdBtw="SELECT to_char( OT_DATE,'YYYY-MM-DD') AS OT_DATE,
					CASe WHEN ph.hol_date is not null THEN 1 ELSE 
			 		case when TO_CHAR(OT_DATE, 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') in ('SAT', 'SUN') then 1 else 0 end
			 		END AS HOLDAY
					FROM (
						SELECT (TO_DATE('$start', 'YYYY-MM-DD'))-1+rownum AS OT_DATE FROM all_objects
						WHERE (TO_DATE('$start', 'YYYY-MM-DD'))-1+ rownum <= last_day(TO_DATE('$start', 'YYYY-MM-DD'))
					  ) tbmain 
					  left join per_holiday ph on(SUBSTR(ph.hol_date,1,10)=to_char( tbmain.OT_DATE,'YYYY-MM-DD'))
					  ";
    $cntDay = $db_dpis2->send_cmd($cmdBtw);
    $workDateIN = "";
    $comma=",";
    $iloop = 0;

    
    $ArrHol = array();
	$IsThereValue = array();
    while ($dataDAY = $db_dpis2->get_array_array()) {
		$ArrHol[$iloop]=$dataDAY[HOLDAY];
        $iloop++;
        if($iloop==$cntDay){$comma='';}
        $workDateIN .= "'".$dataDAY[OT_DATE]."'".$comma;
        if($comma==''){break;}
    }
    $ArrworkDateIN = explode(",", $workDateIN);
	$NumberOfColumnUse = count($ArrworkDateIN);
    //die($workDateIN);
    
    $cmdMain ="select (pn.pn_name||p.per_name||' '||p.per_surname) name
						,(select sum(num_hrs) from ta_per_ot t where  t.SET_FLAG=1  and t.per_id=d.per_id and t.CONTROL_ID=d.CONTROL_ID and HOLYDAY_FLAG=0) hrs1
						,(select sum(num_hrs) from ta_per_ot t where t.SET_FLAG=1  and t.per_id=d.per_id and t.CONTROL_ID=d.CONTROL_ID and HOLYDAY_FLAG=1) hrs2
						,(select sum(amount) from ta_per_ot t where t.SET_FLAG=1  and t.per_id=d.per_id and t.CONTROL_ID=d.CONTROL_ID) amount
						,d.*
						from per_personal p 
						left join per_prename pn on (pn.pn_code=p.PN_CODE)
						
						left join (select * from (
						  select * from (
							select per_id, (substr(start_time,1,2)||':'||substr(start_time,3,2)||'-'||substr(end_time,1,2)||':'||substr(end_time,3,2)) ottime
							  ,ot_date,CONTROL_ID
							from ta_per_ot 
							
							where NUM_HRS > 0 
							AND ORG_ID=$CHKsearch_org_id
							AND NVL(DEPARTMENT_ID,-1)=$CHKDEPARTMENT_ID
							AND NVL(ORG_LOWER1,-1)=$CHKORG_LOWER1
							AND NVL(ORG_LOWER2,-1)=$CHKORG_LOWER2
							AND NVL(ORG_LOWER3,-1)=$CHKORG_LOWER3
							and SET_FLAG=1 
							AND CONTROL_ID in(select CONTROL_ID from TA_PER_OT_CONTROL 
								where BUDGET_YEAR=$search_yearBgn 
				     					AND PAY_MONTH=$search_month)
							
						  )
						  pivot
						  (
							max(ottime)
							for ot_date in (".$workDateIN.")
						  ) 
						))d on (d.per_id=p.per_id)
						left join PER_POSITION x on(x.POS_ID=p.POS_ID) 
                        left join PER_POS_EMP y on(y.POEM_ID=p.POEM_ID) 
                        left join PER_POS_EMPSER z on(z.POEMS_ID=p.POEMS_ID) 
						where p.per_id=d.per_id 
						order by  x.POS_NO,y.POEM_NO,z.POEMS_NO";
						//echo "<pre>\n";
  //echo "<pre>".$cmdMain;
   //die();
    $org_structure=1;
    if($select_org_structure==1){$org_structure=2;}
    $count_page_data = $db_dpis2->send_cmd($cmdMain);
    //die("<pre>$cmdMain");
    $ArrCol = array('C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG');
    $no=0;
    $TOTALHRS1 = 0;
    $TOTALHRS2 = 0;
    $TOTALAMOUNT = 0;
    $baseRow = 7;
    $r=0;
    while ($data = $db_dpis2->get_array_array()) {
        $no++;
        $row = $baseRow + $r;
        
        $objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit($no):$no) ))
                ->setCellValue('B'.$row, iconv('TIS-620','UTF-8',$data[NAME]));
        for($idx=0;$idx<count($ArrworkDateIN);$idx++){
            $dataAbs = $data[$ArrworkDateIN[$idx]];
            $dataAbs = $dataAbs<=0 ? '':$dataAbs;
            $objPHPExcel->getActiveSheet()->setCellValue($ArrCol[$idx].$row, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit($dataAbs):$dataAbs) ));
        }
        $TOTALHRS1 = $TOTALHRS1 + $data[HRS1];
       $TOTALHRS2 = $TOTALHRS2 + $data[HRS2];
       $TOTALAMOUNT = $TOTALAMOUNT + $data[AMOUNT];
       $objPHPExcel->getActiveSheet()->setCellValue('AH'.$row, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit($data[HRS1]):$data[HRS1]) ))
                ->setCellValue('AI'.$row, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit($data[HRS2]):$data[HRS2]) ))
               ->setCellValue('AJ'.$row, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit($data[AMOUNT]):$data[AMOUNT])  ))
               ->setCellValue('AK'.$row, iconv('TIS-620','UTF-8',''))
               ->setCellValue('AL'.$row, iconv('TIS-620','UTF-8',''))
               ->setCellValue('AM'.$row, iconv('TIS-620','UTF-8',''));
       $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':AM'.$row)->getFill()->getStartColor()->setRGB('FFFFFF');
         $objPHPExcel->getActiveSheet()->getStyle('B'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $r++;
    }
    $row = $baseRow + $r;
    $objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
    $objPHPExcel->getActiveSheet()->setCellValue('AH'.$row, iconv('TIS-620','UTF-8',$TOTALHRS1<=0 ? '':(($NUMBER_DISPLAY==2)?convert2thaidigit($TOTALHRS1):$TOTALHRS1)  ));
    $objPHPExcel->getActiveSheet()->setCellValue('AI'.$row, iconv('TIS-620','UTF-8',$TOTALHRS2<=0 ? '':(($NUMBER_DISPLAY==2)?convert2thaidigit($TOTALHRS2):$TOTALHRS2)  ));
    $objPHPExcel->getActiveSheet()->setCellValue('AJ'.$row, iconv('TIS-620','UTF-8',$TOTALAMOUNT<=0 ? '':(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($TOTALAMOUNT)):number_format($TOTALAMOUNT))  ));
    
	$objPHPExcel->getActiveSheet()->removeRow($row+1);
	
    $row=$row+3;
    $objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, iconv('TIS-620','UTF-8','รวมจ่ายเงินทั้งสิ้น '.num2wordsThai($TOTALAMOUNT).' บาทถ้วน'));
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setName(getToFont($CH_PRINT_FONT));
    $objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize($CH_PRINT_SIZE-4);
	$row=$row+1;
    $objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, iconv('TIS-620','UTF-8','ขอรับรองว่า ผู้มีรายชื่อข้างต้นปฏิบัติงานนอกเวลาจริง'));
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setName(getToFont($CH_PRINT_FONT));
    $objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize($CH_PRINT_SIZE-4);

	function getPIC_SIGN($PER_ID,$PER_CARDNO){
		global $db_dpis , $db_dpis2;
	
		$PIC_SIGN="";
		//หารูปที่เป็นลายเซ็น
		$cmd = "	select 		*
									  from 		PER_PERSONALPIC
									  where 		PER_ID=$PER_ID and PER_CARDNO = '$PER_CARDNO' and  PIC_SIGN=1
									  order by 	PER_PICSEQ ";
									  
		$count_pic_sign=$db_dpis->send_cmd($cmd);
		if($count_pic_sign>0){	
		$data = $db_dpis->get_array_array();
		$TMP_PIC_SEQ = $data[PER_PICSEQ];
		$current_list .= ((trim($current_list))?",":"") . $TMP_PIC_SEQ;
		$T_PIC_SEQ = substr("000",0,3-strlen("$TMP_PIC_SEQ"))."$TMP_PIC_SEQ";
		$TMP_SERVER = $data[PIC_SERVER_ID];
		$TMP_PIC_SIGN= $data[PIC_SIGN];
		
		if ($TMP_SERVER) {
			$cmd1 = " SELECT * FROM OTH_SERVER WHERE SERVER_ID=$TMP_SERVER ";
			$db_dpis2->send_cmd($cmd1);
			$data2 = $db_dpis2->get_array_array();
			$tmp_SERVER_NAME = trim($data2[SERVER_NAME]);
			$tmp_ftp_server = trim($data2[FTP_SERVER]);
			$tmp_ftp_username = trim($data2[FTP_USERNAME]);
			$tmp_ftp_password = trim($data2[FTP_PASSWORD]);
			$tmp_main_path = trim($data2[MAIN_PATH]);
			$tmp_http_server = trim($data2[HTTP_SERVER]);
		} else {
			$TMP_SERVER = 0;
			$tmp_SERVER_NAME = "";
			$tmp_ftp_server = "";
			$tmp_ftp_username = "";
			$tmp_ftp_password = "";
			$tmp_main_path = "";
			$tmp_http_server = "";
		}
		$SIGN_NAME = "";
		if($TMP_PIC_SIGN==1){ $SIGN_NAME = "SIGN"; }
		if (trim($PER_CARDNO) && trim($PER_CARDNO) != "NULL") {
			$TMP_PIC_NAME = $data[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg".($tmp_SERVER_NAME ? " [".$tmp_SERVER_NAME."]" : "");
			//$TMP_SHOW_PIC = ($tmp_http_server ? $tmp_http_server."/" : "").$data[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg";
		} else {
			$TMP_PIC_NAME = $data[PER_PICPATH].$data[PER_GENNAME]."-".$SIGN_NAME.$T_PIC_SEQ.".jpg".($tmp_SERVER_NAME ? " [".$tmp_SERVER_NAME."]" : "");
			//$TMP_SHOW_PIC = ($tmp_http_server ? $tmp_http_server."/" : "").$data[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg";
		}
		if(file_exists("../".$TMP_PIC_NAME)){
			$PIC_SIGN = "../".$TMP_PIC_NAME;		//	if($PER_CARDNO && $TMP_PIC_NAME)		$PIC_SIGN = "../../attachments/".$PER_CARDNO."/PER_SIGN/".$TMP_PIC_NAME;
		}
		} //end count	
	return $PIC_SIGN;
	}
	

	
	$cmd = " select g.PN_NAME||a.PER_NAME||' '||a.PER_SURNAME  AS APROVE_FULLNAME_SHOW ,
				h.PN_NAME||b.PER_NAME||' '||b.PER_SURNAME  AS ALLOW_FULLNAME_SHOW ,
				a.PER_CARDNO
				from TA_PER_OT_CONTROL con 
				left join PER_PERSONAL a on(a.PER_ID=con.APPROVE_USER)
				left join PER_PERSONAL b on(b.PER_ID=con.ALLOW_USER)
				left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE)
				left join PER_PRENAME h on(h.PN_CODE=b.PN_CODE)
				where con.BUDGET_YEAR=$search_yearBgn AND con.PAY_MONTH=$search_month ";
	//echo $cmd ; die();
	$db_dpis->send_cmd($cmd);
	$data_dpis2 = $db_dpis->get_array_array();
	
	$APROVE_FULLNAME_SHOW = "                                  ";
	$SpaceName = "                                          ";
	if(trim($data_dpis2[APROVE_FULLNAME_SHOW])){
		$APROVE_FULLNAME_SHOW = trim($data_dpis2[APROVE_FULLNAME_SHOW]);
		$SpaceName = "                                                    ";
	}
	
	$ALLOW_FULLNAME_SHOW = "                                  ";
	if(trim($data_dpis2[ALLOW_FULLNAME_SHOW])){
		$ALLOW_FULLNAME_SHOW = trim($data_dpis2[ALLOW_FULLNAME_SHOW]);
	}
	
	$APPROVE_CARDNO = $dataAP[PER_CARDNO];
	$PIC_SIGN_APPROVE = getPIC_SIGN($APPROVE_PER_ID,$APPROVE_CARDNO);


     $row=$row+1;
     $objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
     $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, iconv('TIS-620','UTF-8','ชื่อ                                   ผู้รับรองการปฏิบัติงาน     ลายมือชื่อ                                   ผู้จ่ายเงิน'));
     $objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setName(getToFont($CH_PRINT_FONT));
     $objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize($CH_PRINT_SIZE-4);
	 $row=$row+1;
     $objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
     $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, iconv('TIS-620','UTF-8','    ('.$ALLOW_FULLNAME_SHOW.')'.$SpaceName.'('.$APROVE_FULLNAME_SHOW.')' ));
	 $objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setName(getToFont($CH_PRINT_FONT));
     $objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize($CH_PRINT_SIZE-4);
    

   $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':AM'.$row)->getFill()->getStartColor()->setRGB('FFFFFF');
   $objPHPExcel->getActiveSheet()->getStyle('B'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="R1211.xls"');
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

     
    /**********************************************************/
    //die('<pre>'.$cmd);
    $db_dpis2->send_cmd($cmd);
    
    $CurPerId=-1;
    $no=1;    
    $sumAB_CODE_04=0;
    $sumAB_CODE_01=0;
    $sumAB_CODE_03=0;
    $sumAB_CODE_99=0;
    $sumAB_CODE_10=0;
    
    $CntChk = 0;
    $diffY= ($search_yearEnd-$search_yearBgn)+1;
    while ($data = $db_dpis2->get_array_array()) {
        
        $r++;
        }
    

?>
