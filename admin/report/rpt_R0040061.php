<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	//ini_set("max_execution_time", $max_execution_time); /*เดิม*/
        ini_set("max_execution_time", 0);
        $debug=0;/*0=close,1=open*/
	
	$IMG_PATH = "../../attachment/pic_personal/";
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
        
	$arr_history_name = explode("|", $HISTORY_LIST);
//	$arr_history_name = array("POSITIONHIS", "SALARYHIS", "EXTRA_INCOMEHIS", "EDUCATE", "TRAINING", "ABILITY", "HEIR", "ABSENTHIS", "PUNISHMENT", "TIMEHIS", "REWARDHIS", "MARRHIS", "NAMEHIS", "DECORATEHIS", "SERVICEHIS", "SPECIALSKILLHIS"); 
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) ."))";

  	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $DEPARTMENT_ID ";
		if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = $data[ORG_NAME];
	}else if($MINISTRY_ID){
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $MINISTRY_ID ";
		if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];

		unset($arr_department);
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID and OL_CODE='02' ";
		if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	}else if($PROVINCE_CODE){
		$cmd = " select ORG_ID from PER_ORG where PV_CODE='$PROVINCE_CODE' and OL_CODE='02' ";
		if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	} // end if

	if($select_org_structure==0) {
		if(trim($search_org_id)){ 
			$arr_search_condition[] = "(c.ORG_ID = $search_org_id or d.ORG_ID = $search_org_id or e.ORG_ID = $search_org_id )";
			$list_type_text .= "$search_org_name";
		} // end if
		if(trim($search_org_id_1)){ 
			$arr_search_condition[] = "(c.ORG_ID_1 = $search_org_id_1 or d.ORG_ID_1 = $search_org_id_1 or e.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			$arr_search_condition[] = "(c.ORG_ID_2 = $search_org_id_2 or d.ORG_ID_2 = $search_org_id_2 or e.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
		} // end if
	}else if($select_org_structure==1) {
		if(trim($search_org_ass_id)){ 
			$arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
			$list_type_text .= "$search_org_ass_name";
		} // end if
		if(trim($search_org_ass_id_1)){ 
			$arr_search_condition[] = "(a.ORG_ID =  $search_org_ass_id_1)";
			$list_type_text .= " - $search_org_ass_name_1";
		} // end if
		if(trim($search_org_ass_id_2)){ 
			$arr_search_condition[] = "(a.ORG_ID =  $search_org_ass_id_2)";
			$list_type_text .= " - $search_org_ass_name_2";
		} // end if
	}

	if(in_array("SELECT", $list_type)){
            if(trim($SELECTED_PER_ID)){ 
                $cnt = 0;
                $SELECTED_LIST_ARR = explode(",",$SELECTED_PER_ID);
                $arr_condition .= "( a.PER_ID in ( ";
                for($i=0;$i<count($SELECTED_LIST_ARR);$i++){
                    if($cnt+1 < 1000 && $i+1 < count($SELECTED_LIST_ARR))  $comma = ",";
                    else $comma = "";
                    if($cnt < 1000){
                        $arr_condition .= $SELECTED_LIST_ARR[$i].$comma;
                        $cnt++;
                    }else{
                        $arr_condition .= " ) or a.PER_ID in ( ";
                        $arr_condition .= $SELECTED_LIST_ARR[$i].$comma;
                        $cnt = 0;
                    }
                }
                $arr_condition .=  " )) ";    
                $arr_search_condition[] = $arr_condition;
                // เดิม $arr_search_condition[] = "(a.PER_ID in (".$SELECTED_PER_ID."))"; //ติดปัญหาเรื่อง IN เกิน 1000 รายการ ฐานข้อมูล oracle ให้ in ได้แค่ 1000 รายการ
            }
	}elseif($list_type == "CONDITION"){
		//ทั้งข้าราชการ/ลูกจ้าง/พนง.ราชการ
		if(trim($search_pos_no)) $arr_search_condition[] = "(trim(c.POS_NO)='$search_pos_no' or trim(d.POEM_NO)='$search_pos_no' or trim(e.POEMS_NO)='$search_pos_no')";
		if(trim($search_name)) $arr_search_condition[] = "(a.PER_NAME like '$search_name%')";
		if(trim($search_surname)) $arr_search_condition[] = "(a.PER_SURNAME like '$search_surname%')";
		if(trim($search_min_level)){ 
			if($DPISDB=="odbc") $arr_search_condition[] = "(trim(a.LEVEL_NO) >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="oci8") $arr_search_condition[] = "(trim(a.LEVEL_NO) >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="mysql") $arr_search_condition[] = "(trim(a.LEVEL_NO) >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
		}
		if(trim($search_max_level)){ 
			if($DPISDB=="odbc") $arr_search_condition[] = "(trim(a.LEVEL_NO) <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="oci8") $arr_search_condition[] = "(trim(a.LEVEL_NO) <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="mysql") $arr_search_condition[] = "(trim(a.LEVEL_NO) <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
		}
		$arr_search_condition[] = "(a.PER_STATUS in (". implode(",", $search_per_status) ."))";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
        //if ($PER_ORDER_BY==1) $order_by = "STAX_ID ,SEQ_NO ASC"; 
	//else $order_by = "b.SAH_EFFECTIVEDAT  DESC";
        
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($MINISTRY_NAME?$MINISTRY_NAME:"".$DEPARTMENT_NAME?$DEPARTMENT_NAME:""."$KP7_TITLE"):$MINISTRY_NAME?$MINISTRY_NAME:"".$DEPARTMENT_NAME?$DEPARTMENT_NAME:""."$KP7_TITLE");
	$report_code = (($NUMBER_DISPLAY==2)?convert2thaidigit("R04061"):"R04061");
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
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$heading_width[POSITIONHIS][0] = "18";
	$heading_width[POSITIONHIS][1] = "56";      
	$heading_width[POSITIONHIS][2] = "52";
	$heading_width[POSITIONHIS][3] = "15";
	$heading_width[POSITIONHIS][4] = "25";
	$heading_width[POSITIONHIS][5] = "15";
	$heading_width[POSITIONHIS][6] = "24";

	/*$heading_width[SALARYHIS][0] = "40";
	$heading_width[SALARYHIS][1] = "80";
	$heading_width[SALARYHIS][2] = "80";
	$heading_width[SALARYHIS][3] = "20";
	$heading_width[SALARYHIS][4] = "25";
	$heading_width[SALARYHIS][5] = "30";*/
        /*Release 5.2.1.8 Begin*/
        $heading_width[SALARYHIS][0] = "18";
	$heading_width[SALARYHIS][1] = "56";      
	$heading_width[SALARYHIS][2] = "52";
	$heading_width[SALARYHIS][3] = "15";
	$heading_width[SALARYHIS][4] = "25";
	$heading_width[SALARYHIS][5] = "15";
	$heading_width[SALARYHIS][6] = "24";
        
	$heading_width[EXTRAHIS][0] = "25";
	$heading_width[EXTRAHIS][1] = "25";
	$heading_width[EXTRAHIS][2] = "125";
	$heading_width[EXTRAHIS][3] = "25";
        /*Release 5.2.1.8  End*/

	$heading_width[EXTRA_INCOMEHIS][0] = "25";
	$heading_width[EXTRA_INCOMEHIS][1] = "25";
	$heading_width[EXTRA_INCOMEHIS][2] = "125";
	$heading_width[EXTRA_INCOMEHIS][3] = "25";

	$heading_width[EDUCATE][0] = "60";
	$heading_width[EDUCATE][1] = "20";
	$heading_width[EDUCATE][2] = "90";
        $heading_width[EDUCATE][3] = "30";

	$heading_width[TRAINING][0] = "30";
	$heading_width[TRAINING][1] = "25";
	$heading_width[TRAINING][2] = "90";
	$heading_width[TRAINING][3] = "30";
	$heading_width[TRAINING][4] = "25";

	$heading_width[ABILITY][0] = "100";
	$heading_width[ABILITY][1] = "100";

	$heading_width[HEIR][0] = "100";
	$heading_width[HEIR][1] = "100";

	$heading_width[ABSENTHIS][0] = "55";
	$heading_width[ABSENTHIS][1] = "20";
	$heading_width[ABSENTHIS][2] = "125";

	$heading_width[PUNISHMENT][0] = "70";
	$heading_width[PUNISHMENT][1] = "75";
	$heading_width[PUNISHMENT][2] = "55";

	$heading_width[TIMEHIS][0] = "150";
	$heading_width[TIMEHIS][1] = "25";
	$heading_width[TIMEHIS][2] = "25";

	$heading_width[REWARDHIS][0] = "60";
	$heading_width[REWARDHIS][1] = "140";

	$heading_width[MARRHIS][0] = "60";
	$heading_width[MARRHIS][1] = "140";

	$heading_width[NAMEHIS][0] = "60";
	$heading_width[NAMEHIS][1] = "140";

	$heading_width[DECORATEHIS][0] = "50";
	$heading_width[DECORATEHIS][1] = "54";
	$heading_width[DECORATEHIS][2] = "32";
	$heading_width[DECORATEHIS][3] = "32";
	$heading_width[DECORATEHIS][4] = "32";

	$heading_width[SERVICEHIS][0] = "30";
	$heading_width[SERVICEHIS][1] = "50";
	$heading_width[SERVICEHIS][2] = "85";
	$heading_width[SERVICEHIS][3] = "35";

	$heading_width[SPECIALSKILLHIS][0] = "80";
	$heading_width[SPECIALSKILLHIS][1] = "120";

	$heading_width[ACTINGHIS][0] = "45";
	$heading_width[ACTINGHIS][1] = "16";      
	$heading_width[ACTINGHIS][2] = "18";
	$heading_width[ACTINGHIS][3] = "18";
	$heading_width[ACTINGHIS][4] = "12";
	$heading_width[ACTINGHIS][5] = "34";
	$heading_width[ACTINGHIS][6] = "30";
	$heading_width[ACTINGHIS][7] = "27";

	$heading_width[LICENSEHIS][0] = "70";
	$heading_width[LICENSEHIS][1] = "45";
	$heading_width[LICENSEHIS][2] = "30";
	$heading_width[LICENSEHIS][3] = "30";
	$heading_width[LICENSEHIS][4] = "25";

	function print_header($HISTORY_NAME){
		global $pdf, $heading_width;
		global $NUMBER_DISPLAY, $DEPARTMENT_TITLE, $ORG_TITLE;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		switch($HISTORY_NAME){
			case "POSITIONHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"วัน เดือน ปี",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ตำแหน่ง",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"ประเภทการเคลื่อนไหว",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"เลขที่",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"ระดับ",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][5] ,7,"อัตรา",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][6] ,7,"เอกสาร",'LTR',1,'C',1);
				
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"ตำแหน่ง",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][5] ,7,"เงินเดือน",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][6] ,7,"อ้างอิง",'LBR',1,'C',1);
				break;			
			case "SALARYHIS" :
				/*$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"วันที่",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ขั้น",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"ประเภทการเคลื่อนไหว",'LTBR',1,'C',1);*/
                                /*Release 5.2.1.8 */
                                $pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"วัน เดือน ปี",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ตำแหน่ง",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"ประเภทการเคลื่อนไหว",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"เลขที่",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"ระดับ",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][5] ,7,"อัตรา",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][6] ,7,"เอกสาร",'LTR',1,'C',1);
				
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"ตำแหน่ง",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][5] ,7,"เงินเดือน",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][6] ,7,"อ้างอิง",'LBR',1,'C',1);
                                /*Release 5.2.1.8 */
				break;	
                        case "EXTRAHIS" :
                            $pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"วันที่",'LTBR',0,'C',1);
                            $pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ถึงวันที่",'LTBR',0,'C',1);
                            $pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"ประเภทเงิน",'LTBR',0,'C',1);
                            $pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"จำนวน",'LTBR',1,'C',1);
                            break;
			case "EXTRA_INCOMEHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"วันที่",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ถึงวันที่",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"ประเภทเงินเพิ่มพิเศษ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"เงินเพิ่มพิเศษ",'LTBR',1,'C',1);
				break;			
			case "EDUCATE" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"สถานศึกษา",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ตั้งแต่ - ถึง",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"วุฒิที่ได้รับ",'LTR',0,'C',1);
                                $pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"ประเทศ",'LTR',1,'C',1);
				
				//แถวที่ 2
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"",'LBR',0,'C',1);	//ฝึกอบรม และดูงาน
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"(เดือน ปี)",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"ระบุสาขาวิชาเอก (ถ้ามี)",'LBR',0,'C',1);
                                $pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"",'LBR',1,'C',1);
				break;			
			case "TRAINING" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"สถานที่ฝึกอบรม",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ตั้งแต่ - ถึง",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"โครงการ, หลักสูตร",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"หน่วยงานที่จัด",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"ได้รับ",'LTBR',1,'C',1);
				break;			
			case "ABILITY" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"ด้านความสามารถพิเศษ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ความสามารถพิเศษ",'LTBR',1,'C',1);
				break;			
			case "HEIR" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"ความสัมพันธ์",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ชื่อ - สกุล",'LTBR',1,'C',1);
				break;			
			case "ABSENTHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"วันที่ลา",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"จำนวนวัน",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"ประเภทการลา",'LTBR',1,'C',1);
				break;			
			case "PUNISHMENT" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"ฐานความผิด",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"กรณีความผิด",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"วันที่รับโทษ",'LTBR',1,'C',1);
				break;			
			case "TIMEHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"เวลาทวีคูณ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"จำนวนวัน",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"จำนวนวันที่ไม่นับ",'LTBR',1,'C',1);
				break;			
			case "REWARDHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"วันที่ได้รับความดีความชอบ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ความดีความชอบ",'LTBR',1,'C',1);
				break;			
			case "MARRHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"ชื่อคู่สมรส",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"วันที่สมรส",'LTBR',1,'C',1);
				break;			
			case "NAMEHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"วันที่เปลี่ยนแปลง",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"คำนำหน้า - ชื่อ - สกุล",'LTBR',1,'C',1);
				break;			
			case "DECORATEHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("(1)"):"(1)"),'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("(2)"):"(2)"),'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("(3)"):"(3)"),'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("(4)"):"(4)"),'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("(5)"):"(5)"),'LTR',1,'C',1);
				
				//แถวที่ 2
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"ตำแหน่ง (อดีต-ปัจจุบัน",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ลำดับเครื่องราชฯ ที่ได้รับ",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"ว.ด.ป.",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"เครื่องราชฯ",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"เครื่องราชฯ",'LBR',1,'C',1);
				
				//แถวที่ 3
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"เฉพาะปีที่ได้รับ",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"พระราชทานแล้ว",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"ที่ได้รับ",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("ตาม (2)"):"ตาม (2)"),'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("ตาม (2)"):"ตาม (2)"),'LBR',1,'C',1);
				
				//แถวที่ 4
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"พระราชทานเครื่องราชฯ)",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"จากชั้นรองไปชั้นสูงตาม ".(($NUMBER_DISPLAY==2)?convert2thaidigit("(1)"):"(1)"),'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"พระราชทานตาม ".(($NUMBER_DISPLAY==2)?convert2thaidigit("(2)"):"(2)"),'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"รับมอบเมื่อ ",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"ส่งคืนเมื่อ ",'LBR',1,'C',1);
				break;			
			case "SERVICEHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"วัน เดือน ปี",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ประเภทราชการพิเศษ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"หัวข้อราชการพิเศษ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"หนังสือ",'LTBR',1,'C',1);
				break;			
			case "SPECIALSKILLHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"ด้านความเชี่ยวชาญพิเศษ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"เน้นทาง",'LTBR',1,'C',1);
				break;			
			case "ACTINGHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"ประเภท",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"เลขที่คำสั่ง",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"ตั้งแต่วันที่",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"ถึงวันนี้",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"เลขที่",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][5] ,7,"ตำแหน่ง",'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][6] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("$DEPARTMENT_TITLE"):"$DEPARTMENT_TITLE"),'LTR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][7] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("$ORG_TITLE"):"$ORG_TITLE"),'LTR',1,'C',1);
				
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"การเคลื่อนไหว",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"ตำแหน่ง",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][5] ,7,"ในสายงาน",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][6] ,7,"",'LBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][7] ,7,"",'LBR',1,'C',1);
				break;	
			case "LICENSEHIS" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"ใบอนุญาตประกอบวิชาชีพ",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"ประเภท/ระดับของใบอนุญาต",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"สาขา",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"เลขที่ใบอนุญาต",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"วันที่หมดอายุ",'LTBR',1,'C',1);
				break;			
		} // end switch case
	} // function		

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE,a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID,
											a.OT_CODE,a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, 
											a.PER_MOTHERSURNAME, c.POS_NO_NAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO_NAME as EMP_POS_NO_NAME, d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO_NAME as EMPSER_POS_NO_NAME, e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2
				 from			PER_PRENAME b inner join 
				 				(
									(
										( 	
											(
										PER_PERSONAL a 
										left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
									) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
								) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
							) left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
						) on (trim(a.PN_CODE)=trim(b.PN_CODE))
								$search_condition
				 order by		a.PER_NAME, a.PER_SURNAME ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "select			a.ORG_ID_1 as ORG_ID_1_ASS ,a.ORG_ID_2 as ORG_ID_2_ASS, a.ORG_ID as ORG_ID_ASS, a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE,a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID,
											a.OT_CODE,a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, 
											a.PER_MOTHERSURNAME, c.POS_NO_NAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO_NAME as EMP_POS_NO_NAME, d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO_NAME as EMPSER_POS_NO_NAME, e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2
						from			PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, PER_LEVEL f
						where		trim(a.PN_CODE)=trim(b.PN_CODE) and 
										a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+) and a.LEVEL_NO=f.LEVEL_NO(+)
										$search_condition
						order by	a.PER_NAME, a.PER_SURNAME "; 
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE,a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID,
											a.OT_CODE,a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, 
											a.PER_MOTHERSURNAME, c.POS_NO_NAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO_NAME as EMP_POS_NO_NAME, d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO_NAME as EMPSER_POS_NO_NAME, e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2
					 	from		PER_PERSONAL a inner join PER_PRENAME b on (trim(a.PN_CODE)=trim(b.PN_CODE))
																  left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
																  left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
																  left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
																  left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
								$search_condition
				 order by			a.PER_NAME, a.PER_SURNAME ";
	}
	/** 
	if($select_org_structure==1) {
		$cmd = str_replace("c.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	*/
	$count_data = $db_dpis->send_cmd($cmd);
    //    echo "<pre>$cmd<br>";
