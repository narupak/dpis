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
                while(! feof($file)){
                    $dataArr = fgetcsv($file);
                    $CARD_ID = trim($dataArr[0]);
					$IN = trim($dataArr[5]);
					$OUT = trim($dataArr[6]);
					$Total_All++;
                    if(!empty($CARD_ID) && ($CARD_ID !=-1) && (!empty($IN) || !empty($OUT))){
                        
                                                   
						$sql="SELECT PER_ID FROM TA_REF_TIMEATTENDACE WHERE CARD_ID='".$CARD_ID."'";
						$db_dpis2->send_cmd($sql);
						$data2 = $db_dpis2->get_array();
						if($data2[PER_ID]>0){

							$PER_ID = $data2[PER_ID];
							$RECORD_BY=2;
							
							//5 = FA หน้า , 3= CA บัตร , 0 = FP นิ้วมือ
							$AUTHTYPE_CODE=0;
							
							//เวลาเข้า
							if(!empty($IN)){
								$DATE_ARR = explode('/', trim($dataArr[2]));
                        		$DATE_CONDITION = $DATE_ARR[2].'-'.$DATE_ARR[1].'-'.$DATE_ARR[0].' '.substr($IN,0,2).":".substr($IN,2,2).":00";
								$sql="INSERT INTO PER_TIME_ATTENDANCE (PER_ID,TIME_STAMP,TA_CODE,AUTHTYPE,ADJUST_USER,ADJUST_DATE,RECORD_BY) 
										VALUES(".$PER_ID.",TO_DATE('".$DATE_CONDITION."','YYYY-MM-DD hh24:mi:ss'),'1001',".$AUTHTYPE_CODE.",".$SESS_USERID.",SYSDATE,$RECORD_BY)";
								$db_dpis1->send_cmd($sql);
								//echo "<pre>".$sql."<br/>";
							}
							
							//เวลาออก
							if(!empty($OUT)){
								$DATE_ARR = explode('/', trim($dataArr[2]));
                        		$DATE_CONDITION = $DATE_ARR[2].'-'.$DATE_ARR[1].'-'.$DATE_ARR[0].' '.substr($OUT,0,2).":".substr($OUT,2,2).":00";
								$sql="INSERT INTO PER_TIME_ATTENDANCE (PER_ID,TIME_STAMP,TA_CODE,AUTHTYPE,ADJUST_USER,ADJUST_DATE,RECORD_BY) 
										VALUES(".$PER_ID.",TO_DATE('".$DATE_CONDITION."','YYYY-MM-DD hh24:mi:ss'),'1001',".$AUTHTYPE_CODE.",".$SESS_USERID.",SYSDATE,$RECORD_BY)";
								$db_dpis1->send_cmd($sql);
								
							}
							
							
							$Total_Success++;
							
							
							
						}
					}else{
						$Total_Dupicate++;
					}
                }
                
                fclose($file);
                
                $excel_msg = "<font color='green'>The file " . basename($_FILES["TEXT_FILE"]["name"]) . " has been uploaded. <br>
                            จำนวนรายการทั้งหมดที่มีเวลาสแกนเข้า-ออก ".$Total_All." รายการ<br>
                            นำเข้าสำเร็จ ".$Total_Success." รายการ (นับเวลาเข้า 1 รายการ, นับเวลาออก 1 รายการ)</font> <br>
                            <font color='red'>ข้อมูลไม่สามารถนำเข้าได้ ".$Total_Dupicate." รายการ 
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
