<?
		// เลือก PER_ID จาก PER_COMDTL ตาม COM_ID วนลูปเพื่อเก็บลง array
		$cmd = "  select PER_ID from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
			$arr_per_id[] = trim($data[PER_ID]);
		}  // end while 	
		// เลือก PER_ID จาก PER_COMDTL ตาม COM_ID วนลูปเพื่อตรวจสอบตำแหน่งที่ต้องการย้าย
		$cmd = "  	select 	a.PER_ID, a.POS_ID, a.POEM_ID, a.POEMS_ID, b.PER_NAME, b.PER_SURNAME, b.DEPARTMENT_ID 
					from		PER_COMDTL a, PER_PERSONAL b 
					where	COM_ID=$COM_ID and a.PER_ID=b.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
			$tmp_per_id = trim($data[PER_ID]);
			$tmp_pos_id = trim($data[POS_ID]);
			$tmp_poem_id = trim($data[POEM_ID]);	
			$tmp_poems_id = trim($data[POEMS_ID]);
			$tmp_per_name = trim($data[PER_NAME]) ." ". trim($data[PER_SURNAME]);
			$tmp_department_id = trim($data[DEPARTMENT_ID]);
			$tmp_data = explode("\|", trim($data[CMD_POSITION]));
			$old_pos_no = $tmp_data[0];		//เลขที่ตำแหน่งเดิม
			if (is_array($arr_pos_id) && in_array($tmp_pos_id, $arr_pos_id))  {	//ข้าราชการ
				$num++;
				$error_move_personal.= "<tr><td width='6%'></td><td><font color='#FF0000'>$num.&nbsp;&nbsp;เลขที่ตำแหน่ง $old_pos_no ซ้ำ</font></td></tr>";
			}  // end if 
			if (is_array($arr_poem_id) && in_array($tmp_poem_id, $arr_poem_id))  { //ลูกจ้างประจำ
				$num++;
				$error_move_personal.= "<tr><td width='6%'></td><td><font color='#FF0000'>$num.&nbsp;&nbsp;เลขที่ตำแหน่ง $old_pos_no ซ้ำ</font></td></tr>";
			}  // end if 
			if (is_array($arr_poems_id) && in_array($tmp_poems_id, $arr_poems_id))  { //พนักงานราชการ
				$num++;
				$error_move_personal.= "<tr><td width='6%'></td><td><font color='#FF0000'>$num.&nbsp;&nbsp;เลขที่ตำแหน่ง $old_pos_no ซ้ำ</font></td></tr>";
			}  // end if 

//################
			if ($tmp_pos_id) {
				$arr_pos_id[] = trim($tmp_pos_id);
				$search_pos = "POS_ID=$tmp_pos_id and DEPARTMENT_ID = $tmp_department_id";
				$search_per_type = "PER_TYPE=1";
				$cmd = " 	select 	POS_NO as POS_NO
				from 		PER_POSITION
				where 	$search_pos";
			} elseif ($tmp_poem_id) {
				$arr_poem_id[] = trim($tmp_poem_id);
				$search_pos = "POEM_ID=$tmp_poem_id and DEPARTMENT_ID = $tmp_department_id";
				$search_per_type = "PER_TYPE=2";
				$cmd = " 	select 	POEM_NO as POS_NO
				from 		PER_POS_EMP
				where 	$search_pos";
			} elseif ($tmp_poems_id) {
				$arr_poems_id[] = trim($tmp_poems_id);
				$search_pos = "POEMS_ID=$tmp_poems_id and DEPARTMENT_ID = $tmp_department_id";
				$search_per_type = "PER_TYPE=3";
				$cmd = " 	select 	POEMS_NO as POS_NO
				from 		PER_POS_EMPSER
				where 	$search_pos";
			}  // end if
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$old_pos_no = trim($data1[POS_NO]);
//################
		
			// ตรวจสอบว่ามีคนครองหรือไม่
			$cmd = " select PER_ID from PER_PERSONAL where $search_pos and $search_per_type and PER_STATUS = 1 ";
			$count_pos = $db_dpis1->send_cmd($cmd);
			if (trim($count_pos)) {						// ถ้ามีคนครองตำแหน่งอยู่
				$data1 = $db_dpis1->get_array();
				$per_id_personal = trim($data1[PER_ID]);
				// ตรวจสอบว่าตำแหน่งที่ต้องการย้ายอยู่ใน PER_COMDTL หรือไม่
				if (!in_array($per_id_personal, $arr_per_id))  { 	// ถ้าไม่มีตำแหน่งที่จะย้ายอยู่ใน PER_COMDTL ตาม COM_ID				
					$num++;
					$error_move_personal.= "<tr><td width='6%'></td><td><font color='#FF0000'>$num.&nbsp;&nbsp;เลขที่ตำแหน่ง <b>$old_pos_no</b> มีคนครองแล้ว&nbsp;::&nbsp;(PER_ID: <b>$tmp_per_id</b>&nbsp;=&nbsp;$tmp_per_name)</font></td></tr>";
					//echo "$num. $tmp_per_id :: $tmp_per_name<br>";
				}  // end if 
			}  // end if 
}  // end while 
		
		if (trim($error_move_personal)) {
			$error_move_personal="<table width='100%' border='0' cellpadding='0' cellspacing='0' class='table_body_3'>".$error_move_personal."</table>";
		} // end if 		
?>