<?
//	fix head for rpt_personal_inquiry_xls

	if ($BKK_FLAG==1) $Row = 8;
	else $Row = 9;
 
	$heading_width[0] = "10";
	$heading_width[1] = "25";
	$heading_width[2] = "18";
	$heading_width[3] = "15";
	$heading_width[4] = "25";
	$heading_width[5] = "25";
	$heading_width[6] = "15";
	$heading_width[7] = "20";
	if ($BKK_FLAG!=1) $heading_width[8] = "30";
	$heading_width[$Row] = "30";
	$heading_width[$Row+1] = "30";
	$heading_width[$Row+2] = "30";
	$heading_width[$Row+3] = "15";
	$heading_width[$Row+4] = "15";
	$heading_width[$Row+5] = "15";
	$heading_width[$Row+6] = "15";
	$heading_width[$Row+7] = "15";
	$heading_width[$Row+8] = "20";
	$heading_width[$Row+9] = "25";
	$heading_width[$Row+10] = "25";
	$heading_width[$Row+11] = "30";
	$heading_width[$Row+12] = "20";
	$heading_width[$Row+13] = "25";
	$heading_width[$Row+14] = "25";
	$heading_width[$Row+15] = "30";
	$heading_width[$Row+16] = "20";
	$heading_width[$Row+17] = "25";
	$heading_width[$Row+18] = "25";
	$heading_width[$Row+19] = "30";
	$heading_width[$Row+20] = "20";
	$heading_width[$Row+21] = "12";
	$heading_width[$Row+22] = "12";
	$heading_width[$Row+23] = "12";
	$heading_width[$Row+24] = "12";
	$heading_width[$Row+25] = "12";
	$heading_width[$Row+26] = "12";
	$heading_width[$Row+27] = "12";
	$heading_width[$Row+28] = "12";
	$heading_width[$Row+29] = "12";
	$heading_width[$Row+30] = "12";
	$heading_width[$Row+31] = "12";
	$heading_width[$Row+32] = "12";
	$heading_width[$Row+33] = "12";
	$heading_width[$Row+34] = "12";
	$heading_width[$Row+35] = "12";
	$heading_width[$Row+36] = "12";
	$heading_width[$Row+37] = "12";
	$heading_width[$Row+38] = "12";
	$heading_width[$Row+39] = "12";
	$heading_width[$Row+40] = "12";
	$heading_width[$Row+41] = "12";
	$heading_width[$Row+42] = "12";
	$heading_width[$Row+43] = "12";
	$heading_width[$Row+44] = "12";
	$heading_width[$Row+45] = "12";
	$heading_width[$Row+46] = "12";
	$heading_width[$Row+47] = "12";
	$heading_width[$Row+48] = "30";
	$heading_width[$Row+49] = "12";
	if ($MFA_FLAG == 1) { 
		$mfa_row = $Row+49;
		$heading_text[$mfa_row+1] = "35";
		$heading_text[$mfa_row+2] = "25";
		$heading_text[$mfa_row+3] = "10";
		$heading_text[$mfa_row+4] = "25";
		$heading_text[$mfa_row+5] = "25";
		$heading_text[$mfa_row+6] = "10";
		$heading_text[$mfa_row+7] = "30";
		$heading_text[$mfa_row+8] = "30";
		$heading_text[$mfa_row+9] = "30";
		$heading_text[$mfa_row+10] = "40";
	}

	$heading_text[0] = "|$POS_NO_TITLE";
	$heading_text[1] = "|���� - ���ʡ��";
	$heading_text[2] = "|�Ţ��Шӵ�ǻ�ЪҪ�";
	$heading_text[3] = "|�ѹ/��͹/���Դ";
	$heading_text[4] = "|$PL_TITLE";
	$heading_text[5] = "|$PM_TITLE";
	$heading_text[6] = "|$PT_TITLE";
	$heading_text[7] = "|$LEVEL_TITLE";
	if ($BKK_FLAG!=1) $heading_text[8] = "|$MINISTRY_TITLE";
	$heading_text[$Row] = "|$DEPARTMENT_TITLE";
	$heading_text[$Row+1] = "|$ORG_TITLE";
	$heading_text[$Row+2] = "|$ORG_TITLE1";
	$heading_text[$Row+3] = "|$ORG_TITLE2";
	$heading_text[$Row+4] = "|�Թ��͹";
	$heading_text[$Row+5] = "|�Թ��Шӵ��˹�";
	$heading_text[$Row+6] = "|�ѹ���³����";
	$heading_text[$Row+7] = "|�ѹ���������Ѻ�Ҫ���";
	$heading_text[$Row+8] = "|�ѹ��������ǹ�Ҫ���";
	$heading_text[$Row+9] = "|$EL_TITLE";
	$heading_text[$Row+10] = "|$EN_TITLE";
	$heading_text[$Row+11] = "�زԷ�����è�|$EM_TITLE";
	$heading_text[$Row+12] = "|$INS_TITLE";
	$heading_text[$Row+13] = "|$EL_TITLE";
	$heading_text[$Row+14] = "|$EN_TITLE";
	$heading_text[$Row+15] = "�ز�㹵��˹觻Ѩ�غѹ|$EM_TITLE";
	$heading_text[$Row+16] = "|$INS_TITLE";
	$heading_text[$Row+17] = "|$EL_TITLE";
	$heading_text[$Row+18] = "|$EN_TITLE";
	$heading_text[$Row+19] = "�ز��٧�ش|$EM_TITLE";
	$heading_text[$Row+20] = "|$INS_TITLE";
	$heading_text[$Row+21] = "|����ͧ�Ҫ �";
	$heading_text[$Row+22] = "|�дѺ 1";
	$heading_text[$Row+23] = "|�дѺ 2";
	$heading_text[$Row+24] = "|�дѺ 3";
	$heading_text[$Row+25] = "|�дѺ 4";
	$heading_text[$Row+26] = "|�дѺ 5";
	$heading_text[$Row+27] = "|�дѺ 6";
	$heading_text[$Row+28] = "|�дѺ 7";
	$heading_text[$Row+29] = "|�дѺ 8";
	$heading_text[$Row+30] = "|�дѺ 9";
	$heading_text[$Row+31] = "|�дѺ 10";
	$heading_text[$Row+32] = "|�дѺ 11";
	$heading_text[$Row+33] = "|��Ժѵԧҹ";
	$heading_text[$Row+34] = "|�ӹҭ�ҹ";
	$heading_text[$Row+35] = "|������";
	$heading_text[$Row+36] = "|�ѡ�о����";
	$heading_text[$Row+37] = "|��Ժѵԡ��";
	$heading_text[$Row+38] = "|�ӹҭ���";
	$heading_text[$Row+39] = "|�ӹҭ��þ����";
	$heading_text[$Row+40] = "|����Ǫҭ";
	$heading_text[$Row+41] = "|�ç�س�ز�";
	$heading_text[$Row+42] = "|�ӹ�¡�õ�";
	$heading_text[$Row+43] = "|�ӹ�¡���٧";
	$heading_text[$Row+44] = "|�����õ�";
	$heading_text[$Row+45] = "|�������٧";
	$heading_text[$Row+46] = "�ѹ���������|�дѺ�Ѩ�غѹ";
	$heading_text[$Row+47] = "�ҹ㹡��|�ӹǳ";
	$heading_text[$Row+48] = "$ORG_TITLE|�ͺ���§ҹ";
	$heading_text[$Row+49] = "|�Ţ������";
	if ($MFA_FLAG == 1) { 
		$mfa_row = $Row+49;
		$heading_text[$mfa_row+1] = "|$MOV_TITLE";
		$heading_text[$mfa_row+2] = "|�Ţ�������";
		$heading_text[$mfa_row+3] = "|$POS_NO_TITLE";
		$heading_text[$mfa_row+4] = "|$PL_TITLE";
		$heading_text[$mfa_row+5] = "|$PM_TITLE";
		$heading_text[$mfa_row+6] = "|$LEVEL_TITLE";
		$heading_text[$mfa_row+7] = "|$DEPARTMENT_TITLE";
		$heading_text[$mfa_row+8] = "|$ORG_TITLE";
		$heading_text[$mfa_row+9] = "$ORG_TITLE|�ͺ���§ҹ";
		$heading_text[$mfa_row+10] = "|$REMARK_TITLE";
	}

	// function ������ aggregate ��  SUM, AVG, PERC ����Ǻ�÷Ѵ (ROW)
	// 									SUM-columnindex1-columnindex2-....  Ex SUM-1-3-4-7 ���¶֧ ������ͧ column ��� 1,3,4 ��� 7
	// 									AVG-columnindex1-columnindex2-....  Ex AVG-1-3-4-7 ���¶֧ �������¢ͧ column ��� 1,3,4 ��� 7
	// 									PERCn-columnindex1-columnindex2-....  Ex PERCn-1-3-4-7 n ��� column ��� ������������º��º������ (��� 1 ��� � column 1 3 4 7) 
	//																												����������� n ���� �����ػ (100%) PERC3-1-3-4-7 ���� ���� column ��� 3 �����������㴢ͧ����� column 1,3,4,7
	//	function ������ �ٻẺ (format) ��觨е�ͧ�����ѧ function ������ aggregate ���� (�����)
	//									TNUM-xn ��� ����¹�繵���Ţ�ͧ��  x ��� ����� comma �������� x �ʴ������ comma �ء��ѡ 1,000  n ��� �ӹǹ�ȹ��� ����դ���� �ٹ�� ���ʴ��� -
	//									TNUM0-xn ��� ����¹�繵���Ţ�ͧ��   x ��� ����� comma �������� x �ʴ������ comma �ء��ѡ 1,000  n ��� �ӹǹ�ȹ��� ����դ���� �ٹ�� ���ʴ��� 0
	//									ENUM-xn ��� �ʴ����Ţ���ҺԤ  x ��� ����� comma �������� x �ʴ������ comma �ء��ѡ 1,000  n ��� �ӹǹ�ȹ��� ����դ���� �ٹ�� ���ʴ��� -
	//									ENUM0-xn ��� �ʴ����Ţ���ҺԤ  x ��� ����� comma �������� x �ʴ������ comma �ء��ѡ 1,000  n ��� �ӹǹ�ȹ��� ����դ���� �ٹ�� ���ʴ��� 0
	//
	$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	if ($BKK_FLAG!=1) $column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+6] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+10] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+11] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+12] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+13] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+14] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+15] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+16] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+17] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+18] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+19] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+20] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+21] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+22] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+23] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+24] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+25] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+26] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+27] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+28] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+29] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+30] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+31] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+32] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+33] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+34] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+35] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+36] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+37] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+38] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+39] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+40] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+41] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+42] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+43] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+44] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+45] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+46] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+47] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Row+48] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");	// if ($BKK_FLAG!=1) column �� 56 ���������� ���� 55
	$column_function[$Row+49] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	if ($MFA_FLAG == 1) { 
		$mfa_row = $Row+49;
		$column_function[$mfa_row+1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[$mfa_row+2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[$mfa_row+3] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[$mfa_row+4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[$mfa_row+5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[$mfa_row+6] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[$mfa_row+7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[$mfa_row+8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[$mfa_row+9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");	
		$column_function[$mfa_row+10] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	}

	if ($MFA_FLAG == 1) { 
		$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		$data_align = array("C","L","C","C","L","L","L","L","L","L","L","L","L","C","C","C","C","C","L","L","L","L","L","L","L","L","L","L","L","L","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","L","L","L","L","C","L","L","C","L","L","L","L","L");
	} elseif ($BKK_FLAG == 1) {
		$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		$data_align = array("C","L","C","C","L","L","L","L","L","L","L","L","C","C","C","C","C","L","L","L","L","L","L","L","L","L","L","L","L","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","L","L","L");
	} else {
		$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		$data_align = array("C","L","C","C","L","L","L","L","L","L","L","L","L","C","C","C","C","C","L","L","L","L","L","L","L","L","L","L","L","L","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","L","L","L");
	}

//	for($i=0; $i < count($heading_text); $i++) {
//		echo "in_format  $i--".$heading_text[$i]."<br>";
//	}

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