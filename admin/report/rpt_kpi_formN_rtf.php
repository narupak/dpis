<?php
//	header('Content-Type: text/html; charset=windows-874');
 
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require("../../RTF/rtf_class.php");

  	ini_set("max_execution_time", $max_execution_time); 
	
	$fname= "rpt_kpi_formN_rtf.rtf";

	if (!$font) $font = "AngsanaUPC";

	$RTF = new RTF("a4", 750, 500, 500, 500);

//	$RTF->set_default_font($font, 14);

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        function showscore($val){
            global $KF_SCORE_STATUS,$KPI_SCORE_CONFIRM,$SESS_USERGROUP ,$PER_ID_REVIEW,$SESS_PER_ID ,
                    $PER_ID_REVIEW0,$SESS_PER_ID,$PER_ID_REVIEW1,$SESS_PER_ID,$PER_ID_REVIEW2,$SESS_PER_ID;
            if ($KF_SCORE_STATUS==1 || $KPI_SCORE_CONFIRM!=1 || $SESS_USERGROUP == 1 || $PER_ID_REVIEW==$SESS_PER_ID || 
                   $PER_ID_REVIEW0==$SESS_PER_ID || $PER_ID_REVIEW1==$SESS_PER_ID || $PER_ID_REVIEW2==$SESS_PER_ID) {
                 return $val;
           }else{
               return '***';
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
        function strreplaceLvl($str){
            $findStr= array("ประเภททั่วไป ระดับ","ประเภทวิชาการ ระดับ","ประเภทอำนวยการ ระดับ","ประเภทบริหาร ระดับ");
            $str = str_replace($findStr, "", $str);
            return $str;
        }
	function getPIC_SIGN($PER_ID,$PER_CARDNO){
		global $db_dpis , $db_dpis2;
	
		$PIC_SIGN="";
		//หารูปที่เป็นลายเซ็น
		$cmd = "	select 		*
									  from 		PER_PERSONALPIC
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
	$db_dpis->send_cmd($cmd);
//echo $cmd;
	$data = $db_dpis->get_array();
	$KF_CYCLE = trim($data[KF_CYCLE]);
	$KF_START_DATE = show_date_format($data[KF_START_DATE], 1);
	$KF_END_DATE = show_date_format($data[KF_END_DATE], 1);
	$KF_YEAR = substr($KF_END_DATE, 6, 4);

	$PER_ID = $data[PER_ID];
    $ACCEPT_FLAG = $data[ACCEPT_FLAG];
	$PER_CARDNO = trim($data[PER_CARDNO]);		
	$PER_ID_REVIEW = $data[PER_ID_REVIEW];
	$PER_ID_REVIEW0 = $data[PER_ID_REVIEW0];
	$PER_ID_REVIEW1 = $data[PER_ID_REVIEW1];
	$PER_ID_REVIEW2 = $data[PER_ID_REVIEW2];
	$KF_SCORE_STATUS = $data[KF_SCORE_STATUS];
	$ORG_ID_KPI = $data[ORG_ID_KPI];
	$PER_SALARY = $data[PER_SALARY];
	$PL_NAME = $data[PL_NAME];
	$PM_NAME = $data[PM_NAME];
    $view_ORG_ID = $data[ORG_ID];
	$REVIEW_PN_NAME = $data[REVIEW_PN_NAME];
	$REVIEW_PER_NAME = $data[REVIEW_PER_NAME];
	$REVIEW_PL_NAME = $data[REVIEW_PL_NAME];
	$REVIEW_PM_NAME = $data[REVIEW_PM_NAME];
        $SCORE_STATUS_DATE = $data[SCORE_STATUS_DATE];
        $ACCEPT_DATE = $data[ACCEPT_DATE];
		$DATE_REVIEW1    = $data[DATE_REVIEW1];
		$DATE_REVIEW2    = $data[DATE_REVIEW2];
	if ($REVIEW_PM_NAME) $REVIEW_PL_NAME = $REVIEW_PM_NAME;
	$REVIEW_LEVEL_NO = $data[REVIEW_LEVEL_NO];
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
	$REVIEW_PL_NAME2 = $data[REVIEW_PL_NAME2];
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
/*//Release 5.2.1.25 ให้ดึงตำแหน่งจากแฟ้ม ถ้าไม่มีให้ว่าง
	if (!$PL_NAME || !$ORG_NAME) {
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
					$REVIEW_PL_NAME = trim($REVIEW_PL_NAME)?($REVIEW_PL_NAME . $REVIEW_POSITION_LEVEL . (($REVIEW_PT_NAME != "ทั่วไป" && $REVIEW_LEVEL_NO >= 6)?"$REVIEW_PT_NAME":"")):$REVIEW_LEVEL_NAME;
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
		}
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
		}
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
		}
	} // end if
	
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
 /*5.2.1.10*/			if($SCORE_STATUS_DATE){
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
                       $ACCEPT_MONTHS    = $ACCEPT_COVERT[1];
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
	$creat_date = $SCORE_DAY." ".$ACCEPT_MONTH = get_month($SCORE_MONT)." ".$SCORE_YEAR;
	
	/*5.2.1.10*/     
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
							a.KPI_TARGET_LEVEL1_DESC, a.KPI_TARGET_LEVEL2_DESC, a.KPI_TARGET_LEVEL3_DESC, a.KPI_TARGET_LEVEL4_DESC, a.KPI_TARGET_LEVEL5_DESC
                                                        ,a.PG_REMARK
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
	
	$cmd = " select 	a.KC_ID, a.CP_CODE, b.CP_NAME, b.CP_MODEL, a.KC_EVALUATE, a.KC_WEIGHT, a.PC_TARGET_LEVEL
			   from 		PER_KPI_COMPETENCE a, PER_COMPETENCE b
			   where 	a.KF_ID=$KF_ID and a.CP_CODE=b.CP_CODE(+) and b.DEPARTMENT_ID=$DEPARTMENT_ID 
			   order by 	a.CP_CODE ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$KC_ID = $data[KC_ID];
		$CP_CODE = $data[CP_CODE];
		$CP_NAME = $data[CP_NAME];
		$CP_MODEL = $data[CP_MODEL];
		if ($KF_SCORE_STATUS==1 || $KPI_SCORE_CONFIRM!=1 || $SESS_USERGROUP == 1 || $PER_ID_REVIEW==$SESS_PER_ID || 
			$PER_ID_REVIEW0==$SESS_PER_ID || $PER_ID_REVIEW1==$SESS_PER_ID || $PER_ID_REVIEW2==$SESS_PER_ID) {
			$KC_EVALUATE = $data[KC_EVALUATE];
		}
		$KC_WEIGHT = $data[KC_WEIGHT];
		$PC_TARGET_LEVEL = $data[PC_TARGET_LEVEL];
		
		$ARR_COMPETENCE[$KC_ID] = $CP_NAME.(($CP_MODEL==2)?" *":"");
		$ARR_COMPETENCE_TARGET[$KC_ID] = $PC_TARGET_LEVEL;
		$ARR_COMPETENCE_EVALUATE[$KC_ID] = $KC_EVALUATE;
		$ARR_COMPETENCE_WEIGHT[$KC_ID]=$KC_WEIGHT;
		//เพื่อแสดงคะแนนในส่วน แบบประเมินการสรุปสมรรถนะ                    COMPETENCY_SCALE=2  Rating / COMPETENCY_SCALE=5 Rating เท่า
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
		 	$PC_SCORE[$KC_ID] = number_format($PC_SCORE[$KC_ID], 2);		
			$TOTAL_PC_SCORE =  number_format($TOTAL_PC_SCORE, 2);	
		}
		//----------------------------------------------------
		
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
	$SHOW_SCORE_KPI = round(round(($SCORE_KPI / ($TOTAL_KPI_WEIGHT * 5)), 3), 2);
//	$SHOW_SCORE_COMPETENCE = round(round(($SCORE_COMPETENCE / ($COUNT_COMPETENCE * 3)), 3), 2);
//	$SHOW_SCORE_COMPETENCE = round(round(($SCORE_COMPETENCE / 5), 3), 2);
	if ($COMPETENCY_SCALE==1)
		$SHOW_SCORE_COMPETENCE = round(round(($SCORE_COMPETENCE / ($COUNT_COMPETENCE * 3)), 3), 2);
	elseif ($COMPETENCY_SCALE==2 || $COMPETENCY_SCALE==5 || $COMPETENCY_SCALE==6)
		$SHOW_SCORE_COMPETENCE = round(round(($SCORE_COMPETENCE / $FULL_SCORE), 3), 2);
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

	$heading_width1[0] = "38";
	$heading_width1[1] = "38";
	$heading_width1[2] = "5";
	
	$heading_width2[0] = "50";
	$heading_width2[1] = "15";
	$heading_width2[2] = "15";
	$heading_width2[3] = "20";
	
	$heading_width3[0] = "47";
	$heading_width3[1] = "27";
	$heading_width3[2] = "26";

	$heading_width4[0] = "41";
	$heading_width4[1] = "9";
	$heading_width4[2] = "12";
	$heading_width4[3] = "9";
	$heading_width4[4] = "9";
	$heading_width4[5] = "20";

	$heading_width5[0] = "52";
	$heading_width5[1] = "5";
	$heading_width5[2] = "7";
	$heading_width5[3] = "7";
	$heading_width5[4] = "9";
	
	$heading_width6[0] = "67";
	$heading_width6[1] = "8";
	$heading_width6[2] = "5";
	
	function print_header($header_select){
		global $RTF, $heading_width1, $heading_width2, $heading_width3, $heading_width4, $heading_width5, $heading_width6, $KPI_SCORE_DECIMAL;
		global $font;
		
		if($header_select == 2){
			$RTF->open_line();
			$RTF->set_font($font, 14);
			$RTF->color("0");	// 0='BLACK'
			$RTF->cell($RTF->bold(1)."องค์ประกอบการประเมิน".$RTF->bold(0), $heading_width2[0], "center", "25", "TRBL");	
			$RTF->cell($RTF->bold(1)."คะแนน (ก)".$RTF->bold(0), $heading_width2[1], "center", "25", "TRBL");
			$RTF->cell($RTF->bold(1)."น้ำหนัก (ข)".$RTF->bold(0), $heading_width2[2], "center", "25", "TRBL");
			$RTF->cell($RTF->bold(1)."รวมคะแนน (ก)x(ข)".$RTF->bold(1), $heading_width2[3], "center", "25", "TRBL");
			$RTF->close_line();
		}elseif($header_select == 3){
			$RTF->open_line();			
			$RTF->set_font($font, 14);
			$RTF->color("0");	// 0='BLACK'
			$RTF->cell($RTF->bold(1)."ความรู้/ ทักษะ/ สมรรถนะที่ต้องได้รับการพัฒนา".$RTF->bold(0), $heading_width3[0], "center", "25", "TRBL");	// 9='BLUE'
			$RTF->cell($RTF->bold(1)."วิธีการพัฒนา".$RTF->bold(0), $heading_width3[1], "center", "25", "TRBL");
			$RTF->cell($RTF->bold(1)."ช่วงเวลาที่ต้องการการพัฒนา".$RTF->bold(0), $heading_width3[2], "center", "25", "TRBL");
			$RTF->close_line();
		}elseif($header_select == 4){
			$RTF->open_line();			
			$RTF->set_font($font, 14);
			$RTF->color("0");	// 0='BLACK'
			$RTF->cell($RTF->bold(1)."สมรรถนะ".$RTF->bold(0), $heading_width4[0], "center", "25", "TRBL");
			$RTF->cell($RTF->bold(1)."ระดับที่".$RTF->bold(0), $heading_width4[1], "center", "25", "TRBL");
			$RTF->cell($RTF->bold(1)."ผลการประเมิน (ก)".$RTF->bold(0), $heading_width4[2], "center", "25", "TRBL");
			$RTF->cell($RTF->bold(1)."น้ำหนัก (ข)".$RTF->bold(1), $heading_width4[3], "center", "25", "TRBL");
			$RTF->cell($RTF->bold(1)."รวมคะแนน".$RTF->bold(1), $heading_width4[4], "center", "25", "TRBL");
			$RTF->cell($RTF->bold(1)."บันทึกโดยผู้ประเมิน (ถ้ามี)".$RTF->bold(1), $heading_width4[5], "center", "25", "TRBL");
			$RTF->close_line();
		}elseif($header_select == 5){	//แบบสรุปการประเมินผลสัมฤทธิ์ของงาน (PAGE 6)
			$RTF->open_line();			
			$RTF->set_font($font, 14);
			$RTF->color("0");	// 0='BLACK'
			$RTF->cell($RTF->bold(1)."ตัวชี้วัดผลงาน".$RTF->bold(0), $heading_width5[0], "center", "25", "TRL");
			$RTF->cell($RTF->bold(1)."คะแนนตามระดับค่าเป้าหมาย".$RTF->bold(0), ($heading_width5[1]*5), "center", "25", "TRL");
			$RTF->cell($RTF->bold(1)."คะแนน (ก)".$RTF->bold(0), $heading_width5[2], "center", "25", "TRL");
			$RTF->cell($RTF->bold(1)."น้ำหนัก (ข)".$RTF->bold(1), $heading_width5[3], "center", "25", "TRL");
			$RTF->cell($RTF->bold(1)."รวมคะแนน".$RTF->bold(1), $heading_width5[4], "center", "25", "TRL");
			$RTF->close_line();
			$RTF->open_line();
			$RTF->cell("", $heading_width5[0], "center", "25", "RBL");
			for($i=1; $i<=5; $i++){ 
				$RTF->cell($RTF->bold(1).convert2thaidigit($i).$RTF->bold(0), $heading_width5[1], "center", "25", "TRBL");
			} // end for
			$RTF->cell("", $heading_width5[2], "center", "25", "RBL");
			$RTF->cell("", $heading_width5[3], "center", "25", "RBL");
			$RTF->cell("", $heading_width5[4], "center", "25", "RBL");
			$RTF->close_line();
		}elseif($header_select == 6){
			$RTF->open_line();			
			$RTF->set_font($font, 14);
			$RTF->color("0");	// 0='BLACK'
			if ($KPI_SCORE_DECIMAL==1) {	
				$RTF->cell($RTF->bold(1)."ผลงานจริง".$RTF->bold(0), $heading_width6[0]+($heading_width6[2]*2), "center", "25", "TRL");
				$RTF->cell($RTF->bold(1)."น้ำหนัก".$RTF->bold(0), $heading_width6[1], "center", "25", "TRL");
				$RTF->cell($RTF->bold(1)."ผลการประเมิน".$RTF->bold(0), ($heading_width6[2]*3), "center", "25", "TRL");
			} else {
				$RTF->cell($RTF->bold(1)."ผลงานจริง".$RTF->bold(0), $heading_width6[0], "center", "25", "TRL");
				$RTF->cell($RTF->bold(1)."น้ำหนัก".$RTF->bold(0), $heading_width6[1], "center", "25", "TRL");
				$RTF->cell($RTF->bold(1)."ผลการประเมิน".$RTF->bold(0), ($heading_width6[2]*5), "center", "25", "TRL");
				$RTF->close_line();
				$RTF->open_line();			
				$RTF->cell("", $heading_width6[0], "center", "25", "RBL");
				$RTF->cell("", $heading_width6[1], "center", "25", "RBL");
				for($i=1; $i<=5; $i++){ 
					$RTF->cell($RTF->bold(1).convert2thaidigit($i).$RTF->bold(0), $heading_width6[2], "center", "25", "TRBL");
				} // end for
			}	// end if
			$RTF->close_line();
		}	// end if
	} // function		

	$RTF->set_table_font($font, 18);
	$RTF->color("0");	// 0=BLACK
	
	$RTF->open_line();			
	$RTF->cellImage("../images/logo_ocsc.jpg", 20, 100, "center", "", "");
	$RTF->close_line();
        
        

	if (trim($DEPARTMENT_NAME)) {
		$RTF->open_line();
		$RTF->set_font($font,25);
		$RTF->cell($RTF->color("2").$RTF->bold(1)."$DEPARTMENT_NAME".$RTF->bold(0), "100", "center", "0");
		$RTF->close_line();
	}
	
	if (trim($ORG_ENG_NAME)) {
		$RTF->open_line();
		$RTF->set_font($font,20);
		$RTF->cell($RTF->color("2").$RTF->bold(1)."$ORG_ENG_NAME".$RTF->bold(0), "100", "center", "0");
		$RTF->close_line();
	}

	$RTF->open_line();
	$RTF->set_font($font,20);
	$RTF->cell($RTF->color("2").$RTF->bold(1)."แบบสรุปการประเมินผลการปฏิบัติราชการ".$RTF->bold(0), "100", "center", "16");
	$RTF->close_line();

	$RTF->ln();

	$RTF->open_line();			
	$RTF->set_font($font,20);
	$RTF->cell($RTF->color("8")."ส่วนที่ ๑:  ข้อมูลของผู้รับการประเมิน", "100", "left", "2");
	$RTF->close_line();
	
	$RTF->open_line();			
	$RTF->set_font($font,18);
	$RTF->cell("", "5", "center", "0");
	$RTF->cell($RTF->color("2").$RTF->bold(1)."รอบการประเมิน".$RTF->bold(0), "17", "left", "0");
	$RTF->cell("", "7", "center", "0");
	if($KF_CYCLE==1) $RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "left", 0);
	else $RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "left", 0);
	$RTF->cell(convert2thaidigit("รอบที่ ๑"), "15", "left", "0");
	$RTF->cell(convert2thaidigit("๑ ตุลาคม "), "15", "left", "0");
	$RTF->cell($RTF->color("0").convert2thaidigit(($KF_CYCLE==1)?($KF_YEAR - 1):""), "9", "left", "0");
	$RTF->cell($RTF->color("2").convert2thaidigit("ถึง ๓๑ มีนาคม "), "16", "left", "0");
	$RTF->cell($RTF->color("0").convert2thaidigit(($KF_CYCLE==1)?$KF_YEAR:""), "10", "left", "0");
	$RTF->close_line();

	$RTF->open_line();			
	$RTF->set_font($font,18);
	$RTF->cell("", "5", "center", "0");
	$RTF->cell("", "17", "left", "0");
	$RTF->cell("", "7", "center", "0");
	if($KF_CYCLE==2) $RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "center", 0);
	else $RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "center", 0);
	$RTF->cell($RTF->color("2").convert2thaidigit("รอบที่ ๒"), "15", "left", "0");
	$RTF->cell(convert2thaidigit("๑ เมษายน "), "15", "left", "0");
	$RTF->cell($RTF->color("0").convert2thaidigit(($KF_CYCLE==2)?$KF_YEAR:""), "9", "left", "0");
	$RTF->cell($RTF->color("2").convert2thaidigit("ถึง ๓๐ กันยายน "), "16", "left", "0");
	$RTF->cell($RTF->color("0").convert2thaidigit(($KF_CYCLE==2)?$KF_YEAR:""), "10", "left", "0");
	$RTF->close_line();

	$RTF->ln();			

	$RTF->open_line();			
	$RTF->set_font($font,18);
	$RTF->cell("", "5", "center", "0");
	$RTF->cell($RTF->color("2").$RTF->bold(1)."ชื่อผู้รับการประเมิน  ".$RTF->bold(0), "20", "left", "0");
	$RTF->cell($RTF->color("0").$RTF->bold(1)."$PER_NAME  ".$RTF->bold(0), "65", "left", "0");
	$RTF->close_line();

	$RTF->open_line();			
	$RTF->set_font($font,18);
	$RTF->cell("", "5", "center", "0");
	$RTF->cell($RTF->color("2").$RTF->bold(1)."ตำแหน่ง  ".$RTF->bold(0), "20", "left", "0");
	$RTF->cell($RTF->color("0").$RTF->bold(1).convert2thaidigit("$PL_NAME  ").$RTF->bold(0), "65", "left", "0");
	$RTF->close_line();

	if ($PER_TYPE==1) {
		$RTF->open_line();
		$RTF->set_font($font,18);
		$RTF->cell("", "5", "center", "0");
		$RTF->cell($RTF->color("2").$RTF->bold(1)."ประเภทตำแหน่ง  ".$RTF->bold(0), "20", "left", "0");
		$RTF->set_font($font,16);
		$RTF->cell($RTF->color("1").$RTF->bold(1).convert2thaidigit("$POSITION_TYPE  ").$RTF->bold(0), "20", "left", "0");
		$RTF->set_font($font,18);
		$RTF->cell("", "7", "center", "0");
		$RTF->cell($RTF->color("2").$RTF->bold(1)."ระดับตำแหน่ง  ".$RTF->bold(0), "15", "left", "0");
		$RTF->set_font($font,16);
		$RTF->cell($RTF->color("0").$RTF->bold(1).convert2thaidigit("$POSITION_LEVEL  ").$RTF->bold(0), "20", "left", "0");
		$RTF->close_line();
	} elseif ($PER_TYPE==3) {
		$RTF->open_line();
		$RTF->set_font($font,18);
		$RTF->cell("", "20", "center", "0");
		$RTF->cell($RTF->color("2").$RTF->bold(1)."กลุ่มงาน  ".$RTF->bold(0), "20", "left", "0");
		$RTF->set_font($font,16);
		$RTF->cell($RTF->color("0").$RTF->bold(1).convert2thaidigit("$POSITION_LEVEL  ").$RTF->bold(0), "65", "left", "0");
		$RTF->close_line();
	}
	
	$RTF->open_line();			
	$RTF->set_font($font,18);
	$RTF->cell("", "5", "center", "0");
	$RTF->cell($RTF->color("2").$RTF->bold(1)."สังกัด  ".$RTF->bold(0), "20", "left", "0");
	$RTF->cell($RTF->color("0").$RTF->bold(1).convert2thaidigit("$ORG_NAME  ").$RTF->bold(0), "65", "left", "0");
	$RTF->close_line();

	$RTF->open_line();			
	$RTF->set_font($font,18);
	$RTF->cell("", "5", "center", "0");
	$RTF->cell($RTF->color("2").$RTF->bold(1)."เงินเดือน  ".$RTF->bold(0), "20", "left", "0");
	$RTF->cell($RTF->color("0").$RTF->bold(1).convert2thaidigit(number_format($PER_SALARY)).$RTF->bold(0), "20", "left", "0");
	$RTF->cell($RTF->color("2").$RTF->bold(1)." บาท".$RTF->bold(0), "7", "left", "0");
	$RTF->close_line();

	$RTF->open_line();			
	$RTF->set_font($font,18);
	$RTF->cell("", "5", "center", "0");
	$RTF->cell($RTF->color("2").$RTF->bold(1)."ชื่อผู้ประเมิน  ".$RTF->bold(0), "20", "left", "0");
	$RTF->cell($RTF->color("0").$RTF->bold(1).$REVIEW_PER_NAME.$RTF->bold(0), "65", "left", "0");
	$RTF->close_line();

	$RTF->open_line();			
	$RTF->set_font($font,18);
	$RTF->cell("", "5", "center", "0");
	$RTF->cell($RTF->color("2").$RTF->bold(1)."ตำแหน่ง  ".$RTF->bold(0), "20", "left", "0");
	$RTF->cell($RTF->color("0").$RTF->bold(1).convert2thaidigit("$REVIEW_PL_NAME").$RTF->bold(0), "65", "left", "0");
	$RTF->close_line();

	$RTF->ln();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell("", "5", "center", "0");
	$RTF->cell($RTF->color("8").$RTF->bold(1)."คำชี้แจง".$RTF->bold(0), "95", "left", "2");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->cell("", "6", "center", "0");
	$RTF->cell($RTF->color("2")."แบบสรุปการประเมินผลการปฏิบัติราชการนี้ มีด้วยกัน ๓ หน้า ประกอบด้วย", "85", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "9", "center", "0");
	$RTF->cell($RTF->bold(1)."ส่วนที่ ๑: ".$RTF->bold(0), "10", "left", "0");
	$RTF->cell("ข้อมูลของผู้รับการประเมิน", "30", "left", "0");
	$RTF->cell("เพื่อระบุรายละเอียดต่างๆ ที่เกี่ยวข้องกับตัวผู้รับการประเมิน", "50", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,12);
	$RTF->cell("", "9", "center", "0");
	$RTF->cell($RTF->bold(1)."ส่วนที่ ๒: ".$RTF->bold(0), "10", "left", "0");
	$RTF->cell("สรุปผลการประเมิน", "30", "left", "0");
	$RTF->cell("ใช้เพื่อกรอกค่าคะแนนการประเมินในองค์ประกอบด้านผลสัมฤทธิ์ของงาน องค์ประกอบ", "50", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,12);
	$RTF->cell("", "9", "center", "0");
	$RTF->cell("ด้านพฤติกรรมการปฏิบัติราชการ และน้ำหนักของทั้งสององค์ประกอบ ในแบบส่วนสรุปส่วนที่ ๒ นี้", "90", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->cell("", "9", "center", "0");
	$RTF->cell("ยังใช้สำหรับคำนวณคะแนนผลการปฏิบัติราชการรวมด้วย", "90", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->cell("", "9", "center", "0");
	$RTF->cell("- สำหรับคะแนนองค์ประกอบด้านผลสัมฤทธิ์ของงาน ให้นำมาจากแบบประเมินผลสัมฤทธิ์ของงาน โดยให้แนบท้ายแบบสรุปฉบับนี้", "90", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->cell("", "9", "center", "0");
	$RTF->cell("- สำหรับคะแนนองค์ประกอบด้านพฤติกรรมการปฏิบัติราชการ ให้นำมาจากแบบประเมินสมรรถนะ โดยให้แนบท้ายแบบสรุปฉบับนี้", "90", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->cell("", "9", "center", "0");
	$RTF->cell($RTF->bold(1)."ส่วนที่ ๓: ".$RTF->bold(0), "10", "left", "0");
	$RTF->cell($RTF->bold(1)."แผนพัฒนาการปฏิบัติราชการรายบุคคล".$RTF->bold(0), "30", "left", "0");
	$RTF->cell("ผู้ประเมิน และผู้รับการประเมินร่วมกันจัดทำแผนพัฒนาผลการปฏิบัติราชการ", "50", "left", "0");
	$RTF->close_line();

	$RTF->open_line();			
	$RTF->set_font($font,14);
	$RTF->cell("", "9", "center", "0");
	$RTF->cell($RTF->bold(1)."ส่วนที่ ๔: ".$RTF->bold(0), "10", "left", "0");
	$RTF->cell($RTF->bold(1)."การรับทราบผลการประเมิน".$RTF->bold(0), "30", "left", "0");
	$RTF->cell("ผู้รับการประเมินลงนามรับทราบผลการประเมิน", "50", "left", "0");
	$RTF->close_line();

	$RTF->open_line();			
	$RTF->set_font($font,14);
	$RTF->cell("", "9", "center", "0");
	$RTF->cell($RTF->bold(1)."ส่วนที่ ๕: ".$RTF->bold(0), "10", "left", "0");
	$RTF->cell($RTF->bold(1)."ความเห็นของผู้บังคับบัญชาเหนือขึ้นไป".$RTF->bold(0), "30", "left", "0");
	$RTF->cell("ผู้บังคับบัญชาเหนือขึ้นไปกลั่นกรองผลการประเมิน แผนพัฒนาผลการปฏิบัติ", "50", "left", "0");
	$RTF->close_line();

	$RTF->open_line();			
	$RTF->set_font($font,14);
	$RTF->cell("", "10", "center", "0");
	$RTF->cell(" ราชการและให้ความเห็น", "50", "left", "0");
	$RTF->close_line();
	
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->cell("", "9", "center", "0");
	$RTF->cell("คำว่า 'ผู้บังคับบัญชาเหนือขึ้นไป' สำหรับผู้ประเมินตามข้อ ๒ (๙) หมายถึงหัวหน้าส่วนราชการประจำจังหวัดผู้บังคับบัญชาของผู้รับการประเมิน", "90", "left", "0");
	$RTF->close_line();
	//$RTF->ln();
	
	// ======================= PAGE 2 =====================//	
	$RTF->new_page();
	
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->cell($RTF->color("0")."ส่วนที่ ๒: การสรุปผลการประเมิน", "100", "left", 2);
	$RTF->close_line();
	$RTF->ln();

	print_header(2);

	$RTF->open_line();			
	$RTF->set_font($font,12);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell(convert2thaidigit("องค์ประกอบที่ ๑: ผลสัมฤทธิ์ของงาน"), $heading_width2[0], "left", "0", "TRBL");
	$RTF->cell(convert2thaidigit(number_format($SHOW_SCORE_KPI, 2)), $heading_width2[1], "center", "0", "TRBL");
	if ($PERFORMANCE_WEIGHT)
		$RTF->cell(convert2thaidigit($PERFORMANCE_WEIGHT . "%"), $heading_width2[2], "center", "0", "TRBL");
	else
		$RTF->cell("", $heading_width2[2], "center", "16", "TRBL");
	$RTF->cell(convert2thaidigit(number_format($SUM_KPI, 2)), $heading_width2[3], "center", "0", "TRBL");
	$RTF->close_line();

	//แถวที่ 1
	$RTF->open_line();			
	$RTF->set_font($font,12);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell(convert2thaidigit("องค์ประกอบที่ ๒: พฤติกรรมการปฏิบัติราชการ (สมรรถนะ)"), $heading_width2[0], "left", "0", "TRBL");
	$RTF->cell(convert2thaidigit(number_format($SHOW_SCORE_COMPETENCE, 2)), $heading_width2[1], "center", "0", "TRBL");
	if ($COMPETENCE_WEIGHT)
		$RTF->cell(convert2thaidigit($COMPETENCE_WEIGHT . "%"), $heading_width2[2], "center", "0", "TRBL");
	else
		$RTF->cell("", $heading_width2[2], "center", "0", "TRBL");
	$RTF->cell(convert2thaidigit(number_format($SUM_COMPETENCE, 2)), $heading_width2[3], "center", "0", "TRBL");
	$RTF->close_line();
	
	//แถวที่ 2
	$RTF->open_line();			
	$RTF->set_font($font,12);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell(convert2thaidigit("องค์ประกอบอื่นๆ (ถ้ามี)"), $heading_width2[0], "left", "0", "TRBL");
	$RTF->cell(convert2thaidigit(number_format($SHOW_SCORE_OTHER, 2)), $heading_width2[1], "center", "0", "TRBL");
	if ($OTHER_WEIGHT)
		$RTF->cell(convert2thaidigit($OTHER_WEIGHT . "%"), $heading_width2[2], "center", "0", "TRBL");
	else
		$RTF->cell("", $heading_width2[2], "center", "0", "TRBL");
	$RTF->cell(convert2thaidigit(number_format($SUM_OTHER, 2)), $heading_width2[3], "center", "0", "TRBL");
	$RTF->close_line();
	
	//แถวที่ 3
	$RTF->open_line();			
	$RTF->set_font($font,12);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("รวม", ($heading_width2[0]+$heading_width2[1]), "right", "0", "TRBL");
	$RTF->cell(convert2thaidigit("๑๐๐%"), $heading_width2[2], "center", "0", "TRBL");
	$RTF->cell(convert2thaidigit(number_format($SUM_TOTAL, 2)), $heading_width2[3], "center", "0", "TRBL");
	$RTF->close_line();

	$RTF->ln();

	$RTF->open_line();			
	$RTF->set_font($font,14);
	$RTF->color("1");	// 0=DARKGRAY
	$RTF->cell("ระดับผลการประเมิน", "20", "center", "0");
	$RTF->close_line();

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
        
        
        /**/
	//หาระดับผลการประเมินหลัก 
	$cmd = "	select		AM_CODE, AM_NAME, AM_POINT_MIN, AM_POINT_MAX, AM_ACTIVE
								from		PER_ASSESS_MAIN where AM_YEAR = '$KF_YEAR' and AM_CYCLE = $KF_CYCLE and PER_TYPE = $PER_TYPE
					order by AM_POINT_MAX desc, AM_CODE desc ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$AM_CODE = $data[AM_CODE];
                $AM_NAME = $data[AM_NAME];
		$AM_POINT_MIN = $data[AM_POINT_MIN];
		$AM_POINT_MAX = $data[AM_POINT_MAX];

			$RTF->open_line();
			$RTF->set_font($font,14);
			$RTF->color("1");	// 0=DARKGRAY
			$RTF->cell("", "10", "center", "0");
                        /*Release 5.2.1.8 Begin*/
                        if($AM_CODE==$chkam_code){
                           $RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "center", 0);
                        }else{
                           $RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "center", 0);  
                        }
                        /*Release 5.2.1.8 End*/
			
			$RTF->cell("$AM_NAME", "40", "left", "0");
			$RTF->close_line();

	} //end while
	
	//--------------------------------------------------
        /*
	$RTF->open_line();
	$RTF->cell("", "100", "left", "0");
	$RTF->close_line();*/
	
        $RTF->new_page();
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 0=DARKGRAY
	$RTF->cell("ส่วนที่ ๓: แผนพัฒนาการปฏิบัติราชการรายบุคคล", "100", "left", "2");
	$RTF->close_line();
	$RTF->ln();

	print_header(3);

	$data_count = 0;
	foreach($ARR_IPIP as $IPIP_ID => $DEVELOP_COMPETENCE){
		$data_count++;

		$RTF->open_line();
		$RTF->set_font($font,12);
		$RTF->color("0");	// 0=BLACK
		$RTF->cell(convert2thaidigit($DEVELOP_COMPETENCE), $heading_width3[0], "left", "0", "TRBL");
		$RTF->cell(convert2thaidigit($ARR_IPIP_METHOD[$IPIP_ID]), $heading_width3[1], "left", "0", "TRBL");
		$RTF->cell(convert2thaidigit($ARR_IPIP_INTERVAL[$IPIP_ID]), $heading_width3[2], "left", "0", "TRBL");
		$RTF->close_line();
	} // end foreach
	$RTF->ln();
    $RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("ความเห็นของผู้บังคับบัญชาชั้นต้น:", "100", "left", "9");
	$RTF->close_line();
	
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("ความเห็นด้านผลงาน", "99", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,12);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell(convert2thaidigit($RESULT_COMMENT), "100", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("ความเห็นด้านสมรรถนะ", "99", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,12);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell(convert2thaidigit($COMPETENCE_COMMENT), "95", "left", "0");
	$RTF->close_line();
	
	// ======================= PAGE 3 =====================//	
	$RTF->new_page();
	
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("ส่วนที่ ๔: การรับทราบผลการประเมิน", "100", "left", "2");
	$RTF->close_line();

	$RTF->ln();

	$RTF->open_line();
	$RTF->set_font($font,12);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("ผู้รับการประเมิน:", "100", "left", "9");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("0");	// 0=BLACK
	/*if ($PER_NAME)
		$RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "center", 0);
	else
		$RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "center", 0);
	*/
        $str_accept='';
        if($ACCEPT_FLAG=="1"){
            $RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "center", 0);
           // if($ACCEPT_FLAG=="0"){$str_accept=' (ไม่ยอมรับ)';}
           // if($ACCEPT_FLAG==1){$str_accept=' (ยอมรับ)';}
        }else{
            $RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "center", 0);
        }
	
	$RTF->cell("ได้รับทราบผลการประเมินและแผนพัฒนาผลการปฏิบัติราชการรายบุคคลแล้ว ".$str_accept, "100", "left", "0");
	$RTF->close_line();
	//ผู้รับการประเมิน
	//die( "x=>".$ACCEPT_MONTHS);
		$MONTH_ACCEPT = get_month($ACCEPT_MONTHS);
	  $PIC_SIGN ="";  
	if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์	
        
            if($ACCEPT_FLAG == 1){
                            $PIC_SIGN = getPIC_SIGN($PER_ID,$PER_CARDNO);
            }    
            if($PIC_SIGN){
                    $RTF->open_line();
                    $RTF->cell("", "40", "left", "0");
                    $RTF->cellImage($PIC_SIGN, 60, 50, "center", 0);		//right
                    $RTF->close_line();
            }else{
                    $RTF->open_line();
                    $RTF->set_font($font,12);
                    $RTF->color("1");	// 1=DARKGRAY
                    $RTF->cell("", "40", "left", "0");
                    $RTF->cell(("ลงชื่อ $SIGN_PN_NAME". str_repeat(".", 70)), "50", "right", "0");
                    $RTF->close_line();
            }
	}else{
		$RTF->open_line();
		$RTF->set_font($font,12);
		$RTF->color("1");	// 1=DARKGRAY
		$RTF->cell("", "40", "left", "0");
		$RTF->cell(("ลงชื่อ $SIGN_PN_NAME". str_repeat(".", 70)), "50", "right", "0");
		$RTF->close_line();
	}	
	
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "40", "left", "0");
	$RTF->cell(("( $SIGN_NAME )"), "50", "center", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "40", "left", "0");
        /*Release 5.2.1.6 Begin*/
        if(!empty($PM_NAME)){ 
            $valPM_NAME = $PM_NAME;
        }else{
            $valPM_NAME = strreplaceLvl($PL_NAME.$POSITION_LEVEL);
            //$valPM_NAME = strreplaceLvl($PL_NAME);
        }
	$RTF->cell(("ตำแหน่ง ".convert2thaidigit($valPM_NAME)), "50", "center", "0");
        /*Release 5.2.1.6 End*/
	//$RTF->cell(("ตำแหน่ง ".convert2thaidigit("$PL_NAME$POSITION_LEVEL")), "50", "center", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "40", "left", "0");
        if($ACCEPT_DATE && ($ACCEPT_FLAG == "0" || $ACCEPT_FLAG == "1" ) ){
        $RTF->cell(("วันที่ ". $ACCEPT_DAY ." เดือน ". $MONTH_ACCEPT ." พ.ศ. ". $ACCEPT_YEAR), "50", "center", "0");
        }else{
		$RTF->cell(("วันที่ ". str_repeat(".", 10) ." เดือน ". str_repeat(".", 25) ." พ.ศ. ". str_repeat(".", 15)), "50", "center", "0");	
        }

	$RTF->close_line();
	$RTF->ln();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("ผู้ประเมิน:", "100", "left", "9");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("0");	// 0=BLACK
        if($ACCEPT_FLAG=="1"){
            $RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "center", 0);
        }else{
            $RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "center", 0);
        }
	$RTF->cell("ได้แจ้งผลการประเมินและผู้รับการประเมินได้ลงนามรับทราบ", "100", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("0");	// 0=BLACK
	if($ACCEPT_FLAG=="0"){
            $RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "center", 0);
        }else{
            $RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "center", 0);
        }
        if($ACCEPT_FLAG=="0" && $KF_SCORE_STATUS == "1" && $SCORE_STATUS_DATE !=""){
            $RTF->cell("ได้แจ้งผลการประเมิน เมื่อวันที่ ". $creat_date, "100", "left", "0");
        }else{
            $RTF->cell("ได้แจ้งผลการประเมิน เมื่อวันที่ ". str_repeat(".", 50), "100", "left", "0");
        }
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "1", "left", "0");
	$RTF->cell("แต่ผู้รับการประเมินไม่ลงนามรับทราบ", "99", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "1", "left", "0");
	$RTF->cell("โดยมี ". str_repeat(".", 75)."เป็นพยาน", "99", "left", "0");
	$RTF->close_line();

	//ลงชื่อพยาน
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "1", "left", "0");
	$RTF->cell(("ลงชื่อ ". str_repeat(".", 80)."พยาน"), "99", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "1", "left", "0");
	$RTF->cell(("ตำแหน่ง". str_repeat(".", 77)), "99", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "1", "left", "0");
	$RTF->cell(("วันที่ ". str_repeat(".", 10) ." เดือน ". str_repeat(".", 25) ." พ.ศ. ". str_repeat(".", 15)), "99", "left", "0");
	$RTF->close_line();
	
	//ลงชื่อผู้ประเมิน
	  $PIC_SIGN ="";  
	if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์	
		//if(($KF_SCORE_STATUS!="" && $KF_SCORE_STATUS!=0)&& $KF_SCORE_STATUS == 1){
                if($KF_SCORE_STATUS == "1" && $PER_ID_REVIEW){
			$PIC_SIGN = getPIC_SIGN($PER_ID_REVIEW,$PER_CARDNO_REVIEW_N);
		}
		if($PIC_SIGN){
			$RTF->open_line();
			$RTF->cell("", "40", "left", "0");
			$RTF->cellImage($PIC_SIGN, 20, 50, "center", 0);
			$RTF->close_line();
		}else{
		$RTF->open_line();
		$RTF->set_font($font,14);
		$RTF->color("0");	// 0=BLACK
		$RTF->cell("", "40", "left", "0");
		$RTF->cell(("ลงชื่อ $REVIEW_SIGN_PN_NAME". str_repeat(".", 70)), "50", "left", "0");
		$RTF->close_line();
		}
	}else{
		$RTF->open_line();
		$RTF->set_font($font,14);
		$RTF->color("0");	// 0=BLACK
		$RTF->cell("", "40", "left", "0");
		$RTF->cell(("ลงชื่อ $REVIEW_SIGN_PN_NAME". str_repeat(".", 70)), "50", "left", "0");
		$RTF->close_line();
	}
 
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "40", "left", "0");
	$RTF->cell(("( $REVIEW_SIGN_NAME )"), "50", "center", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "40", "left", "0");
	$RTF->cell(("ตำแหน่ง $REVIEW_PL_NAME "), "50", "center", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "40", "left", "0");
	 //die($KF_SCORE_STATUS."<>".$SCORE_STATUS_DATE );
         if($KF_SCORE_STATUS == "1" && $SCORE_STATUS_DATE ){
			
			$RTF->cell(("วันที่ ". $SCORE_DAY ." เดือน ". $ACCEPT_MONTH = get_month($SCORE_MONT) ." พ.ศ. ". $SCORE_YEAR), "50", "center", "0");
        }else{
			$RTF->cell(("วันที่ ". str_repeat(".", 10) ." เดือน ". str_repeat(".", 25) ." พ.ศ. ". str_repeat(".", 15)), "99", "left", "0");
        }
	$RTF->close_line();
	$RTF->ln();
	
	if($SESS_KPI_PER_REVIEW==1){		// ผู้ให้ข้อมูล
		$RTF->ln();
	
		$RTF->open_line();
		$RTF->set_font($font,14);
		$RTF->color("0");	// 0=BLACK
		$RTF->cell("ผู้ให้ข้อมูล:", "100", "left", "9");
		$RTF->close_line();
	
		$RTF->open_line();
		$RTF->set_font($font,14);
		$RTF->color("0");	// 0=BLACK
		$RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "center", 0);
		$RTF->cell("ได้แจ้งผลการให้ข้อมูล เมื่อวันที่ ". str_repeat(".", 50), "100", "left", "0");
		$RTF->close_line();
	
		//ลงชื่อผู้ให้ข้อมูล
                $PIC_SIGN ="";  
		if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์	
                        if(($KF_SCORE_STATUS!="" && $KF_SCORE_STATUS!=0)&& $KF_SCORE_STATUS == 1){
                            $PIC_SIGN = getPIC_SIGN($PER_ID_REVIEW0,$PER_CARDNO_REVIEW0);
                        }    
			if($PIC_SIGN){
				$RTF->open_line();
				$RTF->cell("", "40", "left", "0");
				$RTF->cellImage($PIC_SIGN, 20, 50, "center", 0);
				$RTF->close_line();
			}else{
			$RTF->open_line();
			$RTF->set_font($font,14);
			$RTF->color("0");	// 0=BLACK
			$RTF->cell("", "40", "left", "0");
			$RTF->cell(("ลงชื่อ $REVIEW_SIGN_PN_NAME0". str_repeat(".", 70)), "50", "left", "0");
			$RTF->close_line();
			}
		}else{
			$RTF->open_line();
			$RTF->set_font($font,14);
			$RTF->color("0");	// 0=BLACK
			$RTF->cell("", "40", "left", "0");
			$RTF->cell(("ลงชื่อ $REVIEW_SIGN_PN_NAME0". str_repeat(".", 70)), "50", "left", "0");
			$RTF->close_line();
		}
	
		$RTF->open_line();
		$RTF->set_font($font,14);
		$RTF->color("0");	// 0=BLACK
		$RTF->cell("", "40", "left", "0");
		$RTF->cell(("( $REVIEW_SIGN_NAME0 )"), "50", "center", "0");
		$RTF->close_line();
	
		$RTF->open_line();
		$RTF->set_font($font,14);
		$RTF->color("0");	// 0=BLACK
		$RTF->cell("", "40", "left", "0");
		$RTF->cell(("ตำแหน่ง $REVIEW_PL_NAME0 "), "50", "center", "0");
		$RTF->close_line();
	
		$RTF->open_line();
		$RTF->set_font($font,14);
		$RTF->color("0");	// 0=BLACK
		$RTF->cell("", "40", "left", "0");
		$RTF->cell(("วันที่ ". str_repeat(".", 10) ." เดือน ". str_repeat(".", 25) ." พ.ศ. ". str_repeat(".", 15)), "50", "center", "0");
		$RTF->close_line();
		$RTF->ln();
	}  //end if
			
	// ======================= PAGE 4 =====================//	
	$RTF->new_page();
	
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("ส่วนที่ ๕: ความเห็นของผู้บังคับบัญชาเหนือขึ้นไป", "100", "left", "2");
	$RTF->close_line();

	$RTF->ln();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("ผู้บังคับบัญชาเหนือขึ้นไป:", "100", "left", "9");
	$RTF->close_line();
	
	$RTF->open_line();
	if(trim($AGREE_REVIEW1)) 
		$RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "left", 0);
	else 
		$RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "left", 0);
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("เห็นด้วยกับผลการประเมิน", "99", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,12);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell(convert2thaidigit($AGREE_REVIEW1), "100", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	if(trim($DIFF_REVIEW1)) 
		$RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "left", 0);
	else 
		$RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "left", 0);
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("มีความเห็นแตกต่าง ดังนี้", "99", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,12);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell(convert2thaidigit($DIFF_REVIEW1), "95", "left", "0");
	$RTF->close_line();
	$PIC_SIGN="";
	if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์	
            if($AGREE_REVIEW1 || $DIFF_REVIEW1){
                $PIC_SIGN = getPIC_SIGN($PER_ID_REVIEW1,$PER_CARDNO_REVIEW1);
            }
            if($PIC_SIGN){
                    $RTF->open_line();
                    $RTF->cell("", "40", "left", "0");
                    $RTF->cellImage($PIC_SIGN, 20, 50, "center", 0);
                    $RTF->close_line();
            }else{
                    $RTF->open_line();
                    $RTF->set_font($font,12);
                    $RTF->color("0");	// 0=BLACK
                    $RTF->cell("", "40", "left", "0");
                    $RTF->cell(("ลงชื่อ $REVIEW_SIGN_PN_NAME1". str_repeat(".", 70)), "50", "center", "0");
                    $RTF->close_line();
            }
	}else{
            $RTF->open_line();
            $RTF->set_font($font,12);
            $RTF->color("0");	// 0=BLACK
            $RTF->cell("", "40", "left", "0");
            $RTF->cell(("ลงชื่อ $REVIEW_SIGN_PN_NAME1". str_repeat(".", 70)), "50", "center", "0");
            $RTF->close_line();
	}

	$RTF->open_line();
	$RTF->set_font($font,12);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "40", "left", "0");
	$RTF->cell(("( $REVIEW_SIGN_NAME1 )"), "50", "center", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,12);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "40", "left", "0");
	$RTF->cell(("ตำแหน่ง $REVIEW_PL_NAME1"), "50", "center", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,12);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "40", "left", "0");
	if(($AGREE_REVIEW1 || $DIFF_REVIEW1)&& $DATE_REVIEW1 && $CH_SCORE_DAY=="N"){
	$RTF->cell(("วันที่ ". $DATE_REVIEW1_DAY ." เดือน ". $review1 = get_month($DATE_REVIEW1_MONTH) ." พ.ศ. ". $DATE_REVIEW1_YEAR), "50", "center", "0");
	}else{
	$RTF->cell(("วันที่ ". str_repeat(".", 10) ." เดือน ". str_repeat(".", 25) ." พ.ศ. ". str_repeat(".", 15)), "50", "center", "0");	
	}
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("ผู้บังคับบัญชาเหนือขึ้นไปอีกชั้นหนึ่ง (ถ้ามี):", "100", "left", "2");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	if(trim($AGREE_REVIEW2)) 
		$RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "center", 0);
	else 
		$RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "center", 0);
	$RTF->cell("เห็นด้วยกับผลการประเมิน", "99", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,12);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell(convert2thaidigit($AGREE_REVIEW2), "100", "center", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	if(trim($DIFF_REVIEW2)) 
		$RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "center", 0);
	else 
		$RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "center", 0);
	$RTF->cell("มีความเห็นแตกต่าง ดังนี้", "99", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,12);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell(convert2thaidigit($DIFF_REVIEW2), "99", "left", "0");
	$RTF->close_line();
	
	$PIC_SIGN="";
	if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์	

            if($AGREE_REVIEW2 || $DIFF_REVIEW2){
                $PIC_SIGN = getPIC_SIGN($PER_ID_REVIEW2,$PER_CARDNO_REVIEW2);
            }
            if($PIC_SIGN){
                $RTF->open_line();
                $RTF->cell("", "40", "left", "0");
                $RTF->cellImage($PIC_SIGN, 20, 50, "center", 0);
                $RTF->close_line();
            }else{
                $RTF->open_line();
                $RTF->set_font($font,12);
                $RTF->color("1");	// 1=DARKGRAY
                $RTF->cell("", "40", "center", "0");
                $RTF->cell(("ลงชื่อ $REVIEW_SIGN_PN_NAME2". str_repeat(".", 70)), "50", "center", "0");
                $RTF->close_line();
            }
	}else{
		$RTF->open_line();
		$RTF->set_font($font,12);
		$RTF->color("1");	// 1=DARKGRAY
		$RTF->cell("", "40", "center", "0");
		$RTF->cell(("ลงชื่อ $REVIEW_SIGN_PN_NAME2". str_repeat(".", 70)), "50", "center", "0");
		$RTF->close_line();
	}

	$RTF->open_line();
	$RTF->set_font($font,12);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("", "40", "center", "0");
	$RTF->cell(("( $REVIEW_SIGN_NAME2 )"), "50", "center", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,12);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("", "40", "center", "0");
	$RTF->cell(("ตำแหน่ง $REVIEW_PL_NAME2"), "50", "center", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,12);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("", "40", "center", "0");
	if(($AGREE_REVIEW2 || $DIFF_REVIEW2)&& $DATE_REVIEW2 && $CH_SCORE_DAY=="N"){//$CH_SCORE_DAY = ค่าจาก confix c06 ช่อง ไม่แสดงวันที่ในแบบสรุปผลการประเมินการปฏิบัติราชการ   
	$RTF->cell(("วันที่ ". $DATE_REVIEW2_DAY ." เดือน ". $review2 = get_month($DATE_REVIEW2_MONTH) ." พ.ศ. ". $DATE_REVIEW2_YEAR), "50", "center", "0");
	}else{
	$RTF->cell(("วันที่ ". str_repeat(".", 10) ." เดือน ". str_repeat(".", 25) ." พ.ศ. ". str_repeat(".", 15)), "50", "center", "0");	
	}
	$RTF->close_line();
	$RTF->ln();

	// ======================= PAGE 5 =====================//	
	$RTF->new_page();
	
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("แบบสรุปการประเมินสมรรถนะ", "50", "left", "0", "");
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("รอบการประเมิน", "20", "right", "0", "");
	if($KF_CYCLE==1) 
		$RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "center", 0, "");
	else 
		$RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "center", 0, "");
	$RTF->cell(convert2thaidigit("รอบที่ ๑"), "14", "left", "0", "");
	if($KF_CYCLE==2) 
		$RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "center", 0, "");
	else 
		$RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "center", 0, "");
	$RTF->cell(convert2thaidigit("รอบที่ ๒"), "14", "left", "0", "");
	$RTF->close_line();


    if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์	

             if($ACCEPT_FLAG == 1){
                $PIC_PER_ID = getPIC_SIGN($PER_ID,$PER_CARDNO);
            }
    }
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("ชื่อผู้รับการประเมิน: $PER_NAME", "60", "left", "0", "");
    if($PIC_PER_ID){
        $RTF->cellImage($PIC_PER_ID, 15, 8, "left", 0, "");
    }else{
        $RTF->cell(("ลงนาม ". str_repeat(".", 40)), "30", "right", "0", "");
    }
	$RTF->close_line();

    $RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("ชื่อผู้บังคับบัญชา/ผู้ประเมิน: $REVIEW_PER_NAME", "60", "left", "0", "");
    $PIC_REVIEW ="";  
		if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์	
                        if($KF_SCORE_STATUS == "1" && $PER_ID_REVIEW){
                            $PIC_REVIEW = getPIC_SIGN($PER_ID_REVIEW,$PER_CARDNO_REVIEW_N);
                        }
			if($PIC_REVIEW){
				
				$RTF->cellImage($PIC_REVIEW, 15, 8, "right", 0);
			
			}else{
				$RTF->cell(("ลงนาม ". str_repeat(".", 40)), "30", "right", "0", "");
			}
		}else{
			    $RTF->cell(("ลงนาม ". str_repeat(".", 40)), "30", "right", "0", "");
		}
	$RTF->close_line();
	
	
	print_header(4);
	
	$data_count = 0;
	foreach($ARR_COMPETENCE as $KC_ID => $CP_NAME){
		$data_count++;

		$RTF->open_line();
		$RTF->set_font($font,14);
		$RTF->color("1");	// 1=DARKGRAY
		$RTF->cell(convert2thaidigit($CP_NAME), $heading_width4[0], "left", "0", "TRBL");
		$RTF->cell(convert2thaidigit($ARR_COMPETENCE_TARGET[$KC_ID]), $heading_width4[1], "center", "0", "TRBL");
		$RTF->cell($RTF->bold(1).convert2thaidigit($ARR_COMPETENCE_EVALUATE[$KC_ID]).$RTF->bold(0), $heading_width4[2], "center", "0", "TRBL");
		if ($ARR_COMPETENCE_WEIGHT[$KC_ID])
			$RTF->cell(convert2thaidigit($ARR_COMPETENCE_WEIGHT[$KC_ID] . "%"), $heading_width4[3], "center", "0", "TRBL");
		else
			$RTF->cell("", $heading_width4[3], "center", "0", "TRBL");
		$RTF->cell($RTF->bold(1).convert2thaidigit($PC_SCORE[$KC_ID]).$RTF->bold(0), $heading_width4[4], "center", "0", "TRBL");
		$RTF->cell("", $heading_width4[5], "center", "0", "TRBL");
		$RTF->close_line();
	} // end foreach
	
	/*if ($COMPETENCY_SCALE==2 || $COMPETENCY_SCALE==5) {	
		 $TOTAL_PC_SCORE= ($YYY_PC_SCORE * ($XXX_WEIGHT/100)) ;
	}*/
	
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("คะแนนรวม ", $heading_width4[0]+$heading_width4[1], "right", "0", "TRBL");
	$RTF->cell($RTF->bold(1).convert2thaidigit(array_sum($ARR_COMPETENCE_EVALUATE)).$RTF->bold(0), $heading_width4[2], "center", "0", "TRBL");
	if (array_sum($ARR_COMPETENCE_WEIGHT))
		$RTF->cell(convert2thaidigit(array_sum($ARR_COMPETENCE_WEIGHT) . "%"), $heading_width4[3], "center", "0", "TRBL");
	else
		$RTF->cell("", $heading_width4[3], "center", "0", "TRBL");
	$RTF->cell($RTF->bold(1).convert2thaidigit($TOTAL_PC_SCORE).$RTF->bold(0), $heading_width4[4], "center", "0", "TRBL");
	$RTF->cell("", $heading_width4[5], "center", "0", "TRBL");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("คะแนนประเมิน ", $heading_width4[0]+$heading_width4[1], "right", "0", "TRBL");
	$RTF->cell($RTF->bold(1).convert2thaidigit($TOTAL_PC_SCORE).$RTF->bold(0), ($heading_width4[2] +$heading_width4[3]+$heading_width4[4]+$heading_width4[5]), "center", "0", "TRBL");
	$RTF->close_line();
	$RTF->ln();
	
	// ======================= PAGE 6 =====================//	
	$RTF->new_page();
	
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("แบบสรุปการประเมินผลสัมฤทธิ์ของงาน", "50", "left", "0");
	$RTF->set_font($font,14);
	$RTF->cell("รอบการประเมิน", "20", "right", "0");
	if($KF_CYCLE==1) 
		$RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "center", 0);
	else 
		$RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "center", 0);
	$RTF->cell(convert2thaidigit("รอบที่ ๑"), "14", "left", "0");
	if($KF_CYCLE==2) 
		$RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "center", 0);
	else 
		$RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "center", 0);
	$RTF->cell(convert2thaidigit("รอบที่ ๒"), "14", "left", "0");
	$RTF->close_line();
        
        //ปิดเพราะไม่ตามสิทธิ์
        //$PIC_PER_ID = getPIC_SIGN($PER_ID,$PER_CARDNO);
        //$PIC_REVIEW = getPIC_SIGN($PER_ID_REVIEW,$PER_CARDNO_REVIEW_N);
 
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("ชื่อผู้รับการประเมิน: $PER_NAME", "60", "left", "0", "");
    if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์	
        if($ACCEPT_FLAG == 1){
            $PIC_PER_ID = getPIC_SIGN($PER_ID,$PER_CARDNO);
        }
    }
    if($PIC_PER_ID){
        $RTF->cellImage($PIC_PER_ID, 15, 8, "right", 0);
    }else{
        $RTF->cell(("ลงนาม ". str_repeat(".", 40)), "30", "right", "0", "");
    }
	$RTF->close_line();

    $RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("ชื่อผู้บังคับบัญชา/ผู้ประเมิน: $REVIEW_PER_NAME", "60", "left", "0", "");
	$PIC_REVIEW ="";  
		if($SESS_E_SIGN[1]==1){	// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์	
                        if($KF_SCORE_STATUS == "1" && $PER_ID_REVIEW){
                            $PIC_REVIEW = getPIC_SIGN($PER_ID_REVIEW,$PER_CARDNO_REVIEW_N);
                        }
			if($PIC_REVIEW){
				
				$RTF->cellImage($PIC_REVIEW, 15, 8, "right", 0);
			
			}else{
				$RTF->cell(("ลงนาม ". str_repeat(".", 40)), "30", "right", "0", "");
			}
		}else{
			    $RTF->cell(("ลงนาม ". str_repeat(".", 40)), "30", "right", "0", "");
		}


	$RTF->close_line();

	print_header(5);
	
	$data_count = 0;
	foreach($ARR_KPI as $PG_ID => $KPI_NAME){
		$data_count++;

		$RTF->open_line();
		$RTF->set_font($font,14);
		$RTF->color("0");	// 0=BLACK
		$RTF->cell(convert2thaidigit($KPI_NAME), $heading_width5[0], "left", "0", "TRBL");
		for($i=1; $i<=5; $i++) { 
			//$RTF->cell(convert2thaidigit(${"ARR_KPI_TARGET".$i}[$PG_ID]), $heading_width5[1], "center", "0", "TRBL");
			$RTF->cell(convert2thaidigit($i), $heading_width5[1], "center", "0", "TRBL");
		}
//		for($i=1; $i<=5; $i++){ 
//			if($i == $ARR_KPI_EVALUATE[$PG_ID]){
					$SCORE[$PG_ID]=$ARR_KPI_EVALUATE[$PG_ID];
					$SUMSCORE[$PG_ID]=$ARR_KPI_EVALUATE[$PG_ID]*$ARR_KPI_WEIGHT[$PG_ID];
					$TOTAL_KPI_RESULT+=$ARR_KPI_EVALUATE[$PG_ID];
//			}	
//		} // end for
		$RTF->set_font($font,12);
		$RTF->color("0");	// 0=BLACK
		$RTF->cell(convert2thaidigit($ARR_KPI_EVALUATE[$PG_ID]), $heading_width5[2], "center", "0", "TRBL");
		$RTF->cell(convert2thaidigit($ARR_KPI_WEIGHT[$PG_ID]), $heading_width5[3], "center", "0", "TRBL");
		$RTF->cell(convert2thaidigit($SUMSCORE[$PG_ID]), $heading_width5[4], "center", "0", "TRBL");
		$RTF->close_line();

		//===ข้อ 1 - 5===//
		$RTF->open_line();
		$RTF->set_font($font,12);
		$RTF->color("1");	// 1=DARKGRAY
		$RTF->cell($RTF->bold(1).convert2thaidigit("1 = ".$ARR_KPI_TARGET1_DESC[$PG_ID]).$RTF->bold(0), 100, "left", "0", "TRBL");
		$RTF->close_line();

		$RTF->open_line();
		$RTF->set_font($font,12);
		$RTF->color("1");	// 1=DARKGRAY
		$RTF->cell($RTF->bold(1).convert2thaidigit("2 = ".$ARR_KPI_TARGET2_DESC[$PG_ID]).$RTF->bold(0), 100, "left", "0", "TRBL");
		$RTF->close_line();

		$RTF->open_line();
		$RTF->set_font($font,12);
		$RTF->color("1");	// 1=DARKGRAY
		$RTF->cell($RTF->bold(1).convert2thaidigit("3 = ".$ARR_KPI_TARGET3_DESC[$PG_ID]).$RTF->bold(0), 100, "left", "0", "TRBL");
		$RTF->close_line();

		$RTF->open_line();
		$RTF->set_font($font,12);
		$RTF->color("1");	// 1=DARKGRAY
		$RTF->cell($RTF->bold(1).convert2thaidigit("4 = ".$ARR_KPI_TARGET4_DESC[$PG_ID]).$RTF->bold(0), 100, "left", "0", "TRBL");
		$RTF->close_line();

		$RTF->open_line();
		$RTF->set_font($font,12);
		$RTF->color("1");	// 1=DARKGRAY
		$RTF->cell($RTF->bold(1).convert2thaidigit("5 = ".$ARR_KPI_TARGET5_DESC[$PG_ID]).$RTF->bold(0), 100, "left", "0", "TRBL");
		$RTF->close_line();
	} // end foreach
