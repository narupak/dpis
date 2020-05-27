<?
	ini_set("max_execution_time", "1800");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
	if(!trim($RPTORD_LIST)){ 
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		elseif($list_type=="PER_COUNTRY" && trim($search_pv_code)!="") $RPTORD_LIST = "PROVINCE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE", "CO_LEVEL", "PROVINCE"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_SEQ_NO, a.ORG_ID";
				elseif($select_org_structure==1) $select_list .= "e.ORG_SEQ_NO, d.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_SEQ_NO, a.ORG_ID";
				elseif($select_org_structure==1) $order_by .= "e.ORG_SEQ_NO, d.ORG_ID";

				$heading_name .= " สำนัก/กอง";
				break;
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "a.ORG_ID_1";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "a.ORG_ID_1";

				$heading_name .= " ฝ่าย";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "a.ORG_ID_2";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "a.ORG_ID_2";

				$heading_name .= " งาน";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.PL_CODE";

				$heading_name .= " สายงาน";
				break;
			case "CO_LEVEL" :
				if($select_list) $select_list .= ", ";
				if($DPISDB=="odbc") $select_list .= "a.CL_NAME, IIf(IsNull(c.LEVEL_NO_MIN), 0, CInt(c.LEVEL_NO_MIN))";
				elseif($DPISDB=="oci8") $select_list .= "a.CL_NAME, TO_NUMBER(c.LEVEL_NO_MIN)";
				elseif($DPISDB=="mysql") $select_list .= "a.CL_NAME, c.LEVEL_NO_MIN";
				
				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") $order_by .= "IIf(IsNull(c.LEVEL_NO_MIN), 0, CInt(c.LEVEL_NO_MIN))";
				elseif($DPISDB=="oci8") $order_by .= "TO_NUMBER(c.LEVEL_NO_MIN)";
				elseif($DPISDB=="mysql") $order_by .= "c.LEVEL_NO_MIN";

				$heading_name .= " ช่วงระดับตำแหน่ง";
				break;
			case "PROVINCE" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.PV_CODE";
				elseif($select_org_structure==1) $select_list .= "e.PV_CODE";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.PV_CODE";
				elseif($select_org_structure==1) $order_by .= "e.PV_CODE";

				$heading_name .= " จังหวัด";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if($select_org_structure==0) $order_by = "b.ORG_SEQ_NO, a.ORG_ID";
		elseif($select_org_structure==1) $order_by = "e.ORG_SEQ_NO, d.ORG_ID";
	} // end if
	if(!trim($select_list)){
		if($select_org_structure==0) $select_list = "b.ORG_SEQ_NO, a.ORG_ID";
		elseif($select_org_structure==1) $select_list = "e.ORG_SEQ_NO, d.ORG_ID";
	} // end if

	$search_condition = "";
	$arr_search_condition[] = "(a.POS_STATUS=1)";

	$list_type_text = "ทั้งส่วนราชการ";

	if($list_type == "PER_ORG_TYPE_1"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		if($DPISDB=="odbc"){ 
			if($select_org_structure==0) $arr_search_condition[] = "(LEFT(trim(b.OT_CODE), 1)='1')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(LEFT(trim(e.OT_CODE), 1)='1')";
		}elseif($DPISDB=="oci8"){ 
			if($select_org_structure==0) $arr_search_condition[] = "(SUBSTR(trim(b.OT_CODE), 1, 1)='1'";
			elseif($select_org_structure==1) $arr_search_condition[] = "(SUBSTR(trim(e.OT_CODE), 1, 1)='1'";
		}elseif($DPISDB=="mysql"){
			if($select_org_structure==0) $arr_search_condition[] = "(LEFT(trim(b.OT_CODE), 1)='1')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(LEFT(trim(e.OT_CODE), 1)='1')";
		} // end if
	}elseif($list_type == "PER_ORG_TYPE_2"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		if($DPISDB=="odbc"){ 
			if($select_org_structure==0) $arr_search_condition[] = "(LEFT(trim(b.OT_CODE), 1)='2')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(LEFT(trim(e.OT_CODE), 1)='2')";
		}elseif($DPISDB=="oci8"){ 
			if($select_org_structure==0) $arr_search_condition[] = "(SUBSTR(trim(b.OT_CODE), 1, 1)='2')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(SUBSTR(trim(e.OT_CODE), 1, 1)='2')";
		}elseif($DPISDB=="mysql"){
			if($select_org_structure==0) $arr_search_condition[] = "(LEFT(trim(b.OT_CODE), 1)='2')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(LEFT(trim(e.OT_CODE), 1)='2')";
		} // end if
	}elseif($list_type == "PER_ORG"){
		// สำนัก/กอง , ฝ่าย , งาน
		$list_type_text = "";
		if(trim($search_org_id)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(d.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
		} // end if
		if(trim($search_org_id_1)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
		} // end if
	}elseif($list_type == "PER_LINE"){
		// สายงาน
		$list_type_text = "";
		if(trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "(trim(a.PL_CODE)='$search_pl_code')";
			$list_type_text .= "$search_pl_name";
		} // end if
	}elseif($list_type == "PER_COUNTRY"){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(b.CT_CODE) = '$search_ct_code')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(b.PV_CODE) = '$search_pv_code')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}else{
		// ทั้งส่วนราชการ
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "จำนวนตำแหน่งจำแนกตามสถานภาพและลักษณะของตำแหน่ง";
	$report_code = "R0101";

	function count_position($position_type, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $select_org_structure, $DEPARTMENT_ID;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		if($position_type == 1){
			// ตำแหน่งมีผู้ถือครอง		
			$search_condition = str_replace(" where ", " and ", $search_condition);
			if($select_org_structure == 0){
				if($DPISDB=="odbc"){
					$cmd = " select			count(a.POS_ID) as count_position
							   from			(
												(
													PER_POSITION a
													inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)														
												) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
											) inner join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
							   where		d.PER_STATUS=1 and a.DEPARTMENT_ID=$DEPARTMENT_ID
											$search_condition
							   group by		a.POS_ID
						   	";
				}elseif($DPISDB=="oci8"){				
					$cmd = " select			count(a.POS_ID) as count_position
							   from			PER_POSITION a, PER_ORG b, PER_CO_LEVEL c, PER_PERSONAL d
							   where		a.ORG_ID=b.ORG_ID and a.CL_NAME=c.CL_NAME and a.POS_ID=d.POS_ID and d.PER_STATUS=1
											and a.DEPARTMENT_ID=$DEPARTMENT_ID
											$search_condition
							   group by		a.POS_ID
						   	";
				}elseif($DPISDB=="mysql"){
					$cmd = " select			count(a.POS_ID) as count_position
							   from			(
												(
													PER_POSITION a
													inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)														
												) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
											) inner join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
							   where		d.PER_STATUS=1 and a.DEPARTMENT_ID=$DEPARTMENT_ID
											$search_condition
							   group by		a.POS_ID
						   	";
				} // end if
			}elseif($select_org_structure == 1){
				if($DPISDB=="odbc"){
					$cmd = " select			count(a.POS_ID) as count_position
							   from			(
												(
													(
														PER_POSITION a
														inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)														
													) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
												) inner join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
											) inner join PER_ORG_ASS e on (d.ORG_ID=e.ORG_ID)
							   where		d.PER_STATUS=1 and a.DEPARTMENT_ID=$DEPARTMENT_ID
											$search_condition
							   group by		a.POS_ID
						   	";
				}elseif($DPISDB=="oci8"){				
					$cmd = " select			count(a.POS_ID) as count_position
							   from			PER_POSITION a, PER_ORG b, PER_CO_LEVEL c, PER_PERSONAL d, PER_ORG_ASS e
							   where		a.ORG_ID=b.ORG_ID and a.CL_NAME=c.CL_NAME and a.POS_ID=d.POS_ID and d.ORG_ID=e.ORG_ID and d.PER_STATUS=1
											and a.DEPARTMENT_ID=$DEPARTMENT_ID
											$search_condition
							   group by		a.POS_ID
						   	";
				}elseif($DPISDB=="mysql"){
					$cmd = " select			count(a.POS_ID) as count_position
							   from			(
												(
													(
														PER_POSITION a
														inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)														
													) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
												) inner join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
											) inner join PER_ORG_ASS e on (d.ORG_ID=e.ORG_ID)
							   where		d.PER_STATUS=1 and a.DEPARTMENT_ID=$DEPARTMENT_ID
											$search_condition
							   group by		a.POS_ID
						   	";
				} // end if
			} // end if
		}elseif($position_type == 2){
			// ตำแหน่งว่างมีเงิน
			$cmd = " select 		count(b.PER_ID) as count_person, a.POS_ID 
							 from 			PER_POSITION a, PER_PERSONAL b 
							 where 		a.POS_ID=b.POS_ID and b.PER_STATUS=1 and a.POS_STATUS=1 and a.DEPARTMENT_ID=$DEPARTMENT_ID
							 group by 	a.POS_ID 
							 having 		count(b.PER_ID) > 0 
						  ";
			$db_dpis2->send_cmd($cmd);
			while($data = $db_dpis2->get_array()) $arr_exclude[] = $data[POS_ID];
			
			$search_condition = str_replace(" where ", " and ", $search_condition);
//				if(count($arr_exclude)) $search_condition .= " and a.POS_ID not in (". implode(",", $arr_exclude) .")";
			if(count($arr_exclude)) $search_condition .= " and a.POS_ID not in ( select distinct a.POS_ID from PER_POSITION a, PER_PERSONAL b where a.POS_ID=b.POS_ID and b.PER_STATUS=1 and a.POS_STATUS=1 and a.DEPARTMENT_ID=$DEPARTMENT_ID )";

			if($select_org_structure == 0){
				if($DPISDB=="odbc"){
					$cmd = " select			count(a.POS_ID) as count_position
									 from			(
															PER_POSITION a
															inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
														) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
									 where		trim(a.POS_GET_DATE)<>'' and a.POS_GET_DATE is not null and a.DEPARTMENT_ID=$DEPARTMENT_ID
														$search_condition
									 group by	a.POS_ID
								   ";
				}elseif($DPISDB=="oci8"){
					$cmd = " select			count(a.POS_ID) as count_position
									 from			PER_POSITION a, PER_ORG b, PER_CO_LEVEL c
									 where		a.ORG_ID=b.ORG_ID and a.CL_NAME=c.CL_NAME and a.POS_GET_DATE is not null
									 					and a.DEPARTMENT_ID=$DEPARTMENT_ID
														$search_condition
									 group by	a.POS_ID
								   ";
				}elseif($DPISDB=="mysql"){
					$cmd = " select			count(a.POS_ID) as count_position
									 from			(
															PER_POSITION a
															inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
														) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
									 where		trim(a.POS_GET_DATE)<>'' and a.POS_GET_DATE is not null and a.DEPARTMENT_ID=$DEPARTMENT_ID
														$search_condition
									 group by	a.POS_ID
								   ";
				} // end if	
			}elseif($select_org_structure == 1){
				if($DPISDB=="odbc"){
					$cmd = " select			count(a.POS_ID) as count_position
							   from			(
												(
													(
														PER_POSITION a
														inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)														
													) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
												) inner join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
											) inner join PER_ORG_ASS e on (d.ORG_ID=e.ORG_ID)
							   where		trim(a.POS_GET_DATE)<>'' and a.POS_GET_DATE is not null and d.PER_STATUS=1 and a.DEPARTMENT_ID=$DEPARTMENT_ID
											$search_condition
							   group by		a.POS_ID
						   	";
				}elseif($DPISDB=="oci8"){				
					$cmd = " select			count(a.POS_ID) as count_position
							   from			PER_POSITION a, PER_ORG b, PER_CO_LEVEL c, PER_PERSONAL d, PER_ORG_ASS e
							   where		a.ORG_ID=b.ORG_ID and a.CL_NAME=c.CL_NAME and a.POS_ID=d.POS_ID and d.ORG_ID=e.ORG_ID 
							   				and a.POS_GET_DATE is not null and d.PER_STATUS=1 and a.DEPARTMENT_ID=$DEPARTMENT_ID
											$search_condition
							   group by		a.POS_ID
						   	";
				}elseif($DPISDB=="mysql"){
					$cmd = " select			count(a.POS_ID) as count_position
							   from			(
												(
													(
														PER_POSITION a
														inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)														
													) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
												) inner join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
											) inner join PER_ORG_ASS e on (d.ORG_ID=e.ORG_ID)
							   where		trim(a.POS_GET_DATE)<>'' and a.POS_GET_DATE is not null and d.PER_STATUS=1 and a.DEPARTMENT_ID=$DEPARTMENT_ID
											$search_condition
							   group by		a.POS_ID
						   	";
				} // end if
			} // end if
		}elseif($position_type == 3){
			// ตำแหน่งว่างไม่มีเงิน
			$cmd = " select 		count(b.PER_ID) as count_person, a.POS_ID 
							 from 			PER_POSITION a, PER_PERSONAL b 
							 where 		a.POS_ID=b.POS_ID and b.PER_STATUS=1 and a.POS_STATUS=1 and a.DEPARTMENT_ID=$DEPARTMENT_ID
							 group by 	a.POS_ID 
							 having 		count(b.PER_ID) > 0 
						  ";
			$db_dpis2->send_cmd($cmd);
			while($data = $db_dpis2->get_array()) $arr_exclude[] = $data[POS_ID];			
			
			$search_condition = str_replace(" where ", " and ", $search_condition);	
//				if(count($arr_exclude)) $search_condition .= " and a.POS_ID not in (". implode(",", $arr_exclude) .")";
			if(count($arr_exclude)) $search_condition .= " and a.POS_ID not in ( select distinct a.POS_ID from PER_POSITION a, PER_PERSONAL b where a.POS_ID=b.POS_ID and b.PER_STATUS=1 and a.POS_STATUS=1 and a.DEPARTMENT_ID=$DEPARTMENT_ID )";

			if($select_org_structure == 0){
				if($DPISDB=="odbc"){
					$cmd = " select			count(a.POS_ID) as count_position
									 from			(
															PER_POSITION a
															inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
														) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
									 where		(trim(a.POS_GET_DATE)='' or a.POS_GET_DATE is null) and a.DEPARTMENT_ID=$DEPARTMENT_ID
														$search_condition
									 group by	a.POS_ID
								   ";
				}elseif($DPISDB=="oci8"){
					$cmd = " select			count(a.POS_ID) as count_position
									 from			PER_POSITION a, PER_ORG b, PER_CO_LEVEL c
									 where		a.ORG_ID=b.ORG_ID and a.CL_NAME=c.CL_NAME and a.POS_GET_DATE is null
														and a.DEPARTMENT_ID=$DEPARTMENT_ID
														$search_condition
									 group by	a.POS_ID
								   ";
				}elseif($DPISDB=="mysql"){
					$cmd = " select			count(a.POS_ID) as count_position
									 from			(
															PER_POSITION a
															inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
														) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
									 where		(trim(a.POS_GET_DATE)='' or a.POS_GET_DATE is null) and a.DEPARTMENT_ID=$DEPARTMENT_ID
														$search_condition
									 group by	a.POS_ID
								   ";
				} // end if
			}elseif($select_org_structure == 1){
				if($DPISDB=="odbc"){
					$cmd = " select			count(a.POS_ID) as count_position
							   from			(
												(
													(
														PER_POSITION a
														inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)														
													) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
												) inner join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
											) inner join PER_ORG_ASS e on (d.ORG_ID=e.ORG_ID)
							   where		(trim(a.POS_GET_DATE)='' or a.POS_GET_DATE is null) and d.PER_STATUS=1 and a.DEPARTMENT_ID=$DEPARTMENT_ID
											$search_condition
							   group by		a.POS_ID
						   	";
				}elseif($DPISDB=="oci8"){				
					$cmd = " select			count(a.POS_ID) as count_position
							   from			PER_POSITION a, PER_ORG b, PER_CO_LEVEL c, PER_PERSONAL d, PER_ORG_ASS e
							   where		a.ORG_ID=b.ORG_ID and a.CL_NAME=c.CL_NAME and a.POS_ID=d.POS_ID and d.ORG_ID=e.ORG_ID 
							   				and a.POS_GET_DATE is null and d.PER_STATUS=1 and a.DEPARTMENT_ID=$DEPARTMENT_ID
											$search_condition
							   group by		a.POS_ID
						   	";
				}elseif($DPISDB=="mysql"){
					$cmd = " select			count(a.POS_ID) as count_position
							   from			(
												(
													(
														PER_POSITION a
														inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)														
													) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
												) inner join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
											) inner join PER_ORG_ASS e on (d.ORG_ID=e.ORG_ID)
							   where		(trim(a.POS_GET_DATE)='' or a.POS_GET_DATE is null) and d.PER_STATUS=1 and a.DEPARTMENT_ID=$DEPARTMENT_ID
											$search_condition
							   group by		a.POS_ID
						   	";
				} // end if
			} // end if
		} // end if

		$count_position = $db_dpis2->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis2->show_error();
