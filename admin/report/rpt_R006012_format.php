<?
//	fix head for rpt_R006012

	$heading_width[0] = "10";
	$heading_width[1] = "117";
	$heading_width[2] = "25";
	$heading_width[3] = "25";
	$heading_width[4] = "25";
	$heading_width[5] = "35";
	
	$heading_text[0] = "�ӴѺ|���";
	$heading_text[1] = "�ѧ�Ѵ|";
	$heading_text[2] = "<**1**>�ѧ�Ѵ|������";
	$heading_text[3] = "<**2**>��ҧ���Թ|���˹�";
	$heading_text[4] = "<**2**>��ҧ���Թ|������";
	$heading_text[5] = "<**3**>��ҧ������Թ|���˹�";
	$heading_text[6] = "<**3**>��ҧ������Թ|������";
	$heading_text[7] = "<**4**>���|���˹�";
	$heading_text[8] = "<**4**>���|������";

	$heading_align = array("C","C","C","C","C","C","C","C","C");	
	
	$data_align = array("L", "C", "C", "C", "C", "C", "C", "C", "C");

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