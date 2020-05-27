<?
	class connect_mssql {
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
		// ===================== class variable =========================
		
		// ===================== class function =========================
		function connect_mssql ($host, $database, $user, $password){
			$this -> host = $host;
			$this -> database = $database;
			$this -> user = $user;
			$this -> password = $password;
//	  		$this -> link_con = mssql_connect ($this -> host, $this -> user, $this -> password) or die ("cannot connect MSSQL Server");
	  		$this -> link_con = mssql_connect ($this -> host, $this -> user, $this -> password);
  			mssql_select_db ($this -> database, $this -> link_con);
		}
		function select_db ($database) {
			$this -> database = $database;
			mssql_select_db($this->database, $this->link_con);
		}
		function get_database () {
			return $this->database;
		}
		function query ($sql_query){
	  		$this->sql_query = $sql_query;
  			$this->result = mssql_query ($this->sql_query, $this -> link_con);
  			$this->ERROR = mssql_get_last_message ();
			$this->count = mssql_num_rows ($this -> result);
  			$this->columns = mssql_num_fields($this->result);
			return $this->count;
		}
		function send_cmd ($sql_query){
			return $this->query($sql_query);
		}
		function get_data () {
			return mssql_fetch_row ($this -> result);
		}
		function get_data_row($row_no){
  			return mssql_data_seek($this->result, $row_no);
		}
		function get_array () {
			return mssql_fetch_array ($this -> result);
		}
		function get_object () {
			return mssql_fetch_object ($this -> result);
		}
		function num_rows () {
			return mssql_num_rows($this->result);
		}
		function num_fields () {
			return mssql_num_fields($this->result);
		}
		function field_type ($field_no) {
			return mssql_field_type($this->result,$field_no);
		}
		function field_name ($field_no) {
			return mssql_field_name($this->result,$field_no);
		}
		function list_fields($table_name) {
			$count_f = $this->columns;
			$count_f++;
			for ($i = 1; $i < $count_f; $i++) {
				$fields_list[$i]["name"] = mssql_field_name($this->result, ($i-1));
				$fields_list[$i]["type"] = mssql_field_type($this->result, ($i-1));
				$fields_list[$i]["len"] = mssql_field_length($this->result, ($i-1));
			}
			return $fields_list;
		}
		function show_error () {
			echo "$this->sql_query<br>$this->ERROR<br>\n";
		}
		function free_result () {
			mssql_free_result ($this -> result);
		}
		function close () {
			mssql_close ($this -> link_con);
		}
		// ===================== class function =========================
	}
?>