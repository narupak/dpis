<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd); 

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	//$report_title = trim(iconv("utf-8","tis620",urldecode($report_title)));
	$report_title1 = "��§ҹT0203 �Ѵ��â����� OT";
	$worksheet = &$workbook->addworksheet('T0203');
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//
	if($P_COL_OTSCAN=='T' || empty($P_COL_OTSCAN)){$Scan="(Scanx)"; }else{$Scan="(Process)";}

	function print_header($xlsRow,$Scan){
		global $worksheet;

		$worksheet->set_column(0, 0, 8);
		$worksheet->set_column(1, 1, 25);
		$worksheet->set_column(2, 2, 10);
		$worksheet->set_column(3, 3, 10);
		$worksheet->set_column(4, 4, 10);
		$worksheet->set_column(5, 5, 10);
		$worksheet->set_column(6, 6, 10);
		$worksheet->set_column(7, 7, 10);
		$worksheet->set_column(8, 8, 10);
		$worksheet->set_column(9, 9, 10);
		$worksheet->set_column(10, 10, 10);
		$worksheet->set_column(11, 11, 10);
		$worksheet->set_column(12, 12, 10);
		$worksheet->set_column(13, 13, 10);
		
		
		
		$worksheet->write($xlsRow, 0, "�ӴѺ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "����-ʡ��", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "�ѹ���� OT", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "�������ѹ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "������� ".$Scan, set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "�����͡ ".$Scan, set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "����������� OT ���", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "��������ش OT ���", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "����������� OT ���", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 9, "��������ش OT ���", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 10, "���ҷ��� OT", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 11, "�ӹǹ�������", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 12, "�ӹǹ�Թ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 13, "͹��ѵ�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // end if


  /*---------------------------------------------------------------*/
    //	��˹���� default timezone		//phpinfo();
	function set_default_timezone($timezone){
		if (version_compare(phpversion(), '5', '>=')){
			if(function_exists('date_default_timezone_set')) { 
				$result_set = date_default_timezone_set($timezone); 	// PHP  >= 5.1.0
				//echo date_default_timezone_get();	// PHP  >= 5.1.0
			} 
		}else{		// < version 5
			$result_set = ini_set('date.timezone',$timezone);
		}
	return $result_set;
	}
    
	
	set_default_timezone('Asia/Bangkok');	// ���������������⫹����˹�
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$CREATE_DATE = date("Y-m-d H:i A", time());
	$TODATE = date("Y-m-d");
	$temp_date = explode("-", $TODATE);
	$temp_todate = ($temp_date[0]) ."-". $temp_date[1] ."-". $temp_date[2];
	if(!$search_date_min) {
		$search_date_min = "01/". $temp_date[1] ."/". ($temp_date[0] + 543);
	}
	if(!$search_date_max) {		// �ҡ��͹�Ѩ�غѹ��ա 60 �ѹ
        $search_abs_endmonth = ($temp_date[1] + 0);
        if($search_abs_endmonth<10) $search_abs_endmonth = "0".($search_abs_endmonth);
        $search_abs_endyear = $temp_date[0];
		$max_date = get_max_date($search_abs_endmonth, $search_abs_endyear);
		//$search_date_max = $max_date."/". $search_abs_endmonth ."/". ($search_abs_endyear + 543);
        $search_date_max = date("d")."/". $search_abs_endmonth ."/". ($search_abs_endyear + 543);
	}


    $cmd2 = " select POS_ID,ORG_ID,POEM_ID,POEMS_ID,POT_ID,ORG_ID_1,PER_OT_FLAG from PER_PERSONAL where PER_ID=$SESS_PER_ID"; 
    $db_dpis2->send_cmd($cmd2);
    $data2 = $db_dpis2->get_array();
    $PER_POS_ID = $data2[POS_ID]; /*�Ҥ������˹��§ҹ��������� ����Ҫ���*/
    $PER_POEM_ID = $data2[POEM_ID]; /*�Ҥ������˹��§ҹ��������� �١��ҧ��Ш�*/
    $PER_POEMS_ID = $data2[POEMS_ID]; /*�Ҥ������˹��§ҹ��������� ��ѡ�ҹ�Ҫ���*/
    $PER_POT_ID = $data2[POT_ID]; /*�Ҥ������˹��§ҹ��������� �١��ҧ���Ǥ���*/
    $PER_ORG_ID = $data2[ORG_ID]; /*�Ҥ������˹��§ҹ�����ͺ���§ҹ*/
	
	$PER_ORG_ID_1 = $data2[ORG_ID_1]; /*�Ҥ������˹��§ҹ�����ͺ���§ҹ ��ӡ����ӹѡ 1 �дѺ*/
	$PER_OT_FLAG = $data2[PER_OT_FLAG]; /*������Ңͧ OT ���������*/

    
    //������������� ��. ��� �������--------------------------------
    $cmd4 = "	select	 b.CODE from	user_detail a, user_group b
					where a.group_id=b.id AND a.ID=".$SESS_USERID;
    $db_dpis2->send_cmd($cmd4);
    $data4 = $db_dpis2->get_array();
    if ($data4[CODE]) {
        $NAME_GROUP_HRD = $data4[CODE];
    }else{
        $NAME_GROUP_HRD = "";
    }
    
    if(empty($select_org_structure_ot)){$select_org_structure_ot=0;}
    
    
    
    
     if ($SESS_USERGROUP !=1 && $NAME_GROUP_HRD!='HRD'){
    	
      	if($P_OTTYPE_ORGANIZE ==2){ // �ͺ����
                $cmd3 = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$PER_ORG_ID"; 
                $db_dpis2->send_cmd($cmd3);
                $data3 = $db_dpis2->get_array();
                $search_org_name = $data3[ORG_NAME];
                $search_org_id = $PER_ORG_ID;
				
				if($PER_OT_FLAG!=$PER_ORG_ID){
                    $cmd3 = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$PER_OT_FLAG"; 
                    $db_dpis2->send_cmd($cmd3);
                    $data3 = $db_dpis2->get_array();
                    $search_org_name_1 = $data3[ORG_NAME];
                    $search_org_id_1 = $PER_OT_FLAG;
                }
         }else{ // ������
                if($PER_POS_ID){ /*�Ҥ������˹��§ҹ��������� ����Ҫ���*/
                	$fromTable = "PER_POSITION";
                    $whereTable = " c.POS_ID=$PER_POS_ID";
                }elseif($PER_POEM_ID){ /*�Ҥ������˹��§ҹ��������� �١��ҧ��Ш�*/
                	$fromTable = "PER_POS_EMP";
                    $whereTable = " c.POEM_ID=$PER_POEM_ID";
                }elseif($PER_POEMS_ID){ /*�Ҥ������˹��§ҹ��������� ��ѡ�ҹ�Ҫ���*/
                	$fromTable = "PER_POS_EMPSER";
                    $whereTable = " c.POEMS_ID=$PER_POEMS_ID";
                }elseif($PER_POT_ID){ /*�Ҥ������˹��§ҹ��������� �١��ҧ���Ǥ���*/
                	$fromTable = "PER_POS_TEMP";
                    $whereTable = " c.POT_ID=$PER_POT_ID";
                }
                
                $cmd3 = " select g.ORG_NAME ,g.ORG_ID,g1.ORG_NAME as ORG_NAME_1 ,g1.ORG_ID as ORG_ID_1
                   from $fromTable c 
                   LEFT JOIN PER_ORG  g on (g.ORG_ID=c.ORG_ID)
				   LEFT JOIN PER_ORG  g1 on (g1.ORG_ID=c.ORG_ID_1)
                   where $whereTable "; 
                $db_dpis2->send_cmd($cmd3);
                $data3 = $db_dpis2->get_array();
                $search_org_name = $data3[ORG_NAME];
                $search_org_id = $data3[ORG_ID];
				
				if($PER_OT_FLAG==$data3[ORG_ID_1]){
                	$search_org_name_1 = $data3[ORG_NAME_1];
                	$search_org_id_1 = $data3[ORG_ID_1];
                }
         }
        
        if(!$search_date_min) { // ���ѹ������������ѧ�ҡ�Դ�Ǵ�ͷ� ��͹����ش
    
            $cmd2 = " select max(CONTROL_ID) as CONTROL_ID  from TA_PER_OT where DEPARTMENT_ID=$search_org_id and CONTROL_ID IS NOT NULL "; 
            $db_dpis2->send_cmd($cmd2);
            $data2 = $db_dpis2->get_array();
            
            if (!empty($data2[CONTROL_ID])){
                $cmd2 = " select END_DATE  from TA_PER_OT_CONTROL where CONTROL_ID=".$data2[CONTROL_ID]; 
                $db_dpis2->send_cmd($cmd2);
                $data3 = $db_dpis2->get_array();

                if (!empty($data3[END_DATE])){
                	$dateEnd_date = substr($data3[END_DATE],8,2)."-".substr($data3[END_DATE],5,2)."-".substr($data3[END_DATE],0,4);
                    $datetomorow = date('d-m-Y', strtotime("+1 day", strtotime("$dateEnd_date")));
                     $search_date_min = substr($datetomorow,0,2)."/". substr($datetomorow,3,2) ."/". (substr($datetomorow,6,4) + 543);
                }else{
                    $search_date_min = "01/". $temp_date[1] ."/". ($temp_date[0] + 543);
                }
            }else{
            	$search_date_min = "01/". $temp_date[1] ."/". ($temp_date[0] + 543);
            }
            
		}
    }else{
    	 if(!$search_date_min) {
			$search_date_min = "01/". $temp_date[1] ."/". ($temp_date[0] + 543);
		}
    
    }
    
   
    
    /*-------------------------------------------------------------*/
	if( $SESS_USERGROUP ==1 || $NAME_GROUP_HRD =='HRD'){ 
       	if($select_org_head == 1){  // ˹��§ҹ����ѧ�Ѵ 
        	 if($select_org_structure == 0){ //���������
             	if($search_org_id_1){
                	$arr_search_condition[] = "(c.ORG_ID_1=$search_org_id_1 or d.ORG_ID_1=$search_org_id_1 or e.ORG_ID_1=$search_org_id_1 or j.ORG_ID_1=$search_org_id_1)";
                }elseif($search_org_id){
                  //echo "1";
                   $arr_search_condition[] = "(c.ORG_ID=$search_org_id or d.ORG_ID=$search_org_id or e.ORG_ID=$search_org_id or j.ORG_ID=$search_org_id)";
                }else{
                	//echo "2";
                   $arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
                }
            }else{ //����ͺ����
            	if($search_org_id_1){
                	$arr_search_condition[] = "(a.ORG_ID_1=$search_org_id_1)";
                }elseif($search_org_id){
                	//echo "3";
                   $arr_search_condition[] = "(a.ORG_ID=$search_org_id)";
                }else{
                	//echo "4";
                   $arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
                }
            
            }
            
         }else{ // ˹��§ҹ��Ңͧ����ͧ (OT)
         	if($search_org_id_1){
            	$arr_search_condition[] = "(ot.DEPARTMENT_ID=$search_org_id AND ot.ORG_LOWER1=$search_org_id_1)";
            }elseif($search_org_id){
            	//echo "5";
                $arr_search_condition[] = "(ot.DEPARTMENT_ID=$search_org_id AND ot.ORG_LOWER1=-1)";
            }else{
            	//echo "6";
                $arr_search_condition[] = "(ot.ORG_ID=$search_department_id AND ot.DEPARTMENT_ID=-1  )";
            }
         }
    }else{ // ��. �ӹѡ
    	//echo "7";
        if($search_org_id_1){
        	$arr_search_condition[] = "(ot.DEPARTMENT_ID= $search_org_id AND ot.ORG_LOWER1=$search_org_id_1)";
        }else{
    		$arr_search_condition[] = "(ot.DEPARTMENT_ID= $search_org_id AND ot.ORG_LOWER1=-1)";
        }
    }
   
   	if(trim($search_name)) $arr_search_condition[] = "(a.PER_NAME like '".trim($search_name)."%' or UPPER(a.PER_ENG_NAME) like UPPER('".trim($search_name)."%'))";
   	if(trim($search_surname)) $arr_search_condition[] = "(a.PER_SURNAME like '".trim($search_surname)."%' or UPPER(a.PER_ENG_SURNAME) like UPPER('".trim($search_surname)."%'))";
  
    /*if(trim($search_cardno)) $arr_search_condition[] = "(a.PER_CARDNO ='".trim($search_cardno)."')";
    if(trim($search_offno)) $arr_search_condition[] = "(a.PER_OFFNO ='".trim($search_offno)."')";
    
    if(trim($search_pos_no))  {	
		if ($search_per_type == 1 || $search_per_type==5)
			$arr_search_condition[] = "(trim(c.POS_NO) = '".trim($search_pos_no)."')";
		elseif ($search_per_type == 2) 
			$arr_search_condition[] = "(trim(d.POEM_NO) = '".trim($search_pos_no)."')";		
		elseif ($search_per_type == 3) 	
			$arr_search_condition[] = "(trim(e.POEMS_NO) = '".trim($search_pos_no)."')";
		elseif ($search_per_type == 4) 	
			$arr_search_condition[] = "(trim(j.POT_NO) = '".trim($search_pos_no)."')";
		else if ($search_per_type==0)		//������
			$arr_search_condition[] = "((trim(c.POS_NO) = '".trim($search_pos_no)."') or (trim(d.POEM_NO) = '".trim($search_pos_no)."') or (trim(e.POEMS_NO) = '".trim($search_pos_no)."') or (trim(j.POT_NO) = '".trim($search_pos_no)."')) ";
	}
    
    if(trim($search_pos_no_name)){
		if ($search_per_type == 1 || $search_per_type==5)
			$arr_search_condition[] = "(trim(c.POS_NO_NAME) like '".trim($search_pos_no_name)."%')";
		elseif ($search_per_type == 2) 
			$arr_search_condition[] = "(trim(d.POEM_NO_NAME) like '".trim($search_pos_no_name)."%')";		
		elseif ($search_per_type == 3) 	
			$arr_search_condition[] = "(trim(e.POEMS_NO_NAME) like '".trim($search_pos_no_name)."%')";
		elseif ($search_per_type == 4) 	
			$arr_search_condition[] = "(trim(j.POT_NO_NAME) like '".trim($search_pos_no_name)."%')";
		else if ($search_per_type==0)		//������
			$arr_search_condition[] = "((trim(c.POS_NO_NAME) like '".trim($search_pos_no_name)."%') or (trim(d.POEM_NO_NAME) like '".trim($search_pos_no_name)."%') or 
			(trim(e.POEMS_NO_NAME) like '".trim($search_pos_no_name)."%') or (trim(j.POT_NO_NAME) like '".trim($search_pos_no_name)."%')) ";
	}*/
    
    if(trim($search_pay_no))  $arr_search_condition[] = "(trim(c.POS_NO) = '$search_pay_no' and a.PER_TYPE = 1)";
    if(trim($search_level_no)) $arr_search_condition[] = "(trim(a.LEVEL_NO) = '". trim($search_level_no) ."')";
    if(trim($search_pl_code)) $arr_search_condition[] = "(trim(c.PL_CODE) = '". trim($search_pl_code) ."')";
    if(trim($search_pm_code)) $arr_search_condition[] = "(trim(c.PM_CODE) = '". trim($search_pm_code) ."')";
    if(trim($search_per_type)) 	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
    
    $condition_date = "";
    if($search_date_min && $search_date_max){ 
		 $tmpsearch_date_min =  save_date($search_date_min);
         $tmpsearch_date_max =  save_date($search_date_max);
         $arr_search_condition[] = " ( ot.OT_DATE BETWEEN  '$tmpsearch_date_min' and '$tmpsearch_date_max') ";
         $condition_date = " AND  ( OT_DATE BETWEEN  '$tmpsearch_date_min' and '$tmpsearch_date_max') ";
	}else if($search_date_min && empty($search_date_max)){ 
		 $tmpsearch_date_min =  save_date($search_date_min);
         $arr_search_condition[] = " (ot.OT_DATE='$tmpsearch_date_min') ";
         $condition_date = " AND (OT_DATE='$tmpsearch_date_min') ";
    }else if(empty($search_date_min) && $search_date_max){ 
		 $tmpsearch_date_max =  save_date($search_date_max);
         $arr_search_condition[] = " (ot.OT_DATE='$tmpsearch_date_max') ";
         $condition_date = " AND (OT_DATE='$tmpsearch_date_max') ";
    }
    
    /*�礡�äӹǹ Ot �ҡ��Ңͧ����ͧ*/
    $condition_OT = "";
    if ($SESS_USERGROUP !=1 && $NAME_GROUP_HRD!='HRD'){
    	if($select_org_structure_ot=="0"){ // ������
        	if($search_org_id_1){
     			$condition_OT = " AND DEPARTMENT_ID=" .$search_org_id." AND ORG_LOWER1=".$search_org_id_1;
            }else{
            	$condition_OT = " AND DEPARTMENT_ID=" .$search_org_id;
            }
        }else{ // �ͺ����
        	if($search_org_id_1){
     			$condition_OT = " AND DEPARTMENT_ID=" .$PER_ORG_ID." AND ORG_LOWER1=".$search_org_id_1;
            }else{
            	$condition_OT = " AND DEPARTMENT_ID=" .$PER_ORG_ID;
            }
        }
    }else{ 
    	if($select_org_head=="0"){
        	if($search_org_id_1){
            	$condition_OT = " AND DEPARTMENT_ID=" .$search_org_id." AND ORG_LOWER1=".$search_org_id_1;
        	}elseif($search_org_id){
            	$condition_OT = " AND DEPARTMENT_ID=$search_org_id";
            }else{
            	$condition_OT = " AND ORG_ID=$search_department_id AND DEPARTMENT_ID=-1";
            }
     		
        }
        
    }

    
    
    $search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);


		$cmd = "	select 	
							ot.PER_ID,a.PER_TYPE,
							g.PN_SHORTNAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW,
							c.POS_NO,d.POEM_NO,e.POEMS_NO,ot.OT_DATE,ot.HOLYDAY_FLAG,
							ot.START_TIME,ot.END_TIME,ot.NUM_HRS,ot.AMOUNT,ot.PER_CARDNO,
							ot.AUDIT_FLAG,ot.ALLOW_FLAG,ot.APPROVE_FLAG,
							ot.AUDIT_USER,ot.ALLOW_USER,ot.APPROVE_USER,ot.SET_FLAG,
							ot.CONTROL_ID,
							(select to_char(min(TIME_STAMP),'hh24:mi') from PER_TIME_ATTENDANCE 
							   where per_id=ot.PER_ID AND to_char(TIME_STAMP,'YYYY-MM-DD')=ot.OT_DATE) AS TIME_MIN ,
							 case when (select to_char(min(TIME_STAMP),'hh24:mi') from PER_TIME_ATTENDANCE 
							   where per_id=ot.PER_ID AND to_char(TIME_STAMP,'YYYY-MM-DD')=ot.OT_DATE) = 
							   (select to_char(max(TIME_STAMP),'hh24:mi') from PER_TIME_ATTENDANCE 
							   where per_id=ot.PER_ID AND to_char(TIME_STAMP,'YYYY-MM-DD')=ot.OT_DATE) 
							   then  null 
							   else (select to_char(max(TIME_STAMP),'hh24:mi') from PER_TIME_ATTENDANCE 
							   where per_id=ot.PER_ID AND to_char(TIME_STAMP,'YYYY-MM-DD')=ot.OT_DATE)
							   end
							   AS TIME_MAX_IN_NOOUT,
							 (select to_char(max(TIME_STAMP),'hh24:mi') from PER_TIME_ATTENDANCE 
							   where per_id=ot.PER_ID AND to_char(TIME_STAMP,'YYYY-MM-DD')=ot.OT_DATE) AS TIME_MAX_IN_OUT,
							   ot.START_TIME_BFW,ot.END_TIME_BFW,ot.OT_STATUS
							from  TA_PER_OT ot
							left join PER_PERSONAL a on(a.PER_ID=ot.PER_ID) 
							left join PER_ORG b on(b.ORG_ID=a.ORG_ID) 
							left join PER_POSITION c on(c.POS_ID=a.POS_ID) 
							left join PER_POS_EMP d on(d.POEM_ID=a.POEM_ID) 
							left join PER_POS_EMPSER e on(e.POEMS_ID=a.POEMS_ID) 
							left join PER_LEVEL f on(f.LEVEL_NO=a.LEVEL_NO) 
							left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE) 
							left join PER_MGT h on(h.PM_CODE=c.PM_CODE)
							left join PER_LINE i on(i.PL_CODE=c.PL_CODE)
							left join PER_POS_TEMP j on (j.POT_ID=a.POT_ID)
								$search_condition 
							order by 	a.PER_NAME ASC,a.PER_SURNAME ASC ,ot.OT_DATE desc ";

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title1", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1,$Scan);
		$data_count = 1;
		$data_num = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_num++;

			$DATA_FULLNAME_SHOW = trim($data[FULLNAME_SHOW]);
			$DATA_OT_DATE = show_date_format($data[OT_DATE], $DATE_DISPLAY);
			
			if($data[HOLYDAY_FLAG]==0){
				$DATA_HOLYDAY_FLAG="�ѹ�ӧҹ";
				
				if($data[OT_STATUS]==1){
					$DATA_OT_STATUS="���";
				}elseif($data[OT_STATUS]==3){
					$DATA_OT_STATUS="���������";
				}else{
					$DATA_OT_STATUS="���";
				}
			}else{
				$DATA_HOLYDAY_FLAG="�ѹ��ش";
				$DATA_OT_STATUS="";
			}
			
			$DATA_START_TIME="";
			$DATA_END_TIME="";
			if($data[OT_STATUS]==2 || $data[OT_STATUS]==3){ //���,���������,�ѹ��ش
				if($data[START_TIME] != $data[END_TIME]){
					if(!empty($data[START_TIME])){
							$DATA_START_TIME=substr($data[START_TIME],0,2).":".substr($data[START_TIME],2,2). " �.";
					}
					
					if(!empty($data[END_TIME])){
						$DATA_END_TIME=substr($data[END_TIME],0,2).":".substr($data[END_TIME],2,2). " �.";
				   }
				}
				
			 } 
			 
			 
			 
			$DATA_START_BFW="";
			$DATA_END_BFW="";
			if($data[HOLYDAY_FLAG]==0){ // ����ʴ��ѹ��ش
				if($data[OT_STATUS]==1 || $data[OT_STATUS]==3){ //���,���������
					if($data[START_TIME_BFW] != $data[END_TIME_BFW]){
						if(!empty($data[START_TIME_BFW])){
								$DATA_START_BFW=substr($data[START_TIME_BFW],0,2).":".substr($data[START_TIME_BFW],2,2). " �.";
						}
						
						if(!empty($data[END_TIME_BFW])){
							$DATA_END_BFW=substr($data[END_TIME_BFW],0,2).":".substr($data[END_TIME_BFW],2,2). " �.";
					   }
					}
				 }
			}
			
			$DATA_NUM_HRS ="";
			if(!empty($data[NUM_HRS]) && $data[NUM_HRS] != 0){
				$DATA_NUM_HRS = $data[NUM_HRS];
			}
			
			if(!empty($data[AMOUNT])){
				$DATA_AMOUNT = number_format($data[AMOUNT]);
			}else{
				$DATA_AMOUNT ="";
			}
			$DATA_SET_FLAG = $data[SET_FLAG];
			if($DATA_SET_FLAG==0){
				$DATA_AMOUNT="";
				$DATA_NUM_HRS="";
			}else{
				$DATA_AMOUNT=$DATA_AMOUNT;
				$DATA_NUM_HRS=$DATA_NUM_HRS;
			}
			
			$DATA_PAY_DATE="";
			if($data[CONTROL_ID]){
				$cmd = " select PAY_DATE,CONFIRM_FLAG from TA_PER_OT_CONTROL where CONTROL_ID=$data[CONTROL_ID] ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				if($data2[CONFIRM_FLAG]==1){
					$DATA_PAY_DATE = show_date_format(substr($data2[PAY_DATE],0,10), $DATE_DISPLAY);
				}else{
					$DATA_PAY_DATE = "";
				}
			}
			
			$DATA_TIME_MIN ="";
			$DATA_TIME_MAX ="";
			if($P_COL_OTSCAN=='T' || empty($P_COL_OTSCAN)){
				if($data[TIME_MIN]){
					$DATA_TIME_MIN = $data[TIME_MIN]." �.";
				}
				
				if($data[TIME_MAX_IN_OUT]){
					$DATA_TIME_MAX = $data[TIME_MAX_IN_OUT]." �.";
				}
			}else{
			
				$cmd2 = "	  select 	to_char(min(w.SCAN_ENTTIME),'hh24:mi') AS SCAN_ENTTIME,to_char(max(w.SCAN_EXITTIME),'hh24:mi') AS  SCAN_EXITTIME
					  from 		PER_WORK_TIME w
					  left join PER_WORK_CYCLE c on(c.WC_CODE=w.WC_CODE)
					  where 		w.PER_ID=".$data[PER_ID]." and TO_CHAR(w.WORK_DATE,'yyyy-mm-dd')='".$data[OT_DATE]."'";

				$db_dpis2->send_cmd($cmd2);
				$data2 = $db_dpis2->get_array();
				
				if($data2[SCAN_ENTTIME]){ 
					$DATA_TIME_MIN = $data2[SCAN_ENTTIME]. " �.";
				}
				
				if($data2[SCAN_EXITTIME]){ 
					$DATA_TIME_MAX = $data2[SCAN_EXITTIME]. " �.";
				}
			
			
			}
			
			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $data_num, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $DATA_FULLNAME_SHOW, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $DATA_OT_DATE, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $DATA_HOLYDAY_FLAG, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $DATA_TIME_MIN, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 5, $DATA_TIME_MAX, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 6, $DATA_START_BFW, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 7, $DATA_END_BFW, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 8, $DATA_START_TIME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 9, $DATA_END_TIME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 10, $DATA_OT_STATUS, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 11, $DATA_NUM_HRS, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 12, $DATA_AMOUNT, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 13, $DATA_PAY_DATE, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ����բ����� *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"T0203_ALLOW.xls\"");
	header("Content-Disposition: inline; filename=\"T0203_ALLOW.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>