<?
//	fix head for P0406 xls
		$heading_width = array(6,30,15,25,25,15,13,8,10,10,10,20);
		$heading_text = array("�ӴѺ|���",$FULLNAME_HEAD."|","�Ţ��Шӵ��|��ЪҪ�",$COM_HEAD_01."�ѧ�Ѵ",$COM_HEAD_01."���˹�","<**1**>������|���˹�","<**1**>|�дѺ���˹�","<**1**>|�Ţ���","�Թ��͹","��ҵͺ᷹|�����","������Ѻ|�Թ��͹","�����˵�|");
		$heading_align = array('C','C','C','C','C','C','C','C','C','C','C');
		$column_function = array( (($NUMBER_DISPLAY==2)?"TNUM":"ENUM"),(($NUMBER_DISPLAY==2)?"TNUM":"ENUM"),(($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x"),(($NUMBER_DISPLAY==2)?"TNUM":"ENUM"),(($NUMBER_DISPLAY==2)?"TNUM":"ENUM"),(($NUMBER_DISPLAY==2)?"TNUM":"ENUM"),(($NUMBER_DISPLAY==2)?"TNUM":"ENUM"),(($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x"),(($NUMBER_DISPLAY==2)?"TNUM":"ENUM"),(($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2"),(($NUMBER_DISPLAY==2)?"TNUM":"ENUM"),(($NUMBER_DISPLAY==2)?"TNUM":"ENUM") );
		$data_align = array("C","L","C","C","C","L","C","L","R","R","R","L");
	
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