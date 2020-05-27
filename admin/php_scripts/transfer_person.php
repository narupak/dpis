<?
	//include("php_scripts/session_start.php");
	//include("php_scripts/function_share.php");
	include("php_scripts/function_gen_user.php");
	include("php_scripts/function_share_cdgs.php");
	
	function f_new_person($strIDDpis,$strLevelNo,$strEffectDate,$strMoveCode,$strPosIDDpis,$strComID,$strPerType){
		//$strEffectDate yyyy-mm-dd 2015-03-31
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$db_opm2 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		
		// $aa=f_ins_psst_person($strIDDpis);
		$strID=f_get_personID($strIDDpis);
		// $aa=f_upd_psst_position($strIDDpis,$strPosIDDpis,$strComID,$strPerType,$strEffectDate);
	     $cmd = " select lower(name_eng) name_eng,lower(surname_eng) surname_eng,b.org_serial org_serial ,b.pos_id pos_id,
		  				 prefix_code,name,surname ,a.per_type,to_char(nvl(retire_date,sysdate+3650),'yyyy/mm/dd') retire_date,aut_id
			  from psst_person a,psst_position b 
			  where a.id=b.id and a.id=".f_genField($strID,"N"); 
		//echo "$cmd<br>";
		$db_opm->send_cmd($cmd);
		$num_row=$db_opm->num_rows();
		if ($num_row == 1) {
			$data = $db_opm->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$strOrgSerial=$data[org_serial];
			$strRetireDate=$data[retire_date];
			$strPrefixCode=$data[prefix_code];
			$strName=$data[name];
			$strSurName=$data[surname];
			$strPosID=$data[pos_id];
			$strMoveCodeTmp="01";
			$strAutID=$data[aut_id];
			if (substr($strMoveCode,0,2)== "01" || substr($strMoveCode,0,2)== "05") {
				if (substr($strMoveCode,0,2)=="01"){
					$strMoveCodeTmp="01";
				}elseif (substr($strMoveCode,0,2)=="05") {
					$strMoveCodeTmp="10";
				} // end if
			}//end if
			$cmd="delete ctlt_emp where emp_id=".f_genField($strID,"N");
			$db_opm2->send_cmd($cmd);
			
			$strUserCode=genUserID($data[name_eng],$data[surname_eng],$strID);
			//$strUserPwd=encryptPwd($strUserCode);
			$strUserPwd=encryptPwd(strtoupper(substr($strUserCode,0,1)).substr($strUserCode,1,strlen($strUserCode))."#123");
			$strUserPwdLdap=strtoupper(substr($strUserCode,0,1)).substr($strUserCode,1,strlen($strUserCode))."#123";
			$cmd="	insert into ctlt_emp(user_code,emp_id,user_pwd,user_efft_date,user_exp_date)
			values(".f_genField($strUserCode,"S").",".f_genField($strID,"N")."
			,".f_genField($strUserPwd,"S").",".f_genField($strEffectDate,"D2").",".f_genField($strRetireDate,"D2").")";
			$db_opm2->send_cmd($cmd);
			
			global $autMySQL_Connect;
			$conn = new COM("ADODB.Connection") or die("Cannot start ADO");
			$conn->Open($autMySQL_Connect);
			$rsMySQL= new COM("ADODB.Recordset");
			$cmd="select a.person_seq from aut_person a inner join aut_user b on a.person_seq = b.person_seq ";
			$cmd.=" where a.person_id = ".f_genField($strID,"N")." and b.user_id = ".f_genField($strUserCode,"S");
			//echo "$cmd<br>";
			$rsMySQL->Open($cmd,$conn,1,3);
			if (!$rsMySQL->eof){
				//old user_id and role  was existing then update aut_person, aut_user  and delete aut_roleuseraction
				$strPersonSeqTmp=f_ins_aut_person($strID,"N");
				//update aut_user
				$aa=f_ins_aut_user ($strUserCode,$strAutID,$strUserPwdLdap,$strEffectDate,$strRetireDate);
				//delete aut_roleuseraction 
				global $aut_host, $aut_dsn,$aut_user,$aut_pwd;
				$db_aut= new connect_db_aut ($aut_host, $aut_dsn,$aut_user,$aut_pwd);
				$cmd="delete  from aut_roleuseraction where user_id=".f_genField($strUserCode,"S");
				$db_aut->send_cmd($cmd);
				$db_aut->close();
				//	
			}else{
				//new user id or change name_eng or surname_eng then insert aut_user
				$rsMySQL2= new COM("ADODB.Recordset");
				$cmd="select person_seq from aut_person where person_id=".f_genField($strID,"N");
				//echo "$cmd<br>";
				$rsMySQL2->Open($cmd,$conn,1,1);
				if (!$rsMySQL2->eof){
					//old aut_person was existing and replace person_seq with psst_person.aut_id 
					//echo "old aut_person was existing and replace person_seq with psst_person.aut_id <br>";
					$strPersonSeqTmp=f_ins_aut_person($strID,"Y");
				}else{
					// new aut_person
					$strPersonSeqTmp=f_ins_aut_person($strID,"N");
				}
				//new aut_user
				//echo "$strPersonSeqTmp<br>";
				//echo "insert aut_user<br>";
				$aa=f_ins_aut_user ($strUserCode,$strPersonSeqTmp,$strUserPwdLdap,$strEffectDate,$strRetireDate);
				$rsMySQL2->close();
				$rsMySQL2=null;
			}
			$rsMySQL->close();
			$rsMySQL=null;
			
			$cmd="select com_code from ctlt_command_Line where pos_id=".f_genField($strPosID,"S")." and per_type=".f_genField($strPerType,"S");
			$db_opm2->send_cmd($cmd);
			$num_row_opm2=$db_opm2->num_rows();
			if ($num_row_opm2 == 1) {
				$data_opm2 = $db_opm2->get_array();
				$data_opm2 = array_change_key_case($data_opm2, CASE_LOWER);
				$strComCode=$data_opm2[com_code];
							
				$ins=f_ins_system_right($strUserCode,$strOrgSerial,$strComCode);
			} //end if 	
			
			
			 $cmd="insert into ctlt_emp_hist(user_code,id,user_pwd,user_eff_date,user_exp_date,last_date)
					values(".f_genField($strUserCode,"S").",".f_genField($strID,"N").",
					".f_genField($strUserPwd,"S").",".f_genField($strEffectDate,"D2").",".f_genField($strRetireDate,"D2").",sysdate) ";
			$db_opm2->send_cmd($cmd);
			$cmd="insert into ctlt_movement_tr(id,chg_date,move_code,prefix_code,name,surname,org_serial,user_code)
					values(".f_genField($strID,"N").",".f_genField($strEffectDate,"D2").",".
					f_genField($strMoveCodeTmp,"S").",".f_genField($strPrefixCode,"S").",".f_genField($strName,"S").",".
					f_genField($strSurName,"S").",".f_genField($strOrgSerial,"N").",".f_genField($strUserCode,"S").") ";
			$db_opm2->send_cmd($cmd);
			//add user_code to fw_user
			$strUsername=$strName." ".$strSurName;
			$cmd="	insert into fw_user(user_id,person_id,username)	values(".f_genField($strUserCode,"S").",".f_genField($strID,"S")."
			,".f_genField($strUsername,"S").")";
			$db_opm2->send_cmd($cmd);
			
				///////////// MY EDIT /////////////
			addUser($strUserCode,$strUserPwdLdap,$strName,$strSurName);
			///////////// END EDIT /////////////
			
			$cmd = " select count(*) cnt from sast_per_Aud_seq where id=".f_genField($strID,"N");
			$db_opm2->send_cmd($cmd);
			$data88 = $db_opm2->get_array();
			$data88 = array_change_key_case($data88, CASE_LOWER);
			if ($data88[cnt] == 0) {					
				$aa=f_get_sast_per_aud_seq ($strID,f_get_curlev($strLevelNo));
			} //end if  check dup id sast_per_aud_seq

			$cmd="select intranet_server from ctlt_env ";
			$db_opm2->send_cmd($cmd);
			$data81 = $db_opm2->get_array();
			$data81 = array_change_key_case($data81, CASE_LOWER);
			if ($data81[intranet_server] != "")
				$aa=f_add_person_mssql($strID,$strUserCode,$strEffectDate);
			//end if 
			
			
		}//end if  num_row
		$db_opm->close();
		$db_opm2->close();
	}
	
	function f_promote_person($strID,$strPosIDDpis,$strCmdPosition,$strNewCurLevDpis,$strOldCurLevDpis,$strEffectDate,$strMoveCode,$strOrgDate){
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$db_opm2 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$db_opm3 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		//echo "id=$strID,oldPosDpis=$strPosIDDpis,cmd=$strCmdPosition,NewCur=$strNewCurLevDpis,OldCur=$strOldCurLevDpis,eff=$strEffectDate,move=$strMoveCode,orgdate=$strOrgDate<br>";
		$cmd=" select per_type from psst_person where id=".f_genField($strID,"N");
		//echo "$cmd<br>";
		$num_row=$db_opm->send_cmd($cmd);
		if ($num_row == 1){
			$data_9 = $db_opm->get_array();
			$data_9 = array_change_key_case($data_9, CASE_LOWER);
			$strPerType=$data_9[per_type];
		}//end num_row
		$temp_pos = explode("\|", trim($strCmdPosition));
		$strOldPosID=$temp_pos[0];
		//$strNewCurLev=f_get_curlev($strNewCurLevDpis);
		//$strOldCurLev=f_get_curlev($strOldCurLevDpis);
		
		/*
		select data from dpis database
		*/
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		$db_dpis9 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		if ($strPerType=="11"){
			$cmd = " select nvl(org_id,'') org_id, nvl(org_id_1,'') org_id_1,nvl(org_id_2,'') org_id_2,pl_code  code
					FROM per_position where pos_id=";
			$strNewCurLev=f_get_curlev($strNewCurLevDpis);
			$strOldCurLev=f_get_curlev($strOldCurLevDpis);}
		elseif ($strPerType=="14"){
			$cmd = " select nvl(org_id,'') org_id, nvl(org_id_1,'') org_id_1,nvl(org_id_2,'') org_id_2,ep_code code 
					FROM per_pos_empser where poems_id=";
			$strNewCurLev="1";
			$strOldCurLev=1;}
		else {//strPerType='21'
			$cmd = " select nvl(org_id,'') org_id, nvl(org_id_1,'') org_id_1,nvl(org_id_2,'') org_id_2,pn_code  code
					FROM per_pos_emp where poem_id=";
			$strNewCurLev="1";
			$strOldCurLev=1;}
		//end if
		$cmd.=f_genField($strOldPosID,"N");
		//echo "$cmd<br>";
		$count_temp = $db_dpis9->send_cmd($cmd);
		//echo "count=$count_temp<br>";
		if ($count_temp == 1){
		   //  echo "in count_temp old <br>";		
			$data_temp = $db_dpis9->get_array();
			$data_temp = array_change_key_case($data_temp, CASE_LOWER);
			$strOldLineCode=f_get_line_code($data_temp[code],$strPerType);
			$strOldOrgSerial=f_get_org_serial($data_temp[org_id],$data_temp[org_id_1],$data_temp[org_id_2]);
			//echo "strOldOrgSerial=$strOldOrgSerial<br>";
		}//end count_temp
		if ($strPerType=="11")
			$cmd = " select nvl(org_id,'') org_id, nvl(org_id_1,'') org_id_1,nvl(org_id_2,'') org_id_2,pl_code  code,pos_salary,pt_code
					FROM per_position where pos_id=";
		elseif ($strPerType=="14")
			$cmd = " select nvl(org_id,'') org_id, nvl(org_id_1,'') org_id_1,nvl(org_id_2,'') org_id_2,ep_code code 
					FROM per_pos_empser where poems_id=";
		else //strPerType='21'
			$cmd = " select nvl(org_id,'') org_id, nvl(org_id_1,'') org_id_1,nvl(org_id_2,'') org_id_2,pn_code  code
					FROM per_pos_emp where poem_id=";
		//end if
		//$cmd = " select nvl(org_id,'') org_id, nvl(org_id_1,'') org_id_1,nvl(org_id_2,'') org_id_2,pl_code,pos_salary,pt_code 
			//FROM per_position where pos_id=".f_genField($strPosID,"S");
		$cmd.=f_genField($strPosIDDpis,"N");
		//echo "$cmd<br>";
		//echo "count=$count_temp<br>";
		$count_temp = $db_dpis9->send_cmd($cmd);
		if ($count_temp == 1){
			//echo "in count_temp new <br>";
			$data_temp = $db_dpis9->get_array();
			$data_temp = array_change_key_case($data_temp, CASE_LOWER);
			$strNewLineCode=f_get_line_code($data_temp[code],$strPerType);
			$strNewPTCode=$data_temp[pt_code];
			$strNewPosSalary=$data_temp[pos_salary];
			$strNewOrgSerial=f_get_org_serial($data_temp[org_id],$data_temp[org_id_1],$data_temp[org_id_2]);
			//echo "strNewOrgSerial=$strNewOrgSerial<br>";
		}//end count_temp
		$strOldPosID=f_get_pos_id($temp_pos[0],$strPerType);
		$strNewPosID=f_get_pos_id($strPosIDDpis,$strPerType);
		$cmd="update psst_position   set pos_status    = '2' , id = null,
    		 pos_date =  null   ,    approve_date = ".f_genField($strEffectDate,"D2");
		$cmd.=" where id=".f_genField($strID,"N");
		$db_opm2->send_cmd($cmd);
		$strNewCodeRecv=f_get_layer_no($strNewPosSalary,$strNewCurLevDpis);
		$cmd="update psst_position set pos_status = '1',id=".f_genField($strID,"N");
		if ($strNewCurLev != ""){
			$cmd.=", cur_lev = ".f_genField($strNewCurLev,"N");
		} //end if
		$cmd.=",lev_type=".f_genField($strNewPTCode,"N").",salary=".f_genField($strNewPosSalary,"N");
		//$cmd.=",sal_code=".f_genField($strNewCodeRecv,"N");
		$cmd.=",pos_date= ".f_genField($strEffectDate,"D2").",approve_date = null ";
		$cmd.= " where pos_id=".f_genField($strNewPosID,"S")." and per_type=".f_genField($strPerType,"S");
		$cmd.=" and org_serial=".f_genField($strNewOrgSerial,"N");
		$db_opm2->send_cmd($cmd);
		//echo "$cmd<br>";
		$cmd="update PSST_PERSON
			set movement_code = ".f_genField($strMoveCode,"S").",pos_date=".f_genField($strEffectDate,"D2");
		$cmd.=",org_date=".f_genField($strOrgDate,"D2").",promote_date=".f_genField($strEffectDate,"D2");
//		$cmd.=",sal_lev_recv=".f_genField($strNewCurLev,"S").",sal_code_recv=".f_genField($strNewCodeRecv,"N");
		$cmd.=",sal_lev_recv=".f_genField($strNewCurLev,"S");
		$cmd.=",salary_recv=".f_genField($strNewPosSalary,"N").",per_status='1'";
		$cmd.=",per_line_code=".f_genField($strNewLineCode,"S").",per_org_serial=".f_genField($strNewOrgSerial,"N");
		$cmd.= " where id = ".f_genField($strID,"N");
		$db_opm2->send_cmd($cmd);
		//echo "$cmd<br>";
		//echo "strNewPosID=$strNewPosID,strOldPosID=$strOldPosID,strNewOrgSerial=$strNewOrgSerial,strOldOrgSerial=$strOldOrgSerial<br>";
		if ($strNewPosID != $strOldPosID || $strNewOrgSerial != $strOldOrgSerial) {			
			$cmd = " 	select lower(name_eng) name_eng,lower(surname_eng) surname_eng,a.per_org_serial per_org_serial,
			  				prefix_code,a.name name,surname,user_code ,d.com_code com_code , b.per_type
				  from psst_person a,psst_position b, ctlt_emp c,ctlt_command_line d
				   where a.id=b.id and a.id=c.emp_id and b.pos_id=d.pos_id and b.per_type=d.per_type and a.id=".f_genField($strID,"N");
		}else{
			$cmd = " select lower(name_eng) name_eng,lower(surname_eng) surname_eng,a.per_org_serial per_org_serial ,
		  				 prefix_code,a.name,surname ,user_code, per_type
				  from psst_person a, ctlt_emp b
				  where a.id=b.emp_id and a.id=".f_genField($strID,"N");
		} //end if
		//echo "$cmd<br>";
		$num_row=$db_opm->send_cmd($cmd);
		if (trim($num_row) == 1) {
			$data = $db_opm->get_array();
			$data = array_change_key_case($data, CASE_LOWER);		
			//echo "Newposid=$strNewPosID,OldPosId=$strOldPosID,neworg=$strNewOrgSerial,oldorg=$strOldOrgSerial<br>";
			if ($strNewPosID != $strOldPosID || $strNewOrgSerial != $strOldOrgSerial){
				$strSessID = f_get_SessionID();
				$cmd=" insert into ctlt_system_right_temp(user_code,sys_code,org_serial,user_right,session_id)
				select user_code,sys_code,org_serial,user_right,".f_genField($strSessID,"N")." from ctlt_system_right
				where user_code=".f_genField($data[user_code],"S");
				$db_opm2->send_cmd($cmd);
				$cmd=" delete ctlt_system_right	where user_code=".f_genField($data[user_code],"S");
				$db_opm2->send_cmd($cmd);
				//delete aut_roleuseraction 
				global $aut_host, $aut_dsn,$aut_user,$aut_pwd;
				$db_aut= new connect_db_aut ($aut_host, $aut_dsn,$aut_user,$aut_pwd);
				$cmd="delete  from aut_roleuseraction where user_id=".f_genField($data[user_code],"S");
				$db_aut->send_cmd($cmd);
				$db_aut->close();
				//
				$ins=f_ins_system_right($data[user_code],$data[per_org_serial],$data[com_code]);
			} // NewPosID != OldPosID or NewOrgSerial != OldOrgSerial
			if ($strNewCurLev != $strOldCurLev){
				$cmd="insert into ctlt_movement_tr(id,chg_date,move_code,prefix_code,name,surname,org_serial,
				user_code,old_cur_lev,cur_lev)	values(".f_genField($strID,"N").",".f_genField($strEffectDate,"D2").",'05',";
				$cmd.=f_genField($data[prefix_code],"S").",".f_genField($data[name],"S").",";
				$cmd.=f_genField($data[surname],"S").",".f_genField($data[per_org_serial],"N").",".f_genField($data[user_code],"S").",";
				$cmd.=f_genField($strOldCurLev,"N").",".f_genField($strNewCurLev,"N").")";
				$db_opm2->send_cmd($cmd);
			}//end if NewCurLev != OldCurLev
			//echo "newlinecode=$strNewLineCode,old=$strOldLineCode<br>";
			if ($strNewLineCode != $strOldLineCode){
				$cmd="insert into ctlt_movement_tr(id,chg_date,move_code,prefix_code,name,surname,org_serial,
				user_code,old_line_code,line_code)	values(".f_genField($strID,"N").",".f_genField($strEffectDate,"D2").",'04',";
				$cmd.=f_genField($data[prefix_code],"S").",".f_genField($data[name],"S").",";
				$cmd.=f_genField($data[surname],"S").",".f_genField($data[per_org_serial],"N").",".f_genField($data[user_code],"S").",";
				$cmd.=f_genField($strOldLineCode,"N").",".f_genField($strNewLineCode,"N").")";
				$db_opm2->send_cmd($cmd);
				// update aut_person change position_name
				$cmd = " select convert(line_name,'utf8','th8tisascii') line_name from pssv_person where id=".f_genField($strID,"N");
				$num_row1=$db_opm3->send_cmd($cmd);
				if ($num_row1 == 1){
					$data3 = $db_opm3->get_array();
					$data3 = array_change_key_case($data3, CASE_LOWER);
					global $autMySQL_Connect;
					$conn = new COM("ADODB.Connection") or die("Cannot start ADO");
					$conn->Open($autMySQL_Connect);
					$rsMySQL= new COM("ADODB.Recordset");
					$cmd="select * from aut_person where person_id= ".f_genField($strID,"N");
					//echo "cmd=$cmd";
					$rsMySQL->Open($cmd,$conn,1,3);
					if (!$rsMySQL->eof){
						$rsMySQL->fields["position_name"]->value=$data3[line_name];
						$UPDATE_DATE = date("Y-m-d H:i:s");
						$rsMySQL->fields["lastupdate"]->value=$UPDATE_DATE;
						$rsMySQL->fields["updateuserid"]->value="dpis";
						$rsMySQL->update();
					}
					$rsMySQL->close();
					$rsMySQL=null;
					$conn->close();
					$conn=null;
				} //end if
				$db_opm3->close();
				// end postion of aut_person
				
			}//end if NewLineCode != OldLineCode
			//echo "strNewOrgSerial=$strNewOrgSerial,strOldOrgSerial=$strOldOrgSerial<br>";
			if ($strNewOrgSerial != $strOldOrgSerial){
				$cmd="insert into ctlt_movement_tr(id,chg_date,move_code,prefix_code,name,surname,org_serial,
				user_code,old_org_serial)	values(".f_genField($strID,"N").",".f_genField($strEffectDate,"D2").",'06',";
				$cmd.=f_genField($data[prefix_code],"S").",".f_genField($data[name],"S").",";
				$cmd.=f_genField($data[surname],"S").",".f_genField($data[per_org_serial],"N").",".f_genField($data[user_code],"S").",";
				$cmd.=f_genField($strOldOrgSerial,"N").")";				
				$db_opm2->send_cmd($cmd);
				//update aut_person
				$cmd = " select aut_org_serial from ctlt_organize where org_serial=".f_genField($strNewOrgSerial,"N");
				$num_row1=$db_opm3->send_cmd($cmd);
				if ($num_row1 == 1){
					$data3 = $db_opm3->get_array();
					$data3 = array_change_key_case($data3, CASE_LOWER);
					$strAutOrgSerial=$data3[aut_org_serial];
				} //end if
				/*$cmd = " select convert(line_name,'utf8','th8tisascii') line_name from pssv_person where id=".f_genField($strID,"N")
				$num_row1=$db_opm3->send_cmd($cmd);
				if ($num_row1 == 1){
					$data3 = $db_opm3->get_array();
					$data3 = array_change_key_case($data3, CASE_LOWER);
					$strLineName=$data3[line_name];
				} //end if
				$db_opm3->close();*/
				global $aut_host, $aut_dsn,$aut_user,$aut_pwd;
				$db_aut= new connect_db_aut ($aut_host, $aut_dsn,$aut_user,$aut_pwd);
				$UPDATE_DATE = date("Y-m-d H:i:s");
				$cmd="update aut_person set org_seq=".f_genField($strAutOrgSerial,"N");
				$cmd.=", lastupdate=".f_genField($UPDATE_DATE,"S").",updateuserid='dpis' ";
				$cmd.=" where person_id=".f_genField($strID,"N");
				$db_aut->send_cmd($cmd);
				//echo "$cmd<br>";
				$db_aut->close();
				// end update aut_person
			}//end if NewLineCode != OldLineCode
			if ($strNewCurLev != $strOldCurLev){
				$strXXX=f_chk_grp ($strID,$strNewCurLev,$strOldCurLev,$data[per_type]);
			}//end if
			$cmd="select intranet_server from ctlt_env ";
			$db_opm2->send_cmd($cmd);
			$data81 = $db_opm2->get_array();
			$data81 = array_change_key_case($data81, CASE_LOWER);
			if ($data81[intranet_server] != "")
				$aa=f_add_person_mssql($strID,$data[user_code],$strEffectDate);
			//end if
		}//end if  num_row
		$db_opm2->close();
		$db_opm->close();
	}
	function f_ins_system_right($strUserCode,$strOrgSerial,$strComCode){
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm3 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$db_opm4 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$db_opm5 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd = " select sys_code,pattern_code,sys_type from ctlt_system 
	  					where 	basic_type='Y' ";
		//echo "$cmd<br>";
		$db_opm3->send_cmd($cmd);
		while ($data3 = $db_opm3->get_array()) {
			$data3 = array_change_key_case($data3, CASE_LOWER);
			if ($data3[sys_code] == "WebBrd") {
				$strAuthorize = "S";
			}else{
				$strAuthorize = "U";
			} // end if
			$cmd= "	insert into ctlt_system_right(user_code,sys_code,org_serial,act_org_serial,com_code,user_right)
								values(".f_genField($strUserCode,"S").",".f_genField($data3[sys_code],"S");
			$cmd.=",".f_genField($strOrgSerial,"N").",".f_genField($strOrgSerial,"N").",".f_genField($strComCode,"N")
				.",".f_genField($strAuthorize,"S").")";
			$db_opm4->send_cmd($cmd);
			if ($data3[sys_code] == "DCS3") {
				//check dup
				global $aut_host, $aut_dsn,$aut_user,$aut_pwd;
				$db_aut= new connect_db_aut ($aut_host, $aut_dsn,$aut_user,$aut_pwd);
				$cmd = " select aut_org_serial from ctlt_organize where org_serial=".f_genField($strOrgSerial,"N");
				//echo "$cmd<br>";
				$num_row1=$db_opm5->send_cmd($cmd);
				if ($num_row1 == 1){
					$data5 = $db_opm5->get_array();
					$data5 = array_change_key_case($data5, CASE_LOWER);
					$strAutOrgSerial=$data5[aut_org_serial];
				} //end if
				$db_opm5->close();
				
				//$cmd="delete  from aut_roleuseraction where user_id=".f_genField($strUserCode,"S");
				//$cmd.=" and orgactive_seq=".f_genField($strAutOrgSerial,"N")." and role_seq in (28,5) ";
				//echo "$cmd";
				//$db_aut->send_cmd($cmd);
				
				//28
				$cmd="select seq from arc_pkctrl where table_name='aut_roleuseraction' ";
				//echo "$cmd<br>";
				$num_row=$db_aut->send_cmd($cmd);
				if ($num_row == 1){
					$data_arc = $db_aut->get_array();
					$data_arc = array_change_key_case($data_arc, CASE_LOWER);
					$strSeq1=$data_arc[seq];
				} //end if
				$cmd="update arc_pkctrl set seq=seq+1 where table_name='aut_roleuseraction' ";
				//echo "$cmd<br>";
				$db_aut->send_cmd($cmd);
				//insert aut_roleuseraction
				$UPDATE_DATE = date("Y-m-d H:i:s");
				$cmd="insert into aut_roleuseraction(action_seq,user_id,orgactive_seq,role_seq,default_flag,Updateuserid,Lastupdate) values( ";
				$cmd.=f_genField($strSeq1,"S").",".f_genField($strUserCode,"S").",".f_genField($strAutOrgSerial,"N").",".f_genField(28,"N");
				$cmd.=",".f_genField("1","S").",".f_genField("dpis","S").",".f_genField($UPDATE_DATE,"S").")";
				$db_aut->send_cmd($cmd);
				//echo "$cmd<br>";
				// DCS3=35
				$cmd="select seq from arc_pkctrl where table_name='aut_roleuseraction' ";
				//echo "$cmd<br>";
				$num_row=$db_aut->send_cmd($cmd);
				if ($num_row == 1){
					$data_arc = $db_aut->get_array();
					$data_arc = array_change_key_case($data_arc, CASE_LOWER);
					$strSeq1=$data_arc[seq];
				} //end if
				$cmd="update arc_pkctrl set seq=seq+1 where table_name='aut_roleuseraction' ";
				//echo "$cmd<br>";
				$db_aut->send_cmd($cmd);
				//insert aut_roleuseraction
				$UPDATE_DATE = date("Y-m-d H:i:s");
				$cmd="insert into aut_roleuseraction(action_seq,user_id,orgactive_seq,role_seq,default_flag,Updateuserid,Lastupdate) values( ";
				$cmd.=f_genField($strSeq1,"S").",".f_genField($strUserCode,"S").",".f_genField($strAutOrgSerial,"N").",".f_genField(5,"N");
				$cmd.=",".f_genField("1","S").",".f_genField("dpis","S").",".f_genField($UPDATE_DATE,"S").")";
				$db_aut->send_cmd($cmd);
				//echo "$cmd<br>";
				$db_aut->close();
			}
			
		}//end while
		$db_opm3->close();
		$db_opm4->close();
	//return "";
	}
	function f_get_sast_per_aud_seq ($strID,$strCurLev){
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm5 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$db_opm6 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd = " select nvl(admin_code,'9999') admin_code,admin_name,line_inorg,org_name,
                    b.per_type per_type,line_name,lev_type,a.cur_lev,level_name ,
					line_name||cur_lev_name  pos_name,line_name||' ระดับ'||cur_lev_name pos_name2, line_name pos_name3
                    from pssv_person a,psst_per_level  b
                    where  b.cur_lev=a.cur_lev and id = ".f_genField($strID,"N");
		$num=$db_opm5->send_cmd($cmd);
		$strTmpPosType = "";
		if ($num== 1) {
			$data5 = $db_opm5->get_array();
			$data5 = array_change_key_case($data5, CASE_LOWER);
			if ($data5[per_type] != "14" && $data5[per_type] != "21") {
				//$strTmpPosName = $data5[pos_name];
				/*if ($data5[cur_lev] <= 11) {
					$strTmpPosName = $data5[pos_name];
				}else{
					$strTmpPosName = $data5[line_name]." ".$data5[level_name];
				}//end if cur_lev */
				//echo "pos_name=$strTmpPosName<br>";
				if	($strCurLev==12) {
					$strTmpPosType = "6";  
					$strTmpOrgAudSerl = 5;
					$strTmpPosName = $data5[pos_name];
				}elseif ($strCurLev==13 || $strCurLev==16) {
						$strTmpPosType = "5";  
						$strTmpOrgAudSerl = 4;		
						$strTmpPosName = $data5[pos_name];	
				}elseif ($strCurLev==17 || $strCurLev==18 || $strCurLev==14) {
						$strTmpPosType = "4";  
						$strTmpOrgAudSerl = 3;
						$strTmpPosName = $data5[pos_name];
				}elseif ($strCurLev==19 || $strCurLev==21 || $strCurLev==22){
  						$strTmpPosType = "3";  
						$strTmpOrgAudSerl = 2;
						if ($strCurLev==19){
							$strTmpPosName = $data5[pos_name]; //strCurLev=19
						}else{
							 if ($strAdminCode == "0219") { //ผู้ตรวจสอบภายในระดับกระทรวง
								$strTmpPosName = $data5[admin_name].$data5[org_name];
							}else { //ผอ.สำนัก/กอง
								$strTmpPosName = $data5[line_inorg];	
							}//end if
						}//end if
				}elseif (($strCurLev==20 || $strCurLev==23 || $strCurLev==24 ) ) {
				// รองปลัด/ปลัดไม่ต้องมี  queue
						if ($data5[admin_code] = "0268" || $data5[admin_code] = "0109") {
							$strTmpPosType = "";
						}else{
							$strTmpPosType = "2";  
							$strTmpOrgAudSerl = 1;						
							if ($data5[admin_code] == "0218"){ //cur_lev=24
							// ผู้ตรวจ
								$strTmpPosName = $data5[pos_name3];
							}elseif ($data5[admin_code] == "0151"){ //cur_lev=23
								$strTmpPosName = $data5[admin_name];
							}else{ //cur_lev=20
								$strTmpPosName = $data5[pos_name];
							}//end if
						}
 						
				}else{
					$strTmpPosType = "6";	
					$strTmpOrgAudSerl = 5;
					$strTmpPosName = $data5[pos_name2];
				} // end if
			
			}else{
				$strTmpPosType = "6";	
				$strTmpOrgAudSerl = 5;
				$strTmpPosName = $data5[pos_name2];	
			}
			// end if 14,21
		}//end if num_row
		if ($strTmpPosType != ""){
			$cmd="	select per_aud_serl,id  from   sast_per_aud_seq
					where  last_rec='Y' and pos_type=".f_genField($strTmpPosType,"S");
		//	echo "$cmd<br>";
			$db_opm5->send_cmd($cmd);
			//$db_opm5->show_error();
			if ($db_opm5->num_rows()== 1 ) {
				$data5 = $db_opm5->get_array();
				$data5 = array_change_key_case($data5, CASE_LOWER);
				if ($data5[per_aud_serl] != "") {
					$cmd="select  max(per_aud_serl) + 1 per_aud_serl from    sast_per_aud_seq
								where   org_aud_serl = ".f_genField($strTmpOrgAudSerl,"N");
				//	echo "$cmd<br>";
					$db_opm6->send_cmd($cmd);
					//$db_opm6->show_error();
					if ($db_opm6->num_rows()== 1) {
						$data6 = $db_opm6->get_array();
						$data6 = array_change_key_case($data6, CASE_LOWER);
						$strTmpPerAudSerl = $data6[per_aud_serl];
					}else{
						$strTmpPerAudSerl = 1;
					} // end if num_rows6
					$strTmpPerAudSeq = $strTmpPerAudSerl;
					 $strTmpPerAudStat  =  "0";
					// $strTmpPerAudTimes = 0;
					 $cmd="update sast_per_aud_seq set    last_rec=null
									where  id=".f_genField($data5[id],"N");
					$db_opm6->send_cmd($cmd);
				//	$db_opm6->show_error();
					//echo "$cmd<br>";
					$cmd="insert into sast_per_aud_seq
					       (org_aud_serl,per_aud_serl,id,per_aud_seq,per_aud_stat,pos_type,
				          per_aud_times,pos_name,last_rec,pre_org_serl,pre_per_serl)
				  values (".f_genField($strTmpOrgAudSerl,"N").",".f_genField($strTmpPerAudSerl,"N").",".f_genField($strID,"N");
				   $cmd.=",".f_genField($strTmpPerAudSeq,"N").",".f_genField($strTmpPerAudStat,"S").",".f_genField($strTmpPosType,"S").",";
				   $cmd.=f_genField("0","N").",".f_genField($strTmpPosName,"S").",'Y',".f_genField($strTmpOrgAudSerl,"N");
				   $cmd.=",".f_genField($data5[per_aud_serl],"N").") ";
					$db_opm6->send_cmd($cmd);
				} // end if per_aud_serl
			} //end if num row5
		} //end if tmpPosType
		$db_opm5->close();
		$db_opm6->close();
	}
	function f_get_bound($strID) {
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm7 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd="select admin_name||'เขตที่ ' admin_name, bound
				      from psst_admin_code a, dist_hold b     where a.admin_code = 218  
				      and b.end_date is null    and b.id =  $strID  order by decode(bound,'สก',0,bound) ";
		$strTmpName="";
		$db_opm7->send_cmd($cmd);
		//$db_opm7->show_error();
		$strFirst="Y";
		while ($data7 = $db_opm7->get_array()) {
			$data7 = array_change_key_case($data7, CASE_LOWER);
			if ($data7[bound]=="สก") {
				$strTName=" ผู้ตรวจราชการส่วนกลาง";
			}else{
				//$strTName=$data7[admin_name]." ".$data7[bound];
				$strTName=$data7[bound];
			} // end if
			if ($strFirst=="Y"){
				if ($data7[bound]=="สก"){
					$strTmpName=$strTName;
				}else{
					$strTmpName=$data7[admin_name]." "."เขตที่ ".$strTName;
				}//end if
				$strFirst="N";
			}else{
				$strTmpName=$strTmpName.", ".$strTName;
			}//end if						
		} // end while 
		//echo "tmpPosName=$strTmpName<br>";
		$db_opm7->close();
		return(trim($strTmpName));
	}
	function f_chk_grp ($strID,$strNewCurLev,$strCurLev,$strPerType){
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm5 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$db_opm6 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd = " select nvl(admin_code,'9999') admin_code from psst_position where  id = ".f_genField($strID,"N");
		$num=$db_opm5->send_cmd($cmd);


		if (trim($num)==1){
			$data5 = $db_opm5->get_array();
			$data5 = array_change_key_case($data5, CASE_LOWER);
			$strAdminCode=$data5[admin_code];
		}//end if
		$cmd = " select pos_type from sast_per_aud_seq where  id = ".f_genField($strID,"N");
		$num=$db_opm5->send_cmd($cmd);
		if (trim($num)==1){
			$data5 = $db_opm5->get_array();
			$data5 = array_change_key_case($data5, CASE_LOWER);
			$strPosType=$data5[pos_type];
		}//end if
		$cmd = " select line_name,lev_type,a.cur_lev,level_name ,admin_name,line_inorg,org_name,
					line_name||cur_lev_name  pos_name,line_name||' ระดับ'||cur_lev_name pos_name2, line_name pos_name3
                    from pssv_person a,psst_per_level  b
                    where  b.cur_lev=a.cur_lev and id  = ".f_genField($strID,"N");
		//echo "$cmd<br>";
		$num=$db_opm5->send_cmd($cmd);
		
		if (trim($num)==1){
			$data5 = $db_opm5->get_array();
			$data5 = array_change_key_case($data5, CASE_LOWER);
			$strLineName=$data5[line_name];
			$strLevType=$data5[lev_type];
			$strLevelName=$data5[level_name];
			$strTmpPosName=$data5[pos_name];
			//$strTmpPosName=$data5[pos_name];
			//echo "num=$num,per_type=$strPerType<br>";
		}//end if		
		//echo "strPerType=$strPerType,strCurLev=$strCurLev,strNewCurLev=$strNewCurLev<br>";
		if ($strPerType != "14" && $strPerType != "21") {
			$strTmpPosName = $data5[line_name].$data5[level_name];
			if ($strCurLev == 12 && $strNewCurLev == 13) {
					$strTmpPosType = "5";  
					$strTmpPosName=$data5[pos_name];
			}elseif (($strCurLev == 13 && $strNewCurLev == 14) || ($strCurLev == 16 && $strNewCurLev == 17)) {
					$strTmpPosType = "4";  
					$strTmpPosName=$data5[pos_name];
			}elseif ($strCurLev == 18 && ($strNewCurLev == 19 || $strNewCurLev == 21 || $strNewCurLev == 22)) { //ผู้ตรวจสอบภายในระดับกระทรวง หรือ ผอ.กอง
					$strTmpPosType = "3";  
					if ($strAdminCode == "0219") { //ผู้ตรวจสอบภายในระดับกระทรวง
						$strTmpPosName = $data5[admin_name].$data5[org_name];
					}else{ //ผอ.สำนัก/กอง
						 $strTmpPosName = $data5[line_inorg];	
					}//end if 
			}elseif (($strCurLev == 19 || $strCurLev == 21 || $strCurLev == 22) && ($strNewCurLev == 20 || $strNewCurLev == 23 || $strNewCurLev == 24))  {
			// รองปลัด/ปลัดไม่ต้องมี  queue
			/*		$strTmpPosType = "2";
					if ($strCurLev == 22 && $strNewCurLev == 23) { //22-->23
						$strTmpPosName =$data5[admin_name];
					}else{ //19-->20,22-->20
						$strTmpPosName = $data5[line_name].$data5[level_name];
					} */ // end if
			//}elseif (($strCurLev == 20 || $strCurLev == 23 || $strCurLev == 24) && ($strAdminCode == "0268" ||$strAdminCode == "0109")){
			//		$strTmpPosType = "x";  

			// เลื่อเป็นรองปลัด ตัดออกจาก queue
					if ($data5[admin_code] = "0268" || $data5[admin_code] != "0109") {
						$strTmpPosType = "x";
					}else{
						$strTmpPosType = "2"; 
						if ($data5[admin_code] == "0218"){ //New_cur_lev=24
						// ผู้ตรวจ
							$strTmpPosName = $data5[pos_name3];
						}elseif ($data5[admin_code] == "0151"){ //New_cur_lev=23
							$strTmpPosName = $data5[admin_name];
						}else{ //New_cur_lev=20
							$strTmpPosName = $data5[pos_name];
						}//end if
					}





			}else{
				$strTmpPosType = "y";
			} // end if
		} // end if 14,21
		//echo "tmppostype=$strTmpPosType ,strTmpPosName=$strTmpPosName, curlev=$strCurLev, new=$strNewCurLev<br>";
		if ($strTmpPosType == "y"){
			//strCurLev = 17 and strNewCurLev = 18 use $strTmpPosName previous select
			//strCurLev = 21 and strNewCurLev = 22 ใช้ ผอ.+สำนัก
			//strCurLev = 23 and strNewCurLev = 24 ใช้ผู้ตรวจราชการสำนักนายกรัฐมนตรี
			/*if ($strCurLev == 21 && $strNewCurLev == 22) {
				 $strTmpPosName = $data5[line_inorg];	
			}elseif ($strCurLev == 23 && $strNewCurLev == 24) {
				 $strTmpPosName = $data5[admin_name];
			}elseif ($strAdminCode == "0219"){ // ผู้ตรวจสอบภายในระดับกระทรวง
				 $strTmpPosName = $data5[admin_name].$data5[org_name];
			}else{
				$strTmpPosName = $data5[line_name].$data5[level_name];
			}*///end if 
			if ($strNewCurLev == 19 || $strNewCurLev == 21 || $strNewCurLev == 22 ){
				if ($strNewCurLev == 19){
				    $strTmpPosName = $data5[pos_name];
				}else{
					 if ($strAdminCode == "0219") { //ผู้ตรวจสอบภายในระดับกระทรวง
						$strTmpPosName = $data5[admin_name].$data5[org_name];
					}else { //ผอ.สำนัก/กอง
						$strTmpPosName = $data5[line_inorg];	
					}//end if
				}//end if
			}elseif ($strNewCurLev == 20 || $strNewCurLev == 23 || $strNewCurLev == 24 ){
					if ($data5[admin_code] == "0218"){ //cur_lev=24
					// ผู้ตรวจ
						$strTmpPosName = $data5[pos_name3];
					}elseif ($data5[admin_code] == "0151"){ //cur_lev=23
							$strTmpPosName = $data5[admin_name];
					}else{ //cur_lev=20
							$strTmpPosName = $data5[pos_name];
					}//end if
			}else{
					$strTmpPosName = $data5[line_name].$data5[level_name];
			}
			$cmd=" 	update sast_per_aud_seq	set    pos_name=".f_genField($strTmpPosName,"S")."	where  id = ".f_genField($strID,"N");
			$db_opm5->send_cmd($cmd);
		}elseif($strTmpPosType=="x"){ 
				$strxx=f_Delete_Queue($strID);
		}else{
				$strXXX=f_Change_Queue($strID,$strTmpPosType,$strTmpPosName);
		}//end if tmppostype='y'
		$db_opm5->close();
		$db_opm6->close();
	}
	function f_retire_person($strID,$strEffectDate,$strMovCode) {
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm8 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$db_opm81 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		/*$cmd="select user_code,per_org_serial,prefix_code,b.name name ,surname ,per_type, nvl(proximity_serial,'0') proximity_serial
				      from ctlt_emp a,psst_person b where emp_id=id  
				      and   emp_id =  $strID "; */
		$cmd="select user_code from ctlt_emp where emp_id =  ".f_genField($strID,"N");
		$strMoveCodeTmp=$strMovCode;
		$num=$db_opm8->send_cmd($cmd);
		//echo "$cmd<br>num=$num";
		if ($num != 0) {
			$data8 = $db_opm8->get_array();
			$data8 = array_change_key_case($data8, CASE_LOWER);
			$cmd="update ctlt_emp set user_exp_date=".f_genField($strEffectDate,"D2");
			$cmd.= " where user_code=".f_genField($data8[user_code],"S");
			//echo "$cmd<br>";
			$db_opm81->send_cmd($cmd);
			if ($strMovementCode =="1800" || $strMovementCode =="1810" || $strMovementCode =="1820"){
				$strMoveCodeTmp= "02";
			}elseif ($strMovementCode =="1830" || $strMovementCode =="1860" || $strMovementCode =="1900" 
				|| $strMovementCode =="1910" || $strMovementCode =="1920" ){
					$strMoveCodeTmp= "07";
			}elseif ($strMovementCode =="1840") {
					$strMoveCodeTmp= "13";
			}elseif ($strMovementCode =="1850" || $strMovementCode =="2000" || $strMovementCode =="2010" 
					|| $strMovementCode =="2020" || $strMovementCode =="2030" || $strMovementCode =="2040"
					|| $strMovementCode =="2100" || $strMovementCode =="2110" || $strMovementCode =="2200") {
					$strMoveCodeTmp= "12";
			}else{
					$strMoveCodeTmp= "02";
			}// end if
			//update aut_user set disable=1
			global $aut_host, $aut_dsn,$aut_user,$aut_pwd;
			$db_aut= new connect_db_aut ($aut_host, $aut_dsn,$aut_user,$aut_pwd);
			$strEffectDate1=$strEffectDate;
			if ($strEffectDate1 =="")
				$strEffectDate1="null";					
			else{
				$strEffectDate1=str_replace('/','-',$strEffectDate1);
				$strEffectDate1="str_to_date('".$strEffectDate1."','%Y-%m-%d')";
			}
			$cmd="update aut_user set disable='1', expiredate=".$strEffectDate1." where user_id=".f_genField($data8[user_code],"S");
			//echo "$cmd<br>";
			$db_aut->send_cmd($cmd);
			$db_aut->close();
			//end update
  			
			$strXXX=f_Delete_Queue($strID);
			///////////// MY EDIT /////////////
			deleteUser($data8[user_code]);
			///////////// END EDIT /////////////
			$cmd="select intranet_server from ctlt_env ";
			$db_opm81->send_cmd($cmd);
			$data81 = $db_opm81->get_array();
			$data81 = array_change_key_case($data81, CASE_LOWER);
			if ($data81[intranet_server] != "")
				$aa=f_del_person_mssql($strID,$data8[user_code],$strEffectDate);
			//end if
		} // end if num>0
		$cmd="select per_org_serial,prefix_code,name ,surname ,per_type, nvl(proximity_serial,'0') proximity_serial
				      from psst_person where id = ".f_genField($strID,"N");
		//echo "$cmd<br>";
		$num=$db_opm8->send_cmd($cmd);
		if ($num != 0) {
			$data8 = $db_opm8->get_array();
			$data8 = array_change_key_case($data8, CASE_LOWER);
			
			$cmd=" insert into ctlt_movement_tr(id,chg_date,move_code,prefix_code,name,surname,org_serial,user_code)
	  				values(".f_genField($strID,"N").",".f_genField($strEffectDate,"D2").",".f_genField($strMoveCodeTmp,"S").",";
			$cmd.=f_genField($data8[prefix_code],"S").",".f_genField($data8[name],"S").",".f_genField($data8[surname],"S").",";
			$cmd.=f_genField($data8[per_org_serial],"N").",".f_genField($data8[user_code],"S").")";
			$db_opm81->send_cmd($cmd);
			//echo "$cmd<br>";
			
			$cmd=" update psst_person set per_status='2', movement_code	=".f_genField($strMovementCode,"S").",";
			$cmd.=" quit_date=".f_genField($strEffectDate,"D2").",id_proximity=null,proximity_serial = null ";
			$cmd.= " where id=".f_genField($strID,"N");
			$db_opm81->send_cmd($cmd);
			//echo "$cmd<br>";
			$cmd=" update psst_position set pos_status='2', id=null, 
					approve_date = ".f_genField($strEffectDate,"D2");
			$cmd.= " where id=".f_genField($strID,"N");	
			//echo "$cmd<br>";
			$db_opm81->send_cmd($cmd);
			if ($data[proximity_serial] !="0") 	{		
				$cmd="	update	psst_proximity  set	 proximity_status = '2'
						where		proximity_serial = ".f_genField($data8[proximity_serial],"S");
						//echo "$cmd<br>";
				$db_opm81->send_cmd($cmd);
			}//end if
			$cmd="select nvl(max(time_seq),0) time_seq from psst_old_time where id =".f_genField($strID,"N");
			$num_old_time=$db_opm8->send_cmd($cmd);
			if ($num_old_time==1){
				$dataOldTime = $db_opm8->get_array();
				$dataOldTime = array_change_key_case($dataOldTime, CASE_LOWER);
				if ($dataOldTime[time_seq]==1){
					$cmd="update psst_old_time set chg_date=".f_genField($strEffectDate,"D2");
					$cmd.= " where id=".f_genField($strID,"N");
					$cmd.= " and time_seq=".f_genField($dataOldTime[time_seq],"N");
					$db_opm81->send_cmd($cmd);
				} // end if $dataOldTime[time_seq]!=0
			}//end if $num_old_time==1			
		} // end if num>0
		$db_opm81->close();
		$db_opm8->close();
	}
	function f_Delete_Queue($strID) {
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm9 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$db_opm901 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd="select nvl(last_rec,'N') last_rec,org_aud_serl,per_aud_serl,pre_per_serl,pre_org_serl 
				      from   sast_per_aud_seq where id = ".f_genField($strID,"N");
		$num=$db_opm9->send_cmd($cmd);
		if ($num != 0) {
			$data9 = $db_opm9->get_array();
			$data9 = array_change_key_case($data9, CASE_LOWER);
			if ($data9[per_aud_serl] != ""){
				if ($data9[pre_per_serl] == 0 ||  $data9[last_rec] == "N"){
					$cmd="update sast_per_aud_seq  set    pre_per_serl=$data9[pre_per_serl]
					where  pre_org_serl= ".f_genField($data9[org_aud_serl],"N")."  and 	 pre_per_serl=".f_genField($data9[per_aud_serl],"N");
				}else{
					$cmd="update sast_per_aud_seq	set    last_rec ='Y'
							where  org_aud_serl=".f_genField($data9[pre_org_serl],"N")." and  per_aud_serl=".f_genField($data9[pre_per_serl],"N");
				} //end if $data9[pre_per_serl]==0 || $data9[last_rec] == "N"
				$db_opm901->send_cmd($cmd);
				$cmd="delete sast_per_aud_seq	where id=".f_genField($strID,"N");
				$db_opm901->send_cmd($cmd);
			}//end if $data9[per_aud_serl]
		} // end if
		$db_opm9->close();
		$db_opm901->close();
	} 

	function f_Change_Queue($strID,$strTmpPosType,$strTmpPosName) {
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm9 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$db_opm901 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$db_opm902 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$db_opm903 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd="select nvl(last_rec,'N') last_rec,org_aud_serl,per_aud_serl,pre_per_serl,pre_org_serl 
				      from   sast_per_aud_seq where id = ".f_genField($strID,"N");
		$num=$db_opm9->send_cmd($cmd);
		if ($num != 0) {
			$data9 = $db_opm9->get_array();
			$data9 = array_change_key_case($data9, CASE_LOWER);
			if ($data9[pre_per_serl] == 0 ||  $data9[last_rec] == "N"){
				$cmd="update sast_per_aud_seq  set    pre_per_serl=".f_genField($data9[pre_per_serl],"N")."
				where  pre_org_serl= ".f_genField($data9[org_aud_serl],"N")."  and 	 pre_per_serl=".f_genField($data9[per_aud_serl],"N");
			}else{
				$cmd="update sast_per_aud_seq	set    last_rec ='Y'
						where  org_aud_serl=".f_genField($data9[pre_org_serl],"N")." and  per_aud_serl=".f_genField($data9[pre_per_serl],"N");
			} //end if $data9[pre_per_serl]==0 || $data9[last_rec] == "N"
			$db_opm901->send_cmd($cmd);
			$cmd="select org_aud_serl,per_aud_serl,id  from   sast_per_aud_seq
				where  last_rec='Y' and pos_type=".f_genField($strTmpPosType,"S");
			$num1=$db_opm902->send_cmd($cmd);
			if ($num1 != 0) {
				$data902 = $db_opm902->get_array();
				$data902 = array_change_key_case($data902, CASE_LOWER);
				$strLastID = $data902[id];
				if($data902[org_aud_serl] != ""){
					$cmd="	select max(per_aud_serl)+1 per_aud_serl	from   sast_per_aud_seq
						where org_aud_serl=".f_genField($data902[org_aud_serl],"N");
					$num2=$db_opm903->send_cmd($cmd);
					if ($num2 != 0) {
						$data903 = $db_opm903->get_array();
						$data903 = array_change_key_case($data903, CASE_LOWER);
						$cmd="update sast_per_aud_seq set    last_rec=null
								where  id=".f_genField($strLastID,"N");
						$db_opm901->send_cmd($cmd);
						$cmd=" update sast_per_aud_seq
								set    last_rec='Y', 
										pos_type=".f_genField($strTmpPosType,"N").",
									    pos_name=".f_genField($strTmpPosName,"S").",
										org_aud_serl=".f_genField($data902[org_aud_serl],"N").",
							 		   per_aud_serl=".f_genField($data903[per_aud_serl],"N").", 
									   pre_per_serl=".f_genField($data902[per_aud_serl],"N").",						  
							 		   pre_org_serl=".f_genField($data902[org_aud_serl],"N")."
								where   id=".f_genField($strID,"N");
						$db_opm901->send_cmd($cmd);
					}//end if num2
				}//end if $data902[org_aud_serl]
			} // end if num1
		}//end if num
		$db_opm901->close();
		$db_opm902->close();
		$db_opm903->close();
		$db_opm9->close();
	} 
	function f_add_person_mssql($strID,$strUserCode,$strEffectDate){
		$aa=f_del_system_right($strUserCode);
		global $mssql_sql2k_host, $mssql_identity_host,$opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm9 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$db_opm10 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$db_opm13 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd=" select * from ctlt_policy ";
		$db_opm9->send_cmd($cmd);		
		$num=$db_opm9->send_cmd($cmd);
		if ($num != 0) {
			$data9 = $db_opm9->get_array();
			$data9 = array_change_key_case($data9, CASE_LOWER);
			$strPwdForm=$data9[pwd_form];
			if ($strPwdForm == "1") {		    
				$strPwdNum=$data9[pwd_num1] % 255;
				$strPwdOper=$data9[pwd_oper1];
				$strPwdRev=$data9[pwd_reverse1];}
			else{
		// 2
				$strWordConst=$data9[word_const];
				for ($i=0;$i<strlen($strWordConst);$i++){
					
					$strPwdNum=($strPwdNum+ord(substr($strWordConst,$i,1))) % 255;
					} //end for
				$strPwdOper=$data9[pwd_oper2];
				$strPwdRev=$data9[pwd_reverse2];
			}//end if
		}//end if num !=0
		//select data Oracle from  ctlt_emp,psst_person,psst_position where emp_flag=I
		$cmd="select ctlt_emp.user_code,ctlt_emp.emp_id,ctlt_emp.user_pwd,psst_person.name,psst_person.surname,
			psst_person.name_eng,psst_person.surname_eng,psst_person.per_status ,psst_person.prefix_code, 		
			psst_person.per_org_serial,psst_position.pos_id,psst_position.per_type,psst_position.disc_code,	
			psst_position.pos_status,psst_position.line_code,psst_position.length_code,psst_position.admin_code,
			psst_position.cur_lev,psst_position.lev_type,ctlt_organize.org_level,ctlt_organize.org_serial,
			ctlt_organize.upper_org_serial ,to_char(user_efft_date,'dd/mm/yyyy') eff_date,
			to_char(user_exp_date,'dd/mm/yyyy') exp_date 
			from ctlt_emp,psst_person,psst_position,ctlt_organize  
			where emp_id=psst_person.id and psst_person.id=psst_position.id and psst_person.per_org_serial=ctlt_organize.org_serial 
			and user_code=".f_genField($strUserCode,"S");
		$num1=$db_opm9->send_cmd($cmd);		
		if ($num1 !=0){
			$data9 = $db_opm9->get_array();
			$data9 = array_change_key_case($data9, CASE_LOWER);
			$db_opmIntra = new connect_db_mssql($mssql_sql2k_host, "opmintranet", "opm", "opm");
			$cmd="delete tPosition where id=".f_genField($data9[emp_id],"N");
			$db_opmIntra->send_cmd($cmd);
			/* $cmd="select * from tPosition where pos_id=".f_genField($data9[pos_id],"S") . "  and per_type=".f_genField($data9[per_type],"S");
			$cmd.=" and org_serial= ".f_genField($data9[per_org_serial],"N");
			$num=$db_opmIntra->send_cmd($cmd);
			if ($num == 0){
				$cmd="insert into tPosition(pos_id,per_type,org_serial,pos_status,id,disc_code,";
				$cmd.=" line_code,length_code,admin_code,cur_lev,lev_type) values(".f_genField($data9[pos_id],"S");
				$cmd.=" ,".f_genField($data9[per_type],"S").", ".f_genField($data9[per_org_serial],"N");
				$cmd.=" ,".f_genField($data9[pos_status],"S")." ,".f_genField($data9[emp_id],"N");
				$cmd.=" ,".f_genField($data9[disc_code],"S")." ,".f_genField($data9[line_code],"S");
				$cmd.=" ,".f_genField($data9[length_code],"S")." ,".f_genField($data9[admin_code],"S");
				$cmd.=" ,".f_genField($data9[cur_lev],"N")." ,".f_genField($data9[lev_type],"S").")";
			}else{
				$cmd="update tPosition set ";				
				$cmd.=" pos_status=".f_genField($data9[pos_status],"S")." ,id=".f_genField($data9[emp_id],"N");
				$cmd.=" ,disc_code=".f_genField($data9[disc_code],"S")." ,line_code=".f_genField($data9[line_code],"S");
				$cmd.=" ,length_code=".f_genField($data9[length_code],"S")." ,admin_code=".f_genField($data9[admin_code],"S");
				$cmd.=" ,cur_lev=".f_genField($data9[cur_lev],"N")." ,lev_type=".f_genField($data9[lev_type],"S");
				$cmd.=" where pos_id=".f_genField($data9[pos_id],"S")."  and per_type=".f_genField($data9[per_type],"S");	
				$cmd.=" and org_serial= ".f_genField($data9[per_org_serial],"N")	;
			} */
			$cmd="insert into tPosition(pos_id,per_type,org_serial,pos_status,id,disc_code,";
			$cmd.=" line_code,length_code,admin_code,cur_lev,lev_type) values(".f_genField($data9[pos_id],"S");
			$cmd.=" ,".f_genField($data9[per_type],"S").", ".f_genField($data9[per_org_serial],"N");
			$cmd.=" ,".f_genField($data9[pos_status],"S")." ,".f_genField($data9[emp_id],"N");
			$cmd.=" ,".f_genField($data9[disc_code],"S")." ,".f_genField($data9[line_code],"S");
			$cmd.=" ,".f_genField($data9[length_code],"S")." ,".f_genField($data9[admin_code],"S");
			$cmd.=" ,".f_genField($data9[cur_lev],"N")." ,".f_genField($data9[lev_type],"S").")";
			$db_opmIntra->send_cmd($cmd);
			
			$cmd="select * from tPerson where person_id=".f_genField($data9[emp_id],"N");
			$num=$db_opmIntra->send_cmd($cmd);
			if ($num == 0){
				$cmd="insert into tPerson(person_id,per_status,prefix_code,name,surname,name_eng,surname_eng,deptid) values(";
				$cmd.=f_genField($data9[emp_id],"N")." ,".f_genField($data9[per_status],"S")." ,".f_genField($data9[prefix_code],"S");
				$cmd.=" ,".f_genField($data9[name],"S")." ,".f_genField($data9[surname],"S")." ,".f_genField($data9[name_eng],"S");
				$cmd.=" ,".f_genField($data9[surname_eng],"S")." ,".f_genField($data9[per_org_serial],"N").")";
			}else{
				$cmd="update tPerson set ";
				$cmd.="per_status=".f_genField($data9[per_status],"S")." ,prefix_code=".f_genField($data9[prefix_code],"S");
				$cmd.=" ,name=".f_genField($data9[name],"S")." ,surname=".f_genField($data9[surname],"S")." ,name_eng=".f_genField($data9[name_eng],"S");
				$cmd.=" ,surname_eng=".f_genField($data9[surname_eng],"S")." ,deptid=".f_genField($data9[per_org_serial],"N");
				$cmd.=" where person_id=".f_genField($data9[emp_id],"N");
			}//end if
			$db_opmIntra->send_cmd($cmd);
			$strEffDate=substr($data9[eff_date],0,6).((int)substr($data9[eff_date],6,4)+543);
			$strExpDate=substr($data9[exp_date],0,6).((int)substr($data9[exp_date],6,4)+543);
			$cmd="select * from tuser where user_code=".f_genField($strUserCode,"S");
			$num=$db_opmIntra->send_cmd($cmd);
			if ($num == 0){
				$cmd="insert into tUser(user_code,person_id,user_pwd,eff_date,exp_date) values(".f_genField($strUserCode,"S");
				$cmd.=" ,".f_genField($data9[emp_id],"N").", ".f_genField($data9[user_pwd],"S");
				$cmd.=", ".f_genField($strEffDate,"D").", ".f_genField($strExpDate,"D").")";
			}else{
				$cmd="update tUser set user_pwd=".f_genField($data9[user_pwd],"S");
				$cmd.=",eff_date=".f_genField($strEffDate,"D").",exp_date=".f_genField($strExpDate,"D");
				$cmd.=" where user_code=".f_genField($strUserCode,"S");
			}//end if
			$db_opmIntra->send_cmd($cmd);
			$db_opmIntra->close();
			//moc
			$db_moc = new connect_db_mssql($mssql_sql2k_host, "moc", "opm", "opm");
			$cmd="select * from tPerson where person_id=".f_genField($data9[emp_id],"N");
			$num=$db_moc->send_cmd($cmd);
			if ($num == 0){
				$cmd="insert into tPerson(person_id,prefix_code,name,surname,name_eng,surname_eng,deptid) values(";
				$cmd.=f_genField($data9[emp_id],"N")." ,".f_genField($data9[prefix_code],"S")." ,".f_genField($data9[name],"S");
				$cmd.=" ,".f_genField($data9[surname],"S")." ,".f_genField($data9[name_eng],"S");
				$cmd.=" ,".f_genField($data9[surname_eng],"S")." ,".f_genField($data9[per_org_serial],"N").")";
			}else{
				$cmd="update tPerson set  prefix_code=".f_genField($data9[prefix_code],"S")." ,name=".f_genField($data9[name],"S");
				$cmd.=" ,surname=".f_genField($data9[surname],"S")." ,name_eng=".f_genField($data9[name_eng],"S");
				$cmd.=" ,surname_eng=".f_genField($data9[surname_eng],"S")." ,deptid=".f_genField($data9[per_org_serial],"N");
				$cmd.=" where person_id=".f_genField($data9[emp_id],"N");
			}//end if
			$db_moc->send_cmd($cmd);
			$cmd="select * from tuser where user_code=".f_genField($strUserCode,"S");
			$num=$db_moc->send_cmd($cmd);
			if ($num == 0){

				$cmd="insert into tUser(user_code,person_id,user_pwd,eff_date,exp_date,group_code) values(";
				$cmd.=f_genField($strUserCode,"S")." ,".f_genField($data9[emp_id],"N").", ".f_genField($data9[user_pwd],"S");
				$cmd.=", ".f_genField($strEffDate,"D").", ".f_genField($strExpDate,"D").",'02')";
			}else{
				$cmd="update tUser set user_pwd=".f_genField($data9[user_pwd],"S");
				$cmd.=",eff_date=".f_genField($strEffDate,"D").",exp_date=".f_genField($strExpDate,"D");
				$cmd.=" where user_code=".f_genField($strUserCode,"S");
			}//end if
			$db_moc->send_cmd($cmd);
			$db_moc->close();
			//identity
			$db_identity = new connect_db_mssql($mssql_identity_host, "[identity]", "opm", "opm");
			$cmd="select * from psst_person where person_id=".f_genField($data9[emp_id],"N");
			$num=$db_identity->send_cmd($cmd);
			if ($num == 0){
				$cmd="insert into psst_person(person_id,prefix_code,name,surname,name_eng,surname_eng,deptid) values(";
				$cmd.=f_genField($data9[emp_id],"N")." ,".f_genField($data9[prefix_code],"S")." ,".f_genField($data9[name],"S");
				$cmd.=" ,".f_genField($data9[surname],"S")." ,".f_genField($data9[name_eng],"S");
				$cmd.=" ,".f_genField($data9[surname_eng],"S")." ,".f_genField($data9[per_org_serial],"N").")";
			}else{
				$cmd="update psst_person set  prefix_code=".f_genField($data9[prefix_code],"S")." ,name=".f_genField($data9[name],"S");
				$cmd.=" ,surname=".f_genField($data9[surname],"S")." ,name_eng=".f_genField($data9[name_eng],"S");
				$cmd.=" ,surname_eng=".f_genField($data9[surname_eng],"S")." ,deptid=".f_genField($data9[per_org_serial],"N");
				$cmd.=" where person_id=".f_genField($data9[emp_id],"N");
			}//end if
			$db_identity->send_cmd($cmd);
			$cmd="select * from ctlt_user where user_code=".f_genField($strUserCode,"S");
			$num=$db_identity->send_cmd($cmd);
			if ($num == 0){
				$cmd="insert into ctlt_user(user_code,person_id,user_pwd,eff_date,exp_date) values(";
				$cmd.=f_genField($strUserCode,"S")." ,".f_genField($data9[emp_id],"N").", ".f_genField($data9[user_pwd],"S");
				$cmd.=", ".f_genField($strEffDate,"D").", ".f_genField($strExpDate,"D").")";
			}else{
				$cmd="update ctlt_user set user_pwd=".f_genField($data9[user_pwd],"S");
				$cmd.=",eff_date=".f_genField($strEffDate,"D").",exp_date=".f_genField($strExpDate,"D");
				$cmd.=" where user_code=".f_genField($strUserCode,"S");
			}//end if
			$db_identity->send_cmd($cmd);
			$db_identity->close();
			//find deptid and id (oracle)
			$strOrgSerial=0;
			if (trim($data9[org_level])=="4")
					$strOrgSerial=$data9[upper_org_serial];
			elseif  (trim($data9[org_level])=="3")
					$strOrgSerial=$data9[org_serial];
			else {
				//org_level="5"
				$cmd =" SELECT     ctlt_organize2.upper_org_serial    FROM  ctlt_organize  ctlt_organize1 ,ctlt_organize   ctlt_organize2 ";
				$cmd.=" WHERE    ctlt_organize1.org_serial = ".f_genField($data9[per_org_serial],"N");
				$cmd.=" and    ctlt_organize1.root_level1 = ctlt_organize2.root_level1 and  ctlt_organize1.root_level2 = ctlt_organize2.root_level2 ";
				$cmd.=" and  ctlt_organize1.root_level3 = ctlt_organize2.root_level3  and  ctlt_organize1.root_level4 = ctlt_organize2.org_code ";
				$cmd.=" and   ctlt_organize2.org_level=4 and ctlt_organize2.org_status='A' "	;
				$num=$db_opm10->send_cmd($cmd);
				if ($num !=0){	
					$data10 = $db_opm10->get_array();
					$data10 = array_change_key_case($data10, CASE_LOWER);
					$strOrgSerial=$data10[upper_org_serial];
				}//end if
			}//end if
			//echo "org=$strOrgSerial<br>";
			$db_ifmuser = new connect_db_mssql($mssql_sql2k_host, "ifmuser", "ifmsa", "infoma");
			$cmd="select max(id) id from user_detail where (id <> 'AAAAA' and id <> '99999')";	
			$num=$db_ifmuser->send_cmd($cmd);
			if ($num !=0){
				$data11 = $db_ifmuser->get_array();
				$data11 = array_change_key_case($data11, CASE_LOWER);
				$tmpnewid1 = (int)$data11[id] + 1;		
				$tmpnewid2 = trim(((string)$tmpnewid1));
				$tmpid=str_pad($tmpnewid2,5-strlen($tmpnewid2),"0",STR_PAD_LEFT);
			}//end if num != 0
			$strDeptID="99999998";
			$strLinkID="998";
			$cmd="select * from ctlt_sdept where org_serial=".f_genField($strOrgSerial,"N");
			$num=$db_opm10->send_cmd($cmd);
			if ($num !=0){	
				$data12 = $db_opm10->get_array();
				$data12 = array_change_key_case($data12, CASE_LOWER);
				$strDeptID=$data12[id];
				$strLinkID=$data12[linkid];
			}//end if
			$cmd="select * from user_detail where lname=".f_genField(strtoupper($strUserCode),"S");
			$num=$db_ifmuser->send_cmd($cmd);
			if ($num == 0){
				$cmd="insert into user_detail(id,lname,lpwd,tname,tsurname,ename,esurname,m_dept,s_dept,u_track,u_chgpwdnext,u_alwchgpwd,u_pause) values(";
				$cmd.=f_genField($tmpid,"S").",".f_genField(strtoupper($strUserCode),"S").",".f_genField(f_EncryptSQL(strtoupper(f_DeCryptOra($data9[user_pwd]))),"S");
				$cmd.=",".f_genField($data9[name],"S").",".f_genField($data9[surname],"S").",".f_genField($data9[name_eng],"S").",".f_genField($data9[surname_eng],"S");
				$cmd.=",' '".",".f_genField($strDeptID,"S").",'0','0','0','0')";
			}else{ 
				$cmd="update user_detail set lpwd=".f_genField(f_EncryptSQL(strtoupper(f_DeCryptOra($data9[user_pwd]))),"S");
				$cmd.=",tname=".f_genField($data9[name],"S").",tsurname=".f_genField($data9[surname],"S");
				$cmd.=",ename=".f_genField($data9[name_eng],"S").",esurname=".f_genField($data9[surname_eng],"S").",s_dept=".f_genField($strDeptID,"S");
				$cmd.=" where  lname=".f_genField(strtoupper($strUserCode),"S");
			}//end if
			$db_ifmuser->send_cmd($cmd);
			//
			$cmd="select ctlt_system_right.sys_code,ctlt_system_right.user_right,ctlt_user_right.seclev, ctlt_user_right.viewlev,ctlt_user_right.dept,ctlt_user_right.viewdoc,";
			$cmd.=" ctlt_user_right.printdoc,ctlt_user_right.scandoc,ctlt_user_right.importdoc,ctlt_user_right.updnote,ctlt_user_right.delnote,ctlt_user_right.grantdoc,";
			$cmd.="ctlt_user_right.printrep,ctlt_user_right.movenoss,ctlt_user_right.viewall,ctlt_user_right.grantnew,ctlt_user_right.otherdept,ctlt_user_right.filingop,";
			$cmd.="ctlt_user_right.folderop,ctlt_user_right.loggedin ";
			$cmd.=" from ctlt_system_right,ctlt_user_right,ctlt_sys_user_right ";
			$cmd.=" where ctlt_system_right.sys_code=ctlt_sys_user_right.sys_code and ctlt_system_right.user_right=ctlt_sys_user_right.user_right ";
			$cmd.=" and ctlt_sys_user_right.user_right=ctlt_user_right.user_right and ";

			$cmd.=" exists(select * from ctlt_system where ctlt_system_right.sys_code=ctlt_system.sys_code and sys_type='A')";
			$cmd.=" and ctlt_system_right.user_code=".f_genField($strUserCode,"S")." and ctlt_system_right.sys_code not in ('MIS') ";
			$db_opm13->send_cmd($cmd);
			while ($data13 = $db_opm13->get_array()){
				$data13 = array_change_key_case($data13, CASE_LOWER);
				if ($data13[sys_code]=="BOARD" || $data13[sys_code]=="RESERVE" ||$data13[sys_code]=="LIB"){
					 $db_sql2k = new connect_db_mssql($mssql_sql2k_host, "opmintranet", "opm", "opm");
					 $strAuthorize=$data13[user_right];
					 $cmd="select * from tSystemRight where user_code=".f_genField($strUserCode,"S")." and sys_code=".f_genField($data13[sys_code],"S");
					 $num1=$db_sql2k->send_cmd($cmd);
					if ($num1==0){
						$cmd="insert into tSystemRight(user_code,sys_code,authorize,seclev, viewlev,viewdoc,";
						$cmd.=" printdoc,scandoc,importdoc,delnote,grantdoc,";
						$cmd.="printrep,movenoss,viewall,grantnew,otherdept,filingop,folderop,loggedin) values(";
						$cmd.=f_genField($strUserCode,"S").",".f_genField($data13[sys_code],"S").",".f_genField($strAuthorize,"S");
						$cmd.=",".f_genField($data13[seclev],"S").",".f_genField($data13[viewlev],"S").",".f_genField($data13[viewdoc],"S");
						$cmd.=",".f_genField($data13[printdoc],"S").",".f_genField($data13[scandoc],"S");
						$cmd.=",".f_genField($data13[importdoc],"S").",".f_genField($data13[delnote],"S").",".f_genField($data13[grantdoc],"S");
						$cmd.=",".f_genField($data13[printrep],"S").",".f_genField($data13[movenoss],"S").",".f_genField($data13[viewall],"S");
						$cmd.=",".f_genField($data13[grantnew],"S").",".f_genField($data13[otherdept],"S").",".f_genField($data13[filingop],"S");
						$cmd.=",".f_genField($data13[folderop],"S").",".f_genField($data13[loggedin],"S").")";
						$cmd="insert into tSystemRight(user_code,sys_code,authorize) values(";
						$cmd.=f_genField($strUserCode,"S").",".f_genField($data13[sys_code],"S").",".f_genField($strAuthorize,"S").")";
					}else{
						$cmd.="update tSystemRight set  authorize=".f_genField($strAuthorize,"S");
						$cmd.=",seclev=".f_genField($data13[seclev],"S").",viewlev=".f_genField($data13[viewlev],"S").",viewdoc=".f_genField($data13[viewdoc],"S");
						$cmd.=",printdoc=".f_genField($data13[printdoc],"S").",scandoc=".f_genField($data13[scandoc],"S");
						$cmd.=",importdoc=".f_genField($data13[importdoc],"S").",delnote=".f_genField($data13[delnote],"S").",grantdoc=".f_genField($data13[grantdoc],"S");
						$cmd.=",printrep=".f_genField($data13[printrep],"S").",movenoss=".f_genField($data13[movenoss],"S").",viewall=".f_genField($data13[viewall],"S");
						$cmd.=",grantnew=".f_genField($data13[grantnew],"S").",otherdept=".f_genField($data13[otherdept],"S").",filingop=".f_genField($data13[filingop],"S");
						$cmd.=",folderop=".f_genField($data13[folderop],"S").",loggedin=".f_genField($data13[loggedin],"S");
						$cmd.=" where user_code=".f_genField($strUserCode,"S")." and sys_code=".f_genField($data13[sys_code],"S");
					}//end if
				}elseif ($data13[sys_code]=="NISWEB"){
						$db_sql2k=new connect_db_mssql($mssql_identity_host, "[identity]", "opm", "opm");						
						$cmd="select * from ctlt_user_group where user_code=".f_genField($strUserCode,"S")." and group_code='01'";
						$num1=$db_sql2k->send_cmd($cmd);
						if ($num1==0){
							$cmd="insert into ctlt_user_group(user_code,group_code) values(";
							$cmd.=f_genField($strUserCode,"S").",'01')"	;
							$db_sql2k->send_cmd($cmd);
						}//end if	
						$strAuthorize=$data13[user_right];
						$cmd="select * from ctlt_System_Right where user_code=".f_genField($strUserCode,"S")." and sys_code=";
						$cmd.=f_genField($data13[sys_code],"S");
						$num1=$db_sql2k->send_cmd($cmd);
						if ($num1==0){
							$cmd="insert into ctlt_System_Right(user_code,sys_code,authorize) values(";
							$cmd.=f_genField($strUserCode,"S").",".f_genField($data13[sys_code],"S").",".f_genField($strAuthorize,"S").")";
						}else{
							$cmd="update ctlt_System_Right set  authorize=".f_genField($strAuthorize,"S");
							$cmd.=" where user_code=".f_genField($strUserCode,"S")." and sys_code=".f_genField($data13[sys_code],"S");
						}//end if	
				}elseif ($data13[sys_code]=="MOC"){			
						 $strAuthorize=$data13[user_right];
						 $db_sql2k = new connect_db_mssql($mssql_sql2k_host, "moc", "opm", "opm");
						 $cmd="select * from tSystemRight where user_code=".f_genField($strUserCode,"S")." and sys_code=".f_genField($data13[sys_code],"S");
	  					 $num1=$db_sql2k->send_cmd($cmd);
						 if ($num1==0){
							$cmd="insert into tSystemRight(user_code,sys_code,authorize) values(";
							$cmd.=f_genField($strUserCode,"S").",".f_genField($data13[sys_code],"S").",".f_genField($strAuthorize,"S").")";
						}else{
							$cmd="update tSystemRight set  authorize=".f_genField($strAuthorize,"S");
							$cmd.=" where user_code=".f_genField($strUserCode,"S")." and sys_code=".f_genField($data13[sys_code],"S");
						}//end if	
					}elseif  ($data13[sys_code]=="NEWS_ACT" || $data13[sys_code]=="NEWS_ANNO" || $data13[sys_code]=="NEWS_AUDIT" ||$data13[sys_code]=="NEWS_CLP" 
			|| $data13[sys_code]=="NEWS_INEX" ||$data13[sys_code]=="NEWS_ROYAL" ||$data13[sys_code]=="NEWS_SERV" ||$data13[sys_code]=="WebBrd" 
			|| $data13[sys_code]=="HR"){
						$strFlag="Y";
						if  ($data13[sys_code]=="NEWS_ACT"){
							$db_sql2k = new connect_db_mssql($mssql_sql2k_host, "opmnews_act", "ifmsa", "infoma");
						}elseif  ($data13[sys_code]=="NEWS_ANNO"){
							$db_sql2k = new connect_db_mssql($mssql_sql2k_host, "opmNews_ANNO", "ifmsa", "infoma");
						}elseif  ($data13[sys_code]=="NEWS_AUDIT"){
							$db_sql2k = new connect_db_mssql($mssql_sql2k_host, "opmNews_audit", "ifmsa", "infoma");
						}elseif  ($data13[sys_code]=="NEWS_CLP"){
							$db_sql2k = new connect_db_mssql($mssql_sql2k_host, "opmNews_clp", "ifmsa", "infoma");
						}elseif  ($data13[sys_code]=="NEWS_INEX"){
							$db_sql2k = new connect_db_mssql($mssql_sql2k_host, "opmnews_inex", "ifmsa", "infoma");

						}elseif  ($data13[sys_code]=="NEWS_ROYAL"){
							$db_sql2k = new connect_db_mssql($mssql_sql2k_host, "opmNews_royal", "ifmsa", "infoma");
						}elseif  ($data13[sys_code]=="NEWS_SERV"){
							$db_sql2k = new connect_db_mssql($mssql_sql2k_host, "opmNews_serv", "ifmsa", "infoma");
						}elseif  ($data13[sys_code]=="WebBrd"){
							$db_sql2k = new connect_db_mssql($mssql_sql2k_host, "OPMWebBrd", "ifmsa", "infoma");
							$strFlag="N";
						}elseif  ($data13[sys_code]=="HR"){
							$db_sql2k = new connect_db_mssql($mssql_sql2k_host, "opmhr", "ifmsa", "infoma");	
							$strFlag="N";
						}// endif
						$cmd="select * from dbuser where name=".f_genField(strtoupper($strUserCode),"S");
						$num1=$db_sql2k->send_cmd($cmd);
						if ($num1==0){
							$cmd="insert into dbuser(name,password, seclev, viewlev,dept,viewdoc,";
							$cmd.=" printdoc,scandoc,importdoc,updnote,delnote,grantdoc,";
							$cmd.="printrep,movenoss,viewall,grantnew,otherdept,filingop,folderop,loggedin) values(";
							$cmd.=f_genField(strtoupper($strUserCode),"S").",".f_genField(f_EncryptSQL(strtoupper(f_DeCryptOra($data9[user_pwd]))),"S");
							$cmd.=",".f_genField($data13[seclev],"S").",".f_genField($data13[viewlev],"S").",".f_genField($strLinkID,"S");
							$cmd.=",".f_genField($data13[viewdoc],"S").",".f_genField($data13[printdoc],"S").",".f_genField($data13[scandoc],"S");
							$cmd.=",".f_genField($data13[importdoc],"S").",".f_genField($data13[updnote],"S").",".f_genField($data13[delnote],"S");
							$cmd.=",".f_genField($data13[grantdoc],"S").",".f_genField($data13[printrep],"S").",".f_genField($data13[movenoss],"S");
							$cmd.=",".f_genField($data13[viewall],"S").",".f_genField($data13[grantnew],"S").",".f_genField($data13[otherdept],"S");
							$cmd.=",".f_genField($data13[filingop],"S").",".f_genField($data13[folderop],"S").",".f_genField($data13[loggedin],"S").")";
						}else{
							$cmd.="update dbuser set  password=".f_genField(f_EncryptSQL(strtoupper(f_DeCryptOra($data9[user_pwd]))),"S");
							$cmd.=",seclev=".f_genField($data13[seclev],"S").",viewlev=".f_genField($data13[viewlev],"S").",dept=".f_genField($strLinkID,"S");
							$cmd.=",viewdoc=".f_genField($data13[viewdoc],"S").",printdoc=".f_genField($data13[printdoc],"S").",scandoc=".f_genField($data13[scandoc],"S");
							$cmd.=",importdoc=".f_genField($data13[importdoc],"S").",updnote=".f_genField($data13[updnote],"S").",delnote=".f_genField($data13[delnote],"S");
							$cmd.=",grantdoc=".f_genField($data13[grantdoc],"S").",printrep=".f_genField($data13[printrep],"S").",movenoss=".f_genField($data13[movenoss],"S");
							$cmd.=",viewall=".f_genField($data13[viewall],"S").",grantnew=".f_genField($data13[grantnew],"S").",otherdept=".f_genField($data13[otherdept],"S");
							$cmd.=",filingop=".f_genField($data13[filingop],"S").",folderop=".f_genField($data13[folderop],"S").",loggedin=".f_genField($data13[loggedin],"S");
							$cmd.=" where name=".f_genField(strtoupper($strUserCode),"S");
						}//end If
						if ($strFlag =="Y"){
							$cmd2="select * from tsystemRight where name=".f_genField(strtoupper($strUserCode),"S");
							$num1=$db_sql2k->send_cmd($cmd2);
							if ($num1==0){
								$cmd2="insert into tsystemRight(name,authorize) values(";
								$cmd2.=f_genField(strtoupper($strUserCode),"S").",".f_genField($data13[user_right],"S").")";
							}else{
								$cmd2="update tsystemRight set authorize=".f_genField($data13[user_right],"S");
								$cmd2.=" where name=".f_genField(strtoupper($strUserCode),"S");
							}//End If
							$db_sql2k->send_cmd($cmd2);
						}//end if
				}//end if
				$db_sql2k->send_cmd($cmd);
				$db_sql2k->free_result();
				$db_sql2k->close();
			}//end while
		}//end if num1 !=0
		$db_opm9->close();
		$db_opm10->close();
		$db_opm13->close();
	}
	function f_del_person_mssql($strID,$strUserCode,$strEffectDate){
		global $mssql_sql2k_host, $mssql_identity_host,$opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
//		$db_opm9 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$db_moc = new connect_db_mssql($mssql_sql2k_host, "moc", "opm", "opm");
		$db_identity = new connect_db_mssql($mssql_identity_host, "[identity]", "opm", "opm");
		$db_opmIntra = new connect_db_mssql($mssql_sql2k_host, "opmintranet", "opm", "opm");
		$cmd="update tPosition set pos_status='2' ,id=null where id=".f_genField($strID,"N");
		$db_opmIntra->send_cmd($cmd);
		$cmd=" update tPerson set  per_status='2' where person_id=".f_genField($strID,"N");
		$db_opmIntra->send_cmd($cmd);
		$strExpDate=substr($strEffectDate,8,2)."/".substr($strEffectDate,5,2)."/".((int)substr($strEffectDate,0,4)+543);
		$cmd=" update tUser set  exp_date=".f_genField($strExpDate,"D")." where user_code=".f_genField($strUserCode,"S");
		$db_opmIntra->send_cmd($cmd);
		$db_opmIntra->close();
		//moc db
		$db_moc->send_cmd($cmd);
		$db_moc->close();
		//identity
		$cmd=" update ctlt_User set  exp_date=".f_genField($strExpDate,"D")." where user_code=".f_genField($strUserCode,"S");
		$db_identity->send_cmd($cmd);
		$db_identity->close();
		//ifmsa
		$db_ifmuser = new connect_db_mssql($mssql_identity_host, "ifmuser", "ifmsa", "infoma");
		$cmd="update user_detail set u_pause='1' where lname=".f_genField(strtoupper($strUserCode),"S");
		$db_ifmuser->send_cmd($cmd);
		$db_ifmuser->close();
		$aa=f_del_system_right($strUserCode);
	}
	function f_del_system_right($strUserCode){
		global $mssql_sql2k_host, $mssql_identity_host,$opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm9 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd="select * from ctlt_system_right where 
				 exists(select * from ctlt_system where ctlt_system_right.sys_code=ctlt_system.sys_code and sys_type='A') and sys_code != 'MIS'
				 and ctlt_system_right.user_code=".f_genField($strUserCode,"S");
		$db_opm9->send_cmd($cmd);
		while ($data9 = $db_opm9->get_array()) {
			$data9 = array_change_key_case($data9, CASE_LOWER);
			$cmd="delete dbuser  where name=".f_genField(strtoupper($strUserCode),"S");
			$strFlag="Y";
			if  ($data9[sys_code]=="NEWS_ACT"){
				$db_sql2k = new connect_db_mssql($mssql_sql2k_host, "opmnews_act", "ifmsa", "infoma");
			}elseif  ($data9[sys_code]=="NEWS_ANNO"){
				$db_sql2k = new connect_db_mssql($mssql_sql2k_host, "opmNews_ANNO", "ifmsa", "infoma");
			}elseif  ($data9[sys_code]=="NEWS_AUDIT"){
				$db_sql2k = new connect_db_mssql($mssql_sql2k_host, "opmNews_audit", "ifmsa", "infoma");
			}elseif  ($data9[sys_code]=="NEWS_CLP"){
				$db_sql2k = new connect_db_mssql($mssql_sql2k_host, "opmNews_clp", "ifmsa", "infoma");
			}elseif  ($data9[sys_code]=="NEWS_INEX"){
				$db_sql2k = new connect_db_mssql($mssql_sql2k_host, "opmnews_inex", "ifmsa", "infoma");
			}elseif  ($data9[sys_code]=="NEWS_ROYAL"){
				$db_sql2k = new connect_db_mssql($mssql_sql2k_host, "opmNews_royal", "ifmsa", "infoma");
			}elseif  ($data9[sys_code]=="NEWS_SERV"){
				$db_sql2k = new connect_db_mssql($mssql_sql2k_host, "opmNews_serv", "ifmsa", "infoma");
			}elseif  ($data9[sys_code]=="WebBrd"){
				$db_sql2k = new connect_db_mssql($mssql_sql2k_host, "OPMWebBrd", "ifmsa", "infoma");
				$strFlag="N";
			}elseif  ($data9[sys_code]=="HR"){
				$db_sql2k = new connect_db_mssql($mssql_sql2k_host, "opmhr", "ifmsa", "infoma");	
				$strFlag="N";			
			}// endif
			if  ($data9[sys_code]=="BOARD" || $data9[sys_code]=="RESERVE"  ||$data9[sys_code]=="LIB"){
				$db_sql2k = new connect_db_mssql($mssql_sql2k_host, "opmintranet", "opm", "opm");
			 	$cmd="delete tSystemRight where user_code=".f_genField($strUserCode,"S")." and sys_code=".f_genField($data9[sys_code],"S");
				$strFlag="N";
			}elseif ($data9[sys_code]=="MOC"){
				 $db_sql2k = new connect_db_mssql($mssql_sql2k_host, "moc", "opm", "opm"); 				
			 	$cmd="delete tSystemRight where user_code=".f_genField($strUserCode,"S")." and sys_code=".f_genField($data9[sys_code],"S");
				$strFlag="N";
			}elseif ($data9[sys_code]=="NISWEB"){
				 $db_sql2k = new connect_db_mssql($mssql_identity_host, "[identity]", "opm", "opm");  				
			 	$cmd="delete ctlt_System_Right where user_code=".f_genField($strUserCode,"S")." and sys_code=".f_genField($data9[sys_code],"S");
				$strFlag="N";
			}elseif ($data9[sys_code]=="OPMPORTAL"){
				$db_sql2k = new connect_db_mssql("172.17.0.52", "[dims_portal]", "opm", "opm");  				
			 	$cmd="delete delete [user] where username=".f_genField($strUserCode,"S");
				$strFlag="N";
			}//end if
			$db_sql2k->send_cmd($cmd);
			if ($strFlag=="Y"){
				$cmd="delete tsystemRight  where name=".f_genField(strtoupper($strUserCode),"S");
				$db_sql2k->send_cmd($cmd);
			}//End if 
			$db_sql2k->free_result();
			$db_sql2k->close();
		}//end while
		$db_opm9->close();
	}
	
	
	function f_get_SessionID(){
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm9 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd="select userenv('SESSIONID') session_id from dual ";
		$num=$db_opm9->send_cmd($cmd);
		if ($num != 0) {
			$data9 = $db_opm9->get_array();
			$data9 = array_change_key_case($data9, CASE_LOWER);
			$strSessionID=$data9[session_id];
		}else{
			$strSessionID="";
		} // end if
		$db_opm9->free_result();
		$db_opm9->close();
		return($strSessionID);
	}
	
	function f_upd_psst_position($strIDDpis,$strPosIDDpis,$strComID,$strPerType,$strEffectDate){
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm91 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$db_opm92 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		$db_dpis91 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$cmd="select * from per_comdtl where com_id=".f_genField($strComID,"N")." and per_id=".f_genField($strIDDpis,"N");
		$strLevelNo="";
		$num44=$db_dpis91->send_cmd($cmd);
		if ($num44==1){
			$data91 = $db_dpis91->get_array();
			$data91 = array_change_key_case($data91, CASE_LOWER);
			$strLevelNo=f_get_curlev($data91[level_no]);
			$strCmdSalary=$data91[cmd_salary];
			$strMovCode=f_get_movement_code($data91[mov_code]);
		}
		$db_dpis91->free_result();
		$db_dpis91->close();
		$strTmpID=f_get_personID($strIDDpis);
		if ($strPerType=="14" || $strPerType=="11")
		{
			$TimeCode="90";
		}else{
			$TimeCode="00";
		}
		
		if ($strPerType !="11")
		{
			$strLevelNo=1;
		}
		$cmd=" update psst_position set id=".f_genField($strTmpID,"N").", pos_status='1'";
		if ($strLevelNo !="") {
			$cmd.=", cur_lev=".f_genField($strLevelNo,"N");
		}//end if
		$cmd.=", salary=".f_genField($strCmdSalary,"N");		
		$cmd.=" where pos_id_dpis=".f_genField($strPosIDDpis,"N")." and per_type=".f_genField($strPerType,"S");
		$db_opm91->send_cmd($cmd);
		
		$cmd="select org_serial from psst_position where pos_id_dpis=".f_genField($strPosIDDpis,"N")." and per_type=".f_genField($strPerType,"S");
		$num91=$db_opm91->send_cmd($cmd);
		if ($num91==1){
			$data91 = $db_opm91->get_array();
			$data91 = array_change_key_case($data91, CASE_LOWER);
			$strOrgSerial=$data91[org_serial];
		}//end if
		
		$cmd="update psst_person set per_status='1', movement_code=".f_genField($strMovCode,"S");
		$cmd.=", per_org_serial=".f_genField($strOrgSerial,"N");
		$cmd.=",time_code=".f_genField($TimeCode,"S");
		//$cmd.=",time_code='90' ";
		$cmd.=" where id=".f_genField($strTmpID,"N");
		$db_opm91->send_cmd($cmd);
		
		//psst_old_time
		//select max seq then plus 1
		$cmd="select nvl(max(time_seq),0) +1 time_seq from psst_old_time where id =".f_genField($strTmpID,"N");
		$num92=$db_opm92->send_cmd($cmd);
		if ($num92==1){
			$data92 = $db_opm92->get_array();
			$data92 = array_change_key_case($data92, CASE_LOWER);
			$strTimeSeq=$data92[time_seq];
		}
		else {
			$strTimeSeq=1;
		}//end if
		
		$cmd="insert into psst_old_time(id,chg_date,time_seq,old_time,per_type) values(";
		//$cmd.=f_genField($strTmpID,"N").",".f_genField($$strEffectDate,"D2").",1,'90')";
		//$cmd.=f_genField($strTmpID,"N").",".f_genField($strEffectDate,"D2").",1,".f_genField($TimeCode,"S").",".f_genField($strPerType,"S").")";
		$cmd.=f_genField($strTmpID,"N").",".f_genField($strEffectDate,"D2").",".f_genField($strTimeSeq,"N").",".f_genField($TimeCode,"S").",".f_genField($strPerType,"S").")";
		$db_opm91->send_cmd($cmd);
		
		$db_opm91->free_result();
		$db_opm91->close();  
		$db_opm92->free_result();
		$db_opm92->close();  
	}
	
	function f_ins_aut_person($strPersonID,$strPersonFlag){
		// date ต้องเป็น  ค.ศ.
		$strPersonSeqTmp=0;
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm90= new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd="select a.id,aut_id,aut_org_serial,d.aut_prefix_code,convert(a.name,'utf8','th8tisascii') name,convert(a.surname,'utf8','th8tisascii') surname,";
		$cmd.=" convert(line_name,'utf8','th8tisascii') line_name, ";
		$cmd.=" id_card,email,decode(sex,'1','M','F') sex ,convert(address,'utf8','th8tisascii') address,tel,a.user_id user_id ,";
		$cmd.= " to_char(a.last_date,'dd/mm/')||to_char(a.last_date,'yyyy') last_date ";
		$cmd.=" from psst_person a, psst_position b, ctlt_organize c,ctlt_prefix_code d ,psst_line_code e ";
		$cmd.=" where a.id=b.id and b.org_serial=c.org_serial and a.prefix_code=d.prefix_code ";
		$cmd.=" and b.line_code=e.line_code ";
		$cmd.=" and a.id=".f_genField($strPersonID,"N");
		$num=$db_opm90->send_cmd($cmd);
		//echo "num=$cmd<br>";
		if ($num==1) {
			$data = $db_opm90->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$conn = new COM("ADODB.Connection") or die("Cannot start ADO");
			global $autMySQL_Connect;
			$conn->Open($autMySQL_Connect);
			$rsMySQL= new COM("ADODB.Recordset");
			if ($strPersonFlag=="Y") {
				$strPersonID=-1;
			}
			$cmd="select * from aut_person where person_id=".f_genField($strPersonID,"N");
			//echo "$cmd<br>";
			$rsMySQL->Open($cmd,$conn,1,3);
			if ($rsMySQL->eof){
				$cmd="select seq from arc_pkctrl where table_name='aut_person' ";
				//echo "insert aut_person<br>";
				$rsMySQL2= new COM("ADODB.Recordset");
				$rsMySQL2->Open($cmd,$conn,1,1);
				if (!$rsMySQL2->eof){
					$strSeq=$rsMySQL2->fields["seq"]->value;
				}
				$rsMySQL2->close();
				$rsMySQL2->null;
				$cmd="update arc_pkctrl set seq=seq+1 where table_name='aut_person' ";
				$conn->execute($cmd);
				//update psst_person
				$db_opm91= new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
				$cmd="update psst_person set aut_id=".f_genField($strSeq,"N");
				$cmd.=" where id=".f_genField($data["id"],"N");
				$num=$db_opm91->send_cmd($cmd);
				$db_opm91->free_result();
				$db_opm91->close();
				//end
				$rsMySQL->addNew();
				$rsMySQL->fields["person_seq"]->value=$strSeq;
				$rsMySQL->fields["person_id"]->value=$data["id"];
				$rsMySQL->fields["site_seq"]->value="0";
				$strPersonSeqTmp=$strSeq;
			} //end if
			$UPDATE_DATE = date("Y-m-d H:i:s");
			if ($data["last_date"]=="") {
				$strLastDate=$UPDATE_DATE;				
			}else{
				$strLastDate=$data["last_date"];
			}
			//echo "update aut_person<br>";
			$rsMySQL->fields["org_seq"]->value=$data["aut_org_serial"];
			$rsMySQL->fields["title_code"]->value=$data["aut_prefix_code"];
			$rsMySQL->fields["name"]->value=$data["name"];
			$rsMySQL->fields["surname"]->value=$data["surname"];
			$rsMySQL->fields["position_name"]->value=$data["line_name"];
			$rsMySQL->fields["idcard"]->value=$data["id_card"];
			$rsMySQL->fields["email"]->value=$data["email"];
			$rsMySQL->fields["sex"]->value=$data["sex"];
			$rsMySQL->fields["address"]->value=$data["address"];
			$rsMySQL->fields["tel"]->value=$data["tel"];
			$rsMySQL->fields["updateuserid"]->value="dpis";
			$rsMySQL->fields["lastupdate"]->value=$strLastDate;
			$rsMySQL->update();
			$rsMySQL->close();
			$rsMySQL->null;
			$conn->close();
			$conn=null;
		}
		$db_opm90->free_result();
		$db_opm90->close();
		return ($strPersonSeqTmp);
	}
	
	function f_ins_psst_person($strIDDpis){
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm99 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$db_opm91 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		$db_dpis98 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

		$cmd="select * from per_personal where per_id=".f_genField($strIDDpis,"N");
		$num=$db_dpis98->send_cmd($cmd);
		//echo $cmd."num=".$num;
		if ($num==1){
			$data98 = $db_dpis98->get_array();
			$data98 = array_change_key_case($data98, CASE_LOWER);
			$strPerType=f_get_per_type($data98[ot_code]);	
			$strPrefixCode=f_get_prefix_code($data98[pn_code]);
			$strCurLev=f_get_curlev($data98[level_no]);
			$strMarital=f_get_marital($data98[mr_code]);
			$strBlood=f_get_blood($data98[per_blood]);
			$strReligionCode=f_get_religion_code($data98[re_code]);
			$strLocSerial=f_get_loc_serial($data98[pv_code]);
			$strMovementCode=f_get_movement_code($data98[mov_code]);
			$strAddr=$data98[per_add1]." ".$data98[per_add2];
			if ($data98[per_number]==0)
				$strFundStatus="2";
			else
				$strFundStatus="1";
			//end if
			$cmd="select * from psst_position where pos_id_dpis=".f_genField($data98[pos_id],"N")." and per_type=".f_genField(strPerType,"S");
			$num2=$db_opm91->send_cmd($cmd);
			if ($num2==1){
				$data91 = $db_opm91->get_array();
				$data91 = array_change_key_case($data91, CASE_LOWER);
				$strPosID=$data91[pos_id];
				$strOrgSerial=$data91[org_serial];
				$strLineCode=$data91[line_code];
				
			}//end if
			if ($data98[per_status]==1){
				$strPerStatus=1;
			}else{
				$strPerStatus=2;
			}//
			
			$cmd="select * from psst_person where per_id_dpis=".f_genField($strIDDpis,"N");//." or id_card=".f_genField($data98[id_card],"S");
			$num1=$db_opm99->send_cmd($cmd);
			if ($num1==0){
				//sal_lev,sal_code,salary,approve_date,start_date,
				$strPerSerialNo=f_get_per_serial();
				$strID=$strPerSerialNo;
				// check id from cpst if found set id is null
				$strPerCardNo=$data98[id_card];
				$cmd2="select * from psst_person where id_card=".f_genField($strPerCardNo,"S");
				$num2=$db_opm99->send_cmd($cmd);
				if ($num2 != 0){
					$strPerCardNo="";
				}
				//******
				$cmd="insert into psst_person (id, per_id_dpis, per_status, per_type,";
				$cmd.=" prefix_code, name, surname, name_eng, surname_eng, ";
				$cmd.=" per_org_serial, sal_lev_recv, salary_recv, sex, marital, id_card, tax_no, blood_group, ";
				$cmd.=" religion_code, born_date, retire_date, enter_date, start_date, esc_test_date, quit_date, ";
				$cmd.=" address, born_loc_serial, movement_code,  fund_status, ";
				$cmd.=" tel, tel_org, fax, mobile, email,org_type) values(";
				$cmd.=f_genField($strPerSerialNo,"N").",".f_genField($strIDDpis,"N").",";
				$cmd.=f_genField($strPerStatus,"S").",".f_genField($strPerType,"S").",".f_genField($strPrefixCode,"S").",";
				$cmd.=f_genField($data98[per_name],"S").",".f_genField($data98[per_surname],"S").",".f_genField(strtolower($data98[per_eng_name]),"S").",";
				$cmd.=f_genField(strtolower($data98[per_eng_surname]),"S").",".f_genField($strOrgSerial,"N").",";
				$cmd.=f_genField($strCurLev,"S").",".f_genField($data98[per_salary],"N").",".f_genField($data98[per_gender],"S").",";
				$cmd.=f_genField($strMarital,"S").",".f_genField($strPerCardNo,"S").",".f_genField($data98[per_taxno],"S").",";
				$cmd.=f_genField($strBlood,"S").",".f_genField($strReligionCode,"S").",".f_genField($data98[per_birthdate],"D2").",";
				$cmd.=f_genField($data98[per_retiredate],"D2").",".f_genField($data98[per_startdate],"D2").",".f_genField($data98[per_occupydate],"D2").",";
				$cmd.=f_genField($data98[per_occupydate],"D2").",".f_genField($data98[per_posdate],"D2").",";
				$cmd.=f_genField($strAddr,"S").",".f_genField($strLocSerial,"N").",".f_genField($strMovementCode,"S").",";
				$cmd.=f_genField($strFundStatus,"S").",".f_genField($data98[per_home_tel],"S").",".f_genField($data98[per_office_tel],"S").",";
				$cmd.=f_genField($data98[per_fax],"S").",".f_genField($data98[per_mobile],"S").",".f_genField($data98[per_email],"S").",'10')";
			}else{
				$strID=f_get_personID($data98[per_id]);
				$cmd=" update  psst_person set per_type=".f_genField($strPerType,"S").",prefix_code=".f_genField($strPrefixCode,"S");
				$cmd.=",per_id_dpis=".f_genField($strIDDpis,"N");
				$cmd.=",name=".f_genField($data98[per_name],"S").",surname=".f_genField($data98[per_surname],"S");
				$cmd.=",name_eng=".f_genField(strtolower($data98[per_eng_name]),"S").",surname_eng=".f_genField(strtolower($data98[per_eng_surname]),"S");
				$cmd.=",per_org_serial=".f_genField($strOrgSerial,"N").",sal_lev_recv=".f_genField($strCurLev,"S");
				$cmd.=",salary_recv=".f_genField($data98[per_salary],"N").",sex=".f_genField($data98[per_gender],"S");
				$cmd.=",marital=".f_genField($strMarital,"S").",id_card=".f_genField($data98[per_cardno],"S");
				$cmd.=",tax_no=".f_genField($data98[per_taxno],"S").",blood_group=".f_genField($strBlood,"S");
				$cmd.=",religion_code=".f_genField($strReligionCode,"S").",born_date=".f_genField($data98[per_birthdate],"D2");
				$cmd.=",retire_date=".f_genField($data98[per_retiredate],"D2").",enter_date=".f_genField($data98[per_startdate],"D2");
				$cmd.=",start_date=".f_genField($data98[per_occupydate],"D2").",esc_test_date=".f_genField($data98[per_occupydate],"D2");
				$cmd.=",quit_date=".f_genField($data98[per_posdate],"D2").",address=".f_genField($strAddr,"S");
				$cmd.=",born_loc_serial=".f_genField($strLocSerial,"N").",movement_code=".f_genField($strMovementCode,"S");
				$cmd.=",fund_status=".f_genField($strFundStatus,"S").",tel=".f_genField($data98[per_home_tel],"S");
				$cmd.=",fax=".f_genField($data98[per_fax],"S");
				//$cmd.=",tel_org=".f_genField($data98[per_office_tel],"S").",fax=".f_genField($data98[per_fax],"S");
				//$cmd.=",mobile=".f_genField($data98[per_mobile],"S").",email=".f_genField($data98[per_email],"S");
				$cmd.=" where  id=".f_genField($strID,"N");//.id_card=".f_genField($data98[per_cardno],"S")." or ;
				//
			}//end if	num1=0	
			
			$db_opm99->send_cmd($cmd);
		}//end if num=0
		/**/
		$db_opm99->free_result();
		$db_opm99->close();
		$db_opm91->free_result();
		$db_opm91->close();
		$db_dpis98->free_result();
		$db_dpis98->close();
	}
	function f_ins_aut_user ($strUserCode,$strAutID,$strUserPwdLdap,$strEffectDate,$strRetireDate){
	//insert aut_user
			global $autMySQL_Connect;
			$conn = new COM("ADODB.Connection") or die("Cannot start ADO");
			$conn->Open($autMySQL_Connect);
			$rsMySQL= new COM("ADODB.Recordset");
			$cmd="select * from aut_user where user_id= ".f_genField($strUserCode,"S");
			//echo "$cmd<br>";
			//echo "$strAutID<br>";
			$rsMySQL->Open($cmd,$conn,1,3);
			if ($rsMySQL->eof){
				$UPDATE_DATE = date("Y-m-d H:i:s");
				$rsMySQL->addNew();
				$rsMySQL->fields["user_id"]->value=$strUserCode;
				$rsMySQL->fields["person_seq"]->value=$strAutID;
			} //end if
			$rsMySQL->fields["password"]->value=f_EncryptMD5Arc($strUserPwdLdap);
			//$rsMySQL->fields["initialdate"]->value=f_genField($strEffectDate,"D3");
			//$rsMySQL->fields["expiredate"]->value=f_genField($strRetireDate,"D3");
			$rsMySQL->fields["Disable"]->value="0";
			$rsMySQL->fields["user_flag"]->value="C";
			$rsMySQL->fields["Createdate"]->value=$UPDATE_DATE;
			$rsMySQL->fields["lastupdate"]->value=$UPDATE_DATE;
			$rsMySQL->fields["updateuserid"]->value="dpis";
			//echo "insert aut_user <br>";
			$rsMySQL->update();
			$rsMySQL->close();
			$rsMySQL=null;
			//update again
			if ($strEffectDate =="")
				$strEffectDate="null";					
			else{
				$strEffectDate=str_replace('/','-',$strEffectDate);
				$strEffectDate="str_to_date('".$strEffectDate."','%Y-%m-%d')";
			}
			if ($strRetireDate =="")
				$strRetireDate="null";					
			else{
				$strRetireDate=str_replace('/','-',$strRetireDate);
				$strRetireDate="str_to_date('".$strRetireDate."','%Y-%m-%d')";
			}
			$strSQL="update aut_user set initialdate=".$strEffectDate;
			$strSQL.=",expiredate=".$strRetireDate;
			$strSQL.=" where user_id=".f_genField($strUserCode,"S");
			
			$conn->execute($strSQL);
			//echo "$strSQL<br>";
			$conn->close();
			$conn=null;
			
	
			
			//end insert aut_user
	}
	function f_EncryptMD5Arc($strPaintext){
	if ($strPaintext=="") {
		$output="";
	}else{
		$obj = new COM("HashModule.HFunction");
		$temp = $obj->hash("SetSepComm~@;");
		$output = $obj->hash("EncryptByMD5ToString~".$strPaintext.";");
		$temp = $obj->hash("SetSepComm~#;");
		$output=substr($output,0,strpos($output,"==")+3);
	}
	return($output);
	}
//============================================================================
	/*class connect_db_aut extends connect_odbc { };
	$autMySQL_Connect = "Driver={MySQL ODBC 5.3 Unicode Driver}; Server=172.17.0.17;UID=archive;pwd=archive;database=archivedb;option=3306;CharSet=utf8";
	$aut_host = "172.17.0.17";
	$aut_dsn="archive";
	$aut_user = "archive";
	$aut_pwd = "archive";
	$strUserCode=f_new_person(276,"02","2008/08/29","10140","312",); */
	//$strIDDpis,$strLevelNo,$strEffectDate,$strMoveCode,$strPosIDDpis,$strComID,$strPerType
	//$strUserCode=f_get_sast_per_aud_seq (76,24);
	//$strUserCode=f_set_exp_emp(276,"2008/08/29","1840");
	//$strUserCode=f_Delete_Queue(25067);
	//$strUserCode=$strUserCode=f_Delete_Queue(f_get_personID(401));
	//$strUserID=f_get_curlev(401);
	
?>