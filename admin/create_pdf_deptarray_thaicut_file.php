<?php
// outputs : php include file เก็บ array ของ ชื่อสำนักงานต่าง ๆ

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
		echo "*** สร้าง array ลงใน ../PDF/pdf_deptarray_thaicut.php เรียบร้อยแล้ว<br>";
	} else {
		echo "*** ไม่พบข้อมูลชื่อหน่วยงานไม่สามารถสร้าง array ได้<br>";
	}

?>