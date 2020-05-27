<?php
	include("../../php_scripts/connect_database.php");
        if (!$FLAG_RTF){ include("../php_scripts/pdf_wordarray_thaicut.php");}
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
        
        if($_GET[NUMBER_DISPLAY]){	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];}
        
        $DEBUG=0;
        if($DEBUG==1){
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        }
        
	if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
		require("../../RTF/rtf_class.php");
	} else	 {
		define('FPDF_FONTPATH','../../PDF/font/');
		include ("../../PDF/fpdf.php");
		include ("../../PDF/pdf_extends_DPIS.php");
	}
        
	ini_set("max_execution_time", $max_execution_time);
	
        $db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_code = "b.PL_CODE";
		$line_name = "b.PL_NAME";
		$line_short_name = "PL_SHORTNAME";
                $line_active = "PL_ACTIVE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "b.PN_CODE";
		$line_name = "b.PN_NAME";
                $line_active = "PN_ACTIVE";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "b.EP_CODE";
		$line_name = "b.EP_NAME";
                $line_active = "EP_ACTIVE";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "b.TP_CODE";
		$line_name = "b.TP_NAME";
                $line_active = "TP_ACTIVE";
	} // end
        
        
        function get_name_org($org_id,$name,$select_org_structure){
            global $db_dpis2;
            if($name=='กระทรวง' && $select_org_structure==1){
                $cmd = "WITH p AS (
                            SELECT ORG_ID_REF from PER_ORG WHERE ORG_ID = ".$org_id." AND ORG_ACTIVE = 1 
                        ) , TB_NAME AS (
                            SELECT a.ORG_NAME FROM PER_ORG a, p WHERE a.ORG_ID=p.ORG_ID_REF
                        ) SELECT ORG_NAME FROM TB_NAME";
            }else{
                $cmd  = " SELECT ORG_NAME FROM PER_ORG WHERE ORG_ID = ".$org_id." AND ORG_ACTIVE = 1 ";
            }    
            if($name != 'กระทรวง' && $select_org_structure==1){
                $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
            }
            $db_dpis2->send_cmd($cmd);
            $data = $db_dpis2->get_array();
            if($data[ORG_NAME]){
                return $data[ORG_NAME];
            }else{
                return 'ไม่ระบุ'.$name;
            }
        }
        function get_name_line($pl_code,$name){
            global $db_dpis2 ,$line_name ,$line_code , $line_active ,$line_table;
            $cmd  = " SELECT $line_name as PL_NAME from $line_table b where $line_code = ".$pl_code."  and  $line_active = 1 ";
            $db_dpis2->send_cmd($cmd);
            $data = $db_dpis2->get_array();
            if($data[PL_NAME]){
                return $data[PL_NAME];
            }else{
                return 'ไม่ระบุ'.$name;
            }
        }
        
	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {
		$f_all = true; 
		$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
        } else {$f_all = false;	}

	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)==""){ $RPTORD_LIST .= "ORG_1|";}
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)==""){ $RPTORD_LIST .= "ORG_2|";}
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!=""){ $RPTORD_LIST .= "LINE|";}
	} // end if
	$arr_rpt_order_setnew = explode("|", $RPTORD_LIST);
	$arr_rpt_order_tmp_setnew = array_unique($arr_rpt_order_setnew);	//ตัดค่าที่ซ้ำกันออก เพื่อไม่ให้ขึ้นข้อมูลซ้ำกัน 2 แถว	และเรียงตำแหน่ง index ใหม่ 0 1 2 ...
	foreach($arr_rpt_order_tmp_setnew as $key=>$value){
		$arr_rpt_order[]=$value;
	}
	unset($arr_rpt_order_setnew);	unset($arr_rpt_order_tmp_setnew);

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
            $REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
            switch($REPORT_ORDER){
                case "MINISTRY" : 
                    if($select_list){ $select_list .= ", ";}
                    $select_list .= " p.MINISTRY_ID";	

                    if($select_list1){ $select_list1 .= ", ";}
                    $select_list1 .= " NVL(p.MINISTRY_ID,NULL) as MINISTRY_ID";

                    if($order_by){ $order_by .= ", ";}
                    $order_by .= " NVL(p.MINISTRY_ID,-1)";

                    if($group_by){ $group_by .= ", ";}
                    $group_by .= " p.MINISTRY_ID ";
                    $heading_name .= " $MINISTRY_TITLE";
                break; 
                case "DEPARTMENT" : 
                    if($select_list){ $select_list .= ", ";}
                    $select_list .= " p.DEPARTMENT_ID";	

                    if($select_list1){ $select_list1 .= ", ";}
                    $select_list1 .= " NVL(p.DEPARTMENT_ID,NULL) as DEPARTMENT_ID ";

                    if($order_by){ $order_by .= ", ";}
                    $order_by .= " NVL(p.DEPARTMENT_ID,-1)";

                    if($group_by){ $group_by .= ", ";}
                    $group_by .= " p.DEPARTMENT_ID ";
                    $heading_name .= " $DEPARTMENT_TITLE";

                break; 
                case "ORG" :
                    if($select_list){ $select_list .= ", ";}
                    $select_list .= " p.ORG_ID";	

                    if($select_list1){ $select_list1 .= ", ";}
                    $select_list1 .= " NVL(p.ORG_ID,NULL) as ORG_ID ";

                    if($order_by){ $order_by .= ", ";}
                    $order_by .= " NVL(p.ORG_ID,-1)";

                    if($group_by){ $group_by .= ", ";}
                    $group_by .= " p.ORG_ID ";
                    $heading_name .= " $ORG_TITLE";

                break;
                case "ORG_1" :
                    if($select_list){ $select_list .= ", ";}
                    $select_list .= " p.ORG_ID_1 ";	

                    if($select_list1){ $select_list1 .= ", ";}
                    $select_list1 .= " NVL(p.ORG_ID_1,NULL) as ORG_ID_1 ";

                    if($order_by){ $order_by .= ", ";}
                    $order_by .= " NVL(p.ORG_ID_1,-1)";

                    if($group_by){ $group_by .= ", ";}
                    $group_by .= " p.ORG_ID_1 ";
                    $heading_name .= " $ORG_TITLE1";

                break;
                case "ORG_2" :
                    if($select_list){ $select_list .= ", ";}
                    $select_list .= " p.ORG_ID_2 ";	

                    if($select_list1){ $select_list1 .= ", ";}
                    $select_list1 .= " NVL(p.ORG_ID_2,NULL) as ORG_ID_2 ";

                    if($order_by){ $order_by .= ", ";}
                    $order_by .= " NVL(p.ORG_ID_2,-1)";

                    if($group_by){ $group_by .= ", ";}
                    $group_by .= " p.ORG_ID_2 ";
                    $heading_name .= " $ORG_TITLE2";

                break;
                case "LINE" :
                    if($select_list){ $select_list .= ", ";}
                    $select_list .= " p.PL_CODE ";	

                    if($select_list1){ $select_list1 .= ", ";}
                    $select_list1 .= " NVL(p.PL_CODE,NULL) as PL_CODE ";

                    if($order_by){ $order_by .= ", ";}
                    $order_by .= " NVL(p.PL_CODE,-1)";

                    if($group_by){ $group_by .= ", ";}
                    $group_by .= " p.PL_CODE ";
                    $heading_name .= " $line_title";

                break;
            } // end switch case
	} // end for
	if(!trim($select_list)){ 
            $select_list = " p.MINISTRY_ID,p.DEPARTMENT_ID,p.ORG_ID,p.ORG_ID_1,p.ORG_ID_2,p.PL_CODE ";
	} // end if
        
        if(!trim($select_list1)){ 
            $select_list1 = " NVL(p.MINISTRY_ID,NULL) as MINISTRY_ID, NVL(p.DEPARTMENT_ID,NULL) as DEPARTMENT_ID, NVL(p.ORG_ID,NULL) as ORG_ID,NVL(p.ORG_ID_1,NULL) as ORG_ID_1,NVL(p.ORG_ID_2,NULL) as ORG_ID_2, NVL(p.PL_CODE,NULL) as PL_CODE ";
	} // end if
        
	if(!trim($order_by)){ 
            $order_by = " NVL(p.MINISTRY_ID,-1),NVL(p.DEPARTMENT_ID,-1),NVL(p.ORG_ID,-1),NVL(p.ORG_ID_1,-1),NVL(p.ORG_ID_2,-1),NVL(p.PL_CODE,-1) asc ";
	} // end if
	
        if(!trim($group_by)){ 
            $group_by = " p.MINISTRY_ID,p.DEPARTMENT_ID,p.ORG_ID,p.ORG_ID_1,p.ORG_ID_2,p.PL_CODE ";
	} // end if
        $arr_select = explode(",",$select_list);
        $select_list_case = '';
        if($arr_select){
            $select_list_case .= 'case ';
            for($i=0;$i < count($arr_select)+1;$i++){
                $select_list_case .= 'when ';
                    $con_and = 'AND';
                    $str_null = 'is not null';
                    for($j=0;$j < count($arr_select)+1;$j++){
                        if($i<=$j){
                            $str_null = 'is null';
                        }
                        if(count($arr_select) == $i){
                            $str_null = 'is not null';
                        }
                        if(count($arr_select)-1 == $j){
                            $con_and = '';
                            $str_null = '';
                        }$select_list_case .= $arr_select[$j].' '.$str_null.' '.$con_and.' ';
                    }
                if($i==0){
                    $select_list_case .= "then '".$i."' ";    
                }else{
                    $select_list_case .= "then '".$i."|/".$arr_rpt_order[$i-1]."' ";    
                }
            }
            $select_list_case .= 'end';
        }
        //echo $select_list_case;
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$list_type_text = $ALL_REPORT_TITLE;

	include ("../report/rpt_condition3.php");
	
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$show_budget_year = (($NUMBER_DISPLAY==2)?convert2thaidigit($search_budget_year):$search_budget_year);
	$report_title = "$DEPARTMENT_NAME||อัตราการเข้าออกของ$PERSON_TYPE[$search_per_type]ในปีงบประมาณ $show_budget_year";
	$report_code = "R0306";
	include ("rpt_R003006_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	if ($FLAG_RTF) {
//		$sum_w = array_sum($heading_width);
		$sum_w = 0;
		for($h = 0; $h < count($heading_width); $h++) {
                    if ($arr_column_sel[$h]==1){
                        $sum_w += $heading_width[$h];
                    }        
		}
		for($h = 0; $h < count($heading_width); $h++) {
                    if ($arr_column_sel[$h]==1){
                        $heading_width[$h] = $heading_width[$h] / $sum_w * 100;
                    }
		}

		$fname= "rpt_R003006_rtf.rtf";

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
		$unit="mm";
		$paper_size="A4";
		$lang_code="TH";
		$orientation='L';

		$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
		
		$pdf->Open();
		$pdf->SetMargins(5,5,5);
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont($font,'',14);
		
		$page_start_x = $pdf->x;			$page_start_y = $pdf->y;
	}
        
        $search_conditions = str_replace(" where ", " and ", $search_condition);
        
        //เดิมนับคนจากประวัติ ปรับใหม่หมดตามเคส http://dpis.ocsc.go.th/Service/node/2853
        $cmd = "
                WITH TB_BEGIN_YEAR AS (
                    SELECT PER_ID FROM PER_PERSONAL WHERE PER_STATUS IN (1) 
                        AND (PER_RETIREDATE != '".(($search_budget_year - 543) - 1)."-10-01' 
                        AND PER_RETIREDATE > '".(($search_budget_year - 543) - 1)."-10-01')
                ) ,  
                TB_IN_YEAR AS (
                    SELECT PER_ID from PER_PERSONAL WHERE (PER_STATUS IN (1,2)) 
                        AND (PER_RETIREDATE != '".(($search_budget_year - 543) - 1)."-10-01' 
                        AND PER_RETIREDATE > '".(($search_budget_year - 543) - 1)."-10-01')
                        AND PER_STARTDATE >= '".(($search_budget_year - 543) - 1)."-10-01' 
                        AND PER_STARTDATE < '".($search_budget_year - 543)."-10-01'
                ) ,  
                TB_OUT_YEAR AS (
                    SELECT PER_ID from PER_PERSONAL WHERE PER_STATUS IN (2) 
                        AND PER_POSDATE >= '".(($search_budget_year - 543) - 1)."-10-01' 
                        AND PER_POSDATE < '".($search_budget_year - 543)."-10-01'
                ) ,  
                p AS (
                    SELECT  p.MINISTRY_ID , p.DEPARTMENT_ID,  NVL(p.ORG_ID,0) AS ORG_ID , NVL(p.ORG_ID_1,0) as ORG_ID_1 ,NVL(p.ORG_ID_2,0) as ORG_ID_2 ,  NVL(p.PL_CODE,0) as PL_CODE ,
                            p.CNT_BEGIN_YEAR, p.CNT_IN_YEAR, p.CNT_OUT_YEAR,((NVL(p.CNT_BEGIN_YEAR,0)+NVL(p.CNT_IN_YEAR,0))-NVL(p.CNT_OUT_YEAR,0)) AS CNT_LAST_YEAR ,
                            (NVL(p.CNT_IN_YEAR,0)+NVL(p.CNT_OUT_YEAR,0)) AS CNT_IN_OUT 
                    FROM (
                        select   g.ORG_ID_REF as MINISTRY_ID, g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID , b.ORG_ID_1 ,b.ORG_ID_2 , $line_code as PL_CODE,  a.PER_ID,
                                (SELECT 1 FROM TB_BEGIN_YEAR x WHERE x.PER_ID = a.PER_ID ) AS CNT_BEGIN_YEAR ,
                                (SELECT 1 FROM TB_IN_YEAR x WHERE x.PER_ID = a.PER_ID ) AS CNT_IN_YEAR ,
                                (SELECT 1 FROM TB_OUT_YEAR x WHERE x.PER_ID = a.PER_ID ) AS CNT_OUT_YEAR 
                        FROM    PER_PERSONAL a, 
                                $position_table b, 
                                PER_ORG c, 
                                PER_ORG g 
                        WHERE   $position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=g.ORG_ID(+)
                                $search_conditions
                            /*ORDER BY g.ORG_ID_REF, g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, a.PER_ID*/
                    ) p			 
                )
                SELECT  p.* , 
                        ( $select_list_case ) AS lv
                FROM (
                    SELECT  
                            $select_list1, p.CNT_BEGIN_YEAR , p.CNT_IN_YEAR , p.CNT_OUT_YEAR , p.CNT_LAST_YEAR  , p.CNT_IN_OUT
                    FROM (
                        SELECT
                            SUM(p.CNT_BEGIN_YEAR) AS CNT_BEGIN_YEAR,SUM(p.CNT_IN_YEAR) AS CNT_IN_YEAR,SUM(p.CNT_OUT_YEAR) AS CNT_OUT_YEAR, SUM(p.CNT_LAST_YEAR) AS CNT_LAST_YEAR , SUM(p.CNT_IN_OUT) AS CNT_IN_OUT ,
                            $select_list
                        FROM p
                        GROUP BY  ROLLUP ($group_by)
                    ) p ORDER BY  $order_by ASC
                ) p
        ";
        if($select_org_structure==1) {
            $cmd1 = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
            $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd1);
	}
        //echo "<pre>$cmd";
        $db_dpis->send_cmd($cmd);
        $cnt_data = $db_dpis->num_rows();
        $db_dpis1->send_cmd($cmd);
	$irow=6-$row_sty;
        $AllCountChk = $cnt_data;
        $chk_i=0;
        $space_mis=" ";
        $space_dep=" ";
        $space_org=" ";
        $space_org_1="   ";
        $space_org_2="   ";
        
        $head_text1 = implode(",", $heading_text);
	$head_width1 = implode(",", $heading_width);
	$head_align1 = implode(",", $heading_align);
	$col_function = implode(",", $column_function);
	if ($FLAG_RTF) {
		$RTF->add_header("", 0, false);	// header default
		$RTF->add_footer("", 0, false);		// footer default
			
	//	echo "$head_text1<br>";
		$tab_align = "center";
		$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
	} else {
		$pdf->AutoPageBreak = false; 
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
	}
        if (!$result) {echo "****** error ****** on open table for $table<br>";}
        
        if($cnt_data){
            while($data = $db_dpis1->get_array()){
                $chk_i++;
                if($data[LV] == '0'){
                    $All_begin = ($data[CNT_BEGIN_YEAR])?$data[CNT_BEGIN_YEAR]:'-';
                    $All_in = ($data[CNT_IN_YEAR])?$data[CNT_IN_YEAR]:'-';
                    $All_out = ($data[CNT_OUT_YEAR])?$data[CNT_OUT_YEAR]:'-';
                    $All_last = ($data[CNT_LAST_YEAR])?$data[CNT_LAST_YEAR]:'-';
                    $Alltotalcount = ($data[CNT_IN_OUT])?$data[CNT_IN_OUT]:'-';
                }else{
                    $MINISTRY_ID = $data[MINISTRY_ID];
                    $DEPARTMENT_ID = $data[DEPARTMENT_ID];
                    $ORG_ID = $data[ORG_ID];
                    $ORG_ID_1 = $data[ORG_ID_1];
                    $ORG_ID_2 = $data[ORG_ID_2];
                    $LINE_ID = $data[PL_CODE];
                    $LEVEL_ARR = explode('|/',$data[LV]);
                    $NAME_H = '';

                    if($LEVEL_ARR[1]=='MINISTRY' || $LEVEL_ARR[1]=='DEPARTMENT' || $LEVEL_ARR[1]=='ORG' || $LEVEL_ARR[1]=='ORG_1' || $LEVEL_ARR[1]=='ORG_2'){
                        if($LEVEL_ARR[1]=='MINISTRY'){
                            $chk_NAME = 'กระทรวง';
                            $id_getname=$MINISTRY_ID;
                        }else if($LEVEL_ARR[1]=='DEPARTMENT'){
                            $chk_NAME = 'กรม';
                            $id_getname=$DEPARTMENT_ID;
                        }else if($LEVEL_ARR[1]=='ORG'){
                            $chk_NAME = 'สำนัก/กอง';
                            $id_getname=$ORG_ID;
                        }else if($LEVEL_ARR[1]=='ORG_1'){
                            $chk_NAME = 'ต่ำกว่า สำนัก/กอง 1 ระดับ';
                            $id_getname=$ORG_ID_1;
                        }else if($LEVEL_ARR[1]=='ORG_2'){
                            $chk_NAME = 'ต่ำกว่า สำนัก/กอง 2 ระดับ';
                            $id_getname=$ORG_ID_2;
                        }
                        $NAME_H = get_name_org($id_getname,$chk_NAME,$select_org_structure,$LEVEL_ARR[1]);
                    }else if($LEVEL_ARR[1]=='LINE'){
                        $NAME_H = get_name_line($LINE_ID,'สายงาน');
                    }

                    $col_main =  str_repeat(" ", $LEVEL_ARR[0]*3).$NAME_H;
                    $count_begin = ($data[CNT_BEGIN_YEAR])?$data[CNT_BEGIN_YEAR]:'-'; //คอน์ลั่ม ต้นปี วิธีคิดคือ จำนวนคนที่มีสถานะปกติ และยังไม่เกษียณอายุในวันที่ 1 ตค 2561
                    $count_in = ($data[CNT_IN_YEAR])?$data[CNT_IN_YEAR]:'-';  //คอน์ลั่ม เข้า  วิธีคิดคือ จำนวนคนที่มีสถานะปกติ/พ้นจากส่วนราชการ และยังไม่เกษียณอายุ ที่มีวันเข้าส่วนราชการ ตั้งแต่วันที่ 1 ตค 61 ถึง 30 กย 62
                    $count_out = ($data[CNT_OUT_YEAR])?$data[CNT_OUT_YEAR]:'-'; //คอน์ลั่ม ออก วิธีคิดคือ จำนวนคนที่มีสถานะพ้นจากส่วนราชการซึ่งมีผลระหว่างวันที่ 1 ตค 61 - 30 กย 62
                    $count_last = ($data[CNT_LAST_YEAR])?$data[CNT_LAST_YEAR]:'-'; //คอน์ลั่ม สิ้นปี คือ ต้นปี + เข้า - ออก
                    $count_all = ($data[CNT_IN_OUT])?$data[CNT_IN_OUT]:'-'; 
                    if($Alltotalcount != 0 && $Alltotalcount != '-'){
                        if($count_in != '-'){
                            $per_in = number_format((100*$count_in)/$Alltotalcount,2); //คอน์ลั่ม ร้อยละเข้า วิธีคิดคือ (100*เข้า/(เข้า+ออก))     //$count_all == เข้า+ออก
                        } else {
                            $per_in = 0;
                        }
                        if($count_out != '-'){
                            $per_out = number_format((100*$count_out)/$Alltotalcount,2); //คอน์ลั่ม ร้อยละออก  วิธีคิดคือ (100*ออก/(เข้า+ออก)) //$count_all == เข้า+ออก
                        } else {
                            $per_out = 0;
                        }
                    }else{
                        $per_in = 0;
                        $per_out = 0;
                    }
                    $arr_data = (array) null;
                    $arr_data[] = $col_main;
                    $arr_data[] = $count_begin;
                    $arr_data[] = $count_in;
                    $arr_data[] = $count_out;
                    $arr_data[] = $count_last;
                    $arr_data[] = $per_in;
                    $arr_data[] = $per_out;
                    //var_dump($arr_data);
                    if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1) {
                        if ($FLAG_RTF){
                            $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
                        }else{
                            $result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "b", "000000", "");		//TRHBL
                        }
                    } else {
                        if ($FLAG_RTF){
                            $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
                        }else{
                            $result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
                        }    
                    }
                    if (!$result){ echo "****** error ****** add data to table at record count = $data_count <br>";}
                }
            }
            if($AllCountChk==$chk_i){
                $col_main =  'รวม';
                $All_begin = $All_begin;
                $All_in = $All_in;
                $All_out = $All_out;
                $All_last = $All_last;
                if($Alltotalcount != 0  && $Alltotalcount != '-'){
                    $Allper_in = number_format((100*$All_in)/$Alltotalcount,2); //คอน์ลั่ม ร้อยละเข้า วิธีคิดคือ (100*เข้า/(เข้า+ออก))     //$count_all == เข้า+ออก
                    $Allper_out = number_format((100*$All_out)/$Alltotalcount,2); //คอน์ลั่ม ร้อยละออก  วิธีคิดคือ (100*ออก/(เข้า+ออก)) //$count_all == เข้า+ออก
                }else{
                    $Allper_in = 0;
                    $Allper_out = 0;
                }
                $arr_data = (array) null;
		$arr_data[] = $col_main;
		$arr_data[] = $All_begin;
		$arr_data[] = $All_in;
		$arr_data[] = $All_out;
		$arr_data[] = $All_last;
		$arr_data[] = $Allper_in;
		$arr_data[] = $Allper_out;

		if ($FLAG_RTF){
                    $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
                }else{
                    $result = $pdf->add_data_tab($arr_data, 7, "TRHLB", $data_align, "", "14", "b", "000000", "");		//TRHBL
                }        
                if (!$result){ echo "****** error ****** add data to table at record count = $data_count <br>";}
            }
        } else{
            if ($FLAG_RTF){
                $result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
            }else{
                $result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
            }    
            if (!$result){ echo "****** error ****** add text line to table at record count = $data_count <br>";}
	}
        
        if ($FLAG_RTF) {
            $RTF->close_tab(); 
            $RTF->display($fname);
	} else {
            $pdf->close_tab(""); 
            $pdf->close();
            $pdf->Output();	
	}