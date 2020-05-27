 <?	
	function get_arr_person($t_org_id, $tree_depth) {
		global $DPISDB, $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;

		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		if ($tree_depth==1) $a = "c.ORG_ID = $t_org_id and i.ORG_NAME != '-' and c.ORG_ID_1 is null and c.ORG_ID_2 is null";
		else if ($tree_depth==2) $a = "c.ORG_ID_1 = $t_org_id and i.ORG_NAME != '-' and c.ORG_ID_2 is null";
		else if ($tree_depth==3) $a = "c.ORG_ID_2 = $t_org_id and i.ORG_NAME != '-'";
		else $a = "c.DEPARTMENT_ID = $t_org_id and c.ORG_ID is null and c.ORG_ID_1 is null and i.ORG_NAME != '-' and c.ORG_ID_2 is null";
//		if ($tree_depth==1) $a = "c.ORG_ID = $t_org_id and c.ORG_ID_1 is null and c.ORG_ID_2 is null";
//		else if ($tree_depth==2) $a = "c.ORG_ID_1 = $t_org_id and c.ORG_ID_2 is null";
//		else if ($tree_depth==3) $a = "c.ORG_ID_2 = $t_org_id";
//		else $a = "c.DEPARTMENT_ID = $t_org_id and c.ORG_ID is null and c.ORG_ID_1 is null and c.ORG_ID_2 is null";
//		echo "tree_depth=$tree_depth ($a)<br>";

		/**** อ่าน ข้าราชการเมื่อ ถึงหน่วยย่อยสุด ****/
		if($DPISDB=="odbc"){
			$cmd = " select 	a.PER_ID, a.PN_CODE, PER_NAME, PER_SURNAME, i.ORG_ID, i.ORG_NAME, c.POS_ID
							 from (
										(
											(
												(
													(
														PER_PERSONAL a
														left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
													) 	left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
												)	left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID) 
											) 	left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
										) 	left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
									) 	left join PER_ORG i on (c.ORG_ID=i.ORG_ID)
							 where    $a and a.PER_TYPE=1 and a.PER_STATUS=1
							 order by PER_NAME, PER_SURNAME ";
		}elseif($DPISDB=="oci8"){
			$cmd = "select 		a.PER_ID, a.PN_CODE, PER_NAME, PER_SURNAME, i.ORG_ID, i.ORG_NAME, c.POS_ID
							from 		PER_PERSONAL a, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, PER_LEVEL f, PER_POS_TEMP g, PER_ORG i
							where 	a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+) and a.LEVEL_NO=f.LEVEL_NO(+) and 
											a.POT_ID=g.POT_ID(+) and c.ORG_ID=i.ORG_ID(+)
											and $a and a.PER_TYPE=1 and a.PER_STATUS=1
							order by 	PER_NAME, PER_SURNAME  ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	a.PER_ID, a.PN_CODE, PER_NAME, PER_SURNAME, i.ORG_ID, i.ORG_NAME, c.POS_ID
							 from (
										(
											(
												(	
													(
														PER_PERSONAL a
														left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
													) 	left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
												)	left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID) 
											) 	left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
										) 	left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
									) 	left join PER_ORG i on (c.ORG_ID=i.ORG_ID)
							 where $a and a.PER_TYPE=1 and a.PER_STATUS=1
							 order by PER_NAME, PER_SURNAME ";
		}
		$count_person = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "read person.....$cmd ($count_person) [tree_depth=$tree_depth]<br>";
		$data_count = 0;
		$arr_content = (array) null;
		if ($count_person) {
			while ($data = $db_dpis->get_array()) {
				$arr_content[$data_count][PER_ID] = trim($data[PER_ID]);
				$arr_content[$data_count][PN_CODE] = trim($data[PN_CODE]);
				$arr_content[$data_count][PER_NAME] = trim($data[PER_NAME]);
				$arr_content[$data_count][PER_SURNAME] = trim($data[PER_SURNAME]);
				$arr_content[$data_count][ORG_ID] = trim($data[ORG_ID]);
				$arr_content[$data_count][ORG_NAME] = trim($data[ORG_NAME]);
				$arr_content[$data_count][POS_ID] = trim($data[POS_ID]);
				$data_count++;
			}
		} // end if ($count_person)

		return $arr_content;		
	} // end function
	
	function find_org_dept($ORG_ID) {
		global $DPISDB, $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $ORGTAB;

		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		$depth = 0;
		if ($ORG_ID) {
			$cmd = " select ORG_ID, ORG_ID_REF from $ORGTAB where ORG_ID = $ORG_ID ";
			$count_data = $db_dpis->send_cmd($cmd);
	//		echo "org_depth...$cmd ($count_data)<br>";
			if ($count_data > 0){
				$data = $db_dpis->get_array();
	//			echo " data[ORG_ID_REF](".$data[ORG_ID_REF].") != ORG_ID($ORG_ID)<br>";
				if ($data[ORG_ID_REF] != $ORG_ID)
					$depth = find_org_dept($data[ORG_ID_REF]);
				$depth++;
			}
		}
				
		return $depth;
	}

	function get_org_2top($ORG_ID, $cut_flag=false) {
		global $DPISDB, $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $ORGTAB;

		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		$cmd = " select ORG_ID, ORG_NAME, ORG_ID_REF from $ORGTAB where ORG_ID = $ORG_ID ";
		$count_data = $db_dpis->send_cmd($cmd);
//		echo "org_depth...$cmd ($count_data)<br>";
		$orglist  = "";
		if ($count_data > 0){
			$data = $db_dpis->get_array();
//			echo " data[ORG_ID_REF](".$data[ORG_ID_REF].") != ORG_ID($ORG_ID)<br>";
			if ($data[ORG_ID_REF] != $ORG_ID || $ORG_ID > 1)
				$orglist = get_org_2top($data[ORG_ID_REF]);
			// ถ้า ใส่ $cut flag มา และ org name คือ "-" 
			if (!($cut_flag && $data[ORG_NAME]=="-"))	// ถ้า ไม่ (cut flag และ name คือ "-")
				$orglist .= ($orglist ? "," : "").$ORG_ID;
//			$orglist .= ($orglist ? "," : "").$ORG_ID."|".$data[ORG_NAME];
//			echo "ORG_ID=$ORG_ID , orglist=$orglist<br>";
		}
				
		return $orglist;
	}

?>