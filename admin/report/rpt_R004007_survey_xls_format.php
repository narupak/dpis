<?
//	fix head for rpt_R004007

	$heading_width[0] = 25;
	$heading_width[1] = 25;
	$heading_width[2] = 8;		
	$heading_width[3] = 25;
	$heading_width[4] = 8;
	$heading_width[5] = 5;
	$heading_width[6] = 10;
	if ($SURVEY_NO==3) {
		$heading_width[7] = 5;
		$col = 7;
	} elseif ($SURVEY_NO==4) {
		$col = 6;
	}
	$heading_width[$col+1] =  10;
	$heading_width[$col+2] = 10;
	$heading_width[$col+3] = 12;
	$heading_width[$col+4] = 12;
	$heading_width[$col+5] = 8;
	$heading_width[$col+6] = 8;

	$heading_text[0] = "<**1**>��á�˹����˹���ШѴ����Ҫ��� ��� �ú.|<**1**>����º����Ҫ��þ����͹ �.�.2535 (��͹ 11 �ѹ�Ҥ� 2551)|���͵��˹����§ҹ|(�дѺ 7 ���� 7�)|||";
	$heading_text[1] = "<**1**>��á�˹����˹���ШѴ����Ҫ��� ��� �ú.|<**1**>����º����Ҫ��þ����͹ �.�.2535 (��͹ 11 �ѹ�Ҥ� 2551)|����-���ʡ��|����ç���˹�|(1)||";
	$heading_text[2] = "<**1**>��á�˹����˹���ШѴ����Ҫ��� ��� �ú.|<**1**>����º����Ҫ��þ����͹ �.�.2535 (��͹ 11 �ѹ�Ҥ� 2551)|���˹�|�Ţ���|||";
	$heading_text[3] = "<**2**>��ç���˹� / ������ / �дѺ|<**2**>(������ѹ��� 11 �ѹ�Ҥ� 2557)|<**3**>����ç���˹觵�� (1) �Ѩ�غѹ��ç���˹����§ҹ� / �дѺ� / ���³���ػ�� ���� ���³���������|���͵��˹����§ҹ|||";
	$heading_text[4] = "<**2**>��ç���˹� / ������ / �дѺ|<**2**>(������ѹ��� 11 �ѹ�Ҥ� 2557)|<**3**>����ç���˹觵�� (1) �Ѩ�غѹ��ç���˹����§ҹ� / �дѺ� / ���³���ػ�� ���� ���³���������|���˹�|�Ţ���||";
	if ($SURVEY_NO==3) {
		$heading_text[5] = "<**2**>��ç���˹� / ������ / �дѺ|<**2**>(������ѹ��� 11 �ѹ�Ҥ� 2557)|<**3**>����ç���˹觵�� (1) �Ѩ�غѹ��ç���˹����§ҹ� / �дѺ� / ���³���ػ�� ���� ���³���������|<**4**>�дѺ���˹�|��||";
		$heading_text[6] = "<**2**>��ç���˹� / ������ / �дѺ|<**2**>(������ѹ��� 11 �ѹ�Ҥ� 2557)|<**3**>����ç���˹觵�� (1) �Ѩ�غѹ��ç���˹����§ҹ� / �дѺ� / ���³���ػ�� ���� ���³���������|<**4**>�дѺ���˹�|�ѹ����觵��|����ç|���˹� ��";
		$heading_text[7] = "<**2**>��ç���˹� / ������ / �дѺ|<**2**>(������ѹ��� 11 �ѹ�Ҥ� 2557)|<**3**>����ç���˹觵�� (1) �Ѩ�غѹ��ç���˹����§ҹ� / �дѺ� / ���³���ػ�� ���� ���³���������|<**4**>�дѺ���˹�|��||";
		$heading_width[7] = 5;
	} elseif ($SURVEY_NO==4) {
		$heading_text[5] = "<**2**>��ç���˹� / ������ / �дѺ|<**2**>(������ѹ��� 11 �ѹ�Ҥ� 2557)|<**3**>����ç���˹觵�� (1) �Ѩ�غѹ��ç���˹����§ҹ� / �дѺ� / ���³���ػ�� ���� ���³���������|<**4**>�дѺ���˹�|��||";
		$heading_text[6] = "<**2**>��ç���˹� / ������ / �дѺ|<**2**>(������ѹ��� 11 �ѹ�Ҥ� 2557)|<**3**>����ç���˹觵�� (1) �Ѩ�غѹ��ç���˹����§ҹ� / �дѺ� / ���³���ػ�� ���� ���³���������|<**4**>�дѺ���˹�|�ѹ����觵��|����ç|���˹� ��";
	}
	$heading_text[$col+1] = "<**2**>��ç���˹� / ������ / �дѺ|<**2**>(������ѹ��� 11 �ѹ�Ҥ� 2557)|<**3**>����ç���˹觵�� (1) �Ѩ�غѹ��ç���˹����§ҹ� / �дѺ� / ���³���ػ�� ���� ���³���������|��ǹ��ҧ|||";
	$heading_text[$col+2] = "<**2**>��ç���˹� / ������ / �дѺ|<**2**>(������ѹ��� 11 �ѹ�Ҥ� 2557)|<**3**>����ç���˹觵�� (1) �Ѩ�غѹ��ç���˹����§ҹ� / �дѺ� / ���³���ػ�� ���� ���³���������|��ǹ��ҧ���|������Ҥ||";
	$heading_text[$col+3] = "<**2**>��ç���˹� / ������ / �дѺ|<**2**>(������ѹ��� 11 �ѹ�Ҥ� 2557)|<**3**>����ç���˹觵�� (1) �Ѩ�غѹ��ç���˹����§ҹ� / �дѺ� / ���³���ػ�� ���� ���³���������|��ǹ�����Ҥ|(�ѧ��Ѵ/�����)||";
	$heading_text[$col+4] = "<**2**>��ç���˹� / ������ / �дѺ|<**2**>(������ѹ��� 11 �ѹ�Ҥ� 2557)|<**3**>����ç���˹觵�� (1) �Ѩ�غѹ��ç���˹����§ҹ� / �дѺ� / ���³���ػ�� ���� ���³���������|��� �|(�ô�к�)||";
	$heading_text[$col+5] = "<**2**>��ç���˹� / ������ / �дѺ|<**2**>(������ѹ��� 11 �ѹ�Ҥ� 2557)|<**3**>����ç���˹觵�� (1) �Ѩ�غѹ��ç���˹����§ҹ� / �дѺ� / ���³���ػ�� ���� ���³���������|<**5**>������³�����Ҫ���|�� �.�.|�������³|";
	$heading_text[$col+6] = "<**2**>��ç���˹� / ������ / �дѺ|<**2**>(������ѹ��� 11 �ѹ�Ҥ� 2557)|<**3**>����ç���˹觵�� (1) �Ѩ�غѹ��ç���˹����§ҹ� / �дѺ� / ���³���ػ�� ���� ���³���������|<**5**>������³�����Ҫ���|���³|�����|";

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
	$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	if ($SURVEY_NO==3) {
		$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$col = 7;
	} elseif ($SURVEY_NO==4) {
		$col = 6;
	}
	$column_function[$col+1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$col+2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$col+3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$col+4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$col+5] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$column_function[$col+6] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	
	$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C");

	$data_align = array("L", "L", "C", "L", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C");

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