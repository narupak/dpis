<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

		if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
		require("../../RTF/rtf_class.php");
	} else	 {
		define('FPDF_FONTPATH','../../PDF/font/');
		include ("../../PDF/fpdf.php");
		include ("../../PDF/pdf_extends_DPIS.php");
	}

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select		COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
									a.COM_TYPE, COM_CONFIRM, COM_LEVEL_SALP, a.DEPARTMENT_ID, b.COM_DESC, b.COM_NAME as COM_TYPE_NAME
					 from		PER_COMMAND a, PER_COMTYPE b
					 where	COM_ID=$COM_ID and a.COM_TYPE=b.COM_TYPE ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
		
	$COM_NO = trim($data[COM_NO]);
	$COM_NAME = trim($data[COM_NAME]);
	$COM_DATE = $data[COM_DATE];
	$COM_DATE = show_date_format($COM_DATE,5);
	$COM_NOTE = trim($data[COM_NOTE]);
	$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
	$COM_CONFIRM = trim($data[COM_CONFIRM]);
	$COM_LEVEL_SALP = $data[COM_LEVEL_SALP];
	$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
		
	$COM_TYPE = trim($data[COM_TYPE]);
	$COM_DESC = str_replace("คำสั่ง", "", $data[COM_DESC]);
	$COM_TYPE_NAME = trim($data[COM_TYPE_NAME]);

	if($COM_PER_TYPE == 1){
		$position_table = "PER_POSITION";
		$position_join = "b.POS_ID=c.POS_ID";
	}elseif($COM_PER_TYPE == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "b.POEM_ID=c.POEM_ID";
	}elseif($COM_PER_TYPE == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "b.POEMS_ID=c.POEMS_ID";
	}elseif($COM_PER_TYPE == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "b.POT_ID=c.POT_ID";
	} // end if

	$cmd = " select	CMD_DATE from	PER_COMDTL where	COM_ID=$COM_ID ";
	$db_dpis->send_cmd($cmd);
