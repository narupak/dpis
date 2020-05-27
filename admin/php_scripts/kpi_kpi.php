<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($_POST[KPI_YEAR])		$KPI_YEAR = $_POST[KPI_YEAR];

        
        $cmdChk ="  SELECT COLUMN_NAME, DATA_SCALE
                      FROM USER_TAB_COLS 
                      WHERE TABLE_NAME = 'PER_KPI' AND 
                    UPPER(COLUMN_NAME)IN('KPI_TARGET_LEVEL1','KPI_TARGET_LEVEL2','KPI_TARGET_LEVEL3','KPI_TARGET_LEVEL4','KPI_TARGET_LEVEL5')";
        $countChk = $db_dpis2->send_cmd($cmdChk);
        $srtAlert='';
        if($countChk){
            while($dataAlter = $db_dpis2->get_array()){
                $COL_COLUMN_NAME=$dataAlter[COLUMN_NAME];
                $COL_DATA_SCALE=$dataAlter[DATA_SCALE];
                if($COL_DATA_SCALE==0){
                    $srtAlert='<font color=red>เมนู K02 ตัวชี้วัด มีการปรับปรุงโครงสร้างฐานข้อมูลเพื่อรองรับการบันทึกค่าเป้าหมาย (เป้าหมาย 1 - 5) ในรูปแบบทศนิยม<br>
                        จึงจำเป็นต้องดำเนินการปรับเปลี่ยนฐานข้อมูลที่เมนู C07 การจัดการข้อมูล -> C0704 โปรแกรมปรับเปลี่ยนฐานข้อมูล<br>
                        หากท่านไม่สามารถเข้าถึงเมนูดังกล่าวได้ โปรดติดต่อผู้ดูแลระบบ เพื่อดำเนินการต่อไป</font>';
                }
            }
        }
        
        
        
        
        
        
        
	//	$BKK_FLAG==1
	$where_DEPARTMENT_ID="";
	if($DEPARTMENT_ID) $where_DEPARTMENT_ID = " (DEPARTMENT_ID=$DEPARTMENT_ID";
	if($where_DEPARTMENT_ID){	$where_DEPARTMENT_ID .= " or DEPARTMENT_ID=0)"; 	}else{	$where_DEPARTMENT_ID .= " DEPARTMENT_ID=0";	}
	if($ORG_ID) $where_DEPARTMENT_ID .= " and (ORG_ID=$ORG_ID or ORG_ID=0)";

	if($command=="ADD" && $KPI_YEAR && trim($NEW_KPI_NAME) && $NEW_KPI_PER_ID && $NEW_PFR_ID){
		$KPI_ID_REF = $KPI_ID;
		if(!$KPI_ID) $KPI_ID_REF = "NULL";
		if(!$DEPARTMENT_ID)	$DEPARTMENT_ID=0;		//		$BKK_FLAG==1
		if(!$ORG_ID)	$ORG_ID=0;

		if(!$NEW_KPI_WEIGHT) $NEW_KPI_WEIGHT = "NULL";
		if(!$NEW_KPI_TARGET_LEVEL1) $NEW_KPI_TARGET_LEVEL1 = "NULL";
		if(!$NEW_KPI_TARGET_LEVEL2) $NEW_KPI_TARGET_LEVEL2 = "NULL";
		if(!$NEW_KPI_TARGET_LEVEL3) $NEW_KPI_TARGET_LEVEL3 = "NULL";
		if(!$NEW_KPI_TARGET_LEVEL4) $NEW_KPI_TARGET_LEVEL4 = "NULL";
		if(!$NEW_KPI_TARGET_LEVEL5) $NEW_KPI_TARGET_LEVEL5 = "NULL";
		if(!$NEW_KPI_EVALUATE1) $NEW_KPI_EVALUATE1 = "NULL";
		if(!$NEW_KPI_EVALUATE2) $NEW_KPI_EVALUATE2 = "NULL";
		if(!$NEW_KPI_EVALUATE3) $NEW_KPI_EVALUATE3 = "NULL";
		if(!$NEW_KPI_EVALUATE) $NEW_KPI_EVALUATE = "NULL";
		
		if (!get_magic_quotes_gpc()) {
			$NEW_KPI_TARGET_LEVEL1_DESC = addslashes(str_replace('"', "&quot;", trim($NEW_KPI_TARGET_LEVEL1_DESC)));
			$NEW_KPI_TARGET_LEVEL2_DESC = addslashes(str_replace('"', "&quot;", trim($NEW_KPI_TARGET_LEVEL2_DESC)));
			$NEW_KPI_TARGET_LEVEL3_DESC = addslashes(str_replace('"', "&quot;", trim($NEW_KPI_TARGET_LEVEL3_DESC)));
			$NEW_KPI_TARGET_LEVEL4_DESC = addslashes(str_replace('"', "&quot;", trim($NEW_KPI_TARGET_LEVEL4_DESC)));
			$NEW_KPI_TARGET_LEVEL5_DESC = addslashes(str_replace('"', "&quot;", trim($NEW_KPI_TARGET_LEVEL5_DESC)));
		}else{
			$NEW_KPI_TARGET_LEVEL1_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($NEW_KPI_TARGET_LEVEL1_DESC))));
			$NEW_KPI_TARGET_LEVEL2_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($NEW_KPI_TARGET_LEVEL2_DESC))));
			$NEW_KPI_TARGET_LEVEL3_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($NEW_KPI_TARGET_LEVEL3_DESC))));
			$NEW_KPI_TARGET_LEVEL4_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($NEW_KPI_TARGET_LEVEL4_DESC))));
			$NEW_KPI_TARGET_LEVEL5_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($NEW_KPI_TARGET_LEVEL5_DESC))));
		} // end if
		
		//$NEW_KPI_NAME=str_replace("'","&#39;",trim(preg_replace('/\s+/', ' ', $NEW_KPI_NAME))); // แทนค่า ' และ " เป็น html
                $NEW_KPI_NAME=str_replace('"','',str_replace("'","",trim(preg_replace('/\s+/', ' ', $NEW_KPI_NAME)))); // บันทึกแบบไม่ให้มี ' และ "
		$cmd = " select max(KPI_ID) as max_id from PER_KPI ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$KPI_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_KPI
							(KPI_ID, KPI_NAME, KPI_WEIGHT, KPI_MEASURE, KPI_PER_ID,ORG_NAME, UNDER_ORG_NAME1, PFR_ID,
							 KPI_TARGET_LEVEL1, KPI_TARGET_LEVEL2, KPI_TARGET_LEVEL3, KPI_TARGET_LEVEL4, KPI_TARGET_LEVEL5,
							 KPI_TARGET_LEVEL1_DESC, KPI_TARGET_LEVEL2_DESC, KPI_TARGET_LEVEL3_DESC, KPI_TARGET_LEVEL4_DESC, 
							 KPI_TARGET_LEVEL5_DESC, KPI_EVALUATE, KPI_ID_REF, KPI_YEAR, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE, 
							 KPI_TYPE, KPI_DEFINE, KPI_SCORE_FLAG, ORG_ID)
						values
							($KPI_ID, '$NEW_KPI_NAME', $NEW_KPI_WEIGHT, '$NEW_KPI_MEASURE', $NEW_KPI_PER_ID, '$NEW_ORG_NAME', '$NEW_UNDER_ORG_NAME1', $NEW_PFR_ID,
							 $NEW_KPI_TARGET_LEVEL1, $NEW_KPI_TARGET_LEVEL2, $NEW_KPI_TARGET_LEVEL3, $NEW_KPI_TARGET_LEVEL4, 
							 $NEW_KPI_TARGET_LEVEL5,	'$NEW_KPI_TARGET_LEVEL1_DESC', '$NEW_KPI_TARGET_LEVEL2_DESC', 
							 '$NEW_KPI_TARGET_LEVEL3_DESC', '$NEW_KPI_TARGET_LEVEL4_DESC', '$NEW_KPI_TARGET_LEVEL5_DESC', 
							 $NEW_KPI_EVALUATE, $KPI_ID_REF, '$KPI_YEAR', $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE', '$NEW_KPI_TYPE', '$NEW_KPI_DEFINE', '$NEW_KPI_SCORE_FLAG', $ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//echo "$cmd<br>";
		// $db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มตัวชี้วัดย่อย [$KPI_ID_REF : $KPI_ID : $KPI_NAME]");

		$KPI_ID_REF += 0;

		// ================================= UPDATE KPI_EVALUATE OF KPI PARENT ============================= //
		update_parent_evaluate($KPI_ID, $KPI_ID_REF);
		// ================================= UPDATE KPI_EVALUATE OF KPI PARENT ============================= //
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับปรุงผลการประเมิน KPI PARENT ของ [$KPI_ID_REF : $KPI_ID : $KPI_NAME]");
	} // end if
	
	if($command=="UPDATE" && $KPI_ID && $KPI_YEAR && trim($KPI_NAME) && $KPI_PER_ID && $PFR_ID){
		if(!$DEPARTMENT_ID)	$DEPARTMENT_ID=0;		//		$BKK_FLAG==1
		if(!$ORG_ID)	$ORG_ID=0;
		if(!$KPI_WEIGHT) $KPI_WEIGHT = "NULL";
		if(!$KPI_TARGET_LEVEL1) $KPI_TARGET_LEVEL1 = "NULL";
		if(!$KPI_TARGET_LEVEL1) $KPI_TARGET_LEVEL1 = "NULL";
		if(!$KPI_TARGET_LEVEL2) $KPI_TARGET_LEVEL2 = "NULL";
		if(!$KPI_TARGET_LEVEL3) $KPI_TARGET_LEVEL3 = "NULL";
		if(!$KPI_TARGET_LEVEL4) $KPI_TARGET_LEVEL4 = "NULL";
		if(!$KPI_TARGET_LEVEL5) $KPI_TARGET_LEVEL5 = "NULL";
		if(!$KPI_EVALUATE1)  $KPI_EVALUATE1 = "NULL";
		if(!$KPI_EVALUATE2) $KPI_EVALUATE2 = "NULL";
		if(!$KPI_EVALUATE3) $KPI_EVALUATE3 = "NULL";
		if(!$KPI_EVALUATE) $KPI_EVALUATE = "NULL";
		if (!get_magic_quotes_gpc()) {
			$KPI_WEIGHT= addslashes(str_replace('"', "&quot;", trim($KPI_WEIGHT)));
			$KPI_EVALUATE= addslashes(str_replace('"', "&quot;", trim($KPI_EVALUATE)));
			$KPI_TARGET_LEVEL1_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL1_DESC)));
			$KPI_TARGET_LEVEL2_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL2_DESC)));
			$KPI_TARGET_LEVEL3_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL3_DESC)));
			$KPI_TARGET_LEVEL4_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL4_DESC)));
			$KPI_TARGET_LEVEL5_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL5_DESC)));
		}else{
			$KPI_WEIGHT = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_WEIGHT))));
			$KPI_EVALUATE = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_EVALUATE))));
			$KPI_TARGET_LEVEL1_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL1_DESC))));
			$KPI_TARGET_LEVEL2_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL2_DESC))));
			$KPI_TARGET_LEVEL3_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL3_DESC))));
			$KPI_TARGET_LEVEL4_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL4_DESC))));
			$KPI_TARGET_LEVEL5_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL5_DESC))));
		} // end if
		
		$updae_evaulate = "";
                //$KPI_NAME=str_replace("'","&#39;",trim(preg_replace('/\s+/', ' ', $KPI_NAME))); // แทนค่า ' และ " เป็น html
		$KPI_NAME=str_replace('"','',str_replace("'","",trim(preg_replace('/\s+/', ' ', $KPI_NAME)))); // บันทึกแบบไม่ให้มี ' และ "
		$cmd = " update PER_KPI set
							KPI_NAME='$KPI_NAME', 
							KPI_WEIGHT=$KPI_WEIGHT,
							KPI_MEASURE='$KPI_MEASURE',
							KPI_PER_ID='$KPI_PER_ID',
							ORG_ID=$ORG_ID ,
							ORG_NAME='$ORG_NAME',
							UNDER_ORG_NAME1='$UNDER_ORG_NAME1',
							PFR_ID='$PFR_ID',
							KPI_TARGET_LEVEL1=$KPI_TARGET_LEVEL1,
							KPI_TARGET_LEVEL2=$KPI_TARGET_LEVEL2,
							KPI_TARGET_LEVEL3=$KPI_TARGET_LEVEL3,
							KPI_TARGET_LEVEL4=$KPI_TARGET_LEVEL4,
							KPI_TARGET_LEVEL5=$KPI_TARGET_LEVEL5,
							KPI_TARGET_LEVEL1_DESC='$KPI_TARGET_LEVEL1_DESC',
							KPI_TARGET_LEVEL2_DESC='$KPI_TARGET_LEVEL2_DESC',
							KPI_TARGET_LEVEL3_DESC='$KPI_TARGET_LEVEL3_DESC',
							KPI_TARGET_LEVEL4_DESC='$KPI_TARGET_LEVEL4_DESC',
							KPI_TARGET_LEVEL5_DESC='$KPI_TARGET_LEVEL5_DESC',
							KPI_EVALUATE = $KPI_EVALUATE,
							KPI_RESULT='$KPI_RESULT', 
							KPI_TYPE='$KPI_TYPE', 
							KPI_DEFINE='$KPI_DEFINE', 
							KPI_SCORE_FLAG='$KPI_SCORE_FLAG', 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
						 where KPI_ID=$KPI_ID "; 
		$db_dpis->send_cmd($cmd);
		//echo "$cmd<br>";
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับปรุงตัวชี้วัด [$KPI_ID_REF : $KPI_ID : $KPI_NAME]");

		if($KPI_LEAF_NODE){
			// ================================= UPDATE KPI_EVALUATE OF KPI PARENT ============================= //
			update_parent_evaluate($KPI_ID, $KPI_ID_REF);
			// ================================= UPDATE KPI_EVALUATE OF KPI PARENT ============================= //
			
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับปรุงผลการประเมิน KPI PARENT ของ [$KPI_ID_REF : $KPI_ID : $KPI_NAME]");
		} // end if
	} // end if

	if($command=="CHANGEKPIPARENT" && isset($NEW_KPI_ID_REF)){
		if(!$NEW_KPI_ID_REF) $NEW_KPI_ID_REF = "NULL";

		$cmd = " update PER_KPI set KPI_ID_REF=$NEW_KPI_ID_REF where KPI_ID=$KPI_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เปลี่ยนระดับตัวชี้วัด [$KPI_ID : $KPI_NAME | $KPI_ID_REF => $NEW_KPI_ID_REF]");	

		$KPI_ID_REF = $NEW_KPI_ID_REF;
		$KPI_ID_REF += 0;

		// ================================= UPDATE KPI_EVALUATE OF KPI PARENT ============================= //
		update_parent_evaluate($KPI_ID, $KPI_ID_REF);
		// ================================= UPDATE KPI_EVALUATE OF KPI PARENT ============================= //
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับปรุงผลการประเมิน KPI PARENT ของ [$KPI_ID_REF : $KPI_ID : $KPI_NAME]");
	} // end if

	if($command=="DELETE" && $KPI_ID){
		delete_kpi($KPI_ID, $KPI_ID_REF);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบตัวชี้วัด [$KPI_ID_REF : $KPI_ID : $KPI_NAME]");

		$KPI_ID = $KPI_ID_REF + 0;
		unset($KPI_ID_REF);
		
		// ================================= UPDATE KPI_EVALUATE ============================= //
		$cmd = " select SUM(KPI_EVALUATE) as SUM_KPI_EVALUATE, COUNT(KPI_ID) as COUNT_KPI_CHILD from PER_KPI where KPI_ID_REF=$KPI_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$SUM_KPI_EVALUATE = $data[SUM_KPI_EVALUATE] + 0;
		$COUNT_KPI_CHILD = $data[COUNT_KPI_CHILD] + 0;
			
		$KPI_EVALUATE = "NULL";
		if($SUM_KPI_EVALUATE > 0 && $COUNT_KPI_CHILD > 0) $KPI_EVALUATE = floor($SUM_KPI_EVALUATE / $COUNT_KPI_CHILD);

		$cmd = " update PER_KPI set KPI_EVALUATE=$KPI_EVALUATE where KPI_ID=$KPI_ID ";
		$db_dpis->send_cmd($cmd);
		// ================================= UPDATE KPI_EVALUATE ============================= //
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับปรุงผลการประเมิน [$KPI_ID : $KPI_NAME]");
	} // end if

	$cmd = " select distinct KPI_YEAR from PER_KPI where (".$where_DEPARTMENT_ID.") order by KPI_YEAR desc ";
	$HAVE_YEAR = $db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		if(!$START_YEAR) $START_YEAR = $data[KPI_YEAR];
		$arr_kpi_year[] = $data[KPI_YEAR];
	} // end while
	
	if($command=="ADDYEAR" && $NEW_KPI_YEAR){
		$KPI_YEAR = $NEW_KPI_YEAR;
		if(!in_array($NEW_KPI_YEAR, $arr_kpi_year)) $arr_kpi_year[] = $NEW_KPI_YEAR;
		sort($arr_kpi_year);
		$HAVE_YEAR += 1;	
		
//		echo "<pre>"; print_r($arr_kpi_year); echo "</pre>";
	} // end if

	if(!$KPI_YEAR || !in_array($KPI_YEAR, $arr_kpi_year)) $KPI_YEAR = $START_YEAR;

