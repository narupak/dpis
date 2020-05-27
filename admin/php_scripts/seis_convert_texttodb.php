<?php
    
    /* Test Function Rollback
     * 
     * Create connect oci8
     * @param get form file db_connect_var.php 
     * 
     * 
     * include("../php_scripts/db_connect_var.php");
    */
    /*  $host = '164.115.146.226';
     *  $database ='ES';
     *  $user = 'coj091161';
     *  $password= 'coj091161';
     *  $port = '1521';
     * 
     *  $conn = connect_oci8_test($host, $database, $user, $password, $port);
     *  $conn2 = connect_oci8_test($host, $database, $user, $password, $port);
     *
     *  $count_data =  num_row($conn,$sql_query);
     *  $stid = oci_parse($conn2, $sql_query);
     *  oci_execute($stid);
     *  while (($row = oci_fetch_array($stid, OCI_BOTH)) != false) {
     *      echo $row[0] . " und " . $row[PL_NAME]   . " count_data = ".$count_data."<br>\n";
     *  }
     *  oci_free_statement($stid);
     *  oci_close($conn2);
     *  die();
    */
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
    /*
     * 
     * Count numrow data
     * 
     */
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
    
     /*
     * Simple helper to debug to the console
     * 
     * @param  Array, String $data
     * @return String
     */
    function debug_to_console( $data ) {
        if ( is_array( $data ) )
                $output = '<script>console.log( "Debug Objects: ' . implode( ',', $data) . '" );</script>';
        else
                $output = '<script>console.log( "Debug Objects: ' . $data . '" );</script>';
        echo $output;
    }
    
    /*ini_set("max_execution_time",0);
    set_time_limit(0);
    ini_set("memory_limit","512M");*/
    //ini_set("session.gc_maxlifetime",60*60*24);
    
    /*$conn = connect_oci8_test($db_host, $db_name, $db_user, $db_pwd, $port);
    
    $xxx=0;
    $TIMESTAMPS = date("Y-m-d H:i:s");
    $strSQL = " INSERT INTO TMP_MAP_MASTERDATA ( NEW_CODE, OLD_CODE, TIMESTAMPS ) VALUES ( 'xxx1' , 'yyy' , '$TIMESTAMPS' ) ";
    $parse_Test = OCIParse($conn, $strSQL);    
    if(OCIExecute($parse_Test, OCI_NO_AUTO_COMMIT)) {
        //OCIExecute($this->result, OCI_COMMIT_ON_SUCCESS);
        //echo 'Success';        
        
        $query_Test1 = "INSERT INTO TMP_MAP_MASTERDATA ( NEW_CODE, OLD_CODE, TIMESTAMPS ) VALUES ( 'xxx2' , 'yyy' , '$TIMESTAMPS' ) ";    

        $parse_Test1 = OCIParse($conn, $query_Test1);
        if(!OCIExecute($parse_Test1, OCI_NO_AUTO_COMMIT)){
            //echo "ไม่ผ่าน";
            ocirollback($conn);  
        }else{
            //echo "ผ่าน";
            // insert or update when statement success
            ocicommit($conn); 
        }
        OCILogOff($conn);
    }*/
    
    /*
     * 
     * End Test Rollback
     * 
    */
