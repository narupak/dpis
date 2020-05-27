<?
	include("../php_scripts/connect_database.php");
	include("php_scripts/session_start.php");
	include("../php_scripts/connect_file.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        
//error_reporting(1);
        /*FK*/
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
        
        
        $TableNameSub_FK= array(
            "PER_AMPHUR"=>array(
                "PV_CODE"=>"PER_PROVINCE"
            ),
            "PER_CRIME_DTL"=>array(
                "CR_CODE"=>"PER_CRIME"
            ),
            "PER_EDUCNAME"=>array(
                "EL_CODE"=>"PER_EDUCLEVEL"
            ),
            "PER_HEIR"=>array(
                "HR_CODE"=>"PER_HEIRTYPE"
            ),
            "PER_INSTITUTE"=>array(
                "CT_CODE"=>"PER_COUNTRY"
            ),
            "PER_MGT"=>array(
                "PS_CODE"=>"PER_STATUS"
            ),
            "PER_ORG"=>array(
                "OL_CODE"=>"PER_ORG_LEVEL",
                "OT_CODE"=>"PER_ORG_TYPE",
                "AP_CODE"=>"PER_AMPHUR",
                "PV_CODE"=>"PER_PROVINCE",
                "CT_CODE"=>"PER_COUNTRY",
                "ORG_ID_REF"=>"PER_ORG"
            ),
            "PER_POS_EMP"=>array(
                "ORG_ID"=>"PER_ORG",
                "ORG_ID_1"=>"PER_ORG",
                "ORG_ID_2"=>"PER_ORG",
                "PN_CODE"=>"PER_POS_NAME",
                "DEPARTMENT_ID"=>'PER_ORG',
            ),
            "PER_POS_NAME"=>array(
                "PG_CODE"=>"PER_POS_GROUP"
            ),
            "PER_POSITION"=>array(
                "PC_CODE"=>"PER_CONDITION",
                "DEPARTMENT_ID"=>"PER_ORG",
                "ORG_ID"=>"PER_ORG",
                "OT_CODE"=>"PER_OFF_TYPE",
                "ORG_ID_1"=>"PER_ORG",
                "ORG_ID_2"=>"PER_ORG",
                "PM_CODE"=>"PER_MGT",
                "PL_CODE"=>"PER_LINE",
                "SKILL_CODE"=>"PER_SKILL",
                "PT_CODE"=>"PER_TYPE",
            ),
            "PER_PROVINCE"=>array(
                "CT_CODE"=>"PER_COUNTRY"
            ),
            "PER_SKILL"=>array(
                "SG_CODE"=>"PER_SKILL_GROUP"
            ),
        );
        function get_val_mst($db_mst,$TableName_FK,$tbname,$txt_field_name,$txt_field_value){
            //$mst_table=$TableName_FK[$tbname][$fld];/*ชื่อตารางหลัก*/
            $arr_field_name=  explode(',', $txt_field_name);
            $arr_field_value=  explode(',', str_replace('[,]','[#]',$txt_field_value) );
            $cnt=count($arr_field_name);
            
            if($cnt>0){
                unset($val_new);
                unset($modify_field_value);
                for($idx=0;$idx<=$cnt;$idx++){
                    $fld=trim($arr_field_name[$idx]);
                    $val=trim($arr_field_value[$idx]);
                    
                    
                    
                    $mst_table=$TableName_FK[$tbname][$fld];
                    if($mst_table){
                        //echo $mst_table.'['.$idx.'] fld='.$fld.' , value='.$val.'<br>';
                        $arr_val=  explode('[#]', $val);
                        if(trim($arr_val[0])!='NULL'){
                            $sql="SELECT COUNT(*) AS CNT FROM ".$mst_table." WHERE trim(".$fld.")=".trim($arr_val[0])."' ";
                            $db_mst->send_cmd_fast($sql);
                            $data_chk = $db_mst->get_array();
                            $isdata=$data_chk["CNT"];
                            if($isdata==1){
                                $val_return=trim($arr_val[0])."'";
                                $modify_field_value[]=trim($arr_val[0])."'";
                                //echo '<br><font color=blue>[1=>'.$val_return.']</font>=>'.$val;
                            }else{
                                $val_return=trim($arr_val[0])."'";;
                                insert_data_new_mst($db_mst,$mst_table,$txt_field_name,$val,trim($arr_val[0]));
                                $modify_field_value[]=trim($arr_val[0])."'";
                                //echo '<br><font color=blue>[2=>'.$val_return.']</font>=>'.$val;
                            }
                            //$val_new[]=$val_return; 
                        }
                    }else{
                        $modify_field_value[]=$val."[".$tbname.".".$fld."]";
                    }
                }
                $str_field_value= implode(',',$modify_field_value);
                $len_field_value=strlen($str_field_value)-1;
                return substr($str_field_value,0,$len_field_value);
                //return implode(',',$modify_field_value);
            }
        }
        function insert_data_new_mst($db_mst,$tbname,$txt_field_name,$txt_field_val,$val_return){
            $cmd = " select * from $tbname where rownum =1";
            $db_mst->send_cmd_fast($cmd);
            if ($db_mst->ERROR) {
                $mst_field_list = $db_mst->list_fields($tbname);
                $cnt_col=count($mst_field_list);
                unset($arr_mstfields);
                for($idx=1;$idx<=$cnt_col;$idx++){
                    $arr_mstfields[] = $mst_field_list[$idx]["name"];
                }
                if(count($arr_mstfields)>0){
                    $mstfields_name = implode(",", $arr_mstfields);
                }
                $fld_key=$fld;
                if($tbname=='PER_ORG'){
                    $fld_key='ORG_ID';
                }
                $vals_len=strlen($txt_field_val)-2;
                $vals_prepare=substr($txt_field_val,1, $vals_len);//ตัด ' หน้าหลังออก
                $vals_arr=explode('[#]', $vals_prepare);
                $vals_cnt=count($vals_arr);
                if($vals_cnt>0){
                    for($idx=0;$idx<$vals_cnt;$idx++){
                        $fld_val=$vals_arr[$idx];
                        if(empty($fld_val)){
                            $vals_new[]="NULL";
                        }else{
                            $vals_new[]="'".addslashes(trim($fld_val))."'";
                        }
                    }
                    $vals_data=implode(',', $vals_new);
                }
                //$vals=str_replace('[#]',',',  substr($txt_field_val,1, $vals_len) );
                $sub_qry= "INSERT INTO ".$tbname."(".$mstfields_name.") 
                            VALUES(".$vals_data.")";
                //echo '<br><font color=green>'.$sub_qry.'</font><br>';
                $db_mst->send_cmd_fast($sub_qry);
                //return $val_return."'";
            }
            //return $val_return."'";
            //echo $tbname.'['.$val_return.']';
            //$val=str_replace('[#]',',',$txt_field_val);
            //echo "INSERT INTO ".$tbname." (".$txt_field_name.") VALUES(".$val.") <br>";
        }
        
        /**/
        //echo "name=".count($TableName_FK["PER_TRAINING"])."<BR> "; 
        //echo "name=".$TableName_FK["PER_TRAINING"][0]."<BR> "; 
        /*function chk_data_master($TableName_FK){
            
        }*/
        /*Modify*/
        function checkFld($db,$fld,$tb,$rtn){
            $fld_arr = explode(',',$fld);
            $cnt_fld=count($fld_arr);
            $msg="";
            $msg="ผลการตรวจสอบตาราง ".$tb;
            $rtn=0;
            if($cnt_fld>0){
                for($idx=0;$idx<$cnt_fld;$idx++){
                    
                    $fldval=strtoupper(trim($fld_arr[$idx]));
                    $tbval=strtoupper(trim($tb));
                    $cmdChk ="  SELECT COUNT(COLUMN_NAME) AS CNT FROM USER_TAB_COLS 
                                WHERE TABLE_NAME = '".strtoupper($tb)."' AND UPPER(COLUMN_NAME) ='".$fldval."' ";
                    $db->send_cmd($cmdChk);
                    $dataChk = $db->get_array();
                    if($dataChk[CNT]=="0"){
                        $rtn++;
                        $msg.="<font color=red> -> ไม่พบฟิลด์ ".$fldval.'<br>';
                    }
                }
                if($rtn>0){
                    $msg.=" กรุณาติดต่อติดต่อเจ้าหน้าที่ พร้อมส่งข้อความนี้ให้เจ้าหน้าที่เพื่อทำการตรวจสอบกับหน่วยงานต้นทาง</font>";
                }else{
                    $msg.=" : ปกติ";
                }
            }else{
                $msg.=" : ปกติ";
            }
            return $msg;
        }
        
        
        /*Release 5.2.1.5 Begin*/
        function set_constraints($db_mst,$db_cont,$tbname,$set){
            //$set=DISABLE,ENABLE
            $sql="  SELECT CONSTRAINT_NAME
                    FROM USER_CONSTRAINTS NATURAL JOIN USER_CONS_COLUMNS
                    WHERE TABLE_NAME = '".strtoupper($tbname)."' AND CONSTRAINT_TYPE='R'
                    ORDER BY CONSTRAINT_NAME ";
            $db_mst->send_cmd($sql);
            while($data_fk = $db_mst->get_array()){	
                $CONSTRAINT_NAME = $data_fk[CONSTRAINT_NAME];
                $sql_set="ALTER TABLE ".strtoupper($tbname)." ".$set." CONSTRAINT ".$CONSTRAINT_NAME;
                $db_cont->send_cmd($sql_set);
            }
        }
        /*Release 5.2.1.5 End*/            

        /**/
        
        
        
        
	// ===== กำหนดค่าเริ่มต้น และค่าแยกประเภทตัวแปรระหว่าง ข้อความ กับ ตัวเลข
	$DIVIDE_TEXTFILE = "|#|";
        
        $msg_step_import='';/*Release 5.2.1.5*/
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
	
	if ($DPISDB == "odbc") {
		$TYPE_TEXT_STR = array("VARCHAR", "MEMO", "LONGCHAR", "TEXT");
		$TYPE_TEXT_INT = array("INTEGER", "INTEGER2", "SMALLINT", "SINGLE", "DOUBLE", "REAL", "NUMBER");
	} elseif ($DPISDB == "oci8") {
		$TYPE_TEXT_STR = array("VARCHAR", "VARCHAR2", "CHAR");
		$TYPE_TEXT_INT = array("NUMBER");
	} elseif ($DPISDB == "mysql") {
		$TYPE_TEXT_STR = array("VARCHAR", "MEMO", "LONGCHAR", "TEXT");
		$TYPE_TEXT_INT = array("INTEGER", "INTEGER2", "SMALLINT", "SINGLE", "DOUBLE", "REAL", "NUMBER");
	}
	// =======================================================

	if ($command=="CONVERT") { 
//		$arrfield_except_per_org = array( 
//			"ORG_ID" => "NULL", 
//			"ORG_WEBSITE" => "NULL");

		// =====================================================
		// ===== select ชื่อ fields จาก $table ===== 
		if ($STP || $HIPPS) {
			$cmd = " delete from PER_POSITION where DEPARTMENT_ID = $SESS_DEPARTMENT_ID ";
			$db_dpis->send_cmd_fast($cmd);
//			$db_dpis->show_error();

			$cmd = " delete from PER_ORG where DEPARTMENT_ID = $SESS_DEPARTMENT_ID and ORG_ACTIVE = 1 ";
			$db_dpis->send_cmd_fast($cmd);
//			$db_dpis->show_error();

			$cmd = " update PER_ORG set ORG_WEBSITE = NULL ";
			$db_dpis->send_cmd_fast($cmd);
//			$db_dpis->show_error();

			$cmd = " select * from PER_ORG where rownum = 1 ";
			$db_dpis->send_cmd_fast($cmd);
			$field_list = $db_dpis->list_fields(PER_ORG);
			// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
			unset($arr_fields);
			if ($DPISDB=="odbc" || $DPISDB=="oci8") {
				for($i=1; $i<=count($field_list); $i++) : 
					$arr_fields[] = $tmp_name = $field_list[$i]["name"];
					$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
				endfor;
			} else {
				for($i=0; $i<=count($field_list); $i++) : 
					$arr_fields[] = $tmp_name = $field_list[$i]["name"];
					$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
				endfor;
			} // end if

			$cmd = " select max(ORG_ID) as MAX_ID from PER_ORG ";
			$db_dpis2->send_cmd_fast($cmd);
			$data2 = $db_dpis2->get_array();
			$MAX_ID = $data2[MAX_ID] + 1;

			$db_textfile = new connect_file("PER_ORG", "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");
			while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
				// ลบค่าข้อมูล field ORG_ID ที่ได้จาก textfile ออก เพื่อใส่ค่า max ตัวใหม่
				$ORG_ID = $data[ORG_ID];	
				$OL_CODE = trim($data[OL_CODE]);	
				$ORG_ID_REF = $data[ORG_ID_REF];	
				unset($data[ORG_ID], $field_name, $field_value);
				// ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields
				foreach ($data as $key => $fieldvalue) {	
					if ($key!='OP_CODE' && $key!='OS_CODE') {
						if (($key=='ORG_CODE' || $key=='ORG_NAME' || $key=='ORG_SHORT' || $key=='OL_CODE' || $key=='OT_CODE' || $key=='OP_CODE' || $key=='OS_CODE' || 
						$key=='ORG_ADDR1' || $key=='ORG_ADDR2' || $key=='ORG_ADDR3' || $key=='AP_CODE' || $key=='PV_CODE' || $key=='CT_CODE'  || $key=='ORG_DATE' || 
						$key=='ORG_JOB' || $key=='UPDATE_DATE' || $key=='ORG_WEBSITE') && $fieldvalue!='NULL') 	$fieldvalue = $fieldvalue;
						if ($key=='ORG_WEBSITE') 	$fieldvalue = "'$ORG_ID'";
						if ($key=='DEPARTMENT_ID') $fieldvalue = $SESS_DEPARTMENT_ID;
						if ($OL_CODE=='03' && $key=='ORG_ID_REF') $fieldvalue = $SESS_DEPARTMENT_ID;
						if ($OL_CODE > "03" && $key=='ORG_ID_REF') {
							$cmd = " select ORG_ID from PER_ORG where ORG_WEBSITE = $ORG_ID_REF ";
							$db_dpis2->send_cmd_fast($cmd);
							$data2 = $db_dpis2->get_array();
							$fieldvalue = $data2[ORG_ID];
						}
						$field_name .= (trim($field_name) != "")? ", " . $key : $key;
						// ทดสอบว่าเป็น field ที่ไม่ต้องการ insert ค่าลงใน table ใช่หรือไม่
						if ( in_array($key, array_keys($arrfield_except_per_org)) )		
							$field_value .= (trim($field_value) != "")? ", " . $arrfield_except_per_org[$key] : $arrfield_except_per_org[$key];
						else
							$field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
	//					echo "$field_value<br>";
					}	
				}
                                set_constraints($db_dpis1,$db_dpis2,'PER_ORG','DISABLE');/*Release 5.2.1.5 ...*/
				
                                $cmd = " INSERT INTO PER_ORG ( ORG_ID, $field_name ) VALUES ( $MAX_ID, $field_value )";	
				$db_dpis->send_cmd_fast($cmd);
                                
                                set_constraints($db_dpis1,$db_dpis2,'PER_ORG','ENABLE');/*Release 5.2.1.5 ...*/
				$chkErr=$db_dpis->ERROR;
				if(!$chkErr){
					echo '<font color=red>ไม่สามารถนำเข้าข้อมูลหน่วยงานได้้ <br>['.$db_dpis->show_error().']</font><br>-----------------------<br>';
				}
//				$db_dpis->show_error();
//				echo "<br><u>PER_ORG:</u> <b>$MAX_ID.</b> $cmd<br>===================<br>";
				$MAX_ID++;
			}  // end while 

			$cmd = " select * from PER_POSITION where rownum = 1 ";
			$db_dpis->send_cmd_fast($cmd);
			$field_list = $db_dpis->list_fields(PER_POSITION);
			// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
			unset($arr_fields);
			if ($DPISDB=="odbc" || $DPISDB=="oci8") {
				for($i=1; $i<=count($field_list); $i++) : 
					$arr_fields[] = $tmp_name = $field_list[$i]["name"];
					$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
				endfor;
			}else{
				for($i=0; $i<=count($field_list); $i++) : 
					$arr_fields[] = $tmp_name = $field_list[$i]["name"];
					$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
				endfor;
			} // end if

			$cmd = " select max(POS_ID) as MAX_ID from PER_POSITION ";
			$db_dpis2->send_cmd_fast($cmd);
			$data2 = $db_dpis2->get_array();
			$POS_ID = $data2[MAX_ID] + 1;
			$DEPARTMENT_ID = $SESS_DEPARTMENT_ID;

			$db_textfile = new connect_file("PER_POSITION", "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");
			while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
				// ลบค่าข้อมูล field POS_ID ที่ได้จาก textfile ออก เพื่อใส่ค่า max ตัวใหม่
				$CL_NAME = "'$data[CL_NAME]'";	
				$LV_NAME = $data[LV_NAME];	
				$POS_NO_INT = $data[POS_NO] + 0;	
				$TEMP_ORG_ID = $data[ORG_ID];	
				$TEMP_ORG_ID_1 = $data[ORG_ID_1];	
				$TEMP_ORG_ID_2 = $data[ORG_ID_2];	
				unset($data[POS_ID], $field_name, $field_value);
				unset($data[DEPARTMENT_ID], $field_name, $field_value);
				unset($data[POS_NO_INT], $field_name, $field_value);

				$cmd = " select ORG_ID from  PER_ORG where ORG_WEBSITE = $TEMP_ORG_ID ";
				$db_dpis2->send_cmd_fast($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$TMP_ORG_ID = $data2[ORG_ID];
				if ($TEMP_ORG_ID_1!='NULL') {
					$cmd = " select ORG_ID from  PER_ORG where ORG_WEBSITE = $TEMP_ORG_ID_1 ";
					$db_dpis2->send_cmd_fast($cmd);
//					$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					$TMP_ORG_ID_1 = $data2[ORG_ID];
				} else $TMP_ORG_ID_1 = 'NULL';
				if ($TEMP_ORG_ID_2!='NULL') {
					$cmd = " select ORG_ID from  PER_ORG where ORG_WEBSITE = $TEMP_ORG_ID_2 ";
					$db_dpis2->send_cmd_fast($cmd);
//					$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					$TMP_ORG_ID_2 = $data2[ORG_ID];
				} else $TMP_ORG_ID_2 = 'NULL';
//echo "$TEMP_ORG_ID-$TMP_ORG_ID-$TEMP_ORG_ID_1-$TMP_ORG_ID_1-$TEMP_ORG_ID_2-$TMP_ORG_ID_2<br>";		
				// ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields
				foreach ($data as $key => $fieldvalue) {	
					if ($key=='LV_NAME') 	
						if ($LV_NAME=='M1') $fieldvalue = "SES1";
						elseif ($LV_NAME=='M2') $fieldvalue = "SES2";
						elseif ($LV_NAME=='D1') $fieldvalue = "M1";
						elseif ($LV_NAME=='D2') $fieldvalue = "M2";
					if (($key=='CL_NAME' || $key=='POS_NO' || $key=='OT_CODE' || $key=='LV_NAME' || $key=='SKILL_CODE' || $key=='PT_CODE' || $key=='PC_CODE' || $key=='PL_CODE' || 
					$key=='PM_CODE' || $key=='POS_DATE' || $key=='POS_GET_DATE' || $key=='POS_CHANGE_DATE' || $key=='UPDATE_DATE'  || $key=='POS_CONDITION' || 
					$key=='POS_DOC_NO' || $key=='POS_REMARK' || $key=='LEVEL_NO' || $key=='POS_PER_NAME') && $fieldvalue!='NULL') 	$fieldvalue = "'$fieldvalue'";
					if ($key=='ORG_ID') 	$fieldvalue = $TMP_ORG_ID;
					if ($key=='ORG_ID_1') 	$fieldvalue = $TMP_ORG_ID_1;
					if ($key=='ORG_ID_2') 	$fieldvalue = $TMP_ORG_ID_2;
					$field_name .= (trim($field_name) != "")? ", " . $key : $key;
					// ทดสอบว่าเป็น field ที่ไม่ต้องการ insert ค่าลงใน table ใช่หรือไม่
					if ( in_array($key, array_keys($arrfield_except_per_position)) )		
						$field_value .= (trim($field_value) != "")? ", " . $arrfield_except_per_position[$key] : $arrfield_except_per_position[$key];
					else
						$field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
				}
                                set_constraints($db_dpis1,$db_dpis2,'PER_POSITION','DISABLE');/*Release 5.2.1.5 ...*/
                                
				$cmd = "	INSERT INTO PER_POSITION ( POS_ID, DEPARTMENT_ID, POS_NO_INT, $field_name ) 
									VALUES ( $POS_ID, $DEPARTMENT_ID, $POS_NO_INT, $field_value )";	
				$db_dpis->send_cmd_fast($cmd);
                                
                                set_constraints($db_dpis1,$db_dpis2,'PER_POSITION','ENABLE');/*Release 5.2.1.5 ...*/
				$chkErr=$db_dpis->ERROR;
				if(!$chkErr){
					echo '<font color=red>ไม่สามารถนำเข้าข้อมูล ตำแหน่งข้าราชการได้ <br>['.$db_dpis->show_error().']</font><br>-----------------------<br>';
				}
//				$db_dpis->show_error();
//				echo "<br><u>PER_POSITION:</u><b>$POS_ID.</b> $cmd<br>===================<br>";
				$POS_ID++;
			}  // end while 
		}  // end if 

		$table = array(	"PER_POSITIONHIS", "PER_SALARYHIS", "PER_EXTRAHIS", 
				"PER_EDUCATE", "PER_TRAINING", "PER_ABILITY", 
				"PER_SPECIAL_SKILL", "PER_HEIR", "PER_ABSENTHIS", 
				"PER_PUNISHMENT", "PER_SERVICEHIS", "PER_REWARDHIS", 
				"PER_MARRHIS", "PER_NAMEHIS", "PER_DECORATEHIS", 
				"PER_TIMEHIS", "PER_EXTRA_INCOMEHIS" , "PER_ADDRESS" , "PER_FAMILY");

		$arrfield_except_per_personal = array( 
			"ORG_ID" => "NULL", 
			"POS_ID" => "NULL", 
			"POEM_ID" => "NULL", 
			"POEMS_ID" => "NULL" );

		// =====================================================
		// ===== นำข้อมูลเข้า PER_PERSONAL ก่อน table อื่น =====
		// ===== select ชื่อ fields จาก $table ===== 
		$cmd = " select * from PER_PERSONAL where rownum = 1 ";
		$db_dpis->send_cmd_fast($cmd);
		$field_list = $db_dpis->list_fields($table);
		// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
		unset($arr_fields);
		if ($DPISDB=="odbc" || $DPISDB=="oci8") {
			for($i=1; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		}else{
			for($i=0; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		} // end if
		//echo "<pre>".print_r($field_list)."</pre>";
//		echo "<br>=>".count($arr_fields)."+".count($arr_fields_type)."<br>";
		// =====================================================
		// ===== insert into PER_PERSONAL =====
		$loop=1;
		$cmd = " select max(PER_ID) as MAX_ID from PER_PERSONAL ";
		$db_dpis2->send_cmd_fast($cmd);
		$data2 = $db_dpis2->get_array();
		//$PER_ID= $data2[MAX_ID] + 1;
		$LAST_PER_ID= $data2[MAX_ID];
		$DEPARTMENT_ID = $SESS_DEPARTMENT_ID;
		if (!$DEPARTMENT_ID) $DEPARTMENT_ID = "NULL";

		$db_textfile = new connect_file("PER_PERSONAL", "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");

//$log_file =  fopen("PER_PERSONAL".".sql", 'w');
                //เพิ่มเช็ค นำเข้าเฉพาะที่คอลั่มปลายทางมี
                $xsql = "select COLUMN_NAME from user_tab_cols where table_name='PER_PERSONAL'";
                //echo "xxx=>".$xsql."<br>";
                $db_dpis2->send_cmd($xsql);
                $xexist = array();
                while ($x = $db_dpis2->get_array()){
                    array_push($xexist,$x[COLUMN_NAME]);
                }
                //var_dump($xexist);
		while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
			$ctext = count($data);
                        //echo '<br>loop=>'.$loop.'<br>';
			$oldperid=$data[PER_ID];
			$PER_ID[$oldperid]=($LAST_PER_ID+$loop);
			//echo "<br>old: $oldperid - new:  $PER_ID[$oldperid]<br> ";

			// ลบค่าข้อมูล field PER_ID ที่ได้จาก textfile ออก เพื่อใส่ค่า max ตัวใหม่
			//ถ้า unset เท่ากับลบ field หายไป 3 fields และใน $field_name, $field_value ค่าเหล่านี้จะหายไปด้วย
			unset($data[PER_ID], $field_name, $field_value);
			unset($data[DEPARTMENT_ID], $field_name, $field_value);
			///unset($data[PER_OFFNO], $field_name, $field_value);   //field นี้จะหายและไม่ครบในการ insert ซึ่งมันเป็น filed อยู่กลาง
			if(isset($data[PER_OFFNO]))	$data[PER_OFFNO]="NULL";
			$PER_NAME = str_replace("'", "", $data[PER_NAME]) ." ". str_replace("'", "", $data[PER_SURNAME]);
			$ARR_PER_NAME[]=$PER_NAME;
                        
			
                        
			// ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields
			foreach ($data as $key => $fieldvalue) {
				if ($key != "PERSON_ID" && $key != "BIRTH_DATE" && $key != "START_DATE" && $key != "OCCUPY_DATE" && $key != "UPDATE_STATUS") {
					//echo "<br>$key => $fieldvalue<br>";
                                    
                                 
//                                    $xsql = "select 1 X from user_tab_cols where table_name='PER_PERSONAL' and column_name=upper('" . $key ."')";
//                                    //echo "xxx=>".$xsql."<br>";
//                                    $db_dpis2->send_cmd($xsql);
//                                    $xexist = $db_dpis2->get_array();
//                                    //print_r($xexist);
                                    
                                     //เพิ่มเช็ค นำเข้าเฉพาะที่คอลั่มปลายทางมี
                                     if (in_array($key , $xexist)) { //if ($xexist[X] == 1) {
                                    
                                    
					$fieldvalue = str_replace("<br>", "\n", $fieldvalue);
					if ($key=='UPDATE_DATE' && $fieldvalue=='NULL') {
						$fieldvalue = date("Y-m-d H:i:s");
						$fieldvalue = "'$fieldvalue'";
					}
					if (($key=='MAH_MARRY_DATE' || $key=='MV_DATE' || $key=='POH_EFFECTIVEDATE' || $key=='POH_DOCDATE' || $key=='SAH_EFFECTIVEDATE' || $key=='SAH_DOCDATE') && $fieldvalue=='NULL') {
						$fieldvalue = "1957-01-01";
						$fieldvalue = "'$fieldvalue'";
					}
					if (($key=='POH_REMARK' || $key=='POH_POS_NO' || $key=='POH_DOCNO' || $key=='SAH_DOCNO') && $fieldvalue=='NULL') {
						$fieldvalue = "-";
						$fieldvalue = "'$fieldvalue'";
					}
					$field_name .= (trim($field_name) != "")? ", " . $key : $key;
					// ทดสอบว่าเป็น field ที่ไม่ต้องการ insert ค่าลงใน table ใช่หรือไม่
					if (in_array($key, array_keys($arrfield_except_per_personal))){
						$field_value .= (trim($field_value) != "")? ", " . $arrfield_except_per_personal[$key] : $arrfield_except_per_personal[$key];
					}else{
						$field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
					}
                                    }    
                                    
                                        
				} // end if	
			} // end for	
                        $msg_step_import.=checkFld($db_dpis2,$field_name,'PER_PERSONAL',$rtn=0).'<br>';/*Release 5.2.1.5*/
                        
                        
                        //$field_value_new=get_val_mst($db_dpis2,$TableName_FK,'PER_PERSONAL',$field_name,$field_value).'<br><br>';
                        //echo '<br><font color=red>ก่อน>>>'.$field_value.'</font><br>';
                        //if(0==1){$field_value=$field_value_new;}    
                        //echo '<br><font color=green>หลัง>>>'.$field_value_new.'</font><br>';
			
                        set_constraints($db_dpis1,$db_dpis2,'PER_PERSONAL','DISABLE');/*Release 5.2.1.5 ...*/
                        
                        $cmd = " INSERT INTO PER_PERSONAL ( PER_ID, DEPARTMENT_ID, $field_name ) 
                                VALUES ($PER_ID[$oldperid], $DEPARTMENT_ID, $field_value ) ";	
                        //fwrite($log_file, $cmd . "\r\n");
                        //echo '<pre>'.$cmd;
			$db_dpis->send_cmd_fast($cmd); //comment test
                        
                        set_constraints($db_dpis1,$db_dpis2,'PER_PERSONAL','ENABLE');/*Release 5.2.1.5 ...*/
                        
			$chkErr=$db_dpis->ERROR;
			if(!$chkErr){
				echo '<font color=red>ไม่สามารถนำเข้าข้อมูล ข้าราชการได้ <br>['.$db_dpis->show_error().']</font><br>-----------------------<br>';
			}
//			$db_dpis->show_error();

			$atestfield_name=explode(",",$field_name);
			$atestfield_value=explode(",",$field_value);
			//+2 fields คือ PER_ID/DEPARTMENT_ID ที่ถูก unset ไป และเอาค่าใหม่ใส่เข้ามา เพราะมันอยู่หลัง function นี้
			$cloop = count($data)+2; $nfield_name=count($atestfield_name)+2;	$nfield_value=count($atestfield_value)+2;
//			echo "<br><u>PER_PERSONAL:</u>[".$ctext."-".$cloop."-".$nfield_name."+".$nfield_value."] <b>$j.</b> $cmd<br>===================<br>";
		//$PER_ID++;	//***********ถ้าบวก PER_ID เพิ่มไป 1 เวลา insert query ถัดไป PER_ID ถูกเพิ่มด้วย และจะไม่ตรงกัน 
		$loop++;
		}  // end while 

//fclose($log_file);

		// ===== end นำข้อมูลเข้า PER_PERSONAL ก่อน =====
		// =====================================================

		// =========================================================================
		// ===== วนลูปตาม array table ที่เหลือโดยอิง PER_ID กับ PER_PERSONAL ที่ insert ด้านบ้าน =====
		//print_r($PER_ID);
		for ( $i=0; $i<count($table); $i++ ) { 
			//echo "<b>:: $table[$i] :: </b><br>";
			// ===== select ชื่อ fields จาก $table ===== 
			$cmd = " select * from $table[$i] where rownum=1";
			$db_dpis->send_cmd_fast($cmd);
			$field_list = $db_dpis->list_fields($table);
			// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
			unset($arr_fields);
			if ($DPISDB=="odbc" || $DPISDB=="oci8") {
				for($j=1; $j<=count($field_list); $j++) : 
					$arr_fields[] = $tmp_name = $field_list[$j]["name"];
					$arr_fields_type[$tmp_name] = $field_list[$j]["type"];
				endfor;
			} // end if

			// ===== นำข้อมูล fields จาก textfile write ลง db
			$db_textfile = new connect_file("$table[$i]", "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");
                        $tbname = '';

//$log_file =  fopen("$table[$i]".".sql", 'w');

			while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
				$oldperid = $data[PER_ID];
//				echo "<br>old: $oldperid- new:  $PER_ID[$oldperid]<br> ";
                                
				switch($table[$i]){
					case "PER_POSITIONHIS" :
                                            $tbname = 'ประวัติการดำรงตำแหน่ง';
						$cmd = " select max(POH_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd_fast($cmd);
						//$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						$POH_ID = $data2[MAX_ID] + 1;

						$key_POH_ID = array_search('POH_ID', $arr_fields); 
						unset($arr_fields[$key_POH_ID], $data[PER_ID], $data[POH_ID], $data[ORG_ID_1], $data[ORG_ID_2], $data[ORG_ID_3]);
						$insert_field = "PER_ID, POH_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ";
						$insert_value = "$PER_ID[$oldperid], $POH_ID, $DEPARTMENT_ID, $DEPARTMENT_ID, $DEPARTMENT_ID, ";	
						break;
					case "PER_SALARYHIS" :
                                            $tbname = 'ประวัติการรับเงินเดือน';
						$cmd = " select max(SAH_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd_fast($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$SAH_ID = $data2[MAX_ID] + 1;
				
						$key_SAH_ID = array_search('SAH_ID', $arr_fields); 
						unset($arr_fields[$key_SAH_ID], $data[PER_ID], $data[SAH_ID]);
						$insert_field = "PER_ID, SAH_ID, ";
						$insert_value = "$PER_ID[$oldperid], $SAH_ID, ";	
						break;
					case "PER_EXTRAHIS" :
                                            $tbname = 'ประวัติการรับเงินเพิ่มพิเศษ';
						$cmd = " select max(EXH_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd_fast($cmd);
						$data2 = $db_dpis2->get_array();
						//$db_dpis2->show_error();
						$EXH_ID = $data2[MAX_ID] + 1;
				
						$key_EXH_ID = array_search('EXH_ID', $arr_fields); 
						unset($arr_fields[$key_EXH_ID], $data[PER_ID], $data[EXH_ID]);
						$insert_field = "PER_ID, EXH_ID, ";
						$insert_value = "$PER_ID[$oldperid], $EXH_ID, ";		
						break;
					case "PER_EDUCATE" :
                                            $tbname = 'ประวัติการศึกษา';
						$cmd = " select max(EDU_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd_fast($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$EDU_ID = $data2[MAX_ID] + 1;
	
						$key_EDU_ID = array_search('EDU_ID', $arr_fields); 
						unset($arr_fields[$key_EDU_ID], $data[PER_ID], $data[EDU_ID]);		
						$insert_field = "PER_ID, EDU_ID, ";
						$insert_value = "$PER_ID[$oldperid], $EDU_ID, ";	
						break;
					case "PER_TRAINING" :
                                            $tbname = 'ประวัติการอบรม ดูงาน สัมมนา';
						$cmd = " select max(TRN_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd_fast($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$TRN_ID = $data2[MAX_ID] + 1;
				
						$key_TRN_ID = array_search('TRN_ID', $arr_fields); 
						unset($arr_fields[$key_TRN_ID], $data[PER_ID], $data[TRN_ID]);		
						$insert_field = "PER_ID, TRN_ID, ";
						$insert_value = "$PER_ID[$oldperid], $TRN_ID, ";	
						break;
					case "PER_ABILITY" :
                                            $tbname = 'ความสามารถพิเศษ';
						$cmd = " select max(ABI_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd_fast($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$ABI_ID = $data2[MAX_ID] + 1;
				
						$key_ABI_ID = array_search('ABI_ID', $arr_fields); 
						unset($arr_fields[$key_ABI_ID], $data[PER_ID], $data[ABI_ID]);	
						$insert_field = "PER_ID, ABI_ID, ";
						$insert_value = "$PER_ID[$oldperid], $ABI_ID, ";	
						break;
					case "PER_SPECIAL_SKILL" :
                                            $tbname = 'ประวัติความเชี่ยวชาญพิเศษ';
						$cmd = " select max(SPS_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd_fast($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$SPS_ID = $data2[MAX_ID] + 1;

						$key_SPS_ID = array_search('SPS_ID', $arr_fields); 
						unset($arr_fields[$key_SPS_ID], $data[PER_ID], $data[SPS_ID]);
						$insert_field = "PER_ID, SPS_ID, ";
						$insert_value = "$PER_ID[$oldperid], $SPS_ID, ";	
						break;
					case "PER_HEIR" :
                                            $tbname = 'ประเภททายาทผู้รับผลประโยชน์';
						$cmd = " select max(HEIR_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd_fast($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$HEIR_ID = $data2[MAX_ID] + 1;
				
						$key_HEIR_ID = array_search('HEIR_ID', $arr_fields); 
						unset($arr_fields[$key_HEIR_ID], $data[PER_ID], $data[HEIR_ID]);
						$insert_field = "PER_ID, HEIR_ID, ";
						$insert_value = "$PER_ID[$oldperid], $HEIR_ID, ";	
						break;
					case "PER_ABSENTHIS" :
                                            $tbname = 'ประวัติการลา';
						$cmd = " select max(ABS_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd_fast($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$ABS_ID = $data2[MAX_ID] + 1;
				
						$key_ABS_ID = array_search('ABS_ID', $arr_fields); 
						unset($arr_fields[$key_ABS_ID], $data[PER_ID], $data[ABS_ID]);
						$insert_field = "PER_ID, ABS_ID, ";
						$insert_value = "$PER_ID[$oldperid], $ABS_ID, ";	
						break;
					case "PER_PUNISHMENT" :
                                            $tbname = 'ประวัติทางวินัย';
						$cmd = " select max(PUN_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd_fast($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$PUN_ID = $data2[MAX_ID] + 1;
				
						$key_PUN_ID = array_search('PUN_ID', $arr_fields); 
						unset($arr_fields[$key_PUN_ID], $data[PER_ID], $data[PUN_ID]);	
						$insert_field = "PER_ID, PUN_ID, ";
						$insert_value = "$PER_ID[$oldperid], $PUN_ID, ";
						break;
					case "PER_SERVICEHIS" :
                                            $tbname = 'ราชการพิเศษ';
						$cmd = " select max(SRH_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd_fast($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$SRH_ID = $data2[MAX_ID] + 1;
				
						$key_SRH_ID = array_search('SRH_ID', $arr_fields); 
						unset($arr_fields[$key_SRH_ID], $data[PER_ID], $data[SRH_ID]);
						$insert_field = "PER_ID, SRH_ID, ";
						$insert_value = "$PER_ID[$oldperid], $SRH_ID, ";
						break;
					case "PER_REWARDHIS" :
                                            $tbname = 'ประวัติการรับความดีความชอบ';
						$cmd = " select max(REH_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd_fast($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$REH_ID = $data2[MAX_ID] + 1;
				
						$key_REH_ID = array_search('REH_ID', $arr_fields); 
						unset($arr_fields[$key_REH_ID], $data[PER_ID], $data[REH_ID]);
						$insert_field = "PER_ID, REH_ID, ";
						$insert_value = "$PER_ID[$oldperid], $REH_ID, ";				
						break;
					case "PER_MARRHIS" :
                                            $tbname = 'ประวัติการสมรส';
						$cmd = " select max(MAH_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd_fast($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$MAH_ID = $data2[MAX_ID] + 1;
				
						$key_MAH_ID = array_search('MAH_ID', $arr_fields); 
						unset($arr_fields[$key_MAH_ID], $data[PER_ID], $data[MAH_ID]);	
						$insert_field = "PER_ID, MAH_ID, ";
						$insert_value = "$PER_ID[$oldperid], $MAH_ID, ";
						break;
					case "PER_NAMEHIS" :
                                            $tbname = 'ประวัติการเปลี่ยนแปลงชื่อ-สกุล';
						$cmd = " select max(NH_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd_fast($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$NH_ID = $data2[MAX_ID] + 1;
				
						$key_NH_ID = array_search('NH_ID', $arr_fields); 
						unset($arr_fields[$key_NH_ID], $data[PER_ID], $data[NH_ID]);
						$insert_field = "PER_ID, NH_ID, ";
						$insert_value = "$PER_ID[$oldperid], $NH_ID, ";
						break;
					case "PER_DECORATEHIS" :
                                            $tbname = 'ประวัติการรับเครื่องราชฯ';
						$cmd = " select max(DEH_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd_fast($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$DEH_ID = $data2[MAX_ID] + 1;
				
						$key_DEH_ID = array_search('DEH_ID', $arr_fields); 
						unset($arr_fields[$key_DEH_ID], $data[PER_ID], $data[DEH_ID]);
						$insert_field = "PER_ID, DEH_ID, ";
						$insert_value = "$PER_ID[$oldperid], $DEH_ID, ";
						break;
					case "PER_TIMEHIS" :
                                            $tbname = 'ประวัติเวลาทวีคูณ';
						$cmd = " select max(TIMEH_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd_fast($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$TIMEH_ID = $data2[MAX_ID] + 1;
				
						$key_TIMEH_ID = array_search('TIMEH_ID', $arr_fields); 
						unset($arr_fields[$key_TIMEH_ID], $data[PER_ID], $data[TIMEH_ID]);
						$insert_field = "PER_ID, TIMEH_ID, ";
						$insert_value = "$PER_ID[$oldperid], $TIMEH_ID, ";				
						break;
					case "PER_EXTRA_INCOMEHIS" :
                                            $tbname = 'ประวัติการรับเงินพิเศษ';
						$cmd = " select max(EXINH_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd_fast($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$EXINH_ID = $data2[MAX_ID] + 1;
				
						$key_EXINH_ID = array_search('EXINH_ID', $arr_fields); 
						unset($arr_fields[$key_EXINH_ID], $data[PER_ID], $data[EXINH_ID]);
						$insert_field = "PER_ID, EXINH_ID, ";
						$insert_value = "$PER_ID[$oldperid], $EXINH_ID, ";
						break;
                                        case "PER_ADDRESS" :
                                            $tbname = 'ที่อยู่';
						$cmd = " select max(ADR_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd_fast($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$ADR_ID = $data2[MAX_ID] + 1;
				
						$key_ADR_ID = array_search('ADR_ID', $arr_fields); 
						unset($arr_fields[$key_ADR_ID], $data[PER_ID], $data[ADR_ID]);
						$insert_field = "PER_ID, ADR_ID, ";
						$insert_value = "$PER_ID[$oldperid], $ADR_ID, ";
						break;    
                                         case "PER_FAMILY" :
                                            $tbname = 'ข้อมูลครอบครัว';
						$cmd = " select max(FML_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd_fast($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$FML_ID = $data2[MAX_ID] + 1;
				
						$key_FML_ID = array_search('FML_ID', $arr_fields); 
						unset($arr_fields[$key_FML_ID], $data[PER_ID], $data[FML_ID]);
						$insert_field = "PER_ID, FML_ID, ";
						$insert_value = "$PER_ID[$oldperid], $FML_ID, ";
						break;  
                                                
				} // end switch case
		
			// ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields ตาม texfile 
			unset ($field_name, $field_value);
			foreach ($data as $key => $fieldvalue) {	
				if ($key != "EFFECTIVE_DATE" && $key != "DOC_DATE" && $key != "UPDATE_STATUS" && $key != "TEMP_ORG3" && 
					$key != "RECEIVE_DATE" && $key != "RETURN_DATE" && $key != "MODIFIED_STATUS") {
					$fieldvalue = str_replace("<br>", "\n", $fieldvalue);
					if ($key=='UPDATE_DATE' && $fieldvalue=='NULL') {
						$fieldvalue = date("Y-m-d H:i:s");
						$fieldvalue = "'$fieldvalue'";
					}
					if (($key=='MAH_MARRY_DATE' || $key=='MV_DATE' || $key=='POH_EFFECTIVEDATE' || $key=='POH_DOCDATE' || $key=='SAH_EFFECTIVEDATE' || 			
						$key=='SAH_DOCDATE') && $fieldvalue=='NULL') {
						$fieldvalue = "1957-01-01";
						$fieldvalue = "'$fieldvalue'";
					}
					if (($key=='POH_REMARK' || $key=='POH_POS_NO' || $key=='POH_DOCNO' || $key=='SAH_DOCNO') && $fieldvalue=='NULL') {
						$fieldvalue = "-";
						$fieldvalue = "'$fieldvalue'";
					}
					if ($key=='EN_CODE') {
						$cmd = " select EN_NAME from  PER_EDUCNAME where EN_CODE = $fieldvalue ";
						$count_data = $db_dpis2->send_cmd_fast($cmd);
		//				$db_dpis2->show_error();
						if (!$count_data) $fieldvalue = "'0010000'";
					}
					$field_name .= (trim($field_name) != "")? ", " . $key : $key;
					$field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
				} // end if		
			} // end for		
                        
                        //$fldName=$insert_field.$field_name;
                        //$msg_step_import.=checkFld($db_dpis2,$fldName,$table[$i],$rtn=0).'<br>';/*Release 5.2.1.5*/
		// ===== insert ข้อมูลลง database =====
                        //echo 'ก่อน>>>'.$field_value.'<br><br>+++++++++++++++++++++++++++++++++++';
                        //$field_value_new=get_val_mst($db_dpis1,$TableName_FK,$table[$i],$field_name,$field_value).'<br><br>';
                        //echo '<br><font color=red>หลัง>>>'.$field_value_new.'</font><br>';
                        //if(0==1){$field_value=$field_value_new;}
                        set_constraints($db_dpis1,$db_dpis2,$table[$i],'DISABLE');/*Release 5.2.1.5 ...*/
                        
			$cmd = " INSERT INTO ".$table[$i]." ( $insert_field $field_name ) VALUES ( $insert_value $field_value ) ";
//fwrite($log_file, $cmd . "\r\n");
                        //echo '<pre>'.$cmd;
			$db_dpis->send_cmd_fast($cmd); //comment test
                        
                        set_constraints($db_dpis1,$db_dpis2,$table[$i],'ENABLE');/*Release 5.2.1.5 ...*/
			$chkErr=$db_dpis->ERROR;
			if(!$chkErr){
//				echo '<font color=red>ไม่สามารถนำเข้าข้อมูล '.$table[$i].'ได้้ <br>['.$db_dpis->execute_error().']</font><br>-----------------------<br>';
				//echo '<font color=red>ไม่สามารถนำเข้าข้อมูล '.$table[$i].'ได้ <br>['.$cmd.']</font><br>-----------------------<br>';
                                echo  '<font color=red>ไม่สามารถนำเข้าข้อมูล '.$table[$i].'ได้ <br>['.$db_dpis->show_error().']</font><br>-----------------------<br>';
			}
//			$db_dpis->show_error();
//			echo "<br><u>$table[$i]:</u><b>$j.</b> $cmd<br>===================<br>";	
		}  // end while 

//fclose($log_file);

	} 	// endif for ($i=0; $i<=count($table); $i++)
	unset($data, $arr_fields, $field_name, $field_value);
	
	$path_toshow = stripslashes($path_toshow);
	} // endif command==CONVERT
?>