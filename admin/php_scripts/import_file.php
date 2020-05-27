<?
//echo $insert_active;
//echo "php :14/04/63 <br>";
include("php_scripts/session_start.php");
include("php_scripts/function_repgen.php");
include("../php_scripts/connect_file.php");   
ini_set('error_reporting', 0); //ปิด warning
if (strlen($form) == 0) {
    echo "โปรดส่งค่าชื่อ ตาราง ด้วย ดังตัวอย่างนี้ import_file.html?form=per_slip_form.php<br>";
    exit;
} else {
    include("import_forms/" . $form);
}

$debug=0;/*0=ปิด,1=เปิด*/

$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["TEXT_FILE"]["name"]);
$RealFile = $target_file;
if ($command == "TEMPDATA")
    $impfile = "TEMPDATA.TXT";
if ($impfile)
    $impfile = stripslashes($impfile);
$c = strrpos($RealFile, ".");
if ($c)
    $imptype = strtolower(substr($RealFile, $c + 1));
	//echo "form=$form , impfile=$impfile , RealFile=$RealFile , imptype=$imptype<br>";
	//echo "fix_status=$fix_status , remark=$remark<br>";
//date_default_timezone_set("Asia/Bangkok"); // eror because Version: 4.3.11 but Version: 5.1.?? up

$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$db_dpis5 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$db_dpis6 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);


if (!$current_page)
    $current_page = 1;
if (!$data_per_page)
    $data_per_page = 30;
$start_record = ($current_page - 1) * $data_per_page;

$arr_primery = explode("|", $prime); // กระจายค่า Primery Key

$delimeter = chr(126); // ~

if ($DPISDB == "odbc")
    $cmd = " select top 1 * from $table ";
elseif ($DPISDB == "oci8")
    $cmd = " select * from $table where rownum = 1 ";
elseif ($DPISDB == "mysql")
    $cmd = " select * from $table limit 1 ";

$db_dpis->send_cmd($cmd);

$field_list = $db_dpis->list_fields($table);
//	echo "<pre>";		print_r($field_list);		echo "</pre>";
unset($arr_fields);
unset($arr_fields_type);
if ($DPISDB == "odbc" || $DPISDB == "oci8" || $DPISDB == "mssql") {
    for ($i = 1; $i <= count($field_list); $i++) :
        $arr_fields[] = $tmp_name = $field_list[$i]["name"];
        $arr_fields_type[$tmp_name] = $field_list[$i]["type"];
        //echo "type=".$field_list[$i]["type"]." , in array=".$arr_fields_type[$tmp_name]."<br>";
    endfor;
    
    //http://dpis.ocsc.go.th/Service/node/2359
    if($search_per_type==3){
        $arr_fields[]='LIVING_EXPENSES';
        $arr_fields_type['LIVING_EXPENSES']='NUMBER';
    }
    //end
} // end if

$UPDATE_DATE = date("Y-m-d H:i:s");

