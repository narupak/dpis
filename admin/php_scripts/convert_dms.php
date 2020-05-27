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

// OK โครงสร้าง 
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

// ตำแหน่งข้าราชการ 9266
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
			if ($ADMINPOST=='024') $PM_CODE = "0297"; // วิสัญญีพยาบาล
			elseif ($ADMINPOST=='078') $PM_CODE = "0375"; // ผู้อำนวยการสถาบัน
			elseif ($ADMINPOST=='212') $PM_CODE = "0234"; // ผู้อำนวยการ
			elseif ($ADMINPOST=='213') $PM_CODE = "0357"; // อธิบดี
			elseif ($ADMINPOST=='222') $PM_CODE = "0270"; // รองผู้อำนวยการ
			elseif ($ADMINPOST=='223') $PM_CODE = "0276"; // รองอธิบดี
			elseif ($ADMINPOST=='248') $PM_CODE = "0289"; // วิชาชีพเฉพาะ (วช.)
			elseif ($ADMINPOST=='310') $PM_CODE = "0251"; // ผู้อำนวยการสำนัก
			elseif ($ADMINPOST=='311') $PM_CODE = "0283"; // เลขานุการกรม
			elseif ($ADMINPOST=='312') $PM_CODE = "0235"; // ผู้อำนวยการกอง
			elseif ($ADMINPOST=='315') $PM_CODE = "0243"; // ผู้อำนวยการศูนย์
			elseif ($ADMINPOST=='401') $PM_CODE = "0026"; // เจ้าหน้าที่วิเคราะห์นโยบายและแผน
			elseif ($ADMINPOST=='402') $PM_CODE = "9999"; // เจ้าหน้าที่บริหารงานทั่วไป
			elseif ($ADMINPOST=='403') $PM_CODE = "9999"; // บุคลากร
			elseif ($ADMINPOST=='405') $PM_CODE = "9999"; // เจ้าพนักงานธุรการ
			elseif ($ADMINPOST=='408') $PM_CODE = "9999"; // เจ้าพนักงานพัสดุ
			elseif ($ADMINPOST=='411') $PM_CODE = "9999"; // เจ้าหน้าที่บันทึกข้อมูล
			elseif ($ADMINPOST=='412') $PM_CODE = "9999"; // เจ้าหน้าที่พิมพ์ดีด
			elseif ($ADMINPOST=='416') $PM_CODE = "9999"; // นักสถิติ
			elseif ($ADMINPOST=='417') $PM_CODE = "9999"; // เจ้าหน้าที่เวชสถิติ
			elseif ($ADMINPOST=='419') $PM_CODE = "9999"; // เจ้าหน้าที่วิเทศสัมพันธ์
			elseif ($ADMINPOST=='420') $PM_CODE = "9999"; // เจ้าพนักงานการเงินและบัญชี
			elseif ($ADMINPOST=='421') $PM_CODE = "9999"; // นักวิชาการเงินและบัญชี
			elseif ($ADMINPOST=='423') $PM_CODE = "9999"; // เจ้าหน้าที่ตรวจสอบภายใน
			elseif ($ADMINPOST=='424') $PM_CODE = "9999"; // เจ้าหน้าที่ประชาสัมพันธ์
			elseif ($ADMINPOST=='427') $PM_CODE = "9999"; // เจ้าพนักงานโสตทัศนศึกษา
			elseif ($ADMINPOST=='428') $PM_CODE = "9999"; // นักวิชาการโสตทัศนศึกษา
			elseif ($ADMINPOST=='429') $PM_CODE = "0089"; // นายแพทย์
			elseif ($ADMINPOST=='430') $PM_CODE = "9999"; // ทันตแพทย์
			elseif ($ADMINPOST=='431') $PM_CODE = "9999"; // นายสัตวแพทย์
			elseif ($ADMINPOST=='432') $PM_CODE = "9999"; // นักวิทยาศาสตร์การแพทย์
			elseif ($ADMINPOST=='433') $PM_CODE = "9999"; // เภสัชกร
			elseif ($ADMINPOST=='435') $PM_CODE = "9999"; // นักโภชนาการ
			elseif ($ADMINPOST=='436') $PM_CODE = "9999"; // นักจิตวิทยา
			elseif ($ADMINPOST=='438') $PM_CODE = "9999"; // พยาบาลเทคนิค
			elseif ($ADMINPOST=='440') $PM_CODE = "9999"; // พยาบาลวิชาชีพ
			elseif ($ADMINPOST=='443') $PM_CODE = "9999"; // เจ้าหน้าที่รังสีการแพทย์
			elseif ($ADMINPOST=='444') $PM_CODE = "9999"; // นักรังสีการแพทย์
			elseif ($ADMINPOST=='445') $PM_CODE = "9999"; // นักกายภาพบำบัด
			elseif ($ADMINPOST=='446') $PM_CODE = "9999"; // เจ้าหน้าที่อาชีวบำบัด
			elseif ($ADMINPOST=='447') $PM_CODE = "9999"; // นักอาชีวบำบัด
			elseif ($ADMINPOST=='448') $PM_CODE = "9999"; // เจ้าพนักงานวิทยาศาสตร์การแพทย์
			elseif ($ADMINPOST=='449') $PM_CODE = "9999"; // เจ้าพนักงานเภสัชกรรม
			elseif ($ADMINPOST=='450') $PM_CODE = "9999"; // นักวิชาการสาธารณสุข
			elseif ($ADMINPOST=='451') $PM_CODE = "9999"; // ผู้ช่วยทันตแพทย์
			elseif ($ADMINPOST=='452') $PM_CODE = "9999"; // เจ้าพนักงานเวชกรรมฟื้นฟู
			elseif ($ADMINPOST=='453') $PM_CODE = "9999"; // นักเทคนิคการแพทย์
			elseif ($ADMINPOST=='459') $PM_CODE = "9999"; // ช่างกายอุปกรณ์
			elseif ($ADMINPOST=='461') $PM_CODE = "9999"; // นักวิชาการศึกษาพิเศษ
			elseif ($ADMINPOST=='462') $PM_CODE = "9999"; // บรรณารักษ์
			elseif ($ADMINPOST=='464') $PM_CODE = "9999"; // นักสังคมสงเคราะห์
			elseif ($ADMINPOST=='465') $PM_CODE = "9999"; // นักวิชาการคอมพิวเตอร์
			elseif ($ADMINPOST=='469') $PM_CODE = "9999"; // นักกายอุปกรณ์
			elseif ($ADMINPOST=='470') $PM_CODE = "9999"; // นักกิจกรรมบำบัด
			elseif ($ADMINPOST=='471') $PM_CODE = "9999"; // นักเทคโนโลยีหัวใจและทรวงอก
			elseif ($ADMINPOST=='901') $PM_CODE = "0194"; // ผู้ชำนาญการ
			elseif ($ADMINPOST=='902') $PM_CODE = "0200"; // ผู้ชำนาญการพิเศษ
			elseif ($ADMINPOST=='903') $PM_CODE = "0203"; // ผู้เชี่ยวชาญ
			elseif ($ADMINPOST=='904') $PM_CODE = "0207"; // ผู้เชี่ยวชาญพิเศษ
			elseif ($ADMINPOST=='948') $PM_CODE = "0033"; // ว
			elseif ($ADMINPOST=='949') $PM_CODE = "0034"; // วช
			elseif ($ADMINPOST=='956') $PM_CODE = "0380"; // ผู้อำนวยการโรงพยาบาล
			elseif ($ADMINPOST=='958') $PM_CODE = "0026"; // เจ้าหน้าที่วิเคราะห์นโยบายและแผน
			elseif ($ADMINPOST=='965') $PM_CODE = "0033"; // ตำแหน่งที่มีประสบการณ์ (ว)
			elseif ($ADMINPOST=='966') $PM_CODE = "0034"; // ตำแหน่งวิชาชีพ (วช.)
			elseif ($ADMINPOST=='999') $PM_CODE = "9999"; // ไม่ใช่ตำแหน่งทางบริหาร/วิชาการ
			if ($ADMINPOST && !$PM_CODE) {
				$ADMIN_POST = '0' . $ADMINPOST;
				$cmd = " SELECT PM_NAME FROM PER_MGT WHERE PM_CODE = '$ADMIN_POST' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd<br>";
				$data = $db_dpis->get_array();
				$PM_NAME = trim($data[PM_NAME]);
				if (!$PM_NAME) echo "ตำแหน่งในการบริหารงาน $ADMINPOST<br>";
			}
			$PM_CODE = (trim($PM_CODE))? "'".trim($PM_CODE)."'" : "NULL";

			$PL_CODE = "";
			if ($POSCODE=='10108') $PL_CODE = "510108"; // นักบริหาร
			elseif ($POSCODE=='10703') $PL_CODE = "510703"; // นักวิเคราะห์นโยบายและแผน
			elseif ($POSCODE=='11103') $PL_CODE = "511104"; // นักจัดการงานทั่วไป
			elseif ($POSCODE=='11104') $PL_CODE = "511612"; // เจ้าพนักงานธุรการ
			elseif ($POSCODE=='11403') $PL_CODE = "510903"; // นักทรัพยากรบุคคล
			elseif ($POSCODE=='11612') $PL_CODE = "511612"; // เจ้าพนักงานธุรการ
			elseif ($POSCODE=='11614') $PL_CODE = "511612"; // เจ้าพนักงานธุรการ
			elseif ($POSCODE=='11712') $PL_CODE = "511712"; // เจ้าพนักงานพัสดุ
			elseif ($POSCODE=='11723') $PL_CODE = "511723"; // นักวิชาการพัสดุ
			elseif ($POSCODE=='11730') $PL_CODE = "511723"; // นักวิชาการพัสดุ
			elseif ($POSCODE=='11731') $PL_CODE = "511712"; // เจ้าพนักงานพัสดุ
			elseif ($POSCODE=='11801') $PL_CODE = "511612"; // เจ้าพนักงานธุรการ
			elseif ($POSCODE=='12201') $PL_CODE = "512212"; // เจ้าพนักงานสถิติ
			elseif ($POSCODE=='12212') $PL_CODE = "512212"; // เจ้าพนักงานสถิติ
			elseif ($POSCODE=='12223') $PL_CODE = "512003"; // นักวิชาการสถิติ
			elseif ($POSCODE=='12302') $PL_CODE = "512302"; // เจ้าพนักงานเวชสถิติ
			elseif ($POSCODE=='12403') $PL_CODE = "512403"; // นิติกร
			elseif ($POSCODE=='13003') $PL_CODE = "513003"; // นักวิเทศสัมพันธ์
			elseif ($POSCODE=='20412') $PL_CODE = "520412"; // เจ้าพนักงานการเงินและบัญชี
			elseif ($POSCODE=='20423') $PL_CODE = "520423"; // นักวิชาการเงินและบัญชี
			elseif ($POSCODE=='20430') $PL_CODE = "520423"; // นักวิชาการเงินและบัญชี
			elseif ($POSCODE=='20431') $PL_CODE = "520412"; // เจ้าพนักงานการเงินและบัญชี
			elseif ($POSCODE=='20603') $PL_CODE = "520603"; // นักวิชาการตรวจสอบภายใน
			elseif ($POSCODE=='31801') $PL_CODE = "531801"; // เจ้าพนักงานเผยแพร่ประชาสัมพันธ์
			elseif ($POSCODE=='31813') $PL_CODE = "531813"; // นักประชาสัมพันธ์
			elseif ($POSCODE=='32501') $PL_CODE = "532512"; // เจ้าพนักงานโสตทัศนศึกษา
			elseif ($POSCODE=='32512') $PL_CODE = "532512"; // เจ้าพนักงานโสตทัศนศึกษา
			elseif ($POSCODE=='32523') $PL_CODE = "532523"; // นักวิชาการโสตทัศนศึกษา
			elseif ($POSCODE=='60104') $PL_CODE = "560104"; // นายแพทย์
			elseif ($POSCODE=='60204') $PL_CODE = "560204"; // ทันตแพทย์
			elseif ($POSCODE=='60304') $PL_CODE = "560304"; // นายสัตวแพทย์
			elseif ($POSCODE=='60403') $PL_CODE = "560403"; // นักวิทยาศาสตร์การแพทย์
			elseif ($POSCODE=='60603') $PL_CODE = "560603"; // เภสัชกร
			elseif ($POSCODE=='60802') $PL_CODE = "560802"; // โภชนากร
			elseif ($POSCODE=='60813') $PL_CODE = "560813"; // นักโภชนาการ
			elseif ($POSCODE=='61203') $PL_CODE = "561204"; // นักจิตวิทยาคลินิก
			elseif ($POSCODE=='61303') $PL_CODE = "562503"; // นักวิชาการสาธารณสุข
			elseif ($POSCODE=='61502') $PL_CODE = "561502"; // พยาบาลเทคนิค
			elseif ($POSCODE=='61510') $PL_CODE = "561514"; // นักวิชาการพยาบาล
			elseif ($POSCODE=='61523') $PL_CODE = "561523"; // พยาบาลวิชาชีพ
			elseif ($POSCODE=='61601') $PL_CODE = "561502"; // พยาบาลเทคนิค
			elseif ($POSCODE=='61701') $PL_CODE = "561712"; // เจ้าพนักงานรังสีการแพทย์
			elseif ($POSCODE=='61712') $PL_CODE = "561712"; // เจ้าพนักงานรังสีการแพทย์
			elseif ($POSCODE=='61723') $PL_CODE = "561723"; // นักรังสีการแพทย์
			elseif ($POSCODE=='61803') $PL_CODE = "561803"; // นักกายภาพบำบัด
			elseif ($POSCODE=='61902') $PL_CODE = "561902"; // เจ้าหน้าที่อาชีวบำบัด
			elseif ($POSCODE=='61913') $PL_CODE = "561914"; // นักกิจกรรมบำบัด
			elseif ($POSCODE=='62212') $PL_CODE = "562212"; // เจ้าพนักงานวิทยาศาสตร์การแพทย์
			elseif ($POSCODE=='62312') $PL_CODE = "562312"; // เจ้าพนักงานเภสัชกรรม
			elseif ($POSCODE=='62503') $PL_CODE = "562503"; // นักวิชาการสาธารณสุข
			elseif ($POSCODE=='62601') $PL_CODE = "562802"; // เจ้าพนักงานทันตสาธารณสุข
			elseif ($POSCODE=='62712') $PL_CODE = "562712"; // เจ้าพนักงานเวชกรรมฟื้นฟู
			elseif ($POSCODE=='63603') $PL_CODE = "563603"; // นักเทคนิคการแพทย์
			elseif ($POSCODE=='63700') $PL_CODE = "561914"; // นักกิจกรรมบำบัด
			elseif ($POSCODE=='63800') $PL_CODE = "536020"; // นักเทคโนโลยีหัวใจและทรวงอก
			elseif ($POSCODE=='73212') $PL_CODE = "573012"; // นายช่างไฟฟ้า
			elseif ($POSCODE=='73213') $PL_CODE = "573012"; // นายช่างไฟฟ้า
			elseif ($POSCODE=='73512') $PL_CODE = "573512"; // นายช่างเทคนิค
			elseif ($POSCODE=='74412') $PL_CODE = "574412"; // นายช่างศิลป์
			elseif ($POSCODE=='75003') $PL_CODE = "575003"; // ช่างภาพการแพทย์
			elseif ($POSCODE=='75601') $PL_CODE = "575602"; // ช่างกายอุปกรณ์
			elseif ($POSCODE=='75602') $PL_CODE = "575612"; // นักกายอุปกรณ์
			elseif ($POSCODE=='75702') $PL_CODE = "575702"; // ช่างทันตกรรม
			elseif ($POSCODE=='80513') $PL_CODE = "561915"; // นักเวชศาสตร์การสื่อความหมาย
			elseif ($POSCODE=='80514') $PL_CODE = "580513"; // นักวิชาการศึกษาพิเศษ
			elseif ($POSCODE=='81303') $PL_CODE = "581303"; // บรรณารักษ์
			elseif ($POSCODE=='81501') $PL_CODE = "581501"; // เจ้าพนักงานห้องสมุด
			elseif ($POSCODE=='82903') $PL_CODE = "582903"; // นักสังคมสงเคราะห์
			elseif ($POSCODE=='99070') $PL_CODE = "511013"; // นักวิชาการคอมพิวเตอร์
			elseif ($POSCODE=='99071') $PL_CODE = "562212"; // เจ้าพนักงานวิทยาศาสตร์การแพทย์
			elseif ($POSCODE=='99072') $PL_CODE = "513003"; // นักวิเทศสัมพันธ์

			if ($POSCODE && !$PL_CODE) {
				$POS_CODE = '0' . $POSCODE;
				$cmd = " SELECT PL_NAME FROM PER_LINE WHERE PL_CODE = '$POS_CODE' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd<br>";
				$data = $db_dpis->get_array();
				$PL_NAME = trim($data[PL_NAME]);
				if (!$PL_NAME) echo "ตำแหน่งในสายงาน $POSCODE<br>";
			}
			$PL_CODE = (trim($PL_CODE))? "'".trim($PL_CODE)."'" : "NULL";

			$SKILL_CODE = "";
			if ($EXPPOST=='01002') $SKILL_CODE = "002"; // ด้านเวชกรรม สาขาจักษุวิทยา
			elseif ($EXPPOST=='01010') $SKILL_CODE = "010"; // ด้านเวชกรรม สาขาประสาทวิทยา
			elseif ($EXPPOST=='01011') $SKILL_CODE = "011"; // ด้านเวชกรรม สาขาพยาธิวิทยา
			elseif ($EXPPOST=='01017') $SKILL_CODE = "016"; // ด้านเวชกรรม สาขารังสีวิทยา
			elseif ($EXPPOST=='01020') $SKILL_CODE = "019"; // ด้านเวชกรรม สาขาวิสัญญีวิทยา
			elseif ($EXPPOST=='01021') $SKILL_CODE = "020"; // ด้านเวชกรรม สาขาเวชกรรมทั่วไป
			elseif ($EXPPOST=='01022') $SKILL_CODE = "006"; // ด้านเวชกรรมป้องกัน
			elseif ($EXPPOST=='01023') $SKILL_CODE = "022"; // ด้านเวชกรรม สาขาเวชกรรมฟื้นฟู
			elseif ($EXPPOST=='01026') $SKILL_CODE = "025"; // ด้านเวชกรรม สาขาศัลยกรรม
			elseif ($EXPPOST=='01029') $SKILL_CODE = "027"; // ด้านเวชกรรม สาขาออร์โธปิดิกส์
			elseif ($EXPPOST=='01031') $SKILL_CODE = "029"; // ด้านเวชกรรม สาขาสูติ-นรีเวชกรรม
			elseif ($EXPPOST=='01032') $SKILL_CODE = "054"; // ด้านเวชกรรม สาขาโสต ศอ นาสิก
			elseif ($EXPPOST=='01034') $SKILL_CODE = "032"; // ด้านเวชกรรม สาขาอายุรกรรม
			elseif ($EXPPOST=='01035') $SKILL_CODE = "021"; // ด้านเวชกรรม สาขามะเร็งวิทยานรีเวช
			elseif ($EXPPOST=='01037') $SKILL_CODE = "026"; // ด้านเวชกรรม สาขาประสาทศัลยกรรม
			elseif ($EXPPOST=='01038') $SKILL_CODE = "030"; // ด้านเวชกรรม สาขากุมารประสาทวิทยา
			elseif ($EXPPOST=='01040') $SKILL_CODE = "028"; // ด้านเวชกรรม สาขาประสาทรังสีวิทยา
			elseif ($EXPPOST=='01041') $SKILL_CODE = "004"; // ด้านเวชกรรม สาขาจิตเวช
			elseif ($EXPPOST=='01042') $SKILL_CODE = "007"; // ด้านเวชกรรม สาขาตจวิทยา
			elseif ($EXPPOST=='01044') $SKILL_CODE = "031"; // ด้านเวชกรรม สาขาจักษุประสาทวิทยา
			elseif ($EXPPOST=='01045') $SKILL_CODE = "543"; // ด้านส่งเสริมพัฒนา
			elseif ($EXPPOST=='01047') $SKILL_CODE = "043"; // ด้านเวชกรรม สาขาเวชกรรมทั่วไป (อุบัติเหตุและฉุกเฉิน)
			elseif ($EXPPOST=='02001') $SKILL_CODE = "033"; // ด้านทันตกรรม
			elseif ($EXPPOST=='03002') $SKILL_CODE = "037"; // ด้านเภสัชกรรมการผลิต
			elseif ($EXPPOST=='03003') $SKILL_CODE = "038"; // ด้านเภสัชกรรมคลินิก
			elseif ($EXPPOST=='05026') $SKILL_CODE = "543"; // ส่งเสริมพัฒนา
			elseif ($EXPPOST=='05028') $SKILL_CODE = "085"; // ด้านสาธารณสุข
			elseif ($EXPPOST=='17014') $SKILL_CODE = "064"; // ด้านบริการทางวิชาการ สาขาธนาคารเลือด
			elseif ($EXPPOST=='17015') $SKILL_CODE = "041"; // ด้านเวชกรรม สาขากุมารเวชกรรม
			elseif ($EXPPOST=='17016') $SKILL_CODE = "001"; // ด้านเวชกรรม
			elseif ($EXPPOST=='17017') $SKILL_CODE = "048"; // ด้านการพยาบาลผู้ป่วยผ่าตัด
			elseif ($EXPPOST=='17019') $SKILL_CODE = "009"; // ด้านบริการทางวิชาการ
			elseif ($EXPPOST=='17021') $SKILL_CODE = "042"; // ด้านเวชกรรม สาขากุมารศัลยกรรม
			elseif ($EXPPOST=='18000') $SKILL_CODE = "044"; // ด้านการพยาบาล
			elseif ($EXPPOST=='19000') $SKILL_CODE = "046"; // ด้านการพยาบาลวิสัญญี
			elseif ($EXPPOST=='30000') $SKILL_CODE = "049"; // ด้านการพยาบาลผู้ป่วยหนัก
			elseif ($EXPPOST=='40000') $SKILL_CODE = "047"; // ด้านการพยาบาลผู้คลอด
			elseif ($EXPPOST=='50000') $SKILL_CODE = "050"; // ด้านการพยาบาลผู้ป่วยอุบัติเหตุและฉุกเฉิน
			elseif ($EXPPOST=='60000') $SKILL_CODE = "058"; // ด้านการพยาบาลในการตรวจรักษาพิเศษ
			elseif ($EXPPOST=='79550') $SKILL_CODE = "441"; // ด้านวิจัย
			elseif ($EXPPOST=='90114') $SKILL_CODE = "686"; // ด้านโสตสัมผัสวิทยาหรือการแก้ไขการพูด
			elseif ($EXPPOST=='90115') $SKILL_CODE = "010"; // นักวิชาการพยาบาล 9 บส.
			elseif ($EXPPOST=='90118') $SKILL_CODE = "686"; // ด้านโสต สัมผัสวิทยา
			elseif ($EXPPOST=='90120') $SKILL_CODE = "687"; // ด้านการแก้ไขการพูด
			elseif ($EXPPOST=='90122') $SKILL_CODE = "108"; // ด้านสาธารณสุข สาขาพัฒนาระบบการถ่ายทอดความรู้และเทคโนโลยีทางการแพทย์
			elseif ($EXPPOST=='90124') $SKILL_CODE = "012"; // ด้านพัฒนาระบบบริหาร
			elseif ($EXPPOST=='99999') $SKILL_CODE = "683"; // ไม่มีสาขาชำนาญการ
			$SKILL_CODE = (trim($SKILL_CODE))? "'".trim($SKILL_CODE)."'" : "NULL";

			if ($PT_CODE == '10') $PT_CODE = "11";
			$cmd = " SELECT MISCNAME_NEW FROM STATICDETAIL WHERE GCODE = '6' AND CODE = '$LEVRAN' ";
			$db_att1->send_cmd($cmd);
//			$db_att1->show_error();
			$data1 = $db_att1->get_array();
			$CL_NAME = trim($data1[MISCNAME_NEW]);
			$CL_NAME = str_replace("/", " หรือ ", $CL_NAME);
			$CL_NAME = str_replace("ระดับ", "", $CL_NAME);

			$cmd = " SELECT CL_NAME FROM PER_CO_LEVEL WHERE CL_NAME = '$CL_NAME' ";
			$count_data = $db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "$cmd<br>";
			if (!$count_data) echo "ช่วงระดับตำแหน่ง $CL_NAME<br>";

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
// ข้าราชการ 8587
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
			if (substr($PER_FATHERNAME,0,3)=="นาย") {
				$PN_CODE_F = "003";
				$PER_FATHERNAME = substr($PER_FATHERNAME,3);
			}
			$PER_FATHERSURNAME = $arr_temp[1]." ".$arr_temp[2];
			$MOTHERNAME = trim($data[MOTHERNAME]);
			$arr_temp = explode("  ", $MOTHERNAME);
			$PER_MOTHERNAME = $arr_temp[0];
			$PN_CODE_M = "";
			if (substr($PER_MOTHERNAME,0,3)=="นาง") {
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

			$MOV_CODE = "213"; // รอแก้
			$PER_ORDAIN = 0;
			$PER_SOLDIER = 0; 
			$PER_MEMBER = 0;
//			$per_retiredate = (substr($BIRTH,0,4) + 60).'-'.substr($BIRTH,5,2).'-'.substr($BIRTH,8,2).' 00:00:00';

			if (!$SEX) {
				if ($TITLE=='1') $SEX = "1"; // ชาย
				else $SEX = "2"; // หญิง
			}

			$PN_CODE = "";
			if ($TITLE=='1') $PN_CODE = "003"; // นาย
			elseif ($TITLE=='2') $PN_CODE = "005"; // นาง
			elseif ($TITLE=='3') $PN_CODE = "004"; // นางสาว
			elseif ($TITLE=='17') $PN_CODE = "669"; // ร้อยตำรวจโท
			elseif ($TITLE=='18') $PN_CODE = "667"; // ร้อยตำรวจเอก
			elseif ($TITLE=='19') $PN_CODE = "665"; // พันตำรวจตรี
			elseif ($TITLE=='33') $PN_CODE = "223"; // สิบเอก
			elseif ($TITLE=='37') $PN_CODE = "219"; // ว่าที่ร้อยตรี
			elseif ($TITLE=='40') $PN_CODE = "214"; // ร้อยเอก
			elseif ($TITLE=='41') $PN_CODE = "212"; // พันตรี
			elseif ($TITLE=='42') $PN_CODE = "210"; // พันโท
			elseif ($TITLE=='43') $PN_CODE = "208"; // พันเอก
			elseif ($TITLE=='77') $PN_CODE = "520"; // พันจ่าอากาศเอก
			elseif ($TITLE=='82') $PN_CODE = "510"; // น.ท.
			elseif ($TITLE=='89') $PN_CODE = "519"; // พ.อ.อ.หญิง
			elseif ($TITLE=='93') $PN_CODE = "674"; // สิบตำรวจตรีหญิง
			elseif ($TITLE=='94') $PN_CODE = "378"; // จ่าเอกหญิง
			elseif ($TITLE=='96') $PN_CODE = "217"; // ว่าที่ร้อยโท
			elseif ($TITLE=='97') $PN_CODE = "733"; // ร.ต.ท.หญิง
			elseif ($TITLE=='98') $PN_CODE = "734"; // พันตรีหญิง
			elseif ($TITLE=='99') $PN_CODE = "735"; // เรือตรีหญิง
			elseif ($TITLE=='100') $PN_CODE = "736"; // สิบโทหญิง
			elseif ($TITLE=='102') $PN_CODE = "130"; //	คุณหญิง
			elseif ($TITLE=='103') $PN_CODE = "214"; //	ร้อยเอก
			elseif ($TITLE=='104') $PN_CODE = "219"; //	ว่าที่ร้อยตรี
			elseif ($TITLE=='106') $PN_CODE = "229"; //	ว่าที่ ร.ต.หญิง
			elseif ($TITLE=='107') $PN_CODE = "374"; //	จ่าเอก
			elseif ($TITLE=='108') $PN_CODE = "737"; // ร้อยโทหญิง
			elseif ($TITLE=='109') $PN_CODE = "213"; //	ว่าที่พันตรี
			elseif ($TITLE=='110') $PN_CODE = "738"; // ร้อยตรีหญิง
			elseif ($TITLE=='112') $PN_CODE = "514"; //	เรืออากาศเอก
			elseif ($TITLE=='113') $PN_CODE = "516"; //	เรืออากาศโท
			elseif ($TITLE=='114') $PN_CODE = "512"; //	นาวาอากาศตรี
			elseif ($TITLE=='115') $PN_CODE = "229"; //	ว่าที่ร้อยตรีหญิง
			elseif ($TITLE=='116') $PN_CODE = "739"; // ว่าที่ร้อยตำรวจโทหญิง
			elseif ($TITLE=='117') $PN_CODE = "365"; //	เรือเอก
			elseif ($TITLE=='118') $PN_CODE = "740"; // นาวาอากาศโทหญิง
			elseif ($TITLE=='119') $PN_CODE = "741"; // เรืออากาศเอกหญิง
			elseif ($TITLE=='122') $PN_CODE = "399"; // จ่าสิบเอกหญิง
			else echo "คำนำหน้าชื่อ $TITLE $FNAME $LNAME<br>";
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

			$PER_MGTSALARY = 0; // รอแก้
			$MOV_CODE = "213"; // รอแก้
			$PER_ORDAIN = 0;
			$PER_SOLDIER = 0; 
			$PER_MEMBER = 0;

			$PN_CODE = "";
			if ($TITLE=='1') $PN_CODE = "003"; // นาย
			elseif ($TITLE=='2') $PN_CODE = "005"; // นาง
			elseif ($TITLE=='3') $PN_CODE = "004"; // นางสาว
			elseif ($TITLE=='17') $PN_CODE = "669"; // ร้อยตำรวจโท
			elseif ($TITLE=='18') $PN_CODE = "667"; // ร้อยตำรวจเอก
			elseif ($TITLE=='19') $PN_CODE = "665"; // พันตำรวจตรี
			elseif ($TITLE=='33') $PN_CODE = "223"; // สิบเอก
			elseif ($TITLE=='37') $PN_CODE = "219"; // ว่าที่ร้อยตรี
			elseif ($TITLE=='40') $PN_CODE = "214"; // ร้อยเอก
			elseif ($TITLE=='41') $PN_CODE = "212"; // พันตรี
			elseif ($TITLE=='42') $PN_CODE = "210"; // พันโท
			elseif ($TITLE=='43') $PN_CODE = "208"; // พันเอก
			elseif ($TITLE=='77') $PN_CODE = "520"; // พันจ่าอากาศเอก
			elseif ($TITLE=='82') $PN_CODE = "510"; // น.ท.
			elseif ($TITLE=='89') $PN_CODE = "519"; // พ.อ.อ.หญิง
			elseif ($TITLE=='92') $PN_CODE = "741"; //	เรืออากาศเอกหญิง
			elseif ($TITLE=='93') $PN_CODE = "674"; // สิบตำรวจตรีหญิง
			elseif ($TITLE=='94') $PN_CODE = "378"; // จ่าเอกหญิง
			elseif ($TITLE=='96') $PN_CODE = "217"; // ว่าที่ร้อยโท
			elseif ($TITLE=='97') $PN_CODE = "733"; // ร.ต.ท.หญิง
			elseif ($TITLE=='98') $PN_CODE = "734"; // พันตรีหญิง
			elseif ($TITLE=='99') $PN_CODE = "735"; // เรือตรีหญิง
			elseif ($TITLE=='100') $PN_CODE = "736"; // สิบโทหญิง
			elseif ($TITLE=='102') $PN_CODE = "130"; //	คุณหญิง
			elseif ($TITLE=='103') $PN_CODE = "214"; //	ร้อยเอก
			elseif ($TITLE=='106') $PN_CODE = "229"; //	ว่าที่ ร.ต.หญิง
			elseif ($TITLE=='107') $PN_CODE = "374"; //	จ่าเอก
			elseif ($TITLE=='108') $PN_CODE = "737"; // ร้อยโทหญิง
			elseif ($TITLE=='109') $PN_CODE = "213"; //	ว่าที่พันตรี
			elseif ($TITLE=='110') $PN_CODE = "738"; // ร้อยตรีหญิง
			elseif ($TITLE=='112') $PN_CODE = "514"; //	เรืออากาศเอก
			elseif ($TITLE=='113') $PN_CODE = "516"; //	เรืออากาศโท
			elseif ($TITLE=='114') $PN_CODE = "512"; //	นาวาอากาศตรี
			elseif ($TITLE=='115') $PN_CODE = "229"; //	ว่าที่ร้อยตรีหญิง
			elseif ($TITLE=='116') $PN_CODE = "739"; // ว่าที่ร้อยตำรวจโทหญิง
			elseif ($TITLE=='117') $PN_CODE = "365"; //	เรือเอก
			elseif ($TITLE=='118') $PN_CODE = "740"; // นาวาอากาศโทหญิง
			elseif ($TITLE=='119') $PN_CODE = "741"; // เรืออากาศเอกหญิง
			$PN_CODE = (trim($PN_CODE))? "'".trim($PN_CODE)."'" : "'001'";

			$ASS_ORG_ID = "NULL";
			$OT_CODE = "01";
			$PER_STATUS = 2;
			if (!$MARITAL || $MARITAL == '0') $MARITAL = '1';
			if (!$SEX) 
				if ($TITLE=='1') $SEX = "1"; // นาย
				elseif ($TITLE=='2' || $TITLE=='3') $SEX = "2"; // นาง

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
			if ($ADMINPOST=='024') $PM_CODE = "0297"; // วิสัญญีพยาบาล
			elseif ($ADMINPOST=='078') $PM_CODE = "0375"; // ผู้อำนวยการสถาบัน
			elseif ($ADMINPOST=='212') $PM_CODE = "0234"; // ผู้อำนวยการ
			elseif ($ADMINPOST=='213') $PM_CODE = "0357"; // อธิบดี
			elseif ($ADMINPOST=='222') $PM_CODE = "0270"; // รองผู้อำนวยการ
			elseif ($ADMINPOST=='223') $PM_CODE = "0276"; // รองอธิบดี
			elseif ($ADMINPOST=='248') $PM_CODE = "0289"; // วิชาชีพเฉพาะ (วช.)
			elseif ($ADMINPOST=='310') $PM_CODE = "0251"; // ผู้อำนวยการสำนัก
			elseif ($ADMINPOST=='311') $PM_CODE = "0283"; // เลขานุการกรม
			elseif ($ADMINPOST=='312') $PM_CODE = "0235"; // ผู้อำนวยการกอง
			elseif ($ADMINPOST=='315') $PM_CODE = "0243"; // ผู้อำนวยการศูนย์
			elseif ($ADMINPOST=='401') $PM_CODE = "0026"; // เจ้าหน้าที่วิเคราะห์นโยบายและแผน
			elseif ($ADMINPOST=='402') $PM_CODE = "9999"; // เจ้าหน้าที่บริหารงานทั่วไป
			elseif ($ADMINPOST=='403') $PM_CODE = "9999"; // บุคลากร
			elseif ($ADMINPOST=='405') $PM_CODE = "9999"; // เจ้าพนักงานธุรการ
			elseif ($ADMINPOST=='408') $PM_CODE = "9999"; // เจ้าพนักงานพัสดุ
			elseif ($ADMINPOST=='411') $PM_CODE = "9999"; // เจ้าหน้าที่บันทึกข้อมูล
			elseif ($ADMINPOST=='412') $PM_CODE = "9999"; // เจ้าหน้าที่พิมพ์ดีด
			elseif ($ADMINPOST=='416') $PM_CODE = "9999"; // นักสถิติ
			elseif ($ADMINPOST=='417') $PM_CODE = "9999"; // เจ้าหน้าที่เวชสถิติ
			elseif ($ADMINPOST=='419') $PM_CODE = "9999"; // เจ้าหน้าที่วิเทศสัมพันธ์
			elseif ($ADMINPOST=='420') $PM_CODE = "9999"; // เจ้าพนักงานการเงินและบัญชี
			elseif ($ADMINPOST=='421') $PM_CODE = "9999"; // นักวิชาการเงินและบัญชี
			elseif ($ADMINPOST=='424') $PM_CODE = "9999"; // เจ้าหน้าที่ประชาสัมพันธ์
			elseif ($ADMINPOST=='427') $PM_CODE = "9999"; // เจ้าพนักงานโสตทัศนศึกษา
			elseif ($ADMINPOST=='428') $PM_CODE = "9999"; // นักวิชาการโสตทัศนศึกษา
			elseif ($ADMINPOST=='429') $PM_CODE = "0089"; // นายแพทย์
			elseif ($ADMINPOST=='430') $PM_CODE = "9999"; // ทันตแพทย์
			elseif ($ADMINPOST=='433') $PM_CODE = "9999"; // เภสัชกร
			elseif ($ADMINPOST=='435') $PM_CODE = "9999"; // นักโภชนาการ
			elseif ($ADMINPOST=='438') $PM_CODE = "9999"; // พยาบาลเทคนิค
			elseif ($ADMINPOST=='440') $PM_CODE = "9999"; // พยาบาลวิชาชีพ
			elseif ($ADMINPOST=='443') $PM_CODE = "9999"; // เจ้าหน้าที่รังสีการแพทย์
			elseif ($ADMINPOST=='444') $PM_CODE = "9999"; // นักรังสีการแพทย์
			elseif ($ADMINPOST=='445') $PM_CODE = "9999"; // นักกายภาพบำบัด
			elseif ($ADMINPOST=='447') $PM_CODE = "9999"; // นักอาชีวบำบัด
			elseif ($ADMINPOST=='448') $PM_CODE = "9999"; // เจ้าพนักงานวิทยาศาสตร์การแพทย์
			elseif ($ADMINPOST=='449') $PM_CODE = "9999"; // เจ้าพนักงานเภสัชกรรม
			elseif ($ADMINPOST=='450') $PM_CODE = "9999"; // นักวิชาการสาธารณสุข
			elseif ($ADMINPOST=='452') $PM_CODE = "9999"; // เจ้าพนักงานเวชกรรมฟื้นฟู
			elseif ($ADMINPOST=='453') $PM_CODE = "9999"; // นักเทคนิคการแพทย์
			elseif ($ADMINPOST=='459') $PM_CODE = "9999"; // ช่างกายอุปกรณ์
			elseif ($ADMINPOST=='462') $PM_CODE = "9999"; // บรรณารักษ์
			elseif ($ADMINPOST=='465') $PM_CODE = "9999"; // นักวิชาการคอมพิวเตอร์
			elseif ($ADMINPOST=='901') $PM_CODE = "0194"; // ผู้ชำนาญการ
			elseif ($ADMINPOST=='902') $PM_CODE = "0200"; // ผู้ชำนาญการพิเศษ
			elseif ($ADMINPOST=='903') $PM_CODE = "0203"; // ผู้เชี่ยวชาญ
			elseif ($ADMINPOST=='904') $PM_CODE = "0207"; // ผู้เชี่ยวชาญพิเศษ
			elseif ($ADMINPOST=='948') $PM_CODE = "0033"; // ว
			elseif ($ADMINPOST=='949') $PM_CODE = "0034"; // วช
			elseif ($ADMINPOST=='956') $PM_CODE = "0380"; // ผู้อำนวยการโรงพยาบาล
			elseif ($ADMINPOST=='958') $PM_CODE = "0026"; // เจ้าหน้าที่วิเคราะห์นโยบายและแผน
			elseif ($ADMINPOST=='965') $PM_CODE = "0033"; // ตำแหน่งที่มีประสบการณ์ (ว)
			elseif ($ADMINPOST=='966') $PM_CODE = "0034"; // ตำแหน่งวิชาชีพ (วช.)
			elseif ($ADMINPOST=='999') $PM_CODE = "9999"; // ไม่ใช่ตำแหน่งทางบริหาร/วิชาการ
			if ($ADMINPOST && !$PM_CODE) {
				$ADMIN_POST = '0' . $ADMINPOST;
				$cmd = " SELECT PM_NAME FROM PER_MGT WHERE PM_CODE = '$ADMIN_POST' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd<br>";
				$data = $db_dpis->get_array();
				$PM_NAME = trim($data[PM_NAME]);
//				if (!$PM_NAME) echo "ตำแหน่งในการบริหารงาน $ADMINPOST<br>";
			}
			$PM_CODE = (trim($PM_CODE))? "'".trim($PM_CODE)."'" : "NULL";

			$PL_CODE = "";
			if ($POSCODE=='10108') $PL_CODE = "010108"; // นักบริหาร
			elseif ($POSCODE=='10403') $PL_CODE = "010403"; // เจ้าพนักงานปกครอง
			elseif ($POSCODE=='10501') $PL_CODE = "010501"; // เจ้าหน้าที่ปกครอง
			elseif ($POSCODE=='10703') $PL_CODE = "010703"; // เจ้าหน้าที่วิเคราะห์นโยบายและแผน
			elseif ($POSCODE=='10903') $PL_CODE = "010903"; // เจ้าหน้าที่วิเคราะห์งานบุคคล
			elseif ($POSCODE=='11003') $PL_CODE = "011003"; // เจ้าหน้าที่ระบบงานคอมพิวเตอร์
			elseif ($POSCODE=='11103') $PL_CODE = "011103"; // เจ้าหน้าที่บริหารงานทั่วไป
			elseif ($POSCODE=='11403') $PL_CODE = "011403"; // บุคลากร
			elseif ($POSCODE=='11612') $PL_CODE = "011612"; // เจ้าพนักงานธุรการ
			elseif ($POSCODE=='11613') $PL_CODE = "011901"; // เจ้าหน้าที่พิมพ์ดีด
			elseif ($POSCODE=='11614') $PL_CODE = "011601"; // เจ้าหน้าที่ธุรการ
			elseif ($POSCODE=='11701') $PL_CODE = "011701"; // เจ้าหน้าที่พัสดุ
			elseif ($POSCODE=='11712') $PL_CODE = "011712"; // เจ้าพนักงานพัสดุ
			elseif ($POSCODE=='11723') $PL_CODE = "011723"; // นักวิชาการพัสดุ
			elseif ($POSCODE=='11730') $PL_CODE = "011734"; // เจ้าหน้าที่บริหารงานพัสดุ
			elseif ($POSCODE=='11601') $PL_CODE = "011601"; // เจ้าหน้าที่ธุรการ
			elseif ($POSCODE=='11801') $PL_CODE = "011801"; // เจ้าหน้าที่บันทึกข้อมูล
			elseif ($POSCODE=='11901') $PL_CODE = "011901"; // เจ้าหน้าที่พิมพ์ดีด
			elseif ($POSCODE=='12201') $PL_CODE = "012201"; // เจ้าหน้าที่สถิติ
			elseif ($POSCODE=='12212') $PL_CODE = "012212"; // เจ้าพนักงานสถิติ
			elseif ($POSCODE=='12223') $PL_CODE = "012223"; // นักสถิติ
			elseif ($POSCODE=='12302') $PL_CODE = "012302"; // เจ้าหน้าที่เวชสถิติ
			elseif ($POSCODE=='12403') $PL_CODE = "012403"; // นิติกร
			elseif ($POSCODE=='13003') $PL_CODE = "013003"; // เจ้าหน้าที่วิเทศสัมพันธ์
			elseif ($POSCODE=='20401') $PL_CODE = "020401"; // เจ้าหน้าที่การเงินและบัญชี
			elseif ($POSCODE=='20412') $PL_CODE = "020412"; // เจ้าพนักงานการเงินและบัญชี
			elseif ($POSCODE=='20423') $PL_CODE = "020423"; // นักวิชาการเงินและบัญชี
			elseif ($POSCODE=='20430') $PL_CODE = "020435"; // เจ้าหน้าที่บริหารงานการเงินและบัญชี
			elseif ($POSCODE=='20603') $PL_CODE = "020603"; // เจ้าหน้าที่ตรวจสอบภายใน
			elseif ($POSCODE=='20801') $PL_CODE = "020801"; // เจ้าหน้าที่ตรวจสอบบัญชี
			elseif ($POSCODE=='20823') $PL_CODE = "020823"; // นักวิชาการตรวจสอบบัญชี
			elseif ($POSCODE=='30201') $PL_CODE = "030201"; // เจ้าหน้าที่ขนส่ง
			elseif ($POSCODE=='31201') $PL_CODE = "031201"; // เจ้าหน้าที่สื่อสาร
			elseif ($POSCODE=='31801') $PL_CODE = "031801"; // เจ้าหน้าที่ประชาสัมพันธ์
			elseif ($POSCODE=='31813') $PL_CODE = "031813"; // นักประชาสัมพันธ์
			elseif ($POSCODE=='31923') $PL_CODE = "031923"; // นักการข่าว
			elseif ($POSCODE=='32401') $PL_CODE = "032401"; // เจ้าหน้าที่เผยแพร่
			elseif ($POSCODE=='32423') $PL_CODE = "032423"; // นักวิชาการเผยแพร่
			elseif ($POSCODE=='32501') $PL_CODE = "032501"; // เจ้าหน้าที่โสตทัศนศึกษา
			elseif ($POSCODE=='32512') $PL_CODE = "032512"; // เจ้าพนักงานโสตทัศนศึกษา
			elseif ($POSCODE=='32523') $PL_CODE = "032523"; // นักวิชาการโสตทัศนศึกษา
			elseif ($POSCODE=='41412') $PL_CODE = "041412"; // เจ้าพนักงานเคหกิจเกษตร
			elseif ($POSCODE=='50201') $PL_CODE = "050201"; // เจ้าหน้าที่วิทยาศาสตร์
			elseif ($POSCODE=='50212') $PL_CODE = "050212"; // เจ้าพนักงานวิทยาศาสตร์
			elseif ($POSCODE=='60104') $PL_CODE = "060104"; // นายแพทย์
			elseif ($POSCODE=='60204') $PL_CODE = "060204"; // ทันตแพทย์
			elseif ($POSCODE=='60304') $PL_CODE = "060304"; // นายสัตวแพทย์
			elseif ($POSCODE=='60403') $PL_CODE = "060403"; // นักวิทยาศาสตร์การแพทย์
			elseif ($POSCODE=='60503') $PL_CODE = "060503"; // นักเภสัชวิจัย
			elseif ($POSCODE=='60603') $PL_CODE = "060603"; // เภสัชกร
			elseif ($POSCODE=='60802') $PL_CODE = "060802"; // โภชนากร
			elseif ($POSCODE=='60813') $PL_CODE = "060813"; // นักโภชนาการ
			elseif ($POSCODE=='61203') $PL_CODE = "061203"; // นักจิตวิทยา
			elseif ($POSCODE=='61303') $PL_CODE = "061303"; // นักวิชาการสุขศึกษา
			elseif ($POSCODE=='61502') $PL_CODE = "061502"; // พยาบาลเทคนิค
			elseif ($POSCODE=='61510') $PL_CODE = "061514"; // นักวิชาการพยาบาล
			elseif ($POSCODE=='61523') $PL_CODE = "061523"; // พยาบาลวิชาชีพ
			elseif ($POSCODE=='61601') $PL_CODE = "061601"; // เจ้าหน้าที่พยาบาล
			elseif ($POSCODE=='61701') $PL_CODE = "061701"; // เจ้าหน้าที่เอ็กซเรย์
			elseif ($POSCODE=='61712') $PL_CODE = "061712"; // เจ้าหน้าที่รังสีการแพทย์
			elseif ($POSCODE=='61723') $PL_CODE = "061723"; // นักรังสีการแพทย์
			elseif ($POSCODE=='61803') $PL_CODE = "061803"; // นักกายภาพบำบัด
			elseif ($POSCODE=='61902') $PL_CODE = "061902"; // เจ้าหน้าที่อาชีวบำบัด
			elseif ($POSCODE=='61913') $PL_CODE = "061913"; // นักอาชีวบำบัด
			elseif ($POSCODE=='62002') $PL_CODE = "062002"; // ทันตานามัย
			elseif ($POSCODE=='62201') $PL_CODE = "062201"; // เจ้าหน้าที่วิทยาศาสตร์การแพทย์
			elseif ($POSCODE=='62212') $PL_CODE = "062212"; // เจ้าพนักงานวิทยาศาสตร์การแพทย์
			elseif ($POSCODE=='62301') $PL_CODE = "062301"; // ผู้ช่วยเภสัชกร
			elseif ($POSCODE=='62312') $PL_CODE = "062312"; // เจ้าพนักงานเภสัชกรรม
			elseif ($POSCODE=='62403') $PL_CODE = "062403"; // เจ้าหน้าที่เวชภัณฑ์
			elseif ($POSCODE=='62503') $PL_CODE = "062503"; // นักวิชาการสาธารณสุข
			elseif ($POSCODE=='62601') $PL_CODE = "062601"; // ผู้ช่วยทันตแพทย์
			elseif ($POSCODE=='62701') $PL_CODE = "062701"; // เจ้าหน้าที่กายภาพบำบัด
			elseif ($POSCODE=='62712') $PL_CODE = "062712"; // เจ้าพนักงานเวชกรรมฟื้นฟู
			elseif ($POSCODE=='63012') $PL_CODE = "063012"; // เจ้าพนักงานส่งเสริมสุขภาพ
			elseif ($POSCODE=='63112') $PL_CODE = "063112"; // เจ้าพนักงานควบคุมโรค
			elseif ($POSCODE=='63023') $PL_CODE = "063023"; // นักวิชาการส่งเสริมสุขภาพ
			elseif ($POSCODE=='63123') $PL_CODE = "063123"; // นักวิชาการควบคุมโรค
			elseif ($POSCODE=='63201') $PL_CODE = "063201"; // เจ้าหน้าที่สุขาภิบาล
			elseif ($POSCODE=='63502') $PL_CODE = "063502"; // เจ้าพนักงานสาธารณสุขชุมชน
			elseif ($POSCODE=='63603') $PL_CODE = "063603"; // นักเทคนิคการแพทย์
			elseif ($POSCODE=='71401') $PL_CODE = "071401"; // ช่างโยธา
			elseif ($POSCODE=='71901') $PL_CODE = "071901"; // ช่างเครื่องกล
			elseif ($POSCODE=='71912') $PL_CODE = "071912"; // นายช่างเครื่องกล
			elseif ($POSCODE=='72001') $PL_CODE = "072001"; // ช่างเครื่องยนต์
			elseif ($POSCODE=='72401') $PL_CODE = "072401"; // ช่างโลหะ
			elseif ($POSCODE=='73001') $PL_CODE = "073001"; // ช่างไฟฟ้า
			elseif ($POSCODE=='73012') $PL_CODE = "073012"; // นายช่างไฟฟ้า
			elseif ($POSCODE=='73101') $PL_CODE = "073101"; // ช่างไฟฟ้าสื่อสาร
			elseif ($POSCODE=='73201') $PL_CODE = "073201"; // ช่างอีเล็คทรอนิคส์
			elseif ($POSCODE=='73212') $PL_CODE = "073212"; // นายช่างอิเล็คทรอนิคส์
			elseif ($POSCODE=='73501') $PL_CODE = "073501"; // ช่างเทคนิค
			elseif ($POSCODE=='73512') $PL_CODE = "073512"; // นายช่างเทคนิค
			elseif ($POSCODE=='74401') $PL_CODE = "074401"; // ช่างศิลป์
			elseif ($POSCODE=='74412') $PL_CODE = "074412"; // นายช่างศิลป์
			elseif ($POSCODE=='75003') $PL_CODE = "075003"; // ช่างภาพการแพทย์
			elseif ($POSCODE=='75601') $PL_CODE = "075602"; // ช่างกายอุปกรณ์
			elseif ($POSCODE=='75602') $PL_CODE = "010108"; // นักกายอุปกรณ์
			elseif ($POSCODE=='75702') $PL_CODE = "075702"; // ช่างทันตกรรม
			elseif ($POSCODE=='80403') $PL_CODE = "080403"; // วิทยาจารย์
			elseif ($POSCODE=='80603') $PL_CODE = "080603"; // เจ้าหน้าที่ฝึกอบรม
			elseif ($POSCODE=='80513') $PL_CODE = "080513"; // นักวิชาการศึกษาพิเศษ
			elseif ($POSCODE=='81303') $PL_CODE = "081303"; // บรรณารักษ์
			elseif ($POSCODE=='81501') $PL_CODE = "081501"; // เจ้าหน้าที่ห้องสมุด
			elseif ($POSCODE=='82903') $PL_CODE = "082903"; // นักสังคมสงเคราะห์
			elseif ($POSCODE=='99070') $PL_CODE = "011013"; // นักวิชาการคอมพิวเตอร์
			elseif ($POSCODE=='99071') $PL_CODE = "062201"; // เจ้าหน้าที่วิทยาศาสตร์การแพทย์
			if ($POSCODE && !$PL_CODE) {
				$POS_CODE = '0' . $POSCODE;
				$cmd = " SELECT PL_NAME FROM PER_LINE WHERE PL_CODE = '$POS_CODE' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd<br>";
				$data = $db_dpis->get_array();
				$PL_NAME = trim($data[PL_NAME]);
				if (!$PL_NAME) echo "ตำแหน่งในสายงาน $POSCODE<br>";
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
			if ($FLOW && !$MOV_CODE) echo "ความเคลื่อนไหวตำแหน่ง $FLOW<br>";
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
			if ($SALCODE && !$MOV_CODE) echo "ความเคลื่อนไหวเงินเดือน $SALCODE<br>";
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
			if ($DOCNO=="จ.1.5/ 21 /2544 - 72/54/4" || $DOCNO=="จ.1.5/ - 0/0/0") {
				$DOCDATE = "";
			} elseif ($DOCNO=="ประกาศกรมทางหลวง- 8 ก.พ.2551" || $DOCNO=="ปะรกาศกรมทางหลวง - 8 ก.พ.2551" || $DOCNO=="ประกาศกรมทางหลวง ลงวันที่ 8 ก.พ.2551") {
				$DOCNO = "ประกาศกรมทางหลวง";
				$DOCDATE = "8 ก.พ.2551";
			} elseif ($DOCNO=="จ. 3.3/41/2545 - ลงวันที่ 19 พฤศจิกายน 2545") {
				$DOCNO = "จ. 3.3/41/2545";
				$DOCDATE = "19 พฤศจิกายน 2545";
			} elseif (strpos($DOCNO,"-ลว.") !== false) {
				$arr_temp = explode("-ลว.", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"- ลว.") !== false) {
				$arr_temp = explode("- ลว.", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"- ลว") !== false) {
				$arr_temp = explode("- ลว", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"-ลว") !== false) {
				$arr_temp = explode("-ลว", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"- ลงวันที่") !== false) {
				$arr_temp = explode("- ลงวันที่", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลงวันที่ลว.") !== false) {
				$arr_temp = explode("ลงวันที่ลว.", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลงวันที่วัน") !== false) {
				$arr_temp = explode("ลงวันที่วัน", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลงวันที่ที่") !== false) {
				$arr_temp = explode("ลงวันที่ที่", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลงวันที่ ์") !== false) {
				$arr_temp = explode("ลงวันที่ ์", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลงวันที่") !== false) {
				$arr_temp = explode("ลงวันที่", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"-") !== false && strpos($DOCNO,"ลว.") === false) {
				$arr_temp = explode("-", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลว.วันที่") !== false) {
				$arr_temp = explode("ลว.วันที่", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลว.ที่") !== false) {
				$arr_temp = explode("ลว.ที่", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลว. ที่") !== false) {
				$arr_temp = explode("ลว. ที่", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลว.อ") !== false) {
				$arr_temp = explode("ลว.อ", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลว..") !== false) {
				$arr_temp = explode("ลว..", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลวฃง") !== false) {
				$arr_temp = explode("ลวฃง", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลวหก.") !== false) {
				$arr_temp = explode("ลวหก.", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลวก.") !== false) {
				$arr_temp = explode("ลวก.", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลวส.") !== false) {
				$arr_temp = explode("ลวส.", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลว.") !== false) {
				$arr_temp = explode("ลว.", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลว,") !== false) {
				$arr_temp = explode("ลว,", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลว .") !== false) {
				$arr_temp = explode("ลว .", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลวง") !== false && strpos($DOCNO,"กรมทางหลวง") === false) {
				$arr_temp = explode("ลวง", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ลว") !== false && strpos($DOCNO,"กรมทางหลวง") === false) {
				$arr_temp = explode("ลว", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ณ วันที่ ท") !== false) {
				$arr_temp = explode("ณ วันที่ ท", $DOCNO);
				$DOCNO = $arr_temp[0];
				$DOCDATE = trim($arr_temp[1]);
			} elseif (strpos($DOCNO,"ณ วันที่") !== false) {
				$arr_temp = explode("ณ วันที่", $DOCNO);
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
				} else	if ($DOCDATE=="13 สิ.ค.2506") {
					$DOCDATE = "1963-08-13";
				} else	if ($DOCDATE=="13 มิิ.ย.2509") {
					$DOCDATE = "1966-06-13";
				} else	if ($DOCDATE=="23 มิถุนายน2509") {
					$DOCDATE = "1966-06-23";
				} else	if ($DOCDATE=="31ตค.09") {
					$DOCDATE = "1966-10-31";
				} else	if ($DOCDATE=="15พค.10") {
					$DOCDATE = "1967-05-15";
				} else	if ($DOCDATE=="31พ.ค. 2510") {
					$DOCDATE = "1967-05-31";
				} else	if ($DOCDATE=="16มิย.10") {
					$DOCDATE = "1967-06-16";
				} else	if ($DOCDATE=="26 มิถุนายน2510") {
					$DOCDATE = "1967-06-26";
				} else	if ($DOCDATE=="5กค.10") {
					$DOCDATE = "1967-07-05";
				} else	if ($DOCDATE=="18 ส.คซ2510") {
					$DOCDATE = "1967-08-18";
				} else	if ($DOCDATE=="13มิย.11") {
					$DOCDATE = "1968-06-13";
				} else	if ($DOCDATE=="26กย.11") {
					$DOCDATE = "1968-09-26";
				} else	if ($DOCDATE=="12พค.12") {
					$DOCDATE = "1969-05-12";
				} else	if ($DOCDATE=="5 า.ค.2512") {
					$DOCDATE = "1969-08-05";
				} else	if ($DOCDATE=="0 14 ส.ค.2512") {
					$DOCDATE = "1969-08-14";
				} else	if ($DOCDATE=="24ก.ย. 2512") {
					$DOCDATE = "1969-09-24";
				} else	if ($DOCDATE=="3 ทต.ค.2512") {
					$DOCDATE = "1969-10-03";
				} else	if ($DOCDATE=="27เมย.13") {
					$DOCDATE = "1970-04-27";
				} else	if ($DOCDATE=="25 มิ.ญ.2513") {
					$DOCDATE = "1970-06-25";
				} else	if ($DOCDATE=="16ก.ย. 2513") {
					$DOCDATE = "1970-09-16";
				} else	if ($DOCDATE=="27ก.ย. 2513") {
					$DOCDATE = "1970-09-27";
				} else	if ($DOCDATE=="28ก.ย. 2513") {
					$DOCDATE = "1970-09-28";
				} else	if ($DOCDATE=="17 พ.ย.2513") {
					$DOCDATE = "1970-11-17";
				} else	if ($DOCDATE=="21 พ.ค.2514") {
					$DOCDATE = "1971-05-21";
				} else	if ($DOCDATE=="41มิ.ย. 2514") {
					$DOCDATE = "1971-06-11";
				} else	if ($DOCDATE=="29มิย.14") {
					$DOCDATE = "1971-06-29";
				} else	if ($DOCDATE=="15 ก.ค.2514") {
					$DOCDATE = "1971-07-15";
				} else	if ($DOCDATE=="ง 4 ส.ค.2514") {
					$DOCDATE = "1971-08-04";
				} else	if ($DOCDATE=="20ตค.14") {
					$DOCDATE = "1971-10-20";
				} else	if ($DOCDATE=="24มี.ค. 2515") {
					$DOCDATE = "1972-03-24";
				} else	if ($DOCDATE=="ง 12 พ.ค.2515") {
					$DOCDATE = "1972-05-12";
				} else	if ($DOCDATE=="24 มี.ย.2515") {
					$DOCDATE = "1972-06-24";
				} else	if ($DOCDATE=="30มิย.15") {
					$DOCDATE = "1972-06-30";
				} else	if ($DOCDATE=="29กย.15(ฉบับที่3) พศ.2517") {
					$DOCDATE = "1972-09-29";
				} else	if ($DOCDATE=="13 .ค.2515" || $DOCDATE=="13 ธ. ค. 2515") {
					$DOCDATE = "1972-12-13";
				} else	if ($DOCDATE=="25052516" || $DOCDATE=="25พค.16") {
					$DOCDATE = "1973-05-16";
				} else	if ($DOCDATE=="21พค.16)") {
					$DOCDATE = "1973-05-21";
				} else	if ($DOCDATE=="7 มิ่.ย.2516)") {
					$DOCDATE = "1973-06-07";
				} else	if ($DOCDATE=="5-11-16") {
					$DOCDATE = "1973-11-05";
				} else	if ($DOCDATE=="22-12-16") {
					$DOCDATE = "1973-12-22";
				} else	if ($DOCDATE=="20 มี.ค2517" || $DOCDATE=="20 มีร.ค.2517") {
					$DOCDATE = "1974-03-20";
				} else	if ($DOCDATE=="1 พ. ค. 2517" || $DOCDATE=="ส. 1 พ.ค.2517" || $DOCDATE=="1 ฑ.ค.2517") {
					$DOCDATE = "1974-05-01";
				} else	if ($DOCDATE=="07052517" || $DOCDATE=="7พค.17") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="17พค.17") {
					$DOCDATE = "1974-05-17";
				} else	if ($DOCDATE=="20 มี.ย.2517") {
					$DOCDATE = "1974-06-20";
				} else	if ($DOCDATE=="28 ก พ.2518") {
					$DOCDATE = "1975-02-28";
				} else	if ($DOCDATE=="5มี.ค.18") {
					$DOCDATE = "1975-03-05";
				} else	if ($DOCDATE=="21มีค.18") {
					$DOCDATE = "1975-03-21";
				} else	if ($DOCDATE=="14พค.18") {
					$DOCDATE = "1975-05-14";
				} else	if ($DOCDATE=="21 สมคย.2518") {
					$DOCDATE = "1975-08-21";
				} else	if ($DOCDATE=="5กย.18") {
					$DOCDATE = "1975-09-05";
				} else	if ($DOCDATE=="11กย. 2518" || $DOCDATE=="11 กมยย.2518") {
					$DOCDATE = "1975-09-11";
				} else	if ($DOCDATE=="18กย.18") {
					$DOCDATE = "1975-09-18";
				} else	if ($DOCDATE=="3 ต.ย.2518") {
					$DOCDATE = "1975-10-03";
				} else	if ($DOCDATE=="18ธค.18") {
					$DOCDATE = "1975-12-18";
				} else	if ($DOCDATE=="27กพ.19") {
					$DOCDATE = "1976-02-27";
				} else	if ($DOCDATE=="0 1 มี.ค. 2519") {
					$DOCDATE = "1976-03-01";
				} else	if ($DOCDATE=="28 ม.ย.2519" || $DOCDATE=="2 8 มิ.ย.2519") {
					$DOCDATE = "1976-06-28";
				} else	if ($DOCDATE=="28ก.ย. 2519") {
					$DOCDATE = "1976-09-28";
				} else	if ($DOCDATE=="01102519") {
					$DOCDATE = "1976-10-01";
				} else	if ($DOCDATE=="5 ตค2519" || $DOCDATE=="5 คต.ค.2519" || $DOCDATE=="5 ต..ค.2519") {
					$DOCDATE = "1976-10-05";
				} else	if ($DOCDATE=="พ.ย. 2519") {
					$DOCDATE = "1976-11-00";
				} else	if ($DOCDATE=="4 พ.ย.2519") {
					$DOCDATE = "1976-11-04";
				} else	if ($DOCDATE=="13มค.20") {
					$DOCDATE = "1977-01-13";
				} else	if ($DOCDATE=="2กพ.20") {
					$DOCDATE = "1977-02-02";
				} else	if ($DOCDATE=="13มิย.20") {
					$DOCDATE = "1977-06-13";
				} else	if ($DOCDATE=="14มิย.20") {
					$DOCDATE = "1977-06-14";
				} else	if ($DOCDATE=="23กย.20") {
					$DOCDATE = "1977-09-23";
				} else	if ($DOCDATE=="11 ค.ค.2520") {
					$DOCDATE = "1977-10-11";
				} else	if ($DOCDATE=="28 ธ.ค2520") {
					$DOCDATE = "1977-12-28";
				} else	if ($DOCDATE=="31 ่ม.ค.2521") {
					$DOCDATE = "1978-01-31";
				} else	if ($DOCDATE==". 27 ก.พ.2521") {
					$DOCDATE = "1978-02-27";
				} else	if ($DOCDATE=="26 มิ.ต.2521") {
					$DOCDATE = "1978-06-26";
				} else	if ($DOCDATE=="1 เม.ร.2521") {
					$DOCDATE = "1978-04-01";
				} else	if ($DOCDATE=="8 ส.ย.2521") {
					$DOCDATE = "1978-08-08";
				} else	if ($DOCDATE=="20ก.ย.21" || $DOCDATE=="20กย.21") {
					$DOCDATE = "1978-09-20";
				} else	if ($DOCDATE=="11ตค.21") {
					$DOCDATE = "1978-10-11";
				} else	if ($DOCDATE=="31 31 ม.ค.2522") {
					$DOCDATE = "1979-01-31";
				} else	if ($DOCDATE=="5 มี.ค2522") {
					$DOCDATE = "1979-03-05";
				} else	if ($DOCDATE=="23 มี. ค. 2522") {
					$DOCDATE = "1979-03-23";
				} else	if ($DOCDATE=="29 มิ.ค.2522") {
					$DOCDATE = "1979-03-29";
				} else	if ($DOCDATE=="27เมย.22") {
					$DOCDATE = "1979-04-27";
				} else	if ($DOCDATE=="24 ก.ย2522") {
					$DOCDATE = "1979-09-24";
				} else	if ($DOCDATE=="10 ต..ค.2522") {
					$DOCDATE = "1979-10-10";
				} else	if ($DOCDATE=="20 ธ.ค2522") {
					$DOCDATE = "1979-12-20";
				} else	if ($DOCDATE=="22 ธ.ค2522") {
					$DOCDATE = "1979-12-22";
				} else	if ($DOCDATE=="24ธ.ค.22") {
					$DOCDATE = "1979-12-24";
				} else	if ($DOCDATE=="16 16 ม.ค.2523") {
					$DOCDATE = "1980-01-16";
				} else	if ($DOCDATE=="29 17 มี.ค.2523") {
					$DOCDATE = "1980-03-17";
				} else	if ($DOCDATE=="24 มี.ค2523") {
					$DOCDATE = "1980-03-24";
				} else	if ($DOCDATE=="25 มี.ย.2523") {
					$DOCDATE = "1980-06-25";
				} else	if ($DOCDATE=="ม 19 ส.ค. 2523") {
					$DOCDATE = "1980-08-19";
				} else	if ($DOCDATE=="17กย.23") {
					$DOCDATE = "1980-09-17";
				} else	if ($DOCDATE=="29 กงย.2523" || $DOCDATE=="29 ก.ย2523" || $DOCDATE=="29 ก .ย.2523") {
					$DOCDATE = "1980-09-29";
				} else	if ($DOCDATE=="2ตค.23") {
					$DOCDATE = "1980-10-02";
				} else	if ($DOCDATE=="3ตค.23") {
					$DOCDATE = "1980-10-03";
				} else	if ($DOCDATE=="25 พถ.ย.2523") {
					$DOCDATE = "1980-11-25";
				} else	if ($DOCDATE=="30 ธ..ค.2523") {
					$DOCDATE = "1980-12-30";
				} else	if ($DOCDATE=="8 ม.ค.2524" || $DOCDATE=="8 ม. ค. 2524") {
					$DOCDATE = "1981-01-08";
				} else	if ($DOCDATE=="9 ก .พ.2524") {
					$DOCDATE = "1981-02-09";
				} else	if ($DOCDATE=="18กพ.24") {
					$DOCDATE = "1981-02-18";
				} else	if ($DOCDATE=="15 เม .ย.2524") {
					$DOCDATE = "1981-04-15";
				} else	if ($DOCDATE=="24 พ.ก.2524") {
					$DOCDATE = "1981-05-24";
				} else	if ($DOCDATE=="10ส.ค. 2524") {
					$DOCDATE = "1981-08-10";
				} else	if ($DOCDATE=="19 ส.ค2524") {
					$DOCDATE = "1981-08-19";
				} else	if ($DOCDATE=="3กันยายน 2524") {
					$DOCDATE = "1981-09-24";
				} else	if ($DOCDATE=="24 ก.ย2524") {
					$DOCDATE = "1981-09-24";
				} else	if ($DOCDATE=="ต.ค. 2524") {
					$DOCDATE = "1981-10-00";
				} else	if ($DOCDATE=="7 จต2524") {
					$DOCDATE = "1981-10-07";
				} else	if ($DOCDATE=="พ.ย. 2524") {
					$DOCDATE = "1981-11-00";
				} else	if ($DOCDATE=="22 ธ.ค2.524") {
					$DOCDATE = "1981-12-22";
				} else	if ($DOCDATE=="27มค.25") {
					$DOCDATE = "1982-01-27";
				} else	if ($DOCDATE=="28 .ค.2525") {
					$DOCDATE = "1982-01-28";
				} else	if ($DOCDATE=="15กพ.25") {
					$DOCDATE = "1982-02-15";
				} else	if ($DOCDATE=="29 ่มี.ค.2525") {
					$DOCDATE = "1982-03-29";
				} else	if ($DOCDATE=="11 ส . ค. 2525") {
					$DOCDATE = "1982-08-11";
				} else	if ($DOCDATE=="31 ส.คง2525") {
					$DOCDATE = "1982-08-31";
				} else	if ($DOCDATE=="29 กันยายน2525" || $DOCDATE=="29 ก.ย2525") {
					$DOCDATE = "1982-09-29";
				} else	if ($DOCDATE=="29 ด.ย.2525") {
					$DOCDATE = "1982-10-29";
				} else	if ($DOCDATE=="12 พ.ย.2525") {
					$DOCDATE = "1982-11-12";
				} else	if ($DOCDATE=="2ธค.25") {
					$DOCDATE = "1982-12-02";
				} else	if ($DOCDATE=="29 คซึซ2525") {
					$DOCDATE = "1982-12-29";
				} else	if ($DOCDATE=="7มิย.26") {
					$DOCDATE = "1983-06-07";
				} else	if ($DOCDATE=="15 มืย.2526") {
					$DOCDATE = "1983-06-15";
				} else	if ($DOCDATE=="31 กซึซ2526") {
					$DOCDATE = "1983-07-31";
				} else	if ($DOCDATE=="18ส.ค. 2526") {
					$DOCDATE = "1983-08-18";
				} else	if ($DOCDATE=="21กย.26") {
					$DOCDATE = "1983-09-21";
				} else	if ($DOCDATE=="23ก.ย. 2526" || $DOCDATE=="23 กันยายน2526") {
					$DOCDATE = "1983-09-23";
				} else	if ($DOCDATE=="10 ต.ค.2526") {
					$DOCDATE = "1983-10-10";
				} else	if ($DOCDATE=="14 พ.ย2526") {
					$DOCDATE = "1983-11-14";
				} else	if ($DOCDATE=="30พย.26") {
					$DOCDATE = "1983-11-26";
				} else	if ($DOCDATE=="12 ธงค.2526") {
					$DOCDATE = "1983-12-12";
				} else	if ($DOCDATE=="20 ธ ค.2526") {
					$DOCDATE = "1983-12-20";
				} else	if ($DOCDATE=="20 ม.ย.2527") {
					$DOCDATE = "1984-04-20";
				} else	if ($DOCDATE=="27 ม.ย.2527" || $DOCDATE=="ง 27 มิ.ย.2527") {
					$DOCDATE = "1984-06-27";
				} else	if ($DOCDATE=="28 ส. ค. 2527") {
					$DOCDATE = "1984-08-28";
				} else	if ($DOCDATE=="1 0 ก.ย. 2527") {
					$DOCDATE = "1984-09-10";
				} else	if ($DOCDATE=="11กย.27") {
					$DOCDATE = "1984-09-11";
				} else	if ($DOCDATE=="18กย.27" || $DOCDATE=="18 ก. ย. 2527") {
					$DOCDATE = "1984-09-18";
				} else	if ($DOCDATE=="31ตค.27") {
					$DOCDATE = "1984-10-31";
				} else	if ($DOCDATE=="8 พ.ยย.2527" || $DOCDATE=="8พ.ย. 27") {
					$DOCDATE = "1984-11-08";
				} else	if ($DOCDATE=="11 ธ.คฅ2527") {
					$DOCDATE = "1984-12-11";
				} else	if ($DOCDATE=="20 ธ.ต.2527") {
					$DOCDATE = "1984-12-20";
				} else	if ($DOCDATE=="25 ฒง๕ง2528") {
					$DOCDATE = "1985-01-25";
				} else	if ($DOCDATE=="15 มี. ค. 2528") {
					$DOCDATE = "1985-03-15";
				} else	if ($DOCDATE=="5เมย.28") {
					$DOCDATE = "1985-04-05";
				} else	if ($DOCDATE=="26 เมษายน2528") {
					$DOCDATE = "1985-04-26";
				} else	if ($DOCDATE=="7 พ .ค. 2528") {
					$DOCDATE = "1985-05-07";
				} else	if ($DOCDATE=="ฅ 21 พ.ค.2528") {
					$DOCDATE = "1985-05-21";
				} else	if ($DOCDATE=="ง 12 มิ.ย. 2528" || $DOCDATE=="12 มิ .ย. 2528") {
					$DOCDATE = "1985-06-12";
				} else	if ($DOCDATE=="ง 14 ส.ค. 2528") {
					$DOCDATE = "1985-08-14";
				} else	if ($DOCDATE=="12กย.28") {
					$DOCDATE = "1985-09-12";
				} else	if ($DOCDATE=="8 ค.ค.2528") {
					$DOCDATE = "1985-10-08";
				} else	if ($DOCDATE=="28 0.ค.2528") {
					$DOCDATE = "1985-10-28";
				} else	if ($DOCDATE=="31ตค.28") {
					$DOCDATE = "1985-10-31";
				} else	if ($DOCDATE=="3 13 ธ.ค.2528") {
					$DOCDATE = "1985-12-13";
				} else	if ($DOCDATE=="29 ส.๕.29") {
					$DOCDATE = "1986-08-29";
				} else	if ($DOCDATE=="23กย.29") {
					$DOCDATE = "1986-09-23";
				} else	if ($DOCDATE=="28กย29") {
					$DOCDATE = "1986-09-28";
				} else	if ($DOCDATE=="2 ธ.ต.2529" || $DOCDATE=="2 ธ ค.2529") {
					$DOCDATE = "1986-12-02";
				} else	if ($DOCDATE=="12 มี .ค.2530") {
					$DOCDATE = "1987-03-12";
				} else	if ($DOCDATE=="30 23 มี.ค.2530") {
					$DOCDATE = "1987-03-23";
				} else	if ($DOCDATE=="30 มี ค.2530") {
					$DOCDATE = "1987-03-30";
				} else	if ($DOCDATE=="11 มิถุนายน.2530") {
					$DOCDATE = "1987-06-11";
				} else	if ($DOCDATE=="29 ก.บ.25300" || $DOCDATE=="29ก.ย. 2530" || $DOCDATE=="29 ก.ย.2530") {
					$DOCDATE = "1987-09-29";
				} else	if ($DOCDATE=="27ส.ค. 2530") {
					$DOCDATE = "1987-08-27";
				} else	if ($DOCDATE=="31ส.ค. 2530") {
					$DOCDATE = "1987-08-31";
				} else	if ($DOCDATE=="5 ก .พ. 2531") {
					$DOCDATE = "1988-02-05";
				} else	if ($DOCDATE=="9 พ.ค2531") {
					$DOCDATE = "1988-05-09";
				} else	if ($DOCDATE=="8มิย.31") {
					$DOCDATE = "1988-06-08";
				} else	if ($DOCDATE=="23 มี.ย.2531") {
					$DOCDATE = "1988-06-23";
				} else	if ($DOCDATE=="27กรกฎาคม 2531" || $DOCDATE=="27 ก.ค2531") {
					$DOCDATE = "1988-07-27";
				} else	if ($DOCDATE=="13 กันยายน.2531") {
					$DOCDATE = "1988-09-13";
				} else	if ($DOCDATE=="28กย.31" || $DOCDATE=="28ก.ย.31") {
					$DOCDATE = "1988-09-28";
				} else	if ($DOCDATE=="30 กันยายน2531") {
					$DOCDATE = "1988-09-30";
				} else	if ($DOCDATE=="26ตค.31") {
					$DOCDATE = "1988-10-26";
				} else	if ($DOCDATE=="23 ่พ.ค.2532") {
					$DOCDATE = "1989-05-23";
				} else	if ($DOCDATE=="15 สงค.2532") {
					$DOCDATE = "1989-08-15";
				} else	if ($DOCDATE=="25 ส. ค. 2532") {
					$DOCDATE = "1989-08-25";
				} else	if ($DOCDATE=="31 ส. ค. 2532") {
					$DOCDATE = "1989-08-31";
				} else	if ($DOCDATE=="6 ก.ย2532") {
					$DOCDATE = "1989-09-06";
				} else	if ($DOCDATE=="12 ก ย.2532") {
					$DOCDATE = "1989-09-12";
				} else	if ($DOCDATE=="4ตค.32" || $DOCDATE=="4ต.ค.32") {
					$DOCDATE = "1989-10-04";
				} else	if ($DOCDATE=="24ต.ค. 2532") {
					$DOCDATE = "1989-10-24";
				} else	if ($DOCDATE=="11 มิ.ค.2533") {
					$DOCDATE = "1990-03-11";
				} else	if ($DOCDATE=="7 ม.ย.2533") {
					$DOCDATE = "1990-04-07";
				} else	if ($DOCDATE=="20 ้เม.ย.2533") {
					$DOCDATE = "1990-04-20";
				} else	if ($DOCDATE=="2 พ..ค.2533") {
					$DOCDATE = "1990-05-02";
				} else	if ($DOCDATE=="10 พ.๕.33") {
					$DOCDATE = "1990-05-10";
				} else	if ($DOCDATE=="ง 18 พ.ค. 2533") {
					$DOCDATE = "1990-05-18";
				} else	if ($DOCDATE=="27 มื.ย.2533") {
					$DOCDATE = "1990-06-27";
				} else	if ($DOCDATE=="28มิ.ย. 2533") {
					$DOCDATE = "1990-06-28";
				} else	if ($DOCDATE=="19 กรกฎาคม2533") {
					$DOCDATE = "1990-07-19";
				} else	if ($DOCDATE=="31 สิ.ค.2533") {
					$DOCDATE = "1990-08-31";
				} else	if ($DOCDATE=="27ก.ย.2533") {
					$DOCDATE = "1990-09-27";
				} else	if ($DOCDATE=="28 ก..ย.2533") {
					$DOCDATE = "1990-09-28";
				} else	if ($DOCDATE=="16 พ.ยย.2533") {
					$DOCDATE = "1990-11-16";
				} else	if ($DOCDATE=="24 ธันวาคม2533") {
					$DOCDATE = "1990-12-27";
				} else	if ($DOCDATE=="27 ธ. ค. 2533") {
					$DOCDATE = "1990-12-27";
				} else	if ($DOCDATE=="30 2.ค. 2534") {
					$DOCDATE = "1991-01-30";
				} else	if ($DOCDATE=="10 มิ ย.2534") {
					$DOCDATE = "1991-06-10";
				} else	if ($DOCDATE=="14 มิ.ย2534") {
					$DOCDATE = "1991-06-14";
				} else	if ($DOCDATE=="16 ก.ค2534") {
					$DOCDATE = "1991-07-16";
				} else	if ($DOCDATE=="6 สิ.ค.2534" || $DOCDATE=="6 กส.ค.2534") {
					$DOCDATE = "1991-08-06";
				} else	if ($DOCDATE=="1 2 ก.ย.2534") {
					$DOCDATE = "1991-09-12";
				} else	if ($DOCDATE=="20 วฅตฅ2534") {
					$DOCDATE = "1991-10-20";
				} else	if ($DOCDATE=="24 ด.ย.2534") {
					$DOCDATE = "1991-10-24";
				} else	if ($DOCDATE=="4 ธ. ค. 2534") {
					$DOCDATE = "1991-12-04";
				} else	if ($DOCDATE=="17 ะ.ค.2534") {
					$DOCDATE = "1991-12-17";
				} else	if ($DOCDATE=="8 ม..ค.2535") {
					$DOCDATE = "1992-01-08";
				} else	if ($DOCDATE=="22ม.ค 2535") {
					$DOCDATE = "1992-01-22";
				} else	if ($DOCDATE=="23 มี. ค.2535") {
					$DOCDATE = "1992-03-23";
				} else	if ($DOCDATE=="20ส.ค. 2535" || $DOCDATE=="20 ศง๕ง2535") {
					$DOCDATE = "1992-08-20";
				} else	if ($DOCDATE=="31 สิงหาคม2535") {
					$DOCDATE = "1992-08-31";
				} else	if ($DOCDATE=="14 กันยายน2535") {
					$DOCDATE = "1992-09-14";
				} else	if ($DOCDATE=="18 ก.ญ.35") {
					$DOCDATE = "1992-09-18";
				} else	if ($DOCDATE=="8 ต . ค. 2535") {
					$DOCDATE = "1992-10-08";
				} else	if ($DOCDATE=="10 พงย.2535") {
					$DOCDATE = "1992-11-10";
				} else	if ($DOCDATE=="8 ธ.ค2535" || $DOCDATE=="8ธ.ค. 2535") {
					$DOCDATE = "1992-12-08";
				} else	if ($DOCDATE=="24 ธ.ต.2535") {
					$DOCDATE = "1992-12-24";
				} else	if ($DOCDATE=="19 ,.ค.2536") {
					$DOCDATE = "1993-01-19";
				} else	if ($DOCDATE=="29062536") {
					$DOCDATE = "1993-06-29";
				} else	if ($DOCDATE=="1 ก.ส.ค. 2536") {
					$DOCDATE = "1993-07-21";
				} else	if ($DOCDATE=="20 ส.๕.36") {
					$DOCDATE = "1993-08-20";
				} else	if ($DOCDATE=="31ส.ค. 2536") {
					$DOCDATE = "1993-08-31";
				} else	if ($DOCDATE=="8 กา.ย.2536") {
					$DOCDATE = "1993-09-08";
				} else	if ($DOCDATE=="30 ก.บ.2536") {
					$DOCDATE = "1993-09-30";
				} else	if ($DOCDATE=="ต.ค. 2536") {
					$DOCDATE = "1993-10-00";
				} else	if ($DOCDATE=="25 ค.ค.2536") {
					$DOCDATE = "1993-10-25";
				} else	if ($DOCDATE=="6 ะ.ค.2536") {
					$DOCDATE = "1993-12-06";
				} else	if ($DOCDATE=="21ธ.ค. 2536") {
					$DOCDATE = "1993-12-21";
				} else	if ($DOCDATE=="24 ธ. ค. 2536") {
					$DOCDATE = "1993-12-24";
				} else	if ($DOCDATE=="14 มค2537") {
					$DOCDATE = "1994-01-14";
				} else	if ($DOCDATE=="22 ม.. 2537") {
					$DOCDATE = "1994-01-22";
				} else	if ($DOCDATE=="15 กุมภาพันธ์2537") {
					$DOCDATE = "1994-02-15";
				} else	if ($DOCDATE=="11 มีร.ค.2537") {
					$DOCDATE = "1994-03-11";
				} else	if ($DOCDATE=="22 มี. ค. 2537") {
					$DOCDATE = "1994-03-22";
				} else	if ($DOCDATE=="10 ม.ย.2537") {
					$DOCDATE = "1994-04-10";
				} else	if ($DOCDATE=="ก.ค. 2537") {
					$DOCDATE = "1994-07-00";
				} else	if ($DOCDATE=="22ส.ค. 37") {
					$DOCDATE = "1994-08-22";
				} else	if ($DOCDATE=="23 กันยายน2537") {
					$DOCDATE = "1994-09-23";
				} else	if ($DOCDATE=="1 ธ .ค.2537") {
					$DOCDATE = "1994-12-01";
				} else	if ($DOCDATE=="21 .ธ.ค.2537") {
					$DOCDATE = "1994-12-21";
				} else	if ($DOCDATE=="10 ก.พ.2538") {
					$DOCDATE = "1995-02-10";
				} else	if ($DOCDATE=="21 กุมภาพันธ์2538") {
					$DOCDATE = "1995-02-21";
				} else	if ($DOCDATE=="10 มึค. 38") {
					$DOCDATE = "1995-03-10";
				} else	if ($DOCDATE=="23มี.ค.38") {
					$DOCDATE = "1995-03-23";
				} else	if ($DOCDATE=="30มี.ค. 2538") {
					$DOCDATE = "1995-03-30";
				} else	if ($DOCDATE=="1 มิถุนายน2538") {
					$DOCDATE = "1995-06-01";
				} else	if ($DOCDATE=="30 มิ.ย2538" || $DOCDATE=="ง 30 มิ.ย. 2538" || $DOCDATE=="30 มิงย.2538" || $DOCDATE=="30 ม.ย.2538" || $DOCDATE=="30 ม.ย. 2538" || $DOCDATE=="30 มิ.ญ.38") {
					$DOCDATE = "1995-06-30";
				} else	if ($DOCDATE=="4 กรกฎาคม2538") {
					$DOCDATE = "1995-07-04";
				} else	if ($DOCDATE=="11ส.ค. 2538") {
					$DOCDATE = "1995-08-11";
				} else	if ($DOCDATE=="วันที่ 14 ส.ค.2538") {
					$DOCDATE = "1995-08-14";
				} else	if ($DOCDATE=="21 สิงหาคม2538") {
					$DOCDATE = "1995-08-21";
				} else	if ($DOCDATE=="23ส.ค. 2538" || $DOCDATE=="23 ส.คง2538" || $DOCDATE=="23 สง๕ง2538" || $DOCDATE=="2 3 สิงหาคม 2538") {
					$DOCDATE = "1995-08-23";
				} else	if ($DOCDATE=="20 กันยายน2538") {
					$DOCDATE = "1995-09-20";
				} else	if ($DOCDATE=="10 ด.พ.2538" || $DOCDATE=="10 ค.ค.2538") {
					$DOCDATE = "1995-10-10";
				} else	if ($DOCDATE=="17 ต. ค. 2538" || $DOCDATE=="17 ค.ค.2538" || $DOCDATE=="17ต.ค. 2538") {
					$DOCDATE = "1995-10-17";
				} else	if ($DOCDATE=="6 พ. ย. 2538") {
					$DOCDATE = "1995-11-06";
				} else	if ($DOCDATE=="21 พ.น.2538") {
					$DOCDATE = "1995-11-21";
				} else	if ($DOCDATE=="21 ธ. ค. 2538") {
					$DOCDATE = "1995-12-21";
				} else	if ($DOCDATE=="7 มึค.2539") {
					$DOCDATE = "1996-03-07";
				} else	if ($DOCDATE=="19 มี.ค2539") {
					$DOCDATE = "1996-03-19";
				} else	if ($DOCDATE=="4 ม.ย.2539") {
					$DOCDATE = "1996-04-04";
				} else	if ($DOCDATE=="29 พ. ค.2539") {
					$DOCDATE = "1996-05-29";
				} else	if ($DOCDATE=="18 กรกฎาคม2539") {
					$DOCDATE = "1996-07-18";
				} else	if ($DOCDATE=="22 ก .ค. 2539") {
					$DOCDATE = "1996-07-22";
				} else	if ($DOCDATE=="ส.ค. 2539") {
					$DOCDATE = "1996-08-00";
				} else	if ($DOCDATE=="8 สิงหาคม2539") {
					$DOCDATE = "1996-08-08";
				} else	if ($DOCDATE=="15ส.ค. 2539") {
					$DOCDATE = "1996-08-15";
				} else	if ($DOCDATE=="16 ส.คง2539" || $DOCDATE=="16ส.ค. 2539") {
					$DOCDATE = "1996-08-16";
				} else	if ($DOCDATE=="23สค. 39") {
					$DOCDATE = "1996-08-23";
				} else	if ($DOCDATE=="17 ก.ย2539") {
					$DOCDATE = "1996-09-17";
				} else	if ($DOCDATE=="15 ค.ค.2539") {
					$DOCDATE = "1996-10-15";
				} else	if ($DOCDATE=="18 ค.ค.2539") {
					$DOCDATE = "1996-10-18";
				} else	if ($DOCDATE=="6 พฤศจิกายน2539") {
					$DOCDATE = "1996-11-06";
				} else	if ($DOCDATE=="20ธ.ค. 2539") {
					$DOCDATE = "1996-12-20";
				} else	if ($DOCDATE=="ม.ค. 2540") {
					$DOCDATE = "1997-01-00";
				} else	if ($DOCDATE=="4ก.พ. 2540") {
					$DOCDATE = "1997-02-04";
				} else	if ($DOCDATE=="28 กุมภาพันธ์.2540") {
					$DOCDATE = "1997-02-28";
				} else	if ($DOCDATE=="1 2 มี.ค. 40") {
					$DOCDATE = "1997-03-12";
				} else	if ($DOCDATE=="เม.ย 40") {
					$DOCDATE = "1997-04-00";
				} else	if ($DOCDATE=="2 5 เม.ย.2540") {
					$DOCDATE = "1997-04-25";
				} else	if ($DOCDATE=="4 มิถุนายน 2540") {
					$DOCDATE = "1997-06-04";
				} else	if ($DOCDATE=="31 กรกฎาคม2540") {
					$DOCDATE = "1997-07-31";
				} else	if ($DOCDATE=="14 สิงหาคม.2540" || $DOCDATE=="14 สิงหาคม2540") {
					$DOCDATE = "1997-08-14";
				} else	if ($DOCDATE=="23 กันยายน2540") {
					$DOCDATE = "1997-09-23";
				} else	if ($DOCDATE=="25ก.ย. 2540") {
					$DOCDATE = "1997-09-25";
				} else	if ($DOCDATE=="3 ค.ค.2540") {
					$DOCDATE = "1997-10-03";
				} else	if ($DOCDATE=="8 ตุลาคาม2540") {
					$DOCDATE = "1997-10-08";
				} else	if ($DOCDATE=="14 ค.ค.2540") {
					$DOCDATE = "1997-10-14";
				} else	if ($DOCDATE=="28 ตุลาคม2540") {
					$DOCDATE = "1997-10-28";
				} else	if ($DOCDATE=="31 ตุลาคม2540") {
					$DOCDATE = "1997-10-31";
				} else	if ($DOCDATE=="11 ธันวาคม2540") {
					$DOCDATE = "1997-12-11";
				} else	if ($DOCDATE=="25ก.พ. 2541" || $DOCDATE=="25 ก3พ.2541") {
					$DOCDATE = "1998-02-25";
				} else	if ($DOCDATE=="26มี.ค.2541" || $DOCDATE=="26มี.ค.41") {
					$DOCDATE = "1998-03-26";
				} else	if ($DOCDATE=="ก.ย. 2541") {
					$DOCDATE = "1998-09-00";
				} else	if ($DOCDATE=="2 กันยายน2541") {
					$DOCDATE = "1998-09-02";
				} else	if ($DOCDATE=="1 6 ก.ย. 2541") {
					$DOCDATE = "1998-09-16";
				} else	if ($DOCDATE=="30 กันยายน2541") {
					$DOCDATE = "1998-09-30";
				} else	if ($DOCDATE=="6ต.ค.41") {
					$DOCDATE = "1998-10-06";
				} else	if ($DOCDATE=="2 พฤศจิกายน2541") {
					$DOCDATE = "1998-11-02";
				} else	if ($DOCDATE=="9ธันวาคม 2541") {
					$DOCDATE = "1998-12-09";
				} else	if ($DOCDATE=="6ม.ค. 2542") {
					$DOCDATE = "1999-01-06";
				} else	if ($DOCDATE=="27 6เม.ย.42") {
					$DOCDATE = "1999-04-06";
				} else	if ($DOCDATE=="17 ม. ย. 2542") {
					$DOCDATE = "1999-04-17";
				} else	if ($DOCDATE=="11 พฤษภาคม2542" || $DOCDATE=="11พฤษภาคม 2542") {
					$DOCDATE = "1999-05-11";
				} else	if ($DOCDATE=="12กรกฎาคม 2542") {
					$DOCDATE = "1999-07-12";
				} else	if ($DOCDATE=="18ก.ย. 2542") {
					$DOCDATE = "1999-09-18";
				} else	if ($DOCDATE=="21ธ.ค. 2542") {
					$DOCDATE = "1999-12-21";
				} else	if ($DOCDATE=="22 ม.ย.2543") {
					$DOCDATE = "2000-06-22";
				} else	if ($DOCDATE=="27ก.ค.2543") {
					$DOCDATE = "2000-07-27";
				} else	if ($DOCDATE=="13 กันยายน2543") {
					$DOCDATE = "2000-09-13";
				} else	if ($DOCDATE=="27ต.ค. 2543" || $DOCDATE=="27ต.ค.2543") {
					$DOCDATE = "2000-10-27";
				} else	if ($DOCDATE=="11 พค44") {
					$DOCDATE = "2001-05-11";
				} else	if ($DOCDATE=="11มิย. 44") {
					$DOCDATE = "2001-06-11";
				} else	if ($DOCDATE=="กันยายน 2544") {
					$DOCDATE = "2001-09-00";
				} else	if ($DOCDATE=="20ก.ย.2544" || $DOCDATE=="20กันยายน 2544") {
					$DOCDATE = "2001-09-20";
				} else	if ($DOCDATE=="7พ.ย.2544") {
					$DOCDATE = "2001-11-07";
				} else	if ($DOCDATE=="มีนาคม 2545") {
					$DOCDATE = "2002-03-00";
				} else	if ($DOCDATE=="22พ.ค. 2545" || $DOCDATE=="22พ.ค.2545") {
					$DOCDATE = "2002-05-22";
				} else	if ($DOCDATE=="มิถุนายน 2545") {
					$DOCDATE = "2002-06-00";
				} else	if ($DOCDATE=="8 มิถุนายน2545") {
					$DOCDATE = "2002-06-08";
				} else	if ($DOCDATE=="13 มิถุนายน2545") {
					$DOCDATE = "2002-06-13";
				} else	if ($DOCDATE=="27 มิถุนายน2545") {
					$DOCDATE = "2002-06-27";
				} else	if ($DOCDATE=="23 ก.ญ.45") {
					$DOCDATE = "2002-09-23";
				} else	if ($DOCDATE=="1พ.ย. 2545") {
					$DOCDATE = "2002-11-01";
				} else	if ($DOCDATE=="4ธันวาคม 2545") {
					$DOCDATE = "2002-12-04";
				} else	if ($DOCDATE=="28ม.ค. 2546") {
					$DOCDATE = "2003-01-28";
				} else	if ($DOCDATE=="7ก.พ.46") {
					$DOCDATE = "2003-02-07";
				} else	if ($DOCDATE=="มีนาคม 2546") {
					$DOCDATE = "2003-03-00";
				} else	if ($DOCDATE=="พฤษภาคม 2546") {
					$DOCDATE = "2003-05-00";
				} else	if ($DOCDATE=="มิถุนายน 2546") {
					$DOCDATE = "2003-06-00";
				} else	if ($DOCDATE=="17ก.ค.46") {
					$DOCDATE = "2003-07-17";
				} else	if ($DOCDATE=="สิงหาคม 2546") {
					$DOCDATE = "2003-08-00";
				} else	if ($DOCDATE=="11สิงหาคม 2546") {
					$DOCDATE = "2003-08-11";
				} else	if ($DOCDATE=="16 ตุลาคม2546") {
					$DOCDATE = "2003-10-16";
				} else	if ($DOCDATE=="6ก.พ.47") {
					$DOCDATE = "2004-02-06";
				} else	if ($DOCDATE=="9มี.ค.47") {
					$DOCDATE = "2004-03-09";
				} else	if ($DOCDATE=="พฤษภาคม 2547") {
					$DOCDATE = "2004-05-00";
				} else	if ($DOCDATE=="28  กรกฎาคม2547") {
					$DOCDATE = "2004-07-28";
				} else	if ($DOCDATE=="3ก.ย.2547") {
					$DOCDATE = "2004-09-03";
				} else	if ($DOCDATE=="พฤศจิกายน 2547" || $DOCDATE=="พฤศจิายน 2547") {
					$DOCDATE = "2004-11-00";
				} else	if ($DOCDATE=="11ม.ค.2548") {
					$DOCDATE = "2005-01-11";
				} else	if ($DOCDATE=="17ม.ค.2548") {
					$DOCDATE = "2005-01-17";
				} else	if ($DOCDATE=="21ม.ค 2548") {
					$DOCDATE = "2005-01-21";
				} else	if ($DOCDATE=="26 มกราคม2548") {
					$DOCDATE = "2005-01-21";
				} else	if ($DOCDATE=="28มีนาคม 2548") {
					$DOCDATE = "2005-03-28";
				} else	if ($DOCDATE=="1มิถุนายน 2548") {
					$DOCDATE = "2005-06-01";
				} else	if ($DOCDATE=="7 มิถุนายน2548") {
					$DOCDATE = "2005-06-07";
				} else	if ($DOCDATE=="4 กรกฎาคม2548") {
					$DOCDATE = "2005-07-04";
				} else	if ($DOCDATE=="ซ 2 ก.ย.2548") {
					$DOCDATE = "2005-09-02";
				} else	if ($DOCDATE=="15 ก.ย.2548") {
					$DOCDATE = "2005-09-15";
				} else	if ($DOCDATE=="ตุลาคม 2548") {
					$DOCDATE = "2005-10-00";
				} else	if ($DOCDATE=="13ต.ค.2548") {
					$DOCDATE = "2005-10-13";
				} else	if ($DOCDATE=="ธ.ค. 2548") {
					$DOCDATE = "2005-12-00";
				} else	if ($DOCDATE=="21ธ.ค.2548") {
					$DOCDATE = "2005-12-21";
				} else	if ($DOCDATE=="23ม.ค.49") {
					$DOCDATE = "2006-01-23";
				} else	if ($DOCDATE=="มีนาคม 2549") {
					$DOCDATE = "2006-03-00";
				} else	if ($DOCDATE=="18พ.ค.2549") {
					$DOCDATE = "2006-05-18";
				} else	if ($DOCDATE=="25พ.ค.2549") {
					$DOCDATE = "2006-05-25";
				} else	if ($DOCDATE=="กรกฎาคม 2549") {
					$DOCDATE = "2006-07-00";
				} else	if ($DOCDATE=="สิงหาคม 2549") {
					$DOCDATE = "2006-08-00";
				} else	if ($DOCDATE=="8ส.ค.2549") {
					$DOCDATE = "2006-08-08";
				} else	if ($DOCDATE=="14ก.ย.2549") {
					$DOCDATE = "2006-09-14";
				} else	if ($DOCDATE=="24ต.ค.2549") {
					$DOCDATE = "2006-10-24";
				} else	if ($DOCDATE=="6 ดดด bbbb") {
					$DOCDATE = "2007-00-06";
				} else	if ($DOCDATE=="16มีนาคม 2550") {
					$DOCDATE = "2007-03-16";
				} else	if ($DOCDATE=="22มี.ค.2550") {
					$DOCDATE = "2007-03-22";
				} else	if ($DOCDATE=="18เม.ย.2550") {
					$DOCDATE = "2007-04-18";
				} else	if ($DOCDATE=="พฤษภาคม 2550") {
					$DOCDATE = "2007-05-00";
				} else	if ($DOCDATE=="17พฤษภาคม 2550") {
					$DOCDATE = "2007-05-17";
				} else	if ($DOCDATE=="30 พฤษภาคม2550") {
					$DOCDATE = "2007-05-30";
				} else	if ($DOCDATE=="7ส.ค.2550") {
					$DOCDATE = "2007-08-07";
				} else	if ($DOCDATE=="22ส.ค.2550") {
					$DOCDATE = "2007-08-22";
				} else	if ($DOCDATE=="2 ดดด bbbb") {
					$DOCDATE = "2007-11-02";
				} else	if ($DOCDATE=="6 ธันวาคม2550" || $DOCDATE=="6ธ.ค.2550") {
					$DOCDATE = "2007-12-06";
				} else	if ($DOCDATE=="17มกราคม 2551") {
					$DOCDATE = "2008-01-17";
				} else	if ($DOCDATE=="1ก.พ. 2551") {
					$DOCDATE = "2008-02-01";
				} else	if ($DOCDATE=="11กุมภาพันธ์ 2551") {
					$DOCDATE = "2008-02-11";
				} else	if ($DOCDATE=="25มี.ค.51") {
					$DOCDATE = "2008-03-25";
				} else	if ($DOCDATE=="4 เมษายน2551") {
					$DOCDATE = "2008-04-04";
				} else	if ($DOCDATE=="12พ.ค2551") {
					$DOCDATE = "2008-05-12";
				} else	if ($DOCDATE=="23 พฤษภาคม2551") {
					$DOCDATE = "2008-05-23";
				} else	if ($DOCDATE=="29 พฤษภาคม551") {
					$DOCDATE = "2008-05-29";
				} else	if ($DOCDATE=="9มิ.ย.51") {
					$DOCDATE = "2008-06-09";
				} else	if ($DOCDATE=="7กรกฎาคม 2551") {
					$DOCDATE = "2008-07-07";
				} else	if ($DOCDATE=="14 กรกฎาคม2551") {
					$DOCDATE = "2008-07-14";
				} else	if ($DOCDATE=="21กรกฎาคม 2551") {
					$DOCDATE = "2008-07-21";
				} else	if ($DOCDATE=="22กรกาคม 2551") {
					$DOCDATE = "2008-07-22";
				} else	if ($DOCDATE=="23 กรกฎาคม2551") {
					$DOCDATE = "2008-07-23";
				} else	if ($DOCDATE=="25กรกฎาคม 2551" || $DOCDATE=="25กรกาคม 2551") {
					$DOCDATE = "2008-07-25";
				} else	if ($DOCDATE=="6 สิงหาคม2551") {
					$DOCDATE = "2008-08-06";
				} else	if ($DOCDATE=="กันยายน 2551") {
					$DOCDATE = "2008-09-00";
				} else	if ($DOCDATE=="17กันยายน 2551") {
					$DOCDATE = "2008-09-17";
				} else	if ($DOCDATE=="25กันยายน 2551") {
					$DOCDATE = "2008-09-25";
				} else	if ($DOCDATE=="ตุลาคม 2551") {
					$DOCDATE = "2008-10-00";
				} else	if ($DOCDATE=="7ต.ค. 2551") {
					$DOCDATE = "2008-10-07";
				} else	if ($DOCDATE=="22ตุลาคม 2551") {
					$DOCDATE = "2008-10-22";
				} else	if ($DOCDATE=="28 พฤศจิกายน2551") {
					$DOCDATE = "2008-11-28";
				} else	if ($DOCDATE=="1-12-51") {
					$DOCDATE = "2008-12-01";
				} else	if ($DOCDATE=="8ธ.ค.51") {
					$DOCDATE = "2008-12-08";
				} else	if ($DOCDATE=="23ธันวาคม 2551") {
					$DOCDATE = "2008-12-23";
				} else	if ($DOCDATE=="30ธ.ค.51" || $DOCDATE=="30ธ.ค.2551") {
					$DOCDATE = "2008-12-30";
				} else	if ($DOCDATE=="21ม.ค.2552") {
					$DOCDATE = "2009-01-21";
				} else	if ($DOCDATE=="27ก.พ. 52") {
					$DOCDATE = "2009-02-27";
				} else	if ($DOCDATE=="29เมษายน 2552") {
					$DOCDATE = "2009-04-29";
				} else	if ($DOCDATE=="15พฤษภาคม 2552") {
					$DOCDATE = "2009-05-15";
				} else	if ($DOCDATE=="25พ.ค.2552") {
					$DOCDATE = "2009-05-25";
				} else	if ($DOCDATE=="กันยายน 2552") {
					$DOCDATE = "2009-09-00";
				} else	if ($DOCDATE=="11ก.ย. 52") {
					$DOCDATE = "2009-09-11";
				} else	if ($DOCDATE=="30ก.ย.2552") {
					$DOCDATE = "2009-09-30";
				} else	if ($DOCDATE=="8ต.ค.2552") {
					$DOCDATE = "2009-10-08";
				} else	if ($DOCDATE=="9ต.ค.2552") {
					$DOCDATE = "2009-10-09";
				} else	if ($DOCDATE=="26ต.ค.2552" || $DOCDATE=="26ตุลาคม 2552") {
					$DOCDATE = "2009-10-26";
				} else	if ($DOCDATE=="29ต.ค.2552") {
					$DOCDATE = "2009-10-29";
				} else	if ($DOCDATE=="3พ.ย.2552") {
					$DOCDATE = "2009-11-03";
				} else	if ($DOCDATE=="4พ.ย.2552") {
					$DOCDATE = "2009-11-04";
				} else	if ($DOCDATE=="5พ.ย.2552") {
					$DOCDATE = "2009-11-05";
				} else	if ($DOCDATE=="6พ.ย.2552") {
					$DOCDATE = "2009-11-06";
				} else	if ($DOCDATE=="9พ.ย.2552") {
					$DOCDATE = "2009-11-09";
				} else	if ($DOCDATE=="11พ.ย.2552") {
					$DOCDATE = "2009-11-11";
				} else	if ($DOCDATE=="12พ.ย.2552") {
					$DOCDATE = "2009-11-12";
				} else	if ($DOCDATE=="8 ก.พ25.53") {
					$DOCDATE = "2010-02-08";
				} else	if ($DOCDATE=="03032553") {
					$DOCDATE = "2010-03-03";
				} else	if ($DOCDATE=="7ก.ค.2553") {
					$DOCDATE = "2010-07-07";
				} else	if ($DOCDATE=="23กรกฎาคม 2553") {
					$DOCDATE = "2010-07-23";
				} else	if ($DOCDATE=="10 .สค.53") {
					$DOCDATE = "2010-08-10";
				} else	if ($DOCDATE=="16สิงหาคม 2553") {
					$DOCDATE = "2010-08-16";
				} else	if ($DOCDATE=="27กันยายน 2553") {
					$DOCDATE = "2010-09-27";
				} else	if ($DOCDATE=="20ตุลาคม 2553" || $DOCDATE=="20 ตค53") {
					$DOCDATE = "2010-10-20";
				} else	if ($DOCDATE=="26 ดดด bbbb") {
					$DOCDATE = "2010-11-26";
				} else	if ($DOCDATE=="29พ.ย.2553") {
					$DOCDATE = "2010-11-29";
				} else	if ($DOCDATE=="01012554") {
					$DOCDATE = "2011-01-01";
				} else	if ($DOCDATE=="17กุมภาพันธ์ 2554") {
					$DOCDATE = "2011-02-17";
				} else	if ($DOCDATE=="28ก.พ.2554") {
					$DOCDATE = "2011-02-28";
				} else	if ($DOCDATE=="29 เม.ย.2554" || $DOCDATE=="2 9 เม.ย.2554" || $DOCDATE=="29 เมษายน2554") {
					$DOCDATE = "2011-04-29";
				} else	if ($DOCDATE=="21มิ.ย.54") {
					$DOCDATE = "2011-06-21";
				} else	if ($DOCDATE=="กรกฎาคม 2554") {
					$DOCDATE = "2011-07-00";
				} else	if ($DOCDATE=="22 กรกฎาคม2554") {
					$DOCDATE = "2011-07-22";
				} else	if ($DOCDATE=="6ก.ย.54") {
					$DOCDATE = "2011-09-06";
				} else	if ($DOCDATE=="9 กันยายน2554") {
					$DOCDATE = "2011-09-09";
				} else	if ($DOCDATE=="29 กันยายน.2554") {
					$DOCDATE = "2011-09-29";
				} else	if ($DOCDATE=="30 พฤศจิกายน2554") {
					$DOCDATE = "2011-11-30";
				} else	if ($DOCDATE=="30.ธค. 2554") {
					$DOCDATE = "2011-12-30";
				} else	if ($DOCDATE=="28 ดดด bbbb") {
					$DOCDATE = "2011-12-28";
				} else	if ($DOCDATE=="8 ดดด bbbb") {
					$DOCDATE = "2008-07-08";
				} else	if ($DOCDATE=="12ม.ค.2555") {
					$DOCDATE = "2012-01-12";
				} else	if ($DOCDATE=="กุมภาพันธ์ 2555") {
					$DOCDATE = "2012-02-00";
				} else	if ($DOCDATE=="9กุมภาพันธ์ 2555") {
					$DOCDATE = "2012-02-09";
				} else	if ($DOCDATE=="17 กุมภาพันธ ์2555") {
					$DOCDATE = "2012-02-17";
				} else	if ($DOCDATE=="4พ.ค.55") {
					$DOCDATE = "2012-05-04";
				} else	if ($DOCDATE=="12 ตุลาคม2555") {
					$DOCDATE = "2012-10-12";
				} else	if ($DOCDATE=="138กุมภาพันธ์ 2556" || $DOCDATE=="18 กุมภาพันธ์2556") {
					$DOCDATE = "2013-02-18";
				} else	if ($DOCDATE=="8 มีนาคม2556") {
					$DOCDATE = "2013-03-08";
				} else	if ($DOCDATE=="20 พฤษภาคม.2556") {
					$DOCDATE = "2013-05-20";
				} else	if ($DOCDATE=="มิถุนายน 2556") {
					$DOCDATE = "2013-06-00";
				} else	if ($DOCDATE=="25 พฤศจิกายน2556") {
					$DOCDATE = "2013-11-25";
				} else	if ($DOCDATE=="17 ธันวาคม2556") {
					$DOCDATE = "2013-12-17";
				} else	if ($DOCDATE=="มกราคม 2557") {
					$DOCDATE = "2014-01-00";
				} else	if ($DOCDATE=="มีนาคม 2557") {
					$DOCDATE = "2014-03-00";
				} else	if ($DOCDATE=="22 กันยายน2557") {
					$DOCDATE = "2014-09-22";
				} else	if ($DOCDATE=="ธันวาคม 2557") {
					$DOCDATE = "2014-12-00";
				} else	if ($DOCDATE=="24เมษายน 2558") {
					$DOCDATE = "2015-04-24";
				} else	if ($DOCDATE=="24 มิถุนายน.2558") {
					$DOCDATE = "2015-06-24";
				} else	if ($DOCDATE=="29 ดดด bbbb") {
					$DOCDATE = "2015-00-00";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if ($DOCDATE=="07052517") {
					$DOCDATE = "1974-05-07";
				} else	if (strpos($DOCDATE," ") !== false) {
					$arr_temp = explode(" ", $DOCDATE);
					$dd = str_pad(trim($arr_temp[0]), 2, "0", STR_PAD_LEFT);
					if (trim($arr_temp[1])=="มกราคม" || trim($arr_temp[1])=="ม.ค." || trim($arr_temp[1])=="ม.ค" || trim($arr_temp[1])=="ม.คง" || trim($arr_temp[1])=="ม,ค," || trim($arr_temp[1])=="มกราาคม" || trim($arr_temp[1])=="มกราคม." || trim($arr_temp[1])=="มกาคม" || trim($arr_temp[1])=="มกรคม" || trim($arr_temp[1])=="มกราตม" || trim($arr_temp[1])=="มดราคม") $mm = "01";
					elseif (trim($arr_temp[1])=="กุมภาพันธ์" || trim($arr_temp[1])=="กุมภาภันธ์" || trim($arr_temp[1])=="กุมหภาพันธ์" || trim($arr_temp[1])=="กุมภาพันธฺ์" || trim($arr_temp[1])=="ก.พ." || 
						trim($arr_temp[1])=="กพ." || trim($arr_temp[1])=="กพ" || trim($arr_temp[1])=="ก.พ" || trim($arr_temp[1])=="กุมภารพันธ์" || trim($arr_temp[1])=="กกุมภาพันธ์" || trim($arr_temp[1])=="กุมภาพันธื" || trim($arr_temp[1])=="กุมภานธ์" || trim($arr_temp[1])=="กุมภาพันธ" || trim($arr_temp[1])=="กุมภาพัน์" || trim($arr_temp[1])=="กุม-ภาพันธ์" || trim($arr_temp[1])=="กุมภาพนธ์" || trim($arr_temp[1])=="กมภาพันธ์" || trim($arr_temp[1])=="กุทภาพันธ์" || trim($arr_temp[1])=="กุมภพันธ์" || trim($arr_temp[1])=="กุมพาพันธ์") $mm = "02";
					elseif (trim($arr_temp[1])=="มีนาคม" || trim($arr_temp[1])=="มี.ค." || trim($arr_temp[1])=="มี.คง" || trim($arr_temp[1])=="มี.ค" || trim($arr_temp[1])=="มี.ต." || trim($arr_temp[1])=="มึ.ค.") $mm = "03";
					elseif (trim($arr_temp[1])=="เมษายน" || trim($arr_temp[1])=="เม.ย." || trim($arr_temp[1])=="เม.ย" || trim($arr_temp[1])=="เม.ยง" || trim($arr_temp[1])=="เม.ร." || trim($arr_temp[1])=="เมายน" || trim($arr_temp[1])=="ม.ย." || trim($arr_temp[1])=="เมษายนจ" || trim($arr_temp[1])=="เมษษยน" || trim($arr_temp[1])=="เมษาน" || trim($arr_temp[1])=="เมษายน." || trim($arr_temp[1])=="มษายน") $mm = "04";
					elseif (trim($arr_temp[1])=="พฤษภาคม" || trim($arr_temp[1])=="พ.ค." || trim($arr_temp[1])=="พค." || trim($arr_temp[1])=="พ.ต." || trim($arr_temp[1])=="พ.ค" || trim($arr_temp[1])=="พฏษภาคม" || trim($arr_temp[1])=="พฤา๓ษ๕ฒ" || trim($arr_temp[1])=="พฤษภาคม." || trim($arr_temp[1])=="พฤาภาคม" || trim($arr_temp[1])=="พ.ฅ." || trim($arr_temp[1])=="พฤษภาคา" || trim($arr_temp[1])=="พฤษาคม") $mm = "05";
					elseif (trim($arr_temp[1])=="มิถุนายน" || trim($arr_temp[1])=="มิ.ย." || trim($arr_temp[1])=="มิ.ยง" || trim($arr_temp[1])=="มิ.ย" || trim($arr_temp[1])=="มฺ.ย." || trim($arr_temp[1])=="มิถนายน" || trim($arr_temp[1])=="มิงย." || trim($arr_temp[1])=="มื.ย." || trim($arr_temp[1])=="มิภุนายน" || trim($arr_temp[1])=="มิถุนายน." || trim($arr_temp[1])=="มิถุยสบย") $mm = "06";
					elseif (trim($arr_temp[1])=="กรกฎาคม" || trim($arr_temp[1])=="กรกฏาคม" || trim($arr_temp[1])=="ก.ค." || trim($arr_temp[1])=="ก.คง" || trim($arr_temp[1])=="ก.ต." || trim($arr_temp[1])=="กค." || trim($arr_temp[1])=="กรกาคม" || trim($arr_temp[1])=="กุรกฎาคม" || trim($arr_temp[1])=="กรกฎคม" || trim($arr_temp[1])=="กรฏาคม" || trim($arr_temp[1])=="กรกฎาสคม" || trim($arr_temp[1])=="ก.ค" || trim($arr_temp[1])=="ก.คฅ" || trim($arr_temp[1])=="กรฎาคม" || trim($arr_temp[1])=="กรกำคม" || trim($arr_temp[1])=="กรกฎาคม." || trim($arr_temp[1])=="กรกฎมคม" || trim($arr_temp[1])=="กนกฎาคม" || trim($arr_temp[1])=="ก..ค.") $mm = "07";
					elseif (trim($arr_temp[1])=="สิงหาคม" || trim($arr_temp[1])=="สิงหาค" || trim($arr_temp[1])=="ส.ค." || trim($arr_temp[1])=="ส.ค" || trim($arr_temp[1])=="ส.คง" || trim($arr_temp[1])=="สค." || trim($arr_temp[1])=="าส.ค." || trim($arr_temp[1])=="สิงหาคม." || trim($arr_temp[1])=="สืงหาคม" || trim($arr_temp[1])=="ก.ส.ค" || trim($arr_temp[1])=="สิงหาค-ม" || trim($arr_temp[1])=="lbsk8," || trim($arr_temp[1])=="สืงกาคม" || trim($arr_temp[1])=="สิงหคม" || trim($arr_temp[1])=="งหาคม" || trim($arr_temp[1])=="ส..ค." || trim($arr_temp[1])=="สิงหาคทม" || trim($arr_temp[1])=="สคฅ" || trim($arr_temp[1])=="สค" || trim($arr_temp[1])=="สางหาคม") $mm = "08";
					elseif (trim($arr_temp[1])=="กันยายน" || trim($arr_temp[1])=="ก.ย." || trim($arr_temp[1])=="ก.ย" || trim($arr_temp[1])=="ก.ยง" || trim($arr_temp[1])=="กันยายนม" || trim($arr_temp[1])=="ก.บ." || trim($arr_temp[1])=="กันยาายน" || trim($arr_temp[1])=="กันยยน" || trim($arr_temp[1])=="กีนยายน" || trim($arr_temp[1])=="กันายายน" || trim($arr_temp[1])=="ก.ญ." || trim($arr_temp[1])=="กันนายน" || trim($arr_temp[1])=="กันยาย" || trim($arr_temp[1])=="กันยายา") $mm = "09";
					elseif (trim($arr_temp[1])=="ตุลาคม" || trim($arr_temp[1])=="ต.ค." || trim($arr_temp[1])=="ต.คย." || trim($arr_temp[1])=="ตค" || trim($arr_temp[1])=="ต.ค" || 
						trim($arr_temp[1])=="ต.คง" || trim($arr_temp[1])=="าต.ค." || trim($arr_temp[1])=="ตงค." || trim($arr_temp[1])=="ต.ย." || trim($arr_temp[1])=="ตุลาสคม" || trim($arr_temp[1])=="ตุลาาคม" || trim($arr_temp[1])=="ตุลาคม." || trim($arr_temp[1])=="ตจุลาคม" || trim($arr_temp[1])=="ด.ค." || trim($arr_temp[1])=="ตุบาคม" || trim($arr_temp[1])=="ตุ.ค." || trim($arr_temp[1])=="ตุลายน" || trim($arr_temp[1])=="ตุลาควม" || trim($arr_temp[1])=="ตุลาตม") $mm = "10";
					elseif (trim($arr_temp[1])=="พฤศจิกายน" || trim($arr_temp[1])=="พ.ย." || trim($arr_temp[1])=="พย" || trim($arr_temp[1])=="พย." || trim($arr_temp[1])=="พ.ย" || 
						trim($arr_temp[1])=="พ.ยง" || trim($arr_temp[1])=="พ..ย" || trim($arr_temp[1])=="พฤสจิกายน" || trim($arr_temp[1])=="พฤศจิกยน" || trim($arr_temp[1])=="พฤศจิกายน." || trim($arr_temp[1])=="พฤศจิกาายน" || trim($arr_temp[1])=="พฤษศจิกายน" || trim($arr_temp[1])=="พฤษติกายน" || trim($arr_temp[1])=="พฤจิกายน" || trim($arr_temp[1])=="พฤศจิกาบน" || trim($arr_temp[1])=="พฤศจืกายน" || trim($arr_temp[1])=="พฤศจิการยน") $mm = "11";
					elseif (trim($arr_temp[1])=="ธันวาคม" || trim($arr_temp[1])=="ธันวาคา" || trim($arr_temp[1])=="ธ.ค." || trim($arr_temp[1])=="ธ.คง" || trim($arr_temp[1])=="ธ.ค" || trim($arr_temp[1])=="ธ .ค." || trim($arr_temp[1])=="ธงค." || trim($arr_temp[1])=="ะ.ค." || trim($arr_temp[1])=="ธันวคาม" || trim($arr_temp[1])=="ธันวาตม" || trim($arr_temp[1])=="ธ.คฅ" || trim($arr_temp[1])=="ธ,ค." || trim($arr_temp[1])=="ธีนวาคม" || trim($arr_temp[1])=="ธันาคม" || trim($arr_temp[1])=="ธันวาคม." || trim($arr_temp[1])=="ธนวาคม" || trim($arr_temp[1])=="ธัวาคม") $mm = "12";
					elseif (substr($arr_temp[1],0,4)=="ม.ค.") {
						$mm = "01";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="มค." || substr($arr_temp[1],0,3)=="ม.ค") {
						$mm = "01";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,4)=="ก.พ." || substr($arr_temp[1],0,4)=="กงพ.") {
						$mm = "02";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="กพ." || substr($arr_temp[1],0,3)=="ก.พ") {
						$mm = "02";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,5)=="มี.ค." || substr($arr_temp[1],0,5)=="ม๊.ค." || substr($arr_temp[1],0,5)=="มึ.ค." || substr($arr_temp[1],0,5)=="มี.คง") {
						$mm = "03";
						$yy = trim(substr($arr_temp[1],5));
					} elseif (substr($arr_temp[1],0,4)=="มีค.") {
						$mm = "03";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,5)=="เม.ย." || substr($arr_temp[1],0,5)=="เม.ยง") {
						$mm = "04";
						$yy = trim(substr($arr_temp[1],5));
					} elseif (substr($arr_temp[1],0,4)=="เมย." || substr($arr_temp[1],0,4)=="เมษ." || substr($arr_temp[1],0,4)=="เม.ย") {
						$mm = "04";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,4)=="พ.ค." || substr($arr_temp[1],0,4)=="พ.คง" || substr($arr_temp[1],0,4)=="พงค." || substr($arr_temp[1],0,4)=="พ.ต.") {
						$mm = "05";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="พค.") {
						$mm = "05";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,5)=="มิ.ย." || substr($arr_temp[1],0,5)=="มิ.ยง") {
						$mm = "06";
						$yy = trim(substr($arr_temp[1],5));
					} elseif (substr($arr_temp[1],0,4)=="มิย.") {
						$mm = "06";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,4)=="ก.ค." || substr($arr_temp[1],0,4)=="ก.คง" || substr($arr_temp[1],0,4)=="ก.ต.") {
						$mm = "07";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="กค.") {
						$mm = "07";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,4)=="ส.ค.") {
						$mm = "08";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="สค.") {
						$mm = "08";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,4)=="ก.ย." || substr($arr_temp[1],0,4)=="ก.ยง" || substr($arr_temp[1],0,4)=="กซนซ" || substr($arr_temp[1],0,4)=="ก.ยซ") {
						$mm = "09";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="กย." || substr($arr_temp[1],0,2)=="กย") {
						$mm = "09";
						$yy = trim(substr($arr_temp[1],2));
					} elseif (substr($arr_temp[1],0,4)=="ต.ค." || substr($arr_temp[1],0,4)=="ต.คง" || substr($arr_temp[1],0,4)=="ตงค.") {
						$mm = "10";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="ตค." || substr($arr_temp[1],0,3)=="ต.ต") {
						$mm = "10";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,3)=="ต.ค") {
						$mm = "10";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,4)=="พ.ย.") {
						$mm = "11";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="พย.") {
						$mm = "11";
						$yy = trim(substr($arr_temp[1],3));
					} elseif (substr($arr_temp[1],0,4)=="ธ.ค." || substr($arr_temp[1],0,4)=="ธ.คง") {
						$mm = "12";
						$yy = trim(substr($arr_temp[1],4));
					} elseif (substr($arr_temp[1],0,3)=="ธค.") {
						$mm = "12";
						$yy = trim(substr($arr_temp[1],3));
					} elseif ($arr_temp[0]=="21ธ.ค." && $arr_temp[1]=="2542") {
						$dd = "21";
						$mm = "12";
						$yy = "2542";
					} elseif ($arr_temp[0]=="25กุมภาพันธ์" && $arr_temp[1]=="2543") {
						$dd = "25";
						$mm = "02";
						$yy = "2543";
					} elseif ($arr_temp[0]=="27ต.ค." && $arr_temp[1]=="2543") {
						$dd = "27";
						$mm = "10";
						$yy = "2543";
					} elseif ($arr_temp[0]=="22พ.ค." && $arr_temp[1]=="2545") {
						$dd = "22";
						$mm = "05";
						$yy = "2545";
					} elseif ($arr_temp[0]=="1พ.ย." && $arr_temp[1]=="2545") {
						$dd = "01";
						$mm = "11";
						$yy = "2545";
					} elseif ($arr_temp[0]=="4ธันวาคม" && $arr_temp[1]=="2545") {
						$dd = "04";
						$mm = "12";
						$yy = "2545";
					} elseif ($arr_temp[0]=="28ม.ค." && $arr_temp[1]=="2546") {
						$dd = "28";
						$mm = "01";
						$yy = "2546";
					} elseif ($arr_temp[0]=="11สิงหาคม" && $arr_temp[1]=="2546") {
						$dd = "11";
						$mm = "08";
						$yy = "2546";
					} elseif ($arr_temp[0]=="17มกราคม" && $arr_temp[1]=="2550") {
						$dd = "17";
						$mm = "01";
						$yy = "2550";
					} elseif ($arr_temp[0]=="1ก.พ." && $arr_temp[1]=="2551") {
						$dd = "01";
						$mm = "02";
						$yy = "2551";
					} elseif ($arr_temp[0]=="27ก.พ." && $arr_temp[1]=="2551") {
						$dd = "27";
						$mm = "02";
						$yy = "2551";
					} elseif ($arr_temp[0]=="7ต.ค." && $arr_temp[1]=="2551") {
						$dd = "07";
						$mm = "10";
						$yy = "2551";
					} elseif ($arr_temp[0]=="27ก.พ." && $arr_temp[1]=="52") {
						$dd = "27";
						$mm = "02";
						$yy = "2552";
					} elseif ($arr_temp[0]=="11ก.ย." && $arr_temp[1]=="52") {
						$dd = "11";
						$mm = "09";
						$yy = "2552";
					} elseif ($arr_temp[0]=="30พ.ค." && $arr_temp[1]=="2555") {
						$dd = "30";
						$mm = "05";
						$yy = "2555";
					} elseif ($arr_temp[0]=="28" && $arr_temp[1]=="กรกฎาคม2547") {
						$dd = "28";
						$mm = "07";
						$yy = "2547";
					} elseif ($arr_temp[0]=="6" && $arr_temp[1]=="ธันวาคม2550") {
						$dd = "06";
						$mm = "12";
						$yy = "2550";
					} elseif ($arr_temp[0]=="11" && $arr_temp[1]=="ธันวาคม2552") {
						$dd = "11";
						$mm = "12";
						$yy = "2552";
					} elseif ($arr_temp[0]=="16" && $arr_temp[1]=="ธ.ค2556.") {
						$dd = "16";
						$mm = "12";
						$yy = "2556";
					} elseif ($arr_temp[0]=="7" && $arr_temp[1]=="ก." && $arr_temp[2]=="ย.53") {
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
			if (substr($DESCRIPTION,0,5) == "โอนมา" || substr($DESCRIPTION,0,6) == "รับโอน") 
				$MOV_CODE = "10510";
			elseif (substr($DESCRIPTION,0,25) == "โอนไปสังกัดสำนักงานเทศบาล") 
				$MOV_CODE = "10620";
			elseif (substr($DESCRIPTION,0,5) == "โอนไป" || substr($DESCRIPTION,0,6) == "ให้โอน") 
				$MOV_CODE = "10610";
			elseif (substr($DESCRIPTION,0,4) == "ย้าย") 
				$MOV_CODE = "103";
			elseif (substr($DESCRIPTION,0,9) == "รายงานตัว") 
				$MOV_CODE = "11260";
			elseif (substr($DESCRIPTION,0,5) == "โอนมา") 
				$MOV_CODE = "";
			elseif (substr($DESCRIPTION,0,5) == "โอนมา") 
				$MOV_CODE = "";
			elseif (substr($DESCRIPTION,0,5) == "โอนมา") 
				$MOV_CODE = "";
			elseif (substr($DESCRIPTION,0,5) == "โอนมา") 
				$MOV_CODE = "";
			elseif (substr($DESCRIPTION,0,5) == "โอนมา") 
				$MOV_CODE = "";
			elseif (substr($DESCRIPTION,0,5) == "โอนมา") 
				$MOV_CODE = "";
			elseif (substr($DESCRIPTION,0,5) == "โอนมา") 
				$MOV_CODE = "";
			elseif (substr($DESCRIPTION,0,5) == "โอนมา") 
				$MOV_CODE = "";
			elseif (substr($DESCRIPTION,0,5) == "โอนมา") 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"เกษียณอายุก่อนกำหนด") !== false || strpos($DESCRIPTION,"เกษียณก่อนกำหนด") !== false) 
				$MOV_CODE = "11830";
			elseif (strpos($DESCRIPTION,"เกษียณอายุ") !== false) 
				$MOV_CODE = "11910";
			elseif (strpos($DESCRIPTION,"อนุญาตให้ข้าราชการลาออก") !== false || strpos($DESCRIPTION,"อนุญาตให้ลาออก") !== false || strpos($DESCRIPTION,"ลาออกจากราชการ") !== false) 
				$MOV_CODE = "11810";
			elseif (strpos($DESCRIPTION,"รักษาราชการแทน") !== false) {
				$MOV_CODE = "11010";
				$ES_CODE = "26";
			} elseif (strpos($DESCRIPTION,"รักษาการในตำแหน่ง") !== false || strpos($DESCRIPTION,"รักษาราชการในตำแหน่ง") !== false) {
				$MOV_CODE = "11020";
				$ES_CODE = "07";
			} elseif (strpos($DESCRIPTION,"ได้รับเงินเดือนเพิ่มขึ้นตามคุณวุฒิ") !== false || strpos($DESCRIPTION,"ได้รับเงินเดือนตามวุฒิ") !== false || strpos($DESCRIPTION,"ได้รับเงินเดือนตามคุณวุฒิ") !== false || 
				strpos($DESCRIPTION,"ได้รับเงินเพิ่มขึ้นตามคุณวุฒิ") !== false) 
				$MOV_CODE = "21510";
			elseif (strpos($DESCRIPTION,"ได้รับค่าตอบแทนพิเศษ") !== false || strpos($DESCRIPTION,"ได้รับเงินค่าตอบแทนพิเศษ") !== false || strpos($DESCRIPTION,"ได้รับเงินตอบแทนพิเศษ") !== false || 
				strpos($DESCRIPTION,"ได้ค่าตอบแทนพิเศษ") !== false) 
				$MOV_CODE = "21415";
			elseif (strpos($DESCRIPTION,"เลื่อนเงินเดือนระดับดีเด่น") !== false) 
				$MOV_CODE = "21345";
			elseif (strpos($DESCRIPTION,"เลื่อนเงินเดือนระดับดีมาก") !== false) 
				$MOV_CODE = "21335";
			elseif (strpos($DESCRIPTION,"เลื่อนเงินเดือนระดับดี") !== false) 
				$MOV_CODE = "21325";
			elseif (strpos($DESCRIPTION,"เลื่อนเงินเดือนระดับพอใช้") !== false) 
				$MOV_CODE = "21315";
			elseif (strpos($DESCRIPTION,"0.5 ขั้น") !== false) 
				$MOV_CODE = "21310";
			elseif (strpos($DESCRIPTION,"1 ขั้น") !== false) 
				$MOV_CODE = "21320";
			elseif (strpos($DESCRIPTION,"1.5 ขั้น") !== false) 
				$MOV_CODE = "21330";
			elseif (strpos($DESCRIPTION,"2 ขั้น") !== false) 
				$MOV_CODE = "21340";
			elseif (strpos($DESCRIPTION,"ลาศึกษา") !== false || strpos($DESCRIPTION,"ลาไปศึกษา") !== false) {
				$MOV_CODE = "11210";
				$ES_CODE = "08";
			} elseif (strpos($DESCRIPTION,"แก้ไขคำสั่ง") !== false) 
				$MOV_CODE = "11420";
			elseif (strpos($DESCRIPTION,"เงินเดือนเต็มขั้น") !== false) 
				$MOV_CODE = "21410";
			elseif (strpos($DESCRIPTION,"ไม่ เลื่อนขั้นเงินเดือน") !== false || strpos($DESCRIPTION,"ไม่ขั้นเงินเดือน") !== false || strpos($DESCRIPTION,"ไม่ขี้นขั้นเงินเดือน") !== false || 
				strpos($DESCRIPTION,"ไม่ขี้นเงินเดือน") !== false || strpos($DESCRIPTION,"ไม่ขึ้นขั้นเงินเดือน") !== false || strpos($DESCRIPTION,"ไม่ขึ้นเงินเดือน") !== false || 
				strpos($DESCRIPTION,"ไม่ด้เลื่อนขั้นเงินเดือน") !== false || strpos($DESCRIPTION,"ไม่ได้ เลื่อนขั้นเงินเดือน") !== false || strpos($DESCRIPTION,"ไม่ได้เเลื่อนขั้นเงินเดือน") !== false || 
				strpos($DESCRIPTION,"ไม่ได้ขึ้นเงินเดือน") !== false || strpos($DESCRIPTION,"ไม่ได้รับการเลื่อนขั้นเงินเดือน") !== false || strpos($DESCRIPTION,"ไม่ได้รับเงินเดือน") !== false || 
				strpos($DESCRIPTION,"ไม่ได้รัลเงินเดือน") !== false || strpos($DESCRIPTION,"ไม่ได้เลื่อนขั้น") !== false || strpos($DESCRIPTION,"ไม่เลื่อนขั้น") !== false) 
				$MOV_CODE = "21370";
			elseif (strpos($DESCRIPTION,"ใม่ได้เลื่อนเงินเดือน") !== false || strpos($DESCRIPTION,"ใม่เลื่อนเงินเดือน") !== false || strpos($DESCRIPTION,"ไม่ได้ เลื่อนเงินเดือน") !== false || 
				strpos($DESCRIPTION,"ไม่ได้เลิ่อนเงินเดือน") !== false || strpos($DESCRIPTION,"ไม่ได้เลื่อนเงินเดือน") !== false || strpos($DESCRIPTION,"ไม่เลื่อนเงินเดือน") !== false) 
				$MOV_CODE = "21375";
			elseif (strpos($DESCRIPTION,"เลื่อนขั้นเงินเดือนประจำปี") !== false) 
				$MOV_CODE = "213";
			elseif (strpos($DESCRIPTION,"เลื่อนระดับ") !== false || substr($DESCRIPTION,0,6) == "เลื่อน") 
				$MOV_CODE = "104";
			elseif (strpos($DESCRIPTION,"ปรับอัตราเงินเดือน") !== false || strpos($DESCRIPTION,"ปรับอัตราเงืนเดือน") !== false) 
				$MOV_CODE = "21520";
			elseif (strpos($DESCRIPTION,"ไม่พ้นจากการทดลอง") !== false || strpos($DESCRIPTION,"ไม่พ้นทดลอง") !== false) 
				$MOV_CODE = "10220";
			elseif (strpos($DESCRIPTION,"พ้นจากการทดลอง") !== false || strpos($DESCRIPTION,"พ้นทดลอง") !== false || strpos($DESCRIPTION,"มาตรฐานที่กำหนดรับราชการต่อไป") !== false || 
				strpos($DESCRIPTION,"ไม่ต่ำกว่าเกณฑ์รับราชการต่อไป") !== false || strpos($DESCRIPTION,"พ้นจากทดลอง") !== false) 
				$MOV_CODE = "10210";
			elseif (strpos($DESCRIPTION,"ยกเลิกคำสั่ง") !== false || strpos($DESCRIPTION,"ยกเลิคำสั่ง") !== false) 
				$MOV_CODE = "11410";
			elseif (strpos($DESCRIPTION,"ยกเลิกเงินค่าตอบแทน") !== false || strpos($DESCRIPTION,"ยกเลิกเงินตอบแทน") !== false || strpos($DESCRIPTION,"ยกาเลิกเงินค่าตอบแทน") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"ยกเลิกเลื่อนขั้นเงินเดือน") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"ยกเลิกเลื่อนเงินเดือน") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"ยกเลิกเลื่อนระดับ") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"ยกเลิกให้ได้รับค่าตอบแทนพิเศษ") !== false || strpos($DESCRIPTION,"ยกเลืกเงินค่าตอบแทน") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"ลงโทษตัดเงินเดือน") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"ลงโทษปลดออก") !== false) 
				$MOV_CODE = "12110";
			elseif (strpos($DESCRIPTION,"ลงโทษภาคทัณฑ์") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"ลงโทษลดขั้นเงินเดือน") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"ลงโทษไล่ออก") !== false || strpos($DESCRIPTION,"ไล่ออกจากราชการ") !== false) 
				$MOV_CODE = "12210";
			elseif (strpos($DESCRIPTION,"ลดขั้นเงินเดือน 0.5 ขั้น") !== false) 
				$MOV_CODE = "21610";
			elseif (strpos($DESCRIPTION,"ลดขั้นเงินเดือน 1 ขั้น") !== false) 
				$MOV_CODE = "21620";
			elseif (strpos($DESCRIPTION,"ลากิจส่วนตัว") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"ลาคลอด") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"ลาติดตามคู่สมรส") !== false) 
				$MOV_CODE = "11220";
			elseif (strpos($DESCRIPTION,"ลาป่วย") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"ลาฝึกอบรม") !== false) 
				$MOV_CODE = "11211";
			elseif (strpos($DESCRIPTION,"ส่งตัวกลับ") !== false) 
				$MOV_CODE = "11250";
			elseif (strpos($DESCRIPTION,"ได้รับเงินประจำตำแหน่ง") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"ข้าราชการปฏิบัติราชการ") !== false || strpos($DESCRIPTION,"ข้าราชการปฏิบัติหน้าที่") !== false || strpos($DESCRIPTION,"ข้าราชการไปช่วยปฎิบัติราชการ") !== false || 
				strpos($DESCRIPTION,"ข้าราชการไปปฏิบัติราชการ") !== false || strpos($DESCRIPTION,"ให้ปฎิบัติราชการ") !== false || strpos($DESCRIPTION,"ให้ปฏิบัติหน้าที่ราชการ") !== false || 
				strpos($DESCRIPTION,"ให้ไปช่วยปฏิบัติราชการ") !== false || strpos($DESCRIPTION,"ให้ไปปฎิบัติราชการ") !== false) 
				$MOV_CODE = "11310";
			elseif (strpos($DESCRIPTION,"เงินเดือน") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"เงินเดือน") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"เงินเดือน") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"เงินเดือน") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"เงินเดือน") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"เงินเดือน") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"เงินเดือน") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"เงินเดือน") !== false) 
				$MOV_CODE = "";
			elseif (strpos($DESCRIPTION,"เงินเดือน") !== false) 
				$MOV_CODE = "";
			else {
				if (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล 1") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล 1", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล 2") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล 2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจว้าหน้าที่พยาบาล 2") !== false) {
					$arr_temp = explode("เจว้าหน้าที่พยาบาล 2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล 3") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าาหน้าที่พยาบาล 3") !== false) {
					$arr_temp = explode("เจ้าาหน้าที่พยาบาล 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ผู้ช่วยพยาบาล (ผู้ช่วยพยาบาล 1)") !== false) {
					$arr_temp = explode("ผู้ช่วยพยาบาล (ผู้ช่วยพยาบาล 1)", $DESCRIPTION);
					$POH_PL_NAME = "ผู้ช่วยพยาบาล (ผู้ช่วยพยาบาล 1)";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาลเทคนิค 2") !== false) {
					$arr_temp = explode("พยาบาลเทคนิค 2", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลเทคนิค 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาลเทคนิค 3") !== false) {
					$arr_temp = explode("พยาบาลเทคนิค 3", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลเทคนิค 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาลเทคนิค 4") !== false) {
					$arr_temp = explode("พยาบาลเทคนิค 4", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลเทคนิค 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาเทคนิค 4") !== false) {
					$arr_temp = explode("พยาบาเทคนิค 4", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลเทคนิค 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาลเทคนิค 5") !== false) {
					$arr_temp = explode("พยาบาลเทคนิค 5", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลเทคนิค 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาลเทคนิค 6") !== false) {
					$arr_temp = explode("พยาบาลเทคนิค 6", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลเทคนิค 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่สาธารณสุข 1") !== false) {
					$arr_temp = explode("เจ้าหน้าที่สาธารณสุข 1", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่สาธารณสุข 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ผู้ช่วยพยาบาล 1") !== false) {
					$arr_temp = explode("ผู้ช่วยพยาบาล 1", $DESCRIPTION);
					$POH_PL_NAME = "ผู้ช่วยพยาบาล 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ผู้ช่วยพยาบาล 2") !== false) {
					$arr_temp = explode("ผู้ช่วยพยาบาล 2", $DESCRIPTION);
					$POH_PL_NAME = "ผู้ช่วยพยาบาล 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ผู้ช่วยพยาบาล 3") !== false) {
					$arr_temp = explode("ผู้ช่วยพยาบาล 3", $DESCRIPTION);
					$POH_PL_NAME = "ผู้ช่วยพยาบาล 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ผู้ช่วยพยาบาล2") !== false) {
					$arr_temp = explode("ผู้ช่วยพยาบาล2", $DESCRIPTION);
					$POH_PL_NAME = "ผู้ช่วยพยาบาล2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ผู้ช่วยพยาบาล3") !== false) {
					$arr_temp = explode("ผู้ช่วยพยาบาล3", $DESCRIPTION);
					$POH_PL_NAME = "ผู้ช่วยพยาบาล3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาลวิชาชีพ 3") !== false) {
					$arr_temp = explode("พยาบาลวิชาชีพ 3", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลวิชาชีพ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาลวิชาชีพ 4") !== false) {
					$arr_temp = explode("พยาบาลวิชาชีพ 4", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลวิชาชีพ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาลวิชาชีพ 5") !== false) {
					$arr_temp = explode("พยาบาลวิชาชีพ 5", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลวิชาชีพ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาลวิชาชีพ 6 ว.") !== false) {
					$arr_temp = explode("พยาบาลวิชาชีพ 6 ว.", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลวิชาชีพ 6 ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาลวิชาชีพ 6") !== false) {
					$arr_temp = explode("พยาบาลวิชาชีพ 6", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลวิชาชีพ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาลวิชาชีพ 7วช.") !== false) {
					$arr_temp = explode("พยาบาลวิชาชีพ 7วช.", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลวิชาชีพ 7วช.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาลวิชาชีพ 7 วช.") !== false) {
					$arr_temp = explode("พยาบาลวิชาชีพ 7 วช.", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลวิชาชีพ 7 วช.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาลวิชาชีพ 7 วช") !== false) {
					$arr_temp = explode("พยาบาลวิชาชีพ 7 วช", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลวิชาชีพ 7 วช";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาลวิชาชีพ 7 ว.") !== false) {
					$arr_temp = explode("พยาบาลวิชาชีพ 7 ว.", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลวิชาชีพ 7 ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาลวิชาชีพ 8 วช.") !== false) {
					$arr_temp = explode("พยาบาลวิชาชีพ 8 วช.", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลวิชาชีพ 8 วช.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาลวิชาชีพ 8 วช") !== false) {
					$arr_temp = explode("พยาบาลวิชาชีพ 8 วช", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลวิชาชีพ 8 วช";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พิมพ์ดีด 1") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พิมพ์ดีด 1", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พิมพ์ดีด 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พิมพ์ดีด 2") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พิมพ์ดีด 2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พิมพ์ดีด 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พิมพ์ดีด 3") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พิมพ์ดีด 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พิมพ์ดีด 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่บันทึกข้อมูล 3") !== false) {
					$arr_temp = explode("เจ้าหน้าที่บันทึกข้อมูล 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่บันทึกข้อมูล 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานธุรการ 4") !== false) {
					$arr_temp = explode("เจ้าพนักงานธุรการ 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานธุรการ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานธุรการ 5") !== false) {
					$arr_temp = explode("เจ้าพนักงานธุรการ 5", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานธุรการ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานธุรการ 6") !== false) {
					$arr_temp = explode("เจ้าพนักงานธุรการ 6", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานธุรการ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่วิเคราะห์งานบุคคล 3") !== false) {
					$arr_temp = explode("เจ้าหน้าที่วิเคราะห์งานบุคคล 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่วิเคราะห์งานบุคคล 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่วิเคราะห์งานบุคคล 4") !== false) {
					$arr_temp = explode("เจ้าหน้าที่วิเคราะห์งานบุคคล 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่วิเคราะห์งานบุคคล 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่วิเคราะห์งานบุคคล 5") !== false) {
					$arr_temp = explode("เจ้าหน้าที่วิเคราะห์งานบุคคล 5", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่วิเคราะห์งานบุคคล 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่วิ่เคราะห์งานบุคคล 5") !== false) {
					$arr_temp = explode("เจ้าหน้าที่วิ่เคราะห์งานบุคคล 5", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่วิเคราะห์งานบุคคล 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่บริหารงานทั่วไป 5") !== false) {
					$arr_temp = explode("เจ้าหน้าที่บริหารงานทั่วไป 5", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่บริหารงานทั่วไป 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่บริหารงานทั่วไป 6") !== false) {
					$arr_temp = explode("เจ้าหน้าที่บริหารงานทั่วไป 6", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่บริหารงานทั่วไป 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่บริหารงานทั่วไป 7") !== false) {
					$arr_temp = explode("เจ้าหน้าที่บริหารงานทั่วไป 7", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่บริหารงานทั่วไป 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาล 2") !== false) {
					$arr_temp = explode("พยาบาล 2", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาล 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาล 3") !== false) {
					$arr_temp = explode("พยาบาล 3", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาล 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาล 4") !== false) {
					$arr_temp = explode("พยาบาล 4", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาล 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาล 5") !== false) {
					$arr_temp = explode("พยาบาล 5", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาล 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาล 6") !== false) {
					$arr_temp = explode("พยาบาล 6", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาล 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ครูพยาบาลตรี") !== false) {
					$arr_temp = explode("ครูพยาบาลตรี", $DESCRIPTION);
					$POH_PL_NAME = "ครูพยาบาลตรี";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาลตรี") !== false) {
					$arr_temp = explode("พยาบาลตรี", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลตรี";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาลโท") !== false) {
					$arr_temp = explode("พยาบาลโท", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลโท";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ผู้ช่วยพยาบาลจัตวา") !== false) {
					$arr_temp = explode("ผู้ช่วยพยาบาลจัตวา", $DESCRIPTION);
					$POH_PL_NAME = "ผู้ช่วยพยาบาลจัตวา";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาลจัตวา") !== false) {
					$arr_temp = explode("พยาบาลจัตวา", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลจัตวา";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการสาธารณสุข 4") !== false) {
					$arr_temp = explode("นักวิชาการสาธารณสุข 4", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการสาธารณสุข 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการสาธารณสุข 5") !== false) {
					$arr_temp = explode("นักวิชาการสาธารณสุข 5", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการสาธารณสุข 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการสาธารณสุข 6 ว") !== false) {
					$arr_temp = explode("นักวิชาการสาธารณสุข 6 ว", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการสาธารณสุข 6 ว";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการสาธารณสุข 6ว.") !== false) {
					$arr_temp = explode("นักวิชาการสาธารณสุข 6ว.", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการสาธารณสุข 6ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการสาธารณสุข 6") !== false) {
					$arr_temp = explode("นักวิชาการสาธารณสุข 6", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการสาธารณสุข 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการสาธารณสุข 7 ว.") !== false) {
					$arr_temp = explode("นักวิชาการสาธารณสุข 7 ว.", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการสาธารณสุข 7 ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการสาธารณสุข 7") !== false) {
					$arr_temp = explode("นักวิชาการสาธารณสุข 7", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการสาธารณสุข 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการสาธารณสุข 8 ว.") !== false) {
					$arr_temp = explode("นักวิชาการสาธารณสุข 8 ว.", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการสาธารณสุข 8 ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เสมียนพนักงาน") !== false) {
					$arr_temp = explode("เสมียนพนักงาน", $DESCRIPTION);
					$POH_PL_NAME = "เสมียนพนักงาน";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่ธุรการ 1") !== false) {
					$arr_temp = explode("เจ้าหน้าที่ธุรการ 1", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่ธุรการ 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่ธุรการ 2") !== false) {
					$arr_temp = explode("เจ้าหน้าที่ธุรการ 2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่ธุรการ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่ธุรการ 3") !== false) {
					$arr_temp = explode("เจ้าหน้าที่ธุรการ 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่ธุรการ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่ธุรการ 4") !== false) {
					$arr_temp = explode("เจ้าหน้าที่ธุรการ 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่ธุรการ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่บริหารงานธุรการ 5") !== false) {
					$arr_temp = explode("เจ้าหน้าที่บริหารงานธุรการ 5", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่บริหารงานธุรการ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่สาธารณสุข 2") !== false) {
					$arr_temp = explode("เจ้าหน้าที่สาธารณสุข 2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่สาธารณสุข 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่สาธารณสุข 3") !== false) {
					$arr_temp = explode("เจ้าหน้าที่สาธารณสุข 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่สาธารณสุข 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่เวชสถิติ 1") !== false) {
					$arr_temp = explode("เจ้าหน้าที่เวชสถิติ 1", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่เวชสถิติ 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่เวชสถิติ 2") !== false) {
					$arr_temp = explode("เจ้าหน้าที่เวชสถิติ 2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่เวชสถิติ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่เวชสถิติ 3") !== false) {
					$arr_temp = explode("เจ้าหน้าที่เวชสถิติ 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่เวชสถิติ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่เวชสถิติ 4") !== false) {
					$arr_temp = explode("เจ้าหน้าที่เวชสถิติ 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่เวชสถิติ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่เวชสถิติ 5") !== false) {
					$arr_temp = explode("เจ้าหน้าที่เวชสถิติ 5", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่เวชสถิติ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่เวชสถิติ 6") !== false) {
					$arr_temp = explode("เจ้าหน้าที่เวชสถิติ 6", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่เวชสถิติ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่เวชสถิติ 7") !== false) {
					$arr_temp = explode("เจ้าหน้าที่เวชสถิติ 7", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่เวชสถิติ 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่เวชสถิติ2") !== false) {
					$arr_temp = explode("เจ้าหน้าที่เวชสถิติ2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่เวชสถิติ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่เวชสถิติ3") !== false) {
					$arr_temp = explode("เจ้าหน้าที่เวชสถิติ3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่เวชสถิติ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่เวชสถิติ4") !== false) {
					$arr_temp = explode("เจ้าหน้าที่เวชสถิติ4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่เวชสถิติ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักจิตวิทยา 3") !== false) {
					$arr_temp = explode("นักจิตวิทยา 3", $DESCRIPTION);
					$POH_PL_NAME = "นักจิตวิทยา 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักจิตวิทยา 4") !== false) {
					$arr_temp = explode("นักจิตวิทยา 4", $DESCRIPTION);
					$POH_PL_NAME = "นักจิตวิทยา 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักจิตวิทยา 5") !== false) {
					$arr_temp = explode("นักจิตวิทยา 5", $DESCRIPTION);
					$POH_PL_NAME = "นักจิตวิทยา 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักจิตวิทยา 6 ว.") !== false) {
					$arr_temp = explode("นักจิตวิทยา 6 ว.", $DESCRIPTION);
					$POH_PL_NAME = "นักจิตวิทยา 6 ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักจิตวิทยา 6 ว") !== false) {
					$arr_temp = explode("นักจิตวิทยา 6 ว", $DESCRIPTION);
					$POH_PL_NAME = "นักจิตวิทยา 6 ว";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักจิตวิทยา 6") !== false) {
					$arr_temp = explode("นักจิตวิทยา 6", $DESCRIPTION);
					$POH_PL_NAME = "นักจิตวิทยา 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักจิตวิทยาตรี") !== false) {
					$arr_temp = explode("นักจิตวิทยาตรี", $DESCRIPTION);
					$POH_PL_NAME = "นักจิตวิทยาตรี";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักจิตวิทยาโท") !== false) {
					$arr_temp = explode("นักจิตวิทยาโท", $DESCRIPTION);
					$POH_PL_NAME = "นักจิตวิทยาโท";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานธุรการ 3") !== false) {
					$arr_temp = explode("เจ้าพนักงานธุรการ 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานธุรการ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานธุรการ 4") !== false) {
					$arr_temp = explode("เจ้าพนักงานธุรการ 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานธุรการ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานธุรการ 5") !== false) {
					$arr_temp = explode("เจ้าพนักงานธุรการ 5", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานธุรการ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักสังคมสงเคราะห์ 3") !== false) {
					$arr_temp = explode("นักสังคมสงเคราะห์ 3", $DESCRIPTION);
					$POH_PL_NAME = "นักสังคมสงเคราะห์ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักสังคมสงเคราะห์ 4") !== false) {
					$arr_temp = explode("นักสังคมสงเคราะห์ 4", $DESCRIPTION);
					$POH_PL_NAME = "นักสังคมสงเคราะห์ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักสังคมสงเคราะห์ 5") !== false) {
					$arr_temp = explode("นักสังคมสงเคราะห์ 5", $DESCRIPTION);
					$POH_PL_NAME = "นักสังคมสงเคราะห์ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักสังคมสงเคราะห์ 6") !== false) {
					$arr_temp = explode("นักสังคมสงเคราะห์ 6", $DESCRIPTION);
					$POH_PL_NAME = "นักสังคมสงเคราะห์ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักสังคมสงเคราะห์ 7 ว.") !== false) {
					$arr_temp = explode("นักสังคมสงเคราะห์ 7 ว.", $DESCRIPTION);
					$POH_PL_NAME = "นักสังคมสงเคราะห์ 7 ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักสังคมสงเคราะห์ตรี") !== false) {
					$arr_temp = explode("นักสังคมสงเคราะห์ตรี", $DESCRIPTION);
					$POH_PL_NAME = "นักสังคมสงเคราะห์ตรี";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักสังคมสงเคราะห์โท") !== false) {
					$arr_temp = explode("นักสังคมสงเคราะห์โท", $DESCRIPTION);
					$POH_PL_NAME = "นักสังคมสงเคราะห์โท";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่การเงินและบัญชี 1") !== false) {
					$arr_temp = explode("เจ้าหน้าที่การเงินและบัญชี 1", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่การเงินและบัญชี 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่การเงินและบัญชี 2") !== false) {
					$arr_temp = explode("เจ้าหน้าที่การเงินและบัญชี 2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่การเงินและบัญชี 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่การเงินและบัญชี 3") !== false) {
					$arr_temp = explode("เจ้าหน้าที่การเงินและบัญชี 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่การเงินและบัญชี 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่การเงินและบัญชี 4") !== false) {
					$arr_temp = explode("เจ้าหน้าที่การเงินและบัญชี 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่การเงินและบัญชี 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่การเงินและบัญชี 5") !== false) {
					$arr_temp = explode("เจ้าหน้าที่การเงินและบัญชี 5", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่การเงินและบัญชี 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานการเงินและบัญชี 2") !== false) {
					$arr_temp = explode("เจ้าพนักงานการเงินและบัญชี 2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานการเงินและบัญชี 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานการเงินและบัญชี 3") !== false) {
					$arr_temp = explode("เจ้าพนักงานการเงินและบัญชี 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานการเงินและบัญชี 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานการเงินและบัญชี 4") !== false) {
					$arr_temp = explode("เจ้าพนักงานการเงินและบัญชี 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานการเงินและบัญชี 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานการเงินและบัญชี 5") !== false) {
					$arr_temp = explode("เจ้าพนักงานการเงินและบัญชี 5", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานการเงินและบัญชี 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานการเงินและบัญชี 6") !== false) {
					$arr_temp = explode("เจ้าพนักงานการเงินและบัญชี 6", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานการเงินและบัญชี 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่บันทึกข้อมูล 2") !== false) {
					$arr_temp = explode("เจ้าหน้าที่บันทึกข้อมูล 2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่บันทึกข้อมูล 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่บันทึกข้อมูล 3") !== false) {
					$arr_temp = explode("เจ้าหน้าที่บันทึกข้อมูล 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่บันทึกข้อมูล 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่โภชนาการ 1") !== false) {
					$arr_temp = explode("เจ้าหน้าที่โภชนาการ 1", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่โภชนาการ 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่โภชนาการ 2") !== false) {
					$arr_temp = explode("เจ้าหน้าที่โภชนาการ 2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่โภชนาการ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พนักงานโภชนาการ 2") !== false) {
					$arr_temp = explode("พนักงานโภชนาการ 2", $DESCRIPTION);
					$POH_PL_NAME = "พนักงานโภชนาการ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"โภชนากร 2") !== false) {
					$arr_temp = explode("โภชนากร 2", $DESCRIPTION);
					$POH_PL_NAME = "โภชนากร 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"โภชนากร 3") !== false) {
					$arr_temp = explode("โภชนากร 3", $DESCRIPTION);
					$POH_PL_NAME = "โภชนากร 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"โภชนากร 4") !== false) {
					$arr_temp = explode("โภชนากร 4", $DESCRIPTION);
					$POH_PL_NAME = "โภชนากร 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"โภชนากร 5") !== false) {
					$arr_temp = explode("โภชนากร 5", $DESCRIPTION);
					$POH_PL_NAME = "โภชนากร 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"โภชนากร 6") !== false) {
					$arr_temp = explode("โภชนากร 6", $DESCRIPTION);
					$POH_PL_NAME = "โภชนากร 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"โภชนากรตรี") !== false) {
					$arr_temp = explode("โภชนากรตรี", $DESCRIPTION);
					$POH_PL_NAME = "โภชนากรตรี";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"โภชนากร ตรี") !== false) {
					$arr_temp = explode("โภชนากร ตรี", $DESCRIPTION);
					$POH_PL_NAME = "โภชนากรตรี";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ผู้ช่วยเภสัชกร 1") !== false) {
					$arr_temp = explode("ผู้ช่วยเภสัชกร 1", $DESCRIPTION);
					$POH_PL_NAME = "ผู้ช่วยเภสัชกร 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ผู้ช่วยเภสัชกร 2") !== false) {
					$arr_temp = explode("ผู้ช่วยเภสัชกร 2", $DESCRIPTION);
					$POH_PL_NAME = "ผู้ช่วยเภสัชกร 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ผู้ช่วยเภสัชกร 3") !== false) {
					$arr_temp = explode("ผู้ช่วยเภสัชกร 3", $DESCRIPTION);
					$POH_PL_NAME = "ผู้ช่วยเภสัชกร 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานเภสัชกร 4") !== false) {
					$arr_temp = explode("เจ้าพนักงานเภสัชกร 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานเภสัชกร 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานเภสัชกรรม 2") !== false) {
					$arr_temp = explode("เจ้าพนักงานเภสัชกรรม 2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานเภสัชกรรม 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานเภสัชกรรม 3") !== false) {
					$arr_temp = explode("เจ้าพนักงานเภสัชกรรม 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานเภสัชกรรม 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานเภสัชกรรม 4") !== false) {
					$arr_temp = explode("เจ้าพนักงานเภสัชกรรม 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานเภสัชกรรม 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานเภสัชกรรม 5") !== false) {
					$arr_temp = explode("เจ้าพนักงานเภสัชกรรม 5", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานเภสัชกรรม 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานเภสัชกรรม 6") !== false) {
					$arr_temp = explode("เจ้าพนักงานเภสัชกรรม 6", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานเภสัชกรรม 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายแพทย์ 4") !== false) {
					$arr_temp = explode("นายแพทย์ 4", $DESCRIPTION);
					$POH_PL_NAME = "นายแพทย์ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายแพทย์ 5") !== false) {
					$arr_temp = explode("นายแพทย์ 5", $DESCRIPTION);
					$POH_PL_NAME = "นายแพทย์ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายแพทย์ 6") !== false) {
					$arr_temp = explode("นายแพทย์ 6", $DESCRIPTION);
					$POH_PL_NAME = "นายแพทย์ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายแพทย์ 7 วช.") !== false) {
					$arr_temp = explode("นายแพทย์ 7 วช.", $DESCRIPTION);
					$POH_PL_NAME = "นายแพทย์ 7 วช.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"(นายแพทย์ 7)") !== false) {
					$arr_temp = explode("(นายแพทย์ 7)", $DESCRIPTION);
					$POH_PL_NAME = "นายแพทย์ 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายแพทย์ 7 นายแพทย์ผู้ชำนาญการพิเศษ") !== false) {
					$arr_temp = explode("นายแพทย์ 7 นายแพทย์ผู้ชำนาญการพิเศษ", $DESCRIPTION);
					$POH_PL_NAME = "นายแพทย์ 7 นายแพทย์ผู้ชำนาญการพิเศษ";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"(นายแพทย์ผู้ชำนาญการพิเศษ)") !== false) {
					$arr_temp = explode("(นายแพทย์ผู้ชำนาญการพิเศษ)", $DESCRIPTION);
					$POH_PL_NAME = "นายแพทย์ผู้ชำนาญการพิเศษ";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายแพทย์ 7 ผู้ชำนาญการพิเศษ") !== false) {
					$arr_temp = explode("นายแพทย์ 7 ผู้ชำนาญการพิเศษ", $DESCRIPTION);
					$POH_PL_NAME = "นายแพทย์ 7 ผู้ชำนาญการพิเศษ";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายแพทย์ 7") !== false) {
					$arr_temp = explode("นายแพทย์ 7", $DESCRIPTION);
					$POH_PL_NAME = "นายแพทย์ 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายแพทย์ 8 วช.") !== false) {
					$arr_temp = explode("นายแพทย์ 8 วช.", $DESCRIPTION);
					$POH_PL_NAME = "นายแพทย์ 8 วช.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายแพทย์ 8") !== false) {
					$arr_temp = explode("นายแพทย์ 8", $DESCRIPTION);
					$POH_PL_NAME = "นายแพทย์ 8";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายแพทย์ 9") !== false) {
					$arr_temp = explode("นายแพทย์ 9", $DESCRIPTION);
					$POH_PL_NAME = "นายแพทย์ 9";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายแพทย์ 10") !== false) {
					$arr_temp = explode("นายแพทย์ 10", $DESCRIPTION);
					$POH_PL_NAME = "นายแพทย์ 10";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายแพทย์โท") !== false) {
					$arr_temp = explode("นายแพทย์โท", $DESCRIPTION);
					$POH_PL_NAME = "นายแพทย์โท";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายแพทย์เอก") !== false) {
					$arr_temp = explode("นายแพทย์เอก", $DESCRIPTION);
					$POH_PL_NAME = "นายแพทย์เอก";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างไฟฟ้า 1") !== false) {
					$arr_temp = explode("ช่างไฟฟ้า 1", $DESCRIPTION);
					$POH_PL_NAME = "ช่างไฟฟ้า 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างไฟฟ้า 2") !== false) {
					$arr_temp = explode("ช่างไฟฟ้า 2", $DESCRIPTION);
					$POH_PL_NAME = "ช่างไฟฟ้า 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างไฟฟ้า 3") !== false) {
					$arr_temp = explode("ช่างไฟฟ้า 3", $DESCRIPTION);
					$POH_PL_NAME = "ช่างไฟฟ้า 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายช่างเทคนิค 4") !== false) {
					$arr_temp = explode("นายช่างเทคนิค 4", $DESCRIPTION);
					$POH_PL_NAME = "นายช่างเทคนิค 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายช่างเทคนิค 5") !== false) {
					$arr_temp = explode("นายช่างเทคนิค 5", $DESCRIPTION);
					$POH_PL_NAME = "นายช่างเทคนิค 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายช่างเทคนิค 6") !== false) {
					$arr_temp = explode("นายช่างเทคนิค 6", $DESCRIPTION);
					$POH_PL_NAME = "นายช่างเทคนิค 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่อาชีวบำบัด 2") !== false) {
					$arr_temp = explode("เจ้าหน้าที่อาชีวบำบัด 2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่อาชีวบำบัด 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่อาชีวบำบัด3") !== false) {
					$arr_temp = explode("เจ้าหน้าที่อาชีวบำบัด3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่อาชีวบำบัด 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่อาชีวบำบัด 4") !== false) {
					$arr_temp = explode("เจ้าหน้าที่อาชีวบำบัด 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่อาชีวบำบัด 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่อาชีบำบัด 4") !== false) {
					$arr_temp = explode("เจ้าหน้าที่อาชีบำบัด 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่อาชีวบำบัด 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่อาชีวบำบัด 5") !== false) {
					$arr_temp = explode("เจ้าหน้าที่อาชีวบำบัด 5", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่อาชีวบำบัด 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่อาชีวบำบัด 6") !== false) {
					$arr_temp = explode("เจ้าหน้าที่อาชีวบำบัด 6", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่อาชีวบำบัด 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่วิทยาศาสตร์การแพทย์ 1") !== false) {
					$arr_temp = explode("เจ้าหน้าที่วิทยาศาสตร์การแพทย์ 1", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่วิทยาศาสตร์การแพทย์ 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่วิทยาศาสตร์การแพทย์ 2") !== false) {
					$arr_temp = explode("เจ้าหน้าที่วิทยาศาสตร์การแพทย์ 2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่วิทยาศาสตร์การแพทย์ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่วิทยาศาสตร์การแพทย์ 3") !== false) {
					$arr_temp = explode("เจ้าหน้าที่วิทยาศาสตร์การแพทย์ 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่วิทยาศาสตร์การแพทย์ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่วิทยาศาสตร์การแพทย์ 4") !== false) {
					$arr_temp = explode("เจ้าหน้าที่วิทยาศาสตร์การแพทย์ 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่วิทยาศาสตร์การแพทย์ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่วิทยาศาสตร์การแพทย์ 5") !== false) {
					$arr_temp = explode("เจ้าหน้าที่วิทยาศาสตร์การแพทย์ 5", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่วิทยาศาสตร์การแพทย์ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานวิทยาศาสตร์การแพทย์ 2") !== false) {
					$arr_temp = explode("เจ้าพนักงานวิทยาศาสตร์การแพทย์ 2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานวิทยาศาสตร์การแพทย์ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานวิทยาศาสตร์การแพทย์ 3") !== false) {
					$arr_temp = explode("เจ้าพนักงานวิทยาศาสตร์การแพทย์ 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานวิทยาศาสตร์การแพทย์ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานวิทยาศาสตร์การแพทย์ 4") !== false) {
					$arr_temp = explode("เจ้าพนักงานวิทยาศาสตร์การแพทย์ 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานวิทยาศาสตร์การแพทย์ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานวิทยาศาสตร์การแพทย์ 5") !== false) {
					$arr_temp = explode("เจ้าพนักงานวิทยาศาสตร์การแพทย์ 5", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานวิทยาศาสตร์การแพทย์ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานวิทยาศาสตร์การแพทย์ 6") !== false) {
					$arr_temp = explode("เจ้าพนักงานวิทยาศาสตร์การแพทย์ 6", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานวิทยาศาสตร์การแพทย์ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่กายภาพบำบัด 1") !== false) {
					$arr_temp = explode("เจ้าหน้าที่กายภาพบำบัด 1", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่กายภาพบำบัด 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่กายภาพบำบัด 2") !== false) {
					$arr_temp = explode("เจ้าหน้าที่กายภาพบำบัด 2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่กายภาพบำบัด 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่กายภาพบำบัด 3") !== false) {
					$arr_temp = explode("เจ้าหน้าที่กายภาพบำบัด 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่กายภาพบำบัด 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานเวชกรรมฟื้นฟู 3") !== false) {
					$arr_temp = explode("เจ้าพนักงานเวชกรรมฟื้นฟู 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานเวชกรรมฟื้นฟู 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานเวชกรรมฟื้นฟู 4") !== false) {
					$arr_temp = explode("เจ้าพนักงานเวชกรรมฟื้นฟู 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานเวชกรรมฟื้นฟู 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานเวชกรรมฟื้นฟู 5") !== false) {
					$arr_temp = explode("เจ้าพนักงานเวชกรรมฟื้นฟู 5", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานเวชกรรมฟื้นฟู 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานเวชกรรมฟื้นฟู 6") !== false) {
					$arr_temp = explode("เจ้าพนักงานเวชกรรมฟื้นฟู 6", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานเวชกรรมฟื้นฟู 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่ห้องสมุด 1") !== false) {
					$arr_temp = explode("เจ้าหน้าที่ห้องสมุด 1", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่ห้องสมุด 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่ห้องสมุด 2") !== false) {
					$arr_temp = explode("เจ้าหน้าที่ห้องสมุด 2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่ห้องสมุด 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่ห้องสมุด 3") !== false) {
					$arr_temp = explode("เจ้าหน้าที่ห้องสมุด 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่ห้องสมุด 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่ห้องสมุด 4") !== false) {
					$arr_temp = explode("เจ้าหน้าที่ห้องสมุด 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่ห้องสมุด 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างภาพตรี") !== false) {
					$arr_temp = explode("ช่างภาพตรี", $DESCRIPTION);
					$POH_PL_NAME = "ช่างภาพตรี";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างภาพการแพทย์ 3") !== false) {
					$arr_temp = explode("ช่างภาพการแพทย์ 3", $DESCRIPTION);
					$POH_PL_NAME = "ช่างภาพการแพทย์ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างภาพการแพทย์ 4") !== false) {
					$arr_temp = explode("ช่างภาพการแพทย์ 4", $DESCRIPTION);
					$POH_PL_NAME = "ช่างภาพการแพทย์ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างภาพการแพทย์ 5") !== false) {
					$arr_temp = explode("ช่างภาพการแพทย์ 5", $DESCRIPTION);
					$POH_PL_NAME = "ช่างภาพการแพทย์ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างภาพการแพทย์ 6") !== false) {
					$arr_temp = explode("ช่างภาพการแพทย์ 6", $DESCRIPTION);
					$POH_PL_NAME = "ช่างภาพการแพทย์ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างภาพการแพทย์ 7ว.") !== false) {
					$arr_temp = explode("ช่างภาพการแพทย์ 7ว.", $DESCRIPTION);
					$POH_PL_NAME = "ช่างภาพการแพทย์ 7ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิทยาศาสตร์ตรี") !== false) {
					$arr_temp = explode("นักวิทยาศาสตร์ตรี", $DESCRIPTION);
					$POH_PL_NAME = "นักวิทยาศาสตร์ตรี";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิทยาศาสตร์โท") !== false) {
					$arr_temp = explode("นักวิทยาศาสตร์โท", $DESCRIPTION);
					$POH_PL_NAME = "นักวิทยาศาสตร์โท";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิทยาศาสตร์การแพทย์ 3") !== false) {
					$arr_temp = explode("นักวิทยาศาสตร์การแพทย์ 3", $DESCRIPTION);
					$POH_PL_NAME = "นักวิทยาศาสตร์การแพทย์ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิทยาศาสตร์การแพทย์ 4") !== false) {
					$arr_temp = explode("นักวิทยาศาสตร์การแพทย์ 4", $DESCRIPTION);
					$POH_PL_NAME = "นักวิทยาศาสตร์การแพทย์ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิทยาศาสตร์การแพทย์ 5") !== false) {
					$arr_temp = explode("นักวิทยาศาสตร์การแพทย์ 5", $DESCRIPTION);
					$POH_PL_NAME = "นักวิทยาศาสตร์การแพทย์ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิทยาศาสตร์การแพทย์ 6ว.") !== false) {
					$arr_temp = explode("นักวิทยาศาสตร์การแพทย์ 6ว.", $DESCRIPTION);
					$POH_PL_NAME = "นักวิทยาศาสตร์การแพทย์ 6ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิทยาศาสตร์การแพทย์ 6") !== false) {
					$arr_temp = explode("นักวิทยาศาสตร์การแพทย์ 6", $DESCRIPTION);
					$POH_PL_NAME = "นักวิทยาศาสตร์การแพทย์ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิทยาศาสตร์การแพทย์ 7 ว.") !== false) {
					$arr_temp = explode("นักวิทยาศาสตร์การแพทย์ 7 ว.", $DESCRIPTION);
					$POH_PL_NAME = "นักวิทยาศาสตร์การแพทย์ 7 ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิทยาศาสตร์การแพทย์ 8 ว") !== false) {
					$arr_temp = explode("นักวิทยาศาสตร์การแพทย์ 8 ว", $DESCRIPTION);
					$POH_PL_NAME = "นักวิทยาศาสตร์การแพทย์ 8 ว";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักเทคนิคการแพทย์ 6ว.") !== false) {
					$arr_temp = explode("นักเทคนิคการแพทย์ 6ว.", $DESCRIPTION);
					$POH_PL_NAME = "นักเทคนิคการแพทย์ 6ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักเทคนิคการแพทย์ 7 วช.") !== false) {
					$arr_temp = explode("นักเทคนิคการแพทย์ 7 วช.", $DESCRIPTION);
					$POH_PL_NAME = "นักเทคนิคการแพทย์ 7 วช.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักเทคนิคการแพทย์ 8 วช.") !== false) {
					$arr_temp = explode("นักเทคนิคการแพทย์ 8 วช.", $DESCRIPTION);
					$POH_PL_NAME = "นักเทคนิคการแพทย์ 8 วช.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานพัสดุ 3") !== false) {
					$arr_temp = explode("เจ้าพนักงานพัสดุ 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานพัสดุ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานพัสดุ 4") !== false) {
					$arr_temp = explode("เจ้าพนักงานพัสดุ 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานพัสดุ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานพัสดุ 5") !== false) {
					$arr_temp = explode("เจ้าพนักงานพัสดุ 5", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานพัสดุ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักกายภาพบำบัด 3") !== false) {
					$arr_temp = explode("นักกายภาพบำบัด 3", $DESCRIPTION);
					$POH_PL_NAME = "นักกายภาพบำบัด 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักกายภาพบำบัด 4") !== false) {
					$arr_temp = explode("นักกายภาพบำบัด 4", $DESCRIPTION);
					$POH_PL_NAME = "นักกายภาพบำบัด 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักกายภาพบำบัด 5") !== false) {
					$arr_temp = explode("นักกายภาพบำบัด 5", $DESCRIPTION);
					$POH_PL_NAME = "นักกายภาพบำบัด 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักกายภาพบำบัด 6ว.") !== false) {
					$arr_temp = explode("นักกายภาพบำบัด 6ว.", $DESCRIPTION);
					$POH_PL_NAME = "นักกายภาพบำบัด 6ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักกายภาพบำบัด 6") !== false) {
					$arr_temp = explode("นักกายภาพบำบัด 6", $DESCRIPTION);
					$POH_PL_NAME = "นักกายภาพบำบัด 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักกายภาพบำบัด 7วช.") !== false) {
					$arr_temp = explode("นักกายภาพบำบัด 7วช.", $DESCRIPTION);
					$POH_PL_NAME = "นักกายภาพบำบัด 7วช.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักกายภาพบำบัดตรี") !== false) {
					$arr_temp = explode("นักกายภาพบำบัดตรี", $DESCRIPTION);
					$POH_PL_NAME = "นักกายภาพบำบัดตรี";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ครูอาชีวบำบัดตรี") !== false) {
					$arr_temp = explode("ครูอาชีวบำบัดตรี", $DESCRIPTION);
					$POH_PL_NAME = "ครูอาชีวบำบัดตรี";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างจัตวา") !== false) {
					$arr_temp = explode("ช่างจัตวา", $DESCRIPTION);
					$POH_PL_NAME = "ช่างจัตวา";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายช่างอีเล็คโทรนิคส์ 3") !== false) {
					$arr_temp = explode("นายช่างอีเล็คโทรนิคส์ 3", $DESCRIPTION);
					$POH_PL_NAME = "นายช่างอีเล็คโทรนิคส์ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายช่างอิเลคทรอนิคส์ 4") !== false) {
					$arr_temp = explode("นายช่างอิเลคทรอนิคส์ 4", $DESCRIPTION);
					$POH_PL_NAME = "นายช่างอิเลคทรอนิคส์ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายช่างอิเลคทรอนิคส์ 5") !== false) {
					$arr_temp = explode("นายช่างอิเลคทรอนิคส์ 5", $DESCRIPTION);
					$POH_PL_NAME = "นายช่างอิเลคทรอนิคส์ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายช่างอิเล็คทรอนิคส์ 6") !== false) {
					$arr_temp = explode("นายช่างอิเล็คทรอนิคส์ 6", $DESCRIPTION);
					$POH_PL_NAME = "นายช่างอิเล็คทรอนิคส์ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างอีเล็กทรอนิคส์ 1") !== false) {
					$arr_temp = explode("ช่างอีเล็กทรอนิคส์ 1", $DESCRIPTION);
					$POH_PL_NAME = "ช่างอีเล็กทรอนิคส์ 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างอีเล็กทรอนิกส์ 2") !== false) {
					$arr_temp = explode("ช่างอีเล็กทรอนิกส์ 2", $DESCRIPTION);
					$POH_PL_NAME = "ช่างอีเล็กทรอนิกส์ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างอีเล็คทรอนิคส์ 3") !== false) {
					$arr_temp = explode("ช่างอีเล็คทรอนิคส์ 3", $DESCRIPTION);
					$POH_PL_NAME = "ช่างอีเล็คทรอนิคส์ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างอีเล็คโทรนิค 1") !== false) {
					$arr_temp = explode("ช่างอีเล็คโทรนิค 1", $DESCRIPTION);
					$POH_PL_NAME = "ช่างอีเลคทรอนิคส์ 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างอีเลคโทรนิค 2") !== false) {
					$arr_temp = explode("ช่างอีเลคโทรนิค 2", $DESCRIPTION);
					$POH_PL_NAME = "ช่างอีเลคทรอนิคส์ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างอีเลคโทรนิค 3") !== false) {
					$arr_temp = explode("ช่างอีเลคโทรนิค 3", $DESCRIPTION);
					$POH_PL_NAME = "ช่างอีเลคทรอนิคส์ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการเงินและบัญชี 3") !== false) {
					$arr_temp = explode("นักวิชาการเงินและบัญชี 3", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการเงินและบัญชี 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการเงินและบัญชี 4") !== false) {
					$arr_temp = explode("นักวิชาการเงินและบัญชี 4", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการเงินและบัญชี 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการเงินและบัญชี 5") !== false) {
					$arr_temp = explode("นักวิชาการเงินและบัญชี 5", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการเงินและบัญชี 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่บริหารงานการเงินและบัญชี 5") !== false) {
					$arr_temp = explode("เจ้าหน้าที่บริหารงานการเงินและบัญชี 5", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่บริหารงานการเงินและบัญชี 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่โสตทัศนศึกษา 1") !== false) {
					$arr_temp = explode("เจ้าหน้าที่โสตทัศนศึกษา 1", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่โสตทัศนศึกษา 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่โสตทัศนศึกษา 2") !== false) {
					$arr_temp = explode("เจ้าหน้าที่โสตทัศนศึกษา 2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่โสตทัศนศึกษา 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่โสตทัศนศึกษา 3") !== false) {
					$arr_temp = explode("เจ้าหน้าที่โสตทัศนศึกษา 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่โสตทัศนศึกษา 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานโสตทัศนศึกษา 4") !== false) {
					$arr_temp = explode("เจ้าพนักงานโสตทัศนศึกษา 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานโสตทัศนศึกษา 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานโสตทัศนศึกษา 5") !== false) {
					$arr_temp = explode("เจ้าพนักงานโสตทัศนศึกษา 5", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานโสตทัศนศึกษา 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ผู้ช่วยทันตแพทย์ 1") !== false) {
					$arr_temp = explode("ผู้ช่วยทันตแพทย์ 1", $DESCRIPTION);
					$POH_PL_NAME = "ผู้ช่วยทันตแพทย์ 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ผู้ช่วยทันตแพทย์ 2") !== false) {
					$arr_temp = explode("ผู้ช่วยทันตแพทย์ 2", $DESCRIPTION);
					$POH_PL_NAME = "ผู้ช่วยทันตแพทย์ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ผู้ช่วยทันตแพทย์ 3") !== false) {
					$arr_temp = explode("ผู้ช่วยทันตแพทย์ 3", $DESCRIPTION);
					$POH_PL_NAME = "ผู้ช่วยทันตแพทย์ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ผู้ช่วยทันตแพทย์ 4") !== false) {
					$arr_temp = explode("ผู้ช่วยทันตแพทย์ 4", $DESCRIPTION);
					$POH_PL_NAME = "ผู้ช่วยทันตแพทย์ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ผู้ช่วยทันตแพทย์ 5") !== false) {
					$arr_temp = explode("ผู้ช่วยทันตแพทย์ 5", $DESCRIPTION);
					$POH_PL_NAME = "ผู้ช่วยทันตแพทย์ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ทันตแพทย์ 4") !== false) {
					$arr_temp = explode("ทันตแพทย์ 4", $DESCRIPTION);
					$POH_PL_NAME = "ทันตแพทย์ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ทันตแพทย์ 5") !== false) {
					$arr_temp = explode("ทันตแพทย์ 5", $DESCRIPTION);
					$POH_PL_NAME = "ทันตแพทย์ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ทันตแพทย์ 6") !== false) {
					$arr_temp = explode("ทันตแพทย์ 6", $DESCRIPTION);
					$POH_PL_NAME = "ทันตแพทย์ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ทันตแพทย์ 7 วช.") !== false) {
					$arr_temp = explode("ทันตแพทย์ 7 วช.", $DESCRIPTION);
					$POH_PL_NAME = "ทันตแพทย์ 7 วช.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"(ทันตแพทย์ 7ว.)") !== false) {
					$arr_temp = explode("(ทันตแพทย์ 7ว.)", $DESCRIPTION);
					$POH_PL_NAME = "ทันตแพทย์ 7ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ทันตแพทย์ 7ว.") !== false) {
					$arr_temp = explode("ทันตแพทย์ 7ว.", $DESCRIPTION);
					$POH_PL_NAME = "ทันตแพทย์ 7ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"(ทันตแพทย์ 7)") !== false) {
					$arr_temp = explode("(ทันตแพทย์ 7)", $DESCRIPTION);
					$POH_PL_NAME = "ทันตแพทย์ 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ทันตแพทย์ 7") !== false) {
					$arr_temp = explode("ทันตแพทย์ 7", $DESCRIPTION);
					$POH_PL_NAME = "ทันตแพทย์ 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"(ทันตแพทย์ 8ว.)") !== false) {
					$arr_temp = explode("(ทันตแพทย์ 8ว.)", $DESCRIPTION);
					$POH_PL_NAME = "ทันตแพทย์ 8ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ทันตแพทย์ 8ว.") !== false) {
					$arr_temp = explode("ทันตแพทย์ 8ว.", $DESCRIPTION);
					$POH_PL_NAME = "ทันตแพทย์ 8ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ทันตแพทย์ 8 วช.") !== false) {
					$arr_temp = explode("ทันตแพทย์ 8 วช.", $DESCRIPTION);
					$POH_PL_NAME = "ทันตแพทย์ 8 วช.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ทันตแพทย์ 8 วช") !== false) {
					$arr_temp = explode("ทันตแพทย์ 8 วช", $DESCRIPTION);
					$POH_PL_NAME = "ทันตแพทย์ 8 วช";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ทันตแพทย์ 8วช.") !== false) {
					$arr_temp = explode("ทันตแพทย์ 8วช.", $DESCRIPTION);
					$POH_PL_NAME = "ทันตแพทย์ 8วช.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"(ทันตแพทย์ 8)") !== false) {
					$arr_temp = explode("(ทันตแพทย์ 8)", $DESCRIPTION);
					$POH_PL_NAME = "ทันตแพทย์ 8";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ทันตแพทย์ 9 วช.") !== false) {
					$arr_temp = explode("ทันตแพทย์ 9 วช.", $DESCRIPTION);
					$POH_PL_NAME = "ทันตแพทย์ 9 วช.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่รังสีการแพทย์ 2") !== false) {
					$arr_temp = explode("เจ้าหน้าที่รังสีการแพทย์ 2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่รังสีการแพทย์ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่รังสีการแพทย์ 3") !== false) {
					$arr_temp = explode("เจ้าหน้าที่รังสีการแพทย์ 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่รังสีการแพทย์ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่รังสีการแพทย์ 4") !== false) {
					$arr_temp = explode("เจ้าหน้าที่รังสีการแพทย์ 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่รังสีการแพทย์ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่รังสีการแพทย์ 5") !== false) {
					$arr_temp = explode("เจ้าหน้าที่รังสีการแพทย์ 5", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่รังสีการแพทย์ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่รังสีการแพทย์ 6") !== false) {
					$arr_temp = explode("เจ้าหน้าที่รังสีการแพทย์ 6", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่รังสีการแพทย์ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักรังสีการแพทย์ 3") !== false) {
					$arr_temp = explode("นักรังสีการแพทย์ 3", $DESCRIPTION);
					$POH_PL_NAME = "นักรังสีการแพทย์ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักรังสีการแพทย์ 4") !== false) {
					$arr_temp = explode("นักรังสีการแพทย์ 4", $DESCRIPTION);
					$POH_PL_NAME = "นักรังสีการแพทย์ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักรังสีการแพทย์ 5") !== false) {
					$arr_temp = explode("นักรังสีการแพทย์ 5", $DESCRIPTION);
					$POH_PL_NAME = "นักรังสีการแพทย์ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักรังสีการแพทย์ 6 ว.") !== false) {
					$arr_temp = explode("นักรังสีการแพทย์ 6 ว.", $DESCRIPTION);
					$POH_PL_NAME = "นักรังสีการแพทย์ 6 ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่รังสีเทคนิค 1") !== false) {
					$arr_temp = explode("เจ้าหน้าที่รังสีเทคนิค 1", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่รังสีเทคนิค 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่รังสีเทคนิค 2") !== false) {
					$arr_temp = explode("เจ้าหน้าที่รังสีเทคนิค 2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่รังสีเทคนิค 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่รังสีเทคนิค 3") !== false) {
					$arr_temp = explode("เจ้าหน้าที่รังสีเทคนิค 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่รังสีเทคนิค 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่รังสีเทคนิค 4") !== false) {
					$arr_temp = explode("เจ้าหน้าที่รังสีเทคนิค 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่รังสีเทคนิค 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่รังสีเทคนิค 5") !== false) {
					$arr_temp = explode("เจ้าหน้าที่รังสีเทคนิค 5", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่รังสีเทคนิค 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาลวิชาชีพชำนาญการ ด้านการพยาบาล ประเภทวิชาการ") !== false) {
					$arr_temp = explode("พยาบาลวิชาชีพชำนาญการ ด้านการพยาบาล ประเภทวิชาการ", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลวิชาชีพชำนาญการ ด้านการพยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาลเทคนิคชำนาญงาน ประเภททั่วไป") !== false) {
					$arr_temp = explode("พยาบาลเทคนิคชำนาญงาน ประเภททั่วไป", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลเทคนิคชำนาญงาน";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาลเทคนิคชำนาญงาน") !== false) {
					$arr_temp = explode("พยาบาลเทคนิคชำนาญงาน", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลเทคนิคชำนาญงาน";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักจัดการงานทั่วไปชำนาญการ ประเภทวิชาการ") !== false) {
					$arr_temp = explode("นักจัดการงานทั่วไปชำนาญการ ประเภทวิชาการ", $DESCRIPTION);
					$POH_PL_NAME = "นักจัดการงานทั่วไปชำนาญการ";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิเคราะห์นโยบายและแผนชำนาญการพิเศษ ด้านพัฒนาระบบบริหาร ประเภทวิชาการ ") !== false) {
					$arr_temp = explode("นักวิเคราะห์นโยบายและแผนชำนาญการพิเศษ ด้านพัฒนาระบบบริหาร ประเภทวิชาการ ", $DESCRIPTION);
					$POH_PL_NAME = "นักวิเคราะห์นโยบายและแผนชำนาญการพิเศษ ด้านพัฒนาระบบบริหาร";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาลวิชาชีพชำนาญการพิเศษ ด้านการพยาบาลผู้ป่วยหนัก ประเภทวิชาการ") !== false) {
					$arr_temp = explode("พยาบาลวิชาชีพชำนาญการพิเศษ ด้านการพยาบาลผู้ป่วยหนัก ประเภทวิชาการ", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลวิชาชีพชำนาญการพิเศษ ด้านการพยาบาลผู้ป่วยหนัก";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักประชาสัมพันธ์ 3") !== false) {
					$arr_temp = explode("นักประชาสัมพันธ์ 3", $DESCRIPTION);
					$POH_PL_NAME = "นักประชาสัมพันธ์ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักประชาสัมพันธ์ 4") !== false) {
					$arr_temp = explode("นักประชาสัมพันธ์ 4", $DESCRIPTION);
					$POH_PL_NAME = "นักประชาสัมพันธ์ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักประชาสัมพันธ์ 5") !== false) {
					$arr_temp = explode("นักประชาสัมพันธ์ 5", $DESCRIPTION);
					$POH_PL_NAME = "นักประชาสัมพันธ์ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักประชาสัมพันธ์ 6 ว.") !== false) {
					$arr_temp = explode("นักประชาสัมพันธ์ 6 ว.", $DESCRIPTION);
					$POH_PL_NAME = "นักประชาสัมพันธ์ 6 ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักประชาสัมพันธ์ 6") !== false) {
					$arr_temp = explode("นักประชาสัมพันธ์ 6", $DESCRIPTION);
					$POH_PL_NAME = "นักประชาสัมพันธ์ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักประชาสัมพันธ์ 7 ว") !== false) {
					$arr_temp = explode("นักประชาสัมพันธ์ 7 ว", $DESCRIPTION);
					$POH_PL_NAME = "นักประชาสัมพันธ์ 7 ว";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักประชาสัมพันธ์ 7") !== false) {
					$arr_temp = explode("นักประชาสัมพันธ์ 7", $DESCRIPTION);
					$POH_PL_NAME = "นักประชาสัมพันธ์ 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักประชาสัมพันธ์ชำนาญการ ประเภทวิชาการ") !== false) {
					$arr_temp = explode("นักประชาสัมพันธ์ชำนาญการ ประเภทวิชาการ", $DESCRIPTION);
					$POH_PL_NAME = "นักประชาสัมพันธ์ชำนาญการ";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการเผยแพร่ 3") !== false) {
					$arr_temp = explode("นักวิชาการเผยแพร่ 3", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการเผยแพร่ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการเผยแพร่ 4") !== false) {
					$arr_temp = explode("นักวิชาการเผยแพร่ 4", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการเผยแพร่ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการเผยแพร่ 5") !== false) {
					$arr_temp = explode("นักวิชาการเผยแพร่ 5", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการเผยแพร่ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการเผยแพร่ 6") !== false) {
					$arr_temp = explode("นักวิชาการเผยแพร่ 6", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการเผยแพร่ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการเผยแพร่ 7") !== false) {
					$arr_temp = explode("นักวิชาการเผยแพร่ 7", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการเผยแพร่ 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการสุขศึกษา 3") !== false) {
					$arr_temp = explode("นักวิชาการสุขศึกษา 3", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการสุขศึกษา 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการสุขศึกษา 4") !== false) {
					$arr_temp = explode("นักวิชาการสุขศึกษา 4", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการสุขศึกษา 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการสุขศึกษา 5") !== false) {
					$arr_temp = explode("นักวิชาการสุขศึกษา 5", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการสุขศึกษา 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการสุขศึกษา 6 ว.") !== false) {
					$arr_temp = explode("นักวิชาการสุขศึกษา 6 ว.", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการสุขศึกษา 6 ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการสุขศึกษา 6 ว") !== false) {
					$arr_temp = explode("นักวิชาการสุขศึกษา 6 ว", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการสุขศึกษา 6 ว";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการสุขศึกษา 6ว.") !== false) {
					$arr_temp = explode("นักวิชาการสุขศึกษา 6ว.", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการสุขศึกษา 6ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการสุขศึกษา 7 ว.") !== false) {
					$arr_temp = explode("นักวิชาการสุขศึกษา 7 ว.", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการสุขศึกษา 7 ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการสุขศึกษา 7 ว") !== false) {
					$arr_temp = explode("นักวิชาการสุขศึกษา 7 ว", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการสุขศึกษา 7 ว";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พยาบาลวิชาชีพชำนาญการพิเศษ ด้านการพยาบาล ประเภทวิชาการ") !== false) {
					$arr_temp = explode("พยาบาลวิชาชีพชำนาญการพิเศษ ด้านการพยาบาล ประเภทวิชาการ", $DESCRIPTION);
					$POH_PL_NAME = "พยาบาลวิชาชีพชำนาญการพิเศษ ด้านการพยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายแพทย์เชี่ยวชาญ ด้านเวชกรรม สาขาจักษุประสาทวิทยา ประเภทวิชาการ") !== false) {
					$arr_temp = explode("นายแพทย์เชี่ยวชาญ ด้านเวชกรรม สาขาจักษุประสาทวิทยา ประเภทวิชาการ", $DESCRIPTION);
					$POH_PL_NAME = "นายแพทย์เชี่ยวชาญ ด้านเวชกรรม สาขาจักษุประสาทวิทยา";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"บรรณารักษ์ 3") !== false) {
					$arr_temp = explode("บรรณารักษ์ 3", $DESCRIPTION);
					$POH_PL_NAME = "บรรณารักษ์ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"บรรณารักษ์ 4") !== false) {
					$arr_temp = explode("บรรณารักษ์ 4", $DESCRIPTION);
					$POH_PL_NAME = "บรรณารักษ์ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"บรรณารักษ์ 5") !== false) {
					$arr_temp = explode("บรรณารักษ์ 5", $DESCRIPTION);
					$POH_PL_NAME = "บรรณารักษ์ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"บรรณารักษ์ 6") !== false) {
					$arr_temp = explode("บรรณารักษ์ 6", $DESCRIPTION);
					$POH_PL_NAME = "บรรณารักษ์ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"บุคลากร 3") !== false) {
					$arr_temp = explode("บุคลากร 3", $DESCRIPTION);
					$POH_PL_NAME = "บุคลากร 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"บุคลากร 4") !== false) {
					$arr_temp = explode("บุคลากร 4", $DESCRIPTION);
					$POH_PL_NAME = "บุคลากร 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"บุคลากร 5") !== false) {
					$arr_temp = explode("บุคลากร 5", $DESCRIPTION);
					$POH_PL_NAME = "บุคลากร 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"บุคลากร 6 ว.") !== false) {
					$arr_temp = explode("บุคลากร 6 ว.", $DESCRIPTION);
					$POH_PL_NAME = "บุคลากร 6 ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"บุคลากร 6 ว") !== false) {
					$arr_temp = explode("บุคลากร 6 ว", $DESCRIPTION);
					$POH_PL_NAME = "บุคลากร 6 ว";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"บุคลากร 6") !== false) {
					$arr_temp = explode("บุคลากร 6", $DESCRIPTION);
					$POH_PL_NAME = "บุคลากร 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"บุคลากร 7ว") !== false) {
					$arr_temp = explode("บุคลากร 7ว", $DESCRIPTION);
					$POH_PL_NAME = "บุคลากร 7ว";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"บุคลากร 7 ว.") !== false) {
					$arr_temp = explode("บุคลากร 7 ว.", $DESCRIPTION);
					$POH_PL_NAME = "บุคลากร 7 ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"บุคลากร 7 ว") !== false) {
					$arr_temp = explode("บุคลากร 7 ว", $DESCRIPTION);
					$POH_PL_NAME = "บุคลากร 7 ว";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"บุคลากร 7") !== false) {
					$arr_temp = explode("บุคลากร 7", $DESCRIPTION);
					$POH_PL_NAME = "บุคลากร 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"(บุคลากร 8)") !== false) {
					$arr_temp = explode("(บุคลากร 8)", $DESCRIPTION);
					$POH_PL_NAME = "บุคลากร 8";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"บุคลากร 8 ว") !== false) {
					$arr_temp = explode("บุคลากร 8 ว", $DESCRIPTION);
					$POH_PL_NAME = "บุคลากร 8 ว";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายช่างศิลป์ 2") !== false) {
					$arr_temp = explode("นายช่างศิลป์ 2", $DESCRIPTION);
					$POH_PL_NAME = "นายช่างศิลป์ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายช่างศิลป์ 3") !== false) {
					$arr_temp = explode("นายช่างศิลป์ 3", $DESCRIPTION);
					$POH_PL_NAME = "นายช่างศิลป์ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายช่างศิลป์ 4") !== false) {
					$arr_temp = explode("นายช่างศิลป์ 4", $DESCRIPTION);
					$POH_PL_NAME = "นายช่างศิลป์ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายช่างศิลป์ 5") !== false) {
					$arr_temp = explode("นายช่างศิลป์ 5", $DESCRIPTION);
					$POH_PL_NAME = "นายช่างศิลป์ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายช่ายศิลป์ 5") !== false) {
					$arr_temp = explode("นายช่ายศิลป์ 5", $DESCRIPTION);
					$POH_PL_NAME = "นายช่างศิลป์ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นายช่างศิลป์ 6") !== false) {
					$arr_temp = explode("นายช่างศิลป์ 6", $DESCRIPTION);
					$POH_PL_NAME = "นายช่างศิลป์ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพนักงานเภสัชกรรมชำนาญงาน ประเภททั่วไป") !== false) {
					$arr_temp = explode("เจ้าพนักงานเภสัชกรรมชำนาญงาน ประเภททั่วไป", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพนักงานเภสัชกรรมชำนาญงาน";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เภสัชกร 3") !== false) {
					$arr_temp = explode("เภสัชกร 3", $DESCRIPTION);
					$POH_PL_NAME = "เภสัชกร 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เภสัชกร 4") !== false) {
					$arr_temp = explode("เภสัชกร 4", $DESCRIPTION);
					$POH_PL_NAME = "เภสัชกร 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เภสัชกร 5") !== false) {
					$arr_temp = explode("เภสัชกร 5", $DESCRIPTION);
					$POH_PL_NAME = "เภสัชกร 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เภสัชกร 6ว.") !== false) {
					$arr_temp = explode("เภสัชกร 6ว.", $DESCRIPTION);
					$POH_PL_NAME = "เภสัชกร 6ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เภสัชกร 6 ว.") !== false) {
					$arr_temp = explode("เภสัชกร 6 ว.", $DESCRIPTION);
					$POH_PL_NAME = "เภสัชกร 6 ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เภสัชกร 7 วช.") !== false) {
					$arr_temp = explode("เภสัชกร 7 วช.", $DESCRIPTION);
					$POH_PL_NAME = "เภสัชกร 7 วช.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการอาหารและยา 3") !== false) {
					$arr_temp = explode("นักวิชาการอาหารและยา 3", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการอาหารและยา 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการอาหารและยา 4") !== false) {
					$arr_temp = explode("นักวิชาการอาหารและยา 4", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการอาหารและยา 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการอาหารและยา 5") !== false) {
					$arr_temp = explode("นักวิชาการอาหารและยา 5", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการอาหารและยา 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการอาหารและยา 6") !== false) {
					$arr_temp = explode("นักวิชาการอาหารและยา 6", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการอาหารและยา 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พนักงายวิทยาศาสตร์จัตวา") !== false) {
					$arr_temp = explode("พนักงายวิทยาศาสตร์จัตวา", $DESCRIPTION);
					$POH_PL_NAME = "พนักงานวิทยาศาสตร์จัตวา";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"พนักงานวิทยาศาสตร์จัตวา") !== false) {
					$arr_temp = explode("พนักงานวิทยาศาสตร์จัตวา", $DESCRIPTION);
					$POH_PL_NAME = "พนักงานวิทยาศาสตร์จัตวา";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักสถิติ 3") !== false) {
					$arr_temp = explode("นักสถิติ 3", $DESCRIPTION);
					$POH_PL_NAME = "นักสถิติ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักสถิติ 4") !== false) {
					$arr_temp = explode("นักสถิติ 4", $DESCRIPTION);
					$POH_PL_NAME = "นักสถิติ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักสถิติ 5") !== false) {
					$arr_temp = explode("นักสถิติ 5", $DESCRIPTION);
					$POH_PL_NAME = "นักสถิติ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักสถิติ 6") !== false) {
					$arr_temp = explode("นักสถิติ 6", $DESCRIPTION);
					$POH_PL_NAME = "นักสถิติ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการศึกษาพิเศษ 4") !== false) {
					$arr_temp = explode("นักวิชาการศึกษาพิเศษ 4", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการศึกษาพิเศษ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการศึกษาพิเศษ 5") !== false) {
					$arr_temp = explode("นักวิชาการศึกษาพิเศษ 5", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการศึกษาพิเศษ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการศึกษาพิเศษ 6ว.") !== false) {
					$arr_temp = explode("นักวิชาการศึกษาพิเศษ 6ว.", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการศึกษาพิเศษ 6ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"นักวิชาการศึกษาพิเศษ 7") !== false) {
					$arr_temp = explode("นักวิชาการศึกษาพิเศษ 7", $DESCRIPTION);
					$POH_PL_NAME = "นักวิชาการศึกษาพิเศษ 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่ประชาสัมพันธ์ 1") !== false) {
					$arr_temp = explode("เจ้าหน้าที่ประชาสัมพันธ์ 1", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่ประชาสัมพันธ์ 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่ประชาสัมพันธ์ 2") !== false) {
					$arr_temp = explode("เจ้าหน้าที่ประชาสัมพันธ์ 2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่ประชาสัมพันธ์ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่ประชาสัมพันธ์ 3") !== false) {
					$arr_temp = explode("เจ้าหน้าที่ประชาสัมพันธ์ 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่ประชาสัมพันธ์ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่ประชาสัมพันธ์ 4") !== false) {
					$arr_temp = explode("เจ้าหน้าที่ประชาสัมพันธ์ 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่ประชาสัมพันธ์ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่ประชาสัมพันธ์ 5") !== false) {
					$arr_temp = explode("เจ้าหน้าที่ประชาสัมพันธ์ 5", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่ประชาสัมพันธ์ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าพน้าที่วิทยาศาสตร์การแพทย์ 2") !== false) {
					$arr_temp = explode("เจ้าพน้าที่วิทยาศาสตร์การแพทย์ 2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าพน้าที่วิทยาศาสตร์การแพทย์ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่บันทึกข้อมูล 1") !== false) {
					$arr_temp = explode("เจ้าหน้าที่บันทึกข้อมูล 1", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่บันทึกข้อมูล 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าทีบันทึกข้อมูล 2") !== false) {
					$arr_temp = explode("เจ้าหน้าทีบันทึกข้อมูล 2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าทีบันทึกข้อมูล 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่บันทึกข้อมูล 3") !== false) {
					$arr_temp = explode("เจ้าหน้าที่บันทึกข้อมูล 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่บันทึกข้อมูล 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่บันทึกข้อมูล 4") !== false) {
					$arr_temp = explode("เจ้าหน้าที่บันทึกข้อมูล 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่บันทึกข้อมูล 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่บันทึกข้อมูล 5") !== false) {
					$arr_temp = explode("เจ้าหน้าที่บันทึกข้อมูล 5", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่บันทึกข้อมูล 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่ฝึกอบรม 3") !== false) {
					$arr_temp = explode("เจ้าหน้าที่ฝึกอบรม 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่ฝึกอบรม 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่ฝึกอบรม 4") !== false) {
					$arr_temp = explode("เจ้าหน้าที่ฝึกอบรม 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่ฝึกอบรม 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่ฝึกอบรม 5") !== false) {
					$arr_temp = explode("เจ้าหน้าที่ฝึกอบรม 5", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่ฝึกอบรม 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่ฝึกอบรม 6 ว") !== false) {
					$arr_temp = explode("เจ้าหน้าที่ฝึกอบรม 6 ว", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่ฝึกอบรม 6 ว";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่ฝีกอบรม 6") !== false) {
					$arr_temp = explode("เจ้าหน้าที่ฝีกอบรม 6", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่ฝีกอบรม 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่วิเคราะห์นโยบายและแผน 3") !== false) {
					$arr_temp = explode("เจ้าหน้าที่วิเคราะห์นโยบายและแผน 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่วิเคราะห์นโยบายและแผน 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่วิเคราะห์นโยบายและแผน 4") !== false) {
					$arr_temp = explode("เจ้าหน้าที่วิเคราะห์นโยบายและแผน 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่วิเคราะห์นโยบายและแผน 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่วิเคราะห์นโยบายและแผน 5") !== false) {
					$arr_temp = explode("เจ้าหน้าที่วิเคราะห์นโยบายและแผน 5", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่วิเคราะห์นโยบายและแผน 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่วิเคราะห์นโยบายและแผน 6 ว.") !== false) {
					$arr_temp = explode("เจ้าหน้าที่วิเคราะห์นโยบายและแผน 6 ว.", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่วิเคราะห์นโยบายและแผน 6 ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่วิเคราะห์นโยบายและแผน 6") !== false) {
					$arr_temp = explode("เจ้าหน้าที่วิเคราะห์นโยบายและแผน 6", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่วิเคราะห์นโยบายและแผน 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่วิเคราะห์นโยบายและแผน 7 ว.") !== false) {
					$arr_temp = explode("เจ้าหน้าที่วิเคราะห์นโยบายและแผน 7 ว.", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่วิเคราะห์นโยบายและแผน 7 ว.";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่วิเคราะห์นโยบายและแผน 7 ว") !== false) {
					$arr_temp = explode("เจ้าหน้าที่วิเคราะห์นโยบายและแผน 7 ว", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่วิเคราะห์นโยบายและแผน 7 ว";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่วิเคราะห์นโยบายและแผน 7") !== false) {
					$arr_temp = explode("เจ้าหน้าที่วิเคราะห์นโยบายและแผน 7", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่วิเคราะห์นโยบายและแผน 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่วิเคราะห์นโยบายและแผน 8 ว") !== false) {
					$arr_temp = explode("เจ้าหน้าที่วิเคราะห์นโยบายและแผน 8 ว", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่วิเคราะห์นโยบายและแผน 8 ว";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พัสดุ 1") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พัสดุ 1", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พัสดุ 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พัสดุ 2") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พัสดุ 2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พัสดุ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พัสดุ 3") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พัสดุ 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พัสดุ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พัสดุ 4") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พัสดุ 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พัสดุ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างโลหะ 1") !== false) {
					$arr_temp = explode("ช่างโลหะ 1", $DESCRIPTION);
					$POH_PL_NAME = "ช่างโลหะ 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างโลหะ 2") !== false) {
					$arr_temp = explode("ช่างโลหะ 2", $DESCRIPTION);
					$POH_PL_NAME = "ช่างโลหะ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างโลหะ 3") !== false) {
					$arr_temp = explode("ช่างโลหะ 3", $DESCRIPTION);
					$POH_PL_NAME = "ช่างโลหะ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างเครื่องยนต์ 1") !== false) {
					$arr_temp = explode("ช่างเครื่องยนต์ 1", $DESCRIPTION);
					$POH_PL_NAME = "ช่างเครื่องยนต์ 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างเครื่องยนต์ 2") !== false) {
					$arr_temp = explode("ช่างเครื่องยนต์ 2", $DESCRIPTION);
					$POH_PL_NAME = "ช่างเครื่องยนต์ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างเครื่องยนต์ 3") !== false) {
					$arr_temp = explode("ช่างเครื่องยนต์ 3", $DESCRIPTION);
					$POH_PL_NAME = "ช่างเครื่องยนต์ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างเทคนิค 1") !== false) {
					$arr_temp = explode("ช่างเทคนิค 1", $DESCRIPTION);
					$POH_PL_NAME = "ช่างเทคนิค 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างเทคนิค 2") !== false) {
					$arr_temp = explode("ช่างเทคนิค 2", $DESCRIPTION);
					$POH_PL_NAME = "ช่างเทคนิค 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างเทคนิค 3") !== false) {
					$arr_temp = explode("ช่างเทคนิค 3", $DESCRIPTION);
					$POH_PL_NAME = "ช่างเทคนิค 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างกายอุปกรณ์ 1") !== false) {
					$arr_temp = explode("ช่างกายอุปกรณ์ 1", $DESCRIPTION);
					$POH_PL_NAME = "ช่างกายอุปกรณ์ 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างกายอุปกรณ์ 2") !== false) {
					$arr_temp = explode("ช่างกายอุปกรณ์ 2", $DESCRIPTION);
					$POH_PL_NAME = "ช่างกายอุปกรณ์ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างกายอุปกรณ์ 3") !== false) {
					$arr_temp = explode("ช่างกายอุปกรณ์ 3", $DESCRIPTION);
					$POH_PL_NAME = "ช่างกายอุปกรณ์ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างกายอุปกรณ์ 4") !== false) {
					$arr_temp = explode("ช่างกายอุปกรณ์ 4", $DESCRIPTION);
					$POH_PL_NAME = "ช่างกายอุปกรณ์ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างกายอุปกรณ์ 5") !== false) {
					$arr_temp = explode("ช่างกายอุปกรณ์ 5", $DESCRIPTION);
					$POH_PL_NAME = "ช่างกายอุปกรณ์ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"ช่างกายอุปกรณ์ 6") !== false) {
					$arr_temp = explode("ช่างกายอุปกรณ์ 6", $DESCRIPTION);
					$POH_PL_NAME = "ช่างกายอุปกรณ์ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่เอ็กซเรย์ 1") !== false) {
					$arr_temp = explode("เจ้าหน้าที่เอ็กซเรย์ 1", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่เอ็กซเรย์ 1";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่เอ็กซเรย์ 2") !== false) {
					$arr_temp = explode("เจ้าหน้าที่เอ็กซเรย์ 2", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่เอ็กซเรย์ 2";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่เอ็กซเรย์ 3") !== false) {
					$arr_temp = explode("เจ้าหน้าที่เอ็กซเรย์ 3", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่เอ็กซเรย์ 3";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่บริหารงานพัสดุ 4") !== false) {
					$arr_temp = explode("เจ้าหน้าที่บริหารงานพัสดุ 4", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่บริหารงานพัสดุ 4";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่บริหารงานพัสดุ 5") !== false) {
					$arr_temp = explode("เจ้าหน้าที่บริหารงานพัสดุ 5", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่บริหารงานพัสดุ 5";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่บริหารงานพัสดุ 6") !== false) {
					$arr_temp = explode("เจ้าหน้าที่บริหารงานพัสดุ 6", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่บริหารงานพัสดุ 6";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่บริหารงานพัสดุ 7") !== false) {
					$arr_temp = explode("เจ้าหน้าที่บริหารงานพัสดุ 7", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่บริหารงานพัสดุ 7";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} elseif (strpos($DESCRIPTION,"เจ้าหน้าที่พยาบาล") !== false) {
					$arr_temp = explode("เจ้าหน้าที่พยาบาล", $DESCRIPTION);
					$POH_PL_NAME = "เจ้าหน้าที่พยาบาล";
					$POH_ORG = trim($arr_temp[1]);
				} else {
//					echo "$DESCRIPTION<br>";
				}
			}

			if (strpos($POH_ORG,"กองการเจ้าหน้าที่") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"รพ.สงฆ์") !== false || strpos($POH_ORG,"โรงพยาบาลสงฆ์") !== false) {
				$POH_ORG3 = "โรงพยาบาลสงฆ์";
			} elseif (strpos($POH_ORG,"รพ.ขอนแก่น") !== false) {
				$POH_ORG3 = "รพ.ขอนแก่น สสจ.ขอนแก่น";
			} elseif (strpos($POH_ORG,"รพ.นครปฐม") !== false) {
				$POH_ORG3 = "รพ.นครปฐม";
			} elseif (strpos($POH_ORG,"รพ.มหาราชนครราชสีมา") !== false) {
				$POH_ORG3 = "รพ.มหาราชนครราชสีมา";
			} elseif (strpos($POH_ORG,"รพ.เลิดสิน") !== false || strpos($POH_ORG,"โรงพยาบาลเลิดสิน") !== false) {
				$POH_ORG3 = "โรงพยาบาลเลิดสิน";
			} elseif (strpos($POH_ORG,"รพ.ราชวิถี") !== false || strpos($POH_ORG,"โรงพยาบาลราชวิถี") !== false || strpos($POH_ORG,"ร.พ.ราชวิถี") !== false || strpos($POH_ORG,"รพ. ราชวิถี") !== false || strpos($POH_ORG,"รพ.ราชวิถร") !== false) {
				$POH_ORG3 = "โรงพยาบาลราชวิถี";
			} elseif (strpos($POH_ORG,"กองระบาดวิทยา") !== false) {
				$POH_ORG3 = "กองระบาดวิทยา";
			} elseif (strpos($POH_ORG,"รพ.นครราชสีมา") !== false) {
				$POH_ORG3 = "รพ.นครราชสีมา";
			} elseif (strpos($POH_ORG,"รพ.เพชรบูรณ์") !== false || strpos($POH_ORG,"รพ. เพชรบูรณ์") !== false) {
				$POH_ORG3 = "รพ.เพชรบูรณ์";
			} elseif (strpos($POH_ORG,"รพ.โรคทรวงอก") !== false || strpos($POH_ORG,"โรงพยาบาลโรคทรวงอก") !== false) {
				$POH_ORG3 = "โรงพยาบาลโรคทรวงอก";
			} elseif (strpos($POH_ORG,"สถาบันสุขภาพเด็กแห่งชาติมหาราชินี") !== false) {
				$POH_ORG3 = "สถาบันสุขภาพเด็กแห่งชาติมหาราชินี";
			} elseif (strpos($POH_ORG,"สถาบันประสาทวิทยา") !== false) {
				$POH_ORG3 = "สถาบันประสาทวิทยา";
			} elseif (strpos($POH_ORG,"โรงพยาบาลประสาทเชียงใหม่") !== false || strpos($POH_ORG,"รพ.ประสาทเชียงใหม่") !== false) {
				$POH_ORG3 = "โรงพยาบาลประสาทเชียงใหม่";
			} elseif (strpos($POH_ORG,"ศูนย์โรคผิวหนังเขตร้อนภาคใต้ จังหวัดตรัง") !== false) {
				$POH_ORG3 = "ศูนย์โรคผิวหนังเขตร้อนภาคใต้ จังหวัดตรัง";
			} elseif (strpos($POH_ORG,"สถาบันสุขภาพเด็กฯ") !== false) {
				$POH_ORG3 = "สถาบันสุขภาพเด็กฯ";
			} elseif (strpos($POH_ORG,"รพ.เมตตาฯ") !== false || strpos($POH_ORG,"รพ.เมตตาประชารักษ์") !== false || strpos($POH_ORG,"รพ.เมตตาประชา- รักษ์") !== false || strpos($POH_ORG,"โรงพยาบาลเมตตาประชารักษ์") !== false) {
				$POH_ORG3 = "โรงพยาบาลเมตตาประชารักษ์ (วัดไร่ขิง)";
			} elseif (strpos($POH_ORG,"ร.พ.ลำปาง") !== false) {
				$POH_ORG3 = "ร.พ.ลำปาง";
			} elseif (strpos($POH_ORG,"รพ.สรรพสิทธิ์ประสงค์ สสจ.อุบลราชธานี") !== false || strpos($POH_ORG,"รพ.สรรพสิทธิประสงค์ อุบลราชธานี") !== false) {
				$POH_ORG3 = "รพ.สรรพสิทธิ์ประสงค์ สสจ.อุบลราชธานี";
			} elseif (strpos($POH_ORG,"รพ.สวรรค์ประชารักษ์ สสจ.นครสวรรค์") !== false || strpos($POH_ORG,"โรงพยาบาลสวรรค์ประชารักษ์") !== false) {
				$POH_ORG3 = "รพ.สวรรค์ประชารักษ์ สสจ.นครสวรรค์";
			} elseif (strpos($POH_ORG,"รพ.พะเยา สสจ.พะเยา") !== false || strpos($POH_ORG,"รพ.พะเยา สำนักงานสาธารณสุขจังหวัดพะเยา") !== false || strpos($POH_ORG,"รพ. พะเยา") !== false) {
				$POH_ORG3 = "รพ.พะเยา สสจ.พะเยา";
			} elseif (strpos($POH_ORG,"รพ.บ้านบือ สสจ.อุดรธานี") !== false) {
				$POH_ORG3 = "รพ.บ้านบือ สสจ.อุดรธานี";
			} elseif (strpos($POH_ORG,"รพ.พระปกเกล้า จันทบุรี") !== false || strpos($POH_ORG,"รพ. พระปกเกล้า จันทบุรี") !== false) {
				$POH_ORG3 = "รพ.พระปกเกล้า จันทบุรี สสจ.จันทบุรี";
			} elseif (strpos($POH_ORG,"ศูนย์บริการสาธารณสุข 23 สี่พระยา สำนักอนามัย") !== false) {
				$POH_ORG3 = "ศูนย์บริการสาธารณสุข 23 สี่พระยา สำนักอนามัย";
			} elseif (strpos($POH_ORG,"ร.พ.ร้อยเอ็ด สำนักงานสาธารณสุขจังหวัดร้อยเอ็ด") !== false) {
				$POH_ORG3 = "ร.พ.ร้อยเอ็ด สำนักงานสาธารณสุขจังหวัดร้อยเอ็ด";
			} elseif (strpos($POH_ORG,"รพ.โนนสัง สสจ.หนองบัวลำภู") !== false) {
				$POH_ORG3 = "รพ.โนนสัง สสจ.หนองบัวลำภู";
			} elseif (strpos($POH_ORG,"รพ.นากลาง สสจ.หนองบัวลำภู") !== false) {
				$POH_ORG3 = "รพ.นากลาง สสจ.หนองบัวลำภู";
			} elseif (strpos($POH_ORG,"รพ.คลองหาด สสจ.สระแก้ว") !== false) {
				$POH_ORG3 = "รพ.คลองหาด สสจ.สระแก้ว";
			} elseif (strpos($POH_ORG,"รพ.สมุทรสาคร สสจ.สมุทรสาคร") !== false) {
				$POH_ORG3 = "รพ.สมุทรสาคร สสจ.สมุทรสาคร";
			} elseif (strpos($POH_ORG,"ร.พ.พระจอมเกล้าจ.เพชรบุรี สสจ.เพชรบุรี") !== false) {
				$POH_ORG3 = "ร.พ.พระจอมเกล้าจ.เพชรบุรี สสจ.เพชรบุรี";
			} elseif (strpos($POH_ORG,"รพ.นพรัตนราชธานี") !== false || strpos($POH_ORG,"โรงพยาบาลนพรัตนราชธานี") !== false || strpos($POH_ORG,"รพ.นพรัตนฯ") !== false) {
				$POH_ORG3 = "โรงพยาบาลนพรัตนราชธานี";
			} elseif (strpos($POH_ORG,"โรงพยาบาลเด็ก") !== false || strpos($POH_ORG,"รพ.เด็ก") !== false) {
				$POH_ORG3 = "โรงพยาบาลเด็ก";
			} elseif (strpos($POH_ORG,"ร.พ.ปทุมรัตน์") !== false) {
				$POH_ORG3 = "ร.พ.ปทุมรัตน์";
			} elseif (strpos($POH_ORG,"รพ.บ้านหมี่ สสจ.ลพบุรี") !== false) {
				$POH_ORG3 = "รพ.บ้านหมี่ สสจ.ลพบุรี";
			} elseif (strpos($POH_ORG,"โรงพยาบาลมหาราชนครศรีธรรมราช") !== false) {
				$POH_ORG3 = "โรงพยาบาลมหาราชนครศรีธรรมราช";
			} elseif (strpos($POH_ORG,"รพ.ยโสธร") !== false) {
				$POH_ORG3 = "รพ.ยโสธร";
			} elseif (strpos($POH_ORG,"รพ.พิจิตร") !== false) {
				$POH_ORG3 = "รพ.พิจิตร";
			} elseif (strpos($POH_ORG,"ร.พ.ตะกั่วป่า สสจ.พังงา") !== false) {
				$POH_ORG3 = "ร.พ.ตะกั่วป่า สสจ.พังงา";
			} elseif (strpos($POH_ORG,"รพ.นราธิวาส") !== false) {
				$POH_ORG3 = "รพ.นราธิวาส";
			} elseif (strpos($POH_ORG,"รพ.บ้านไผ่ สสจ.ขอนแก่น") !== false || strpos($POH_ORG,"ร.พ.บ้านไผ่ สสจ.ขอนแก่น") !== false) {
				$POH_ORG3 = "รพ.บ้านไผ่ สสจ.ขอนแก่น";
			} elseif (strpos($POH_ORG,"รพ.ลำปาง สสจ.ลำปาง") !== false) {
				$POH_ORG3 = "รพ.ลำปาง สสจ.ลำปาง";
			} elseif (strpos($POH_ORG,"รพ.โพธาราม สำนักงานสาธารณสุขจังหวัดราชบุรี") !== false) {
				$POH_ORG3 = "รพ.โพธาราม สำนักงานสาธารณสุขจังหวัดราชบุรี";
			} elseif (strpos($POH_ORG,"รพ. แม่สอด สสจ. ตาก") !== false) {
				$POH_ORG3 = "รพ. แม่สอด สสจ. ตาก";
			} elseif (strpos($POH_ORG,"ร.พ.สระบุรี") !== false || strpos($POH_ORG,"รพ. สระบุรี") !== false) {
				$POH_ORG3 = "ร.พ.สระบุรี";
			} elseif (strpos($POH_ORG,"ร.พ.ปัตตานี สสจ.ปัตตานี") !== false) {
				$POH_ORG3 = "ร.พ.ปัตตานี สสจ.ปัตตานี";
			} elseif (strpos($POH_ORG,"รพ.เบตง สสจ.ยะลา") !== false) {
				$POH_ORG3 = "รพ.เบตง สสจ.ยะลา";
			} elseif (strpos($POH_ORG,"สถาบันมะเร็งแห่งชาติ") !== false) {
				$POH_ORG3 = "สถาบันมะเร็งแห่งชาติ";
			} elseif (strpos($POH_ORG,"รพ.โรคติดต่อภาคตะวันออกเฉียงเหนือ จ.ขอนแก่น") !== false) {
				$POH_ORG3 = "รพ.โรคติดต่อภาคตะวันออกเฉียงเหนือ จ.ขอนแก่น";
			} elseif (strpos($POH_ORG,"สถาบันโรคผิวหนัง") !== false) {
				$POH_ORG3 = "สถาบันโรคผิวหนัง";
			} elseif (strpos($POH_ORG,"รพ.สระบุรี") !== false) {
				$POH_ORG3 = "รพ.สระบุรี";
			} elseif (strpos($POH_ORG,"รพ.สีคิ้ว สสจ.นครราชสีมา") !== false) {
				$POH_ORG3 = "รพ.สีคิ้ว สสจ.นครราชสีมา";
			} elseif (strpos($POH_ORG,"ศูนย์สุขภาพจิตชุมชนชัยนาท") !== false) {
				$POH_ORG3 = "ศูนย์สุขภาพจิตชุมชนชัยนาท";
			} elseif (strpos($POH_ORG,"กองโรงพยาบาลภูมิภาค") !== false) {
				$POH_ORG3 = "กองโรงพยาบาลภูมิภาค";
			} elseif (strpos($POH_ORG,"โรงพยาบาลบำราศนราดูร") !== false) {
				$POH_ORG3 = "โรงพยาบาลบำราศนราดูร";
			} elseif (strpos($POH_ORG,"รพ.อ่างทอง") !== false) {
				$POH_ORG3 = "รพ.อ่างทอง สสจ.อ่างทอง";
			} elseif (strpos($POH_ORG,"รพ.ชลบุรี") !== false) {
				$POH_ORG3 = "รพ.ชลบุรี";
			} elseif (strpos($POH_ORG,"โรงพยาบาลมหาราช-นครศรีธรรมราช สสจ.นครศรีธรรมราช") !== false || strpos($POH_ORG,"โรงพยาบาลมหาราชนครศรีธรรมราช สสจ.นครศรีธรรมราช") !== false) {
				$POH_ORG3 = "โรงพยาบาลมหาราชนครศรีธรรมราช สสจ.นครศรีธรรมราช";
			} elseif (strpos($POH_ORG,"รพ.อุดรธานี") !== false) {
				$POH_ORG3 = "รพ.อุดรธานี";
			} elseif (strpos($POH_ORG,"รพ.โกสุมพิสัย สสจ.มหาสารคาม") !== false) {
				$POH_ORG3 = "รพ.โกสุมพิสัย สสจ.มหาสารคาม";
			} elseif (strpos($POH_ORG,"ศูนย์ป้องกันและควบคุมโรคมะเร็งจ.อุดรธานี สถาบันมะเร็งฯ") !== false) {
				$POH_ORG3 = "ศูนย์ป้องกันและควบคุมโรคมะเร็งจ.อุดรธานี สถาบันมะเร็งฯ";
			} elseif (strpos($POH_ORG,"รพ.พุทธชินราช พิษณุโลก สสจ.พิษณุโลก") !== false || strpos($POH_ORG,"รพ.พุทธชินราช สสจ.พิษณุโลก") !== false) {
				$POH_ORG3 = "รพ.พุทธชินราช พิษณุโลก สสจ.พิษณุโลก";
			} elseif (strpos($POH_ORG,"รพ.เมืองฉะเชิงเทรา") !== false) {
				$POH_ORG3 = "รพ.เมืองฉะเชิงเทรา สสจ.ฉะเชิงเทรา";
			} elseif (strpos($POH_ORG,"กองสาธารณสุขภูมิภาค") !== false) {
				$POH_ORG3 = "กองสาธารณสุขภูมิภาค";
			} elseif (strpos($POH_ORG,"รพ.เชียงรายประชานุเคราะห์ สสจ.เชียงใหม่") !== false) {
				$POH_ORG3 = "รพ.เชียงรายประชานุเคราะห์ สสจ.เชียงใหม่";
			} elseif (strpos($POH_ORG,"รพ.กะบี่") !== false) {
				$POH_ORG3 = "รพ.กระบี่";
			} elseif (strpos($POH_ORG,"รพ.หนองแซง สสจ.สระบุรี") !== false) {
				$POH_ORG3 = "รพ.หนองแซง สสจ.สระบุรี";
			} elseif (strpos($POH_ORG,"รพ.จตุรพักตรพิมาน จ.ร้อยเอ็ด") !== false) {
				$POH_ORG3 = "รพ.จตุรพักตรพิมาน จ.ร้อยเอ็ด";
			} elseif (strpos($POH_ORG,"รพ.สมเด็จเจ้าพระยา") !== false) {
				$POH_ORG3 = "รพ.สมเด็จเจ้าพระยา";
			} elseif (strpos($POH_ORG,"สำนักงานเลขานุการกรม") !== false) {
				$POH_ORG3 = "สำนักงานเลขานุการกรม";
			} elseif (strpos($POH_ORG,"รพ.ลพบุรี จ.ลพบุรี") !== false) {
				$POH_ORG3 = "รพ.ลพบุรี จ.ลพบุรี";
			} elseif (strpos($POH_ORG,"รพ.สกลนคร") !== false) {
				$POH_ORG3 = "รพ.สกลนคร";
			} elseif (strpos($POH_ORG,"รพ.หญิง") !== false) {
				$POH_ORG3 = "รพ.หญิง";
			} elseif (strpos($POH_ORG,"โรงพยาบาลธัญญารักษ์") !== false) {
				$POH_ORG3 = "โรงพยาบาลธัญญารักษ์";
			} elseif (strpos($POH_ORG,"สถาบันยาเสพติดธัญญารักษ์") !== false) {
				$POH_ORG3 = "สถาบันยาเสพติดธัญญารักษ์";
			} elseif (strpos($POH_ORG,"สถาบันธัญญารักษ์") !== false) {
				$POH_ORG3 = "สถาบันธัญญารักษ์";
			} elseif (strpos($POH_ORG,"รพ. กาฬสินธุ์") !== false) {
				$POH_ORG3 = "รพ. กาฬสินธุ์";
			} elseif (strpos($POH_ORG,"รพ.สมุทรปราการ") !== false) {
				$POH_ORG3 = "รพ.สมุทรปราการ";
			} elseif (strpos($POH_ORG,"สถาบันพยาธิวิทยา") !== false) {
				$POH_ORG3 = "สถาบันพยาธิวิทยา";
			} elseif (strpos($POH_ORG,"สำนักงานสาธารณสุขจังหวัดนครปฐม") !== false) {
				$POH_ORG3 = "สำนักงานสาธารณสุขจังหวัดนครปฐม";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (strpos($POH_ORG,"เจ้าหน้าที่พยาบาล") !== false) {
				$POH_ORG3 = "กองการเจ้าหน้าที่";
			} elseif (trim($POH_ORG)) {
				echo "$POH_ORG<br>";
			} 
			$POH_ORG1 = "กระทรวงสาธารณสุข";
			$POH_ORG2 = "กรมการแพทย์";
			if (strpos($POH_ORG,"สสจ.") !== false || strpos($POH_ORG,"สำนักงานสาธารณสุขจังหวัด") !== false) 
				$POH_ORG2 = "สำนักงานปลัดกระทรวงสาธารณสุข";
//			$POH_ORG3 = trim($data[DEPT_NAME]);
			$POSITION_FLAG = $SALARY_FLAG = 0;
			if (strpos($DESCRIPTION,"เงินเดือน") !== false || (strpos($DESCRIPTION,"บรรจุ") !== false) && $NEW_FLAG==1) $SALARY_FLAG = 1;
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
			if ($ROYAL && !$DC_CODE) echo "เครื่องราชย์ $ROYAL<br>";

			$cmd = " SELECT DEH_DATE FROM PER_DECORATEHIS WHERE PER_ID = $EMPID AND DC_CODE = '$DC_CODE' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$DEH_DATE = $data[DEH_DATE];
			if ($DEH_DATE) {
				$i++;
				echo "$i เครื่องราชย์ $EMPID - $ROYAL<br>";
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
			if ($TEMP_INS_CODE && $TEMP_INS_CODE != $INS_CODE) echo "สถาบันการศึกษา $INS_NAME $INS_CODE != $TEMP_INS_CODE <br>";

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
			if ($TEMP_EN_CODE && $TEMP_EN_CODE != $EN_CODE) echo "วุฒิการศึกษา $EN_NAME $EN_CODE != $TEMP_EN_CODE <br>";

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
			if ($TEMP_EM_CODE && $TEMP_EM_CODE != $EM_CODE) echo "สาขาวิชาเอก $EM_NAME $EM_CODE != $TEMP_EM_CODE <br>";

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
			if ($TRAININGNAME && !$TR_CODE) echo "ฝึกอบรม $TRAININGNAME<br>";
				
			$cmd = " SELECT PER_NAME FROM PER_PERSONAL WHERE PER_ID = $EMPID ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$PER_NAME = $data[PER_NAME];
			if (!$PER_NAME) {
				$i++;
				echo "$i ฝึกอบรม $EMPID<br>";
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
			elseif ($INSCODE=='1400001') $INS_CODE = "1400001"; // จุฬาลงกรณ์มหาวิทยาลัย
			elseif ($INSCODE=='1400002') $INS_CODE = "1400002"; // มหาวิทยาลัยเกษตรศาสตร์
			elseif ($INSCODE=='1400003') $INS_CODE = "1400003"; // มหาวิทยาลัยขอนแก่น
			elseif ($INSCODE=='1400004') $INS_CODE = "1400004"; // มหาวิทยาลัยเชียงใหม่
			elseif ($INSCODE=='1400005') $INS_CODE = "1400005"; // มหาวิทยาลัยธรรมศาสตร์
			elseif ($INSCODE=='1400006') $INS_CODE = "1400006"; // มหาวิทยาลัยนเรศวร
			elseif ($INSCODE=='1400007') $INS_CODE = "1400007"; // มหาวิทยาลัยบูรพา
			elseif ($INSCODE=='1400008') $INS_CODE = "1400008"; // มหาวิทยาลัยมหิดล
			elseif ($INSCODE=='1400009') $INS_CODE = "1400009"; // มหาวิทยาลัยรามคำแหง
			elseif ($INSCODE=='1400010') $INS_CODE = "1400010"; // มหาวิทยาลัยศรีนครินทรวิโรฒ
			elseif ($INSCODE=='1400011') $INS_CODE = "1400011"; // มหาวิทยาลัยศิลปากร
			elseif ($INSCODE=='1400012') $INS_CODE = "1400012"; // มหาวิทยาลัยสงขลานครินทร์
			elseif ($INSCODE=='1400013') $INS_CODE = "1400013"; // มหาวิทยาลัยสุโขทัยธรรมาธิราช
			elseif ($INSCODE=='1400014') $INS_CODE = "1400014"; // มหาวิทยาลัยอุบลราชธานี
			elseif ($INSCODE=='1400016') $INS_CODE = "1400016"; // สถาบันเทคโนโลยีการเกษตรแม่โจ้/มหาวิทยาลั
			elseif ($INSCODE=='1400018') $INS_CODE = "1400018"; // สถาบันเทคโนโลยีพระจอมเกล้า ธนบุรี
			elseif ($INSCODE=='1400019') $INS_CODE = "1400019"; // สถาบันเทคโนโลยีพระจอมเกล้า พระนครเหนือ
			elseif ($INSCODE=='1400020') $INS_CODE = "1400020"; // สถาบันบัณฑิตพัฒนบริหารศาสตร์
			elseif ($INSCODE=='1400021') $INS_CODE = "1400021"; // แพทยสภา
			elseif ($INSCODE=='1400022') $INS_CODE = "1400022"; // มหาจุฬาลงกรณราชวิทยาลัย ในพระบรมราชูปถัม
			elseif ($INSCODE=='1400025') $INS_CODE = "1400025"; // มหาวิทยาลัยมหาสารคาม
			elseif ($INSCODE=='1400027') $INS_CODE = "1400027"; // มหาวิทยาลัยวลัยลักษณ์
			elseif ($INSCODE=='1400031') $INS_CODE = "1400031"; // สภาเภสัชกรรม
			elseif ($INSCODE=='1400032') $INS_CODE = "1400032"; // สภาการพยาบาล
			elseif ($INSCODE=='1400033') $INS_CODE = "1400033"; // ราชวิทยาลัยอายุรแพทย์แห่งประเทศไทย ในพระบรมราชูปถัมภ์
			elseif ($INSCODE=='1401001') $INS_CODE = "1401001"; // มหาวิทยาลัยกรุงเทพ
			elseif ($INSCODE=='1401002') $INS_CODE = "1401002"; // มหาวิทยาลัยเกริก
			elseif ($INSCODE=='1401003') $INS_CODE = "1401003"; // มหาวิทยาลัยธุรกิจบัณฑิตย์
			elseif ($INSCODE=='1401004') $INS_CODE = "1401004"; // มหาวิทยาลัยศรีปทุม วิทยาเขตชลบุรี
			elseif ($INSCODE=='1401005') $INS_CODE = "1401005"; // มหาวิทยาลัยหอการค้าไทย
			elseif ($INSCODE=='1401006') $INS_CODE = "1401006"; // มหาวิทยาลัยอัสสัมชัญ วิทยาเขตบางนา
			elseif ($INSCODE=='1401007') $INS_CODE = "1401007"; // มหาวิทยาลัยเอเชียอาคเนย์
			elseif ($INSCODE=='1401008') $INS_CODE = "1401008"; // มหาวิทยาลัยสยาม
			elseif ($INSCODE=='1401009') $INS_CODE = "1401009"; // มหาวิทยาลัยพายัพ
			elseif ($INSCODE=='1401011') $INS_CODE = "1401011"; // มหาวิทยาลัยหัวเฉียวเฉลิมพระเกียรติ วิทยา
			elseif ($INSCODE=='1401013') $INS_CODE = "1401013"; // มหาวิทยาลัยคริสเตียน
			elseif ($INSCODE=='1401015') $INS_CODE = "1401015"; // มหาวิทยาลัยรังสิต
			elseif ($INSCODE=='1401016') $INS_CODE = "1401016"; // วิทยาลัยเซนต์หลุยส์
			elseif ($INSCODE=='1401018') $INS_CODE = "1401018"; // วิทยาลัยมิชชั่น
			elseif ($INSCODE=='1401019') $INS_CODE = "1401019"; // มหาวิทยาลัยเกษมบัณฑิต
			elseif ($INSCODE=='1401020') $INS_CODE = "1401020"; // มหาวิทยาลัยโยนก
			elseif ($INSCODE=='1401022') $INS_CODE = "1401022"; // มหาวิทยาลัยเซนต์จอห์น
			elseif ($INSCODE=='1401025') $INS_CODE = "1401025"; // วิทยาลัยเทคโนโลยีราชธานี
			elseif ($INSCODE=='1401028') $INS_CODE = "1401028"; // มหาวิทยาลัยอีสเทิร์นเอเชีย
			elseif ($INSCODE=='1401031') $INS_CODE = "1401031"; // มหาวิทยาลัยณิวัฒนา
			elseif ($INSCODE=='1401047') $INS_CODE = "1401047"; // วิทยาลัยปทุธานี
			elseif ($INSCODE=='1401052') $INS_CODE = "1401052"; // วิทยาลัยกรุงเทพธนบุรี
			elseif ($INSCODE=='1402101') $INS_CODE = "1402101"; // สถาบันราชภัฏเชียงใหม่
			elseif ($INSCODE=='1402103') $INS_CODE = "1402103"; // สถาบันราชภัฏลำปาง
			elseif ($INSCODE=='1402104') $INS_CODE = "1402104"; // สถาบันราชภัฏอุตรดิตถ์
			elseif ($INSCODE=='1402107') $INS_CODE = "1402107"; // สถาบันราชภัฏนครสวรรค์
			elseif ($INSCODE=='1402108') $INS_CODE = "1402108"; // สถาบันราชภัฏเพชรบูรณ์
			elseif ($INSCODE=='1402109') $INS_CODE = "1402109"; // สถาบันราชภัฏอุดรธานี
			elseif ($INSCODE=='1402111') $INS_CODE = "1402111"; // สถาบันราชภัฏเลย
			elseif ($INSCODE=='1402113') $INS_CODE = "1402113"; // สถาบันราชภัฏนครราชสีมา
			elseif ($INSCODE=='1402114') $INS_CODE = "1402114"; // สถาบันราชภัฏบุรีรัมย์
			elseif ($INSCODE=='1402115') $INS_CODE = "1402115"; // สถาบันราชภัฏสุรินทร์
			elseif ($INSCODE=='1402116') $INS_CODE = "1402116"; // สถาบันราชภัฏอุบลราชธานี
			elseif ($INSCODE=='1402117') $INS_CODE = "1402117"; // สถาบันราชภัฏพระนครศรีอยุธยา
			elseif ($INSCODE=='1402119') $INS_CODE = "1402119"; // สถาบันราชภัฏราชนครินทร์ ฉะเชิงเทรา
			elseif ($INSCODE=='1402120') $INS_CODE = "1402120"; // สถาบันราชภัฏเทพสตรี ลพบุรี
			elseif ($INSCODE=='1402121') $INS_CODE = "1402121"; // สถาบันราชภัฏเพชรบุรีวิทยาลงกรณ์
			elseif ($INSCODE=='1402122') $INS_CODE = "1402122"; // สถาบันราชภัฏเพชรบุรี
			elseif ($INSCODE=='1402123') $INS_CODE = "1402123"; // สถาบันราชภัฏกาญจนบุรี
			elseif ($INSCODE=='1402124') $INS_CODE = "1402124"; // สถาบันราชภัฏนครปฐม
			elseif ($INSCODE=='1402125') $INS_CODE = "1402125"; // สถาบันราชภัฏหมู่บ้านจอมบึง ราชบุรี
			elseif ($INSCODE=='1402126') $INS_CODE = "1402126"; // สถาบันราชภัฏสุราษฎร์ธานี
			elseif ($INSCODE=='1402128') $INS_CODE = "1402128"; // สถาบันราชภัฏภูเก็ต
			elseif ($INSCODE=='1402129') $INS_CODE = "1402129"; // สถาบันราชภัฏสงขลา
			elseif ($INSCODE=='1402130') $INS_CODE = "1402130"; // สถาบันราชภัฏยะลา
			elseif ($INSCODE=='1402131') $INS_CODE = "1402131"; // สถาบันราชภัฏสวนสุนันทา
			elseif ($INSCODE=='1402132') $INS_CODE = "1402132"; // สถาบันราชภัฏสวนดุสิต
			elseif ($INSCODE=='1402133') $INS_CODE = "1402133"; // สถาบันราชภัฏจันทรเกษม
			elseif ($INSCODE=='1402134') $INS_CODE = "1402134"; // สถาบันราชภัฏพระนคร
			elseif ($INSCODE=='1402135') $INS_CODE = "1402135"; // สถาบันราชภัฏธนบุรี
			elseif ($INSCODE=='1402136') $INS_CODE = "1402136"; // สถาบันราชภัฏบ้านสมเด็จเจ้าพระยา
			elseif ($INSCODE=='1402142') $INS_CODE = "1402142"; // มหาวิทยาลัยราชภัฏวไลอลงกรณ์
			elseif ($INSCODE=='1402200') $INS_CODE = "1402200"; // สถาบันการศึกษาในสังกัดสถาบันเทคโนโลยีราชมงคล
			elseif ($INSCODE=='1402204') $INS_CODE = "1402204"; // สถาบันเทคโนโลยีราชมงคลวิทยาเขตเทคนิคกรุงเทพ
			elseif ($INSCODE=='1402206') $INS_CODE = "1402206"; // สถาบันเทคโนโลยีราชมงคลวิทยาเขตบพิตรพิมุข จักรวรรดิ
			elseif ($INSCODE=='1402208') $INS_CODE = "1402208"; // สถาบันเทคโนโลยีราชมงคลวิทยาเขตจักรพงษภูวนาท
			elseif ($INSCODE=='1402210') $INS_CODE = "1402210"; // สถาบันเทคโนโลยีราชมงคลวิทยาเขตพระนครใต้
			elseif ($INSCODE=='1402218') $INS_CODE = "1402218"; // สถาบันเทคโนโลยีราชมงคลวิทยาเขตพิษณุโลก
			elseif ($INSCODE=='1402219') $INS_CODE = "1402219"; // สถาบันเทคโนโลยีราชมงคลวิทยาเขตปทุมธานี
			elseif ($INSCODE=='1402222') $INS_CODE = "1402222"; // สถาบันเทคโนโลยีราชมงคลวิทยาเขตพระนครศรีอยุธยา หันตรา
			elseif ($INSCODE=='1402223') $INS_CODE = "1402223"; // สถาบันเทคโนโลยีราชมงคลวิทยาเขตพระนครศรีอยุธยา วาสุกรี
			elseif ($INSCODE=='1402224') $INS_CODE = "1402224"; // สถาบันเทคโนโลยีราชมงคลวิทยาเขตสุพรรณบุรี
			elseif ($INSCODE=='1402233') $INS_CODE = "1402233"; // สถาบันเทคโนโลยีราชมงคลวิทยาเขตกาฬสินธุ์
			elseif ($INSCODE=='1402239') $INS_CODE = "1402239"; // สถาบันเทคโนโลยีราชมงคลวิทยาเขตภาคใต้
			elseif ($INSCODE=='1402241') $INS_CODE = "1402241"; // สถาบันเทคโนโลยีราชมงคลวิทยาเขตนครศรีธรรมราช
			elseif ($INSCODE=='1402242') $INS_CODE = "1402242"; // สถาบันเทคโนโลยีราชมงคลวิทยาเขตธัญบุรี
			elseif ($INSCODE=='1402300') $INS_CODE = "1402300"; // สถาบันการศึกษาในสังกัดกรมการศาสนา
			elseif ($INSCODE=='1402401003') $INS_CODE = "1402401003"; // วิทยาลัยอาชีวศึกษาเชียงใหม่
			elseif ($INSCODE=='1402401010') $INS_CODE = "1402401010"; // วิทยาลัยเทคนิคลำพูน
			elseif ($INSCODE=='1402401015') $INS_CODE = "1402401015"; // วิทยาลัยอาชีวศึกษาแพร่
			elseif ($INSCODE=='1402401020') $INS_CODE = "1402401020"; // วิทยาลัยเทคนิคน่าน *
			elseif ($INSCODE=='1402401035') $INS_CODE = "1402401035"; // วิทยาลัยเทคนิคพะเยา
			elseif ($INSCODE=='1402401040') $INS_CODE = "1402401040"; // วิทยาลัยอาชีวศึกษาลำปาง
			elseif ($INSCODE=='1402401045') $INS_CODE = "1402401045"; // วิทยาลัยอาชีวศึกษาสุโขทัย
			elseif ($INSCODE=='1402401058') $INS_CODE = "1402401058"; // วิทยาลัยเทคนิคพิษณุโลก
			elseif ($INSCODE=='1402401064') $INS_CODE = "1402401064"; // วิทยาลัยเทคนิคพิจิตร
			elseif ($INSCODE=='1402401068') $INS_CODE = "1402401068"; // วิทยาลัยเทคนิคเพชรบูรณ์
			elseif ($INSCODE=='1402402019') $INS_CODE = "1402402019"; // วิทยาลัยอาชีวศึกษาอุดรธานี
			elseif ($INSCODE=='1402402031') $INS_CODE = "1402402031"; // วิทยาลัยเทคนิคนครพนม  *
			elseif ($INSCODE=='1402402057') $INS_CODE = "1402402057"; // วิทยาลัยอาชีวศึกษาร้อยเอ็ด
			elseif ($INSCODE=='1402402065') $INS_CODE = "1402402065"; // วิทยาลัยเทคนิคยโสธร
			elseif ($INSCODE=='1402402068') $INS_CODE = "1402402068"; // วิทยาลัยเทคนิคอุบลราชธานี
			elseif ($INSCODE=='1402402070') $INS_CODE = "1402402070"; // วิทยาลัยอาชีวศึกษาอุบลราชธานี
			elseif ($INSCODE=='1402402076') $INS_CODE = "1402402076"; // วิทยาลัยเทคนิคบุรีรัมย์
			elseif ($INSCODE=='1402402082') $INS_CODE = "1402402082"; // วิทยาลัยเทคนิคสุรินทร์ *
			elseif ($INSCODE=='1402402089') $INS_CODE = "1402402089"; // วิทยาลัยเทคนิคศรีสะเกษ
			elseif ($INSCODE=='1402402099') $INS_CODE = "1402402099"; // วิทยาลัยอาชีวศึกษานครราชสีมา
			elseif ($INSCODE=='1402402107') $INS_CODE = "1402402107"; // วิทยาลัยเทคนิคชัยภูมิ
			elseif ($INSCODE=='1402403006') $INS_CODE = "1402403006"; // วิทยาลัยเทคนิคยะลา *
			elseif ($INSCODE=='1402403013') $INS_CODE = "1402403013"; // วิทยาลัยอาชีวศึกษาปัตตานี
			elseif ($INSCODE=='1402403023') $INS_CODE = "1402403023"; // วิทยาลัยการอาชีพบางแก้ว
			elseif ($INSCODE=='1402403027') $INS_CODE = "1402403027"; // วิทยาลัยอาชีวศึกษาสงขลา
			elseif ($INSCODE=='1402403037') $INS_CODE = "1402403037"; // วิทยาลัยเทคนิคชุมพร
			elseif ($INSCODE=='1402403045') $INS_CODE = "1402403045"; // วิทยาลัยอาชีวศึกษาสุราษฎร์ธานี *
			elseif ($INSCODE=='1402403054') $INS_CODE = "1402403054"; // วิทยาลัยอาชีวศึกษานครศรีธรรมราช
			elseif ($INSCODE=='1402404002') $INS_CODE = "1402404002"; // วิทยาลัยอาชีวศึกษาเพชรบุรี
			elseif ($INSCODE=='1402404007') $INS_CODE = "1402404007"; // วิทยาลัยเทคนิคประจวบคีรีขันธ์
			elseif ($INSCODE=='1402404011') $INS_CODE = "1402404011"; // วิทยาลัยเทคนิคราชบุรี *
			elseif ($INSCODE=='1402404018') $INS_CODE = "1402404018"; // วิทยาลัยเทคนิคชัยนาท
			elseif ($INSCODE=='1402404021') $INS_CODE = "1402404021"; // วิทยาลัยเทคนิคอุทัยธานี
			elseif ($INSCODE=='1402404027') $INS_CODE = "1402404027"; // วิทยาลัยอาชีวศึกษานครสวรรค์ *
			elseif ($INSCODE=='1402404039') $INS_CODE = "1402404039"; // วิทยาลัยเทคนิคพระนครศรีอยุธยา *
			elseif ($INSCODE=='1402404042') $INS_CODE = "1402404042"; // วิทยาลัยอาชีวศึกษาพระนครศรีอยุธยา
			elseif ($INSCODE=='1402404047') $INS_CODE = "1402404047"; // วิทยาลัยเทคนิคลพบุรี
			elseif ($INSCODE=='1402404068') $INS_CODE = "1402404068"; // วิทยาลัยอาชีวศึกษาสุพรรณบุรี
			elseif ($INSCODE=='1402404076') $INS_CODE = "1402404076"; // วิทยาลัยเทคนิคนครปฐม
			elseif ($INSCODE=='1402404077') $INS_CODE = "1402404077"; // วิทยาลัยอาชีวศึกษานครปฐม
			elseif ($INSCODE=='1402404082') $INS_CODE = "1402404082"; // วิทยาลัยเทคนิคสมุทรสาคร *
			elseif ($INSCODE=='1402405004') $INS_CODE = "1402405004"; // วิทยาลัยเทคนิคปราจีนบุรี
			elseif ($INSCODE=='1402405020') $INS_CODE = "1402405020"; // วิทยาลัยอาชีวศึกษาชลบุรี
			elseif ($INSCODE=='1402405030') $INS_CODE = "1402405030"; // วิทยาลัยเทคนิคตราด
			elseif ($INSCODE=='1402406003') $INS_CODE = "1402406003"; // วิทยาลัยพณิชยการบางนา
			elseif ($INSCODE=='1402406020') $INS_CODE = "1402406020"; // วิทยาลัยพณิชยการธนบุรี
			elseif ($INSCODE=='1402406021') $INS_CODE = "1402406021"; // วิทยาลัยพณิชยการเชตุพน
			elseif ($INSCODE=='1402406023') $INS_CODE = "1402406023"; // วิทยาลัยอาชีวศึกษาธนบุรี
			elseif ($INSCODE=='1402406030') $INS_CODE = "1402406030"; // วิทยาลัยเทคโนโลยีและอาชีวศึกษา
			elseif ($INSCODE=='1402406031') $INS_CODE = "1402406031"; // วิทยาลัยเทคนิคกรุงเทพ
			elseif ($INSCODE=='1402512003') $INS_CODE = "1402512003"; // โรงเรียนศรีบุญยานนท์
			elseif ($INSCODE=='1403003') $INS_CODE = "1403003"; // กระทรวงกลาโหม
			elseif ($INSCODE=='1403011') $INS_CODE = "1403011"; // กรมแพทย์ทหารอากาศ
			elseif ($INSCODE=='1403012') $INS_CODE = "1403012"; // โรงเรียนพยาบาลทหารอากาศ
			elseif ($INSCODE=='1403013') $INS_CODE = "1403013"; // โรงเรียนพยาบาลผดุงครรภ์ และอนามัยแห่งกอง
			elseif ($INSCODE=='1403015') $INS_CODE = "1403015"; // กรมแพทย์ทหารเรือ
			elseif ($INSCODE=='1403017') $INS_CODE = "1403017"; // วิทยาลัยแพทยศาสตร์ พระมงกุฏเกล้า กองทัพบ
			elseif ($INSCODE=='1403034') $INS_CODE = "1403034"; // กรมวิทยาศาสตร์การแพทย์
			elseif ($INSCODE=='1403036') $INS_CODE = "1403036"; // สำนักงานปลัดกระทรวงสาธารณสุข
			elseif ($INSCODE=='1403037') $INS_CODE = "1403037"; // กรมการแพทย์
			elseif ($INSCODE=='1403040') $INS_CODE = "1403040"; // วิทยาลัยพยาบาลสภากาชาดไทย
			elseif ($INSCODE=='1403043') $INS_CODE = "1403043"; // วชิรพยาบาล
			elseif ($INSCODE=='1403050') $INS_CODE = "1403050"; // โรงเรียนพยาบาล ผดุงครรภ์และอนามัย โรงพยา
			elseif ($INSCODE=='1403051') $INS_CODE = "1403051"; // วิทยาลัยพยาบาลเกื้อการุณย์ กรุงเทพมหานคร
			elseif ($INSCODE=='1403059') $INS_CODE = "1403059"; // โรงเรียนรังสีเทคนิคโรงพยาบาลจุฬาลงกรณ์ ส
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
			if ($DEGREECODE=='0010000') $EN_CODE = "0010000"; // วุฒิอื่น ๆ เป็นกลุ่มวุฒิที่หน่วยงานผลิตมาเพื่อใช้เฉพาะตำแหน่ง
			elseif ($DEGREECODE=='0010001') $EN_CODE = "0010001"; // เนติบัณฑิตไทย
			elseif ($DEGREECODE=='0010002') $EN_CODE = "0010002"; // ใบอนุญาตประกอบโรคศิลปะสาขาทันตกรรม
			elseif ($DEGREECODE=='0010003') $EN_CODE = "0010003"; // ใบอนุญาตประกอบโรคศิลปะสาขาเทคนิคการแพทย์
			elseif ($DEGREECODE=='0010004') $EN_CODE = "0010004"; // ใบอนุญาตประกอบวิชาชีพการพยาบาล
			elseif ($DEGREECODE=='0010005') $EN_CODE = "0010005"; // ใบอนุญาตประกอบวิชาชีพผดุงครรภ์ชั้น 1
			elseif ($DEGREECODE=='0010006') $EN_CODE = "0010006"; // ใบอนุญาตประกอบวิชาชีพสาขากายภาพบำบัด
			elseif ($DEGREECODE=='0010007') $EN_CODE = "0010007"; // ใบอนุญาตประกอบวิชาชีพสาขาการพยาบาลและผดุงครรภ์ชั้น 1
			elseif ($DEGREECODE=='0010008') $EN_CODE = "0010008"; // ใบอนุญาตประกอบวิชาชีพสาขาเภสัชกรรมชั้น 1
			elseif ($DEGREECODE=='0010009') $EN_CODE = "0010009"; // ใบอนุญาตประกอบวิชาชีพสาขาเวชกรรม
			elseif ($DEGREECODE=='0010075') $EN_CODE = "0010075"; // ปบ.พยาบาลเวชปฏิบัติทั่วไป
			elseif ($DEGREECODE=='0010087') $EN_CODE = "0010087"; // ปบ.วิสัญญีพยาบาล
			elseif ($DEGREECODE=='0010091') $EN_CODE = "0010091"; // ใบอนุญาตประกอบวิชาชีพสาขาการพยาบาลและผดุงครรภ์ชั้น 2
			elseif ($DEGREECODE=='0010092') $EN_CODE = "0010092"; // ใบอนุญาตเป็นผู้ประกอบโรคศิลปะ สาขารังสีเทคนิค
			elseif ($DEGREECODE=='0010093') $EN_CODE = "0010093"; // ใบอนุญาตประกอบโรคศิลปะสาขาจิตวิทยาคลินิก
			elseif ($DEGREECODE=='0010094') $EN_CODE = "0010094"; // ใบอนุญาตประกอบโรคศิลปะสาขาการแพทย์แผนไทยประเภทการผดุงครรภ์ไทย
			elseif ($DEGREECODE=='0010095') $EN_CODE = "0010095"; // ใบอนุญาตประกอบโรคศิลปะสาขากิจกรรมบำบัด
			elseif ($DEGREECODE=='0010097') $EN_CODE = "0010097"; // ใบอนุญาตประกอบการวิชาชีพการพยาบาล ชั้น 1
			elseif ($DEGREECODE=='0510000') $EN_CODE = "0510000"; // วุฒิต่ำกว่าปบ.วิชาชีพ (ปวช.)
			elseif ($DEGREECODE=='0510002') $EN_CODE = "0510002"; // การศึกษาผู้ใหญ่ระดับ 4 (ม.3 , มศ.3)
			elseif ($DEGREECODE=='0510003') $EN_CODE = "0510003"; // การศึกษาผู้ใหญ่ระดับ 5 (ม.6 , มศ.5)
			elseif ($DEGREECODE=='0510004') $EN_CODE = "0510004"; // นักธรรมเอก
			elseif ($DEGREECODE=='0510005') $EN_CODE = "0510005"; // ปบ.การพยาบาลและผดุงครรภ์(ระดับต้น)
			elseif ($DEGREECODE=='0510006') $EN_CODE = "0510006"; // ปบ.ครูพิเศษการศึกษา
			elseif ($DEGREECODE=='0510008') $EN_CODE = "0510008"; // ปบ.เจ้าพนักงานวิทยาศาสตร์การแพทย์ (ปีที่ 1)
			elseif ($DEGREECODE=='0510009') $EN_CODE = "0510009"; // ปบ.เจ้าหน้าที่เวชสาธิต
			elseif ($DEGREECODE=='0510021') $EN_CODE = "0510021"; // ปบ.ประโยคครูประถม
			elseif ($DEGREECODE=='0510028') $EN_CODE = "0510028"; // ปบ.ผดุงครรภ์อนามัย
			elseif ($DEGREECODE=='0510029') $EN_CODE = "0510029"; // ปบ.ผู้ช่วยทันตแพทย์
			elseif ($DEGREECODE=='0510030') $EN_CODE = "0510030"; // ปบ.ผู้ช่วยพยาบาล
			elseif ($DEGREECODE=='0510031') $EN_CODE = "0510031"; // ปบ.ผู้ช่วยพยาบาลและจิตเวช
			elseif ($DEGREECODE=='0510032') $EN_CODE = "0510032"; // ปบ.ผู้ช่วยพยาบาลและผดุงครรภ์
			elseif ($DEGREECODE=='0510033') $EN_CODE = "0510033"; // ปบ.ผู้ช่วยเภสัชกร
			elseif ($DEGREECODE=='0510036') $EN_CODE = "0510036"; // ปบ.พนักงานผู้ช่วยกายภาพบำบัด
			elseif ($DEGREECODE=='0510043') $EN_CODE = "0510043"; // ปบ.มัธยมศึกษาตอนต้น
			elseif ($DEGREECODE=='0510044') $EN_CODE = "0510044"; // ปบ.มัธยมศึกษาตอนปลาย
			elseif ($DEGREECODE=='0510045') $EN_CODE = "0510045"; // ปบ.มัธยมศึกษาตอนปลาย(วิชาอาชีพ1)
			elseif ($DEGREECODE=='0510050') $EN_CODE = "0510050"; // ปบ.วิชาการศึกษา
			elseif ($DEGREECODE=='0510058') $EN_CODE = "0510058"; // ปบ.วิชาผู้ช่วยพยาบาล
			elseif ($DEGREECODE=='0510059') $EN_CODE = "0510059"; // ปบ.วิชาผู้ช่วยพยาบาลและจิตเวช
			elseif ($DEGREECODE=='0510060') $EN_CODE = "0510060"; // ปบ.วิชาผู้ช่วยเภสัชกร
			elseif ($DEGREECODE=='0510062') $EN_CODE = "0510062"; // ปบ.วิชาพนักงานผู้ช่วยกายภาพบำบัด
			elseif ($DEGREECODE=='0510076') $EN_CODE = "0510076"; // มัธยมศึกษาตอนปลาย
			elseif ($DEGREECODE=='1010000') $EN_CODE = "1010000"; // ปบ.วิชาชีพ (ปวช.) หรือเทียบเท่า
			elseif ($DEGREECODE=='1010008') $EN_CODE = "1010008"; // ปบ.บรรณารักษ์
			elseif ($DEGREECODE=='1010017') $EN_CODE = "1010017"; // ปบ.พนักงานวิทยาศาสตร์การแพทย์ (รังสีเทคนิค)
			elseif ($DEGREECODE=='1010020') $EN_CODE = "1010020"; // ปบ.พนักงานห้องปฏิบัติการชันสูตรโรค
			elseif ($DEGREECODE=='1010022') $EN_CODE = "1010022"; // ปบ.พยาบาลผดุงครรภ์และอนามัย
			elseif ($DEGREECODE=='1010036') $EN_CODE = "1010036"; // ปบ.วิชาเจ้าหน้าที่วิทยาศาสตร์การแพทย์สาขาพยาธิวิทยา
			elseif ($DEGREECODE=='1010040') $EN_CODE = "1010040"; // ปบ.วิชาชีพ ประเภทวิชาคหกรรม
			elseif ($DEGREECODE=='1010041') $EN_CODE = "1010041"; // ปบ.วิชาชีพ ประเภทวิชาช่างอุตสาหกรรม
			elseif ($DEGREECODE=='1010043') $EN_CODE = "1010043"; // ปบ.วิชาชีพ ประเภทวิชาพาณิชยกรรม
			elseif ($DEGREECODE=='1010044') $EN_CODE = "1010044"; // ปบ.วิชาชีพ ประเภทวิชาศิลปหัตถกรรม
			elseif ($DEGREECODE=='1010047') $EN_CODE = "1010047"; // ปบ.วิชาชีพครู
			elseif ($DEGREECODE=='1010054') $EN_CODE = "1010054"; // ปบ.วิชาพยาบาล
			elseif ($DEGREECODE=='1010055') $EN_CODE = "1010055"; // ปบ.วิชาพยาบาลผดุงครรภ์
			elseif ($DEGREECODE=='1010056') $EN_CODE = "1010056"; // ปบ.วิชาภาษาอังกฤษเฉพาะอาชีพ (กฎหมาย)
			elseif ($DEGREECODE=='1010062') $EN_CODE = "1010062"; // ปบ.วิชาภาษาอังกฤษเฉพาะอาชีพ(การสาธารณสุข)
			elseif ($DEGREECODE=='1010075') $EN_CODE = "1010075"; // ปบ.เวชสาธิต
			elseif ($DEGREECODE=='1010080') $EN_CODE = "1010080"; // อนุป.พยาบาลและผดุงครรภ์
			elseif ($DEGREECODE=='1010081') $EN_CODE = "1010081"; // ปบ.การพยาบาลศาสตร์และผดุงครรภ์ชั้นสูง
			elseif ($DEGREECODE=='2010000') $EN_CODE = "2010000"; // ปบ.วิชาชีพเทคนิค (ปวท.) หรือเทียบเท่า
			elseif ($DEGREECODE=='2010003') $EN_CODE = "2010003"; // ปบ.กายอุปกรณ์เสริมและเทียม
			elseif ($DEGREECODE=='2010004') $EN_CODE = "2010004"; // ปบ.การพยาบาลและการผดุงครรภ์(ระดับต้น)
			elseif ($DEGREECODE=='2010005') $EN_CODE = "2010005"; // ปบ.การพยาบาลและผดุงครรภ์ระดับต้น
			elseif ($DEGREECODE=='2010013') $EN_CODE = "2010013"; // ปบ.เจ้าพนักงานเภสัชกรรม
			elseif ($DEGREECODE=='2010014') $EN_CODE = "2010014"; // ปบ.เจ้าพนักงานวิทยาศาสตร์การแพทย์
			elseif ($DEGREECODE=='2010015') $EN_CODE = "2010015"; // ปบ.เจ้าพนักงานวิทยาศาสตร์การแพทย์ (ปีที่ 2)
			elseif ($DEGREECODE=='2010016') $EN_CODE = "2010016"; // ปบ.เจ้าพนักงานสาธารณสุข (พนักงานอนามัย)
			elseif ($DEGREECODE=='2010017') $EN_CODE = "2010017"; // ปบ.เจ้าหน้าที่วิทยาศาสตร์การแพทย์
			elseif ($DEGREECODE=='2010018') $EN_CODE = "2010018"; // ปบ.เจ้าหน้าที่วิทยาศาสตร์การแพทย์ (พยาธิวิทยา)
			elseif ($DEGREECODE=='2010019') $EN_CODE = "2010019"; // ปบ.เจ้าหน้าที่วิทยาศาสตร์การแพทย์ (รังสีเทคนิค)
			elseif ($DEGREECODE=='2010020') $EN_CODE = "2010020"; // ปบ.เจ้าหน้าที่วิทยาศาสตร์การแพทย์ปีที่ 2
			elseif ($DEGREECODE=='2010023') $EN_CODE = "2010023"; // ปบ.ช่างทันตกรรม
			elseif ($DEGREECODE=='2010024') $EN_CODE = "2010024"; // ปบ.ช่างประดิษฐ์กายอุปกรณ์เสริมและเทียม
			elseif ($DEGREECODE=='2010025') $EN_CODE = "2010025"; // ปบ.เซลล์วิทยา
			elseif ($DEGREECODE=='2010026') $EN_CODE = "2010026"; // ปบ.ทันตาภิบาล
			elseif ($DEGREECODE=='2010029') $EN_CODE = "2010029"; // ปบ.ผดุงครรภ์
			elseif ($DEGREECODE=='2010031') $EN_CODE = "2010031"; // ปบ.พนักงานวิทยาศาสตร์การแพทย์
			elseif ($DEGREECODE=='2010036') $EN_CODE = "2010036"; // ปบ.พยาบาลและผดุงครรภ์
			elseif ($DEGREECODE=='2010037') $EN_CODE = "2010037"; // ปบ.พยาบาลวิสัญญีกรรม
			elseif ($DEGREECODE=='2010038') $EN_CODE = "2010038"; // ปบ.พยาบาลศาสตร์ระดับต้น
			elseif ($DEGREECODE=='2010039') $EN_CODE = "2010039"; // ปบ.รังสีการแพทย์
			elseif ($DEGREECODE=='2010040') $EN_CODE = "2010040"; // ปบ.วิชาการพยาบาลและจิตเวช
			elseif ($DEGREECODE=='2010041') $EN_CODE = "2010041"; // ปบ.วิชาการพยาบาลและผดุงครรภ์
			elseif ($DEGREECODE=='2010043') $EN_CODE = "2010043"; // ปบ.วิชาการศึกษาชั้นสูง
			elseif ($DEGREECODE=='2010058') $EN_CODE = "2010058"; // ปบ.วิชาผู้ช่วยพยาบาลและผดุงครรภ์
			elseif ($DEGREECODE=='2010059') $EN_CODE = "2010059"; // ปบ.วิชาพนักงานวิทยาศาสตร์การแพทย์
			elseif ($DEGREECODE=='2010060') $EN_CODE = "2010060"; // ปบ.วิชารังสีเทคนิค
			elseif ($DEGREECODE=='2010062') $EN_CODE = "2010062"; // ปบ.วิชาเวชสถิติ (เริ่มปี 2530)
			elseif ($DEGREECODE=='2010064') $EN_CODE = "2010064"; // ปบ.วิทยาศาสตร์การแพทย์
			elseif ($DEGREECODE=='2010065') $EN_CODE = "2010065"; // ปบ.วิทยาศาสตร์สุขภาพ
			elseif ($DEGREECODE=='2010066') $EN_CODE = "2010066"; // ปบ.เวชนิทัศน์
			elseif ($DEGREECODE=='2010067') $EN_CODE = "2010067"; // ปบ.เวชระเบียน
			elseif ($DEGREECODE=='2010068') $EN_CODE = "2010068"; // ปบ.เวชสถิติ
			elseif ($DEGREECODE=='2010070') $EN_CODE = "2010070"; // ปบ.สาธารณสุขชุมชน(ผดุงครรภ์อนามัย)
			elseif ($DEGREECODE=='2010071') $EN_CODE = "2010071"; // ปบ.สาธารณสุขศาสตร์
			elseif ($DEGREECODE=='2010074') $EN_CODE = "2010074"; // ปบ.สาธารณสุขศาสตร์(เทคนิคเภสัชกรรม)
			elseif ($DEGREECODE=='2010075') $EN_CODE = "2010075"; // ปบ.โสตทัศนศึกษา
			elseif ($DEGREECODE=='2010076') $EN_CODE = "2010076"; // พนักงานวิทยาศาสตร์การแพทย์ สาขาโลหิตวิทยาและธนาคารเลือด
			elseif ($DEGREECODE=='2010078') $EN_CODE = "2010078"; // อนุป.วิชาการศึกษา
			elseif ($DEGREECODE=='2010079') $EN_CODE = "2010079"; // อนุป.วิทยาศาสตร์
			elseif ($DEGREECODE=='2010080') $EN_CODE = "2010080"; // อนุป.ศิลปศาสตร์
			elseif ($DEGREECODE=='2010081') $EN_CODE = "2010081"; // ปบ.เภสัชกรรมแผนไทย
			elseif ($DEGREECODE=='3010000') $EN_CODE = "3010000"; // ปบ.วิชาชีพชั้นสูง (ปวส.) หรือเทียบเท่า
			elseif ($DEGREECODE=='3010006') $EN_CODE = "3010006"; // ปบ.ประโยคครูมัธยม
			elseif ($DEGREECODE=='3010011') $EN_CODE = "3010011"; // ปบ.ประโยคครูมัธยมวิชาชีพชั้นสูง
			elseif ($DEGREECODE=='3010015') $EN_CODE = "3010015"; // ปบ.พยาบาล ผดุงครรภ์และอนามัย
			elseif ($DEGREECODE=='3010018') $EN_CODE = "3010018"; // ปบ.วิชาชีพชั้นสูง (ช่างอุปกรณ์การแพทย์)
			elseif ($DEGREECODE=='3010019') $EN_CODE = "3010019"; // ปบ.วิชาชีพชั้นสูง ประเภทวิชาเกษตรกรรม
			elseif ($DEGREECODE=='3010020') $EN_CODE = "3010020"; // ปบ.วิชาชีพชั้นสูง ประเภทวิชาคหกรรม
			elseif ($DEGREECODE=='3010021') $EN_CODE = "3010021"; // ปบ.วิชาชีพชั้นสูง ประเภทวิชาช่างอุตสาหกรรม
			elseif ($DEGREECODE=='3010022') $EN_CODE = "3010022"; // ปบ.วิชาชีพชั้นสูง ประเภทวิชาบริหารธุรกิจ
			elseif ($DEGREECODE=='3010025') $EN_CODE = "3010025"; // ปบ.วิชาชีพชั้นสูงสายวิชาคหกรรมศาสตร์
			elseif ($DEGREECODE=='3010027') $EN_CODE = "3010027"; // ปบ.วิชาพยาบาลและอนามัย(3ปี)และป.ผดุงครรภ์(6เดือน)
			elseif ($DEGREECODE=='3010028') $EN_CODE = "3010028"; // ปบ.วิทยาศาสตร์การแพทย์(เทียบเท่าอนุป.)
			elseif ($DEGREECODE=='3010031') $EN_CODE = "3010031"; // ประโยคอาชีวศึกษาชั้นสูง
			elseif ($DEGREECODE=='3010032') $EN_CODE = "3010032"; // อนุป.
			elseif ($DEGREECODE=='3010037') $EN_CODE = "3010037"; // อนุป.ทันตานามัย
			elseif ($DEGREECODE=='3010043') $EN_CODE = "3010043"; // อนุป.บริหารธุรกิจ
			elseif ($DEGREECODE=='3010046') $EN_CODE = "3010046"; // อนุป.พยาบาล
			elseif ($DEGREECODE=='3010047') $EN_CODE = "3010047"; // อนุป.พยาบาลและอนามัย
			elseif ($DEGREECODE=='3010048') $EN_CODE = "3010048"; // อนุป.พยาบาลสาธารณสุข (เวชปฏิบัติ)
			elseif ($DEGREECODE=='3010057') $EN_CODE = "3010057"; // อนุป.วิทยาศาสตร์การแพทย์
			elseif ($DEGREECODE=='3010067') $EN_CODE = "3010067"; // อนุป.ศึกษาศาสตร์
			elseif ($DEGREECODE=='3010074') $EN_CODE = "3010074"; // ปบ.การผดุงครรภ์
			elseif ($DEGREECODE=='4010000') $EN_CODE = "4010000"; // ป.ตรีหรือเทียบเท่า
			elseif ($DEGREECODE=='4010002') $EN_CODE = "4010002"; // ปบ.ครูพยาบาลตรี หรือพยาบาลตรี
			elseif ($DEGREECODE=='4010003') $EN_CODE = "4010003"; // ปบ.เทคนิคชั้นสูง (ปทส.)
			elseif ($DEGREECODE=='4010007') $EN_CODE = "4010007"; // ปบ.พยาบาลผดุงครรภ์และอนามัยชั้นสูง
			elseif ($DEGREECODE=='4010008') $EN_CODE = "4010008"; // ปบ.พยาบาลศาสตร์
			elseif ($DEGREECODE=='4010009') $EN_CODE = "4010009"; // ปบ.พยาบาลศาสตรและผดุงครรภ์ชั้นสูง
			elseif ($DEGREECODE=='4010010') $EN_CODE = "4010010"; // ปบ.วิชาผดุงครรภ์
			elseif ($DEGREECODE=='4010012') $EN_CODE = "4010012"; // ป.การศึกษาบัณฑิต
			elseif ($DEGREECODE=='4010013') $EN_CODE = "4010013"; // ป.การศึกษาบัณฑิต (พยาบาลศึกษา)
			elseif ($DEGREECODE=='4010017') $EN_CODE = "4010017"; // ป.เกษตรศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010018') $EN_CODE = "4010018"; // ป.ครุศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010022') $EN_CODE = "4010022"; // ป.ครุศาสตรอุตสาหกรรมบัณฑิต
			elseif ($DEGREECODE=='4010027') $EN_CODE = "4010027"; // ป.คหกรรมศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010030') $EN_CODE = "4010030"; // ป.ทันตแพทยศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010031') $EN_CODE = "4010031"; // ป.เทคโนโลยีการเกษตรบัณฑิต
			elseif ($DEGREECODE=='4010033') $EN_CODE = "4010033"; // ป.เทคโนโลยีบัณฑิต
			elseif ($DEGREECODE=='4010034') $EN_CODE = "4010034"; // ป.นิติศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010035') $EN_CODE = "4010035"; // ป.นิเทศศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010036') $EN_CODE = "4010036"; // ป.บริหารธุรกิจ (บริหารงานอุตสาหกรรม)
			elseif ($DEGREECODE=='4010038') $EN_CODE = "4010038"; // ป.บริหารธุรกิจบัณฑิต
			elseif ($DEGREECODE=='4010039') $EN_CODE = "4010039"; // ป.บริหารธุรกิจบัณฑิต (การตลาด)
			elseif ($DEGREECODE=='4010040') $EN_CODE = "4010040"; // ป.บริหารธุรกิจบัณฑิต (การบริหารงานบุคคล)
			elseif ($DEGREECODE=='4010041') $EN_CODE = "4010041"; // ป.บริหารธุรกิจบัณฑิต (การบัญชี)
			elseif ($DEGREECODE=='4010044') $EN_CODE = "4010044"; // ป.บัญชีบัณฑิต
			elseif ($DEGREECODE=='4010046') $EN_CODE = "4010046"; // ป.พยาบาลศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010047') $EN_CODE = "4010047"; // ป.พาณิชยศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010049') $EN_CODE = "4010049"; // ป.แพทยศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010051') $EN_CODE = "4010051"; // ป.เภสัชบริบาลศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010052') $EN_CODE = "4010052"; // ป.เภสัชศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010054') $EN_CODE = "4010054"; // ป.รัฐประศาสนศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010055') $EN_CODE = "4010055"; // ป.รัฐศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010058') $EN_CODE = "4010058"; // ป.วารสารศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010061') $EN_CODE = "4010061"; // ป.วิทยาศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010062') $EN_CODE = "4010062"; // ป.วิทยาศาสตรบัณฑิต (กายภาพบำบัด)
			elseif ($DEGREECODE=='4010064') $EN_CODE = "4010064"; // ป.วิทยาศาสตรบัณฑิต (คหกรรมศาสตร์)
			elseif ($DEGREECODE=='4010065') $EN_CODE = "4010065"; // ป.วิทยาศาสตรบัณฑิต (จิตวิทยา)
			elseif ($DEGREECODE=='4010067') $EN_CODE = "4010067"; // ป.วิทยาศาสตรบัณฑิต (ทางพยาบาลและผดุงครรภ์)
			elseif ($DEGREECODE=='4010069') $EN_CODE = "4010069"; // ป.วิทยาศาสตรบัณฑิต (ประมง)
			elseif ($DEGREECODE=='4010070') $EN_CODE = "4010070"; // ป.วิทยาศาสตรบัณฑิต (พยาบาล และผดุงครรภ์)
			elseif ($DEGREECODE=='4010072') $EN_CODE = "4010072"; // ป.วิทยาศาสตรบัณฑิต (ฟิสิกส์การแพทย์)
			elseif ($DEGREECODE=='4010074') $EN_CODE = "4010074"; // ป.วิทยาศาสตรบัณฑิต (รังสีเทคนิค)
			elseif ($DEGREECODE=='4010078') $EN_CODE = "4010078"; // ป.วิทยาศาสตรบัณฑิต (วิทยาศาสตร์การแพทย์)
			elseif ($DEGREECODE=='4010079') $EN_CODE = "4010079"; // ป.วิทยาศาสตรบัณฑิต (เวชนิทัศน์)
			elseif ($DEGREECODE=='4010082') $EN_CODE = "4010082"; // ป.วิทยาศาสตรบัณฑิต (เศรษฐศาสตร์เกษตร)
			elseif ($DEGREECODE=='4010083') $EN_CODE = "4010083"; // ป.วิทยาศาสตรบัณฑิต (สัตวศาสตร์)
			elseif ($DEGREECODE=='4010085') $EN_CODE = "4010085"; // ป.วิทยาศาสตรบัณฑิต (สาธารณสุขศาสตร์)
			elseif ($DEGREECODE=='4010087') $EN_CODE = "4010087"; // ป.วิทยาศาสตรบัณฑิต พยาบาล (สาธารณสุข)
			elseif ($DEGREECODE=='4010088') $EN_CODE = "4010088"; // ป.วิทยาศาสตรบัณฑิต(การพยาบาลสาธารณสุข)
			elseif ($DEGREECODE=='4010091') $EN_CODE = "4010091"; // ป.วิทยาศาสตรบัณฑิต(เคมี)
			elseif ($DEGREECODE=='4010092') $EN_CODE = "4010092"; // ป.วิทยาศาสตรบัณฑิต(จิตวิทยา)
			elseif ($DEGREECODE=='4010093') $EN_CODE = "4010093"; // ป.วิทยาศาสตรบัณฑิต(ชีววิทยา)
			elseif ($DEGREECODE=='4010094') $EN_CODE = "4010094"; // ป.วิทยาศาสตรบัณฑิต(พยาบาลและผดุงครรภ์)
			elseif ($DEGREECODE=='4010103') $EN_CODE = "4010103"; // ป.วิศวกรรมศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010105') $EN_CODE = "4010105"; // ป.ศาสนศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010106') $EN_CODE = "4010106"; // ป.ศิลปกรรมบัณฑิต
			elseif ($DEGREECODE=='4010108') $EN_CODE = "4010108"; // ป.ศิลปกรรมศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010111') $EN_CODE = "4010111"; // ป.ศิลปบัณฑิต
			elseif ($DEGREECODE=='4010123') $EN_CODE = "4010123"; // ป.ศิลปศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010125') $EN_CODE = "4010125"; // ป.ศิลปศาสตรบัณฑิต (จิตวิทยา)
			elseif ($DEGREECODE=='4010131') $EN_CODE = "4010131"; // ป.ศิลปศาสตรบัณฑิต (ภาษาไทย)
			elseif ($DEGREECODE=='4010133') $EN_CODE = "4010133"; // ป.ศิลปศาสตรบัณฑิต (ภาษาอังกฤษ)
			elseif ($DEGREECODE=='4010137') $EN_CODE = "4010137"; // ป.ศิลปศาสตรบัณฑิต (สังคมวิทยา มานุษยวิทยา
			elseif ($DEGREECODE=='4010140') $EN_CODE = "4010140"; // ป.ศิลปศาสตรบัณฑิต(รัฐศาสตร์)
			elseif ($DEGREECODE=='4010142') $EN_CODE = "4010142"; // ป.ศึกษาศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010147') $EN_CODE = "4010147"; // ป.ศึกษาศาสตรบัณฑิต (สุขศึกษา)
			elseif ($DEGREECODE=='4010149') $EN_CODE = "4010149"; // ป.เศรษฐศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010151') $EN_CODE = "4010151"; // ป.ส่งเสริมการเกษตรและสหกรณ์บัณฑิต
			elseif ($DEGREECODE=='4010153') $EN_CODE = "4010153"; // ป.สถิติศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010154') $EN_CODE = "4010154"; // ป.สังคมวิทยาบัณฑิต
			elseif ($DEGREECODE=='4010156') $EN_CODE = "4010156"; // ป.สังคมศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010157') $EN_CODE = "4010157"; // ป.สังคมสงเคราะห์ศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010158') $EN_CODE = "4010158"; // ป.สัตวแพทยศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010159') $EN_CODE = "4010159"; // ป.สาธารณสุขศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010160') $EN_CODE = "4010160"; // ป.สาธารณสุขศาสตรบัณฑิต (บริหารสาธารณสุข)
			elseif ($DEGREECODE=='4010164') $EN_CODE = "4010164"; // ป.อักษรศาสตรบัณฑิต (บรรณารักษ์ศาสตร์)
			elseif ($DEGREECODE=='4010166') $EN_CODE = "4010166"; // ป.อุตสาหกรรมศาสตรบัณฑิต
			elseif ($DEGREECODE=='4010168') $EN_CODE = "4010168"; // ปบ.พยาบาลศสาตร์เทียบเท่าปริญญาตรี (ต่อเนื่อง 2 ปี)
			elseif ($DEGREECODE=='4010169') $EN_CODE = "4010169"; // ปบ.พยาบาลศาสตร์ (ต่อเนื่อง 2 ปี เทียบเท่าปริญญาตรี)
			elseif ($DEGREECODE=='5010000') $EN_CODE = "5010000"; // ปบ.ชั้นสูง/ปบ.บัณฑิต (สูงกว่าป.ตรี/ต่ำกว่าป.โท)
			elseif ($DEGREECODE=='5010002') $EN_CODE = "5010002"; // ปบ.ชั้นสูง/ปบ.บัณฑิตวิทยาศาสตร์การแพทย์คลีนิค
			elseif ($DEGREECODE=='5010005') $EN_CODE = "5010005"; // ปบ.ชั้นสูงการบัญชี
			elseif ($DEGREECODE=='5010008') $EN_CODE = "5010008"; // ปบ.ชั้นสูงทางการสอบบัญชี
			elseif ($DEGREECODE=='5010009') $EN_CODE = "5010009"; // ปบ.ชั้นสูงทางวิทยาศาสตร์การแพทย์
			elseif ($DEGREECODE=='5010010') $EN_CODE = "5010010"; // ปบ.ชั้นสูงทางวิทยาศาสตร์การแพทย์คลีนิค
			elseif ($DEGREECODE=='5010036') $EN_CODE = "5010036"; // ปบ.บัณฑิตทางวิทยาศาสตร์การแพทย์คลินิก
			elseif ($DEGREECODE=='5010037') $EN_CODE = "5010037"; // ปบ.บัณฑิตทางวิทยาศาสตร์การแพทย์คลีนิค
			elseif ($DEGREECODE=='5010042') $EN_CODE = "5010042"; // ปบ.บัณฑิตวิชาชีพครู
			elseif ($DEGREECODE=='5010043') $EN_CODE = "5010043"; // ปบ.บัณฑิตอายุรศาสตร์เขตร้อนและสุขวิทยา
			elseif ($DEGREECODE=='6010000') $EN_CODE = "6010000"; // ป.โทหรือเทียบเท่า
			elseif ($DEGREECODE=='6010002') $EN_CODE = "6010002"; // ป.การจัดการภาครัฐและภาคเอกชนมหาบัณฑิต
			elseif ($DEGREECODE=='6010003') $EN_CODE = "6010003"; // ป.การจัดการมหาบัณฑิต
			elseif ($DEGREECODE=='6010007') $EN_CODE = "6010007"; // ป.การศึกษามหาบัณฑิต
			elseif ($DEGREECODE=='6010010') $EN_CODE = "6010010"; // ป.ครุศาสตรมหาบัณฑิต
			elseif ($DEGREECODE=='6010013') $EN_CODE = "6010013"; // ป.คหกรรมศาสตรมหาบัณฑิต
			elseif ($DEGREECODE=='6010015') $EN_CODE = "6010015"; // ป.ทันตแพทยศาสตรมหาบัณฑิต
			elseif ($DEGREECODE=='6010018') $EN_CODE = "6010018"; // ป.นิติศาสตรมหาบัณฑิต
			elseif ($DEGREECODE=='6010021') $EN_CODE = "6010021"; // ป.บริหารธุรกิจมหาบัณฑิต
			elseif ($DEGREECODE=='6010022') $EN_CODE = "6010022"; // ป.บัญชีมหาบัณฑิต
			elseif ($DEGREECODE=='6010025') $EN_CODE = "6010025"; // ป.พยาบาลศาสตรมหาบัณฑิต
			elseif ($DEGREECODE=='6010027') $EN_CODE = "6010027"; // ป.พัฒนบริหารศาสตรมหาบัณฑิต
			elseif ($DEGREECODE=='6010029') $EN_CODE = "6010029"; // ป.พัฒนบริหารศาสตรมหาบัณฑิต (สถิติประยุกต์)
			elseif ($DEGREECODE=='6010030') $EN_CODE = "6010030"; // ป.พัฒนบริหารศาสตรมหาบัณฑิต(พัฒนาสังคม)
			elseif ($DEGREECODE=='6010031') $EN_CODE = "6010031"; // ป.พัฒนบริหารศาสตรมหาบัณฑิตทางการจัดการภาครัฐและเอกชน
			elseif ($DEGREECODE=='6010039') $EN_CODE = "6010039"; // ป.พัฒนบริหารศาสตรมหาบัณฑิตทางรัฐประศาสนศาสตร์
			elseif ($DEGREECODE=='6010044') $EN_CODE = "6010044"; // ป.พัฒนาแรงงานและสวัสดิการมหาบัณฑิต
			elseif ($DEGREECODE=='6010045') $EN_CODE = "6010045"; // ป.พาณิชยศาสตรมหาบัณฑิต
			elseif ($DEGREECODE=='6010047') $EN_CODE = "6010047"; // ป.เภสัชศาสตรมหาบัณฑิต
			elseif ($DEGREECODE=='6010048') $EN_CODE = "6010048"; // ป.มานุษยวิทยามหาบัณฑิต
			elseif ($DEGREECODE=='6010049') $EN_CODE = "6010049"; // ป.รัฐประศาสนศาสตรมหาบัณฑิต
			elseif ($DEGREECODE=='6010050') $EN_CODE = "6010050"; // ป.รัฐศาสตรมหาบัณฑิต
			elseif ($DEGREECODE=='6010054') $EN_CODE = "6010054"; // ป.วิทยาศาสตรมหาบัณฑิต
			elseif ($DEGREECODE=='6010056') $EN_CODE = "6010056"; // ป.วิทยาศาสตรมหาบัณฑิต (เคมี)
			elseif ($DEGREECODE=='6010059') $EN_CODE = "6010059"; // ป.วิทยาศาสตรมหาบัณฑิต (เทคโนโลยีสิ่งแวดล้อม)
			elseif ($DEGREECODE=='6010060') $EN_CODE = "6010060"; // ป.วิทยาศาสตรมหาบัณฑิต (พยาบาลศาสตร์)
			elseif ($DEGREECODE=='6010063') $EN_CODE = "6010063"; // ป.วิทยาศาสตรมหาบัณฑิต (วิทยาการระบาด)
			elseif ($DEGREECODE=='6010071') $EN_CODE = "6010071"; // ป.วิทยาศาสตรมหาบัณฑิต(พยาบาล)
			elseif ($DEGREECODE=='6010075') $EN_CODE = "6010075"; // ป.วิทยาศาสตรมหาบัณฑิต(สถิติประยุกต์)
			elseif ($DEGREECODE=='6010076') $EN_CODE = "6010076"; // ป.วิทยาศาสตรมหาบัณฑิต(สาธารณสุขศาสตร์)
			elseif ($DEGREECODE=='6010077') $EN_CODE = "6010077"; // ป.วิทยาศาสตรมหาบัณฑิตการแพทย์คลีนิค
			elseif ($DEGREECODE=='6010089') $EN_CODE = "6010089"; // ป.ศิลปศาสตรมหาบัณฑิต
			elseif ($DEGREECODE=='6010101') $EN_CODE = "6010101"; // ป.ศึกษาศาสตรมหาบัณฑิต
			elseif ($DEGREECODE=='6010103') $EN_CODE = "6010103"; // ป.ศึกษาศาสตรมหาบัณฑิต (จิตวิทยาการศึกษา)
			elseif ($DEGREECODE=='6010104') $EN_CODE = "6010104"; // ป.เศรษฐศาสตรมหาบัณฑิต
			elseif ($DEGREECODE=='6010111') $EN_CODE = "6010111"; // ป.สังคมศาสตรมหาบัณฑิต
			elseif ($DEGREECODE=='6010113') $EN_CODE = "6010113"; // ป.สังคมสงเคราะห์ศาสตรมหาบัณฑิต
			elseif ($DEGREECODE=='6010114') $EN_CODE = "6010114"; // ป.สาธารณสุขศาสตรมหาบัณฑิต
			elseif ($DEGREECODE=='6010115') $EN_CODE = "6010115"; // ป.อักษรศาสตรมหาบัณฑิต
			elseif ($DEGREECODE=='8010003') $EN_CODE = "8010003"; // ป.การศึกษาดุษฎีบัณฑิต
			elseif ($DEGREECODE=='8010004') $EN_CODE = "8010004"; // ป.ครุศาสตรดุษฎีบัณฑิต
			elseif ($DEGREECODE=='8010007') $EN_CODE = "8010007"; // ป.ดุษฎีบัณฑิตกิตติมศักดิ์
			elseif ($DEGREECODE=='8010014') $EN_CODE = "8010014"; // ป.ปรัชญาดุษฎีบัณฑิต
			elseif ($DEGREECODE=='8010026') $EN_CODE = "8010026"; // ป.วิทยาศาสตรดุษฎีบัณฑิต
			elseif ($DEGREECODE=='8010035') $EN_CODE = "8010035"; // ป.สาธารณสุขศาสตรดุษฎีบัณฑิต
			elseif ($DEGREECODE=='9010003') $EN_CODE = "9010003"; // วุฒิบัตรจากแพทยสภา
			elseif ($DEGREECODE=='9010004') $EN_CODE = "9010004"; // วุฒิบัตรแสดงความรู้ความชำนาญในการประกอบวิชาชีพเวชกรรม
			elseif ($DEGREECODE=='9010005') $EN_CODE = "9010005"; // หนังสืออนุมัติแสดงความรู้ความชำนาญในการประกอบวิชาชีพเวชกรรม
			elseif ($DEGREECODE=='9010006') $EN_CODE = "9010006"; // อนุมัติบัตร
			elseif ($DEGREECODE=='9010007') $EN_CODE = "9010007"; // อเมริกันบอร์ด 3 ปี
			elseif ($DEGREECODE=='9010008') $EN_CODE = "9010008"; // วุฒิบัตรจากสภาการพยาบาล
			elseif ($DEGREECODE=='9010009') $EN_CODE = "9010009"; // ปบ.การศึกษาอบรม
			elseif ($DEGREECODE=='9010010') $EN_CODE = "9010010"; // ปบ.การพยาบาลเฉพาะทาง
			$EN_CODE = (trim($EN_CODE))? "'".trim($EN_CODE)."'" : "NULL";

			$EM_CODE = "";
			if ($MAJORCODE=='10000100') $EM_CODE = "10000100"; // 	การศึกษา (EDUCATION)
			elseif ($MAJORCODE=='10000101') $EM_CODE = "10000101"; // ศึกษาศาสตร์
			elseif ($MAJORCODE=='12002101') $EM_CODE = "12002101"; // การประถมศึกษา
			elseif ($MAJORCODE=='15001101') $EM_CODE = "15001101"; // การศึกษาผู้ใหญ่และการศึกษาต่อเนื่อง
			elseif ($MAJORCODE=='16000100') $EM_CODE = "16000100"; // การศึกษาพิเศษ
			elseif ($MAJORCODE=='17012100') $EM_CODE = "17012100"; // สุขศึกษา
			elseif ($MAJORCODE=='17018100') $EM_CODE = "17018100"; // เทคโนโลยีและนวัตกรรม
			elseif ($MAJORCODE=='17019100') $EM_CODE = "17019100"; // วิทยาศาสตร์ (กายภาพชีวภาพ)
			elseif ($MAJORCODE=='17021100') $EM_CODE = "17021100"; // ศิลปศึกษา
			elseif ($MAJORCODE=='17026100') $EM_CODE = "17026100"; // ศึกษาศาสตร์-เกษตร
			elseif ($MAJORCODE=='17039100') $EM_CODE = "17039100"; // จิตวิทยาการศึกษา
			elseif ($MAJORCODE=='17039101') $EM_CODE = "17039101"; // จิตวิทยาการศึกษาและการแนะแนว
			elseif ($MAJORCODE=='17039104') $EM_CODE = "17039104"; // จิตวิทยาการแนะแนว
			elseif ($MAJORCODE=='17039106') $EM_CODE = "17039106"; // จิตวิทยาและการแนะแนว
			elseif ($MAJORCODE=='17039119') $EM_CODE = "17039119"; // จิตวิทยาพัฒนาการ
			elseif ($MAJORCODE=='17039122') $EM_CODE = "17039122"; // จิตวิทยาการปรึกษา
			elseif ($MAJORCODE=='17039123') $EM_CODE = "17039123"; // จิตวิทยาการให้คำปรึกษา
			elseif ($MAJORCODE=='17039124') $EM_CODE = "17039124"; // จิตวิทยา(จิตวิทยาการศึกษาและแนะแนวการศึกษา)
			elseif ($MAJORCODE=='17040100') $EM_CODE = "17040100"; // จิตวิทยาอุตสาหกรรม
			elseif ($MAJORCODE=='17041101') $EM_CODE = "17041101"; // จิตวิทยาชุมชน
			elseif ($MAJORCODE=='17042100') $EM_CODE = "17042100"; // จิตวิทยา
			elseif ($MAJORCODE=='17044100') $EM_CODE = "17044100"; // จิตวิทยาอุตสากรรมและองค์การ
			elseif ($MAJORCODE=='17046100') $EM_CODE = "17046100"; // ธุรกิจศึกษา
			elseif ($MAJORCODE=='17048100') $EM_CODE = "17048100"; // บรรณารักษศาสตร์และสนเทศศาสตร์
			elseif ($MAJORCODE=='17049100') $EM_CODE = "17049100"; // บรรณารักษ์ศาสตร์
			elseif ($MAJORCODE=='17050100') $EM_CODE = "17050100"; // บรรณารักษศาสตร์และสารนิเทศศาสตร์
			elseif ($MAJORCODE=='17052101') $EM_CODE = "17052101"; // การแนะแนว
			elseif ($MAJORCODE=='17053100') $EM_CODE = "17053100"; // คหกรรมศาสตร์ศึกษา
			elseif ($MAJORCODE=='17054100') $EM_CODE = "17054100"; // ครูพยาบาล
			elseif ($MAJORCODE=='17059100') $EM_CODE = "17059100"; // สหกรณ์
			elseif ($MAJORCODE=='17064100') $EM_CODE = "17064100"; // การจัดการสำหรับนักบริหาร
			elseif ($MAJORCODE=='18003102') $EM_CODE = "18003102"; // การวิจัยและพัฒนาหลักสูตร
			elseif ($MAJORCODE=='18004101') $EM_CODE = "18004101"; // นิเทศการศึกษาและพัฒนาหลักสูตร
			elseif ($MAJORCODE=='18006101') $EM_CODE = "18006101"; // เทคโนโลยีทางการศึกษา
			elseif ($MAJORCODE=='18007100') $EM_CODE = "18007100"; // เทคโนโลยีและสื่อสารการศึกษา
			elseif ($MAJORCODE=='18008100') $EM_CODE = "18008100"; // เทคโนโลยีและนวัตกรรมทางการศึกษา
			elseif ($MAJORCODE=='18011100') $EM_CODE = "18011100"; // การวัดและประเมินผลการศึกษา
			elseif ($MAJORCODE=='18014101') $EM_CODE = "18014101"; // การวัดผลการศึกษา
			elseif ($MAJORCODE=='18017100') $EM_CODE = "18017100"; // การวิจัยดำเนินงาน
			elseif ($MAJORCODE=='18019100') $EM_CODE = "18019100"; // โสตทัศนศึกษา
			elseif ($MAJORCODE=='18020100') $EM_CODE = "18020100"; // เทคโนโลยีการศึกษา
			elseif ($MAJORCODE=='18023100') $EM_CODE = "18023100"; // การบริหารการศึกษา
			elseif ($MAJORCODE=='18023101') $EM_CODE = "18023101"; // บริหารการศึกษา
			elseif ($MAJORCODE=='18024100') $EM_CODE = "18024100"; // การบริหารโรงเรียน
			elseif ($MAJORCODE=='21012100') $EM_CODE = "21012100"; // ภาษาศาสตร
			elseif ($MAJORCODE=='21030100') $EM_CODE = "21030100"; // ภาษาอังกฤษ
			elseif ($MAJORCODE=='21031100') $EM_CODE = "21031100"; // ภาษาฝรั่งเศส
			elseif ($MAJORCODE=='21034100') $EM_CODE = "21034100"; // ภาษาไทย
			elseif ($MAJORCODE=='21036100') $EM_CODE = "21036100"; // ภาษาต่างประเทศ
			elseif ($MAJORCODE=='21042100') $EM_CODE = "21042100"; // ภาษาอังกฤษธุรกิจ
			elseif ($MAJORCODE=='21052100') $EM_CODE = "21052100"; // ภาษาญี่ปุ่นธุรกิจ
			elseif ($MAJORCODE=='21055100') $EM_CODE = "21055100"; // ภาษา วิชาเอกภาษาไทย
			elseif ($MAJORCODE=='23000100') $EM_CODE = "23000100"; // ประวัติศาสตร์
			elseif ($MAJORCODE=='26003100') $EM_CODE = "26003100"; // ความผิดปกติของการสื่อความหมาย
			elseif ($MAJORCODE=='30000100') $EM_CODE = "30000100"; // ศิลปกรรม
			elseif ($MAJORCODE=='31000100') $EM_CODE = "31000100"; // ออกแบบประยุกต์ศิลป์
			elseif ($MAJORCODE=='31000101') $EM_CODE = "31000101"; // ออกแบบนิเทศศิลป์
			elseif ($MAJORCODE=='31009100') $EM_CODE = "31009100"; // อุตสาหกรรมศิลป์
			elseif ($MAJORCODE=='31023100') $EM_CODE = "31023100"; // ออกแบบผลิตภัณฑ์
			elseif ($MAJORCODE=='32000100') $EM_CODE = "32000100"; // วิจิตรศิลป์
			elseif ($MAJORCODE=='32005100') $EM_CODE = "32005100"; // จิตรกรรมสากล
			elseif ($MAJORCODE=='32006100') $EM_CODE = "32006100"; // ประติมากรรม
			elseif ($MAJORCODE=='34000100') $EM_CODE = "34000100"; // นาฏศิลป์
			elseif ($MAJORCODE=='35000100') $EM_CODE = "35000100"; // หัตถกรรม
			elseif ($MAJORCODE=='36004100') $EM_CODE = "36004100"; // การออกแบบ
			elseif ($MAJORCODE=='51001100') $EM_CODE = "51001100"; // สังคมศาสตร์การพัฒนา
			elseif ($MAJORCODE=='51004100') $EM_CODE = "51004100"; // สังคมสงเคราะห์
			elseif ($MAJORCODE=='51004101') $EM_CODE = "51004101"; // สังคมสงเคราะห์ศาสตร์
			elseif ($MAJORCODE=='51006100') $EM_CODE = "51006100"; // นโยบายและการวางแผนทางสังคม
			elseif ($MAJORCODE=='51008100') $EM_CODE = "51008100"; // สังคมศึกษา
			elseif ($MAJORCODE=='51009100') $EM_CODE = "51009100"; // สังคมสงเคราะห์ทางการแพทย์
			elseif ($MAJORCODE=='51011100') $EM_CODE = "51011100"; // สังคมศาสตร์สุขภาพ
			elseif ($MAJORCODE=='51012100') $EM_CODE = "51012100"; // สังคมวิทยา
			elseif ($MAJORCODE=='51014100') $EM_CODE = "51014100"; // สังคมวิทยาและมานุษยวิทยา
			elseif ($MAJORCODE=='51015100') $EM_CODE = "51015100"; // สังคมวิทยาประยุกต์
			elseif ($MAJORCODE=='51017101') $EM_CODE = "51017101"; // การพัฒนาชุมชน
			elseif ($MAJORCODE=='51018100') $EM_CODE = "51018100"; // พัฒนาสังคม
			elseif ($MAJORCODE=='51030100') $EM_CODE = "51030100"; // วิจัยประชากรและสังคม
			elseif ($MAJORCODE=='51032100') $EM_CODE = "51032100"; // การพัฒนาทรัพยากรมนุษย์
			elseif ($MAJORCODE=='51032101') $EM_CODE = "51032101"; // การพัฒนาทรัพยากรมนุษย์และองค์การ
			elseif ($MAJORCODE=='51034100') $EM_CODE = "51034100"; // ประชากรและการพัฒนา
			elseif ($MAJORCODE=='51039100') $EM_CODE = "51039100"; // ประชากรศึกษา
			elseif ($MAJORCODE=='51042100') $EM_CODE = "51042100"; // จริยศาสตร์ศึกษา
			elseif ($MAJORCODE=='51043100') $EM_CODE = "51043100"; // ภูมิศาสตร์
			elseif ($MAJORCODE=='51046101') $EM_CODE = "51046101"; // เทคโนโลยีสังคม
			elseif ($MAJORCODE=='51051100') $EM_CODE = "51051100"; // นโยบายสาธารณะ
			elseif ($MAJORCODE=='52009100') $EM_CODE = "52009100"; // เศรษฐศาสตร์เกษตร
			elseif ($MAJORCODE=='52016100') $EM_CODE = "52016100"; // ทรัพยากรมนุษย์
			elseif ($MAJORCODE=='52017100') $EM_CODE = "52017100"; // เศรษฐศาสตร์ธุรกิจ
			elseif ($MAJORCODE=='52021100') $EM_CODE = "52021100"; // เศรษฐศาสตร์สหกรณ์
			elseif ($MAJORCODE=='52027100') $EM_CODE = "52027100"; // เศรษฐศาสตร์การพัฒนา
			elseif ($MAJORCODE=='52029100') $EM_CODE = "52029100"; // เศรษฐศาสตร์สาธารณสุข
			elseif ($MAJORCODE=='53001100') $EM_CODE = "53001100"; // นิเทศศาสตร์
			elseif ($MAJORCODE=='53021100') $EM_CODE = "53021100"; // สื่อสารมวลชน
			elseif ($MAJORCODE=='53039100') $EM_CODE = "53039100"; // สารนิเทศศาสตร์
			elseif ($MAJORCODE=='53041100') $EM_CODE = "53041100"; // วารสารศาสตร์
			elseif ($MAJORCODE=='54000100') $EM_CODE = "54000100"; // รัฐศาสตร์
			elseif ($MAJORCODE=='54001100') $EM_CODE = "54001100"; // การเมืองการปกครอง
			elseif ($MAJORCODE=='54002100') $EM_CODE = "54002100"; // การปกครอง
			elseif ($MAJORCODE=='54008100') $EM_CODE = "54008100"; // รัฐประศาสนศาสตร์
			elseif ($MAJORCODE=='54009100') $EM_CODE = "54009100"; // บริหารรัฐกิจ
			elseif ($MAJORCODE=='54018101') $EM_CODE = "54018101"; // การบริหารองค์การ
			elseif ($MAJORCODE=='54019100') $EM_CODE = "54019100"; // การจัดการ
			elseif ($MAJORCODE=='54019101') $EM_CODE = "54019101"; // การจัดการงานสาธารณะ
			elseif ($MAJORCODE=='54019102') $EM_CODE = "54019102"; // การบริหารและนโยบายสวัสดิการสังคม
			elseif ($MAJORCODE=='54020100') $EM_CODE = "54020100"; // การจัดการทั่วไป
			elseif ($MAJORCODE=='54021100') $EM_CODE = "54021100"; // การบริหารทั่วไป
			elseif ($MAJORCODE=='54028100') $EM_CODE = "54028100"; // การบริหารทรัพยากรมนุษย์
			elseif ($MAJORCODE=='54032100') $EM_CODE = "54032100"; // การวิเคราะห์และประเมินโครงการ
			elseif ($MAJORCODE=='55000100') $EM_CODE = "55000100"; // การโฆษณา
			elseif ($MAJORCODE=='55001100') $EM_CODE = "55001100"; // การประชาสัมพันธ์
			elseif ($MAJORCODE=='55003100') $EM_CODE = "55003100"; // การโฆษณาและการประชาสัมพันธ์
			elseif ($MAJORCODE=='55009100') $EM_CODE = "55009100"; // การบัญชี
			elseif ($MAJORCODE=='55009101') $EM_CODE = "55009101"; // บัญชี
			elseif ($MAJORCODE=='55011107') $EM_CODE = "55011107"; // ธุรกิจระหว่างประเทศ
			elseif ($MAJORCODE=='55011109') $EM_CODE = "55011109"; // การจัดการความขัดแย้งแบบบูรณาการ
			elseif ($MAJORCODE=='55013101') $EM_CODE = "55013101"; // การบริหารธุรกิจ
			elseif ($MAJORCODE=='55013102') $EM_CODE = "55013102"; // บริหารธุรกิจ
			elseif ($MAJORCODE=='55014104') $EM_CODE = "55014104"; // ธุรกิจศึกษา(บัญชี)
			elseif ($MAJORCODE=='55019101') $EM_CODE = "55019101"; // การเลขานุการ
			elseif ($MAJORCODE=='55021100') $EM_CODE = "55021100"; // การจัดการสำนักงาน
			elseif ($MAJORCODE=='55021101') $EM_CODE = "55021101"; // พณิชยการ
			elseif ($MAJORCODE=='55021104') $EM_CODE = "55021104"; // พาณิชยการ
			elseif ($MAJORCODE=='55021105') $EM_CODE = "55021105"; // พาณิชยกรรม
			elseif ($MAJORCODE=='55022100') $EM_CODE = "55022100"; // การตลาด
			elseif ($MAJORCODE=='55023100') $EM_CODE = "55023100"; // การขาย
			elseif ($MAJORCODE=='55024100') $EM_CODE = "55024100"; // การเงินและการธนาคาร
			elseif ($MAJORCODE=='55025100') $EM_CODE = "55025100"; // การธนาคารและการเงิน
			elseif ($MAJORCODE=='55026100') $EM_CODE = "55026100"; // การเงิน
			elseif ($MAJORCODE=='55น่า100') $EM_CODE = "55น่า100"; // การจัดการงานก่อสร้าง
			elseif ($MAJORCODE=='56021100') $EM_CODE = "56021100"; // การโรงแรมและการท่องเที่ยว
			elseif ($MAJORCODE=='57000100') $EM_CODE = "57000100"; // คหกรรมศาสตร์
			elseif ($MAJORCODE=='57000101') $EM_CODE = "57000101"; // คหกรรม
			elseif ($MAJORCODE=='57002100') $EM_CODE = "57002100"; // คหกรรมศาสตร์ทั่วไป
			elseif ($MAJORCODE=='57005100') $EM_CODE = "57005100"; // ผ้าและเครื่องแต่งกาย
			elseif ($MAJORCODE=='57007100') $EM_CODE = "57007100"; // พัฒนาเด็กและครอบครัว
			elseif ($MAJORCODE=='58003100') $EM_CODE = "58003100"; // เลขานุการการแพทย์
			elseif ($MAJORCODE=='58006100') $EM_CODE = "58006100"; // อุตสาหกรรมเครื่องกล
			elseif ($MAJORCODE=='60000100') $EM_CODE = "60000100"; // วิทยาศาสตร์ทั่วไป
			elseif ($MAJORCODE=='60001102') $EM_CODE = "60001102"; // วิทยาศาสตร์-ชีววิทยา
			elseif ($MAJORCODE=='60001103') $EM_CODE = "60001103"; // วิทยาศาสตร์-เคมี
			elseif ($MAJORCODE=='61000100') $EM_CODE = "61000100"; // วิทยาศาสตร์
			elseif ($MAJORCODE=='61003100') $EM_CODE = "61003100"; // พันธุศาสตร์ (GENETICS)
			elseif ($MAJORCODE=='61003102') $EM_CODE = "61003102"; // ชีววิทยา
			elseif ($MAJORCODE=='61005100') $EM_CODE = "61005100"; // ชีววิทยาประยุกต์
			elseif ($MAJORCODE=='61008100') $EM_CODE = "61008100"; // จุลชีววิทยา
			elseif ($MAJORCODE=='61010100') $EM_CODE = "61010100"; // จุลชีววิทยาประยุกต์
			elseif ($MAJORCODE=='61012100') $EM_CODE = "61012100"; // ชีวสถิติ
			elseif ($MAJORCODE=='61015100') $EM_CODE = "61015100"; // เคมี
			elseif ($MAJORCODE=='61022100') $EM_CODE = "61022100"; // ชีวเคมี
			elseif ($MAJORCODE=='61026100') $EM_CODE = "61026100"; // ชีวเคมีทางการแพทย์
			elseif ($MAJORCODE=='61027100') $EM_CODE = "61027100"; // เคมีชีวภาพ
			elseif ($MAJORCODE=='61036100') $EM_CODE = "61036100"; // ฟิสิกส์
			elseif ($MAJORCODE=='63001101') $EM_CODE = "63001101"; // เทคโนโลยีชีวภาพ
			elseif ($MAJORCODE=='63006100') $EM_CODE = "63006100"; // สิ่งแวดล้อมศึกษา
			elseif ($MAJORCODE=='63007100') $EM_CODE = "63007100"; // สิ่งแวดล้อม
			elseif ($MAJORCODE=='63009100') $EM_CODE = "63009100"; // วิทยาศาสตร์และเทคโนโลยีการอาหาร
			elseif ($MAJORCODE=='63014100') $EM_CODE = "63014100"; // นโยบายและการจัดการทรัพยากรและสิ่งแวดล้อม
			elseif ($MAJORCODE=='63017100') $EM_CODE = "63017100"; // อนามัยสิ่งแวดล้อม
			elseif ($MAJORCODE=='63025100') $EM_CODE = "63025100"; // เทคโนโลยีที่เหมาะสมเพื่อการพัฒนาทรัพยากร
			elseif ($MAJORCODE=='63026100') $EM_CODE = "63026100"; // โภชนวิทยา
			elseif ($MAJORCODE=='63027100') $EM_CODE = "63027100"; // เทคโนโลยีอาหาร
			elseif ($MAJORCODE=='63032100') $EM_CODE = "63032100"; // โภชนาการชุมชน
			elseif ($MAJORCODE=='63040100') $EM_CODE = "63040100"; // วิทยาศาสตร์การอาหารและโภชนาการ
			elseif ($MAJORCODE=='63044100') $EM_CODE = "63044100"; // อาหารและโภชนาการ
			elseif ($MAJORCODE=='64000100') $EM_CODE = "64000100"; // สถิติ
			elseif ($MAJORCODE=='64003100') $EM_CODE = "64003100"; // คณิตศาสตร์
			elseif ($MAJORCODE=='64006101') $EM_CODE = "64006101"; // สถิติประยุกต์
			elseif ($MAJORCODE=='65000100') $EM_CODE = "65000100"; // วิทยาการคอมพิวเตอร์
			elseif ($MAJORCODE=='65000101') $EM_CODE = "65000101"; // คอมพิวเตอร์
			elseif ($MAJORCODE=='65001100') $EM_CODE = "65001100"; // คอมพิวเตอร์ศึกษา
			elseif ($MAJORCODE=='65001101') $EM_CODE = "65001101"; // คอมพิวเตอร์ธุรกิจ
			elseif ($MAJORCODE=='65006100') $EM_CODE = "65006100"; // ระบบสารสนเทศคอมพิวเตอร์
			elseif ($MAJORCODE=='65007100') $EM_CODE = "65007100"; // เทคโนโลยีสารสนเทศ
			elseif ($MAJORCODE=='66000100') $EM_CODE = "66000100"; // นิติวิทยาศาสตร์
			elseif ($MAJORCODE=='70000101') $EM_CODE = "70000101"; // การพยาบาลสาธารณสุข
			elseif ($MAJORCODE=='70001100') $EM_CODE = "70001100"; // สูติศาสตร์-นรีเวชวิทยา (Obstetrics and Gynecology)
			elseif ($MAJORCODE=='70002100') $EM_CODE = "70002100"; // โสต นาสิก ลาริงช์วิทยา
			elseif ($MAJORCODE=='70002101') $EM_CODE = "70002101"; // โสต ศอ นาสิกวิทยา (Otolaryngology)
			elseif ($MAJORCODE=='70004100') $EM_CODE = "70004100"; // อายุรศาสตร์ (Internal Medicine)
			elseif ($MAJORCODE=='70004101') $EM_CODE = "70004101"; // อายุรศาสตร์โรคเลือด (Adult Haematology)
			elseif ($MAJORCODE=='70004102') $EM_CODE = "70004102"; // อนุสาขาอายุรศาสตร์โรคหัวใจ (Cardiology)
			elseif ($MAJORCODE=='70004103') $EM_CODE = "70004103"; // อนุสาขาอายุรศาสตร์โรคไต (Nephrology)
			elseif ($MAJORCODE=='70004104') $EM_CODE = "70004104"; // อายุรศาสตร์โรคระบบการหายใจ
			elseif ($MAJORCODE=='70004105') $EM_CODE = "70004105"; // อายุรศาสตร์เขตร้อน
			elseif ($MAJORCODE=='70004107') $EM_CODE = "70004107"; // อายุรศาสตร์เขตร้อนและสุขวิทยา
			elseif ($MAJORCODE=='70004109') $EM_CODE = "70004109"; // อนุสาขาอายุรศาสตร์โรคระบบการหายใจและภาวะวิกฤตโรคระบบการหายใจ (Pulmonary and Pulmonary Critical Care
			elseif ($MAJORCODE=='70004110') $EM_CODE = "70004110"; // อนุสาขาอายุรศาสตร์โรคระบบทางเดินอาหาร (Gastroenterology)
			elseif ($MAJORCODE=='70004111') $EM_CODE = "70004111"; // อนุสาขาอายุรศาสตร์โรคต่อมไร้ท่อและเมตะบอลิสม (Endocrinology and Metabolism)
			elseif ($MAJORCODE=='70004112') $EM_CODE = "70004112"; // อนุสาขาอายุรศาสตร์โรคข้อและรูมาติสซั่ม (Rheumatology)
			elseif ($MAJORCODE=='70004113') $EM_CODE = "70004113"; // อายุรศาสตร์มะเร็งวิทยา (Oncology)
			elseif ($MAJORCODE=='70004114') $EM_CODE = "70004114"; // อนุสาขาอายุรศาสตร์โรคติดเชื้อ (Infectious Diseases)
			elseif ($MAJORCODE=='70004117') $EM_CODE = "70004117"; // อายุรศาสตร์ - ศัลยศาสตร์
			elseif ($MAJORCODE=='70005100') $EM_CODE = "70005100"; // เวชปฏิบัติทั่วไป (General Practice)
			elseif ($MAJORCODE=='70005102') $EM_CODE = "70005102"; // เวชศาสตร์เขตร้อน
			elseif ($MAJORCODE=='70005104') $EM_CODE = "70005104"; // เวชศาสตร์ป้องกัน (Preventive Medicine)
			elseif ($MAJORCODE=='70005105') $EM_CODE = "70005105"; // เวชศาสตร์ป้องกัน แขนงเวชศาสตร์ป้องกันคลินิก (Preventive Medicine : Clinical Preventive Medicine)
			elseif ($MAJORCODE=='70005106') $EM_CODE = "70005106"; // เวชศาสตร์ป้องกัน แขนงระบาดวิทยา (Preventive Medicine : Epidemiology)
			elseif ($MAJORCODE=='70005108') $EM_CODE = "70005108"; // เวชศาสตร์ป้องกัน แขนงสาธารณสุขศาสตร์ (Preventive Medicine : Public Health)
			elseif ($MAJORCODE=='70005110') $EM_CODE = "70005110"; // เวชปฏิบัติทั่วไป (การรักษาโรคเบื้องต้น)
			elseif ($MAJORCODE=='70006100') $EM_CODE = "70006100"; // เวชศาสตร์ฟื้นฟู (Rehabilitation medicine)
			elseif ($MAJORCODE=='70006101') $EM_CODE = "70006101"; // เวชศาสตร์ฉุกเฉิน (Emergency Medicine)
			elseif ($MAJORCODE=='70006102') $EM_CODE = "70006102"; // อนุสาขาเวชศาสตร์มารดาและทารกในครรภ์
			elseif ($MAJORCODE=='70006103') $EM_CODE = "70006103"; // อนุสาขาเวชศาสตร์การเจริญพันธุ์ (Reproductive Medicine)
			elseif ($MAJORCODE=='70007100') $EM_CODE = "70007100"; // เวชสถิติ
			elseif ($MAJORCODE=='70008100') $EM_CODE = "70008100"; // กุมารเวชศาสตร์ (Pediatrics)
			elseif ($MAJORCODE=='70008101') $EM_CODE = "70008101"; // กุมารเวชศาสตร์ โรคเลือด (Pediatric Haematology)
			elseif ($MAJORCODE=='70008102') $EM_CODE = "70008102"; // อนุสาขากุมารเวชศาสตร์ โรคหัวใจ (Pediatric Cardiology)
			elseif ($MAJORCODE=='70008103') $EM_CODE = "70008103"; // อนุสาขากุมารเวชศาสตร์ โรคไต (Pediatric Nephrology)
			elseif ($MAJORCODE=='70008104') $EM_CODE = "70008104"; // อนุสาขากุมารเวชศาสตร์ โรคระบบการหายใจ (Pediatric Respiratory Diseases)
			elseif ($MAJORCODE=='70008106') $EM_CODE = "70008106"; // อนุสาขากุมารเวชศาสตร์โรคติดเชื้อ (Pediatric Infectious Diseases)
			elseif ($MAJORCODE=='70008107') $EM_CODE = "70008107"; // อนุสาขากุมารเวชศาสตร์ทารกแรกเกิดและปริกำเนิด (Neonatal-Perinatal Medicine)
			elseif ($MAJORCODE=='70008108') $EM_CODE = "70008108"; // อนุสาขากุมารเวชศาสตร์โรคระบบทางเดินอาหารและโรคตับ (Pediatric Gastroenterology and Hepatology)
			elseif ($MAJORCODE=='70008109') $EM_CODE = "70008109"; // อนุสาขากุมารเวชศาสตร์ประสาทวิทยา (Pediatric Neurology)
			elseif ($MAJORCODE=='70008110') $EM_CODE = "70008110"; // อนุสาขากุมารเวชศาสตร์พัฒนาการและพฤติกรรม (Developmental and Behavioral Pediatrics)
			elseif ($MAJORCODE=='70008111') $EM_CODE = "70008111"; // อนุสาขากุมารเวชศาสตร์โรคภูมิแพ้และภูมิคุ้มกัน (Pediatric Allergy and Immumology)
			elseif ($MAJORCODE=='70009100') $EM_CODE = "70009100"; // ประสาทวิทยา (Neurology)
			elseif ($MAJORCODE=='70010100') $EM_CODE = "70010100"; // โรคติดเชื้อ
			elseif ($MAJORCODE=='70012100') $EM_CODE = "70012100"; // พยาธิวิทยา
			elseif ($MAJORCODE=='70012101') $EM_CODE = "70012101"; // พยาธิชีววิทยา
			elseif ($MAJORCODE=='70012102') $EM_CODE = "70012102"; // พยาธิวิทยาคลินิค (Clinical Pathology)
			elseif ($MAJORCODE=='70012103') $EM_CODE = "70012103"; // พยาธิวิทยาทั่วไป (Anatomical and Clinical Pathology)
			elseif ($MAJORCODE=='70012104') $EM_CODE = "70012104"; // พยาธิวิทยากายวิภาค (Anatomical Pathology)
			elseif ($MAJORCODE=='70012105') $EM_CODE = "70012105"; // พยาธิวิทยากายวิภาค(พยาธิวิทยา)
			elseif ($MAJORCODE=='70013100') $EM_CODE = "70013100"; // รังสีเทคนิค
			elseif ($MAJORCODE=='70013101') $EM_CODE = "70013101"; // รังสีวิทยา
			elseif ($MAJORCODE=='70013102') $EM_CODE = "70013102"; // วิทยาศาสตร์รังสี
			elseif ($MAJORCODE=='70013103') $EM_CODE = "70013103"; // รังสีรักษา
			elseif ($MAJORCODE=='70013104') $EM_CODE = "70013104"; // รังสีวิทยาทั่วไป (General Radiology)
			elseif ($MAJORCODE=='70013105') $EM_CODE = "70013105"; // รังสีวินิจฉัย
			elseif ($MAJORCODE=='70013106') $EM_CODE = "70013106"; // รังสีรักษาและเวชศาสตร์นิวเคลียร์ (Radiotherapy and Nuclear Medicine)
			elseif ($MAJORCODE=='70013107') $EM_CODE = "70013107"; // รังสีวิทยาวินิจฉัย (Diagnostic Radiology)
			elseif ($MAJORCODE=='70013108') $EM_CODE = "70013108"; // รังสีวิทยาและมะเร็งวิทยา (Therapeutic Radiology and Oncology)
			elseif ($MAJORCODE=='70014100') $EM_CODE = "70014100"; // วิสัญญีวิทยา
			elseif ($MAJORCODE=='70014101') $EM_CODE = "70014101"; // อนุสาขาวิสัญญีวิทยา สำหรับผู้ป่วยโรคทางระบบประสาท (Neuroanesthesia)
			elseif ($MAJORCODE=='70015102') $EM_CODE = "70015102"; // สรีรสิทยาของการออกกำลังกาย
			elseif ($MAJORCODE=='70017100') $EM_CODE = "70017100"; // ศัลยศาสตร์ (Surgery)
			elseif ($MAJORCODE=='70017101') $EM_CODE = "70017101"; // ศัลยศาสตร์ทั่วไป (Surgery)
			elseif ($MAJORCODE=='70017102') $EM_CODE = "70017102"; // ศัลยศาสตร์ตกแต่ง (Plastic Surgery)
			elseif ($MAJORCODE=='70017103') $EM_CODE = "70017103"; // ศัลยศาสตร์ทรวงอก (Thoracic Surgery)
			elseif ($MAJORCODE=='70017104') $EM_CODE = "70017104"; // อนุสาขาศัลยศาสตร์ลำไส้ใหญ่และทวารหนัก (Colorectal Surgery)
			elseif ($MAJORCODE=='70017106') $EM_CODE = "70017106"; // ศัลยศาสตร์ยูโรวิทยา (Urological Surgery)
			elseif ($MAJORCODE=='70017107') $EM_CODE = "70017107"; // ศัลยศาสตร์ออร์โธปิดิกส์ (Orthopedic Surgery)
			elseif ($MAJORCODE=='70017108') $EM_CODE = "70017108"; // อนุสาขาศัลยศาสตร์มะเร็งวิทยา (Surgical Oncology)
			elseif ($MAJORCODE=='70017109') $EM_CODE = "70017109"; // อนุสาขาศัลยศาสตร์ตกแต่งและเสริมสร้างใบหน้า (Facial Plastic and Reconstructive Surgery)
			elseif ($MAJORCODE=='70017110') $EM_CODE = "70017110"; // อนุสาขาศัลยศาสตร์อุบัติเหตุ (Trauma Surgery)
			elseif ($MAJORCODE=='70018100') $EM_CODE = "70018100"; // กุมารศัลยศาสตร์ (Pediatric Surgery) (4ปี)
			elseif ($MAJORCODE=='70020100') $EM_CODE = "70020100"; // ประสาทศัลยศาสตร์ (Neurological Surgery)
			elseif ($MAJORCODE=='70021100') $EM_CODE = "70021100"; // ประสาทวิทยาศาสตร์
			elseif ($MAJORCODE=='70023100') $EM_CODE = "70023100"; // ออร์โธปิดิกส์
			elseif ($MAJORCODE=='70024100') $EM_CODE = "70024100"; // จักษุวิทยา (Ophthalmology)
			elseif ($MAJORCODE=='70025100') $EM_CODE = "70025100"; // ตจวิทยา (Dermatology)
			elseif ($MAJORCODE=='70026100') $EM_CODE = "70026100"; // เวชศาสตร์ครอบครัว (Family Medicine)
			elseif ($MAJORCODE=='70028100') $EM_CODE = "70028100"; // อนุสาขามะเร็งนรีเวชวิทยา (Gynecologic Oncology)
			elseif ($MAJORCODE=='71000100') $EM_CODE = "71000100"; // เวชศาสตร์ช่องปาก
			elseif ($MAJORCODE=='71001101') $EM_CODE = "71001101"; // ทันตแพทยศาสตร์
			elseif ($MAJORCODE=='71001102') $EM_CODE = "71001102"; // ทันตกรรมจัดฟัน
			elseif ($MAJORCODE=='71001103') $EM_CODE = "71001103"; // ทันตกรรมประดิษฐ์
			elseif ($MAJORCODE=='71001104') $EM_CODE = "71001104"; // ทันตกรรมสำหรับเด็ก
			elseif ($MAJORCODE=='71001105') $EM_CODE = "71001105"; // ทันตกรรมหัตถการ
			elseif ($MAJORCODE=='71001107') $EM_CODE = "71001107"; // ทันตกรรม
			elseif ($MAJORCODE=='71001108') $EM_CODE = "71001108"; // ทันตกรรมทั่วไป
			elseif ($MAJORCODE=='71002100') $EM_CODE = "71002100"; // ปริทันตวิทยา
			elseif ($MAJORCODE=='71004100') $EM_CODE = "71004100"; // วิทยาเอนโดดอนต์
			elseif ($MAJORCODE=='71006100') $EM_CODE = "71006100"; // ทันตสาธารณสุข
			elseif ($MAJORCODE=='71008100') $EM_CODE = "71008100"; // ศัลยศาสตร์ช่องปากและแม็กซิลโลเฟเชียล
			elseif ($MAJORCODE=='71008101') $EM_CODE = "71008101"; // ศัลยศาสตร์ช่องปาก
			elseif ($MAJORCODE=='71010100') $EM_CODE = "71010100"; // ศัลยศาสตร์ช่องปาก (ทันตศัลยศาสตร์)
			elseif ($MAJORCODE=='73000100') $EM_CODE = "73000100"; // เภสัชวิทยา
			elseif ($MAJORCODE=='73001101') $EM_CODE = "73001101"; // เภสัชศาสตร์
			elseif ($MAJORCODE=='73001102') $EM_CODE = "73001102"; // เภสัชศาสตร์ชีวภาพ
			elseif ($MAJORCODE=='73001103') $EM_CODE = "73001103"; // เภสัชกรรม
			elseif ($MAJORCODE=='73001108') $EM_CODE = "73001108"; // เภสัชเคมี
			elseif ($MAJORCODE=='73001109') $EM_CODE = "73001109"; // เภสัชเวท
			elseif ($MAJORCODE=='73001112') $EM_CODE = "73001112"; // เภสัชกรรมคลินิก
			elseif ($MAJORCODE=='73001114') $EM_CODE = "73001114"; // บริหารเภสัชกิจ
			elseif ($MAJORCODE=='73001123') $EM_CODE = "73001123"; // เภสัชกรรมชุมชน
			elseif ($MAJORCODE=='73004100') $EM_CODE = "73004100"; // เทคนิคเภสัชกรรม
			elseif ($MAJORCODE=='74000100') $EM_CODE = "74000100"; // พยาบาลสาธารณสุข (NURSING)
			elseif ($MAJORCODE=='74001100') $EM_CODE = "74001100"; // พยาบาลศาสตร์
			elseif ($MAJORCODE=='74001101') $EM_CODE = "74001101"; // พยาบาล
			elseif ($MAJORCODE=='74001103') $EM_CODE = "74001103"; // การพยาบาลอายุรศาตร์ - ศัลยศาสตร์
			elseif ($MAJORCODE=='74001104') $EM_CODE = "74001104"; // พยาบาลและผดุงครรภ์
			elseif ($MAJORCODE=='74001107') $EM_CODE = "74001107"; // การพยาบาลผู้ป่วยโรคไร้เชื้อ (ระยะเวลา 3 เดือน)
			elseif ($MAJORCODE=='74001108') $EM_CODE = "74001108"; // ระบบหายใจ หัวใจและหลอดเลือด (ระยะเวลา 2 เดือน)
			elseif ($MAJORCODE=='74001109') $EM_CODE = "74001109"; // การพยาบาลโรคหัวใจและทรวงอก
			elseif ($MAJORCODE=='74001110') $EM_CODE = "74001110"; // การพยาบาลไตเทียม (ระยะเวลา 4 เดือน)
			elseif ($MAJORCODE=='74001111') $EM_CODE = "74001111"; // การพยาบาล ไอ.ซี.ยู (ระยะเวลา 2 เดือน)
			elseif ($MAJORCODE=='74001112') $EM_CODE = "74001112"; // การพยาบาลผู้ป่วยวิกฤติ (ระยะเวลา 3 เดือน)
			elseif ($MAJORCODE=='74001114') $EM_CODE = "74001114"; // พยาบาลเวชปฏิบัติทางโรคตา
			elseif ($MAJORCODE=='74001115') $EM_CODE = "74001115"; // พยาบาลทางโรคตา
			elseif ($MAJORCODE=='74001116') $EM_CODE = "74001116"; // การพยาบาลกุมารเวชศาสตร์ (โรคติดเชื้อ)
			elseif ($MAJORCODE=='74001117') $EM_CODE = "74001117"; // การพยาบาลศัลยกรรมอุบัติเหตุ
			elseif ($MAJORCODE=='74001118') $EM_CODE = "74001118"; // การพยาบาลอายุรศาสตร์
			elseif ($MAJORCODE=='74001119') $EM_CODE = "74001119"; // การพยาบาลผู้ป่วยบาดแผล ออสโตมี และควบคุมการขับถ่ายไม่ได้
			elseif ($MAJORCODE=='74001120') $EM_CODE = "74001120"; // การพยาบาลออร์โธปิดิกส์
			elseif ($MAJORCODE=='74002100') $EM_CODE = "74002100"; // การบริหารการพยาบาล
			elseif ($MAJORCODE=='74002101') $EM_CODE = "74002101"; // บริหารการพยาบาลศึกษา
			elseif ($MAJORCODE=='74003100') $EM_CODE = "74003100"; // การพยาบาลแม่และเด็ก
			elseif ($MAJORCODE=='74005100') $EM_CODE = "74005100"; // การพยาบาลผู้ใหญ่
			elseif ($MAJORCODE=='74006100') $EM_CODE = "74006100"; // การพยาบาลศึกษา
			elseif ($MAJORCODE=='74006101') $EM_CODE = "74006101"; // พยาบาลศึกษา
			elseif ($MAJORCODE=='74007100') $EM_CODE = "74007100"; // การพยาบาลครอบครัว
			elseif ($MAJORCODE=='74008100') $EM_CODE = "74008100"; // การพยาบาลผู้สูงอายุ
			elseif ($MAJORCODE=='74009100') $EM_CODE = "74009100"; // การพยาบาลชุมชน
			elseif ($MAJORCODE=='74009101') $EM_CODE = "74009101"; // การพยาบาลอนามัยชุมชน
			elseif ($MAJORCODE=='74010100') $EM_CODE = "74010100"; // การพยาบาลด้านการควบคุมการติดเชื้อ
			elseif ($MAJORCODE=='74011100') $EM_CODE = "74011100"; // การพยาบาลอาชีวอนามัย
			elseif ($MAJORCODE=='74013100') $EM_CODE = "74013100"; // การพยาบาลผู้ป่วยมะเร็ง
			elseif ($MAJORCODE=='74013101') $EM_CODE = "74013101"; // การพยาบาลเฉพาะทางโรคมะเร็งสำหรับพยาบาลวิชาชีพ
			elseif ($MAJORCODE=='74014100') $EM_CODE = "74014100"; // การพยาบาลสุขภาพจิตและจิตเวช
			elseif ($MAJORCODE=='75000100') $EM_CODE = "75000100"; // สาธารณสุข
			elseif ($MAJORCODE=='75001100') $EM_CODE = "75001100"; // บริหารสาธารณสุข
			elseif ($MAJORCODE=='75001101') $EM_CODE = "75001101"; // การบริหารสาธารณสุข
			elseif ($MAJORCODE=='75003100') $EM_CODE = "75003100"; // สุขาภิบาลสิ่งแวดล้อม
			elseif ($MAJORCODE=='75004100') $EM_CODE = "75004100"; // สังคมการแพทย์และสาธารณสุข
			elseif ($MAJORCODE=='75005100') $EM_CODE = "75005100"; // สาธารณสุขศาสตร์
			elseif ($MAJORCODE=='75008100') $EM_CODE = "75008100"; // อนามัยชุมชน
			elseif ($MAJORCODE=='75011101') $EM_CODE = "75011101"; // เวชศาสตร์ป้องกัน แขนงอาชีวเวชศาสตร์ (Preventive Medicine : Occupational Medicine)
			elseif ($MAJORCODE=='75013100') $EM_CODE = "75013100"; // อาชีวอนามัยและความปลอดภัย
			elseif ($MAJORCODE=='75015100') $EM_CODE = "75015100"; // สังคมศาสตร์การแพทย์และสาธารณสุข
			elseif ($MAJORCODE=='75016100') $EM_CODE = "75016100"; // การคุ้มครองผู้บริโภคทางสาธารณสุข
			elseif ($MAJORCODE=='75017100') $EM_CODE = "75017100"; // ชันสูตรสาธารณสุข
			elseif ($MAJORCODE=='75020100') $EM_CODE = "75020100"; // การส่งเสริมสุขภาพ
			elseif ($MAJORCODE=='75021100') $EM_CODE = "75021100"; // งานบริการฟื้นฟูสมรรถภาพคนพิการ
			elseif ($MAJORCODE=='76000100') $EM_CODE = "76000100"; // วิทยาศาสตร์การแพทย์
			elseif ($MAJORCODE=='76001100') $EM_CODE = "76001100"; // เซลล์วิทยา
			elseif ($MAJORCODE=='76005100') $EM_CODE = "76005100"; // โลหิตวิทยา (Haematology)
			elseif ($MAJORCODE=='76006100') $EM_CODE = "76006100"; // วิทยาการระบาด
			elseif ($MAJORCODE=='76006101') $EM_CODE = "76006101"; // วิทยาการระบาดทางการแพทย์
			elseif ($MAJORCODE=='76006102') $EM_CODE = "76006102"; // ระบาดวิทยา
			elseif ($MAJORCODE=='76008100') $EM_CODE = "76008100"; // โลหิตวิทยาและธนาคารเลือด
			elseif ($MAJORCODE=='76009100') $EM_CODE = "76009100"; // เวชศาสตร์การธนาคารเลือด
			elseif ($MAJORCODE=='76010100') $EM_CODE = "76010100"; // เวชศาสตร์ชุมชน
			elseif ($MAJORCODE=='76011100') $EM_CODE = "76011100"; // วิทยาศาสตร์การบริการโลหิต
			elseif ($MAJORCODE=='76012100') $EM_CODE = "76012100"; // จุลชีววิทยาทางการแพทย์
			elseif ($MAJORCODE=='76013100') $EM_CODE = "76013100"; // กายภาพบำบัด
			elseif ($MAJORCODE=='76014101') $EM_CODE = "76014101"; // ฟิสิกส์การแพทย์
			elseif ($MAJORCODE=='76017100') $EM_CODE = "76017100"; // กิจกรรมบำบัด
			elseif ($MAJORCODE=='76018100') $EM_CODE = "76018100"; // พิษวิทยา
			elseif ($MAJORCODE=='76019100') $EM_CODE = "76019100"; // พิษวิทยาทางอาหารและโภชนาการ
			elseif ($MAJORCODE=='76019101') $EM_CODE = "76019101"; // เวชนิทัศน์
			elseif ($MAJORCODE=='76021100') $EM_CODE = "76021100"; // เทคนิคการแพทย์
			elseif ($MAJORCODE=='77000100') $EM_CODE = "77000100"; // สุขศึกษาและพฤติกรรมศาสตร์
			elseif ($MAJORCODE=='77001100') $EM_CODE = "77001100"; // จิตวิทยาคลีนิค
			elseif ($MAJORCODE=='77002100') $EM_CODE = "77002100"; // สุขภาพจิต
			elseif ($MAJORCODE=='77002101') $EM_CODE = "77002101"; // เวชศาสตร์ป้องกัน แขนงสุขภาพจิตชุมชน (Preventive Medicine : Community Mental Health)
			elseif ($MAJORCODE=='77003100') $EM_CODE = "77003100"; // จิตวิทยาคลินิคและชุมชน
			elseif ($MAJORCODE=='77004100') $EM_CODE = "77004100"; // จิตเวชศาสตร์ (Psychiatry)
			elseif ($MAJORCODE=='77005100') $EM_CODE = "77005100"; // สุขภาพจิตและการพยาบาลจิตเวช
			elseif ($MAJORCODE=='77006100') $EM_CODE = "77006100"; // จิตเวชศาสตร์เด็กและวัยรุ่น (Child and Adolescent Psychiatry)
			elseif ($MAJORCODE=='77008100') $EM_CODE = "77008100"; // พลศึกษา
			elseif ($MAJORCODE=='77009100') $EM_CODE = "77009100"; // วิทยาศาสตร์สุขภาพ
			elseif ($MAJORCODE=='78001100') $EM_CODE = "78001100"; // นิติเวชศาสตร์ (Forensic Medicine)
			elseif ($MAJORCODE=='78002101') $EM_CODE = "78002101"; // การบริหารโรงพยาบาล
			elseif ($MAJORCODE=='78004100') $EM_CODE = "78004100"; // เวชระเบียน
			elseif ($MAJORCODE=='80001100') $EM_CODE = "80001100"; // ก่อสร้าง
			elseif ($MAJORCODE=='80001101') $EM_CODE = "80001101"; // ช่างก่อสร้าง
			elseif ($MAJORCODE=='80008100') $EM_CODE = "80008100"; // เทคโนโลยีการก่อสร้าง
			elseif ($MAJORCODE=='81006100') $EM_CODE = "81006100"; // วิศวกรรมไฟฟ้า
			elseif ($MAJORCODE=='81008100') $EM_CODE = "81008100"; // ช่างอิเล็กทรอนิกส์
			elseif ($MAJORCODE=='81008102') $EM_CODE = "81008102"; // อิเล็กทรอนิกส์
			elseif ($MAJORCODE=='81014100') $EM_CODE = "81014100"; // วิศวกรรมอิเล็กทรอนิกส์
			elseif ($MAJORCODE=='81015100') $EM_CODE = "81015100"; // ไฟฟ้า
			elseif ($MAJORCODE=='81015101') $EM_CODE = "81015101"; // ช่างไฟฟ้า
			elseif ($MAJORCODE=='81026100') $EM_CODE = "81026100"; // ไฟฟ้ากำลัง
			elseif ($MAJORCODE=='81026101') $EM_CODE = "81026101"; // ช่างไฟฟ้ากำลัง
			elseif ($MAJORCODE=='82000101') $EM_CODE = "82000101"; // ช่างกลโรงงาน
			elseif ($MAJORCODE=='82005100') $EM_CODE = "82005100"; // เครื่องกล
			elseif ($MAJORCODE=='83000100') $EM_CODE = "83000100"; // วิศวกรรมอุตสาหการ
			elseif ($MAJORCODE=='83001100') $EM_CODE = "83001100"; // เทคโนโลยีอุตสาหกรรม
			elseif ($MAJORCODE=='88001101') $EM_CODE = "88001101"; // ช่างยนต์
			elseif ($MAJORCODE=='89002100') $EM_CODE = "89002100"; // ช่างภาพ
			elseif ($MAJORCODE=='90000101') $EM_CODE = "90000101"; // เกษตรกรรม
			elseif ($MAJORCODE=='90004100') $EM_CODE = "90004100"; // เทคโนโลยีการเกษตร
			elseif ($MAJORCODE=='90006100') $EM_CODE = "90006100"; // ส่งเสริมการเกษตร
			elseif ($MAJORCODE=='92000101') $EM_CODE = "92000101"; // สัตวบาล
			elseif ($MAJORCODE=='92000102') $EM_CODE = "92000102"; // สัตวศาสตร์
			elseif ($MAJORCODE=='94000104') $EM_CODE = "94000104"; // ประมง
			elseif ($MAJORCODE=='95000101') $EM_CODE = "95000101"; // วนศาสตร์
			elseif ($MAJORCODE=='98002101') $EM_CODE = "98002101"; // ไม่ระบุสาขาวิชาเอก
			$EM_CODE = (trim($EM_CODE))? "'".trim($EM_CODE)."'" : "NULL";
*/
?>