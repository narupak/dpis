<?
//	fix head for rpt_R005003

	$heading_width[0] = "10";
	$heading_width[1] = "42";
	$heading_width[2] = "10";
	$heading_width[3] = "10";
	$heading_width[4] = "10";
	$heading_width[5] = "10";
	$heading_width[6] = "10";
	$heading_width[7] = "10";
	$heading_width[8] = "10";
	$heading_width[9] = "10";
	$heading_width[10] = "10";
	$heading_width[11] = "10";
	$heading_width[12] = "10";
	$heading_width[13] = "10";
	$heading_width[14] = "10";
	$heading_width[15] = "10";
	$heading_width[16] = "10";
	$heading_width[17] = "10";
	$heading_width[18] = "10";	// ���
	$heading_width[19] = "10";	// ���
	$heading_width[20] = "10";	// ���
	$heading_width[21] = "20";	// �����˵�
	
	$heading_text[0] = "|�ӴѺ|";
	$heading_text[1] = "���/��ǹ�Ҫ���|�����º���|";
	$heading_text[2] = "<**1**>����ͧ�Ҫ��������ó�|<**1**>�.�.|�����";
	$heading_text[3] = "<**1**>����ͧ�Ҫ��������ó�|<**1**>�.�.|ʵ��";
	$heading_text[4] = "<**1**>����ͧ�Ҫ��������ó�|<**2**>�.�.|�����";
	$heading_text[5] = "<**1**>����ͧ�Ҫ��������ó�|<**2**>�.�.|ʵ��";
	$heading_text[6] = "<**1**>����ͧ�Ҫ��������ó�|<**3**>�.�.|�����";
	$heading_text[7] = "<**1**>����ͧ�Ҫ��������ó�|<**3**>�.�.|ʵ��";
	$heading_text[8] = "<**1**>����ͧ�Ҫ��������ó�|<**4**>�.�.|�����";
	$heading_text[9] = "<**1**>����ͧ�Ҫ��������ó�|<**4**>�.�.|ʵ��";
	$heading_text[10] = "<**1**>����ͧ�Ҫ��������ó�|<**5**>�.�.|�����";
	$heading_text[11] = "<**1**>����ͧ�Ҫ��������ó�|<**5**>�.�.|ʵ��";
	$heading_text[12] = "<**1**>����ͧ�Ҫ��������ó�|<**6**>�.�.|�����";
	$heading_text[13] = "<**1**>����ͧ�Ҫ��������ó�|<**6**>�.�.|ʵ��";
	$heading_text[14] = "<**1**>����ͧ�Ҫ��������ó�|<**7**>�.�.|�����";
	$heading_text[15] = "<**1**>����ͧ�Ҫ��������ó�|<**7**>�.�.|ʵ��";
	$heading_text[16] = "<**1**>����ͧ�Ҫ��������ó�|<**8**>�.�.|�����";
	$heading_text[17] = "<**1**>����ͧ�Ҫ��������ó�|<**8**>�.�.|ʵ��";
	$heading_text[18] = "<**2**>���|<**9**>|�����";
	$heading_text[19] = "<**2**>���|<**9**>|ʵ��";
	$heading_text[20] = "<**2**>���|<**9**>|���";
	$heading_text[21] = "|�����˵�|";

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
	$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[10] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[11] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[12] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[13] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[14] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[15] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[16] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[17] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[18] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[19] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[20] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[21] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");

	$heading_align = array('C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C');	
	
	$data_align = array("C", "L", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C");

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