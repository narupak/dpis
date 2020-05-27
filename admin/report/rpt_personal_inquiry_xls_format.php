<? 
//	fix head for rpt_personal_inquiry_xls
	if ($BKK_FLAG!=1) $Col = 16; else $Col = 8; /*9*/
 
	$heading_width[0] = "20";/*เลขที่ตำแหน่ง*/
	$heading_width[1] = "27";/*คำนำหน้าชื่อ(ไทย)*/
    $heading_width[2] = "35";/*ชื่อ(ไทย)*/
    $heading_width[3] = "35";/*นามสกุล(ไทย)*/
    $heading_width[4] = "33";/*คำนำหน้าชื่อ(อังกฤษ)*/
    $heading_width[5] = "45";/*ชื่อ(อังกฤษ)*/
    $heading_width[6] = "45";/*นามสกุล(อังกฤษ)*/
	$heading_width[7] = "35";/*เลขประจำตัวประชาชน*/
    $heading_width[8] = "15";/*Release 5.1.0.4 Begin แทรก เพศ*/
	$heading_width[9] = "25";/*วัน/เดือน/ปีเกิด*/  
    $heading_width[10] = "60";/*อายุข้าราชการ*/ /*http://dpis.ocsc.go.th/Service/node/1716*/    
	$heading_width[11] = "40";/*ตำแหน่งในสายงาน*/
	$heading_width[12] = "40";/*ตำแหน่งในการบริหารงาน*/
	$heading_width[13] = "40";/*ประเภทตำแหน่ง*/
	$heading_width[14] = "40";/*ระดับตำแหน่ง*/   
	if ($BKK_FLAG!=1) $heading_width[15] = "50";/*กระทรวง*/     
	$heading_width[$Col] = "50";/*กรม*/
	$heading_width[$Col+1] = "50";/*สำนัก/กอง*/
	$heading_width[$Col+2] = "50";/*ต่ำกว่าสำนัก/กอง 1 ระดับ*/
	$heading_width[$Col+3] = "50";/*ต่ำกว่าสำนัก/กอง 2 ระดับ*/
	$heading_width[$Col+4] = "20";/*เงินเดือน*/
	$heading_width[$Col+5] = "30";/*เงินประจำตำแหน่ง*/
	$heading_width[$Col+6] = "30";/*วันเกษียณอายุ*/
	$heading_width[$Col+7] = "30";/*วันที่เริ่มรับราชการ*/
    $heading_width[$Col+8] = "60";/*อายุราชการ(อายุงาน)*/   
	$heading_width[$Col+9] = "30";/*วันที่เข้าส่วนราชการ*/
	$heading_width[$Col+10] = "50";/*วุฒิที่ใช้บรรจุ ระดับการศึกษา*/
	$heading_width[$Col+11] = "50";/*วุฒิที่ใช้บรรจุ วุฒิการศึกษา*/
	$heading_width[$Col+12] = "50";/*วุฒิที่ใช้บรรจุ สาขาวิชาเอก*/
	$heading_width[$Col+13] = "50";/*วุฒิที่ใช้บรรจุ สถาบันการศึกษา*/
    $heading_width[$Col+14] = "40";/*Release 5.1.0.4 Begin วุฒิที่ใช้บรรจุ ประเทศที่สำเร็จการศึกษา*/
    $heading_width[$Col+15] = "30";/*Release 5.1.0.4 Begin วุฒิที่ใช้บรรจุ ปีที่สำเร็จการศึกษา*/
    $heading_width[$Col+16] = "50";/*วุฒิที่ใช้บรรจุ ประเภททุน*/
    $heading_width[$Col+17] = "50";/*วุฒิในตำแหน่งปัจจุบัน ระดับการศึกษา*/ 
	$heading_width[$Col+18] = "50";/*วุฒิในตำแหน่งปัจจุบัน วุฒิการศึกษา*/
    $heading_width[$Col+19] = "50";/*วุฒิในตำแหน่งปัจจุบัน สาขาวิชาเอก*/
	$heading_width[$Col+20] = "50";/*วุฒิในตำแหน่งปัจจุบัน สถาบันการศึกษา*/
    $heading_width[$Col+21] = "40";/*Release 5.1.0.4 Begin วุฒิในตำแหน่งปัจจุบัน ประเทศที่สำเร็จการศึกษา*/
    $heading_width[$Col+22] = "30";/*Release 5.1.0.4 Begin วุฒิในตำแหน่งปัจจุบัน ปีที่สำเร็จการศึกษา*/
	$heading_width[$Col+23] = "50";/*วุฒิในตำแหน่งปัจจุบัน ประเภททุน*/
    $heading_width[$Col+24] = "50";/*วุฒิสูงสุด ระดับการศึกษา*/
	$heading_width[$Col+25] = "50";/*วุฒิสูงสุด วุฒิการศึกษา*/
	$heading_width[$Col+26] = "50";/*วุฒิสูงสุด สาขาวิชาเอก*/
	$heading_width[$Col+27] = "30";/*วุฒิสูงสุด สถาบันการศึกษา*/
    $heading_width[$Col+28] = "40";/*Release 5.1.0.4 Begin วุฒิสูงสุด ประเทศที่สำเร็จการศึกษา*/
    $heading_width[$Col+29] = "30";/*Release 5.1.0.4 Begin วุฒิสูงสุด ปีที่สำเร็จการศึกษา*/
	$heading_width[$Col+30] = "50";/*วุฒิสูงสุด ประเภททุน*/
    $heading_width[$Col+31] = "50";/*ด้านความเชี่ยวชาญพิเศษ*/
    $heading_width[$Col+32] = "50";/*ด้านรอง*/
    $heading_width[$Col+33] = "50";/*ด้านย่อย*/
    $heading_width[$Col+34] = "50";/*เน้นทาง*/
    $heading_width[$Col+35] = "35";/*Release 5.1.0.4 Begin ปีที่รับเครื่องราชฯ */
    $heading_width[$Col+36] = "25";/*เครื่องราช ฯ*/ 
