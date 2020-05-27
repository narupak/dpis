<?php
	include("../../php_scripts/connect_database.php");
	include ("../php_scripts/session_start.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	ini_set("max_execution_time", $max_execution_time);
        $CURRENT_DATE = date("Y-m-d H:i:s");
        function Convert($amount_number){
            $amount_number = number_format($amount_number, 2, ".","");
            $pt = strpos($amount_number , ".");
            $number = $fraction = "";
            if ($pt === false) 
                $number = $amount_number;
            else {
                $number = substr($amount_number, 0, $pt);
                $fraction = substr($amount_number, $pt + 1);
            }
            $ret = "";
            $baht = ReadNumber ($number);
            if ($baht != "")
                $ret .= $baht . "บาท";

            $satang = ReadNumber($fraction);
            if ($satang != "")
                $ret .=  $satang . "สตางค์";
            else 
                $ret .= "ถ้วน";
            return $ret;
        }

        function ReadNumber($number){
            $position_call = array("แสน", "หมื่น", "พัน", "ร้อย", "สิบ", "");
            $number_call = array("", "หนึ่ง", "สอง", "สาม", "สี่", "ห้า", "หก", "เจ็ด", "แปด", "เก้า");
            $number = $number + 0;
            $ret = "";
            if ($number == 0) return $ret;
            if ($number > 1000000){
                $ret .= ReadNumber(intval($number / 1000000)) . "ล้าน";
                $number = intval(fmod($number, 1000000));
            }

            $divider = 100000;
            $pos = 0;
            while($number > 0){
                $d = intval($number / $divider);
                $ret .= (($divider == 10) && ($d == 2)) ? "ยี่" : 
                    ((($divider == 10) && ($d == 1)) ? "" :
                    ((($divider == 1) && ($d == 1) && ($ret != "")) ? "เอ็ด" : $number_call[$d]));
                $ret .= ($d ? $position_call[$pos] : "");
                $number = $number % $divider;
                $divider = $divider / 10;
                $pos++;
            }
            return $ret;
        }
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if(trim($LET_DATE)){
		$arr_temp = explode("/", $LET_DATE);
		$LET_DATE = ($arr_temp[0] + 0) ."   ". $month_full[($arr_temp[1] + 0)][TH] ."   พ.ศ.  ". $arr_temp[2];
		//$LET_DATE = ($arr_temp[0])."-".($arr_temp[1] + 0)."-".$arr_temp[2];
		//$LET_DATE = show_date_format($data2[TRN_STARTDATE],$DATE_DISPLAY);
	} // end if
	
	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STARTDATE, a.LEVEL_NO, a.PER_TYPE, c.OT_NAME,
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.PER_SALARY, a.PER_MGTSALARY, a.PER_SPSALARY ,a.PER_JOB
						 from			PER_PERSONAL a, PER_PRENAME b, PER_OFF_TYPE c
						 where		a.PER_ID=$PER_ID and a.PN_CODE=b.PN_CODE and a.OT_CODE=c.OT_CODE ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select			a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STARTDATE, a.LEVEL_NO, a.PER_TYPE, c.OT_NAME,
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.PER_SALARY, a.PER_MGTSALARY, a.PER_SPSALARY ,a.PER_JOB
						 from			PER_PERSONAL a, PER_PRENAME b, PER_OFF_TYPE c
						 where		a.PER_ID=$PER_ID and a.PN_CODE=b.PN_CODE and a.OT_CODE=c.OT_CODE ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STARTDATE, a.LEVEL_NO, a.PER_TYPE, c.OT_NAME,
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.PER_SALARY, a.PER_MGTSALARY, a.PER_SPSALARY ,a.PER_JOB
						 from			PER_PERSONAL a, PER_PRENAME b, PER_OFF_TYPE c
						 where		a.PER_ID=$PER_ID and a.PN_CODE=b.PN_CODE and a.OT_CODE=c.OT_CODE ";	
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
        $PER_ID = $data[PER_ID];
        $PER_TYPE = $data[PER_TYPE];
        
        //========================================================================read tamplate=====================================================================================
        if($PER_TYPE==1){
            $filename = "rpt_data_letter_template.rtf";
        }else{
            $filename = "rpt_data_letter_template_2.rtf";
        }
        // read template content
	$handle = fopen ($filename, "r");
	$rtf_contents = fread($handle, filesize($filename));
	fclose($handle);
	//echo $rtf_contents;
        //======================================================================End read tamplate=====================================================================================
        
        //============================================================= เงินประจำตำแหน่ง & เงินตอบแทนพิเศษ =================================================================================
        $PER_SPSALARY = $PER_MGTSALARY_SPSALARY = $PER_TOTALSALARY = $EXH_SEQ = $EX_SEQ = "";
        $CNT_NUMSAlARY = 0;
        $NUM_MGT = 0;
        $NUM_SP = 0;
        $PER_TOTALSALARY += $data[PER_SALARY];
        if ($PER_TYPE=="1") {
            $cmd = " select EX_NAME, PMH_AMT from PER_POS_MGTSALARYHIS a, PER_EXTRATYPE b 
                                              where trim(a.EX_CODE)=trim(b.EX_CODE) and PER_ID=$PER_ID and PMH_ACTIVE = 1 and 
                                            (PMH_ENDDATE is NULL or PMH_ENDDATE >= '$CURRENT_DATE')
                                              order by EX_SEQ_NO, b.EX_CODE ";
            $CNT_MGT = $db_dpis2->send_cmd($cmd);
            while($data2 = $db_dpis2->get_array()){
                    $NUM_MGT++;
                    $EX_SEQ++;
                    $CNT_NUMSAlARY++;
                    $EX_NAME = trim($data2[EX_NAME]);
                    $PMH_AMT = $data2[PMH_AMT];
                    $PER_TOTALSALARY += $PMH_AMT;
                    //$PER_MGTSALARY .= $EX_SEQ.". ประเภท : ".$EX_NAME."  จำนวนเงิน : ".number_format($data2[PMH_AMT],2)."  บาท";
                    if($NUM_MGT===$CNT_MGT){ $PER_MGTSALARY_SPSALARY .= "และ".$EX_NAME." ".number_format($data2[PMH_AMT])." บาท ";}
                    else{ $PER_MGTSALARY_SPSALARY .= $EX_NAME." ".number_format($data2[PMH_AMT])." บาท ";}
                    
            }		
        }
        //echo "<pre> $cmd <br>";
                        
        $cmd = " select EX_NAME, EXH_AMT	from PER_EXTRAHIS a, PER_EXTRATYPE b
                                        where trim(a.EX_CODE)=trim(b.EX_CODE) and PER_ID=$PER_ID and EXH_ACTIVE = 1 and 
                                        (EXH_ENDDATE is NULL or EXH_ENDDATE >= '$CURRENT_DATE')
                                        order by EX_SEQ_NO, b.EX_CODE ";
        //echo "<pre> $cmd <br>";
        $CNT_SP = $db_dpis2->send_cmd($cmd);
        if($CNT_SP>0){
            $PER_MGTSALARY_SPSALARY = str_replace("และ","",$PER_MGTSALARY_SPSALARY);
            while($data2 = $db_dpis2->get_array()){
                $EXH_SEQ++;
                $NUM_SP++;
                $CNT_NUMSAlARY++;
                $EX_NAME = trim($data2[EX_NAME]);
                $EXH_AMT = $data2[EXH_AMT];
                $PER_TOTALSALARY += $EXH_AMT;
                //$PER_SPSALARY .= $EXH_SEQ.". ประเภท : ".$EX_NAME."  จำนวนเงิน : ".number_format($data2[EXH_AMT],2)."  บาท ";
                if($NUM_SP===$CNT_SP){ $PER_MGTSALARY_SPSALARY .= "และ".$EX_NAME." ".number_format($data2[EXH_AMT])." บาท ";}
                else{ $PER_MGTSALARY_SPSALARY .= $EX_NAME." ".number_format($data2[EXH_AMT])." บาท ";}
            }
        }
        
        
        if(!$PER_TOTALSALARY){
            $PER_SALARY_WORD = "";
        }else{
            $PER_SALARY_WORD = Convert(str_replace(",","",$PER_TOTALSALARY));
        }
        $PER_TOTALSALARY = $PER_TOTALSALARY?number_format($PER_TOTALSALARY):"-";
        $PER_SALARY = $data[PER_SALARY]?number_format($data[PER_SALARY]):"-";
        
        //================================================================= จบ เงินประจำตำแหน่ง & เงินตอบแทนพิเศษ =============================================================================
        
	//echo  "จำนวนเงิน $CNT_NUMSAlARY --  $PER_MGTSALARY_SPSALARY : รวม = $PER_TOTALSALARY  ($PER_SALARY_WORD)";
        //die();
	$PER_NAME = "$data[PN_NAME]$data[PER_NAME] $data[PER_SURNAME]";
	$PER_STARTDATE = show_date_format($data[PER_STARTDATE],3);
        $today = date("Y-m-d");
        $DATE_DIFF = date_difference($today, $data[PER_STARTDATE], "full");
	$LEVEL_NO = trim($data[LEVEL_NO]);
        $PER_JOB = trim($data[PER_JOB]);
        
	$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO = '$LEVEL_NO'";
	$db_dpis2->send_cmd($cmd);
	$data2  = $db_dpis2->get_array();
	$LEVEL_NAME2 = $data2[LEVEL_NAME];
	$POSITION_LEVEL = $data2[POSITION_LEVEL];
	if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME2;
	$OFFICER_TYPE = $data[OT_NAME];
        
	if($PER_TYPE==1){
		$POS_ID = $data[POS_ID];
		$cmd = "	select		a.POS_NO, b.PL_NAME, a.ORG_ID, c.ORG_NAME, c.ORG_ID_REF, a.PT_CODE
							from		PER_POSITION a, PER_LINE b, PER_ORG c
							where		a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();

		$POS_NO = trim($data2[POS_NO]);
		$PL_NAME = trim($data2[PL_NAME]);
		$ORG_ID = $data2[ORG_ID];
		$ORG_NAME = trim($data2[ORG_NAME]);
		$DEPARTMENT_ID = $data2[ORG_ID_REF];
		$PT_CODE = trim($data[PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PT_NAME = trim($data2[PT_NAME]);
		$PL_NAME = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"";
		$LEVEL_NAME = (($PT_CODE==11)?"ท.":$PT_NAME) . $LEVEL_NAME2;
	}elseif($PER_TYPE==2){
		$POEM_ID = $data[POEM_ID];
		$cmd = "	select		a.POEM_NO, b.PN_NAME, a.ORG_ID, c.ORG_NAME, c.ORG_ID_REF
							from		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
							where		a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POS_NO = trim($data2[POEM_NO]);
		$PL_NAME = trim($data2[PN_NAME]);
		$ORG_ID = $data2[ORG_ID];
		$ORG_NAME = trim($data2[ORG_NAME]);
		$DEPARTMENT_ID = $data2[ORG_ID_REF];
		$PL_NAME = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL):"";
		$LEVEL_NAME = $LEVEL_NAME2;
	}elseif($PER_TYPE==3){
		$POEMS_ID = $data[POEMS_ID];
		$cmd = "	select		a.POEMS_NO, b.EP_NAME, a.ORG_ID, c.ORG_NAME, c.ORG_ID_REF
							from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
							where		a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POS_NO = trim($data2[POEMS_NO]);
		$PL_NAME = trim($data2[EP_NAME]);
		$ORG_ID = $data2[ORG_ID];
		$ORG_NAME = trim($data2[ORG_NAME]);
		$DEPARTMENT_ID = $data2[ORG_ID_REF];
		$PL_NAME = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL):"";
		$LEVEL_NAME = $LEVEL_NAME2;
	} elseif($PER_TYPE==4){
		$POT_ID = $data[POT_ID];
		$cmd = "	select		a.POT_NO, b.TP_NAME, a.ORG_ID, c.ORG_NAME, c.ORG_ID_REF
							from		PER_POS_TEMP a, PER_TEMP_POS_NAME b, PER_ORG c
							where		a.POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POS_NO = trim($data2[POT_NO]);
		$PL_NAME = trim($data2[TP_NAME]);
		$ORG_ID = $data2[ORG_ID];
		$ORG_NAME = trim($data2[ORG_NAME]);
		$DEPARTMENT_ID = $data2[ORG_ID_REF];
		$PL_NAME = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL):"";
		$LEVEL_NAME = $LEVEL_NAME2;
	} // end if
	
	$cmd = " select ORG_ID_REF, ORG_NAME  from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$DEPARTMENT_NAME = $data2[ORG_NAME];
	$MINISTRY_ID = $data2[ORG_ID_REF];
	
	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$MINISTRY_NAME = $data2[ORG_NAME];
	
	$cmd = " select ORG_ADDR1, ORG_ADDR2, ORG_ADDR3 from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$ORG_ADDR1 = $data2[ORG_ADDR1];	
	$A = $data2[ORG_ADDR2];	
	$B = $data2[ORG_ADDR3];	
	
        //PER_LETTER
	//$ORG_ADDR123 = $ORG_ADDR1." ".$ORG_ADDR2." ".$ORG_ADDR3;
	//echo $cmd."<br>  $ORG_ADDR1";
	//echo $cmd."$A <br> $B";
	/*$strSQL = "SELECT * FROM PER_LETTER WHERE LET_ID = $LET_ID";
        $db_dpis2->send_cmd($strSQL);
	$data3 = $db_dpis2->get_array();
        $LET_SIGN = $data3[LET_SIGN];
        $LET_POSITION = $data3[LET_POSITION];*/
	
	$LET_SIGN = ($LET_ASSIGN==1?"ปฏิบัติราชการแทน":($LET_ASSIGN==2?"รักษาการแทน":""))."$LET_SIGN";
	
	$rtf_contents = replaceRTF("@LET_NO@", convert2rtfascii(convert2thaidigit($LET_NO)), $rtf_contents);
	$rtf_contents = replaceRTF("@DEPARTMENT_NAME@", convert2rtfascii(convert2thaidigit($DEPARTMENT_NAME)), $rtf_contents);
	$rtf_contents = replaceRTF("@ORG_ADDR@", convert2rtfascii(convert2thaidigit($ORG_ADDR1)), $rtf_contents);
	$rtf_contents = replaceRTF("@A@", convert2rtfascii(convert2thaidigit($A)), $rtf_contents);
	$rtf_contents = replaceRTF("@B@", convert2rtfascii(convert2thaidigit($B)), $rtf_contents);
	$rtf_contents = replaceRTF("@PER_NAME@", convert2rtfascii(convert2thaidigit($PER_NAME)), $rtf_contents);
	$rtf_contents = replaceRTF("@OFFICER_TYPE@", convert2rtfascii(convert2thaidigit($OFFICER_TYPE)), $rtf_contents);
	$rtf_contents = replaceRTF("@PL_NAME@", convert2rtfascii(convert2thaidigit($PL_NAME)), $rtf_contents);
	$rtf_contents = replaceRTF("@ORG_NAME@", convert2rtfascii(convert2thaidigit($ORG_NAME)), $rtf_contents);
	$rtf_contents = replaceRTF("@MINISTRY_NAME@", convert2rtfascii(convert2thaidigit($MINISTRY_NAME)), $rtf_contents);
        if($PER_TYPE==1){
            $rtf_contents = replaceRTF("@WORK_START@", convert2rtfascii(convert2thaidigit(" เริ่มรับราชการเมื่อวันที่ ".$PER_STARTDATE)), $rtf_contents);
            $rtf_contents = replaceRTF("@CNT_WORK@", convert2rtfascii(convert2thaidigit(" รวมเป็นเวลารับราชการ ".$DATE_DIFF)), $rtf_contents);
            $rtf_contents = replaceRTF("@PER_SALARY@", convert2rtfascii(convert2thaidigit($PER_SALARY)), $rtf_contents);
            $rtf_contents = replaceRTF("@PER_MGTSALARY_SPSALARY@", convert2rtfascii(convert2thaidigit($PER_MGTSALARY_SPSALARY)), $rtf_contents);
            $rtf_contents = replaceRTF("@PER_SALARY_ALL@", convert2rtfascii(convert2thaidigit($PER_TOTALSALARY)), $rtf_contents);
            if($PER_SALARY_WORD){$rtf_contents = replaceRTF("@PER_SALARY_WORD@", convert2rtfascii(convert2thaidigit($PER_SALARY_WORD)), $rtf_contents);}
            else{$rtf_contents = replaceRTF("(@PER_SALARY_WORD@)", convert2rtfascii(convert2thaidigit("")), $rtf_contents);}
            //$rtf_contents = replaceRTF("@PER_MGTSALARY@", convert2rtfascii(convert2thaidigit($PER_MGTSALARY)), $rtf_contents);
            //$rtf_contents = replaceRTF("@LEVEL_NAME@", convert2rtfascii(convert2thaidigit($LEVEL_NAME)), $rtf_contents);
            //$rtf_contents = replaceRTF("@PER_SPSALARY@", convert2rtfascii(convert2thaidigit($PER_SPSALARY)), $rtf_contents);
        }else{
            $rtf_contents = replaceRTF("@WORK_START@", convert2rtfascii(convert2thaidigit(" เริ่มจ้างเมื่อวันที่ ".$PER_STARTDATE)), $rtf_contents);
            $rtf_contents = replaceRTF("@CNT_WORK@", convert2rtfascii(convert2thaidigit(" รวมเป็นเวลา ".$DATE_DIFF)), $rtf_contents);
            if($PER_JOB){$rtf_contents = replaceRTF("@WORK_DUTY@", convert2rtfascii(convert2thaidigit(" ปฏิบัติงานเกี่ยวกับ ".$PER_JOB)), $rtf_contents);}
			else{$rtf_contents = replaceRTF("@WORK_DUTY@", convert2rtfascii(convert2thaidigit("")), $rtf_contents);}
			$rtf_contents = replaceRTF("@SHOWE_SALARY@", convert2rtfascii(convert2thaidigit(" ปัจจุบันได้รับเงินเดือน ".$PER_SALARY." บาท ")), $rtf_contents);
			$rtf_contents = replaceRTF("@PER_MGTSALARY_SPSALARY@", convert2rtfascii(convert2thaidigit($PER_MGTSALARY_SPSALARY)), $rtf_contents);
			$rtf_contents = replaceRTF("@PER_SALARY_ALL@", convert2rtfascii(convert2thaidigit($PER_TOTALSALARY)), $rtf_contents);
            if($PER_SALARY_WORD){$rtf_contents = replaceRTF("@PER_SALARY_WORD@", convert2rtfascii(convert2thaidigit($PER_SALARY_WORD)), $rtf_contents);}
            else{$rtf_contents = replaceRTF("(@PER_SALARY_WORD@)", convert2rtfascii(convert2thaidigit("")), $rtf_contents);}
            
        }
	$rtf_contents = replaceRTF("@LET_DATE@", convert2rtfascii(convert2thaidigit($LET_DATE)), $rtf_contents);
	$rtf_contents = replaceRTF("@PER_NAME_SIGN1@", convert2rtfascii(convert2thaidigit($PER_NAME_SIGN1)), $rtf_contents);
	$rtf_contents = replaceRTF("@LET_POSITION@", convert2rtfascii(convert2thaidigit($LET_POSITION)), $rtf_contents);
	$rtf_contents = replaceRTF("@LET_SIGN@", convert2rtfascii(convert2thaidigit($LET_SIGN)), $rtf_contents);

	// write rtf content
	$filename = "rpt_data_letter_$SESS_USERNAME.rtf";
	$handle = fopen ($filename, "w");
	fwrite($handle, $rtf_contents);
	fclose($handle);
        
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.$filename);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        ob_clean();
        flush();
        readfile($filename);
        unlink($filename); // deletes the temporary file
        exit;
	//ini_set("max_execution_time", 30);
