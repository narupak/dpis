<? 
//	fix head for rpt_personal_inquiry_xls
	if ($BKK_FLAG!=1) $Col = 16; else $Col = 8; /*9*/
 
	$heading_width[0] = "20";/*�Ţ�����˹�*/
	$heading_width[1] = "27";/*�ӹ�˹�Ҫ���(��)*/
    $heading_width[2] = "35";/*����(��)*/
    $heading_width[3] = "35";/*���ʡ��(��)*/
    $heading_width[4] = "33";/*�ӹ�˹�Ҫ���(�ѧ���)*/
    $heading_width[5] = "45";/*����(�ѧ���)*/
    $heading_width[6] = "45";/*���ʡ��(�ѧ���)*/
	$heading_width[7] = "35";/*�Ţ��Шӵ�ǻ�ЪҪ�*/
    $heading_width[8] = "15";/*Release 5.1.0.4 Begin �á ��*/
	$heading_width[9] = "25";/*�ѹ/��͹/���Դ*/  
    $heading_width[10] = "60";/*���آ���Ҫ���*/ /*http://dpis.ocsc.go.th/Service/node/1716*/    
	$heading_width[11] = "40";/*���˹����§ҹ*/
	$heading_width[12] = "40";/*���˹�㹡�ú����çҹ*/
	$heading_width[13] = "40";/*���������˹�*/
	$heading_width[14] = "40";/*�дѺ���˹�*/   
	if ($BKK_FLAG!=1) $heading_width[15] = "50";/*��з�ǧ*/     
	$heading_width[$Col] = "50";/*���*/
	$heading_width[$Col+1] = "50";/*�ӹѡ/�ͧ*/
	$heading_width[$Col+2] = "50";/*��ӡ����ӹѡ/�ͧ 1 �дѺ*/
	$heading_width[$Col+3] = "50";/*��ӡ����ӹѡ/�ͧ 2 �дѺ*/
	$heading_width[$Col+4] = "20";/*�Թ��͹*/
	$heading_width[$Col+5] = "30";/*�Թ��Шӵ��˹�*/
	$heading_width[$Col+6] = "30";/*�ѹ���³����*/
	$heading_width[$Col+7] = "30";/*�ѹ���������Ѻ�Ҫ���*/
    $heading_width[$Col+8] = "60";/*�����Ҫ���(���اҹ)*/   
	$heading_width[$Col+9] = "30";/*�ѹ��������ǹ�Ҫ���*/
	$heading_width[$Col+10] = "50";/*�زԷ�����è� �дѺ����֡��*/
	$heading_width[$Col+11] = "50";/*�زԷ�����è� �زԡ���֡��*/
	$heading_width[$Col+12] = "50";/*�زԷ�����è� �Ң��Ԫ��͡*/
	$heading_width[$Col+13] = "50";/*�زԷ�����è� ʶҺѹ����֡��*/
    $heading_width[$Col+14] = "40";/*Release 5.1.0.4 Begin �زԷ�����è� ����ȷ������稡���֡��*/
    $heading_width[$Col+15] = "30";/*Release 5.1.0.4 Begin �زԷ�����è� �շ������稡���֡��*/
    $heading_width[$Col+16] = "50";/*�زԷ�����è� �������ع*/
    $heading_width[$Col+17] = "50";/*�ز�㹵��˹觻Ѩ�غѹ �дѺ����֡��*/ 
	$heading_width[$Col+18] = "50";/*�ز�㹵��˹觻Ѩ�غѹ �زԡ���֡��*/
    $heading_width[$Col+19] = "50";/*�ز�㹵��˹觻Ѩ�غѹ �Ң��Ԫ��͡*/
	$heading_width[$Col+20] = "50";/*�ز�㹵��˹觻Ѩ�غѹ ʶҺѹ����֡��*/
    $heading_width[$Col+21] = "40";/*Release 5.1.0.4 Begin �ز�㹵��˹觻Ѩ�غѹ ����ȷ������稡���֡��*/
    $heading_width[$Col+22] = "30";/*Release 5.1.0.4 Begin �ز�㹵��˹觻Ѩ�غѹ �շ������稡���֡��*/
	$heading_width[$Col+23] = "50";/*�ز�㹵��˹觻Ѩ�غѹ �������ع*/
    $heading_width[$Col+24] = "50";/*�ز��٧�ش �дѺ����֡��*/
	$heading_width[$Col+25] = "50";/*�ز��٧�ش �زԡ���֡��*/
	$heading_width[$Col+26] = "50";/*�ز��٧�ش �Ң��Ԫ��͡*/
	$heading_width[$Col+27] = "30";/*�ز��٧�ش ʶҺѹ����֡��*/
    $heading_width[$Col+28] = "40";/*Release 5.1.0.4 Begin �ز��٧�ش ����ȷ������稡���֡��*/
    $heading_width[$Col+29] = "30";/*Release 5.1.0.4 Begin �ز��٧�ش �շ������稡���֡��*/
	$heading_width[$Col+30] = "50";/*�ز��٧�ش �������ع*/
    $heading_width[$Col+31] = "50";/*��ҹ��������Ǫҭ�����*/
    $heading_width[$Col+32] = "50";/*��ҹ�ͧ*/
    $heading_width[$Col+33] = "50";/*��ҹ����*/
    $heading_width[$Col+34] = "50";/*�鹷ҧ*/
    $heading_width[$Col+35] = "35";/*Release 5.1.0.4 Begin �շ���Ѻ����ͧ�Ҫ� */
    $heading_width[$Col+36] = "25";/*����ͧ�Ҫ �*/ 
