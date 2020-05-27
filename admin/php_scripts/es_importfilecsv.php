<?php
    include("php_scripts/session_start.php");
    include("php_scripts/function_repgen.php");
    include("../php_scripts/connect_file.php");
    ini_set('error_reporting', 0); //ปิด warning
    
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["TEXT_FILE"]["name"]);
    $RealFile = $target_file;
    $excel_msg="";
    
    $db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    
    $Total_All=0;
    $Total_Success=0;
    $Total_Dupicate=0;
    if($command=="UPLOADCSV"){
        $iEXTENSION = pathinfo($target_file, PATHINFO_EXTENSION);
        if(strtoupper($iEXTENSION) =="CSV"){
            if (move_uploaded_file($_FILES["TEXT_FILE"]["tmp_name"], $target_file)) {
                $file = fopen($target_file,"r");
                $TA_CODE = $select_TA_CODE;
                while(! feof($file)){
                    $dataArr = fgetcsv($file);
                    $PER_ID = $dataArr[0];
                    if(!empty($PER_ID)){
                        $Total_All++;
                        $DATE_ARR = explode('.', $dataArr[1]);
                        $DATE_CONDITION = $DATE_ARR[2].'-'.$DATE_ARR[1].'-'.$DATE_ARR[0].' '.$dataArr[2];
                        $AUTHTYPE = strtoupper(substr($dataArr[4],0,2));//5 = FA หน้า , 3= CA บัตร , 0 = FP นิ้วมือ
                        if($AUTHTYPE=="FA"){
                            $AUTHTYPE_CODE=5;
                                            }else if($AUTHTYPE=="CA"){
                            $AUTHTYPE_CODE=3;
                        }else{
                            $AUTHTYPE_CODE=0;
                        }    

                        if($PER_ID==-1){ //ลงเวลาไม่สำเร็จ
                            $sql="SELECT COUNT(*) AS CNT FROM TA_UNKNOWN_PIC WHERE TA_CODE='".$TA_CODE."' AND TIME_STAMP=TO_DATE('".$DATE_CONDITION."','DD-MM-YYYY hh24:mi:ss')  ";
                            $db_dpis2->send_cmd($sql);
                            $data2 = $db_dpis2->get_array();
                            if($data2[CNT]==0){
                                $sql="INSERT INTO TA_UNKNOWN_PIC (TIME_STAMP,TA_CODE,AUTHTYPE) VALUES(TO_DATE('".$DATE_CONDITION."','DD-MM-YYYY hh24:mi:ss'),'".$TA_CODE."',".$AUTHTYPE_CODE.")";
                                $db_dpis2->send_cmd($sql);
                                $Total_Success++;
                            }else{
                                $Total_Dupicate++;
                            }
                        }else{ //เวลาการมาปฏิบัติราชการจากเครื่องบันทึกเวลา
                            $sql=" SELECT COUNT(*) AS CNT FROM PER_TIME_ATTENDANCE WHERE PER_ID=".$PER_ID." AND TIME_STAMP=TO_DATE('".$DATE_CONDITION."','DD-MM-YYYY hh24:mi:ss') AND TA_CODE = '".$TA_CODE."' ";
                            $db_dpis2->send_cmd($sql);
                            $data2 = $db_dpis2->get_array();
                            if($data2[CNT]==0){
                                                            $RECORD_BY=2;
                                $sql="INSERT INTO PER_TIME_ATTENDANCE (PER_ID,TIME_STAMP,TA_CODE,AUTHTYPE,ADJUST_USER,ADJUST_DATE,RECORD_BY) 
                                    VALUES(".$PER_ID.",TO_DATE('".$DATE_CONDITION."','DD-MM-YYYY hh24:mi:ss'),'".$TA_CODE."',".$AUTHTYPE_CODE.",".$SESS_USERID.",SYSDATE,$RECORD_BY)";
                                $db_dpis2->send_cmd($sql);
                                $Total_Success++;
                            }else{
                                 $Total_Dupicate++;
                            }
                        }
                    }
                }
                
                fclose($file);
                
                $excel_msg = "<font color='green'>The file " . basename($_FILES["TEXT_FILE"]["name"]) . " has been uploaded. <br>
                            จำนวนรายการทั้งหมด ".$Total_All." รายการ<br>
                            นำเข้าสำเร็จ ".$Total_Success." รายการ <br>
                            พบว่าเป็นรายการซ้ำ ".$Total_Dupicate." รายการ (ไม่นำเข้าฐานข้อมูล)
                            </font>";
            } else {
                $excel_msg = "<font color='red'>Sorry, there was an error uploading your file.</font>";
            }
            if (file_exists($target_file)) {
                unlink($target_file);
            }
        }
        $command="";
    }
?>
