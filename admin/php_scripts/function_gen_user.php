<?
	// include("$ROOTPATH/php_scripts/connect_oci8.php");
	function encryptPwd($strPwd){
		$SERVER_TYPE = explode("/", $_SERVER['SERVER_SOFTWARE']);
		if( substr_count($SERVER_TYPE[0], "Apache") ){
			$ROOTPATH = $_SERVER['DOCUMENT_ROOT'];
		}elseif( substr_count($SERVER_TYPE[0], "IIS") ){
			$ROOTPATH = $_SERVER['APPL_PHYSICAL_PATH'];
		//		$virtual_site = "DREAM/";
		}
		include("$ROOTPATH/php_scripts/db_connect_var.php");
		//echo "opm_db_host=$opm_db_host,opm_db_sid=$opm_db_sid,opm_db_user=$opm_db_user,opm_db_pwd=$opm_db_pwd,opm_db_port=$opm_db_port";
		// global $db_host,$db_user,$db_pwd, $db_port;
		// $db_opm99 = new connect_db_opm($db_host, $db_name, $db_user, $db_pwd, $db_port);
		$db_opm99 = new connect_oci8($db_host, $db_name, $db_user, $db_pwd, $db_port);
		$strTmpPwd="";
		$cmd = " select * from ctlt_policy ";		
		$db_opm99->send_cmd($cmd);
		while ($data99 = $db_opm99->get_array()) {
			$data99 = array_change_key_case($data99, CASE_LOWER);
			$strPwdForm=$data99[pwd_form];
			//$strPwdForm="2";
			if ($strPwdForm == "1") {		    
				$strPwdNum=$data99[pwd_num1] % 255;
				$strPwdOper=$data99[pwd_oper1];
				$strPwdRev=$data99[pwd_reverse1];}
			else{
		// 2
				$strWordConst=$data99[word_const];
				for ($i=0;$i<strlen($strWordConst);$i++){
					
					$strPwdNum=($strPwdNum+ord(substr($strWordConst,$i,1))) % 255;
					} //end for
				$strPwdOper=$data99[pwd_oper2];
				$strPwdRev=$data99[pwd_reverse2];
			}//end if
			//Check Operator
			if ($strPwdOper == "A"){
				for ($i=0;$i<strlen($strPwd);$i++){
					$strTmpPwd .=chr(((int)ord(substr($strPwd,$i,1))+$strPwdNum) % 255);
					//echo "strTmpPwd=$strTmpPwd<br>";
					}//next		
			}
			else{
				for ($i=0;$i<strlen($strPwd);$i++){
					$strTmpPwd .=chr(((int)ord(substr($strPwd,$i,1))-$strPwdNum) % 255);
				}//next	
			}//end if
			//'--*- reverse
			if ($strPwdRev == "Y"){				 
				$strTmpPwd=strrev($strTmpPwd);	
			}//end if  
		} // end while*/
		return $strTmpPwd;
	}
	function decryptPwd($strPwd){
		// global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		// $db_opm99 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$db_opm99 = new connect_oci8($db_host, $db_name, $db_user, $db_pwd, $db_port);
		$strOut="";
		$cmd = " select * from ctlt_policy ";		
		$db_opm99->send_cmd($cmd);
		while ($data99 = $db_opm99->get_array()) { 
			$data99 = array_change_key_case($data99, CASE_LOWER);
			$strPwdForm=$data99[pwd_form];
			//$strPwdForm="2";
			if ($strPwdForm == "1") {		    
				$strPwdNum=$data99[pwd_num1] % 255;
				$strPwdOper=$data99[pwd_oper1];
				$strPwdRev=$data99[pwd_reverse1];}
			else{
		// 2
				$strWordConst=$data99[word_const];
				for ($i=0;$i<strlen($strWordConst);$i++){
					
					$strPwdNum=($strPwdNum+ord(substr($strWordConst,$i,1))) % 255;
					} //end for
				$strPwdOper=$data99[pwd_oper2];
				$strPwdRev=$data99[pwd_reverse2];
				}//end if
		}//end while
		
		$strTmpPwd="";
		//$strTmpPwd=$strPwd;
		if ($strPwdRev == "Y") {
			 $strTmpPwd=strrev($strPwd);	
		}//end if   
		//Check Operator
		if ($strPwdOper == "A"){
			for ($i=0;$i<strlen($strTmpPwd);$i++){
				$strOut .=chr(((int)ord(substr($strTmpPwd,$i,1))-$strPwdNum) % 255);
				}//next		
		}
		else{
			for ($i=0;$i<strlen($strTmpPwd);$i++){
				$strOut .=chr(((int)ord(substr($strTmpPwd,$i,1))+$strPwdNum) % 255);
			;}//next	
		}//end if 	*/
		
		return $strOut;
 	}
 	function genUserID($strNameEng, $strSurnameEng,$strID){
		// global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		// $db_opm99 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$db_opm99 = new connect_oci8($db_host, $db_name, $db_user, $db_pwd, $db_port);
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		$db_dpis9 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$cmd = " select * from ctlt_policy ";	
		//echo "name1=$strNameEng,sur1=$strSurnameEng<br>";
	//	echo "$cmd<br>";	
		$num=$db_opm99->send_cmd($cmd);
	//	$db_opm99->show_error();
	//	$num=$db_opm99->num_rows();
	//	echo "num=$num<br>";
		while ($data99 = $db_opm99->get_array()) {
			$data99 = array_change_key_case($data99, CASE_LOWER);
			if ($data99[user_form]=="1"){
				$strFnamePosLen    = $data99[fname_pos_len1];
				$strFnamePosStatus = $data99[fname_pos_status1];
				$strLnamePosLen    = $data99[lname_pos_len1];
				$strLnamePosStatus = $data99[lname_pos_status1];
				$strDupPos	 	   = $data99[dup_lname_pos];		
				$strFname		   = $strNameEng;
				$strLname		   = $strSurnameEng;
				$strSeperate     	= $data99[seperate1];
			}else {
				$strFnamePosLen    = $data99[lname_pos_len2];
				$strFnamePosStatus = $data99[lname_pos_status2];
				$strLnamePosLen    = $data99[fname_pos_len2];
				$strLnamePosStatus = $data99[fname_pos_status2];
				$strDupPos	 	   = $data99[dup_fname_pos];		
				$strFname		   = $strSurnameEng;
				$strLname		   = $strNameEng;
				$strSeperate     	= $data99[seperate2];
			} //end if
			//first position
			if ($strFnamePosStatus == "F") {
				$strFname = substr($strFname,0,$strFnamePosLen);}
			else {
				$strFname = substr(strrev($strFname),0,$strFnamePosLen);
			} //end if;
			//echo "<br>";
			
			//reverse right position
			if ($strLnamePosStatus == "B") { 
				$strLname = strrev($strLname);
			} //end if;	
			//echo "strFname=$strFname,strFname=$strFname,strLname=$strLname,len=$len<br>";
			if (strlen($strLname) < $strLnamePosLen) {
				$strTmp = strlen($strLname);
				$strLnamePosLen = strlen($strLname);}
			else{
				$strTmp = $strLnamePosLen;
			} //end if;
			$strUserCode = $strFname.$strSeperate.substr($strLname,0,$strLnamePosLen);
			$strFlag = "N";
			$strFirst = "Y";	
			while (($strFlag == "N" ) && ($strTmp <= strlen($strLname))) {
				//fname+lanem1+lname2 ....
				if ($strFirst =="Y"){			
					$strUserCode = $strFname.$strSeperate.substr($strLname,0,$strLnamePosLen);
					$strFirst ="N";
				}
				else{		
					$strUserCode = $strFname.$strSeperate.substr($strLname,0,$strLnamePosLen-1).substr($strLname,$strTmp-1,1);
				} //end if;
				//echo "in while user=$strUserCode<br>";
				$strTmp = $strTmp+$strDupPos;
				$strDupPos = 1;				
				//check dup
				if ($strID=="9999999"){
					$cmd="select 'N' from user_detail username='$strUserCode'";
					$db_com = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
				}else{
					$cmd=" select 'N' from ctlt_emp	where user_code = '$strUserCode' ";
					$db_com = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
				}
				$db_com->send_cmd($cmd);
				if ($db_com->num_rows()== 0) {
					$strFlag = "Y";
				}else{
					 $strFlag = "N";
					 $cmd= "insert into ctlt_dup_user (id,user_code) values ($strID,'$strUserCode') ";
					// echo "$cmd<br>";
					 $db_com->send_cmd($cmd);
					// $db_opm99->show_error();
		 //			$db_opm99->show_error();
				}				
			} //end while  
		}//end while 
		return $strUserCode;
	}
?>
