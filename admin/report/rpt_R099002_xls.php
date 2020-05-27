<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
        include ("../report/rpt_function.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$order_by = (isset($order_by))?  $order_by : 1;
  	if($order_by==1){
            //Release 5.2.1.25 
            if(in_array('1', $search_kf_cycle) && in_array('2', $search_kf_cycle) && $search_per_type==3){
                $order_str = "NVL(c1.PER_NAME,c2.PER_NAME), NVL(c1.PER_SURNAME,c2.PER_SURNAME)";
            }else{
                $order_str = "a.KF_END_DATE, a.KF_CYCLE, PER_NAME, PER_SURNAME";
            }    
        }elseif($order_by==2) {
		if($DPISDB=="odbc") {
			if ($search_per_type==0 || $search_per_type==1) $order_str = "a.KF_END_DATE, a.KF_CYCLE, CLng(POS_NO)";
			elseif ($search_per_type==0 || $search_per_type==2) $order_str = "a.KF_END_DATE, a.KF_CYCLE, CLng(POEM_NO)";
			elseif ($search_per_type==0 || $search_per_type==3) $order_str = "a.KF_END_DATE, a.KF_CYCLE, CLng(POEMS_NO)";
			elseif ($search_per_type==0 || $search_per_type==4) $order_str = "a.KF_END_DATE, a.KF_CYCLE, CLng(POT_NO)";
		}elseif($DPISDB=="oci8"){
                    if(in_array('1', $search_kf_cycle) && in_array('2', $search_kf_cycle) && $search_per_type==3){
                        if ($search_per_type==0 || $search_per_type==1) $order_str =  "to_number(replace(NVL(c1.POS_NO,c2.POS_NO2),'-',''))";
		 	elseif ($search_per_type==0 || $search_per_type==2) $order_str =  "to_number(replace(NVL(c1.POEM_NO,c2.POEM_NO2),'-',''))";
		 	elseif ($search_per_type==0 || $search_per_type==3) $order_str =  "to_number(replace(NVL(c1.POEMS_NO,c2.POEMS_NO2),'-',''))";
		 	elseif ($search_per_type==0 || $search_per_type==4) $order_str =  "to_number(replace(NVL(c1.POT_NO,c2.POT_NO2),'-',''))";
                    }else{
                        if ($search_per_type==0 || $search_per_type==1) $order_str = "a.KF_END_DATE, a.KF_CYCLE, to_number(replace(POS_NO,'-',''))";
		 	elseif ($search_per_type==0 || $search_per_type==2) $order_str = "a.KF_END_DATE, a.KF_CYCLE, to_number(replace(POEM_NO,'-',''))";
		 	elseif ($search_per_type==0 || $search_per_type==3) $order_str = "a.KF_END_DATE, a.KF_CYCLE, to_number(replace(POEMS_NO,'-',''))";
		 	elseif ($search_per_type==0 || $search_per_type==4) $order_str = "a.KF_END_DATE, a.KF_CYCLE, to_number(replace(POT_NO,'-',''))";
                    }
		}elseif($DPISDB=="mysql"){ 
			if ($search_per_type==0 || $search_per_type==1) $order_str = "a.KF_END_DATE, a.KF_CYCLE, POS_NO+0";
			elseif ($search_per_type==0 || $search_per_type==2) $order_str = "a.KF_END_DATE, a.KF_CYCLE, POEM_NO+0";
			elseif ($search_per_type==0 || $search_per_type==3) $order_str = "a.KF_END_DATE, a.KF_CYCLE, POEMS_NO+0";
			elseif ($search_per_type==0 || $search_per_type==4) $order_str = "a.KF_END_DATE, a.KF_CYCLE, POT_NO+0";
		}
  	} elseif($order_by==3){ 
            if(in_array('1', $search_kf_cycle) && in_array('2', $search_kf_cycle) && $search_per_type==3){
                $order_str = "NVL(c1.LEVEL_SEQ_NO,c2.LEVEL_SEQ_NO2) DESC, NVL(c1.PER_NAME,c2.PER_NAME), NVL(c1.PER_SURNAME,c2.PER_SURNAME)";        
            }else{
                $order_str = "a.KF_END_DATE, a.KF_CYCLE, f.LEVEL_SEQ_NO DESC, PER_NAME, PER_SURNAME";
            }
        }elseif($order_by==4) {
            if(in_array('1', $search_kf_cycle) && in_array('2', $search_kf_cycle) && $search_per_type==3){
                if ($search_per_type==0 || $search_per_type==1) $order_str = "NVL(c1.ORG_ID_POS,c2.ORG_ID_POS2), NVL(c1.PER_NAME,c2.PER_NAME), NVL(c1.PER_SURNAME,c2.PER_SURNAME)";
		elseif ($search_per_type==0 || $search_per_type==2) $order_str = "NVL(c1.ORG_ID_POSEMP,c2.ORG_ID_POSEMP2), NVL(c1.PER_NAME,c2.PER_NAME), NVL(c1.PER_SURNAME,c2.PER_SURNAME)";
		elseif ($search_per_type==0 || $search_per_type==3) $order_str = "NVL(c1.ORG_ID_POSEMPS,c2.ORG_ID_POSEMPS2), NVL(c1.PER_NAME,c2.PER_NAME), NVL(c1.PER_SURNAME,c2.PER_SURNAME)";
		elseif ($search_per_type==0 || $search_per_type==4) $order_str = "NVL(c1.ORG_ID_POSTEMP,c2.ORG_ID_POSTEMP2), NVL(c1.PER_NAME,c2.PER_NAME), NVL(c1.PER_SURNAME,c2.PER_SURNAME)";   
            }else{
                if ($search_per_type==0 || $search_per_type==1) $order_str = "a.KF_END_DATE, a.KF_CYCLE, c.ORG_ID, PER_NAME, PER_SURNAME";
		elseif ($search_per_type==0 || $search_per_type==2) $order_str = "a.KF_END_DATE, a.KF_CYCLE, d.ORG_ID, PER_NAME, PER_SURNAME";
		elseif ($search_per_type==0 || $search_per_type==3) $order_str = "a.KF_END_DATE, a.KF_CYCLE, e.ORG_ID, PER_NAME, PER_SURNAME";
		elseif ($search_per_type==0 || $search_per_type==4) $order_str = "a.KF_END_DATE, a.KF_CYCLE, g.ORG_ID, PER_NAME, PER_SURNAME";
            }
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
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
	}elseif($search_ministry_id){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
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
        if(in_array('1', $search_kf_cycle) && in_array('2', $search_kf_cycle) && $search_per_type==3){
        }else{$arr_search_condition[] = "(a.KF_CYCLE in (". implode(",", $search_kf_cycle) ."))";}    
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
		$cmd = "	select a.KF_ID, a.KF_END_DATE, a.KF_CYCLE, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, a.SUM_KPI, a.SUM_COMPETENCE, a.SUM_OTHER, a.PER_ID, a.PER_ID_REVIEW, a.PER_ID_REVIEW1, a.PER_ID_REVIEW2, CHIEF_PER_ID, FRIEND_FLAG, TOTAL_SCORE, a.ORG_ID_KPI, a.ORG_ID, b.LEVEL_NO, b.PER_TYPE, b.POS_ID, b.POEM_ID, b.POEMS_ID,b.POT_ID
							from 	(	
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
							order by 	$order_str ";
	}elseif($DPISDB=="oci8"){
                
	    $min_rownum = (($current_page - 1) * $data_per_page) + 1;
            $max_rownum = $current_page * $data_per_page;
            if(in_array('1', $search_kf_cycle) && in_array('2', $search_kf_cycle) && $search_per_type==3){          //Release 5.2.1.25     
		$cmd = " WITH  tb_kpi_c1 as ( 
                            select  a.KF_ID, a.KF_END_DATE, a.KF_CYCLE, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, a.SUM_KPI, a.SUM_COMPETENCE, a.SUM_OTHER, a.PER_ID, a.PER_ID_REVIEW, 
                                    a.PER_ID_REVIEW1, a.PER_ID_REVIEW2, CHIEF_PER_ID, FRIEND_FLAG, TOTAL_SCORE, a.ORG_ID_KPI, a.ORG_ID, b.LEVEL_NO, b.PER_TYPE, b.POS_ID, 
                                    b.POEM_ID, b.POEMS_ID,b.POT_ID,f.LEVEL_SEQ_NO, c.POS_NO, d.POEM_NO, e.POEMS_NO,g.POT_NO,c.ORG_ID AS ORG_ID_POS,d.ORG_ID as ORG_ID_POSEMP,
                                    e.ORG_ID as ORG_ID_POSEMPS,g.ORG_ID  as ORG_ID_POSTEMP
                            from    PER_KPI_FORM a, PER_PERSONAL b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, PER_LEVEL f ,PER_POS_TEMP g
                            where   a.PER_ID=b.PER_ID 
                                    and b.POS_ID=c.POS_ID(+) 
                                    and b.POEM_ID=d.POEM_ID(+) 
                                    and b.POEMS_ID=e.POEMS_ID(+) 
                                    and b.POT_ID=g.POT_ID(+) 
                                    and b.LEVEL_NO=f.LEVEL_NO(+)
                                    and (a.KF_CYCLE in (1))
                                    $search_condition 
                           ),tb_kpi_c2 as ( 
                            select   a.PER_ID as PER_ID2,a.KF_END_DATE AS KF_END_DATE2,a.KF_ID as KF_ID2,a.KF_CYCLE as KF_CYCLE2, b.PN_CODE, b.PER_NAME , b.PER_SURNAME ,a.ORG_ID as ORG_ID2,a.SUM_KPI as SUM_KPI2, 
                            a.SUM_COMPETENCE as SUM_COMPETENCE2, a.SUM_OTHER as SUM_OTHER2, a.TOTAL_SCORE as TOTAL_SCORE2, b.LEVEL_NO as LEVEL_NO2, b.PER_TYPE as PER_TYPE2, b.POS_ID as POS_ID2, 
                            b.POEM_ID as POEM_ID2, b.POEMS_ID as POEMS_ID2,b.POT_ID as POT_ID2,f.LEVEL_SEQ_NO as LEVEL_SEQ_NO2, c.POS_NO as POS_NO2, d.POEM_NO as POEM_NO2, e.POEMS_NO as POEMS_NO2,g.POT_NO as POT_NO2,
                            c.ORG_ID AS ORG_ID_POS2,d.ORG_ID as ORG_ID_POSEMP2,e.ORG_ID as ORG_ID_POSEMPS2,g.ORG_ID  as ORG_ID_POSTEMP2
                            from  PER_KPI_FORM a, PER_PERSONAL b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, PER_LEVEL f ,PER_POS_TEMP g
                            where   a.PER_ID=b.PER_ID 
                                    and b.POS_ID=c.POS_ID(+) 
                                    and b.POEM_ID=d.POEM_ID(+) 
                                    and b.POEMS_ID=e.POEMS_ID(+) 
                                    and b.POT_ID=g.POT_ID(+) 
                                    and b.LEVEL_NO=f.LEVEL_NO(+)
                                    and (a.KF_CYCLE in (2))
                                    $search_condition
                            )
                            select NVL(c1.PER_ID,c2.PER_ID2) as PER_ID,NVL(c1.PN_CODE,c2.PN_CODE) as PN_CODE ,NVL(c1.PER_NAME,c2.PER_NAME) as PER_NAME, NVL(c1.PER_SURNAME,c2.PER_SURNAME) as PER_SURNAME , 
                                  NVL(c1.KF_END_DATE,c2.KF_END_DATE2) as KF_END_DATE,c1.KF_ID,c1.KF_CYCLE,c1.ORG_ID,c1.SUM_KPI,c1.SUM_COMPETENCE,c1.SUM_OTHER,c1.TOTAL_SCORE,c2.PER_ID2,c2.KF_ID2,c2.KF_CYCLE2,c2.ORG_ID2,c2.SUM_KPI2,
                                  c2.SUM_COMPETENCE2,c2.SUM_OTHER2,c2.TOTAL_SCORE2, NVL(c1.PER_TYPE,c2.PER_TYPE2) as PER_TYPE,NVL(c1.POS_ID,c2.POS_ID2) as POS_ID,NVL(c1.POEM_ID,c2.POEM_ID2) as POEM_ID,
                                  NVL(c1.POEMS_ID,c2.POEMS_ID2) as POEMS_ID,NVL(c1.POT_ID,c2.POT_ID2) as POT_ID , NVL(c1.LEVEL_NO,c2.LEVEL_NO2) as LEVEL_NO
                            from tb_kpi_c1 c1  FULL JOIN tb_kpi_c2 c2 on (c1.PER_ID=c2.PER_ID2) 
                            ORDER BY $order_str";
            }else{
                $cmd = "select		a.KF_ID, a.KF_END_DATE, a.KF_CYCLE, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, a.SUM_KPI, a.SUM_COMPETENCE, a.SUM_OTHER, a.PER_ID,  CHIEF_PER_ID, FRIEND_FLAG, TOTAL_SCORE, a.ORG_ID_KPI, a.ORG_ID, b.LEVEL_NO, b.PER_TYPE, b.POS_ID, b.POEM_ID, b.POEMS_ID,b.POT_ID
								from		PER_KPI_FORM a, PER_PERSONAL b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, PER_LEVEL f ,PER_POS_TEMP g
								where		a.PER_ID=b.PER_ID and b.POS_ID=c.POS_ID(+) and b.POEM_ID=d.POEM_ID(+) and b.POEMS_ID=e.POEMS_ID(+) and b.POT_ID=g.POT_ID(+) and b.LEVEL_NO=f.LEVEL_NO(+)
												$search_condition
								order by 	$order_str ";
            }                                                    
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		a.KF_ID, a.KF_END_DATE, a.KF_CYCLE, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, a.SUM_KPI, a.SUM_COMPETENCE, a.SUM_OTHER, a.PER_ID, a.PER_ID_REVIEW, a.PER_ID_REVIEW1, a.PER_ID_REVIEW2, CHIEF_PER_ID, FRIEND_FLAG, TOTAL_SCORE, a.ORG_ID_KPI, a.ORG_ID, b.LEVEL_NO, b.PER_TYPE, b.POS_ID, b.POEM_ID, b.POEMS_ID,b.POT_ID
							from 	(	
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

	//echo $cmd;<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
        //echo "<pre>";
        //die($cmd);
	$report_title = "รายงานตัวชิ้วัดรายบุคคล";

	if ($SESS_PROVINCE_NAME!=""){
		if ($SESS_PROVINCE_NAME=="กรุงเทพมหานคร") $report_title .= "|" . $SESS_PROVINCE_NAME;
		else $report_title .= "|จังหวัด" . $SESS_PROVINCE_NAME;
	}
	if ($SESS_USERGROUP_LEVEL <= 3) $report_title .= "|" . $MINISTRY_NAME;
	$report_title .= "|" . $DEPARTMENT_NAME;

        //Release 5.2.1.25 
        if(in_array('1', $search_kf_cycle) && in_array('2', $search_kf_cycle) && $search_per_type==3){
            $report_title .= "|" . "ปีงบประมาณ ". $search_kf_year;
        }
	$report_code = "R9902";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow,$CTRL_TYPE,$SESS_USERGROUP_LEVEL,$search_department_name,$search_ministry_name){ 
		global $worksheet, $ORG_TITLE, $DEPARTMENT_TITLE, $MINISTRY_TITLE;
                global $search_kf_cycle,$search_per_type; //Release 5.2.1.25 
		
               //============================================== เลือกแบบทั้ง 2 รอบการประเมิน =================================================
                if(in_array('1', $search_kf_cycle) && in_array('2', $search_kf_cycle) && $search_per_type==3){          //Release 5.2.1.25 
                    //============================================== บรรทัดที่ 1 ====================================================
                    $worksheet->set_column(0, 0, 35);
                    $worksheet->write($xlsRow, 0, "ชื่อผู้รับการประเมิน", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                    if($CTRL_TYPE <= 3 && $SESS_USERGROUP_LEVEL <= 3 && !($search_department_name) ){
                            if(!($search_ministry_name) ){
                                    $worksheet->set_column(1, 1, 10);     //เพิ่มใหม่ .21
                                    $worksheet->set_column(2, 2, 40);
                                    $worksheet->set_column(3, 3, 40);
                                    $worksheet->set_column(4, 4, 50); 
                                    $worksheet->set_column(5, 5, 50); 
                                    $worksheet->set_column(6, 6, 10);
                                    $worksheet->set_column(7, 7, 10);
                                    $worksheet->set_column(8, 8, 10);
                                    $worksheet->set_column(9, 9, 12);
                                    $worksheet->set_column(10, 10, 40);
                                    $worksheet->set_column(11, 11, 12);
                                    $worksheet->set_column(12, 12, 12);
                                    $worksheet->set_column(13, 13, 12);
                                    $worksheet->set_column(14, 14, 12);
                                    $worksheet->set_column(15, 15, 12);
                                    $worksheet->write($xlsRow, 1, "เลขที่ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));  //เพิ่มใหม่ .21
                                    $worksheet->write($xlsRow, 2, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow, 3, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow, 4, "$DEPARTMENT_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow, 5, "$MINISTRY_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
                                    $worksheet->write($xlsRow, 7, "ครั้งที่ 1", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
                                    $worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
                                    $worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
                                    
                                    $worksheet->write($xlsRow, 10, "$ORG_TITLE รอบ2", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
                                    $worksheet->write($xlsRow, 12, "ครั้งที่ 2", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
                                    $worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
                                    $worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
                                    $worksheet->write($xlsRow, 15, "เฉลี่ย", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            }else{
                                    $worksheet->set_column(1, 1, 10);     //เพิ่มใหม่ .21
                                    $worksheet->set_column(2, 2, 40);
                                    $worksheet->set_column(3, 3, 40);
                                    $worksheet->set_column(4, 4, 50); 
                                    $worksheet->set_column(5, 5, 50); 
                                    $worksheet->set_column(6, 6, 10);
                                    $worksheet->set_column(7, 7, 10);
                                    $worksheet->set_column(8, 8, 10);
                                    $worksheet->set_column(9, 9, 40);
                                    $worksheet->set_column(10, 10, 12);
                                    $worksheet->set_column(11, 11, 12);
                                    $worksheet->set_column(12, 12, 12);
                                    $worksheet->set_column(13, 13, 12);
                                    $worksheet->set_column(14, 14, 12);
                                    $worksheet->write($xlsRow, 1, "เลขที่ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));  //เพิ่มใหม่ .21
                                    $worksheet->write($xlsRow, 2, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow, 3, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow, 4, "$DEPARTMENT_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
                                    $worksheet->write($xlsRow, 6, "ครั้งที่ 1", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
                                    $worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
                                    $worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
                                    
                                    $worksheet->write($xlsRow, 9, "$ORG_TITLE รอบ2", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
                                    $worksheet->write($xlsRow, 11, "ครั้งที่ 2", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
                                    $worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
                                    $worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
                                    $worksheet->write($xlsRow, 14, "เฉลี่ย", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            }
                    }else{
                            $worksheet->set_column(1, 1, 10);      //เพิ่มใหม่ .21
                            $worksheet->set_column(2, 2, 40);
                            $worksheet->set_column(3, 3, 40);
                            $worksheet->set_column(4, 4, 10);
                            $worksheet->set_column(5, 5, 10);
                            $worksheet->set_column(6, 6, 10);
                            $worksheet->set_column(7, 7, 12);
                            $worksheet->set_column(8, 8, 40);
                            $worksheet->set_column(9, 9, 12);
                            $worksheet->set_column(10, 10, 12);
                            $worksheet->set_column(11,11, 12);
                            $worksheet->set_column(12,12, 12);
                            $worksheet->set_column(13,13, 12);
                            $worksheet->write($xlsRow, 1, "เลขที่ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));  //เพิ่มใหม่ .21
                            $worksheet->write($xlsRow, 2, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            $worksheet->write($xlsRow, 3, "$ORG_TITLE รอบ1", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            $worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
                            $worksheet->write($xlsRow, 5, "ครั้งที่ 1", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
                            $worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
                            $worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
                            
                            $worksheet->write($xlsRow, 8, "$ORG_TITLE รอบ2", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            $worksheet->write($xlsRow, 9, "ครั้งที่ 2", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
                            $worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
                            $worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
                            $worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
                            $worksheet->write($xlsRow, 13, "เฉลี่ย", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                    }
                    //==============================================END บรรทัดที่ 1 =================================================
                    //============================================== บรรทัดที่ 2 ====================================================
                    $worksheet->set_column(0, 0, 35);
                    $worksheet->write($xlsRow+1, 0, " ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                    if($CTRL_TYPE <= 3 && $SESS_USERGROUP_LEVEL <= 3 && !($search_department_name) ){
                            if(!($search_ministry_name) ){
                                    $worksheet->set_column(1, 1, 10);     //เพิ่มใหม่ .21
                                    $worksheet->set_column(2, 2, 40);
                                    $worksheet->set_column(3, 3, 40);
                                    $worksheet->set_column(4, 4, 50); 
                                    $worksheet->set_column(5, 5, 50); 
                                    $worksheet->set_column(6, 6, 10);
                                    $worksheet->set_column(7, 7, 10);
                                    $worksheet->set_column(8, 8, 10);
                                    $worksheet->set_column(9, 9, 12);
                                    $worksheet->set_column(10, 10, 40);
                                    $worksheet->set_column(11, 11, 12);
                                    $worksheet->set_column(12, 12, 12);
                                    $worksheet->set_column(13, 13, 12);
                                    $worksheet->set_column(14, 14, 12);
                                    $worksheet->set_column(15, 15, 12);
                                    $worksheet->write($xlsRow+1, 1, " ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));  //เพิ่มใหม่ .21
                                    $worksheet->write($xlsRow+1, 2, " ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 3, " ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 4, " ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 5, " ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 6, "ผลสัมฤทธิ์", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 7, "สมรรถนะ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 8, "อื่น ๆ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 9, "ผลการประเมิน", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 10, " ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 11, "ผลสัมฤทธิ์", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 12, "สมรรถนะ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 13, "อื่น ๆ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 14, "ผลการประเมิน", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 15, "", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            }else{
                                    $worksheet->set_column(1, 1, 10);     //เพิ่มใหม่ .21
                                    $worksheet->set_column(2, 2, 40);
                                    $worksheet->set_column(3, 3, 40);
                                    $worksheet->set_column(4, 4, 50); 
                                    $worksheet->set_column(5, 5, 50); 
                                    $worksheet->set_column(6, 6, 10);
                                    $worksheet->set_column(7, 7, 10);
                                    $worksheet->set_column(8, 8, 10);
                                    $worksheet->set_column(9, 9, 40);
                                    $worksheet->set_column(10, 10, 12);
                                    $worksheet->set_column(11, 11, 12);
                                    $worksheet->set_column(12, 12, 12);
                                    $worksheet->set_column(13, 13, 12);
                                    $worksheet->set_column(14, 14, 12);
                                    $worksheet->write($xlsRow+1, 1, " ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));  //เพิ่มใหม่ .21
                                    $worksheet->write($xlsRow+1, 2, " ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 3, " ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 4, " ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 5, "ผลสัมฤทธิ์", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 6, "สมรรถนะ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 7, "อื่น ๆ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 8, "ผลการประเมิน", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 9, " ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 9, "ผลสัมฤทธิ์", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 10, "สมรรถนะ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 11, "อื่น ๆ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 12, "ผลการประเมิน", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow+1, 13, "", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            }
                    }else{
                            $worksheet->set_column(1, 1, 10);      //เพิ่มใหม่ .21
                            $worksheet->set_column(2, 2, 40);
                            $worksheet->set_column(3, 3, 40);
                            $worksheet->set_column(4, 4, 10);
                            $worksheet->set_column(5, 5, 10);
                            $worksheet->set_column(6, 6, 10);
                            $worksheet->set_column(7, 7, 12);
                            $worksheet->set_column(8, 8, 40);
                            $worksheet->set_column(9, 9, 12);
                            $worksheet->set_column(10, 10, 12);
                            $worksheet->set_column(11,11, 12);
                            $worksheet->set_column(12,12, 12);
                            $worksheet->set_column(13,13, 12);
                            $worksheet->write($xlsRow+1, 1, " ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));  //เพิ่มใหม่ .21
                            $worksheet->write($xlsRow+1, 2, " ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            $worksheet->write($xlsRow+1, 3, " ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            $worksheet->write($xlsRow+1, 4, "ผลสัมฤทธิ์", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            $worksheet->write($xlsRow+1, 5, "สมรรถนะ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            $worksheet->write($xlsRow+1, 6, "อื่น ๆ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            $worksheet->write($xlsRow+1, 7, "ผลการประเมิน", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            $worksheet->write($xlsRow+1, 8, " ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            $worksheet->write($xlsRow+1, 9, "ผลสัมฤทธิ์", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            $worksheet->write($xlsRow+1, 10, "สมรรถนะ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            $worksheet->write($xlsRow+1, 11, "อื่น ๆ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            $worksheet->write($xlsRow+1, 12, "ผลการประเมิน", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            $worksheet->write($xlsRow+1, 13, "", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                    }
                    //==============================================END บรรทัดที่ 2 =================================================
                    //==============================================END เลือกแบบทั้ง 2 รอบการประเมิน =================================================
                }else{
                    $worksheet->set_column(0, 0, 10);
                    $worksheet->set_column(1, 1, 5);
                    $worksheet->set_column(2, 2, 35);

                    $worksheet->write($xlsRow, 0, "ปีงบประมาณ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                    $worksheet->write($xlsRow, 1, "ครั้งที่", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                    $worksheet->write($xlsRow, 2, "ชื่อผู้รับการประเมิน", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                    if($CTRL_TYPE <= 3 && $SESS_USERGROUP_LEVEL <= 3 && !($search_department_name) ){
                            if(!($search_ministry_name) ){
                                    $worksheet->set_column(3, 3, 15);     //เพิ่มใหม่ .21
                                    $worksheet->set_column(4, 4, 50);
                                    $worksheet->set_column(5, 5, 50);
                                    $worksheet->set_column(6, 6, 50); 
                                    $worksheet->set_column(7, 7, 50); 
                                    $worksheet->set_column(8, 8, 10);
                                    $worksheet->set_column(9, 9, 10);
                                    $worksheet->set_column(10, 10, 10);
                                    $worksheet->set_column(11, 11, 12);
                                    $worksheet->write($xlsRow, 3, "เลขที่ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));  //เพิ่มใหม่ .21
                                    $worksheet->write($xlsRow, 4, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow, 5, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow, 6, "$DEPARTMENT_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow, 7, "$MINISTRY_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow, 8, "ผลสัมฤทธิ์", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow, 9, "สมรรถนะ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow, 10, "อื่น ๆ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow, 11, "ผลการประเมิน", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            }else{
                                    $worksheet->set_column(3, 3, 15);    //เพิ่มใหม่ .21
                                    $worksheet->set_column(4, 4, 50);
                                    $worksheet->set_column(5, 5, 50);
                                    $worksheet->set_column(6, 6, 50); 
                                    $worksheet->set_column(7, 7, 10);
                                    $worksheet->set_column(8, 8, 10);
                                    $worksheet->set_column(9, 9, 10);
                                    $worksheet->set_column(10, 10, 12);
                                    $worksheet->write($xlsRow, 3, "เลขที่ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));  //เพิ่มใหม่ .21
                                    $worksheet->write($xlsRow, 4, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow, 5, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow, 6, "$DEPARTMENT_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow, 7, "ผลสัมฤทธิ์", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow, 8, "สมรรถนะ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow, 9, "อื่น ๆ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                                    $worksheet->write($xlsRow, 10, "ผลการประเมิน", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            }
                    }else{
                            $worksheet->set_column(3, 3, 15);      //เพิ่มใหม่ .21
                            $worksheet->set_column(4, 4, 50);
                            $worksheet->set_column(5, 5, 50);
                            $worksheet->set_column(6, 6, 10);
                            $worksheet->set_column(7, 7, 10);
                            $worksheet->set_column(8, 8, 10);
                            $worksheet->set_column(9, 9, 12);
                            $worksheet->write($xlsRow, 3, "เลขที่ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));  //เพิ่มใหม่ .21
                            $worksheet->write($xlsRow, 4, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            $worksheet->write($xlsRow, 5, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            $worksheet->write($xlsRow, 6, "ผลสัมฤทธิ์", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            $worksheet->write($xlsRow, 7, "สมรรถนะ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            $worksheet->write($xlsRow, 8, "อื่น ๆ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                            $worksheet->write($xlsRow, 9, "ผลการประเมิน", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
                    }
                }    
		
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
//$db_dpis->show_error();

	if($CTRL_TYPE <= 3 && $SESS_USERGROUP_LEVEL <= 3 && !($search_department_name) ){
		if(!($search_ministry_name) ){
			$iRound = 11;
		}else{
			$iRound = 10;
		}
	}else{
             if(in_array('1', $search_kf_cycle) && in_array('2', $search_kf_cycle) && $search_per_type==3){          //Release 5.2.1.25 
		$iRound = 13;
            }else{
                $iRound = 9;
            }    
	}

	if($count_data){
		$xlsRow = 0;
		$arr_title = explode("|", $report_title);
		for($j=0; $j<count($arr_title); $j++){
			$xlsRow = $j;
			$worksheet->write($xlsRow, 0, $arr_title[$j], set_format("xlsFmtTitle", "B", "C", "", 1));
			for($i=1; $i<=$iRound; $i++) $worksheet->write($xlsRow, $i, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if

		print_header($xlsRow+1,$CTRL_TYPE,$SESS_USERGROUP_LEVEL,$search_department_name,$search_ministry_name);
		$data_count = $xlsRow+1;
		
		while($data = $db_dpis->get_array()){
                    $data_count++;
                    $data_row++;
                    $xlsRow = $data_count;
                    
                    //$TMP_KF_ID = $data[KF_ID];
                    //$current_list .= ((trim($current_list))?",":"") . $TMP_KF_ID;
                    //$SELF_PER_ID = $data[PER_ID];
                    //$CHIEF_PER_ID = $data[CHIEF_PER_ID]; 	// รหัสหัวหน้า
                    //$FRIEND_FLAG = $data[FRIEND_FLAG];
                    //$ORG_ID_KPI = $data[ORG_ID_KPI];
                    
                    $KF_END_DATE = substr($data[KF_END_DATE], 0, 10);
                    //$KF_END_DATE = show_date_format($KF_END_DATE,$DATE_DISPLAY);
                    $KF_YEAR = substr($KF_END_DATE, 0, 4) + 543;
                    $TOTAL_SCORE = $data[TOTAL_SCORE];
                    $KF_CYCLE = $data[KF_CYCLE];
                    $PN_CODE = $data[PN_CODE];
                    $TMP_ORG_ID = $data[ORG_ID];
                    $TMP_ORG_NAME = "-";
                    if($TMP_ORG_ID){
                        $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $TMP_ORG_ID ";
                        $db_dpis2->send_cmd($cmd);
                        $data2 = $db_dpis2->get_array();
                        $TMP_ORG_NAME = trim($data2[ORG_NAME]);
                    }    

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

                    if(in_array('1', $search_kf_cycle) && in_array('2', $search_kf_cycle) && $search_per_type==3){
                        $TOTAL_SCORE2 = $data[TOTAL_SCORE2];
                        $TMP_ORG_ID2 = $data[ORG_ID2];
                        $TMP_ORG_NAME2 = "-";
                         if($TMP_ORG_ID2){
                            $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $TMP_ORG_ID2 ";
                            $db_dpis2->send_cmd($cmd);
                            $data2 = $db_dpis2->get_array();
                            $TMP_ORG_NAME2 = trim($data2[ORG_NAME]);
                        }    
                        
                        if ($TOTAL_SCORE2){
                            $RESULT2 = number_format($TOTAL_SCORE2, 2);
                        }else{
                                $RESULT2 = number_format(($data[SUM_KPI2] + $data[SUM_COMPETENCE2]), 2);
                        }
                        if ($data[SUM_KPI2]!=""){ 
                            $SUM_KPI2 = number_format($data[SUM_KPI2], 2);
                        }else{ 
                            $SUM_KPI2 = "-";
                        }
                        if ($data[SUM_COMPETENCE2]!=""){
                            $SUM_COMPETENCE2 = number_format($data[SUM_COMPETENCE2], 2);
                        }else{
                            $SUM_COMPETENCE2 = "-";
                        }
                        if ($data[SUM_OTHER2]!=""){
                            $SUM_OTHER2 = number_format($data[SUM_OTHER2], 2);
                        }else{
                            $SUM_OTHER2 = "-";
                        }
                        $AVERRAGE = ($RESULT+$RESULT2)/2; 
                    } 
                    
                    $cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
                    $db_dpis2->send_cmd($cmd);
                    $data2 = $db_dpis2->get_array();
                    $PN_NAME = $data2[PN_NAME];

                    $PER_FULLNAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;

                    $LEVEL_NO = trim($data[LEVEL_NO]);
                    $PER_TYPE = $data[PER_TYPE];
                    $POS_ID = $data[POS_ID];
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
                            $db_dpis2->send_cmd($cmd);
                            $data2 = $db_dpis2->get_array();
                            $TMP_PL_NAME = $data2[PL_NAME] . $POSITION_LEVEL;
                            $TMP_POS_NO = $data2[POS_NO];    //เพิ่มใหม่ .21
                    }elseif($PER_TYPE == 2){
                            $cmd = " select	pl.PN_NAME, po.ORG_NAME    
                                                            from	PER_POS_EMP pp, PER_POS_NAME pl, PER_ORG po 
                                                            where	pp.POEM_ID=$POEM_ID and pp.ORG_ID=po.ORG_ID and pp.PN_CODE=pl.PN_CODE  ";
                            $db_dpis2->send_cmd($cmd);
                            $data2 = $db_dpis2->get_array();
                            $PL_NAME = trim($data2[PN_NAME]);
                            $TMP_PL_NAME = (trim($PL_NAME))? "$PL_NAME$POSITION_LEVEL" : "";	
                    }elseif($PER_TYPE == 3){
                            $cmd = " select	pl.EP_NAME, po.ORG_NAME ,pp.POEMS_NO  
                                                            from	PER_POS_EMPSER pp, PER_EMPSER_POS_NAME pl, PER_ORG po 
                                                            where	pp.POEMS_ID=$POEMS_ID and pp.ORG_ID=po.ORG_ID and pp.EP_CODE=pl.EP_CODE  ";
                            $db_dpis2->send_cmd($cmd);
                            $data2 = $db_dpis2->get_array();
                            $PL_NAME = trim($data2[EP_NAME]);
                            // $TMP_PL_NAME = (trim($PL_NAME))? "$PL_NAME$POSITION_LEVEL" : "";	
                            $TMP_PL_NAME = (trim($PL_NAME))? "$PL_NAME" : "";	
                            $TMP_POS_NO = $data2[POEMS_NO];   //เพิ่มใหม่ .21
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
                            $db_dpis2->send_cmd($cmd);
                            $data2 = $db_dpis2->get_array();
                            $TMP_ORG_ID_REF1 = trim($data2[ORG_ID_REF]);

                            $cmd = " select ORG_NAME,ORG_ID_REF from PER_ORG where ORG_ID = $TMP_ORG_ID_REF1 ";
                            $db_dpis2->send_cmd($cmd);
                            $data2 = $db_dpis2->get_array();
                            $Department = trim($data2[ORG_NAME]);
                            $TMP_ORG_ID_REF2 = trim($data2[ORG_ID_REF]);

                            if(!($search_ministry_name) ){
                                    $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $TMP_ORG_ID_REF2 ";
                                    $db_dpis2->send_cmd($cmd);
                                    $data2 = $db_dpis2->get_array();
                                    $Ministry = trim($data2[ORG_NAME]);
                            }
                    }
                    
                    //=========================================== Write To Excel 2 รอบ =============================================================
                    if(in_array('1', $search_kf_cycle) && in_array('2', $search_kf_cycle) && $search_per_type==3){
                        //die("xxxx");
                        $II = 1;
                        $worksheet->write($xlsRow+$II, 0, $PER_FULLNAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                        $worksheet->write($xlsRow+$II, 1, $TMP_POS_NO, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));        //เพิ่มใหม่ .21
                        $worksheet->write($xlsRow+$II, 2, $TMP_PL_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                        if($CTRL_TYPE <= 3 && $SESS_USERGROUP_LEVEL <= 3 && !($search_department_name) ){
                                if(!($search_ministry_name) ){
                                        $worksheet->write($xlsRow+$II, 3, $TMP_ORG_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                        $worksheet->write($xlsRow+$II, 4, $Department, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                        $worksheet->write($xlsRow+$II, 5, $Ministry, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                        $worksheet->write($xlsRow+$II, 6, $SUM_KPI, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                        $worksheet->write($xlsRow+$II, 7, $SUM_COMPETENCE, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                        $worksheet->write($xlsRow+$II, 8, $SUM_OTHER, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                        $worksheet->write($xlsRow+$II, 9, $RESULT, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                        
                                        $worksheet->write($xlsRow+$II, 10, $TMP_ORG_NAME2, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                        $worksheet->write($xlsRow+$II, 11, $SUM_KPI2, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                        $worksheet->write($xlsRow+$II, 12, $SUM_COMPETENCE2, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                        $worksheet->write($xlsRow+$II, 13, $SUM_OTHER2, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                        $worksheet->write($xlsRow+$II, 14, $RESULT2, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                        $worksheet->write($xlsRow+$II, 15, $AVERRAGE, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                }else{
                                        $worksheet->write($xlsRow+$II, 3, $TMP_ORG_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                        $worksheet->write($xlsRow+$II, 4, $Department, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                        $worksheet->write($xlsRow+$II, 5, $SUM_KPI, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                        $worksheet->write($xlsRow+$II, 6, $SUM_COMPETENCE, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                        $worksheet->write($xlsRow+$II, 7, $SUM_OTHER, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                        $worksheet->write($xlsRow+$II, 8, $RESULT, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                        
                                        $worksheet->write($xlsRow+$II, 9, $TMP_ORG_NAME2, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                        $worksheet->write($xlsRow+$II, 10, $SUM_KPI2, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                        $worksheet->write($xlsRow+$II, 11, $SUM_COMPETENCE2, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                        $worksheet->write($xlsRow+$II, 12, $SUM_OTHER2, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                        $worksheet->write($xlsRow+$II, 13, $RESULT2, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                        $worksheet->write($xlsRow+$II, 14, $AVERRAGE, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                }
                        }else{
                                $worksheet->write($xlsRow+$II, 3, $TMP_ORG_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                $worksheet->write($xlsRow+$II, 4, $SUM_KPI, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                $worksheet->write($xlsRow+$II, 5, $SUM_COMPETENCE, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                $worksheet->write($xlsRow+$II, 6, $SUM_OTHER, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                $worksheet->write($xlsRow+$II, 7, $RESULT, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                
                                $worksheet->write($xlsRow+$II, 8, $TMP_ORG_NAME2, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                $worksheet->write($xlsRow+$II, 9, $SUM_KPI2, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                $worksheet->write($xlsRow+$II, 10, $SUM_COMPETENCE2, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                $worksheet->write($xlsRow+$II, 11, $SUM_OTHER2, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                $worksheet->write($xlsRow+$II, 12, $RESULT2, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                $worksheet->write($xlsRow+$II, 13, $AVERRAGE, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                        }
                    //=========================================== End Write To Excel 2 รอบ ==========================================================    
                    }else{
                    //=========================================== Write To Excel 1 รอบ ==============================================================
                        $worksheet->write_string($xlsRow, 0, $KF_YEAR, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
                        $worksheet->write($xlsRow, 1, $KF_CYCLE, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
                        $worksheet->write($xlsRow, 2, $PER_FULLNAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                        $worksheet->write($xlsRow, 3, $TMP_POS_NO, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));        //เพิ่มใหม่ .21
                        $worksheet->write($xlsRow, 4, $TMP_PL_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                        if($CTRL_TYPE <= 3 && $SESS_USERGROUP_LEVEL <= 3 && !($search_department_name) ){
                                if(!($search_ministry_name) ){
                                        $worksheet->write($xlsRow, 5, $TMP_ORG_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                        $worksheet->write($xlsRow, 6, $Department, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                        $worksheet->write($xlsRow, 7, $Ministry, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                        $worksheet->write($xlsRow, 8, $SUM_KPI, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                        $worksheet->write($xlsRow, 9, $SUM_COMPETENCE, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                        $worksheet->write($xlsRow, 10, $SUM_OTHER, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                        $worksheet->write($xlsRow, 11, $RESULT, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                }else{
                                        $worksheet->write($xlsRow, 5, $TMP_ORG_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                        $worksheet->write($xlsRow, 6, $Department, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                        $worksheet->write($xlsRow, 7, $SUM_KPI, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                        $worksheet->write($xlsRow, 8, $SUM_COMPETENCE, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                        $worksheet->write($xlsRow, 9, $SUM_OTHER, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                        $worksheet->write($xlsRow, 10, $RESULT, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                }
                        }else{
                                $worksheet->write($xlsRow, 5, $TMP_ORG_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                $worksheet->write($xlsRow, 6, $SUM_KPI, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                $worksheet->write($xlsRow, 7, $SUM_COMPETENCE, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                $worksheet->write($xlsRow, 8, $SUM_OTHER, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                                $worksheet->write($xlsRow, 9, $RESULT, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
                        }
                    }
                    //=========================================== End Write To Excel 1 รอบ ==========================================================
		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		for($i=1; $i<=$iRound; $i++) $worksheet->write($xlsRow, $i, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_code.xls\"");
	header("Content-Disposition: inline; filename=\"$report_code.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>