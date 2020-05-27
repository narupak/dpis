<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if (!$LIST_OPENED_ORG) {
		$LIST_OPENED_ORG = ",$SESS_MINISTRY_ID.0.1,$SESS_DEPARTMENT_ID.0.$SESS_MINISTRY_ID,";
		$LIST_OPENED_ORG = "$LIST_OPENED_ORG".($SESS_ORG_ID ? "$SESS_ORG_ID.0.$SESS_DEPARTMENT_ID," : "");
		$LIST_OPENED_ORG = "$LIST_OPENED_ORG".($SESS_PROVINCE_CODE ? "$SESS_PROVINCE_CODE.0.$SESS_ORG_ID," : "");
	}
	if (!$ORG_ID)  {
		if ($SESS_ORG_ID) $ORG_ID=$SESS_ORG_ID;
		elseif ($SESS_DEPARTMENT_ID) $ORG_ID=$SESS_DEPARTMENT_ID;
		elseif ($SESS_MINISTRY_ID) $ORG_ID=$SESS_MINISTRY_ID;
		else $ORG_ID=0;
	}

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	$cmd = " select CTRL_TYPE, PV_CODE, ORG_ID from PER_CONTROL ";
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$CTRL_TYPE = $data[CTRL_TYPE];
	if(!$CTRL_TYPE) $CTRL_TYPE = 4;

  	if($CTRL_TYPE==1 || $CTRL_TYPE==2) { 
		unset($AUTH_CHILD_ORG);
	
		if($CTRL_TYPE==2){
			$AUTH_CHILD_ORG[] = $SESS_DEPARTMENT_ID;
			list_child_org($SESS_DEPARTMENT_ID);
		}elseif($CTRL_TYPE==3 || $SESS_USERGROUP_LEVEL==3){
			$AUTH_CHILD_ORG[] = $SESS_MINISTRY_ID;
			list_child_org($SESS_MINISTRY_ID);
		}elseif($CTRL_TYPE==4 || $SESS_USERGROUP_LEVEL==4){
			$AUTH_CHILD_ORG[] = $SESS_DEPARTMENT_ID;
			list_child_org($SESS_DEPARTMENT_ID);
		}elseif($CTRL_TYPE==5 || $SESS_USERGROUP_LEVEL==5){
			$AUTH_CHILD_ORG[] = $SESS_ORG_ID;
			list_child_org($SESS_ORG_ID);
		} // end if
	
		switch($SESS_USERGROUP_LEVEL){
			case 1 :
				$START_ORG_ID = 1;
				break;
			case 2 :
				$START_ORG_ID = 1;
				break;
			case 3 :
				$START_ORG_ID = $SESS_MINISTRY_ID;
				break;
			case 4 :
				$START_ORG_ID = $SESS_DEPARTMENT_ID;
				break;
			case 5 :
				$START_ORG_ID = $SESS_ORG_ID;
				break;
		} // end switch case
	} else {
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=ORG_ID ";
		$HAVE_ID_1 = $db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$START_ORG_ID = $data[ORG_ID];
	}

	if(!$ORG_ID && !$ORG_ID_REF){
		$ORG_ID = $START_ORG_ID;
		$ORG_ID_REF = $START_ORG_ID;
	} // end if

	if(!$ORG_ID && !$ORG_ID_REF){
		$ORG_ID = 1;
		$ORG_ID_REF = 1;
	} // end if

	if($ORG_ID && !$ORG_ID_REF)	{
		if($DPISDB=="odbc"){
			$cmd = " select top 1 ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select top 1 ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID ";
		} // end if
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$ORG_ID_REF = $data[ORG_ID_REF];
	} // end if

	$cmd = " select		ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OP_CODE, OT_CODE, OS_CODE, ORG_JOB,
						ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, CT_CODE, PV_CODE, AP_CODE, ORG_DATE, ORG_ACTIVE,
						ORG_SEQ_NO, ORG_WEBSITE, ORG_ENG_NAME, POS_LAT, POS_LONG, ORG_DOPA_CODE
					from		PER_ORG
					 where		ORG_ID=$ORG_ID ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
