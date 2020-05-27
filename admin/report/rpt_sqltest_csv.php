<?php
    include("../../php_scripts/connect_database.php");
    include ("../php_scripts/function_share.php");
    //phpinfo();
    //die();
    //Create function write CSV is for PHP version low 5.0.0
    function fputcsv2 ($fh, $fields, $delimiter = ',', $enclosure = '"', $mysql_null = false) {
        $delimiter_esc = preg_quote($delimiter, '/');
        $enclosure_esc = preg_quote($enclosure, '/');

        $output = array();
        foreach ($fields as $field) {
            if ($field === null && $mysql_null) {
                $output[] = 'NULL';
                continue;
            }

            $output[] = preg_match("/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field) ? (
                $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure
            ) : $field;
        }

        fwrite($fh, join($delimiter, $output) . "\n");
    }

    /*CSV Begin*/
    $db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    ini_set("max_execution_time",0);
    ini_set("memory_limit","9999M"); 
    $heading_text = (array) null;
    $count_data = $db_dpis->send_cmd($sel_cmd);
    $data = $db_dpis->get_array();
    $data = array_change_key_case($data, CASE_LOWER);
    foreach($data as $key => $val) {
        $heading_text[] = strtoupper($key);
        //echo strtoupper($key).'<br>';
    }
    //$heading_text[]=PHP_EOL;
    //die();
    $filename_csv="datacsv_c0706sql.csv";  
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$filename_csv); 
    $output = fopen('php://output', 'w');
    $column_name=$heading_text;

    if (version_compare(PHP_VERSION, '5.0.0', '<')) {
        // php version isn't high enough
        fputcsv2($output, $column_name);
    }else{
        fputcsv($output, $column_name);
    }

    $db_dpis2->send_cmd($sel_cmd); 
    while($data = $db_dpis2->get_array()){
        $arr_data = (array) null;
        foreach($data as $key => $val) {
            $arr_data[] = $val;
        }
        if (version_compare(PHP_VERSION, '5.0.0', '<')) {
            // php version isn't high enough
            fputcsv2($output, $arr_data);
        }else{
            fputcsv($output, $arr_data);
        }
    }
    /*CSV End*/
?>