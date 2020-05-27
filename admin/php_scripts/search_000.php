<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	

	// ==== use for testing phase =====
	if($table=="PER_GROUP_N"){
		$DPISDB = "mysql";
		$db_dpis = $db;
	} // end if
	// ==========================

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

	$cmd = " select * from $table ";
	$db_dpis->send_cmd($cmd);
	$field_list = $db_dpis->list_fields($table);
//	echo "$cmd -- field_list=".implode(",",implode("|",$field_list))."<br>";
//	foreach($field_list as $key => $value) {
//		echo "$key>".$value[name].">";
//		foreach($value as $key1 => $value1) {
//			echo "$key1($value1),";
//		}
//		echo " ... ";
//	}
//	echo "||<br>";
	unset($arr_fields);
	unset($arr_fldtyp);
	unset($arr_fldlen);

	if($DPISDB=="odbc" || $DPISDB=="oci8"){
		for($i=1; $i<=count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
			$arr_fldtyp[] = $field_list[$i]["type"];
			$arr_fldlen[] = $field_list[$i]["len"];
		endfor;
	}elseif($DPISDB=="mysql"){
		for($i=0; $i<count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
			$arr_fldtyp[] = $field_list[$i]["type"];
			$arr_fldlen[] = $field_list[$i]["len"];
		endfor;
	} // end if
//	echo ">>".(implode(", ", $arr_fields))."||".(implode(", ", $arr_fldtyp))."<br>";

	$UPDATE_DATE = date("Y-m-d H:i:s");
	if (!$$arr_fields[$seq_idx]) $$arr_fields[$seq_idx] = "NULL";

//	echo "1.1..LG_CODE=$LG_CODE, LG_NAME=$LG_NAME UPD=$UPD DEL=$DEL err_text=$err_text<br>";
	if($UPD){
		$sel_idx = $arr_fields[$key_index]."=".(strpos($arr_fldtyp[$key_index],"CHAR")!==false ? "'".trim($$arr_fields[$key_index])."'" : $$arr_fields[$key_index]);
//		if($table=="PER_PROJECT_GROUP" || $table=="PER_PROJECT_PAYMENT") {  $cmd = " select $arr_fields[1], $arr_fields[2], $arr_fields[5] from $table where $arr_fields[0]=".$$arr_fields[0]." "; }
//		else { $cmd = " select $arr_fields[1], $arr_fields[2], $arr_fields[5] from $table where $arr_fields[0]='".$$arr_fields[0]."' "; }
		$cmd = " select * from $table where ".$sel_idx." ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$arr_head_control_index = explode(",",$head_control_index);
		for($i=0; $i<count($arr_fields); $i++) {
			if ($arr_fields[$i]!="UPDATE_USER" && $arr_fields[$i]!="UPDATE_DATE") {
				$$arr_fields[$i] = trim($data[$arr_fields[$i]]);
				$idx = array_search($i,$arr_head_control_index);	// ตรวจสอบว่าเป็น key นอก
				if ($idx !== false) {	// แสดงว่าเป็น key นอก
					$arr_head_control_tab = explode("|",$head_control_tab[$idx]);
					$column_control_code = explode(",",$arr_head_control_tab[2]);
					$column_control_name = explode(",",$arr_head_control_tab[3]);
		            if ($arr_head_control_tab[4]) $where1 = "where ".$arr_head_control_tab[4]; else $where1 = "";
					$cmd1 = " select * from ".$arr_head_control_tab[0]." $where1";
                    $cmd1 = str_replace("var1", $$column_control_code[1], $cmd1);
					$cnt = $db_dpis1->send_cmd($cmd1);
//					echo "cmd1=$cmd1 ($cnt)<br>";
					$data1 = $db_dpis1->get_array();
//					echo "code=".(implode("=>",$column_control_code)).", name=".(implode("=>",$column_control_name))."<br>";
					if (count($column_control_code) > 1)
						$$column_control_code[1] = $data1[$column_control_code[0]];
					else
						${"CHANGE_".$column_control_code[0]} = $data1[$column_control_code[0]];
					if (count($column_control_name) > 1)
						$$column_control_name[1] = $data1[$column_control_name[0]];
					else
						${"CHANGE_".$column_control_name[0]} = $data1[$column_control_name[0]];
//					echo "code--".$column_control_code[1]."=".$$column_control_code[1]." , ".$column_control_name[1]."=".$$column_control_name[1]."<br>";
//					echo "cmd=$cmd1..CHANGE-->2..".$column_control_code[0]."-->3..".$column_control_name[0]."<br>";
				} else if (trim($data[$arr_fields[$i]])=='NULL' || is_null($data[$arr_fields[$i]])) {
					$$arr_fields[$i] = "";
				}
			}
		}
	} // end if
	
//	echo "1.2..LG_CODE=$LG_CODE, LG_NAME=$LG_NAME UPD=$UPD DEL=$DEL err_text=$err_text<br>";
	if( (!$UPD && !$DEL && !$err_text) ){
		$arr_head_control_index = explode(",",$head_control_index);
		for($i=0; $i<count($arr_fields); $i++) {
			if ($arr_fields[$i]!="UPDATE_USER" && $arr_fields[$i]!="UPDATE_DATE") {
				if ($i==$active_index) {
					$$arr_fields[$i] = 1;
				} else if ($i==$seq_order_index) {
					$$arr_fields[$i] = 0;
				} else {
					$idx = array_search($i,$arr_head_control_index);	// ตรวจสอบว่าเป็น key นอก
					if ($idx !== false) {	// แสดงว่าเป็น key นอก
						$arr_head_control_tab = explode("|",$head_control_tab[$idx]);
						$column_control_code = explode(",",$arr_head_control_tab[2]);
						$column_control_name = explode(",",$arr_head_control_tab[3]);
//						echo "code=".(implode("=>",$column_control_code)).", name=".(implode("=>",$column_control_name))."<br>";
						if (count($column_control_code) > 1)
							$$column_control_code[1] = "";
						else {
							${"CHANGE_".$column_control_code[0]} = "";
//							$$column_control_code[0] = "";
						}
						if (count($column_control_name) > 1)
							$$column_control_name[1] = "";
						else {
							${"CHANGE_".$column_control_name[0]} = "";
//							$$column_control_name[0] = "";
						}
	//					echo "cmd=$cmd1..CHANGE-->2..".$column_control_code[0]."-->3..".$column_control_name[0]."<br>";
					} else {
						$$arr_fields[$i] = "";
//						echo "$i--".$arr_fields[$i]."=".$$arr_fields[$i]."<br>";
					}
				}
			}
		}	
//		$$arr_fields[0] = "";
//		$$arr_fields[1] = "";
//		$$arr_fields[2] = 1;
//		$$arr_fields[5] = 0;
	} // end if
//	echo "1.3..LG_CODE=$LG_CODE, LG_NAME=$LG_NAME<br>";
?>