if($search_per_type == 1){
    $heading_width[$Col+37] = "50";/*สายงานระดับ*/
	$heading_width[$Col+38] = "50";/*ระดับ 1*/
	$heading_width[$Col+39] = "50";/*สำนักกอง*/
    $heading_width[$Col+40] = "50";/*สายงานระดับ*/
	$heading_width[$Col+41] = "50";/*ระดับ 2*/
	$heading_width[$Col+42] = "50";/*สำนักกอง*/
    $heading_width[$Col+43] = "50";/*สายงานระดับ*/
	$heading_width[$Col+44] = "50";/*ระดับ 3*/
	$heading_width[$Col+45] = "50";/*สำนักกอง*/
    $heading_width[$Col+46] = "50";/*สายงานระดับ*/
	$heading_width[$Col+47] = "50";/*ระดับ 4*/
	$heading_width[$Col+48] = "50";/*สำนักกอง*/
    $heading_width[$Col+49] = "50";/*สายงานระดับ*/
	$heading_width[$Col+50] = "50";/*ระดับ 5*/
	$heading_width[$Col+51] = "50";/*สำนักกอง*/
    $heading_width[$Col+52] = "50";/*สายงานระดับ*/
	$heading_width[$Col+53] = "50";/*ระดับ 6*/
	$heading_width[$Col+54] = "50";/*สำนักกอง*/
    $heading_width[$Col+55] = "50";/*สายงานระดับ*/
	$heading_width[$Col+56] = "50";/*ระดับ 7*/
	$heading_width[$Col+57] = "50";/*สำนักกอง*/
    $heading_width[$Col+58] = "50";/*สายงานระดับ*/
	$heading_width[$Col+59] = "50";/*ระดับ 8*/
	$heading_width[$Col+60] = "50";/*สำนักกอง*/
    $heading_width[$Col+61] = "50";/*สายงานระดับ*/
	$heading_width[$Col+62] = "50";/*ระดับ 9*/
	$heading_width[$Col+63] = "50";/*สำนักกอง*/
    $heading_width[$Col+64] = "50";/*สายงานระดับ*/
	$heading_width[$Col+65] = "50";/*ระดับ 10*/
	$heading_width[$Col+66] = "50";/*สำนักกอง*/
    $heading_width[$Col+67] = "50";/*สายงานระดับ*/
	$heading_width[$Col+68] = "50";/*ระดับ 11*/
	$heading_width[$Col+69] = "50";/*สำนักกอง*/
    $heading_width[$Col+70] = "50";/*สายงานระดับ*/
	$heading_width[$Col+71] = "50";/*ปฏิบัติงาน*/ 
	$heading_width[$Col+72] = "50";/*สำนักกอง*/
    $heading_width[$Col+73] = "50";/*สายงานระดับ*/
	$heading_width[$Col+74] = "50";/*ชำนาญงาน*/ 
	$heading_width[$Col+75] = "50";/*สำนักกอง*/
    $heading_width[$Col+76] = "50";/*สายงานระดับ*/
	$heading_width[$Col+77] = "50";/*อาวุโส*/
	$heading_width[$Col+78] = "50";/*สำนักกอง*/
    $heading_width[$Col+79] = "50";/*สายงานระดับ*/
	$heading_width[$Col+82] = "50";/*ทักษะพิเศษ*/
	$heading_width[$Col+81] = "50";/*สำนักกอง*/
    $heading_width[$Col+82] = "50";/*สายงานระดับ*/
	$heading_width[$Col+83] = "50";/*ปฏิบัติการ*/  
	$heading_width[$Col+84] = "50";/*สำนักกอง*/	
    $heading_width[$Col+85] = "50";/*สายงานระดับ*/
	$heading_width[$Col+86] = "50";/*ชำนาญการ*/ 
	$heading_width[$Col+87] = "50";/*สำนักกอง*/	
    $heading_width[$Col+88] = "50";/*สายงานระดับ*/
	$heading_width[$Col+89] = "50";/*ชำนาญการพิเศษ*/  
	$heading_width[$Col+90] = "50";/*สำนักกอง*/	
    $heading_width[$Col+91] = "50";/*สายงานระดับ*/
	$heading_width[$Col+92] = "50";/*เชี่ยวชาญ*/
	$heading_width[$Col+93] = "50";/*สำนักกอง*/	
    $heading_width[$Col+94] = "50";/*สายงานระดับ*/
	$heading_width[$Col+95] = "50";/*ทรงคุณวุฒิ*/
	$heading_width[$Col+96] = "50";/*สำนักกอง*/	
    $heading_width[$Col+97] = "50";/*สายงานระดับ*/
	$heading_width[$Col+98] = "50";/*อำนวยการต้น*/
	$heading_width[$Col+99] = "50";/*สำนักกอง*/	
    $heading_width[$Col+100] = "50";/*สายงานระดับ*/
	$heading_width[$Col+101] = "50";/*อำนวยการสูง*/
	$heading_width[$Col+102] = "50";/*สำนักกอง*/	
    $heading_width[$Col+103] = "50";/*สายงานระดับ*/
	$heading_width[$Col+104] = "50";/*บริหารต้น*/ 
	$heading_width[$Col+105] = "50";/*สำนักกอง*/	
    $heading_width[$Col+106] = "50";/*สายงานระดับ*/
	$heading_width[$Col+107] = "50";/*บริหารสูง*/
	$heading_width[$Col+108] = "50";/*สำนักกอง*/	
	$heading_width[$Col+109] = "50";/*วันที่เข้าสู่ระดับปัจจุบัน*/
	$heading_width[$Col+110] = "50";/*ฐานในการคำนวณ*/
	$heading_width[$Col+111] = "50";/*สำนัก/กองมอบหมายงาน*/
	$heading_width[$Col+112] = "30";/*เลขที่แฟ้ม*/
    $heading_width[$Col+113] = "30";/*เบอร์โทรศัพท์มือถือ*/
    $heading_width[$Col+114] = "50";/*อีเมล์*/
    $heading_width[$Col+115] = "50";/*เลขที่ประจำตัวข้าราชการ*/   
    $heading_width[$Col+116] = "80";/*ความเชี่ยวชาญด้านหลัก*/
    $heading_width[$Col+117] = "80";/*ความเชี่ยวชาญด้านรอง*/
    $heading_width[$Col+118] = "80";/*รายละเอียด/คำอธิบาย*/
    $heading_width[$Col+119] = "80";/*ระดับความสามารถ*/
    $heading_width[$Col+120] = "80";/*ประเภทความเชี่ยวชาญ*/       
    $heading_width[$Col+121] = "80";/*ความเชี่ยวชาญด้านหลัก2*/
    $heading_width[$Col+122] = "80";/*ความเชี่ยวชาญด้านรอง2*/
    $heading_width[$Col+123] = "80";/*รายละเอียด/คำอธิบาย2*/
    $heading_width[$Col+124] = "80";/*ระดับความสามารถ2*/
    $heading_width[$Col+125] = "80";/*ประเภทความเชี่ยวชาญ2*/
}else{
    $heading_width[$Col+37] = "50";/*วันที่เข้าสู่ระดับปัจจุบัน*/
	$heading_width[$Col+38] = "50";/*ฐานในการคำนวณ*/
	$heading_width[$Col+39] = "50";/*สำนัก/กองมอบหมายงาน*/
	$heading_width[$Col+40] = "30";/*เลขที่แฟ้ม*/
    $heading_width[$Col+41] = "30";/*เบอร์โทรศัพท์มือถือ*/
    $heading_width[$Col+42] = "50";/*อีเมล์*/
    $heading_width[$Col+43] = "50";/*เลขที่ประจำตัวข้าราชการ*/   
    $heading_width[$Col+44] = "80";/*ความเชี่ยวชาญด้านหลัก*/
    $heading_width[$Col+45] = "80";/*ความเชี่ยวชาญด้านรอง*/
    $heading_width[$Col+46] = "80";/*รายละเอียด/คำอธิบาย*/
    $heading_width[$Col+47] = "80";/*ระดับความสามารถ*/
    $heading_width[$Col+48] = "80";/*ประเภทความเชี่ยวชาญ*/       
    $heading_width[$Col+49] = "80";/*ความเชี่ยวชาญด้านหลัก2*/
    $heading_width[$Col+50] = "80";/*ความเชี่ยวชาญด้านรอง2*/
    $heading_width[$Col+51] = "80";/*รายละเอียด/คำอธิบาย2*/
    $heading_width[$Col+52] = "80";/*ระดับความสามารถ2*/
    $heading_width[$Col+53] = "80";/*ประเภทความเชี่ยวชาญ2*/
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
	$heading_text[1] = "|คำนำหน้าชื่อ(ไทย)";
    $heading_text[2] = "|ชื่อ(ไทย)";
    $heading_text[3] = "|นามสกุล(ไทย)";
    $heading_text[4] = "|คำนำหน้าชื่อ(อังกฤษ)";
    $heading_text[5] = "|ชื่อ(อังกฤษ)";
    $heading_text[6] = "|นามสกุล(อังกฤษ)";     
	$heading_text[7] = "|เลขประจำตัวประชาชน";
    $heading_text[8] = "|เพศ";/*Release 5.1.0.4 Begin*/
	$heading_text[9] = "|วัน/เดือน/ปีเกิด";   
    $heading_text[10] = "|อายุข้าราชการ";/*Release 5.2.1.18*/   
	$heading_text[11] = "|$PL_TITLE";
	$heading_text[12] = "|$PM_TITLE";
	$heading_text[13] = "| $PT_TITLE";
	$heading_text[14] = "|$LEVEL_TITLE";
	if ($BKK_FLAG!=1) $heading_text[15] = "|$MINISTRY_TITLE";     
	$heading_text[$Col] = "|$DEPARTMENT_TITLE";
	$heading_text[$Col+1] = "|$ORG_TITLE";
	$heading_text[$Col+2] = "|$ORG_TITLE1";
	$heading_text[$Col+3] = "|$ORG_TITLE2";
	$heading_text[$Col+4] = "|เงินเดือน";
	$heading_text[$Col+5] = "|เงินประจำตำแหน่ง";
	$heading_text[$Col+6] = "|วันเกษียณอายุ";
	$heading_text[$Col+7] = "|วันที่เริ่มรับราชการ";   
    $heading_text[$Col+8] = "|อายุราชการ(อายุงาน)";     
	$heading_text[$Col+9] = "|วันที่เข้าส่วนราชการ";
	$heading_text[$Col+10] = "|$EL_TITLE";
	$heading_text[$Col+11] = "|$EN_TITLE";
	$heading_text[$Col+12] = "วุฒิที่ใช้บรรจุ|$EM_TITLE";
	$heading_text[$Col+13] = "|$INS_TITLE";
    $heading_text[$Col+14] = "|ประเทศที่สำเร็จการศึกษา";/*Release 5.1.0.4 Begin*/
    $heading_text[$Col+15] = "|ปีที่สำเร็จการศึกษา";/*Release 5.1.0.4 End*/
    $heading_text[$Col+16] = "|ประเภททุน ของวุฒิที่ใช้บรรจุ";/*Release 5.1.0.4*/    
	$heading_text[$Col+17] = "|$EL_TITLE";
	$heading_text[$Col+18] = "|$EN_TITLE";        
	$heading_text[$Col+19] = "วุฒิในตำแหน่งปัจจุบัน|$EM_TITLE";
	$heading_text[$Col+20] = "|$INS_TITLE";
    $heading_text[$Col+21] = "|ประเทศที่สำเร็จการศึกษา";/*Release 5.1.0.4 Begin*/
    $heading_text[$Col+22] = "|ปีที่สำเร็จการศึกษา";/*Release 5.1.0.4 End*/
    $heading_text[$Col+23] = "|ประเภททุน ของวุฒิในตำแหน่งปัจจุบัน";/*Release 5.1.0.4*/    
	$heading_text[$Col+24] = "|$EL_TITLE";
	$heading_text[$Col+25] = "|$EN_TITLE";
	$heading_text[$Col+26] = "วุฒิสูงสุด|$EM_TITLE";
	$heading_text[$Col+27] = "|$INS_TITLE";
    $heading_text[$Col+28] = "|ประเทศที่สำเร็จการศึกษา";/*Release 5.1.0.4 Begin*/
    $heading_text[$Col+29] = "|ปีที่สำเร็จการศึกษา";/*Release 5.1.0.4 End*/
    $heading_text[$Col+30] = "|ประเภททุน ของวุฒิสูงสุด";/*Release 5.1.0.4*/    
    $heading_text[$Col+31] = "|ด้านความเชี่ยวชาญพิเศษ";/*Release 5.1.0.4*/
    $heading_text[$Col+32] = "|ด้านรอง";
    $heading_text[$Col+33] = "|ด้านย่อย";
    $heading_text[$Col+34] = "|เน้นทาง";/*Release 5.1.0.4*/    
    $heading_text[$Col+35] = "|ปีที่รับเครื่องราชฯ";/*Release 5.1.0.4*/
	$heading_text[$Col+36] = "|เครื่องราช ฯ";
if($search_per_type == 1){
    $heading_text[$Col+37] = "|สายงานระดับ";
	$heading_text[$Col+38] = "|ระดับ 1";
	$heading_text[$Col+39] = "|สำนัก/กอง";
    $heading_text[$Col+40] = "|สายงานระดับ";
	$heading_text[$Col+41] = "|ระดับ 2";
	$heading_text[$Col+42] = "|สำนัก/กอง";
    $heading_text[$Col+43] = "|สายงานระดับ";
	$heading_text[$Col+44] = "|ระดับ 3";
	$heading_text[$Col+45] = "|สำนัก/กอง";
    $heading_text[$Col+46] = "|สายงานระดับ";
	$heading_text[$Col+47] = "|ระดับ 4";
	$heading_text[$Col+48] = "|สำนัก/กอง";
    $heading_text[$Col+49] = "|สายงานระดับ";
	$heading_text[$Col+50] = "|ระดับ 5";
	$heading_text[$Col+51] = "|สำนัก/กอง";
    $heading_text[$Col+52] = "|สายงานระดับ";
	$heading_text[$Col+53] = "|ระดับ 6";
	$heading_text[$Col+54] = "|สำนัก/กอง";
    $heading_text[$Col+55] = "|สายงานระดับ";
	$heading_text[$Col+56] = "|ระดับ 7";
	$heading_text[$Col+57] = "|สำนัก/กอง";
    $heading_text[$Col+58] = "|สายงานระดับ";
	$heading_text[$Col+59] = "|ระดับ 8";
	$heading_text[$Col+60] = "|สำนัก/กอง";
    $heading_text[$Col+61] = "|สายงานระดับ";
	$heading_text[$Col+62] = "|ระดับ 9";
	$heading_text[$Col+63] = "|สำนัก/กอง";
    $heading_text[$Col+64] = "|สายงานระดับ";
	$heading_text[$Col+65] = "|ระดับ 10";
	$heading_text[$Col+66] = "|สำนัก/กอง";
    $heading_text[$Col+67] = "|สายงานระดับ";
	$heading_text[$Col+68] = "|ระดับ 11";
	$heading_text[$Col+69] = "|สำนัก/กอง";	
    $heading_text[$Col+70] = "|สายงานระดับ";
	$heading_text[$Col+71] = "|ปฏิบัติงาน";
	$heading_text[$Col+72] = "|สำนัก/กอง";
    $heading_text[$Col+73] = "|สายงานระดับ";
	$heading_text[$Col+74] = "|ชำนาญงาน";
	$heading_text[$Col+75] = "|สำนัก/กอง";
    $heading_text[$Col+76] = "|สายงานระดับ";
	$heading_text[$Col+77] = "|อาวุโส";
	$heading_text[$Col+78] = "|สำนัก/กอง";
    $heading_text[$Col+79] = "|สายงานระดับ";
	$heading_text[$Col+80] = "|ทักษะพิเศษ";
	$heading_text[$Col+81] = "|สำนัก/กอง";	
    $heading_text[$Col+82] = "|สายงานระดับ";
	$heading_text[$Col+83] = "|ปฏิบัติการ";
	$heading_text[$Col+84] = "|สำนัก/กอง";	
    $heading_text[$Col+85] = "|สายงานระดับ";
	$heading_text[$Col+86] = "|ชำนาญการ"; 
	$heading_text[$Col+87] = "|สำนัก/กอง";	
    $heading_text[$Col+88] = "|สายงานระดับ";
	$heading_text[$Col+89] = "|ชำนาญการพิเศษ";
	$heading_text[$Col+90] = "|สำนัก/กอง";	
    $heading_text[$Col+91] = "|สายงานระดับ";
	$heading_text[$Col+92] = "|เชี่ยวชาญ";
	$heading_text[$Col+93] = "|สำนัก/กอง";
    $heading_text[$Col+94] = "|สายงานระดับ";
	$heading_text[$Col+95] = "|ทรงคุณวุฒิ";  
	$heading_text[$Col+96] = "|สำนัก/กอง";	
    $heading_text[$Col+97] = "|สายงานระดับ";
	$heading_text[$Col+98] = "|อำนวยการต้น";
	$heading_text[$Col+99] = "|สำนัก/กอง";	
    $heading_text[$Col+100] = "|สายงานระดับ";
	$heading_text[$Col+101] = "|อำนวยการสูง";
	$heading_text[$Col+102] = "|สำนัก/กอง";    
    $heading_text[$Col+103] = "|สายงานระดับ";
    $heading_text[$Col+104] = "|บริหารต้น"; 
	$heading_text[$Col+105] = "|สำนัก/กอง";	
    $heading_text[$Col+106] = "|สายงานระดับ";
	$heading_text[$Col+107] = "|บริหารสูง"; 
	$heading_text[$Col+108] = "|สำนัก/กอง";	
	$heading_text[$Col+109] = "วันที่เข้าสู่|ระดับปัจจุบัน";
	$heading_text[$Col+110] = "ฐานในการ|คำนวณ";
	$heading_text[$Col+111] = "$ORG_TITLE|มอบหมายงาน";
	$heading_text[$Col+112] = "|เลขที่แฟ้ม";   
    $heading_text[$Col+113] = "|เบอร์โทรศัพท์มือถือ";/*Release 5.1.0.4*/
    $heading_text[$Col+114] = "|อีเมล์";/*Release 5.1.0.4*/
    $heading_text[$Col+115] = "|ความเชี่ยวชาญด้านหลัก-1";/*Release 5.2.1.7*/
    $heading_text[$Col+116] = "|ความเชี่ยวชาญด้านรอง-1";/*Release 5.2.1.7*/
    $heading_text[$Col+117] = "|รายละเอียด/คำอธิบาย-1";/*Release 5.2.1.7*/
    $heading_text[$Col+118] = "|ระดับความสามารถ-1";/*Release 5.2.1.7*/
    $heading_text[$Col+119] = "|ประเภทความเชี่ยวชาญ-1";/*Release 5.2.1.7*/  
    $heading_text[$Col+120] = "|ความเชี่ยวชาญด้านหลัก-2";/*Release 5.2.1.7*/
    $heading_text[$Col+121] = "|ความเชี่ยวชาญด้านรอง-2";/*Release 5.2.1.7*/
    $heading_text[$Col+122] = "|รายละเอียด/คำอธิบาย-2";/*Release 5.2.1.7*/
    $heading_text[$Col+123] = "|ระดับความสามารถ-2";/*Release 5.2.1.7*/
    $heading_text[$Col+124] = "|ประเภทความเชี่ยวชาญ-2";/*Release 5.2.1.7*/
}else{
    $heading_text[$Col+37]  = "วันที่เข้าสู่|ระดับปัจจุบัน"; 
    $heading_text[$Col+38]  = "ฐานในการ|คำนวณ";
    $heading_text[$Col+39]  = "$ORG_TITLE|มอบหมายงาน";
    $heading_text[$Col+40]  = "|เลขที่แฟ้ม";   
    $heading_text[$Col+41]  = "|เบอร์โทรศัพท์มือถือ";/*Release 5.1.0.4*/
    $heading_text[$Col+42]  = "|อีเมล์";/*Release 5.1.0.4*/
    $heading_text[$Col+43]  = "|เลขที่ประจำตัวข้าราชการ";/*Release 5.1.0.4*/ 
    $heading_text[$Col+44]  = "|ความเชี่ยวชาญด้านหลัก-1";/*Release 5.2.1.7*/
    $heading_text[$Col+45]  = "|ความเชี่ยวชาญด้านรอง-1";/*Release 5.2.1.7*/
    $heading_text[$Col+46]  = "|รายละเอียด/คำอธิบาย-1";/*Release 5.2.1.7*/
    $heading_text[$Col+47]  = "|ระดับความสามารถ-1";/*Release 5.2.1.7*/
    $heading_text[$Col+48]  = "|ประเภทความเชี่ยวชาญ-1";/*Release 5.2.1.7*/  
    $heading_text[$Col+49]  = "|ความเชี่ยวชาญด้านหลัก-2";/*Release 5.2.1.7*/
    $heading_text[$Col+50]  = "|ความเชี่ยวชาญด้านรอง-2";/*Release 5.2.1.7*/
    $heading_text[$Col+51]  = "|รายละเอียด/คำอธิบาย-2";/*Release 5.2.1.7*/
    $heading_text[$Col+52]  = "|ระดับความสามารถ-2";/*Release 5.2.1.7*/
    $heading_text[$Col+53]  = "|ประเภทความเชี่ยวชาญ-2";/*Release 5.2.1.7*/
}
	if ($MFA_FLAG == 1) { 
		$mfa_col = $Col+64;
        $heading_text[$mfa_col+1] = "|$MOV_TITLE";
		$heading_text[$mfa_col+2] = "|เลขที่คำสั่ง";
		$heading_text[$mfa_col+3] = "|$POS_NO_TITLE";
		$heading_text[$mfa_col+4] = "|$PL_TITLE";
		$heading_text[$mfa_col+5] = "|$PM_TITLE";
		$heading_text[$mfa_col+6] = "|$LEVEL_TITLE";
		$heading_text[$mfa_col+7] = "|$DEPARTMENT_TITLE";
		$heading_text[$mfa_col+8] = "|$ORG_TITLE";
		$heading_text[$mfa_col+9] = "$ORG_TITLE|มอบหมายงาน";
		$heading_text[$mfa_col+10] = "|$REMARK_TITLE";
	}

	// function ประเภท aggregate มี  SUM, AVG, PERC ตามแนวบรรทัด (ROW)
	// 									SUM-columnindex1-columnindex2-....  Ex SUM-1-3-4-7 หมายถึง ผลรวมของ column ที่ 1,3,4 และ 7
	// 									AVG-columnindex1-columnindex2-....  Ex AVG-1-3-4-7 หมายถึง ค่าเฉลี่ยของ column ที่ 1,3,4 และ 7
	// 									PERCn-columnindex1-columnindex2-....  Ex PERCn-1-3-4-7 n คือ column ที่ เป็นเป้าหมายเปรียบเทียบร้อยละ (ค่า 1 ค่า ใน column 1 3 4 7) 
	//																												ถ้าไม่ใส่ค่า n ก็คือ ค่าสรุป (100%) PERC3-1-3-4-7 ก็คือ ค่าใน column ที่ 3 เป็นร้อยละเท่าใดของผลรวม column 1,3,4,7
	//	function ประเภท รูปแบบ (format) ซึ่งจะต้องตามหลัง function ประเภท aggregate เสมอ (ถ้ามี)
	//									TNUM-xn คือ เปลี่ยนเป็นตัวเลขของไทย  x คือ ไม่มี comma ถ้าไม่มี x แสดงว่ามี comma ทุกหลัก 1,000  n คือ จำนวนทศนิยม ถ้ามีค่าเป็น ศูนย์ จะแสดงเป็น -
	//									TNUM0-xn คือ เปลี่ยนเป็นตัวเลขของไทย   x คือ ไม่มี comma ถ้าไม่มี x แสดงว่ามี comma ทุกหลัก 1,000  n คือ จำนวนทศนิยม ถ้ามีค่าเป็น ศูนย์ จะแสดงเป็น 0
	//									ENUM-xn คือ แสดงเป็นเลขอาราบิค  x คือ ไม่มี comma ถ้าไม่มี x แสดงว่ามี comma ทุกหลัก 1,000  n คือ จำนวนทศนิยม ถ้ามีค่าเป็น ศูนย์ จะแสดงเป็น -
	//									ENUM0-xn คือ แสดงเป็นเลขอาราบิค  x คือ ไม่มี comma ถ้าไม่มี x แสดงว่ามี comma ทุกหลัก 1,000  n คือ จำนวนทศนิยม ถ้ามีค่าเป็น ศูนย์ จะแสดงเป็น 0
	//
	$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");/*เพิ่ม*/
    $column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");/*เพิ่ม*/
    $column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");/*เพิ่ม*/
    $column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");/*เพิ่ม*/
    $column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");/*เพิ่ม*/
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
	$column_function[$Col+72] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");	// if ($BKK_FLAG!=1) column มี 56 ถ้าไม่แล้ว จะมี 55
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
	
	if (!$COLUMN_FORMAT) {	// ต้องกำหนดเป็น element ให้อยู่ใน form1 ด้วย  <input type="hidden" name="COLUMN_FORMAT" value="<?=$COLUMN_FORMAT
		$arr_column_map = (array) null;
		$arr_column_sel = (array) null;
              
		for($i=0; $i < count($heading_text); $i++) {
			$arr_column_map[] = $i;		// link index ของ head 
			$arr_column_sel[] = 1;			// 1=แสดง	0=ไม่แสดง   ถ้าไม่มีการปรับแต่งรายงาน ก็เป็นแสดงหมด
                       //echo $i.">".$heading_text[$i]."<br>"; 
		}
                //die();
		$arr_column_width = $heading_width;	// ความกว้าง
		$arr_column_align = $data_align;		// align
		$COLUMN_FORMAT = implode(",",$arr_column_map)."|".implode(",",$arr_column_sel)."|".implode(",",$arr_column_width)."|".implode(",",$arr_column_align);
	} else {
            
		$arrbuff = explode("|",$COLUMN_FORMAT);
		$arr_column_map = explode(",",$arrbuff[0]);		// index ของ head เริ่มต้น
		$arr_column_sel = explode(",",$arrbuff[1]);	// 1=แสดง	0=ไม่แสดง
		$arr_column_width = explode(",",$arrbuff[2]);	// ความกว้าง
		$heading_width = $arr_column_width;	// ความกว้าง
		$arr_column_align = explode(",",$arrbuff[3]);		// align
	}
	
	$total_show_width = 0;
	for($iii=0; $iii < count($arr_column_width); $iii++) 	if ($arr_column_sel[$iii]==1) $total_show_width += (int)$arr_column_width[$iii];

?>