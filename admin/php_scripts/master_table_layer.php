<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");

	//echo "LAYER_TYPE=$LAYER_TYPE || LEVEL_NO=$LEVEL_NO || LAYER_NO=$LAYER_NO<br>";
        
        
        
        
        
        
        
	
	function list_per_level_master ($name, $val) {
		global $db_list, $DPISDB, $TR_PER_TYPE, $RPT_N, $LEVEL_TITLE, $BKK_FLAG, $ISCS_FLAG, $LIST_LEVEL_NO;
		if ($BKK_FLAG==1)
			$cmd = "	select LEVEL_NO, LEVEL_NAME FROM PER_LEVEL where PER_TYPE = 1 and LEVEL_ACTIVE = 1 order by PER_TYPE, LEVEL_SEQ_NO";
		elseif ($ISCS_FLAG==1)
			$cmd = "	select LEVEL_NO, LEVEL_NAME FROM PER_LEVEL where PER_TYPE = 1 and LEVEL_ACTIVE = 1 and LEVEL_NO in $LIST_LEVEL_NO order by PER_TYPE, LEVEL_SEQ_NO";
		else
			$cmd = "	select LEVEL_NO, LEVEL_NAME FROM PER_LEVEL order by PER_TYPE, LEVEL_SEQ_NO";
		$db_list->send_cmd($cmd);
		//$db_list->show_error();	
		if ($RPT_N) 
			echo "<select name=\"$name\" class=\"selectbox\" >
				<option value=''>== ".$LEVEL_TITLE." ==</option>";
		else
			echo "<select name=\"$name\" class=\"selectbox\" onchange=\"document.all.PROCESS_IFRAME_1.src = 'find_promote_c_comdtl_layer.html?LEVEL_NO=' + this.value\">
				<option value=''>== ".$LEVEL_TITLE." ==</option>";
		while ($data_list = $db_list->get_array()) {
			//$data_list = array_change_key_case($data_list, CASE_LOWER);
			$tmp_dat = trim($data_list[LEVEL_NO]);
			$tmp_name = trim($data_list[LEVEL_NAME]);
			$qm_arr[$tmp_dat] = $tmp_dat;
			$sel = (($tmp_dat) == trim($val))? "selected" : "";
			echo "<option value='$tmp_dat' $sel>". $tmp_name ."</option>";
		}
		echo "</select>";
		return $val;
		//echo "<pre>";		
		//print_r($data_list);
		//echo "</pre>";	
	}	

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "COPY0") {
		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 4870, LAYER_SALARY_MAX = 19100, LAYER_SALARY_MIDPOINT = 13715, 
						LAYER_SALARY_MIDPOINT1 = 11020, LAYER_SALARY_MIDPOINT2 = 16410
						where LAYER_TYPE = 0 and LEVEL_NO = 'O1' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 10190, LAYER_SALARY_MAX = 35220, LAYER_SALARY_MIDPOINT = 22715, 
						LAYER_SALARY_MIDPOINT1 = 16450, LAYER_SALARY_MIDPOINT2 = 28970
						where LAYER_TYPE = 0 and LEVEL_NO = 'O2' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 15410, LAYER_SALARY_MAX = 49830, LAYER_SALARY_FULL = 37830, 
						LAYER_SALARY_MIDPOINT = 32625, LAYER_SALARY_MIDPOINT1 = 29550, LAYER_SALARY_MIDPOINT2 = 41230, 
						LAYER_EXTRA_MIDPOINT = 29555, LAYER_EXTRA_MIDPOINT1 = 29550, LAYER_EXTRA_MIDPOINT2 = 32230
						where LAYER_TYPE = 0 and LEVEL_NO = 'O3' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 48220, LAYER_SALARY_MAX = 62760, LAYER_SALARY_MIDPOINT = 55495, 
						LAYER_SALARY_MIDPOINT1 = 51860, LAYER_SALARY_MIDPOINT2 = 59130
						where LAYER_TYPE = 0 and LEVEL_NO = 'O4' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 8340, LAYER_SALARY_MAX = 24450, LAYER_SALARY_TEMP = 7140, 
						LAYER_SALARY_MIDPOINT = 19115, LAYER_SALARY_MIDPOINT1 = 16440, LAYER_SALARY_MIDPOINT2 = 21780
						where LAYER_TYPE = 0 and LEVEL_NO = 'K1' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 15050, LAYER_SALARY_MAX = 39630, LAYER_SALARY_TEMP = 13160, 
						LAYER_SALARY_MIDPOINT = 27345, LAYER_SALARY_MIDPOINT1 = 22220, LAYER_SALARY_MIDPOINT2 = 33490
						where LAYER_TYPE = 0 and LEVEL_NO = 'K2' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 22140, LAYER_SALARY_MAX = 53080, LAYER_SALARY_TEMP = 19860, 
						LAYER_SALARY_MIDPOINT = 37615, LAYER_SALARY_MIDPOINT1 = 33510, LAYER_SALARY_MIDPOINT2 = 45350
						where LAYER_TYPE = 0 and LEVEL_NO = 'K3' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 31400, LAYER_SALARY_MAX = 62760, LAYER_SALARY_TEMP = 24400, 
						LAYER_SALARY_MIDPOINT = 47085, LAYER_SALARY_MIDPOINT1 = 46260, LAYER_SALARY_MIDPOINT2 = 54920
						where LAYER_TYPE = 0 and LEVEL_NO = 'K4' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 43810, LAYER_SALARY_MAX = 69810, LAYER_SALARY_TEMP = 29980, LAYER_SALARY_FULL = 67560, 
						LAYER_SALARY_MIDPOINT = 56815, LAYER_SALARY_MIDPOINT1 = 56020, LAYER_SALARY_MIDPOINT2 = 63310, 
						LAYER_EXTRA_MIDPOINT = 56025, LAYER_EXTRA_MIDPOINT1 = 56020, LAYER_EXTRA_MIDPOINT2 = 61630
						where LAYER_TYPE = 0 and LEVEL_NO = 'K5' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 26660, LAYER_SALARY_MAX = 54090, LAYER_SALARY_TEMP = 19860, 
						LAYER_SALARY_MIDPOINT = 40385, LAYER_SALARY_MIDPOINT1 = 33520, LAYER_SALARY_MIDPOINT2 = 47240
						where LAYER_TYPE = 0 and LEVEL_NO = 'D1' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 32850, LAYER_SALARY_MAX = 63960, LAYER_SALARY_TEMP = 24400, 
						LAYER_SALARY_MIDPOINT = 48415, LAYER_SALARY_MIDPOINT1 = 48190, LAYER_SALARY_MIDPOINT2 = 56190
						where LAYER_TYPE = 0 and LEVEL_NO = 'D2' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 51140, LAYER_SALARY_MAX = 67560, LAYER_SALARY_TEMP = 24400, 
						LAYER_SALARY_MIDPOINT = 59355, LAYER_SALARY_MIDPOINT1 = 57320, LAYER_SALARY_MIDPOINT2 = 63460
						where LAYER_TYPE = 0 and LEVEL_NO = 'M1' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 56380, LAYER_SALARY_MAX = 69810, LAYER_SALARY_TEMP = 29980, 
						LAYER_SALARY_MIDPOINT = 64735, LAYER_SALARY_MIDPOINT1 = 64730, LAYER_SALARY_MIDPOINT2 = 66460
						where LAYER_TYPE = 0 and LEVEL_NO = 'M2' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับบัญชีข้าราชการ ๖ มิ.ย. ๒๕๕๔");
	}
	
	if ($command == "COPY") {
		if ($RPT_N) {
			$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 4870, LAYER_SALARY_MAX = 21010, LAYER_SALARY_MIDPOINT = 13715, 
							LAYER_SALARY_MIDPOINT1 = 11020, LAYER_SALARY_MIDPOINT2 = 16410
							where LAYER_TYPE = 0 and LEVEL_NO = 'O1' and LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();

			$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 10190, LAYER_SALARY_MAX = 38750, LAYER_SALARY_MIDPOINT = 22715, 
							LAYER_SALARY_MIDPOINT1 = 16450, LAYER_SALARY_MIDPOINT2 = 28970
							where LAYER_TYPE = 0 and LEVEL_NO = 'O2' and LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();

			$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 15410, LAYER_SALARY_MAX = 54820, LAYER_SALARY_FULL = 37830, 
							LAYER_SALARY_MIDPOINT = 32625, LAYER_SALARY_MIDPOINT1 = 29550, LAYER_SALARY_MIDPOINT2 = 41230, 
							LAYER_EXTRA_MIDPOINT = 29555, LAYER_EXTRA_MIDPOINT1 = 29550, LAYER_EXTRA_MIDPOINT2 = 32230
							where LAYER_TYPE = 0 and LEVEL_NO = 'O3' and LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();

			$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 48220, LAYER_SALARY_MAX = 69040, LAYER_SALARY_MIDPOINT = 55495, 
							LAYER_SALARY_MIDPOINT1 = 51860, LAYER_SALARY_MIDPOINT2 = 59130
							where LAYER_TYPE = 0 and LEVEL_NO = 'O4' and LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();

			$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 8340, LAYER_SALARY_MAX = 26900, LAYER_SALARY_TEMP = 7140, 
							LAYER_SALARY_MIDPOINT = 19115, LAYER_SALARY_MIDPOINT1 = 16440, LAYER_SALARY_MIDPOINT2 = 21780
							where LAYER_TYPE = 0 and LEVEL_NO = 'K1' and LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();

			$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 15050, LAYER_SALARY_MAX = 43600, LAYER_SALARY_TEMP = 13160, 
							LAYER_SALARY_MIDPOINT = 27345, LAYER_SALARY_MIDPOINT1 = 22220, LAYER_SALARY_MIDPOINT2 = 33490
							where LAYER_TYPE = 0 and LEVEL_NO = 'K2' and LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();

			$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 22140, LAYER_SALARY_MAX = 58390, LAYER_SALARY_TEMP = 19860, 
							LAYER_SALARY_MIDPOINT = 37615, LAYER_SALARY_MIDPOINT1 = 33510, LAYER_SALARY_MIDPOINT2 = 45350
							where LAYER_TYPE = 0 and LEVEL_NO = 'K3' and LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();

			$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 31400, LAYER_SALARY_MAX = 69040, LAYER_SALARY_TEMP = 24400, 
							LAYER_SALARY_MIDPOINT = 47085, LAYER_SALARY_MIDPOINT1 = 46260, LAYER_SALARY_MIDPOINT2 = 54920
							where LAYER_TYPE = 0 and LEVEL_NO = 'K4' and LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();

			$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 43810, LAYER_SALARY_MAX = 76800, LAYER_SALARY_TEMP = 29980, LAYER_SALARY_FULL = 67560, 
							LAYER_SALARY_MIDPOINT = 56815, LAYER_SALARY_MIDPOINT1 = 56020, LAYER_SALARY_MIDPOINT2 = 63310, 
							LAYER_EXTRA_MIDPOINT = 56025, LAYER_EXTRA_MIDPOINT1 = 56020, LAYER_EXTRA_MIDPOINT2 = 61630
							where LAYER_TYPE = 0 and LEVEL_NO = 'K5' and LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();

			$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 26660, LAYER_SALARY_MAX = 59500, LAYER_SALARY_TEMP = 19860, 
							LAYER_SALARY_MIDPOINT = 40385, LAYER_SALARY_MIDPOINT1 = 33520, LAYER_SALARY_MIDPOINT2 = 47240
							where LAYER_TYPE = 0 and LEVEL_NO = 'D1' and LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();

			$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 32850, LAYER_SALARY_MAX = 70360, LAYER_SALARY_TEMP = 24400, 
							LAYER_SALARY_MIDPOINT = 48415, LAYER_SALARY_MIDPOINT1 = 48190, LAYER_SALARY_MIDPOINT2 = 56190
							where LAYER_TYPE = 0 and LEVEL_NO = 'D2' and LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();

			$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 51140, LAYER_SALARY_MAX = 74320, LAYER_SALARY_TEMP = 24400, 
							LAYER_SALARY_MIDPOINT = 59355, LAYER_SALARY_MIDPOINT1 = 57320, LAYER_SALARY_MIDPOINT2 = 63460
							where LAYER_TYPE = 0 and LEVEL_NO = 'M1' and LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();

			$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 56380, LAYER_SALARY_MAX = 76800, LAYER_SALARY_TEMP = 29980, 
							LAYER_SALARY_MIDPOINT = 64735, LAYER_SALARY_MIDPOINT1 = 64730, LAYER_SALARY_MIDPOINT2 = 66460
							where LAYER_TYPE = 0 and LEVEL_NO = 'M2' and LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();

		} else {
			$cmd = " select LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_SALARY_MIN, LAYER_SALARY_MAX, 
							LAYER_ACTIVE, LAYER_SALARY_TEMP, LAYER_SALARY_FULL, LAYER_SALARY_MIDPOINT, 
							LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, LAYER_EXTRA_MIDPOINT, LAYER_EXTRA_MIDPOINT1, 
							LAYER_EXTRA_MIDPOINT2 from PER_LAYER_NEW ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
			
			while($data = $db_dpis->get_array()){
				$data = array_change_key_case($data, CASE_LOWER);
				$layer_type = trim($data[layer_type]);
				$level_no = $data[level_no];
				$layer_no = $data[layer_no];
				$layer_salary = $data[layer_salary];
				$layer_salary_min = $data[layer_salary_min]+0;
				$layer_salary_max = $data[layer_salayer_max]+0;
				$layer_active = $data[layer_active];
				$layer_salary_temp = $data[layer_salary_temp]+0;
				$layer_salary_full = $data[layer_salayer_full]+0;
				$layer_salary_midpoint = $data[layer_salary_midpoint]+0;
				$layer_salary_midpoint1 = $data[layer_salary_midpoint1]+0;
				$layer_salary_midpoint2 = $data[layer_salary_midpoint2]+0;
				$layer_extra_midpoint = $data[layer_extra_midpoint]+0;
				$layer_extra_midpoint1 = $data[layer_extra_midpoint1]+0;
				$layer_extra_midpoint2 = $data[layer_extra_midpoint2]+0;

				$cmd = " select LAYER_SALARY from PER_LAYER 
								where LAYER_TYPE=$layer_type and LEVEL_NO='$level_no' and LAYER_NO=$layer_no ";
				$count_data = $db_dpis2->send_cmd($cmd);
				if ($count_data)
					$cmd = " update PER_LAYER set LAYER_SALARY = $layer_salary, 
																						LAYER_SALARY_MIN = $layer_salary_min, 
																						LAYER_SALARY_MAX = $layer_salary_max, 
																						LAYER_ACTIVE = $layer_active, 
																						LAYER_SALARY_TEMP = $layer_salary_temp, 
																						LAYER_SALARY_FULL = $layer_salary_full, 
																						LAYER_SALARY_MIDPOINT = $layer_salary_midpoint, 
																						LAYER_SALARY_MIDPOINT1 = $layer_salary_midpoint1, 
																						LAYER_SALARY_MIDPOINT2 = $layer_salary_midpoint2,
																						LAYER_EXTRA_MIDPOINT = $layer_extra_midpoint, 
																						LAYER_EXTRA_MIDPOINT1 = $layer_extra_midpoint1, 
																						LAYER_EXTRA_MIDPOINT2 = $layer_extra_midpoint2
									where LAYER_TYPE=$layer_type and LEVEL_NO='$level_no' and LAYER_NO=$layer_no ";
				else
					$cmd = " insert into PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_SALARY_MIN,			
									LAYER_SALARY_MAX, LAYER_ACTIVE, LAYER_SALARY_TEMP, LAYER_SALARY_FULL, 
									LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, 
									LAYER_EXTRA_MIDPOINT, LAYER_EXTRA_MIDPOINT1, LAYER_EXTRA_MIDPOINT2, UPDATE_USER, 
									UPDATE_DATE) 
									values ($layer_type, '$level_no', $layer_no, $layer_salary, $layer_salary_min, $layer_salary_max, $layer_active, 
									$layer_salary_temp, $layer_salary_full, $layer_salary_midpoint, $layer_salary_midpoint1, $layer_salary_midpoint2, 
									$layer_extra_midpoint, $layer_extra_midpoint1, $layer_extra_midpoint2, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();

			} // end while  
		} // enf if ($RPT_N)

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับบัญชีข้าราชการ ๑ ธ.ค. ๒๕๕๗");
	}
	
	if ($command == "COPYEMPSER") {
			$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 6410, LAYER_SALARY_MAX = 19430
							where LAYER_TYPE = 0 and LEVEL_NO = 'E1' and LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();

			$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 10010, LAYER_SALARY_MAX = 33360
							where LAYER_TYPE = 0 and LEVEL_NO = 'E3' and LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();

			$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 10850, LAYER_SALARY_MAX = 42830
							where LAYER_TYPE = 0 and LEVEL_NO = 'E4' and LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();

			$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 37680, LAYER_SALARY_MAX = 68350
							where LAYER_TYPE = 0 and LEVEL_NO = 'E5' and LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();

			$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 7370, LAYER_SALARY_MAX = 23970
							where LAYER_TYPE = 0 and LEVEL_NO = 'E6' and LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();

			$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 12850, LAYER_SALARY_MAX = 59790
							where LAYER_TYPE = 0 and LEVEL_NO = 'E7' and LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();

			$cmd = " update PER_LAYER set LAYER_SALARY_MAX = 109200 where LAYER_TYPE = 0 and LEVEL_NO = 'S1' and LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();

			$cmd = " update PER_LAYER set LAYER_SALARY_MAX = 163800 where LAYER_TYPE = 0 and LEVEL_NO = 'S2' and LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();

			$cmd = " update PER_LAYER set LAYER_SALARY_MAX = 218400 where LAYER_TYPE = 0 and LEVEL_NO = 'S3' and LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
                        

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับบัญชีพนักงานราชการใหม่");
	}
        
        //Release 5.0.0.43 Begin
        //ตามประกาศคณะกรรมการบริหารพนักงานราชการ เรื่อง ค่าตอบแทนของพนักงานราชการ(ฉบับที่ 8) พ.ศ. 2558
        if ($command == "COPYEMPSER_BOOK8") { //ตามประกาศคณะกรรมการบริหารพนักงานราชการ เรื่อง ค่าตอบแทนของพนักงานราชการ(ฉบับที่ 8) พ.ศ. 2558
            //กลุ่มงานบริการ
            $cmd = " update PER_LAYER set LAYER_SALARY_MIN = 6410, LAYER_SALARY_MAX = 20210
							where LAYER_TYPE = 0 and LEVEL_NO = 'E1' and LAYER_NO = 0 ";
            $db_dpis->send_cmd($cmd);
             
            //กลุ่มงานเทคนิคทั่วไป
            $cmd = " update PER_LAYER set LAYER_SALARY_MIN = 7370, LAYER_SALARY_MAX = 24930
                                            where LAYER_TYPE = 0 and LEVEL_NO = 'E2' and LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
			
            //กลุ่มงานเทคนิคพิเศษ
            $cmd = " update PER_LAYER set LAYER_SALARY_MIN = 12850, LAYER_SALARY_MAX = 59790
                                            where LAYER_TYPE = 0 and LEVEL_NO = 'E7' and LAYER_NO = 0 ";
            $db_dpis->send_cmd($cmd);
            
            //กลุ่มงานบริหารทั่วไป
            $cmd = " update PER_LAYER set LAYER_SALARY_MIN = 10010, LAYER_SALARY_MAX = 34700
                                            where LAYER_TYPE = 0 and LEVEL_NO = 'E3' and LAYER_NO = 0 ";
            $db_dpis->send_cmd($cmd);
            
            //กลุ่มงานวิชาชีพเฉพาะ
            $cmd = " update PER_LAYER set LAYER_SALARY_MIN = 10850, LAYER_SALARY_MAX = 44550
                                            where LAYER_TYPE = 0 and LEVEL_NO = 'E4' and LAYER_NO = 0 ";
            $db_dpis->send_cmd($cmd);
            
            //กลุ่มงานเชี่ยวชาญเฉพาะ
            $cmd = " update PER_LAYER set LAYER_SALARY_MIN = 37680, LAYER_SALARY_MAX = 68350
                                            where LAYER_TYPE = 0 and LEVEL_NO = 'E5' and LAYER_NO = 0 ";
            $db_dpis->send_cmd($cmd);
            
            //กลุ่มงานเชี่ยวชาญพิเศษ ระดับสากล ไม่เกิน 218400
            $cmd = " update PER_LAYER set LAYER_SALARY_MAX = 218400 where LAYER_TYPE = 0 and LEVEL_NO = 'S3' and LAYER_NO = 0 ";
            $db_dpis->send_cmd($cmd);
            //กลุ่มงานเชี่ยวชาญพิเศษ ระดับประเทศ  ไม่เกิน 163800
            $cmd = " update PER_LAYER set LAYER_SALARY_MAX = 163800 where LAYER_TYPE = 0 and LEVEL_NO = 'S2' and LAYER_NO = 0 ";
            $db_dpis->send_cmd($cmd);
            //กลุ่มงานเชี่ยวชาญพิเศษ ระดับทั่วไป ไม่เกิน 109200
            $cmd = " update PER_LAYER set LAYER_SALARY_MAX = 109200 where LAYER_TYPE = 0 and LEVEL_NO = 'S1' and LAYER_NO = 0 ";
            $db_dpis->send_cmd($cmd);

            insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับบัญชีพนักงานราชการใหม่ (ฉบับที่ 8) พ.ศ. 2558");
        }
        //Release 5.0.0.43 End ...
	
	if ($command == "COPY3") {
		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 4870, LAYER_SALARY_MAX = 21010, LAYER_SALARY_MIDPOINT = 13715, 
						LAYER_SALARY_MIDPOINT1 = 12310, LAYER_SALARY_MIDPOINT2 = 18110
						where LAYER_TYPE = 0 and LEVEL_NO = 'O1' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 10190, LAYER_SALARY_MAX = 38750, LAYER_SALARY_MIDPOINT = 22715, 
						LAYER_SALARY_MIDPOINT1 = 18480, LAYER_SALARY_MIDPOINT2 = 31610
						where LAYER_TYPE = 0 and LEVEL_NO = 'O2' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 15410, LAYER_SALARY_MAX = 54820, LAYER_SALARY_FULL = 41620, 
						LAYER_SALARY_MIDPOINT = 32625, LAYER_SALARY_MIDPOINT1 = 32250, LAYER_SALARY_MIDPOINT2 = 44970, 
						LAYER_EXTRA_MIDPOINT = 29555, LAYER_EXTRA_MIDPOINT1 = 32250, LAYER_EXTRA_MIDPOINT2 = 35070
						where LAYER_TYPE = 0 and LEVEL_NO = 'O3' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 48220, LAYER_SALARY_MAX = 69040, LAYER_SALARY_MIDPOINT = 55495, 
						LAYER_SALARY_MIDPOINT1 = 53430, LAYER_SALARY_MIDPOINT2 = 63840
						where LAYER_TYPE = 0 and LEVEL_NO = 'O4' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 7140, LAYER_SALARY_MAX = 26900, LAYER_SALARY_TEMP = 7140, 
						LAYER_SALARY_MIDPOINT = 19115, LAYER_SALARY_MIDPOINT1 = 17980, LAYER_SALARY_MIDPOINT2 = 23930
						where LAYER_TYPE = 0 and LEVEL_NO = 'K1' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 13160, LAYER_SALARY_MAX = 43600, LAYER_SALARY_TEMP = 13160, 
						LAYER_SALARY_MIDPOINT = 27345, LAYER_SALARY_MIDPOINT1 = 24410, LAYER_SALARY_MIDPOINT2 = 36470
						where LAYER_TYPE = 0 and LEVEL_NO = 'K2' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 19860, LAYER_SALARY_MAX = 58390, LAYER_SALARY_TEMP = 19860, 
						LAYER_SALARY_MIDPOINT = 37615, LAYER_SALARY_MIDPOINT1 = 37200, LAYER_SALARY_MIDPOINT2 = 49330
						where LAYER_TYPE = 0 and LEVEL_NO = 'K3' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 24400, LAYER_SALARY_MAX = 69040, LAYER_SALARY_TEMP = 24400, 
						LAYER_SALARY_MIDPOINT = 47085, LAYER_SALARY_MIDPOINT1 = 50320, LAYER_SALARY_MIDPOINT2 = 59630
						where LAYER_TYPE = 0 and LEVEL_NO = 'K4' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 29980, LAYER_SALARY_MAX = 76800, LAYER_SALARY_TEMP = 29980, LAYER_SALARY_FULL = 74320, 
						LAYER_SALARY_MIDPOINT = 56815, LAYER_SALARY_MIDPOINT1 = 60830, LAYER_SALARY_MIDPOINT2 = 68560, 
						LAYER_EXTRA_MIDPOINT = 56025, LAYER_EXTRA_MIDPOINT1 = 60830, LAYER_EXTRA_MIDPOINT2 = 66700
						where LAYER_TYPE = 0 and LEVEL_NO = 'K5' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 19860, LAYER_SALARY_MAX = 59500, LAYER_SALARY_TEMP = 19860, 
						LAYER_SALARY_MIDPOINT = 40385, LAYER_SALARY_MIDPOINT1 = 37210, LAYER_SALARY_MIDPOINT2 = 51290
						where LAYER_TYPE = 0 and LEVEL_NO = 'D1' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 24400, LAYER_SALARY_MAX = 70360, LAYER_SALARY_TEMP = 24400, 
						LAYER_SALARY_MIDPOINT = 48415, LAYER_SALARY_MIDPOINT1 = 52320, LAYER_SALARY_MIDPOINT2 = 60990
						where LAYER_TYPE = 0 and LEVEL_NO = 'D2' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 24400, LAYER_SALARY_MAX = 74320, LAYER_SALARY_TEMP = 24400, 
						LAYER_SALARY_MIDPOINT = 59355, LAYER_SALARY_MIDPOINT1 = 62210, LAYER_SALARY_MIDPOINT2 = 68530
						where LAYER_TYPE = 0 and LEVEL_NO = 'M1' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 29980, LAYER_SALARY_MAX = 76800, LAYER_SALARY_TEMP = 29980, 
						LAYER_SALARY_MIDPOINT = 64735, LAYER_SALARY_MIDPOINT1 = 69910, LAYER_SALARY_MIDPOINT2 = 71700
						where LAYER_TYPE = 0 and LEVEL_NO = 'M2' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับบัญชีข้าราชการ ๑ เมษายน ๒๕๕๘");
	}
	
	if ($command == "COPY4") {
		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 4870, LAYER_SALARY_MAX = 21010, LAYER_SALARY_MIDPOINT = 13995, 
						LAYER_SALARY_MIDPOINT1 = 12310, LAYER_SALARY_MIDPOINT2 = 18110
						where LAYER_TYPE = 0 and LEVEL_NO = 'O1' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 10190, LAYER_SALARY_MAX = 38750, LAYER_SALARY_MIDPOINT = 23175, 
						LAYER_SALARY_MIDPOINT1 = 18480, LAYER_SALARY_MIDPOINT2 = 31610
						where LAYER_TYPE = 0 and LEVEL_NO = 'O2' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 15410, LAYER_SALARY_MAX = 54820, LAYER_SALARY_FULL = 41620, 
						LAYER_SALARY_MIDPOINT = 33285, LAYER_SALARY_MIDPOINT1 = 32250, LAYER_SALARY_MIDPOINT2 = 44970, 
						LAYER_EXTRA_MIDPOINT = 30155, LAYER_EXTRA_MIDPOINT1 = 32250, LAYER_EXTRA_MIDPOINT2 = 35070
						where LAYER_TYPE = 0 and LEVEL_NO = 'O3' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 48220, LAYER_SALARY_MAX = 69040, LAYER_SALARY_MIDPOINT = 56605, 
						LAYER_SALARY_MIDPOINT1 = 53430, LAYER_SALARY_MIDPOINT2 = 63840
						where LAYER_TYPE = 0 and LEVEL_NO = 'O4' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 7140, LAYER_SALARY_MAX = 26900, LAYER_SALARY_TEMP = 7140, 
						LAYER_SALARY_MIDPOINT = 19505, LAYER_SALARY_MIDPOINT1 = 17980, LAYER_SALARY_MIDPOINT2 = 23930
						where LAYER_TYPE = 0 and LEVEL_NO = 'K1' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 13160, LAYER_SALARY_MAX = 43600, LAYER_SALARY_TEMP = 13160, 
						LAYER_SALARY_MIDPOINT = 27895, LAYER_SALARY_MIDPOINT1 = 24410, LAYER_SALARY_MIDPOINT2 = 36470
						where LAYER_TYPE = 0 and LEVEL_NO = 'K2' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 19860, LAYER_SALARY_MAX = 58390, LAYER_SALARY_TEMP = 19860, 
						LAYER_SALARY_MIDPOINT = 38375, LAYER_SALARY_MIDPOINT1 = 37200, LAYER_SALARY_MIDPOINT2 = 49330
						where LAYER_TYPE = 0 and LEVEL_NO = 'K3' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 24400, LAYER_SALARY_MAX = 69040, LAYER_SALARY_TEMP = 24400, 
						LAYER_SALARY_MIDPOINT = 48035, LAYER_SALARY_MIDPOINT1 = 50320, LAYER_SALARY_MIDPOINT2 = 59630
						where LAYER_TYPE = 0 and LEVEL_NO = 'K4' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 29980, LAYER_SALARY_MAX = 76800, LAYER_SALARY_TEMP = 29980, LAYER_SALARY_FULL = 74320, 
						LAYER_SALARY_MIDPOINT = 57955, LAYER_SALARY_MIDPOINT1 = 60830, LAYER_SALARY_MIDPOINT2 = 68560, 
						LAYER_EXTRA_MIDPOINT = 57155, LAYER_EXTRA_MIDPOINT1 = 60830, LAYER_EXTRA_MIDPOINT2 = 66700
						where LAYER_TYPE = 0 and LEVEL_NO = 'K5' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 19860, LAYER_SALARY_MAX = 59500, LAYER_SALARY_TEMP = 19860, 
						LAYER_SALARY_MIDPOINT = 41195, LAYER_SALARY_MIDPOINT1 = 37210, LAYER_SALARY_MIDPOINT2 = 51290
						where LAYER_TYPE = 0 and LEVEL_NO = 'D1' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 24400, LAYER_SALARY_MAX = 70360, LAYER_SALARY_TEMP = 24400, 
						LAYER_SALARY_MIDPOINT = 49385, LAYER_SALARY_MIDPOINT1 = 52320, LAYER_SALARY_MIDPOINT2 = 60990
						where LAYER_TYPE = 0 and LEVEL_NO = 'D2' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 24400, LAYER_SALARY_MAX = 74320, LAYER_SALARY_TEMP = 24400, 
						LAYER_SALARY_MIDPOINT = 60545, LAYER_SALARY_MIDPOINT1 = 62210, LAYER_SALARY_MIDPOINT2 = 68530
						where LAYER_TYPE = 0 and LEVEL_NO = 'M1' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 29980, LAYER_SALARY_MAX = 76800, LAYER_SALARY_TEMP = 29980, 
						LAYER_SALARY_MIDPOINT = 66035, LAYER_SALARY_MIDPOINT1 = 69910, LAYER_SALARY_MIDPOINT2 = 71700
						where LAYER_TYPE = 0 and LEVEL_NO = 'M2' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับบัญชีข้าราชการ ๑ ตุลาคม ๒๕๕๘");
	}
	
	if ($command == "COPY5") {
		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 4870, LAYER_SALARY_MAX = 21010, LAYER_SALARY_MIDPOINT = 14275, 
						LAYER_SALARY_MIDPOINT1 = 12310, LAYER_SALARY_MIDPOINT2 = 18110
						where LAYER_TYPE = 0 and LEVEL_NO = 'O1' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 10190, LAYER_SALARY_MAX = 38750, LAYER_SALARY_MIDPOINT = 23645, 
						LAYER_SALARY_MIDPOINT1 = 18480, LAYER_SALARY_MIDPOINT2 = 31610
						where LAYER_TYPE = 0 and LEVEL_NO = 'O2' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 15410, LAYER_SALARY_MAX = 54820, LAYER_SALARY_FULL = 41620, 
						LAYER_SALARY_MIDPOINT = 33955, LAYER_SALARY_MIDPOINT1 = 32250, LAYER_SALARY_MIDPOINT2 = 44970, 
						LAYER_EXTRA_MIDPOINT = 30765, LAYER_EXTRA_MIDPOINT1 = 32250, LAYER_EXTRA_MIDPOINT2 = 35070
						where LAYER_TYPE = 0 and LEVEL_NO = 'O3' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 48220, LAYER_SALARY_MAX = 69040, LAYER_SALARY_MIDPOINT = 57745, 
						LAYER_SALARY_MIDPOINT1 = 53430, LAYER_SALARY_MIDPOINT2 = 63840
						where LAYER_TYPE = 0 and LEVEL_NO = 'O4' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 7140, LAYER_SALARY_MAX = 26900, LAYER_SALARY_TEMP = 7140, 
						LAYER_SALARY_MIDPOINT = 19895, LAYER_SALARY_MIDPOINT1 = 17980, LAYER_SALARY_MIDPOINT2 = 23930
						where LAYER_TYPE = 0 and LEVEL_NO = 'K1' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 13160, LAYER_SALARY_MAX = 43600, LAYER_SALARY_TEMP = 13160, 
						LAYER_SALARY_MIDPOINT = 28455, LAYER_SALARY_MIDPOINT1 = 24410, LAYER_SALARY_MIDPOINT2 = 36470
						where LAYER_TYPE = 0 and LEVEL_NO = 'K2' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 19860, LAYER_SALARY_MAX = 58390, LAYER_SALARY_TEMP = 19860, 
						LAYER_SALARY_MIDPOINT = 39145, LAYER_SALARY_MIDPOINT1 = 37200, LAYER_SALARY_MIDPOINT2 = 49330
						where LAYER_TYPE = 0 and LEVEL_NO = 'K3' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 24400, LAYER_SALARY_MAX = 69040, LAYER_SALARY_TEMP = 24400, 
						LAYER_SALARY_MIDPOINT = 49005, LAYER_SALARY_MIDPOINT1 = 50320, LAYER_SALARY_MIDPOINT2 = 59630
						where LAYER_TYPE = 0 and LEVEL_NO = 'K4' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 29980, LAYER_SALARY_MAX = 76800, LAYER_SALARY_TEMP = 29980, LAYER_SALARY_FULL = 74320, 
						LAYER_SALARY_MIDPOINT = 59115, LAYER_SALARY_MIDPOINT1 = 60830, LAYER_SALARY_MIDPOINT2 = 68560, 
						LAYER_EXTRA_MIDPOINT = 58305, LAYER_EXTRA_MIDPOINT1 = 60830, LAYER_EXTRA_MIDPOINT2 = 66700
						where LAYER_TYPE = 0 and LEVEL_NO = 'K5' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 19860, LAYER_SALARY_MAX = 59500, LAYER_SALARY_TEMP = 19860, 
						LAYER_SALARY_MIDPOINT = 42025, LAYER_SALARY_MIDPOINT1 = 37210, LAYER_SALARY_MIDPOINT2 = 51290
						where LAYER_TYPE = 0 and LEVEL_NO = 'D1' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 24400, LAYER_SALARY_MAX = 70360, LAYER_SALARY_TEMP = 24400, 
						LAYER_SALARY_MIDPOINT = 50375, LAYER_SALARY_MIDPOINT1 = 52320, LAYER_SALARY_MIDPOINT2 = 60990
						where LAYER_TYPE = 0 and LEVEL_NO = 'D2' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 24400, LAYER_SALARY_MAX = 74320, LAYER_SALARY_TEMP = 24400, 
						LAYER_SALARY_MIDPOINT = 61765, LAYER_SALARY_MIDPOINT1 = 62210, LAYER_SALARY_MIDPOINT2 = 68530
						where LAYER_TYPE = 0 and LEVEL_NO = 'M1' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 29980, LAYER_SALARY_MAX = 76800, LAYER_SALARY_TEMP = 29980, 
						LAYER_SALARY_MIDPOINT = 67365, LAYER_SALARY_MIDPOINT1 = 69910, LAYER_SALARY_MIDPOINT2 = 71700
						where LAYER_TYPE = 0 and LEVEL_NO = 'M2' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับบัญชีข้าราชการ ๑ เมษายน ๒๕๕๙");
	}
	
	if ($command == "COPY6") {
		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 4870, LAYER_SALARY_MAX = 21010, LAYER_SALARY_MIDPOINT = 14565, 
						LAYER_SALARY_MIDPOINT1 = 12310, LAYER_SALARY_MIDPOINT2 = 18110
						where LAYER_TYPE = 0 and LEVEL_NO = 'O1' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 10190, LAYER_SALARY_MAX = 38750, LAYER_SALARY_MIDPOINT = 24125, 
						LAYER_SALARY_MIDPOINT1 = 18480, LAYER_SALARY_MIDPOINT2 = 31610
						where LAYER_TYPE = 0 and LEVEL_NO = 'O2' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 15410, LAYER_SALARY_MAX = 54820, LAYER_SALARY_FULL = 41620, 
						LAYER_SALARY_MIDPOINT = 34635, LAYER_SALARY_MIDPOINT1 = 32250, LAYER_SALARY_MIDPOINT2 = 44970, 
						LAYER_EXTRA_MIDPOINT = 31385, LAYER_EXTRA_MIDPOINT1 = 32250, LAYER_EXTRA_MIDPOINT2 = 35070
						where LAYER_TYPE = 0 and LEVEL_NO = 'O3' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 48220, LAYER_SALARY_MAX = 69040, LAYER_SALARY_MIDPOINT = 58635, 
						LAYER_SALARY_MIDPOINT1 = 53430, LAYER_SALARY_MIDPOINT2 = 63840
						where LAYER_TYPE = 0 and LEVEL_NO = 'O4' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 7140, LAYER_SALARY_MAX = 26900, LAYER_SALARY_TEMP = 7140, 
						LAYER_SALARY_MIDPOINT = 20295, LAYER_SALARY_MIDPOINT1 = 17980, LAYER_SALARY_MIDPOINT2 = 23930
						where LAYER_TYPE = 0 and LEVEL_NO = 'K1' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 13160, LAYER_SALARY_MAX = 43600, LAYER_SALARY_TEMP = 13160, 
						LAYER_SALARY_MIDPOINT = 29025, LAYER_SALARY_MIDPOINT1 = 24410, LAYER_SALARY_MIDPOINT2 = 36470
						where LAYER_TYPE = 0 and LEVEL_NO = 'K2' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 19860, LAYER_SALARY_MAX = 58390, LAYER_SALARY_TEMP = 19860, 
						LAYER_SALARY_MIDPOINT = 39935, LAYER_SALARY_MIDPOINT1 = 37200, LAYER_SALARY_MIDPOINT2 = 49330
						where LAYER_TYPE = 0 and LEVEL_NO = 'K3' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 24400, LAYER_SALARY_MAX = 69040, LAYER_SALARY_TEMP = 24400, 
						LAYER_SALARY_MIDPOINT = 49985, LAYER_SALARY_MIDPOINT1 = 50320, LAYER_SALARY_MIDPOINT2 = 59630
						where LAYER_TYPE = 0 and LEVEL_NO = 'K4' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 29980, LAYER_SALARY_MAX = 76800, LAYER_SALARY_TEMP = 29980, LAYER_SALARY_FULL = 74320, 
						LAYER_SALARY_MIDPOINT = 60305, LAYER_SALARY_MIDPOINT1 = 60830, LAYER_SALARY_MIDPOINT2 = 68560, 
						LAYER_EXTRA_MIDPOINT = 59475, LAYER_EXTRA_MIDPOINT1 = 60830, LAYER_EXTRA_MIDPOINT2 = 66700
						where LAYER_TYPE = 0 and LEVEL_NO = 'K5' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 19860, LAYER_SALARY_MAX = 59500, LAYER_SALARY_TEMP = 19860, 
						LAYER_SALARY_MIDPOINT = 42875, LAYER_SALARY_MIDPOINT1 = 37210, LAYER_SALARY_MIDPOINT2 = 51290
						where LAYER_TYPE = 0 and LEVEL_NO = 'D1' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 24400, LAYER_SALARY_MAX = 70360, LAYER_SALARY_TEMP = 24400, 
						LAYER_SALARY_MIDPOINT = 51385, LAYER_SALARY_MIDPOINT1 = 52320, LAYER_SALARY_MIDPOINT2 = 60990
						where LAYER_TYPE = 0 and LEVEL_NO = 'D2' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 24400, LAYER_SALARY_MAX = 74320, LAYER_SALARY_TEMP = 24400, 
						LAYER_SALARY_MIDPOINT = 62735, LAYER_SALARY_MIDPOINT1 = 62210, LAYER_SALARY_MIDPOINT2 = 68530
						where LAYER_TYPE = 0 and LEVEL_NO = 'M1' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 29980, LAYER_SALARY_MAX = 76800, LAYER_SALARY_TEMP = 29980, 
						LAYER_SALARY_MIDPOINT = 68715, LAYER_SALARY_MIDPOINT1 = 69910, LAYER_SALARY_MIDPOINT2 = 71700
						where LAYER_TYPE = 0 and LEVEL_NO = 'M2' and LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับบัญชีข้าราชการ ๑ ตุลาคม ๒๕๕๙");
	}
	//๑ เมษายน ๒๕๖๐ 
	if ($command == "COPY7") { 
            /*LAYER_SALARY_MIN=เงินเดือนขั้นต่ำ
             *LAYER_SALARY_MAX=เงินเดือนขั้นสูง
             * 
             *LAYER_SALARY_FULL=เงินเดือนพิเศษ
             * 
             *LAYER_SALARY_MIDPOINT=เงินเดือนที่นำมาแบ่งช่วง
             * 
             *LAYER_SALARY_MIDPOINT1=ฐานในการคำนวณล่าง ระดับ2
             *LAYER_SALARY_MIDPOINT2=ฐานในการคำนวณบน  ระดับ2
             * 
             *LAYER_EXTRA_MIDPOINT=เงินเดือนที่นำมาแบ่งช่วง
             *LAYER_EXTRA_MIDPOINT1=ฐานในการคำนวณล่าง  ระดับ1
             *LAYER_EXTRA_MIDPOINT2=ฐานในการคำนวณบน  ระดับ1
             * 
             *LAYER_SALARY_TEMPUP=**เงินเดือนขั้นสูงตามมติ ครม.
             */
                //ทั่วไป-->ปฏิบัติงาน
		$cmd = " update PER_LAYER set 
                            LAYER_SALARY_MIN = 4870, 
                            LAYER_SALARY_MAX = 38750, 
                            LAYER_SALARY_MIDPOINT = 14865, 
                            LAYER_SALARY_MIDPOINT1 = 12310, 
                            LAYER_SALARY_MIDPOINT2 = 18110,
                            LAYER_SALARY_TEMPUP= 21010
                         where LAYER_TYPE = 0 and LEVEL_NO = 'O1' and LAYER_NO = 0 ";
                //echo '<pre>'.$cmd.'<br>---------------------<br>';
		$db_dpis->send_cmd($cmd);
                //ทั่วไป-->ชำนาญงาน
		$cmd = " update PER_LAYER set 
                            LAYER_SALARY_MIN = 10190, 
                            LAYER_SALARY_MAX = 54820, 
                            LAYER_SALARY_MIDPOINT = 24475, 
                            LAYER_SALARY_MIDPOINT1 = 18480, 
                            LAYER_SALARY_MIDPOINT2 = 31610,
                            LAYER_SALARY_TEMPUP=38750 
                         where LAYER_TYPE = 0 and LEVEL_NO = 'O2' and LAYER_NO = 0 ";
                //echo '<pre>'.$cmd.'<br>---------------------<br>';
		$db_dpis->send_cmd($cmd);
                //ทั่วไป-->อาวุโส
		$cmd = " update PER_LAYER set 
                            LAYER_SALARY_MIN = 15410, 
                            LAYER_SALARY_MAX = 69040 , 
                            LAYER_SALARY_FULL = 41620, 
                            LAYER_SALARY_MIDPOINT = 35125, 
                            LAYER_SALARY_MIDPOINT1 = 32250, 
                            LAYER_SALARY_MIDPOINT2 = 44970, 
                            LAYER_EXTRA_MIDPOINT = 32015, 
                            LAYER_EXTRA_MIDPOINT1 = 32250, 
                            LAYER_EXTRA_MIDPOINT2 = 35070,
                            LAYER_SALARY_TEMPUP=54820
                         where LAYER_TYPE = 0 and LEVEL_NO = 'O3' and LAYER_NO = 0 ";
                //echo '<pre>'.$cmd.'<br>---------------------<br>';
		$db_dpis->send_cmd($cmd);
                //ทั่วไป-->ทักษะพิเศษ
		$cmd = " update PER_LAYER set 
                            LAYER_SALARY_MIN = 48220, 
                            LAYER_SALARY_MAX = 74320, 
                            LAYER_SALARY_MIDPOINT = 58635,
                            LAYER_SALARY_MIDPOINT1 = 53430, 
                            LAYER_SALARY_MIDPOINT2 = 63840,
                            LAYER_SALARY_TEMPUP=69040 
                        where LAYER_TYPE = 0 and LEVEL_NO = 'O4' and LAYER_NO = 0 ";
                //echo '<pre>'.$cmd.'<br>---------------------<br>';
		$db_dpis->send_cmd($cmd);
                //วิชาการ-->ปฏิบัติการ    
		$cmd = " update PER_LAYER set 
                            LAYER_SALARY_MIN = 7140, 
                            LAYER_SALARY_MAX = 43600 , 
                            LAYER_SALARY_TEMP = 7140,
                            LAYER_SALARY_MIDPOINT = 20705, 
                            LAYER_SALARY_MIDPOINT1 = 17980, 
                            LAYER_SALARY_MIDPOINT2 = 23930,
                            LAYER_SALARY_TEMPUP=26900
                        where LAYER_TYPE = 0 and LEVEL_NO = 'K1' and LAYER_NO = 0 ";
                //echo '<pre>'.$cmd.'<br>---------------------<br>';
		$db_dpis->send_cmd($cmd);
                //วิชาการ-->ชำนาญการ
		$cmd = " update PER_LAYER set 
                            LAYER_SALARY_MIN = 13160, 
                            LAYER_SALARY_MAX = 58390, 
                            LAYER_SALARY_TEMP = 13160,
                            LAYER_SALARY_MIDPOINT = 29335, 
                            LAYER_SALARY_MIDPOINT1 = 24410, 
                            LAYER_SALARY_MIDPOINT2 = 36470,
                            LAYER_SALARY_TEMPUP=43600 
                        where LAYER_TYPE = 0 and LEVEL_NO = 'K2' and LAYER_NO = 0 ";
                //echo '<pre>'.$cmd.'<br>---------------------<br>';
		$db_dpis->send_cmd($cmd);
                //วิชาการ-->ชำนาญการพิเศษ
		$cmd = " update PER_LAYER set 
                            LAYER_SALARY_MIN = 19860, 
                            LAYER_SALARY_MAX = 69040 , 
                            LAYER_SALARY_TEMP = 19860,
                            LAYER_SALARY_MIDPOINT = 40275, 
                            LAYER_SALARY_MIDPOINT1 = 37200, 
                            LAYER_SALARY_MIDPOINT2 = 49330,
                            LAYER_SALARY_TEMPUP=58390
                        where LAYER_TYPE = 0 and LEVEL_NO = 'K3' and LAYER_NO = 0 ";
                //echo '<pre>'.$cmd.'<br>---------------------<br>';
		$db_dpis->send_cmd($cmd);
                //วิชาการ-->เชี่ยวชาญ
		$cmd = " update PER_LAYER set 
                            LAYER_SALARY_MIN = 24400, 
                            LAYER_SALARY_MAX = 74320, 
                            LAYER_SALARY_TEMP = 24400,
                            LAYER_SALARY_MIDPOINT = 50325, 
                            LAYER_SALARY_MIDPOINT1 = 50320, 
                            LAYER_SALARY_MIDPOINT2 = 59630, 
                            LAYER_SALARY_TEMPUP=69040 
                        where LAYER_TYPE = 0 and LEVEL_NO = 'K4' and LAYER_NO = 0 ";
                //echo '<pre>'.$cmd.'<br>---------------------<br>';
		$db_dpis->send_cmd($cmd);
                //วิชาการ-->ทรงคุณวุฒิ
		$cmd = " update PER_LAYER set 
                            LAYER_SALARY_MIN = 29980, 
                            LAYER_SALARY_MAX = 76800 , 
                            LAYER_SALARY_TEMP = 29980, 
                            LAYER_SALARY_FULL = 74320,
                            LAYER_SALARY_MIDPOINT = 60835, 
                            LAYER_SALARY_MIDPOINT1 = 60830, 
                            LAYER_SALARY_MIDPOINT2 = 68560,
                            LAYER_EXTRA_MIDPOINT = 60665, 
                            LAYER_EXTRA_MIDPOINT1 = 60830, 
                            LAYER_EXTRA_MIDPOINT2 = 66700,
                            LAYER_SALARY_TEMPUP=76800
                        where LAYER_TYPE = 0 and LEVEL_NO = 'K5' and LAYER_NO = 0 ";
                //echo '<pre>'.$cmd.'<br>---------------------<br>';
		$db_dpis->send_cmd($cmd);
                //อำนวยการ-->ต้น
		$cmd = " update PER_LAYER set 
                            LAYER_SALARY_MIN = 19860, 
                            LAYER_SALARY_MAX = 70360 , 
                            LAYER_SALARY_TEMP = 19860,
                            LAYER_SALARY_MIDPOINT = 43085, 
                            LAYER_SALARY_MIDPOINT1 = 37210, 
                            LAYER_SALARY_MIDPOINT2 = 51290,
                            LAYER_SALARY_TEMPUP=59500
                         where LAYER_TYPE = 0 and LEVEL_NO = 'D1' and LAYER_NO = 0 ";
                //echo '<pre>'.$cmd.'<br>---------------------<br>';
		$db_dpis->send_cmd($cmd);
                //อำนวยการ-->สูง
		$cmd = " update PER_LAYER set 
                            LAYER_SALARY_MIN = 24400, 
                            LAYER_SALARY_MAX = 74320 , 
                            LAYER_SALARY_TEMP = 24400,
                            LAYER_SALARY_MIDPOINT = 52325, 
                            LAYER_SALARY_MIDPOINT1 = 52320, 
                            LAYER_SALARY_MIDPOINT2 = 60990,
                            LAYER_SALARY_TEMPUP=70360
                        where LAYER_TYPE = 0 and LEVEL_NO = 'D2' and LAYER_NO = 0 ";
                //echo '<pre>'.$cmd.'<br>---------------------<br>';
		$db_dpis->send_cmd($cmd);
                //บริหาร-->ต้น
		$cmd = " update PER_LAYER set 
                            LAYER_SALARY_MIN = 24400, 
                            LAYER_SALARY_MAX = 76800 , 
                            LAYER_SALARY_TEMP = 24400,
                            LAYER_SALARY_MIDPOINT = 62735, 
                            LAYER_SALARY_MIDPOINT1 = 62210, 
                            LAYER_SALARY_MIDPOINT2 = 68530,
                            LAYER_SALARY_TEMPUP=74320
                        where LAYER_TYPE = 0 and LEVEL_NO = 'M1' and LAYER_NO = 0 ";
                //echo '<pre>'.$cmd.'<br>---------------------<br>';
		$db_dpis->send_cmd($cmd);
                //บริหาร-->สูง
		$cmd = " update PER_LAYER set 
                            LAYER_SALARY_MIN = 29980, 
                            LAYER_SALARY_MAX = 76800, 
                            LAYER_SALARY_TEMP = 29980,
                            LAYER_SALARY_MIDPOINT = 69915, 
                            LAYER_SALARY_MIDPOINT1 = 69910, 
                            LAYER_SALARY_MIDPOINT2 = 71700,
                            LAYER_SALARY_TEMPUP=76800 
                        where LAYER_TYPE = 0 and LEVEL_NO = 'M2' and LAYER_NO = 0 ";
                //echo '<pre>'.$cmd.'<br>---------------------<br>';
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับบัญชีข้าราชการ ๑ เมษายน ๒๕๖๐");
	}
	
	if ($command == "COPY8") { //๑ ตุลาคม ๒๕๖๐
            /*LAYER_SALARY_MIN=เงินเดือนขั้นต่ำ
             *LAYER_SALARY_MAX=เงินเดือนขั้นสูง
             * 
             *LAYER_SALARY_FULL=เงินเดือนพิเศษ
             * 
             *LAYER_SALARY_MIDPOINT=เงินเดือนที่นำมาแบ่งช่วง
             * 
             *LAYER_SALARY_MIDPOINT1=ฐานในการคำนวณล่าง ระดับ2
             *LAYER_SALARY_MIDPOINT2=ฐานในการคำนวณบน  ระดับ2
             * 
             *LAYER_EXTRA_MIDPOINT=เงินเดือนที่นำมาแบ่งช่วง
             *LAYER_EXTRA_MIDPOINT1=ฐานในการคำนวณล่าง  ระดับ1
             *LAYER_EXTRA_MIDPOINT2=ฐานในการคำนวณบน  ระดับ1
             * 
             *LAYER_SALARY_TEMPUP=**เงินเดือนขั้นสูงตามมติ ครม.
             */
                
                /*เดิม*/
		/*$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 4870, LAYER_SALARY_MAX = 21010, LAYER_SALARY_MIDPOINT = 15215, 
						LAYER_SALARY_MIDPOINT1 = 12310, LAYER_SALARY_MIDPOINT2 = 18110
						where LAYER_TYPE = 0 and LEVEL_NO = 'O1' and LAYER_NO = 0 ";*/
                //ทั่วไป-->ปฏิบัติงาน
                $cmd = " UPDATE PER_LAYER 
                        SET LAYER_SALARY_MIN = 4870, 
                            LAYER_SALARY_MAX = 38750, 
                            LAYER_SALARY_MIDPOINT = 15215, 
                            LAYER_SALARY_MIDPOINT1 = 12310, 
                            LAYER_SALARY_MIDPOINT2 = 18110 ,
                            LAYER_SALARY_TEMPUP= 21010 
                        WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'O1' AND LAYER_NO = 0 ";//ทั่วไป-->ปฏิบัติงาน
		$db_dpis->send_cmd($cmd);

                /*เดิม*/    
		/*$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 10190, LAYER_SALARY_MAX = 38750, LAYER_SALARY_MIDPOINT = 24475, 
						LAYER_SALARY_MIDPOINT1 = 18480, LAYER_SALARY_MIDPOINT2 = 31610
						where LAYER_TYPE = 0 and LEVEL_NO = 'O2' and LAYER_NO = 0 ";*/
                //ทั่วไป-->ชำนาญงาน
                $cmd = " UPDATE PER_LAYER 
                        SET LAYER_SALARY_MIN = 10190, 
                            LAYER_SALARY_MAX = 54820, 
                            LAYER_SALARY_MIDPOINT = 24475,
                            LAYER_SALARY_MIDPOINT1 = 18480, 
                            LAYER_SALARY_MIDPOINT2 = 31610 ,
                            LAYER_SALARY_TEMPUP=38750 
                        WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'O2' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);

                /*เดิม*/ 
		/*$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 15410, LAYER_SALARY_MAX = 54820, LAYER_SALARY_FULL = 41620, 
						LAYER_SALARY_MIDPOINT = 35125, LAYER_SALARY_MIDPOINT1 = 32250, LAYER_SALARY_MIDPOINT2 = 44970, 
						LAYER_EXTRA_MIDPOINT = 32255, LAYER_EXTRA_MIDPOINT1 = 32250, LAYER_EXTRA_MIDPOINT2 = 35070
						where LAYER_TYPE = 0 and LEVEL_NO = 'O3' and LAYER_NO = 0 ";*/
                //ทั่วไป-->อาวุโส
                $cmd = " UPDATE PER_LAYER 
                        SET LAYER_SALARY_MIN = 15410, 
                            LAYER_SALARY_MAX = 69040, 
                            LAYER_SALARY_FULL = 41620, 
                            LAYER_SALARY_MIDPOINT = 35125, 
                            LAYER_SALARY_MIDPOINT1 = 32250, 
                            LAYER_SALARY_MIDPOINT2 = 44970, 
                            LAYER_EXTRA_MIDPOINT = 32255, 
                            LAYER_EXTRA_MIDPOINT1 = 32250, 
                            LAYER_EXTRA_MIDPOINT2 = 35070 ,
                            LAYER_SALARY_TEMPUP=54820
                        WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'O3' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);

                /*เดิม*/ 
		/*$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 48220, LAYER_SALARY_MAX = 69040, LAYER_SALARY_MIDPOINT = 58635, 
						LAYER_SALARY_MIDPOINT1 = 53430, LAYER_SALARY_MIDPOINT2 = 63840
						where LAYER_TYPE = 0 and LEVEL_NO = 'O4' and LAYER_NO = 0 ";*/
                //ทั่วไป-->ทักษะพิเศษ
                $cmd = " UPDATE PER_LAYER 
                        SET LAYER_SALARY_MIN = 48220, 
                            LAYER_SALARY_MAX = 74320, 
                            LAYER_SALARY_MIDPOINT = 58635, 
                            LAYER_SALARY_MIDPOINT1 = 53430, 
                            LAYER_SALARY_MIDPOINT2 = 63840 ,
                            LAYER_SALARY_TEMPUP=69040 
                        WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'O4' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);

                /*เดิม*/ 
		/*$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 7140, LAYER_SALARY_MAX = 26900, LAYER_SALARY_TEMP = 7140, 
						LAYER_SALARY_MIDPOINT = 20955, LAYER_SALARY_MIDPOINT1 = 17980, LAYER_SALARY_MIDPOINT2 = 23930
						where LAYER_TYPE = 0 and LEVEL_NO = 'K1' and LAYER_NO = 0 ";*/
                //วิชาการ-->ปฏิบัติการ    
                $cmd = " UPDATE PER_LAYER 
                        SET LAYER_SALARY_MIN = 7140, 
                            LAYER_SALARY_MAX = 43600, 
                            LAYER_SALARY_TEMP = 7140, 
                            LAYER_SALARY_MIDPOINT = 20955, 
                            LAYER_SALARY_MIDPOINT1 = 17980, 
                            LAYER_SALARY_MIDPOINT2 = 23930,
                            LAYER_SALARY_TEMPUP=26900 
                        WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'K1' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);

                /*เดิม*/     
		/*$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 13160, LAYER_SALARY_MAX = 43600, LAYER_SALARY_TEMP = 13160, 
						LAYER_SALARY_MIDPOINT = 29335, LAYER_SALARY_MIDPOINT1 = 24410, LAYER_SALARY_MIDPOINT2 = 36470
						where LAYER_TYPE = 0 and LEVEL_NO = 'K2' and LAYER_NO = 0 ";*/
                //วิชาการ-->ชำนาญการ
                $cmd = " UPDATE PER_LAYER 
                        SET LAYER_SALARY_MIN = 13160, 
                            LAYER_SALARY_MAX = 58390, 
                            LAYER_SALARY_TEMP = 13160, 
                            LAYER_SALARY_MIDPOINT = 29335, 
                            LAYER_SALARY_MIDPOINT1 = 24410, 
                            LAYER_SALARY_MIDPOINT2 = 36470,
                            LAYER_SALARY_TEMPUP=43600 
                        WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'K2' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);

                /*เดิม*/  
		/*$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 19860, LAYER_SALARY_MAX = 58390, LAYER_SALARY_TEMP = 19860, 
						LAYER_SALARY_MIDPOINT = 40275, LAYER_SALARY_MIDPOINT1 = 37200, LAYER_SALARY_MIDPOINT2 = 49330
						where LAYER_TYPE = 0 and LEVEL_NO = 'K3' and LAYER_NO = 0 ";*/
                //วิชาการ-->ชำนาญการพิเศษ
                $cmd = " UPDATE PER_LAYER 
                        SET LAYER_SALARY_MIN = 19860, 
                            LAYER_SALARY_MAX = 69040, 
                            LAYER_SALARY_TEMP = 19860, 
                            LAYER_SALARY_MIDPOINT = 40275, 
                            LAYER_SALARY_MIDPOINT1 = 37200, 
                            LAYER_SALARY_MIDPOINT2 = 49330,
                            LAYER_SALARY_TEMPUP=58390 
                        WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'K3' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);

                /*เดิม*/  
		/*$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 24400, LAYER_SALARY_MAX = 69040, LAYER_SALARY_TEMP = 24400, 
						LAYER_SALARY_MIDPOINT = 50325, LAYER_SALARY_MIDPOINT1 = 50320, LAYER_SALARY_MIDPOINT2 = 59630
						where LAYER_TYPE = 0 and LEVEL_NO = 'K4' and LAYER_NO = 0 ";*/
                //วิชาการ-->เชี่ยวชาญ
                $cmd = " UPDATE PER_LAYER 
                        SET LAYER_SALARY_MIN = 24400, 
                            LAYER_SALARY_MAX = 74320, 
                            LAYER_SALARY_TEMP = 24400, 
                            LAYER_SALARY_MIDPOINT = 50325, 
                            LAYER_SALARY_MIDPOINT1 = 50320, 
                            LAYER_SALARY_MIDPOINT2 = 59630,
                            LAYER_SALARY_TEMPUP=69040 
                        WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'K4' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);

                /*เดิม*/
		/*$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 29980, LAYER_SALARY_MAX = 76800, LAYER_SALARY_TEMP = 29980, LAYER_SALARY_FULL = 74320, 
						LAYER_SALARY_MIDPOINT = 60835, LAYER_SALARY_MIDPOINT1 = 60830, LAYER_SALARY_MIDPOINT2 = 68560, 
						LAYER_EXTRA_MIDPOINT = 60835, LAYER_EXTRA_MIDPOINT1 = 60830, LAYER_EXTRA_MIDPOINT2 = 66700
						where LAYER_TYPE = 0 and LEVEL_NO = 'K5' and LAYER_NO = 0 ";*/
                //วิชาการ-->ทรงคุณวุฒิ
                $cmd = " UPDATE PER_LAYER 
                        SET LAYER_SALARY_MIN = 29980, 
                            LAYER_SALARY_MAX = 76800, 
                            LAYER_SALARY_TEMP = 29980, 
                            LAYER_SALARY_FULL = 74320, 
                            LAYER_SALARY_MIDPOINT = 60835, 
                            LAYER_SALARY_MIDPOINT1 = 60830, 
                            LAYER_SALARY_MIDPOINT2 = 68560, 
                            LAYER_EXTRA_MIDPOINT = 60835, 
                            LAYER_EXTRA_MIDPOINT1 = 60830, 
                            LAYER_EXTRA_MIDPOINT2 = 66700,
                            LAYER_SALARY_TEMPUP=76800 
                        WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'K5' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);

                /*เดิม*/
		/*$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 19860, LAYER_SALARY_MAX = 59500, LAYER_SALARY_TEMP = 19860, 
						LAYER_SALARY_MIDPOINT = 43085, LAYER_SALARY_MIDPOINT1 = 37210, LAYER_SALARY_MIDPOINT2 = 51290
						where LAYER_TYPE = 0 and LEVEL_NO = 'D1' and LAYER_NO = 0 ";*/
                //อำนวยการ-->ต้น
                $cmd = " UPDATE PER_LAYER 
                        SET LAYER_SALARY_MIN = 19860, 
                            LAYER_SALARY_MAX = 70360, 
                            LAYER_SALARY_TEMP = 19860, 
                            LAYER_SALARY_MIDPOINT = 43085, 
                            LAYER_SALARY_MIDPOINT1 = 37210, 
                            LAYER_SALARY_MIDPOINT2 = 51290,
                            LAYER_SALARY_TEMPUP=59500
                        WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'D1' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);

                /*เดิม*/
		/*$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 24400, LAYER_SALARY_MAX = 70360, LAYER_SALARY_TEMP = 24400, 
						LAYER_SALARY_MIDPOINT = 52325, LAYER_SALARY_MIDPOINT1 = 52320, LAYER_SALARY_MIDPOINT2 = 60990
						where LAYER_TYPE = 0 and LEVEL_NO = 'D2' and LAYER_NO = 0 ";*/
                //อำนวยการ-->สูง
                $cmd = " UPDATE PER_LAYER 
                        SET LAYER_SALARY_MIN = 24400, 
                            LAYER_SALARY_MAX = 74320, 
                            LAYER_SALARY_TEMP = 24400, 
                            LAYER_SALARY_MIDPOINT = 52325, 
                            LAYER_SALARY_MIDPOINT1 = 52320, 
                            LAYER_SALARY_MIDPOINT2 = 60990,
                            LAYER_SALARY_TEMPUP=70360 
                        WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'D2' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);

                /*เดิม*/
		/*$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 24400, LAYER_SALARY_MAX = 74320, LAYER_SALARY_TEMP = 24400, 
						LAYER_SALARY_MIDPOINT = 62735, LAYER_SALARY_MIDPOINT1 = 62210, LAYER_SALARY_MIDPOINT2 = 68530
						where LAYER_TYPE = 0 and LEVEL_NO = 'M1' and LAYER_NO = 0 ";*/
                //บริหาร-->ต้น
                $cmd = " UPDATE PER_LAYER 
                        SET LAYER_SALARY_MIN = 24400, 
                            LAYER_SALARY_MAX = 76800, 
                            LAYER_SALARY_TEMP = 24400, 
                            LAYER_SALARY_MIDPOINT = 62735, 
                            LAYER_SALARY_MIDPOINT1 = 62210, 
                            LAYER_SALARY_MIDPOINT2 = 68530,
                            LAYER_SALARY_TEMPUP=74320
                        WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'M1' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);

                /*เดิม*/
		/*$cmd = " update PER_LAYER set LAYER_SALARY_MIN = 29980, LAYER_SALARY_MAX = 76800, LAYER_SALARY_TEMP = 29980, 
						LAYER_SALARY_MIDPOINT = 69915, LAYER_SALARY_MIDPOINT1 = 69910, LAYER_SALARY_MIDPOINT2 = 71700
						where LAYER_TYPE = 0 and LEVEL_NO = 'M2' and LAYER_NO = 0 ";*/
                //บริหาร-->สูง
                $cmd = " UPDATE PER_LAYER 
                        SET LAYER_SALARY_MIN = 29980, 
                            LAYER_SALARY_MAX = 76800, 
                            LAYER_SALARY_TEMP = 29980, 
                            LAYER_SALARY_MIDPOINT = 69915, 
                            LAYER_SALARY_MIDPOINT1 = 69910, 
                            LAYER_SALARY_MIDPOINT2 = 71700,
                            LAYER_SALARY_TEMPUP=76800 
                        WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'M2' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
                
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับบัญชีข้าราชการ ๑ ตุลาคม ๒๕๖๐");
	}
	
	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
