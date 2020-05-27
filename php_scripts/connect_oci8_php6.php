<?
	class connect_oci8 {
		// ===================== class variable =========================
		var $host;
		var $database;
		var $user;
		var $password;
		var $link_con = 0;
		var $sql_query;
		var $result;
		var $count;
		var $columns;
		var $ERRNO;
		var $ERROR;
//		var $data;							// use for fetch data from result set
		// ===================== class variable =========================
		
		// ===================== class function =========================
		function connect_oci8 ($host, $database, $user, $password){
			$this -> host = $host;
			$this -> database = "(DESCRIPTION =
													 (ADDRESS =
														 (PROTOCOL = TCP)
														 (HOST = $host)
														 (PORT = 1521)
													 )
												   (CONNECT_DATA = (SERVICE_NAME = $database))
												  )";
			
			$this -> user = $user;
			$this -> password = $password;
//	  		$this -> link_con = oci_pconnect ($this -> user, $this -> password, $this -> database) or die ("cannot connect to Oracle 8 Database");
	  		$this -> link_con = oci_pconnect ($this -> user, $this -> password, $this -> database);
		}
		function select_db ($database) {
			$this -> database = $database;
//			mysql_select_db($this->database, $this->link_con);
		}
		function get_database () {
			return $this->database;
		}
		function query ($sql_query){
	  		$this->sql_query = $sql_query;
  			$this->result = oci_parse($this->link_con, $this->sql_query);
  			$this->ERROR=oci_execute($this->result, OCI_COMMIT_ON_SUCCESS);
			$this->count = oci_fetch_all($this->result, $data);
			$this->columns = oci_num_fields($this->result);
  			if($this->count) $this->ERROR=oci_execute($this->result, OCI_COMMIT_ON_SUCCESS);
//			echo "ERROR :: ".$this->ERROR." :: COUNT :: ".$this->count." :: COLUMN :: ".$this->columns."<br>";
			return $this->count;
		}
		function send_cmd ($sql_query){
			return $this->query($sql_query);
		}
		function get_data () {
			oci_fetch_row ($this -> result, $data, OCI_NUM + OCI_RETURN_NULLS);
			return $data;
		}
		function get_data_row () {
			oci_fetch_row ($this -> result, $data, OCI_NUM + OCI_RETURN_NULLS); // not yet complete
			return $data;
		}
		function get_array () {
			oci_fetch_row ($this -> result, $data, OCI_ASSOC + OCI_RETURN_NULLS);
			return $data;
		}
		function get_object () {
			oci_fetch_row ($this -> result, $data);
			return $data;
		}
		function num_rows () {
			$numrows = oci_fetch_all($this->result, $data);
  			oci_execute($this->result, OCI_COMMIT_ON_SUCCESS);
			return $numrows;
		}
		function num_fields () {
			return oci_num_fields($this->result);
		}
		function field_type ($field_no) {
			return oci_field_type($this->result,$field_no);
		}
		function field_name ($field_no) {
			return oci_field_name($this->result,$field_no);
		}
		function list_fields($table_name) {
			$count_f = $this->columns;
			for ($i = 1; $i <= $count_f; $i++) {
				$fields_list[$i]["name"] = oci_field_name($this->result, $i);
				$fields_list[$i]["type"] = oci_field_type($this->result, $i);
				$fields_list[$i]["len"] = oci_field_size($this->result, $i);
			}
			return $fields_list;
		}
		function show_error () {
//			echo print_r(OCIError($this->result))."<br>\n";
			$error = oci_error($this->result);
			if($error["offset"]) echo substr ($error["sqltext"], 0, $error["offset"]) . "*" . substr ($error["sqltext"], $error["offset"]) ."<br>&nbsp;&nbsp;". $error["message"];
			else echo $error["sqltext"];
		}
		function free_result () {
			oci_free_statement ($this -> result);
		}
		function close () {
			oci_close ($this -> link_con);
		}
		// ===================== class function =========================
	}
?>