<?
	function f_genField($strField,$strType){
		$strFieldTemp=$strField;
		if ($strType == "S"){
				$strFieldTemp=str_replace("'","''",$strFieldTemp);
				if ($strFieldTemp !="")
						$strFieldTemp = "'".trim($strFieldTemp)."'";
				else
						$strFieldTemp="null";
				//end if
		}elseif ($strType == "N"){
				if ($strFieldTemp =="")
					$strFieldTemp="null";
				//end if
		}elseif ($strType == "D"){
				if ($strFieldTemp ==""){
					$strFieldTemp="null";					
				}else{
					$strFieldTemp=substr($strFieldTemp,0,2)."/".substr($strFieldTemp,3,2)."/".((int)substr($strField,6,4)-543);
					$strFieldTemp="convert(datetime,'".$strFieldTemp."',103)";
				}//end if
		}elseif ($strType == "D2"){
		//D2=Date for Oracle
				if ($strFieldTemp =="")
					$strFieldTemp="null";					
				else{
					$strFieldTemp=str_replace('/','',$strFieldTemp);
					$strFieldTemp=str_replace('-','',$strFieldTemp);
					$strFieldTemp="to_date('".$strFieldTemp."','yyyymmdd')";
				}//end if
		}elseif ($strType="D3"){
			//D3=Date for mysql input yyyy-mm-dd
				if ($strFieldTemp =="")
					$strFieldTemp="null";					
				else{
					//$strFieldTemp=str_replace('/','-',$strFieldTemp);
					//$strFieldTemp="str_to_date('".$strFieldTemp."','%Y-%m-%d')";
					//old
					$strFieldTemp=str_replace('-','/',$strFieldTemp);
					//$strFieldTemp=substr($strFieldTemp,8,2)."/".substr($strFieldTemp,5,2)."/".substr($strFieldTemp,0,4);
					$strFieldTemp=substr($strFieldTemp,5,2)."/".substr($strFieldTemp,8,2)."/".substr($strFieldTemp,0,4);
					//new format
					//$strFieldTemp=str_replace('/','',$strFieldTemp);
					//$strFieldTemp=str_replace('-','',$strFieldTemp);
					//$strFieldTemp="str_to_date('".$strFieldTemp."','%Y%m%d')";
					//$strFieldTemp=str_replace('/','-',$strFieldTemp);
					//$strFieldTemp="'".$strFieldTemp."'";
					
					
				}//end if
		}
		else{
			$strFieldTemp="null";
		}//end if
		return ($strFieldTemp);
	}
	function f_EncryptSQL($s)
	{
		if(strcmp($s, "") == 0)
			return "";

		$newstring = "";
		$slen = strlen($s);
		$bytearr = array($slen); 
		
		for($i=0; $i<$slen; $i++)
			$bytearr[$i] = ord(substr($s, $i, 1)) + ($i+1);

		for($i=0; $i<$slen-1; $i++)
			$bytearr[$i] = $bytearr[$i] * $bytearr[$i+1];

		$bytearr[$slen-1] = $bytearr[$slen-1] * $bytearr[0];

		for($i=0; $i<$slen; $i++)
		{
			while($bytearr[$i] < ord("Z"))
				$bytearr[$i] += 147;

			while($bytearr[$i] > ord("Z"))
				$bytearr[$i] = $bytearr[$i] - (ord("Z") - ord("A"));

			$newstring .= chr($bytearr[$i]);
		}//end for
	
		return $newstring;
		
	} //end function
//============================================================================


