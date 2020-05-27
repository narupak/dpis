<?
	$arr_word_thai_cut = get_word_thai_cut();
//	if (file_exists("connect_database.php")) include("connect_database.php");
//	else if (file_exists("php_scripts/connect_database.php")) include("php_scripts/connect_database.php");
//	else if (file_exists("../php_scripts/connect_database.php")) include("../php_scripts/connect_database.php");
//	else if (file_exists("../../php_scripts/connect_database.php")) include("../../php_scripts/connect_database.php");

	function get_word_thai_cut(){
		global $DPISDB, $db_dpis;
		
		$cmd = " select * from THAIWORD_FORTHAICUT where WORD_ACTIVE=1 order by WORD_GROUP, THAIWORD ";
//		$cmd = " select * from THAIWORD_FORTHAICUT where THAIWORD like '�ӹѡ%' and WORD_ACTIVE=1 order by WORD_GROUP, THAIWORD ";
//		echo "pdf_wordarray-->$cmd<br>";
		$count_data = $db_dpis->send_cmd($cmd);
	
		$arr_word_thai_cut = (array) null;
		if ($count_data > 0) {
			$grp = "";
			$t_word = (array) null;
			while ($data = $db_dpis->get_array()) {
				$data = array_change_key_case($data, CASE_LOWER);
				if ($data[word_group] != $grp) {
					if ($grp) {
						$arr_word_thai_cut[$grp] = implode(",",$t_word);
					}
					$t_word = (array) null;
					$grp = $data[word_group];
				}
//				$t_word[] = "\"".$data[thaiword]."\"";
				$t_word[] = $data[thaiword];
			}
			if (count($t_word) > 0)
				$arr_word_thai_cut[$grp] = implode("|",$t_word);
		}
/*
		if ($arr_word_thai_cut) {
			foreach ($arr_word_thai_cut as $key => $value) {
				echo "key:$key-->value:$value<br>";
			}
		}
*/
		return $arr_word_thai_cut;
	}
?>