<?php
	$report_code = "data ����Ҫ��� Flow In";
	
	// ===== select data =====
	$cmd = " select 	PER_ID, PER_GENDER, PER_BIRTHDATE, PER_STARTDATE, LEVEL_NO, 
					PER_SALARY, PER_MGTSALARY, PER_OFFNO, PER_CARDNO, PN_CODE, 
					PER_NAME, PER_SURNAME, PER_ENG_NAME, PER_ENG_SURNAME, 
					ORG_ID, MOV_CODE, POS_ID, PER_OCCUPYDATE, PER_POSDATE, PER_RETIREDATE, 
					PER_DISABILITY, RE_CODE, PER_UNION, PER_UNION2, PER_UNION3, PER_UNION4, PER_UNION5 
			from		PER_PERSONAL  
			where	PER_TYPE=1 and PER_OCCUPYDATE >= '$budget_year_from' and PER_OCCUPYDATE <= '$budget_year_to' ";
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd ($count_data)<br>";
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	initialize_parameter(0);

	$f_new = false;
	$fname= "../../Excel/tmp/rpt_gmis_xls2";
	$fname1 = $fname.".xls";
//	$fnum = 0; $fnumtext="";
	$workbook = new writeexcel_workbook($fname1);

	//====================== SET FORMAT ======================//
