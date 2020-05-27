<?php
$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$db_dpis_Pic = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$db_dpis_PicDef = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

/*1.ตรวจหาข้อมูลที่เป็น รูปประจำตัว ที่ pic_show is null */
    $cmd =" SELECT * 
            FROM PER_PERSONALPIC 
            WHERE per_picpath LIKE '%attachment/pic_personal%' AND pic_show is null";
    $cntPicPer = $db_dpis->send_cmd($cmd);
    if(!empty($cntPicPer)){
        /*1.1 ถ้าพบให้ทำการ update pic_show=0*/
        $cmd =" UPDATE PER_PERSONALPIC 
                SET pic_show=0,pic_sign=0 
                WHERE per_picpath LIKE '%attachment/pic_personal%' AND pic_show is null ";
        $db_dpis->send_cmd($cmd);
        /*1.2 และ update รายการรูปลำดับล่าสุดให้ pic_show=1*/
        $cmd =" SELECT PER_ID ,SUM(1) AS CNT
                FROM PER_PERSONALPIC 
                WHERE per_picpath LIKE '%attachment/pic_personal%' 
                GROUP BY PER_ID 
                HAVING COUNT(per_id) > 1"; //per_picseq
        $db_dpis->send_cmd($cmd);
        while($data = $db_dpis->get_array()){
            $dbPER_ID = $data[PER_ID];
            $cmd = "SELECT MAX(PER_PICSEQ) AS PER_PICSEQ
                    FROM PER_PERSONALPIC 
                    WHERE per_id=".$dbPER_ID." and per_picpath like '%attachment/pic_personal%'";
            $db_dpis_Pic->send_cmd($cmd);
            while($dataPic = $db_dpis_Pic->get_array()){
                $dbPER_PICSEQ=$dataPic[PER_PICSEQ];
                $cmd ="UPDATE PER_PERSONALPIC SET pic_show=1 where PER_ID=".$dbPER_ID." AND PER_PICSEQ=".$dbPER_PICSEQ;
                $db_dpis_PicDef->send_cmd($cmd);
            }
        }
    }

/*2.ตรวจสอบข้อมูลที่เป็น ภาพลายเซน */
    $cmd =" SELECT * 
            FROM PER_PERSONALPIC 
            WHERE per_picpath LIKE '%/PER_SIGN/%' AND pic_show is null";
    $cntPicSIGN = $db_dpis->send_cmd($cmd);
    if(!empty($cntPicSIGN)){
        $cmd =" UPDATE PER_PERSONALPIC 
                SET pic_show=0,pic_sign=1 
                WHERE per_picpath LIKE '%/PER_SIGN/%' AND pic_show is null ";
        $db_dpis->send_cmd($cmd);
        $cmd =" SELECT PER_ID ,SUM(1) AS CNT
                FROM PER_PERSONALPIC 
                WHERE per_picpath LIKE '%/PER_SIGN/%' 
                GROUP BY PER_ID 
                HAVING COUNT(per_id) >= 1"; //per_picseq
        $db_dpis->send_cmd($cmd);
        while($data = $db_dpis->get_array()){
            $dbPER_ID = $data[PER_ID];
            $cmd = "SELECT MAX(PER_PICSEQ) AS PER_PICSEQ
                    FROM PER_PERSONALPIC 
                    WHERE per_id=".$dbPER_ID." and per_picpath like '%/PER_SIGN/%'";
            $db_dpis_Pic->send_cmd($cmd);
            //echo $cmd."<br>";
            while($dataPic = $db_dpis_Pic->get_array()){
                $dbPER_PICSEQ=$dataPic[PER_PICSEQ];
                $cmd ="UPDATE PER_PERSONALPIC SET pic_show=1 where PER_ID=".$dbPER_ID." AND PER_PICSEQ=".$dbPER_PICSEQ;
                $db_dpis_PicDef->send_cmd($cmd);
               // echo $cmd."<br>";
            }
        }
    }
?>
