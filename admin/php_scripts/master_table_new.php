<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");

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

	if($command=="REORDER"){
		foreach($ARR_ORDER as $CODE => $SEQ_NO){
			if($SEQ_NO=="") { $cmd = " update $table set $arr_fields[$seq_order_index]='' where $arr_fields[$key_index]='$CODE' "; }
			else {	$cmd = " update $table set $arr_fields[$seq_order_index]=$SEQ_NO where $arr_fields[$key_index]='$CODE' "; }
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<br>".$cmd;
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับ [$CODE : $SEQ_NO]");
	} // end if

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
//		echo "$setflagshow";
		$cmd = " update $table set $arr_fields[$active_index] = 0 where $arr_fields[$key_index] in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$cmd = " update $table set $arr_fields[$active_index] = 1 where $arr_fields[$key_index] in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}
	
	if($command == "ADD"){
		if(!trim($$arr_fields[$key_index])) {
			$cmd = " select max(".$arr_fields[$key_index].") as max_key from $table ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			${"new_".$arr_fields[$key_index]} = $data[max_key]+1;
//			echo "1.."."new_".$arr_fields[$key_index]."==".${"new_".$arr_fields[$key_index]}." ($cmd)<br>";
		} else {
//			$add_idx = $arr_fields[$key_index]."=".(strtolower(substr($col_spec_datatype[$i],0,4))=="date" ? "'".save_date(trim($$arr_fields[$key_index]))."'" : (strpos($arr_fldtyp[$key_index],"CHAR")!==false ? "'".trim($$arr_fields[$key_index])."'" : trim($$arr_fields[$key_index])));
			if (strpos($arr_fldtyp[$key_index],"CHAR")!==false)	// ถ้าเป็นประเภท char
				if ( $arr_fldlen[$key_index]==19 && valid_char19_date_ddmmyyyy(trim($$arr_fields[$key_index]))!="NO" )
					$add_idx = $arr_fields[$key_index]."='".save_date(trim($$arr_fields[$key_index]))."'";
				else
					$add_idx = $arr_fields[$key_index]."='".trim($$arr_fields[$key_index])."'";
			else if ( trim($$arr_fields[$key_index]) )
				$add_idx = $arr_fields[$key_index]."=".trim($$arr_fields[$key_index]);
			$cmd = " select $arr_fields[$key_index], $arr_fields[$name_index] from $table where ". $add_idx ." ";
			$count_duplicate = $db_dpis->send_cmd($cmd);
			if($count_duplicate <= 0){	// ไม่ duplicate
				${"new_".$arr_fields[$key_index]} = $$arr_fields[$key_index];
			} else {
				${"new_".$arr_fields[$key_index]} = "";
			}
		}
		if (trim(${"new_".$arr_fields[$key_index]})) {
			$add_value = (array) null;
			for($i=0; $i<count($arr_fields); $i++) {
				if ($arr_fields[$i]=="UPDATE_USER")
					$add_value[] = $SESS_USERID;
				else if ($arr_fields[$i]=="UPDATE_USER")
					$add_value[] = "'".$UPDATE_DATE."'";
				else if ($i==$key_index) {
//					echo "add INDEX-i=$i-->".strtolower(substr($col_spec_datatype[$i],0,4))."<br>";
					if (strtolower(substr($col_spec_datatype[$i],0,4))=="date")
						$aa = "'".save_date(trim(${"new_".$arr_fields[$i]}))."'";
					else if (strpos($arr_fldtyp[$i],"CHAR")!==false)
						if ( $arr_fldlen[$i]==19 && valid_char19_date_ddmmyyyy(trim(${"new_".$arr_fields[$i]}))!="NO" )
							$aa = "'".save_date(trim(${"new_".$arr_fields[$i]}))."'";
						else
							$aa = "'".trim(${"new_".$arr_fields[$i]})."'";
					else $aa = ${"new_".$arr_fields[$i]};
					$add_value[] = $aa;
				} else {
//					echo "$i - arr_fields [$i]=".$arr_fields[$i]."<br>";
					if ( isset(${"CHANGE_".$arr_fields[$i]}) ) {
//						echo "add CHANGE-i=$i-->".strtolower(substr($col_spec_datatype[$i],0,4))."<br>";
						if ( trim(${"CHANGE_".$arr_fields[$i]}) )
							if (strtolower(substr($col_spec_datatype[$i],0,4))=="date")
								$aa = "'".save_date(trim(${"CHANGE_".$arr_fields[$i]}))."'";
							else if (strpos($arr_fldtyp[$i],"CHAR")!==false)
								if ( $arr_fldlen[$i]==19 && valid_char19_date_ddmmyyyy(trim(${"CHANGE_".$arr_fields[$i]}))!="NO" )
									$aa = "'".save_date(trim(${"CHANGE_".$arr_fields[$i]}))."'";
								else
									$aa = "'".trim(${"CHANGE_".$arr_fields[$i]})."'";
							else $aa = trim(${"CHANGE_".$arr_fields[$i]});
						else
							$aa = strpos($arr_fldtyp[$i],"CHAR")!==false ? "''" : 0;
						$add_value[] = $aa;
//						echo "$i=CHANGE_".$arr_fields[$i]." (".trim(${"CHANGE_".$arr_fields[$i]}).")<br>";
					} else if ( isset($$arr_fields[$i]) ) {		// if ( trim( $$arr_fields[$i] ) )
//						echo "add -i=$i-->".strtolower(substr($col_spec_datatype[$i],0,4))."<br>";
						if (trim($$arr_fields[$i]))
							if (strtolower(substr($col_spec_datatype[$i],0,4))=="date")
								$aa = "'".save_date(trim($$arr_fields[$i]))."'";
							else if (strpos($arr_fldtyp[$i],"CHAR")!==false)
								if ( $arr_fldlen[$i]==19 && valid_char19_date_ddmmyyyy(trim($$arr_fields[$i]))!="NO" )
									$aa = "'".save_date(trim($$arr_fields[$i]))."'";
								else
									$aa = "'".trim($$arr_fields[$i])."'";
							else $aa = trim($$arr_fields[$i]);
						else
							$aa = strpos($arr_fldtyp[$i],"CHAR")!==false ? "''" : 0;
//						echo "$i=".$arr_fields[$i]." (".trim($$arr_fields[$i]).")<br>";
						$add_value[] = $aa;
					} else {
						$add_value[] = strpos($arr_fldtyp[$i],"CHAR")!==false ? "''" : 0;
					}
				}
			}
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") values (".implode(", ", $add_value).") ";
			$db_dpis->send_cmd($cmd);
//			echo "insert-$cmd<br>";
//			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($$arr_fields[$key_index])." : ".$$arr_fields[$name_index]."]");
		}else{
			$data = $db_dpis->get_array();
			$err_text = "รหัสข้อมูลซ้ำ [".$data[$arr_fields[$key_index]]." ".$data[$arr_fields[$name_index]]."]";
		} // endif
	}

	if ($command == "UPDATE" && trim($$arr_fields[$key_index])) {
		$upd_value = (array) null;
		$upd_idx = "";
		for($i=0; $i<count($arr_fields); $i++) {
			if ($i != $key_index && $i != $seq_order_index) {
//				echo "arr_fields [$i]=".$arr_fields[$i]." (issetCHANGE_==>".isset(${"CHANGE_".$arr_fields[$i]}).") (isset=>".isset($$arr_fields[$i]).") if (".($arr_fields[$i]=="UPDATE_DATE").")<br>";
				if ($arr_fields[$i]=="UPDATE_USER") {
					$upd_value[] = "".$arr_fields[$i]."=".$SESS_USERID;
				} else if ($arr_fields[$i]=="UPDATE_DATE") {
					$upd_value[] = "".$arr_fields[$i]."='".$UPDATE_DATE."'";
				} else {
					if ( isset(${"CHANGE_".$arr_fields[$i]}) ) {
//						echo "upd CHANGE-i=$i-->".strtolower(substr($col_spec_datatype[$i],0,4))."<br>";
						if (strtolower(substr($col_spec_datatype[$i],0,4))=="date")
							$upd_value[] = $arr_fields[$i]."='".save_date(trim(${"CHANGE_".$arr_fields[$i]}))."'";
						else if (strpos($arr_fldtyp[$i],"CHAR")!==false)	// ถ้าเป็นประเภท char
							if ( $arr_fldlen[$i]==19 && valid_char19_date_ddmmyyyy(trim(${"CHANGE_".$arr_fields[$i]}))!="NO" )
								$upd_value[] = $arr_fields[$i]."='".save_date(trim(${"CHANGE_".$arr_fields[$i]}))."'";
							else
								$upd_value[] = $arr_fields[$i]."='".trim(${"CHANGE_".$arr_fields[$i]})."'";
						else if ( trim(${"CHANGE_".$arr_fields[$i]}) )
							$upd_value[] = $arr_fields[$i]."=".trim(${"CHANGE_".$arr_fields[$i]});
//						echo "$i=CHANGE_".$arr_fields[$i]." (".trim(${"CHANGE_".$arr_fields[$i]}).")<br>";
					} else if ( isset($$arr_fields[$i]) ) {		// if ( trim( $$arr_fields[$i] ) )
//						echo "upd -i=$i-->".strtolower(substr($col_spec_datatype[$i],0,4))."<br>";
						if (strtolower(substr($col_spec_datatype[$i],0,4))=="date")
							$upd_value[] = $arr_fields[$i]."='".save_date(trim($$arr_fields[$i]))."'";
						else if (strpos($arr_fldtyp[$i],"CHAR")!==false)	// ถ้าเป็นประเภท char
							if ( $arr_fldlen[$i]==19 && valid_char19_date_ddmmyyyy(trim($$arr_fields[$i]))!="NO" )
								$upd_value[] = $arr_fields[$i]."='".save_date(trim($$arr_fields[$i]))."'";
							else
								$upd_value[] = $arr_fields[$i]."='".trim($$arr_fields[$i])."'";
						else if ( trim($$arr_fields[$i]) )
							$upd_value[] = $arr_fields[$i]."=".trim($$arr_fields[$i]);
//						echo "$i=".$arr_fields[$i]." (".trim($$arr_fields[$i]).")<br>";
					}
				}
			} else {
				if ($i == $key_index)
//					echo "upd INDEX-i=$i-->".strtolower(substr($col_spec_datatype[$i],0,4))."<br>";
					if (strtolower(substr($col_spec_datatype[$i],0,4))=="date")
						$upd_idx = $arr_fields[$i]."='".save_date(trim($$arr_fields[$i]))."'";
					else if (strpos($arr_fldtyp[$i],"CHAR")!==false)	// ถ้าเป็นประเภท char
						if ( $arr_fldlen[$i]==19 && valid_char19_date_ddmmyyyy(trim($$arr_fields[$i]))!="NO" )
							$upd_idx = $arr_fields[$i]."='".save_date(trim($$arr_fields[$i]))."'";
						else
							$upd_idx = $arr_fields[$i]."='".trim($$arr_fields[$i])."'";
					else if ( trim($$arr_fields[$i]) )
						$upd_idx = $arr_fields[$i]."=".trim($$arr_fields[$i]);
			}
		}	
		$cmd = " update $table set ".implode(",",$upd_value)." where $upd_idx ";	 
//		if($table=="PER_PROJECT_GROUP" || $table=="PER_PROJECT_PAYMENT") {  
//			$cmd = " update $table set $arr_fields[1]='".$$arr_fields[1]."', $arr_fields[2]=".$$arr_fields[2].", UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' where $arr_fields[0]=".$$arr_fields[0]." ";	 
//		} else {
//			$cmd = " update $table set $arr_fields[1]='".$$arr_fields[1]."', $arr_fields[2]=".$$arr_fields[2].", UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' where $arr_fields[0]='".$$arr_fields[0]."' ";		
//		}
//		echo "update-$cmd<br>";
	//	exit;
		$db_dpis->send_cmd($cmd);
//		exit;
		//$db_dpis->show_error();

		$UPD=1;
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
	}
	
	if($command == "DELETE" && trim($$arr_fields[$key_index])){
//		$del_idx = $arr_fields[$key_index]."=".(strtolower($col_spec_datatype[$i])=="date" ? "'".save_date(trim($$arr_fields[$key_index]))."'" : (strpos($arr_fldtyp[$key_index],"CHAR")!==false ? "'".trim($$arr_fields[$key_index])."'" : trim($$arr_fields[$key_index])));
		if (strpos($arr_fldtyp[$key_index],"CHAR")!==false)	// ถ้าเป็นประเภท char
			if ( $arr_fldlen[$key_index]==19 && valid_char19_date_ddmmyyyy(trim($$arr_fields[$key_index]))!="NO" )
				$del_idx = $arr_fields[$key_index]."='".save_date(trim($$arr_fields[$key_index]))."'";
			else
				$del_idx = $arr_fields[$key_index]."='".trim($$arr_fields[$key_index])."'";
		else if ( trim($$arr_fields[$key_index]) )
			$del_idx = $arr_fields[$key_index]."=".trim($$arr_fields[$key_index]);
//		$cmd = " select $arr_fields[1] from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$cmd = " select $arr_fields[$name_index] from $table where ".$del_idx." ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[$name_index] = $data[$arr_fields[$name_index]];
		//echo $cmd;
//		if($table=="PER_PROJECT_GROUP" || $table=="PER_PROJECT_PAYMENT") {    $cmd = " delete from $table where $arr_fields[0]=".$$arr_fields[0]." "; }
//		else { $cmd = " delete from $table where $arr_fields[0]='".$$arr_fields[0]."' "; }
		$cmd = " delete from $table where ".$del_idx." ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$DEL=1;
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($$arr_fields[$key_index])." : ".$$arr_fields[$name_index]."]");
	}

