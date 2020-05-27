<?
for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
	$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
	switch($REPORT_ORDER){
		case "POSNO" :
			if($POS_NO != trim($data[POS_NO])){
				$POS_NO = trim($data[POS_NO]);

				$addition_condition = generate_condition($rpt_order_index);

				if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

				if($rpt_order_index == (count($arr_rpt_order) - 1)){	
					$data_row++;
					$POS_ID = $data[POS_ID];
					$CL_NAME = trim($data[CL_NAME]);
					$PT_NAME = trim($data[PT_NAME]);
					$PM_CODE = trim($data[PM_CODE]);
					$PM_NAME = trim($data[PM_NAME]);					
					$PL_CODE = trim($data[PL_CODE]);
					$PL_NAME = trim($data[PL_NAME]);	
					$PV_CODE = trim($data[PV_CODE]);
					$PV_NAME = trim($data[PV_NAME]);
					$OT_CODE = trim($data[OT_CODE]);
					$OT_NAME = trim($data[OT_NAME]);
					
					$ORG_CODE = substr(trim($data[ORG_CODE]),0,5);		// รหัสส่วนราชการระดับสำนัก/กอง
					$ORG_ID_2 = trim($data[ORG_ID]);
					$ORG_NAME_2 = trim($data[ORG_NAME]);			// ชื่อสำนัก/กอง
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
					$db_dpis3->send_cmd($cmd);
					$data3 = $db_dpis3->get_array();
					$PV_CODE_ORG = trim($data3[PV_CODE]);
					$PV_NAME_ORG = trim($data3[PV_NAME]);
					$CT_CODE_ORG = trim($data3[CT_CODE]);
					$CT_NAME_ORG = trim($data3[CT_NAME]);
										
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
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array(); 
					$ORG_NAME = trim($data2[ORGNAME1]);
					$ORG_NAME_1 = trim($data2[ORGNAME2]);
				
					$cmd = " select 	PER_ID, PER_GENDER, PER_BIRTHDATE, PER_STARTDATE, LEVEL_NO, 
									PER_SALARY, PER_MGTSALARY, PER_OFFNO, PER_CARDNO, PN_CODE, 
									PER_NAME, PER_SURNAME, PER_ENG_NAME, PER_ENG_SURNAME, 
									ORG_ID, MOV_CODE 
							from		PER_PERSONAL
							where	POS_ID=$POS_ID and PER_STATUS=1  ";
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
//								$BIRTHDATE_Y = substr(($arr_temp[0] + 543), -2);
						$BIRTHDATE_Y = $arr_temp[0] + 543;
						$RETIREDATE_Y = ("$BIRTHDATE_M-$BIRTHDATE_D" >= "10-01")?($arr_temp[0] + 543 + 60):($arr_temp[0] + 543 + 61);
					} // end if
					$PER_STARTDATE = substr(trim($data2[PER_STARTDATE]), 0, 10);
					$STARTDATE_D = $STARTDATE_M = $STARTDATE_Y = "";
					if($PER_STARTDATE){
						$arr_temp = explode("-", $PER_STARTDATE);
						$STARTDATE_D = $arr_temp[2];
						$STARTDATE_M = $arr_temp[1];
//								$STARTDATE_Y = substr(($arr_temp[0] + 543), -2);
						$STARTDATE_Y = $arr_temp[0] + 543;
					} // end if
					$LEVEL_NO = trim($data2[LEVEL_NO]);
					$PER_SALARY = $data2[PER_SALARY];
					$PER_MGTSALARY = $data2[PER_MGTSALARY];
					$PER_OFFNO = $data2[PER_OFFNO];
					$PER_CARDNO = $data2[PER_CARDNO];
					$PN_CODE = $data2[PN_CODE];
					$PER_NAME = trim($data2[PER_NAME]);
					$PER_SURNAME = trim($data2[PER_SURNAME]);
					$PER_ENG_NAME = trim($data2[PER_ENG_NAME]);
					$PER_ENG_SURNAME = trim($data2[PER_ENG_SURNAME]);
					
					$ORG_ID_ASS = trim($data2[ORG_ID]);
					if ($ORG_ID_ASS) {
						// === หาจังหวัดและประเทศตามมอบหมายงาน
						if ($DPISDB == "odbc") 
							$cmd = " select a.PV_CODE, PV_NAME, a.CT_CODE, CT_NAME 
									from ( PER_ORG a 
										   left join PER_PROVINCE b on (a.PV_CODE=b.PV_CODE)
										) left join PER_COUNTRY c  on (a.CT_CODE=c.CT_CODE)
									where ORG_ID=$ORG_ID_ASS ";
						elseif ($DPISDB == "oci8") 
							$cmd = " select a.PV_CODE, PV_NAME, a.CT_CODE, CT_NAME 
									from PER_ORG a, PER_PROVINCE b, PER_COUNTRY c 
									where ORG_ID=$ORG_ID_ASS and a.PV_CODE=b.PV_CODE(+) and 
											a.CT_CODE=c.CT_CODE(+) ";
						elseif($DPISDB=="mysql")
							$cmd = " select a.PV_CODE, PV_NAME, a.CT_CODE, CT_NAME 
									from ( PER_ORG a 
										   left join PER_PROVINCE b on (a.PV_CODE=b.PV_CODE)
										) left join PER_COUNTRY c  on (a.CT_CODE=c.CT_CODE)
									where ORG_ID=$ORG_ID_ASS ";
						$db_dpis3->send_cmd($cmd);
						$data3 = $db_dpis3->get_array();
						$PV_CODE_ORG = trim($data3[PV_CODE]);
						$PV_NAME_ORG = trim($data3[PV_NAME]);
						$CT_CODE_ORG = trim($data3[CT_CODE]);
						$CT_NAME_ORG = trim($data3[CT_NAME]);
					} else {
						$PV_CODE_ORG_ASS = $PV_CODE_ORG;
						$PV_NAME_ORG_ASS = $PV_NAME_ORG;						
						$CT_CODE_ORG_ASS = $CT_CODE_ORG;	
						$CT_NAME_ORG_ASS = $CT_NAME_ORG;							
					}  // end if ($ORG_ID_ASS)
					
					$MOV_CODE = trim($data2[MOV_CODE]);
					$cmd = " select MOV_NAME from PER_MOVMENT where MOV_CODE='$MOV_CODE' ";
					$db_dpis3->send_cmd($cmd);
					$data3 = $db_dpis3->get_array();
					$MOV_NAME = ($data3[MOV_NAME]);
					
					$RANK_NAME = "";
					$cmd = " select PN_NAME, RANK_FLAG from PER_PRENAME where PN_CODE='$PN_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PN_NAME = $data2[PN_NAME];
					$RANK_FLAG = $data2[RANK_FLAG];
					if ($RANK_FLAG==1) {
						$RANK_NAME = $PN_NAME;
						$PN_NAME = "";
					}

					$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$LEVEL_NAME = $data2[LEVEL_NAME];

					$EL_NAME = "";
					if($PER_ID){
						if($DPISDB=="odbc"){
							// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
							$cmd = " 	select 	c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE
									from 	((
												PER_EDUCATE a
												inner join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
											) inner join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
											) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
									where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%||4||%' ";									
						}elseif($DPISDB=="oci8"){
							// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
							$cmd = "	select 	c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE  
									from 	PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
									where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%||4||%' and 
											a.EN_CODE=b.EN_CODE and b.EL_CODE=c.EL_CODE and 
											a.EM_CODE=d.EM_CODE(+) ";	
						}elseif($DPISDB=="mysql"){
							// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
							$cmd = " 	select 	c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE
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
								$cmd = " 	select 	c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE
										from 	((
													PER_EDUCATE a
													inner join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
												) inner join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
										where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%||2||%' ";
							}elseif($DPISDB=="oci8"){
								// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
								$cmd = "	select 	c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE  
										from 	PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
										where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%||2||%' and 
												a.EN_CODE=b.EN_CODE and b.EL_CODE=c.EL_CODE and 
												a.EM_CODE=d.EM_CODE(+) ";
							}elseif($DPISDB=="mysql"){
								// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
								$cmd = " 	select 	c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE
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
								$cmd = " 	select 	c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE
										from 	((
													PER_EDUCATE a
													inner join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
												) inner join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
										where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%||1||%' ";
							}elseif($DPISDB=="oci8"){
								// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
								$cmd = "	select 	c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE  
										from 	PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
										where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%||1||%' and 
												a.EN_CODE=b.EN_CODE and b.EL_CODE=c.EL_CODE and 
												a.EM_CODE=d.EM_CODE(+) ";
							}elseif($DPISDB=="mysql"){
								// === หาการระดับการศึกษาสูงสุด สาขา โรงเรียน 
								$cmd = " 	select 	c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE
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
						$EL_NAME = trim($data2[EL_NAME]);
						$EN_NAME = trim($data2[EN_NAME]);
						$EM_NAME = trim($data2[EM_NAME]);
						$INS_CODE = trim($data2[INS_CODE]);
						
						$INS_NAME = $CT_NAME_EDU = "";
						if ($INS_CODE) {
							if($DPISDB=="odbc") {				
								// หาชื่อโรงเรียน และประเทศของโรงเรียน
								$cmd = " select INS_NAME, CT_NAME 
										from   PER_INSTITUTE a 
											   left join PER_COUNTRY b on (a.CT_CODE=b.CT_CODE)
										where INS_CODE='$INS_CODE' ";
							} elseif ($DPISDB=="oci8") { 
								// หาชื่อโรงเรียน และประเทศของโรงเรียน
								$cmd = " select INS_NAME, CT_NAME from PER_INSTITUTE a, PER_COUNTRY b 
										where INS_CODE='$INS_CODE' and a.CT_CODE=b.CT_CODE(+) ";
							}elseif($DPISDB=="mysql"){
								// หาชื่อโรงเรียน และประเทศของโรงเรียน
								$cmd = " select INS_NAME, CT_NAME 
										from   PER_INSTITUTE a 
											   left join PER_COUNTRY b on (a.CT_CODE=b.CT_CODE)
										where INS_CODE='$INS_CODE' ";
							} 			
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$INS_NAME = trim($data2[INS_NAME]);
							$CT_NAME_EDU = trim($data2[CT_NAME]);
							
						} // end if
						
						// === หาวันที่เงินเดือนมีผล 
						$SAH_EFFECTIVEDATE = "";
						$cmd = " select SAH_EFFECTIVEDATE
								from   PER_SALARYHIS
								where PER_ID=$PER_ID 
								order by SAH_EFFECTIVEDATE desc ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						if (trim($data2[SAH_EFFECTIVEDATE])) {
							$tmp_date = explode("-", substr(trim($data2[SAH_EFFECTIVEDATE]), 0, 10));
							$SAH_EFFECTIVEDATE = $tmp_date[2] ."/". $tmp_date[1] ."/". $tmp_date[0];
						}
						
						// === หาเงินเดือนย้อนหลัง 5 ปี 
						$UPDATE_DATE = date("Y-m-d H:i:s");
						$tmp_date = explode("-", substr(trim($UPDATE_DATE), 0, 10));
						$DATE5Y = ($tmp_date[0]-5) ."-". $tmp_date[1] ."-". $tmp_date[2];
						$PER_SALARY5Y = "";
						$cmd = " select SAH_SALARY
								from   PER_SALARYHIS
								where PER_ID=$PER_ID and SAH_EFFECTIVEDATE < '$DATE5Y' 
								order by SAH_EFFECTIVEDATE desc ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$PER_SALARY5Y = $data2[SAH_SALARY];
						
						// === หาตำแหน่งล่าสุด เลขที่คำสั่ง, วันที่ออกคำสั่ง, วันที่มีผล
						$POH_DOCNO = $POH_DOCDATE = $POH_EFFECTIVEDATE = "";
						$cmd = " select POH_DOCNO, POH_DOCDATE, POH_EFFECTIVEDATE, LEVEL_NO
								from   PER_POSITIONHIS
								where PER_ID=$PER_ID 
								order by POH_EFFECTIVEDATE desc ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$POH_DOCNO = trim($data2[POH_DOCNO]);
						$POH_DOCDATE = trim($data2[POH_DOCDATE]);
						if ($POH_DOCDATE) {
							$tmp_date = explode("-", substr(trim($data2[POH_DOCDATE]), 0, 10));	
							$POH_DOCDATE = $tmp_date[2] ."/". $tmp_date[1] ."/". ($tmp_date[0] + 543);
						}
						$POH_EFFECTIVEDATE = trim($data2[POH_EFFECTIVEDATE]);
						if ($POH_EFFECTIVEDATE) {	
							$tmp_date = explode("-", substr(trim($data2[POH_EFFECTIVEDATE]), 0, 10));
							$POH_EFFECTIVEDATE = $tmp_date[2] ."/". $tmp_date[1] ."/". ($tmp_date[0] + 543);
						}
//						$LEVEL_NO = trim($data2[LEVEL_NO]);
						
						// === หาตำแหน่งที่บรรจุ
						$START_PL_NAME = "";
						$cmd = " select PL_CODE, LEVEL_NO
										from   PER_POSITIONHIS a, PER_MOVMENT b
										where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and MOV_SUB_TYPE=1
								order by POH_EFFECTIVEDATE";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$START_PL_CODE = trim($data2[PL_CODE]);
						$START_LEVEL_NO = trim($data2[LEVEL_NO]);

						$cmd = " select PL_NAME from PER_LINE where PL_CODE='$START_PL_CODE' ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$START_PL_NAME = $data2[PL_NAME];
						
						// === BEG_C_DATE
						$cmd = " select MIN(POH_EFFECTIVEDATE) as MIN_POH_EFFECTIVEDATE,LEVEL_NO
								from   PER_POSITIONHIS
								where PER_ID=$PER_ID and LEVEL_NO <= '11'
								group by LEVEL_NO,POH_EFFECTIVEDATE
								order by LEVEL_NO desc,MIN(POH_EFFECTIVEDATE) desc";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						//echo "<br>$cmd<br>";
						$BEG_C_DATE = trim($data2[MIN_POH_EFFECTIVEDATE]);
						$BEG_LEVEL_NO = trim($data2[LEVEL_NO]);
						if ($BEG_C_DATE) {	
							$tmp_date = explode("-", substr(trim($BEG_C_DATE), 0, 10));
							$BEG_C_DATE = $tmp_date[2] ."/". $tmp_date[1] ."/". ($tmp_date[0] + 543);
						}

						// === BEG_BEF_C_DATE
						$cmd = " select MIN(POH_EFFECTIVEDATE) as MIN_POH_EFFECTIVEDATE,LEVEL_NO
								from   PER_POSITIONHIS
								where PER_ID=$PER_ID and LEVEL_NO < '$BEG_LEVEL_NO'
								group by LEVEL_NO,POH_EFFECTIVEDATE
								order by LEVEL_NO desc,MIN(POH_EFFECTIVEDATE) desc";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						//echo "<br>$cmd<br>";
						$BEG_BEF_C_DATE = trim($data2[MIN_POH_EFFECTIVEDATE]);
						if ($BEG_BEF_C_DATE) {	
							$tmp_date = explode("-", substr(trim($BEG_BEF_C_DATE), 0, 10));
							$BEG_BEF_C_DATE = $tmp_date[2] ."/". $tmp_date[1] ."/". ($tmp_date[0] + 543);
						}

						// === หาเครื่องราชย์ชั้นสุดท้าย
						$DC_NAME = $DEH_DATE = "";
						$cmd = " select DC_SHORTNAME, DEH_DATE, DEH_POSITION, DEH_ORG
												from   PER_DECORATEHIS a, PER_DECORATION b
												where PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE and DC_FLAG = 1 
								order by DEH_DATE desc ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$DC_NAME = trim($data2[DC_SHORTNAME]);
						$DEH_DATE = trim($data2[DEH_DATE]);
						if ($DEH_DATE) {	
							$tmp_date = explode("-", substr(trim($data2[DEH_DATE]), 0, 10));
							$DEH_DATE = $tmp_date[2] ."/". $tmp_date[1] ."/". ($tmp_date[0] + 543);
						}

						// === หาชื่อทุน และแหล่งทุน
						$SCH_NAME = $ST_NAME = "";
						$cmd = " select SCH_NAME, ST_NAME
								from   PER_SCHOLAR a, PER_SCHOLARSHIP b, PER_SCHOLARTYPE c
								where PER_ID=$PER_ID and a.SCH_CODE=b.SCH_CODE and 
									   b.ST_CODE=c.ST_CODE
								order by a.SC_ID desc ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$SCH_NAME = trim($data2[SCH_NAME]);
						$ST_NAME = trim($data2[ST_NAME]);

						// === ชื่อเดิม
						$OLD_NAME = "";
						$cmd = " select NH_NAME, NH_SURNAME
								from   PER_NAMEHIS
								where PER_ID=$PER_ID 
								order by NH_DATE desc ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$NH_NAME = trim($data2[NH_NAME]);
						$NH_SURNAME = trim($data2[NH_SURNAME]);
						$OLD_NAME = $NH_NAME . " " . $NH_SURNAME;

						$cmd = " select EX_NAME, PMH_AMT from PER_POS_MGTSALARYHIS a, PER_EXTRATYPE b 
										  where trim(a.EX_CODE)=trim(b.EX_CODE) and PER_ID=$PER_ID and PMH_ACTIVE = 1 and 
										(PMH_ENDDATE is NULL or PMH_ENDDATE >= '$UPDATE_DATE') and MGT_FLAG = 1
										  order by EX_SEQ_NO, b.EX_CODE ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$PER_MGTSALARY = $data2[PMH_AMT];
					} // end if
					
					if ($PER_ID) {
						$arr_content[$data_count][type] = "CONTENT";
						$arr_content[$data_count][order] = $data_row;
						$arr_content[$data_count][org_code] = $ORG_CODE;
						$arr_content[$data_count][per_offno] = $PER_OFFNO;
						$arr_content[$data_count][per_cardno] = $PER_CARDNO;
						$arr_content[$data_count][pn_name] = $PN_NAME;
						$arr_content[$data_count][rank_name] = $RANK_NAME;
						$arr_content[$data_count][per_name] = $PER_NAME;
						$arr_content[$data_count][per_surname] = $PER_SURNAME;
						$arr_content[$data_count][per_eng_name] = $PER_ENG_NAME."  ".$PER_ENG_SURNAME;
						$arr_content[$data_count][per_gender] = $PER_GENDER;
						$arr_content[$data_count][birthdate] = $BIRTHDATE_D.$BIRTHDATE_M.$BIRTHDATE_Y;
						$arr_content[$data_count][startdate] = $STARTDATE_D.$STARTDATE_M.$STARTDATE_Y;
						$arr_content[$data_count][start_level_no] = level_no_format($START_LEVEL_NO);
						$arr_content[$data_count][pos_no] = $POS_NO;
						$arr_content[$data_count][pm_code] = $PM_CODE;
						$arr_content[$data_count][pm_name] = $PM_NAME;
						$arr_content[$data_count][pl_code] = $PL_CODE;
						$arr_content[$data_count][pl_name] = $PL_NAME;
						$arr_content[$data_count][start_pl_name] = $START_PL_NAME;
						$arr_content[$data_count][pt_name] = $PT_NAME;
						$arr_content[$data_count][ot_code] = $OT_CODE;
						$arr_content[$data_count][ot_name] = $OT_NAME;
						$arr_content[$data_count][cl_code] = $CL_CODE;
						$arr_content[$data_count][cl_name] = $CL_NAME;
						$arr_content[$data_count][pv_code_org] = $PV_CODE_ORG;
						$arr_content[$data_count][pv_name_org] = $PV_NAME_ORG;
						$arr_content[$data_count][ct_code_org] = $CT_CODE_ORG;
						$arr_content[$data_count][ct_name_org] = $CT_NAME_ORG;					
						$arr_content[$data_count][pv_code_org_ass] = $PV_CODE_ORG_ASS;
						$arr_content[$data_count][pv_name_org_ass] = $PV_NAME_ORG_ASS;
						$arr_content[$data_count][ct_code_org_ass] = $CT_CODE_ORG_ASS;
						$arr_content[$data_count][ct_name_org_ass] = $CT_NAME_ORG_ASS;
						$arr_content[$data_count][org_name] = $ORG_NAME;
						$arr_content[$data_count][org_name_1] = $ORG_NAME_1;
						$arr_content[$data_count][org_name_2] = $ORG_NAME_2;
						$arr_content[$data_count][org_name_3] = $ORG_NAME_3;
						$arr_content[$data_count][org_name_4] = $ORG_NAME_4;
						$arr_content[$data_count][level_no] = level_no_format($BEG_LEVEL_NO);
						$arr_content[$data_count][sah_effectivedate] = $SAH_EFFECTIVEDATE;
						$arr_content[$data_count][per_salary] = (($PER_SALARY)?number_format($PER_SALARY,0,'',','):"");
						$arr_content[$data_count][per_salary5y] = (($PER_SALARY5Y)?number_format($PER_SALARY5Y,0,'',','):"");
						$arr_content[$data_count][per_mgtsalary] = (($PER_MGTSALARY)?number_format($PER_MGTSALARY,0,'',','):"");
						$arr_content[$data_count][poh_docno] = $POH_DOCNO;
						$arr_content[$data_count][poh_docdate] = $POH_DOCDATE;
						$arr_content[$data_count][poh_effectivedate] = $POH_EFFECTIVEDATE;
						$arr_content[$data_count][el_name] = $EL_NAME;
						$arr_content[$data_count][en_name] = $EN_NAME;
						$arr_content[$data_count][em_name] = $EM_NAME;
						$arr_content[$data_count][ins_name] = $INS_NAME;
						$arr_content[$data_count][ct_name_edu] = $CT_NAME_EDU;
						$arr_content[$data_count][st_name] = $ST_NAME;
						$arr_content[$data_count][sch_name] = $SCH_NAME;
						$arr_content[$data_count][mov_name] = $MOV_NAME;
						$arr_content[$data_count][dc_name] = $DC_NAME;
						$arr_content[$data_count][deh_date] = $DEH_DATE;
						$arr_content[$data_count][level_name] = $LEVEL_NO;
						$arr_content[$data_count][beg_c_date] = $BEG_C_DATE;
						$arr_content[$data_count][beg_bef_c_date] = $BEG_BEF_C_DATE;
						$arr_content[$data_count][remark] = $OLD_NAME;
	
						$data_count++;														
					} // end if
				} // end if
			} // end if
		break;
	} // end switch case
} // end for
?>