//	echo "$data_count - ".count($ARR_KPI);

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("คะแนนรวม ", ($heading_width5[0]+($heading_width5[1]*5)), "left", "0", "TRBL");
	$RTF->cell($RTF->bold(1).convert2thaidigit($TOTAL_KPI_RESULT).$RTF->bold(0), $heading_width5[2], "center", "0", "TRBL");
	$RTF->cell($RTF->bold(1).convert2thaidigit($TOTAL_KPI_WEIGHT).$RTF->bold(0), $heading_width5[3], "center", "0", "TRBL");
	$RTF->cell($RTF->bold(1).convert2thaidigit($TOTAL_KPI_EVALUATE).$RTF->bold(0), $heading_width5[4], "center", "0", "TRBL");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("คะแนนประเมิน ", ($heading_width5[0]+($heading_width5[1]*5)), "left", "0", "TRBL");
	$RTF->cell($RTF->bold(1).convert2thaidigit(number_format(($TOTAL_KPI_EVALUATE / $TOTAL_KPI_WEIGHT), 2)).$RTF->bold(0), ($heading_width5[2]+$heading_width5[3]+$heading_width5[4]), "center", "0", "TRBL");
	$RTF->close_line();
	$RTF->ln();

	// ======================= PAGE 7 =====================//	
	$RTF->new_page();
	
	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell($RTF->bold(1)."บันทึกเพิ่มเติมประกอบแบบสรุปการประเมินสมรรถนะ".$RTF->bold(0), "100", "left", "2");
	$RTF->close_line();

	$RTF->ln();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("สมรรถนะ".str_repeat("_", 30), "100", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell(str_repeat(".", 170), "100", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell(str_repeat(".", 170), "100", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell(str_repeat(".", 170), "100", "left", "0");
	$RTF->close_line();

	//__2
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("16");	// 16=LIGHTGRAY
	$RTF->cell("สมรรถนะ".str_repeat("_", 30), "100", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("16");	// 16=LIGHTGRAY
	$RTF->cell(str_repeat(".", 170), "100", "left", "0");
	$RTF->close_line();
	
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("16");	// 16=LIGHTGRAY
	$RTF->cell(str_repeat(".", 170), "100", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("16");	// 16=LIGHTGRAY
	$RTF->cell(str_repeat(".", 170), "100", "left", "0");
	$RTF->close_line();

	//__3	
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("16");	// 16=LIGHTGRAY
	$RTF->cell("สมรรถนะ".str_repeat("_", 30), "100", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("16");	// 16=LIGHTGRAY
	$RTF->cell(str_repeat(".", 170), "100", "left", "0");
	$RTF->close_line();
	
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("16");	// 16=LIGHTGRAY
	$RTF->cell(str_repeat(".", 170), "100", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("16");	// 1=DARKGRAY
	$RTF->cell(str_repeat(".", 170), "100", "left", "0");
	$RTF->close_line();
	
	//__4	
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("16");	// 1=LIGHTGRAY
	$RTF->cell("สมรรถนะ".str_repeat("_", 30), "100", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("16");	// 16=LIGHTGRAY
	$RTF->cell(str_repeat(".", 170), "100", "left", "0");
	$RTF->close_line();
	
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("16");	// 16=LIGHTGRAY
	$RTF->cell(str_repeat(".", 170), "100", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("16");	// 16=LIGHTGRAY
	$RTF->cell(str_repeat(".", 170), "100", "left", "0");
	$RTF->close_line();
	$RTF->ln();

	// ======================= PAGE 7 =====================//	
	$RTF->new_page();
	
	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell($RTF->bold(1)."ผลสำเร็จของงานจริง".$RTF->bold(0), "100", "left", "2");
	$RTF->close_line();

	print_header(6);

	$data_count = 0;
	foreach($ARR_KPI as $PG_ID => $KPI_NAME){
		$data_count++;

		$RTF->open_line();
		$RTF->set_font($font,12);
		$RTF->color("0");	// 0=BLACK
                /*เพิ่มเติม ความเห็นในส่วนของ "หมายเหตุผู้ประเมิน" (ส่วนที่ 2.1)*/
                $NoteAssessor = "\line หมายเหตุผู้ประเมิน : -";
                if(!empty($ARR_KPI_PG_REMARK[$PG_ID])){
                    $NoteAssessor = "\line หมายเหตุผู้ประเมิน : ".$ARR_KPI_PG_REMARK[$PG_ID];
                }
                
                /**/
		if ($KPI_SCORE_DECIMAL==1) 
			$RTF->cell($RTF->bold(1).convert2thaidigit($data_count).". ".$ARR_KPI[$PG_ID]."\line ผลงานจริง : ".convert2thaidigit($ARR_KPI_RESULT[$PG_ID]).$NoteAssessor.$RTF->bold(0), $heading_width6[0]+($heading_width6[2]*2), "left", "0", "TRBL");
		else
			$RTF->cell($RTF->bold(1).convert2thaidigit($data_count).". ".$ARR_KPI[$PG_ID]."\line ผลงานจริง : ".convert2thaidigit($ARR_KPI_RESULT[$PG_ID]).$NoteAssessor.$RTF->bold(0), $heading_width6[0], "left", "0", "TRBL");
		$RTF->cell($RTF->bold(1).convert2thaidigit($ARR_KPI_WEIGHT[$PG_ID]).$RTF->bold(0), $heading_width6[1], "center", "0", "TRBL");

		$RTF->set_font($font,12);
		$RTF->color("1");	// 1=DARKGRAY
		if ($KPI_SCORE_DECIMAL==1) {	
			$RTF->cell($RTF->bold(1).convert2thaidigit($ARR_KPI_EVALUATE[$PG_ID]).$RTF->bold(0), ($heading_width6[2]*3), "center", "0", "TRBL");
		} else {
			for($i=1; $i<=5; $i++){ 
				if ($i % 2 == 0) $b_col = 0; else $b_col = 0;			
				if($i == $ARR_KPI_EVALUATE[$PG_ID]){
					$RTF->cell($RTF->bold(1).convert2thaidigit(${"ARR_KPI_TARGET".$i}[$PG_ID]).$RTF->bold(0), $heading_width6[2], "center", $b_col, "TRBL");
				}else{
					$RTF->cell("", $heading_width6[2], "center", $b_col, "TRBL");
				} // end if			
			} // end for
		} // end if				
		$RTF->close_line();
	} // end foreach
	
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	if ($KPI_SCORE_DECIMAL==1) 
		$RTF->cell("คะแนนรวม ", $heading_width6[0] + ($heading_width6[2] * 2), "right", "0", "TRBL");
	else
		$RTF->cell("คะแนนรวม ", $heading_width6[0], "right", "0", "TRBL");
	$RTF->color("0");	// 0=BLACK
	$RTF->Cell(convert2thaidigit($TOTAL_KPI_WEIGHT), $heading_width6[1], 'center', 0, "TRBL");
	$RTF->color("1");	// 1=DARKGRAY
	if ($KPI_SCORE_DECIMAL==1) 
		$RTF->Cell(convert2thaidigit($TOTAL_KPI_EVALUATE), ($heading_width6[2] * 3), 'center', 0, "TRBL");
	else
		$RTF->Cell(convert2thaidigit($TOTAL_KPI_EVALUATE), ($heading_width6[2] * 5), 'center', 0, "TRBL");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	if ($KPI_SCORE_DECIMAL==1) 
		$RTF->cell("คะแนนประเมิน ", $heading_width6[0] + ($heading_width6[2] * 2), "right", "0", "TRBL");
	else
		$RTF->cell("คะแนนประเมิน ", $heading_width6[0], "right", "0", "TRBL");
	$RTF->color("0");	// 0=BLACK
	$RTF->Cell("", $heading_width6[1], 'right', 0, "TRBL");
	$RTF->color("1");	// 1=DARKGRAY
	if ($KPI_SCORE_DECIMAL==1) 
		$RTF->Cell(convert2thaidigit(number_format(($TOTAL_KPI_EVALUATE / $TOTAL_KPI_WEIGHT), 2)), ($heading_width6[2] * 3), 'center', 0, "TRBL");
	else
		$RTF->Cell(convert2thaidigit(number_format(($TOTAL_KPI_EVALUATE / $TOTAL_KPI_WEIGHT), 2)), ($heading_width6[2] * 5), 'center', 0, "TRBL");
	$RTF->close_line();
	
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("ผลรวมของคะแนนประเมินของผลสำเร็จของงาน", "100", "left", "0", "");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell($RTF->bold(1)."ผลรวมของคะแนนประเมินผลสำเร็จของงานทั้งหมด".$RTF->bold(0), "60", "left", "0", "");
	$RTF->cell($RTF->bold(1).convert2thaidigit(number_format(($TOTAL_KPI_EVALUATE / $TOTAL_KPI_WEIGHT), 2)).$RTF->bold(0), "10", "center", "36", "TRBL");
	$RTF->cell("", "5", "center", "0", "");
	$RTF->close_line();

	$RTF->display($fname);

	ini_set("max_execution_time", 30);
?>