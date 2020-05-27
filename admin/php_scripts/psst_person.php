<?
	//$ROOTPATH = $_SERVER['DOCUMENT_ROOT'];
	//include("session_start.php");
	//include("function_share.php");
	//include("load_per_control.php");
	include("function_share_cdgs.php");
	//include("php_scripts/function_share_cdgs.php");
	
	
	function f_ins_psst_person($strPersonID){
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		//global $aut_host,$aut_dsn,$aut_user,$aut_pwd;
		$db_opm99 = new connect_dpis($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$db_opm91 = new connect_dpis($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
//		$db_opm89 = new connect_dpis($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		//echo "dpisdb_host=$dpisdb_host,dpisdb_name=$dpisdb_name,dpisdb_user=$dpisdb_user,dpisdb_pwd=$dpisdb_pwd";
		$db_dpis98 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd , null);
		//global $aut_host,$aut_dsn,$aut_user,$aut_pwd;
		

		//$db_aut= new connect_db_aut ($aut_host, $aut_dsn,$aut_user,$aut_pwd);
		//$db_aut2= new connect_db_aut ($aut_host, $aut_dsn,$aut_user,$aut_pwd);
		$cmd="select * from per_personal where per_id=".f_genField($strPersonID,"N");
		$num=$db_dpis98->send_cmd($cmd);
		//echo $cmd."num=".$num;
		if ($num==1){
			$data98 = $db_dpis98->get_array();
			$data98 = array_change_key_case($data98, CASE_LOWER);
			$strPerType=f_get_per_type($data98[ot_code]);	
			//echo "per_type=$strPerType<br>";
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
			$strPerCardNo=$data98[per_cardno];
			$cmd="select * from psst_position where pos_id_dpis=".f_genField($data98[pos_id],"N");
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
			
			$cmd="select * from psst_person where per_id_dpis=".f_genField($strPersonID,"N");//." or id_card=".f_genField($data98[per_cardno],"S");
			$num1=$db_opm99->send_cmd($cmd);
			if ($num1==0){
				//sal_lev,sal_code,salary,approve_date,start_date,
				$strPerSerialNo=f_get_per_serial();
				$strID=$strPerSerialNo;
				// check id from cpst if found set id is null
				$cmd="select * from psst_person where id_card=".f_genField($strPerCardNo,"S");
				$num2=$db_opm99->send_cmd($cmd);
				if ($num2 != 0){
					$strPerCardNo="";
				}
				$cmd="insert into psst_person (id, per_id_dpis, per_status, per_type,";
				$cmd.=" prefix_code, name, surname, name_eng, surname_eng, ";
				$cmd.=" per_org_serial, sal_lev_recv, salary_recv, sex, marital, id_card, tax_no, blood_group, ";
				$cmd.=" religion_code, born_date, retire_date, enter_date, start_date, esc_test_date, quit_date, ";
				$cmd.=" address, born_loc_serial, movement_code,  fund_status, ";
				$cmd.=" tel, tel_org, fax, mobile, email,org_type) values(";
				$cmd.=f_genField($strPerSerialNo,"N").",".f_genField($strPersonID,"N").",";
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
				$cmd.=",per_id_dpis=".f_genField($strPersonID,"N");
				$cmd.=",name=".f_genField($data98[per_name],"S").",surname=".f_genField($data98[per_surname],"S");
				$cmd.=",name_eng=".f_genField(strtolower($data98[per_eng_name]),"S").",surname_eng=".f_genField(strtolower($data98[per_eng_surname]),"S");
				$cmd.=",sal_lev_recv=".f_genField($strCurLev,"S");
				//$cmd.=",per_org_serial=".f_genField($strOrgSerial,"N").",sal_lev_recv=".f_genField($strCurLev,"S");
				$cmd.=",salary_recv=".f_genField($data98[per_salary],"N").",sex=".f_genField($data98[per_gender],"S");
				//$cmd.=",marital=".f_genField($strMarital,"S").",id_card=".f_genField($strPerCardNo,"S");
				$cmd.=",marital=".f_genField($strMarital,"S");
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
			$aaa=f_upd_person_saraban($strID,$strPerCardNo);
			// update person sql2k
			//
			
			global $mssql_sql2k_host;
			$db_opmIntra = new connect_db_mssql($mssql_sql2k_host, "opmintranet", "opm", "opm");
			$cmd="select * from tPerson where person_id=".f_genField($strID,"N");
			$num=$db_opmIntra->send_cmd($cmd);
			if ($num != 0){
			  $cmd="update tPerson set prefix_code=".f_genField($strPrefixCode,"S");
			  $cmd.=" ,name=".f_genField($data98[per_name],"S")." ,surname=".f_genField($data98[per_surname],"S");
			  $cmd.=" where person_id=".f_genField($strID,"N");
			  $db_opmIntra->send_cmd($cmd);
			}
			$db_opmIntra->close();
			//$aa=ddd;
			//$aa=f_mgt_psst_person($strID);
			//f_person($strID);
		}//end if num=0
		/**/
		$db_opm99->free_result();
		$db_opm99->close();
		//$db_opm89->free_result();
		//$db_opm89->close();
		$db_opm91->free_result();
		$db_opm91->close();
		$db_dpis98->free_result();
		$db_dpis98->close();
	}
	
	function f_del_psst_person($strPersonID){
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		// echo $opm_db_host; die;
		$db_opm99 = new connect_dpis($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd="update psst_person set per_status='2',per_id_dpis=null where per_id_dpis=".f_genField($strPersonID,"N");
		$db_opm99->send_cmd($cmd);
	}
	function f_mgt_psst_person($strPersonID){
		// date à¸•à¹ÿà¸­à¸ÿà¹€à¸ÿà¹ÿà¸ÿ  à¸ÿ.à¸¨.
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm90= new connect_dpis($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd="select a.id,aut_id,aut_org_serial,d.aut_prefix_code,convert(a.name,'utf8','th8tisascii') name,convert(a.surname,'utf8','th8tisascii') surname,";
		$cmd.=" convert(line_name,'utf8','th8tisascii') line_name, ";
		$cmd.=" id_card,email,decode(sex,'1','M','F') sex ,convert(address,'utf8','th8tisascii') address,tel,a.user_id user_id ,";
		$cmd.= " to_char(a.last_date,'dd/mm/')||to_char(a.last_date,'yyyy') last_date ";
		$cmd.=" from psst_person a, psst_position b, ctlt_organize c,ctlt_prefix_code d ,psst_line_code e ";
		$cmd.=" where a.id=b.id and b.org_serial=c.org_serial and a.prefix_code=d.prefix_code ";
		$cmd.=" and b.line_code=e.line_code ";
		$cmd.=" and a.id=".f_genField($strPersonID,"N");
		
		$num=$db_opm90->send_cmd($cmd);
		
		//echo "$cmd";
		if ($num==1) {
			$data = $db_opm90->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$conn = new COM("ADODB.Connection") or die("Cannot start ADO");
			global $autMySQL_Connect;
			$conn->Open($autMySQL_Connect);
			$rsMySQL= new COM("ADODB.Recordset");
			$cmd="select * from aut_person where person_id=".f_genField($strPersonID,"N");
			//echo "cmd=$cmd";
			$rsMySQL->Open($cmd,$conn,1,3);
			if ($rsMySQL->eof){
				//echo "not found";
				$cmd="select seq from arc_pkctrl where table_name='aut_person' ";
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
				$db_opm91= new connect_dpis($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
				$cmd="update psst_person set aut_id=".f_genField($strSeq,"N");
				$cmd.=" where id=".f_genField($strPersonID,"N");
				$num=$db_opm91->send_cmd($cmd);
				$db_opm91->free_result();
				$db_opm91->close();
				//end
				$rsMySQL->addNew();
				$rsMySQL->fields["person_seq"]->value=$strSeq;
				$rsMySQL->fields["person_id"]->value=$data["id"];
				$rsMySQL->fields["site_seq"]->value="0";
			}
			$UPDATE_DATE = date("Y-m-d H:i:s");
			if ($data["last_date"]=="") {
				$strLastDate=$UPDATE_DATE;				
			}else{
				$strLastDate=$data["last_date"];
			}
			$rsMySQL->fields["org_seq"]->value=$data["aut_org_serial"];
			$rsMySQL->fields["title_code"]->value=$data["aut_prefix_code"];
			$rsMySQL->fields["name"]->value=$data["name"];
			$rsMySQL->fields["surname"]->value=$data["surname"];
			$rsMySQL->fields["position_name"]->value=$data["line_name"];
			//$rsMySQL->fields["idcard"]->value=$data["id_card"];
			$rsMySQL->fields["idcard"]->value=$strPerCardNo;
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
			$cmd2="insert into dpis_log(log_name) values(".f_genField($cmd,"S").")";
			$db_opm90->send_cmd($cmd2);
			$db_opm90->free_result();
			$db_opm90->close();
			//f_person($strID);
			//$db_aut= new connect_db_aut ($aut_host, $aut_dsn,$aut_user,$aut_pwd);
	}
	
	function f_upd_person_saraban($strPersonID,$strPerCardNo){
		// date à¸•à¹ÿà¸­à¸ÿà¹€à¸ÿà¹ÿà¸ÿ  à¸ÿ.à¸¨.
		global $opm_db_host,$opm_db_sid,$opm_db_user,$opm_db_pwd, $opm_db_port;
		$db_opm90= new connect_dpis($opm_db_host, $opm_db_sid, $opm_db_user, $opm_db_pwd, $opm_db_port);
		$cmd="select a.id,aut_id,aut_org_serial,d.aut_prefix_code,convert(a.name,'utf8','th8tisascii') name,convert(a.surname,'utf8','th8tisascii') surname,";
		$cmd.=" convert(line_name,'utf8','th8tisascii') line_name, ";
		$cmd.=" id_card,email,decode(sex,'1','M','F') sex ,convert(address,'utf8','th8tisascii') address,tel,a.user_id user_id ,";
		$cmd.= " to_char(a.last_date,'dd/mm/')||to_char(a.last_date,'yyyy') last_date ";
		$cmd.=" from psst_person a, psst_position b, ctlt_organize c,ctlt_prefix_code d ,psst_line_code e ";
		$cmd.=" where a.id=b.id and b.org_serial=c.org_serial and a.prefix_code=d.prefix_code ";
		$cmd.=" and b.line_code=e.line_code ";
		$cmd.=" and a.id=".f_genField($strPersonID,"N");
		
		$num=$db_opm90->send_cmd($cmd);
		//echo "num=$cmd";
		if ($num==1) {
			$data = $db_opm90->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$conn = new COM("ADODB.Connection") or die("Cannot start ADO");
			global $autMySQL_Connect;
			$conn->Open($autMySQL_Connect);
			$rsMySQL= new COM("ADODB.Recordset");
			$cmd="select * from aut_person where person_id=".f_genField($strPersonID,"N");
			//echo "cmd=$cmd";
			$rsMySQL->Open($cmd,$conn,1,3);
			if (!$rsMySQL->eof){
				$UPDATE_DATE = date("Y-m-d H:i:s");
			if ($data["last_date"]=="") {
				$strLastDate=$UPDATE_DATE;				
			}else{
				$strLastDate=$data["last_date"];
			}
			
			$rsMySQL->fields["title_code"]->value=$data["aut_prefix_code"];
			$rsMySQL->fields["name"]->value=$data["name"];
			$rsMySQL->fields["surname"]->value=$data["surname"];			
			//$rsMySQL->fields["idcard"]->value=$data["id_card"];
			//$rsMySQL->fields["idcard"]->value=$strPerCardNo;
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
			
			}
			$db_opm90->free_result();
			$db_opm90->close();
			//f_person($strID);
			//$db_aut= new connect_db_aut ($aut_host, $aut_dsn,$aut_user,$aut_pwd);
	}
	
	
		
//============================================================================
		
	//$strUserCode=f_new_person(276,"2008/08/29","10140");
	//$strUserCode=f_get_sast_per_aud_seq (76,24);
	//$strUserCode=f_set_exp_emp(276,"2008/08/29","1840");
	//$strUserCode=f_Delete_Queue(25067);
	//$strUserCode=$strUserCode=f_Delete_Queue(f_get_personID(401));
	/*include("$ROOTPATH/php_scripts/connect_oracle.php");
class connect_dpis extends connect_oracle { };
$opm_db_host="172.17.0.3";
$opm_db_sid="spmdb";
$opm_db_user="opm";
$opm_db_pwd="opm2003";
$opm_db_port="1521";

include($ROOTPATH."/php_scripts/connect_oci8.php");
class connect_dpis extends connect_oci8 { };
$dpisdb_host="172.17.0.3";
$dpisdb_name="spmdb";
$dpisdb_user="dpis";
$dpisdb_pwd="dpis";
$db_type="oci8";
include($ROOTPATH."/php_scripts/connect_odbc.php");
class connect_db_aut extends connect_odbc { };
$aut_host = "172.17.0.17";
$aut_dsn="archive";
$aut_user = "archive";
$aut_pwd = "archive";
$autMySQL_Connect = "Driver={MySQL ODBC 5.3 Unicode Driver}; Server=172.17.0.17;UID=archive;pwd=archive;database=archivedb;option=3306;CharSet=utf8"; */
	//$aa=f_ins_psst_person("30");
	//$aa=f_mgt_psst_person("10");
	//$aa=f_del_psst_person("222");
	//$strPersonID="10";
	
	
	
	
?>