<?
	class connect_odbc {
		// ===================== class variable =========================
		var $host = "";
		var $dsn	= "";
		var $user	= "";
		var $pwd	= "";
		var $link_con = 0;
		var $sqlquery;
		var $tablename;
		var $result;
		var $count_row;
		var $count_fields;
		var $ERRNO;
		var $ERROR;
		// ===================== class variable =========================

		// ===================== class function =========================
		function connect_odbc ($host, $dsn,$user,$pwd) {
			$this->host = $host;
			$this->dsn = $dsn;
			$this->user = $user;
			$this->pwd = $pwd;
			//$this->link_con = odbc_connect($dsn,$user,$pwd) or die ("$dsn,$user,$pwd = ODBC Connect Error");
			$this->link_con = odbc_connect($dsn,$user,$pwd);
		}
		function get_database () {
			return $this->dsn;
		}
		function query($query){
			$this->ERRNO = 0;
			$this->ERROR = '';
  			$this->sqlquery  = $query;

  			$this->result = odbc_exec($this->link_con,$query);
			if($this->result == false) {
				$this->ERRNO = odbc_error($this->link_con);
				$this->ERROR = odbc_errormsg($this->link_con);
			} 

			$count_row = 0;
			while($data = odbc_fetch_row($this->result)) $count_row++;
			$this->count_row = $count_row;
			odbc_fetch_row($this->result, 0);
			$this->count_fields =  odbc_num_fields($this->result);

  			return $this->count_row;
		}
		function send_cmd ($query){
			return $this->query($query);
		}
		function get_data(){
  			return odbc_fetch_row($this->result);
		}
		function get_data_row($row_no){
  			return odbc_fetch_row($this->result, $row_no);
		}
		function get_array () {
			return odbc_fetch_array ($this -> result);
		}
		function get_object(){
  			return odbc_fetch_object($this->result);
		}
		function num_rows () {
			return odbc_num_rows($this->result);
		}
		function num_fields () {
			return odbc_num_fields($this->result);
		}
		function field_type ($field_no) {
			return odbc_field_type($this->result,$field_no);
		}
		function field_name ($field_no) {
  			return odbc_field_name($this->result,$field_no);
		}
		function list_fields($table_name) {
			$count_f = $this->count_fields;
			$count_f++;
			for ($i = 1; $i < $count_f; $i++) {
				$fields_list[$i]["name"] = odbc_field_name($this->result, $i);
				$fields_list[$i]["type"] = odbc_field_type($this->result, $i);
				$fields_list[$i]["len"] = odbc_field_len($this->result, $i);
			}
			return $fields_list;
		}
		function show_error() {
			$errtxt = "SQL = " . $this->sqlquery . "\nError No = " . $this->ERRNO . "\nError Des = " . $this->ERROR ;
			echo nl2br($errtxt);
		}
		function free_result(){
  			odbc_free_result($this->result);
		}
		function close() {
  			 odbc_close ($this->link_con);
		}
		// ===================== class function =========================
	} // end of class
?>