//	$db_dpis->show_error();

	if($count_data){
		$data_count = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$PER_ID = $data[PER_ID];
			$PER_TYPE = $data[PER_TYPE];
			
			if($PER_TYPE==1){
				$POS_ID = $data[POS_ID];
				$POS_NO = $data[POS_NO_NAME].$data[POS_NO];
				$PL_CODE = trim($data[PL_CODE]);
				$PT_CODE = trim($data[PT_CODE]);
				$ORG_ID = $data[ORG_ID];
				$ORG_ID_1 = $data[ORG_ID_1];
				$ORG_ID_2 = $data[ORG_ID_2];
				
				$ORG_ID_ASS = $data[ORG_ID_ASS];
				$ORG_ID_1_ASS = $data[ORG_ID_1_ASS];
				$ORG_ID_2_ASS = $data[ORG_ID_2_ASS];
				if($select_org_structure==1) { 
					$ORG_ID = $ORG_ID_ASS;
					$ORG_ID_1 = $ORG_ID_1_ASS;
					$ORG_ID_2 = $ORG_ID_2_ASS;
				}
				$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PL_NAME]);

				$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE) = '$PT_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PT_NAME = trim($data2[PT_NAME]);
			}elseif($PER_TYPE==2){
				$POS_ID = $data[POEM_ID];
				$POS_NO = $data[EMP_POS_NO_NAME].$data[EMP_POS_NO];
				$PL_CODE = trim($data[EMP_PL_CODE]);
				$ORG_ID = $data[EMP_ORG_ID];
				$ORG_ID_1 = $data[EMP_ORG_ID_1];
				$ORG_ID_2 = $data[EMP_ORG_ID_2];
				$ORG_ID_ASS = $data[ORG_ID_ASS];
				$ORG_ID_1_ASS = $data[ORG_ID_1_ASS];
				$ORG_ID_2_ASS = $data[ORG_ID_2_ASS];
				if($select_org_structure==1) { 
					$ORG_ID  = $ORG_ID_ASS;
					$ORG_ID_1 = $ORG_ID_1_ASS;
					$ORG_ID_2 = $ORG_ID_2_ASS;
				}

				$cmd = " select PN_NAME from PER_POS_NAME where trim(PN_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PN_NAME]);
			}elseif($PER_TYPE==3){
				$POS_ID = $data[POEMS_ID];
				$POS_NO = $data[EMPSER_POS_NO_NAME].$data[EMPSER_POS_NO];
				$PL_CODE = trim($data[EMPSER_PL_CODE]);
				$ORG_ID = $data[EMPSER_ORG_ID];
				$ORG_ID_1 = $data[EMPSER_ORG_ID_1];
				$ORG_ID_2 = $data[EMPSER_ORG_ID_2];
				$ORG_ID_ASS = $data[ORG_ID_ASS];
				$ORG_ID_1_ASS = $data[ORG_ID_1_ASS];
				$ORG_ID_2_ASS = $data[ORG_ID_2_ASS];
				if($select_org_structure==1) { 
					$ORG_ID = $ORG_ID_ASS;
					$ORG_ID_1 = $ORG_ID_1_ASS;
					$ORG_ID_2 = $ORG_ID_2_ASS;
				}
				$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[EP_NAME]);
			} 

			// ข้อมูลประเภทข้าราชการ
			$OT_CODE = trim($data[OT_CODE]);
			$cmd = " select OT_NAME from PER_OFF_TYPE where trim(OT_CODE)='$OT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$OT_NAME = trim($data_dpis2[OT_NAME]);
			$ORG_NAME = "";
			if($ORG_ID){
			 $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID ";
			 if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			 $db_dpis2->send_cmd($cmd);
//			 $db_dpis2->show_error();
			 $data2 = $db_dpis2->get_array();
			 $ORG_NAME = trim($data2[ORG_NAME]);
			}
			$ORG_NAME_1 = "";
			if($ORG_ID_1){
			 $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_1 ";
			 if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			 $db_dpis2->send_cmd($cmd);
//			 $db_dpis2->show_error();
			 $data2 = $db_dpis2->get_array();
			 $ORG_NAME_1 = trim($data2[ORG_NAME]);
			}
			$ORG_NAME_2 = "";
			if($ORG_ID_2){
		 	 $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_2";
			 if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			 $db_dpis2->send_cmd($cmd);
//			 $db_dpis2->show_error();
			 $data2 = $db_dpis2->get_array();
			 $ORG_NAME_2 = trim($data2[ORG_NAME]);
			}
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$LEVEL_NAME = trim(str_replace("ระดับ","",$data[LEVEL_NAME]));
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$FULLNAME = ($PN_NAME)."$PER_NAME $PER_SURNAME";			
			$PER_CARDNO = $data[PER_CARDNO];			
			$img_file = "";
			if($PER_CARDNO && file_exists($IMG_PATH.$PER_CARDNO.".jpg")) $img_file = $IMG_PATH.$PER_CARDNO.".jpg";			

			$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
			$PER_RETIREDATE = show_date_format($data[PER_RETIREDATE],$DATE_DISPLAY);
			
			$PER_SALARY = $data[PER_SALARY];
			$PER_MGTSALARY = $data[PER_MGTSALARY];

			//วันบรรจุ
			$PER_STARTDATE = show_date_format($data[PER_STARTDATE],$DATE_DISPLAY);
			//วันที่เข้าส่วนราชการ
			$PER_OCCUPYDATE = show_date_format($data[PER_OCCUPYDATE],$DATE_DISPLAY);
		
			// =====  ข้อมูลบิดา และมารดา =====
			$cmd = "	select	PN_CODE, FML_NAME, FML_SURNAME
								from		PER_FAMILY
								where	PER_ID=$PER_ID and FML_TYPE=1 ";	
			$db_dpis2->send_cmd($cmd);
	//		$db_dpis2->show_error();
			$data1 = $db_dpis2->get_array();		
			$PN_CODE_F = trim($data1[PN_CODE]);
			$PER_FATHERNAME = $data1[FML_NAME];
			$PER_FATHERSURNAME = $data1[FML_SURNAME];
			if (!$PER_FATHERNAME) {
				$PN_CODE_F = trim($data[PN_CODE_F]);
				$PER_FATHERNAME = trim($data[PER_FATHERNAME]);
				$PER_FATHERSURNAME = trim($data[PER_FATHERSURNAME]);
			}
			$PN_NAME_F = "";
			if($PN_CODE_F){
			 $cmd = "select PN_NAME from PER_PRENAME WHERE PN_CODE = $PN_CODE_F ";
			 $db_dpis2->send_cmd($cmd);
			 $data_dpis2 = $db_dpis2->get_array();
			 $PN_NAME_F = trim($data_dpis2[PN_NAME]);
			}
			$FATHERNAME = ($PN_NAME_F)."$PER_FATHERNAME $PER_FATHERSURNAME";
		
			$cmd = "	select	PN_CODE, FML_NAME, FML_SURNAME
								from		PER_FAMILY
								where	PER_ID=$PER_ID and FML_TYPE=2 ";	
			$db_dpis2->send_cmd($cmd);
	//		$db_dpis2->show_error();
