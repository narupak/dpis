<?
	include("php_scripts/session_start.php");
    include("php_scripts/function_share.php");
    /*cdgs*/
	include("php_scripts/function_gen_user.php");
	/*cdgs*/

        // ���ҧ table
        
         $db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
         
          if($db_type=="mysql") {
            $cmdChk = "SELECT count(*)AS CNT FROM information_schema.tables WHERE  table_name = 'user_detail2' ";
            $db->send_cmd($cmdChk);
            $dataChk = $db->get_array();
            if($dataChk[CNT]=="0"){
                $cmdA = "CREATE TABLE IF NOT EXISTS user_detail2 (
                        id int(11) NOT NULL DEFAULT '0',
                        tel_home varchar(100) DEFAULT NULL,
                        mobile varchar(100) DEFAULT NULL,
                        line_id varchar(100) DEFAULT NULL,
                        update_user int(11) DEFAULT NULL,
                        update_date varchar(19) DEFAULT NULL,
                      PRIMARY KEY (id)
                      )";
                $db->send_cmd($cmdA);
            }
          }else{
            $cmdChk = "SELECT count(*)AS CNT FROM user_tables WHERE  TABLE_NAME = 'USER_DETAIL2'";
            $db_dpis->send_cmd($cmdChk);
            $dataChk = $db_dpis->get_array();
            if($dataChk[CNT]=="0"){
                $cmdA = "CREATE TABLE USER_DETAIL2(
                  ID		NUMBER(11) 	not null,
                  TEL_HOME	VARCHAR2(100),
                  MOBILE		VARCHAR2(100),
                  LINE_ID		VARCHAR2(100),
                  UPDATE_USER	NUMBER(11),
                  UPDATE_DATE 	VARCHAR2(19),
                  CONSTRAINT USER_DETAIL2_PK PRIMARY KEY (ID)
                  )";
                $db_dpis->send_cmd($cmdA);
                $cmdA = 'COMMIT';
                $db_dpis->send_cmd($cmdA);
            }
          }
          /*my*/
         
          
	if($db_type=="mysql") {
		$update_date = "NOW()";
		$update_by = "'$SESS_USERNAME'";
	} elseif($db_type=="mssql") {
		$update_date = "GETDATE()";
		$update_by = $SESS_USERID;
	} elseif($db_type=="oci8" || $db_type=="odbc") {
		$update_date = date("Y-m-d H:i:s");
		$update_date = "'$update_date'";
		$update_by = $SESS_USERID;
	}

	if($command=='UPDATE' ){
            $cmdID =  "select user_link_id from user_detail where id = $SESS_USERID ";
            $db_dpis1->send_cmd($cmdID);
            $data2 = $db_dpis1->get_array();
            $data2 = array_change_key_case($data2, CASE_LOWER);
            $per_idp = $data2[user_link_id];
            //echo "".$per_idp;
		if($old_passwd=="" && $passwd==""){
                    $cmd = "update user_detail set 
                                                                    titlename = TRIM('$titlename'),
                                                                    fullname = TRIM('$user_name'), 
                                                                    address = TRIM('$user_address'), 
                                                                    email = TRIM('$user_email'), 
                                                                    tel = TRIM('$user_tel'), 
                                                                    fax = TRIM('$user_fax'),
                                                                    update_date = $update_date, 
                                                                    update_by = $update_by
                                                            where id=$SESS_USERID";
                            $db->send_cmd($cmd);
                            
                            $cmdP = "update per_personal set per_email = TRIM('$user_email') where per_id=$per_idp";
                            $db->send_cmd($cmdP);
                            //echo "up=>".$cmdP;
                            
                            $cmd = "select * from user_detail2 where id = $SESS_USERID";
                            $count_id_us = $db->send_cmd($cmd);
                            if($count_id_us > 0){
                                $cmd = "update user_detail2 set 
                                                                    tel_home = TRIM('$per_home_tel'),
                                                                    mobile = TRIM('$per_mobile'), 
                                                                    line_id = TRIM('$line_id'),
                                                                    update_user = $update_by, 
                                                                    update_date  = $update_date
                                                            where id=$SESS_USERID";
                                $db->send_cmd($cmd);
                            }else{
                                $cmd = "insert into user_detail2 (ID,TEL_HOME,MOBILE,LINE_ID) 
                                               values ($SESS_USERID,'$per_home_tel','$per_mobile','$line_id')";
                                $db->send_cmd($cmd);
                            } 
                            
                            insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢢����ż����ҹ [$username -> $user_name]");

                            $passwd = "";
                            $confirm_passwd = "";
                        ?>
                            <script>alert("����¹�ŧ���������º��������"); </script>
                        <?
                           
                }else{
                    $old_password = md5($old_passwd);
                    /*cdgs*/
		           //$old_password = md5($old_passwd);
		            // $old_password = encryptPwd($old_passwd);
		            /*cdgs*/
                    $cmd =  " select id from user_detail where username = '$username' and password = '$old_password' ";
                    $count_id = $db->send_cmd($cmd);
                    if($count_id > 0){
                        if($SESS_PASSWORD_DB==$passwd){
                            echo "<script>alert('���ͤ�����ʹ��� ���������¹���ʼ�ҹ�������ѹ��͹���Դ'); window.location.href='user_profile.html?MENU_ID_LV0=12&MENU_ID_LV1=0'; </script>";
                        }else{
                            $allow_update = 1;	 
                            if($passwd) $set_password = ", password = '".md5($passwd)."'";
                            /*cdgs*/
			                //if($passwd) $set_password = ", password = '".md5($passwd)."'";
			                // if($passwd) $set_password = ", password = '".encryptPwd($passwd)."'";
			                /*cdgs*/
                            $cmd = "update user_detail set 
                                                                    username = TRIM('$username') 
                                                                    $set_password,
                                                                    titlename = TRIM('$titlename'),
                                                                    fullname = TRIM('$user_name'), 
                                                                    address = TRIM('$user_address'), 
                                                                    email = TRIM('$user_email'), 
                                                                    tel = TRIM('$user_tel'), 
                                                                    fax = TRIM('$user_fax'),
                                                                    update_date = $update_date, 
                                                                    update_by = $update_by
                                                            where id=$SESS_USERID";
                            $db->send_cmd($cmd);

                            $cmdP = "update per_personal set per_email = TRIM('$user_email') where per_id = $per_idp";
                            $db->send_cmd($cmdP);
                            //echo "down".$cmdP;
                            
                            insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢢����ż����ҹ [$username -> $user_name]");

                            $passwd = "";
                            $confirm_passwd = "";
                            
                            $cmd = "select * from user_detail2 where id = $SESS_USERID";
                            $count_id_us = $db->send_cmd($cmd);
                            if($count_id_us > 0){
                                $cmd = "update user_detail2 set 
                                                                    tel_home = TRIM('$per_home_tel'),
                                                                    mobile = TRIM('$per_mobile'), 
                                                                    line_id = TRIM('$line_id'),
                                                                    update_user = $update_by, 
                                                                    update_date  = $update_date
                                                            where id=$SESS_USERID";
                                $db->send_cmd($cmd);
                            }else{
                                $cmd = "insert into user_detail2 (ID,TEL_HOME,MOBILE,LINE_ID) 
                                               values ($SESS_USERID,'$per_home_tel','$per_mobile','$line_id')";
                                $db->send_cmd($cmd);
                            }
                            ?>
                            <script>alert("����¹�ŧ���������º�������� ��س� Login ����к��ա����"); window.location.href="../index.html"; </script>
                             <?
                        }
                             
                            echo "<meta http-equiv=refresh content='0; url=../index.html'>";

                    }else{ 
                            $allow_update = 0;	
                            $err_text = "�������ö��䢢����ż����ҹ�� ������ʼ�ҹ������١��ͧ";
                    }
                }        
                
	} //end command

	/*$cmd = " select us1.username, us1.group_id, us1.titlename, us1.fullname, us1.address, us1.district_id, 
                    us1.amphur_id, us1.province_id, us1.email, us1.tel, us1.fax, us1.user_link_id,
                    us2.line_id,us2.tel_home,us2.mobile
                from user_detail us1 
                LEFT JOIN USER_DETAIL2 us2 on (us1.id=us2.id)
                where us1.id=$SESS_USERID ";*/
        $cmd = " select us1.username, us1.group_id, us1.titlename, us1.fullname, us1.address, us1.district_id, 
                    us1.amphur_id, us1.province_id, us1.email, us1.tel, us1.fax, us1.user_link_id
                from user_detail us1 
                where us1.id=$SESS_USERID ";
	$db->send_cmd($cmd); //��� $db
	$data = $db->get_array();//��� $db
	$data = array_change_key_case($data, CASE_LOWER);
	$username = $data[username]; 
	$group_id = $data[group_id];
	$titlename = $data[titlename];
	$user_name = $data[fullname];
	$user_code = $data[user_code];
	$user_address = $data[address];
	$district_id = $data[district_id]; 
        $amphur_id = $data[amphur_id]; 
        $province_id = $data[province_id]; 
	$user_email = $data[email];
	$user_tel = $data[tel]; 
        $user_fax = $data[fax];
	$user_link_id = $data[user_link_id];
        
        /*USER_DETAIL2 Begin*/
        $cmdU2="select line_id,tel_home,mobile from USER_DETAIL2 where id=".$SESS_USERID;
        $db->send_cmd($cmdU2);
        $dataU2 = $db->get_array();
        $line_id = '';
        $tel_home = '';
        $mobile = '';
        if($dataU2){
            $dataU2 = array_change_key_case($dataU2, CASE_LOWER);
            $line_id = $dataU2[line_id];
            $tel_home = $dataU2[tel_home];
            $mobile = $dataU2[mobile];
        }
        /*USER_DETAIL2 Begin*/
	if($group_id) :
		$cmd = " select name_th from user_group where id=$group_id ";
		$db->send_cmd($cmd);//��� $db
		$data = $db->get_array();//��� $db
		$data = array_change_key_case($data, CASE_LOWER);
		$user_group = $data[name_th];
	endif; 
	
	if($user_link_id){
		if($DPISDB=="odbc"){
			$cmd = " select 	b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, d.ORG_NAME, f.ORG_NAME as EMP_ORG_NAME
							 from 		(
												(
													(
														(
															PER_PERSONAL a
															left join PER_PRENAME b on (a.PN_CODE=b.PN_CODE)
														) left join PER_POSITION c on (a.POS_ID=c.POS_ID)
													) left join PER_ORG d on (c.ORG_ID=d.ORG_ID)
												) left join PER_POS_EMP e on (a.POEM_ID=e.POEM_ID)
											) left join PER_ORG f on (e.ORG_ID=f.ORG_ID)
							 where 	a.PER_ID=". $user_link_id;
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, d.ORG_NAME, f.ORG_NAME as EMP_ORG_NAME , h.ORG_NAME as EMPSER_ORG_NAME, j.ORG_NAME as TEMP_ORG_NAME ,a.PER_FAX ,a.PER_OFFICE_TEL,a.PER_HOME_TEL,a.PER_MOBILE,a.PER_EMAIL
								 from 		PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_ORG d, PER_POS_EMP e, PER_ORG f , PER_POS_EMPSER g , PER_ORG h , PER_POS_TEMP i , PER_ORG j
								 where 	a.PN_CODE=b.PN_CODE(+) and a.POS_ID=c.POS_ID(+) and c.ORG_ID=d.ORG_ID(+) 
								 				and a.POEM_ID=e.POEM_ID(+) and e.ORG_ID=f.ORG_ID(+) and a.POEMS_ID=g.POEMS_ID(+) and g.ORG_ID=h.ORG_ID(+) and a.POT_ID=i.POT_ID(+) and i.ORG_ID=j.ORG_ID(+)
								 				and a.PER_ID=". $user_link_id;
			
                }                                                                        
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data_dpis = $db_dpis->get_array();
                $per_fax = $data_dpis[PER_FAX];
                $per_home_tel = $data_dpis[PER_HOME_TEL];
                $per_mobile = $data_dpis[PER_MOBILE];
                $per_email = $data_dpis[PER_EMAIL];
               
                $per_office_tel = $data_dpis[PER_OFFICE_TEL];
		$user_name = $data_dpis[PN_NAME] . (trim($data[PN_NAME])?" ":"") . $data_dpis[PER_NAME] ." ". $data_dpis[PER_SURNAME];
		if($data_dpis[PER_TYPE]==1) $user_address = $data_dpis[ORG_NAME];
		elseif($data_dpis[PER_TYPE]==2) $user_address = $data_dpis[EMP_ORG_NAME];
		elseif($data_dpis[PER_TYPE]==3) $user_address = $data_dpis[EMPSER_ORG_NAME];
		elseif($data_dpis[PER_TYPE]==4) $user_address = $data_dpis[TEMP_ORG_NAME];
                
	} // end if
?>