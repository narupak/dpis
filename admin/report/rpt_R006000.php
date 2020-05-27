<?php
	include("../../php_scripts/connect_database.php");
        //include("php_scripts/session_start.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if (!$PER_ID) 	$PER_ID = $_GET[PER_ID];
	if (!$PER_ID) 	$PER_ID = $_POST[PER_ID];
        

	if (!$SAH_KF_YEAR) 	$SAH_KF_YEAR = $_GET[SAH_KF_YEAR];
	if (!$SAH_KF_YEAR) 	$SAH_KF_YEAR = $_POST[SAH_KF_YEAR];
	if ($SAH_KF_YEAR) 		$search_budget_year = $SAH_KF_YEAR;
	if (!$SAH_KF_CYCLE) 	$SAH_KF_CYCLE = $_GET[SAH_KF_CYCLE];
	if (!$SAH_KF_CYCLE) 	$SAH_KF_CYCLE = $_POST[SAH_KF_CYCLE];
	if ($SAH_KF_CYCLE) 		$search_kf_cycle = $SAH_KF_CYCLE;
//	echo "search_budget_year=$search_budget_year ,  search_kf_cycle=$search_kf_cycle<br>";

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

//	echo "search_per_type=$search_per_type<br>";
	$mgt_code = "";
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_code ="b.PL_CODE";
		$line_name = "PL_NAME";
		$position_no_name = "b.POS_NO_NAME";
		$position_no = "b.POS_NO";
		$mgt_code = ", PM_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "b.PN_CODE";
		$line_name = "PN_NAME";	
		$position_no_name = "b.POEM_NO_NAME";
		$position_no = "b.POEM_NO";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "b.EP_CODE";
		$line_name = "EP_NAME";	
		$position_no_name = "b.POEMS_NO_NAME";
		$position_no = "b.POEMS_NO";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "b.TP_CODE";
		$line_name = "TP_NAME";	
		$position_no_name = "b.POT_NO_NAME";
		$position_no = "b.POT_NO";
	} // end if
