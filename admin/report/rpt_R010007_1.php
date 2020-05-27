<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", "1800");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	unset($ARR_LEVEL_NO);
	unset($ARR_POSITION_TYPE);
	unset($ARR_GENDER);
	//$cmd = "select LEVEL_NO, LEVEL_NAME from PER_LEVEL where (LEVEL_ACTIVE=1) and (PER_TYPE = $search_per_type) order by LEVEL_SEQ_NO ,LEVEL_NO";
	$cmd = "select LEVEL_NO, LEVEL_NAME from PER_LEVEL where (LEVEL_ACTIVE=1) and (LEVEL_NAME LIKE '%$search_pt_name%') and (PER_TYPE = $search_per_type) order by LEVEL_SEQ_NO ,LEVEL_NO"; 
	$db_dpis->send_cmd($cmd);
	
	$ARR_COL=array("ชาย","หญิง","รวม");		//SEX
	while($data = $db_dpis->get_array()) {
		$arr_temp = explode(" ", trim($data[LEVEL_NAME]));
		//หาชื่อตำแหน่งประเภท
		if($search_per_type==1){
			$POSITION_TYPE = str_replace("ประเภท", "", $arr_temp[0]);
		}elseif($search_per_type==2){
			$POSITION_TYPE = $arr_temp[0];
		}elseif($search_per_type==3){
			$POSITION_TYPE = str_replace("กลุ่มงาน", "", $arr_temp[0]);
		}
		//หาชื่อระดับตำแหน่ง 
		$arr_temp[1]=str_replace("ระดับ", "", $arr_temp[1]);
		$LEVEL_NAME =  trim($arr_temp[1]);
		$ARR_POSITION_TYPE[$POSITION_TYPE][] = $LEVEL_NAME;
	
		$ARR_LEVEL_NO[$LEVEL_NAME] = trim($data[LEVEL_NO]);
		$ARR_GENDER[$POSITION_TYPE][] = $ARR_COL;
	}
		//print("<pre>");
		//print_r($ARR_LEVEL_NO[$search_pt_name]);
		//print_r($ARR_GENDER);
		//print("</pre>");

