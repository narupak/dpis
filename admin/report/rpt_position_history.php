<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = trim($report_title);
	$report_code = "";
	$orientation='L';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	$pdf->SetAutoPageBreak(true,10);
	
	$heading_width[0] = "20";
	$heading_width[1] = "40";
	$heading_width[2] = "40";
	$heading_width[3] = "25";
	$heading_width[4] = "23";
	$heading_width[5] = "30";
	$heading_width[6] = "30";
	$heading_width[7] = "40";
	$heading_width[8] = "40";
	
		
	function print_header(){
		global $pdf, $heading_width, $DEPARTMENT_TITLE, $ORG_TITLE, $ORG_TITLE1, $ORG_TITLE2;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"วันที่",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ตำแหน่งใน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ตำแหน่งใน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ช่วงระดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ประเภทตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"$DEPARTMENT_TITLE",'LTR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"$ORG_TITLE",'LTR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"$ORG_TITLE1",'LTR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"$ORG_TITLE2",'LTR',1,'C',1);
		
		
		
		$pdf->Cell($heading_width[0] ,7,"เปลี่ยนแปลง",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"สายงาน",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"การบริหารงาน",'LBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ตำแหน่ง",'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"พรบ.2535",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"",'LBR',1,'C',1);
		
	} // function		
		
	$cmd = " select * from $table ";
	$db_dpis->send_cmd($cmd);
	$field_list = $db_dpis->list_fields($table);
//	echo "<pre>";		print_r($field_list);		echo "</pre>";
	unset($arr_fields);
	if($DPISDB=="odbc" || $DPISDB=="oci8"){
		for($i=1; $i<=count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	}elseif($DPISDB=="mysql"){
		for($i=0; $i<count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	} // end if

  	$search_condition = "";
	$arr_search_condition[] = "(a.POS_ID=$POS_ID)";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);
  	
	if($DPISDB=="odbc"){
		$cmd_his =" select 		a.POS_ID
						from 		(
											PER_POS_MOVE a
											inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
										)
						$search_condition ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd_his =" select 		a.POS_ID
						from 		PER_POS_MOVE a, PER_ORG b
						where 	a.ORG_ID=b.ORG_ID
										$search_condition ";
	}elseif($DPISDB=="mysql"){
		$cmd_his =" select 		a.POS_ID
						from 		(
											PER_POS_MOVE a
											inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
										)
						$search_condition ";
	} // end if
	if($select_org_structure==1) $cmd_his = str_replace("PER_ORG", "PER_ORG_ASS", $cmd_his);
	$count_data = $db_dpis->send_cmd($cmd_his);
	//if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);
		
	if($DPISDB=="odbc"){
	  	 $cmd_his =" select 		a.POS_ID, LEFT(trim(a.POS_DATE), 10) as POS_DATE, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
										a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS
						from 		(
											PER_POS_MOVE a
											inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
										)
										$search_condition
										
						order by a.POS_DATE desc ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition); 
		
		$cmd_his = "
							select 		a.POS_ID, SUBSTR(trim(a.POS_DATE), 1, 10) as POS_DATE, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS
							from 		PER_POS_MOVE a, PER_ORG b
							where 	a.ORG_ID=b.ORG_ID
											$search_condition
							order by a.POS_DATE desc ";
	}elseif($DPISDB=="mysql"){
	  	 $cmd_his =" select 		a.POS_ID, LEFT(trim(a.POS_DATE), 10) as POS_DATE, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
										a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS
						from 		(
											PER_POS_MOVE a
											inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
										)
										$search_condition
						order by a.POS_DATE desc ";
	} // end if
	if($select_org_structure==1) $cmd_his = str_replace("PER_ORG", "PER_ORG_ASS", $cmd_his);
	$count_data = $db_dpis->send_cmd($cmd_his);
