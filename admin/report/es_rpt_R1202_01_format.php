<?
//	fix head for rpt_R007005

	$heading_width[] = "15";
	$heading_width[] = "15";
	$heading_width[] = "50";
	$heading_width[] = "15";
	$heading_width[] = "15";
	$heading_width[] = "15";
	$heading_width[] = "15";
	$heading_width[] = "15";
	$heading_width[] = "15";
	$heading_width[] = "15";
	$heading_width[] = "15";
	$heading_width[] = "15";
	$heading_width[] = "15";
	$heading_width[] = "15";
	$heading_width[] = "15";
	$heading_width[] = "15";
	$heading_width[] = "50";
	
	$heading_text[] = "���|";
	$heading_text[] = "�Ţ�����˹�|";
	$heading_text[] = "���� - ʡ��|";
	$heading_text[] = "<**1**>�һ���|�ѹ���";
	$heading_text[] = "<**1**>�һ���|���";
	$heading_text[] = "<**1**>�һ���|����";
	$heading_text[] = "<**2**>�ҡԨ|�ѹ���";
	$heading_text[] = "<**2**>�ҡԨ|���";
	$heading_text[] = "<**2**>�ҡԨ|����";
	$heading_text[] = "��� ����/�Ԩ|(����)";
	$heading_text[] = "<**3**>���|�ѹ���";
	$heading_text[] = "<**3**>���|���";
	$heading_text[] = "<**4**>�ѡ��͹|�ѹ���";
	$heading_text[] = "<**4**>�ѡ��͹|���";
	$heading_text[] = "<**5**>����|�ѹ���";
	$heading_text[] = "<**5**>����|���";
	$heading_text[] = "�����˵�|";

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

	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");

	$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");	

	$data_align = array("C","C","L","C","C","C","C","C","C","C","C","C","C","C","C","C","L");
	
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