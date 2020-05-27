<?
       // echo 100;
       //die();
       
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
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

        
        $dir_folder = file_exists("../../Excel/tmp/R0505");
        if($dir_folder==0){
            mkdir("../../Excel/tmp/R0505"); 
        }

       
        
	$db_dpis  = connect_oci8_test($db_host, $db_name, $db_user, $db_pwd, $port);
        $db_dpis1 = connect_oci8_test($db_host, $db_name, $db_user, $db_pwd, $port);
        $db_dpis2 = connect_oci8_test($db_host, $db_name, $db_user, $db_pwd, $port);
        
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
                        SELECT count(*) AS cnt FROM (
                            SELECT t.cur_level||t.per_id||t.POH_EFFECTIVEDATE||typ||SALARY_HIS xx,t.* FROM tb_his t  --WHERE  rownum<= 500
                        )
                    
                        order by xx 
                    ";
                    //echo "<pre>";
                   // die($cmd);
        if($select_org_structure==1) { 
                $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
                $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
        }
        $count_data = num_row($db_dpis1,$cmd);
        $exQuery1 = oci_parse($db_dpis2, $cmd);
        oci_execute($exQuery1);
        $data = oci_fetch_array($exQuery1, OCI_BOTH);
        //$count_data = 1;
        if($count_data){
            echo $data[CNT];
            //die();
        }else{
            echo "0";
        }

   
        
?>