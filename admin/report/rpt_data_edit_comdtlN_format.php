<?
//	fix head for  P1702

	if($COM_TYPE=="0202"||"5120"){
	        if (!$FLAG_RTF) {
		$heading_width[0] = "9";
		$heading_width[1] = "22";
		$heading_width[2] = "25";
		$heading_width[3] = "21";
		$heading_width[4] = "15";
		$heading_width[5] = "12";
		$heading_width[6] = "14";
		$heading_width[7] = "14";
		$heading_width[8] = "21";
		$heading_width[9] = "12";
		$heading_width[10] = "17";
		$heading_width[11] = "22";
		$heading_width[12] = "15";
		$heading_width[13] = "12";	
		$heading_width[14] = "12";
		$heading_width[15] = "14";
		$heading_width[16] = "15";	
		$heading_width[17] = "15";		
		     }else if ($FLAG_RTF) {
		$heading_width[0] = "4";
		$heading_width[1] = "7";
		$heading_width[2] = "9";
		$heading_width[3] = "7";
		$heading_width[4] = "6";
		$heading_width[5] = "4";
		$heading_width[6] = "4";
		$heading_width[7] = "4";
		$heading_width[8] = "7";
		$heading_width[9] = "5";
		$heading_width[10] = "6";
		$heading_width[11] = "7";
		$heading_width[12] = "6";
		$heading_width[13] = "4";	
		$heading_width[14] = "4";
		$heading_width[15] = "4";
		$heading_width[16] = "6";	
		$heading_width[17] = "6";			
						}
	}else{
	    if (!$FLAG_RTF) {
		$heading_width[0] = "9";
		$heading_width[1] = "28";
		$heading_width[2] = "27";
		$heading_width[3] = "24";
		$heading_width[4] = "24";
		$heading_width[5] = "12";
		$heading_width[6] = "16";
		$heading_width[7] = "16";
		$heading_width[8] = "25";
		$heading_width[9] = "24";
		$heading_width[10] = "17";
		$heading_width[11] = "19";
		$heading_width[12] = "17";
		$heading_width[13] = "18";	
		$heading_width[14] = "25";
	  }else if ($FLAG_RTF) {	
			   $heading_width[0] = "4";
				$heading_width[1] = "9";
				$heading_width[2] = "9";
				$heading_width[3] = "8";
				$heading_width[4] = "8";
				$heading_width[5] = "4";
				$heading_width[6] = "5";
				$heading_width[7] = "5";
				$heading_width[8] = "8";
				$heading_width[9] = "7";
				$heading_width[10] = "6";
				$heading_width[11] = "6";
				$heading_width[12] = "6";
				$heading_width[13] = "7";	
				$heading_width[14] = "8";
				}
	}

	if($COM_TYPE=="0202"||"5120"){
		$heading_text[0] = "�ӴѺ|���|";
		$heading_text[1] = "$FULLNAME_HEAD";
		if ($CARDNO_FLAG==1) $heading_text[1] .= "*Enter*$CARDNO_TITLE";
		$heading_text[2] = "�زԷ�����Ѻ�������/|ʶҹ�֡��/�ѹ���|����稡���֡��";
		$heading_text[3] = $COM_HEAD_01."���|���˹�/�ѧ�Ѵ|";
		$heading_text[4] = $COM_HEAD_01."���|���˹�|������";
		$heading_text[5] = $COM_HEAD_01."���|�дѺ|";
		$heading_text[6] = $COM_HEAD_01."���|�Ţ���|";
		$heading_text[7] = $COM_HEAD_01."���|�Թ��͹|";
		$heading_text[8] = "<**2**>�ͺ�觢ѹ��|���˹�|";
		$heading_text[9] = "<**2**>�ͺ�觢ѹ��|�ӴѺ���|";
		$heading_text[10] = "<**2**>�ͺ�觢ѹ��|��С�ȼ�|����ͺ�ͧ";
		$heading_text[11] = $COM_HEAD_03."����Ѻ�͹|���˹�/�ѧ�Ѵ|";
		$heading_text[12] = $COM_HEAD_03."����Ѻ�͹|���˹�|������";
		$heading_text[13] = $COM_HEAD_03."����Ѻ�͹|�дѺ|";
		$heading_text[14] = $COM_HEAD_03."����Ѻ�͹|�Ţ���|";
		$heading_text[15] = $COM_HEAD_03."����Ѻ�͹|�Թ��͹|";
		$heading_text[16] = "������ѹ���||";
		$heading_text[17] = "�����˵�||";
	}else{
		$heading_text[0] = "�ӴѺ|���";
		$heading_text[1] = "$FULLNAME_HEAD";
		if ($CARDNO_FLAG==1) $heading_text[1] .= "*Enter*$CARDNO_TITLE";
		$heading_text[2] = "�ز�/�Ң�/|ʶҹ�֡��";
		$heading_text[3] = $COM_HEAD_01."���|���˹�/�ѧ�Ѵ";
		$heading_text[4] = $COM_HEAD_01."���|���˹觻�����";
		$heading_text[5] = $COM_HEAD_01."���|�дѺ";
		$heading_text[6] = $COM_HEAD_01."���|�Ţ���";
		$heading_text[7] = $COM_HEAD_01."���|�Թ��͹";
		$heading_text[8] = $COM_HEAD_02."�������|���˹�/�ѧ�Ѵ";
		$heading_text[9] = $COM_HEAD_02."�������|���˹觻�����";
		$heading_text[10] = $COM_HEAD_02."�������|�дѺ";
		$heading_text[11] = $COM_HEAD_02."�������|�Ţ���";
		$heading_text[12] = $COM_HEAD_02."�������|�Թ��͹";
		$heading_text[13] = "������ѹ���|";
		$heading_text[14] = "�����˵�|";
	}

	// function ������ aggregate ��  SUM, AVG, PERC ����Ǻ�÷Ѵ (ROW)
	// 									SUM-columnindex1-columnindex2-....  Ex SUM-1-3-4-7 ���¶֧ ������ͧ column ��� 1,3,4 ��� 7
	// 									AVG-columnindex1-columnindex2-....  Ex AVG-1-3-4-7 ���¶֧ �������¢ͧ column ��� 1,3,4 ��� 7
	// 									PERCn-columnindex1-columnindex2-....  Ex PERCn-1-3-4-7 n ��� column ��� ������������º��º������ (��� 1 ��� � column 1 3 4 7) 
	//																												����������� n ���� �����ػ (100%) PERC3-1-3-4-7 ���� ���� column ��� 3 �����������㴢ͧ����� column 1,3,4,7
	//	function ������ �ٻẺ (format) ��觨е�ͧ�����ѧ function ������ aggregate ���� (�����)
	//									TNUM-n ��� ����¹�繵���Ţ�ͧ�� n ��� �ӹǹ�ȹ��� ����դ���� �ٹ�� ���ʴ��� -
	//									TNUM0-n ��� ����¹�繵���Ţ�ͧ�� n ��� �ӹǹ�ȹ��� ����դ���� �ٹ�� ���ʴ��� 0
	//									ENUM-n ��� �ʴ����Ţ���ҺԤ n ��� �ӹǹ�ȹ��� ����դ���� �ٹ�� ���ʴ��� -
	//									ENUM0-n ��� �ʴ����Ţ���ҺԤ n ��� �ӹǹ�ȹ��� ����դ���� �ٹ�� ���ʴ��� 0
	//
	if($COM_TYPE=="0202"||"5120"){
		$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[10] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[11] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[12] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[13] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[14] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[15] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[16] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[17] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	
		$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		
		$data_align = array("C","L","L","L","C","C","C","R","L","R","L","L","C","C","C","R","C","L");
	} else {
		$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[10] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[11] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[12] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[13] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[14] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		
		$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		
		$data_align = array("C","L","L","L","C","C","C","R","L","C","C","C","R","C","L");
	}

	$total_head_width = 0;
	for($iii=0; $iii < count($heading_width); $iii++) 	$total_head_width += (int)$heading_width[$iii];
	
	if (!$COLUMN_FORMAT) {	// ��ͧ��˹��� element �������� form1 ����  <input type="hidden" name="COLUMN_FORMAT" value="<?=$COLUMN_FORMAT
		$arr_column_map = (array) null;
		$arr_column_sel = (array) null;
		for($i=0; $i < count($heading_text); $i++) {
			$arr_column_map[] = $i;		// link index �ͧ head 
			$arr_column_sel[] = 1;			// 1=�ʴ�	0=����ʴ�   �������ա�û�Ѻ����§ҹ �����ʴ����
		}
		$arr_column_width = $heading_width;	// �������ҧ
		$arr_column_align = $data_align;		// align
		$COLUMN_FORMAT = implode(",",$arr_column_map)."|".implode(",",$arr_column_sel)."|".implode(",",$arr_column_width)."|".implode(",",$arr_column_align);
	} else {
		$arrbuff = explode("|",$COLUMN_FORMAT);
		$arr_column_map = explode(",",$arrbuff[0]);		// index �ͧ head �������
		$arr_column_sel = explode(",",$arrbuff[1]);	// 1=�ʴ�	0=����ʴ�
		$arr_column_width = explode(",",$arrbuff[2]);	// �������ҧ
		$heading_width = $arr_column_width;	// �������ҧ
		$arr_column_align = explode(",",$arrbuff[3]);		// align
	}
	
	$total_show_width = 0;
	for($iii=0; $iii < count($arr_column_width); $iii++) 	if ($arr_column_sel[$iii]==1) $total_show_width += (int)$arr_column_width[$iii];

?>