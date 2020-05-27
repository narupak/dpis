<?
//	fix head for rpt_R010012

	if ($ISCS_FLAG==1) 
		$ARR_LEVEL_GROUP = array("M", "D", "K");
	else
		$ARR_LEVEL_GROUP = array("M", "D", "K", "O");
	$ARR_LEVEL_GROUP_NAME["M"] = "������";
	$ARR_LEVEL_GROUP_NAME["D"] = "�ӹ�¡��";
	$ARR_LEVEL_GROUP_NAME["K"] = "�Ԫҡ��";
	if ($ISCS_FLAG!=1) 
		$ARR_LEVEL_GROUP_NAME["O"] = "�����";

	$ARR_LEVEL["M"] = array("M2", "M1");
	$ARR_LEVEL["D"] = array("D2", "D1");
	if ($ISCS_FLAG==1) 
		$ARR_LEVEL["K"] = array("K5", "K4");
	else
		$ARR_LEVEL["K"] = array("K5", "K4", "K3", "K2", "K1");
	if ($ISCS_FLAG!=1) 
		$ARR_LEVEL["O"] = array("O4", "O3", "O2", "O1");
	if ($ISCS_FLAG!=1) { 
		$ARR_LEVEL_NAME["M2"][0] = "";
		$ARR_LEVEL_NAME["M1"][0] = "";
		$ARR_LEVEL_NAME["D2"][0] = "";
		$ARR_LEVEL_NAME["D1"][0] = "";
		$ARR_LEVEL_NAME["K5"][0] = "";
		$ARR_LEVEL_NAME["K4"][0] = "";
		$ARR_LEVEL_NAME["K3"][0] = "�ӹҭ���";
		$ARR_LEVEL_NAME["K2"][0] = "";
		$ARR_LEVEL_NAME["K1"][0] = "";
		$ARR_LEVEL_NAME["O4"][0] = "�ѡ��";
		$ARR_LEVEL_NAME["O3"][0] = "";
		$ARR_LEVEL_NAME["O2"][0] = "";
		$ARR_LEVEL_NAME["O1"][0] = "";
	}
	$ARR_LEVEL_NAME["M2"][1] = "�٧";
	$ARR_LEVEL_NAME["M1"][1] = "��";
	$ARR_LEVEL_NAME["D2"][1] = "�٧";
	$ARR_LEVEL_NAME["D1"][1] = "��";
	$ARR_LEVEL_NAME["K5"][1] = "�ç�س�ز�";
	$ARR_LEVEL_NAME["K4"][1] = "����Ǫҭ";
	if ($ISCS_FLAG!=1) { 
		$ARR_LEVEL_NAME["K3"][1] = "�����";
		$ARR_LEVEL_NAME["K2"][1] = "�ӹҭ���";
		$ARR_LEVEL_NAME["K1"][1] = "��Ժѵԡ��";
		$ARR_LEVEL_NAME["O4"][1] = "�����";
		$ARR_LEVEL_NAME["O3"][1] = "������";
		$ARR_LEVEL_NAME["O2"][1] = "�ӹҭ�ҹ";
		$ARR_LEVEL_NAME["O1"][1] = "��Ժѵԧҹ";
	}

	$heading_width = (array) null;
	$heading_text = (array) null;
	$heading_align = (array) null;
	$data_align = (array) null;
	$column_function = (array) null;

	if (!$heading_name) $heading_name="����� heading_name";
	if ($ISCS_FLAG==1) 
		$heading_width[]  = "120";
	else
		$heading_width[]  = "80";
	$heading_text[] = "|$heading_name|";
	$heading_align[] = "C";
	$data_align[] = "L";
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	for($i=0; $i<count($ARR_LEVEL_GROUP); $i++){
		$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
		for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
			$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
			if ($ISCS_FLAG==1) 
				$heading_width[]  = "20";
			else
				$heading_width[]  = "15";
			$heading_text[] = "<**".($i+1)."**>".$ARR_LEVEL_GROUP_NAME[$LEVEL_GROUP]."|".$ARR_LEVEL_NAME[$LEVEL_NO][0]."|".$ARR_LEVEL_NAME[$LEVEL_NO][1];
			$heading_align[] = "C";
			$data_align[] = "R";
			$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		} // loop for
	} // loop for
	if ($ISCS_FLAG==1) 
		$heading_width[]  = "20";
	else
		$heading_width[]  = "12";
	$heading_text[] = "|���|";
	$heading_align[] = "C";
	$data_align[] = "R";
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
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