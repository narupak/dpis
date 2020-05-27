<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
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
			$fname= "T0301.pdf";
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
		$heading_width[0] = "35";
		$heading_width[1] = "55";
		$heading_width[2] = "35";
		$heading_width[3] = "25";
		$heading_width[4] = "25";
		$heading_width[5] = "25";
	}else{
		$heading_width[0] = "35";
		$heading_width[1] = "55";
		$heading_width[2] = "35";
		$heading_width[3] = "25";
		$heading_width[4] = "25";
		$heading_width[5] = "25";
	}	
	$heading_text[0] = "วัน-เวลาที่สแกน";
	$heading_text[1] = "ชื่อ-สกุล";
	$heading_text[2] = "ประเภทการลา";
	$heading_text[3] = "วันที่เริ่มต้น";
	$heading_text[4] = "วันที่สิ้นสุด";
	$heading_text[5] = "จำนวนวัน";
		
	$heading_align = array('C','C','C','C','C','C');
		

  /*	switch($CTRL_TYPE){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			$PROVINCE_CODE = $PROVINCE_CODE;
			$PROVINCE_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			$MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
			$MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){								
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			$PROVINCE_CODE = $PROVINCE_CODE;
			$PROVINCE_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			$MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
			$MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			$MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
			$MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			$MAIN_ORG_ID = $ORG_ID;
			$MAIN_ORG_NAME = $ORG_NAME;
			break;
		case 6 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$search_org_id_1 = $ORG_ID_1;
			$search_org_name_1 = $ORG_NAME_1;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			$MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
			$MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			$MAIN_ORG_ID = $ORG_ID;
			$MAIN_ORG_NAME = $ORG_NAME;
			break;
	} // end switch case*/
    
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
    

    
    //-ถ้าไม่ใช่กลุ่ม admin กับกลุ่ม กจ. กรม จะเอาหน่วยงานตามหมอบหมายงาน
 
   /* if ( ($SESS_USERGROUP !=1 && $NAME_GROUP_HRD!='HRD') ){
    	$select_org_structure=1;
        $PER_AUDIT_FLAG=1;
        if(empty($Submit3) && empty($image2) ){
           
            $search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
            
           if($SESS_AuditArray[0][0] <>0){

                $cmd_ass = " select ORG_ID,ORG_NAME from PER_ORG_ASS where ORG_ID = ".$SESS_AuditArray[0][0]; 
                $db_dpis2->send_cmd($cmd_ass);
                $data_ass = $db_dpis2->get_array();
                $search_org_name = $data_ass[ORG_NAME];
                $search_org_id = $data_ass[ORG_ID];
            }
            
            if($SESS_AuditArray[0][1] <>0){
				 $cmd_l1 = " select ORG_ID,ORG_NAME from PER_ORG_ASS where ORG_ID=".$SESS_AuditArray[0][1]; 
                $db_dpis2->send_cmd($cmd_l1);
                $data_l1 = $db_dpis2->get_array();
                $search_org_name_1 = $data_l1[ORG_NAME];
                $search_org_id_1 = $data_l1[ORG_ID];
             }
            
            

            
            
        }
        
    }*/

   
    if ( ($SESS_USERGROUP !=1 && $NAME_GROUP_HRD!='HRD')  ){
		$select_org_structure=1;
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
                
            if($search_org_id_1 && !$search_org_id_2){ /* Release 5.1.0.4 */
                if($select_org_structure==0){ //โครงสร้างตามกฎหมาย
                    $arr_search_condition[] = "(c.ORG_ID_1=$search_org_id_1 or d.ORG_ID_1=$search_org_id_1 or e.ORG_ID_1=$search_org_id_1 or j.ORG_ID_1=$search_org_id_1)";
                }else if($select_org_structure==1){ //โครงสร้างตามมอบหมายงาน
                    $arr_search_condition[] = "(a.ORG_ID_1=$search_org_id_1)";
                }
            }elseif($search_org_id_1 && $search_org_id_2){ /* Release 5.1.0.4 */
                if($select_org_structure==0){ //โครงสร้างตามกฎหมาย
                    $arr_search_condition[] = "(c.ORG_ID_2=$search_org_id_2 or d.ORG_ID_2=$search_org_id_2 or e.ORG_ID_2=$search_org_id_2 or j.ORG_ID_2=$search_org_id_2)";
                }else if($select_org_structure==1){ //โครงสร้างตามมอบหมายงาน
                    $arr_search_condition[] = "(a.ORG_ID_2=$search_org_id_2)";
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
    

    if(trim($search_per_type)) 	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
    
    if($search_date_min && $search_date_max){ 
		 $tmpsearch_date_min =  save_date($search_date_min)." 00:00:00";
         $tmpsearch_date_max =  save_date($search_date_max)." 23:59:59";
         $tmpsearch_date_max1 =  save_date($search_date_max);
         
         $arr_search_condition[] = " (att.TIME_STAMP  BETWEEN to_date('$tmpsearch_date_min','yyyy-mm-dd hh24:mi:ss')   AND to_date('$tmpsearch_date_max','yyyy-mm-dd hh24:mi:ss')) ";
       
	}

    $search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);



		$cmd = " 
					select		 q1.*
                             from (
					select 	DISTINCT
                                        abs.PER_ID,abs.ABS_STARTDATE,abs.ABS_STARTPERIOD,abs.ABS_ENDDATE,abs.ABS_ENDPERIOD,
                                        TO_CHAR(att.TIME_STAMP,'yyyy-mm-dd') AS TIME_STAMP,
										(SELECT TO_CHAR(min(TIME_STAMP),'HH24:MI:SS') 
                                        		FROM PER_TIME_ATTENDANCE  
                 								where PER_ID=abs.PER_ID AND TO_CHAR(TIME_STAMP,'yyyy-mm-dd') =TO_CHAR(att.TIME_STAMP,'yyyy-mm-dd')
                                                ) AS HHII ,
                                        a.PER_TYPE,g.PN_SHORTNAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW,
                                         c.POS_NO,d.POEM_NO,e.POEMS_NO,k.AB_NAME,j.POT_NO,abs.ABS_DAY
                                from  PER_ABSENTHIS  abs 
                                left join PER_TIME_ATTENDANCE att on(att.PER_ID=abs.PER_ID AND
												( TO_CHAR(att.TIME_STAMP,'yyyy-mm-dd')  BETWEEN substr(abs.ABS_STARTDATE,1,10)AND substr(abs.ABS_ENDDATE,1,10)))
                                left join PER_PERSONAL a on(a.PER_ID=abs.PER_ID)
                                left join PER_ORG b on(b.ORG_ID=a.ORG_ID) 
                                left join PER_POSITION c on(c.POS_ID=a.POS_ID) 
                                left join PER_POS_EMP d on(d.POEM_ID=a.POEM_ID) 
                                left join PER_POS_EMPSER e on(e.POEMS_ID=a.POEMS_ID) 
                                left join PER_LEVEL f on(f.LEVEL_NO=a.LEVEL_NO) 
                                left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE) 
                                left join PER_MGT h on(h.PM_CODE=c.PM_CODE)
                                left join PER_LINE i on(i.PL_CODE=c.PL_CODE)
                                left join PER_POS_TEMP j on (j.POT_ID=a.POT_ID)
                                left join PER_ABSENTTYPE k on (k.AB_CODE=abs.AB_CODE)
                                left join PER_WORK_CYCLEHIS his on ( his.PER_ID=a.PER_ID and (att.TIME_STAMP between to_date(his.START_DATE, 'YYYY-MM-DD hh24:mi:ss') and to_date(his.END_DATE,'YYYY-MM-DD hh24:mi:ss')) )
                                left join PER_WORK_CYCLE l on (l.wc_code=his.wc_code)
                                
                            WHERE abs.AB_CODE NOT IN(10,13) AND att.TIME_STAMP IS NOT NULL
                            $search_condition
                            
                            AND (abs.ABS_ENDPERIOD = 3 or 
                                 (abs.ABS_ENDPERIOD = 1 and att.TIME_STAMP <=
                                  TRUNC(att.TIME_STAMP)+(((to_number(substr(l.End_LateTime,1,2))*60)+to_number(substr(l.End_LateTime,3,2)))/1440)+(59/86400) 
                                 ) or
                                 (abs.ABS_ENDPERIOD = 2 and att.TIME_STAMP >=
                                 
                                    TRUNC(att.TIME_STAMP)+(((to_number(substr(l.End_LateTime,1,2))*60)+to_number(substr(l.End_LateTime,3,2)))/1440)+(59/86400) 
                                 
                                   and ((SELECT max(TIME_STAMP)	FROM PER_TIME_ATTENDANCE  
                                          where PER_ID=abs.PER_ID AND TO_CHAR(TIME_STAMP,'yyyy-mm-dd')=TO_CHAR(att.TIME_STAMP,'yyyy-mm-dd')
                                        ) =
                                        (SELECT min(TIME_STAMP)	FROM PER_TIME_ATTENDANCE  
                                          where PER_ID=abs.PER_ID AND TO_CHAR(TIME_STAMP,'yyyy-mm-dd')=TO_CHAR(att.TIME_STAMP,'yyyy-mm-dd')
                                        )
                                        or
                                        (SELECT max(TIME_STAMP)	FROM PER_TIME_ATTENDANCE  
                                          where PER_ID=abs.PER_ID AND TO_CHAR(TIME_STAMP,'yyyy-mm-dd')=TO_CHAR(att.TIME_STAMP,'yyyy-mm-dd')
                                        ) >= (case when l.wc_code=-1 
                                                then (SELECT min(TIME_STAMP)	FROM PER_TIME_ATTENDANCE  
                                                        where PER_ID=abs.PER_ID AND TO_CHAR(TIME_STAMP,'yyyy-mm-dd')=TO_CHAR(att.TIME_STAMP,'yyyy-mm-dd')
                                                      )+(8/24) 
                                                else TRUNC(att.TIME_STAMP)+NextDay_Exit+((to_number(substr(l.WC_End,1,2))*60)+to_number(substr(l.WC_End,3,2))/1440)
                                             end)
                                      ) 
                                 )
                                )
                            
						) q1  ORDER BY q1.TIME_STAMP,q1.HHII
						";
//echo  "<pre>".$cmd; die();

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
			
			$DATA_TIME_STAMP = show_date_format($data[TIME_STAMP], $DATE_DISPLAY);
			$TMP_HHII = $data[HHII];
			$arr_data[] =$DATA_TIME_STAMP." ".$TMP_HHII;
			
			$DATA_FULLNAME_SHOW = trim($data[FULLNAME_SHOW]);
			$arr_data[] = $DATA_FULLNAME_SHOW;
			
			$DATA_AB_NAME = trim($data[AB_NAME]); 
            $arr_data[] = $DATA_AB_NAME; 
			 
			 $DATA_ABS_STARTDATE = show_date_format($data[ABS_STARTDATE], $DATE_DISPLAY); 
			 $arr_data[] = $DATA_ABS_STARTDATE;  
			 
			 $DATA_ABS_ENDDATE = show_date_format($data[ABS_ENDDATE], $DATE_DISPLAY); 
			 $arr_data[] = $DATA_ABS_ENDDATE;  
			 
			 $DATA_ABS_STARTPERIOD ="";
			if($data[ABS_STARTPERIOD]=="1"){
				$DATA_ABS_STARTPERIOD =" (ครึ่งเช้า)";
			}elseif($data[ABS_STARTPERIOD]=="2"){
				$DATA_ABS_STARTPERIOD =" (ครึ่งบ่าย)";
			}
			if($data[ABS_DAY]!="0.5"){
				$DATA_ABS_STARTPERIOD ="";
			}
			
			$DATA_ABS_DAY = trim(round($data[ABS_DAY],2)).$DATA_ABS_STARTPERIOD;
			$arr_data[] = $DATA_ABS_DAY;  
			

			$data_align = array("C", "L", "L", "C", "C", "C");
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
		$data_align = array("C", "C", "C","C", "C","C");
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