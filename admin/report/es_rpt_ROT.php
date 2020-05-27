<?php include("../../php_scripts/connect_database.php");
    include("../php_scripts/pdf_wordarray_thaicut.php");
    include("../../php_scripts/calendar_data.php");
    define('FPDF_FONTPATH','../../PDF/font/');
    include ("../../PDF/es_fpdf.php");
    include ("../../PDF/es_pdf_extends_DPIS.php");
	include ("../../PDF/fpdi.php");
    
    ini_set("max_execution_time", $max_execution_time);
    $db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_ESIGN_OT' ";
	$db_dpis2->send_cmd($cmd);
	$data = $db_dpis2->get_array();
	$P_ESIGN_ATT = $data[CONFIG_VALUE];
	
	
	// วันที่พิมพ์
	$MONTH_TH[] = "เดือน";
	$MONTH_TH[] = "ม.ค.";		$MONTH_TH[] = "ก.พ.";
	$MONTH_TH[] = "มี.ค.";		$MONTH_TH[] = "เม.ย.";
	$MONTH_TH[] = "พ.ค";		$MONTH_TH[] = "มิ.ย.";
	$MONTH_TH[] = "ก.ค.";		$MONTH_TH[] = "ส.ค.";
	$MONTH_TH[] = "ก.ย.";		$MONTH_TH[] = "ต.ค.";
	$MONTH_TH[] = "พ.ย";		$MONTH_TH[] = "ธ.ค.";



	$today = getdate(); 
	$year = $today['year'];
	$year = $year + 543; 
	$month = $MONTH_TH[$today['mon']]; 

	$mday = $today['mday'];
	$time = date('H:i:s');
	$mday = (($NUMBER_DISPLAY==2)?convert2thaidigit($mday):$mday);
	$year = (($NUMBER_DISPLAY==2)?convert2thaidigit($year):$year);
	$time = (($NUMBER_DISPLAY==2)?convert2thaidigit($time):$time);
			
			
	///////////////////////////////
	$cmd = " select FULLNAME
						from USER_DETAIL
						where ID=$SESS_USERID ";
				$db_dpis->send_cmd($cmd);
				$data_dpis2 = $db_dpis->get_array();
				$PRINT_NAME_SHOW = trim($data_dpis2[FULLNAME]);
	
	//////////////////////////////
	
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
		$data = $db_dpis->get_array();
		$TMP_PIC_SEQ = $data[PER_PICSEQ];
		$current_list .= ((trim($current_list))?",":"") . $TMP_PIC_SEQ;
		$T_PIC_SEQ = substr("000",0,3-strlen("$TMP_PIC_SEQ"))."$TMP_PIC_SEQ";
		$TMP_SERVER = $data[PIC_SERVER_ID];
		$TMP_PIC_SIGN= $data[PIC_SIGN];
		
		if ($TMP_SERVER) {
			$cmd1 = " SELECT * FROM OTH_SERVER WHERE SERVER_ID=$TMP_SERVER ";
			$db_dpis2->send_cmd($cmd1);
			$data2 = $db_dpis2->get_array();
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

    $unit="mm";
    $paper_size="A4";
    $lang_code="TH";
    $orientation='L';
	
	$CHKsearch_org_id=$_GET['search_org_id'];
	$search_yearBgn=$_GET['search_yearBgn'];
	$search_month=$_GET['search_month'];
	$CHKDEPARTMENT_ID=$_GET['DEPARTMENT_ID'];
	$CHKORG_LOWER1=$_GET['ORG_LOWER1'];
	$CHKORG_LOWER2=$_GET['ORG_LOWER2'];
	$CHKORG_LOWER3=$_GET['ORG_LOWER3'];
	$CONTROL_ID=$_GET['CONTROL_ID'];
	
	if($CHKORG_LOWER1 != "-1"){
		if($P_OTTYPE_ORGANIZE==2){	
			$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$CHKORG_LOWER1 ";
		}else{
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$CHKORG_LOWER1 ";
		}
		
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$CHKORG_LOWER1_NAME = trim($data_dpis2[ORG_NAME])." ";
	}
	
	if($CHKDEPARTMENT_ID != "-1"){
		if($P_OTTYPE_ORGANIZE==2){	
			$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$CHKDEPARTMENT_ID ";
		}else{
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$CHKDEPARTMENT_ID ";
		}
		
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$CHKDEPARTMENT_NAME = trim($data_dpis2[ORG_NAME])." ";
	}
	
	if($P_OTTYPE_ORGANIZE==2){	
		$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$CHKsearch_org_id ";
	}else{
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$CHKsearch_org_id ";
	}
	
	$db_dpis2->send_cmd($cmd);
	$data_dpis2 = $db_dpis2->get_array();
	$CHKMINIS_NAME = trim($data_dpis2[ORG_NAME]);
	
    
    
    $report_title = "ส่วนราชการ ".$CHKORG_LOWER1_NAME. "".$CHKDEPARTMENT_NAME. "".$CHKMINIS_NAME;
	$report_title1 = "ประจำเดือน ".$month_full[($search_month + 0)][TH]." พ.ศ. ". $search_yearBgn;
    $report_code = "ROT";
    $company_name ="";
    session_cache_limiter("private");
    session_start();
 
    $pdf = new FPDI($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	$pdf->AddFont($font,'',$font.'.PHP',true);
	$pdf->setSourceFile("Template_OT.pdf");
	
	
	
	
	$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_FONT' ";
	$db_dpis->send_cmd($cmd);
	$dataFONT = $db_dpis->get_array();
	$CH_PRINT_FONT  = $dataFONT[CONFIG_VALUE];
	
	
	$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_SIZE' ";
	$db_dpis->send_cmd($cmd);
	$dataSIZE = $db_dpis->get_array();
	$CH_PRINT_SIZE = 9;



    //$pdf->Open();
//    $pdf->SetFont($font,'',$CH_PRINT_SIZE-3);
	
	$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_DISLEFT' ";
	$db_dpis->send_cmd($cmd);
	$dataDISLEFT = $db_dpis->get_array();
	//$Px_DISLEFT = $dataDISLEFT[CONFIG_VALUE];
	$Px_DISLEFT = 1;
	
	$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_DISHEAD' ";
	$db_dpis->send_cmd($cmd);
	$dataDISHEAD = $db_dpis->get_array();
	//$Px_DISHEAD = $dataDISHEAD[CONFIG_VALUE];
	$Px_DISHEAD = 1;
	
	$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_DISRIGHT' ";
	$db_dpis->send_cmd($cmd);
	$dataDISRIGHT = $db_dpis->get_array();
	//$Px_DISRIGHT = $dataDISRIGHT[CONFIG_VALUE];
	$Px_DISRIGHT = 1;
	
	$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_DISFOOT' ";
	$db_dpis->send_cmd($cmd);
	$dataDISFOOT = $db_dpis->get_array();
	//$Px_DISFOOT = $dataDISFOOT[CONFIG_VALUE];
	$Px_DISFOOT = 1;
	
    $pdf->SetMargins($Px_DISLEFT,$Px_DISHEAD,$Px_DISRIGHT);
	
	
	
	$NumPage = 0;
	
    $pdf->AliasNbPages();
	$pdf->SetFont($font,'',$CH_PRINT_SIZE);
	$NumPage++;
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	
	
	$pdf->importPage(1);
	$pdf->useTemplate(1,0,0);
    

    $pdf->AutoPageBreak = false;

	$pdf->SetXY(60, 18.2);
	$pdf->Write(0,$report_title);
	
	$pdf->SetXY(200, 18.2);
	$pdf->Write(0,$report_title1);
	
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
    while ($dataDAY = $db_dpis2->get_array()) {
		$ArrHol[$iloop]=$dataDAY[HOLDAY];
        $iloop++;
        if($iloop==$cntDay){$comma='';}
        $workDateIN .= "'".$dataDAY[OT_DATE]."'".$comma;
        if($comma==''){break;}
    }
	
 //print_r($ArrHol)."<br>";
    $ArrworkDateIN = explode(",", $workDateIN);
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
							select per_id, 
								CASE WHEN HOLYDAY_FLAG=1 THEN (substr(start_time,1,2)||':'||substr(start_time,3,2)||'-'||substr(end_time,1,2)||':'||substr(end_time,3,2)) || ',xx:xx-xx:xx'
								WHEN HOLYDAY_FLAG=0 and OT_STATUS=3 THEN (substr(START_TIME_BFW,1,2)||':'||substr(START_TIME_BFW,3,2)||'-'||substr(END_TIME_BFW,1,2)||':'||substr(END_TIME_BFW,3,2))||','||(substr(start_time,1,2)||':'||substr(start_time,3,2)||'-'||substr(end_time,1,2)||':'||substr(end_time,3,2))
								WHEN HOLYDAY_FLAG=0 and OT_STATUS=1 THEN (substr(START_TIME_BFW,1,2)||':'||substr(START_TIME_BFW,3,2)||'-'||substr(END_TIME_BFW,1,2)||':'||substr(END_TIME_BFW,3,2))||',xx:xx-xx:xx'
								ELSE
								'xx:xx-xx:xx,'||(substr(start_time,1,2)||':'||substr(start_time,3,2)||'-'||substr(end_time,1,2)||':'||substr(end_time,3,2))
								END ottime
							  ,ot_date,CONTROL_ID
							from ta_per_ot 
							
							where NUM_HRS > 0 
							AND CONTROL_ID=$CONTROL_ID
							AND ORG_ID=$CHKsearch_org_id
							AND NVL(DEPARTMENT_ID,-1)=$CHKDEPARTMENT_ID
							AND NVL(ORG_LOWER1,-1)=$CHKORG_LOWER1
							AND NVL(ORG_LOWER2,-1)=$CHKORG_LOWER2
							AND NVL(ORG_LOWER3,-1)=$CHKORG_LOWER3
							and SET_FLAG=1 
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
						order by  x.POS_NO,y.POEM_NO,z.POEMS_NO ";
   //echo "<pre>\n";
   //echo "<pre>".$cmdMain;
   //die();
    $org_structure=1;
    if($select_org_structure==1){$org_structure=2;}
    $count_page_data = $db_dpis2->send_cmd($cmdMain);
    //die("<pre>$cmdMain");
    $no=0;
	
	$TOTALHRS1 = 0;
	$TOTALHRS2 = 0;
	$TOTALAMOUNT = 0;
	
	$LoopYDF =29.3;
	$LoopYF =31;
	$LoopYLF =103.8;
	$LoopYL =105.5;
	$Xno =6.5;
	$XNAME =14;
	
	$X1 =52.9;
	$X2 =65.5;
	$X3 =78.1;
	$X4 =90.6;
	$X5 =103.3;
	$X6 =116.1;
	$X7 =128.7;
	$X8 =141.3;
	$X9 =153.9;
	$X10 =166.7;
	$X11 =179.4;
	$X12 =191.8;
	$X13 =204.5;
	$X14 =217.1;
	$X15 =229.7;
	$X16 =242.4;
	$X17 =255;
	$X18 =267.6;
	$X19 =280.2;
	
	$XDF1 =53.9;
	$XDF2 =66.6;
	$XDF3 =79.4;
	$XDF4 =92;
	$XDF5 =104.7;
	$XDF6 =117.4;
	$XDF7 =130;
	$XDF8 =142.6;
	$XDF9 =155.3;
	$XDF10 =167.9;
	$XDF11 =180.6;
	$XDF12 =193.2;
	$XDF13 =205.7;
	$XDF14 =218.3;
	$XDF15 =231;
	$XDF16 =243.6;
	$XDF17 =256.2;
	$XDF18 =268.9;
	$XDF19 =281.5;
	
	$Lasen=0;
	$loopMod = 0;
	$LoopNameNew=5.4;
	$LoopNameNewDF=5.34;
    while ($data = $db_dpis2->get_array()) {
		if($no>0){
			$LoopNameNew=10.8;
			$LoopNameNewDF=10.68;
		}
        $no++;
		$Lasen++;
		$loopMod++;
		if ($loopMod % 6 == 0) {		/* NEW PAGE */
			$LoopNameNew=5.4;
			$LoopNameNewDF=5.34;
			if($NumPage!=ceil( $count_page_data/5)){ // ใส่พื้นสีขาว
				$pdf->setFillColor(255,255,255); 
				$pdf->Rect(0, 162.2, 300, 200,'F');
			}
			
			$pdf->SetFont($font,'',10);
			
			$pdf->SetXY(5, 200);
			$pdf->Write(0,'_____________________________________________________________________________________________________________________________________________________________________________________________________________________________________');
			$pdf->SetXY(10, 205);
			$pdf->Write(0,'รายงานT0203');
			
			$pdf->SetXY(110, 205);
			$pdf->Write(0,'หน้าที่ : '.$NumPage.'/'.ceil( $count_page_data/5));
			
			$pdf->SetXY(210, 205);
			$pdf->Write(0,'ผู้พิมพ์ : '.$PRINT_NAME_SHOW);
			

			$pdf->SetXY(255, 205);
			$pdf->Write(0,'วันที่พิมพ์ : '.$mday . " " . $month . " " . $year . " " . $time);
			
			$pdf->SetFont($font,'',$CH_PRINT_SIZE);
			$NumPage++;
			$pdf->AddPage();
			$pdf->SetTextColor(0, 0, 0);
			$pdf->importPage(1);
			$pdf->useTemplate(1,0,0);
			
			$pdf->SetXY(90, 18.2);
			$pdf->Write(0,$report_title);
	
			$pdf->SetXY(170.5, 18.2);
			$pdf->Write(0,$report_title1);
			
			$LoopYDF =29.3;
			$LoopYF =31;
			$LoopYLF =103.8;
			$LoopYL =105.5;
			$Xno =6.5;
			$XNAME =14;
			
			$X1 =52.9;
			$X2 =65.5;
			$X3 =78.1;
			$X4 =90.6;
			$X5 =103.3;
			$X6 =116.1;
			$X7 =128.7;
			$X8 =141.3;
			$X9 =153.9;
			$X10 =166.7;
			$X11 =179.4;
			$X12 =191.8;
			$X13 =204.5;
			$X14 =217.1;
			$X15 =229.7;
			$X16 =242.4;
			$X17 =255;
			$X18 =267.6;
			$X19 =280.2;
			
			$Lasen=1;
			$loopMod = 1;
			
			$XDF1 =53.9;
			$XDF2 =66.6;
			$XDF3 =79.4;
			$XDF4 =92;
			$XDF5 =104.7;
			$XDF6 =117.4;
			$XDF7 =130;
			$XDF8 =142.6;
			$XDF9 =155.3;
			$XDF10 =167.9;
			$XDF11 =180.6;
			$XDF12 =193.2;
			$XDF13 =205.7;
			$XDF14 =218.3;
			$XDF15 =231;
			$XDF16 =243.6;
			$XDF17 =256.2;
			$XDF18 =268.9;
			$XDF19 =281.5;
			
			
		}
		// บวก line ที่ 2
		$LoopYFNew=5.4;
		//ส่วนที่1
		$LoopYF=$LoopYF+$LoopNameNew;
		$pdf->SetXY($Xno, $LoopYF);
		$pdf->Write(0, $no);
		
		$pdf->SetXY($XNAME, $LoopYF);
		$pdf->Write(0, $data[NAME]);
		
		//ส่วนที่2
		$LoopYL=$LoopYL+$LoopNameNew;
		$pdf->SetXY($Xno, $LoopYL);
		$pdf->Write(0, $no);
		
		$pdf->SetXY($XNAME, $LoopYL);
		$pdf->Write(0, $data[NAME]);
		
		$I_HRS1 = (215.9)-($pdf->GetStringWidth(number_format($data[HRS1])));
		$pdf->SetXY($I_HRS1, $LoopYL);
		$pdf->Write(0, $data[HRS1]);
		
		$I_HRS2 = (228.7)-($pdf->GetStringWidth(number_format($data[HRS2])));
		$pdf->SetXY($I_HRS2, $LoopYL);
		$pdf->Write(0, $data[HRS2]);
		
		$I_AMOUNT = (241)-($pdf->GetStringWidth(number_format($data[AMOUNT])));
		$pdf->SetXY($I_AMOUNT, $LoopYL);
		$pdf->Write(0, number_format($data[AMOUNT]));
		
		$LoopYDF=$LoopYDF+$LoopNameNewDF;
		$LoopYLF=$LoopYLF+$LoopNameNewDF;
		
		for($idx=0;$idx<count($ArrworkDateIN);$idx++){
			if($ArrHol[$idx]==1){ //วันหยุด
				$pdf->SetFillColor(240,235,235);
				$pdf->SetDrawColor(240,235,235);
				//$pdf->SetDrawColor(218,218,218);
				if($idx==0){ //วันที่1
					$pdf->Rect($XDF1,$LoopYDF,11.4,4,'FD'); 
					$pdf->Rect($XDF1,$LoopYDF+$LoopYFNew,11.4,4,'FD');  
				}
				
				if($idx==1){ //วันที่2
					$pdf->Rect($XDF2,$LoopYDF,11.4,4,'FD');
					$pdf->Rect($XDF2,$LoopYDF+$LoopYFNew,11.4,4,'FD');   
				}
				
				if($idx==2){ //วันที่3
					$pdf->Rect($XDF3,$LoopYDF,11.4,4,'FD'); 
					$pdf->Rect($XDF3,$LoopYDF+$LoopYFNew,11.4,4,'FD');  
				}
				
				if($idx==3){ //วันที่4
					$pdf->Rect($XDF4,$LoopYDF,11.4,4,'FD');
					$pdf->Rect($XDF4,$LoopYDF+$LoopYFNew,11.4,4,'FD');   
				}
				
				if($idx==4){ //วันที่5
					$pdf->Rect($XDF5,$LoopYDF,11.4,4,'FD'); 
					$pdf->Rect($XDF5,$LoopYDF+$LoopYFNew,11.4,4,'FD');  
				}
				
				if($idx==5){ //วันที่6
					$pdf->Rect($XDF6,$LoopYDF,11.4,4,'FD');  
					$pdf->Rect($XDF6,$LoopYDF+$LoopYFNew,11.4,4,'FD'); 
				}
				
				if($idx==6){ //วันที่7
					$pdf->Rect($XDF7,$LoopYDF,11.4,4,'FD');
					$pdf->Rect($XDF7,$LoopYDF+$LoopYFNew,11.4,4,'FD');   
				}
				
				if($idx==7){ //วันที่8
					$pdf->Rect($XDF8,$LoopYDF,11.4,4,'FD');
					$pdf->Rect($XDF8,$LoopYDF+$LoopYFNew,11.4,4,'FD');   
				}
				
				if($idx==8){ //วันที่9
					$pdf->Rect($XDF9,$LoopYDF,11.4,4,'FD'); 
					$pdf->Rect($XDF9,$LoopYDF+$LoopYFNew,11.4,4,'FD');  
				}
				
				if($idx==9){ //วันที่10
					$pdf->Rect($XDF10,$LoopYDF,11.4,4,'FD');
					$pdf->Rect($XDF10,$LoopYDF+$LoopYFNew,11.4,4,'FD');   
				}
				
				if($idx==10){ //วันที่11
					$pdf->Rect($XDF11,$LoopYDF,11.4,4,'FD');
					$pdf->Rect($XDF11,$LoopYDF+$LoopYFNew,11.4,4,'FD');   
				}
				
				if($idx==11){ //วันที่12
					$pdf->Rect($XDF12,$LoopYDF,11.4,4,'FD');
					$pdf->Rect($XDF12,$LoopYDF+$LoopYFNew,11.4,4,'FD');   
				}
				
				if($idx==12){ //วันที่13
					$pdf->Rect($XDF13,$LoopYDF,11.4,4,'FD'); 
					$pdf->Rect($XDF13,$LoopYDF+$LoopYFNew,11.4,4,'FD');  
				}
				
				if($idx==13){ //วันที่14
					$pdf->Rect($XDF14,$LoopYDF,11.4,4,'FD'); 
					$pdf->Rect($XDF14,$LoopYDF+$LoopYFNew,11.4,4,'FD');  
				}
				
				if($idx==14){ //วันที่15
					$pdf->Rect($XDF15,$LoopYDF,11.4,4,'FD');  
					$pdf->Rect($XDF15,$LoopYDF+$LoopYFNew,11.4,4,'FD'); 
				}
				
				if($idx==15){ //วันที่16
					$pdf->Rect($XDF16,$LoopYDF,11.4,4,'FD'); 
					$pdf->Rect($XDF16,$LoopYDF+$LoopYFNew,11.4,4,'FD');  
				}
				
				if($idx==16){ //วันที่17
					$pdf->Rect($XDF17,$LoopYDF,11.4,4,'FD');
					$pdf->Rect($XDF17,$LoopYDF+$LoopYFNew,11.4,4,'FD');   
				}
				
				if($idx==17){ //วันที่18
					$pdf->Rect($XDF18,$LoopYDF,11.4,4,'FD'); 
					$pdf->Rect($XDF18,$LoopYDF+$LoopYFNew,11.4,4,'FD');  
				}
				
				if($idx==18){ //วันที่19
					$pdf->Rect($XDF19,$LoopYDF,11.4,4,'FD'); 
					$pdf->Rect($XDF19,$LoopYDF+$LoopYFNew,11.4,4,'FD');  
				}
				
				if($idx==19){ //วันที่20
					$pdf->Rect($XDF1,$LoopYLF,11.4,4,'FD');  
					$pdf->Rect($XDF1,$LoopYLF+$LoopYFNew,11.4,4,'FD'); 
				}
				
				if($idx==20){ //วันที่21
					$pdf->Rect($XDF2,$LoopYLF,11.4,4,'FD');
					$pdf->Rect($XDF2,$LoopYLF+$LoopYFNew,11.4,4,'FD');   
				}
				
				if($idx==21){ //วันที่22
					$pdf->Rect($XDF3,$LoopYLF,11.4,4,'FD'); 
					$pdf->Rect($XDF3,$LoopYLF+$LoopYFNew,11.4,4,'FD');  
				}
				
				if($idx==22){ //วันที่23
					$pdf->Rect($XDF4,$LoopYLF,11.4,4,'FD'); 
					$pdf->Rect($XDF4,$LoopYLF+$LoopYFNew,11.4,4,'FD');  
				}
				
				if($idx==23){ //วันที่24
					$pdf->Rect($XDF5,$LoopYLF,11.4,4,'FD'); 
					$pdf->Rect($XDF5,$LoopYLF+$LoopYFNew,11.4,4,'FD');  
				}
				
				if($idx==24){ //วันที่25
					$pdf->Rect($XDF6,$LoopYLF,11.4,4,'FD'); 
					$pdf->Rect($XDF6,$LoopYLF+$LoopYFNew,11.4,4,'FD');  
				}
				
				if($idx==25){ //วันที่26
					$pdf->Rect($XDF7,$LoopYLF,11.4,4,'FD');
					$pdf->Rect($XDF7,$LoopYLF+$LoopYFNew,11.4,4,'FD');   
				}
				
				if($idx==26){ //วันที่27
					$pdf->Rect($XDF8,$LoopYLF,11.4,4,'FD'); 
					$pdf->Rect($XDF8,$LoopYLF+$LoopYFNew,11.4,4,'FD');  
				}
				
				if($idx==27){ //วันที่28
					$pdf->Rect($XDF9,$LoopYLF,11.4,4,'FD');  
					$pdf->Rect($XDF9,$LoopYLF+$LoopYFNew,11.4,4,'FD'); 
				}
				
				if($idx==28){ //วันที่29
					$pdf->Rect($XDF10,$LoopYLF,11.4,4,'FD'); 
					$pdf->Rect($XDF10,$LoopYLF+$LoopYFNew,11.4,4,'FD');  
				}
				
				if($idx==29){ //วันที่30
					$pdf->Rect($XDF11,$LoopYLF,11.4,4,'FD'); 
					$pdf->Rect($XDF11,$LoopYLF+$LoopYFNew,11.4,4,'FD');  
				}
				
				if($idx==30){ //วันที่31
					$pdf->Rect($XDF12,$LoopYLF,11.4,4,'FD');
					$pdf->Rect($XDF12,$LoopYLF+$LoopYFNew,11.4,4,'FD');   
				}
				
				
			
			}
			
		
			$dataAbsChk = $data[$ArrworkDateIN[$idx]];
			if($dataAbsChk ==""){$dataAbsChk = "xx:xx-xx:xx,xx:xx-xx:xx";}
			$dataAbs = explode(",",$dataAbsChk);
			if($dataAbs[0] ==":-:"){$dataAbs[0] = "";}
			if($dataAbs[1] ==":-:"){$dataAbs[1] = "";}
			
			if($dataAbs[0] !=""){
				$I_X = (12/2)-($pdf->GetStringWidth($dataAbs[0])/2);
			}else{
				$I_X = (12/2)-($pdf->GetStringWidth($dataAbs[1])/2);
			}
			
			if($idx<19){
				
				if($idx==0){ //วันที่1
					$pdf->SetXY($X1+$I_X, $LoopYF);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X1+$I_X, $LoopYF+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
					
				}
				
				if($idx==1){ //วันที่2
					$pdf->SetXY($X2+$I_X, $LoopYF);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X2+$I_X, $LoopYF+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==2){ //วันที่3
					$pdf->SetXY($X3+$I_X, $LoopYF);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X3+$I_X, $LoopYF+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==3){ //วันที่4
					$pdf->SetXY($X4+$I_X, $LoopYF);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X4+$I_X, $LoopYF+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==4){ //วันที่5
					$pdf->SetXY($X5+$I_X, $LoopYF);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X5+$I_X, $LoopYF+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==5){ //วันที่6
					$pdf->SetXY($X6+$I_X, $LoopYF);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X6+$I_X, $LoopYF+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==6){ //วันที่7
					$pdf->SetXY($X7+$I_X, $LoopYF);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X7+$I_X, $LoopYF+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==7){ //วันที่8
					$pdf->SetXY($X8+$I_X, $LoopYF);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X8+$I_X, $LoopYF+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==8){ //วันที่9
					$pdf->SetXY($X9+$I_X, $LoopYF);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X9+$I_X, $LoopYF+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==9){ //วันที่10
					$pdf->SetXY($X10+$I_X, $LoopYF);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X10+$I_X, $LoopYF+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==10){ //วันที่11
					$pdf->SetXY($X11+$I_X, $LoopYF);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X11+$I_X, $LoopYF+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==11){ //วันที่12
					$pdf->SetXY($X12+$I_X, $LoopYF);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X12+$I_X, $LoopYF+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==12){ //วันที่13
					$pdf->SetXY($X13+$I_X, $LoopYF);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X13+$I_X, $LoopYF+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==13){ //วันที่14
					$pdf->SetXY($X14+$I_X, $LoopYF);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X14+$I_X, $LoopYF+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==14){ //วันที่15
					$pdf->SetXY($X15+$I_X, $LoopYF);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X15+$I_X, $LoopYF+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==15){ //วันที่16
					$pdf->SetXY($X16+$I_X, $LoopYF);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X16+$I_X, $LoopYF+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==16){ //วันที่17
					$pdf->SetXY($X17+$I_X, $LoopYF);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X17+$I_X, $LoopYF+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==17){ //วันที่18
					$pdf->SetXY($X18+$I_X, $LoopYF);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X18+$I_X, $LoopYF+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==18){ //วันที่19
					$pdf->SetXY($X19+$I_X, $LoopYF);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X19+$I_X, $LoopYF+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				
			}else{
				
				if($idx==19){ //วันที่20
					$pdf->SetXY($X1+$I_X, $LoopYL);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X1+$I_X, $LoopYL+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);	
				}
				
				if($idx==20){ //วันที่21
					$pdf->SetXY($X2+$I_X, $LoopYL);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X2+$I_X, $LoopYL+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==21){ //วันที่22
					$pdf->SetXY($X3+$I_X, $LoopYL);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X3+$I_X, $LoopYL+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==22){ //วันที่23
					$pdf->SetXY($X4+$I_X, $LoopYL);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X4+$I_X, $LoopYL+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==23){ //วันที่24
					$pdf->SetXY($X5+$I_X, $LoopYL);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X5+$I_X, $LoopYL+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==24){ //วันที่25
					$pdf->SetXY($X6+$I_X, $LoopYL);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X6+$I_X, $LoopYL+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==25){ //วันที่26
					$pdf->SetXY($X7+$I_X, $LoopYL);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X7+$I_X, $LoopYL+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==26){ //วันที่27
					$pdf->SetXY($X8+$I_X, $LoopYL);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X8+$I_X, $LoopYL+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==27){ //วันที่28
					$pdf->SetXY($X9+$I_X, $LoopYL);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X9+$I_X, $LoopYL+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==28){ //วันที่29
					$pdf->SetXY($X10+$I_X, $LoopYL);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X10+$I_X, $LoopYL+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==29){ //วันที่30
					$pdf->SetXY($X11+$I_X, $LoopYL);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X11+$I_X, $LoopYL+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
				if($idx==30){ //วันที่31
					$pdf->SetXY($X12+$I_X, $LoopYL);
					$pdf->Write(0, $dataAbs[0]<=0 ? '':$dataAbs[0]);
					
					$pdf->SetXY($X12+$I_X, $LoopYL+$LoopYFNew);
					$pdf->Write(0, $dataAbs[1]<=0 ? '':$dataAbs[1]);
				}
				
			}

        } // end for($idx=0;$idx<count($ArrworkDateIN);$idx++){
		
		
		
		$TOTALHRS1 = $TOTALHRS1 + $data[HRS1];
		$TOTALHRS2 = $TOTALHRS2 + $data[HRS2];
		$TOTALAMOUNT = $TOTALAMOUNT + $data[AMOUNT];
		
		
		
		
		if($count_page_data==$no){
			
			$pdf->SetXY(121, 164.7);
			$pdf->Write(0, num2wordsThai($TOTALAMOUNT). " บาทถ้วน");

			$I_TOTALHRS1 = (215.9)-($pdf->GetStringWidth(number_format($TOTALHRS1)));
			$pdf->SetXY($I_TOTALHRS1, 164.7);
			$pdf->Write(0, $TOTALHRS1<=0 ? '':number_format($TOTALHRS1));
			
			$I_TOTALHRS2 = (228.7)-($pdf->GetStringWidth(number_format($TOTALHRS2)));
			$pdf->SetXY($I_TOTALHRS2, 164.7);
			$pdf->Write(0, $TOTALHRS2<=0 ? '':number_format($TOTALHRS2));
			
			$I_TOTALAMOUNTSUM = (241)-($pdf->GetStringWidth(number_format($TOTALAMOUNT)));
			$pdf->SetXY($I_TOTALAMOUNTSUM, 164.7);
			$pdf->Write(0, $TOTALAMOUNT<=0 ? '':number_format($TOTALAMOUNT));
			
		
			// ส่วนลงนาม
			//if($Lasen==1){
			if(1==1){
				$cmd = " select g.PN_NAME||a.PER_NAME||' '||a.PER_SURNAME  AS APROVE_FULLNAME_SHOW ,
						h.PN_NAME||b.PER_NAME||' '||b.PER_SURNAME  AS ALLOW_FULLNAME_SHOW ,
						a.PER_CARDNO AS APPROVE_CARDNO,b.PER_CARDNO AS ALLOW_CARDNO ,
						con.APPROVE_USER,con.ALLOW_USER
						from TA_PER_OT_CONTROL con 
						left join PER_PERSONAL a on(a.PER_ID=con.APPROVE_USER)
						left join PER_PERSONAL b on(b.PER_ID=con.ALLOW_USER)
						left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE)
						left join PER_PRENAME h on(h.PN_CODE=b.PN_CODE)
						where con.CONTROL_ID=$CONTROL_ID AND  con.BUDGET_YEAR=$search_yearBgn AND con.PAY_MONTH=$search_month ";
				$db_dpis->send_cmd($cmd);
				$data_dpis2 = $db_dpis->get_array();
				$APROVE_FULLNAME_SHOW = trim($data_dpis2[APROVE_FULLNAME_SHOW]);
				$ALLOW_FULLNAME_SHOW = trim($data_dpis2[ALLOW_FULLNAME_SHOW]);
				$APPROVE_CARDNO = $data_dpis2[APPROVE_CARDNO];
				$APPROVE_PER_ID = $data_dpis2[APPROVE_USER];
				$ALLOW_CARDNO = $data_dpis2[ALLOW_CARDNO];
				$ALLOW_PER_ID = $data_dpis2[ALLOW_USER];
				
				//echo $ALLOW_PER_ID."||".$ALLOW_CARDNO; die();
				$PIC_SIGN_ALLOW = "";
				$PIC_SIGN_APPROVE = "";
				if($P_ESIGN_ATT=="Y"){
					$PIC_SIGN_ALLOW = getPIC_SIGN($ALLOW_PER_ID,$ALLOW_CARDNO);
					
					$PIC_SIGN_APPROVE = getPIC_SIGN($APPROVE_PER_ID,$APPROVE_CARDNO);
				}
				
				
				if($PIC_SIGN_ALLOW){
					$pdf->Image($PIC_SIGN_ALLOW, 14.5, 173,40,14,"","");
				}
				
				$XFt =(46/2)-($pdf->GetStringWidth($ALLOW_FULLNAME_SHOW)/2);
				$pdf->SetXY(10+$XFt, 192);
				$pdf->Write(0,$ALLOW_FULLNAME_SHOW);
				
				
				if($PIC_SIGN_APPROVE){
					$pdf->Image($PIC_SIGN_APPROVE, 216.5,173,40,14,"","");
				}
				
				$XFtA = (47/2)-($pdf->GetStringWidth($ALLOW_FULLNAME_SHOW)/2);
				$pdf->SetXY(213+$XFtA, 192);
				$pdf->Write(0,$APROVE_FULLNAME_SHOW);
				
				
			
			} // end if($Lasen==0){
			
			
			$pdf->SetFont($font,'',10);
			
			$pdf->SetXY(5, 200);
			$pdf->Write(0,'_____________________________________________________________________________________________________________________________________________________________________________________________________________________________________');
			$pdf->SetXY(10, 205);
			$pdf->Write(0,'รายงานT0203');
			
			$pdf->SetXY(110, 205);
			$pdf->Write(0,'หน้าที่ : '.$NumPage.'/'.ceil( $count_page_data/5));
			
			$pdf->SetXY(210, 205);
			$pdf->Write(0,'ผู้พิมพ์ : '.$PRINT_NAME_SHOW);
			

			$pdf->SetXY(255, 205);
			$pdf->Write(0,'วันที่พิมพ์ : '.$mday . " " . $month . " " . $year . " " . $time);
			
		}
        
    }
	
	
    
    $pdf->close();
	
    $fname = "T0203.pdf";
	

	$pdf->Output($fname,'D');	
       
?>