//	echo "<pre>".$cmd;
			$data1 = $db_dpis2->get_array();		
			$PN_CODE_M = trim($data1[PN_CODE]);
			$PER_MOTHERNAME = $data1[FML_NAME];
			$PER_MOTHERSURNAME = $data1[FML_SURNAME];
			if (!$PER_MOTHERNAME) {
				$PN_CODE_M = trim($data[PN_CODE_M]);	
				$PER_MOTHERNAME = trim($data[PER_MOTHERNAME]);
				$PER_MOTHERSURNAME = trim($data[PER_MOTHERSURNAME]);
			}
			$PN_NAME_M = "";
			if($PN_CODE_M){
			$cmd = "select PN_NAME from PER_PRENAME WHERE PN_CODE = $PN_CODE_M ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PN_NAME_M = trim($data_dpis2[PN_NAME]);
			}
			$MOTHERNAME = ($PN_NAME_M)."$PER_MOTHERNAME $PER_MOTHERSURNAME";

			// =====  ข้อมูลคู่สมรส =====
			$SHOW_SPOUSE = "";
			$cmd = "	select 	PN_CODE, MAH_NAME, DV_CODE, MR_CODE 		
							from		PER_MARRHIS 
							where	PER_ID=$PER_ID 	
							order by	MAH_SEQ desc ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PN_CODE = trim($data_dpis2[PN_CODE]);
			$MAH_NAME = trim($data_dpis2[MAH_NAME]);
			$DV_CODE = trim($data_dpis2[DV_CODE]);
			$MR_CODE = trim($data_dpis2[MR_CODE]);
			$PN_NAME = "";
			$SHOW_SPOUSE = "";
			if ($MR_CODE==2) {
				$cmd = "select PN_NAME from PER_PRENAME WHERE PN_CODE = $PN_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$PN_NAME = trim($data_dpis2[PN_NAME]);
                                $SHOW_SPOUSE = $PN_NAME.$MAH_NAME;
			}
			

			if (!$SHOW_SPOUSE) {
				$cmd = "	select PN_NAME, FML_NAME, FML_SURNAME from PER_FAMILY	a, PER_PRENAME b
									where a.PN_CODE = b.PN_CODE(+) and PER_ID=$PER_ID and FML_TYPE = 3 and (MR_CODE is NULL or trim(MR_CODE) not in ('3','4')) ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$SHOW_SPOUSE = trim($data_dpis2[PN_NAME]).trim($data_dpis2[FML_NAME])." ".trim($data_dpis2[FML_SURNAME]);
			}
			
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			/***$pdf->Cell(200,7,"เลขที่ตำแหน่ง  ".($POS_NO?$POS_NO:"-"),0,1,"L");

			if($img_file){
				$image_x = ($pdf->x + 170);		$image_y = ($pdf->y - 7);		$image_w = 30;			$image_h = 40;
				$pdf->Image($img_file, $image_x, $image_y, $image_w, $image_h);
			} // end if

			$pdf->Cell(95,7,$pdf->Text_print_optimize("ตำแหน่ง  ".(trim($PL_NAME)?($PL_NAME ." ". $LEVEL_NAME . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):" ".$LEVEL_NAME), 100),0,0,"L");
			$pdf->Cell(85,7,$pdf->Text_print_optimize("  สังกัด  ".($ORG_NAME?$ORG_NAME:"-"), 100),0,1,"L");
			
			if($ORG_ID_1){
				$pdf->Cell(85,7,"",0,0,"L");
				$pdf->Cell(85,7,$pdf->Text_print_optimize($ORG_NAME_1, 100),0,1,"L");
			} // end if

			if($ORG_ID_2){
				$pdf->Cell(85,7,"",0,0,"L");
				$pdf->Cell(85,7,$pdf->Text_print_optimize($ORG_NAME_2, 100),0,1,"L");
			} // end if
			***/

			$pdf->Cell(60 ,7,"ชื่อ  ".(($NUMBER_DISPLAY==2)?convert2thaidigit($FULLNAME):$FULLNAME),1,0,"L");
			$pdf->Cell(70 ,7,"ชื่อคู่สมรส ".($SHOW_SPOUSE?(($NUMBER_DISPLAY==2)?convert2thaidigit($SHOW_SPOUSE):$SHOW_SPOUSE):"-"),1,0,"L");
			//$pdf->Cell(70 ,7,"วันสั่งบรรจุ  ".($PER_OCCUPYDATE?$PER_OCCUPYDATE:"-"),1,1,"L");
			$pdf->Cell(70 ,7,"วันสั่งบรรจุ  ".($PER_STARTDATE?(($NUMBER_DISPLAY==2)?convert2thaidigit($PER_STARTDATE):$PER_STARTDATE):"-"),1,1,"L");

			$pdf->Cell(60 ,7,"วัน เดือน ปี เกิด  ".($PER_BIRTHDATE?(($NUMBER_DISPLAY==2)?convert2thaidigit($PER_BIRTHDATE):$PER_BIRTHDATE):"-"),1,0,"L");
			$pdf->Cell(70 ,7,"ชื่อบิดา  ".($FATHERNAME?(($NUMBER_DISPLAY==2)?convert2thaidigit($FATHERNAME):$FATHERNAME):"-"),1,0,"L");
			$pdf->Cell(70 ,7,"วันเริ่มปฏิบัติราชการ  ".($PER_STARTDATE?(($NUMBER_DISPLAY==2)?convert2thaidigit($PER_STARTDATE):$PER_STARTDATE):"-"),1,1,"L");

			$pdf->Cell(60 ,7,"วันเกษียณอายุ  ".($PER_RETIREDATE?(($NUMBER_DISPLAY==2)?convert2thaidigit($PER_RETIREDATE):$PER_RETIREDATE):"-"),1,0,"L");
			$pdf->Cell(70 ,7,"ชื่อมารดา  ".($MOTHERNAME?(($NUMBER_DISPLAY==2)?convert2thaidigit($MOTHERNAME):$MOTHERNAME):"-"),1,0,"L");
			$pdf->Cell(70 ,7,"ประเภท".($OT_NAME?(($NUMBER_DISPLAY==2)?convert2thaidigit($OT_NAME):$OT_NAME):"-"),1,1,"L");

			$pdf->AutoPageBreak = false;

			//สำหรับ กพ. 7
			$ORG_NAME_1 = "";		$ORG_NAME_2 = "";
			$POH_EFFECTIVEDATE = "";	$PL_NAME = "";	$TMP_PL_NAME = "";
			$LEVEL_NO = "";	$POH_POS_NO = "";	$POH_SALARY = "";
			$SAH_EFFECTIVEDATE = "";	$SAH_SALARY = "";	$MOV_NAME = "";

			//########################
			//ประวัติการดำรงตำแหน่งข้าราชการ
			//########################
			if($DPISDB=="odbc"){
				$cmd = " select		a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_ASS_ORG, a.POH_EFFECTIVEDATE, a.MOV_CODE, d.PL_NAME, i.PN_NAME, 
													a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_LEVEL, a.POH_POS_NO_NAME, a.POH_POS_NO, a.POH_SALARY, a.POH_DOCNO, a.PT_CODE, a.PM_CODE, 
													f.PM_NAME, a.EP_CODE, a.TP_CODE, a.POH_SEQ_NO
								   from			(
														(
															(
																PER_POSITIONHIS a
																left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
															) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
														) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
													)  left join PER_POS_NAME i on (a.PN_CODE=i.PN_CODE)
								   where		a.PER_ID=$PER_ID
								   order by	LEFT(a.POH_EFFECTIVEDATE,10) desc, a.POH_SEQ_NO desc  
								   ";							   
			}elseif($DPISDB=="oci8"){
				$cmd = "select a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_ASS_ORG, a.POH_EFFECTIVEDATE, 
                                            a.MOV_CODE, d.PL_NAME, i.PN_NAME, a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_LEVEL, a.POH_POS_NO_NAME, 
                                            a.POH_POS_NO, a.POH_SALARY, a.POH_DOCNO, a.PT_CODE, a.PM_CODE, f.PM_NAME, a.EP_CODE, a.TP_CODE, 
                                            a.POH_SEQ_NO,a.POH_DOCDATE,a.POH_PL_NAME,a.POH_PM_NAME
                                        from PER_POSITIONHIS a, PER_LINE d, PER_MGT f, PER_LEVEL g, PER_POS_NAME i
                                        where a.PER_ID=$PER_ID 
                                            and a.PL_CODE=d.PL_CODE(+) 
                                            and a.PM_CODE=f.PM_CODE(+) 
                                            and a.LEVEL_NO=g.LEVEL_NO(+) 
                                            and a.PN_CODE=i.PN_CODE(+)
                                        order by SUBSTR(a.POH_EFFECTIVEDATE,1,10) desc, a.POH_SEQ_NO desc ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select		a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_ASS_ORG, a.POH_EFFECTIVEDATE, a.MOV_CODE, d.PL_NAME, i.PN_NAME, 
													a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_LEVEL, a.POH_POS_NO_NAME, a.POH_POS_NO, a.POH_SALARY, a.POH_DOCNO, a.PT_CODE, a.PM_CODE, 
													f.PM_NAME, a.EP_CODE, a.TP_CODE, a.POH_SEQ_NO
								   from			(
														(
															(
																PER_POSITIONHIS a
																left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
															) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
														) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
													)  left join PER_POS_NAME i on (a.PN_CODE=i.PN_CODE)
								   where		a.PER_ID=$PER_ID
								   order by	LEFT(a.POH_EFFECTIVEDATE,10) desc, a.POH_SEQ_NO desc
								   ";
			} // end if
			$count_positionhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
			//echo "<pre>$cmd</pre><br>";
			
			if($count_positionhis){
				$positionhis_count = 0;
				while($data2 = $db_dpis2->get_array()){
					$positionhis_count++;
					$POH_EFFECTIVEDATE = trim(substr($data2[POH_EFFECTIVEDATE],0,10));
					$POH_EFFECTIVEDATE = ($POH_EFFECTIVEDATE?$POH_EFFECTIVEDATE:"-");
					$LEVEL_NO = trim($data2[LEVEL_NO]);
                                        $LEVEL_NAME_V2 = $data2[POSITION_LEVEL]; /*Release 5.2.1.8*/
					if( ("$PM_CODE $PL_NAME $LEVEL_NO $POH_POS_NO") != (trim($data2[PM_CODE])." ".trim($data2[PL_NAME])." ".$LEVEL_NO." ".trim($data2[POH_POS_NO_NAME]).trim($data2[POH_POS_NO]))){
						$PL_NAME = trim($data2[PL_NAME]);
						$PN_NAME = trim($data2[PN_NAME]);
						$LEVEL_NAME = trim(str_replace("ระดับ","",$data2[POSITION_LEVEL]));
						$PT_CODE = trim($data2[PT_CODE]);
						if($PT_CODE) { 
							$cmd = " select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE' ";
							$db_dpis3->send_cmd($cmd);
							$data3 = $db_dpis3->get_array();
							$PT_NAME = trim($data3[PT_NAME]);
						}
						$PM_CODE = trim($data2[PM_CODE]);
						$PM_NAME = trim($data2[PM_NAME]);
						$EP_CODE = trim($data2[EP_CODE]);
						$TP_CODE = trim($data2[TP_CODE]);
                                                
                                                //  Release 5.2.1.21
                                                $POH_PL_NAME = trim($data2[POH_PL_NAME]);
                                                $POH_PM_NAME = trim($data2[POH_PM_NAME]);
                                                // End  Release 5.2.1.21

						if($PL_NAME) {  
							//$TMP_PL_NAME = $PL_NAME ." x". $LEVEL_NAME . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");/*เดิม*/
                                                        $TMP_PL_NAME = $PL_NAME ." " . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");/*เดิม*/
						} elseif($PN_NAME) { 
							$TMP_PL_NAME = trim($PN_NAME); 
						} elseif($EP_CODE) { 
							$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE='$EP_CODE' ";
							$db_dpis3->send_cmd($cmd);
							$data3 = $db_dpis3->get_array();
							$TMP_PL_NAME = $data3[EP_NAME];
						} elseif($TP_CODE) { 
							$cmd = " select TP_NAME from PER_TEMP_POS_NAME where TP_CODE='$TP_CODE' ";
							$db_dpis3->send_cmd($cmd);
							$data3 = $db_dpis3->get_array();
							$TMP_PL_NAME = $data3[TP_NAME];
						}else{   //Release 5.2.1.21
                                                        $TMP_PL_NAME = (trim($POH_PL_NAME)?($POH_PL_NAME . $LEVEL_NAME . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):$LEVEL_NAME);
                                                        if($POH_PM_NAME){
                                                            $TMP_PL_NAME = $POH_PM_NAME."\n($TMP_PL_NAME)"; 
                                                        }
                                                } // End  Release 5.2.1.21
						if($PM_CODE && $PM_CODE!="9999") $TMP_PL_NAME = $PM_NAME." ($TMP_PL_NAME)";
					}else{
                                            //$TMP_PL_NAME = $PL_NAME ." x". trim(str_replace("ระดับ","",$LEVEL_NAME_V2)) . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");
                                            $TMP_PL_NAME = $PL_NAME;/*Release 5.2.1.8*/
                                        }
					$TMP_PL_NAME = (trim($TMP_PL_NAME)?trim($TMP_PL_NAME):"-");
                                        //echo $TMP_PL_NAME.'-'.$LEVEL_NAME_V2.'<br>';
					$POH_POS_NO = trim($data2[POH_POS_NO_NAME]).trim($data2[POH_POS_NO]);	
					$POH_POS_NO = ($POH_POS_NO?$POH_POS_NO:"-");
					$POH_SALARY = $data2[POH_SALARY];		
					$POH_SALARY = ($POH_SALARY?$POH_SALARY:"-");
					$POH_DOCNO = (trim($data2[POH_DOCNO]))? $data2[POH_DOCNO] : "-" ;
					$MOV_CODE = trim($data2[MOV_CODE]);
                                        
                                        $POH_DOCDATE = trim(substr($data2[POH_DOCDATE],0,10));
					$POH_DOCDATE = ($POH_DOCDATE?$POH_DOCDATE:"-");

					//หาประเภทการเคลื่อนไหวของประวัติการดำรงตำแหน่งข้าราชการ
					$cmd = " select MOV_NAME from PER_MOVMENT pcd where MOV_CODE='$MOV_CODE' ";
					$db_dpis3->send_cmd($cmd);
					//$db_dpis3->show_error();
					$data3 = $db_dpis3->get_array();
					$MOV_NAME = $data3[MOV_NAME];

					$POH_SEQ_NO = trim($data2[POH_SEQ_NO]);
					//เก็บลง array ของ POSTION HIS
                                        if($chk_order_date_command=='Y'){ //ต้องการเรียงลำดับตามวันที่ลงนามในคำสั่ง http://dpis.ocsc.go.th/Service/node/1304
                                            $ARR_POSITIONHIS[$PER_ID][] = array(
                                                'DOCDATE'=>$POH_DOCDATE,
                                                'DATE'=>$POH_EFFECTIVEDATE,
                                                'POH_SEQ_NO'=>$POH_SEQ_NO,
                                                'MOVE'=>$MOV_NAME,
                                                'POS_NAME'=>$TMP_PL_NAME,
                                                'POS_NO'=>$POH_POS_NO,
                                                'LEVEL'=>$LEVEL_NO,
                                                'SALARY'=>$POH_SALARY,
                                                'DOC_NO'=>$POH_DOCNO,
                                            );
                                        }else{
                                            $ARR_POSITIONHIS[$PER_ID][] = array(
                                                'DATE'=>$POH_EFFECTIVEDATE,
                                                'POH_SEQ_NO'=>$POH_SEQ_NO,
                                                'MOVE'=>$MOV_NAME,
                                                'POS_NAME'=>$TMP_PL_NAME,
                                                'POS_NO'=>$POH_POS_NO,
                                                'LEVEL'=>$LEVEL_NO,
                                                'SALARY'=>$POH_SALARY,
                                                'DOC_NO'=>$POH_DOCNO,
                                            );
                                        }
					
				} // end while
			} //end if 

			//########################
			//ประวัติการเลื่อนขั้นเงินเดือน
			//########################
			if($DPISDB=="odbc"){
				$cmd = " select			LEFT(b.SAH_EFFECTIVEDATE,10) as SAH_EFFECTIVEDATE, c.MOV_NAME, b.SAH_SALARY, b.SAH_DOCNO, 
													b.LEVEL_NO, b.SAH_POSITION, b.SAH_POS_NO_NAME, b.SAH_POS_NO, 1 AS POH_SEQ_NO
								 from			PER_SALARYHIS b
								 inner join 	PER_MOVMENT c	 on	b.MOV_CODE=c.MOV_CODE					
								 where		b.PER_ID=$PER_ID
								 order by		b.SAH_EFFECTIVEDATE desc  ";							   
			}elseif($DPISDB=="oci8"){
				$cmd = " select SUBSTR(b.SAH_EFFECTIVEDATE,1,10) as SAH_EFFECTIVEDATE, c.MOV_NAME, b.SAH_SALARY, b.SAH_DOCNO, 
                                            b.LEVEL_NO, b.SAH_POSITION, b.SAH_POS_NO_NAME, b.SAH_POS_NO, 1 AS POH_SEQ_NO,
                                            b.SAH_DOCDATE
                                        from PER_SALARYHIS b, PER_MOVMENT c 
                                        where b.PER_ID=$PER_ID and b.MOV_CODE=c.MOV_CODE 
                                        order by b.SAH_EFFECTIVEDATE desc ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			LEFT(b.SAH_EFFECTIVEDATE,10) as SAH_EFFECTIVEDATE, c.MOV_NAME, b.SAH_SALARY, b.SAH_DOCNO, 
													b.LEVEL_NO, b.SAH_POSITION, b.SAH_POS_NO_NAME, b.SAH_POS_NO, 1 AS POH_SEQ_NO
								 from			PER_SALARYHIS b
								 inner join 	PER_MOVMENT c	 on	b.MOV_CODE=c.MOV_CODE					
								 where		b.PER_ID=$PER_ID
								 order by		b.SAH_EFFECTIVEDATE desc ";
			} // end if
			$count_salaryhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
                        //echo "<pre>$cmd</pre><br>";
			if($count_salaryhis){
				$salaryhis_count = 0;
				while($data2 = $db_dpis2->get_array()){
					$salaryhis_count++;
					$SAH_EFFECTIVEDATE = trim($data2[SAH_EFFECTIVEDATE]);
					$SAH_EFFECTIVEDATE = ($SAH_EFFECTIVEDATE?$SAH_EFFECTIVEDATE:"-");
					$MOV_NAME = trim($data2[MOV_NAME]);		
					$MOV_NAME = (trim($MOV_NAME)?trim($MOV_NAME):"-");
					$SAH_SALARY = $data2[SAH_SALARY];		
					$SAH_SALARY = ($SAH_SALARY?$SAH_SALARY:"-");
					$SAH_DOCNO = (trim($data2[SAH_DOCNO]))? $data2[SAH_DOCNO] : "-" ;
					$POH_POS_NO = trim($data2[SAH_POS_NO_NAME]).trim($data2[SAH_POS_NO]);				
					$POH_POS_NO =($POH_POS_NO?$POH_POS_NO:"-");
					$LEVEL_NO = trim($data2[LEVEL_NO]);	
					$TMP_PL_NAME = trim($data2[SAH_POSITION]);	
					$TMP_PL_NAME = (trim($TMP_PL_NAME)?trim($TMP_PL_NAME):"-");
					$POH_SEQ_NO = trim($data2[POH_SEQ_NO]);	
                                        
                                        $SAH_DOCDATE = trim($data2[SAH_DOCDATE]);
					$SAH_DOCDATE = ($SAH_DOCDATE?$SAH_DOCDATE:"-");
                                        if($chk_order_date_command=='Y'){ //ต้องการเรียงลำดับตามวันที่ลงนามในคำสั่ง http://dpis.ocsc.go.th/Service/node/1304
                                            $ARR_SALARYHIS[$PER_ID][] = array(
                                                'DOCDATE'=>$SAH_DOCDATE,
                                                'DATE'=>$SAH_EFFECTIVEDATE,
                                                'POH_SEQ_NO'=>$POH_SEQ_NO,
                                                'MOVE'=>$MOV_NAME,
                                                'POS_NAME'=>$TMP_PL_NAME,
                                                'POS_NO'=>$POH_POS_NO,
                                                'LEVEL'=>$LEVEL_NO,
                                                'SALARY'=>$SAH_SALARY,
                                                'DOC_NO'=>$SAH_DOCNO
                                            );
                                        }else{
                                            $ARR_SALARYHIS[$PER_ID][] = array(
                                                'DATE'=>$SAH_EFFECTIVEDATE,
                                                'POH_SEQ_NO'=>$POH_SEQ_NO,
                                                'MOVE'=>$MOV_NAME,
                                                'POS_NAME'=>$TMP_PL_NAME,
                                                'POS_NO'=>$POH_POS_NO,
                                                'LEVEL'=>$LEVEL_NO,
                                                'SALARY'=>$SAH_SALARY,
                                                'DOC_NO'=>$SAH_DOCNO
                                            );	
                                        }
					//เก็บลง array ของ SALARYHIS
																							
				} // end while
			}// end if

			//######################################
			//รวมประวัติการดำรงตำแหน่ง + การเลื่อนขั้นเงินเดือน
			//######################################
			//array_multisort($ARR_POSITIONHIS[$PER_ID], SORT_ASC, $ARR_SALARYHIS[$PER_ID], SORT_ASC);
			//$ARRAY_POH_SAH[$PER_ID] = array_merge_recursive($ARR_POSITIONHIS[$PER_ID] , $ARR_SALARYHIS[$PER_ID]);
                        $ARRAY_POH_SAH[$PER_ID] = array_merge_recursive($ARR_POSITIONHIS[$PER_ID]);
                        
                        $ARRAY_SALARYHIS[$PER_ID] = $ARR_SALARYHIS[$PER_ID];/*Release 5.2.1.8 */
			unset($ARR_POSITIONHIS);
			unset($ARR_SALARYHIS);
		
                        if($debug==1){
                            print("<pre>");
                            print_r($ARRAY_SALARYHIS);
                            print("</pre>");
                        }
			// เรียงข้อมูล ตามวันที่ / เงินเดือน น้อยไปมาก
			/*print("<pre>");
			print_r($ARRAY_POH_SAH);
			print("</pre>");*/
			/*foreach ($ARRAY_POH_SAH[$PER_ID] as $key => $value) {		//กรณีที่วันที่เดียวกัน แต่ต้องเอาเงินเดือนน้อยกว่าแสดงก่อน
				$DATE[$key]  = $value['DATE'];
				$MOVE[$key]  = $value['MOVE'];
				$POS_NAME[$key] = $value['POS_NAME'];
				$POS_NO[$key]  = $value['POS_NO'];
				$LEVEL[$key]  = $value['LEVEL'];
				$SALARY[$key] = $value['SALARY'];
				$DOC_NO[$key]  = $value['DOC_NO'];
			} // end foreach
			array_multisort($DATE, SORT_ASC, $SALARY, SORT_ASC, $ARRAY_POH_SAH);*/
                        
                        /*Release 5.2.1.8 ส่วนของประวัติการเลื่อนเงินเดือน Begin*/
                        for($in=0; $in < count($ARRAY_SALARYHIS[$PER_ID]);$in++){
                                //เก็บค่าวันที่
				if($chk_order_date_command=='Y'){ //ต้องการเรียงลำดับตามวันที่ลงนามในคำสั่ง http://dpis.ocsc.go.th/Service/node/1304
                                    $DATE_SALARYHIS[] = array( 
                                                    'DOCDATE'=>$ARRAY_SALARYHIS[$PER_ID][$in]['DOCDATE'],
                                                    'DATE'=>$ARRAY_SALARYHIS[$PER_ID][$in]['DATE'],
                                                    'POH_SEQ_NO'=>$ARRAY_SALARYHIS[$PER_ID][$in]['POH_SEQ_NO'],
                                                    'MOVE'=>$ARRAY_SALARYHIS[$PER_ID][$in]['MOVE'],
                                                    'POS_NAME'=>$ARRAY_SALARYHIS[$PER_ID][$in]['POS_NAME'],
                                                    'POS_NO'=>$ARRAY_SALARYHIS[$PER_ID][$in]['POS_NO'],
                                                    'LEVEL'=>$ARRAY_SALARYHIS[$PER_ID][$in]['LEVEL'],
                                                    'SALARY'=>$ARRAY_SALARYHIS[$PER_ID][$in]['SALARY'],
                                                    'DOC_NO'=>$ARRAY_SALARYHIS[$PER_ID][$in]['DOC_NO']
                                                );
                                }else{
                                    $DATE_SALARYHIS[] = array('DATE'=>$ARRAY_SALARYHIS[$PER_ID][$in]['DATE'],
                                                    'POH_SEQ_NO'=>$ARRAY_SALARYHIS[$PER_ID][$in]['POH_SEQ_NO'],
                                                    'MOVE'=>$ARRAY_SALARYHIS[$PER_ID][$in]['MOVE'],
                                                    'POS_NAME'=>$ARRAY_SALARYHIS[$PER_ID][$in]['POS_NAME'],
                                                    'POS_NO'=>$ARRAY_SALARYHIS[$PER_ID][$in]['POS_NO'],
                                                    'LEVEL'=>$ARRAY_SALARYHIS[$PER_ID][$in]['LEVEL'],
                                                    'SALARY'=>$ARRAY_SALARYHIS[$PER_ID][$in]['SALARY'],
                                                    'DOC_NO'=>$ARRAY_SALARYHIS[$PER_ID][$in]['DOC_NO']
                                                );
                                }	
			} // end for
			unset($ARRAY_SALARYHIS);
                        array_multisort($DATE_SALARYHIS, SORT_DESC);
                        foreach ($DATE_SALARYHIS as $key => $value) {		//กรณีที่วันที่เดียวกัน แต่ต้องเอาเงินเดือนน้อยกว่าแสดงก่อน
                            if($chk_order_date_command=='Y'){ //ต้องการเรียงลำดับตามวันที่ลงนามในคำสั่ง http://dpis.ocsc.go.th/Service/node/1304
                                $DOCDATE[$key]  = $value['DOCDATE'];
                            }
                            $DATE[$key]  = $value['DATE'];
                            $POH_SEQ_NO[$key]  = $value['POH_SEQ_NO'];
                            $MOVE[$key]  = $value['MOVE'];
                            $POS_NAME[$key] = $value['POS_NAME'];
                            $POS_NO[$key]  = $value['POS_NO'];
                            $LEVEL[$key]  = $value['LEVEL'];
                            $SALARY[$key] = $value['SALARY'];
                            $DOC_NO[$key]  = $value['DOC_NO'];
				
				//echo $DOC_NO[$key]." | ".$POH_SEQ_NO[$key]."<br>";
			} // end foreach
                        if($chk_order_date_command=='Y'){ //ต้องการเรียงลำดับตามวันที่ลงนามในคำสั่ง http://dpis.ocsc.go.th/Service/node/1304
                            array_multisort($DOCDATE, SORT_DESC,$POH_SEQ_NO, SORT_DESC, $DATE_SALARYHIS);
                        }else{
                            array_multisort($DATE, SORT_DESC,$POH_SEQ_NO, SORT_DESC, $DATE_SALARYHIS);
                        }
			
			$POH_SALARYHIS[$PER_ID]=$DATE_SALARYHIS;
			unset($DATE_SALARYHIS);
                        /*Release 5.2.1.8 ส่วนของประวัติการเลื่อนเงินเดือน End*/
                        
                        
                        
			for($in=0; $in < count($ARRAY_POH_SAH[$PER_ID]);$in++){
                                //เก็บค่าวันที่
				if($chk_order_date_command=='Y'){ //ต้องการเรียงลำดับตามวันที่ลงนามในคำสั่ง http://dpis.ocsc.go.th/Service/node/1304
                                    $DATE_HIS[] = array( 
                                                    'DOCDATE'=>$ARRAY_POH_SAH[$PER_ID][$in]['DOCDATE'],
                                                    'DATE'=>$ARRAY_POH_SAH[$PER_ID][$in]['DATE'],
                                                    'POH_SEQ_NO'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_SEQ_NO'],
                                                    'MOVE'=>$ARRAY_POH_SAH[$PER_ID][$in]['MOVE'],
                                                    'POS_NAME'=>$ARRAY_POH_SAH[$PER_ID][$in]['POS_NAME'],
                                                    'POS_NO'=>$ARRAY_POH_SAH[$PER_ID][$in]['POS_NO'],
                                                    'LEVEL'=>$ARRAY_POH_SAH[$PER_ID][$in]['LEVEL'],
                                                    'SALARY'=>$ARRAY_POH_SAH[$PER_ID][$in]['SALARY'],
                                                    'DOC_NO'=>$ARRAY_POH_SAH[$PER_ID][$in]['DOC_NO']
                                                );
                                }else{
                                    $DATE_HIS[] = array('DATE'=>$ARRAY_POH_SAH[$PER_ID][$in]['DATE'],
                                                    'POH_SEQ_NO'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_SEQ_NO'],
                                                    'MOVE'=>$ARRAY_POH_SAH[$PER_ID][$in]['MOVE'],
                                                    'POS_NAME'=>$ARRAY_POH_SAH[$PER_ID][$in]['POS_NAME'],
                                                    'POS_NO'=>$ARRAY_POH_SAH[$PER_ID][$in]['POS_NO'],
                                                    'LEVEL'=>$ARRAY_POH_SAH[$PER_ID][$in]['LEVEL'],
                                                    'SALARY'=>$ARRAY_POH_SAH[$PER_ID][$in]['SALARY'],
                                                    'DOC_NO'=>$ARRAY_POH_SAH[$PER_ID][$in]['DOC_NO']
                                                );
                                }
                                
											
														
						
			} // end for
			unset($ARRAY_POH_SAH);
			//sort($DATE_HIS);
                        array_multisort($DATE_HIS, SORT_DESC);
                        
			
			//array_multisort($DATE_HIS, SORT_DESC,$POH_SEQ_NO_HIS,SORT_DESC, $ARRAY_POH_SAH[$PER_ID]);
			/*print("<pre>");
			print_r($POH_SEQ_NO_HIS);
			print("</pre> <br>");*/
			//array_multisort($DATE_HIS, SORT_DESC,$POH_SEQ_NO_HIS,SORT_DESC);
			foreach ($DATE_HIS as $key => $value) {		//กรณีที่วันที่เดียวกัน แต่ต้องเอาเงินเดือนน้อยกว่าแสดงก่อน
                            if($chk_order_date_command=='Y'){ //ต้องการเรียงลำดับตามวันที่ลงนามในคำสั่ง http://dpis.ocsc.go.th/Service/node/1304
                                $DOCDATE[$key]  = $value['DOCDATE'];
                            }
                            $DATE[$key]  = $value['DATE'];
                            $POH_SEQ_NO[$key]  = $value['POH_SEQ_NO'];
                            $MOVE[$key]  = $value['MOVE'];
                            $POS_NAME[$key] = $value['POS_NAME'];
                            $POS_NO[$key]  = $value['POS_NO'];
                            $LEVEL[$key]  = $value['LEVEL'];
                            $SALARY[$key] = $value['SALARY'];
                            $DOC_NO[$key]  = $value['DOC_NO'];
				
				//echo $DOC_NO[$key]." | ".$POH_SEQ_NO[$key]."<br>";
			} // end foreach
			
			//echo "----------------- <br>";
			// เดิม array_multisort($DATE, SORT_ASC, $SALARY, SORT_ASC, $DATE_HIS);
			//array_multisort($DATE, SORT_DESC,$POH_SEQ_NO, SORT_DESC, $DATE_HIS);
                        if($chk_order_date_command=='Y'){ //ต้องการเรียงลำดับตามวันที่ลงนามในคำสั่ง http://dpis.ocsc.go.th/Service/node/1304
                            array_multisort($DOCDATE, SORT_DESC,$POH_SEQ_NO, SORT_DESC, $DATE_HIS);
                        }else{
                            array_multisort($DATE, SORT_DESC,$POH_SEQ_NO, SORT_DESC, $DATE_HIS);
                        }
			
			$POH_SAH_HIS[$PER_ID]=$DATE_HIS;
			unset($DATE_HIS);
			
			/*print("<pre>");
			print_r($POH_SAH_HIS);
			print("</pre>");*/
			###IN CASE POSITIONHIS #######
			/////////////////////////////////////////////////////////////////
            $no_index=0;
            for($history_index=0; $history_index<count($arr_history_name); $history_index++){
                        
                $HISTORY_NAME = $arr_history_name[$history_index];
                                
                switch($HISTORY_NAME){
                    case "POSITIONHIS" : //รวมประวัติรับราชการ + เลื่อนขั้นเงินเดือนเข้าด้วยกัน
                        $no_index++;
                        if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
                        
                        $pdf->SetFont($font,'b',14);
                        $pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
                        
                        if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
                        
                        $pdf->Cell(200,7,(($NUMBER_DISPLAY==2)?convert2thaidigit(($history_index + 1)):($history_index + 1))."  ประวัติการรับราชการ ",0,1,"L");
                        print_header($HISTORY_NAME);

                        //ส่วนแสดงเนื้อหา หลังจากจัดเรียงข้อมูลแล้ว
                        if(is_array($POH_SAH_HIS) && !empty($POH_SAH_HIS)){
                                                    
                            $count_positionhis=count($POH_SAH_HIS[$PER_ID]);
                            $positionhis_count=0;
                            for($in=0; $in < count($POH_SAH_HIS[$PER_ID]);$in++){
                                $positionhis_count++;
                                if($POH_SAH_HIS[$PER_ID][$in]['DATE']){
                                    /*$arr_temp = explode("-", substr($POH_SAH_HIS[$PER_ID][$in]['DATE'], 0, 10));
                                    $DATE_POH_SAH = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);*/
                                    $DATE_POH_SAH = show_date_format(substr($POH_SAH_HIS[$PER_ID][$in]['DATE'], 0, 10),$DATE_DISPLAY);
                                }

                                $border = "";
                                $pdf->SetFont($font,'',14);
                                $pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

                                $start_x = $pdf->x;
                                $start_y = $pdf->y;
                                $max_y = $pdf->y;
                                //หาระดับตำแหน่ง (1,2,3,4,5,6,7,8,9);
                                $cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='".$POH_SAH_HIS[$PER_ID][$in]['LEVEL']."' order by LEVEL_NO";
                                $db_dpis2->send_cmd($cmd);
                                $data2 = $db_dpis2->get_array();
                                $LEVEL_NAME = trim($data2[POSITION_LEVEL]);                                              
                                /*$arr_temp = explode(" ", $LEVEL_NAME);
                                //หาชื่อตำแหน่งประเภท
                                $POSITION_TYPE="";
                                if(strstr($LEVEL_NAME, 'ประเภท') == TRUE) {
                                    $POSITION_TYPE = str_replace("ประเภท", "", $arr_temp[0]);
                                }elseif(strstr($LEVEL_NAME, 'กลุ่มงาน') == TRUE) {
                                    $POSITION_TYPE = str_replace("กลุ่มงาน", "", $arr_temp[0]);
                                }else{
                                    if($arr_temp[0] != 'ระดับ'){
                                        $POSITION_TYPE = $arr_temp[0];
                                    }
                                }
                                //หาชื่อระดับตำแหน่ง 
                                $LEVEL_NAME ="";
                                if(strstr($arr_temp[1], 'ระดับ') == TRUE) {
                                    $LEVEL_NAME =  str_replace("ระดับ", "", $arr_temp[1]);
                                }else{
                                    $LEVEL_NAME =  level_no_format($arr_temp[1]);
                                } */
                                //---------------------------------------------
									
                                //$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,$POH_SAH_HIS[$PER_ID][$in]['DATE'],$border,0,"C");
                                $pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($DATE_POH_SAH):$DATE_POH_SAH),$border,0,"C");
                                /*$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,$POH_SAH_HIS[$PER_ID][$in]['MOVE'],$border,0,"L");*/
                                if (strpos($POH_SAH_HIS[$PER_ID][$in]['POS_NAME'],"$LEVEL_NAME") === false && $POH_SAH_HIS[$PER_ID][$in]['POS_NAME'] != "-") {
                                    /*เดิม*/
                                    /*if ($LEVEL_NAME >= "00" && $LEVEL_NAME <= "99")
                                        $pdf->MultiCell($heading_width[$HISTORY_NAME][1] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($POH_SAH_HIS[$PER_ID][$in]['POS_NAME']." ".$LEVEL_NAME):$POH_SAH_HIS[$PER_ID][$in]['POS_NAME']." ".$LEVEL_NAME), $border, "L");	//ตำแหน่ง
                                      else
                                        $pdf->MultiCell($heading_width[$HISTORY_NAME][1] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($POH_SAH_HIS[$PER_ID][$in]['POS_NAME'].$LEVEL_NAME):$POH_SAH_HIS[$PER_ID][$in]['POS_NAME'].$LEVEL_NAME), $border, "L");	//ตำแหน่ง*/
                                    /*ปรับ Release 5.2.1.8*/
                                    if ($LEVEL_NAME >= "00" && $LEVEL_NAME <= "99")
                                        $pdf->MultiCell($heading_width[$HISTORY_NAME][1] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($POH_SAH_HIS[$PER_ID][$in]['POS_NAME']):$POH_SAH_HIS[$PER_ID][$in]['POS_NAME']), $border, "L");	//ตำแหน่ง
                                    else
                                        $pdf->MultiCell($heading_width[$HISTORY_NAME][1] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($POH_SAH_HIS[$PER_ID][$in]['POS_NAME']):$POH_SAH_HIS[$PER_ID][$in]['POS_NAME']), $border, "L");	//ตำแหน่ง
                                } else {
                                    $pdf->MultiCell($heading_width[$HISTORY_NAME][1] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($POH_SAH_HIS[$PER_ID][$in]['POS_NAME']):$POH_SAH_HIS[$PER_ID][$in]['POS_NAME']), $border, "L");	//ตำแหน่ง
                                }
                                                                        
                                if($pdf->y > $max_y) $max_y = $pdf->y;
                                $pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
                                $pdf->y = $start_y;
                                $pdf->MultiCell($heading_width[$HISTORY_NAME][2] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($POH_SAH_HIS[$PER_ID][$in]['MOVE']):$POH_SAH_HIS[$PER_ID][$in]['MOVE']), $border, "L");//ประเภทการเคลื่อนไหว
                                if($pdf->y > $max_y) $max_y = $pdf->y;
                                $pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
                                $pdf->y = $start_y;
                                $pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($POH_SAH_HIS[$PER_ID][$in]['POS_NO']):$POH_SAH_HIS[$PER_ID][$in]['POS_NO']),$border,0,"C");
                                $pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,($LEVEL_NAME?(($NUMBER_DISPLAY==2)?convert2thaidigit($LEVEL_NAME):$LEVEL_NAME):"-"),$border,0,"C");
                                $pdf->Cell($heading_width[$HISTORY_NAME][5] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($POH_SAH_HIS[$PER_ID][$in]['SALARY'])):number_format($POH_SAH_HIS[$PER_ID][$in]['SALARY'])),$border,0,'R');
                                $pdf->MultiCell($heading_width[$HISTORY_NAME][6], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($POH_SAH_HIS[$PER_ID][$in]['DOC_NO']):$POH_SAH_HIS[$PER_ID][$in]['DOC_NO']), $border, "L");
                                if($pdf->y > $max_y) $max_y = $pdf->y;
                                $pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3] + $heading_width[$HISTORY_NAME][4] + $heading_width[$HISTORY_NAME][5] + $heading_width[$HISTORY_NAME][6];
                                $pdf->y = $start_y;
	
                                //================= Draw Border Line ====================
                                $line_start_y = $start_y;		$line_start_x = $start_x;
                                $line_end_y = $max_y;		$line_end_x = $start_x;
                                $pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);

                                for($i=0; $i<=6; $i++){
                                    $line_start_y = $start_y;
                                    $line_start_x += $heading_width[$HISTORY_NAME][$i];
                                    $line_end_y = $max_y;
                                    $line_end_x += $heading_width[$HISTORY_NAME][$i];
                                    $pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
                                } // end for
                                //====================================================
								
                                if(($pdf->h - $max_y - 10) < 10){ 
                                    $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
                                    if($positionhis_count < $count_positionhis){
                                        $pdf->AddPage();
                                        print_header($HISTORY_NAME);
                                        $max_y = $pdf->y;
                                    } // end if
                                }else{
                                    if($positionhis_count == $count_positionhis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
                                } // end if
                                $pdf->x = $start_x;
                                $pdf->y = $max_y;					
                            } //end for
                        }
                        if($positionhis_count<=0){	//}else{
                            $pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
                            $pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
                        } // end if	
                        break;
                                                
                    /*case "SALARYHIS" ://ในนี้ไม่มี ถูกรวมอยู่กับ POSITIONHIS แล้ว
                        //ลบข้อ 2 ประวัติการเลื่อนขั้นเงินเดือน
                        break;
                    */
                            
                    /*Release 5.2.1.8 http://dpis.ocsc.go.th/Service/node/1432*/
                    case "SALARYHIS" :
                        $no_index++;
                        if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
                        $pdf->SetFont($font,'b',14);
                        $pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
                        if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
                        //$pdf->Cell(200,7,($history_index + 1)."  ประวัติการศึกษา ".$no_index,0,1,"L");
                        $pdf->Cell(200,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($no_index):$no_index)."  ประวัติการเลื่อนขั้นเงินเดือน ",0,1,"L");
                        print_header($HISTORY_NAME);
                        //ส่วนแสดงเนื้อหา หลังจากจัดเรียงข้อมูลแล้ว
                        if(is_array($POH_SALARYHIS) && !empty($POH_SALARYHIS)){
                                                    
                            $count_positionhis=count($POH_SALARYHIS[$PER_ID]);
                            $positionhis_count=0;
                            for($in=0; $in < count($POH_SALARYHIS[$PER_ID]);$in++){
                                $positionhis_count++;
                                if($POH_SALARYHIS[$PER_ID][$in]['DATE']){
                                    /*$arr_temp = explode("-", substr($POH_SALARYHIS[$PER_ID][$in]['DATE'], 0, 10));
                                    $DATE_POH_SAH = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);*/
                                    $DATE_POH_SAH = show_date_format(substr($POH_SALARYHIS[$PER_ID][$in]['DATE'], 0, 10),$DATE_DISPLAY);
                                }

                                $border = "";
                                $pdf->SetFont($font,'',14);
                                $pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

                                $start_x = $pdf->x;
                                $start_y = $pdf->y;
                                $max_y = $pdf->y;
                                //หาระดับตำแหน่ง (1,2,3,4,5,6,7,8,9);
                                $cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='".$POH_SALARYHIS[$PER_ID][$in]['LEVEL']."' order by LEVEL_NO";
                                $db_dpis2->send_cmd($cmd);
                                $data2 = $db_dpis2->get_array();
                                $LEVEL_NAME = trim($data2[POSITION_LEVEL]);

                                $pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($DATE_POH_SAH):$DATE_POH_SAH),$border,0,"C");
                                /*$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,$POH_SALARYHIS[$PER_ID][$in]['MOVE'],$border,0,"L");*/
                                if (strpos($POH_SALARYHIS[$PER_ID][$in]['POS_NAME'],"$LEVEL_NAME") === false && $POH_SALARYHIS[$PER_ID][$in]['POS_NAME'] != "-") {
                                    /*เดิม*/
                                    /*if ($LEVEL_NAME >= "00" && $LEVEL_NAME <= "99")
                                        $pdf->MultiCell($heading_width[$HISTORY_NAME][1] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($POH_SALARYHIS[$PER_ID][$in]['POS_NAME']." ".$LEVEL_NAME):$POH_SALARYHIS[$PER_ID][$in]['POS_NAME']." ".$LEVEL_NAME), $border, "L");	//ตำแหน่ง
                                      else
                                        $pdf->MultiCell($heading_width[$HISTORY_NAME][1] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($POH_SALARYHIS[$PER_ID][$in]['POS_NAME'].$LEVEL_NAME):$POH_SALARYHIS[$PER_ID][$in]['POS_NAME'].$LEVEL_NAME), $border, "L");	//ตำแหน่ง*/
                                    /*ปรับ Release 5.2.1.8*/
                                    if ($LEVEL_NAME >= "00" && $LEVEL_NAME <= "99")
                                        $pdf->MultiCell($heading_width[$HISTORY_NAME][1] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($POH_SALARYHIS[$PER_ID][$in]['POS_NAME']):$POH_SALARYHIS[$PER_ID][$in]['POS_NAME']), $border, "L");	//ตำแหน่ง
                                    else
                                        $pdf->MultiCell($heading_width[$HISTORY_NAME][1] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($POH_SALARYHIS[$PER_ID][$in]['POS_NAME']):$POH_SALARYHIS[$PER_ID][$in]['POS_NAME']), $border, "L");	//ตำแหน่ง
                                } else {
                                    $pdf->MultiCell($heading_width[$HISTORY_NAME][1] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($POH_SALARYHIS[$PER_ID][$in]['POS_NAME']):$POH_SALARYHIS[$PER_ID][$in]['POS_NAME']), $border, "L");	//ตำแหน่ง
                                }
                                                                        
                                if($pdf->y > $max_y) $max_y = $pdf->y;
                                $pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
                                $pdf->y = $start_y;
                                $pdf->MultiCell($heading_width[$HISTORY_NAME][2] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($POH_SALARYHIS[$PER_ID][$in]['MOVE']):$POH_SALARYHIS[$PER_ID][$in]['MOVE']), $border, "L");//ประเภทการเคลื่อนไหว
                                if($pdf->y > $max_y) $max_y = $pdf->y;
                                $pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
                                $pdf->y = $start_y;
                                $pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($POH_SALARYHIS[$PER_ID][$in]['POS_NO']):$POH_SALARYHIS[$PER_ID][$in]['POS_NO']),$border,0,"C");
                                $pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,($LEVEL_NAME?(($NUMBER_DISPLAY==2)?convert2thaidigit($LEVEL_NAME):$LEVEL_NAME):"-"),$border,0,"C");
                                $pdf->Cell($heading_width[$HISTORY_NAME][5] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($POH_SALARYHIS[$PER_ID][$in]['SALARY'])):number_format($POH_SALARYHIS[$PER_ID][$in]['SALARY'])),$border,0,'R');
                                $pdf->MultiCell($heading_width[$HISTORY_NAME][6], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($POH_SALARYHIS[$PER_ID][$in]['DOC_NO']):$POH_SALARYHIS[$PER_ID][$in]['DOC_NO']), $border, "L");
                                if($pdf->y > $max_y) $max_y = $pdf->y;
                                $pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3] + $heading_width[$HISTORY_NAME][4] + $heading_width[$HISTORY_NAME][5] + $heading_width[$HISTORY_NAME][6];
                                $pdf->y = $start_y;
	
                                //================= Draw Border Line ====================
                                $line_start_y = $start_y;		$line_start_x = $start_x;
                                $line_end_y = $max_y;		$line_end_x = $start_x;
                                $pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);

                                for($i=0; $i<=6; $i++){
                                    $line_start_y = $start_y;
                                    $line_start_x += $heading_width[$HISTORY_NAME][$i];
                                    $line_end_y = $max_y;
                                    $line_end_x += $heading_width[$HISTORY_NAME][$i];
                                    $pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
                                } // end for
                                //====================================================
								
                                if(($pdf->h - $max_y - 10) < 10){ 
                                    $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
                                    if($positionhis_count < $count_positionhis){
                                        $pdf->AddPage();
                                        print_header($HISTORY_NAME);
                                        $max_y = $pdf->y;
                                    } // end if
                                }else{
                                    if($positionhis_count == $count_positionhis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
                                } // end if
                                $pdf->x = $start_x;
                                $pdf->y = $max_y;					
                            } //end for
                        }
                        if($positionhis_count<=0){	//}else{
                            $pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
                            $pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
                        } // end if	
                        break; 
                    case "EXTRAHIS" :
                        $no_index++;
                        if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
                        $pdf->SetFont($font,'b',14);
                        $pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
                        if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
                        $pdf->Cell(200,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($no_index):$no_index)."  ประวัติการรับเงินตามตำแหน่ง ",0,1,"L");
                        print_header($HISTORY_NAME);
                        
                        $PMH_EFFECTIVEDATE = "";
                        $PMH_ENDDATE = "";
                        $EX_NAME = "";
                        $PMH_AMT = "";
	
                        $cmd =" select peh.PMH_EFFECTIVEDATE  ,peh.PMH_ENDDATE,pet.EX_NAME,peh.PMH_AMT
                                from PER_POS_MGTSALARYHIS peh, PER_EXTRATYPE pet
                                where peh.PER_ID=$PER_ID and trim(peh.EX_CODE)=trim(pet.EX_CODE) 
                                order by peh.PMH_EFFECTIVEDATE desc  ";
                        $count_extrahis = $db_dpis2->send_cmd($cmd);
                        if($count_extrahis){
                                $extrahis_count = 0;
                                while($data2 = $db_dpis2->get_array()){
                                        $extrahis_count++;
                                        $PMH_EFFECTIVEDATE = show_date_format($data2[PMH_EFFECTIVEDATE],$DATE_DISPLAY);
                                        $PMH_ENDDATE = show_date_format($data2[PMH_ENDDATE],$DATE_DISPLAY);
                                        $EX_NAME = trim($data2[EX_NAME]);
                                        $PMH_AMT = $data2[PMH_AMT];

                                        $border = "";
                                        $pdf->SetFont($font,'',14);
                                        $pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

                                        $start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

                                        $pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($PMH_EFFECTIVEDATE):$PMH_EFFECTIVEDATE),$border,0,"L");
                                        $pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($PMH_ENDDATE):$PMH_ENDDATE),$border,0,"L");

                                        $pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($EX_NAME):$EX_NAME), $border, "L");
                                        if($pdf->y > $max_y) $max_y = $pdf->y;
                                        $pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
                                        $pdf->y = $start_y;

                                        $pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PMH_AMT)):number_format($PMH_AMT)),$border,0,'R');

                                        //================= Draw Border Line ====================
                                        $line_start_y = $start_y;		$line_start_x = $start_x;
                                        $line_end_y = $max_y;		$line_end_x = $start_x;
                                        $pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);

                                        for($i=0; $i<=3; $i++){
                                                $line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
                                                $line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
                                                $pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
                                        } // end for
                                        //====================================================

                                        if(($pdf->h - $max_y - 10) < 10){ 
                                                $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
                                                if($extrahis_count < $count_exincomehis){
                                                        $pdf->AddPage();
                                                        print_header($HISTORY_NAME);
                                                        $max_y = $pdf->y;
                                                } // end if
                                        }else{
                                                if($extrahis_count == $count_extrahis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
                                        } // end if
                                        $pdf->x = $start_x;			$pdf->y = $max_y;
                                } // end while
                        }else{
                                $pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
                                $pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
                        } // end if
                        break; 
                        /*Release 5.2.1.8 End*/   
                    case "EDUCATE" :
                                        $no_index++;
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							//$pdf->Cell(200,7,($history_index + 1)."  ประวัติการศึกษา ".$no_index,0,1,"L");
							$pdf->Cell(200,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($no_index):$no_index)."  ประวัติการศึกษา ",0,1,"L");
							print_header($HISTORY_NAME);
							
							$EDU_PERIOD="";
							$EN_NAME = "";
							$EM_NAME = "";
							$INS_NAME = "";
							$CT_NAME = "";

							if($DPISDB=="odbc"){
								$cmd = " 	select		a.EDU_STARTYEAR, a.EDU_ENDYEAR,b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME, a.EDU_INSTITUTE
									 	from			(((PER_EDUCATE a
													left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
													) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
													) left join PER_COUNTRY e on (a.CT_CODE_EDU=e.CT_CODE)
										 where		a.PER_ID=$PER_ID
										 order by		a.EDU_SEQ DESC ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select		a.EDU_STARTYEAR, a.EDU_ENDYEAR,b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME, a.EDU_INSTITUTE
										from			PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d, PER_COUNTRY e
										where		a.PER_ID=$PER_ID and a.EN_CODE=b.EN_CODE(+) and 
													a.EM_CODE=c.EM_CODE(+) and a.INS_CODE=d.INS_CODE(+) and 
													trim(a.CT_CODE_EDU)=trim(e.CT_CODE(+))
										order by		a.EDU_SEQ DESC ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " 	select		a.EDU_STARTYEAR, a.EDU_ENDYEAR,b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME, a.EDU_INSTITUTE
									 	from			(((PER_EDUCATE a
													left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
													) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
													) left join PER_COUNTRY e on (a.CT_CODE_EDU=e.CT_CODE)
										 where		a.PER_ID=$PER_ID
										 order by		a.EDU_SEQ DESC ";			
							} // end if
							$count_educatehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_educatehis){
								$educatehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$educatehis_count++;
									$EDU_STARTYEAR = trim($data2[EDU_STARTYEAR]);
									$EDU_PERIOD='';
									$EDU_STARTYEAR = trim($data2[EDU_STARTYEAR]);
									if($EDU_STARTYEAR){	$EDU_PERIOD=$EDU_STARTYEAR;	}
									$EDU_ENDYEAR =  trim($data2[EDU_ENDYEAR]);
									if($EDU_PERIOD!="" && $EDU_ENDYEAR){
										$EDU_PERIOD.=" - $EDU_ENDYEAR";
									}else{
										$EDU_PERIOD.=$EDU_ENDYEAR;
									}

									$EN_NAME = trim($data2[EN_NAME]);
									$EM_NAME = trim($data2[EM_NAME]);
									if($EM_NAME!=""){ $EM_NAME="($EM_NAME)"; }
									$INS_NAME = trim($data2[INS_NAME]);
									if (!$INS_NAME) $INS_NAME = trim($data2[EDU_INSTITUTE]);
									$CT_NAME = trim($data2[CT_NAME]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($INS_NAME):$INS_NAME), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;
									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($EDU_PERIOD):$EDU_PERIOD), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit("$EN_NAME  $EM_NAME"):"$EN_NAME  $EM_NAME"), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;
                                                                        
                                                                        $pdf->MultiCell($heading_width[$HISTORY_NAME][3], 7, "$CT_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=3; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($educatehis_count < $count_educatehis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($educatehis_count == $count_educatehis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "TRAINING" :
                                        $no_index++;
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							//$pdf->Cell(200,7,($history_index + 1)."  ประวัติการฝึกอบรม/สัมมนา/ดูงาน ".$no_index;,0,1,"L");
							$pdf->Cell(200,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($no_index):$no_index)."  ประวัติการฝึกอบรม/สัมมนา/ดูงาน",0,1,"L");
							print_header($HISTORY_NAME);

							$TRN_DURATION = "";
							$TRN_STARTDATE = "";
							$TRN_ENDDATE = "";
							$TR_NAME = "";
							$TRN_PLACE = "";
							
							if($DPISDB=="odbc"){
								$cmd = " select		a.TRN_STARTDATE, a.TRN_ENDDATE,a.TRN_ORG, b.TR_NAME, a.TRN_PLACE, a.TRN_FUND, a.TRN_COURSE_NAME, a.TRN_NO
												 from			PER_TRAINING a, PER_TRAIN b
												 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE
												 order by	a.TRN_STARTDATE DESC ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select		a.TRN_STARTDATE, a.TRN_ENDDATE,a.TRN_ORG, b.TR_NAME, a.TRN_PLACE, a.TRN_FUND, a.TRN_COURSE_NAME, a.TRN_NO
												 from			PER_TRAINING a, PER_TRAIN b
												 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE(+)
												 order by	a.TRN_STARTDATE DESC ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select		a.TRN_STARTDATE, a.TRN_ENDDATE,a.TRN_ORG, b.TR_NAME, a.TRN_PLACE, a.TRN_FUND, a.TRN_COURSE_NAME, a.TRN_NO
												 from			PER_TRAINING a, PER_TRAIN b
												 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE
												 order by	a.TRN_STARTDATE DESC ";							   
							}
							$count_traininghis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_traininghis){
								$traininghis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$traininghis_count++;
									$TRN_STARTDATE = show_date_format($data2[TRN_STARTDATE],$DATE_DISPLAY);
									$TRN_ENDDATE = show_date_format($data2[TRN_ENDDATE],$DATE_DISPLAY);
									if(trim($TRN_STARTDATE) && trim($TRN_ENDDATE)){
										$TRN_DURATION = "$TRN_STARTDATE - $TRN_ENDDATE";
										if($TRN_STARTDATE == $TRN_ENDDATE) $TRN_DURATION = "$TRN_STARTDATE";
									}
									$TRN_ORG = trim($data2[TRN_ORG]);
									$TR_NAME = trim($data2[TR_NAME]);				
									if (!$TR_NAME || $TR_NAME=="อื่นๆ") $TR_NAME = trim($data2[TRN_COURSE_NAME]);
									//if($TR_NAME!=""){	$TR_NAME = str_replace("&quot;",""",$TR_NAME);		}
									$TRN_PLACE = trim($data2[TRN_PLACE]);
									$TRN_FUND = trim($data2[TRN_FUND]);
									$TRN_NO = trim($data2[TRN_NO]);
									if ($TRN_NO) $TR_NAME .= "รุ่นที่ ".$TRN_NO;
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($TRN_PLACE):$TRN_PLACE), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($TRN_DURATION):$TRN_DURATION), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($TR_NAME):$TR_NAME), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][3], 7, $TRN_ORG?(($NUMBER_DISPLAY==2)?convert2thaidigit($TRN_ORG):$TRN_ORG):"-", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2]+ $heading_width[$HISTORY_NAME][3];
									$pdf->y = $start_y;
									
									$pdf->MultiCell($heading_width[$HISTORY_NAME][4], 7, $TRN_FUND?(($NUMBER_DISPLAY==2)?convert2thaidigit($TRN_FUND):$TRN_FUND):"-", $border, "L"); //ทุน
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + + $heading_width[$HISTORY_NAME][3] + + $heading_width[$HISTORY_NAME][4];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=4; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($traininghis_count < $count_traininghis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($traininghis_count == $count_traininghis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "EXTRA_INCOMEHIS" :
                                        $no_index++;
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							//$pdf->Cell(200,7,($history_index + 1)."  ประวัติการรับเงินเพิ่มพิเศษ ".$no_index;,0,1,"L");
							$pdf->Cell(200,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($no_index):$no_index)."  ประวัติการรับเงินเพิ่มพิเศษ ",0,1,"L");
							print_header($HISTORY_NAME);
							
							$EXH_EFFECTIVEDATE = "";
							$EXH_ENDDATE = "";
							$EX_NAME = "";
							$EXH_AMT = "";
							
							$cmd = " select		a.EXH_EFFECTIVEDATE, a.EXH_ENDDATE, b.EX_NAME, a.EXH_AMT
											 from			PER_EXTRAHIS a, PER_EXTRATYPE b
											 where		a.PER_ID=$PER_ID and a.EX_CODE=b.EX_CODE 
											 order by	a.EXH_EFFECTIVEDATE DESC ";							   
							$count_extrahis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_extrahis){
								$extrahis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$extrahis_count++;
									$EXH_EFFECTIVEDATE = show_date_format($data2[EXH_EFFECTIVEDATE],$DATE_DISPLAY);
									$EXH_ENDDATE = show_date_format($data2[EXH_ENDDATE],$DATE_DISPLAY);
									$EX_NAME = trim($data2[EX_NAME]);
									$EXH_AMT = $data2[EXH_AMT];
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($EXH_EFFECTIVEDATE):$EXH_EFFECTIVEDATE),$border,0,"L");
									$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($EXH_ENDDATE):$EXH_ENDDATE),$border,0,"L");

									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($EX_NAME):$EX_NAME), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;

									$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($EXH_AMT)):number_format($EXH_AMT)),$border,0,'R');

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=3; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($extrahis_count < $count_exincomehis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($extrahis_count == $count_extrahis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "ABILITY" :
                                        $no_index++;
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							//$pdf->Cell(200,7,($history_index + 1)."  ความสามารถพิเศษ ".$no_index;,0,1,"L");
							$pdf->Cell(200,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($no_index):$no_index)."  ความสามารถพิเศษ ",0,1,"L");
							print_header($HISTORY_NAME);

							$AL_NAME = "";
							$ABI_DESC = "";

							$cmd = " select		b.AL_NAME, a.ABI_DESC
											 from			PER_ABILITY a, PER_ABILITYGRP b
											 where		a.PER_ID=$PER_ID and a.AL_CODE=b.AL_CODE
											 order by	a.ABI_ID  ";							   
							$count_abilityhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_abilityhis){
								$abilityhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$abilityhis_count++;
									$AL_NAME = trim($data2[AL_NAME]);
									$ABI_DESC = trim($data2[ABI_DESC]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($AL_NAME):$AL_NAME), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($ABI_DESC):$ABI_DESC), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=1; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($abilityhis_count < $count_abilityhis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($abilityhis_count == $count_abilityhis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "HEIR" :
                                        $no_index++;
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							//$pdf->Cell(200,7,($history_index + 1)."  ข้อมูลทายาท ".$no_index;,0,1,"L");
							$pdf->Cell(200,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($no_index):$no_index)."  ข้อมูลทายาท ",0,1,"L");
							print_header($HISTORY_NAME);

							$HR_NAME = "";
							$HEIR_NAME = "";

							$cmd = " select		b.HR_NAME, a.HEIR_NAME
											 from			PER_HEIR a, PER_HEIRTYPE b
											 where		a.PER_ID=$PER_ID and a.HR_CODE=b.HR_CODE
											 order by	a.HEIR_ID ";							   
							$count_heirhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_heirhis){
								$heirhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$heirhis_count++;
									$HR_NAME = trim($data2[HR_NAME]);
									$HEIR_NAME = trim($data2[HEIR_NAME]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($HR_NAME):$HR_NAME), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($HEIR_NAME):$HEIR_NAME), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=1; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($heirhis_count < $count_heirhis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($heirhis_count == $count_heirhis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "ABSENTHIS" :
                                        $no_index++;
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							//$pdf->Cell(200,7,($history_index + 1)."  ประวัติการลา สาย ขาด ".$no_index;,0,1,"L");
							$pdf->Cell(200,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($no_index):$no_index)."  ประวัติการลา สาย ขาด ",0,1,"L");
							print_header($HISTORY_NAME);
							
							$ABS_STARTDATE = "";
							$ABS_ENDDATE = "";
							$ABS_DAY = "";
							$AB_NAME = "";

							$cmd = " select			a.ABS_STARTDATE, a.ABS_ENDDATE, a.ABS_DAY, b.AB_NAME
											 from			PER_ABSENTHIS a, PER_ABSENTTYPE b
											 where		a.PER_ID=$PER_ID and a.AB_CODE=b.AB_CODE
											 order by		a.ABS_STARTDATE DESC ";							   
							$count_absenthis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_absenthis){
								$absenthis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$absenthis_count++;
									$ABS_STARTDATE = show_date_format($data2[ABS_STARTDATE],$DATE_DISPLAY);
									$ABS_ENDDATE = show_date_format($data2[ABS_ENDDATE],$DATE_DISPLAY);
									$ABS_DURATION = "$ABS_STARTDATE - $ABS_ENDDATE";
									if($ABS_STARTDATE == $ABS_ENDDATE) $ABS_DURATION = "$ABS_STARTDATE";
									$ABS_DAY = $data2[ABS_DAY];
									$AB_NAME = trim($data2[AB_NAME]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($ABS_DURATION):$ABS_DURATION), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,($ABS_DAY?(($NUMBER_DISPLAY==2)?convert2thaidigit($ABS_DAY):$ABS_DAY):""),$border,0,"R");

									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($AB_NAME):$AB_NAME),$border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=2; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($absenthis_count < $count_absenthis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($absenthis_count == $count_absenthis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "PUNISHMENT" :
                                        $no_index++;
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							//$pdf->Cell(200,7,($history_index + 1)."  ประวัติทางวินัย ".$no_index;,0,1,"L");
							$pdf->Cell(200,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($no_index):$no_index)."  ประวัติทางวินัย ",0,1,"L");
							print_header($HISTORY_NAME);

							$CR_NAME = "";
							$CRD_NAME = "";
							$PUN_STARTDATE = "";
							$PUN_ENDDATE = "";							
							
							$cmd = " select			c.CR_NAME, b.CRD_NAME, a.PUN_STARTDATE, a.PUN_ENDDATE
											 from			PER_PUNISHMENT a, PER_CRIME_DTL b, PER_CRIME c
											 where		a.PER_ID=$PER_ID and a.CRD_CODE=b.CRD_CODE and b.CR_CODE=c.CR_CODE
											 order by		a.PUN_STARTDATE DESC ";							   
							$count_punishmenthis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
//							$db_dpis2->show_error();
							if($count_punishmenthis){
								$punishmenthis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$punishmenthis_count++;
									$CR_NAME = trim($data2[CR_NAME]);
									$CRD_NAME = trim($data2[CRD_NAME]);
									$PUN_STARTDATE = show_date_format($data2[PUN_STARTDATE],$DATE_DISPLAY);
									$PUN_ENDDATE = show_date_format($data2[PUN_ENDDATE],$DATE_DISPLAY);
									$PUN_DURATION = "$PUN_STARTDATE - $PUN_ENDDATE";
									if($PUN_STARTDATE == $PUN_ENDDATE) $PUN_DURATION = "$PUN_STARTDATE";
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($CR_NAME):$CR_NAME), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($CRD_NAME):$CRD_NAME), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($PUN_DURATION):$PUN_DURATION), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=2; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($punishmenthis_count < $count_punishmenthis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($punishmenthis_count == $count_punishmenthis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "TIMEHIS" :
                                        $no_index++;
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							//$pdf->Cell(200,7,($history_index + 1)."  ประวัติเวลาทวีคูณ ".$no_index;,0,1,"L");
							$pdf->Cell(200,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($no_index):$no_index)."  ประวัติเวลาทวีคูณ ",0,1,"L");
							print_header($HISTORY_NAME);

							$TIME_NAME = "";
							$TIME_DAY = "";
							$TIMEH_MINUS = "";
							
							$cmd = " select		b.TIME_NAME, b.TIME_DAY, a.TIMEH_MINUS
											 from			PER_TIMEHIS a, PER_TIME b
											 where		a.PER_ID=$PER_ID and a.TIME_CODE=b.TIME_CODE
											 order by	a.TIMEH_ID ";							   
							$count_timehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_timehis){
								$timehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$timehis_count++;
									$TIME_NAME = trim($data2[TIME_NAME]);
									$TIME_DAY = $data2[TIME_DAY];
									$TIMEH_MINUS = $data2[TIMEH_MINUS];
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($TIME_NAME):$TIME_NAME), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,($TIME_DAY?(($NUMBER_DISPLAY==2)?convert2thaidigit($TIME_DAY):$TIME_DAY):""),$border,0,"R");
									$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,($TIMEH_MINUS?(($NUMBER_DISPLAY==2)?convert2thaidigit($TIMEH_MINUS):$TIMEH_MINUS):""),$border,0,"R");

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=2; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($timehis_count < $count_timehis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($timehis_count == $count_timehis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "REWARDHIS" :
                                        $no_index++;
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							//$pdf->Cell(200,7,($history_index + 1)."  ประวัติการรับความดีความชอบ ".$no_index;,0,1,"L");
							$pdf->Cell(200,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($no_index):$no_index)."  ประวัติการรับความดีความชอบ ",0,1,"L");
							print_header($HISTORY_NAME);

							$REH_DATE = "";
							$REW_NAME = "";
							
							$cmd = " select		a.REH_DATE, b.REW_NAME
											 from			PER_REWARDHIS a, PER_REWARD b
											 where		a.PER_ID=$PER_ID and a.REW_CODE=b.REW_CODE
											 order by	a.REH_DATE DESC ";							   
							$count_rewardhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_rewardhis){
								$rewardhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$rewardhis_count++;
									$REH_DATE = show_date_format($data2[REH_DATE],$DATE_DISPLAY);
									$REW_NAME = trim($data2[REW_NAME]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($REH_DATE):$REH_DATE), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($REW_NAME):$REW_NAME), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=1; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($rewardhis_count < $count_rewardhis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($rewardhis_count == $count_rewardhis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "MARRHIS" :
                                        $no_index++;
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							//$pdf->Cell(200,7,($history_index + 1)."  ประวัติการสมรส ".$no_index;,0,1,"L");
							$pdf->Cell(200,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($no_index):$no_index)."  ประวัติการสมรส ",0,1,"L");
							print_header($HISTORY_NAME);

							$MAH_NAME = "";
							$MAH_MARRY_DATE = "";
							
							$cmd = " select		MAH_NAME, MAH_MARRY_DATE
											 from			PER_MARRHIS
											 where		PER_ID=$PER_ID
											 order by	MAH_SEQ DESC ";							   
							$count_marryhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_marryhis){
								$marryhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$marryhis_count++;
									$MAH_NAME = trim($data2[MAH_NAME]);
									$MAH_MARRY_DATE = show_date_format($data2[MAH_MARRY_DATE],$DATE_DISPLAY);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($MAH_NAME):$MAH_NAME), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($MAH_MARRY_DATE):$MAH_MARRY_DATE), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=1; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($marryhis_count < $count_marryhis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($marryhis_count == $count_marryhis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "NAMEHIS" :
                                        $no_index++;
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							//$pdf->Cell(200,7,($history_index + 1)."  ประวัติการเปลี่ยนแปลงชื่อ-สกุล ".$no_index;,0,1,"L");
							$pdf->Cell(200,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($no_index):$no_index)."  ประวัติการเปลี่ยนแปลงชื่อ-สกุล ",0,1,"L");
							print_header($HISTORY_NAME);

							$NH_DATE = "";
							$PN_NAME = "";
							$NH_NAME = "";
							$NH_SURNAME = "";
							
							$cmd = " select		a.NH_DATE, b.PN_NAME, a.NH_NAME, a.NH_SURNAME
											 from			PER_NAMEHIS a, PER_PRENAME b
											 where		a.PER_ID=$PER_ID and a.PN_CODE=b.PN_CODE
											 order by	a.NH_DATE DESC ";							   
							$count_namehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_namehis){
								$namehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$namehis_count++;
									$NH_DATE = show_date_format($data2[NH_DATE],$DATE_DISPLAY);
									$PN_NAME = trim($data2[PN_NAME]);
									$NH_NAME = trim($data2[NH_NAME]);
									$NH_SURNAME = trim($data2[NH_SURNAME]);
									$FULLNAME = ($PN_NAME?$PN_NAME:"")."$NH_NAME $NH_SURNAME";
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($NH_DATE):$NH_DATE), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($FULLNAME):$FULLNAME), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=1; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($namehis_count < $count_namehis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($namehis_count == $count_namehis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "DECORATEHIS" :
                                        $no_index++;
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							//$pdf->Cell(200,7,($history_index + 1)."  ประวัติการรับเครื่องราชฯ ".$no_index;,0,1,"L");
							$pdf->Cell(200,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($no_index):$no_index)."  ประวัติการรับเครื่องราชฯ ",0,1,"L");
							print_header($HISTORY_NAME);

							$DC_NAME = "";
							$DEH_DATE = "";
							$DEH_GAZETTE = "";
							
							$cmd = " select		b.DC_NAME, a.DEH_DATE, a.DEH_GAZETTE
											 from			PER_DECORATEHIS a, PER_DECORATION b
											 where		a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE
											 order by	a.DEH_DATE DESC ";							   
							$count_decoratehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_decoratehis){
								$decoratehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$decoratehis_count++;
									$DC_NAME = trim($data2[DC_NAME]);
									$DEH_DATE = trim($data2[DEH_DATE]);
									if($DEH_DATE){
										$DEH_DATE = substr($DEH_DATE, 0, 10);
										$DEH_DATE1 = show_date_format($DEH_DATE,$DATE_DISPLAY);
									} // end if	
									$DEH_GAZETTE = trim($data2[DEH_GAZETTE]);
				
									//หาตำแหน่งที่ดำรง ณ ระหว่างวันที่ได้รับพระราชทานเครื่องราช
									if($DPISDB=="odbc"){
										$cmd3 = "select 		POH_ID, POH_EFFECTIVEDATE, POH_ENDDATE, POH_ORG2, POH_ORG3, 
																			PL_CODE, PM_CODE, PT_CODE, PN_CODE, EP_CODE, LEVEL_NO, POH_POS_NO_NAME, POH_POS_NO    
														from 			PER_POSITIONHIS 
														where 		(PER_ID=$PER_ID) AND (POH_EFFECTIVEDATE <= '$DEH_DATE')
														ORDER BY	 LEFT(POH_EFFECTIVEDATE,10) desc, LEFT(POH_ENDDATE,10) desc ";
									}elseif($DPISDB=="oci8"){
										$cmd3 = "select 		POH_ID, POH_EFFECTIVEDATE, POH_ENDDATE, POH_ORG2, POH_ORG3, 
																			PL_CODE, PM_CODE, PT_CODE, PN_CODE, EP_CODE, LEVEL_NO, POH_POS_NO_NAME, POH_POS_NO    
														from 			PER_POSITIONHIS 
														where 		(PER_ID=$PER_ID) AND (POH_EFFECTIVEDATE <= '$DEH_DATE')
														order by 		SUBSTR(POH_EFFECTIVEDATE,1,10)  desc, SUBSTR(POH_ENDDATE,1,10) desc ";
									}elseif($DPISDB=="mysql"){
										$cmd3 = "select 		POH_ID, POH_EFFECTIVEDATE, POH_ENDDATE, POH_ORG2, POH_ORG3, 
																			PL_CODE, PM_CODE, PT_CODE, PN_CODE, EP_CODE, LEVEL_NO, POH_POS_NO_NAME, POH_POS_NO    
														from 			PER_POSITIONHIS 
														where 		(PER_ID=$PER_ID) AND (POH_EFFECTIVEDATE <= '$DEH_DATE')
														ORDER BY	 LEFT(POH_EFFECTIVEDATE,10) desc, LEFT(POH_ENDDATE,10) desc ";
									}
									//echo "<br>$cmd3<br>";
									$db_dpis3->send_cmd($cmd3);
									$data3 = $db_dpis3->get_array();
									$POH_LEVEL_NO = trim($data3[LEVEL_NO]);
									$POH_PL_CODE = trim($data3[PL_CODE]);
									$POH_PM_CODE = trim($data3[PM_CODE]);
									$POH_PT_CODE = trim($data3[PT_CODE]);
									$POH_PN_CODE = $data3[PN_CODE];
									$POH_EP_CODE = $data3[EP_CODE];
		
									$DEH_PL_NAME = "";
									$cmd3= " select PL_NAME from PER_LINE where PL_CODE='$POH_PL_CODE' ";			
									$db_dpis3->send_cmd($cmd3);
									$data3 = $db_dpis3->get_array();
									$DEH_PL_NAME = $data3[PL_NAME];
						
									$cmd3 = " 	select PM_NAME from PER_MGT	where PM_CODE='$POH_PM_CODE'  ";
									$db_dpis3->send_cmd($cmd3);
									$data3 = $db_dpis3->get_array();
									$DEH_PM_NAME = trim($data3[PM_NAME]);
						
									$cmd3 = " 	select PT_NAME from PER_TYPE	where PT_CODE='$POH_PT_CODE'  ";
									$db_dpis3->send_cmd($cmd3);
									$data3 = $db_dpis3->get_array();
									$DEH_PT_NAME = trim($data3[PT_NAME]);
																		
									$cmd3 = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$POH_LEVEL_NO' ";
									$db_dpis3->send_cmd($cmd3);
									$data3 = $db_dpis3->get_array();
									$DEH_LEVEL_NAME = $data3[LEVEL_NAME];

								    $DEH_PL_NAME = (trim($DEH_PM_NAME) ?"$DEH_PM_NAME (":"") . (trim($DEH_PL_NAME)?($DEH_PL_NAME ." ". $DEH_LEVEL_NAME . (($DEH_PT_NAME != "ทั่วไป" && $DEH_LEVEL_NO >= 6)?"$DEH_PT_NAME":"")):"") . (trim($DEH_PM_NAME) ?")":"");
									//----------------------------
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit(" $DEH_PL_NAME"):" $DEH_PL_NAME"), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($DC_NAME):$DC_NAME), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($DEH_DATE1):$DEH_DATE1), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;
									
									$pdf->MultiCell($heading_width[$HISTORY_NAME][3], 7, $DEH_GAZETTE?(($NUMBER_DISPLAY==2)?convert2thaidigit($DEH_GAZETTE):$DEH_GAZETTE):"-", $border, "C");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3];
									$pdf->y = $start_y;
									
									$pdf->Cell($heading_width[$HISTORY_NAME][4] ,7,"-",$border,0,"C");
									

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=4; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($decoratehis_count < $count_decoratehis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($decoratehis_count == $count_decoratehis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "SERVICEHIS" :
                                        $no_index++;
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							//$pdf->Cell(200,7,($history_index + 1)."  ประวัติการปฏิบัติราชการพิเศษ ".$no_index;,0,1,"L");
							$pdf->Cell(200,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($no_index):$no_index)."  ประวัติการปฏิบัติราชการพิเศษ ",0,1,"L");
							print_header($HISTORY_NAME);

							$SRH_STARTDATE = "";
							$SV_NAME = "";
							$SRH_DOCNO = "";
							$SRH_NOTE = "";
							
							$cmd = " select		a.SRH_STARTDATE, b.SV_NAME, a.SRH_DOCNO, a.SRH_NOTE, c.ORG_NAME, d.SRT_NAME, SRH_ORG, SRH_SRT_NAME
											 from			PER_SERVICEHIS a, PER_SERVICE b, PER_ORG c, PER_SERVICETITLE d
											 where		a.PER_ID=$PER_ID and a.SV_CODE=b.SV_CODE and a.ORG_ID=c.ORG_ID(+) and a.SRT_CODE=d.SRT_CODE(+)
											 order by	a.SRH_STARTDATE DESC ";							   
							$count_servicehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_servicehis){
								$servicehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$servicehis_count++;
									$SRH_STARTDATE = show_date_format($data2[SRH_STARTDATE],$DATE_DISPLAY);
									$SV_NAME = trim($data2[SV_NAME]);
									$SRH_DOCNO = trim($data2[SRH_DOCNO]);
									$SRH_NOTE = trim($data2[SRH_NOTE]);
									$SRH_ORG = trim($data2[ORG_NAME]);
									$SRT_NAME = trim($data2[SRT_NAME]);
									if (!$SRH_ORG) $SRH_ORG = trim($data2[SRH_ORG]);
									if (!$SRT_NAME) $SRT_NAME = trim($data2[SRH_SRT_NAME]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($SRH_STARTDATE):$SRH_STARTDATE), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($SV_NAME):$SV_NAME), $border, "L");	//$SRH_NOTE
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($SRT_NAME):$SRT_NAME), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;
									
									$pdf->MultiCell($heading_width[$HISTORY_NAME][3], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($SRH_DOCNO):$SRH_DOCNO), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=3; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($servicehis_count < $count_servicehis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($servicehis_count == $count_servicehis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "SPECIALSKILLHIS" :
                                        $no_index++;
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							//$pdf->Cell(200,7,($history_index + 1)."  ความเชี่ยวชาญพิเศษ ".$no_index;,0,1,"L");
							$pdf->Cell(200,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($no_index):$no_index)."  ความเชี่ยวชาญพิเศษ ",0,1,"L");
							print_header($HISTORY_NAME);

							$SS_NAME = "";
							$SPS_EMPHASIZE = "";
							
							$cmd = " select		b.SS_NAME, a.SPS_EMPHASIZE
											 from			PER_SPECIAL_SKILL a, PER_SPECIAL_SKILLGRP b
											 where		a.PER_ID=$PER_ID and a.SS_CODE=b.SS_CODE
											 order by	a.SPS_ID ";							   
							$count_specialskillhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_specialskillhis){
								$specialskillhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$specialskillhis_count++;
									$SS_NAME = trim($data2[SS_NAME]);
									$SPS_EMPHASIZE = trim($data2[SPS_EMPHASIZE]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($SS_NAME):$SS_NAME), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($SPS_EMPHASIZE):$SPS_EMPHASIZE), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=1; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($specialskillhis_count < $count_specialskillhis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($specialskillhis_count == $count_specialskillhis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
				case "ACTINGHIS" :
                                $no_index++;
						if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
						$pdf->SetFont($font,'b',14);
						$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
						if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
						$pdf->Cell(200,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($no_index):$no_index)."  ประวัติการรักษาราชการ/มอบหมายงาน ",0,1,"L");
						print_header($HISTORY_NAME);
							
					if($DPISDB=="odbc") $order_by = "LEFT(ACTH_EFFECTIVEDATE,10) DESC, LEFT(ACTH_ENDDATE,10) DESC, ACTH_SEQ_NO DESC";
					elseif($DPISDB=="oci8") $order_by = "SUBSTR(ACTH_EFFECTIVEDATE,1,10) DESC, SUBSTR(ACTH_ENDDATE,1,10) DESC, ACTH_SEQ_NO DESC";			 
					elseif($DPISDB=="mysql") $order_by = "LEFT(ACTH_EFFECTIVEDATE,10) DESC, LEFT(ACTH_ENDDATE,10) DESC, ACTH_SEQ_NO DESC"; 
					if($DPISDB=="odbc"){
						$cmd = " SELECT 		ACTH_ID, ACTH_EFFECTIVEDATE, ACTH_ENDDATE, ACTH_ORG2_ASSIGN, ACTH_ORG3_ASSIGN, 
											PL_CODE_ASSIGN, PM_CODE_ASSIGN, LEVEL_NO_ASSIGN, ACTH_POS_NO_ASSIGN, MOV_CODE, 
											ACTH_DOCNO, ACTH_PM_NAME_ASSIGN, ACTH_PL_NAME_ASSIGN    
								FROM		PER_ACTINGHIS 
								WHERE		PER_ID=$PER_ID 
								ORDER BY	 $order_by ";
								//echo $cmd;
					}elseif($DPISDB=="oci8"){			 
						$cmd = "select 		ACTH_ID, ACTH_EFFECTIVEDATE, ACTH_ENDDATE, ACTH_ORG2_ASSIGN, ACTH_ORG3_ASSIGN,
															PL_CODE_ASSIGN, PM_CODE_ASSIGN, LEVEL_NO_ASSIGN, ACTH_POS_NO_ASSIGN, MOV_CODE, 
															ACTH_DOCNO, ACTH_PM_NAME_ASSIGN, ACTH_PL_NAME_ASSIGN
												  from 		PER_ACTINGHIS 
												  where 		PER_ID=$PER_ID
												  order by 	$order_by ";				 					 
					}elseif($DPISDB=="mysql"){
						$cmd = " SELECT 	ACTH_ID, ACTH_EFFECTIVEDATE, ACTH_ENDDATE, ACTH_ORG2_ASSIGN, ACTH_ORG3_ASSIGN, 
									PL_CODE_ASSIGN, PM_CODE_ASSIGN, LEVEL_NO_ASSIGN, ACTH_POS_NO_ASSIGN, MOV_CODE, ACTH_DOCNO, 
									ACTH_PM_NAME_ASSIGN, ACTH_PL_NAME_ASSIGN
									FROM		PER_ACTINGHIS 
									WHERE PER_ID=$PER_ID 
									ORDER BY 	$order_by ";
					} // end if
					$count_actinghis = $db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					if($count_actinghis){
								$actinghis_count = 0;
								while($data = $db_dpis2->get_array()){
									$actinghis_count++;
									$TMP_ACTH_ID = $data[ACTH_ID];
									$TMP_ACTH_EFFECTIVEDATE = show_date_format($data[ACTH_EFFECTIVEDATE], $DATE_DISPLAY);
									$TMP_ACTH_ENDDATE = show_date_format($data[ACTH_ENDDATE], $DATE_DISPLAY);
									$TMP_LEVEL_NO = trim($data[LEVEL_NO_ASSIGN]);
									$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$TMP_LEVEL_NO' ";
									$db_dpis3->send_cmd($cmd);
									$data2 = $db_dpis3->get_array();
									$TMP_LEVEL_NAME = $data2[LEVEL_NAME];
									
									$TMP_ACTH_POS_NO = (trim($data[ACTH_POS_NO_ASSIGN]))?   $data[ACTH_POS_NO_ASSIGN] : "-";
									
									$TMP_ORG_NAME_2 = $data[ACTH_ORG2_ASSIGN];
									$TMP_ORG_NAME_3 = $data[ACTH_ORG3_ASSIGN];
									
									$TMP_PL_CODE = trim($data[PL_CODE_ASSIGN]);
									$TMP_PM_CODE = trim($data[PM_CODE_ASSIGN]);
									$TMP_MOV_CODE = $data[MOV_CODE];
									$TMP_ACTH_DOCNO = $data[ACTH_DOCNO];
									
									$cmd = " select MOV_NAME from PER_MOVMENT where MOV_CODE='$TMP_MOV_CODE' ";
									$db_dpis3->send_cmd($cmd);
									$data2 = $db_dpis3->get_array();
									$TMP_MOV_NAME = $data2[MOV_NAME];		
									
									if($PER_TYPE==1){
										$TMP_PL_NAME = "";
										$cmd = " select PL_NAME from PER_LINE where PL_CODE='$TMP_PL_CODE' ";			
										$db_dpis3->send_cmd($cmd);
										$data2 = $db_dpis3->get_array();
										$TMP_PL_NAME = $data2[PL_NAME];
									
										$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$TMP_PM_CODE'  ";
										$db_dpis3->send_cmd($cmd);
										$data2 = $db_dpis3->get_array();
										$TMP_PM_NAME = trim($data2[PM_NAME]);
									
										$cmd = " 	select PT_NAME from PER_TYPE	where PT_CODE='$TMP_PT_CODE'  ";
										$db_dpis3->send_cmd($cmd);
										$data2 = $db_dpis3->get_array();
										$TMP_PT_NAME = trim($data2[PT_NAME]);
									
										if ($RPT_N)
											$TMP_PL_NAME = (trim($TMP_PM_NAME) ?"$TMP_PM_NAME (":"") . (trim($TMP_PL_NAME)? "$TMP_PL_NAME $TMP_LEVEL_NAME" : "") . (trim($TMP_PM_NAME) ?")":"");
										else
											$TMP_PL_NAME = (trim($TMP_PM_NAME) ?"$TMP_PM_NAME (":"") . (trim($TMP_PL_NAME)?($TMP_PL_NAME ." ". level_no_format($TMP_LEVEL_NO) . (($TMP_PT_NAME != "ทั่วไป" && $TMP_LEVEL_NO >= 6)?"$TMP_PT_NAME":"")):"") . (trim($TMP_PM_NAME) ?")":"");
									} elseif($PER_TYPE==2){
										$cmd = " select PN_NAME from PER_POS_NAME where PN_CODE='$TMP_PN_CODE' ";
										$db_dpis3->send_cmd($cmd);
										$data2 = $db_dpis3->get_array();
										$TMP_PL_NAME = $data2[PN_NAME];	
									} elseif($PER_TYPE==3){
										$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE='$TMP_EP_CODE' ";
										$db_dpis3->send_cmd($cmd);
										$data2 = $db_dpis3->get_array();
										$TMP_PL_NAME = $data2[EP_NAME];		
									} // end if
									if (!$TMP_PL_NAME) $TMP_PL_NAME = trim($data[ACTH_PL_NAME]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit("$actinghis_count.) $TMP_MOV_NAME"):"$actinghis_count.) $TMP_MOV_NAME"), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($TMP_ACTH_DOCNO):$TMP_ACTH_DOCNO),$border,0,"R");
									$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($TMP_ACTH_EFFECTIVEDATE):$TMP_ACTH_EFFECTIVEDATE),$border,0,"L");
									$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($TMP_ACTH_ENDDATE):$TMP_ACTH_ENDDATE),$border,0,"L");

									$pdf->MultiCell($heading_width[$HISTORY_NAME][4], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($TMP_ACTH_POS_NO):$TMP_ACTH_POS_NO), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0]+ $heading_width[$HISTORY_NAME][1]+ $heading_width[$HISTORY_NAME][2]+ $heading_width[$HISTORY_NAME][3]+ $heading_width[$HISTORY_NAME][4];
									$pdf->y = $start_y;
									$pdf->MultiCell($heading_width[$HISTORY_NAME][5], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($TMP_PL_NAME):$TMP_PL_NAME), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0]+ $heading_width[$HISTORY_NAME][1]+ $heading_width[$HISTORY_NAME][2]+ $heading_width[$HISTORY_NAME][3]+ $heading_width[$HISTORY_NAME][4]+ $heading_width[$HISTORY_NAME][5];
									$pdf->y = $start_y;
									$pdf->MultiCell($heading_width[$HISTORY_NAME][6], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($TMP_ORG_NAME_2):$TMP_ORG_NAME_2), $border, "L");
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0]+ $heading_width[$HISTORY_NAME][1]+ $heading_width[$HISTORY_NAME][2]+ $heading_width[$HISTORY_NAME][3]+ $heading_width[$HISTORY_NAME][4]+ $heading_width[$HISTORY_NAME][5]+ $heading_width[$HISTORY_NAME][6];
									$pdf->y = $start_y;
									$pdf->MultiCell($heading_width[$HISTORY_NAME][7], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($TMP_ORG_NAME_3):$TMP_ORG_NAME_3), $border, "L");
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0]+ $heading_width[$HISTORY_NAME][1]+ $heading_width[$HISTORY_NAME][2]+ $heading_width[$HISTORY_NAME][3]+ $heading_width[$HISTORY_NAME][4]+ $heading_width[$HISTORY_NAME][5]+ $heading_width[$HISTORY_NAME][6]+ $heading_width[$HISTORY_NAME][7];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=7; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($specialskillhis_count < $count_specialskillhis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($specialskillhis_count == $count_specialskillhis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if		
					break;	
					case "LICENSEHIS" :
                                        $no_index++;
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($no_index):$no_index)."  ประวัติใบอนุญาตประกอบวิชาชีพ ",0,1,"L");
							print_header($HISTORY_NAME);

							$LH_LICENSE_DATE = "";	$LH_EXPIRE_DATE = "";	$LT_NAME = "";		$LH_MAJOR = "";
							
							if($DPISDB=="odbc"){
								$cmd = " select		a.LH_LICENSE_DATE, a.LH_EXPIRE_DATE,a.LH_SUB_TYPE, b.LT_NAME, a.LH_MAJOR, a.LH_LICENSE_NO
												 from			PER_LICENSEHIS a, PER_LICENSE_TYPE b
												 where		a.PER_ID=$PER_ID and a.LT_CODE=b.LT_CODE
												 order by	a.LH_EXPIRE_DATE  DESC ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select		a.LH_LICENSE_DATE, a.LH_EXPIRE_DATE,a.LH_SUB_TYPE, b.LT_NAME, a.LH_MAJOR, a.LH_LICENSE_NO
												 from			PER_LICENSEHIS a, PER_LICENSE_TYPE b
												 where		a.PER_ID=$PER_ID and a.LT_CODE=b.LT_CODE(+)
												 order by	a.LH_EXPIRE_DATE DESC ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select		a.LH_LICENSE_DATE, a.LH_EXPIRE_DATE,a.LH_SUB_TYPE, b.LT_NAME, a.LH_MAJOR, a.LH_LICENSE_NO
												 from			PER_LICENSEHIS a, PER_LICENSE_TYPE b
												 where		a.PER_ID=$PER_ID and a.LT_CODE=b.LT_CODE
												 order by	a.LH_EXPIRE_DATE DESC ";							   
							}
							$count_licensehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_licensehis){
								$licensehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$licensehis_count++;
									$LH_LICENSE_DATE = show_date_format($data2[LH_LICENSE_DATE],$DATE_DISPLAY);
									$LH_EXPIRE_DATE = show_date_format($data2[LH_EXPIRE_DATE],$DATE_DISPLAY);
									$LH_SUB_TYPE = trim($data2[LH_SUB_TYPE]);
									$LT_NAME = trim($data2[LT_NAME]);
									$LH_MAJOR = trim($data2[LH_MAJOR]);
									$LH_LICENSE_NO = trim($data2[LH_LICENSE_NO]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($LT_NAME):$LT_NAME), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($LH_SUB_TYPE):$LH_SUB_TYPE), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($LH_MAJOR):$LH_MAJOR), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][3], 7, $LH_LICENSE_NO?(($NUMBER_DISPLAY==2)?convert2thaidigit($LH_LICENSE_NO):$LH_LICENSE_NO):"-", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2]+ $heading_width[$HISTORY_NAME][3];
									$pdf->y = $start_y;
									
									$pdf->MultiCell($heading_width[$HISTORY_NAME][4], 7, $LH_EXPIRE_DATE?(($NUMBER_DISPLAY==2)?convert2thaidigit($LH_EXPIRE_DATE):$LH_EXPIRE_DATE):"-", $border, "L"); //ทุน
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + + $heading_width[$HISTORY_NAME][3] + + $heading_width[$HISTORY_NAME][4];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=4; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($traininghis_count < $count_traininghis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($traininghis_count == $count_traininghis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
				} // end switch
			} // end for
			
			if($data_count < $count_data) $pdf->AddPage();
		} // end while
	}else{
		$pdf->SetFont($font,'b',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();
?>