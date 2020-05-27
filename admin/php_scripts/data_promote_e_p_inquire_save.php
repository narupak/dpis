<?
unset($arr_search_condition, $search_condition);

// เงื่อนไขตามแต่ละ table
if ($table_select == "PER_PROMOTE_P") {
	$cmd = " delete from $table_select where PRO_DATE='$temp_pro_date' and POS_ID=$POS_POEM_ID and DEPARTMENT_ID=$ORG_ID_M2 ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	$cmd_save = " insert into $table_select 
				(PRO_DATE, POS_ID, PER_ID, PRO_SUMMARY, PER_CARDNO, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE) 
				values ";
} elseif ($table_select == "PER_PROMOTE_E") {
	$cmd = " delete from $table_select where PRO_DATE='$temp_pro_date' and POEM_ID=$POS_POEM_ID and DEPARTMENT_ID=$ORG_ID_M2 ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	$cmd_save = " insert into $table_select 
				(PRO_DATE, POEM_ID, PER_ID, PRO_SUMMARY, PER_CARDNO, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE) 
				values ";
}

// ========= start of condition =========เงื่อนไขต้องเหมือนกับที่เลือกมาจาก data_promote_e_p_inquire_query_search.php
if (trim($search_from))		$arr_search_condition[] = "(a.$search_pos = b.$search_pos)";

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
	$arr_search_condition[] = "(a.DEPARTMENT_ID = $ORG_ID_M2)";			//เพิ่มใหม่ตาม data_promote_e_p_inquire_query_search.php
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

if (trim($SAL_START))
	$arr_search_condition[] = "(PER_SALARY > $SAL_START)";	
if (trim($SAL_END))	
	$arr_search_condition[] = "(PER_SALARY < $SAL_END)";				
if (trim($EN_CODE)) {
	$EN_CODE = trim($EN_CODE);
	$arr_search_condition[] = "e.EN_CODE = '$EN_CODE'";
}

if (trim($YEAR_START)) {
	$effectivedate_min = date_adjust(date("Y-m-d"), "y", ($YEAR_START * -1));
	if($DPISDB=="odbc") $arr_having_condition[] = "(LEFT(trim(min(POH_EFFECTIVEDATE)), 10) <= '$effectivedate_min')";
	elseif($DPISDB=="oci8") $arr_having_condition[] = "(SUBSTR(trim(min(POH_EFFECTIVEDATE)), 1, 10) <= '$effectivedate_min')";
	elseif($DPISDB=="mysql") $arr_having_condition[] = "(LEFT(trim(min(POH_EFFECTIVEDATE)), 10) <= '$effectivedate_min')";
}
if (trim($YEAR_END))	{
	$effectivedate_max = date_adjust(date("Y-m-d"), "y", ($YEAR_END * -1));
	if($DPISDB=="odbc") $arr_having_condition[] = "(LEFT(trim(min(POH_EFFECTIVEDATE)), 10) >= '$effectivedate_max')";
	elseif($DPISDB=="oci8") $arr_having_condition[] = "(SUBSTR(trim(min(POH_EFFECTIVEDATE)), 1, 10) >= '$effectivedate_max')";
	elseif($DPISDB=="mysql") $arr_having_condition[] = "(LEFT(trim(min(POH_EFFECTIVEDATE)), 10) >= '$effectivedate_max')";
}	
if ($search_perline_table) $search_perline_table = " $search_perline_table";
$having_clause = $search_condition = "";
if ($arr_search_condition)		$search_condition = " and " . implode(" and ", $arr_search_condition);
if(count($arr_having_condition)) 	$having_clause = " having " . implode(" and ", $arr_having_condition);
// ========= end of condition =========

