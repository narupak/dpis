<?php
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis_cnt = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        $db_dpismain = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($PER_TYPE==1){ 
            $POS_NO_FROM=" LEFT JOIN PER_POSITION b ON (b.POS_ID=a.POS_ID)";
            $pl_field_his = "PL_CODE ";
            $posno_field_his = "POS_NO ";
            $field_position = "b.PL_CODE, b.PM_CODE, b.PT_CODE, b.POS_NO"; 	
            $group_by = "b.PL_CODE, b.PM_CODE, b.PT_CODE, b.POS_NO";
        }elseif($PER_TYPE==2){ 
            $POS_NO_FROM=" LEFT JOIN PER_POS_EMP b ON (b.POEM_ID=a.POEM_ID)";
            $pl_field_his = "PN_CODE ";
            $posno_field_his = "POEM_NO ";
            $field_position = "b.PN_CODE as PL_CODE, b.POEM_NO as POS_NO"; 
            $group_by = "b.PN_CODE, b.POEM_NO"; 
        }elseif($PER_TYPE==3){ 
            $POS_NO_FROM=" LEFT JOIN PER_POS_EMPSER b ON (b.POEMS_ID=a.POEMS_ID)";
            $pl_field_his = "EP_CODE ";
            $posno_field_his = "POEMS_NO ";
            $field_position = "b.EP_CODE as PL_CODE, b.POEMS_NO as POS_NO";  
            $group_by = "b.EP_CODE, b.POEMS_NO";
        }elseif($PER_TYPE==4){ 
            $POS_NO_FROM="  LEFT JOIN PER_POS_TEMP b ON (b.POT_ID=a.POT_ID)";
            $pl_field_his = "TP_CODE ";
            $posno_field_his = "POT_NO ";
            $field_position = "b.TP_CODE as PL_CODE, b.POT_NO as POS_NO";  
            $group_by = "b.TP_CODE, b.POT_NO";
        }
        if (trim($PER_TYPE))  $arr_search_condition[] = "(a.PER_TYPE=$PER_TYPE)";
        if (trim($PL_PN_CODE)) {
            if ($PER_TYPE == 1) 		$arr_search_condition[] = "( b.PL_CODE='$PL_PN_CODE' )";
            elseif ($PER_TYPE == 2) 	$arr_search_condition[] = "( b.PN_CODE='$PL_PN_CODE' )";	
            elseif ($PER_TYPE == 3) 	$arr_search_condition[] = "( b.EP_CODE='$PL_PN_CODE' )";	
            elseif ($PER_TYPE == 4) 	$arr_search_condition[] = "( b.TP_CODE='$PL_PN_CODE' )";	
        }
        if (trim($PM_CODE))  $arr_search_condition[] = "(b.PM_CODE=$PM_CODE)";
        if(trim($LEVEL_START)) $arr_search_condition[] = "(a.LEVEL_NO >= '$LEVEL_START')";
        if(trim($LEVEL_END)) $arr_search_condition[] = "(a.LEVEL_NO <= '$LEVEL_END')";
        if(trim($ORG_ID)) {				
            if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID=$ORG_ID)";
            if($select_org_structure==1) $arr_search_condition[] = "(a.ORG_ID=$ORG_ID)";
        }elseif($DEPARTMENT_ID){
            $arr_search_condition[] = "(a.DEPARTMENT_ID=$DEPARTMENT_ID)";
        }elseif($MINISTRY_ID){
            $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
            $db_dpis->send_cmd($cmd);
            while($data = $db_dpis->get_array()) $arr_department_all[] = $data[ORG_ID];
            $arr_department = array_unique($arr_department_all);
            $arr_search_condition[] = "(a.DEPARTMENT_ID  in (". implode(",", $arr_department) ."))";
        }elseif(trim($PV_CODE)){
            $cmd = " select 	ORG_ID
                    from   	PER_ORG
                    where  	OL_CODE='03' and PV_CODE='$PV_CODE'
                    order by ORG_ID ";
            $db_dpis->send_cmd($cmd);
            while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
            $arr_search_condition[] = "(b.ORG_ID in (". implode(",", $arr_org) ."))";
        } 
        if ($CHECK_DATE) $tmp_date = save_date($CHECK_DATE);
        else $tmp_date = date("Y-m-d");

        if ($search_year11 || $search_year12 || $search_year31 || $search_year32 || $search_year41 || $search_year42 || $search_year51 || $search_year52 ) {
            if ($search_year11 || $search_year12){
                if($search_year11 == $search_year12) $search_year12 += 1;				
                if($search_year11){ $arr_having_condition[] = "(j.POSITION_YEARS >= '$search_year11')"; } 
                if($search_year12){ $arr_having_condition[] = "(j.POSITION_YEARS <= '$search_year12')"; } 
            } 
            if ($search_year31 || $search_year32){
                if($search_year31 == $search_year32) $search_year32 += 1;				
                if($search_year31){ $arr_having_condition[] = "(l.LEVEL_YEARS >= '$search_year31')"; }
                if($search_year32){ $arr_having_condition[] = "(l.LEVEL_YEARS <= '$search_year32')"; }
            } 
            if ($search_year41 || $search_year42){
                if($search_year41 == $search_year42) $search_year42 += 1;				
                if($search_year41){ $arr_having_condition[] = "(m.POSNO_YEARS >= '$search_year41')"; }
                if($search_year42){ $arr_having_condition[] = "(m.POSNO_YEARS <= '$search_year42')"; }
            }
            if ($search_year51 || $search_year52){
                if($search_year51 == $search_year52) $search_year52 += 1;
                if($search_year51){ $arr_having_condition[] = "(n.ORG_YEARS >= '$search_year51')"; }
                if($search_year52){ $arr_having_condition[] = "(n.ORG_YEARS <= '$search_year52')"; }
            }
        }

        if ($search_year21 || $search_year22){
            if($search_year21 == $search_year22) $search_year22 += 1;
            if($search_year21){ $arr_having_condition[] = "(k.START_YEARS >= '$search_year21')"; }
            if($search_year22){ $arr_having_condition[] = "(k.START_YEARS <= '$search_year22')"; }
        }

        $having_clause = $search_condition = $search_from = "";
        if(count($arr_search_condition)) 	$search_condition  = " and ".implode(" and ", $arr_search_condition);
        if(count($arr_having_condition)) 	$having_clause 	= " having " . implode(" and ", $arr_having_condition);

	$company_name = "";
	if ($BKK_FLAG==1 || $ISCS_FLAG==1)
		$report_title = "�ͺ��������Ţ���Ҫ���������Ѻ����¹";
	else
		$report_title = "�ͺ��������Ţ���Ҫ���/�١��ҧ������Ѻ����¹";
	$report_code = "P0303";
	
	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//	
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//
	
	function print_header(){
		global $worksheet, $xlsRow, $MINISTRY_TITLE, $DEPARTMENT_TITLE, $ORG_TITLE, $ORG_TITLE1, $ORG_TITLE2;

		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 30);
		$worksheet->set_column(2, 2, 40);
		$worksheet->set_column(3, 3, 20);
		$worksheet->set_column(4, 4, 25);
		$worksheet->set_column(5, 5, 30);
		$worksheet->set_column(6, 6, 20);
		$worksheet->set_column(7, 7, 20);
		$worksheet->set_column(8, 8, 25);
		$worksheet->set_column(9, 9, 25);
		$worksheet->set_column(10, 10, 25);
		$worksheet->set_column(11,11, 25);
		$worksheet->set_column(12, 12, 25);
                
                $worksheet->set_column(13, 13, 25);
                $worksheet->set_column(14, 14, 25);
                $worksheet->set_column(15, 15, 25);
                $worksheet->set_column(16, 16, 25);
                $worksheet->set_column(17, 17, 25);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "�ӴѺ���", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "����-ʡ��", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "���˹�/�дѺ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "$MINISTRY_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "$DEPARTMENT_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 6, "$ORG_TITLE1", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 7, "$ORG_TITLE2", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 8, "�������ҷ���ç���˹�", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 9, "�ѹ����������ҷ���ç���˹�", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 10, "�����Ҫ���(��)", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 11, "�ѹ��������Ҫ���", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 12, "�������ҷ��������дѺ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 13, "�ѹ����������ҷ��������дѺ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 14, "�������ҷ��������Ţ�����˹�", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 15, "�ѹ����������ҷ��������Ţ�����˹�", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 16, "�������ҷ�������$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 17, "�ѹ����������ҷ�������$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function		
		
	if($order_by==1){	//����-ʡ��
		$order_str = "ORDER BY PER_NAME $SortType[$order_by], PER_SURNAME $SortType[$order_by]";
  	}elseif($order_by==2) {	//���˹�
		$order_str = "ORDER BY LEVEL_NO  ".$SortType[$order_by];
  	} elseif($order_by==3) {	//�ѧ�Ѵ
		$order_str = "ORDER BY ORG_ID ".$SortType[$order_by];
	}
        
        $strSQLMAIN = "
            WITH LIST_PERID as (
                select per_id from per_personal
                where per_status=1 and PER_TYPE = $PER_TYPE
                -- and per_id in (16216,14063,14006)
            ),  
            TB_POHIS_NOT_FORMAT AS (
                SELECT p.* FROM (  
                      SELECT PER_ID,POH_ID,
                        CASE WHEN LENGTH(TRIM(POH_EFFECTIVEDATE)) > 9 THEN SUBSTR(POH_EFFECTIVEDATE,0,4) ELSE '' END as y, 
                        CASE WHEN LENGTH(TRIM(POH_EFFECTIVEDATE)) > 9 THEN SUBSTR(POH_EFFECTIVEDATE,6,2) ELSE '' END as m ,
                        CASE WHEN LENGTH(TRIM(POH_EFFECTIVEDATE)) > 9 THEN SUBSTR(POH_EFFECTIVEDATE,9,2) ELSE '' END as d, 
                        LENGTH(TRIM(POH_EFFECTIVEDATE))  AS len ,
                        POH_EFFECTIVEDATE 
                      from PER_POSITIONHIS 
                ) p
                  WHERE 
                  p.d NOT IN ('01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31')
                  OR p.m not IN ('01','02','03','04','05','06','07','08','09','10','11','12')
                  OR INSTR(p.y,'-') > 0
                  OR INSTR(p.y,'/') > 0
                  OR len < 9
                  OR TRIM(POH_EFFECTIVEDATE) IS NULL
                  OR TRIM(POH_EFFECTIVEDATE) = '-'
            ),
            TB_PORSON_NOT_FORMAT AS (
                SELECT p.* FROM (  
                      SELECT PER_ID,
                        CASE WHEN LENGTH(TRIM(PER_STARTDATE)) > 9 THEN SUBSTR(PER_STARTDATE,0,4) ELSE '' END as y ,
                        CASE WHEN LENGTH(TRIM(PER_STARTDATE)) > 9 THEN SUBSTR(PER_STARTDATE,6,2) ELSE '' END as m ,
                        CASE WHEN LENGTH(TRIM(PER_STARTDATE)) > 9 THEN SUBSTR(PER_STARTDATE,9,2) ELSE '' END as d , 
                        LENGTH(TRIM(PER_STARTDATE))  AS len , PER_STARTDATE 
                      from PER_PERSONAL 
                ) p 
                  WHERE 
                  p.d NOT IN ('01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31')
                  OR p.m not IN ('01','02','03','04','05','06','07','08','09','10','11','12')
                  OR INSTR(p.y,'-') > 0
                  OR INSTR(p.y,'/') > 0
                  OR len < 9
                  OR TRIM(PER_STARTDATE) IS NULL
            ),
            TB_MAX_POH_EFF as (
              select PER_ID, POH_ORG3, POH_EFFECTIVEDATE ,$pl_field_his ,LEVEL_NO , POH_POS_NO , POH_ORG2
                from PER_POSITIONHIS p
              where PER_ID in (select PER_ID from LIST_PERID)
                and POH_EFFECTIVEDATE=(select max(POH_EFFECTIVEDATE) from PER_POSITIONHIS x where x.PER_ID=p.PER_ID)
                and p.POH_LAST_POSITION='Y'
            ),
            OD_TX_ORG_DATE as (
              select p.* , (select max(poh_effectivedate) from per_positionhis x where x.per_id=p.per_id and x.poh_org3<>p.poh_org3 and x.poh_effectivedate < p.poh_effectivedate) od
              from TB_MAX_POH_EFF p
            ),
            TB_ORG_HIS as (
              select x.POH_ID ,x.PER_ID ,x.POH_EFFECTIVEDATE ,x.POH_ORG3,x.$pl_field_his ,x.LEVEL_NO , x.POH_POS_NO , x.POH_ORG2 ,'ORG' as TYPE_DATAS
                from per_positionhis x
              where x.per_id in (select per_id from LIST_PERID)
                and poh_effectivedate=(select min(poh_effectivedate) from per_positionhis 
                p where p.per_id=x.per_id and ((select od from OD_TX_ORG_DATE o where o.per_id=p.per_id ) is null or p.poh_effectivedate > (select od from OD_TX_ORG_DATE o where o.per_id=p.per_id )))
                and poh_org3=(select poh_org3 from TB_MAX_POH_EFF t where t.per_id=x.per_id)
            ),
            OD_TX_PLCODE_DATE as (
              select p.* , (select max(poh_effectivedate) from per_positionhis x where x.per_id=p.per_id and x.$pl_field_his<>p.$pl_field_his and x.poh_effectivedate < p.poh_effectivedate) od
              from TB_MAX_POH_EFF p
            ),
            TB_PLCODE_HIS as (
              select x.POH_ID ,x.PER_ID ,x.POH_EFFECTIVEDATE ,x.POH_ORG3,x.$pl_field_his ,x.LEVEL_NO , x.POH_POS_NO , x.POH_ORG2 ,'PLCODE' as TYPE_DATAS
                from per_positionhis x
              where x.per_id in (select per_id from LIST_PERID)
                and poh_effectivedate=(select min(poh_effectivedate) from per_positionhis 
                p where p.per_id=x.per_id and ((select od from OD_TX_PLCODE_DATE o where o.per_id=p.per_id ) is null or p.poh_effectivedate > (select od from OD_TX_PLCODE_DATE o where o.per_id=p.per_id )))
                and $pl_field_his=(select $pl_field_his from TB_MAX_POH_EFF t where t.per_id=x.per_id)
            ),
            OD_TX_LEVEL_DATE as (
              select p.* , (select max(poh_effectivedate) from per_positionhis x where x.per_id=p.per_id and x.LEVEL_NO<>p.LEVEL_NO and x.poh_effectivedate < p.poh_effectivedate) od
              from TB_MAX_POH_EFF p
            ),
            TB_LEVEL_HIS as (
              select x.POH_ID ,x.PER_ID ,x.POH_EFFECTIVEDATE ,x.POH_ORG3,x.$pl_field_his ,x.LEVEL_NO , x.POH_POS_NO , x.POH_ORG2 ,'LEVEL' as TYPE_DATAS
                from per_positionhis x
              where x.per_id in (select per_id from LIST_PERID)
                and poh_effectivedate=(select min(poh_effectivedate) from per_positionhis 
                p where p.per_id=x.per_id and ((select od from OD_TX_LEVEL_DATE o where o.per_id=p.per_id ) is null or p.poh_effectivedate > (select od from OD_TX_LEVEL_DATE o where o.per_id=p.per_id )))
                and LEVEL_NO=(select LEVEL_NO from TB_MAX_POH_EFF t where t.per_id=x.per_id)
            ), 
            OD_TX_POSNO_DATE as (
              select p.* , (select max(poh_effectivedate) from per_positionhis x where x.per_id=p.per_id and x.POH_POS_NO<>p.POH_POS_NO and x.poh_effectivedate < p.poh_effectivedate) od
              from TB_MAX_POH_EFF p
            ),
            TB_POSNO_HIS as (
              select  x.POH_ID ,x.PER_ID ,x.POH_EFFECTIVEDATE ,x.POH_ORG3,x.$pl_field_his ,x.LEVEL_NO , x.POH_POS_NO , x.POH_ORG2 ,'POSNO' as TYPE_DATAS
                from per_positionhis x
              where x.per_id in (select per_id from LIST_PERID)
                and poh_effectivedate=(select min(poh_effectivedate) from per_positionhis 
                p where p.per_id=x.per_id and ((select od from OD_TX_POSNO_DATE o where o.per_id=p.per_id ) is null or p.poh_effectivedate > (select od from OD_TX_POSNO_DATE o where o.per_id=p.per_id )))
                and POH_POS_NO=(select POH_POS_NO from TB_MAX_POH_EFF t where t.per_id=x.per_id) and POH_ORG2=(select POH_ORG2 from TB_MAX_POH_EFF t where t.per_id=x.per_id)
            ),
            ALL_TB as (
                  SELECT * from TB_ORG_HIS UNION ALL
                  SELECT * FROM TB_PLCODE_HIS UNION ALL
                  SELECT * FROM TB_LEVEL_HIS  UNION ALL
                  SELECT * FROM TB_POSNO_HIS
            ),
            PO_HIS AS (
                select  a.PER_ID, a.POH_EFFECTIVEDATE , a.$pl_field_his as PL_CODE , a.LEVEL_NO , a.POH_POS_NO , a.POH_ORG2 , a.POH_ORG3, a.TYPE_DATAS ,
                        trunc(months_between(to_date('".$tmp_date."','YYYY-MM-DD'),to_date(a.POH_EFFECTIVEDATE,'YYYY-MM-DD'))/12) YEARS,
                        trunc(mod(months_between(to_date('".$tmp_date."','YYYY-MM-DD'),to_date(a.POH_EFFECTIVEDATE,'YYYY-MM-DD')),12)) MONTHS,
                        trunc(to_date('".$tmp_date."','YYYY-MM-DD')-(add_months(to_date(a.POH_EFFECTIVEDATE,'YYYY-MM-DD'),
                        trunc(months_between(to_date('".$tmp_date."','YYYY-MM-DD'),to_date(a.POH_EFFECTIVEDATE,'YYYY-MM-DD'))/12)*12+
                        trunc(mod(months_between(to_date('".$tmp_date."','YYYY-MM-DD'),to_date(a.POH_EFFECTIVEDATE,'YYYY-MM-DD')),12))))) DAYS 
                  from	ALL_TB a 
                  WHERE a.POH_ID NOT IN (SELECT POH_ID FROM TB_POHIS_NOT_FORMAT)
            ),
            TB_POSITION_TIME AS (
                select 
                      PER_ID , POH_EFFECTIVEDATE AS POSITION_EFFECTIVEDATE, PL_CODE ,
                      YEARS AS POSITION_YEARS,
                      MONTHS AS POSITION_MONTHS,
                      DAYS AS POSITION_DAYS,
                      CASE WHEN YEARS <> 0 AND YEARS IS NOT NULL THEN  YEARS||' �� ' ELSE '' END||
                      CASE WHEN MONTHS <> 0 AND MONTHS IS NOT NULL THEN MONTHS||' ��͹ ' ELSE '' END|| 
                      CASE WHEN DAYS <> 0 AND DAYS IS NOT NULL THEN DAYS||' �ѹ' ELSE '' END  AS POSITION_TIME 
                from	PO_HIS WHERE TYPE_DATAS='PLCODE'
            ),
            TB_LEVEL_TIME AS (
                select
                      PER_ID,	POH_EFFECTIVEDATE AS LEVEL_EFFECTIVEDATE, LEVEL_NO , 
                      YEARS AS  LEVEL_YEARS,
                      MONTHS AS LEVEL_MONTHS,
                      DAYS AS LEVEL_DAYS ,
                      CASE WHEN YEARS <> 0 AND YEARS IS NOT NULL THEN  YEARS||' �� ' ELSE '' END||
                      CASE WHEN MONTHS <> 0 AND MONTHS IS NOT NULL THEN MONTHS||' ��͹ ' ELSE '' END|| 
                      CASE WHEN DAYS <> 0 AND DAYS IS NOT NULL THEN DAYS||' �ѹ' ELSE '' END  AS LEVEL_TIME
                 from	PO_HIS WHERE TYPE_DATAS='LEVEL'
            ), 
            TB_POSNO_TIME AS (
                select  
                      PER_ID,	POH_EFFECTIVEDATE AS POSNO_EFFECTIVEDATE ,POH_POS_NO as POSNO_POS_NO , POH_ORG2 AS POSNO_ORG2 ,
                      YEARS AS  POSNO_YEARS,
                      MONTHS AS POSNO_MONTHS,
                      DAYS AS POSNO_DAYS ,
                      CASE WHEN YEARS <> 0 AND YEARS IS NOT NULL THEN  YEARS||' �� ' ELSE '' END||
                      CASE WHEN MONTHS <> 0 AND MONTHS IS NOT NULL THEN MONTHS||' ��͹ ' ELSE '' END|| 
                      CASE WHEN DAYS <> 0 AND DAYS IS NOT NULL THEN DAYS||' �ѹ' ELSE '' END  AS POSNO_TIME
                from	PO_HIS WHERE TYPE_DATAS='POSNO'
            ),
            TB_ORG_TIME AS (
                SELECT 
                        PER_ID, POH_EFFECTIVEDATE AS ORG_EFFECTIVEDATE , POH_ORG3 AS ORG_ORG3 ,
                        YEARS AS  ORG_YEARS,
                        MONTHS AS ORG_MONTHS,
                        DAYS AS ORG_DAYS,
                        CASE WHEN YEARS <> 0 AND YEARS IS NOT NULL THEN  YEARS||' �� ' ELSE '' END||
                      CASE WHEN MONTHS <> 0 AND MONTHS IS NOT NULL THEN MONTHS||' ��͹ ' ELSE '' END|| 
                      CASE WHEN DAYS <> 0 AND DAYS IS NOT NULL THEN DAYS||' �ѹ' ELSE '' END  AS ORG_TIME
                 from	PO_HIS WHERE TYPE_DATAS='ORG'
            ),
            TB_OFFICER_TIME AS (
              SELECT p.*, 
                  CASE WHEN p.START_YEARS <> 0 AND p.START_YEARS IS NOT NULL THEN  p.START_YEARS||' �� ' ELSE '' END||
                  CASE WHEN p.START_MONTHS <> 0 AND p.START_MONTHS IS NOT NULL THEN p.START_MONTHS||' ��͹ ' ELSE '' END|| 
                  CASE WHEN p.START_DAYS <> 0 AND p.START_DAYS IS NOT NULL THEN p.START_DAYS||' �ѹ' ELSE '' END AS START_TIME
                FROM (
                  select  ROW_NUMBER() OVER (PARTITION BY a.PER_ID  ORDER BY a.PER_STARTDATE ASC) AS NUMROW ,a.PER_ID, a.PER_STARTDATE AS START_STARTDATE,
                        trunc(months_between(to_date('".$tmp_date."','YYYY-MM-DD'),to_date(SUBSTR(a.PER_STARTDATE,0,10),'YYYY-MM-DD'))/12) START_YEARS,
                        trunc(mod(months_between(to_date('".$tmp_date."','YYYY-MM-DD'),to_date(SUBSTR(a.PER_STARTDATE,0,10),'YYYY-MM-DD')),12)) START_MONTHS,
                        trunc(to_date('".$tmp_date."','YYYY-MM-DD')-(add_months(to_date(SUBSTR(a.PER_STARTDATE,0,10),'YYYY-MM-DD'),
                        trunc(months_between(to_date('".$tmp_date."','YYYY-MM-DD'),to_date(SUBSTR(a.PER_STARTDATE,0,10),'YYYY-MM-DD'))/12)*12+
                        trunc(mod(months_between(to_date('".$tmp_date."','YYYY-MM-DD'),to_date(SUBSTR(a.PER_STARTDATE,0,10),'YYYY-MM-DD')),12))))) START_DAYS 
                    from	PER_PERSONAL a 
                    WHERE a.PER_ID NOT IN (SELECT PER_ID FROM TB_PORSON_NOT_FORMAT)
               ) p 
            ),
            p AS (
                select  a.PER_ID, $field_position , a.PN_CODE, PER_NAME, PER_SURNAME, a.LEVEL_NO, a.PER_STARTDATE, b.ORG_ID, g.LEVEL_NAME, g.POSITION_LEVEL, a.DEPARTMENT_ID ,
                        k.START_YEARS,k.START_MONTHS,k.START_DAYS ,k.START_TIME,k.START_STARTDATE, 
                        j.POSITION_YEARS,j.POSITION_MONTHS,j.POSITION_DAYS ,j.POSITION_TIME , j.POSITION_EFFECTIVEDATE ,
                        l.LEVEL_YEARS,l.LEVEL_MONTHS,l.LEVEL_DAYS ,l.LEVEL_TIME , l.LEVEL_EFFECTIVEDATE,
                        m.POSNO_YEARS,m.POSNO_MONTHS,m.POSNO_DAYS ,m.POSNO_TIME , m.POSNO_EFFECTIVEDATE,
                        n.ORG_YEARS,n.ORG_MONTHS,n.ORG_DAYS ,n.ORG_TIME , n.ORG_EFFECTIVEDATE
                FROM  PER_PERSONAL a
                $POS_NO_FROM
                LEFT JOIN  PER_ORG c ON  (a.DEPARTMENT_ID=c.ORG_ID)
                LEFT JOIN  PER_ORG d ON  (b.ORG_ID=d.ORG_ID)
                LEFT JOIN  PER_LEVEL g ON  (a.LEVEL_NO=g.LEVEL_NO)
                LEFT JOIN  PER_ORG   h ON  (b.ORG_ID=h.ORG_ID)
                LEFT JOIN  TB_OFFICER_TIME k ON  (a.PER_ID=k.PER_ID)
                LEFT JOIN  TB_POSITION_TIME j ON  (a.PER_ID=j.PER_ID AND b.$pl_field_his=j.PL_CODE)
                LEFT JOIN  TB_LEVEL_TIME l ON  (a.PER_ID=l.PER_ID AND a.LEVEL_NO=l.LEVEL_NO)
                LEFT JOIN  TB_POSNO_TIME m ON  (a.PER_ID=m.PER_ID AND b.$posno_field_his=trim(m.POSNO_POS_NO) AND trim(c.ORG_NAME)=trim(m.POSNO_ORG2))
                LEFT JOIN  TB_ORG_TIME n ON  (a.PER_ID=n.PER_ID AND trim(d.ORG_NAME)=trim(n.ORG_ORG3))
                WHERE   PER_STATUS=1  $search_condition
                GROUP BY    a.PER_ID, $group_by , a.PN_CODE, PER_NAME, PER_SURNAME, a.LEVEL_NO, g.POSITION_LEVEL, g.LEVEL_NAME, a.PER_STARTDATE, 
                            b.ORG_ID, a.DEPARTMENT_ID,
                            k.START_YEARS,k.START_MONTHS,k.START_DAYS ,k.START_TIME ,k.START_STARTDATE , 
                            j.POSITION_YEARS, j.POSITION_MONTHS, j.POSITION_DAYS,j.POSITION_TIME , j.POSITION_EFFECTIVEDATE,
                            l.LEVEL_YEARS,l.LEVEL_MONTHS,l.LEVEL_DAYS ,l.LEVEL_TIME, l.LEVEL_EFFECTIVEDATE,
                            m.POSNO_YEARS,m.POSNO_MONTHS,m.POSNO_DAYS ,m.POSNO_TIME , m.POSNO_EFFECTIVEDATE,
                            n.ORG_YEARS,n.ORG_MONTHS,n.ORG_DAYS ,n.ORG_TIME , n.ORG_EFFECTIVEDATE
                $having_clause
            )  SELECT * FROM p 
        ";
        //echo "<pre>$strSQLMAIN";
        //die();
        $db_dpis_cnt->send_cmd($strSQLMAIN);
        $cnt_datap = $db_dpis_cnt->num_rows();
        $db_dpismain->send_cmd_fast($strSQLMAIN);
