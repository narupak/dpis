<?
//	fix head for rpt_data_retire_comdtlN_format

	$heading_width[0] = "10";
	$heading_width[1] = "40";
	$heading_width[2] = "50";
	$heading_width[3] = "15";
	$heading_width[4] = "20";
	$heading_width[5] = "13";
	$heading_width[6] = "15";
        
	if(in_array($COM_TYPE, array("1702", "1703", "5112", "5113"))){
		$heading_width[7] = "35";
		$heading_width[8] = "20";
		$heading_width[9] = "35";
	}elseif(in_array($COM_TYPE, array("0305", "5035"))){
		$heading_width[7] = "50";
		$heading_width[8] = "25";
		$heading_width[9] = "45";
	}elseif($COM_TYPE=="�͡�ҡ�Ҫ���任�Ժѵԧҹ������ ���."){	//1707
		$heading_width[7] = "25";
		$heading_width[8] = "55";
		$heading_width[9] = "85";
		$heading_width[10] = "85";
	}else if($COM_TYPE=="1706"){
		$heading_width[7] = "25";
		$heading_width[8] = "55";
		$heading_width[9] = "25";
		$heading_width[10] = "25";
		$heading_width[11] = "85";
	}else{	
		$heading_width[7] = "32";
		$heading_width[8] = "55";
	}

	$data_align = (array) null;
	$heading_align = (array) null;
	$heading_text[0] = "";
	$heading_text[1] = "";
	if($COM_TYPE=="1702" || $COM_TYPE=="5112"){
		$heading_text[2] = "<**1**>���˹觷�跴�ͧ��Ժѵ�˹�ҷ���Ҫ���";
		$heading_text[3] = "<**1**>���˹觷�跴�ͧ��Ժѵ�˹�ҷ���Ҫ���";
		$heading_text[4] = "<**1**>���˹觷�跴�ͧ��Ժѵ�˹�ҷ���Ҫ���";
		$heading_text[5] = "<**1**>���˹觷�跴�ͧ��Ժѵ�˹�ҷ���Ҫ���";
		$heading_text[6] = "<**1**>���˹觷�跴�ͧ��Ժѵ�˹�ҷ���Ҫ���";
		$heading_text[7] = "";
		$heading_text[8] = "";
		$heading_text[9] = "";
	}elseif($COM_TYPE=="0305" || $COM_TYPE=="5035"){
		$heading_text[2] = $COM_HEAD_01."���";
		$heading_text[3] = $COM_HEAD_01."���";
		$heading_text[4] = $COM_HEAD_01."���";
		$heading_text[5] = $COM_HEAD_01."���";
		$heading_text[6] = $COM_HEAD_01."���";
		$heading_text[7] = "";
		$heading_text[8] = "";
		$heading_text[9] = "";
	}elseif($COM_TYPE=="�͡�ҡ�Ҫ���任�Ժѵԧҹ������ ���."){	//1706
		$heading_text[2] = $COM_HEAD_01;
		$heading_text[3] = $COM_HEAD_01;
		$heading_text[4] = $COM_HEAD_01;
		$heading_text[5] = $COM_HEAD_01;
		$heading_text[6] = $COM_HEAD_01;
		$heading_text[7] = "";
		$heading_text[8] = "";
		$heading_text[9] = "";
		$heading_text[10] = "";
	}elseif($COM_TYPE=="1703" || $COM_TYPE=="5113"){
		$heading_text[2] = "";
		$heading_text[3] = $COM_HEAD_01;
		$heading_text[4] = $COM_HEAD_01;
		$heading_text[5] = $COM_HEAD_01;
		$heading_text[6] = $COM_HEAD_01;
		$heading_text[7] = $COM_HEAD_01;
		$heading_text[8] = "";
		$heading_text[9] = "";
	}else if($COM_TYPE=="1706"){
		$heading_text[2] = $COM_HEAD_01;
		$heading_text[3] = $COM_HEAD_01;
		$heading_text[4] = $COM_HEAD_01;
		$heading_text[5] = $COM_HEAD_01;
		$heading_text[6] = "";
		$heading_text[7] = "";
		$heading_text[8] = "";
		$heading_text[9] = "";
		$heading_text[10] = "";
		$heading_text[11] = "";
	}else{	//1701,1705
		$heading_text[2] = $COM_HEAD_01;
		$heading_text[3] = $COM_HEAD_01;
		$heading_text[4] = $COM_HEAD_01;
		$heading_text[5] = $COM_HEAD_01;
		if ($BKK_FLAG==1)
			$heading_text[6] = "";
		else
			$heading_text[6] = $COM_HEAD_01;
		$heading_text[7] = "";
		$heading_text[8] = "";
	}

	$data_align = (array) null;
	$heading_align = (array) null;
	$heading_text[0] = "�ӴѺ";
	$heading_text[1] = $FULLNAME_HEAD;
	if ($CARDNO_FLAG==1) $heading_text[1] .= "|$CARDNO_TITLE";
	$heading_text[2] .= "|���˹�/�ѧ�Ѵ";			$data_align[] = "L";	$heading_align[] = "C";
	$heading_text[3] .= "|���˹�";			$data_align[] = "L";	$heading_align[] = "C";
	$heading_text[4] .= "|�дѺ";		$data_align[] = "C";	$heading_align[] = "C";
	$heading_text[5] .= "|�Ţ���";		$data_align[] = "C";	$heading_align[] = "C";
	$heading_text[6] .= "|�Թ��͹";		$data_align[] = "R";	$heading_align[] = "C";
	if($COM_TYPE=="1702" || $COM_TYPE=="5112"){
		$heading_text[7] = "���ͧ";
		$heading_text[8] = "����͡";
		$heading_text[9] = "�����˵�";
	}elseif($COM_TYPE=="0305" || $COM_TYPE=="5035"){
		$heading_text[7] = "�͹��ѧ�Ѵ";
		$heading_text[8] = "������ѹ���";
		$heading_text[9] = "�����˵�";
	}elseif($COM_TYPE=="�͡�ҡ�Ҫ���任�Ժѵԧҹ������ ���."){	//1706
		$heading_text[7] = "任�Ժѵ�";
		$heading_text[8] = "�ա�˹�";
		$heading_text[9] = "����͡";
		$heading_text[10] = "�����˵�";
	}elseif($COM_TYPE=="1703" || $COM_TYPE=="5113"){
		$heading_text[2] = "�ز�/�Ң�/ʶҹ�֡��";
		$heading_text[3] = "";
		$heading_text[4] = "";
		$heading_text[5] = "";
		$heading_text[6] = "";
		$heading_text[7] = "";
		$heading_text[8] = "����͡";
		$heading_text[9] = "�����˵�";
	}else if($COM_TYPE=="1706"){
		$heading_text[7] = "任�Ժѵ�";
		$heading_text[8] = "�ա�˹�";
		$heading_text[9] = "���Ѻ�Թ��͹";
		$heading_text[10] = "任�Ժѵԧҹ";
		$heading_text[11] = "�����˵�";
	}else{	//1701,1705
		if ($BKK_FLAG==1)
			$heading_name6 = "�͡�ҡ�Ҫ���";
		else
			$heading_name6="�͡";
		if($COM_TYPE=="1701" || $COM_TYPE=="5111"){	$heading_name6="���͡";		}
		$heading_text[7] = "���$heading_name6";
		$heading_text[8] = "�����˵�";
	}

	$data_align = (array) null;
	$heading_align = (array) null;
	$heading_text[0] .= "|���";		$data_align[] = "C";	$heading_align[] = "C";
	$heading_text[1] .= "|";		$data_align[] = "L";	$heading_align[] = "C";
	$heading_text[2] .= "|";			$data_align[] = "L";	$heading_align[] = "C";
	$heading_text[3] .= "|������";			$data_align[] = "L";	$heading_align[] = "C";
	$heading_text[4] .= "|";		$data_align[] = "C";	$heading_align[] = "C";
	$heading_text[5] .= "|";		$data_align[] = "C";	$heading_align[] = "C";
	$heading_text[6] .= "|";		$data_align[] = "R";	$heading_align[] = "C";
	if(in_array($COM_TYPE, array("1702", "1703", "5112", "5113"))){
		$heading_text[7] .= "|������ѹ���";		$data_align[] = "C";	$heading_align[] = "C";
		$heading_text[8] .= "|������ѹ���";		$data_align[] = "C";	$heading_align[] = "C";
		$heading_text[9] .= "|";		$data_align[] = "L";	$heading_align[] = "C";
	}elseif(in_array($COM_TYPE, array("0305", "5035"))){
		$heading_text[7] .= "|";		$data_align[] = "C";	$heading_align[] = "C";
		$heading_text[8] .= "|";		$data_align[] = "C";	$heading_align[] = "C";
		$heading_text[9] .= "|";		$data_align[] = "L";	$heading_align[] = "C";
	}elseif($COM_TYPE=="�͡�ҡ�Ҫ���任�Ժѵԧҹ������ ���."){	//1707
		$heading_text[7] .= "|˹�ҷ��";		$data_align[] = "C";	$heading_align[] = "C";
		$heading_text[8] .= "|����";		$data_align[] = "C";	$heading_align[] = "C";
		$heading_text[9] .= "|������ѹ���";		$data_align[] = "C";	$heading_align[] = "C";
		$heading_text[10] .= "|";		$data_align[] = "L";	$heading_align[] = "C";
	}else if($COM_TYPE=="1706"){
		$heading_text[7] .= "|˹�ҷ��";		$data_align[] = "C";	$heading_align[] = "C";
		$heading_text[8] .= "|����";		$data_align[] = "C";	$heading_align[] = "C";
		$heading_text[9] .= "|�����ҧ��Ժѵԧҹ�ҡ";		$data_align[] = "C";	$heading_align[] = "C";
		$heading_text[10] .= "|������ѹ���";		$data_align[] = "C";	$heading_align[] = "C";
		$heading_text[11] .= "|";		$data_align[] = "L";	$heading_align[] = "C";
	}else{
		$heading_text[7] .= "|������ѹ���";		$data_align[] = "C";	$heading_align[] = "C";
		$heading_text[8] .= "|";		$data_align[] = "L";	$heading_align[] = "C";
	}

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
	$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	if(in_array($COM_TYPE, array("1702", "1703", "5112", "5113","0305", "5035"))){
		$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	}elseif($COM_TYPE=="�͡�ҡ�Ҫ���任�Ժѵԧҹ������ ���."){	//1707
		$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[10] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	}else if($COM_TYPE=="1706"){
		$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[10] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[11] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	}else{
		$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
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