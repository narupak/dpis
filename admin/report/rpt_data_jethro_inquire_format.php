<?

	$heading_width[] = "15";
	$heading_width[] = "17";
	$heading_width[] = "15";
	$heading_width[] = "40";
	$heading_width[] = "20";
	$heading_width[] = "15";
	$heading_width[] = "15";
	$heading_width[] = "40";
	$heading_width[] = "20";
	$heading_width[] = "15";
	$heading_width[] = "15";
	$heading_width[] = "15";
	$heading_width[] = "15";
	$heading_width[] = "17";
	$heading_width[] = "15";
	
	$heading_text[] = "<**1**>��þԨ�óҢͧ��С�����á�˹����˹��дѺ�٧|���駷��";
	$heading_text[] = "<**1**>��þԨ�óҢͧ��С�����á�˹����˹��дѺ�٧|�ѹ���";
	$heading_text[] = "<**1**>��þԨ�óҢͧ��С�����á�˹����˹��дѺ�٧|<**1**>���˹����|���˹��Ţ���";
	$heading_text[] = "<**1**>��þԨ�óҢͧ��С�����á�˹����˹��дѺ�٧|<**1**>���˹����|��ǹ�Ҫ���/���͵��˹�㹡�ú����çҹ";
	$heading_text[] = "<**1**>��þԨ�óҢͧ��С�����á�˹����˹��дѺ�٧|<**1**>���˹����|��§ҹ";
	$heading_text[] = "<**1**>��þԨ�óҢͧ��С�����á�˹����˹��дѺ�٧|<**1**>���˹����|������";
	$heading_text[] = "<**1**>��þԨ�óҢͧ��С�����á�˹����˹��дѺ�٧|<**1**>���˹����|�дѺ";
	$heading_text[] = "<**1**>��þԨ�óҢͧ��С�����á�˹����˹��дѺ�٧|<**2**>���˹觻�Ѻ��ا����|��ǹ�Ҫ���/���͵��˹�㹡�ú����çҹ";
	$heading_text[] = "<**1**>��þԨ�óҢͧ��С�����á�˹����˹��дѺ�٧|<**2**>���˹觻�Ѻ��ا����|��§ҹ";
	$heading_text[] = "<**1**>��þԨ�óҢͧ��С�����á�˹����˹��дѺ�٧|<**2**>���˹觻�Ѻ��ا����|������";
	$heading_text[] = "<**1**>��þԨ�óҢͧ��С�����á�˹����˹��дѺ�٧|<**2**>���˹觻�Ѻ��ا����|�дѺ";
	$heading_text[] = "<**1**>��þԨ�óҢͧ��С�����á�˹����˹��дѺ�٧|�š�þԨ�ó�";
	$heading_text[] = "<**2**>��þԨ�óҢͧ �.�.�. ��з�ǧ|���駷��";
	$heading_text[] = "<**2**>��þԨ�óҢͧ �.�.�. ��з�ǧ|�ѹ���";
	$heading_text[] = "<**2**>��þԨ�óҢͧ �.�.�. ��з�ǧ|�š�þԨ�ó�";  

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
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");

	$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
	
	$data_align = array("C","C","C","L","L","C","C","L","L","C","C","C","C","C","C");

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
		$arr_column_align = explode(",",$arrbuff[3]);		// align
	}
	
	$total_show_width = 0;
	for($iii=0; $iii < count($arr_column_width); $iii++) 	if ($arr_column_sel[$iii]==1) $total_show_width += (int)$arr_column_width[$iii];

?>