//	echo "Data :: <pre>"; print_r($data); echo "</pre>";
	$ORG_CODE = $data[ORG_CODE];
	$ORG_NAME = $data[ORG_NAME];
	$ORG_SHORT = $data[ORG_SHORT];
	$ORG_ENG_NAME = $data[ORG_ENG_NAME];
	$ORG_JOB = str_replace("\r", "", str_replace("\n", "", $data[ORG_JOB]));
	$ORG_ADDR1 = $data[ORG_ADDR1];
	$ORG_ADDR2 = $data[ORG_ADDR2];
	$ORG_ADDR3 = $data[ORG_ADDR3];
	$ORG_ACTIVE = $data[ORG_ACTIVE];
	$ORG_SEQ_NO = $data[ORG_SEQ_NO];
	$ORG_WEBSITE = $data[ORG_WEBSITE];
	$POS_LAT = $data[POS_LAT];
	$POS_LONG = $data[POS_LONG];
	$ORG_DOPA_CODE = $data[ORG_DOPA_CODE];

	$OL_CODE = $data[OL_CODE];
	$OL_NAME = "";
	if($OL_CODE){
		$cmd = " select OL_NAME from PER_ORG_LEVEL where OL_CODE='$OL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OL_NAME = $data2[OL_NAME];
	} // end if

	$OP_CODE = $data[OP_CODE];
	$OP_NAME = "";
	if($OP_CODE){
		$cmd = " select OP_NAME from PER_ORG_PROVINCE where ST_CODE='$OP_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OP_NAME = $data2[OP_NAME];
	} // end if

	$OT_CODE = $data[OT_CODE];
	$OT_NAME = "";
	if($OT_CODE){
		$cmd = " select OT_NAME from PER_ORG_TYPE where OT_CODE='$OT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OT_NAME = $data2[OT_NAME];
	} // end if

	$OS_CODE = $data[OS_CODE];
	$OS_NAME = "";
	if($OS_CODE){
		$cmd = " select OS_NAME from PER_ORG_STAT where OS_CODE='$OS_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OS_NAME = $data2[OS_NAME];
	} // end if

	$CT_CODE = $data[CT_CODE];
	$CT_NAME = "";
	if($CT_CODE){
		$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CT_NAME = $data2[CT_NAME];
	} // end if

	$PV_CODE = $data[PV_CODE];
	$PV_NAME = "";
	if($PV_CODE){
		$cmd = " select PV_NAME from PER_PROVINCE where CT_CODE='$CT_CODE' and PV_CODE='$PV_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PV_NAME = $data2[PV_NAME];
	} // end if

	$AP_CODE = $data[AP_CODE];
	$AP_NAME = "";
	if($AP_CODE){
		$cmd = " select AP_NAME from PER_AMPHUR where PV_CODE='$PV_CODE' and AP_CODE='$AP_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$AP_NAME = $data2[AP_NAME];
	} // end if
	
	$ORG_DATE = trim($data[ORG_DATE]);
	if($ORG_DATE){
		$arr_temp = explode("-", $ORG_DATE);
		$ORG_DATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
	} // end if
	$NEW_CT_CODE = "140";
	$NEW_CT_NAME = "ไทย";

	function list_tree_org ($pre_image, $org_parent, $sel_org_id, $tree_depth) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_ORG, $START_ORG_ID, $PAGE_ROWS, $PAGE_TABS, $ADD_FLAG, $DPISDB, $ORG_SEARCH, $stext;
		
		$opened_org = substr($LIST_OPENED_ORG, 1, -1);
