<?php
// outputs : php include file �� array �ͧ �����ӹѡ�ҹ��ҧ �

	include("../php_scripts/connect_database.php");
	
	$filename = "../PDF/pdf_deptarray_thaicut.php";
//	if (file_exists($filename)) {
//		echo "$filename was last changed: " . date("F d Y H:i:s.", filectime($filename));
//	}

	$cmd = " select distinct ORG_NAME from PER_ORG 
					where OL_CODE in ('02', '03','04','05') and ORG_ACTIVE=1 and ORG_NAME != '-'
					order by ORG_NAME ";

	$count_data = $db_dpis->send_cmd($cmd);
	echo "<br>\$count_data=$count_data<br>";
	$arr_data = (array) null;
	if($count_data){
		while($data = $db_dpis->get_array()){
			$arr_data[] = "'".trim($data['ORG_NAME'])."'";
		}
		$arr_string = implode(",",$arr_data);
		$string = "<?php \$deptword = array(".$arr_string.");	?>";
		
		$fp = fopen($filename, "w");
		fwrite($fp, $string);
		fclose($fp);
		echo "*** ���ҧ array ŧ� ../PDF/pdf_deptarray_thaicut.php ���º��������<br>";
	} else {
		echo "*** ��辺�����Ū���˹��§ҹ�������ö���ҧ array ��<br>";
	}

?>