if($search_per_type == 1){
    $heading_width[$Col+37] = "50";/*��§ҹ�дѺ*/
	$heading_width[$Col+38] = "50";/*�дѺ 1*/
	$heading_width[$Col+39] = "50";/*�ӹѡ�ͧ*/
    $heading_width[$Col+40] = "50";/*��§ҹ�дѺ*/
	$heading_width[$Col+41] = "50";/*�дѺ 2*/
	$heading_width[$Col+42] = "50";/*�ӹѡ�ͧ*/
    $heading_width[$Col+43] = "50";/*��§ҹ�дѺ*/
	$heading_width[$Col+44] = "50";/*�дѺ 3*/
	$heading_width[$Col+45] = "50";/*�ӹѡ�ͧ*/
    $heading_width[$Col+46] = "50";/*��§ҹ�дѺ*/
	$heading_width[$Col+47] = "50";/*�дѺ 4*/
	$heading_width[$Col+48] = "50";/*�ӹѡ�ͧ*/
    $heading_width[$Col+49] = "50";/*��§ҹ�дѺ*/
	$heading_width[$Col+50] = "50";/*�дѺ 5*/
	$heading_width[$Col+51] = "50";/*�ӹѡ�ͧ*/
    $heading_width[$Col+52] = "50";/*��§ҹ�дѺ*/
	$heading_width[$Col+53] = "50";/*�дѺ 6*/
	$heading_width[$Col+54] = "50";/*�ӹѡ�ͧ*/
    $heading_width[$Col+55] = "50";/*��§ҹ�дѺ*/
	$heading_width[$Col+56] = "50";/*�дѺ 7*/
	$heading_width[$Col+57] = "50";/*�ӹѡ�ͧ*/
    $heading_width[$Col+58] = "50";/*��§ҹ�дѺ*/
	$heading_width[$Col+59] = "50";/*�дѺ 8*/
	$heading_width[$Col+60] = "50";/*�ӹѡ�ͧ*/
    $heading_width[$Col+61] = "50";/*��§ҹ�дѺ*/
	$heading_width[$Col+62] = "50";/*�дѺ 9*/
	$heading_width[$Col+63] = "50";/*�ӹѡ�ͧ*/
    $heading_width[$Col+64] = "50";/*��§ҹ�дѺ*/
	$heading_width[$Col+65] = "50";/*�дѺ 10*/
	$heading_width[$Col+66] = "50";/*�ӹѡ�ͧ*/
    $heading_width[$Col+67] = "50";/*��§ҹ�дѺ*/
	$heading_width[$Col+68] = "50";/*�дѺ 11*/
	$heading_width[$Col+69] = "50";/*�ӹѡ�ͧ*/
    $heading_width[$Col+70] = "50";/*��§ҹ�дѺ*/
	$heading_width[$Col+71] = "50";/*��Ժѵԧҹ*/ 
	$heading_width[$Col+72] = "50";/*�ӹѡ�ͧ*/
    $heading_width[$Col+73] = "50";/*��§ҹ�дѺ*/
	$heading_width[$Col+74] = "50";/*�ӹҭ�ҹ*/ 
	$heading_width[$Col+75] = "50";/*�ӹѡ�ͧ*/
    $heading_width[$Col+76] = "50";/*��§ҹ�дѺ*/
	$heading_width[$Col+77] = "50";/*������*/
	$heading_width[$Col+78] = "50";/*�ӹѡ�ͧ*/
    $heading_width[$Col+79] = "50";/*��§ҹ�дѺ*/
	$heading_width[$Col+82] = "50";/*�ѡ�о����*/
	$heading_width[$Col+81] = "50";/*�ӹѡ�ͧ*/
    $heading_width[$Col+82] = "50";/*��§ҹ�дѺ*/
	$heading_width[$Col+83] = "50";/*��Ժѵԡ��*/  
	$heading_width[$Col+84] = "50";/*�ӹѡ�ͧ*/	
    $heading_width[$Col+85] = "50";/*��§ҹ�дѺ*/
	$heading_width[$Col+86] = "50";/*�ӹҭ���*/ 
	$heading_width[$Col+87] = "50";/*�ӹѡ�ͧ*/	
    $heading_width[$Col+88] = "50";/*��§ҹ�дѺ*/
	$heading_width[$Col+89] = "50";/*�ӹҭ��þ����*/  
	$heading_width[$Col+90] = "50";/*�ӹѡ�ͧ*/	
    $heading_width[$Col+91] = "50";/*��§ҹ�дѺ*/
	$heading_width[$Col+92] = "50";/*����Ǫҭ*/
	$heading_width[$Col+93] = "50";/*�ӹѡ�ͧ*/	
    $heading_width[$Col+94] = "50";/*��§ҹ�дѺ*/
	$heading_width[$Col+95] = "50";/*�ç�س�ز�*/
	$heading_width[$Col+96] = "50";/*�ӹѡ�ͧ*/	
    $heading_width[$Col+97] = "50";/*��§ҹ�дѺ*/
	$heading_width[$Col+98] = "50";/*�ӹ�¡�õ�*/
	$heading_width[$Col+99] = "50";/*�ӹѡ�ͧ*/	
    $heading_width[$Col+100] = "50";/*��§ҹ�дѺ*/
	$heading_width[$Col+101] = "50";/*�ӹ�¡���٧*/
	$heading_width[$Col+102] = "50";/*�ӹѡ�ͧ*/	
    $heading_width[$Col+103] = "50";/*��§ҹ�дѺ*/
	$heading_width[$Col+104] = "50";/*�����õ�*/ 
	$heading_width[$Col+105] = "50";/*�ӹѡ�ͧ*/	
    $heading_width[$Col+106] = "50";/*��§ҹ�дѺ*/
	$heading_width[$Col+107] = "50";/*�������٧*/
	$heading_width[$Col+108] = "50";/*�ӹѡ�ͧ*/	
	$heading_width[$Col+109] = "50";/*�ѹ����������дѺ�Ѩ�غѹ*/
	$heading_width[$Col+110] = "50";/*�ҹ㹡�äӹǳ*/
	$heading_width[$Col+111] = "50";/*�ӹѡ/�ͧ�ͺ���§ҹ*/
	$heading_width[$Col+112] = "30";/*�Ţ������*/
    $heading_width[$Col+113] = "30";/*�������Ѿ����Ͷ��*/
    $heading_width[$Col+114] = "50";/*������*/
    $heading_width[$Col+115] = "50";/*�Ţ����Шӵ�Ǣ���Ҫ���*/   
    $heading_width[$Col+116] = "80";/*��������Ǫҭ��ҹ��ѡ*/
    $heading_width[$Col+117] = "80";/*��������Ǫҭ��ҹ�ͧ*/
    $heading_width[$Col+118] = "80";/*��������´/��͸Ժ��*/
    $heading_width[$Col+119] = "80";/*�дѺ��������ö*/
    $heading_width[$Col+120] = "80";/*��������������Ǫҭ*/       
    $heading_width[$Col+121] = "80";/*��������Ǫҭ��ҹ��ѡ2*/
    $heading_width[$Col+122] = "80";/*��������Ǫҭ��ҹ�ͧ2*/
    $heading_width[$Col+123] = "80";/*��������´/��͸Ժ��2*/
    $heading_width[$Col+124] = "80";/*�дѺ��������ö2*/
    $heading_width[$Col+125] = "80";/*��������������Ǫҭ2*/
}else{
    $heading_width[$Col+37] = "50";/*�ѹ����������дѺ�Ѩ�غѹ*/
	$heading_width[$Col+38] = "50";/*�ҹ㹡�äӹǳ*/
	$heading_width[$Col+39] = "50";/*�ӹѡ/�ͧ�ͺ���§ҹ*/
	$heading_width[$Col+40] = "30";/*�Ţ������*/
    $heading_width[$Col+41] = "30";/*�������Ѿ����Ͷ��*/
    $heading_width[$Col+42] = "50";/*������*/
    $heading_width[$Col+43] = "50";/*�Ţ����Шӵ�Ǣ���Ҫ���*/   
    $heading_width[$Col+44] = "80";/*��������Ǫҭ��ҹ��ѡ*/
    $heading_width[$Col+45] = "80";/*��������Ǫҭ��ҹ�ͧ*/
    $heading_width[$Col+46] = "80";/*��������´/��͸Ժ��*/
    $heading_width[$Col+47] = "80";/*�дѺ��������ö*/
    $heading_width[$Col+48] = "80";/*��������������Ǫҭ*/       
    $heading_width[$Col+49] = "80";/*��������Ǫҭ��ҹ��ѡ2*/
    $heading_width[$Col+50] = "80";/*��������Ǫҭ��ҹ�ͧ2*/
    $heading_width[$Col+51] = "80";/*��������´/��͸Ժ��2*/
    $heading_width[$Col+52] = "80";/*�дѺ��������ö2*/
    $heading_width[$Col+53] = "80";/*��������������Ǫҭ2*/
}
      
	if ($MFA_FLAG == 1) { 
		$mfa_col = $Col+64;
		$heading_text[$mfa_col+1] = "35";/**/
		$heading_text[$mfa_col+2] = "25";/**/
		$heading_text[$mfa_col+3] = "10";/**/
		$heading_text[$mfa_col+4] = "25";/**/
		$heading_text[$mfa_col+5] = "25";/**/
		$heading_text[$mfa_col+6] = "10";/**/
		$heading_text[$mfa_col+7] = "30";/**/
		$heading_text[$mfa_col+8] = "30";/**/
		$heading_text[$mfa_col+9] = "30";/**/
		$heading_text[$mfa_col+10] = "40";/**/
	}

	$heading_text[0] = "|$POS_NO_TITLE";
	$heading_text[1] = "|�ӹ�˹�Ҫ���(��)";
    $heading_text[2] = "|����(��)";
    $heading_text[3] = "|���ʡ��(��)";
    $heading_text[4] = "|�ӹ�˹�Ҫ���(�ѧ���)";
    $heading_text[5] = "|����(�ѧ���)";
    $heading_text[6] = "|���ʡ��(�ѧ���)";     
	$heading_text[7] = "|�Ţ��Шӵ�ǻ�ЪҪ�";
    $heading_text[8] = "|��";/*Release 5.1.0.4 Begin*/
	$heading_text[9] = "|�ѹ/��͹/���Դ";   
    $heading_text[10] = "|���آ���Ҫ���";/*Release 5.2.1.18*/   
	$heading_text[11] = "|$PL_TITLE";
	$heading_text[12] = "|$PM_TITLE";
	$heading_text[13] = "| $PT_TITLE";
	$heading_text[14] = "|$LEVEL_TITLE";
	if ($BKK_FLAG!=1) $heading_text[15] = "|$MINISTRY_TITLE";     
	$heading_text[$Col] = "|$DEPARTMENT_TITLE";
	$heading_text[$Col+1] = "|$ORG_TITLE";
	$heading_text[$Col+2] = "|$ORG_TITLE1";
	$heading_text[$Col+3] = "|$ORG_TITLE2";
	$heading_text[$Col+4] = "|�Թ��͹";
	$heading_text[$Col+5] = "|�Թ��Шӵ��˹�";
	$heading_text[$Col+6] = "|�ѹ���³����";
	$heading_text[$Col+7] = "|�ѹ���������Ѻ�Ҫ���";   
    $heading_text[$Col+8] = "|�����Ҫ���(���اҹ)";     
	$heading_text[$Col+9] = "|�ѹ��������ǹ�Ҫ���";
	$heading_text[$Col+10] = "|$EL_TITLE";
	$heading_text[$Col+11] = "|$EN_TITLE";
	$heading_text[$Col+12] = "�زԷ�����è�|$EM_TITLE";
	$heading_text[$Col+13] = "|$INS_TITLE";
    $heading_text[$Col+14] = "|����ȷ������稡���֡��";/*Release 5.1.0.4 Begin*/
    $heading_text[$Col+15] = "|�շ������稡���֡��";/*Release 5.1.0.4 End*/
    $heading_text[$Col+16] = "|�������ع �ͧ�زԷ�����è�";/*Release 5.1.0.4*/    
	$heading_text[$Col+17] = "|$EL_TITLE";
	$heading_text[$Col+18] = "|$EN_TITLE";        
	$heading_text[$Col+19] = "�ز�㹵��˹觻Ѩ�غѹ|$EM_TITLE";
	$heading_text[$Col+20] = "|$INS_TITLE";
    $heading_text[$Col+21] = "|����ȷ������稡���֡��";/*Release 5.1.0.4 Begin*/
    $heading_text[$Col+22] = "|�շ������稡���֡��";/*Release 5.1.0.4 End*/
    $heading_text[$Col+23] = "|�������ع �ͧ�ز�㹵��˹觻Ѩ�غѹ";/*Release 5.1.0.4*/    
	$heading_text[$Col+24] = "|$EL_TITLE";
	$heading_text[$Col+25] = "|$EN_TITLE";
	$heading_text[$Col+26] = "�ز��٧�ش|$EM_TITLE";
	$heading_text[$Col+27] = "|$INS_TITLE";
    $heading_text[$Col+28] = "|����ȷ������稡���֡��";/*Release 5.1.0.4 Begin*/
    $heading_text[$Col+29] = "|�շ������稡���֡��";/*Release 5.1.0.4 End*/
    $heading_text[$Col+30] = "|�������ع �ͧ�ز��٧�ش";/*Release 5.1.0.4*/    
    $heading_text[$Col+31] = "|��ҹ��������Ǫҭ�����";/*Release 5.1.0.4*/
    $heading_text[$Col+32] = "|��ҹ�ͧ";
    $heading_text[$Col+33] = "|��ҹ����";
    $heading_text[$Col+34] = "|�鹷ҧ";/*Release 5.1.0.4*/    
    $heading_text[$Col+35] = "|�շ���Ѻ����ͧ�Ҫ�";/*Release 5.1.0.4*/
	$heading_text[$Col+36] = "|����ͧ�Ҫ �";
