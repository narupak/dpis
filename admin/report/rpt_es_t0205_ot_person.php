<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd); 
	
	   if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
		require("../../RTF/rtf_class.php");
	} else	 {
		define('FPDF_FONTPATH','../../PDF/font/');
		include ("../../PDF/fpdf.php");
		include ("../../PDF/pdf_extends_DPIS.php");
	}
	
	ini_set("max_execution_time", $max_execution_time);
	
	$report_title = trim(iconv("utf-8","tis620",urldecode($report_title)));
	$report_code = "";
	

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	if ($FLAG_RTF) {
		$sum_w = array_sum($heading_width);
		for($h = 0; $h < count($heading_width); $h++) {
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}
		$fname= $report_title.".rtf";
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
			$fname= "T0203_PERSON.pdf";
    $unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$orientation='P';
	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	$pdf->SetAutoPageBreak(true,10);
	}
	if ($FLAG_RTF) {
		$heading_width[0] = "12";
		$heading_width[1] = "60";
		$heading_width[2] = "70";
		$heading_width[3] = "30";
		$heading_width[4] = "30";
	}else{
		$heading_width[0] = "12";
		$heading_width[1] = "60";
		$heading_width[2] = "70";
		$heading_width[3] = "30";
		$heading_width[4] = "30";
	}	
	$heading_text[0] = "ลำดับที่";
	$heading_text[1] = "ชื่อ-สกุล";
	$heading_text[2] = "สำนัก/กองตามกฏหมาย";
	$heading_text[3] = "จำนวนชั่วโมง";
	$heading_text[4] = "จำนวนเงิน";
		
	$heading_align = array('C','C','C','C','C');
		
	/*---------------------------------------------------------------*/
    //	กำหนดค่า default timezone		//phpinfo();
	function set_default_timezone($timezone){
		if (version_compare(phpversion(), '5', '>=')){
			if(function_exists('date_default_timezone_set')) { 
				$result_set = date_default_timezone_set($timezone); 	// PHP  >= 5.1.0
				//echo date_default_timezone_get();	// PHP  >= 5.1.0
			} 
		}else{		// < version 5
			$result_set = ini_set('date.timezone',$timezone);
		}
	return $result_set;
	}
    
	
	set_default_timezone('Asia/Bangkok');	// ทำเวลาให้เป็นเวลาโซนที่กำหนด
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$CREATE_DATE = date("Y-m-d H:i A", time());
	$TODATE = date("Y-m-d");
	$temp_date = explode("-", $TODATE);
	$temp_todate = ($temp_date[0]) ."-". $temp_date[1] ."-". $temp_date[2];
	if(!$search_date_min) {
		$search_date_min = "01/". $temp_date[1] ."/". ($temp_date[0] + 543);
	}
	if(!$search_date_max) {		// จากเดือนปัจจุบันไปอีก 60 วัน
        $search_abs_endmonth = ($temp_date[1] + 0);
        if($search_abs_endmonth<10) $search_abs_endmonth = "0".($search_abs_endmonth);
        $search_abs_endyear = $temp_date[0];
		$max_date = get_max_date($search_abs_endmonth, $search_abs_endyear);
		//$search_date_max = $max_date."/". $search_abs_endmonth ."/". ($search_abs_endyear + 543);
        $search_date_max = date("d")."/". $search_abs_endmonth ."/". ($search_abs_endyear + 543);
	}

    $cmd2 = " select POS_ID,ORG_ID,POEM_ID,POEMS_ID,POT_ID,ORG_ID_1,PER_OT_FLAG from PER_PERSONAL where PER_ID=$SESS_PER_ID"; 
    $db_dpis2->send_cmd($cmd2);
    $data2 = $db_dpis2->get_array();
    $PER_POS_ID = $data2[POS_ID]; /*หาคนภายใต้หน่วยงานตามกฏหมาย ข้าราชการ*/
    $PER_POEM_ID = $data2[POEM_ID]; /*หาคนภายใต้หน่วยงานตามกฏหมาย ลูกจ้างประจำ*/
    $PER_POEMS_ID = $data2[POEMS_ID]; /*หาคนภายใต้หน่วยงานตามกฏหมาย พนักงานราชการ*/
    $PER_POT_ID = $data2[POT_ID]; /*หาคนภายใต้หน่วยงานตามกฏหมาย ลูกจ้างชั่วคราว*/
    $PER_ORG_ID = $data2[ORG_ID]; /*หาคนภายใต้หน่วยงานตามหมอบหมายงาน*/
	
	$PER_ORG_ID_1 = $data2[ORG_ID_1]; /*หาคนภายใต้หน่วยงานตามหมอบหมายงาน ต่ำกว่าสำนัก 1 ระดับ*/
	$PER_OT_FLAG = $data2[PER_OT_FLAG]; /*รหัสเจ้าของ OT ตามกฏหมาย*/

    
    //หาว่าอยู่กลุ่ม กจ. กรม หรือไม่--------------------------------
    $cmd4 = "	select	 b.CODE from	user_detail a, user_group b
					where a.group_id=b.id AND a.ID=".$SESS_USERID;
    $db_dpis2->send_cmd($cmd4);
    $data4 = $db_dpis2->get_array();
    if ($data4[CODE]) {
        $NAME_GROUP_HRD = $data4[CODE];
    }else{
        $NAME_GROUP_HRD = "";
    }
    
    if(empty($select_org_structure_ot)){$select_org_structure_ot=0;}
    
    
    
    
     if ($SESS_USERGROUP !=1 && $NAME_GROUP_HRD!='HRD'){
    	
      	if($select_org_structure_ot==1){ // มอบหมาย
                $cmd3 = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$PER_ORG_ID"; 
                $db_dpis2->send_cmd($cmd3);
                $data3 = $db_dpis2->get_array();
                $search_org_name = $data3[ORG_NAME];
                $search_org_id = $PER_ORG_ID;
				
				$cmd3 = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$PER_ORG_ID_1"; 
                $db_dpis2->send_cmd($cmd3);
                $data3 = $db_dpis2->get_array();
                $search_org_name_1 = $data3[ORG_NAME];
                $search_org_id_1 = $PER_ORG_ID_1;
         }else{ // กฏหมาย
         
    			if($PER_POS_ID){ /*หาคนภายใต้หน่วยงานตามกฏหมาย ข้าราชการ*/
                	$fromTable = "PER_POSITION";
                    $whereTable = " c.POS_ID=$PER_POS_ID";
                }elseif($PER_POEM_ID){ /*หาคนภายใต้หน่วยงานตามกฏหมาย ลูกจ้างประจำ*/
                	$fromTable = "PER_POS_EMP";
                    $whereTable = " c.POEM_ID=$PER_POEM_ID";
                }elseif($PER_POEMS_ID){ /*หาคนภายใต้หน่วยงานตามกฏหมาย พนักงานราชการ*/
                	$fromTable = "PER_POS_EMPSER";
                    $whereTable = " c.POEMS_ID=$PER_POEMS_ID";
                }elseif($PER_POT_ID){ /*หาคนภายใต้หน่วยงานตามกฏหมาย ลูกจ้างชั่วคราว*/
                	$fromTable = "PER_POS_TEMP";
                    $whereTable = " c.POT_ID=$PER_POT_ID";
                }
                
                $cmd3 = " select g.ORG_NAME ,g.ORG_ID,g1.ORG_NAME as ORG_NAME_1 ,g1.ORG_ID as ORG_ID_1
                   from $fromTable c 
                   LEFT JOIN PER_ORG  g on (g.ORG_ID=c.ORG_ID)
				   LEFT JOIN PER_ORG  g1 on (g1.ORG_ID=c.ORG_ID_1)
                   where $whereTable "; 
                $db_dpis2->send_cmd($cmd3);
                $data3 = $db_dpis2->get_array();
                $search_org_name = $data3[ORG_NAME];
                $search_org_id = $data3[ORG_ID];
				
				if($PER_OT_FLAG==$data3[ORG_ID_1]){
                	$search_org_name_1 = $data3[ORG_NAME_1];
                	$search_org_id_1 = $data3[ORG_ID_1];
                }
         }
        
        if(!$search_date_min) { // หาวันที่เริ่มต้นหลังจากปิดงวดโอที เดือนล่าสุด
    
            $cmd2 = " select max(CONTROL_ID) as CONTROL_ID  from TA_PER_OT where DEPARTMENT_ID=$search_org_id and CONTROL_ID IS NOT NULL "; 
            $db_dpis2->send_cmd($cmd2);
            $data2 = $db_dpis2->get_array();
            
            if (!empty($data2[CONTROL_ID])){
                $cmd2 = " select END_DATE  from TA_PER_OT_CONTROL where CONTROL_ID=".$data2[CONTROL_ID]; 
                $db_dpis2->send_cmd($cmd2);
                $data3 = $db_dpis2->get_array();

                if (!empty($data3[END_DATE])){
                	$dateEnd_date = substr($data3[END_DATE],8,2)."-".substr($data3[END_DATE],5,2)."-".substr($data3[END_DATE],0,4);
                    $datetomorow = date('d-m-Y', strtotime("+1 day", strtotime("$dateEnd_date")));
                     $search_date_min = substr($datetomorow,0,2)."/". substr($datetomorow,3,2) ."/". (substr($datetomorow,6,4) + 543);
                }else{
                    $search_date_min = "01/". $temp_date[1] ."/". ($temp_date[0] + 543);
                }
            }else{
            	$search_date_min = "01/". $temp_date[1] ."/". ($temp_date[0] + 543);
            }
            
		}
    }else{
    	 if(!$search_date_min) {
			$search_date_min = "01/". $temp_date[1] ."/". ($temp_date[0] + 543);
		}
    
    }
    
   
    
    /*-------------------------------------------------------------*/

    if( $SESS_USERGROUP ==1 || $NAME_GROUP_HRD =='HRD'){ 
       	if($select_org_head == 1){  // หน่วยงานที่สังกัด 
        	 if($select_org_structure == 0){ //ตามกฏหมาย
             	if($search_org_id_1){
                	$arr_search_condition[] = "(c.ORG_ID_1=$search_org_id_1 or d.ORG_ID_1=$search_org_id_1 or e.ORG_ID_1=$search_org_id_1 or j.ORG_ID_1=$search_org_id_1)";
                }elseif($search_org_id){
                  //echo "1";
                   $arr_search_condition[] = "(c.ORG_ID=$search_org_id or d.ORG_ID=$search_org_id or e.ORG_ID=$search_org_id or j.ORG_ID=$search_org_id)";
                }else{
                	//echo "2";
                   $arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
                }
            }else{ //ตามมอบหมาย
            	if($search_org_id_1){
                	$arr_search_condition[] = "(a.ORG_ID_1=$search_org_id_1)";
                }elseif($search_org_id){
                	//echo "3";
                   $arr_search_condition[] = "(a.ORG_ID=$search_org_id)";
                }else{
                	//echo "4";
                   $arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
                }
            
            }
            
         }else{ // หน่วยงานเจ้าของเรื่อง (OT)
         	if($search_org_id_1){
            	$arr_search_condition[] = "(ot.DEPARTMENT_ID=$search_org_id AND ot.ORG_LOWER1=$search_org_id_1)";
            }elseif($search_org_id){
            	//echo "5";
                $arr_search_condition[] = "(ot.DEPARTMENT_ID=$search_org_id AND ot.ORG_LOWER1=-1)";
            }else{
            	//echo "6";
                $arr_search_condition[] = "(ot.ORG_ID=$search_department_id AND ot.DEPARTMENT_ID=-1  )";
            }
         }
    }else{ // กจ. สำนัก
    	//echo "7";
        if($search_org_id_1){
        	$arr_search_condition[] = "(ot.DEPARTMENT_ID= $search_org_id AND ot.ORG_LOWER1=$search_org_id_1)";
        }else{
    		$arr_search_condition[] = "(ot.DEPARTMENT_ID= $search_org_id AND ot.ORG_LOWER1=-1)";
        }
    }
   
   	if(trim($search_name)) $arr_search_condition[] = "(a.PER_NAME like '".trim($search_name)."%' or UPPER(a.PER_ENG_NAME) like UPPER('".trim($search_name)."%'))";
   	if(trim($search_surname)) $arr_search_condition[] = "(a.PER_SURNAME like '".trim($search_surname)."%' or UPPER(a.PER_ENG_SURNAME) like UPPER('".trim($search_surname)."%'))";
  
    //if(trim($search_cardno)) $arr_search_condition[] = "(a.PER_CARDNO ='".trim($search_cardno)."')";
   // if(trim($search_offno)) $arr_search_condition[] = "(a.PER_OFFNO ='".trim($search_offno)."')";
    
   /* if(trim($search_pos_no))  {	
		if ($search_per_type == 1 || $search_per_type==5)
			$arr_search_condition[] = "(trim(c.POS_NO) = '".trim($search_pos_no)."')";
		elseif ($search_per_type == 2) 
			$arr_search_condition[] = "(trim(d.POEM_NO) = '".trim($search_pos_no)."')";		
		elseif ($search_per_type == 3) 	
			$arr_search_condition[] = "(trim(e.POEMS_NO) = '".trim($search_pos_no)."')";
		elseif ($search_per_type == 4) 	
			$arr_search_condition[] = "(trim(j.POT_NO) = '".trim($search_pos_no)."')";
		else if ($search_per_type==0)		//ทั้งหมด
			$arr_search_condition[] = "((trim(c.POS_NO) = '".trim($search_pos_no)."') or (trim(d.POEM_NO) = '".trim($search_pos_no)."') or (trim(e.POEMS_NO) = '".trim($search_pos_no)."') or (trim(j.POT_NO) = '".trim($search_pos_no)."')) ";
	}*/
    
    /*if(trim($search_pos_no_name)){
		if ($search_per_type == 1 || $search_per_type==5)
			$arr_search_condition[] = "(trim(c.POS_NO_NAME) like '".trim($search_pos_no_name)."%')";
		elseif ($search_per_type == 2) 
			$arr_search_condition[] = "(trim(d.POEM_NO_NAME) like '".trim($search_pos_no_name)."%')";		
		elseif ($search_per_type == 3) 	
			$arr_search_condition[] = "(trim(e.POEMS_NO_NAME) like '".trim($search_pos_no_name)."%')";
		elseif ($search_per_type == 4) 	
			$arr_search_condition[] = "(trim(j.POT_NO_NAME) like '".trim($search_pos_no_name)."%')";
		else if ($search_per_type==0)		//ทั้งหมด
			$arr_search_condition[] = "((trim(c.POS_NO_NAME) like '".trim($search_pos_no_name)."%') or (trim(d.POEM_NO_NAME) like '".trim($search_pos_no_name)."%') or 
			(trim(e.POEMS_NO_NAME) like '".trim($search_pos_no_name)."%') or (trim(j.POT_NO_NAME) like '".trim($search_pos_no_name)."%')) ";
	}*/
    
    if(trim($search_pay_no))  $arr_search_condition[] = "(trim(c.POS_NO) = '$search_pay_no' and a.PER_TYPE = 1)";
    if(trim($search_level_no)) $arr_search_condition[] = "(trim(a.LEVEL_NO) = '". trim($search_level_no) ."')";
    if(trim($search_pl_code)) $arr_search_condition[] = "(trim(c.PL_CODE) = '". trim($search_pl_code) ."')";
    if(trim($search_pm_code)) $arr_search_condition[] = "(trim(c.PM_CODE) = '". trim($search_pm_code) ."')";
    if(trim($search_per_type)) 	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
    
    $condition_date = "";
    if($search_date_min && $search_date_max){ 
		 $tmpsearch_date_min =  save_date($search_date_min);
         $tmpsearch_date_max =  save_date($search_date_max);
         $arr_search_condition[] = " ( ot.OT_DATE BETWEEN  '$tmpsearch_date_min' and '$tmpsearch_date_max') ";
         $condition_date = " AND  ( OT_DATE BETWEEN  '$tmpsearch_date_min' and '$tmpsearch_date_max') ";
	}else if($search_date_min && empty($search_date_max)){ 
		 $tmpsearch_date_min =  save_date($search_date_min);
         $arr_search_condition[] = " (ot.OT_DATE='$tmpsearch_date_min') ";
         $condition_date = " AND (OT_DATE='$tmpsearch_date_min') ";
    }else if(empty($search_date_min) && $search_date_max){ 
		 $tmpsearch_date_max =  save_date($search_date_max);
         $arr_search_condition[] = " (ot.OT_DATE='$tmpsearch_date_max') ";
         $condition_date = " AND (OT_DATE='$tmpsearch_date_max') ";
    }
    
    /*เช็คการคำนวน Ot จากเจ้าของเรื่อง*/
    $condition_OT = "";
    if ($SESS_USERGROUP !=1 && $NAME_GROUP_HRD!='HRD'){
        	if($search_org_id_1){
     			$condition_OT = " AND DEPARTMENT_ID=" .$search_org_id." AND ORG_LOWER1=".$search_org_id_1;
            }else{
            	$condition_OT = " AND DEPARTMENT_ID=" .$search_org_id;
            }

    }else{ 
    	if($select_org_head=="0"){
        	if($search_org_id_1){
            	$condition_OT = " AND DEPARTMENT_ID=" .$search_org_id." AND ORG_LOWER1=".$search_org_id_1;
        	}elseif($search_org_id){
            	$condition_OT = " AND DEPARTMENT_ID=$search_org_id";
            }else{
            	$condition_OT = " AND ORG_ID=$search_department_id AND DEPARTMENT_ID=-1";
            }
     		
        }
        
    }

    
    
    $search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);


		$cmd = "	select	 q1.*
                             from (
                                 select 	distinct
                                 			ot.PER_ID,a.PER_TYPE,
                                            g.PN_SHORTNAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW,
                                            c.POS_NO,d.POEM_NO,e.POEMS_NO,
                                            (select min(OT_DATE) from TA_PER_OT 
                                               where  PER_ID=ot.PER_ID $condition_date $condition_OT) AS OT_MIN,
                                            (select max(OT_DATE) from TA_PER_OT 
                                               where PER_ID=ot.PER_ID $condition_date $condition_OT) AS OT_MAX,
                                            (select sum(NUM_HRS) from TA_PER_OT 
                                               where SET_FLAG !=0 AND PER_ID=ot.PER_ID $condition_date $condition_OT) AS NUM_HRS,
                                            (select sum(AMOUNT) from TA_PER_OT 
                                               where SET_FLAG !=0 AND PER_ID=ot.PER_ID $condition_date $condition_OT) AS AMOUNT,
											 c.PL_CODE, d.PN_CODE, e.EP_CODE,j.TP_CODE, c.ORG_ID,
                                              d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
                                              j.POT_ID AS POT_ORG_ID,j.POT_NO
                                            from  TA_PER_OT ot
                                            left join PER_PERSONAL a on(a.PER_ID=ot.PER_ID) 
                                            left join PER_ORG b on(b.ORG_ID=a.ORG_ID) 
                                            left join PER_POSITION c on(c.POS_ID=a.POS_ID) 
                                            left join PER_POS_EMP d on(d.POEM_ID=a.POEM_ID) 
                                            left join PER_POS_EMPSER e on(e.POEMS_ID=a.POEMS_ID) 
                                            left join PER_LEVEL f on(f.LEVEL_NO=a.LEVEL_NO) 
                                            left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE) 
                                            left join PER_MGT h on(h.PM_CODE=c.PM_CODE)
                                            left join PER_LINE i on(i.PL_CODE=c.PL_CODE)
                                            left join PER_POS_TEMP j on (j.POT_ID=a.POT_ID)
                                 where 		1=1
                                                $search_condition 
                                				
                            ) q1 
							order by 	q1.FULLNAME_SHOW ASC ";

