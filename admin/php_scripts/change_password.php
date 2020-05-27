<?
include("../php_scripts/connect_database.php");
include("php_scripts/load_per_control.php");
include("php_scripts/function_share.php");
if($CHANGE_PASSWORD == 'N'){
	 echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">
	 		<tr><td align="center"><font color="red"><h1>ไม่มีสิทธิ์ในหน้างานนี้</h1></font></td></tr>
    	  </table>';
	die();	
}

$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

 /*Show Release Now*/
    $cmd = " select msg_header 
        from PER_MESSAGE 
        WHERE msg_type=0 
          AND msg_id IN(select max(msg_id) from PER_MESSAGE where msg_type=0 AND msg_header LIKE '%Patch Update DPIS Release%' ) ";
    
        $db_dpis->send_cmd($cmd);
        $data = $db_dpis->get_array_array();
        $DPIS_Release = trim(substr($data[0], 18,30)) ;
        session_register("DPIS_Release");
    /**/


if($command1 == "check_data"){
		$PER_BIRTH = save_date($birth_date);
		$PER_OFFICIAL = save_date($move_official);
		
		$sql="select pn.PER_ID, pn.PER_BIRTHDATE, pn.PER_NAME, pn.PER_SURNAME,pn.PER_CARDNO
			  from  PER_PERSONAL pn
			  where pn.PER_CARDNO = trim('$card_no')
			  and pn.PER_BIRTHDATE ='$PER_BIRTH'
			  and pn.PER_STARTDATE='$PER_OFFICIAL'
			  and(pn.PER_STATUS in(1,0))";
			 //echo "<pre>".$sql; 	
			$db_dpis1->send_cmd($sql);
			$data1 = $db_dpis1->get_array();
			$first_name = $data1[PER_NAME];
			$last_name  = $data1[PER_SURNAME];
			$per_cardno = $data1[PER_CARDNO];
			$per_id		= $data1[PER_ID];
			$per_birthdate = $data1[PER_BIRTHDATE];
			$full_name = "คุณ".$first_name." ".$last_name;
			//เช็คไม่ให้เปลี่ยนรหัสผ่านเป็น ว/ด/ป เกิด
			$bth_ex  = explode("-",$per_birthdate);
			$year_en = trim($bth_ex[0]); //ปี ค.ศ.
			$year_th = trim($bth_ex[0])+543; //ปี พ.ศ.
			$month   = trim($bth_ex[1]);
			$day     = trim($bth_ex[2]);
			$full_day_th = $day.$month.$year_th;
			$full_day_en = $day.$month.$year_en;

			if($per_cardno==""){
				$send_show  = 0;//ไม่พบข้อมูลข้าราชการ
			}else{
				$sql="select distinct(USER_FLAG), USERNAME, USER_LINK_ID
					FROM USER_DETAIL
					where USERNAME='$per_cardno'
					AND USER_LINK_ID =$per_id";	
				$cnt=$db_dpis->send_cmd($sql);
				if($cnt>0){
					while($data = $db_dpis->get_array()){
							$username  	  = $data[USERNAME];
							$user_link_id = $data[USER_LINK_ID];	
							$user_flage   = $data[USER_FLAG];	
						if($user_flage=='Y'){
							$send_show = 1;//สถานะปรกติ อนุญาติให้เปลี่ยนรหัสได้
						}else{
							$send_show = 3;//บัญชีถูกยกเลิกเเล้ว
						}	
					}//end while 	
				}else{
					$send_show = 2;//ไม่พบบัญชีเข้าใช้งาน
					$full_name ;		
				}
					
			}
}//end check_data
if($command1 == "change_pass"){
		$password = md5($confirm_new_password);
		$sql="UPDATE user_detail SET  password = '$password' 
		where username='$card_no2'
		and USER_FLAG = 'Y'	";
		$db_dpis1->send_cmd($sql);
		echo '<script> alert("เปลี่ยนรหัสผ่านเรียบร้อยเเล้ว ระบบจะกลับไปที่หน้า Login");window.location.href = "index.html";</script>';	
}
?>