if($search_per_type == 1){
    $heading_text[$Col+37] = "|��§ҹ�дѺ";
	$heading_text[$Col+38] = "|�дѺ 1";
	$heading_text[$Col+39] = "|�ӹѡ/�ͧ";
    $heading_text[$Col+40] = "|��§ҹ�дѺ";
	$heading_text[$Col+41] = "|�дѺ 2";
	$heading_text[$Col+42] = "|�ӹѡ/�ͧ";
    $heading_text[$Col+43] = "|��§ҹ�дѺ";
	$heading_text[$Col+44] = "|�дѺ 3";
	$heading_text[$Col+45] = "|�ӹѡ/�ͧ";
    $heading_text[$Col+46] = "|��§ҹ�дѺ";
	$heading_text[$Col+47] = "|�дѺ 4";
	$heading_text[$Col+48] = "|�ӹѡ/�ͧ";
    $heading_text[$Col+49] = "|��§ҹ�дѺ";
	$heading_text[$Col+50] = "|�дѺ 5";
	$heading_text[$Col+51] = "|�ӹѡ/�ͧ";
    $heading_text[$Col+52] = "|��§ҹ�дѺ";
	$heading_text[$Col+53] = "|�дѺ 6";
	$heading_text[$Col+54] = "|�ӹѡ/�ͧ";
    $heading_text[$Col+55] = "|��§ҹ�дѺ";
	$heading_text[$Col+56] = "|�дѺ 7";
	$heading_text[$Col+57] = "|�ӹѡ/�ͧ";
    $heading_text[$Col+58] = "|��§ҹ�дѺ";
	$heading_text[$Col+59] = "|�дѺ 8";
	$heading_text[$Col+60] = "|�ӹѡ/�ͧ";
    $heading_text[$Col+61] = "|��§ҹ�дѺ";
	$heading_text[$Col+62] = "|�дѺ 9";
	$heading_text[$Col+63] = "|�ӹѡ/�ͧ";
    $heading_text[$Col+64] = "|��§ҹ�дѺ";
	$heading_text[$Col+65] = "|�дѺ 10";
	$heading_text[$Col+66] = "|�ӹѡ/�ͧ";
    $heading_text[$Col+67] = "|��§ҹ�дѺ";
	$heading_text[$Col+68] = "|�дѺ 11";
	$heading_text[$Col+69] = "|�ӹѡ/�ͧ";	
    $heading_text[$Col+70] = "|��§ҹ�дѺ";
	$heading_text[$Col+71] = "|��Ժѵԧҹ";
	$heading_text[$Col+72] = "|�ӹѡ/�ͧ";
    $heading_text[$Col+73] = "|��§ҹ�дѺ";
	$heading_text[$Col+74] = "|�ӹҭ�ҹ";
	$heading_text[$Col+75] = "|�ӹѡ/�ͧ";
    $heading_text[$Col+76] = "|��§ҹ�дѺ";
	$heading_text[$Col+77] = "|������";
	$heading_text[$Col+78] = "|�ӹѡ/�ͧ";
    $heading_text[$Col+79] = "|��§ҹ�дѺ";
	$heading_text[$Col+80] = "|�ѡ�о����";
	$heading_text[$Col+81] = "|�ӹѡ/�ͧ";	
    $heading_text[$Col+82] = "|��§ҹ�дѺ";
	$heading_text[$Col+83] = "|��Ժѵԡ��";
	$heading_text[$Col+84] = "|�ӹѡ/�ͧ";	
    $heading_text[$Col+85] = "|��§ҹ�дѺ";
	$heading_text[$Col+86] = "|�ӹҭ���"; 
	$heading_text[$Col+87] = "|�ӹѡ/�ͧ";	
    $heading_text[$Col+88] = "|��§ҹ�дѺ";
	$heading_text[$Col+89] = "|�ӹҭ��þ����";
	$heading_text[$Col+90] = "|�ӹѡ/�ͧ";	
    $heading_text[$Col+91] = "|��§ҹ�дѺ";
	$heading_text[$Col+92] = "|����Ǫҭ";
	$heading_text[$Col+93] = "|�ӹѡ/�ͧ";
    $heading_text[$Col+94] = "|��§ҹ�дѺ";
	$heading_text[$Col+95] = "|�ç�س�ز�";  
	$heading_text[$Col+96] = "|�ӹѡ/�ͧ";	
    $heading_text[$Col+97] = "|��§ҹ�дѺ";
	$heading_text[$Col+98] = "|�ӹ�¡�õ�";
	$heading_text[$Col+99] = "|�ӹѡ/�ͧ";	
    $heading_text[$Col+100] = "|��§ҹ�дѺ";
	$heading_text[$Col+101] = "|�ӹ�¡���٧";
	$heading_text[$Col+102] = "|�ӹѡ/�ͧ";    
    $heading_text[$Col+103] = "|��§ҹ�дѺ";
    $heading_text[$Col+104] = "|�����õ�"; 
	$heading_text[$Col+105] = "|�ӹѡ/�ͧ";	
    $heading_text[$Col+106] = "|��§ҹ�дѺ";
	$heading_text[$Col+107] = "|�������٧"; 
	$heading_text[$Col+108] = "|�ӹѡ/�ͧ";	
	$heading_text[$Col+109] = "�ѹ���������|�дѺ�Ѩ�غѹ";
	$heading_text[$Col+110] = "�ҹ㹡��|�ӹǳ";
	$heading_text[$Col+111] = "$ORG_TITLE|�ͺ���§ҹ";
	$heading_text[$Col+112] = "|�Ţ������";   
    $heading_text[$Col+113] = "|�������Ѿ����Ͷ��";/*Release 5.1.0.4*/
    $heading_text[$Col+114] = "|������";/*Release 5.1.0.4*/
    $heading_text[$Col+115] = "|��������Ǫҭ��ҹ��ѡ-1";/*Release 5.2.1.7*/
    $heading_text[$Col+116] = "|��������Ǫҭ��ҹ�ͧ-1";/*Release 5.2.1.7*/
    $heading_text[$Col+117] = "|��������´/��͸Ժ��-1";/*Release 5.2.1.7*/
    $heading_text[$Col+118] = "|�дѺ��������ö-1";/*Release 5.2.1.7*/
    $heading_text[$Col+119] = "|��������������Ǫҭ-1";/*Release 5.2.1.7*/  
    $heading_text[$Col+120] = "|��������Ǫҭ��ҹ��ѡ-2";/*Release 5.2.1.7*/
    $heading_text[$Col+121] = "|��������Ǫҭ��ҹ�ͧ-2";/*Release 5.2.1.7*/
    $heading_text[$Col+122] = "|��������´/��͸Ժ��-2";/*Release 5.2.1.7*/
    $heading_text[$Col+123] = "|�дѺ��������ö-2";/*Release 5.2.1.7*/
    $heading_text[$Col+124] = "|��������������Ǫҭ-2";/*Release 5.2.1.7*/
}else{
    $heading_text[$Col+37]  = "�ѹ���������|�дѺ�Ѩ�غѹ"; 
    $heading_text[$Col+38]  = "�ҹ㹡��|�ӹǳ";
    $heading_text[$Col+39]  = "$ORG_TITLE|�ͺ���§ҹ";
    $heading_text[$Col+40]  = "|�Ţ������";   
    $heading_text[$Col+41]  = "|�������Ѿ����Ͷ��";/*Release 5.1.0.4*/
    $heading_text[$Col+42]  = "|������";/*Release 5.1.0.4*/
    $heading_text[$Col+43]  = "|�Ţ����Шӵ�Ǣ���Ҫ���";/*Release 5.1.0.4*/ 
    $heading_text[$Col+44]  = "|��������Ǫҭ��ҹ��ѡ-1";/*Release 5.2.1.7*/
    $heading_text[$Col+45]  = "|��������Ǫҭ��ҹ�ͧ-1";/*Release 5.2.1.7*/
    $heading_text[$Col+46]  = "|��������´/��͸Ժ��-1";/*Release 5.2.1.7*/
    $heading_text[$Col+47]  = "|�дѺ��������ö-1";/*Release 5.2.1.7*/
    $heading_text[$Col+48]  = "|��������������Ǫҭ-1";/*Release 5.2.1.7*/  
    $heading_text[$Col+49]  = "|��������Ǫҭ��ҹ��ѡ-2";/*Release 5.2.1.7*/
    $heading_text[$Col+50]  = "|��������Ǫҭ��ҹ�ͧ-2";/*Release 5.2.1.7*/
    $heading_text[$Col+51]  = "|��������´/��͸Ժ��-2";/*Release 5.2.1.7*/
    $heading_text[$Col+52]  = "|�дѺ��������ö-2";/*Release 5.2.1.7*/
    $heading_text[$Col+53]  = "|��������������Ǫҭ-2";/*Release 5.2.1.7*/
}
	if ($MFA_FLAG == 1) { 
		$mfa_col = $Col+64;
        $heading_text[$mfa_col+1] = "|$MOV_TITLE";
		$heading_text[$mfa_col+2] = "|�Ţ�������";
		$heading_text[$mfa_col+3] = "|$POS_NO_TITLE";
		$heading_text[$mfa_col+4] = "|$PL_TITLE";
		$heading_text[$mfa_col+5] = "|$PM_TITLE";
		$heading_text[$mfa_col+6] = "|$LEVEL_TITLE";
		$heading_text[$mfa_col+7] = "|$DEPARTMENT_TITLE";
		$heading_text[$mfa_col+8] = "|$ORG_TITLE";
		$heading_text[$mfa_col+9] = "$ORG_TITLE|�ͺ���§ҹ";
		$heading_text[$mfa_col+10] = "|$REMARK_TITLE";
	}

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
	$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");/*����*/
    $column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");/*����*/
    $column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");/*����*/
    $column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");/*����*/
    $column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");/*����*/
	$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
    $column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");/*Release 5.1.0.4*/
	$column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");    
    $column_function[10] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");/* Release 5.2.1.18 */     
	$column_function[11] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[12] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[13] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[14] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");    
	if ($BKK_FLAG!=1) $column_function[15] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");     
	$column_function[$Col] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+6] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");     
    $column_function[$Col+8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");   
	$column_function[$Col+9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+10] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+11] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+12] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+13] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");       
    $column_function[$Col+14] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");/*Release 5.1.0.4*/
    $column_function[$Col+15] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");/*Release 5.1.0.4*/
    $column_function[$Col+16] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");/*Release 5.1.0.4*/
	$column_function[$Col+17] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+18] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+19] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+20] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+21] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");/*Release 5.1.0.4*/
    $column_function[$Col+22] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");/*Release 5.1.0.4*/        
    $column_function[$Col+23] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");/*Release 5.1.0.4*/
	$column_function[$Col+24] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+25] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+26] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+27] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+28] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");/*Release 5.1.0.4*/
    $column_function[$Col+29] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");/*Release 5.1.0.4*/   
    $column_function[$Col+30] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");/*Release 5.1.0.4*/
    $column_function[$Col+31] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");/*Release 5.1.0.4*/
    $column_function[$Col+32] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");/*Release 5.1.0.4*/
    $column_function[$Col+33] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");/*Release 5.1.0.4*/
	$column_function[$Col+34] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+35] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+36] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
