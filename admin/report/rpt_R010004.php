<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$search_condition = "";
//	$arr_search_condition[] = "(l.DC_TYPE in (1, 2))";
	$arr_search_condition[] = "(a.PER_TYPE = 1)";
	$arr_search_condition[] = "(a.PER_STATUS = 1)";
	$arr_search_condition[] = "(a.LEVEL_NO = 'M1')";  //----------- เปลี่ยนจาก 10
	$arr_search_condition[] = "(b.PT_CODE = '32')";

	$list_type_text = $ALL_REPORT_TITLE;

	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
	}elseif($MINISTRY_ID){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
		if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		$list_type_text .= " - $MINISTRY_NAME";
	}elseif($PROVINCE_CODE){
		$PROVINCE_CODE = trim($PROVINCE_CODE);
		$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "รายงานนักบริหารระดับ 10";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);

	$report_code = "R1004";
	$orientation='L';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$heading_width[0] = "80";
	$heading_width[1] = "75";
	$heading_width[2] = "25";
	$heading_width[3] = "35";
	$heading_width[4] = "20";
	$heading_width[5] = "25";
	$heading_width[6] = "25";
	
	$heading_text[0] = "$MINISTRY_TITLE / $DEPARTMENT_TITLE |  ชื่อ - สกุล";
	$heading_text[1] = "ตำแหน่ง";
	$heading_text[2] = "วันเลื่อนระดับ";
	$heading_text[3] = "วันดำรงตำแหน่ง|ปัจจุบัน";
	$heading_text[4] = "เงินเดือน";
	$heading_text[5] = "วันบรรจุ";
	$heading_text[6] = "ปีเกษียณอายุ|(1 ต.ค. ของปี)";
	$heading_align = array('C','C','C','C','C','C','C');

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, 
								b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_NAME, a.PER_BIRTHDATE, a.PER_STARTDATE, a.PER_RETIREDATE, 
								a.PER_SALARY, b.ORG_ID, c.ORG_NAME, a.DEPARTMENT_ID as DEPARTMENT_ID, d.ORG_NAME as DEPARTMENT_NAME, 
								d.ORG_ID_REF as MINISTRY_ID, e.ORG_NAME as MINISTRY_NAME
				   from			(
									(
										(
											(
												(
													(
														(
															(
																(
																	(
																		PER_PERSONAL a
																		inner join PER_POSITION b on (a.POS_ID=b.POS_ID)
																	) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) inner join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
															) inner join PER_ORG e on (d.ORG_ID_REF=e.ORG_ID)
														) inner join PER_LINE f on (b.PL_CODE=f.PL_CODE)
													) left join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
												) left join PER_MGT h on (b.PM_CODE=h.PM_CODE)
											) left join PER_PRENAME i on (a.PN_CODE=i.PN_CODE)
										) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
									) left join  PER_DECORATEHIS k on (a.PER_ID=k.PER_ID)
								) left join PER_DECORATION l on (k.DC_CODE=l.DC_CODE)
				   $search_condition
				   group by		a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, 
								b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_NAME, LEFT(trim(a.PER_BIRTHDATE), 10), 
								LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_RETIREDATE), 10), 
								a.PER_SALARY, b.ORG_ID, c.ORG_NAME, a.DEPARTMENT_ID, d.ORG_NAME, d.ORG_ID_REF, e.ORG_NAME
				   order by		d.ORG_ID_REF, a.DEPARTMENT_ID, a.LEVEL_NO desc, MIN(LEFT(trim(j.POH_EFFECTIVEDATE), 10)), a.PER_SALARY desc, 
				   				LEFT(trim(a.PER_STARTDATE), 10), MIN(l.DC_ORDER), LEFT(trim(a.PER_BIRTHDATE), 10) ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace("where", "and", $search_condition);
		$cmd = " select			a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, 
								b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_NAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, 
								SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, SUBSTR(trim(a.PER_RETIREDATE), 1, 10) as PER_RETIREDATE, 
								a.PER_SALARY, b.ORG_ID, c.ORG_NAME, a.DEPARTMENT_ID as DEPARTMENT_ID, d.ORG_NAME as DEPARTMENT_NAME, 
								d.ORG_ID_REF as MINISTRY_ID, e.ORG_NAME as MINISTRY_NAME											
				   from		PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_ORG d, PER_ORG e, PER_LINE f, PER_TYPE g, PER_MGT h, 
				   				PER_PRENAME i, PER_POSITIONHIS j, PER_DECORATEHIS k, PER_DECORATION l
				   where	a.POS_ID=b.POS_ID and b.ORG_ID=c.ORG_ID and a.DEPARTMENT_ID=d.ORG_ID and d.ORG_ID_REF=e.ORG_ID 
								and b.PL_CODE=f.PL_CODE and b.PT_CODE=g.PT_CODE(+) and b.PM_CODE=h.PM_CODE(+) and a.PN_CODE=i.PN_CODE(+)
								and a.PER_ID=j.PER_ID(+) and a.PER_ID=k.PER_ID(+) and k.DC_CODE=l.DC_CODE(+)
								$search_condition
				   group by	a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, 
								b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_NAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), 
								SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_RETIREDATE), 1, 10), 
								a.PER_SALARY, b.ORG_ID, c.ORG_NAME, a.DEPARTMENT_ID, d.ORG_NAME, d.ORG_ID_REF, e.ORG_NAME
				   order by		d.ORG_ID_REF, a.DEPARTMENT_ID, a.LEVEL_NO desc, MIN(SUBSTR(trim(j.POH_EFFECTIVEDATE), 1, 10)), a.PER_SALARY desc, 
				   				SUBSTR(trim(a.PER_STARTDATE), 1, 10), MIN(l.DC_ORDER), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) ";
	}elseif($DPISDB=="mysql") {
		$cmd = " select			a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, 
								b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_NAME, a.PER_BIRTHDATE, a.PER_STARTDATE, a.PER_RETIREDATE, 
								a.PER_SALARY, b.ORG_ID, c.ORG_NAME, a.DEPARTMENT_ID as DEPARTMENT_ID, d.ORG_NAME as DEPARTMENT_NAME, 
								d.ORG_ID_REF as MINISTRY_ID, e.ORG_NAME as MINISTRY_NAME
					   from		(
										(
											(
												(
													(
														(
															(
																(
																	(
																		(
																			PER_PERSONAL a
																			inner join PER_POSITION b on (a.POS_ID=b.POS_ID)
																		) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																	) inner join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
																) inner join PER_ORG e on (d.ORG_ID_REF=e.ORG_ID)
															) inner join PER_LINE f on (b.PL_CODE=f.PL_CODE)
														) left join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
													) left join PER_MGT h on (b.PM_CODE=h.PM_CODE)
												) left join PER_PRENAME i on (a.PN_CODE=i.PN_CODE)
											) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
										) left join  PER_DECORATEHIS k on (a.PER_ID=k.PER_ID)
									) left join PER_DECORATION l on (k.DC_CODE=l.DC_CODE)
					   $search_condition
					   group by		a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, 
									b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_NAME, LEFT(trim(a.PER_BIRTHDATE), 10), 
									LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_RETIREDATE), 10), 
									a.PER_SALARY, b.ORG_ID, c.ORG_NAME, a.DEPARTMENT_ID, d.ORG_NAME, d.ORG_ID_REF, e.ORG_NAME
					   order by		d.ORG_ID_REF, a.DEPARTMENT_ID, a.LEVEL_NO desc, MIN(LEFT(trim(j.POH_EFFECTIVEDATE), 10)), a.PER_SALARY desc, 
									LEFT(trim(a.PER_STARTDATE), 10), MIN(l.DC_ORDER), LEFT(trim(a.PER_BIRTHDATE), 10) ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
	//$db_dpis->show_error();
	//echo $cmd;
	$data_count = 0;
	$data_row = 0;
	$MINISTRY_ID = -1;
	$DEPARTMENT_ID = -1;
	while($data = $db_dpis->get_array()){		
		if($MINISTRY_ID != $data[MINISTRY_ID]){
			$MINISTRY_ID = $data[MINISTRY_ID];
			$MINISTRY_NAME = $data[MINISTRY_NAME];
			
			$arr_content[$data_count][type] = "MINISTRY";
			$arr_content[$data_count][name] = $MINISTRY_NAME;

			$data_row = 0;
			$data_count++;
		} // end if
		
		if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){
			$DEPARTMENT_ID = $data[DEPARTMENT_ID];
			$DEPARTMENT_NAME = $data[DEPARTMENT_NAME];
			
			$arr_content[$data_count][type] = "DEPARTMENT";
			$arr_content[$data_count][name] = $DEPARTMENT_NAME;

			$data_row = 0;
			$data_count++;
		} // end if

		$data_row++;

		$PER_ID = $data[PER_ID];
		$PN_NAME = $data[PN_NAME];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$LEVEL_NO = trim($data[LEVEL_NO]);
		
		$cmd = " select LEVEL_NAME from PER_LEVEL where  trim(LEVEL_NO)='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		
		$LEVEL_NAME = trim($data2[LEVEL_NAME]);
		
		$PL_CODE = trim($data[PL_CODE]);
		$PL_NAME = $data[PL_NAME];
		$PM_CODE = trim($data[PM_CODE]);
		$PM_NAME = $data[PM_NAME];

		$PT_CODE = trim($data[PT_CODE]);
		$PT_NAME = $data[PT_NAME];
		
		$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
		$arr_temp = explode("-", $PER_BIRTHDATE);
		$PER_RETIRE_YEAR = ($arr_temp[0] + 543) + (($arr_temp[1] >= 10)?61:60);
		$PER_BIRTHDATE = show_date_format($PER_BIRTHDATE,$DATE_DISPLAY);

		$PER_STARTDATE = show_date_format($data[PER_STARTDATE], $DATE_DISPLAY);
		$PER_SALARY = $data[PER_SALARY];
		
		$cmd = " select MIN(POH_EFFECTIVEDATE) as EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and trim(LEVEL_NO)='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$LEVEL_EFFECTIVEDATE = show_date_format($data2[EFFECTIVEDATE],$DATE_DISPLAY);

		$cmd = " select MIN(POH_EFFECTIVEDATE) as EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and trim(PL_CODE)='$PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POSITION_EFFECTIVEDATE = show_date_format($data2[EFFECTIVEDATE],$DATE_DISPLAY);

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][name] = "$data_row." . str_repeat(" ", 5) . $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][position] = (trim($PM_NAME)?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME ." ". $LEVEL_NAME . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):$LEVEL_NAME) . (trim($PM_NAME)?")":"");
		$arr_content[$data_count][level_effectivedate] = $LEVEL_EFFECTIVEDATE;
		$arr_content[$data_count][position_effectivedate] = $POSITION_EFFECTIVEDATE;
		$arr_content[$data_count][salary] = number_format($PER_SALARY);
		$arr_content[$data_count][startdate] = $PER_STARTDATE;
		$arr_content[$data_count][retireyear] = $PER_RETIRE_YEAR;
				
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";