//		$data = $db_dpis2->get_array();
//		$data = array_change_key_case($data, CASE_LOWER);
//		$count_position = $data[count_position] + 0;

		return $count_position;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $select_org_structure;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $CL_NAME, $PV_CODE;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					if($select_org_structure==0)
						if($ORG_ID) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					elseif($select_org_structure==1)
						if($ORG_ID) $arr_addition_condition[] = "(d.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(d.ORG_ID = 0 or d.ORG_ID is null)";
				break;
				case "ORG_1" :
					if($select_org_structure==0)
						if($ORG_ID_1) $arr_addition_condition[] = "(a.ORG_ID_1 = $ORG_ID_1)";
						else $arr_addition_condition[] = "(a.ORG_ID_1 = 0 or a.ORG_ID_1 is null)";
				break;
				case "ORG_2" :
					if($select_org_structure==0)
						if($ORG_ID_2) $arr_addition_condition[] = "(a.ORG_ID_2 = $ORG_ID_2)";
						else $arr_addition_condition[] = "(a.ORG_ID_2 = 0 or a.ORG_ID_2 is null)";					
				break;
				case "LINE" :
					if($PL_CODE) $arr_addition_condition[] = "(trim(a.PL_CODE) = '$PL_CODE')";
					else $arr_addition_condition[] = "(trim(a.PL_CODE) = '$PL_CODE' or a.PL_CODE is null)";
				break;
				case "CO_LEVEL" :
					if($CL_NAME) $arr_addition_condition[] = "(trim(a.CL_NAME) = '$CL_NAME')";
					else $arr_addition_condition[] = "(trim(a.CL_NAME) = '$CL_NAME' or a.CL_NAME is null)";
				break;
				case "PROVINCE" :
					if($select_org_structure==0)
						if($PV_CODE) $arr_addition_condition[] = "(trim(b.PV_CODE) = '$PV_CODE')";
						else $arr_addition_condition[] = "(trim(b.PV_CODE) = '$PV_CODE' or b.PV_CODE is null)";
					elseif($select_org_structure==1)
						if($PV_CODE) $arr_addition_condition[] = "(trim(e.PV_CODE) = '$PV_CODE')";
						else $arr_addition_condition[] = "(trim(e.PV_CODE) = '$PV_CODE' or e.PV_CODE is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $CL_NAME, $PV_CODE;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					$ORG_ID = -1;
				break;
				case "ORG_1" :
					$ORG_ID_1 = -1;
				break;
				case "ORG_2" :
					$ORG_ID_2 = -1;
				break;
				case "LINE" :
					$PL_CODE = -1;
				break;
				case "CO_LEVEL" :
					$CL_NAME = -1;
				break;
				case "PROVINCE" :
					$PV_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function

	if($select_org_structure == 0){
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
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct g.ORG_SEQ_NO, g.ORG_ID as MINISTRY_ID, d.ORG_SEQ_NO, d.ORG_ID as DEPARTMENT_ID, $select_list
					   from			PER_POSITION a, PER_ORG b, PER_CO_LEVEL c, PER_ORG d, PER_ORG g
					   where		a.ORG_ID=b.ORG_ID and a.CL_NAME=c.CL_NAME and b.ORG_ID_REF=d.ORG_ID and d.ORG_ID_REF=g.ORG_ID
									$search_condition
					   order by		g.ORG_SEQ_NO, g.ORG_ID, d.ORG_SEQ_NO, d.ORG_ID, $order_by
					";
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
	}elseif($select_org_structure == 1){
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
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = 0;
	initialize_parameter(0);
	$DEPARTMENT_ID = -1;
	while($data = $db_dpis->get_array()){

		if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){	
			$MINISTRY_ID = $data[MINISTRY_ID];
			if($select_org_structure==0)
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$MINISTRY_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$MINISTRY_NAME = $data2[ORG_NAME];

			$DEPARTMENT_ID = $data[DEPARTMENT_ID];			
			if($select_org_structure==0)
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_NAME = $data2[ORG_NAME];
		} // end if
		
		$rpt_order_index = count($arr_rpt_order) - 1;
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){

			case "ORG" :
				if($ORG_ID != $data[ORG_ID]){
					$ORG_ID = $data[ORG_ID];
					if($ORG_ID == ""){
						$ORG_NAME = "[ไม่ระบุสำนัก/กอง]";
					}else{
						if($select_org_structure==0)
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
						elseif($select_org_structure==1)
							$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$ORG_ID ";
						$db_dpis2->send_cmd($cmd);
//						$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						$ORG_NAME = $data2[ORG_NAME];
					} // end if

					$addition_condition = generate_condition($rpt_order_index);

					$arr_categories[] = $ORG_NAME;
					$arr_series_caption_data[0][] = count_position(1, $search_condition, $addition_condition);
					$arr_series_caption_data[1][] = count_position(2, $search_condition, $addition_condition);
					$arr_series_caption_data[2][] = count_position(3, $search_condition, $addition_condition);

				} // end if
			break;

			case "ORG_1" :
				if($ORG_ID_1 != $data[ORG_ID_1]){
					$ORG_ID_1 = $data[ORG_ID_1];
					if($ORG_ID_1 == ""){
						$ORG_NAME_1 = "[ไม่ระบุฝ่าย]";
					}else{
						if($select_org_structure==0)
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
						$db_dpis2->send_cmd($cmd);
//						$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						$ORG_NAME_1 = $data2[ORG_NAME];
					} // end if

					$addition_condition = generate_condition($rpt_order_index);
		
					$arr_categories[] = $ORG_NAME_1;
					$arr_series_caption_data[0][] = count_position(1, $search_condition, $addition_condition);
					$arr_series_caption_data[1][] = count_position(2, $search_condition, $addition_condition);
					$arr_series_caption_data[2][] = count_position(3, $search_condition, $addition_condition);

				} // end if
			break;
	
			case "ORG_2" :
				if($ORG_ID_2 != $data[ORG_ID_2]){
					$ORG_ID_2 = $data[ORG_ID_2];
					if($ORG_ID_2 == ""){
						$ORG_NAME_2 = "[ไม่ระบุงาน]";
					}else{
						if($select_org_structure==0)
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
						$db_dpis2->send_cmd($cmd);
//						$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						$ORG_NAME_2 = $data2[ORG_NAME];
					} // end if

					$addition_condition = generate_condition($rpt_order_index);
		
					$arr_categories[] = $ORG_NAME_2;
					$arr_series_caption_data[0][] = count_position(1, $search_condition, $addition_condition);
					$arr_series_caption_data[1][] = count_position(2, $search_condition, $addition_condition);
					$arr_series_caption_data[2][] = count_position(3, $search_condition, $addition_condition);

				} // end if
			break;
	
			case "LINE" :
				if($PL_CODE != trim($data[PL_CODE])){
					$PL_CODE = trim($data[PL_CODE]);
					if($PL_CODE == ""){
						$PL_NAME = "[ไม่ระบุสายงาน]";
					}else{
						$cmd = " select PL_NAME, PL_SHORTNAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
						$db_dpis2->send_cmd($cmd);
//						$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						$PL_NAME = trim($data2[PL_SHORTNAME])?$data2[PL_SHORTNAME]:$data2[PL_NAME];
					} // end if

					$addition_condition = generate_condition($rpt_order_index);
		
					$arr_categories[] = $PL_NAME;
					$arr_series_caption_data[0][] = count_position(1, $search_condition, $addition_condition);
					$arr_series_caption_data[1][] = count_position(2, $search_condition, $addition_condition);
					$arr_series_caption_data[2][] = count_position(3, $search_condition, $addition_condition);

				} // end if
			break;
	
			case "CO_LEVEL" :
				if($CL_NAME != trim($data[CL_NAME])){
					$CL_NAME = trim($data[CL_NAME]);

					$addition_condition = generate_condition($rpt_order_index);
		
					$arr_categories[] = (trim($CL_NAME)?"ระดับ $CL_NAME":"[ไม่ระบุช่วงระดับตำแหน่ง]");
					$arr_series_caption_data[0][] = count_position(1, $search_condition, $addition_condition);
					$arr_series_caption_data[1][] = count_position(2, $search_condition, $addition_condition);
					$arr_series_caption_data[2][] = count_position(3, $search_condition, $addition_condition);

				} // end if
			break;
	
			case "PROVINCE" :
				if($PV_CODE != trim($data[PV_CODE])){
					$PV_CODE = trim($data[PV_CODE]);
					if($PV_CODE == ""){
						$PV_NAME = "[ไม่ระบุจังหวัด]";
					}else{
						$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE' ";
						$db_dpis2->send_cmd($cmd);
//						$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						$PV_NAME = $data2[PV_NAME];
					} // end if

					$addition_condition = generate_condition($rpt_order_index);
		
					$arr_categories[] = $PV_NAME;
					$arr_series_caption_data[0][] = count_position(1, $search_condition, $addition_condition);
					$arr_series_caption_data[1][] = count_position(2, $search_condition, $addition_condition);
					$arr_series_caption_data[2][] = count_position(3, $search_condition, $addition_condition);

				} // end if
			break;
		} // end switch case

	} // end while
	
	// graph parameter
	// chart_title					หัวข้อเรื่องหลัก
	// chart_subtitle			หัวข้อเรื่องย่อย
	// categories_list			legend
	// series_caption_list	label ข้อมูล
	// series_list					ข้อมูล

	$chart_title = $report_title;
	$chart_subtitle = $company_name;
	if(!$setWidth) $setWidth = "800";
	if(!$setHeight) $setHeight = "600";
	$selectedFormat = "SWF";
	$series_caption_list = "ถือครอง;ว่างมีเงิน;ว่างไม่มีเงิน";
	$categories_list = implode(";", $arr_categories);
	$arr_series_list[0] = implode(";", $arr_series_caption_data[0]);
	$arr_series_list[1] = implode(";", $arr_series_caption_data[1]);
	$arr_series_list[2] = implode(";", $arr_series_caption_data[2]);
	$series_list = implode("|", $arr_series_list);
	
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
	} // end switch case
?>