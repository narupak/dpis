<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$UPDATE_DATE = date("Y-m-d H:i:s");
  	if(strtoupper($dpisdb_user)=="SARABUN")  $where = "";
  	elseif(strtoupper($dpisdb_user)=="BMA")  $where = "and PER_TYPE != 1";
	else $where = "xxxxxxxxxxxxxxx";
	if ($search_org_id) {
		$where_per_id = " (SELECT PER_ID FROM PER_PERSONAL a, PER_POSITION b WHERE a.POS_ID = b.POS_ID and 
											b.DEPARTMENT_ID = $search_department_id $where and b.ORG_ID = $search_org_id) ";
	} else {
		$where_per_id = " (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id $where) ";
	}

	switch($CTRL_TYPE){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			break;
	} // end switch case

	if( $command=='ORG' ) { // โครงสร้างส่วนราชการ  
/*		$cmd = " create index idx_per_org_department on per_org (department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " create index idx_per_position_department on per_position (department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " create index idx_per_pos_emp_department on per_pos_emp (department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " create index idx_per_pos_empser_department on per_pos_empser (department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " create index idx_per_pos_temp_department on per_pos_temp (department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " create index idx_per_personal_department on per_personal (department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error(); */

		$table = array(	"per_positionhis", "per_salaryhis", "per_extrahis", "per_educate", "per_training", "per_ability", "per_special_skill", "per_heir", 
									"per_absenthis", "per_punishment", "per_servicehis", "per_rewardhis", "per_marrhis", "per_namehis", "per_decoratehis", 
									"per_timehis", "per_extra_incomehis", "per_personalpic", "per_comdtl", "per_salpromote", "per_coursedtl", "per_decordtl", 
									"per_promote_p", "per_move_req", "per_absent", "per_scholar", "per_family", 
									"per_personal" );

		for ( $i=0; $i<count($table); $i++ ) { 
			$cmd = " delete from $table[$i] where per_id in $where_per_id ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
		
			$cmd = "  delete from $table[$i] where per_id in (SELECT PER_ID FROM PER_PERSONAL WHERE POS_ID IN (SELECT POS_ID FROM PER_POSITION WHERE DEPARTMENT_ID = $search_department_id)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			if ($table[$i]=="per_servicehis") {	
				$cmd = " delete from per_servicehis where per_id_assign in (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id) ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			}

			if ($table[$i]=="per_scholar") {	
				$cmd = " delete from per_scholarinc where sc_id in (select sc_id from per_scholar where per_id in $where_per_id) ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();

				$cmd = " delete from per_scholar where per_id in $where_per_id ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			}
		} // end for
		$cmd = " delete from per_servicehis 
						where org_id in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_pos_emp 
						where org_id_1 in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_pos_emp 
						where org_id_2 in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_pos_empser 
						where org_id_1 in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_pos_empser 
						where org_id_2 in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_pos_temp 
						where org_id_1 in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_pos_temp 
						where org_id_2 in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_move_req 
						where org_id_1 in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_move_req 
						where org_id_2 in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_move_req 
						where org_id_3 in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_pos_move 
						where org_id in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_salquotadtl2 
						where org_id in (SELECT ORG_ID FROM PER_ORG_ASS WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_salquotadtl1 
						where org_id in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_salquota where DEPARTMENT_ID = $search_department_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_bonusquotadtl2 
						where org_id in (SELECT ORG_ID FROM PER_ORG_ASS WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_bonusquotadtl1 
						where org_id in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_bonusquota where DEPARTMENT_ID = $search_department_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " delete from per_command where DEPARTMENT_ID = $search_department_id ";
		$db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();

		$cmd = " DELETE FROM PER_PERSONAL WHERE POS_ID IN (SELECT POS_ID FROM PER_POSITION WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_POSITION WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_POS_EMP WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_POS_EMPSER WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_POS_TEMP WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL10 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL9 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL8 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL7 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL6 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL5 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL4 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL3 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL2 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM_DTL1 WHERE SUM_ID IN (SELECT SUM_ID FROM PER_SUM  
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_SUM 
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " UPDATE PER_POSITIONHIS SET ORG_ID_3 = 1 
						WHERE ORG_ID_3 IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;
		
		$cmd = " DELETE FROM PER_KPI 
						WHERE ORG_ID IN (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_KPI WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_ORG WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();

	} // end if

?>