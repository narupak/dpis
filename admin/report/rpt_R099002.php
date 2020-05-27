<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include ("../php_scripts/function_share.php");
	
	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", 864000);
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$order_by = (isset($order_by))?  $order_by : 1;
  	if($order_by==1) $order_str = "a.KF_END_DATE, a.KF_CYCLE, PER_NAME, PER_SURNAME";
  	elseif($order_by==2) {
		if($DPISDB=="odbc") {
			if ($search_per_type==0 || $search_per_type==1) $order_str = "a.KF_END_DATE, a.KF_CYCLE, CLng(POS_NO)";
			elseif ($search_per_type==0 || $search_per_type==2) $order_str = "a.KF_END_DATE, a.KF_CYCLE, CLng(POEM_NO)";
			elseif ($search_per_type==0 || $search_per_type==3) $order_str = "a.KF_END_DATE, a.KF_CYCLE, CLng(POEMS_NO)";
			elseif ($search_per_type==0 || $search_per_type==4) $order_str = "a.KF_END_DATE, a.KF_CYCLE, CLng(POT_NO)";
		}elseif($DPISDB=="oci8"){
		 	if ($search_per_type==0 || $search_per_type==1) $order_str = "a.KF_END_DATE, a.KF_CYCLE, to_number(replace(POS_NO,'-',''))";
		 	elseif ($search_per_type==0 || $search_per_type==2) $order_str = "a.KF_END_DATE, a.KF_CYCLE, to_number(replace(POEM_NO,'-',''))";
		 	elseif ($search_per_type==0 || $search_per_type==3) $order_str = "a.KF_END_DATE, a.KF_CYCLE, to_number(replace(POEMS_NO,'-',''))";
		 	elseif ($search_per_type==0 || $search_per_type==4) $order_str = "a.KF_END_DATE, a.KF_CYCLE, to_number(replace(POT_NO,'-',''))";
		}elseif($DPISDB=="mysql"){ 
			if ($search_per_type==0 || $search_per_type==1) $order_str = "a.KF_END_DATE, a.KF_CYCLE, POS_NO+0";
			elseif ($search_per_type==0 || $search_per_type==2) $order_str = "a.KF_END_DATE, a.KF_CYCLE, POEM_NO+0";
			elseif ($search_per_type==0 || $search_per_type==3) $order_str = "a.KF_END_DATE, a.KF_CYCLE, POEMS_NO+0";
			elseif ($search_per_type==0 || $search_per_type==4) $order_str = "a.KF_END_DATE, a.KF_CYCLE, POT_NO+0";
		}
  	} elseif($order_by==3) $order_str = "a.KF_END_DATE, a.KF_CYCLE, f.LEVEL_SEQ_NO DESC, PER_NAME, PER_SURNAME";
  	elseif($order_by==4) {
		if ($search_per_type==0 || $search_per_type==1) $order_str = "a.KF_END_DATE, a.KF_CYCLE, c.ORG_ID, PER_NAME, PER_SURNAME";
		elseif ($search_per_type==0 || $search_per_type==2) $order_str = "a.KF_END_DATE, a.KF_CYCLE, d.ORG_ID, PER_NAME, PER_SURNAME";
		elseif ($search_per_type==0 || $search_per_type==3) $order_str = "a.KF_END_DATE, a.KF_CYCLE, e.ORG_ID, PER_NAME, PER_SURNAME";
		elseif ($search_per_type==0 || $search_per_type==4) $order_str = "a.KF_END_DATE, a.KF_CYCLE, g.ORG_ID, PER_NAME, PER_SURNAME";
	}

	if ($search_ministry_name) $MINISTRY_NAME = $search_ministry_name;
	if ($search_department_name) $DEPARTMENT_NAME = $search_department_name;
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
	if(trim($search_org_id)){
		if($search_per_type=='1'){
                    if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID = $search_org_id)";
                    else if($select_org_structure==1) $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
                }else{
                    if($select_org_structure==0) $arr_search_condition[] = "(e.ORG_ID = $search_org_id)";
                    else if($select_org_structure==1) $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
                }
	}
	if(trim($search_org_id_1)){
	 	if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_1 = $search_org_id_1)";
		else if($select_org_structure==1) $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
	}
	if(trim($search_org_id_2)){
	 	if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_2 = $search_org_id_2)";
		else if($select_org_structure==1) $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
	}
	
	if($search_org_id1){
		$arr_search_condition[] = "(a.ORG_ID_KPI=$search_org_id1)";
  	}elseif($search_department_id){
		if($select_org_structure==1){
			unset($arr_org);
			$arr_org[] = $search_department_id;	
			$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$search_department_id and OL_CODE='03' ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()){
				$arr_org[] = $data[ORG_ID];		//$data[ORG_ID] = ORG_ID	
				$cmd1 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data[ORG_ID] and OL_CODE='04' ";
				$db_dpis1->send_cmd($cmd1);
				while($data1 = $db_dpis1->get_array()){
					$arr_org[] = $data1[ORG_ID];		 //$data1[ORG_ID] = ORG_ID_1
					$cmd2 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data1[ORG_ID] and OL_CODE='05' ";
					$db_dpis2->send_cmd($cmd2);
					while($data2 = $db_dpis2->get_array()){
						$arr_org[] = $data2[ORG_ID];		 //$data2[ORG_ID] = ORG_ID_2
					}
				}
			}			
			$arr_search_condition[] =  "(b.ORG_ID in (". implode(",", $arr_org) ."))";
		}else{
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
		}
	}elseif($search_ministry_id){
		if($SESS_ORG_STRUCTURE==1){
			$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
			unset($arr_org);
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id and OL_CODE='02' ";		
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()){
			 	$arr_org[] = $data[ORG_ID];		  //$data[ORG_ID] = DEPARTMENT_ID
				$cmd1 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data[ORG_ID] and OL_CODE='03' ";
				$db_dpis1->send_cmd($cmd1);
				while($data1 = $db_dpis1->get_array()){
					$arr_org[] = $data1[ORG_ID];		 //$data1[ORG_ID] = ORG_ID	
					$cmd2 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data1[ORG_ID] and OL_CODE='04' ";
					$db_dpis2->send_cmd($cmd2);
					while($data2 = $db_dpis2->get_array()){
						$arr_org[] = $data2[ORG_ID];		 //$data2[ORG_ID] = ORG_ID_1
						$cmd3 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data2[ORG_ID] and OL_CODE='05' ";
						$db_dpis3->send_cmd($cmd3);
						while($data3 = $db_dpis3->get_array()){
							$arr_org[] = $data3[ORG_ID];		 //$data3[ORG_ID] = ORG_ID_2
						}
					}
				} //end while
			}		
			$arr_search_condition[] = "(b.ORG_ID in (". implode(",", $arr_org) ."))";	
		}else{
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
		}
	} // end if
	
  	if(trim($search_kf_year)){ 
		if($DPISDB=="odbc"){ 
			$arr_search_condition[] = "(LEFT(a.KF_START_DATE, 10) >= '". ($search_kf_year - 543 - 1)."-10-01')";
			$arr_search_condition[] = "(LEFT(a.KF_END_DATE, 10) < '". ($search_kf_year - 543)."-10-01')";
		}elseif($DPISDB=="oci8"){
			$arr_search_condition[] = "(SUBSTR(a.KF_START_DATE, 1, 10) >= '". ($search_kf_year - 543 - 1)."-10-01')";
			$arr_search_condition[] = "(SUBSTR(a.KF_END_DATE, 1, 10) < '". ($search_kf_year - 543)."-10-01')";
		}elseif($DPISDB=="mysql"){
			$arr_search_condition[] = "(LEFT(a.KF_START_DATE, 10) >= '". ($search_kf_year - 543 - 1)."-10-01')";
			$arr_search_condition[] = "(LEFT(a.KF_END_DATE, 10) < '". ($search_kf_year - 543)."-10-01')";
		} // end if
	} // end if
	if(trim($search_per_name)) $arr_search_condition[] = "(b.PER_NAME like '$search_per_name%')";
	if(trim($search_per_surname)) $arr_search_condition[] = "(b.PER_SURNAME like '$search_per_surname%')";
	$arr_search_condition[] = "(a.KF_CYCLE in (". implode(",", $search_kf_cycle) ."))";
        //http://dpis.ocsc.go.th/Service/node/1957
        if($search_per_type==''){$search_per_type = 1;}

        $arr_search_condition[] = "(b.PER_TYPE = $search_per_type)";
        //$arr_search_condition[] = "(b.PER_STATUS = 1)";
        if($search_per_status==''){
            $arr_search_condition[] = "(b.PER_STATUS in (1))";
        }else{
            $arr_search_condition[] = "(b.PER_STATUS in (". implode(", ", $search_per_status) ."))";
        }
        //end
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = "	select a.KF_ID, a.KF_END_DATE, a.KF_CYCLE, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, a.SUM_KPI, a.SUM_COMPETENCE, a.SUM_OTHER, a.PER_ID, a.PER_ID_REVIEW, a.PER_ID_REVIEW1, a.PER_ID_REVIEW2, CHIEF_PER_ID, FRIEND_FLAG, TOTAL_SCORE, a.ORG_ID_KPI, a.ORG_ID, b.LEVEL_NO, b.PER_TYPE, b.POS_ID, b.POEM_ID, b.POEMS_ID, b.POT_ID
							from (	
										(	
											(	
												(	
													(
														PER_KPI_FORM a
														 	inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
													)	left join PER_POSITION c on (b.POS_ID=c.POS_ID) 
												) 	left join PER_POS_EMP d on (b.POEM_ID=d.POEM_ID)
											) 	left join PER_POS_TEMP g on (b.POT_ID=g.POT_ID)
										) 	left join PER_LEVEL f on (b.LEVEL_NO=f.LEVEL_NO)
									) 	left join PER_LEVEL f on (b.LEVEL_NO=f.LEVEL_NO)
											$search_condition
							order by 	$order_str ";
	}elseif($DPISDB=="oci8"){
		$min_rownum = (($current_page - 1) * $data_per_page) + 1;
		$max_rownum = $current_page * $data_per_page;

		$cmd = "select		a.KF_ID, a.KF_END_DATE, a.KF_CYCLE, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, a.SUM_KPI, a.SUM_COMPETENCE, a.SUM_OTHER, a.PER_ID, a.PER_ID_REVIEW, a.PER_ID_REVIEW1, a.PER_ID_REVIEW2, CHIEF_PER_ID, FRIEND_FLAG, TOTAL_SCORE, a.ORG_ID_KPI, a.ORG_ID, b.LEVEL_NO, b.PER_TYPE, b.POS_ID, b.POEM_ID, b.POEMS_ID,b.POT_ID
								from		PER_KPI_FORM a, PER_PERSONAL b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, PER_LEVEL f ,PER_POS_TEMP g
								where		a.PER_ID=b.PER_ID and b.POS_ID=c.POS_ID(+) and b.POEM_ID=d.POEM_ID(+) and b.POEMS_ID=e.POEMS_ID(+) and b.POT_ID=g.POT_ID(+) and b.LEVEL_NO=f.LEVEL_NO(+)
												$search_condition
								order by 	$order_str ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		a.KF_ID, a.KF_END_DATE, a.KF_CYCLE, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, a.SUM_KPI, a.SUM_COMPETENCE, a.SUM_OTHER, a.PER_ID, a.PER_ID_REVIEW, a.PER_ID_REVIEW1, a.PER_ID_REVIEW2, CHIEF_PER_ID, FRIEND_FLAG, TOTAL_SCORE, a.ORG_ID_KPI, a.ORG_ID, b.LEVEL_NO, b.PER_TYPE, b.POS_ID, b.POEM_ID, b.POEMS_ID,b.POT_ID
							from  (	
							            (	
											(	
												(	
													(
														PER_KPI_FORM a
														 	inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
													)	left join PER_POSITION c on (b.POS_ID=c.POS_ID) 
												) 	left join PER_POS_EMP d on (b.POEM_ID=d.POEM_ID)
											) 	left join PER_POS_EMPSER e on (b.POEMS_ID=e.POEMS_ID)
									  ) 	left join PER_POS_TEMP g on (b.POT_ID=g.POT_ID)	
									) 	left join PER_LEVEL f on (b.LEVEL_NO=f.LEVEL_NO)
											$search_condition
							order by 	$order_str";
	} // end if
	/*if($select_org_structure==1) { 
		$cmd = str_replace("c.ORG_ID", "b.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}*/
	//echo $cmd;

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "รายงานตัวชิ้วัดรายบุคคล";

	if ($SESS_PROVINCE_NAME!=""){
		if ($SESS_PROVINCE_NAME=="กรุงเทพมหานคร") $report_title .= "||" . $SESS_PROVINCE_NAME;
		else $report_title .= "||จังหวัด" . $SESS_PROVINCE_NAME;
	}
	if ($SESS_USERGROUP_LEVEL <= 3) $report_title .= "||" . $MINISTRY_NAME;
	$report_title .= "||" . $DEPARTMENT_NAME;
	$report_code = "R99002";
	$orientation='L';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',12);

	$heading_width[0] = "15";
	$heading_width[1] = "7";
	$heading_width[2] = "50";
	if($CTRL_TYPE <= 3 && $SESS_USERGROUP_LEVEL <= 3 && !($search_department_name) ){
		if(!($search_ministry_name) ){
                        $heading_width[3] = "20";
			$heading_width[4] = "40";
			$heading_width[5] = "35";
			$heading_width[6] = "35"; 
			$heading_width[7] = "35"; 
			$heading_width[8] = "12";
			$heading_width[9] = "12";
			$heading_width[10] = "12";
			$heading_width[11] = "18";
			//echo "case 1" ."<br>";
		}else{
                        $heading_width[3] = "20";
			$heading_width[4] = "47";
			$heading_width[5] = "47";
			$heading_width[6] = "46";
			$heading_width[7] = "13";
			$heading_width[8] = "13";
			$heading_width[9] = "13";
			$heading_width[10] = "18";
			//echo "case 2" ."<br>";
		}
	}else{
                $heading_width[3] = "20";
		$heading_width[4] = "60";
		$heading_width[5] = "70";
		$heading_width[6] = "15";
		$heading_width[7] = "15";
		$heading_width[8] = "15";
		$heading_width[9] = "18";
		//echo "case 3" ."<br>";
	}
		
	function print_header($CTRL_TYPE,$SESS_USERGROUP_LEVEL,$search_department_name,$search_ministry_name){
		global $pdf, $heading_width, $ORG_TITLE, $DEPARTMENT_TITLE, $MINISTRY_TITLE;
		
		$pdf->SetFont($font,'',10);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ปีงบประมาณ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ครั้งที่",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ชื่อผู้รับการประเมิน",'LTBR',0,'C',1);
                $pdf->Cell($heading_width[3] ,7,"เลขที่ตำแหน่ง",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ตำแหน่ง",'LTBR',0,'C',1);
		if($CTRL_TYPE <= 3 && $SESS_USERGROUP_LEVEL <= 3 && !($search_department_name) ){
			if(!($search_ministry_name) ){
				$pdf->Cell($heading_width[5] ,7,"$ORG_TITLE",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[6] ,7,"$DEPARTMENT_TITLE",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[7] ,7,"$MINISTRY_TITLE",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[8] ,7,"ผลสัมฤทธิ์",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[9] ,7,"สมรรถนะ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[10] ,7,"อื่น ๆ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[11] ,7,"ผลการประเมิน",'LTBR',1,'C',1);
			}else{
				$pdf->Cell($heading_width[5] ,7,"$ORG_TITLE",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[6] ,7,"$DEPARTMENT_TITLE",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[7] ,7,"ผลสัมฤทธิ์",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[8] ,7,"สมรรถนะ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[9] ,7,"อื่น ๆ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[10] ,7,"ผลการประเมิน",'LTBR',1,'C',1);
			}
		}else{
			$pdf->Cell($heading_width[5] ,7,"$ORG_TITLE",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[6] ,7,"ผลสัมฤทธิ์",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[7] ,7,"สมรรถนะ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[8] ,7,"อื่น ๆ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[9] ,7,"ผลการประเมิน",'LTBR',1,'C',1);
		}

	} // function		
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	if($count_data){
		//echo "CTRL_TYPE=".$CTRL_TYPE."<br>";
		//echo "SESS_USERGROUP_LEVEL=".$SESS_USERGROUP_LEVEL."<br>";
		//echo "search_ministry_name=".$search_ministry_name."<br>";
		//echo "search_department_name=".$search_department_name."<br>";
		//echo ($CTRL_TYPE <= 3 && $SESS_USERGROUP_LEVEL <= 3 && !($search_department_name))."<br>";

		$pdf->AutoPageBreak = false;
		print_header($CTRL_TYPE,$SESS_USERGROUP_LEVEL,$search_department_name,$search_ministry_name);
		$data_count = $data_row = 0;
		$iMAX=0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;			
			
			$TMP_KF_ID = $data[KF_ID];
			$current_list .= ((trim($current_list))?",":"") . $TMP_KF_ID;
			$KF_END_DATE = substr($data[KF_END_DATE], 0, 10);
			$KF_YEAR = substr($KF_END_DATE, 0, 4) + 543;
			$KF_END_DATE = show_date_format($KF_END_DATE,$DATE_DISPLAY);
			$KF_CYCLE = $data[KF_CYCLE];
			$PN_CODE = $data[PN_CODE];
			$SELF_PER_ID = $data[PER_ID];
			$CHIEF_PER_ID = $data[CHIEF_PER_ID]; 	// รหัสหัวหน้า
			$FRIEND_FLAG = $data[FRIEND_FLAG];
			$TOTAL_SCORE = $data[TOTAL_SCORE];
			$ORG_ID_KPI = $data[ORG_ID_KPI];
			$TMP_ORG_ID = $data[ORG_ID];

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $TMP_ORG_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_ORG_NAME = trim($data2[ORG_NAME]);

			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			if ($TOTAL_SCORE)
				$RESULT = number_format($TOTAL_SCORE, 2);
			else
				$RESULT = number_format(($data[SUM_KPI] + $data[SUM_COMPETENCE]), 2);
			
			if ($data[SUM_KPI]!="") $SUM_KPI = number_format($data[SUM_KPI], 2);
			else $SUM_KPI = "-";

			if ($data[SUM_COMPETENCE]!="") $SUM_COMPETENCE = number_format($data[SUM_COMPETENCE], 2);
			else $SUM_COMPETENCE = "-";

			if ($data[SUM_OTHER]!="") $SUM_OTHER = number_format($data[SUM_OTHER], 2);
			else $SUM_OTHER = "-";

			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PN_NAME = $data2[PN_NAME];
		
			$PER_FULLNAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;
			
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$PER_TYPE = $data[PER_TYPE];
			$POS_ID = $data[POS_ID];
			$POEM_ID = $data[POEM_ID];
			$POEMS_ID = $data[POEMS_ID];
			
			$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LEVEL_NAME = trim($data2[LEVEL_NAME]);
			$POSITION_LEVEL = $data2[POSITION_LEVEL];
			if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;

			if($PER_TYPE == 1){
				$cmd = " select 	a.ORG_ID, d.ORG_NAME, b.PL_NAME, a.PT_CODE ,a.POS_NO
								from 		PER_POSITION a, PER_LINE b, PER_ORG d
								where 	a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=d.ORG_ID ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_PL_NAME = $data2[PL_NAME] . $POSITION_LEVEL;
                                $TMP_POS_NO = $data2[POS_NO];
			}elseif($PER_TYPE == 2){
				$cmd = " select	pl.PN_NAME, po.ORG_NAME    
								from	PER_POS_EMP pp, PER_POS_NAME pl, PER_ORG po 
								where	pp.POEM_ID=$POEM_ID and pp.ORG_ID=po.ORG_ID and pp.PN_CODE=pl.PN_CODE  ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PN_NAME]);
				$TMP_PL_NAME = (trim($PL_NAME))? "$PL_NAME$POSITION_LEVEL" : "";	
			}elseif($PER_TYPE == 3){
				$cmd = " select	pl.EP_NAME, po.ORG_NAME ,pp.POEMS_NO
								from	PER_POS_EMPSER pp, PER_EMPSER_POS_NAME pl, PER_ORG po 
								where	pp.POEMS_ID=$POEMS_ID and pp.ORG_ID=po.ORG_ID and pp.EP_CODE=pl.EP_CODE  ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[EP_NAME]);
				//$TMP_PL_NAME = (trim($PL_NAME))? "$PL_NAME$POSITION_LEVEL" : "";
                                $TMP_PL_NAME = (trim($PL_NAME))? "$PL_NAME" : "";
                                $TMP_POS_NO = $data2[POEMS_NO];
                                
			}elseif($PER_TYPE == 4){
				$cmd = " select	pl.TP_NAME, po.ORG_NAME   
								from	 PER_POS_TEMP pp, PER_TEMP_POS_NAME pl, PER_ORG po 
								where	pp.POT_ID=$POT_ID and pp.ORG_ID=po.ORG_ID and pp.TP_CODE=pl.TP_CODE  ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[TP_NAME]);
				$TMP_PL_NAME = (trim($PL_NAME))? "$PL_NAME$POSITION_LEVEL" : "";	
			} // end if

			if($CTRL_TYPE <= 3 && $SESS_USERGROUP_LEVEL <= 3 && !($search_department_name) ){
				$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID = $TMP_ORG_ID ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_ORG_ID_REF1 = trim($data2[ORG_ID_REF]);

				$cmd = " select ORG_NAME,ORG_ID_REF from PER_ORG where ORG_ID = $TMP_ORG_ID_REF1 ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$Department = trim($data2[ORG_NAME]);
				$TMP_ORG_ID_REF2 = trim($data2[ORG_ID_REF]);

				if(!($search_ministry_name) ){
					$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $TMP_ORG_ID_REF2 ";
					if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$Ministry = trim($data2[ORG_NAME]);
				}
			}
			
			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->Cell($heading_width[0], 7, $KF_YEAR, $border, 0, 'C', 0);
			$pdf->Cell($heading_width[1], 7, $KF_CYCLE, $border, 0, 'C', 0);

			$pdf->MultiCell($heading_width[2], 7, $PER_FULLNAME, $border, "L");
                        
                        if($pdf->y > $max_y) $max_y = $pdf->y;                                               // Release 5.2.1.21 
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];         // Release 5.2.1.21 
			$pdf->y = $start_y;                                                                     // Release 5.2.1.21 
                        $pdf->MultiCell($heading_width[3], 7, $TMP_POS_NO, $border, "C");                       // Release 5.2.1.21 
                        
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2]+$heading_width[3];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[4], 7, $TMP_PL_NAME, $border, "L");
                        
                        
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3]+$heading_width[4];
			$pdf->y = $start_y;

			if($CTRL_TYPE <= 3 && $SESS_USERGROUP_LEVEL <= 3 && !($search_department_name) ){
				if(!($search_ministry_name) ){
					$iMAX=11;
					$pdf->MultiCell($heading_width[5], 7, $TMP_ORG_NAME, $border, "L");
					if($pdf->y > $max_y) $max_y = $pdf->y;
					$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5];
					$pdf->y = $start_y;

					$pdf->MultiCell($heading_width[6], 7, $Department, $border, "L");
					if($pdf->y > $max_y) $max_y = $pdf->y;
					$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5]+ $heading_width[6];
					$pdf->y = $start_y;

					$pdf->MultiCell($heading_width[7], 7, $Ministry, $border, "L");
					if($pdf->y > $max_y) $max_y = $pdf->y;
					$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6]+ $heading_width[7];
					$pdf->y = $start_y;

					$pdf->Cell($heading_width[8], 7, $SUM_KPI, $border, 0, 'R', 0);
					$pdf->Cell($heading_width[9], 7, $SUM_COMPETENCE, $border, 0, 'R', 0);
					$pdf->Cell($heading_width[10], 7, $SUM_OTHER, $border, 0, 'R', 0);
					$pdf->Cell($heading_width[11], 7, $RESULT,  $border, 1, 'R', 0);
				}else{
					$iMAX=10;
					$pdf->MultiCell($heading_width[5], 7, $TMP_ORG_NAME, $border, "L");
					if($pdf->y > $max_y) $max_y = $pdf->y;
					$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4]+ $heading_width[5];
					$pdf->y = $start_y;

					$pdf->MultiCell($heading_width[6], 7, $Department, $border, "L");
					if($pdf->y > $max_y) $max_y = $pdf->y;
					$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5]+ $heading_width[6];
					$pdf->y = $start_y;

					$pdf->Cell($heading_width[7], 7, $SUM_KPI, $border, 0, 'R', 0);
					$pdf->Cell($heading_width[8], 7, $SUM_COMPETENCE, $border, 0, 'R', 0);
					$pdf->Cell($heading_width[9], 7, $SUM_OTHER, $border, 0, 'R', 0);
					$pdf->Cell($heading_width[10], 7, $RESULT,  $border, 1, 'R', 0);
				}
			}else{
				$iMAX=9;
				$pdf->MultiCell($heading_width[5], 7, $TMP_ORG_NAME, $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4]+ $heading_width[5];
				$pdf->y = $start_y;

				$pdf->Cell($heading_width[6], 7, $SUM_KPI, $border, 0, 'R', 0);
				$pdf->Cell($heading_width[7], 7, $SUM_COMPETENCE, $border, 0, 'R', 0);
				$pdf->Cell($heading_width[8], 7, $SUM_OTHER, $border, 0, 'R', 0);
				$pdf->Cell($heading_width[9], 7, $RESULT,  $border, 1, 'R', 0);
			}

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
			for($i=0; $i<=$iMAX; $i++){
				$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
				$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
			//====================================================

			if(($pdf->h - $max_y - 11) < 15){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < $count_data){
					$pdf->AddPage();
					print_header($CTRL_TYPE,$SESS_USERGROUP_LEVEL,$search_department_name,$search_ministry_name);
					$max_y = $pdf->y;
				} // end if
			}else{
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
			} // end if
			$pdf->x = $start_x;			$pdf->y = $max_y;
		} // end while
		$border = "LTBR";
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		if($CTRL_TYPE <= 3 && $SESS_USERGROUP_LEVEL <= 3 && !($search_department_name) ){
			if(!($search_ministry_name) ){
				$pdf->Cell(($heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10]+ $heading_width[11]), 7, " ", $border, 1, 'L', 0);
			}else{
				$pdf->Cell(($heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9]+ $heading_width[10]), 7, " ", $border, 1, 'L', 0);
			}
		}else{
			$pdf->Cell(($heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8]+ $heading_width[9]), 7, " ", $border, 1, 'L', 0);
		}
		

	}else{
		$pdf->SetFont($font,'b',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 846000);
?>