<?
//	fix head for rpt_P1501

	if($COM_TYPE == "5090" || $COM_TYPE == "1300"){
		$heading_name4="�ѡ���Ҫ���᷹";
	}elseif($COM_TYPE == "5100" || $COM_TYPE == "1400"){
		$heading_name4="�ѡ�ҡ��㹵��˹�";
	}		
	if ($BKK_FLAG==1) {
		$heading_width[0] = "10";
		$heading_width[1] = "35";
		$heading_width[2] = "35";
		$heading_width[3] = "17";
		$heading_width[4] = "20";
		$heading_width[5] = "35";
		$heading_width[6] = "17";
		$heading_width[7] = "20";
		$heading_width[8] = "30";
		$heading_text[0] = "|�ӴѺ|���";
		$heading_text[1] = "|����-���ʡ��";
		if ($CARDNO_FLAG==1) $heading_text[1] .= "|$CARDNO_TITLE";
		$heading_text[2] =  $COM_HEAD_01."|���˹�/�ѧ�Ѵ";
		$heading_text[3] =  $COM_HEAD_01."|���˹�|������";
		$heading_text[4] = $COM_HEAD_01."|�дѺ";
		$heading_text[5] =  "$heading_name4";
		$heading_text[6] = "|������ѹ���";
		$heading_text[7] = "|�֧�ѹ���";
		$heading_text[8] = "|�����˵�";
	} else {
		$heading_width[0] = "10";
		$heading_width[1] = "35";
		$heading_width[2] = "35";
		$heading_width[3] = "17";
		$heading_width[4] = "20";
		$heading_width[5] = "15";
		$heading_width[6] = "35";
		$heading_width[7] = "17";
		$heading_width[8] = "20";
		$heading_width[9] = "15";
		$heading_width[10] = "20";
		if($COM_TYPE == "5090" || $COM_TYPE == "1300" || ($MFA_FLAG==1 && ($COM_TYPE == "5100" || $COM_TYPE == "1400"))){
			$heading_width[11] = "20";
			$heading_width[12] = "30";
		}elseif($COM_TYPE == "5100" || $COM_TYPE == "1400"){
			$heading_width[11] = "30";
		}
		$heading_text[0] = "|�ӴѺ|���";
		$heading_text[1] = "|����-���ʡ��";
		if ($CARDNO_FLAG==1) $heading_text[1] .= "|$CARDNO_TITLE";
		$heading_text[2] =  $COM_HEAD_01."|���˹�/�ѧ�Ѵ";
		$heading_text[3] =  $COM_HEAD_01."|���˹�|������";
		$heading_text[4] = $COM_HEAD_01."|�дѺ";
		$heading_text[5] = $COM_HEAD_01."|�Ţ���";
		$heading_text[6] =  "<**2**>$heading_name4|���˹�/�ѧ�Ѵ";
		$heading_text[7] =  "<**2**>$heading_name4|���˹�|������";
		$heading_text[8] =  "<**2**>$heading_name4|�дѺ";
		$heading_text[9] = "<**2**>$heading_name4|�Ţ���";
		$heading_text[10] = "|������ѹ���";
		
		if($COM_TYPE == "5090" || $COM_TYPE == "1300" || ($MFA_FLAG==1 && ($COM_TYPE == "5100" || $COM_TYPE == "1400"))){
			$heading_text[11] = "|�֧�ѹ���";
			$heading_text[12] = "|�����˵�";
		}elseif($COM_TYPE == "5100" || $COM_TYPE == "1400"){
			$heading_text[11] =  "|�����˵�";
		}
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
	if ($BKK_FLAG==1) {
		$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$heading_align = array("C","C","C","C","C","C","C","C","C");	
		$data_align = array("C","L","C","C","L","L","C","C","C");
	} else {
		$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[10] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$heading_align = array("C","C","C","C","C","C","C","C","C","C","C");	
		$data_align = array("C","L","C","C","L","L","L","C","L","R","C");
		if($COM_TYPE == "5090" || $COM_TYPE == "1300" || ($MFA_FLAG==1 && ($COM_TYPE == "5100" || $COM_TYPE == "1400"))){
			$column_function[11] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[12] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$heading_align[] = "C";
			$heading_align[] = "C";
			$data_align[] = "C";		$data_align[] = "C";
		}elseif($COM_TYPE == "5100" || $COM_TYPE == "1400") {
			$column_function[11] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$heading_align[] = "C";
			$data_align[] = "C";
		}
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