//		echo "$setflagshow<br>";
		if($DPISDB=="oci8") 
			$cmd = " update $table set LAYER_ACTIVE = 0 where concat(trim(LAYER_TYPE), concat('|', concat(trim(LEVEL_NO), concat('|', trim(LAYER_NO))))) in (".stripslashes($current_list).") ";
		elseif($DPISDB=="odbc") 
			$cmd = " update $table set LAYER_ACTIVE = 0 where trim(LAYER_TYPE) + '|' + trim(LEVEL_NO) + '|' + trim(LAYER_NO) in (".stripslashes($current_list).") ";
		elseif($DPISDB=="mysql") 
			$cmd = " update $table set LAYER_ACTIVE = 0 where trim(LAYER_TYPE) + '|' + trim(LEVEL_NO) + '|' + trim(LAYER_NO) in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); echo "<br>";
		//echo  "LAYER_TYPE<br>";  echo  "LEVEL_NO<br>";  echo  "LAYER_NO<br>";
		
		if($DPISDB=="oci8") 
			$cmd = " update $table set LAYER_ACTIVE = 1 where concat(trim(LAYER_TYPE), concat('|', concat(trim(LEVEL_NO), concat('|', trim(LAYER_NO))))) in (".stripslashes($setflagshow).") ";
		elseif($DPISDB=="odbc")  
			$cmd = " update $table set LAYER_ACTIVE = 1 where trim(LAYER_TYPE) + '|' + trim(LEVEL_NO) + '|' + trim(LAYER_NO) in (".stripslashes($setflagshow).") ";
		elseif($DPISDB=="mysql") 
			$cmd = " update $table set LAYER_ACTIVE = 1 where trim(LAYER_TYPE) + '|' + trim(LEVEL_NO) + '|' + trim(LAYER_NO) in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); echo "<br>";
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}
	
	$LAYER_TYPE += 0;
	$LAYER_NO += 0;
	$LAYER_SALARY = str_replace(",", "", $LAYER_SALARY) + 0;
	$LAYER_SALARY_MIN = str_replace(",", "", $LAYER_SALARY_MIN) + 0;
	$LAYER_SALARY_MAX = str_replace(",", "", $LAYER_SALARY_MAX) + 0;
	$LAYER_SALARY_TEMP = str_replace(",", "", $LAYER_SALARY_TEMP) + 0;
	$LAYER_SALARY_FULL = str_replace(",", "", $LAYER_SALARY_FULL) + 0;
	$LAYER_SALARY_MIDPOINT = str_replace(",", "", $LAYER_SALARY_MIDPOINT) + 0;
	$LAYER_SALARY_MIDPOINT1 = str_replace(",", "", $LAYER_SALARY_MIDPOINT1) + 0;
	$LAYER_SALARY_MIDPOINT2 = str_replace(",", "", $LAYER_SALARY_MIDPOINT2) + 0;
	$LAYER_EXTRA_MIDPOINT = str_replace(",", "", $LAYER_EXTRA_MIDPOINT) + 0;
	$LAYER_EXTRA_MIDPOINT1 = str_replace(",", "", $LAYER_EXTRA_MIDPOINT1) + 0;
	$LAYER_EXTRA_MIDPOINT2 = str_replace(",", "", $LAYER_EXTRA_MIDPOINT2) + 0;
        $LAYER_SALARY_TEMPUP = str_replace(",", "", $LAYER_SALARY_TEMPUP) + 0;
	
	if($command == "ADD" && (trim($LAYER_TYPE)!="") && ($LEVEL_NO) && trim($LAYER_NO)!=""){			
		$cmd = " select LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, LAYER_SALARY_MIN, 
						LAYER_SALARY_MAX, LAYER_SALARY_TEMP ,LAYER_SALARY_TEMPUP 
						from $table where LAYER_TYPE=$LAYER_TYPE and LEVEL_NO = '$LEVEL_NO' and LAYER_NO = $LAYER_NO ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			if($salary_type == 1){			// ถ้าเงินเดือนเป็นขั้น ลบเงินเดือนที่เป็นช่วง
//				$cmd = " delete from PER_LAYER where LAYER_TYPE=trim($LAYER_TYPE) and LEVEL_NO='$LEVEL_NO' and LAYER_NO=0 ";
//				$db_dpis->send_cmd($cmd);
			}elseif($salary_type == 2){		// ถ้าเงินเดือนเป็นช่วง ลบเงินเดือนที่เป็นขั้น
//				$cmd = " delete from PER_LAYER where LAYER_TYPE=trim($LAYER_TYPE) and LEVEL_NO='$LEVEL_NO' and LAYER_NO > 0 ";
//				$db_dpis->send_cmd($cmd);
			} // end if
			
			$cmd = " insert into $table (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, LAYER_SALARY_MIN, 
							LAYER_SALARY_MAX, LAYER_SALARY_TEMP, LAYER_SALARY_FULL, LAYER_SALARY_MIDPOINT, 
							LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, LAYER_EXTRA_MIDPOINT, 
							LAYER_EXTRA_MIDPOINT1, LAYER_EXTRA_MIDPOINT2, UPDATE_USER, UPDATE_DATE,LAYER_SALARY_TEMPUP) 
							values ($LAYER_TYPE, '$LEVEL_NO', $LAYER_NO, $LAYER_SALARY, $LAYER_ACTIVE, $LAYER_SALARY_MIN, 
							$LAYER_SALARY_MAX, $LAYER_SALARY_TEMP, $LAYER_SALARY_FULL, $LAYER_SALARY_MIDPOINT, 
							$LAYER_SALARY_MIDPOINT1, $LAYER_SALARY_MIDPOINT2, $LAYER_EXTRA_MIDPOINT, 
							$LAYER_EXTRA_MIDPOINT1, $LAYER_EXTRA_MIDPOINT2, $SESS_USERID, '$UPDATE_DATE',$LAYER_SALARY_TEMPUP) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [trim($LAYER_TYPE) : $LEVEL_NO : $LAYER_NO : $LAYER_SALARY : $LAYER_SALARY_MIN : $LAYER_SALARY_MAX]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [$data[LAYER_TYPE] $data[LEVEL_NO] $data[LAYER_NO] $LAYER_SALARY $LAYER_SALARY_MIN $LAYER_SALARY_MAX]";
		} // endif
	}

	if($command == "UPDATE" && (trim($LAYER_TYPE)!="") && ($LEVEL_NO) && trim($LAYER_NO)!=""){	
		if($LAYER_TYPE != $old_LAYER_TYPE || $LEVEL_NO != $old_LEVEL_NO || $LAYER_NO != $old_LAYER_NO){
			$cmd = " select LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, LAYER_SALARY_MIN, 
							  LAYER_SALARY_MAX, LAYER_SALARY_TEMP ,LAYER_SALARY_TEMPUP  
							  from $table where LAYER_TYPE=$LAYER_TYPE and LEVEL_NO = '$LEVEL_NO' and LAYER_NO = $LAYER_NO ";
			$count_duplicate = $db_dpis->send_cmd($cmd);
		} // end if
		if($count_duplicate <= 0){
//			$cmd = " delete from $table where LAYER_TYPE=$old_LAYER_TYPE and LEVEL_NO = '$old_LEVEL_NO' and 
//							  LAYER_NO = $old_LAYER_NO ";
//			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>";
		
			if ($salary_type == 1) {			// ถ้าเงินเดือนเป็นขั้น ลบเงินเดือนที่เป็นช่วง
				$LAYER_SALARY_MIN = 0;
				$LAYER_SALARY_MAX = 0;
	
//				$cmd = " delete from PER_LAYER where LAYER_TYPE=trim($LAYER_TYPE) and LEVEL_NO='$LEVEL_NO' and LAYER_NO=0 ";
//				$db_dpis->send_cmd($cmd);
			} elseif ($salary_type == 2) {	// ถ้าเงินเดือนเป็นช่วง ลบเงินเดือนที่เป็นขั้น
				$LAYER_NO = 0;		
	
//				$cmd = " delete from PER_LAYER where LAYER_TYPE=trim($LAYER_TYPE) and LEVEL_NO='$LEVEL_NO' and LAYER_NO > 0 ";
//				$db_dpis->send_cmd($cmd);
			}
			/*$cmd = " update PER_LAYER set LAYER_SALARY = $LAYER_SALARY, 
                                        LAYER_SALARY_MIN = $LAYER_SALARY_MIN, 
                                        LAYER_SALARY_MAX = $LAYER_SALARY_MAX, 
                                        LAYER_ACTIVE = $LAYER_ACTIVE, 
                                        LAYER_SALARY_TEMP = $LAYER_SALARY_TEMP, 
                                        LAYER_SALARY_FULL = $LAYER_SALARY_FULL, 
                                        LAYER_SALARY_MIDPOINT = $LAYER_SALARY_MIDPOINT, 
                                        LAYER_SALARY_MIDPOINT1 = $LAYER_SALARY_MIDPOINT1, 
                                        LAYER_SALARY_MIDPOINT2 = $LAYER_SALARY_MIDPOINT2,
                                        LAYER_EXTRA_MIDPOINT = $LAYER_EXTRA_MIDPOINT, 
                                        LAYER_EXTRA_MIDPOINT1 = $LAYER_EXTRA_MIDPOINT1, 
                                        LAYER_EXTRA_MIDPOINT2 = $LAYER_EXTRA_MIDPOINT2,
                                        LAYER_SALARY_TEMPUP = $LAYER_SALARY_TEMPUP,    
                                        UPDATE_USER = $SESS_USERID,
                                        UPDATE_DATE = '$UPDATE_DATE'
                                where (LAYER_TYPE=$LAYER_TYPE) and (LEVEL_NO='$LEVEL_NO' and LAYER_NO=$LAYER_NO)";*//*เดิม*/
                        $cmd = " update PER_LAYER set LAYER_SALARY = $LAYER_SALARY, 
                                        LAYER_SALARY_MIN = $LAYER_SALARY_MIN, 
                                        LAYER_SALARY_TEMPUP = $LAYER_SALARY_MAX, 
                                        LAYER_ACTIVE = $LAYER_ACTIVE, 
                                        LAYER_SALARY_TEMP = $LAYER_SALARY_TEMP, 
                                        LAYER_SALARY_FULL = $LAYER_SALARY_FULL, 
                                        LAYER_SALARY_MIDPOINT = $LAYER_SALARY_MIDPOINT, 
                                        LAYER_SALARY_MIDPOINT1 = $LAYER_SALARY_MIDPOINT1, 
                                        LAYER_SALARY_MIDPOINT2 = $LAYER_SALARY_MIDPOINT2,
                                        LAYER_EXTRA_MIDPOINT = $LAYER_EXTRA_MIDPOINT, 
                                        LAYER_EXTRA_MIDPOINT1 = $LAYER_EXTRA_MIDPOINT1, 
                                        LAYER_EXTRA_MIDPOINT2 = $LAYER_EXTRA_MIDPOINT2,
                                        LAYER_SALARY_MAX = $LAYER_SALARY_TEMPUP,    
                                        UPDATE_USER = $SESS_USERID,
                                        UPDATE_DATE = '$UPDATE_DATE'
                                where (LAYER_TYPE=$LAYER_TYPE) and (LEVEL_NO='$LEVEL_NO' and LAYER_NO=$LAYER_NO)";/*Release 5.2.1.6 */
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "$cmd<br>";	
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [trim($LAYER_TYPE) : $LEVEL_NO : $LAYER_NO : 
								   $LAYER_SALARY : $LAYER_SALARY_MIN : $LAYER_SALARY_MAX]");
		}else{
			$data = $db_dpis->get_array();
			$err_text = "รหัสข้อมูลซ้ำ [$data[LAYER_TYPE] $data[LEVEL_NO] $data[LAYER_NO] $LAYER_SALARY $LAYER_SALARY_MIN 
								  $LAYER_SALARY_MAX]";
			
			$UPD = 1;
			$LAYER_TYPE = ${"old_LAYER_TYPE"};
			$LEVEL_NO = ${"old_LEVEL_NO"};
			$LAYER_NO = ${"old_LAYER_NO"};
		} // end if
	}
	
	if($command == "DELETE" && (trim($LAYER_TYPE)!="") && ($LEVEL_NO) && trim($LAYER_NO)!=""){
		$cmd = " select LAYER_SALARY, LAYER_SALARY_MIN, LAYER_SALARY_MAX  from $table 
						  where LAYER_TYPE=$LAYER_TYPE and LEVEL_NO = '$LEVEL_NO' and LAYER_NO = $LAYER_NO ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$LAYER_SALARY = $data[LAYER_SALARY];
		$LAYER_SALARY_MIN = $data[LAYER_SALARY_MIN];
		$LAYER_SALARY_MAX = $data[LAYER_SALARY_MAX];
		
		if($DPISDB=="odbc") {
			$cmd2="select LAYER_TYPE from PER_LAYER_NEW";
			$db_dpis->send_cmd($cmd2);
			$data=$db_dpis->get_array();
			$LAYER_TYPE=$data[LAYER_TYPE];
			//echo "------------<br>"; echo "$cmd<br>"; echo "------------<br>";
			//echo "------------<br>"; echo "LAYER_TYPE=$LAYER_TYPE = $LAYER_TYPE <br>"; echo "------------<br>";
			//echo "------------<br>"; echo "LEVEL_NO=$LEVEL_NO = $LEVEL_NO<br>"; echo "------------<br>";
			//echo "------------<br>"; echo "LAYER_NO=$LAYER_NO=$LAYER_NO<br>"; echo "------------<br>";
			$cmd = " delete from $table where LAYER_TYPE=$LAYER_TYPE and LEVEL_NO = '$LEVEL_NO' and LAYER_NO = $LAYER_NO ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//close  if($DPISDB=="odbc")
		}else{
			$cmd = " delete from $table where LAYER_TYPE=$LAYER_TYPE and LEVEL_NO = '$LEVEL_NO' and LAYER_NO = $LAYER_NO ";
			$db_dpis->send_cmd($cmd);			
			//$db_dpis->show_error();
		}
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [trim($LAYER_TYPE) : $LEVEL_NO : $LAYER_NO : 
							  $LAYER_SALARY : $LAYER_SALARY_MIN : $LAYER_SALARY_MAX]");
	} // close else

