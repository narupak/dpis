<?php

include("../../php_scripts/connect_database.php");
include("../php_scripts/function_share.php");
//$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
header("content-type: application/x-javascript; charset=TIS-620");

$on_change='';
$func='show_poskp7();';
if($frompage=='S02'){
    $func='';
}
if( ($PAGE_AUTH_ADD=="Y" || $PAGE_AUTH_EDIT=="Y") && !$VIEW){
    $on_change=$func.'check_salary_type(1, this.value);';
}

echo '<select class="selectbox" name="LEVEL_NO" id="LEVEL_NO" onChange="'.$on_change.'">';
echo '<option value="" '.(($LEVEL_NO=="")?"selected":"").' >== ระดับตำแหน่ง ==</option>';
    $where = "";
    $cmd = " SELECT LEVEL_NO_MIN, LEVEL_NO_MAX 
            FROM PER_CO_LEVEL 
            WHERE trim(CL_NAME)='".trim($CL_CODE)."' ";
    $count_per_line = $db_dpis->send_cmd($cmd);
//					echo "cmd=$cmd ($count_data)<br>";
    if ($count_per_line) {
        $data = $db_dpis->get_array();
        $LEVEL_NO_MIN = trim($data[LEVEL_NO_MIN]); 
        $LEVEL_NO_MAX = trim($data[LEVEL_NO_MAX]); 
        $where = "";
        if ($LEVEL_NO_MIN)
            $where .= " and LEVEL_NO >= '$LEVEL_NO_MIN' ";
        if ($LEVEL_NO_MAX)
            $where .= " and LEVEL_NO <= '$LEVEL_NO_MAX' ";
    }

    $cmd = " select LEVEL_NO, LEVEL_NAME from PER_LEVEL where PER_TYPE = $PER_TYPE and LEVEL_ACTIVE = 1 $where order by LEVEL_SEQ_NO ";
    $db_dpis->send_cmd($cmd);
    while($data = $db_dpis->get_array()){					
        $TMP_LEVEL_NO = $data[LEVEL_NO];
        $TMP_LEVEL_NAME = $data[LEVEL_NAME];
        echo '<option value="'.$TMP_LEVEL_NO.'" '.((trim($LEVEL_NO)==trim($TMP_LEVEL_NO))?"selected":"").' >'.$TMP_LEVEL_NAME.'</option>';
    }    
?>
