<?php

include("../../php_scripts/connect_database.php");
include("../php_scripts/function_share.php");
//$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
header("content-type: application/x-javascript; charset=TIS-620");


$on_change='';
if( ($PAGE_AUTH_ADD=="Y" || $PAGE_AUTH_EDIT=="Y") && !$VIEW){
    $on_change='check_salary_type(1, this.value);';
}

echo '<select class="selectbox" name="search_level_no" id="search_level_no" onChange="'.$on_change.'">';
echo '<option value="" '.(($LEVEL_NO=="")?"selected":"").' >== ระดับตำแหน่ง ==</option>';

    if($PER_TYPE=="0"){
        $cmd = " select LEVEL_NO, LEVEL_NAME from PER_LEVEL where  LEVEL_ACTIVE = 1 order by PER_TYPE,LEVEL_SEQ_NO";
    }else{
        $cmd = " select LEVEL_NO, LEVEL_NAME from PER_LEVEL where PER_TYPE = $PER_TYPE and LEVEL_ACTIVE = 1 order by LEVEL_SEQ_NO ";
    }
    
    $db_dpis->send_cmd($cmd);
    while($data = $db_dpis->get_array()){					
        $TMP_LEVEL_NO = $data[LEVEL_NO];
        $TMP_LEVEL_NAME = $data[LEVEL_NAME];
        echo '<option value="'.$TMP_LEVEL_NO.'" '.((trim($LEVEL_NO)==trim($TMP_LEVEL_NO))?"selected":"").' >'.$TMP_LEVEL_NAME.'</option>';
    }    
?>
