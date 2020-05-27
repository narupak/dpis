<?
//	fix head for rpt_R001005

	if ($search_per_type == 1) {
		$cmd = "select LEVEL_NO, LEVEL_NAME, POSITION_LEVEL as LEVEL_SHORTNAME from PER_LEVEL where (LEVEL_ACTIVE=1) and (LEVEL_NO in $LIST_LEVEL_NO) 
						order by  LEVEL_SEQ_NO,LEVEL_NO";
	} else {
		$cmd = "select LEVEL_NO, LEVEL_NAME, LEVEL_SHORTNAME from PER_LEVEL where (LEVEL_ACTIVE=1) and (PER_TYPE = $search_per_type) 
						order by  LEVEL_SEQ_NO,LEVEL_NO";
	}
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){ 
		$ARR_LEVEL_NO[] = $data[LEVEL_NO];		
		if(trim($data[LEVEL_SHORTNAME])){	$LNAME=str_replace("�дѺ","",$data[LEVEL_SHORTNAME]); }
		else{	$LNAME=str_replace("������ҹ","",$data[LEVEL_NAME]); }
		if ($data[LEVEL_NO]=="O2") $LNAME = "�ӹҭ*Enter*�ҹ";
		elseif ($data[LEVEL_NO]=="O4") $LNAME = "�ѡ��*Enter*�����";
		elseif ($data[LEVEL_NO]=="K2") $LNAME = "�ӹҭ*Enter*���";
		elseif ($data[LEVEL_NO]=="K3") $LNAME = "�ӹҭ*Enter*��þ����";
		elseif ($data[LEVEL_NO]=="K4") $LNAME = "�����*Enter*�ҭ";
		elseif ($data[LEVEL_NO]=="K5") $LNAME = "�ç*Enter*�س�ز�";
		elseif ($data[LEVEL_NO]=="D1") $LNAME = "�ӹ��*Enter*��õ�";
		elseif ($data[LEVEL_NO]=="D2") $LNAME = "�ӹ��*Enter*����٧";
		elseif ($data[LEVEL_NO]=="M1") $LNAME = "������*Enter*��";
		elseif ($data[LEVEL_NO]=="M2") $LNAME = "������*Enter*�٧";
		$ARR_LEVEL_SHORTNAME[] = $LNAME;
	}
	if (count($ARR_LEVEL_SHORTNAME)==0) { echo "***** ����բ����� �дѺ ����Ѻ�������ؤ��ҡù�� *****<br>"; exit; }
//	echo "per_type=$search_per_type   count level : ".count($ARR_LEVEL_NO)."<br>";
	if (!$heading_name) $heading_name="����� heading_name";

	$heading_text[0] = "$heading_name|";
	if($search_per_type == 1 || $search_per_type == 5){
		if ($ISCS_FLAG==1)
			$heading_width[0] = "120";
		else
			$heading_width[0] = "90";
	}elseif($search_per_type == 2){
		$heading_width[0] = "70";
	}elseif($search_per_type == 3 || $search_per_type == 4){
		$heading_width[0] = "70";
	} // end if
	// function ������ aggregate ��  SUM, AVG, PERC ����Ǻ�÷Ѵ (ROW)
	// 									SUM-columnindex1-columnindex2-....  Ex SUM-1-3-4-7 ���¶֧ ������ͧ column ��� 1,3,4 ��� 7
	// 									AVG-columnindex1-columnindex2-....  Ex AVG-1-3-4-7 ���¶֧ �������¢ͧ column ��� 1,3,4 ��� 7
	// 									PERCn-columnindex1-columnindex2-....  Ex PERCn-1-3-4-7 n ��� column ��� ������������º��º������ (��� 1 ��� � column 1 3 4 7) 
	//																												����������� n ���� �����ػ (100%) PERC3-1-3-4-7 ���� ���� column ��� 3 �����������㴢ͧ����� column 1,3,4,7
	//	function ������ �ٻẺ (format) ��觨е�ͧ�����ѧ function ������ aggregate ���� (�����)
	//									TNUM-xn ��� ����¹�繵���Ţ�ͧ��  x ��� ����� comma �������� x ���� comma �͡��ѡ 1,000 �������  n ��� �ӹǹ�ȹ��� ����դ���� �ٹ�� ���ʴ��� -
	//									TNUM0-xn ��� ����¹�繵���Ţ�ͧ��   x ��� ����� comma �������� x ���� comma �͡��ѡ 1,000 �������   n ��� �ӹǹ�ȹ��� ����դ���� �ٹ�� ���ʴ��� 0
	//									ENUM-xn ��� �ʴ����Ţ���ҺԤ   x ��� ����� comma �������� x ���� comma �͡��ѡ 1,000 �������   n ��� �ӹǹ�ȹ��� ����դ���� �ٹ�� ���ʴ��� -
	//									ENUM0-xn ��� �ʴ����Ţ���ҺԤ   x ��� ����� comma �������� x ���� comma �͡��ѡ 1,000 �������   n ��� �ӹǹ�ȹ��� ����դ���� �ٹ�� ���ʴ��� 0
	//
	$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_align[0] = "C";
	$data_align[0] = "L";
	$sum_text = "";
        if($search_per_type==3 || $search_per_type==2){
            $txtLevelName= "������ҹ";
        }else{
           $txtLevelName= "�дѺ���˹�"; 
        }
	for($i=0; $i<count($ARR_LEVEL_SHORTNAME); $i++){ 
		$tmp_level_shortname = $ARR_LEVEL_SHORTNAME[$i];
		$heading_text[$i+1] = "<**1**>$txtLevelName|$tmp_level_shortname";
		if($search_per_type == 1 || $search_per_type == 5){
			if ($ISCS_FLAG==1)
				$heading_width[$i+1] = "15";
			else
				$heading_width[$i+1] = "13";
		}elseif($search_per_type == 2){
			$heading_width[$i+1] = "8";
		}elseif($search_per_type == 3 || $search_per_type == 4){
			$heading_width[$i+1] = "19";
		}
		$column_function[$i+1] = (($NUMBER_DISPLAY==2)?"TNUM-n":"ENUM-n");
		$heading_align[$i+1] = "C";
		$data_align[$i+1] = "R";
		$sum_text .= "-".($i+1);
	} // end for
	$heading_text[count($ARR_LEVEL_SHORTNAME)+1] = "<**2**>���|���˹�";
	$heading_text[count($ARR_LEVEL_SHORTNAME)+2] = "<**2**>���|������";
	$heading_width[count($ARR_LEVEL_SHORTNAME)+1] = "15";
	$heading_width[count($ARR_LEVEL_SHORTNAME)+2] = "15";
	$column_function[count($ARR_LEVEL_SHORTNAME)+1] = (($NUMBER_DISPLAY==2)?"TNUM-n":"ENUM-n");
	$column_function[count($ARR_LEVEL_SHORTNAME)+2] = (($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2");
	$heading_align[count($ARR_LEVEL_SHORTNAME)+1] = "C";
	$heading_align[count($ARR_LEVEL_SHORTNAME)+2] = "C";
	$data_align[count($ARR_LEVEL_SHORTNAME)+1] = "R";
	$data_align[count($ARR_LEVEL_SHORTNAME)+2] = "R";

//	$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");	
	
//	$data_align = array("L","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R");

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