//--------------------------------------------------------------------------------------------------------
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE", "SEX", "PROVINCE", "EDUCLEVEL", "EDUCMAJOR"); 
		
	//echo $RPTORD_LIST;
	//print_r($arr_rpt_order);
	//แยกตามเงื่อนไขที่เลือก
	$select_list = "";		$order_by = "";		$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "SEX" :											//แยกตามเพศ###############
				if($select_list) $select_list .= ", ";
				$select_list .= "a.SUM_QTY_M, a.SUM_QTY_F";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.SUM_QTY_M, a.SUM_QTY_F";
				$heading_name .= "เพศ";
			break;
		} // end switch case
	} // end for
	//if(!trim($order_by)) $order_by = "b.ORG_ID";
	//if(!trim($select_list)) $select_list = "b.ORG_ID";

	$search_condition = "";
	//$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	//$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	$list_type_text = "ทั้งส่วนราชการ";
	
	if($list_type=="ALL"){	//ทุกกระทรวง

	}elseif($list_type == "PER_ORG_TYPE_1"){	//ส่วนกลาง
	
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		}
		
		$list_type_text = "ส่วนกลาง";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='01')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='01')";
	}elseif($list_type == "PER_ORG_TYPE_2"){		// ส่วนภูมิภาค
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		}

		$list_type_text = "ส่วนภูมิภาค";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='03')";
	}elseif($list_type == "PER_TYPE"){	//dsdsd########################
		// ประเภทตำแหน่ง
		$list_type_text = "";
		// ตน.ในสายงาน
		if($search_per_type==1){
			/***if(trim($search_pt_code)){
				$search_pt_name = trim($search_pt_name);
				$arr_search_condition[] = "(trim(c.LEVEL_NAME) LIKE '%$search_pt_name%')";
				$list_type_text .= "$search_pt_name";
			}***/
			if(trim($search_pl_code)){
				$search_pl_code = trim($search_pl_code);
				$arr_search_condition[] = "(trim(a.PL_CODE)='$search_pl_code')";
				$list_type_text .= "$search_pl_name";
			}
		}elseif($search_per_type==3 && trim($search_ep_code)){
			$search_ep_code = trim($search_ep_code);
			//$arr_search_condition[] = "(trim(a.EP_CODE)='$search_ep_code')";
			$arr_search_condition[] = "(trim(a.EL_CODE)='$search_ep_code')";
			$list_type_text .= "$search_ep_name";
		} // end if
		/***elseif($search_per_type==2 && trim($search_pn_code)){
			$search_pn_code = trim($search_pn_code);
			$arr_search_condition[] = "(trim(a.PN_CODE)='$search_pn_code')";
			$list_type_text .= "$search_pn_name";
		} ***/
		//echo $list_type_text;
	}
		
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	
	$position_type = "ตำแหน่งประเภท : $search_pt_name";
	$position_line = "ชื่อตำแหน่งในสายงาน : $search_pl_name";
	$company_name = $position_type." ".$position_line;
	
	$data_date = "ข้อมูล ณ วันที่ .....";
	
	$report_title = "จำนวนข้าราชการพลเรือน แยกตาม$heading_name";
	$report_title .= " $list_type_text";
	$report_title .= " ประจำปีงบประมาณ  พ.ศ. $search_year";
	/***if($search_per_type==1) $report_title = "$DEPARTMENT_NAME||จำนวนข้าราชการจำแนกตามระดับตำแหน่ง";
	elseif($search_per_type==2) $report_title = "$DEPARTMENT_NAME||จำนวนลูกจ้างประจำจำแนกตามระดับตำแหน่ง";
	elseif($search_per_type==3) $report_title = "$DEPARTMENT_NAME||จำนวนพนักงานราชการจำแนกตามระดับตำแหน่ง";***/
	$report_code = "R1007";
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
	$pdf->SetFont('angsa','',14);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$count_position_type=count($ARR_POSITION_TYPE[$search_pt_name]);
	$count_column=$count_position_type*count($ARR_COL);
	$heading_width[0] = "100";
	for($i=1;$i<=$count_column;$i++) {
		$heading_width[$i] = "10";
	}
	$heading_width[count($heading_width)] = "10";
	$heading_width[count($heading_width)+1] = "10";
	$heading_width[count($heading_width)+2] = "10";

	function print_header(){
		global $pdf, $heading_width, $heading_name;
		global $count_position_type,$search_pt_name;
		global $ARR_POSITION_TYPE,$ARR_GENDER;

		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell($heading_width[0] ,7,"ส่วนราชการ",'LTR',0,'C',1);
		for($j=0;$j<$count_position_type;$j++) {
				$count=count($ARR_GENDER[$search_pt_name][$j]);
				$tmp_position_type = $ARR_POSITION_TYPE[$search_pt_name][$j];
				if($j==0){
					$merg=($heading_width[$j+($count-2)] +$heading_width[$j+($count-1)] +$heading_width[$j+$count]);
				}else{
					$tmp_count+=$count;
					$k=($j+$tmp_count);
					$merg=($heading_width[$k] + $heading_width[$k+1] + $heading_width[$k+2]);
				}
				$pdf->Cell($merg ,7,"$tmp_position_type",'LTBR',0,'C',1);
				///$ARR_GENDER[$search_pt_name][$j] = $ARR_COL;
		}
		$heading_width[$k+4]="10";		//????????
		//echo ($heading_width[$k+3]+$heading_width[$k+4]+$heading_width[$k+5]);
		$pdf->Cell(($heading_width[$k+3]+$heading_width[$k+4]+$heading_width[$k+5]),7,"จำนวนรวม",'LTBR',1,'C',1);
	
		//แถว 2 ---------------------
		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		for($i=0;$i<$count_position_type; $i++){	
			for($j=0;$j<count($ARR_GENDER[$search_pt_name][$i]);$j++) {
				$tmp_gender = $ARR_GENDER[$search_pt_name][$i][$j];
				$pdf->Cell($heading_width[$j+1] ,7,"$tmp_gender",'LTBR',0,'C',1);
			}
		}
		$pdf->Cell($heading_width[$k+3] ,7,"ชาย",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[$k+4] ,7,"หญิง",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[$k+5] ,7,"รวม",'LTBR',1,'C',1);
	} // function		

	//หาจำนวนข้าราชการทั้งหมด other case
	function count_person($level_no,$gender,$MINISTRY_ID, $search_condition, $addition_condition){ //10001
		global $DPISDB, $db_dpis2;
		global $arr_rpt_order, $search_per_type;
	
		//echo "<br>------".$level_no."<br>++++++++".$search_condition."<br>********".$addition_condition;
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$level_no = str_pad($level_no, 2, "0", STR_PAD_LEFT);
		if($gender=="ชาย"){				$field="a.SUM_QTY_M";		}
		elseif($gender=="หญิง"){		$field="a.SUM_QTY_F";		}
		elseif($gender=="รวม"){		$field="(a.SUM_QTY_M+a.SUM_QTY_F)";		}

		if($search_per_type==1){
			// ข้าราชการ
			if($DPISDB=="odbc"){
				if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															(	
																PER_PERSONAL a 
																left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%||2||%')
									 where		trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}else{
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
									 where		trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
                                } // end if
			}elseif($DPISDB=="oci8"){		//กำลังแก้		
				if(in_array("SEX", $arr_rpt_order) || in_array("SEX", $arr_rpt_order)){
					$cmd = "select $field as count_person
							 from PER_SUM_DTL10 a,PER_SUM b,PER_ORG c,PER_ORG d,PER_ORG e
							 where  (b.ORG_ID=c.ORG_ID) and (c.ORG_ID_REF=d.ORG_ID) and (d.ORG_ID_REF=e.ORG_ID) and (e.ORG_ID=$MINISTRY_ID)
				 				and (a.SUM_ID=b.SUM_ID) and (trim(a.LEVEL_NO)='$level_no') 
								 $search_condition  
			 				order by a.SUM_ID
							 ";
				}else{
					$cmd = "select g.ORG_ID  as MINISTRY_ID,e.ORG_ID  as DEPARTMENT_ID,a.LEVEL_NO,a.PL_CODE,a.EL_CODE,c.LEVEL_NAME,$select_list
							 from PER_SUM_DTL10 a,PER_SUM b,PER_LEVEL c,PER_LINE d,PER_ORG e,PER_ORG f,PER_ORG g
							 where  (b.ORG_ID=e.ORG_ID) and (e.ORG_ID_REF=f.ORG_ID) and (f.ORG_ID_REF=g.ORG_ID) 
							 				and (g.ORG_ID_REF = 1 and g.ORG_ID <> 1) and (a.SUM_ID=b.SUM_ID) and (a.LEVEL_NO=c.LEVEL_NO)
							 			  	and (a.PL_CODE=d.PL_CODE)
							 				$search_condition  
							 order by g.ORG_SEQ_NO, g.ORG_CODE,e.ORG_SEQ_NO, e.ORG_CODE
							 ";
				}
			}elseif($DPISDB=="mysql"){
				if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															(	
																PER_PERSONAL a 
																left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%||2||%')
									 where		trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}else{
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
									 where		trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}
			} // end if
		}elseif($search_per_type==2){
			// ลูกจ้าง
			if($DPISDB=="odbc"){
				if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															(	
																PER_PERSONAL a 
																left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%||2||%')
									 where		trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}else{
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															PER_PERSONAL a 
															left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
									 where		trim(a.LEVEL_NO)) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
                                } // end if
			}elseif($DPISDB=="oci8"){				
				if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_EDUCATE d
									 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID(+) and d.EDU_TYPE like '%||2||%'
														and trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}else{
					$cmd = " select			count(a.PER_ID) as count_person
									 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c
									 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+)
														and trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				} // end if
			}elseif($DPISDB=="mysql"){
				if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															(	
																PER_PERSONAL a 
																left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%||2||%')
									 where		trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}else{
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															PER_PERSONAL a 
															left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
									 where		trim(a.LEVEL_NO)) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}
			} // end if
		} // end if
	else if($search_per_type==3){
			// พนักงานราชการ
			if($DPISDB=="odbc"){
				if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															(	
																PER_PERSONAL a 
																left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%||2||%')
									 where		trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}else{
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															PER_PERSONAL a 
															left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
									 where		trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
                                } // end if
			}elseif($DPISDB=="oci8"){				
				if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_EDUCATE d
									 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID(+) and d.EDU_TYPE like '%||2||%'
														and trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}else{
					$cmd = " select			count(a.PER_ID) as count_person
									 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c
									 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+)
														and trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				} // end if
			}elseif($DPISDB=="mysql"){
				if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															(	
																PER_PERSONAL a 
																left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%||2||%')
									 where		trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}else{
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															PER_PERSONAL a 
															left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
									 where		trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID
								   ";
				}
			} // end if
		}

		if($select_org_structure==1) $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$count_person = $db_dpis2->send_cmd($cmd);
		//echo "<br>$cmd<br>";
		//echo "<hr><br>";
		//$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if
