<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_att1 = new connect_att($attdb_host, $attdb_name, $attdb_user, $attdb_pwd);
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$UPDATE_USER = 99998;
	$DEPARTMENT_ID = 3013;

	if($command=='ORG'){ // 1121
/*		$cmd = " ALTER TABLE PER_TRAINING DISABLE CONSTRAINTS FK1_PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PUNISHMENT DISABLE CONSTRAINTS FK1_PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORDTL DISABLE CONSTRAINTS FK3_PER_DECORDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORATEHIS DISABLE CONSTRAINTS FK1_PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSE DISABLE CONSTRAINTS FK4_PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSEDTL DISABLE CONSTRAINTS FK2_PER_COURSEDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS DISABLE CONSTRAINTS FK1_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALPROMOTE DISABLE CONSTRAINTS FK2_PER_SALPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALARYHIS DISABLE CONSTRAINTS FK1_PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_P DISABLE CONSTRAINTS FK2_PER_PROMOTE_P ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_E DISABLE CONSTRAINTS FK2_PER_PROMOTE_E ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_C DISABLE CONSTRAINTS FK1_PER_PROMOTE_C ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ORG_JOB DISABLE CONSTRAINTS FK1_PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MOVE_REQ DISABLE CONSTRAINTS FK1_PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER DISABLE CONSTRAINTS FK1_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER DISABLE CONSTRAINTS FK2_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_BONUSPROMOTE DISABLE CONSTRAINTS FK2_PER_BONUSPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TIMEHIS DISABLE CONSTRAINTS FK1_PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SCHOLAR DISABLE CONSTRAINTS FK1_PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_REWARDHIS DISABLE CONSTRAINTS FK1_PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_POSITIONHIS DISABLE CONSTRAINTS FK1_PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PERSONALPIC DISABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_NAMEHIS DISABLE CONSTRAINTS FK1_PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MARRHIS DISABLE CONSTRAINTS FK1_PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EDUCATE DISABLE CONSTRAINTS FK1_PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENTHIS DISABLE CONSTRAINTS FK1_PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENT DISABLE CONSTRAINTS FK1_PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABILITY DISABLE CONSTRAINTS FK1_PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST2DTL DISABLE CONSTRAINTS FK2_PER_INVEST2DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST1DTL DISABLE CONSTRAINTS FK2_PER_INVEST1DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_HEIR DISABLE CONSTRAINTS FK1_PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS DISABLE CONSTRAINTS FK1_PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRAHIS DISABLE CONSTRAINTS FK1_PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_KPI DISABLE CONSTRAINTS FK1_PER_KPI ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_FAMILY DISABLE CONSTRAINTS FK1_PER_FAMILY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$table = array( "per_absenthis", "per_marrhis", "per_timehis", "per_ability", "per_rewardhis", "per_extrahis", "per_extra_incomehis", "per_namehis", 
			"per_punishment", "per_decoratehis", "per_training", "per_educate", "per_salaryhis", "per_positionhis", "per_personalpic", "per_heir", "per_servicehis", 
			"per_comdtl", "per_bonuspromote", "per_bonusquotadtl2", "per_bonusquotadtl1", "per_bonusquota", "per_special_skill", "per_coursedtl", "per_course", "per_decordtl", "per_org_job", 
			"per_promote_p", "per_letter", "per_move_req", "per_scholar", "per_absent", "per_invest1dtl", "per_promote_c", "per_address", "per_kpi", "per_kpi_form", 
			"per_salpromote", "per_family", "per_licensehis", "per_personal", "per_pos_move", "per_salquotadtl1", "per_salquotadtl2", "per_salquota", "per_command", "per_decor", 
			"per_order_dtl", "per_req3_dtl", "PER_TRANSFER_REQ" );
		for ( $i=0; $i<count($table); $i++ ) { 
			if ($table[$i]=="per_bonusquota" || $table[$i]=="per_scholar" || $table[$i]=="per_salquota" || $table[$i]=="per_command" || $table[$i]=="per_decor")
				$cmd = " delete from $table[$i] ";
			else
				$cmd = " truncate table $table[$i] ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end for

// OK �ç���ҧ 
		$cmd = " DELETE FROM PER_POSITION ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_POS_EMP ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_POS_EMPSER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_POS_TEMP ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;
*/
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_ORG' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;
/*
		$cmd = " DELETE FROM PER_ORG WHERE DEPARTMENT_ID = $SESS_DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
*/
		$cmd = " SELECT MAX(ORG_ID) AS ORG_ID FROM PER_ORG ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$ORG_ID = $data[ORG_ID];

		$cmd = " SELECT DIV, SECT, SUBSECT, NAME1, NAME2 FROM DIVISIONS WHERE SECT = '000' AND SUBSECT = '00' ORDER BY DIV, SECT, SUBSECT ";
		$db_att->send_cmd($cmd);
		$db_att->show_error();
		//echo "<br>";
		while($data = $db_att->get_array()){
			$DIV = trim($data[DIV]);
			$SECT = trim($data[SECT]);
			$SUBSECT = trim($data[SUBSECT]);
			$NAME1 = trim($data[NAME1]);
			$NAME2 = trim($data[NAME2]);
			$ORG_WEBSITE = $DIV . $SECT . $SUBSECT;
			
			$OL_CODE = "03";
			$OT_CODE = "01";
			$ORG_ID_REF = $DEPARTMENT_ID;

			$cmd1 = " SELECT ORG_ID FROM PER_ORG WHERE ORG_NAME = '$NAME1' AND DEPARTMENT_ID = $SESS_DEPARTMENT_ID AND OL_CODE = '$OL_CODE' "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if ($count_data) {
				$data1 = $db_dpis1->get_array();
				$TMP_ORG_ID = $data1[ORG_ID];
				$cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_ID = $TMP_ORG_ID ";
/*			if ($DIV=="000") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300000000' ";
			elseif ($DIV=="001") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300010000' ";
			elseif ($DIV=="061") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300020000' ";
			elseif ($DIV=="062") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300030000' ";
			elseif ($DIV=="063") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300040000' ";
			elseif ($DIV=="064") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300050000' ";
			elseif ($DIV=="065") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300060000' ";
			elseif ($DIV=="066") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300070000' ";
			elseif ($DIV=="067") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300080000' ";
			elseif ($DIV=="068") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300090000' ";
			elseif ($DIV=="069") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300100000' ";
			elseif ($DIV=="070") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300110000' ";
			elseif ($DIV=="071") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300120000' ";
			elseif ($DIV=="072") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300130000' ";
			elseif ($DIV=="073") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300130000' ";
			elseif ($DIV=="074") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300140000' ";
			elseif ($DIV=="075") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300140000' ";
			elseif ($DIV=="076") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300140000' ";
			elseif ($DIV=="077") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300140000' ";
			elseif ($DIV=="078") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300140000' ";
			elseif ($DIV=="079") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300140000' ";
			elseif ($DIV=="080") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300140000' ";
			elseif ($DIV=="081") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300140000' ";
			elseif ($DIV=="082") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300150000' ";
			elseif ($DIV=="083") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300140000' ";
			elseif ($DIV=="084") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300140000' ";
			elseif ($DIV=="085") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300140000' ";
			elseif ($DIV=="086") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300140000' ";
			elseif ($DIV=="087") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300140000' ";
			elseif ($DIV=="088") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300160000' ";
			elseif ($DIV=="089") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300170000' ";
			elseif ($DIV=="090") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300180000' ";
			elseif ($DIV=="091") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300180000' ";
			elseif ($DIV=="100") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300190000' ";
			elseif ($DIV=="200") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300190000' ";
			elseif ($DIV=="400") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300190000' ";
			elseif ($DIV=="500") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300190000' ";
			elseif ($DIV=="600") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300200000' ";
			elseif ($DIV=="700") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300210000' ";
			elseif ($DIV=="800") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300190000' ";
			elseif ($DIV=="900") $cmd = " UPDATE PER_ORG SET ORG_WEBSITE = '$ORG_WEBSITE' WHERE ORG_CODE = '2100300190000' "; */
			} else { 
				$ORG_ID++;
				$ORG_CODE = '210030' . $DIV . '0000';
				$cmd = " INSERT INTO PER_ORG (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, AP_CODE, PV_CODE, CT_CODE, 
									  ORG_DATE, ORG_JOB, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, ORG_WEBSITE, DEPARTMENT_ID, ORG_SEQ_NO)
									  VALUES ($ORG_ID, '$ORG_CODE', '$NAME1', '$NAME1', '$OL_CODE', '$OT_CODE', NULL, NULL, 
									  '140', NULL, NULL, $ORG_ID_REF, 1, $UPDATE_USER, '$UPDATE_DATE', '$ORG_WEBSITE', $SESS_DEPARTMENT_ID, $DIV) ";
			}
//			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT ORG_ID FROM PER_ORG WHERE ORG_ID = $ORG_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}
			$PER_ORG++;
		} // end while						

		$cmd = " SELECT DIV, SECT, SUBSECT, NAME1, NAME2 FROM DIVISIONS WHERE SECT <> '000' AND SUBSECT = '00' ORDER BY DIV, SECT, SUBSECT ";
		$db_att->send_cmd($cmd);
		$db_att->show_error();
		//echo "<br>";
		while($data = $db_att->get_array()){
			$DIV = trim($data[DIV]);
			$SECT = trim($data[SECT]);
			$SUBSECT = trim($data[SUBSECT]);
			$NAME1 = trim($data[NAME1]);
			$NAME2 = trim($data[NAME2]);
			$OLD_CODE = $DIV . $SECT . $SUBSECT;
			$ORG_WEBSITE = $DIV . "00000";
				
			$OL_CODE = "04";
			$OT_CODE = "01";
			if ($NAME1=='-') $NAME1 = $OLD_CODE;
					
			$cmd = " SELECT ORG_ID, ORG_CODE FROM PER_ORG WHERE ORG_WEBSITE = '$ORG_WEBSITE' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			//echo "$cmd<br>";
			$data = $db_dpis->get_array();
			$ORG_ID_REF = $data[ORG_ID];
			$ORG_CODE = trim($data[ORG_CODE]);
			$ORG_CODE = substr($ORG_CODE, 0, 9) . substr($SECT, 1, 2) . '00';

			$cmd1 = " SELECT ORG_ID FROM PER_ORG WHERE ORG_NAME = '$NAME1' AND DEPARTMENT_ID = $SESS_DEPARTMENT_ID AND OL_CODE = '$OL_CODE' "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if ($count_data) {
				$data1 = $db_dpis1->get_array();
				$TMP_ORG_ID = $data1[ORG_ID];
			} else { 
				$ORG_ID++;
				$cmd = " INSERT INTO PER_ORG (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, AP_CODE, PV_CODE, CT_CODE, 
								  ORG_DATE, ORG_JOB, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, ORG_WEBSITE, DEPARTMENT_ID, ORG_SEQ_NO)
								  VALUES ($ORG_ID, '$ORG_CODE', '$NAME1', '$NAME2', '$OL_CODE', '$OT_CODE', NULL, NULL, 
								  '140', NULL, NULL, $ORG_ID_REF, 1, $UPDATE_USER, '$UPDATE_DATE', '$OLD_CODE', $SESS_DEPARTMENT_ID, NULL) ";
			}
//			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT ORG_ID FROM PER_ORG WHERE ORG_ID = $ORG_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}
			$PER_ORG++;
		} // end while						

		$cmd = " SELECT DIV, SECT, SUBSECT, NAME1, NAME2 FROM DIVISIONS WHERE SECT <> '000' AND SUBSECT <> '00' ORDER BY DIV, SECT, SUBSECT ";
		$db_att->send_cmd($cmd);
		$db_att->show_error();
		//echo "<br>";
		while($data = $db_att->get_array()){
			$DIV = trim($data[DIV]);
			$SECT = trim($data[SECT]);
			$SUBSECT = trim($data[SUBSECT]);
			$NAME1 = trim($data[NAME1]);
			$NAME2 = trim($data[NAME2]);
			$OLD_CODE = $DIV . $SECT . $SUBSECT;
			$ORG_WEBSITE = $DIV . $SECT . "00";
				
			$OL_CODE = "05";
			$OS_CODE = "08";
			$OT_CODE = "01";
			if ($NAME1=='-') $NAME1 = $OLD_CODE;
			if ($NAME2=='-') $NAME2 = $OLD_CODE;
				
			$cmd = " SELECT ORG_ID, ORG_CODE FROM PER_ORG WHERE ORG_WEBSITE = '$ORG_WEBSITE' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			//echo "$cmd<br>";
			$data = $db_dpis->get_array();
			$ORG_ID_REF = $data[ORG_ID];
			$ORG_CODE = trim($data[ORG_CODE]);
			$ORG_CODE = substr($ORG_CODE, 0, 11) . $SUBSECT;
			if (!$ORG_ID_REF) {
				$ORG_WEBSITE = $DIV . "00000";
				$cmd = " SELECT ORG_ID, ORG_CODE FROM PER_ORG WHERE ORG_WEBSITE = '$ORG_WEBSITE' ";
				$db_dpis->send_cmd($cmd);
				$db_dpis->show_error();
				//echo "$cmd<br>";
				$data = $db_dpis->get_array();
				$ORG_ID_REF = $data[ORG_ID];
				$ORG_CODE = trim($data[ORG_CODE]);
				$ORG_CODE = substr($ORG_CODE, 0, 11) . $SUBSECT;
			}

			$cmd1 = " SELECT ORG_ID FROM PER_ORG WHERE ORG_NAME = '$NAME1' AND DEPARTMENT_ID = $SESS_DEPARTMENT_ID AND OL_CODE = '$OL_CODE' "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if ($count_data) {
				$data1 = $db_dpis1->get_array();
				$TMP_ORG_ID = $data1[ORG_ID];
			} else { 
				$ORG_ID++;
				$cmd = " INSERT INTO PER_ORG (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, AP_CODE, PV_CODE, CT_CODE, 
								  ORG_DATE, ORG_JOB, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, ORG_WEBSITE, DEPARTMENT_ID, ORG_SEQ_NO)
								  VALUES ($ORG_ID, '$ORG_CODE', '$NAME1', '$NAME2', '$OL_CODE', '$OT_CODE', NULL, NULL, 
								  '140', NULL, NULL, $ORG_ID_REF, 1, $UPDATE_USER, '$UPDATE_DATE', '$OLD_CODE', $SESS_DEPARTMENT_ID, NULL) ";
			}
//			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT ORG_ID FROM PER_ORG WHERE ORG_ID = $ORG_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}
			$PER_ORG++;
		} // end while						

		$cmd = " select count(ORG_ID) as COUNT_NEW from PER_ORG where DEPARTMENT_ID = $SESS_DEPARTMENT_ID and OL_CODE in ('03', '04', '05') ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ORG - $PER_ORG - $COUNT_NEW<br>";
/*
		$cmd = " ALTER TABLE PER_TRAINING ENABLE CONSTRAINTS FK1_PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PUNISHMENT ENABLE CONSTRAINTS FK1_PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORDTL ENABLE CONSTRAINTS FK3_PER_DECORDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORATEHIS ENABLE CONSTRAINTS FK1_PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSE ENABLE CONSTRAINTS FK4_PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSEDTL ENABLE CONSTRAINTS FK2_PER_COURSEDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS ENABLE CONSTRAINTS FK1_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALPROMOTE ENABLE CONSTRAINTS FK2_PER_SALPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALARYHIS ENABLE CONSTRAINTS FK1_PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_P ENABLE CONSTRAINTS FK2_PER_PROMOTE_P ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_E ENABLE CONSTRAINTS FK2_PER_PROMOTE_E ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_C ENABLE CONSTRAINTS FK1_PER_PROMOTE_C ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ORG_JOB ENABLE CONSTRAINTS FK1_PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MOVE_REQ ENABLE CONSTRAINTS FK1_PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER ENABLE CONSTRAINTS FK1_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER ENABLE CONSTRAINTS FK2_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_BONUSPROMOTE ENABLE CONSTRAINTS FK2_PER_BONUSPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TIMEHIS ENABLE CONSTRAINTS FK1_PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SCHOLAR ENABLE CONSTRAINTS FK1_PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_REWARDHIS ENABLE CONSTRAINTS FK1_PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_POSITIONHIS ENABLE CONSTRAINTS FK1_PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PERSONALPIC ENABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_NAMEHIS ENABLE CONSTRAINTS FK1_PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MARRHIS ENABLE CONSTRAINTS FK1_PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EDUCATE ENABLE CONSTRAINTS FK1_PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENTHIS ENABLE CONSTRAINTS FK1_PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENT ENABLE CONSTRAINTS FK1_PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABILITY ENABLE CONSTRAINTS FK1_PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST2DTL ENABLE CONSTRAINTS FK2_PER_INVEST2DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST1DTL ENABLE CONSTRAINTS FK2_PER_INVEST1DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_HEIR ENABLE CONSTRAINTS FK1_PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ENABLE CONSTRAINTS FK1_PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRAHIS ENABLE CONSTRAINTS FK1_PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_KPI ENABLE CONSTRAINTS FK1_PER_KPI ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_FAMILY ENABLE CONSTRAINTS FK1_PER_FAMILY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;
*/
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if
	
	if($command=='POSITION'){ // 9266
/*		$cmd = " ALTER TABLE PER_TRAINING DISABLE CONSTRAINTS FK1_PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PUNISHMENT DISABLE CONSTRAINTS FK1_PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORDTL DISABLE CONSTRAINTS FK3_PER_DECORDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORATEHIS DISABLE CONSTRAINTS FK1_PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSE DISABLE CONSTRAINTS FK4_PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSEDTL DISABLE CONSTRAINTS FK2_PER_COURSEDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS DISABLE CONSTRAINTS FK1_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALPROMOTE DISABLE CONSTRAINTS FK2_PER_SALPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALARYHIS DISABLE CONSTRAINTS FK1_PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_P DISABLE CONSTRAINTS FK2_PER_PROMOTE_P ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_E DISABLE CONSTRAINTS FK2_PER_PROMOTE_E ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_C DISABLE CONSTRAINTS FK1_PER_PROMOTE_C ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ORG_JOB DISABLE CONSTRAINTS FK1_PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MOVE_REQ DISABLE CONSTRAINTS FK1_PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER DISABLE CONSTRAINTS FK1_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER DISABLE CONSTRAINTS FK2_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_BONUSPROMOTE DISABLE CONSTRAINTS FK2_PER_BONUSPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TIMEHIS DISABLE CONSTRAINTS FK1_PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SCHOLAR DISABLE CONSTRAINTS FK1_PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_REWARDHIS DISABLE CONSTRAINTS FK1_PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_POSITIONHIS DISABLE CONSTRAINTS FK1_PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PERSONALPIC DISABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_NAMEHIS DISABLE CONSTRAINTS FK1_PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MARRHIS DISABLE CONSTRAINTS FK1_PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EDUCATE DISABLE CONSTRAINTS FK1_PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENTHIS DISABLE CONSTRAINTS FK1_PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENT DISABLE CONSTRAINTS FK1_PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABILITY DISABLE CONSTRAINTS FK1_PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST2DTL DISABLE CONSTRAINTS FK2_PER_INVEST2DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST1DTL DISABLE CONSTRAINTS FK2_PER_INVEST1DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_HEIR DISABLE CONSTRAINTS FK1_PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS DISABLE CONSTRAINTS FK1_PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRAHIS DISABLE CONSTRAINTS FK1_PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_KPI DISABLE CONSTRAINTS FK1_PER_KPI ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_FAMILY DISABLE CONSTRAINTS FK1_PER_FAMILY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$table = array( "per_absenthis", "per_marrhis", "per_timehis", "per_ability", "per_rewardhis", "per_extrahis", "per_extra_incomehis", "per_namehis", 
			"per_punishment", "per_decoratehis", "per_training", "per_educate", "per_salaryhis", "per_positionhis", "per_personalpic", "per_heir", "per_servicehis", 
			"per_comdtl", "per_bonuspromote", "per_bonusquotadtl2", "per_bonusquotadtl1", "per_bonusquota", "per_special_skill", "per_coursedtl", "per_decordtl", "per_org_job", 
			"per_promote_p", "per_letter", "per_move_req", "per_scholar", "per_absent", "per_invest1dtl", "per_promote_c", "per_address", "per_kpi_form", 
			"per_salpromote", "per_family", "per_licensehis", "per_personal", "per_pos_move", "per_salquotadtl1", "per_salquotadtl2", "per_salquota", "per_command", "per_decor", 
			"per_order_dtl", "per_req3_dtl" );
		for ( $i=0; $i<count($table); $i++ ) { 
			if ($table[$i]=="per_bonusquota" || $table[$i]=="per_scholar" || $table[$i]=="per_salquota" || $table[$i]=="per_command" || $table[$i]=="per_decor")
				$cmd = " delete from $table[$i] ";
			else
				$cmd = " truncate table $table[$i] ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end for

// ���˹觢���Ҫ��� 9266
*/
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_POSITION' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;
/*
		$cmd = " DELETE FROM PER_POSITION ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();

		$cmd = " delete from per_mgt where update_user = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT ADMINPOST, ADNAME1                 
						  FROM ADMIN
						  WHERE ADMINPOST IN (SELECT DISTINCT ADMINPOST FROM POST) 
						  ORDER BY  ADMINPOST ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$TIT_M_ID = $data[TIT_M_ID];
			$TIT_M_CODE = trim($data[TIT_M_CODE]);
			$TIT_M_NAME = trim($data[TIT_M_NAME]);
			$PS_CODE = "13";
			if (!$TIT_M_CODE) $TIT_M_CODE = '$TIT_M_ID';

			$cmd = " INSERT INTO PER_MGT (PM_CODE, PM_NAME, PM_SHORTNAME, PS_CODE, PM_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('$TIT_M_CODE', '$TIT_M_NAME', trim(substr('$TIT_M_NAME',1,20)), '$PS_CODE', 1, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "<br>";
		} // end while						
 
		$cmd = " delete from per_line where update_user = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT POSCODE, POS_NAME1                 
						  FROM POSTCODE
						  WHERE POSCODE IN (SELECT DISTINCT POSCODE FROM POST)
						  ORDER BY POSCODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$TIT_J_ID = $data[TIT_J_ID];
			$TIT_J_CODE = trim($data[TIT_J_CODE]);
			$TIT_J_NAME = trim($data[TIT_J_NAME]);
			$TIT_J_ABBR = trim($data[TIT_J_ABBR]);
			if (!$TIT_J_CODE) $TIT_J_CODE = '$TIT_J_ID';
			if (!$TIT_J_ABBR) $TIT_J_ABBR = $TIT_J_NAME;

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('$TIT_J_CODE', '$TIT_J_NAME', '$TIT_J_ABBR', 1, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "<br>";
		} // end while						
		$cmd = " SELECT EXPPOST, EXPNAME1                 
						  FROM EXPERT
						  WHERE EXPPOST IN (SELECT DISTINCT EXPPOST FROM POST)
						  ORDER BY EXPPOST ";
*/
		$cmd = " SELECT POSNUM, DIVCODE, SECTCODE, SUBSECTCODE, POSCODE, ADMINPOST, EXPPOST, CCODE, PCODE, 
						  DCODE, LEVRAN, POSCON, POSSTAT, POSDATE, CSCDATE, BOBDATE, ENTRDATE, BUDGET, STATUS, 
						  COMMANDER, DISPLAYTYPE, POSTTYPE1, POSTTYPE2, PREMARK, POSITIONINSENTIVE, BINSENTIVE, 
						  POSITIONREMARK, APREMARK, POLDLEVEL, OLDBUDGET, OLDPINSENTIVE, BUDGETREMARK, OSALARY, 
						  OLDBUDGETREMARK, [OPOSITION REMARK] as OPOSITIONREMARK, DEPTCODE, KEEP, CONDITION, 
						  CANCELPOSITION, GROUPINPOSITION, MANAGING, PINSENTIVERPAST, PINTENSIVER, POSITIONREMARKPAST, 
						  SPOSDESCRIPTION, POSITIONREMARKP, POSCONDITION, POSCODEP, ADMINPOSTP, EXPPOSTP, LEVRANPOS, 
						  OLDPOSNUM, JOBGROUPID                 
						  FROM POST
						  ORDER BY CINT(POSNUM) ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_POSITION++;
			$POSNUM = $data[POSNUM];
			$DIVCODE = trim($data[DIVCODE]);
			$SECTCODE = trim($data[SECTCODE]);
			$SUBSECTCODE = trim($data[SUBSECTCODE]);
			$POSCODE = trim($data[POSCODE]);
			$ADMINPOST = trim($data[ADMINPOST]);
			$EXPPOST = trim($data[EXPPOST]);
			$LEVRAN = trim($data[LEVRAN]);
			$POSCON = trim($data[POSCON]);
			$POSDATE = trim($data[POSDATE]);
			$CSCDATE = trim($data[CSCDATE]);
			$BOBDATE = trim($data[BOBDATE]);
			$BUDGET = $data[BUDGET] + 0;
			$POSITIONINSENTIVE = $data[POSITIONINSENTIVE];
			$POSTTYPE1 = trim($data[POSTTYPE1]);
			$POSTTYPE2 = trim($data[POSTTYPE2]);
			$POS_CONDITION = trim($data[POSITIONREMARKPAST]);
			$POS_DOC_NO = trim($data[POSITIONREMARK]);
			$POS_REMARK = trim($data[PREMARK]);
			if (!$SUBSECTCODE) $SUBSECTCODE = "00";
			$ORG_WEBSITE = $DIVCODE . $SECTCODE . $SUBSECTCODE;
			$ORG_WEBSITE1 = $DIVCODE . $SECTCODE . "00";
			$ORG_WEBSITE2 = $DIVCODE . "00000";
			$PT_CODE = $POSTTYPE1 . $POSTTYPE2;

			$OT_CODE = "01";
			$ORG_ID_1 = "NULL";
			$ORG_ID_2 = "NULL";
			$cmd = " SELECT OL_CODE, ORG_ID FROM PER_ORG WHERE ORG_WEBSITE = '$ORG_WEBSITE2' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "$cmd<br>";
			$data = $db_dpis->get_array();
			$OL_CODE = trim($data[OL_CODE]);
			if ($OL_CODE == "03") $ORG_ID = $data[ORG_ID];

			if ($ORG_WEBSITE2 != $ORG_WEBSITE1) {
				$cmd = " SELECT OL_CODE, ORG_ID, ORG_NAME FROM PER_ORG WHERE ORG_WEBSITE = '$ORG_WEBSITE1' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd<br>";
				$data = $db_dpis->get_array();
				$OL_CODE = trim($data[OL_CODE]);
				$ORG_NAME = trim($data[ORG_NAME]);
				if ($OL_CODE == "04" and substr($ORG_NAME,0,1) != '0') $ORG_ID_1 = $data[ORG_ID];

				if ($ORG_WEBSITE1 != $ORG_WEBSITE) {
					$cmd = " SELECT OL_CODE, ORG_ID, ORG_NAME FROM PER_ORG WHERE ORG_WEBSITE = '$ORG_WEBSITE' ";
					$db_dpis->send_cmd($cmd);
//					$db_dpis->show_error();
//					echo "$cmd<br>";
					$data = $db_dpis->get_array();
					$OL_CODE = trim($data[OL_CODE]);
					$ORG_NAME = trim($data[ORG_NAME]);
					if ($OL_CODE == "05" and substr($ORG_NAME,0,1) != '0') $ORG_ID_2 = $data[ORG_ID];
				}
			}

			if (!$ORG_ID) $ORG_ID = $DEPARTMENT_ID;
			if (!$POSITIONINSENTIVE) $POSITIONINSENTIVE = 0;

			$PM_CODE = "";
			if ($ADMINPOST=='024') $PM_CODE = "0297"; // ���ѭ�վ�Һ��
			elseif ($ADMINPOST=='078') $PM_CODE = "0375"; // ����ӹ�¡��ʶҺѹ
			elseif ($ADMINPOST=='212') $PM_CODE = "0234"; // ����ӹ�¡��
			elseif ($ADMINPOST=='213') $PM_CODE = "0357"; // ͸Ժ��
			elseif ($ADMINPOST=='222') $PM_CODE = "0270"; // �ͧ����ӹ�¡��
			elseif ($ADMINPOST=='223') $PM_CODE = "0276"; // �ͧ͸Ժ��
			elseif ($ADMINPOST=='248') $PM_CODE = "0289"; // �ԪҪվ੾�� (Ǫ.)
			elseif ($ADMINPOST=='310') $PM_CODE = "0251"; // ����ӹ�¡���ӹѡ
			elseif ($ADMINPOST=='311') $PM_CODE = "0283"; // �Ţҹء�á��
			elseif ($ADMINPOST=='312') $PM_CODE = "0235"; // ����ӹ�¡�áͧ
			elseif ($ADMINPOST=='315') $PM_CODE = "0243"; // ����ӹ�¡���ٹ��
			elseif ($ADMINPOST=='401') $PM_CODE = "0026"; // ���˹�ҷ�����������º�����Ἱ
			elseif ($ADMINPOST=='402') $PM_CODE = "9999"; // ���˹�ҷ������çҹ�����
			elseif ($ADMINPOST=='403') $PM_CODE = "9999"; // �ؤ�ҡ�
			elseif ($ADMINPOST=='405') $PM_CODE = "9999"; // ��Ҿ�ѡ�ҹ��á��
			elseif ($ADMINPOST=='408') $PM_CODE = "9999"; // ��Ҿ�ѡ�ҹ��ʴ�
			elseif ($ADMINPOST=='411') $PM_CODE = "9999"; // ���˹�ҷ��ѹ�֡������
			elseif ($ADMINPOST=='412') $PM_CODE = "9999"; // ���˹�ҷ������մ
			elseif ($ADMINPOST=='416') $PM_CODE = "9999"; // �ѡʶԵ�
			elseif ($ADMINPOST=='417') $PM_CODE = "9999"; // ���˹�ҷ���ǪʶԵ�
			elseif ($ADMINPOST=='419') $PM_CODE = "9999"; // ���˹�ҷ����������ѹ��
			elseif ($ADMINPOST=='420') $PM_CODE = "9999"; // ��Ҿ�ѡ�ҹ����Թ��кѭ��
			elseif ($ADMINPOST=='421') $PM_CODE = "9999"; // �ѡ�Ԫҡ���Թ��кѭ��
			elseif ($ADMINPOST=='423') $PM_CODE = "9999"; // ���˹�ҷ���Ǩ�ͺ����
			elseif ($ADMINPOST=='424') $PM_CODE = "9999"; // ���˹�ҷ���Ъ�����ѹ��
			elseif ($ADMINPOST=='427') $PM_CODE = "9999"; // ��Ҿ�ѡ�ҹ�ʵ��ȹ�֡��
			elseif ($ADMINPOST=='428') $PM_CODE = "9999"; // �ѡ�Ԫҡ���ʵ��ȹ�֡��
			elseif ($ADMINPOST=='429') $PM_CODE = "0089"; // ���ᾷ��
			elseif ($ADMINPOST=='430') $PM_CODE = "9999"; // �ѹ�ᾷ��
			elseif ($ADMINPOST=='431') $PM_CODE = "9999"; // ����ѵ�ᾷ��
			elseif ($ADMINPOST=='432') $PM_CODE = "9999"; // �ѡ�Է����ʵ����ᾷ��
			elseif ($ADMINPOST=='433') $PM_CODE = "9999"; // ���Ѫ��
			elseif ($ADMINPOST=='435') $PM_CODE = "9999"; // �ѡ����ҡ��
			elseif ($ADMINPOST=='436') $PM_CODE = "9999"; // �ѡ�Ե�Է��
			elseif ($ADMINPOST=='438') $PM_CODE = "9999"; // ��Һ��෤�Ԥ
			elseif ($ADMINPOST=='440') $PM_CODE = "9999"; // ��Һ���ԪҪվ
			elseif ($ADMINPOST=='443') $PM_CODE = "9999"; // ���˹�ҷ���ѧ�ա��ᾷ��
			elseif ($ADMINPOST=='444') $PM_CODE = "9999"; // �ѡ�ѧ�ա��ᾷ��
			elseif ($ADMINPOST=='445') $PM_CODE = "9999"; // �ѡ����Ҿ�ӺѴ
			elseif ($ADMINPOST=='446') $PM_CODE = "9999"; // ���˹�ҷ���Ҫ�ǺӺѴ
			elseif ($ADMINPOST=='447') $PM_CODE = "9999"; // �ѡ�Ҫ�ǺӺѴ
			elseif ($ADMINPOST=='448') $PM_CODE = "9999"; // ��Ҿ�ѡ�ҹ�Է����ʵ����ᾷ��
			elseif ($ADMINPOST=='449') $PM_CODE = "9999"; // ��Ҿ�ѡ�ҹ���Ѫ����
			elseif ($ADMINPOST=='450') $PM_CODE = "9999"; // �ѡ�Ԫҡ���Ҹ�ó�آ
			elseif ($ADMINPOST=='451') $PM_CODE = "9999"; // �����·ѹ�ᾷ��
			elseif ($ADMINPOST=='452') $PM_CODE = "9999"; // ��Ҿ�ѡ�ҹ�Ǫ������鹿�
			elseif ($ADMINPOST=='453') $PM_CODE = "9999"; // �ѡ෤�Ԥ���ᾷ��
			elseif ($ADMINPOST=='459') $PM_CODE = "9999"; // ��ҧ����ػ�ó�
			elseif ($ADMINPOST=='461') $PM_CODE = "9999"; // �ѡ�Ԫҡ���֡�Ҿ����
			elseif ($ADMINPOST=='462') $PM_CODE = "9999"; // ��ó��ѡ��
			elseif ($ADMINPOST=='464') $PM_CODE = "9999"; // �ѡ�ѧ��ʧ������
			elseif ($ADMINPOST=='465') $PM_CODE = "9999"; // �ѡ�Ԫҡ�ä���������
			elseif ($ADMINPOST=='469') $PM_CODE = "9999"; // �ѡ����ػ�ó�
			elseif ($ADMINPOST=='470') $PM_CODE = "9999"; // �ѡ�Ԩ�����ӺѴ
			elseif ($ADMINPOST=='471') $PM_CODE = "9999"; // �ѡ෤�����������з�ǧ͡
			elseif ($ADMINPOST=='901') $PM_CODE = "0194"; // ���ӹҭ���
			elseif ($ADMINPOST=='902') $PM_CODE = "0200"; // ���ӹҭ��þ����
			elseif ($ADMINPOST=='903') $PM_CODE = "0203"; // �������Ǫҭ
			elseif ($ADMINPOST=='904') $PM_CODE = "0207"; // �������Ǫҭ�����
			elseif ($ADMINPOST=='948') $PM_CODE = "0033"; // �
			elseif ($ADMINPOST=='949') $PM_CODE = "0034"; // Ǫ
			elseif ($ADMINPOST=='956') $PM_CODE = "0380"; // ����ӹ�¡���ç��Һ��
			elseif ($ADMINPOST=='958') $PM_CODE = "0026"; // ���˹�ҷ�����������º�����Ἱ
			elseif ($ADMINPOST=='965') $PM_CODE = "0033"; // ���˹觷���ջ��ʺ��ó� (�)
			elseif ($ADMINPOST=='966') $PM_CODE = "0034"; // ���˹��ԪҪվ (Ǫ.)
			elseif ($ADMINPOST=='999') $PM_CODE = "9999"; // �������˹觷ҧ������/�Ԫҡ��
			if ($ADMINPOST && !$PM_CODE) {
				$ADMIN_POST = '0' . $ADMINPOST;
				$cmd = " SELECT PM_NAME FROM PER_MGT WHERE PM_CODE = '$ADMIN_POST' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd<br>";
				$data = $db_dpis->get_array();
				$PM_NAME = trim($data[PM_NAME]);
				if (!$PM_NAME) echo "���˹�㹡�ú����çҹ $ADMINPOST<br>";
			}
			$PM_CODE = (trim($PM_CODE))? "'".trim($PM_CODE)."'" : "NULL";

			$PL_CODE = "";
			if ($POSCODE=='10108') $PL_CODE = "510108"; // �ѡ������
			elseif ($POSCODE=='10703') $PL_CODE = "510703"; // �ѡ���������º�����Ἱ
			elseif ($POSCODE=='11103') $PL_CODE = "511104"; // �ѡ�Ѵ��çҹ�����
			elseif ($POSCODE=='11104') $PL_CODE = "511612"; // ��Ҿ�ѡ�ҹ��á��
			elseif ($POSCODE=='11403') $PL_CODE = "510903"; // �ѡ��Ѿ�ҡúؤ��
			elseif ($POSCODE=='11612') $PL_CODE = "511612"; // ��Ҿ�ѡ�ҹ��á��
			elseif ($POSCODE=='11614') $PL_CODE = "511612"; // ��Ҿ�ѡ�ҹ��á��
			elseif ($POSCODE=='11712') $PL_CODE = "511712"; // ��Ҿ�ѡ�ҹ��ʴ�
			elseif ($POSCODE=='11723') $PL_CODE = "511723"; // �ѡ�Ԫҡ�þ�ʴ�
			elseif ($POSCODE=='11730') $PL_CODE = "511723"; // �ѡ�Ԫҡ�þ�ʴ�
			elseif ($POSCODE=='11731') $PL_CODE = "511712"; // ��Ҿ�ѡ�ҹ��ʴ�
			elseif ($POSCODE=='11801') $PL_CODE = "511612"; // ��Ҿ�ѡ�ҹ��á��
			elseif ($POSCODE=='12201') $PL_CODE = "512212"; // ��Ҿ�ѡ�ҹʶԵ�
			elseif ($POSCODE=='12212') $PL_CODE = "512212"; // ��Ҿ�ѡ�ҹʶԵ�
			elseif ($POSCODE=='12223') $PL_CODE = "512003"; // �ѡ�Ԫҡ��ʶԵ�
			elseif ($POSCODE=='12302') $PL_CODE = "512302"; // ��Ҿ�ѡ�ҹ�ǪʶԵ�
			elseif ($POSCODE=='12403') $PL_CODE = "512403"; // �Եԡ�
			elseif ($POSCODE=='13003') $PL_CODE = "513003"; // �ѡ��������ѹ��
			elseif ($POSCODE=='20412') $PL_CODE = "520412"; // ��Ҿ�ѡ�ҹ����Թ��кѭ��
			elseif ($POSCODE=='20423') $PL_CODE = "520423"; // �ѡ�Ԫҡ���Թ��кѭ��
			elseif ($POSCODE=='20430') $PL_CODE = "520423"; // �ѡ�Ԫҡ���Թ��кѭ��
			elseif ($POSCODE=='20431') $PL_CODE = "520412"; // ��Ҿ�ѡ�ҹ����Թ��кѭ��
			elseif ($POSCODE=='20603') $PL_CODE = "520603"; // �ѡ�Ԫҡ�õ�Ǩ�ͺ����
			elseif ($POSCODE=='31801') $PL_CODE = "531801"; // ��Ҿ�ѡ�ҹ������Ъ�����ѹ��
			elseif ($POSCODE=='31813') $PL_CODE = "531813"; // �ѡ��Ъ�����ѹ��
			elseif ($POSCODE=='32501') $PL_CODE = "532512"; // ��Ҿ�ѡ�ҹ�ʵ��ȹ�֡��
			elseif ($POSCODE=='32512') $PL_CODE = "532512"; // ��Ҿ�ѡ�ҹ�ʵ��ȹ�֡��
			elseif ($POSCODE=='32523') $PL_CODE = "532523"; // �ѡ�Ԫҡ���ʵ��ȹ�֡��
			elseif ($POSCODE=='60104') $PL_CODE = "560104"; // ���ᾷ��
			elseif ($POSCODE=='60204') $PL_CODE = "560204"; // �ѹ�ᾷ��
			elseif ($POSCODE=='60304') $PL_CODE = "560304"; // ����ѵ�ᾷ��
			elseif ($POSCODE=='60403') $PL_CODE = "560403"; // �ѡ�Է����ʵ����ᾷ��
			elseif ($POSCODE=='60603') $PL_CODE = "560603"; // ���Ѫ��
			elseif ($POSCODE=='60802') $PL_CODE = "560802"; // ����ҡ�
			elseif ($POSCODE=='60813') $PL_CODE = "560813"; // �ѡ����ҡ��
			elseif ($POSCODE=='61203') $PL_CODE = "561204"; // �ѡ�Ե�Է�Ҥ�Թԡ
			elseif ($POSCODE=='61303') $PL_CODE = "562503"; // �ѡ�Ԫҡ���Ҹ�ó�آ
			elseif ($POSCODE=='61502') $PL_CODE = "561502"; // ��Һ��෤�Ԥ
			elseif ($POSCODE=='61510') $PL_CODE = "561514"; // �ѡ�Ԫҡ�þ�Һ��
			elseif ($POSCODE=='61523') $PL_CODE = "561523"; // ��Һ���ԪҪվ
			elseif ($POSCODE=='61601') $PL_CODE = "561502"; // ��Һ��෤�Ԥ
			elseif ($POSCODE=='61701') $PL_CODE = "561712"; // ��Ҿ�ѡ�ҹ�ѧ�ա��ᾷ��
			elseif ($POSCODE=='61712') $PL_CODE = "561712"; // ��Ҿ�ѡ�ҹ�ѧ�ա��ᾷ��
			elseif ($POSCODE=='61723') $PL_CODE = "561723"; // �ѡ�ѧ�ա��ᾷ��
			elseif ($POSCODE=='61803') $PL_CODE = "561803"; // �ѡ����Ҿ�ӺѴ
			elseif ($POSCODE=='61902') $PL_CODE = "561902"; // ���˹�ҷ���Ҫ�ǺӺѴ
			elseif ($POSCODE=='61913') $PL_CODE = "561914"; // �ѡ�Ԩ�����ӺѴ
			elseif ($POSCODE=='62212') $PL_CODE = "562212"; // ��Ҿ�ѡ�ҹ�Է����ʵ����ᾷ��
			elseif ($POSCODE=='62312') $PL_CODE = "562312"; // ��Ҿ�ѡ�ҹ���Ѫ����
			elseif ($POSCODE=='62503') $PL_CODE = "562503"; // �ѡ�Ԫҡ���Ҹ�ó�آ
			elseif ($POSCODE=='62601') $PL_CODE = "562802"; // ��Ҿ�ѡ�ҹ�ѹ��Ҹ�ó�آ
			elseif ($POSCODE=='62712') $PL_CODE = "562712"; // ��Ҿ�ѡ�ҹ�Ǫ������鹿�
			elseif ($POSCODE=='63603') $PL_CODE = "563603"; // �ѡ෤�Ԥ���ᾷ��
			elseif ($POSCODE=='63700') $PL_CODE = "561914"; // �ѡ�Ԩ�����ӺѴ
			elseif ($POSCODE=='63800') $PL_CODE = "536020"; // �ѡ෤�����������з�ǧ͡
			elseif ($POSCODE=='73212') $PL_CODE = "573012"; // ��ª�ҧ俿��
			elseif ($POSCODE=='73213') $PL_CODE = "573012"; // ��ª�ҧ俿��
			elseif ($POSCODE=='73512') $PL_CODE = "573512"; // ��ª�ҧ෤�Ԥ
			elseif ($POSCODE=='74412') $PL_CODE = "574412"; // ��ª�ҧ��Ż�
			elseif ($POSCODE=='75003') $PL_CODE = "575003"; // ��ҧ�Ҿ���ᾷ��
			elseif ($POSCODE=='75601') $PL_CODE = "575602"; // ��ҧ����ػ�ó�
			elseif ($POSCODE=='75602') $PL_CODE = "575612"; // �ѡ����ػ�ó�
			elseif ($POSCODE=='75702') $PL_CODE = "575702"; // ��ҧ�ѹ�����
			elseif ($POSCODE=='80513') $PL_CODE = "561915"; // �ѡ�Ǫ��ʵ�������ͤ�������
			elseif ($POSCODE=='80514') $PL_CODE = "580513"; // �ѡ�Ԫҡ���֡�Ҿ����
			elseif ($POSCODE=='81303') $PL_CODE = "581303"; // ��ó��ѡ��
			elseif ($POSCODE=='81501') $PL_CODE = "581501"; // ��Ҿ�ѡ�ҹ��ͧ��ش
			elseif ($POSCODE=='82903') $PL_CODE = "582903"; // �ѡ�ѧ��ʧ������
			elseif ($POSCODE=='99070') $PL_CODE = "511013"; // �ѡ�Ԫҡ�ä���������
			elseif ($POSCODE=='99071') $PL_CODE = "562212"; // ��Ҿ�ѡ�ҹ�Է����ʵ����ᾷ��
			elseif ($POSCODE=='99072') $PL_CODE = "513003"; // �ѡ��������ѹ��

			if ($POSCODE && !$PL_CODE) {
				$POS_CODE = '0' . $POSCODE;
				$cmd = " SELECT PL_NAME FROM PER_LINE WHERE PL_CODE = '$POS_CODE' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd<br>";
				$data = $db_dpis->get_array();
				$PL_NAME = trim($data[PL_NAME]);
				if (!$PL_NAME) echo "���˹����§ҹ $POSCODE<br>";
			}
			$PL_CODE = (trim($PL_CODE))? "'".trim($PL_CODE)."'" : "NULL";

			$SKILL_CODE = "";
			if ($EXPPOST=='01002') $SKILL_CODE = "002"; // ��ҹ�Ǫ���� �ҢҨѡ���Է��
			elseif ($EXPPOST=='01010') $SKILL_CODE = "010"; // ��ҹ�Ǫ���� �Ңһ���ҷ�Է��
			elseif ($EXPPOST=='01011') $SKILL_CODE = "011"; // ��ҹ�Ǫ���� �ҢҾ�Ҹ��Է��
			elseif ($EXPPOST=='01017') $SKILL_CODE = "016"; // ��ҹ�Ǫ���� �Ң��ѧ���Է��
			elseif ($EXPPOST=='01020') $SKILL_CODE = "019"; // ��ҹ�Ǫ���� �Ң����ѭ���Է��
			elseif ($EXPPOST=='01021') $SKILL_CODE = "020"; // ��ҹ�Ǫ���� �Ң��Ǫ���������
			elseif ($EXPPOST=='01022') $SKILL_CODE = "006"; // ��ҹ�Ǫ������ͧ�ѹ
			elseif ($EXPPOST=='01023') $SKILL_CODE = "022"; // ��ҹ�Ǫ���� �Ң��Ǫ������鹿�
			elseif ($EXPPOST=='01026') $SKILL_CODE = "025"; // ��ҹ�Ǫ���� �Ң����¡���
			elseif ($EXPPOST=='01029') $SKILL_CODE = "027"; // ��ҹ�Ǫ���� �Ң�����⸻Դԡ��
			elseif ($EXPPOST=='01031') $SKILL_CODE = "029"; // ��ҹ�Ǫ���� �Ң��ٵ�-����Ǫ����
			elseif ($EXPPOST=='01032') $SKILL_CODE = "054"; // ��ҹ�Ǫ���� �Ң��ʵ �� ���ԡ
			elseif ($EXPPOST=='01034') $SKILL_CODE = "032"; // ��ҹ�Ǫ���� �Ң�����á���
			elseif ($EXPPOST=='01035') $SKILL_CODE = "021"; // ��ҹ�Ǫ���� �Ң�������Է�ҹ���Ǫ
			elseif ($EXPPOST=='01037') $SKILL_CODE = "026"; // ��ҹ�Ǫ���� �Ңһ���ҷ���¡���
			elseif ($EXPPOST=='01038') $SKILL_CODE = "030"; // ��ҹ�Ǫ���� �Ңҡ���û���ҷ�Է��
			elseif ($EXPPOST=='01040') $SKILL_CODE = "028"; // ��ҹ�Ǫ���� �Ңһ���ҷ�ѧ���Է��
			elseif ($EXPPOST=='01041') $SKILL_CODE = "004"; // ��ҹ�Ǫ���� �ҢҨԵ�Ǫ
			elseif ($EXPPOST=='01042') $SKILL_CODE = "007"; // ��ҹ�Ǫ���� �Ңҵ��Է��
			elseif ($EXPPOST=='01044') $SKILL_CODE = "031"; // ��ҹ�Ǫ���� �ҢҨѡ�ػ���ҷ�Է��
			elseif ($EXPPOST=='01045') $SKILL_CODE = "543"; // ��ҹ��������Ѳ��
			elseif ($EXPPOST=='01047') $SKILL_CODE = "043"; // ��ҹ�Ǫ���� �Ң��Ǫ��������� (�غѵ��˵���Щء�Թ)
			elseif ($EXPPOST=='02001') $SKILL_CODE = "033"; // ��ҹ�ѹ�����
			elseif ($EXPPOST=='03002') $SKILL_CODE = "037"; // ��ҹ���Ѫ������ü�Ե
			elseif ($EXPPOST=='03003') $SKILL_CODE = "038"; // ��ҹ���Ѫ������Թԡ
			elseif ($EXPPOST=='05026') $SKILL_CODE = "543"; // ��������Ѳ��
			elseif ($EXPPOST=='05028') $SKILL_CODE = "085"; // ��ҹ�Ҹ�ó�آ
			elseif ($EXPPOST=='17014') $SKILL_CODE = "064"; // ��ҹ��ԡ�÷ҧ�Ԫҡ�� �ҢҸ�Ҥ�����ʹ
			elseif ($EXPPOST=='17015') $SKILL_CODE = "041"; // ��ҹ�Ǫ���� �Ңҡ�����Ǫ����
			elseif ($EXPPOST=='17016') $SKILL_CODE = "001"; // ��ҹ�Ǫ����
			elseif ($EXPPOST=='17017') $SKILL_CODE = "048"; // ��ҹ��þ�Һ�ż����¼�ҵѴ
			elseif ($EXPPOST=='17019') $SKILL_CODE = "009"; // ��ҹ��ԡ�÷ҧ�Ԫҡ��
			elseif ($EXPPOST=='17021') $SKILL_CODE = "042"; // ��ҹ�Ǫ���� �Ңҡ�������¡���
			elseif ($EXPPOST=='18000') $SKILL_CODE = "044"; // ��ҹ��þ�Һ��
			elseif ($EXPPOST=='19000') $SKILL_CODE = "046"; // ��ҹ��þ�Һ�����ѭ��
			elseif ($EXPPOST=='30000') $SKILL_CODE = "049"; // ��ҹ��þ�Һ�ż�����˹ѡ
			elseif ($EXPPOST=='40000') $SKILL_CODE = "047"; // ��ҹ��þ�Һ�ż���ʹ
			elseif ($EXPPOST=='50000') $SKILL_CODE = "050"; // ��ҹ��þ�Һ�ż������غѵ��˵���Щء�Թ
			elseif ($EXPPOST=='60000') $SKILL_CODE = "058"; // ��ҹ��þ�Һ��㹡�õ�Ǩ�ѡ�Ҿ����
			elseif ($EXPPOST=='79550') $SKILL_CODE = "441"; // ��ҹ�Ԩ��
			elseif ($EXPPOST=='90114') $SKILL_CODE = "686"; // ��ҹ�ʵ�������Է�����͡����䢡�þٴ
			elseif ($EXPPOST=='90115') $SKILL_CODE = "010"; // �ѡ�Ԫҡ�þ�Һ�� 9 ��.
			elseif ($EXPPOST=='90118') $SKILL_CODE = "686"; // ��ҹ�ʵ �������Է��
			elseif ($EXPPOST=='90120') $SKILL_CODE = "687"; // ��ҹ�����䢡�þٴ
			elseif ($EXPPOST=='90122') $SKILL_CODE = "108"; // ��ҹ�Ҹ�ó�آ �ҢҾѲ���к���ö��·ʹ����������෤����շҧ���ᾷ��
			elseif ($EXPPOST=='90124') $SKILL_CODE = "012"; // ��ҹ�Ѳ���к�������
			elseif ($EXPPOST=='99999') $SKILL_CODE = "683"; // ������ҢҪӹҭ���
			$SKILL_CODE = (trim($SKILL_CODE))? "'".trim($SKILL_CODE)."'" : "NULL";

			if ($PT_CODE == '10') $PT_CODE = "11";
			$cmd = " SELECT MISCNAME_NEW FROM STATICDETAIL WHERE GCODE = '6' AND CODE = '$LEVRAN' ";
			$db_att1->send_cmd($cmd);
//			$db_att1->show_error();
			$data1 = $db_att1->get_array();
			$CL_NAME = trim($data1[MISCNAME_NEW]);
			$CL_NAME = str_replace("/", " ���� ", $CL_NAME);
			$CL_NAME = str_replace("�дѺ", "", $CL_NAME);

			$cmd = " SELECT CL_NAME FROM PER_CO_LEVEL WHERE CL_NAME = '$CL_NAME' ";
			$count_data = $db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "$cmd<br>";
			if (!$count_data) echo "��ǧ�дѺ���˹� $CL_NAME<br>";

			$POS_STATUS = 1;

			$cmd1 = " SELECT POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, CL_NAME, POS_SALARY, 
								  POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, POS_CONDITION, POS_DOC_NO, POS_REMARK, POS_DATE, POS_GET_DATE, 
								  POS_CHANGE_DATE, POS_STATUS FROM PER_POSITION WHERE POS_NO = '$POSNUM' "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if ($count_data) {
				$data1 = $db_dpis1->get_array();
				echo "POS_NO - $POSNUM<br>";
				if ($OT_CODE != trim($data1[OT_CODE])) echo "OT_CODE - $OT_CODE != trim($data1[OT_CODE])<br>";
				if ($ORG_ID_1 != trim($data1[ORG_ID_1])) echo "ORG_ID_1 - $ORG_ID_1 != trim($data1[ORG_ID_1])<br>";
				if ($ORG_ID_2 != trim($data1[ORG_ID_2])) echo "ORG_ID_2 - $ORG_ID_2 != trim($data1[ORG_ID_2])<br>";
				if ($PM_CODE != trim($data1[PM_CODE])) echo "PM_CODE - $PM_CODE != trim($data1[PM_CODE])<br>";
				if ($PL_CODE != trim($data1[PL_CODE])) echo "PL_CODE - $PL_CODE != trim($data1[PL_CODE])<br>";
				if ($CL_NAME != trim($data1[CL_NAME])) echo "CL_NAME - $CL_NAME != trim($data1[CL_NAME])<br>";
				if ($BUDGET != trim($data1[POS_SALARY])) echo "POS_SALARY - $BUDGET != trim($data1[POS_SALARY])<br>";
				if ($POSITIONINSENTIVE != trim($data1[POS_MGTSALARY])) echo "POS_MGTSALARY - $POSITIONINSENTIVE != trim($data1[POS_MGTSALARY])<br>";
				if ($SKILL_CODE != trim($data1[SKILL_CODE])) echo "SKILL_CODE - $SKILL_CODE != trim($data1[SKILL_CODE])<br>";
				if ($PT_CODE != trim($data1[PT_CODE])) echo "PT_CODE - $PT_CODE != trim($data1[PT_CODE])<br>";
				if ($PC_CODE != trim($data1[PC_CODE])) echo "PC_CODE - $PC_CODE != trim($data1[PC_CODE])<br>";
				if ($POS_CONDITION != trim($data1[POS_CONDITION])) echo "POS_CONDITION - $POS_CONDITION != trim($data1[POS_CONDITION])<br>";
				if ($POS_REMARK != trim($data1[POS_REMARK])) echo "POS_REMARK - $POS_REMARK != trim($data1[POS_REMARK])<br>";
				if ($CSCDATE != trim($data1[POS_DATE])) echo "POS_DATE - $CSCDATE != trim($data1[POS_DATE])<br>";
				if ($BOBDATE != trim($data1[POS_GET_DATE])) echo "POS_GET_DATE - $BOBDATE != trim($data1[POS_GET_DATE])<br>";
				if ($POSDATE != trim($data1[POS_CHANGE_DATE])) echo "POS_CHANGE_DATE - $POSDATE != trim($data1[POS_CHANGE_DATE])<br>";
				if ($POS_STATUS != trim($data1[POS_STATUS])) echo "POS_STATUS - $POS_STATUS != trim($data1[POS_STATUS])<br>";
			} else {
				$cmd = " INSERT INTO PER_POSITION (POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, CL_NAME, POS_SALARY, 
								  POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, POS_CONDITION, POS_DOC_NO, POS_REMARK, POS_DATE, POS_GET_DATE, 
								  POS_CHANGE_DATE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
								  VALUES ($POSNUM, $ORG_ID, '$POSNUM', '$OT_CODE', $ORG_ID_1, $ORG_ID_2, $PM_CODE, $PL_CODE, '$CL_NAME', $BUDGET, 
								  $POSITIONINSENTIVE, $SKILL_CODE, '$PT_CODE', '$PC_CODE', '$POS_CONDITION', NULL, '$POS_REMARK', '$CSCDATE', '$BOBDATE', 
								  '$POSDATE', $POS_STATUS, $UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID) ";
	//			$db_dpis->send_cmd($cmd);
	//			echo "$cmd<br>==================<br>";
	//			$db_dpis->show_error();
	//			echo "<br>end ". ++$i  ."=======================<br>";
				$cmd1 = " SELECT POS_ID FROM PER_POSITION WHERE POS_ID = $POSNUM "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_POSITION', '$POS_NO', '$POSNUM', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$POS_ID++;
		} // end while						

		$cmd = " select count(POS_ID) as COUNT_NEW from PER_POSITION ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_POSITION - $PER_POSITION = $COUNT_NEW<br>";
/*
		$cmd = " ALTER TABLE PER_TRAINING ENABLE CONSTRAINTS FK1_PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PUNISHMENT ENABLE CONSTRAINTS FK1_PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORDTL ENABLE CONSTRAINTS FK3_PER_DECORDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORATEHIS ENABLE CONSTRAINTS FK1_PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSE ENABLE CONSTRAINTS FK4_PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSEDTL ENABLE CONSTRAINTS FK2_PER_COURSEDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS ENABLE CONSTRAINTS FK1_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALPROMOTE ENABLE CONSTRAINTS FK2_PER_SALPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALARYHIS ENABLE CONSTRAINTS FK1_PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_P ENABLE CONSTRAINTS FK2_PER_PROMOTE_P ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_E ENABLE CONSTRAINTS FK2_PER_PROMOTE_E ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_C ENABLE CONSTRAINTS FK1_PER_PROMOTE_C ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ORG_JOB ENABLE CONSTRAINTS FK1_PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MOVE_REQ ENABLE CONSTRAINTS FK1_PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER ENABLE CONSTRAINTS FK1_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER ENABLE CONSTRAINTS FK2_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_BONUSPROMOTE ENABLE CONSTRAINTS FK2_PER_BONUSPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TIMEHIS ENABLE CONSTRAINTS FK1_PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SCHOLAR ENABLE CONSTRAINTS FK1_PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_REWARDHIS ENABLE CONSTRAINTS FK1_PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_POSITIONHIS ENABLE CONSTRAINTS FK1_PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PERSONALPIC ENABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_NAMEHIS ENABLE CONSTRAINTS FK1_PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MARRHIS ENABLE CONSTRAINTS FK1_PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EDUCATE ENABLE CONSTRAINTS FK1_PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENTHIS ENABLE CONSTRAINTS FK1_PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENT ENABLE CONSTRAINTS FK1_PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABILITY ENABLE CONSTRAINTS FK1_PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST2DTL ENABLE CONSTRAINTS FK2_PER_INVEST2DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST1DTL ENABLE CONSTRAINTS FK2_PER_INVEST1DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_HEIR ENABLE CONSTRAINTS FK1_PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ENABLE CONSTRAINTS FK1_PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRAHIS ENABLE CONSTRAINTS FK1_PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_KPI ENABLE CONSTRAINTS FK1_PER_KPI ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_FAMILY ENABLE CONSTRAINTS FK1_PER_FAMILY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;
*/
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if
	
	if($command=='PERSONAL'){ // 8587
/*		$cmd = " ALTER TABLE PER_TRAINING DISABLE CONSTRAINTS FK1_PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PUNISHMENT DISABLE CONSTRAINTS FK1_PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORDTL DISABLE CONSTRAINTS FK3_PER_DECORDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORATEHIS DISABLE CONSTRAINTS FK1_PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSE DISABLE CONSTRAINTS FK4_PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSEDTL DISABLE CONSTRAINTS FK2_PER_COURSEDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS DISABLE CONSTRAINTS FK1_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALPROMOTE DISABLE CONSTRAINTS FK2_PER_SALPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALARYHIS DISABLE CONSTRAINTS FK1_PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_P DISABLE CONSTRAINTS FK2_PER_PROMOTE_P ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_E DISABLE CONSTRAINTS FK2_PER_PROMOTE_E ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_C DISABLE CONSTRAINTS FK1_PER_PROMOTE_C ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ORG_JOB DISABLE CONSTRAINTS FK1_PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MOVE_REQ DISABLE CONSTRAINTS FK1_PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER DISABLE CONSTRAINTS FK1_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER DISABLE CONSTRAINTS FK2_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_BONUSPROMOTE DISABLE CONSTRAINTS FK2_PER_BONUSPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TIMEHIS DISABLE CONSTRAINTS FK1_PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SCHOLAR DISABLE CONSTRAINTS FK1_PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_REWARDHIS DISABLE CONSTRAINTS FK1_PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_POSITIONHIS DISABLE CONSTRAINTS FK1_PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PERSONALPIC DISABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_NAMEHIS DISABLE CONSTRAINTS FK1_PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MARRHIS DISABLE CONSTRAINTS FK1_PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EDUCATE DISABLE CONSTRAINTS FK1_PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENTHIS DISABLE CONSTRAINTS FK1_PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENT DISABLE CONSTRAINTS FK1_PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABILITY DISABLE CONSTRAINTS FK1_PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST2DTL DISABLE CONSTRAINTS FK2_PER_INVEST2DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST1DTL DISABLE CONSTRAINTS FK2_PER_INVEST1DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_HEIR DISABLE CONSTRAINTS FK1_PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS DISABLE CONSTRAINTS FK1_PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRAHIS DISABLE CONSTRAINTS FK1_PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_KPI DISABLE CONSTRAINTS FK1_PER_KPI ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_FAMILY DISABLE CONSTRAINTS FK1_PER_FAMILY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$table = array( "per_absenthis", "per_marrhis", "per_timehis", "per_ability", "per_rewardhis", "per_extrahis", "per_extra_incomehis", "per_namehis", 
			"per_punishment", "per_decoratehis", "per_training", "per_educate", "per_salaryhis", "per_positionhis", "per_personalpic", "per_heir", "per_servicehis", 
			"per_comdtl", "per_bonuspromote", "per_bonusquotadtl2", "per_bonusquotadtl1", "per_bonusquota", "per_special_skill", "per_coursedtl", "per_decordtl", "per_org_job", 
			"per_promote_p", "per_letter", "per_move_req", "per_scholar", "per_absent", "per_invest1dtl", "per_promote_c", "per_address", "per_kpi_form", 
			"per_salpromote", "per_family", "per_licensehis" );
		for ( $i=0; $i<count($table); $i++ ) { 
			if ($table[$i]=="per_bonusquota" || $table[$i]=="per_scholar" || $table[$i]=="per_salquota" || $table[$i]=="per_command" || $table[$i]=="per_decor")
				$cmd = " delete from $table[$i] ";
			else
				$cmd = " truncate table $table[$i] ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end for
*/
// ����Ҫ��� 8587
		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_PERSONAL' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;
/*
		$cmd = " DELETE FROM PER_PERSONAL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();

		$cmd = " SELECT TITLE, PERNAME FROM PERTIT WHERE TITLE IN (SELECT DISTINCT TITLE FROM PERSON) ORDER BY TITLE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
*/
		$cmd = " SELECT EMPID, A.POSNUM, TITLE, FNAME, LNAME, REALDIVCODE, REALSECTCODE, REALSUBSECTCODE, SEX, RELIGION, BIRTH, HOMEPROV, 
						  MARITAL, ENTRYCS, ENTRYDP, JOBCLASSID, PROMDATE, SALARY, FLOWDATE, A.ENTRDATE, IDNO, 
						  GROUPFLOW, FLOW, ADDRESS1, ADDRESS2, PROVINCE, TELEPHONE, COUNTRY, VACATION, FATHERNAME, 
						  MOTHERNAME, SPOUSENAME, HISTORYID, TAXID 
						  FROM PERSON A, POST B
						  WHERE A.POSNUM = B.POSNUM
						  ORDER BY CINT(A.POSNUM) ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_PERSONAL++;
			$EMPID = $data[EMPID];
			$POSNUM = trim($data[POSNUM]);
			$TITLE = trim($data[TITLE]);
			$FNAME = trim($data[FNAME]);
			$LNAME = trim($data[LNAME]);
			$REALDIVCODE = trim($data[REALDIVCODE]);
			$REALSECTCODE = trim($data[REALSECTCODE]);
			$REALSUBSECTCODE = trim($data[REALSUBSECTCODE]);
			$SEX = trim($data[SEX]);
			$RELIGION = trim($data[RELIGION]);
			$BIRTH = trim($data[BIRTH]);
			$HOMEPROV = $data[HOMEPROV];
			$MARITAL = trim($data[MARITAL]);
			$ENTRYCS = trim($data[ENTRYCS]);
			$ENTRYDP = trim($data[ENTRYDP]);
			$JOBCLASSID = trim($data[JOBCLASSID]);
			$PROMDATE = trim($data[PROMDATE]);
			$SALARY = $data[SALARY] + 0;
			$FLOWDATE = trim($data[FLOWDATE]);
			$ENTRDATE = trim($data[ENTRDATE]);
			$IDNO = trim($data[IDNO]);
			$GROUPFLOW = $data[GROUPFLOW];
			$FLOW = trim($data[FLOW]);
			$ADDRESS1 = trim($data[ADDRESS1]);
			$ADDRESS2 = trim($data[ADDRESS2]);
			$PROVINCE = trim($data[PROVINCE]);
			$TELEPHONE = trim($data[TELEPHONE]);
			$COUNTRY = trim($data[COUNTRY]);
			$VACATION = trim($data[VACATION]);
			$FATHERNAME = trim($data[FATHERNAME]);
			$arr_temp = explode("  ", $FATHERNAME);
			$PER_FATHERNAME = $arr_temp[0];
			$PN_CODE_F = "";
			if (substr($PER_FATHERNAME,0,3)=="���") {
				$PN_CODE_F = "003";
				$PER_FATHERNAME = substr($PER_FATHERNAME,3);
			}
			$PER_FATHERSURNAME = $arr_temp[1]." ".$arr_temp[2];
			$MOTHERNAME = trim($data[MOTHERNAME]);
			$arr_temp = explode("  ", $MOTHERNAME);
			$PER_MOTHERNAME = $arr_temp[0];
			$PN_CODE_M = "";
			if (substr($PER_MOTHERNAME,0,3)=="�ҧ") {
				$PN_CODE_M = "005";
				$PER_MOTHERNAME = substr($PER_MOTHERNAME,3);
			}
			$PER_MOTHERSURNAME = $arr_temp[1]." ".$arr_temp[2];
			$HISTORYID = trim($data[HISTORYID]);
			$TAXID = trim($data[TAXID]);
			if ($POSNUM==509) $TAXID = "1018989332x";
			elseif ($POSNUM==641) $TAXID = "1018992127x";
			elseif ($POSNUM==1890) $TAXID = "1196870965x";
			elseif ($POSNUM==3090) $TAXID = "1004502477x";
			elseif ($POSNUM==4416) $IDNO = "364020031091x";
			elseif ($POSNUM==5026) $TAXID = "1196834554x";
			elseif ($POSNUM==5030) $TAXID = "1006221058x";
			elseif ($POSNUM==5115) $IDNO = "320060029331x";
			elseif ($POSNUM==6484) $IDNO = "319090017987x";
			elseif ($POSNUM==7075) {
				$IDNO = "310040036627x";
				$TAXID = "1682286119x";
			}

			if ($JOBCLASSID=='11') $LEVEL_NO = "O1"; 
			elseif ($JOBCLASSID=='12') $LEVEL_NO = "O2"; 
			elseif ($JOBCLASSID=='13') $LEVEL_NO = "O3"; 
			elseif ($JOBCLASSID=='14') $LEVEL_NO = "O4"; 
			elseif ($JOBCLASSID=='21') $LEVEL_NO = "K1"; 
			elseif ($JOBCLASSID=='22') $LEVEL_NO = "K2"; 
			elseif ($JOBCLASSID=='23') $LEVEL_NO = "K3"; 
			elseif ($JOBCLASSID=='24') $LEVEL_NO = "K4"; 
			elseif ($JOBCLASSID=='25') $LEVEL_NO = "K5"; 
			elseif ($JOBCLASSID=='31') $LEVEL_NO = "D1"; 
			elseif ($JOBCLASSID=='32') $LEVEL_NO = "D2"; 
			elseif ($JOBCLASSID=='41') $LEVEL_NO = "M1"; 
			elseif ($JOBCLASSID=='42') $LEVEL_NO = "M2"; 

			$cmd = " select PT_CODE from PER_POSITION where POS_ID = $POSNUM ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$PT_CODE = trim($data_dpis[PT_CODE]);

			$cmd = " select MS_SALARY from PER_MGTSALARY where trim(PT_CODE)='$PT_CODE' and trim(LEVEL_NO)='$LEVEL_NO' and MS_ACTIVE=1 ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$PER_MGTSALARY = $data_dpis[MS_SALARY];

			$MOV_CODE = "213"; // ����
			$PER_ORDAIN = 0;
			$PER_SOLDIER = 0; 
			$PER_MEMBER = 0;
//			$per_retiredate = (substr($BIRTH,0,4) + 60).'-'.substr($BIRTH,5,2).'-'.substr($BIRTH,8,2).' 00:00:00';

			if (!$SEX) {
				if ($TITLE=='1') $SEX = "1"; // ���
				else $SEX = "2"; // ˭ԧ
			}

			$PN_CODE = "";
			if ($TITLE=='1') $PN_CODE = "003"; // ���
			elseif ($TITLE=='2') $PN_CODE = "005"; // �ҧ
			elseif ($TITLE=='3') $PN_CODE = "004"; // �ҧ���
			elseif ($TITLE=='17') $PN_CODE = "669"; // ���µ��Ǩ�
			elseif ($TITLE=='18') $PN_CODE = "667"; // ���µ��Ǩ�͡
			elseif ($TITLE=='19') $PN_CODE = "665"; // �ѹ���Ǩ���
			elseif ($TITLE=='33') $PN_CODE = "223"; // �Ժ�͡
			elseif ($TITLE=='37') $PN_CODE = "219"; // ��ҷ�����µ��
			elseif ($TITLE=='40') $PN_CODE = "214"; // �����͡
			elseif ($TITLE=='41') $PN_CODE = "212"; // �ѹ���
			elseif ($TITLE=='42') $PN_CODE = "210"; // �ѹ�
			elseif ($TITLE=='43') $PN_CODE = "208"; // �ѹ�͡
			elseif ($TITLE=='77') $PN_CODE = "520"; // �ѹ����ҡ���͡
			elseif ($TITLE=='82') $PN_CODE = "510"; // �.�.
			elseif ($TITLE=='89') $PN_CODE = "519"; // �.�.�.˭ԧ
			elseif ($TITLE=='93') $PN_CODE = "674"; // �Ժ���Ǩ���˭ԧ
			elseif ($TITLE=='94') $PN_CODE = "378"; // ����͡˭ԧ
			elseif ($TITLE=='96') $PN_CODE = "217"; // ��ҷ�������
			elseif ($TITLE=='97') $PN_CODE = "733"; // �.�.�.˭ԧ
			elseif ($TITLE=='98') $PN_CODE = "734"; // �ѹ���˭ԧ
			elseif ($TITLE=='99') $PN_CODE = "735"; // ���͵��˭ԧ
			elseif ($TITLE=='100') $PN_CODE = "736"; // �Ժ�˭ԧ
			elseif ($TITLE=='102') $PN_CODE = "130"; //	�س˭ԧ
			elseif ($TITLE=='103') $PN_CODE = "214"; //	�����͡
			elseif ($TITLE=='104') $PN_CODE = "219"; //	��ҷ�����µ��
			elseif ($TITLE=='106') $PN_CODE = "229"; //	��ҷ�� �.�.˭ԧ
			elseif ($TITLE=='107') $PN_CODE = "374"; //	����͡
			elseif ($TITLE=='108') $PN_CODE = "737"; // �����˭ԧ
			elseif ($TITLE=='109') $PN_CODE = "213"; //	��ҷ��ѹ���
			elseif ($TITLE=='110') $PN_CODE = "738"; // ���µ��˭ԧ
			elseif ($TITLE=='112') $PN_CODE = "514"; //	�����ҡ���͡
			elseif ($TITLE=='113') $PN_CODE = "516"; //	�����ҡ���
			elseif ($TITLE=='114') $PN_CODE = "512"; //	�����ҡ�ȵ��
			elseif ($TITLE=='115') $PN_CODE = "229"; //	��ҷ�����µ��˭ԧ
			elseif ($TITLE=='116') $PN_CODE = "739"; // ��ҷ�����µ��Ǩ�˭ԧ
			elseif ($TITLE=='117') $PN_CODE = "365"; //	�����͡
			elseif ($TITLE=='118') $PN_CODE = "740"; // �����ҡ���˭ԧ
			elseif ($TITLE=='119') $PN_CODE = "741"; // �����ҡ���͡˭ԧ
			elseif ($TITLE=='122') $PN_CODE = "399"; // ����Ժ�͡˭ԧ
			else echo "�ӹ�˹�Ҫ��� $TITLE $FNAME $LNAME<br>";
			$PN_CODE = (trim($PN_CODE))? "'".trim($PN_CODE)."'" : "'001'";

			$ASS_ORG_ID = "NULL";
			$OT_CODE = "01";
			$PER_STATUS = 1;
			if (!$MARITAL || $MARITAL == '0') $MARITAL = '1';

			$cmd1 = " SELECT PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, PER_SURNAME, PER_ENG_NAME, PER_ENG_SURNAME, 
								  ORG_ID, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, PER_ORGMGT, PER_SALARY, PER_MGTSALARY, PER_SPSALARY, PER_GENDER, MR_CODE, 
								  PER_CARDNO, PER_OFFNO, PER_TAXNO, PER_BLOOD, RE_CODE, PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, PER_OCCUPYDATE, 
								  PER_POSDATE, PN_CODE_F, PER_FATHERNAME, PER_FATHERSURNAME, PN_CODE_M, PER_MOTHERNAME, PER_MOTHERSURNAME, 
								  PER_ADD1, PER_ADD2, PV_CODE, MOV_CODE, PER_ORDAIN, PER_SOLDIER, PER_MEMBER, PER_STATUS, PER_HIP_FLAG, PER_CERT_OCC, 
								  DEPARTMENT_ID FROM PER_PERSONAL WHERE PER_CARDNO = '$IDNO' OR (PER_NAME = '$FNAME' AND PER_SURNAME = '$LNAME') "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if ($count_data) {
				$data1 = $db_dpis1->get_array();
				$PER_ID = $data1[PER_ID];
				echo "PER_ID - $EMPID - $PER_ID<br>";
				if ($OT_CODE != trim($data1[OT_CODE])) echo "OT_CODE - $OT_CODE != trim($data1[OT_CODE])<br>";
				if ($PN_CODE != trim($data1[PN_CODE])) echo "PN_CODE - $PN_CODE != trim($data1[PN_CODE])<br>";
				if ($FNAME != trim($data1[PER_NAME])) echo "PER_NAME - $FNAME != trim($data1[PER_NAME])<br>";
				if ($PM_CODE != trim($data1[PM_CODE])) echo "PM_CODE - $PM_CODE != trim($data1[PM_CODE])<br>";
				if ($PL_CODE != trim($data1[PL_CODE])) echo "PL_CODE - $PL_CODE != trim($data1[PL_CODE])<br>";
				if ($CL_NAME != trim($data1[CL_NAME])) echo "CL_NAME - $CL_NAME != trim($data1[CL_NAME])<br>";
				if ($BUDGET != trim($data1[POS_SALARY])) echo "POS_SALARY - $BUDGET != trim($data1[POS_SALARY])<br>";
				if ($POSITIONINSENTIVE != trim($data1[POS_MGTSALARY])) echo "POS_MGTSALARY - $POSITIONINSENTIVE != trim($data1[POS_MGTSALARY])<br>";
				if ($SKILL_CODE != trim($data1[SKILL_CODE])) echo "SKILL_CODE - $SKILL_CODE != trim($data1[SKILL_CODE])<br>";
				if ($PT_CODE != trim($data1[PT_CODE])) echo "PT_CODE - $PT_CODE != trim($data1[PT_CODE])<br>";
				if ($PC_CODE != trim($data1[PC_CODE])) echo "PC_CODE - $PC_CODE != trim($data1[PC_CODE])<br>";
				if ($POS_CONDITION != trim($data1[POS_CONDITION])) echo "POS_CONDITION - $POS_CONDITION != trim($data1[POS_CONDITION])<br>";
				if ($POS_REMARK != trim($data1[POS_REMARK])) echo "POS_REMARK - $POS_REMARK != trim($data1[POS_REMARK])<br>";
				if ($CSCDATE != trim($data1[POS_DATE])) echo "POS_DATE - $CSCDATE != trim($data1[POS_DATE])<br>";
				if ($BOBDATE != trim($data1[POS_GET_DATE])) echo "POS_GET_DATE - $BOBDATE != trim($data1[POS_GET_DATE])<br>";
				if ($POSDATE != trim($data1[POS_CHANGE_DATE])) echo "POS_CHANGE_DATE - $POSDATE != trim($data1[POS_CHANGE_DATE])<br>";
				if ($POS_STATUS != trim($data1[POS_STATUS])) echo "POS_STATUS - $POS_STATUS != trim($data1[POS_STATUS])<br>";
			} else {
				$cmd = " INSERT INTO PER_PERSONAL (PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, PER_SURNAME, PER_ENG_NAME, PER_ENG_SURNAME, 
								  ORG_ID, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, PER_ORGMGT, PER_SALARY, PER_MGTSALARY, PER_SPSALARY, PER_GENDER, MR_CODE, 
								  PER_CARDNO, PER_OFFNO, PER_TAXNO, PER_BLOOD, RE_CODE, PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, PER_OCCUPYDATE, 
								  PER_POSDATE, PN_CODE_F, PER_FATHERNAME, PER_FATHERSURNAME, PN_CODE_M, PER_MOTHERNAME, PER_MOTHERSURNAME, 
								  PER_ADD1, PER_ADD2, PV_CODE, MOV_CODE, PER_ORDAIN, PER_SOLDIER, PER_MEMBER, PER_STATUS, PER_HIP_FLAG, PER_CERT_OCC, 
								  DEPARTMENT_ID, LEVEL_NO_SALARY, UPDATE_USER, UPDATE_DATE, PER_HOME_TEL)
								  VALUES ($EMPID, 1, '$OT_CODE', $PN_CODE, trim('$FNAME'), trim('$LNAME'), NULL, NULL, $ASS_ORG_ID, $POSNUM, NULL, 
								  NULL, '$LEVEL_NO', 0, $SALARY, 0, 0, $SEX, '$MARITAL', '$IDNO', NULL, '$TAXID', NULL, 
								  '$RELIGION', '$BIRTH', NULL, '$ENTRYCS', '$ENTRYDP', NULL, '$PN_CODE_F', trim('$PER_FATHERNAME'), 
								  trim('$PER_FATHERSURNAME'), '$PN_CODE_M', trim('$PER_MOTHERNAME'), trim('$PER_MOTHERSURNAME'), '$ADDRESS1', '$ADDRESS1', NULL, 
								  '$MOV_CODE', $PER_ORDAIN, $PER_SOLDIER, $PER_MEMBER, $PER_STATUS, NULL, NULL, $DEPARTMENT_ID, 
								  '$LEVEL_NO', $UPDATE_USER, '$UPDATE_DATE', '$TELEPHONE') ";
	//			$db_dpis->send_cmd($cmd);
	//			echo "$cmd<br>==================<br>";
	//			$db_dpis->show_error();
	//			echo "<br>end ". ++$i  ."=======================<br>"; 

				$cmd1 = " SELECT PER_ID FROM PER_PERSONAL WHERE PER_ID = $EMPID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
			}

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_PERSONAL', '$EMPID', '$PER_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_PERSONAL where PER_TYPE = 1 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_PERSONAL - $PER_PERSONAL - $COUNT_NEW<br>";
/*		
		$cmd = " ALTER TABLE PER_TRAINING ENABLE CONSTRAINTS FK1_PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PUNISHMENT ENABLE CONSTRAINTS FK1_PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORDTL ENABLE CONSTRAINTS FK3_PER_DECORDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORATEHIS ENABLE CONSTRAINTS FK1_PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSE ENABLE CONSTRAINTS FK4_PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSEDTL ENABLE CONSTRAINTS FK2_PER_COURSEDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS ENABLE CONSTRAINTS FK1_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALPROMOTE ENABLE CONSTRAINTS FK2_PER_SALPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALARYHIS ENABLE CONSTRAINTS FK1_PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_P ENABLE CONSTRAINTS FK2_PER_PROMOTE_P ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_E ENABLE CONSTRAINTS FK2_PER_PROMOTE_E ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_C ENABLE CONSTRAINTS FK1_PER_PROMOTE_C ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ORG_JOB ENABLE CONSTRAINTS FK1_PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MOVE_REQ ENABLE CONSTRAINTS FK1_PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER ENABLE CONSTRAINTS FK1_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER ENABLE CONSTRAINTS FK2_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_BONUSPROMOTE ENABLE CONSTRAINTS FK2_PER_BONUSPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TIMEHIS ENABLE CONSTRAINTS FK1_PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SCHOLAR ENABLE CONSTRAINTS FK1_PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_REWARDHIS ENABLE CONSTRAINTS FK1_PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_POSITIONHIS ENABLE CONSTRAINTS FK1_PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PERSONALPIC ENABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_NAMEHIS ENABLE CONSTRAINTS FK1_PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MARRHIS ENABLE CONSTRAINTS FK1_PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EDUCATE ENABLE CONSTRAINTS FK1_PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENTHIS ENABLE CONSTRAINTS FK1_PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENT ENABLE CONSTRAINTS FK1_PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABILITY ENABLE CONSTRAINTS FK1_PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST2DTL ENABLE CONSTRAINTS FK2_PER_INVEST2DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST1DTL ENABLE CONSTRAINTS FK2_PER_INVEST1DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_HEIR ENABLE CONSTRAINTS FK1_PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ENABLE CONSTRAINTS FK1_PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRAHIS ENABLE CONSTRAINTS FK1_PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_KPI ENABLE CONSTRAINTS FK1_PER_KPI ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_FAMILY ENABLE CONSTRAINTS FK1_PER_FAMILY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;
*/
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if
	
	if($command=='EXPERS'){ // 1289
		$cmd = " delete from per_personal where update_user = $UPDATE_USER and PER_STATUS = 2 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT MAX(PER_ID) AS PER_ID FROM PER_PERSONAL ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_ID = $data[PER_ID];

		$cmd = " SELECT EMPID, DIVCODE, SECTCODE, SUBSECTCODE, PROV, IDNO, TITLE, FNAME, LNAME, SEX, RELIGION, BIRTH, HOMEPROV, ENTRYCS, ENTRYDP, 
				 [LEVEL] as LEVELNO, SALARY, GROUP, FLOW, FLOWDATE 
						  FROM EXPERS
						  ORDER BY EMPID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_PERSONAL++;
			$EMPID = $data[EMPID];
			$TITLE = trim($data[TITLE]);
			$FNAME = trim($data[FNAME]);
			if (!$FNAME) $FNAME = "-";
			$LNAME = trim($data[LNAME]);
			if (!$LNAME) $LNAME = "-";
			$DIVCODE = trim($data[DIVCODE]);
			$SECTCODE = trim($data[SECTCODE]);
			$SUBSECTCODE = trim($data[SUBSECTCODE]);
			$SEX = trim($data[SEX]);
			$RELIGION = trim($data[RELIGION]);
			$RELIGION = str_pad($RELIGION, 2, "0", STR_PAD_LEFT);
			if (!$RELIGION) $RELIGION = "00";
			$BIRTH = trim($data[BIRTH]);
			$HOMEPROV = $data[HOMEPROV];
			$ENTRYCS = trim($data[ENTRYCS]);
			if (!$ENTRYCS) $ENTRYCS = "-";
			$ENTRYDP = trim($data[ENTRYDP]);
			if (!$ENTRYDP) $ENTRYDP = "-";
			$LEVEL_NO = trim($data[LEVELNO]);
			$LEVEL_NO = str_pad($LEVEL_NO, 2, "0", STR_PAD_LEFT);
			if ($LEVEL_NO=="00") $LEVEL_NO = "01";
			$SALARY = $data[SALARY] + 0;
			$FLOWDATE = trim($data[FLOWDATE]);
			$IDNO = trim($data[IDNO]);
			$GROUP = $data[GROUP];
			$FLOW = trim($data[FLOW]);
			$PROV = trim($data[PROV]);

			$PER_MGTSALARY = 0; // ����
			$MOV_CODE = "213"; // ����
			$PER_ORDAIN = 0;
			$PER_SOLDIER = 0; 
			$PER_MEMBER = 0;

			$PN_CODE = "";
			if ($TITLE=='1') $PN_CODE = "003"; // ���
			elseif ($TITLE=='2') $PN_CODE = "005"; // �ҧ
			elseif ($TITLE=='3') $PN_CODE = "004"; // �ҧ���
			elseif ($TITLE=='17') $PN_CODE = "669"; // ���µ��Ǩ�
			elseif ($TITLE=='18') $PN_CODE = "667"; // ���µ��Ǩ�͡
			elseif ($TITLE=='19') $PN_CODE = "665"; // �ѹ���Ǩ���
			elseif ($TITLE=='33') $PN_CODE = "223"; // �Ժ�͡
			elseif ($TITLE=='37') $PN_CODE = "219"; // ��ҷ�����µ��
			elseif ($TITLE=='40') $PN_CODE = "214"; // �����͡
			elseif ($TITLE=='41') $PN_CODE = "212"; // �ѹ���
			elseif ($TITLE=='42') $PN_CODE = "210"; // �ѹ�
			elseif ($TITLE=='43') $PN_CODE = "208"; // �ѹ�͡
			elseif ($TITLE=='77') $PN_CODE = "520"; // �ѹ����ҡ���͡
			elseif ($TITLE=='82') $PN_CODE = "510"; // �.�.
			elseif ($TITLE=='89') $PN_CODE = "519"; // �.�.�.˭ԧ
			elseif ($TITLE=='92') $PN_CODE = "741"; //	�����ҡ���͡˭ԧ
			elseif ($TITLE=='93') $PN_CODE = "674"; // �Ժ���Ǩ���˭ԧ
			elseif ($TITLE=='94') $PN_CODE = "378"; // ����͡˭ԧ
			elseif ($TITLE=='96') $PN_CODE = "217"; // ��ҷ�������
			elseif ($TITLE=='97') $PN_CODE = "733"; // �.�.�.˭ԧ
			elseif ($TITLE=='98') $PN_CODE = "734"; // �ѹ���˭ԧ
			elseif ($TITLE=='99') $PN_CODE = "735"; // ���͵��˭ԧ
			elseif ($TITLE=='100') $PN_CODE = "736"; // �Ժ�˭ԧ
			elseif ($TITLE=='102') $PN_CODE = "130"; //	�س˭ԧ
			elseif ($TITLE=='103') $PN_CODE = "214"; //	�����͡
			elseif ($TITLE=='106') $PN_CODE = "229"; //	��ҷ�� �.�.˭ԧ
			elseif ($TITLE=='107') $PN_CODE = "374"; //	����͡
			elseif ($TITLE=='108') $PN_CODE = "737"; // �����˭ԧ
			elseif ($TITLE=='109') $PN_CODE = "213"; //	��ҷ��ѹ���
			elseif ($TITLE=='110') $PN_CODE = "738"; // ���µ��˭ԧ
			elseif ($TITLE=='112') $PN_CODE = "514"; //	�����ҡ���͡
			elseif ($TITLE=='113') $PN_CODE = "516"; //	�����ҡ���
			elseif ($TITLE=='114') $PN_CODE = "512"; //	�����ҡ�ȵ��
			elseif ($TITLE=='115') $PN_CODE = "229"; //	��ҷ�����µ��˭ԧ
			elseif ($TITLE=='116') $PN_CODE = "739"; // ��ҷ�����µ��Ǩ�˭ԧ
			elseif ($TITLE=='117') $PN_CODE = "365"; //	�����͡
			elseif ($TITLE=='118') $PN_CODE = "740"; // �����ҡ���˭ԧ
			elseif ($TITLE=='119') $PN_CODE = "741"; // �����ҡ���͡˭ԧ
			$PN_CODE = (trim($PN_CODE))? "'".trim($PN_CODE)."'" : "'001'";

			$ASS_ORG_ID = "NULL";
			$OT_CODE = "01";
			$PER_STATUS = 2;
			if (!$MARITAL || $MARITAL == '0') $MARITAL = '1';
			if (!$SEX) 
				if ($TITLE=='1') $SEX = "1"; // ���
				elseif ($TITLE=='2' || $TITLE=='3') $SEX = "2"; // �ҧ

			$cmd = " select PER_CARDNO from PER_PERSONAL where PER_CARDNO ='$IDNO' ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$PER_CARDNO = trim($data_dpis[PER_CARDNO]);
			if ($PER_CARDNO) $IDNO = 'x' . substr($IDNO,1);

			$cmd = " select PER_TAXNO from PER_PERSONAL where PER_TAXNO ='$TAXID' ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$PER_TAXNO = trim($data_dpis[PER_TAXNO]);
			if ($PER_TAXNO) $TAXID = $TAXID . 'x';

			$PER_ID++;

			$cmd = " INSERT INTO PER_PERSONAL (PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, PER_SURNAME, PER_ENG_NAME, PER_ENG_SURNAME, 
							  ORG_ID, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, PER_ORGMGT, PER_SALARY, PER_MGTSALARY, PER_SPSALARY, PER_GENDER, MR_CODE, 
							  PER_CARDNO, PER_OFFNO, PER_TAXNO, PER_BLOOD, RE_CODE, PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, PER_OCCUPYDATE, 
							  PER_POSDATE, PN_CODE_F, PER_FATHERNAME, PER_FATHERSURNAME, PN_CODE_M, PER_MOTHERNAME, PER_MOTHERSURNAME, 
							  PER_ADD1, PER_ADD2, PV_CODE, MOV_CODE, PER_ORDAIN, PER_SOLDIER, PER_MEMBER, PER_STATUS, PER_HIP_FLAG, PER_CERT_OCC, 
							  DEPARTMENT_ID, LEVEL_NO_SALARY, UPDATE_USER, UPDATE_DATE, PER_HOME_TEL)
							  VALUES ($PER_ID, 1, '$OT_CODE', $PN_CODE, trim('$FNAME'), trim('$LNAME'), NULL, NULL, $ASS_ORG_ID, NULL, NULL, 
							  NULL, '$LEVEL_NO', 0, $SALARY, 0, 0, $SEX, '$MARITAL', '$IDNO', NULL, '$TAXID', NULL, 
							  '$RELIGION', '$BIRTH', NULL, '$ENTRYCS', '$ENTRYDP', NULL, NULL, trim('$PER_FATHERNAME'), 
							  trim('$PER_FATHERSURNAME'), NULL, trim('$PER_MOTHERNAME'), trim('$PER_MOTHERSURNAME'), '$ADDRESS1', '$ADDRESS1', NULL, 
							  '$MOV_CODE', $PER_ORDAIN, $PER_SOLDIER, $PER_MEMBER, $PER_STATUS, NULL, NULL, $DEPARTMENT_ID, 
							  '$LEVEL_NO', $UPDATE_USER, '$UPDATE_DATE', '$TELEPHONE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>"; 

			$cmd1 = " SELECT PER_ID FROM PER_PERSONAL WHERE PER_ID = $PER_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_PERSONAL where PER_TYPE = 1 and PER_STATUS = 2 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_PERSONAL - $PER_PERSONAL - $COUNT_NEW<br>";
		
	} // end if
	
	if( $command=='POSITIONHIS' ){ // 35462 35462
		$cmd = " SELECT ADMINPOST, ADNAME1                 
						  FROM ADMIN
						  WHERE ADMINPOST IN (SELECT DISTINCT ADMINPOST FROM CAREER) 
						  ORDER BY  ADMINPOST ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT POSCODE, POS_NAME1                 
						  FROM POSTCODE
						  WHERE POSCODE IN (SELECT DISTINCT POSCODE FROM CAREER)
						  ORDER BY POSCODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();

		$cmd = " delete from per_positionhis where update_user = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT MAX(POH_ID) AS POH_ID FROM PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$POH_ID = $data[POH_ID] + 0;

		$cmd = " SELECT EMPID, DIVCODE, SECTCODE, SUBSECTCODE, PCODE, ADMINPOST, POSCODE, LEVELNO, GROUP, FLOW, ENDATE, EXDATE, DEPT FROM CAREER
						  ORDER BY EMPID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$POH_ID++;
			$EMPID = $data[EMPID];
			$DIVCODE = trim($data[DIVCODE]);
			$SECTCODE = trim($data[SECTCODE]);
			$SUBSECTCODE = trim($data[SUBSECTCODE]);
			$PCODE = trim($data[PCODE]);
			$ADMINPOST = trim($data[ADMINPOST]);
			$POSCODE = trim($data[POSCODE]);
			$LEVEL_NO = trim($data[LEVELNO]);
			$LEVEL_NO = str_pad($LEVEL_NO, 2, "0", STR_PAD_LEFT);
			$GROUP = trim($data[GROUP]);
			$FLOW = trim($data[FLOW]);
			$ENDATE = trim($data[ENDATE]);
			$EXDATE = trim($data[EXDATE]);
			$DEPT = trim($data[DEPT]);
			if (!$ENDATE) $ENDATE = $EXDATE;
			if ($LEVEL_NO=="00") $LEVEL_NO = "01";
			if (!$SUBSECTCODE) $SUBSECTCODE = "00";
			$ORG_WEBSITE = $DIVCODE . $SECTCODE . $SUBSECTCODE;
			$ORG_WEBSITE1 = $DIVCODE . $SECTCODE . "00";
			$ORG_WEBSITE2 = $DIVCODE . "00000";

			$POH_ORG1 = "";
			$POH_ORG2 = "";
			$POH_ORG3 = "";
			$POH_UNDER_ORG1 = "";
			$POH_UNDER_ORG2 = "";
			$cmd = " SELECT ORG_ID_REF, OL_CODE, ORG_NAME FROM PER_ORG WHERE ORG_WEBSITE = '$ORG_WEBSITE2' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "$cmd<br>";
			$data = $db_dpis->get_array();
			$ORG_ID_REF = $data[ORG_ID_REF];
			$OL_CODE = trim($data[OL_CODE]);
			if ($OL_CODE == "03") {
				$POH_ORG3 = trim($data[ORG_NAME]);
				$cmd = " SELECT ORG_ID_REF, OL_CODE, ORG_NAME FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd<br>";
				$data = $db_dpis->get_array();
				$OL_CODE = trim($data[OL_CODE]);
				if ($OL_CODE == "02") {
					$POH_ORG2 = trim($data[ORG_NAME]);
					$ORG_ID_REF = $data[ORG_ID_REF];
					$cmd = " SELECT OL_CODE, ORG_NAME FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
					$db_dpis->send_cmd($cmd);
//					$db_dpis->show_error();
//					echo "$cmd<br>";
					$data = $db_dpis->get_array();
					$OL_CODE = trim($data[OL_CODE]);
					if ($OL_CODE == "01") $POH_ORG1 = trim($data[ORG_NAME]);
				}
			}

			if ($ORG_WEBSITE2 != $ORG_WEBSITE1) {
				$cmd = " SELECT OL_CODE, ORG_NAME FROM PER_ORG WHERE ORG_WEBSITE = '$ORG_WEBSITE1' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd<br>";
				$data = $db_dpis->get_array();
				$OL_CODE = trim($data[OL_CODE]);
				if ($OL_CODE == "04") $POH_UNDER_ORG1 = trim($data[ORG_NAME]);

				if ($ORG_WEBSITE1 != $ORG_WEBSITE) {
					$cmd = " SELECT OL_CODE, ORG_NAME FROM PER_ORG WHERE ORG_WEBSITE = '$ORG_WEBSITE' ";
					$db_dpis->send_cmd($cmd);
//					$db_dpis->show_error();
//					echo "$cmd<br>";
					$data = $db_dpis->get_array();
					$OL_CODE = trim($data[OL_CODE]);
					if ($OL_CODE == "05") $POH_UNDER_ORG2 = trim($data[ORG_NAME]);
				}
			}

			$PM_CODE = "";
			if ($ADMINPOST=='024') $PM_CODE = "0297"; // ���ѭ�վ�Һ��
			elseif ($ADMINPOST=='078') $PM_CODE = "0375"; // ����ӹ�¡��ʶҺѹ
			elseif ($ADMINPOST=='212') $PM_CODE = "0234"; // ����ӹ�¡��
			elseif ($ADMINPOST=='213') $PM_CODE = "0357"; // ͸Ժ��
			elseif ($ADMINPOST=='222') $PM_CODE = "0270"; // �ͧ����ӹ�¡��
			elseif ($ADMINPOST=='223') $PM_CODE = "0276"; // �ͧ͸Ժ��
			elseif ($ADMINPOST=='248') $PM_CODE = "0289"; // �ԪҪվ੾�� (Ǫ.)
			elseif ($ADMINPOST=='310') $PM_CODE = "0251"; // ����ӹ�¡���ӹѡ
			elseif ($ADMINPOST=='311') $PM_CODE = "0283"; // �Ţҹء�á��
			elseif ($ADMINPOST=='312') $PM_CODE = "0235"; // ����ӹ�¡�áͧ
			elseif ($ADMINPOST=='315') $PM_CODE = "0243"; // ����ӹ�¡���ٹ��
			elseif ($ADMINPOST=='401') $PM_CODE = "0026"; // ���˹�ҷ�����������º�����Ἱ
			elseif ($ADMINPOST=='402') $PM_CODE = "9999"; // ���˹�ҷ������çҹ�����
			elseif ($ADMINPOST=='403') $PM_CODE = "9999"; // �ؤ�ҡ�
			elseif ($ADMINPOST=='405') $PM_CODE = "9999"; // ��Ҿ�ѡ�ҹ��á��
			elseif ($ADMINPOST=='408') $PM_CODE = "9999"; // ��Ҿ�ѡ�ҹ��ʴ�
			elseif ($ADMINPOST=='411') $PM_CODE = "9999"; // ���˹�ҷ��ѹ�֡������
			elseif ($ADMINPOST=='412') $PM_CODE = "9999"; // ���˹�ҷ������մ
			elseif ($ADMINPOST=='416') $PM_CODE = "9999"; // �ѡʶԵ�
			elseif ($ADMINPOST=='417') $PM_CODE = "9999"; // ���˹�ҷ���ǪʶԵ�
			elseif ($ADMINPOST=='419') $PM_CODE = "9999"; // ���˹�ҷ����������ѹ��
			elseif ($ADMINPOST=='420') $PM_CODE = "9999"; // ��Ҿ�ѡ�ҹ����Թ��кѭ��
			elseif ($ADMINPOST=='421') $PM_CODE = "9999"; // �ѡ�Ԫҡ���Թ��кѭ��
			elseif ($ADMINPOST=='424') $PM_CODE = "9999"; // ���˹�ҷ���Ъ�����ѹ��
			elseif ($ADMINPOST=='427') $PM_CODE = "9999"; // ��Ҿ�ѡ�ҹ�ʵ��ȹ�֡��
			elseif ($ADMINPOST=='428') $PM_CODE = "9999"; // �ѡ�Ԫҡ���ʵ��ȹ�֡��
			elseif ($ADMINPOST=='429') $PM_CODE = "0089"; // ���ᾷ��
			elseif ($ADMINPOST=='430') $PM_CODE = "9999"; // �ѹ�ᾷ��
			elseif ($ADMINPOST=='433') $PM_CODE = "9999"; // ���Ѫ��
			elseif ($ADMINPOST=='435') $PM_CODE = "9999"; // �ѡ����ҡ��
			elseif ($ADMINPOST=='438') $PM_CODE = "9999"; // ��Һ��෤�Ԥ
			elseif ($ADMINPOST=='440') $PM_CODE = "9999"; // ��Һ���ԪҪվ
			elseif ($ADMINPOST=='443') $PM_CODE = "9999"; // ���˹�ҷ���ѧ�ա��ᾷ��
			elseif ($ADMINPOST=='444') $PM_CODE = "9999"; // �ѡ�ѧ�ա��ᾷ��
			elseif ($ADMINPOST=='445') $PM_CODE = "9999"; // �ѡ����Ҿ�ӺѴ
			elseif ($ADMINPOST=='447') $PM_CODE = "9999"; // �ѡ�Ҫ�ǺӺѴ
			elseif ($ADMINPOST=='448') $PM_CODE = "9999"; // ��Ҿ�ѡ�ҹ�Է����ʵ����ᾷ��
			elseif ($ADMINPOST=='449') $PM_CODE = "9999"; // ��Ҿ�ѡ�ҹ���Ѫ����
			elseif ($ADMINPOST=='450') $PM_CODE = "9999"; // �ѡ�Ԫҡ���Ҹ�ó�آ
			elseif ($ADMINPOST=='452') $PM_CODE = "9999"; // ��Ҿ�ѡ�ҹ�Ǫ������鹿�
			elseif ($ADMINPOST=='453') $PM_CODE = "9999"; // �ѡ෤�Ԥ���ᾷ��
			elseif ($ADMINPOST=='459') $PM_CODE = "9999"; // ��ҧ����ػ�ó�
			elseif ($ADMINPOST=='462') $PM_CODE = "9999"; // ��ó��ѡ��
			elseif ($ADMINPOST=='465') $PM_CODE = "9999"; // �ѡ�Ԫҡ�ä���������
			elseif ($ADMINPOST=='901') $PM_CODE = "0194"; // ���ӹҭ���
			elseif ($ADMINPOST=='902') $PM_CODE = "0200"; // ���ӹҭ��þ����
			elseif ($ADMINPOST=='903') $PM_CODE = "0203"; // �������Ǫҭ
			elseif ($ADMINPOST=='904') $PM_CODE = "0207"; // �������Ǫҭ�����
			elseif ($ADMINPOST=='948') $PM_CODE = "0033"; // �
			elseif ($ADMINPOST=='949') $PM_CODE = "0034"; // Ǫ
			elseif ($ADMINPOST=='956') $PM_CODE = "0380"; // ����ӹ�¡���ç��Һ��
			elseif ($ADMINPOST=='958') $PM_CODE = "0026"; // ���˹�ҷ�����������º�����Ἱ
			elseif ($ADMINPOST=='965') $PM_CODE = "0033"; // ���˹觷���ջ��ʺ��ó� (�)
			elseif ($ADMINPOST=='966') $PM_CODE = "0034"; // ���˹��ԪҪվ (Ǫ.)
			elseif ($ADMINPOST=='999') $PM_CODE = "9999"; // �������˹觷ҧ������/�Ԫҡ��
			if ($ADMINPOST && !$PM_CODE) {
				$ADMIN_POST = '0' . $ADMINPOST;
				$cmd = " SELECT PM_NAME FROM PER_MGT WHERE PM_CODE = '$ADMIN_POST' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd<br>";
				$data = $db_dpis->get_array();
				$PM_NAME = trim($data[PM_NAME]);
//				if (!$PM_NAME) echo "���˹�㹡�ú����çҹ $ADMINPOST<br>";
			}
			$PM_CODE = (trim($PM_CODE))? "'".trim($PM_CODE)."'" : "NULL";

			$PL_CODE = "";
			if ($POSCODE=='10108') $PL_CODE = "010108"; // �ѡ������
			elseif ($POSCODE=='10403') $PL_CODE = "010403"; // ��Ҿ�ѡ�ҹ����ͧ
			elseif ($POSCODE=='10501') $PL_CODE = "010501"; // ���˹�ҷ�軡��ͧ
			elseif ($POSCODE=='10703') $PL_CODE = "010703"; // ���˹�ҷ�����������º�����Ἱ
			elseif ($POSCODE=='10903') $PL_CODE = "010903"; // ���˹�ҷ����������ҹ�ؤ��
			elseif ($POSCODE=='11003') $PL_CODE = "011003"; // ���˹�ҷ���к��ҹ����������
			elseif ($POSCODE=='11103') $PL_CODE = "011103"; // ���˹�ҷ������çҹ�����
			elseif ($POSCODE=='11403') $PL_CODE = "011403"; // �ؤ�ҡ�
			elseif ($POSCODE=='11612') $PL_CODE = "011612"; // ��Ҿ�ѡ�ҹ��á��
			elseif ($POSCODE=='11613') $PL_CODE = "011901"; // ���˹�ҷ������մ
			elseif ($POSCODE=='11614') $PL_CODE = "011601"; // ���˹�ҷ���á��
			elseif ($POSCODE=='11701') $PL_CODE = "011701"; // ���˹�ҷ���ʴ�
			elseif ($POSCODE=='11712') $PL_CODE = "011712"; // ��Ҿ�ѡ�ҹ��ʴ�
			elseif ($POSCODE=='11723') $PL_CODE = "011723"; // �ѡ�Ԫҡ�þ�ʴ�
			elseif ($POSCODE=='11730') $PL_CODE = "011734"; // ���˹�ҷ������çҹ��ʴ�
			elseif ($POSCODE=='11601') $PL_CODE = "011601"; // ���˹�ҷ���á��
			elseif ($POSCODE=='11801') $PL_CODE = "011801"; // ���˹�ҷ��ѹ�֡������
			elseif ($POSCODE=='11901') $PL_CODE = "011901"; // ���˹�ҷ������մ
			elseif ($POSCODE=='12201') $PL_CODE = "012201"; // ���˹�ҷ��ʶԵ�
			elseif ($POSCODE=='12212') $PL_CODE = "012212"; // ��Ҿ�ѡ�ҹʶԵ�
			elseif ($POSCODE=='12223') $PL_CODE = "012223"; // �ѡʶԵ�
			elseif ($POSCODE=='12302') $PL_CODE = "012302"; // ���˹�ҷ���ǪʶԵ�
			elseif ($POSCODE=='12403') $PL_CODE = "012403"; // �Եԡ�
			elseif ($POSCODE=='13003') $PL_CODE = "013003"; // ���˹�ҷ����������ѹ��
			elseif ($POSCODE=='20401') $PL_CODE = "020401"; // ���˹�ҷ�����Թ��кѭ��
			elseif ($POSCODE=='20412') $PL_CODE = "020412"; // ��Ҿ�ѡ�ҹ����Թ��кѭ��
			elseif ($POSCODE=='20423') $PL_CODE = "020423"; // �ѡ�Ԫҡ���Թ��кѭ��
			elseif ($POSCODE=='20430') $PL_CODE = "020435"; // ���˹�ҷ������çҹ����Թ��кѭ��
			elseif ($POSCODE=='20603') $PL_CODE = "020603"; // ���˹�ҷ���Ǩ�ͺ����
			elseif ($POSCODE=='20801') $PL_CODE = "020801"; // ���˹�ҷ���Ǩ�ͺ�ѭ��
			elseif ($POSCODE=='20823') $PL_CODE = "020823"; // �ѡ�Ԫҡ�õ�Ǩ�ͺ�ѭ��
			elseif ($POSCODE=='30201') $PL_CODE = "030201"; // ���˹�ҷ�袹��
			elseif ($POSCODE=='31201') $PL_CODE = "031201"; // ���˹�ҷ���������
			elseif ($POSCODE=='31801') $PL_CODE = "031801"; // ���˹�ҷ���Ъ�����ѹ��
			elseif ($POSCODE=='31813') $PL_CODE = "031813"; // �ѡ��Ъ�����ѹ��
			elseif ($POSCODE=='31923') $PL_CODE = "031923"; // �ѡ��â���
			elseif ($POSCODE=='32401') $PL_CODE = "032401"; // ���˹�ҷ�������
			elseif ($POSCODE=='32423') $PL_CODE = "032423"; // �ѡ�Ԫҡ�������
			elseif ($POSCODE=='32501') $PL_CODE = "032501"; // ���˹�ҷ���ʵ��ȹ�֡��
			elseif ($POSCODE=='32512') $PL_CODE = "032512"; // ��Ҿ�ѡ�ҹ�ʵ��ȹ�֡��
			elseif ($POSCODE=='32523') $PL_CODE = "032523"; // �ѡ�Ԫҡ���ʵ��ȹ�֡��
			elseif ($POSCODE=='41412') $PL_CODE = "041412"; // ��Ҿ�ѡ�ҹ�ˡԨ�ɵ�
			elseif ($POSCODE=='50201') $PL_CODE = "050201"; // ���˹�ҷ���Է����ʵ��
			elseif ($POSCODE=='50212') $PL_CODE = "050212"; // ��Ҿ�ѡ�ҹ�Է����ʵ��
			elseif ($POSCODE=='60104') $PL_CODE = "060104"; // ���ᾷ��
			elseif ($POSCODE=='60204') $PL_CODE = "060204"; // �ѹ�ᾷ��
			elseif ($POSCODE=='60304') $PL_CODE = "060304"; // ����ѵ�ᾷ��
			elseif ($POSCODE=='60403') $PL_CODE = "060403"; // �ѡ�Է����ʵ����ᾷ��
			elseif ($POSCODE=='60503') $PL_CODE = "060503"; // �ѡ���Ѫ�Ԩ��
			elseif ($POSCODE=='60603') $PL_CODE = "060603"; // ���Ѫ��
			elseif ($POSCODE=='60802') $PL_CODE = "060802"; // ����ҡ�
			elseif ($POSCODE=='60813') $PL_CODE = "060813"; // �ѡ����ҡ��
			elseif ($POSCODE=='61203') $PL_CODE = "061203"; // �ѡ�Ե�Է��
			elseif ($POSCODE=='61303') $PL_CODE = "061303"; // �ѡ�Ԫҡ���آ�֡��
			elseif ($POSCODE=='61502') $PL_CODE = "061502"; // ��Һ��෤�Ԥ
			elseif ($POSCODE=='61510') $PL_CODE = "061514"; // �ѡ�Ԫҡ�þ�Һ��
			elseif ($POSCODE=='61523') $PL_CODE = "061523"; // ��Һ���ԪҪվ
			elseif ($POSCODE=='61601') $PL_CODE = "061601"; // ���˹�ҷ���Һ��
			elseif ($POSCODE=='61701') $PL_CODE = "061701"; // ���˹�ҷ����硫����
			elseif ($POSCODE=='61712') $PL_CODE = "061712"; // ���˹�ҷ���ѧ�ա��ᾷ��
			elseif ($POSCODE=='61723') $PL_CODE = "061723"; // �ѡ�ѧ�ա��ᾷ��
			elseif ($POSCODE=='61803') $PL_CODE = "061803"; // �ѡ����Ҿ�ӺѴ
			elseif ($POSCODE=='61902') $PL_CODE = "061902"; // ���˹�ҷ���Ҫ�ǺӺѴ
			elseif ($POSCODE=='61913') $PL_CODE = "061913"; // �ѡ�Ҫ�ǺӺѴ
			elseif ($POSCODE=='62002') $PL_CODE = "062002"; // �ѹ�ҹ����
			elseif ($POSCODE=='62201') $PL_CODE = "062201"; // ���˹�ҷ���Է����ʵ����ᾷ��
			elseif ($POSCODE=='62212') $PL_CODE = "062212"; // ��Ҿ�ѡ�ҹ�Է����ʵ����ᾷ��
			elseif ($POSCODE=='62301') $PL_CODE = "062301"; // ���������Ѫ��
			elseif ($POSCODE=='62312') $PL_CODE = "062312"; // ��Ҿ�ѡ�ҹ���Ѫ����
			elseif ($POSCODE=='62403') $PL_CODE = "062403"; // ���˹�ҷ���Ǫ�ѳ��
			elseif ($POSCODE=='62503') $PL_CODE = "062503"; // �ѡ�Ԫҡ���Ҹ�ó�آ
			elseif ($POSCODE=='62601') $PL_CODE = "062601"; // �����·ѹ�ᾷ��
			elseif ($POSCODE=='62701') $PL_CODE = "062701"; // ���˹�ҷ�����Ҿ�ӺѴ
			elseif ($POSCODE=='62712') $PL_CODE = "062712"; // ��Ҿ�ѡ�ҹ�Ǫ������鹿�
			elseif ($POSCODE=='63012') $PL_CODE = "063012"; // ��Ҿ�ѡ�ҹ��������آ�Ҿ
			elseif ($POSCODE=='63112') $PL_CODE = "063112"; // ��Ҿ�ѡ�ҹ�Ǻ����ä
			elseif ($POSCODE=='63023') $PL_CODE = "063023"; // �ѡ�Ԫҡ����������آ�Ҿ
			elseif ($POSCODE=='63123') $PL_CODE = "063123"; // �ѡ�Ԫҡ�äǺ����ä
			elseif ($POSCODE=='63201') $PL_CODE = "063201"; // ���˹�ҷ���آ��Ժ��
			elseif ($POSCODE=='63502') $PL_CODE = "063502"; // ��Ҿ�ѡ�ҹ�Ҹ�ó�آ�����
			elseif ($POSCODE=='63603') $PL_CODE = "063603"; // �ѡ෤�Ԥ���ᾷ��
			elseif ($POSCODE=='71401') $PL_CODE = "071401"; // ��ҧ�¸�
			elseif ($POSCODE=='71901') $PL_CODE = "071901"; // ��ҧ����ͧ��
			elseif ($POSCODE=='71912') $PL_CODE = "071912"; // ��ª�ҧ����ͧ��
			elseif ($POSCODE=='72001') $PL_CODE = "072001"; // ��ҧ����ͧ¹��
			elseif ($POSCODE=='72401') $PL_CODE = "072401"; // ��ҧ����
			elseif ($POSCODE=='73001') $PL_CODE = "073001"; // ��ҧ俿��
			elseif ($POSCODE=='73012') $PL_CODE = "073012"; // ��ª�ҧ俿��
			elseif ($POSCODE=='73101') $PL_CODE = "073101"; // ��ҧ俿���������
			elseif ($POSCODE=='73201') $PL_CODE = "073201"; // ��ҧ����礷�͹Ԥ��
			elseif ($POSCODE=='73212') $PL_CODE = "073212"; // ��ª�ҧ����礷�͹Ԥ��
			elseif ($POSCODE=='73501') $PL_CODE = "073501"; // ��ҧ෤�Ԥ
			elseif ($POSCODE=='73512') $PL_CODE = "073512"; // ��ª�ҧ෤�Ԥ
			elseif ($POSCODE=='74401') $PL_CODE = "074401"; // ��ҧ��Ż�
			elseif ($POSCODE=='74412') $PL_CODE = "074412"; // ��ª�ҧ��Ż�
			elseif ($POSCODE=='75003') $PL_CODE = "075003"; // ��ҧ�Ҿ���ᾷ��
			elseif ($POSCODE=='75601') $PL_CODE = "075602"; // ��ҧ����ػ�ó�
			elseif ($POSCODE=='75602') $PL_CODE = "010108"; // �ѡ����ػ�ó�
			elseif ($POSCODE=='75702') $PL_CODE = "075702"; // ��ҧ�ѹ�����
			elseif ($POSCODE=='80403') $PL_CODE = "080403"; // �Է�Ҩ����
			elseif ($POSCODE=='80603') $PL_CODE = "080603"; // ���˹�ҷ��֡ͺ��
			elseif ($POSCODE=='80513') $PL_CODE = "080513"; // �ѡ�Ԫҡ���֡�Ҿ����
			elseif ($POSCODE=='81303') $PL_CODE = "081303"; // ��ó��ѡ��
			elseif ($POSCODE=='81501') $PL_CODE = "081501"; // ���˹�ҷ����ͧ��ش
			elseif ($POSCODE=='82903') $PL_CODE = "082903"; // �ѡ�ѧ��ʧ������
			elseif ($POSCODE=='99070') $PL_CODE = "011013"; // �ѡ�Ԫҡ�ä���������
			elseif ($POSCODE=='99071') $PL_CODE = "062201"; // ���˹�ҷ���Է����ʵ����ᾷ��
			if ($POSCODE && !$PL_CODE) {
				$POS_CODE = '0' . $POSCODE;
				$cmd = " SELECT PL_NAME FROM PER_LINE WHERE PL_CODE = '$POS_CODE' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd<br>";
				$data = $db_dpis->get_array();
				$PL_NAME = trim($data[PL_NAME]);
				if (!$PL_NAME) echo "���˹����§ҹ $POSCODE<br>";
			}
			$PL_CODE = (trim($PL_CODE))? "'".trim($PL_CODE)."'" : "NULL";

			$MOV_CODE = "101";
			if ($FLOW=='00') $MOV_CODE = "10710"; 
			elseif ($FLOW=='11') $MOV_CODE = "10110"; 
			elseif ($FLOW=='12') $MOV_CODE = "10120"; 
			elseif ($FLOW=='13') $MOV_CODE = "10130"; 
			elseif ($FLOW=='14') $MOV_CODE = "10140"; 
			elseif ($FLOW=='21') $MOV_CODE = "10510"; 
			elseif ($FLOW=='22') $MOV_CODE = "10520"; 
			elseif ($FLOW=='31') $MOV_CODE = "10310"; 
			elseif ($FLOW=='32') $MOV_CODE = "10320"; 
			elseif ($FLOW=='33') $MOV_CODE = "10330"; 
			elseif ($FLOW=='34') $MOV_CODE = "10340"; 
			elseif ($FLOW=='40') $MOV_CODE = "10410"; 
			elseif ($FLOW=='41') $MOV_CODE = "10420"; 
			elseif ($FLOW=='42') $MOV_CODE = "10430"; 
			elseif ($FLOW=='43') $MOV_CODE = "10440"; 
			elseif ($FLOW=='44') $MOV_CODE = "10450"; 
			elseif ($FLOW=='45') $MOV_CODE = "12410"; 
			elseif ($FLOW=='51') $MOV_CODE = "11820"; 
			elseif ($FLOW=='52') $MOV_CODE = "10620"; 
			elseif ($FLOW=='61') $MOV_CODE = "11810"; 
			elseif ($FLOW=='95') $MOV_CODE = "10840"; 
			if ($FLOW && !$MOV_CODE) echo "��������͹��ǵ��˹� $FLOW<br>";
			$MOV_CODE = (trim($MOV_CODE))? "'".trim($MOV_CODE)."'" : "NULL";

			if (!$POH_POS_NO) $POH_POS_NO = "-";
			if (!$REFERENCE) $REFERENCE = "-";
			if (!$EFFECTIVE_DATE) $EFFECTIVE_DATE = "-";
			if (!$ORG_NAME) $ORG_NAME = "-";
			if (!$ORDER_PHRASE) $ORDER_PHRASE = "-";
			if (!$SALARY) $SALARY = 0;
			if (!$POH_DOCNO) $POH_DOCNO = "-";
				
			$cmd = " INSERT INTO PER_POSITIONHIS(POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_ENDDATE, POH_DOCNO, POH_DOCDATE, POH_POS_NO, 
							  PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, POH_ORGMGT, ORG_ID_1, ORG_ID_2, ORG_ID_3, 	
							  POH_UNDER_ORG1, POH_UNDER_ORG2, POH_SALARY, POH_SALARY_POS, POH_REMARK, UPDATE_USER, UPDATE_DATE, POH_ORG1, POH_ORG2, 
							  POH_ORG3, POH_ORG)
							  VALUES ($POH_ID, $EMPID, '$ENDATE', $MOV_CODE, '$EXDATE', '$POH_DOCNO', '$ENDATE', '$POH_POS_NO', $PM_CODE, 
							  '$LEVEL_NO', 
						  $PL_CODE, NULL, NULL, '140', NULL, NULL, 2, NULL, NULL, NULL, '$POH_UNDER_ORG1', '$POH_UNDER_ORG2', $SALARY, 0, '$ORDER_PHRASE', $UPDATE_USER, '$UPDATE_DATE', '$POH_ORG1', 
						  '$POH_ORG2', '$POH_ORG3', '$DEPT') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "<br>======================<br>";
//			echo "<br><b>$j.</b> $cmd<br>===================<br>";	
		} // end while						
	} // end if

	if( $command=='SALARYHIS' ){ // 194581 194581
		$cmd = " delete from per_salaryhis where update_user = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT MAX(SAH_ID) AS SAH_ID FROM PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$SAH_ID = $data[SAH_ID] + 0;

		$cmd = " SELECT EMPID, GROUP, SALCODE, SALDATE, SALARY FROM SALHIS ORDER BY EMPID, SALDATE, SALARY ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$SAH_ID++;
			$EMPID = $data[EMPID];
			$GROUP = trim($data[GROUP]);
			$SALCODE = trim($data[SALCODE]);
			$SALDATE = trim($data[SALDATE]);
			$SALARY = $data[SALARY] + 0;
			$MOV_CODE = "213";
			if ($SALCODE=='0') $MOV_CODE = "21370"; 
			elseif ($SALCODE=='00') $MOV_CODE = "21370"; 
			elseif ($SALCODE=='01') $MOV_CODE = "21310"; 
			elseif ($SALCODE=='1') $MOV_CODE = "21310"; 
			elseif ($SALCODE=='10') $MOV_CODE = "21390"; 
			elseif ($SALCODE=='11') $MOV_CODE = "21350"; 
			elseif ($SALCODE=='12') $MOV_CODE = "21610"; 
			elseif ($SALCODE=='13') $MOV_CODE = "218"; 
			elseif ($SALCODE=='14') $MOV_CODE = "219"; 
			elseif ($SALCODE=='2') $MOV_CODE = "21320"; 
			elseif ($SALCODE=='3') $MOV_CODE = "21330"; 
			elseif ($SALCODE=='4') $MOV_CODE = "21340"; 
			elseif ($SALCODE=='5') $MOV_CODE = "21410"; 
			elseif ($SALCODE=='6') $MOV_CODE = "21360"; 
			elseif ($SALCODE=='7') $MOV_CODE = "21620"; 
			elseif ($SALCODE=='8') $MOV_CODE = "21510"; 
			elseif ($SALCODE=='9') $MOV_CODE = "21520"; 
			elseif ($SALCODE=='99') $MOV_CODE = "213"; 
			if ($SALCODE && !$MOV_CODE) echo "��������͹����Թ��͹ $SALCODE<br>";
			$MOV_CODE = (trim($MOV_CODE))? "'".trim($MOV_CODE)."'" : "NULL";
			if (!$REFERENCE) $REFERENCE = "-";
				
			$cmd = " INSERT INTO PER_SALARYHIS(SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, SAH_DOCDATE, SAH_ENDDATE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ($SAH_ID, $EMPID, '$SALDATE', $MOV_CODE, $SALARY, '$REFERENCE', '$SALDATE', NULL, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "<br>======================<br>";
//			echo "<br><b>$j.</b> $cmd<br>===================<br>";	
		} // end while						
	} // end if

// create index idx_salaryhis on per_salaryhis (PER_ID,SAH_EFFECTIVEDATE,SAH_SALARY);
// create index idx_positionhis  on per_positionhis (PER_ID,POH_EFFECTIVEDATE);
	if( $command=="HISTORY" ){ // 450885 215716 16721
		$cmd = " delete from per_salaryhis where update_user = $UPDATE_USER ";
		//$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from per_positionhis where update_user = $UPDATE_USER ";
		//$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT MAX(SAH_ID) AS SAH_ID FROM PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$SAH_ID = $data[SAH_ID] + 0;

		$cmd = " SELECT MAX(POH_ID) AS POH_ID FROM PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$POH_ID = $data[POH_ID] + 0;

		$cmd = " SELECT EMPID, HDATE, DESCRIPTION, POSNUM, [LEVEL] as LEVELNO, SALARY, REFERENCE, JOBGROUPID, JOBCLASSID 
						  FROM HISTORY 
						  WHERE EMPID > 20000
						  ORDER BY EMPID, HDATE, SALARY ";
		$db_att->send_cmd($cmd);
		//$db_att->show_error();
		//echo "<br>";
//						  WHERE EMPID > 0 AND EMPID <= 5000
//						  WHERE EMPID > 5000 AND EMPID <= 10000
//						  WHERE EMPID > 10000 AND EMPID <= 15000
//						  WHERE EMPID > 15000 AND EMPID <= 20000
//						  WHERE EMPID > 20000
		while($data = $db_att->get_array()){
			$EMPID = $data[EMPID]+0;
			if ($EMPID == $TMP_EMPID) {
				$NEW_FLAG = 0;
			} else {
				$NEW_FLAG = 1;
				$TMP_EMPID = $EMPID;
			}
			$ES_CODE = "02";
			$HDATE = substr(trim($data[HDATE]),0,10);
			$DESCRIPTION = trim($data[DESCRIPTION]);
			$DESCRIPTION = str_replace("'", "", $DESCRIPTION);
			$DESCRIPTION = str_replace("  ", " ", $DESCRIPTION);
			$DESCRIPTION = str_replace("  ", " ", $DESCRIPTION);
			$DESCRIPTION = str_replace("  ", " ", $DESCRIPTION);
			$DESCRIPTION = str_replace("  ", " ", $DESCRIPTION);
			$POSNUM = $data[POSNUM]+0;
			$LEVEL_NO = trim($data[LEVELNO]);
			$LEVEL_NO = str_pad($LEVEL_NO, 2, "0", STR_PAD_LEFT);
			$SALARY = $data[SALARY] + 0;
			$DOCNO = trim($data[REFERENCE]);
			$DOCNO = str_replace("'", "", $DOCNO);
			$DOCNO = str_replace("'", "", $DOCNO);
			$DOCNO = str_replace("  ", " ", $DOCNO);
			$DOCNO = str_replace("  ", " ", $DOCNO);
			$DOCNO = str_replace("  ", " ", $DOCNO);
			$DOCNO = str_replace("  ", " ", $DOCNO);
			$DOCNO = str_replace("  ", " ", $DOCNO);
			$DOCNO = str_replace("  ", " ", $DOCNO);
			$REMARK2 = $DOCNO;
			$DOCDATE = $DOCNO_EDIT = $DOCDATE_EDIT = $POH_PL_NAME = $POH_ORG = "";
			if ($DOCNO=="�.1.5/ 21 /2544 - 72/54/4" || $DOCNO=="�.1.5/ - 0/0/0") {
				$DOCDATE = "";
			} elseif ($DOCNO=="��С�ȡ���ҧ��ǧ- 8 �.�.2551" || $DOCNO=="��á�ȡ���ҧ��ǧ - 8 �.�.2551" || $DOCNO=="��С�ȡ���ҧ��ǧ ŧ�ѹ��� 8 �.�.2551") {
				$DOCNO = "��С�ȡ���ҧ��ǧ";
				$DOCDATE = "8 �.�.2551";
			} elseif ($DOCNO=="�. 3.3/41/2545 - ŧ�ѹ��� 19 ��Ȩԡ�¹ 2545") {
				$DOCNO = "�. 3.3/41/2545";
				$DOCDATE = "19 ��Ȩԡ�¹ 2545";
			} elseif (strpos($DOCNO,"-��.") !== false) {
				$arr_temp = explode("-��.", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"- ��.") !== false) {
				$arr_temp = explode("- ��.", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"- ��") !== false) {
				$arr_temp = explode("- ��", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"-��") !== false) {
				$arr_temp = explode("-��", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"- ŧ�ѹ���") !== false) {
				$arr_temp = explode("- ŧ�ѹ���", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ŧ�ѹ�����.") !== false) {
				$arr_temp = explode("ŧ�ѹ�����.", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ŧ�ѹ����ѹ") !== false) {
				$arr_temp = explode("ŧ�ѹ����ѹ", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ŧ�ѹ�����") !== false) {
				$arr_temp = explode("ŧ�ѹ�����", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ŧ�ѹ��� �") !== false) {
				$arr_temp = explode("ŧ�ѹ��� �", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ŧ�ѹ���") !== false) {
				$arr_temp = explode("ŧ�ѹ���", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"-") !== false && strpos($DOCNO,"��.") === false) {
				$arr_temp = explode("-", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"��.�ѹ���") !== false) {
				$arr_temp = explode("��.�ѹ���", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"��.���") !== false) {
				$arr_temp = explode("��.���", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"��. ���") !== false) {
				$arr_temp = explode("��. ���", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"��.�") !== false) {
				$arr_temp = explode("��.�", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"��..") !== false) {
				$arr_temp = explode("��..", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"�ǣ�") !== false) {
				$arr_temp = explode("�ǣ�", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"��ˡ.") !== false) {
				$arr_temp = explode("��ˡ.", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"�ǡ.") !== false) {
				$arr_temp = explode("�ǡ.", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"���.") !== false) {
				$arr_temp = explode("���.", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"��.") !== false) {
				$arr_temp = explode("��.", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"��,") !== false) {
				$arr_temp = explode("��,", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"�� .") !== false) {
				$arr_temp = explode("�� .", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"�ǧ") !== false && strpos($DOCNO,"����ҧ��ǧ") === false) {
				$arr_temp = explode("�ǧ", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"��") !== false && strpos($DOCNO,"����ҧ��ǧ") === false) {
				$arr_temp = explode("��", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"� �ѹ��� �") !== false) {
				$arr_temp = explode("� �ѹ��� �", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"� �ѹ���") !== false) {
				$arr_temp = explode("� �ѹ���", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			}
			if ($DOCDATE) {
				$dd = $mm = $yy = "";
				if (strpos($DOCDATE,"/") !== false) {
					$arr_temp = explode("/", $DOCDATE);
					$dd = str_pad(trim($arr_temp[0]), 2, "0", STR_PAD_LEFT);
					$mm = str_pad(trim($arr_temp[1]), 2, "0", STR_PAD_LEFT);
					$yy = trim($arr_temp[2]);
					if (strlen($yy)==2) $yy = "25".$yy;
					if ($yy+0 < 543) {
						if ($mm < substr($GOVPOS_DATE, 2, 2))
							$yy = substr($GOVPOS_DATE, 4, 4) + 1;
						else
							$yy = substr($GOVPOS_DATE, 4, 4);
					}
					$yy = $yy - 543;
					$DOCDATE = $yy."-".$mm."-".$dd;
				} else	if ($DOCDATE=="13 ��.�.2506") {
					$DOCDATE = "1963-08-13";
				} else	if ($DOCDATE=="13 ���.�.2509") {
					$DOCDATE = "1966-06-13";
				} else	if ($DOCDATE=="23 �Զع�¹2509") {
					$DOCDATE = "1966-06-23";
				} else	if ($DOCDATE=="31��.09") {
					$DOCDATE = "1966-10-31";
				} else	if ($DOCDATE=="15��.10") {
					$DOCDATE = "1967-05-15";
				} else	if ($DOCDATE=="31�.�. 2510") {
					$DOCDATE = "1967-05-31";
				} else	if ($DOCDATE=="16���.10") {
					$DOCDATE = "1967-06-16";
				} else	if ($DOCDATE=="26 �Զع�¹2510") {
					$DOCDATE = "1967-06-26";
				} else	if ($DOCDATE=="5��.10") {
					$DOCDATE = "1967-07-05";
				} else	if ($DOCDATE=="18 �.��2510") {
					$DOCDATE = "1967-08-18";
				} else	if ($DOCDATE=="13���.11") {
					$DOCDATE = "1968-06-13";
				} else	if ($DOCDATE=="26��.11") {
					$DOCDATE = "1968-09-26";
				} else	if ($DOCDATE=="12��.12") {
					$DOCDATE = "1969-05-12";
				} else	if ($DOCDATE=="5 �.�.2512") {
					$DOCDATE = "1969-08-05";
				} else	if ($DOCDATE=="0 14 �.�.2512") {
					$DOCDATE = "1969-08-14";
				} else	if ($DOCDATE=="24�.�. 2512") {
					$DOCDATE = "1969-09-24";
				} else	if ($DOCDATE=="3 ��.�.2512") {
					$DOCDATE = "1969-10-03";
				} else	if ($DOCDATE=="27���.13") {
					$DOCDATE = "1970-04-27";
				} else	if ($DOCDATE=="25 ��.�.2513") {
					$DOCDATE = "1970-06-25";
				} else	if ($DOCDATE=="16�.�. 2513") {
					$DOCDATE = "1970-09-16";
				} else	if ($DOCDATE=="27�.�. 2513") {
					$DOCDATE = "1970-09-27";
				} else	if ($DOCDATE=="28�.�. 2513") {
					$DOCDATE = "1970-09-28";
				} else	if ($DOCDATE=="17 �.�.2513") {
					$DOCDATE = "1970-11-17";
				} else	if ($DOCDATE=="21 �.�.2514") {
					$DOCDATE = "1971-05-21";
				} else	if ($DOCDATE=="41��.�. 2514") {
					$DOCDATE = "1971-06-11";
				} else	if ($DOCDATE=="29���.14") {
					$DOCDATE = "1971-06-29";
				} else	if ($DOCDATE=="15 �.�.2514") {
					$DOCDATE = "1971-07-15";
				} else	if ($DOCDATE=="� 4 �.�.2514") {
					$DOCDATE = "1971-08-04";
				} else	if ($DOCDATE=="20��.14") {
					$DOCDATE = "1971-10-20";
				} else	if ($DOCDATE=="24��.�. 2515") {
					$DOCDATE = "1972-03-24";
				} else	if ($DOCDATE=="� 12 �.�.2515") {
					$DOCDATE = "1972-05-12";
				} else	if ($DOCDATE=="24 ��.�.2515") {
					$DOCDATE = "1972-06-24";
				} else	if ($DOCDATE=="30���.15") {
					$DOCDATE = "1972-06-30";
				} else	if ($DOCDATE=="29��.15(��Ѻ���3) ��.2517") {
					$DOCDATE = "1972-09-29";
				} else	if ($DOCDATE=="13 .�.2515" || $DOCDATE=="13 �. �. 2515") {
					$DOCDATE = "1972-12-13";
				} else	if ($DOCDATE=="25052516" || $DOCDATE=="25��.16") {
					$DOCDATE = "1973-05-16";
				} else	if ($DOCDATE=="21��.16)") {
					$DOCDATE = "1973-05-21";
				} else	if ($DOCDATE=="7 ���.�.2516)") {
					$DOCDATE = "1973-06-07";
				} else	if ($DOCDATE=="5-11-16") {
					$DOCDATE = "1973-11-05";
				} else	if ($DOCDATE=="22-12-16") {
					$DOCDATE = "1973-12-22";
				} else	if ($DOCDATE=="20 ��.�2517" || $DOCDATE=="20 ���.�.2517") {
					$DOCDATE = "1974-03-20";
				} else	if ($DOCDATE=="1 �. �. 2517" || $DOCDATE=="�. 1 �.�.2517" || $DOCDATE=="1 �.�.2517") {
					$DOCDATE = "1974-05-01";
				} else	if ($DOCDATE=="07052517" || $DOCDATE=="7��.17") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="17��.17") {
					$DOCDATE = "1974-05-17";
				} else	if ($DOCDATE=="20 ��.�.2517") {
					$DOCDATE = "1974-06-20";
				} else	if ($DOCDATE=="28 � �.2518") {
					$DOCDATE = "1975-02-28";
				} else	if ($DOCDATE=="5��.�.18") {
					$DOCDATE = "1975-03-05";
				} else	if ($DOCDATE=="21�դ.18") {
					$DOCDATE = "1975-03-21";
				} else	if ($DOCDATE=="14��.18") {
					$DOCDATE = "1975-05-14";
				} else	if ($DOCDATE=="21 ����.2518") {
					$DOCDATE = "1975-08-21";
				} else	if ($DOCDATE=="5��.18") {
					$DOCDATE = "1975-09-05";
				} else	if ($DOCDATE=="11��. 2518" || $DOCDATE=="11 ����.2518") {
					$DOCDATE = "1975-09-11";
				} else	if ($DOCDATE=="18��.18") {
					$DOCDATE = "1975-09-18";
				} else	if ($DOCDATE=="3 �.�.2518") {
					$DOCDATE = "1975-10-03";
				} else	if ($DOCDATE=="18��.18") {
					$DOCDATE = "1975-12-18";
				} else	if ($DOCDATE=="27��.19") {
					$DOCDATE = "1976-02-27";
				} else	if ($DOCDATE=="0 1 ��.�. 2519") {
					$DOCDATE = "1976-03-01";
				} else	if ($DOCDATE=="28 �.�.2519" || $DOCDATE=="2 8 ��.�.2519") {
					$DOCDATE = "1976-06-28";
				} else	if ($DOCDATE=="28�.�. 2519") {
					$DOCDATE = "1976-09-28";
				} else	if ($DOCDATE=="01102519") {
					$DOCDATE = "1976-10-01";
				} else	if ($DOCDATE=="5 ��2519" || $DOCDATE=="5 ��.�.2519" || $DOCDATE=="5 �..�.2519") {
					$DOCDATE = "1976-10-05";
				} else	if ($DOCDATE=="�.�. 2519") {
					$DOCDATE = "1976-11-00";
				} else	if ($DOCDATE=="4 �.�.2519") {
					$DOCDATE = "1976-11-04";
				} else	if ($DOCDATE=="13��.20") {
					$DOCDATE = "1977-01-13";
				} else	if ($DOCDATE=="2��.20") {
					$DOCDATE = "1977-02-02";
				} else	if ($DOCDATE=="13���.20") {
					$DOCDATE = "1977-06-13";
				} else	if ($DOCDATE=="14���.20") {
					$DOCDATE = "1977-06-14";
				} else	if ($DOCDATE=="23��.20") {
					$DOCDATE = "1977-09-23";
				} else	if ($DOCDATE=="11 �.�.2520") {
					$DOCDATE = "1977-10-11";
				} else	if ($DOCDATE=="28 �.�2520") {
					$DOCDATE = "1977-12-28";
				} else	if ($DOCDATE=="31 ��.�.2521") {
					$DOCDATE = "1978-01-31";
				} else	if ($DOCDATE==". 27 �.�.2521") {
					$DOCDATE = "1978-02-27";
				} else	if ($DOCDATE=="26 ��.�.2521") {
					$DOCDATE = "1978-06-26";
				} else	if ($DOCDATE=="1 ��.�.2521") {
					$DOCDATE = "1978-04-01";
				} else	if ($DOCDATE=="8 �.�.2521") {
					$DOCDATE = "1978-08-08";
				} else	if ($DOCDATE=="20�.�.21" || $DOCDATE=="20��.21") {
					$DOCDATE = "1978-09-20";
				} else	if ($DOCDATE=="11��.21") {
					$DOCDATE = "1978-10-11";
				} else	if ($DOCDATE=="31 31 �.�.2522") {
					$DOCDATE = "1979-01-31";
				} else	if ($DOCDATE=="5 ��.�2522") {
					$DOCDATE = "1979-03-05";
				} else	if ($DOCDATE=="23 ��. �. 2522") {
					$DOCDATE = "1979-03-23";
				} else	if ($DOCDATE=="29 ��.�.2522") {
					$DOCDATE = "1979-03-29";
				} else	if ($DOCDATE=="27���.22") {
					$DOCDATE = "1979-04-27";
				} else	if ($DOCDATE=="24 �.�2522") {
					$DOCDATE = "1979-09-24";
				} else	if ($DOCDATE=="10 �..�.2522") {
					$DOCDATE = "1979-10-10";
				} else	if ($DOCDATE=="20 �.�2522") {
					$DOCDATE = "1979-12-20";
				} else	if ($DOCDATE=="22 �.�2522") {
					$DOCDATE = "1979-12-22";
				} else	if ($DOCDATE=="24�.�.22") {
					$DOCDATE = "1979-12-24";
				} else	if ($DOCDATE=="16 16 �.�.2523") {
					$DOCDATE = "1980-01-16";
				} else	if ($DOCDATE=="29 17 ��.�.2523") {
					$DOCDATE = "1980-03-17";
				} else	if ($DOCDATE=="24 ��.�2523") {
					$DOCDATE = "1980-03-24";
				} else	if ($DOCDATE=="25 ��.�.2523") {
					$DOCDATE = "1980-06-25";
				} else	if ($DOCDATE=="� 19 �.�. 2523") {
					$DOCDATE = "1980-08-19";
				} else	if ($DOCDATE=="17��.23") {
					$DOCDATE = "1980-09-17";
				} else	if ($DOCDATE=="29 ���.2523" || $DOCDATE=="29 �.�2523" || $DOCDATE=="29 � .�.2523") {
					$DOCDATE = "1980-09-29";
				} else	if ($DOCDATE=="2��.23") {
					$DOCDATE = "1980-10-02";
				} else	if ($DOCDATE=="3��.23") {
					$DOCDATE = "1980-10-03";
				} else	if ($DOCDATE=="25 ��.�.2523") {
					$DOCDATE = "1980-11-25";
				} else	if ($DOCDATE=="30 �..�.2523") {
					$DOCDATE = "1980-12-30";
				} else	if ($DOCDATE=="8 �.�.2524" || $DOCDATE=="8 �. �. 2524") {
					$DOCDATE = "1981-01-08";
				} else	if ($DOCDATE=="9 � .�.2524") {
					$DOCDATE = "1981-02-09";
				} else	if ($DOCDATE=="18��.24") {
					$DOCDATE = "1981-02-18";
				} else	if ($DOCDATE=="15 �� .�.2524") {
					$DOCDATE = "1981-04-15";
				} else	if ($DOCDATE=="24 �.�.2524") {
					$DOCDATE = "1981-05-24";
				} else	if ($DOCDATE=="10�.�. 2524") {
					$DOCDATE = "1981-08-10";
				} else	if ($DOCDATE=="19 �.�2524") {
					$DOCDATE = "1981-08-19";
				} else	if ($DOCDATE=="3�ѹ��¹ 2524") {
					$DOCDATE = "1981-09-24";
				} else	if ($DOCDATE=="24 �.�2524") {
					$DOCDATE = "1981-09-24";
				} else	if ($DOCDATE=="�.�. 2524") {
					$DOCDATE = "1981-10-00";
				} else	if ($DOCDATE=="7 ��2524") {
					$DOCDATE = "1981-10-07";
				} else	if ($DOCDATE=="�.�. 2524") {
					$DOCDATE = "1981-11-00";
				} else	if ($DOCDATE=="22 �.�2.524") {
					$DOCDATE = "1981-12-22";
				} else	if ($DOCDATE=="27��.25") {
					$DOCDATE = "1982-01-27";
				} else	if ($DOCDATE=="28 .�.2525") {
					$DOCDATE = "1982-01-28";
				} else	if ($DOCDATE=="15��.25") {
					$DOCDATE = "1982-02-15";
				} else	if ($DOCDATE=="29 ���.�.2525") {
					$DOCDATE = "1982-03-29";
				} else	if ($DOCDATE=="11 � . �. 2525") {
					$DOCDATE = "1982-08-11";
				} else	if ($DOCDATE=="31 �.��2525") {
					$DOCDATE = "1982-08-31";
				} else	if ($DOCDATE=="29 �ѹ��¹2525" || $DOCDATE=="29 �.�2525") {
					$DOCDATE = "1982-09-29";
				} else	if ($DOCDATE=="29 �.�.2525") {
					$DOCDATE = "1982-10-29";
				} else	if ($DOCDATE=="12 �.�.2525") {
					$DOCDATE = "1982-11-12";
				} else	if ($DOCDATE=="2��.25") {
					$DOCDATE = "1982-12-02";
				} else	if ($DOCDATE=="29 ��֫2525") {
					$DOCDATE = "1982-12-29";
				} else	if ($DOCDATE=="7���.26") {
					$DOCDATE = "1983-06-07";
				} else	if ($DOCDATE=="15 ���.2526") {
					$DOCDATE = "1983-06-15";
				} else	if ($DOCDATE=="31 ��֫2526") {
					$DOCDATE = "1983-07-31";
				} else	if ($DOCDATE=="18�.�. 2526") {
					$DOCDATE = "1983-08-18";
				} else	if ($DOCDATE=="21��.26") {
					$DOCDATE = "1983-09-21";
				} else	if ($DOCDATE=="23�.�. 2526" || $DOCDATE=="23 �ѹ��¹2526") {
					$DOCDATE = "1983-09-23";
				} else	if ($DOCDATE=="10 �.�.2526") {
					$DOCDATE = "1983-10-10";
				} else	if ($DOCDATE=="14 �.�2526") {
					$DOCDATE = "1983-11-14";
				} else	if ($DOCDATE=="30��.26") {
					$DOCDATE = "1983-11-26";
				} else	if ($DOCDATE=="12 ���.2526") {
					$DOCDATE = "1983-12-12";
				} else	if ($DOCDATE=="20 � �.2526") {
					$DOCDATE = "1983-12-20";
				} else	if ($DOCDATE=="20 �.�.2527") {
					$DOCDATE = "1984-04-20";
				} else	if ($DOCDATE=="27 �.�.2527" || $DOCDATE=="� 27 ��.�.2527") {
					$DOCDATE = "1984-06-27";
				} else	if ($DOCDATE=="28 �. �. 2527") {
					$DOCDATE = "1984-08-28";
				} else	if ($DOCDATE=="1 0 �.�. 2527") {
					$DOCDATE = "1984-09-10";
				} else	if ($DOCDATE=="11��.27") {
					$DOCDATE = "1984-09-11";
				} else	if ($DOCDATE=="18��.27" || $DOCDATE=="18 �. �. 2527") {
					$DOCDATE = "1984-09-18";
				} else	if ($DOCDATE=="31��.27") {
					$DOCDATE = "1984-10-31";
				} else	if ($DOCDATE=="8 �.��.2527" || $DOCDATE=="8�.�. 27") {
					$DOCDATE = "1984-11-08";
				} else	if ($DOCDATE=="11 �.��2527") {
					$DOCDATE = "1984-12-11";
				} else	if ($DOCDATE=="20 �.�.2527") {
					$DOCDATE = "1984-12-20";
				} else	if ($DOCDATE=="25 ����2528") {
					$DOCDATE = "1985-01-25";
				} else	if ($DOCDATE=="15 ��. �. 2528") {
					$DOCDATE = "1985-03-15";
				} else	if ($DOCDATE=="5���.28") {
					$DOCDATE = "1985-04-05";
				} else	if ($DOCDATE=="26 ����¹2528") {
					$DOCDATE = "1985-04-26";
				} else	if ($DOCDATE=="7 � .�. 2528") {
					$DOCDATE = "1985-05-07";
				} else	if ($DOCDATE=="� 21 �.�.2528") {
					$DOCDATE = "1985-05-21";
				} else	if ($DOCDATE=="� 12 ��.�. 2528" || $DOCDATE=="12 �� .�. 2528") {
					$DOCDATE = "1985-06-12";
				} else	if ($DOCDATE=="� 14 �.�. 2528") {
					$DOCDATE = "1985-08-14";
				} else	if ($DOCDATE=="12��.28") {
					$DOCDATE = "1985-09-12";
				} else	if ($DOCDATE=="8 �.�.2528") {
					$DOCDATE = "1985-10-08";
				} else	if ($DOCDATE=="28 0.�.2528") {
					$DOCDATE = "1985-10-28";
				} else	if ($DOCDATE=="31��.28") {
					$DOCDATE = "1985-10-31";
				} else	if ($DOCDATE=="3 13 �.�.2528") {
					$DOCDATE = "1985-12-13";
				} else	if ($DOCDATE=="29 �.�.29") {
					$DOCDATE = "1986-08-29";
				} else	if ($DOCDATE=="23��.29") {
					$DOCDATE = "1986-09-23";
				} else	if ($DOCDATE=="28��29") {
					$DOCDATE = "1986-09-28";
				} else	if ($DOCDATE=="2 �.�.2529" || $DOCDATE=="2 � �.2529") {
					$DOCDATE = "1986-12-02";
				} else	if ($DOCDATE=="12 �� .�.2530") {
					$DOCDATE = "1987-03-12";
				} else	if ($DOCDATE=="30 23 ��.�.2530") {
					$DOCDATE = "1987-03-23";
				} else	if ($DOCDATE=="30 �� �.2530") {
					$DOCDATE = "1987-03-30";
				} else	if ($DOCDATE=="11 �Զع�¹.2530") {
					$DOCDATE = "1987-06-11";
				} else	if ($DOCDATE=="29 �.�.25300" || $DOCDATE=="29�.�. 2530" || $DOCDATE=="29 �.�.2530") {
					$DOCDATE = "1987-09-29";
				} else	if ($DOCDATE=="27�.�. 2530") {
					$DOCDATE = "1987-08-27";
				} else	if ($DOCDATE=="31�.�. 2530") {
					$DOCDATE = "1987-08-31";
				} else	if ($DOCDATE=="5 � .�. 2531") {
					$DOCDATE = "1988-02-05";
				} else	if ($DOCDATE=="9 �.�2531") {
					$DOCDATE = "1988-05-09";
				} else	if ($DOCDATE=="8���.31") {
					$DOCDATE = "1988-06-08";
				} else	if ($DOCDATE=="23 ��.�.2531") {
					$DOCDATE = "1988-06-23";
				} else	if ($DOCDATE=="27�á�Ҥ� 2531" || $DOCDATE=="27 �.�2531") {
					$DOCDATE = "1988-07-27";
				} else	if ($DOCDATE=="13 �ѹ��¹.2531") {
					$DOCDATE = "1988-09-13";
				} else	if ($DOCDATE=="28��.31" || $DOCDATE=="28�.�.31") {
					$DOCDATE = "1988-09-28";
				} else	if ($DOCDATE=="30 �ѹ��¹2531") {
					$DOCDATE = "1988-09-30";
				} else	if ($DOCDATE=="26��.31") {
					$DOCDATE = "1988-10-26";
				} else	if ($DOCDATE=="23 �.�.2532") {
					$DOCDATE = "1989-05-23";
				} else	if ($DOCDATE=="15 ʧ�.2532") {
					$DOCDATE = "1989-08-15";
				} else	if ($DOCDATE=="25 �. �. 2532") {
					$DOCDATE = "1989-08-25";
				} else	if ($DOCDATE=="31 �. �. 2532") {
					$DOCDATE = "1989-08-31";
				} else	if ($DOCDATE=="6 �.�2532") {
					$DOCDATE = "1989-09-06";
				} else	if ($DOCDATE=="12 � �.2532") {
					$DOCDATE = "1989-09-12";
				} else	if ($DOCDATE=="4��.32" || $DOCDATE=="4�.�.32") {
					$DOCDATE = "1989-10-04";
				} else	if ($DOCDATE=="24�.�. 2532") {
					$DOCDATE = "1989-10-24";
				} else	if ($DOCDATE=="11 ��.�.2533") {
					$DOCDATE = "1990-03-11";
				} else	if ($DOCDATE=="7 �.�.2533") {
					$DOCDATE = "1990-04-07";
				} else	if ($DOCDATE=="20 ���.�.2533") {
					$DOCDATE = "1990-04-20";
				} else	if ($DOCDATE=="2 �..�.2533") {
					$DOCDATE = "1990-05-02";
				} else	if ($DOCDATE=="10 �.�.33") {
					$DOCDATE = "1990-05-10";
				} else	if ($DOCDATE=="� 18 �.�. 2533") {
					$DOCDATE = "1990-05-18";
				} else	if ($DOCDATE=="27 ��.�.2533") {
					$DOCDATE = "1990-06-27";
				} else	if ($DOCDATE=="28��.�. 2533") {
					$DOCDATE = "1990-06-28";
				} else	if ($DOCDATE=="19 �á�Ҥ�2533") {
					$DOCDATE = "1990-07-19";
				} else	if ($DOCDATE=="31 ��.�.2533") {
					$DOCDATE = "1990-08-31";
				} else	if ($DOCDATE=="27�.�.2533") {
					$DOCDATE = "1990-09-27";
				} else	if ($DOCDATE=="28 �..�.2533") {
					$DOCDATE = "1990-09-28";
				} else	if ($DOCDATE=="16 �.��.2533") {
					$DOCDATE = "1990-11-16";
				} else	if ($DOCDATE=="24 �ѹ�Ҥ�2533") {
					$DOCDATE = "1990-12-27";
				} else	if ($DOCDATE=="27 �. �. 2533") {
					$DOCDATE = "1990-12-27";
				} else	if ($DOCDATE=="30 2.�. 2534") {
					$DOCDATE = "1991-01-30";
				} else	if ($DOCDATE=="10 �� �.2534") {
					$DOCDATE = "1991-06-10";
				} else	if ($DOCDATE=="14 ��.�2534") {
					$DOCDATE = "1991-06-14";
				} else	if ($DOCDATE=="16 �.�2534") {
					$DOCDATE = "1991-07-16";
				} else	if ($DOCDATE=="6 ��.�.2534" || $DOCDATE=="6 ��.�.2534") {
					$DOCDATE = "1991-08-06";
				} else	if ($DOCDATE=="1 2 �.�.2534") {
					$DOCDATE = "1991-09-12";
				} else	if ($DOCDATE=="20 ǥ��2534") {
					$DOCDATE = "1991-10-20";
				} else	if ($DOCDATE=="24 �.�.2534") {
					$DOCDATE = "1991-10-24";
				} else	if ($DOCDATE=="4 �. �. 2534") {
					$DOCDATE = "1991-12-04";
				} else	if ($DOCDATE=="17 �.�.2534") {
					$DOCDATE = "1991-12-17";
				} else	if ($DOCDATE=="8 �..�.2535") {
					$DOCDATE = "1992-01-08";
				} else	if ($DOCDATE=="22�.� 2535") {
					$DOCDATE = "1992-01-22";
				} else	if ($DOCDATE=="23 ��. �.2535") {
					$DOCDATE = "1992-03-23";
				} else	if ($DOCDATE=="20�.�. 2535" || $DOCDATE=="20 ȧ��2535") {
					$DOCDATE = "1992-08-20";
				} else	if ($DOCDATE=="31 �ԧ�Ҥ�2535") {
					$DOCDATE = "1992-08-31";
				} else	if ($DOCDATE=="14 �ѹ��¹2535") {
					$DOCDATE = "1992-09-14";
				} else	if ($DOCDATE=="18 �.�.35") {
					$DOCDATE = "1992-09-18";
				} else	if ($DOCDATE=="8 � . �. 2535") {
					$DOCDATE = "1992-10-08";
				} else	if ($DOCDATE=="10 ���.2535") {
					$DOCDATE = "1992-11-10";
				} else	if ($DOCDATE=="8 �.�2535" || $DOCDATE=="8�.�. 2535") {
					$DOCDATE = "1992-12-08";
				} else	if ($DOCDATE=="24 �.�.2535") {
					$DOCDATE = "1992-12-24";
				} else	if ($DOCDATE=="19 ,.�.2536") {
					$DOCDATE = "1993-01-19";
				} else	if ($DOCDATE=="29062536") {
					$DOCDATE = "1993-06-29";
				} else	if ($DOCDATE=="1 �.�.�. 2536") {
					$DOCDATE = "1993-07-21";
				} else	if ($DOCDATE=="20 �.�.36") {
					$DOCDATE = "1993-08-20";
				} else	if ($DOCDATE=="31�.�. 2536") {
					$DOCDATE = "1993-08-31";
				} else	if ($DOCDATE=="8 ��.�.2536") {
					$DOCDATE = "1993-09-08";
				} else	if ($DOCDATE=="30 �.�.2536") {
					$DOCDATE = "1993-09-30";
				} else	if ($DOCDATE=="�.�. 2536") {
					$DOCDATE = "1993-10-00";
				} else	if ($DOCDATE=="25 �.�.2536") {
					$DOCDATE = "1993-10-25";
				} else	if ($DOCDATE=="6 �.�.2536") {
					$DOCDATE = "1993-12-06";
				} else	if ($DOCDATE=="21�.�. 2536") {
					$DOCDATE = "1993-12-21";
				} else	if ($DOCDATE=="24 �. �. 2536") {
					$DOCDATE = "1993-12-24";
				} else	if ($DOCDATE=="14 ��2537") {
					$DOCDATE = "1994-01-14";
				} else	if ($DOCDATE=="22 �.. 2537") {
					$DOCDATE = "1994-01-22";
				} else	if ($DOCDATE=="15 ����Ҿѹ��2537") {
					$DOCDATE = "1994-02-15";
				} else	if ($DOCDATE=="11 ���.�.2537") {
					$DOCDATE = "1994-03-11";
				} else	if ($DOCDATE=="22 ��. �. 2537") {
					$DOCDATE = "1994-03-22";
				} else	if ($DOCDATE=="10 �.�.2537") {
					$DOCDATE = "1994-04-10";
				} else	if ($DOCDATE=="�.�. 2537") {
					$DOCDATE = "1994-07-00";
				} else	if ($DOCDATE=="22�.�. 37") {
					$DOCDATE = "1994-08-22";
				} else	if ($DOCDATE=="23 �ѹ��¹2537") {
					$DOCDATE = "1994-09-23";
				} else	if ($DOCDATE=="1 � .�.2537") {
					$DOCDATE = "1994-12-01";
				} else	if ($DOCDATE=="21 .�.�.2537") {
					$DOCDATE = "1994-12-21";
				} else	if ($DOCDATE=="10 �.�.2538") {
					$DOCDATE = "1995-02-10";
				} else	if ($DOCDATE=="21 ����Ҿѹ��2538") {
					$DOCDATE = "1995-02-21";
				} else	if ($DOCDATE=="10 �֤. 38") {
					$DOCDATE = "1995-03-10";
				} else	if ($DOCDATE=="23��.�.38") {
					$DOCDATE = "1995-03-23";
				} else	if ($DOCDATE=="30��.�. 2538") {
					$DOCDATE = "1995-03-30";
				} else	if ($DOCDATE=="1 �Զع�¹2538") {
					$DOCDATE = "1995-06-01";
				} else	if ($DOCDATE=="30 ��.�2538" || $DOCDATE=="� 30 ��.�. 2538" || $DOCDATE=="30 �ԧ�.2538" || $DOCDATE=="30 �.�.2538" || $DOCDATE=="30 �.�. 2538" || $DOCDATE=="30 ��.�.38") {
					$DOCDATE = "1995-06-30";
				} else	if ($DOCDATE=="4 �á�Ҥ�2538") {
					$DOCDATE = "1995-07-04";
				} else	if ($DOCDATE=="11�.�. 2538") {
					$DOCDATE = "1995-08-11";
				} else	if ($DOCDATE=="�ѹ��� 14 �.�.2538") {
					$DOCDATE = "1995-08-14";
				} else	if ($DOCDATE=="21 �ԧ�Ҥ�2538") {
					$DOCDATE = "1995-08-21";
				} else	if ($DOCDATE=="23�.�. 2538" || $DOCDATE=="23 �.��2538" || $DOCDATE=="23 ʧ��2538" || $DOCDATE=="2 3 �ԧ�Ҥ� 2538") {
					$DOCDATE = "1995-08-23";
				} else	if ($DOCDATE=="20 �ѹ��¹2538") {
					$DOCDATE = "1995-09-20";
				} else	if ($DOCDATE=="10 �.�.2538" || $DOCDATE=="10 �.�.2538") {
					$DOCDATE = "1995-10-10";
				} else	if ($DOCDATE=="17 �. �. 2538" || $DOCDATE=="17 �.�.2538" || $DOCDATE=="17�.�. 2538") {
					$DOCDATE = "1995-10-17";
				} else	if ($DOCDATE=="6 �. �. 2538") {
					$DOCDATE = "1995-11-06";
				} else	if ($DOCDATE=="21 �.�.2538") {
					$DOCDATE = "1995-11-21";
				} else	if ($DOCDATE=="21 �. �. 2538") {
					$DOCDATE = "1995-12-21";
				} else	if ($DOCDATE=="7 �֤.2539") {
					$DOCDATE = "1996-03-07";
				} else	if ($DOCDATE=="19 ��.�2539") {
					$DOCDATE = "1996-03-19";
				} else	if ($DOCDATE=="4 �.�.2539") {
					$DOCDATE = "1996-04-04";
				} else	if ($DOCDATE=="29 �. �.2539") {
					$DOCDATE = "1996-05-29";
				} else	if ($DOCDATE=="18 �á�Ҥ�2539") {
					$DOCDATE = "1996-07-18";
				} else	if ($DOCDATE=="22 � .�. 2539") {
					$DOCDATE = "1996-07-22";
				} else	if ($DOCDATE=="�.�. 2539") {
					$DOCDATE = "1996-08-00";
				} else	if ($DOCDATE=="8 �ԧ�Ҥ�2539") {
					$DOCDATE = "1996-08-08";
				} else	if ($DOCDATE=="15�.�. 2539") {
					$DOCDATE = "1996-08-15";
				} else	if ($DOCDATE=="16 �.��2539" || $DOCDATE=="16�.�. 2539") {
					$DOCDATE = "1996-08-16";
				} else	if ($DOCDATE=="23ʤ. 39") {
					$DOCDATE = "1996-08-23";
				} else	if ($DOCDATE=="17 �.�2539") {
					$DOCDATE = "1996-09-17";
				} else	if ($DOCDATE=="15 �.�.2539") {
					$DOCDATE = "1996-10-15";
				} else	if ($DOCDATE=="18 �.�.2539") {
					$DOCDATE = "1996-10-18";
				} else	if ($DOCDATE=="6 ��Ȩԡ�¹2539") {
					$DOCDATE = "1996-11-06";
				} else	if ($DOCDATE=="20�.�. 2539") {
					$DOCDATE = "1996-12-20";
				} else	if ($DOCDATE=="�.�. 2540") {
					$DOCDATE = "1997-01-00";
				} else	if ($DOCDATE=="4�.�. 2540") {
					$DOCDATE = "1997-02-04";
				} else	if ($DOCDATE=="28 ����Ҿѹ��.2540") {
					$DOCDATE = "1997-02-28";
				} else	if ($DOCDATE=="1 2 ��.�. 40") {
					$DOCDATE = "1997-03-12";
				} else	if ($DOCDATE=="��.� 40") {
					$DOCDATE = "1997-04-00";
				} else	if ($DOCDATE=="2 5 ��.�.2540") {
					$DOCDATE = "1997-04-25";
				} else	if ($DOCDATE=="4 �Զع�¹ 2540") {
					$DOCDATE = "1997-06-04";
				} else	if ($DOCDATE=="31 �á�Ҥ�2540") {
					$DOCDATE = "1997-07-31";
				} else	if ($DOCDATE=="14 �ԧ�Ҥ�.2540" || $DOCDATE=="14 �ԧ�Ҥ�2540") {
					$DOCDATE = "1997-08-14";
				} else	if ($DOCDATE=="23 �ѹ��¹2540") {
					$DOCDATE = "1997-09-23";
				} else	if ($DOCDATE=="25�.�. 2540") {
					$DOCDATE = "1997-09-25";
				} else	if ($DOCDATE=="3 �.�.2540") {
					$DOCDATE = "1997-10-03";
				} else	if ($DOCDATE=="8 ���Ҥ��2540") {
					$DOCDATE = "1997-10-08";
				} else	if ($DOCDATE=="14 �.�.2540") {
					$DOCDATE = "1997-10-14";
				} else	if ($DOCDATE=="28 ���Ҥ�2540") {
					$DOCDATE = "1997-10-28";
				} else	if ($DOCDATE=="31 ���Ҥ�2540") {
					$DOCDATE = "1997-10-31";
				} else	if ($DOCDATE=="11 �ѹ�Ҥ�2540") {
					$DOCDATE = "1997-12-11";
				} else	if ($DOCDATE=="25�.�. 2541" || $DOCDATE=="25 �3�.2541") {
					$DOCDATE = "1998-02-25";
				} else	if ($DOCDATE=="26��.�.2541" || $DOCDATE=="26��.�.41") {
					$DOCDATE = "1998-03-26";
				} else	if ($DOCDATE=="�.�. 2541") {
					$DOCDATE = "1998-09-00";
				} else	if ($DOCDATE=="2 �ѹ��¹2541") {
					$DOCDATE = "1998-09-02";
				} else	if ($DOCDATE=="1 6 �.�. 2541") {
					$DOCDATE = "1998-09-16";
				} else	if ($DOCDATE=="30 �ѹ��¹2541") {
					$DOCDATE = "1998-09-30";
				} else	if ($DOCDATE=="6�.�.41") {
					$DOCDATE = "1998-10-06";
				} else	if ($DOCDATE=="2 ��Ȩԡ�¹2541") {
					$DOCDATE = "1998-11-02";
				} else	if ($DOCDATE=="9�ѹ�Ҥ� 2541") {
					$DOCDATE = "1998-12-09";
				} else	if ($DOCDATE=="6�.�. 2542") {
					$DOCDATE = "1999-01-06";
				} else	if ($DOCDATE=="27 6��.�.42") {
					$DOCDATE = "1999-04-06";
				} else	if ($DOCDATE=="17 �. �. 2542") {
					$DOCDATE = "1999-04-17";
				} else	if ($DOCDATE=="11 ����Ҥ�2542" || $DOCDATE=="11����Ҥ� 2542") {
					$DOCDATE = "1999-05-11";
				} else	if ($DOCDATE=="12�á�Ҥ� 2542") {
					$DOCDATE = "1999-07-12";
				} else	if ($DOCDATE=="18�.�. 2542") {
					$DOCDATE = "1999-09-18";
				} else	if ($DOCDATE=="21�.�. 2542") {
					$DOCDATE = "1999-12-21";
				} else	if ($DOCDATE=="22 �.�.2543") {
					$DOCDATE = "2000-06-22";
				} else	if ($DOCDATE=="27�.�.2543") {
					$DOCDATE = "2000-07-27";
				} else	if ($DOCDATE=="13 �ѹ��¹2543") {
					$DOCDATE = "2000-09-13";
				} else	if ($DOCDATE=="27�.�. 2543" || $DOCDATE=="27�.�.2543") {
					$DOCDATE = "2000-10-27";
				} else	if ($DOCDATE=="11 ��44") {
					$DOCDATE = "2001-05-11";
				} else	if ($DOCDATE=="11���. 44") {
					$DOCDATE = "2001-06-11";
				} else	if ($DOCDATE=="�ѹ��¹ 2544") {
					$DOCDATE = "2001-09-00";
				} else	if ($DOCDATE=="20�.�.2544" || $DOCDATE=="20�ѹ��¹ 2544") {
					$DOCDATE = "2001-09-20";
				} else	if ($DOCDATE=="7�.�.2544") {
					$DOCDATE = "2001-11-07";
				} else	if ($DOCDATE=="�չҤ� 2545") {
					$DOCDATE = "2002-03-00";
				} else	if ($DOCDATE=="22�.�. 2545" || $DOCDATE=="22�.�.2545") {
					$DOCDATE = "2002-05-22";
				} else	if ($DOCDATE=="�Զع�¹ 2545") {
					$DOCDATE = "2002-06-00";
				} else	if ($DOCDATE=="8 �Զع�¹2545") {
					$DOCDATE = "2002-06-08";
				} else	if ($DOCDATE=="13 �Զع�¹2545") {
					$DOCDATE = "2002-06-13";
				} else	if ($DOCDATE=="27 �Զع�¹2545") {
					$DOCDATE = "2002-06-27";
				} else	if ($DOCDATE=="23 �.�.45") {
					$DOCDATE = "2002-09-23";
				} else	if ($DOCDATE=="1�.�. 2545") {
					$DOCDATE = "2002-11-01";
				} else	if ($DOCDATE=="4�ѹ�Ҥ� 2545") {
					$DOCDATE = "2002-12-04";
				} else	if ($DOCDATE=="28�.�. 2546") {
					$DOCDATE = "2003-01-28";
				} else	if ($DOCDATE=="7�.�.46") {
					$DOCDATE = "2003-02-07";
				} else	if ($DOCDATE=="�չҤ� 2546") {
					$DOCDATE = "2003-03-00";
				} else	if ($DOCDATE=="����Ҥ� 2546") {
					$DOCDATE = "2003-05-00";
				} else	if ($DOCDATE=="�Զع�¹ 2546") {
					$DOCDATE = "2003-06-00";
				} else	if ($DOCDATE=="17�.�.46") {
					$DOCDATE = "2003-07-17";
				} else	if ($DOCDATE=="�ԧ�Ҥ� 2546") {
					$DOCDATE = "2003-08-00";
				} else	if ($DOCDATE=="11�ԧ�Ҥ� 2546") {
					$DOCDATE = "2003-08-11";
				} else	if ($DOCDATE=="16 ���Ҥ�2546") {
					$DOCDATE = "2003-10-16";
				} else	if ($DOCDATE=="6�.�.47") {
					$DOCDATE = "2004-02-06";
				} else	if ($DOCDATE=="9��.�.47") {
					$DOCDATE = "2004-03-09";
				} else	if ($DOCDATE=="����Ҥ� 2547") {
					$DOCDATE = "2004-05-00";
				} else	if ($DOCDATE=="28  �á�Ҥ�2547") {
					$DOCDATE = "2004-07-28";
				} else	if ($DOCDATE=="3�.�.2547") {
					$DOCDATE = "2004-09-03";
				} else	if ($DOCDATE=="��Ȩԡ�¹ 2547" || $DOCDATE=="��Ȩ��¹ 2547") {
					$DOCDATE = "2004-11-00";
				} else	if ($DOCDATE=="11�.�.2548") {
					$DOCDATE = "2005-01-11";
				} else	if ($DOCDATE=="17�.�.2548") {
					$DOCDATE = "2005-01-17";
				} else	if ($DOCDATE=="21�.� 2548") {
					$DOCDATE = "2005-01-21";
				} else	if ($DOCDATE=="26 ���Ҥ�2548") {
					$DOCDATE = "2005-01-21";
				} else	if ($DOCDATE=="28�չҤ� 2548") {
					$DOCDATE = "2005-03-28";
				} else	if ($DOCDATE=="1�Զع�¹ 2548") {
					$DOCDATE = "2005-06-01";
				} else	if ($DOCDATE=="7 �Զع�¹2548") {
					$DOCDATE = "2005-06-07";
				} else	if ($DOCDATE=="4 �á�Ҥ�2548") {
					$DOCDATE = "2005-07-04";
				} else	if ($DOCDATE=="� 2 �.�.2548") {
					$DOCDATE = "2005-09-02";
				} else	if ($DOCDATE=="15 �.�.2548") {
					$DOCDATE = "2005-09-15";
				} else	if ($DOCDATE=="���Ҥ� 2548") {
					$DOCDATE = "2005-10-00";
				} else	if ($DOCDATE=="13�.�.2548") {
					$DOCDATE = "2005-10-13";
				} else	if ($DOCDATE=="�.�. 2548") {
					$DOCDATE = "2005-12-00";
				} else	if ($DOCDATE=="21�.�.2548") {
					$DOCDATE = "2005-12-21";
				} else	if ($DOCDATE=="23�.�.49") {
					$DOCDATE = "2006-01-23";
				} else	if ($DOCDATE=="�չҤ� 2549") {
					$DOCDATE = "2006-03-00";
				} else	if ($DOCDATE=="18�.�.2549") {
					$DOCDATE = "2006-05-18";
				} else	if ($DOCDATE=="25�.�.2549") {
					$DOCDATE = "2006-05-25";
				} else	if ($DOCDATE=="�á�Ҥ� 2549") {
					$DOCDATE = "2006-07-00";
				} else	if ($DOCDATE=="�ԧ�Ҥ� 2549") {
					$DOCDATE = "2006-08-00";
				} else	if ($DOCDATE=="8�.�.2549") {
					$DOCDATE = "2006-08-08";
				} else	if ($DOCDATE=="14�.�.2549") {
					$DOCDATE = "2006-09-14";
				} else	if ($DOCDATE=="24�.�.2549") {
					$DOCDATE = "2006-10-24";
				} else	if ($DOCDATE=="6 ��� bbbb") {
					$DOCDATE = "2007-00-06";
				} else	if ($DOCDATE=="16�չҤ� 2550") {
					$DOCDATE = "2007-03-16";
				} else	if ($DOCDATE=="22��.�.2550") {
					$DOCDATE = "2007-03-22";
				} else	if ($DOCDATE=="18��.�.2550") {
					$DOCDATE = "2007-04-18";
				} else	if ($DOCDATE=="����Ҥ� 2550") {
					$DOCDATE = "2007-05-00";
				} else	if ($DOCDATE=="17����Ҥ� 2550") {
					$DOCDATE = "2007-05-17";
				} else	if ($DOCDATE=="30 ����Ҥ�2550") {
					$DOCDATE = "2007-05-30";
				} else	if ($DOCDATE=="7�.�.2550") {
					$DOCDATE = "2007-08-07";
				} else	if ($DOCDATE=="22�.�.2550") {
					$DOCDATE = "2007-08-22";
				} else	if ($DOCDATE=="2 ��� bbbb") {
					$DOCDATE = "2007-11-02";
				} else	if ($DOCDATE=="6 �ѹ�Ҥ�2550" || $DOCDATE=="6�.�.2550") {
					$DOCDATE = "2007-12-06";
				} else	if ($DOCDATE=="17���Ҥ� 2551") {
					$DOCDATE = "2008-01-17";
				} else	if ($DOCDATE=="1�.�. 2551") {
					$DOCDATE = "2008-02-01";
				} else	if ($DOCDATE=="11����Ҿѹ�� 2551") {
					$DOCDATE = "2008-02-11";
				} else	if ($DOCDATE=="25��.�.51") {
					$DOCDATE = "2008-03-25";
				} else	if ($DOCDATE=="4 ����¹2551") {
					$DOCDATE = "2008-04-04";
				} else	if ($DOCDATE=="12�.�2551") {
					$DOCDATE = "2008-05-12";
				} else	if ($DOCDATE=="23 ����Ҥ�2551") {
					$DOCDATE = "2008-05-23";
				} else	if ($DOCDATE=="29 ����Ҥ�551") {
					$DOCDATE = "2008-05-29";
				} else	if ($DOCDATE=="9��.�.51") {
					$DOCDATE = "2008-06-09";
				} else	if ($DOCDATE=="7�á�Ҥ� 2551") {
					$DOCDATE = "2008-07-07";
				} else	if ($DOCDATE=="14 �á�Ҥ�2551") {
					$DOCDATE = "2008-07-14";
				} else	if ($DOCDATE=="21�á�Ҥ� 2551") {
					$DOCDATE = "2008-07-21";
				} else	if ($DOCDATE=="22�áҤ� 2551") {
					$DOCDATE = "2008-07-22";
				} else	if ($DOCDATE=="23 �á�Ҥ�2551") {
					$DOCDATE = "2008-07-23";
				} else	if ($DOCDATE=="25�á�Ҥ� 2551" || $DOCDATE=="25�áҤ� 2551") {
					$DOCDATE = "2008-07-25";
				} else	if ($DOCDATE=="6 �ԧ�Ҥ�2551") {
					$DOCDATE = "2008-08-06";
				} else	if ($DOCDATE=="�ѹ��¹ 2551") {
					$DOCDATE = "2008-09-00";
				} else	if ($DOCDATE=="17�ѹ��¹ 2551") {
					$DOCDATE = "2008-09-17";
				} else	if ($DOCDATE=="25�ѹ��¹ 2551") {
					$DOCDATE = "2008-09-25";
				} else	if ($DOCDATE=="���Ҥ� 2551") {
					$DOCDATE = "2008-10-00";
				} else	if ($DOCDATE=="7�.�. 2551") {
					$DOCDATE = "2008-10-07";
				} else	if ($DOCDATE=="22���Ҥ� 2551") {
					$DOCDATE = "2008-10-22";
				} else	if ($DOCDATE=="28 ��Ȩԡ�¹2551") {
					$DOCDATE = "2008-11-28";
				} else	if ($DOCDATE=="1-12-51") {
					$DOCDATE = "2008-12-01";
				} else	if ($DOCDATE=="8�.�.51") {
					$DOCDATE = "2008-12-08";
				} else	if ($DOCDATE=="23�ѹ�Ҥ� 2551") {
					$DOCDATE = "2008-12-23";
				} else	if ($DOCDATE=="30�.�.51" || $DOCDATE=="30�.�.2551") {
					$DOCDATE = "2008-12-30";
				} else	if ($DOCDATE=="21�.�.2552") {
					$DOCDATE = "2009-01-21";
				} else	if ($DOCDATE=="27�.�. 52") {
					$DOCDATE = "2009-02-27";
				} else	if ($DOCDATE=="29����¹ 2552") {
					$DOCDATE = "2009-04-29";
				} else	if ($DOCDATE=="15����Ҥ� 2552") {
					$DOCDATE = "2009-05-15";
				} else	if ($DOCDATE=="25�.�.2552") {
					$DOCDATE = "2009-05-25";
				} else	if ($DOCDATE=="�ѹ��¹ 2552") {
					$DOCDATE = "2009-09-00";
				} else	if ($DOCDATE=="11�.�. 52") {
					$DOCDATE = "2009-09-11";
				} else	if ($DOCDATE=="30�.�.2552") {
					$DOCDATE = "2009-09-30";
				} else	if ($DOCDATE=="8�.�.2552") {
					$DOCDATE = "2009-10-08";
				} else	if ($DOCDATE=="9�.�.2552") {
					$DOCDATE = "2009-10-09";
				} else	if ($DOCDATE=="26�.�.2552" || $DOCDATE=="26���Ҥ� 2552") {
					$DOCDATE = "2009-10-26";
				} else	if ($DOCDATE=="29�.�.2552") {
					$DOCDATE = "2009-10-29";
				} else	if ($DOCDATE=="3�.�.2552") {
					$DOCDATE = "2009-11-03";
				} else	if ($DOCDATE=="4�.�.2552") {
					$DOCDATE = "2009-11-04";
				} else	if ($DOCDATE=="5�.�.2552") {
					$DOCDATE = "2009-11-05";
				} else	if ($DOCDATE=="6�.�.2552") {
					$DOCDATE = "2009-11-06";
				} else	if ($DOCDATE=="9�.�.2552") {
					$DOCDATE = "2009-11-09";
				} else	if ($DOCDATE=="11�.�.2552") {
					$DOCDATE = "2009-11-11";
				} else	if ($DOCDATE=="12�.�.2552") {
					$DOCDATE = "2009-11-12";
				} else	if ($DOCDATE=="8 �.�25.53") {
					$DOCDATE = "2010-02-08";
				} else	if ($DOCDATE=="03032553") {
					$DOCDATE = "2010-03-03";
				} else	if ($DOCDATE=="7�.�.2553") {
					$DOCDATE = "2010-07-07";
				} else	if ($DOCDATE=="23�á�Ҥ� 2553") {
					$DOCDATE = "2010-07-23";
				} else	if ($DOCDATE=="10 .ʤ.53") {
					$DOCDATE = "2010-08-10";
				} else	if ($DOCDATE=="16�ԧ�Ҥ� 2553") {
					$DOCDATE = "2010-08-16";
				} else	if ($DOCDATE=="27�ѹ��¹ 2553") {
					$DOCDATE = "2010-09-27";
				} else	if ($DOCDATE=="20���Ҥ� 2553" || $DOCDATE=="20 ��53") {
					$DOCDATE = "2010-10-20";
				} else	if ($DOCDATE=="26 ��� bbbb") {
					$DOCDATE = "2010-11-26";
				} else	if ($DOCDATE=="29�.�.2553") {
					$DOCDATE = "2010-11-29";
				} else	if ($DOCDATE=="01012554") {
					$DOCDATE = "2011-01-01";
				} else	if ($DOCDATE=="17����Ҿѹ�� 2554") {
					$DOCDATE = "2011-02-17";
				} else	if ($DOCDATE=="28�.�.2554") {
					$DOCDATE = "2011-02-28";
				} else	if ($DOCDATE=="29 ��.�.2554" || $DOCDATE=="2 9 ��.�.2554" || $DOCDATE=="29 ����¹2554") {
					$DOCDATE = "2011-04-29";
				} else	if ($DOCDATE=="21��.�.54") {
					$DOCDATE = "2011-06-21";
				} else	if ($DOCDATE=="�á�Ҥ� 2554") {
					$DOCDATE = "2011-07-00";
				} else	if ($DOCDATE=="22 �á�Ҥ�2554") {
					$DOCDATE = "2011-07-22";
				} else	if ($DOCDATE=="6�.�.54") {
					$DOCDATE = "2011-09-06";
				} else	if ($DOCDATE=="9 �ѹ��¹2554") {
					$DOCDATE = "2011-09-09";
				} else	if ($DOCDATE=="29 �ѹ��¹.2554") {
					$DOCDATE = "2011-09-29";
				} else	if ($DOCDATE=="30 ��Ȩԡ�¹2554") {
					$DOCDATE = "2011-11-30";
				} else	if ($DOCDATE=="30.��. 2554") {
					$DOCDATE = "2011-12-30";
				} else	if ($DOCDATE=="28 ��� bbbb") {
					$DOCDATE = "2011-12-28";
				} else	if ($DOCDATE=="8 ��� bbbb") {
					$DOCDATE = "2008-07-08";
				} else	if ($DOCDATE=="12�.�.2555") {
					$DOCDATE = "2012-01-12";
				} else	if ($DOCDATE=="����Ҿѹ�� 2555") {
					$DOCDATE = "2012-02-00";
				} else	if ($DOCDATE=="9����Ҿѹ�� 2555") {
					$DOCDATE = "2012-02-09";
				} else	if ($DOCDATE=="17 ����Ҿѹ� �2555") {
					$DOCDATE = "2012-02-17";
				} else	if ($DOCDATE=="4�.�.55") {
					$DOCDATE = "2012-05-04";
				} else	if ($DOCDATE=="12 ���Ҥ�2555") {
					$DOCDATE = "2012-10-12";
				} else	if ($DOCDATE=="138����Ҿѹ�� 2556" || $DOCDATE=="18 ����Ҿѹ��2556") {
					$DOCDATE = "2013-02-18";
				} else	if ($DOCDATE=="8 �չҤ�2556") {
					$DOCDATE = "2013-03-08";
				} else	if ($DOCDATE=="20 ����Ҥ�.2556") {
					$DOCDATE = "2013-05-20";
				} else	if ($DOCDATE=="�Զع�¹ 2556") {
					$DOCDATE = "2013-06-00";
				} else	if ($DOCDATE=="25 ��Ȩԡ�¹2556") {
					$DOCDATE = "2013-11-25";
				} else	if ($DOCDATE=="17 �ѹ�Ҥ�2556") {
					$DOCDATE = "2013-12-17";
				} else	if ($DOCDATE=="���Ҥ� 2557") {
					$DOCDATE = "2014-01-00";
				} else	if ($DOCDATE=="�չҤ� 2557") {
					$DOCDATE = "2014-03-00";
				} else	if ($DOCDATE=="22 �ѹ��¹2557") {
					$DOCDATE = "2014-09-22";
				} else	if ($DOCDATE=="�ѹ�Ҥ� 2557") {
					$DOCDATE = "2014-12-00";
				} else	if ($DOCDATE=="24����¹ 2558") {
					$DOCDATE = "2015-04-24";
				} else	if ($DOCDATE=="24 �Զع�¹.2558") {
					$DOCDATE = "2015-06-24";
				} else	if ($DOCDATE=="29 ��� bbbb") {
					$DOCDATE = "2015-00-00";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if (strpos($DOCDATE," ") !== false) {
					$arr_temp = explode(" ", $DOCDATE);
					$dd = str_pad(trim($arr_temp[0]), 2, "0", STR_PAD_LEFT);
					if (trim($arr_temp[1])=="���Ҥ�" || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="�.�" || trim($arr_temp[1])=="�.��" || trim($arr_temp[1])=="�,�," || trim($arr_temp[1])=="����Ҥ�" || trim($arr_temp[1])=="���Ҥ�." || trim($arr_temp[1])=="��Ҥ�" || trim($arr_temp[1])=="��ä�" || trim($arr_temp[1])=="���ҵ�" || trim($arr_temp[1])=="���Ҥ�") $mm = "01";
					elseif (trim($arr_temp[1])=="����Ҿѹ��" || trim($arr_temp[1])=="������ѹ��" || trim($arr_temp[1])=="�����Ҿѹ��" || trim($arr_temp[1])=="����Ҿѹ���" || trim($arr_temp[1])=="�.�." || 
						trim($arr_temp[1])=="��." || trim($arr_temp[1])=="��" || trim($arr_temp[1])=="�.�" || trim($arr_temp[1])=="�����þѹ��" || trim($arr_temp[1])=="�����Ҿѹ��" || trim($arr_temp[1])=="����Ҿѹ��" || trim($arr_temp[1])=="����ҹ��" || trim($arr_temp[1])=="����Ҿѹ�" || trim($arr_temp[1])=="����Ҿѹ�" || trim($arr_temp[1])=="���-�Ҿѹ��" || trim($arr_temp[1])=="����Ҿ���" || trim($arr_temp[1])=="���Ҿѹ��" || trim($arr_temp[1])=="�ط�Ҿѹ��" || trim($arr_temp[1])=="�����ѹ��" || trim($arr_temp[1])=="����Ҿѹ��") $mm = "02";
					elseif (trim($arr_temp[1])=="�չҤ�" || trim($arr_temp[1])=="��.�." || trim($arr_temp[1])=="��.��" || trim($arr_temp[1])=="��.�" || trim($arr_temp[1])=="��.�." || trim($arr_temp[1])=="��.�.") $mm = "03";
					elseif (trim($arr_temp[1])=="����¹" || trim($arr_temp[1])=="��.�." || trim($arr_temp[1])=="��.�" || trim($arr_temp[1])=="��.§" || trim($arr_temp[1])=="��.�." || trim($arr_temp[1])=="���¹" || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="����¹�" || trim($arr_temp[1])=="����¹" || trim($arr_temp[1])=="���ҹ" || trim($arr_temp[1])=="����¹." || trim($arr_temp[1])=="���¹") $mm = "04";
					elseif (trim($arr_temp[1])=="����Ҥ�" || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="��." || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="�.�" || trim($arr_temp[1])=="����Ҥ�" || trim($arr_temp[1])=="�������" || trim($arr_temp[1])=="����Ҥ�." || trim($arr_temp[1])=="����Ҥ�" || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="����Ҥ�" || trim($arr_temp[1])=="���Ҥ�") $mm = "05";
					elseif (trim($arr_temp[1])=="�Զع�¹" || trim($arr_temp[1])=="��.�." || trim($arr_temp[1])=="��.§" || trim($arr_temp[1])=="��.�" || trim($arr_temp[1])=="��.�." || trim($arr_temp[1])=="�Զ��¹" || trim($arr_temp[1])=="�ԧ�." || trim($arr_temp[1])=="��.�." || trim($arr_temp[1])=="���ع�¹" || trim($arr_temp[1])=="�Զع�¹." || trim($arr_temp[1])=="�Զ��ʺ�") $mm = "06";
					elseif (trim($arr_temp[1])=="�á�Ҥ�" || trim($arr_temp[1])=="�á�Ҥ�" || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="�.��" || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="��." || trim($arr_temp[1])=="�áҤ�" || trim($arr_temp[1])=="��á�Ҥ�" || trim($arr_temp[1])=="�á���" || trim($arr_temp[1])=="�ïҤ�" || trim($arr_temp[1])=="�á��ʤ�" || trim($arr_temp[1])=="�.�" || trim($arr_temp[1])=="�.��" || trim($arr_temp[1])=="�îҤ�" || trim($arr_temp[1])=="�áӤ�" || trim($arr_temp[1])=="�á�Ҥ�." || trim($arr_temp[1])=="�á����" || trim($arr_temp[1])=="����Ҥ�" || trim($arr_temp[1])=="�..�.") $mm = "07";
					elseif (trim($arr_temp[1])=="�ԧ�Ҥ�" || trim($arr_temp[1])=="�ԧ�Ҥ" || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="�.�" || trim($arr_temp[1])=="�.��" || trim($arr_temp[1])=="ʤ." || trim($arr_temp[1])=="��.�." || trim($arr_temp[1])=="�ԧ�Ҥ�." || trim($arr_temp[1])=="�ק�Ҥ�" || trim($arr_temp[1])=="�.�.�" || trim($arr_temp[1])=="�ԧ�Ҥ-�" || trim($arr_temp[1])=="lbsk8," || trim($arr_temp[1])=="�ק�Ҥ�" || trim($arr_temp[1])=="�ԧˤ�" || trim($arr_temp[1])=="��Ҥ�" || trim($arr_temp[1])=="�..�." || trim($arr_temp[1])=="�ԧ�Ҥ��" || trim($arr_temp[1])=="ʤ�" || trim($arr_temp[1])=="ʤ" || trim($arr_temp[1])=="�ҧ�Ҥ�") $mm = "08";
					elseif (trim($arr_temp[1])=="�ѹ��¹" || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="�.�" || trim($arr_temp[1])=="�.§" || trim($arr_temp[1])=="�ѹ��¹�" || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="�ѹ���¹" || trim($arr_temp[1])=="�ѹ�¹" || trim($arr_temp[1])=="�չ��¹" || trim($arr_temp[1])=="�ѹ���¹" || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="�ѹ��¹" || trim($arr_temp[1])=="�ѹ���" || trim($arr_temp[1])=="�ѹ����") $mm = "09";
					elseif (trim($arr_temp[1])=="���Ҥ�" || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="�.��." || trim($arr_temp[1])=="��" || trim($arr_temp[1])=="�.�" || 
						trim($arr_temp[1])=="�.��" || trim($arr_temp[1])=="ҵ.�." || trim($arr_temp[1])=="���." || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="����ʤ�" || trim($arr_temp[1])=="����Ҥ�" || trim($arr_temp[1])=="���Ҥ�." || trim($arr_temp[1])=="����Ҥ�" || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="�غҤ�" || trim($arr_temp[1])=="��.�." || trim($arr_temp[1])=="����¹" || trim($arr_temp[1])=="���Ҥ��" || trim($arr_temp[1])=="���ҵ�") $mm = "10";
					elseif (trim($arr_temp[1])=="��Ȩԡ�¹" || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="��" || trim($arr_temp[1])=="��." || trim($arr_temp[1])=="�.�" || 
						trim($arr_temp[1])=="�.§" || trim($arr_temp[1])=="�..�" || trim($arr_temp[1])=="��ʨԡ�¹" || trim($arr_temp[1])=="��Ȩԡ¹" || trim($arr_temp[1])=="��Ȩԡ�¹." || trim($arr_temp[1])=="��Ȩԡ��¹" || trim($arr_temp[1])=="���Ȩԡ�¹" || trim($arr_temp[1])=="��ɵԡ�¹" || trim($arr_temp[1])=="�Ĩԡ�¹" || trim($arr_temp[1])=="��ȨԡҺ�" || trim($arr_temp[1])=="��Ȩס�¹" || trim($arr_temp[1])=="��Ȩԡ��¹") $mm = "11";
					elseif (trim($arr_temp[1])=="�ѹ�Ҥ�" || trim($arr_temp[1])=="�ѹ�Ҥ�" || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="�.��" || trim($arr_temp[1])=="�.�" || trim($arr_temp[1])=="� .�." || trim($arr_temp[1])=="���." || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="�ѹǤ��" || trim($arr_temp[1])=="�ѹ�ҵ�" || trim($arr_temp[1])=="�.��" || trim($arr_temp[1])=="�,�." || trim($arr_temp[1])=="�չ�Ҥ�" || trim($arr_temp[1])=="�ѹҤ�" || trim($arr_temp[1])=="�ѹ�Ҥ�." || trim($arr_temp[1])=="���Ҥ�" || trim($arr_temp[1])=="���Ҥ�") $mm = "12";
					elseif (substr($arr_temp[1],0,4)=="�.�.") {
						$mm = "01";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="��." || substr($arr_temp[1],0,3)=="�.�") {
						$mm = "01";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,4)=="�.�." || substr($arr_temp[1],0,4)=="���.") {
						$mm = "02";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="��." || substr($arr_temp[1],0,3)=="�.�") {
						$mm = "02";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,5)=="��.�." || substr($arr_temp[1],0,5)=="��.�." || substr($arr_temp[1],0,5)=="��.�." || substr($arr_temp[1],0,5)=="��.��") {
						$mm = "03";
						$yy = trim(substr($arr_temp[1],5));
					} elseif (substr($arr_temp[1],0,4)=="�դ.") {
						$mm = "03";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,5)=="��.�." || substr($arr_temp[1],0,5)=="��.§") {
						$mm = "04";
						$yy = trim(substr($arr_temp[1],5));
					} elseif (substr($arr_temp[1],0,4)=="���." || substr($arr_temp[1],0,4)=="���." || substr($arr_temp[1],0,4)=="��.�") {
						$mm = "04";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,4)=="�.�." || substr($arr_temp[1],0,4)=="�.��" || substr($arr_temp[1],0,4)=="���." || substr($arr_temp[1],0,4)=="�.�.") {
						$mm = "05";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="��.") {
						$mm = "05";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,5)=="��.�." || substr($arr_temp[1],0,5)=="��.§") {
						$mm = "06";
						$yy = trim(substr($arr_temp[1],5));
					} elseif (substr($arr_temp[1],0,4)=="���.") {
						$mm = "06";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,4)=="�.�." || substr($arr_temp[1],0,4)=="�.��" || substr($arr_temp[1],0,4)=="�.�.") {
						$mm = "07";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="��.") {
						$mm = "07";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,4)=="�.�.") {
						$mm = "08";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="ʤ.") {
						$mm = "08";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,4)=="�.�." || substr($arr_temp[1],0,4)=="�.§" || substr($arr_temp[1],0,4)=="����" || substr($arr_temp[1],0,4)=="�.«") {
						$mm = "09";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="��." || substr($arr_temp[1],0,2)=="��") {
						$mm = "09";
						$yy = trim(substr($arr_temp[1],2));
					} elseif (substr($arr_temp[1],0,4)=="�.�." || substr($arr_temp[1],0,4)=="�.��" || substr($arr_temp[1],0,4)=="���.") {
						$mm = "10";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="��." || substr($arr_temp[1],0,3)=="�.�") {
						$mm = "10";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,3)=="�.�") {
						$mm = "10";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,4)=="�.�.") {
						$mm = "11";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="��.") {
						$mm = "11";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,4)=="�.�." || substr($arr_temp[1],0,4)=="�.��") {
						$mm = "12";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="��.") {
						$mm = "12";
						$yy = trim(substr($arr_temp[1],3));
					} elseif ($arr_temp[0]=="21�.�." && $arr_temp[1]=="2542") {
						$dd = "21";
						$mm = "12";
						$yy = "2542";
					} elseif ($arr_temp[0]=="25����Ҿѹ��" && $arr_temp[1]=="2543") {
						$dd = "25";
						$mm = "02";
						$yy = "2543";
					} elseif ($arr_temp[0]=="27�.�." && $arr_temp[1]=="2543") {
						$dd = "27";
						$mm = "10";
						$yy = "2543";
					} elseif ($arr_temp[0]=="22�.�." && $arr_temp[1]=="2545") {
						$dd = "22";
						$mm = "05";
						$yy = "2545";
					} elseif ($arr_temp[0]=="1�.�." && $arr_temp[1]=="2545") {
						$dd = "01";
						$mm = "11";
						$yy = "2545";
					} elseif ($arr_temp[0]=="4�ѹ�Ҥ�" && $arr_temp[1]=="2545") {
						$dd = "04";
						$mm = "12";
						$yy = "2545";
					} elseif ($arr_temp[0]=="28�.�." && $arr_temp[1]=="2546") {
						$dd = "28";
						$mm = "01";
						$yy = "2546";
					} elseif ($arr_temp[0]=="11�ԧ�Ҥ�" && $arr_temp[1]=="2546") {
						$dd = "11";
						$mm = "08";
						$yy = "2546";
					} elseif ($arr_temp[0]=="17���Ҥ�" && $arr_temp[1]=="2550") {
						$dd = "17";
						$mm = "01";
						$yy = "2550";
					} elseif ($arr_temp[0]=="1�.�." && $arr_temp[1]=="2551") {
						$dd = "01";
						$mm = "02";
						$yy = "2551";
					} elseif ($arr_temp[0]=="27�.�." && $arr_temp[1]=="2551") {
						$dd = "27";
						$mm = "02";
						$yy = "2551";
					} elseif ($arr_temp[0]=="7�.�." && $arr_temp[1]=="2551") {
						$dd = "07";
						$mm = "10";
						$yy = "2551";
					} elseif ($arr_temp[0]=="27�.�." && $arr_temp[1]=="52") {
						$dd = "27";
						$mm = "02";
						$yy = "2552";
					} elseif ($arr_temp[0]=="11�.�." && $arr_temp[1]=="52") {
						$dd = "11";
						$mm = "09";
						$yy = "2552";
					} elseif ($arr_temp[0]=="30�.�." && $arr_temp[1]=="2555") {
						$dd = "30";
						$mm = "05";
						$yy = "2555";
					} elseif ($arr_temp[0]=="28" && $arr_temp[1]=="�á�Ҥ�2547") {
						$dd = "28";
						$mm = "07";
						$yy = "2547";
					} elseif ($arr_temp[0]=="6" && $arr_temp[1]=="�ѹ�Ҥ�2550") {
						$dd = "06";
						$mm = "12";
						$yy = "2550";
					} elseif ($arr_temp[0]=="11" && $arr_temp[1]=="�ѹ�Ҥ�2552") {
						$dd = "11";
						$mm = "12";
						$yy = "2552";
					} elseif ($arr_temp[0]=="16" && $arr_temp[1]=="�.�2556.") {
						$dd = "16";
						$mm = "12";
						$yy = "2556";
					} elseif ($arr_temp[0]=="7" && $arr_temp[1]=="�." && $arr_temp[2]=="�.53") {
						$dd = "07";
						$mm = "09";
						$yy = "2553";
					} else echo "$arr_temp[0]**$arr_temp[1]**$arr_temp[2]**$REMARK2<br>";
					if (!$yy) $yy = trim($arr_temp[2]);
					if (strlen($yy)==2) $yy = "25".$yy;
					if ($yy+0 < 543) {
						if ($mm < substr($GOVPOS_DATE, 2, 2))
							$yy = substr($GOVPOS_DATE, 4, 4) + 1;
						else
							$yy = substr($GOVPOS_DATE, 4, 4);
					}
					$yy = $yy - 543;
					$DOCDATE = $yy."-".$mm."-".$dd; 
				}
			}			
			$JOBGROUPID = trim($data[JOBGROUPID]);
			$JOBCLASSID = trim($data[JOBCLASSID]);
			if (substr($LEVEL_NO,0,2)=='11') $LEVEL_NO = "11";
			elseif (substr($LEVEL_NO,0,2)=='10') $LEVEL_NO = "10";
			elseif (substr($LEVEL_NO,0,1)=='9' || substr($LEVEL_NO,0,2)=='09') $LEVEL_NO = "09";
			elseif (substr($LEVEL_NO,0,1)=='8' || substr($LEVEL_NO,0,2)=='08') $LEVEL_NO = "08";
			elseif (substr($LEVEL_NO,0,1)=='7' || substr($LEVEL_NO,0,2)=='07') $LEVEL_NO = "07";
			elseif (substr($LEVEL_NO,0,1)=='6' || substr($LEVEL_NO,0,2)=='06') $LEVEL_NO = "06";
			elseif (substr($LEVEL_NO,0,1)=='5' || substr($LEVEL_NO,0,2)=='05') $LEVEL_NO = "05";
			elseif (substr($LEVEL_NO,0,1)=='4' || substr($LEVEL_NO,0,2)=='04') $LEVEL_NO = "04";
			elseif (substr($LEVEL_NO,0,1)=='3' || substr($LEVEL_NO,0,2)=='03') $LEVEL_NO = "03";
			elseif (substr($LEVEL_NO,0,1)=='2' || substr($LEVEL_NO,0,2)=='02') $LEVEL_NO = "02";
			elseif (substr($LEVEL_NO,0,1)=='1' || substr($LEVEL_NO,0,2)=='01') $LEVEL_NO = "01";
			else $LEVEL_NO = "";

			if ($JOBCLASSID=='11') $LEVEL_NO = "O1"; 
			elseif ($JOBCLASSID=='12') $LEVEL_NO = "O2"; 
			elseif ($JOBCLASSID=='13') $LEVEL_NO = "O3"; 
			elseif ($JOBCLASSID=='14') $LEVEL_NO = "O4"; 
			elseif ($JOBCLASSID=='21') $LEVEL_NO = "K1"; 
			elseif ($JOBCLASSID=='22') $LEVEL_NO = "K2"; 
			elseif ($JOBCLASSID=='23') $LEVEL_NO = "K3"; 
			elseif ($JOBCLASSID=='24') $LEVEL_NO = "K4"; 
			elseif ($JOBCLASSID=='25') $LEVEL_NO = "K5"; 
			elseif ($JOBCLASSID=='31') $LEVEL_NO = "D1"; 
			elseif ($JOBCLASSID=='32') $LEVEL_NO = "D2"; 
			elseif ($JOBCLASSID=='41') $LEVEL_NO = "M1"; 
			elseif ($JOBCLASSID=='42') $LEVEL_NO = "M2"; 

			if (!$POSNUM) $POSNUM = "-";
			if (!$DOCNO) $DOCNO = "-";
			if (!$HDATE) $HDATE = "-";
			if (!$DESCRIPTION) $DESCRIPTION = "-";
			$MOV_CODE = "99999";
			if (substr($DESCRIPTION,0,5) == "�͹��" || substr($DESCRIPTION,0,6) == "�Ѻ�͹") 
				$MOV_CODE = "10510";
			elseif (substr($DESCRIPTION,0,25) == "�͹��ѧ�Ѵ�ӹѡ�ҹ�Ⱥ��") 
				$MOV_CODE = "10620";
			elseif (substr($DESCRIPTION,0,5) == "�͹�" || substr($DESCRIPTION,0,6) == "����͹") 
				$MOV_CODE = "10610";
			elseif (substr($DESCRIPTION,0,4) == "����") 
				$MOV_CODE = "103";
			elseif (substr($DESCRIPTION,0,9) == "��§ҹ���") 
				$MOV_CODE = "11260";
			elseif (substr($DESCRIPTION,0,5) == "�͹��") 
				$MOV_CODE = "";
			elseif (substr($DESCRIPTION,0,5) == "�͹��") 
				$MOV_CODE = "";
			elseif (substr($DESCRIPTION,0,5) == "�͹��") 
				$MOV_CODE = "";
			elseif (substr($DESCRIPTION,0,5) == "�͹��") 
				$MOV_CODE = "";
			elseif (substr($DESCRIPTION,0,5) == "�͹��") 
				$MOV_CODE = "";
			elseif (substr($DESCRIPTION,0,5) == "�͹��") 
				$MOV_CODE = "";
			elseif (substr($DESCRIPTION,0,5) == "�͹��") 
				$MOV_CODE = "";
			elseif (substr($DESCRIPTION,0,5) == "�͹��") 
				$MOV_CODE = "";
			elseif (substr($DESCRIPTION,0,5) == "�͹��") 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"���³���ء�͹��˹�") !== false || strpos($DESCRIPTION,"���³��͹��˹�") !== false) 
				$MOV_CODE = "11830";
			elseif (strpos($DESCRIPTION,"���³����") !== false) 
				$MOV_CODE = "11910";
			elseif (strpos($DESCRIPTION,"͹حҵ������Ҫ������͡") !== false || strpos($DESCRIPTION,"͹حҵ������͡") !== false || strpos($DESCRIPTION,"���͡�ҡ�Ҫ���") !== false) 
				$MOV_CODE = "11810";
			elseif (strpos($DESCRIPTION,"�ѡ���Ҫ���᷹") !== false) {
				$MOV_CODE = "11010";
				$ES_CODE = "26";
			} elseif (strpos($DESCRIPTION,"�ѡ�ҡ��㹵��˹�") !== false || strpos($DESCRIPTION,"�ѡ���Ҫ���㹵��˹�") !== false) {
				$MOV_CODE = "11020";
				$ES_CODE = "07";
			} elseif (strpos($DESCRIPTION,"���Ѻ�Թ��͹������鹵���س�ز�") !== false || strpos($DESCRIPTION,"���Ѻ�Թ��͹����ز�") !== false || strpos($DESCRIPTION,"���Ѻ�Թ��͹����س�ز�") !== false || 
				strpos($DESCRIPTION,"���Ѻ�Թ������鹵���س�ز�") !== false) 
				$MOV_CODE = "21510";
			elseif (strpos($DESCRIPTION,"���Ѻ��ҵͺ᷹�����") !== false || strpos($DESCRIPTION,"���Ѻ�Թ��ҵͺ᷹�����") !== false || strpos($DESCRIPTION,"���Ѻ�Թ�ͺ᷹�����") !== false || 
				strpos($DESCRIPTION,"���ҵͺ᷹�����") !== false) 
				$MOV_CODE = "21415";
			elseif (strpos($DESCRIPTION,"����͹�Թ��͹�дѺ����") !== false) 
				$MOV_CODE = "21345";
			elseif (strpos($DESCRIPTION,"����͹�Թ��͹�дѺ���ҡ") !== false) 
				$MOV_CODE = "21335";
			elseif (strpos($DESCRIPTION,"����͹�Թ��͹�дѺ��") !== false) 
				$MOV_CODE = "21325";
			elseif (strpos($DESCRIPTION,"����͹�Թ��͹�дѺ����") !== false) 
				$MOV_CODE = "21315";
			elseif (strpos($DESCRIPTION,"0.5 ���") !== false) 
				$MOV_CODE = "21310";
			elseif (strpos($DESCRIPTION,"1 ���") !== false) 
				$MOV_CODE = "21320";
			elseif (strpos($DESCRIPTION,"1.5 ���") !== false) 
				$MOV_CODE = "21330";
			elseif (strpos($DESCRIPTION,"2 ���") !== false) 
				$MOV_CODE = "21340";
			elseif (strpos($DESCRIPTION,"���֡��") !== false || strpos($DESCRIPTION,"����֡��") !== false) {
				$MOV_CODE = "11210";
				$ES_CODE = "08";
			} elseif (strpos($DESCRIPTION,"��䢤����") !== false) 
				$MOV_CODE = "11420";
			elseif (strpos($DESCRIPTION,"�Թ��͹������") !== false) 
				$MOV_CODE = "21410";
			elseif (strpos($DESCRIPTION,"��� ����͹����Թ��͹") !== false || strpos($DESCRIPTION,"������Թ��͹") !== false || strpos($DESCRIPTION,"����鹢���Թ��͹") !== false || 
				strpos($DESCRIPTION,"������Թ��͹") !== false || strpos($DESCRIPTION,"����鹢���Թ��͹") !== false || strpos($DESCRIPTION,"������Թ��͹") !== false || 
				strpos($DESCRIPTION,"��������͹����Թ��͹") !== false || strpos($DESCRIPTION,"����� ����͹����Թ��͹") !== false || strpos($DESCRIPTION,"����������͹����Թ��͹") !== false || 
				strpos($DESCRIPTION,"��������Թ��͹") !== false || strpos($DESCRIPTION,"������Ѻ�������͹����Թ��͹") !== false || strpos($DESCRIPTION,"������Ѻ�Թ��͹") !== false || 
				strpos($DESCRIPTION,"���������Թ��͹") !== false || strpos($DESCRIPTION,"���������͹���") !== false || strpos($DESCRIPTION,"�������͹���") !== false) 
				$MOV_CODE = "21370";
			elseif (strpos($DESCRIPTION,"���������͹�Թ��͹") !== false || strpos($DESCRIPTION,"�������͹�Թ��͹") !== false || strpos($DESCRIPTION,"����� ����͹�Թ��͹") !== false || 
				strpos($DESCRIPTION,"���������͹�Թ��͹") !== false || strpos($DESCRIPTION,"���������͹�Թ��͹") !== false || strpos($DESCRIPTION,"�������͹�Թ��͹") !== false) 
				$MOV_CODE = "21375";
			elseif (strpos($DESCRIPTION,"����͹����Թ��͹��Шӻ�") !== false) 
				$MOV_CODE = "213";
			elseif (strpos($DESCRIPTION,"����͹�дѺ") !== false || substr($DESCRIPTION,0,6) == "����͹") 
				$MOV_CODE = "104";
			elseif (strpos($DESCRIPTION,"��Ѻ�ѵ���Թ��͹") !== false || strpos($DESCRIPTION,"��Ѻ�ѵ���׹��͹") !== false) 
				$MOV_CODE = "21520";
			elseif (strpos($DESCRIPTION,"���鹨ҡ��÷��ͧ") !== false || strpos($DESCRIPTION,"���鹷��ͧ") !== false) 
				$MOV_CODE = "10220";
			elseif (strpos($DESCRIPTION,"�鹨ҡ��÷��ͧ") !== false || strpos($DESCRIPTION,"�鹷��ͧ") !== false || strpos($DESCRIPTION,"�ҵðҹ����˹��Ѻ�Ҫ��õ���") !== false || 
				strpos($DESCRIPTION,"����ӡ���ࡳ���Ѻ�Ҫ��õ���") !== false || strpos($DESCRIPTION,"�鹨ҡ���ͧ") !== false) 
				$MOV_CODE = "10210";
			elseif (strpos($DESCRIPTION,"¡��ԡ�����") !== false || strpos($DESCRIPTION,"¡��Ԥ����") !== false) 
				$MOV_CODE = "11410";
			elseif (strpos($DESCRIPTION,"¡��ԡ�Թ��ҵͺ᷹") !== false || strpos($DESCRIPTION,"¡��ԡ�Թ�ͺ᷹") !== false || strpos($DESCRIPTION,"¡���ԡ�Թ��ҵͺ᷹") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"¡��ԡ����͹����Թ��͹") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"¡��ԡ����͹�Թ��͹") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"¡��ԡ����͹�дѺ") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"¡��ԡ������Ѻ��ҵͺ᷹�����") !== false || strpos($DESCRIPTION,"¡��ס�Թ��ҵͺ᷹") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"ŧ�ɵѴ�Թ��͹") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"ŧ�ɻŴ�͡") !== false) 
				$MOV_CODE = "12110";
			elseif (strpos($DESCRIPTION,"ŧ���Ҥ�ѳ��") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"ŧ��Ŵ����Թ��͹") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"ŧ������͡") !== false || strpos($DESCRIPTION,"����͡�ҡ�Ҫ���") !== false) 
				$MOV_CODE = "12210";
			elseif (strpos($DESCRIPTION,"Ŵ����Թ��͹ 0.5 ���") !== false) 
				$MOV_CODE = "21610";
			elseif (strpos($DESCRIPTION,"Ŵ����Թ��͹ 1 ���") !== false) 
				$MOV_CODE = "21620";
			elseif (strpos($DESCRIPTION,"�ҡԨ��ǹ���") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"�Ҥ�ʹ") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"�ҵԴ����������") !== false) 
				$MOV_CODE = "11220";
			elseif (strpos($DESCRIPTION,"�һ���") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"�ҽ֡ͺ��") !== false) 
				$MOV_CODE = "11211";
			elseif (strpos($DESCRIPTION,"�觵�ǡ�Ѻ") !== false) 
				$MOV_CODE = "11250";
			elseif (strpos($DESCRIPTION,"���Ѻ�Թ��Шӵ��˹�") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"����Ҫ��û�Ժѵ��Ҫ���") !== false || strpos($DESCRIPTION,"����Ҫ��û�Ժѵ�˹�ҷ��") !== false || strpos($DESCRIPTION,"����Ҫ���仪��»�Ժѵ��Ҫ���") !== false || 
				strpos($DESCRIPTION,"����Ҫ���任�Ժѵ��Ҫ���") !== false || strpos($DESCRIPTION,"��黮Ժѵ��Ҫ���") !== false || strpos($DESCRIPTION,"��黯Ժѵ�˹�ҷ���Ҫ���") !== false || 
				strpos($DESCRIPTION,"���仪��»�Ժѵ��Ҫ���") !== false || strpos($DESCRIPTION,"���任�Ժѵ��Ҫ���") !== false) 
				$MOV_CODE = "11310";
			elseif (strpos($DESCRIPTION,"�Թ��͹") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"�Թ��͹") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"�Թ��͹") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"�Թ��͹") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"�Թ��͹") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"�Թ��͹") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"�Թ��͹") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"�Թ��͹") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"�Թ��͹") !== false) 
				$MOV_CODE = "";
			else {
				if (strpos($DESCRIPTION,"���˹�ҷ���Һ�� 1") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ�� 1", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ�� 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ�� 2") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ�� 2", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ�� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"����˹�ҷ���Һ�� 2") !== false) {
					$arr_temp = explode("����˹�ҷ���Һ�� 2", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ�� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ�� 3") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ�� 3", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"����˹�ҷ���Һ�� 3") !== false) {
					$arr_temp = explode("����˹�ҷ���Һ�� 3", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�����¾�Һ�� (�����¾�Һ�� 1)") !== false) {
					$arr_temp = explode("�����¾�Һ�� (�����¾�Һ�� 1)", $DESCRIPTION);
					$POH_PL_NAME = "�����¾�Һ�� (�����¾�Һ�� 1)";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ��෤�Ԥ 2") !== false) {
					$arr_temp = explode("��Һ��෤�Ԥ 2", $DESCRIPTION);
					$POH_PL_NAME = "��Һ��෤�Ԥ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ��෤�Ԥ 3") !== false) {
					$arr_temp = explode("��Һ��෤�Ԥ 3", $DESCRIPTION);
					$POH_PL_NAME = "��Һ��෤�Ԥ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ��෤�Ԥ 4") !== false) {
					$arr_temp = explode("��Һ��෤�Ԥ 4", $DESCRIPTION);
					$POH_PL_NAME = "��Һ��෤�Ԥ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ�෤�Ԥ 4") !== false) {
					$arr_temp = explode("��Һ�෤�Ԥ 4", $DESCRIPTION);
					$POH_PL_NAME = "��Һ��෤�Ԥ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ��෤�Ԥ 5") !== false) {
					$arr_temp = explode("��Һ��෤�Ԥ 5", $DESCRIPTION);
					$POH_PL_NAME = "��Һ��෤�Ԥ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ��෤�Ԥ 6") !== false) {
					$arr_temp = explode("��Һ��෤�Ԥ 6", $DESCRIPTION);
					$POH_PL_NAME = "��Һ��෤�Ԥ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Ҹ�ó�آ 1") !== false) {
					$arr_temp = explode("���˹�ҷ���Ҹ�ó�آ 1", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Ҹ�ó�آ 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�����¾�Һ�� 1") !== false) {
					$arr_temp = explode("�����¾�Һ�� 1", $DESCRIPTION);
					$POH_PL_NAME = "�����¾�Һ�� 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�����¾�Һ�� 2") !== false) {
					$arr_temp = explode("�����¾�Һ�� 2", $DESCRIPTION);
					$POH_PL_NAME = "�����¾�Һ�� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�����¾�Һ�� 3") !== false) {
					$arr_temp = explode("�����¾�Һ�� 3", $DESCRIPTION);
					$POH_PL_NAME = "�����¾�Һ�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�����¾�Һ��2") !== false) {
					$arr_temp = explode("�����¾�Һ��2", $DESCRIPTION);
					$POH_PL_NAME = "�����¾�Һ��2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�����¾�Һ��3") !== false) {
					$arr_temp = explode("�����¾�Һ��3", $DESCRIPTION);
					$POH_PL_NAME = "�����¾�Һ��3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ���ԪҪվ 3") !== false) {
					$arr_temp = explode("��Һ���ԪҪվ 3", $DESCRIPTION);
					$POH_PL_NAME = "��Һ���ԪҪվ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ���ԪҪվ 4") !== false) {
					$arr_temp = explode("��Һ���ԪҪվ 4", $DESCRIPTION);
					$POH_PL_NAME = "��Һ���ԪҪվ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ���ԪҪվ 5") !== false) {
					$arr_temp = explode("��Һ���ԪҪվ 5", $DESCRIPTION);
					$POH_PL_NAME = "��Һ���ԪҪվ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ���ԪҪվ 6 �.") !== false) {
					$arr_temp = explode("��Һ���ԪҪվ 6 �.", $DESCRIPTION);
					$POH_PL_NAME = "��Һ���ԪҪվ 6 �.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ���ԪҪվ 6") !== false) {
					$arr_temp = explode("��Һ���ԪҪվ 6", $DESCRIPTION);
					$POH_PL_NAME = "��Һ���ԪҪվ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ���ԪҪվ 7Ǫ.") !== false) {
					$arr_temp = explode("��Һ���ԪҪվ 7Ǫ.", $DESCRIPTION);
					$POH_PL_NAME = "��Һ���ԪҪվ 7Ǫ.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ���ԪҪվ 7 Ǫ.") !== false) {
					$arr_temp = explode("��Һ���ԪҪվ 7 Ǫ.", $DESCRIPTION);
					$POH_PL_NAME = "��Һ���ԪҪվ 7 Ǫ.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ���ԪҪվ 7 Ǫ") !== false) {
					$arr_temp = explode("��Һ���ԪҪվ 7 Ǫ", $DESCRIPTION);
					$POH_PL_NAME = "��Һ���ԪҪվ 7 Ǫ";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ���ԪҪվ 7 �.") !== false) {
					$arr_temp = explode("��Һ���ԪҪվ 7 �.", $DESCRIPTION);
					$POH_PL_NAME = "��Һ���ԪҪվ 7 �.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ���ԪҪվ 8 Ǫ.") !== false) {
					$arr_temp = explode("��Һ���ԪҪվ 8 Ǫ.", $DESCRIPTION);
					$POH_PL_NAME = "��Һ���ԪҪվ 8 Ǫ.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ���ԪҪվ 8 Ǫ") !== false) {
					$arr_temp = explode("��Һ���ԪҪվ 8 Ǫ", $DESCRIPTION);
					$POH_PL_NAME = "��Һ���ԪҪվ 8 Ǫ";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ������մ 1") !== false) {
					$arr_temp = explode("���˹�ҷ������մ 1", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ������մ 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ������մ 2") !== false) {
					$arr_temp = explode("���˹�ҷ������մ 2", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ������մ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ������մ 3") !== false) {
					$arr_temp = explode("���˹�ҷ������մ 3", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ������մ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ��ѹ�֡������ 3") !== false) {
					$arr_temp = explode("���˹�ҷ��ѹ�֡������ 3", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ��ѹ�֡������ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ��á�� 4") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ��á�� 4", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ��á�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ��á�� 5") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ��á�� 5", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ��á�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ��á�� 6") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ��á�� 6", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ��á�� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ����������ҹ�ؤ�� 3") !== false) {
					$arr_temp = explode("���˹�ҷ����������ҹ�ؤ�� 3", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ����������ҹ�ؤ�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ����������ҹ�ؤ�� 4") !== false) {
					$arr_temp = explode("���˹�ҷ����������ҹ�ؤ�� 4", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ����������ҹ�ؤ�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ����������ҹ�ؤ�� 5") !== false) {
					$arr_temp = explode("���˹�ҷ����������ҹ�ؤ�� 5", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ����������ҹ�ؤ�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ�����������ҹ�ؤ�� 5") !== false) {
					$arr_temp = explode("���˹�ҷ�����������ҹ�ؤ�� 5", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ����������ҹ�ؤ�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ������çҹ����� 5") !== false) {
					$arr_temp = explode("���˹�ҷ������çҹ����� 5", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ������çҹ����� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ������çҹ����� 6") !== false) {
					$arr_temp = explode("���˹�ҷ������çҹ����� 6", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ������çҹ����� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ������çҹ����� 7") !== false) {
					$arr_temp = explode("���˹�ҷ������çҹ����� 7", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ������çҹ����� 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ�� 2") !== false) {
					$arr_temp = explode("��Һ�� 2", $DESCRIPTION);
					$POH_PL_NAME = "��Һ�� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ�� 3") !== false) {
					$arr_temp = explode("��Һ�� 3", $DESCRIPTION);
					$POH_PL_NAME = "��Һ�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ�� 4") !== false) {
					$arr_temp = explode("��Һ�� 4", $DESCRIPTION);
					$POH_PL_NAME = "��Һ�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ�� 5") !== false) {
					$arr_temp = explode("��Һ�� 5", $DESCRIPTION);
					$POH_PL_NAME = "��Һ�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ�� 6") !== false) {
					$arr_temp = explode("��Һ�� 6", $DESCRIPTION);
					$POH_PL_NAME = "��Һ�� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��پ�Һ�ŵ��") !== false) {
					$arr_temp = explode("��پ�Һ�ŵ��", $DESCRIPTION);
					$POH_PL_NAME = "��پ�Һ�ŵ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ�ŵ��") !== false) {
					$arr_temp = explode("��Һ�ŵ��", $DESCRIPTION);
					$POH_PL_NAME = "��Һ�ŵ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ���") !== false) {
					$arr_temp = explode("��Һ���", $DESCRIPTION);
					$POH_PL_NAME = "��Һ���";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�����¾�Һ�Ũѵ��") !== false) {
					$arr_temp = explode("�����¾�Һ�Ũѵ��", $DESCRIPTION);
					$POH_PL_NAME = "�����¾�Һ�Ũѵ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ�Ũѵ��") !== false) {
					$arr_temp = explode("��Һ�Ũѵ��", $DESCRIPTION);
					$POH_PL_NAME = "��Һ�Ũѵ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ���Ҹ�ó�آ 4") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ���Ҹ�ó�آ 4", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ���Ҹ�ó�آ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ���Ҹ�ó�آ 5") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ���Ҹ�ó�آ 5", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ���Ҹ�ó�آ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ���Ҹ�ó�آ 6 �") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ���Ҹ�ó�آ 6 �", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ���Ҹ�ó�آ 6 �";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ���Ҹ�ó�آ 6�.") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ���Ҹ�ó�آ 6�.", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ���Ҹ�ó�آ 6�.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ���Ҹ�ó�آ 6") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ���Ҹ�ó�آ 6", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ���Ҹ�ó�آ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ���Ҹ�ó�آ 7 �.") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ���Ҹ�ó�آ 7 �.", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ���Ҹ�ó�آ 7 �.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ���Ҹ�ó�آ 7") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ���Ҹ�ó�آ 7", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ���Ҹ�ó�آ 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ���Ҹ�ó�آ 8 �.") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ���Ҹ�ó�آ 8 �.", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ���Ҹ�ó�آ 8 �.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"����¹��ѡ�ҹ") !== false) {
					$arr_temp = explode("����¹��ѡ�ҹ", $DESCRIPTION);
					$POH_PL_NAME = "����¹��ѡ�ҹ";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���á�� 1") !== false) {
					$arr_temp = explode("���˹�ҷ���á�� 1", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���á�� 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���á�� 2") !== false) {
					$arr_temp = explode("���˹�ҷ���á�� 2", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���á�� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���á�� 3") !== false) {
					$arr_temp = explode("���˹�ҷ���á�� 3", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���á�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���á�� 4") !== false) {
					$arr_temp = explode("���˹�ҷ���á�� 4", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���á�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ������çҹ��á�� 5") !== false) {
					$arr_temp = explode("���˹�ҷ������çҹ��á�� 5", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ������çҹ��á�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Ҹ�ó�آ 2") !== false) {
					$arr_temp = explode("���˹�ҷ���Ҹ�ó�آ 2", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Ҹ�ó�آ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Ҹ�ó�آ 3") !== false) {
					$arr_temp = explode("���˹�ҷ���Ҹ�ó�آ 3", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Ҹ�ó�آ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ǪʶԵ� 1") !== false) {
					$arr_temp = explode("���˹�ҷ���ǪʶԵ� 1", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ǪʶԵ� 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ǪʶԵ� 2") !== false) {
					$arr_temp = explode("���˹�ҷ���ǪʶԵ� 2", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ǪʶԵ� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ǪʶԵ� 3") !== false) {
					$arr_temp = explode("���˹�ҷ���ǪʶԵ� 3", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ǪʶԵ� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ǪʶԵ� 4") !== false) {
					$arr_temp = explode("���˹�ҷ���ǪʶԵ� 4", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ǪʶԵ� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ǪʶԵ� 5") !== false) {
					$arr_temp = explode("���˹�ҷ���ǪʶԵ� 5", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ǪʶԵ� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ǪʶԵ� 6") !== false) {
					$arr_temp = explode("���˹�ҷ���ǪʶԵ� 6", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ǪʶԵ� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ǪʶԵ� 7") !== false) {
					$arr_temp = explode("���˹�ҷ���ǪʶԵ� 7", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ǪʶԵ� 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ǪʶԵ�2") !== false) {
					$arr_temp = explode("���˹�ҷ���ǪʶԵ�2", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ǪʶԵ� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ǪʶԵ�3") !== false) {
					$arr_temp = explode("���˹�ҷ���ǪʶԵ�3", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ǪʶԵ� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ǪʶԵ�4") !== false) {
					$arr_temp = explode("���˹�ҷ���ǪʶԵ�4", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ǪʶԵ� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ե�Է�� 3") !== false) {
					$arr_temp = explode("�ѡ�Ե�Է�� 3", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ե�Է�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ե�Է�� 4") !== false) {
					$arr_temp = explode("�ѡ�Ե�Է�� 4", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ե�Է�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ե�Է�� 5") !== false) {
					$arr_temp = explode("�ѡ�Ե�Է�� 5", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ե�Է�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ե�Է�� 6 �.") !== false) {
					$arr_temp = explode("�ѡ�Ե�Է�� 6 �.", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ե�Է�� 6 �.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ե�Է�� 6 �") !== false) {
					$arr_temp = explode("�ѡ�Ե�Է�� 6 �", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ե�Է�� 6 �";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ե�Է�� 6") !== false) {
					$arr_temp = explode("�ѡ�Ե�Է�� 6", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ե�Է�� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ե�Է�ҵ��") !== false) {
					$arr_temp = explode("�ѡ�Ե�Է�ҵ��", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ե�Է�ҵ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ե�Է���") !== false) {
					$arr_temp = explode("�ѡ�Ե�Է���", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ե�Է���";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ��á�� 3") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ��á�� 3", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ��á�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ��á�� 4") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ��á�� 4", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ��á�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ��á�� 5") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ��á�� 5", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ��á�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�ѧ��ʧ������ 3") !== false) {
					$arr_temp = explode("�ѡ�ѧ��ʧ������ 3", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�ѧ��ʧ������ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�ѧ��ʧ������ 4") !== false) {
					$arr_temp = explode("�ѡ�ѧ��ʧ������ 4", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�ѧ��ʧ������ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�ѧ��ʧ������ 5") !== false) {
					$arr_temp = explode("�ѡ�ѧ��ʧ������ 5", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�ѧ��ʧ������ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�ѧ��ʧ������ 6") !== false) {
					$arr_temp = explode("�ѡ�ѧ��ʧ������ 6", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�ѧ��ʧ������ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�ѧ��ʧ������ 7 �.") !== false) {
					$arr_temp = explode("�ѡ�ѧ��ʧ������ 7 �.", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�ѧ��ʧ������ 7 �.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�ѧ��ʧ��������") !== false) {
					$arr_temp = explode("�ѡ�ѧ��ʧ��������", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�ѧ��ʧ��������";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�ѧ��ʧ�������") !== false) {
					$arr_temp = explode("�ѡ�ѧ��ʧ�������", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�ѧ��ʧ�������";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ�����Թ��кѭ�� 1") !== false) {
					$arr_temp = explode("���˹�ҷ�����Թ��кѭ�� 1", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ�����Թ��кѭ�� 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ�����Թ��кѭ�� 2") !== false) {
					$arr_temp = explode("���˹�ҷ�����Թ��кѭ�� 2", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ�����Թ��кѭ�� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ�����Թ��кѭ�� 3") !== false) {
					$arr_temp = explode("���˹�ҷ�����Թ��кѭ�� 3", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ�����Թ��кѭ�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ�����Թ��кѭ�� 4") !== false) {
					$arr_temp = explode("���˹�ҷ�����Թ��кѭ�� 4", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ�����Թ��кѭ�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ�����Թ��кѭ�� 5") !== false) {
					$arr_temp = explode("���˹�ҷ�����Թ��кѭ�� 5", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ�����Թ��кѭ�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ����Թ��кѭ�� 2") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ����Թ��кѭ�� 2", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ����Թ��кѭ�� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ����Թ��кѭ�� 3") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ����Թ��кѭ�� 3", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ����Թ��кѭ�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ����Թ��кѭ�� 4") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ����Թ��кѭ�� 4", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ����Թ��кѭ�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ����Թ��кѭ�� 5") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ����Թ��кѭ�� 5", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ����Թ��кѭ�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ����Թ��кѭ�� 6") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ����Թ��кѭ�� 6", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ����Թ��кѭ�� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ��ѹ�֡������ 2") !== false) {
					$arr_temp = explode("���˹�ҷ��ѹ�֡������ 2", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ��ѹ�֡������ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ��ѹ�֡������ 3") !== false) {
					$arr_temp = explode("���˹�ҷ��ѹ�֡������ 3", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ��ѹ�֡������ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ������ҡ�� 1") !== false) {
					$arr_temp = explode("���˹�ҷ������ҡ�� 1", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ������ҡ�� 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ������ҡ�� 2") !== false) {
					$arr_temp = explode("���˹�ҷ������ҡ�� 2", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ������ҡ�� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ѡ�ҹ����ҡ�� 2") !== false) {
					$arr_temp = explode("��ѡ�ҹ����ҡ�� 2", $DESCRIPTION);
					$POH_PL_NAME = "��ѡ�ҹ����ҡ�� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"����ҡ� 2") !== false) {
					$arr_temp = explode("����ҡ� 2", $DESCRIPTION);
					$POH_PL_NAME = "����ҡ� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"����ҡ� 3") !== false) {
					$arr_temp = explode("����ҡ� 3", $DESCRIPTION);
					$POH_PL_NAME = "����ҡ� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"����ҡ� 4") !== false) {
					$arr_temp = explode("����ҡ� 4", $DESCRIPTION);
					$POH_PL_NAME = "����ҡ� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"����ҡ� 5") !== false) {
					$arr_temp = explode("����ҡ� 5", $DESCRIPTION);
					$POH_PL_NAME = "����ҡ� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"����ҡ� 6") !== false) {
					$arr_temp = explode("����ҡ� 6", $DESCRIPTION);
					$POH_PL_NAME = "����ҡ� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"����ҡõ��") !== false) {
					$arr_temp = explode("����ҡõ��", $DESCRIPTION);
					$POH_PL_NAME = "����ҡõ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"����ҡ� ���") !== false) {
					$arr_temp = explode("����ҡ� ���", $DESCRIPTION);
					$POH_PL_NAME = "����ҡõ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���������Ѫ�� 1") !== false) {
					$arr_temp = explode("���������Ѫ�� 1", $DESCRIPTION);
					$POH_PL_NAME = "���������Ѫ�� 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���������Ѫ�� 2") !== false) {
					$arr_temp = explode("���������Ѫ�� 2", $DESCRIPTION);
					$POH_PL_NAME = "���������Ѫ�� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���������Ѫ�� 3") !== false) {
					$arr_temp = explode("���������Ѫ�� 3", $DESCRIPTION);
					$POH_PL_NAME = "���������Ѫ�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ���Ѫ�� 4") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ���Ѫ�� 4", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ���Ѫ�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ���Ѫ���� 2") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ���Ѫ���� 2", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ���Ѫ���� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ���Ѫ���� 3") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ���Ѫ���� 3", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ���Ѫ���� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ���Ѫ���� 4") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ���Ѫ���� 4", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ���Ѫ���� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ���Ѫ���� 5") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ���Ѫ���� 5", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ���Ѫ���� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ���Ѫ���� 6") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ���Ѫ���� 6", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ���Ѫ���� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���ᾷ�� 4") !== false) {
					$arr_temp = explode("���ᾷ�� 4", $DESCRIPTION);
					$POH_PL_NAME = "���ᾷ�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���ᾷ�� 5") !== false) {
					$arr_temp = explode("���ᾷ�� 5", $DESCRIPTION);
					$POH_PL_NAME = "���ᾷ�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���ᾷ�� 6") !== false) {
					$arr_temp = explode("���ᾷ�� 6", $DESCRIPTION);
					$POH_PL_NAME = "���ᾷ�� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���ᾷ�� 7 Ǫ.") !== false) {
					$arr_temp = explode("���ᾷ�� 7 Ǫ.", $DESCRIPTION);
					$POH_PL_NAME = "���ᾷ�� 7 Ǫ.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"(���ᾷ�� 7)") !== false) {
					$arr_temp = explode("(���ᾷ�� 7)", $DESCRIPTION);
					$POH_PL_NAME = "���ᾷ�� 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���ᾷ�� 7 ���ᾷ����ӹҭ��þ����") !== false) {
					$arr_temp = explode("���ᾷ�� 7 ���ᾷ����ӹҭ��þ����", $DESCRIPTION);
					$POH_PL_NAME = "���ᾷ�� 7 ���ᾷ����ӹҭ��þ����";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"(���ᾷ����ӹҭ��þ����)") !== false) {
					$arr_temp = explode("(���ᾷ����ӹҭ��þ����)", $DESCRIPTION);
					$POH_PL_NAME = "���ᾷ����ӹҭ��þ����";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���ᾷ�� 7 ���ӹҭ��þ����") !== false) {
					$arr_temp = explode("���ᾷ�� 7 ���ӹҭ��þ����", $DESCRIPTION);
					$POH_PL_NAME = "���ᾷ�� 7 ���ӹҭ��þ����";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���ᾷ�� 7") !== false) {
					$arr_temp = explode("���ᾷ�� 7", $DESCRIPTION);
					$POH_PL_NAME = "���ᾷ�� 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���ᾷ�� 8 Ǫ.") !== false) {
					$arr_temp = explode("���ᾷ�� 8 Ǫ.", $DESCRIPTION);
					$POH_PL_NAME = "���ᾷ�� 8 Ǫ.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���ᾷ�� 8") !== false) {
					$arr_temp = explode("���ᾷ�� 8", $DESCRIPTION);
					$POH_PL_NAME = "���ᾷ�� 8";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���ᾷ�� 9") !== false) {
					$arr_temp = explode("���ᾷ�� 9", $DESCRIPTION);
					$POH_PL_NAME = "���ᾷ�� 9";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���ᾷ�� 10") !== false) {
					$arr_temp = explode("���ᾷ�� 10", $DESCRIPTION);
					$POH_PL_NAME = "���ᾷ�� 10";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���ᾷ���") !== false) {
					$arr_temp = explode("���ᾷ���", $DESCRIPTION);
					$POH_PL_NAME = "���ᾷ���";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���ᾷ���͡") !== false) {
					$arr_temp = explode("���ᾷ���͡", $DESCRIPTION);
					$POH_PL_NAME = "���ᾷ���͡";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ俿�� 1") !== false) {
					$arr_temp = explode("��ҧ俿�� 1", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ俿�� 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ俿�� 2") !== false) {
					$arr_temp = explode("��ҧ俿�� 2", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ俿�� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ俿�� 3") !== false) {
					$arr_temp = explode("��ҧ俿�� 3", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ俿�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ª�ҧ෤�Ԥ 4") !== false) {
					$arr_temp = explode("��ª�ҧ෤�Ԥ 4", $DESCRIPTION);
					$POH_PL_NAME = "��ª�ҧ෤�Ԥ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ª�ҧ෤�Ԥ 5") !== false) {
					$arr_temp = explode("��ª�ҧ෤�Ԥ 5", $DESCRIPTION);
					$POH_PL_NAME = "��ª�ҧ෤�Ԥ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ª�ҧ෤�Ԥ 6") !== false) {
					$arr_temp = explode("��ª�ҧ෤�Ԥ 6", $DESCRIPTION);
					$POH_PL_NAME = "��ª�ҧ෤�Ԥ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Ҫ�ǺӺѴ 2") !== false) {
					$arr_temp = explode("���˹�ҷ���Ҫ�ǺӺѴ 2", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Ҫ�ǺӺѴ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Ҫ�ǺӺѴ3") !== false) {
					$arr_temp = explode("���˹�ҷ���Ҫ�ǺӺѴ3", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Ҫ�ǺӺѴ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Ҫ�ǺӺѴ 4") !== false) {
					$arr_temp = explode("���˹�ҷ���Ҫ�ǺӺѴ 4", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Ҫ�ǺӺѴ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ҪպӺѴ 4") !== false) {
					$arr_temp = explode("���˹�ҷ���ҪպӺѴ 4", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Ҫ�ǺӺѴ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Ҫ�ǺӺѴ 5") !== false) {
					$arr_temp = explode("���˹�ҷ���Ҫ�ǺӺѴ 5", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Ҫ�ǺӺѴ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Ҫ�ǺӺѴ 6") !== false) {
					$arr_temp = explode("���˹�ҷ���Ҫ�ǺӺѴ 6", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Ҫ�ǺӺѴ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Է����ʵ����ᾷ�� 1") !== false) {
					$arr_temp = explode("���˹�ҷ���Է����ʵ����ᾷ�� 1", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Է����ʵ����ᾷ�� 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Է����ʵ����ᾷ�� 2") !== false) {
					$arr_temp = explode("���˹�ҷ���Է����ʵ����ᾷ�� 2", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Է����ʵ����ᾷ�� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Է����ʵ����ᾷ�� 3") !== false) {
					$arr_temp = explode("���˹�ҷ���Է����ʵ����ᾷ�� 3", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Է����ʵ����ᾷ�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Է����ʵ����ᾷ�� 4") !== false) {
					$arr_temp = explode("���˹�ҷ���Է����ʵ����ᾷ�� 4", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Է����ʵ����ᾷ�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Է����ʵ����ᾷ�� 5") !== false) {
					$arr_temp = explode("���˹�ҷ���Է����ʵ����ᾷ�� 5", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Է����ʵ����ᾷ�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ�Է����ʵ����ᾷ�� 2") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ�Է����ʵ����ᾷ�� 2", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ�Է����ʵ����ᾷ�� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ�Է����ʵ����ᾷ�� 3") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ�Է����ʵ����ᾷ�� 3", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ�Է����ʵ����ᾷ�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ�Է����ʵ����ᾷ�� 4") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ�Է����ʵ����ᾷ�� 4", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ�Է����ʵ����ᾷ�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ�Է����ʵ����ᾷ�� 5") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ�Է����ʵ����ᾷ�� 5", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ�Է����ʵ����ᾷ�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ�Է����ʵ����ᾷ�� 6") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ�Է����ʵ����ᾷ�� 6", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ�Է����ʵ����ᾷ�� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ�����Ҿ�ӺѴ 1") !== false) {
					$arr_temp = explode("���˹�ҷ�����Ҿ�ӺѴ 1", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ�����Ҿ�ӺѴ 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ�����Ҿ�ӺѴ 2") !== false) {
					$arr_temp = explode("���˹�ҷ�����Ҿ�ӺѴ 2", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ�����Ҿ�ӺѴ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ�����Ҿ�ӺѴ 3") !== false) {
					$arr_temp = explode("���˹�ҷ�����Ҿ�ӺѴ 3", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ�����Ҿ�ӺѴ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ�Ǫ������鹿� 3") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ�Ǫ������鹿� 3", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ�Ǫ������鹿� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ�Ǫ������鹿� 4") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ�Ǫ������鹿� 4", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ�Ǫ������鹿� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ�Ǫ������鹿� 5") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ�Ǫ������鹿� 5", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ�Ǫ������鹿� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ�Ǫ������鹿� 6") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ�Ǫ������鹿� 6", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ�Ǫ������鹿� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ����ͧ��ش 1") !== false) {
					$arr_temp = explode("���˹�ҷ����ͧ��ش 1", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ����ͧ��ش 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ����ͧ��ش 2") !== false) {
					$arr_temp = explode("���˹�ҷ����ͧ��ش 2", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ����ͧ��ش 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ����ͧ��ش 3") !== false) {
					$arr_temp = explode("���˹�ҷ����ͧ��ش 3", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ����ͧ��ش 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ����ͧ��ش 4") !== false) {
					$arr_temp = explode("���˹�ҷ����ͧ��ش 4", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ����ͧ��ش 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ�Ҿ���") !== false) {
					$arr_temp = explode("��ҧ�Ҿ���", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ�Ҿ���";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ�Ҿ���ᾷ�� 3") !== false) {
					$arr_temp = explode("��ҧ�Ҿ���ᾷ�� 3", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ�Ҿ���ᾷ�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ�Ҿ���ᾷ�� 4") !== false) {
					$arr_temp = explode("��ҧ�Ҿ���ᾷ�� 4", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ�Ҿ���ᾷ�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ�Ҿ���ᾷ�� 5") !== false) {
					$arr_temp = explode("��ҧ�Ҿ���ᾷ�� 5", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ�Ҿ���ᾷ�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ�Ҿ���ᾷ�� 6") !== false) {
					$arr_temp = explode("��ҧ�Ҿ���ᾷ�� 6", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ�Ҿ���ᾷ�� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ�Ҿ���ᾷ�� 7�.") !== false) {
					$arr_temp = explode("��ҧ�Ҿ���ᾷ�� 7�.", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ�Ҿ���ᾷ�� 7�.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Է����ʵ����") !== false) {
					$arr_temp = explode("�ѡ�Է����ʵ����", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Է����ʵ����";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Է����ʵ���") !== false) {
					$arr_temp = explode("�ѡ�Է����ʵ���", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Է����ʵ���";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Է����ʵ����ᾷ�� 3") !== false) {
					$arr_temp = explode("�ѡ�Է����ʵ����ᾷ�� 3", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Է����ʵ����ᾷ�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Է����ʵ����ᾷ�� 4") !== false) {
					$arr_temp = explode("�ѡ�Է����ʵ����ᾷ�� 4", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Է����ʵ����ᾷ�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Է����ʵ����ᾷ�� 5") !== false) {
					$arr_temp = explode("�ѡ�Է����ʵ����ᾷ�� 5", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Է����ʵ����ᾷ�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Է����ʵ����ᾷ�� 6�.") !== false) {
					$arr_temp = explode("�ѡ�Է����ʵ����ᾷ�� 6�.", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Է����ʵ����ᾷ�� 6�.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Է����ʵ����ᾷ�� 6") !== false) {
					$arr_temp = explode("�ѡ�Է����ʵ����ᾷ�� 6", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Է����ʵ����ᾷ�� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Է����ʵ����ᾷ�� 7 �.") !== false) {
					$arr_temp = explode("�ѡ�Է����ʵ����ᾷ�� 7 �.", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Է����ʵ����ᾷ�� 7 �.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Է����ʵ����ᾷ�� 8 �") !== false) {
					$arr_temp = explode("�ѡ�Է����ʵ����ᾷ�� 8 �", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Է����ʵ����ᾷ�� 8 �";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ෤�Ԥ���ᾷ�� 6�.") !== false) {
					$arr_temp = explode("�ѡ෤�Ԥ���ᾷ�� 6�.", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ෤�Ԥ���ᾷ�� 6�.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ෤�Ԥ���ᾷ�� 7 Ǫ.") !== false) {
					$arr_temp = explode("�ѡ෤�Ԥ���ᾷ�� 7 Ǫ.", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ෤�Ԥ���ᾷ�� 7 Ǫ.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ෤�Ԥ���ᾷ�� 8 Ǫ.") !== false) {
					$arr_temp = explode("�ѡ෤�Ԥ���ᾷ�� 8 Ǫ.", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ෤�Ԥ���ᾷ�� 8 Ǫ.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ��ʴ� 3") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ��ʴ� 3", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ��ʴ� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ��ʴ� 4") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ��ʴ� 4", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ��ʴ� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ��ʴ� 5") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ��ʴ� 5", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ��ʴ� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ����Ҿ�ӺѴ 3") !== false) {
					$arr_temp = explode("�ѡ����Ҿ�ӺѴ 3", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ����Ҿ�ӺѴ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ����Ҿ�ӺѴ 4") !== false) {
					$arr_temp = explode("�ѡ����Ҿ�ӺѴ 4", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ����Ҿ�ӺѴ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ����Ҿ�ӺѴ 5") !== false) {
					$arr_temp = explode("�ѡ����Ҿ�ӺѴ 5", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ����Ҿ�ӺѴ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ����Ҿ�ӺѴ 6�.") !== false) {
					$arr_temp = explode("�ѡ����Ҿ�ӺѴ 6�.", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ����Ҿ�ӺѴ 6�.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ����Ҿ�ӺѴ 6") !== false) {
					$arr_temp = explode("�ѡ����Ҿ�ӺѴ 6", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ����Ҿ�ӺѴ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ����Ҿ�ӺѴ 7Ǫ.") !== false) {
					$arr_temp = explode("�ѡ����Ҿ�ӺѴ 7Ǫ.", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ����Ҿ�ӺѴ 7Ǫ.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ����Ҿ�ӺѴ���") !== false) {
					$arr_temp = explode("�ѡ����Ҿ�ӺѴ���", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ����Ҿ�ӺѴ���";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"����Ҫ�ǺӺѴ���") !== false) {
					$arr_temp = explode("����Ҫ�ǺӺѴ���", $DESCRIPTION);
					$POH_PL_NAME = "����Ҫ�ǺӺѴ���";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ�ѵ��") !== false) {
					$arr_temp = explode("��ҧ�ѵ��", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ�ѵ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ª�ҧ������ùԤ�� 3") !== false) {
					$arr_temp = explode("��ª�ҧ������ùԤ�� 3", $DESCRIPTION);
					$POH_PL_NAME = "��ª�ҧ������ùԤ�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ª�ҧ���Ť��͹Ԥ�� 4") !== false) {
					$arr_temp = explode("��ª�ҧ���Ť��͹Ԥ�� 4", $DESCRIPTION);
					$POH_PL_NAME = "��ª�ҧ���Ť��͹Ԥ�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ª�ҧ���Ť��͹Ԥ�� 5") !== false) {
					$arr_temp = explode("��ª�ҧ���Ť��͹Ԥ�� 5", $DESCRIPTION);
					$POH_PL_NAME = "��ª�ҧ���Ť��͹Ԥ�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ª�ҧ����礷�͹Ԥ�� 6") !== false) {
					$arr_temp = explode("��ª�ҧ����礷�͹Ԥ�� 6", $DESCRIPTION);
					$POH_PL_NAME = "��ª�ҧ����礷�͹Ԥ�� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ����硷�͹Ԥ�� 1") !== false) {
					$arr_temp = explode("��ҧ����硷�͹Ԥ�� 1", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ����硷�͹Ԥ�� 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ����硷�͹ԡ�� 2") !== false) {
					$arr_temp = explode("��ҧ����硷�͹ԡ�� 2", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ����硷�͹ԡ�� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ����礷�͹Ԥ�� 3") !== false) {
					$arr_temp = explode("��ҧ����礷�͹Ԥ�� 3", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ����礷�͹Ԥ�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ������ùԤ 1") !== false) {
					$arr_temp = explode("��ҧ������ùԤ 1", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ���Ť��͹Ԥ�� 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ���Ť�ùԤ 2") !== false) {
					$arr_temp = explode("��ҧ���Ť�ùԤ 2", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ���Ť��͹Ԥ�� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ���Ť�ùԤ 3") !== false) {
					$arr_temp = explode("��ҧ���Ť�ùԤ 3", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ���Ť��͹Ԥ�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ���Թ��кѭ�� 3") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ���Թ��кѭ�� 3", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ���Թ��кѭ�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ���Թ��кѭ�� 4") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ���Թ��кѭ�� 4", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ���Թ��кѭ�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ���Թ��кѭ�� 5") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ���Թ��кѭ�� 5", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ���Թ��кѭ�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ������çҹ����Թ��кѭ�� 5") !== false) {
					$arr_temp = explode("���˹�ҷ������çҹ����Թ��кѭ�� 5", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ������çҹ����Թ��кѭ�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ʵ��ȹ�֡�� 1") !== false) {
					$arr_temp = explode("���˹�ҷ���ʵ��ȹ�֡�� 1", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ʵ��ȹ�֡�� 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ʵ��ȹ�֡�� 2") !== false) {
					$arr_temp = explode("���˹�ҷ���ʵ��ȹ�֡�� 2", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ʵ��ȹ�֡�� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ʵ��ȹ�֡�� 3") !== false) {
					$arr_temp = explode("���˹�ҷ���ʵ��ȹ�֡�� 3", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ʵ��ȹ�֡�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ�ʵ��ȹ�֡�� 4") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ�ʵ��ȹ�֡�� 4", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ�ʵ��ȹ�֡�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ�ʵ��ȹ�֡�� 5") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ�ʵ��ȹ�֡�� 5", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ�ʵ��ȹ�֡�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�����·ѹ�ᾷ�� 1") !== false) {
					$arr_temp = explode("�����·ѹ�ᾷ�� 1", $DESCRIPTION);
					$POH_PL_NAME = "�����·ѹ�ᾷ�� 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�����·ѹ�ᾷ�� 2") !== false) {
					$arr_temp = explode("�����·ѹ�ᾷ�� 2", $DESCRIPTION);
					$POH_PL_NAME = "�����·ѹ�ᾷ�� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�����·ѹ�ᾷ�� 3") !== false) {
					$arr_temp = explode("�����·ѹ�ᾷ�� 3", $DESCRIPTION);
					$POH_PL_NAME = "�����·ѹ�ᾷ�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�����·ѹ�ᾷ�� 4") !== false) {
					$arr_temp = explode("�����·ѹ�ᾷ�� 4", $DESCRIPTION);
					$POH_PL_NAME = "�����·ѹ�ᾷ�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�����·ѹ�ᾷ�� 5") !== false) {
					$arr_temp = explode("�����·ѹ�ᾷ�� 5", $DESCRIPTION);
					$POH_PL_NAME = "�����·ѹ�ᾷ�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѹ�ᾷ�� 4") !== false) {
					$arr_temp = explode("�ѹ�ᾷ�� 4", $DESCRIPTION);
					$POH_PL_NAME = "�ѹ�ᾷ�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѹ�ᾷ�� 5") !== false) {
					$arr_temp = explode("�ѹ�ᾷ�� 5", $DESCRIPTION);
					$POH_PL_NAME = "�ѹ�ᾷ�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѹ�ᾷ�� 6") !== false) {
					$arr_temp = explode("�ѹ�ᾷ�� 6", $DESCRIPTION);
					$POH_PL_NAME = "�ѹ�ᾷ�� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѹ�ᾷ�� 7 Ǫ.") !== false) {
					$arr_temp = explode("�ѹ�ᾷ�� 7 Ǫ.", $DESCRIPTION);
					$POH_PL_NAME = "�ѹ�ᾷ�� 7 Ǫ.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"(�ѹ�ᾷ�� 7�.)") !== false) {
					$arr_temp = explode("(�ѹ�ᾷ�� 7�.)", $DESCRIPTION);
					$POH_PL_NAME = "�ѹ�ᾷ�� 7�.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѹ�ᾷ�� 7�.") !== false) {
					$arr_temp = explode("�ѹ�ᾷ�� 7�.", $DESCRIPTION);
					$POH_PL_NAME = "�ѹ�ᾷ�� 7�.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"(�ѹ�ᾷ�� 7)") !== false) {
					$arr_temp = explode("(�ѹ�ᾷ�� 7)", $DESCRIPTION);
					$POH_PL_NAME = "�ѹ�ᾷ�� 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѹ�ᾷ�� 7") !== false) {
					$arr_temp = explode("�ѹ�ᾷ�� 7", $DESCRIPTION);
					$POH_PL_NAME = "�ѹ�ᾷ�� 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"(�ѹ�ᾷ�� 8�.)") !== false) {
					$arr_temp = explode("(�ѹ�ᾷ�� 8�.)", $DESCRIPTION);
					$POH_PL_NAME = "�ѹ�ᾷ�� 8�.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѹ�ᾷ�� 8�.") !== false) {
					$arr_temp = explode("�ѹ�ᾷ�� 8�.", $DESCRIPTION);
					$POH_PL_NAME = "�ѹ�ᾷ�� 8�.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѹ�ᾷ�� 8 Ǫ.") !== false) {
					$arr_temp = explode("�ѹ�ᾷ�� 8 Ǫ.", $DESCRIPTION);
					$POH_PL_NAME = "�ѹ�ᾷ�� 8 Ǫ.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѹ�ᾷ�� 8 Ǫ") !== false) {
					$arr_temp = explode("�ѹ�ᾷ�� 8 Ǫ", $DESCRIPTION);
					$POH_PL_NAME = "�ѹ�ᾷ�� 8 Ǫ";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѹ�ᾷ�� 8Ǫ.") !== false) {
					$arr_temp = explode("�ѹ�ᾷ�� 8Ǫ.", $DESCRIPTION);
					$POH_PL_NAME = "�ѹ�ᾷ�� 8Ǫ.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"(�ѹ�ᾷ�� 8)") !== false) {
					$arr_temp = explode("(�ѹ�ᾷ�� 8)", $DESCRIPTION);
					$POH_PL_NAME = "�ѹ�ᾷ�� 8";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѹ�ᾷ�� 9 Ǫ.") !== false) {
					$arr_temp = explode("�ѹ�ᾷ�� 9 Ǫ.", $DESCRIPTION);
					$POH_PL_NAME = "�ѹ�ᾷ�� 9 Ǫ.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ѧ�ա��ᾷ�� 2") !== false) {
					$arr_temp = explode("���˹�ҷ���ѧ�ա��ᾷ�� 2", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ѧ�ա��ᾷ�� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ѧ�ա��ᾷ�� 3") !== false) {
					$arr_temp = explode("���˹�ҷ���ѧ�ա��ᾷ�� 3", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ѧ�ա��ᾷ�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ѧ�ա��ᾷ�� 4") !== false) {
					$arr_temp = explode("���˹�ҷ���ѧ�ա��ᾷ�� 4", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ѧ�ա��ᾷ�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ѧ�ա��ᾷ�� 5") !== false) {
					$arr_temp = explode("���˹�ҷ���ѧ�ա��ᾷ�� 5", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ѧ�ա��ᾷ�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ѧ�ա��ᾷ�� 6") !== false) {
					$arr_temp = explode("���˹�ҷ���ѧ�ա��ᾷ�� 6", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ѧ�ա��ᾷ�� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�ѧ�ա��ᾷ�� 3") !== false) {
					$arr_temp = explode("�ѡ�ѧ�ա��ᾷ�� 3", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�ѧ�ա��ᾷ�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�ѧ�ա��ᾷ�� 4") !== false) {
					$arr_temp = explode("�ѡ�ѧ�ա��ᾷ�� 4", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�ѧ�ա��ᾷ�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�ѧ�ա��ᾷ�� 5") !== false) {
					$arr_temp = explode("�ѡ�ѧ�ա��ᾷ�� 5", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�ѧ�ա��ᾷ�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�ѧ�ա��ᾷ�� 6 �.") !== false) {
					$arr_temp = explode("�ѡ�ѧ�ա��ᾷ�� 6 �.", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�ѧ�ա��ᾷ�� 6 �.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ѧ��෤�Ԥ 1") !== false) {
					$arr_temp = explode("���˹�ҷ���ѧ��෤�Ԥ 1", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ѧ��෤�Ԥ 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ѧ��෤�Ԥ 2") !== false) {
					$arr_temp = explode("���˹�ҷ���ѧ��෤�Ԥ 2", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ѧ��෤�Ԥ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ѧ��෤�Ԥ 3") !== false) {
					$arr_temp = explode("���˹�ҷ���ѧ��෤�Ԥ 3", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ѧ��෤�Ԥ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ѧ��෤�Ԥ 4") !== false) {
					$arr_temp = explode("���˹�ҷ���ѧ��෤�Ԥ 4", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ѧ��෤�Ԥ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ѧ��෤�Ԥ 5") !== false) {
					$arr_temp = explode("���˹�ҷ���ѧ��෤�Ԥ 5", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ѧ��෤�Ԥ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ���ԪҪվ�ӹҭ��� ��ҹ��þ�Һ�� �������Ԫҡ��") !== false) {
					$arr_temp = explode("��Һ���ԪҪվ�ӹҭ��� ��ҹ��þ�Һ�� �������Ԫҡ��", $DESCRIPTION);
					$POH_PL_NAME = "��Һ���ԪҪվ�ӹҭ��� ��ҹ��þ�Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ��෤�Ԥ�ӹҭ�ҹ �����������") !== false) {
					$arr_temp = explode("��Һ��෤�Ԥ�ӹҭ�ҹ �����������", $DESCRIPTION);
					$POH_PL_NAME = "��Һ��෤�Ԥ�ӹҭ�ҹ";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ��෤�Ԥ�ӹҭ�ҹ") !== false) {
					$arr_temp = explode("��Һ��෤�Ԥ�ӹҭ�ҹ", $DESCRIPTION);
					$POH_PL_NAME = "��Һ��෤�Ԥ�ӹҭ�ҹ";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ѵ��çҹ����仪ӹҭ��� �������Ԫҡ��") !== false) {
					$arr_temp = explode("�ѡ�Ѵ��çҹ����仪ӹҭ��� �������Ԫҡ��", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ѵ��çҹ����仪ӹҭ���";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ���������º�����Ἱ�ӹҭ��þ���� ��ҹ�Ѳ���к������� �������Ԫҡ�� ") !== false) {
					$arr_temp = explode("�ѡ���������º�����Ἱ�ӹҭ��þ���� ��ҹ�Ѳ���к������� �������Ԫҡ�� ", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ���������º�����Ἱ�ӹҭ��þ���� ��ҹ�Ѳ���к�������";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ���ԪҪվ�ӹҭ��þ���� ��ҹ��þ�Һ�ż�����˹ѡ �������Ԫҡ��") !== false) {
					$arr_temp = explode("��Һ���ԪҪվ�ӹҭ��þ���� ��ҹ��þ�Һ�ż�����˹ѡ �������Ԫҡ��", $DESCRIPTION);
					$POH_PL_NAME = "��Һ���ԪҪվ�ӹҭ��þ���� ��ҹ��þ�Һ�ż�����˹ѡ";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ��Ъ�����ѹ�� 3") !== false) {
					$arr_temp = explode("�ѡ��Ъ�����ѹ�� 3", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ��Ъ�����ѹ�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ��Ъ�����ѹ�� 4") !== false) {
					$arr_temp = explode("�ѡ��Ъ�����ѹ�� 4", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ��Ъ�����ѹ�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ��Ъ�����ѹ�� 5") !== false) {
					$arr_temp = explode("�ѡ��Ъ�����ѹ�� 5", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ��Ъ�����ѹ�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ��Ъ�����ѹ�� 6 �.") !== false) {
					$arr_temp = explode("�ѡ��Ъ�����ѹ�� 6 �.", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ��Ъ�����ѹ�� 6 �.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ��Ъ�����ѹ�� 6") !== false) {
					$arr_temp = explode("�ѡ��Ъ�����ѹ�� 6", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ��Ъ�����ѹ�� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ��Ъ�����ѹ�� 7 �") !== false) {
					$arr_temp = explode("�ѡ��Ъ�����ѹ�� 7 �", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ��Ъ�����ѹ�� 7 �";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ��Ъ�����ѹ�� 7") !== false) {
					$arr_temp = explode("�ѡ��Ъ�����ѹ�� 7", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ��Ъ�����ѹ�� 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ��Ъ�����ѹ��ӹҭ��� �������Ԫҡ��") !== false) {
					$arr_temp = explode("�ѡ��Ъ�����ѹ��ӹҭ��� �������Ԫҡ��", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ��Ъ�����ѹ��ӹҭ���";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ������� 3") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ������� 3", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ������� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ������� 4") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ������� 4", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ������� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ������� 5") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ������� 5", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ������� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ������� 6") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ������� 6", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ������� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ������� 7") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ������� 7", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ������� 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ���آ�֡�� 3") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ���آ�֡�� 3", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ���آ�֡�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ���آ�֡�� 4") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ���آ�֡�� 4", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ���آ�֡�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ���آ�֡�� 5") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ���آ�֡�� 5", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ���آ�֡�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ���آ�֡�� 6 �.") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ���آ�֡�� 6 �.", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ���آ�֡�� 6 �.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ���آ�֡�� 6 �") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ���آ�֡�� 6 �", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ���آ�֡�� 6 �";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ���آ�֡�� 6�.") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ���آ�֡�� 6�.", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ���آ�֡�� 6�.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ���آ�֡�� 7 �.") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ���آ�֡�� 7 �.", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ���آ�֡�� 7 �.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ���آ�֡�� 7 �") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ���آ�֡�� 7 �", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ���آ�֡�� 7 �";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Һ���ԪҪվ�ӹҭ��þ���� ��ҹ��þ�Һ�� �������Ԫҡ��") !== false) {
					$arr_temp = explode("��Һ���ԪҪվ�ӹҭ��þ���� ��ҹ��þ�Һ�� �������Ԫҡ��", $DESCRIPTION);
					$POH_PL_NAME = "��Һ���ԪҪվ�ӹҭ��þ���� ��ҹ��þ�Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���ᾷ������Ǫҭ ��ҹ�Ǫ���� �ҢҨѡ�ػ���ҷ�Է�� �������Ԫҡ��") !== false) {
					$arr_temp = explode("���ᾷ������Ǫҭ ��ҹ�Ǫ���� �ҢҨѡ�ػ���ҷ�Է�� �������Ԫҡ��", $DESCRIPTION);
					$POH_PL_NAME = "���ᾷ������Ǫҭ ��ҹ�Ǫ���� �ҢҨѡ�ػ���ҷ�Է��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ó��ѡ�� 3") !== false) {
					$arr_temp = explode("��ó��ѡ�� 3", $DESCRIPTION);
					$POH_PL_NAME = "��ó��ѡ�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ó��ѡ�� 4") !== false) {
					$arr_temp = explode("��ó��ѡ�� 4", $DESCRIPTION);
					$POH_PL_NAME = "��ó��ѡ�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ó��ѡ�� 5") !== false) {
					$arr_temp = explode("��ó��ѡ�� 5", $DESCRIPTION);
					$POH_PL_NAME = "��ó��ѡ�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ó��ѡ�� 6") !== false) {
					$arr_temp = explode("��ó��ѡ�� 6", $DESCRIPTION);
					$POH_PL_NAME = "��ó��ѡ�� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ؤ�ҡ� 3") !== false) {
					$arr_temp = explode("�ؤ�ҡ� 3", $DESCRIPTION);
					$POH_PL_NAME = "�ؤ�ҡ� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ؤ�ҡ� 4") !== false) {
					$arr_temp = explode("�ؤ�ҡ� 4", $DESCRIPTION);
					$POH_PL_NAME = "�ؤ�ҡ� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ؤ�ҡ� 5") !== false) {
					$arr_temp = explode("�ؤ�ҡ� 5", $DESCRIPTION);
					$POH_PL_NAME = "�ؤ�ҡ� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ؤ�ҡ� 6 �.") !== false) {
					$arr_temp = explode("�ؤ�ҡ� 6 �.", $DESCRIPTION);
					$POH_PL_NAME = "�ؤ�ҡ� 6 �.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ؤ�ҡ� 6 �") !== false) {
					$arr_temp = explode("�ؤ�ҡ� 6 �", $DESCRIPTION);
					$POH_PL_NAME = "�ؤ�ҡ� 6 �";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ؤ�ҡ� 6") !== false) {
					$arr_temp = explode("�ؤ�ҡ� 6", $DESCRIPTION);
					$POH_PL_NAME = "�ؤ�ҡ� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ؤ�ҡ� 7�") !== false) {
					$arr_temp = explode("�ؤ�ҡ� 7�", $DESCRIPTION);
					$POH_PL_NAME = "�ؤ�ҡ� 7�";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ؤ�ҡ� 7 �.") !== false) {
					$arr_temp = explode("�ؤ�ҡ� 7 �.", $DESCRIPTION);
					$POH_PL_NAME = "�ؤ�ҡ� 7 �.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ؤ�ҡ� 7 �") !== false) {
					$arr_temp = explode("�ؤ�ҡ� 7 �", $DESCRIPTION);
					$POH_PL_NAME = "�ؤ�ҡ� 7 �";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ؤ�ҡ� 7") !== false) {
					$arr_temp = explode("�ؤ�ҡ� 7", $DESCRIPTION);
					$POH_PL_NAME = "�ؤ�ҡ� 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"(�ؤ�ҡ� 8)") !== false) {
					$arr_temp = explode("(�ؤ�ҡ� 8)", $DESCRIPTION);
					$POH_PL_NAME = "�ؤ�ҡ� 8";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ؤ�ҡ� 8 �") !== false) {
					$arr_temp = explode("�ؤ�ҡ� 8 �", $DESCRIPTION);
					$POH_PL_NAME = "�ؤ�ҡ� 8 �";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ª�ҧ��Ż� 2") !== false) {
					$arr_temp = explode("��ª�ҧ��Ż� 2", $DESCRIPTION);
					$POH_PL_NAME = "��ª�ҧ��Ż� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ª�ҧ��Ż� 3") !== false) {
					$arr_temp = explode("��ª�ҧ��Ż� 3", $DESCRIPTION);
					$POH_PL_NAME = "��ª�ҧ��Ż� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ª�ҧ��Ż� 4") !== false) {
					$arr_temp = explode("��ª�ҧ��Ż� 4", $DESCRIPTION);
					$POH_PL_NAME = "��ª�ҧ��Ż� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ª�ҧ��Ż� 5") !== false) {
					$arr_temp = explode("��ª�ҧ��Ż� 5", $DESCRIPTION);
					$POH_PL_NAME = "��ª�ҧ��Ż� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ª�����Ż� 5") !== false) {
					$arr_temp = explode("��ª�����Ż� 5", $DESCRIPTION);
					$POH_PL_NAME = "��ª�ҧ��Ż� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ª�ҧ��Ż� 6") !== false) {
					$arr_temp = explode("��ª�ҧ��Ż� 6", $DESCRIPTION);
					$POH_PL_NAME = "��ª�ҧ��Ż� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ�ѡ�ҹ���Ѫ�����ӹҭ�ҹ �����������") !== false) {
					$arr_temp = explode("��Ҿ�ѡ�ҹ���Ѫ�����ӹҭ�ҹ �����������", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ�ѡ�ҹ���Ѫ�����ӹҭ�ҹ";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���Ѫ�� 3") !== false) {
					$arr_temp = explode("���Ѫ�� 3", $DESCRIPTION);
					$POH_PL_NAME = "���Ѫ�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���Ѫ�� 4") !== false) {
					$arr_temp = explode("���Ѫ�� 4", $DESCRIPTION);
					$POH_PL_NAME = "���Ѫ�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���Ѫ�� 5") !== false) {
					$arr_temp = explode("���Ѫ�� 5", $DESCRIPTION);
					$POH_PL_NAME = "���Ѫ�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���Ѫ�� 6�.") !== false) {
					$arr_temp = explode("���Ѫ�� 6�.", $DESCRIPTION);
					$POH_PL_NAME = "���Ѫ�� 6�.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���Ѫ�� 6 �.") !== false) {
					$arr_temp = explode("���Ѫ�� 6 �.", $DESCRIPTION);
					$POH_PL_NAME = "���Ѫ�� 6 �.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���Ѫ�� 7 Ǫ.") !== false) {
					$arr_temp = explode("���Ѫ�� 7 Ǫ.", $DESCRIPTION);
					$POH_PL_NAME = "���Ѫ�� 7 Ǫ.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ������������ 3") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ������������ 3", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ������������ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ������������ 4") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ������������ 4", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ������������ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ������������ 5") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ������������ 5", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ������������ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ������������ 6") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ������������ 6", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ������������ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ѡ����Է����ʵ��ѵ��") !== false) {
					$arr_temp = explode("��ѡ����Է����ʵ��ѵ��", $DESCRIPTION);
					$POH_PL_NAME = "��ѡ�ҹ�Է����ʵ��ѵ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ѡ�ҹ�Է����ʵ��ѵ��") !== false) {
					$arr_temp = explode("��ѡ�ҹ�Է����ʵ��ѵ��", $DESCRIPTION);
					$POH_PL_NAME = "��ѡ�ҹ�Է����ʵ��ѵ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡʶԵ� 3") !== false) {
					$arr_temp = explode("�ѡʶԵ� 3", $DESCRIPTION);
					$POH_PL_NAME = "�ѡʶԵ� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡʶԵ� 4") !== false) {
					$arr_temp = explode("�ѡʶԵ� 4", $DESCRIPTION);
					$POH_PL_NAME = "�ѡʶԵ� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡʶԵ� 5") !== false) {
					$arr_temp = explode("�ѡʶԵ� 5", $DESCRIPTION);
					$POH_PL_NAME = "�ѡʶԵ� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡʶԵ� 6") !== false) {
					$arr_temp = explode("�ѡʶԵ� 6", $DESCRIPTION);
					$POH_PL_NAME = "�ѡʶԵ� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ���֡�Ҿ���� 4") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ���֡�Ҿ���� 4", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ���֡�Ҿ���� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ���֡�Ҿ���� 5") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ���֡�Ҿ���� 5", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ���֡�Ҿ���� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ���֡�Ҿ���� 6�.") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ���֡�Ҿ���� 6�.", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ���֡�Ҿ���� 6�.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"�ѡ�Ԫҡ���֡�Ҿ���� 7") !== false) {
					$arr_temp = explode("�ѡ�Ԫҡ���֡�Ҿ���� 7", $DESCRIPTION);
					$POH_PL_NAME = "�ѡ�Ԫҡ���֡�Ҿ���� 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Ъ�����ѹ�� 1") !== false) {
					$arr_temp = explode("���˹�ҷ���Ъ�����ѹ�� 1", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Ъ�����ѹ�� 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Ъ�����ѹ�� 2") !== false) {
					$arr_temp = explode("���˹�ҷ���Ъ�����ѹ�� 2", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Ъ�����ѹ�� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Ъ�����ѹ�� 3") !== false) {
					$arr_temp = explode("���˹�ҷ���Ъ�����ѹ�� 3", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Ъ�����ѹ�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Ъ�����ѹ�� 4") !== false) {
					$arr_temp = explode("���˹�ҷ���Ъ�����ѹ�� 4", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Ъ�����ѹ�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Ъ�����ѹ�� 5") !== false) {
					$arr_temp = explode("���˹�ҷ���Ъ�����ѹ�� 5", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Ъ�����ѹ�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��Ҿ��ҷ���Է����ʵ����ᾷ�� 2") !== false) {
					$arr_temp = explode("��Ҿ��ҷ���Է����ʵ����ᾷ�� 2", $DESCRIPTION);
					$POH_PL_NAME = "��Ҿ��ҷ���Է����ʵ����ᾷ�� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ��ѹ�֡������ 1") !== false) {
					$arr_temp = explode("���˹�ҷ��ѹ�֡������ 1", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ��ѹ�֡������ 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷպѹ�֡������ 2") !== false) {
					$arr_temp = explode("���˹�ҷպѹ�֡������ 2", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷպѹ�֡������ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ��ѹ�֡������ 3") !== false) {
					$arr_temp = explode("���˹�ҷ��ѹ�֡������ 3", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ��ѹ�֡������ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ��ѹ�֡������ 4") !== false) {
					$arr_temp = explode("���˹�ҷ��ѹ�֡������ 4", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ��ѹ�֡������ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ��ѹ�֡������ 5") !== false) {
					$arr_temp = explode("���˹�ҷ��ѹ�֡������ 5", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ��ѹ�֡������ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ��֡ͺ�� 3") !== false) {
					$arr_temp = explode("���˹�ҷ��֡ͺ�� 3", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ��֡ͺ�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ��֡ͺ�� 4") !== false) {
					$arr_temp = explode("���˹�ҷ��֡ͺ�� 4", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ��֡ͺ�� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ��֡ͺ�� 5") !== false) {
					$arr_temp = explode("���˹�ҷ��֡ͺ�� 5", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ��֡ͺ�� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ��֡ͺ�� 6 �") !== false) {
					$arr_temp = explode("���˹�ҷ��֡ͺ�� 6 �", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ��֡ͺ�� 6 �";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ��աͺ�� 6") !== false) {
					$arr_temp = explode("���˹�ҷ��աͺ�� 6", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ��աͺ�� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ�����������º�����Ἱ 3") !== false) {
					$arr_temp = explode("���˹�ҷ�����������º�����Ἱ 3", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ�����������º�����Ἱ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ�����������º�����Ἱ 4") !== false) {
					$arr_temp = explode("���˹�ҷ�����������º�����Ἱ 4", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ�����������º�����Ἱ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ�����������º�����Ἱ 5") !== false) {
					$arr_temp = explode("���˹�ҷ�����������º�����Ἱ 5", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ�����������º�����Ἱ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ�����������º�����Ἱ 6 �.") !== false) {
					$arr_temp = explode("���˹�ҷ�����������º�����Ἱ 6 �.", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ�����������º�����Ἱ 6 �.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ�����������º�����Ἱ 6") !== false) {
					$arr_temp = explode("���˹�ҷ�����������º�����Ἱ 6", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ�����������º�����Ἱ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ�����������º�����Ἱ 7 �.") !== false) {
					$arr_temp = explode("���˹�ҷ�����������º�����Ἱ 7 �.", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ�����������º�����Ἱ 7 �.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ�����������º�����Ἱ 7 �") !== false) {
					$arr_temp = explode("���˹�ҷ�����������º�����Ἱ 7 �", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ�����������º�����Ἱ 7 �";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ�����������º�����Ἱ 7") !== false) {
					$arr_temp = explode("���˹�ҷ�����������º�����Ἱ 7", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ�����������º�����Ἱ 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ�����������º�����Ἱ 8 �") !== false) {
					$arr_temp = explode("���˹�ҷ�����������º�����Ἱ 8 �", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ�����������º�����Ἱ 8 �";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ʴ� 1") !== false) {
					$arr_temp = explode("���˹�ҷ���ʴ� 1", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ʴ� 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ʴ� 2") !== false) {
					$arr_temp = explode("���˹�ҷ���ʴ� 2", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ʴ� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ʴ� 3") !== false) {
					$arr_temp = explode("���˹�ҷ���ʴ� 3", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ʴ� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���ʴ� 4") !== false) {
					$arr_temp = explode("���˹�ҷ���ʴ� 4", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���ʴ� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ���� 1") !== false) {
					$arr_temp = explode("��ҧ���� 1", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ���� 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ���� 2") !== false) {
					$arr_temp = explode("��ҧ���� 2", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ���� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ���� 3") !== false) {
					$arr_temp = explode("��ҧ���� 3", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ���� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ����ͧ¹�� 1") !== false) {
					$arr_temp = explode("��ҧ����ͧ¹�� 1", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ����ͧ¹�� 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ����ͧ¹�� 2") !== false) {
					$arr_temp = explode("��ҧ����ͧ¹�� 2", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ����ͧ¹�� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ����ͧ¹�� 3") !== false) {
					$arr_temp = explode("��ҧ����ͧ¹�� 3", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ����ͧ¹�� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ෤�Ԥ 1") !== false) {
					$arr_temp = explode("��ҧ෤�Ԥ 1", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ෤�Ԥ 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ෤�Ԥ 2") !== false) {
					$arr_temp = explode("��ҧ෤�Ԥ 2", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ෤�Ԥ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ෤�Ԥ 3") !== false) {
					$arr_temp = explode("��ҧ෤�Ԥ 3", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ෤�Ԥ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ����ػ�ó� 1") !== false) {
					$arr_temp = explode("��ҧ����ػ�ó� 1", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ����ػ�ó� 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ����ػ�ó� 2") !== false) {
					$arr_temp = explode("��ҧ����ػ�ó� 2", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ����ػ�ó� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ����ػ�ó� 3") !== false) {
					$arr_temp = explode("��ҧ����ػ�ó� 3", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ����ػ�ó� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ����ػ�ó� 4") !== false) {
					$arr_temp = explode("��ҧ����ػ�ó� 4", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ����ػ�ó� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ����ػ�ó� 5") !== false) {
					$arr_temp = explode("��ҧ����ػ�ó� 5", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ����ػ�ó� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"��ҧ����ػ�ó� 6") !== false) {
					$arr_temp = explode("��ҧ����ػ�ó� 6", $DESCRIPTION);
					$POH_PL_NAME = "��ҧ����ػ�ó� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ����硫���� 1") !== false) {
					$arr_temp = explode("���˹�ҷ����硫���� 1", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ����硫���� 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ����硫���� 2") !== false) {
					$arr_temp = explode("���˹�ҷ����硫���� 2", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ����硫���� 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ����硫���� 3") !== false) {
					$arr_temp = explode("���˹�ҷ����硫���� 3", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ����硫���� 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ������çҹ��ʴ� 4") !== false) {
					$arr_temp = explode("���˹�ҷ������çҹ��ʴ� 4", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ������çҹ��ʴ� 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ������çҹ��ʴ� 5") !== false) {
					$arr_temp = explode("���˹�ҷ������çҹ��ʴ� 5", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ������çҹ��ʴ� 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ������çҹ��ʴ� 6") !== false) {
					$arr_temp = explode("���˹�ҷ������çҹ��ʴ� 6", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ������çҹ��ʴ� 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ������çҹ��ʴ� 7") !== false) {
					$arr_temp = explode("���˹�ҷ������çҹ��ʴ� 7", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ������çҹ��ʴ� 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"���˹�ҷ���Һ��") !== false) {
					$arr_temp = explode("���˹�ҷ���Һ��", $DESCRIPTION);
					$POH_PL_NAME = "���˹�ҷ���Һ��";
					$POH_ORG = trim($arr_temp[1]);
				} else {
//					echo "$DESCRIPTION<br>";
				}
			}

			if (strpos($POH_ORG,"�ͧ������˹�ҷ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"þ.ʧ��") !== false || strpos($POH_ORG,"�ç��Һ��ʧ��") !== false) {
				$POH_ORG3 = "�ç��Һ��ʧ��";
			} elseif (strpos($POH_ORG,"þ.�͹��") !== false) {
				$POH_ORG3 = "þ.�͹�� �ʨ.�͹��";
			} elseif (strpos($POH_ORG,"þ.��û��") !== false) {
				$POH_ORG3 = "þ.��û��";
			} elseif (strpos($POH_ORG,"þ.����Ҫ����Ҫ����") !== false) {
				$POH_ORG3 = "þ.����Ҫ����Ҫ����";
			} elseif (strpos($POH_ORG,"þ.��Դ�Թ") !== false || strpos($POH_ORG,"�ç��Һ����Դ�Թ") !== false) {
				$POH_ORG3 = "�ç��Һ����Դ�Թ";
			} elseif (strpos($POH_ORG,"þ.�Ҫ�Զ�") !== false || strpos($POH_ORG,"�ç��Һ���Ҫ�Զ�") !== false || strpos($POH_ORG,"�.�.�Ҫ�Զ�") !== false || strpos($POH_ORG,"þ. �Ҫ�Զ�") !== false || strpos($POH_ORG,"þ.�Ҫ�Զ�") !== false) {
				$POH_ORG3 = "�ç��Һ���Ҫ�Զ�";
			} elseif (strpos($POH_ORG,"�ͧ�кҴ�Է��") !== false) {
				$POH_ORG3 = "�ͧ�кҴ�Է��";
			} elseif (strpos($POH_ORG,"þ.����Ҫ����") !== false) {
				$POH_ORG3 = "þ.����Ҫ����";
			} elseif (strpos($POH_ORG,"þ.ྪú�ó�") !== false || strpos($POH_ORG,"þ. ྪú�ó�") !== false) {
				$POH_ORG3 = "þ.ྪú�ó�";
			} elseif (strpos($POH_ORG,"þ.�ä��ǧ͡") !== false || strpos($POH_ORG,"�ç��Һ���ä��ǧ͡") !== false) {
				$POH_ORG3 = "�ç��Һ���ä��ǧ͡";
			} elseif (strpos($POH_ORG,"ʶҺѹ�آ�Ҿ����觪ҵ�����ҪԹ�") !== false) {
				$POH_ORG3 = "ʶҺѹ�آ�Ҿ����觪ҵ�����ҪԹ�";
			} elseif (strpos($POH_ORG,"ʶҺѹ����ҷ�Է��") !== false) {
				$POH_ORG3 = "ʶҺѹ����ҷ�Է��";
			} elseif (strpos($POH_ORG,"�ç��Һ�Ż���ҷ��§����") !== false || strpos($POH_ORG,"þ.����ҷ��§����") !== false) {
				$POH_ORG3 = "�ç��Һ�Ż���ҷ��§����";
			} elseif (strpos($POH_ORG,"�ٹ���ä���˹ѧࢵ��͹�Ҥ�� �ѧ��Ѵ��ѧ") !== false) {
				$POH_ORG3 = "�ٹ���ä���˹ѧࢵ��͹�Ҥ�� �ѧ��Ѵ��ѧ";
			} elseif (strpos($POH_ORG,"ʶҺѹ�آ�Ҿ���") !== false) {
				$POH_ORG3 = "ʶҺѹ�آ�Ҿ���";
			} elseif (strpos($POH_ORG,"þ.������") !== false || strpos($POH_ORG,"þ.����һ�Ъ��ѡ��") !== false || strpos($POH_ORG,"þ.����һ�Ъ�- �ѡ��") !== false || strpos($POH_ORG,"�ç��Һ������һ�Ъ��ѡ��") !== false) {
				$POH_ORG3 = "�ç��Һ������һ�Ъ��ѡ�� (�Ѵ���ԧ)";
			} elseif (strpos($POH_ORG,"�.�.�ӻҧ") !== false) {
				$POH_ORG3 = "�.�.�ӻҧ";
			} elseif (strpos($POH_ORG,"þ.��þ�Է�����ʧ�� �ʨ.�غ��Ҫ�ҹ�") !== false || strpos($POH_ORG,"þ.��þ�Է�Ի��ʧ�� �غ��Ҫ�ҹ�") !== false) {
				$POH_ORG3 = "þ.��þ�Է�����ʧ�� �ʨ.�غ��Ҫ�ҹ�";
			} elseif (strpos($POH_ORG,"þ.���ä��Ъ��ѡ�� �ʨ.������ä�") !== false || strpos($POH_ORG,"�ç��Һ�����ä��Ъ��ѡ��") !== false) {
				$POH_ORG3 = "þ.���ä��Ъ��ѡ�� �ʨ.������ä�";
			} elseif (strpos($POH_ORG,"þ.����� �ʨ.�����") !== false || strpos($POH_ORG,"þ.����� �ӹѡ�ҹ�Ҹ�ó�آ�ѧ��Ѵ�����") !== false || strpos($POH_ORG,"þ. �����") !== false) {
				$POH_ORG3 = "þ.����� �ʨ.�����";
			} elseif (strpos($POH_ORG,"þ.��ҹ��� �ʨ.�شøҹ�") !== false) {
				$POH_ORG3 = "þ.��ҹ��� �ʨ.�شøҹ�";
			} elseif (strpos($POH_ORG,"þ.��л����� �ѹ�����") !== false || strpos($POH_ORG,"þ. ��л����� �ѹ�����") !== false) {
				$POH_ORG3 = "þ.��л����� �ѹ����� �ʨ.�ѹ�����";
			} elseif (strpos($POH_ORG,"�ٹ���ԡ���Ҹ�ó�آ 23 ������� �ӹѡ͹����") !== false) {
				$POH_ORG3 = "�ٹ���ԡ���Ҹ�ó�آ 23 ������� �ӹѡ͹����";
			} elseif (strpos($POH_ORG,"�.�.������� �ӹѡ�ҹ�Ҹ�ó�آ�ѧ��Ѵ�������") !== false) {
				$POH_ORG3 = "�.�.������� �ӹѡ�ҹ�Ҹ�ó�آ�ѧ��Ѵ�������";
			} elseif (strpos($POH_ORG,"þ.⹹�ѧ �ʨ.˹ͧ�������") !== false) {
				$POH_ORG3 = "þ.⹹�ѧ �ʨ.˹ͧ�������";
			} elseif (strpos($POH_ORG,"þ.�ҡ�ҧ �ʨ.˹ͧ�������") !== false) {
				$POH_ORG3 = "þ.�ҡ�ҧ �ʨ.˹ͧ�������";
			} elseif (strpos($POH_ORG,"þ.��ͧ�Ҵ �ʨ.������") !== false) {
				$POH_ORG3 = "þ.��ͧ�Ҵ �ʨ.������";
			} elseif (strpos($POH_ORG,"þ.��ط��Ҥ� �ʨ.��ط��Ҥ�") !== false) {
				$POH_ORG3 = "þ.��ط��Ҥ� �ʨ.��ط��Ҥ�";
			} elseif (strpos($POH_ORG,"�.�.��Ш�����Ҩ.ྪú��� �ʨ.ྪú���") !== false) {
				$POH_ORG3 = "�.�.��Ш�����Ҩ.ྪú��� �ʨ.ྪú���";
			} elseif (strpos($POH_ORG,"þ.���ѵ��Ҫ�ҹ�") !== false || strpos($POH_ORG,"�ç��Һ�Ź��ѵ��Ҫ�ҹ�") !== false || strpos($POH_ORG,"þ.���ѵ��") !== false) {
				$POH_ORG3 = "�ç��Һ�Ź��ѵ��Ҫ�ҹ�";
			} elseif (strpos($POH_ORG,"�ç��Һ����") !== false || strpos($POH_ORG,"þ.��") !== false) {
				$POH_ORG3 = "�ç��Һ����";
			} elseif (strpos($POH_ORG,"�.�.�����ѵ��") !== false) {
				$POH_ORG3 = "�.�.�����ѵ��";
			} elseif (strpos($POH_ORG,"þ.��ҹ���� �ʨ.ž����") !== false) {
				$POH_ORG3 = "þ.��ҹ���� �ʨ.ž����";
			} elseif (strpos($POH_ORG,"�ç��Һ������Ҫ�����ո����Ҫ") !== false) {
				$POH_ORG3 = "�ç��Һ������Ҫ�����ո����Ҫ";
			} elseif (strpos($POH_ORG,"þ.��ʸ�") !== false) {
				$POH_ORG3 = "þ.��ʸ�";
			} elseif (strpos($POH_ORG,"þ.�ԨԵ�") !== false) {
				$POH_ORG3 = "þ.�ԨԵ�";
			} elseif (strpos($POH_ORG,"�.�.�С��ǻ�� �ʨ.�ѧ��") !== false) {
				$POH_ORG3 = "�.�.�С��ǻ�� �ʨ.�ѧ��";
			} elseif (strpos($POH_ORG,"þ.��Ҹ����") !== false) {
				$POH_ORG3 = "þ.��Ҹ����";
			} elseif (strpos($POH_ORG,"þ.��ҹ�� �ʨ.�͹��") !== false || strpos($POH_ORG,"�.�.��ҹ�� �ʨ.�͹��") !== false) {
				$POH_ORG3 = "þ.��ҹ�� �ʨ.�͹��";
			} elseif (strpos($POH_ORG,"þ.�ӻҧ �ʨ.�ӻҧ") !== false) {
				$POH_ORG3 = "þ.�ӻҧ �ʨ.�ӻҧ";
			} elseif (strpos($POH_ORG,"þ.⾸���� �ӹѡ�ҹ�Ҹ�ó�آ�ѧ��Ѵ�Ҫ����") !== false) {
				$POH_ORG3 = "þ.⾸���� �ӹѡ�ҹ�Ҹ�ó�آ�ѧ��Ѵ�Ҫ����";
			} elseif (strpos($POH_ORG,"þ. ����ʹ �ʨ. �ҡ") !== false) {
				$POH_ORG3 = "þ. ����ʹ �ʨ. �ҡ";
			} elseif (strpos($POH_ORG,"�.�.��к���") !== false || strpos($POH_ORG,"þ. ��к���") !== false) {
				$POH_ORG3 = "�.�.��к���";
			} elseif (strpos($POH_ORG,"�.�.�ѵ�ҹ� �ʨ.�ѵ�ҹ�") !== false) {
				$POH_ORG3 = "�.�.�ѵ�ҹ� �ʨ.�ѵ�ҹ�";
			} elseif (strpos($POH_ORG,"þ.ີ� �ʨ.����") !== false) {
				$POH_ORG3 = "þ.ີ� �ʨ.����";
			} elseif (strpos($POH_ORG,"ʶҺѹ�������觪ҵ�") !== false) {
				$POH_ORG3 = "ʶҺѹ�������觪ҵ�";
			} elseif (strpos($POH_ORG,"þ.�ä�Դ����Ҥ���ѹ�͡��§�˹�� �.�͹��") !== false) {
				$POH_ORG3 = "þ.�ä�Դ����Ҥ���ѹ�͡��§�˹�� �.�͹��";
			} elseif (strpos($POH_ORG,"ʶҺѹ�ä���˹ѧ") !== false) {
				$POH_ORG3 = "ʶҺѹ�ä���˹ѧ";
			} elseif (strpos($POH_ORG,"þ.��к���") !== false) {
				$POH_ORG3 = "þ.��к���";
			} elseif (strpos($POH_ORG,"þ.�դ��� �ʨ.����Ҫ����") !== false) {
				$POH_ORG3 = "þ.�դ��� �ʨ.����Ҫ����";
			} elseif (strpos($POH_ORG,"�ٹ���آ�Ҿ�Ե�������¹ҷ") !== false) {
				$POH_ORG3 = "�ٹ���آ�Ҿ�Ե�������¹ҷ";
			} elseif (strpos($POH_ORG,"�ͧ�ç��Һ�������Ҥ") !== false) {
				$POH_ORG3 = "�ͧ�ç��Һ�������Ҥ";
			} elseif (strpos($POH_ORG,"�ç��Һ�ź���ȹ�Ҵ��") !== false) {
				$POH_ORG3 = "�ç��Һ�ź���ȹ�Ҵ��";
			} elseif (strpos($POH_ORG,"þ.��ҧ�ͧ") !== false) {
				$POH_ORG3 = "þ.��ҧ�ͧ �ʨ.��ҧ�ͧ";
			} elseif (strpos($POH_ORG,"þ.�ź���") !== false) {
				$POH_ORG3 = "þ.�ź���";
			} elseif (strpos($POH_ORG,"�ç��Һ������Ҫ-�����ո����Ҫ �ʨ.�����ո����Ҫ") !== false || strpos($POH_ORG,"�ç��Һ������Ҫ�����ո����Ҫ �ʨ.�����ո����Ҫ") !== false) {
				$POH_ORG3 = "�ç��Һ������Ҫ�����ո����Ҫ �ʨ.�����ո����Ҫ";
			} elseif (strpos($POH_ORG,"þ.�شøҹ�") !== false) {
				$POH_ORG3 = "þ.�شøҹ�";
			} elseif (strpos($POH_ORG,"þ.��������� �ʨ.�����ä��") !== false) {
				$POH_ORG3 = "þ.��������� �ʨ.�����ä��";
			} elseif (strpos($POH_ORG,"�ٹ���ͧ�ѹ��ФǺ����ä����秨.�شøҹ� ʶҺѹ������") !== false) {
				$POH_ORG3 = "�ٹ���ͧ�ѹ��ФǺ����ä����秨.�شøҹ� ʶҺѹ������";
			} elseif (strpos($POH_ORG,"þ.�ط��Թ�Ҫ ��ɳ��š �ʨ.��ɳ��š") !== false || strpos($POH_ORG,"þ.�ط��Թ�Ҫ �ʨ.��ɳ��š") !== false) {
				$POH_ORG3 = "þ.�ط��Թ�Ҫ ��ɳ��š �ʨ.��ɳ��š";
			} elseif (strpos($POH_ORG,"þ.���ͧ���ԧ���") !== false) {
				$POH_ORG3 = "þ.���ͧ���ԧ��� �ʨ.���ԧ���";
			} elseif (strpos($POH_ORG,"�ͧ�Ҹ�ó�آ�����Ҥ") !== false) {
				$POH_ORG3 = "�ͧ�Ҹ�ó�آ�����Ҥ";
			} elseif (strpos($POH_ORG,"þ.��§��»�Ъҹ������� �ʨ.��§����") !== false) {
				$POH_ORG3 = "þ.��§��»�Ъҹ������� �ʨ.��§����";
			} elseif (strpos($POH_ORG,"þ.�к��") !== false) {
				$POH_ORG3 = "þ.��к��";
			} elseif (strpos($POH_ORG,"þ.˹ͧ᫧ �ʨ.��к���") !== false) {
				$POH_ORG3 = "þ.˹ͧ᫧ �ʨ.��к���";
			} elseif (strpos($POH_ORG,"þ.���þѡ�þ��ҹ �.�������") !== false) {
				$POH_ORG3 = "þ.���þѡ�þ��ҹ �.�������";
			} elseif (strpos($POH_ORG,"þ.������Ҿ����") !== false) {
				$POH_ORG3 = "þ.������Ҿ����";
			} elseif (strpos($POH_ORG,"�ӹѡ�ҹ�Ţҹء�á��") !== false) {
				$POH_ORG3 = "�ӹѡ�ҹ�Ţҹء�á��";
			} elseif (strpos($POH_ORG,"þ.ž���� �.ž����") !== false) {
				$POH_ORG3 = "þ.ž���� �.ž����";
			} elseif (strpos($POH_ORG,"þ.ʡŹ��") !== false) {
				$POH_ORG3 = "þ.ʡŹ��";
			} elseif (strpos($POH_ORG,"þ.˭ԧ") !== false) {
				$POH_ORG3 = "þ.˭ԧ";
			} elseif (strpos($POH_ORG,"�ç��Һ�Ÿѭ���ѡ��") !== false) {
				$POH_ORG3 = "�ç��Һ�Ÿѭ���ѡ��";
			} elseif (strpos($POH_ORG,"ʶҺѹ���ʾ�Դ�ѭ���ѡ��") !== false) {
				$POH_ORG3 = "ʶҺѹ���ʾ�Դ�ѭ���ѡ��";
			} elseif (strpos($POH_ORG,"ʶҺѹ�ѭ���ѡ��") !== false) {
				$POH_ORG3 = "ʶҺѹ�ѭ���ѡ��";
			} elseif (strpos($POH_ORG,"þ. ����Թ���") !== false) {
				$POH_ORG3 = "þ. ����Թ���";
			} elseif (strpos($POH_ORG,"þ.��طû�ҡ��") !== false) {
				$POH_ORG3 = "þ.��طû�ҡ��";
			} elseif (strpos($POH_ORG,"ʶҺѹ��Ҹ��Է��") !== false) {
				$POH_ORG3 = "ʶҺѹ��Ҹ��Է��";
			} elseif (strpos($POH_ORG,"�ӹѡ�ҹ�Ҹ�ó�آ�ѧ��Ѵ��û��") !== false) {
				$POH_ORG3 = "�ӹѡ�ҹ�Ҹ�ó�آ�ѧ��Ѵ��û��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (strpos($POH_ORG,"���˹�ҷ���Һ��") !== false) {
				$POH_ORG3 = "�ͧ������˹�ҷ��";
			} elseif (trim($POH_ORG)) {
				echo "$POH_ORG<br>";
			} 
			$POH_ORG1 = "��з�ǧ�Ҹ�ó�آ";
			$POH_ORG2 = "������ᾷ��";
			if (strpos($POH_ORG,"�ʨ.") !== false || strpos($POH_ORG,"�ӹѡ�ҹ�Ҹ�ó�آ�ѧ��Ѵ") !== false) 
				$POH_ORG2 = "�ӹѡ�ҹ��Ѵ��з�ǧ�Ҹ�ó�آ";
//			$POH_ORG3 = trim($data[DEPT_NAME]);
			$POSITION_FLAG = $SALARY_FLAG = 0;
			if (strpos($DESCRIPTION,"�Թ��͹") !== false || (strpos($DESCRIPTION,"��è�") !== false) && $NEW_FLAG==1) $SALARY_FLAG = 1;
			if ( $SALARY_FLAG == 0) $POSITION_FLAG = 1;
			if ($SALARY_FLAG == 1) {
				$cmd = " SELECT SAH_ID FROM PER_SALARYHIS WHERE PER_ID = $EMPID AND SAH_EFFECTIVEDATE = '$HDATE' AND SAH_SALARY = $SALARY ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd<br>";
				$data1 = $db_dpis->get_array();
				$TMP_SAH_ID = $data1[SAH_ID];

				if ($TMP_SAH_ID > 0) {
//					$i++;
//					echo "$i Update Salary<br>";
//					$cmd = " UPDATE PER_SALARYHIS SET SAH_DOCNO = '$DOCNO' WHERE SAH_ID = $TMP_SAH_ID ";
					$cmd = " ";
				} else { 
					$SAH_ID++;
					$cmd = " INSERT INTO PER_SALARYHIS(SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, SAH_DOCDATE, SAH_ENDDATE, 
							  UPDATE_USER, UPDATE_DATE, SAH_POS_NO, SAH_REMARK, SAH_REMARK2)
							  VALUES ($SAH_ID, $EMPID, '$HDATE', '$MOV_CODE', $SALARY, '$DOCNO', '$DOCDATE', NULL, $UPDATE_USER, '$UPDATE_DATE', '$POSNUM', '$DESCRIPTION', '$REMARK2') ";
				}
				$db_dpis->send_cmd($cmd); 

				$cmd1 = " SELECT SAH_ID FROM PER_SALARYHIS WHERE SAH_ID = $SAH_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
//					echo "$cmd<br>==================<br>";
//					$db_dpis->show_error();
//					echo "<br>end ". ++$i  ."=======================<br>";
				}
			} 
			if ($POSITION_FLAG == 1) {
/*				$cmd = " SELECT POH_ID FROM PER_POSITIONHIS WHERE PER_ID = $EMPID AND POH_EFFECTIVEDATE = '$HDATE' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd<br>";
				$data1 = $db_dpis->get_array();
				$TMP_POH_ID = $data1[POH_ID];

				if ($TMP_POH_ID) {
//					$k++;
//					echo "$k Update Position<br>";
					$cmd = " UPDATE PER_POSITIONHIS SET POH_DOCNO = '$DOCNO', POH_POS_NO = '$POSNUM', LEVEL_NO = '$LEVEL_NO', POH_SALARY = $SALARY, POH_REMARK = '$DESCRIPTION'  WHERE POH_ID = $TMP_POH_ID ";
				} else { */
					$POH_ID++;
					$cmd = " INSERT INTO PER_POSITIONHIS(POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_ENDDATE, POH_DOCNO, POH_DOCDATE, POH_POS_NO, 
							  PM_CODE, LEVEL_NO, POH_LEVEL_NO, PL_CODE, PN_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, POH_ORGMGT, ORG_ID_1, ORG_ID_2, ORG_ID_3, 	
							  POH_UNDER_ORG1, POH_UNDER_ORG2, POH_SALARY, POH_SALARY_POS, POH_REMARK, UPDATE_USER, UPDATE_DATE, POH_ORG1, POH_ORG2, 
							  POH_ORG3, POH_REMARK2, POH_PL_NAME, POH_ORG, ES_CODE)
							  VALUES ($POH_ID, $EMPID, '$HDATE', '$MOV_CODE', NULL, '$DOCNO', '$DOCDATE', '$POSNUM', NULL, 
							  '$LEVEL_NO', '$LEVEL_NO', 
							  NULL, NULL, NULL, '140', NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, $SALARY, 0, '$DESCRIPTION', $UPDATE_USER, '$UPDATE_DATE', '$POH_ORG1', 
							  '$POH_ORG2', '$POH_ORG3', '$REMARK2', '$POH_PL_NAME', '$POH_ORG', '$ES_CODE') ";
//				}
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT POH_ID FROM PER_POSITIONHIS WHERE POH_ID = $POH_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
//					echo "$cmd<br>==================<br>";
//					$db_dpis->show_error();
//					echo "<br>end ". ++$i  ."=======================<br>";
				}
			}              
		} // end while						
	} // end if

	if( $command=='DECORATEHIS' ){ // 23640
		$cmd = " SELECT INS_CODE, INS_NAME FROM DECOR WHERE INS_CODE IN (SELECT DISTINCT ROYAL FROM ROYHIS) ORDER BY INS_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";

		$cmd = " delete from per_decoratehis where update_user = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT MAX(DEH_ID) AS DEH_ID FROM PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$DEH_ID = $data[DEH_ID] + 0;

		$cmd = " SELECT DISTINCT EMPID, ROYAL, ROYDATE FROM ROYHIS WHERE ROYDATE IS NOT NULL ORDER BY EMPID, ROYAL, ROYDATE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$DEH_ID++;
			$EMPID = $data[EMPID];
			$ROYAL = trim($data[ROYAL]);
			$ROYDATE = trim($data[ROYDATE]);
			if ($ROYAL=='17') $DC_CODE = "61";
			elseif ($ROYAL=='16') $DC_CODE = "08";
			elseif ($ROYAL=='15') $DC_CODE = "09";
			elseif ($ROYAL=='14') $DC_CODE = "10";
			elseif ($ROYAL=='13') $DC_CODE = "11";
			elseif ($ROYAL=='12') $DC_CODE = "15";
			elseif ($ROYAL=='11') $DC_CODE = "16";
			elseif ($ROYAL=='10') $DC_CODE = "23";
			elseif ($ROYAL=='09') $DC_CODE = "24";
			elseif ($ROYAL=='08') $DC_CODE = "28";
			elseif ($ROYAL=='07') $DC_CODE = "29";
			elseif ($ROYAL=='06') $DC_CODE = "33";
			elseif ($ROYAL=='05') $DC_CODE = "34";
			elseif ($ROYAL=='04') $DC_CODE = "54";
			elseif ($ROYAL=='03') $DC_CODE = "55";
			elseif ($ROYAL=='02') $DC_CODE = "57";
			elseif ($ROYAL=='01') $DC_CODE = "58";
			if ($ROYAL && !$DC_CODE) echo "����ͧ�Ҫ�� $ROYAL<br>";

			$cmd = " SELECT DEH_DATE FROM PER_DECORATEHIS WHERE PER_ID = $EMPID AND DC_CODE = '$DC_CODE' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$DEH_DATE = $data[DEH_DATE];
			if ($DEH_DATE) {
				$i++;
				echo "$i ����ͧ�Ҫ�� $EMPID - $ROYAL<br>";
			}
				
			$cmd = " INSERT INTO PER_DECORATEHIS(DEH_ID, PER_ID, DC_CODE, DEH_DATE, UPDATE_USER, UPDATE_DATE)
							  VALUES ($DEH_ID, $EMPID, '$DC_CODE', '$ROYDATE', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "<br>";

			$cmd1 = " SELECT DEH_ID FROM PER_DECORATEHIS WHERE DEH_ID = $DEH_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_DECORATEHIS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_DECORATEHIS - $DEH_ID - $COUNT_NEW<br>";
	} // end if

	if( $command=='EDUCATE' ){ //21235
		$cmd = " delete from per_institute where update_user = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT INS_CODE, INS_NAME, CT_CODE, INS_ACTIVE FROM STD_INSTITUTE WHERE INS_CODE IN 
				(SELECT DISTINCT INSCODE FROM EDHIS) ORDER BY INS_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";

		while($data = $db_att->get_array()){
			$INS_CODE = trim($data[INS_CODE]);
			$INS_NAME = trim($data[INS_NAME]);
			$CT_CODE = trim($data[CT_CODE]);
			$INS_ACTIVE = $data[INS_ACTIVE];
			if (!$INS_ACTIVE) $INS_ACTIVE = 1;

			$cmd = " SELECT INS_CODE FROM PER_INSTITUTE WHERE INS_NAME = '$INS_NAME' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "$cmd <br>";
			$data = $db_dpis->get_array();
			$TEMP_INS_CODE = trim($data[INS_CODE]);
			if ($TEMP_INS_CODE && $TEMP_INS_CODE != $INS_CODE) echo "ʶҺѹ����֡�� $INS_NAME $INS_CODE != $TEMP_INS_CODE <br>";

			if (!$TEMP_INS_CODE || $TEMP_INS_CODE != $INS_CODE) {
				$cmd = " INSERT INTO PER_INSTITUTE (INS_CODE, INS_NAME, CT_CODE, INS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						 VALUES ('$INS_CODE', '$INS_NAME', '$CT_CODE', $INS_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd<br>";

				$cmd1 = " SELECT INS_CODE FROM PER_INSTITUTE WHERE INS_CODE = '$INS_CODE' "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
			}
		} // end while						
 
		$cmd = " delete from per_educname where update_user = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT EN_CODE, EL_CODE, EN_SHORTNAME, EN_NAME, EN_ACTIVE FROM STD_DEGREE WHERE EN_CODE IN 
				(SELECT DISTINCT DEGREECODE FROM EDHIS) ORDER BY EN_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";

		while($data = $db_att->get_array()){
			$EN_CODE = trim($data[EN_CODE]);
			$EL_CODE = trim($data[EL_CODE]);
			$EN_SHORTNAME = trim($data[EN_SHORTNAME]);
			$EN_NAME = trim($data[EN_NAME]);
			$EN_ACTIVE = $data[EN_ACTIVE];
			if (!$EN_ACTIVE) $EN_ACTIVE = 1;

			$cmd = " SELECT EN_CODE FROM PER_EDUCNAME WHERE EN_NAME = '$EN_NAME' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "$cmd <br>";
			$data = $db_dpis->get_array();
			$TEMP_EN_CODE = trim($data[EN_CODE]);
			if ($TEMP_EN_CODE && $TEMP_EN_CODE != $EN_CODE) echo "�زԡ���֡�� $EN_NAME $EN_CODE != $TEMP_EN_CODE <br>";

			if (!$TEMP_EN_CODE || $TEMP_EN_CODE != $EN_CODE) {
				$cmd = " INSERT INTO PER_EDUCNAME (EN_CODE, EL_CODE, EN_SHORTNAME, EN_NAME, EN_ACTIVE, UPDATE_USER, UPDATE_DATE)
						 VALUES ('$EN_CODE', '$EL_CODE', '$EN_SHORTNAME', '$EN_NAME', $EN_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd<br>";

				$cmd1 = " SELECT EN_CODE FROM PER_EDUCNAME WHERE EN_CODE = '$EN_CODE' "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
			}
		} // end while						
 
		$cmd = " delete from per_educmajor where update_user = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT EM_CODE, EM_NAME, EM_ACTIVE FROM STD_DEGREEMAJOR WHERE EM_CODE IN (SELECT DISTINCT MAJORCODE FROM EDHIS) ORDER BY EM_CODE ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";

		while($data = $db_att->get_array()){
			$EM_CODE = trim($data[EM_CODE]);
			$EM_NAME = trim($data[EM_NAME]);
			$EM_ACTIVE = $data[EM_ACTIVE];
			if (!$EM_ACTIVE) $EM_ACTIVE = 1;

			$cmd = " SELECT EM_CODE FROM PER_EDUCMAJOR WHERE EM_NAME = '$EM_NAME' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "$cmd <br>";
			$data = $db_dpis->get_array();
			$TEMP_EM_CODE = trim($data[EM_CODE]);
			if ($TEMP_EM_CODE && $TEMP_EM_CODE != $EM_CODE) echo "�Ң��Ԫ��͡ $EM_NAME $EM_CODE != $TEMP_EM_CODE <br>";

			if (!$TEMP_EM_CODE || $TEMP_EM_CODE != $EM_CODE) {
				$cmd = " INSERT INTO PER_EDUCMAJOR (EM_CODE, EM_NAME, EM_ACTIVE, UPDATE_USER, UPDATE_DATE)
						 VALUES ('$EM_CODE', '$EM_NAME', $EM_ACTIVE, $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd<br>";

				$cmd1 = " SELECT EM_CODE FROM PER_EDUCMAJOR WHERE EM_CODE = '$EM_CODE' "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				}
			}
		} // end while						
 
		$cmd = " delete from per_educate where update_user = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT MAX(EDU_ID) AS EDU_ID FROM PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$EDU_ID = $data[EDU_ID] + 0;

		$cmd = " SELECT EID, EORDER, EMPID, INSCODE, BEGINDATE, ENDDATE, DEGREECODE, MAJORCODE, COMPLETEDYR, DEGREEINPOST, ETYPE FROM EDHIS
						  ORDER BY EMPID, EORDER ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$EDU_ID++;
			$EID = $data[EID];
			$EORDER = $data[EORDER];
			$EMPID = $data[EMPID];
			$INSCODE = trim($data[INSCODE]);
			$BEGINDATE = trim($data[BEGINDATE]);
			$ENDDATE = trim($data[ENDDATE]);
			$DEGREECODE = trim($data[DEGREECODE]);
			$MAJORCODE = trim($data[MAJORCODE]);
			$COMPLETEDYR = trim($data[COMPLETEDYR]);
			$DEGREEINPOST = trim($data[DEGREEINPOST]);
			if ($DEGREEINPOST==0) $EDU_TYPE = "2"; else $EDU_TYPE = "3";
			$ETYPE = trim($data[ETYPE]);
			if (!$EORDER || $EORDER > 99) $EORDER = 1;
			if ($COMPLETEDYR=='22524') $COMPLETEDYR = "2524";
			elseif ($COMPLETEDYR=='25333') $COMPLETEDYR = "2533";
			elseif ($COMPLETEDYR=='25330') $COMPLETEDYR = "2530";
			elseif ($COMPLETEDYR=='25148') $COMPLETEDYR = "2548";
			elseif ($COMPLETEDYR=='25544') $COMPLETEDYR = "2544";
			elseif ($COMPLETEDYR=='25552') $COMPLETEDYR = "2552";
			elseif ($COMPLETEDYR=='25497') $COMPLETEDYR = "2549";
			elseif ($COMPLETEDYR=='25576') $COMPLETEDYR = "2556";
			elseif ($COMPLETEDYR=='25546') $COMPLETEDYR = "2546";
			elseif ($COMPLETEDYR=='25549') $COMPLETEDYR = "2549";
			if (!$BEGINDATE) $BEGINDATE = $COMPLETEDYR;
			if (!$ENDDATE) $ENDDATE = $BEGINDATE;
			if ($INSCODE=='1402149') $INSCODE = "1402119";

			$cmd = " INSERT INTO PER_EDUCATE(EDU_ID, PER_ID, EDU_SEQ, EDU_STARTYEAR, EDU_ENDYEAR, ST_CODE, CT_CODE, EDU_FUND, EN_CODE, EM_CODE, 
							  INS_CODE, EDU_TYPE, UPDATE_USER, UPDATE_DATE)
							  VALUES ($EDU_ID, $EMPID, $EORDER, '$BEGINDATE', '$ENDDATE', NULL, NULL, NULL, '$DEGREECODE', '$MAJORCODE', '$INSCODE', '$EDU_TYPE', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "<br>";

			$cmd1 = " SELECT EDU_ID FROM PER_EDUCATE WHERE EDU_ID = $EDU_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}
		} // end while						

		$cmd = " select count(PER_ID) as COUNT_NEW from PER_EDUCATE ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_EDUCATE - $EDU_ID - $COUNT_NEW<br>";
	} // end if

	if( $command=='CHECK' ){
		$cmd = " SELECT EMPID, A.POSNUM, TITLE, FNAME, LNAME, REALDIVCODE, REALSECTCODE, REALSUBSECTCODE, SEX, RELIGION, BIRTH, HOMEPROV, 
						  MARITAL, ENTRYCS, ENTRYDP, LEVEL_NO, PROMDATE, SALARY, FLOWDATE, A.ENTRDATE, IDNO, 
						  GROUPFLOW, FLOW, ADDRESS1, ADDRESS2, PROVINCE, TELEPHONE, COUNTRY, VACATION, FATHERNAME, 
						  MOTHERNAME, SPOUSENAME, HISTORYID, TAXID 
						  FROM PERSON A, POST B
						  WHERE A.POSNUM = B.POSNUM
						  ORDER BY CINT(A.POSNUM) ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$EMPID = $data[EMPID];
			$POSNUM = trim($data[POSNUM]);
			$cmd = " SELECT POS_ID FROM PER_PERSONAL WHERE POS_ID = $POSNUM ";
			$count_data = $db_dpis->send_cmd($cmd);
			if (!$count_data) echo "xxxxxxxxxxxxxxx $POSNUM <br>";
		} // end while					

		$cmd = " SELECT EMPID, A.POSNUM, TITLE, FNAME, LNAME, REALDIVCODE, REALSECTCODE, REALSUBSECTCODE, SEX, RELIGION, BIRTH, HOMEPROV, 
						  MARITAL, ENTRYCS, ENTRYDP, LEVEL_NO, PROMDATE, SALARY, FLOWDATE, A.ENTRDATE, IDNO, 
						  GROUPFLOW, FLOW, ADDRESS1, ADDRESS2, PROVINCE, TELEPHONE, COUNTRY, VACATION, FATHERNAME, 
						  MOTHERNAME, SPOUSENAME, HISTORYID, TAXID 
						  FROM PERSON A, POST B
						  WHERE A.POSNUM = B.POSNUM
						  ORDER BY CINT(A.POSNUM) ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$FNAME = trim($data[FNAME]);
			$LNAME = trim($data[LNAME]);
			$cmd = " SELECT PER_ID FROM PER_PERSONAL WHERE PER_NAME = '$FNAME' AND PER_SURNAME = '$LNAME' ";
			$count_data = $db_dpis->send_cmd($cmd);
//			echo $cmd;
			if (!$count_data) echo "xxxxxxxxxxxxxxx $FNAME $LNAME <br>";
		} // end while					

		$cmd = " SELECT FNAME, LNAME FROM EXPERS ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$FNAME = trim($data[FNAME]);
			$LNAME = trim($data[LNAME]);
			$cmd = " SELECT PER_ID FROM PER_PERSONAL WHERE PER_NAME = '$FNAME' AND PER_SURNAME = '$LNAME' ";
			$count_data = $db_dpis->send_cmd($cmd);
//			echo $cmd;
			if (!$count_data) echo "xxxxxxxxxxxxxxx $FNAME $LNAME <br>";
		} // end while					

		$cmd = " SELECT EMPID, ROYAL, ROYDATE FROM ROYHIS ORDER BY EMPID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$EMPID = $data[EMPID];
			$ROYAL = trim($data[ROYAL]);
			$ROYDATE = trim($data[ROYDATE]);
			$cmd = " SELECT PER_ID FROM PER_PERSONAL WHERE PER_ID = $EMPID ";
			$count_data = $db_dpis->send_cmd($cmd);
//			if (!$count_data) echo "xxxxxxxxxxxxxxx $EMPID <br>";
		} // end while					

		$cmd = " SELECT EMPID FROM TRAININGHIS ORDER BY EMPID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$EMPID = $data[EMPID];
			$cmd = " SELECT PER_ID FROM PER_PERSONAL WHERE PER_ID = $EMPID ";
			$count_data = $db_dpis->send_cmd($cmd);
//			if (!$count_data) echo "xxxxxxxxxxxxxxx $EMPID <br>";
		} // end while					
	} // end if

	if( $command=='TRAIN' ){ // 838 776
		$cmd = " delete from per_training where update_user = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from per_train where update_user = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT DISTINCT TRAININGNAME FROM TRAININGHIS ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$TRAININGNAME = trim($data[TRAININGNAME]);
			$ID++;

			$cmd = " INSERT INTO PER_TRAIN(TR_CODE, TR_TYPE, TR_NAME, TR_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('$ID', 1, '$TRAININGNAME', 1, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "<br>";
		} // end while			
		
		$cmd = " SELECT MAX(TRN_ID) AS TRN_ID FROM PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$TRN_ID = $data[TRN_ID] + 0;

		$cmd = " SELECT EID, EMPID, EORDER, ETYPE, TRAININGID, BUDGETYEAR, TRAININGNAME, OWNER, INTERVAL, STARTDATE, ENDDATE, INTERVALUNIT, REMARK
						  FROM TRAININGHIS
						  ORDER BY EMPID, EORDER ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$TRN_ID++;
			$EID = $data[EID];
			$EMPID = $data[EMPID];
			$EORDER = $data[EORDER];
			$ETYPE = trim($data[ETYPE]);
			$TRAININGID = trim($data[TRAININGID]);
			$BUDGETYEAR = trim($data[BUDGETYEAR]);
			$TRAININGNAME = trim($data[TRAININGNAME]);
			$OWNER = trim($data[OWNER]);
			$INTERVAL = trim($data[INTERVAL]);
			$STARTDATE = trim($data[STARTDATE]);
			$ENDDATE = trim($data[ENDDATE]);
			$INTERVALUNIT = trim($data[INTERVALUNIT]);
			$REMARK = trim($data[REMARK]);
			if (!$STARTDATE) $STARTDATE = "-";
			if (!$ENDDATE) $ENDDATE = "-";

			$TR_CODE = "";
			$cmd = " SELECT TR_CODE FROM PER_TRAIN WHERE TR_NAME = '$TRAININGNAME' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$TR_CODE = $data[TR_CODE];
			if ($TRAININGNAME && !$TR_CODE) echo "�֡ͺ�� $TRAININGNAME<br>";
				
			$cmd = " SELECT PER_NAME FROM PER_PERSONAL WHERE PER_ID = $EMPID ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$PER_NAME = $data[PER_NAME];
			if (!$PER_NAME) {
				$i++;
				echo "$i �֡ͺ�� $EMPID<br>";
			}
				
			$cmd = " INSERT INTO PER_TRAINING(TRN_ID, PER_ID, TRN_TYPE, TR_CODE, TRN_STARTDATE, TRN_ENDDATE, TRN_ORG, TRN_PLACE, UPDATE_USER, 
							  UPDATE_DATE)
					 VALUES ($TRN_ID, $EMPID, 1, '$TR_CODE', '$STARTDATE', '$ENDDATE', '$OWNER', NULL, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			echo "<br>";
		} // end while				
	} // end if

/*
			$INS_CODE = "";
			if ($INSCODE=='1100020') $INS_CODE = "1100020"; // BANARAS HINDU UNIVERSITY
			elseif ($INSCODE=='1100172') $INS_CODE = "1100172"; // -
			elseif ($INSCODE=='1150307') $INS_CODE = "1150307"; // OSAKA CITY UNIVERSITY
			elseif ($INSCODE=='1150422') $INS_CODE = "1150422"; // TOHOKU UNIVERSITY
			elseif ($INSCODE=='1150446') $INS_CODE = "1150446"; // TOKYO MEDICAL AND DENTAL UNIVERSITY
			elseif ($INSCODE=='1300074') $INS_CODE = "1300074"; // Manila Central University
			elseif ($INSCODE=='1300393') $INS_CODE = "1300393"; // Bicol Christian College of Medicine
			elseif ($INSCODE=='1300520') $INS_CODE = "1300520"; // Cebu Doctors' College of Medicine
			elseif ($INSCODE=='1300547') $INS_CODE = "1300547"; // Southwestern University , Philipines
			elseif ($INSCODE=='1300583') $INS_CODE = "1300583"; // Divine Word University of Tacloban
			elseif ($INSCODE=='1300790') $INS_CODE = "1300790"; // Remedios T. Romualdez Medical Foundation
			elseif ($INSCODE=='1300801') $INS_CODE = "1300801"; // Saint Louis University
			elseif ($INSCODE=='1400001') $INS_CODE = "1400001"; // ����ŧ�ó�����Է�����
			elseif ($INSCODE=='1400002') $INS_CODE = "1400002"; // ����Է������ɵ���ʵ��
			elseif ($INSCODE=='1400003') $INS_CODE = "1400003"; // ����Է����¢͹��
			elseif ($INSCODE=='1400004') $INS_CODE = "1400004"; // ����Է�������§����
			elseif ($INSCODE=='1400005') $INS_CODE = "1400005"; // ����Է����¸�����ʵ��
			elseif ($INSCODE=='1400006') $INS_CODE = "1400006"; // ����Է����¹�����
			elseif ($INSCODE=='1400007') $INS_CODE = "1400007"; // ����Է����º�þ�
			elseif ($INSCODE=='1400008') $INS_CODE = "1400008"; // ����Է�������Դ�
			elseif ($INSCODE=='1400009') $INS_CODE = "1400009"; // ����Է�����������˧
			elseif ($INSCODE=='1400010') $INS_CODE = "1400010"; // ����Է�������չ��Թ�����ò
			elseif ($INSCODE=='1400011') $INS_CODE = "1400011"; // ����Է�������Żҡ�
			elseif ($INSCODE=='1400012') $INS_CODE = "1400012"; // ����Է�����ʧ��ҹ��Թ���
			elseif ($INSCODE=='1400013') $INS_CODE = "1400013"; // ����Է�������⢷�¸���Ҹ��Ҫ
			elseif ($INSCODE=='1400014') $INS_CODE = "1400014"; // ����Է������غ��Ҫ�ҹ�
			elseif ($INSCODE=='1400016') $INS_CODE = "1400016"; // ʶҺѹ෤����ա���ɵ������/����Է����
			elseif ($INSCODE=='1400018') $INS_CODE = "1400018"; // ʶҺѹ෤����վ�Ш������ ������
			elseif ($INSCODE=='1400019') $INS_CODE = "1400019"; // ʶҺѹ෤����վ�Ш������ ��й���˹��
			elseif ($INSCODE=='1400020') $INS_CODE = "1400020"; // ʶҺѹ�ѳ�Ե�Ѳ���������ʵ��
			elseif ($INSCODE=='1400021') $INS_CODE = "1400021"; // ᾷ����
			elseif ($INSCODE=='1400022') $INS_CODE = "1400022"; // ��Ҩ���ŧ�ó�Ҫ�Է����� 㹾�к���Ҫٻ���
			elseif ($INSCODE=='1400025') $INS_CODE = "1400025"; // ����Է����������ä��
			elseif ($INSCODE=='1400027') $INS_CODE = "1400027"; // ����Է����������ѡɳ�
			elseif ($INSCODE=='1400031') $INS_CODE = "1400031"; // ������Ѫ����
			elseif ($INSCODE=='1400032') $INS_CODE = "1400032"; // ��ҡ�þ�Һ��
			elseif ($INSCODE=='1400033') $INS_CODE = "1400033"; // �Ҫ�Է����������ᾷ����觻������ 㹾�к���Ҫٻ�����
			elseif ($INSCODE=='1401001') $INS_CODE = "1401001"; // ����Է����¡�ا෾
			elseif ($INSCODE=='1401002') $INS_CODE = "1401002"; // ����Է�������ԡ
			elseif ($INSCODE=='1401003') $INS_CODE = "1401003"; // ����Է����¸�áԨ�ѳ�Ե��
			elseif ($INSCODE=='1401004') $INS_CODE = "1401004"; // ����Է�������ջ��� �Է��ࢵ�ź���
			elseif ($INSCODE=='1401005') $INS_CODE = "1401005"; // ����Է������͡�ä����
			elseif ($INSCODE=='1401006') $INS_CODE = "1401006"; // ����Է������������ѭ �Է��ࢵ�ҧ��
			elseif ($INSCODE=='1401007') $INS_CODE = "1401007"; // ����Է�����������Ҥ���
			elseif ($INSCODE=='1401008') $INS_CODE = "1401008"; // ����Է���������
			elseif ($INSCODE=='1401009') $INS_CODE = "1401009"; // ����Է����¾��Ѿ
			elseif ($INSCODE=='1401011') $INS_CODE = "1401011"; // ����Է����������������������õ� �Է��
			elseif ($INSCODE=='1401013') $INS_CODE = "1401013"; // ����Է����¤�����¹
			elseif ($INSCODE=='1401015') $INS_CODE = "1401015"; // ����Է������ѧ�Ե
			elseif ($INSCODE=='1401016') $INS_CODE = "1401016"; // �Է�����ૹ��������
			elseif ($INSCODE=='1401018') $INS_CODE = "1401018"; // �Է������Ԫ���
			elseif ($INSCODE=='1401019') $INS_CODE = "1401019"; // ����Է���������ѳ�Ե
			elseif ($INSCODE=='1401020') $INS_CODE = "1401020"; // ����Է������¹�
			elseif ($INSCODE=='1401022') $INS_CODE = "1401022"; // ����Է�����ૹ�����
			elseif ($INSCODE=='1401025') $INS_CODE = "1401025"; // �Է�����෤������Ҫ�ҹ�
			elseif ($INSCODE=='1401028') $INS_CODE = "1401028"; // ����Է�����������������
			elseif ($INSCODE=='1401031') $INS_CODE = "1401031"; // ����Է����³��Ѳ��
			elseif ($INSCODE=='1401047') $INS_CODE = "1401047"; // �Է����»�ظҹ�
			elseif ($INSCODE=='1401052') $INS_CODE = "1401052"; // �Է����¡�ا෾������
			elseif ($INSCODE=='1402101') $INS_CODE = "1402101"; // ʶҺѹ�Ҫ�ѯ��§����
			elseif ($INSCODE=='1402103') $INS_CODE = "1402103"; // ʶҺѹ�Ҫ�ѯ�ӻҧ
			elseif ($INSCODE=='1402104') $INS_CODE = "1402104"; // ʶҺѹ�Ҫ�ѯ�صôԵ��
			elseif ($INSCODE=='1402107') $INS_CODE = "1402107"; // ʶҺѹ�Ҫ�ѯ������ä�
			elseif ($INSCODE=='1402108') $INS_CODE = "1402108"; // ʶҺѹ�Ҫ�ѯྪú�ó�
			elseif ($INSCODE=='1402109') $INS_CODE = "1402109"; // ʶҺѹ�Ҫ�ѯ�شøҹ�
			elseif ($INSCODE=='1402111') $INS_CODE = "1402111"; // ʶҺѹ�Ҫ�ѯ���
			elseif ($INSCODE=='1402113') $INS_CODE = "1402113"; // ʶҺѹ�Ҫ�ѯ����Ҫ����
			elseif ($INSCODE=='1402114') $INS_CODE = "1402114"; // ʶҺѹ�Ҫ�ѯ���������
			elseif ($INSCODE=='1402115') $INS_CODE = "1402115"; // ʶҺѹ�Ҫ�ѯ���Թ���
			elseif ($INSCODE=='1402116') $INS_CODE = "1402116"; // ʶҺѹ�Ҫ�ѯ�غ��Ҫ�ҹ�
			elseif ($INSCODE=='1402117') $INS_CODE = "1402117"; // ʶҺѹ�Ҫ�ѯ��й�������ظ��
			elseif ($INSCODE=='1402119') $INS_CODE = "1402119"; // ʶҺѹ�Ҫ�ѯ�Ҫ���Թ��� ���ԧ���
			elseif ($INSCODE=='1402120') $INS_CODE = "1402120"; // ʶҺѹ�Ҫ�ѯ෾ʵ�� ž����
			elseif ($INSCODE=='1402121') $INS_CODE = "1402121"; // ʶҺѹ�Ҫ�ѯྪú����Է��ŧ�ó�
			elseif ($INSCODE=='1402122') $INS_CODE = "1402122"; // ʶҺѹ�Ҫ�ѯྪú���
			elseif ($INSCODE=='1402123') $INS_CODE = "1402123"; // ʶҺѹ�Ҫ�ѯ�ҭ������
			elseif ($INSCODE=='1402124') $INS_CODE = "1402124"; // ʶҺѹ�Ҫ�ѯ��û��
			elseif ($INSCODE=='1402125') $INS_CODE = "1402125"; // ʶҺѹ�Ҫ�ѯ�����ҹ����֧ �Ҫ����
			elseif ($INSCODE=='1402126') $INS_CODE = "1402126"; // ʶҺѹ�Ҫ�ѯ����ɮ��ҹ�
			elseif ($INSCODE=='1402128') $INS_CODE = "1402128"; // ʶҺѹ�Ҫ�ѯ����
			elseif ($INSCODE=='1402129') $INS_CODE = "1402129"; // ʶҺѹ�Ҫ�ѯʧ���
			elseif ($INSCODE=='1402130') $INS_CODE = "1402130"; // ʶҺѹ�Ҫ�ѯ����
			elseif ($INSCODE=='1402131') $INS_CODE = "1402131"; // ʶҺѹ�Ҫ�ѯ�ǹ�عѹ��
			elseif ($INSCODE=='1402132') $INS_CODE = "1402132"; // ʶҺѹ�Ҫ�ѯ�ǹ���Ե
			elseif ($INSCODE=='1402133') $INS_CODE = "1402133"; // ʶҺѹ�Ҫ�ѯ�ѹ�����
			elseif ($INSCODE=='1402134') $INS_CODE = "1402134"; // ʶҺѹ�Ҫ�ѯ��й��
			elseif ($INSCODE=='1402135') $INS_CODE = "1402135"; // ʶҺѹ�Ҫ�ѯ������
			elseif ($INSCODE=='1402136') $INS_CODE = "1402136"; // ʶҺѹ�Ҫ�ѯ��ҹ������Ҿ����
			elseif ($INSCODE=='1402142') $INS_CODE = "1402142"; // ����Է������Ҫ�ѯ����ŧ�ó�
			elseif ($INSCODE=='1402200') $INS_CODE = "1402200"; // ʶҺѹ����֡����ѧ�ѴʶҺѹ෤������Ҫ����
			elseif ($INSCODE=='1402204') $INS_CODE = "1402204"; // ʶҺѹ෤������Ҫ�����Է��ࢵ෤�Ԥ��ا෾
			elseif ($INSCODE=='1402206') $INS_CODE = "1402206"; // ʶҺѹ෤������Ҫ�����Է��ࢵ��Եþ��آ �ѡ���ô�
			elseif ($INSCODE=='1402208') $INS_CODE = "1402208"; // ʶҺѹ෤������Ҫ�����Է��ࢵ�ѡþ����ǹҷ
			elseif ($INSCODE=='1402210') $INS_CODE = "1402210"; // ʶҺѹ෤������Ҫ�����Է��ࢵ��й����
			elseif ($INSCODE=='1402218') $INS_CODE = "1402218"; // ʶҺѹ෤������Ҫ�����Է��ࢵ��ɳ��š
			elseif ($INSCODE=='1402219') $INS_CODE = "1402219"; // ʶҺѹ෤������Ҫ�����Է��ࢵ�����ҹ�
			elseif ($INSCODE=='1402222') $INS_CODE = "1402222"; // ʶҺѹ෤������Ҫ�����Է��ࢵ��й�������ظ�� �ѹ���
			elseif ($INSCODE=='1402223') $INS_CODE = "1402223"; // ʶҺѹ෤������Ҫ�����Է��ࢵ��й�������ظ�� ���ء��
			elseif ($INSCODE=='1402224') $INS_CODE = "1402224"; // ʶҺѹ෤������Ҫ�����Է��ࢵ�ؾ�ó����
			elseif ($INSCODE=='1402233') $INS_CODE = "1402233"; // ʶҺѹ෤������Ҫ�����Է��ࢵ����Թ���
			elseif ($INSCODE=='1402239') $INS_CODE = "1402239"; // ʶҺѹ෤������Ҫ�����Է��ࢵ�Ҥ��
			elseif ($INSCODE=='1402241') $INS_CODE = "1402241"; // ʶҺѹ෤������Ҫ�����Է��ࢵ�����ո����Ҫ
			elseif ($INSCODE=='1402242') $INS_CODE = "1402242"; // ʶҺѹ෤������Ҫ�����Է��ࢵ�ѭ����
			elseif ($INSCODE=='1402300') $INS_CODE = "1402300"; // ʶҺѹ����֡����ѧ�Ѵ��������ʹ�
			elseif ($INSCODE=='1402401003') $INS_CODE = "1402401003"; // �Է������Ҫ���֡����§����
			elseif ($INSCODE=='1402401010') $INS_CODE = "1402401010"; // �Է�����෤�Ԥ�Ӿٹ
			elseif ($INSCODE=='1402401015') $INS_CODE = "1402401015"; // �Է������Ҫ���֡�����
			elseif ($INSCODE=='1402401020') $INS_CODE = "1402401020"; // �Է�����෤�Ԥ��ҹ *
			elseif ($INSCODE=='1402401035') $INS_CODE = "1402401035"; // �Է�����෤�Ԥ�����
			elseif ($INSCODE=='1402401040') $INS_CODE = "1402401040"; // �Է������Ҫ���֡���ӻҧ
			elseif ($INSCODE=='1402401045') $INS_CODE = "1402401045"; // �Է������Ҫ���֡����⢷��
			elseif ($INSCODE=='1402401058') $INS_CODE = "1402401058"; // �Է�����෤�Ԥ��ɳ��š
			elseif ($INSCODE=='1402401064') $INS_CODE = "1402401064"; // �Է�����෤�Ԥ�ԨԵ�
			elseif ($INSCODE=='1402401068') $INS_CODE = "1402401068"; // �Է�����෤�Ԥྪú�ó�
			elseif ($INSCODE=='1402402019') $INS_CODE = "1402402019"; // �Է������Ҫ���֡���شøҹ�
			elseif ($INSCODE=='1402402031') $INS_CODE = "1402402031"; // �Է�����෤�Ԥ��þ��  *
			elseif ($INSCODE=='1402402057') $INS_CODE = "1402402057"; // �Է������Ҫ���֡���������
			elseif ($INSCODE=='1402402065') $INS_CODE = "1402402065"; // �Է�����෤�Ԥ��ʸ�
			elseif ($INSCODE=='1402402068') $INS_CODE = "1402402068"; // �Է�����෤�Ԥ�غ��Ҫ�ҹ�
			elseif ($INSCODE=='1402402070') $INS_CODE = "1402402070"; // �Է������Ҫ���֡���غ��Ҫ�ҹ�
			elseif ($INSCODE=='1402402076') $INS_CODE = "1402402076"; // �Է�����෤�Ԥ���������
			elseif ($INSCODE=='1402402082') $INS_CODE = "1402402082"; // �Է�����෤�Ԥ���Թ��� *
			elseif ($INSCODE=='1402402089') $INS_CODE = "1402402089"; // �Է�����෤�Ԥ�������
			elseif ($INSCODE=='1402402099') $INS_CODE = "1402402099"; // �Է������Ҫ���֡�ҹ���Ҫ����
			elseif ($INSCODE=='1402402107') $INS_CODE = "1402402107"; // �Է�����෤�Ԥ�������
			elseif ($INSCODE=='1402403006') $INS_CODE = "1402403006"; // �Է�����෤�Ԥ���� *
			elseif ($INSCODE=='1402403013') $INS_CODE = "1402403013"; // �Է������Ҫ���֡�һѵ�ҹ�
			elseif ($INSCODE=='1402403023') $INS_CODE = "1402403023"; // �Է����¡���Ҫվ�ҧ���
			elseif ($INSCODE=='1402403027') $INS_CODE = "1402403027"; // �Է������Ҫ���֡��ʧ���
			elseif ($INSCODE=='1402403037') $INS_CODE = "1402403037"; // �Է�����෤�Ԥ�����
			elseif ($INSCODE=='1402403045') $INS_CODE = "1402403045"; // �Է������Ҫ���֡������ɮ��ҹ� *
			elseif ($INSCODE=='1402403054') $INS_CODE = "1402403054"; // �Է������Ҫ���֡�ҹ����ո����Ҫ
			elseif ($INSCODE=='1402404002') $INS_CODE = "1402404002"; // �Է������Ҫ���֡��ྪú���
			elseif ($INSCODE=='1402404007') $INS_CODE = "1402404007"; // �Է�����෤�Ԥ��ШǺ���բѹ��
			elseif ($INSCODE=='1402404011') $INS_CODE = "1402404011"; // �Է�����෤�Ԥ�Ҫ���� *
			elseif ($INSCODE=='1402404018') $INS_CODE = "1402404018"; // �Է�����෤�Ԥ��¹ҷ
			elseif ($INSCODE=='1402404021') $INS_CODE = "1402404021"; // �Է�����෤�Ԥ�ط�¸ҹ�
			elseif ($INSCODE=='1402404027') $INS_CODE = "1402404027"; // �Է������Ҫ���֡�ҹ�����ä� *
			elseif ($INSCODE=='1402404039') $INS_CODE = "1402404039"; // �Է�����෤�Ԥ��й�������ظ�� *
			elseif ($INSCODE=='1402404042') $INS_CODE = "1402404042"; // �Է������Ҫ���֡�Ҿ�й�������ظ��
			elseif ($INSCODE=='1402404047') $INS_CODE = "1402404047"; // �Է�����෤�Ԥž����
			elseif ($INSCODE=='1402404068') $INS_CODE = "1402404068"; // �Է������Ҫ���֡���ؾ�ó����
			elseif ($INSCODE=='1402404076') $INS_CODE = "1402404076"; // �Է�����෤�Ԥ��û��
			elseif ($INSCODE=='1402404077') $INS_CODE = "1402404077"; // �Է������Ҫ���֡�ҹ�û��
			elseif ($INSCODE=='1402404082') $INS_CODE = "1402404082"; // �Է�����෤�Ԥ��ط��Ҥ� *
			elseif ($INSCODE=='1402405004') $INS_CODE = "1402405004"; // �Է�����෤�Ԥ��Ҩչ����
			elseif ($INSCODE=='1402405020') $INS_CODE = "1402405020"; // �Է������Ҫ���֡�Ҫź���
			elseif ($INSCODE=='1402405030') $INS_CODE = "1402405030"; // �Է�����෤�Ԥ��Ҵ
			elseif ($INSCODE=='1402406003') $INS_CODE = "1402406003"; // �Է����¾�Ԫ¡�úҧ��
			elseif ($INSCODE=='1402406020') $INS_CODE = "1402406020"; // �Է����¾�Ԫ¡�ø�����
			elseif ($INSCODE=='1402406021') $INS_CODE = "1402406021"; // �Է����¾�Ԫ¡��વؾ�
			elseif ($INSCODE=='1402406023') $INS_CODE = "1402406023"; // �Է������Ҫ���֡�Ҹ�����
			elseif ($INSCODE=='1402406030') $INS_CODE = "1402406030"; // �Է�����෤���������Ҫ���֡��
			elseif ($INSCODE=='1402406031') $INS_CODE = "1402406031"; // �Է�����෤�Ԥ��ا෾
			elseif ($INSCODE=='1402512003') $INS_CODE = "1402512003"; // �ç���¹��պح�ҹ���
			elseif ($INSCODE=='1403003') $INS_CODE = "1403003"; // ��з�ǧ������
			elseif ($INSCODE=='1403011') $INS_CODE = "1403011"; // ���ᾷ������ҡ��
			elseif ($INSCODE=='1403012') $INS_CODE = "1403012"; // �ç���¹��Һ�ŷ����ҡ��
			elseif ($INSCODE=='1403013') $INS_CODE = "1403013"; // �ç���¹��Һ�ż�ا����� ���͹������觡ͧ
			elseif ($INSCODE=='1403015') $INS_CODE = "1403015"; // ���ᾷ���������
			elseif ($INSCODE=='1403017') $INS_CODE = "1403017"; // �Է�����ᾷ���ʵ�� ������د���� �ͧ�Ѿ�
			elseif ($INSCODE=='1403034') $INS_CODE = "1403034"; // ����Է����ʵ����ᾷ��
			elseif ($INSCODE=='1403036') $INS_CODE = "1403036"; // �ӹѡ�ҹ��Ѵ��з�ǧ�Ҹ�ó�آ
			elseif ($INSCODE=='1403037') $INS_CODE = "1403037"; // ������ᾷ��
			elseif ($INSCODE=='1403040') $INS_CODE = "1403040"; // �Է����¾�Һ����ҡҪҴ��
			elseif ($INSCODE=='1403043') $INS_CODE = "1403043"; // Ǫ�þ�Һ��
			elseif ($INSCODE=='1403050') $INS_CODE = "1403050"; // �ç���¹��Һ�� ��ا��������͹���� �ç���
			elseif ($INSCODE=='1403051') $INS_CODE = "1403051"; // �Է����¾�Һ�����͡��س�� ��ا෾��ҹ��
			elseif ($INSCODE=='1403059') $INS_CODE = "1403059"; // �ç���¹�ѧ��෤�Ԥ�ç��Һ�Ũ���ŧ�ó� �
			elseif ($INSCODE=='2330027') $INS_CODE = "2330027"; // UNIVERSITY OF LONDON
			elseif ($INSCODE=='2330102') $INS_CODE = "2330102"; // UNIVERSITY COLLEGE LONDON,UNIVERSITY OF
			elseif ($INSCODE=='4010097') $INS_CODE = "4010097"; // St.THOMAS UNIVERSITY
			elseif ($INSCODE=='4021238') $INS_CODE = "4021238"; // INDIANA UNIVERSITY-PURDUE UNIVERSITY AT INDIANAPOLIS
			elseif ($INSCODE=='4021977') $INS_CODE = "4021977"; // NORTH DAKOTA STATE UNIVERSITY
			elseif ($INSCODE=='4022563') $INS_CODE = "4022563"; // SOUTHWESTERN UNIVERSITY
			elseif ($INSCODE=='4023011') $INS_CODE = "4023011"; // UNIVERSITY OF CALIFORNIA, LOS ANGELES
			elseif ($INSCODE=='7010019') $INS_CODE = "7010019"; // THE UNIVERSITY OF QUEENSLAND
			elseif ($INSCODE=='7010032') $INS_CODE = "7010032"; // CURTIN UNIVERSITY OF TECHNOLOGY
			elseif ($INSCODE=='7080004') $INS_CODE = "7080004"; // UNIVERSITY OF OTAGO
			$INS_CODE = (trim($INS_CODE))? "'".trim($INS_CODE)."'" : "NULL";

			$EN_CODE = "";
			if ($DEGREECODE=='0010000') $EN_CODE = "0010000"; // �ز���� � �繡�����زԷ��˹��§ҹ��Ե��������੾�е��˹�
			elseif ($DEGREECODE=='0010001') $EN_CODE = "0010001"; // ๵Ժѳ�Ե��
			elseif ($DEGREECODE=='0010002') $EN_CODE = "0010002"; // �͹حҵ��Сͺ�ä��Ż��Ңҷѹ�����
			elseif ($DEGREECODE=='0010003') $EN_CODE = "0010003"; // �͹حҵ��Сͺ�ä��Ż��Ң�෤�Ԥ���ᾷ��
			elseif ($DEGREECODE=='0010004') $EN_CODE = "0010004"; // �͹حҵ��Сͺ�ԪҪվ��þ�Һ��
			elseif ($DEGREECODE=='0010005') $EN_CODE = "0010005"; // �͹حҵ��Сͺ�ԪҪվ��ا������� 1
			elseif ($DEGREECODE=='0010006') $EN_CODE = "0010006"; // �͹حҵ��Сͺ�ԪҪվ�Ңҡ���Ҿ�ӺѴ
			elseif ($DEGREECODE=='0010007') $EN_CODE = "0010007"; // �͹حҵ��Сͺ�ԪҪվ�Ңҡ�þ�Һ����м�ا������� 1
			elseif ($DEGREECODE=='0010008') $EN_CODE = "0010008"; // �͹حҵ��Сͺ�ԪҪվ�Ң����Ѫ������� 1
			elseif ($DEGREECODE=='0010009') $EN_CODE = "0010009"; // �͹حҵ��Сͺ�ԪҪվ�Ң��Ǫ����
			elseif ($DEGREECODE=='0010075') $EN_CODE = "0010075"; // ��.��Һ���Ǫ��ԺѵԷ����
			elseif ($DEGREECODE=='0010087') $EN_CODE = "0010087"; // ��.���ѭ�վ�Һ��
			elseif ($DEGREECODE=='0010091') $EN_CODE = "0010091"; // �͹حҵ��Сͺ�ԪҪվ�Ңҡ�þ�Һ����м�ا������� 2
			elseif ($DEGREECODE=='0010092') $EN_CODE = "0010092"; // �͹حҵ�繼���Сͺ�ä��Ż� �Ң��ѧ��෤�Ԥ
			elseif ($DEGREECODE=='0010093') $EN_CODE = "0010093"; // �͹حҵ��Сͺ�ä��Ż��ҢҨԵ�Է�Ҥ�Թԡ
			elseif ($DEGREECODE=='0010094') $EN_CODE = "0010094"; // �͹حҵ��Сͺ�ä��Ż��Ңҡ��ᾷ��Ἱ�»�������ü�ا�������
			elseif ($DEGREECODE=='0010095') $EN_CODE = "0010095"; // �͹حҵ��Сͺ�ä��Ż��ҢҡԨ�����ӺѴ
			elseif ($DEGREECODE=='0010097') $EN_CODE = "0010097"; // �͹حҵ��Сͺ����ԪҪվ��þ�Һ�� ��� 1
			elseif ($DEGREECODE=='0510000') $EN_CODE = "0510000"; // �زԵ�ӡ��һ�.�ԪҪվ (�Ǫ.)
			elseif ($DEGREECODE=='0510002') $EN_CODE = "0510002"; // ����֡�Ҽ���˭��дѺ 4 (�.3 , ��.3)
			elseif ($DEGREECODE=='0510003') $EN_CODE = "0510003"; // ����֡�Ҽ���˭��дѺ 5 (�.6 , ��.5)
			elseif ($DEGREECODE=='0510004') $EN_CODE = "0510004"; // �ѡ�����͡
			elseif ($DEGREECODE=='0510005') $EN_CODE = "0510005"; // ��.��þ�Һ����м�ا�����(�дѺ��)
			elseif ($DEGREECODE=='0510006') $EN_CODE = "0510006"; // ��.��پ���ɡ���֡��
			elseif ($DEGREECODE=='0510008') $EN_CODE = "0510008"; // ��.��Ҿ�ѡ�ҹ�Է����ʵ����ᾷ�� (�շ�� 1)
			elseif ($DEGREECODE=='0510009') $EN_CODE = "0510009"; // ��.���˹�ҷ���Ǫ�ҸԵ
			elseif ($DEGREECODE=='0510021') $EN_CODE = "0510021"; // ��.����¤��ٻ�ж�
			elseif ($DEGREECODE=='0510028') $EN_CODE = "0510028"; // ��.��ا�����͹����
			elseif ($DEGREECODE=='0510029') $EN_CODE = "0510029"; // ��.�����·ѹ�ᾷ��
			elseif ($DEGREECODE=='0510030') $EN_CODE = "0510030"; // ��.�����¾�Һ��
			elseif ($DEGREECODE=='0510031') $EN_CODE = "0510031"; // ��.�����¾�Һ����ШԵ�Ǫ
			elseif ($DEGREECODE=='0510032') $EN_CODE = "0510032"; // ��.�����¾�Һ����м�ا�����
			elseif ($DEGREECODE=='0510033') $EN_CODE = "0510033"; // ��.���������Ѫ��
			elseif ($DEGREECODE=='0510036') $EN_CODE = "0510036"; // ��.��ѡ�ҹ�����¡���Ҿ�ӺѴ
			elseif ($DEGREECODE=='0510043') $EN_CODE = "0510043"; // ��.�Ѹ���֡�ҵ͹��
			elseif ($DEGREECODE=='0510044') $EN_CODE = "0510044"; // ��.�Ѹ���֡�ҵ͹����
			elseif ($DEGREECODE=='0510045') $EN_CODE = "0510045"; // ��.�Ѹ���֡�ҵ͹����(�Ԫ��Ҫվ1)
			elseif ($DEGREECODE=='0510050') $EN_CODE = "0510050"; // ��.�Ԫҡ���֡��
			elseif ($DEGREECODE=='0510058') $EN_CODE = "0510058"; // ��.�ԪҼ����¾�Һ��
			elseif ($DEGREECODE=='0510059') $EN_CODE = "0510059"; // ��.�ԪҼ����¾�Һ����ШԵ�Ǫ
			elseif ($DEGREECODE=='0510060') $EN_CODE = "0510060"; // ��.�ԪҼ��������Ѫ��
			elseif ($DEGREECODE=='0510062') $EN_CODE = "0510062"; // ��.�ԪҾ�ѡ�ҹ�����¡���Ҿ�ӺѴ
			elseif ($DEGREECODE=='0510076') $EN_CODE = "0510076"; // �Ѹ���֡�ҵ͹����
			elseif ($DEGREECODE=='1010000') $EN_CODE = "1010000"; // ��.�ԪҪվ (�Ǫ.) ������º���
			elseif ($DEGREECODE=='1010008') $EN_CODE = "1010008"; // ��.��ó��ѡ��
			elseif ($DEGREECODE=='1010017') $EN_CODE = "1010017"; // ��.��ѡ�ҹ�Է����ʵ����ᾷ�� (�ѧ��෤�Ԥ)
			elseif ($DEGREECODE=='1010020') $EN_CODE = "1010020"; // ��.��ѡ�ҹ��ͧ��Ժѵԡ�êѹ�ٵ��ä
			elseif ($DEGREECODE=='1010022') $EN_CODE = "1010022"; // ��.��Һ�ż�ا��������͹����
			elseif ($DEGREECODE=='1010036') $EN_CODE = "1010036"; // ��.�Ԫ����˹�ҷ���Է����ʵ����ᾷ���ҢҾ�Ҹ��Է��
			elseif ($DEGREECODE=='1010040') $EN_CODE = "1010040"; // ��.�ԪҪվ �������ԪҤˡ���
			elseif ($DEGREECODE=='1010041') $EN_CODE = "1010041"; // ��.�ԪҪվ �������ԪҪ�ҧ�ص��ˡ���
			elseif ($DEGREECODE=='1010043') $EN_CODE = "1010043"; // ��.�ԪҪվ �������ԪҾҳԪ¡���
			elseif ($DEGREECODE=='1010044') $EN_CODE = "1010044"; // ��.�ԪҪվ �������Ԫ���Ż�ѵ�����
			elseif ($DEGREECODE=='1010047') $EN_CODE = "1010047"; // ��.�ԪҪվ���
			elseif ($DEGREECODE=='1010054') $EN_CODE = "1010054"; // ��.�ԪҾ�Һ��
			elseif ($DEGREECODE=='1010055') $EN_CODE = "1010055"; // ��.�ԪҾ�Һ�ż�ا�����
			elseif ($DEGREECODE=='1010056') $EN_CODE = "1010056"; // ��.�Ԫ������ѧ���੾���Ҫվ (������)
			elseif ($DEGREECODE=='1010062') $EN_CODE = "1010062"; // ��.�Ԫ������ѧ���੾���Ҫվ(����Ҹ�ó�آ)
			elseif ($DEGREECODE=='1010075') $EN_CODE = "1010075"; // ��.�Ǫ�ҸԵ
			elseif ($DEGREECODE=='1010080') $EN_CODE = "1010080"; // ͹ػ.��Һ����м�ا�����
			elseif ($DEGREECODE=='1010081') $EN_CODE = "1010081"; // ��.��þ�Һ����ʵ����м�ا��������٧
			elseif ($DEGREECODE=='2010000') $EN_CODE = "2010000"; // ��.�ԪҪվ෤�Ԥ (�Ƿ.) ������º���
			elseif ($DEGREECODE=='2010003') $EN_CODE = "2010003"; // ��.����ػ�ó�������������
			elseif ($DEGREECODE=='2010004') $EN_CODE = "2010004"; // ��.��þ�Һ����С�ü�ا�����(�дѺ��)
			elseif ($DEGREECODE=='2010005') $EN_CODE = "2010005"; // ��.��þ�Һ����м�ا������дѺ��
			elseif ($DEGREECODE=='2010013') $EN_CODE = "2010013"; // ��.��Ҿ�ѡ�ҹ���Ѫ����
			elseif ($DEGREECODE=='2010014') $EN_CODE = "2010014"; // ��.��Ҿ�ѡ�ҹ�Է����ʵ����ᾷ��
			elseif ($DEGREECODE=='2010015') $EN_CODE = "2010015"; // ��.��Ҿ�ѡ�ҹ�Է����ʵ����ᾷ�� (�շ�� 2)
			elseif ($DEGREECODE=='2010016') $EN_CODE = "2010016"; // ��.��Ҿ�ѡ�ҹ�Ҹ�ó�آ (��ѡ�ҹ͹����)
			elseif ($DEGREECODE=='2010017') $EN_CODE = "2010017"; // ��.���˹�ҷ���Է����ʵ����ᾷ��
			elseif ($DEGREECODE=='2010018') $EN_CODE = "2010018"; // ��.���˹�ҷ���Է����ʵ����ᾷ�� (��Ҹ��Է��)
			elseif ($DEGREECODE=='2010019') $EN_CODE = "2010019"; // ��.���˹�ҷ���Է����ʵ����ᾷ�� (�ѧ��෤�Ԥ)
			elseif ($DEGREECODE=='2010020') $EN_CODE = "2010020"; // ��.���˹�ҷ���Է����ʵ����ᾷ��շ�� 2
			elseif ($DEGREECODE=='2010023') $EN_CODE = "2010023"; // ��.��ҧ�ѹ�����
			elseif ($DEGREECODE=='2010024') $EN_CODE = "2010024"; // ��.��ҧ��д�ɰ����ػ�ó�������������
			elseif ($DEGREECODE=='2010025') $EN_CODE = "2010025"; // ��.�����Է��
			elseif ($DEGREECODE=='2010026') $EN_CODE = "2010026"; // ��.�ѹ���Ժ��
			elseif ($DEGREECODE=='2010029') $EN_CODE = "2010029"; // ��.��ا�����
			elseif ($DEGREECODE=='2010031') $EN_CODE = "2010031"; // ��.��ѡ�ҹ�Է����ʵ����ᾷ��
			elseif ($DEGREECODE=='2010036') $EN_CODE = "2010036"; // ��.��Һ����м�ا�����
			elseif ($DEGREECODE=='2010037') $EN_CODE = "2010037"; // ��.��Һ�����ѭ�ա���
			elseif ($DEGREECODE=='2010038') $EN_CODE = "2010038"; // ��.��Һ����ʵ���дѺ��
			elseif ($DEGREECODE=='2010039') $EN_CODE = "2010039"; // ��.�ѧ�ա��ᾷ��
			elseif ($DEGREECODE=='2010040') $EN_CODE = "2010040"; // ��.�Ԫҡ�þ�Һ����ШԵ�Ǫ
			elseif ($DEGREECODE=='2010041') $EN_CODE = "2010041"; // ��.�Ԫҡ�þ�Һ����м�ا�����
			elseif ($DEGREECODE=='2010043') $EN_CODE = "2010043"; // ��.�Ԫҡ���֡�Ҫ���٧
			elseif ($DEGREECODE=='2010058') $EN_CODE = "2010058"; // ��.�ԪҼ����¾�Һ����м�ا�����
			elseif ($DEGREECODE=='2010059') $EN_CODE = "2010059"; // ��.�ԪҾ�ѡ�ҹ�Է����ʵ����ᾷ��
			elseif ($DEGREECODE=='2010060') $EN_CODE = "2010060"; // ��.�Ԫ��ѧ��෤�Ԥ
			elseif ($DEGREECODE=='2010062') $EN_CODE = "2010062"; // ��.�Ԫ��ǪʶԵ� (������� 2530)
			elseif ($DEGREECODE=='2010064') $EN_CODE = "2010064"; // ��.�Է����ʵ����ᾷ��
			elseif ($DEGREECODE=='2010065') $EN_CODE = "2010065"; // ��.�Է����ʵ���آ�Ҿ
			elseif ($DEGREECODE=='2010066') $EN_CODE = "2010066"; // ��.�Ǫ�Է�ȹ�
			elseif ($DEGREECODE=='2010067') $EN_CODE = "2010067"; // ��.�Ǫ����¹
			elseif ($DEGREECODE=='2010068') $EN_CODE = "2010068"; // ��.�ǪʶԵ�
			elseif ($DEGREECODE=='2010070') $EN_CODE = "2010070"; // ��.�Ҹ�ó�آ�����(��ا�����͹����)
			elseif ($DEGREECODE=='2010071') $EN_CODE = "2010071"; // ��.�Ҹ�ó�آ��ʵ��
			elseif ($DEGREECODE=='2010074') $EN_CODE = "2010074"; // ��.�Ҹ�ó�آ��ʵ��(෤�Ԥ���Ѫ����)
			elseif ($DEGREECODE=='2010075') $EN_CODE = "2010075"; // ��.�ʵ��ȹ�֡��
			elseif ($DEGREECODE=='2010076') $EN_CODE = "2010076"; // ��ѡ�ҹ�Է����ʵ����ᾷ�� �Ң����Ե�Է����и�Ҥ�����ʹ
			elseif ($DEGREECODE=='2010078') $EN_CODE = "2010078"; // ͹ػ.�Ԫҡ���֡��
			elseif ($DEGREECODE=='2010079') $EN_CODE = "2010079"; // ͹ػ.�Է����ʵ��
			elseif ($DEGREECODE=='2010080') $EN_CODE = "2010080"; // ͹ػ.��Ż��ʵ��
			elseif ($DEGREECODE=='2010081') $EN_CODE = "2010081"; // ��.���Ѫ����Ἱ��
			elseif ($DEGREECODE=='3010000') $EN_CODE = "3010000"; // ��.�ԪҪվ����٧ (���.) ������º���
			elseif ($DEGREECODE=='3010006') $EN_CODE = "3010006"; // ��.����¤����Ѹ��
			elseif ($DEGREECODE=='3010011') $EN_CODE = "3010011"; // ��.����¤����Ѹ���ԪҪվ����٧
			elseif ($DEGREECODE=='3010015') $EN_CODE = "3010015"; // ��.��Һ�� ��ا��������͹����
			elseif ($DEGREECODE=='3010018') $EN_CODE = "3010018"; // ��.�ԪҪվ����٧ (��ҧ�ػ�ó���ᾷ��)
			elseif ($DEGREECODE=='3010019') $EN_CODE = "3010019"; // ��.�ԪҪվ����٧ �������Ԫ��ɵá���
			elseif ($DEGREECODE=='3010020') $EN_CODE = "3010020"; // ��.�ԪҪվ����٧ �������ԪҤˡ���
			elseif ($DEGREECODE=='3010021') $EN_CODE = "3010021"; // ��.�ԪҪվ����٧ �������ԪҪ�ҧ�ص��ˡ���
			elseif ($DEGREECODE=='3010022') $EN_CODE = "3010022"; // ��.�ԪҪվ����٧ �������ԪҺ����ø�áԨ
			elseif ($DEGREECODE=='3010025') $EN_CODE = "3010025"; // ��.�ԪҪվ����٧����ԪҤˡ�����ʵ��
			elseif ($DEGREECODE=='3010027') $EN_CODE = "3010027"; // ��.�ԪҾ�Һ�����͹����(3��)��л.��ا�����(6��͹)
			elseif ($DEGREECODE=='3010028') $EN_CODE = "3010028"; // ��.�Է����ʵ����ᾷ��(��º���͹ػ.)
			elseif ($DEGREECODE=='3010031') $EN_CODE = "3010031"; // ����¤�Ҫ���֡�Ҫ���٧
			elseif ($DEGREECODE=='3010032') $EN_CODE = "3010032"; // ͹ػ.
			elseif ($DEGREECODE=='3010037') $EN_CODE = "3010037"; // ͹ػ.�ѹ�ҹ����
			elseif ($DEGREECODE=='3010043') $EN_CODE = "3010043"; // ͹ػ.�����ø�áԨ
			elseif ($DEGREECODE=='3010046') $EN_CODE = "3010046"; // ͹ػ.��Һ��
			elseif ($DEGREECODE=='3010047') $EN_CODE = "3010047"; // ͹ػ.��Һ�����͹����
			elseif ($DEGREECODE=='3010048') $EN_CODE = "3010048"; // ͹ػ.��Һ���Ҹ�ó�آ (�Ǫ��Ժѵ�)
			elseif ($DEGREECODE=='3010057') $EN_CODE = "3010057"; // ͹ػ.�Է����ʵ����ᾷ��
			elseif ($DEGREECODE=='3010067') $EN_CODE = "3010067"; // ͹ػ.�֡����ʵ��
			elseif ($DEGREECODE=='3010074') $EN_CODE = "3010074"; // ��.��ü�ا�����
			elseif ($DEGREECODE=='4010000') $EN_CODE = "4010000"; // �.���������º���
			elseif ($DEGREECODE=='4010002') $EN_CODE = "4010002"; // ��.��پ�Һ�ŵ�� ���;�Һ�ŵ��
			elseif ($DEGREECODE=='4010003') $EN_CODE = "4010003"; // ��.෤�Ԥ����٧ (���.)
			elseif ($DEGREECODE=='4010007') $EN_CODE = "4010007"; // ��.��Һ�ż�ا��������͹���ª���٧
			elseif ($DEGREECODE=='4010008') $EN_CODE = "4010008"; // ��.��Һ����ʵ��
			elseif ($DEGREECODE=='4010009') $EN_CODE = "4010009"; // ��.��Һ����ʵ���м�ا��������٧
			elseif ($DEGREECODE=='4010010') $EN_CODE = "4010010"; // ��.�ԪҼ�ا�����
			elseif ($DEGREECODE=='4010012') $EN_CODE = "4010012"; // �.����֡�Һѳ�Ե
			elseif ($DEGREECODE=='4010013') $EN_CODE = "4010013"; // �.����֡�Һѳ�Ե (��Һ���֡��)
			elseif ($DEGREECODE=='4010017') $EN_CODE = "4010017"; // �.�ɵ���ʵúѳ�Ե
			elseif ($DEGREECODE=='4010018') $EN_CODE = "4010018"; // �.�����ʵúѳ�Ե
			elseif ($DEGREECODE=='4010022') $EN_CODE = "4010022"; // �.�����ʵ��ص��ˡ����ѳ�Ե
			elseif ($DEGREECODE=='4010027') $EN_CODE = "4010027"; // �.�ˡ�����ʵúѳ�Ե
			elseif ($DEGREECODE=='4010030') $EN_CODE = "4010030"; // �.�ѹ�ᾷ���ʵúѳ�Ե
			elseif ($DEGREECODE=='4010031') $EN_CODE = "4010031"; // �.෤����ա���ɵúѳ�Ե
			elseif ($DEGREECODE=='4010033') $EN_CODE = "4010033"; // �.෤����պѳ�Ե
			elseif ($DEGREECODE=='4010034') $EN_CODE = "4010034"; // �.�Ե���ʵúѳ�Ե
			elseif ($DEGREECODE=='4010035') $EN_CODE = "4010035"; // �.������ʵúѳ�Ե
			elseif ($DEGREECODE=='4010036') $EN_CODE = "4010036"; // �.�����ø�áԨ (�����çҹ�ص��ˡ���)
			elseif ($DEGREECODE=='4010038') $EN_CODE = "4010038"; // �.�����ø�áԨ�ѳ�Ե
			elseif ($DEGREECODE=='4010039') $EN_CODE = "4010039"; // �.�����ø�áԨ�ѳ�Ե (��õ�Ҵ)
			elseif ($DEGREECODE=='4010040') $EN_CODE = "4010040"; // �.�����ø�áԨ�ѳ�Ե (��ú����çҹ�ؤ��)
			elseif ($DEGREECODE=='4010041') $EN_CODE = "4010041"; // �.�����ø�áԨ�ѳ�Ե (��úѭ��)
			elseif ($DEGREECODE=='4010044') $EN_CODE = "4010044"; // �.�ѭ�պѳ�Ե
			elseif ($DEGREECODE=='4010046') $EN_CODE = "4010046"; // �.��Һ����ʵúѳ�Ե
			elseif ($DEGREECODE=='4010047') $EN_CODE = "4010047"; // �.�ҳԪ���ʵúѳ�Ե
			elseif ($DEGREECODE=='4010049') $EN_CODE = "4010049"; // �.ᾷ���ʵúѳ�Ե
			elseif ($DEGREECODE=='4010051') $EN_CODE = "4010051"; // �.���Ѫ��Ժ����ʵúѳ�Ե
			elseif ($DEGREECODE=='4010052') $EN_CODE = "4010052"; // �.���Ѫ��ʵúѳ�Ե
			elseif ($DEGREECODE=='4010054') $EN_CODE = "4010054"; // �.�Ѱ�����ʹ��ʵúѳ�Ե
			elseif ($DEGREECODE=='4010055') $EN_CODE = "4010055"; // �.�Ѱ��ʵúѳ�Ե
			elseif ($DEGREECODE=='4010058') $EN_CODE = "4010058"; // �.��������ʵúѳ�Ե
			elseif ($DEGREECODE=='4010061') $EN_CODE = "4010061"; // �.�Է����ʵúѳ�Ե
			elseif ($DEGREECODE=='4010062') $EN_CODE = "4010062"; // �.�Է����ʵúѳ�Ե (����Ҿ�ӺѴ)
			elseif ($DEGREECODE=='4010064') $EN_CODE = "4010064"; // �.�Է����ʵúѳ�Ե (�ˡ�����ʵ��)
			elseif ($DEGREECODE=='4010065') $EN_CODE = "4010065"; // �.�Է����ʵúѳ�Ե (�Ե�Է��)
			elseif ($DEGREECODE=='4010067') $EN_CODE = "4010067"; // �.�Է����ʵúѳ�Ե (�ҧ��Һ����м�ا�����)
			elseif ($DEGREECODE=='4010069') $EN_CODE = "4010069"; // �.�Է����ʵúѳ�Ե (�����)
			elseif ($DEGREECODE=='4010070') $EN_CODE = "4010070"; // �.�Է����ʵúѳ�Ե (��Һ�� ��м�ا�����)
			elseif ($DEGREECODE=='4010072') $EN_CODE = "4010072"; // �.�Է����ʵúѳ�Ե (���ԡ����ᾷ��)
			elseif ($DEGREECODE=='4010074') $EN_CODE = "4010074"; // �.�Է����ʵúѳ�Ե (�ѧ��෤�Ԥ)
			elseif ($DEGREECODE=='4010078') $EN_CODE = "4010078"; // �.�Է����ʵúѳ�Ե (�Է����ʵ����ᾷ��)
			elseif ($DEGREECODE=='4010079') $EN_CODE = "4010079"; // �.�Է����ʵúѳ�Ե (�Ǫ�Է�ȹ�)
			elseif ($DEGREECODE=='4010082') $EN_CODE = "4010082"; // �.�Է����ʵúѳ�Ե (���ɰ��ʵ���ɵ�)
			elseif ($DEGREECODE=='4010083') $EN_CODE = "4010083"; // �.�Է����ʵúѳ�Ե (�ѵ���ʵ��)
			elseif ($DEGREECODE=='4010085') $EN_CODE = "4010085"; // �.�Է����ʵúѳ�Ե (�Ҹ�ó�آ��ʵ��)
			elseif ($DEGREECODE=='4010087') $EN_CODE = "4010087"; // �.�Է����ʵúѳ�Ե ��Һ�� (�Ҹ�ó�آ)
			elseif ($DEGREECODE=='4010088') $EN_CODE = "4010088"; // �.�Է����ʵúѳ�Ե(��þ�Һ���Ҹ�ó�آ)
			elseif ($DEGREECODE=='4010091') $EN_CODE = "4010091"; // �.�Է����ʵúѳ�Ե(���)
			elseif ($DEGREECODE=='4010092') $EN_CODE = "4010092"; // �.�Է����ʵúѳ�Ե(�Ե�Է��)
			elseif ($DEGREECODE=='4010093') $EN_CODE = "4010093"; // �.�Է����ʵúѳ�Ե(����Է��)
			elseif ($DEGREECODE=='4010094') $EN_CODE = "4010094"; // �.�Է����ʵúѳ�Ե(��Һ����м�ا�����)
			elseif ($DEGREECODE=='4010103') $EN_CODE = "4010103"; // �.���ǡ�����ʵúѳ�Ե
			elseif ($DEGREECODE=='4010105') $EN_CODE = "4010105"; // �.��ʹ��ʵúѳ�Ե
			elseif ($DEGREECODE=='4010106') $EN_CODE = "4010106"; // �.��Ż�����ѳ�Ե
			elseif ($DEGREECODE=='4010108') $EN_CODE = "4010108"; // �.��Ż������ʵúѳ�Ե
			elseif ($DEGREECODE=='4010111') $EN_CODE = "4010111"; // �.��Ż�ѳ�Ե
			elseif ($DEGREECODE=='4010123') $EN_CODE = "4010123"; // �.��Ż��ʵúѳ�Ե
			elseif ($DEGREECODE=='4010125') $EN_CODE = "4010125"; // �.��Ż��ʵúѳ�Ե (�Ե�Է��)
			elseif ($DEGREECODE=='4010131') $EN_CODE = "4010131"; // �.��Ż��ʵúѳ�Ե (������)
			elseif ($DEGREECODE=='4010133') $EN_CODE = "4010133"; // �.��Ż��ʵúѳ�Ե (�����ѧ���)
			elseif ($DEGREECODE=='4010137') $EN_CODE = "4010137"; // �.��Ż��ʵúѳ�Ե (�ѧ���Է�� �ҹ����Է��
			elseif ($DEGREECODE=='4010140') $EN_CODE = "4010140"; // �.��Ż��ʵúѳ�Ե(�Ѱ��ʵ��)
			elseif ($DEGREECODE=='4010142') $EN_CODE = "4010142"; // �.�֡����ʵúѳ�Ե
			elseif ($DEGREECODE=='4010147') $EN_CODE = "4010147"; // �.�֡����ʵúѳ�Ե (�آ�֡��)
			elseif ($DEGREECODE=='4010149') $EN_CODE = "4010149"; // �.���ɰ��ʵúѳ�Ե
			elseif ($DEGREECODE=='4010151') $EN_CODE = "4010151"; // �.�����������ɵ�����ˡó�ѳ�Ե
			elseif ($DEGREECODE=='4010153') $EN_CODE = "4010153"; // �.ʶԵ���ʵúѳ�Ե
			elseif ($DEGREECODE=='4010154') $EN_CODE = "4010154"; // �.�ѧ���Է�Һѳ�Ե
			elseif ($DEGREECODE=='4010156') $EN_CODE = "4010156"; // �.�ѧ����ʵúѳ�Ե
			elseif ($DEGREECODE=='4010157') $EN_CODE = "4010157"; // �.�ѧ��ʧ��������ʵúѳ�Ե
			elseif ($DEGREECODE=='4010158') $EN_CODE = "4010158"; // �.�ѵ�ᾷ���ʵúѳ�Ե
			elseif ($DEGREECODE=='4010159') $EN_CODE = "4010159"; // �.�Ҹ�ó�آ��ʵúѳ�Ե
			elseif ($DEGREECODE=='4010160') $EN_CODE = "4010160"; // �.�Ҹ�ó�آ��ʵúѳ�Ե (�������Ҹ�ó�آ)
			elseif ($DEGREECODE=='4010164') $EN_CODE = "4010164"; // �.�ѡ����ʵúѳ�Ե (��ó��ѡ����ʵ��)
			elseif ($DEGREECODE=='4010166') $EN_CODE = "4010166"; // �.�ص��ˡ�����ʵúѳ�Ե
			elseif ($DEGREECODE=='4010168') $EN_CODE = "4010168"; // ��.��Һ����ҵ����º��һ�ԭ�ҵ�� (������ͧ 2 ��)
			elseif ($DEGREECODE=='4010169') $EN_CODE = "4010169"; // ��.��Һ����ʵ�� (������ͧ 2 �� ��º��һ�ԭ�ҵ��)
			elseif ($DEGREECODE=='5010000') $EN_CODE = "5010000"; // ��.����٧/��.�ѳ�Ե (�٧���һ.���/��ӡ��һ.�)
			elseif ($DEGREECODE=='5010002') $EN_CODE = "5010002"; // ��.����٧/��.�ѳ�Ե�Է����ʵ����ᾷ���չԤ
			elseif ($DEGREECODE=='5010005') $EN_CODE = "5010005"; // ��.����٧��úѭ��
			elseif ($DEGREECODE=='5010008') $EN_CODE = "5010008"; // ��.����٧�ҧ����ͺ�ѭ��
			elseif ($DEGREECODE=='5010009') $EN_CODE = "5010009"; // ��.����٧�ҧ�Է����ʵ����ᾷ��
			elseif ($DEGREECODE=='5010010') $EN_CODE = "5010010"; // ��.����٧�ҧ�Է����ʵ����ᾷ���չԤ
			elseif ($DEGREECODE=='5010036') $EN_CODE = "5010036"; // ��.�ѳ�Ե�ҧ�Է����ʵ����ᾷ���Թԡ
			elseif ($DEGREECODE=='5010037') $EN_CODE = "5010037"; // ��.�ѳ�Ե�ҧ�Է����ʵ����ᾷ���չԤ
			elseif ($DEGREECODE=='5010042') $EN_CODE = "5010042"; // ��.�ѳ�Ե�ԪҪվ���
			elseif ($DEGREECODE=='5010043') $EN_CODE = "5010043"; // ��.�ѳ�Ե�������ʵ��ࢵ��͹����آ�Է��
			elseif ($DEGREECODE=='6010000') $EN_CODE = "6010000"; // �.�������º���
			elseif ($DEGREECODE=='6010002') $EN_CODE = "6010002"; // �.��èѴ����Ҥ�Ѱ����Ҥ�͡����Һѳ�Ե
			elseif ($DEGREECODE=='6010003') $EN_CODE = "6010003"; // �.��èѴ�����Һѳ�Ե
			elseif ($DEGREECODE=='6010007') $EN_CODE = "6010007"; // �.����֡����Һѳ�Ե
			elseif ($DEGREECODE=='6010010') $EN_CODE = "6010010"; // �.�����ʵ���Һѳ�Ե
			elseif ($DEGREECODE=='6010013') $EN_CODE = "6010013"; // �.�ˡ�����ʵ���Һѳ�Ե
			elseif ($DEGREECODE=='6010015') $EN_CODE = "6010015"; // �.�ѹ�ᾷ���ʵ���Һѳ�Ե
			elseif ($DEGREECODE=='6010018') $EN_CODE = "6010018"; // �.�Ե���ʵ���Һѳ�Ե
			elseif ($DEGREECODE=='6010021') $EN_CODE = "6010021"; // �.�����ø�áԨ��Һѳ�Ե
			elseif ($DEGREECODE=='6010022') $EN_CODE = "6010022"; // �.�ѭ����Һѳ�Ե
			elseif ($DEGREECODE=='6010025') $EN_CODE = "6010025"; // �.��Һ����ʵ���Һѳ�Ե
			elseif ($DEGREECODE=='6010027') $EN_CODE = "6010027"; // �.�Ѳ���������ʵ���Һѳ�Ե
			elseif ($DEGREECODE=='6010029') $EN_CODE = "6010029"; // �.�Ѳ���������ʵ���Һѳ�Ե (ʶԵԻ���ء��)
			elseif ($DEGREECODE=='6010030') $EN_CODE = "6010030"; // �.�Ѳ���������ʵ���Һѳ�Ե(�Ѳ���ѧ��)
			elseif ($DEGREECODE=='6010031') $EN_CODE = "6010031"; // �.�Ѳ���������ʵ���Һѳ�Ե�ҧ��èѴ����Ҥ�Ѱ����͡��
			elseif ($DEGREECODE=='6010039') $EN_CODE = "6010039"; // �.�Ѳ���������ʵ���Һѳ�Ե�ҧ�Ѱ�����ʹ��ʵ��
			elseif ($DEGREECODE=='6010044') $EN_CODE = "6010044"; // �.�Ѳ���ç�ҹ������ʴԡ����Һѳ�Ե
			elseif ($DEGREECODE=='6010045') $EN_CODE = "6010045"; // �.�ҳԪ���ʵ���Һѳ�Ե
			elseif ($DEGREECODE=='6010047') $EN_CODE = "6010047"; // �.���Ѫ��ʵ���Һѳ�Ե
			elseif ($DEGREECODE=='6010048') $EN_CODE = "6010048"; // �.�ҹ����Է����Һѳ�Ե
			elseif ($DEGREECODE=='6010049') $EN_CODE = "6010049"; // �.�Ѱ�����ʹ��ʵ���Һѳ�Ե
			elseif ($DEGREECODE=='6010050') $EN_CODE = "6010050"; // �.�Ѱ��ʵ���Һѳ�Ե
			elseif ($DEGREECODE=='6010054') $EN_CODE = "6010054"; // �.�Է����ʵ���Һѳ�Ե
			elseif ($DEGREECODE=='6010056') $EN_CODE = "6010056"; // �.�Է����ʵ���Һѳ�Ե (���)
			elseif ($DEGREECODE=='6010059') $EN_CODE = "6010059"; // �.�Է����ʵ���Һѳ�Ե (෤���������Ǵ����)
			elseif ($DEGREECODE=='6010060') $EN_CODE = "6010060"; // �.�Է����ʵ���Һѳ�Ե (��Һ����ʵ��)
			elseif ($DEGREECODE=='6010063') $EN_CODE = "6010063"; // �.�Է����ʵ���Һѳ�Ե (�Է�ҡ���кҴ)
			elseif ($DEGREECODE=='6010071') $EN_CODE = "6010071"; // �.�Է����ʵ���Һѳ�Ե(��Һ��)
			elseif ($DEGREECODE=='6010075') $EN_CODE = "6010075"; // �.�Է����ʵ���Һѳ�Ե(ʶԵԻ���ء��)
			elseif ($DEGREECODE=='6010076') $EN_CODE = "6010076"; // �.�Է����ʵ���Һѳ�Ե(�Ҹ�ó�آ��ʵ��)
			elseif ($DEGREECODE=='6010077') $EN_CODE = "6010077"; // �.�Է����ʵ���Һѳ�Ե���ᾷ���չԤ
			elseif ($DEGREECODE=='6010089') $EN_CODE = "6010089"; // �.��Ż��ʵ���Һѳ�Ե
			elseif ($DEGREECODE=='6010101') $EN_CODE = "6010101"; // �.�֡����ʵ���Һѳ�Ե
			elseif ($DEGREECODE=='6010103') $EN_CODE = "6010103"; // �.�֡����ʵ���Һѳ�Ե (�Ե�Է�ҡ���֡��)
			elseif ($DEGREECODE=='6010104') $EN_CODE = "6010104"; // �.���ɰ��ʵ���Һѳ�Ե
			elseif ($DEGREECODE=='6010111') $EN_CODE = "6010111"; // �.�ѧ����ʵ���Һѳ�Ե
			elseif ($DEGREECODE=='6010113') $EN_CODE = "6010113"; // �.�ѧ��ʧ��������ʵ���Һѳ�Ե
			elseif ($DEGREECODE=='6010114') $EN_CODE = "6010114"; // �.�Ҹ�ó�آ��ʵ���Һѳ�Ե
			elseif ($DEGREECODE=='6010115') $EN_CODE = "6010115"; // �.�ѡ����ʵ���Һѳ�Ե
			elseif ($DEGREECODE=='8010003') $EN_CODE = "8010003"; // �.����֡�Ҵ�ɮպѳ�Ե
			elseif ($DEGREECODE=='8010004') $EN_CODE = "8010004"; // �.�����ʵô�ɮպѳ�Ե
			elseif ($DEGREECODE=='8010007') $EN_CODE = "8010007"; // �.��ɮպѳ�Ե�Ե����ѡ���
			elseif ($DEGREECODE=='8010014') $EN_CODE = "8010014"; // �.��Ѫ�Ҵ�ɮպѳ�Ե
			elseif ($DEGREECODE=='8010026') $EN_CODE = "8010026"; // �.�Է����ʵô�ɮպѳ�Ե
			elseif ($DEGREECODE=='8010035') $EN_CODE = "8010035"; // �.�Ҹ�ó�آ��ʵô�ɮպѳ�Ե
			elseif ($DEGREECODE=='9010003') $EN_CODE = "9010003"; // �زԺѵèҡᾷ����
			elseif ($DEGREECODE=='9010004') $EN_CODE = "9010004"; // �زԺѵ��ʴ������������ӹҭ㹡�û�Сͺ�ԪҪվ�Ǫ����
			elseif ($DEGREECODE=='9010005') $EN_CODE = "9010005"; // ˹ѧ���͹��ѵ��ʴ������������ӹҭ㹡�û�Сͺ�ԪҪվ�Ǫ����
			elseif ($DEGREECODE=='9010006') $EN_CODE = "9010006"; // ͹��ѵԺѵ�
			elseif ($DEGREECODE=='9010007') $EN_CODE = "9010007"; // ����ԡѹ���� 3 ��
			elseif ($DEGREECODE=='9010008') $EN_CODE = "9010008"; // �زԺѵèҡ��ҡ�þ�Һ��
			elseif ($DEGREECODE=='9010009') $EN_CODE = "9010009"; // ��.����֡��ͺ��
			elseif ($DEGREECODE=='9010010') $EN_CODE = "9010010"; // ��.��þ�Һ��੾�зҧ
			$EN_CODE = (trim($EN_CODE))? "'".trim($EN_CODE)."'" : "NULL";

			$EM_CODE = "";
			if ($MAJORCODE=='10000100') $EM_CODE = "10000100"; // 	����֡�� (EDUCATION)
			elseif ($MAJORCODE=='10000101') $EM_CODE = "10000101"; // �֡����ʵ��
			elseif ($MAJORCODE=='12002101') $EM_CODE = "12002101"; // ��û�ж��֡��
			elseif ($MAJORCODE=='15001101') $EM_CODE = "15001101"; // ����֡�Ҽ���˭���С���֡�ҵ�����ͧ
			elseif ($MAJORCODE=='16000100') $EM_CODE = "16000100"; // ����֡�Ҿ����
			elseif ($MAJORCODE=='17012100') $EM_CODE = "17012100"; // �آ�֡��
			elseif ($MAJORCODE=='17018100') $EM_CODE = "17018100"; // ෤�������й�ѵ����
			elseif ($MAJORCODE=='17019100') $EM_CODE = "17019100"; // �Է����ʵ�� (����Ҿ����Ҿ)
			elseif ($MAJORCODE=='17021100') $EM_CODE = "17021100"; // ��Ż�֡��
			elseif ($MAJORCODE=='17026100') $EM_CODE = "17026100"; // �֡����ʵ��-�ɵ�
			elseif ($MAJORCODE=='17039100') $EM_CODE = "17039100"; // �Ե�Է�ҡ���֡��
			elseif ($MAJORCODE=='17039101') $EM_CODE = "17039101"; // �Ե�Է�ҡ���֡����С������
			elseif ($MAJORCODE=='17039104') $EM_CODE = "17039104"; // �Ե�Է�ҡ������
			elseif ($MAJORCODE=='17039106') $EM_CODE = "17039106"; // �Ե�Է����С������
			elseif ($MAJORCODE=='17039119') $EM_CODE = "17039119"; // �Ե�Է�ҾѲ�ҡ��
			elseif ($MAJORCODE=='17039122') $EM_CODE = "17039122"; // �Ե�Է�ҡ�û�֡��
			elseif ($MAJORCODE=='17039123') $EM_CODE = "17039123"; // �Ե�Է�ҡ�����ӻ�֡��
			elseif ($MAJORCODE=='17039124') $EM_CODE = "17039124"; // �Ե�Է��(�Ե�Է�ҡ���֡��������ǡ���֡��)
			elseif ($MAJORCODE=='17040100') $EM_CODE = "17040100"; // �Ե�Է���ص��ˡ���
			elseif ($MAJORCODE=='17041101') $EM_CODE = "17041101"; // �Ե�Է�Ҫ����
			elseif ($MAJORCODE=='17042100') $EM_CODE = "17042100"; // �Ե�Է��
			elseif ($MAJORCODE=='17044100') $EM_CODE = "17044100"; // �Ե�Է���ص�ҡ������ͧ����
			elseif ($MAJORCODE=='17046100') $EM_CODE = "17046100"; // ��áԨ�֡��
			elseif ($MAJORCODE=='17048100') $EM_CODE = "17048100"; // ��ó��ѡ���ʵ�����ʹ����ʵ��
			elseif ($MAJORCODE=='17049100') $EM_CODE = "17049100"; // ��ó��ѡ����ʵ��
			elseif ($MAJORCODE=='17050100') $EM_CODE = "17050100"; // ��ó��ѡ���ʵ�������ù�����ʵ��
			elseif ($MAJORCODE=='17052101') $EM_CODE = "17052101"; // �������
			elseif ($MAJORCODE=='17053100') $EM_CODE = "17053100"; // �ˡ�����ʵ���֡��
			elseif ($MAJORCODE=='17054100') $EM_CODE = "17054100"; // ��پ�Һ��
			elseif ($MAJORCODE=='17059100') $EM_CODE = "17059100"; // �ˡó�
			elseif ($MAJORCODE=='17064100') $EM_CODE = "17064100"; // ��èѴ�������Ѻ�ѡ������
			elseif ($MAJORCODE=='18003102') $EM_CODE = "18003102"; // ����Ԩ����оѲ����ѡ�ٵ�
			elseif ($MAJORCODE=='18004101') $EM_CODE = "18004101"; // ���ȡ���֡����оѲ����ѡ�ٵ�
			elseif ($MAJORCODE=='18006101') $EM_CODE = "18006101"; // ෤����շҧ����֡��
			elseif ($MAJORCODE=='18007100') $EM_CODE = "18007100"; // ෤��������������á���֡��
			elseif ($MAJORCODE=='18008100') $EM_CODE = "18008100"; // ෤�������й�ѵ�����ҧ����֡��
			elseif ($MAJORCODE=='18011100') $EM_CODE = "18011100"; // ����Ѵ��л����Թ�š���֡��
			elseif ($MAJORCODE=='18014101') $EM_CODE = "18014101"; // ����Ѵ�š���֡��
			elseif ($MAJORCODE=='18017100') $EM_CODE = "18017100"; // ����Ԩ�´��Թ�ҹ
			elseif ($MAJORCODE=='18019100') $EM_CODE = "18019100"; // �ʵ��ȹ�֡��
			elseif ($MAJORCODE=='18020100') $EM_CODE = "18020100"; // ෤����ա���֡��
			elseif ($MAJORCODE=='18023100') $EM_CODE = "18023100"; // ��ú����á���֡��
			elseif ($MAJORCODE=='18023101') $EM_CODE = "18023101"; // �����á���֡��
			elseif ($MAJORCODE=='18024100') $EM_CODE = "18024100"; // ��ú������ç���¹
			elseif ($MAJORCODE=='21012100') $EM_CODE = "21012100"; // ������ʵ�
			elseif ($MAJORCODE=='21030100') $EM_CODE = "21030100"; // �����ѧ���
			elseif ($MAJORCODE=='21031100') $EM_CODE = "21031100"; // ���ҽ������
			elseif ($MAJORCODE=='21034100') $EM_CODE = "21034100"; // ������
			elseif ($MAJORCODE=='21036100') $EM_CODE = "21036100"; // ���ҵ�ҧ�����
			elseif ($MAJORCODE=='21042100') $EM_CODE = "21042100"; // �����ѧ��ɸ�áԨ
			elseif ($MAJORCODE=='21052100') $EM_CODE = "21052100"; // ���ҭ���蹸�áԨ
			elseif ($MAJORCODE=='21055100') $EM_CODE = "21055100"; // ���� �Ԫ��͡������
			elseif ($MAJORCODE=='23000100') $EM_CODE = "23000100"; // ����ѵ���ʵ��
			elseif ($MAJORCODE=='26003100') $EM_CODE = "26003100"; // �����Դ���Ԣͧ������ͤ�������
			elseif ($MAJORCODE=='30000100') $EM_CODE = "30000100"; // ��Ż����
			elseif ($MAJORCODE=='31000100') $EM_CODE = "31000100"; // �͡Ẻ����ء����Ż�
			elseif ($MAJORCODE=='31000101') $EM_CODE = "31000101"; // �͡Ẻ������Ż�
			elseif ($MAJORCODE=='31009100') $EM_CODE = "31009100"; // �ص��ˡ�����Ż�
			elseif ($MAJORCODE=='31023100') $EM_CODE = "31023100"; // �͡Ẻ��Ե�ѳ��
			elseif ($MAJORCODE=='32000100') $EM_CODE = "32000100"; // �ԨԵ���Ż�
			elseif ($MAJORCODE=='32005100') $EM_CODE = "32005100"; // �Եá����ҡ�
			elseif ($MAJORCODE=='32006100') $EM_CODE = "32006100"; // ��е��ҡ���
			elseif ($MAJORCODE=='34000100') $EM_CODE = "34000100"; // �ү��Ż�
			elseif ($MAJORCODE=='35000100') $EM_CODE = "35000100"; // �ѵ�����
			elseif ($MAJORCODE=='36004100') $EM_CODE = "36004100"; // ����͡Ẻ
			elseif ($MAJORCODE=='51001100') $EM_CODE = "51001100"; // �ѧ����ʵ���þѲ��
			elseif ($MAJORCODE=='51004100') $EM_CODE = "51004100"; // �ѧ��ʧ������
			elseif ($MAJORCODE=='51004101') $EM_CODE = "51004101"; // �ѧ��ʧ��������ʵ��
			elseif ($MAJORCODE=='51006100') $EM_CODE = "51006100"; // ��º����С���ҧἹ�ҧ�ѧ��
			elseif ($MAJORCODE=='51008100') $EM_CODE = "51008100"; // �ѧ���֡��
			elseif ($MAJORCODE=='51009100') $EM_CODE = "51009100"; // �ѧ��ʧ������ҧ���ᾷ��
			elseif ($MAJORCODE=='51011100') $EM_CODE = "51011100"; // �ѧ����ʵ���آ�Ҿ
			elseif ($MAJORCODE=='51012100') $EM_CODE = "51012100"; // �ѧ���Է��
			elseif ($MAJORCODE=='51014100') $EM_CODE = "51014100"; // �ѧ���Է������ҹ����Է��
			elseif ($MAJORCODE=='51015100') $EM_CODE = "51015100"; // �ѧ���Է�һ���ء��
			elseif ($MAJORCODE=='51017101') $EM_CODE = "51017101"; // ��þѲ�Ҫ����
			elseif ($MAJORCODE=='51018100') $EM_CODE = "51018100"; // �Ѳ���ѧ��
			elseif ($MAJORCODE=='51030100') $EM_CODE = "51030100"; // �Ԩ�»�Ъҡ�����ѧ��
			elseif ($MAJORCODE=='51032100') $EM_CODE = "51032100"; // ��þѲ�ҷ�Ѿ�ҡ�������
			elseif ($MAJORCODE=='51032101') $EM_CODE = "51032101"; // ��þѲ�ҷ�Ѿ�ҡ����������ͧ����
			elseif ($MAJORCODE=='51034100') $EM_CODE = "51034100"; // ��Ъҡ���С�þѲ��
			elseif ($MAJORCODE=='51039100') $EM_CODE = "51039100"; // ��Ъҡ��֡��
			elseif ($MAJORCODE=='51042100') $EM_CODE = "51042100"; // ������ʵ���֡��
			elseif ($MAJORCODE=='51043100') $EM_CODE = "51043100"; // ������ʵ��
			elseif ($MAJORCODE=='51046101') $EM_CODE = "51046101"; // ෤������ѧ��
			elseif ($MAJORCODE=='51051100') $EM_CODE = "51051100"; // ��º���Ҹ�ó�
			elseif ($MAJORCODE=='52009100') $EM_CODE = "52009100"; // ���ɰ��ʵ���ɵ�
			elseif ($MAJORCODE=='52016100') $EM_CODE = "52016100"; // ��Ѿ�ҡ�������
			elseif ($MAJORCODE=='52017100') $EM_CODE = "52017100"; // ���ɰ��ʵ���áԨ
			elseif ($MAJORCODE=='52021100') $EM_CODE = "52021100"; // ���ɰ��ʵ���ˡó�
			elseif ($MAJORCODE=='52027100') $EM_CODE = "52027100"; // ���ɰ��ʵ���þѲ��
			elseif ($MAJORCODE=='52029100') $EM_CODE = "52029100"; // ���ɰ��ʵ���Ҹ�ó�آ
			elseif ($MAJORCODE=='53001100') $EM_CODE = "53001100"; // ������ʵ��
			elseif ($MAJORCODE=='53021100') $EM_CODE = "53021100"; // ���������Ū�
			elseif ($MAJORCODE=='53039100') $EM_CODE = "53039100"; // ��ù�����ʵ��
			elseif ($MAJORCODE=='53041100') $EM_CODE = "53041100"; // ��������ʵ��
			elseif ($MAJORCODE=='54000100') $EM_CODE = "54000100"; // �Ѱ��ʵ��
			elseif ($MAJORCODE=='54001100') $EM_CODE = "54001100"; // ������ͧ��û���ͧ
			elseif ($MAJORCODE=='54002100') $EM_CODE = "54002100"; // ��û���ͧ
			elseif ($MAJORCODE=='54008100') $EM_CODE = "54008100"; // �Ѱ�����ʹ��ʵ��
			elseif ($MAJORCODE=='54009100') $EM_CODE = "54009100"; // �������Ѱ�Ԩ
			elseif ($MAJORCODE=='54018101') $EM_CODE = "54018101"; // ��ú�����ͧ����
			elseif ($MAJORCODE=='54019100') $EM_CODE = "54019100"; // ��èѴ���
			elseif ($MAJORCODE=='54019101') $EM_CODE = "54019101"; // ��èѴ��çҹ�Ҹ�ó�
			elseif ($MAJORCODE=='54019102') $EM_CODE = "54019102"; // ��ú�������й�º�����ʴԡ���ѧ��
			elseif ($MAJORCODE=='54020100') $EM_CODE = "54020100"; // ��èѴ��÷����
			elseif ($MAJORCODE=='54021100') $EM_CODE = "54021100"; // ��ú����÷����
			elseif ($MAJORCODE=='54028100') $EM_CODE = "54028100"; // ��ú����÷�Ѿ�ҡ�������
			elseif ($MAJORCODE=='54032100') $EM_CODE = "54032100"; // �������������л����Թ�ç���
			elseif ($MAJORCODE=='55000100') $EM_CODE = "55000100"; // ����ɳ�
			elseif ($MAJORCODE=='55001100') $EM_CODE = "55001100"; // ��û�Ъ�����ѹ��
			elseif ($MAJORCODE=='55003100') $EM_CODE = "55003100"; // ����ɳ���С�û�Ъ�����ѹ��
			elseif ($MAJORCODE=='55009100') $EM_CODE = "55009100"; // ��úѭ��
			elseif ($MAJORCODE=='55009101') $EM_CODE = "55009101"; // �ѭ��
			elseif ($MAJORCODE=='55011107') $EM_CODE = "55011107"; // ��áԨ�����ҧ�����
			elseif ($MAJORCODE=='55011109') $EM_CODE = "55011109"; // ��èѴ��ä����Ѵ���Ẻ��óҡ��
			elseif ($MAJORCODE=='55013101') $EM_CODE = "55013101"; // ��ú����ø�áԨ
			elseif ($MAJORCODE=='55013102') $EM_CODE = "55013102"; // �����ø�áԨ
			elseif ($MAJORCODE=='55014104') $EM_CODE = "55014104"; // ��áԨ�֡��(�ѭ��)
			elseif ($MAJORCODE=='55019101') $EM_CODE = "55019101"; // ����Ţҹء��
			elseif ($MAJORCODE=='55021100') $EM_CODE = "55021100"; // ��èѴ����ӹѡ�ҹ
			elseif ($MAJORCODE=='55021101') $EM_CODE = "55021101"; // ��Ԫ¡��
			elseif ($MAJORCODE=='55021104') $EM_CODE = "55021104"; // �ҳԪ¡��
			elseif ($MAJORCODE=='55021105') $EM_CODE = "55021105"; // �ҳԪ¡���
			elseif ($MAJORCODE=='55022100') $EM_CODE = "55022100"; // ��õ�Ҵ
			elseif ($MAJORCODE=='55023100') $EM_CODE = "55023100"; // ��â��
			elseif ($MAJORCODE=='55024100') $EM_CODE = "55024100"; // ����Թ��С�ø�Ҥ��
			elseif ($MAJORCODE=='55025100') $EM_CODE = "55025100"; // ��ø�Ҥ����С���Թ
			elseif ($MAJORCODE=='55026100') $EM_CODE = "55026100"; // ����Թ
			elseif ($MAJORCODE=='55���100') $EM_CODE = "55���100"; // ��èѴ��çҹ������ҧ
			elseif ($MAJORCODE=='56021100') $EM_CODE = "56021100"; // ����ç�����С�÷�ͧ�����
			elseif ($MAJORCODE=='57000100') $EM_CODE = "57000100"; // �ˡ�����ʵ��
			elseif ($MAJORCODE=='57000101') $EM_CODE = "57000101"; // �ˡ���
			elseif ($MAJORCODE=='57002100') $EM_CODE = "57002100"; // �ˡ�����ʵ������
			elseif ($MAJORCODE=='57005100') $EM_CODE = "57005100"; // ����������ͧ�觡��
			elseif ($MAJORCODE=='57007100') $EM_CODE = "57007100"; // �Ѳ������Ф�ͺ����
			elseif ($MAJORCODE=='58003100') $EM_CODE = "58003100"; // �Ţҹء�á��ᾷ��
			elseif ($MAJORCODE=='58006100') $EM_CODE = "58006100"; // �ص��ˡ�������ͧ��
			elseif ($MAJORCODE=='60000100') $EM_CODE = "60000100"; // �Է����ʵ������
			elseif ($MAJORCODE=='60001102') $EM_CODE = "60001102"; // �Է����ʵ��-����Է��
			elseif ($MAJORCODE=='60001103') $EM_CODE = "60001103"; // �Է����ʵ��-���
			elseif ($MAJORCODE=='61000100') $EM_CODE = "61000100"; // �Է����ʵ��
			elseif ($MAJORCODE=='61003100') $EM_CODE = "61003100"; // �ѹ����ʵ�� (GENETICS)
			elseif ($MAJORCODE=='61003102') $EM_CODE = "61003102"; // ����Է��
			elseif ($MAJORCODE=='61005100') $EM_CODE = "61005100"; // ����Է�һ���ء��
			elseif ($MAJORCODE=='61008100') $EM_CODE = "61008100"; // ��Ū���Է��
			elseif ($MAJORCODE=='61010100') $EM_CODE = "61010100"; // ��Ū���Է�һ���ء��
			elseif ($MAJORCODE=='61012100') $EM_CODE = "61012100"; // ���ʶԵ�
			elseif ($MAJORCODE=='61015100') $EM_CODE = "61015100"; // ���
			elseif ($MAJORCODE=='61022100') $EM_CODE = "61022100"; // ������
			elseif ($MAJORCODE=='61026100') $EM_CODE = "61026100"; // �����շҧ���ᾷ��
			elseif ($MAJORCODE=='61027100') $EM_CODE = "61027100"; // ��ժ���Ҿ
			elseif ($MAJORCODE=='61036100') $EM_CODE = "61036100"; // ���ԡ��
			elseif ($MAJORCODE=='63001101') $EM_CODE = "63001101"; // ෤����ժ���Ҿ
			elseif ($MAJORCODE=='63006100') $EM_CODE = "63006100"; // ����Ǵ�����֡��
			elseif ($MAJORCODE=='63007100') $EM_CODE = "63007100"; // ����Ǵ����
			elseif ($MAJORCODE=='63009100') $EM_CODE = "63009100"; // �Է����ʵ�����෤����ա�������
			elseif ($MAJORCODE=='63014100') $EM_CODE = "63014100"; // ��º����С�èѴ��÷�Ѿ�ҡ��������Ǵ����
			elseif ($MAJORCODE=='63017100') $EM_CODE = "63017100"; // ͹��������Ǵ����
			elseif ($MAJORCODE=='63025100') $EM_CODE = "63025100"; // ෤����շ������������͡�þѲ�ҷ�Ѿ�ҡ�
			elseif ($MAJORCODE=='63026100') $EM_CODE = "63026100"; // �����Է��
			elseif ($MAJORCODE=='63027100') $EM_CODE = "63027100"; // ෤����������
			elseif ($MAJORCODE=='63032100') $EM_CODE = "63032100"; // ����ҡ�ê����
			elseif ($MAJORCODE=='63040100') $EM_CODE = "63040100"; // �Է����ʵ����������������ҡ��
			elseif ($MAJORCODE=='63044100') $EM_CODE = "63044100"; // ������������ҡ��
			elseif ($MAJORCODE=='64000100') $EM_CODE = "64000100"; // ʶԵ�
			elseif ($MAJORCODE=='64003100') $EM_CODE = "64003100"; // ��Ե��ʵ��
			elseif ($MAJORCODE=='64006101') $EM_CODE = "64006101"; // ʶԵԻ���ء��
			elseif ($MAJORCODE=='65000100') $EM_CODE = "65000100"; // �Է�ҡ�ä���������
			elseif ($MAJORCODE=='65000101') $EM_CODE = "65000101"; // ����������
			elseif ($MAJORCODE=='65001100') $EM_CODE = "65001100"; // �����������֡��
			elseif ($MAJORCODE=='65001101') $EM_CODE = "65001101"; // �����������áԨ
			elseif ($MAJORCODE=='65006100') $EM_CODE = "65006100"; // �к����ʹ�Ȥ���������
			elseif ($MAJORCODE=='65007100') $EM_CODE = "65007100"; // ෤��������ʹ��
			elseif ($MAJORCODE=='66000100') $EM_CODE = "66000100"; // �Ե��Է����ʵ��
			elseif ($MAJORCODE=='70000101') $EM_CODE = "70000101"; // ��þ�Һ���Ҹ�ó�آ
			elseif ($MAJORCODE=='70001100') $EM_CODE = "70001100"; // �ٵ���ʵ��-����Ǫ�Է�� (Obstetrics and Gynecology)
			elseif ($MAJORCODE=='70002100') $EM_CODE = "70002100"; // �ʵ ���ԡ ���ԧ���Է��
			elseif ($MAJORCODE=='70002101') $EM_CODE = "70002101"; // �ʵ �� ���ԡ�Է�� (Otolaryngology)
			elseif ($MAJORCODE=='70004100') $EM_CODE = "70004100"; // �������ʵ�� (Internal Medicine)
			elseif ($MAJORCODE=='70004101') $EM_CODE = "70004101"; // �������ʵ���ä���ʹ (Adult Haematology)
			elseif ($MAJORCODE=='70004102') $EM_CODE = "70004102"; // ͹��Ң��������ʵ���ä���� (Cardiology)
			elseif ($MAJORCODE=='70004103') $EM_CODE = "70004103"; // ͹��Ң��������ʵ���ä� (Nephrology)
			elseif ($MAJORCODE=='70004104') $EM_CODE = "70004104"; // �������ʵ���ä�к��������
			elseif ($MAJORCODE=='70004105') $EM_CODE = "70004105"; // �������ʵ��ࢵ��͹
			elseif ($MAJORCODE=='70004107') $EM_CODE = "70004107"; // �������ʵ��ࢵ��͹����آ�Է��
			elseif ($MAJORCODE=='70004109') $EM_CODE = "70004109"; // ͹��Ң��������ʵ���ä�к����������������ԡĵ�ä�к�������� (Pulmonary and Pulmonary Critical Care
			elseif ($MAJORCODE=='70004110') $EM_CODE = "70004110"; // ͹��Ң��������ʵ���ä�к��ҧ�Թ����� (Gastroenterology)
			elseif ($MAJORCODE=='70004111') $EM_CODE = "70004111"; // ͹��Ң��������ʵ���ä���������������к����� (Endocrinology and Metabolism)
			elseif ($MAJORCODE=='70004112') $EM_CODE = "70004112"; // ͹��Ң��������ʵ���ä���������ҵ�ʫ��� (Rheumatology)
			elseif ($MAJORCODE=='70004113') $EM_CODE = "70004113"; // �������ʵ��������Է�� (Oncology)
			elseif ($MAJORCODE=='70004114') $EM_CODE = "70004114"; // ͹��Ң��������ʵ���ä�Դ���� (Infectious Diseases)
			elseif ($MAJORCODE=='70004117') $EM_CODE = "70004117"; // �������ʵ�� - ������ʵ��
			elseif ($MAJORCODE=='70005100') $EM_CODE = "70005100"; // �Ǫ��ԺѵԷ���� (General Practice)
			elseif ($MAJORCODE=='70005102') $EM_CODE = "70005102"; // �Ǫ��ʵ��ࢵ��͹
			elseif ($MAJORCODE=='70005104') $EM_CODE = "70005104"; // �Ǫ��ʵ���ͧ�ѹ (Preventive Medicine)
			elseif ($MAJORCODE=='70005105') $EM_CODE = "70005105"; // �Ǫ��ʵ���ͧ�ѹ ᢹ��Ǫ��ʵ���ͧ�ѹ��Թԡ (Preventive Medicine : Clinical Preventive Medicine)
			elseif ($MAJORCODE=='70005106') $EM_CODE = "70005106"; // �Ǫ��ʵ���ͧ�ѹ ᢹ��кҴ�Է�� (Preventive Medicine : Epidemiology)
			elseif ($MAJORCODE=='70005108') $EM_CODE = "70005108"; // �Ǫ��ʵ���ͧ�ѹ ᢹ��Ҹ�ó�آ��ʵ�� (Preventive Medicine : Public Health)
			elseif ($MAJORCODE=='70005110') $EM_CODE = "70005110"; // �Ǫ��ԺѵԷ���� (����ѡ���ä���ͧ��)
			elseif ($MAJORCODE=='70006100') $EM_CODE = "70006100"; // �Ǫ��ʵ���鹿� (Rehabilitation medicine)
			elseif ($MAJORCODE=='70006101') $EM_CODE = "70006101"; // �Ǫ��ʵ��ء�Թ (Emergency Medicine)
			elseif ($MAJORCODE=='70006102') $EM_CODE = "70006102"; // ͹��Ң��Ǫ��ʵ����ô���з�á㹤����
			elseif ($MAJORCODE=='70006103') $EM_CODE = "70006103"; // ͹��Ң��Ǫ��ʵ������ԭ�ѹ��� (Reproductive Medicine)
			elseif ($MAJORCODE=='70007100') $EM_CODE = "70007100"; // �ǪʶԵ�
			elseif ($MAJORCODE=='70008100') $EM_CODE = "70008100"; // ������Ǫ��ʵ�� (Pediatrics)
			elseif ($MAJORCODE=='70008101') $EM_CODE = "70008101"; // ������Ǫ��ʵ�� �ä���ʹ (Pediatric Haematology)
			elseif ($MAJORCODE=='70008102') $EM_CODE = "70008102"; // ͹��Ңҡ�����Ǫ��ʵ�� �ä���� (Pediatric Cardiology)
			elseif ($MAJORCODE=='70008103') $EM_CODE = "70008103"; // ͹��Ңҡ�����Ǫ��ʵ�� �ä� (Pediatric Nephrology)
			elseif ($MAJORCODE=='70008104') $EM_CODE = "70008104"; // ͹��Ңҡ�����Ǫ��ʵ�� �ä�к�������� (Pediatric Respiratory Diseases)
			elseif ($MAJORCODE=='70008106') $EM_CODE = "70008106"; // ͹��Ңҡ�����Ǫ��ʵ���ä�Դ���� (Pediatric Infectious Diseases)
			elseif ($MAJORCODE=='70008107') $EM_CODE = "70008107"; // ͹��Ңҡ�����Ǫ��ʵ���á�á�Դ��л�ԡ��Դ (Neonatal-Perinatal Medicine)
			elseif ($MAJORCODE=='70008108') $EM_CODE = "70008108"; // ͹��Ңҡ�����Ǫ��ʵ���ä�к��ҧ�Թ���������ä�Ѻ (Pediatric Gastroenterology and Hepatology)
			elseif ($MAJORCODE=='70008109') $EM_CODE = "70008109"; // ͹��Ңҡ�����Ǫ��ʵ�����ҷ�Է�� (Pediatric Neurology)
			elseif ($MAJORCODE=='70008110') $EM_CODE = "70008110"; // ͹��Ңҡ�����Ǫ��ʵ��Ѳ�ҡ����оĵԡ��� (Developmental and Behavioral Pediatrics)
			elseif ($MAJORCODE=='70008111') $EM_CODE = "70008111"; // ͹��Ңҡ�����Ǫ��ʵ���ä������������Ԥ����ѹ (Pediatric Allergy and Immumology)
			elseif ($MAJORCODE=='70009100') $EM_CODE = "70009100"; // ����ҷ�Է�� (Neurology)
			elseif ($MAJORCODE=='70010100') $EM_CODE = "70010100"; // �ä�Դ����
			elseif ($MAJORCODE=='70012100') $EM_CODE = "70012100"; // ��Ҹ��Է��
			elseif ($MAJORCODE=='70012101') $EM_CODE = "70012101"; // ��ҸԪ���Է��
			elseif ($MAJORCODE=='70012102') $EM_CODE = "70012102"; // ��Ҹ��Է�Ҥ�ԹԤ (Clinical Pathology)
			elseif ($MAJORCODE=='70012103') $EM_CODE = "70012103"; // ��Ҹ��Է�ҷ���� (Anatomical and Clinical Pathology)
			elseif ($MAJORCODE=='70012104') $EM_CODE = "70012104"; // ��Ҹ��Է�ҡ�����Ҥ (Anatomical Pathology)
			elseif ($MAJORCODE=='70012105') $EM_CODE = "70012105"; // ��Ҹ��Է�ҡ�����Ҥ(��Ҹ��Է��)
			elseif ($MAJORCODE=='70013100') $EM_CODE = "70013100"; // �ѧ��෤�Ԥ
			elseif ($MAJORCODE=='70013101') $EM_CODE = "70013101"; // �ѧ���Է��
			elseif ($MAJORCODE=='70013102') $EM_CODE = "70013102"; // �Է����ʵ���ѧ��
			elseif ($MAJORCODE=='70013103') $EM_CODE = "70013103"; // �ѧ���ѡ��
			elseif ($MAJORCODE=='70013104') $EM_CODE = "70013104"; // �ѧ���Է�ҷ���� (General Radiology)
			elseif ($MAJORCODE=='70013105') $EM_CODE = "70013105"; // �ѧ���ԹԨ���
			elseif ($MAJORCODE=='70013106') $EM_CODE = "70013106"; // �ѧ���ѡ������Ǫ��ʵ���������� (Radiotherapy and Nuclear Medicine)
			elseif ($MAJORCODE=='70013107') $EM_CODE = "70013107"; // �ѧ���Է���ԹԨ��� (Diagnostic Radiology)
			elseif ($MAJORCODE=='70013108') $EM_CODE = "70013108"; // �ѧ���Է�����������Է�� (Therapeutic Radiology and Oncology)
			elseif ($MAJORCODE=='70014100') $EM_CODE = "70014100"; // ���ѭ���Է��
			elseif ($MAJORCODE=='70014101') $EM_CODE = "70014101"; // ͹��Ң����ѭ���Է�� ����Ѻ�������ä�ҧ�к�����ҷ (Neuroanesthesia)
			elseif ($MAJORCODE=='70015102') $EM_CODE = "70015102"; // �����Է�Ңͧ����͡���ѧ���
			elseif ($MAJORCODE=='70017100') $EM_CODE = "70017100"; // ������ʵ�� (Surgery)
			elseif ($MAJORCODE=='70017101') $EM_CODE = "70017101"; // ������ʵ������ (Surgery)
			elseif ($MAJORCODE=='70017102') $EM_CODE = "70017102"; // ������ʵ�쵡�� (Plastic Surgery)
			elseif ($MAJORCODE=='70017103') $EM_CODE = "70017103"; // ������ʵ���ǧ͡ (Thoracic Surgery)
			elseif ($MAJORCODE=='70017104') $EM_CODE = "70017104"; // ͹��Ң�������ʵ��������˭���з���˹ѡ (Colorectal Surgery)
			elseif ($MAJORCODE=='70017106') $EM_CODE = "70017106"; // ������ʵ�������Է�� (Urological Surgery)
			elseif ($MAJORCODE=='70017107') $EM_CODE = "70017107"; // ������ʵ������⸻Դԡ�� (Orthopedic Surgery)
			elseif ($MAJORCODE=='70017108') $EM_CODE = "70017108"; // ͹��Ң�������ʵ��������Է�� (Surgical Oncology)
			elseif ($MAJORCODE=='70017109') $EM_CODE = "70017109"; // ͹��Ң�������ʵ�쵡�������������ҧ�˹�� (Facial Plastic and Reconstructive Surgery)
			elseif ($MAJORCODE=='70017110') $EM_CODE = "70017110"; // ͹��Ң�������ʵ���غѵ��˵� (Trauma Surgery)
			elseif ($MAJORCODE=='70018100') $EM_CODE = "70018100"; // �����������ʵ�� (Pediatric Surgery) (4��)
			elseif ($MAJORCODE=='70020100') $EM_CODE = "70020100"; // ����ҷ������ʵ�� (Neurological Surgery)
			elseif ($MAJORCODE=='70021100') $EM_CODE = "70021100"; // ����ҷ�Է����ʵ��
			elseif ($MAJORCODE=='70023100') $EM_CODE = "70023100"; // ����⸻Դԡ��
			elseif ($MAJORCODE=='70024100') $EM_CODE = "70024100"; // �ѡ���Է�� (Ophthalmology)
			elseif ($MAJORCODE=='70025100') $EM_CODE = "70025100"; // ���Է�� (Dermatology)
			elseif ($MAJORCODE=='70026100') $EM_CODE = "70026100"; // �Ǫ��ʵ���ͺ���� (Family Medicine)
			elseif ($MAJORCODE=='70028100') $EM_CODE = "70028100"; // ͹��Ң�����秹���Ǫ�Է�� (Gynecologic Oncology)
			elseif ($MAJORCODE=='71000100') $EM_CODE = "71000100"; // �Ǫ��ʵ���ͧ�ҡ
			elseif ($MAJORCODE=='71001101') $EM_CODE = "71001101"; // �ѹ�ᾷ���ʵ��
			elseif ($MAJORCODE=='71001102') $EM_CODE = "71001102"; // �ѹ������Ѵ�ѹ
			elseif ($MAJORCODE=='71001103') $EM_CODE = "71001103"; // �ѹ�������д�ɰ�
			elseif ($MAJORCODE=='71001104') $EM_CODE = "71001104"; // �ѹ���������Ѻ��
			elseif ($MAJORCODE=='71001105') $EM_CODE = "71001105"; // �ѹ������ѵ����
			elseif ($MAJORCODE=='71001107') $EM_CODE = "71001107"; // �ѹ�����
			elseif ($MAJORCODE=='71001108') $EM_CODE = "71001108"; // �ѹ����������
			elseif ($MAJORCODE=='71002100') $EM_CODE = "71002100"; // ��Էѹ��Է��
			elseif ($MAJORCODE=='71004100') $EM_CODE = "71004100"; // �Է���͹ⴴ͹��
			elseif ($MAJORCODE=='71006100') $EM_CODE = "71006100"; // �ѹ��Ҹ�ó�آ
			elseif ($MAJORCODE=='71008100') $EM_CODE = "71008100"; // ������ʵ���ͧ�ҡ�����硫���������
			elseif ($MAJORCODE=='71008101') $EM_CODE = "71008101"; // ������ʵ���ͧ�ҡ
			elseif ($MAJORCODE=='71010100') $EM_CODE = "71010100"; // ������ʵ���ͧ�ҡ (�ѹ�������ʵ��)
			elseif ($MAJORCODE=='73000100') $EM_CODE = "73000100"; // ���Ѫ�Է��
			elseif ($MAJORCODE=='73001101') $EM_CODE = "73001101"; // ���Ѫ��ʵ��
			elseif ($MAJORCODE=='73001102') $EM_CODE = "73001102"; // ���Ѫ��ʵ�����Ҿ
			elseif ($MAJORCODE=='73001103') $EM_CODE = "73001103"; // ���Ѫ����
			elseif ($MAJORCODE=='73001108') $EM_CODE = "73001108"; // ���Ѫ���
			elseif ($MAJORCODE=='73001109') $EM_CODE = "73001109"; // ���Ѫ�Ƿ
			elseif ($MAJORCODE=='73001112') $EM_CODE = "73001112"; // ���Ѫ������Թԡ
			elseif ($MAJORCODE=='73001114') $EM_CODE = "73001114"; // ���������Ѫ�Ԩ
			elseif ($MAJORCODE=='73001123') $EM_CODE = "73001123"; // ���Ѫ���������
			elseif ($MAJORCODE=='73004100') $EM_CODE = "73004100"; // ෤�Ԥ���Ѫ����
			elseif ($MAJORCODE=='74000100') $EM_CODE = "74000100"; // ��Һ���Ҹ�ó�آ (NURSING)
			elseif ($MAJORCODE=='74001100') $EM_CODE = "74001100"; // ��Һ����ʵ��
			elseif ($MAJORCODE=='74001101') $EM_CODE = "74001101"; // ��Һ��
			elseif ($MAJORCODE=='74001103') $EM_CODE = "74001103"; // ��þ�Һ��������ҵ�� - ������ʵ��
			elseif ($MAJORCODE=='74001104') $EM_CODE = "74001104"; // ��Һ����м�ا�����
			elseif ($MAJORCODE=='74001107') $EM_CODE = "74001107"; // ��þ�Һ�ż������ä������� (�������� 3 ��͹)
			elseif ($MAJORCODE=='74001108') $EM_CODE = "74001108"; // �к����� ���������ʹ���ʹ (�������� 2 ��͹)
			elseif ($MAJORCODE=='74001109') $EM_CODE = "74001109"; // ��þ�Һ���ä������з�ǧ͡
			elseif ($MAJORCODE=='74001110') $EM_CODE = "74001110"; // ��þ�Һ������� (�������� 4 ��͹)
			elseif ($MAJORCODE=='74001111') $EM_CODE = "74001111"; // ��þ�Һ�� ��.��.�� (�������� 2 ��͹)
			elseif ($MAJORCODE=='74001112') $EM_CODE = "74001112"; // ��þ�Һ�ż������ԡĵ� (�������� 3 ��͹)
			elseif ($MAJORCODE=='74001114') $EM_CODE = "74001114"; // ��Һ���Ǫ��ԺѵԷҧ�ä��
			elseif ($MAJORCODE=='74001115') $EM_CODE = "74001115"; // ��Һ�ŷҧ�ä��
			elseif ($MAJORCODE=='74001116') $EM_CODE = "74001116"; // ��þ�Һ�š�����Ǫ��ʵ�� (�ä�Դ����)
			elseif ($MAJORCODE=='74001117') $EM_CODE = "74001117"; // ��þ�Һ�����¡����غѵ��˵�
			elseif ($MAJORCODE=='74001118') $EM_CODE = "74001118"; // ��þ�Һ���������ʵ��
			elseif ($MAJORCODE=='74001119') $EM_CODE = "74001119"; // ��þ�Һ�ż����ºҴ�� ������ ��ФǺ�����âѺ���������
			elseif ($MAJORCODE=='74001120') $EM_CODE = "74001120"; // ��þ�Һ������⸻Դԡ��
			elseif ($MAJORCODE=='74002100') $EM_CODE = "74002100"; // ��ú����á�þ�Һ��
			elseif ($MAJORCODE=='74002101') $EM_CODE = "74002101"; // �����á�þ�Һ���֡��
			elseif ($MAJORCODE=='74003100') $EM_CODE = "74003100"; // ��þ�Һ����������
			elseif ($MAJORCODE=='74005100') $EM_CODE = "74005100"; // ��þ�Һ�ż���˭�
			elseif ($MAJORCODE=='74006100') $EM_CODE = "74006100"; // ��þ�Һ���֡��
			elseif ($MAJORCODE=='74006101') $EM_CODE = "74006101"; // ��Һ���֡��
			elseif ($MAJORCODE=='74007100') $EM_CODE = "74007100"; // ��þ�Һ�Ť�ͺ����
			elseif ($MAJORCODE=='74008100') $EM_CODE = "74008100"; // ��þ�Һ�ż���٧����
			elseif ($MAJORCODE=='74009100') $EM_CODE = "74009100"; // ��þ�Һ�Ū����
			elseif ($MAJORCODE=='74009101') $EM_CODE = "74009101"; // ��þ�Һ��͹���ª����
			elseif ($MAJORCODE=='74010100') $EM_CODE = "74010100"; // ��þ�Һ�Ŵ�ҹ��äǺ�����õԴ����
			elseif ($MAJORCODE=='74011100') $EM_CODE = "74011100"; // ��þ�Һ���Ҫ��͹����
			elseif ($MAJORCODE=='74013100') $EM_CODE = "74013100"; // ��þ�Һ�ż����������
			elseif ($MAJORCODE=='74013101') $EM_CODE = "74013101"; // ��þ�Һ��੾�зҧ�ä���������Ѻ��Һ���ԪҪվ
			elseif ($MAJORCODE=='74014100') $EM_CODE = "74014100"; // ��þ�Һ���آ�Ҿ�Ե��ШԵ�Ǫ
			elseif ($MAJORCODE=='75000100') $EM_CODE = "75000100"; // �Ҹ�ó�آ
			elseif ($MAJORCODE=='75001100') $EM_CODE = "75001100"; // �������Ҹ�ó�آ
			elseif ($MAJORCODE=='75001101') $EM_CODE = "75001101"; // ��ú������Ҹ�ó�آ
			elseif ($MAJORCODE=='75003100') $EM_CODE = "75003100"; // �آ��Ժ������Ǵ����
			elseif ($MAJORCODE=='75004100') $EM_CODE = "75004100"; // �ѧ�����ᾷ������Ҹ�ó�آ
			elseif ($MAJORCODE=='75005100') $EM_CODE = "75005100"; // �Ҹ�ó�آ��ʵ��
			elseif ($MAJORCODE=='75008100') $EM_CODE = "75008100"; // ͹���ª����
			elseif ($MAJORCODE=='75011101') $EM_CODE = "75011101"; // �Ǫ��ʵ���ͧ�ѹ ᢹ��Ҫ���Ǫ��ʵ�� (Preventive Medicine : Occupational Medicine)
			elseif ($MAJORCODE=='75013100') $EM_CODE = "75013100"; // �Ҫ��͹������Ф�����ʹ���
			elseif ($MAJORCODE=='75015100') $EM_CODE = "75015100"; // �ѧ����ʵ����ᾷ������Ҹ�ó�آ
			elseif ($MAJORCODE=='75016100') $EM_CODE = "75016100"; // ��ä�����ͧ���������ҧ�Ҹ�ó�آ
			elseif ($MAJORCODE=='75017100') $EM_CODE = "75017100"; // �ѹ�ٵ��Ҹ�ó�آ
			elseif ($MAJORCODE=='75020100') $EM_CODE = "75020100"; // �����������آ�Ҿ
			elseif ($MAJORCODE=='75021100') $EM_CODE = "75021100"; // �ҹ��ԡ�ÿ�鹿����ö�Ҿ���ԡ��
			elseif ($MAJORCODE=='76000100') $EM_CODE = "76000100"; // �Է����ʵ����ᾷ��
			elseif ($MAJORCODE=='76001100') $EM_CODE = "76001100"; // �����Է��
			elseif ($MAJORCODE=='76005100') $EM_CODE = "76005100"; // ���Ե�Է�� (Haematology)
			elseif ($MAJORCODE=='76006100') $EM_CODE = "76006100"; // �Է�ҡ���кҴ
			elseif ($MAJORCODE=='76006101') $EM_CODE = "76006101"; // �Է�ҡ���кҴ�ҧ���ᾷ��
			elseif ($MAJORCODE=='76006102') $EM_CODE = "76006102"; // �кҴ�Է��
			elseif ($MAJORCODE=='76008100') $EM_CODE = "76008100"; // ���Ե�Է����и�Ҥ�����ʹ
			elseif ($MAJORCODE=='76009100') $EM_CODE = "76009100"; // �Ǫ��ʵ���ø�Ҥ�����ʹ
			elseif ($MAJORCODE=='76010100') $EM_CODE = "76010100"; // �Ǫ��ʵ������
			elseif ($MAJORCODE=='76011100') $EM_CODE = "76011100"; // �Է����ʵ���ú�ԡ�����Ե
			elseif ($MAJORCODE=='76012100') $EM_CODE = "76012100"; // ��Ū���Է�ҷҧ���ᾷ��
			elseif ($MAJORCODE=='76013100') $EM_CODE = "76013100"; // ����Ҿ�ӺѴ
			elseif ($MAJORCODE=='76014101') $EM_CODE = "76014101"; // ���ԡ����ᾷ��
			elseif ($MAJORCODE=='76017100') $EM_CODE = "76017100"; // �Ԩ�����ӺѴ
			elseif ($MAJORCODE=='76018100') $EM_CODE = "76018100"; // ����Է��
			elseif ($MAJORCODE=='76019100') $EM_CODE = "76019100"; // ����Է�ҷҧ������������ҡ��
			elseif ($MAJORCODE=='76019101') $EM_CODE = "76019101"; // �Ǫ�Է�ȹ�
			elseif ($MAJORCODE=='76021100') $EM_CODE = "76021100"; // ෤�Ԥ���ᾷ��
			elseif ($MAJORCODE=='77000100') $EM_CODE = "77000100"; // �آ�֡����оĵԡ�����ʵ��
			elseif ($MAJORCODE=='77001100') $EM_CODE = "77001100"; // �Ե�Է�Ҥ�չԤ
			elseif ($MAJORCODE=='77002100') $EM_CODE = "77002100"; // �آ�Ҿ�Ե
			elseif ($MAJORCODE=='77002101') $EM_CODE = "77002101"; // �Ǫ��ʵ���ͧ�ѹ ᢹ��آ�Ҿ�Ե����� (Preventive Medicine : Community Mental Health)
			elseif ($MAJORCODE=='77003100') $EM_CODE = "77003100"; // �Ե�Է�Ҥ�ԹԤ��Ъ����
			elseif ($MAJORCODE=='77004100') $EM_CODE = "77004100"; // �Ե�Ǫ��ʵ�� (Psychiatry)
			elseif ($MAJORCODE=='77005100') $EM_CODE = "77005100"; // �آ�Ҿ�Ե��С�þ�Һ�ŨԵ�Ǫ
			elseif ($MAJORCODE=='77006100') $EM_CODE = "77006100"; // �Ե�Ǫ��ʵ������������� (Child and Adolescent Psychiatry)
			elseif ($MAJORCODE=='77008100') $EM_CODE = "77008100"; // ���֡��
			elseif ($MAJORCODE=='77009100') $EM_CODE = "77009100"; // �Է����ʵ���آ�Ҿ
			elseif ($MAJORCODE=='78001100') $EM_CODE = "78001100"; // �Ե��Ǫ��ʵ�� (Forensic Medicine)
			elseif ($MAJORCODE=='78002101') $EM_CODE = "78002101"; // ��ú������ç��Һ��
			elseif ($MAJORCODE=='78004100') $EM_CODE = "78004100"; // �Ǫ����¹
			elseif ($MAJORCODE=='80001100') $EM_CODE = "80001100"; // ������ҧ
			elseif ($MAJORCODE=='80001101') $EM_CODE = "80001101"; // ��ҧ������ҧ
			elseif ($MAJORCODE=='80008100') $EM_CODE = "80008100"; // ෤����ա�á�����ҧ
			elseif ($MAJORCODE=='81006100') $EM_CODE = "81006100"; // ���ǡ���俿��
			elseif ($MAJORCODE=='81008100') $EM_CODE = "81008100"; // ��ҧ����硷�͹ԡ��
			elseif ($MAJORCODE=='81008102') $EM_CODE = "81008102"; // ����硷�͹ԡ��
			elseif ($MAJORCODE=='81014100') $EM_CODE = "81014100"; // ���ǡ�������硷�͹ԡ��
			elseif ($MAJORCODE=='81015100') $EM_CODE = "81015100"; // 俿��
			elseif ($MAJORCODE=='81015101') $EM_CODE = "81015101"; // ��ҧ俿��
			elseif ($MAJORCODE=='81026100') $EM_CODE = "81026100"; // 俿�ҡ��ѧ
			elseif ($MAJORCODE=='81026101') $EM_CODE = "81026101"; // ��ҧ俿�ҡ��ѧ
			elseif ($MAJORCODE=='82000101') $EM_CODE = "82000101"; // ��ҧ���ç�ҹ
			elseif ($MAJORCODE=='82005100') $EM_CODE = "82005100"; // ����ͧ��
			elseif ($MAJORCODE=='83000100') $EM_CODE = "83000100"; // ���ǡ����ص��ˡ��
			elseif ($MAJORCODE=='83001100') $EM_CODE = "83001100"; // ෤������ص��ˡ���
			elseif ($MAJORCODE=='88001101') $EM_CODE = "88001101"; // ��ҧ¹��
			elseif ($MAJORCODE=='89002100') $EM_CODE = "89002100"; // ��ҧ�Ҿ
			elseif ($MAJORCODE=='90000101') $EM_CODE = "90000101"; // �ɵá���
			elseif ($MAJORCODE=='90004100') $EM_CODE = "90004100"; // ෤����ա���ɵ�
			elseif ($MAJORCODE=='90006100') $EM_CODE = "90006100"; // �����������ɵ�
			elseif ($MAJORCODE=='92000101') $EM_CODE = "92000101"; // �ѵǺ��
			elseif ($MAJORCODE=='92000102') $EM_CODE = "92000102"; // �ѵ���ʵ��
			elseif ($MAJORCODE=='94000104') $EM_CODE = "94000104"; // �����
			elseif ($MAJORCODE=='95000101') $EM_CODE = "95000101"; // ǹ��ʵ��
			elseif ($MAJORCODE=='98002101') $EM_CODE = "98002101"; // ����к��Ң��Ԫ��͡
			$EM_CODE = (trim($EM_CODE))? "'".trim($EM_CODE)."'" : "NULL";
*/
?>