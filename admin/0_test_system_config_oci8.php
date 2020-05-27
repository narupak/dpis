<?
		$db_host 			= 	"localhost";
		$db_name 		= 	"ocsc";
		$db_user 			= 	"industry";
		$db_pwd			= 	"industry";

		// Connects to the XE service (i.e. database) on the "localhost" machine
		$host_db = "$db_host/$db_name";
		$conn = oci_connect($db_user, $db_pwd, $host_db);
		if (!$conn) {
		    $e = oci_error();
		    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		}

		$cmd = " select config_name, config_value from system_config ";
		
		$stid = oci_parse($conn, $cmd);
		oci_execute($stid);

		echo "<table border='1'>\n";
		while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
		    echo "<tr>\n";
		    foreach ($row as $item) {
		        echo "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
		    }
		    echo "</tr>\n";
		}
		echo "</table>\n";
?>