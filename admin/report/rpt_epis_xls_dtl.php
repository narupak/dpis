<?
//echo("arr_rpt_order count = ".count($arr_rpt_order)."<BR>");
for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
//echo($rpt_order_index."<BR>");
	$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
	switch($REPORT_ORDER){
		case "POSNO" :
			//echo("POEMS_NO = ".$POEMS_NO."/data[POEMS_NO] = ".$data[POEMS_NO]."<BR>");
			if($POEMS_NO != trim($data[POEMS_NO])){
				$POEMS_NO = trim($data[POEMS_NO]);

				$addition_condition = generate_condition($rpt_order_index);
				//echo("arr_rpt_order count = ".count($arr_rpt_order)."<BR>");
				//echo("rpt_order_index = ".$rpt_order_index."/arr_rpt_order = ".(count($arr_rpt_order)-1)."<BR>");
				if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

				if($rpt_order_index == (count($arr_rpt_order) - 1)){	
					$data_row++;
					$POEMS_ID = $data[POEMS_ID];
					$EP_CODE = trim($data[EP_CODE]);
					$EP_NAME = trim($data[EP_NAME]);	
					$PV_CODE = trim($data[PV_CODE]);
					$PV_NAME = trim($data[PV_NAME]);
					$OT_CODE = trim($data[OT_CODE]);
					$OT_NAME = trim($data[OT_NAME]);
					$PPT_CODE = trim($data[PPT_CODE]);
					$PEF_CODE = trim($data[PEF_CODE]);
					$PPS_CODE = trim($data[PPS_CODE]);
					$POEMS_SKILL = trim($data[POEMS_SKILL]);
					$POEMS_SOUTH = trim($data[POEMS_SOUTH]);
					$ORG_ID_2 = trim($data[ORG_ID]);
					$ORG_NAME_2 = trim($data[ORG_NAME]);			// ชื่อสำนัก/กอง

					// === หาฝ่ายและงานจาก สำนัก/กอง ของ PER_POSITION
					unset($tmp_ORG_ID, $ORG_ID_search, $ORG_ID_3, $ORG_ID_4, $ORG_NAME_3, $ORG_NAME_4);
					$ORG_ID_3 = $tmp_ORG_ID[] = trim($data[ORG_ID_1]);
					$ORG_ID_4 = $tmp_ORG_ID[] = trim($data[ORG_ID_2]);
					$ORG_ID_search = implode(", ", $tmp_ORG_ID);
					$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID in ($ORG_ID_search) ";
					$db_dpis2->send_cmd($cmd);
					while ( $data2 = $db_dpis2->get_array() ) {
						// ชื่อฝ่าย
						$ORG_NAME_3 = ($ORG_ID_3 == trim($data2[ORG_ID]))? trim($data2[ORG_NAME]) : "$ORG_NAME_3";
						// ชื่องาน
						$ORG_NAME_4 = ($ORG_ID_4 == trim($data2[ORG_ID]))? trim($data2[ORG_NAME]) : "$ORG_NAME_4";
					}	// while
					// === หากระทรวงและกรมจาก สำนัก/กอง ของ PER_POSITION
					$cmd = " select a.ORG_NAME, b.ORG_NAME as ORGNAME2, c.ORG_NAME as ORGNAME1
							from PER_ORG a, PER_ORG b, PER_ORG c
							where a.ORG_ID=$ORG_ID_2 and a.ORG_ID_REF=b.ORG_ID and b.ORG_ID_REF=c.ORG_ID";
					if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array(); 
					$ORG_NAME = trim($data2[ORGNAME1]);
					$ORG_NAME_1 = trim($data2[ORGNAME2]);
				
					$where = "POEMS_ID=$POEMS_ID ";
					$where .= " and PER_STATUS=1";
					$cmd = " select 	PER_ID, PER_GENDER, PER_BIRTHDATE, PER_STARTDATE, LEVEL_NO, 
									PER_SALARY, PER_MGTSALARY, PER_OFFNO, PER_CARDNO, PN_CODE, 
									PER_NAME, PER_SURNAME, MOV_CODE, PER_OCCUPYDATE, PER_POSDATE, 
									PER_RETIREDATE, PER_CONTACT_COUNT, PER_DISABILITY 
							from		PER_PERSONAL  
							where	$where ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PER_ID = $data2[PER_ID];
					$PER_GENDER = $data2[PER_GENDER];
					$PER_BIRTHDATE = substr(trim($data2[PER_BIRTHDATE]), 0, 10);
					$BIRTHDATE_D = $BIRTHDATE_M = $BIRTHDATE_Y = $RETIREDATE_Y = "";
					if($PER_BIRTHDATE){
						$arr_temp = explode("-", $PER_BIRTHDATE);
						$BIRTHDATE_D = $arr_temp[2];
						$BIRTHDATE_M = $arr_temp[1];
						$BIRTHDATE_Y = $arr_temp[0] + 543;
						$RETIREDATE_Y = ("$BIRTHDATE_M-$BIRTHDATE_D" >= "10-01")?($arr_temp[0] + 543 + 60):($arr_temp[0] + 543 + 61);
						$PER_BIRTHDATE = show_date_format($PER_BIRTHDATE,1);
					} // end if
					$PER_STARTDATE = substr(trim($data2[PER_STARTDATE]), 0, 10);
					$STARTDATE_D = $STARTDATE_M = $STARTDATE_Y = "";
					if($PER_STARTDATE){
						$arr_temp = explode("-", $PER_STARTDATE);
						$STARTDATE_D = $arr_temp[2];
						$STARTDATE_M = $arr_temp[1];
						$STARTDATE_Y = $arr_temp[0] + 543;
						$PER_STARTDATE = show_date_format($PER_STARTDATE,1);
					} // end if
					$budget_year = $search_budget_year - 543; 
					$budget_year_from = $budget_year - 1; 
					$budget_year_from = $budget_year_from.'-10-01'; 
					$budget_year_to = $budget_year.'-09-30';
					$LEVEL_NO = trim($data2[LEVEL_NO]);
					$PER_SALARY = $data2[PER_SALARY];
					$PER_MGTSALARY = $data2[PER_MGTSALARY];
					$PER_OFFNO = $data2[PER_OFFNO];
					$PER_CARDNO = $data2[PER_CARDNO];
					$PN_CODE = $data2[PN_CODE];
					$PER_NAME = $data2[PER_NAME];
					$PER_SURNAME = $data2[PER_SURNAME];
					$PER_CONTACT_COUNT = $data2[PER_CONTACT_COUNT];
					$PER_DISABILITY = $data2[PER_DISABILITY];
					
					$MOV_CODE = trim($data2[MOV_CODE]);
					$cmd = " select MOV_NAME from PER_MOVMENT where MOV_CODE='$MOV_CODE'";
					$db_dpis3->send_cmd($cmd);
					$data3 = $db_dpis3->get_array();
					$MOV_NAME = ($data3[MOV_NAME]);
					
					$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PN_NAME = $data2[PN_NAME];

					$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$LEVEL_NAME = $data2[LEVEL_NAME];

					$cmd = " select PPS_NAME from PER_POS_STATUS where PPS_CODE='$PPS_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PPS_NAME = $data2[PPS_NAME];

					$cmd = " select PPT_NAME from PER_PRACTICE where PPT_CODE='$PPT_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PPT_NAME = $data2[PPT_NAME];

					$cmd = " select PEF_NAME from PER_POS_EMPSER_FRAME where PEF_CODE='$PEF_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PEF_NAME = $data2[PEF_NAME];

					$EL_CODE = $EL_NAME = $EN_NAME = $EM_NAME = $INS_CODE = $INS_NAME = $ST_CODE = $ST_NAME = $CT_CODE_EDU = $CT_NAME_EDU = "";
					if($PER_ID){
						if($DPISDB=="odbc"){
							// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
							$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE, CT_CODE_EDU
									from 	((
												PER_EDUCATE a
												inner join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
											) inner join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
											) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
									where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%||4||%' ";									
						}elseif($DPISDB=="oci8"){
							// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
							$cmd = "	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE, CT_CODE_EDU  
									from 	PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
									where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%||4||%' and 
											a.EN_CODE=b.EN_CODE and b.EL_CODE=c.EL_CODE and 
											a.EM_CODE=d.EM_CODE(+) ";	
						}elseif($DPISDB=="mysql"){
							// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
							$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE, CT_CODE_EDU
									from 	((
												PER_EDUCATE a
												inner join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
											) inner join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
											) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
									where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%||4||%' ";	
						} // end if
						$count_educate = $db_dpis2->send_cmd($cmd);

						if(!$count_educate){
							if($DPISDB=="odbc"){
								// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
								$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE, CT_CODE_EDU
										from 	((
													PER_EDUCATE a
													inner join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
												) inner join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
										where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%||2||%' ";
							}elseif($DPISDB=="oci8"){
								// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
								$cmd = "	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE, CT_CODE_EDU  
										from 	PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
										where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%||2||%' and 
												a.EN_CODE=b.EN_CODE and b.EL_CODE=c.EL_CODE and 
												a.EM_CODE=d.EM_CODE(+) ";
							}elseif($DPISDB=="mysql"){
								// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
								$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE, CT_CODE_EDU
										from 	((
													PER_EDUCATE a
													inner join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
												) inner join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
										where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%||2||%' ";
							} // end if
							$count_educate = $db_dpis2->send_cmd($cmd);
						}

						if(!$count_educate){
							if($DPISDB=="odbc"){
								// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
								$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE, CT_CODE_EDU
										from 	((
													PER_EDUCATE a
													inner join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
												) inner join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
										where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%||1||%' ";
							}elseif($DPISDB=="oci8"){
								// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
								$cmd = "	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE, CT_CODE_EDU  
										from 	PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
										where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%||1||%' and 
												a.EN_CODE=b.EN_CODE and b.EL_CODE=c.EL_CODE and 
												a.EM_CODE=d.EM_CODE(+) ";
							}elseif($DPISDB=="mysql"){
								// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
								$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE, CT_CODE_EDU
										from 	((
													PER_EDUCATE a
													inner join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
												) inner join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
										where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%||1||%' ";
							} // end if
							$count_educate = $db_dpis2->send_cmd($cmd);
						}

						$data2 = $db_dpis2->get_array();
						$EL_CODE = trim($data2[EL_CODE]);
						$EL_NAME = trim($data2[EL_NAME]);
						$EN_NAME = trim($data2[EN_NAME]);
						$EM_NAME = trim($data2[EM_NAME]);
						$INS_CODE = trim($data2[INS_CODE]);
						$ST_CODE = trim($data2[ST_CODE]);
						$CT_CODE_EDU = trim($data2[CT_CODE_EDU]);
						
						if ($INS_CODE) {
							$cmd = " select INS_NAME from PER_INSTITUTE where INS_CODE='$INS_CODE' ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$INS_NAME = trim($data2[INS_NAME]);
						} else {	
							$INS_NAME = trim($data2[EDU_INSTITUTE]);
						} // end if
						
						$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE_EDU' ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$CT_NAME_EDU = $data2[CT_NAME];

						// === หาวันที่เริ่มสัญญาปัจจุบัน, วันที่สิ้นสุดสัญญาปัจจุบัน
						$POH_EFFECTIVEDATE = $POH_ENDDATE = "";
						$cmd = " select POH_EFFECTIVEDATE, POH_ENDDATE
								from   PER_POSITIONHIS
								where PER_ID=$PER_ID 
								order by POH_EFFECTIVEDATE desc ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$POH_EFFECTIVEDATE = substr(trim($data2[POH_EFFECTIVEDATE]), 0, 10);
						$POH_EFFECTIVEDATE_D = $POH_EFFECTIVEDATE_M = $POH_EFFECTIVEDATE_Y = "";
						if($POH_EFFECTIVEDATE){
							$arr_temp = explode("-", $POH_EFFECTIVEDATE);
							$POH_EFFECTIVEDATE_D = $arr_temp[2];
							$POH_EFFECTIVEDATE_M = $arr_temp[1];
							$POH_EFFECTIVEDATE_Y = $arr_temp[0] + 543;
							$POH_EFFECTIVEDATE = show_date_format($POH_EFFECTIVEDATE,1);
						} // end if
						$POH_ENDDATE = substr(trim($data2[POH_ENDDATE]), 0, 10);
						$POH_ENDDATE_D = $POH_ENDDATE_M = $POH_ENDDATE_Y = "";
						if($POH_ENDDATE){
							$arr_temp = explode("-", $POH_ENDDATE);
							$POH_ENDDATE_D = $arr_temp[2];
							$POH_ENDDATE_M = $arr_temp[1];
							$POH_ENDDATE_Y = $arr_temp[0] + 543;
							$POH_ENDDATE = show_date_format($POH_ENDDATE,1);
						} // end if

						// === หาความผิดวินัย, ประเภทโทษทางวินัย 
						$cmd = " select PUN_TYPE, PEN_CODE
								from   PER_PUNISHMENT 
								where PER_ID=$PER_ID  
								order by PUN_STARTDATE desc ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$PUN_TYPE = trim($data2[PUN_TYPE]);
						$PEN_CODE = trim($data2[PEN_CODE]);
						$PEN_NAME = "";
						if ($PEN_CODE) {
							$cmd = " select PEN_NAME from PER_PENALTY where PEN_CODE='$PEN_CODE' ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$PEN_NAME = trim($data2[PEN_NAME]);
						} // end if
						
						// === หาเครื่องราชอิสริยาภรณ์สูงสุดที่ได้รับ 
						$cmd = " select DC_NAME
								from   PER_DECORATEHIS a, PER_DECORATION b
								where PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE
								order by DC_TYPE, DC_ORDER desc ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$DC_NAME = trim($data2[DC_NAME]);
						
						// === หาร้อยละที่ได้รับการเลื่อนเงินเดือน 
						$cmd = " select SAH_PERCENT_UP
								from   PER_SALARYHIS
								where PER_ID=$PER_ID 
								order by SAH_EFFECTIVEDATE desc ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$SAH_PERCENT_UP = trim($data2[SAH_PERCENT_UP]);
						
						$cmd = " select SUM_KPI, SUM_COMPETENCE from PER_KPI_FORM 
										where PER_ID = $PER_ID and KF_START_DATE >= '$budget_year_from'  and  KF_END_DATE <= '$budget_year_to'  ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$SUM_KPI = $data2[SUM_KPI];
						$SUM_COMPETENCE = $data2[SUM_COMPETENCE];

					} // end if
					
					$arr_content[$data_count][type] = "CONTENT";
					$arr_content[$data_count][order] = $data_row;
					$arr_content[$data_count][per_cardno] = $PER_CARDNO;
					$arr_content[$data_count][pn_name] = $PN_NAME;
					$arr_content[$data_count][per_name] = $PER_NAME;
					$arr_content[$data_count][per_surname] = $PER_SURNAME;
					$arr_content[$data_count][per_gender] = $PER_GENDER;
					$arr_content[$data_count][birthdate] = 	$PER_BIRTHDATE;			//	$BIRTHDATE_D.'/'.$BIRTHDATE_M.'/'.$BIRTHDATE_Y;
					$arr_content[$data_count][startdate] = 	$PER_STARTDATE;			// 	$STARTDATE_D.'/'.$STARTDATE_M.'/'.$STARTDATE_Y;
					$arr_content[$data_count][poems_no] = $POEMS_NO;
					$arr_content[$data_count][ep_name] = $EP_NAME;
					$arr_content[$data_count][ot_name] = $OT_NAME;
					$arr_content[$data_count][pv_name] = $PV_NAME;
					$arr_content[$data_count][poems_skill] = $POEMS_SKILL;
					$arr_content[$data_count][poems_south] = $POEMS_SOUTH;
					$arr_content[$data_count][org_name] = $ORG_NAME;
					$arr_content[$data_count][org_name_1] = $ORG_NAME_1;
					$arr_content[$data_count][org_name_2] = $ORG_NAME_2;
					$arr_content[$data_count][org_name_3] = $ORG_NAME_3;
					$arr_content[$data_count][org_name_4] = $ORG_NAME_4;
					$arr_content[$data_count][level_name] = $LEVEL_NAME;
//					$arr_content[$data_count][per_salary] = (($PER_SALARY)?number_format($PER_SALARY,0,'',','):"");
//					$arr_content[$data_count][per_mgtsalary] = (($PER_MGTSALARY)?number_format($PER_MGTSALARY,0,'',','):"");
					$arr_content[$data_count][per_salary] = $PER_SALARY;
					$arr_content[$data_count][per_mgtsalary] = $PER_MGTSALARY;
					$arr_content[$data_count][poh_effectivedate] = $POH_EFFECTIVEDATE;		//	$POH_EFFECTIVEDATE_D.'/'.$POH_EFFECTIVEDATE_M.'/'.$POH_EFFECTIVEDATE_Y;
					$arr_content[$data_count][poh_enddate] = $POH_ENDDATE;							//	$POH_ENDDATE_D.'/'.$POH_ENDDATE_M.'/'.$POH_ENDDATE_Y;
					$arr_content[$data_count][el_name] = $EL_NAME;
					$arr_content[$data_count][en_name] = $EN_NAME;
					$arr_content[$data_count][em_name] = $EM_NAME;
					$arr_content[$data_count][ins_name] = $INS_NAME;
					$arr_content[$data_count][ct_name_edu] = $CT_NAME_EDU;
					$arr_content[$data_count][mov_name] = $MOV_NAME;
					$arr_content[$data_count][ppt_name] = $PPT_NAME;
					$arr_content[$data_count][pef_name] = $PEF_NAME;
					$arr_content[$data_count][pps_name] = $PPS_NAME;
					$arr_content[$data_count][per_contact_count] = $PER_CONTACT_COUNT;
					$arr_content[$data_count][pun_type] = $PUN_TYPE;
					$arr_content[$data_count][pen_name] = $PEN_NAME;
					$arr_content[$data_count][dc_name] = $DC_NAME;
					$arr_content[$data_count][sah_percent_up] = $SAH_PERCENT_UP;
					$arr_content[$data_count][sum_kpi] = $SUM_KPI;
					$arr_content[$data_count][sum_competence] = $SUM_COMPETENCE;
					$arr_content[$data_count][per_disability] = $PER_DISABILITY;

					$data_count++;				
					//echo("data_count = ".$data_count."<BR>");								
				} // end if
			} // end if
		break;
	} // end switch case
} // end for

?>