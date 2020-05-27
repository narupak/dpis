<?
for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
	$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
	switch($REPORT_ORDER){
		case "POSNO" :
			if($POS_NO != trim($data[POS_NO])){
				$POS_NO = trim($data[POS_NO]);

				$addition_condition = generate_condition1($rpt_order_index);

				if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

				if($rpt_order_index == (count($arr_rpt_order) - 1)){	
					$data_row++;
					$POS_ID = $data[POS_ID];
				
					$cmd = " select 	PER_ID, PER_CARDNO, PN_CODE, PER_NAME, PER_SURNAME
							from		PER_PERSONAL
							where	POS_ID=$POS_ID and PER_STATUS=1  ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PER_ID = $data2[PER_ID];
					$PER_CARDNO = $data2[PER_CARDNO];
					$PN_CODE = $data2[PN_CODE];
					$PER_NAME = trim($data2[PER_NAME]);
					$PER_SURNAME = trim($data2[PER_SURNAME]);
					
					$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PN_NAME = $data2[PN_NAME];

					if($PER_ID){
						// === หาเครื่องราชย์
						$DC_NAME = $DEH_DATE = "";
						$cmd = " select DC_SHORTNAME, DEH_DATE, DEH_POSITION, DEH_ORG
												from   PER_DECORATEHIS a, PER_DECORATION b
												where PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE
								order by DEH_DATE desc ";
//												where PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE and DC_ORDER <= 18 
						$db_dpis1->send_cmd($cmd);
						while($data1 = $db_dpis1->get_array()){
							$DC_NAME = trim($data1[DC_SHORTNAME]);
							$DEH_DATE = trim($data1[DEH_DATE]);
							if ($DEH_DATE) {	
								$tmp_date = explode("-", substr(trim($data1[DEH_DATE]), 0, 10));
								$DEH_DATE = $tmp_date[2] ."/". $tmp_date[1] ."/". ($tmp_date[0] + 543);
							}

							$POH_EFFECTIVEDATE = $tmp_date[0] ."-". $tmp_date[1] ."-". $tmp_date[2];
							$PL_CODE = $PL_NAME = $ORG_NAME = "";
							$cmd = " select PL_CODE, POH_ORG2
									from   PER_POSITIONHIS
									where PER_ID=$PER_ID and POH_EFFECTIVEDATE < '$POH_EFFECTIVEDATE'
									order by POH_EFFECTIVEDATE desc ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$PL_CODE = trim($data2[PL_CODE]);
							$ORG_NAME = trim($data2[POH_ORG2]);

							$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$PL_NAME = $data2[PL_NAME];
						
							$arr_content[$data_count][type] = "CONTENT";
							$arr_content[$data_count][order] = $data_row;
							$arr_content[$data_count][per_cardno] = $PER_CARDNO;
							$arr_content[$data_count][per_name] = $PER_NAME;
							$arr_content[$data_count][per_surname] = $PER_SURNAME;
							$arr_content[$data_count][dc_name] = $DC_NAME;
							$arr_content[$data_count][deh_date] = $DEH_DATE;
							$arr_content[$data_count][pl_name] = $PL_NAME;
							$arr_content[$data_count][org_name] = $ORG_NAME;

							$data_count++;														
						} // end while
					} // end if
				} // end if
			} // end if
		break;
	} // end switch case
} // end for
?>