//	echo "cmd=$cmd<br>";
	$data = $db_dpis->get_array();
	$CMD_DATE = $data[CMD_DATE];
	if (substr($CMD_DATE,4,6) == "-04-01") {
		$search_kf_cycle = 2;
		$KF_START_DATE = substr($CMD_DATE,0,4) . "-04-01";
		$KF_END_DATE = substr($CMD_DATE,0,4) . "-09-30";
	} elseif (substr($CMD_DATE,4,6) == "-10-01") {
		$search_kf_cycle = 1;
		$KF_START_DATE = (substr($CMD_DATE,0,4)-1) . "-10-01";
		if($COM_PER_TYPE == 1) $KF_END_DATE = substr($CMD_DATE,0,4) . "-03-31";
		elseif($COM_PER_TYPE == 3) $KF_END_DATE = substr($CMD_DATE,0,4) . "-09-30";
	}

	if ($print_order_by==1) $order_str = "a.CMD_SEQ";
	else 
		if($DPISDB=="odbc") $order_str = "g.ORG_SEQ_NO, g.ORG_CODE, d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_SEQ_NO, CMD_POS_NO_NAME, CLng(CMD_POS_NO)";
		elseif($DPISDB=="oci8")  $order_str = "nvl(g.ORG_SEQ_NO,0), g.ORG_CODE, nvl(d.ORG_SEQ_NO,0), d.ORG_CODE, nvl(e.ORG_SEQ_NO,0), nvl(e.ORG_CODE,d.ORG_CODE), nvl(f.ORG_SEQ_NO,0), nvl(f.ORG_CODE, nvl(e.ORG_CODE,d.ORG_CODE)), CMD_POS_NO_NAME, to_number(replace(CMD_POS_NO,'-',''))";
		elseif($DPISDB=="mysql")  $order_str = "g.ORG_SEQ_NO, g.ORG_CODE, d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_SEQ_NO , CMD_POS_NO_NAME, CMD_POS_NO+0";

	if ($BKK_FLAG==1)
		$report_title = "บัญชีรายละเอียดการปรับอัตราเงินเดือน$PERSON_TYPE[$COM_PER_TYPE]||แนบท้ายคำสั่ง $COM_NO ลงวันที่ $COM_DATE";                     
	else
		$report_title = "$COM_TYPE_NAME||บัญชีรายละเอียดการ$COM_NAME||แนบท้ายคำสั่ง $DEPARTMENT_NAME ที่ $COM_NO ลงวันที่ $COM_DATE";                     
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "P0406";
	

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	if ($FLAG_RTF) {
//		$sum_w = array_sum($heading_width);
		$sum_w = 0;
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1)
				$sum_w += $heading_width[$h];
		}
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1)
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}
		$fname= "rpt_data_salpromote_comdtlN.rtf";
	//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
		$paper_size="a4";
		$orientation='P';
		$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);
		$RTF->set_default_font($font, 14);
	//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

		$RTF->set_report_code($report_code);
		$RTF->set_report_title($report_title);
		$RTF->set_company_name($company_name);
		} else {
    $unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$orientation='L';
	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	$pdf->SetAutoPageBreak(true,10);
	}
	include ("rpt_data_salpromote_comdtlN_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
 
	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
								a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
								a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY, a.CMD_SPSALARY,
								a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_PERCENT, a.CMD_POS_NO_NAME, a.CMD_POS_NO, a.MOV_CODE
				   from		(
									(
										(
											(
												(
													PER_COMDTL a 
													inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
												) left join $position_table c on ($position_join)
											) left join PER_ORG d on (c.ORG_ID=d.ORG_ID)
										) left join PER_ORG e on (c.ORG_ID_1=e.ORG_ID)
									) left join PER_ORG f on (c.ORG_ID_2=f.ORG_ID)
								) left join PER_ORG g on (b.DEPARTMENT_ID=g.ORG_ID)
				   where		a.COM_ID=$COM_ID
				   order by 	$order_str ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select		a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
							a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL,
							a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY, a.CMD_SPSALARY,
							a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_PERCENT, a.CMD_POS_NO_NAME, a.CMD_POS_NO, a.MOV_CODE
			   from			PER_COMDTL a, PER_PERSONAL b, $position_table c, PER_ORG d, PER_ORG e, PER_ORG f, PER_ORG g
			   where			a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID and $position_join(+) and c.ORG_ID=d.ORG_ID(+) and 
									c.ORG_ID_1=e.ORG_ID(+) and c.ORG_ID_2=f.ORG_ID(+) and b.DEPARTMENT_ID=g.ORG_ID
			   order by 		$order_str ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
								a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
								a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY, a.CMD_SPSALARY,
								a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_PERCENT, a.CMD_POS_NO_NAME, a.CMD_POS_NO, a.MOV_CODE
				   from		(
									(
										(
											(
												(
													PER_COMDTL a 
													inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
												) left join $position_table c on ($position_join)
											) left join PER_ORG d on (c.ORG_ID=d.ORG_ID)
										) left join PER_ORG e on (c.ORG_ID_1=e.ORG_ID)
									) left join PER_ORG f on (c.ORG_ID_2=f.ORG_ID)
								) left join PER_ORG g on (b.DEPARTMENT_ID=g.ORG_ID)
				   where		a.COM_ID=$COM_ID
				   order by 	$order_str ";
	} // end if
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	$CMD_ORG3 = $CMD_ORG4 = -1;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$PER_ID = $data[PER_ID];
		$PN_CODE = trim($data[PN_CODE]);
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PN_NAME = $data2[PN_NAME];		
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		if ($CARDNO_FLAG==1) $PER_CARDNO = $data[PER_CARDNO];
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$CMD_DATE_DISPLAY);
		$PER_TYPE = $data[PER_TYPE];
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$CMD_LEVEL = trim($data[CMD_LEVEL]);
		$CMD_POS_NO_NAME = trim($data[CMD_POS_NO_NAME]); 
		$CMD_POS_NO = trim($data[CMD_POS_NO]); 
		if ($print_order_by==1) {
			$CMD_ORG3 = $data[CMD_ORG3];
			$CMD_ORG4 = $data[CMD_ORG4];
			$CMD_ORG5 = $data[CMD_ORG5];
		}
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$POS_ID = $data[POS_ID];
		$POEM_ID = $data[POEM_ID];
		$POEMS_ID = $data[POEMS_ID];
		$POT_ID = $data[POT_ID];
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_SALARY = $data[CMD_SALARY];
		$CMD_DIFF = $CMD_SALARY - $CMD_OLD_SALARY;
		$CMD_SPSALARY = $data[CMD_SPSALARY];
		$CMD_DATE = trim($data[CMD_DATE]);
		$CMD_NOTE1 = trim($data[CMD_NOTE1]);
		$CMD_NOTE2 = trim($data[CMD_NOTE2]);
		$CMD_PERCENT = $data[CMD_PERCENT];
		$CMD_STEP = "";
		if ($CMD_NOTE1 == "ข้อ 7 กฎ ก.พ.ว่าด้วยการเลื่อนขั้นเงินเดือน พ.ศ.2544")
			$CMD_STEP = "0.5 ขั้น";
		elseif ($CMD_NOTE1 == "ข้อ 8 กฎ ก.พ.ว่าด้วยการเลื่อนขั้นเงินเดือน พ.ศ.2544" || 
			$CMD_NOTE1 == "ข้อ 9 ระเบียบกระทรวงการคลังว่าด้วยการเลื่อนขั้นเงินเดือน พ.ศ.2544")
			$CMD_STEP = "1 ขั้น";
		elseif ($CMD_NOTE1 == "ข้อ 14 กฎ ก.พ.ว่าด้วยการเลื่อนขั้นเงินเดือน พ.ศ.2544")
			$CMD_STEP = "1.5 ขั้น";
		if ($CMD_PERCENT > 0) $CMD_STEP = $CMD_PERCENT;

		$cmd = "select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$CMD_LEVEL' ";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$TYPE_NAME =  $data2[POSITION_TYPE];
		$LEVEL_NAME = $data2[POSITION_LEVEL];

		$cmd = " 	select SAH_SALARY_EXTRA from PER_SALARYHIS 
						where PER_ID=$PER_ID and SAH_EFFECTIVEDATE='2014-10-01'
						order by SAH_SALARY_EXTRA desc ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$SAH_SALARY_EXTRA = $data2[SAH_SALARY_EXTRA] + 0;

		$TMP_SALARY_EXTRA = $CMD_SALARY4 = "";
		if ($CMD_NOTE1) {
			$EXTRA_FLAG = 1;
			$arr_temp = explode("เงินตอบแทนพิเศษ ", $CMD_NOTE1);
			if ($arr_temp[1] > "0.00") $SAH_SALARY_EXTRA = str_replace(",","",$arr_temp[1])+0;
			$TMP_SALARY_EXTRA = (ceil($SAH_SALARY_EXTRA/10))*10 ;
		}
		$CMD_SALARY_EXTRA = $CMD_OLD_SALARY  + $TMP_SALARY_EXTRA;
		if ($CMD_LEVEL=="O1" || $CMD_LEVEL=="O2" || $CMD_LEVEL=="K1" || $CMD_LEVEL=="K2") {
			$CMD_SALARY4 = (ceil($CMD_SALARY_EXTRA * (4/100)  /10))*10;
		}

		if (substr($CMD_DATE,4,6) == "-04-01") {
			$search_kf_cycle = 1;
			$KF_START_DATE = (substr($CMD_DATE,0,4)-1) . "-10-01";
			if($COM_PER_TYPE == 1) $KF_END_DATE = substr($CMD_DATE,0,4) . "-03-31";
			elseif($COM_PER_TYPE == 3) $KF_END_DATE = substr($CMD_DATE,0,4) . "-09-30";
		} elseif (substr($CMD_DATE,4,6) == "-10-01") {
			$search_kf_cycle = 2;
			$KF_START_DATE = substr($CMD_DATE,0,4) . "-04-01";
			$KF_END_DATE = substr($CMD_DATE,0,4) . "-09-30";
		}

		if ($BKK_FLAG==1) {
			$MOV_CODE = trim($data[MOV_CODE]);
			$cmd = "select MOV_NAME from PER_MOVMENT where MOV_CODE='$MOV_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2= $db_dpis2->get_array();
			$MOV_NAME =  trim($data2[MOV_NAME]);
			if ($MOV_NAME=="เลื่อนขั้น 0.5 ขั้น" && $search_kf_cycle == 1) $CMD_NOTE1 = "ครึ่งขั้น";
			elseif ($MOV_NAME=="เลื่อนขั้น 1 ขั้น" && $search_kf_cycle == 2) $CMD_NOTE1 = "หนึ่งขั้น";
			elseif ($MOV_NAME=="เลื่อนขั้น 1.5 ขั้น") $CMD_NOTE1 = "หนึ่งขั้นครึ่ง";
			elseif ($MOV_NAME=="เลื่อนขั้น 2 ขั้น") $CMD_NOTE1 = "สองขั้น";
			elseif (substr($MOV_NAME,0,12)=="ไม่ได้เลื่อน") $CMD_NOTE1 = $MOV_NAME;
		}

		//ข้อมุลการศึกษา
		$cmd = "	select		b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME, a.EDU_ENDDATE, EDU_INSTITUTE, EDU_HONOR, CT_CODE_EDU
								from		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d
								where		a.PER_ID = $PER_ID and  EDU_TYPE like '%2%' 
								and a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and a.INS_CODE=d.INS_CODE(+) ";
		$db_dpis2->send_cmd($cmd);
