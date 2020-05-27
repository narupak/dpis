<?
//	fix head for rpt_data_mgt_competency_assessment_001_format.php

	$heading_width[0] = "20";
	$heading_width[1] = "15";
	$heading_width[2] = "15";
	$heading_width[3] = "15";
	$heading_width[4] = "15";
	$heading_width[5] = "15";
	$heading_width[6] = "15";
	$heading_width[7] = "15";
	$heading_width[8] = "15";
	$heading_width[9] = "20";
	$heading_width[10] = "20";
	$heading_width[11] = "20";

	$heading_text[0] = "���駷��|��ǹ�Ҫ���";
	$heading_text[1] = "|2";
	$heading_text[2] = "|%";
	$heading_text[3] = "|3";
	$heading_text[4] = "|%";
	$heading_text[5] = "|4";
	$heading_text[6] = "|%";
	$heading_text[7] = "|5";
	$heading_text[8] = "|%";
	$heading_text[9] = "��ṹ|���".($dup_way==1 ? "�٧���" : "���ŧ");
	$heading_text[10] = "Mean|%";
	$heading_text[11] = "�ӹǹ|�����";
	
	$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C");

	$heading_font_style = array("B|","B|B","B|B","B|B","B|B","B|B","B|B","B|B","B|B","B|","|B","B|");
	$heading_font_size = array("14","16","16","16","16","16","16","16","16","14","16","16");
	$heading_fill_color = array("EEEEFF","AAAAAA","EEEEFF","999999","EEEEFF","999999","EEEEFF","999999","EEEEFF","AAAAAA","EEEEFF","EEEEFF");
	$heading_font_color = array("0066CC","555500","0066CC","555555","0066CC","555555","0066CC","555555","0066CC","555500","0066CC","0066CC");
	$heading_border = array("TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR");

	$data_align = array("C","C","R","C","R","C","R","C","R","C","R","C");
	$data_font_style = array("B","","","","","","","","","B","","B");
	$data_font_size = array("16","16","16","16","16","16","16","16","16","16","16","16");
	$data_fill_color = array("","CCCCCC","","CCCCCC","","CCCCCC","","CCCCCC","","CCCCCC","","");
	$data_font_color = array("","555555","","555555","","555555","","555555","","555555","","");
	$data_border = array("TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR");

	$tot_font_style = array("B","B","B","B","B","B","B","B","B","B","B","B");
	$tot_font_size = array("16","16","16","16","16","16","16","16","16","16","16","16");
	$tot_fill_color = array("EEEE55","AAAA55","EEEE55","999955","EEEE55","999955","EEEE55","999955","EEEE55","AAAA55","EEEE55","EEEE55");
	$tot_font_color = array("CC6600","005555","CC6600","225577","CC6600","225577","CC6600","225577","CC6600","005555","CC6600","CC6600");

	// function ������ aggregate ��  SUM, AVG, PERC ����Ǻ�÷Ѵ (ROW)
	// 									SUM-columnindex1-columnindex2-....  Ex SUM-1-3-4-7 ���¶֧ ������ͧ column ��� 1,3,4 ��� 7
	// 									AVG-columnindex1-columnindex2-....  Ex AVG-1-3-4-7 ���¶֧ �������¢ͧ column ��� 1,3,4 ��� 7
	// 									PERCn-columnindex1-columnindex2-....  Ex PERCn-1-3-4-7 n ��� column ��� ������������º��º������ (��� 1 ��� � column 1 3 4 7) 
	//																												����������� n ���� �����ػ (100%) PERC3-1-3-4-7 ���� ���� column ��� 3 �����������㴢ͧ����� column 1,3,4,7
	//	function ������ �ٻẺ (format) ��觨е�ͧ�����ѧ function ������ aggregate ���� (�����)
	//									TNUM-xn ��� ����¹�繵���Ţ�ͧ��  x ��� ����� comma �������� x ���� comma �͡��ѡ 1,000 �������  n ��� �ӹǹ�ȹ��� ����դ���� �ٹ�� ���ʴ��� -
	//									TNUM0-xn ��� ����¹�繵���Ţ�ͧ��   x ��� ����� comma �������� x ���� comma �͡��ѡ 1,000 �������   n ��� �ӹǹ�ȹ��� ����դ���� �ٹ�� ���ʴ��� 0
	//									ENUM-xn ��� �ʴ����Ţ���ҺԤ   x ��� ����� comma �������� x ���� comma �͡��ѡ 1,000 �������   n ��� �ӹǹ�ȹ��� ����դ���� �ٹ�� ���ʴ��� -
	//									ENUM0-xn ��� �ʴ����Ţ���ҺԤ   x ��� ����� comma �������� x ���� comma �͡��ѡ 1,000 �������   n ��� �ӹǹ�ȹ��� ����դ���� �ٹ�� ���ʴ��� 0
	//
	$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2");
	$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2");
	$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2");
	$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2");
	$column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[10] = (($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2");
	$column_function[11] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	
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