//	$db_dpis->show_error();
//	echo $cmd;
	$data_count = $data_row = 0;
	while($data = $db_dpismain->get_array()){
		$show_data = 0;
            $TMP_PER_ID = $data[PER_ID];
            $POS_NO = trim($data[POS_NO]);
            $ORG_ID = trim($data[ORG_ID]);
            if ($ORG_ID) {
                    $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
                    $db_dpis1->send_cmd($cmd);
                    $data1 = $db_dpis1->get_array();
                    $TMP_ORG_NAME = $data1[ORG_NAME];
            }
            $show_data = 1;
            if ($show_data==1) { 
                $data_row++;
                $TMP_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
                $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_DEPARTMENT_ID ";
                $db_dpis1->send_cmd($cmd);
                $data1 = $db_dpis1->get_array();
                $TMP_DEPARTMENT_NAME = $data1[ORG_NAME];
                $PN_CODE = trim($data[PN_CODE]);
                if ($PN_CODE) {
                    $cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
                    $db_dpis1->send_cmd($cmd);
                    $data1 = $db_dpis1->get_array();
                    $TMP_PN_NAME = $data1[PN_NAME];
                }
                $TMP_PER_NAME = $TMP_PN_NAME . trim($data[PER_NAME]) . " " . trim($data[PER_SURNAME]);
                $LEVEL_NO = trim($data[LEVEL_NO]);
                $LEVEL_NAME = trim($data[LEVEL_NAME]);
                $POSITION_LEVEL = $data[POSITION_LEVEL];
                if ($PER_TYPE == 1) {
                    $PL_CODE = trim($data[PL_CODE]);
                    $PM_CODE = trim($data[PM_CODE]);
                    $PT_CODE = trim($data[PT_CODE]);

                    $cmd = "	select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
                    $db_dpis1->send_cmd($cmd);
                    $data1 = $db_dpis1->get_array();
                    $PL_NAME = trim($data1[PL_NAME]);

                    $cmd = "	select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";
                    $db_dpis1->send_cmd($cmd);
                    $data1 = $db_dpis1->get_array();
                    $PM_NAME = trim($data1[PM_NAME]);

                    $cmd = "	select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
                    $db_dpis1->send_cmd($cmd);
                    $data1 = $db_dpis1->get_array();
                    $PT_NAME = trim($data1[PT_NAME]);
                    $POSITION_NAME = pl_name_format($PL_NAME, $PM_NAME, $PT_NAME, $LEVEL_NO);	
                } elseif ($PER_TYPE == 2) {
                    $PN_CODE = trim($data[PL_CODE]);

                    $cmd = "	select PN_NAME from PER_POS_NAME where PN_CODE='$PN_CODE' ";
                    $db_dpis1->send_cmd($cmd);
                    $data1 = $db_dpis1->get_array();
                    $POSITION_NAME = trim($data1[PN_NAME])." ".$LEVEL_NAME;
                } elseif ($PER_TYPE == 3) {
                    $EP_CODE = trim($data[PL_CODE]);

                    $cmd = "	select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE='$EP_CODE' ";
                    $db_dpis1->send_cmd($cmd);
                    $data1 = $db_dpis1->get_array();
                    $POSITION_NAME = trim($data1[EP_NAME])." ".$LEVEL_NAME;
                } elseif ($PER_TYPE == 4) {
                    $TP_CODE = trim($data[PL_CODE]);

                    $cmd = "	select TP_NAME from PER_TEMP_POS_NAME where TP_CODE='$TP_CODE' ";
                    $db_dpis1->send_cmd($cmd);
                    $data1 = $db_dpis1->get_array();
                    $POSITION_NAME = trim($data1[TP_NAME])." ".$LEVEL_NAME;
                }
		
		$ORG_ID = $data[ORG_ID];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME = $data2[ORG_NAME];
		//-----------------
	
		$ORG_ID_1 = $data[ORG_ID_1];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		//$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$ORG_1_NAME = $data2[ORG_NAME];
		
	//echo "ORG_ID_2 --  $data[ORG_ID_2]<br>";
		$ORG_ID_2 = $data[ORG_ID_2];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		//$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$ORG_2_NAME = $data2[ORG_NAME];
				
		$cmd = "select ORG_ID_REF from PER_ORG where ORG_ID= $ORG_ID";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$department = $data2[ORG_ID_REF];
		
		$cmd = "select ORG_NAME from PER_ORG where org_id = $department ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();				
		$DEPARTMENT_NAME = $data2[ORG_NAME];
		//----------------
		
		$cmd = "select ORG_ID_REF from PER_ORG where ORG_ID= $department";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ministry = $data2[ORG_ID_REF];
		
		$cmd = "select ORG_NAME from PER_ORG where org_id = $ministry ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();				
		$MINISTRY_NAME  = $data2[ORG_NAME];
				
		$TMP_POH_EFFECTIVEDATE = substr($data[POSITION_EFFECTIVEDATE], 0, 10);
                $TMP_POSITION_TIME = $data[POSITION_TIME];
                if ($TMP_POH_EFFECTIVEDATE) $TMP_POH_EFFECTIVEDATE = show_date_format($TMP_POH_EFFECTIVEDATE, 1);

                $TMP_PER_STARTDATE = substr($data[START_STARTDATE], 0, 10);
                $TMP_OFFICER_TIME = $data[START_TIME];
                if ($TMP_PER_STARTDATE) $TMP_PER_STARTDATE = show_date_format($TMP_PER_STARTDATE, 1);

                $TMP_POH_LEVELDATE = substr($data[LEVEL_EFFECTIVEDATE], 0, 10);
                $TMP_LEVEL_TIME = $data[LEVEL_TIME];
                if ($TMP_POH_LEVELDATE) $TMP_POH_LEVELDATE = show_date_format($TMP_POH_LEVELDATE, 1);

                $TMP_POH_POSNODATE = substr($data[POSNO_EFFECTIVEDATE], 0, 10);
                $TMP_POSNO_TIME = $data[POSNO_TIME];
                if ($TMP_POH_POSNODATE) $TMP_POH_POSNODATE = show_date_format($TMP_POH_POSNODATE, 1);

                $TMP_POH_ORGDATE = substr($data[ORG_EFFECTIVEDATE], 0, 10);
                $TMP_ORG_TIME = $data[ORG_TIME];
                if ($TMP_POH_ORGDATE) $TMP_POH_ORGDATE = show_date_format($TMP_POH_ORGDATE, 1);
                /*
                    $arr_content[$data_count][type] = "CONTENT";
                    $arr_content[$data_count][order] = $data_row;
                    $arr_content[$data_count][per_name] = $TMP_PER_NAME;
                    $arr_content[$data_count][position] = $POSITION_NAME;
                    $arr_content[$data_count][position_time] = $TMP_POSITION_TIME."\n".$TMP_POH_EFFECTIVEDATE;
                    $arr_content[$data_count][officer_time] = $TMP_OFFICER_TIME."\n".$TMP_PER_STARTDATE;
                    $arr_content[$data_count][level_time] = $TMP_LEVEL_TIME."\n".$TMP_POH_LEVELDATE;
                    $arr_content[$data_count][posno_time] = $TMP_POSNO_TIME."\n".$TMP_POH_POSNODATE;
                    $arr_content[$data_count][org_time] = $TMP_ORG_TIME."\n".$TMP_POH_ORGDATE;
                    $arr_content[$data_count][department_name] = $DEPARTMENT_NAME;
                    $arr_content[$data_count][ministry_name] = $MINISTRY_NAME;
                    $arr_content[$data_count][org_name] = $ORG_NAME;
                    $arr_content[$data_count][org_1_name] = $ORG_1_NAME;
                    $arr_content[$data_count][org_2_name] = $ORG_2_NAME;
		*/		
                $arr_content[$data_count][type] = "CONTENT";
                $arr_content[$data_count][order] = $data_row;
                $arr_content[$data_count][per_name] = $TMP_PER_NAME;
                $arr_content[$data_count][position] = $POSITION_NAME;
                $arr_content[$data_count][position_time] = $TMP_POSITION_TIME;
                $arr_content[$data_count][date_position_time] = $TMP_POH_EFFECTIVEDATE;
                $arr_content[$data_count][officer_time] = $TMP_OFFICER_TIME;
                $arr_content[$data_count][date_officer_time] = $TMP_PER_STARTDATE;
                $arr_content[$data_count][level_time] = $TMP_LEVEL_TIME;
                $arr_content[$data_count][date_level_time] = $TMP_POH_LEVELDATE;
                $arr_content[$data_count][posno_time] = $TMP_POSNO_TIME;
                $arr_content[$data_count][date_posno_time] = $TMP_POH_POSNODATE;
                $arr_content[$data_count][org_time] = $TMP_ORG_TIME;
                $arr_content[$data_count][date_org_time] = $TMP_POH_ORGDATE;
                $arr_content[$data_count][department_name] = $DEPARTMENT_NAME;
                $arr_content[$data_count][ministry_name] = $MINISTRY_NAME;
                $arr_content[$data_count][org_name] = $ORG_NAME;
                $arr_content[$data_count][org_1_name] = $ORG_1_NAME;
                $arr_content[$data_count][org_2_name] = $ORG_2_NAME;
		$data_count++;
		} // end if
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($cnt_datap){
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$xlsRow = $i;
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                        $worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if

		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
                        $worksheet->write($xlsRow, 13, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 14, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 15, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 16, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 17, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$ORDER = $arr_content[$data_count][order];
			$PER_NAME = $arr_content[$data_count][per_name];
			$POSITION = $arr_content[$data_count][position];
			$POSITION_TIME = $arr_content[$data_count][position_time];
                        $DATE_POSITION_TIME = $arr_content[$data_count][date_position_time];
			$OFFICER_TIME = $arr_content[$data_count][officer_time];
                        $DATE_OFFICER_TIME = $arr_content[$data_count][date_officer_time];
			$LEVEL_TIME = $arr_content[$data_count][level_time];
                        $DATE_LEVEL_TIME = $arr_content[$data_count][date_level_time];
			$POSNO_TIME = $arr_content[$data_count][posno_time];
                        $DATE_POSNO_TIME = $arr_content[$data_count][date_posno_time];
			$ORG_TIME = $arr_content[$data_count][org_time];
                        $DATE_ORG_TIME = $arr_content[$data_count][date_org_time];
			$DEPARTMENT_NAME = $arr_content[$data_count][department_name];
			$MINISTRY_NAME = $arr_content[$data_count][ministry_name];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$ORG_1_NAME = $arr_content[$data_count][org_1_name];
			$ORG_2_NAME = $arr_content[$data_count][org_2_name];
			
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "$PER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "$POSITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "$MINISTRY_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "$DEPARTMENT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 6, "$ORG_1_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 7, "$ORG_2_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 8, (($NUMBER_DISPLAY==2)?convert2thaidigit($POSITION_TIME):$POSITION_TIME), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
                        $worksheet->write($xlsRow, 9, (($NUMBER_DISPLAY==2)?convert2thaidigit($DATE_POSITION_TIME):$DATE_POSITION_TIME), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 10,(($NUMBER_DISPLAY==2)?convert2thaidigit($OFFICER_TIME):$OFFICER_TIME), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
                        $worksheet->write($xlsRow, 11,(($NUMBER_DISPLAY==2)?convert2thaidigit($DATE_OFFICER_TIME):$DATE_OFFICER_TIME), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 12,(($NUMBER_DISPLAY==2)?convert2thaidigit($LEVEL_TIME):$LEVEL_TIME), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
                        $worksheet->write($xlsRow, 13,(($NUMBER_DISPLAY==2)?convert2thaidigit($DATE_LEVEL_TIME):$DATE_LEVEL_TIME), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 14, (($NUMBER_DISPLAY==2)?convert2thaidigit($POSNO_TIME):$POSNO_TIME), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
                        $worksheet->write($xlsRow, 15, (($NUMBER_DISPLAY==2)?convert2thaidigit($DATE_POSNO_TIME):$DATE_POSNO_TIME), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 16, (($NUMBER_DISPLAY==2)?convert2thaidigit($ORG_TIME):$ORG_TIME), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
                        $worksheet->write($xlsRow, 17, (($NUMBER_DISPLAY==2)?convert2thaidigit($DATE_ORG_TIME):$DATE_ORG_TIME), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
		} // end for				
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ����բ����� *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                
                $worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"�ͺ��������Ţ���Ҫ���/�١��ҧ������Ѻ����¹.xls\"");
	header("Content-Disposition: inline; filename=\"�ͺ��������Ţ���Ҫ���/�١��ҧ������Ѻ����¹.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>