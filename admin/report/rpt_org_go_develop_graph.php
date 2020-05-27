<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$KF_CYCLE = $R_KF_CYCLE;
	$KF_START_DATE = $R_KF_START;
	$DEPARTMENT_ID = $R_DEPARTMENT_ID;
	$ORG_ID = $R_ORG_ID;
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$arr_content = (array) null;
	$arr_target = (array) null;
	$arr_percent = (array) null;
	$arr_cftype = (array) null;
	$arr_N = (array) null;

	if ($DEPARTMENT_ID == "total") {
		$txt_dept = "";
	} else {
		$txt_dept = "and DEPARTMENT_ID=$DEPARTMENT_ID";
		if ($ORG_ID <> $DEPARTMENT_ID) {
			$txt_dept = $txt_dept." and ORG_ID=$ORG_ID";
		}
	}
	$cmd = "  select  a.*, b.*, b.KF_ID as CF_KF_ID  from PER_KPI_FORM a, PER_COMPETENCY_FORM b
					where KF_CYCLE=$KF_CYCLE and KF_START_DATE='$KF_START_DATE' $txt_dept and PER_ID=CF_PER_ID
					order by CF_PER_ID, CF_TYPE ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$arr_cftype = (array) null;
	while ($data = $db_dpis->get_array()) {
		if (!$KF_YEAR) {
			$KF_YEAR = substr($data[KF_END_DATE], 0, 4) + 543;
		}
		if ($CF_PER_ID != $data[CF_PER_ID]) {
			$CF_PER_ID = $data[CF_PER_ID];
			// อ่านระดับของผู้ถูกประเมิน
			$cmd1 = " select 	LEVEL_NO from PER_PERSONAL where PER_ID=$CF_PER_ID ";
			$db_dpis2->send_cmd($cmd1);
			$data2 = $db_dpis2->get_array();
			$CF_LEVEL_NO = trim($data2[LEVEL_NO]);
		}
		$CF_TYPE = $data[CF_TYPE];
		// รายงานนี้แสดงยอดสรุปการประเมินตามผู้ถูกประเมิน ความหมายจึงเป็นตรงกันข้ามดังนี้ 
		//															1 ตนเองประเมินตนเอง 2 ผู้ใต้บังคับบัญชาประเมิน 3 เพื่อนประเมิน 4 ผู้บังคับบัญชาประเมิน
		// จากที่เห็นจึงต้องเปลี่ยน $CF_TYPE เป็นตรงกันข้าม ซึ่งมีที่ต้องสลับ 2 ตัวคือค่า 2 เป็น 4 และ 4 เป็น 2 รายงานจะจะลงถูกความหมาย
		if ($CF_TYPE==2) { $CF_TYPE=4; } else if ($CF_TYPE==4) { $CF_TYPE=2; }
		// จัดเตรียมค่า cf_type array เพื่อการอ่าน Percent น้ำหนัก
		if (!in_array($CF_TYPE, $arr_cftype)) {
			$arr_cftype[] = $CF_TYPE;
		}
		if ($CF_TYPE == 1) { $CF_KF_ID = $data[CF_KF_ID]; }
			
//		$CF_SCORE = $data2[CF_SCORE];
		$CF_SCORE = $data[CF_SCORE];
		$ARR_POINT = explode(",",$CF_SCORE);
		for($score_i = 0; $score_i < count($ARR_POINT); $score_i++) {
			$POINT_K = explode(":",$ARR_POINT[$score_i]);
			$CP_CODE = $POINT_K[0];
			$POINT = $POINT_K[1];
//			echo "$CP_CODE:$POINT<br>";

//			if (!$arr_target[$CF_PER_ID][$CP_CODE]) {
				$cmd2 = " select PC_TARGET_LEVEL, KC_EVALUATE, KC_WEIGHT from PER_KPI_COMPETENCE 
								  where KF_ID=$CF_KF_ID and CP_CODE='$CP_CODE' ";
				$db_dpis3->send_cmd($cmd2);
//				$db_dpis3->show_error(); 
				$data3 = $db_dpis3->get_array();
				$PC_TARGET_LEVEL = $data3[PC_TARGET_LEVEL];
				$KC_EVALUATE = $data3[KC_EVALUATE];
//			}

			$arr_target[$CF_PER_ID][$CP_CODE] = $PC_TARGET_LEVEL;
//			$arr_content[$CF_PER_ID][$CP_CODE][$CF_TYPE] = $arr_content[$CF_PER_ID][$CP_CODE][$CF_TYPE] + $POINT;
//			$arr_N[$CF_PER_ID][$CP_CODE][$CF_TYPE]++;
			$arr_content[$CF_PER_ID][$CP_CODE] = $KC_EVALUATE;
//			echo "weight[$CF_PER_ID][$CP_CODE]="+$KC_EVALUATE+"<br>";
//			echo "[$CF_PER_ID][$CP_CODE][$CF_TYPE]<br>";
		} //  end for $score_i
		// เรียงชุดของ cftype เพื่อไปต่อทำเป็น key ในการเรียก per_pos_type
		array_multisort($arr_cftype, SORT_ASC);
		//	echo implode("",$arr_cftype)+"|";
		$postype=substr($CF_LEVEL_NO,0,1).implode("",$arr_cftype);
		//	echo "LEVEL_NO=$CF_LEVEL_NO<br>";
		$cmd1 = " SELECT * FROM PER_POS_TYPE WHERE POS_TYPE='$postype' ";
		$db_dpis2->send_cmd($cmd1);
		//	$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$arr_percent[$CF_PER_ID][1] = $data2[SEFT_RATIO];
		$arr_percent[$CF_PER_ID][2] = $data2[CHIEF_RATIO];
		$arr_percent[$CF_PER_ID][3] = $data2[FRIEND_RATIO];
		$arr_percent[$CF_PER_ID][4] = $data2[SUB_RATIO];
	} // end while read PER_KPI_FORM
	
	if(count($arr_content) > 0) {	
		$arr_per_gap = (array) null;;
		$arr_per_n = (array) null;;
		foreach($arr_content as $I_PER_ID => $val) {
			foreach($val as $CP_CODE => $val1) {
				$cnt_col=1;
				$tot = 0;
				// gap ผลค่าง ระหว่าง เป้าหมายตามการประเมินรายบุคคล กับ % คะแนนรวมแต่ละบุคคล ทำเป๋น + แล้วปัดเศษออก
				$gaptemp = $arr_content[$I_PER_ID][$CP_CODE] - $arr_target[$I_PER_ID][$CP_CODE]; 
				if ($gaptemp < 0) {
					$weight_int = ceil($arr_content[$I_PER_ID][$CP_CODE]);
					$gap = ($weight_int == $arr_content[$I_PER_ID][$CP_CODE] ? $weight_int+1 : $weight_int);
					$arr_per_gap[$CP_CODE][$gap]++;
//					echo "[$CP_CODE][$gap]".$arr_per_gap[$CP_CODE][$gap]."<br>";
				}
			} // end foreach
		} // end foreach $I_PER_ID
	} // if(count($arr_content) > 0)
	
	if ($DEPARTMENT_ID == "total") {
		$head_dept = "";
	} else {
		$cmd1 = " SELECT * FROM PER_ORG WHERE ORG_ID=$DEPARTMENT_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd1);
		//	$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$head_dept = "$data2[ORG_NAME]";
		if ($ORG_ID <> $DEPARTMENT_ID) {
			$cmd1 = " SELECT * FROM PER_ORG WHERE ORG_ID=$ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd1);
		//		$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$head_dept = $head_dept."  $data2[ORG_NAME]";
		}
	}	
	$report_title = "ตารางแสดงจำนวนระดับที่ต้องพัฒนาของบุคคลแยกตามสมรรถนะและระดับ ของข้าราชการ $head_dept";
	$company_name = "ประจำรอบการประเมินครั้งที่ $KF_CYCLE พ.ศ. $KF_YEAR ";
	$report_code = "R_ORG_Go_Develop";

	if(count($arr_per_gap) > 0) {
		$ctot = (array) null;
		$gtotal=0;
		$i = 0;
		foreach($arr_per_gap as $CP_CODE => $val) {
//			echo "CP_CODE=$CP_CODE<br>";
			$cmd1 = " select * from PER_COMPETENCE where CP_CODE=$CP_CODE ";
			$db_dpis2->send_cmd($cmd1);
			$data2 = $db_dpis2->get_array();
			$CP_NAME = $data2[CP_NAME];
			$arr_categories[$i] = $CP_NAME;
			$cnt_col=1;
			$tot = 0;
			for($p=1; $p <= 5; $p++) { // loop ตาม gap
				$cnt_col++;
				$gap_cnt = (!$val[$p] ? 0 : $val[$p]);
				$arr_series_caption_data[$p-1][$i] = $gap_cnt;
				$ctot[$p]=$ctot[$p]+$gap_cnt;
				$tot=$tot+$gap_cnt;
				$gtotal=$gtotal+$gap_cnt;
			} // end for $CF_TYPE
			// $tot;
			$i++;
		} // end foreach
		// "รวม" "$ctot[1]" "$ctot[2]" "$ctot[3]" "$ctot[4]" "$ctot[5]" "$gtotal"
		for($p=0; $p < count($arr_series_caption_data); $p++) {
			$arr_series_list[$p] = implode(";", $arr_series_caption_data[$p])."";
		}

		$chart_title = $report_title;
		$chart_subtitle = $company_name;
		if(!$setWidth){ $setWidth = "$GRAPH_WIDE";}else{ $setWidth = "800";}
		if(!$setHeight){ $setHeight = "$GRAPH_HIGH";}else{$setHeight = "600";}
		$selectedFormat = "SWF";
		$series_caption_list = "ระดับ 1;ระดับ 2;ระดับ 3;ระดับ 4;ระดับ 5";
		$categories_list = implode(";", $arr_categories)."";
		$series_list = implode("|", $arr_series_list);
//		echo "$categories_list[$series_list]<br>";
	} // end if
?>