//	echo "1.1..LG_CODE=$LG_CODE, LG_NAME=$LG_NAME UPD=$UPD DEL=$DEL err_text=$err_text<br>";
	if($UPD){
//		$sel_idx = $arr_fields[$key_index]."=".(strtolower(substr($col_spec_datatype[$i]),0,4)=="date" ? "'".save_date(trim($$arr_fields[$key_index]))."'" : (strpos($arr_fldtyp[$key_index],"CHAR")!==false ? "'".trim($$arr_fields[$key_index])."'" : trim($$arr_fields[$key_index])));
		if (strpos($arr_fldtyp[$key_index],"CHAR")!==false)	// ถ้าเป็นประเภท char
			if ( $arr_fldlen[$key_index]==19 && valid_char19_date_ddmmyyyy(trim($$arr_fields[$key_index]))!="NO" )
				$sel_idx = $arr_fields[$key_index]."='".save_date(trim($$arr_fields[$key_index]))."'";
			else
				$sel_idx = $arr_fields[$key_index]."='".trim($$arr_fields[$key_index])."'";
		else if ( trim($$arr_fields[$key_index]) )
			$sel_idx = $arr_fields[$key_index]."=".trim($$arr_fields[$key_index]);
//		if($table=="PER_PROJECT_GROUP" || $table=="PER_PROJECT_PAYMENT") {  $cmd = " select $arr_fields[1], $arr_fields[2], $arr_fields[5] from $table where $arr_fields[0]=".$$arr_fields[0]." "; }
//		else { $cmd = " select $arr_fields[1], $arr_fields[2], $arr_fields[5] from $table where $arr_fields[0]='".$$arr_fields[0]."' "; }
		$cmd = " select * from $table where ".$sel_idx." ";
		$db_dpis->send_cmd($cmd);
//		echo "cmd=$cmd<br>";
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$arr_head_control_index = explode(",",$head_control_index);
		for($i=0; $i<count($arr_fields); $i++) {
			if ($arr_fields[$i]!="UPDATE_USER" && $arr_fields[$i]!="UPDATE_DATE") {
//				echo ">>>>col_spec_datatype [$i]=".$col_spec_datatype[$i]."<br>";
				if (strtolower(substr($col_spec_datatype[$i],0,4))=="date") {
					$date_formno = substr($col_spec_datatype[$i],4);
					$$arr_fields[$i] = show_date_format(trim($data[$arr_fields[$i]]),($date_formno ? $date_formno : 1));
//					echo "field [$i] :: ".$arr_fields[$i]." = ".$$arr_fields[$i]."<br>";
				} else
					if ( strpos($arr_fldtyp[$i],"CHAR")!==false && $arr_fldlen[$i]==19 && valid_char19_date_yyyymmdd(trim($data[$arr_fields[$i]]))!="NO" )
						$$arr_fields[$i] = show_date_format(trim($data[$arr_fields[$i]]),($date_formno ? $date_formno : 1));
					else
						$$arr_fields[$i] = trim($data[$arr_fields[$i]]);
				$idx = array_search($i,$arr_head_control_index);	// ตรวจสอบว่าเป็น key นอก
				if ($idx !== false) {	// แสดงว่าเป็น key นอก
					$arr_head_control_tab = explode("|",$head_control_tab[$idx]);
					$column_control_code = explode(",",$arr_head_control_tab[2]);
					$column_control_name = explode(",",$arr_head_control_tab[3]);
		            if ($arr_head_control_tab[4]) $where1 = "where ".$arr_head_control_tab[4]; else $where1 = "";
					$cmd1 = " select * from ".$arr_head_control_tab[0]." $where1";
                    // แทนค่าลงตัวแปรในเงื่อนไข var1 var2...
                    for($k = 0; $k < count($column_control_code); $k++) {
	                   	$column_buff = explode("&",$column_control_code[$k]);
                    	$var = "var".($k+1);	// ชื่อของตัวแปร ลำดับตั้งแต่ 1
						if (strtolower(substr($column_buff[2],0,4))=="date")	// รูปแบบพิเศษเช่น date
		                    $cmd1 = str_replace($var, save_date($$column_buff[0]), $cmd1);	// เป็นตัวแรก [0] เพราะเป็นชื่อที่ใช้เก็บในตอน อ่าน ข้อมูลหลัก	 คือเหมือนชื่อ column ใน table หลัก
						else
							if (valid_char19_date_ddmmyyyy($$column_buff[0])!="NO" )
								$cmd1 = str_replace($var, save_date($$column_buff[0]), $cmd1);
							else
			                    $cmd1 = str_replace($var, $$column_buff[0], $cmd1);	// เป็นตัวแรก [0] เพราะเป็นชื่อที่ใช้เก็บในตอน อ่าน ข้อมูลหลัก	 คือเหมือนชื่อ column ใน table หลัก
					}
					$cnt = $db_dpis1->send_cmd($cmd1);
//					echo "cmd1=$cmd1 ($cnt)<br>";
					$data1 = $db_dpis1->get_array();
//					echo "code=".(implode("=>",$column_control_code)).", name=".(implode("=>",$column_control_name))."<br>";

					// ใส่ค่าลงใน element ในการ update ด้วย
                    for($k = 0; $k < count($column_control_code); $k++) {
	                   	$column_buff = explode("&",$column_control_code[$k]);
						if ($column_buff[1]) { 	// ใช้ตัวที่ [1] เพราะเป็นชื่อ ELEMENT ใน FORM
							if (strtolower(substr($column_buff[2],0,4))=="date")	{ // รูปแบบพิเศษเช่น date
								$date_formno = substr($column_buff[2],4);
								$$column_buff[1] = show_date_format($data1[$column_buff[0]],($date_formno ? $date_formno : 1));		// ใช้ตัวที่ [0] เพราะเป็นชื่อ COLUMN ใน TABLE
							} else
								if ( valid_char19_date_yyyymmdd(trim($data1[$column_buff[0]]))!="NO" )
									$$column_buff[1] = show_date_format($data1[$column_buff[0]],($date_formno ? $date_formno : 1));		// ใช้ตัวที่ [0] เพราะเป็นชื่อ COLUMN ใน TABLE
								else																						 
									$$column_buff[1] = $data1[$column_buff[0]];		// ใช้ตัวที่ [0] เพราะเป็นชื่อ COLUMN ใน TABLE
//							echo "1>code> ".$column_buff[1]."==".$data1[$column_buff[0]]."<br>";
						} else {
							if (strtolower(substr($column_buff[2],0,4))=="date") {	// รูปแบบพิเศษเช่น date
								$date_formno = substr($column_buff[2],4);
								${"CHANGE_".$column_buff[0]} = show_date_format($data1[$column_buff[0]],($date_formno ? $date_formno : 1)); 	// ถ้าตัวที่ [1] ไม่มี ใช้ CHANGE_ชื่อตัวที่ [0] เป็นชื่อกำหนดให้ใน FORM
							} else
								if ( valid_char19_date_yyyymmdd(trim($data1[$column_buff[0]]))!="NO" )
									${"CHANGE_".$column_buff[0]} = show_date_format($data1[$column_buff[0]],($date_formno ? $date_formno : 1)); 	// ถ้าตัวที่ [1] ไม่มี ใช้ CHANGE_ชื่อตัวที่ [0] เป็นชื่อกำหนดให้ใน FORM
								else
									${"CHANGE_".$column_buff[0]} = $data1[$column_buff[0]]; 	// ถ้าตัวที่ [1] ไม่มี ใช้ CHANGE_ชื่อตัวที่ [0] เป็นชื่อกำหนดให้ใน FORM
//							echo "2>code> CHANGE_".$column_buff[0]."==".$data1[$column_buff[0]]."<br>";
						}
					}
                    for($k = 0; $k < count($column_control_name); $k++) {
	                   	$column_buff = explode("&",$column_control_name[$k]);
						if ($column_buff[1]) { 	// ใช้ตัวที่ [1] เพราะเป็นชื่อ ELEMENT ใน FORM
							if (strtolower(substr($column_buff[2],0,4))=="date") {	// รูปแบบพิเศษเช่น date
								$date_formno = substr($column_buff[2],4);
								$$column_buff[1] = show_date_format($data1[$column_buff[0]],($date_formno ? $date_formno : 1));		// ใช้ตัวที่ [0] เพราะเป็นชื่อ COLUMN ใน TABLE
							} else
								if ( valid_char19_date_yyyymmdd(trim($data1[$column_buff[0]]))!="NO" )
									$$column_buff[1] = show_date_format($data1[$column_buff[0]],($date_formno ? $date_formno : 1));		// ใช้ตัวที่ [0] เพราะเป็นชื่อ COLUMN ใน TABLE
								else
									$$column_buff[1] = $data1[$column_buff[0]];		// ใช้ตัวที่ [0] เพราะเป็นชื่อ COLUMN ใน TABLE
//							echo "1>name> ".$column_buff[1]."==".$data1[$column_buff[0]]."<br>";
						} else {
							if (strtolower(substr($column_buff[2],0,4))=="date") {	// รูปแบบพิเศษเช่น date
								$date_formno = substr($column_buff[2],4);
								${"CHANGE_".$column_buff[0]} = show_date_format($data1[$column_buff[0]],($date_formno ? $date_formno : 1));	// ถ้าตัวที่ [1] ไม่มี ใช้ CHANGE_ชื่อตัวที่ [0] เป็นชื่อกำหนดให้ใน FORM
							} else
								if ( valid_char19_date_yyyymmdd(trim($data1[$column_buff[0]]))!="NO" )
									${"CHANGE_".$column_buff[0]} = show_date_format($data1[$column_buff[0]],($date_formno ? $date_formno : 1)); 	// ถ้าตัวที่ [1] ไม่มี ใช้ CHANGE_ชื่อตัวที่ [0] เป็นชื่อกำหนดให้ใน FORM
								else
									${"CHANGE_".$column_buff[0]} = $data1[$column_buff[0]];	// ถ้าตัวที่ [1] ไม่มี ใช้ CHANGE_ชื่อตัวที่ [0] เป็นชื่อกำหนดให้ใน FORM
//							echo "2>name> CHANGE_".$column_buff[0]."==".$data1[$column_buff[0]]."<br>";
						}
					}
				} else if (trim($data[$arr_fields[$i]])=='NULL' || is_null($data[$arr_fields[$i]])) {
					$$arr_fields[$i] = "";
				}
			}
		}
	} // end if
	
