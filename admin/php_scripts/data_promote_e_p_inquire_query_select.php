<?
//####มาจาก tab ค้นหาข้อมูล กดมาแล้วเข้าหน้านี้ #####//
//===ควรเทียบ code ตาม data_promote_e_p_inquire_query_search.php ให้จำนวนและข้อมูลเหมือนกับที่ search มา
unset($arr_search_condition);

// ========= start of condition =========
//if (trim($search_from))		$arr_search_condition[] = "(a.$search_pos = b.$search_pos)";		// เงื่อนไขของ $search_from กับ $search_position_condition เหมือนกัน ถ้าใส่มันจะอยู่ในเงื่อนไขทั้งคู่

//if($search_pos && $POS_POEM_ID)	$arr_search_condition[] ="(b.$search_pos=$POS_POEM_ID)";
if (trim($LEVEL_START)) 
	$arr_search_condition[] = "(a.LEVEL_NO >= '$LEVEL_START')";
if (trim($LEVEL_END)) 
	$arr_search_condition[] = "(a.LEVEL_NO <= '$LEVEL_END')";
if (trim($PL_PN_CODE)){
	if($PER_TYPE==1) $arr_search_condition[] = "(b.PL_CODE = '$PL_PN_CODE')";	
	elseif($PER_TYPE==2) $arr_search_condition[] = "(b.PN_CODE = '$PL_PN_CODE')";	
	elseif($PER_TYPE==3) $arr_search_condition[] = "(b.EP_CODE = '$PL_PN_CODE')";	
} // end if

if (trim($ORG_ID_M3)){
	$arr_search_condition[] = "(b.ORG_ID = $ORG_ID_M3)";	
}elseif(trim($ORG_ID_M2)){
	$arr_search_condition[] = "(a.DEPARTMENT_ID = $ORG_ID_M2)";	
	$cmd = " select ORG_ID from PER_ORG where OL_CODE='03' and ORG_ID_REF=$ORG_ID_M2 ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
	$arr_search_condition[] = "(b.ORG_ID in (". implode(",", $arr_org) ."))";
}elseif(trim($ORG_ID_M1)){
	$cmd = " select 	b.ORG_ID
					 from   	PER_ORG a, PER_ORG b
					 where  	a.OL_CODE='02' and b.OL_CODE='03' and a.ORG_ID_REF=$ORG_ID_M1 and b.ORG_ID_REF=a.ORG_ID
					 order by a.ORG_ID, b.ORG_ID ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
	$arr_search_condition[] = "(b.ORG_ID in (". implode(",", $arr_org) ."))";
}elseif(trim($PV_CODE)){
	$cmd = " select 	ORG_ID
					 from   	PER_ORG
					 where  	OL_CODE='03' and PV_CODE='$PV_CODE'
					 order by ORG_ID ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
	$arr_search_condition[] = "(b.ORG_ID in (". implode(",", $arr_org) ."))";
} // end if

//$search_condition = "";
//if ($arr_search_condition)		$search_condition = " and " . implode(" and ", $arr_search_condition);

//if(trim($PRO_DATE))	$arr_search_condition[] = "(PRO_DATE = '".trim($PRO_DATE)."')";
if (trim($SAL_START))
	$arr_search_condition[] = "(PER_SALARY > $SAL_START)";	
if (trim($SAL_END))	
	$arr_search_condition[] = "(PER_SALARY < $SAL_END)";				
