<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
	} // end if

	if(!trim($RPTORD_LIST)) $RPTORD_LIST = "ORG";
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG"); 

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";
//	$arr_search_condition[] = "(e.MOV_CODE in ('10330', '10340', '10350', '10360', '10430', '10440', '10450'))";
	if ($BKK_FLAG==1)
		$arr_search_condition[] = "(e.MOV_CODE in ('3', '36'))";
	else
		$arr_search_condition[] = "(e.MOV_CODE in ('10330', '10340', '10350', '10360'))";

	$list_type_text = $ALL_REPORT_TITLE;
	if($export_type=="report"){
		include ("../report/rpt_condition3.php");
		}else if($export_type=="graph"){
		include ("../../admin/report/rpt_condition3.php");	//���͹䢷���ͧ����ʴ���
		}	
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "�ٻẺ����͡��§ҹ : ". ($select_org_structure==1?"�ç���ҧ����ͺ���§ҹ - ":"�ç���ҧ��������� - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||�ӹǹ$PERSON_TYPE[$search_per_type]������������ҧ$ORG_TITLE 㹻է�����ҳ $search_budget_year";
	if($export_type=="report")	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0303";
	$orientation='P';

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

	$heading_width[0] = "85";
	$heading_width[1] = "85";
	$heading_width[2] = "10";
	$heading_width[3] = "10";
	$heading_width[4] = "10";
	
//new***************************************************
    $heading_text[0] = "$ORG_TITLE ���|";
	$heading_text[1] = "$ORG_TITLE �������|";
	$heading_text[2] = "<**1**>�ӹǹ|���";
	$heading_text[3] = "<**1**>�ӹǹ|˭ԧ";
	$heading_text[4] = "<**1**>�ӹǹ|���";
		
	$heading_align = array('C','C','C','C','C');


		if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, a.PER_GENDER, e.MOV_CODE, e.ORG_ID_3, e.POH_ORG3, e.POH_EFFECTIVEDATE
						 from			(
												(
													PER_PERSONAL a 
													left join $position_table b on $position_join
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
											$search_condition
						 order by		a.PER_ID, e.POH_ORG3 ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			a.PER_ID, a.PER_GENDER, e.MOV_CODE, e.ORG_ID_3, e.POH_ORG3, e.POH_EFFECTIVEDATE
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_POSITIONHIS e
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=e.PER_ID(+)
											$search_condition
						 order by		a.PER_ID, e.POH_ORG3 ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, a.PER_GENDER, e.MOV_CODE, e.ORG_ID_3, e.POH_ORG3, e.POH_EFFECTIVEDATE
						 from			(
												(
													PER_PERSONAL a 
													left join $position_table b on $position_join
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
											$search_condition
						 order by		a.PER_ID, e.POH_ORG3 ";
	} // end if
	if($select_org_structure==1){ 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$GRAND_TOTAL = $GRAND_TOTAL_1 = $GRAND_TOTAL_2 = 0;
	while($data = $db_dpis->get_array()){
		$PER_ID = $data[PER_ID];
		$PER_GENDER = $data[PER_GENDER];
		$POH_EFFECTIVEDATE = substr($data[POH_EFFECTIVEDATE], 0, 10);
		$NEW_ORG_ID = $data[ORG_ID_3];
		$NEW_ORG_NAME = $data[POH_ORG3];
		$NEW_ORG_SHORT = $data[ORG_SHORT];
		
		if($DPISDB=="odbc")
			$cmd = " select 	 a.ORG_ID_3, a.POH_ORG3, b.ORG_SHORT
							 from 		 PER_POSITIONHIS a
							 				 left join PER_ORG b on (a.POH_ORG3=b.ORG_NAME)
							 where 	 a.PER_ID=$PER_ID and LEFT(trim(a.POH_EFFECTIVEDATE), 10) < '$POH_EFFECTIVEDATE' 
							 order by a.POH_EFFECTIVEDATE desc ";
		elseif($DPISDB=="oci8")
			$cmd = " select 	 a.ORG_ID_3, a.POH_ORG3, b.ORG_SHORT
							 from 		 PER_POSITIONHIS a, PER_ORG b
							 where 	 a.POH_ORG3=b.ORG_NAME(+) and a.PER_ID=$PER_ID and SUBSTR(trim(a.POH_EFFECTIVEDATE), 1, 10) < '$POH_EFFECTIVEDATE' 
							 order by a.POH_EFFECTIVEDATE desc ";
		elseif($DPISDB=="mysql")
			$cmd = " select 	 a.ORG_ID_3, a.POH_ORG3, b.ORG_SHORT
					 from 		 PER_POSITIONHIS a
									 left join PER_ORG b on (a.POH_ORG3=b.ORG_NAME)
					 where 	 a.PER_ID=$PER_ID and LEFT(trim(a.POH_EFFECTIVEDATE), 10) < '$POH_EFFECTIVEDATE' 
					 order by a.POH_EFFECTIVEDATE desc ";
		if($select_org_structure==1){ 
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	
		}
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$OLD_ORG_ID = $data2[ORG_ID_3];
		$OLD_ORG_NAME = $data2[POH_ORG3];
		$OLD_ORG_SHORT = $data2[ORG_SHORT];

		$POH_MOV_CODE = trim($data[MOV_CODE]);

		if ($BKK_FLAG==1) { 
			if((($OLD_ORG_NAME && $OLD_ORG_NAME != $NEW_ORG_NAME && in_array($POH_MOV_CODE, array('3', '36'))) || in_array($POH_MOV_CODE, array('36'))) && (($list_type=="PER_ORG" && $search_org_id==$OLD_ORG_ID) || $list_type!="PER_ORG")){
				$key = "$OLD_ORG_NAME:$NEW_ORG_NAME";
				if(!array_key_exists($key, $arr_content)){ 
					$arr_content[$key][old_org] = $OLD_ORG_NAME;
					$arr_content[$key][old_sho] = $OLD_ORG_SHORT;
					$arr_content[$key][new_org] = $NEW_ORG_NAME;
					$arr_content[$key][new_sho] = $NEW_ORG_SHORT;
					$arr_content[$key][count_1] = 0;
					$arr_content[$key][count_2] = 0;
				} // end if

				if($PER_GENDER==1){ 
					$arr_content[$key][count_1]++;
					$GRAND_TOTAL_1++;
				}elseif($PER_GENDER==2){ 
					$arr_content[$key][count_2]++;
					$GRAND_TOTAL_2++;
				} // end if
			} // end if
		} else {
			if((($OLD_ORG_NAME && $OLD_ORG_NAME != $NEW_ORG_NAME && in_array($POH_MOV_CODE, array('10350', '10360'))) || in_array($POH_MOV_CODE, array('10330', '10340'))) && (($list_type=="PER_ORG" && $search_org_id==$OLD_ORG_ID) || $list_type!="PER_ORG")){
				$key = "$OLD_ORG_NAME:$NEW_ORG_NAME";
				if(!array_key_exists($key, $arr_content)){ 
					$arr_content[$key][old_org] = $OLD_ORG_NAME;
					$arr_content[$key][old_sho] = $OLD_ORG_SHORT;
					$arr_content[$key][new_org] = $NEW_ORG_NAME;
					$arr_content[$key][new_sho] = $NEW_ORG_SHORT;
					$arr_content[$key][count_1] = 0;
					$arr_content[$key][count_2] = 0;
				} // end if

				if($PER_GENDER==1){ 
					$arr_content[$key][count_1]++;
					$GRAND_TOTAL_1++;
				}elseif($PER_GENDER==2){ 
					$arr_content[$key][count_2]++;
					$GRAND_TOTAL_2++;
				} // end if
			} // end if
		}
	} // end while

	ksort($arr_content);
	$GRAND_TOTAL = $GRAND_TOTAL_1 + $GRAND_TOTAL_2;
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	$count_data = count($arr_content);
	
//new*****************************************************************
	if($export_type=="report"){
	    if($count_data){
			$head_text1 = implode(",", $heading_text);
			$head_width1 = implode(",", $heading_width);
			$head_align1 = implode(",", $heading_align);
			$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
			if (!$result) echo "****** error ****** on open table for $table<br>";
			$pdf->AutoPageBreak = false; 
			
	//		print_header();
			
	//		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$data_count = 0;
			foreach($arr_content as $key => $value){
				$NAME_1 = $arr_content[$key][old_org];
				$NAME_2 = $arr_content[$key][new_org];
				$COUNT_1 = $arr_content[$key][count_1];
				$COUNT_2 = $arr_content[$key][count_2];
				$COUNT_TOTAL = $COUNT_1 + $COUNT_2;
		
			//new format************************************************************			
				$arr_data = (array) null;
				$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($NAME_1):$NAME_1);
				$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($NAME_2):$NAME_2);
				$arr_data[] =($COUNT_1?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_1)):number_format($COUNT_1)):"-") ;
				$arr_data[] =($COUNT_2?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_2)):number_format($COUNT_2)):"-") ;
				$arr_data[] =($COUNT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_TOTAL)):number_format($COUNT_TOTAL)):"-");
		
				$data_align = array("L", "L", "R", "R", "R");
				
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "cordia", "14", "", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end for
			$pdf->add_data_tab("", 0, "RHBL", $data_align, "cordia", "12", "", "000000", "");		// ��鹻Դ��÷Ѵ			

		//new format************************************************************			
			$arr_data = (array) null;
			$arr_data[] ="<**1**>���";
			$arr_data[] ="<**1**>���";
			$arr_data[] =($GRAND_TOTAL_1?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_TOTAL_1)):number_format($GRAND_TOTAL_1)):"-");
			$arr_data[] =($GRAND_TOTAL_2?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_TOTAL_2)):number_format($GRAND_TOTAL_2)):"-");
			$arr_data[] =($GRAND_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_TOTAL)):number_format($GRAND_TOTAL)):"-");
			
			$data_align = array("R", "R", "R", "R", "R");
			
			$result = $pdf->add_data_tab($arr_data, 7, "RHBL", $data_align, "cordia", "14", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

			$pdf->add_data_tab("", 0, "RHBL", $data_align, "cordia", "12", "b", "000000", "");		// ��鹻Դ��÷Ѵ				
		}else{
			$pdf->SetFont($fontb,'',16);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell(200,10,"********** ����բ����� **********",0,1,'C');
		} // end if

		$pdf->close();
		$pdf->Output();	
	}else if($export_type=="graph"){//if($export_type=="report"){
		sort($arr_key);
	//	echo "<pre>"; print_r($arr_key); echo "</pre>";
	//	echo "<pre>"; print_r($arr_content); echo "</pre>";
		$arr_content_key = array_keys($arr_content[0]);//print_r($arr_content_key);
		$arr_categories = array();
		foreach($arr_content as $key => $value){
			if($short_name=="y"){
				$old_name = $arr_content[$key][old_sho];
				$new_name = $arr_content[$key][new_sho];
			}else{
				$old_name = $arr_content[$key][old_org];
				$new_name = $arr_content[$key][new_org];
			}
			$arr_categories[$key] = trim(str_replace("/"," ",$old_name." / ".$new_name));
			$arr_series_caption_data[0][] = $arr_content[$key][count_1];
			$arr_series_caption_data[1][] = $arr_content[$key][count_2];
//			echo "content (".$i.")-M:".$arr_content[$key][count_1]."-W:".$arr_content[$key][count_2]."<br>";
		}
	//	echo "<pre>"; print_r($arr_categories); echo "</pre>";
		$arr_series_list[0] = implode(";", $arr_series_caption_data[0]).";$GRAND_TOTAL_1";
		$arr_series_list[1] = implode(";", $arr_series_caption_data[1]).";$GRAND_TOTAL_2";
		$chart_title = trim(str_replace("|"," ",$report_title));
		$chart_subtitle = $company_name;
		if(!$setWidth){ $setWidth = "$GRAPH_WIDE";}else{ $setWidth = "800";}
		if(!$setHeight){ $setHeight = "$GRAPH_HIGH";}else{$setHeight = "600";}
		$selectedFormat = "SWF";
		$series_caption_list = "���;˭ԧ";
		$categories_list = implode(";", $arr_categories).";���";
		if(strtolower($graph_type)=="pie"){
			$series_list = $GRAND_TOTAL_1.";".$GRAND_TOTAL_2."";
		}else{
			$series_list = implode("|", $arr_series_list);
		}
	//	echo($series_list);
		switch( strtolower($graph_type) ){
			case "column" :
				$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Column/style/column.scs";
				break;
			case "bar" :
				$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Bar/style/bar.scs";
				break;
			case "line" :
				$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Line/style/line.scs";
				break;
			case "pie" :
				$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Pie/style/pie.scs";
				break;
		} //switch( strtolower($graph_type) ){
	}//}else if($export_type=="graph"){
?>