//		echo "opened>>$opened_org<br>";
//		echo "add_flag>>$ADD_FLAG<br>";
//		$arr_add_flag = explode(",", $ADD_FLAG);
		// เพิ่มเติมโดย พงษ์ศักดิ์  28 ธ.ค. 2009
		$arr_temp1 = explode(",", $opened_org);
		$arr_opened_org = $arr_temp1;
		$arr_num_page = $arr_temp1;
		$arr_parent_org = $arr_temp1;
		$search_org = explode(",", $ORG_SEARCH);
		if ($sel_org_id == $search_org[0]) {
			$search_text = $search_org[1];
		} else {
			$search_text = "";
		}
		$parent_page=0;
		for($k=0; $k < count($arr_temp1); $k++) {
			$arr_temp2 = explode(".", $arr_temp1[$k]);
			$arr_opened_org[$k] = $arr_temp2[0];
			$arr_num_page[$k] = $arr_temp2[1];
			$arr_parent_org[$k] = $arr_temp2[2];
//			echo "$k>>$arr_num_page[$k],";
			if ($arr_opened_org[$k] == $org_parent) {
				$parent_page = $arr_num_page[$k];
			}
		}
		// เพิ่มเติมโดย พงษ์ศักดิ์  28 ธ.ค. 2009

		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

		$cmd = " select ORG_ID , ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID_REF = $org_parent and ORG_ID <> $START_ORG_ID 
						order by ORG_ACTIVE DESC, ORG_SEQ_NO, ORG_CODE ";
		$count_data = $db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		if($count_data){
			echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"label_normal\">";	
			// เพิ่มเติมโดย พงษ์ศักดิ์  29 ธ.ค. 2009
			if ($parent_page > 0) {
				if ($DPISDB=="oci8") {
					if (search_text.length > 0) {
						$cmd1= "SELECT * FROM (SELECT r.*, ROWNUM as row_number FROM ($cmd ) r) WHERE ORG_NAME = '$search_text%' ORDER BY row_number";
						$count_data1 = $db_dpis2->send_cmd($cmd1);
						if ($count_data1 > 0) {
							$data1 = $db_dpis2->get_array();
							$myrow=$data1[row_number];
							$parent_page = floor($myrow / $PAGE_ROWS) + 1;
						}
					}
					$first_rec = (($parent_page - 1) * $PAGE_ROWS);
					$end_rec = $first_rec + $PAGE_ROWS - 1;
					$cmd1= "SELECT * FROM (SELECT r.*, ROWNUM as row_number FROM ($cmd ) r WHERE ROWNUM <= $end_rec) WHERE $first_rec <= row_number";
					$count_data1 = $db_dpis->send_cmd($cmd1);
				} else {
					$first_rec = (($parent_page - 1) * $PAGE_ROWS) ;
					$data = $db_dpis->get_data_row($first_rec);
					$rec_cnt = 0;
				}
			}
			// เพิ่มเติมโดย พงษ์ศักดิ์  29 ธ.ค. 2009
			while($data = $db_dpis->get_array()) {
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=". $data[ORG_ID];
//				echo "$cmd<br>";
				$count_sub_tree = $db_dpis2->send_cmd($cmd);
				$class = "table_body";
				if ($data[ORG_ID] == $sel_org_id) $class = "table_body_over";
				// เพิ่มเติมโดย พงษ์ศักดิ์  27 ธ.ค. 2009
				// ทำรายการข้อความเพื่อการค้นหา
//				echo "$data[ORG_ID] in $arr_add_flag[0],$arr_add_flag[1],$arr_add_flag[2],$arr_add_flag[3],$arr_add_flag[4],";
//				if (in_array($data[ORG_ID], $arr_add_flag)) {
//					$stext = "$data[ORG_ID]"; 	// $searchText ใช้เพื่อเป็นข้อความในการค้นหา
//				} else {
//					$stext = "";
//				}

				$inArr = in_array($data[ORG_ID], $arr_opened_org); 	// เพื่อตรวจสอบว่าเป็นโครงสร้างที่เปิดเอาไว้
				$arr_pos = array_search($data[ORG_ID], $arr_opened_org);	// บอกตำแหน่ง ข้อมูลโครงสร้าง ที่เปิดเอาไว้

				// เพื่อกำหนดหน้า ในการทำรายการ แบ่งหน้า
				if ($data[ORG_ID] == $search_org[0] && $search_org[1].length > 0) {
					$this_page = $parent_page;
				} else {
					if (!$inArr) $this_page=0; else $this_page = $arr_num_page[$arr_pos];
					if (!is_null($PAGE_ROWS) && $count_sub_tree > $PAGE_ROWS) {
						if ($this_page == 0) $this_page = 1;
					} if (is_null($PAGE_ROWS)) {
						$this_page = 0;
					}
				} // end if ($data[ORG_ID] == $search_org[0] && $search_org[1].length > 0) {
				// กำหนด function เพื่อ เปิด/ปิด ระดับลึกลงไป
				$icon_name = "icon_plus.gif";
				$onClick = "add_opened_org($data[ORG_ID], $this_page, $org_parent);";
//				echo "$onClick";
				if ($inArr) { 
					$icon_name = "icon_minus.gif";
					$onClick = "remove_closed_org(". $data[ORG_ID] .");";
				} // end if
				// กำหนด
				$showpage = "";
				if ($count_sub_tree > $PAGE_ROWS && $inArr) {
					// ทำรายการการแบ่งหน้า
					$tpages = floor($count_sub_tree / $PAGE_ROWS) + ($count_sub_tree % $PAGE_ROWS > 0 ? 1 : 0);
					$showpage=" ";
					$stpage=(floor(($this_page-1) / $PAGE_TABS)*$PAGE_TABS)+1; // หาเลขเริ่มแต่ละชุดหน้า
//					echo "stpage=$stpage, this_page=$this_page, PAGE_TABs=$PAGE_TABS<br>";
					for ($ii=$stpage; $ii < $stpage+$PAGE_TABS; $ii++) {
						if ($ii <= $tpages) {
							if ($ii == $this_page) {
								$showpage = $showpage."<b><a href=\"javascript:refresh_opened_org(". $data[ORG_ID] .",".$ii.")\">$ii</a></b> ";
							} else {
								$showpage = $showpage."<a href=\"javascript:refresh_opened_org(". $data[ORG_ID] .",".$ii.")\">$ii</a> ";
							}
						} else {
							break;
						}
					} // end for
					if ($stpage > 1 && $tpages > $PAGE_TABS) {
						$ppage = $stpage - $PAGE_TABS;
						if ($ppage < 0) { $ppage=1; }
						$showpage = "<b><a href=\"javascript:refresh_opened_org(". $data[ORG_ID] .",".$ppage.")\">Prev</a></b>&nbsp;&nbsp; ".$showpage;
					}
					if ($stpage + $PAGE_TABS <= $tpages && $tpages > $PAGE_TABS) {
						$npage = $stpage + $PAGE_TABS;
						$showpage = $showpage." &nbsp;&nbsp;<b><a href=\"javascript:refresh_opened_org(". $data[ORG_ID] .",".$npage.")\">Next</a></b>";
					}
					if ($showpage > " ") {
						$showpage = $showpage." .../$tpages";
					}
//					echo "**$showpage";
				} // end if
				// จบการทำรายการแบ่งหน้า
				if(!$count_sub_tree) $icon_name = "";
				echo "<tr>";
				echo "<td width=\"15\" align=\"center\"><img src=\"images/$icon_name\" width=\"9\" height=\"9\" onClick=\"$onClick\" style=\"cursor:hand;\"></td>";
				echo "<td class=\"$class\" height=\"22\">&nbsp;<span onClick=\"select_org(". $data[ORG_ID] .",". $data[ORG_ID_REF] .");\" style=\"cursor:hand\">" . $data[ORG_NAME] . "</span></td>";
				echo "</tr>";
				// เพิ่มรายการแบ่งหน้าใต้แถวที่เปิดแบ่งหน้าเอาไว้
				if ($showpage > "") {
					$searchformname="search_form	$this_page";
					$search_part="<form name=\"$searchformname\" method=\"post\" action=\"structure_by_law.html\" enctype=\"multipart/form-data\"><input type=\"text\" name=\"stext\" value=\"$search_org[1]\"><input name=\"orgsearch\" type=\"submit\" class=\"button\" value=\"ค้นหา\">";
					echo "<tr><td width=\"15\"></td><td height=\"14\" align=\"left\">&nbsp;$showpage&nbsp&nbsp$search_part</td></tr>";
				}
				// เพิ่มเติมโดย พงษ์ศักดิ์  27 ธ.ค. 2009

/*
				if($count_sub_tree > 0 && $inArr){
					echo "<tr>";
					echo "<td width=\"15\" align=\"center\"></td>";
					echo "<td>";
					$display = "none";
					if(in_array($data[ORG_ID], $arr_opened_org)) $display = "block";
					echo "<div id=\"DIV_". $data[ORG_ID] ."\" style=\"display:$display\">";
					list_tree_org("", $data[ORG_ID], $sel_org_id, ($tree_depth + 1));
					// เพิ่มรายการแบ่งหน้าใต้กลุ่มรายการที่เปิดแบ่งหน้าเอาไว้ เป็นการปิดท้าย
					if ($showpage > "") {
						echo "<tr><td width=\"15\"></td><td height=\"14\" align=\"left\">&nbsp;$showpage</td></tr>";
					}
					// จบการแบ่งหน้า
					echo "</div>";
					echo "</td>";
					echo "</tr>";
				} // end if
*/
				$rec_cnt++;
				if ($parent_page > 0 && $rec_cnt >= $PAGE_ROWS) {
					break;
				}
			} // end while						
			echo "</table>";
		} // end if
	} //function list_tree_org ($pre_image, $org_parent, $sel_org_id, $tree_depth) {

	function delete_org($ORG_ID, $ORG_ID_REF){
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_ORG, $START_ORG_ID;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		$cmd = " select ORG_ID, ORG_ID_REF from PER_ORG where ORG_ID_REF=$ORG_ID and ORG_ID<>$START_ORG_ID ";
		$count_child = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		if($count_child){
			while($data = $db_dpis->get_array()){
				delete_org($data[ORG_ID], $data[ORG_ID_REF]);
			} // end while
		} // end if

		$cmd = " delete from PER_ORG where ORG_ID=$ORG_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$LIST_OPENED_ORG = str_replace(",$ORG_ID,", ",", $LIST_OPENED_ORG);
		
		return;
	} // function

	function list_child_org ($org_parent) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $AUTH_CHILD_ORG;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$org_parent ";
		$count_data = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		if($count_data){
			while($data = $db_dpis->get_array()){
				$AUTH_CHILD_ORG[] = $data[ORG_ID];
				list_child_org($data[ORG_ID]);
			} // end while
		} // end if
	} // function
?>