//	echo "1.2..LG_CODE=$LG_CODE, LG_NAME=$LG_NAME UPD=$UPD DEL=$DEL err_text=$err_text<br>";
	if( (!$UPD && !$err_text) || $command=="CANCEL" ){
		$arr_head_control_index = explode(",",$head_control_index);
		for($i=0; $i<count($arr_fields); $i++) {
			if ($arr_fields[$i]!="UPDATE_USER" && $arr_fields[$i]!="UPDATE_DATE") {
				if ($active_index && $i==$active_index) {
					$$arr_fields[$i] = 1;
//					echo "$i..active...<br>";
				} else if ($seq_order_index && $i==$seq_order_index) {
					$$arr_fields[$i] = 0;
//					echo "$i..seq_ord...<br>";
				} else {
					if ($head_control_index) {
						$idx = array_search($i,$arr_head_control_index);	// ตรวจสอบว่าเป็น key นอก
						if ($idx !== false) {	// แสดงว่าเป็น key นอก
							$arr_head_control_tab = explode("|",$head_control_tab[$idx]);
							$column_control_code = explode(",",$arr_head_control_tab[2]);	// ชื่อใน table ของรหัส,ชื่อ
							$column_control_name = explode(",",$arr_head_control_tab[3]);
//							echo "code=".(implode("=>",$column_control_code)).", name=".(implode("=>",$column_control_name))."<br>";
							for($k = 0; $k < count($column_control_code); $k++) {
//								echo "$k - column_control_code [$k]=".$column_control_code[$k]."<br>";
								$column_buff = explode("&",$column_control_code[$k]);
								if ($column_buff[1]) { 	// ใช้ตัวที่ [1]  ของ เพราะเป็นชื่อ ELEMENT ใน FORM
									$$column_buff[1] = "";		
								} else {
									${"CHANGE_".$column_buff[0]} = "";	// ถ้าตัวที่ [1] ไม่มี ใช้ CHANGE_ชื่อตัวที่ [0] เป็นชื่อกำหนดให้ใน FORM
								}
							}
							for($k = 0; $k < count($column_control_name); $k++) {
//								echo "$k - column_control_name [$k]=".$column_control_name[$k]."<br>";
								$column_buff = explode("&",$column_control_name[$k]);
								if ($column_buff[1]) { 	// ใช้ตัวที่ [1] เพราะเป็นชื่อ ELEMENT ใน FORM
									$$column_buff[1] = "";		// ใช้ตัวที่ [0] เพราะเป็นชื่อ COLUMN ใน TABLE
								} else {
									${"CHANGE_".$column_buff[0]} = ""; // ถ้าตัวที่ [1] ไม่มี ใช้ CHANGE_ชื่อตัวที่ [0] เป็นชื่อกำหนดให้ใน FORM
								}
							}
//							echo "cmd=$cmd1..CHANGE-->2..".$column_control_code[0]."-->3..".$column_control_name[0]."<br>";
//							echo "$i--key นอก ".$column_buff[0]."<br>";
						} else {
							$$arr_fields[$i] = "";
//							echo "$i-อื่น ๆ--".$arr_fields[$i]."=".$$arr_fields[$i]."<br>";
						}
					} else {
						$$arr_fields[$i] = "";
//						echo "$i-อื่น ๆ 2--".$arr_fields[$i]."=".$$arr_fields[$i]."<br>";
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