//============================================================================= Satrt ========================================================================
    
    include("../php_scripts/connect_database.php");
    include("php_scripts/session_start.php");
    include("../php_scripts/connect_file.php");
    include ("php_scripts/function_share.php");
   
    /*ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);*/
    $debug=0; /* open = 1 || close = 0 */
    
    function set_constraints($db_mst,$db_cont,$tbname,$set){
        //$set=DISABLE,ENABLE
        $sql="  SELECT CONSTRAINT_NAME
                FROM USER_CONSTRAINTS NATURAL JOIN USER_CONS_COLUMNS
                WHERE TABLE_NAME = '".strtoupper($tbname)."' AND CONSTRAINT_TYPE='R'
                ORDER BY CONSTRAINT_NAME ";
        $db_mst->send_cmd($sql);
        while($data_fk = $db_mst->get_array()){	
            $CONSTRAINT_NAME = $data_fk[CONSTRAINT_NAME];
            $sql_set="ALTER TABLE ".strtoupper($tbname)." ".$set." CONSTRAINT ".$CONSTRAINT_NAME;
            $db_cont->send_cmd($sql_set);
        }
    }
    
    //gen table tempmap
    /*$cmdChk ="SELECT COUNT(COLUMN_NAME) AS CNT FROM USER_TAB_COLS WHERE  TABLE_NAME = 'TMP_MAP_MASTERDATA'";
    $db_dpis->send_cmd($cmdChk);
    $dataChk = $db_dpis->get_array();
    if($dataChk[CNT]=="0"){
        $cmdA_1 = " CREATE TABLE TMP_MAP_MASTERDATA (
                        NEW_CODE VARCHAR2(255 BYTE) ,
                        OLD_CODE VARCHAR2(255 BYTE) ,
                        TIMESTAMPS VARCHAR2(19 BYTE)
                    ) ";
        $db_dpis->send_cmd($cmdA_1);
        $cmdA = "COMMIT";
        $db_dpis->send_cmd($cmdA_1);
    }*/

    $db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis5 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    
    //SELECT * FROM USER_TABLES;
    
    $path_toshow_s = "C:\\dpis\\personal_data_seis\\";
    $path_tosave = (trim($path_tosave))? $path_tosave : $path_toshow_s;
    $path_tosave_tmp = str_replace("\\", "\\\\", $path_tosave);
    $path_toshow = $path_tosave;
    if(is_dir($path_toshow) == false) {
        mkdir($path_toshow, 0777,true);
    }
    $Err_Part = 0;
    if(is_dir($path_toshow) == false) {
        $Err_Part = 1;
    }
    //$path_tosave_tmp  = $dir;   
    $DIVIDE_TEXTFILE = "|#|";
    $TYPE_TEXT_STR = array("VARCHAR", "VARCHAR2", "CHAR");
    $TYPE_TEXT_INT = array("NUMBER");
    $DEPARTMENT_ID = $SESS_DEPARTMENT_ID;
    if (!$DEPARTMENT_ID){ $DEPARTMENT_ID = "NULL";}
    $TIMESTAMPS = date("Y-m-d H:i:s");
    
    if ($command=="CONVERT") { 
        $url = 'php_scripts/table.json';
        $data_json = file_get_contents($url);
        $data_table = json_decode($data_json, true);

        $sort_arr[0]['name'] = "TB_SEQ";
        $sort_arr[0]['sort'] = "ASC";
        $sort_arr[0]['case'] = FALSE; //  Case sensitive

        array_sort($data_table, $sort_arr);

        //var_dump($data_table);
        $MAX_COLEVEL = rand(1000,100000);
        $num=0;
        
        //Check if there is a position or not.
        if(1===1){
            // ================ select ชื่อ fields จาก $table_master =======================
            $cmd = " select * from PER_POSITION where rownum = 1 ";
            $db_dpis->send_cmd_fast($cmd);
            $field_list = $db_dpis->list_fields();
            // ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
            unset($arr_fields);
            for($i=1; $i<=count($field_list); $i++) : 
                    $arr_fields[] = $tmp_name = $field_list[$i]["name"];
                    $arr_fields_type[$tmp_name] = $field_list[$i]["type"];
            endfor;
           
            
            $db_textfile = new connect_file("PER_POSITION", "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");
            while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
                // ลบค่าข้อมูล field POS_ID ที่ได้จาก textfile ออก เพื่อใส่ค่า max ตัวใหม่
                $POS_NO_INT = $data[POS_NO];
                $old_POS_ID = $data[POS_ID];
                unset($POS_ERR_NULL);
                $POS_ERR_NULL = array();
                $cmd = " select POS_NO,POS_ID from  PER_POSITION where POS_NO = $POS_NO_INT and POS_STATUS=1 ";
                $cnt_chk = $db_dpis2->query($cmd);
                if($cnt_chk){
                    $data2 = $db_dpis2->get_array();
                    $TMP_POS_ID = $data2[POS_ID];
                    $POS_ID[$old_POS_ID] = $TMP_POS_ID;
                }else{
                    array_push($POS_ERR_NULL,$POS_NO_INT);
                }
            }  // end while 
        }
        //array_push($POS_ERR_NULL,'0203231');
        //var_dump($POS_ERR_NULL);
        //unset($POS_ERR_NULL);
        //echo count($POS_ERR_NULL);
        $CNT_POS = count($POS_ERR_NULL);
        if($CNT_POS<=0){
        //if(1==0){ //if($CNT_POS<=0){
            $codenew_exist = array();
            foreach ($data_table as $table_master) {
                // ================ select ชื่อ fields จาก $table_master =======================
                $cmd = " select * from ".$table_master['TABLE_NAME']." where rownum=1";
                $db_dpis->send_cmd_fast($cmd);
                $field_list = $db_dpis->list_fields($table_master['TABLE_NAME']);
                // ============== start นำชื่อ fields และประเภทของ fields เก็บลง array ============
                unset($arr_fields);
                if ($DPISDB == "odbc" || $DPISDB == "oci8") {
                    for ($j = 1; $j <= count($field_list); $j++):
                        $arr_fields[] = $tmp_name = $field_list[$j]["name"];
                        $arr_fields_type[$tmp_name] = $field_list[$j]["type"];
                    endfor;
                }
                $db_textfile = new connect_file($table_master['TABLE_NAME'], "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");

                if($table_master['TB_TYPE']=='MASTER'){
                    //============================================================== check exist column in table master =================================================================//
                    $xsql = "select COLUMN_NAME from USER_TAB_COLS where table_name='".$table_master['TABLE_NAME']."'";
                    //echo "xxx=>".$xsql."<br>";
                    $db_dpis2->send_cmd($xsql);
                    unset($xexist);
                    $xexist = array();
                    while ($x = $db_dpis2->get_array()){
                        array_push($xexist,$x[COLUMN_NAME]);
                    }
                    //============================================================= End check exist column in table master ==================================================================//
                    //
                    //============================================================== Read File && import Table to Master ===================================================================//
                    //$tbname = '';
                    while ($data = $db_textfile->get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
                        // ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields ตาม texfile
                        unset($field_name, $field_value);
                        unset($CURR_CODE, $NEW_CODE ,$NEW_CODE);
                        foreach ($data as $key => $fieldvalue) {
                            if (in_array($key, $xexist)) { 
                                $fieldvalue = str_replace("<br>", "\n", $fieldvalue);
                                $num++;
                                if($key==$table_master['LIST_COLUMN'][0]['CODE'] || $key==$table_master['LIST_COLUMN'][0]['NAME'] || $key==$table_master['LIST_COLUMN'][0]['NAME1']){
                                    if($key==$table_master['LIST_COLUMN'][0]['CODE']){
                                        //echo $key."<br>";
                                        $cmd = " select ".$table_master['LIST_COLUMN'][0]['CODE']."  from ".$table_master['TABLE_NAME']." where trim(".$table_master['LIST_COLUMN'][0]['CODE'].") = trim(".$fieldvalue.")";
                                        $cnt = $db_dpis2->send_cmd($cmd);
                                        //echo $cnt."===".$cmd."<br>";
                                        //$db_dpis2->show_error();
                                        if($cnt){
                                            $data2 = $db_dpis2->get_array();
                                            $chk_have_CODE = 1;
                                            $OLD_CODE = $fieldvalue;
                                            //get max value
                                            if($table_master['TABLE_NAME'] != "PER_CO_LEVEL" && $table_master['TABLE_NAME'] != "PER_BLOOD"){
                                                $cmd_new = " select max(".$table_master['LIST_COLUMN'][0]['CODE'].") as MAX_ID from ".$table_master['TABLE_NAME']." ";
                                                $db_dpis2->send_cmd($cmd_new);
                                                $data_new = $db_dpis2->get_array();
                                                $NEW_CODE = $data_new[MAX_ID]+1;
                                            }else{
                                                //ไม่สามารถ get max ได้เนื่องจากค่าเป็น String
                                                $NEW_CODE = $MAX_COLEVEL+$num;
                                            }
                                        }else{
                                            $chk_have_CODE = 0;
                                            $OLD_CODE = $fieldvalue;
                                            $NEW_CODE = $fieldvalue;
                                        }
                                    }
                                    if($key==$table_master['LIST_COLUMN'][0]['NAME']){
                                        //echo $key."<br>";
                                        $cmd = " select ".$table_master['LIST_COLUMN'][0]['CODE']." ,".$table_master['LIST_COLUMN'][0]['NAME']."  from ".$table_master['TABLE_NAME']." where trim(".$table_master['LIST_COLUMN'][0]['NAME'].") = trim(".$fieldvalue.")";
                                        $cnt = $db_dpis2->send_cmd($cmd);
                                        //echo $cnt."===".$cmd."<br>";
                                        if($cnt){
                                            $chk_have_NAME = 1;
                                            $data_new_chk = $db_dpis2->get_array();
                                            $CURR_CODE = $data_new_chk[$table_master['LIST_COLUMN'][0]['CODE']];
                                            $CO_MIN='';
                                            if($table_master['TABLE_NAME'] == "PER_CO_LEVEL"){
                                                $CO_MIN = $fieldvalue;
                                            }
                                        } else {
                                            $chk_have_NAME = 0;
                                            //$CURR_CODE = $fieldvalue;
                                            $CO_MIN='';
                                            if($table_master['TABLE_NAME'] == "PER_CO_LEVEL"){
                                                $CO_MIN = $fieldvalue;
                                            }
                                        }
                                        $field_name .= (trim($field_name) != "")? ", " . $key : $key;
                                        $field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
                                    }
                                    if($key==$table_master['LIST_COLUMN'][0]['NAME1']){
                                        //echo $key."<br>";
                                        $CO_MAX='';
                                        if($table_master['TABLE_NAME'] == "PER_CO_LEVEL"){
                                            $CO_MAX = $fieldvalue;
                                        }
                                        $field_name .= (trim($field_name) != "")? ", " . $key : $key;
                                        $field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
                                    }
                                }else{
                                    $field_name .= (trim($field_name) != "")? ", " . $key : $key;
                                    $field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
                                }
                            }
                        }
                        //Table Co_level ต้องเช็คแยก เพราะต้องเช็คคู๋กัน 2 ค่า
                        if($table_master['TABLE_NAME'] == "PER_CO_LEVEL"){
                            $cmd = " select CL_NAME from ".$table_master['TABLE_NAME']." where LEVEL_NO_MIN = $CO_MIN and LEVEL_NO_MAX = $CO_MAX ";
                            //echo "<pre> $cmd <br>";
                            $cnt = $db_dpis2->send_cmd($cmd);
                            if($cnt){
                                $chk_have_NAME = 1;
                                $data_new_chk_name = $db_dpis2->get_array();
                                $CURR_CODE = $data_new_chk_name[CL_NAME];
                            } else {
                                $chk_have_NAME = 0;
                                //$CURR_CODE = $fieldvalue;
                            }
                        }
                        $key_CODE = array_search("'".$table_master['LIST_COLUMN'][0]['CODE']."'", $arr_fields); 
                        unset($arr_fields[$key_CODE], $data[$table_master['LIST_COLUMN'][0]['CODE']]);
                        if($chk_have_CODE){
                            $insert_field = "".$table_master['LIST_COLUMN'][0]['CODE']." , ";
                            $insert_value = "$NEW_CODE, ";
                        }else{
                            if($chk_have_NAME){
                                $NEW_CODE = "'$CURR_CODE'";
                            }else{
                                $insert_field = "".$table_master['LIST_COLUMN'][0]['CODE']." , ";
                                $insert_value = "$NEW_CODE, ";
                            }
                        }
                        if(!$chk_have_CODE && !$chk_have_NAME){
                            $strSQL2 = " INSERT INTO ".$table_master['TABLE_NAME']." ( $insert_field $field_name ) VALUES ( $insert_value $field_value ) ";
                            $db_dpis2->send_cmd_fast($strSQL2);
                            if($debug==1){echo $table_master['TABLE_NAME']." = ".$strSQL2."<br>";}
                        }
                        if(!$chk_have_CODE || !$chk_have_NAME){
                            $strSQL = " INSERT INTO TMP_MAP_MASTERDATA ( NEW_CODE, OLD_CODE, TIMESTAMPS ) VALUES ( $NEW_CODE , $OLD_CODE , '$TIMESTAMPS' ) ";
                            $db_dpis2->send_cmd_fast($strSQL);
                            //array_push($codenew_exist,$table_master['LIST_COLUMN'][0]['CODE']); //ปิดเรื่องการเช็ค ว่ามีคอน์ลั่ม Master ใดบ้างที่ gen code ใหม่
                            if($debug==1){echo "tmpMasterData = ".$strSQL."<br><hr><br>";}
                        }
                    }
                }else if($table_master['TB_TYPE']=='PERSON'){

                    //============================================================== check exist column in table Person =================================================================//
                    $xsql = "select COLUMN_NAME from user_tab_cols where table_name='PER_PERSONAL'";
                    //echo "xxx=>".$xsql."<br>";
                    $db_dpis2->send_cmd($xsql);
                    unset($xexist);
                    $xexist = array();
                    while ($x = $db_dpis2->get_array()){
                        array_push($xexist,$x[COLUMN_NAME]);
                    }
                    //============================================================= End check exist column in table Person ==================================================================//
                    //
                    //============================================================== Read File && import Table to Person ===================================================================//
                    $loop=1;
                    $cmd = " select max(".$table_master['LIST_COLUMN'][0]['CODE'].") as MAX_ID from ".$table_master['TABLE_NAME'];
                    $db_dpis2->send_cmd_fast($cmd);
                    $data2 = $db_dpis2->get_array();
                    //$PER_ID= $data2[MAX_ID] + 1;
                    $LAST_PER_ID= $data2[MAX_ID];

                    while ($data = $db_textfile->get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
                        $ctext = count($data);
                        $oldperid = $data[PER_ID];
                        $oldposid = $data[POS_ID];
                        if($oldposid && $oldposid != 'NULL'){
                            $f_column = "POS_ID,";
                            $NEW_POS_ID = $POS_ID[$oldposid].","; 
                        }else{
                            $f_column = "";
                            $NEW_POS_ID = "";
                        }
                        $PER_ID[$oldperid] = ($LAST_PER_ID + $loop);
                        unset($data[PER_ID], $field_name, $field_value);
                        if($oldposid && $oldposid != 'NULL'){ unset($data[POS_ID], $field_name, $field_value);}
                        unset($data[DEPARTMENT_ID], $field_name, $field_value);
                        if (isset($data[PER_OFFNO])) {
                            $data[PER_OFFNO] = "NULL";
                        }

                        // ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields
                        foreach ($data as $key => $fieldvalue) {
                            if (in_array($key, $xexist)) {
                                $fieldvalue = str_replace("<br>", "\n", $fieldvalue);
                                if ($key == 'UPDATE_DATE' && $fieldvalue == 'NULL') {
                                    $fieldvalue_x = date("Y-m-d H:i:s");
                                    $fieldvalue = "'$fieldvalue_x'";
                                }
                                //if (in_array($key, $codenew_exist)) { //ปิดเรื่องการเช็ค ว่ามีคอน์ลั่ม Master ใดบ้างที่ gen code ใหม่
                                $strSQLP = "SELECT NEW_CODE FROM TMP_MAP_MASTERDATA WHERE OLD_CODE = ".$fieldvalue." and TIMESTAMPS = '$TIMESTAMPS'";
                                $cnt = $db_dpis2->send_cmd($strSQLP);
                                if($debug==1){echo "<pre>".$strSQLP."<br>";}
                                if($cnt){
                                    $data_new = $db_dpis2->get_array();
                                    $NEW_CODE = $data_new[NEW_CODE];
                                    $fieldvalue = "'$NEW_CODE'";
                                }else{
                                    $fieldvalue = $fieldvalue;
                                }
                                //}
                                $field_name .= (trim($field_name) != "") ? ", " . $key : $key;
                                $field_value .= (trim($field_value) != "") ? ", " . $fieldvalue : $fieldvalue;
                            }
                        }
                        set_constraints($db_dpis1, $db_dpis5, 'PER_PERSONAL', 'DISABLE'); 
                        $cmd = " INSERT INTO PER_PERSONAL ( PER_ID, $f_column  DEPARTMENT_ID, $field_name )
                                VALUES ($PER_ID[$oldperid], $NEW_POS_ID  $DEPARTMENT_ID, $field_value ) ";
                        $db_dpis->send_cmd_fast($cmd);
                        if($debug==1){echo "<pre>".$cmd."<br><hr><br>";}
                        set_constraints($db_dpis1, $db_dpis5, 'PER_PERSONAL', 'ENABLE'); 

                        $chkErr = $db_dpis->ERROR;
                        if (!$chkErr) {
                            echo '<font color=red>ไม่สามารถนำเข้าข้อมูล ข้าราชการได้ <br>[' . $db_dpis->show_error() . ']</font><br>-----------------------<br>';
                        }else{
                            $PER_NAME = str_replace("'", "", $data[PER_NAME]) . " " . str_replace("'", "", $data[PER_SURNAME]);
                            $ARR_PER_NAME[] = $PER_NAME;
                        }

                        $loop++;
                    }
                }else if($table_master['TB_TYPE']=='HISTRORY') {
                     //============================================================== check exist column in table History =================================================================//
                    $xsql = "select COLUMN_NAME from USER_TAB_COLS where table_name='".$table_master['TABLE_NAME']."'";
                    //echo "xxx=>".$xsql."<br>";
                    $db_dpis2->send_cmd($xsql);
                    unset($xexist);
                    $xexist = array();
                    while ($x = $db_dpis2->get_array()){
                        array_push($xexist,$x[COLUMN_NAME]);
                    }
                    //============================================================= End check exist column in table History ==================================================================//
                    //
                    //======================================================================== import Table HISTORY =========================================================================//
                    while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
                        $oldperid = $data[PER_ID];

                        $cmd = " select max(".$table_master['LIST_COLUMN'][0]['CODE'].") as MAX_ID from ".$table_master['TABLE_NAME'];
                        $db_dpis2->send_cmd_fast($cmd);
                        //$db_dpis2->show_error();
                        $data2 = $db_dpis2->get_array();
                        $MAX_ID = $data2[MAX_ID] + 1;

                        $key_MAX_ID = array_search("'".$table_master['LIST_COLUMN'][0]['CODE']."'", $arr_fields);
                        
                        //unset code table history
                        if($table_master['TABLE_NAME'] != "PER_POSITIONHIS"){
                            unset($arr_fields[$key_MAX_ID], $data[PER_ID], $data[$table_master['LIST_COLUMN'][0]['CODE']]);
                            $insert_field = "PER_ID, ".$table_master['LIST_COLUMN'][0]['CODE'].", ";
                            $insert_value = "$PER_ID[$oldperid], $MAX_ID, ";
                        }else{	
                            unset($arr_fields[$key_MAX_ID], $data[PER_ID], $data[$table_master['LIST_COLUMN'][0]['CODE']], $data[ORG_ID_1], $data[ORG_ID_2], $data[ORG_ID_3] );
                            $insert_field = "PER_ID, ".$table_master['LIST_COLUMN'][0]['CODE'].", ORG_ID_1, ORG_ID_2, ORG_ID_3, ";
                            $insert_value = "$PER_ID[$oldperid], $MAX_ID , $DEPARTMENT_ID, $DEPARTMENT_ID, $DEPARTMENT_ID,  ";	
                        }
                        
                        unset ($field_name, $field_value);
                        // ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields
                        foreach ($data as $key => $fieldvalue) {
                            if (in_array($key, $xexist)) {
                                $fieldvalue = str_replace("<br>", "\n", $fieldvalue);
                                if ($key=='UPDATE_DATE' && $fieldvalue=='NULL') {
                                        $fieldvalue_x = date("Y-m-d H:i:s");
                                        $fieldvalue = "'$fieldvalue_x'";
                                }
                                if (($key=='MAH_MARRY_DATE' || $key=='POH_EFFECTIVEDATE' || $key=='POH_DOCDATE' || $key=='SAH_EFFECTIVEDATE' || $key=='SAH_DOCDATE') && $fieldvalue=='NULL') {
                                        $fieldvalue_x = "1957-01-01";
                                        $fieldvalue = "'$fieldvalue_x'";
                                }
                                if (($key=='POH_REMARK' || $key=='POH_POS_NO' || $key=='POH_DOCNO' || $key=='SAH_DOCNO') && $fieldvalue=='NULL') {
                                        $fieldvalue_x = "-";
                                        $fieldvalue = "'$fieldvalue_x'";
                                }
                                //if (in_array($key, $codenew_exist)) { //ปิดเรื่องการเช็ค ว่ามีคอน์ลั่ม Master ใดบ้างที่ gen code ใหม่
                                $strSQLNEW = "select NEW_CODE, OLD_CODE from TMP_MAP_MASTERDATA  WHERE OLD_CODE = ".$fieldvalue." and TIMESTAMPS = '$TIMESTAMPS'";
                                $cnt_c = $db_dpis4->send_cmd($strSQLNEW);
                                if($debug==1){echo "Search OLD code =>".$strSQLNEW."<br>";}
                                if($cnt_c){
                                    $data_codenew = $db_dpis4->get_array();
                                    $NEW_CODE = $data_codenew[NEW_CODE];//$fieldvalue;//
                                    $fieldvalue = "'$NEW_CODE'";
                                }else{
                                    $fieldvalue = $fieldvalue;
                                }
                                //}

                                $field_name .= (trim($field_name) != "")? ", " . $key : $key;
                                $field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
                            }
                        }
                        set_constraints($db_dpis1,$db_dpis5,$table_master['TABLE_NAME'],'DISABLE');
                        $cmd = " INSERT INTO ".$table_master['TABLE_NAME']." ( $insert_field $field_name ) VALUES ( $insert_value $field_value ) ";
                        if($debug==1){echo "<pre>".$cmd."<br>";}
                        $db_dpis->send_cmd_fast($cmd); //comment test
                        set_constraints($db_dpis1,$db_dpis5,$table_master['TABLE_NAME'],'ENABLE');
                        $chkErr=$db_dpis->ERROR;
                        if(!$chkErr){
                            echo  '<font color=red>ไม่สามารถนำเข้าข้อมูล '.$table_master['TABLE_NAME'].'ได้ <br>['.$db_dpis->show_error().']</font><br>-----------------------<br>';
                        }
                    }
                }
            }
            unset($data, $arr_fields, $field_name, $field_value,$codenew_exist); 
            $path_toshow = stripslashes($path_toshow);
        //}
        }
    }