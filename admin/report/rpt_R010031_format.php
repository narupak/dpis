<?
//	fix head for rpt_R010031

	if ($ISCS_FLAG==1)
		$head_width = 15;
	else
		$head_width = 8;
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
	$heading_width[10] = $head_width;
	$heading_width[11] = $head_width;
	$heading_width[12] = $head_width;
	$heading_width[13] = $head_width;
	$heading_width[14] = $head_width;
	$heading_width[15] = $head_width;
	if ($ISCS_FLAG!=1) {
		$heading_width[16] = $head_width;
		$heading_width[17] = $head_width;
		$heading_width[18] = $head_width;
		$heading_width[19] = $head_width;
		$heading_width[20] = $head_width;
		$heading_width[21] = $head_width;
		$heading_width[22] = $head_width;
		$heading_width[23] = $head_width;
		$heading_width[24] = $head_width;
		$heading_width[25] = $head_width;
		$heading_width[26] = $head_width;
		$heading_width[27] = $head_width;
		if ($NOT_LEVEL_NO_O4!="Y") {
			$heading_width[28] = $head_width;
			$heading_width[29] = $head_width;
		}
	}
	
	if (!$heading_name) $heading_name="����� heading_name";
	if ($ISCS_FLAG==1) {
		$heading_text[0] = "|�ӴѺ|";
		$heading_text[1] = "|$heading_name|";
		$heading_text[2] = "<**1**>������|<**6**>�������٧|���";
		$heading_text[3] = "<**1**>������|<**6**>�������٧|˭ԧ";
		$heading_text[4] = "<**1**>������|<**7**>�����õ�|���";
		$heading_text[5] = "<**1**>������|<**7**>�����õ�|˭ԧ";
		$heading_text[6] = "<**2**>�ӹ�¡��|<**8**>�ӹ�¡���٧|���";
		$heading_text[7] = "<**2**>�ӹ�¡��|<**8**>�ӹ�¡���٧|˭ԧ";
		$heading_text[8] = "<**2**>�ӹ�¡��|<**9**>�ӹ�¡�õ�|���";
		$heading_text[9] = "<**2**>�ӹ�¡��|<**9**>�ӹ�¡�õ�|˭ԧ";
		$heading_text[10] = "<**3**>�Ԫҡ��|<**10**>�ç�س�ز�|���";
		$heading_text[11] = "<**3**>�Ԫҡ��|<**10**>�ç�س�ز�|˭ԧ";
		$heading_text[12] = "<**3**>�Ԫҡ��|<**11**>����Ǫҭ|���";
		$heading_text[13] = "<**3**>�Ԫҡ��|<**11**>����Ǫҭ|˭ԧ";
		$heading_text[14] = "<**5**>���|<**19^**>|���";
		$heading_text[15] = "<**5**>���|<**19^**>|˭ԧ";
		$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");	
		
		$data_align = array("C","L","R","R","R","R","R","R","R","R","R","R","R","R","R","R");
	} else {
		$heading_text[0] = "|�ӴѺ||";
		$heading_text[1] = "|$heading_name||";
		$heading_text[2] = "<**1**>������|<**6**>|<**6^**>�������٧|���";
		$heading_text[3] = "<**1**>������|<**6**>|<**6^**>�������٧|˭ԧ";
		$heading_text[4] = "<**1**>������|<**7**>|<**7^**>�����õ�|���";
		$heading_text[5] = "<**1**>������|<**7**>|<**7^**>�����õ�|˭ԧ";
		$heading_text[6] = "<**2**>�ӹ�¡��|<**8**>|<**8^**>�ӹ�¡���٧|���";
		$heading_text[7] = "<**2**>�ӹ�¡��|<**8**>|<**8^**>�ӹ�¡���٧|˭ԧ";
		$heading_text[8] = "<**2**>�ӹ�¡��|<**9**> |<**9^**>�ӹ�¡�õ�|���";
		$heading_text[9] = "<**2**>�ӹ�¡��|<**9**> |<**9^**>�ӹ�¡�õ�|˭ԧ";
		$heading_text[10] = "<**3**>�Ԫҡ��|<**10**>�ç|<**10^**>�س�ز�|���";
		$heading_text[11] = "<**3**>�Ԫҡ��|<**10**>�ç|<**10^**>�س�ز�|˭ԧ";
		$heading_text[12] = "<**3**>�Ԫҡ��|<**11**> |<**11^**>����Ǫҭ|���";
		$heading_text[13] = "<**3**>�Ԫҡ��|<**11**> |<**11^**>����Ǫҭ|˭ԧ";
		$heading_text[14] = "<**3**>�Ԫҡ��|<**12**>�ӹҭ|<**12^**>��þ����|���";
		$heading_text[15] = "<**3**>�Ԫҡ��|<**12**>�ӹҭ|<**12^**>��þ����|˭ԧ";
		$heading_text[16] = "<**3**>�Ԫҡ��|<**13**>�ӹҭ|<**13^**>���|���";
		$heading_text[17] = "<**3**>�Ԫҡ��|<**13**>�ӹҭ|<**13^**>���|˭ԧ";
		$heading_text[18] = "<**3**>�Ԫҡ��|<**14**>��Ժѵ�|<**14^**>���|���";
		$heading_text[19] = "<**3**>�Ԫҡ��|<**14**>��Ժѵ�|<**14^**>���|˭ԧ";
		if ($NOT_LEVEL_NO_O4!="Y") {
			$heading_text[20] = "<**4**>�����|<**15**>�ѡ��|<**15^**>�����|���";
			$heading_text[21] = "<**4**>�����|<**15**>�ѡ��|<**15^**>�����|˭ԧ";
			$heading_text[22] = "<**4**>�����|<**16**> |<**16^**>������|���";
			$heading_text[23] = "<**4**>�����|<**16**> |<**16^**>������|˭ԧ";
			$heading_text[24] = "<**4**>�����|<**17**>�ӹҭ|<**17^**>�ҹ|���";
			$heading_text[25] = "<**4**>�����|<**17**>�ӹҭ|<**17^**>�ҹ|˭ԧ";
			$heading_text[26] = "<**4**>�����|<**18**>��Ժѵ�|<**18^**>�ҹ|���";
			$heading_text[27] = "<**4**>�����|<**18**>��Ժѵ�|<**18^**>�ҹ|˭ԧ";
			$heading_text[28] = "<**5**>|<**19^**>���|<**19^**>|���";
			$heading_text[29] = "<**5**>|<**19^**>���|<**19^**>|˭ԧ";
		} else {
			$heading_text[20] = "<**4**>�����|<**16**> |<**16^**>������|���";
			$heading_text[21] = "<**4**>�����|<**16**> |<**16^**>������|˭ԧ";
			$heading_text[22] = "<**4**>�����|<**17**>�ӹҭ|<**17^**>�ҹ|���";
			$heading_text[23] = "<**4**>�����|<**17**>�ӹҭ|<**17^**>�ҹ|˭ԧ";
			$heading_text[24] = "<**4**>�����|<**18**>��Ժѵ�|<**18^**>�ҹ|���";
			$heading_text[25] = "<**4**>�����|<**18**>��Ժѵ�|<**18^**>�ҹ|˭ԧ";
			$heading_text[26] = "<**5**>|<**19^**>���|<**19^**>|���";
			$heading_text[27] = "<**5**>|<**19^**>���|<**19^**>|˭ԧ";
		}
		$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");	
		
		$data_align = array("C","L","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R");
	}

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
	$column_function[12] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[13] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[14] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[15] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	if ($ISCS_FLAG!=1) {
		$column_function[16] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[17] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[18] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[19] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[20] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[21] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[22] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[23] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[24] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[25] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[26] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[27] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		if ($NOT_LEVEL_NO_O4!="Y") {
			$column_function[28] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[29] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
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