//================================= Decypt ====================================
function f_DeCryptOra($strPwd)
{
	//-----------------------------

	$strTmpPwd = $strPwd;
	$strOut = "";

	if(strcmp($strPwdRev, "Y") == 0)
	{
		$i = 0;
		$alen = strlen($strTmpPwd);
		while($i < $alen)
		{
			$strOut = substr($strTmpPwd, $i, 1).$strOut;
			$i++;
		}
		$strTmpPwd = $strOut;
	}//end if

	$strOut = "";
	if(strcmp($strPwdOper, "A") == 0)
	{
		for($i=0; $i<strlen($strTmpPwd); $i++)
			$strOut .= chr(ord(substr($strTmpPwd, $i , 1)) - $strPwdNum);
	}
	else
	{
		for($i=0; $i<strlen($strTmpPwd); $i++)
			$strOut .= chr(ord(substr($strTmpPwd, $i, 1)) + $strPwdNum);
	}

	return $strOut;

}//end function
	function f_get_per_type($strOTCode){
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm99 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd="select min(per_type) per_type from psst_per_type where trim(ot_code)=".f_genField(trim($strOTCode),"S");
		$num=$db_opm99->send_cmd($cmd);
		if ($num==1){
			$data99 = $db_opm99->get_array();
			$data99 = array_change_key_case($data99, CASE_LOWER);
			$strPerType=$data99[per_type];
		}//end if
		$db_opm99->free_result();
		$db_opm99->close();
		return($strPerType);
	}
	function f_get_org_serial($strOrgID,$strOrgID1,$strOrgID2){		
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm99 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		if ($strOrgID != "" && $strOrgID1=="" && $strOrgID2==""){
			$strOrgIDTmp=$strOrgID;
		}elseif ($strOrgID != "" && $strOrgID1 !="" && $strOrgID2==""){
			$strOrgIDTmp=$strOrgID1;
		}else{
			$strOrgIDTmp=$strOrgID2;
		}	//org
		$cmd="select org_serial from ctlt_organize where org_id=".f_genField($strOrgIDTmp,"S");
		$num=$db_opm99->send_cmd($cmd);
		if ($num != 0) {
			$data99 = $db_opm99->get_array();
			$data99 = array_change_key_case($data99, CASE_LOWER);
			$strOrgSerial=$data99[org_serial];
		}// end if
		$db_opm99->free_result();
		$db_opm99->close();
		return($strOrgSerial);
	} 
	function f_get_admin_code($strPMCode){		
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm99 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd="select admin_code from psst_admin_code where trim(pm_code)=".f_genField(trim($strPMCode),"S");
		$num=$db_opm99->send_cmd($cmd);
		if ($num != 0) {
			$data99 = $db_opm99->get_array();
			$data99 = array_change_key_case($data99, CASE_LOWER);
			$strAdminCode=$data99[admin_code];
		}// end if
		$db_opm99->free_result();
		$db_opm99->close();
		return($strAdminCode);
	} 
	function f_get_expert_code($strSkillCode){		
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm99 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd="select min(expert_code) expert_code from psst_expert_code where trim(skill_code)=".f_genField(trim($strSkillCode),"S");
		$num=$db_opm99->send_cmd($cmd);
		if ($num != 0) {
			$data99 = $db_opm99->get_array();
			$data99 = array_change_key_case($data99, CASE_LOWER);
			$strExpertCode=$data99[expoert_code];
		}// end if
		$db_opm99->free_result();
		$db_opm99->close();
		return($strExpertCode);
	} 
	function f_get_line_code($strCode,$strPerType){		
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm99 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		if ($strPerType=="11"){
			$cmd="select max(line_code) line_code from psst_line_code where pl_code=";
		}elseif ($strPerType=="21"){
			$cmd="select min(line_code) line_code from psst_line_code where pn_code=";
		}else{ // ($strPerType=="14"){
			$cmd="select min(line_code) line_code from psst_line_code where ep_code=";
		}
		$cmd.=f_genField($strCode,"S")." and per_type=".f_genField($strPerType,"S");
		$num=$db_opm99->send_cmd($cmd);
		if ($num != 0) {
			$data99 = $db_opm99->get_array();
			$data99 = array_change_key_case($data99, CASE_LOWER);
			$strLineCode=$data99[line_code];
		}// end if		
		$db_opm99->free_result();
		$db_opm99->close();
		return($strLineCode);
	} 
	function f_get_cl_code($strCLName){		
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		$db_dpis8 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd , null);
		$cmd="select cl_code from per_co_level where cl_name=".f_genField($strCLName,"S"); 
		$num=$db_dpis8->send_cmd($cmd);
		if ($num != 0) {
			$data92 = $db_dpis8->get_array();
			$data92 = array_change_key_case($data92, CASE_LOWER);
			$strClCodeTmp=$data92[cl_code];
		}// end if
		$db_dpis8->free_result();
		$db_dpis8->close();
		return($strClCodeTmp);
	} 
	function f_get_length_code($strCLCode){		
		//except per_type=14,21
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm99 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd="select min(length_code) length_code from psst_length_code where trim(cl_code)=".f_genField(trim($strCLCode),"S");
		$num=$db_opm99->send_cmd($cmd);
		if ($num != 0) {
			$data99 = $db_opm99->get_array();
			$data99 = array_change_key_case($data99, CASE_LOWER);
			$strLengthCodeTmp=$data99[length_code];
		}// end if
		$db_opm99->free_result();
		$db_opm99->close();
		return($strLengthCodeTmp);
	} 
	function f_get_curlev($strLevelNo){		
		//except per_type=14,21
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm99 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
	//	$cmd="select min(cur_lev) cur_lev from psst_per_level where trim(level_no)=".f_genField(trim($strLevelNo),"S");
		$cmd="select min(cur_lev) cur_lev from psst_per_level where trim(level_no) in (".f_genField($strLevelNo,"S").",'0$strLevelNo')";
		$num=$db_opm99->send_cmd($cmd);
		if ($num != 0) {
			$data99 = $db_opm99->get_array();
			$data99 = array_change_key_case($data99, CASE_LOWER);
			$strCurLev=$data99[cur_lev];
		}// end if
		$db_opm99->free_result();
		$db_opm99->close();
		return($strCurLev);
	}
	function f_get_postype($strPTCode){		
		//except per_type=14,21
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm99 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd="select min(pos_type) pos_type from psst_pos_type  where trim(pt_code)=".f_genField(trim($strPTCode),"S");
		$num=$db_opm99->send_cmd($cmd);
		if ($num != 0) {
			$data99 = $db_opm99->get_array();
			$data99 = array_change_key_case($data99, CASE_LOWER);
			$strPosType=$data99[pos_type];
		}// end if
		$db_opm99->free_result();
		$db_opm99->close();
		return($strPosType);
	}
	
	function f_get_pos_id($strPosIDDpis,$strPerType){		
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm92 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd="select pos_id from psst_position where  pos_id_dpis=".f_genField($strPosIDDpis,"S")." and per_type=".f_genField($strPerType,"S");
		$num=$db_opm92->send_cmd($cmd);
		if ($num != 0) {
			$data92 = $db_opm92->get_array();
			$data92 = array_change_key_case($data92, CASE_LOWER);
			$strPosID=$data92[pos_id];
		}// end if
		$db_opm92->free_result();
		$db_opm92->close();
		return($strPosID);
	} 
	
	function f_get_movement_code($strMovCodeDpis){		
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm92 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd="select min(movement_code) movement_code from psst_movement_code where mov_code=".f_genField($strMovCodeDpis,"S");
		$num=$db_opm92->send_cmd($cmd);
		if ($num != 0) {
			$data92 = $db_opm92->get_array();
			$data92 = array_change_key_case($data92, CASE_LOWER);
			$strMovementCode=$data92[movement_code];
		}// end if
		$db_opm92->free_result();
		$db_opm92->close();
		return($strMovementCode);
	} 
	function f_get_layer_no($strLayerSalary,$strLevelNo){		
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		$db_dpis8 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$cmd="select layer_no from per_layer where layer_salary=".f_genField($strLayerSalary,"N")."  
				and level_no=".f_genField($strLevelNo,"S");
		$num=$db_dpis8->send_cmd($cmd);
		if ($num != 0) {
			$data92 = $db_dpis8->get_array();
			$data92 = array_change_key_case($data92, CASE_LOWER);
			$strLayerNo=$data92[layer_no];
		}// end if
		$db_dpis8->free_result();
		$db_dpis8->close();
		return($strLayerNo);
	} 
	function f_get_personID($strPerIDDpis){
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm9 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd="select id from psst_person where per_id_dpis=".f_genField($strPerIDDpis,"N");
		$num=$db_opm9->send_cmd($cmd);
		if ($num != 0) {
			$data9 = $db_opm9->get_array();
			$data9 = array_change_key_case($data9, CASE_LOWER);
			$strID=$data9[id];
		} // end if
		$db_opm9->free_result();
		$db_opm9->close();
		return($strID);
	} 
	function f_change_pos_status($strPosStatus){
		//echo "pos=$strPosStatus<br>";
		if ($strPosStatus == 1)
			$strPosStatusTmp=2;
		elseif ($strPosStatus == 2)
			$strPosStatusTmp=4;
		else
			$strPosStatusTmp=$strPosStatus;
		 // end if
		// echo "pos=$strPosStatusTmp<br>";
		return($strPosStatusTmp);
	} 
	function f_get_prefix_code($strPnCode){		
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm92 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd="select min(prefix_code) prefix_code from ctlt_prefix_code where trim(pn_code) = ".f_genField(trim($strPnCode),"S");
		$num=$db_opm92->send_cmd($cmd);
		if ($num != 0) {
			$data92 = $db_opm92->get_array();
			$data92 = array_change_key_case($data92, CASE_LOWER);
			$strPrefixCodeTmp=$data92[prefix_code];
		}// end if
		$db_opm92->free_result();
		$db_opm92->close();
		return($strPrefixCodeTmp);
	} 
	function f_get_marital($strMrCode){		
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm92 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd="select min(marital) marital from psst_per_married where trim(mr_code) = ".f_genField(trim($strMrCode),"S");
		$num=$db_opm92->send_cmd($cmd);
		if ($num != 0) {
			$data92 = $db_opm92->get_array();
			$data92 = array_change_key_case($data92, CASE_LOWER);
			$strMaritalTmp=$data92[marital];
		}// end if
		$db_opm92->free_result();
		$db_opm92->close();
		return($strMaritalTmp);
	} 
	function f_get_blood($strPerBlood){		
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm92 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd="select min(blood) blood from psst_per_blood where trim(bl_code) = ".f_genField(trim($strPerBlood),"S");
		$num=$db_opm92->send_cmd($cmd);
		if ($num != 0) {
			$data92 = $db_opm92->get_array();
			$data92 = array_change_key_case($data92, CASE_LOWER);
			$strBloodTmp=$data92[blood];
		}// end if
		$db_opm92->free_result();
		$db_opm92->close();
		return($strBloodTmp);
	} 
	function f_get_religion_code($strReCode){		
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm92 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd="select min(religion_code) religion_code from ctlt_religion_code where trim(re_code) = ".f_genField(trim($strReCode),"S");
		$num=$db_opm92->send_cmd($cmd);
		if ($num != 0) {
			$data92 = $db_opm92->get_array();
			$data92 = array_change_key_case($data92, CASE_LOWER);
			$strReligionCodeTmp=$data92[religion_code];
		}// end if
		$db_opm92->free_result();
		$db_opm92->close();
		return($strReligionCodeTmp);
	} 
	function f_get_loc_serial($strPvCode){		
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm92 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd="select min(loc_serial) loc_serial from ctlt_location where trim(pv_code) = ".f_genField(trim($strPvCode),"S");
		$cmd.=" and trim(ap_code) ='0000' ";
		$num=$db_opm92->send_cmd($cmd);
		if ($num != 0) {
			$data92 = $db_opm92->get_array();
			$data92 = array_change_key_case($data92, CASE_LOWER);
			$strLocSerialTmp=$data92[loc_serial];
		}// end if
		$db_opm92->free_result();
		$db_opm92->close();
		return($strLocSerialTmp);
	}
	function f_get_per_serial(){		
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm92 = new connect_db_opm($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd="select max(serial_no) + 1 serial_no from ctlt_per_serial";
		$num=$db_opm92->send_cmd($cmd);
		if ($num != 0) {
			$data92 = $db_opm92->get_array();
			$data92 = array_change_key_case($data92, CASE_LOWER);
			$strSerialNoTmp=$data92[serial_no];
			$cmd="update ctlt_per_serial set serial_no = ".f_genField($data92[serial_no],"N");
			$db_opm92->send_cmd($cmd);
		}// end if
		$db_opm92->free_result();
		$db_opm92->close();
		return($strSerialNoTmp);
	}
	///////////// MY EDIT /////////////
	require_once('lib/nusoap.php');

	define("WS_LOCATION", "http://172.17.0.42:8080/OpenLdapWebservice/services/OpenLdapService?wsdl");
	
	function authenticate($username, $password) {
		$client = new soapclient(WS_LOCATION,true);
		$input['username'] = $username;
		$input['password'] = $password;
		$output = $client->call('authenticate',$input);
		return ($output['return']=="0000");
	}
	
	function addUser($username, $password, $name, $surname) {
		$client = new soapclient(WS_LOCATION,true);
		$input['uid'] = $username;
		$input['name'] = $name;
		$input['sn'] = $surname;
		$input['mail'] = "ldap@php.com";
		$input['password'] = $password;
		$input['id'] = "";
		$input['idCard'] = "1234567890123";
		$input['address'] = "";
		$input['startdate'] = "";
		$input['enddate'] = "";
//		echo "before call addUser <br>";
		$output = $client->call('addUser',$input);
//		echo "after call addUser <br>";
		return ($output['return']=="0000");
	}
	
	function deleteUser($username) {
		$client = new soapclient(WS_LOCATION,true);
		$input['uid'] = $username;
		$output = $client->call('deleteUser',$input);
		return ($output['return']=="0000");
	}
	///////////// END EDIT /////////////
?>
