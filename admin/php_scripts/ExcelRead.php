<?php

require_once 'reader.php';

function excel_table($data) {
	$html = "<table border='1'>";
	foreach ($data as $row) {
		$html .= "<tr>";
		foreach ($row as $item) {
			$html .= "<td> &nbsp;";
			$html .= $item;
			$html .= "</td>";
		}
		$html .= "</tr>";
	}
	$html .= "</table>";
	return $html;
}

function excel_read($config) {
	if (!isset($config))
		return FALSE;

	if (!isset($config['excel_filename']) )
		return FALSE;

	$excel_file = $config['excel_filename'];
	$sheet 		= 0 ;
	$numeric	= FALSE;

	if ( isset($config['excel_sheet']) )
		$sheet = $config['excel_sheet'];

	if ( isset($config['excel_numeric']) )
		$numeric = $config['excel_numeric'];

	if ( isset($config['excel_duplicate']) )
		$duplicate = $config['excel_duplicate'];

	if ( isset($config['excel_duplicate']) )
		$duplicate = $config['excel_duplicate'];

	if ( isset($config['excel_sort']) )
		$sort = $config['excel_sort'];

	if ( isset($config['excel_debug']) )
		$duplicate = $config['excel_debug'];

	if ( isset($config['excel_lowerfield']) )
		$field = $config['excel_lowerfield'];

	return excel_showall($excel_file, $sheet, $numeric, $duplicate, $sort, $field, $debug);
}

function excel_showall($excel_file, $sheet=0, $numeric=FALSE,
                       $duplicate=FALSE, $sort=TRUE,
                       $lowerfield=FALSE, $debug=FALSE) {
	$data = new Spreadsheet_Excel_Reader();
	$data->setOutputEncoding('tis-620');
	$data->read($excel_file); //'Senator.xls'
	error_reporting(E_ALL ^ E_NOTICE);
	//var_dump($data);
	//show filename
	echo "<font color='green'><b>$excel_file</b></font><br />";

	$rows = $data->sheets[$sheet]['numRows'];
	$cols = $data->sheets[$sheet]['numCols'];


	echo "Rows: ".$rows . ", ";
	echo "Cols: ".$cols . ", ";

	$keys   = array();
	$result = array();

	for ($i = 1; $i <= $rows; $i++) {
		$col   = array();
		$key   = '';
		$comma = '';
		$pass  = TRUE;

		for ($j = 1; $j <= $cols; $j++) {
			$item = $data->sheets[$sheet]['cells'][$i][$j];
			if ($numeric===TRUE) {
				if ( is_numeric($item) && $pass  ) {
					$pass = TRUE;
				} else {
					$pass = FALSE;
				}
			}

			//if (is_numeric($lowerfield) && $j==$lowerfield ) {
			if (is_array($lowerfield) && in_array($j , $lowerfield)) {
				//echo $j.';'.$lowerfield.$item.'<br/>';
				$col[] = strtolower( $item );
			}
			else
				$col[] = $item;

			$key  .= $item;
			$comma = ',';
		}

		if ($pass) {
			if (!$duplicate) {
				if (!array_key_exists($key , $keys)) {
					$keys[$key]   = $col;
					$result[$key] = $col;
                                        //echo $i." duplicate "."<br>";
				}
                                
			} else
				$result[$key] = $col;
                        
                        //echo $i." >>> "."<br>";
		}
	}
	$total = "Total: ".count($result) . "<br />";
	if ($rows != count($result) )
		$total = "<font color='red'><b>$total</b></font>";

	echo $total;

	if ($sort)
		asort($result);

	if ($debug) {
		var_dump($result);
	} else
		echo $html;
		
	return $result;
}



?>
