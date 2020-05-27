<?php

	function force_download($file) {
	     header("Content-type: application/force-download");
		 header("Content-Transfer-Encoding: Binary");
		 header("Content-length: ".filesize($file));
		 header("Content-disposition: attachment; filename=\"".basename($file)."\"");
		 readfile($file);
	}
	
	function upload_file($uploadfile) {
		
		sleep(3);
		$file = $uploadfile['fileupload']['name'];
		$typefile = $uploadfile['fileupload']['type']; 
		$sizefile = $uploadfile['fileupload']['size']; 
		$tempfile = date("Y-m-d")."-".$file;

		if(move_uploaded_file($uploadfile['fileupload']['tmp_name'],$tempfile)) 
			return $tempfile;
		else 
			return false;
	}
	
	function print_a($array) {
		echo "<pre>";print_r($array);echo "</pre>";
	}
	
	function condition_process($field,$operator,$value,$match) {
		switch($operator) {
			case '=' : 
			case '!=' : 
			case '>' : case '>=' :
			case '<' : case '<=' : 
							if($match) $field_match = "$field $operator $match ";
							if($value) $field_value = "$field $operator '$value'";
							if($match && $value) $field = "$field_match and $field_value";
							elseif($match) $field = $field_match;
							elseif($value) $field = $field_value;
							break;
			case 'like' : case 'not like' : 
							$field = "$field $operator '%$value%'";break;
			case 'in' :	case 'not in' : 
							$field = "$field $operator ('".str_replace(",","','",$value)."')";break;
			case 'between' : case 'not between' : 
							list($x1,$x2) = explode(',',$value);
							$field = "$field $operator '$x1' and '$x2'";break;
		}
		
		return $field;
	}
	
	function match_process($field,$operator,$value,$match) {
	}
	
	function field_process($field,$value) {
		//echo "$field as $value <br>";
		$field = "$field as $value";
		return $field;
	}	
	
	
	function join_process($table_array) {
		global $db;
		
		// table1 join table2 on table1.field1 = table2.field2
		//
		//print_r($table_array);
		foreach($table_array as $table_name) {
			// this table have forienkey 
			$sql = "select fname,tname,from_field,to_field,fid from rpt_fk,rpt_aliasname where aid = from_field and tname = '$table_name'" ;
			$db->send_cmd($sql);
			//echo " select FK =>" . $sql . "<br>";
			unset($_to_field);
			while($temp = $db->get_array()) {
				// have FK must select PK 
				$fid = $temp[fid];
				$_array[$fid][from_fname] = str_replace('_xfieldx_','.',$temp[fname]);
				$_array[$fid][from_tname] = $temp[tname];
				$_to_field[] = $temp[to_field];
			}
			
			// this table have forienkey 
			if(!is_array($_to_field)) continue; 
			$sql = "select fname,tname,fid from rpt_fk,rpt_aliasname where aid = to_field and to_field in ('" . implode("','",$_to_field) . "')";
			$db->send_cmd($sql);
			//echo " select PK =>" . $sql . "<br>";
			while($temp = $db->get_array()) {
				// have FK must select PK 
				$fid = $temp[fid];
				$_array[$fid][to_fname] = str_replace('_xfieldx_','.',$temp[fname]);
				$_array[$fid][to_tname] = $temp[tname];
			}
			
			//list($table,$xxx) = explode('.',$field);
			//$return = $temp[tname] . " join $table on " . str_replace('_xfield_','.',$temp[fname]) . " = " . $field;
			//echo "<pre>"; print_r($_array); echo "</pre>";
		}
		// create array 
		foreach($_array as $fid => $row) {
			$to_tname = $row[to_tname];
			$from_tname = $row[from_tname];
			$_return["$to_tname"]["$from_tname"][] = $row[to_fname] . " = " . $row[from_fname];
		}
		
		// create join clause
		echo "<pre>";
		//print_r($_return);
		echo "</pre>";
		foreach($_return as $_table => $_joinrow) {
			$return .= " $_table ";
			foreach($_joinrow as $_jointable => $_row) {
				$return .= " join $_jointable on " . implode(" and ",$_row);
			}
		}
		return $return;
	}	
	
	function order_process($field,$value) {
		$field = "$field $value";
		return $field;
	}
	
	function implode_ksort($grue,$array) {
		ksort($array);
		return implode($grue,$array);
	}
	
	function get_rpt_general($gid) {
		global $db;
		
		$sql = "select gname,gheader from rpt_general where gid = " . $gid;
		$db->send_cmd($sql);
		return $db->get_array();
	}
	
	function get_tname($gid) {
		global $db;
		
		$sql = "select tname from rpt_manual_table where gid = " . $gid;
		$db->send_cmd($sql);
		while($temp = $db->get_array()) {
			$return[] = $temp['tname'];
			//$selected_table .= "<option value='".$temp['tname']."'>".$temp['tname']."</option>";
		}
		return $return;
	}
	
	function get_fname($gid) {
		global $db;
		
		$sql = "select fname from rpt_manual_detail where gid = " . $gid;
		$db->send_cmd($sql);
		while($temp = $db->get_array()) {
			list($table,$field) = explode('.',$temp['fname']);
			$return[$table][] = $field;
		}
		return $return; 
	}

	function create_option_field($field_array) {
		foreach($field_array as $table_name => $field_rows) {
			foreach($field_rows as $field) {
				$value = $table_name . "_xfieldx_" . $field;
				$show = $table_name . ":" . $field;
				$option .= "<option value='$value'>$show</option>";
			}
		}
		return $option;
	}

	function create_var_param($gid){
		
		$table_array = get_fname($gid);
		//print_r($field_array);
		foreach($table_array as $table => $field_array) {
			foreach($field_array as $field) {
				$return[] = $table.'_xfieldx_'.$field;
			}
		}
		return $return;
	}

	function get_field_colunm($gid,$col) {
		global $db;
		
		$sql = "select fname, $col from rpt_manual_detail where gid = " . $gid;
		$db->send_cmd($sql);
		while($temp = $db->get_array()) {
			foreach($temp as $key => $value) {
				if($key == 'fname')
					$fname = $value;
				else 
					$col = $value;
			}
			$fname = str_replace('.',':',$fname);
			$return[$fname] = $col;
		}
		//print_r($return);  echo "<br>";
		return $return;
	}
	
	function create_option_operator($selected="") {			
		$op_array = array(
						 "="=>"is equal",
						 "!="=>"is not equal",
						 ">"=>"is grester than",
						 ">="=>"is gester then or equal",
						 "<"=>"is less than",
						 "<="=>"is less than or equal",
						 "like"=>"contain",
						 "not like"=>"not contain",
						 "in"=>"is in list",
						 "not in"=>"is not in list",
						 "between"=>"between",
						 "not between"=>"not berween"
						);
		foreach($op_array as $ops => $value) {
			if($selected == $ops) {
				$return .= '<option value="'.$ops.'" selected>'.$value.'</option>';
			} else {
				$return .= '<option value="'.$ops.'">'.$value.'</option>';
			}
		}
		
		return $return;
	}
	
	function create_option_match($selected="",$field_array) {
		foreach($field_array as $ext_name) {
			$field = str_replace('_xfieldx_','.',$ext_name);
			if($selected == $field)
				$return .= '<option value="'.$field.'" selected="selected">'.$field.'</option>';
			else
				$return .= '<option value="'.$field.'">'.$field.'</option>';
		}
		return $return;
	}
		
	function create_option_pk($selected="",$field_array) {
		foreach($field_array as $ext_name => $field_name) {
			if($selected == $field_name['id'])
				$return .= '<option value="'.$field_name['id'].'" selected="selected">'.$field_name['name'].'</option>';
			else
				$return .= '<option value="'.$field_name['id'].'">'.$field_name['name'].'</option>';
		}
		return $return;
	}
	
	//=====================================================================
	//
	//	function for easy
	//
	//=====================================================================
	
	function create_option_array($array,$selected="") {
		foreach($array as $key => $value) {
			if($key==$selected)
				$return .= '<option value="'.$key.'" selected="selected">'.$value.'</option>';
			else
				$return .= '<option value="'.$key.'">'.$value.'</option>';
		}
		return $return;
	}
	
	function get_aliasname_with_aid($aid_array = array()) {
		global $db;
		
		if(count($aid_array) > 0)
			$sql = "select aname,aid from rpt_aliasname where aid in ('" . implode("','",$aid_array) . "') order by aname";
		else 
			$sql = "select aname,aid from rpt_aliasname order by aname";
		$db->send_cmd($sql);
		while($temp = $db->get_array()) {
			$_aid = $temp[aid];
			$_alias_array[$_aid] = $temp[aname];
		}
		return $_alias_array;
	}	
	
	function get_tname_with_aid($aid_array = array()) {
		global $db;
		
		$sql = "select tname,aid from rpt_aliasname where aid in ('" . implode("','",$aid_array) . "') order by tname";
		$db->send_cmd($sql);
		while($temp = $db->get_array()) {
			$_aid = $temp[aid];
			$_alias_array[$_aid] = $temp[tname];
		}
		$_alias_array = array_unique($_alias_array);
		return $_alias_array;
	}
	
	function get_fname_with_aid($aid_array = array()) {
		global $db;
		
		$sql = "select fname,aid from rpt_aliasname where aid in ('" . implode("','",$aid_array) . "') order by fname";
		$db->send_cmd($sql);
		while($temp = $db->get_array()) {
			$_aid = $temp[aid];
			$_alias_array[$_aid] = str_replace("_xfieldx_",".",$temp[fname]);
		}
		$_alias_array = array_unique($_alias_array);
		return $_alias_array;
	}
	
	function get_aliasname_selected($gid) {
		global $db;
		
		$sql = "select rpt_aliasname.aname,rpt_aliasname.aid from rpt_easy_detail,rpt_aliasname 
				where rpt_aliasname.aid = rpt_easy_detail.aid and 
				rpt_easy_detail.gid = '$gid' order by aname";
		$db->send_cmd($sql);
		while($temp = $db->get_array()) {
			$_aid = $temp[aid];
			$_alias_array[$_aid] = str_replace("_xfieldx_",".",$temp[aname]);
		}
		$_alias_array = array_unique($_alias_array);
		return $_alias_array;
	}
	
	function get_aid_selected($gid) {
		global $db;

		$sql = "select aid from rpt_easy_detail where rpt_easy_detail.gid = '$gid'";
		$db->send_cmd($sql);
		while($temp = $db->get_array()) {
			$_alias_array[] = $temp[aid];
		}
		return $_alias_array;
	}
	
	function get_easy_detail_colunm($gid,$col) {
		global $db;
		
		$sql = "select aid, $col from rpt_easy_detail where gid = " . $gid;
		$db->send_cmd($sql);
		while($temp = $db->get_array()) {
			foreach($temp as $key => $value) {
				if($key == 'aid')
					$fname = $value;
				else 
					$col = $value;
			}
			$return[$fname] = $col;
		}
		return $return;
	}
		
	function get_easy_header($gid) {
		global $db;
		
		$sql = "select gname,gheader from rpt_easy_header where gid = " . $gid;
		$db->send_cmd($sql);
		return $db->get_array();
	}
	
?>