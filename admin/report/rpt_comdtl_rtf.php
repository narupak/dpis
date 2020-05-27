<?
//	echo "font=$font<br>";
	include("../../php_scripts/connect_database.php");
	include ("../php_scripts/session_start.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	// read template content
	require("../../RTF/rtf_class.php");

//	date_default_timezone_set('Asia/Bangkok');

	ini_set("max_execution_time", $max_execution_time);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if ($form_part=="3_1_1" || $form_part=="3_3_1") {
		$cmd = "	select	CMD_ORG2, ORG_NAME
							from		PER_COMDTL a, PER_POSITION b, PER_ORG c
							where	a.POS_ID = b.POS_ID  and b.DEPARTMENT_ID = c.ORG_ID and COM_ID = $COM_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CMD_ORG2 = trim($data[CMD_ORG2]);
		$ORG_NAME = trim($data[ORG_NAME]);
	}

	if ($form_part=="3_5") {
		$cmd = " select CMD_ORG2 from PER_COMDTL where	COM_ID = $COM_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CMD_ORG2 = trim($data[CMD_ORG2]);
		if($DPISDB=="mysql"){
			$arr_temp = explode("|", $data[CMD_ORG2]);
		}else{
			$arr_temp = explode("\|", $data[CMD_ORG2]);
		}
		$ORG_NAME = $arr_temp[1];
		if (!$ORG_NAME) $ORG_NAME = $arr_temp[0];
	}

	$prefix = "";
	if ($SESS_DEPARTMENT_NAME=="กรมชลประทาน") {
		$FULLNAME = "นายเลิศวิโรจน์  โกวัฒนะ";
		$PM_NAME = "อธิบดีกรมชลประทาน";
		$prefix = "_rid";
	} elseif ($DOH_FLAG==1) {
		$FULLNAME = "นายชูศักดิ์ เกวี";
		$PM_NAME = "อธิบดีกรมทางหลวง";
		$prefix = "_rid";
	} elseif ($BKK_FLAG==1) {
		$FULLNAME = "นายพรเทพ  อัครวรกุลชัย";
		$PM_NAME = "หัวหน้าสำนักงาน ก.ก.";
		$prefix = "_bkk";
	} elseif ($MFA_FLAG==1) {
		$FULLNAME = "นายสีหศักดิ์ พวงเกตุแก้ว";
		$PM_NAME = "ปลัดกระทรวงการต่างประเทศ";
		$prefix = "_mfa";
		if (!$DEPARTMENT_NAME) $DEPARTMENT_NAME = "กระทรวงการต่างประเทศ";
	}
//	echo "$font";
	if (!$font) $font = "AngsanaUPC";
	
    if ($form_part) {
        if (substr($form_part,2,1)=='_')
            $form_part_name = substr($form_part,0,4);
        elseif (substr($form_part,1,1)=='_')
            $form_part_name = substr($form_part,0,3);
		else
			$form_part_name = $form_part;
		//    	echo "1..include file==>"."../prt_forms/r_".$form_part_name."_rtfform.php  (".file_exists("../prt_forms/r_".$form_part_name."_rtfform.php").")<br>";
        include("../prt_forms/r_".$form_part_name."_rtfform".$prefix.".php");	// กำหนดค่า $key_index, $active_index, $seq_order_index

		unset($arr_fields);
		unset($arr_fldtyp);
		unset($arr_fldlen);
	
		$arr_syntab = (array) null;
		for($k = 0; $k < count($arr_table); $k++) {
			$arr_syntab[] = chr(97+$k);	// เริ่มที่ a.....
			if (trim($arr_table[$k])) {
				$cmd = " select * from ".trim($arr_table[$k]);
				$db_dpis->send_cmd($cmd);
				$field_list = $db_dpis->list_fields(trim($arr_table[$k]));
	//			echo "$cmd -- field_list=".implode(",",implode("|",$field_list))."<br>";
	//			foreach($field_list as $key => $value) {
	//				echo "$key>".$value[name].">";
	//				foreach($value as $key1 => $value1) {
	//					echo "$key1($value1),";
	//				}
	//				echo " ... ";
	//			}
	//			echo "||<br>";
	
				if($DPISDB=="odbc" || $DPISDB=="oci8"){
					for($i=1; $i<=count($field_list); $i++) :
						$arr_fields[$k][] = $field_list[$i]["name"];
						$arr_fldtyp[$k][] = $field_list[$i]["type"];
						$arr_fldlen[$k][] = $field_list[$i]["len"];
					endfor;
				}elseif($DPISDB=="mysql"){
					for($i=0; $i<count($field_list); $i++) :
						$arr_fields[$k][] = $field_list[$i]["name"];
						$arr_fldtyp[$k][] = $field_list[$i]["type"];
						$arr_fldlen[$k][] = $field_list[$i]["len"];
					endfor;
				} // end if
	//			echo ">>".(implode(", ", $arr_fields))."||".(implode(", ", $arr_fldtyp))."<br>";
			} // end if (trim($arr_table[$k]))
		} // end for $k

//		for($link_i = 0; $link_i < count(tab_link); $link_i++) {
//			$a = explode_formula_a($tab_link[$link_i]);
//		}

		$fname= "r_".$form_part_name."_RTF.rtf";

		ini_set("max_execution_time", $max_execution_time);
	
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		$cmd = " select	COM_NO, COM_DATE from	PER_COMMAND where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COM_NO = trim($data[COM_NO]);
		$COM_DATE = $data[COM_DATE];
		$COM_DATE = show_date_format($COM_DATE,5);
	
		$cmd = " select	COUNT(CMD_SEQ) as QTY from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$QTY = trim($data[QTY]);

		$RTF = new RTF();
		if ($SESS_DEPARTMENT_NAME=="กรมชลประทาน") {
			$RTF = new RTF("a4", 1800, 1100, 600, 200);
		} elseif ($BKK_FLAG==1) {
			$RTF = new RTF("a4", 1200, 1100, 600, 200);
		} elseif ($MFA_FLAG==1) {
			$RTF = new RTF("a4", 1800, 1100, 600, 200);
		}
		$RTF->set_default_font($font, 16);

		// รอบแรก
		for($i=0; $i < count($form_print); $i++) {
			$arr_c = explode("^",$form_print[$i]);
			if (substr(strtolower($arr_c[0]),0,6)=="%line%") {
				$linenum = (int)substr($arr_c[0],6);
				if ($linenum) $RTF->new_line($linenum);
				else $RTF->new_line();
			} else {
				for($k=0; $k < count($arr_c); $k++) {
					$arr_t = explode("|",$arr_c[$k]);
					if ($arr_t[0]=="text") {
						$font_a = explode(",",$arr_t[2]);
						if (!$font_a[0]) $font_a[0] = $font;
						if (!$font_a[1]) $font_a[1] = 16;
						$RTF->set_font($font_a[0], $font_a[1]);
						$align = $arr_t[3];
						$tt = $arr_t[4];
						$c = strpos($tt,"@");
						while ($c !== false) {
							$c1 = strpos($tt,"@",$c+1);
							if ($c1 !== false) {
								$var  = substr($tt,$c,$c1-$c+1);
								$tt = str_replace($var, ${substr($var,1,strlen($var)-2)}, $tt);
								$c = strpos($tt,"@",$c1+1);
							} else $c = $c1;
						}
						$text = (($NUMBER_DISPLAY==2) ? convert2thaidigit($tt) : $tt);
						if (strpos(strtolower($arr_t[1]),"emboss")!==false) {
							$text =  $RTF->emboss(1).$text.$RTF->emboss(0);
						}
						if (strpos(strtolower($arr_t[1]),"sub")!==false) {
							$text =  $RTF->sub(1).$text.$RTF->sub(0);
						}
						if (strpos(strtolower($arr_t[1]),"super")!==false) {
							$text =  $RTF->super(1).$text.$RTF->super(0);
						}
						if (strpos(strtolower($arr_t[1]),"engrave")!==false) {
							$text =  $RTF->engrave(1).$text.$RTF->engrave(0);
						}
						if (strpos(strtolower($arr_t[1]),"caps")!==false) {
							$text =  $RTF->caps(1).$text.$RTF->caps(0);
						}
						if (strpos(strtolower($arr_t[1]),"outline")!==false) {
							$text =  $RTF->outline(1).$text.$RTF->outline(0);
						}
						if (strpos(strtolower($arr_t[1]),"shadow")!==false) {
							$text =  $RTF->shadow(1).$text.$RTF->shadow(0);
						}
						if (strpos(strtolower($arr_t[1]),"bold")!==false) {
							$text =  $RTF->bold(1).$text.$RTF->bold(0);
						}
						if (strpos(strtolower($arr_t[1]),"underline")!==false) {
							$text =  $RTF->underline(1).$text.$RTF->underline(0);
						}
						if (strpos(strtolower($arr_t[1]),"italic")!==false) {
							$text =  $RTF->italic(1).$text.$RTF->italic(0);
						}
						$RTF->add_text($text, $align);
					} else if ($arr_t[0]=="image") {
						$img_ratio = $arr_t[1];
						$align = $arr_t[2];
						$img_name = $arr_t[3];
						$RTF->add_image($img_name, $img_ratio, $align);
					}
				} // end line
				$RTF->paragraph();
			} // end if
		} // end $i
		
		// รอบสอง
		$RTF->new_page();
		
		if ($MFA_FLAG!=1) $form_print[1] = "text|bold|,20|center|สำเนาคู่ฉบับ";
		for($i=0; $i < count($form_print); $i++) {
			$arr_c = explode("^",$form_print[$i]);
			if (substr(strtolower($arr_c[0]),0,6)=="%line%") {
				$linenum = (int)substr($arr_c[0],6);
				if ($linenum) $RTF->new_line($linenum);
				else $RTF->new_line();
			} else {
				for($k=0; $k < count($arr_c); $k++) {
					$arr_t = explode("|",$arr_c[$k]);
					if ($arr_t[0]=="text") {
						$font_a = explode(",",$arr_t[2]);
						if (!$font_a[0]) $font_a[0] = $font;
						if (!$font_a[1]) $font_a[1] = 16;
						$RTF->set_font($font_a[0], $font_a[1]);
						$align = $arr_t[3];
						$tt = $arr_t[4];
						$c = strpos($tt,"@");
						while ($c !== false) {
							$c1 = strpos($tt,"@",$c+1);
							if ($c1 !== false) {
								$var  = substr($tt,$c,$c1-$c+1);
								$tt = str_replace($var, ${substr($var,1,strlen($var)-2)}, $tt);
								$c = strpos($tt,"@",$c1+1);
							} else $c = $c1;
						}
						$text = (($NUMBER_DISPLAY==2) ? convert2thaidigit($tt) : $tt);
						if (strpos(strtolower($arr_t[1]),"emboss")!==false) {
							$text =  $RTF->emboss(1).$text.$RTF->emboss(0);
						}
						if (strpos(strtolower($arr_t[1]),"sub")!==false) {
							$text =  $RTF->sub(1).$text.$RTF->sub(0);
						}
						if (strpos(strtolower($arr_t[1]),"super")!==false) {
							$text =  $RTF->super(1).$text.$RTF->super(0);
						}
						if (strpos(strtolower($arr_t[1]),"engrave")!==false) {
							$text =  $RTF->engrave(1).$text.$RTF->engrave(0);
						}
						if (strpos(strtolower($arr_t[1]),"caps")!==false) {
							$text =  $RTF->caps(1).$text.$RTF->caps(0);
						}
						if (strpos(strtolower($arr_t[1]),"outline")!==false) {
							$text =  $RTF->outline(1).$text.$RTF->outline(0);
						}
						if (strpos(strtolower($arr_t[1]),"shadow")!==false) {
							$text =  $RTF->shadow(1).$text.$RTF->shadow(0);
						}
						if (strpos(strtolower($arr_t[1]),"bold")!==false) {
							$text =  $RTF->bold(1).$text.$RTF->bold(0);
						}
						if (strpos(strtolower($arr_t[1]),"underline")!==false) {
							$text =  $RTF->underline(1).$text.$RTF->underline(0);
						}
						if (strpos(strtolower($arr_t[1]),"italic")!==false) {
							$text =  $RTF->italic(1).$text.$RTF->italic(0);
						}
						$RTF->add_text($text, $align);
					} else if ($arr_t[0]=="image") {
						$img_ratio = $arr_t[1];
						$align = $arr_t[2];
						$img_name = $arr_t[3];
						$RTF->add_image($img_name, $img_ratio, $align);
					}
				} // end line
				$RTF->paragraph();
				if ($MFA_FLAG!=1) if ($i<2) $RTF->ln(2);
			} // end if
		} // end $i	

		$RTF->display($fname);
	} else {
        $err_text = "****ไม่ได้ส่ง ชื่อ form ประกอบเข้ามา ไม่สามารถทำงานได้****";
	}
?>
