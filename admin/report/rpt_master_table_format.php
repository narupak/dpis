<?
//	fix head for rpt_master_table_format.php
	if (!$orientation) $orientation="P";
	$self_path= $_SERVER['PHP_SELF'];
	$c = strrpos($self_path,"/admin/");
	$c1 = strpos($self_path,"/",$c+7);
//	echo "self_path=$self_path ($c) ($c1)<br>";
	if ($c1) $pref = "../"; else "";
//	echo "1..include file==>".$pref."data_forms/master_".strtolower(substr($table,4))."_preset.php  (".file_exists($pref."data_forms/master_".strtolower(substr($table,4))."_preset.php").")<br>";
	include($pref."data_forms/master_".strtolower(substr($table,4))."_preset.php");	// ��˹���� $key_index, $active_index, $seq_order_index
//	echo "2..include file==>".$pref."data_forms/master_".strtolower(substr($table,4))."_form.php  (".file_exists($pref."data_forms/master_".strtolower(substr($table,4))."_form.php").")<br>";
	include($pref."data_forms/master_".strtolower(substr($table,4))."_form.php"); // �Ѵ����� PER_ �͡ �ҵ���� form file

// ���ҧ ��� = 200 ����Ѻ A4 P  ��� 290 ����Ѻ A4 L
// A4 P ��Ҥӹǹ������ column �˹ ���¡��� 5 �������¹�� A4 L �ѵ��ѵ�
// A4 L ��Ҥӹǹ������ column �˹ ���¡��� 5 ����� 仨��Ҿ������͡��§ҹ�ӡ�û�Ѻ�͡
	$sum_w = 0; $rest_w = 0; $cnt_space=0;
	$arr_w = (array) null;
	for($i=0; $i < count($tab_width); $i++) {
		$w = (int)str_replace("pc","",str_replace("pl","",str_replace("px","",str_replace("%","",$tab_width[$i]))));
		$arr_w[] = $w;
	}
//	echo "1..arr_w=".implode(",",$arr_w).", tab_head=".implode(",",$tab_head)."<br>";
	for($i=0; $i < count($tab_head); $i++) {
		if ($tab_width[$i] == "") {
			$cnt_space++;
			$arr_w[$i] = -1;
		} else {
			if ($tab_head[$i] != $EDIT_TITLE && $tab_head[$i] != $DEL_TITLE) {
				$sum_w += $arr_w[$i];
			} else {
				$rest_w += $arr_w[$i];
			}
		}
	}
//	echo "2..arr_w=".implode(",",$arr_w)."<br>";
	$w_space = 100 - $sum_w - $rest_w; 	// ����Ҫ�ͧ ���������ʴ�� report �� ��ͧ DEL ��� EDIT ($rest_w) �����Ѻ ��ͧ��ҧ���������ʴ�
	$w_each_space = ($cnt_space > 0 ? $w_space / $cnt_space : 0);
	$w_radio = ($orientation=="P" ? 200/100 : 290/100);	// 200 = �������ҧ��д�� A4P, 290 = �������ҧ��д�� A4L
//	echo "w_space=$w_space, sum_w=$sum_w, rest_w=$rest_w, cnt_space=$cnt_space, w_each_space=$w_each_space, w_radio=$w_radio<br>";
	for($i=0; $i < count($tab_head); $i++) {
		if ($arr_w[$i] == -1) {
			$arr_w[$i] = floor($w_each_space * $w_radio);
		} else {
			$arr_w[$i] = floor($arr_w[$i] * $w_radio);
		}
	}
//	echo "3..arr_w=".implode(",",$arr_w)."<br>";
	$heading_text = (array) null;
	$heading_width = (array) null;
	$heading_align = (array) null;
	$data_align = (array) null;
	$column_function = (array) null;
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
	for($i=0; $i < count($tab_head); $i++) {
		if ($tab_head[$i] != $EDIT_TITLE && $tab_head[$i] != $DEL_TITLE) {
			$heading_text[] = trim(str_replace("||","#",$tab_head[$i]));		//." (".$arr_w[$i].")";
			$heading_width[] = (string)$arr_w[$i];
			$heading_align[] = "C";
			$data_align[] = strtoupper(substr($tab_align[$i],0,1)); // align � pdf �Ѻ excel �������ѡ�õ��˹�� R L C
			$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		}
	}