//	require_once "../../Excel/my.defined_format.inc.set_format.php";	// use set_format(set of parameter) funtion , help is in file
	require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
	//====================== SET FORMAT ======================//

	$ORG_ID_REF = -1;
	$arr_content = "";
	while($data = $db_dpis->get_array()){
		$PER_ID = $data[PER_ID];
		$PER_GENDER = $data[PER_GENDER];
		$PER_DISABILITY = $data[PER_DISABILITY];
		$PER_BIRTHDATE = substr(trim($data[PER_BIRTHDATE]), 0, 10);
		$BIRTHDATE_D = $BIRTHDATE_M = $BIRTHDATE_Y = $RETIREDATE_Y = "";
		if($PER_BIRTHDATE){
			$arr_temp = explode("-", $PER_BIRTHDATE);
			$BIRTHDATE_D = $arr_temp[2];
			$BIRTHDATE_M = $arr_temp[1];
			$BIRTHDATE_Y = $arr_temp[0] + 543;
			$BIRTHDATE = show_date_format($PER_BIRTHDATE,1);
			$RETIREDATE_Y = ("$BIRTHDATE_M-$BIRTHDATE_D" >= "10-01")?($arr_temp[0] + 543 + 60):($arr_temp[0] + 543 + 61);
		} // end if
		$STARTDATE = show_date_format($data[PER_STARTDATE],1);
		$FLOWDATE = show_date_format($data[PER_OCCUPYDATE],1);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		if (substr($LEVEL_NO,0,1)=='O')	$LEVEL_GROUP = '�����';
		elseif (substr($LEVEL_NO,0,1)=='K')	$LEVEL_GROUP = '�Ԫҡ��';
		elseif (substr($LEVEL_NO,0,1)=='D')	$LEVEL_GROUP = '�ӹ�¡��';
		elseif (substr($LEVEL_NO,0,1)=='M') 	$LEVEL_GROUP = '������';
		$PER_SALARY = $data[PER_SALARY];
		$PER_MGTSALARY = $data[PER_MGTSALARY];
		$PER_OFFNO = $data[PER_OFFNO];
		$PER_CARDNO = $data[PER_CARDNO];
		$PN_CODE = $data[PN_CODE];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PER_ENG_NAME = $data[PER_ENG_NAME];
		$PER_ENG_SURNAME = $data[PER_ENG_SURNAME];
		$PER_UNION = $data[PER_UNION];
		$PER_UNION2 = $data[PER_UNION2];
		$PER_UNION3 = $data[PER_UNION3];
		$PER_UNION4 = $data[PER_UNION4];
		$PER_UNION5 = $data[PER_UNION5];
		$RE_CODE = trim($data[RE_CODE]);

		$ORG_ID_ASS = trim($data[ORG_ID]);
		if ($ORG_ID_ASS) {
			// === �Ҩѧ��Ѵ��л���ȵ���ͺ���§ҹ
			if ($DPISDB == "odbc") 
				$cmd = " select a.PV_CODE, PV_NAME, a.CT_CODE, CT_NAME 
						from ( PER_ORG_ASS a 
							   left join PER_PROVINCE b on (a.PV_CODE=b.PV_CODE)
							) left join PER_COUNTRY c  on (a.CT_CODE=c.CT_CODE)
						where ORG_ID=$ORG_ID_ASS ";
			elseif ($DPISDB == "oci8") 
				$cmd = " select a.PV_CODE, PV_NAME, a.CT_CODE, CT_NAME 
						from PER_ORG_ASS a, PER_PROVINCE b, PER_COUNTRY c 
						where ORG_ID=$ORG_ID_ASS and a.PV_CODE=b.PV_CODE(+) and 
								a.CT_CODE=c.CT_CODE(+) ";
			elseif($DPISDB=="mysql")
				$cmd = " select a.PV_CODE, PV_NAME, a.CT_CODE, CT_NAME 
						from ( PER_ORG_ASS a 
							   left join PER_PROVINCE b on (a.PV_CODE=b.PV_CODE)
							) left join PER_COUNTRY c  on (a.CT_CODE=c.CT_CODE)
						where ORG_ID=$ORG_ID_ASS ";
			$db_dpis3->send_cmd($cmd);
			$data3 = $db_dpis3->get_array();
			$PV_CODE_ORG_ASS = trim($data3[PV_CODE]);
			$PV_NAME_ORG_ASS = trim($data3[PV_NAME]);
			$CT_CODE_ORG_ASS = trim($data3[CT_CODE]);
			$CT_NAME_ORG_ASS = trim($data3[CT_NAME]);
		} else {
			$PV_CODE_ORG_ASS = $PV_CODE_ORG;
			$PV_NAME_ORG_ASS = $PV_NAME_ORG;						
			$CT_CODE_ORG_ASS = $CT_CODE_ORG;	
			$CT_NAME_ORG_ASS = $CT_NAME_ORG;							
		}  // end if ($ORG_ID_ASS)
		
		$MOV_CODE = trim($data[MOV_CODE]);
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

		$cmd = " select LEVEL_NAME, POSITION_TYPE from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$LEVEL_NAME = trim($data2[LEVEL_NAME]);
		$POSITION_TYPE = trim($data2[POSITION_TYPE]);

		$cmd = " select SKILL_NAME from PER_SKILL where SKILL_CODE='$SKILL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$SKILL_NAME = trim($data2[SKILL_NAME]);

		$cmd = " select RE_NAME from PER_RELIGION where RE_CODE='$RE_CODE' ";
		$db_dpis3->send_cmd($cmd);
		$data3 = $db_dpis3->get_array();
		$RE_NAME = trim($data3[RE_NAME]);

		$EL_CODE = $EL_NAME = $EN_NAME = $EM_NAME = $INS_CODE = $INS_NAME = $ST_CODE = $ST_NAME = $CT_CODE_EDU = $CT_NAME_EDU = "";
		$SAH_EFFECTIVEDATE = $POH_DOCNO = $POH_DOCDATE = $POH_EFFECTIVEDATE = $SCH_NAME = $ST_NAME = $DC_NAME = "";
		$RESULT1 = $RESULT2 = $PERCENT_SALARY1 = $PERCENT_SALARY2 = $UNION_CODE = "";

		$POS_ID = $data[POS_ID];
					
		if($DPISDB=="odbc"){
			$cmd = " select		
							b.ORG_ID_REF, $select_list, a.POS_ID, b.ORG_NAME, c.PL_NAME, d.PM_NAME, a.PT_CODE, 
							a.CL_NAME, f.PV_NAME, g.OT_NAME, b.ORG_CODE, a.PM_CODE, a.PL_CODE, b.PV_CODE, 
							b.OT_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, a.SKILL_CODE 
				 from		(
									(
										(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_LINE c on (a.PL_CODE=c.PL_CODE)
										) left join PER_MGT d on (a.PM_CODE=d.PM_CODE)
									) left join PER_PROVINCE f on (b.PV_CODE=f.PV_CODE)
								) left join PER_ORG_TYPE g on (b.OT_CODE=g.OT_CODE)
							where a.POS_ID = $POS_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select		
							b.ORG_ID_REF, $select_list, a.POS_ID, b.ORG_NAME, c.PL_NAME, d.PM_NAME, a.PT_CODE, 
							a.CL_NAME, g.OT_NAME, b.ORG_CODE, a.PM_CODE, a.PL_CODE, b.PV_CODE, f.PV_NAME, 
							b.OT_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, a.SKILL_CODE 
				from			PER_POSITION a, PER_ORG b, PER_LINE c, PER_MGT d, PER_PROVINCE f, PER_ORG_TYPE g
				where		a.POS_ID = $POS_ID and a.ORG_ID=b.ORG_ID and a.PL_CODE=c.PL_CODE and a.PM_CODE=d.PM_CODE(+) and 
							b.PV_CODE=f.PV_CODE(+) and b.OT_CODE=g.OT_CODE(+)  ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		
							b.ORG_ID_REF, $select_list, a.POS_ID, b.ORG_NAME, c.PL_NAME, d.PM_NAME, a.PT_CODE, 
							a.CL_NAME, f.PV_NAME, g.OT_NAME, b.ORG_CODE, a.PM_CODE, a.PL_CODE, b.PV_CODE, 
							b.OT_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, a.SKILL_CODE 
				 from		(
									(
										(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_LINE c on (a.PL_CODE=c.PL_CODE)
										) left join PER_MGT d on (a.PM_CODE=d.PM_CODE)
									) left join PER_PROVINCE f on (b.PV_CODE=f.PV_CODE)
								) left join PER_ORG_TYPE g on (b.OT_CODE=g.OT_CODE)
							where a.POS_ID = $POS_ID ";
		} // end if
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();

		$POS_NO = trim($data2[POS_NO]);
		$CL_NAME = trim($data2[CL_NAME]);
		$PT_CODE = trim($data2[PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE' ";
		$db_dpis3->send_cmd($cmd);
		$data3 = $db_dpis3->get_array();
		$PT_NAME = ($data3[PT_NAME]);

		$PM_CODE = trim($data2[PM_CODE]);
		$PM_NAME = trim($data2[PM_NAME]);					
		$PL_CODE = trim($data2[PL_CODE]);
		$PL_NAME = trim($data2[PL_NAME]);	
		$PV_CODE = trim($data2[PV_CODE]);
		$PV_NAME = trim($data2[PV_NAME]);
		$OT_CODE = trim($data2[OT_CODE]);
		$OT_NAME = trim($data2[OT_NAME]);
		$SKILL_CODE = trim($data2[SKILL_CODE]);
		$SKILL_NAME = trim($data2[SKILL_NAME]);					
				
		$ORG_CODE = substr(trim($data2[ORG_CODE]),0,5);	
		$ORG_ID_2 = trim($data2[ORG_ID]);
		$ORG_NAME_2 = trim($data2[ORG_NAME]);		
		// === �Ҩѧ��Ѵ��л���ȵ���ç���ҧ
		$CT_NAME_ORG = $PV_NAME_ORG = "";
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
		//$db_dpis3->show_error();
		$data3 = $db_dpis3->get_array();
		$PV_CODE_ORG = trim($data3[PV_CODE]);
		$PV_NAME_ORG = trim($data3[PV_NAME]);
		$CT_CODE_ORG = trim($data3[CT_CODE]);
		$CT_NAME_ORG = trim($data3[CT_NAME]);
										
		unset($tmp_ORG_ID, $ORG_ID_search, $ORG_ID_3, $ORG_ID_4, $ORG_NAME_3, $ORG_NAME_4);
		$ORG_ID_3 = $tmp_ORG_ID[] = trim($data2[ORG_ID_1]);
		$ORG_ID_4 = $tmp_ORG_ID[] = trim($data2[ORG_ID_2]);
		$ORG_ID_search = implode(", ", $tmp_ORG_ID);
		$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID in ($ORG_ID_search) ";
		$db_dpis2->send_cmd($cmd);
		while ( $data2 = $db_dpis2->get_array() ) {
			$ORG_NAME_3 = ($ORG_ID_3 == trim($data2[ORG_ID]))? trim($data2[ORG_NAME]) : "$ORG_NAME_3";
			$ORG_NAME_4 = ($ORG_ID_4 == trim($data2[ORG_ID]))? trim($data2[ORG_NAME]) : "$ORG_NAME_4";
		}	// while
		if ($DPISDB == "odbc") 
			$cmd = " select a.ORG_NAME, b.ORG_NAME as ORGNAME2, c.ORG_NAME as ORGNAME1 
					from ( PER_ORG a 
						   left join PER_ORG b on (a.ORG_ID_REF=b.ORG_ID)
						) left join PER_ORG c  on (b.ORG_ID_REF=c.ORG_ID)
					where a.ORG_ID=$ORG_ID_2  and a.ORG_ID_REF=b.ORG_ID and b.ORG_ID_REF=c.ORG_ID";
		elseif ($DPISDB == "oci8") 
			$cmd = " select a.ORG_NAME, b.ORG_NAME as ORGNAME2, c.ORG_NAME as ORGNAME1
					from PER_ORG a, PER_ORG b, PER_ORG c
					where a.ORG_ID=$ORG_ID_2 and a.ORG_ID_REF=b.ORG_ID and b.ORG_ID_REF=c.ORG_ID";
		elseif($DPISDB=="mysql")
			$cmd = " select a.ORG_NAME, b.ORG_NAME as ORGNAME2, c.ORG_NAME as ORGNAME1 
					from ( PER_ORG a 
						   left join PER_ORG b on (a.ORG_ID_REF=b.ORG_ID)
						) left join PER_ORG c  on (b.ORG_ID_REF=c.ORG_ID)
					where a.ORG_ID=$ORG_ID_2  and a.ORG_ID_REF=b.ORG_ID and b.ORG_ID_REF=c.ORG_ID";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array(); 
		$ORG_NAME = trim($data2[ORGNAME1]);
		$ORG_NAME_1 = trim($data2[ORGNAME2]);
				
		if($PER_ID){
			if($DPISDB=="odbc"){
				// === �ҡ���дѺ����֡���٧�ش �Ң� �ç���¹ 
				$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE
									from 	((
												PER_EDUCATE a
												left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
											) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
											) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
									where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%4%' ";									
			}elseif($DPISDB=="oci8"){
				// === �ҡ���дѺ����֡���٧�ش �Ң� �ç���¹ 
				$cmd = "	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE  
									from 	PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
									where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%4%' and 
											a.EN_CODE=b.EN_CODE(+) and b.EL_CODE=c.EL_CODE(+) and 
											a.EM_CODE=d.EM_CODE(+) ";	
			}elseif($DPISDB=="mysql"){
				// === �ҡ���дѺ����֡���٧�ش �Ң� �ç���¹ 
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
					// === �ҡ���дѺ����֡���٧�ش �Ң� �ç���¹ 
					$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE
										from 	((
													PER_EDUCATE a
													left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
												) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
										where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%' ";
				}elseif($DPISDB=="oci8"){
					// === �ҡ���дѺ����֡���٧�ش �Ң� �ç���¹ 
					$cmd = "	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE  
										from 	PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
										where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%' and 
												a.EN_CODE=b.EN_CODE(+) and b.EL_CODE=c.EL_CODE(+) and 
												a.EM_CODE=d.EM_CODE(+) ";
				}elseif($DPISDB=="mysql"){
					// === �ҡ���дѺ����֡���٧�ش �Ң� �ç���¹ 
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
					// === �ҡ���дѺ����֡���٧�ش �Ң� �ç���¹ 
					$cmd = " 	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE
										from 	((
													PER_EDUCATE a
													left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
												) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE) 
										where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%1%' ";
				}elseif($DPISDB=="oci8"){
					// === �ҡ���дѺ����֡���٧�ش �Ң� �ç���¹ 
					$cmd = "	select 	c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE  
										from 	PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
										where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%1%' and 
												a.EN_CODE=b.EN_CODE(+) and b.EL_CODE=c.EL_CODE(+) and 
												a.EM_CODE=d.EM_CODE(+) ";
				}elseif($DPISDB=="mysql"){
					// === �ҡ���дѺ����֡���٧�ش �Ң� �ç���¹ 
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
			$EM_NAME = stripslashes(trim($data2[EM_NAME]));
			$INS_CODE = trim($data2[INS_CODE]);
			$ST_CODE = trim($data2[ST_CODE]);
						
			if ($INS_CODE) {
				if($DPISDB=="odbc") {				
					// �Ҫ����ç���¹ ��л���Ȣͧ�ç���¹
					$cmd = " select INS_NAME, a.CT_CODE, CT_NAME 
										from   PER_INSTITUTE a 
											   left join PER_COUNTRY b on (a.CT_CODE=b.CT_CODE)
										where INS_CODE='$INS_CODE' ";
				} elseif ($DPISDB=="oci8") { 
					// �Ҫ����ç���¹ ��л���Ȣͧ�ç���¹
					$cmd = " select INS_NAME, a.CT_CODE, CT_NAME from PER_INSTITUTE a, PER_COUNTRY b 
										where INS_CODE='$INS_CODE' and a.CT_CODE=b.CT_CODE(+) ";
				}elseif($DPISDB=="mysql"){
					// �Ҫ����ç���¹ ��л���Ȣͧ�ç���¹
					$cmd = " select INS_NAME, a.CT_CODE, CT_NAME 
										from   PER_INSTITUTE a 
											   left join PER_COUNTRY b on (a.CT_CODE=b.CT_CODE)
										where INS_CODE='$INS_CODE' ";
				} 			
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$INS_NAME = stripslashes(trim($data2[INS_NAME]));
				$CT_CODE_EDU = trim($data2[CT_CODE]);
				$CT_NAME_EDU = trim($data2[CT_NAME]);
			} else {	
				$INS_NAME = trim($data2[EDU_INSTITUTE]);
			} // end if
						
			// === ���ѹ����Թ��͹�ռ� 
			$cmd = " select SAH_EFFECTIVEDATE
								from   PER_SALARYHIS
								where PER_ID=$PER_ID 
								order by SAH_EFFECTIVEDATE desc ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$SAH_EFFECTIVEDATE = show_date_format($data2[SAH_EFFECTIVEDATE],1);
						
			// === �ҵ��˹�����ش �Ţ�������, �ѹ����͡�����, �ѹ����ռ�
			$cmd = " select POH_DOCNO, POH_DOCDATE, POH_EFFECTIVEDATE
							from   PER_POSITIONHIS a, PER_MOVMENT b
							where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and 
							(MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
					order by POH_EFFECTIVEDATE desc ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_DOCNO = trim($data2[POH_DOCNO]);
			$POH_DOCDATE = show_date_format($data2[POH_DOCDATE],1);
			$POH_EFFECTIVEDATE = show_date_format($data2[POH_EFFECTIVEDATE],1);
			
			// === �Ҫ��ͷع ������觷ع
			if ($DPISDB == "odbc") 
				$cmd = " select SCH_NAME, ST_NAME 
						from ( PER_SCHOLAR a 
							   left join PER_SCHOLARSHIP b on (a.SCH_CODE=b.SCH_CODE)
							) left join PER_SCHOLARTYPE c  on (b.ST_CODE=c.ST_CODE)
						where PER_ID=$PER_ID ";
			elseif ($DPISDB == "oci8") 
				$cmd = " select SCH_NAME, ST_NAME
					from   PER_SCHOLAR a, PER_SCHOLARSHIP b, PER_SCHOLARTYPE c
					where PER_ID=$PER_ID and a.SCH_CODE=b.SCH_CODE and 
						   b.ST_CODE=c.ST_CODE
					order by a.SC_ID desc ";
			elseif($DPISDB=="mysql")
				$cmd = " select SCH_NAME, ST_NAME 
						from ( PER_SCHOLAR a 
							   left join PER_SCHOLARSHIP b on (a.SCH_CODE=b.SCH_CODE)
							) left join PER_SCHOLARTYPE c  on (b.ST_CODE=c.ST_CODE)
						where PER_ID=$PER_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$SCH_NAME = trim($data2[SCH_NAME]);
			$ST_NAME = trim($data2[ST_NAME]);

			// ������ͧ�Ҫ�
			if($DPISDB=="odbc") {				
				$cmd = " select DC_NAME 
						from   PER_DECORATEHIS a 
							   left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
						where PER_ID=$PER_ID
						order by DC_TYPE, DC_ORDER ";
			} elseif ($DPISDB=="oci8") { 
				$cmd = " select DC_NAME from PER_DECORATEHIS a, PER_DECORATION b 
						where PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE(+)
						order by DC_TYPE, DC_ORDER ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select DC_NAME 
						from   PER_DECORATEHIS a 
							   left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
						where PER_ID=$PER_ID
						order by DC_TYPE, DC_ORDER ";
			} 			
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DC_NAME = trim($data2[DC_NAME]);

			// === �������з�����Ѻ�������͹�Թ��͹ 
			$cmd = " select MOV_NAME, SAH_PERCENT_UP
							from   PER_SALARYHIS a, PER_MOVMENT b
							where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and SAH_KF_YEAR = '$search_budget_year' and 
										SAH_KF_CYCLE = 1 and substr(SAH_EFFECTIVEDATE, 6,5) = '04-01' and MOV_SUB_TYPE <> '0'
							order by SAH_EFFECTIVEDATE desc, SAH_CMD_SEQ desc, SAH_SALARY desc ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$RESULT1 = trim($data2[MOV_NAME]);
			$PERCENT_SALARY1 = trim($data2[SAH_PERCENT_UP]);
		
			$search_budget_year2 = $search_budget_year - 1;
			$cmd = " select MOV_NAME, SAH_PERCENT_UP
							from   PER_SALARYHIS a, PER_MOVMENT b
							where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and SAH_KF_YEAR = '$search_budget_year2' and 
										SAH_KF_CYCLE = 2  and substr(SAH_EFFECTIVEDATE, 6,5) = '10-01' and MOV_SUB_TYPE <> '0'
							order by SAH_EFFECTIVEDATE desc, SAH_CMD_SEQ desc, SAH_SALARY desc ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$RESULT2 = trim($data2[MOV_NAME]);
			$PERCENT_SALARY2 = trim($data2[SAH_PERCENT_UP]);

			//���ѹ�������дѺ�Ѩ�غѹ
			$PROMOTEDATE = "";
			if ($MFA_FLAG==1 && $PM_CODE) 
				$cmd = " select POH_EFFECTIVEDATE
								from   PER_POSITIONHIS a, PER_MOVMENT b
								where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and LEVEL_NO='$LEVEL_NO' and PM_CODE='$PM_CODE' and  
											(MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
								order by POH_EFFECTIVEDATE ";
			else
				$cmd = " select POH_EFFECTIVEDATE
								from   PER_POSITIONHIS a, PER_MOVMENT b
								where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and LEVEL_NO='$LEVEL_NO' and 
											(MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
								order by POH_EFFECTIVEDATE ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			if (trim($data2[POH_EFFECTIVEDATE])) {	
				$PROMOTEDATE = show_date_format($data2[POH_EFFECTIVEDATE],1);
			}

			// === ���������Ҿ����Ҫ���
			$UNION_CODE = "22222";
			if ($PER_UNION==1 && $PER_UNION2!=1 && $PER_UNION3!=1 && $PER_UNION4!=1 && $PER_UNION5!=1) $UNION_CODE = "12222";
			elseif ($PER_UNION!=1 && $PER_UNION2==1 && $PER_UNION3!=1 && $PER_UNION4!=1 && $PER_UNION5!=1) $UNION_CODE = "21222";
			elseif ($PER_UNION!=1 && $PER_UNION2!=1 && $PER_UNION3==1 && $PER_UNION4!=1 && $PER_UNION5!=1) $UNION_CODE = "22122";
			elseif ($PER_UNION!=1 && $PER_UNION2!=1 && $PER_UNION3!=1 && $PER_UNION4==1 && $PER_UNION5!=1) $UNION_CODE = "22212";
			elseif ($PER_UNION!=1 && $PER_UNION2!=1 && $PER_UNION3!=1 && $PER_UNION4!=1 && $PER_UNION5==1) $UNION_CODE = "22221";
			elseif ($PER_UNION==1 && $PER_UNION2==1 && $PER_UNION3==1 && $PER_UNION4==1 && $PER_UNION5!=1) $UNION_CODE = "11112";
			elseif ($PER_UNION==1 && $PER_UNION2==1 && $PER_UNION3==1 && $PER_UNION4!=1 && $PER_UNION5!=1) $UNION_CODE = "11122";
			elseif ($PER_UNION==1 && $PER_UNION2==1 && $PER_UNION3!=1 && $PER_UNION4!=1 && $PER_UNION5!=1) $UNION_CODE = "11222";
			elseif ($PER_UNION==1 && $PER_UNION2==1 && $PER_UNION3!=1 && $PER_UNION4==1 && $PER_UNION5!=1) $UNION_CODE = "11212";
			elseif ($PER_UNION==1 && $PER_UNION2!=1 && $PER_UNION3==1 && $PER_UNION4!=1 && $PER_UNION5!=1) $UNION_CODE = "12122";
			elseif ($PER_UNION==1 && $PER_UNION2!=1 && $PER_UNION3==1 && $PER_UNION4==1 && $PER_UNION5!=1) $UNION_CODE = "12112";
			elseif ($PER_UNION==1 && $PER_UNION2!=1 && $PER_UNION3!=1 && $PER_UNION4==1 && $PER_UNION5!=1) $UNION_CODE = "12212";
			elseif ($PER_UNION!=1 && $PER_UNION2==1 && $PER_UNION3==1 && $PER_UNION4==1 && $PER_UNION5!=1) $UNION_CODE = "21112";
			elseif ($PER_UNION!=1 && $PER_UNION2==1 && $PER_UNION3==1 && $PER_UNION4!=1 && $PER_UNION5!=1) $UNION_CODE = "21122";
			elseif ($PER_UNION!=1 && $PER_UNION2==1 && $PER_UNION3!=1 && $PER_UNION4==1 && $PER_UNION5!=1) $UNION_CODE = "21212";
			elseif ($PER_UNION!=1 && $PER_UNION2!=1 && $PER_UNION3==1 && $PER_UNION4==1 && $PER_UNION5!=1) $UNION_CODE = "22112";
		} // end if
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][org_code] = $ORG_CODE;
		$arr_content[$data_count][per_offno] = $PER_OFFNO;
		$arr_content[$data_count][per_cardno] = $PER_CARDNO;
		$arr_content[$data_count][pn_code] = $PN_CODE;
		$arr_content[$data_count][pn_name] = $PN_NAME;
		$arr_content[$data_count][class_name] = $CLASS_NAME;
		$arr_content[$data_count][per_name] = $PER_NAME;
		$arr_content[$data_count][per_surname] = $PER_SURNAME;
		$arr_content[$data_count][per_eng_name] = $PER_ENG_NAME."  ".$PER_ENG_SURNAME;
		$arr_content[$data_count][per_gender] = $PER_GENDER;
		$arr_content[$data_count][per_disability] = $PER_DISABILITY;
		$arr_content[$data_count][birthdate] = $BIRTHDATE;
		$arr_content[$data_count][startdate] = $STARTDATE;
		$arr_content[$data_count][flowdate] = $FLOWDATE;	
		$arr_content[$data_count][resigndate] = $RESIGNDATE;
		$arr_content[$data_count][pos_no] = $POS_NO;
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
		$arr_content[$data_count][re_code] = $RE_CODE;
		$arr_content[$data_count][re_name] = $RE_NAME;
		$arr_content[$data_count][ct_code_org] = $CT_CODE_ORG;
		$arr_content[$data_count][ct_name_org] = $CT_NAME_ORG;					
		$arr_content[$data_count][pv_code_org] = $PV_CODE_ORG;
		$arr_content[$data_count][pv_name_org] = $PV_NAME_ORG;
		$arr_content[$data_count][ct_code_org_ass] = $CT_CODE_ORG_ASS;
		$arr_content[$data_count][ct_name_org_ass] = $CT_NAME_ORG_ASS;
		$arr_content[$data_count][pv_code_org_ass] = $PV_CODE_ORG_ASS;
		$arr_content[$data_count][pv_name_org_ass] = $PV_NAME_ORG_ASS;
		$arr_content[$data_count][org_name] = $ORG_NAME;
		$arr_content[$data_count][org_name_1] = $ORG_NAME_1;
		$arr_content[$data_count][org_name_2] = $ORG_NAME_2;
		$arr_content[$data_count][org_name_3] = $ORG_NAME_3;
		$arr_content[$data_count][org_name_4] = $ORG_NAME_4;
		$arr_content[$data_count][level_no] = level_no_format($LEVEL_NO);
		$arr_content[$data_count][level_name] = $LEVEL_NAME;
		$arr_content[$data_count][position_type] = $POSITION_TYPE;
		$arr_content[$data_count][sah_effectivedate] = $SAH_EFFECTIVEDATE;
//			$arr_content[$data_count][per_salary] = (($PER_SALARY)?number_format($PER_SALARY,0,'',','):"");
//			$arr_content[$data_count][per_mgtsalary] = (($PER_MGTSALARY)?number_format($PER_MGTSALARY,0,'',','):"");
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
		$arr_content[$data_count][dc_code] = $DC_CODE;
		$arr_content[$data_count][dc_name] = $DC_NAME;
		$arr_content[$data_count][mov_code] = $MOV_CODE;
		$arr_content[$data_count][mov_name] = $MOV_NAME;
		$arr_content[$data_count][union_code] = $UNION_CODE; 
		$arr_content[$data_count][result1] = $RESULT1; 
		$arr_content[$data_count][percent_salary1] = $PERCENT_SALARY1; 
		$arr_content[$data_count][result2] = $RESULT2; 
		$arr_content[$data_count][percent_salary2] = $PERCENT_SALARY2; 
		$arr_content[$data_count][promotedate] = $PROMOTEDATE;

		$data_count++;	
		//echo("data_count = ".$data_count."<BR>");								
		
	} // end while
	
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	
	// ===== condition $count_data  from "select data" =====
	if($count_data){
		$xlsRow = 0;
		$count_org_ref = 0;

		$worksheet = &$workbook->addworksheet("$report_code".($count_org_ref?"_$count_org_ref":""));
		$worksheet->set_margin_right(0.50);
		$worksheet->set_margin_bottom(1.10);
		
		//====================== SET FORMAT ======================//
//				require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
		//====================== SET FORMAT ======================//

		$arr_title = explode("||", "$ORG_NAME_REF||$report_title");
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
			$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 22, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 23, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 24, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 25, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 26, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 27, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 28, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 29, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 30, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 31, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 32, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 33, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 34, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 35, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 36, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 37, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 38, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 39, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 40, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 41, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 42, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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
			$worksheet->write($xlsRow, 18, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 19, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 20, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 21, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 22, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 23, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 24, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 25, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 26, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 27, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 28, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 29, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 30, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 31, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 32, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 33, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 34, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 35, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 36, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 37, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 38, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 39, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 40, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 41, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 42, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$ORG_CODE = $arr_content[$data_count][org_code];
			$PER_CARDNO = $arr_content[$data_count][per_cardno];
			$PN_CODE = $arr_content[$data_count][pn_code];
			$PN_NAME = $arr_content[$data_count][pn_name];
			$CLASS_NAME = $arr_content[$data_count][class_name];
			$PER_NAME = $arr_content[$data_count][per_name];
			$PER_SURNAME = $arr_content[$data_count][per_surname];
			$PER_GENDER = $arr_content[$data_count][per_gender];
			$PER_DISABILITY = $arr_content[$data_count][per_disability];
			$PER_GENDER_NAME = $PER_DISABILITY_NAME = $BIRTHDATE = $STARTDATE = $FLOWDATE = "";
			if ($arr_content[$data_count][per_gender] == 1)	$PER_GENDER_NAME = "���";
			elseif ($arr_content[$data_count][per_gender] == 2)	$PER_GENDER_NAME = "˭ԧ";	
			if ($arr_content[$data_count][per_disability] == 1)	$PER_DISABILITY_NAME = "����";
			elseif ($arr_content[$data_count][per_disability] >= 2)	$PER_DISABILITY_NAME = "�ԡ��";	
                        
			$BIRTHDATE = $arr_content[$data_count][birthdate];
			if ($BIRTHDATE=="//") $BIRTHDATE = "";
			$STARTDATE = $arr_content[$data_count][startdate];
			if ($STARTDATE=="//") $STARTDATE = "";
			$FLOWDATE = $arr_content[$data_count][flowdate];
			if ($FLOWDATE=="//") $FLOWDATE = "";
			$RESIGNDATE = $arr_content[$data_count][resigndate];
			if ($RESIGNDATE=="//") $RESIGNDATE = "";
			$PROMOTEDATE = $arr_content[$data_count][promotedate];
			if ($PROMOTEDATE=="//") $PROMOTEDATE = "";
			if ($PER_NAME) $POS_STATUS = "�դ���ͤ�ͧ";
			else $POS_STATUS = "���˹���ҧ";
			$POS_NO = $arr_content[$data_count][pos_no];
			$LEVEL_GROUP = $arr_content[$data_count][level_group];
			$PM_CODE = $arr_content[$data_count][pm_code];
			$PM_NAME = $arr_content[$data_count][pm_name];
			$PL_CODE = $arr_content[$data_count][pl_code];
			$PL_NAME = $arr_content[$data_count][pl_name];
			$PT_NAME = $arr_content[$data_count][pt_name];
			$CT_CODE_ORG = $arr_content[$data_count][ct_code_org];
			$CT_NAME_ORG = $arr_content[$data_count][ct_name_org];
			$PV_CODE_ORG = $arr_content[$data_count][pv_code_org];
			$PV_NAME_ORG = $arr_content[$data_count][pv_name_org];
			$SKILL_CODE = $arr_content[$data_count][skill_code];
			$SKILL_NAME = $arr_content[$data_count][skill_name];
			$RE_CODE = $arr_content[$data_count][re_code];
			$RE_NAME = $arr_content[$data_count][re_name];
			$ORG_NAME = $arr_content[$data_count][org_name]; 	
			$ORG_NAME_1 = $arr_content[$data_count][org_name_1]; 	
			$ORG_NAME_2 = $arr_content[$data_count][org_name_2]; 
			$CL_NAME = $arr_content[$data_count][cl_name];
			$OT_CODE = $arr_content[$data_count][ot_code];
			$OT_NAME = $arr_content[$data_count][ot_name];
			$PV_CODE = $arr_content[$data_count][pv_code];
			$PV_NAME = $arr_content[$data_count][pv_name];
			$RETIREDATE_Y = $arr_content[$data_count][retiredate_y];
			$LEVEL_NO = $arr_content[$data_count][level_no];
			if (!$LEVEL_NO) {
				$cmd = " select LEVEL_NO_MIN from PER_CO_LEVEL where CL_NAME='$CL_NAME' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$LEVEL_NO = $data2[LEVEL_NO_MIN];
			}

			$LEVEL_NAME = $arr_content[$data_count][level_name];
			$POSITION_TYPE = $arr_content[$data_count][position_type];
			$SAH_EFFECTIVEDATE = $arr_content[$data_count][sah_effectivedate];
			if ($SAH_EFFECTIVEDATE=="//") $SAH_EFFECTIVEDATE = "";
			$PER_SALARY = $arr_content[$data_count][per_salary].".00";
			$PER_MGTSALARY = $arr_content[$data_count][per_mgtsalary];
			if ($PER_MGTSALARY) $PER_MGTSALARY .= ".00";
			$POH_DOCNO = $arr_content[$data_count][poh_docno];
			$POH_DOCDATE = $arr_content[$data_count][poh_docdate];
			$POH_EFFECTIVEDATE = $arr_content[$data_count][poh_effectivedate];
			if ($POH_EFFECTIVEDATE=="//") $POH_EFFECTIVEDATE = "";
			$EL_CODE = $arr_content[$data_count][el_code];
			$EL_NAME = $arr_content[$data_count][el_name];
			$EN_NAME = $arr_content[$data_count][en_name];
			$EM_NAME = $arr_content[$data_count][em_name];
			$INS_NAME = $arr_content[$data_count][ins_name];
			if (!get_magic_quotes_gpc()) {
				$EM_NAME = addslashes(str_replace('"', "&quot;", trim($EM_NAME)));
				$INS_NAME = addslashes(str_replace('"', "&quot;", trim($INS_NAME)));
			}else{
				$EM_NAME = addslashes(str_replace('"', "&quot;", stripslashes(trim($EM_NAME))));
				$INS_NAME = addslashes(str_replace('"', "&quot;", stripslashes(trim($INS_NAME))));
			}
			$CT_CODE_EDU = $arr_content[$data_count][ct_code_edu];
			$CT_NAME_EDU = $arr_content[$data_count][ct_name_edu];
			$ST_CODE = $arr_content[$data_count][st_code];
			$ST_NAME = $arr_content[$data_count][st_name];
			$SCH_NAME = $arr_content[$data_count][sch_name];
			$DC_CODE = $arr_content[$data_count][dc_code];
			$DC_NAME = $arr_content[$data_count][dc_name];
			$MOV_CODE = $arr_content[$data_count][mov_code];
			$MOV_NAME = $arr_content[$data_count][mov_name];
			$UNION_CODE = $arr_content[$data_count][union_code];
			$RESULT1 = $arr_content[$data_count][result1];
			$PERCENT_SALARY1 = $arr_content[$data_count][percent_salary1];
			$RESULT2 = $arr_content[$data_count][result2];
			$PERCENT_SALARY2 = $arr_content[$data_count][percent_salary2];
			$PROMOTEDATE = $arr_content[$data_count][promotedate];
			$EFFECTIVEDATE = "";
			if (substr($MOV_CODE,0,1)=="1") $EFFECTIVEDATE = $POH_EFFECTIVEDATE;
			elseif (substr($MOV_CODE,0,1)=="2") $EFFECTIVEDATE = $SAH_EFFECTIVEDATE;
			
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, "$ORG_NAME_1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2, "$ORG_NAME_2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 3, "$OT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 4, "$POS_NO", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 5, "$PM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 6, "$PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 7, "$POSITION_TYPE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 8, "$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 9, "$CL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 10, "$SKILL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 11, "$CT_NAME_ORG", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 12, "$PV_NAME_ORG", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 13, "$POS_STATUS", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 14, "$CLASS_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 15, "$PN_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 16, "$PER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 17, "$PER_SURNAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 18, "$PER_CARDNO", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 19, "$PER_GENDER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 20, "$PER_DISABILITY_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 21, "$RE_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 22, "$BIRTHDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 23, "$PER_SALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 24, "$PER_MGTSALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 25, "$EL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 26, "$EN_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 27, "$EM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 28, "$INS_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 29, "$CT_NAME_EDU", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 30, "$ST_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 31, "$MOV_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 32, "$EFFECTIVEDATE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 33, "$STARTDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 34, "$FLOWDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 35, "$RESIGNDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 36, "$PROMOTEDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 37, "$DC_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 38, "$UNION_CODE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 39, "$RESULT1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 40, "$PERCENTSALARY1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 41, "$RESULT2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 42, "$PERCENTSALARY2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));

			$cmd = " insert into GMIS_GPIS_FLOW_IN (tempMinistry, tempOrganize, tempDivisionName, tempOrganizeType, tempPositionNo, tempManagePosition, 
							tempLine, tempPositionType, tempLevel, tempClName, tempSkill, tempCountry, tempProvince, tempPositionStatus, tempClass, 
							tempPrename, tempFirstName, tempLastName, tempCardNo, tempGender, tempStatusDisability, tempReligion, tempBirthDate, 
							tempSalary, tempPositionSalary, tempEducationLevel, tempEducationName, tempEducationMajor, tempGraduated, tempEducationCountry, 
							tempScholarType, tempMovementType, tempMovementDate, tempStartDate, tempFlowDate, tempResignDate, tempPromoteDate,
							tempDecoration, tempUnion, tempResult1, tempPercentSalary1, tempResult2, tempPercentSalary2)
							values ('$ORG_NAME', '$ORG_NAME_1', '$ORG_NAME_2', '$OT_NAME', '$POS_NO', '$PM_NAME', '$PL_NAME', '$PT_NAME', '$LEVEL_NO', 
							'$CL_NAME', '$SKILL_NAME', '$CT_NAME_ORG', '$PV_NAME_ORG', '$POS_STATUS', '$CLASS_NAME', '$PN_NAME', '$PER_NAME', 
							'$PER_SURNAME', '$PER_CARDNO', '$PER_GENDER_NAME', '$PER_DISABILITY_NAME', '$RE_NAME', '$BIRTHDATE', '$PER_SALARY', 
							'$PER_MGTSALARY', '$EL_NAME', '$EN_NAME', '".save_quote($EM_NAME)."', '".save_quote($INS_NAME)."', '$CT_NAME_EDU', '$ST_NAME', '$MOV_NAME', '$EFFECTIVEDATE', 
							'$STARTDATE', '$FLOWDATE', '$RESIGNDATE', '$PROMOTEDATE', '$DC_NAME', '$UNION_CODE', '$RESULT1', '$PERCENT_SALARY1', 
							'$RESULT2', '$PERCENT_SALARY2') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd1 = " SELECT tempPositionNo FROM GMIS_GPIS_FLOW_IN WHERE tempPositionNo = '$POS_NO' "; 
			$count_data = $db_dpis2->send_cmd($cmd1);
			if (!$count_data) {
                            echo "[FLOW_IN]��辺�������Ţ�����˹觢ͧ ".$PER_NAME." ".$PER_SURNAME."[".$PER_CARDNO."]<br>";
				//echo "$cmd<br>==================<br>";
				//$db_dpis->show_error();
				//echo "<br>end ". ++$i  ."=======================<br>";
			}
		} 	// end for				
	}else{	// if($count_data)
		$worksheet = &$workbook->addworksheet("$report_code");
		$worksheet->set_margin_right(0.50);
		$worksheet->set_margin_bottom(1.10);
		
		//====================== SET FORMAT ======================//
//		require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
		//====================== SET FORMAT ======================//

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
		$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 22, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 23, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 24, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 25, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 26, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 27, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 28, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 29, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 30, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 31, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 32, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 33, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 34, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 35, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 36, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 37, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 38, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 39, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 40, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 41, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 42, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if
	$workbook->close();
	$arr_file[] = $fname1;
?>
<html>
<head>
<title><?=$webpage_title?> - <?=$MENU_TITLE_LV0?><?if($MENU_ID_LV1){?> - <?=$MENU_TITLE_LV1?><?}?></title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-874">
<link rel="stylesheet" href="<?=$cssfileselected?>" type="text/css">
<link rel="alternate stylesheet" type="text/css" media="all" href="stylesheets/calendar-blue.css" title="winter"/>
</head>
<?
//	echo "�ӵ����§ҹ�ش 3<br>";

	include("rpt_gmis_xls3.php");

?>