//echo "<pre>".$cmd; die();
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	    $head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		if ($FLAG_RTF) {
			$RTF->add_header("", 0, false);	// header default
			$RTF->add_footer("", 0, false);		// footer default
			
	//		echo "$head_text1<br>";
		$tab_align = "center";
		$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, "", false, $tab_align);
		} else {
		$pdf->AutoPageBreak = false; 
	    $result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
		          }
	if (!$result) echo "****** error ****** on open table for $table<br>";

	if($count_data){
		$data_num = 0;
		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;			
			$data_num++;
			$arr_data = (array) null;
			
			$arr_data[] = $data_num;
			
			$DATA_FULLNAME_SHOW = trim($data[FULLNAME_SHOW]);
			$arr_data[] = $DATA_FULLNAME_SHOW;
			
			$DATA_PER_TYPE = trim($data[PER_TYPE]);
			if($DATA_PER_TYPE==1){ 
				$TMP_POS_NO = $data[POS_NO];
				$TMP_POS_NO_NAME = $data[POS_NO_NAME];
				$TMP_PER_TYPE = "ข้าราชการ";
				$TMP_ORG_ID = $data[ORG_ID];
				$TMP_ORG_ID_1 = $data[ORG_ID_1];
				$TMP_PL_CODE = $data[PL_CODE];
				$TMP_PT_CODE = trim($data[PT_CODE]);
				$TMP_PM_CODE = $data[PM_CODE];

				$cmd = " select PL_NAME from PER_LINE where PL_CODE='$TMP_PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$DATA_PL_NAME = $data2[PL_NAME];
				
				$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$TMP_PT_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_PT_NAME = $data2[PT_NAME];
				
				$cmd = " select PM_NAME from PER_MGT where PM_CODE='$TMP_PM_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_PM_NAME = $data2[PM_NAME];
				
				if ($RPT_N)
					$TMP_POSITION = (trim($TMP_PM_NAME) ?"$TMP_PM_NAME (":"") . (trim($TMP_PL_NAME)? "$TMP_PL_NAME$POSITION_LEVEL" : "") . (trim($TMP_PM_NAME) ?")":"");
				else
					$TMP_POSITION = (trim($TMP_PM_NAME) ?"$TMP_PM_NAME (":"") . (trim($TMP_PL_NAME)?($TMP_PL_NAME ." ". level_no_format($TMP_LEVEL_NO) . (($TMP_PT_NAME != "ทั่วไป" && $TMP_LEVEL_NO >= 6)?"$TMP_PT_NAME":"")):"") . (trim($TMP_PM_NAME) ?")":"");
			}elseif($DATA_PER_TYPE==2){ 
				$TMP_POS_NO = $data[POEM_NO];
				$TMP_POS_NO_NAME = $data[POEM_NO_NAME];
				$TMP_PER_TYPE = "ลูกจ้างประจำ";
				$TMP_ORG_ID = $data[EMP_ORG_ID];
				$TMP_PL_CODE = $data[PN_CODE];

				$cmd = " select PN_NAME from PER_POS_NAME where PN_CODE='$TMP_PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$DATA_PL_NAME = $data2[PN_NAME];

				$TMP_POSITION = $DATA_PL_NAME;
			} elseif ($DATA_PER_TYPE == 3) {
				$TMP_POS_NO = $data[POEMS_NO];
				$TMP_POS_NO_NAME = $data[POEMS_NO_NAME];
				$TMP_PER_TYPE = "พนักงานราชการ";
				$TMP_ORG_ID = $data[EMPS_ORG_ID];					
				$TMP_PL_CODE = $data[EP_CODE];

				$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE='$TMP_PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$DATA_PL_NAME = $data2[EP_NAME];

				$TMP_POSITION = $DATA_PL_NAME;
							
			} elseif ($DATA_PER_TYPE == 4) {
				$TMP_POS_NO = $data[POT_NO];
				$TMP_POS_NO_NAME = $data[POT_NO_NAME];
				$TMP_PER_TYPE = "ลูกจ้างชั่วคราว";
				$TMP_ORG_ID = $data[POT_ORG_ID];					
				$TMP_PL_CODE = $data[TP_CODE];

				$cmd = " select TP_NAME from PER_TEMP_POS_NAME where TP_CODE='$TMP_PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				//echo $cmd;
				$data2 = $db_dpis2->get_array();
				$DATA_PL_NAME = $data2[TP_NAME];

				$TMP_POSITION = $DATA_PL_NAME; 
			} // end if
			
			
			if($TMP_ORG_ID){
				$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$TMP_ORG_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$DATA_ORG_NAME = $data2[ORG_NAME];
			}
			$arr_data[] = $DATA_ORG_NAME;
			
			 if(!empty($data[NUM_HRS])){
				$DATA_NUM_HRS = number_format($data[NUM_HRS]);
			}else{
				$DATA_NUM_HRS ="";
			}
			$arr_data[] = $DATA_NUM_HRS;
			
			
			if(!empty($data[AMOUNT])){
				$DATA_AMOUNT = number_format($data[AMOUNT]);
			}else{
				$DATA_AMOUNT ="";
			}
			$arr_data[] = $DATA_AMOUNT;
			

			$data_align = array("C", "L", "L", "C", "C");
			  if ($FLAG_RTF)
			 $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		    else	
			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end while
	}else{
	      if ($FLAG_RTF){
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		  }else{
		$arr_data = (array) null;
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
                  }
		$data_align = array("C", "L", "L", "C", "C");
	    if ($FLAG_RTF)
	    $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "16", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		else	
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "16", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
	} // end if
	  if ($FLAG_RTF) {
			$RTF->close_tab(); 
//			$RTF->close_section(); 
			$RTF->display($fname);
		} else {
			$pdf->close_tab(""); 
			$pdf->close();
			$pdf->Output($fname,'D');	
		}
	ini_set("max_execution_time", 30);
?>