//	echo "LAYER_TYPE=$LAYER_TYPE<br>";
	if($UPD){
		$cmd = " select LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, LAYER_SALARY_MIN, 
						LAYER_SALARY_MAX, LAYER_SALARY_TEMP, LAYER_SALARY_FULL, LAYER_SALARY_MIDPOINT, 
						LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, LAYER_EXTRA_MIDPOINT, 
						LAYER_EXTRA_MIDPOINT1, LAYER_EXTRA_MIDPOINT2, UPDATE_USER, UPDATE_DATE,LAYER_SALARY_TEMPUP
						from $table where LAYER_TYPE=$LAYER_TYPE and LEVEL_NO = '$LEVEL_NO' and LAYER_NO = $LAYER_NO ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		//echo "<pre>$cmd<br>";			
		$data = $db_dpis->get_array();
		$LEVEL_NO = $data[LEVEL_NO];
		$LAYER_NO = $data[LAYER_NO];
		$LAYER_SALARY = $data[LAYER_SALARY];
		$LAYER_ACTIVE = $data[LAYER_ACTIVE];
		$LAYER_SALARY_MIN = $data[LAYER_SALARY_MIN];
		//$LAYER_SALARY_MAX = $data[LAYER_SALARY_MAX];/*เดิม*/
                $LAYER_SALARY_MAX = $data[LAYER_SALARY_TEMPUP];/*Release 5.2.1.6 */
		$LAYER_SALARY_TEMP = $data[LAYER_SALARY_TEMP];
		$LAYER_SALARY_FULL = $data[LAYER_SALARY_FULL];
		$LAYER_SALARY_MIDPOINT = $data[LAYER_SALARY_MIDPOINT];
		$LAYER_SALARY_MIDPOINT1 = $data[LAYER_SALARY_MIDPOINT1];
		$LAYER_SALARY_MIDPOINT2 = $data[LAYER_SALARY_MIDPOINT2];
		$LAYER_EXTRA_MIDPOINT = $data[LAYER_EXTRA_MIDPOINT];
		$LAYER_EXTRA_MIDPOINT1 = $data[LAYER_EXTRA_MIDPOINT1];
		$LAYER_EXTRA_MIDPOINT2 = $data[LAYER_EXTRA_MIDPOINT2];
		$UPDATE_USER = $data[UPDATE_USER];
                //$LAYER_SALARY_TEMPUP = $data[LAYER_SALARY_TEMPUP];/*เดิม*/
                $LAYER_SALARY_TEMPUP = $data[LAYER_SALARY_MAX];/*Release 5.2.1.6 */
                
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		
		// primary key
		${"old_LAYER_TYPE"} = $LAYER_TYPE;
		${"old_LEVEL_NO"} = $LEVEL_NO;
		${"old_LAYER_NO"} = $LAYER_NO;

		$SALARY_TYPE = 1;
		if($LAYER_NO==0) $SALARY_TYPE = 2;
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$LAYER_TYPE = 1;
		$LEVEL_NO = "";
		$LAYER_NO = "";
		$LAYER_SALARY = "";
		$LAYER_ACTIVE = 1;
		$LAYER_SALARY_MIN = "";
		$LAYER_SALARY_MAX = "";
		$LAYER_SALARY_TEMP = "";
		$LAYER_SALARY_FULL = "";
		$LAYER_SALARY_MIDPOINT = "";
		$LAYER_SALARY_MIDPOINT1 = "";
		$LAYER_SALARY_MIDPOINT2 = "";
		$LAYER_EXTRA_MIDPOINT = "";
		$LAYER_EXTRA_MIDPOINT1 = "";
		$LAYER_EXTRA_MIDPOINT2 = "";
                $LAYER_SALARY_TEMPUP = "";
		$SALARY_TYPE = 1;
		$PER_TYPE = 1;
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
	
?>