//	echo "width=".implode(",",$heading_width)." (".$COLUMN_FORMAT.")<br>";
	
	$total_head_width = 0;
	for($iii=0; $iii < count($heading_width); $iii++) 	$total_head_width += (int)$heading_width[$iii];
	
//	echo "FORMAT_SEQ=$FORMAT_SEQ (COLUMN_FORMAT=$COLUMN_FORMAT)<br>";
	if ($FORMAT_SEQ==99) {	// ��ͧ��˹��� element �������� form1 ����  <input type="hidden" name="COLUMN_FORMAT" value="<?=$COLUMN_FORMAT
		$arr_column_map = (array) null;
		$arr_column_sel = (array) null;
		for($i=0; $i < count($heading_text); $i++) {
			$arr_column_map[] = $i;		// link index �ͧ head 
			$arr_column_sel[] = 1;			// 1=�ʴ�	0=����ʴ�   �������ա�û�Ѻ����§ҹ �����ʴ����
		}
		$arr_column_width = $heading_width;		// �������ҧ
		$arr_column_align = $data_align;		// align
		$COLUMN_FORMAT = implode(",",$arr_column_map)."|".implode(",",$arr_column_sel)."|".implode(",",$arr_column_width)."|".implode(",",$arr_column_align);
	} else if (!$COLUMN_FORMAT) {	// ��ͧ��˹��� element �������� form1 ����  <input type="hidden" name="COLUMN_FORMAT" value="<?=$COLUMN_FORMAT
		$arr_column_map = (array) null;
		$arr_column_sel = (array) null;
		for($i=0; $i < count($heading_text); $i++) {
			$arr_column_map[] = $i;		// link index �ͧ head 
			$arr_column_sel[] = $default_sel[$i];			// 1=�ʴ�	0=����ʴ�   ��˹���� $col_map
		}
		$arr_column_width = $heading_width;		// �������ҧ
		$arr_column_align = $data_align;		// align
		$COLUMN_FORMAT = implode(",",$arr_column_map)."|".implode(",",$arr_column_sel)."|".implode(",",$arr_column_width)."|".implode(",",$arr_column_align);
	} else {
		$arr_column_map = (array) null;
		$arr_column_sel = (array) null;
		for($i=0; $i < count($heading_text); $i++) {
			$arr_column_map[] = $i;		// link index �ͧ head 
			$arr_column_sel[] = 1;			// 1=�ʴ�	0=����ʴ�   �������ա�û�Ѻ����§ҹ �����ʴ����
		}
		$arr_column_width = $heading_width;		// �������ҧ
		$arr_column_align = $data_align;		// align
		$orig_COLUMN_FORMAT = implode(",",$arr_column_map)."|".implode(",",$arr_column_sel)."|".implode(",",$arr_column_width)."|".implode(",",$arr_column_align);
		if ($COLUMN_FORMAT != $orig_COLUMN_FORMAT) {
			$arrbuff = explode("|",$COLUMN_FORMAT);
			$arr_column_map = explode(",",$arrbuff[0]);		// index �ͧ head �������
			$arr_column_sel = explode(",",$arrbuff[1]);	// 1=�ʴ�	0=����ʴ�
			$arr_column_width = explode(",",$arrbuff[2]);	// �������ҧ
			$arr_column_align = explode(",",$arrbuff[3]);		// align
		} else {
			$FORMAT_SEQ = 99;
		}
	}
	
	$total_show_width = 0;
	for($iii=0; $iii < count($arr_column_width); $iii++) 	if ($arr_column_sel[$iii]==1) $total_show_width += (int)$arr_column_width[$iii];
//	echo "arr_col_w=".implode(",",$arr_column_width)."<br>";
//	echo "width=".implode(",",$heading_width)." (".$COLUMN_FORMAT.")<br>";
?>