<?
	include("php_scripts/session_start.php");
	include("../php_scripts/connect_file.php");

	date_default_timezone_set("Asia/Bangkok");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

	$arr_primery = explode("|",$prime);	// กระจายค่า Primery Key
	
	$delimeter = chr(126); // ~
	
	if ($DPISDB=="odbc") 
		$cmd = " select top 1 * from $table ";
	elseif ($DPISDB=="oci8")
		$cmd = " select * from $table where rownum = 1 ";
	elseif($DPISDB=="mysql")
		$cmd = " select * from $table limit 1 ";
	$db_dpis->send_cmd($cmd);

	$field_list = $db_dpis->list_fields($table);
//	echo "<pre>";		print_r($field_list);		echo "</pre>";
	unset($arr_fields);
	if ($DPISDB=="odbc" || $DPISDB=="oci8" || $DPISDB=="mssql") {
		for($i=1; $i<=count($field_list); $i++) : 
			$arr_fields[] = $tmp_name = $field_list[$i]["name"];
			$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
		endfor;
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($DPISDB == "odbc") {
		$TYPE_TEXT_STR = array("VARCHAR", "MEMO", "LONGCHAR", "TEXT");
		$TYPE_TEXT_INT = array("INTEGER", "INTEGER2", "SMALLINT", "SINGLE", "DOUBLE", "REAL", "NUMBER");
	} elseif ($DPISDB == "oci8") {
		$TYPE_TEXT_STR = array("VARCHAR", "VARCHAR2", "CHAR");
		$TYPE_TEXT_INT = array("NUMBER");
	} elseif ($DPISDB == "mysql") {
		$TYPE_TEXT_STR = array("VARCHAR", "MEMO", "LONGCHAR", "TEXT");
		$TYPE_TEXT_INT = array("INTEGER", "INTEGER2", "SMALLINT", "SINGLE", "DOUBLE", "REAL", "NUMBER");
	}
	// ======================================================================

//	echo $command."- $RealFile --".is_file($RealFile);
	if ($command=="CONVERT" && is_file($RealFile)) { 		//	trim($RealFile)
		// ล้างข้อมูลเก่าออกก่อน
		$cmd = "select * from IMPORT_TEMP where IMPORT_FILENAME='$form'";
//		echo "select cmd=$cmd<br>";
		$cntdel = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if ($cntdel > 0) {
			$cmd = "	delete from IMPORT_TEMP where IMPORT_FILENAME='$form'";
//			echo "delete cmd=$cmd<br>";
			$db_dpis->send_cmd($cmd);
		//	$db_dpis->show_error();
		}
		
		if ($running > -1) {	// เป็นกรณี key เป็น running number
			$cmd = " select max($arr_fields[$running]) as MAX_ID from $table ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$MAX_ID = $data1[MAX_ID] + 1;
//			echo "max_id-$cmd ($MAX_ID)<br>";
		}

//		echo "????? imptype=$imptype<br>";
		if ($imptype=="xls") {
//			echo "xls.....";
			$excel_msg = "";
			require_once('excelread.php');
	
			$config = array('excel_filename'=>$RealFile,
							'excel_sheet'=>0,'excel_numeric'=>FALSE,'excel_duplicate'=>FALSE,'excel_sort'=>FALSE,'excel_debug'=>FALSE);
	
			$excel_data = excel_read($config);
	 
//	/		echo "<pre>";
			$tmp_row_num = 1;
			foreach($excel_data as $row) {
//				if($inx) {
					$values = implode("','",$row);
//					$fields = explode(',',$values);
//					$fields = str_replace("'", "", $fields);
//					$data = implode(",", $fields);
					$data = str_replace("'", "", $values);
//					echo "values=".$values."  data=".$data."<br>";
					
					/* map data */
					$a_column = (array) null;
					$a_data = (array) null;
					$a_type = (array) null;
					$a_text = (array) null;
					$a_text1 = (array) null;
					$err = 0;
					for($i = 0; $i < count($column_map); $i++) {
						$arr_temp = explode(",", $data);
						if ($column_map[$i]) {
							$a_dat = "";
							if (substr($column_map[$i],0,3)=="sql") {
								// หาชื่อข้อมูลที่ select เข้ามา
								$data_type = substr($column_map[$i],4,1);
								$field_type = substr($column_map[$i],6,1);
								$sql = substr($column_map[$i],8);
								$c = strpos($sql,"from");
								$colname = trim(substr($sql, 7, $c-7));
								// จบการหาชื่อข้อมูลที่ select เข้ามา
								$c1 = strpos($sql,"{");
								$val = "";
								while ($c1) {
									$c2 = strpos($sql,"}", $c1);
									if (!$c2) { echo "***** รูปแบบ SQL ไม่ถูกต้อง *****<br>"; exit; }
									$data_col_sym = substr($sql,$c1,$c2-$c1+1);
									$data_col_idx = (int)substr($sql,$c1+1,$c2-$c1-1)-1;
									$val = ($data_type=="s" ? "'".$arr_temp[$data_col_idx]."'" : $arr_temp[$data_col_idx]);
//									$val = $arr_temp[$data_col_idx];
//									echo "data_col_sym=$data_col_sym, data_col_idx=($data_col_idx+1), val=$val<br>";
									$sql = str_replace($data_col_sym,$val,$sql);
									$c1 = strpos($sql,"{");
								} // end loop while {n}
//								echo "sql=$sql (colname=$colname)<br>";
								if ($sql) {
									$db_dpis1->send_cmd($sql);
//									echo "sql=".$sql."<br>";
									$data1 = $db_dpis1->get_array();
									if ($data_type=="s")
										$a_dat = trim($data1[$colname]);
									else
										$a_dat = trim($data1[$colname]);
									$a_typ = $data_type;
//									echo "colname=$colname , a_dat=$a_dat , a_typ=$a_typ<br>";
									if (!str_replace("'","",$a_dat)) { $a_dat = "**ไม่พบ $colname จากค่า $val**"; $err = 1; }
//									echo "$sql , a_dat=$a_dat<br>";
								} else {
									echo "***** ไม่พบประโยค SQL *****<br>"; 
									exit;
								}
//								echo "1..field [$i]=".$arr_fields[$i].", data [".$column_map[$i]."]".$arr_temp[$column_map[$i]]." ($a_dat)<br>";
							} else if ($column_map[$i]=="running") {
								$a_dat = $MAX_ID;
								$a_typ = "n";
//								echo "2..field [$i]=".$arr_fields[$i].", data [".$column_map[$i]."]".$arr_temp[$column_map[$i]]." ($a_dat)<br>";
							} else if ($column_map[$i]=="update_user") {
								$a_dat = $SESS_USERID;
								$a_typ = "n";
//								echo "3..field [$i]=".$arr_fields[$i].", data [".$column_map[$i]."]".$arr_temp[$column_map[$i]]." ($a_dat)<br>";
							} else if ($column_map[$i]=="update_date") {
								$a_dat = $UPDATE_DATE;
								$a_typ = "s";
//								echo "4..field [$i]=".$arr_fields[$i].", data [".$column_map[$i]."]".$arr_temp[$column_map[$i]]." ($a_dat)<br>";
							} else {
								$a_dat = $arr_temp[$column_map[$i]-1];
								if (strpos($a_dat,"'") > -1) $a_typ = "s";
								else $a_typ = "n";
//								echo "5..field [$i]=".$arr_fields[$i].", data [".($column_map[$i]-1)."]".$arr_temp[$column_map[$i]-1]."<br>";
							}
//							echo "field [$i]=".$arr_fields[$i].", data [".$column_map[$i]."]".$arr_temp[$column_map[$i]]."<br>";
							$a_data[] = trim($a_dat);
							$a_type[] = $a_typ;
							$a_column[] = $arr_fields[$i];
							$a_text[] = $arr_fields[$i]."=".trim($a_dat);
							$a_text1[] = $a_typ."=".$arr_fields[$i]."=".trim(str_replace("'","",$a_dat));
						} // end if ($column_map[$i])
					} // end loop for column_map
					$s_data = implode(",",$a_data);
					$s_column = implode(",",$a_column);
					$s_text = implode($delimeter,$a_text1);
					$cmd = "	insert into IMPORT_TEMP (IMPORT_FILENAME, ROW_NUM, DATA_PACK, ERROR) values ('$form', $tmp_row_num, '$s_text', $err)";
					$db_dpis->send_cmd($cmd);
//					echo "********************insert-$cmd<br>";
					$tmp_row_num++;
					if ($running > -1) $MAX_ID++;
//				} else {
//					$inx = 1;
//					$fields = $row;
//				}
			} // end for loop
//			echo "</pre>";
		} else {
//			echo "imptype=$imptype<br>";
			$head = 0;		// text file ไม่มี head = 0   ถ้ามี head ให้ = 1
			$db_textfile = new connect_file("$table", "r", $DIVIDE_TEXTFILE, $RealFile, $head);
			$field_name=implode(",",$arr_fields);
			$tmp_row_num = 1;
			while ($data = $db_textfile -> get_text_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
	//			str_replace("'00000000'",0,$data);	// ????????
				/* map data */
				$a_column = (array) null;
				$a_data = (array) null;
				$a_type = (array) null;
				$a_text = (array) null;
				$a_text1 = (array) null;
				$err = 0;
				for($i = 0; $i < count($column_map); $i++) {
					$arr_temp = explode(",", $data);
					if ($column_map[$i]) {
						$a_dat = "";
						if (substr($column_map[$i],0,3)=="sql") {
							// หาชื่อข้อมูลที่ select เข้ามา
							$data_type = substr($column_map[$i],4,1);
							$field_type = substr($column_map[$i],6,1);
							$sql = substr($column_map[$i],8);
							$c = strpos($sql,"from");
							$colname = trim(substr($sql, 7, $c-7));
							// จบการหาชื่อข้อมูลที่ select เข้ามา
							$c1 = strpos($sql,"{");
							$val = "";
							while ($c1) {
								$c2 = strpos($sql,"}", $c1);
								if (!$c2) { echo "***** รูปแบบ SQL ไม่ถูกต้อง *****<br>"; exit; }
								$data_col_sym = substr($sql,$c1,$c2-$c1+1);
								$data_col_idx = (int)substr($sql,$c1+1,$c2-$c1-1)-1;
								$val = $arr_temp[$data_col_idx];
	//							echo "data_col_sym=$data_col_sym, data_col_idx=($data_col_idx+1), val=$val<br>";
								$sql = str_replace($data_col_sym,$val,$sql);
								$c1 = strpos($sql,"{");
							} // end loop while {n}
	//						echo "sql=$sql (colname=$colname)<br>";
							if ($sql) {
								$db_dpis1->send_cmd($sql);
								$data1 = $db_dpis1->get_array();
								if ($data_type=="s")
									$a_dat = trim($data1[$colname]);
								else
									$a_dat = trim($data1[$colname]);
								$a_typ = $data_type;
	//							echo "colname=$colname , a_dat=$a_dat , a_typ=$a_typ<br>";
								if (!str_replace("'","",$a_dat)) { $a_dat = "**ไม่พบ $colname จากค่า $val**"; $err = 1; }
	//							echo "$sql , a_dat=$a_dat<br>";
							} else {
								echo "***** ไม่พบประโยค SQL *****<br>"; 
								exit;
							}
	//						echo "1..field [$i]=".$arr_fields[$i].", data [".$column_map[$i]."]".$arr_temp[$column_map[$i]]." ($a_dat)<br>";
						} else if ($column_map[$i]=="running") {
							$a_dat = $MAX_ID;
							$a_typ = "n";
	//						echo "2..field [$i]=".$arr_fields[$i].", data [".$column_map[$i]."]".$arr_temp[$column_map[$i]]." ($a_dat)<br>";
						} else if ($column_map[$i]=="update_user") {
							$a_dat = $SESS_USERID;
							$a_typ = "n";
	//						echo "3..field [$i]=".$arr_fields[$i].", data [".$column_map[$i]."]".$arr_temp[$column_map[$i]]." ($a_dat)<br>";
						} else if ($column_map[$i]=="update_date") {
							$a_dat = $UPDATE_DATE;
							$a_typ = "s";
	//						echo "4..field [$i]=".$arr_fields[$i].", data [".$column_map[$i]."]".$arr_temp[$column_map[$i]]." ($a_dat)<br>";
						} else {
							$a_dat = $arr_temp[$column_map[$i]-1];
							if (strpos($a_dat,"'") > -1) $a_typ = "s";
							else $a_typ = "n";
	//						echo "5..field [$i]=".$arr_fields[$i].", data [".($column_map[$i]-1)."]".$arr_temp[$column_map[$i]-1]."<br>";
						}
	//					echo "field [$i]=".$arr_fields[$i].", data [".$column_map[$i]."]".$arr_temp[$column_map[$i]]."<br>";
						$a_data[] = trim($a_dat);
						$a_type[] = $a_typ;
						$a_column[] = $arr_fields[$i];
						$a_text[] = $arr_fields[$i]."=".trim($a_dat);
						$a_text1[] = $a_typ."=".$arr_fields[$i]."=".trim(str_replace("'","",$a_dat));
					} // end if ($column_map[$i])
				} // end loop for column_map
				$s_data = implode(",",$a_data);
				$s_column = implode(",",$a_column);
				$s_text = implode($delimeter,$a_text1);
				$cmd = "	insert into IMPORT_TEMP (IMPORT_FILENAME, ROW_NUM, DATA_PACK, ERROR) values ('$form', $tmp_row_num, '$s_text', $err)";
				$db_dpis->send_cmd($cmd);
	//			echo "********************insert-$cmd<br>";
				$tmp_row_num++;
				if ($running > -1) $MAX_ID++;
			}  // end while 
		} // end if ($imptype)
		if ($tmp_row_num)  $impfile = stripslashes($RealFile);
//		echo "impfile=".$impfile."<br>";
	} // endif command==CONVERT

	if ($command=="IMPORT") { 		//	trim($RealFile)
		$cmd = "select * from IMPORT_TEMP where IMPORT_FILENAME='$form' order by ROW_NUM";
		$cnt = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
//		echo "select cmd=$cmd ($cnt)<br>";
		if ($cnt > 0) {
			if ($running > -1) {	// เป็นกรณี key เป็น running number
				$cmd1 = " select max($arr_fields[$running]) as MAX_ID from $table ";
				$db_dpis1->send_cmd($cmd1);
				$data1 = $db_dpis1->get_array();
				$MAX_ID = $data1[MAX_ID] + 1;
//				echo "max_id-$cmd<br>";
			}
			$cnt_insert=0;
			while($data = $db_dpis->get_array()) {
//				echo "ROW_NUM=".$data[ROW_NUM]."<br>";
				if ($data[ERROR]!=1) {
//					echo "$MAX_ID=MAX_ID<br>";
					$dpack = $data[DATA_PACK];
					$data_fld = explode($delimeter, $dpack);
					$key_dup = explode("|",$dup_column);
					$a_cond = (array) null;
					for($k = 0; $k < count($key_dup); $k++) {
						$a_dat = explode("=",$data_fld[$key_dup[$k]]);
						$a_cond[] = $a_dat[1]."=".($a_dat[0]=="s" ? "'".$a_dat[2]."'" : $a_dat[2]);
					} // end for loop
					$where = implode(" and ",$a_cond);
					$cmd = "select * from $table ".($where ? " where ".$where : "");
					$cntdel = $db_dpis1->send_cmd($cmd);
//					echo "select for delete = $cmd<br>";
					if ($cntdel) {
						$cmd = "delete from $table ".($where ? " where ".$where : "");
						$db_dpis1->send_cmd($cmd);
					}
					// แปลงเป็น insert sql
					$a_colname = (array) null;
					$a_value = (array) null;
					for($i = 0; $i < count($data_fld); $i++) {
						if ($column_map[$i]) {
							$a_dat = (array) null;
							if ($column_map[$i]=="running") {
								$a_dat = explode("=",$data_fld[$i]);
								$a_colname[] = $a_dat[1];
								$a_value[] = $MAX_ID;
							} else if ($column_map[$i]=="update_user") {
								$a_dat = explode("=",$data_fld[$i]);
								$a_colname[] = $a_dat[1];
								$a_value[] = $SESS_USERID;
							} else if ($column_map[$i]=="update_date") {
								$a_dat = explode("=",$data_fld[$i]);
								$a_colname[] = $a_dat[1];
								$a_value[] = "'".$UPDATE_DATE."'";
							} else {
								$a_dat = explode("=",$data_fld[$i]);
								$a_colname[] = $a_dat[1];
								$a_value[] = ($a_dat[0]=="s" ? "'".$a_dat[2]."'" : ($a_dat[2]=='NULL' ? 'NULL' :(float)$a_dat[2]));
							} // end if ($data[ERROR]!=1)
						} // end if ($column_map[$i])
					} // end for loop
					$cmd = "	insert into $table (".implode(",",$a_colname).") values (".implode(",",$a_value).")";
					$db_dpis1->send_cmd($cmd);
//					echo "insert cmd=".$cmd."<br>";
					$MAX_ID++;
					$cnt_insert++;
				} // end if ($data[ERROR]!=1)
			} // end loop while
			if ($cnt==$cnt_insert) { // 	ถ้า import ครบทุกรายการ ให้ delete temp data
				$cmd = "delete from IMPORT_TEMP where IMPORT_FILENAME='$form'";
				$db_dpis->send_cmd($cmd);
			}
		} // end if ($cnt > 0)
	} // endif command==IMPORT
	
	if ($command=="SAVE" && $ROW_NUM) {
		$data_fld = explode($delimeter, $updtext);
		$err = 0;
		for($i = 0; $i < count($data_fld); $i++) {
			$dat_detail = explode("=",$data_fld[$i]);
//			echo "data_fld [$i]=".$data_fld[$i]." ($old_data)<br>";
			if ($column_map[$i]) {
				if (substr($column_map[$i],0,3)=="sql") {
					// หาชื่อข้อมูลที่ select เข้ามา
					$data_type = substr($column_map[$i],4,1);
					$field_type = substr($column_map[$i],6,1);
					$sql = substr($column_map[$i],8);
					$c = strpos($sql,"from");
					$colname = trim(substr($sql, 7, $c-7));
					// จบการหาชื่อข้อมูลที่ select เข้ามา
					$c1 = strpos($sql,"{");
					$val = "";
					$a_dat = "";
					while ($c1) {
						$c2 = strpos($sql,"}", $c1);
						if (!$c2) { echo "***** รูปแบบ SQL ไม่ถูกต้อง *****<br>"; exit; }
						$data_col_sym = substr($sql,$c1,$c2-$c1+1);
						$data_col_idx = (int)substr($sql,$c1+1,$c2-$c1-1);
						$data_map_idx = array_search($data_col_idx, $column_map);
						if (!$data_map_idx) {
							for($k = 0; $k < count($column_map); $k++) {
								if (strpos($column_map[$k], $data_col_sym)!==false) {
									$data_map_idx = $k;
									break;
								}
							}
						}
						$dat_detail1 = explode("=",$data_fld[$data_map_idx]);
//						echo "data_col_sym=$data_col_sym, data_col_idx=$data_col_idx , dat_detail1[1]=".$dat_detail1[1]."=".$$dat_detail1[1]." , data_map_idx=$data_map_idx , ".$data_fld[$data_map_idx]."<br>";
						$val = ($dat_detail1[0] = "s" ? "'".$$dat_detail1[1]."'" : $$dat_detail1[1]);
//						$val = $$dat_detail1[1];
						$sql = str_replace($data_col_sym,$val,$sql);
						$c1 = strpos($sql,"{");
					} // end loop while {n}
//					echo "sql=$sql<br>";
					if ($sql) {
						$db_dpis1->send_cmd($sql);
						$data1 = $db_dpis1->get_array();
						if ($data_type=="s")
							$a_dat = trim($data1[$colname]);
						else
							$a_dat = trim($data1[$colname]);
						$a_typ = $data_type;
						if (!str_replace("'","",$a_dat)) { $a_dat = "**ไม่พบ $colname จากค่า $val**"; $err = 1; }
//						if (!$a_dat) { $a_dat = "**Not found $colname**"; $err = 1; }
//						echo "$sql , a_dat=$a_dat<br>";
						$dat_detail[2] = $a_dat;
					}
				} else if ($column_map[$i]=="running") {
//					$a_dat = $MAX_ID;
//					echo "2..field [$i]=".$arr_fields[$i].", data [".$column_map[$i]."]".$arr_temp[$column_map[$i]]." ($a_dat)<br>";
				} else if ($column_map[$i]=="update_user") {
					$dat_detail[2] = $SESS_USERID;
//					echo "3..field [$i]=".$arr_fields[$i].", data [".$column_map[$i]."]".$arr_temp[$column_map[$i]]." ($a_dat)<br>";
				} else if ($column_map[$i]=="update_date") {
					$dat_detail[2] = $UPDATE_DATE;	// ไม่จำเป็นต้องใส่ ' เพราะมัน update ใน temp ตอน update ลง table จริงค่อยใส่
//					echo "4..field [$i]=".$arr_fields[$i].", data [".$column_map[$i]."]".$arr_temp[$column_map[$i]]." ($a_dat)<br>";
				} else {
//				} else if ( !($column_map[$i]=="running" || $column_map[$i]=="update_user" || $column_map[$i]=="update_date") ) {
//					echo "i=$i , data=".$dat_detail[1]." (".$$dat_detail[1].")<br>";
					$dat_detail[2]=$$dat_detail[1];
				}
			} // end if ($column_map[$i])
			$data_fld[$i] = implode("=",$dat_detail);
		} // end loop for $i
		$updtext = implode($delimeter,$data_fld);
		$cmd = "update IMPORT_TEMP set DATA_PACK='$updtext', ERROR='$err' where IMPORT_FILENAME='$form' and ROW_NUM=$ROW_NUM";
		$db_dpis->send_cmd($cmd);
//		echo "save....$cmd<br>";
	}

	if ($command=="DELETE" && $ROW_NUM) {
		$cmd = "delete from IMPORT_TEMP where IMPORT_FILENAME='$form' and ROW_NUM=$ROW_NUM";
		$db_dpis->send_cmd($cmd);
//		echo "delete....$cmd<br>";
	}
?>