//	$db_dpis->show_error();
//	echo $cmd_his; 
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();
		$data_count = $data_row = 0;
		
	//=========STR หา ตน.ปัจจุบัน =====================//

		$data1 = $db_dpis->get_array();
		$POS_ID_CUR = trim($data1[POS_ID]); //****
		if($DPISDB=="odbc"){
			if($SESS_ORG_STRUCTURE==1){		//มอบหมายงาน
				$cmd1 =" select 		a.POS_ID, a.DEPARTMENT_ID, iif(isnull(POS_NO),0,CLng(POS_NO)) as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
												a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
												c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
												f.PER_STATUS as PER_STATUS4, a.LEVEL_NO
								from  (
										 (
											(
												(
													(
														PER_POSITION a
														inner join PER_ORG_ASS b on (a.ORG_ID=b.ORG_ID)
													) left join PER_PERSONAL c on (a.POS_ID=c.POS_ID and c.PER_STATUS=1)
												) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and (d.PER_STATUS=0 or d.PER_STATUS=2))
											) left join PER_PERSONAL e on (a.POS_ID=e.PAY_ID and e.PER_STATUS=1)
										) left join PER_PERSONAL f on (a.POS_ID=f.PAY_ID and (f.PER_STATUS=0 or f.PER_STATUS=2))
										) left join PER_PERSONAL g on (a.POS_ID=g.POS_ID)
								where (a.POS_ID = $POS_ID_CUR)
								group by a.POS_ID, a.DEPARTMENT_ID, iif(isnull(POS_NO),0,CLng(POS_NO)), PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
												a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
												c.PER_STATUS, d.PER_STATUS, e.PER_STATUS, f.PER_STATUS, a.LEVEL_NO
								order by a.DEPARTMENT_ID, iif(isnull(POS_NO),0,CLng(POS_NO)) ";
			}else{//ตามกฎหมาย
				$cmd1 =" select 		a.POS_ID, a.DEPARTMENT_ID, iif(isnull(POS_NO),0,CLng(POS_NO)) as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
												a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
												c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
												f.PER_STATUS as PER_STATUS4, a.LEVEL_NO
								from (
											(
												(
													(
														PER_POSITION a
														inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) left join PER_PERSONAL c on (a.POS_ID=c.POS_ID and c.PER_STATUS=1)
												) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and (d.PER_STATUS=0 or d.PER_STATUS=2))
											) left join PER_PERSONAL e on (a.POS_ID=e.PAY_ID and e.PER_STATUS=1)
										) left join PER_PERSONAL f on (a.POS_ID=f.PAY_ID and (f.PER_STATUS=0 or f.PER_STATUS=2))
								where (a.POS_ID = $POS_ID_CUR)
								group by a.POS_ID, a.DEPARTMENT_ID, iif(isnull(POS_NO),0,CLng(POS_NO)), PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
												a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
												c.PER_STATUS, d.PER_STATUS, e.PER_STATUS, f.PER_STATUS, a.LEVEL_NO
								order by a.DEPARTMENT_ID, iif(isnull(POS_NO),0,CLng(POS_NO)) ";
			}
		}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition); 
				if($SESS_ORG_STRUCTURE==1){		//มอบหมายงาน   
					$cmd1 = "select 	a.POS_ID, a.DEPARTMENT_ID, to_number(replace(POS_NO,'-','')) as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
												g.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
												c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
												f.PER_STATUS as PER_STATUS4, a.LEVEL_NO
									from 	PER_POSITION a, PER_ORG_ASS b, PER_PERSONAL g,
											(select POS_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_STATUS=1) c, 
											(select POS_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_STATUS=0 or PER_STATUS=2) d, 
											(select PAY_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_STATUS=1) e, 
											(select PAY_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_STATUS=0 or PER_STATUS=2) f
									where 	a.ORG_ID=b.ORG_ID and a.POS_ID=c.POS_ID(+) and a.POS_ID=d.POS_ID(+) and a.POS_ID=e.PAY_ID(+) and a.POS_ID=f.PAY_ID(+)
													and a.POS_ID=g.POS_ID(+)
													and (a.POS_ID = $POS_ID_CUR)
									group by a.POS_ID, a.DEPARTMENT_ID, to_number(replace(POS_NO,'-','')), PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
												a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, c.PER_STATUS, 
												d.PER_STATUS, e.PER_STATUS, f.PER_STATUS, a.LEVEL_NO, g.ORG_ID
									order by a.DEPARTMENT_ID, to_number(replace(POS_NO,'-','')) ";
			}else{	//ตามกฎหมาย 
				$cmd1 = "select 	a.POS_ID, a.DEPARTMENT_ID, to_number(replace(POS_NO,'-','')) as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
											c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
											f.PER_STATUS as PER_STATUS4, a.LEVEL_NO
								from 	PER_POSITION a, PER_ORG b, 
										(select POS_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_STATUS=1) c, 
										(select POS_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_STATUS=0 or PER_STATUS=2) d, 
										(select PAY_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_STATUS=1) e, 
										(select PAY_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_STATUS=0 or PER_STATUS=2) f
								where 	a.ORG_ID=b.ORG_ID and a.POS_ID=c.POS_ID(+) and a.POS_ID=d.POS_ID(+) and a.POS_ID=e.PAY_ID(+) and a.POS_ID=f.PAY_ID(+)
											and (a.POS_ID = $POS_ID_CUR)
								group by a.POS_ID, a.DEPARTMENT_ID, to_number(replace(POS_NO,'-','')), PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, c.PER_STATUS, 
											d.PER_STATUS, e.PER_STATUS, f.PER_STATUS, a.LEVEL_NO
								order by a.DEPARTMENT_ID, to_number(replace(POS_NO,'-','')) ";
			}			
		}elseif($DPISDB=="mysql"){
			$search_condition = str_replace(" where ", " and ", $search_condition); 
			if($SESS_ORG_STRUCTURE==1){		//มอบหมายงาน
				$cmd1 =" select 		a.POS_ID, a.DEPARTMENT_ID, POS_NO+0 as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
											c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
											f.PER_STATUS as PER_STATUS4, a.LEVEL_NO
							from (
									(
										(
											(
												(
													PER_POSITION a
													inner join PER_ORG_ASS b on (a.ORG_ID=b.ORG_ID)
												) left join PER_PERSONAL c on (a.POS_ID=c.POS_ID and c.PER_STATUS=1)
											) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and (d.PER_STATUS=0 or d.PER_STATUS=2))
										) left join PER_PERSONAL e on (a.POS_ID=e.PAY_ID and e.PER_STATUS=1)
									) left join PER_PERSONAL f on (a.POS_ID=f.PAY_ID and (f.PER_STATUS=0 or f.PER_STATUS=2))
									) left join PER_PERSONAL g on (a.POS_ID=g.POS_ID)
							where 	a.ORG_ID=b.ORG_ID	and (a.POS_ID = $POS_ID_CUR)
							group by a.POS_ID, a.DEPARTMENT_ID, POS_NO+0, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
											c.PER_STATUS, d.PER_STATUS, e.PER_STATUS, f.PER_STATUS, a.LEVEL_NO
							order by a.DEPARTMENT_ID, POS_NO+0";
			}else{	//ตามกฎหมาย
				$cmd1 =" select 		a.POS_ID, a.DEPARTMENT_ID, POS_NO+0 as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
											c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
											f.PER_STATUS as PER_STATUS4, a.LEVEL_NO
							from (
										(
											(
												(
													PER_POSITION a
													inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
												) left join PER_PERSONAL c on (a.POS_ID=c.POS_ID and c.PER_STATUS=1)
											) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and (d.PER_STATUS=0 or d.PER_STATUS=2))
										) left join PER_PERSONAL e on (a.POS_ID=e.PAY_ID and e.PER_STATUS=1)
									) left join PER_PERSONAL f on (a.POS_ID=f.PAY_ID and (f.PER_STATUS=0 or f.PER_STATUS=2))
							where 	a.ORG_ID=b.ORG_ID	and (a.POS_ID = $POS_ID_CUR)
							group by a.POS_ID, a.DEPARTMENT_ID, POS_NO+0, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
											c.PER_STATUS, d.PER_STATUS, e.PER_STATUS, f.PER_STATUS, a.LEVEL_NO
							order by a.DEPARTMENT_ID, POS_NO+0";
			}
		} // end if
		$db_dpis->send_cmd($cmd1);
		//$db_dpis->show_error();
		$data2 = $db_dpis->get_array();
		$POS_NO_CUR = trim($data2[POS_NO]);
		$ORG_ID_CUR = trim($data2[ORG_ID]);
		$ORG_NAME_CUR = trim($data2[ORG_NAME]);
		$ORG_ID_REF_CUR = trim($data2[ORG_ID_REF]);
		$PM_CODE_CUR = trim($data2[PM_CODE]);
		$PL_CODE_CUR = trim($data2[PL_CODE]);
		$PT_CODE_CUR = trim($data2[PT_CODE]);
		$CL_NAME_CUR = trim($data2[CL_NAME]);
		$ORG_ID_1_CUR = trim($data2[ORG_ID_1]);
		$ORG_ID_2_CUR = trim($data2[ORG_ID_2]);
		$POS_STATUS_CUR = trim($data2[POS_STATUS]);
		$LEVEL_NO_CUR = trim($data2[LEVEL_NO]);

		$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='".$PM_CODE_CUR."' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PM_NAME_CUR = $data_dpis2[PM_NAME];

		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='".$PL_CODE_CUR."' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PL_NAME_CUR = $data_dpis2[PL_NAME];
		if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง" && !$PM_NAME_CUR) $PM_NAME_CUR = $PL_NAME_CUR;

		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='".$PT_CODE_CUR."' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PT_NAME_CUR = $data_dpis2[PT_NAME];

		$cmd = "select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO_CUR'";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$NEW_POSITION_TYPE_CUR = $data_dpis2[POSITION_TYPE];
		$LEVEL_NAME_CUR = $data_dpis2[POSITION_LEVEL];

		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='02' and ORG_ID=$ORG_ID_REF_CUR ";
		if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_REF_NAME_CUR = $data_dpis2[ORG_NAME];

		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='04' and ORG_ID=$ORG_ID_1_CUR ";
		if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME_1_CUR = $data_dpis2[ORG_NAME];

		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='05' and ORG_ID=$ORG_ID_2_CUR ";
		if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME_2_CUR = $data_dpis2[ORG_NAME];
		//-----------------------
		$border = "";
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

		$pdf->Cell($heading_width[0], 7, "ปัจจุบัน", $border, 0, 'C', 0);
		$pdf->MultiCell($heading_width[1], 7, $PL_NAME_CUR, $border, "C");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
		$pdf->y = $start_y;
		$pdf->MultiCell($heading_width[2], 7, $PM_NAME_CUR, $border, "C");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
		$pdf->y = $start_y;
		$pdf->MultiCell($heading_width[3], 7, $CL_NAME_CUR, $border, "C");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
		$pdf->y = $start_y;
		$pdf->MultiCell($heading_width[4], 7, $PT_NAME_CUR, $border, "C");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4];
		$pdf->y = $start_y;
		$pdf->MultiCell($heading_width[5], 7, $ORG_REF_NAME_CUR, $border, "C");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5];
		$pdf->y = $start_y;
		$pdf->MultiCell($heading_width[6], 7, $ORG_NAME_CUR, $border, "C");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6];
		$pdf->y = $start_y;
		$pdf->MultiCell($heading_width[7], 7, $ORG_NAME_1_CUR, $border, "C");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7];
		$pdf->y = $start_y;
		$pdf->MultiCell($heading_width[8], 7, $ORG_NAME_2_CUR, $border, "C");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8];
		$pdf->y = $start_y;
		

		//================= Draw Border Line ====================
		$line_start_y = $start_y;		$line_start_x = $start_x;
		$line_end_y = $max_y;		$line_end_x = $start_x;
		$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
		for($i=0; $i<=8; $i++){
			$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
			$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
		} // end for
		//====================================================

		if(($pdf->h - $max_y - 10) < 15){ 
			$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
			if($data_count < $count_data){
				$pdf->AddPage();
				print_header();
				$max_y = $pdf->y;
			} // end if
		}else{
			$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
		} // end if
		$pdf->x = $start_x;			$pdf->y = $max_y;
	//===============	END CUR =================//
	//===============	STR HIS =================//
		$db_dpis->send_cmd($cmd_his);  //###
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;			

			$POS_ID = trim($data[POS_ID]);
			$show_POS_DATE = show_date_format($data[POS_DATE], 1);
			
			$ORG_ID = trim($data[ORG_ID]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$ORG_ID_REF = trim($data[ORG_ID_REF]);
			$PM_CODE = trim($data[PM_CODE]);
			$PL_CODE = trim($data[PL_CODE]);
			$PT_CODE = trim($data[PT_CODE]);
			$CL_NAME = trim($data[CL_NAME]);
			$ORG_ID_1 = trim($data[ORG_ID_1]);
			$ORG_ID_2 = trim($data[ORG_ID_2]);
			$POS_STATUS = trim($data[POS_STATUS]);
			if($POS_STATUS==1) $POS_STATUS = "ว่างไม่มีเงิน";
			elseif($POS_STATUS==2) $POS_STATUS = "ว่างมีเงิน";
			elseif($POS_STATUS==3) $POS_STATUS = "มีคนครอง";

			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='".$PM_CODE."' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PM_NAME = $data_dpis2[PM_NAME];

			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='".$PL_CODE."' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PL_NAME = $data_dpis2[PL_NAME];

			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='".$PT_CODE."' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PT_NAME = $data_dpis2[PT_NAME];

			$cmd = " select ORG_NAME from PER_ORG where OL_CODE='02' and ORG_ID=$ORG_ID_REF ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_REF_NAME = $data_dpis2[ORG_NAME];

			$cmd = " select ORG_NAME from PER_ORG where OL_CODE='04' and ORG_ID=$ORG_ID_1 ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME_1 = $data_dpis2[ORG_NAME];

			$cmd = " select ORG_NAME from PER_ORG where OL_CODE='05' and ORG_ID=$ORG_ID_2 ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME_2 = $data_dpis2[ORG_NAME];

			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->Cell($heading_width[0], 7, $show_POS_DATE, $border, 0, 'C', 0);
			$pdf->MultiCell($heading_width[1], 7, $PL_NAME, $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[2], 7, $PM_NAME, $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[3], 7, $CL_NAME, $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[4], 7, $PT_NAME, $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[5], 7, $ORG_REF_NAME, $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[6], 7, $ORG_NAME, $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[7], 7, $ORG_NAME_1, $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[8], 7, $ORG_NAME_2, $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8];
			$pdf->y = $start_y;
			

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
			for($i=0; $i<=8; $i++){
				$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
				$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
			//====================================================

			if(($pdf->h - $max_y - 10) < 15){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < $count_data){
					$pdf->AddPage();
					print_header();
					$max_y = $pdf->y;
				} // end if
			}else{
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
			} // end if
			$pdf->x = $start_x;			$pdf->y = $max_y;
		} // end while
	}else{
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>