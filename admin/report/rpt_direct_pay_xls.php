<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	header ("Content-type: application/vnd.ms-excel");
	header ("Content-Disposition: inline; filename=". trim($report_title) .".xls");

	// ==== use for testing phase =====
//	$DPISDB = "oci8";
//	$db_dpis = $db;
	// ==========================

	ini_set("max_execution_time", $max_execution_time);
	
	$lang_code="TH";
	$company_name = "";
	$report_title = trim($report_title);
	$report_code = "";

	$project_font = "<font face=\"Microsoft Sans Serif, Courier New, Microsoft Sans Serif\" size=\"2\" color=\"#333366\">";
	$title_font = "<font face=\"Microsoft Sans Serif, Courier New, Microsoft Sans Serif\" size=\"2\" color=\"#993300\">";
	$header_font = "<font face=\"Microsoft Sans Serif, Courier New, Microsoft Sans Serif\" size=\"2\" color=\"#0066CC\">";
	$font = "<font face=\"Microsoft Sans Serif, Courier New, Microsoft Sans Serif\" size=\"2\">";

	$content .= "<tr height=\"25\" align=\"center\" nowrap><td>$project_font <b>$report_title</b></font></td></tr>";

	function print_header(){
		global $content, $header_font;
		
		$content .= "<tr>";
		$content .= "<td align=\"center\" nowrap>$header_font <b>คำนำหน้าชื่อ</b></font></td>";
		$content .= "<td align=\"center\" nowrap>$header_font <b>ชื่อ</b></font></td>";
		$content .= "<td align=\"center\" nowrap>$header_font <b>นามสกุล</b></font></td>";
		$content .= "<td align=\"center\" nowrap>$header_font <b>เลขประจำตัวประชาชน</b></font></td>";
		$content .= "<td align=\"center\" nowrap>$header_font <b>วันเดือนปีเกิด</b></font></td>";
		$content .= "</tr>";
	} // function		
	
	$cmd = " select per_id,pn_name,per_name,per_surname,per_cardno,per_birthdate,pn_code_f,per_fathername,per_fathersurname,pn_code_m,per_mothername,per_mothersurname 
					from per_personal a, per_prename b
					where a.pn_code = b.pn_code(+) ";
	$count_data = $db_dpis->send_cmd($cmd);
	$db_dpis->show_error();

	if($count_data){
		$content .= "<tr><td><table border=\"1\" width=\"100%\">";
		print_header();
		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;			
			$data_row++;			

			$content .= "<tr>";
			$content .= "<td align=\"left\" nowrap>$font ".$data[PN_NAME]."&nbsp;</font></td>";
			$content .= "<td align=\"left\" nowrap>$font ".$data[PER_NAME]."&nbsp;</font></td>";
			$content .= "<td align=\"left\" nowrap>$font ".$data[PER_SURNAME]."&nbsp;</font></td>";
			$content .= "<td align=\"center\" nowrap>$font ".card_no_format($data[PER_CARDNO],$CARD_NO_DISPLAY)."&nbsp;</font></td>";
			$content .= "<td align=\"center\" nowrap>$font ".show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY)."&nbsp;</font></td>";
			$content .= "</tr>";
			if ($data[PER_FATHERNAME] > " ") {
   			   $content .= "<tr>";
			   $content .= "<td align=\"left\" nowrap>$font ".$data[PN_NAME]."&nbsp;</font></td>";
			   $content .= "<td align=\"left\" nowrap>$font ".$data[PER_FATHERNAME]."&nbsp;</font></td>";
			   $content .= "<td align=\"left\" nowrap>$font ".$data[PER_FATHERSURNAME]."&nbsp;</font></td>";
			   $content .= "<td align=\"center\" nowrap>$font "." "."&nbsp;</font></td>";
			   $content .= "<td align=\"center\" nowrap>$font "." "."&nbsp;</font></td>";
			   $content .= "</tr>";
			}
			if ($data[PER_MOTHERNAME] > " ") {
			   $content .= "<tr>";
			   $content .= "<td align=\"left\" nowrap>$font ".$data[PN_NAME]."&nbsp;</font></td>";
			   $content .= "<td align=\"left\" nowrap>$font ".$data[PER_MOTHERNAME]."&nbsp;</font></td>";
			   $content .= "<td align=\"left\" nowrap>$font ".$data[PER_MOTHERSURNAME]."&nbsp;</font></td>";
			   $content .= "<td align=\"center\" nowrap>$font "." "."&nbsp;</font></td>";
			   $content .= "<td align=\"center\" nowrap>$font "." "."&nbsp;</font></td>";
			   $content .= "</tr>";
			}
		} // end while
		$content .= "</table></td></tr>";
	}else{
		$content .= "<tr height=\"25\"><td>$font ********** ไม่มีข้อมูล **********</font></td></tr>";
	} // end if

	ini_set("max_execution_time", 30);

	echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-874\"></head><body><table width=\"100%\">$content</table></body></html>";
?>