if (trim($EN_CODE)) {
	$EN_CODE = trim($EN_CODE);
	$arr_search_condition[] = "e.EN_CODE = '$EN_CODE'";
}
if($PRO_DATE){
	$temp_pro_date =  save_date($PRO_DATE);
	$temp_date = explode("-",$temp_pro_date);
	$PRO_DATE = trim($temp_date[2])."/".trim($temp_date[1])."/".trim((string)((int)$temp_date[0] + 543));	//สำหรับแสดง 
}
//Step 1 : หาคนจากตาราง  
if ($table_select == "PER_PROMOTE_P")					$field = "POS_ID";
elseif ($table_select == "PER_PROMOTE_E") 		$field = "POEM_ID";
// Step 2 : หา PER_ID ตามเงื่อนไข
$cmd = " select 		$field , PER_ID
				from 			$table_select 
				where 		PRO_DATE= '".trim($temp_pro_date)."' and $field = $POS_POEM_ID
				order by 	$field, PER_ID  ";				 					 
$count_page_data = $db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
if ($count_page_data) {
	while ($data = $db_dpis->get_array()) {
		$arr_per_id[] = $data[PER_ID];
	}
	$temp_per_id = implode(", ", $arr_per_id);
	$search_per_id = " a.PER_ID in ($temp_per_id) and ";
	$arr_search_condition[] = " a.PER_ID in ($temp_per_id) ";
} //end count
//echo "<br>SHOW_SEARCH_SELECT [ $count_page_data ] -----> $cmd";

if (trim($YEAR_START)) {
	if ($CHECK_DATE) {
		$tmp_date = save_date($CHECK_DATE);
		$effectivedate_min = date_adjust($tmp_date, "y", ($YEAR_START * -1));
	} else {
		$effectivedate_min = date_adjust(date("Y-m-d"), "y", ($YEAR_START * -1));
	}
	if($DPISDB=="odbc") $arr_having_condition[] = "(LEFT(trim(min(POH_EFFECTIVEDATE)), 10) <= '$effectivedate_min')";
	elseif($DPISDB=="oci8") $arr_having_condition[] = "(SUBSTR(trim(min(POH_EFFECTIVEDATE)), 1, 10) <= '$effectivedate_min')";
	elseif($DPISDB=="mysql") $arr_having_condition[] = "(LEFT(trim(min(POH_EFFECTIVEDATE)), 10) <= '$effectivedate_min')";
}
if (trim($YEAR_END))	{
	if ($CHECK_DATE) {
		$tmp_date = save_date($CHECK_DATE);
		$effectivedate_max = date_adjust($tmp_date, "y", ($YEAR_END * -1));
	} else {
		$effectivedate_max = date_adjust(date("Y-m-d"), "y", ($YEAR_END * -1));
	}
	if($DPISDB=="odbc") $arr_having_condition[] = "(LEFT(trim(min(POH_EFFECTIVEDATE)), 10) >= '$effectivedate_max')";
	elseif($DPISDB=="oci8") $arr_having_condition[] = "(SUBSTR(trim(min(POH_EFFECTIVEDATE)), 1, 10) >= '$effectivedate_max')";
	elseif($DPISDB=="mysql") $arr_having_condition[] = "(LEFT(trim(min(POH_EFFECTIVEDATE)), 10) >= '$effectivedate_max')";
}	

$having_clause = $search_condition = "";
if ($arr_search_condition)				$search_condition = " and " . implode(" and ", $arr_search_condition);
if(count($arr_having_condition)) 	$having_clause = " having " . implode(" and ", $arr_having_condition);
$SELECTED_LIST="";
// ========= end of condition =========
	
