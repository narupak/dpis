<?php
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
        //============================================= function ============================================================
        function strreplaceLvl($str){
            $findStr= array("ประเภททั่วไป ระดับ","ประเภทวิชาการ ระดับ","ประเภทอำนวยการ ระดับ","ประเภทบริหาร ระดับ");
            $str = str_replace($findStr, "", $str);
            return $str;
        }
        function showscore($val){
            global $KF_SCORE_STATUS,$KPI_SCORE_CONFIRM,$SESS_USERGROUP ,$PER_ID_REVIEW,$SESS_PER_ID ,
                    $PER_ID_REVIEW0,$SESS_PER_ID,$PER_ID_REVIEW1,$SESS_PER_ID,$PER_ID_REVIEW2,$SESS_PER_ID;
            if ($KF_SCORE_STATUS==1 || $KPI_SCORE_CONFIRM!=1 || $SESS_USERGROUP == 1 || $PER_ID_REVIEW==$SESS_PER_ID || 
                   $PER_ID_REVIEW0==$SESS_PER_ID || $PER_ID_REVIEW1==$SESS_PER_ID || $PER_ID_REVIEW2==$SESS_PER_ID) {
                 return $val;
           }else{
               return '';
           }
        }
        function get_cardno($PER_ID){
            global $db_dpis , $db_dpis2;
            $cmd = " select  PER_CARDNO
                    from		PER_PERSONAL
                    where	PER_ID=$PER_ID ";
            $db_dpis->send_cmd($cmd);
            $data = $db_dpis->get_array();
            $PER_CARDNO_ALL = trim($data[PER_CARDNO]);
            return $PER_CARDNO_ALL;
        }
        function getPIC_SIGN($PER_ID,$PER_CARDNO){
            global $db_dpis , $db_dpis2;

            $PIC_SIGN="";
            //หารูปที่เป็นลายเซ็น
            $cmd = "	select *from    PER_PERSONALPIC
                            where 		PER_ID=$PER_ID and PER_CARDNO = '$PER_CARDNO' and  PIC_SIGN=1 and PIC_SHOW=1
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
                $TMP_SIGN = "";
                if($TMP_PIC_SIGN==1){ $TMP_SIGN = "SIGN"; }
                if (trim($PER_CARDNO) && trim($PER_CARDNO) != "NULL") {
                        $TMP_PIC_NAME = $data[PER_PICPATH].$PER_CARDNO."-".$TMP_SIGN.$T_PIC_SEQ.".jpg".($tmp_SERVER_NAME ? " [".$tmp_SERVER_NAME."]" : "");
                } else {
                        $TMP_PIC_NAME = $data[PER_PICPATH].$data[PER_GENNAME]."-".$TMP_SIGN.$T_PIC_SEQ.".jpg".($tmp_SERVER_NAME ? " [".$tmp_SERVER_NAME."]" : "");
                }
                if(file_exists("../".$TMP_PIC_NAME)){
                        $PIC_SIGN = "../".$TMP_PIC_NAME;
                }
            } 	
            return $PIC_SIGN;
	}
        function get_month($month,$month_select){
            if($month_select==1){
                if($month == "01"){ $full_month_th = "มกราคม"; }
                else if($month == "02"){ $full_month_th = "กุมภาพันธ์";}
                else if($month == "03"){ $full_month_th = "มีนาคม"; }
                else if($month == "04"){ $full_month_th = "เมษายน"; }
                else if($month == "05"){ $full_month_th = "พฤษภาคม"; }
                else if($month == "06"){ $full_month_th = "มิถุนายน"; }
                else if($month == "07"){ $full_month_th = "กรกฎาคม"; }
                else if($month == "08"){ $full_month_th = "สิงหาคม"; }
                else if($month == "09"){ $full_month_th = "กันยายน"; }
                else if($month == "10"){ $full_month_th = "ตุลาคม"; }
                else if($month == "11"){ $full_month_th = "พฤศจิกายน"; }
                else if($month == "12"){ $full_month_th = "ธันวาคม"; }
            }else{    
                if($month == "01"){ $full_month_th = "ม.ค."; }
                else if($month == "02"){ $full_month_th = "ก.พ.";}
                else if($month == "03"){ $full_month_th = "มี.ค."; }
                else if($month == "04"){ $full_month_th = "เม.ย."; }
                else if($month == "05"){ $full_month_th = "พ.ค."; }
                else if($month == "06"){ $full_month_th = "มิ.ย."; }
                else if($month == "07"){ $full_month_th = "ก.ค."; }
                else if($month == "08"){ $full_month_th = "ส.ค."; }
                else if($month == "09"){ $full_month_th = "ก.ย."; }
                else if($month == "10"){ $full_month_th = "ต.ค."; }
                else if($month == "11"){ $full_month_th = "พ.ย."; }
                else if($month == "12"){ $full_month_th = "ธ.ค."; }
            }
            return $full_month_th;
        }
        function print_header($header_select){
		global $pdf,  $heading_width3, $KPI_SCORE_DECIMAL;
		
		if($header_select == 3){
			$pdf->SetFont($font,'',14);
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
			$pdf->Cell($heading_width3[0] ,7,"ความรู้/ ทักษะ/ สมรรถนะ",'LTR',0,'C',1);
			$pdf->Cell($heading_width3[1] ,13,"",'LTR',0,'C',1);
			$pdf->Cell($heading_width3[2] ,13,"",'LTR',1,'C',1);

			$pdf->y -= 7;
			$pdf->Cell($heading_width3[0] ,13,"ที่ต้องได้รับการพัฒนา",'LBR',0,'C',1);
			$pdf->Cell($heading_width3[1] ,13,"วิธีการพัฒนา",'LBR',0,'C',1);
			$pdf->Cell($heading_width3[2] ,13,"ช่วงเวลาที่ต้องการการพัฒนา",'LBR',1,'C',1);
                        
		}	// end if
	} // function	
        
         //--------------------------------------------------select data-------------------------------------------------------
        
        //หาชื่อหน่วยงาน
	if($PV_CODE){
		$select_org_name = "PV_ENG_NAME";
		$find_org_name = "PV_CODE='$PV_CODE'";
	}else{
		$select_org_name = "ORG_ENG_NAME";
		$find_org_name = "ORG_ID=$DEPARTMENT_ID";
	}
	$cmd = " select $select_org_name from PER_ORG where $find_org_name";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$ORG_ENG_NAME = trim($data[$select_org_name]);

	$cmd = " select 	KF_CYCLE, KF_START_DATE, KF_END_DATE, PER_ID, PER_CARDNO, PER_ID_REVIEW, PER_ID_REVIEW0, PER_ID_REVIEW1, 
						PER_ID_REVIEW2,	SCORE_KPI, SUM_KPI, SCORE_COMPETENCE, SUM_COMPETENCE, SCORE_OTHER, SUM_OTHER, RESULT_COMMENT, 
						COMPETENCE_COMMENT, SALARY_RESULT, SALARY_REMARK1, SALARY_REMARK2, AGREE_REVIEW1, DIFF_REVIEW1, AGREE_REVIEW2, 
						DIFF_REVIEW2, PERFORMANCE_WEIGHT, COMPETENCE_WEIGHT, OTHER_WEIGHT, KF_STATUS, LEVEL_NO, KF_SCORE_STATUS,
						ORG_ID_KPI, PER_SALARY, PL_NAME, PM_NAME, REVIEW_PN_NAME, REVIEW_PER_NAME, REVIEW_PL_NAME, REVIEW_PM_NAME, 
						REVIEW_LEVEL_NO, REVIEW_PN_NAME0, REVIEW_PER_NAME0, REVIEW_PL_NAME0, REVIEW_PM_NAME0, REVIEW_LEVEL_NO0, 
						REVIEW_PN_NAME1, REVIEW_PER_NAME1, REVIEW_PL_NAME1, REVIEW_PM_NAME1, REVIEW_LEVEL_NO1, REVIEW_PN_NAME2, 
						REVIEW_PER_NAME2, REVIEW_PL_NAME2, REVIEW_PM_NAME2, REVIEW_LEVEL_NO2,ORG_ID,ACCEPT_FLAG, 
						SCORE_STATUS_DATE, ACCEPT_DATE, DATE_REVIEW1, DATE_REVIEW2, DATE_REVIEW0
			   from 		PER_KPI_FORM
			   where 	KF_ID=$KF_ID ";
        
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$KF_CYCLE = trim($data[KF_CYCLE]);
	$KF_START_DATE = show_date_format($data[KF_START_DATE], 1);
	$KF_END_DATE = show_date_format($data[KF_END_DATE], 1);
	$KF_YEAR = substr($KF_END_DATE, 6, 4);

        $ACCEPT_FLAG= $data[ACCEPT_FLAG];
	$PER_ID = $data[PER_ID];
	$PER_CARDNO = trim($data[PER_CARDNO]);		
	$PER_ID_REVIEW = $data[PER_ID_REVIEW];
	$PER_ID_REVIEW0 = $data[PER_ID_REVIEW0];
	$PER_ID_REVIEW1 = $data[PER_ID_REVIEW1];
	$PER_ID_REVIEW2 = $data[PER_ID_REVIEW2];
	$KF_SCORE_STATUS = $data[KF_SCORE_STATUS];
	$DATE_REVIEW0    = $data[DATE_REVIEW0];
	$DATE_REVIEW1    = $data[DATE_REVIEW1];
	$DATE_REVIEW2    = $data[DATE_REVIEW2];
	$ORG_ID_KPI = $data[ORG_ID_KPI];
        $view_ORG_ID = $data[ORG_ID];
	$PER_SALARY = $data[PER_SALARY];
	$PL_NAME = $data[PL_NAME];
	$PM_NAME = $data[PM_NAME];
	$REVIEW_PN_NAME = $data[REVIEW_PN_NAME];
	$REVIEW_PER_NAME = $data[REVIEW_PER_NAME];
	$REVIEW_PL_NAME = $data[REVIEW_PL_NAME];
	$REVIEW_PM_NAME = $data[REVIEW_PM_NAME];
	if ($REVIEW_PM_NAME) $REVIEW_PL_NAME = $REVIEW_PM_NAME;
	$REVIEW_LEVEL_NO = $data[REVIEW_LEVEL_NO];
        $SCORE_STATUS_DATE = $data[SCORE_STATUS_DATE];
        $ACCEPT_DATE = $data[ACCEPT_DATE];
	if ($REVIEW_PER_NAME) {
            $cmd = " select RANK_FLAG from PER_PRENAME where trim(PN_NAME)='$REVIEW_PN_NAME' ";
            $db_dpis2->send_cmd($cmd);
            $data2 = $db_dpis2->get_array();
            $RANK_FLAG = trim($data2[RANK_FLAG]);

            if ($RANK_FLAG==1) {
                    $REVIEW_SIGN_NAME = $REVIEW_PER_NAME;
                    $REVIEW_SIGN_PN_NAME = $REVIEW_PN_NAME;
            } else { 
                    $REVIEW_SIGN_NAME = $REVIEW_PN_NAME . $REVIEW_PER_NAME;
                    $REVIEW_SIGN_PN_NAME = "";
            }
            $REVIEW_PER_NAME = $REVIEW_PN_NAME . $REVIEW_PER_NAME;
	}

	$REVIEW_PN_NAME0 = $data[REVIEW_PN_NAME0];
	$REVIEW_PER_NAME0 = $data[REVIEW_PER_NAME0];
	$REVIEW_PL_NAME0 = $data[REVIEW_PL_NAME0];
	$REVIEW_PM_NAME0 = $data[REVIEW_PM_NAME0];
	if ($REVIEW_PM_NAME0) $REVIEW_PL_NAME0 = $REVIEW_PM_NAME0;
	$REVIEW_LEVEL_NO0 = $data[REVIEW_LEVEL_NO0];
	if ($REVIEW_PER_NAME0) {
            $cmd = " select RANK_FLAG from PER_PRENAME where trim(PN_NAME)='$REVIEW_PN_NAME0' ";
            $db_dpis2->send_cmd($cmd);
            $data2 = $db_dpis2->get_array();
            $RANK_FLAG = trim($data2[RANK_FLAG]);

            if ($RANK_FLAG==1) {
                    $REVIEW_SIGN_NAME0 = $REVIEW_PER_NAME0;
                    $REVIEW_SIGN_PN_NAME0 = $REVIEW_PN_NAME0;
            } else { 
                    $REVIEW_SIGN_NAME0 = $REVIEW_PN_NAME0 . $REVIEW_PER_NAME0;
                    $REVIEW_SIGN_PN_NAME0 = "";
            }
            $REVIEW_PER_NAME0 = $REVIEW_PN_NAME0 . $REVIEW_PER_NAME0;
	}
	$REVIEW_PN_NAME1 = $data[REVIEW_PN_NAME1];
	$REVIEW_PER_NAME1 = $data[REVIEW_PER_NAME1];
	$REVIEW_PL_NAME1 = $data[REVIEW_PL_NAME1];
	$REVIEW_PM_NAME1 = $data[REVIEW_PM_NAME1];
	if ($REVIEW_PM_NAME1) $REVIEW_PL_NAME1 = $REVIEW_PM_NAME1;
	$REVIEW_LEVEL_NO1 = $data[REVIEW_LEVEL_NO1];
	if ($REVIEW_PER_NAME1) {
            $cmd = " select RANK_FLAG from PER_PRENAME where trim(PN_NAME)='$REVIEW_PN_NAME1' ";
            $db_dpis2->send_cmd($cmd);
            $data2 = $db_dpis2->get_array();
            $RANK_FLAG = trim($data2[RANK_FLAG]);

            if ($RANK_FLAG==1) {
                    $REVIEW_SIGN_NAME1 = $REVIEW_PER_NAME1;
                    $REVIEW_SIGN_PN_NAME1 = $REVIEW_PN_NAME1;
            } else { 
                    $REVIEW_SIGN_NAME1 = $REVIEW_PN_NAME1 . $REVIEW_PER_NAME1;
                    $REVIEW_SIGN_PN_NAME1 = "";
            }
            $REVIEW_PER_NAME1 = $REVIEW_PN_NAME1 . $REVIEW_PER_NAME1;
	}
	$REVIEW_PN_NAME2 = $data[REVIEW_PN_NAME2];
	$REVIEW_PER_NAME2 = $data[REVIEW_PER_NAME2];
	$REVIEW_PL_NAME2 = trim($data[REVIEW_PL_NAME2]);
	$REVIEW_PM_NAME2 = $data[REVIEW_PM_NAME2];
	if ($REVIEW_PM_NAME2) $REVIEW_PL_NAME2 = $REVIEW_PM_NAME2;
	$REVIEW_LEVEL_NO2 = $data[REVIEW_LEVEL_NO2];
	if ($REVIEW_PER_NAME2) {
            $cmd = " select RANK_FLAG from PER_PRENAME where trim(PN_NAME)='$REVIEW_PN_NAME2' ";
            $db_dpis2->send_cmd($cmd);
            $data2 = $db_dpis2->get_array();
            $RANK_FLAG = trim($data2[RANK_FLAG]);

            if ($RANK_FLAG==1) {
                    $REVIEW_SIGN_NAME2 = $REVIEW_PER_NAME2;
                    $REVIEW_SIGN_PN_NAME2 = $REVIEW_PN_NAME2;
            } else { 
                    $REVIEW_SIGN_NAME2 = $REVIEW_PN_NAME2 . $REVIEW_PER_NAME2;
                    $REVIEW_SIGN_PN_NAME2 = "";
            }
            $REVIEW_PER_NAME2 = $REVIEW_PN_NAME2 . $REVIEW_PER_NAME2;
	}

	if ($KF_SCORE_STATUS==1 || $KPI_SCORE_CONFIRM!=1 || $SESS_USERGROUP == 1 || $PER_ID_REVIEW==$SESS_PER_ID || 
            $PER_ID_REVIEW0==$SESS_PER_ID || $PER_ID_REVIEW1==$SESS_PER_ID || $PER_ID_REVIEW2==$SESS_PER_ID) {
            $SCORE_KPI = $data[SCORE_KPI];
            $SUM_KPI = $data[SUM_KPI];
            $SCORE_COMPETENCE = $data[SCORE_COMPETENCE]; 
            $SUM_COMPETENCE = $data[SUM_COMPETENCE];
            $SCORE_OTHER = $data[SCORE_OTHER];
            $SUM_OTHER = $data[SUM_OTHER];
	}
	$PERFORMANCE_WEIGHT = $data[PERFORMANCE_WEIGHT];
	$COMPETENCE_WEIGHT = $data[COMPETENCE_WEIGHT];
	$OTHER_WEIGHT = $data[OTHER_WEIGHT];
	$KF_STATUS = $data[KF_STATUS];
	$LEVEL_NO = $data[LEVEL_NO];
	$RESULT_COMMENT = $data[RESULT_COMMENT];
	$COMPETENCE_COMMENT = $data[COMPETENCE_COMMENT];
	$SALARY_RESULT = $data[SALARY_RESULT];
	$SALARY_REMARK1 = $data[SALARY_REMARK1];
	$SALARY_REMARK2 = $data[SALARY_REMARK2];
	$AGREE_REVIEW1 = $data[AGREE_REVIEW1];
	$DIFF_REVIEW1 = $data[DIFF_REVIEW1];
	$AGREE_REVIEW2 = $data[AGREE_REVIEW2];
	$DIFF_REVIEW2 = $data[DIFF_REVIEW2];
 
	$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, LEVEL_NO, PER_SALARY, PER_TYPE, POS_ID, POEM_ID, POEMS_ID
					 from		PER_PERSONAL
					 where	PER_ID=$PER_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PN_CODE = trim($data[PN_CODE]);
	$PER_NAME = trim($data[PER_NAME]);
	$PER_SURNAME = trim($data[PER_SURNAME]);
	if (!$PER_SALARY) $PER_SALARY = trim($data[PER_SALARY]);
	if (!$LEVEL_NO) $LEVEL_NO = trim($data[LEVEL_NO]);
	$PER_TYPE = trim($data[PER_TYPE]);
	$POS_ID = trim($data[POS_ID]);
	$POEM_ID = trim($data[POEM_ID]);
	$POEMS_ID = trim($data[POEMS_ID]);
	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_KPI ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$ORG_NAME = trim($data[ORG_NAME]);
	$cmd = " select PN_NAME, RANK_FLAG from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PN_NAME = trim($data[PN_NAME]);
	$RANK_FLAG = trim($data[RANK_FLAG]);
	if ($RANK_FLAG==1) {
		$SIGN_NAME = $PER_NAME . " " . $PER_SURNAME;
		$SIGN_PN_NAME = $PN_NAME;
	} else { 
		$SIGN_NAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;
		$SIGN_PN_NAME = "";
	}
	$PER_NAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;
	$cmd = " select LEVEL_NAME, POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$LEVEL_NO' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$LEVEL_NAME = trim($data[LEVEL_NAME]);
	$POSITION_TYPE = trim($data[POSITION_TYPE]);
	$POSITION_LEVEL = trim($data[POSITION_LEVEL]);
        $view_ORG_NAME = "";
        if ($view_ORG_ID) {
            $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $view_ORG_ID ";
            if($SESS_ORG_STRUCTURE==1) {
                    $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
            }
            $db_dpis2->send_cmd($cmd);
            $data2 = $db_dpis2->get_array();
            $view_ORG_NAME = trim($data2[ORG_NAME]);
            if ($view_ORG_NAME=="-") $view_ORG_NAME = "";
        }
        $ORG_NAME = $view_ORG_NAME;	
        if (!$REVIEW_PER_NAME || !$REVIEW_PL_NAME || !$REVIEW_LEVEL_NO) {
		if($PER_ID_REVIEW){
			$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO , PER_CARDNO
							 from		PER_PERSONAL
							 where	PER_ID=$PER_ID_REVIEW ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PN_CODE = trim($data[PN_CODE]);
			$REVIEW_PER_NAME = trim($data[PER_NAME]);
			$REVIEW_PER_SURNAME = trim($data[PER_SURNAME]);
			$REVIEW_PER_TYPE = trim($data[PER_TYPE]);
			$REVIEW_POS_ID = trim($data[POS_ID]);
			$REVIEW_POEM_ID = trim($data[POEM_ID]);
			$REVIEW_POEMS_ID = trim($data[POEMS_ID]);
			if (!$REVIEW_LEVEL_NO) $REVIEW_LEVEL_NO = trim($data[LEVEL_NO]);
			$PER_CARDNO_REVIEW = trim($data[PER_CARDNO]);

			$cmd = " select PN_NAME, RANK_FLAG from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PN_NAME = trim($data[PN_NAME]);
			$RANK_FLAG = trim($data[RANK_FLAG]);
			
			if ($RANK_FLAG==1) {
				$REVIEW_SIGN_NAME = $REVIEW_PER_NAME . " " . $REVIEW_PER_SURNAME;
				$REVIEW_SIGN_PN_NAME = $REVIEW_PN_NAME;
			} else { 
				$REVIEW_SIGN_NAME = $REVIEW_PN_NAME . $REVIEW_PER_NAME . " " . $REVIEW_PER_SURNAME;
				$REVIEW_SIGN_PN_NAME = "";
			}
			$REVIEW_PER_NAME = $REVIEW_PN_NAME . $REVIEW_PER_NAME . " " . $REVIEW_PER_SURNAME;
			 
			if (!$REVIEW_PL_NAME) {
                          
				if($REVIEW_PER_TYPE==1){
					$cmd = " select 	b.PL_NAME, a.PT_CODE, a.PM_CODE 
									 from 		PER_POSITION a, PER_LINE b
									 where	a.POS_ID=$REVIEW_POS_ID and a.PL_CODE=b.PL_CODE ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					if (!$REVIEW_PL_NAME) $REVIEW_PL_NAME = trim($data[PL_NAME]);
					$REVIEW_PT_CODE = trim($data[PT_CODE]);
					$REVIEW_PT_NAME = trim($data[PT_NAME]);
					$REVIEW_PM_CODE = trim($data[PM_CODE]);
                                        
                                        
					if($REVIEW_PM_CODE && !$REVIEW_PM_NAME){
						$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$REVIEW_PM_CODE' ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
						$REVIEW_PL_NAME = $data[PM_NAME]; 
					} // end if
                                       
				}elseif($REVIEW_PER_TYPE==2){
					$cmd = " select 	b.PN_NAME
									 from 		PER_POS_EMP a, PER_POS_NAME b
									 where	a.POEM_ID=$REVIEW_POEM_ID and a.PN_CODE=b.PN_CODE ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					if (!$REVIEW_PL_NAME) $REVIEW_PL_NAME = trim($data[PN_NAME]);
				}elseif($REVIEW_PER_TYPE==3){
					$cmd = " select 	b.EP_NAME
									 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
									 where	a.POEMS_ID=$REVIEW_POEMS_ID and a.EP_CODE=b.EP_CODE ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					if (!$REVIEW_PL_NAME) $REVIEW_PL_NAME = trim($data[EP_NAME]);
				} // end if
                                
			} // end if
		} // end if
	} // end if
        
	$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$REVIEW_LEVEL_NO' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_LEVEL_NAME = trim($data[LEVEL_NAME]);
	$REVIEW_POSITION_LEVEL = $data[POSITION_LEVEL];
	if (!$REVIEW_POSITION_LEVEL) $REVIEW_POSITION_LEVEL = $REVIEW_LEVEL_NAME;
		
	if (!$REVIEW_PER_NAME0 || !$REVIEW_PL_NAME0 || !$REVIEW_LEVEL_NO0) {
		if($PER_ID_REVIEW0){
			$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO , PER_CARDNO
							 from		PER_PERSONAL
							 where	PER_ID=$PER_ID_REVIEW0 ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PN_CODE0 = trim($data[PN_CODE]);
			$REVIEW_PER_NAME0 = trim($data[PER_NAME]);
			$REVIEW_PER_SURNAME0 = trim($data[PER_SURNAME]);
			$REVIEW_PER_TYPE0 = trim($data[PER_TYPE]);
			$REVIEW_POS_ID0 = trim($data[POS_ID]);
			$REVIEW_POEM_ID0 = trim($data[POEM_ID]);
			$REVIEW_POEMS_ID0 = trim($data[POEMS_ID]);
			if (!$REVIEW_LEVEL_NO0) $REVIEW_LEVEL_NO0 = trim($data[LEVEL_NO]);
			$PER_CARDNO_REVIEW0 = trim($data[PER_CARDNO]);
			$cmd = " select PN_NAME, RANK_FLAG from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE0' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PN_NAME0 = trim($data[PN_NAME]);
			$RANK_FLAG = trim($data[RANK_FLAG]);
			if ($RANK_FLAG==1) {
				$REVIEW_SIGN_NAME0 = $REVIEW_PER_NAME0 . " " . $REVIEW_PER_SURNAME0;
				$REVIEW_SIGN_PN_NAME0 = $REVIEW_PN_NAME0;
			} else {
				$REVIEW_SIGN_NAME0 = $REVIEW_PN_NAME0 . $REVIEW_PER_NAME0 . " " . $REVIEW_PER_SURNAME0;
				$REVIEW_SIGN_PN_NAME0 = "";
			}
			$REVIEW_PER_NAME0 = $REVIEW_PN_NAME0 . $REVIEW_PER_NAME0 . " " . $REVIEW_PER_SURNAME0;
			
			if (!$REVIEW_PL_NAME0) {
				if($REVIEW_PER_TYPE0==1){
					$cmd = " select 	b.PL_NAME, a.PT_CODE, a.PM_CODE 
									 from 		PER_POSITION a, PER_LINE b
									 where	a.POS_ID=$REVIEW_POS_ID0 and a.PL_CODE=b.PL_CODE ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					if (!$REVIEW_PL_NAME0) $REVIEW_PL_NAME0 = trim($data[PL_NAME]);
					$REVIEW_PT_CODE0 = trim($data[PT_CODE]);
					$REVIEW_PT_NAME0 = trim($data[PT_NAME]);
					$REVIEW_PL_NAME0 = trim($REVIEW_PL_NAME0)?($REVIEW_PL_NAME0 . $REVIEW_POSITION_LEVEL0 . (($REVIEW_PT_NAME0 != "ทั่วไป" && $REVIEW_LEVEL_NO0 >= 6)?"$REVIEW_PT_NAME0":"")):$REVIEW_LEVEL_NAME0;
					$REVIEW_PM_CODE0 = trim($data[PM_CODE]);
					if($REVIEW_PM_CODE0 && !$REVIEW_PM_NAME0){
						$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$REVIEW_PM_CODE0' ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
	//					$REVIEW_PL_NAME0 = $data[PM_NAME]." ($REVIEW_PL_NAME0)"; 
						$REVIEW_PL_NAME0 = $data[PM_NAME]; 
					} // end if
				}elseif($REVIEW_PER_TYPE0==2){
					$cmd = " select 	b.PN_NAME
									 from 		PER_POS_EMP a, PER_POS_NAME b
									 where	a.POEM_ID=$REVIEW_POEM_ID0 and a.PN_CODE=b.PN_CODE ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					if (!$REVIEW_PL_NAME0) $REVIEW_PL_NAME0 = trim($data[PN_NAME]);
				}elseif($REVIEW_PER_TYPE==3){
					$cmd = " select 	b.EP_NAME
									 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
									 where	a.POEMS_ID=$REVIEW_POEMS_ID and a.EP_CODE=b.EP_CODE ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					if (!$REVIEW_PL_NAME0) $REVIEW_PL_NAME0 = trim($data[EP_NAME]);
				} // end if
			} // end if
		} // end if
	} // end if
	
	$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$REVIEW_LEVEL_NO0' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_LEVEL_NAME0 = trim($data[LEVEL_NAME]);
	$REVIEW_POSITION_LEVEL0 = $data[POSITION_LEVEL];
	if (!$REVIEW_POSITION_LEVEL0) $REVIEW_POSITION_LEVEL0 = $REVIEW_LEVEL_NAME0;
		
	if (!$REVIEW_PER_NAME1 || !$REVIEW_PL_NAME1 || !$REVIEW_LEVEL_NO1) {
		if($PER_ID_REVIEW1){
			$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, PER_CARDNO
							 from		PER_PERSONAL
							 where	PER_ID=$PER_ID_REVIEW1 ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PN_CODE1 = trim($data[PN_CODE]);
			$REVIEW_PER_NAME1 = trim($data[PER_NAME]);
			$REVIEW_PER_SURNAME1 = trim($data[PER_SURNAME]);
			$REVIEW_PER_TYPE1 = trim($data[PER_TYPE]);
			$REVIEW_POS_ID1 = trim($data[POS_ID]);
			$REVIEW_POEM_ID1 = trim($data[POEM_ID]);
			$REVIEW_POEMS_ID1 = trim($data[POEMS_ID]);
			if (!$REVIEW_LEVEL_NO1) $REVIEW_LEVEL_NO1 = trim($data[LEVEL_NO]);
			$PER_CARDNO_REVIEW1 = trim($data[PER_CARDNO]);	

			$cmd = " select PN_NAME, RANK_FLAG from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE1' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PN_NAME1 = trim($data[PN_NAME]);
			$RANK_FLAG = trim($data[RANK_FLAG]);
			
			if ($RANK_FLAG==1) {
				$REVIEW_SIGN_NAME1 = $REVIEW_PER_NAME1 . " " . $REVIEW_PER_SURNAME1;
				$REVIEW_SIGN_PN_NAME1 = $REVIEW_PN_NAME1;
			} else {
				$REVIEW_SIGN_NAME1 = $REVIEW_PN_NAME1 . $REVIEW_PER_NAME1 . " " . $REVIEW_PER_SURNAME1;
				$REVIEW_SIGN_PN_NAME1 = "";
			}
			$REVIEW_PER_NAME1 = $REVIEW_PN_NAME1 . $REVIEW_PER_NAME1 . " " . $REVIEW_PER_SURNAME1;
			
			if (!$REVIEW_PL_NAME1) {
				if($REVIEW_PER_TYPE1==1){
					$cmd = " select 	b.PL_NAME, a.PT_CODE, a.PM_CODE 
									 from 		PER_POSITION a, PER_LINE b
									 where	a.POS_ID=$REVIEW_POS_ID1 and a.PL_CODE=b.PL_CODE ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					if (!$REVIEW_PL_NAME1) $REVIEW_PL_NAME1 = trim($data[PL_NAME]);
					$REVIEW_PT_CODE1 = trim($data[PT_CODE]);
					$REVIEW_PT_NAME1 = trim($data[PT_NAME]);
					$REVIEW_PL_NAME1 = trim($REVIEW_PL_NAME1)?($REVIEW_PL_NAME1 . $REVIEW_POSITION_LEVEL1 . (($REVIEW_PT_NAME1 != "ทั่วไป" && $REVIEW_LEVEL_NO1 >= 6)?"$REVIEW_PT_NAME1":"")):$REVIEW_LEVEL_NAME1;
					$REVIEW_PM_CODE1 = trim($data[PM_CODE]);
					if($REVIEW_PM_CODE1 && !$REVIEW_PM_NAME1){
						$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$REVIEW_PM_CODE1' ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
			//			$REVIEW_PL_NAME1 = $data[PM_NAME]." ($REVIEW_PL_NAME1)";
						$REVIEW_PL_NAME1 = $data[PM_NAME];
					} // end if
				}elseif($REVIEW_PER_TYPE1==2){
					$cmd = " select 	b.PN_NAME
									 from 		PER_POS_EMP a, PER_POS_NAME b
									 where	a.POEM_ID=$REVIEW_POEM_ID1 and a.PN_CODE=b.PN_CODE ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					if (!$REVIEW_PL_NAME1) $REVIEW_PL_NAME1 = trim($data[PN_NAME]);
				}elseif($REVIEW_PER_TYPE1==3){
					$cmd = " select 	b.EP_NAME
									 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
									 where	a.POEMS_ID=$REVIEW_POEMS_ID1 and a.EP_CODE=b.EP_CODE ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					if (!$REVIEW_PL_NAME1) $REVIEW_PL_NAME1 = trim($data[EP_NAME]);
				} // end if
			} // end if
		} // end if
	} // end if
	
	/*5.2.1.10*/
						if($SCORE_STATUS_DATE){
					   //วันที่ ติกให้เห็นคะเเนน 
                       $SCORE_COVERT_1 = substr($SCORE_STATUS_DATE, 0, 10);
                       $SCORE_COVERT   =  explode("-",$SCORE_COVERT_1);
                       $SCORE_DAY      =  $SCORE_COVERT[2];
                       $SCORE_MONT     = $SCORE_COVERT[1];
                       $SCORE_YEAR     = $SCORE_COVERT[0]+543;
						}
						if($ACCEPT_DATE){
                       //วันที่ยอมรับคะเเนน การประเมิน
                       $ACCEPT_COVERT_1 = substr($ACCEPT_DATE, 0, 10);
                       $ACCEPT_COVERT   =  explode("-",$ACCEPT_COVERT_1);
                       $ACCEPT_DAY      = $ACCEPT_COVERT[2];
                       $ACCEPT_MONTH    = $ACCEPT_COVERT[1];
                       $ACCEPT_YEAR     = $ACCEPT_COVERT[0]+543;
						}
                    if($DATE_REVIEW1){
                        // วันที่เเสดงความคิดเห็น เหนือขึ้นไป
                        $DATE_REVIEW1_COVERT_1 = substr($DATE_REVIEW1, 0, 10);
                        $DATE_REVIEW1_COVERT   =  explode("-",$DATE_REVIEW1_COVERT_1);
                        $DATE_REVIEW1_DAY      = $DATE_REVIEW1_COVERT[2];
                        $DATE_REVIEW1_MONTH    = $DATE_REVIEW1_COVERT[1];
                        $DATE_REVIEW1_YEAR     = $DATE_REVIEW1_COVERT[0]+543;
                    }
					   
                    if($DATE_REVIEW2){
                        //คะเเนน วันที่เเสดงความคิดเห็น เหนือขึ้นไป1ขั้น
                        $DATE_REVIEW2_COVERT_2 = substr($DATE_REVIEW2, 0, 10);
                        $DATE_REVIEW2_COVERT   =  explode("-",$DATE_REVIEW2_COVERT_2);
                        $DATE_REVIEW2_DAY      = $DATE_REVIEW2_COVERT[2];
                        $DATE_REVIEW2_MONTH    = $DATE_REVIEW2_COVERT[1];
                        $DATE_REVIEW2_YEAR     = $DATE_REVIEW2_COVERT[0]+543;
                    }
                    if($DATE_REVIEW0){
                        //คะเเนน วันที่เเสดงความคิดเห็น เหนือขึ้นไป1ขั้น
                        $DATE_REVIEW0_COVERT_0 = substr($DATE_REVIEW0, 0, 10);
                        $DATE_REVIEW0_COVERT   =  explode("-",$DATE_REVIEW0_COVERT_0);
                        $DATE_REVIEW0_DAY      = $DATE_REVIEW0_COVERT[2];
                        $DATE_REVIEW0_MONTH    = $DATE_REVIEW0_COVERT[1];
                        $DATE_REVIEW0_YEAR     = $DATE_REVIEW0_COVERT[0]+543;
                    }				 
	if($PER_ID_REVIEW) $PER_CARDNO_REVIEW_N = get_cardno($PER_ID_REVIEW);
	if($PER_ID_REVIEW1)$PER_CARDNO_REVIEW1  = get_cardno($PER_ID_REVIEW1);
	if($PER_ID_REVIEW2)$PER_CARDNO_REVIEW2  = get_cardno($PER_ID_REVIEW2);
	
	
	/*5.2.1.10*/
	
	
	$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$REVIEW_LEVEL_NO1' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_LEVEL_NAME1 = trim($data[LEVEL_NAME]);
	$REVIEW_POSITION_LEVEL1 = $data[POSITION_LEVEL];
	if (!$REVIEW_POSITION_LEVEL1) $REVIEW_POSITION_LEVEL1 = $REVIEW_LEVEL_NAME1;

	if (!$REVIEW_PER_NAME2 || !$REVIEW_PL_NAME2 || !$REVIEW_LEVEL_NO2) {
		if($PER_ID_REVIEW2){
			$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, PER_CARDNO
							 from		PER_PERSONAL
							 where	PER_ID=$PER_ID_REVIEW2 ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PN_CODE2 = trim($data[PN_CODE]);
			$REVIEW_PER_NAME2 = trim($data[PER_NAME]);
			$REVIEW_PER_SURNAME2 = trim($data[PER_SURNAME]);
			$REVIEW_PER_TYPE2 = trim($data[PER_TYPE]);
			$REVIEW_POS_ID2 = trim($data[POS_ID]);
			$REVIEW_POEM_ID2 = trim($data[POEM_ID]);
			$REVIEW_POEMS_ID2 = trim($data[POEMS_ID]);
			if (!$REVIEW_LEVEL_NO2) $REVIEW_LEVEL_NO2 = trim($data[LEVEL_NO]);
			$PER_CARDNO_REVIEW2 = trim($data[PER_CARDNO]);	

			$cmd = " select PN_NAME, RANK_FLAG from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE2' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PN_NAME2 = trim($data[PN_NAME]);
			$RANK_FLAG = trim($data[RANK_FLAG]);
			
			if ($RANK_FLAG==1) {
				$REVIEW_SIGN_NAME2 = $REVIEW_PER_NAME2 . " " . $REVIEW_PER_SURNAME2;
				$REVIEW_SIGN_PN_NAME2 = $REVIEW_PN_NAME2;
			} else {
				$REVIEW_SIGN_NAME2 = $REVIEW_PN_NAME2 . $REVIEW_PER_NAME2 . " " . $REVIEW_PER_SURNAME2;
				$REVIEW_SIGN_PN_NAME2 = "";
			}
			$REVIEW_PER_NAME2 = $REVIEW_PN_NAME2 . $REVIEW_PER_NAME2 . " " . $REVIEW_PER_SURNAME2;
			
			if (!$REVIEW_PL_NAME2) {
				if($REVIEW_PER_TYPE2==1){
					$cmd = " select 	b.PL_NAME, a.PT_CODE, a.PM_CODE 
									 from 		PER_POSITION a, PER_LINE b
									 where	a.POS_ID=$REVIEW_POS_ID2 and a.PL_CODE=b.PL_CODE ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					if (!$REVIEW_PL_NAME2) $REVIEW_PL_NAME2 = trim($data[PL_NAME]);
					$REVIEW_PT_CODE2 = trim($data[PT_CODE]);
					$REVIEW_PT_NAME2 = trim($data[PT_NAME]);
					$REVIEW_PL_NAME2 = trim($REVIEW_PL_NAME2)?($REVIEW_PL_NAME2 . $REVIEW_POSITION_LEVEL2 . (($REVIEW_PT_NAME2 != "ทั่วไป" && $REVIEW_LEVEL_NO2 >= 6)?"$REVIEW_PT_NAME2":"")):$REVIEW_LEVEL_NAME2;
					$REVIEW_PM_CODE2 = trim($data[PM_CODE]);
					if($REVIEW_PM_CODE2 && !$REVIEW_PM_NAME2){
						$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$REVIEW_PM_CODE2' ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
			//			$REVIEW_PL_NAME2 = $data[PM_NAME]." ($REVIEW_PL_NAME2)";
						$REVIEW_PL_NAME2 = $data[PM_NAME];
					} // end if 
				}elseif($REVIEW_PER_TYPE2==2){
					$cmd = " select 	b.PN_NAME
									 from 		PER_POS_EMP a, PER_POS_NAME b
									 where	a.POEM_ID=$REVIEW_POEM_ID2 and a.PN_CODE=b.PN_CODE ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					if (!$REVIEW_PL_NAME2) $REVIEW_PL_NAME2 = trim($data[PN_NAME]);
				}elseif($REVIEW_PER_TYPE2==3){
					$cmd = " select 	b.EP_NAME
									 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
									 where	a.POEMS_ID=$REVIEW_POEMS_ID2 and a.EP_CODE=b.EP_CODE ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					if (!$REVIEW_PL_NAME2) $REVIEW_PL_NAME2 = trim($data[EP_NAME]);
				} // end if
			} // end if
		}
	} // end if

	$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$REVIEW_LEVEL_NO2' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$REVIEW_LEVEL_NAME2 = trim($data[LEVEL_NAME]);
	$REVIEW_POSITION_LEVEL2 = $data[POSITION_LEVEL];
	if (!$REVIEW_POSITION_LEVEL2) $REVIEW_POSITION_LEVEL2 = $REVIEW_LEVEL_NAME2;

	if($DPISDB=="odbc") 
		$cmd = " select 	a.PG_ID, a.PG_SEQ, a.KPI_NAME, c.PFR_NAME, a.KPI_WEIGHT, a.PG_RESULT, a.PG_EVALUATE,
							a.KPI_TARGET_LEVEL1, a.KPI_TARGET_LEVEL2, a.KPI_TARGET_LEVEL3, a.KPI_TARGET_LEVEL4, a.KPI_TARGET_LEVEL5,
							a.KPI_TARGET_LEVEL1_DESC, a.KPI_TARGET_LEVEL2_DESC, a.KPI_TARGET_LEVEL3_DESC, a.KPI_TARGET_LEVEL4_DESC, a.KPI_TARGET_LEVEL5_DESC
				   from		(
									PER_PERFORMANCE_GOALS a
									left join PER_KPI b on (a.KPI_ID=b.KPI_ID)
								) left join PER_PERFORMANCE_REVIEW c on (b.PFR_ID=c.PFR_ID)
				   where 	a.KF_ID=$KF_ID
				   order by	a.PG_SEQ ";
	elseif($DPISDB=="oci8") 
		$cmd = " select 	a.PG_ID, a.PG_SEQ, a.KPI_NAME, c.PFR_NAME, a.KPI_WEIGHT, a.PG_RESULT, a.PG_EVALUATE,
							a.KPI_TARGET_LEVEL1, a.KPI_TARGET_LEVEL2, a.KPI_TARGET_LEVEL3, a.KPI_TARGET_LEVEL4, a.KPI_TARGET_LEVEL5,
							a.KPI_TARGET_LEVEL1_DESC, a.KPI_TARGET_LEVEL2_DESC, a.KPI_TARGET_LEVEL3_DESC, a.KPI_TARGET_LEVEL4_DESC, a.KPI_TARGET_LEVEL5_DESC,
                                                        a.PG_REMARK
				   from 		PER_PERFORMANCE_GOALS a, PER_KPI b, PER_PERFORMANCE_REVIEW c
				   where 	a.KF_ID=$KF_ID and a.KPI_ID=b.KPI_ID(+) and b.PFR_ID=c.PFR_ID(+)
				   order by	a.PG_SEQ ";
	elseif($DPISDB=="mysql")
		$cmd = " select 	a.PG_ID, a.PG_SEQ, a.KPI_NAME, c.PFR_NAME, a.KPI_WEIGHT, a.PG_RESULT, a.PG_EVALUATE,
							a.KPI_TARGET_LEVEL1, a.KPI_TARGET_LEVEL2, a.KPI_TARGET_LEVEL3, a.KPI_TARGET_LEVEL4, a.KPI_TARGET_LEVEL5,
							a.KPI_TARGET_LEVEL1_DESC, a.KPI_TARGET_LEVEL2_DESC, a.KPI_TARGET_LEVEL3_DESC, a.KPI_TARGET_LEVEL4_DESC, a.KPI_TARGET_LEVEL5_DESC
				   from		(
									PER_PERFORMANCE_GOALS a
									left join PER_KPI b on (a.KPI_ID=b.KPI_ID)
								) left join PER_PERFORMANCE_REVIEW c on (b.PFR_ID=c.PFR_ID)
				   where 	a.KF_ID=$KF_ID
				   order by	a.PG_SEQ ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$PG_ID = $data[PG_ID];
		$PG_SEQ = $data[PG_SEQ];
		$PFR_NAME = $data[PFR_NAME];
		$KPI_NAME = $data[KPI_NAME];
		$KPI_WEIGHT = $data[KPI_WEIGHT];
		$KPI_TARGET_LEVEL1 = $data[KPI_TARGET_LEVEL1];
		$KPI_TARGET_LEVEL2 = $data[KPI_TARGET_LEVEL2];
		$KPI_TARGET_LEVEL3 = $data[KPI_TARGET_LEVEL3];
		$KPI_TARGET_LEVEL4 = $data[KPI_TARGET_LEVEL4];
		$KPI_TARGET_LEVEL5 = $data[KPI_TARGET_LEVEL5];
		$KPI_TARGET_LEVEL1_DESC = $data[KPI_TARGET_LEVEL1_DESC];
		$KPI_TARGET_LEVEL2_DESC = $data[KPI_TARGET_LEVEL2_DESC];
		$KPI_TARGET_LEVEL3_DESC = $data[KPI_TARGET_LEVEL3_DESC];
		$KPI_TARGET_LEVEL4_DESC = $data[KPI_TARGET_LEVEL4_DESC];
		$KPI_TARGET_LEVEL5_DESC = $data[KPI_TARGET_LEVEL5_DESC];
		$PG_RESULT = $data[PG_RESULT];
		if ($KF_SCORE_STATUS==1 || $KPI_SCORE_CONFIRM!=1 || $SESS_USERGROUP == 1 || $PER_ID_REVIEW==$SESS_PER_ID || 
			$PER_ID_REVIEW0==$SESS_PER_ID || $PER_ID_REVIEW1==$SESS_PER_ID || $PER_ID_REVIEW2==$SESS_PER_ID) {
			$PG_EVALUATE = $data[PG_EVALUATE];
		}
                $KPI_PG_REMARK = $data[PG_REMARK];
		
		$ARR_KPI[$PG_ID] = $KPI_NAME;
		$ARR_KPI_SEQ[$PG_ID] = $PG_SEQ;
		$ARR_KPI_PFR[$PG_ID] = $PFR_NAME;
		$ARR_KPI_WEIGHT[$PG_ID] = $KPI_WEIGHT;
		$ARR_KPI_TARGET1[$PG_ID] = $KPI_TARGET_LEVEL1;
		$ARR_KPI_TARGET2[$PG_ID] = $KPI_TARGET_LEVEL2;
		$ARR_KPI_TARGET3[$PG_ID] = $KPI_TARGET_LEVEL3;
		$ARR_KPI_TARGET4[$PG_ID] = $KPI_TARGET_LEVEL4;
		$ARR_KPI_TARGET5[$PG_ID] = $KPI_TARGET_LEVEL5;
		$ARR_KPI_TARGET1_DESC[$PG_ID] = $KPI_TARGET_LEVEL1_DESC;
		$ARR_KPI_TARGET2_DESC[$PG_ID] = $KPI_TARGET_LEVEL2_DESC;
		$ARR_KPI_TARGET3_DESC[$PG_ID] = $KPI_TARGET_LEVEL3_DESC;
		$ARR_KPI_TARGET4_DESC[$PG_ID] = $KPI_TARGET_LEVEL4_DESC;
		$ARR_KPI_TARGET5_DESC[$PG_ID] = $KPI_TARGET_LEVEL5_DESC;
		$ARR_KPI_RESULT[$PG_ID] = $PG_RESULT;
		$ARR_KPI_EVALUATE[$PG_ID] = $PG_EVALUATE;
                $ARR_KPI_PG_REMARK[$PG_ID] = $KPI_PG_REMARK; /*เพิ่มมานะจ๊ะ...*/
		
