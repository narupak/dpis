<?
//	fix head for P0203
//	echo "COM_TYPE=$COM_TYPE<br>";
 
	if($COM_TYPE=="0101" || $COM_TYPE=="5011"){	//�ͺ�觢ѹ��
	    if (!$FLAG_RTF) {
		$heading_width[0] = "9";
		$heading_width[1] = "40";
		$heading_width[2] = "30";
		$heading_width[3] = "20";
		$heading_width[4] = "9";
		$heading_width[5] = "28";
		$heading_width[6] = "35";
		$heading_width[7] = "20";
		$heading_width[8] = "20";
		$heading_width[9] = "17";
		$heading_width[10] = "17";
		$heading_width[11] = "18";
		$heading_width[12] = "25";
		}	else if ($FLAG_RTF) {
		$heading_width[0] = "4";
		$heading_width[1] = "12";
		$heading_width[2] = "10";
		$heading_width[3] = "6";
		$heading_width[4] = "8";
		$heading_width[5] = "6";
		$heading_width[6] = "14";
		$heading_width[7] = "10";
		$heading_width[8] = "10";
		$heading_width[9] = "4";
		$heading_width[10] = "6";
		$heading_width[11] = "10";
		$heading_width[12] = "10";
		}
		if($BKK_FLAG==1) {
			$heading_text[0] = "|�ӴѺ|���";
			$heading_text[1] = "|".$FULLNAME_HEAD."|";
			$heading_text[2] = "|�ز�/�Ң�|";
			$heading_text[3] = "<**1**>�ͺ�觢ѹ��|���˹�";
			$heading_text[4] = "<**1**>�ͺ�觢ѹ��|�ӴѺ|���";
			$heading_text[5] = "<**1**>�ͺ�觢ѹ��|��С�ȼš���ͺ";
			$heading_text[6] = "<**2**>���˹�����ѧ�Ѵ����è�|���˹�/�ѧ�Ѵ";
			$heading_text[7] = "<**2**>���˹�����ѧ�Ѵ����è�|���˹觻�����";
			$heading_text[8] = "<**2**>���˹�����ѧ�Ѵ����è�|�дѺ";
			$heading_text[9] = "<**2**>���˹�����ѧ�Ѵ����è�|�Ţ���";
			$heading_text[10] = "|�Թ��͹|";
		} else {
			$heading_text[0] = "�ӴѺ|���";
			$heading_text[1] = $FULLNAME_HEAD."|(�ѹ��͹���Դ)";
			if ($CARDNO_FLAG==1) $heading_text[1] .= "|$CARDNO_TITLE";
			$heading_text[2] = "�ز�/�Ң�/|ʶҹ�֡��";
			$heading_text[3] = "<**1**>�ͺ�觢ѹ��|���˹�";
			$heading_text[4] = "<**1**>�ͺ�觢ѹ��|�ӴѺ|���";
			$heading_text[5] = "<**1**>�ͺ�觢ѹ��|��С�ȼš���ͺ�ͧ";
			$heading_text[6] = $COM_HEAD_02."����è��觵��|���˹�/�ѧ�Ѵ";
			$heading_text[7] = $COM_HEAD_02."����è��觵��|���˹觻�����";
			$heading_text[8] = $COM_HEAD_02."����è��觵��|�дѺ";
			$heading_text[9] = $COM_HEAD_02."����è��觵��|�Ţ���";
			$heading_text[10] = $COM_HEAD_02."����è��觵��|�Թ��͹";
		}
		$heading_text[11] = "|������ѹ���|";
		$heading_text[12] = "|�����˵�|";

		$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[10] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[11] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[12] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");

		$heading_align = array('C','C','C','C','C','C','C','C','C','C','C','C','C');

		$data_align = array("C","L","L","L","C","L","L","C","C","C","R","C","L");
	}elseif(in_array($COM_TYPE, array("0102", "0103", "0504", "0604", "0704", "0803","5012","5013"))){	//��èؼ�����Ѻ�Ѵ���͡
		$heading_width[0] = "10";
		$heading_width[1] = "43";
		$heading_width[2] = "40";
		$heading_width[3] = "44";
		$heading_width[4] = "20";
		$heading_width[5] = "17";
		$heading_width[6] = "17";
		$heading_width[7] = "20";
		$heading_width[8] = "24";
		$heading_width[9] = "50";

		$heading_text[0] = "�ӴѺ|���";
		$heading_text[1] = $FULLNAME_HEAD."|(�ѹ��͹���Դ)";
		if ($CARDNO_FLAG==1) $heading_text[1] .= "|$CARDNO_TITLE";
		$heading_text[2] = "�ز�/�Ң�/ʶҹ�֡��|";
		$heading_text[3] = $COM_HEAD_01."����è�|���˹�/�ѧ�Ѵ";
		$heading_text[4] = $COM_HEAD_01."����è�|���˹觻�����";
		$heading_text[5] = $COM_HEAD_01."����è�|�дѺ";
		$heading_text[6] = $COM_HEAD_01."����è�|�Ţ���";
		$heading_text[7] = $COM_HEAD_01."����è�|�Թ��͹";
		$heading_text[8] = "|������ѹ���|";
		$heading_text[9] = "|�����˵�|";

		$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");

		$heading_align = array('C','C','C','C','C','C','C','C','C','C');

		$data_align = array("C","L","L","L","C","C","C","R","C","L");
	}elseif(in_array($COM_TYPE, array("�觵���ѡ���Ҫ���᷹","�觵������ѡ�ҡ��㹵��˹�"))){
		if($COM_TYPE == "�觵���ѡ���Ҫ���᷹"){
			$heading_name4="�ѡ���Ҫ���᷹";
		}elseif($COM_TYPE == "�觵������ѡ�ҡ��㹵��˹�"){
			$heading_name4="�ѡ�ҡ��㹵��˹�";
		}
		$heading_width[0] = "10";
		$heading_width[1] = "45";
		$heading_width[2] = "40";
		$heading_width[3] = "25";
		$heading_width[4] = "25";
		$heading_width[5] = "40";
		$heading_width[6] = "25";
		$heading_width[7] = "13";
		$heading_width[8] = "25";
		$heading_width[9] = "25";
		$heading_width[10] = "20";
		$heading_width[11] = "60";

		$heading_text[0] = "�ӴѺ|���";
		$heading_text[1] = $FULLNAME_HEAD."|";
		$heading_text[2] = $COM_HEAD_01."|���˹�/�ѧ�Ѵ";
		$heading_text[3] = $COM_HEAD_01."|���˹觻�����";
		$heading_text[4] = $COM_HEAD_01."|�дѺ";
		$heading_text[5] = "<**2**>$heading_name4�|���˹�/�ѧ�Ѵ";
		$heading_text[6] = "<**2**>$heading_name4�|���˹觻�����";
		$heading_text[7] = "<**2**>$heading_name4�|�дѺ";
		$heading_text[8] = "<**2**>$heading_name4�|�Ţ���";
		$heading_text[9] = "|������ѹ���|";
		$heading_text[10] = "|�֧�ѹ���|";
		$heading_text[11] = "|�����˵�|";

		$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[10] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[11] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");

		$heading_align = array('C','C','C','C','C','C','C','C','C','C','C','C');

		$data_align = array("C","L","C","C","L","L","L","C","L","R","C","L");
	}elseif($COM_TYPE=="0302" || $COM_TYPE=="5032"){  //�Ѻ�͹����Ҫ��þ����͹���ѭ������Ѻ�ز��������
		$heading_width[0] = "5";
		$heading_width[1] = "35";
		$heading_width[2] = "35";
		$heading_width[3] = "40";
		$heading_width[4] = "15";
		$heading_width[5] = "10";
		$heading_width[6] = "10";
		$heading_width[7] = "30";
		$heading_width[8] = "10";
		$heading_width[9] = "15";
		$heading_width[10] = "30";
		$heading_width[11] = "15";
		$heading_width[12] = "10";
		$heading_width[13] = "10";
		$heading_width[14] = "10";
		$heading_width[15] = "15";
		$heading_width[16] = "60";

		$heading_text[0] = "�ӴѺ|���";
		$heading_text[1] = $FULLNAME_HEAD;
		if ($CARDNO_FLAG==1) $heading_text[1] .= "/|$CARDNO_TITLE";
		$heading_text[2] = "�زԷ�����Ѻ�������/|ʶҹ�֡��/�ѹ�������稡���֡��";
		$heading_text[3] = $COM_HEAD_01."���|���˹�/�ѧ�Ѵ";
		$heading_text[4] = $COM_HEAD_01."���|���˹觻�����";
		$heading_text[5] = $COM_HEAD_01."���|�дѺ";
		$heading_text[6] = $COM_HEAD_01."���|�Թ��͹";
		$heading_text[7] = "<**2**>�ͺ�觢ѹ��|���˹�";
		$heading_text[8] = "<**2**>�ͺ�觢ѹ��|�ӴѺ���";
		$heading_text[9] = "<**2**>�ͺ�觢ѹ��|��С�ȼš���ͺ�ͧ";
		$heading_text[10] = $COM_HEAD_03."����Ѻ�͹|���˹�/�ѧ�Ѵ";
		$heading_text[11] = $COM_HEAD_03."����Ѻ�͹|���˹觻�����";
		$heading_text[12] = $COM_HEAD_03."����Ѻ�͹|�дѺ";
		$heading_text[13] = $COM_HEAD_03."����Ѻ�͹|�Ţ���";
		$heading_text[14] = $COM_HEAD_03."����Ѻ�͹|�Թ��͹";
		$heading_text[15] = "|������ѹ���|";
		$heading_text[16] = "|�����˵�|";

		$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[10] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[11] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[12] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[13] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[14] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[15] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[16] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");

		$heading_align = array('C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C');

		$data_align = array("C", "L", "L", "L", "C", "C", "R", "L", "R", "L", "L", "C", "C", "C", "R", "C", "L");
	}else{ //########
		$heading_width = (array) null;
		$heading_width[0] = 9;
		$heading_width[1] = 35;
		if(in_array($COM_TYPE, array("0105", "0106", "5016", "5017"))){
			$heading_width[2] = 25;
			$heading_width[3] = 25;
			$heading_width[4] = 15;
			$heading_width[5] = 15;
			$heading_width[6] = 15;
			$heading_width[7] = 18;
			$heading_width[8] = 18;
			$heading_width[9] = 25;
			$heading_width[10] = 15;
			$heading_width[11] = 15;
			$heading_width[12] = 10;
			$heading_width[13] = 15;
			$heading_width[14] = 18;
			$heading_width[15] = 18;
		}elseif(in_array($COM_TYPE, array("0107","5018"))){
			$heading_width[2] = 30;
			$heading_width[3] = 30;
			$heading_width[4] = 15;
			$heading_width[5] = 15;
			$heading_width[6] = 15;
			$heading_width[7] = 20;
			$heading_width[8] = 30;
			$heading_width[9] = 15;
			$heading_width[10] = 15;
			$heading_width[11] = 10;
			$heading_width[12] = 15;
			$heading_width[13] = 20;
			$heading_width[14] = 20;
		}elseif(in_array($COM_TYPE, array("0108","5019"))){
			$heading_width[2] = 30;
			$heading_width[3] = 30;
			$heading_width[4] = 15;
			$heading_width[5] = 15;
			$heading_width[6] = 10;
			$heading_width[7] = 15;
			$heading_width[8] = 30;
			$heading_width[9] = 15;
			$heading_width[10] = 15;
			$heading_width[11] = 10;
			$heading_width[12] = 15;
			$heading_width[13] = 20;
			$heading_width[14] =30;
		}elseif(in_array($COM_TYPE, array("0109","5015"))){	//�Ѻ�͹ ���. ��ǹ��ͧ���
			$heading_width[2] = 30;
			$heading_width[3] = 30;
			$heading_width[4] = 15;
			$heading_width[5] = 15;
			$heading_width[6] = 30;
			$heading_width[7] = 25;
			$heading_width[8] = 14;
			$heading_width[9] = 33;
			$heading_width[10] = 30;
			$heading_width[11] = 40;
			$heading_width[12] = 14;
			$heading_width[13] = 15;
			$heading_width[14] = 20;
			$heading_width[15] = 15;
			$heading_width[16] = 20;
			$heading_width[17] = 100;
		}elseif(in_array($COM_TYPE, array("0303","5033"))){	//�Ѻ�͹����Ҫ��þ����͹���ѭ�Ҵ�ç���˹���дѺ����٧���
			$heading_width[2] = 30;
			$heading_width[3] = 30;
			$heading_width[4] = 15;
			$heading_width[5] = 15;
			$heading_width[6] = 15;
			$heading_width[7] = 15;
			$heading_width[8] = 20;
			$heading_width[9] = 30;
			$heading_width[10] = 15;
			$heading_width[11] = 15;
			$heading_width[12] = 10;
			$heading_width[13] = 15;
			$heading_width[14] = 20;
			$heading_width[15] = 30;
		}else{	//--0104,0301			//�͹ �Ҵ 0304
			$heading_width[2] = 30;
			$heading_width[3] = 30;
			$heading_width[4] = 15;
			$heading_width[5] = 15;
			$heading_width[6] = 15;
			$heading_width[7] = 30;
			$heading_width[8] = 15;
			$heading_width[9] = 15;
			$heading_width[10] = 10;
			$heading_width[11] = 15;
			$heading_width[12] = 20;
			$heading_width[13] = 30;
		}
		$head_line1 = (array) null;
		$head_line1[] = "�ӴѺ";
		$head_line1[] = $FULLNAME_HEAD;
		if ($BKK_FLAG==1)
			$head_line1[] = "�ز�/�Ң�";
		else
			$head_line1[] = "�ز�/�Ң�/";
		if(in_array($COM_TYPE, array("0108","5019"))){
			$head_line1[] = $COM_HEAD_01."���";
			$head_line1[] = $COM_HEAD_01."���";
			$head_line1[] = $COM_HEAD_01."���";
			$head_line1[] = $COM_HEAD_01."���";
		}else{
			$head_line1[] = $COM_HEAD_02."���";
			$head_line1[] = $COM_HEAD_02."���";
			$head_line1[] = $COM_HEAD_02."���";
			if($MFA_FLAG==1 && in_array($COM_TYPE, array("0301","5031","0303","5033"))) {
				$head_line1[] = $COM_HEAD_02."���";
			}
			$head_line1[] = $COM_HEAD_02."���";
		}
		if(in_array($COM_TYPE, array("0105", "0106", "5016", "5017"))){
			if($COM_TYPE=="0105" || $COM_TYPE=="5016"){
				$heading_name7="�͡�ҡ";
				$heading_name8="�鹨ҡ�Ҫ���";
			}elseif($COM_TYPE=="0106" || $COM_TYPE=="5017"){
				$heading_name7="�͡任�Ժѵԧҹ";
				$heading_name8="�鹨ҡ��û�Ժѵԧҹ";
			}
			$head_line1[] = "$heading_name7";
			$head_line1[] = "$heading_name8";
			$head_line1[] = $COM_HEAD_03."����è�";
			$head_line1[] = $COM_HEAD_03."����è�";
			$head_line1[] = $COM_HEAD_03."����è�";
			$head_line1[] = $COM_HEAD_03."����è�";
			$head_line1[] = $COM_HEAD_03."����è�";
			$head_line1[] = "";
			$head_line1[] = "";
		}elseif(in_array($COM_TYPE, array("0107","5018"))){
			$head_line1[] = "";
			$head_line1[] = $COM_HEAD_03."����è�";
			$head_line1[] = $COM_HEAD_03."����è�";
			$head_line1[] = $COM_HEAD_03."����è�";
			$head_line1[] = $COM_HEAD_03."����è�";
			$head_line1[] = $COM_HEAD_03."����è�";
			$head_line1[] = "";
			$head_line1[] = "";
		}elseif(in_array($COM_TYPE, array("0108","5019"))){			
			$head_line1[] = "";
			$head_line1[] = $COM_HEAD_03."����è�";
			$head_line1[] = $COM_HEAD_03."����è�";
			$head_line1[] = $COM_HEAD_03."����è�";
			$head_line1[] = $COM_HEAD_03."����è�";
			$head_line1[] = $COM_HEAD_03."����è�";
			$head_line1[] = "";
			$head_line1[] = "";
		}elseif(in_array($COM_TYPE, array("0109","5015"))){
			$head_line1[] = "<**3**>�ͺ�觢ѹ��";
			$head_line1[] = "<**3**>�ͺ�觢ѹ��";
			$head_line1[] = "<**3**>�ͺ�觢ѹ��";
			$head_line1[] = "<**3**>�ͺ�觢ѹ��";
			$head_line1[] = "<**4**>���˹������ǹ�Ҫ��÷���Ѻ�͹";
			$head_line1[] = "<**4**>���˹������ǹ�Ҫ��÷���Ѻ�͹";
			$head_line1[] = "<**4**>���˹������ǹ�Ҫ��÷���Ѻ�͹";
			$head_line1[] = "<**4**>���˹������ǹ�Ҫ��÷���Ѻ�͹";
			$head_line1[] = "<**4**>���˹������ǹ�Ҫ��÷���Ѻ�͹";
			$head_line1[] = "";
			$head_line1[] = "";
		}elseif(in_array($COM_TYPE, array("0303","5033"))){
			$head_line1[] = "��ç���˹�";
			$head_line1[] = $COM_HEAD_03."����Ѻ�͹������͹";
			$head_line1[] = $COM_HEAD_03."����Ѻ�͹������͹";
			$head_line1[] = $COM_HEAD_03."����Ѻ�͹������͹";
			$head_line1[] = $COM_HEAD_03."����Ѻ�͹������͹";
			$head_line1[] = $COM_HEAD_03."����Ѻ�͹������͹";
			$head_line1[] = "";
			$head_line1[] = "";
		}else{
			$head_line1[] = $COM_HEAD_03."����Ѻ�͹";
			$head_line1[] = $COM_HEAD_03."����Ѻ�͹";
			$head_line1[] = $COM_HEAD_03."����Ѻ�͹";
			$head_line1[] = $COM_HEAD_03."����Ѻ�͹";
			$head_line1[] = $COM_HEAD_03."����Ѻ�͹";
			$head_line1[] = "";
			$head_line1[] = "";
		}

		//�Ƿ�� 2 ------------------------------
		$head_line2 = (array) null;
		$column_function = (array) null;
		$heading_align = (array) null;
		$data_align = (array) null;
		$head_line2[] = "���";		$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
		if ($BKK_FLAG==1)
			$head_line2[] = "";	
		else
			$head_line2[] = "(�ѹ ��͹ ���Դ)";	
		$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");		$heading_align[] = "C";	$data_align[] = "C";
		if(in_array($COM_TYPE, array("�觵���ѡ���Ҫ���᷹","�觵������ѡ�ҡ��㹵��˹�"))){
			$head_line2[] = "���˹�/�ѧ�Ѵ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			$head_line2[] = "���˹觻�����";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			$head_line2[] = "�дѺ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			$head_line2[] = "���˹�/�ѧ�Ѵ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			$head_line2[] = "���˹觻�����";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			$head_line2[] = "�дѺ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			$head_line2[] = "�Ţ���";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");		$heading_align[] = "C";	$data_align[] = "C";
			$head_line2[] = "";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			$head_line2[] = "";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			$head_line2[] = "";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
		}else{	//
			if ($BKK_FLAG==1)
				$head_line2[] = "";	
			else
				$head_line2[] = "ʶҹ�֡��";	
			$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			$head_line2[] = "���˹�/�ѧ�Ѵ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			$head_line2[] = "���˹觻�����";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			$head_line2[] = "�дѺ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			if($MFA_FLAG==1 && in_array($COM_TYPE, array("0301","5031","0303","5033"))) {
				$head_line2[] = "�Ţ���";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "R";
			}
			$head_line2[] = "�Թ��͹";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "R";
			
			if(in_array($COM_TYPE, array("0105","0106", "5016", "5017"))){
				if($COM_TYPE=="0105" || $COM_TYPE=="5016"){
					$heading_name7="�Ҫ��������";
					$heading_name8="���������";
				}elseif($COM_TYPE=="0106" || $COM_TYPE=="5017"){
					$heading_name7="������ ���.�����";
					$heading_name8="������ ���. �����";
				}
				$head_line2[] = "$heading_name7";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "$heading_name8";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "���˹�/�ѧ�Ѵ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "���˹觻�����";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�дѺ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�Ţ���";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�Թ��͹";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "R";
				$head_line2[] = "������ѹ���";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�����˵�";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			}elseif(in_array($COM_TYPE, array("0107","5018"))){
				$head_line2[] = "���͡�����";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "���˹�/�ѧ�Ѵ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "���˹觻�����";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�дѺ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�Ţ���";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�Թ��͹";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "R";
				$head_line2[] = "������ѹ���";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�����˵�";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			}elseif(in_array($COM_TYPE, array("0108","5019"))){
				$head_line2[] = "���͡�����";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "���˹�/�ѧ�Ѵ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "���˹觻�����";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�дѺ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�Ţ���";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�Թ��͹";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "R";
				$head_line2[] = "������ѹ���";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�����˵�";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			}elseif(in_array($COM_TYPE, array("0109","5015"))){
				$head_line2[] = "���˹�";		$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "���˹觻�����";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�ӴѺ���";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "��С�ȼš���ͺ�ͧ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "���˹�/�ѧ�Ѵ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "���˹觻�����";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�дѺ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�Ţ���";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�Թ��͹";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "R";
				$head_line2[] = "������ѹ���";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�����˵�";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			}elseif(in_array($COM_TYPE, array("0303","5033"))){
				$head_line2[] = "��дѺ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "���˹�/�ѧ�Ѵ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "���˹觻�����";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�дѺ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�Ţ���";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�Թ��͹";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "R";
				$head_line2[] = "������ѹ���";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�����˵�";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			}else{
				$head_line2[] = "���˹�/�ѧ�Ѵ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "���˹觻�����";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�дѺ";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�Ţ���";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�Թ��͹";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "R";
				$head_line2[] = "������ѹ���";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
				$head_line2[] = "�����˵�";	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");		$heading_align[] = "C";	$data_align[] = "C";
			}
		}
		$heading_text = (array) null;
		for($k=0; $k < count($head_line1); $k++) {
			$heading_text[] = $head_line1[$k]."|".$head_line2[$k];
		}
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
//	echo "COM_TYPE=$COM_TYPE, heading_text=".implode(",",$heading_text)."<br>";
	
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