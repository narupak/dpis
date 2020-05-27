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
		$orientation='L';
		$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);
		$RTF->set_default_font($font, 14);
	//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

		$RTF->set_report_code($report_code);
		$RTF->set_report_title($report_title);
		$RTF->set_company_name($company_name);
		} else {
			$fname= "T0202.pdf";
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
	if ($FLAG_RTF) {
		$heading_width[0] = "23";
		$heading_width[1] = "56";
		$heading_width[2] = "77";
		$heading_width[3] = "27";
		$heading_width[4] = "27";
		$heading_width[5] = "76";
	}else{
		$heading_width[0] = "23";
		$heading_width[1] = "56";
		$heading_width[2] = "77";
		$heading_width[3] = "27";
		$heading_width[4] = "27";
		$heading_width[5] = "76";
	}	
	$heading_text[0] = "เลขที่ตำแหน่ง";
	$heading_text[1] = "ชื่อ-สกุล";
	$heading_text[2] = "ตำแหน่งในสายงาน-ระดับตำแหน่ง";
	$heading_text[3] = "วันที่เริ่มยกเว้น";
	$heading_text[4] = "วันที่สิ้นสุด";
	$heading_text[5] = "หมายเหตุ";
		
	$heading_align = array('C','C','C','C','C','C');
		
	$search_per_status = (isset($search_per_status))?  $search_per_status : 2;
	if(trim($search_per_status) < 5) {
          $temp_per_status = $search_per_status - 1;		
          $arr_search_condition[] = "(a.PER_STATUS = $temp_per_status)";	
    }
	
	 //ค้นเฉพาะของ กจ. สำนัก ไม่เอาจากกลุ่มแดมิน กับ กจ. กรม
	if ( ($SESS_USERGROUP ==1 || $NAME_GROUP_HRD =='HRD')  ){
		if($search_onlyme_flag==1){
		  $arr_search_condition[] = " (wch.CREATE_USER not in
						 (select	a.ID from	user_detail a, user_group b
					where a.group_id=b.id 
					AND (b.ID=1 OR b.CODE='HRD')
					
					) )";
		}
	
	}
	
  	if ( ($SESS_USERGROUP !=1 && $NAME_GROUP_HRD!='HRD')  ){
        $tCon="(";
        for ($i=0; $i < count($SESS_AuditArray); $i++) {
            if ($i>0)
                $tCon .= " or ";
            $tCon .= "(a.ORG_ID=" .$SESS_AuditArray[$i][0];
            if ($SESS_AuditArray[$i][1] != 0)
                $tCon .= ' and a.ORG_ID_1='. $SESS_AuditArray[$i][1];
            $tCon .= ")";
        }
        $tCon .= ")";
        $arr_search_condition[] = $tCon;
    	
        if($search_org_id && !$search_org_id_1 ){
        	$arr_search_condition[] = "(a.ORG_ID=$search_org_id )";
        }else if($search_org_id && $search_org_id_1){
        	$arr_search_condition[] = "(a.ORG_ID=$search_org_id AND a.ORG_ID_1=$search_org_id_1 )";
        }
       

        }else{
        	if($search_org_id_1){ /* Release 5.1.0.4 */
                if($select_org_structure==0){ //โครงสร้างตามกฎหมาย
                    $arr_search_condition[] = "(c.ORG_ID_1=$search_org_id_1 or d.ORG_ID_1=$search_org_id_1 or e.ORG_ID_1=$search_org_id_1 or j.ORG_ID_1=$search_org_id_1)";
                }else if($select_org_structure==1){ //โครงสร้างตามมอบหมายงาน
                    $arr_search_condition[] = "(a.ORG_ID_1=$search_org_id_1)";
                }
            
            }elseif($search_org_id){
                if($select_org_structure==0){
                    $arr_search_condition[] = "(c.ORG_ID=$search_org_id or d.ORG_ID=$search_org_id or e.ORG_ID=$search_org_id or j.ORG_ID=$search_org_id)";
                }else if($select_org_structure==1){
                    $arr_search_condition[] = "(a.ORG_ID=$search_org_id)";
                }
            }elseif($search_department_id){
                $arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
            }elseif($search_ministry_id){
                unset($arr_department);
                $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id and OL_CODE='02' ";
                if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
                $db_dpis->send_cmd($cmd);
                while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
                $arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
            }elseif($PROVINCE_CODE){
                $cmd = " select distinct ORG_ID_REF from PER_ORG where PV_CODE='$PROVINCE_CODE' and OL_CODE='03' ";
                if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
                $db_dpis->send_cmd($cmd);
                while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID_REF];
                $arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
            } // end if  
        
	
    	}

   
   	if(trim($search_name)) $arr_search_condition[] = "(a.PER_NAME like '".trim($search_name)."%' or UPPER(a.PER_ENG_NAME) like UPPER('".trim($search_name)."%'))";
   	 if(trim($search_surname)) $arr_search_condition[] = "(a.PER_SURNAME like '".trim($search_surname)."%' or UPPER(a.PER_ENG_SURNAME) like UPPER('".trim($search_surname)."%'))";
  

    if(trim($search_cardno)) $arr_search_condition[] = "(a.PER_CARDNO ='".trim($search_cardno)."')";
    if(trim($search_offno)) $arr_search_condition[] = "(a.PER_OFFNO ='".trim($search_offno)."')";
    
    if(trim($search_pos_no))  {	
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
	}
    
    if(trim($search_pos_no_name)){
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
	}
    
    if(trim($search_pay_no))  $arr_search_condition[] = "(trim(c.POS_NO) = '$search_pay_no' and a.PER_TYPE = 1)";
    if(trim($search_level_no)) $arr_search_condition[] = "(trim(a.LEVEL_NO) = '". trim($search_level_no) ."')";
    if(trim($search_pl_code)) $arr_search_condition[] = "(trim(c.PL_CODE) = '". trim($search_pl_code) ."')";
    if(trim($search_pm_code)) $arr_search_condition[] = "(trim(c.PM_CODE) = '". trim($search_pm_code) ."')";
    if(trim($search_per_type)) 	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
    
    if($search_date_min && $search_date_max){ 
		 $tmpsearch_date_min =  save_date($search_date_min);
         $tmpsearch_date_max =  save_date($search_date_max);
         $arr_search_condition[] = " (   (substr(wch.START_DATE,1,10)  BETWEEN '$tmpsearch_date_min' and '$tmpsearch_date_max') 
         					or  (substr(wch.END_DATE,1,10) BETWEEN '$tmpsearch_date_min' and '$tmpsearch_date_max' ) 
                            or ( '$tmpsearch_date_min'  BETWEEN substr(wch.START_DATE,1,10) and substr(wch.END_DATE,1,10) )
    						or ( '$tmpsearch_date_max'  BETWEEN substr(wch.START_DATE,1,10) and substr(wch.END_DATE,1,10) )
                            )";
	}else if($search_date_min && empty($search_date_max)){ 
		 $tmpsearch_date_min =  save_date($search_date_min);
         $arr_search_condition[] = " ('$tmpsearch_date_min' BETWEEN substr(wch.START_DATE,1,10) and  ( CASE WHEN wch.END_DATE IS NOT NULL THEN substr(wch.END_DATE,1,10) ELSE to_char(sysdate,'yyyy-mm-dd') END) ) ";
    }else if(empty($search_date_min) && $search_date_max){ 
		 $tmpsearch_date_max =  save_date($search_date_max);
         $arr_search_condition[] = " ('$tmpsearch_date_max' BETWEEN substr(wch.START_DATE,1,10) and ( CASE WHEN wch.END_DATE IS NOT NULL THEN substr(wch.END_DATE,1,10) ELSE to_char(sysdate,'yyyy-mm-dd') END) ) ";
    }

    
    
    $search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

	$Tmpsearch_CANCEL_FLAG = "";
    if($search_CANCEL_FLAG==1 || empty($search_CANCEL_FLAG)) {  
    		$Tmpsearch_CANCEL_FLAG = " AND  wch.CANCEL_FLAG=1 ";  
    }else if($search_CANCEL_FLAG==2) {  
            $Tmpsearch_CANCEL_FLAG = " AND  wch.CANCEL_FLAG=0 AND wch.REC_ID =(select max(REC_ID) as REC_ID FROM TA_SET_EXCEPTPER WHERE PER_ID=wch.PER_ID)  ";
    }else if($search_CANCEL_FLAG==3) {  
            $Tmpsearch_CANCEL_FLAG = " AND wch.REC_ID =(select max(REC_ID) as REC_ID FROM TA_SET_EXCEPTPER WHERE PER_ID=wch.PER_ID)  ";
	}
	
	if($order_by==1){	//(ค่าเริ่มต้น)
		$order_str = "ORDER BY to_number(replace(c.POS_NO,'-','')) ".$SortType[$order_by].", to_number(replace(d.POEM_NO,'-','')) ".$SortType[$order_by].", to_number(replace(e.POEMS_NO,'-','')) ".$SortType[$order_by].", to_number(replace(j.POT_NO,'-','')) ".$SortType[$order_by];
	}elseif($order_by==2) {	//
		$order_str = "ORDER BY a.PER_NAME ".$SortType[$order_by].", a.PER_SURNAME ".$SortType[$order_by];
	} elseif($order_by==3) {	
		$order_str = "ORDER BY a.POS_ID ".$SortType[$order_by].", a.POEMS_ID ".$SortType[$order_by].", a.POEM_ID ".$SortType[$order_by];
	} elseif($order_by==4) {	     
		$order_str = "ORDER BY f.LEVEL_NAME ".$SortType[$order_by];
	} elseif($order_by==5) {	     
		$order_str = "ORDER BY a.DEPARTMENT_ID ".$SortType[$order_by];
	 } elseif($order_by==6) {	     
		$order_str = "ORDER BY a.DEPARTMENT_ID_ASS ".$SortType[$order_by];
	} elseif($order_by==7) {	//
		$order_str = "ORDER BY wch.START_DATE ".$SortType[$order_by];
	} elseif($order_by==8) {	//
		$order_str = "ORDER BY wch.END_DATE ".$SortType[$order_by];
	}
	
	$cmd = "select 	distinct
			(select ex1.REC_ID from TA_SET_EXCEPTPER ex1 WHERE ex1.REC_ID  in (select max(ex2.REC_ID) from TA_SET_EXCEPTPER ex2 WHERE ex2.PER_ID=a.PER_ID) ) as REC_ID,
			a.PER_TYPE,
			g.PN_SHORTNAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW,
			(select ex1.START_DATE from TA_SET_EXCEPTPER ex1 WHERE ex1.REC_ID  in (select max(ex2.REC_ID) from TA_SET_EXCEPTPER ex2 WHERE ex2.PER_ID=a.PER_ID) ) as START_DATE,
			(select ex1.END_DATE from TA_SET_EXCEPTPER ex1 WHERE ex1.REC_ID  in (select max(ex2.REC_ID) from TA_SET_EXCEPTPER ex2 WHERE ex2.PER_ID=a.PER_ID) ) as END_DATE,
			(select ex1.REMARK from TA_SET_EXCEPTPER ex1 WHERE ex1.REC_ID  in (select max(ex2.REC_ID) from TA_SET_EXCEPTPER ex2 WHERE ex2.PER_ID=a.PER_ID) ) as REMARK,
			c.POS_NO,d.POEM_NO,e.POEMS_NO,f.LEVEL_NAME,
			a.POS_ID,a.POEM_ID, a.POEMS_ID, a.POT_ID,a.DEPARTMENT_ID,
			a.DEPARTMENT_ID_ASS ,a.ORG_ID,a.ORG_ID_1, a.ORG_ID_2,
			wch.CANCEL_FLAG,wch.PER_ID,j.POT_NO,a.PER_STATUS, a.PER_CARDNO,
			(select ex1.CREATE_USER from TA_SET_EXCEPTPER ex1 WHERE ex1.REC_ID  in (select max(ex2.REC_ID) from TA_SET_EXCEPTPER ex2 WHERE ex2.PER_ID=a.PER_ID) ) as CREATE_USER
			from  PER_PERSONAL a  
			left join TA_SET_EXCEPTPER wch on(a.PER_ID=wch.PER_ID)
			left join PER_ORG b on(b.ORG_ID=a.ORG_ID) 
			left join PER_POSITION c on(c.POS_ID=a.POS_ID) 
			left join PER_POS_EMP d on(d.POEM_ID=a.POEM_ID) 
			left join PER_POS_EMPSER e on(e.POEMS_ID=a.POEMS_ID) 
			left join PER_LEVEL f on(f.LEVEL_NO=a.LEVEL_NO) 
			left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE) 
			left join PER_MGT h on(h.PM_CODE=c.PM_CODE)
			left join PER_LINE i on(i.PL_CODE=c.PL_CODE)
			left join PER_POS_TEMP j on (j.POT_ID=a.POT_ID)
			where 	1=1
			$Tmpsearch_CANCEL_FLAG
			$search_condition
			$order_str ";

		// $cmd = "	select		wch.REC_ID,a.PER_TYPE,
        //                                     g.PN_SHORTNAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW,
        //                                     wch.START_DATE,wch.END_DATE,wch.REMARK,
        //                                     c.POS_NO,d.POEM_NO,e.POEMS_NO,f.POSITION_LEVEL,
        //                                     a.POS_ID,a.POEM_ID, a.POEMS_ID, a.POT_ID,a.DEPARTMENT_ID,
        //                                     a.DEPARTMENT_ID_ASS ,a.ORG_ID,a.ORG_ID_1, a.ORG_ID_2,
        //                                     wch.CANCEL_FLAG,wch.PER_ID,j.POT_NO,a.PER_STATUS
        //                                     from  PER_PERSONAL a  
        //                                     left join TA_SET_EXCEPTPER wch on(a.PER_ID=wch.PER_ID)
        //                                     left join PER_ORG b on(b.ORG_ID=a.ORG_ID) 
        //                                     left join PER_POSITION c on(c.POS_ID=a.POS_ID) 
        //                                     left join PER_POS_EMP d on(d.POEM_ID=a.POEM_ID) 
        //                                     left join PER_POS_EMPSER e on(e.POEMS_ID=a.POEMS_ID) 
        //                                     left join PER_LEVEL f on(f.LEVEL_NO=a.LEVEL_NO) 
        //                                     left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE) 
        //                                     left join PER_MGT h on(h.PM_CODE=c.PM_CODE)
        //                                     left join PER_LINE i on(i.PL_CODE=c.PL_CODE)
        //                                     left join PER_POS_TEMP j on (j.POT_ID=a.POT_ID)
        //                          where 	wch.REC_ID  in (select max(REC_ID) from TA_SET_EXCEPTPER WHERE PER_ID=wch.PER_ID)
		// 						 			$Tmpsearch_CANCEL_FLAG
        //                                     $search_condition
		// 					order by 	to_number(replace(c.POS_NO,'-','')) ASC, to_number(replace(d.POEM_NO,'-','')) ASC, to_number(replace(e.POEMS_NO,'-','')) ASC, to_number(replace(j.POT_NO,'-','')) ASC ";


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

		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;			

			$arr_data = (array) null;
			$DATA_POS_NO = trim($data[POS_NO]).trim($data[POEM_NO]).trim($data[POEMS_NO]).trim($data[POT_NO]);
			if($data[PER_STATUS] == 2){		//พ้นจากส่วนราชการ
                 $DATA_POS_NO = "";
            } // end if	
			$arr_data[] = $DATA_POS_NO;
			
			$arr_data[] = $data[FULLNAME_SHOW];
			
			$DATA_LEVEL_NAME = trim($data[POSITION_LEVEL]); 
			$DATA_PER_TYPE = trim($data[PER_TYPE]); 
			$DATA_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]); 
			$DATA_ORG_ASS_ID = trim($data[ORG_ID]);
			$DATA_ORG_ASS_ID_1 = trim($data[ORG_ID_1]);
			$DATA_ORG_ASS_ID_2 = trim($data[ORG_ID_2]);
			$DATA_CANCEL_FLAG = trim($data[CANCEL_FLAG]);
			$DATA_PER_ID = $data[PER_ID];
			
			if ($DATA_PER_TYPE == 1 || $DATA_PER_TYPE == 5) {
                    $DATA_POS_ID = $data[POS_ID];
                    if ($DATA_POS_ID) {
                        $cmd = " 	select		POS_NO_NAME, POS_NO, pl.PL_NAME, po.ORG_NAME, pp.PT_CODE, po.ORG_ID, pp.PM_CODE, pp.ORG_ID_1, pp.ORG_ID_2
                                            from		PER_POSITION pp, PER_LINE pl, PER_ORG po
                                            where		pp.POS_ID=$DATA_POS_ID and pp.ORG_ID=po.ORG_ID and pp.PL_CODE=pl.PL_CODE ";
                        $db_dpis2->send_cmd($cmd);
                        $data2 = $db_dpis2->get_array();
                        $DATA_POSEM_NO = $data2[POS_NO_NAME].' '.$data2[POS_NO];
                        if ($MUNICIPALITY_FLAG==1) $DATA_POSEM_NO = pos_no_format($DATA_POSEM_NO,2);
                        $DATA_PL_NAME = $data2[PL_NAME];
                        $DATA_PT_CODE = trim($data2[PT_CODE]);
                        $DATA_PT_NAME = trim($data2[PT_NAME]);
                        $DATA_PM_CODE = trim($data2[PM_CODE]);
                        $DATA_ORG_NAME = trim($data2[ORG_NAME]);
                        $DATA_ORG_ID_1 = trim($data2[ORG_ID_1]);
                        $DATA_ORG_ID_2 = trim($data2[ORG_ID_2]);
    
                        $cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$DATA_PM_CODE'  ";
                        $db_dpis2->send_cmd($cmd);
                        $data2 = $db_dpis2->get_array();
                        $DATA_PM_NAME = trim($data2[PM_NAME]);
                        if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง" && !$DATA_PM_NAME) $DATA_PM_NAME = $DATA_PL_NAME;
                    }
			
                    $DATA_PAY_ID = $data[PAY_ID];
                    if ($DATA_PAY_ID) {
                        $cmd = " 	select		POS_NO_NAME, POS_NO, po.ORG_NAME
                                            from		PER_POSITION pp, PER_ORG po
                                            where		pp.POS_ID=$DATA_PAY_ID and pp.ORG_ID=po.ORG_ID ";
                        if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
                        $db_dpis2->send_cmd($cmd);
                        $data2 = $db_dpis2->get_array();
                        $DATA_PAY_NO = $data2[POS_NO_NAME] . $data2[POS_NO] . " " . $data2[ORG_NAME];
                    }
			
            } elseif ($DATA_PER_TYPE == 2) {
                $DATA_POEM_ID = $data[POEM_ID];
                if ($DATA_POEM_ID) {
                    $cmd = " 	select		POEM_NO_NAME, POEM_NO, pl.PN_NAME, po.ORG_NAME, po.ORG_ID, pp.ORG_ID_1, pp.ORG_ID_2, pl.PG_CODE   
                                    from			PER_POS_EMP pp, PER_POS_NAME pl, PER_ORG po 
                                    where		pp.POEM_ID=$DATA_POEM_ID and pp.ORG_ID=po.ORG_ID and pp.PN_CODE=pl.PN_CODE  ";
                    $db_dpis2->send_cmd($cmd);
                    $data2 = $db_dpis2->get_array();
                    $DATA_POSEM_NO = trim($data2[POEM_NO_NAME]).trim($data2[POEM_NO]);
                    $DATA_PL_NAME = trim($data2[PN_NAME]);
                    $DATA_PG_CODE = trim($data2[PG_CODE]);
                    $DATA_ORG_NAME = trim($data2[ORG_NAME]);
                    $DATA_ORG_ID_1 = trim($data2[ORG_ID_1]);
                    $DATA_ORG_ID_2 = trim($data2[ORG_ID_2]);
    
                    $cmd = " 	select PG_NAME from PER_POS_GROUP where trim(PG_CODE)='$DATA_PG_CODE'  ";
                    $db_dpis2->send_cmd($cmd);
                    $data2 = $db_dpis2->get_array();
                    $DATA_PM_NAME = trim($data2[PG_NAME]);
                }
            } elseif ($DATA_PER_TYPE == 3) {
                $DATA_POEMS_ID = $data[POEMS_ID];
                if ($DATA_POEMS_ID) {
                    $cmd = " 	select		POEMS_NO, pl.EP_NAME, po.ORG_NAME, po.ORG_ID, pp.ORG_ID_1, pp.ORG_ID_2, le.LEVEL_NAME  
                            from			PER_POS_EMPSER pp, PER_EMPSER_POS_NAME pl, PER_ORG po, PER_LEVEL le
                            where		pp.POEMS_ID=$DATA_POEMS_ID and pp.ORG_ID=po.ORG_ID and pp.EP_CODE=pl.EP_CODE  and pl.LEVEL_NO = le.LEVEL_NO";
                    $db_dpis2->send_cmd($cmd);
                    $data2 = $db_dpis2->get_array();
                    $DATA_POSEM_NO = trim($data2[POEMS_NO]);
                    $DATA_PL_NAME = trim($data2[EP_NAME]);
                    $DATA_PM_NAME = trim($data2[LEVEL_NAME]);
                    $DATA_ORG_NAME = trim($data2[ORG_NAME]);
                    $DATA_ORG_ID_1 = trim($data2[ORG_ID_1]);
                    $DATA_ORG_ID_2 = trim($data2[ORG_ID_2]);
                }
            } elseif ($DATA_PER_TYPE == 4) {
                $DATA_POT_ID = $data[POT_ID];
                if ($DATA_POT_ID) {
                    $cmd = " 	select		POT_NO, pl.TP_NAME, po.ORG_NAME, po.ORG_ID, pp.ORG_ID_1, pp.ORG_ID_2   
                                    from			PER_POS_TEMP pp, PER_TEMP_POS_NAME pl, PER_ORG po 
                                    where		pp.POT_ID=$DATA_POT_ID and pp.ORG_ID=po.ORG_ID and pp.TP_CODE=pl.TP_CODE  ";
                    $db_dpis2->send_cmd($cmd);
                    $data2 = $db_dpis2->get_array();
                    $DATA_POSEM_NO = trim($data2[POT_NO]);
                    $DATA_PL_NAME = trim($data2[TP_NAME]);
                    $DATA_ORG_NAME = trim($data2[ORG_NAME]);
                    $DATA_ORG_ID_1 = trim($data2[ORG_ID_1]);
                    $DATA_ORG_ID_2 = trim($data2[ORG_ID_2]);
                }
            }
            
            //สำหรับมอบหมายงาน
            //################################
            if ($DATA_ORG_ASS_ID) {
                $cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$DATA_ORG_ASS_ID ";
                $db_dpis2->send_cmd($cmd);
                $data2 = $db_dpis2->get_array();
                $DATA_ORG_ASS_NAME_0 = $data2[ORG_NAME];
                if ($DATA_ORG_ASS_NAME_0=="-") $DATA_ORG_ASS_NAME_0 = "";
            }
            if ($DATA_ORG_ASS_ID_1) {
                $cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$DATA_ORG_ASS_ID_1 ";
                $db_dpis2->send_cmd($cmd);
                $data2 = $db_dpis2->get_array();
                $DATA_ORG_ASS_NAME_1 = $data2[ORG_NAME];
            }
            if ($DATA_ORG_ASS_ID_2) {
                $cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$DATA_ORG_ASS_ID_2 ";
                $db_dpis2->send_cmd($cmd);
                $data2 = $db_dpis2->get_array();
                $DATA_ORG_ASS_NAME_2 = $data2[ORG_NAME];
            }
			
			if($DATA_ORG_ASS_NAME_1){
				$DATA_ORG_ASS_NAME = $DATA_ORG_ASS_NAME_0. "/".$DATA_ORG_ASS_NAME_1;
			
			}else{
				$DATA_ORG_ASS_NAME = $DATA_ORG_ASS_NAME_0;
			
			}

			
			
			$arr_data[] = $DATA_PL_NAME." ".$DATA_LEVEL_NAME;
			//$arr_data[] = $DATA_LEVEL_NAME;
			//$arr_data[] = $DATA_ORG_NAME;
			//$arr_data[] = $DATA_ORG_ASS_NAME;
			
			$DATA_START_DATE = show_date_format($data[START_DATE], $DATE_DISPLAY);
			$arr_data[] = $DATA_START_DATE;
			
			$DATA_END_DATE ="";
			if($data[END_DATE]){
				$DATA_END_DATE = show_date_format(substr($data[END_DATE],0,10), $DATE_DISPLAY);
			}
			$arr_data[] = $DATA_END_DATE;

			$arr_data[] = $data[REMARK];
			

			$data_align = array("C", "L", "L", "L", "L", "L");
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
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
                  }
		$data_align = array("C", "C", "C", "C","C", "C");
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