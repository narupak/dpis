<?
		// ���͡ PER_ID �ҡ PER_COMDTL ��� COM_ID ǹ�ٻ������ŧ array
		$cmd = "  select PER_ID from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
			$arr_per_id[] = trim($data[PER_ID]);
		}  // end while 	
		// ���͡ PER_ID �ҡ PER_COMDTL ��� COM_ID ǹ�ٻ���͵�Ǩ�ͺ���˹觷���ͧ�������
		$cmd = "  	select 	a.PER_ID, a.POS_ID, a.POEM_ID, a.POEMS_ID, b.PER_NAME, b.PER_SURNAME 
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
			
			if ($tmp_pos_id) {
				$search_pos = "POS_ID=$tmp_pos_id";
			} elseif ($tmp_poem_id) {
				$search_pos = "POEM_ID=$tmp_poem_id";
			} elseif ($tmp_poems_id) {
				$search_pos = "POEMS_ID=$tmp_poems_id";
			}  // end if
			
			// ��Ǩ�ͺ����դ���ͧ�������
			$cmd = " select PER_ID from PER_PERSONAL where $search_pos and PER_STATUS = 1 ";
			$count_pos = $db_dpis1->send_cmd($cmd);
			if (trim($count_pos)) {						// ����դ���ͧ���˹�����
				$data1 = $db_dpis1->get_array();
				$per_id_personal = trim($data1[PER_ID]);
				// ��Ǩ�ͺ��ҵ��˹觷���ͧ������������ PER_COMDTL �������
				if (!in_array($per_id_personal, $arr_per_id))  { 	// �������յ��˹觷������������ PER_COMDTL ��� COM_ID				
					$num++;
					$error_move_personal.= "<tr><td width='6%'></td><td><font color='#FF0000'>$num.&nbsp;&nbsp;$tmp_per_id&nbsp;::&nbsp;$tmp_per_name</font></td></tr>";
					//echo "$num. $tmp_per_id :: $tmp_per_name<br>";
				}  // end if 
			}  // end if 
		}  // end while 
		
		if (trim($error_move_personal)) {
			$error_move_personal="<table width='100%' border='0' cellpadding='0' cellspacing='0' class='table_body_3'>".$error_move_personal."</table>";
		} // end if 		
?>