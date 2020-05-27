<?php  

	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
        //เซตการแสดงจุดทศนิยม  13/02/2018
        $number_formate = 4;

        
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
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
         // echo"<pre>".  $cmd; 
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
			//$TMP_SHOW_PIC = ($tmp_http_server ? $tmp_http_server."/" : "").$data[PER_PICPATH].$PER_CARDNO."-".$TMP_SIGN.$T_PIC_SEQ.".jpg";
		} else {
			$TMP_PIC_NAME = $data[PER_PICPATH].$data[PER_GENNAME]."-".$TMP_SIGN.$T_PIC_SEQ.".jpg".($tmp_SERVER_NAME ? " [".$tmp_SERVER_NAME."]" : "");
			//$TMP_SHOW_PIC = ($tmp_http_server ? $tmp_http_server."/" : "").$data[PER_PICPATH].$PER_CARDNO."-".$TMP_SIGN.$T_PIC_SEQ.".jpg";
		}
		if(file_exists("../".$TMP_PIC_NAME)){
			$PIC_SIGN = "../".$TMP_PIC_NAME;		//	if($PER_CARDNO && $TMP_PIC_NAME)		$PIC_SIGN = "../../attachments/".$PER_CARDNO."/PER_SIGN/".$TMP_PIC_NAME;
		}
		} //end count	
	return $PIC_SIGN;
	}	

	//หาชื่อหน่วยงาน
	if($PV_CODE){
		$select_org_name = "PV_ENG_NAME";
		$find_org_name = "PV_CODE='$PV_CODE'";
	}else{
		$select_org_name = "ORG_ENG_NAME";
		$find_org_name = "ORG_ID=$DEPARTMENT_ID";
	}
        //echo $DEPARTMENT_ID.'<br>';
	$cmd = " select $select_org_name from PER_ORG where $find_org_name";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$ORG_ENG_NAME = trim($data[$select_org_name]);
	//echo $cmd;

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
 //     echo "<pre>".$cmd;  
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
//Release 5.2.1.25 ให้ดึงตำแหน่งจากแฟ้ม ถ้าไม่มีให้ว่าง
/*	if (!$PL_NAME || !$ORG_NAME) {
		if($PER_TYPE==1){
			$cmd = " select 	b.PL_NAME, c.ORG_NAME, a.PT_CODE 
							 from 		PER_POSITION a, PER_LINE b, PER_ORG c
							 where	a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			if (!$PL_NAME) $PL_NAME = trim($data[PL_NAME]);
			if (!$ORG_NAME) $ORG_NAME = trim($data[ORG_NAME]);
			$PT_CODE = trim($data[PT_CODE]);
			$PT_NAME = trim($data[PT_NAME]);
		}elseif($PER_TYPE==2){
			$cmd = " select 	b.PN_NAME, c.ORG_NAME 
							 from 		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
							 where	a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			if (!$PL_NAME) $PL_NAME = trim($data[PN_NAME]);
			if (!$ORG_NAME) $ORG_NAME = trim($data[ORG_NAME]);
		}elseif($PER_TYPE==3){
			$cmd = " select 	b.EP_NAME, c.ORG_NAME 
							 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
							 where	a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			if (!$PL_NAME) $PL_NAME = trim($data[EP_NAME]);
			if (!$ORG_NAME) $ORG_NAME = trim($data[ORG_NAME]);
		} // end if
	} // end if
*/

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
					//$REVIEW_PL_NAME = trim($REVIEW_PL_NAME)?($REVIEW_PL_NAME . $REVIEW_POSITION_LEVEL . (($REVIEW_PT_NAME != "ทั่วไป" && $REVIEW_LEVEL_NO >= 6)?"$REVIEW_PT_NAME":"")):$REVIEW_LEVEL_NAME;/*เดิม*/
					$REVIEW_PM_CODE = trim($data[PM_CODE]);
                                        
                                        
					if($REVIEW_PM_CODE && !$REVIEW_PM_NAME){
						$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$REVIEW_PM_CODE' ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
			//			$REVIEW_PL_NAME = $data[PM_NAME]." ($REVIEW_PL_NAME)"; 
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
					
                    function get_month($month){
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
                        
                        return $full_month_th;
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
	//echo "<br>$cmd<br>";
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
	//echo "<pre>$cmd<hr>";
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
		//echo "$TOTAL_PC_SCORE =+= $PC_SCORE[$KC_ID] <br>";
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
	
//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	$pdf->SetAutoPageBreak(true,10);

	$heading_width1[0] = "75";
	$heading_width1[1] = "75";
	$heading_width1[2] = "10";
	
	$heading_width2[0] = "80";
	$heading_width2[1] = "40";
	$heading_width2[2] = "40";
	$heading_width2[3] = "40";
	
	$heading_width3[0] = "93";
	$heading_width3[1] = "55";
	$heading_width3[2] = "52";

	$heading_width4[0] = "85";
	$heading_width4[1] = "18";
	$heading_width4[2] = "25";
	$heading_width4[3] = "18";
	$heading_width4[4] = "18";
	$heading_width4[5] = "40";

	$heading_width5[0] = "94";
	$heading_width5[1] = "10";
	$heading_width5[2] = "18";
	$heading_width5[3] = "18";
	$heading_width5[4] = "24";
	
	$heading_width6[0] = "135";
	$heading_width6[1] = "15";
	$heading_width6[2] = "10";
	
	function print_header($header_select){
		global $pdf, $heading_width1, $heading_width2, $heading_width3, $heading_width4, $heading_width5, $heading_width6, $KPI_SCORE_DECIMAL;
		
		if($header_select == 2){
			$pdf->SetFont($font,'',14);
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
			$pdf->Cell($heading_width2[0] ,7,"องค์ประกอบการประเมิน",'LTBR',0,'C',1);
			$pdf->Cell($heading_width2[1] ,7,"คะแนน (ก)",'LTBR',0,'C',1);
			$pdf->Cell($heading_width2[2] ,7,"น้ำหนัก (ข)",'LTBR',0,'C',1);
			$pdf->Cell($heading_width2[3] ,7,"รวมคะแนน (ก)x(ข)",'LTBR',1,'C',1);
		}elseif($header_select == 3){
			$pdf->SetFont($font,'',14);
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
			$pdf->Cell($heading_width3[0] ,7,"ความรู้/ ทักษะ/ สมรรถนะ",'LTR',0,'C',1);
			$pdf->Cell($heading_width3[1] ,12,"",'LTR',0,'C',1);
			$pdf->Cell($heading_width3[2] ,12,"",'LTR',1,'C',1);

			$pdf->y -= 7;
			$pdf->Cell($heading_width3[0] ,12,"ที่ต้องได้รับการพัฒนา",'LBR',0,'C',1);
			$pdf->Cell($heading_width3[1] ,12,"วิธีการพัฒนา",'LBR',0,'C',1);
			$pdf->Cell($heading_width3[2] ,12,"ช่วงเวลาที่ต้องการการพัฒนา",'LBR',1,'C',1);
		}elseif($header_select == 4){
			$pdf->SetFont($font,'',14);
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
			$pdf->Cell($heading_width4[0] ,7,"สมรรถนะ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width4[1] ,7,"ระดับที่",'LTBR',0,'C',1);
			$pdf->Cell($heading_width4[2] ,7,"ผลการประเมิน (ก)",'LTBR',0,'C',1);
			$pdf->Cell($heading_width4[3] ,7,"น้ำหนัก (ข)",'LTBR',0,'C',1);
			$pdf->Cell($heading_width4[4] ,7,"รวมคะแนน",'LTBR',0,'C',1);
			$pdf->Cell($heading_width4[5] ,7,"บันทึกโดยผู้ประเมิน (ถ้ามี)",'LTBR',1,'C',1);
			
			/*$pdf->y -= 7;
			$pdf->Cell($heading_width4[1] ,7,"คาดหวัง",'LBR',0,'C',1);
			$pdf->Cell($heading_width4[4] ,7,"(กxข)",'LBR',0,'C',1);
			$pdf->Cell($heading_width4[5] ,7,"พื้นที่ไม่พอ ให้บันทึกลงในเอกสารหน้าหลัง",'LBR',1,'C',1);  */

			//กรอบขวา
/***
			$pdf->Cell(17, 7, "แนวทางการประเมินสมรรถนะ", 0, 0, 'L', 0);

			$pdf->Cell(17, 7, "ได้นำคะแนนมาจากแบบประเมินสมรรถนะอื่นๆ มาสรุปไว้ในแบบสรุปนี้", 0, 0, 'L', 0);
			$pdf->Cell(17, 7, "ระบุ ..........................................................................................................", 0, 0, 'L', 0);
			
			$pdf->Cell(17, 7, "ใช้แบบสรุปฯนี้ในการประเมินสมรรถนะ โดยตั้งมาตรวัดสมรรถนะซึ่งส่วนราชการเห็นว่ามีความเหมาะสมไว้ดังนี้", 0, 0, 'L', 0);
			
			$pdf->Cell(17, 7, "คะแนน		นิยาม", 0, 0, 'L', 0);
			$pdf->Cell(17, 7, "๑					ไม่สังเกตุเห็น (Not Abserve)", 0, 0, 'L', 0);
			$pdf->Cell(17, 7, "๑					กำลังพัฒนา โดยต้องใช้เวลาอีกมากจึงจะพัฒนาได้", 0, 0, 'L', 0);
			$pdf->Cell(17, 7, "๓					กำลังพัฒนา	 (Developing)", 0, 0, 'L', 0);
			$pdf->Cell(17, 7, "๔					อยู่ในระดับที่ใช้งานได้ (Sufficient)", 0, 0, 'L', 0);
			$pdf->Cell(17, 7, "๕					เป็นแบบอย่างที่ดีให้กับผู้อื่น (Role Model)", 0, 0, 'L', 0);
***/
		}elseif($header_select == 5){	//แบบสรุปการประเมินผลสัมฤทธิ์ของงาน (PAGE 6)
			$pdf->SetFont($font,'',14);
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	
			$pdf->Cell($heading_width5[0] ,14,"ตัวชี้วัดผลงาน",'LTBR',0,'C',1);
			$pdf->Cell(($heading_width5[1] * 5) ,7, "คะแนนตามระดับค่าเป้าหมาย",'LTBR',0,'C',1);
			$pdf->Cell($heading_width5[2] ,14,"คะแนน (ก)",'LTBR',0,'C',1);
			$pdf->Cell($heading_width5[3] ,14,"น้ำหนัก (ข)",'LTBR',0,'C',1);
			$pdf->Cell($heading_width5[4] ,14,"รวมคะแนน (กxข)",'LTBR',0,'C',1);

			$pdf->x = ($heading_width5[0]+5);
			$pdf->y = $pdf->y + 7;
			for($i=1; $i<=5; $i++){ 
				if($i < 5) $pdf->Cell($heading_width5[1] ,7, convert2thaidigit($i),'LTBR',0,'C',1);
				else $pdf->Cell($heading_width5[1] ,7, convert2thaidigit($i),'LTBR',1,'C',1);
			} // end for
		}elseif($header_select == 6){
			$pdf->SetFont($font,'',14);
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	
			if ($KPI_SCORE_DECIMAL==1) {	
				$pdf->Cell($heading_width6[0] + ($heading_width6[2] * 2) ,7,"ผลงานจริง",'LTBR',0,'C',1);
				$pdf->Cell($heading_width6[1] ,7,"น้ำหนัก",'LTBR',0,'C',1);
				$pdf->Cell(($heading_width6[2] * 3) ,7, "ผลการประเมิน",'LTBR',1,'C',1);
			} else {
				$pdf->Cell($heading_width6[0] ,14,"ผลงานจริง",'LTBR',0,'C',1);
				$pdf->Cell($heading_width6[1] ,14,"น้ำหนัก",'LTBR',0,'C',1);
				$pdf->Cell(($heading_width6[2] * 5) ,7, "ผลการประเมิน",'LTBR',1,'C',1);
				
				$pdf->x += ($heading_width6[0] + $heading_width6[1]);
				for($i=1; $i<=5; $i++){ 
					if($i < 5) $pdf->Cell($heading_width6[2] ,7, convert2thaidigit($i),'LTBR',0,'C',1);
					else $pdf->Cell($heading_width6[2] ,7, convert2thaidigit($i),'LTBR',1,'C',1);
				} // end for
			}	// end if
		}	// end if
	} // function		

	$pdf->AutoPageBreak = false;
	
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
	$pdf->Cell(200, 10, "ส่วนที่ ๑:  ข้อมูลของผู้รับการประเมิน", 1, 1, 'L', 1);
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->SetFont($font,'b','',18);
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(30, 10, "รอบการประเมิน", 0, 0, 'L', 0);
	$pdf->Cell(15, 10, "", 0, 0, 'L', 0);
	$pdf->Cell(25, 10, convert2thaidigit("รอบที่ ๑"), 0, 0, 'L', 0);
	$pdf->Cell(22, 10, convert2thaidigit("๑ ตุลาคม "), 0, 0, 'L', 0);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(18, 10, convert2thaidigit(($KF_CYCLE==1)?($KF_YEAR - 1):""), 0, 0, 'L', 0);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(32, 10, convert2thaidigit("ถึง ๓๑ มีนาคม "), 0, 0, 'L', 0);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(20, 10, convert2thaidigit(($KF_CYCLE==1)?$KF_YEAR:""), 0, 1, 'L', 0);
	if($KF_CYCLE==1) $pdf->Image("../images/checkbox_check.jpg", 58, ($pdf->y - 7), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", 58, ($pdf->y - 7), 4, 4,"jpg");

	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->SetFont($font,'b','',18);
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(30, 10, "", 0, 0, 'L', 0);
	$pdf->Cell(15, 10, "", 0, 0, 'L', 0);
	$pdf->Cell(25, 10, convert2thaidigit("รอบที่ ๒"), 0, 0, 'L', 0);
	$pdf->Cell(22, 10, convert2thaidigit("๑ เมษายน "), 0, 0, 'L', 0);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(18, 10, convert2thaidigit(($KF_CYCLE==2)?$KF_YEAR:""), 0, 0, 'L', 0);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(32, 10, convert2thaidigit("ถึง ๓๐ กันยายน "), 0, 0, 'L', 0);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(20, 10, convert2thaidigit(($KF_CYCLE==2)?$KF_YEAR:""), 0, 1, 'L', 0);	
	if($KF_CYCLE==2) $pdf->Image("../images/checkbox_check.jpg", 58, ($pdf->y - 7), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", 58, ($pdf->y - 7), 4, 4,"jpg");

	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->SetFont($font,'b','',18);
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(40, 10, "ชื่อผู้รับการประเมิน  ", 0, 0, 'L', 0);
	$pdf->SetFont($font,'b','',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(130, 10, "$PER_NAME  ", 0, 1, 'L', 0);
	
	$pdf->SetFont($font,'b','',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(15, 10, "ตำแหน่ง  ", 0, 0, 'L', 0);
	$pdf->SetFont($font,'b','',16);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(15, 10, convert2thaidigit("$PL_NAME  "), 0, 1, 'L', 0);
	
	if ($PER_TYPE==1) {
		$pdf->SetFont($font,'b','',18);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
		$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
		$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
		$pdf->Cell(15, 10, "ประเภทตำแหน่ง  ", 0, 0, 'L', 0);
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
		$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
		$pdf->Cell(15, 10, convert2thaidigit("$POSITION_TYPE  "), 0, 0, 'L', 0);
		$pdf->SetFont($font,'b','',18);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
		$pdf->Cell(40, 10, "", 0, 0, 'C', 0);
		$pdf->Cell(40, 10, "ระดับตำแหน่ง  ", 0, 0, 'L', 0);
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
		$pdf->Cell(130, 10, convert2thaidigit("$POSITION_LEVEL  "), 0, 1, 'L', 0);
	} elseif ($PER_TYPE==3) {
		$pdf->SetFont($font,'b','',18);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
		$pdf->Cell(40, 10, "", 0, 0, 'C', 0);
		$pdf->Cell(40, 10, "กลุ่มงาน  ", 0, 0, 'L', 0);
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
		$pdf->Cell(130, 10, convert2thaidigit("$POSITION_LEVEL  "), 0, 1, 'L', 0);
	}
	
	$pdf->SetFont($font,'b','',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(15, 10, "สังกัด  ", 0, 0, 'L', 0);
	$pdf->SetFont($font,'b','',16);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(15, 10, convert2thaidigit("$ORG_NAME  "), 0, 1, 'L', 0);
	
	$pdf->SetFont($font,'b','',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(15, 10, "เงินเดือน  ", 0, 0, 'L', 0);
	$pdf->SetFont($font,'b','',16);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(15, 10, convert2thaidigit(number_format($PER_SALARY)), 0, 0, 'L', 0);
	$pdf->SetFont($font,'b','',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(15, 10, "บาท", 0, 1, 'L', 0);

	$pdf->SetFont($font,'b','',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 4, "", 0, 1, 'C', 0);
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(30, 10, "ชื่อผู้ประเมิน  ", 0, 0, 'L', 0);
	$pdf->SetFont($font,'b','',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(130, 10, $REVIEW_PER_NAME, 0, 1, 'L', 0);

	$pdf->SetFont($font,'b','',18);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
	$pdf->Cell(15, 10, "ตำแหน่ง  ", 0, 0, 'L', 0);
	$pdf->SetFont($font,'b','',16);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(15, 10, "", 0, 0, 'C', 0);
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
       
        
	$pdf->Cell(15, 10, convert2thaidigit("$REVIEW_PL_NAME"), 0, 1, 'L', 0);
	
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->SetFont($font,'b','',16);
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);	
	$pdf->Cell(10, 10, "", 0, 0, 'C', 0);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("99"),hexdec("CC"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(180, 10, "คำชี้แจง", 0, 1, 'L', 1);

	$pdf->SetFont($font,'',14);
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);	
	$pdf->Cell(12, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(170, 7, " แบบสรุปการประเมินผลการปฏิบัติราชการนี้ มีด้วยกัน ๓ หน้า ประกอบด้วย", 0, 1, 'L', 0);

	$pdf->SetFont($font,'b','',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "ส่วนที่ ๑: ", 0, 0, 'L', 0);
	$pdf->Cell(40, 7, "ข้อมูลของผู้รับการประเมิน", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(40, 7, "เพื่อระบุรายละเอียดต่างๆ ที่เกี่ยวข้องกับตัวผู้รับการประเมิน", 0, 1, 'L', 0);

	$pdf->SetFont($font,'b','',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "ส่วนที่ ๒: ", 0, 0, 'L', 0);
	$pdf->Cell(40, 7, "สรุปผลการประเมิน", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(40, 7, "ใช้เพื่อกรอกค่าคะแนนการประเมินในองค์ประกอบด้านผลสัมฤทธิ์ของงาน องค์ประกอบ", 0, 1, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "ด้านพฤติกรรมการปฏิบัติราชการ และน้ำหนักของทั้งสององค์ประกอบ ในแบบส่วนสรุปส่วนที่ ๒ นี้", 0, 1, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "ยังใช้สำหรับคำนวณคะแนนผลการปฏิบัติราชการรวมด้วย", 0, 1, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "- สำหรับคะแนนองค์ประกอบด้านผลสัมฤทธิ์ของงาน ให้นำมาจากแบบประเมินผลสัมฤทธิ์ของงาน โดยให้แนบท้ายแบบสรุปฉบับนี้", 0, 1, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "- สำหรับคะแนนองค์ประกอบด้านพฤติกรรมการปฏิบัติราชการ ให้นำมาจากแบบประเมินสมรรถนะ โดยให้แนบท้ายแบบสรุปฉบับนี้", 0, 1, 'L', 0);

	$pdf->SetFont($font,'b','',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "ส่วนที่ ๓: ", 0, 0, 'L', 0);
	$pdf->Cell(40, 7, "แผนพัฒนาการปฏิบัติราชการรายบุคคล", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(40, 7, "ผู้ประเมิน และผู้รับการประเมินร่วมกันจัดทำแผนพัฒนาผลการปฏิบัติราชการ", 0, 1, 'L', 0);

	$pdf->SetFont($font,'b','',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "ส่วนที่ ๔: ", 0, 0, 'L', 0);
	$pdf->Cell(40, 7, "การรับทราบผลการประเมิน", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(40, 7, "ผู้รับการประเมินลงนามรับทราบผลการประเมิน", 0, 1, 'L', 0);
		
	$pdf->SetFont($font,'b','',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, "ส่วนที่ ๕: ", 0, 0, 'L', 0);
	$pdf->Cell(40, 7, "ความเห็นของผู้บังคับบัญชาเหนือขึ้นไป", 0, 0, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(40, 7, "ผู้บังคับบัญชาเหนือขึ้นไปกลั่นกรองผลการประเมิน แผนพัฒนาผลการปฏิบัติ", 0, 1, 'L', 0);
	$pdf->SetFont($font,'',14);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(17, 7, " ราชการและให้ความเห็น", 0, 1, 'L', 0);
	
	$pdf->SetFont($font,'',14);
//	$pdf->Cell(220, 7, "", 0, 1, 'C', 0);
	$pdf->Cell(17, 7, "", 0, 0, 'C', 0);
	$pdf->Cell(170, 7, "คำว่า 'ผู้บังคับบัญชาเหนือขึ้นไป' สำหรับผู้ประเมินตามข้อ ๒ (๙) หมายถึงหัวหน้าส่วนราชการประจำจังหวัดผู้บังคับบัญชาของผู้รับการประเมิน", 0, 1, 'C', 0);

	// ======================= PAGE 2 =====================//	
	$pdf->AddPage();
	$pdf->SetFont($font,'',14);

	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("99"),hexdec("CC"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "ส่วนที่ ๒: การสรุปผลการประเมิน", 1, 1, 'L', 1);
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);

	print_header(2);

	$border = "";
	$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

	$pdf->SetFont($font,'',12);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

	$pdf->Cell($heading_width2[0], 7, convert2thaidigit("องค์ประกอบที่ ๑: ผลสัมฤทธิ์ของงาน"), 'LTBR', 0, 'L', 0);
        
	$pdf->Cell($heading_width2[1], 7, showscore(convert2thaidigit(number_format($SHOW_SCORE_KPI, 2))), 'LTBR', 0, 'C', 0);
	if ($PERFORMANCE_WEIGHT)
		$pdf->Cell($heading_width2[2], 7, convert2thaidigit($PERFORMANCE_WEIGHT . "%"), 'LTBR', 0, 'C', 0);
	else
		$pdf->Cell($heading_width2[2], 7, "", 'LTBR', 0, 'C', 0);
	$pdf->Cell($heading_width2[3], 7, showscore(convert2thaidigit(number_format($SUM_KPI, 2))), 'LTBR', 1, 'C', 0);
	
	//แถวที่ 2
	$pdf->Cell($heading_width2[0], 7, convert2thaidigit("องค์ประกอบที่ ๒: พฤติกรรมการปฏิบัติราชการ (สมรรถนะ)"), 'LTBR', 0, 'L', 0);
	$pdf->Cell($heading_width2[1], 7, showscore(convert2thaidigit(number_format($SHOW_SCORE_COMPETENCE, 2))), 'LTBR', 0, 'C', 0);
	if ($COMPETENCE_WEIGHT)
		$pdf->Cell($heading_width2[2], 7, convert2thaidigit($COMPETENCE_WEIGHT . "%"), 'LTBR', 0, 'C', 0);
	else
		$pdf->Cell($heading_width2[2], 7, "", 'LTBR', 0, 'C', 0);
	$pdf->Cell($heading_width2[3], 7, showscore(convert2thaidigit(number_format($SUM_COMPETENCE, 2))), 'LTBR', 1, 'C', 0);
	
	//แถวที่ 2
	$pdf->Cell($heading_width2[0], 7, convert2thaidigit("องค์ประกอบอื่นๆ (ถ้ามี)"), 'LTBR', 0, 'L', 0);
	$pdf->Cell($heading_width2[1], 7, showscore(convert2thaidigit(number_format($SHOW_SCORE_OTHER, 2))), 'LTBR', 0, 'C', 0);
	if ($OTHER_WEIGHT)
		$pdf->Cell($heading_width2[2], 7, convert2thaidigit($OTHER_WEIGHT . "%"), 'LTBR', 0, 'C', 0);
	else
		$pdf->Cell($heading_width2[2], 7, "", 'LTBR', 0, 'C', 0);
	$pdf->Cell($heading_width2[3], 7, showscore(convert2thaidigit(number_format($SUM_OTHER, 2))), 'LTBR', 1, 'C', 0);
	
	//แถวที่ 3
	$pdf->Cell($heading_width2[0] + $heading_width2[1] , 7, "รวม", 'LTBR', 0, 'R', 0);
	$pdf->Cell($heading_width2[2], 7,convert2thaidigit("๑๐๐%"), 'LTBR', 0, 'C', 0);
	$pdf->Cell($heading_width2[3], 7, showscore(convert2thaidigit(number_format($SUM_TOTAL, 2))), 'LTBR', 1, 'C', 0);

	//================= Draw Border Line ====================
	$line_start_y = $start_y;		$line_start_x = $start_x;
	$line_end_y = $max_y;		$line_end_x = $start_x;
	$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
		
	for($i=0; $i<=4; $i++){
		$line_start_y = $start_y;		$line_start_x += $heading_width2[$i];
		$line_end_y = $max_y;		$line_end_x += $heading_width2[$i];
		$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
	} // end for
	//====================================================

	if(($pdf->h - $max_y - 10) < 16){ 
		$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
		if($data_count < $COUNT_KPI){
			$pdf->AddPage();
			print_header(2);
			$max_y = $pdf->y;
		} // end if
	}else{
		$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
	} // end if
	$pdf->x = $start_x;			$pdf->y = $max_y;

	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 35, "", 0, 1, 'C', 0);
	$pdf->Cell(40, 7, "ระดับผลการประเมิน", 0, 1, 'C', 1);
	$pdf->Cell(400, 3, "", 0, 1, 'C', 0);	
	
        if($SCORE_KPI > 0 && $SCORE_COMPETENCE>0){
            /*Release 5.2.1.8 */
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
                //echo $cmd;
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
        }//if check
       
       // echo "<pre>".$cmd;
        /**/
        
	//หาระดับผลการประเมินหลัก 
	$cmd = "	select		AM_CODE, AM_NAME, AM_POINT_MIN, AM_POINT_MAX, AM_ACTIVE
								from		PER_ASSESS_MAIN where AM_YEAR = '$KF_YEAR' and AM_CYCLE = $KF_CYCLE and PER_TYPE = $PER_TYPE
					order by AM_POINT_MAX desc, AM_CODE desc ";
 	//echo "$cmd : <br>";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$AM_CODE = $data[AM_CODE];
                $AM_NAME = $data[AM_NAME];
		$AM_POINT_MIN = $data[AM_POINT_MIN];
		$AM_POINT_MAX = $data[AM_POINT_MAX];
/*เดิม*/
////	if($SUM_TOTAL >= $AM_POINT_MIN && $SUM_TOTAL <= $AM_POINT_MAX){
////			$pdf->SetFont($font,'b','',16);
////			$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
////			$show_checkbox = "../images/checkbox_check.jpg";
////	}else{
//			$pdf->SetFont($font,'',14);
//			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
//			$show_checkbox = "../images/checkbox_blank.jpg";
////	}

                /*Release 5.2.1.8 Begin*/
                        if($AM_CODE==$chkam_code){
                            $pdf->SetFont($font,'',14);
                            $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
                            $show_checkbox = "../images/checkbox_check.jpg";
                        }else{
                            $pdf->SetFont($font,'',14);
                            $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
                            $show_checkbox = "../images/checkbox_blank.jpg";  
                        }
                /*Release 5.2.1.8 End*/        
		$pdf->Cell(25, 8, "", 0, 0, 'C', 0);
		$pdf->Cell(10, 8, "", 0, 0, 'C', 0);
		$pdf->Image($show_checkbox, ($pdf->x - 7), ($pdf->y + 2), 4, 4,"jpg");
		$pdf->Cell(50, 8, "$AM_NAME", 0, 1, 'L', 0);
//		$pdf->Cell(30, 8, convert2thaidigit("$AM_POINT_MIN %")." - ".convert2thaidigit("$AM_POINT_MAX %"), 0, 1, 'L', 0);
	} //end while
	
	//--------------------------------------------------
	$pdf->Cell(200, 7, "", 0, 1, 'C', 0);
        $pdf->AddPage(); //pitak
	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("99"),hexdec("CC"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "ส่วนที่ ๓: แผนพัฒนาการปฏิบัติราชการรายบุคคล", 1, 1, 'L', 1);
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	
	print_header(3);

	$data_count = 0;
	foreach($ARR_IPIP as $IPIP_ID => $DEVELOP_COMPETENCE){
		$data_count++;
		$border = "";
		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

		$pdf->SetFont($font,'',12);
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

    $pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(200, 7, "ความเห็นของผู้บังคับบัญชาชั้นต้น:", 1, 1, 'L', 1);	
	
	
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->Cell(190, 7, " ความเห็นด้านผลงาน", 0, 1, 'L', 0);

	$pdf->SetFont($font,'',12);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->MultiCell(190, 5, convert2thaidigit(str_repeat(" ", 5).$RESULT_COMMENT), "", "L");
	$pdf->x = $save_x;		$pdf->y = $save_y;
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	

	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->Cell(190, 7, "ความเห็นด้านสมรรถนะ", 0, 1, 'L', 0);
	$pdf->SetFont($font,'',12);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->MultiCell(190, 5, convert2thaidigit(str_repeat(" ", 5).$COMPETENCE_COMMENT), "", "L");
	$pdf->x = $save_x;		$pdf->y = $save_y;
	
	/*if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์	
		if($COMPETENCE_COMMENT || $RESULT_COMMENT){
			$PIC_SIGN = getPIC_SIGN($PER_ID_REVIEW,$PER_CARDNO_REVIEW_N);	
		}
		if($PIC_SIGN){
			$pdf->Cell(120, 15, "", "L", 0, 'C', 0);
			$pdf->Image($PIC_SIGN,($pdf->x+1), ($pdf->y+1), 60, 15,"jpg");	// Original size = wxh (60x15)
			$save_x = $pdf->x;		$save_y = $pdf->y;
			$pdf->x += 5;			$pdf->y -= 15;
			$pdf->Cell(120, 15, "", "R", 1, 'C', 0); 
		}else{
			$pdf->SetFont($font,'',12);
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
			$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
			$pdf->Cell(100, 5, ("ลงชื่อ $REVIEW_SIGN_PN_NAME". str_repeat(".", 100)), "R", 1, 'C', 0);
		}
	}else{
		$pdf->SetFont($font,'',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
		$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
		$pdf->Cell(100, 5, ("ลงชื่อ $REVIEW_SIGN_PN_NAME". str_repeat(".", 100)), "R", 1, 'C', 0);
	}*/
	/*$REVIEW_SIGN_NAME = trim($REVIEW_SIGN_NAME)?$REVIEW_SIGN_NAME:"".str_repeat(" ", 100)."";		//ถ้าไม่มีข้อมูล แสดงช่องว่าง
	if($PIC_SIGN){
		$pdf->Cell(100, 20, "", "L", 0, 'C', 0);
		$pdf->Cell(100, 20, "", "R", 1, 'C', 0); 
	}
	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
	$pdf->Cell(100, 5, ("( $REVIEW_SIGN_NAME )"), "R", 1, 'C', 0);
*/
	$REVIEW_PL_NAME = trim($REVIEW_PL_NAME)?$REVIEW_PL_NAME:"".str_repeat(" ", 100)."";		//ถ้าไม่มีข้อมูล แสดงช่องว่าง
	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
        /*Release 5.1.0.5 Begin*/
        
        /*if(!empty($REVIEW_PM_NAME)){
            $REVIEW_PL_NAME = $REVIEW_PM_NAME;
        }else{
            $REVIEW_PL_NAME = strreplaceLvl($REVIEW_PL_NAME.$REVIEW_LEVEL_NAME);
        }*/
        /*Release 5.1.0.5 End*/
	$pdf->Cell(100, 5, (" "), "R", 1, 'C', 0);

	$pdf->Cell(100, 5, "", "LB", 0, 'C', 0);
       /* if($KF_SCORE_STATUS == "1" && $DATE_REVIEW0){
        $pdf->Cell(100, 5, ("วันที่ ".$DATE_REVIEW0_DAY ." เดือน ".$DATE_REVIEW0_MONTH = get_month($DATE_REVIEW0_MONTH) ." พ.ศ. ".$DATE_REVIEW0_YEAR), "BR", 1, 'C', 0);
        }else{
        $pdf->Cell(100, 5,("วันที่ ". str_repeat(".", 13) ." เดือน ". str_repeat(".", 30) ." พ.ศ. ". str_repeat(".", 20)), "BR", 1, 'C', 0);
        }*/
		$pdf->Cell(100, 5,(""), "BR", 1, 'C', 0);
	$PIC_SIGN = "";		
	

	// ======================= PAGE 3 =====================//	
	$pdf->AddPage();
	$pdf->SetFont($font,'',14);

	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("99"),hexdec("CC"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "ส่วนที่ ๔: การรับทราบผลการประเมิน", 1, 1, 'L', 1);
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);

	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(200, 7, "ผู้รับการประเมิน:", 1, 1, 'L', 1);
	//if($PER_NAME) $pdf->Image("../images/checkbox_check.jpg",($pdf->x+1), ($pdf->y+1), 4, 4,"jpg");
	//else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x +1), ($pdf->y+1), 4, 4,"jpg");
        $str_accept='';
        if($ACCEPT_FLAG=="1"){
            $pdf->Image("../images/checkbox_check.jpg",($pdf->x+1), ($pdf->y+1), 4, 4,"jpg");
           // if($ACCEPT_FLAG=="0"){$str_accept=' (ไม่ยอมรับ)';}
           // if($ACCEPT_FLAG==1){$str_accept=' (ยอมรับ)';}
        }else{
            $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x +1), ($pdf->y+1), 4, 4,"jpg");
        }
        
	
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->Cell(190, 7, "ได้รับทราบผลการประเมินและแผนพัฒนาผลการปฏิบัติราชการรายบุคคลแล้ว ".$str_accept, 0, 1, 'L', 0);
	$MONTH_ACCEPT = get_month($ACCEPT_MONTH);
	//ผุ้รับการประเมิน
		$PIC_SIGN="";
	if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์	
            if($ACCEPT_FLAG == 1){
		$PIC_SIGN = getPIC_SIGN($PER_ID,$PER_CARDNO);
            }    
            if($PIC_SIGN){
                    $pdf->Cell(120, 15, "", "L", 0, 'C', 0);
                    $pdf->Image($PIC_SIGN,($pdf->x+1), ($pdf->y+1), 60, 15,"jpg");	// Original size = wxh (60x15)
                    $save_x = $pdf->x;		$save_y = $pdf->y;
                    $pdf->x += 5;			$pdf->y -= 15;
                    $pdf->Cell(120, 15, "", "R", 1, 'C', 0); 
            }else{
                    $pdf->SetFont($font,'',12);
                    $pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
                    $pdf->Cell(100, 5, "", "L", 0, 'C', 0);
                    $pdf->Cell(100, 5, ("ลงชื่อ $SIGN_PN_NAME". str_repeat(".", 100)), "R", 1, 'C', 0);
            }
	}else{
		$pdf->SetFont($font,'',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
		$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
		$pdf->Cell(100, 5, ("ลงชื่อ $SIGN_PN_NAME". str_repeat(".", 100)), "R", 1, 'C', 0); 
	} 
	if($PIC_SIGN){
		$pdf->Cell(100, 20, "", "L", 0, 'C', 0);
		$pdf->Cell(100, 20, "", "R", 1, 'C', 0); 
	}
	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
	$pdf->Cell(100, 5, ("( $SIGN_NAME )"), "R", 1, 'C', 0); 

	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
        /*Release 5.1.0.5 Begin*/
        if(!empty($PM_NAME)){ 
            $valPM_NAME = $PM_NAME;
        }else{
			if(!$PL_NAME){
				$valPM_NAME = "";
			}else{
				$valPM_NAME = strreplaceLvl($PL_NAME.$POSITION_LEVEL);
			}
            
            //$valPM_NAME = strreplaceLvl($PL_NAME);
        }
	$pdf->Cell(100, 5, ("ตำแหน่ง ".convert2thaidigit("$valPM_NAME")), "R", 1, 'C', 0);
        /*Release 5.1.0.5 End*/
	$pdf->Cell(100, 5, "", "LB", 0, 'C', 0);
 	
        if($ACCEPT_DATE && ($ACCEPT_FLAG == "0" || $ACCEPT_FLAG == "1") && $CH_SCORE_DAY=="N"){//&& $CH_SCORE_DAY=="N"
			$pdf->Cell(100, 5, ("วันที่ ".$ACCEPT_DAY ." เดือน ".$MONTH_ACCEPT ." พ.ศ. ".$ACCEPT_YEAR), "BR", 1, 'C', 0);           
        }else{
			$pdf->Cell(100, 5, ("วันที่ ". str_repeat(".", 13) ." เดือน ". str_repeat(".", 30) ." พ.ศ. ". str_repeat(".", 20)), "BR", 1, 'C', 0);
        }    
	
        //$pdf->Cell(100, 5, ("วันที่ ". str_repeat(".", 13) ." เดือน ". str_repeat(".", 30) ." พ.ศ. ". str_repeat(".", 20)), "BR", 1, 'C', 0);
	if($SESS_KPI_PER_REVIEW==1){		// ผู้ให้ข้อมูล
		$pdf->SetFont($font,'',14);
		$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
		$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
		$pdf->Cell(200, 7, "ผู้ให้ข้อมูล:", 1, 1, 'L', 1);
		$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x + 1), ($pdf->y + 1), 4, 4,"jpg");
		$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
		$save_x = $pdf->x;		$save_y = $pdf->y;
		$pdf->x += 5;			$pdf->y -= 15;
		$pdf->Cell(190, 7, "ได้แจ้งผลการให้ข้อมูล เมื่อวันที่ ". str_repeat(".", 50), 0, 1, 'L', 0);

		//ลงชื่อผู้ประเมิน
		$PIC_SIGN="";
		if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์	
			if(($KF_SCORE_STATUS!="" && $KF_SCORE_STATUS!=0)&& $KF_SCORE_STATUS == 1){
                            $PIC_SIGN = getPIC_SIGN($PER_ID_REVIEW0,$PER_CARDNO_REVIEW0);
			}
			if($PIC_SIGN){
				$pdf->Cell(120, 15, "", "L", 0, 'C', 0);
				$pdf->Image($PIC_SIGN,($pdf->x+1), ($pdf->y+1), 60, 15,"jpg");	// Original size = wxh (60x15)
				$save_x = $pdf->x;		$save_y = $pdf->y;
				$pdf->x += 5;			$pdf->y -= 15;
				$pdf->Cell(120, 15, "", "R", 1, 'C', 0); 
			}else{
				$pdf->SetFont($font,'',12);
				$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
				$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
				$pdf->Cell(100, 5, ("ลงชื่อ $REVIEW_SIGN_PN_NAME0". str_repeat(".", 100)), "R", 1, 'C', 0);
			}
		}else{
				$pdf->SetFont($font,'',12);
				$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
				$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
				$pdf->Cell(100, 5, ("ลงชื่อ $REVIEW_SIGN_PN_NAME0". str_repeat(".", 100)), "R", 1, 'C', 0);
		}
		
		$REVIEW_SIGN_NAME0 = trim($REVIEW_SIGN_NAME0)?$REVIEW_SIGN_NAME0:"".str_repeat(" ", 100)."";		//ถ้าไม่มีข้อมูล แสดงช่องว่าง
		if($PIC_SIGN){
			$pdf->Cell(100, 20, "", "L", 0, 'C', 0);
			$pdf->Cell(100, 20, "", "R", 1, 'C', 0); 
		}
		$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
		$pdf->Cell(100, 5, ("( $REVIEW_SIGN_NAME0 )"), "R", 1, 'C', 0);							
		$REVIEW_PL_NAME0_CHK = trim($REVIEW_PL_NAME0)?1:0;
		$REVIEW_PL_NAME0 = trim($REVIEW_PL_NAME0)?$REVIEW_PL_NAME0:"".str_repeat(" ", 100)."";		//ถ้าไม่มีข้อมูล แสดงช่องว่าง
		$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
                /*Release 5.1.0.5 Begin*/
                if(!empty($REVIEW_PM_NAME0)){
                    $REVIEW_PL_NAME0 = $REVIEW_PM_NAME0;
                }else{
					if(!$REVIEW_PL_NAME0){
						$REVIEW_PL_NAME0 = "";
					}else{
						$REVIEW_PL_NAME0 = ($REVIEW_PL_NAME0_CHK==1)?strreplaceLvl($REVIEW_PL_NAME0.$REVIEW_LEVEL_NAME0):"".str_repeat(" ", 100);
						
					}
                   
                }
                /*Release 5.1.0.5 End*/
		$pdf->Cell(100, 5, ("ตำแหน่ง $REVIEW_PL_NAME0 "), "R", 1, 'C', 0);
	
		$pdf->Cell(100, 5, "", "LB", 0, 'C', 0);
		$pdf->Cell(100, 5, ("วันที่ ". str_repeat(".", 13) ." เดือน ". str_repeat(".", 30) ." พ.ศ. ". str_repeat(".", 20)), "BR", 1, 'C', 0);
	}  //end if

        $creat_date = $SCORE_DAY." ".$ACCEPT_MONTH = get_month($SCORE_MONT)." ".$SCORE_YEAR;
	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(200, 7, "ผู้ประเมิน:", 1, 1, 'L', 1);
	if($ACCEPT_FLAG=="1")$pdf->Image("../images/checkbox_check.jpg",($pdf->x+1), ($pdf->y+1), 4, 4,"jpg");
        else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x +1), ($pdf->y+1), 4, 4,"jpg");
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15; 
	$pdf->Cell(190, 7, "ได้แจ้งผลการประเมินและผู้รับการประเมินได้ลงนามรับทราบ", 0, 1, 'L', 0);
	if($ACCEPT_FLAG=="0") $pdf->Image("../images/checkbox_check.jpg",($pdf->x +1), ($pdf->y + 1), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x +1), ($pdf->y + 1), 4, 4,"jpg");
	//$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x + 1), ($pdf->y + 1), 4, 4,"jpg");
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
        if($ACCEPT_FLAG=="0" && $KF_SCORE_STATUS == "1" && $SCORE_STATUS_DATE!=""){
	$pdf->Cell(190, 7, "ได้แจ้งผลการประเมิน เมื่อวันที่ ". $creat_date, 0, 1, 'L', 0);
        }else{
        $pdf->Cell(190, 7, "ได้แจ้งผลการประเมิน เมื่อวันที่ ". str_repeat(".", 50), 0, 1, 'L', 0);
        }
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->Cell(190, 7, "แต่ผู้รับการประเมินไม่ลงนามรับทราบ", 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->Cell(190, 7, "โดยมี ". str_repeat(".", 75)."เป็นพยาน", 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 10;
	//ลงชื่อพยาน
	$pdf->SetFont($font,'',12);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(190, 7, ("ลงชื่อ ". str_repeat(".", 80)."พยาน"), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->Cell(190, 7, ("ตำแหน่ง". str_repeat(".", 77)), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->Cell(170, 7, ("วันที่ ". str_repeat(".", 13) ." เดือน ". str_repeat(".", 30) ." พ.ศ. ". str_repeat(".", 20)), 0, 1, 'L', 0);
	
	//ลงชื่อผู้ประเมิน
        $MONTH_SCORE = get_month($SCORE_MONT);
		$PIC_SIGN="";
	if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์	
		if($KF_SCORE_STATUS == "1" && $PER_ID_REVIEW){
			$PIC_SIGN = getPIC_SIGN($PER_ID_REVIEW,$PER_CARDNO_REVIEW_N);	
		}
		if($PIC_SIGN){
			$pdf->Cell(120, 15, "", "L", 0, 'C', 0);
			$pdf->Image($PIC_SIGN,($pdf->x+1), ($pdf->y+1), 60, 15,"jpg");	// Original size = wxh (60x15)
			$save_x = $pdf->x;		$save_y = $pdf->y;
			$pdf->x += 5;			$pdf->y -= 15;
			$pdf->Cell(120, 15, "", "R", 1, 'C', 0); 
		}else{
			$pdf->SetFont($font,'',12);
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
			$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
			$pdf->Cell(100, 5, ("ลงชื่อ $REVIEW_SIGN_PN_NAME". str_repeat(".", 100)), "R", 1, 'C', 0);
		}
	}else{
			$pdf->SetFont($font,'',12);
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
			$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
			$pdf->Cell(100, 5, ("ลงชื่อ $REVIEW_SIGN_PN_NAME". str_repeat(".", 100)), "R", 1, 'C', 0);
	}

	$REVIEW_SIGN_NAME = trim($REVIEW_SIGN_NAME)?$REVIEW_SIGN_NAME:"".str_repeat(" ", 100)."";		//ถ้าไม่มีข้อมูล แสดงช่องว่าง
	if($PIC_SIGN){
		$pdf->Cell(100, 20, "", "L", 0, 'C', 0);
		$pdf->Cell(100, 20, "", "R", 1, 'C', 0); 
	}
	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
	$pdf->Cell(100, 5, ("( $REVIEW_SIGN_NAME )"), "R", 1, 'C', 0);
	$REVIEW_PL_NAME_CHK = trim($REVIEW_PL_NAME)?1:0;
	$REVIEW_PL_NAME = trim($REVIEW_PL_NAME)?$REVIEW_PL_NAME:"".str_repeat(" ", 100)."";		//ถ้าไม่มีข้อมูล แสดงช่องว่าง
	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
        /*Release 5.1.0.5 Begin*/
        if(!empty($REVIEW_PM_NAME)){
            $REVIEW_PL_NAME = $REVIEW_PM_NAME;
        }else{
			if(!$REVIEW_PL_NAME){
				$REVIEW_PL_NAME = "";
			}else{
				$REVIEW_PL_NAME = ($REVIEW_PL_NAME_CHK==1)?strreplaceLvl($REVIEW_PL_NAME.$REVIEW_LEVEL_NAME):"".str_repeat(" ", 100);	
			}
           
        }
        /*Release 5.1.0.5 End*/
	$pdf->Cell(100, 5, ("ตำแหน่ง $REVIEW_PL_NAME "), "R", 1, 'C', 0);

	$pdf->Cell(100, 5, "", "LB", 0, 'C', 0);
	//echo "KF_SCORE_STATUS ==".$KF_SCORE_STATUS.  "&&". "SCORE_STATUS_DATE ==".$SCORE_STATUS_DATE ."&&". "CH_SCORE_DAY ==".$CH_SCORE_DAY ;
        if($KF_SCORE_STATUS == "1" && $SCORE_STATUS_DATE && $CH_SCORE_DAY=="N" ){//&&  $CH_SCORE_DAY == 'N'
        $pdf->Cell(100, 5, ("วันที่ ".$SCORE_DAY ." เดือน ".$MONTH_SCORE ." พ.ศ. ".$SCORE_YEAR), "BR", 1, 'C', 0);
        }else{
        $pdf->Cell(100, 5, ("วันที่ ". str_repeat(".", 13) ." เดือน ". str_repeat(".", 30) ." พ.ศ. ". str_repeat(".", 20)), "BR", 1, 'C', 0);
        }
	// ======================= PAGE 4 =====================//	
	$pdf->AddPage();
	$pdf->SetFont($font,'',14);

	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("99"),hexdec("CC"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "ส่วนที่ ๕: ความเห็นของผู้บังคับบัญชาเหนือขึ้นไป", 1, 1, 'L', 1);

	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(200, 7, "ผู้บังคับบัญชาเหนือขึ้นไป:", 1, 1, 'L', 1);	
	
	if(trim($AGREE_REVIEW1)) $pdf->Image("../images/checkbox_check.jpg",($pdf->x +1), ($pdf->y + 1), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x + 1), ($pdf->y + 1), 4, 4,"jpg");
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->Cell(190, 7, "เห็นด้วยกับผลการประเมิน", 0, 1, 'L', 0);

	$pdf->SetFont($font,'',12);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->MultiCell(190, 5, convert2thaidigit($AGREE_REVIEW1), "", "L");
	$pdf->x = $save_x;		$pdf->y = $save_y;
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	if(trim($DIFF_REVIEW1)) $pdf->Image("../images/checkbox_check.jpg",($pdf->x +1), ($pdf->y + 1), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x +1), ($pdf->y + 1), 4, 4,"jpg");

	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->Cell(190, 7, "มีความเห็นแตกต่าง ดังนี้", 0, 1, 'L', 0);
	$pdf->SetFont($font,'',12);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->MultiCell(190, 5, convert2thaidigit($DIFF_REVIEW1), "", "L");
	$pdf->x = $save_x;		$pdf->y = $save_y;
	
	$PIC_SIGN="";
	if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์
		if($AGREE_REVIEW1 || $DIFF_REVIEW1){
			$PIC_SIGN = getPIC_SIGN($PER_ID_REVIEW1,$PER_CARDNO_REVIEW1);
		}	
		if($PIC_SIGN){
			$pdf->Cell(120, 15, "", "L", 0, 'C', 0);
			$pdf->Image($PIC_SIGN,($pdf->x+1), ($pdf->y+1), 60, 15,"jpg");	// Original size = wxh (60x15)
			$save_x = $pdf->x;		$save_y = $pdf->y;
			$pdf->x += 5;			$pdf->y -= 15;
			$pdf->Cell(120, 15, "", "R", 1, 'C', 0); 
		}else{
			$pdf->SetFont($font,'',12);
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
			$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
			$pdf->Cell(100, 5, ("ลงชื่อ $REVIEW_SIGN_PN_NAME1". str_repeat(".", 100)), "R", 1, 'C', 0);
		}
	}else{
		$pdf->SetFont($font,'',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
		$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
		$pdf->Cell(100, 5, ("ลงชื่อ $REVIEW_SIGN_PN_NAME1". str_repeat(".", 100)), "R", 1, 'C', 0);
	}

	$REVIEW_SIGN_NAME1 = trim($REVIEW_SIGN_NAME1)?$REVIEW_SIGN_NAME1:"".str_repeat(" ", 100)."";		//ถ้าไม่มีข้อมูล แสดงช่องว่าง
	if($PIC_SIGN){
		$pdf->Cell(100, 20, "", "L", 0, 'C', 0);
		$pdf->Cell(100, 20, "", "R", 1, 'C', 0); 
	}
	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
//	$pdf->Cell(100, 5, ("( ". str_repeat(".", 90) ." )"), "R", 1, 'C', 0);
	$pdf->Cell(100, 5, ("( $REVIEW_SIGN_NAME1 )"), "R", 1, 'C', 0);

	$REVIEW_PL_NAME1_CHK = trim($REVIEW_PL_NAME1)?1:0;
	$REVIEW_PL_NAME1 = trim($REVIEW_PL_NAME1)?$REVIEW_PL_NAME1:"".str_repeat(" ", 100)."";		//ถ้าไม่มีข้อมูล แสดงช่องว่าง
	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
//	$pdf->Cell(100, 5, ("ตำแหน่ง ". str_repeat(".", 80)), "R", 1, 'C', 0);
        /*Release 5.1.0.5 Begin*/
        if(!empty($REVIEW_PM_NAME1)){
            	$REVIEW_PL_NAME1 = $REVIEW_PM_NAME1;
        }else{	
				$REVIEW_PL_NAME1 = ($REVIEW_PL_NAME1_CHK==1)?strreplaceLvl($REVIEW_PL_NAME1.$REVIEW_LEVEL_NAME1):"".str_repeat(" ", 100);	
        }
        /*Release 5.1.0.5 End*/
	$pdf->Cell(100, 5, ("ตำแหน่ง $REVIEW_PL_NAME1"), "R", 1, 'C', 0);

	$pdf->Cell(100, 5, "", "LB", 0, 'C', 0);
	//echo $AGREE_REVIEW1 .' && '. $DATE_REVIEW1.' && '.$CH_SCORE_DAY;
	if(($AGREE_REVIEW1 || $DIFF_REVIEW1)&& $DATE_REVIEW1 && $CH_SCORE_DAY=="N" ){//&& $CH_SCORE_DAY=="N"
		$pdf->Cell(100, 5, ("วันที่ ".$DATE_REVIEW1_DAY ." เดือน ". $review1 = get_month($DATE_REVIEW1_MONTH) ." พ.ศ. ". $DATE_REVIEW1_YEAR), "BR", 1, 'C', 0);
	}else{
		$pdf->Cell(100, 5, ("วันที่ ". str_repeat(".", 13) ." เดือน ". str_repeat(".", 30) ." พ.ศ. ". str_repeat(".", 20)), "BR", 1, 'C', 0);
	}
	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 3, "", 0, 1, 'C', 0);
	$pdf->Cell(200, 7, "ผู้บังคับบัญชาเหนือขึ้นไปอีกชั้นหนึ่ง (ถ้ามี):", 1, 1, 'L', 1);
	if(trim($AGREE_REVIEW2)) $pdf->Image("../images/checkbox_check.jpg",($pdf->x + 1), ($pdf->y + 1), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x + 1), ($pdf->y + 1), 4, 4,"jpg");
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->Cell(190, 7, "เห็นด้วยกับผลการประเมิน", 0, 1, 'L', 0);
	$pdf->SetFont($font,'',12);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->MultiCell(190, 5, convert2thaidigit($AGREE_REVIEW2), "", "L");
	$pdf->x = $save_x;		$pdf->y = $save_y;
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	if(trim($DIFF_REVIEW2)) $pdf->Image("../images/checkbox_check.jpg",($pdf->x + 1), ($pdf->y + 1), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x + 1), ($pdf->y + 1), 4, 4,"jpg");
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->Cell(190, 7, "มีความเห็นแตกต่าง ดังนี้", 0, 1, 'L', 0);
	$pdf->SetFont($font,'',12);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell(200, 15, "", "LR", 1, 'L', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 15;
	$pdf->MultiCell(190, 5, convert2thaidigit($DIFF_REVIEW2), "", "L");
	$pdf->x = $save_x;		$pdf->y = $save_y;
		$PIC_SIGN="";
	if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์	
		if($AGREE_REVIEW2 || $DIFF_REVIEW2){
			$PIC_SIGN = getPIC_SIGN($PER_ID_REVIEW2,$PER_CARDNO_REVIEW2);
		}
		if($PIC_SIGN){
			$pdf->Cell(120, 15, "", "L", 0, 'C', 0);
			$pdf->Image($PIC_SIGN,($pdf->x+1), ($pdf->y+1), 60, 15,"jpg");	// Original size = wxh (60x15)
			$save_x = $pdf->x;		$save_y = $pdf->y;
			$pdf->x += 5;			$pdf->y -= 15;
			$pdf->Cell(120, 15, "", "R", 1, 'C', 0); 
		}else{
			$pdf->SetFont($font,'',12);
			$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
			$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
			$pdf->Cell(100, 5, ("ลงชื่อ $REVIEW_SIGN_PN_NAME2". str_repeat(".", 100)), "R", 1, 'C', 0);
		}
	}else{
		$pdf->SetFont($font,'',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
		$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
		$pdf->Cell(100, 5, ("ลงชื่อ $REVIEW_SIGN_PN_NAME2". str_repeat(".", 100)), "R", 1, 'C', 0);
	}

	$REVIEW_SIGN_NAME2 = trim($REVIEW_SIGN_NAME2)?$REVIEW_SIGN_NAME2:"".str_repeat(" ", 100)."";		//ถ้าไม่มีข้อมูล แสดงช่องว่าง
	if($PIC_SIGN){
		$pdf->Cell(100, 20, "", "L", 0, 'C', 0);
		$pdf->Cell(100, 20, "", "R", 1, 'C', 0); 
	}
	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
//	$pdf->Cell(100, 5, ("( ". str_repeat(".", 90) ." )"), "R", 1, 'C', 0);
	$pdf->Cell(100, 5, ("( $REVIEW_SIGN_NAME2 )"), "R", 1, 'C', 0);
	$REVIEW_PL_NAME2_CHK = trim($REVIEW_PL_NAME2)?1:0;
	$REVIEW_PL_NAME2 = trim($REVIEW_PL_NAME2)?$REVIEW_PL_NAME2:"".str_repeat(" ", 100)."";		//ถ้าไม่มีข้อมูล แสดงช่องว่าง
	$pdf->Cell(100, 5, "", "L", 0, 'C', 0);
//	$pdf->Cell(100, 5, ("ตำแหน่ง ". str_repeat(".", 80)), "R", 1, 'C', 0);
		/*Release 5.1.0.5 Begin*/
        if(!empty($REVIEW_PM_NAME2)){
            $REVIEW_PL_NAME2 = $REVIEW_PM_NAME2;
        }else{	
			$REVIEW_PL_NAME2 = ($REVIEW_PL_NAME2_CHK==1)? strreplaceLvl($REVIEW_PL_NAME2.$REVIEW_LEVEL_NAME2):"".str_repeat(" ", 100);	
		}
        /*Release 5.1.0.5 End*/
	$pdf->Cell(100, 5, ("ตำแหน่ง $REVIEW_PL_NAME2"), "R", 1, 'C', 0);

	$pdf->Cell(100, 5, "", "LB", 0, 'C', 0);
	//$pdf->Cell(100, 5, ("วันที่ ". str_repeat(".", 13) ." เดือน ". str_repeat(".", 30) ." พ.ศ. ". str_repeat(".", 20)), "BR", 1, 'C', 0);
	if(($AGREE_REVIEW2 || $DIFF_REVIEW2)&& $DATE_REVIEW2 && $CH_SCORE_DAY=="N"){
		
		$pdf->Cell(100, 5, ("วันที่ ".$DATE_REVIEW2_DAY ." เดือน ". $review2 = get_month($DATE_REVIEW2_MONTH) ." พ.ศ. ". $DATE_REVIEW2_YEAR), "BR", 1, 'C', 0);
	}else{
		$pdf->Cell(100, 5, ("วันที่ ". str_repeat(".", 13) ." เดือน ". str_repeat(".", 30) ." พ.ศ. ". str_repeat(".", 20)), "BR", 1, 'C', 0);
	}
	
	// ======================= PAGE 5 =====================//	
	$pdf->AddPage();
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->SetFont($font,'b',14);

	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("99"),hexdec("CC"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(60, 7, "แบบสรุปการประเมินสมรรถนะ", 0, 0, 'L', 0);

	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(60, 7, "", 0, 0, 'L', 0);		//space
	$pdf->Cell(40, 7, "รอบการประเมิน", 0, 0, 'L', 0);
	$pdf->Cell(22, 7, convert2thaidigit("รอบที่ ๑"), 0, 0, 'L', 0);
	if($KF_CYCLE==1) $pdf->Image("../images/checkbox_check.jpg", ($pdf->x-27), ($pdf->y+1), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x-27), ($pdf->y+1), 4, 4,"jpg");

	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(15, 7, convert2thaidigit("รอบที่ ๒"), 0, 0, 'L', 0);
	if($KF_CYCLE==2) $pdf->Image("../images/checkbox_check.jpg", ($pdf->x-20), ($pdf->y+1), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x-20), ($pdf->y+1), 4, 4,"jpg");
 
    if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์	
        if($ACCEPT_FLAG == 1){$PIC_PER_ID = getPIC_SIGN($PER_ID,$PER_CARDNO);}
    }
    if($PIC_PER_ID || $PIC_REVIEW){
      $x_space = 10;
        
    }else{
      $x_space = 7;
    }
    //echo $x_spack;

	$pdf->SetFont($font,'',14);
	$pdf->Cell(200, $x_space, "", 0, 1, 'C', 0);
	$pdf->Cell(100, 10, "ชื่อผู้รับการประเมิน: $PER_NAME",0, 0, 'L', 0);  
    if($PIC_PER_ID){
        $pdf->Image($PIC_PER_ID,($pdf->x+10), ($pdf->y-2), 15, 8,"jpg");	// Original size = wxh (60x15)
        $pdf->Cell(100, 10, ("ลงนาม ". str_repeat(".", 100)), 0, 0, 'L', 0);
    }else{
        $pdf->Cell(100, 10, ("ลงนาม ". str_repeat(".", 100)), 0, 0, 'L', 0);
    }
	
	$pdf->SetFont($font,'',14);
	$pdf->Cell(200, $x_space, "", 0, 1, 'C', 0);
	$pdf->Cell(100, 10, "ชื่อผู้บังคับบัญชา/ผู้ประเมิน: ".trim($REVIEW_PER_NAME), 0, 0, 'L',0);
	if($SESS_E_SIGN[1]==1){// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์	
		if($KF_SCORE_STATUS == "1" && $PER_ID_REVIEW){
			$PIC_REVIEW = getPIC_SIGN($PER_ID_REVIEW,$PER_CARDNO_REVIEW_N);
		}
		
		if($PIC_REVIEW){
			$pdf->Image($PIC_REVIEW,($pdf->x+10), ($pdf->y-2), 15, 8,"jpg");	// Original size = wxh (60x15)
			$pdf->Cell(100, 10, "ลงนาม ". str_repeat(".", 100), 0, 0, 'L',0);
		}else{ 
			$pdf->Cell(100, 10, "ลงนาม ". str_repeat(".", 100), 0, 0, 'L',0);
		}	
	}else{
			$pdf->Cell(100, 10, "ลงนาม ". str_repeat(".", 100), 0, 0, 'L',0);
	}	

	$pdf->Cell(200, 10, "", 0, 1, 'C', 0);
	print_header(4);
	
	$data_count = 0;
	foreach($ARR_COMPETENCE as $KC_ID => $CP_NAME){
		$data_count++;
		$border = "";
		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$pdf->MultiCell($heading_width4[0], 7, convert2thaidigit($CP_NAME), $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width4[0];
		$pdf->y = $start_y;
		$pdf->Cell($heading_width4[1], 7, convert2thaidigit($ARR_COMPETENCE_TARGET[$KC_ID]), $border, 0, 'C', 0);
		$pdf->SetFont($font,'b','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
		$pdf->Cell($heading_width4[2], 7, showscore(convert2thaidigit(number_format($ARR_COMPETENCE_EVALUATE[$KC_ID], 2))), $border, 0, 'C', 0);
		if ($ARR_COMPETENCE_WEIGHT[$KC_ID])
			$pdf->Cell($heading_width4[3], 7, convert2thaidigit($ARR_COMPETENCE_WEIGHT[$KC_ID] . "%"), $border, 0, 'C', 0);
		else
			$pdf->Cell($heading_width4[3], 7, "", $border, 0, 'C', 0);
		$pdf->Cell($heading_width4[4], 7,showscore(convert2thaidigit(number_format($PC_SCORE[$KC_ID], $number_formate))), $border, 0, 'C', 0);
		//$pdf->Cell($heading_width4[5], 7, "", $border, 0, 'C', 0);/*เดิม*/
                //$pdf->Cell($heading_width4[5], 7, $ARR_KC_REMARK[$KC_ID], $border, 0, 'L', 0);/*ปรับเปลี่ยน http://dpis.ocsc.go.th/Service/node/1148*/
                $pdf->MultiCell($heading_width4[5], 7, $ARR_KC_REMARK[$KC_ID], $border, "L");/*ปรับเปลี่ยน http://dpis.ocsc.go.th/Service/node/1148*/
                if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width4[5]+164;
		$pdf->y = $start_y;
		//================= Draw Border Line ====================
		$line_start_y = $start_y;
                $line_start_x = $start_x;
		$line_end_y = $max_y;
                $line_end_x = $start_x;
		$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
		for($i=0; $i<=5; $i++){
			$line_start_y = $start_y;		$line_start_x += $heading_width4[$i];
			$line_end_y = $max_y;		$line_end_x += $heading_width4[$i];
			
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
		} // end for
		//====================================================

		if(($pdf->h - $max_y - 10) < 20){ 
			$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
			if($data_count < $COUNT_COMPETENCE){
				$pdf->AddPage();
				print_header(4);
				$max_y = $pdf->y;
			} // end if
		}else{
			$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
		} // end if

		$pdf->x = $start_x;			$pdf->y = $max_y;
	} // end foreach
	
	/*if ($COMPETENCY_SCALE==2 || $COMPETENCY_SCALE==5) {	
		 $TOTAL_PC_SCORE= ($YYY_PC_SCORE * ($XXX_WEIGHT/100)) ;
	}*/
        $TOTAL_PC_SCORE_SUM = ($TOTAL_PC_SCORE * 20);
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell($heading_width4[0]+$heading_width4[1], 7, "คะแนนรวม ", $border, 0, 'R', 0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
///	$pdf->Cell(($heading_width4[2] +$heading_width4[3]+$heading_width4[4]+$heading_width4[5]), 7, convert2thaidigit(), 1, 1, 'C', 0);
	$pdf->Cell(($heading_width4[2]), 7,showscore(convert2thaidigit(number_format(array_sum($ARR_COMPETENCE_EVALUATE), 2))), 1, 0, 'C', 0);     //เดิม $ARR_COMPETENCE_EVALUATE $ARR_COMPETENCE_EVALUATE; 
	if (array_sum($ARR_COMPETENCE_WEIGHT))
		$pdf->Cell(($heading_width4[3]), 7,convert2thaidigit(array_sum($ARR_COMPETENCE_WEIGHT) . "%"), 1, 0, 'C', 0);
	else
		$pdf->Cell(($heading_width4[3]), 7,"", 1, 0, 'C', 0);
		$pdf->Cell(($heading_width4[4]), 7,showscore(convert2thaidigit(number_format($TOTAL_PC_SCORE, $number_formate))),1, 0, 'C', 0);
		$pdf->Cell(($heading_width4[5]), 7,"",1, 1, 'C', 0);

	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell($heading_width4[0]+$heading_width4[1], 7, "คะแนนประเมิน ", $border, 0, 'R', 0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(($heading_width4[2] +$heading_width4[3]+$heading_width4[4]+$heading_width4[5]), 7,showscore(convert2thaidigit(number_format($TOTAL_PC_SCORE_SUM, 2))), 1, 1, 'C', 0); //convert2thaidigit(number_format(($TOTAL_KPI_EVALUATE / $TOTAL_KPI_WEIGHT), 2))
		
 /**********************
	//ผลสำเร็จของงานจริง
	$data_count = 0;
	foreach($ARR_KPI as $PG_ID => $KPI_NAME){
		$data_count++;
		$border = "";
		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
		$max_y = $ARR_KPI_MAXY[$PG_ID];

		$pdf->SetFont($font,'',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$pdf->MultiCell($heading_width4[0], 7, convert2thaidigit($ARR_KPI_RESULT[$PG_ID]), $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width4[0];
		$pdf->y = $start_y;
		$pdf->Cell($heading_width4[1], 7, convert2thaidigit($ARR_KPI_WEIGHT[$PG_ID]), $border, 0, 'C', 0);
		for($i=1; $i<5; $i++){ 
			if($i == $ARR_KPI_EVALUATE[$PG_ID]){
				$pdf->SetFont($font,'b','',12);
				$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
				$pdf->Cell($heading_width4[$i+1], 7, $i."+".convert2thaidigit(${"ARR_KPI_TARGET".$i}[$PG_ID]), $border, 0, 'C', 0);
			}else{
				$pdf->SetFont($font,'',12);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
//				$pdf->Cell($heading_width2[$i+1], 7, convert2thaidigit(${"ARR_KPI_TARGET".$i}[$PG_ID]), $border, 0, 'C', 0);
				$pdf->Cell($heading_width4[$i+1], 7, "$i", $border, 0, 'C', 0);
			} // end if			
		} // end for

		//================= Draw Border Line ====================
		$line_start_y = $start_y;		$line_start_x = $start_x;
		$line_end_y = $max_y;		$line_end_x = $start_x;
		$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
		for($i=0; $i<=5; $i++){
			$line_start_y = $start_y;		$line_start_x += $heading_width4[$i];
			$line_end_y = $max_y;		$line_end_x += $heading_width4[$i];
			
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
		} // end for
		//====================================================

		if(($pdf->h - $max_y - 10) < 20){ 
			$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
			if($data_count < $COUNT_KPI){
				$pdf->AddPage();
				print_header(4);
				$max_y = $pdf->y;
			} // end if
		}else{
			$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
		} // end if

		$pdf->x = $start_x;			$pdf->y = $max_y;
	} // end foreach

	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell($heading_width4[0], 7, "คะแนนรวม ", $border, 0, 'R', 0);
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell($heading_width4[1], 7, convert2thaidigit($TOTAL_KPI_WEIGHT), 1, 0, 'R', 0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(($heading_width4[2] +$heading_width4[3]+$heading_width4[4]+$heading_width4[5]), 7, convert2thaidigit($TOTAL_KPI_EVALUATE), 1, 1, 'C', 0);

	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell($heading_width4[0], 7, "คะแนนประเมิน ", $border, 0, 'R', 0);
	$pdf->Cell($heading_width4[1], 7, "", $border, 0, 'R', 0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(($heading_width4[2] +$heading_width4[3]+$heading_width4[4]+$heading_width4[5]), 7, convert2thaidigit(number_format(($TOTAL_KPI_EVALUATE / $TOTAL_KPI_WEIGHT), 2)), 1, 1, 'C', 0);
**********************/

	// ======================= PAGE 6 =====================//	
	$pdf->AddPage();

	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->SetFont($font,'b',16);
	$pdf->Cell(60, 7, "แบบสรุปการประเมินผลสัมฤทธิ์ของงาน", 0, 0, 'L', 0);
	
	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(60, 7, "", 0, 0, 'L', 0);		//space
	$pdf->Cell(40, 7, "รอบการประเมิน", 0, 0, 'L', 0);
//	$pdf->Cell(10, 7, "", 0, 0, 'L', 0);		//space
	$pdf->Cell(22, 7, convert2thaidigit("รอบที่ ๑"), 0, 0, 'L', 0);
	if($KF_CYCLE==1) $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 27), ($pdf->y +1 ), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 27), ($pdf->y + 1), 4, 4,"jpg");
	
	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(15, 7, convert2thaidigit("รอบที่ ๒"), 0, 0, 'L', 0);
	if($KF_CYCLE==2) $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 20), ($pdf->y+1), 4, 4,"jpg");
	else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y+1), 4, 4,"jpg");

     $PIC_PER_ID="";
     $PIC_REVIEW="";
		$PIC_SIGN="";
    if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์	
        if($ACCEPT_FLAG == 1){$PIC_PER_ID = getPIC_SIGN($PER_ID,$PER_CARDNO);}
    }
    if($PIC_PER_ID || $PIC_REVIEW){
      $x_space = 10;
    }else{
      $x_space = 7;
    }
 
	
	$pdf->SetFont($font,'',14);
	$pdf->Cell(200, $x_space, "", 0, 1, 'C', 0);
	$pdf->Cell(100, 10, "ชื่อผู้รับการประเมิน: ".trim($PER_NAME),0, 0, 'L', 0);

	 if($PIC_PER_ID ){
        $pdf->Image($PIC_PER_ID,($pdf->x+10), ($pdf->y-2), 15, 8,"jpg");	// Original size = wxh (60x15)
         $pdf->Cell(100, 10, "ลงนาม ". str_repeat(".", 100), 0, 0, 'L',0);
    }else{
        $pdf->Cell(100, 7, ("ลงนาม ". str_repeat(".", 100)), 0, 0, 'L', 0);
    }
	
	$pdf->SetFont($font,'',14);
	$pdf->Cell(200, $x_space, "", 0, 1, 'C', 0);
	$pdf->Cell(100, 10, "ชื่อผู้บังคับบัญชา/ผู้ประเมิน: ".trim($REVIEW_PER_NAME), 0, 0, 'L',0);

	if($SESS_E_SIGN[1]==1){// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์	
		if($KF_SCORE_STATUS == "1" && $PER_ID_REVIEW){
			$PIC_REVIEW = getPIC_SIGN($PER_ID_REVIEW,$PER_CARDNO_REVIEW_N);
		}
		
		if($PIC_REVIEW){
			$pdf->Image($PIC_REVIEW,($pdf->x+10), ($pdf->y-2), 15, 8,"jpg");	// Original size = wxh (60x15)
			$pdf->Cell(100, 10, "ลงนาม ". str_repeat(".", 100), 0, 0, 'L',0);
		}else{ 
			$pdf->Cell(100, 10, "ลงนาม ". str_repeat(".", 100), 0, 0, 'L',0);
		}	
	}else{
			$pdf->Cell(100, 10, "ลงนาม ". str_repeat(".", 100), 0, 0, 'L',0);
	}	
	$pdf->Cell(200, 10, "", 0, 1, 'C', 0);
	print_header(5);
	
	$data_count = 0;
	foreach($ARR_KPI as $PG_ID => $KPI_NAME){
		$data_count++;
		$border = "";
		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

//		echo "ARR_KPI_EVALUATE[$PG_ID]=".$ARR_KPI_EVALUATE[$PG_ID]."<br>";
//		echo "0..$data_count >> pdf->h(".$pdf->h.") - y_multi(".$y_multi.") - max_y(".$max_y.") >> COUNT_KPI=$COUNT_KPI, ii=$ii<br>";
		$y_multi =$max_y + $pdf->h_MultiCellThaiCut($heading_width5[0], 7, convert2thaidigit($KPI_NAME), $border, "L");
		//echo "1..$data_count >> pdf->h(".$pdf->h.") - y_multi(".$y_multi.") - max_y(".$max_y.") >> COUNT_KPI=$COUNT_KPI, ii=$ii<br>";
            //    echo ($pdf->h - $y_multi - 10)."<br>"; 
		if(($pdf->h - $y_multi - 10) < 25){ 
			$pdf->Line($start_x, $max_y, $line_end_x, $max_y);
			if($data_count <= $COUNT_KPI){
//				echo "AddPage<br>";
				$pdf->AddPage();
				print_header(5);
				$max_y = $pdf->y;
			} // end if
			$pdf->x = $start_x;			$pdf->y = $max_y;
			$start_x = $pdf->x;			$start_y = $pdf->y;
            
		} else if(($pdf->h - $y_multi - 10) < 40){
            $pdf->Line($start_x, $max_y, $line_end_x, $max_y);
			if($data_count <= $COUNT_KPI){
//				echo "AddPage<br>";
				$pdf->AddPage();
				print_header(5);
				$max_y = $pdf->y;
			} // end if
			$pdf->x = $start_x;			$pdf->y = $max_y;
			$start_x = $pdf->x;			$start_y = $pdf->y;
        
        
        }else{
//			echo "start_x=$start_x, max_y=$max_y, line_end_x=$line_end_x, max_y=$max_y<br>";
			$pdf->Line($start_x, $max_y, $line_end_x, $max_y);
		}

		$pdf->SetFont($font,'',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		//$pdf->MultiCell($heading_width5[0], 7, convert2thaidigit($ARR_KPI_PFR[$PG_ID]), $border, "L");
		$pdf->MultiCellThaiCut($heading_width5[0], 7, convert2thaidigit($data_count).". ".convert2thaidigit($KPI_NAME), $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width5[0];
		$pdf->y = $start_y;
//		echo "$data_count).KPI_NAME=$KPI_NAME<br>";
		for($i=1; $i<=5; $i++) { 
			//$pdf->Cell($heading_width5[1], 7, convert2thaidigit(${"ARR_KPI_TARGET".$i}[$PG_ID]), $border, 0, 'C', 0); 
			$pdf->Cell($heading_width5[1], 7, convert2thaidigit($i), $border, 0, 'C', 0); 
//			$a=${"ARR_KPI_TARGET".$i}[$PG_ID]; echo "     $i.. $a<br>"; 
		}

//		for($i=1; $i<=5; $i++){ 
//			if($i == $ARR_KPI_EVALUATE[$PG_ID]){
					$SCORE[$PG_ID]=$ARR_KPI_EVALUATE[$PG_ID];
					$SUMSCORE[$PG_ID]=($ARR_KPI_EVALUATE[$PG_ID]*$ARR_KPI_WEIGHT[$PG_ID])/$TOTAL_KPI_WEIGHT;
					$TOTAL_KPI_RESULT+=$ARR_KPI_EVALUATE[$PG_ID];
//					echo "SCORE[$PG_ID]:".$SCORE[$PG_ID]."<br>";
//			}	
//		} // end for
		$pdf->SetFont($font,'b','',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
		$pdf->Cell($heading_width5[2], 7, convert2thaidigit($ARR_KPI_EVALUATE[$PG_ID]), $border, 0, 'C', 0);
		$pdf->Cell($heading_width5[3], 7, convert2thaidigit($ARR_KPI_WEIGHT[$PG_ID]), $border, 0, 'C', 0);
		$pdf->Cell($heading_width5[4], 7, convert2thaidigit($SUMSCORE[$PG_ID]), $border, 0, 'C', 0);

		$ARR_KPI_MAXY[$PG_ID] = $max_y;
//		echo "ARR_KPI_MAXY[$PG_ID]:".$ARR_KPI_MAXY[$PG_ID]."<br>";

		//================= Draw Border Line ====================
		$line_start_y = $start_y;		$line_start_x = $start_x;
		$line_end_y = $max_y;		$line_end_x = $start_x;
		$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
		for($i=0; $i<=8; $i++){
			if($i>=1 && $i <=5){
				$line_start_y = $start_y;		$line_start_x += $heading_width5[1];
				$line_end_y = $max_y;		$line_end_x += $heading_width5[1];
			}else if($i>5){
				$line_start_y = $start_y;		$line_start_x += $heading_width5[$i-4];
				$line_end_y = $max_y;		$line_end_x += $heading_width5[$i-4];
			}else{
				$line_start_y = $start_y;		$line_start_x += $heading_width5[$i];
				$line_end_y = $max_y;		$line_end_x += $heading_width5[$i];
			}
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
		} // end for
		//====================================================

//		echo "1..pdf->h(".$pdf->h.") - $max_y(".$pdf->y."), data_count($data_count) < COUNT_KPI($COUNT_KPI)<br>";
//		if(($pdf->h - $pdf->y - 10) < 20){ 
//				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
//				if($data_count < $COUNT_KPI){
//					echo "addpage<br>";
//					$pdf->AddPage();
//					print_header(5);
//					$max_y = $pdf->y;
//				} // end if
//		}else{
//				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
//		} // end if

		$pdf->x = $start_x;			$pdf->y = $max_y;

		//===ข้อ 1 - 5===//
		$pdf->SetFont($font,'b','',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
//		$pdf->Cell(204, 7, convert2thaidigit("1 = ".$ARR_KPI_TARGET1_DESC[$PG_ID]), 1, 1, 'L', 0);
		$pdf->MultiCellThaiCut(204, 7, convert2thaidigit("1 = ".$ARR_KPI_TARGET1_DESC[$PG_ID]), 1, "L");
	
//		echo "2..pdf->h(".$pdf->h.") - $max_y(".$pdf->y."), data_count($data_count) < COUNT_KPI($COUNT_KPI)<br>";
                //echo ($pdf->h - $pdf->y - 10)."<15 <br>";
		if(($pdf->h - $pdf->y - 10) < 20){ /*if(($pdf->h - $pdf->y - 10) < 20){*/
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < $COUNT_KPI){
//					echo "addpage<br>";
					$pdf->AddPage();
					print_header(5);
					$max_y = $pdf->y;
				} // end if
		}else{
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
		} // end if

		$pdf->SetFont($font,'b','',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
//		$pdf->Cell(204, 7, convert2thaidigit("2 = ".$ARR_KPI_TARGET2_DESC[$PG_ID]), 1, 1, 'L', 0);
		$pdf->MultiCellThaiCut(204, 7, convert2thaidigit("2 = ".$ARR_KPI_TARGET2_DESC[$PG_ID]), 1, "L");
	
//		echo "3..pdf->h(".$pdf->h.") - $max_y(".$pdf->y."), data_count($data_count) < COUNT_KPI($COUNT_KPI)<br>";
		if(($pdf->h - $pdf->y - 10) < 20){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < $COUNT_KPI){
//					echo "addpage<br>";
					$pdf->AddPage();
					print_header(5);
					$max_y = $pdf->y;
				} // end if
		}else{
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
		} // end if

		$pdf->SetFont($font,'b','',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
//		$pdf->Cell(204, 7, convert2thaidigit("3 = ".$ARR_KPI_TARGET3_DESC[$PG_ID]), 1, 1, 'L', 0);
		$pdf->MultiCellThaiCut(204, 7, convert2thaidigit("3 = ".$ARR_KPI_TARGET3_DESC[$PG_ID]), 1, "L");
	
//		echo "4..pdf->h(".$pdf->h.") - $max_y(".$pdf->y."), data_count($data_count) < COUNT_KPI($COUNT_KPI)<br>";
		if(($pdf->h - $pdf->y - 10) < 20){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < $COUNT_KPI){
//					echo "addpage<br>";
					$pdf->AddPage();
					print_header(5);
					$max_y = $pdf->y;
				} // end if
		}else{
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
		} // end if

		$pdf->SetFont($font,'b','',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
//		$pdf->Cell(204, 7, convert2thaidigit("4 = ".$ARR_KPI_TARGET4_DESC[$PG_ID]), 1, 1, 'L', 0);
		$pdf->MultiCellThaiCut(204, 7, convert2thaidigit("4 = ".$ARR_KPI_TARGET4_DESC[$PG_ID]), 1, "L");
	
//		echo "5..pdf->h(".$pdf->h.") - $max_y(".$pdf->y."), data_count($data_count) < COUNT_KPI($COUNT_KPI)<br>";
		if(($pdf->h - $pdf->y - 10) < 20){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < $COUNT_KPI){
//					echo "addpage<br>";
					$pdf->AddPage();
					print_header(5);
					$max_y = $pdf->y;
				} // end if
		}else{
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
		} // end if

		$pdf->SetFont($font,'b','',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
//		$pdf->Cell(204, 7, convert2thaidigit("5 = ".$ARR_KPI_TARGET5_DESC[$PG_ID]), 1, 1, 'L', 0);
		$pdf->MultiCellThaiCut(204, 7, convert2thaidigit("5 = ".$ARR_KPI_TARGET5_DESC[$PG_ID]), 1, "L");

		if(($pdf->h - $pdf->y - 10) < 20){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < $COUNT_KPI){
					$pdf->AddPage();
					print_header(5);
					$max_y = $pdf->y;
				} // end if
		}else{
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
		} // end if
/***
		if($data_count !=count($ARR_KPI)){
			$pdf->AddPage();
			print_header(5);
		}
***/
		//==============================================================//
	} // end foreach
//	echo "$data_count - ".count($ARR_KPI);

	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(($heading_width5[0]+($heading_width5[1]*5)), 7, "คะแนนรวม ", $border, 0, 'R', 0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	$pdf->Cell(($heading_width5[2]), 7,convert2thaidigit($TOTAL_KPI_RESULT), 1, 0, 'C', 0);
	$pdf->Cell(($heading_width5[3]), 7,convert2thaidigit($TOTAL_KPI_WEIGHT), 1, 0, 'C', 0);
	$pdf->Cell(($heading_width5[4]), 7,convert2thaidigit(number_format(($TOTAL_KPI_EVALUATE / $TOTAL_KPI_WEIGHT), 2)),1, 1, 'C', 0);

	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell($heading_width5[0]+($heading_width5[1]*5), 7, "คะแนนประเมิน ", $border, 0, 'R', 0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	/**$pdf->Cell(($heading_width5[2]), 7, "", 1, 0, 'C', 0);
	$pdf->Cell(($heading_width5[3]), 7, "",  1, 0, 'C', 0);
	$pdf->Cell(($heading_width5[4]), 7, "", 1, 1, 'C', 0);	**/
	$pdf->Cell(($heading_width5[2]+$heading_width5[3]+$heading_width5[4]), 7, convert2thaidigit($TOTAL_KPI_EVALUATE), 1, 1, 'C', 0);

/**********************
	// ======================= PAGE 7 =====================//	
	$pdf->AddPage();
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->SetFont($font,'b','',16);
	$pdf->Cell(200, 10, "บันทึกเพิ่มเติมประกอบแบบสรุปการประเมินสมรรถนะ", 0, 1, 'C', 1);
	$pdf->Cell(200, 7, "", 0, 1, 'C', 0);

	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -= 10;
	
	//__1
	$pdf->Cell(250, 7, "สมรรถนะ".str_repeat("_", 30), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 75;			$pdf->y -= 15;			//space
	$pdf->Cell(190, 7, str_repeat(".", 150), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 75;			$pdf->y -= 15;			//space
	$pdf->Cell(190, 7, str_repeat(".", 150), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 75;			$pdf->y -= 15;			//space
	$pdf->Cell(190, 7, str_repeat(".", 150), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -=10;
	
	//__2
	$pdf->Cell(250, 7, "สมรรถนะ".str_repeat("_", 30), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 75;			$pdf->y -= 15;			//space
	$pdf->Cell(190, 7, str_repeat(".", 150), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 75;			$pdf->y -= 15;			//space
	$pdf->Cell(190, 7, str_repeat(".", 150), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 75;			$pdf->y -= 15;			//space
	$pdf->Cell(190, 7, str_repeat(".", 150), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -=10;
	
	//__3	
	$pdf->Cell(250, 7, "สมรรถนะ".str_repeat("_", 30), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 75;			$pdf->y -= 15;			//space
	$pdf->Cell(190, 7, str_repeat(".", 150), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 75;			$pdf->y -= 15;			//space
	$pdf->Cell(190, 7, str_repeat(".", 150), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 75;			$pdf->y -= 15;			//space
	$pdf->Cell(190, 7, str_repeat(".", 150), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -=10;
	
	//__4	
	$pdf->Cell(250, 7, "สมรรถนะ".str_repeat("_", 30), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 75;			$pdf->y -= 15;			//space
	$pdf->Cell(190, 7, str_repeat(".", 150), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 75;			$pdf->y -= 15;			//space
	$pdf->Cell(190, 7, str_repeat(".", 150), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 75;			$pdf->y -= 15;			//space
	$pdf->Cell(190, 7, str_repeat(".", 150), 0, 1, 'L', 0);
	$pdf->Cell(200, 15, "", 0, 1, 'C', 0);
	$save_x = $pdf->x;		$save_y = $pdf->y;
	$pdf->x += 5;			$pdf->y -=10;
*****************************/

/*	if(($pdf->h - $pdf_y) < 60) $pdf->AddPage();
	else $pdf->Cell(200, 5, "", 0, 1, 'C', 0);  */
	
	// ======================= PAGE 7 =====================//	
	$pdf->AddPage();
	
	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("BB"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "ผลสำเร็จของงานจริง", 1, 1, 'L', 1);

	print_header(6);

	$line_end_x = $start_x+$heading_width6[0]+$heading_width6[1]+$heading_width6[2]+$heading_width6[2]+$heading_width6[2]+$heading_width6[2]+$heading_width6[2];
	
	$data_count = 0;
	foreach($ARR_KPI as $PG_ID => $KPI_NAME){
		$data_count++;
		$border = "";
		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

//		$max_y = $ARR_KPI_MAXY[$PG_ID];

		$pdf->SetFont($font,'b','',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
		$ii = $ARR_KPI_EVALUATE[$PG_ID];
//		echo "ARR_KPI_EVALUATE[$PG_ID]=".$ARR_KPI_EVALUATE[$PG_ID]."<br>";
//		echo "0..$data_count >> pdf->h(".$pdf->h.") - y_multi(".$y_multi.") - max_y(".$max_y.") >> COUNT_KPI=$COUNT_KPI, ii=$ii<br>";
		if ($KPI_SCORE_DECIMAL==1) 	
			$y_multi =$max_y + $pdf->h_MultiCellThaiCut($heading_width6[0] + ($heading_width6[2] * 2), 7, convert2thaidigit($ARR_KPI_RESULT[$PG_ID]), $border, "L");
		else
			$y_multi =$max_y + $pdf->h_MultiCellThaiCut($heading_width6[0], 7, convert2thaidigit($ARR_KPI_RESULT[$PG_ID]), $border, "L");
//		echo "1..$data_count >> pdf->h(".$pdf->h.") - y_multi(".$y_multi.") - max_y(".$max_y.") >> COUNT_KPI=$COUNT_KPI, ii=$ii<br>";
		if(($pdf->h - $y_multi - 10) < 20){ 
			$pdf->Line($start_x, $max_y, $line_end_x, $max_y);
			if($data_count <= $COUNT_KPI){
//				echo "AddPage<br>";
				$pdf->AddPage();
				print_header(6);
				$max_y = $pdf->y;
			} // end if
			$pdf->x = $start_x;			$pdf->y = $max_y;
			$start_x = $pdf->x;			$start_y = $pdf->y;
		} else {
//			echo "start_x=$start_x, max_y=$max_y, line_end_x=$line_end_x, max_y=$max_y<br>";
			$pdf->Line($start_x, $max_y, $line_end_x, $max_y);
		}

		$pdf->SetFont($font,'',12);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

//		echo "1..pdf->y(".$pdf->y.") > max_y(".$max_y.")<br>";
                /*เพิ่มเติม ความเห็นในส่วนของ "หมายเหตุผู้ประเมิน" (ส่วนที่ 2.1)*/
                $NoteAssessor = "|หมายเหตุผู้ประเมิน : -";
                if(!empty($ARR_KPI_PG_REMARK[$PG_ID])){
                    $NoteAssessor = "|หมายเหตุผู้ประเมิน : ".$ARR_KPI_PG_REMARK[$PG_ID];
                }
                
                /**/
		if ($KPI_SCORE_DECIMAL==1) 	
			$pdf->MultiCellThaiCut($heading_width6[0] + ($heading_width6[2] * 2), 7, convert2thaidigit($data_count).". ".$ARR_KPI[$PG_ID]."|ผลงานจริง : ".convert2thaidigit($ARR_KPI_RESULT[$PG_ID]).$NoteAssessor, $border, "L");
		else
			$pdf->MultiCellThaiCut($heading_width6[0], 7, convert2thaidigit($data_count).". ".$ARR_KPI[$PG_ID]."|ผลงานจริง : ".convert2thaidigit($ARR_KPI_RESULT[$PG_ID]).$NoteAssessor, $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		if ($KPI_SCORE_DECIMAL==1) 	
			$pdf->x = $start_x + $heading_width6[0] + ($heading_width6[2] * 2);
		else
			$pdf->x = $start_x + $heading_width6[0];
		$pdf->y = $start_y;
//		echo "2..pdf->y(".$pdf->y.") > max_y(".$max_y.")<br>";
		$pdf->Cell($heading_width6[1], 7, convert2thaidigit($ARR_KPI_WEIGHT[$PG_ID]), $border, 0, 'R', 0);
		if ($KPI_SCORE_DECIMAL==1) {	
			$pdf->Cell(($heading_width6[2] * 3), 7, convert2thaidigit($ARR_KPI_EVALUATE[$PG_ID]), $border, 0, 'C', 0);
		} else {
			for($i=1; $i<=5; $i++){ 
				if($i == $ARR_KPI_EVALUATE[$PG_ID]){
					$pdf->SetFont($font,'b','',12);
					$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
					$pdf->Cell($heading_width6[2], 7, convert2thaidigit(${"ARR_KPI_TARGET".$i}[$PG_ID]), $border, 0, 'C', 0);			//${"ARR_KPI_TARGET".$i}[$PG_ID]
				}else{
					$pdf->SetFont($font,'',12);
					$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
					$pdf->Cell($heading_width6[2], 7, "", $border, 0, 'C', 0);
				} // end if				
			} // end for
		} // end if				

		//================= Draw Border Line ====================
		$line_start_y = $start_y;		$line_start_x = $start_x;
		$line_end_y = $max_y;		$line_end_x = $start_x;
		$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
		if ($KPI_SCORE_DECIMAL==1) {	
			for($i=0; $i<=2; $i++){
				if($i == 0){
					$line_start_y = $start_y;		$line_start_x += $heading_width6[0] + ($heading_width6[2] * 2);
					$line_end_y = $max_y;		$line_end_x += $heading_width6[0] + ($heading_width6[2] * 2);
				}elseif($i == 1){
					$line_start_y = $start_y;		$line_start_x += $heading_width6[1];
					$line_end_y = $max_y;		$line_end_x += $heading_width6[1];
				}else{
					$line_start_y = $start_y;		$line_start_x += $heading_width6[2] * 3;
					$line_end_y = $max_y;		$line_end_x += $heading_width6[2] * 3;
				} // end if
			
//				echo "$line_start_x, $line_start_y, $line_end_x, $line_end_y<br>";
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
		} else {
			for($i=0; $i<=6; $i++){
				if($i < 2){
					$line_start_y = $start_y;		$line_start_x += $heading_width6[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width6[$i];
				}else{
					$line_start_y = $start_y;		$line_start_x += $heading_width6[2];
					$line_end_y = $max_y;		$line_end_x += $heading_width6[2];
				} // end if
			
//				echo "$line_start_x, $line_start_y, $line_end_x, $line_end_y<br>";
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
		} // end if				
		//====================================================
//		echo "2..$data_count >> pdf->h(".$pdf->h.") - y_multi(".$y_multi.") - max_y(".$max_y.") >> COUNT_KPI=$COUNT_KPI, ii=$ii<br>";

		$pdf->x = $start_x;			$pdf->y = $max_y;
	} // end foreach
	
	$line_start_y = $max_y;		$line_start_x = $start_x;
	$line_end_y = $max_y;		$line_end_x = $start_x+$heading_width6[0]+$heading_width6[1]+$heading_width6[2]+$heading_width6[2]+$heading_width6[2]+$heading_width6[2]+$heading_width6[2];
	$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
		
//	echo "1..$pdf->h - $pdf->y<br>";
	if(($pdf->h - $pdf->y) < 60) $pdf->AddPage();
	else $pdf->Cell(200, 5, "", 0, 1, 'C', 0);
	
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	if ($KPI_SCORE_DECIMAL==1) 
		$pdf->Cell($heading_width6[0] + ($heading_width6[2] * 2), 7, "คะแนนรวม ", $border, 0, 'R', 0);
	else
		$pdf->Cell($heading_width6[0], 7, "คะแนนรวม ", $border, 0, 'R', 0);
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	$pdf->Cell($heading_width6[1], 7, convert2thaidigit($TOTAL_KPI_WEIGHT), 1, 0, 'R', 0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	if ($KPI_SCORE_DECIMAL==1) 
		$pdf->Cell(($heading_width6[2] * 3), 7, convert2thaidigit($TOTAL_KPI_EVALUATE), 1, 1, 'C', 0);
	else
		$pdf->Cell(($heading_width6[2] * 5), 7, convert2thaidigit($TOTAL_KPI_EVALUATE), 1, 1, 'C', 0);

	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	if ($KPI_SCORE_DECIMAL==1) 
		$pdf->Cell($heading_width6[0] + ($heading_width6[2] * 2), 7, "คะแนนประเมิน ", $border, 0, 'R', 0);
	else
		$pdf->Cell($heading_width6[0], 7, "คะแนนประเมิน ", $border, 0, 'R', 0);
	$pdf->Cell($heading_width6[1], 7, "", $border, 0, 'R', 0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
	if ($KPI_SCORE_DECIMAL==1) 
		$pdf->Cell(($heading_width6[2] * 3), 7, convert2thaidigit(number_format(($TOTAL_KPI_EVALUATE / $TOTAL_KPI_WEIGHT), 2)), 1, 1, 'C', 0);
	else
		$pdf->Cell(($heading_width6[2] * 5), 7, convert2thaidigit(number_format(($TOTAL_KPI_EVALUATE / $TOTAL_KPI_WEIGHT), 2)), 1, 1, 'C', 0);

//	echo "2..$pdf->h - $pdf->y<br>";
	/*if(($pdf->h - $pdf->y) < 50) $pdf->AddPage();																					
	else $pdf->Cell(200, 5, "", 0, 1, 'C', 0);*/

//	$pdf->AddPage();			
	
	$pdf->SetFont($font,'',14);
	$pdf->SetDrawColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	$pdf->SetTextColor(hexdec("00"),hexdec("44"),hexdec("CC"));
	$pdf->Cell(200, 7, "ผลรวมของคะแนนประเมินของผลสำเร็จของงาน", 0, 1, 'L', 1);
	$pdf->Cell(200, 5, "", 0, 1, 'C', 0);

	$pdf->Cell(70, 7, "ผลรวมของคะแนนประเมินผลสำเร็จของงานทั้งหมด", 0, 0, 'R', 0);
	$pdf->Cell(5, 7, "", 0, 0, 'C', 0);
	$pdf->SetFont($font,'b','',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("33"));
//	$pdf->Cell(20, 7, convert2thaidigit(number_format(($TOTAL_KPI_EVALUATE / $COUNT_KPI), 2)), 1, 0, 'C', 0);
	$pdf->Cell(20, 7, convert2thaidigit(number_format(($TOTAL_KPI_EVALUATE / $TOTAL_KPI_WEIGHT), 2)), 1, 0, 'C', 0);
	$pdf->Cell(5, 7, "", 0, 1, 'C', 0);

	$pdf->close();
        $fname= "rpt_kpi_formN.pdf";
	$pdf->Output($fname,'I');
        
        

	ini_set("max_execution_time", 30);
        
?>