<?
	$db_host 			= 	"localhost";
	$db_name 		= 	"dpis35";
	$db_user 			= 	"root";
	$db_pwd			= 	"dpis";
	$cn = mysql_pconnect ($db_host, $db_user, $db_pwd);
  	$db = mysql_select_db ($db_name, $cn);
	mysql_query("SET character_set_results = 'tis620', character_set_client = 'tis620', character_set_connection = 'tis620', character_set_database = 'tis620', character_set_server = 'tis620'", $cn);
	$result = mysql_query ("select * from user_group", $cn);
	$count = mysql_num_rows ($result);
	if ($count) {
		echo "ข้อมูลจำนวน ".$count." records<br>";
		while ($data = mysql_fetch_array ($result)) {
			echo "$i-".$data[id].",".$data[code].",".$data[name_th]."<br>";
		}
	}
	$cn.close();
?>