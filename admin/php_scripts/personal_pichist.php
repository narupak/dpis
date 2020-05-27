<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
        
	$UPDATE_DATE = date("Y-m-d H:i:s");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if(!$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

	$img_success = "";
	$img_error = "";

	// C06 ��˹��� IP  ������ҧ  ##xxx.xxx.xx.xxx   {�ٻ�Ҿ}  (᷹ # ���� \)

	// ���º��º�Ң�Ҵ��� (˹��� MB) (�٨ҡ��Ҵ���������ö�Ѿ��Ŵ���٧�ش��� C06)
	function compare_mfsize($fs_bytes,$maxsizeMB_up_file) {
		$MAX_FILE_SIZE = number_format($maxsizeMB_up_file, 2);
		$img_error = "�������ö Upload �� ��Ҵ����Թ ".$maxsizeMB_up_file." MB";
		if ($fs_bytes < 1000 * 1024){		//bytes to KB
			//return number_format($fs_bytes / 1024, 2) . " KB";
			// test
			/*if(number_format($fs_bytes / 1024, 2) > $MAX_FILE_SIZE){
				return $img_error;
			}else{
				return "";
			}*/
			return "";
		}elseif ($fs_bytes < 1000 * 1048576){	//bytes to MB
			if(number_format($fs_bytes / 1048576, 2) > $MAX_FILE_SIZE){
				return $img_error;
			}else{
				return "";
			}
		}elseif ($fs_bytes < 1000 * 1073741824){	//bytes to GB
			if(number_format($fs_bytes / 1073741824, 2) > $MAX_FILE_SIZE){
				return $img_error;
			}else{
				return "";
			}
		}else{	//bytes to TB
			if(number_format($fs_bytes / 1099511627776, 2) > $MAX_FILE_SIZE){
				return $img_error;
			}else{
				return "";
			}
		}
	} // end function

	if($PER_ID){
		if($DPISDB=="odbc"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_TYPE, PER_PERSONAL.PER_CARDNO,PER_PERSONAL.DEPARTMENT_ID
 					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and 
							PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		} // end if
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_GENNAME = personal_gen_name($data[PER_NAME],$data[PER_SURNAME]);
		$PER_GENNAME = "9999".$PER_GENNAME;
		$PER_CARDNO = (trim($data[PER_CARDNO]))? trim($data[PER_CARDNO]) : "NULL";
//		echo "----> $cmd--(PER_CARDNO=$PER_CARDNO)<br>";
	} // end if

/***
	if (!$server_id) $server_id = 0;	

	if ($server_id && $server_id > 0) {
		$cmd = " select * from OTH_SERVER where SERVER_ID=$server_id ";
		$db_dpis->send_cmd($cmd);
//		echo "0---$cmd<br>";
		$data = $db_dpis->get_array();
		$FTP_SERVER_NAME = trim($data[SERVER_NAME]);
		$ftp_server = trim($data[FTP_SERVER]);
		$ftp_username = trim($data[FTP_USERNAME]);
		$ftp_password = trim($data[FTP_PASSWORD]);
		$main_path = trim($data[MAIN_PATH]);
		$http_server = trim($data[HTTP_SERVER]);
	} else {
//		echo "0---not read server ($server_id)<br>";
		$FTP_SERVER_NAME = "This";
		$ftp_server = "";
		$ftp_username = "";
		$ftp_password = "";
		$main_path = "";
		$http_server = "";
//		$PIC_PATH = "../attachment/pic_personal/";	
	}
***/

	if($PIC_SIGN=="")	$PIC_SIGN=0;
	$server_id	= $server_select;
	if ($server_id && ($server_id > 0 && $server_id!=99)) {	// server_id 99 = ip �ҡ��駤���к� C06
		$cmd = " select * from OTH_SERVER where SERVER_ID=$server_id ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$FTP_SERVER_NAME = trim($data[SERVER_NAME]);
		$ftp_server = trim($data[FTP_SERVER]);
		$ftp_username = trim($data[FTP_USERNAME]);
		$ftp_password = trim($data[FTP_PASSWORD]);
		$main_path = trim($data[MAIN_PATH]);
		$http_server = trim($data[HTTP_SERVER]);
		/*$main_path = str_replace("#","'",$main_path);
		$main_path = addslashes($main_path);
		$main_path = str_replace("'","",$main_path);*/
	}
        if($PIC=="0"){
            $PIC_SIGN="0";
        }else{ //1
            $PIC_SIGN="1";
        }
	if($server_id>0){
			if($server_id==99){		// server_id 99 = ip �ҡ��駤���к� C06				 �� \			$IMG_PATH_DISPLAY = IP ONLY
				if($PIC_SIGN==1){	//����Ѻ�ٻ���ૹ��	
					$PIC_PATH = "attachments#".$PER_CARDNO."#PER_SIGN#";		//	$IMG_PATH_DISPLAY."#".$PER_CARDNO."#PER_SIGN#";
				}else{
					$PIC_PATH = "attachment#pic_personal#";										//	$IMG_PATH_DISPLAY."#pic_personal#";	
				}
			}else{		// other server
				if($PIC_SIGN==1){	//����Ѻ�ٻ���ૹ��	
					$PIC_PATH = $FTP_SERVER_NAME."#".$PER_CARDNO."#PER_SIGN#";
				}else{
					$PIC_PATH = $FTP_SERVER_NAME."#pic_personal#";	
				}
			}
	}else{	// 0 ==THIS== LOCAL HOST									 �� /
			if($PIC_SIGN==1){	//����Ѻ�ٻ���ૹ��	
				$PIC_PATH = "../attachments/".$PER_CARDNO."/PER_SIGN/";	
			}else{
				$PIC_PATH = "../attachment/pic_personal/";	
			}
	}
	
	//}
	if (!$PIC_PATH){
		if($PIC_SIGN==1){	//����Ѻ�ٻ���ૹ��	
			$PIC_PATH = "../attachments/".$PER_CARDNO."/PER_SIGN/";	
		}else{
		 	$PIC_PATH = "../attachment/pic_personal/";	
		}
	}
//	echo "server_select ===> $server_id, $FTP_SERVER_NAME, $ftp_server, $ftp_username, $ftp_password, $main_path, $http_server<br>";
/* �ѧ��� OK
	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_PERSONALPIC set AUDIT_FLAG = 'N' where PER_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_PERSONALPIC set AUDIT_FLAG = 'Y' where PER_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��駤�ҡ�õ�Ǩ�ͺ������");
	}
*/
	if($command=="DELETE" && $PER_ID && $PIC_SEQ){
		$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID AND PER_PICSEQ=$PIC_SEQ ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_GENNAME = trim($data[PER_GENNAME]);
		$PIC_PATH = trim($data[PER_PICPATH]);
		$T_PIC_SEQ = substr("000",0,3-strlen("$PIC_SEQ"))."$PIC_SEQ";
		$N_PIC_NAME=$PIC_PATH.($PER_CARDNO=="NULL"?$PER_GENNAME:$PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
		
		$cmd = " DELETE from PER_PERSONALPIC where PER_ID=$PER_ID AND PER_PICSEQ=$PIC_SEQ ";
		$db_dpis->send_cmd($cmd);
		if($N_PIC_NAME){
			unlink($N_PIC_NAME);
			$img_success = "ź�ٻ [$N_PIC_NAME]";
		}else{
			$img_error = "�������öź�ٻ��";
		} // end if
		/*cdgs */
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm99 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd="delete psst_person_image where id=".f_get_personID($PER_ID);
		$db_opm99->send_cmd($cmd);
		$db_opm99->free_result();
		$db_opm99->close();
		/*cdgs*/
		
		/* $cmd1 = " select PER_ID from PER_PERSONALPIC where PER_ID = $PER_ID and PER_PICSEQ = $PIC_SEQ "; 
		$count_data = $db_dpis->send_cmd($cmd1);
		if ($count_data) echo "delete......$cmd<br>"; */
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź�������ٻ�Ҿ [$PER_ID : $PIC_SEQ]");
	} // end if

//	echo "command=$command , PER_ID=$PER_ID , PER_PICSEQ=$PIC_SEQ<br>";
	if($command=="UPDATE" && $PER_ID && $PIC_SEQ){
            
		$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID AND PER_PICSEQ=$PIC_SEQ ";
		if ($db_dpis->send_cmd($cmd)) { 
			$data = $db_dpis->get_array();
			$O_PIC_SHOW = trim($data[PIC_SHOW]);
                        if($PIC=="0"){
                            $PIC_SIGN="0";
                        }else{ //1
                            $PIC_SIGN="1";
                        }
			if ($O_PIC_SHOW=="1" && $PIC_SHOW!="1")  $change_show="NoShow";
			else if ($O_PIC_SHOW==$PIC_SHOW) 	$change_show="NoChange";
			else $change_show="Show";
//			echo "UPD...PIC_SEQ=$PIC_SEQ , O_PIC_SHOW=$O_PIC_SHOW , PIC_SHOW=$PIC_SHOW , change_show=$change_show<br>";

                        /*���...*/
			/*if ($change_show=="Show") {
				$cmd = "update PER_PERSONALPIC set PIC_SHOW='0' where PER_ID=$PER_ID and PIC_SHOW='1'";
				$db_dpis->send_cmd($cmd);
			}*/
                        /*-------------------*/
                        
                        /*Release 5.1.0.4 Begin*/
                        /*$cmd = "UPDATE PER_PERSONALPIC 
                                        SET PIC_SHOW='0' 
                                WHERE PER_ID=$PER_ID AND PIC_SIGN= '$PIC_SIGN' ";
				$db_dpis->send_cmd($cmd);*/
                        /*Release 5.1.0.4 End*/
                        
                        /*Release 5.2.1.11 Begin*/ 
                        if(!$PIC_SHOW){$PIC_SHOW='0';}//new
                                $cmd = "SELECT PER_PICSEQ,NVL(PIC_SHOW,0) AS PIC_SHOW FROM PER_PERSONALPIC WHERE PER_ID=$PER_ID AND PIC_SIGN= '$PIC_SIGN' ";
                                $db_dpis2->send_cmd($cmd);
                                while ($dataDB = $db_dpis2->get_array()) {
                                    $PER_PICSEQDB=$dataDB[PER_PICSEQ];
                                    $PIC_SHOWDB=$dataDB[PIC_SHOW];
                                    //echo $PER_PICSEQDB.'=='.$PIC_SEQ.'>>>PIC_SHOWDB='.$PIC_SHOWDB.','.$PIC_SHOW.'<br>';
                                    if($PER_PICSEQDB==$PIC_SEQ){
                                        if($PIC_SHOW!="1"){
                                            $cmd = "UPDATE PER_PERSONALPIC 
                                                    SET PIC_SHOW='0' 
                                            WHERE PER_ID=$PER_ID AND PER_PICSEQ=$PER_PICSEQDB ";
                                            $db_dpis->send_cmd($cmd);
                                        }
                                    }else{
                                        if($PIC_SHOW!="1"){
                                        }else{
                                            $cmd = "UPDATE PER_PERSONALPIC 
                                                    SET PIC_SHOW='0' 
                                            WHERE PER_ID=$PER_ID AND PER_PICSEQ=$PER_PICSEQDB ";
                                            $db_dpis->send_cmd($cmd);
                                        } 
                                    }
                                }
                        /*Release 5.2.1.11 End*/
                                
                                
                                
                        
			if (!$server_id) $server_id = 0;	//"NULL";
                        
			$cmd = " UPDATE PER_PERSONALPIC SET 
							PER_PICPATH='$PIC_PATH', 
							PIC_SHOW='$PIC_SHOW', 
							PIC_REMARK='$PIC_REMARK',
							PIC_SERVER_ID=$server_id,
							PIC_SIGN=$PIC_SIGN, 
							PIC_YEAR='$PIC_YEAR',
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE' 
							WHERE PER_ID=$PER_ID AND PER_PICSEQ=$PIC_SEQ ";
			$db_dpis->send_cmd($cmd);
                        /*Release 5.1.0.4 Begin*/
                        /*update ੾�������*/
                       /* $cmd="UPDATE PER_PERSONALPIC SET PIC_SHOW=1 
                            WHERE  PER_ID=$PER_ID AND pic_sign=1 ";*/
                        /*Release 5.1.0.4 End*/
                        
//			echo "update-$cmd<br>";
		} else {
			$img_error = "�������ö��䢢������ٻ��";
		}
		$UPD=1;
		$command="UPDATEIMG";	//������͡�ٻ�����ǡ�������䢢�����
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢻���ѵ��ٻ�Ҿ [$PER_ID : $PIC_SEQ]");
	} // end if

	if($command=="ADD" && $PER_ID){
            if($PIC=="0"){
                $PIC_SIGN="0";
            }else{ //1
                $PIC_SIGN="1";
            }
		$cmd2="select PER_PICSEQ from PER_PERSONALPIC  where PER_ID=$PER_ID and PER_PICSEQ=$PIC_SEQ ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("�������ö������������ ���ͧ�ҡ�س�к��ӴѺ����ٻ�Ҿ��� !!!");
				-->   </script>	<? 
		} else {	  
			$cmd = " select max(PER_PICSEQ) as max_id from PER_PERSONALPIC where PER_ID=$PER_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$PIC_SEQ = $data[max_id] + 1;

			$T_PIC_SEQ = substr("000",0,3-strlen("$PIC_SEQ"))."$PIC_SEQ";
			if($PIC_SIGN==1){	//����Ѻ�ٻ���ૹ��	
				$PIC_NAME = $PIC_PATH.($PER_CARDNO=="NULL"?$PER_GENNAME:$PER_CARDNO)."-SIGN".$T_PIC_SEQ.".jpg";
			}else{
				$PIC_NAME = $PIC_PATH.($PER_CARDNO=="NULL"?$PER_GENNAME:$PER_CARDNO).$T_PIC_SEQ.".jpg";
			}
			if ($PIC_SHOW=="1") $change_show="Show"; else $change_show="NoShow";

			/*���*/
                        /*if ($change_show=="Show") {
				$cmd = " UPDATE PER_PERSONALPIC SET PIC_SHOW=''	
								WHERE PER_ID=$PER_ID AND PIC_SHOW='1' ";
				$db_dpis->send_cmd($cmd);
			}*/
                        if($PIC=="0"){
                            if($PIC_SHOW=="1"){
                                $cmd = " UPDATE PER_PERSONALPIC SET PIC_SHOW='' WHERE PER_ID=$PER_ID AND PER_PICPATH NOT LIKE '%PER_SIGN%' ";
                            }
                        }else{ //1
                            if($PIC_SHOW=="1"){
                                $cmd = " UPDATE PER_PERSONALPIC SET PIC_SHOW='' WHERE PER_ID=$PER_ID AND PER_PICPATH  LIKE '%PER_SIGN%' ";
                            }
                            
                        } 
                        if($PIC_SHOW=="1"){
                            $db_dpis->send_cmd($cmd);
                        }
                        
			//�������� PIC_SERVER_ID �Ѻ PIC_SIGN 
			if (!$server_id) $server_id = 0;	//"NULL";
                        
			$cmd = " insert into PER_PERSONALPIC (PER_ID, PER_PICSEQ, PER_CARDNO, PER_PICPATH, PER_PICSAVEDATE, PIC_REMARK, 
							PIC_SHOW, PER_GENNAME, PIC_SERVER_ID, PIC_SIGN, PIC_YEAR, UPDATE_USER, UPDATE_DATE)
							values ($PER_ID, $PIC_SEQ, $PER_CARDNO, '$PIC_PATH', '', '$PIC_REMARK', '$PIC_SHOW', '$PER_GENNAME', $server_id,
							$PIC_SIGN, '$PIC_YEAR', $SESS_USERID, '$UPDATE_DATE')   ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			/* $cmd1 = " select PER_ID from PER_PERSONALPIC where PER_ID = $PER_ID and PER_PICSEQ = $PIC_SEQ "; 
			$count_data = $db_dpis->send_cmd($cmd1);
			if (!$count_data) echo "insert......$cmd<br>"; */
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��������ѵ��ٻ�Ҿ [$PER_ID : $PIC_SEQ]");
			$ADD_NEXT = 1;
		} // end if
		$UPD=1;
		$command="UPDATEIMG";	//������͡�ٻ����ҵ����͹�á�������������
	}
	//echo " ADD=> $cmd<br>";

	//script upload image (��ѧ�ҡ����������ŧ DB ����)
	// - � host ���ǡѹ ���� drive
	// -  
	if($command=="UPDATEIMG" && $PER_ID){
//		echo "============= $PIC_SIGN (PIC_SEQ=$PIC_SEQ)============<br>";
		if($PIC_SIGN==1){	//����Ѻ�ٻ���ૹ��
			$cmd = " select * from PER_PERSONALPIC where (PER_ID=$PER_ID AND PER_PICSEQ=$PIC_SEQ  and PIC_SIGN=1) ";
			$db_dpis->send_cmd($cmd);
			if ($data = $db_dpis->get_array()) {
				$PER_CARDNO = trim($data[PER_CARDNO]);
				$PER_GENNAME = trim($data[PER_GENNAME]);
//				echo "IMG:$cmd (PER_CARDNO=$PER_CARDNO)<br>";
				$O_PIC_SHOW = trim($data[PIC_SHOW]);
				if ($O_PIC_SHOW=="1" && $PIC_SHOW!="1")  $change_show="NoShow";
				else if ($O_PIC_SHOW==$PIC_SHOW) 	$change_show="NoChange";
				else $change_show="Show";
				$PIC_SIGN = trim($data[PIC_SIGN]);
				$T_PIC_SEQ = substr("000",0,3-strlen("$PIC_SEQ"))."$PIC_SEQ";
				$f_upd=true;
			} else {
				$cmd = " select max(PER_PICSEQ) as max_id from PER_PERSONALPIC where PER_ID=$PER_ID ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				$PIC_SEQ = $data[max_id] + 1;
				if ($PIC_SHOW=="1") $change_show="Show"; else $change_show="NoShow";
				$T_PIC_SEQ = substr("000",0,3-strlen("$PIC_SEQ"))."$PIC_SEQ";
				$f_upd=false;
			}
			if (!$PER_CARDNO){
				$cmd = " select PER_CARDNO from PER_PERSONAL where PER_ID=$PER_ID";		//�Ң�����
				//echo "IMG:$cmd<br>";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$PER_CARDNO = trim($data[PER_CARDNO]);
//				echo "1....PER_CARDNO=$PER_CARDNO ($cmd)<br>";
			}
			$N_PIC_NAME=$PIC_PATH.($PER_CARDNO=="NULL"?$PER_GENNAME:$PER_CARDNO)."-SIGN".$T_PIC_SEQ.".jpg";
			$T_PIC_NAME=$PIC_PATH."tmp_".($PER_CARDNO=="NULL"?$PER_GENNAME:$PER_CARDNO)."-SIGN".$T_PIC_SEQ.".jpg";
		}else{	//Ṻ����ٻ�Ҿ �ؤ�� ����
			$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID AND PER_PICSEQ=$PIC_SEQ ";
	//		echo "IMG:$cmd<br>";
			if ($db_dpis->send_cmd($cmd)) { 
				$data = $db_dpis->get_array();
				$PER_CARDNO = trim($data[PER_CARDNO]);
				$PER_GENNAME = trim($data[PER_GENNAME]);
				$PIC_PATH = trim($data[PER_PICPATH]);
				$O_PIC_SHOW = trim($data[PIC_SHOW]);
				if ($O_PIC_SHOW=="1" && $PIC_SHOW!="1")  $change_show="NoShow";
				else if ($O_PIC_SHOW==$PIC_SHOW) 	$change_show="NoChange";
				else $change_show="Show";
//				echo "UPDIMAGE...PIC_SEQ=$PIC_SEQ , O_PIC_SHOW=$O_PIC_SHOW , PIC_SHOW=$PIC_SHOW , change_show=$change_show<br>";
				$T_PIC_SEQ = substr("000",0,3-strlen("$PIC_SEQ"))."$PIC_SEQ";
				$N_PIC_NAME=$PIC_PATH.($PER_CARDNO=="NULL"?$PER_GENNAME:$PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
				$T_PIC_NAME=$PIC_PATH."tmp_".($PER_CARDNO=="NULL"?$PER_GENNAME:$PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
				$f_upd=true;
			} else {
				$cmd = " select max(PER_PICSEQ) as max_id from PER_PERSONALPIC where PER_ID=$PER_ID ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				$PIC_SEQ = $data[max_id] + 1;
				if ($PIC_SHOW=="1") $change_show="Show"; else $change_show="NoShow";
				$T_PIC_SEQ = substr("000",0,3-strlen("$PIC_SEQ"))."$PIC_SEQ";
				$N_PIC_NAME=$PIC_PATH.($PER_CARDNO=="NULL"?$PER_GENNAME:$PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
				$T_PIC_NAME=$PIC_PATH."tmp_".($PER_CARDNO=="NULL"?$PER_GENNAME:$PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
	//			echo "N_PIC_NAME=$N_PIC_NAME:pic_show=$PIC_SHOW<br>";
				$f_upd=false;
			}
		}
//		echo "<hr>$PIC_SIGN +++ N_PIC_NAME=$N_PIC_NAME, PERSONAL_PIC=$PERSONAL_PIC, server : $ftp_server && $ftp_username && $ftp_password && $PIC_PATH<br>";

		// �� # �ó� server ��� ����¹ # ����� \ ������㹡���Ѿ��Ŵ�ٻ
		$N_PIC_NAME = str_replace("#","'",$N_PIC_NAME);
		$T_PIC_NAME =  str_replace("#","'",$T_PIC_NAME);
		$N_PIC_NAME = addslashes($N_PIC_NAME);
		$T_PIC_NAME = addslashes($T_PIC_NAME);
		$N_PIC_NAME = str_replace("'","",$N_PIC_NAME);
		$T_PIC_NAME =  str_replace("'","",$T_PIC_NAME);

		if($N_PIC_NAME){
			if (strlen($ftp_server) > 0 && strlen($ftp_username) > 0 && strlen($ftp_password) > 0) {
				// the file name that should be uploaded
				$filep=$_FILES['PERSONAL_PIC']['tmp_name']; 
				$name=$_FILES['PERSONAL_PIC']['name'];	
				//echo "1 : filep=$filep, name=$name<br>";
				$filep=str_replace("#","'",$filep);
				$filep = addslashes($filep);
				$filep = str_replace("'","",$filep);
						
				$name=str_replace("#","'",$name);
				$name = addslashes($name);
				$name = str_replace("'","",$name);
//echo "<br>2 : filep=$filep, name=$name<br>";

				if ($name) {
					$filepath = $main_path;

					$conn_id = ftp_connect($ftp_server);        // set up basic connection
//					#print_r($conn_id);
					$login_result = ftp_login($conn_id, $ftp_username, $ftp_password);

//					echo "	$ftp_server --->  $conn_id // $login_result  = $conn_id, $ftp_username, $ftp_password";
					
//					login with username and password, or give invalid user message
					if ((!$conn_id) || (!$login_result)) {  // check connection
						// wont ever hit this, b/c of the die call on ftp_login
						$img_error = "�Դ��� $ftp_server ���¼����������ʼ�ҹ $ftp_username/$ftp_password ��������<br>";
					} else {
						// echo "Connected to $ftp_server, for user $ftp_user_name <br />";
						// upload the file
						$upload = @ftp_put($conn_id, $filepath."".$N_PIC_NAME, $filep, FTP_BINARY);
 
						// check upload status
						if (!$upload) {
							$img_error = "�������ö Upload $filepath"."$N_PIC_NAME ($filep) ��<br>";
						} else {
							$img_success = "Upload $filepath"."$N_PIC_NAME 价�� $ftp_server ���º��������<br>";
						}
					}
				} // end if ($name)
			} else {
				if($PIC_SIGN==1 && !is_dir($PIC_PATH)){	//����Ѻ�ٻ���ૹ��
				
					//����ѧ����� folder ��ͧ���ҧ�����͹  --->  
					//<THIS> ../attachments/NNNNNNNNNNNNN/PER_SIGN/    [ 4]
					//<OTH>  SERVER___/NNNNNNNNNNNNN/  [< 4]
					$arr_tmp= explode("/",$PIC_PATH);
					if(count($arr_tmp)<4)	$first_dir = trim($arr_tmp[0]."/".$arr_tmp[1]);		//OTH
					else		$first_dir = trim($arr_tmp[0]."/".$arr_tmp[1]."/".$arr_tmp[2]);			// THIS
					if (!file_exists($first_dir)) {
						$mode = 0755;
						$result = mkdir($first_dir,$mode);
						
					}
                                        if (!file_exists($PIC_PATH)) {
                                                $mode = 0755;
                                                $result = mkdir($PIC_PATH,$mode);
                                        }
				} //end if
				
				if(is_file($PERSONAL_PIC)&&is_uploaded_file($PERSONAL_PIC)){
					$tmp_filename = $T_PIC_NAME;
					move_uploaded_file($PERSONAL_PIC, $tmp_filename);	
					$arr_img_attr = getimagesize($tmp_filename);
					if(filesize($tmp_filename)>0 && $maxsize_up_file)		$img_error = compare_mfsize(filesize($tmp_filename),$maxsize_up_file);
					
//					echo "<pre>"; print_r($arr_img_attr); echo "</pre>";
					switch($arr_img_attr[2]){
						case IMAGETYPE_GIF :																	// 1
							$img = @imagecreatefromgif($tmp_filename);
							break;
						case IMAGETYPE_JPEG :																// 2
							$img = @imagecreatefromjpeg($tmp_filename);
							break;						
						case IMAGETYPE_PNG :																// 3
							$img = @imagecreatefrompng($tmp_filename);
							break;
						case IMAGETYPE_BMP :																// 6
							$img = @imagecreatefrombmp($tmp_filename);
							break;
						case IMAGETYPE_WBMP :															// 15
							$img = @imagecreatefromwbmp($tmp_filename);
							break;
						default : 
//							echo "NOT SUPPORT IMAGE FORMAT<br>";
							$img = imagecreatefromstring(file_get_contents($tmp_filename));
							unlink($tmp_filename);
							imagepng($img, $tmp_filename);
							imagedestroy($img);
							$img = @imagecreatefrompng($tmp_filename);
					} // end switch case
//					echo "image resource :: $img<br>";
					unlink($tmp_filename);
					$filename = $N_PIC_NAME;
					$convert_success = imagejpeg($img, $filename, 100);
//					echo "convert result :: $convert_success<br>";
					imagedestroy($img);
					$img_success = "����¹�ٻ [$N_PIC_NAME]";
					$PIC_SAVEDATE = date("Y-m-d");
				}/*else{   // ����͡ ���кҧ�����������������´ ������������ٻ�����
					$img_error = "�������ö����¹�ٻ��";
				} // end if */
			} // end if (strlen($ftp_server) > 0 && strlen($username) > 0 && strlen($password) > 0 && strlen($filepath) > 0)
		}else{
			$img_error = "�������ö����¹�ٻ��";
		} // end if
		
		if ($change_show=="Show") {
			$cmd = " UPDATE PER_PERSONALPIC SET PIC_SHOW=''	
							WHERE PER_ID=$PER_ID AND PIC_SHOW='1' ";
			$db_dpis->send_cmd($cmd);
//			echo "update (show)--$cmd<br>";
		}

		if($img_error || $img_success){ 
?>
			<script>
<? 
			if($img_error){ 
?>			alert('<?=$img_error; ?>');
<? 		}else if($img_success){ 
?>			alert('<?=$img_success; ?>');
<? 		} ?>
			</script>
<? 	}
//		echo "img_error=$img_error<br>";
		if(!$img_error){	//$img_success
			if ($f_upd) {
				$cmd = " UPDATE PER_PERSONALPIC SET 
								PER_PICPATH='$PIC_PATH', 
								PIC_SERVER_ID=$server_id, 
								PIC_SHOW='$PIC_SHOW', 
								PIC_REMARK='$PIC_REMARK', 
								PIC_SIGN=$PIC_SIGN,
								PIC_YEAR='$PIC_YEAR', 
								PER_PICSAVEDATE='$PIC_SAVEDATE', 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE' 	
								WHERE PER_ID=$PER_ID AND PER_PICSEQ=$T_PIC_SEQ ";
//				echo "update-$cmd<br>";
			} else {
				$cmd = " insert into PER_PERSONALPIC (PER_ID, PER_PICSEQ, PER_CARDNO, PER_PICPATH, PER_PICSAVEDATE, PIC_REMARK, 
								PIC_SHOW, PER_GENNAME, PIC_SERVER_ID, PIC_SIGN, PIC_YEAR, UPDATE_USER, UPDATE_DATE)
								values ($PER_ID, $T_PIC_SEQ, $PER_CARDNO, '$PIC_PATH', '$PIC_SAVEDATE', '$PIC_REMARK', '$PIC_SHOW', 
								'$PER_GENNAME', $server_id, $PIC_SIGN, '$PIC_YEAR', $SESS_USERID, '$UPDATE_DATE') "; 
//				echo "insert-$cmd<br>";
			}
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			$UPD=1;
			//���êྨ
/*?>
			alert();
			<script>document.form1.submit();</script>			
<?*/
			$picname = explode("/",$N_PIC_NAME);
//			echo "===> $cmd (f_upd=$f_upd) (PERSONAL_PIC=$PERSONAL_PIC) [picname".$picname[count($picname)-1]."]<br>";
//			echo "<meta http-equiv=\"refresh\" content=\"0;URL=personal_pichist.html?PER_ID=".$PER_ID."&PIC_SEQ=".$PER_PICSEQ."&MENU_ID_LV0=".$MENU_ID_LV0."&MENU_ID_LV1=".$MENU_ID_LV1."&MENU_ID_LV2=".$MENU_ID_LV2."&MENU_ID_LV3=".$MENU_ID_LV3."".($MAIN_VIEW?"&MAIN_VIEW=1":"")."&UPD=1&PIC_SIGN=$PIC_SIGN&PIC_NAME=".$picname[count($picname)-1]."&getdate=".date('YmdHis')."&HIDE_HEADER=1\">";	
//			exit;
?>
			<script>
				form1.submit();
			</script>
<? 
		} // end img_error
	} // end if UPDATEIMG
	
	if(($UPD && $PER_ID && $PIC_SEQ) || ($VIEW && $PER_ID && $PIC_SEQ)){
		$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID AND PER_PICSEQ=$PIC_SEQ ";
//		echo "get data ($cmd)<br>";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_GENNAME = trim($data[PER_GENNAME]);
		$PIC_PATH = trim($data[PER_PICPATH]);
		$T_PIC_SEQ = substr("000",0,3-strlen("$PIC_SEQ"))."$PIC_SEQ";
		$PIC_SIGN = trim($data[PIC_SIGN]);
		if($PIC_SIGN==1){	//����Ѻ�ٻ���ૹ��	
			$PIC_NAME = ($PER_CARDNO=="NULL"?$PER_GENNAME:$PER_CARDNO)."-SIGN".$T_PIC_SEQ.".jpg";
		}else{
			$PIC_NAME = ($PER_CARDNO=="NULL"?$PER_GENNAME:$PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
		}
		$PIC_REMARK = trim($data[PIC_REMARK]);
		$PIC_YEAR = trim($data[PIC_YEAR]);
//		$PIC_SAVEDATE = show_date_format($data[PER_PICSAVEDATE], 1);
		$PIC_SAVEDATE = $data[PER_PICSAVEDATE];
		$PIC_SHOW = $data[PIC_SHOW];
		$UPDATE_USER = $data[UPDATE_USER];
		$UPDATE_USER = $data[UPDATE_USER];
		$server_id = $data[PIC_SERVER_ID];
		$server_select = $server_id;
		
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
//	echo "UPD($UPD) , VIEW($VIEW)<br>";

	if( !$UPD && !$VIEW ){
//		echo "not UPD && not VIEW<br>";
		if($IMG_PATH_DISPLAY)	 	$server_select = 99;		//server_id 99 = ip ����ͧ���ٻ (��駤���к� C06)
		unset($PIC_SIGN);
		unset($PIC_SEQ);
		unset($PIC_SHOW);
		unset($PIC_SAVEDATE);
		unset($PIC_REMARK);
		unset($PIC_PATH);
		unset($server_id);
		unset($PIC_YEAR);
	
		unset($PIC_NAME);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>