//		echo "->".$cmd;
//		$db_dpis->show_error();
		$data2 = $db_dpis2->get_array();
		$EN_NAME = trim($data2[EN_NAME]);
		if (!trim($data2[EN_SHORTNAME])) {
			$EN_SHORTNAME = trim($data2[EN_NAME]);
		} else {
			$EN_SHORTNAME = trim($data2[EN_SHORTNAME]);
		}
		if (trim($data2[EM_NAME])) {
			$EM_NAME = "(".trim($data2[EM_NAME]).")";
		}
		$INS_NAME = trim($data2[INS_NAME]);
		if (!$INS_NAME) $INS_NAME = trim($data2[EDU_INSTITUTE]);
		$EDU_ENDDATE = show_date_format($data2[EDU_ENDDATE],$CMD_DATE_DISPLAY);
		$EDU_HONOR = trim($data2[EDU_HONOR]);
		if ($EDU_HONOR && strpos($EDUCATION_NAME,"เกียรตินิยม") !== true) $EDU_HONOR = "เกียรตินิยม" . $EDU_HONOR;
		$CT_CODE_EDU = trim($data2[CT_CODE_EDU]);
		$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE_EDU' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CT_NAME = $data2[CT_NAME];
		if ($CT_NAME=="ไทย") $CT_NAME = "";
		
		if($PER_TYPE==1){
			$cmd = " select TOTAL_SCORE, SALARY_REMARK1 from PER_KPI_FORM where PER_ID = $PER_ID and KF_CYCLE = $search_kf_cycle and 
							KF_START_DATE = '$KF_START_DATE'  and  KF_END_DATE = '$KF_END_DATE'  ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TOTAL_SCORE = $data2[TOTAL_SCORE] + 0; 		
			$TOTAL_SCORE = $TOTAL_SCORE?number_format($TOTAL_SCORE,2):"-";
			if (!$CMD_NOTE1) $CMD_NOTE1 = trim($data2[SALARY_REMARK1]);
		}elseif($PER_TYPE==3){
			$TOTAL_SCORE = "";
			$cmd = " select TOTAL_SCORE from PER_KPI_FORM where PER_ID = $PER_ID and 
							KF_START_DATE >= '$KF_START_DATE'  and  KF_END_DATE <= '$KF_END_DATE'
							order by KF_CYCLE ";
			$db_dpis2->send_cmd($cmd);
			while($data2 = $db_dpis2->get_array()){
				$TEMP_TOTAL_SCORE = $data2[TOTAL_SCORE] + 0; 			 
				$TEMP_TOTAL_SCORE = $TEMP_TOTAL_SCORE?number_format($TEMP_TOTAL_SCORE,2):"-";
				if ($TOTAL_SCORE) $TOTAL_SCORE .= $TEMP_TOTAL_SCORE;
				else $TOTAL_SCORE = $TEMP_TOTAL_SCORE.","; 			 
			}
		}

		if($PER_TYPE==1){
			$cmd = " select PL_CODE, PM_CODE, PT_CODE from PER_POSITION where POS_ID=$POS_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_CODE = trim($data2[PT_CODE]);
			$CMD_PM_CODE = trim($data2[PM_CODE]);
			$CMD_PL_CODE = trim($data2[PL_CODE]);
			
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$CMD_PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_NAME = trim($data2[PT_NAME]);

			if(!$CMD_PM_NAME){
				$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$CMD_PM_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$CMD_PM_NAME = trim($data2[PM_NAME]);
			}

			$CMD_POSITION = pl_name_format($CMD_POSITION, $CMD_PM_NAME, $CMD_PT_NAME, $CMD_LEVEL);

			$cmd = " select LAYER_TYPE from PER_LINE where PL_CODE = '$CMD_PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LAYER_TYPE = $data2[LAYER_TYPE] + 0;
	
			$cmd = " select LAYER_SALARY_MAX, LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, 
				 LAYER_SALARY_FULL, LAYER_EXTRA_MIDPOINT,LAYER_EXTRA_MIDPOINT1,LAYER_EXTRA_MIDPOINT2
				 from PER_LAYER where LAYER_TYPE = 0 and LEVEL_NO = '$LEVEL_NO' and LAYER_NO = 0 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
                        
                         /* Release 5.2.1.20 http://dpis.ocsc.go.th/Service/node/1918*/ 
                        // เดิม if ($LAYER_TYPE==1 && ($LEVEL_NO == "O3" || $LEVEL_NO == "K5") && $CMD_OLD_SALARY <= $data2[LAYER_SALARY_FULL]) {
			if ($LAYER_TYPE==1 && ($LEVEL_NO == "O3" || $LEVEL_NO == "K5") && $CMD_OLD_SALARY < $data2[LAYER_SALARY_FULL]) {
				$LAYER_SALARY_MAX = $data2[LAYER_SALARY_FULL];
				$SALARY_POINT_MID = $data2[LAYER_EXTRA_MIDPOINT];
				$SALARY_POINT_MID1 = $data2[LAYER_EXTRA_MIDPOINT1];
				$SALARY_POINT_MID2 = $data2[LAYER_EXTRA_MIDPOINT2];
			} else {
				$LAYER_SALARY_MAX = $data2[LAYER_SALARY_MAX];
				$SALARY_POINT_MID = $data2[LAYER_SALARY_MIDPOINT];
				$SALARY_POINT_MID1 = $data2[LAYER_SALARY_MIDPOINT1];
				$SALARY_POINT_MID2 = $data2[LAYER_SALARY_MIDPOINT2];
			}

			if($SALARY_POINT_MID > $CMD_OLD_SALARY) {
				$TMP_MIDPOINT = $SALARY_POINT_MID1;
			} else {
				$TMP_MIDPOINT = $SALARY_POINT_MID2;
			}
			$CMD_SALARY_MAX = $LAYER_SALARY_MAX;
		}elseif($PER_TYPE==2){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $LEVEL_NAME):"";
		}elseif($PER_TYPE==3){
//			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";
			$TMP_MIDPOINT = $CMD_OLD_SALARY;
		} elseif($PER_TYPE==4){
//			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";
			$TMP_MIDPOINT = $CMD_OLD_SALARY;
		} // end if

		if ($CARDNO_FLAG==1) $cardID = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
		if($DPISDB=="mysql")	{
			$tmp_data = explode("|", trim($data[CMD_POSITION]));
		}else{
			$tmp_data = explode("\|", trim($data[CMD_POSITION]));
		}
		//ในกรณีที่มี CMD_PM_NAME
		if(is_array($tmp_data)){
			$CMD_POSITION = $tmp_data[0];
			$CMD_PM_NAME = $tmp_data[1];
		}else{
			$CMD_POSITION = $data[CMD_POSITION];
		}
		if ($CMD_POSITION==$CMD_PM_NAME) $CMD_PM_NAME = "";
		if ($RPT_N){
			if($PER_TYPE==1)
				$CMD_POSITION = (trim($CMD_PM_NAME) ?"$CMD_PM_NAME (":"") . (trim($CMD_POSITION)? "$CMD_POSITION$LEVEL_NAME" : "") . (trim($CMD_PM_NAME) ?")":"");
		}else{
			$CMD_POSITION = (trim($CMD_PM_NAME) ?"$CMD_PM_NAME (":"") . (trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL) . (($CMD_PT_NAME != "ทั่วไป" && $CMD_LEVEL >= 6)?"$CMD_PT_NAME":"")):"") . (trim($CMD_PM_NAME) ?")":"");
		}

		if ($print_order_by==2) {
			if($CMD_ORG3 != trim($data[CMD_ORG3]) && trim($data[CMD_ORG3]) && trim($data[CMD_ORG3]) != "-"){
				$CMD_ORG3 = trim($data[CMD_ORG3]);

				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][org_name] = $CMD_ORG3;
				$arr_content[$data_count][new_org_name] = $NEW_ORG_NAME;

				$data_count++;
			} // end if
		
			if($CMD_ORG4 != trim($data[CMD_ORG4]) && trim($data[CMD_ORG4]) && trim($data[CMD_ORG4]) != "-"){
				$CMD_ORG4 = trim($data[CMD_ORG4]);
	
				$arr_content[$data_count][type] = "ORG_1";
				$arr_content[$data_count][org_name] = $CMD_ORG4;

				$data_count++;
			} // end if

			if($CMD_ORG5 != trim($data[CMD_ORG5]) && trim($data[CMD_ORG5]) && trim($data[CMD_ORG5]) != "-"){
				$CMD_ORG5 = trim($data[CMD_ORG5]);

				$arr_content[$data_count][type] = "ORG_2";
				$arr_content[$data_count][org_name] = $CMD_ORG5;

				$data_count++;
			} // end if
		} // end if
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][cmd_pos_no_name] = $CMD_POS_NO_NAME;
		$arr_content[$data_count][cmd_pos_no] = $CMD_POS_NO;
		$CMD_ORG3 = $data[CMD_ORG3];
		$CMD_ORG4 = $data[CMD_ORG4];
		$arr_content[$data_count][cmd_position] = $CMD_POSITION;
		$arr_content[$data_count][cmd_level] = "ท." . level_no_format($CMD_LEVEL);
		$arr_content[$data_count][cmd_level_name] =$CMD_LEVEL_NAME;
		$arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY;
		
		$arr_content[$data_count][cmd_salary] = $CMD_SALARY;
		$arr_content[$data_count][cmd_salary_max] = $CMD_SALARY_MAX;
		$arr_content[$data_count][cmd_spsalary] = $CMD_SPSALARY;
		$arr_content[$data_count][cmd_diff] = $CMD_DIFF;
		$arr_content[$data_count][sah_salary_extra] = $SAH_SALARY_EXTRA;
		
		$arr_content[$data_count][cmd_note1] = $EN_NAME;
		$arr_content[$data_count][cmd_step] = $CMD_STEP;
		
		if ($CARDNO_FLAG==1) $arr_content[$data_count][cardno] = "*Enter* (".(($NUMBER_DISPLAY==2)?convert2thaidigit($cardID):$cardID).")"; //ถ้าเลือกโชวเลขบัตรหลังชื่อ
		$arr_content[$data_count][type_name] = $TYPE_NAME;
		$arr_content[$data_count][level_name] = $LEVEL_NAME;
		$arr_content[$data_count][cmd_midpoint] = $TMP_MIDPOINT;
		$arr_content[$data_count][total_score] = $TOTAL_SCORE;
		if ($print_order_by==1) {
			$data_count++;
			$arr_content[$data_count][type] = "CONTENT";
			if ($CARDNO_FLAG==1) $arr_content[$data_count][name] = "*Enter* (".(($NUMBER_DISPLAY==2)?convert2thaidigit($cardID):$cardID).")"; //ถ้าเลือกโชวเลขบัตรหลังชื่อ
			$arr_content[$data_count][cmd_position] = $CMD_ORG4." ".$CMD_ORG3;
		} elseif ($print_order_by==2) {
			$data_count++;
			$arr_content[$data_count][type] = "CONTENT";
			if ($CARDNO_FLAG==1) $arr_content[$data_count][name] = "*Enter* (".(($NUMBER_DISPLAY==2)?convert2thaidigit($cardID):$cardID).")"; //ถ้าเลือกโชวเลขบัตรหลังชื่อ
		} 
		$arr_content[$data_count][cmd_note1] = $EM_NAME;
		
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	//new format************************************************************
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
		if ($FLAG_RTF) {
			$RTF->add_header("", 0, false);	// header default
			$RTF->add_footer("", 0, false);		// footer default
			
	//		echo "$head_text1<br>";
			$tab_align = "center";
			$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
		} else {
			$pdf->AutoPageBreak = false; 
	$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
					}
	if (!$result) echo "****** error ****** on open table for $table<br>";
	
	if($count_data){
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			if ($CARDNO_FLAG==1) $CARDNO = $arr_content[$data_count][cardno];
			$TYPE_NAME = $arr_content[$data_count][type_name];
			$LEVEL_NAME = $arr_content[$data_count][level_name];
			$CMD_POS_NO_NAME = $arr_content[$data_count][cmd_pos_no_name];
			$CMD_POS_NO = $arr_content[$data_count][cmd_pos_no];
			$CMD_LEVEL = $arr_content[$data_count][cmd_level];
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
//			$CMD_ORG3 = $arr_content[$data_count][cmd_org3];

			$CMD_OLD_SALARY = $arr_content[$data_count][cmd_old_salary];
			$CMD_SALARY = $arr_content[$data_count][cmd_salary];
			$CMD_SALARY_MAX = $arr_content[$data_count][cmd_salary_max];
			$CMD_SPSALARY = $arr_content[$data_count][cmd_spsalary];
			$CMD_DIFF = $arr_content[$data_count][cmd_diff];
			$SAH_SALARY_EXTRA = $arr_content[$data_count][sah_salary_extra];
			$CMD_STEP = $arr_content[$data_count][cmd_step];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
			$CMD_MIDPOINT = $arr_content[$data_count][cmd_midpoint];
			$TOTAL_SCORE = $arr_content[$data_count][total_score];
			
			if($CONTENT_TYPE=="ORG" || $CONTENT_TYPE=="ORG_1" || $CONTENT_TYPE=="ORG_2"){
				$ORG_NAME = $arr_content[$data_count][org_name];
//				$NEW_ORG_NAME = $arr_content[$data_count][new_org_name];


				if($CONTENT_TYPE == "ORG")$pdf->SetFont($font,'b','',14);
				else $pdf->SetFont($font,'',14);
				$arr_data = (array) null;
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "$ORG_NAME";
				$arr_data[] = "";
				$arr_data[] = "";;
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$data_align = array("L", "L", "L", "L", "L", "L", "L", "L");
		    	if ($FLAG_RTF)
			    $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			    else
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				
			}elseif($CONTENT_TYPE=="CONTENT"){
				$arr_data = (array) null;
				$arr_data[] =  $ORDER;
				$arr_data[] =  $NAME;
				$arr_data[] = $CMD_POSITION;
				$arr_data[] = $LEVEL_NAME;
				$arr_data[] = $CMD_POS_NO_NAME.$CMD_POS_NO;
				$arr_data[] = $CMD_OLD_SALARY;
				$arr_data[] = $CMD_SALARY;
				$arr_data[] = $CMD_NOTE1;
			
				$data_align = array("C", "L", "L", "L", "C", "R", "R", "L");
			     if ($FLAG_RTF)
			     $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				 else
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end if
			//====================================================
	
			if($CMD_NOTE2){
				$arr_data = (array) null;
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				
				$data_align = array("L", "L", "L", "L", "L", "L", "L", "L");
			     if ($FLAG_RTF)
			     $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end if
		} // end if
	} // end for				
           if ($FLAG_RTF) {
			$RTF->close_tab(); 
			}else {
			$pdf->close_tab("");
			        } 
	
	if($COM_NOTE){
		$head_text1 = "";
		$head_width1 = "20,269";
		$head_align1 = "L,L";
		if ($FLAG_RTF) {
			$RTF->add_header("", 0, false);	// header default
			$RTF->add_footer("", 0, false);		// footer default
			$tab_align = "center";
		$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
		 } else {
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
		             }
		if (!$result) echo "****** error ****** on open table for $table<br>";
		
		$arr_data = (array) null;
		$arr_data[] = "หมายเหตุ : ";
		$arr_data[] = "$COM_NOTE";

		$data_align = array("L", "L");
		if ($FLAG_RTF)
		$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		else	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

		
	} // end if
   if ($FLAG_RTF) {
			$RTF->close_tab(); 
//			$RTF->close_section(); 
			$RTF->display($fname);
		} else {
			$pdf->close_tab(""); 
			$pdf->close();
			$pdf->Output();	
		}
	ini_set("max_execution_time", 30);
?>