if($search_per_type == 1){
	$column_function[$Col+37] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+38] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+39] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+40] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+41] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+42] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+43] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+44] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+45] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+46] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+47] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+48] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+49] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+50] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+51] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+52] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+53] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+54] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+55] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+56] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+57] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+58] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");    
	$column_function[$Col+59] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+60] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+61] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+62] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+63] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+64] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+65] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+66] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+67] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+68] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+69] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+70] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+71] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+72] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");	// if ($BKK_FLAG!=1) column �� 56 ���������� ���� 55
    $column_function[$Col+73] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+74] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+75] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+76] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+77] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+78] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+79] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+80] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+81] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+82] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+83] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+84] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+85] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+86] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
    $column_function[$Col+87] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");/*Release 5.1.0.4*/
    $column_function[$Col+88] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");/*Release 5.1.0.4*/
    $column_function[$Col+89] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");/*Release 5.1.0.4*/
    $column_function[$Col+90] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");/*Release 5.1.0.4*/
    $column_function[$Col+91] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");/*Release 5.1.0.4*/   
    $column_function[$Col+92] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$column_function[$Col+93] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$column_function[$Col+94] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$column_function[$Col+95] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+96] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");       
    $column_function[$Col+97] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$column_function[$Col+98] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$column_function[$Col+99] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$column_function[$Col+100] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+101] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+102] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+103] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+104] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+105] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+106] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+107] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+108] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+109] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+110] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+111] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+112] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+113] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+114] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+115] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+116] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+117] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+118] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+119] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+120] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+121] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+123] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+124] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$Col+125] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
}else{
    $column_function[$Col+37] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+38] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+39] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+40] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+41] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+42] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+43] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+44] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+45] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+46] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+47] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+48] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+49] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+50] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+51] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+52] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[$Col+53] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
}
	if ($MFA_FLAG == 1) { 
		$mfa_col = $Col+64;
		$column_function[$mfa_col+1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[$mfa_col+2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[$mfa_col+3] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[$mfa_col+4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[$mfa_col+5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[$mfa_col+6] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[$mfa_col+7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[$mfa_col+8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[$mfa_col+9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");	
		$column_function[$mfa_col+10] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	}

	if ($MFA_FLAG == 1) { 
		$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		$data_align = array("C","L","C","C","L","L","L","L","L","L","L","L","L","C","C","C","C","C","L","L","L","L","L","L","L","L","L","L","L","L","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","L","L","L","L","C","L","L","C","L","L","L","L","L");
	} elseif ($BKK_FLAG == 1) {
		$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		$data_align = array("C","L","C","C","L","L","L","L","L","L","L","L","C","C","C","C","C","L","L","L","L","L","L","L","L","L","L","L","L","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","L","L","L");
	} else {
		$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		$data_align = array("C","L","L","L","L","L","L","L","L","R","R","L","L","L","L","L","L","L","L","L","R","R","R","R","R","R","L","L","L","L","L","R","L","L","L","L","L","L","R","L","L","L","L","L","L","L","L","L","L","L","L","L","R","L","R","L","R","L","R","L","R","L","R","L","R","L","R","L","R","L","R","L","R","L","R","L","R","L","R","L","R","R","R","R","R","R","R","R","R","R","R","R","L","R","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L");
	}

//	for($i=0; $i < count($heading_text); $i++) {
//		echo "in_format  $i--".$heading_text[$i]."<br>";
//	}

	$total_head_width = 0;
	for($iii=0; $iii < count($heading_width); $iii++) 	$total_head_width += (int)$heading_width[$iii];
	
	if (!$COLUMN_FORMAT) {	// ��ͧ��˹��� element �������� form1 ����  <input type="hidden" name="COLUMN_FORMAT" value="<?=$COLUMN_FORMAT
		$arr_column_map = (array) null;
		$arr_column_sel = (array) null;
              
		for($i=0; $i < count($heading_text); $i++) {
			$arr_column_map[] = $i;		// link index �ͧ head 
			$arr_column_sel[] = 1;			// 1=�ʴ�	0=����ʴ�   �������ա�û�Ѻ����§ҹ �����ʴ����
                       //echo $i.">".$heading_text[$i]."<br>"; 
		}
                //die();
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