<?
//	fix head for 0_rpttest_rtf_format
//	$set_of_colors = "C0362C^FF8642^F4DCB5^816C5B^C3B7AC^E7E3D7^668D3C^B1DDA1^E5F3CF^0097AC^3CD6E6^97EAF4^007996^06C2F4^FAD8FA";	// pattern :: BRACH
//	$set_of_colors = "646464^929292^BEBEBE^695D54^91867E^BDB6B0^513E3E^846D74^B7A6AD^3C5B59^4F8D97^74A18E^000000^003E51^587992";	// pattern :: CLASSIC
//	$set_of_colors = "1F5647^78AD95^82C891^004159^005F8A^5B97B1^191919^7C98AE^BCC9D6^815F3E^D2BE96^E3DCC0^9FA0A3^9E8D81^B7A6AD";	// pattern :: INDY

//	�������ҧ ����� % �������ѹ = 100 �ʴ�����������ҧ
	$heading_width[0] = "25";
	$heading_width[1] = "10";
	$heading_width[2] = "10";
	$heading_width[3] = "10";
	$heading_width[4] = "10";
	$heading_width[5] = "10";
	$heading_width[6] = "10";
	$heading_width[7] = "15";
	
	$page_total_w = array_sum($heading_width);

	$heading_text=array("<**0**>��͸Ժ��|<**0^**>","<**1**>�ش 1|������� 1","<**1**>�ش 1|������� 2","<**2**>�ش 2|������� 3","<**2**>�ش 2|������� 4","<**2**>�ش 2|������� 5","<**2**>�ش 2|������� 6","<**3**>���|<**3^**>");

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
	$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$column_function[7] = (($NUMBER_DISPLAY==2)?"SUM-1-2-3-4-5-6|TNUM-z":"SUM-1-2-3-4-5-6|ENUM-z");
	
	$heading_align = array("C","C","C","C","C","C","C","C");	
	
	$data_align = array("L","C","C","C","C","C","C","C");

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