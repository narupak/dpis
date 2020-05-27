<?
	  function check_1_2($CHK_12) {
		if($CHK_12){
			if($CHK_12 == "1" || $CHK_12 == "2"){
				return $CHK_01;
			}else if($CHK_12 == "ใช้งาน" || $CHK_12 == "ผ่าน" || $CHK_12 == "พิมพ์" || $CHK_12 == "ใช่"){
				return  "1";
			}else if($CHK_12 == "ยกเลิก" || $CHK_12 == "ไม่ผ่าน" || $CHK_12 == "ไม่พิมพ์" || $CHK_12 == "ไม่ใช่"){
				return "2";
			}else{
				return "ค่าไม่ถูกต้อง";
			}
		}else{
			return "2";
		}
	} 

	function check_0_1($CHK_01) {
		if($CHK_01){
			if ($CHK_01 == "0" || $CHK_01== "1") {
				return $CHK_01;
			}else if($CHK_01 == "ใช้งาน" || $CHK_01 =="ผ่าน" || $CHK_01 == "พิมพ์" || $CHK_01 == "ใช่"){
				return  "1";
			}else if($CHK_01 == "ยกเลิก" || $CHK_01 =="ไม่ผ่าน" || $CHK_01 == "ไม่พิมพ์" || $CHK_01 == "ไม่ใช่"){
				return "0";
			}else{
				return "ค่าไม่ถูกต้อง";
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
			}else if($CHK_YN == "ใช่" || $CHK_YN == "จริง" || $CHK_YN == "1" || $CHK_YN == "ตรวจสอบ"){
				return "Y";
			}else if($CHK_YN == "ไม่ใช่" || $CHK_YN == "ไม่จริง" || $CHK_YN == "0"){
				return "N";
			}else{
				return "ค่าไม่ถูกต้อง";
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
			}else if($CHK_TYPE == "อบรม"){
				$CHK_TYPE = "1";
				return $CHK_TYPE;
			}else if($CHK_TYPE == "ดูงาน"){
				$CHK_TYPE = "2";
				return $CHK_TYPE;
			}else if($CHK_TYPE == "สัมมนา"){    
				$CHK_TYPE = "3";
				return $CHK_TYPE;
			}else if($CHK_TYPE == "ประชุม"){
				$CHK_TYPE = "4";
				return $CHK_TYPE;
			}else{
				return "ค่าไม่ถูกต้อง";
			}
		}else{
			return "";
		}
	}
	
	function check_TRN_FLAG($CHK_TYPE) {
		if($CHK_TYPE){
			if ($CHK_TYPE=="1" || $CHK_TYPE=="2" || $CHK_TYPE=="3") {
				return $CHK_TYPE;
			}else if($CHK_TYPE == "ระดับสูง"){
				$CHK_TYPE = "1";
				return $CHK_TYPE;
			}else if($CHK_TYPE == "ระดับกลาง"){
				$CHK_TYPE = "2";
				return $CHK_TYPE;
			}else if($CHK_TYPE == "ระดับต้น"){    
				$CHK_TYPE = "3";
				return $CHK_TYPE;
			}else{
				return "ค่าไม่ถูกต้อง";
			}
		}else{
			return "";
		}
	}  
	 //close////////////////////////////////////////// training //////////////////////////////////////////////////////// 
	
?>

