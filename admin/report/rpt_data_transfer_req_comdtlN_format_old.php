<?
//	fix head for P0203

		if($COM_TYPE=="0101"  || $COM_TYPE=="5011"){	//�ͺ�觢ѹ��
		$heading_width[0] = "9";
		$heading_width[1] = "40";
		$heading_width[2] = "25";
		$heading_width[3] = "20";
		$heading_width[4] = "9";
		$heading_width[5] = "28";
		$heading_width[6] = "35";
		$heading_width[7] = "30";
		$heading_width[8] = "20";
		$heading_width[9] = "17";
		$heading_width[10] = "17";
		$heading_width[11] = "18";
		$heading_width[12] = "20";

		$heading_text[0] = "�ӴѺ|���";
		$heading_text[1] = "����/���ʡ�� (Ǵ�./|�Ţ��Шӵ�ǻ�ЪҪ�)";
		$heading_text[2] = "�ز�/�Ң�/|ʶҹ�֡��";
		$heading_text[3] = "<**1**>�ͺ�觢ѹ��|���˹�";
		$heading_text[4] = "<**1**>�ͺ�觢ѹ��|�ӴѺ|���";
		$heading_text[5] = "<**1**>�ͺ�觢ѹ��|��С�ȼš���ͺ�ͧ";
		$heading_text[6] = "<**2**>���˹������ǹ�Ҫ��÷���è��觵��|���˹�/�ѧ�Ѵ";
		$heading_text[7] = "<**2**>���˹������ǹ�Ҫ��÷���è��觵��|���˹觻�����";
		$heading_text[8] = "<**2**>���˹������ǹ�Ҫ��÷���è��觵��|�дѺ";
		$heading_text[9] = "<**2**>���˹������ǹ�Ҫ��÷���è��觵��|�Ţ���";
		$heading_text[10] = "|�Թ��͹";
		$heading_text[11] = "������ѹ���|";
		$heading_text[12] = "�����˵�|";
		
		$heading_align = array('C','C','C','C','C','C','C','C','C','C','C','C','C');
	}elseif(in_array($COM_TYPE, array("0102", "0103", "0504", "0604", "0704", "0803"))){	//��èؼ�����Ѻ�Ѵ���͡
		$heading_width[0] = "10";
		$heading_width[1] = "43";
		$heading_width[2] = "40";
		$heading_width[3] = "44";
		$heading_width[4] = "25";
		$heading_width[5] = "17";
		$heading_width[6] = "17";
		$heading_width[7] = "20";
		$heading_width[8] = "24";
		$heading_width[9] = "50";

		$heading_text[0] = "�ӴѺ|���";
		$heading_text[1] = "����/���ʡ�� (�ѹ��͹���Դ|�Ţ��Шӵ�ǻ�ЪҪ�)";
		$heading_text[2] = "�ز�/�Ң�/ʶҹ�֡��|";
		$heading_text[3] = "<**1**>���˹������ǹ�Ҫ��÷���è��觵��|���˹�/�ѧ�Ѵ";
		$heading_text[4] = "<**1**>���˹������ǹ�Ҫ��÷���è��觵��|���˹觻�����";
		$heading_text[5] = "<**1**>���˹������ǹ�Ҫ��÷���è��觵��|�дѺ";
		$heading_text[6] = "<**1**>���˹������ǹ�Ҫ��÷���è��觵��|�Ţ���";
		$heading_text[7] = "|�Թ��͹";
		$heading_text[8] = "������ѹ���|";
		$heading_text[9] = "�����˵�|";
		
		$heading_align = array('C','C','C','C','C','C','C','C','C','C');
	}elseif(in_array($COM_TYPE, array("�觵���ѡ���Ҫ���᷹","�觵������ѡ�ҡ��㹵��˹�"))){
		if($COM_TYPE == "�觵���ѡ���Ҫ���᷹"){
			$heading_name4="�ѡ���Ҫ���᷹";
		}elseif($COM_TYPE == "�觵������ѡ�ҡ��㹵��˹�"){
			$heading_name4="�ѡ�ҡ��㹵��˹�";
		}
		$heading_width[0] = "10";
		$heading_width[1] = "45";
		$heading_width[2] = "40";
		$heading_width[3] = "25";
		$heading_width[4] = "25";
		$heading_width[5] = "40";
		$heading_width[6] = "25";
		$heading_width[7] = "13";
		$heading_width[8] = "25";
		$heading_width[9] = "25";
		$heading_width[10] = "20";
		$heading_width[11] = "60";

		$heading_text[0] = "�ӴѺ|���";
		$heading_text[1] = "����/���ʡ��|";
		$heading_text[2] = "���˹������ǹ�Ҫ���|���˹�/�ѧ�Ѵ";
		$heading_text[3] = "���˹������ǹ�Ҫ���|���˹觻�����";
		$heading_text[4] = "���˹������ǹ�Ҫ���|�дѺ";
		$heading_text[5] = "$heading_name4�|���˹�/�ѧ�Ѵ";
		$heading_text[6] = "$heading_name4�|���˹觻�����";
		$heading_text[7] = "$heading_name4�|�дѺ";
		$heading_text[8] = "$heading_name4�|�Ţ���";
		$heading_text[9] = "������ѹ���|";
		$heading_text[10] = "�֧�ѹ���|";
		$heading_text[11] = "�����˵�|";
		
		$heading_align = array('C','C','C','C','C','C','C','C','C','C','C','C');
		
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
	$column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2");
	$column_function[10] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[11] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[12] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	
	$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C");
	
	$data_align = array("C","L","C","C","L","L","L","C","L","R","C","C","C");

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