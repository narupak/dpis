<?php
//	fix head for rpt_R004003

	$heading_width[] = "8";
	if ($have_pic) {
		$heading_width[] = "20";
		$heading_width[] = "50";
	}else if($have_cardno){
                $heading_width[] = "30";
		$heading_width[] = "50";
        }else {
		$heading_width[] = "100";
	}
	$heading_width[] = "60";
	$heading_width[] = "50";
	$heading_width[] = "50";
	$heading_width[] = "100";
        
        $heading_width[] = "100"; //ADD NEW RECORD ORG_ASS
        $heading_width[] = "100"; //ADD NEW RECORD MGT
        $heading_width[] = "100"; //ADD NEW RECORD ORG_ASSIGE
        $heading_width[] = "100"; //ADD NEW RECORD 
        
	$heading_width[] = "25";
	$heading_width[] = "35";
	$heading_width[] = "100";
        
        $heading_width[] = "25";//new
        $heading_width[] = "25";//new
        
	$heading_width[] = "25";
	$heading_width[] = "25";
	$heading_width[] = "15";
	$heading_width[] = "20";
	$heading_width[] = "60";
	$heading_width[] = "60";
	$heading_width[] = "30";
        $heading_width[] = "30";
        if($have_manage){
            $heading_width[] = "30";
        }
        if($have_competency){ 
            $heading_width[] = "75";
            $heading_width[] = "75";
            $heading_width[] = "75";
            $heading_width[] = "75";
            $heading_width[] = "75";
            $heading_width[] = "75";
       }
	
    $heading_text[] = "�ӴѺ|";
        if ($have_pic) {
		$heading_text[] = "�ٻ|";
		$heading_text[] = "���� - ʡ��|";
	}else if($have_cardno){
            $heading_text[] = "�Ţ�ѵû�ЪҪ�";
	    $heading_text[] = "���� - ʡ��|";
        }else {
		$heading_text[] = "���� - ʡ��|";
	}
	$heading_text[] = "���˹�";
	$heading_text[] = "�ѹ�������дѺ|�Ѩ�غѹ";
	$heading_text[] = "�дѺ|";
        $heading_text[] = "�ѧ�Ѵ|(�ç���ҧ���������)";
        
        $heading_text[] = "�ѧ�Ѵ|(�ç���ҧ����ͺ���§ҹ)"; //ADD NEW RECORD
        $heading_text[] = "���˹觷ҧ��ú����çҹ����|"; //ADD NEW RECORD
        $heading_text[] = "�ѡ���Ҫ���᷹(˹��§ҹ)|"; //ADD NEW RECORD
        $heading_text[] = "�͹حҵ��Сͺ�ԪҪվ|"; //ADD NEW RECORD
        
	$heading_text[] = "�Ţ���|���˹�";
	$heading_text[] = "�ز�|";
	$heading_text[] = "�Ң�|�Ԫ��͡";
        
        $heading_text[] = "�дѺ�Ѩ�غѹ|(�����§������)";//new
        $heading_text[] = "�ѹ����������дѺ|(�����§������)";//new
        
	$heading_text[] = "�ѹ�������дѺ|";
	$heading_text[] = "�дѺ|";
	$heading_text[] = "�Թ��͹|";
	$heading_text[] = "����ͧ�Ҫ�|";
	$heading_text[] = "�ѹ��è�|(�����Ҫ���)";
	$heading_text[] = "�ѹ��͹���Դ|(���ص��)";
	$heading_text[] = "�ѹ���³|";
        //$heading_text[] = "�����˵�|";
         if($have_manage){
            $heading_text[] = "���˹觷ҧ��ú�����";
        }
        if($have_competency){
            $heading_text[] = "�����š�û����Թ��͹��ѧ 3 ��|";
            $heading_text[] = "�����š�û����Թ��͹��ѧ 3 ��|";
            $heading_text[] = "�����š�û����Թ��͹��ѧ 3 ��|";
            $heading_text[] = "�����š�û����Թ��͹��ѧ 3 ��|";
            $heading_text[] = "�����š�û����Թ��͹��ѧ 3 ��|";
            $heading_text[] = "�����š�û����Թ��͹��ѧ 3 ��|";
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
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	if ($have_pic) {
            $column_function[] = "";
            $column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	}else if($have_cardno){
            $column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
            $column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
        }else if($have_manage){
            $column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
            $column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
        }else {
            $column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	} 
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
        
        $column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM"); //ADD NEW RECORD
        $column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM"); //ADD NEW RECORD
        $column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM"); //ADD NEW RECORD
        $column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM"); //ADD NEW RECORD
        
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
        
        $column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");//new
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");//new
        
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	//$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
        if($have_competency){ 
        $column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
        $column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
        $column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
        $column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
       }
	
	if ($have_pic){
            $heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
	}else if($have_competency){
            $heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
        }else{
            $heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
	}
	if ($have_pic)
	$data_align = array("C","C","L","L","L","L","L","L","L","L","L","L","L","L","C","L","C","C","C","C","C");
	else
	$data_align = array("C","L","L","L","L","L","C","L","L","L","L","L","L","L","L","C","C","C","C","C","C");

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