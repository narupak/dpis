<?
//	fix head for rpt_R009002

	$heading_width[0] = "15";
	$heading_width[1] = "70";
	$heading_width[2] = "67";
	$heading_width[3] = "60";
	$heading_width[4] = "25";
	$heading_width[5] = "25";
	$heading_width[6] = "25";
	
	$heading_text[0] = "�ӴѺ";
	$heading_text[1] = "���� - ʡ��";
	$heading_text[2] = "���˹�";
	$heading_text[3] = "���ö��";
	$heading_text[4] = "�дѺ����˹�";
	$heading_text[5] = "�š�û����Թ";
	$heading_text[6] = "�ŵ�ҧ";

	$heading_align = array("C","C","C","C","C","C","C");	
	
	$data_align = array("R","L","L","L","R","R","R");

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