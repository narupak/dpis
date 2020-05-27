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
	$Total_NoPersonal=0;
    $Total_Dupicate=0;
    if($command=="UPLOADCSV"){
        $iEXTENSION = pathinfo($target_file, PATHINFO_EXTENSION);
        if(strtoupper($iEXTENSION) =="CSV"){
            if (move_uploaded_file($_FILES["TEXT_FILE"]["tmp_name"], $target_file)) {
                $file = fopen($target_file,"r");
                while(! feof($file)){
                    $dataArr = fgetcsv($file);
                    $CARD_ID = trim($dataArr[0]);
                    if(!empty($CARD_ID)){
                        $Total_All++;
                        $FS_NAME = trim($dataArr[1]);
                           
						$sql="SELECT COUNT(*) AS CNT FROM TA_REF_TIMEATTENDACE WHERE CARD_ID=".$CARD_ID;
						$db_dpis2->send_cmd($sql);
						$data2 = $db_dpis2->get_array();
						if($data2[CNT]==0){
							
							$cmd = " select PER_ID from PER_PERSONAL where PER_STATUS !=2 and trim(PER_NAME)||' '||trim(PER_SURNAME) like '%$FS_NAME%' ";
							$db_dpis2->send_cmd($cmd);
							$data = $db_dpis2->get_array();
							if($data[PER_ID]){
								$PER_ID = $data[PER_ID];
							}else{
								$PER_ID = -1;
								$Total_NoPersonal++;
							}
							
							$sql="INSERT INTO TA_REF_TIMEATTENDACE (CARD_ID,PER_ID,FS_NAME) VALUES('".$CARD_ID."',".$PER_ID.",'".$FS_NAME."')";
							$db_dpis1->send_cmd($sql);
							$Total_Success++;
						}else{
							$Total_Dupicate++;
						}
                    }
                }
                
                fclose($file);
                
                $excel_msg = "<font color='green'>The file " . basename($_FILES["TEXT_FILE"]["name"]) . " has been uploaded. <br>
                            จำนวนรายการทั้งหมด ".$Total_All." รายการ<br>
                            นำเข้าสำเร็จ ".$Total_Success." รายการ</font> <br>
							<font color='orange'>ข้อมูลไม่สามารถเชื่อมโยงในฐานข้อมูลบุคคล ".$Total_NoPersonal." รายการ</font> <br>
                            <font color='red'>พบว่าเป็นรายการซ้ำ ".$Total_Dupicate." รายการ (ไม่นำเข้าฐานข้อมูล)
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