if($count_data){
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
		if (!$result) echo "****** error ****** on open table for $table<br>";
		$pdf->AutoPageBreak = false; 
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$POSITION = $arr_content[$data_count][position];
			$LEVEL_EFFECTIVEDATE = $arr_content[$data_count][level_effectivedate];
			$POSITION_EFFECTIVEDATE = $arr_content[$data_count][position_effectivedate];
			$SALARY = $arr_content[$data_count][salary];
			$STARTDATE = $arr_content[$data_count][startdate];
			$RETIREYEAR = $arr_content[$data_count][retireyear];

				if($REPORT_ORDER == "MINISTRY"){
				$ORG_NAME = $arr_content[$data_count][name];
            	$arr_data = (array) null;
				$arr_data[] ="$NAME";
				$arr_data[] =  "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] ="";
            	$arr_data[] ="";
				$arr_data[] ="";
		    
				$data_align = array("L", "L", "L", "C", "C", "C", "C");
				
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				
		}elseif($REPORT_ORDER == "DEPARTMENT"){
		$ORG_NAME = $arr_content[$data_count][name];
         	$arr_data = (array) null;
				$arr_data[] ="$NAME";
				$arr_data[] =  "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] ="";
            	$arr_data[] ="";
                $arr_data[] ="";		    	
				
				$data_align = array("L", "L", "L", "C", "C", "C", "C");
				
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			
				}elseif($REPORT_ORDER == "CONTENT"){
	         	$arr_data = (array) null;
				$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($NAME):$NAME);
				$arr_data[] =  $POSITION;
				$arr_data[] =($LEVEL_EFFECTIVEDATE?(($NUMBER_DISPLAY==2)?convert2thaidigit($LEVEL_EFFECTIVEDATE):$LEVEL_EFFECTIVEDATE):"-");       
				$arr_data[] =($POSITION_EFFECTIVEDATE?(($NUMBER_DISPLAY==2)?convert2thaidigit($POSITION_EFFECTIVEDATE):$POSITION_EFFECTIVEDATE):"-");       
				$arr_data[] =($SALARY?(($NUMBER_DISPLAY==2)?convert2thaidigit($SALARY):$SALARY):"-");       
            	$arr_data[] =($STARTDATE?(($NUMBER_DISPLAY==2)?convert2thaidigit($STARTDATE):$STARTDATE):"-");       
			    $arr_data[] =($RETIREYEAR?(($NUMBER_DISPLAY==2)?convert2thaidigit($RETIREYEAR):$RETIREYEAR):"-");       
		  
				$data_align = array("L", "L", "C", "C", "C", "C", "C", "L");
				
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end if	
		} // end for
					$pdf->add_data_tab("", 0, "RHBL", $data_align, "", "12", "b", "000000", "");
	}else{
		$pdf->SetFont($font,'b',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();			

?>