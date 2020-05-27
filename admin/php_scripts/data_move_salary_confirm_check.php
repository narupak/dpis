<?
		// เลือก PER_ID จาก PER_COMDTL ตาม COM_ID วนลูปเพื่อเก็บลง array
		$cmd = "  select PER_ID from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
			$arr_per_id[] = trim($data[PER_ID]);
		}  // end while 	
		// เลือก PER_ID จาก PER_COMDTL ตาม COM_ID วนลูปเพื่อตรวจสอบตำแหน่งที่ต้องการตัดโอน
		$cmd = "  	select 	a.PER_ID, a.PAY_ID, b.PER_NAME, b.PER_SURNAME 
					from		PER_COMDTL a, PER_PERSONAL b 
					where	COM_ID=$COM_ID and a.PER_ID=b.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
			$tmp_per_id = trim($data[PER_ID]);
			$tmp_pos_id = trim($data[PAY_ID]);
			$tmp_per_name = trim($data[PER_NAME]) ." ". trim($data[PER_SURNAME]);
			$tmp_data = explode("\|", trim($data[CMD_POSITION]));
			$old_pos_no = $tmp_data[0];		//เลขที่ตำแหน่งเดิม
			if (in_array($tmp_pos_id, $arr_pos_id))  { 					
				$num++;
				$error_move_personal.= "<tr><td width='6%'></td><td><font color='#FF0000'>$num.&nbsp;&nbsp;เลขที่ตำแหน่งซ้ำ=$tmp_pos_id</font></td></tr>";
			}  // end if 
			$arr_pos_id[] = trim($tmp_pos_id);
			$search_pos = "PAY_ID=$tmp_pos_id and PER_TYPE=1";
			
			// ตรวจสอบว่ามีคนครองหรือไม่
			$cmd = " select PER_ID from PER_PERSONAL where $search_pos and PER_STATUS = 1 ";
			$count_pos = $db_dpis1->send_cmd($cmd);
			if (trim($count_pos)) {						// ถ้ามีคนครองตำแหน่งอยู่
				$data1 = $db_dpis1->get_array();
				$per_id_personal = trim($data1[PER_ID]);
				// ตรวจสอบว่าตำแหน่งที่ต้องการตัดโอนอยู่ใน PER_COMDTL หรือไม่
				if (!in_array($per_id_personal, $arr_per_id))  { 	// ถ้าไม่มีตำแหน่งที่จะตัดโอนอยู่ใน PER_COMDTL ตาม COM_ID				
					$num++;
					$error_move_personal.= "<tr><td width='6%'></td><td><font color='#FF0000'>$num.&nbsp;&nbsp;เลขที่ตำแหน่งเดิม=$old_pos_no&nbsp;::&nbsp;$tmp_per_id&nbsp;::&nbsp;$tmp_per_name</font></td></tr>";
					//echo "$num. $tmp_per_id :: $tmp_per_name<br>";
				}  // end if 
			}  // end if 
		}  // end while 
		
		if (trim($error_move_personal)) {
			$error_move_personal="<table width='100%' border='0' cellpadding='0' cellspacing='0' class='table_body_3'>".$error_move_personal."</table>";
		} // end if 		
?>