/*******************
// count ข้อมูลทั้งหมดตามที่สอบถาม และนำ PER_ID ไป select ข้อมูล		[มันไม่ได้เก็บ PL_CODE ในตาราง promote และวันที่ก็เอามาใช้ในเงื่อนไขนี้ไม่ได้]
// count ข้อมูลทั้งหมดตามเงื่อนไข
if($DPISDB=="odbc"){
	$cmd = " 	select 		a.PER_ID, a.PN_CODE as PRENAME_CODE, PER_NAME, PER_SURNAME, min(POH_EFFECTIVEDATE) as EFFECTIVEDATE, 
										b.ORG_ID, a.PER_SALARY, a.PER_STARTDATE, a.$search_pos
										$search_field 
						from 		(
											(
												(
													PER_PERSONAL a
													inner join $search_position_table on ($search_position_condition)
												) left join PER_POSITIONHIS c on (a.PER_ID=c.PER_ID and a.LEVEL_NO=c.LEVEL_NO)
											) inner join $search_perline_table on ($search_perline_condition)
										) left join PER_EDUCATE e on (a.PER_ID=e.PER_ID)
						where		PER_TYPE=$PER_TYPE and PER_STATUS=1
										$search_condition
						group by	a.PER_ID, a.PN_CODE, PER_NAME, PER_SURNAME, c.PL_CODE, 	
										b.ORG_ID, a.PER_SALARY, a.PER_STARTDATE, a.$search_pos
										$search_field
						$having_clause ";
}elseif($DPISDB=="oci8"){
	$cmd = " 	select 		a.PER_ID, a.PN_CODE as PRENAME_CODE, PER_NAME, PER_SURNAME, min(POH_EFFECTIVEDATE) as EFFECTIVEDATE, 
										b.ORG_ID, a.PER_SALARY, a.PER_STARTDATE, a.$search_pos
										$search_field 
						from 		PER_PERSONAL a, PER_POSITIONHIS c, PER_EDUCATE e,
										$search_position_table, $search_perline_table 
						where		PER_TYPE=$PER_TYPE and PER_STATUS=1 and a.PER_ID=c.PER_ID(+) and a.LEVEL_NO=c.LEVEL_NO(+)
										and $search_position_condition and $search_perline_condition and a.PER_ID=e.PER_ID(+)
										$search_condition												
						group by	a.PER_ID, a.PN_CODE, PER_NAME, PER_SURNAME, c.PL_CODE, 	
										b.ORG_ID, a.PER_SALARY, a.PER_STARTDATE, a.$search_pos
										$search_field
						$having_clause ";
}elseif($DPISDB=="mysql"){
	$cmd = " 	select 		a.PER_ID, a.PN_CODE as PRENAME_CODE, PER_NAME, PER_SURNAME, min(POH_EFFECTIVEDATE) as EFFECTIVEDATE, 
										b.ORG_ID, a.PER_SALARY, a.PER_STARTDATE, a.$search_pos
										$search_field 
						from 		(
											(
												(
													PER_PERSONAL a
													inner join $search_position_table on ($search_position_condition)
												) left join PER_POSITIONHIS c on (a.PER_ID=c.PER_ID and a.LEVEL_NO=c.LEVEL_NO)
											) inner join $search_perline_table on ($search_perline_condition)
										) left join PER_EDUCATE e on (a.PER_ID=e.PER_ID)
						where		PER_TYPE=$PER_TYPE and PER_STATUS=1
										$search_condition
						group by	a.PER_ID, a.PN_CODE, PER_NAME, PER_SURNAME, c.PL_CODE, 	
										b.ORG_ID, a.PER_SALARY, a.PER_STARTDATE, a.$search_pos
										$search_field
						$having_clause ";
} // end if
$count_data = $db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
//echo "search PER_ID:$cmd<br>";
while ($data = $db_dpis->get_array()) {
	$arr_per_id[] = $data[PER_ID];
}
$temp_per_id = implode(", ", $arr_per_id);
$search_per_id = " a.PER_ID in ($temp_per_id) and ";
****************/

