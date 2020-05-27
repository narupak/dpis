<html>
<head><title>test mysql</title></head>
<body>

<?php
 	require_once('excelread.php');

 	$config = array('excel_filename'=>'decor.xls',
 					'excel_sheet'=>0,'excel_numeric'=>FALSE,'excel_duplicate'=>FALSE,'excel_sort'=>FALSE,'excel_debug'=>FALSE);

 	$data = excel_read($config);
 
// 	echo excel_table($data);
	echo "<pre>";
	foreach($data as $row) {
		if($inx) {
			$cmd = "insert into per_decoratehis (".implode(",",$fields).") values ('".implode("','",$row)."')";
			echo $cmd . "<br>";
		} else {
			$inx = 1;
			$fields = $row;
		}
	}
	echo "</pre>";
?>

<?php
/*
	require_once('config.php');
	require_once('html.php');
	require_once('view_course.php');

	$config = init_config('mysql',TRUE); // TRUE is Auto Connect to database
	if (!$config ) {
		exit;
	}
	insert($config, $data);

	select($config);

	db_close($config);
	*/
?>

<?php
/*
	function insert($config, $data) {
		db_begin_trans($config);

		foreach ($data as $rows) {
			$GradeID		    = $rows[0];
			$CourseID    	    = $rows[1];
			$sql = "INSERT INTO course_grade(
						gradeID,courseID
		        	)
		       	 	VALUES ( $GradeID, $CourseID )";

			$config['query'] = $sql ;
			$config = db_query_exec($config);
			if( $config['commit'] != TRUE) {
				break;
			}
		}
		db_end_trans($config);
	}

	function select($config) {
		$query  = "SELECT * ";
		$query .= "FROM course_grade ";
		$query .= "ORDER BY gradeid ";

		$config['query'] = $query;
		$rows = db_query_object($config);

		if (!$rows) {
			exit;
		}

		$params = array();
		foreach ($rows as $rowid=> $value) {
			$data = show_table($rowid, $value, count($rows));
			$params[$rowid] = $data;
			echo $data->html;
		}
	}
*/
?>

</body>
</html>