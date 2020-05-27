<?
	class connect_pgsql {
		// ===================== class variable =========================
		var $host;
		var $database;
		var $user;
		var $password;
		var $link_con = 0;
		var $pgsql_query;
		var $result;
		var $count;
		var $columns;
		var $ERRNO;
		var $ERROR;
		// ===================== class variable =========================
		
		// ===================== class function =========================
		function connect_pgsql ($host, $database, $user, $password){
			$this -> host = $host;
			$this -> database = $database;
			$this -> user = $user;
			$this -> password = $password;
//	  		$this -> link_con = pg_pconnect ($this -> host, $this -> user, $this -> password) or die ("cannot connect to PostgreSQL Database");
	  		$this -> link_con = pg_pconnect ($this -> host, $this -> database , $this -> user, $this -> password);
  			//pg_select_db ($this -> database, $this -> link_con);
		}
		/*function select_db ($database) {
			$this -> database = $database;
			pg_select_db($this->database, $this->link_con);
		}*/
		function get_database () {
			return $this->database;
		}
		function query ($pgsql_query){
	  		$this->pgsql_query = $pgsql_query;
  			$this->result = pg_query ($this -> link_con,$this->pgsql_query);
			$this->ERRNO = pg_errno();
  			$this->ERROR = pg_error ();
			$this->count = pg_num_rows ($this->result);
  			$this->columns = pg_num_fields($this->result);
			return $this->count;
		}
		function send_cmd ($pgsql_query){
			return $this->query($pgsql_query);
		}
		function get_data () {
			return pg_fetch_row ($this -> result);
		}
		function get_data_row () {
			return pg_fetch_row ($this -> result); // not yet complete
		}
		function get_array () {
			return pg_fetch_array ($this -> result);
		}
		function get_object () {
			return pg_fetch_object ($this -> result);
		}
		function num_rows () {
			return pg_num_rows($this->result);
		}
		function num_fields () {
			return pg_num_fields($this->result);
		}
		function field_type ($field_no) {
			return pg_field_type($this->result,$field_no);
		}
		function field_name ($field_no) {
			return pg_field_name($this->result,$field_no);
		}
		function list_fields($table_name) {
			$count_f = $this->columns;
			for ($i = 0; $i < $count_f; $i++) {
				$fields_list[$i]["name"] = pg_field_name($this->result, $i);
				$fields_list[$i]["type"] = pg_field_type($this->result, $i);
				$fields_list[$i]["len"] = pg_field_prtlen($this->result, $i);
			}
			return $fields_list;
		}
		function show_error () {
			echo "$this->pgsql_query<br>$this->ERRNO :: $this->ERROR<br>\n";
		}
		function free_result () {
			pg_free_result ($this -> result);
		}
		function close () {
			pg_close ($this -> link_con);
		}
		// ===================== class function =========================
	}
?>