<?
	  function check_1_2($CHK_12) {
		if($CHK_12){
			if($CHK_12 == "1" || $CHK_12 == "2"){
				return $CHK_01;
			}else if($CHK_12 == "��ҹ" || $CHK_12 == "��ҹ" || $CHK_12 == "�����" || $CHK_12 == "��"){
				return  "1";
			}else if($CHK_12 == "¡��ԡ" || $CHK_12 == "����ҹ" || $CHK_12 == "�������" || $CHK_12 == "�����"){
				return "2";
			}else{
				return "������١��ͧ";
			}
		}else{
			return "2";
		}
	} 

	function check_0_1($CHK_01) {
		if($CHK_01){
			if ($CHK_01 == "0" || $CHK_01== "1") {
				return $CHK_01;
			}else if($CHK_01 == "��ҹ" || $CHK_01 =="��ҹ" || $CHK_01 == "�����" || $CHK_01 == "��"){
				return  "1";
			}else if($CHK_01 == "¡��ԡ" || $CHK_01 =="����ҹ" || $CHK_01 == "�������" || $CHK_01 == "�����"){
				return "0";
			}else{
				return "������١��ͧ";
			}
		}else{
			return "";
		}
	} 
	
	function check_Y_N($CHK_YN) {
		$CHK_YN = strtoupper($CHK_YN);
		if($CHK_YN){
			if ($CHK_YN=="Y" || $CHK_YN=="N") {
				return $CHK_YN;
			}else if($CHK_YN == "��" || $CHK_YN == "��ԧ" || $CHK_YN == "1" || $CHK_YN == "��Ǩ�ͺ"){
				return "Y";
			}else if($CHK_YN == "�����" || $CHK_YN == "����ԧ" || $CHK_YN == "0"){
				return "N";
			}else{
				return "������١��ͧ";
			}
		}else{
			return "";
		}
	}
	//open////////////////////////////////////////// training ////////////////////////////////////////////////////////
	function check_TRN_TYPE($CHK_TYPE) {
		if($CHK_TYPE){
			if ($CHK_TYPE=="1" || $CHK_TYPE=="2" || $CHK_TYPE=="3" || $CHK_TYPE=="4") {
				return $CHK_TYPE;
			}else if($CHK_TYPE == "ͺ��"){
				$CHK_TYPE = "1";
				return $CHK_TYPE;
			}else if($CHK_TYPE == "�٧ҹ"){
				$CHK_TYPE = "2";
				return $CHK_TYPE;
			}else if($CHK_TYPE == "������"){    
				$CHK_TYPE = "3";
				return $CHK_TYPE;
			}else if($CHK_TYPE == "��Ъ��"){
				$CHK_TYPE = "4";
				return $CHK_TYPE;
			}else{
				return "������١��ͧ";
			}
		}else{
			return "";
		}
	}
	
	function check_TRN_FLAG($CHK_TYPE) {
		if($CHK_TYPE){
			if ($CHK_TYPE=="1" || $CHK_TYPE=="2" || $CHK_TYPE=="3") {
				return $CHK_TYPE;
			}else if($CHK_TYPE == "�дѺ�٧"){
				$CHK_TYPE = "1";
				return $CHK_TYPE;
			}else if($CHK_TYPE == "�дѺ��ҧ"){
				$CHK_TYPE = "2";
				return $CHK_TYPE;
			}else if($CHK_TYPE == "�дѺ��"){    
				$CHK_TYPE = "3";
				return $CHK_TYPE;
			}else{
				return "������١��ͧ";
			}
		}else{
			return "";
		}
	}  
	 //close////////////////////////////////////////// training //////////////////////////////////////////////////////// 
	
?>

