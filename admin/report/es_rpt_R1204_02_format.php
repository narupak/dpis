<?php
    $heading_width[0] = '15';
    $heading_width[1] = '15';
    $heading_width[2] = '10';
    $heading_width[3] = '40';
    $heading_width[4] = '20';
    $heading_width[5] = '20';
    $heading_width[6] = '20';
    $heading_width[7] = '20';
    $heading_width[8] = '20';
    $heading_width[9] = '20';
    $heading_width[10] = '20';
    $heading_width[11] = '20';
    $heading_width[12] = '40';
    
    $heading_text[0] = '�ӴѺ|���';
    $heading_text[1] = '��Шӻ�';
    $heading_text[2] = '�ͺ';
    $heading_text[3] = '�ѹ���';
    $heading_text[4] = '<**1**>�Ҿѡ��͹|����';
    $heading_text[5] = '<**1**>�Ҿѡ��͹|��';
    $heading_text[6] = '<**1**>�Ҿѡ��͹|�������';
    $heading_text[7] = '�һ���';
    $heading_text[8] = '�ҡԨ��ǹ���';
    $heading_text[9] = '������';
    $heading_text[10] = '�����';
    $heading_text[11] = '�Ҵ|�Ҫ���';
    $heading_text[12] = '�����˵�';
    
    $column_function[0] = "TSTR";
    $column_function[1] = "TSTR";
    $column_function[2] = "TSTR";
    $column_function[3] = "TSTR";
    $column_function[4] = "TSTR";
    $column_function[5] = "TSTR";
    $column_function[6] = "TSTR";
    $column_function[7] = "TSTR";
    $column_function[8] = "TSTR";
    $column_function[9] = "TSTR";
    $column_function[10] = "TSTR";
    $column_function[11] = "TSTR";
    $column_function[12] = "TSTR";

    
    $heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C");
    $data_align = array("C","C","C","C","C","C","C","C","C","C","C","C","L");

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
