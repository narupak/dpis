<?php
include("../../php_scripts/connect_database.php");
    include ("../php_scripts/session_start.php");
    include("../../php_scripts/calendar_data.php");
    include ("../php_scripts/function_share.php");
	$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

    require("../../RTF/rtf_class.php");
    
    ini_set("max_execution_time", $max_execution_time); 
	
	$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_ESIGN_ATT' ";
	$db_dpis2->send_cmd($cmd);
	$data = $db_dpis2->get_array();
	$P_ESIGN_ATT = $data[CONFIG_VALUE];
    
    $FONTSIZE12 = "12";
    $FONTSIZE14 = "14";
    $FONTSIZE16 = "16";
    
    if(!$FONTSIZE)$FONTSIZE = $FONTSIZE16;
    if($_GET[REC_ID])$REC_ID = $_GET[REC_ID];
    if($SESS_USERGROUP==1) $number_doc = $SESS_USERGROUP;
    $fname = "T0204_2.rtf";
    if (!$font) $font = "AngsanaUPC";
    
    $RTF = new RTF("a4", 500, 500, 400, 200);//1150,720, 600, 200
    $RTF->set_default_font($font, $FONTSIZE);
    $RTF->set_table_font($font,$FONTSIZE);
    $RTF->color("0");	// 0=BLACK
    
    session_cache_limiter("private");
    session_start();
    
    $db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    
    function EnterNewLine($line){
        global $RTF,$font,$FONTSIZE;
        for($i=0;$i<$line;$i++){
            $RTF->open_line();			
            $RTF->set_font($font,$FONTSIZE);
            $RTF->color("0");	// 0=BLACK
            $RTF->cell(" ", "100", "center", "0");
            $RTF->close_line();
        }
    }
    

    
    $RTF->open_line();
    $RTF->set_font($font, 16);
    $RTF->color("0");	// 0=BLACK
    $RTF->cell($RTF->bold(1).$RTF->underline(1)."แบบฟอร์มขออนุญาตออกก่อนเวลาปฏิบัติราชการ".$RTF->underline(0).$RTF->bold(0).$RTF->color("0"), "100", "center", "0");
    $RTF->close_line();
	
	/*--------------------------------------------------------------------*/
	
	
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
	
	
	$cmd = "	  select 	PER_TYPE,PER_ID,PER_CARDNO,REQUEST_TYPE,SUBMITTED_DATE,REQUEST_DATE,START_FLAG,
                                START_TIME,END_FLAG,END_TIME,MEETING_FLAG,SCAN_FLAG,OTH_FLAG,OTH_NOTE,REQUEST_NOTE,
                                REQ_FLAG,REQ_TIME,REQ_STATUS,UPDATE_USER,UPDATE_DATE,ALLOW_FLAG,
                                ALLOW_USER,ALLOW_DATE,ALLOW_NOTE,APPROVE_FLAG,APPROVE_USER,APPROVE_DATE,APPROVE_NOTE
					  from 		TA_REQUESTTIME
					  where 		REC_ID=$REC_ID";

	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$PER_ID = $data[PER_ID];
	
	$cmd ="select g.PN_NAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW
				from PER_PERSONAL a 
				left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE) 
				where a.PER_ID = $PER_ID ";
	$db->send_cmd($cmd);
	$data2 = $db->get_array();
	$PER_NAME =  trim($data2[FULLNAME_SHOW]);
	
	$PER_CARDNO = $data[PER_CARDNO];
	$PER_TYPE = $data[PER_TYPE];
	
	$cmd = "	select 		a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, 
										b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
										a.PER_CARDNO,  c.ORG_ID AS ORG_ID_P, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
										c.PL_CODE, d.PN_CODE, e.EP_CODE,f.POSITION_LEVEL,a.ORG_ID,j.ORG_ID AS POT_ORG_ID
						  from 		PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, 
						  				PER_POS_EMP d, PER_POS_EMPSER e,PER_LEVEL f,PER_POS_TEMP j
						  where 	a.PN_CODE=b.PN_CODE and a.POS_ID=c.POS_ID(+) 
						  				and a.POEM_ID=d.POEM_ID(+) 
										and a.POEMS_ID=e.POEMS_ID(+)  
										and a.LEVEL_NO=f.LEVEL_NO(+)  
										and a.POT_ID=j.POT_ID(+)  
										and a.PER_ID=$PER_ID ";

	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array();

	if($PER_TYPE==1) 
		$ORG_ID = $data1[ORG_ID_P];
	elseif($PER_TYPE==2) 
		$ORG_ID = $data1[EMP_ORG_ID];
	elseif ($PER_TYPE == 3) 
		$ORG_ID = $data1[EMPS_ORG_ID];
	elseif ($PER_TYPE == 4) 
		$ORG_ID = $data1[POT_ORG_ID];
	
	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$ORG_NAME = $data2[ORG_NAME];
	
	$ORG_ASS_NAME = "";
	$ORG_ASS_ID = trim($data1[ORG_ID]);
	if ($ORG_ASS_ID) {
		$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$ORG_ASS_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_ASS_NAME = $data2[ORG_NAME];
   }

	if($ORG_ID){
		$cmd = " select ORG_ID_REF from PER_ORG where OL_CODE='03' and ORG_ID=$ORG_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DEPARTMENT_ID = $data2[ORG_ID_REF];
	}
	
	if($DEPARTMENT_ID){
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where OL_CODE='02' and ORG_ID=$DEPARTMENT_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DEPARTMENT_NAME = $data2[ORG_NAME];
		$MINISTRY_ID = $data2[ORG_ID_REF];
	}

	if($MINISTRY_ID){
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='01' and ORG_ID=$MINISTRY_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$MINISTRY_NAME = $data2[ORG_NAME];
	}
	
	$arr_tempsubmit = explode("-", $data[SUBMITTED_DATE]);
	$SUBMITTED_DATE = ($arr_tempsubmit[2]+0)." เดือน ". $month_full[($arr_tempsubmit[1] + 0)][TH] ." พ.ศ. ".($arr_tempsubmit[0] + 543)  ;
	
	$SUBMITTED_DATE_S = ($arr_tempsubmit[2]+0)." ". $month_abbr[($arr_tempsubmit[1] + 0)][TH] ." ".($arr_tempsubmit[0] + 543)  ;
	
	$arr_tempreq = explode("-", $data[REQUEST_DATE]);
	$REQUEST_DATE = ($arr_tempreq[2]+0)." เดือน ". $month_full[($arr_tempreq[1] + 0)][TH] ." พ.ศ. ".($arr_tempreq[0] + 543)  ;
	
	if($PER_TYPE==1){ 
		$TMP_PL_CODE = $data1[PL_CODE];

		$cmd = " select PL_NAME from PER_LINE where PL_CODE='$TMP_PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DATA_PL_NAME = $data2[PL_NAME];

	}elseif($PER_TYPE==2){ 
		$TMP_PL_CODE = $data1[PN_CODE];

		$cmd = " select PN_NAME from PER_POS_NAME where PN_CODE='$TMP_PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DATA_PL_NAME = $data2[PN_NAME];

	} elseif ($PER_TYPE == 3) {					
		$TMP_PL_CODE = $data1[EP_CODE];

		$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE='$TMP_PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DATA_PL_NAME = $data2[EP_NAME];

					
	} elseif ($DATA_PER_TYPE == 4) {			
		$TMP_PL_CODE = $data1[TP_CODE];

		$cmd = " select TP_NAME from PER_TEMP_POS_NAME where TP_CODE='$TMP_PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		//echo $cmd;
		$data2 = $db_dpis2->get_array();
		$DATA_PL_NAME = $data2[TP_NAME];

	} // end if
	
	$DATA_LEVEL_NAME = trim($data1[POSITION_LEVEL]);
	$forgate1 = $data[START_FLAG];
	$END_HH_1 = substr($data[START_TIME],0,2);
	$END_II_1 = substr($data[START_TIME],2,2);
	$forgate2 = $data[END_FLAG];
	$END_HH_2 = substr($data[END_TIME],0,2);
	$END_II_2 = substr($data[END_TIME],2,2);
	$MEETING_FLAG = $data[MEETING_FLAG];
	$SCAN_FLAG = $data[SCAN_FLAG];
	$OTH_FLAG = $data[OTH_FLAG];
	$OTH_NOTE = $data[OTH_NOTE];
	$forgate3 = $data[REQ_FLAG];
	$END_HH_3 = substr($data[REQ_TIME],0,2);
	$END_II_3 = substr($data[REQ_TIME],2,2);
	$REQUEST_NOTE = $data[REQUEST_NOTE];
	$REQ_STATUS = $data[REQ_STATUS];
	
	$REVIEW1_PER_ID = $data[ALLOW_USER];
	$cmd ="select g.PN_NAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW,
				a.PER_CARDNO,a.LEVEL_NO, a.PER_TYPE,mg.PM_NAME,
				c.PL_CODE, d.PN_CODE, e.EP_CODE,f.POSITION_LEVEL
				from PER_PERSONAL a 
				left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE)
				left join PER_POSITION c on(c.POS_ID=a.POS_ID)  
				left join PER_MGT mg on(mg.PM_CODE=c.PM_CODE) 
				left join PER_POS_EMP d on(a.POEM_ID=d.POEM_ID) 
				left join PER_POS_EMPSER e on(a.POEMS_ID=e.POEMS_ID) 
				left join PER_LEVEL f on(a.LEVEL_NO=f.LEVEL_NO) 
				where a.PER_ID = $REVIEW1_PER_ID ";
				
	$db->send_cmd($cmd);
	$data_ALLOW = $db->get_array();
	$ALLOW_CARDNO = $data_ALLOW[PER_CARDNO];
	$PIC_SIGN_ALLOW = "";
	
	if($P_ESIGN_ATT=="Y"){
		if($data[ALLOW_DATE]){
			$PIC_SIGN_ALLOW = getPIC_SIGN($REVIEW1_PER_ID,$ALLOW_CARDNO);
		}
	}
	$REVIEW1_PER_NAME = $data_ALLOW[FULLNAME_SHOW];
	$ABS_REVIEW1_FLAG = $data[ALLOW_FLAG];

	$arr_tempdateAllow = explode("-", $data[ALLOW_DATE]);
	$DATA_ALLOW_DATE = ($arr_tempdateAllow[2]+0)." ". $month_abbr[($arr_tempdateAllow[1] + 0)][TH] ." ".($arr_tempdateAllow[0] + 543)  ;
	
	$ALLOW_NOTE = $data[ALLOW_NOTE];
	$ALLOW_PM_NAME = $data_ALLOW[PM_NAME];
	$ALLOW_PER_TYPE = $data_ALLOW[PER_TYPE];
	$ALLOW_POSITION_LEVEL = $data_ALLOW[POSITION_LEVEL];
	
	if($ALLOW_PER_TYPE==1){ 
		$TMP_PL_CODE_ALLOW = $data_ALLOW[PL_CODE];

		$cmd = " select PL_NAME from PER_LINE where PL_CODE='$TMP_PL_CODE_ALLOW' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ALLOW_PL_NAME = $data2[PL_NAME];

	}elseif($ALLOW_PER_TYPE==2){ 
		$TMP_PL_CODE_ALLOW = $data_ALLOW[PN_CODE];

		$cmd = " select PN_NAME from PER_POS_NAME where PN_CODE='$TMP_PL_CODE_ALLOW' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ALLOW_PL_NAME = $data2[PN_NAME];

	} elseif ($ALLOW_PER_TYPE == 3) {					
		$TMP_PL_CODE_ALLOW = $data_ALLOW[EP_CODE];

		$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE='$TMP_PL_CODE_ALLOW' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ALLOW_PL_NAME = $data2[EP_NAME];

					
	} elseif ($ALLOW_PER_TYPE == 4) {			
		$TMP_PL_CODE_ALLOW = $data_ALLOW[TP_CODE];

		$cmd = " select TP_NAME from PER_TEMP_POS_NAME where TP_CODE='$TMP_PL_CODE_ALLOW' ";
		$db_dpis2->send_cmd($cmd);
		//echo $cmd;
		$data2 = $db_dpis2->get_array();
		$ALLOW_PL_NAME = $data2[TP_NAME];

	} // end if
	
	
	
	
	/*-------------------------------------------------------------------------------------------------*/
	$PIC_SIGN_PERSON = "";
	if($P_ESIGN_ATT=="Y"){
		$PIC_SIGN_PERSON = getPIC_SIGN($PER_ID,$PER_CARDNO);
	}
	$APPROVE_PER_ID = $data[APPROVE_USER];
	$cmd ="select g.PN_SHORTNAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW,
				a.PER_CARDNO,a.LEVEL_NO, a.PER_TYPE,mg.PM_NAME,
				c.PL_CODE, d.PN_CODE, e.EP_CODE,f.POSITION_LEVEL,
				c.ORG_ID AS ORG_ID_P, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
				a.ORG_ID
				from PER_PERSONAL a 
				left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE)
				left join PER_POSITION c on(c.POS_ID=a.POS_ID)  
				left join PER_MGT mg on(mg.PM_CODE=c.PM_CODE) 
				left join PER_POS_EMP d on(a.POEM_ID=d.POEM_ID) 
				left join PER_POS_EMPSER e on(a.POEMS_ID=e.POEMS_ID) 
				left join PER_LEVEL f on(a.LEVEL_NO=f.LEVEL_NO) 
				where a.PER_ID = $APPROVE_PER_ID ";
	$db->send_cmd($cmd);
	$dataAP = $db->get_array();
	$APPROVE_CARDNO = $dataAP[PER_CARDNO];
	$PIC_SIGN_APPROVE = "";
	if($P_ESIGN_ATT=="Y"){
		if($data[APPROVE_DATE]){
			$PIC_SIGN_APPROVE = getPIC_SIGN($APPROVE_PER_ID,$APPROVE_CARDNO);
		}
	}
	$APPROVE_PER_NAME = $dataAP[FULLNAME_SHOW];
	$ABS_APPROVE_FLAG = $data[APPROVE_FLAG];
	$arr_tempdateApprove = explode("-", $data[APPROVE_DATE]);
	$DATA_APPROVE_DATE = ($arr_tempdateApprove[2]+0)." ". $month_abbr[($arr_tempdateApprove[1] + 0)][TH] ." ".($arr_tempdateApprove[0] + 543)  ;
	
	$APPROVE_NOTE = $data[APPROVE_NOTE];
	
	$APPROVE_PM_NAME = $dataAP[PM_NAME];
	$APPROVE_PER_TYPE = $dataAP[PER_TYPE];
	$APPROVE_POSITION_LEVEL = $dataAP[POSITION_LEVEL];
	
	if($APPROVE_PER_TYPE==1) 
		$APPROVE_ORG_ID = $dataAP[ORG_ID_P];
	elseif($APPROVE_PER_TYPE==2) 
		$APPROVE_ORG_ID = $dataAP[EMP_ORG_ID];
	elseif ($APPROVE_PER_TYPE == 3) 
		$APPROVE_ORG_ID = $dataAP[EMPS_ORG_ID];
	
	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$APPROVE_ORG_ID ";
	$db_dpis2->send_cmd($cmd);
	$dataAP2 = $db_dpis2->get_array();
	$APPROVE_ORG_NAME = $dataAP2[ORG_NAME];
	
	$APPROVE_ORG_ASS_NAME = "";
	$TMP_APPROVE_ORG_ASS_ID = trim($dataAP[ORG_ID]);
	if ($TMP_APPROVE_ORG_ASS_ID) {
			$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$TMP_APPROVE_ORG_ASS_ID ";
            $db_dpis2->send_cmd($cmd);
            $dataAP2 = $db_dpis2->get_array();
            $APPROVE_ORG_ASS_NAME = $dataAP2[ORG_NAME];
  	}
	
	if($APPROVE_PER_TYPE==1){ 
		$TMP_PL_CODE_APPROVE = $dataAP[PL_CODE];

		$cmd = " select PL_NAME from PER_LINE where PL_CODE='$TMP_PL_CODE_APPROVE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$APPROVE_PL_NAME = $data2[PL_NAME];

	}elseif($APPROVE_PER_TYPE==2){ 
		$TMP_PL_CODE_APPROVE = $dataAP[PN_CODE];

		$cmd = " select PN_NAME from PER_POS_NAME where PN_CODE='$TMP_PL_CODE_APPROVE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$APPROVE_PL_NAME = $data2[PN_NAME];

	} elseif ($APPROVE_PER_TYPE == 3) {					
		$TMP_PL_CODE_APPROVE = $dataAP[EP_CODE];

		$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE='$TMP_PL_CODE_APPROVE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$APPROVE_PL_NAME = $data2[EP_NAME];

					
	} elseif ($APPROVE_PER_TYPE == 4) {			
		$TMP_PL_CODE_APPROVE = $dataAP[TP_CODE];

		$cmd = " select TP_NAME from PER_TEMP_POS_NAME where TP_CODE='$TMP_PL_CODE_APPROVE' ";
		$db_dpis2->send_cmd($cmd);
		//echo $cmd;
		$data2 = $db_dpis2->get_array();
		$APPROVE_PL_NAME = $data2[TP_NAME];

	} // end if
	
	/*----------------------------------------------------------------------------------*/
	
	/*ดูตามมอบหมายงานก่อนถ้าไม่มีค่อยดูที่กฏหมาย*/
	if($APPROVE_ORG_NAME){
		$SHOW_APPROVE_ORG_NAME = $APPROVE_ORG_NAME;
	}else{
		$SHOW_APPROVE_ORG_NAME = $APPROVE_ORG_ASS_NAME;
	}
	
	
	//--------------------------------------------------------
	
	/*เขียนที่ */
	if($ORG_NAME){
		$SHOW_ORG_NAME = $ORG_NAME;
		
	}else{
		$SHOW_ORG_NAME = $ORG_ASS_NAME;
	}
	
	//---------------------------------------------------------
	
	/*ตำแหน่งผู้อนุมัติ*/
	
	if(!empty($APPROVE_PM_NAME)){ 
            $valAPPROVE_PL_NAME = $APPROVE_PM_NAME;
        }else{
			 $valAPPROVE_PL_NAME = $APPROVE_PL_NAME.$APPROVE_POSITION_LEVEL;
	}
	//---------------------------------------------------------
    
    $SUBMITTED_DATE1="วันที่ ".$SUBMITTED_DATE;
    $RTF->open_line();			
    $RTF->set_font($font,$FONTSIZE);
    $RTF->cell($RTF->color("0").$RTF->bold(0).convert2rtfascii($SUBMITTED_DATE1).$RTF->bold(0), "100", "right", "0");
    $RTF->close_line();
    
    $PER_NAME="".$PER_NAME;
    $PL_NAME="".$DATA_PL_NAME;
    $LEVEL_NAME="".$DATA_LEVEL_NAME ;
    $SHOW_ORG_NAME="".$SHOW_ORG_NAME;
    $RTF->open_line();			
    $RTF->set_font($font,$FONTSIZE);
    $RTF->color("0");	// 0=BLACK
    $RTF->cell("ข้าพเจ้า ".convert2rtfascii($PER_NAME)." ตำแหน่ง ".convert2rtfascii($PL_NAME)."".convert2rtfascii($DATA_LEVEL_NAME)." สังกัด ".convert2rtfascii($SHOW_ORG_NAME)." ขออนุญาตออกก่อนเวลาปฏิบัติราชการ เนื่องจาก".convert2rtfascii($REQUEST_NOTE." โดยขอออกก่อนเวลา ในวันที่ ".$REQUEST_DATE." ตั้งแต่เวลา ".$END_HH_3.":".$END_II_3." น."), "100", "left", "0");  
    $RTF->close_line();
    
	/*$RTF->open_line();			
    $RTF->set_font($font,$FONTSIZE);
    $RTF->color("0");	// 0=BLACK
    $RTF->cell("สังกัด ".convert2rtfascii($SHOW_ORG_NAME." ขออนุญาตออกก่อนเวลาปฏิบัติราชการ เนื่องจาก"), "100", "left", "0");  
    $RTF->close_line();*/
    
    /*$RTF->open_line();			
    $RTF->set_font($font,$FONTSIZE);
    $RTF->color("0");	// 0=BLACK
	if($REQUEST_NOTE){
			$RTF->cell(convert2rtfascii($REQUEST_NOTE." โดยขอออกก่อนเวลา ในวันที่ ".$REQUEST_DATE." ตั้งแต่เวลา ".$END_HH_3.":".$END_II_3." น."), "100", "left", "0");  
			$RTF->close_line();
	}else{
			$RTF->cell(str_repeat(".", 207), "100", "left", "0"); 
			$RTF->close_line();
			
			$RTF->open_line();			
			$RTF->set_font($font,$FONTSIZE);
			$RTF->color("0");	// 0=BLACK
			$RTF->cell(convert2rtfascii(str_repeat(".", 150)." โดยขอออกก่อนเวลา"), "100", "left", "0"); 
			
			$RTF->close_line();
			$COMMENT ="ในวันที่ ".$REQUEST_DATE." ตั้งแต่เวลา ".$END_HH_3.":".$END_II_3." น.";
			$RTF->open_line();			
			$RTF->set_font($font,$FONTSIZE);
			$RTF->color("0");	// 0=BLACK
			$RTF->cell(convert2rtfascii($COMMENT), "100", "left", "0");  
			$RTF->close_line();
	}*/
    
	
    $RTF->open_line();			
    $RTF->set_font($font,$FONTSIZE);
    $RTF->color("0");	// 0=BLACK
    $RTF->cell("", "50", "left", "0");
    if($PIC_SIGN_PERSON){
		$RTF->color("0");	// 0=BLACK
		$RTF->cellImagexy($PIC_SIGN_PERSON, 20, 20, 50, "center", 0);
	}else{
		$RTF->color("1");	// 1=DARKGRAY
		$RTF->cell(convert2rtfascii("(ลงชื่อ) ". str_repeat(".", 45)), "50", "center", "0");
	}
    $RTF->close_line();
	
    $RTF->open_line();			
    $RTF->set_font($font,$FONTSIZE);
    $RTF->color("0");	// 0=BLACK
    $RTF->cell("", "50", "left", "0");
    $RTF->cell(convert2rtfascii("(".$PER_NAME .")"), "50", "center", "0"); 
    $RTF->close_line();
    
    EnterNewLine(1);
    
	$heading_width1 = "50";
	//Row 1
	$RTF->open_line();
	$RTF->set_font($font, $FONTSIZE);
	$RTF->color("0");// 0='BLACK'
	$RTF->cell($RTF->bold(1).$RTF->underline(1)."ความเห็นผู้บังคับบัญชาระดับต้น".$RTF->underline(0).$RTF->bold(0), $heading_width1, "left", "50", "TRL");	
	$RTF->cell($RTF->bold(1).$RTF->underline(1)."คำสั่ง".$RTF->underline(0).$RTF->bold(0)." (ผอ.สำนัก/สถาบัน/ศูนย์)", $heading_width1, "left", "50", "TRL");
	$RTF->close_line();
	
	//Row 2	
	$RTF->open_line();
	$RTF->set_font($font, $FONTSIZE);
	$RTF->color("0");// 0='BLACK'
	$RTF->cell("", "5", "left", "0", "L");
	if($ABS_REVIEW1_FLAG==1){
			$ABS_REVIEW1="checkbox_check";
			$ABS_REVIEW2="checkbox_blank";
		}elseif($ABS_REVIEW1_FLAG==2){
			$ABS_REVIEW1="checkbox_blank";
			$ABS_REVIEW2="checkbox_check";
		}else{
			$ABS_REVIEW1="checkbox_blank";
			$ABS_REVIEW2="checkbox_blank";
		}
	$RTF->cellImage("../images/".$ABS_REVIEW1.".jpg", 100, 2.7, "center", "");
	$RTF->cell("เห็นควรอนุญาต", "15.3", "left", "0", "");
	$RTF->cellImage("../images/".$ABS_REVIEW2.".jpg", 100, 2.7, "center", "");
	$RTF->cell("ไม่เห็นควรอนุญาต", "20.3", "left", "0", "");
	
	if($ABS_APPROVE_FLAG==1){
			$ABS_APPROVE1="checkbox_check";
			$ABS_APPROVE2="checkbox_blank";
		}elseif($ABS_APPROVE_FLAG==2){
			$ABS_APPROVE1="checkbox_blank";
			$ABS_APPROVE2="checkbox_check";
		}else{
			$ABS_APPROVE1="checkbox_blank";
			$ABS_APPROVE2="checkbox_blank";
		}
	$RTF->cell("", "4", "left", "0", "R");
	$RTF->cell("", "12", "left", "0", "");
	$RTF->cellImage("../images/".$ABS_APPROVE1.".jpg", 100, 2.7, "center", "");
	$RTF->cell("อนุญาต", "10.3", "left", "0", "");
	$RTF->cellImage("../images/".$ABS_APPROVE2.".jpg", 100, 2.7, "center", "");
	$RTF->cell("ไม่อนุญาต", "22.3", "left", "0", "R");
	$RTF->close_line();
	
	//Row 3
	$RTF->open_line();
	$RTF->set_font($font, $FONTSIZE);
	$RTF->color("0");//0='BLACK'
	if($PIC_SIGN_ALLOW){
		$RTF->color("0");	// 0=BLACK
		$RTF->cellImagexy($PIC_SIGN_ALLOW, 20, 20, 50, "center", 0, "RL");
	}else{
		$RTF->color("1");	// 1=DARKGRAY
		$RTF->cell(convert2rtfascii("ลงชื่อ ". str_repeat(".", 70)), "50", "center", "0", "RL");
	}
	
	if($PIC_SIGN_APPROVE){
		$RTF->color("0");	// 0=BLACK
		$RTF->cellImagexy($PIC_SIGN_APPROVE, 20, 20, 50, "center", 0,"R");
	}else{
		$RTF->color("1");	// 1=DARKGRAY
		$RTF->cell(convert2rtfascii("ลงชื่อ ". str_repeat(".", 70)), "50", "center", "0","R");
	} 
	
	$RTF->close_line();
	
	//Row 4
	$RTF->open_line();
	$RTF->set_font($font, $FONTSIZE);
	$RTF->color("0");//0='BLACK'
	if($REVIEW1_PER_NAME){
		$RTF->cell(convert2rtfascii("(".$REVIEW1_PER_NAME.")"), "50", "center", "0", "RL");
	}else{
		$RTF->cell(convert2rtfascii("(".str_repeat(".", 77).")"), "50", "center", "0", "RL");
	}
	
	if($APPROVE_PER_NAME){
		$RTF->cell(convert2rtfascii("(".$APPROVE_PER_NAME.")"), "50", "center", "0", "R");
	}else{
		$RTF->cell(convert2rtfascii("(".str_repeat(".", 77).")"), "50", "center", "0", "R");
	}
	
	$RTF->close_line();
	
	//Row 5
	$RTF->open_line();
	$RTF->set_font($font, $FONTSIZE);
	$RTF->color("0");//0='BLACK'
	if(!empty($ALLOW_PM_NAME)){ 
            $valALLOW_PM_NAME = $ALLOW_PM_NAME;
        }else{
            $valALLOW_PM_NAME = $ALLOW_PL_NAME.$ALLOW_POSITION_LEVEL;
	}
	if($valALLOW_PM_NAME){
		$RTF->cell(convert2rtfascii("(ตำแหน่ง) ".$valALLOW_PM_NAME), "50", "center", "0", "RL");
	}else{
		$RTF->cell(convert2rtfascii("(ตำแหน่ง) ".str_repeat(".", 67)), "50", "center", "0", "RL");
	}
	

	if($valAPPROVE_PL_NAME){
		$RTF->cell(convert2rtfascii("(ตำแหน่ง) ".$valAPPROVE_PL_NAME), "50", "center", "0", "R");
	}else{
		$RTF->cell(convert2rtfascii("(ตำแหน่ง) ".str_repeat(".", 67)), "50", "center", "0", "R");
	}

	$RTF->close_line();
	
	//Row 6
	
	$RTF->open_line();
	$RTF->set_font($font, $FONTSIZE);
	$RTF->color("0");//0='BLACK'
	$RTF->cell("", "50", "center", "0", "RLB");
	$RTF->cell("", "50", "center", "0", "RB");
	$RTF->close_line();


    $RTF->display($fname);
    ini_set("max_execution_time", 30);    
?>
