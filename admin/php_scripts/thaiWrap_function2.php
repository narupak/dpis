<?php
function thaiWrap($dataIn){
	$specword = array("��ʵ��","���˹�","����","�����","�ѧ��Ѵ","����ͧ","��Ժѵ�","�ѭ��","���˹�ҷ��", "��к��", "�ҭ������", "����Թ���", "��ᾧྪ�", "�͹��", "�ѹ�����", "���ԧ���", "�ź���", "��¹ҷ", "�������", "�����", "��§���", "��§����", "��ѧ", "��Ҵ", "�ҡ", "��ù�¡", "��û��", "��þ��", "����Ҫ����", "�����ո����Ҫ", "������ä�", "�������", "��Ҹ����", "��ҹ", "���������", "�����ҹ�", "��ШǺ���բѹ��", "��Ҩչ����", "�ѵ�ҹ�", "��й�������ظ��", "�����", "�ѧ��", "�ѷ�ا", "�ԨԵ�", "��ɳ��š", "ྪú���", "ྪú�ó�", "���", "����", "�����ä��", "�ء�����", "�����ͧ�͹", "��ʸ�", "����", "�������", "�йͧ", "���ͧ", "�Ҫ����", "ž����", "�ӻҧ", "�Ӿٹ", "���", "�������", "ʡŹ��", "ʧ���", "ʵ��", "��طû�ҡ��", "��ط�ʧ����", "��ط��Ҥ�", "������", "��к���", "�ԧ�����", "��⢷��", "�ؾ�ó����", "����ɮ��ҹ�", "���Թ���", "˹ͧ���", "˹ͧ�������", "��ҧ�ͧ", "�ӹҨ��ԭ", "�شøҹ�", "�صôԵ��", "�ط�¸ҹ�", "�غ��Ҫ�ҹ�", "���", "���õ���"); // ������੾��������
	$mypatt = implode("|", $specword);
	$min_pos_i = strlen($dataIn);
	$pos_i = 0;
	$i_ord = 0;
	$ret_len = 0;
	
	if (preg_match("/($mypatt)/",$dataIn,$match)) {
		$pos_i = strpos($dataIn, $match[0]);
		if ($pos_i < $min_pos_i) {
			$min_pos_i = $pos_i;
			$ret_len = strlen($match[0]);
		}
	} 
	if (preg_match("/(�([�-�])(�|�)?��(��)?)/",$dataIn,$match)) { // // ������ ����� ��� ��� ���� ������
		$pos_i = strpos($dataIn, $match[0]);
		if ($pos_i < $min_pos_i) {
			$min_pos_i = $pos_i;
			$ret_len = strlen($match[0]);
		}
	} 
	if (preg_replace("/(��(?:�|�|�á��|���|������|�ԡ))/","$1<wbr />",$dataIn,$match)) { // 
		$pos_i = strpos($dataIn, $match[0]);
		if ($pos_i < $min_pos_i) {
			$min_pos_i = $pos_i;
			$ret_len = strlen($match[0]);
		}
	}
	if (preg_match("/(�(?:�|ѹ|��))/",$dataIn,$match)) { // ��� �ѹ ���
		$pos_i = strpos($dataIn, $match[0]);
		if ($pos_i < $min_pos_i) {
			$min_pos_i = $pos_i;
			$ret_len = strlen($match[0]);
		}
	} 
	if (preg_match("/((�|�)([�-�])[����]([�|�|�|�|�|�|�|�|�])?([�|�|�])?)/",$dataIn,$match)) { // ���� ᡧ �Χ �ç ��� �ŧ ᴧ �ǧ ᵧ ���� ��� ὧ ��ŧ ��� ����
		$pos_i = strpos($dataIn, $match[0]);
		if ($pos_i < $min_pos_i) {
			$min_pos_i = $pos_i;
			$ret_len = strlen($match[0]);
		}
	} 
	if (preg_match("/(([�����������������������������])(?:�(?:�(?:���)?|��|(?:�|��)�|�ҹ)|�(?:�(?:��ͧ|�)|��?�|�(?:���|���)|(?:��|��|��)�|����|�Դ|��|���)|�(?:ѹ|Ѻ|��|�|����|���)|�(?:�(?:�|��)|�|��)|�(?:ͧ|��)|�(?:�|�|ҡ|(?:�|��)�)|�(?:�(?:�|��)|��|��|��)|�(?:��|��|��|�|��)|�(?:�ҧ|��|ѹ)|�(?:���|�|��)|�(?:��|��|��)|�(?:���|���|���)|�(?:�(?:��|�ҧ)|[��]�|Ҩ|(?:��|�)�|���)|�(?:�(?:ҧ|�)|�ͧ|��|��)|�(?:�(?:��|ҹ)|�)|�(?:֧|��|١)|�(?:��|�|�)|�(?:����|�|ѭ��)|�(?:ѧ��|���ö|�ǹ|��|Ӥѭ)|(?:���|��|��|�)�|��|�Ѻ|���Ե|�ҹ|��ä))/","$1<wbr />",$dataIn,$match)) { 
		$pos_i = strpos($dataIn, $match[0]);
		if ($pos_i < $min_pos_i) {
			$min_pos_i = $pos_i;
			$ret_len = strlen($match[0]);
		}
	}
	if (preg_match("/(�([�-�])�([�|�|�|�])?§)/",$dataIn,$match)) { // ��§ ����§ ����§ ����§ 
		$pos_i = strpos($dataIn, $match[0]);
		if ($pos_i < $min_pos_i) {
			$min_pos_i = $pos_i;
			$ret_len = strlen($match[0]);
		}
	}

	$space_ch_i = strpos($dataIn, " ");
	if ($space_ch_i > -1) {
		if ($space_ch_i < $min_pos_i) {
			$min_pos=0;
			$ret_len = $space_ch_i;
		} else if ($space_ch_i < $min_pos_i + $ret_len) {
			$ret_len = $space_ch_i - $min_pos_i;
		}
	}

	$ch = substr($dataIn,$min_pos_i+$ret_len,1);
	if (strpos("���������������",$ch) > -1) {
		$ret_len--;
	} else {
		$ch = substr($dataIn,$min_pos_i+$ret_len+1,1);
		if (strpos(" �����",$ch) > -1) {
			$ret_len++;
		}		
	}

	return $min_pos_i+$ret_len;
}
function thaiCutLine($dataIn, $n, $delim){
	$vowels = array("<br>", "<BR>", "<Br>", "<br />", "<BR />", "<Br />", "<br/>", "<BR/>", "<Br/>", "\n");
	$tempdataIn = str_replace($vowels, "", $dataIn);
	$midch_cnt=0;
	$i = 0;
	$ctext = "";
	$out = "";
	while (strlen($tempdataIn) > 0) {
		$ch = substr($tempdataIn,$i,1);
		if ($ch == "\n") { // �� �Ѵ line
			$out = "$out".trim($ctext)."$delim";
//			echo "******* $out(".strlen($out).")<br>";
			$ctext=substr($tempdataIn,0,$mylen);
			$tempdataIn = substr($tempdataIn,$mylen);
			$midch_cnt=$mylen1;
		} else if (strpos( "���������������", $ch) === false) { // ������ѡ���ǡ�ҧ (������ѡ��¡   �  �  �  �  �  �  �  �  �  �  �  �  �)
			$mylen = thaiWrap($tempdataIn);
			$text1 = substr($tempdataIn,0,$mylen);
			$cnt_up=0;
			for($ii=0; $ii < strlen($text1); $ii++) {
				$ch1=substr($text1,$ii,1);
				if (strpos( "���������������", $ch1) > -1) { // ���ѡ��¡   �  �  �  �  �  �  �  �  �  �  �  �  �
					$cnt_up++;
				}
			}
			$mylen1 = $mylen - $cnt_up;
			echo "midch_cnt = $midch_cnt + $mylen($out)<br>";
			if ($midch_cnt + $mylen1 > $n) {
				$out = "$out".trim($ctext)."$delim";
				$ctext=substr($tempdataIn,0,$mylen);
				echo "new-".$ctext."(".strlen($ctext).")<br>";
				$tempdataIn = substr($tempdataIn,$mylen);
				$midch_cnt=$mylen1;
			} else { // <= $n
				$ctext = "$ctext".substr($tempdataIn,0,$mylen);
				echo "old-".$ctext."(".strlen($ctext).")<br>";
				$tempdataIn = substr($tempdataIn,$mylen);
				$midch_cnt = $midch_cnt + $mylen1;
			}
			$i=-1;
		} // end if ch
		$i++;
	} // end loop while
	$out = "$out".trim($ctext);
	return $out; 
}
?>