//	echo "<pre> $HAVE_YEAR / "; print_r($arr_kpi_year); echo " $START_YEAR / $KPI_YEAR / </pre>";

	$cmd = " select KPI_ID from PER_KPI ";
	$HAVE_KPI = $db_dpis->send_cmd($cmd);

	if($KPI_ID)	{
		$cmd = " select KPI_ID_REF from PER_KPI where KPI_ID=$KPI_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$KPI_ID_REF = $data[KPI_ID_REF];

		$cmd = " select		KPI_NAME, KPI_WEIGHT, KPI_MEASURE, KPI_PER_ID, ORG_ID ,ORG_NAME, UNDER_ORG_NAME1, PFR_ID, 
										KPI_TARGET_LEVEL1, KPI_TARGET_LEVEL2, KPI_TARGET_LEVEL3, KPI_TARGET_LEVEL4, KPI_TARGET_LEVEL5, 
										KPI_TARGET_LEVEL1_DESC, KPI_TARGET_LEVEL2_DESC, KPI_TARGET_LEVEL3_DESC, 
										KPI_TARGET_LEVEL4_DESC, KPI_TARGET_LEVEL5_DESC, KPI_EVALUATE, KPI_RESULT, KPI_TYPE, KPI_DEFINE, KPI_SCORE_FLAG
						 from		PER_KPI
						 where	KPI_ID=$KPI_ID ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
//		echo "Data :: <pre>"; print_r($data); echo "</pre>";
		$KPI_NAME = $data[KPI_NAME];
		$KPI_WEIGHT = $data[KPI_WEIGHT];
		$KPI_MEASURE = $data[KPI_MEASURE];
		$ORG_ID = $data[ORG_ID];
		$ORG_NAME = $data[ORG_NAME];
		$UNDER_ORG_NAME1 = $data[UNDER_ORG_NAME1];
		$KPI_TARGET_LEVEL1 = $data[KPI_TARGET_LEVEL1];
		$KPI_TARGET_LEVEL2 = $data[KPI_TARGET_LEVEL2];
		$KPI_TARGET_LEVEL3 = $data[KPI_TARGET_LEVEL3];
		$KPI_TARGET_LEVEL4 = $data[KPI_TARGET_LEVEL4];
		$KPI_TARGET_LEVEL5 = $data[KPI_TARGET_LEVEL5];
		$KPI_TARGET_LEVEL1_DESC = $data[KPI_TARGET_LEVEL1_DESC];
		$KPI_TARGET_LEVEL2_DESC = $data[KPI_TARGET_LEVEL2_DESC];
		$KPI_TARGET_LEVEL3_DESC = $data[KPI_TARGET_LEVEL3_DESC];
		$KPI_TARGET_LEVEL4_DESC = $data[KPI_TARGET_LEVEL4_DESC];
		$KPI_TARGET_LEVEL5_DESC = $data[KPI_TARGET_LEVEL5_DESC];
		$KPI_EVALUATE = $data[KPI_EVALUATE];
		$KPI_RESULT = $data[KPI_RESULT];
		$KPI_TYPE = $data[KPI_TYPE];
		$KPI_DEFINE = $data[KPI_DEFINE];
		$KPI_SCORE_FLAG = $data[KPI_SCORE_FLAG];
		
		$KPI_PER_ID = $data[KPI_PER_ID];
		$cmd = " select PN_CODE, PER_NAME, PER_SURNAME from PER_PERSONAL where PER_ID=$KPI_PER_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$KPI_PER_NAME = $data2[PER_NAME] ." ". $data2[PER_SURNAME];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME = $data2[ORG_NAME];	
		
		$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$data2[PN_CODE]' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$KPI_PER_NAME = $data2[PN_NAME] . $KPI_PER_NAME;	
	
		$PFR_ID = $data[PFR_ID];
		$PFR_ID_REF = $PFR_NAME2 = "";
		if($PFR_ID){
			$cmd = " select PFR_ID_REF, PFR_NAME from PER_PERFORMANCE_REVIEW where PFR_ID=$PFR_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PFR_ID_REF = $data2[PFR_ID_REF];
			$PFR_NAME2 = $data2[PFR_NAME];
		}
		
		if ($PFR_ID!=$PFR_ID_REF) {
			$PFR_NAME1 = "";
			if($PFR_ID_REF){
				$cmd = " select PFR_ID_REF, PFR_NAME from PER_PERFORMANCE_REVIEW where PFR_ID=$PFR_ID_REF ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PFR_ID_REF = $data2[PFR_ID_REF];
				$PFR_NAME1 = $data2[PFR_NAME];
			}
			if ($PFR_ID!=$PFR_ID_REF) {
				$PFR_NAME = "";
				if($PFR_ID_REF){
					$cmd = " select PFR_ID_REF, PFR_NAME from PER_PERFORMANCE_REVIEW where PFR_ID=$PFR_ID_REF ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PFR_ID_REF = $data2[PFR_ID_REF];
					$PFR_NAME = $data2[PFR_NAME];
				}
			}
		}
		if (!$PFR_NAME1) {
			$PFR_NAME1 = $PFR_NAME2;
			$PFR_NAME2 = "";
		}
		if (!$PFR_NAME) {
			$PFR_NAME = $PFR_NAME1;
			$PFR_NAME1 = "";
		}
	
		$cmd = " select KPI_ID from PER_KPI where KPI_ID_REF=$KPI_ID ";
		$count_child = $db_dpis->send_cmd($cmd);
		$KPI_LEAF_NODE = 0;
		if(!$count_child) $KPI_LEAF_NODE = 1;
	}else{
		$KPI_NAME = "";
		$KPI_WEIGHT = "";
		$KPI_MEASURE = "";
		$KPI_PER_ID = "";
		$KPI_PER_NAME = "";
		$UNDER_ORG_NAME1 = "";
		$PFR_ID = "";
		$PFR_NAME = "";		$PFR_NAME1 = "";		$PFR_NAME2 = "";
		$KPI_TARGET_LEVEL1 = "";
		$KPI_TARGET_LEVEL2 = "";
		$KPI_TARGET_LEVEL3 = "";
		$KPI_TARGET_LEVEL4 = "";
		$KPI_TARGET_LEVEL5 = "";
		$KPI_TARGET_LEVEL1_DESC = "";
		$KPI_TARGET_LEVEL2_DESC = "";
		$KPI_TARGET_LEVEL3_DESC = "";
		$KPI_TARGET_LEVEL4_DESC = "";
		$KPI_TARGET_LEVEL5_DESC = "";
		$KPI_EVALUATE = "";
		$KPI_RESULT = "";
		$KPI_TYPE = "1";
		$KPI_DEFINE = "";
		$KPI_SCORE_FLAG = 1;
		$KPI_LEAF_NODE = 0;
	} // end if

	function list_tree_kpi ($pre_image, $kpi_parent, $sel_kpi_id, $tree_depth) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_KPI, $KPI_YEAR, $DEPARTMENT_ID, $BKK_FLAG, $where_DEPARTMENT_ID, $SESS_ORG_ID;
		
		$opened_kpi = substr($LIST_OPENED_KPI, 1, -1);
		$arr_opened_kpi = explode(",", $opened_kpi);
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		if ($BKK_FLAG==1) $order_str = "ORG_ID, KPI_TYPE, KPI_NAME";
		else $order_str = "ORG_ID, KPI_NAME";

		$cmd = " select 	KPI_ID , KPI_NAME, KPI_ID_REF, 
										KPI_TARGET_LEVEL1, KPI_TARGET_LEVEL2, KPI_TARGET_LEVEL3, KPI_TARGET_LEVEL4, KPI_TARGET_LEVEL5, 
										KPI_TARGET_LEVEL1_DESC, KPI_TARGET_LEVEL2_DESC, KPI_TARGET_LEVEL3_DESC, 
										KPI_TARGET_LEVEL4_DESC, KPI_TARGET_LEVEL5_DESC, KPI_EVALUATE, KPI_RESULT, KPI_TYPE, KPI_DEFINE, KPI_SCORE_FLAG, ORG_ID 
				   from 		PER_KPI 
				   where 	".(trim($kpi_parent)?"KPI_ID_REF = $kpi_parent":"(KPI_ID_REF = 0 or KPI_ID_REF is null)")." 
				   			and KPI_YEAR='$KPI_YEAR' and ($where_DEPARTMENT_ID)
				   order by 	$order_str ";
		$count_data = $db_dpis->send_cmd($cmd);
