<?php 

	//error_reporting(E_ALL);
	include("php_scripts/lax_ldap_function.php");


	function sent_mail() {
	}
	
	function add_user($_user_array,$default_group=1) {
		global $db;

		$db->select_db('DPIS35');		
		$sql = "select id from user_detail where username = '".$_user_array['1']."'";
		$db->send_cmd($sql);
		//echo $db->count;
		if(!$db->count) {
			$sql = "select max(id) from user_detail";
			$db->send_cmd($sql);
			$data = $db->get_array();
			$max_id = $data['0'] + 1;
			$sql = "insert into user_detail (id,group_id,username,fullname,email,update_date,create_by) 
			values ($max_id,'".$default_group."','".$_user_array['1']."','".$_user_array['2']."','".$_user_array['3']."',NOW(),'LDAP')";
			$db->send_cmd($sql);
			//echo $sql . "<br>";	
			return 1;
		} else {
			echo "Dup => " . $_user_array['1'];
			return 0;
		}
	}

	function showuserldap($page=1) {
		global $ldap_db,$_per_page,$nav_paging;
		
		$ldap_db->select_db('notebookstyle');		
		
		if($page < 1) $page = 1;
		$start_row = ($page - 1 ) * $_per_page;
		$sql = "select 
						id,username,fullname,email 
					from 
						ldap_user 
					order by 
						fullname ";
		$ldap_db->send_cmd($sql);
		$allpage = ceil(($ldap_db->count)/$_per_page);
		//$nav_paging = create_paging($allpage,$page,'',"php_scripts/lax_ldap_search.php");
		
		//echo $nav_paging;
		
		$sql = "select 
						id,username,fullname,email 
					from 
						ldap_user 
					order by 
						fullname 
					limit $start_row,$_per_page";
		$ldap_db->send_cmd($sql);
		$count = 0;
		while($row = $ldap_db->get_array()) {
			$userldap[$count] = $row;
			$count++;
		}
		return $userldap;
	}
	
	function get_user_ldap($user_set) {
		global $ldap_db;
		
		$ldap_db->select_db('notebookstyle');
		if($user_set != 'All') $user_set = " and id in ($user_set) ";
		else $user_set = "";
		$sql = "select
						id,username,fullname,email 
					from 
						ldap_user 
					where 1 $user_set";
		$ldap_db->send_cmd($sql);
		
		while($row = $ldap_db->get_array()) {
			$return[$count] = $row;
			$count++;
		}
		return $return;
	}

	function showuserdpis($page=1) {
		global $db,$_per_page;
		
		$db->select_db('DPIS35');

		if($page < 1) $page = 1;
		$start_row = ($page - 1 ) * $_per_page;
		$sql = "select 
						id,group_id,username,fullname,email,create_by 
					from 
						user_detail 
					where 
						create_by = 'LDAP'
					order by 
						create_by,fullname 
					limit $start_row,$_per_page";
		$db->send_cmd($sql);
		$count = 0;
		while($row = $db->get_array()) {
			$userdpis[$count] = $row;
			$count++;
		}
		
		return $userdpis;
	}
	
	function showusergroup() {
		global $db;
		
		$db->select_db('DPIS35');
		$sql = "select *	from 	user_group order by name_th";
		$count_row = $db->query($sql);
		$count = 0;
		while($row = $db->get_array()) {
			$rs[$count] = $row;
			//echo $row['name_th'];
			$count++;
		}
//		echo "<pre>";
//		print_r($rs);
//		echo "</pre>";
		return $rs;
	}

	function delete_user_dpis($delete_set) {
		global $db; 
		
		$db->select_db('DPIS35');
		
		$sql = "delete from user_detail where id in ($delete_set)";
		$db->send_cmd($sql);
		
		return 1;
	}
	
	function del_diff_user($delete_set) {
		global $db; 
		
		$db->select_db('DPIS35');
		
		$sql = "delete from user_detail where username not in ('$delete_set') and create_by = 'LDAP'";
		$db->send_cmd($sql);
		
		return 1;
	}

	function update_user_dpis($ldap_set,$action) {
		global $db,$_default_group; 
		
		$db->select_db('DPIS35');
		if($action == 'changepassword') {
			$sql = "update user_detail set password = md5('admin') where id in ($ldap_set)";	
		} elseif($action == 'updateuser') {
			$sql = "update user_detail set group_id= '$_default_group' where id in ($ldap_set)";	
		}
		//echo $sql;
		$db->send_cmd($sql);
		
		return 1;
	}

	$ldap_db = new connect_db("localhost", "notebookstyle", $db_user, $db_pwd);
	$_per_page = 10;
	$_default_group = 2;
	switch ($_POST['command']) {
		case "AddUser" : case "AddAllUser" :
			$_user_str = substr(stripslashes($_POST['ldapdata']),1);
			$_user_array = get_user_ldap($_user_str);
			foreach($_user_array as $row) {	
				add_user($row,$_default_group);
			}
			break;
		case "DelDiffUser" :
			// get all user from dpis
			$_user_array = get_user_ldap('All');
			foreach($_user_array as $row) {
				$_user_ldap_array[] = $row['username'];
			}
			$_user_ldap_set = implode("','",$_user_ldap_array);
			del_diff_user($_user_ldap_set);
			break;
		case "DelUser" :
			delete_user_dpis(substr($_POST['deleteid'],1));
			break;
		case "ChangePassword" :
			update_user_dpis(substr($_POST['ldapdata'],1),'changepassword');
			break;
		case "UpdateUser" :
			update_user_dpis(substr($_POST['ldapdata'],1),'updateuser');
			break;		
	}

	$userdpis_rs = showuserdpis($_POST['page']);
	$userldap_rs = showuserldap($_POST['page']);
	$usergroup_rs = showusergroup();
//	echo $nav_paging;		

?>