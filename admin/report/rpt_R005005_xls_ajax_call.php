<?
        /*for($i=0;$i<5;$i++){
             echo '.';
             
             ob_flush(); 
             flush();
             sleep(1);
        }*/
       
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
        
        function connect_oci8_test ($host, $database, $user, $password, $port){
                if (!$port) $port = 1521;
                if ($database=="misusers") $port = 1522;
                
                $databases = "(DESCRIPTION =
                                    (ADDRESS =
                                        (PROTOCOL = TCP)
                                        (HOST = $host)
                                        (PORT = $port)
                                    )
                              (CONNECT_DATA = (SERVICE_NAME = $database))
                             )";

                $link_con = OCIPLogon ($user, $password, $databases);
                return $link_con;
                
        }
        
        function num_row($link_con,$sql_queryx){
            $sql_query = $sql_queryx;
            $result = OCIParse($link_con, $sql_query);
            $ERROR = OCIExecute($result, OCI_COMMIT_ON_SUCCESS);
            $columns = OCINumCols($result);
            $numrows = 0;
            while (OCIFetchInto($result, $row, OCI_ASSOC)) {
                    $numrows++;
            }
            oci_free_statement($sql_queryx);
            oci_close($link_con);
            return $numrows;
            
        }
        /* หา Date Differrence */
        $days_per_year = 365.2425;
	$days_per_month = 30.436875;
	$seconds_per_day = 86400;				// 60 * 60 * 24
        function calculate_secx($date_d, $date_m, $date_y){
            global $days_per_year, $days_per_month, $seconds_per_day;

            $time_sec = 0;
            $time_sec += ($date_y * $days_per_year * $seconds_per_day);
            $time_sec += ($date_m * $days_per_month * $seconds_per_day);
            $time_sec += ($date_d * $seconds_per_day);
            return $time_sec;
	} 
        
        function date_differencex($input_date1, $input_date2, $diff_type){
		
            global $days_per_year, $days_per_month, $seconds_per_day;
            /*Release 5.2.1.6*/

            if($input_date2 > $input_date1){/*เดิม*/
                    $temp = $input_date1;
                    $input_date1 = $input_date2;
                    $input_date2 = $temp;
                   
            }
            list($year1, $month1, $date1) = split("-", $input_date1, 3);
            list($year2, $month2, $date2) = split("-", $input_date2, 3);

            $date_diff = calculate_secx($date1, $month1, $year1) - calculate_secx($date2, $month2, $year2);
            switch(strtolower($diff_type)){
                    case "year" :
                    $result = $date_diff / ($seconds_per_day * $days_per_year);
                    break;
            }  
            return $result;
	} 
         /* End หา Date Differrence */
        
        include("../../php_scripts/db_connect_var.php");
        /*  $host = '164.115.146.226';
        *    $database ='ES';
        *    $user = 'coj091161';
        *   $password= 'coj091161';
        *    $port = '1521';
        *
        *
        *   $sql_query = 'select * from per_line ';
        *   $x = '';
        *   $conn = connect_oci8_test($host, $database, $user, $password, $port);
        *   $conn2 = connect_oci8_test($host, $database, $user, $password, $port);
        *
        *
        *    $count_data =  num_row($conn,$sql_query);
        *    $stid = oci_parse($conn2, $sql_query);
        *    oci_execute($stid);
        *    while (($row = oci_fetch_array($stid, OCI_BOTH)) != false) {
        *        echo $row[0] . " und " . $row[PL_NAME]   . " count_data = ".$count_data."<br>\n";
        *    }
        *    oci_free_statement($stid);
        *    oci_close($conn2);
        *    die();
        */
       
        if($_POST['RPTORD_LIST']){ $search_per_type=$_POST['RPTORD_LIST']; }
        if($_POST[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_POST[NUMBER_DISPLAY];
        if($_POST['CH_PRINT_FONT']){ $CH_PRINT_FONT=$_POST['CH_PRINT_FONT']; }
        if($_POST['search_per_type']){ $search_per_type=$_POST['search_per_type']; }
        if($_POST['search_per_status']){ $search_per_status=$_POST['search_per_status']; }
        if($_POST['select_org_structure']){ $select_org_structure = $_POST['select_org_structure']; }
        if($_POST['search_year']){ $search_year = $_POST['search_year']; }
        if($_POST['list_type']){ $list_type=$_POST['list_type']; }
        if($_POST['MINISTRY_NAME']){ $MINISTRY_NAME = $_POST['MINISTRY_NAME']; }
        if($_POST['MINISTRY_ID']){ $MINISTRY_ID = $_POST['MINISTRY_ID']; }
        if($_POST['DEPARTMENT_NAME']){ $DEPARTMENT_NAME=$_POST['DEPARTMENT_NAME']; }
        if($_POST['DEPARTMENT_ID']){ $DEPARTMENT_ID=$_POST['DEPARTMENT_ID']; }
        if($_POST['search_org_name']){ $search_org_name=$_POST['search_org_name']; }
        if($_POST['search_org_id']){ $search_org_id=$_POST['search_org_id']; }
        
        
        ini_set("max_execution_time",0);
        set_time_limit(0);
        ini_set("memory_limit","512M");
        //ini_set("session.gc_maxlifetime",60*60*24);
        
	//include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
	include ("../report/rpt_function.php");

	//include ("rpt_R005005_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น
        
        function getToFont($id){
            if($id==1){
                    $fullname	= 'Angsana';
            }else if($id==2){
                    $fullname	= 'Cordia';
            }else if($id==3){
                    $fullname	= 'TH SarabunPSK';
            }else{
                    $fullname	= 'Browallia';
            }
            return $fullname;
        }
        
        require_once '../../Excel/eslip/Classes/PHPExcel.php';
        
        $dir_folder = file_exists("../../Excel/tmp/R0505");
        if($dir_folder==0){
            mkdir("../../Excel/tmp/R0505"); 
        }

        $files = glob('../../Excel/tmp/R0505/rpt_R005005_xls.xlsx'); // get all file names
        foreach($files as $file){ // iterate files
          if(is_file($file))
            unlink($file); // delete file
        }

   $logfile = '../../Excel/tmp/R0505/XLOG.txt';
    if (1==1) {
        $fp = fopen($logfile, 'a');
        fwrite($fp, "Begin!!!!\r\n");
        fclose($fp);                    
    }
        
        if($MFA_FLAG == 1) {
	$DATE_DISPLAY=2;
	}
        
        $db_dpis  = connect_oci8_test($db_host, $db_name, $db_user, $db_pwd, $port);
        $db_dpis1 = connect_oci8_test($db_host, $db_name, $db_user, $db_pwd, $port);
        $db_dpis2 = connect_oci8_test($db_host, $db_name, $db_user, $db_pwd, $port);
        $db_dpis3 = connect_oci8_test($db_host, $db_name, $db_user, $db_pwd, $port);
        $db_dpis4 = connect_oci8_test($db_host, $db_name, $db_user, $db_pwd, $port);
        $db_dpis5 = connect_oci8_test($db_host, $db_name, $db_user, $db_pwd, $port);
        $db_dpis6 = connect_oci8_test($db_host, $db_name, $db_user, $db_pwd, $port);
        
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
                $exQuery = oci_parse($db_dpis, $cmd);
                oci_execute($exQuery);
		while (($row = oci_fetch_array($stid, OCI_BOTH)) != false) $arr_org_ref[] = $data[ORG_ID];
                oci_close($db_dpis);
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
            $search_year = $search_year-543;
            $arr_search_condition[] = "MONTHS_BETWEEN(TO_DATE($search_year||'-07-28','yyyy-mm-dd'),TO_DATE(PER_STARTDATE,'yyyy-mm-dd'))/12 >=25";
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
	
        
        
        
        $ws_head_line1 = array("วันเดือนปี","ตำแหน่ง","หน่วยงาน","อายุ","เงินเดือน","หมายเหตุ");
        $ws_head_line2 = array("ที่รับราชการ","","","","","");
        $ws_width = array(10,30,45,10,10,60);
        // new 
        $object = new PHPExcel();
        /*
            $object->setActiveSheetIndex(0);
            $object->getActiveSheet()->getDefaultRowDimension()->setRowHeight(23);
            $object->getActiveSheet()->getColumnDimension('C')->setAutoSize(TRUE);
            $object->getActiveSheet()->getColumnDimension('D')->setAutoSize(TRUE);
            $object->getActiveSheet()->getColumnDimension('C')->setAutoSize(TRUE);
            $object->getActiveSheet()->getColumnDimension('E')->setAutoSize(TRUE);
            $object->getActiveSheet()->getColumnDimension('F')->setAutoSize(TRUE);
            
            $column = 1;
                for($excel_row=2;$excel_row<=10;$excel_row++){

                    $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, "1");
                    $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, "ม.ค.");
                    $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, "15000");
                    $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, "100");
                    $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, "-");
                    $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, "500");

                     $excel_row++;
                }     
        */    
            //$object->getActiveSheet()->getStyle('F5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //ตัวอย่างการเซตสี
            //$object->getActiveSheet()->getStyle('B5:M5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('C6E0B4');
           //$object->getActiveSheet()->getStyle('A3')->getFont()->setBold(TRUE)->getColor()->setRGB($set_font_color_content);
        $cmd = "    WITH tbmain as  (
                            SELECT  a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, a.LEVEL_NO, a.DEPARTMENT_ID, b.ORG_ID, c.ORG_NAME, $pl_code as PL_CODE, $pl_name as PL_NAME $select_type_code ,SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE 
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
                            SELECT t.cur_level||t.per_id||t.POH_EFFECTIVEDATE||typ||SALARY_HIS xx,t.* FROM tb_his t --WHERE  rownum<= 500
                        )
                        order by xx 
                    ";
                    // echo "<pre>";
                    //die($cmd);
        if($select_org_structure==1) { 
                $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
                $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
        }
        /*echo '.';
        ob_flush(); 
        flush();*/
        $count_data = num_row($db_dpis1,$cmd);
        //$count_data = 1;
        
        $font_name = getToFont($CH_PRINT_FONT);
        $styleArray_head1 = array(
            'font'  => array(
                'bold'  => TRUE,
                'size'  => '16',
                'name'  => $font_name
        ));
        $styleArray_head = array(
            'font'  => array(
                'bold'  => FALSE,
                'size'  => '14',
                'name'  => $font_name
        ));
        $styleArray_body = array(
            'font'  => array(
                'bold'  => FALSE,
                'size'  => '14',
                'name'  => $font_name
         ));
        //set border header
        $styleArray=array(
        'borders'=>array(
            'allborders'=>array(
                'style'=>PHPExcel_Style_Border::BORDER_THIN
                    )
                )
        );
        
        if($count_data){
                $xlsRow++;
                $object->getActiveSheet()->getStyle('A1:F2')->applyFromArray($styleArray_head1);
                $object->setActiveSheetIndex(0)->mergeCells('A1:F1')->setCellValue('A1', iconv('TIS-620','UTF-8','สำนักงานศาลยุติธรรม'))->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $object->setActiveSheetIndex(0)->mergeCells('A2:F2')->setCellValue('A2', iconv('TIS-620','UTF-8','ประวัติสำหรับเสนอขอพระราชทานเหรียญจักรพรรดิมาลา'))->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                
                $name = "";
                $POSITION_HIS_Before ="";
                $ORG_HIS_Before ="";
                $data_count = 0;
             
                $idx_count = 0;
                $count_tab = 1;
                $xlsRow = 3;
                //$logfile = 'XLOG-' . date('YmdHis')  . '.txt';    
                    if (1==1) {
                        $fp = fopen($logfile, 'a');
                        fwrite($fp, "Start!\r\n");

                        fclose($fp);                    
                    }
                //$person = 0;                
               
                $exQuery1 = oci_parse($db_dpis2, $cmd);
                oci_execute($exQuery1);
                
                while (($data = oci_fetch_array($exQuery1, OCI_BOTH)) != false){  
                    
                    //if($data_count%10==0){
                        echo ".";
                        ob_flush(); 
                        flush();
                    //}
                    
                    if (1==1) {
                        $person++;                
                        $fp = fopen($logfile, 'a');

                        fwrite($fp, 'Data:' . $data_count . '   Excel row:' . $xlsRow . "\r\n");
                        fclose($fp);                    
                    }
                
                    //$PER_ID = $data[PER_ID];
                    $PER_NAME = $data[NAME];
                    $PER_POS_NAME = $data[POS_NAME];
                    //ชื่อสำนัก / กอง
                    $ORG_NAME = $data[ORG_NAME];
                    //ชื่อกรม
                    $DEPARTMENT_ID = $data[DEPARTMENT_ID];
                    $cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID";
                    if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                    $exQuery2 = oci_parse($db_dpis3, $cmd);
                    oci_execute($exQuery2);
                    $data2 = oci_fetch_array($exQuery2, OCI_BOTH);
                    $DEPARTMENT_NAME = $data2[ORG_NAME];
                    //ชื่อกระทรวง
                    $REF_ORG_ID_REF = $data2[ORG_ID_REF];
                    $cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$REF_ORG_ID_REF ";
                    if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                    
                    $exQuery3 = oci_parse($db_dpis4, $cmd);
                    oci_execute($exQuery3);
                    $data3 = oci_fetch_array($exQuery3, OCI_BOTH);
                    
                    $MINISTRY_NAME = $data3[ORG_NAME];

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
                            $exQuery4 = oci_parse($db_dpis5, $cmd);
                            oci_execute($exQuery4);
                            $data4 = oci_fetch_array($exQuery4, OCI_BOTH);
                            $POH_PL_NAME = trim($data4[PL_NAME]);
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
                            
                            $exQuery5 = oci_parse($db_dpis6, $cmd);
                            oci_execute($exQuery5);
                            $data5 = oci_fetch_array($exQuery5, OCI_BOTH);
                            
                            $POH_PT_NAME = trim($data5[PT_NAME]);
                            $POSITION_HIS .= (($POH_PT_NAME != "ทั่วไป" && $POH_LEVEL_NO >= 6)?"$POH_PT_NAME":"");
                    }
                    if(!$POSITION_HIS){
                        $POSITION_HIS = $POSITION_HIS_Before;
                    }         
                    $ORG_NAME_HIS = $data[ORG_NAME_HIS];
                     if(!$ORG_NAME_HIS){
                        $ORG_NAME_HIS = $ORG_HIS_Before;
                    }
                    
                    $AGE = floor(date_differencex($data[POH_EFFECTIVEDATE], $data[PER_BIRTHDATE], "year"));
                    $SALARY = $data[SALARY_HIS];
                    $NOTE = $data[MOV_NAME];
                    
                    if($PER_NAME!=$name){
                        $idx_count++;		
                        if($name!=""){
                              
                            
                            $xlsRow++;
                            $object->setActiveSheetIndex(0)->mergeCells('A'.$xlsRow.':F'.$xlsRow);
                            $xlsRow++;
                            $object->getActiveSheet()->getStyle('A'.$xlsRow.':F'.$xlsRow)->applyFromArray($styleArray_body);
                            $object->getActiveSheet()->mergeCells('D'.$xlsRow.':F'.$xlsRow)->setCellValue('D'.$xlsRow, iconv('TIS-620','UTF-8',("ลงชื่อ  ". str_repeat(".", 70))))->getStyle('D'.$xlsRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $xlsRow++;
                            $object->getActiveSheet()->getStyle('A'.$xlsRow.':F'.$xlsRow)->applyFromArray($styleArray_body);
                            $object->getActiveSheet()->mergeCells('D'.$xlsRow.':F'.$xlsRow)->setCellValue('D'.$xlsRow, iconv('TIS-620','UTF-8',"$name"))->getStyle('D'.$xlsRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $xlsRow++;
                            $object->setActiveSheetIndex(0)->mergeCells('A'.$xlsRow.':F'.$xlsRow);
                            /*if($idx_count>=1){
                                break;
                            }*/
                               
                        }
                        $xlsRow++;
                        $object->getActiveSheet()->getStyle('A'.$xlsRow.':F'.$xlsRow)->applyFromArray($styleArray_body);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(0, $xlsRow, iconv('TIS-620','UTF-8',"ชื่อ  $PER_NAME"));
                        $object->getActiveSheet()->setCellValueByColumnAndRow(3, $xlsRow, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit("ตำแหน่ง  $PER_POS_NAME"):"ตำแหน่ง  $PER_POS_NAME")));
                        $xlsRow++;
                        $object->getActiveSheet()->getStyle('A'.$xlsRow.':F'.$xlsRow)->applyFromArray($styleArray_body);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(0, $xlsRow, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit("$ORG_TITLE  $ORG_NAME"):"$ORG_TITLE  $ORG_NAME")));
                        $object->getActiveSheet()->setCellValueByColumnAndRow(3, $xlsRow, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit("$DEPARTMENT_TITLE  $DEPARTMENT_NAME"):"$DEPARTMENT_TITLE  $DEPARTMENT_NAME")));
                        $xlsRow++;
                        $object->getActiveSheet()->getStyle('A'.$xlsRow.':F'.$xlsRow)->applyFromArray($styleArray_body);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(0, $xlsRow, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit("$MINISTRY_TITLE  $MINISTRY_NAME"):"$MINISTRY_TITLE  $MINISTRY_NAME")));
                        $object->getActiveSheet()->setCellValueByColumnAndRow(3, $xlsRow, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit("เกิดวันที่  $PER_BIRTHDATE_D  เดือน  $PER_BIRTHDATE_M  พ.ศ.  $PER_BIRTHDATE_Y"):"เกิดวันที่  $PER_BIRTHDATE_D  เดือน  $PER_BIRTHDATE_M  พ.ศ.  $PER_BIRTHDATE_Y")));
                        $xlsRow++;
                        $object->getActiveSheet()->getStyle('A'.$xlsRow.':F'.$xlsRow)->applyFromArray($styleArray_body);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(0, $xlsRow, iconv('TIS-620','UTF-8',(($NUMBER_DISPLAY==2)?convert2thaidigit("รับราชการมาครบ 25 ปี  เมื่อวันที่  $COMPLETE_CONDDATE_D  เดือน  $COMPLETE_CONDDATE_M  พ.ศ.  $COMPLETE_CONDDATE_Y"):"รับราชการมาครบ 25 ปี  เมื่อวันที่  $COMPLETE_CONDDATE_D  เดือน  $COMPLETE_CONDDATE_M  พ.ศ.  $COMPLETE_CONDDATE_Y")));
                        $xlsRow++;
                       
                        $xlsRow++;
                        $object->getActiveSheet()->getStyle('A'.$xlsRow.':F'.$xlsRow)->applyFromArray($styleArray_head);
                        //set bg header color
                        $object->getActiveSheet()->getStyle('A'.$xlsRow.':F'.$xlsRow)->getFill()
                                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                    ->getStartColor()->setARGB('99cdfe');
                        
                        $object->getActiveSheet()->getStyle('A'.$xlsRow.':F'.$xlsRow)->applyFromArray($styleArray);
                        
                        $object->getActiveSheet()->getColumnDimension('A')->setWidth('25')->setAutoSize(FALSE);
                        $object->getActiveSheet()->getColumnDimension('B')->setWidth('30')->setAutoSize(FALSE);
                        $object->getActiveSheet()->getColumnDimension('C')->setWidth('60')->setAutoSize(FALSE);
                        $object->getActiveSheet()->getColumnDimension('D')->setWidth('10')->setAutoSize(FALSE);
                        $object->getActiveSheet()->getColumnDimension('E')->setWidth('10')->setAutoSize(FALSE);
                        $object->getActiveSheet()->getColumnDimension('F')->setWidth('60')->setAutoSize(FALSE);
                        $object->getActiveSheet()->getRowDimension($xlsRow)->setRowHeight(37.5);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(0, $xlsRow, iconv('TIS-620','UTF-8',"วันเดือนปี\nที่รับราชการ"));
                        $object->getActiveSheet()->getStyle('A'.$xlsRow)->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(1, $xlsRow, iconv('TIS-620','UTF-8',"ตำแหน่ง"));
                        $object->getActiveSheet()->getStyle('B'.$xlsRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(2, $xlsRow, iconv('TIS-620','UTF-8',"หน่วยงาน"));
                        $object->getActiveSheet()->getStyle('C'.$xlsRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(3, $xlsRow, iconv('TIS-620','UTF-8',"อายุ"));
                        $object->getActiveSheet()->getStyle('D'.$xlsRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(4, $xlsRow, iconv('TIS-620','UTF-8',"เงินเดือน"));
                        $object->getActiveSheet()->getStyle('E'.$xlsRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(5, $xlsRow, iconv('TIS-620','UTF-8',"หมายเหตุ"));
                        $object->getActiveSheet()->getStyle('F'.$xlsRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                         
                       
                        //print_header($worksheet);
                        $name = $PER_NAME;

                    }   
                    $POSITION_HIS_Before = $POSITION_HIS;
                    $ORG_HIS_Before = $ORG_NAME_HIS;
                    
                    
                    
                    $xlsRow++;
                    $object->getActiveSheet()->getStyle('A'.$xlsRow.':F'.$xlsRow)->applyFromArray($styleArray_body);
                    $object->getActiveSheet()->getStyle('A'.$xlsRow.':F'.$xlsRow)->applyFromArray($styleArray);
                    $object->getActiveSheet()->getRowDimension($xlsRow)->setRowHeight(18.75);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(0, $xlsRow, iconv('TIS-620','UTF-8',"$EFFECTIVEDATE"));
                    $object->getActiveSheet()->getStyle('A'.$xlsRow)->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(1, $xlsRow, iconv('TIS-620','UTF-8',"$POSITION_HIS"));
                    $object->getActiveSheet()->getStyle('B'.$xlsRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(2, $xlsRow, iconv('TIS-620','UTF-8',"$ORG_NAME_HIS"));
                    $object->getActiveSheet()->getStyle('C'.$xlsRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(3, $xlsRow, iconv('TIS-620','UTF-8',"$AGE"));
                    $object->getActiveSheet()->getStyle('D'.$xlsRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(4, $xlsRow, iconv('TIS-620','UTF-8',"$SALARY"));
                    $object->getActiveSheet()->getStyle('E'.$xlsRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(5, $xlsRow, iconv('TIS-620','UTF-8',"$NOTE"));
                    $object->getActiveSheet()->getStyle('F'.$xlsRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                   
                    $data_count++;

                }
               
                $xlsRow++;
                $object->setActiveSheetIndex(0)->mergeCells('A'.$xlsRow.':F'.$xlsRow);
                $xlsRow++;
                $object->getActiveSheet()->getStyle('A'.$xlsRow.':F'.$xlsRow)->applyFromArray($styleArray_body);
                $object->getActiveSheet()->mergeCells('D'.$xlsRow.':F'.$xlsRow)->setCellValue('D'.$xlsRow, iconv('TIS-620','UTF-8',("ลงชื่อ  ". str_repeat(".", 70))))->getStyle('D'.$xlsRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $xlsRow++;
                $object->getActiveSheet()->getStyle('A'.$xlsRow.':F'.$xlsRow)->applyFromArray($styleArray_body);
                $object->getActiveSheet()->mergeCells('D'.$xlsRow.':F'.$xlsRow)->setCellValue('D'.$xlsRow, iconv('TIS-620','UTF-8',"$name"))->getStyle('D'.$xlsRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $xlsRow++;
                //die();
            
        }else{
            //ไม่มีข้อมูล
            $xlsRow++;
            $object->getActiveSheet()->getStyle('A1:F2')->applyFromArray($styleArray_head);
            $object->setActiveSheetIndex(0)->mergeCells('A1:F1')->setCellValue('A1', iconv('TIS-620','UTF-8','**** ไม่พบข้อมูล ****'))->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //$object->setActiveSheetIndex(0)->mergeCells('A2:F2')->setCellValue('A2', iconv('TIS-620','UTF-8','ประวัติสำหรับเสนอขอพระราชทานเหรียญจักรพรรดิมาลา'))->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }

        if (1==1) {
            $person++;                
            $fp = fopen($logfile, 'a');

            fwrite($fp, "End\r\n");
            fclose($fp);                    
        }
        //ob_clean();
       /* $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="R0505.xlsx"');
        $object_writer->save('php://output');*/
        
        //$objPHPExcel->setActiveSheetIndex(0);
        
        $file_name = "../../Excel/tmp/R0505/rpt_R005005_xls.xlsx";
        $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        $objWriter->save($file_name);
        unlink($logfile);
        
        /*if(file_exists($file_name )){
            
            echo "<a href='".$file_name."'>R0505.xlsx คลิกเพื่อ Download</a><br>";
        }*/
        
?>
   