/*Release 5.2.1.6 Begin*/
$cmd_search_count="select * from (
					   select 			rownum rnum, q1.*
					   from ( 
							select 		a.PER_ID, a.PN_CODE as PRENAME_CODE, PER_NAME, PER_SURNAME, a.PER_TYPE, 
											b.ORG_ID, a.PER_SALARY, a.PER_STARTDATE, a.$search_pos
											$search_field 
							from 		PER_PERSONAL a, PER_EDUCATE e,
											$search_position_table, $search_perline_table 
							where		PER_TYPE=$PER_TYPE and PER_STATUS=1
											and $search_position_condition and $search_perline_condition and a.PER_ID=e.PER_ID(+)
											$search_condition												
							group by	a.PER_ID, a.PN_CODE, PER_NAME, PER_SURNAME, a.PER_TYPE, 
											b.ORG_ID, a.PER_SALARY, a.PER_STARTDATE, a.$search_pos
											$search_field 
							$having_clause
							order by	a.LEVEL_NO desc, a.PER_SALARY desc,  a.PER_STARTDATE 
					   )  q1
				)";
$count_data = $db_dpis->send_cmd($cmd_search_count);

/*Release 5.2.1.6 End*/
$total_page = ceil( $count_data / $data_per_page );
$page_link = create_link_page($total_page, $current_page);
$limit_data = "";	
//echo 'ก่อนคลิกค้นหา Debug=>['.$total_page.' = ceil( '.$count_data.' / '.$data_per_page.' )]';
if($DPISDB=="odbc"){
	$select_top = ($current_page==$total_page)?($count_data - ($data_per_page * ($current_page - 1))):$data_per_page;
	$cmd_search = "	select	*
						from	(
									select	top $select_top *
									from	(
												select 		top ". ($data_per_page * $current_page) ."
																a.PER_ID, a.PN_CODE as PRENAME_CODE, PER_NAME, PER_SURNAME, a.PER_TYPE, 
																b.ORG_ID, a.PER_SALARY, a.PER_STARTDATE, a.$search_pos
																$search_field 
												from 		(
																		(
																			PER_PERSONAL a
																			inner join $search_position_table on ($search_position_condition)
																	) inner join $search_perline_table on ($search_perline_condition)
																) left join PER_EDUCATE e on (a.PER_ID=e.PER_ID)
												where		PER_TYPE=$PER_TYPE and PER_STATUS=1
																$search_condition		
												group by	a.PER_ID, a.PN_CODE, PER_NAME, PER_SURNAME, a.PER_TYPE, 	
																b.ORG_ID, a.PER_SALARY, a.PER_STARTDATE, a.$search_pos
																$search_field 
												$having_clause
												order by	a.LEVEL_NO desc, a.PER_SALARY desc,  a.PER_STARTDATE
												)
									order by a.LEVEL_NO, EFFECTIVEDATE desc, a.PER_SALARY,  a.PER_STARTDATE desc
									)
						order by a.LEVEL_NO desc, EFFECTIVEDATE, a.PER_SALARY desc,  a.PER_STARTDATE ";
}elseif($DPISDB=="oci8"){	
	$rec_start = (($current_page-1) * $data_per_page) + 1;
	$rec_end = ($current_page > 1)? ($current_page * $data_per_page) : $data_per_page;

/***
	$cmd_search  = "select * from (
					   select 			rownum rnum, q1.*
					   from ( 
							select 		a.PER_ID, a.PN_CODE as PRENAME_CODE, PER_NAME, PER_SURNAME, a.PER_TYPE, min(POH_EFFECTIVEDATE) as EFFECTIVEDATE, 
											b.ORG_ID, a.PER_SALARY, a.PER_STARTDATE, a.$search_pos
											$search_field 
							from 		PER_PERSONAL a, PER_POSITIONHIS c, PER_EDUCATE e,
											$search_position_table, $search_perline_table 
							where		PER_TYPE=$PER_TYPE and PER_STATUS=1 and a.PER_ID=c.PER_ID(+) and a.LEVEL_NO=c.LEVEL_NO(+)
											and $search_position_condition and $search_perline_condition and a.PER_ID=e.PER_ID(+)
											$search_condition												
							group by	a.PER_ID, a.PN_CODE, PER_NAME, PER_SURNAME, a.PER_TYPE, c.PL_CODE, 	
											b.ORG_ID, a.PER_SALARY, a.PER_STARTDATE, a.$search_pos
											$search_field 
							$having_clause
							order by	a.LEVEL_NO desc, min(POH_EFFECTIVEDATE), a.PER_SALARY desc,  a.PER_STARTDATE 
					   )  q1
				) where rnum between $rec_start and $rec_end  ";
***/
        /*Release 5.2.1.6 เพ่มการนับ*/
        $cmd_search_count="select * from (
					   select 			rownum rnum, q1.*
					   from ( 
							select 		a.PER_ID, a.PN_CODE as PRENAME_CODE, PER_NAME, PER_SURNAME, a.PER_TYPE, 
											b.ORG_ID, a.PER_SALARY, a.PER_STARTDATE, a.$search_pos
											$search_field 
							from 		PER_PERSONAL a, PER_EDUCATE e,
											$search_position_table, $search_perline_table 
							where		PER_TYPE=$PER_TYPE and PER_STATUS=1
											and $search_position_condition and $search_perline_condition and a.PER_ID=e.PER_ID(+)
											$search_condition												
							group by	a.PER_ID, a.PN_CODE, PER_NAME, PER_SURNAME, a.PER_TYPE, 
											b.ORG_ID, a.PER_SALARY, a.PER_STARTDATE, a.$search_pos
											$search_field 
							$having_clause
							order by	a.LEVEL_NO desc, a.PER_SALARY desc,  a.PER_STARTDATE 
					   )  q1
				)";
       /*Release 5.2.1.6*/ 
        
	$cmd_search  = "select * from (
					   select 			rownum rnum, q1.*
					   from ( 
							select 		a.PER_ID, a.PN_CODE as PRENAME_CODE, PER_NAME, PER_SURNAME, a.PER_TYPE, 
											b.ORG_ID, a.PER_SALARY, a.PER_STARTDATE, a.$search_pos
											$search_field 
							from 		PER_PERSONAL a, PER_EDUCATE e,
											$search_position_table, $search_perline_table 
							where		PER_TYPE=$PER_TYPE and PER_STATUS=1
											and $search_position_condition and $search_perline_condition and a.PER_ID=e.PER_ID(+)
											$search_condition												
							group by	a.PER_ID, a.PN_CODE, PER_NAME, PER_SURNAME, a.PER_TYPE, 
											b.ORG_ID, a.PER_SALARY, a.PER_STARTDATE, a.$search_pos
											$search_field 
							$having_clause
							order by	a.LEVEL_NO desc, a.PER_SALARY desc,  a.PER_STARTDATE 
					   )  q1
				) where rnum between $rec_start and $rec_end  ";
        //echo '<pre>'.$cmd_search;
}elseif($DPISDB=="mysql"){
	$cmd_search  = "	select	a.PER_ID, a.PN_CODE as PRENAME_CODE, PER_NAME, PER_SURNAME, a.PER_TYPE, 
																b.ORG_ID, a.PER_SALARY, a.PER_STARTDATE, a.$search_pos
																$search_field 
												from 		(
																		(
																			PER_PERSONAL a
																			inner join $search_position_table on ($search_position_condition)
																	) inner join $search_perline_table on ($search_perline_condition)
																) left join PER_EDUCATE e on (a.PER_ID=e.PER_ID)
												where		PER_TYPE=$PER_TYPE and PER_STATUS=1
																$search_condition		
												group by	a.PER_ID, a.PN_CODE, PER_NAME, PER_SURNAME, a.PER_TYPE, 	
																b.ORG_ID, a.PER_SALARY, a.PER_STARTDATE, a.$search_pos
																$search_field 
																$having_clause
												order by	a.LEVEL_NO desc, a.PER_SALARY desc,  a.PER_STARTDATE ";
} 	// end if
//$count_page_data = $db_dpis->send_cmd($cmd_show);
//$db_dpis->show_error();
//echo "in-select:$cmd_show<br>";
?>