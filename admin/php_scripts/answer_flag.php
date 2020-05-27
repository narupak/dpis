<?
include("php_scripts/function_share.php");
$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$update_date = date("Y-m-d H:i:s");
$update_date = "'$update_date'";
$update_by = $SESS_USERID;
             
if($command == "SETFLAG2"){


			for($x = 0; $x<count($TXTCODE) ; $x++) {
				$data_all = explode("_",$TXTCODE[$x]);
				if($data_all[0]=='N'){
					$id_n	  = $data_all[1];
					$per_id = $data_all[2];
					$cmd ="select per_posdate,POS_ID  from per_personal where per_id='$per_id'";
						$db_dpis3->send_cmd($cmd);
						$dataN = $db_dpis3->get_array();
						$per_postdate = $dataN[PER_POSDATE];
						$pos_id = $dataN[POS_ID];		
						
				    $cmd ="update user_detail set user_flag = 'N' where id =  $id_n"; 
				    $db_dpis2->send_cmd($cmd);	
					if($per_id && $per_postdate && ($pos_id =="")){
					$cmd="update per_personal set per_status = 2 where per_id =$per_id";
					$db_dpis2->send_cmd($cmd);
					}
				}
			}

    $close = 1;
    $returnValue="$close";
    echo "<script language=\"JavaScript\">parent.refresh_opener('$returnValue')</script>";
}
?>