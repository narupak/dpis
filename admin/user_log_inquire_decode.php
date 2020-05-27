<?     /////////////////////////////////// ส่วนหลัง ของการถอดรหัส /////////////////////////////////////////////////
		$array_date=explode('[',$TMP_LOG_DETAIL);
		$id_detail=$array_date[0];
		$id_detail_1=$array_date[1];
		
		$array_date_2=explode(',',$id_detail_1);
		$id_detail=$array_date_2[0];
		$id_detail_1=$array_date_2[1];
		
		$array_date_3=explode(':',$id_detail);
		$id_detail=$array_date_3[0];
		$id_detail_1=$array_date_3[1];
		
		$array_date_4=explode('->',$id_detail);
		$id_detail=$array_date_4[0];
		$id_detail_1=$array_date_4[1];
		
		$detail_2 = str_replace("]","",$id_detail);
		$TMP_LOG_DETAIL_cut = trim($detail_2);
		
	
		/////////////////////////////////// ส่วนหลัง ของการถอดรหัส /////////////////////////////////////////////////
	    if(strstr($TMP_LOG_DETAIL,'> C01')){ //จับข้อความที่มี			
          $id_detail_1=$array_date[1];
		  $detail_2 = str_replace("]","",$id_detail_1);
		  $detail_id_1 = trim($detail_2);
		}
		else if(strstr($TMP_LOG_DETAIL,'> C02')){ //จับข้อความที่มี
		  $id_detail_1=$array_date[1];
		  $detail_2 = str_replace("]","",$id_detail_1);		
		  $detail_id_1 = trim($detail_2);
			//if (!is_numeric($TMP_LOG_DETAIL_cut)) $detail_id_1 = "error ค่าที่ตัดออกมาไม่ใช่ตัวเลขทั้งหมด";
	    }
		else if(strstr($TMP_LOG_DETAIL,'> C07')){ //จับข้อความที่มี
		
		}
		else if(strstr($TMP_LOG_DETAIL,'> K07')){ //จับข้อความที่มี
		$cmd_1 = " select POS_ID,CL_NAME from PER_POSITION
						where POS_ID = $TMP_LOG_DETAIL_cut";	
					
		$db_dpis1->send_cmd($cmd_1);
			while ($data_cut = $db_dpis1->get_array()) {
			$detail_id_1 =  trim($data_cut[CL_NAME]);
			if (!is_numeric($TMP_LOG_DETAIL_cut)) $detail_id_1 = "error ค่าที่ตัดออกมาไม่ใช่ตัวเลขทั้งหมด";
			}
		}
		else if(strstr($TMP_LOG_DETAIL,'> C14')){ //จับข้อความที่มี
		
		}
		else if(strstr($TMP_LOG_DETAIL,'> S01')){ //จับข้อความที่มี
		  $id_detail_1=$array_date[1];
		  $detail_2 = str_replace("]","",str_replace(":","",$id_detail_1));
		  $detail_id_1 = trim($detail_2);		  
		}
		else if(strstr($TMP_LOG_DETAIL,'> S02')){ //จับข้อความที่มี	
				if(strstr($TMP_LOG_DETAIL,'ตำแหน่งข้าราชการ')){
				$id_detail_1=$array_date_3[1];
				$detail_2 = str_replace("]","",$id_detail_1);
				$TMP_LOG_DETAIL_cut = trim($detail_2);
				$cmd_1 = " select a.POS_ID,b.PL_CODE,b.PL_NAME from PER_POSITION a,PER_LINE b
						where a.POS_ID = $TMP_LOG_DETAIL_cut and
						a.PL_CODE = b.PL_CODE";
						$db_dpis1->send_cmd($cmd_1);
					while ($data_cut = $db_dpis1->get_array()) {
					$detail_id_1_1 =  trim($data_cut[PL_NAME]);
					if (!is_numeric($TMP_LOG_DETAIL_cut)) $detail_id_1 = "error ค่าที่ตัดออกมาไม่ใช่ตัวเลขทั้งหมด";
					}
				}else if(strstr($TMP_LOG_DETAIL,'ตำแหน่งลูกจ้างประจำ')){
					$id_detail_1=$array_date_3[1];
					$detail_2 = str_replace("]","",$id_detail_1);
					$TMP_LOG_DETAIL_cut = trim($detail_2);
					$cmd_1 = " select a.POEM_ID,b.PN_CODE,b.PN_NAME from PER_POS_EMP a,PER_POS_NAME b
						where a.POEM_ID = $TMP_LOG_DETAIL_cut and
						a.PN_CODE = b.PN_CODE";
						$db_dpis1->send_cmd($cmd_1);
						while ($data_cut = $db_dpis1->get_array()) {
						$detail_id_1_1 =  trim($data_cut[PN_NAME]);
						if (!is_numeric($TMP_LOG_DETAIL_cut)) $detail_id_1 = "error ค่าที่ตัดออกมาไม่ใช่ตัวเลขทั้งหมด";
						}
				}else if(strstr($TMP_LOG_DETAIL,'ตำแหน่งพนักงานราชการ')){
					$id_detail_1=$array_date_3[1];
					$detail_2 = str_replace("]","",$id_detail_1);
					$TMP_LOG_DETAIL_cut = trim($detail_2);
					$cmd_1 = " select a.POEMS_ID,b.EP_CODE,b.EP_NAME from PER_POS_EMPSER a,PER_EMPSER_POS_NAME b
						where a.POEMS_ID = $TMP_LOG_DETAIL_cut and
						a.EP_CODE = b.EP_CODE";
						$db_dpis1->send_cmd($cmd_1);
						while ($data_cut = $db_dpis1->get_array()) {
						$detail_id_1_1 =  trim($data_cut[EP_NAME]);
						if (!is_numeric($TMP_LOG_DETAIL_cut)) $detail_id_1 = "error ค่าที่ตัดออกมาไม่ใช่ตัวเลขทั้งหมด";
						}
				}else if(strstr($TMP_LOG_DETAIL,'ตำแหน่งลูกจ้างชั่วคราว ')){
					$id_detail_1=$array_date_3[1];
					$detail_2 = str_replace("]","",$id_detail_1);
					$TMP_LOG_DETAIL_cut = trim($detail_2);
					$cmd_1 = " select a.POT_ID,b.TP_CODE,b.TP_NAME from PER_POS_TEMP a,PER_TEMP_POS_NAME b
						where a.POT_ID = $TMP_LOG_DETAIL_cut and
						a.TP_CODE = b.TP_CODE";
						$db_dpis1->send_cmd($cmd_1);
						while ($data_cut = $db_dpis1->get_array()) {
						$detail_id_1_1 =  trim($data_cut[TP_NAME]);
						if (!is_numeric($TMP_LOG_DETAIL_cut)) $detail_id_1 = "error ค่าที่ตัดออกมาไม่ใช่ตัวเลขทั้งหมด";
						}
				}
				$id_detail=$array_date_3[0];
				$id_detail_2=str_replace("]","",$array_date_3[2]);
				$detail_2 = str_replace("]","",$id_detail);
				$TMP_LOG_DETAIL_cut = trim($detail_2);
		        $cmd_1 = " select ORG_NAME,DEPARTMENT_ID from PER_ORG
						where DEPARTMENT_ID = $TMP_LOG_DETAIL_cut";
						$db_dpis1->send_cmd($cmd_1);
				while ($data_cut = $db_dpis1->get_array()) {
				$detail_id_1_2 =  trim($data_cut[ORG_NAME]);
				if (!is_numeric($TMP_LOG_DETAIL_cut)) $detail_id_1 = "error ค่าที่ตัดออกมาไม่ใช่ตัวเลขทั้งหมด";
				}
				$detail_id_1 = $detail_id_1_2.":".$detail_id_1_1.":".$id_detail_2;
		}	
		else if(strstr($TMP_LOG_DETAIL,'> S04')){ //จับข้อความที่มี
			$id_detail_2=$array_date_3[2];
			$id_detail_1=$array_date_3[1];
			$detail_2 = str_replace("]","",$id_detail_2);
			$TMP_LOG_DETAIL_cut_2 = trim($detail_2);
			$cmd_1 = " select PL_CODE,PL_NAME from PER_LINE 
						where PL_CODE = $TMP_LOG_DETAIL_cut_2 ";	
				$db_dpis1->send_cmd($cmd_1);
				while ($data_cut = $db_dpis1->get_array()) {
				$detail_id_1_old =  trim($data_cut[PL_NAME]);
				if (!is_numeric($TMP_LOG_DETAIL_cut)) $detail_id_1 = "error ค่าที่ตัดออกมาไม่ใช่ตัวเลขทั้งหมด";
			}		
			if(strstr($TMP_LOG_DETAIL,'บันทึกประวัติตำแหน่ง ')){
			$cmd_1 = " select a.POS_ID,a.CL_NAME,b.PL_CODE,b.PL_NAME from PER_POSITION a,PER_LINE b
						where POS_ID = $TMP_LOG_DETAIL_cut and
						a.PL_CODE = b.PL_CODE";	
						
			$db_dpis1->send_cmd($cmd_1);
				while ($data_cut = $db_dpis1->get_array()) {
				$detail_id_1_1 =  trim($data_cut[CL_NAME]);
				$detail_id_1_2 =  trim($data_cut[PL_NAME]);
				$detail_id_1 = $id_detail_1.",".$detail_id_1_1.",".$detail_id_1_2;
				if (!is_numeric($TMP_LOG_DETAIL_cut)) $detail_id_1 = "error ค่าที่ตัดออกมาไม่ใช่ตัวเลขทั้งหมด";
				}
			
			}
		}	
		else{
		$detail_id_1 = "ไม่มีการเช็คค่าเพื่อแสดงข้อมูล";
		}
		
		
 
		
?>

