<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis7 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";
	$report_title = "Rpt_personal_detail";
	
	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_title");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//
	
	
		
		
		
		if($command == "SEARCH") {
			
			$cmd = "select * from (
							   select rownum rnum, q1.* from ( 
									  select 		a.PER_ID, a.PN_CODE, PER_NAME, PER_SURNAME, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO, a.PER_TYPE, a.PER_STATUS, ES_CODE, PL_NAME_WORK, ORG_NAME_WORK, PAY_ID, a.PER_CARDNO, a.DEPARTMENT_ID, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_3, a.ORG_ID_4, a.ORG_ID_5,a.ORG_ID_2, PER_SEQ_NO, PER_FILE_NO, a.DEPARTMENT_ID_ASS
									  from 			PER_PERSONAL a, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, PER_LEVEL f, PER_POS_TEMP g, PER_ORG i
									  where 		$search_con(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+) and 
														a.LEVEL_NO=f.LEVEL_NO(+) and a.POT_ID=g.POT_ID(+) and c.ORG_ID=i.ORG_ID(+) 
														$sen_cmd
							   )  q1
						) ";
			
			
		}else if($command == "SEARCHNAMEHIS"){
			$cmd = "select * from (
							   select rownum rnum, q1.* from ( 
									  select 		a.PER_ID, a.PN_CODE, PER_NAME, PER_SURNAME, NH_NAME, NH_SURNAME, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO, a.PER_TYPE, a.PER_STATUS, ES_CODE, PL_NAME_WORK, ORG_NAME_WORK, PAY_ID, a.PER_CARDNO, a.DEPARTMENT_ID, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, 
                                      a.ORG_ID_3, a.ORG_ID_4, a.ORG_ID_5,PER_SEQ_NO, PER_FILE_NO, a.DEPARTMENT_ID_ASS
									  from 			PER_PERSONAL a, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, PER_LEVEL f, PER_POS_TEMP g, PER_NAMEHIS h, PER_ORG i 
									  where 		$search_con(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+) and 
														a.LEVEL_NO=f.LEVEL_NO(+) and a.POT_ID=g.POT_ID(+) and a.PER_ID=h.PER_ID(+) and c.ORG_ID = i.ORG_ID(+)
														$sen_cmd
							   )  q1
						) ";
			
		}
		
		//echo "<pre>";
		//die($cmd."/".$command);	
		$xlsRow = 1;
		$worksheet->write(0, 5, "$SESS_DEPARTMENT_NAME", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write(1, 5, "รายชื่อข้าราชการ ตำแหน่ง สังกัด", set_format("xlsFmtTitle", "B", "C", "", 1));
		

		$xlsRow ++;
		$worksheet->set_column(0,0, 10);
		$worksheet->set_column(1,1, 25);
		$worksheet->set_column(2,2, 25);
		$worksheet->set_column(3,3, 20);
		$worksheet->set_column(4,4, 25);
		$worksheet->set_column(5,5, 30);
		$worksheet->set_column(6,6, 30);
		$worksheet->set_column(7,7, 30);
		$worksheet->set_column(8,8, 30);
		
	
		
		$worksheet->write(2, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write(3, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write(2, 1, "ชื่อ - สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write(3, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write(2, 2, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write(3, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write(2, 3, "ตำแหน่งประเภท", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write(3, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write(2, 4, "ระดับตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write(3, 4, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write(2, 5, "สำนัก/กอง ตามกฎหมาย", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write(3, 5, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write(2, 6, "ต่ำกว่าสำนัก/กอง 1 ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write(3, 6, "ตามกฎหมาย", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write(2, 7, "สำนัก/กอง ตามมอบหมาย ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write(3, 7, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write(2, 8, "ต่ำกว่าสำนัก/กอง 1 ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write(3, 8, "ตามมอบหมาย", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$xlsRow=4;
		
		$count_page_data = $db_dpis->send_cmd($cmd);
		$num_order = 0;
		while ($data = $db_dpis->get_array()) {
		if ($count_page_data) { 
		$num_order++;
//		echo "**PER_ID=$PER_ID, TMP_PER_ID=$TMP_PER_ID, temp_PER_ID=$temp_PER_ID<br>";
		$temp_PER_ID = trim($data[PER_ID]);
		if ($SESS_DEPARTMENT_NAME=="กรมวิชาการเกษตร")
			$TMP_SEQ_NO = $data[PER_FILE_NO];
		else
		$TMP_SEQ_NO = $data[PER_SEQ_NO];
		$TMP_PER_TYPE =  trim($data[PER_TYPE]);
		$TMP_LEVEL_NO = trim($data[LEVEL_NO]);
		$PER_NAME_SHOW = trim($data[PER_NAME]);
		$PER_SURNAME_SHOW = trim($data[PER_SURNAME]);
		$NH_NAME = trim($data[NH_NAME]);
		$NH_SURNAME = trim($data[NH_SURNAME]);
		$old_name = ($command == "SEARCHNAMEHIS")?$old_name="($NH_NAME $NH_SURNAME)":$old_name="";	
		$FULLNAME = "$PER_NAME_SHOW $PER_SURNAME_SHOW".$old_name;
		$TMP_ES_CODE = trim($data[ES_CODE]);
		$TMP_PL_NAME_WORK = trim($data[PL_NAME_WORK]);
		$TMP_ORG_NAME_WORK = trim($data[ORG_NAME_WORK]);

        $TMP_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
        $TMP_DEPARTMENT_ASS_ID = trim($data[DEPARTMENT_ID_ASS]);
        $TMP_ORG_ASS_ID   = trim($data[ORG_ID]);
        $TMP_ORG_ASS_ID_1 = trim($data[ORG_ID_1]);
        $TMP_ORG_ASS_ID_2 = trim($data[ORG_ID_2]);
        $TMP_ORG_ASS_ID_3 = trim($data[ORG_ID_3]);
        $TMP_ORG_ASS_ID_4 = trim($data[ORG_ID_4]);
        $TMP_ORG_ASS_ID_5 = trim($data[ORG_ID_5]);    

		$TMP_PN_NAME = $TMP_PN_SHORTNAME = "";
		$TMP_PN_CODE = trim($data[PN_CODE]);
		if ($TMP_PN_CODE) {
			$cmd = "	select PN_NAME, PN_SHORTNAME from PER_PRENAME where PN_CODE='$TMP_PN_CODE'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_PN_NAME = $data2[PN_NAME];
			$TMP_PN_SHORTNAME = $data2[PN_SHORTNAME];
		}
				
		$cmd = " select POSITION_LEVEL,POSITION_TYPE from PER_LEVEL where LEVEL_NO='$TMP_LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$TMP_LEVEL_NAME = $data2[POSITION_LEVEL];
		$LV_NAME = trim($data2[POSITION_TYPE]);
		
		$TMP_PER_TYPE = $data[PER_TYPE];
		$TMP_PER_STATUS = $data[PER_STATUS];
		$TMP_POSEM_NO = $TMP_ORG_NAME = $TMP_PL_NAME = $TMP_PM_NAME = $TMP_PAY_NO = "";
		if ($TMP_PER_TYPE == 1 || $TMP_PER_TYPE == 5) {
			if ($command == "SEARCHPAY") {
				$TMP_POS_ID = $data[PAY_ID];
				if ($TMP_POS_ID) {
					$cmd = " 	select		POS_NO_NAME, POS_NO, pl.PL_NAME, po.ORG_NAME, pp.PT_CODE, po.ORG_ID, pp.PM_CODE, pp.ORG_ID_1, pp.ORG_ID_2
										from		PER_POSITION pp, PER_LINE pl, PER_ORG po
										where		pp.POS_ID=$TMP_POS_ID and pp.ORG_ID=po.ORG_ID and pp.PL_CODE=pl.PL_CODE ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$TMP_POSEM_NO = $data2[POS_NO_NAME].' '.$data2[POS_NO];
					$TMP_PL_NAME = $data2[PL_NAME];
					$TMP_PT_CODE = trim($data2[PT_CODE]);
					$TMP_PT_NAME = trim($data2[PT_NAME]);
					$TMP_PM_CODE = trim($data2[PM_CODE]);
					$TMP_ORG_NAME = trim($data2[ORG_NAME]);
					$TMP_ORG_ID_1 = trim($data2[ORG_ID_1]);
					$TMP_ORG_ID_2 = trim($data2[ORG_ID_2]);

					$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$TMP_PM_CODE'  ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$TMP_PM_NAME = trim($data2[PM_NAME]);
					if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง" && !$TMP_PM_NAME) $TMP_PM_NAME = $TMP_PL_NAME;
				}
			
				$TMP_PAY_ID = $data[POS_ID];
				if ($TMP_PAY_ID) {
					$cmd = " 	select		POS_NO_NAME, POS_NO, po.ORG_NAME
										from		PER_POSITION pp, PER_ORG po
										where		pp.POS_ID=$TMP_PAY_ID and pp.ORG_ID=po.ORG_ID ";
					if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$TMP_PAY_NO = $data2[POS_NO_NAME] . $data2[POS_NO] . " " . $data2[ORG_NAME];
				}
			} else {
                               
				$TMP_POS_ID = $data[POS_ID];
                                 //echo "hhhhh =>".$TMP_POS_ID."<br>";
				if ($TMP_POS_ID) {
					$cmd = " 	select		POS_NO_NAME, POS_NO, pl.PL_NAME, po.ORG_NAME, pp.PT_CODE, po.ORG_ID, pp.PM_CODE, pp.ORG_ID_1, pp.ORG_ID_2,pp.ORG_ID_3,pp.ORG_ID_4,pp.ORG_ID_5
										from		PER_POSITION pp, PER_LINE pl, PER_ORG po
										where		pp.POS_ID=$TMP_POS_ID and pp.ORG_ID=po.ORG_ID and pp.PL_CODE=pl.PL_CODE ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
                   // echo "<pre>".$cmd;
					$TMP_POSEM_NO = $data2[POS_NO_NAME].' '.$data2[POS_NO];
					if ($MUNICIPALITY_FLAG==1) $TMP_POSEM_NO = pos_no_format($TMP_POSEM_NO,2);
					$TMP_PL_NAME = $data2[PL_NAME];
					$TMP_PT_CODE = trim($data2[PT_CODE]);
					$TMP_PT_NAME = trim($data2[PT_NAME]);
					$TMP_PM_CODE = trim($data2[PM_CODE]);
					$TMP_ORG_NAME = trim($data2[ORG_NAME]);
					$TMP_ORG_ID_1 = trim($data2[ORG_ID_1]);
					$TMP_ORG_ID_2 = trim($data2[ORG_ID_2]);
                    $TMP_ORG_ID_3 = trim($data2[ORG_ID_3]);
                    $TMP_ORG_ID_4 = trim($data2[ORG_ID_4]);
                    $TMP_ORG_ID_5 = trim($data2[ORG_ID_5]);
					$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$TMP_PM_CODE'  ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$TMP_PM_NAME = trim($data2[PM_NAME]);
                                        //echo "=> ".$TMP_PM_NAME;
					if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง" && !$TMP_PM_NAME) $TMP_PM_NAME = $TMP_PL_NAME;
				}
                
				$TMP_PAY_ID = $data[PAY_ID];
				if ($TMP_PAY_ID) {
					$cmd = " 	select		POS_NO_NAME, POS_NO, po.ORG_NAME
										from		PER_POSITION pp, PER_ORG po
										where		pp.POS_ID=$TMP_PAY_ID and pp.ORG_ID=po.ORG_ID ";
					if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$TMP_PAY_NO = $data2[POS_NO_NAME] . $data2[POS_NO] . " " . $data2[ORG_NAME];
				}
			}
		} elseif ($TMP_PER_TYPE == 2) {
			$TMP_POEM_ID = $data[POEM_ID];
			if ($TMP_POEM_ID) {
				$cmd = " 	select		POEM_NO_NAME, POEM_NO, pl.PN_NAME, po.ORG_NAME, po.ORG_ID, pp.ORG_ID_1, pp.ORG_ID_2, pp.ORG_ID_3, pp.ORG_ID_4, pp.ORG_ID_5, pl.PG_CODE   
								from			PER_POS_EMP pp, PER_POS_NAME pl, PER_ORG po 
								where		pp.POEM_ID=$TMP_POEM_ID and pp.ORG_ID=po.ORG_ID and pp.PN_CODE=pl.PN_CODE  ";
								
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_POSEM_NO = trim($data2[POEM_NO_NAME]).trim($data2[POEM_NO]);
				$TMP_PL_NAME = trim($data2[PN_NAME]);
				$TMP_PG_CODE = trim($data2[PG_CODE]);
				$TMP_ORG_NAME = trim($data2[ORG_NAME]);
				$TMP_ORG_ID_1 = trim($data2[ORG_ID_1]);
				$TMP_ORG_ID_2 = trim($data2[ORG_ID_2]);
                $TMP_ORG_ID_3 = trim($data2[ORG_ID_3]);
                $TMP_ORG_ID_4 = trim($data2[ORG_ID_4]);
                $TMP_ORG_ID_5 = trim($data2[ORG_ID_5]);

				$cmd = " 	select PG_NAME from PER_POS_GROUP where trim(PG_CODE)='$TMP_PG_CODE'  ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_PM_NAME = trim($data2[PG_NAME]);
			}
		} elseif ($TMP_PER_TYPE == 3) {
			$TMP_POEMS_ID = $data[POEMS_ID];
			if ($TMP_POEMS_ID) {
				$cmd = " 	select		POEMS_NO, pl.EP_NAME, po.ORG_NAME, po.ORG_ID, pp.ORG_ID_1, pp.ORG_ID_2, pp.ORG_ID_3, pp.ORG_ID_4, pp.ORG_ID_5, le.LEVEL_NAME  
						from			PER_POS_EMPSER pp, PER_EMPSER_POS_NAME pl, PER_ORG po, PER_LEVEL le
						where		pp.POEMS_ID=$TMP_POEMS_ID and pp.ORG_ID=po.ORG_ID and pp.EP_CODE=pl.EP_CODE  and pl.LEVEL_NO = le.LEVEL_NO";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_POSEM_NO = trim($data2[POEMS_NO]);
				$TMP_PL_NAME = trim($data2[EP_NAME]);
				$TMP_PM_NAME = trim($data2[LEVEL_NAME]);
				$TMP_ORG_NAME = trim($data2[ORG_NAME]);
				$TMP_ORG_ID_1 = trim($data2[ORG_ID_1]);
				$TMP_ORG_ID_2 = trim($data2[ORG_ID_2]);
                $TMP_ORG_ID_3 = trim($data2[ORG_ID_3]);
                $TMP_ORG_ID_4 = trim($data2[ORG_ID_4]);
                $TMP_ORG_ID_5 = trim($data2[ORG_ID_5]);
			}
		} elseif ($TMP_PER_TYPE == 4) {
			$TMP_POT_ID = $data[POT_ID];
			if ($TMP_POT_ID) {
				$cmd = " 	select		POT_NO, pl.TP_NAME, po.ORG_NAME, po.ORG_ID, pp.ORG_ID_1, pp.ORG_ID_2, pp.ORG_ID_3, pp.ORG_ID_4, pp.ORG_ID_5   
								from			PER_POS_TEMP pp, PER_TEMP_POS_NAME pl, PER_ORG po 
								where		pp.POT_ID=$TMP_POT_ID and pp.ORG_ID=po.ORG_ID and pp.TP_CODE=pl.TP_CODE  ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_POSEM_NO = trim($data2[POT_NO]);
				$TMP_PL_NAME = trim($data2[TP_NAME]);
				$TMP_ORG_NAME = trim($data2[ORG_NAME]);
				$TMP_ORG_ID_1 = trim($data2[ORG_ID_1]);
				$TMP_ORG_ID_2 = trim($data2[ORG_ID_2]);
                $TMP_ORG_ID_3 = trim($data2[ORG_ID_3]);
                $TMP_ORG_ID_4 = trim($data2[ORG_ID_4]);
                $TMP_ORG_ID_5 = trim($data2[ORG_ID_5]);
			}
		}
		if ($TMP_ORG_NAME=="-") $TMP_ORG_NAME = "";
		$TMP_ORG_NAME_1 = $TMP_ORG_NAME_2 = $TMP_ORG_NAME_3 = $TMP_ORG_NAME_4 = $TMP_ORG_NAME_5 = "";
		$MINISTRY_ID_REF = "";
		$TMP_MINISTRY_NAME = "";
		$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$TMP_DEPARTMENT_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$MINISTRY_ID_REF = $data2[ORG_ID_REF];
		$DEPARTMENT_NAME_REF = $data2[ORG_NAME];			//กรม
		
		if ($MINISTRY_ID_REF) {
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID_REF ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_MINISTRY_NAME = $data2[ORG_NAME];
		}
           

		if ($TMP_ORG_ID_1) {
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_ORG_NAME_1 = trim($data2[ORG_NAME]);
			if ($data2[ORG_NAME]=="โรงเรียน") {
				if ($TMP_ORG_ID_2) {
					$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_ORG_ID_2 ";
					//if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
				}
			} 
              $TMP_ORG_NAME_1 = $TMP_ORG_NAME_2 = $TMP_ORG_NAME_4 = $TMP_ORG_NAME_5 = "";
              if($TMP_ORG_ID_1){
                     $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_ORG_ID_1 ";
                     $db_dpis2->send_cmd($cmd);
                     $data2 = $db_dpis2->get_array();
                     $TMP_ORG_NAME_1 = $data2[ORG_NAME];
                }
                if($TMP_ORG_ID_2){
                     $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_ORG_ID_2 ";
                     $db_dpis2->send_cmd($cmd);
                     $data2 = $db_dpis2->get_array();
                     $TMP_ORG_NAME_2 = $data2[ORG_NAME];
                }
                if($TMP_ORG_ID_3){
                     $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_ORG_ID_3 ";
                     $db_dpis2->send_cmd($cmd);
                     $data2 = $db_dpis2->get_array();
                     $TMP_ORG_NAME_3 = $data2[ORG_NAME];
                }
                if($TMP_ORG_ID_4){
                     $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_ORG_ID_4 ";
                     $db_dpis2->send_cmd($cmd);
                     $data2 = $db_dpis2->get_array();
                     $TMP_ORG_NAME_4 = $data2[ORG_NAME]; 
                }
                if($TMP_ORG_ID_5){
                     $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_ORG_ID_5 ";
                     $db_dpis2->send_cmd($cmd);
                     $data2 = $db_dpis2->get_array();
                     $TMP_ORG_NAME_5 = $data2[ORG_NAME]; 
                }
		}

		if ($TMP_PM_NAME=="ผู้ว่าราชการจังหวัด" || $TMP_PM_NAME=="รองผู้ว่าราชการจังหวัด" || $TMP_PM_NAME=="ปลัดจังหวัด" || $TMP_PM_NAME=="หัวหน้าสำนักงานจังหวัด") {
			$TMP_PM_NAME .= $TMP_ORG_NAME;
			$TMP_PM_NAME = str_replace("จังหวัดจังหวัด", "จังหวัด", $TMP_PM_NAME); 
			$TMP_PM_NAME = str_replace("สำนักงานจังหวัดสำนักงานจังหวัด", "สำนักงานจังหวัด", $TMP_PM_NAME); 
		} elseif ($TMP_PM_NAME=="นายอำเภอ") {
			$TMP_PM_NAME .= $TMP_ORG_NAME_1;
			$TMP_PM_NAME = str_replace("อำเภอที่ทำการปกครองอำเภอ", "อำเภอ", $TMP_PM_NAME); 
		}
		
		//################################
		//สำหรับมอบหมายงาน
		//################################
		$TMP_ORG_ASS_NAME_0="";
		$TMP_ORG_ASS_NAME_1="";
		$TMP_ORG_ASS_NAME_2="";
		$TMP_ORG_ASS_NAME_3="";
		$TMP_ORG_ASS_NAME_4="";
		$TMP_ORG_ASS_NAME_5="";
		
		if ($TMP_DEPARTMENT_ASS_ID && $TMP_DEPARTMENT_ASS_ID != $TMP_DEPARTMENT_ID) {
			$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$TMP_DEPARTMENT_ASS_ID ";
            $db_dpis2->send_cmd($cmd);
            $data2 = $db_dpis2->get_array();
            $TMP_DEPARTMENT_ASS_NAME = $data2[ORG_NAME];
		}
		if ($TMP_ORG_ASS_ID) {
			$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$TMP_ORG_ASS_ID ";
            $db_dpis2->send_cmd($cmd);
            $data2 = $db_dpis2->get_array();
            $TMP_ORG_ASS_NAME_0 = $data2[ORG_NAME];
			if ($TMP_ORG_ASS_NAME_0=="-") $TMP_ORG_ASS_NAME_0 = "";
		}
		if ($TMP_ORG_ASS_ID_1) {
            $cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$TMP_ORG_ASS_ID_1 ";
			
            $db_dpis2->send_cmd($cmd);
            $data2 = $db_dpis2->get_array();
            "      ".$TMP_ORG_ASS_NAME_1 = $data2[ORG_NAME];
		}
		if ($TMP_ORG_ASS_ID_2) {
            $cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$TMP_ORG_ASS_ID_2 ";
            $db_dpis2->send_cmd($cmd);
            $data2 = $db_dpis2->get_array();
            $TMP_ORG_ASS_NAME_2 = $data2[ORG_NAME];
		}
        if ($TMP_ORG_ASS_ID_3) {
            $cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$TMP_ORG_ASS_ID_3 ";
            $db_dpis2->send_cmd($cmd);
            $data2 = $db_dpis2->get_array();
            $TMP_ORG_ASS_NAME_3 = $data2[ORG_NAME];
		}
        if ($TMP_ORG_ASS_ID_4) {
            $cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$TMP_ORG_ASS_ID_4 ";
            $db_dpis2->send_cmd($cmd);
            $data2 = $db_dpis2->get_array();
            $TMP_ORG_ASS_NAME_4 = $data2[ORG_NAME];
		}
        if ($TMP_ORG_ASS_ID_5) {
            $cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$TMP_ORG_ASS_ID_5 ";
            $db_dpis2->send_cmd($cmd);
            $data2 = $db_dpis2->get_array();
            $TMP_ORG_ASS_NAME_5 = $data2[ORG_NAME];
		}    
		    //$TMP_ORG_ASS_NAME = $TMP_ORG_ASS_NAME_0. "<hr>" . "&nbsp;" . $TMP_ORG_ASS_NAME_1;//ของเดิม
            
		if($TMP_ORG_ASS_ID_1)		$TMP_ORG_ASS_ID=$TMP_ORG_ASS_ID_1;
		if($TMP_ORG_ASS_ID_2)		$TMP_ORG_ASS_ID=$TMP_ORG_ASS_ID_2;
		if(!$TMP_ORG_ASS_ID && !$TMP_ORG_ASS_ID_1 && !$TMP_ORG_ASS_ID_2)		 		$TMP_ORG_ASS_NAME="";
		if ($MFA_FLAG==1 && $TMP_ORG_ASS_NAME=="" && $TMP_DEPARTMENT_ASS_NAME) $TMP_ORG_ASS_NAME = $TMP_DEPARTMENT_ASS_NAME;

		
		}
			
			$worksheet->write($xlsRow, 0, $num_order      , set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $FULLNAME       , set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $TMP_PL_NAME    , set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $LV_NAME        , set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $TMP_LEVEL_NAME , set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, $TMP_ORG_NAME   , set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 6, $TMP_ORG_NAME_1 , set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 7, $TMP_ORG_ASS_NAME_0 , set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 8, $TMP_ORG_ASS_NAME_1 , set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			
			$xlsRow++;
		
		//die("ชื่อ="+$FULLNAME+" ตำแหน่ง="+ $TMP_PL_NAME+" ประเภทตำแหน่ง="+$TMP_LEVEL_NAME);
	}//end while	
	
	
		//$count_page_data = $db_dpis->send_cmd($cmd);
		//print_header(1);
		

			

	

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_title.xls\"");
	header("Content-Disposition: inline; filename=\"$report_title.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>