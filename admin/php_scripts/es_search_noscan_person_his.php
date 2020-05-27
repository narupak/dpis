<? 
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
		$UPDATE_DATE = date("Y-m-d H:i:s");
		$UPDATE_DATE_FLAG = date("Y-m-d");

        /* function Åº â¿Ãà´ÍÃì áÅÐ ÅºÃÒÂ¡ÒÃÀÒÂã¹â¿Ãà´ÍÃì¹Ñé¹æ ·Ñé§ËÁ´ */        
	function rrmdir($dir) {
            if (is_dir($dir)) {
              $objects = scandir($dir);
              foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                  if (filetype($dir."/".$object) == "dir") 
                     rrmdir($dir."/".$object); 
                  else unlink   ($dir."/".$object);
                }
              }
              reset($objects);
              rmdir($dir);
              return TRUE;
            }
        }
       /* end function */
        
        
	if($command=="DELETE"){   	
                        $cmd = " select START_DATE , PER_CARDNO  from TA_SET_EXCEPTPER  where REC_ID= $HIDREC_ID";
                        $db_dpis->send_cmd($cmd);
                        $data = $db_dpis->get_array();
                        //$data = array_change_key_case($data, CASE_LOWER);
                        $del_START_DATE = trim($data[START_DATE]);
                        $del_PER_CARDNO = trim($data[PER_CARDNO]);
                //=================================== Åºä¿Åìá¹º ===========================================
                        $PATH_MAIN = "../attachments";
                        $PART_FILE = $PATH_MAIN."/".$del_PER_CARDNO."/TA_SET_EXCEPTPER/";
                        $LAST_DIR = $HIDREC_ID;
                        if (!file_exists($PART_FILE)){ mkdir($PART_FILE);}
                        if (!file_exists($PART_FILE.$LAST_DIR)){ mkdir($PART_FILE.$LAST_DIR);}
                        //**scan file
                        $all_files = scandir($PART_FILE.$LAST_DIR);
                        $FILE_ID_CHK = "";
                        //echo count($all_files).">2";
                        if(count($all_files)>2){
                            for ($i=0;$i<count($all_files);$i++){
                                if($i>1){
                                        $cmd = "select 	ID
                                                                        from 	EDITOR_ATTACHMENT
                                                                        where REAL_FILENAME = '".$all_files[$i]."'";
                                        $db_dpis2->send_cmd($cmd);
                                        $data = $db_dpis2->get_array();
                                        //$data = array_change_key_case($data, CASE_LOWER);
                                        $file_id = $data[ID];
                                    $FILE_ID_CHK .= $file_id.",";
                                }    
                            }
                        }    
//                        var_dump($all_files);
//                        echo "<br>";
//                        var_dump($FILE_ID_CHK);
                        //Åºä¿ÅìÃÒÂ¡ÒÃ¹Õéã¹â¿Ãà´ÍÃì·Ñé§ËÁ´
                        $CHK_REDIR = rrmdir($PART_FILE.$LAST_DIR);
//                        if($CHK_REDIR)echo "DEL SUCCESS";
//                        else echo "DEL NOT SUCCESS";
                           
                        if(trim($FILE_ID_CHK)){
				$FILE_ID_CHK = substr($FILE_ID_CHK,0,-1);
				$cmd_dell = "delete from EDITOR_ATTACHMENT where ID in ($FILE_ID_CHK)";
				$db_dpis->send_cmd($cmd_dell);
                                //insert_log("DELETE $file_namedb FILE");
			}
                        
                //=================================== Åºä¿Åìá¹º ===========================================
                        
                        insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > Åº¢éÍÁÙÅºØ¤¤Å·ÕèäÁèµéÍ§Å§àÇÅÒ [$HIDPER_ID :$HIDREC_ID : $del_START_DATE ]");

                        $cmd = " delete from TA_SET_EXCEPTPER	 where REC_ID=$HIDREC_ID ";
                        $db_dpis->send_cmd($cmd);

                        echo "<script>window.location='../admin/es_search_noscan_person_his.html?PerSonID=$PerSonID&MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";
                         

 	} // end if*/
	
	
  
?>