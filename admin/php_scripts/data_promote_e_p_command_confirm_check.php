<?
		// ���͡ PER_ID �ҡ PER_COMDTL ��� COM_ID ǹ�ٻ������ŧ array
		$cmd = "  select PER_ID from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
			$arr_per_id[] = trim($data[PER_ID]);
		}  // end while 	
		// ���͡ PER_ID �ҡ PER_COMDTL ��� COM_ID ǹ�ٻ���͵�Ǩ�ͺ���˹觷���ͧ�������͹
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
                        $old_pos_no = trim($data[CMD_POS_NO]); //�Ţ�����˹����
			
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
                        
			if ($DEPARTMENT_NAME=="�����û���ͧ") $search_pos = "PAY_ID=$tmp_pay_id and PER_TYPE=1";
			
			// ��Ǩ�ͺ����դ���ͧ�������
			$cmd = " select PER_ID from PER_PERSONAL where $search_pos and PER_STATUS = 1 ";		//and $search_per_type   ����

			$count_pos = $db_dpis1->send_cmd($cmd);
			if (trim($count_pos)) { // ����դ���ͧ���˹�����
				$data1 = $db_dpis1->get_array();
				$per_id_personal = trim($data1[PER_ID]);
				// ��Ǩ�ͺ��ҵ��˹觷���ͧ�������͹����� PER_COMDTL �������
				if (!in_array($per_id_personal, $arr_per_id))  { 	// �������յ��˹觷�������͹����� PER_COMDTL ��� COM_ID				
					$num++;
					/*���*/
                                        //$error_move_personal.= "<tr><td width='6%'></td><td><font color='#FF0000'>$num.&nbsp;&nbsp;$tmp_per_id&nbsp;::&nbsp;$tmp_per_name</font><td></tr>";
                                        /*Release 5.2.1.5*/
                                        $error_move_personal.= "<tr><td width='6%'></td><td><font color='#FF0000'>$num.&nbsp;&nbsp;�Ţ�����˹����=$old_pos_no&nbsp;::&nbsp;$tmp_per_name&nbsp;::&nbsp;�Ţ�����˹�����=$tmp_pos_no �����ҧ</font></td></tr>";
                                        ///1.  �Ţ�����˹����=16 :: ���ͺ �ͺ���� :: �Ţ�����˹�����=41 �����ҧ
				}  // end if 
			}  // end if 			
		}  // end while 

		if (trim($error_move_personal)) {
			$error_move_personal="<table width='100%' border='0' cellpadding='0' cellspacing='0' class='table_body_3'>".$error_move_personal."</table>";
		} // end if 
?>

