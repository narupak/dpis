<?
	class connect_mysql {
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
		function connect_mysql ($host, $database, $user, $password){
			$this -> host = $host;
			$this -> database = $database;
			$this -> user = $user;
			$this -> password = $password;
//	  		$this -> link_con = mysql_pconnect ($this -> host, $this -> user, $this -> password) or die ("cannot connect to MySQL Database");
	  		$this -> link_con = mysql_pconnect ($this -> host, $this -> user, $this -> password);
  			mysql_select_db ($this -> database, $this -> link_con);
			mysql_query("SET character_set_results = 'tis620', character_set_client = 'tis620', character_set_connection = 'tis620', character_set_database = 'tis620', character_set_server = 'tis620'", $this -> link_con);
		}
		function select_db ($database) {
			$this -> database = $database;
			mysql_select_db($this->database, $this->link_con);
		}
		function get_database () {
			return $this->database;
		}
		function query ($sql_query){
	  		$this->sql_query = $sql_query;
  			$this->result = mysql_query ($this->sql_query, $this -> link_con);
			$this->ERRNO = mysql_errno();
  			$this->ERROR = mysql_error ();
			$this->count = mysql_num_rows ($this->result);
  			$this->columns = mysql_num_fields($this->result);
			return $this->count;
		}
		function send_cmd ($sql_query){
			return $this->query($sql_query);
		}
		function get_data () {
			return mysql_fetch_row ($this -> result);
		}
		function get_data_row($row_no){
  			return mysql_data_seek($this->result, $row_no);
		}
		function get_array () {
			return mysql_fetch_array ($this -> result);
		}
		function get_object () {
			return mysql_fetch_object ($this -> result);
		}
		function num_rows () {
			return mysql_num_rows($this->result);
		}
		function num_fields () {
			return mysql_num_fields($this->result);
		}
		function field_type ($field_no) {
			return mysql_field_type($this->result,$field_no);
		}
		function field_name ($field_no) {
			return mysql_field_name($this->result,$field_no);
		}
		function list_fields($table_name) {
			$count_f = $this->columns;
			for ($i = 0; $i < $count_f; $i++) {
				$fields_list[$i]["name"] = mysql_field_name($this->result, $i);
				$fields_list[$i]["type"] = mysql_field_type($this->result, $i);
				$fields_list[$i]["len"] = mysql_field_len($this->result, $i);
			}
			return $fields_list;
		}
		function show_error () {
			echo "$this->sql_query<br>$this->ERRNO :: $this->ERROR<br>\n";
		}
		function free_result () {
			mysql_free_result ($this -> result);
		}
		function close () {
			mysql_close ($this -> link_con);
		}
		// ===================== class function =========================
	}
?>