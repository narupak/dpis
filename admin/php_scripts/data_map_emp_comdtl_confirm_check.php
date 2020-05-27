<?
		// เลือก PER_ID จาก PER_COMDTL ตาม COM_ID วนลูปเพื่อตรวจสอบตำแหน่งที่ต้องการย้าย
		$cmd = "  	select 	a.PN_CODE_ASSIGN, a.CMD_POSITION, b.PER_NAME, b.PER_SURNAME 
					from		PER_COMDTL a, PER_PERSONAL b 
					where	COM_ID=$COM_ID and a.PER_ID=b.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
			$tmp_pn_code_assign = trim($data[PN_CODE_ASSIGN]);
			$tmp_per_name = trim($data[PER_NAME]) ." ". trim($data[PER_SURNAME]);
			$tmp_data = explode("\|", trim($data[CMD_POSITION]));
			$tmp_poem_no = $tmp_data[0];		//เลขที่ตำแหน่งเดิม

			if (!$tmp_pn_code_assign) {						
					$num++;
					$error_move_personal.= "<tr><td width='6%'></td><td><font color='#FF0000'>$num.&nbsp;&nbsp;ยังไม่ได้จัดตำแหน่ง=$tmp_poem_no&nbsp;::&nbsp;$tmp_per_name&nbsp;::&nbsp;</font></td></tr>";
			}  // end if 
		}  // end while 
		
		if (trim($error_move_personal)) {
			$error_move_personal="<table width='100%' border='0' cellpadding='0' cellspacing='0' class='table_body_3'>".$error_move_personal."</table>";
		} // end if 		
?>