//		$TOTAL_KPI_EVALUATE += $PG_EVALUATE;
		$TOTAL_KPI_EVALUATE += ($PG_EVALUATE * $KPI_WEIGHT);
		$TOTAL_KPI_WEIGHT += $KPI_WEIGHT;
	} // end while
	
	$COUNT_KPI = count($ARR_KPI);

	//สมรรถนะ
	$TOTAL_PC_SCORE = 0;
	//หา DEPARTMENT_ID ของ KF_ID ถ้าไม่ใส่เงื่อนไขนี้ กรณีจังหวัดมันจะ sum ทุก DEPARTMENT_ID ทำให้ผลรวมผิด
	$cmd = " select 	KF_END_DATE, KF_CYCLE, PER_ID, DEPARTMENT_ID
			   from 		PER_KPI_FORM 
			   where 	KF_ID = $KF_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$DEPARTMENT_ID = $data[DEPARTMENT_ID];
	
	$cmd = " select 	a.KC_ID, a.CP_CODE, b.CP_NAME, b.CP_MODEL, a.KC_EVALUATE, a.KC_WEIGHT, a.PC_TARGET_LEVEL,a.KC_REMARK
			   from 		PER_KPI_COMPETENCE a, PER_COMPETENCE b
			   where 	a.KF_ID=$KF_ID and a.CP_CODE=b.CP_CODE(+) and b.DEPARTMENT_ID=$DEPARTMENT_ID 
			   order by 	a.CP_CODE ";
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	while($data = $db_dpis->get_array()){
		$KC_ID = $data[KC_ID];
		$CP_CODE = $data[CP_CODE];
		$CP_NAME = $data[CP_NAME];
		$CP_MODEL = $data[CP_MODEL];
                $KC_REMARK= $data[KC_REMARK];
		if ($KF_SCORE_STATUS==1 || $KPI_SCORE_CONFIRM!=1 || $SESS_USERGROUP == 1 || $PER_ID_REVIEW==$SESS_PER_ID || 
			$PER_ID_REVIEW0==$SESS_PER_ID || $PER_ID_REVIEW1==$SESS_PER_ID || $PER_ID_REVIEW2==$SESS_PER_ID) {
			$KC_EVALUATE = $data[KC_EVALUATE];
		}
		$KC_WEIGHT = $data[KC_WEIGHT];
		$PC_TARGET_LEVEL = $data[PC_TARGET_LEVEL];
		
		$ARR_COMPETENCE[$KC_ID] = $CP_NAME.(($CP_MODEL==2)?" *":"");
		$ARR_COMPETENCE_TARGET[$KC_ID] = $PC_TARGET_LEVEL;
		$ARR_COMPETENCE_EVALUATE[$KC_ID] =   $KC_EVALUATE;//เดิม $KC_EVALUATE;
		$ARR_COMPETENCE_WEIGHT[$KC_ID]=$KC_WEIGHT;
                $ARR_KC_REMARK[$KC_ID]=$KC_REMARK;
		//เพื่อแสดงคะแนนในส่วน แบบประเมินการสรุปสมรรถนะ 					COMPETENCY_SCALE=2  Rating / COMPETENCY_SCALE=5 Rating เท่า
		$PC_SCORE[$KC_ID] = "";
		if ($COMPETENCY_SCALE==1) {	
			if($KC_EVALUATE > 0){	
				if($KC_EVALUATE >= $PC_TARGET_LEVEL) $PC_SCORE[$KC_ID] = 3;
				elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) <= 1) $PC_SCORE[$KC_ID] = 2;
				elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) <= 2) $PC_SCORE[$KC_ID] = 1;
				elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) <= 3) $PC_SCORE[$KC_ID] = 0;
				else $PC_SCORE[$KC_ID] = 0;
			} else $KC_EVALUATE = "";
 		} elseif ($COMPETENCY_SCALE==2 || $COMPETENCY_SCALE==5) {		
			$PC_SCORE[$KC_ID] = $KC_EVALUATE * $KC_WEIGHT / 100;
		} elseif ($COMPETENCY_SCALE==3) {		
			$PC_SCORE[$KC_ID] = $KC_EVALUATE;
		}
		if($PC_SCORE[$KC_ID])	$TOTAL_PC_SCORE += $PC_SCORE[$KC_ID]; 
		if ($COMPETENCY_SCALE==2 || $COMPETENCY_SCALE==5){
		 	$PC_SCORE[$KC_ID] = $PC_SCORE[$KC_ID];		
			$TOTAL_PC_SCORE =  $TOTAL_PC_SCORE;	
		}
		//----------------------------------------------------------------------
		
		if($KC_EVALUATE != ""){
			if($KC_EVALUATE >= $PC_TARGET_LEVEL) $ARR_COMPETENCE_COUNT[GE] += 1;
			elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) == 1) $ARR_COMPETENCE_COUNT[L1] += 1;
			elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) == 2) $ARR_COMPETENCE_COUNT[L2] += 1;
			elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) == 3) $ARR_COMPETENCE_COUNT[L3] += 1;
			else $ARR_COMPETENCE_COUNT[L3] += 1;
		} // end if
	} // end while
	
	$COUNT_COMPETENCE = count($ARR_COMPETENCE);

	$ARR_COMPETENCE_SCORE[GE] = $ARR_COMPETENCE_COUNT[GE] * 3;
	$ARR_COMPETENCE_SCORE[L1] = $ARR_COMPETENCE_COUNT[L1] * 2;
	$ARR_COMPETENCE_SCORE[L2] = $ARR_COMPETENCE_COUNT[L2] * 1;
	$ARR_COMPETENCE_SCORE[L3] = $ARR_COMPETENCE_COUNT[L3] * 0;
	
	$TOTAL_COMPETENCE_SCORE = "";
	if($ARR_COMPETENCE_COUNT[GE] || $ARR_COMPETENCE_COUNT[L1] || $ARR_COMPETENCE_COUNT[L2] || $ARR_COMPETENCE_COUNT[L3]) 
		$TOTAL_COMPETENCE_SCORE = array_sum($ARR_COMPETENCE_SCORE);
		
	$cmd = " select 	CP_CODE from PER_KPI_COMPETENCE where KF_ID=$KF_ID ";
	$COUNT_COMPETENCE = $db_dpis->send_cmd($cmd);
	$FULL_SCORE = 5;

	$cmd = " select 	sum(PC_TARGET_LEVEL) as SUM_TARGET_LEVEL from PER_KPI_COMPETENCE where KF_ID=$KF_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$SUM_TARGET_LEVEL =$data[SUM_TARGET_LEVEL];
        
