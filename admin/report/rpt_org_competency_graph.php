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

	$arr_content = (array) null;

	if ($DEPARTMENT_ID == "total") {
		$txt_dept = "";
	} else {
		$txt_dept = "and DEPARTMENT_ID=$DEPARTMENT_ID";
		if ($ORG_ID <> $DEPARTMENT_ID) {
			$txt_dept = $txt_dept." and ORG_ID=$ORG_ID";
		}
	}
	$cmd = "  select *	from PER_KPI_FORM 
					where KF_CYCLE=$KF_CYCLE and KF_START_DATE='$KF_START_DATE' $txt_dept ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	while ($data = $db_dpis->get_array()) {
		if (!$KF_YEAR) {
			$KF_YEAR = substr($data[KF_END_DATE], 0, 4) + 543;
		}
		$KF_ID = $data[KF_ID];
		$cmd1 = " select * from PER_COMPETENCY_FORM where KF_ID=$KF_ID and CF_STATUS=1 ORDER BY CF_ID ";
		$db_dpis2->send_cmd($cmd1);
		while ($data2 = $db_dpis2->get_array()) {
			$CF_SCORE = $data2[CF_SCORE];
			$ARR_POINT = explode(",",$CF_SCORE);
			for($score_i = 0; $score_i < count($ARR_POINT); $score_i++) {
				$POINT_K = explode(":",$ARR_POINT[$score_i]);
				$CP_CODE = $POINT_K[0];
				$POINT = $POINT_K[1];
//				echo "$CP_CODE:$POINT<br>";
				$arr_content[$CP_CODE][$POINT]++;
			} //  end for $score_i
		} // end while read PER_COMPETENCY_FORM
	} // end while read PER_KPI_FORM
	
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
	$report_title = "ตารางแสดงจำนวนผู้ประเมินแยกตามสมรรถนะและคะแนน ของข้าราชการ $head_dept";
	$company_name = "ประจำรอบการประเมินครั้งที่ $KF_CYCLE พ.ศ. $KF_YEAR ";
	$report_code = "R_ORG_Competency";

	$arr_categories = (array) null;
	$arr_series_caption_data = (array) null;
	$arr_series_list	 = (array) null;
	
	if(count($arr_content) > 0) {
		$ctot = (array) null;
		$gtotal=0;
		$i = 0;
		foreach($arr_content as $CP_CODE => $val) {
//			echo "CP_CODE=$CP_CODE<br>";
			$cmd1 = " select * from PER_COMPETENCE where CP_CODE=$CP_CODE ";
			$db_dpis2->send_cmd($cmd1);
			$data2 = $db_dpis2->get_array();
			$CP_NAME = $data2[CP_NAME];
			$arr_categories[$i] = $CP_NAME;
			$cnt_col=1;
			$tot = 0;
			for($p=1; $p <= 5; $p++) {
				$cnt_col++;
				$val1 = (!$val[$p] ? "-" : $val[$p]);
				$arr_series_caption_data[$p-1][$i] = $val1;
				$ctot[$p]=$ctot[$p]+(!$val[$p] ? 0 : $val[$p]);
				$tot=$tot+(!$val[$p] ? 0 : $val[$p]);
				$gtotal=$gtotal+(!$val[$p] ? 0 : $val[$p]);
			} // end for
			// show total $cnt_col+1, $tot;
			$i++;
		} // end for
		// "รวม" $ctot[1] $ctot[2] $ctot[3] $ctot[4] $ctot[5] $gtotal
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