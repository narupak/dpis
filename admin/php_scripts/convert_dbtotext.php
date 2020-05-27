<?
	include("../php_scripts/connect_database.php");
	include("php_scripts/session_start.php");
	include("php_scripts/load_per_control.php");	
	include("../php_scripts/connect_file.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

/*Release 5.2.1.5 Begin*/
$TableName_FK = array ( 
            "PER_PERSONAL"=>array(
                "OT_CODE" => "PER_OFF_TYPE",
                "PN_CODE" => "PER_PRENAME",
                "ORG_ID" => "PER_ORG_ASS",
                "POS_ID" => "PER_POSITION",
                "POEM_ID" => "PER_POS_EMP",
                "LEVEL_NO" => "PER_LEVEL",
                "MR_CODE" => "PER_MARRIED",
                "RE_CODE" => "PER_RELIGION",
                "PN_CODE_F" => "PER_PRENAME",
                "PN_CODE_M" => "PER_PRENAME",
                "PV_CODE" => "PER_PROVINCE",
                "MOV_CODE" => "PER_MOVMENT",
            ),
            "PER_POSITIONHIS"=>array( 
                "MOV_CODE" => "PER_MOVMENT",
                "PM_CODE" => "PER_MGT",
                "LEVEL_NO" => "PER_LEVEL",
                "PL_CODE" => "PER_LINE",
                "PN_CODE" => "PER_POS_NAME",
                "PT_CODE" => "PER_TYPE",
                "CT_CODE" => "PER_COUNTRY",
                "PV_CODE" => "PER_PROVINCE",
                "AP_CODE" => "PER_AMPHUR",
                "ORG_ID_1" => "PER_ORG",
                "ORG_ID_2" => "PER_ORG",
                "ORG_ID_3" => "PER_ORG",
            ), 
            "PER_ABILITY"=>array( 
                "AL_CODE" => "PER_ABILITYGRP",
            ), 
            "PER_DECORATEHIS"=>array( 
                "DC_CODE" => "PER_DECORATION",
            ), 
            "PER_EDUCATE"=>array( 
                "ST_CODE" => "PER_SCHOLARTYPE",
                "CT_CODE" => "PER_COUNTRY",
                "EN_CODE" => "PER_EDUCNAME",
                "EM_CODE" => "PER_EDUCMAJOR",
                "INS_CODE" => "PER_INSTITUTE",
            ), 
            "PER_EXTRA_INCOMEHIS"=>array( 
                "EXIN_CODE" => "PER_EXTRA_INCOME_TYPE",
            ),
            "PER_EXTRAHIS"=>array( 
                "EX_CODE" => "PER_EXTRATYPE",
            ),
            "PER_HEIR"=>array( 
                "HR_CODE" => "PER_HEIRTYPE",
            ),
            "PER_NAMEHIS"=>array( 
                "PN_CODE" => "PER_PRENAME",
            ),
            "PER_ORG"=>array( 
                "OL_CODE" => "PER_ORG_LEVEL",
                "OT_CODE" => "PER_ORG_TYPE",
                "AP_CODE" => "PER_AMPHUR",
                "PV_CODE" => "PER_PROVINCE",
                "CT_CODE" => "PER_COUNTRY",
                "ORG_ID_REF" => "PER_ORG",
            ),
            "PER_POSITION"=>array( 
                "ORG_ID" => "PER_ORG",
                "OT_CODE" => "PER_OFF_TYPE",
                "ORG_ID_1" => "PER_ORG",
                "ORG_ID_2" => "PER_ORG",
                "PM_CODE" => "PER_MGT",
                "PL_CODE" => "PER_LINE",
                "SKILL_CODE" => "PER_SKILL",
                "PT_CODE" => "PER_TYPE",
                "PC_CODE" => "PER_CONDITION",
                "DEPARTMENT_ID" => "PER_ORG",
            ),
            "PER_PUNISHMENT"=>array( 
                "CRD_CODE" => "PER_CRIME_DTL",
                "PEN_CODE" => "PER_PENALTY",
            ),
            "PER_REWARDHIS"=>array( 
                "REW_CODE" => "PER_REWARD",
            ),
            "PER_SALARYHIS"=>array( 
                "MOV_CODE" => "PER_MOVMENT",
            ),
            "PER_SERVICEHIS"=>array( 
                "SV_CODE" => "PER_SERVICE",
                "SRT_CODE" => "PER_SERVICETITLE",
                "ORG_ID" => "PER_ORG",
            ),
            "PER_TIMEHIS"=>array( 
                "TIME_CODE" => "PER_TIME",
            ),
            "PER_TRAINING"=>array( 
                "TR_CODE" => "PER_TRAIN",
                "CT_CODE" => "PER_COUNTRY",
                "CT_CODE_FUND" => "PER_COUNTRY",
            ),
        );        
    function gen_data_master($db_mst,$TableName_FK,$tbname,$fld,$subfld,$SELECTED_PER_ID){
        $mst_table=$TableName_FK[$tbname][$fld];/*ชื่อตารางหลัก*/
        unset($mst_field_list);
        //$mst_field_list = $db_mst->list_fields($mst_table);
        $cmd = " select * from $mst_table where rownum =1";
        $db_mst->send_cmd_fast($cmd);
        if ($db_mst->ERROR) {
            if($mst_table){
                $mst_field_list = $db_mst->list_fields($mst_table);
                $cnt_col=count($mst_field_list);
                unset($arr_mstfields);
                for($idx=1;$idx<=$cnt_col;$idx++){
                    $arr_mstfields[] = 'tbmst.'.$mst_field_list[$idx]["name"];
                }
                if(count($arr_mstfields)>0){
                    $mstfields_select = implode("||'[,]'||", $arr_mstfields);
                }
                $fld_key=$fld;
                if($mst_table=='PER_ORG'){
                    $fld_key='ORG_ID';
                }
                $sub_qry='(select '.$mstfields_select.' from '.$mst_table.' tbmst where trim(tbmst.'.$fld_key.') IN(trim('.$subfld.'))) AS '.$fld;
            }else{
                $sub_qry=$fld;
            }
            return $sub_qry;
        }
        return $fld;
    }
    function gen_qry($db_mst,$TableName_FK,$tbname,$fields_select,$SELECTED_PER_ID){
       $mst_table=array_keys($TableName_FK[$tbname]);
       $cnt_mst_table=count($mst_table);
       if($cnt_mst_table>0){
           unset($from_fld_arr);
           unset($to_fld_arr);
           for($idx=0;$idx<$cnt_mst_table;$idx++){
               $fld=$mst_table[$idx];
               $from_fld_arr[]=', '.$fld;
               $to_fld_arr[]=', '.gen_data_master($db_mst,$TableName_FK,$tbname,$fld,'tb.'.$fld);
           }
           $fld_replace = str_replace ($from_fld_arr, $to_fld_arr, $fields_select);
           $cmd = " select $fld_replace from $tbname tb WHERE tb.PER_ID in ($SELECTED_PER_ID) ";
           return $cmd;
       }
    }
   
    /*echo '<pre>';
    print_r( array_keys($TableName_FK['PER_POSITIONHIS']));
    $from_fld_arr[]="MOV_CODE";
    $from_fld_arr[]="PM_CODE";
    $from_fld_arr[]="LEVEL_NO";
    
    $to_fld_arr[]=" (select * from tb where 1=1)AS MOV_CODE ";
    $to_fld_arr[]=" (select * from tb where 1=1)AS PM_CODE";
    $to_fld_arr[]=" (select * from tb where 1=1)AS LEVEL_NO";
    
    
    $fld = 'XXX,PM_CODE,LEVEL_NO,XXX,MOV_CODE';
    $text = str_replace ($from_fld_arr, $to_fld_arr, $fld);
    $text= 'select '.$text.' from table';
    echo $text;*/
    /*echo gen_data_master($db_dpis1,$TableName_FK,'PER_POSITIONHIS','MOV_CODE','pph.MOV_CODE').'<br><br>';
    echo gen_data_master($db_dpis1,$TableName_FK,'PER_POSITIONHIS','PM_CODE','pph.PM_CODE').'<br><br>';
    echo gen_data_master($db_dpis1,$TableName_FK,'PER_POSITIONHIS','LEVEL_NO','pph.LEVEL_NO').'<br><br>';
    echo gen_data_master($db_dpis1,$TableName_FK,'PER_POSITIONHIS','PL_CODE','pph.PL_CODE').'<br><br>';
    echo gen_data_master($db_dpis1,$TableName_FK,'PER_POSITIONHIS','PN_CODE','pph.PN_CODE').'<br><br>';
    echo gen_data_master($db_dpis1,$TableName_FK,'PER_POSITIONHIS','PT_CODE','pph.PT_CODE').'<br><br>';
    echo gen_data_master($db_dpis1,$TableName_FK,'PER_POSITIONHIS','CT_CODE','pph.CT_CODE').'<br><br>';
    echo gen_data_master($db_dpis1,$TableName_FK,'PER_POSITIONHIS','PV_CODE','pph.PV_CODE').'<br><br>';
    echo gen_data_master($db_dpis1,$TableName_FK,'PER_POSITIONHIS','AP_CODE','pph.AP_CODE').'<br><br>';
    echo gen_data_master($db_dpis1,$TableName_FK,'PER_POSITIONHIS','ORG_ID_1','pph.ORG_ID_1').'<br><br>';
    echo gen_data_master($db_dpis1,$TableName_FK,'PER_POSITIONHIS','ORG_ID_2','pph.ORG_ID_2').'<br><br>';
    echo gen_data_master($db_dpis1,$TableName_FK,'PER_POSITIONHIS','ORG_ID_3','pph.ORG_ID_3').'<br><br>';*/
/*Release 5.2.1.5 End*/
   
        
        
        
	// ===== กำหนดค่าเริ่มต้น และค่าแยกประเภทตัวแปรระหว่าง ข้อความ กับ ตัวเลข
	$DIVIDE_TEXTFILE = "|#|";
        
        
	if ($BKK_FLAG==1){
		$path_toshow = "C:\\portfolio\\personal_data\\";
	}else{
		$path_toshow = "C:\\dpis\\personal_data\\";
	}
	$path_tosave = (trim($path_tosave))? $path_tosave : $path_toshow;
	$path_tosave_tmp = str_replace("\\", "\\\\", $path_tosave);
	$path_toshow = $path_tosave;
	if(is_dir($path_toshow) == false) {
		mkdir($path_toshow, 0777,true);
	}
	/*echo "$path_toshow <br>
	$path_tosave <br>
	$path_tosave_tmp
	";*/

	if ($DPISDB == "odbc") {
		$TYPE_TEXT_STR = array("VARCHAR", "MEMO", "LONGCHAR", "TEXT");
		$TYPE_TEXT_INT = array("INTEGER", "INTEGER2", "SMALLINT", "SINGLE", "DOUBLE", "REAL", "NUMBER");
	} elseif ($DPISDB == "oci8") {
		$TYPE_TEXT_STR = array("VARCHAR", "VARCHAR2", "CHAR");
		$TYPE_TEXT_INT = array("NUMBER");
	}elseif($DPISDB=="mysql"){
		$TYPE_TEXT_STR = array("VARCHAR", "MEMO", "LONGCHAR", "TEXT");
		$TYPE_TEXT_INT = array("INTEGER", "INTEGER2", "SMALLINT", "SINGLE", "DOUBLE", "REAL", "NUMBER");
	}
	// =======================================================
       
	if ($command=="CONVERT" && $SELECTED_PER_ID) {
           
            if(file_exists($path_toshow)){
		$table = array("PER_PERSONAL", "PER_POSITIONHIS", "PER_SALARYHIS", "PER_EXTRAHIS", "PER_EDUCATE", "PER_TRAINING", "PER_ABILITY", 
		"PER_SPECIAL_SKILL", "PER_HEIR", "PER_ABSENTHIS", "PER_PUNISHMENT", "PER_SERVICEHIS", "PER_REWARDHIS", "PER_MARRHIS", 
		"PER_NAMEHIS", "PER_DECORATEHIS", "PER_TIMEHIS", "PER_EXTRA_INCOMEHIS" , "PER_ADDRESS" , "PER_FAMILY");

		// ===== วนลูปตาม array table =====
		for ( $i=0; $i<count($table); $i++ ) { 
			//echo "<b>$i. $table[$i] ::</b><br>";
	
			unset($field_list);
			// ===== select ชื่อ fields จาก $table ===== 
			$cmd = " select * from $table[$i] where rownum =1";
			$db_dpis->send_cmd_fast($cmd);
			if ($db_dpis->ERROR) {
				$field_list = $db_dpis->list_fields($table[$i]);
			}
//			echo "<pre>"; print_r($field_list); echo "</pre>";

			// ===== นำชื่อ fields เก็บลง array =====
			unset($arr_fields);
			if ($DPISDB=="odbc" || $DPISDB=="oci8") {
				for($j=1; $j<=count($field_list); $j++) :
					$arr_fields[] = $field_list[$j]["name"];
				endfor;
			}else{
				for($j=0; $j<count($field_list); $j++) :
					$arr_fields[] = $field_list[$j]["name"];
				endfor;
			} // end if
			//echo (implode(", ", $arr_fields));
			//echo "<pre>"; print_r($arr_fields); echo "</pre>";

			// ===== เขียนชื่อ fields จาก db write ลง textfile =====
			$db_textfile = new connect_file("$table[$i]", "w", "", "$path_tosave_tmp");
			if (count($arr_fields) > 0) {
				$db_textfile->write_text_data(implode("$DIVIDE_TEXTFILE", $arr_fields) . "\n");
		
				// ===== นำข้อมูลแต่ละ fileds จาก db write ลง textfile =====
				$fields_select = implode(", ", $arr_fields);
                                
                                //echo $fields_select.'<br>';
                                //$sql=gen_qry($db_dpis1,$TableName_FK,$table[$i],$fields_select,$SELECTED_PER_ID);
                                //echo '<pre>';
                                //echo $sql;
                                
				$cmd = " select $fields_select from $table[$i] WHERE PER_ID in ($SELECTED_PER_ID) ";
                                
                                //if(0==1){$cmd=$sql;}/*Release 5.2.1.5  && $table[$i]!="PER_PERSONAL"*/
                                //echo '<br><font color=green>'.$cmd.'</font><br><br>';
                                
				$db_dpis->send_cmd_fast($cmd);
	//			echo "<br>[".count($arr_fields)."] : $cmd<br><br>";
	//			$db_dpis->show_error();
				$db_textfile = new connect_file("$table[$i]", "a", "", "$path_tosave_tmp");
				unset($data, $arr_data);
				while($data = $db_dpis->get_array()){
                                        
					$rc = count($data);
                                        $data=  preg_replace('/\r\n|\r|\n/','',$data) ;/*Release 5.2.1.5*/
					
                                        
                                        $arr_data[] = implode("$DIVIDE_TEXTFILE", $data);
                                        
                                        /*แนวใหม่*/
                                        /*$tmp_line=$data[$arr_fields[0]];
                                        for($idx=1;$idx<count($arr_fields);$idx++){
                                            $tmp_line.= $DIVIDE_TEXTFILE.$data[$arr_fields[$idx]];
                                        }
                                        $arr_data[]=$tmp_line;*/
                                        if($data[PER_NAME] && $data[PER_SURNAME]){
                                            $PER_NAME =  $data[PER_NAME]." ".$data[PER_SURNAME];
                                            $ARR_PER_NAME[]=$PER_NAME;
                                        }    
                                      
				}
				$db_textfile->write_text_data(implode("\n", $arr_data));
			}
		} 	// endif for ($i=0; $i<=count($table); $i++)
		$cmd = " select * from per_position where POS_ID in (SELECT POS_ID FROM PER_PERSONAL WHERE PER_ID in ($SELECTED_PER_ID)) ";
		$db_dpis->send_cmd_fast($cmd);
		//$db_dpis->show_error();
		$field_list = $db_dpis->list_fields(per_position);

		unset($arr_fields);
		if ($DPISDB=="odbc" || $DPISDB=="oci8") {
			for($j=1; $j<=count($field_list); $j++) :
				$arr_fields[] = $field_list[$j]["name"];
			endfor;
		}else{
			for($j=0; $j<=count($field_list); $j++) :
				$arr_fields[] = $field_list[$j]["name"];
			endfor;
		} // end if

		// ===== เขียนชื่อ fields จาก db write ลง textfile =====
		$db_textfile = new connect_file("PER_POSITION", "w", "", "$path_tosave_tmp");
		$db_textfile->write_text_data(implode("$DIVIDE_TEXTFILE", $arr_fields) . "\n");
	
		// ===== นำข้อมูลแต่ละ fileds จาก db write ลง textfile =====
		$fields_select = implode(", ", $arr_fields);
		if ($DPISDB == "odbc") {
			$cmd = " select a.POS_ID, a.ORG_ID, a.POS_NO, a.OT_CODE, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE, a.PL_CODE, a.CL_NAME, a.POS_SALARY, 
							  a.POS_MGTSALARY, a.SKILL_CODE, a.PT_CODE, a.PC_CODE, a.POS_CONDITION, a.POS_DOC_NO, a.POS_REMARK, a.POS_DATE, a.POS_GET_DATE, 
							  a.POS_CHANGE_DATE, a.POS_STATUS, a.UPDATE_USER, a.UPDATE_DATE, a.DEPARTMENT_ID 
							  from PER_POSITION a
							  where  a.POS_ID in (SELECT POS_ID FROM PER_PERSONAL WHERE PER_ID in ($SELECTED_PER_ID)) and POS_STATUS=1 
							 order by iif(isnull(a.POS_NO),0,CLng(a.POS_NO)) ";
		} elseif ($DPISDB == "oci8") {
			$cmd = " select a.POS_ID, a.ORG_ID, a.POS_NO, a.OT_CODE, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE, a.PL_CODE, a.CL_NAME, a.POS_SALARY, 
							  a.POS_MGTSALARY, a.SKILL_CODE, a.PT_CODE, a.PC_CODE, a.POS_CONDITION, a.POS_DOC_NO, a.POS_REMARK, a.POS_DATE, a.POS_GET_DATE, 
							  a.POS_CHANGE_DATE, a.POS_STATUS, a.UPDATE_USER, a.UPDATE_DATE, a.DEPARTMENT_ID 
							  from PER_POSITION a
							  where a.POS_ID in (SELECT POS_ID FROM PER_PERSONAL WHERE PER_ID in ($SELECTED_PER_ID)) and POS_STATUS=1 
							 order by to_number(replace(a.POS_NO,'-','')) ";
		} // end if
		$db_dpis->send_cmd_fast($cmd);
		//$db_dpis->show_error();
		//echo "$count :: $cmd <br>==========<br>";

		$db_textfile = new connect_file("PER_POSITION", "a", "", "$path_tosave_tmp");
		unset($data, $arr_data);
		while ( $data = $db_dpis->get_array() ) {
			$arr_data[] = implode("$DIVIDE_TEXTFILE", str_replace("\n", "<br>", $data));
		}
		$db_textfile->write_text_data(implode("\n", $arr_data));

		$cmd = " select * from per_org ";
		$db_dpis->send_cmd_fast($cmd);
		$field_list = $db_dpis->list_fields(per_org);

		unset($arr_fields);
		if ($DPISDB=="odbc" || $DPISDB=="oci8") {
			for($j=1; $j<=count($field_list); $j++) :
				$arr_fields[] = $field_list[$j]["name"];
			endfor;
		}else{
			for($j=0; $j<=count($field_list); $j++) :
				$arr_fields[] = $field_list[$j]["name"];
			endfor;
		} // end if

		// ===== เขียนชื่อ fields จาก db write ลง textfile =====
		$db_textfile = new connect_file("PER_ORG", "w", "", "$path_tosave_tmp");
		$db_textfile->write_text_data(implode("$DIVIDE_TEXTFILE", $arr_fields) . "\n");
	
		// ===== นำข้อมูลแต่ละ fileds จาก db write ลง textfile =====
		$fields_select = implode(", ", $arr_fields);
		$cmd = " select $fields_select from PER_ORG where DEPARTMENT_ID = $DEPARTMENT_ID and ORG_ACTIVE = 1 order by OL_CODE, ORG_CODE ";
		$count = $db_dpis->send_cmd_fast($cmd);
		//$db_dpis->show_error();

		$db_textfile = new connect_file("PER_ORG", "a", "", "$path_tosave_tmp");
		unset($data, $arr_data);
		while ( $data = $db_dpis->get_array() ) {
			$arr_data[] = implode("$DIVIDE_TEXTFILE", str_replace("\n", "<br>", $data));
		}
		$db_textfile->write_text_data(implode("\n", $arr_data));

		unset($arr_fields, $data, $arr_data);
		$SELECTED_PER_ID = "";
		
//		$arrtmp=explode("\\\\",$path_toshow);
//		print_r($arrtmp);
                $path_toshow_html = $path_toshow;
                $real_path = $path_toshow;
		$path_toshow = stripslashes($path_toshow);
            }    
	} // endif command==CONVERT && PER_ID
?>