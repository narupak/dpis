<?
//	fix head for rpt_R010035

	if ($ISCS_FLAG==1)
		$head_width = 15;
	else
		$head_width = 15;
	$heading_width[0] = "10";
	if ($ISCS_FLAG==1)
		$heading_width[1] = "65";
	else
		$heading_width[1] = "53";
	$heading_width[2] = $head_width;
	$heading_width[3] = $head_width;
	$heading_width[4] = $head_width;
	$heading_width[5] = $head_width;
	$heading_width[6] = $head_width;
	$heading_width[7] = $head_width;
	$heading_width[8] = $head_width;
	$heading_width[9] = $head_width;
	if ($ISCS_FLAG!=1) {
		$heading_width[10] = $head_width;
		$heading_width[11] = $head_width;
		$heading_width[12] = $head_width;
		$heading_width[13] = $head_width;
		$heading_width[14] = $head_width;
		$heading_width[15] = $head_width;
		if ($NOT_LEVEL_NO_O4!="Y") {
			$heading_width[16] = $head_width;
		}
	}

	if (!$heading_name) $heading_name="����� heading_name";
	if ($ISCS_FLAG==1) {
		$heading_text[0] = "|�ӴѺ";
		$heading_text[1] = "|$heading_name";
		$heading_text[2] = "|��";
		$heading_text[3] = "<**1**>������|�дѺ�٧";
		$heading_text[4] = "<**1**>������|�дѺ��";
		$heading_text[5] = "<**2**>�ӹ�¡��|�дѺ�٧";
		$heading_text[6] = "<**2**>�ӹ�¡��|�дѺ��";
		$heading_text[7] = "<**3**>�Ԫҡ��|�ç�س�ز�";
		$heading_text[8] = "<**3**>�Ԫҡ��|����Ǫҭ";
		$heading_text[9] = "���|";
	} else {
		$heading_text[0] = "|�ӴѺ|";
		$heading_text[1] = "|$heading_name|";
		$heading_text[2] = "|��";
		$heading_text[3] = "<**1**>������||�дѺ�٧";
		$heading_text[4] = "<**1**>������||�дѺ��";
		$heading_text[5] = "<**2**>�ӹ�¡��||�дѺ�٧";
		$heading_text[6] = "<**2**>�ӹ�¡��||�дѺ��";
		$heading_text[7] = "<**3**>�Ԫҡ��|�ç|�س�ز�";
		$heading_text[8] = "<**3**>�Ԫҡ��||����Ǫҭ";
		$heading_text[9] = "<**3**>�Ԫҡ��|�ӹҭ|��þ����";
		$heading_text[10] = "<**3**>�Ԫҡ��|�ӹҭ|���";
		$heading_text[11] = "<**3**>�Ԫҡ��|��Ժѵ�|���";
		if ($NOT_LEVEL_NO_O4=="Y") {
			$heading_text[12] = "<**4**>�����||������";
			$heading_text[13] = "<**4**>�����|�ӹҭ|�ҹ";
			$heading_text[14] = "<**4**>�����|��Ժѵ�|�ҹ";
			$heading_text[15] = "|���|";
		} else {
			$heading_text[12] = "<**4**>�����|�ѡ��|�����";
			$heading_text[13] = "<**4**>�����||������";
			$heading_text[14] = "<**4**>�����|�ӹҭ|�ҹ";
			$heading_text[15] = "<**4**>�����|��Ժѵ�|�ҹ";
			$heading_text[16] = "|���|";
		}
	}

	$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
	
	$data_align = array("C","L","C","R","R","R","R","R","R","R","R","R","R","R","R","R","R");

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
	$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[10] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[11] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	if ($ISCS_FLAG!=1) {
		$column_function[12] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[13] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[14] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[15] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		if ($NOT_LEVEL_NO_O4!="Y") {
			$column_function[16] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
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