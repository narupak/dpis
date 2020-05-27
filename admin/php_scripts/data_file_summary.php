<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$SAV_SUM_DATE = substr($SUM_DATE, -4) - 543 . "-" . substr($SUM_DATE, 3, 2) ."-". substr($SUM_DATE, 0, 2);
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	function trace_org_sumdtl1($ARR_ORG_REF, $OL_CODE){
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $arr_result;
		
		$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
//		echo "$OL_CODE :: ($ORG_ID)<br>";
		$cmd = " select 	ORG_ID, OL_CODE 
				   from 		PER_ORG 
				   where 	ORG_ID_REF in (". implode(",", $ARR_ORG_REF) .") and trim(OL_CODE)='".str_pad(($OL_CODE + 1), 2, "0", STR_PAD_LEFT)."' 
				   order by 	ORG_SEQ_NO, ORG_ID ";
		$count_child = $db_dpis1->send_cmd($cmd);
		if($count_child){
			$cmd = " select 	OS_CODE, OT_CODE, count(ORG_ID) as SUM_QTY
					   from 		PER_ORG 
					   where 	ORG_ACTIVE=1 and ORG_ID_REF in (". implode(",", $ARR_ORG_REF) .") 
					   			and trim(OL_CODE)='".str_pad(($OL_CODE + 1), 2, "0", STR_PAD_LEFT)."' 
					   group by 	OS_CODE, OT_CODE ";
			$db_dpis2->send_cmd($cmd);
//			echo "$cmd<br><br>";
			while ( $data2 = $db_dpis2->get_array() ) {
				$OS_CODE = $data2[OS_CODE];
				$OT_CODE = $data2[OT_CODE];
				$SUM_QTY = $data2[SUM_QTY] + 0;
				if(trim($OS_CODE) && trim($OT_CODE)) $arr_result["$OS_CODE:$OT_CODE"] += $SUM_QTY;
			}   // end while

			unset($ARR_ORG_REF);
			while($data1 = $db_dpis1->get_array()) $ARR_ORG_REF[] = $data1[ORG_ID];
			trace_org_sumdtl1($ARR_ORG_REF, str_pad(($OL_CODE + 1), 2, "0", STR_PAD_LEFT));
		} // end if
		return;
	} // function

	function trace_org_sumdtl6($ORG_ID, $OL_CODE){
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $arr_result;
		
		$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
//		echo "$OL_CODE :: ($ORG_ID)<br>";
		$cmd = " select 	ORG_ID, OL_CODE 
				   from 		PER_ORG 
				   where 	ORG_ID_REF=$ORG_ID and trim(OL_CODE)='".str_pad(($OL_CODE + 1), 2, "0", STR_PAD_LEFT)."' 
				   order by 	ORG_SEQ_NO, ORG_ID ";
		$count_child = $db_dpis1->send_cmd($cmd);
		if($count_child){
			while($data1 = $db_dpis1->get_array()) trace_org_sumdtl6($data1[ORG_ID], trim($data1[OL_CODE]));
			
			if($OL_CODE >= "03"){
				if($OL_CODE=="03"){ 	//สำนักกอง
					if($SESS_ORG_STRUCTURE==1){
						$arr_search_condition[] = "(a.ORG_ID = c.ORG_ID)";
						$arr_search_condition[] = "(a.ORG_ID = $ORG_ID and a.ORG_ID_1 is null and a.ORG_ID_2 is null)";
					}else{
						$arr_search_condition[] = "(b.ORG_ID = c.ORG_ID)";
						$arr_search_condition[] = "(b.ORG_ID = $ORG_ID and b.ORG_ID_1 is null and b.ORG_ID_2 is null)";
					}
				}elseif($OL_CODE=="04"){	//ต่ำกว่าสำนักกอง 1
					if($SESS_ORG_STRUCTURE==1){
						$arr_search_condition[] = "(a.ORG_ID_1 = c.ORG_ID)";
						$arr_search_condition[] = "(a.ORG_ID_1 = $ORG_ID and a.ORG_ID_2 is null)";
					}else{
						$arr_search_condition[] = "(b.ORG_ID_1 = c.ORG_ID)";
						$arr_search_condition[] = "(b.ORG_ID_1 = $ORG_ID and b.ORG_ID_2 is null)";
					}
				}elseif($OL_CODE=="05"){	//ต่ำกว่าสำนักกอง 2
					if($SESS_ORG_STRUCTURE==1){
						$arr_search_condition[] = "(a.ORG_ID_2 = c.ORG_ID)";
						$arr_search_condition[] = "(a.ORG_ID_2 = $ORG_ID)";
					}else{
						$arr_search_condition[] = "(b.ORG_ID_2 = c.ORG_ID)";
						$arr_search_condition[] = "(b.ORG_ID_2 = $ORG_ID)";
					}
				}
				$search_condition = "";
				if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);
	
				$cmd = " select 	c.OT_CODE, count(a.PER_ID) as SUM_QTY 
						   from 		PER_PERSONAL a, PER_POSITION b, PER_ORG c
						   where 	a.POS_ID=b.POS_ID and a.PER_STATUS=1
									$search_condition
						   group by 	c.OT_CODE ";
				if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
				$db_dpis1->send_cmd($cmd);
//				echo "$cmd<br>";
//				$db_dpis1->show_error();	
				while ( $data1 = $db_dpis1->get_array() ) {
					$OT_CODE = $data1[OT_CODE];
					$SUM_QTY = $data1[SUM_QTY] + 0;
					if(trim($OT_CODE)) $arr_result[$OT_CODE] += $SUM_QTY;
				}   // end while
			} // end if
		}else{
			if($OL_CODE=="03"){ 
				if($SESS_ORG_STRUCTURE==1){
					$arr_search_condition[] = "(a.ORG_ID = c.ORG_ID)";
					$arr_search_condition[] = "(a.ORG_ID = $ORG_ID)";
				}else{
					$arr_search_condition[] = "(b.ORG_ID = c.ORG_ID)";
					$arr_search_condition[] = "(b.ORG_ID = $ORG_ID)";
				}
			}elseif($OL_CODE=="04"){
				if($SESS_ORG_STRUCTURE==1){
					$arr_search_condition[] = "(a.ORG_ID_1 = c.ORG_ID)";
					$arr_search_condition[] = "(a.ORG_ID_1 = $ORG_ID)";
				}else{
					$arr_search_condition[] = "(b.ORG_ID_1 = c.ORG_ID)";
					$arr_search_condition[] = "(b.ORG_ID_1 = $ORG_ID)";
				}
			}elseif($OL_CODE=="05"){
				if($SESS_ORG_STRUCTURE==1){
					$arr_search_condition[] = "(a.ORG_ID_2 = c.ORG_ID)";
					$arr_search_condition[] = "(a.ORG_ID_2 = $ORG_ID)";
				}else{
					$arr_search_condition[] = "(b.ORG_ID_2 = c.ORG_ID)";
					$arr_search_condition[] = "(b.ORG_ID_2 = $ORG_ID)";
				}
			}
			$search_condition = "";
			if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

			$cmd = " select 	c.OT_CODE, count(a.PER_ID) as SUM_QTY 
					   from 		PER_PERSONAL a, PER_POSITION b, PER_ORG c
					   where 	a.POS_ID=b.POS_ID and a.PER_STATUS=1
					   			$search_condition
					   group by 	c.OT_CODE ";
			if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
			$db_dpis1->send_cmd($cmd);
//			echo "$cmd<br>";
//			$db_dpis1->show_error();	
			while ( $data1 = $db_dpis1->get_array() ) {
				$OT_CODE = $data1[OT_CODE];
				$SUM_QTY = $data1[SUM_QTY] + 0;
				if(trim($OT_CODE)) $arr_result[$OT_CODE] += $SUM_QTY;
			}   // end while
		} // end if				
		return;
	} // function

	if( $command == "PROCESS" ){  
		$cmd = " select SUM_ID from PER_SUM where SUM_YEAR = '$SUM_YEAR' ";
		$db_dpis->send_cmd($cmd);

		while ( $data = $db_dpis->get_array() ) {
			$SUM_ID = $data[SUM_ID] + 0;

			$cmd = " delete from PER_SUM_DTL1 where SUM_ID = $SUM_ID ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();

			$cmd = " delete from PER_SUM_DTL2 where SUM_ID = $SUM_ID ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();

			$cmd = " delete from PER_SUM_DTL3 where SUM_ID = $SUM_ID ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();

			$cmd = " delete from PER_SUM_DTL4 where SUM_ID = $SUM_ID ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();

			$cmd = " delete from PER_SUM_DTL5 where SUM_ID = $SUM_ID ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();

			$cmd = " delete from PER_SUM_DTL6 where SUM_ID = $SUM_ID ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();

			$cmd = " delete from PER_SUM_DTL7 where SUM_ID = $SUM_ID ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();

			$cmd = " delete from PER_SUM_DTL8 where SUM_ID = $SUM_ID ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();

			$cmd = " delete from PER_SUM_DTL9 where SUM_ID = $SUM_ID ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();

			$cmd = " delete from PER_SUM_DTL10 where SUM_ID = $SUM_ID ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();

			$cmd = " delete from PER_SUM where SUM_ID = $SUM_ID ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
		} 

		// ================ BEGIN PROCESS ================
		
		$cmd = " select 	ORG_ID, OL_CODE 
				   from 		PER_ORG 
				   where 	(ORG_ID = $DEPARTMENT_ID or ORG_ID_REF = $DEPARTMENT_ID) and ORG_ACTIVE = 1 
				   order by 	OL_CODE, ORG_SEQ_NO ";
		if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}		   
		$db_dpis->send_cmd($cmd);

		while ( $data = $db_dpis->get_array() ) {
			$cmd = " select max(SUM_ID) as MAX_ID from PER_SUM ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$SUM_ID = $data2[MAX_ID] + 1;

			$ORG_ID = $data[ORG_ID];
			$OL_CODE = trim($data[OL_CODE]);
			
			$cmd = " insert into PER_SUM 
						(SUM_ID, ORG_ID, SUM_YEAR, SUM_DATE, UPDATE_USER, UPDATE_DATE) 
					   values 
					   	($SUM_ID, $ORG_ID, '$SUM_YEAR', '$SAV_SUM_DATE', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();

			// ================================= PER_SUM_DTL1 ================================ //
			unset($arr_result);
//			echo "<br>$OL_CODE :: $ORG_ID<br>";
			trace_org_sumdtl1(array($ORG_ID), $OL_CODE);
//			echo "<pre>"; print_r($arr_result); echo "</pre>";
			foreach($arr_result as $KEY => $SUM_QTY){
				$arr_temp = explode(":", $KEY);
				$OS_CODE = $arr_temp[0];
				$OT_CODE = $arr_temp[1];
				$cmd = " insert into PER_SUM_DTL1 
							(SUM_ID, OS_CODE, OT_CODE, SUM_QTY, UPDATE_USER, UPDATE_DATE) 
						   values 
						   	($SUM_ID, '$OS_CODE', '$OT_CODE', $SUM_QTY, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
			} // end foreach

			// ================================= PER_SUM_DTL2 ================================ //
			unset($arr_search_condition);
			if($OL_CODE=="02"){
				if($SESS_ORG_STRUCTURE==1){
					unset($arr_org);
					$arr_org[] = $ORG_ID;	//ถ้าเค้ากำหนดเป็นกรมตรงๆเลย
					$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$ORG_ID and OL_CODE='03' ";
					$db_dpis1->send_cmd($cmd);
					while($data1 = $db_dpis1->get_array()) $arr_org[] = $data1[ORG_ID];		
					$arr_search_condition[] = "(b.ORG_ID in (". implode(",", $arr_org) ."))";
				}else{	
					$arr_search_condition[] = "(a.DEPARTMENT_ID=$ORG_ID)";
				}
			}elseif($OL_CODE=="03"){
				if($SESS_ORG_STRUCTURE==1){
					//กระทรวง ต้องหาสำนักกองภายใต้กรมลงไปอีก
					unset($arr_org);
					$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$ORG_ID and OL_CODE='02' ";		//หากรม ไม่มีในตาราง PER_ORG_ASS
					$db_dpis1->send_cmd($cmd);
					while($data1 = $db_dpis1->get_array()){
						$arr_org[] = $data1[ORG_ID];		 //$data[ORG_ID] = DEPARTMENT_ID
						//==หาสำนักกองภายใต้กรม ลงไปอีก 
						$cmd2 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data1[ORG_ID] and OL_CODE='03' ";
						$db_dpis2->send_cmd($cmd2);
						while($data2 = $db_dpis2->get_array()){
							$arr_org[] = $data2[ORG_ID];		 //$data2[ORG_ID] = ORG_ID	
							//==หาต่ำกว่าสำนักกอง 1 ภายใต้สำนักกอง ลงไปอีก
							$cmd3 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data2[ORG_ID] and OL_CODE='04' ";
							$db_dpis3->send_cmd($cmd3);
							while($data3 = $db_dpis3->get_array()){
								$arr_org[] = $data3[ORG_ID];		 //$data3[ORG_ID] = ORG_ID_1
								//==หาต่ำกว่าสำนักกอง 2 ภายใต้สำนักกอง ลงไปอีก
								$cmd4 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data3[ORG_ID] and OL_CODE='05' ";
								$db_dpis4->send_cmd($cmd4);
								while($data4 = $db_dpis4->get_array()){
									$arr_org[] = $data4[ORG_ID];		 //$data4[ORG_ID] = ORG_ID_2
								}
							}
						} //end while
					}		
					$arr_search_condition[] = "(b.ORG_ID in (". implode(",", $arr_org) ."))";	
				}else{
				 	$arr_search_condition[] = "(a.ORG_ID=$ORG_ID)";
				}
			}
			$search_condition = "";
			if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

			// ตำแหน่งว่าง
			if($DPISDB=="odbc")
				$cmd = " select 	a.PL_CODE, a.CL_NAME, a.PT_CODE, count(a.POS_ID) as SUM_QTY 
						   from 		PER_POSITION a
						   			left join PER_PERSONAL b on (a.POS_ID=b.POS_ID)
						   where 	(b.PER_STATUS is null or b.PER_STATUS in (0, 2)) and a.POS_STATUS = 1
						   			$search_condition
						   group by 	a.PL_CODE, a.CL_NAME, a.PT_CODE ";
			elseif($DPISDB=="oci8")
				$cmd = " select		a.PL_CODE, a.CL_NAME, a.PT_CODE, count(a.POS_ID) as SUM_QTY 
						   from		PER_POSITION a, PER_PERSONAL b 
						   where	a.POS_ID=b.POS_ID(+) and (b.PER_STATUS is null or b.PER_STATUS in (0, 2)) and a.POS_STATUS = 1
						   			$search_condition
						   group by 	a.PL_CODE, a.CL_NAME, a.PT_CODE ";
			elseif($DPISDB=="mysql")
				$cmd = " select 	a.PL_CODE, a.CL_NAME, a.PT_CODE, count(a.POS_ID) as SUM_QTY 
						   from 		PER_POSITION a
						   			left join PER_PERSONAL b on (a.POS_ID=b.POS_ID)
						   where 	(b.PER_STATUS is null or b.PER_STATUS in (0, 2)) and a.POS_STATUS = 1
						   			$search_condition
						   group by 	a.PL_CODE, a.CL_NAME, a.PT_CODE ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
			while ( $data1 = $db_dpis1->get_array() ) {
				$PL_CODE = $data1[PL_CODE];
				$CL_NAME = $data1[CL_NAME];
				$PT_CODE = $data1[PT_CODE];
				$SUM_QTY = $data1[SUM_QTY] + 0;
				$cmd = " insert into PER_SUM_DTL2 
							(SUM_ID, SUM_TYPE, PL_CODE, CL_NAME, PT_CODE, SUM_QTY, UPDATE_USER, UPDATE_DATE) 
						   values 
							($SUM_ID, 0, '$PL_CODE', '$CL_NAME', '$PT_CODE', $SUM_QTY, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
			}  

			// ตำแหน่งมีคนครอง
			if($DPISDB=="odbc")
				$cmd = " select 	a.PL_CODE, a.CL_NAME, a.PT_CODE, count(a.POS_ID) as SUM_QTY 
						   from 		PER_POSITION a, PER_PERSONAL b
						   where 	a.POS_ID=b.POS_ID and b.PER_STATUS=1 and a.POS_STATUS = 1
						   			$search_condition
						   group by 	a.PL_CODE, a.CL_NAME, a.PT_CODE ";
			elseif($DPISDB=="oci8")
				$cmd = " select		a.PL_CODE, a.CL_NAME, a.PT_CODE, count(a.POS_ID) as SUM_QTY 
						   from		PER_POSITION a, PER_PERSONAL b 
						   where	a.POS_ID=b.POS_ID and b.PER_STATUS=1 and a.POS_STATUS = 1
						   			$search_condition
						   group by 	a.PL_CODE, a.CL_NAME, a.PT_CODE ";
			elseif($DPISDB=="mysql")
				$cmd = " select 	a.PL_CODE, a.CL_NAME, a.PT_CODE, count(a.POS_ID) as SUM_QTY 
						   from 		PER_POSITION a, PER_PERSONAL b
						   where 	a.POS_ID=b.POS_ID and b.PER_STATUS=1 and a.POS_STATUS = 1
						   			$search_condition
						   group by 	a.PL_CODE, a.CL_NAME, a.PT_CODE ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
			while ( $data1 = $db_dpis1->get_array() ) {
				$PL_CODE = $data1[PL_CODE];
				$CL_NAME = $data1[CL_NAME];
				$PT_CODE = $data1[PT_CODE];
				$SUM_QTY = $data1[SUM_QTY] + 0;
				$cmd = " insert into PER_SUM_DTL2 
							(SUM_ID, SUM_TYPE, PL_CODE, CL_NAME, PT_CODE, SUM_QTY, UPDATE_USER, UPDATE_DATE) 
						   values 
							($SUM_ID, 1, '$PL_CODE', '$CL_NAME', '$PT_CODE', $SUM_QTY, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
			}  

			// ================================= PER_SUM_DTL3 ================================ //
			unset($arr_search_condition);
			if($OL_CODE=="02"){
				if($SESS_ORG_STRUCTURE==1){
					unset($arr_org);
					$arr_org[] = $ORG_ID;	//ถ้าเค้ากำหนดเป็นกรมตรงๆเลย
					$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$ORG_ID and OL_CODE='03' ";
					$db_dpis1->send_cmd($cmd);
					while($data1 = $db_dpis1->get_array()) $arr_org[] = $data1[ORG_ID];		
					$arr_search_condition[] = "(b.ORG_ID in (". implode(",", $arr_org) ."))";
				}else{	
					$arr_search_condition[] = "(a.DEPARTMENT_ID=$ORG_ID)";
				}
			}elseif($OL_CODE=="03"){
				if($SESS_ORG_STRUCTURE==1){
					//กระทรวง ต้องหาสำนักกองภายใต้กรมลงไปอีก
					unset($arr_org);
					$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$ORG_ID and OL_CODE='02' ";		//หากรม ไม่มีในตาราง PER_ORG_ASS
					$db_dpis1->send_cmd($cmd);
					while($data1 = $db_dpis1->get_array()){
						$arr_org[] = $data1[ORG_ID];		 //$data[ORG_ID] = DEPARTMENT_ID
						//==หาสำนักกองภายใต้กรม ลงไปอีก 
						$cmd2 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data1[ORG_ID] and OL_CODE='03' ";
						$db_dpis2->send_cmd($cmd2);
						while($data2 = $db_dpis2->get_array()){
							$arr_org[] = $data2[ORG_ID];		 //$data2[ORG_ID] = ORG_ID	
							//==หาต่ำกว่าสำนักกอง 1 ภายใต้สำนักกอง ลงไปอีก
							$cmd3 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data2[ORG_ID] and OL_CODE='04' ";
							$db_dpis3->send_cmd($cmd3);
							while($data3 = $db_dpis3->get_array()){
								$arr_org[] = $data3[ORG_ID];		 //$data3[ORG_ID] = ORG_ID_1
								//==หาต่ำกว่าสำนักกอง 2 ภายใต้สำนักกอง ลงไปอีก
								$cmd4 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data3[ORG_ID] and OL_CODE='05' ";
								$db_dpis4->send_cmd($cmd4);
								while($data4 = $db_dpis4->get_array()){
									$arr_org[] = $data4[ORG_ID];		 //$data4[ORG_ID] = ORG_ID_2
								}
							}
						} //end while
					}		
					$arr_search_condition[] = "(b.ORG_ID in (". implode(",", $arr_org) ."))";	
				}else{
				 	$arr_search_condition[] = "(a.ORG_ID=$ORG_ID)";
				}
			}
			$search_condition = "";
			if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

			if($DPISDB=="odbc")
				$cmd = " select 	distinct a.PL_CODE 
						   from 		PER_POSITION a
						   			left join PER_PERSONAL b on (a.POS_ID=b.POS_ID)
						   where 	(b.PER_STATUS is null or b.PER_STATUS in (0, 2)) and a.POS_STATUS = 1
						   			$search_condition
						   order by	a.PL_CODE
						 ";
			elseif($DPISDB=="oci8")
				$cmd = " select		distinct a.PL_CODE
						   from		PER_POSITION a, PER_PERSONAL b 
						   where	a.POS_ID=b.POS_ID(+) and (b.PER_STATUS is null or b.PER_STATUS in (0, 2)) and a.POS_STATUS = 1
						   			$search_condition
						   order by	a.PL_CODE
						 ";
			elseif($DPISDB=="mysql")
				$cmd = " select 	distinct a.PL_CODE 
						   from 		PER_POSITION a
						   			left join PER_PERSONAL b on (a.POS_ID=b.POS_ID)
						   where 	(b.PER_STATUS is null or b.PER_STATUS in (0, 2)) and a.POS_STATUS = 1
						   			$search_condition
						   order by	a.PL_CODE
						 ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			while ( $data1 = $db_dpis1->get_array() ) {
				$PL_CODE = $data1[PL_CODE];

				if($DPISDB=="odbc")
					$cmd = " select 	count(a.POS_ID)  as SUM_WITH_MONEY
							   from 		PER_POSITION a
										left join PER_PERSONAL b on (a.POS_ID=b.POS_ID)
							   where 	(b.PER_STATUS is null or b.PER_STATUS in (0, 2)) and a.POS_STATUS = 1
										and a.PL_CODE='$PL_CODE' and a.POS_GET_DATE is not null
										$search_condition
							 ";
				elseif($DPISDB=="oci8")
					$cmd = " select		count(a.POS_ID)  as SUM_WITH_MONEY
							   from		PER_POSITION a, PER_PERSONAL b 
							   where	a.POS_ID=b.POS_ID(+) and (b.PER_STATUS is null or b.PER_STATUS in (0, 2)) and a.POS_STATUS = 1
										and a.PL_CODE='$PL_CODE' and a.POS_GET_DATE is not null
										$search_condition
							 ";
				elseif($DPISDB=="mysql")
					$cmd = " select 	count(a.POS_ID)  as SUM_WITH_MONEY
							   from 		PER_POSITION a
										left join PER_PERSONAL b on (a.POS_ID=b.POS_ID)
							   where 	(b.PER_STATUS is null or b.PER_STATUS in (0, 2)) and a.POS_STATUS = 1
										and a.PL_CODE='$PL_CODE' and a.POS_GET_DATE is not null
										$search_condition
							 ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SUM_WITH_MONEY = $data2[SUM_WITH_MONEY] + 0;

				if($DPISDB=="odbc")
					$cmd = " select 	count(a.POS_ID)  as SUM_NO_MONEY
							   from 		PER_POSITION a
										left join PER_PERSONAL b on (a.POS_ID=b.POS_ID)
							   where 	(b.PER_STATUS is null or b.PER_STATUS in (0, 2)) and a.POS_STATUS = 1
										and a.PL_CODE='$PL_CODE' and a.POS_GET_DATE is null
										$search_condition
							 ";
				elseif($DPISDB=="oci8")
					$cmd = " select		count(a.POS_ID)  as SUM_NO_MONEY
							   from		PER_POSITION a, PER_PERSONAL b 
							   where	a.POS_ID=b.POS_ID(+) and (b.PER_STATUS is null or b.PER_STATUS in (0, 2)) and a.POS_STATUS = 1
										and a.PL_CODE='$PL_CODE' and a.POS_GET_DATE is null
										$search_condition
							 ";
				elseif($DPISDB=="mysql")
					$cmd = " select 	count(a.POS_ID)  as SUM_NO_MONEY
							   from 		PER_POSITION a
										left join PER_PERSONAL b on (a.POS_ID=b.POS_ID)
							   where 	(b.PER_STATUS is null or b.PER_STATUS in (0, 2)) and a.POS_STATUS = 1
										and a.PL_CODE='$PL_CODE' and a.POS_GET_DATE is null
										$search_condition
							 ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SUM_NO_MONEY = $data2[SUM_NO_MONEY] + 0;

				$cmd = " insert into PER_SUM_DTL3 
							(SUM_ID, PL_CODE, SUM_WITH_MONEY, SUM_NO_MONEY, UPDATE_USER, UPDATE_DATE) 
						   values 
							($SUM_ID, '$PL_CODE', $SUM_WITH_MONEY, $SUM_NO_MONEY, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
			}  

			// ================================= PER_SUM_DTL4 ================================ //
			unset($arr_search_condition);
			if($OL_CODE=="02"){
				if($SESS_ORG_STRUCTURE==1){
					unset($arr_org);
					$arr_org[] = $ORG_ID;	//ถ้าเค้ากำหนดเป็นกรมตรงๆเลย
					$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$ORG_ID and OL_CODE='03' ";
					$db_dpis1->send_cmd($cmd);
					while($data1 = $db_dpis1->get_array()) $arr_org[] = $data1[ORG_ID];		
					$arr_search_condition[] = "(b.ORG_ID in (". implode(",", $arr_org) ."))";
				}else{	
					$arr_search_condition[] = "(a.DEPARTMENT_ID=$ORG_ID)";
				}
			}elseif($OL_CODE=="03"){
				if($SESS_ORG_STRUCTURE==1){
					//กระทรวง ต้องหาสำนักกองภายใต้กรมลงไปอีก
					unset($arr_org);
					$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$ORG_ID and OL_CODE='02' ";		//หากรม ไม่มีในตาราง PER_ORG_ASS
					$db_dpis1->send_cmd($cmd);
					while($data1 = $db_dpis1->get_array()){
						$arr_org[] = $data1[ORG_ID];		 //$data[ORG_ID] = DEPARTMENT_ID
						//==หาสำนักกองภายใต้กรม ลงไปอีก 
						$cmd2 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data1[ORG_ID] and OL_CODE='03' ";
						$db_dpis2->send_cmd($cmd2);
						while($data2 = $db_dpis2->get_array()){
							$arr_org[] = $data2[ORG_ID];		 //$data2[ORG_ID] = ORG_ID	
							//==หาต่ำกว่าสำนักกอง 1 ภายใต้สำนักกอง ลงไปอีก
							$cmd3 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data2[ORG_ID] and OL_CODE='04' ";
							$db_dpis3->send_cmd($cmd3);
							while($data3 = $db_dpis3->get_array()){
								$arr_org[] = $data3[ORG_ID];		 //$data3[ORG_ID] = ORG_ID_1
								//==หาต่ำกว่าสำนักกอง 2 ภายใต้สำนักกอง ลงไปอีก
								$cmd4 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data3[ORG_ID] and OL_CODE='05' ";
								$db_dpis4->send_cmd($cmd4);
								while($data4 = $db_dpis4->get_array()){
									$arr_org[] = $data4[ORG_ID];		 //$data4[ORG_ID] = ORG_ID_2
								}
							}
						} //end while
					}		
					$arr_search_condition[] = "(b.ORG_ID in (". implode(",", $arr_org) ."))";	
				}else{
				 	$arr_search_condition[] = "(a.ORG_ID=$ORG_ID)";
				}
			}
			$search_condition = "";
			if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

			$cmd = " select 	distinct c.PS_CODE, b.PM_CODE, a.LEVEL_NO 
					   from 		PER_PERSONAL a, PER_POSITION b, PER_MGT c 
					   where 	a.POS_ID = b.POS_ID and b.PM_CODE = c.PM_CODE and a.PER_STATUS = 1
					   			$search_condition
					   order by 	c.PS_CODE, b.PM_CODE, a.LEVEL_NO ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			while ( $data1 = $db_dpis1->get_array() ) {
				$PS_CODE = $data1[PS_CODE];
				$PM_CODE = $data1[PM_CODE];
				$LEVEL_NO = $data1[LEVEL_NO];

				$cmd = " select 	count(a.PER_ID) as SUM_QTY_M 
						   from 		PER_PERSONAL a, PER_POSITION b, PER_MGT c 
						   where 	a.POS_ID = b.POS_ID and b.PM_CODE = c.PM_CODE and a.PER_STATUS = 1 and a.PER_GENDER = 1
						   			and c.PS_CODE='$PS_CODE' and b.PM_CODE='$PM_CODE' and a.LEVEL_NO='$LEVEL_NO'
									$search_condition
						 ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SUM_QTY_M = $data2[SUM_QTY_M] + 0;

				$cmd = " select 	count(a.PER_ID) as SUM_QTY_F 
						   from 		PER_PERSONAL a, PER_POSITION b, PER_MGT c 
						   where 	a.POS_ID = b.POS_ID and b.PM_CODE = c.PM_CODE and a.PER_STATUS = 1 and a.PER_GENDER = 2
						   			and c.PS_CODE='$PS_CODE' and b.PM_CODE='$PM_CODE' and a.LEVEL_NO='$LEVEL_NO'
									$search_condition
						 ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SUM_QTY_F = $data2[SUM_QTY_F] + 0;

				$cmd = " insert into PER_SUM_DTL4 
							(SUM_ID, PS_CODE, PM_CODE, LEVEL_NO, SUM_QTY_M, SUM_QTY_F, UPDATE_USER, UPDATE_DATE) 
						   values 
						   	($SUM_ID, '$PS_CODE', '$PM_CODE', '$LEVEL_NO', $SUM_QTY_M, $SUM_QTY_F, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
			}  

			// ================================= PER_SUM_DTL5 ================================ //
			unset($arr_search_condition);
			if($OL_CODE=="02"){
				if($SESS_ORG_STRUCTURE==1){
					unset($arr_org);
					$arr_org[] = $ORG_ID;	//ถ้าเค้ากำหนดเป็นกรมตรงๆเลย
					$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$ORG_ID and OL_CODE='03' ";
					$db_dpis1->send_cmd($cmd);
					while($data1 = $db_dpis1->get_array()) $arr_org[] = $data1[ORG_ID];		
					$arr_search_condition[] = "(b.ORG_ID in (". implode(",", $arr_org) ."))";
				}else{	
					$arr_search_condition[] = "(a.DEPARTMENT_ID=$ORG_ID)";
				}
			}elseif($OL_CODE=="03"){
				if($SESS_ORG_STRUCTURE==1){
					//กระทรวง ต้องหาสำนักกองภายใต้กรมลงไปอีก
					unset($arr_org);
					$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$ORG_ID and OL_CODE='02' ";		//หากรม ไม่มีในตาราง PER_ORG_ASS
					$db_dpis1->send_cmd($cmd);
					while($data1 = $db_dpis1->get_array()){
						$arr_org[] = $data1[ORG_ID];		 //$data[ORG_ID] = DEPARTMENT_ID
						//==หาสำนักกองภายใต้กรม ลงไปอีก 
						$cmd2 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data1[ORG_ID] and OL_CODE='03' ";
						$db_dpis2->send_cmd($cmd2);
						while($data2 = $db_dpis2->get_array()){
							$arr_org[] = $data2[ORG_ID];		 //$data2[ORG_ID] = ORG_ID	
							//==หาต่ำกว่าสำนักกอง 1 ภายใต้สำนักกอง ลงไปอีก
							$cmd3 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data2[ORG_ID] and OL_CODE='04' ";
							$db_dpis3->send_cmd($cmd3);
							while($data3 = $db_dpis3->get_array()){
								$arr_org[] = $data3[ORG_ID];		 //$data3[ORG_ID] = ORG_ID_1
								//==หาต่ำกว่าสำนักกอง 2 ภายใต้สำนักกอง ลงไปอีก
								$cmd4 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data3[ORG_ID] and OL_CODE='05' ";
								$db_dpis4->send_cmd($cmd4);
								while($data4 = $db_dpis4->get_array()){
									$arr_org[] = $data4[ORG_ID];		 //$data4[ORG_ID] = ORG_ID_2
								}
							}
						} //end while
					}		
					$arr_search_condition[] = "(b.ORG_ID in (". implode(",", $arr_org) ."))";	
				}else{
				 	$arr_search_condition[] = "(a.ORG_ID=$ORG_ID)";
				}
			}
			$search_condition = "";
			if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

			$cmd = " select 	c.OP_CODE, b.PM_CODE, a.LEVEL_NO, count(*) as SUM_QTY 
					   from 		PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_ORG_PROVINCE d
					   where 	a.POS_ID = b.POS_ID and b.ORG_ID = c.ORG_ID and c.OP_CODE = d.OP_CODE and a.PER_STATUS = 1
					   			$search_condition
					   group by 	c.OP_CODE, b.PM_CODE, a.LEVEL_NO ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			while ( $data1 = $db_dpis1->get_array() ) {
				$OP_CODE = $data1[OP_CODE];
				$PM_CODE = $data1[PM_CODE];
				$LEVEL_NO = $data1[LEVEL_NO];
				$SUM_QTY = $data1[SUM_QTY] + 0;

				$cmd = " insert into PER_SUM_DTL5 
							(SUM_ID, OP_CODE, PM_CODE, LEVEL_NO, SUM_QTY, UPDATE_USER, UPDATE_DATE) 
						   values 
						   	($SUM_ID, '$OP_CODE', '$PM_CODE', '$LEVEL_NO', $SUM_QTY, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
			}  

			// ================================= PER_SUM_DTL6 ================================ //
			unset($arr_result);
//			echo "<br>$OL_CODE :: $ORG_ID<br>";
			trace_org_sumdtl6($ORG_ID, $OL_CODE);
//			echo "<pre>"; print_r($arr_result); echo "</pre>";
			foreach($arr_result as $OT_CODE => $SUM_QTY){
				$cmd = " insert into PER_SUM_DTL6 
							(SUM_ID, OT_CODE, SUM_QTY, UPDATE_USER, UPDATE_DATE) 
						   values 
						   	($SUM_ID, '$OT_CODE', $SUM_QTY, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
			} // end foreach

			// ================================= PER_SUM_DTL7 ================================ //
			unset($arr_search_condition);
			if($OL_CODE=="02"){
				if($SESS_ORG_STRUCTURE==1){
					unset($arr_org);
					$arr_org[] = $ORG_ID;	//ถ้าเค้ากำหนดเป็นกรมตรงๆเลย
					$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$ORG_ID and OL_CODE='03' ";
					$db_dpis1->send_cmd($cmd);
					while($data1 = $db_dpis1->get_array()) $arr_org[] = $data1[ORG_ID];		
					$arr_search_condition[] = "(b.ORG_ID in (". implode(",", $arr_org) ."))";
				}else{	
					$arr_search_condition[] = "(a.DEPARTMENT_ID=$ORG_ID)";
				}
			}elseif($OL_CODE=="03"){
				if($SESS_ORG_STRUCTURE==1){
					//กระทรวง ต้องหาสำนักกองภายใต้กรมลงไปอีก
					unset($arr_org);
					$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$ORG_ID and OL_CODE='02' ";		//หากรม ไม่มีในตาราง PER_ORG_ASS
					$db_dpis1->send_cmd($cmd);
					while($data1 = $db_dpis1->get_array()){
						$arr_org[] = $data1[ORG_ID];		 //$data[ORG_ID] = DEPARTMENT_ID
						//==หาสำนักกองภายใต้กรม ลงไปอีก 
						$cmd2 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data1[ORG_ID] and OL_CODE='03' ";
						$db_dpis2->send_cmd($cmd2);
						while($data2 = $db_dpis2->get_array()){
							$arr_org[] = $data2[ORG_ID];		 //$data2[ORG_ID] = ORG_ID	
							//==หาต่ำกว่าสำนักกอง 1 ภายใต้สำนักกอง ลงไปอีก
							$cmd3 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data2[ORG_ID] and OL_CODE='04' ";
							$db_dpis3->send_cmd($cmd3);
							while($data3 = $db_dpis3->get_array()){
								$arr_org[] = $data3[ORG_ID];		 //$data3[ORG_ID] = ORG_ID_1
								//==หาต่ำกว่าสำนักกอง 2 ภายใต้สำนักกอง ลงไปอีก
								$cmd4 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data3[ORG_ID] and OL_CODE='05' ";
								$db_dpis4->send_cmd($cmd4);
								while($data4 = $db_dpis4->get_array()){
									$arr_org[] = $data4[ORG_ID];		 //$data4[ORG_ID] = ORG_ID_2
								}
							}
						} //end while
					}		
					$arr_search_condition[] = "(b.ORG_ID in (". implode(",", $arr_org) ."))";	
				}else{
				 	$arr_search_condition[] = "(a.ORG_ID=$ORG_ID)";
				}
			}
			$search_condition = "";
			if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

			$cmd = " select 	distinct a.LEVEL_NO 
					   from 		PER_PERSONAL a, PER_POSITION b
					   where 	a.POS_ID=b.POS_ID and a.PER_STATUS = 1
					   			$search_condition
					   order by 	a.LEVEL_NO ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			while ( $data1 = $db_dpis1->get_array() ) {
				$LEVEL_NO = $data1[LEVEL_NO];

				$cmd = " select 	count(a.PER_ID) as SUM_QTY_M 
						   from 		PER_PERSONAL a, PER_POSITION b
						   where 	a.POS_ID=b.POS_ID and a.PER_STATUS = 1 and a.PER_GENDER = 1 and a.LEVEL_NO='$LEVEL_NO'
									$search_condition
						 ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SUM_QTY_M = $data2[SUM_QTY_M] + 0;

				$cmd = " select 	count(a.PER_ID) as SUM_QTY_F 
						   from 		PER_PERSONAL a, PER_POSITION b
						   where 	a.POS_ID=b.POS_ID and a.PER_STATUS = 1 and a.PER_GENDER = 2 and a.LEVEL_NO='$LEVEL_NO'
									$search_condition
						 ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SUM_QTY_F = $data2[SUM_QTY_F] + 0;

				$cmd = " insert into PER_SUM_DTL7 
							(SUM_ID, LEVEL_NO, SUM_QTY_M, SUM_QTY_F, UPDATE_USER, UPDATE_DATE) 
						   values 
							($SUM_ID, '$LEVEL_NO', $SUM_QTY_M, $SUM_QTY_F, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
			}  

			// ================================= PER_SUM_DTL8 ================================ //
			unset($arr_search_condition);
			if($OL_CODE=="02"){
				if($SESS_ORG_STRUCTURE==1){
					unset($arr_org);
					$arr_org[] = $ORG_ID;	//ถ้าเค้ากำหนดเป็นกรมตรงๆเลย
					$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$ORG_ID and OL_CODE='03' ";
					$db_dpis1->send_cmd($cmd);
					while($data1 = $db_dpis1->get_array()) $arr_org[] = $data1[ORG_ID];		
					$arr_search_condition[] = "(b.ORG_ID in (". implode(",", $arr_org) ."))";
				}else{	
					$arr_search_condition[] = "(a.DEPARTMENT_ID=$ORG_ID)";
				}
			}elseif($OL_CODE=="03"){
				if($SESS_ORG_STRUCTURE==1){
					//กระทรวง ต้องหาสำนักกองภายใต้กรมลงไปอีก
					unset($arr_org);
					$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$ORG_ID and OL_CODE='02' ";		//หากรม ไม่มีในตาราง PER_ORG_ASS
					$db_dpis1->send_cmd($cmd);
					while($data1 = $db_dpis1->get_array()){
						$arr_org[] = $data1[ORG_ID];		 //$data[ORG_ID] = DEPARTMENT_ID
						//==หาสำนักกองภายใต้กรม ลงไปอีก 
						$cmd2 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data1[ORG_ID] and OL_CODE='03' ";
						$db_dpis2->send_cmd($cmd2);
						while($data2 = $db_dpis2->get_array()){
							$arr_org[] = $data2[ORG_ID];		 //$data2[ORG_ID] = ORG_ID	
							//==หาต่ำกว่าสำนักกอง 1 ภายใต้สำนักกอง ลงไปอีก
							$cmd3 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data2[ORG_ID] and OL_CODE='04' ";
							$db_dpis3->send_cmd($cmd3);
							while($data3 = $db_dpis3->get_array()){
								$arr_org[] = $data3[ORG_ID];		 //$data3[ORG_ID] = ORG_ID_1
								//==หาต่ำกว่าสำนักกอง 2 ภายใต้สำนักกอง ลงไปอีก
								$cmd4 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data3[ORG_ID] and OL_CODE='05' ";
								$db_dpis4->send_cmd($cmd4);
								while($data4 = $db_dpis4->get_array()){
									$arr_org[] = $data4[ORG_ID];		 //$data4[ORG_ID] = ORG_ID_2
								}
							}
						} //end while
					}		
					$arr_search_condition[] = "(b.ORG_ID in (". implode(",", $arr_org) ."))";	
				}else{
				 	$arr_search_condition[] = "(a.ORG_ID=$ORG_ID)";
				}
			}
			$search_condition = "";
			if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

			$cmd = " select 	distinct b.PL_CODE, a.LEVEL_NO
					   from 		PER_PERSONAL a, PER_POSITION b
					   where 	a.POS_ID=b.POS_ID and a.PER_STATUS = 1
					   			$search_condition
					   order by 	b.PL_CODE, a.LEVEL_NO ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			while ( $data1 = $db_dpis1->get_array() ) {
				$PL_CODE = $data1[PL_CODE];
				$LEVEL_NO = $data1[LEVEL_NO];

				$cmd = " select 	count(a.PER_ID) as SUM_QTY_M 
						   from 		PER_PERSONAL a, PER_POSITION b
						   where 	a.POS_ID=b.POS_ID and a.PER_STATUS = 1 and a.PER_GENDER = 1 
						   			and b.PL_CODE='$PL_CODE' and a.LEVEL_NO='$LEVEL_NO'
									$search_condition
						 ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SUM_QTY_M = $data2[SUM_QTY_M] + 0;

				$cmd = " select 	count(a.PER_ID) as SUM_QTY_F 
						   from 		PER_PERSONAL a, PER_POSITION b
						   where 	a.POS_ID=b.POS_ID and a.PER_STATUS = 1 and a.PER_GENDER = 2 
						   			and b.PL_CODE='$PL_CODE' and a.LEVEL_NO='$LEVEL_NO'
									$search_condition
						 ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SUM_QTY_F = $data2[SUM_QTY_F] + 0;

				$cmd = " insert into PER_SUM_DTL8 
							(SUM_ID, PL_CODE, LEVEL_NO, SUM_QTY_M, SUM_QTY_F, UPDATE_USER, UPDATE_DATE) 
						   values 
							($SUM_ID, '$PL_CODE', '$LEVEL_NO', $SUM_QTY_M, $SUM_QTY_F, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
			}  

			// ================================= PER_SUM_DTL9 ================================ //
			unset($arr_search_condition);
			if($OL_CODE=="02"){
				if($SESS_ORG_STRUCTURE==1){
					unset($arr_org);
					$arr_org[] = $ORG_ID;	//ถ้าเค้ากำหนดเป็นกรมตรงๆเลย
					$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$ORG_ID and OL_CODE='03' ";
					$db_dpis1->send_cmd($cmd);
					while($data1 = $db_dpis1->get_array()) $arr_org[] = $data1[ORG_ID];		
					$arr_search_condition[] = "(b.ORG_ID in (". implode(",", $arr_org) ."))";
				}else{	
					$arr_search_condition[] = "(a.DEPARTMENT_ID=$ORG_ID)";
				}
			}elseif($OL_CODE=="03"){
				if($SESS_ORG_STRUCTURE==1){
					//กระทรวง ต้องหาสำนักกองภายใต้กรมลงไปอีก
					unset($arr_org);
					$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$ORG_ID and OL_CODE='02' ";		//หากรม ไม่มีในตาราง PER_ORG_ASS
					$db_dpis1->send_cmd($cmd);
					while($data1 = $db_dpis1->get_array()){
						$arr_org[] = $data1[ORG_ID];		 //$data[ORG_ID] = DEPARTMENT_ID
						//==หาสำนักกองภายใต้กรม ลงไปอีก 
						$cmd2 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data1[ORG_ID] and OL_CODE='03' ";
						$db_dpis2->send_cmd($cmd2);
						while($data2 = $db_dpis2->get_array()){
							$arr_org[] = $data2[ORG_ID];		 //$data2[ORG_ID] = ORG_ID	
							//==หาต่ำกว่าสำนักกอง 1 ภายใต้สำนักกอง ลงไปอีก
							$cmd3 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data2[ORG_ID] and OL_CODE='04' ";
							$db_dpis3->send_cmd($cmd3);
							while($data3 = $db_dpis3->get_array()){
								$arr_org[] = $data3[ORG_ID];		 //$data3[ORG_ID] = ORG_ID_1
								//==หาต่ำกว่าสำนักกอง 2 ภายใต้สำนักกอง ลงไปอีก
								$cmd4 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data3[ORG_ID] and OL_CODE='05' ";
								$db_dpis4->send_cmd($cmd4);
								while($data4 = $db_dpis4->get_array()){
									$arr_org[] = $data4[ORG_ID];		 //$data4[ORG_ID] = ORG_ID_2
								}
							}
						} //end while
					}		
					$arr_search_condition[] = "(b.ORG_ID in (". implode(",", $arr_org) ."))";	
				}else{
				 	$arr_search_condition[] = "(a.ORG_ID=$ORG_ID)";
				}
			}
			$search_condition = "";
			if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

			$cmd = " select 	distinct b.PL_CODE, d.EL_CODE
					   from 		PER_PERSONAL a, PER_POSITION b, PER_EDUCATE c, PER_EDUCNAME d
					   where 	a.POS_ID=b.POS_ID and a.PER_ID=c.PER_ID and c.EN_CODE=d.EN_CODE and a.PER_STATUS = 1
					   			$search_condition
					   order by 	b.PL_CODE, d.EL_CODE ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			while ( $data1 = $db_dpis1->get_array() ) {
				$PL_CODE = $data1[PL_CODE];
				$EL_CODE = $data1[EL_CODE];

				$cmd = " select 	count(a.PER_ID) as SUM_QTY_M 
						   from 		PER_PERSONAL a, PER_POSITION b, PER_EDUCATE c, PER_EDUCNAME d
						   where 	a.POS_ID=b.POS_ID and a.PER_ID=c.PER_ID and c.EN_CODE=d.EN_CODE 
						   			and a.PER_STATUS = 1 and a.PER_GENDER = 1 
						   			and b.PL_CODE='$PL_CODE' and d.EL_CODE='$EL_CODE'
									$search_condition
						 ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SUM_QTY_M = $data2[SUM_QTY_M] + 0;

				$cmd = " select 	count(a.PER_ID) as SUM_QTY_F 
						   from 		PER_PERSONAL a, PER_POSITION b, PER_EDUCATE c, PER_EDUCNAME d
						   where 	a.POS_ID=b.POS_ID and a.PER_ID=c.PER_ID and c.EN_CODE=d.EN_CODE 
						   			and a.PER_STATUS = 1 and a.PER_GENDER = 2 
						   			and b.PL_CODE='$PL_CODE' and d.EL_CODE='$EL_CODE'
									$search_condition
						 ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SUM_QTY_F = $data2[SUM_QTY_F] + 0;

				$cmd = " insert into PER_SUM_DTL9 
							(SUM_ID, PL_CODE, EL_CODE, SUM_QTY_M, SUM_QTY_F, UPDATE_USER, UPDATE_DATE) 
						   values 
							($SUM_ID, '$PL_CODE', '$EL_CODE', $SUM_QTY_M, $SUM_QTY_F, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
			}  

			// ================================= PER_SUM_DTL10 ================================ //
			unset($arr_search_condition);
			if($OL_CODE=="02"){
				if($SESS_ORG_STRUCTURE==1){
					unset($arr_org);
					$arr_org[] = $ORG_ID;	//ถ้าเค้ากำหนดเป็นกรมตรงๆเลย
					$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$ORG_ID and OL_CODE='03' ";
					$db_dpis1->send_cmd($cmd);
					while($data1 = $db_dpis1->get_array()) $arr_org[] = $data1[ORG_ID];		
					$arr_search_condition[] = "(b.ORG_ID in (". implode(",", $arr_org) ."))";
				}else{	
					$arr_search_condition[] = "(a.DEPARTMENT_ID=$ORG_ID)";
				}
			}elseif($OL_CODE=="03"){
				if($SESS_ORG_STRUCTURE==1){
					//กระทรวง ต้องหาสำนักกองภายใต้กรมลงไปอีก
					unset($arr_org);
					$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$ORG_ID and OL_CODE='02' ";		//หากรม ไม่มีในตาราง PER_ORG_ASS
					$db_dpis1->send_cmd($cmd);
					while($data1 = $db_dpis1->get_array()){
						$arr_org[] = $data1[ORG_ID];		 //$data[ORG_ID] = DEPARTMENT_ID
						//==หาสำนักกองภายใต้กรม ลงไปอีก 
						$cmd2 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data1[ORG_ID] and OL_CODE='03' ";
						$db_dpis2->send_cmd($cmd2);
						while($data2 = $db_dpis2->get_array()){
							$arr_org[] = $data2[ORG_ID];		 //$data2[ORG_ID] = ORG_ID	
							//==หาต่ำกว่าสำนักกอง 1 ภายใต้สำนักกอง ลงไปอีก
							$cmd3 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data2[ORG_ID] and OL_CODE='04' ";
							$db_dpis3->send_cmd($cmd3);
							while($data3 = $db_dpis3->get_array()){
								$arr_org[] = $data3[ORG_ID];		 //$data3[ORG_ID] = ORG_ID_1
								//==หาต่ำกว่าสำนักกอง 2 ภายใต้สำนักกอง ลงไปอีก
								$cmd4 = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$data3[ORG_ID] and OL_CODE='05' ";
								$db_dpis4->send_cmd($cmd4);
								while($data4 = $db_dpis4->get_array()){
									$arr_org[] = $data4[ORG_ID];		 //$data4[ORG_ID] = ORG_ID_2
								}
							}
						} //end while
					}		
					$arr_search_condition[] = "(b.ORG_ID in (". implode(",", $arr_org) ."))";	
				}else{
				 	$arr_search_condition[] = "(a.ORG_ID=$ORG_ID)";
				}
			}
			$search_condition = "";
			if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

			$cmd = " select 	distinct b.PL_CODE, a.LEVEL_NO, d.EL_CODE
					   from 		PER_PERSONAL a, PER_POSITION b, PER_EDUCATE c, PER_EDUCNAME d
					   where 	a.POS_ID=b.POS_ID and a.PER_ID=c.PER_ID and c.EN_CODE=d.EN_CODE and a.PER_STATUS = 1
					   			$search_condition
					   order by 	b.PL_CODE, a.LEVEL_NO, d.EL_CODE ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			while ( $data1 = $db_dpis1->get_array() ) {
				$PL_CODE = $data1[PL_CODE];
				$LEVEL_NO = $data1[LEVEL_NO];
				$EL_CODE = $data1[EL_CODE];

				$cmd = " select 	count(a.PER_ID) as SUM_QTY_M 
						   from 		PER_PERSONAL a, PER_POSITION b, PER_EDUCATE c, PER_EDUCNAME d
						   where 	a.POS_ID=b.POS_ID and a.PER_ID=c.PER_ID and c.EN_CODE=d.EN_CODE and a.PER_STATUS = 1 and a.PER_GENDER = 1 
						   			and b.PL_CODE='$PL_CODE' and a.LEVEL_NO='$LEVEL_NO' and d.EL_CODE='$EL_CODE'
									$search_condition
						 ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SUM_QTY_M = $data2[SUM_QTY_M] + 0;

				$cmd = " select 	count(a.PER_ID) as SUM_QTY_F 
						   from 		PER_PERSONAL a, PER_POSITION b, PER_EDUCATE c, PER_EDUCNAME d
						   where 	a.POS_ID=b.POS_ID and a.PER_ID=c.PER_ID and c.EN_CODE=d.EN_CODE and a.PER_STATUS = 1 and a.PER_GENDER = 2 
						   			and b.PL_CODE='$PL_CODE' and a.LEVEL_NO='$LEVEL_NO' and d.EL_CODE='$EL_CODE'
									$search_condition
						 ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SUM_QTY_F = $data2[SUM_QTY_F] + 0;

				$cmd = " insert into PER_SUM_DTL10 
							(SUM_ID, PL_CODE, LEVEL_NO, EL_CODE, SUM_QTY_M, SUM_QTY_F, UPDATE_USER, UPDATE_DATE) 
						   values 
							($SUM_ID, '$PL_CODE', '$LEVEL_NO', '$EL_CODE', $SUM_QTY_M, $SUM_QTY_F, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
			}  

		} // end while outer
	} // end if
	
	if( !$UPD && !$DEL && !$VIEW ){
//		$SUM_YEAR = "";
//		$SUM_DATE = "";
	} // end if		
?>