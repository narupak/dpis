<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R005005_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	//ini_set("max_execution_time", $max_execution_time);
        ini_set("max_execution_time",0);
        ini_set("memory_limit","9999M");
	if($MFA_FLAG == 1) {
	$DATE_DISPLAY=2;
	}
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=f.PL_CODE";
		$pl_code = "b.PL_CODE";
		$pl_name = "f.PL_NAME";
		$type_code ="b.PT_CODE";
		$select_type_code =",b.PT_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=f.PN_CODE";
		$pl_code = "b.PN_CODE";
		$pl_name = "f.PN_NAME";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=f.EP_CODE";
		$pl_code = "b.EP_CODE";
		$pl_name = "f.EP_NAME";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=f.TP_CODE";
		$pl_code = "b.TP_CODE";
		$pl_name = "f.TP_NAME";
	} // end if	
	
	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG"); 
//	$search_per_type = 1;

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	$list_type_text = $ALL_REPORT_TITLE;

	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
	}elseif($MINISTRY_ID){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
		if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		$list_type_text .= " - $MINISTRY_NAME";
	}elseif($PROVINCE_CODE){
		$PROVINCE_CODE = trim($PROVINCE_CODE);
		$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
	if(trim($search_org_id)){ 
		if($select_org_structure==0){
			$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
		}else if($select_org_structure==1){
			$arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		}
		$list_type_text = "$search_org_name";
	} // end if
        if(trim($search_year)){ 
            $arr_search_condition[] = "MONTHS_BETWEEN(TO_DATE($search_year-543||'-07-28','yyyy-mm-dd'),TO_DATE(PER_STARTDATE,'yyyy-mm-dd'))/12 >=25";
	} 
	if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);

//	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	if($MFA_FLAG == 1)
	$report_title = "ประวัติสำหรับเสนอขอพระราชทานเหรียญจักรพรรดิมาลา/เหรียญจักรมาลา";
	else
	$report_title = "$DEPARTMENT_NAME||ประวัติสำหรับเสนอขอพระราชทานเหรียญจักรพรรดิมาลา";
	
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0505";
	$report_code = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_code):$report_code);
	$token = md5(uniqid(rand(), true)); 
        
        $dir_folder = file_exists("../../Excel/tmp/R0505");
        if($dir_folder==0){
            mkdir("../../Excel/tmp/R0505"); 
        }

        $files = glob('../../Excel/tmp/R0505/*'); // get all file names
        foreach($files as $file){ // iterate files
          if(is_file($file))
            unlink($file); // delete file
        }
        
	$fname= "../../Excel/tmp/R0505/dpis_".$token."_1.xls";
        $arr_filename[] =$fname;
	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน
		if($MFA_FLAG==1){
			$ws_head_line1 = array("วันเดือนปี","ตำแหน่ง","กรม หรือ กระทรวง","อายุ","เงินเดือน","หมายเหตุ");
		}else{
			$ws_head_line1 = array("วันเดือนปี","ตำแหน่ง","หน่วยงาน","อายุ","เงินเดือน","หมายเหตุ");
		}
		$ws_head_line2 = array("ที่รับราชการ","","","","","");
		$ws_colmerge_line1 = array(0,0,0,0,0,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TLR","TLR","TLR","TLR","TLR");
		$ws_border_line2 = array("RBL","RBL","RBL","RBL","RBL","RBL");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C");
		$ws_width = array(10,30,45,10,10,60);
	// จบการกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน	

	// คำนวนเปลียบเทียบค่า $ws_width ใหม่ เทียบกับ $heading_width
//		echo "bf..ws_width=".implode(",",$ws_width)."<br>";
		$sum_hdw = 0;
		$sum_wsw = 0;
		for($h = 0; $h < count($heading_width); $h++) {
			$sum_wsw += $ws_width[$h];	// ws_width ยังไม่ได้ บวก ความกว้าง ตัวที่ถูกตัดเข้าไป
			if ($arr_column_sel[$h]==1) {
				$sum_hdw += $heading_width[$h];
			}
		}
		// บัญญัติไตรยางค์   ยอดรวมความกว้าง column ใน heading_width เทียบกับ ยอดรวมใน ws_width
		//                                แต่ละ column ใน ws_width[$h] = sum(ws_width) /sum(heading_width) * heading_width[$h]
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1) {
				$ws_width[$h] = $sum_wsw / $sum_hdw * $heading_width[$h];
			}
		}