//	$SHOW_SCORE_KPI = round(round(($SCORE_KPI / ($COUNT_KPI * 5)), 3), 2);
///*เดิม*/$SHOW_SCORE_KPI = round(round(($SCORE_KPI / ($TOTAL_KPI_WEIGHT * 5)), 3), 2);
        
        $SCORE_KPI_T = ($SCORE_KPI / $TOTAL_KPI_WEIGHT);
        $SCORE_COMPETENCE_T = ($SCORE_COMPETENCE / 100);
        
        $SHOW_SCORE_KPI = round(round((($SCORE_KPI_T * $TOTAL_KPI_WEIGHT) / 5), 3), 2);
//	$SHOW_SCORE_COMPETENCE = round(round(($SCORE_COMPETENCE / ($COUNT_COMPETENCE * 3)), 3), 2);
//		$SHOW_SCORE_COMPETENCE = round(round(($SCORE_COMPETENCE / 5), 3), 2);
	if ($COMPETENCY_SCALE==1)
		$SHOW_SCORE_COMPETENCE = round(round(($SCORE_COMPETENCE / ($COUNT_COMPETENCE * 3)), 3), 2);
	elseif ($COMPETENCY_SCALE==2 || $COMPETENCY_SCALE==5 || $COMPETENCY_SCALE==6)
		$SHOW_SCORE_COMPETENCE = round(round((($SCORE_COMPETENCE * $TOTAL_KPI_WEIGHT) / 5), 3), 2);
	elseif ($COMPETENCY_SCALE==3)
		$SHOW_SCORE_COMPETENCE = round(round(($SCORE_COMPETENCE / $SUM_TARGET_LEVEL), 3), 2);
	$SHOW_SCORE_OTHER = $SCORE_OTHER;

	$SUM_TOTAL = $SUM_KPI + $SUM_COMPETENCE + $SUM_OTHER;
	
	$cmd = " select 	IPIP_ID, DEVELOP_SEQ, DEVELOP_COMPETENCE, DEVELOP_METHOD, DEVELOP_INTERVAL, DEVELOP_EVALUATE
			   from 		PER_IPIP
			   where 	KF_ID=$KF_ID
			   order by	DEVELOP_SEQ ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$IPIP_ID = $data[IPIP_ID];
		$DEVELOP_SEQ = $data[DEVELOP_SEQ];
		$DEVELOP_COMPETENCE = $data[DEVELOP_COMPETENCE];
		$DEVELOP_METHOD = $data[DEVELOP_METHOD];
		$DEVELOP_INTERVAL = $data[DEVELOP_INTERVAL];
		
		$ARR_IPIP[$IPIP_ID] = $DEVELOP_COMPETENCE;
		$ARR_IPIP_METHOD[$IPIP_ID] = $DEVELOP_METHOD;
		$ARR_IPIP_INTERVAL[$IPIP_ID] = $DEVELOP_INTERVAL;
		$ARR_IPIP_EVALUATE[$IPIP_ID] = $DEVELOP_EVALUATE;
	} // end while

	$COUNT_IPIP = count($ARR_IPIP);
        
        // =============================== START GEN PDF ========================//
        
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "";
	$report_code = "";
	$orientation='P';
        $set_font_head = 16;
        $set_font_body = 14;
        $space_line = ($font=='angsa'||$font=='cordia')?"  ":"";
        
	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code);
	
 	$pdf->Open();
        $gy = 0;
	//$pdf->SetMargins(5,5,5);
        $pdf->SetMargins(5,5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
        $pdf->Image("../images/logo_ocsc.jpg", 95, 15, 20, 26,"jpg");		// Original size = 50x65 => 20x26 iscs 40x20

	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 38, "", 0, 1, 'C', 0);
	$pdf->SetFont($font,'',25);
	$pdf->Cell(200, 7, "$DEPARTMENT_NAME", 0, 1, 'C', 0);
	$pdf->SetFont($font,'',20);
	//$pdf->Cell(200, 10, "Office of the Civil Service Commission (OCSC)", 0, 1, 'C', 0);
	$pdf->Cell(200, 7, "$ORG_ENG_NAME", 0, 1, 'C', 0);

	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->SetFont($font,'b','',20);
	$pdf->Cell(200, 10, "แบบสรุปการประเมินผลการปฏิบัติราชการ", 0, 1, 'C', 1);
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("99"),hexdec("CC"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	//$pdf->text(72,26,"แบบสรุปการประเมินผลการประเมินปฏิบัติราชการ");
	$pdf->Ln();
        $ln_cell = 35;
       //---------------------------------------------------- กรอบช่องที่1 -------------------------------------------------------
        $show_checkboxx = "../images/checkbox_check.jpg"; //check box
        $show_checkbox__blank = "../images/checkbox_blank.jpg";  //uncheck box
        
	//$pdf->Rect(14, 35, 182, 30 , 'D');                                      
        $pdf->SetFont($font,'b',$set_font_body);
        $pdf->text(20,40+$ln_cell,"รอบการประเมิน  ",0,1,'L');
        //$pdf->Rect(48, 37, 4, 4 , 'D'); 
        if($KF_CYCLE==1) $pdf->Image($show_checkboxx, 48, 37+$ln_cell, 4, 4,"jpg");
	else $pdf->Image($show_checkbox__blank, 48, 37+$ln_cell, 4, 4,"jpg");
        $pdf->text(58,40+$ln_cell,"รอบที่ ๑  ",0,1,'L');
        $pdf->text(80,40+$ln_cell,"๑ ตุลาคม ",0,1,'L');
        $pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->text(100, 40+$ln_cell, convert2thaidigit(($KF_CYCLE==1)?($KF_YEAR - 1):""), 0, 1, 'L');
        //$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
        $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
        $pdf->text(114,40+$ln_cell,"ถึง ๓๑ มีนาคม ",0,1,'L');
        $pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
        $pdf->text(141, 40+$ln_cell, convert2thaidigit(($KF_CYCLE==1)?($KF_YEAR):""), 0, 1, 'L');
        //$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
        $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
        //$pdf->Rect(48, 47, 4, 4 , 'D'); 
        if($KF_CYCLE==2) $pdf->Image($show_checkboxx, 48, 46.5+$ln_cell, 4, 4,"jpg");
	else $pdf->Image($show_checkbox__blank, 48, 46.5+$ln_cell, 4, 4,"jpg");
        $pdf->text(58,50+$ln_cell,"รอบที่ ๒  ",0,1,'L');
        $pdf->text(80,50+$ln_cell,"๑ เมษายน ",0,1,'L');
        $pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->text(100, 50+$ln_cell, convert2thaidigit(($KF_CYCLE==2)?($KF_YEAR - 1):""), 0, 1, 'L');
        //$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
        $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
        $pdf->text(114,50+$ln_cell,"ถึง ๓o กันยายน ",0,1,'L');
        $pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
        $pdf->text(141, 50+$ln_cell, convert2thaidigit(($KF_CYCLE==2)?($KF_YEAR):""), 0, 1, 'L');
        
        //$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
        $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
        $pdf->SetFont($font,'b',$set_font_body);
        
        $pdf->text(18-$gy,60+$ln_cell,"ชื่อผู้รับการประเมิน  ",0,1,'L');
        
        $pdf->SetFont($font,'b',$set_font_body);
        if($PER_NAME)$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
        else $pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
        $pdf->SetFont($font,'b',$set_font_body);
        if($PER_NAME)$pdf->text(50,60+$ln_cell,"$PER_NAME ",0,1,'L');
        //else$pdf->text(48,65,str_repeat(".",45),0,1,'L');
        
        //$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
        $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
        $pdf->SetFont($font,'b',$set_font_body);
        $pdf->text(18-$gy,70+$ln_cell,"ตำแหน่ง",0,1,'L');
        if($PL_NAME)$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
        else $pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
        $pdf->SetFont($font,'b',$set_font_body);
        if($PL_NAME)$pdf->text(35,70+$ln_cell,"$PL_NAME ",0,1,'L');
        //else$pdf->text(33,75,str_repeat(".",40),0,1,'L');
        
        //$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
        $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
        $pdf->SetFont($font,'b',$set_font_body);
        $pdf->text(114,70+$ln_cell,"สังกัด",0,1,'L'); 
        if($ORG_NAME)$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
        else $pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
        $pdf->SetFont($font,'b',$set_font_body);
        if($ORG_NAME)$pdf->text(125,70+$ln_cell,"$ORG_NAME ",0,1,'L');
        //else$pdf->text(123,75,str_repeat(".",40),0,1,'L');
        
        //$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
        $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
        $pdf->SetFont($font,'b',$set_font_body);
        $pdf->text(18-$gy,80+$ln_cell,"ชื่อผู้ประเมิน  ",0,1,'L');
        if($REVIEW_PER_NAME)$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
        //else $pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
        $pdf->SetFont($font,'b',$set_font_body);
        $pdf->text(40,80+$ln_cell,$REVIEW_PER_NAME ,0,1,'L');
        
        //$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
        $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
        $pdf->SetFont($font,'b',$set_font_body);
        $pdf->text(18-$gy,90+$ln_cell,"ตำแหน่ง",0,1,'L');
        
        /**/ 
        if(REVIEW_PER_TYPE==1){
           if(!empty($REVIEW_PM_NAME)){ 
                $REVIEW_PL_NAME = $REVIEW_PM_NAME;
            }else{
				if(!$REVIEW_PL_NAME){
					$REVIEW_PL_NAME = "";
				}else{
					$REVIEW_PL_NAME = strreplaceLvl($REVIEW_PL_NAME.$REVIEW_LEVEL_NAME);
				}

               
            }  
        }
        
       /**/
        
        if($REVIEW_PL_NAME)$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
        //else $pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
        $pdf->SetFont($font,'b',$set_font_body);
        $pdf->text(35,90+$ln_cell, $REVIEW_PL_NAME ,0,1,'L');
        /* สังกัดไม่มี */
        /*$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
        $pdf->SetFont($font,'',$set_font_head);
        $pdf->text(114,95,"สังกัด",0,1,'L');
        
         if($REVIEW_PL_NAME)$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
        //else $pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
        $pdf->SetFont($font,'',$set_font_body);
        $pdf->text(123,95,str_repeat(".",40),0,1,'L');*/
        
        //---------------------------------------------------- กรอบช่องที่ 2-------------------------------------------------------
        
        //$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
        $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
        $pdf->SetFont($font,'b',15);
        $pdf->text(10,100+$ln_cell,"สรุปผลการประเมิน",0,1,'L');
        $pdf->Ln(53-22+$ln_cell);
        
        $pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("99"),hexdec("CC"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
        
        $heading_width2[0] = "75";
	$heading_width2[1] = "22";
	$heading_width2[2] = "21";
	$heading_width2[3] = "22";
        $heading_width2[4] = "60";
        
        $pdf->SetFont($font,'',14);
        $pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
        $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
        $pdf->Cell($heading_width2[0] ,7,"องค์ประกอบการประเมิน",'LTR',0,'C',1);
        $pdf->Cell($heading_width2[1] ,7,"คะแนน(ก)",'LTR',0,'C',1);
        $pdf->Cell($heading_width2[2] ,7,"น้ำหนัก(ข)",'LTR',0,'C',1);
        $pdf->Cell($heading_width2[3] ,7,"รวมคะแนน",'LTR',0,'C',1);
        $pdf->Cell($heading_width2[4] ,7,"ระดับผลการประเมิน",'LTR',1,'C',1);
        $pdf->Cell($heading_width2[0] ,7,"",'LBR',0,'C',1);
        $pdf->Cell($heading_width2[1] ,7,"",'LBR',0,'C',1);
        $pdf->Cell($heading_width2[2] ,7,"",'LBR',0,'C',1);
        $pdf->Cell($heading_width2[3] ,7,"(ก)x(ข)",'LBR',0,'C',1);
        $pdf->Cell($heading_width2[4] ,7,"",'LBR',1,'C',1);
		
        
        if($SCORE_KPI > 0 && $SCORE_COMPETENCE>0){
            $start=showscore(convert2thaidigit(number_format($SHOW_SCORE_KPI, 2)));
            $chkam_code=0;
            if($start!='***'){
                $strOrg="";
                if(!empty($view_ORG_ID)){
                    $strOrg=" ORG_ID=".$view_ORG_ID." and ";
                }
                $cmd=" select AM_CODE 
                        from PER_ASSESS_LEVEL
                        where ".$strOrg." (PER_TYPE=".$PER_TYPE.") and (AL_YEAR = '$KF_YEAR') and (AL_CYCLE = $KF_CYCLE)
                        and $SUM_TOTAL between al_point_min and al_point_max";
                $cnt = $db_dpis->send_cmd($cmd);
                if($cnt>0){
                        $datachk = $db_dpis->get_array();
                        $chkam_code = $datachk[AM_CODE];
                }else{
                    $cmd=" select AM_CODE 
                            from PER_ASSESS_LEVEL
                            where  (PER_TYPE=".$PER_TYPE.") and (AL_YEAR = '$KF_YEAR') and (AL_CYCLE = $KF_CYCLE)
                            and $SUM_TOTAL between al_point_min and al_point_max";
                    $cnt = $db_dpis->send_cmd($cmd);
                    if($cnt>0){
                        $datachk = $db_dpis->get_array();
                        $chkam_code = $datachk[AM_CODE];
                    }
                }
            }	
        }//if $SUM_TOTAL
        
        //========================================================= Loop ค่า ดีเด่น ดีมาก ออกมาแสดง ===========================================================
        //หาระดับผลการประเมินหลัก 
	$cmd = "	select		AM_CODE, AM_NAME, AM_POINT_MIN, AM_POINT_MAX, AM_ACTIVE
                                        from	PER_ASSESS_MAIN where AM_YEAR = '$KF_YEAR' and AM_CYCLE = $KF_CYCLE and PER_TYPE = $PER_TYPE
					order by AM_POINT_MAX desc, AM_CODE desc ";
	$db_dpis->send_cmd($cmd);
        $pdf->SetXY($pdf->GetX(),125+$ln_cell);
        while($data = $db_dpis->get_array()){
            $AM_CODE = $data[AM_CODE];
            $AM_NAME = $data[AM_NAME];
            $AM_POINT_MIN = $data[AM_POINT_MIN];
            $AM_POINT_MAX = $data[AM_POINT_MAX];
            if($AM_CODE==$chkam_code){
                $pdf->SetFont($font,'',14);
                //$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
                $pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
                $show_checkbox = "../images/checkbox_check.jpg";
            }else{
                $pdf->SetFont($font,'',14);
                //$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
                $pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
                $show_checkbox = "../images/checkbox_blank.jpg";  
            }
            $x=155;
            $y=$pdf->GetY()-2;
            $pdf->SetXY($x+8,$y);
            $pdf->Image($show_checkbox, ($pdf->GetX() - 5), ($pdf->GetY() + 2), 4, 4,"jpg");
            $pdf->Cell(1, 8, "$AM_NAME", 0, 1, 'L', 0);
	} //end while
        
        //================================================== กรอบ องค์ประกอบที่ ๑ ผลสัมฤทธิ์ของงาน ======================================================================
        $pdf->SetXY(5,118+$ln_cell);
        $pdf->SetFont($font,'',14);
        $pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
        
        $h=12;
        $x=$pdf->GetX();
        $y=$pdf->GetY();
        
        $x=$pdf->GetX();
        $pdf->MultiCell($heading_width2[0],$h,'องค์ประกอบที่ ๑ ผลสัมฤทธิ์ของงาน','LRTB','L');
        $pdf->SetXY($x+$heading_width2[0],$y);
        
        $x=$pdf->GetX();
        $pdf->MultiCell($heading_width2[1],$h,showscore(convert2thaidigit(number_format($SHOW_SCORE_KPI, 2))),'LRTB','C');
        $pdf->SetXY($x+$heading_width2[1],$y);
        
        $x=$pdf->GetX();
        if ($PERFORMANCE_WEIGHT)
            $pdf->MultiCell($heading_width2[2],$h,convert2thaidigit($PERFORMANCE_WEIGHT . "%"),'LRTB','C');
        else
            $pdf->MultiCell($heading_width2[2],$h,"",'LRTB','C');
        
        $pdf->SetXY($x+$heading_width2[2],$y);
        
        $x=$pdf->GetX();
        $pdf->MultiCell($heading_width2[3],$h,showscore(convert2thaidigit(number_format($SUM_KPI, 2))),'LRTB','C');
        $pdf->SetXY($x+$heading_width2[3],$y);
        
        $x=$pdf->GetX();
        $pdf->MultiCell($heading_width2[4],$h,'','RHL','L');
        $pdf->SetXY($x+$heading_width2[4],$y);
       
        $pdf->Ln(12);
        
        //================================================== กรอบ องค์ประกอบที่ ๒ พฤติกรรมการปฏิบัติ ราชการ (สมรรถนะ) ======================================================================
        
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        
        //$pdf->Rect($x,$y,55,$h);
        $pdf->MultiCell($heading_width2[0],6,'องค์ประกอบที่ ๒ พฤติกรรมการปฏิบัติราชการ (สมรรถนะ)',"LRTB",'L');
        $pdf->SetXY($x+$heading_width2[0],$y);
        
        $x=$pdf->GetX();
        $pdf->MultiCell($heading_width2[1],$h,showscore(convert2thaidigit(number_format($SHOW_SCORE_COMPETENCE, 2))),'LRTB','C');
        $pdf->SetXY($x+$heading_width2[1],$y);
        
        $x=$pdf->GetX();
        if ($COMPETENCE_WEIGHT)
            $pdf->MultiCell($heading_width2[2],$h,convert2thaidigit($COMPETENCE_WEIGHT . "%"),'LRTB','C');
        else
            $pdf->MultiCell($heading_width2[2],$h,'','LRTB','C');
        $pdf->SetXY($x+$heading_width2[2],$y);
        
        $x=$pdf->GetX();
        $pdf->MultiCell($heading_width2[3],$h,showscore(convert2thaidigit(number_format($SUM_COMPETENCE, 2))),'LRTB','C');
        $pdf->SetXY($x+$heading_width2[3],$y);
        
        $x=$pdf->GetX();
        $pdf->MultiCell($heading_width2[4],$h,'',"RL",'L');
        $pdf->SetXY($x+$heading_width2[4],$y);
        
        $pdf->Ln($h);
	
        //===================================================================== กรอบ องค์ประกอบอื่น ๆ ถ้ามี ==============================================================================
        
        $x=$pdf->GetX();
        $y = $pdf->GetY();
        $pdf->MultiCell($heading_width2[0],$h,'องค์ประกอบอื่น ๆ (ถ้ามี)',"LRTB",'L');
        $pdf->SetXY($x+$heading_width2[0],$y);
        
        $x=$pdf->GetX();
        $pdf->MultiCell($heading_width2[1],$h,showscore(convert2thaidigit(number_format($SHOW_SCORE_OTHER, 2))),"LRTB",'C');
        $pdf->SetXY($x+$heading_width2[1],$y);
        
        $x=$pdf->GetX();
        if ($OTHER_WEIGHT)
            $pdf->MultiCell($heading_width2[2],$h,convert2thaidigit($OTHER_WEIGHT . "%"),"LRTB",'C');
        else
            $pdf->MultiCell($heading_width2[2],$h,'',"LRTB",'C');
        $pdf->SetXY($x+$heading_width2[2],$y);
        
        $x=$pdf->GetX();
        $pdf->MultiCell($heading_width2[3],$h,showscore(convert2thaidigit(number_format($SUM_OTHER, 2))),"LRTB",'C');
        $pdf->SetXY($x+$heading_width2[3],$y);
        
        $x=$pdf->GetX();
        $pdf->MultiCell($heading_width2[4],$h,'',"RL",'L');
        $pdf->SetXY($x+$heading_width2[4],$y);
       
        $pdf->Ln();
        
        //============================================================================ กรอบ รวม =====================================================================================
        
        $x=$pdf->GetX();
        $y = $pdf->GetY();
        $pdf->MultiCell($heading_width2[0],7,'',"TBL",'L');
        $pdf->SetXY($x+$heading_width2[0],$y);
        
        $x=$pdf->GetX();
        $pdf->MultiCell($heading_width2[1],7,'รวม',"RTB",'R');
        $pdf->SetXY($x+$heading_width2[1],$y);
        
        $x=$pdf->GetX();
        $pdf->MultiCell($heading_width2[2],7,convert2thaidigit("๑๐๐%"),"LRTB",'C');
        $pdf->SetXY($x+$heading_width2[2],$y);
        
        $x=$pdf->GetX();
        $pdf->MultiCell($heading_width2[3],7,showscore(convert2thaidigit(number_format($SUM_TOTAL, 2))),"LRTB",'C');
        $pdf->SetXY($x+$heading_width2[3],$y);
        
        $x=$pdf->GetX();
        $pdf->MultiCell($heading_width2[4],7,'',"LRB",'L');
        $pdf->SetXY($x+$heading_width2[4],$y);
        
        $pdf->Ln(16);
        
        //============================================================================ กรอบ แผนการพัฒนาการปฏิบัติราชการรายบุคคล =====================================================================================
        
        //$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
        $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
        $pdf->SetFont($font,'b',15);
        $pdf->text(10,170+$ln_cell,"แผนพัฒนาการปฏิบัติราชการรายบุคคล",0,1,'L');
        //$pdf->Ln(105);
        
        
        $x=5;
        $y=$pdf->GetY()+5;
        $pdf->SetXY($x,$y);
        
        $pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
        $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
  
        $heading_width3[0] = "90";
	$heading_width3[1] = "55";
	$heading_width3[2] = "55";
        $pdf->SetFont($font,'',14);
        
        print_header(3);
        $pdf->SetFont($font,'',14);
        $pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
        $data_count = 0;
	foreach($ARR_IPIP as $IPIP_ID => $DEVELOP_COMPETENCE){
		$data_count++;
		$border = "";
		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

		$pdf->SetFont($font,'',13);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$pdf->MultiCell($heading_width3[0], 7, convert2thaidigit($DEVELOP_COMPETENCE), $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width3[0];
		$pdf->y = $start_y;
		$pdf->MultiCell($heading_width3[1], 7, convert2thaidigit($ARR_IPIP_METHOD[$IPIP_ID]), $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width3[0] + $heading_width3[1];
		$pdf->y = $start_y;
		$pdf->MultiCell($heading_width3[2], 7, convert2thaidigit($ARR_IPIP_INTERVAL[$IPIP_ID]), $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width3[0] + $heading_width3[1] + $heading_width3[2];
		$pdf->y = $start_y;

		//================= Draw Border Line ====================
		$line_start_y = $start_y;		$line_start_x = $start_x;
		$line_end_y = $max_y;		$line_end_x = $start_x;
		$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
		for($i=0; $i<=2; $i++){
			$line_start_y = $start_y;		$line_start_x += $heading_width3[$i];
			$line_end_y = $max_y;		$line_end_x += $heading_width3[$i];
			
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
		} // end for
		//====================================================

		if(($pdf->h - $max_y - 10) < 20){ 
			$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
			if($data_count < $COUNT_IPIP){
				$pdf->AddPage();
				print_header(3);
				$max_y = $pdf->y;
			} // end if
		}else{
			$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
		} // end if

		$pdf->x = $start_x;			$pdf->y = $max_y;
	} // end foreach
        
        //========================================================================================================================================================================
        //========================================================================================================================================================================
        // ============================================================================ PAGE 2 ===================================================================================	
        //========================================================================================================================================================================
        //========================================================================================================================================================================
        
        $pdf->SetAutoPageBreak(false);
	$pdf->AddPage();
        $pdf->SetXY(5,10);
	//$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	//$pdf->SetFont($font,'b',14);

        $Y_PAGE = 31;
        $pdf->SetXY(10,10);
        $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
        $pdf->SetFont($font,'b',15);
        //============================================================================ ผู้รับการประเมิน =====================================================================================
        $pdf->Cell(100 ,7,"การรับทราบผลการประเมิน",0,1,'L',0);
        
        $pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
        $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
    
        $heading_width4[0] = "100";
	$heading_width4[1] = "100";
        $pdf->SetFont($font,'',14);
        $pdf->Cell($heading_width4[0] ,7,"ผู้รับการประเมิน",'LTR',0,'C',1);
        $pdf->Cell($heading_width4[1] ,7,"ผู้ประเมิน",'LTR',1,'C',1);
        	
        $pdf->SetFont($font,'',14);
        $pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
        
        $show_checkboxs = "../images/checkbox_check.jpg"; //check box
        $show_un_checkbox = "../images/checkbox_blank.jpg";  //uncheck box
        //$pdf->text(18,176,"การรับทราบผลการประเมิน",0,1,'L');
        
        //====================================================== เช็คบล๊อก ผู้รับการประเมิน ===========================================================
        $pdf->SetXY(5,$Y_PAGE-7);
        $x=5;
        $y=$pdf->GetY()-1;
        $pdf->SetXY($x+8,$y);
        if($ACCEPT_FLAG=="1")
            $pdf->Image($show_checkboxs, ($pdf->GetX() - 5), ($pdf->GetY() + 2), 4, 4,"jpg");
        else
            $pdf->Image($show_un_checkbox, ($pdf->GetX() - 5), ($pdf->GetY() + 2), 4, 4,"jpg");
        
        //====================================================== เช็คบล๊อก ผู้ประเมิน ===========================================================
        $x=105;
        $y=$pdf->GetY();
        $pdf->SetXY($x+8,$y);
        if($ACCEPT_FLAG=="1")
            $pdf->Image($show_checkboxs, ($pdf->GetX() - 5), ($pdf->GetY() + 2), 4, 4,"jpg");
        else
            $pdf->Image($show_un_checkbox, ($pdf->GetX() - 5), ($pdf->GetY() + 2), 4, 4,"jpg");
        
        $x=105;
        $y=$pdf->GetY();
        $pdf->SetXY($x+8,$y+5.8);
        if($ACCEPT_FLAG=="0")
            $pdf->Image($show_checkboxs, ($pdf->GetX() - 5), ($pdf->GetY() + 2), 4, 4,"jpg");
        else
            $pdf->Image($show_un_checkbox, ($pdf->GetX() - 5), ($pdf->GetY() + 2), 4, 4,"jpg");
            
        
        //=============================================================== get pic_sign ===========================================================================
        $MONTH_ACCEPT = get_month($ACCEPT_MONTH,2);
        $PIC_SIGN="";
	if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์	
            if($ACCEPT_FLAG == 1){
		$PIC_SIGN = getPIC_SIGN($PER_ID,$PER_CARDNO);
            }    
            if($PIC_SIGN){ 
                    $x=65; 
                    $y=$Y_PAGE + 36;
                    $pdf->SetXY($x+8,$y); //เซตใหม่ทุกครั้งที่มีการขยับ แกน Y
                    $TXT_PIC_SIGN = $pdf->Image($PIC_SIGN,($pdf->x+1), ($pdf->y+1), 25, 15,"jpg");	// Original size = wxh (60x15)
            }else{
                    $TXT_PIC_SIGN = "";
            }
	}else{
		$pdf->SetFont($font,'',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	} 
	
        if($SIGN_NAME){
            $TXT_SIGN_NAME = $SIGN_NAME."      "; 
            //$pdf->Cell($x, $y+17,"(".str_repeat(".",49).")" , 0, 1, 'L', 0)
            //$pdf->text($x,$y+17, "(".str_repeat(".",49).")" ,0,1,'L');
        }else{
            $TXT_SIGN_NAME = "(".str_repeat(".",55)." ) ";
        }
        
        if(!empty($PM_NAME)){ 
            $valPM_NAME = $PM_NAME;
        }else{
            if(!$PL_NAME){
                    $valPM_NAME = "";
            }else{
                    $valPM_NAME = strreplaceLvl($PL_NAME.$POSITION_LEVEL);
            }
        }
        if($valPM_NAME){
            $txt_valPM_NAME = "ตำแหน่ง ".convert2thaidigit("$valPM_NAME");
        }else{
            $txt_valPM_NAME = " ตำแหน่ง".str_repeat(".",38);
        }
        if($ACCEPT_DATE && ($ACCEPT_FLAG == "0" || $ACCEPT_FLAG == "1") && $CH_SCORE_DAY=="N"){//&& $CH_SCORE_DAY=="N"
            $txt_date_review = " วันที่ ".$ACCEPT_DAY ."  ".$MONTH_ACCEPT ."  ".$ACCEPT_YEAR;
        }else{
            $txt_date_review = " วันที่".str_repeat(".",10)."เดือน".str_repeat(".",10)."พ.ศ".str_repeat(".",10)."";
        }
        //============================================================ เช็คบล๊อก End ===============================================================================
        
        //                                                              ตารางใหม่
       
        //============================================================= ผู้รับการประเมิน =============================================================================
        $h=6;
        $b=80;
        
        $x=5;
        $y=$Y_PAGE-7;
        $pdf->SetXY($x,$y); //เซตค่า XY ตำแหน่ง ตาราง //เซตใหม่ทุกครั้งที่มีการขยับ แกน Y
       
        
        $x=$pdf->GetX();
        $y=$pdf->GetY();
        $pdf->Rect($x,$y,$heading_width4[0],$b);
        $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
        $pdf->MultiCell($heading_width4[0],$h,$pdf->Cell(100, 5.8, $space_line."       ได้รับทราบผลการประเมินและแผนพัฒนาผลการปฏิบัติ", 0, 1, 'L', 0)."\n"
                . $pdf->Cell(100, 5, " ราชการรายบุคคลแล้ว", 0, 1, 'L', 0)."\n"
                . $pdf->Cell(100, 44, "" , 0, 1, 'L', 0)."\n"
                . $TXT_PIC_SIGN ." "
                . $pdf->Cell(100, 5.5, " ลงชื่อ ".str_repeat(".",46)." " , 0, 1, 'R', 0)."\n"
                . $pdf->Cell(100, 5.5, "".$TXT_SIGN_NAME , 0, 1, 'R', 0)."\n"
                . $pdf->Cell(100, 5.5, "".$txt_valPM_NAME."  " , 0, 1, 'R', 0)."\n"
                . $pdf->Cell(100, 5.5, "".$txt_date_review."      " , 0, 1, 'R', 0)."\n"
                . "",0,"L");
        $pdf->SetXY($x+$heading_width4[0],$y);
        
        //============================================================================ ผู้ประเมิน =====================================================================================
        $creat_date = $SCORE_DAY." ".$ACCEPT_MONTH = get_month($SCORE_MONT,2)." ".$SCORE_YEAR;
        $MONTH_SCORE = get_month($SCORE_MONT);
        //ลงชื่อผู้ประเมิน
        $PIC_SIGN="";
        if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์	
            if($KF_SCORE_STATUS == "1" && $PER_ID_REVIEW){
                    $PIC_SIGN = getPIC_SIGN($PER_ID_REVIEW,$PER_CARDNO_REVIEW_N);	
            }
            if($PIC_SIGN){
                $x_pic=165; 
                $y_pic=$Y_PAGE + 36;
                //$pdf->SetXY($x+8,$y); //เซตใหม่ทุกครั้งที่มีการขยับ แกน Y
                $TXT_PIC_SIGN_REVIEW = $pdf->Image($PIC_SIGN,($x_pic+1), ($y_pic+1), 25, 15,"jpg");	// Original size = wxh (60x15)

            }else{
                $TXT_PIC_SIGN_REVIEW = "";
            }
	}else{
            $pdf->SetFont($font,'',12);
            $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	}
        
        if($REVIEW_SIGN_NAME){
            $TXT_REVIEW_SIGN_NAME = $REVIEW_SIGN_NAME."      "; 
            //$pdf->Cell($x, $y+17,"(".str_repeat(".",49).")" , 0, 1, 'L', 0)
            //$pdf->text($x,$y+17, "(".str_repeat(".",49).")" ,0,1,'L');
        }else{
            $TXT_REVIEW_SIGN_NAME = "(".str_repeat(".",55)." ) ";
        }
        $REVIEW_PL_NAME_CHK = trim($REVIEW_PL_NAME)?1:0;
	$REVIEW_PL_NAME = trim($REVIEW_PL_NAME)?$REVIEW_PL_NAME:"";		//ถ้าไม่มีข้อมูล แสดงช่องว่าง
        if(!empty($REVIEW_PM_NAME)){ 
            $valREVIEW_PM_NAME = $REVIEW_PM_NAME;
        }else{
            if(!$PL_NAME){
                    $valREVIEW_PM_NAME = "";
            }else{
                    $valREVIEW_PM_NAME = ($REVIEW_PL_NAME_CHK==1)?strreplaceLvl($REVIEW_PL_NAME.$REVIEW_LEVEL_NAME):"";
            }
        }
        if($valREVIEW_PM_NAME){
            $txt_valREVIEW_PM_NAME = "ตำแหน่ง ".convert2thaidigit("$valREVIEW_PM_NAME");
        }else{
            $txt_valREVIEW_PM_NAME = " ตำแหน่ง".str_repeat(".",38);
        }
        if($KF_SCORE_STATUS == "1" && $SCORE_STATUS_DATE && $CH_SCORE_DAY=="N" ){//&&  $CH_SCORE_DAY == 'N'
            $txt_date_review1 = " วันที่ ".$SCORE_DAY ."  ".$MONTH_SCORE ."  ".$SCORE_YEAR."      ";
        }else{
            $txt_date_review1 = " วันที่".str_repeat(".",10)."เดือน".str_repeat(".",10)."พ.ศ".str_repeat(".",10)."  ";
        }
      
        $x=$pdf->GetX();
        $pdf->Rect($x,$y,$heading_width4[1],$b);
        
        if($ACCEPT_FLAG=="0" && $KF_SCORE_STATUS == "1" && $SCORE_STATUS_DATE!=""){        
            $date_create =  $creat_date;
        }else{        
            $date_create = str_repeat(".",40);
        }
        
        $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
        $pdf->MultiCell($heading_width4[1],$h,$pdf->Cell(95, 5.8, $space_line."       ได้แจ้งผลการประเมินและผู้รับการประเมินได้ลงนามรับทราบ", 0, 1, 'L', 0)."\n"
                . $pdf->SetXY(105,$pdf->GetY()).""
                . $pdf->Cell(95, 5.8, $space_line."       ได้แจ้งผลการประเมิน เมื่อวันที่ ".$date_create, 0, 1, 'L', 0)."\n"
                . $pdf->SetXY(105,$pdf->GetY()).""
                . $pdf->Cell(95, 5.5, " แต่ผู้รับการประเมินไม่ลงนามรับทราบ ", 0, 1, 'L', 0)."\n"
                . $pdf->SetXY(105,$pdf->GetY()).""
                . $pdf->Cell(95, 5.5, " โดยมี ".str_repeat(".",46)." เป็นพยาน ", 0, 1, 'L', 0)."\n"
                . $pdf->SetXY(105,$pdf->GetY()).""
                . $pdf->Cell(95, 6, "", 0, 1, 'L', 0)."\n"
                . $pdf->SetXY(105,$pdf->GetY()).""
                . $pdf->Cell(95, 5.5, " ลงชื่อ ".str_repeat(".",46)." พยาน" , 0, 1, 'L', 0)."\n"
                . $pdf->SetXY(105,$pdf->GetY()).""
                . $pdf->Cell(95, 5.5, " ตำแหน่ง ".str_repeat(".",48), 0, 1, 'L', 0)."\n"
                . $pdf->SetXY(105,$pdf->GetY()).""
                . $pdf->Cell(95, 5.5, " วันที่ ".str_repeat(".",10)."เดือน ".str_repeat(".",15)."พ.ศ ".str_repeat(".",12), 0, 1, 'L', 0)."\n"
                . $pdf->SetXY(105,$pdf->GetY()).""
                . $pdf->Cell(95, 10, "", 0, 1, 'L', 0)."\n"
                . $TXT_PIC_SIGN_REVIEW ." "
                . $pdf->SetXY(105,$pdf->GetY()).""
                . $pdf->Cell(95, 5.5, " ลงชื่อ ".str_repeat(".",46)." " , 0, 1, 'R', 0)."\n"
                . $pdf->SetXY(105,$pdf->GetY()).""
                . $pdf->Cell(95, 5.5, "".$TXT_REVIEW_SIGN_NAME, 0, 1, 'R', 0)."\n"
                . $pdf->SetXY(105,$pdf->GetY()).""
                . $pdf->Cell(95, 5.5, "".$txt_valREVIEW_PM_NAME."  " , 0, 1, 'R', 0)."\n"
                . $pdf->SetXY(105,$pdf->GetY()).""
                . $pdf->Cell(95, 5.5, "".$txt_date_review1, 0, 1, 'R', 0)."\n"
                . "",0,"L");
        //$pdf->SetXY($x+47,$y);
        $pdf->SetXY($x+$heading_width4[1],$y);
        
        $pdf->Ln($b+10);
        $g_x = $pdf->GetX();
        $g_y = $pdf->GetY();
        
        //==============================================================================================================================================================
        //===========================================================กรอบ  ความคิดเห็นของผู้บังคับบัญชา เหนือขึ้นไป ====================================================================
        //==============================================================================================================================================================
        
         //====================================================== เช็คบล๊อก ความคิดเห็นของผู้บังคับบัญชา|เหนือขึ้นไป ===========================================================
        
        $pdf->SetXY(10,$g_y-4);
        $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
        $pdf->SetFont($font,'b',15);
        //============================================================================ ผู้รับการประเมิน =====================================================================================
        $pdf->Cell(100 ,7,"ความเห็นของผู้บังคับบัญชาเหนือขึ้นไป",0,1,'L',0);
        
        $pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
        $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
        
        
        $g_y_pic = $g_y+9;
        $pdf->SetXY($g_x,$g_y_pic);
        $x=5;
        $y=$pdf->GetY();
        $pdf->SetXY($x+8,$y);
        if(trim($AGREE_REVIEW1))
            $pdf->Image($show_checkboxs, ($pdf->GetX() - 5), ($pdf->GetY() + 2), 4, 4,"jpg");
        else
            $pdf->Image($show_un_checkbox, ($pdf->GetX() - 5), ($pdf->GetY() + 2), 4, 4,"jpg");
            
      
        //================================================ เช็คบล๊อก ความคิดเห็นของผู้บังคับบัญชา|เหนือขึ้นไปอีกชั้นหนึ่ง(ถ้ามี) ===========================================================
        
        $x=105;
        $y=$pdf->GetY();
        $pdf->SetXY($x+8,$y);
        if(trim($AGREE_REVIEW2))
            $pdf->Image($show_checkboxs, ($pdf->GetX() - 5), ($pdf->GetY() + 2), 4, 4,"jpg");
        else
            $pdf->Image($show_un_checkbox, ($pdf->GetX() - 5), ($pdf->GetY() + 2), 4, 4,"jpg");
        
        $x=105;
        $y=$pdf->GetY();
        $pdf->SetXY($x+8,$y);
        
        //========================================================== ตาราง =============================================================================
        
        $pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
        $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
  
        $pdf->SetFont($font,'',14);
        $pdf->SetXY($g_x,$g_y+3);
        $pdf->Cell($heading_width4[0] ,7,"ความเห็นของผู้บังคับบัญชาเหนือขึ้นไป",'LTR',0,'C',1);
        $pdf->Cell($heading_width4[1] ,7,"ความเห็นของผู้บังคับบัญชาเหนือขึ้นไปอีกชั้นหนึ่ง(ถ้ามี)",'LTR',1,'C',1);
       
        $pdf->SetFont($font,'',14);
        $pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
        
        //=========================================================== ความคิดเห็นของผู้บังคับบัญชา เหนือขึ้นไป ====================================================================
        
        if($AGREE_REVIEW1){
            $TXT_AGREE_REVIEW1 = $AGREE_REVIEW1;
        }else{
            $TXT_AGREE_REVIEW1 = "";
        }
        if($DIFF_REVIEW1){
            $TXT_DIFF_REVIEW1 = $DIFF_REVIEW1;
        }else{
            $TXT_DIFF_REVIEW1 = "";
        }
        if($REVIEW_SIGN_NAME1){
            $TXT_REVIEW_SIGN_NAME1 = $REVIEW_SIGN_NAME1."      "; 
            //$pdf->Cell($x, $y+17,"(".str_repeat(".",49).")" , 0, 1, 'L', 0)
            //$pdf->text($x,$y+17, "(".str_repeat(".",49).")" ,0,1,'L');
        }else{
            $TXT_REVIEW_SIGN_NAME1 = "(".str_repeat(".",55).") ";
        }
        
        if(!empty($REVIEW_PM_NAME1)){ 
            $valREVIEW_PM_NAME1 = $REVIEW_PM_NAME1;
        }else{
            if(!$REVIEW_PL_NAME1){
                    $valREVIEW_PM_NAME1 = "";
            }else{
                    $valREVIEW_PM_NAME1 = strreplaceLvl($REVIEW_PL_NAME1.$REVIEW_LEVEL_NAME1);
            }
        }
        if($valREVIEW_PM_NAME1){
            $txt_valREVIEW_PM_NAME1 = "ตำแหน่ง ".convert2thaidigit("$valREVIEW_PM_NAME1");
        }else{
            $txt_valREVIEW_PM_NAME1 = " ตำแหน่ง".str_repeat(".",38);
        }
        
        if(($AGREE_REVIEW1 || $DIFF_REVIEW1)&& $DATE_REVIEW1 && $CH_SCORE_DAY=="N" ){//&& $CH_SCORE_DAY=="N"
            $txt_date_review11 = " วันที่ ".$DATE_REVIEW1_DAY ."  ".$review1 = get_month($DATE_REVIEW1_MONTH) ."  ".$DATE_REVIEW1_YEAR."      ";
        }else{
            $txt_date_review11 = " วันที่".str_repeat(".",10)."เดือน".str_repeat(".",10)."พ.ศ".str_repeat(".",10)."  ";
        }
        $x1=$pdf->GetX();
        $y=$pdf->GetY();
        
        //$pdf->Rect($x1,$y,$heading_width4[0],$b); ปิดใช้อันล่าง
        $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
        $pdf->Cell($heading_width4[0], 6, $space_line."       เห็นด้วยกับผลการประเมิน ", 0, 1, 'L', 0);
        $pdf->Cell($heading_width4[0], 25, "", 0, 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x;			$pdf->y -= 25;
        $start_multi_call_y1 = $pdf->GetY();
        $pdf->MultiCell( $heading_width4[0]  , 5 , $TXT_AGREE_REVIEW1 ,0 );
        ($TXT_AGREE_REVIEW1)?$pdf->Cell($heading_width4[0], 1.5, "", 0, 1, 'L', 0):"";
        ($TXT_AGREE_REVIEW1)?$pdf->Cell($heading_width4[0], 6, str_repeat(".",120), 0, 1, 'L', 0)."\n".$pdf->Cell($heading_width4[0], 6, str_repeat(".",120), 0, 1, 'L', 0):$pdf->Cell($heading_width4[0], 6, str_repeat(".",120), 0, 1, 'L', 0)."\n".$pdf->Cell($heading_width4[0], 6, str_repeat(".",120), 0, 1, 'L', 0)."\n".$pdf->Cell($heading_width4[0], 6, str_repeat(".",120), 0, 1, 'L', 0)."\n".$pdf->Cell($heading_width4[0], 6, str_repeat(".",120), 0, 1, 'L', 0);
        $end_multi_call_y1 = $pdf->GetY();
        $multi_call_height = $end_multi_call_y1-$start_multi_call_y1;
        $pdf->Cell($heading_width4[0], 4, "", 0, 1, 'L', 0);
        if(trim($DIFF_REVIEW1)) $pdf->Image("../images/checkbox_check.jpg",($pdf->x +3), ($pdf->y + 0.5), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x +3), ($pdf->y + 0.5), 4, 4,"jpg");
        $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
        $pdf->Cell($heading_width4[0], 5, $space_line."       มีความเห็นต่าง ดังนี้", 0, 1, 'L', 0);
        $pdf->Cell($heading_width4[0], 25, "", 0, 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x;			$pdf->y -= 25;
        $start_multi_call_y2 = $pdf->GetY();
        $pdf->MultiCell( $heading_width4[0]  , 5 , $TXT_DIFF_REVIEW1,0 );
        ($TXT_DIFF_REVIEW1)?$pdf->Cell($heading_width4[0], 1.5, "", 0, 1, 'L', 0):"";
        ($TXT_DIFF_REVIEW1)?$pdf->Cell($heading_width4[0], 6, str_repeat(".",120), 0, 1, 'L', 0)."\n".$pdf->Cell($heading_width4[0], 6, str_repeat(".",120), 0, 1, 'L', 0):$pdf->Cell($heading_width4[0], 6, str_repeat(".",120), 0, 1, 'L', 0)."\n".$pdf->Cell($heading_width4[0], 6, str_repeat(".",120), 0, 1, 'L', 0)."\n".$pdf->Cell($heading_width4[0], 6, str_repeat(".",120), 0, 1, 'L', 0)."\n".$pdf->Cell($heading_width4[0], 6, str_repeat(".",120), 0, 1, 'L', 0);
        $end_multi_call_y2 = $pdf->GetY();
        $multi_call_height2 = $end_multi_call_y2-$start_multi_call_y2;
        $pdf->Cell($heading_width4[0], 10, "", 0, 1, 'L', 0);
        $PIC_SIGN="";
	if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์
            if($AGREE_REVIEW1 || $DIFF_REVIEW1){
                    $PIC_SIGN = getPIC_SIGN($PER_ID_REVIEW1,$PER_CARDNO_REVIEW1);
            }	
            if($PIC_SIGN){
                 
                    $pdf->Image($PIC_SIGN,($pdf->x+65), ($pdf->y-10), 25, 15,"jpg");	// Original size = wxh (60x15)
            }
	}
        $pdf->Cell($heading_width4[0], 5.5, " ลงชื่อ ".str_repeat(".",46)." " ,0, 1, 'R', 0);
        $pdf->Cell($heading_width4[0], 5.5, "".$TXT_REVIEW_SIGN_NAME1, 0, 1, 'R', 0);
        $pdf->Cell($heading_width4[0], 5.5, "".$txt_valREVIEW_PM_NAME1."  " , 0, 1, 'R', 0);
        $pdf->Cell($heading_width4[0], 5.5, "".$txt_date_review11 , 0, 1, 'R', 0);
        $pdf->SetXY($x1+$heading_width4[0],$y);
        
        //=========================================================== ความคิดเห็นของผู้บังคับบัญชา เหนือขึ้นไปอีกชั้นหนึ่ง(ถ้ามี) =========================================================
       
        if($AGREE_REVIEW2){
            $TXT_AGREE_REVIEW2 = $AGREE_REVIEW2;
        }else{
            $TXT_AGREE_REVIEW2 = "";
        }
        if($DIFF_REVIEW2){
            $TXT_DIFF_REVIEW2 = $DIFF_REVIEW2;
        }else{
            $TXT_DIFF_REVIEW2 = "";
        }
        if($REVIEW_SIGN_NAME2){
            $TXT_REVIEW_SIGN_NAME2 = $REVIEW_SIGN_NAME2."      "; 
            //$pdf->Cell($x, $y+17,"(".str_repeat(".",49).")" , 0, 1, 'L', 0)
            //$pdf->text($x,$y+17, "(".str_repeat(".",49).")" ,0,1,'L');
        }else{
            $TXT_REVIEW_SIGN_NAME2 = "(".str_repeat(".",55).") ";
        }
        
        if(!empty($REVIEW_PM_NAME2)){ 
            $valREVIEW_PM_NAME2 = $REVIEW_PM_NAME2;
        }else{
            if(!$REVIEW_PL_NAME2){
                    $valREVIEW_PM_NAME2 = "";
            }else{
                    $valREVIEW_PM_NAME2 = strreplaceLvl($REVIEW_PL_NAME2.$REVIEW_LEVEL_NAME2);
            }
        }
        if($valREVIEW_PM_NAME2){
            $txt_valREVIEW_PM_NAME2 = "ตำแหน่ง ".convert2thaidigit("$valREVIEW_PM_NAME2");
        }else{
            $txt_valREVIEW_PM_NAME2 = " ตำแหน่ง".str_repeat(".",38);
        }
        
        if(($AGREE_REVIEW2 || $DIFF_REVIEW2)&& $DATE_REVIEW2 && $CH_SCORE_DAY=="N"){//&& $CH_SCORE_DAY=="N"
            $txt_date_review12 = " วันที่ ".$DATE_REVIEW2_DAY ."  ".$review2 = get_month($DATE_REVIEW2_MONTH) ."  ".$DATE_REVIEW2_YEAR."      ";
        }else{
            $txt_date_review12 = " วันที่".str_repeat(".",10)."เดือน".str_repeat(".",10)."พ.ศ".str_repeat(".",10)."  ";
        }
        
        $x2=$pdf->GetX();
        //$pdf->Rect($x2,$y,$heading_width4[1],$b);
        $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
        $pdf->Cell($heading_width4[1], 6, $space_line."       เห็นด้วยกับผลการประเมิน ", 0, 1, 'L', 0);
        $pdf->SetXY(105,$pdf->GetY());
        $pdf->Cell($heading_width4[1], 25, "", 0, 1, 'L', 0);
        $pdf->SetXY(105,$pdf->GetY());
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x;			$pdf->y -= 25;
        $start_multi_call2_y1 = $pdf->GetY();
        $pdf->MultiCell( $heading_width4[1]  , 5 , $TXT_AGREE_REVIEW2,0 );
        $pdf->SetXY(105,$pdf->GetY());
        ($TXT_AGREE_REVIEW2)?$pdf->Cell($heading_width4[1], 1.5, "", 0, 1, 'L', 0):"";
        $pdf->SetXY(105,$pdf->GetY());
        ($TXT_AGREE_REVIEW2)?$pdf->SetXY(105,$pdf->GetY()).$pdf->Cell($heading_width4[1], 6, str_repeat(".",120), 0, 1, 'L', 0)."\n".$pdf->SetXY(105,$pdf->GetY()).$pdf->Cell($heading_width4[1], 6, str_repeat(".",120), 0, 1, 'L', 0):$pdf->SetXY(105,$pdf->GetY()).$pdf->Cell($heading_width4[1], 6, str_repeat(".",120), 0, 1, 'L', 0)."\n".$pdf->SetXY(105,$pdf->GetY()).$pdf->Cell($heading_width4[1], 6, str_repeat(".",120), 0, 1, 'L', 0)."\n".$pdf->SetXY(105,$pdf->GetY()).$pdf->Cell($heading_width4[1], 6, str_repeat(".",120), 0, 1, 'L', 0)."\n".$pdf->SetXY(105,$pdf->GetY()).$pdf->Cell($heading_width4[1], 6, str_repeat(".",120), 0, 1, 'L', 0);
        $end_multi_call2_y1 = $pdf->GetY();
        $multi_call2_height = $end_multi_call2_y1-$start_multi_call2_y1;
        $pdf->SetXY(105,$pdf->GetY());
        $pdf->Cell($heading_width4[1], 4, "", 0, 1, 'L', 0);
        $pdf->SetXY(105,$pdf->GetY());
        if(trim($DIFF_REVIEW2)) $pdf->Image("../images/checkbox_check.jpg",($pdf->x +3), ($pdf->y +0.5), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x +3), ($pdf->y+0.5 ), 4, 4,"jpg");
        $pdf->SetXY(105,$pdf->GetY());
        $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
        $pdf->Cell($heading_width4[1], 5, $space_line."       มีความเห็นต่าง ดังนี้", 0, 1, 'L', 0);
        $pdf->SetXY(105,$pdf->GetY());
        $pdf->Cell($heading_width4[1], 25, "", 0, 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x;			$pdf->y -= 25;
        $pdf->SetXY(105,$pdf->GetY());
        $start_multi_call2_y2 = $pdf->GetY();
        $pdf->MultiCell( $heading_width4[1]  , 5 , $TXT_DIFF_REVIEW2,0);
        $pdf->SetXY(105,$pdf->GetY());
        ($TXT_DIFF_REVIEW2)?$pdf->Cell($heading_width4[1], 1.5, "", 0, 1, 'L', 0):"";
        ($TXT_DIFF_REVIEW2)?$pdf->SetXY(105,$pdf->GetY()).$pdf->Cell($heading_width4[1], 6, str_repeat(".",120), 0, 1, 'L', 0)."\n".$pdf->SetXY(105,$pdf->GetY()).$pdf->Cell($heading_width4[1], 6, str_repeat(".",120), 0, 1, 'L', 0):$pdf->SetXY(105,$pdf->GetY()).$pdf->Cell($heading_width4[1], 6, str_repeat(".",120), 0, 1, 'L', 0)."\n".$pdf->SetXY(105,$pdf->GetY()).$pdf->Cell($heading_width4[1], 6, str_repeat(".",120), 0, 1, 'L', 0)."\n".$pdf->SetXY(105,$pdf->GetY()).$pdf->Cell($heading_width4[1], 6, str_repeat(".",120), 0, 1, 'L', 0)."\n".$pdf->SetXY(105,$pdf->GetY()).$pdf->Cell($heading_width4[1], 6, str_repeat(".",120), 0, 1, 'L', 0);
        $end_multi_call2_y2 = $pdf->GetY();
        $multi_call2_height2 = $end_multi_call2_y2-$start_multi_call2_y2;
        $pdf->SetXY(105,$pdf->GetY());
        $pdf->Cell($heading_width4[1], 10, "", 0, 1, 'L', 0);
        $PIC_SIGN="";
	if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์
            if($AGREE_REVIEW2 || $DIFF_REVIEW2){
                $PIC_SIGN = getPIC_SIGN($PER_ID_REVIEW2,$PER_CARDNO_REVIEW2);
            }	
            if($PIC_SIGN){
                $pdf->SetXY(105,$pdf->GetY());
                $pdf->Image($PIC_SIGN,($pdf->x+65), ($pdf->y-10), 25, 15,"jpg");	// Original size = wxh (60x15)
            }
	}
        $pdf->SetXY(105,$pdf->GetY());
        $pdf->Cell($heading_width4[1], 5.5, " ลงชื่อ ".str_repeat(".",46)." " , 0, 1, 'R', 0);
        $pdf->SetXY(105,$pdf->GetY());
        $pdf->Cell($heading_width4[1], 5.5, "".$TXT_REVIEW_SIGN_NAME2, 0, 1, 'R', 0);
        $pdf->SetXY(105,$pdf->GetY());
        $pdf->Cell($heading_width4[1], 5.5, "".$txt_valREVIEW_PM_NAME2."  " ,0, 1, 'R', 0);
        $pdf->SetXY(105,$pdf->GetY());
        $pdf->Cell($heading_width4[1], 5.5, "".$txt_date_review12 , 0, 1, 'R', 0);
        $pdf->SetXY($x2+$heading_width4[1],$y);
        
        $LAST_BORDER_HEIGHT1 = $multi_call_height+$multi_call_height2+48.5;
        $LAST_BORDER_HEIGHT2 = $multi_call2_height+$multi_call2_height2+48.5;
        
        if($LAST_BORDER_HEIGHT1<$LAST_BORDER_HEIGHT2){
            $BORDER_L = $LAST_BORDER_HEIGHT2;
        }else{
            $BORDER_L = $LAST_BORDER_HEIGHT1;
        }
        $pdf->Rect($x1,$y,$heading_width4[0],$BORDER_L);
        $pdf->Rect($x2,$y,$heading_width4[1],$BORDER_L);
       
	$pdf->close();
        $filename = 'rpt_kpi_sub.pdf';
	$pdf->Output();
	