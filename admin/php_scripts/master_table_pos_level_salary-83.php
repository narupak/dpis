<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
     $db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$table = "PER_POS_LEVEL_SALARY";
	
	$cmd = " select * from $table ";
	$db_dpis->send_cmd($cmd);
	$field_list = $db_dpis->list_fields($table);
//	echo "<pre>";		print_r($field_list);		echo "</pre>";
	unset($arr_fields);
	if($DPISDB=="odbc" || $DPISDB=="oci8"){
		for($i=1; $i<=count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	}elseif($DPISDB=="mysql"){
		for($i=0; $i<count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	} // end if
//	echo (implode(", ", $arr_fields));
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
        $var_gr = implode("," ,$group_salary) ; 
        
if($command == "CHANGE_SALARY"){

                // blackup ข้อมูล PER_POS_LEVEL_SALARY
                  $cmdChk ="  SELECT COUNT(COLUMN_NAME) AS CNT
                              FROM USER_TAB_COLS
                              WHERE  TABLE_NAME = 'PER_POS_LEVEL_SALARY_BK' ";

                  $db_dpis->send_cmd($cmdChk);
                  $dataChk = $db_dpis->get_array();
                if($dataChk[CNT]=="0"){
                      $cmdA = "CREATE TABLE PER_POS_LEVEL_SALARY_BK  AS  SELECT * FROM PER_POS_LEVEL_SALARY";
                      $db_dpis->send_cmd($cmdA);
                      $cmdA = "COMMIT";
                      $db_dpis->send_cmd($cmdA);
                }
                
        // ล้าง ข้อมูลทั้ง table         
             $cmdA = "TRUNCATE TABLE PER_POS_LEVEL_SALARY ";
                  $db_dpis->send_cmd($cmdA);
                  $cmdA = "COMMIT";
                  $db_dpis->send_cmd($cmdA);       
          
//========================== ALTER TABLE GROUP_SALARY และ UP_SALARY ===========================

        $cmdChk ="  SELECT COUNT(COLUMN_NAME) AS CNT
                      FROM USER_TAB_COLS
                      WHERE  TABLE_NAME = 'PER_POS_LEVEL_SALARY' 
                                            AND UPPER(COLUMN_NAME) IN('GROUP_SALARY')";   

          $db_dpis->send_cmd($cmdChk);
          $dataChk = $db_dpis->get_array();
          if($dataChk[CNT]=="0"){
         $cmdA = "ALTER TABLE PER_POS_LEVEL_SALARY  ADD  GROUP_SALARY VARCHAR2(10)";

              $db_dpis->send_cmd($cmdA);
              $cmdA = "COMMIT";
              $db_dpis->send_cmd($cmdA);
          } 
          
 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++         
         $cmdChk ="  SELECT COUNT(COLUMN_NAME) AS CNT
                      FROM USER_TAB_COLS
                      WHERE  TABLE_NAME = 'PER_POS_LEVEL_SALARY 'AND 
                                            UPPER(COLUMN_NAME)IN('UP_SALARY')";
                         

          $db_dpis->send_cmd($cmdChk);
          $dataChk = $db_dpis->get_array();
          if($dataChk[CNT]=="0"){
          $cmdA = "ALTER TABLE PER_POS_LEVEL_SALARY  ADD  UP_SALARY NUMBER(16,2)";

              $db_dpis->send_cmd($cmdA);
              $cmdA = "COMMIT";
              $db_dpis->send_cmd($cmdA);
          }

            
    $cmd = "SELECT count(PN_CODE)AS IM  FROM PER_POS_LEVEL_SALARY"; 
     $db_dpis->send_cmd($cmd);
     $data = $db_dpis->get_array();
     $IM = $data[IM];
     if($IM == 0){
                $data = "php_scripts/insert_data_ps.txt";  
                $Fopen = fopen($data, 'r');
                if ($Fopen) {
                    while (!feof($Fopen)) {
                        $file = fgets($Fopen, 4096);
                        //print_r($file); echo "<br/>\n"; 
                        $db_dpis->send_cmd($file);       
                    }
                        fclose($Fopen);
                }
     }
     
     $cmd = " SELECT LAYERE_NO FROM PER_LAYEREMP where PG_CODE = 4000 AND LAYERE_NO BETWEEN 29 AND 29.5"; 
     $db_dpis->send_cmd($cmd);
     $data = $db_dpis->get_array();
     $LAYERE_NO = $data[LAYERE_NO]; 
     if(!$LAYERE_NO){
                $data = "php_scripts/insert_per_layere_emp.txt";  
                $Fopen = fopen($data, 'r');
                if ($Fopen) {
                    while (!feof($Fopen)) {
                        $file = fgets($Fopen, 4096);
                       //print_r($file); echo "<br/>\n";
                       $db_dpis->send_cmd($file);  
                    }
                        fclose($Fopen);
                }

     }
}//end if CHANGE_SALARY

 


	if($command == "ADD" && trim($$arr_fields[0])){
		$cmd = " 	select $arr_fields[0], $arr_fields[1] from $table 
						where $arr_fields[0]='". trim($$arr_fields[0]) ."' and $arr_fields[1]='". trim($$arr_fields[1]) ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
                
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") 
							  values ('".trim($$arr_fields[0])."', '".trim($$arr_fields[1])."', ".$$arr_fields[2].", ".$$arr_fields[3]." , $SESS_USERID, '$UPDATE_DATE', '".$var_gr."','$UP_SALARY' ) ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			//echo $cmd; 
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]."]";
		} // endif
            
	}

	if($command == "UPDATE" && trim($$arr_fields[0])){
 
		$cmd = " 	select $arr_fields[0], $arr_fields[1] from $table 
						where $arr_fields[0]='". trim($$arr_fields[0]) ."' and $arr_fields[1]=". trim($$arr_fields[1]);
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " 	update $table set 
								$arr_fields[0]='".$$arr_fields[0]."', 
								$arr_fields[1]='".$$arr_fields[1]."', 
								$arr_fields[2]=".$MIN_SALARY.", 
								$arr_fields[3]=".$MAX_SALARY.", 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE',
                                                                GROUP_SALARY='$var_$var_gr',
                                                                UP_SALARY='$UP_SALARY'
							where $arr_fields[0]='".$upd_pn_code."' and $arr_fields[1]='".$upd_level_no."'";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			//echo $cmd;
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [จาก->".trim($$arr_fields[0])." : ".$$arr_fields[1]." เป็น->$upd_pn_code : $upd_level_no]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]."]";
			$UPD = 1;
		} // endif
          
	}
        if($command == "UPD_SALARY"){
        
                    $cmd = " select a.PN_CODE, a.LEVEL_NO, MIN_SALARY, a.MAX_SALARY, a.GROUP_SALARY, a.UP_SALARY, PN_NAME, LEVEL_NAME, LEVEL_SEQ_NO 
				from PER_POS_LEVEL_SALARY a, PER_POS_NAME b, PER_LEVEL c 
				where a.PN_CODE=trim(b.PN_CODE) 
                                and a.LEVEL_NO=c.LEVEL_NO
				order by a.PN_CODE asc, a.LEVEL_NO asc";
                        $db_dpis1->send_cmd($cmd);
                    while($data1 = $db_dpis1->get_array()){
                         $PN_CODE_P      =   trim($data1[PN_CODE]);
                         $LEVEL_NO_P     =   trim($data1[LEVEL_NO]);
                         $MIN_SALARY_P   =   $data1[MIN_SALARY];
                         $MAX_SALARY_P   =   $data1[MAX_SALARY];
                         $GROUP_SALARY_P =   $data1[GROUP_SALARY];
                         $UP_SALARY_P    =   $data1[UP_SALARY];
                         $LEVEL_SEQ_NO   =   $data1[LEVEL_SEQ_NO];
                        
                        $CN_GROUP_SALARY = explode("," ,$GROUP_SALARY_P); //นำกลุ่มบัญชีที่รับเงิน มาแปลงค่า
                            $cnt=count($CN_GROUP_SALARY);
                            $val_pg_code='';
                            unset($arr_pg_code);
                            for($idx=0;$idx<$cnt;$idx++){
                                $code_gr=$CN_GROUP_SALARY[$idx]*1000;
                                if($code_gr!=0){
                                    $arr_pg_code[]=$code_gr;
                                }
                            }
                    $val_pg_code = implode(',',$arr_pg_code);
                    $end_pg_code = end($CN_GROUP_SALARY)*1000;
                   
                   $cmd =" select LAYERE_NO, LAYERE_SALARY,PG_CODE from PER_LAYEREMP  
			where PG_CODE in($val_pg_code) order by LAYERE_NO desc";
                        $db_dpis->send_cmd($cmd);
                        $data = $db_dpis->get_array();
                        $LAYERE_NO = trim($data[LAYERE_NO]);
                        $LAYERE_SALARY = $data[LAYERE_SALARY];
                        $PG_CODE = $data[PG_CODE];
                       
 //=========================================================================================================
 //
                if($MAX_SALARY_P == $LAYERE_SALARY && $PG_CODE == $end_pg_code){
                
                          $cmd= " select a.LEVEL_NO, a.LEVEL_SEQ_NO, b.MAX_SALARY, b.GROUP_SALARY
                                    from PER_LEVEL a, PER_POS_LEVEL_SALARY b
                                    where b.PN_CODE = $PN_CODE_P and b.LEVEL_NO = a.LEVEL_NO and a.LEVEL_SEQ_NO > $LEVEL_SEQ_NO
                                          and b.GROUP_SALARY is not null"; 
                                   $db_dpis->send_cmd($cmd);
                                   $data = $db_dpis->get_array();
                                   $LEVEL_SEQ_NO = $data[LEVEL_SEQ_NO];
                                   $MAX_SALARY_CH = $data[MAX_SALARY];
                            if(!$MAX_SALARY_CH) {$MAX_SALARY_CH = $LAYERE_SALARY;}
                                 
                                 //echo "[เงินอับเดท]--> ".$MAX_SALARY_CH."[]-->".$MAX_SALARY_P."<br>";
                                 
                                 
                                   
                      //echo"[อัตาขั้นสูงเท่ากลับกลุ่ม]-->> ".$MAX_SALARY_P."||".$LAYERE_SALARY."->>[$LEVEL_NO_P]"."--> "."[$end_pg_code]"."<br>";       
                
                }else{
                      //echo"[อัตาขั้นสูง**น้อยกว่ากลุ่ม]-->> ".$MAX_SALARY_P."||".$LAYERE_SALARY."->>[$LEVEL_NO_P]"."--> "."[$end_pg_code]"."<br>";
                      
                      if($PN_CODE_P == 3334 && $MAX_SALARY_P == 46470){
                       
                      
                      }
                
                
                }
                 

                               
                                    
         }//end while                           
    }//endUPD_SALARY

	
	if($command == "DELETE" && trim($$arr_fields[0])){
       
		$cmd = " select $arr_fields[1] from $table where $arr_fields[0]='".$$arr_fields[0]."' and $arr_fields[1]='".$LEVEL_NO."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		
		$cmd = " delete from $table where $arr_fields[0]='".$$arr_fields[0]."' and $arr_fields[1]='".$$arr_fields[1]."' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
	}
	
	if($UPD){
		$cmd = " select 		a.$arr_fields[0], a.$arr_fields[1], $arr_fields[2], a.$arr_fields[3], a.$arr_fields[6], PN_NAME, LEVEL_NAME, a.UPDATE_USER, a.UPDATE_DATE 
					  from 		$table a, PER_POS_NAME b, PER_LEVEL c 
					  where 		a.$arr_fields[0] = '$PN_CODE' and 
					  				a.$arr_fields[1] = '$LEVEL_NO' and 
					  				a.$arr_fields[0]=trim(b.$arr_fields[0]) and 
									a.$arr_fields[1]=c.$arr_fields[1]
					  order by 	a.$arr_fields[0], a.$arr_fields[1] ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		//echo $cmd;
		$data = $db_dpis->get_array();
                $GROUP_SALARY = $data[GROUP_SALARY];
                $var_group = explode("," ,$GROUP_SALARY );
		$$arr_fields[0] = $data[$arr_fields[0]];		
		$$arr_fields[1] = $data[$arr_fields[1]];
		$MIN_SALARY = $data[$arr_fields[2]];
		$MAX_SALARY = $data[$arr_fields[3]];
		$PN_NAME = $data[PN_NAME];
		$LEVEL_NAME = $data[LEVEL_NAME];
		$upd_pn_code = $data[$arr_fields[0]];
		$upd_level_no = $data[$arr_fields[1]];		
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$$arr_fields[0] = "";
		$$arr_fields[1] = "";
		$$arr_fields[2] = "";
		$$arr_fields[3] = "";
		$$arr_fields[4] = "";
		
		$PN_NAME = "";
		$LEVEL_NAME = "";
		$MIN_SALARY  = "";
		$MAX_SALARY = "";
                $UP_SALARY = "";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>