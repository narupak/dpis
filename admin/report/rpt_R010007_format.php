<?
//	fix head for rpt_R010007
	
	if($search_pt_code == 'O') $search_pt_name = "�����";
	elseif($search_pt_code == 'K') $search_pt_name = "�Ԫҡ��";
	elseif($search_pt_code == 'D') $search_pt_name = "�ӹ�¡��";
	elseif($search_pt_code == 'M') $search_pt_name = "������";
	else $search_pt_name = "";

	if (!$search_per_type) $search_per_type = 1;

	unset($ARR_pt_name);
	unset($ARR_LEVEL_NO);
	unset($ARR_POSITION_TYPE);
	unset($ARR_GENDER);
	$cmd = "select LEVEL_NO, POSITION_TYPE, POSITION_LEVEL from PER_LEVEL 
					where (LEVEL_ACTIVE=1) and (LEVEL_NO in $LIST_LEVEL_NO) ".($search_pt_code ? "and (LEVEL_NO LIKE '%$search_pt_code%')" : "and (LEVEL_NO in $LIST_LEVEL_NO)")." ".($search_per_type ? "and (PER_TYPE = $search_per_type)" : "")." order by LEVEL_SEQ_NO ,LEVEL_NO"; 
	$db_dpis->send_cmd($cmd);
//	echo "cmd:$cmd<br>";
	
	if ($FLAG_RTF) 
		$ARR_COL=array("�","�","�");		//SEX
	else
		$ARR_COL=array("���","˭ԧ","���");		//SEX
	while($data = $db_dpis->get_array()) {
		$POSITION_TYPE = trim($data[POSITION_TYPE]);
		if (!in_array($POSITION_TYPE, $ARR_pt_name)) $ARR_pt_name[] = $POSITION_TYPE;
		$LEVEL_NAME = trim($data[POSITION_LEVEL]);
//		echo "$POSITION_TYPE-$LEVEL_NAME<br>";
		if ($POSITION_TYPE=="�ӹ�¡��" || $POSITION_TYPE=="������") $LEVEL_NAME = $POSITION_TYPE . $LEVEL_NAME;
		$ARR_POSITION_TYPE[$POSITION_TYPE][] = $LEVEL_NAME;

		$ARR_LEVEL_NO[$LEVEL_NAME] = trim($data[LEVEL_NO]);
		$ARR_GENDER[$POSITION_TYPE][] = $ARR_COL;
	}
//	echo "arr_pt_name=".implode(",", $ARR_pt_name)."<br>";

	$heading_text[0] = "|��ǹ�Ҫ���|";
	if ($ISCS_FLAG==1 || $BKK_FLAG==1) 
		$heading_width[0] = "70";
	else
		$heading_width[0] = "40";
	$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_align[0] = "C";
	$data_align[0] = "L";
	$cnt = 0;
	for($k=0;$k<count($ARR_pt_name);$k++) {
		$count_position_type=count($ARR_POSITION_TYPE[$ARR_pt_name[$k]]);
		$cnt1 = 0;
		for($i=0;$i<$count_position_type;$i++) {
			$tmp_position_type=$ARR_POSITION_TYPE[$ARR_pt_name[$k]][$i];
			$count=count($ARR_GENDER[$ARR_pt_name[$k]][$i]);
			for($j=0;$j<$count;$j++) {
				$tmp_gender = $ARR_GENDER[$ARR_pt_name[$k]][$i][$j];
				$cnt++; $cnt1++;
				$heading_text[$cnt] = "<**".($k+1)."**>".$ARR_pt_name[$k]."|<**".($i+1)."**>$tmp_position_type|$tmp_gender";
				if ($ISCS_FLAG==1 || $BKK_FLAG==1) 
					$heading_width[$cnt] = "10";
				else
					$heading_width[$cnt] = "7";
				$column_function[$cnt] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
				$heading_align[$cnt] = "C";
				$data_align[$cnt] = "R";
			}
		}
	}
	$heading_text[$cnt+1] = "<**".($k+2)."**>|<**".($cnt1+2)."^**>�ӹǹ���|���";
	$heading_text[$cnt+2] = "<**".($k+2)."**>|<**".($cnt1+2)."^**>�ӹǹ���|˭ԧ";
	$heading_text[$cnt+3] = "<**".($k+2)."**>|<**".($cnt1+2)."^**>�ӹǹ���|���";
	$heading_width[$cnt+1] = "12";
	$heading_width[$cnt+2] = "12";
	$heading_width[$cnt+3] = "12";
	$column_function[$cnt+1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$cnt+2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$cnt+3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_align[$cnt+1] = "C";
	$heading_align[$cnt+2] = "C";
	$heading_align[$cnt+3] = "C";
	$data_align[$cnt+1] = "R";
	$data_align[$cnt+2] = "R";
	$data_align[$cnt+3] = "R";
	
	$sum_total_w = 0;
	for($i=0; $i<count($heading_width); $i++) $sum_total_w += (int)$heading_width[$i];

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