return $count_person;
} // function

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $PN_CODE, $PER_GENDER, $PV_CODE, $EM_CODE, $EN_CODE;
	
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "SEX" :
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $PN_CODE, $PER_GENDER, $PV_CODE, $EM_CODE, $EN_CODE;

		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "SEX" :
					//$PER_GENDER = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
	if($select_org_structure == 0){				//โครงสร้างตามกฏหมาย
		if($DPISDB=="odbc"){
			$cmd = " select			distinct g.ORG_SEQ_NO, g.ORG_ID as MINISTRY_ID, d.ORG_SEQ_NO, d.ORG_ID as DEPARTMENT_ID, $select_list
					   from			(
										(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
										) inner join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
									) inner join PER_ORG g on (d.ORG_ID_REF=g.ORG_ID)
									$search_condition
					   order by		g.ORG_SEQ_NO, g.ORG_ID, d.ORG_SEQ_NO, d.ORG_ID, $order_by
					";
		}elseif($DPISDB=="oci8"){	//กำลังแก้
			$search_condition = str_replace(" where ", " and ", $search_condition);
			/***$cmd = "select g.ORG_ID  as MINISTRY_ID,e.ORG_ID  as DEPARTMENT_ID,a.LEVEL_NO,a.PL_CODE,a.EL_CODE,c.LEVEL_NAME,$select_list
							 from PER_SUM_DTL10 a,PER_SUM b,PER_LEVEL c,PER_LINE d,PER_ORG e,PER_ORG f,PER_ORG g
							 where  (b.ORG_ID=e.ORG_ID) and (e.ORG_ID_REF=f.ORG_ID) and (f.ORG_ID_REF=g.ORG_ID) 
							 				and (g.ORG_ID_REF = 1 and g.ORG_ID <> 1) and (a.SUM_ID=b.SUM_ID) and (a.LEVEL_NO=c.LEVEL_NO)
							 			  	and (a.PL_CODE=d.PL_CODE)
							 $search_condition  
							 				order by g.ORG_SEQ_NO, g.ORG_CODE,e.ORG_SEQ_NO, e.ORG_CODE, $order_by
							 ";***/
			$cmd = "select ORG_ID as MINISTRY_ID from PER_ORG where ORG_ID_REF = 1 and ORG_ID <> 1 order by ORG_SEQ_NO, ORG_CODE";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct g.ORG_SEQ_NO, g.ORG_ID as MINISTRY_ID, d.ORG_SEQ_NO, d.ORG_ID as DEPARTMENT_ID, $select_list
					   from			(
										(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
										) inner join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
									) inner join PER_ORG g on (d.ORG_ID_REF=g.ORG_ID)
									$search_condition
					   order by		g.ORG_SEQ_NO, g.ORG_ID, d.ORG_SEQ_NO, d.ORG_ID, $order_by
					";
		}
	}elseif($select_org_structure == 1){	//โครงสร้างตามมอบหมายงาน
		if($DPISDB=="odbc"){
			$cmd = " select			distinct g.ORG_SEQ_NO, g.ORG_ID as MINISTRY_ID, f.ORG_SEQ_NO, f.ORG_ID as DEPARTMENT_ID, $select_list
					   from			(
										(
											(
												(
													(
														PER_POSITION a on (a.ORG_ID=b.ORG_ID)
														inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
												) inner join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
											) inner join PER_ORG_ASS e on (d.ORG_ID=e.ORG_ID)
										) inner join PER_ORG_ASS f on (e.ORG_ID_REF=f.ORG_ID)
									) inner join PER_ORG_ASS g on (f.ORG_ID_REF=g.ORG_ID)
									$search_condition
					   order by		g.ORG_SEQ_NO, g.ORG_ID, f.ORG_SEQ_NO, f.ORG_ID, $order_by
					";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct g.ORG_SEQ_NO, g.ORG_ID as MINISTRY_ID, f.ORG_SEQ_NO, f.ORG_ID as DEPARTMENT_ID, $select_list
					   from			PER_POSITION a, PER_ORG b, PER_CO_LEVEL c, PER_PERSONAL d, PER_ORG_ASS e, PER_ORG_ASS f, PER_ORG_ASS g
					   where		a.ORG_ID=b.ORG_ID and a.CL_NAME=c.CL_NAME and a.POS_ID=d.POS_ID and d.ORG_ID=e.ORG_ID 
					   				and e.ORG_ID_REF=f.ORG_ID and f.ORG_ID_REF=g.ORG_ID
									$search_condition
					   order by		g.ORG_SEQ_NO, g.ORG_ID, f.ORG_SEQ_NO, f.ORG_ID, $order_by
					";
			/* select ORG_ID , ORG_NAME, ORG_ID_REF from PER_ORG_ASS where ORG_ID_REF = 5 and ORG_ID <> 5 order by ORG_SEQ_NO, ORG_CODE  */
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct g.ORG_SEQ_NO, g.ORG_ID as MINISTRY_ID, f.ORG_SEQ_NO, f.ORG_ID as DEPARTMENT_ID, $select_list
					   from			(
										(
											(
												(
													(
														PER_POSITION a on (a.ORG_ID=b.ORG_ID)
														inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
												) inner join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
											) inner join PER_ORG_ASS e on (d.ORG_ID=e.ORG_ID)
										) inner join PER_ORG_ASS f on (e.ORG_ID_REF=f.ORG_ID)
									) inner join PER_ORG_ASS g on (f.ORG_ID_REF=g.ORG_ID)
									$search_condition
					   order by		g.ORG_SEQ_NO, g.ORG_ID, f.ORG_SEQ_NO, f.ORG_ID, $order_by
					";
		}
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
// echo "$cmd<br>";
//	$db_dpis->show_error();
//		print("<pre>");
//		print_r($ARR_POSITION_TYPE);
//		print_r($ARR_GENDER);
//		print("</pre>");

	$data_count = 0;
	initialize_parameter(0);
	$DEPARTMENT_ID = -1;
	while($data = $db_dpis->get_array()){

	//####################################################################
	/*********if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){	
		if($data_count > 1){
			//$GRAND_TOTAL_1[$DEPARTMENT_ID] = count_position(1, $search_condition, "");
			//$GRAND_TOTAL_2[$DEPARTMENT_ID] = count_position(2, $search_condition, "");
			//$GRAND_TOTAL_3[$DEPARTMENT_ID] = count_position(3, $search_condition, "");
			
			//$GRAND_TOTAL[$DEPARTMENT_ID] = $GRAND_TOTAL_1[$DEPARTMENT_ID] + $GRAND_TOTAL_2[$DEPARTMENT_ID] + $GRAND_TOTAL_3[$DEPARTMENT_ID];
		} // end if

		$MINISTRY_ID = $data[MINISTRY_ID];
		if($select_org_structure==0)
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		elseif($select_org_structure==1)
			$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$MINISTRY_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME = $data2[ORG_NAME];
			
		$arr_content[$data_count][type] = "MINISTRY";
		$arr_content[$data_count][id] = $MINISTRY_ID;
		$arr_content[$data_count][ref_name] = $MINISTRY_NAME;
		$arr_content[$data_count][name] = $ORG_NAME." ".$data[LEVEL_NAME]." ช=".$data[SUM_QTY_M]." ญ=". $data[SUM_QTY_F];
		
		$data_count++;
		} // end if*********/
		//####################################################################		

		//หาชื่อกระทรวง 
		$MINISTRY_ID = $data[MINISTRY_ID];
		if($MINISTRY_ID){	
			if($select_org_structure==0)
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$MINISTRY_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = $data2[ORG_NAME];
			
			$arr_content[$data_count][type] = "MINISTRY";
			///$arr_content[$data_count][id] = $MINISTRY_ID;
			///$arr_content[$data_count][ref_name] = $MINISTRY_NAME;
			$arr_content[$data_count][name] = $ORG_NAME;

			$data_count++;
		} // end if
		//-----------------------------------------------------------------------------------------------
		/***********$arr_temp = explode(" ", trim($data[LEVEL_NAME]));
		//หาชื่อตำแหน่งประเภท
		if($search_per_type==1){
			$POSITION_TYPE = str_replace("ประเภท", "", $arr_temp[0]);
		}elseif($search_per_type==2){
			$POSITION_TYPE = $arr_temp[0];
		}elseif($search_per_type==3){
			$POSITION_TYPE = str_replace("กลุ่มงาน", "", $arr_temp[0]);
		}
		$LEVEL_NO =  trim($data[LEVEL_NO]);
		//หาชื่อระดับตำแหน่ง 
		$arr_temp[1]=str_replace("ระดับ", "", $arr_temp[1]);
		$LEVEL_NAME =  trim($arr_temp[1]);
		//---------------------------------------------------------------------------
		$PER_SUM_M=trim($data[SUM_QTY_M]);	//ชาย	
		$PER_SUM_F=trim($data[SUM_QTY_F]);		//หญิง		
		//---------------------------------------------------------------------------*************/

		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
					case "SEX" :
					//if($PER_GENDER){
									
						$addition_condition = generate_condition($rpt_order_index);
						
						$arr_content[$data_count][type] = "SEX";
						$arr_content[$data_count][name] = $ORG_NAME;
						for($i=0;$i<$count_position_type; $i++){
							$tmp_position_type = $ARR_POSITION_TYPE[$search_pt_name][$i];
							$tmp_level_no = $ARR_LEVEL_NO[$tmp_position_type];
							///$ARR_GENDER[$search_pt_name][$i] = $ARR_COL;
							for($j=0;$j<count($ARR_GENDER[$search_pt_name][$i]);$j++) {
								$k++;
								$tmp_gender = $ARR_GENDER[$search_pt_name][$i][$j];	
								$arr_content[$data_count]["level_".$tmp_gender.$k] = count_person($tmp_level_no,$tmp_gender,$MINISTRY_ID, $search_condition, $addition_condition);
								//$arr_content[$data_count]["level_".$tmp_gender] = $PER_SUM_M." ".$PER_SUM_F;
								if($rpt_order_index == 0) $LEVEL_TOTAL[$tmp_gender.$k] += $arr_content[$data_count]["level_".$tmp_gender.$k];
							}
						} // end for

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					//} // end if
				break;
			} // end switch case
		} // end for
	} // end while
	
	if(array_search("EDUCLEVEL", $arr_rpt_order) !== false  && array_search("EDUCLEVEL", $arr_rpt_order) == 0){
		//for($i=0; $i<count($ARR_COL); $i++){ 
			//$tmp_gender = $ARR_COL[$i];
			//$LEVEL_TOTAL[$tmp_gender] = count_person($tmp_level_no,$tmp_gender,$MINISTRY_ID, $search_condition, "");
		//} // end for
	} // end if
	
	//$GRAND_TOTAL = array_sum($LEVEL_TOTAL);
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
//	echo "<pre>"; print_r($ARR_GENDER); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();

		//รายละเอียดเนื้อหาทั้งหมด
		//echo count($arr_content);
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];						//ส่วนราชการ
			unset($COUNT_LEVEL);
			
			for($i=0;$i<$count_position_type; $i++){			//276
				for($j=0;$j<count($ARR_GENDER[$search_pt_name][$i]);$j++) {
					$p++;
					$tmp_gender = $ARR_GENDER[$search_pt_name][$i][$j]; 
					//echo "<br>".$data_count." : ".$tmp_gender.$p." == level_".$tmp_gender.$p."<br>";
					$COUNT_LEVEL[$tmp_gender.$p] = $arr_content[$data_count]["level_".$tmp_gender.$p];
				}
			} // end for
			$COUNT_TOTAL = array_sum($COUNT_LEVEL);
			//if($GRAND_TOTAL) $PERCENT_TOTAL = ($COUNT_TOTAL / $GRAND_TOTAL) * 100;

			$border = "";
			if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
				$pdf->SetFont('angsab','',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			}else{
				$pdf->SetFont('angsa','',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			} // end if

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->MultiCell($heading_width[0], 7, "$NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0];
			$pdf->y = $start_y;
			for($i=0;$i<$count_position_type; $i++){
				for($j=0;$j<count($ARR_GENDER[$search_pt_name][$i]);$j++) {
					$r++;
					$tmp_gender = $ARR_GENDER[$search_pt_name][$i][$j];
					//echo "<br>".$data_count." : ".$tmp_gender.$r." == level_".$tmp_gender.$r."<br>";
					$pdf->Cell($heading_width[1], 7, ($COUNT_LEVEL[$tmp_gender.$r]?number_format($COUNT_LEVEL[$tmp_gender.$r]):"-"), $border, 0, 'R', 0);
					$pdf->x = $start_x + $heading_width[0] + ($heading_width[1] * ($i+1));
				}
			}
			$pdf->Cell($heading_width[1], 7, ($COUNT_LEVEL[$tmp_gender.$r]?number_format($COUNT_LEVEL[$tmp_gender.$r]):"-"), $border, 0, 'R', 0);
			
			$pdf->Cell($heading_width[2], 7, ($COUNT_TOTAL?number_format($COUNT_TOTAL):"1-"), $border, 0, 'R', 0);					//รวมชาย
			$pdf->Cell($heading_width[3], 7, ($PERCENT_TOTAL?number_format($PERCENT_TOTAL, 2):"2-"), $border, 0, 'R', 0);	//รวมหญิง
			$pdf->Cell($heading_width[4], 7, ($PERCENT_TOTAL?number_format($PERCENT_TOTAL, 2):"3-"), $border, 0, 'R', 0); 	//รวม

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
			for($i=0; $i<$count_column+3; $i++){
				if($i>=1 && $i<=$count_column){			
					$line_start_y = $start_y;		$line_start_x += $heading_width[1];
					$line_end_y = $max_y;		$line_end_x += $heading_width[1];
				}elseif($i > $count_column){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i - ($count_column-1)];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i - ($count_column-1)];
				}else{
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
				} // end if
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
			//====================================================

			if(($pdf->h - $max_y - 10) < 15){ 
			//if(($pdf->h - $max_y - 10) < $count_column){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < (count($arr_content) - 1)){
					$pdf->AddPage();
					print_header();
					$max_y = $pdf->y;
				} // end if
			}else{
				if($data_count == (count($arr_content) - 1)) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);		
			} // end if
			$pdf->x = $start_x;			$pdf->y = $max_y;
		} // end for
		//$PERCENT_TOTAL = ($GRAND_TOTAL / $GRAND_TOTAL) * 100;

		$border = "LTBR";
		$pdf->SetFont('angsab','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

		$pdf->MultiCell($heading_width[0], 7, "รวม", $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width[0];
		$pdf->y = $start_y;
		for($i=0;$i<$count_position_type; $i++){	
			for($j=0;$j<count($ARR_GENDER[$search_pt_name][$i]);$j++) {
				$c++;
				$tmp_gender = $ARR_GENDER[$search_pt_name][$i][$j];
				//echo $tmp_gender.$c." ";
				$pdf->Cell($heading_width[1], 7, ($LEVEL_TOTAL[$tmp_gender.$c]?number_format($LEVEL_TOTAL[$tmp_gender.$c]):"-"), $border, 0, 'R', 0);
			} // end for
		}
		$pdf->Cell($heading_width[2], 7, ($GRAND_TOTAL?number_format($GRAND_TOTAL):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[3], 7, ($PERCENT_TOTAL?number_format($PERCENT_TOTAL, 2):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[4], 7, ($PERCENT_TOTAL?number_format($PERCENT_TOTAL, 2):"-"), $border, 1, 'R', 0);
	}else{
		$pdf->SetFont('angsab','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();		
?>