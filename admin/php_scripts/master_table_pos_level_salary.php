<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
     $db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$table = "PER_POS_LEVEL_SALARY";
	
	$cmd = " select PN_CODE, LEVEL_NO, MIN_SALARY, MAX_SALARY, UPDATE_USER, UPDATE_DATE, 
            GROUP_SALARY, UP_SALARY 
        from $table ";
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
     $cmd = "select MAX_SALARY, GROUP_SALARY, PN_CODE, LEVEL_NO 
                        from  PER_POS_LEVEL_SALARY ORDER by PN_CODE";
                        $db_dpis1->send_cmd($cmd);
          while($data1 = $db_dpis1->get_array()){
                         $MAX_SALARY    = $data1[MAX_SALARY];// ขั้นสูง
                         $GROUP_SALARY  = $data1[GROUP_SALARY];
                         $LEVEL_NO      = $data1[LEVEL_NO];
                         $PN_CODE       = trim($data1[PN_CODE]);
                         
              if($MAX_SALARY == 25670 ){
                $cmd= " UPDATE $table SET UP_SALARY = 34110
                                where PN_CODE = $PN_CODE and  LEVEL_NO = '$LEVEL_NO' ";
                                $db_dpis->send_cmd($cmd);    
              }
              
            if($MAX_SALARY == 41610 ){
               $cmd= " UPDATE $table SET UP_SALARY = 46470
                                where PN_CODE = $PN_CODE and  LEVEL_NO = '$LEVEL_NO' ";
                                $db_dpis->send_cmd($cmd);
           }// end 25670 and 41610   
      }//end while
//  echo"<script> window.location.reload(); </script>";
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
								$arr_fields[0]='".trim($$arr_fields[0])."', 
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
        
                   $cmd = "select MAX_SALARY, GROUP_SALARY, PN_CODE, LEVEL_NO 
                        from  PER_POS_LEVEL_SALARY ORDER by PN_CODE";
                        $db_dpis1->send_cmd($cmd);
                    while($data1 = $db_dpis1->get_array()){
                         $MAX_SALARY    = $data1[MAX_SALARY];// ขั้นสูง
                         $GROUP_SALARY  = $data1[GROUP_SALARY];
                         $LEVEL_NO      = $data1[LEVEL_NO];
                         $PN_CODE       = trim($data1[PN_CODE]);
                         //echo $LEVEL_NO."[]".$PN_CODE."<br>";
                        $CN_GROUP_SALARY = explode("," ,$GROUP_SALARY); //นำกลุ่มบัญชีที่รับเงิน มาแปลงค่า
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
        
                        $cmd =" select max(LAYERE_SALARY) as MAX_SALARY_PG 
                        from PER_LAYEREMP where PG_CODE = '$end_pg_code' ";
                        $db_dpis->send_cmd($cmd);
                        $data = $db_dpis->get_array();
                        $MAX_LAYERE_SALARY = $data[MAX_SALARY_PG];// ขั้นสุดบัญชี
                       
                       
 //======================================= START =====================================================
 
            if($MAX_SALARY == $MAX_LAYERE_SALARY){
                            
                               if($PN_CODE == 3334){// fix ระดับ ช1
                                    $MAX_LAYERE_SALARY = 54170;
                           } else{
                               $up_pg_code = ($end_pg_code)+1000;
                               if($up_pg_code == 5000) $up_pg_code = 4000;
                             $cmd =" select max(LAYERE_SALARY) as MAX_SALARY_PG 
                                from PER_LAYEREMP where PG_CODE = '$up_pg_code' ";
                                $db_dpis->send_cmd($cmd);
                                $data = $db_dpis->get_array();
                                $MAX_LAYERE_SALARY = $data[MAX_SALARY_PG];     
                           } 
                          
                          $cmd= " UPDATE $table SET UP_SALARY = $MAX_LAYERE_SALARY
                                where PN_CODE = $PN_CODE and  LEVEL_NO = '$LEVEL_NO' ";
                                $db_dpis->send_cmd($cmd);
            }else{
                                
                                if($end_pg_code == 4000 && $MAX_SALARY != 74310 && $PN_CODE !=3334){// บัญชีที่ ไม่สุดกลุ่มของกลุ่ม 4 
                                    $MAX_LAYERE_SALARY = 74310;
                                    
                                }else if($PN_CODE == 3334){// fix ระดับ ช2
                                     $MAX_LAYERE_SALARY = 54170;

                                }else{
                                    $cmd =" select max(LAYERE_SALARY) as MAX_SALARY_PG 
                                    from PER_LAYEREMP where PG_CODE = '$end_pg_code' ";
                                    $db_dpis->send_cmd($cmd);
                                    $data = $db_dpis->get_array();
                                    $MAX_LAYERE_SALARY = $data[MAX_SALARY_PG];     
                                }
                                
                             $cmd= " UPDATE $table SET UP_SALARY = $MAX_LAYERE_SALARY
                                    where PN_CODE = $PN_CODE and  LEVEL_NO = '$LEVEL_NO' ";
                                    $db_dpis->send_cmd($cmd);
             }//end if 
          
              //echo $PN_CODE."[]".$LEVEL_NO."[]".$MAX_SALARY."[]".$MAX_LAYERE_SALARY."<br>";
        }//end while                           
    }//end UPD_SALARY

    
	
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
           
		$cmd = " select 		a.$arr_fields[0], a.$arr_fields[1], $arr_fields[2], a.$arr_fields[3], a.$arr_fields[6],  $arr_fields_up PN_NAME, LEVEL_NAME, a.UPDATE_USER, a.UPDATE_DATE 
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