//	echo "position_table=$position_table<br>";
//	echo "search_budget_year=$search_budget_year<br>";

	$KF_CYCLE = $search_kf_cycle;
	if(trim($search_budget_year)){ 
		if($KF_CYCLE == 1){
			$START_DATE = ($search_budget_year - 543) . '-04-01';
			$KF_START_DATE = '1 ต.ค. ' . ($search_budget_year - 1);
			$KF_END_DATE = '31 มี.ค. ' . $search_budget_year;
			$SIGN_START_DATE1 = (($search_budget_year -1) - 543) . '-10-01';
			$SIGN_END_DATE1 = ($search_budget_year - 543) . '-03-31';
		}elseif($KF_CYCLE == 2){
			$START_DATE = ($search_budget_year - 542) . '-10-01';
			$KF_START_DATE = '1 เม.ย. ' . $search_budget_year;
			$KF_END_DATE = '30 ก.ย. ' . $search_budget_year;
			$SIGN_START_DATE1 = ($search_budget_year - 543) . '-04-01';
			$SIGN_END_DATE1 = ($search_budget_year - 543) . '-09-30';
		} // end if
	} // end if

	if(in_array("PER_ORG", $list_type)){
		if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if(trim($search_org_id)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
			else if($select_org_structure==1) $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		} // end if
		if(trim($search_org_id_1)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			else if($select_org_structure==1) $arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1)"; 
		} // end if
		if(trim($search_org_id_2)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			else if($select_org_structure==1) $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2)";
		} // end if
	}else{
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "";
	$report_code = "R04065";
	$orientation='P';

//	session_cache_limiter("nocache"); 
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,2,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',18);
	
	$page_start_x = $pdf->x;	  $page_start_y = $pdf->y;
	
	//#### ดึงลายเซ็น ####//
	function getPIC_SIGN($PER_ID,$PER_CARDNO){
		global $db_dpis , $db_dpis2;
	
		$PIC_SIGN="";
		//หารูปที่เป็นลายเซ็น
		$cmd = "	select 		*
									  from 		PER_PERSONALPIC
									  where 		PER_ID=$PER_ID and PER_CARDNO = '$PER_CARDNO' and  PIC_SIGN=1
									  order by 	PER_PICSEQ ";
		$count_pic_sign=$db_dpis2->send_cmd($cmd);
		if($count_pic_sign>0){	
		$data2 = $db_dpis2->get_array();
		//echo $cmd . "<br>";
		$TMP_PIC_SEQ = $data2[PER_PICSEQ];
		$current_list .= ((trim($current_list))?",":"") . $TMP_PIC_SEQ;
		$T_PIC_SEQ = substr("000",0,3-strlen("$TMP_PIC_SEQ"))."$TMP_PIC_SEQ";
		$TMP_SERVER = $data2[PIC_SERVER_ID];
		$TMP_PIC_SIGN= $data2[PIC_SIGN];
		
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
		if (trim($PER_ID) && trim($PER_CARDNO) != "NULL") {
			$TMP_PIC_NAME = $data2[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg".($tmp_SERVER_NAME ? " [".$tmp_SERVER_NAME."]" : "");
			//$TMP_SHOW_PIC = ($tmp_http_server ? $tmp_http_server."/" : "").$data[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg";
		} else {
			$TMP_PIC_NAME = $data2[PER_PICPATH].$data2[PER_GENNAME]."-".$SIGN_NAME.$T_PIC_SEQ.".jpg".($tmp_SERVER_NAME ? " [".$tmp_SERVER_NAME."]" : "");
			//$TMP_SHOW_PIC = ($tmp_http_server ? $tmp_http_server."/" : "").$data[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg";
		}
		if(file_exists("../".$TMP_PIC_NAME)){
			$PIC_SIGN = "../".$TMP_PIC_NAME;		//	if($PER_CARDNO && $TMP_PIC_NAME)		$PIC_SIGN = "../../attachments/".$PER_CARDNO."/PER_SIGN/".$TMP_PIC_NAME;
		}
		} //end count	
		//echo $TMP_PIC_SIGN. "../".$TMP_PIC_NAME . "<BR>";
	return $PIC_SIGN;
	}
	
	if ($PER_ID) {
		$cond_per_id = "and a.PER_ID = $PER_ID";
	} else {
		$cond_per_id = "";
	}
	
        
        //---Begin
        
        if(!empty($SESS_ORG_ID)){
            if(!empty($SESS_PER_ID)){
                $Condition =" AND a.PER_ID=".$SESS_PER_ID."  ";
            }else{
                $Condition =" AND PER_TYPE = $search_per_type ";
            }
        }else{
            $Condition =" AND PER_TYPE = $search_per_type ";
        }
        //-- End
        $conditionSAH_ID='';
        if($SAH_ID){
            $conditionSAH_ID=' and c.SAH_ID='.$SAH_ID;
        }
	$cmd = " select		a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, $position_no_name as POS_NO_NAME, $position_no as POS_NO, $line_code as PL_CODE, 
										b.ORG_ID, SAH_SALARY_MIDPOINT, SAH_PERCENT_UP, SAH_SALARY_UP, SAH_SALARY, SAH_EFFECTIVEDATE,
										SAH_SALARY_EXTRA, SAH_DOCNO, SAH_DOCDATE, SAH_REMARK, c.MOV_CODE, a.DEPARTMENT_ID $mgt_code, SAH_OLD_SALARY, SAH_POSITION ,
                                                                                SAH_POS_NO, SAH_ORG
					  from			PER_PERSONAL a, $position_table b, PER_SALARYHIS c
					  where		$position_join and a.PER_ID=c.PER_ID   $cond_per_id and 
										SAH_KF_YEAR = '$search_budget_year' and SAH_KF_CYCLE = $KF_CYCLE 
                                                                                    $conditionSAH_ID  
					  $search_condition $Condition 
					  order by a.PER_NAME, a.PER_SURNAME --,c.SAH_EFFECTIVEDATE desc,c.SAH_DOCDATE desc,c.SAH_DOCNO desc ";	
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	//echo "cmd=$cmd ($count_data)<br>";
       /* echo "CTRL_TYPE ".$CTRL_TYPE."<br/>";
        echo "SESS_USERID:".$SESS_USERID."<br/>";
        echo "SESS_ORG_ID : ".$SESS_ORG_ID."<br/>";*/

	if($count_data){

		$data_count = 0;
		while ($data = $db_dpis->get_array()) {
			$MOV_CODE = trim($data[MOV_CODE]);
			$cmd = " select MOV_SUB_TYPE from PER_MOVMENT where trim(MOV_CODE)='$MOV_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			
                        $MOV_SUB_TYPE = trim($data2[MOV_SUB_TYPE]); //เดิม
                        //$MOV_SUB_TYPE = "4"; //test  
			//if (substr($MOV_SUB_TYPE,0,1)=="4") {
				$data_count++;

				$PN_CODE = trim($data[PN_CODE]);
				$LEVEL_NO = trim($data[LEVEL_NO]);
				$POS_NO = trim($data[POS_NO_NAME]).' '.trim($data[POS_NO]);
				/*$PL_CODE = trim($data[PL_CODE]);*/ //ของเดิม
                                
                                /* เพิ่มเติม */
                                $SAH_POS_NO = trim($data[SAH_POS_NO]);
                                $SAH_ORG = trim($data[SAH_ORG]);
                                /* END เพิ่มเติม */
                                
				$PM_CODE = trim($data[PM_CODE]);
				$ORG_ID = $data[ORG_ID];
				$SAH_SALARY_MIDPOINT = $data[SAH_SALARY_MIDPOINT]?number_format($data[SAH_SALARY_MIDPOINT]):" ";
				$SAH_PERCENT_UP = $data[SAH_PERCENT_UP]?number_format($data[SAH_PERCENT_UP],4):" ";
				$SAH_SALARY_UP = $data[SAH_SALARY_UP]?number_format($data[SAH_SALARY_UP]):" ";
				$SAH_SALARY = $data[SAH_SALARY]?number_format($data[SAH_SALARY]):" ";
				$SAH_SALARY_EXTRA = $data[SAH_SALARY_EXTRA]?number_format($data[SAH_SALARY_EXTRA],2):" ";
				$PER_SALARY = $data[SAH_SALARY] - $data[SAH_SALARY_UP];
				$PER_SALARY = number_format($PER_SALARY);
				$SAH_DOCNO = trim($data[SAH_DOCNO]);
				$SAH_DOCDATE = show_date_format(trim($data[SAH_DOCDATE]),$DATE_DISPLAY);
				$SAH_REMARK = trim($data[SAH_REMARK]);
				$SIGN_DATE = trim($data[SAH_DOCDATE]);
				$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
				$SAH_EFFECTIVEDATE = $data[SAH_EFFECTIVEDATE];
                                
                                $cmd = "select PL_CODE as PL_CODE_US, LEVEL_NO as LEVEL_NO_US 
                                from PER_POSITIONHIS where PER_ID = $PER_ID and POH_EFFECTIVEDATE <= '$SAH_EFFECTIVEDATE' and POH_POS_NO = $SAH_POS_NO
                                ORDER BY POH_EFFECTIVEDATE desc, POH_SEQ_NO desc";
                                $db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
                                $PL_CODE = trim($data2[PL_CODE_US]);
                                $level_no_us = $data2[LEVEL_NO_US];
                                
				$arr_temp = explode("-", $SAH_EFFECTIVEDATE);
				if ($arr_temp[1]=="04") $KF_CYCLE = 1;
				elseif ($arr_temp[1]=="10") $KF_CYCLE = 2;
				if($KF_CYCLE == 1){
					$KF_START_DATE = show_date_format(($arr_temp[0] - 1)."-10-01",$DATE_DISPLAY);
					$KF_END_DATE = show_date_format($arr_temp[0]."-03-31",$DATE_DISPLAY);
				}elseif($KF_CYCLE == 2){
					$KF_START_DATE = show_date_format($arr_temp[0]."-04-01",$DATE_DISPLAY);
					$KF_END_DATE = show_date_format($arr_temp[0]."-09-30",$DATE_DISPLAY);
				} // end if
				$SAH_OLD_SALARY = $data[SAH_OLD_SALARY]+0 ;
				$SAH_POSITION = trim($data[SAH_POSITION]);

				$cmd = "select PN_NAME from PER_PRENAME where PN_CODE = '$PN_CODE'";
				$db_dpis2->send_cmd($cmd);
				$data2  = $db_dpis2->get_array();
				$PN_NAME = $data2[PN_NAME];
				$PER_NAME = "$PN_NAME$data[PER_NAME] $data[PER_SURNAME]";
		
				$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO = '$level_no_us'";
				$db_dpis2->send_cmd($cmd);
				$data2  = $db_dpis2->get_array();
				$LEVEL_NAME = $data2[LEVEL_NAME];
				$POSITION_LEVEL = $data2[POSITION_LEVEL];
				if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;

                                $cmd = "select * from PER_LEVEL where LEVEL_NO = '$LEVEL_NO_TM'";
                                $db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
                                $us_level_no = $data2[POSITION_TYPE].$data2[POSITION_LEVEL];
                                
				$cmd = "	select PL_NAME from PER_LINE where	PL_CODE='$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PL_NAME]). $POSITION_LEVEL;

				$cmd = "	select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
				if($select_org_structure==1)	 $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$ORG_NAME = trim($data2[ORG_NAME]);
		
				$cmd = " select ORG_ID_REF, ORG_NAME  from PER_ORG where ORG_ID=$TMP_DEPARTMENT_ID ";
				if($select_org_structure==1)	 $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$DEPARTMENT_NAME = $data2[ORG_NAME];
		
				$MINISTRY_ID = $data2[ORG_ID_REF];
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
				if($select_org_structure==1)	 $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$MINISTRY_NAME = $data2[ORG_NAME];
			//die($PM_CODE);
				if ($PM_CODE) {
					$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$PM_CODE'  ";
                                        //die($cmd);
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PM_NAME = trim($data2[PM_NAME]);
                                        
					if ($PM_NAME=="ผู้ว่าราชการจังหวัด" || $PM_NAME=="รองผู้ว่าราชการจังหวัด" || $PM_NAME=="ปลัดจังหวัด" || 
						$PM_NAME=="ผู้อำนวยการ" || $PM_NAME=="ผู้อำนวยการกอง" || $PM_NAME=="ผู้อำนวยการศูนย์" || 
						$PM_NAME=="ผู้อำนวยการสำนัก" || $PM_NAME=="ผู้อำนวยการสถาบัน" || $PM_NAME=="หัวหน้าสำนักงานจังหวัด") {
						$PM_NAME .= $ORG_NAME;
						$PM_NAME = str_replace("จังหวัดจังหวัด", "จังหวัด", $PM_NAME); 
						$PM_NAME = str_replace("กองกอง", "กอง", $PM_NAME); 
						$PM_NAME = str_replace("ศูนย์ศูนย์", "ศูนย์", $PM_NAME); 
						$PM_NAME = str_replace("สำนักสำนัก", "สำนัก", $PM_NAME); 
						$PM_NAME = str_replace("สถาบันสถาบัน", "สถาบัน", $PM_NAME); 
						$PM_NAME = str_replace("สำนักงานจังหวัดสำนักงานจังหวัด", "สำนักงานจังหวัด", $PM_NAME); 
					} elseif ($PM_NAME=="นายอำเภอ") {
						$PM_NAME .= $ORG_NAME_1;
						$PM_NAME = str_replace("อำเภอที่ทำการปกครองอำเภอ", "อำเภอ", $PM_NAME); 
					}
					if($PM_NAME) $PL_NAME = trim($PM_NAME);
				}
                                
                                //if ($SAH_POSITION) $PL_NAME = $SAH_POSITION;/**/
                                if(!$PL_NAME){
                                    
                                    if ($SAH_POSITION) $PL_NAME = $SAH_POSITION;
                                }
 //die($PL_NAME);
                                //test
                                //$PL_NAME=$PL_NAME.'สำนักงานจังหวัดสำนักงานจังหวัด สำนักงานจังหวัดสำนักงานจังหวัด';
				$pdf->SetFont($font,'',18);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

				$this_y = $pdf->y;
				$this_x = $pdf->x;
				if($img_file){
					$image_x = $pdf->x;		$image_y = $pdf->y;		$image_w = 30;			$image_h = 35;
					$pdf->Image($img_file, $image_x, $image_y, $image_w, $image_h);
				} // end if
                                //เดิม $pdf->x = $this_x+35;  ปรับแก้เป็น $pdf->x = $this_x+15; แก้เรื่อง สังกัดยาวเกินดัน ลงวันที่ไปขึ้นหน้าใหม่
				$pdf->x = $this_x+15;
				$pdf->y = $this_y;
				$pdf->SetFont($font,'',18);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				$pdf->Cell(35 ,28," ",0,1,"L");

				$pdf->x = $this_x+15;
				$pdf->Cell(35,7,"ชื่อ-นามสกุล :  ",0,0,"L");
				$pdf->Cell(35,7,$PER_NAME,0,1,"L");

				$pdf->x = $this_x+15;
				$pdf->Cell(35, 7, "ตำแหน่ง :  ", 0, 0, "L");
				//$pdf->Cell(35, 7, $PL_NAME, 0, 1, "L"); //เดิม
                                $pdf->MultiCell(125, 7, $SAH_POSITION, 0, 1); //ปรับ

				$pdf->x = $this_x+15;
				$pdf->Cell(35 , 7, "สังกัด :  ",0,0,"L");
                                //$pdf->Cell(35 , 7, $ORG_NAME,0,1,"L"); //  เดิม
				$pdf->MultiCell(140 , 7, $SAH_ORG,0,1,"L"); //  ปรับ
				
                                /* เดิม
                                    $pdf->x = $this_x+15;
                                    $pdf->Cell(35 , 7," ",0,0,"L");
                                    $pdf->Cell(35 , 7,$DEPARTMENT_NAME,0,1,"L");
                                */
				$pdf->x = $this_x+15;
				$pdf->Cell(35 , 5," ",0,1,"L");

				$pdf->x = $this_x+15;
				$pdf->Cell(35 ,7,"--------------------------------------------------------------------------------------------------------------------",0,1,"L");

				$pdf->x = $this_x+30;
				$pdf->SetFont($font,'',22);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				$pdf->Cell(140 ,7,"หนังสือแจ้งผลการเลื่อนเงินเดือน",0,1,"C");

				$pdf->SetFont($font,'',18);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				
				$pdf->x = $this_x+15;
				$pdf->Cell(35 ,7,"รอบการประเมิน :  ",0,0,"L");
				$pdf->Cell(45 ,7,"รอบที่  ".convert2thaidigit($KF_CYCLE),0,0,"L");
				$pdf->Cell(30 ,7,convert2thaidigit("$KF_START_DATE"."  ถึง :  $KF_END_DATE"),0,1,"L");

				$pdf->x = $this_x+15;
				$pdf->Cell(35 ,7,"คำสั่งเลขที่ :  ",0,0,"L");
				$pdf->Cell(65 ,7,convert2thaidigit($SAH_DOCNO),0,0,"L");
				$pdf->Cell(10 ,7,"ลงวันที่  ".convert2thaidigit($SAH_DOCDATE),0,1,"L");

				$pdf->x = $this_x+15;
				$pdf->Cell(35,7,"ชื่อ-นามสกุล :  ",0,0,"L");
				$pdf->Cell(35,7,$PER_NAME,0,1,"L");

				$pdf->x = $this_x+15;
				$pdf->Cell(35, 7, "ตำแหน่ง :  ", 0, 0, "L");
				//$pdf->Cell(35, 7, $PL_NAME, 0, 1, "L"); //เดิม
                                $pdf->MultiCell(125, 7, $SAH_POSITION, 0, 1);

				$pdf->x = $this_x+15;
				$pdf->Cell(35 ,7,"เลขที่ตำแหน่ง :  ",0,0,"L");
				$pdf->Cell(35 ,7,convert2thaidigit($SAH_POS_NO),0,1,"L"); // เดิม $POS_NO

				$pdf->x = $this_x+15;
				$pdf->Cell(35 , 7, "สังกัด :  ",0,0,"L");
				$pdf->MultiCell(140 , 7, $SAH_ORG,0,1,"L"); // เดิม $ORG_NAME
				
                                /* เดิม แสดง สังกัดหน่วยงานเดิม
                                    $pdf->x = $this_x+15;
                                    $pdf->Cell(35 , 7," ",0,0,"L");
                                    $pdf->Cell(35 , 7,$DEPARTMENT_NAME,0,1,"L");
                                */
				$pdf->x = $this_x+15;
				$pdf->Cell(35 ,7,"เงินเดือนเดิม (บาท) :  ",0,0,"L");
				$pdf->Cell(35 ,7,convert2thaidigit($PER_SALARY),0,1,"L");

				$pdf->x = $this_x+15;
				$pdf->Cell(35 ,7,"        ได้รับการเลื่อนเงินเดือน",0,1,"L");
                                
				// เดิม if ($SAH_SALARY_UP > 0 || $SAH_SALARY_EXTRA > 0 || $data[SAH_SALARY] > $data[SAH_OLD_SALARY])
                                if (($SAH_SALARY_UP > 0 || $SAH_SALARY_EXTRA > 0 || $data[SAH_SALARY] > $data[SAH_OLD_SALARY]) && $MOV_SUB_TYPE !=49) /*Pitak */
					$pdf->Image("../images/checkbox_check.jpg", 41-20, ($pdf->y - 6), 4, 4,"jpg");
				else
					$pdf->Image("../images/checkbox_blank.jpg", 41-20, ($pdf->y - 6), 4, 4,"jpg");
				
				$pdf->x = $this_x+26;
				$pdf->Cell(35 ,7," ",0,1,"L");

				$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
				$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
				$pdf->x = $this_x+26;
				$pdf->Cell(35 ,7,"ฐานในการคำนวณ",'LTR',0,'C',1);
				$pdf->Cell(25 ,7,"ร้อยละ",'LTR',0,'C',1);
				$pdf->Cell(60 ,7,"จำนวนเงินที่ได้เพิ่ม",'LTRB',0,'C',1);
				$pdf->Cell(30 ,7,"เงินเดือนที่ได้รับ",'LTR',1,'C',1);
		
				$pdf->x = $this_x+26;
				$pdf->Cell(35 ,7,"(บาท)",'LBR',0,'C',1);
				$pdf->Cell(25 ,7,"ที่ได้เลื่อน",'LBR',0,'C',1);
				$pdf->Cell(30 ,7,"เงินเดือน",'LTBR',0,'C',1);
				$pdf->Cell(30 ,7,"เงินค่าตอบแทน",'LTBR',0,'C',1);
				$pdf->Cell(30 ,7,"(บาท)",'LBR',1,'C',1);

				$pdf->x = $this_x+26;
				$pdf->Cell(35 ,7,"",'LBR',0,'C',1);
				$pdf->Cell(25 ,7,"",'LBR',0,'C',1);
				$pdf->Cell(30 ,7,"ที่ได้เลื่อน (บาท)",'LBR',0,'C',1);
				$pdf->Cell(30 ,7,"พิเศษ (บาท)",'LBR',0,'C',1);
				$pdf->Cell(30 ,7,"",'LBR',1,'C',1);

				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				$pdf->x = $this_x+26;
				$pdf->Cell(35 ,7,convert2thaidigit($SAH_SALARY_MIDPOINT),'LBR',0,'C');
				$pdf->Cell(25 ,7,convert2thaidigit($SAH_PERCENT_UP),'LBR',0,'C');
				$pdf->Cell(30 ,7,convert2thaidigit($SAH_SALARY_UP),'LBR',0,'C');
				$pdf->Cell(30 ,7,convert2thaidigit($SAH_SALARY_EXTRA),'LBR',0,'C');
				$pdf->Cell(30 ,7,convert2thaidigit($SAH_SALARY),'LBR',1,'C');

				$pdf->x = $this_x+26;
				$pdf->Cell(35 ,7," ",0,1,"L");

				$pdf->x = $this_x+15;
				$pdf->Cell(35 ,7,"        กรณีที่ไม่ได้รับการเลื่อนเงินเดือน เนื่องจาก (เหตุผล)......................................................",0,1,"L");
				//gfb, if ($SAH_SALARY_UP > 0 || $SAH_SALARY_EXTRA > 0 || $data[SAH_SALARY] > $data[SAH_OLD_SALARY])
                                if (($SAH_SALARY_UP > 0 || $SAH_SALARY_EXTRA > 0 || $data[SAH_SALARY] > $data[SAH_OLD_SALARY]) && $MOV_SUB_TYPE !=49) //pitak
					$pdf->Image("../images/checkbox_blank.jpg", 41-20, ($pdf->y - 6), 4, 4,"jpg");
				else
					$pdf->Image("../images/checkbox_check.jpg", 41-20, ($pdf->y - 6), 4, 4,"jpg");
				
				$pdf->x = $this_x+15;
				if ($SAH_REMARK)
					$pdf->Cell(35 , 7, $SAH_REMARK,0,1,"L");
				else
					$pdf->Cell(35 ,7,".................................................................................................................................................",0,1,"L");
				
				$pdf->x = $this_x+15;
				$pdf->Cell(35 ,7,".................................................................................................................................................",0,1,"L");

	/*
				$pdf->x = $this_x+15;
				$pdf->Cell(35 ,12,"",0,1,"L");
				$pdf->Image("../images/sign_up_salary$RPT_N.jpg", 110, ($pdf->y - 6), 85, 38,"jpg");
	*/	
				
			// หาผู้มีหน้าที่จ่ายเงิน
			//print("<pre>"); print_r($SESS_E_SIGN); print("</pre>");		// 1-> แบบประเมินผลการปฏิบัติราชการ   2->ใบลา   3->สลิปเงินเดือน   4->หนังสือแจ้งผลการเลื่อนเงินเดือน  5->หนังสือรับรอง 		
				$PIC_SIGN=$SIGN_NAME=$SIGN_POSITION=$PIC_SIGN_PER=""; 
				$SIGN_TYPE = 2;	 // type หนังสือแจ้งผลการเลื่อนเงินเดือน
				//หาว่าใครเป็นคน ผู้มีหน้าที่จ่ายเงิน  NVL
				$cmd = " select PER_ID, SIGN_NAME, SIGN_POSITION, SIGN_ACTING from PER_SIGN 
                                        where DEPARTMENT_ID = $TMP_DEPARTMENT_ID and SIGN_TYPE = '$SIGN_TYPE' and SIGN_PER_TYPE = $search_per_type and ((SIGN_ENDDATE IS NOT NULL and ('$SIGN_DATE' between SIGN_STARTDATE and SIGN_ENDDATE or '$SIGN_DATE' between SIGN_STARTDATE and SIGN_ENDDATE)) or (SIGN_ENDDATE IS NULL and '$SIGN_DATE' >= SIGN_STARTDATE))
                                        order by SIGN_STARTDATE desc, SIGN_ENDDATE desc ";
                                //die($cmd);
				$count_exist=$db_dpis2->send_cmd($cmd);
				//echo "<pre>$count_exist -> $cmd";
				if($count_exist>0){
					$data2 = $db_dpis2->get_array();
					$SIGN_PER_ID = $data2[PER_ID];
					$SIGN_NAME  = trim($data2[SIGN_NAME]);
					$SIGN_POSITION  = trim($data2[SIGN_POSITION]);
					$SIGN_ACTING = trim($data2[SIGN_ACTING]);
                                        
                                        /*
                                            if($SIGN_STATUS=='0' || empty($SIGN_STATUS)){
                                                $TMP_POSITION_NAME=$SIGN_POSITION;
                                            }else{
                                                if($SIGN_STATUS==1){$SIGN_POS_TXT=" รักษาราชการแทน";}
                                                if($SIGN_STATUS==2){$SIGN_POS_TXT=" รักษาการในตำแหน่ง";}
                                                if($SIGN_STATUS==3){$SIGN_POS_TXT=" ปฏิบัติราชการแทน";}
                                                $TMP_POSITION_NAME=$SIGN_POSITION.$SIGN_POS_TXT.' '.$SIGN_POS;
                                            }
                                        */
					if($SIGN_PER_ID && $SESS_E_SIGN[4]==1){ // ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์
						// หา PER_CARDNO
						$cmd = " select 	PER_CARDNO from PER_PERSONAL where PER_ID=$SIGN_PER_ID ";
						$db_dpis2->send_cmd($cmd);	
						$data2 = $db_dpis2->get_array();
						$SIGN_PER_CARDNO = trim($data2[PER_CARDNO]);

						//echo "$cmd ->$SIGN_FULL_NAME ".$SESS_E_SIGN[4];
						$PIC_SIGN_PER = getPIC_SIGN($SIGN_PER_ID,$SIGN_PER_CARDNO);
					}
				}
			//หาชื่อ เพื่อไปหาตำแหน่งข้อความชื่อของผู้ถือจ่าย ในตาราง PER_SIGN by somsak
			$cmd="select PER_NAME, PER_SURNAME from PER_PERSONAL WHERE PER_ID = $SIGN_PER_ID";
			$db_dpis2->send_cmd($cmd);	
						$data2 = $db_dpis2->get_array();
						$PER_NAME_POS = trim($data2[PER_NAME]);
						$PER_SURNAME = trim($data2[PER_SURNAME]);
						$full_name = $PER_NAME_POS." ".$PER_SURNAME;
			$pre_name_pos = strpos($SIGN_NAME,$PER_NAME_POS);//หาตำแหน่งคำนำหน้าชื่อ
			$pre_name_sub = substr($SIGN_NAME,0,$pre_name_pos);
			$num_pre_name = strlen($pre_name_sub);
			
			$cmd ="select PN_NAME from PER_PRENAME where PN_NAME ='$pre_name_sub' and RANK_FLAG=1 ";
			$db_dpis2->send_cmd($cmd);	
						$data2 = $db_dpis2->get_array();
						$PN_NAME = trim($data2[PN_NAME]);
				if($PN_NAME){
					$SIGN_NAME = $full_name;
				}else{
					$SIGN_NAME = $SIGN_NAME;
				}
			//echo $cmd;
	//$pdf->x = $this_x+15;
				$pdf->Cell(35 ,12,"",0,1,"L");
		//		$pdf->Cell(35, 10, "", 0, 1, 'C', 0);
	//echo $SIGN_PER_ID ." " . $SIGN_PER_CARDNO . "<br>";
				if($PIC_SIGN_PER){  // มีรูปลายเซ็น
					$pdf->Cell(100, 7, "", 0, 0, 'L', 0);		//space
					if($PN_NAME==null){
						$pdf->Cell(30, 15, "ลงชื่อ", 0, 0, 'L', 0);
						$pdf->Image($PIC_SIGN_PER,($pdf->x-15), ($pdf->y+1), 40, 15,"jpg");	// Original size = wxh (60x15)
						$pdf->Cell(50, 20, str_repeat(" ", 25).$SAH_DOCDATE, 0, 0, 'L', 0);
					}else{
						$cnt_row =($num_pre_name);
						//echo $cnt_row;
						$img_type = "jpg";
						$pdf->Cell(30, 15, "ลงชื่อ $PN_NAME", 0, 0, 'R', 0);
						$pdf->Cell(60, 15, $pdf->Image($PIC_SIGN_PER,($pdf->x+2), ($pdf->y+1), 40, 15,$img_type), 0, 0, 'L', 0);
						$pdf->Cell(50, 20, str_repeat(" ", 25).$SAH_DOCDATE, 0, 0, 'L', 0);
						//$pdf->Image($PIC_SIGN_PER,($pdf->x+$cnt_row-2), ($pdf->y+1), 40, 15,$img_type);	// Original size = wxh (60x15)
					}
					
					//$pdf->Image($PIC_SIGN_PER,($pdf->x+2), ($pdf->y+1), 40, 15,"jpg");	// Original size = wxh (60x15)
					//$save_x = $pdf->x;		$save_y = $pdf->y;
					//$pdf->x += 5;			$pdf->y -= 15;
					$pdf->Cell(30, 15, "", 0, 1, 'L', 0);
				}else{
					if($PN_NAME==null){
						$pdf->Cell(100, 7, "", 0, 0, 'L', 0);		//space
						$pdf->Cell(30, 15, "ลงชื่อ.......................................................", 0, 0, 'L', 0);
						$pdf->Cell(50, 20, str_repeat(" ", 30).$SAH_DOCDATE, 0, 0, 'L', 0);
					}else{
						$pdf->Cell(90, 7, "", 0, 0, 'L', 0);		//space
						$pdf->Cell(15, 15, "ลงชื่อ $PN_NAME..............................................", 0, 0, 'L', 0);
						$pdf->Cell(50, 20, str_repeat(" ", 30).$SAH_DOCDATE, 0, 0, 'L', 0);
					}
					$pdf->Cell(50, 15, "", 0, 1, 'L', 0);
				}

				if ($SIGN_NAME){	
					$pdf->Cell(115,15, "", 0, 0, 'L', 0);
					$pdf->Cell(40,12,"($SIGN_NAME)",0,0,"C");
					$pdf->Cell(10, 7, "", 0, 1, 'L', 0);
				}else{
					$pdf->Cell(115,15, "", 0, 0, 'L', 0);
					$pdf->Cell(40,12,"(.............................................)",0,0,"C");
					$pdf->Cell(10, 7, "", 0, 1, 'L', 0);
				}
				if ($SIGN_POSITION){	
                                    $str_position = strpos($SIGN_POSITION,"รักษา");
                                    if(!$str_position)$str_position = strpos($SIGN_POSITION,"ปฏิบัติ");
                                    
                                    if($str_position) {
                                        
                                        $str1 = substr($SIGN_POSITION,0,$str_position-1);
                                        $str2 = substr($SIGN_POSITION,$str_position,strlen($SIGN_POSITION)-$str_position);
                                        
                                        $pdf->Cell(115,15, "", 0, 0, 'L', 0);
					$pdf->Cell(40,12,"$str1",0,0,"C");
					$pdf->Cell(10,7, "", 0, 1, 'L', 0);
                                        
                                        $pdf->Cell(115,15, "", 0, 0, 'L', 0);
					$pdf->Cell(40,12,"$str2",0,0,"C");
					$pdf->Cell(10,7, "", 0, 1, 'L', 0);
                                    }
                                    else {
					$pdf->Cell(115,15, "", 0, 0, 'L', 0);
					$pdf->Cell(40,12,"$SIGN_POSITION",0,0,"C");
					$pdf->Cell(10,7, "", 0, 1, 'L', 0);
                                    }
				}else{
					$pdf->Cell(100,7, "", 0, 0, 'L', 0);
					$pdf->Cell(30, 15, "ตำแหน่ง.....................................................", 0, 0, 'L', 0);
					$pdf->Cell(50, 15, "", 0, 1, 'L', 0);
				}
				if($SIGN_ACTING){
					$pdf->Cell(115,15, "", 0, 0, 'L', 0);
					$pdf->Cell(40,12,"$SIGN_ACTING",0,0,"C");
					$pdf->Cell(10,7, "", 0, 1, 'L', 0);
				}
					//modified by somsak http://dpis.ocsc.go.th/Service/node/2036
//					$pdf->Cell(100,7, "", 0, 0, 'L', 0);
//					$pdf->Cell(30, 15, "ตำแหน่ง.....................................................", 0, 0, 'L', 0);
//					$pdf->Cell(50, 15, "", 0, 1, 'L', 0);
				
				if ($search_sah_docdate) $SAH_DOCDATE = show_date_format(save_date($search_sah_docdate),$DATE_DISPLAY);
				$pdf->Cell(115,15, "", 0, 0, 'L', 0);
				//$pdf->Cell(40 ,12,"ลงวันที่  ".convert2thaidigit($SAH_DOCDATE),0,0,"C");
				$pdf->Cell(40 ,12,"ลงวันที่".str_repeat(".", 57),0,0,"C");
				$pdf->Cell(10,7, "", 0, 1, 'L', 0);
				
				/***********
				$pdf->x = $this_x+15;
				$pdf->Cell(150 ,7,"ลงชื่อ .......................................................",0,1,"R");

				$pdf->x = $this_x+15;
				$pdf->Cell(150 ,7,"(ผู้บังคับบัญชาผู้มีอำนาจสั่งเลื่อนเงินเดือนหรือผู้ได้รับมอบหมาย)",0,1,"R");

				$pdf->x = $this_x+15;
				$pdf->Cell(150 ,7,"วันที่ .......................................................",0,1,"R");
				************/
	  
				if ($data_count < $count_data) $pdf->AddPage();
			//} // MOVE_SUB_TYPE
		} // end loop while $data
	}else{
		$pdf->SetFont($font,'b',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();
?>