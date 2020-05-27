<?
//	fix head for rpt_P1503
    if (!$FLAG_RTF) {
	$heading_width[0] = "10";
	$heading_width[1] = "45";
	$heading_width[2] = "35";
	$heading_width[3] = "30";
	$heading_width[4] = "13";
	$heading_width[5] = "17";
	$heading_width[6] = "35";
	$heading_width[7] = "20";
	$heading_width[8] = "20";
	$heading_width[9] = "25";
	$heading_width[10] = "38";
	}else   if ($FLAG_RTF){
	$heading_width[0] = "5";
	$heading_width[1] = "13";
	$heading_width[2] = "12";
	$heading_width[3] = "12";
	$heading_width[4] = "6";
	$heading_width[5] = "8";
	$heading_width[6] = "12";
	$heading_width[7] = "6";
	$heading_width[8] = "8";
	$heading_width[9] = "9";
	$heading_width[10] = "9";
	}

//new format***************************************************
    $heading_text[0] = "�ӴѺ|���";
	$heading_text[1] = $FULLNAME_HEAD;
	if ($CARDNO_FLAG==1) $heading_text[1] .= "*Enter*$CARDNO_TITLE";
	$heading_text[2] = "�ز�/�Ң�/ʶҹ�֡��|";
	$heading_text[3] = $COM_HEAD_01."���|���˹�/�ѧ�Ѵ";
	$heading_text[4] = $COM_HEAD_01."���|�Ţ���";
	$heading_text[5] = $COM_HEAD_01."���|�Թ��͹";
    $heading_text[6] = $COM_HEAD_02."�������Ҫ���|���˹�/�ѧ�Ѵ";
	$heading_text[7] = $COM_HEAD_02."�������Ҫ���|�Ţ���";
	$heading_text[8] = $COM_HEAD_02."�������Ҫ���|�Թ��͹";
	$heading_text[9] = "������ѹ���|";
	$heading_text[10] = "�����˵�|";

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
	$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[10] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");

	$heading_align = array('C','C','C','C','C','C','C','C','C','C','C');	

	$data_align = array("C", "L", "L", "L", "C", "R", "L", "C", "R", "C", "L");

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