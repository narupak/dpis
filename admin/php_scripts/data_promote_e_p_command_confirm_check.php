<?
		// เลือก PER_ID จาก PER_COMDTL ตาม COM_ID วนลูปเพื่อเก็บลง array
		$cmd = "  select PER_ID from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
			$arr_per_id[] = trim($data[PER_ID]);
		}  // end while 	
		// เลือก PER_ID จาก PER_COMDTL ตาม COM_ID วนลูปเพื่อตรวจสอบตำแหน่งที่ต้องการเลื่อน
		$cmd = " SELECT a.PER_ID, a.POS_ID, a.POEM_ID, a.POEMS_ID, b.PER_NAME, b.PER_SURNAME, b.PAY_ID, b.DEPARTMENT_ID ,
                            a.CMD_POS_NO 
                         FROM PER_COMDTL a, PER_PERSONAL b 
                         WHERE	COM_ID=$COM_ID AND a.PER_ID=b.PER_ID ";
		//echo $cmd;
                $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
			$tmp_per_id = trim($data[PER_ID]);
			$tmp_pos_id = trim($data[POS_ID]);
			$tmp_poem_id = trim($data[POEM_ID]);	
			$tmp_poems_id = trim($data[POEMS_ID]);
			$tmp_per_name = trim($data[PER_NAME]) ." ". trim($data[PER_SURNAME]);
			$tmp_department_id = trim($data[DEPARTMENT_ID]);
			$tmp_pay_id = trim($data[PAY_ID]);
                        $old_pos_no = trim($data[CMD_POS_NO]); //เลขที่ตำแหน่งเดิม
			
			if ($tmp_pos_id){
                            $search_pos = "POS_ID=$tmp_pos_id and DEPARTMENT_ID = $tmp_department_id and PER_TYPE=1";
                            $cmd = " select POS_NO from PER_POSITION where POS_ID = $tmp_pos_id and DEPARTMENT_ID = $tmp_department_id ";
                            $db_dpis1->send_cmd($cmd);
                            $data1 = $db_dpis1->get_array();
                            $tmp_pos_no = trim($data1[POS_NO]);
                        }elseif ($tmp_poem_id){
                            $search_pos = "POEM_ID=$tmp_poem_id and DEPARTMENT_ID = $tmp_department_id and PER_TYPE=2";
                            $cmd = " select POEM_NO from PER_POS_EMP where  POEM_ID= $tmp_poem_id and DEPARTMENT_ID = $tmp_department_id ";
                            $db_dpis1->send_cmd($cmd);
                            $data1 = $db_dpis1->get_array();
                            $tmp_pos_no = trim($data1[POEM_NO]);
                        }elseif ($tmp_poems_id){ 
                            $search_pos = "POEMS_ID=$tmp_poems_id and DEPARTMENT_ID = $tmp_department_id and PER_TYPE=3";
                            $cmd = " select POEMS_NO from PER_POS_EMPSER where  POEMS_ID = $tmp_poems_id and DEPARTMENT_ID = $tmp_department_id ";
                            $db_dpis1->send_cmd($cmd);
                            $data1 = $db_dpis1->get_array();
                            $tmp_pos_no = trim($data1[POEMS_NO]);
                        }
                        
			if ($DEPARTMENT_NAME=="กรมการปกครอง") $search_pos = "PAY_ID=$tmp_pay_id and PER_TYPE=1";
			
			// ตรวจสอบว่ามีคนครองหรือไม่
			$cmd = " select PER_ID from PER_PERSONAL where $search_pos and PER_STATUS = 1 ";		//and $search_per_type   ปกติ

			$count_pos = $db_dpis1->send_cmd($cmd);
			if (trim($count_pos)) { // ถ้ามีคนครองตำแหน่งอยู่
				$data1 = $db_dpis1->get_array();
				$per_id_personal = trim($data1[PER_ID]);
				// ตรวจสอบว่าตำแหน่งที่ต้องการเลื่อนอยู่ใน PER_COMDTL หรือไม่
				if (!in_array($per_id_personal, $arr_per_id))  { 	// ถ้าไม่มีตำแหน่งที่จะเลื่อนอยู่ใน PER_COMDTL ตาม COM_ID				
					$num++;
					/*เดิม*/
                                        //$error_move_personal.= "<tr><td width='6%'></td><td><font color='#FF0000'>$num.&nbsp;&nbsp;$tmp_per_id&nbsp;::&nbsp;$tmp_per_name</font><td></tr>";
                                        /*Release 5.2.1.5*/
                                        $error_move_personal.= "<tr><td width='6%'></td><td><font color='#FF0000'>$num.&nbsp;&nbsp;เลขที่ตำแหน่งเดิม=$old_pos_no&nbsp;::&nbsp;$tmp_per_name&nbsp;::&nbsp;เลขที่ตำแหน่งใหม่=$tmp_pos_no ไม่ว่าง</font></td></tr>";
                                        ///1.  เลขที่ตำแหน่งเดิม=16 :: ทดสอบ มอบหมาย :: เลขที่ตำแหน่งใหม่=41 ไม่ว่าง
				}  // end if 
			}  // end if 			
		}  // end while 

		if (trim($error_move_personal)) {
			$error_move_personal="<table width='100%' border='0' cellpadding='0' cellspacing='0' class='table_body_3'>".$error_move_personal."</table>";
		} // end if 
?>

