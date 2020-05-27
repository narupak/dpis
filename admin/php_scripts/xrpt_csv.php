<?

function export_csv_alias($getfile) {
	global $db;
	
	// create array for export 
	$_alltables_array = $db->get_tables();
	//print_a($_alltables_array);
	
	foreach($_alltables_array as $table_name) {
		$sql = "SELECT * FROM ".$table_name." limit 1";
		$db->send_cmd($sql);
		//$table_detail_array[$table_name] = $db->get_flags();
		//print_a($table_detail_array);
	}

	// create file csv
	$fp = fopen($getfile, 'w');
	foreach ($table_detail_array as $table_name => $field_line) {
		foreach($field_line as $field_name => $attb_array) {
			if(in_array('primary_key',$attb_array))  
				$line = array($table_name,$field_name,'',"PK");
			else 
				$line = array($table_name,$field_name);
				
			fputcsv($fp,$line);
		}
	}
	fclose($fp);
	force_download($getfile);
}

function import_csv_alias($setname , &$data=array()) {
	global $db;
	
	if (($handle = fopen($setname, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if(empty($data[2])) $data[2] = $data[1];
			$data[1] = $data[0] . "_xfieldx_" . $data[1];
			
			$sql = "select aid from rpt_aliasname where fname = '$data[1]'";
			$db->send_cmd($sql);
			$temp = $db->get_array();
			if(is_array($temp)) {
				$sql = "update rpt_aliasname set tname = '$data[0]' , fname = '$data[1]', aname = '$data[2]' , pk_flag = '$data[3]' where aid = $temp[aid]";
				$db->send_cmd($sql);
				$_fieldid = $temp[aid];
			} else {
				$sql = "insert into rpt_aliasname (tname,fname,aname,pk_flag) values ('$data[0]','$data[1]','$data[2]','$data[3]')";
				$db->send_cmd($sql);
				
				$sql = "select aid from rpt_aliasname where tname = '$data[0]' and fname = '$data[1]' and aname = '$data[2]' ";
				$db->send_cmd($sql);
				$_temp = $db->get_array();
				$_fieldid = $_temp[0];
			}
			
			$_fk_field = $data[1];
			$_fieldid_array[$_fk_field] = $_fieldid;
			if($data[3] == 'FK') $_fk_array[$_fk_field] = str_replace(".","_xfieldx_",$data[4]);
			unset($data);
		}
		
		fclose($handle);
		
		insert_fk($_fk_array,$_fieldid_array);
	}
}

function insert_fk($_fk_array,$_fieldid_array) {
	global $db; 
	
	//print_a($_fk_array);
	//print_a($_fieldid_array);
	// clear data from table
	$sql = "delete from rpt_fk";
	$db->send_cmd($sql);
	
	// insert all FK 
	foreach($_fk_array as $_fk => $_pk) {
		$sql = "insert into rpt_fk (from_field,to_field) values (".$_fieldid_array[$_pk].",".$_fieldid_array[$_fk].") ";
		$db->send_cmd($sql);
	}
	
}

include("../../php_scripts/connect_database.php");

if($_GET['getfile']) export_csv_alias($_GET['getfile']);
echo $_FILES['fileupload']['name'];

if($_FILES['fileupload']['name']) {
	$uploadfile = "_" . $_FILES['fileupload']['name'];
	if(move_uploaded_file($_FILES['fileupload']['tmp_name'],$uploadfile)) {
		import_csv_alias($uploadfile);
		
?>
		<script language="JavaScript">
		<!--
		window.parent.uploadok('<?=$uploadfile?>');
		//-->
		</script>
<?
	}
}

?>