//		echo $cmd;
//		$db_dpis->show_error();
		if($count_data){
			echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"label_normal\">";		
			while($data = $db_dpis->get_array()){
				$cmd = " select KPI_ID from PER_KPI where KPI_ID_REF=". $data[KPI_ID];
				$count_sub_tree = $db_dpis2->send_cmd($cmd);

				$class = "table_body";
				if($data[KPI_ID] == $sel_kpi_id) $class = "table_body_over";
				$icon_name = "icon_plus.gif";
				$onClick = "add_opened_kpi(". $data[KPI_ID] .");";
				if(in_array($data[KPI_ID], $arr_opened_kpi)){ 
					$icon_name = "icon_minus.gif";
					$onClick = "remove_closed_kpi(". $data[KPI_ID] .");";
				} // end if
				if(!$count_sub_tree) $icon_name = "ball.gif";

				$KPI_TARGET_LEVEL1 = $data[KPI_TARGET_LEVEL1];
				$KPI_TARGET_LEVEL2 = $data[KPI_TARGET_LEVEL2];
				$KPI_TARGET_LEVEL3 = $data[KPI_TARGET_LEVEL3];
				$KPI_TARGET_LEVEL4 = $data[KPI_TARGET_LEVEL4];
				$KPI_TARGET_LEVEL5 = $data[KPI_TARGET_LEVEL5];
				$KPI_TARGET_LEVEL1_DESC = $data[KPI_TARGET_LEVEL1_DESC];
				$KPI_TARGET_LEVEL2_DESC = $data[KPI_TARGET_LEVEL2_DESC];
				$KPI_TARGET_LEVEL3_DESC = $data[KPI_TARGET_LEVEL3_DESC];
				$KPI_TARGET_LEVEL4_DESC = $data[KPI_TARGET_LEVEL4_DESC];
				$KPI_TARGET_LEVEL5_DESC = $data[KPI_TARGET_LEVEL5_DESC];
				$KPI_EVALUATE = $data[KPI_EVALUATE];
				$KPI_RESULT = $data[KPI_RESULT];
				$KPI_TYPE = $data[KPI_TYPE];
				$KPI_DEFINE = $data[KPI_DEFINE];
				$KPI_SCORE_FLAG = $data[KPI_SCORE_FLAG];

				$TMP_ORG_ID = $data[ORG_ID];
				$cmd = " select ORG_SHORT, ORG_NAME from PER_ORG where ORG_ID='$TMP_ORG_ID' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_ORG_NAME = trim($data2[ORG_SHORT]);	
				if (!$TMP_ORG_NAME) $TMP_ORG_NAME = trim($data2[ORG_NAME]);	
				if ($TMP_ORG_NAME=="-") $TMP_ORG_NAME = ""; 
		
/*				
				if($KPI_EVALUATE >= 0 && $KPI_EVALUATE <= $KPI_TARGET_LEVEL1) $KPI_IMG = "images/ball_red.gif";
				elseif($KPI_EVALUATE > $KPI_TARGET_LEVEL1 && $KPI_EVALUATE <= $KPI_TARGET_LEVEL2) $KPI_IMG = "images/ball_orange.gif";
				elseif($KPI_EVALUATE > $KPI_TARGET_LEVEL2 && $KPI_EVALUATE <= $KPI_TARGET_LEVEL3) $KPI_IMG = "images/ball_yellow.gif";
				elseif($KPI_EVALUATE > $KPI_TARGET_LEVEL3 && $KPI_EVALUATE <= $KPI_TARGET_LEVEL4) $KPI_IMG = "images/ball_green_light.gif";
				elseif($KPI_EVALUATE > $KPI_TARGET_LEVEL4 && $KPI_EVALUATE <= $KPI_TARGET_LEVEL5) $KPI_IMG = "images/ball_green.gif";
				elseif($KPI_EVALUATE > $KPI_TARGET_LEVEL5) $KPI_IMG = "images/ball_green.gif";
*/				
				switch($KPI_EVALUATE){
					case 1 :
						$KPI_IMG = "images/ball_red.gif";
						break;
					case 2 :
						$KPI_IMG = "images/ball_orange.gif";
						break;
					case 3 :
						$KPI_IMG = "images/ball_yellow.gif";
						break;
					case 4 :
						$KPI_IMG = "images/ball_green_light.gif";
						break;
					case 5 :
						$KPI_IMG = "images/ball_green.gif";
						break;
					default :
						$KPI_IMG = "images/space.gif";
				} // end switch case

				echo "<tr>";
				echo "<td width=\"15\" align=\"center\" valign=\"top\"><img src=\"images/$icon_name\" width=\"9\" height=\"9\" onClick=\"$onClick\" style=\"cursor:hand;\" vspace=\"5\"></td>";
				if ($SESS_ORG_ID)
					echo "<td class=\"$class\" height=\"22\">&nbsp;<img src=\"$KPI_IMG\" width=\"11\" height=\"11\" hspace=\"4\"><span onClick=\"select_kpi(". $data[KPI_ID] .",". ($data[KPI_ID_REF] + 0) .");\" style=\"cursor:hand\">" . $data[KPI_NAME] . "</span></td>";
				else
					echo "<td class=\"$class\" height=\"22\">&nbsp;<img src=\"$KPI_IMG\" width=\"11\" height=\"11\" hspace=\"4\"><span onClick=\"select_kpi(". $data[KPI_ID] .",". ($data[KPI_ID_REF] + 0) .");\" style=\"cursor:hand\">" . $data[KPI_NAME] . " <br><b>" . $TMP_ORG_NAME . "</b></span></td>";
				echo "</tr>";
				if($count_sub_tree && in_array($data[KPI_ID], $arr_opened_kpi)){
					echo "<tr>";
					echo "<td width=\"15\" align=\"center\"></td>";
					echo "<td>";
					$display = "none";
					if(in_array($data[KPI_ID], $arr_opened_kpi)) $display = "block";
					echo "<div id=\"DIV_". $data[KPI_ID] ."\" style=\"display:$display\">";
					list_tree_kpi("", $data[KPI_ID], $sel_kpi_id, ($tree_depth + 1));
					echo "</div>";
					echo "</td>";
					echo "</tr>";
				} // end if
			} // end while						
			echo "</table>";
		} // end if
	} // function

	function delete_kpi($KPI_ID, $KPI_ID_REF){
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_KPI;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		$cmd = " select KPI_ID, KPI_ID_REF from PER_KPI where KPI_ID_REF=$KPI_ID ";
		$count_child = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		if($count_child){
			while($data = $db_dpis->get_array()){
				delete_kpi($data[KPI_ID], $data[KPI_ID_REF]);
			} // end while
		} // end if

		$cmd = " delete from PER_KPI where KPI_ID=$KPI_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$LIST_OPENED_KPI = str_replace(",$KPI_ID,", ",", $LIST_OPENED_KPI);
		
		return;
	} // function

	function update_parent_evaluate($KPI_ID, $KPI_ID_REF){
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $KPI_YEAR;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		if(!$KPI_ID_REF){
			return;
		}else{
			$cmd = " select KPI_ID_REF from PER_KPI where KPI_ID=$KPI_ID_REF ";
			$db_dpis->send_cmd($cmd);
//			echo "select parent : $cmd <br>";
			$data = $db_dpis->get_array();			
			$KPI_ID = $KPI_ID_REF;
			$KPI_ID_REF = $data[KPI_ID_REF];

			$cmd = " select SUM(KPI_EVALUATE) as SUM_KPI_EVALUATE, COUNT(KPI_ID) as COUNT_KPI_CHILD from PER_KPI where KPI_ID_REF=$KPI_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$SUM_KPI_EVALUATE = $data[SUM_KPI_EVALUATE] + 0;
			$COUNT_KPI_CHILD = $data[COUNT_KPI_CHILD] + 0;
			
			$KPI_EVALUATE = "NULL";
			if($SUM_KPI_EVALUATE > 0 && $COUNT_KPI_CHILD > 0) $KPI_EVALUATE = floor($SUM_KPI_EVALUATE / $COUNT_KPI_CHILD);

			$cmd = " update PER_KPI set KPI_EVALUATE=$KPI_EVALUATE where KPI_ID=$KPI_ID ";
			$db_dpis->send_cmd($cmd);
//			echo "update parent : $cmd <br>";

			update_parent_evaluate($KPI_ID, $KPI_ID_REF);
		} // end if
	} // function
?>