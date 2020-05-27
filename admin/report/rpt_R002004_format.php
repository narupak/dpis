<?
//	fix head for rpt_R002004

	$heading_width[0] = "107";
	$heading_width[1] = "18";
	$heading_width[2] = "18";
	$heading_width[3] = "18";
	$heading_width[4] = "18";
	$heading_width[5] = "18";

	if (!$heading_name) $heading_name="����� heading_name";
	$heading_text[0] = "$heading_name|";
	if ($SESS_DEPARTMENT_NAME=="�����û���ͧ") {
		$heading_text[1] = "<**1**>��ǧ����|21 - 30";
		$heading_text[2] = "<**1**>��ǧ����|31 - 40";
		$heading_text[3] = "<**1**>��ǧ����|41 - 50";
		$heading_text[4] = "<**1**>��ǧ����|51 - 60";
		$heading_text[5] = "���������|";

		$heading_align = array("C","C","C","C","C","C");	
		
		$data_align = array("L","R","R","R","R","R");
	} else {
		$heading_width[6] = "18";
		$heading_width[7] = "18";
		$heading_width[8] = "18";
		$heading_width[9] = "18";
                $heading_width[10] = "18";

		$heading_text[1] = "<**1**>��ǧ����|<=24";
		$heading_text[2] = "<**1**>��ǧ����|25 - 29";
		$heading_text[3] = "<**1**>��ǧ����|30 - 34";
		$heading_text[4] = "<**1**>��ǧ����|35 - 39";
		$heading_text[5] = "<**1**>��ǧ����|40 - 44";
		$heading_text[6] = "<**1**>��ǧ����|45 - 49";
		$heading_text[7] = "<**1**>��ǧ����|50 - 54";
		$heading_text[8] = "<**1**>��ǧ����|>=55";
		$heading_text[9] = "���|(�ӹǹ)";
                $heading_text[10] = "���������|";

		$heading_align = array("C","C","C","C","C","C","C","C","C","C","C");	
		
		$data_align = array("L","R","R","R","R","R","R","R","R","R","R");
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
	$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	if ($SESS_DEPARTMENT_NAME=="�����û���ͧ") {
		$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2");
	} else {
		$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
                $column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[10] = (($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2");
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