<?
	include("../../php_scripts/connect_database.php");
	include ("../php_scripts/session_start.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$cmd = " select			a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE,a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID
						from			PER_PERSONAL a, PER_PRENAME b
						 where	a.PN_CODE=b.PN_CODE and PER_TYPE = 1 and PER_STATUS = 1 ";	
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	while ($data = $db_dpis->get_array()) {

//		read template content
		$filename = "rpt_personal_salaryhis_template.rtf";
		$handle = fopen ($filename, "r");
		$rtf_contents = fread($handle, filesize($filename));
		fclose($handle);
//		echo $rtf_contents;

		$PER_ID = $data[PER_ID];
		$PER_NAME = "$data[PN_NAME]$data[PER_NAME] $data[PER_SURNAME]";
		$PER_STARTDATE = substr($data[PER_STARTDATE], 0, 10);
		$PER_STARTDATE = show_date_format($PER_STARTDATE,$DATE_DISPLAY);

		$LEVEL_NO = trim($data[LEVEL_NO]);
		$cmd = "select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$LEVEL_NO'";
		$db_dpis2->send_cmd($cmd);
		$data2  = $db_dpis2->get_array();
		$LEVEL_NAME2 = $data2[LEVEL_NAME];
	
		$PER_TYPE = $data[PER_TYPE];
		if($PER_TYPE == 1 || $PER_TYPE == 5){
			$position_table = "PER_POSITION";
			$position_join = "a.POS_ID=b.POS_ID";
			$position_no= "a.POS_NO";
			$position_id= "a.POS_ID";
			$line_table = "PER_LINE";
			$line_join = "a.PL_CODE=b.PL_CODE";
			$line_code = "a.PL_CODE";
			$line_name = "b.PL_NAME";
			$POS_ID = $data[POS_ID];
			$type_code ="a.PT_CODE";
			$select_type_code =",a.PT_CODE";
		}elseif($PER_TYPE == 2){
			$position_table = "PER_POS_EMP";
			$position_join = "a.POEM_ID=b.POEM_ID";
			$position_no= "a.POEM_NO";
			$position_id= "a.POEM_ID";			
			$line_table = "PER_POS_NAME";
			$line_join = "a.PN_CODE=b.PN_CODE";
			$line_code = "a.PN_CODE";
			$line_name = "b.PN_NAME";	
			$POS_ID = $data[POEM_ID];
		}elseif($PER_TYPE == 3){
			$position_table = "PER_POS_EMPSER";
			$position_join = "a.POEMS_ID=b.POEMS_ID";
			$position_no= "a.POEMS_NO";
			$position_id= "a.POEMS_ID";			
			$line_table = "PER_EMPSER_POS_NAME";
			$line_join = "a.EP_CODE=b.EP_CODE";
			$line_code = "a.EP_CODE";
			$line_name = "b.EP_NAME";	
			$POS_ID = $data[POEMS_ID];
		}elseif($PER_TYPE == 4){
			$position_table = "PER_POS_TEMP";
			$position_join = "a.POT_ID=b.POT_ID";
			$position_no= "a.POT_NO";
			$position_id= "a.POT_ID";			
			$line_table = "PER_TEMP_POS_NAME";
			$line_join = "a.TP_CODE=b.TP_CODE";
			$line_code = "a.TP_CODE";
			$line_name = "b.TP_NAME";	
			$POS_ID = $data[POT_ID];
		} // end if
		$cmd = "	select		$position_no as POS_NO, $line_name as PL_NAME, a.ORG_ID, c.ORG_NAME, c.ORG_ID_REF $select_type_cod
						from			$position_table a, $line_table b, PER_ORG c
						where		$position_id=$POS_ID and $line_join and a.ORG_ID=c.ORG_ID ";
		if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POS_NO = trim($data2[POS_NO]);
		$PL_NAME = trim($data2[PL_NAME]);
		$ORG_ID = $data2[ORG_ID];
		$ORG_NAME = trim($data2[ORG_NAME]);
		$DEPARTMENT_ID = $data2[ORG_ID_REF];
		$PL_NAME = trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO)):"";
		$LEVEL_NAME = level_no_format($LEVEL_NAME2);		
		if(trim($type_code)){
			$PT_CODE = trim($data[PT_CODE]);
			$cmd = " select PT_NAME from PER_TYPE where	PT_CODE=".$PT_CODE;
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = trim($data2[PT_NAME]);
		}		
		/*****if($PER_TYPE==1 || $PER_TYPE == 5){
			$cmd = "	select		$position_no as POS_NO, $line_name as PL_NAME, a.ORG_ID, c.ORG_NAME, c.ORG_ID_REF, a.PT_CODE, d.PT_NAME
								from		$position_table a, $line_table b, PER_ORG c, PER_TYPE d
								where		$position_id=$POS_ID and $line_join and a.ORG_ID=c.ORG_ID and a.PT_CODE=d.PT_CODE ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
		
			$POS_NO = trim($data2[POS_NO]);
			$PL_NAME = trim($data2[PL_NAME]);
			$ORG_ID = $data2[ORG_ID];
			$ORG_NAME = trim($data2[ORG_NAME]);
			$DEPARTMENT_ID = $data2[ORG_ID_REF];
			$PT_CODE = trim($data2[PT_CODE]);
			$PT_NAME = trim($data2[PT_NAME]);
			$PL_NAME = trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NAME2) . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"";
			$LEVEL_NAME = (($PT_CODE==11)?"ท.":$PT_NAME) . level_no_format($LEVEL_NAME2);
		}else{ //2 || 3 || 4
			$cmd = "	select		$position_no as POS_NO, $line_name as PL_NAME, a.ORG_ID, c.ORG_NAME, c.ORG_ID_REF
								from		$position_table a, $line_table b, PER_ORG c
								where		$position_id=$POS_ID and $line_join and a.ORG_ID=c.ORG_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POS_NO = trim($data2[POS_NO]);
			$PL_NAME = trim($data2[PL_NAME]);
			$ORG_ID = $data2[ORG_ID];
			$ORG_NAME = trim($data2[ORG_NAME]);
			$DEPARTMENT_ID = $data2[ORG_ID_REF];
			$PL_NAME = trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO)):"";
			$LEVEL_NAME = level_no_format($LEVEL_NAME2);
		} //end if  *****/
	
		$cmd = " select ORG_ID_REF, ORG_NAME  from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DEPARTMENT_NAME = $data2[ORG_NAME];
	
		$MINISTRY_ID = $data2[ORG_ID_REF];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$MINISTRY_NAME = $data2[ORG_NAME];
	
		$cmd = " select SAH_EFFECTIVEDATE, SAH_SALARY_MIDPOINT, SAH_PERCENT_UP, SAH_SALARY_UP, SAH_SALARY, SAH_SALARY_EXTRA 
						  from PER_SALARYHIS where SAH_ID=$SAH_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$SAH_EFFECTIVEDATE = $data2[SAH_EFFECTIVEDATE];
		$SAH_SALARY_MIDPOINT = $data2[SAH_SALARY_MIDPOINT]?number_format($data2[SAH_SALARY_MIDPOINT],2):" ";
		$SAH_PERCENT_UP = $data2[SAH_PERCENT_UP];
		$SAH_SALARY_UP = $data2[SAH_SALARY_UP]?number_format($data2[SAH_SALARY_UP],2):" ";
		$SAH_SALARY = $data2[SAH_SALARY]?number_format($data2[SAH_SALARY],2):" ";
		$SAH_SALARY_EXTRA = $data2[SAH_SALARY_EXTRA]?number_format($data2[SAH_SALARY_EXTRA],2):" ";
		$PER_SALARY = $data2[SAH_SALARY] - $data2[SAH_SALARY_UP] - $data2[SAH_SALARY_EXTRA];
		$PER_SALARY = number_format($PER_SALARY,2);
		$arr_temp = explode("-", $SAH_EFFECTIVEDATE);
		if ($arr_temp[1]=="04") $KF_CYCLE = 1;
		elseif ($arr_temp[1]=="10") $KF_CYCLE = 2;
		if($KF_CYCLE == 1){
			$KF_START_DATE = '01/10/' . ($arr_temp[0] + 543 - 1);
			$KF_END_DATE = '31/03/' . ($arr_temp[0] + 543);
		}elseif($KF_CYCLE == 2){
			$KF_START_DATE = '01/04/' . ($arr_temp[0] + 543);
			$KF_END_DATE = '30/09/' . ($arr_temp[0] + 543);
		} // end if
	
		$rtf_contents = replaceRTF("@DEPARTMENT_NAME@", convert2rtfascii(convert2thaidigit($DEPARTMENT_NAME)), $rtf_contents);
		$rtf_contents = replaceRTF("@KF_CYCLE@", convert2rtfascii(convert2thaidigit($KF_CYCLE)), $rtf_contents);
		$rtf_contents = replaceRTF("@KF_START_DATE@", convert2rtfascii(convert2thaidigit($KF_START_DATE)), $rtf_contents);
		$rtf_contents = replaceRTF("@KF_END_DATE@", convert2rtfascii(convert2thaidigit($KF_END_DATE)), $rtf_contents);
		$rtf_contents = replaceRTF("@PER_NAME@", convert2rtfascii(convert2thaidigit($PER_NAME)), $rtf_contents);
		$rtf_contents = replaceRTF("@PL_NAME@", convert2rtfascii(convert2thaidigit($PL_NAME)), $rtf_contents);
		$rtf_contents = replaceRTF("@POS_NO@", convert2rtfascii(convert2thaidigit($POS_NO)), $rtf_contents);
		$rtf_contents = replaceRTF("@ORG_NAME@", convert2rtfascii(convert2thaidigit($ORG_NAME)), $rtf_contents);
		$rtf_contents = replaceRTF("@MINISTRY_NAME@", convert2rtfascii(convert2thaidigit($MINISTRY_NAME)), $rtf_contents);
		$rtf_contents = replaceRTF("@PER_SALARY@", convert2rtfascii(convert2thaidigit($PER_SALARY)), $rtf_contents);
		$rtf_contents = replaceRTF("@PER_MGTSALARY@", convert2rtfascii(convert2thaidigit($PER_MGTSALARY)), $rtf_contents);
		$rtf_contents = replaceRTF("@SAH_SALARY_MIDPOINT@", convert2rtfascii(convert2thaidigit($SAH_SALARY_MIDPOINT)), $rtf_contents);
		$rtf_contents = replaceRTF("@SAH_PERCENT_UP@", convert2rtfascii(convert2thaidigit($SAH_PERCENT_UP)), $rtf_contents);
		$rtf_contents = replaceRTF("@SAH_SALARY_UP@", convert2rtfascii(convert2thaidigit($SAH_SALARY_UP)), $rtf_contents);
		$rtf_contents = replaceRTF("@SAH_SALARY@", convert2rtfascii(convert2thaidigit($SAH_SALARY)), $rtf_contents);
		$rtf_contents = replaceRTF("@SAH_SALARY_EXTRA@", convert2rtfascii(convert2thaidigit($SAH_SALARY_EXTRA)), $rtf_contents);

		// write rtf content
		//$filename = "../tmp/rpt_personal_salaryhis_$PER_ID.rtf";
		$filename = "../tmp/rpt_personal_salaryhis_sum.rtf";
		//echo "$PER_ID<br>";
		//echo "$filename<br>";
		$handle = fopen ($filename, "a+");	//$handle = fopen ($filename, "r+");	
		//echo $rtf_contents;
		fwrite($handle, $rtf_contents);
		fclose($handle);
	} // loop while

	ini_set("max_execution_time", 30);
?>
	<meta http-equiv='refresh' content='0;URL=<?=$filename?>'>