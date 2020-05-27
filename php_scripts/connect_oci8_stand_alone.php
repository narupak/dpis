<?php
    class connect_oci8_stand_alone {
        // ===================== class variable =========================
        protected $host;
        protected $port;
        protected $database;
        protected $user;
        protected $password;
        protected $link_con = 0;

        public function __construct ($host, $database, $user, $password, $port) {
            if (!$port){ $port = 1521;}
            if ($database=="misusers"){ $port = 1522;}
            $this->host = $host;
            $this->port = $port;
            $this->database = "(DESCRIPTION =
                                        (ADDRESS =
                                            (PROTOCOL = TCP)
                                            (HOST = $host)
                                            (PORT = $port)
                                        )
                                  (CONNECT_DATA = (SERVICE_NAME = $database))
                                )";

            $this->user = $user;
            $this->password = $password;
        }
        //เขียนขึ้นใหม่
        function execQuery($strSQL){
            //global $db_host, $db_name, $db_user, $db_pwd, $port;
            $conn = OCIPLogon($this -> user, $this -> password, $this -> database);
            $num_rows = $this->num_row($strSQL);
            // Check connection
            $exQuery_result = OCIParse($conn, $strSQL);
            OCIExecute($exQuery_result);
            $array_result_data = '';
            if ($num_rows > 0) {
                OCIFetchInto ($exQuery_result, $array_result_data, OCI_ASSOC + OCI_RETURN_NULLS);
                ocifreestatement($exQuery_result);
                OCILogOff($conn);
                return $array_result_data;
            } else {
                OCILogOff($conn);
                return 0;
            }
        }
        function execNonQuery($strSQL){
            $conn = OCIPLogon ($this -> user, $this -> password, $this -> database);
            $exQuery = OCIParse($conn, $strSQL);
            OCIExecute($exQuery);
            ocifreestatement($exQuery);
            OCILogOff($conn);
        }
        function num_row($sql_queryx){
            $conn = OCIPLogon ($this -> user, $this -> password, $this -> database);
            $sql_query = $sql_queryx;
            $result = OCIParse($conn, $sql_query);
            OCIExecute($result, OCI_COMMIT_ON_SUCCESS);
            OCINumCols($result);
            $numrows = 0;
            while (OCIFetchInto($result, $row, OCI_ASSOC)) {
                $numrows++;
            }
            ocifreestatement($result); //ocicommit //ocirollback
            OCILogOff($conn);
            return $numrows;
        }
        function getTimeServer(){
            $strSQL = " SELECT DISTINCT TO_CHAR(SYSDATE,'dd/mm/YYYY') AS DATE_NOW, TO_CHAR(SYSDATE,'HH24:MI:SS') AS TIME_NOW ,TO_CHAR(sysdate,'yyyy') AS YEARNOW FROM DUAL ";
            return $this->execQuery($strSQL);
        }
        function connect_db_oci8(){
            return $conn = OCIPLogon($this -> user, $this -> password, $this -> database);
        }
        function close ($conn) {
            OCILogOff ($conn);
        }
    }