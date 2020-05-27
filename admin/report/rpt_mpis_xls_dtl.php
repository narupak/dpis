<?
//echo("arr_rpt_order count = ".count($arr_rpt_order)."<BR>");
for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
//echo($rpt_order_index."<BR>");
	$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
	switch($REPORT_ORDER){
		case "POSNO" :
			//echo("POS_NO = ".$POS_NO."/data[POS_NO] = ".$data[POS_NO]."<BR>");
			if($POS_NO != trim($data[POS_NO])){
				if ($PER_TYPE == 1) $POS_NO = trim($data[POS_NO]);
				elseif ($PER_TYPE == 2) $POEM_NO = trim($data[POEM_NO]);
				elseif ($PER_TYPE == 3) $POEMS_NO = trim($data[POEMS_NO]);

				$addition_condition = generate_condition($rpt_order_index);
				//echo("arr_rpt_order count = ".count($arr_rpt_order)."<BR>");
				//echo("rpt_order_index = ".$rpt_order_index."/arr_rpt_order = ".(count($arr_rpt_order)-1)."<BR>");
				if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

				if($rpt_order_index == (count($arr_rpt_order) - 1)){	
					$data_row++;
					if ($PER_TYPE == 1) $POS_ID = $data[POS_ID];
					elseif ($PER_TYPE == 2) $POEM_ID = $data[POEM_ID];
					elseif ($PER_TYPE == 3) $POEMS_ID = $data[POEMS_ID];
					$CL_NAME = trim($data[CL_NAME]);
					$PT_NAME = trim($data[PT_NAME]);
					$PM_CODE = trim($data[PM_CODE]);
					$PM_NAME = trim($data[PM_NAME]);					
					if ($PER_TYPE == 1) {
						$PL_CODE = trim($data[PL_CODE]);
						$PL_NAME = trim($data[PL_NAME]);	
					} elseif ($PER_TYPE == 2) {
						$PL_CODE = trim($data[PN_CODE]);
						$PL_NAME = trim($data[PN_NAME]);	
						$PG_CODE = trim($data[PG_CODE]);
						$PG_CODE_SALARY = trim($data[PG_CODE_SALARY]);

						$cmd = " select PG_NAME from PER_POS_GROUP where PG_CODE='$PG_CODE' ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$PT_NAME = $data2[PG_NAME];
					} elseif ($PER_TYPE == 3) {
						$PL_CODE = trim($data[EP_CODE]);
						$PL_NAME = trim($data[EP_NAME]);	
					}
					$PV_CODE = trim($data[PV_CODE]);
					$PV_NAME = trim($data[PV_NAME]);
					$OT_CODE = trim($data[OT_CODE]);
					$OT_NAME = trim($data[OT_NAME]);
					$SKILL_CODE = trim($data[SKILL_CODE]);
					$SKILL_NAME = trim($data[SKILL_NAME]);					
					
					$ORG_CODE = substr(trim($data[ORG_CODE]),0,5);		
					$ORG_ID_2 = trim($data[ORG_ID]);
					$ORG_NAME_2 = trim($data[ORG_NAME]);			
					// === หาจังหวัดและประเทศตามโครงสร้าง
					if ($DPISDB == "odbc") 
						$cmd = " select a.PV_CODE, PV_NAME, a.CT_CODE, CT_NAME 
								from ( PER_ORG a 
									   left join PER_PROVINCE b on (a.PV_CODE=b.PV_CODE)
									) left join PER_COUNTRY c  on (a.CT_CODE=c.CT_CODE)
								where ORG_ID=$ORG_ID_2 ";
					elseif ($DPISDB == "oci8") 
						$cmd = " select a.PV_CODE, PV_NAME, a.CT_CODE, CT_NAME 
								from PER_ORG a, PER_PROVINCE b, PER_COUNTRY c 
								where ORG_ID=$ORG_ID_2 and a.PV_CODE=b.PV_CODE(+) and 
										a.CT_CODE=c.CT_CODE(+) ";
					elseif($DPISDB=="mysql")
						$cmd = " select a.PV_CODE, PV_NAME, a.CT_CODE, CT_NAME 
								from ( PER_ORG a 
									   left join PER_PROVINCE b on (a.PV_CODE=b.PV_CODE)
									) left join PER_COUNTRY c  on (a.CT_CODE=c.CT_CODE)
								where ORG_ID=$ORG_ID_2 ";
					if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
					$db_dpis3->send_cmd($cmd);
					//$db_dpis3->show_error();
					$data3 = $db_dpis3->get_array();
					$PV_CODE_ORG = trim($data3[PV_CODE]);
					$PV_NAME_ORG = trim($data3[PV_NAME]);
					$CT_CODE_ORG = trim($data3[CT_CODE]);
					$CT_NAME_ORG = trim($data3[CT_NAME]);
										
					unset($tmp_ORG_ID, $ORG_ID_search, $ORG_ID_3, $ORG_ID_4, $ORG_NAME_3, $ORG_NAME_4);
					$ORG_ID_3 = $tmp_ORG_ID[] = trim($data[ORG_ID_1]);
					$ORG_ID_4 = $tmp_ORG_ID[] = trim($data[ORG_ID_2]);
					$ORG_ID_search = implode(", ", $tmp_ORG_ID);
					$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID in ($ORG_ID_search) ";
					if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
					$db_dpis2->send_cmd($cmd);
					while ( $data2 = $db_dpis2->get_array() ) {
						$ORG_NAME_3 = ($ORG_ID_3 == trim($data2[ORG_ID]))? trim($data2[ORG_NAME]) : "$ORG_NAME_3";
						$ORG_NAME_4 = ($ORG_ID_4 == trim($data2[ORG_ID]))? trim($data2[ORG_NAME]) : "$ORG_NAME_4";
					}	// while
					$cmd = " select a.ORG_NAME, b.ORG_NAME as ORGNAME2, c.ORG_NAME as ORGNAME1
							from PER_ORG a, PER_ORG b, PER_ORG c
							where a.ORG_ID=$ORG_ID_2 and a.ORG_ID_REF=b.ORG_ID and b.ORG_ID_REF=c.ORG_ID";
					if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array(); 
					$ORG_NAME = trim($data2[ORGNAME1]);
					$ORG_NAME_1 = trim($data2[ORGNAME2]);
				
					$BIRTHDATE = "";
					if ($PER_TYPE == 1) 
						if ($DEPARTMENT_NAME=="กรมการปกครอง") $where = "PAY_ID=$POS_ID ";
						else $where = "POS_ID=$POS_ID ";
					elseif ($PER_TYPE == 2) $where = "POEM_ID=$POEM_ID ";
					elseif ($PER_TYPE == 3) $where = "POEMS_ID=$POEMS_ID ";
					if ($GPIS_FLAG == "DATA") $where .= " and PER_STATUS=1";
					$cmd = " select 	PER_ID, PER_GENDER, PER_BIRTHDATE, PER_STARTDATE, LEVEL_NO, 
									PER_SALARY, PER_MGTSALARY, PER_OFFNO, PER_CARDNO, PN_CODE, 
									PER_NAME, PER_SURNAME, PER_ENG_NAME, PER_ENG_SURNAME, 
									ORG_ID, MOV_CODE, PER_OCCUPYDATE, PER_POSDATE, PER_RETIREDATE, PER_DISABILITY, RE_CODE, 
									PER_UNION, PER_UNION2, PER_UNION3, PER_UNION4, PER_UNION5 
							from		PER_PERSONAL  
							where	$where ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PER_ID = $data2[PER_ID];
					$PER_GENDER = $data2[PER_GENDER];
					$PER_DISABILITY = $data2[PER_DISABILITY];
					$PER_BIRTHDATE = substr(trim($data2[PER_BIRTHDATE]), 0, 10);
					$BIRTHDATE_D = $BIRTHDATE_M = $BIRTHDATE_Y = $RETIREDATE_Y = "";
					if($PER_BIRTHDATE){
						$arr_temp = explode("-", $PER_BIRTHDATE);
						$BIRTHDATE_D = $arr_temp[2];
						$BIRTHDATE_M = $arr_temp[1];
						$BIRTHDATE_Y = $arr_temp[0] + 543;
						$RETIREDATE_Y = ("$BIRTHDATE_M-$BIRTHDATE_D" >= "10-01")?($arr_temp[0] + 543 + 60):($arr_temp[0] + 543 + 61);
						$BIRTHDATE = show_date_format($PER_BIRTHDATE,1);
					} // end if
					$STARTDATE = show_date_format($data2[PER_STARTDATE],1);
					$LEVEL_NO = trim($data2[LEVEL_NO]);
					if (substr($LEVEL_NO,0,1)=='O')	$LEVEL_GROUP = 'ทั่วไป';
					elseif (substr($LEVEL_NO,0,1)=='K')	$LEVEL_GROUP = 'วิชาการ';
					elseif (substr($LEVEL_NO,0,1)=='D')	$LEVEL_GROUP = 'อำนวยการ';
					elseif (substr($LEVEL_NO,0,1)=='M') 	$LEVEL_GROUP = 'บริหาร';
					$PER_SALARY = $data2[PER_SALARY];
					$PER_MGTSALARY = $data2[PER_MGTSALARY];
					$PER_OFFNO = trim($data2[PER_OFFNO]);
					$PER_CARDNO = trim($data2[PER_CARDNO]);
					$PN_CODE = trim($data2[PN_CODE]);
					$PER_NAME = trim($data2[PER_NAME]);
					$PER_SURNAME = trim($data2[PER_SURNAME]);
					$PER_ENG_NAME = trim($data2[PER_ENG_NAME]);
					$PER_ENG_SURNAME = trim($data2[PER_ENG_SURNAME]);
					$RESIGNDATE = trim($data2[PER_POSDATE]);
					
					$MOV_CODE = trim($data2[MOV_CODE]);
					$cmd = " select MOV_NAME from PER_MOVMENT where MOV_CODE='$MOV_CODE'";
					$db_dpis3->send_cmd($cmd);
					$data3 = $db_dpis3->get_array();
					$MOV_NAME = trim($data3[MOV_NAME]);
					
					$CLASS_NAME = "";
					$cmd = " select PN_NAME, RANK_FLAG from PER_PRENAME where PN_CODE='$PN_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PN_NAME = trim($data2[PN_NAME]);
					$RANK_FLAG = trim($data2[RANK_FLAG]);
					if ($RANK_FLAG==1) $CLASS_NAME = $PN_NAME;

					$cmd = " select LEVEL_NAME, LEVEL_SHORTNAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$LEVEL_NAME = trim($data2[LEVEL_NAME]);
					$LEVEL_SHORTNAME = trim($data2[LEVEL_SHORTNAME]);

					$cmd = " select SKILL_NAME from PER_SKILL where SKILL_CODE='$SKILL_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$SKILL_NAME = trim($data2[SKILL_NAME]);

					$cmd = " select POH_EFFECTIVEDATE as EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and trim(POH_LEVEL_NO)='$LEVEL_NO' order by EFFECTIVEDATE";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PROMOTEDATE = show_date_format($data2[EFFECTIVEDATE],1);

					$EL_CODE = $EL_NAME = $EN_NAME = $EM_NAME = $INS_CODE = $INS_NAME = $ST_CODE = $ST_NAME = $CT_CODE_EDU = $CT_NAME_EDU = "";
					if($PER_ID){
						if($DPISDB=="odbc"){
							// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
							$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE
									from 	((
												PER_EDUCATE a
												left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
											) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
											) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
									where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%4%' ";									
						}elseif($DPISDB=="oci8"){
							// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
							$cmd = "	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE  
									from 	PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
									where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%4%' and 
											a.EN_CODE=b.EN_CODE(+) and b.EL_CODE=c.EL_CODE(+) and 
											a.EM_CODE=d.EM_CODE(+) ";	
						}elseif($DPISDB=="mysql"){
							// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
							$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE
									from 	((
												PER_EDUCATE a
												left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
											) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
											) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
									where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%4%' ";	
						} // end if
						$count_educate = $db_dpis2->send_cmd($cmd);

						if(!$count_educate){
							if($DPISDB=="odbc"){
								// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
								$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE
										from 	((
													PER_EDUCATE a
													left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
												) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
										where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%' ";
							}elseif($DPISDB=="oci8"){
								// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
								$cmd = "	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE  
										from 	PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
										where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%' and 
												a.EN_CODE=b.EN_CODE(+) and b.EL_CODE=c.EL_CODE(+) and 
												a.EM_CODE=d.EM_CODE(+) ";
							}elseif($DPISDB=="mysql"){
								// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
								$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE
										from 	((
													PER_EDUCATE a
													left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
												) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
										where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%' ";
							} // end if
							$count_educate = $db_dpis2->send_cmd($cmd);
						}

						if(!$count_educate){
							if($DPISDB=="odbc"){
								// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
								$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE
										from 	((
													PER_EDUCATE a
													left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
												) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
										where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%1%' ";
							}elseif($DPISDB=="oci8"){
								// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
								$cmd = "	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE  
										from 	PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
										where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%1%' and 
												a.EN_CODE=b.EN_CODE(+) and b.EL_CODE=c.EL_CODE(+) and 
												a.EM_CODE=d.EM_CODE(+) ";
							}elseif($DPISDB=="mysql"){
								// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
								$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE
										from 	((
													PER_EDUCATE a
													left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
												) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
										where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%1%' ";
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
						
						if ($INS_CODE) {
							if($DPISDB=="odbc") {				
								// หาชื่อโรงเรียน และประเทศของโรงเรียน
								$cmd = " select INS_NAME, a.CT_CODE, CT_NAME 
										from   PER_INSTITUTE a 
											   left join PER_COUNTRY b on (a.CT_CODE=b.CT_CODE)
										where INS_CODE='$INS_CODE' ";
							} elseif ($DPISDB=="oci8") { 
								// หาชื่อโรงเรียน และประเทศของโรงเรียน
								$cmd = " select INS_NAME, a.CT_CODE, CT_NAME from PER_INSTITUTE a, PER_COUNTRY b 
										where INS_CODE='$INS_CODE' and a.CT_CODE=b.CT_CODE(+) ";
							}elseif($DPISDB=="mysql"){
								// หาชื่อโรงเรียน และประเทศของโรงเรียน
								$cmd = " select INS_NAME, a.CT_CODE, CT_NAME 
										from   PER_INSTITUTE a 
											   left join PER_COUNTRY b on (a.CT_CODE=b.CT_CODE)
										where INS_CODE='$INS_CODE' ";
							} 			
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$INS_NAME = trim($data2[INS_NAME]);
							$CT_CODE_EDU = trim($data2[CT_CODE]);
							$CT_NAME_EDU = trim($data2[CT_NAME]);
						} else {	
							$INS_NAME = trim($data2[EDU_INSTITUTE]);
						} // end if
						
						// === หาวันที่เงินเดือนมีผล 
						$SAH_EFFECTIVEDATE = "";
						$cmd = " select SAH_EFFECTIVEDATE
								from   PER_SALARYHIS
								where PER_ID=$PER_ID 
								order by SAH_EFFECTIVEDATE desc ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$SAH_EFFECTIVEDATE = show_date_format($data2[SAH_EFFECTIVEDATE],1);
						
						// === หาตำแหน่งล่าสุด เลขที่คำสั่ง, วันที่ออกคำสั่ง, วันที่มีผล
						$POH_DOCNO = $POH_DOCDATE = $POH_EFFECTIVEDATE = "";
						$cmd = " select POH_DOCNO, POH_DOCDATE, POH_EFFECTIVEDATE
								from   PER_POSITIONHIS
								where PER_ID=$PER_ID 
								order by POH_EFFECTIVEDATE desc ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$POH_DOCNO = trim($data2[POH_DOCNO]);
						$POH_DOCDATE = show_date_format($data2[POH_DOCDATE],1);
						$POH_EFFECTIVEDATE = show_date_format($data2[POH_EFFECTIVEDATE],1);
						
						// === หาชื่อทุน และแหล่งทุน
						$cmd = " select ST_NAME from   PER_SCHOLARTYPE 	where ST_CODE='$ST_CODE' ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$ST_NAME = trim($data2[ST_NAME]);
					} // end if
					
					// หาการฝึกอบรม/ดูงาน
					$TR_NAME = "";
					if($DPISDB=="odbc") {				
						$cmd = " select TR_NAME, TRN_COURSE_NAME 
								from   PER_TRAINING a 
									   left join PER_TRAIN b on (a.TR_CODE=b.TR_CODE)
								where PER_ID=$PER_ID
								order by TRN_STARTDATE DESC, TRN_ENDDATE DESC ";
					} elseif ($DPISDB=="oci8") { 
						$cmd = " select TR_NAME, TRN_COURSE_NAME from PER_TRAINING a, PER_TRAIN b 
								where PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE(+)
								order by TRN_STARTDATE DESC, TRN_ENDDATE DESC ";
					}elseif($DPISDB=="mysql"){
						$cmd = " select TR_NAME, TRN_COURSE_NAME 
								from   PER_TRAINING a 
									   left join PER_TRAIN b on (a.TR_CODE=b.TR_CODE)
								where PER_ID=$PER_ID
								order by TRN_STARTDATE DESC, TRN_ENDDATE DESC ";
					} 			
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$TR_NAME = trim($data2[TR_NAME]);
					$TRN_COURSE_NAME = trim($data2[TRN_COURSE_NAME]);
					if ($TRN_COURSE_NAME) $TR_NAME = $TRN_COURSE_NAME;

					// หาเครื่องราชฯ
					$DC_NAME = "";
					if($DPISDB=="odbc") {				
						$cmd = " select DC_NAME 
								from   PER_DECORATEHIS a 
									   left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
								where PER_ID=$PER_ID
								order by DEH_DATE DESC ";
					} elseif ($DPISDB=="oci8") { 
						$cmd = " select DC_NAME from PER_DECORATEHIS a, PER_DECORATION b 
								where PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE(+)
								order by DEH_DATE DESC ";
					}elseif($DPISDB=="mysql"){
						$cmd = " select DC_NAME 
								from   PER_DECORATEHIS a 
									   left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
								where PER_ID=$PER_ID
								order by DEH_DATE DESC ";
					} 			
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$DC_NAME = trim($data2[DC_NAME]);

					// หาวินัย
					$PEN_NAME = "";
					if($DPISDB=="odbc") {				
						$cmd = " select PUN_TYPE, PEN_NAME 
								from   PER_PUNISHMENT a 
									   left join PER_PENALTY b on (a.PEN_CODE=b.PEN_CODE)
								where PER_ID=$PER_ID
								order by PUN_STARTDATE DESC, PUN_ENDDATE DESC ";
					} elseif ($DPISDB=="oci8") { 
						$cmd = " select PUN_TYPE, PEN_NAME from PER_PUNISHMENT a, PER_PENALTY b 
								where PER_ID=$PER_ID and a.PEN_CODE=b.PEN_CODE(+)
								order by PUN_STARTDATE DESC, PUN_ENDDATE DESC ";
					}elseif($DPISDB=="mysql"){
						$cmd = " select PUN_TYPE, PEN_NAME 
								from   PER_PUNISHMENT a 
									   left join PER_PENALTY b on (a.PEN_CODE=b.PEN_CODE)
								where PER_ID=$PER_ID
								order by PUN_STARTDATE DESC, PUN_ENDDATE DESC ";
					} 			
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PUN_TYPE = trim($data2[PUN_TYPE]);
					$PEN_NAME = trim($data2[PEN_NAME]);

					$arr_content[$data_count][type] = "CONTENT";
					$arr_content[$data_count][order] = $data_row;
					$arr_content[$data_count][org_code] = $ORG_CODE;
					$arr_content[$data_count][per_cardno] = $PER_CARDNO;
					$arr_content[$data_count][pn_code] = $PN_CODE;
					$arr_content[$data_count][pn_name] = $PN_NAME;
					$arr_content[$data_count][per_name] = $PER_NAME;
					$arr_content[$data_count][per_surname] = $PER_SURNAME;
					$arr_content[$data_count][per_gender] = $PER_GENDER;
					$arr_content[$data_count][birthdate] = 	$BIRTHDATE;	
					$arr_content[$data_count][startdate] = $STARTDATE;
					if ($PER_TYPE == 1) $arr_content[$data_count][pos_no] = $POS_NO;
					elseif ($PER_TYPE == 2) $arr_content[$data_count][pos_no] = $POEM_NO;
					elseif ($PER_TYPE == 3) $arr_content[$data_count][pos_no] = $POEMS_NO;
					$arr_content[$data_count][level_group] = $LEVEL_GROUP;
					$arr_content[$data_count][pm_code] = $PM_CODE;
					$arr_content[$data_count][pm_name] = $PM_NAME;
					$arr_content[$data_count][pl_code] = $PL_CODE;
					$arr_content[$data_count][pl_name] = $PL_NAME;
					$arr_content[$data_count][pt_name] = $PT_NAME;
					$arr_content[$data_count][ot_code] = $OT_CODE;
					$arr_content[$data_count][ot_name] = $OT_NAME;
					$arr_content[$data_count][cl_code] = $CL_CODE;
					$arr_content[$data_count][cl_name] = $CL_NAME;
					$arr_content[$data_count][skill_code] = $SKILL_CODE;
					$arr_content[$data_count][skill_name] = $SKILL_NAME;
					$arr_content[$data_count][pv_code_org] = $PV_CODE_ORG;
					$arr_content[$data_count][pv_name_org] = $PV_NAME_ORG;
					$arr_content[$data_count][ct_code_org] = $CT_CODE_ORG;
					$arr_content[$data_count][ct_name_org] = $CT_NAME_ORG;					
					$arr_content[$data_count][org_name] = $ORG_NAME;
					$arr_content[$data_count][org_name_1] = $ORG_NAME_1;
					$arr_content[$data_count][org_name_2] = $ORG_NAME_2;
					$arr_content[$data_count][org_name_3] = $ORG_NAME_3;
					$arr_content[$data_count][org_name_4] = $ORG_NAME_4;
					$arr_content[$data_count][level_no] = level_no_format($LEVEL_NO);
					$arr_content[$data_count][level_name] = $LEVEL_NAME;
					$arr_content[$data_count][level_shortname] = $LEVEL_SHORTNAME;
					$arr_content[$data_count][sah_effectivedate] = 	$SAH_EFFECTIVEDATE;	
//					$arr_content[$data_count][per_salary] = (($PER_SALARY)?number_format($PER_SALARY,0,'',','):"");
//					$arr_content[$data_count][per_mgtsalary] = (($PER_MGTSALARY)?number_format($PER_MGTSALARY,0,'',','):"");
					$arr_content[$data_count][per_salary] = $PER_SALARY;
					$arr_content[$data_count][per_mgtsalary] = $PER_MGTSALARY;
					$arr_content[$data_count][poh_docno] = $POH_DOCNO;
					$arr_content[$data_count][poh_docdate] = $POH_DOCDATE;
					$arr_content[$data_count][poh_effectivedate] = $POH_EFFECTIVEDATE;	
					$arr_content[$data_count][el_code] = $EL_CODE;
					$arr_content[$data_count][el_name] = $EL_NAME;
					$arr_content[$data_count][en_name] = $EN_NAME;
					$arr_content[$data_count][em_name] = $EM_NAME;
					$arr_content[$data_count][ins_name] = $INS_NAME;
					$arr_content[$data_count][ct_code_edu] = $CT_CODE_EDU;
					$arr_content[$data_count][ct_name_edu] = $CT_NAME_EDU;
					$arr_content[$data_count][st_code] = $ST_CODE;
					$arr_content[$data_count][st_name] = $ST_NAME;
					$arr_content[$data_count][sch_name] = $SCH_NAME;
					$arr_content[$data_count][mov_code] = $MOV_CODE;
					$arr_content[$data_count][mov_name] = $MOV_NAME;
					$arr_content[$data_count][promotedate] = $PROMOTEDATE;	
					$arr_content[$data_count][resigndate] = $RESIGNDATE;
					$arr_content[$data_count][tr_name] = $TR_NAME;
					$arr_content[$data_count][dc_name] = $DC_NAME;
					$arr_content[$data_count][pun_type] = $PUN_TYPE;
					$arr_content[$data_count][pen_name] = $PEN_NAME;
					$arr_content[$data_count][pg_code_salary] = $PG_CODE_SALARY;

					$data_count++;				
					//echo("data_count = ".$data_count."<BR>");								
				} // end if
			} // end if
		break;
	} // end switch case
} // end for

?>