if ($DPISDB == "odbc") {
    $TYPE_TEXT_STR = array("VARCHAR", "MEMO", "LONGCHAR", "TEXT");
    $TYPE_TEXT_INT = array("INTEGER", "INTEGER2", "SMALLINT", "SINGLE", "DOUBLE", "REAL", "NUMBER");
} elseif ($DPISDB == "oci8") {
    $TYPE_TEXT_STR = array("VARCHAR", "VARCHAR2", "CHAR");
    $TYPE_TEXT_INT = array("NUMBER");
} elseif ($DPISDB == "mysql") {
    $TYPE_TEXT_STR = array("VARCHAR", "MEMO", "LONGCHAR", "TEXT");
    $TYPE_TEXT_INT = array("INTEGER", "INTEGER2", "SMALLINT", "SINGLE", "DOUBLE", "REAL", "NUMBER");
}
// ======================================================================
//	$RealFile = stripslashes($RealFile);
//	echo "command=".$command."- RealFile=$RealFile --[".is_file($RealFile)."]<br>";
if ($command == "UPLOAD") {
//		echo "_FILES['TEXT_FILE']['tmp_name']=".$_FILES["TEXT_FILE"]["tmp_name"]." , _FILES['TEXT_FILE']['name']=".$_FILES["TEXT_FILE"]["name"]."<br>";
//		$target_dir = "uploads/";
//		$target_file = $target_dir . basename($_FILES["TEXT_FILE"]["name"]);
    $uploadOk = 1;
    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
    // Check if image file is a actual image or fake image
    /* 		if(isset($_POST["submit"])) {
      $check = getimagesize($_FILES["TEXT_FILE"]["tmp_name"]);
      ec
      if($check !== false) {
      echo "File is an image - " . $check["mime"] . ".";
      $uploadOk = 1;
      } else {
      echo "File is not an image.";
      $uploadOk = 0;
      }
      } */
    // Check if file already exists
    if (file_exists($target_file)) {
        unlink($target_file);
//			echo "Sorry, file already exists.";
//			$uploadOk = 0;
    }
    // Check file size
//		echo "size=".$_FILES["TEXT_FILE"]["size"] ." > 500000<br>";
		if($maxsize_up_file  == 0){
			$size_file = 5000000; // ถ้าไม่มีการกำหนดขนาดไฟล์ที่หน้า c06 default ให้เป็น 5MB 
		}else{
			$size_file = $maxsize_up_file * 1000000;
		}

    if ($_FILES["TEXT_FILE"]["size"] > $size_file) {
        $excel_msg = "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    // Allow certain file formats
//		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
//		&& $imageFileType != "gif" ) {
//			echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
//			$uploadOk = 0;
//		}
    // Check if $uploadOk is set to 0 by an error
//		echo "target_file=$target_file<br>";
    if ($uploadOk == 0) {
        $excel_msg = "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
    } else {
//			if (file_exists($target_file)) unlink($target_file);
//			echo "$ _FILES['TEXT_FILE']['tmp_name']=".$_FILES["TEXT_FILE"]["tmp_name"]." , target_file=$target_file<br>";
        if (move_uploaded_file($_FILES["TEXT_FILE"]["tmp_name"], $target_file)) {
            $excel_msg = "The file " . basename($_FILES["TEXT_FILE"]["name"]) . " has been uploaded.";
        } else {
            $excel_msg = "Sorry, there was an error uploading your file.";
        }
    }   
} // end if ($command=="UPLOAD")

if ($command == "CONVERT" || ($command == "UPLOAD" && $uploadOk)) {  // && is_file($RealFile)		//	trim($RealFile)
    // ล้างข้อมูลเก่าออกก่อน
    $cmd = "select * from IMPORT_TEMP where IMPORT_FILENAME='$form'";
 //  echo "select cmd=$cmd<br>";
    $cntdel = $db_dpis->send_cmd($cmd);
    //$db_dpis->show_error();
    if ($cntdel > 0) {
        $cmd = "	delete from IMPORT_TEMP where IMPORT_FILENAME='$form'";
//			echo "delete cmd=$cmd<br>";
        $db_dpis->send_cmd($cmd);
        //	$db_dpis->show_error();
    }

    if ($running > -1) { // เป็นกรณี key เป็น running number
        $cmd = " select max($arr_fields[$running]) as MAX_ID from $table ";
        $db_dpis1->send_cmd($cmd);
        $data1 = $db_dpis1->get_array();
        $MAX_ID = $data1[MAX_ID] + 1;
			//echo "max_id-$cmd ($MAX_ID)<br>";
    }

    if ($imptype == "xls") {
        $excel_msg = "";
        require_once('excelread.php');

        $config = array('excel_filename' => $RealFile,
            'excel_sheet' => 0, 'excel_numeric' => FALSE, 'excel_duplicate' => FALSE, 'excel_sort' => FALSE, 'excel_debug' => FALSE);

        $excel_data = excel_read($config);

        //echo "<pre>";
        $tmp_row_num = 1;
   foreach ($excel_data as $row) {
            if($table == "PER_SALARYHIS"){
                $cardno_f_ex = $row[0];
                $PER_CARDNO = str_replace(" ", "", $cardno_f_ex);
                $PER_CARDNO = str_replace("-", "", $PER_CARDNO);
                $cmd = " select PER_ID, PER_STATUS from PER_PERSONAL where  PER_CARDNO= '$PER_CARDNO' ";
                $cnt_no = $db_dpis4->send_cmd($cmd);

                if ($cnt_no > 0) {
                    while ($data4 = $db_dpis4->get_array()) {
                        $PER_STATUS = $data4[PER_STATUS];
                        $row[0] = $data4[PER_ID];
                        $values = implode("'^'", $row);
                        $data = str_replace("'", "", $values);
                        insert2temp($data, "^");
                    }
                } else {
                    $row[0] = $PER_CARDNO;
                    $values = implode("'^'", $row);
                    $data = str_replace("'", "", $values);
                    insert2temp($data, "^");
                }
            }else{
                  $values = implode("'^'", $row);
                  $data = str_replace("'", "", $values);
                  insert2temp($data, "^");
            }
    } // end for loop

//			echo "</pre>";
    } else {
//			echo "imptype=$imptype<br>";
        $head = 0;  // text file ไม่มี head = 0   ถ้ามี head ให้ = 1
                    	echo "connect_file table=$table , RealFile=$RealFile<br>";
        $db_textfile = new connect_file("$table", "r", $DIVIDE_TEXTFILE, $RealFile, $head);

        $field_name = implode("^", $arr_fields);
        $tmp_row_num = 1;

        while ($data = $db_textfile->get_text_data($data_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {

        if($table == "PER_SLIP"){
            $data_im = "";
            $data_chk = explode(",",$data);
            $card_no  = $data_chk[2];
            $cmd = " select PER_ID,PER_CARDNO from PER_PERSONAL where  PER_CARDNO= $card_no ";
            $cnt=$db_dpis4->send_cmd($cmd);
            if($cnt>0){
                while($data_id = $db_dpis4->get_array()) {
                    $data_chk[2] = $data_id[PER_ID];
                    $data_im = implode(",",$data_chk);
                    insert2temp($data_im, ",");
                }
            }else{ //not found
                $data_chk[2] = $card_no;
                $data_im = implode(",",$data_chk);
                insert2temp($data_im, ",");
            }
        }else{
            insert2temp($data, ",");
        }

        }  // end while
    } // end if ($imptype)
    if ($tmp_row_num)
        $impfile = stripslashes($RealFile);
//		echo "impfile=".$impfile."<br>";

} // endif command==CONVERT

if ($command == "IMPORT") {   //	trim($RealFile)
	
	if($table == 'PER_SALARYHIS' && $insert_active==1){
			$cmd = "select * from IMPORT_TEMP where IMPORT_FILENAME='$form' AND ERROR !=2 order by ROW_NUM";
	}else{
			$cmd = "select * from IMPORT_TEMP where IMPORT_FILENAME='$form'  order by ROW_NUM";
	}
	 $cnt = $db_dpis->send_cmd($cmd);
    //$db_dpis->show_error();
	//echo "select cmd=$cmd ($cnt)<br>";
    if ($cnt > 0) {
        if ($running > -1) { // เป็นกรณี key เป็น running number
            $cmd1 = " select max($arr_fields[$running]) as MAX_ID from $table ";
            $db_dpis1->send_cmd($cmd1);
            $data1 = $db_dpis1->get_array();
            $MAX_ID = $data1[MAX_ID] + 1;
//				echo "max_id-$cmd<br>";
        }
        //http://dpis.ocsc.go.th/Service/node/2359

        //if($search_per_type == "3" && $table == "PER_SALARYHIS"){
        // if(1==0){
        //     $COST_OF_LIVING=0;
        //     while ($data = $db_dpis->get_array()) {
        //         if ($data[ERROR] != 1) {
        //             $dpack = $data[DATA_PACK];
        //             $data_fld = explode($delimeter, $dpack);
        //             $dataArr= explode('=',$data_fld[39]);//n=LIVING_EXPENSES=3000
        //             if($dataArr[1]=='LIVING_EXPENSES'){
        //                 $PER_IDArr= explode('=',$data_fld[1]);//n=PER_ID=16445
        //                 $PER_ID=$PER_IDArr[2];

        //                 $EXH_EFFECTIVEDATEArr= explode('=',$data_fld[2]);//SAH_EFFECTIVEDATE
        //                 $EXH_EFFECTIVEDATE="NULL";
        //                 if(!empty($EXH_EFFECTIVEDATEArr[2])){
        //                     $EXH_EFFECTIVEDATE="'".$EXH_EFFECTIVEDATEArr[2]."'";
        //                 }
        //                 $EX_CODE="014";//เงินเพิ่มการครองชีพชั่วคราวสำหรับพนักงานราชการ

        //                 $PER_CARDNOArr= explode('=',$data_fld[10]);//PER_CARDNO
        //                 $PER_CARDNO="'".$PER_CARDNOArr[2]."'";

        //                 $EXH_DOCNOArr= explode('=',$data_fld[5]);//SAH_DOCNO
        //                 $EXH_DOCNO="NULL";
        //                 if(!empty($EXH_EFFECTIVEDATEArr[2])){
        //                     $EXH_DOCNO="'".$EXH_DOCNOArr[2]."'";
        //                 }

        //                 $EXH_DOCDATEArr= explode('=',$data_fld[6]);//SAH_DOCDATE
        //                 $EXH_DOCDATE="NULL";
        //                 if(!empty($EXH_DOCDATEArr[2])){
        //                     $EXH_DOCDATE="'".$EXH_DOCDATEArr[2]."'";
        //                 }
        //                 $EXH_SALARY="NULL";

        //                 $EXH_REMARKArr= explode('=',$data_fld[15]);//SAH_REMARK
        //                 $EXH_REMARK="NULL";
        //                 if(!empty($EXH_REMARKArr[2])){
        //                     $EXH_REMARK="'".$EXH_REMARKArr[2]."'";
        //                 }

        //                 $EXH_ACTIVEArr= explode('=',$data_fld[26]);//SAH_LAST_SALARY
        //                 $EXH_ACTIVE=2;
        //                 if($EXH_ACTIVEArr[2]=="Y"){
        //                     $EXH_ACTIVE=1;
        //                 }


        //                 if(intval($dataArr[2]) <> 0 ){
        //                     $COST_OF_LIVING= intval($dataArr[2]);
        //                     $EXH_AMT=$COST_OF_LIVING;
        //                     $cmd = " select max(EXH_ID) as max_id from PER_EXTRAHIS ";
        //                     $db_dpis1->send_cmd($cmd);
        //                     $data1 = $db_dpis1->get_array();
        //                     $data1 = array_change_key_case($data1, CASE_LOWER);
        //                     $EXH_ID = $data1[max_id] + 1;

        //                     $SQL="insert into PER_EXTRAHIS (EXH_ID, PER_ID, EXH_EFFECTIVEDATE, EX_CODE, EXH_AMT, EXH_ENDDATE, PER_CARDNO, 
        //                                     UPDATE_USER, UPDATE_DATE, EXH_ORG_NAME, EXH_DOCNO, EXH_DOCDATE, EXH_SALARY, EXH_REMARK, EXH_ACTIVE)
        //                             values ($EXH_ID, $PER_ID, $EXH_EFFECTIVEDATE, '$EX_CODE', $EXH_AMT, NULL, $PER_CARDNO, 
        //                             $SESS_USERID, '$UPDATE_DATE', NULL, $EXH_DOCNO, $EXH_DOCDATE, $EXH_SALARY, $EXH_REMARK, $EXH_ACTIVE)";

        //                 }
        //             }
        //             /*echo '<pre>';
        //             echo $COST_OF_LIVING.'<br>';
        //             print_r($dataArr);
        //             print_r($data_fld);*/
        //         }
        //     }
        // }
        //End
        //die('<br>===========================');
        $cnt_insert = 0;
        while ($data = $db_dpis->get_array()) {
//				echo "ROW_NUM=".$data[ROW_NUM]."<br>";
            if ($data[ERROR] != 1) {
//					echo "$MAX_ID=MAX_ID<br>";
                $dpack = $data[DATA_PACK];
                $datain = $data[DATA_IN];
                $data_fld = explode($delimeter, $dpack);

                $data_in = explode("$", $datain);
//					echo "data_in=".implode("|",$data_in)."<br>";
                $var_d_in = (array) null;
                for ($i = 0; $i < count($data_in); $i++) {
//						echo "i=$i , data=[".$arr_temp[$i]."]<br>";
                    $var_a = str_replace(",", "", $data_in[$i]);
                    if (is_numeric($var_a))
                        $var_d_in[] = "var" . ($i + 1) . "=" . $var_a;
                    else
                        $var_d_in[] = "var" . ($i + 1) . "=" . $data_in[$i];
                }

                $key_pri = explode("|", $prime);
                $a_cond = (array) null;
                //echo "<pre>";
                //print_r($key_pri);
                for ($k = 0; $k < count($key_pri); $k++) {
                    $a_dat = explode("=", $data_fld[$key_pri[$k]]);
                    $a_cond[] = $a_dat[1] . "=" . ($a_dat[0] == "s" ? "'" . $a_dat[2] . "'" : $a_dat[2]);
                } // end for loop
                /*เดิม Begin*/
                /*$where = implode(" and ", $a_cond);
                $cmd = "select * from $table " . ($where ? " where " . $where : "");
                $cntdel = $db_dpis1->send_cmd($cmd);
                if ($cntdel && $DEL_FLAG) {
                    $cmd = "delete from $table " . ($where ? " where " . $where : "");
                    $db_dpis1->send_cmd($cmd);
                }*/
                /*เดิม End */
                // แปลงเป็น insert sql
                $a_colname = (array) null;
                $a_value = (array) null;
                for ($i = 0; $i < count($arr_fields); $i++) {
//						echo "column_map [$i]=".$column_map[$arr_fields[$i]]."<br>";
                    if ($column_map[$arr_fields[$i]]) {
                        $a_dat = (array) null;

                        if ($column_map[$arr_fields[$i]] == "running") {
                            $a_dat = explode("=", $data_fld[$i]);
                            $a_colname[] = $a_dat[1];
                            $a_value[] = $MAX_ID;
                        } else if ($column_map[$arr_fields[$i]] == "update_user") {
                            $a_dat = explode("=", $data_fld[$i]);
                            $a_colname[] = $a_dat[1];
                            $a_value[] = $SESS_USERID;
                        } else if ($column_map[$arr_fields[$i]] == "update_date") {
                            $a_dat = explode("=", $data_fld[$i]);
                            $a_colname[] = $a_dat[1];
                            $a_value[] = "'" . $UPDATE_DATE . "'";
                        } else {
                            $a_dat = explode("=", $data_fld[$i]);
                            if ($search_per_type == "1") { //ข้าราชการ
                                if (strtoupper($a_dat[1]) == "SAH_POS_NO" || strtoupper($a_dat[1]) == "SAH_PAY_NO") { //เลขที่ตำแหน่ง
                                    $a_colname[] = $a_dat[1];
                                    $a_value[] =  intval($a_dat[2]);
                                } else {
                                    $a_colname[] = $a_dat[1];
                                    $a_value[] = ($a_dat[0] == "s" ? "'" . $a_dat[2] . "'" : ($a_dat[2] == 'NULL' ? 'NULL' : (float) str_replace(",", "", $a_dat[2]))); // ถ้าเป็นตัวเลขและมี "," comma จะเอาออก
                                }
                            } else {
                                $a_colname[] = $a_dat[1];
                                $a_value[] = ($a_dat[0] == "s" ? "'" . $a_dat[2] . "'" : ($a_dat[2] == 'NULL' ? 'NULL' : (float) str_replace(",", "", $a_dat[2]))); // ถ้าเป็นตัวเลขและมี "," comma จะเอาออก
                            }

                            /* เดิม */
                            /* $a_dat = explode("=",$data_fld[$i]);
                              $a_colname[] = $a_dat[1];
                              $a_value[] = ($a_dat[0]=="s" ? "'".$a_dat[2]."'" : ($a_dat[2]=='NULL' ? 'NULL' :(float)str_replace(",","",$a_dat[2]))); // ถ้าเป็นตัวเลขและมี "," comma จะเอาออก */
                        } // end if ($data[ERROR]!=1)
                        //echo $a_colname[$i]."=".$a_value[$i]."<br>";
                    } // end if ($column_map[$i])
                } // end for loop
                if (function_exists('data_save_extend')) {
                    data_save_extend($data_fld, $var_d_in);
                }
                /* Release 5.1.0.4 Begin*/
                //[1] => PER_ID
                //[2] => SLIP_YEAR
                //[3] => SLIP_MONTH
               // echo "<pre>";
                //print_r($a_colname);
            if($table == "PER_TAXHIS"){// หน้างาน p1120 หักภาษี ณ ที่จ่าย by kong
                 $cmd = "select * from $table WHERE PER_ID=".$a_value[1]." AND TAX_YEAR=".$a_value[2]." AND TAX_DATE=".$a_value[3]." AND TAX_NUM=".$a_value[4]." AND PER_CARDNO =".$a_value[8]."  ";
                $cntdel = $db_dpis1->send_cmd($cmd);
             // echo "PER_TAXHIS_se=>".$cmd;

                if ($DEL_FLAG) {
                    $cmd = "delete from $table WHERE PER_ID=".$a_value[1]." AND TAX_YEAR=".$a_value[2]." AND TAX_DATE=".$a_value[3]." AND TAX_NUM=".$a_value[4]." AND PER_CARDNO =".$a_value[8]."  ";
                    $db_dpis1->send_cmd($cmd);
                  //  echo "PER_TAXHIS_de=>".$cmd;
                }


            }else if($table == "PER_SALARYHIS"){
                $cmd = "select * from $table WHERE PER_ID='".$a_value[1]."' AND SAH_EFFECTIVEDATE=".$a_value[2]." AND SAH_DOCNO=".$a_value[5]." AND SAH_DOCDATE=".$a_value[6]." ";
                $cntdel = $db_dpis1->send_cmd($cmd);
                if ($cntdel && $DEL_FLAG) {
                    $cmd = "delete from $table WHERE PER_ID='".$a_value[1]."' AND SAH_EFFECTIVEDATE=".$a_value[2]." AND SAH_DOCNO=".$a_value[5]." AND SAH_DOCDATE=".$a_value[6]." ";
                    $db_dpis1->send_cmd($cmd);
                }
            }else{
                $cmd = "select * from $table WHERE PER_ID='".$a_value[1]."' AND SLIP_YEAR=".$a_value[2]." AND SLIP_MONTH=".$a_value[3]." ";
                $cntdel = $db_dpis1->send_cmd($cmd);
                if ($cntdel && $DEL_FLAG) {
                    $cmd = "delete from $table WHERE PER_ID='".$a_value[1]."' AND SLIP_YEAR=".$a_value[2]." AND SLIP_MONTH=".$a_value[3]." ";
                    $db_dpis1->send_cmd($cmd);
                }

               }
                /* Release 5.1.0.4 End*/

                if($table == "PER_SALARYHIS" ){
                    if($search_per_type == "3" && $a_value[29]<>0){
                        $data_fld = explode($delimeter, $dpack);
                        $dataArr= explode('=',$data_fld[39]);

                        if($dataArr[1]=='LIVING_EXPENSES' && $search_per_type == "3" && $table == "PER_SALARYHIS"){
                            $PER_IDArr= explode('=',$data_fld[1]);//n=PER_ID=16445
                            $PER_ID=$PER_IDArr[2];

                            $EXH_EFFECTIVEDATEArr= explode('=',$data_fld[2]);//SAH_EFFECTIVEDATE
                            $EXH_EFFECTIVEDATE="NULL";
                            if(!empty($EXH_EFFECTIVEDATEArr[2])){
                                $EXH_EFFECTIVEDATE="'".$EXH_EFFECTIVEDATEArr[2]."'";
                            }
                            $EX_CODE="014";//เงินเพิ่มการครองชีพชั่วคราวสำหรับพนักงานราชการ

                            $PER_CARDNOArr= explode('=',$data_fld[10]);//PER_CARDNO
                            $PER_CARDNO="'".$PER_CARDNOArr[2]."'";

                            $EXH_DOCNOArr= explode('=',$data_fld[5]);//SAH_DOCNO
                            $EXH_DOCNO="NULL";
                            if(!empty($EXH_EFFECTIVEDATEArr[2])){
                                $EXH_DOCNO="'".$EXH_DOCNOArr[2]."'";
                            }

                            $EXH_DOCDATEArr= explode('=',$data_fld[6]);//SAH_DOCDATE
                            $EXH_DOCDATE="NULL";
                            if(!empty($EXH_DOCDATEArr[2])){
                                $EXH_DOCDATE="'".$EXH_DOCDATEArr[2]."'";
                            }
                            $EXH_SALARY="NULL";

                            $EXH_REMARKArr= explode('=',$data_fld[15]);//SAH_REMARK
                            $EXH_REMARK="NULL";
                            if(!empty($EXH_REMARKArr[2])){
                                $EXH_REMARK="'".$EXH_REMARKArr[2]."'";
                            }

                            $EXH_ACTIVEArr= explode('=',$data_fld[26]);//SAH_LAST_SALARY
                            $EXH_ACTIVE=1;
                            if($EXH_ACTIVEArr[2]=="Y"){
                                $EXH_ACTIVE=2;
                            }
                            if(intval($dataArr[2]) <> 0 ){
                                $COST_OF_LIVING= intval($dataArr[2]);
                                $EXH_AMT=$COST_OF_LIVING;
                                $cmd = " select max(EXH_ID) as max_id from PER_EXTRAHIS ";
                                $db_dpis1->send_cmd($cmd);
                                $data1 = $db_dpis1->get_array();
                                $data1 = array_change_key_case($data1, CASE_LOWER);
                                $EXH_ID = $data1[max_id] + 1;

                                $SQL="insert into PER_EXTRAHIS (EXH_ID, PER_ID, EXH_EFFECTIVEDATE, EX_CODE, EXH_AMT, EXH_ENDDATE, PER_CARDNO, 
                                                UPDATE_USER, UPDATE_DATE, EXH_ORG_NAME, EXH_DOCNO, EXH_DOCDATE, EXH_SALARY, EXH_REMARK, EXH_ACTIVE)
                                        values ($EXH_ID, $PER_ID, $EXH_EFFECTIVEDATE, '$EX_CODE', $EXH_AMT, NULL, $PER_CARDNO, 
                                        $SESS_USERID, '785475', NULL, $EXH_DOCNO, $EXH_DOCDATE, $EXH_SALARY, $EXH_REMARK, $EXH_ACTIVE)";
                                      //  echo "<pre>".$SQL;
                                $db_dpis1->send_cmd($SQL);
                            }
                        }
                    }else{
                        unset($a_colname[29]);
                        unset($a_value[29]);
                    }
                    unset($a_colname[29]);
                    unset($a_value[29]);

                    $cmd = " insert into $table (" . implode(",", $a_colname) . ") values (" . implode(",", $a_value) . ")";
                       $db_dpis1->send_cmd($cmd);
                    if($locat_work=="Y"){
                        insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 >  P1115 นำเข้าข้อมูลการเลื่อนเงินเดือน > ระบบหาสังกัดจากเลขที่ตำแหน่งของบุคคลไปบันทึกให้อัตโนมัติ [ ".$UPDATE_DATE." ] ");
                    }else{
                        insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 >  P1115 นำเข้าข้อมูลการเลื่อนเงินเดือน > ระบบนำเข้าข้อมูลสังกัดจากไฟล์ Excel[ ".$UPDATE_DATE." ] ");
                    }

                }else{
                    $cmd = " insert into $table (" . implode(",", $a_colname) . ") values (" . implode(",", $a_value) . ")";
                    $db_dpis1->send_cmd($cmd);
                }

           //  echo "<pre>". $cmd;
//               var_dump($a_colname);
//               var_dump($a_value) ;





                /*
                $data_fld = explode($delimeter, $dpack);
                $dataArr= explode('=',$data_fld[39]);
                if($dataArr[1]=='LIVING_EXPENSES' && $search_per_type == "3" && $table == "PER_SALARYHIS"){
                    $PER_IDArr= explode('=',$data_fld[1]);//n=PER_ID=16445
                    $PER_ID=$PER_IDArr[2];

                    $EXH_EFFECTIVEDATEArr= explode('=',$data_fld[2]);//SAH_EFFECTIVEDATE
                    $EXH_EFFECTIVEDATE="NULL";
                    if(!empty($EXH_EFFECTIVEDATEArr[2])){
                        $EXH_EFFECTIVEDATE="'".$EXH_EFFECTIVEDATEArr[2]."'";
                    }
                    $EX_CODE="014";//เงินเพิ่มการครองชีพชั่วคราวสำหรับพนักงานราชการ

                    $PER_CARDNOArr= explode('=',$data_fld[10]);//PER_CARDNO
                    $PER_CARDNO="'".$PER_CARDNOArr[2]."'";

                    $EXH_DOCNOArr= explode('=',$data_fld[5]);//SAH_DOCNO
                    $EXH_DOCNO="NULL";
                    if(!empty($EXH_EFFECTIVEDATEArr[2])){
                        $EXH_DOCNO="'".$EXH_DOCNOArr[2]."'";
                    }

                    $EXH_DOCDATEArr= explode('=',$data_fld[6]);//SAH_DOCDATE
                    $EXH_DOCDATE="NULL";
                    if(!empty($EXH_DOCDATEArr[2])){
                        $EXH_DOCDATE="'".$EXH_DOCDATEArr[2]."'";
                    }
                    $EXH_SALARY="NULL";

                    $EXH_REMARKArr= explode('=',$data_fld[15]);//SAH_REMARK
                    $EXH_REMARK="NULL";
                    if(!empty($EXH_REMARKArr[2])){
                        $EXH_REMARK="'".$EXH_REMARKArr[2]."'";
                    }

                    $EXH_ACTIVEArr= explode('=',$data_fld[26]);//SAH_LAST_SALARY
                    $EXH_ACTIVE=1;
                    if($EXH_ACTIVEArr[2]=="Y"){
                        $EXH_ACTIVE=2;
                    }

                    if(intval($dataArr[2]) <> 0 ){
                        $COST_OF_LIVING= intval($dataArr[2]);
                        $EXH_AMT=$COST_OF_LIVING;
                        $cmd = " select max(EXH_ID) as max_id from PER_EXTRAHIS ";
                        $db_dpis1->send_cmd($cmd);
                        $data1 = $db_dpis1->get_array();
                        $data1 = array_change_key_case($data1, CASE_LOWER);
                        $EXH_ID = $data1[max_id] + 1;

                        $SQL="insert into PER_EXTRAHIS (EXH_ID, PER_ID, EXH_EFFECTIVEDATE, EX_CODE, EXH_AMT, EXH_ENDDATE, PER_CARDNO,
                                        UPDATE_USER, UPDATE_DATE, EXH_ORG_NAME, EXH_DOCNO, EXH_DOCDATE, EXH_SALARY, EXH_REMARK, EXH_ACTIVE)
                                values ($EXH_ID, $PER_ID, $EXH_EFFECTIVEDATE, '$EX_CODE', $EXH_AMT, NULL, $PER_CARDNO,
                                $SESS_USERID, '$UPDATE_DATE', NULL, $EXH_DOCNO, $EXH_DOCDATE, $EXH_SALARY, $EXH_REMARK, $EXH_ACTIVE)";
                        echo $SQL;
                        $db_dpis1->send_cmd($SQL);
                    }
                }else{

                }*/



               if($debug==1){echo '<pre>'. $cmd . "<br>";}
                $MAX_ID++;
                $cnt_insert++;
            } // end if ($data[ERROR]!=1)
        } // end loop while
        $cnt_import = 0;
        if ($cnt == $cnt_insert) { // 	ถ้า import ครบทุกรายการ ให้ delete temp data
            $cmd = "delete from IMPORT_TEMP where IMPORT_FILENAME='$form'";
            $db_dpis->send_cmd($cmd);
            $cnt_import = $cnt_insert;
        }
    } // end if ($cnt > 0)


} // endif command==IMPORT

if ($command == "SAVE" && $ROW_NUM) {
//		echo "SAVE imptext=$imptext   data_in=$data_in   running_id=$running_id<br>";
    $data_in = explode($DIVIDE_TEXTFILE, $imptext);
    $tmp_row_num = $ROW_NUM;
    insert2temp(implode(",", $data_in), ",");
}

if ($command == "DELETE" && $ROW_NUM) {
    $cmd = "delete from IMPORT_TEMP where IMPORT_FILENAME='$form' and ROW_NUM=$ROW_NUM";
    $db_dpis->send_cmd($cmd);
//		echo "delete....$cmd<br>";
    $imptext = "";
    $updtext = "";
}
function insert2temp($data_in, $a_delimeter) {
    global $db_dpis, $db_dpis1, $db_dpis2;
    global $tmp_row_num, $running, $MAX_ID, $running_id, $PER_STATUS;
    global $column_map, $form, $delimeter, $DIVIDE_TEXTFILE;
    global $SESS_USERID, $UPDATE_DATE;
    global $UPDATE_USER;
    global $arr_fields, $arr_fields_type, $imptype, $command, $textcols;
    global $imptext, $updtext;

//		echo "data_in=$data_in ,  tmp_row_num=$tmp_row_num<br>";
    /* map data */
    $a_column = (array) null;
    $a_data = (array) null;
    $a_type = (array) null;
    $a_text = (array) null;
    $a_text1 = (array) null;
    $arr_temp = explode($a_delimeter, $data_in);
    $f_data = 0;
    $var_d_in = (array) null;
    for ($i = 0; $i < count($arr_temp); $i++) {
//						echo "i=$i , data=[".$arr_temp[$i]."]<br>";
        $arr_temp[$i] = trim($arr_temp[$i]);
         $arr_temp[$i];
        if ($arr_temp[$i] == "-")
            $arr_temp[$i] = 0; // แปลงค่า - เป็น 9
        if (trim($arr_temp[$i])) {
            $f_data = 1;
        }
        $var_a = str_replace(",", "", $arr_temp[$i]);
        if (is_numeric($var_a))
            $var_d_in[] = "var" . ($i + 1) . "=" . $var_a;
        else
            $var_d_in[] = "var" . ($i + 1) . "=" . $arr_temp[$i];
    }
    if ($f_data) { // มีข้อมูล
        if (!$textcols)
            $textcols = count($arr_temp);
//						echo "1..textcols=$textcols<br>";
        $imp_data = str_replace("'", "", implode($DIVIDE_TEXTFILE, $arr_temp));
					//	echo "imp_data=".$imp_data."<br>";
        $err = 0;
       if($PER_STATUS==2){
           $err = 2;
       }
        $arr_name_data_change = (array) null;
        $arr_val_olddata = (array) null;
        for ($i = 0; $i < count($arr_fields); $i++) {
//							echo "column_map [$i($arr_fields[$i])]=".$column_map[$arr_fields[$i]]."<br>";
//							if ($column_map[$i]) {
            $a_dat = "";
            if (substr($column_map[$arr_fields[$i]], 0, 3) == "sql") {
                // หาชื่อข้อมูลที่ select เข้ามา
                $f_nodatainwhere = 0;
                $data_type = substr($column_map[$arr_fields[$i]], 4, 1);
                $field_type = substr($column_map[$arr_fields[$i]], 6, 1);
                $buffsql = substr($column_map[$arr_fields[$i]], 8);
                $sql = explode("^", $buffsql);
                $c = strpos($sql[0], "from");
                $colname = trim(substr($sql[0], 7, $c - 7));
                // จบการหาชื่อข้อมูลที่ select เข้ามา
                $c1 = strpos($sql[0], "@");
			//	e					echo "sql[0]=".$sql[0]." ($c1) sql[1]=".$sql[1]."<br>";
                while ($c1 !== false) {
                    $kk = 1;
                    while (ctype_digit(substr($sql[0], $c1 + 1, $kk)) && $kk < strlen($sql[0]))
                        $kk++;
//										echo "c1=$c1 , kk=$kk<br>";
                    $data_col_idx = (int) substr($sql[0], $c1 + 1, $kk - 1);
                    $data_col_sym = "@" . $data_col_idx;
//										echo "imptype=$imptype<br>";
                    if (trim($arr_temp[$data_col_idx - 1])) {  // ถ้าค่าตัวแปร (@n) มีค่า
                        if ($imptype == "xls")
                            $val = ($data_type == "s" ? "'" . $arr_temp[$data_col_idx - 1] . "'" : $arr_temp[$data_col_idx - 1]);
                        else
                            $val = $arr_temp[$data_col_idx - 1];
                        $sql[0] = str_replace($data_col_sym, $val, $sql[0]);
                        $c1 = strpos($sql[0], "@", $c1 + 1);
                    } else { // ถ้าค่าตัวแปร (@n) ไม่มีค่า
                        $f_nodatainwhere = 1;
                        break;
                    }
                }
//									echo "sql=$sql (colname=$colname) (val=$val)<br>";
                if ($f_nodatainwhere) { // ถ้ามีข้อมูลที่ตัวแปร @n ไม่มีค่า
                    if (trim($sql[1]) == "NOTNULL") {
                        $a_dat = "** ข้อมูลที่จะใช้ค้นหาไม่มีค่า และ column เป็น NOTNULL**";
                        $a_typ = $data_type;
                        $err = 1;
                    } else {
                        $a_dat = "";
                        $a_typ = $data_type;
                    }
                } else if ($sql[0]) {
                    $db_dpis1->send_cmd($sql[0]);
//										echo "sql=".$sql[0]."<br>";
                    $data1 = $db_dpis1->get_array();
//										echo "data1=".$data1."<br>";
                    if ($data_type == "s"){
                        $a_dat = trim($data1[$colname]);
                    }else{
                        $a_dat = trim($data1[$colname]);
                    }
                    $a_typ = $data_type;
									//	echo "colname=$colname , a_dat=$a_dat , a_typ=$a_typ<br>";
                    if (!str_replace("'", "", $a_dat)) {
                        $a_dat = "**ไม่พบ $colname จากค่า $val**";
                        $err = 1;
                    }


//										echo "sql=".$sql[0]." , a_dat=$a_dat<br>";
                } else {
                    echo "***** ไม่พบประโยค SQL *****<br>";
                    exit;
                }
//									echo "1..field [$i]=".$arr_fields[$i].", data [".$column_map[$i]."]".$arr_temp[$column_map[$i]]." ($a_dat)<br>";
            } else if (substr($column_map[$arr_fields[$i]], 0, 4) == "fmla") {
                // หาประโยค formula ก่อน
                $data_type = substr($column_map[$arr_fields[$i]], 5, 1);
                $field_type = substr($column_map[$arr_fields[$i]], 7, 1);
                $formula = substr($column_map[$arr_fields[$i]], 9);
                $a_val = (array) null;
                $c1 = strpos($formula, "@");
//									echo "formula=".$formula." ($c1)<br>";
                while ($c1 !== false) {
                    $kk = 1;
                    while (ctype_digit(substr($formula, $c1 + 1, $kk)) && $kk < strlen($formula))
                        $kk++;
//										echo "c1=$c1 , kk=$kk<br>";
                    $arg = (int) substr($formula, $c1 + 1, $kk - 1);
                    $a_val[$arg] = $arr_temp[$arg - 1];
//										echo "@".$arg."==>".$arr_temp[$arg-1]."<br>";
                    $c1 = strpos($formula, "@", $c1 + 1);
                }
//									echo  "** i=$i , formula=$formula , a_val=".implode(",",$a_val)."<br>";
                $a_dat = compute_formula($formula, $a_val);
                $a_typ = "s";
//									echo "2..field [$i]=".$arr_fields[$i].", data [".$column_map[$i]."]".$arr_temp[$column_map[$i]]." ($a_dat)<br>";
            } else if (substr($column_map[$arr_fields[$i]], 0, 4) == "func") {
                // หาประโยค function ก่อน
                $data_type = substr($column_map[$arr_fields[$i]], 5, 1);
                $field_type = substr($column_map[$arr_fields[$i]], 7, 1);
                $function = substr($column_map[$arr_fields[$i]], 9);
                $c = strpos($function, "(");
                $function_name = substr($function, 0, $c);
                $argu = substr($function, $c + 1);
                $argu = substr($argu, 0, strlen($argu) - 1);
//									echo "function_name=$function_name , argu=$argu  (".strlen($argu).")<br>";
                $arr_argu = explode(",", $argu);
                $a_val = (array) null;
                for ($kk = 0; $kk < count($arr_argu); $kk++) {
//										echo "$kk-".$arr_argu[$kk]."<br>";
                    if (substr($arr_argu[$kk], 0, 1) == "@") {
                        $varmap = substr($arr_argu[$kk], 1);
                        if (ctype_digit($varmap)) {
                            $arg = (int) $varmap;
                            $a_val[] = $arr_temp[$arg - 1];
                        } else {
                            echo "รูปแบบการส่งค่าเข้า function ไม่ถูกต้อง โปรดตรวจสอบ...";
                            exit;
                        }
                    } else {
                        $a_val[] = $arr_argu[$kk]; // ค่าตรง ๆ ที่ส่วเข้ามา
                    }
                }
//									echo  "** i=$i , function=$function , a_val=".implode(",",$a_val)."<br>";
                $a_dat = call_user_func_array($function_name, $a_val);
                $not_data = explode("^", $a_dat);
                if (trim($not_data[1]) == "NOTNULL") {
                    $err = 1;
                    $a_dat = $not_data[0];
                }
                $a_typ = "s";
//									echo "3..field [$i]=".$arr_fields[$i].", data [".$column_map[$i]."]".$arr_temp[$column_map[$i]]." ($a_dat)<br>";
            } else if (substr($column_map[$arr_fields[$i]], 0, 6) == "screen") {
                $data_type = substr($column_map[$arr_fields[$i]], 7, 1);
                $field_type = substr($column_map[$arr_fields[$i]], 9, 1);
                $ppart = explode("|", substr($column_map[$arr_fields[$i]], 11));
                global $$ppart[0];
                $arr_name_data_change[] = $ppart[0];
                $arr_val_olddata[] = $$ppart[0];
//									echo "...ppart [0]=>".$ppart[0].">>".$$ppart[0].">>function [1]>>".$ppart[1]."<br>";
                if ($ppart[1]) { // function
                    $arr = (array) null;
                    $arr[] = $$ppart[0]; // ค่า parameter ตัวที่ 2 ต้องเป็น array
                    $a_dat = call_user_func_array($ppart[1], $arr);
                } else {
                    $a_dat = "'" . $$ppart[0] . "'";
                }
//									echo "++++ a_dat=$a_dat<br>";
                $a_typ = $data_type;
            } else if (substr($column_map[$arr_fields[$i]], 0, 3) == "php") {
                $data_type = substr($column_map[$arr_fields[$i]], 4, 1);
                $field_type = substr($column_map[$arr_fields[$i]], 6, 1);
                $ppart = explode("|", substr($column_map[$arr_fields[$i]], 8));
                global $$ppart[0];
                $arr_name_data_change[] = $ppart[0];
                $arr_val_olddata[] = $$ppart[0];
						//			echo "ppart>".$ppart[0].">>".$$ppart[0]."<br>";
                if ($ppart[1]) { // function
                    $arr = (array) null;
                    $arr[] = $$ppart[0]; // ค่า parameter ตัวที่ 2 ต้องเป็น array
                    $a_dat = call_user_func_array($ppart[1], $arr);
                } else {
                    $a_dat = "'" . $$ppart[0] . "'";
                }
                $a_typ = $data_type;
            } else if ($column_map[$arr_fields[$i]] == "running") {
                $a_dat = ($command == "SAVE" ? $running_id : $MAX_ID);
                $a_typ = "n";
//									echo "4..field [$i]=".$arr_fields[$i].", data [".$column_map[$i]."]".$arr_temp[$column_map[$i]]." ($a_dat)<br>";
            } else if ($column_map[$arr_fields[$i]] == "update_user") {
                $a_dat = $SESS_USERID;
                $a_typ = "n";
//									echo "5..field [$i]=".$arr_fields[$i].", data [".$column_map[$arr_fields[$i]]."]".$arr_temp[$column_map[$arr_fields[$i]]]." ($a_dat)<br>";
            } else if ($column_map[$arr_fields[$i]] == "update_date") {
                $a_dat = $UPDATE_DATE;
                $a_typ = "s";
//									echo "6..field [$i]=".$arr_fields[$i].", data [".$column_map[$arr_fields[$i]]."]".$arr_temp[$column_map[$arr_fields[$i]]]." ($a_dat)<br>";
            } else {
                if ($column_map[$arr_fields[$i]]) {
//										echo "***** i=$i , column_map=".$column_map[$i]." , arr_temp=".$arr_temp[$column_map[$i]-1]."<br>";
                    $ppart = explode("|", $column_map[$arr_fields[$i]]);
//										echo "***(".$ppart[0].")***".$arr_temp[$ppart[0]-1]."<br>";
                    if ($ppart[1]) { // function
                        $arr = (array) null;
                        $arr[] = $arr_temp[$ppart[0] - 1]; // ค่า parameter ตัวที่ 2 ต้องเป็น array
                        $a_dat = call_user_func_array($ppart[1], $arr);
                    } else {
                        if ($ppart[0]) {
//												$a_dat  = "'".$$ppart[0]."'";
                            $a_dat = $arr_temp[$ppart[0] - 1];
                        }
                    }
                    if (strpos($a_dat, "'") > -1)
                        $a_typ = "s";
                    else if (is_numeric($a_dat))
                        $a_typ = "n";
                    else
                        $a_typ = "s";
//										echo "7..field [$i]=".$arr_fields[$i].", data [".($ppart[0]-1)."]".$arr_temp[$ppart[0]-1]."  ppart[1]=".$ppart[1]."<br>";
                } else {
                    $a_dat = "";
                }
            }
//								echo "field [$i]=".$arr_fields[$i].", type=".$arr_fields_type[$arr_fields[$i]]." ,  data [".$column_map[$i]."]".$arr_temp[$column_map[$i]]."<br>";
//								$a_type[] = $a_typ = (strpos($arr_fields_type[$arr_fields[$i]],"CHAR")!==false ? "s" : $a_typ);
            $a_type[] = $a_typ = (strpos($arr_fields_type[$arr_fields[$i]], "CHAR") !== false ? "s" : "n");
            if ($a_typ == "n")
                $a_dat = str_replace(",", "", $a_dat); // ตัวเลขเอา comma ออก
//								echo "a_typ=$a_typ , a_dat=$a_dat<br>";
            $a_data[] = trim($a_dat);
            $a_column[] = $arr_fields[$i];
            $a_text[] = $arr_fields[$i] . "=" . trim($a_dat);
            $a_text1[] = $a_typ . "=" . $arr_fields[$i] . "=" . trim(str_replace("'", "", $a_dat));
//							} // end if ($column_map[$arr_fields[$i]])

        } // end loop for column_map

        if (function_exists('data_setting_extend') && $command != "SAVE") { // จะทำการแปลงข้อมูลพิเศษ ต่อเมื่อไช่ SAVE
            $a_text1 = data_setting_extend($a_text1, $var_d_in);
        } // end if (file_exist("import_forms/".$scrname))

        $s_data = implode(",", $a_data);
        $s_column = implode(",", $a_column);
        $s_text = implode($delimeter, $a_text1);
        if ($command == "SAVE")
            $cmd = "update IMPORT_TEMP set DATA_PACK='$s_text', ERROR=$err, DATA_IN='$imp_data' where IMPORT_FILENAME='$form' and ROW_NUM=$tmp_row_num";
        else
            $cmd = "insert into IMPORT_TEMP (IMPORT_FILENAME, ROW_NUM, DATA_PACK, ERROR, DATA_IN) values ('$form', $tmp_row_num, '$s_text', $err, '$imp_data')";
           // echo "<pre>[$cmd]<br>";
        ini_set('error_reporting', 0);
        $db_dpis->send_cmd($cmd);
        if($debug==1){echo '<pre>'. $cmd . "<br>";}
//						echo "********************".($command=='SAVE' ? "update" : "insert")."-$cmd<br>";
        $tmp_row_num++;
        if ($running > -1)
            $MAX_ID++;
        if ($command == "SAVE") {
            $updtext = $s_text;
            $imptext = $imp_data;
//							echo "SAVE... updtext=$updtext   ::   imptext=$imptext<br>";
        }
        // กำหนดค่าบางค่าที่ถูกเปลี่ยนไปกลับคืน
        if ($arr_name_data_change) {
            for ($k = 0; $k < count($arr_name_data_change); $k++) {
                $$arr_name_data_change[$k] = $arr_val_olddata[$k]; // คืนค่าบน screen กับ php
            }
        }
    } // end if (!$f_nodata)
}

// end function 
?>