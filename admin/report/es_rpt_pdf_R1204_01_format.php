<?php
    $heading_width[0] = '25';
    $heading_width[1] = '30';
    $heading_width[2] = '20';
    $heading_width[3] = '120';
    
    $heading_text[0] = '�է�����ҳ';
    $heading_text[1] = '�����������';
    $heading_text[2] = '�ӹǹ�ѹ';
    $heading_text[3] = '�����˵�';
    
    $column_function[0] = "TSTR";
    $column_function[1] = "TSTR";
    $column_function[2] = "ENUM";
    $column_function[3] = "TSTR";
    
    $heading_align = array("C","C","C","C");
	
    $data_align = array("L","L","C","L","C");
    
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
