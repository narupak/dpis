<?
	class connect_oracle {
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
		function connect_oracle ($host, $database, $user, $password){
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
//	  		$this -> link_con = OCIPLogon ($this -> user, $this -> password, $this -> database) or die ("cannot connect to Oracle Database");
	  		$this -> link_con = OCIPLogon ($this -> user, $this -> password, $this -> database);
//			echo "Oracle--> host=$host; database=$database; user=$user; password=$password";
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
  			$this->result = OCIParse($this->link_con, $this->sql_query);
  			$this->ERROR=OCIExecute($this->result, OCI_COMMIT_ON_SUCCESS);
			$this->count = OCIFetchStatement($this->result, $data);
			$this->columns = OCINumCols($this->result);
   			if($this->count) $this->ERROR=OCIExecute($this->result, OCI_COMMIT_ON_SUCCESS);
//			echo "ERROR :: ".$this->ERROR." :: COUNT :: ".$this->count." :: COLUMN :: ".$this->columns."<br>";
			return $this->count;
		}
		function send_cmd ($sql_query){
			return $this->query($sql_query);
		}
		function get_data () {
			OCIFetchInto ($this -> result, $data, OCI_NUM);
			return $data;
		}
		function get_data_row () {
			OCIFetchInto ($this -> result, $data, OCI_NUM); // not yet complete
			return $data;
		}
		function get_array () {
			OCIFetchInto ($this -> result, $data, OCI_ASSOC);
			return $data;
		}
		function get_object () {
			OCIFetchInto ($this -> result, $data);
			return $data;
		}
		function num_rows () {
			$numrows = OCIFetchStatement($this->result, $data);
  			OCIExecute($this->result, OCI_COMMIT_ON_SUCCESS);
			return $numrows;
		}
		function num_fields () {
			return OCINumCols($this->result);
		}
		function field_type ($field_no) {
			return OCIColumnType($this->result,$field_no);
		}
		function field_name ($field_no) {
			return OCIColumnName($this->result,$field_no);
		}
		function list_fields($table_name) {
			$count_f = $this->columns;
			for ($i = 1; $i <= $count_f; $i++) {
				$fields_list[$i]["name"] = OCIColumnName($this->result, $i);
				$fields_list[$i]["type"] = OCIColumnType($this->result, $i);
				$fields_list[$i]["len"] = OCIColumnSize($this->result, $i);
			}
			return $fields_list;
		}
		function show_error () {
//			echo print_r(OCIError($this->result))."<br>\n";
			$error = OCIError($this->result);
			if($error["offset"]) echo substr ($error["sqltext"], 0, $error["offset"]) . "*" . substr ($error["sqltext"], $error["offset"]) ."<br>&nbsp;&nbsp;". $error["message"];
			else echo $error["sqltext"];
		}
		function free_result () {
			OCIFreeStatement ($this -> result);
		}
		function close () {
			OCILogOff ($this -> link_con);
		}
		// ===================== class function =========================
	}
?>