//		echo "af..ws_width=".implode(",",$ws_width)."<br>";
	// จบการเทียบค่าคำนวนเปลียบเทียบค่า $ws_width ใหม่ เทียบกับ $heading_width
	
	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width;
		global $ws_head_line1, $ws_head_line2, $ws_colmerge_line1, $ws_colmerge_line2;
		global $ws_border_line1, $ws_border_line2, $ws_fontfmt_line1, $ws_fontfmt_line2;
		global $ws_headalign_line1, $ws_headalign_line2, $ws_width;

		// loop กำหนดความกว้างของ column
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->set_column($colseq, $colseq, $ws_width[$arr_column_map[$i]]);
//				echo "$i-map[".$arr_column_map[$i]."], width=".$ws_width[$arr_column_map[$i]]."<br />";
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 1
		$colseq_1=0;
		$xlsRow++;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->write($xlsRow, $colseq_1, $ws_head_line1[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]]));
				$colseq_1++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 2
		$xlsRow++;
		$colseq_2=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) { 	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->write($xlsRow, $colseq_2, $ws_head_line2[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]]));
				$colseq_2++;
			}
		}
	} // function		
	
	
	if ($BKK_FLAG==1) $DC_CODE_COND = "17";
	else $DC_CODE_COND = "61";
        
        $cmd = "    WITH tbmain as  (
                        SELECT  a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, a.LEVEL_NO, a.DEPARTMENT_ID, b.ORG_ID, c.ORG_NAME, $pl_name as PL_CODE, f.PL_NAME as PL_NAME $select_type_code ,SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE 
                        from    PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, $line_table f 
                        WHERE   $position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+) and $line_join(+)  AND a.PER_ID NOT IN (SELECT PER_ID FROM per_decoratehis where dc_code = '61') 
                                $search_condition
                    ),tb_his as (
                        select 'POH' as typ,tbmain.PER_ID, c.LEVEL_SEQ_NO as cur_level, tbmain.PN_NAME||tbmain.PER_NAME||' '||tbmain.PER_SURNAME as name, tbmain.PL_NAME||c.POSITION_LEVEL AS POS_NAME,tbmain.ORG_NAME,tbmain.DEPARTMENT_ID,
                                tbmain.PER_BIRTHDATE, tbmain.PER_STARTDATE ,SUBSTR(a.POH_EFFECTIVEDATE, 1, 10) as POH_EFFECTIVEDATE, a.POH_PL_NAME AS POH_POSNAME, a.POH_ORG as ORG_NAME_HIS,
                                a.POH_SALARY as SALARY_HIS, b.MOV_NAME,d.POSITION_LEVEL, a.PL_CODE,  a.PN_CODE, a.EP_CODE, a.TP_CODE ,a.PT_CODE ,a.POH_LEVEL_NO, a.LEVEL_NO as LEVEL_NO_HIS
                        from	PER_POSITIONHIS a, PER_MOVMENT b , tbmain , PER_LEVEL c , PER_LEVEL d 
                        where	a.PER_ID=tbmain.PER_ID and a.MOV_CODE=b.MOV_CODE AND tbmain.LEVEL_NO = c.LEVEL_NO AND  a.POH_LEVEL_NO = d.LEVEL_NO(+)
                        UNION ALL 
                        select 'SAH' as typ,tbmain.PER_ID,c.LEVEL_SEQ_NO as cur_level, tbmain.PN_NAME||tbmain.PER_NAME||' '||tbmain.PER_SURNAME as name,tbmain.PL_NAME||c.POSITION_LEVEL AS POS_NAME,tbmain.ORG_NAME,tbmain.DEPARTMENT_ID,
                                tbmain.PER_BIRTHDATE, tbmain.PER_STARTDATE ,SUBSTR(a.SAH_EFFECTIVEDATE, 1, 10) as POH_EFFECTIVEDATE, a.SAH_POSITION AS POH_POSNAME,a.SAH_ORG as ORG_NAME_HIS,a.SAH_SALARY as SALARY_HIS, 
                                b.MOV_NAME,d.POSITION_LEVEL, NULL as PL_CODE , NULL as PN_CODE , NULL as EP_CODE , NULL as TP_CODE , NULL as PT_CODE , NULL as POH_LEVEL_NO , NULL as LEVEL_NO_HIS
                      from	PER_SALARYHIS a, PER_MOVMENT b  , tbmain, PER_LEVEL c , PER_LEVEL d 
                      where	a.PER_ID=tbmain.PER_ID and a.MOV_CODE=b.MOV_CODE AND tbmain.LEVEL_NO = c.LEVEL_NO AND a.LEVEL_NO = d.LEVEL_NO(+) 
                    ) 
                    SELECT * FROM (
                        SELECT t.cur_level||t.per_id||t.POH_EFFECTIVEDATE||typ||SALARY_HIS xx,t.* FROM tb_his t  --WHERE  PER_ID = 14323
                    )
                    order by xx 
                ";
             
        if($select_org_structure==1) { 
                $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
                $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
        }
        $count_data = $db_dpis->send_cmd($cmd);
        $db_dpis1->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "$cmd<br>";
        if($count_data){
            $colshow_cnt=0;		// หาจำนวน column ที่แสดงจริง
            for($i=0; $i<count($arr_column_sel); $i++){
                    if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
            }

            $xlsRow = 0;
            /**$temp_report_title = "$REF_NAME||$NAME||$report_title";
            $arr_title = explode("||", $temp_report_title);**/
            $arr_title = explode("||", $report_title);
            for($i=0; $i<count($arr_title); $i++){
                    $worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
                    for($j=0; $j < $colshow_cnt-1; $j++) 
                            $worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
                    $xlsRow++;
            } 
            $name = "";
            $POSITION_HIS_Before ="";
            $ORG_HIS_Before ="";
            $data_count = 0;
           /* $COUNT_ROW_DATA=0;
            while($db_dpis1->get_array()){
                $COUNT_ROW_DATA++;
            }*/
            $idx_count = 1;
            while($data = $db_dpis->get_array()){  
                //$PER_ID = $data[PER_ID];
                $PER_NAME = $data[NAME];
                $PER_POS_NAME = $data[POS_NAME];
                //ชื่อสำนัก / กอง
                $ORG_NAME = $data[ORG_NAME];
                //ชื่อกรม
                $DEPARTMENT_ID = $data[DEPARTMENT_ID];
                $cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID";
                if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                $db_dpis2->send_cmd($cmd);
                $data2 = $db_dpis2->get_array();
                $DEPARTMENT_NAME = $data2[ORG_NAME];
                //ชื่อกระทรวง
                $REF_ORG_ID_REF = $data2[ORG_ID_REF];
                $cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$REF_ORG_ID_REF ";
                if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                $db_dpis2->send_cmd($cmd);
                $data2 = $db_dpis2->get_array();
                $MINISTRY_NAME = $data2[ORG_NAME];
                
                $PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
		if($PER_BIRTHDATE){
			$arr_temp = explode("-", $PER_BIRTHDATE);
			$PER_BIRTHDATE_D = $arr_temp[2] + 0;
			$PER_BIRTHDATE_M = $month_full[($arr_temp[1] + 0)][TH];
			$PER_BIRTHDATE_Y = $arr_temp[0] + 543;
		} // end if
                
                $PER_STARTDATE = trim($data[PER_STARTDATE]);
                $COMPLETE_CONDDATE = date_adjust($PER_STARTDATE, "year", 25);
		$COMPLETE_CONDDATE = date_adjust($COMPLETE_CONDDATE,'d',-1);
		if($COMPLETE_CONDDATE){
			$arr_temp = explode("-", $COMPLETE_CONDDATE);
			$COMPLETE_CONDDATE_D = $arr_temp[2] + 0;
			$COMPLETE_CONDDATE_M = $month_full[($arr_temp[1] + 0)][TH];
			$COMPLETE_CONDDATE_Y = $arr_temp[0] + 543;
		} // end if
                
                $EFFECTIVEDATE = show_date_format($data[POH_EFFECTIVEDATE], 2);
                
                
                $POH_LEVEL_NO = trim($data[POH_LEVEL_NO]);
		if (!$POH_LEVEL_NO) $POH_LEVEL_NO = trim($data[LEVEL_NO_HIS]);
                if($search_per_type == 1) $POH_PL_CODE = trim($data[PL_CODE]);
                elseif($search_per_type == 2) $POH_PL_CODE = trim($data[PN_CODE]);
                elseif($search_per_type == 3) $POH_PL_CODE = trim($data[EP_CODE]);
                elseif($search_per_type == 4) $POH_PL_CODE = trim($data[TP_CODE]);
                $POSITION_LEVEL = $data[POSITION_LEVEL];
                $POH_PL_NAME = $data[POH_POSNAME];
                if (!$POH_PL_NAME) {
                        $cmd = " select $pl_name as PL_NAME from $line_table b where trim($pl_code)='$POH_PL_CODE' ";
                        $db_dpis2->send_cmd($cmd);
                        //echo "<pre>";
                        //die($cmd);
                        $data2 = $db_dpis2->get_array();
                        $POH_PL_NAME = trim($data2[PL_NAME]);
                }

                if (strpos($POH_PL_NAME,$POSITION_LEVEL) !== false){
                        $POSITION_HIS = (trim($POH_PL_NAME))? "$POH_PL_NAME" : "";
                        //echo "if = [POH_PL_NAME] =".$POH_PL_NAME.".[POSITION_LEVEL]=".$POSITION_LEVEL."<br>";
                }else{
                        $POSITION_HIS = (trim($POH_PL_NAME))? "$POH_PL_NAME". $POSITION_LEVEL : "";
                        //echo "else = [POH_PL_NAME] =".$POH_PL_NAME.".[POSITION_LEVEL]=".$POSITION_LEVEL."<br>";
                } 
                if(trim($type_code)){
                        $POH_PT_CODE = trim($data[PT_CODE]);
                        $cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$POH_PT_CODE' ";
                        $db_dpis2->send_cmd($cmd);
                        $data2 = $db_dpis2->get_array();
                        $POH_PT_NAME = trim($data2[PT_NAME]);
                        $POSITION_HIS .= (($POH_PT_NAME != "ทั่วไป" && $POH_LEVEL_NO >= 6)?"$POH_PT_NAME":"");
                }
                if(!$POSITION_HIS){
                    $POSITION_HIS = $POSITION_HIS_Before;
                }         
                $ORG_NAME_HIS = $data[ORG_NAME_HIS];
                 if(!$ORG_NAME_HIS){
                    $ORG_NAME_HIS = $ORG_HIS_Before;
                }
                $AGE = floor(date_difference($data[POH_EFFECTIVEDATE], $data[PER_BIRTHDATE], "year"));
                $SALARY = $data[SALARY_HIS];
                $NOTE = $data[MOV_NAME];
               
                
             
                
                if($PER_NAME!=$name){
                    $idx_count++;
                    if($name!=""){
                         $xlsRow++;
                        $worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                        $worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                        $worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                        $worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                        $worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                        $worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                        $xlsRow++;
                        $worksheet->write_string($xlsRow, 4, ("ลงชื่อ  ". str_repeat(".", 70)), set_format("xlsFmtTableDetail", "", "C", "", 1));
                        $xlsRow++;
                        $worksheet->write_string($xlsRow, 4, "$name", set_format("xlsFmtTableDetail", "", "C", "", 1)); 
                        $xlsRow++;
                        $worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                        $worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                        $worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                        $worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                        $worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                        $worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                        
                        if($idx_count%6==0){
                            $workbook->close();
                            if($idx_count>=12){
                                break;
                            }
                            unset($workbook);
                            if(empty($active_sheet)){$active_sheet=1;}
                            echo "สำเร็จ file DPIS_R0505_PART_".$active_sheet.".xls :". date('Y-m-d H:i').'<br>';
                            flush();
                            ob_flush();
                            sleep(1);
                            include ("rpt_R005005_format.php");
                            //$idx_count=1;

                            $active_sheet++;
                            $token = md5(uniqid(rand(), true)); 
                            $xlsRow = 0;
                            $fname= "../../Excel/tmp/R0505/dpis_".$token."_".$active_sheet.".xls";
                            $arr_filename[] =$fname;

                            $workbook = new writeexcel_workbook($fname);
                            $worksheet = &$workbook->addworksheet("R0505");
                            $worksheet->set_margin_right(0.50);
                            $worksheet->set_margin_bottom(1.10);


                            $colshow_cnt=0;		// หาจำนวน column ที่แสดงจริง
                            for($i=0; $i<count($arr_column_sel); $i++){
                                    if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
                            }

                            $arr_title = explode("||", $report_title);
                            for($i=0; $i<count($arr_title); $i++){
                                    $worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
                                    for($j=0; $j < $colshow_cnt-1; $j++) 
                                            $worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
                                    $xlsRow++;
                            } 

                            $name = "";
                            $POSITION_HIS_Before ="";
                            $ORG_HIS_Before ="";
                            $data_count = 0;
                        }
                    }
                    
                    $xlsRow++;
                    $worksheet->write_string($xlsRow, 0, "ชื่อ  $PER_NAME", set_format("xlsFmtTableDetail", "", "L", "", 0));
                    $worksheet->write_string($xlsRow, 3, (($NUMBER_DISPLAY==2)?convert2thaidigit("ตำแหน่ง  $PER_POS_NAME"):"ตำแหน่ง  $PER_POS_NAME"), set_format("xlsFmtTableDetail", "", "L", "", 0));
                    $xlsRow++;
                    $worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit("$ORG_TITLE  $ORG_NAME"):"$ORG_TITLE  $ORG_NAME"), set_format("xlsFmtTableDetail", "", "L", "", 0));
                    $worksheet->write_string($xlsRow, 3, (($NUMBER_DISPLAY==2)?convert2thaidigit("$DEPARTMENT_TITLE  $DEPARTMENT_NAME"):"$DEPARTMENT_TITLE  $DEPARTMENT_NAME"), set_format("xlsFmtTableDetail", "", "L", "", 0));
                    $xlsRow++;
                    $worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit("$MINISTRY_TITLE  $MINISTRY_NAME"):"$MINISTRY_TITLE  $MINISTRY_NAME"), set_format("xlsFmtTableDetail", "", "L", "", 0));
                    $worksheet->write_string($xlsRow, 3, (($NUMBER_DISPLAY==2)?convert2thaidigit("เกิดวันที่  $PER_BIRTHDATE_D  เดือน  $PER_BIRTHDATE_M  พ.ศ.  $PER_BIRTHDATE_Y"):"เกิดวันที่  $PER_BIRTHDATE_D  เดือน  $PER_BIRTHDATE_M  พ.ศ.  $PER_BIRTHDATE_Y"), set_format("xlsFmtTableDetail", "", "L", "", 0));
                    $xlsRow++;
                    $worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit("รับราชการมาครบ 25 ปี  เมื่อวันที่  $COMPLETE_CONDDATE_D  เดือน  $COMPLETE_CONDDATE_M  พ.ศ.  $COMPLETE_CONDDATE_Y"):"รับราชการมาครบ 25 ปี  เมื่อวันที่  $COMPLETE_CONDDATE_D  เดือน  $COMPLETE_CONDDATE_M  พ.ศ.  $COMPLETE_CONDDATE_Y"), set_format("xlsFmtTableDetail", "", "L", "", 0));
                    $xlsRow++;
                    $worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                    $worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                    $worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                    $worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                    $worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                    $worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                    $idx_count++;
                    print_header();
                    $name = $PER_NAME;
                    
                }   
                $POSITION_HIS_Before = $POSITION_HIS;
                $ORG_HIS_Before = $ORG_NAME_HIS;
                $xlsRow++;
                $worksheet->write_string($xlsRow, 0, "$EFFECTIVEDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
                $worksheet->write_string($xlsRow, 1, "$POSITION_HIS", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write_string($xlsRow, 2, "$ORG_NAME_HIS", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write_string($xlsRow, 3, "$AGE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
                $worksheet->write_string($xlsRow, 4, "$SALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                $worksheet->write_string($xlsRow, 5, "$NOTE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $data_count++;
                             
               /* 
                if($COUNT_ROW_DATA==$data_count){
                    $xlsRow++;
                    $worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                    $worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                    $worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                    $worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                    $worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                    $worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                    $xlsRow++;
                    $worksheet->write_string($xlsRow, 4, ("ลงชื่อ  ". str_repeat(".", 70)), set_format("xlsFmtTableDetail", "", "C", "", 1));
                    $xlsRow++;
                    $worksheet->write_string($xlsRow, 4, "$name", set_format("xlsFmtTableDetail", "", "C", "", 1)); 
                    $xlsRow++;
                    $worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                    $worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                    $worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                    $worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                    $worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                    $worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "C", "", 1));
                }*/  
                
            }  
            //die();
        }else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	//ini_set("max_execution_time", 0);
	
        /*ปิดการดาวน์โหลดอัตโนมัติ*/
	/*  header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
            header("Content-Type: application/x-msexcel; name=\"$report_code.xls\"");
            header("Content-Disposition: inline; filename=\"$report_code.xls\"");
            $fh=fopen($fname, "rb");
            fpassthru($fh);
            fclose($fh);
            unlink($fname);  
        */
        /*เพิ่มเติม ให้โปรแกรมดาวน์โหลดไฟล์ไว้บนเซริฟก่อน*/     
        $cnt = count($arr_filename);
        for($idx=0;$idx<=$cnt;$idx++){
            if(!empty($arr_filename[$idx])){
                echo "<a href='".$arr_filename[$idx]."'>".$arr_filename[$idx]." คลิกเพื่อ Download</a><br>";
            }
        } 
?>