if($DPISDB=="odbc"){
	$cmd = "	select 		a.PER_ID, a.PN_CODE as PRENAME_CODE, PER_NAME, PER_SURNAME, a.PER_TYPE, min(POH_EFFECTIVEDATE) as EFFECTIVEDATE, 
										b.ORG_ID, a.PER_SALARY, a.PER_STARTDATE, a.PER_CARDNO, a.$search_pos
										$search_field 
						from 		PER_PERSONAL a, PER_POSITIONHIS c, PER_EDUCATE e
										$search_from , $search_perline_table
						where		PER_TYPE=$PER_TYPE and PER_STATUS=1 and a.PER_ID=c.PER_ID and a.PER_ID=e.PER_ID and 
										a.LEVEL_NO=c.LEVEL_NO 
										$search_where
										$search_condition		
						group by	a.PER_ID, a.PN_CODE, PER_NAME, PER_SURNAME, 	a.PER_TYPE, c.PL_CODE, 	
										b.ORG_ID, a.PER_SALARY, a.PER_STARTDATE, a.PER_CARDNO, a.$search_pos
										$search_field 
						$having_clause
						order by	a.LEVEL_NO desc, min(POH_EFFECTIVEDATE), a.PER_SALARY desc,  a.PER_STARTDATE 	";
				//order by	PER_NAME, PER_SURNAME, a.$search_pos, a.PER_ID 	";									

}elseif($DPISDB=="oci8"){
	$cmd = "  	select 		a.PER_ID, a.PN_CODE as PRENAME_CODE, PER_NAME, PER_SURNAME, a.PER_TYPE, min(POH_EFFECTIVEDATE) as EFFECTIVEDATE, 
										b.ORG_ID, a.PER_SALARY, a.PER_STARTDATE, a.PER_CARDNO, a.$search_pos
										$search_field 
						from 		PER_PERSONAL a, PER_POSITIONHIS c, PER_EDUCATE e
										$search_from , $search_perline_table
						where		PER_TYPE=$PER_TYPE and PER_STATUS=1 and a.PER_ID=c.PER_ID(+) and a.PER_ID=e.PER_ID(+) and 
										a.LEVEL_NO=c.LEVEL_NO(+)
										$search_where
										$search_condition												
						group by	a.PER_ID, a.PN_CODE, PER_NAME, PER_SURNAME, 	a.PER_TYPE, c.PL_CODE, 	
										b.ORG_ID, a.PER_SALARY, a.PER_STARTDATE, a.PER_CARDNO, a.$search_pos
										$search_field 
						$having_clause
						order by	a.LEVEL_NO desc, min(POH_EFFECTIVEDATE), a.PER_SALARY desc,  a.PER_STARTDATE ";
					//order by	PER_NAME, PER_SURNAME, a.$search_pos, a.PER_ID								
}elseif($DPISDB=="mysql"){
	$cmd = "	select 		a.PER_ID, a.PN_CODE as PRENAME_CODE, PER_NAME, PER_SURNAME, a.PER_TYPE, min(POH_EFFECTIVEDATE) as EFFECTIVEDATE, 
										b.ORG_ID, a.PER_SALARY, a.PER_STARTDATE, a.PER_CARDNO, a.$search_pos
										$search_field 
						from 		PER_PERSONAL a, PER_POSITIONHIS c, PER_EDUCATE e
										$search_from , $search_perline_table
						where		PER_TYPE=$PER_TYPE and PER_STATUS=1 and a.PER_ID=c.PER_ID and a.PER_ID=e.PER_ID and 
										a.LEVEL_NO=c.LEVEL_NO 
										$search_where
										$search_condition		
						group by	a.PER_ID, a.PN_CODE, PER_NAME, PER_SURNAME, a.PER_TYPE, c.PL_CODE, 	
										b.ORG_ID, a.PER_SALARY, a.PER_STARTDATE, a.PER_CARDNO, a.$search_pos
										$search_field 
						$having_clause
						order by	a.LEVEL_NO desc, min(POH_EFFECTIVEDATE), a.PER_SALARY desc,  a.PER_STARTDATE 	";
				//order by	PER_NAME, PER_SURNAME, a.$search_pos, a.PER_ID 	";		
} 	// end if
$cnt = $db_dpis1->send_cmd($cmd);
//$db_dpis1->show_error();
//echo "save :: $cmd (- $cnt -)<hr>";

while ($data1 = $db_dpis1->get_array()) {
	$TMP_PER_ID = $data1[PER_ID];
	$PER_CARDNO = trim($data1[PER_CARDNO])?	"'".$data1[PER_CARDNO]."'" : "NULL";		
//	if (trim($data1[POS_ID]))				$POS_POEM_ID = trim($data1[POS_ID]);
//	elseif (trim($data1[POEM_ID]))		$POS_POEM_ID =	trim($data1[POEM_ID]);
//	elseif (trim($data1[POEMS_ID]))		$POS_POEM_ID =	trim($data1[POEMS_ID]);

	// === INSERT DATA ===
	$cmd = $cmd_save . " ('$temp_pro_date', $POS_POEM_ID, $TMP_PER_ID, '$PRO_SUMMARY', $PER_CARDNO, $ORG_ID_M2, $SESS_USERID, '$UPDATE_DATE') ";
	$db_dpis2->send_cmd($cmd);
	//$db_dpis2->show_error();
	//echo "add/update-$cmd<hr>";

	insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลสอบถามข้าราชการที่มีคุณสมบัติได้เลื่อนตำแหน่ง [$ORG_ID_M2 : ".trim($tmp_pro_date)." : ".$POS_POEM_ID." : ".$TMP_PER_ID."]");	
	//$command ="SELECT";
}	//end while 

?>