<?
//	fix head for rpt_R007001

	if ($have_pic) {
		$heading_width[] = "20";
		$heading_width[] = "45";
	} else {
		$heading_width[] = "65";
	}
	$heading_width[] = "15";
	$heading_width[] = "15";
	$heading_width[] = "15";
	$heading_width[] = "15";
	$heading_width[] = "15";
	$heading_width[] = "15";
	$heading_width[] = "17";
	$heading_width[] = "15";
	$heading_width[] = "17";
	$heading_width[] = "17";
	$heading_width[] = "60";
	
    if ($have_pic) {
		$heading_text[] = "�ٻ|";
		$heading_text[] = "���� - ʡ��|";
	} else {
		$heading_text[] = "���� - ʡ��|";
	}
	$heading_text[] = "<**1**>�һ���|�ѹ�ӡ��";
	$heading_text[] = "<**1**>�һ���|�ӹǹ����";
	$heading_text[] = "<**2**>�ҡԨ|�ѹ�ӡ��";
	$heading_text[] = "<**2**>�ҡԨ|�ӹǹ����";
	$heading_text[] = "<**3**>����һ��� �Ԩ|�ѹ�ӡ��";
    $heading_text[] = "<**3**>����һ��� �Ԩ|�ӹǹ����";
	$heading_text[] = "���|(�ѹ�ӡ��)";
	$heading_text[] = "<**4**>�Ҿѡ��͹|�ѹ�ӡ��";
	$heading_text[] = "<**4**>�Ҿѡ��͹|�ӹǹ����";
	$heading_text[] = "������|(�ѹ�ӡ��)";
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
	if ($have_pic) {
		$column_function[] = "";
		$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	} else {
		$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	} 
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
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");

	if($have_pic)
	$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C");
	else
	$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C");
	
	if($have_pic)
	$data_align = array("C","L","R","R","R","R","R","R","R","R","R","R","L");
	else
	$data_align = array("L","R","R","R","R","R","R","R","R","R","R","L");

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