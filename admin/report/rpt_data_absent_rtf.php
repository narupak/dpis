<?php
	include("../../php_scripts/connect_database.php");
	include ("../php_scripts/session_start.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require("../../RTF/rtf_class.php");

  	ini_set("max_execution_time", $max_execution_time); 
	
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$TODATE = date("Y-m-d");
	$MAX_ABSENT_04 = 10;		// ��˹���� ���Է���Ҿѡ��͹��Шӻ� (�ѹ)
	$FONTSIZE12 = "12";
	$FONTSIZE14 = "14";
	$FONTSIZE16 = "16";

	// read template content
	if($_GET[ABS_ID])		$ABS_ID = $_GET[ABS_ID];
	if($_GET[AB_CODE])	$AB_CODE = $_GET[AB_CODE];
	if(trim($AB_CODE)=="01" || trim($AB_CODE)=="02" || trim($AB_CODE)=="03"){
		//$fname = "rpt_data_absent_template_1.rtf";
		$ABS_NOTE = "";
	}elseif(trim($AB_CODE)=="04"){
		//$fname = "rpt_data_absent_template_2.rtf";
	}
	
	//echo $ABS_ID ; die();
	if(!$FONTSIZE)	$FONTSIZE = $FONTSIZE16;
	if($SESS_USERGROUP==1) $number_doc = $SESS_USERGROUP;
	if($SESS_PER_ID)		$number_doc = $SESS_PER_ID;
	$fname = "rpt_data_absent_".$number_doc."_".$ABS_ID.".rtf";

	if (!$font) $font = "AngsanaUPC";

//	$RTF = new RTF("a4", 750, 500, 500, 500);
	$RTF = new RTF("a4", 1150, 720, 600, 200);

	$RTF->set_default_font($font, $FONTSIZE);
	$RTF->set_table_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if(trim($LET_DATE)){
		$arr_temp = explode("/", $LET_DATE);
		//$LET_DATE = ($arr_temp[0] + 0) ."   ". $month_full[($arr_temp[1] + 0)][TH] ."   �.�.  ". $arr_temp[2];
		$LET_DATE = ($arr_temp[2])."-".($arr_temp[1] + 0)."-".($arr_temp[0] + 0);
		$LET_DATE = show_date_format($LET_DATE,$DATE_DISPLAY);
	} // end if
	
	$heading_width1[0] = "12";
	$heading_width1[1] = "12";
	$heading_width1[2] = "12";
	$heading_width1[3] = "12";
	
	$heading_width2[0] = "12";
	$heading_width2[1] = "12";
	$heading_width2[2] = "12";
	
        function show_result_checkboxV2($result,$result_type,$FONTSPACE){
            global $RTF,$font,$FONTSIZE;
		
            if($result_type=="REVIEW_FLAG"){
                $result_title="��繤��͹حҵ";
            }else if($result_type=="APPROVE_FLAG"){
                $result_title=	"͹حҵ";
            }

            $RTF->set_font($font,$FONTSIZE);
            $RTF->color("0");	// 0=BLACK
            $RTF->cell($FONTSPACE, 0, "left", "0");
            if($result==1)
                $RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "left", 0);
            else
                $RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "left", 0);
            
            $RTF->cell(convert2thaidigit($result_title),"20", "left", "0");  
            if($result==2)
                $RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "left", 0);
            else
                $RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "left", 0);
            
            $RTF->cell(convert2thaidigit("���".$result_title), "20", "left", "0");
        }
        
	function show_result_checkbox($result,$result_type,$FONTSPACE){
		global $RTF,$font,$FONTSIZE;
		
		if($result_type=="REVIEW_FLAG")	{					$result_title="��繤��͹حҵ";		}
		else if($result_type=="APPROVE_FLAG"){		$result_title=	"͹حҵ";					}

		$RTF->open_line();
		$RTF->set_font($font,$FONTSIZE);
		$RTF->color("0");	// 0=BLACK
		$RTF->cell($FONTSPACE, 50, "left", "0");
		if($result==1)	$RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "right", 0);
		else						$RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "right", 0);
		$RTF->cell(convert2thaidigit($result_title),"20", "left", "0");  
//		$RTF->cell("","5", "left", "0");  
		if($result==2)	$RTF->cellImage("../images/checkbox_check.jpg", 100, 3, "right", 0);
		else						$RTF->cellImage("../images/checkbox_blank.jpg", 100, 3, "right", 0);
		$RTF->cell(convert2thaidigit("���".$result_title), "20", "left", "0");
		$RTF->close_line();
		
		/*$RTF->open_line();
		$RTF->cell("", "200", "center", "0");
		$RTF->close_line();*/
		//$RTF->ln();
	}

	function print_header($header_select){
		global $RTF, $heading_width1, $heading_width2, $REVIEW1_NOTE, $REVIEW2_NOTE;
		global $font,$FONTSIZE;
		
		if($header_select == 1){
			$RTF->open_line();
			$RTF->set_font($font, $FONTSIZE);
			$RTF->color("0");	// 0='BLACK'
			$RTF->cell($RTF->bold(1)."��������".$RTF->bold(1), $heading_width1[0], "center", "0", "TRL");	
			$RTF->cell($RTF->bold(1)."��������".$RTF->bold(1).$RTF->bold(0)."(�ѹ�ӡ��)", $heading_width1[1], "center", "0", "TRL");
			$RTF->cell($RTF->bold(1)."�Ҥ��駹��".$RTF->bold(1).$RTF->bold(0)." (�ѹ�ӡ��)", $heading_width1[2], "center", "0", "TRL");
			$RTF->cell($RTF->bold(1)."�����".$RTF->bold(1).$RTF->bold(0)." (�ѹ�ӡ��)".$RTF->bold(0), $heading_width1[3], "center", "0", "TRL");
            if($REVIEW2_NOTE){//����դ�����繼��Ϣ�鹵��˹�͢��� �����Ҥ����������ʴ�
               $RTF->cell($REVIEW2_NOTE, 50, "left", "0", "");
                
            }else if($REVIEW1_NOTE && (empty($REVIEW2_NOTE))){//����դ�����繼��Ϣ�鹵���м��Ϣ�鹵��˹�͢�����ҧ �����Ҥ�����繼��Ϣ�鹵�����ʴ�
                $RTF->cell($REVIEW1_NOTE, 50, "left", "0", "");
                
            }else{
                $RTF->cell( str_repeat(".", 190), 50, "left", "0", "");//�����ҧ��� 2 ������ʴ��ش
            }
			$RTF->close_line();
			 
//			$RTF->open_line();			
//			$RTF->set_font($font, $FONTSIZE);
//			$RTF->color("0");	// 0='BLACK'
//			$RTF->cell("", $heading_width1[0], "center", "0", "RBL");
//			$RTF->cell("(�ѹ�ӡ��)", $heading_width1[1], "center", "0", "RBL");
//			$RTF->cell("(�ѹ�ӡ��)", $heading_width1[2], "center", "0", "RBL");
//			$RTF->cell("(�ѹ�ӡ��)", $heading_width1[3], "center", "0", "RBL");
//           
//                        //$RTF->cell($RTF->bold(1).$RTF->underline(1)."������繼��ѧ�Ѻ�ѭ��".$RTF->underline(0).$RTF->bold(0), 50, "left", "0", "");
//			$RTF->close_line();
		}else if($header_select == 2){
			$RTF->open_line();			
			$RTF->set_font($font, $FONTSIZE);
			$RTF->color("0");	// 0='BLACK'
			$RTF->cell($RTF->bold(1)."��������".$RTF->bold(1), $heading_width2[0], "center", "0", "TRL");
			$RTF->cell($RTF->bold(1)."�Ҥ��駹��".$RTF->bold(1), $heading_width2[1], "center", "0", "TRL");
			$RTF->cell($RTF->bold(1)."�����".$RTF->bold(1).$RTF->bold(0), $heading_width2[2], "center", "0", "TRL");
			$RTF->close_line();
			
			
			$RTF->open_line();			
			$RTF->set_font($font, $FONTSIZE);
			$RTF->color("0");	// 0='BLACK'
			$RTF->cell("(�ѹ�ӡ��)", $heading_width2[0], "center", "0", "RBL");
			$RTF->cell("(�ѹ�ӡ��)", $heading_width2[1], "center", "0", "RBL");
			$RTF->cell("(�ѹ�ӡ��)", $heading_width2[2], "center", "0", "RBL");
			$RTF->cell("", "14", "left", "0"); 
			$RTF->cell($RTF->bold(1).$RTF->underline(1)."������繼��ѧ�Ѻ�ѭ��".$RTF->underline(0).$RTF->bold(0), "50", "left", "0");
			$RTF->close_line();
		}
	} // function			
	
	//#### �֧����� ####//
	function getPIC_SIGN($PER_ID,$PER_CARDNO){
		global $db_dpis , $db_dpis2;
	
		$PIC_SIGN="";
		//���ٻ����������
		$cmd = " select * 
                         from PER_PERSONALPIC
                         where PER_ID=$PER_ID and PER_CARDNO = '$PER_CARDNO' and  PIC_SIGN=1 and PIC_SHOW=1
                         order by PER_PICSEQ ";
		$count_pic_sign=$db_dpis->send_cmd($cmd);
                
		if($count_pic_sign>0){	
		$data = $db_dpis->get_array();
		$TMP_PIC_SEQ = $data[PER_PICSEQ];
		$current_list .= ((trim($current_list))?",":"") . $TMP_PIC_SEQ;
		$T_PIC_SEQ = substr("000",0,3-strlen("$TMP_PIC_SEQ"))."$TMP_PIC_SEQ";
		$TMP_SERVER = $data[PIC_SERVER_ID];
		$TMP_PIC_SIGN= $data[PIC_SIGN];
		
		if ($TMP_SERVER) {
			$cmd1 = " SELECT * FROM OTH_SERVER WHERE SERVER_ID=$TMP_SERVER ";
			$db_dpis2->send_cmd($cmd1);
			$data2 = $db_dpis2->get_array();
			$tmp_SERVER_NAME = trim($data2[SERVER_NAME]);
			$tmp_ftp_server = trim($data2[FTP_SERVER]);
			$tmp_ftp_username = trim($data2[FTP_USERNAME]);
			$tmp_ftp_password = trim($data2[FTP_PASSWORD]);
			$tmp_main_path = trim($data2[MAIN_PATH]);
			$tmp_http_server = trim($data2[HTTP_SERVER]);
		} else {
			$TMP_SERVER = 0;
			$tmp_SERVER_NAME = "";
			$tmp_ftp_server = "";
			$tmp_ftp_username = "";
			$tmp_ftp_password = "";
			$tmp_main_path = "";
			$tmp_http_server = "";
		}
		$SIGN_NAME = "";
		if($TMP_PIC_SIGN==1){ $SIGN_NAME = "SIGN"; }
		if (trim($PER_CARDNO) && trim($PER_CARDNO) != "NULL") {
			$TMP_PIC_NAME = $data[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg".($tmp_SERVER_NAME ? " [".$tmp_SERVER_NAME."]" : "");
			//$TMP_SHOW_PIC = ($tmp_http_server ? $tmp_http_server."/" : "").$data[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg";
		} else {
			$TMP_PIC_NAME = $data[PER_PICPATH].$data[PER_GENNAME]."-".$SIGN_NAME.$T_PIC_SEQ.".jpg".($tmp_SERVER_NAME ? " [".$tmp_SERVER_NAME."]" : "");
			//$TMP_SHOW_PIC = ($tmp_http_server ? $tmp_http_server."/" : "").$data[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg";
		}
		if(file_exists("../".$TMP_PIC_NAME)){
			$PIC_SIGN = "../".$TMP_PIC_NAME;		//	if($PER_CARDNO && $TMP_PIC_NAME)		$PIC_SIGN = "../../attachments/".$PER_CARDNO."/PER_SIGN/".$TMP_PIC_NAME;
		}
		} //end count	
	return $PIC_SIGN;
	}
	
        /**/
        function getTotalBeforeOfYear($ABS_STARTDATE,$PER_ID,$AB_CODE){
            global $db_dpis,$ABS_ID;
			
			//��� $AS_YEAR = substr($ABS_STARTDATE, 6, 4)-543;
			//���� �� �Ե���ѷ�� 25092560
			if(substr($ABS_STARTDATE, 3, 2)>=10){
				$AS_YEAR = substr($ABS_STARTDATE, 6, 4)-542;
			}else{
				$AS_YEAR = substr($ABS_STARTDATE, 6, 4)-543;
			}
			
			//�� ���� �� �Ե���ѷ�� 25092560
            
            $BgnYear = ($AS_YEAR-1).'-10-01';
            $EndYear = $AS_YEAR.'-09-30';
            $cmdCur = "select ABS_STARTDATE,ABS_ENDDATE from PER_ABSENT where abs_id=".$ABS_ID;
            $db_dpis->send_cmd($cmdCur);
            $data = $db_dpis->get_array_array();
            $cur_ABS_STARTDATE= trim($data[0]);
            $cur_ABS_ENDDATE= trim($data[1]);
             
			 // ����Ҥ�ҷ�����������Ƿ�駻է�����ҳ
            /*$cmdTotal = "select  NVL(sum(ABS_DAY),0) AS sumofABS_DAY from PER_ABSENTHIS where PER_ID=$PER_ID 
                        and AB_CODE='$AB_CODE' and ( ABS_STARTDATE between '$BgnYear' and '$EndYear'   
                                                         OR  ABS_ENDDATE between '$BgnYear' and '$EndYear' 
                                                         OR '$BgnYear' between  ABS_STARTDATE and ABS_ENDDATE 
                                                         or '$EndYear' between  ABS_STARTDATE and ABS_ENDDATE ) 
                        and (ABS_STARTDATE not in('$cur_ABS_STARTDATE')  and ABS_ENDDATE not in('$cur_ABS_ENDDATE') ) ";*/
		     // �����Ҥ�����������Ǩҡ ��鹻է�����ҳ���� �Ҩ��֧�ѹ�һѨ�غѹ
			$cmdTotal = "select  NVL(sum(ABS_DAY),0) AS sumofABS_DAY from PER_ABSENTHIS where PER_ID=$PER_ID 
                        and AB_CODE='$AB_CODE' and ( ABS_STARTDATE between '$BgnYear' and '$cur_ABS_STARTDATE'   
                                                         OR  ABS_ENDDATE between '$BgnYear' and '$cur_ABS_STARTDATE' 
                                                         OR '$BgnYear' between  ABS_STARTDATE and ABS_ENDDATE 
                                                         or '$cur_ABS_STARTDATE' between  ABS_STARTDATE and ABS_ENDDATE ) 
                        and (ABS_STARTDATE not in('$cur_ABS_STARTDATE')  and ABS_ENDDATE not in('$cur_ABS_ENDDATE') ) ";
            //if($AB_CODE=='04'){
               // die('<pre>'.$cmdTotal);
            //}
             //echo '<pre>'.$cmdTotal.'<br>';
            
            $count_abs_sum= $db_dpis->send_cmd($cmdTotal);
            $data = $db_dpis->get_array_array();
            if($count_abs_sum  > 0){
                return $sumofABS_DAY= $data[0];
            }else{
                return 0;
            }
        }
        /**/
        
        
	//#### �ʴ��������ѹ������ �ͺ ��лչ�� (��ͧ͹حҵ����)####//
	function get_ABSENT_SUM($PER_ID,$ABS_STARTDATE,$ABS_ENDDATE,$ABS_DAY){
		global $db_dpis, $UPDATE_DATE , $ABS_ID , $AB_CODE;
		global $AB_CODE_01, $DAY1_2, $DAY1_3, $AB_CODE_02, $DAY2_2, $DAY2_3, $AB_CODE_03, $DAY3_2, $DAY3_3, $AB_CODE_04, $DAY4_2, $DAY4_3, $DAY4_4;

		if($ABS_STARTDATE){
			$CHECK_ENDDATE = save_date($ABS_STARTDATE);
			if (substr($ABS_STARTDATE,3,2) > "09" || substr($ABS_STARTDATE,3,2) < "04") $AS_CYCLE = 1;
			elseif (substr($ABS_STARTDATE,3,2) > "03" && substr($ABS_STARTDATE,3,2) < "10")	$AS_CYCLE = 2;
			$AS_YEAR = substr($ABS_STARTDATE, 6, 4);
			if($AS_CYCLE==1){	//��Ǩ�ͺ�ͺ�����
				if (substr($ABS_STARTDATE,3,2) > "09") $AS_YEAR += 1;
				$START_DATE = ($AS_YEAR - 1) . "-10-01";
				$END_DATE = $AS_YEAR . "-03-31";
			}else if($AS_CYCLE==2){
				$START_DATE = $AS_YEAR . "-04-01";
				$END_DATE = $AS_YEAR . "-09-30"; 
			}
		} 
	
		if(!$AS_YEAR){
			if(date("Y-m-d") <= date("Y")."-10-01") $AS_YEAR = date("Y") + 543;
			else $AS_YEAR = (date("Y") + 543) + 1;
		}
		$START_DATE_1 = "01/10/". ($AS_YEAR - 1);
		$END_DATE_1 = "31/03/". $AS_YEAR;
		$START_DATE_2 = "01/04/". $AS_YEAR;
		$END_DATE_2 = "30/09/". $AS_YEAR;
		if (!$CHECK_ENDDATE) $CHECK_ENDDATE =  save_date($END_DATE_2);
	
		if (!$AS_CYCLE){
			if (substr($UPDATE_DATE,5,2) > "09" || substr($UPDATE_DATE,5,2) < "04") $AS_CYCLE = 1;
			elseif (substr($UPDATE_DATE,5,2) > "03" && substr($UPDATE_DATE,5,2) < "10") $AS_CYCLE = 2;
		}
		if($AS_CYCLE == 1){
			$START_DATE =  save_date($START_DATE_1);
			$END_DATE =  save_date($END_DATE_1);
		}else{
			$START_DATE =  save_date($START_DATE_2);
			$END_DATE =  save_date($END_DATE_2);
		} // end if
		//echo "--> $ABS_STARTDATE // $AS_YEAR :: $AS_CYCLE +".substr($ABS_STARTDATE,3,4)." $START_DATE --- $END_DATE";
		
		$AB_CODE_01 = $AB_COUNT_01 = $DAY1_2 = $DAY1_3 = $AB_CODE_02 = $DAY2_2 = $DAY2_3 = $AB_CODE_03 = $AB_COUNT_03 = $DAY3_2 = $DAY3_3  = $AB_CODE_04 = $DAY4_2 = $DAY4_3 = $DAY4_4 = 0;
		if ($BKK_FLAG==1)
			$code = array(	"01", "02", "03", "04" );
		else
			$code = array(	"01", "02", "03", "04" );
		$code_0 = array(	"AB_CODE_01", "AB_CODE_02", "AB_CODE_03", "AB_CODE_04");
		
		$cmd = " SELECT		AB_CODE_01, AB_COUNT_01, AB_CODE_02, AB_CODE_03, AB_COUNT_03, AB_CODE_04 
							FROM		PER_ABSENTSUM 
							WHERE	PER_ID=$PER_ID and AS_YEAR = '$AS_YEAR' ";
		//die('<pre>'.$cmd);
		$count_abs_sum = $db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		if($count_abs_sum  > 0){
			$AB_CODE_01 = $data[AB_CODE_01];		
			$AB_COUNT_01 = $data[AB_COUNT_01];
			$AB_CODE_02 = $data[AB_CODE_02];		
			$AB_CODE_03 = $data[AB_CODE_03];		
			$AB_COUNT_03 = $data[AB_COUNT_03];
			$AB_CODE_04 = $data[AB_CODE_04];		
		} //end if($count_abs_sum  > 0)
                
		//###END SHOW PER_ABSENTSUM  ====================================

		for ( $i=0; $i<count($code); $i++ ) { 
			if ($code[$i]=="04") $CHECK_STARTDATE =  save_date($START_DATE_1);
			else $CHECK_STARTDATE =  $START_DATE;

			$cmd = " select sum(ABS_DAY) as abs_day from PER_ABSENTHIS 
							where PER_ID=$PER_ID and AB_CODE='$code[$i]' and ABS_STARTDATE >= '$CHECK_STARTDATE' and ABS_ENDDATE < '$CHECK_ENDDATE' ";	
			$db_dpis->send_cmd($cmd);
			//echo "-------------------------- $cmd<br>";
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$$code_0[$i] += $data[abs_day];
	
			if ($code[$i]=="01" || $code[$i]=="03") {
				$cmd = " select count(ABS_DAY) as abs_count from PER_ABSENTHIS 
								where PER_ID=$PER_ID and AB_CODE='$code[$i]' and ABS_STARTDATE >= '$START_DATE' and ABS_ENDDATE < '$CHECK_ENDDATE' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				if ($code[$i]=="01") $AB_COUNT_01 += $data[abs_count];
				elseif ($code[$i]=="03") $AB_COUNT_03 += $data[abs_count];
			}
	
			${"ABS_DATE_".$code[$i]} = "";
			// ���ѹ���������ش
			$cmd = " select ABS_STARTDATE, ABS_ENDDATE  
							from PER_ABSENTHIS 
							where PER_ID=$PER_ID and AB_CODE='$code[$i]' and ABS_ENDDATE < '$CHECK_ENDDATE'
							order by ABS_STARTDATE DESC, ABS_ENDDATE DESC, ABS_ID DESC ";
			$count_data = $db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			if ($count_data) {
				$arr_temp = explode("-", $data[ABS_STARTDATE]);
				$ABS_START_DAY = trim($arr_temp[2]);
				$ABS_START_MONTH = trim($arr_temp[1]);
				$ABS_START_YEAR = trim($arr_temp[0] + 543);
				$ABS_STARTDATE_ce_era =  $ABS_START_DAY."/".$ABS_START_MONTH."/".$ABS_START_YEAR ;
				
				$arr_temp = explode("-", $data[ABS_ENDDATE]);
				$ABS_END_DAY = trim($arr_temp[2]);
				$ABS_END_MONTH = trim($arr_temp[1]);
				$ABS_END_YEAR = trim($arr_temp[0] + 543);
				$ABS_ENDDATE_ce_era =  $ABS_END_DAY."/".$ABS_END_MONTH."/".$ABS_END_YEAR ;

	//			echo "$cmd --> $code[$i] =>  ".$ABS_STARTDATE_ce_era." - ".$ABS_ENDDATE_ce_era."<br>";
				${"ABS_DATE_".$code[$i]} = $ABS_ENDDATE_ce_era;		// �ѹ���������ش
			}
		} // end for
		
		if($AS_CYCLE==2){			// �ͺ 2
			$TMP_START_DATE =  save_date($START_DATE_1);
			$TMP_END_DATE =  save_date($END_DATE_1);
			$cmd = " select sum(ABS_DAY) as abs_day from PER_ABSENTHIS 
							where PER_ID=$PER_ID and AB_CODE='04' and ABS_STARTDATE >= '$TMP_START_DATE' and ABS_ENDDATE <= '$TMP_END_DATE' "; 
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$TMP_AB_CODE_04 = $data[abs_day]+0; 
	
			$cmd = " select AB_CODE_04 from PER_ABSENTSUM 
							where PER_ID=$PER_ID and START_DATE = '$TMP_START_DATE' and END_DATE = '$TMP_END_DATE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_AB_CODE_04 += $data[AB_CODE_04]+0; 
		}
		
		//===��ػ
		// 01 = ���� / 02 = ��ʹ / 03 = �Ԩ / 04 = �ѡ��͹
		if($AB_CODE=='01')	$DAY1_2 = $ABS_DAY;
		$DAY1_3 = ($AB_CODE_01 + $DAY1_2);

		if($AB_CODE=='02')	$DAY2_2 = $ABS_DAY;
		$DAY2_3 = ($AB_CODE_02 + $DAY2_2);

		if($AB_CODE=='03')	$DAY3_2 = $ABS_DAY;
		$DAY3_3 = ($AB_CODE_03 + $DAY3_2);

		// ����Ҿѡ��͹
		$cmd = " select VC_DAY from PER_VACATION 
						where VC_YEAR='$AS_YEAR'and PER_ID=$PER_ID ";
		$count = $db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$AB_COUNT_TOTAL_04 = $data[VC_DAY]; 	// �ѹ�Ҿѡ��͹������������㹻է�����ҳ
		$AB_COUNT_04 = $AB_COUNT_TOTAL_04 - $AB_CODE_04;		// �ѹ��������������
		
		if($AB_CODE=='04')	$DAY4_2 = $ABS_DAY;
		$DAY4_3 = ($data[VC_DAY] - $AB_CODE_04); 	// �ѹ�Ҿѡ��͹�������ö���������㹻է�����ҳ
		$DAY4_4 = ($DAY4_3 - $DAY4_2);				// �ѹ�������������� (������� = �ѹ������� - �����������)
	} // end function sum

	if($DPISDB=="odbc"){
		$cmd = " select 	a.AB_CODE, b.AB_NAME, a.ABS_STARTDATE, a.ABS_ENDDATE, a.ABS_DAY, a.ABS_REASON, a.ABS_ADDRESS, 
										a.PER_ID, ABS_LETTER, a.APPROVE_FLAG, a.APPROVE_PER_ID, a.AUDIT_FLAG, a.AUDIT_PER_ID, a.CANCEL_FLAG, 
										a.REVIEW1_FLAG, a.REVIEW1_PER_ID, a.REVIEW2_FLAG, a.REVIEW2_PER_ID, d.RANK_FLAG, d.PN_NAME, c.PER_NAME, c.PER_SURNAME, 
										c.LEVEL_NO, c.PER_TYPE, c.POS_ID, c.POEM_ID, c.POEMS_ID, c.POT_ID  , c.PER_CARDNO,CREATE_DATE, AUDIT_DATE,REVIEW1_DATE,REVIEW2_DATE,APPROVE_DATE,c.ORG_ID AS ORG_ID_ASS
						from		PER_ABSENT a, PER_ABSENTTYPE b,	PER_PERSONAL c, PER_PRENAME d 
						where	a.AB_CODE=b.AB_CODE and a.PER_ID=c.PER_ID and c.PN_CODE=d.PN_CODE and ABS_ID = $ABS_ID ";
	}elseif($DPISDB=="oci8"){	
		$cmd = " select a.AB_CODE, b.AB_NAME, a.ABS_STARTDATE, a.ABS_ENDDATE, a.ABS_DAY, a.ABS_REASON, a.ABS_ADDRESS, 
                                a.PER_ID, ABS_LETTER, NVL(a.APPROVE_FLAG,0) AS APPROVE_FLAG, a.APPROVE_PER_ID, NVL(a.AUDIT_FLAG,0) AS AUDIT_FLAG, a.AUDIT_PER_ID, a.CANCEL_FLAG, 
                                a.REVIEW1_FLAG, a.REVIEW1_PER_ID, NVL(a.REVIEW2_FLAG,0) AS REVIEW2_FLAG, a.REVIEW2_PER_ID, d.RANK_FLAG, d.PN_NAME, c.PER_NAME, c.PER_SURNAME, 
                                c.LEVEL_NO, c.PER_TYPE, c.POS_ID, c.POEM_ID, 
                                c.POEMS_ID, c.POT_ID , c.PER_CARDNO,CREATE_DATE, AUDIT_DATE,REVIEW1_DATE,
                                REVIEW2_DATE,APPROVE_DATE,a.ABS_STARTPERIOD,a.ABS_ENDPERIOD,
                                a.OABS_STARTDATE,a.OABS_STARTPERIOD,a.OABS_ENDDATE,a.OABS_ENDPERIOD,a.OABS_DAY,
                                a.ORI_ABS_STARTDATE,a.ORI_ABS_STARTPERIOD,a.ORI_ABS_ENDDATE,a.ORI_ABS_ENDPERIOD,a.ORI_ABS_DAY,
                                a.POS_STATUS,a.POS_APPROVE,c.ORG_ID AS ORG_ID_ASS,
                                a.REVIEW2_NOTE,a.APPROVE_NOTE
                        from PER_ABSENT a, PER_ABSENTTYPE b, PER_PERSONAL c, PER_PRENAME d 
                        where a.AB_CODE=b.AB_CODE and a.PER_ID=c.PER_ID and c.PN_CODE=d.PN_CODE and ABS_ID = $ABS_ID ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select 	a.AB_CODE, b.AB_NAME, a.ABS_STARTDATE, a.ABS_ENDDATE, a.ABS_DAY, a.ABS_REASON, a.ABS_ADDRESS, 
										a.PER_ID, ABS_LETTER, a.APPROVE_FLAG, a.APPROVE_PER_ID, a.AUDIT_FLAG, a.AUDIT_PER_ID, a.CANCEL_FLAG, 
										a.REVIEW1_FLAG, a.REVIEW1_PER_ID, a.REVIEW2_FLAG, a.REVIEW2_PER_ID, d.RANK_FLAG, d.PN_NAME, c.PER_NAME, c.PER_SURNAME, 
										c.LEVEL_NO, c.PER_TYPE, c.POS_ID, c.POEM_ID, c.POEMS_ID, c.POT_ID  , c.PER_CARDNO,CREATE_DATE, AUDIT_DATE,REVIEW1_DATE,REVIEW2_DATE,APPROVE_DATE,c.ORG_ID AS ORG_ID_ASS
						from		PER_ABSENT a, PER_ABSENTTYPE b, 	PER_PERSONAL c, PER_PRENAME d 
						where	a.AB_CODE=b.AB_CODE and a.PER_ID=c.PER_ID and c.PN_CODE=d.PN_CODE and ABS_ID = $ABS_ID ";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
        //die('<pre>'.$cmd);
        
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();

	$AB_CODE = trim($data[AB_CODE]);
	$AB_NAME = trim($data[AB_NAME]);
	$ORG_ID_ASS = trim($data[ORG_ID_ASS]);
	$TITLE = "��$AB_NAME";
	
	$ABS_STARTDATE = substr($data[ABS_STARTDATE], 0, 10);
	$VC_YEAR = substr($ABS_STARTDATE,0,4) + 543;
	if (substr($ABS_STARTDATE,5,2) > 9) $VC_YEAR += 1;
//	$ABS_STARTDATE = show_date_format($ABS_STARTDATE,$DATE_DISPLAY);		// �ҧ�����͡��˹��ٻẺ�������͹�ѹ 
	$temp_date = explode("-", $ABS_STARTDATE);		// Y-m-d
	$ABS_START_Y= ($temp_date[0] + 543);
	$ABS_START_M= $temp_date[1];
	$ABS_START_D= $temp_date[2];
	$ABS_STARTDATE=convert2thaidigit(($ABS_START_D+0)." ".($month_full[($ABS_START_M + 0)][TH])." ".$ABS_START_Y);
	
	$ABS_ENDDATE = substr($data[ABS_ENDDATE], 0, 10);
//	$ABS_ENDDATE =  show_date_format($ABS_ENDDATE,$DATE_DISPLAY);
	$temp_date = explode("-", $ABS_ENDDATE);		// Y-m-d
	$ABS_END_Y= ($temp_date[0] + 543);
	$ABS_END_M= $temp_date[1];
	$ABS_END_D= $temp_date[2];
	$ABS_ENDDATE=convert2thaidigit(($ABS_END_D+0)." ".($month_full[($ABS_END_M + 0)][TH])." ".$ABS_END_Y);
	
	$CREATE_DATE = substr($data[CREATE_DATE], 0, 10);
	if($CREATE_DATE!=""&&$CREATE_DATE!="NULL"&&$CREATE_DATE!="null"){
		$temp_date = explode("-", $CREATE_DATE);		// Y-m-d
		$CREATE_DATE =  "�ѹ��� ".convert2thaidigit(($temp_date[2] + 0))." ��͹ ".convert2rtfascii($month_full[($temp_date[1] + 0)][TH])." �.�. ".convert2thaidigit(($temp_date[0] + 543));
	}
	if(!$CREATE_DATE || $CREATE_DATE=="NULL" || $CREATE_DATE=="null") $CREATE_DATE="�ѹ���..... ��͹ .................... �.�. .....";

	$AUDIT_DATE = substr($data[AUDIT_DATE], 0, 10);
        
	if($AUDIT_DATE!=""&&$AUDIT_DATE!="NULL"&&$AUDIT_DATE!="null" && is_numeric(strrpos($AUDIT_DATE, "-"))){
		$temp_date = explode("-", $AUDIT_DATE);		// Y-m-d
		$AUDIT_Y= ($temp_date[0] + 543);
		$AUDIT_M= $temp_date[1];
		$AUDIT_D= $temp_date[2];
		$AUDIT_DATE=convert2thaidigit(($AUDIT_D+0)." ".$month_abbr[($AUDIT_M+0)][TH]." ".$AUDIT_Y);
                //$AUDIT_DATE = convert2thaidigit(show_date_format($AUDIT_DATE,$DATE_DISPLAY));
	}
        //die('>>>'.$AUDIT_DATE.'='.is_numeric(strrpos($AUDIT_DATE, "/")).'++'.strrpos($AUDIT_DATE, "/"));	
	if(!$AUDIT_DATE || $AUDIT_DATE=="NULL" || $AUDIT_DATE=="null" ) 
                $AUDIT_DATE="........../........../..........";
	
	 //echo 'REVIEW1_DATE:'.$data[REVIEW1_DATE]."<br>";
         //echo 'REVIEW2_DATE:'.$data[REVIEW2_DATE]."<br>";
	
	if(!empty($data[REVIEW2_DATE]) && $data[REVIEW2_DATE]!="NULL" && $data[REVIEW2_DATE]!="null"  ){
            $REVIEW_DATE = trim(substr($data[REVIEW2_DATE], 0, 10));
            $REVIEW2_DATE = trim(substr($data[REVIEW2_DATE], 0, 10));
            //echo "111";
        }else{ 
            $REVIEW_DATE = trim(substr($data[REVIEW1_DATE], 0, 10));
            $REVIEW1_DATE = trim(substr($data[REVIEW1_DATE], 0, 10));
            //echo "222";
        }
       
        
	if($REVIEW_DATE!="" && $REVIEW_DATE!="NULL"&&$REVIEW_DATE!="null"&& is_numeric(strrpos($REVIEW_DATE, "-"))){
		$temp_date = explode("-", $REVIEW_DATE);		// Y-m-d
		$REVIEW_Y= ($temp_date[0] + 543);
		$REVIEW_M= $temp_date[1];
		$REVIEW_D= $temp_date[2];
		//$REVIEW_DATE=convert2thaidigit(($REVIEW_D+0)."/".($REVIEW_M+0)."/".$REVIEW_Y);
                $REVIEW_DATE=convert2thaidigit(($REVIEW_D+0)." ".$month_abbr[($REVIEW_M+0)][TH]." ".$REVIEW_Y);
                
	}
     
	if(!$REVIEW_DATE || $REVIEW_DATE=="NULL" || $REVIEW_DATE=="null" ) 
                $REVIEW_DATE="........../........../..........";
	
        //die();
	$APPROVE_DATE = substr($data[APPROVE_DATE], 0, 10);
	if($APPROVE_DATE!=""&&$APPROVE_DATE!="NULL"&&$APPROVE_DATE!="null"&& is_numeric(strrpos($APPROVE_DATE, "-"))){
		$temp_date = explode("-", $APPROVE_DATE);		// Y-m-d
		$APPROVE_Y= ($temp_date[0] + 543);
		$APPROVE_M= $temp_date[1];
		$APPROVE_D= $temp_date[2];
		//$APPROVE_DATE=convert2thaidigit(($APPROVE_D+0)."/".($APPROVE_M+0)."/".$APPROVE_Y);
                $APPROVE_DATE=convert2thaidigit(($APPROVE_D+0)." ".$month_abbr[($APPROVE_M+0)][TH]." ".$APPROVE_Y);
                
	}
	if(!$APPROVE_DATE || $APPROVE_DATE=="NULL" || $APPROVE_DATE=="null" ) 
                $APPROVE_DATE="........../........../..........";

	$OABS_STARTDATE="";
	if($data[OABS_STARTDATE]){
		$temp_date = explode("-", $data[OABS_STARTDATE]);		// Y-m-d
		$OABS_Y= ($temp_date[0] + 543);
		$OABS_M= $temp_date[1];
		$OABS_D= $temp_date[2];
		$OABS_STARTDATE=convert2thaidigit(($OABS_D+0)." ".$month_full[($OABS_M+0)][TH]." ".$OABS_Y);
                //$AUDIT_DATE = convert2thaidigit(show_date_format($AUDIT_DATE,$DATE_DISPLAY));
	}
	
	$OABS_ENDDATE="";
	if($data[OABS_ENDDATE]){
		$temp_date = explode("-", $data[OABS_ENDDATE]);		// Y-m-d
		$OABS_Y= ($temp_date[0] + 543);
		$OABS_M= $temp_date[1];
		$OABS_D= $temp_date[2];
		$OABS_ENDDATE=convert2thaidigit(($OABS_D+0)." ".$month_full[($OABS_M+0)][TH]." ".$OABS_Y);
                //$AUDIT_DATE = convert2thaidigit(show_date_format($AUDIT_DATE,$DATE_DISPLAY));
	}
	
	$ORI_ABS_STARTDATE_date="";
	if($data[ORI_ABS_STARTDATE]){
		$temp_date = explode("-", $data[ORI_ABS_STARTDATE]);		// Y-m-d
		$ORI_Y= ($temp_date[0] + 543);
		$ORI_M= $temp_date[1];
		$ORI_D= $temp_date[2];
		$ORI_ABS_STARTDATE_date=convert2thaidigit(($ORI_D+0)." ".$month_full[($ORI_M+0)][TH]." ".$ORI_Y);
	}
	
	$ORI_ABS_ENDDATE_date="";
	if($data[ORI_ABS_ENDDATE]){
		$temp_date = explode("-", $data[ORI_ABS_ENDDATE]);		// Y-m-d
		$ORI_Y= ($temp_date[0] + 543);
		$ORI_M= $temp_date[1];
		$ORI_D= $temp_date[2];
		$ORI_ABS_ENDDATE_date=convert2thaidigit(($ORI_D+0)." ".$month_full[($ORI_M+0)][TH]." ".$ORI_Y);
	}
	
	
	if($data[ORI_ABS_STARTDATE]){
		$ORI_STARTPERIOD=$data[ORI_ABS_STARTPERIOD];
		$ORI_ENDPERIOD=$data[ORI_ABS_ENDPERIOD];
		$ORI_ABS_STARTDATE=$data[ORI_ABS_STARTDATE];
		$ORI_ABS_ENDDATE=$data[ORI_ABS_ENDDATE];
		
		$ABS_STARTDATE_Show = $ORI_ABS_STARTDATE_date;
		$ABS_ENDDATE_Show = $ORI_ABS_ENDDATE_date;
		
	}else{
		$ORI_STARTPERIOD=$data[ABS_STARTPERIOD];
		$ORI_ENDPERIOD=$data[ABS_ENDPERIOD];
		$ORI_ABS_STARTDATE=$data[ABS_STARTDATE];
		$ORI_ABS_ENDDATE=$data[ABS_ENDDATE];
		$ABS_STARTDATE_Show = $ABS_STARTDATE;
		$ABS_ENDDATE_Show = $ABS_ENDDATE;
	}
	
	$ORI_STARTPERIOD_show="";
	$ORI_ENDPERIOD_show="";
	$ORI_STARTPERIOD_LastShow="";
	
	if($ORI_ABS_STARTDATE != $ORI_ABS_ENDDATE){
		if($ORI_STARTPERIOD==1){
			$ORI_STARTPERIOD_show=" (�����ѹ���)";
		}elseif($ORI_STARTPERIOD==2){
			$ORI_STARTPERIOD_show=" (�����ѹ����)";
		}
		
		if($ORI_ENDPERIOD==1){
			$ORI_ENDPERIOD_show=" (�����ѹ���)";
		}elseif($ORI_ENDPERIOD==2){
			$ORI_ENDPERIOD_show=" (�����ѹ����)";
		}
		
	}else{
		if($ORI_STARTPERIOD==1){
			$ORI_STARTPERIOD_LastShow=" (�����ѹ���)";
		}elseif($ORI_STARTPERIOD==2){
			$ORI_STARTPERIOD_LastShow=" (�����ѹ����)";
		}
	}	
        
        
        if($data[ORI_ABS_STARTDATE]){
            if(empty($data[ORI_ABS_DAY])){
                $ABS_DAY = trim($data[ABS_DAY]);
                if( $data[ABS_STARTDATE]==$data[ORI_ABS_STARTDATE] && $data[ABS_STARTPERIOD]==$data[ORI_ABS_STARTPERIOD] &&
                    $data[ABS_ENDDATE]==$data[ORI_ABS_ENDDATE] && $data[ABS_ENDPERIOD]==$data[ORI_ABS_ENDPERIOD]){
                    $ABS_DAY = trim($data[ABS_DAY]);
                }
            }else{
                $ABS_DAY = trim($data[ORI_ABS_DAY]);
            }
	}else{
            $ABS_DAY = trim($data[ABS_DAY]);
	}
        
        $REVIEW2_NOTE = trim($data[REVIEW2_NOTE]);
        $APPROVE_NOTE = trim($data[APPROVE_NOTE]);
        
        $POS_STATUS = trim($data[POS_STATUS]);
        $POS_APPROVE = trim($data[POS_APPROVE]);
        
	$ABS_REASON = trim($data[ABS_REASON]);
	$ABS_ADDRESS = trim($data[ABS_ADDRESS]);

	$TMP_REVIEW1_FLAG = trim($data[REVIEW1_FLAG]);
	$TMP_REVIEW1_PER_ID = trim($data[REVIEW1_PER_ID]);
	
	$TMP_REVIEW2_FLAG = trim($data[REVIEW2_FLAG]);
	$TMP_REVIEW2_PER_ID = trim($data[REVIEW2_PER_ID]);
	
	$TMP_REVIEW_PER_ID = "";
        $is_chkbox='';
	if($TMP_REVIEW2_PER_ID){	// ����դ�����˹�� �����Ҥ���� ����������Ҥ���鹵�
		$TMP_REVIEW_PER_ID = $TMP_REVIEW2_PER_ID;
                $is_chkbox=2;
	}else{ 
		if($TMP_REVIEW1_PER_ID){
			$TMP_REVIEW_PER_ID = $TMP_REVIEW1_PER_ID;
                        $is_chkbox=1;
		}
	}
        
	$TMP_MAIN_PER_NAME=$TMP_APPROVE_PER_NAME=$TMP_REVIEW_PER_NAME=$TMP_AUDIT_PER_NAME=str_repeat(".", 45);
	$TMP_REVIEW_POSITION_NAME=$TMP_APPROVE_POSITION_NAME=$TMP_AUDIT_POSITION_NAME=str_repeat(".", 45);
	
	$TMP_REVIEW_FLAG_RESULT = 0;
	if($TMP_REVIEW2_FLAG && $is_chkbox==2){ //$is_chkbox=2
            $TMP_REVIEW_FLAG_RESULT = $TMP_REVIEW2_FLAG;
	}else{
            if($TMP_REVIEW1_FLAG && $is_chkbox==1){
                    $TMP_REVIEW_FLAG_RESULT = $TMP_REVIEW1_FLAG;
            }
        }
	
        
	$TMP_AUDIT_FLAG_RESULT = 0;
	$TMP_AUDIT_FLAG = trim($data[AUDIT_FLAG]);
	if($TMP_AUDIT_FLAG)					$TMP_AUDIT_FLAG_RESULT = $TMP_AUDIT_FLAG;
	$TMP_AUDIT_PER_ID = trim($data[AUDIT_PER_ID]);

	$TMP_APPROVE_FLAG_RESULT = 0;
	//echo $data[ORI_ABS_STARTDATE] ."||".$data[OABS_STARTDATE];
	if($data[ORI_ABS_STARTDATE] && $data[OABS_STARTDATE]){
		$TMP_APPROVE_FLAG_RESULT = 1;
	}else{
		$TMP_APPROVE_FLAG = trim($data[APPROVE_FLAG]);
	
		if($TMP_APPROVE_FLAG)				$TMP_APPROVE_FLAG_RESULT = $TMP_APPROVE_FLAG;
	}
	
	$TMP_APPROVE_PER_ID = trim($data[APPROVE_PER_ID]);

	$TMP_CANCEL_FLAG = $data[CANCEL_FLAG];

	$PER_ID = $data[PER_ID];
        if($data[RANK_FLAG]==1){
            $PER_NAME = $data[PER_NAME]." ".$data[PER_SURNAME];            
            $RANK_NAME = $data[PN_NAME];
        }
        else{
            $PER_NAME = $data[PN_NAME].$data[PER_NAME]." ".$data[PER_SURNAME];           
            $RANK_NAME = '';
        }
	$LEVEL_NO = trim($data[LEVEL_NO]);
	$PER_TYPE = $data[PER_TYPE];
	//$PIC_SIGN_PER = str_repeat(".", 45);
	if($SESS_E_SIGN[2]==1){	// ���ٻẺ�ͧ���������礷�͹ԡ��	
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PIC_SIGN_PER = getPIC_SIGN($PER_ID,$PER_CARDNO);
	}
	
	$cmd = " select VC_DAY from PER_VACATION where  VC_YEAR = '$VC_YEAR' and PER_ID = $PER_ID ";
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	if ($data2[VC_DAY]) {
		$TOT_DAY = $data2[VC_DAY];
		$VC_DAY = ($TOT_DAY - $MAX_ABSENT_04);
	}

	/***
	$cmd = " select b.PN_CODE, b.PER_NAME, b.PER_SURNAME from PER_PERSONAL b 
				where  b.PER_ID = $PER_ID and b.PER_TYPE=$PER_TYPE ";
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$TMP_PN_CODE = $data2[PN_CODE];
	$TMP_PER_NAME = $data2[PER_NAME];
	$TMP_PER_SURNAME = $data2[PER_SURNAME];
	
	$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$TMP_PN_CODE' ";
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$TMP_PN_NAME = $data2[PN_NAME];
	
	$PER_NAME = $TMP_PN_NAME.$TMP_PER_NAME." ".$TMP_PER_SURNAME;
	***/

	$cmd="select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
	$db_dpis2->send_cmd($cmd);
	$data3=$db_dpis2->get_array();
	if($data3[LEVEL_NAME]){
		$tmp_data = explode(" ",$data3[LEVEL_NAME]);
		$POSITION_TYPE =  trim($tmp_data[0]);
		$LEVEL_NAME = trim($tmp_data[1]);
		if(!$LEVEL_NAME)		$LEVEL_NAME = trim($data3[LEVEL_NAME]);
	}
	$POSITION_LEVEL = $data3[POSITION_LEVEL];
	if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;

	if($PER_TYPE==1){
		if($SESS_ORG_STRUCTURE!=1) {
			$POS_ID = $data[POS_ID];
		}else{
			$POS_ID = $ORG_ID_ASS;
		}
		$cmd = "	select		a.POS_NO_NAME, a.POS_NO, b.PL_NAME, a.ORG_ID, c.ORG_NAME, c.ORG_ID_REF, a.PT_CODE
					from		PER_POSITION a, PER_LINE b, PER_ORG c
					where		a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
		//die($cmd);
                if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POS_NO_NAME = trim($data2[POS_NO_NAME]);
		if (substr($POS_NO_NAME,0,4)=="���.")
			$POS_NO = $POS_NO_NAME." ".trim($data2[POS_NO]);
		else
			$POS_NO = $POS_NO_NAME.trim($data2[POS_NO]);
		$PL_NAME = trim($data2[PL_NAME]);
		$ORG_ID = $data2[ORG_ID];
		$ORG_NAME = trim($data2[ORG_NAME]);
		$DEPARTMENT_ID = $data2[ORG_ID_REF];
		$PT_CODE = trim($data2[PT_CODE]);
		$PT_NAME = trim($data2[PT_NAME]);
		$POSITION_NAME = trim($PL_NAME)?($PL_NAME):"";	//trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "�����" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"";
	}elseif($PER_TYPE==2){
		if($SESS_ORG_STRUCTURE!=1) {
			$POEM_ID = $data[POEM_ID];
		}else{
			$POEM_ID = $ORG_ID_ASS;
		}
		
		$cmd = "	select		a.POEM_NO_NAME, a.POEM_NO, b.PN_NAME, a.ORG_ID, c.ORG_NAME, c.ORG_ID_REF
							from		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
							where		a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POS_NO = trim($data2[POEM_NO_NAME]).trim($data2[POEM_NO]);
		$PL_NAME = trim($data2[PN_NAME]);
		$ORG_ID = $data2[ORG_ID];
		$ORG_NAME = trim($data2[ORG_NAME]);
		$DEPARTMENT_ID = $data2[ORG_ID_REF];
		$POSITION_NAME =	trim($PL_NAME)?($PL_NAME):"";		// trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL):"";
	}elseif($PER_TYPE==3){
		if($SESS_ORG_STRUCTURE!=1) {
			$POEMS_ID = $data[POEMS_ID];
		}else{
			$POEMS_ID = $ORG_ID_ASS;
		}
		
		$cmd = "	select		a.POEMS_NO_NAME, a.POEMS_NO, b.EP_NAME, a.ORG_ID, c.ORG_NAME, c.ORG_ID_REF
							from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
							where		a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POS_NO = trim($data2[POEMS_NO_NAME]).trim($data2[POEMS_NO]);
		$PL_NAME = trim($data2[EP_NAME]);
		$ORG_ID = $data2[ORG_ID];
		$ORG_NAME = trim($data2[ORG_NAME]);
		$DEPARTMENT_ID = $data2[ORG_ID_REF];
		$POSITION_NAME =	trim($PL_NAME)?($PL_NAME):"";		// trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL):"";
	} elseif($PER_TYPE==4){
		if($SESS_ORG_STRUCTURE!=1) {
			$POT_ID = $data[POT_ID];
		}else{
			$POT_ID = $ORG_ID_ASS;
		}
		
		$cmd = "	select		a.POT_NO_NAME, a.POT_NO, b.TP_NAME, a.ORG_ID, c.ORG_NAME, c.ORG_ID_REF
							from		PER_POS_TEMP a, PER_TEMP_POS_NAME  b, PER_ORG c
							where		a.POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POS_NO = trim($data2[POT_NO_NAME]).trim($data2[POT_NO]);
		$PL_NAME = trim($data2[TP_NAME]);
		$ORG_ID = $data2[ORG_ID];
		$ORG_NAME = trim($data2[ORG_NAME]);
		$DEPARTMENT_ID = $data2[ORG_ID_REF];
		$POSITION_NAME = trim($PL_NAME)?($PL_NAME):"";		// trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL):"";
	} // end if
	
	$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$DEPARTMENT_NAME = $data2[ORG_NAME];
	$MINISTRY_ID = $data2[ORG_ID_REF];
        
        
	
	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$MINISTRY_NAME = $data2[ORG_NAME];
        
        
        /*http://dpis.ocsc.go.th/Service/node/1109*/
        //㹪�ͧ ��¹��� �ͧ������ �Ѩ�غѹ�к����ʴ����͡�� �����������ʴ��繪����ӹѡ/�ͧ
	//���������
    $cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$DivisionNameOrg = $data2[ORG_NAME];
	
	//����ͺ����
	$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG_ASS where ORG_ID=$ORG_ID_ASS ";
	
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	
	$DivisionNameAss = $data2[ORG_NAME];
	
	
	// �͹�����ҵ�������¡�͹
	if(!empty($DivisionNameAss)){
		//echo  "xxx";
		if($DivisionNameAss <> $DivisionNameOrg){
			$DivisionNameAssShow = " (��Ժѵ�˹�ҷ�� ".$DivisionNameAss.")";
		}else{
			$DivisionNameAssShow = "";
		}
		$DivisionName = $DivisionNameAss;
	}else{
		//echo  "yyyy";
		$DivisionNameAssShow = "";
		$DivisionName = $DivisionNameOrg;
	}
	
	
        /**/
		
//---------------------------------------------------------------
if($PER_ID){

	$TMP_PERSON_LEVEL_NO = trim($data[LEVEL_NO]);
	$cmd="select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$TMP_PERSON_LEVEL_NO'";
	$db_dpis2->send_cmd($cmd);
	$data3=$db_dpis2->get_array();
	$TMP_PERSON_LEVEL_NAME = trim($data3[LEVEL_NAME]);
	$TMP_PERSON_POSITION_LEVEL = $data3[POSITION_LEVEL];
	if (!$TMP_PERSON_POSITION_LEVEL) $TMP_PERSON_POSITION_LEVEL = $TMP_PERSON_LEVEL_NAME;
	
		if($PER_TYPE ==1){  // ����Ҫ���
			$TMP_PERSON_POS_ID = $data[POS_ID];
			$cmd = "	select		a.POS_NO_NAME, a.POS_NO, b.PL_NAME, 
					a.ORG_ID_2, a.ORG_ID_1, a.ORG_ID, 
            		CASE WHEN c.ORG_SHORT IS NULL THEN c.ORG_NAME ELSE c.ORG_SHORT END AS ORG_NAME,
					c.ORG_ID_REF, a.PT_CODE,d.PT_NAME,a.PM_CODE
					from		PER_POSITION a
					LEFT JOIN PER_LINE b on(a.PL_CODE=b.PL_CODE)
					LEFT JOIN  PER_ORG c on(a.ORG_ID=c.ORG_ID)
					LEFT JOIN  PER_TYPE d on(a.PT_CODE=d.PT_CODE)
					where		a.POS_ID=$TMP_PERSON_POS_ID  ";

			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_PERSON_PL_NAME = trim($data2[PL_NAME]);
			$TMP_PERSON_PT_CODE = trim($data2[PT_CODE]);
			$TMP_PERSON_PT_NAME = trim($data2[PT_NAME]);

			// ���˹觡�ú�����
			$TMP_PERSON_PM_CODE = trim($data2[PM_CODE]);
			$TMP_PERSON_ORG_NAME= trim($data2[ORG_NAME]);
			$TMP_PERSON_ORG_ID_1 = trim($data2[ORG_ID_1]);
			$TMP_PERSON_ORG_ID_2 = trim($data2[ORG_ID_2]);
			//die($cmd);
			if ($TMP_PERSON_ORG_ID_1) {
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_PERSON_ORG_ID_1 ";
                               
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_PERSON_ORG_NAME_1 = trim($data2[ORG_NAME]);
				if ($data2[ORG_NAME]=="�ç���¹") {
					if ($TMP_PERSON_ORG_ID_2) {
						$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_PERSON_ORG_ID_2 ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
					}
				} 
			}
			
			$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$TMP_PERSON_PM_CODE'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_PERSON_PM_NAME = trim($data2[PM_NAME]);
			if ($SESS_DEPARTMENT_NAME=="�����û���ͧ" && !$TMP_PERSON_PM_NAME) $TMP_PERSON_PM_NAME = $TMP_PERSON_PL_NAME;
			if ($TMP_PERSON_PM_NAME=="�������Ҫ��èѧ��Ѵ" || $TMP_PERSON_PM_NAME=="�ͧ�������Ҫ��èѧ��Ѵ" || $TMP_PERSON_PM_NAME=="��Ѵ�ѧ��Ѵ" || 
				$TMP_PERSON_PM_NAME=="����ӹ�¡��" || $TMP_PERSON_PM_NAME=="����ӹ�¡�áͧ" || $TMP_PERSON_PM_NAME=="����ӹ�¡���ٹ��" || 
				$TMP_PERSON_PM_NAME=="����ӹ�¡���ӹѡ" || $TMP_PERSON_PM_NAME=="����ӹ�¡��ʶҺѹ" || $TMP_PERSON_PM_NAME=="���˹���ӹѡ�ҹ�ѧ��Ѵ") {
				$TMP_PERSON_PM_NAME .= $TMP_PERSON_ORG_NAME;
				$TMP_PERSON_PM_NAME = str_replace("�ѧ��Ѵ�ѧ��Ѵ", "�ѧ��Ѵ", $TMP_PERSON_PM_NAME); 
				$TMP_PERSON_PM_NAME = str_replace("�ͧ�ͧ", "�ͧ", $TMP_PERSON_PM_NAME); 
				$TMP_PERSON_PM_NAME = str_replace("�ٹ���ٹ��", "�ٹ��", $TMP_PERSON_PM_NAME); 
				$TMP_PERSON_PM_NAME = str_replace("�ӹѡ�ӹѡ", "�ӹѡ", $TMP_PERSON_PM_NAME); 
				$TMP_PERSON_PM_NAME = str_replace("ʶҺѹʶҺѹ", "ʶҺѹ", $TMP_PERSON_PM_NAME); 
				$TMP_PERSON_PM_NAME = str_replace("�ӹѡ�ҹ�ѧ��Ѵ�ӹѡ�ҹ�ѧ��Ѵ", "�ӹѡ�ҹ�ѧ��Ѵ", $TMP_PERSON_PM_NAME); 
			} elseif ($TMP_PERSON_PM_NAME=="��������") {
				$TMP_PERSON_PM_NAME .= $TMP_PERSON_ORG_NAME_1;
				$TMP_PERSON_PM_NAME = str_replace("����ͷ��ӡ�û���ͧ�����", "�����", $TMP_PERSON_PM_NAME); 
			}
			//$POS_STATUS
			if($TMP_PERSON_PM_NAME){
				$TMP_PERSON_POSITION_NAME = trim($TMP_PERSON_PM_NAME);
			}else{
				$TMP_PERSON_POSITION_NAME = trim($TMP_PERSON_PL_NAME)?($TMP_PERSON_PL_NAME . $TMP_PERSON_POSITION_LEVEL . (($TMP_PERSON_PT_NAME != "�����" && $TMP_PERSON_LEVEL_NO >= 6)?"$TMP_PERSON_PT_NAME":"")):"";	
			}
		
		}elseif($PER_TYPE ==2){  // �١��ҧ��Ш�
			$TMP_PERSON_POS_ID = $data[POEM_ID];
			$cmd = "	select		b.PN_NAME, a.ORG_ID, c.ORG_NAME, 
					c.ORG_ID_REF, a.PN_CODE
					from		PER_POS_EMP a
					LEFT JOIN PER_POS_NAME b on(a.PN_CODE=b.PN_CODE)
					LEFT JOIN  PER_ORG c on(a.ORG_ID=c.ORG_ID)
					where		a.POEM_ID=$TMP_PERSON_POS_ID  ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_PERSON_PL_NAME = trim($data2[PN_NAME]);
			$TMP_PERSON_PT_CODE = trim($data2[PN_CODE]);
			$TMP_PERSON_POSITION_NAME = trim($TMP_PERSON_PL_NAME)?($TMP_PERSON_PL_NAME . $TMP_PERSON_POSITION_LEVEL):"";	
			
		}elseif($PER_TYPE ==3){ // ��ѡ�ҹ�Ҫ���
			$TMP_PERSON_POS_ID = $data[POEMS_ID];
			$cmd = "	select		b.EP_NAME, a.ORG_ID, c.ORG_NAME, 
					c.ORG_ID_REF, a.EP_CODE
					from		PER_POS_EMPSER a
					LEFT JOIN PER_EMPSER_POS_NAME b on(a.EP_CODE=b.EP_CODE)
					LEFT JOIN  PER_ORG c on(a.ORG_ID=c.ORG_ID)
					where		a.POEMS_ID=$TMP_PERSON_POS_ID  ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_PERSON_PL_NAME = trim($data2[EP_NAME]);
			$TMP_PERSON_PT_CODE = trim($data2[EP_CODE]);
			$TMP_PERSON_POSITION_NAME = trim($TMP_PERSON_PL_NAME)?($TMP_PERSON_PL_NAME):"";	
			
		}elseif($PER_TYPE ==4){ // �١��ҧ���Ǥ���
			$TMP_PERSON_POS_ID = $data[POT_ID];
			$cmd = "	select		b.TP_NAME, a.ORG_ID, c.ORG_NAME, 
					c.ORG_ID_REF, a.TP_CODE
					from		PER_POS_TEMP a
					LEFT JOIN PER_TEMP_POS_NAME b on(a.TP_CODE=b.TP_CODE)
					LEFT JOIN  PER_ORG c on(a.ORG_ID=c.ORG_ID)
					where		a.POT_ID=$TMP_PERSON_POS_ID  ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_PERSON_PL_NAME = trim($data2[TP_NAME]);
			$TMP_PERSON_PT_CODE = trim($data2[TP_CODE]);
			$TMP_PERSON_POSITION_NAME = trim($TMP_PERSON_PL_NAME)?($TMP_PERSON_PL_NAME):"";	
			
		}
}		

//------------------------------------------------------------		

//$PIC_SIGN_REVIEW = str_repeat(".", 45);
if($TMP_REVIEW_PER_ID){
	$cmd_review = " select b.RANK_FLAG, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.POS_ID,a.POEM_ID, a.POEMS_ID,
			a.LEVEL_NO, a.PER_CARDNO,a.PER_TYPE 
			from PER_PERSONAL a, PER_PRENAME b 
			where a.PER_ID=$TMP_REVIEW_PER_ID and a.PN_CODE=b.PN_CODE ";
	$db_dpis2->send_cmd($cmd_review);
	$data2 = $db_dpis2->get_array();
        if($data2[RANK_FLAG]==1){
            $TMP_REVIEW_PER_NAME = $data2[PER_NAME]." ".$data2[PER_SURNAME];
            $REVIEW_RANK_NAME = $data2[PN_NAME];
        }
        else{
            $TMP_REVIEW_PER_NAME = $data2[PN_NAME].$data2[PER_NAME]." ".$data2[PER_SURNAME];
            $REVIEW_RANK_NAME = '';
        }
	$TMP_REVIEW_PER_TYPE = $data2[PER_TYPE];
	if($TMP_REVIEW_PER_TYPE==1) 
		$TMP_REVIEW_POS_ID = $data2[POS_ID];
	elseif($TMP_REVIEW_PER_TYPE==2) 
		$TMP_REVIEW_POS_ID = $data2[POEM_ID];
	elseif ($TMP_REVIEW_PER_TYPE == 3) 
		$TMP_REVIEW_POS_ID = $data2[POEMS_ID];
	elseif ($TMP_REVIEW_PER_TYPE == 4) 
		$TMP_REVIEW_POS_ID = $data2[POT_ID];
		
	$TMP_REVIEW_LEVEL_NO = trim($data2[LEVEL_NO]);
	if($SESS_E_SIGN[2]==1){	// ���ٻẺ�ͧ���������礷�͹ԡ��
		$TMP_REVIEW_CARDNO = trim($data2[PER_CARDNO]);
		$PIC_SIGN_REVIEW = getPIC_SIGN($TMP_REVIEW_PER_ID,$TMP_REVIEW_CARDNO);
	}
	$cmd="select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$TMP_REVIEW_LEVEL_NO'";
	$db_dpis2->send_cmd($cmd);
	$data3=$db_dpis2->get_array();
	$TMP_REVIEW_LEVEL_NAME = trim($data3[LEVEL_NAME]);
	$TMP_REVIEW_POSITION_LEVEL = $data3[POSITION_LEVEL];
	if (!$TMP_REVIEW_POSITION_LEVEL) $TMP_REVIEW_POSITION_LEVEL = $TMP_REVIEW_LEVEL_NAME;
	
		if($TMP_REVIEW_PER_TYPE ==1){  // ����Ҫ���
			$cmd = "	select		a.POS_NO_NAME, a.POS_NO, b.PL_NAME, 
					a.ORG_ID_2, a.ORG_ID_1, a.ORG_ID, 
            		CASE WHEN c.ORG_SHORT IS NULL THEN c.ORG_NAME ELSE c.ORG_SHORT END AS ORG_NAME,
					c.ORG_ID_REF, a.PT_CODE,d.PT_NAME,a.PM_CODE
					from		PER_POSITION a
					LEFT JOIN PER_LINE b on(a.PL_CODE=b.PL_CODE)
					LEFT JOIN  PER_ORG c on(a.ORG_ID=c.ORG_ID)
					LEFT JOIN  PER_TYPE d on(a.PT_CODE=d.PT_CODE)
					where		a.POS_ID=$TMP_REVIEW_POS_ID  ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_REVIEW_PL_NAME = trim($data2[PL_NAME]);
			$TMP_REVIEW_PT_CODE = trim($data2[PT_CODE]);
			$TMP_REVIEW_PT_NAME = trim($data2[PT_NAME]);
			
			// ���˹觡�ú�����
			$TMP_REVIEW_PM_CODE = trim($data2[PM_CODE]);
			$TMP_REVIEW_ORG_NAME= trim($data2[ORG_NAME]);
			$TMP_REVIEW_ORG_ID_1 = trim($data2[ORG_ID_1]);
			$TMP_REVIEW_ORG_ID_2 = trim($data2[ORG_ID_2]);
			
			if ($TMP_REVIEW_ORG_ID_1) {
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_REVIEW_ORG_ID_1 ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_REVIEW_ORG_NAME_1 = trim($data2[ORG_NAME]);
				if ($data2[ORG_NAME]=="�ç���¹") {
					if ($TMP_REVIEW_ORG_ID_2) {
						$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_REVIEW_ORG_ID_2 ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
					}
				} 
			}
			
			$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$TMP_REVIEW_PM_CODE'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_REVIEW_PM_NAME = trim($data2[PM_NAME]);
			if ($SESS_DEPARTMENT_NAME=="�����û���ͧ" && !$TMP_REVIEW_PM_NAME) $TMP_REVIEW_PM_NAME = $TMP_REVIEW_PL_NAME;
			if ($TMP_REVIEW_PM_NAME=="�������Ҫ��èѧ��Ѵ" || $TMP_REVIEW_PM_NAME=="�ͧ�������Ҫ��èѧ��Ѵ" || $TMP_REVIEW_PM_NAME=="��Ѵ�ѧ��Ѵ" || 
				$TMP_REVIEW_PM_NAME=="����ӹ�¡��" || $TMP_REVIEW_PM_NAME=="����ӹ�¡�áͧ" || $TMP_REVIEW_PM_NAME=="����ӹ�¡���ٹ��" || 
				$TMP_REVIEW_PM_NAME=="����ӹ�¡���ӹѡ" || $TMP_REVIEW_PM_NAME=="����ӹ�¡��ʶҺѹ" || $TMP_REVIEW_PM_NAME=="���˹���ӹѡ�ҹ�ѧ��Ѵ") {
				$TMP_REVIEW_PM_NAME .= $TMP_REVIEW_ORG_NAME;
				$TMP_REVIEW_PM_NAME = str_replace("�ѧ��Ѵ�ѧ��Ѵ", "�ѧ��Ѵ", $TMP_REVIEW_PM_NAME); 
				$TMP_REVIEW_PM_NAME = str_replace("�ͧ�ͧ", "�ͧ", $TMP_REVIEW_PM_NAME); 
				$TMP_REVIEW_PM_NAME = str_replace("�ٹ���ٹ��", "�ٹ��", $TMP_REVIEW_PM_NAME); 
				$TMP_REVIEW_PM_NAME = str_replace("�ӹѡ�ӹѡ", "�ӹѡ", $TMP_REVIEW_PM_NAME); 
				$TMP_REVIEW_PM_NAME = str_replace("ʶҺѹʶҺѹ", "ʶҺѹ", $TMP_REVIEW_PM_NAME); 
				$TMP_REVIEW_PM_NAME = str_replace("�ӹѡ�ҹ�ѧ��Ѵ�ӹѡ�ҹ�ѧ��Ѵ", "�ӹѡ�ҹ�ѧ��Ѵ", $TMP_REVIEW_PM_NAME); 
			} elseif ($TMP_REVIEW_PM_NAME=="��������") {
				$TMP_REVIEW_PM_NAME .= $TMP_REVIEW_ORG_NAME_1;
				$TMP_REVIEW_PM_NAME = str_replace("����ͷ��ӡ�û���ͧ�����", "�����", $TMP_REVIEW_PM_NAME); 
			}
			//$POS_STATUS
			if($TMP_REVIEW_PM_NAME){
				$TMP_REVIEW_POSITION_NAME = trim($TMP_REVIEW_PM_NAME);
			}else{
				$TMP_REVIEW_POSITION_NAME = trim($TMP_REVIEW_PL_NAME)?($TMP_REVIEW_PL_NAME . $TMP_REVIEW_POSITION_LEVEL . (($TMP_REVIEW_PT_NAME != "�����" && $TMP_REVIEW_LEVEL_NO >= 6)?"$TMP_REVIEW_PT_NAME":"")):"";
			}
			
				
		
		}elseif($TMP_REVIEW_PER_TYPE ==2){  // �١��ҧ��Ш�
			$cmd = "	select		b.PN_NAME, a.ORG_ID, c.ORG_NAME, 
					c.ORG_ID_REF, a.PN_CODE
					from		PER_POS_EMP a
					LEFT JOIN PER_POS_NAME b on(a.PN_CODE=b.PN_CODE)
					LEFT JOIN  PER_ORG c on(a.ORG_ID=c.ORG_ID)
					where		a.POEM_ID=$TMP_REVIEW_POS_ID  ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_REVIEW_PL_NAME = trim($data2[PN_NAME]);
			$TMP_REVIEW_PT_CODE = trim($data2[PN_CODE]);
			$TMP_REVIEW_POSITION_NAME = trim($TMP_REVIEW_PL_NAME)?($TMP_REVIEW_PL_NAME . $TMP_REVIEW_POSITION_LEVEL):"";	
			
		}elseif($TMP_REVIEW_PER_TYPE ==3){ // ��ѡ�ҹ�Ҫ���
			$cmd = "	select		b.EP_NAME, a.ORG_ID, c.ORG_NAME, 
					c.ORG_ID_REF, a.EP_CODE
					from		PER_POS_EMPSER a
					LEFT JOIN PER_EMPSER_POS_NAME b on(a.EP_CODE=b.EP_CODE)
					LEFT JOIN  PER_ORG c on(a.ORG_ID=c.ORG_ID)
					where		a.POEMS_ID=$TMP_REVIEW_POS_ID  ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_REVIEW_PL_NAME = trim($data2[EP_NAME]);
			$TMP_REVIEW_PT_CODE = trim($data2[EP_CODE]);
			$TMP_REVIEW_POSITION_NAME = trim($TMP_REVIEW_PL_NAME)?($TMP_REVIEW_PL_NAME):"";	
			
		}elseif($TMP_REVIEW_PER_TYPE ==4){ // �١��ҧ���Ǥ���
			$cmd = "	select		b.TP_NAME, a.ORG_ID, c.ORG_NAME, 
					c.ORG_ID_REF, a.TP_CODE
					from		PER_POS_TEMP a
					LEFT JOIN PER_TEMP_POS_NAME b on(a.TP_CODE=b.TP_CODE)
					LEFT JOIN  PER_ORG c on(a.ORG_ID=c.ORG_ID)
					where		a.POT_ID=$TMP_REVIEW_POS_ID  ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_REVIEW_PL_NAME = trim($data2[TP_NAME]);
			$TMP_REVIEW_PT_CODE = trim($data2[TP_CODE]);
			$TMP_REVIEW_POSITION_NAME = trim($TMP_REVIEW_PL_NAME)?($TMP_REVIEW_PL_NAME):"";	
			
		}
}
//--------------------

	//$PIC_SIGN_AUDIT = str_repeat(".", 45);
	if($TMP_AUDIT_PER_ID){	
		$cmd = " select b.RANK_FLAG, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.POS_ID,a.POEM_ID, a.POEMS_ID,
				a.LEVEL_NO, a.PER_CARDNO,a.PER_TYPE from PER_PERSONAL a, PER_PRENAME b 
				where a.PER_ID=$TMP_AUDIT_PER_ID and a.PN_CODE=b.PN_CODE ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
                if($data2[RANK_FLAG]==1){
                    $TMP_AUDIT_PER_NAME = $data2[PER_NAME]." ".$data2[PER_SURNAME];
                    $AUDIT_RANK_NAME = $data2[PN_NAME];
                }
                else{
                    $TMP_AUDIT_PER_NAME = $data2[PN_NAME].$data2[PER_NAME]." ".$data2[PER_SURNAME];
                    $AUDIT_RANK_NAME = '';
                }
		$TMP_AUDIT_PER_TYPE = $data2[PER_TYPE];
		if($TMP_AUDIT_PER_TYPE==1) 
			$TMP_AUDIT_POS_ID = $data2[POS_ID];
		elseif($TMP_AUDIT_PER_TYPE==2) 
			$TMP_AUDIT_POS_ID = $data2[POEM_ID];
		elseif ($TMP_AUDIT_PER_TYPE == 3) 
			$TMP_AUDIT_POS_ID = $data2[POEMS_ID];
		elseif ($TMP_AUDIT_PER_TYPE == 4) 
			$TMP_AUDIT_POS_ID = $data2[POT_ID];
		
		
		$TMP_AUDIT_LEVEL_NO = trim($data2[LEVEL_NO]);
		if($SESS_E_SIGN[2]==1){	// ���ٻẺ�ͧ���������礷�͹ԡ��
			$TMP_AUDIT_CARDNO = trim($data2[PER_CARDNO]);
			$PIC_SIGN_AUDIT = getPIC_SIGN($TMP_AUDIT_PER_ID,$TMP_AUDIT_CARDNO);
		}
		
		$cmd="select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$TMP_AUDIT_LEVEL_NO'";
		$db_dpis2->send_cmd($cmd);
		$data3=$db_dpis2->get_array();
		$TMP_AUDIT_NAME = trim($data3[LEVEL_NAME]);
		$TMP_AUDIT_POSITION_LEVEL = $data3[POSITION_LEVEL];
		if (!$TMP_AUDIT_POSITION_LEVEL) $TMP_AUDIT_POSITION_LEVEL = $TMP_AUDIT_LEVEL_NAME;
		
		if($TMP_AUDIT_PER_TYPE ==1){  // ����Ҫ���
			$cmd = "	select		a.POS_NO_NAME, a.POS_NO, b.PL_NAME, 
					a.ORG_ID_2, a.ORG_ID_1, a.ORG_ID, 
            		CASE WHEN c.ORG_SHORT IS NULL THEN c.ORG_NAME ELSE c.ORG_SHORT END AS ORG_NAME,
					c.ORG_ID_REF, a.PT_CODE,d.PT_NAME,a.PM_CODE
					from		PER_POSITION a
					LEFT JOIN PER_LINE b on(a.PL_CODE=b.PL_CODE)
					LEFT JOIN  PER_ORG c on(a.ORG_ID=c.ORG_ID)
					LEFT JOIN  PER_TYPE d on(a.PT_CODE=d.PT_CODE)
					where		a.POS_ID=$TMP_AUDIT_POS_ID  ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_AUDIT_PL_NAME = trim($data2[PL_NAME]);
			$TMP_AUDIT_PT_CODE = trim($data2[PT_CODE]);
			$TMP_AUDIT_PT_NAME = trim($data2[PT_NAME]);
			
			// ���˹觡�ú�����
			$TMP_AUDIT_PM_CODE = trim($data2[PM_CODE]);
			$TMP_AUDIT_ORG_NAME= trim($data2[ORG_NAME]);
			$TMP_AUDIT_ORG_ID_1 = trim($data2[ORG_ID_1]);
			$TMP_AUDIT_ORG_ID_2 = trim($data2[ORG_ID_2]);
			
			if ($TMP_AUDIT_ORG_ID_1) {
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_AUDIT_ORG_ID_1 ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_AUDIT_ORG_NAME_1 = trim($data2[ORG_NAME]);
				if ($data2[ORG_NAME]=="�ç���¹") {
					if ($TMP_AUDIT_ORG_ID_2) {
						$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_AUDIT_ORG_ID_2 ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
					}
				} 
			}
			
			$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$TMP_AUDIT_PM_CODE'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_AUDIT_PM_NAME = trim($data2[PM_NAME]);
			if ($SESS_DEPARTMENT_NAME=="�����û���ͧ" && !$TMP_AUDIT_PM_NAME) $TMP_AUDIT_PM_NAME = $TMP_AUDIT_PL_NAME;
			if ($TMP_AUDIT_PM_NAME=="�������Ҫ��èѧ��Ѵ" || $TMP_AUDIT_PM_NAME=="�ͧ�������Ҫ��èѧ��Ѵ" || $TMP_AUDIT_PM_NAME=="��Ѵ�ѧ��Ѵ" || 
				$TMP_AUDIT_PM_NAME=="����ӹ�¡��" || $TMP_AUDIT_PM_NAME=="����ӹ�¡�áͧ" || $TMP_AUDIT_PM_NAME=="����ӹ�¡���ٹ��" || 
				$TMP_AUDIT_PM_NAME=="����ӹ�¡���ӹѡ" || $TMP_AUDIT_PM_NAME=="����ӹ�¡��ʶҺѹ" || $TMP_AUDIT_PM_NAME=="���˹���ӹѡ�ҹ�ѧ��Ѵ") {
				$TMP_AUDIT_PM_NAME .= $TMP_AUDIT_ORG_NAME;
				$TMP_AUDIT_PM_NAME = str_replace("�ѧ��Ѵ�ѧ��Ѵ", "�ѧ��Ѵ", $TMP_AUDIT_PM_NAME); 
				$TMP_AUDIT_PM_NAME = str_replace("�ͧ�ͧ", "�ͧ", $TMP_AUDIT_PM_NAME); 
				$TMP_AUDIT_PM_NAME = str_replace("�ٹ���ٹ��", "�ٹ��", $TMP_AUDIT_PM_NAME); 
				$TMP_AUDIT_PM_NAME = str_replace("�ӹѡ�ӹѡ", "�ӹѡ", $TMP_AUDIT_PM_NAME); 
				$TMP_AUDIT_PM_NAME = str_replace("ʶҺѹʶҺѹ", "ʶҺѹ", $TMP_AUDIT_PM_NAME); 
				$TMP_AUDIT_PM_NAME = str_replace("�ӹѡ�ҹ�ѧ��Ѵ�ӹѡ�ҹ�ѧ��Ѵ", "�ӹѡ�ҹ�ѧ��Ѵ", $TMP_AUDIT_PM_NAME); 
			} elseif ($TMP_AUDIT_PM_NAME=="��������") {
				$TMP_AUDIT_PM_NAME .= $TMP_AUDIT_ORG_NAME_1;
				$TMP_AUDIT_PM_NAME = str_replace("����ͷ��ӡ�û���ͧ�����", "�����", $TMP_AUDIT_PM_NAME); 
			}
			//$POS_STATUS
			if($TMP_AUDIT_PM_NAME){
				$TMP_AUDIT_POSITION_NAME = trim($TMP_AUDIT_PM_NAME);
			}else{
				$TMP_AUDIT_POSITION_NAME = trim($TMP_AUDIT_PL_NAME)?($TMP_AUDIT_PL_NAME . $TMP_AUDIT_POSITION_LEVEL . (($TMP_AUDIT_PT_NAME != "�����" && $TMP_AUDIT_LEVEL_NO >= 6)?"$TMP_AUDIT_PT_NAME":"")):"";
			}
				
		
		}elseif($TMP_AUDIT_PER_TYPE ==2){  // �١��ҧ��Ш�
			$cmd = "	select		b.PN_NAME, a.ORG_ID, c.ORG_NAME, 
					c.ORG_ID_REF, a.PN_CODE
					from		PER_POS_EMP a
					LEFT JOIN PER_POS_NAME b on(a.PN_CODE=b.PN_CODE)
					LEFT JOIN  PER_ORG c on(a.ORG_ID=c.ORG_ID)
					where		a.POEM_ID=$TMP_AUDIT_POS_ID  ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_AUDIT_PL_NAME = trim($data2[PN_NAME]);
			$TMP_AUDIT_PT_CODE = trim($data2[PN_CODE]);
			$TMP_AUDIT_POSITION_NAME = trim($TMP_AUDIT_PL_NAME)?($TMP_AUDIT_PL_NAME . $TMP_AUDIT_POSITION_LEVEL):"";	
			
		}elseif($TMP_AUDIT_PER_TYPE ==3){ // ��ѡ�ҹ�Ҫ���
			$cmd = "	select		b.EP_NAME, a.ORG_ID, c.ORG_NAME, 
					c.ORG_ID_REF, a.EP_CODE
					from		PER_POS_EMPSER a
					LEFT JOIN PER_EMPSER_POS_NAME b on(a.EP_CODE=b.EP_CODE)
					LEFT JOIN  PER_ORG c on(a.ORG_ID=c.ORG_ID)
					where		a.POEMS_ID=$TMP_AUDIT_POS_ID  ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_AUDIT_PL_NAME = trim($data2[EP_NAME]);
			$TMP_AUDIT_PT_CODE = trim($data2[EP_CODE]);
			$TMP_AUDIT_POSITION_NAME = trim($TMP_AUDIT_PL_NAME)?($TMP_AUDIT_PL_NAME):"";	
			
		}elseif($TMP_AUDIT_PER_TYPE ==4){ // �١��ҧ���Ǥ���
			$cmd = "	select		b.TP_NAME, a.ORG_ID, c.ORG_NAME, 
					c.ORG_ID_REF, a.TP_CODE
					from		PER_POS_TEMP a
					LEFT JOIN PER_TEMP_POS_NAME b on(a.TP_CODE=b.TP_CODE)
					LEFT JOIN  PER_ORG c on(a.ORG_ID=c.ORG_ID)
					where		a.POT_ID=$TMP_AUDIT_POS_ID  ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_AUDIT_PL_NAME = trim($data2[TP_NAME]);
			$TMP_AUDIT_PT_CODE = trim($data2[TP_CODE]);
			$TMP_AUDIT_POSITION_NAME = trim($TMP_AUDIT_PL_NAME)?($TMP_AUDIT_PL_NAME):"";	
			
		}
		
		
	}
//--------------------

//	$PIC_SIGN_APPROVE = str_repeat(".", 45);
	$cmd = " select b.RANK_FLAG , b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.POS_ID, a.LEVEL_NO, a.PER_CARDNO from PER_PERSONAL a, PER_PRENAME b where a.PER_ID=$TMP_APPROVE_PER_ID and a.PN_CODE=b.PN_CODE ";
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
        if($data2[RANK_FLAG]==1){
            $TMP_APPROVE_PER_NAME = $data2[PER_NAME]." ".$data2[PER_SURNAME];
            $APPROVE_RANK_NAME = $data2[PN_NAME];
        }
        else {
            $TMP_APPROVE_PER_NAME = $data2[PN_NAME].$data2[PER_NAME]." ".$data2[PER_SURNAME];
            $APPROVE_RANK_NAME = '';
        }
	$TMP_APPROVE_POS_ID = $data2[POS_ID];
	$TMP_APPROVE_LEVEL_NO = trim($data2[LEVEL_NO]);
	if($SESS_E_SIGN[2]==1){	// ���ٻẺ�ͧ���������礷�͹ԡ��
		$TMP_APPROVE_CARDNO = trim($data2[PER_CARDNO]);
		$PIC_SIGN_APPROVE = getPIC_SIGN($TMP_APPROVE_PER_ID,$TMP_APPROVE_CARDNO);
	}
	
	$cmd="select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$TMP_APPROVE_LEVEL_NO'";
	$db_dpis2->send_cmd($cmd);
	$data3=$db_dpis2->get_array();
	$TMP_APPROVE_LEVEL_NAME = trim($data3[LEVEL_NAME]);
	$TMP_APPROVE_POSITION_LEVEL = $data3[POSITION_LEVEL];
	if (!$TMP_APPROVE_POSITION_LEVEL) $TMP_APPROVE_POSITION_LEVEL = $TMP_APPROVE_LEVEL_NAME;
	$cmd = "select	a.POS_NO_NAME, a.POS_NO, b.PL_NAME, a.ORG_ID_2, a.ORG_ID_1, a.ORG_ID, 
            CASE WHEN '$ORG_SHORT_NAME' = 'Y' THEN c.ORG_SHORT ELSE c.ORG_NAME END AS ORG_NAME,
            c.ORG_ID_REF, a.PT_CODE, a.PM_CODE 
            from PER_POSITION a, PER_LINE b, PER_ORG c 
						where		a.POS_ID=$TMP_APPROVE_POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	
        $db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$TMP_APPROVE_PL_NAME = trim($data2[PL_NAME]);
	$TMP_APPROVE_PT_CODE = trim($data2[PT_CODE]);
	$TMP_APPROVE_PT_NAME = trim($data2[PT_NAME]);
	$TMP_APPROVE_PM_CODE = trim($data2[PM_CODE]);
	$TMP_APPROVE_ORG_NAME= trim($data2[ORG_NAME]);
	$TMP_APPROVE_ORG_ID_1 = trim($data2[ORG_ID_1]);
	$TMP_APPROVE_ORG_ID_2 = trim($data2[ORG_ID_2]);
	
	if ($TMP_APPROVE_ORG_ID_1) {
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_APPROVE_ORG_ID_1 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$TMP_APPROVE_ORG_NAME_1 = trim($data2[ORG_NAME]);
		if ($data2[ORG_NAME]=="�ç���¹") {
			if ($TMP_APPROVE_ORG_ID_2) {
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_APPROVE_ORG_ID_2 ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
			}
		} 
//		if ($data2[ORG_NAME]!="-") $TMP_APPROVE_ORG_NAME = $TMP_APPROVE_ORG_NAME . "<hr>" . "&nbsp; " . $data2[ORG_NAME];
	}
	
	$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$TMP_APPROVE_PM_CODE'  ";
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$TMP_APPROVE_PM_NAME = trim($data2[PM_NAME]);
	if ($SESS_DEPARTMENT_NAME=="�����û���ͧ" && !$TMP_APPROVE_PM_NAME) $TMP_APPROVE_PM_NAME = $TMP_APPROVE_PL_NAME;
	if ($TMP_APPROVE_PM_NAME=="�������Ҫ��èѧ��Ѵ" || $TMP_APPROVE_PM_NAME=="�ͧ�������Ҫ��èѧ��Ѵ" || $TMP_APPROVE_PM_NAME=="��Ѵ�ѧ��Ѵ" || 
		$TMP_APPROVE_PM_NAME=="����ӹ�¡��" || $TMP_APPROVE_PM_NAME=="����ӹ�¡�áͧ" || $TMP_APPROVE_PM_NAME=="����ӹ�¡���ٹ��" || 
		$TMP_APPROVE_PM_NAME=="����ӹ�¡���ӹѡ" || $TMP_APPROVE_PM_NAME=="����ӹ�¡��ʶҺѹ" || $TMP_APPROVE_PM_NAME=="���˹���ӹѡ�ҹ�ѧ��Ѵ") {
		$TMP_APPROVE_PM_NAME .= $TMP_APPROVE_ORG_NAME;
		$TMP_APPROVE_PM_NAME = str_replace("�ѧ��Ѵ�ѧ��Ѵ", "�ѧ��Ѵ", $TMP_APPROVE_PM_NAME); 
		$TMP_APPROVE_PM_NAME = str_replace("�ͧ�ͧ", "�ͧ", $TMP_APPROVE_PM_NAME); 
		$TMP_APPROVE_PM_NAME = str_replace("�ٹ���ٹ��", "�ٹ��", $TMP_APPROVE_PM_NAME); 
		$TMP_APPROVE_PM_NAME = str_replace("�ӹѡ�ӹѡ", "�ӹѡ", $TMP_APPROVE_PM_NAME); 
		$TMP_APPROVE_PM_NAME = str_replace("ʶҺѹʶҺѹ", "ʶҺѹ", $TMP_APPROVE_PM_NAME); 
		$TMP_APPROVE_PM_NAME = str_replace("�ӹѡ�ҹ�ѧ��Ѵ�ӹѡ�ҹ�ѧ��Ѵ", "�ӹѡ�ҹ�ѧ��Ѵ", $TMP_APPROVE_PM_NAME); 
	} elseif ($TMP_APPROVE_PM_NAME=="��������") {
		$TMP_APPROVE_PM_NAME .= $TMP_APPROVE_ORG_NAME_1;
		$TMP_APPROVE_PM_NAME = str_replace("����ͷ��ӡ�û���ͧ�����", "�����", $TMP_APPROVE_PM_NAME); 
	}
	//$POS_STATUS
	if($TMP_APPROVE_PM_NAME){
		$TMP_APPROVE_POSITION_NAME = trim($TMP_APPROVE_PM_NAME);
		$TMP_MAIN_PER_NAME = trim($TMP_APPROVE_PM_NAME);
	}else{
		$TMP_APPROVE_POSITION_NAME = trim($TMP_APPROVE_PL_NAME)?($TMP_APPROVE_PL_NAME . $TMP_APPROVE_POSITION_LEVEL . (($TMP_APPROVE_PT_NAME != "�����" && $TMP_APPROVE_LEVEL_NO >= 6)?"$TMP_APPROVE_PT_NAME":"")):"";
		$TMP_MAIN_PER_NAME = trim($TMP_APPROVE_PER_NAME);
	}
	if($POS_STATUS=='0' || empty($POS_STATUS)){
            
			// kittiphat 07062561
			//$TMP_MAIN_PER_NAME=$TMP_MAIN_PER_NAME;
			if(!empty($POS_APPROVE)){
				$TMP_APPROVE_POSITION_NAME=$POS_APPROVE;
				$TMP_MAIN_PER_NAME=$POS_APPROVE;
			}else{
				$TMP_APPROVE_POSITION_NAME=$TMP_APPROVE_POSITION_NAME;
				$TMP_MAIN_PER_NAME=$TMP_MAIN_PER_NAME;
			}
            
        }else{
            if($POS_STATUS==1){$POS_STATUS_TXT=" �ѡ���Ҫ���᷹";}
            if($POS_STATUS==2){$POS_STATUS_TXT=" �ѡ�ҡ��㹵��˹�";}
            if($POS_STATUS==3){$POS_STATUS_TXT=" ��Ժѵ��Ҫ���᷹";}
            $TMP_APPROVE_POSITION_NAME=$TMP_APPROVE_POSITION_NAME.$POS_STATUS_TXT.' '.$POS_APPROVE;
            $TMP_MAIN_PER_NAME=$POS_APPROVE;
        }
        
        ///////////////////////////////
        
	$ABS_STARTDATE_CONV = show_date_format($data[ABS_STARTDATE], 1);
        
        
        
	get_ABSENT_SUM($PER_ID,$ABS_STARTDATE_CONV,$data[ABS_ENDDATE],$ABS_DAY);	//�֧�ѹ��������
        
        /**/
        //getTotalOfYear($ABS_STARTDATE_CONV,$PER_ID,$SUMOF_SICK_ABS,$SUMOF_KIT_ABS,$SUMOF_MATERNITY_ABS,$SUMOF_PAKPON_ABS);
        
        $SUMOF_SICK_ABS = getTotalBeforeOfYear($ABS_STARTDATE_CONV,$PER_ID,'01');//---
        $SUMOF_KIT_ABS = getTotalBeforeOfYear($ABS_STARTDATE_CONV,$PER_ID,'03');//---
        $SUMOF_MATERNITY_ABS = getTotalBeforeOfYear($ABS_STARTDATE_CONV,$PER_ID,'02');//---
        $SUMOF_PAKPON_ABS= getTotalBeforeOfYear($ABS_STARTDATE_CONV,$PER_ID,'04');//---
        
        $AB_CODE_01=$SUMOF_SICK_ABS;//---
        $DAY1_3 = $DAY1_2+$AB_CODE_01;
        
        $AB_CODE_03=$SUMOF_KIT_ABS; //---
        $DAY3_3 = $DAY3_2+$AB_CODE_03;
        
        $AB_CODE_02=$SUMOF_MATERNITY_ABS; //---
        $DAY2_3 = $DAY2_2+$AB_CODE_02;
        
        $AB_CODE_04=$SUMOF_PAKPON_ABS; //---
        $SumDAY = $DAY4_2 + $AB_CODE_04;
        //die('');
        /**/
        
        
//--------------------

	if($AB_CODE=="01" || $AB_CODE=="02" || $AB_CODE=="03"){
		if(trim($DEPARTMENT_NAME)=="�ӹѡ�ҹ �.�.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"){
			$ABS_NOTE = convert2thaidigit("�����˵�  㹡�þԨ�ó�����͹�Թ��͹��Шӻ�����Ф��駨оԨ�óҨҡࡳ���ѹ����ͺ 6 ��͹  ������ѹ��� 1 �.�. �ͧ��˹�觶֧�ѹ��� 31 ��.�. �ͧ�նѴ�  �����ѹ��� 1 ��.�. �֧�ѹ��� 30 �.�. �ͧ�����ǡѹ������ó�  �¨е�ͧ����һ�������ҡԨ��ǹ�������ѹ�Թ���� 10 ����  ���㹨ӹǹ 10 ���� �е�ͧ����Թ 23 �ѹ��е�ͧ����ҷӧҹ����Թ 18 ���� (�ѹ)");	
		}

		$LAST_ABS_STARTDATE = $LAST_ABS_ENDDATE = $LAST_ABS_DAY ="-";
		/*****
		$cmd = " select 	ABS_STARTDATE, ABS_ENDDATE, ABS_DAY 
				   from		PER_ABSENT
				   where	PER_ID=$PER_ID and trim(AB_CODE)='$AB_CODE' and ABS_ID not in ($ABS_ID)
				   				and ABS_ENDDATE < '".substr($data[ABS_ENDDATE], 0, 10)."'
				   order by	ABS_STARTDATE desc ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		if($data2[ABS_STARTDATE]!=""&&$data2[ABS_STARTDATE]!="NULL"&&$data2[ABS_STARTDATE]!="null"){
			$LAST_ABS_STARTDATE = substr($data2[ABS_STARTDATE], 0, 10);
			if(trim($LAST_ABS_STARTDATE)){
				//$LAST_ABS_STARTDATE = show_date_format($LAST_ABS_STARTDATE,$DATE_DISPLAY);	// �ҧ�����͡��˹��ٻẺ�������͹�ѹ 
				$temp_date = explode("-", $LAST_ABS_STARTDATE);		// Y-m-d
				$LAST_ABS_START_Y= ($temp_date[0] + 543);
				$LAST_ABS_START_M= $temp_date[1];
				$LAST_ABS_START_D= $temp_date[2];
				$LAST_ABS_STARTDATE=convert2thaidigit($LAST_ABS_START_D." ".($month_full[($LAST_ABS_START_M + 0)][TH])." ".$LAST_ABS_START_Y);
			} // end if
		}
		if($data2[ABS_ENDDATE]!=""&&$data2[ABS_ENDDATE]!="NULL"&&$data2[ABS_ENDDATE]!="null"){
			$LAST_ABS_ENDDATE = substr($data2[ABS_ENDDATE], 0, 10);
			if(trim($LAST_ABS_ENDDATE)){
				//$LAST_ABS_ENDDATE = show_date_format($LAST_ABS_ENDDATE,$DATE_DISPLAY);	// �ҧ�����͡��˹��ٻẺ�������͹�ѹ 
				$temp_date = explode("-", $LAST_ABS_ENDDATE);		// Y-m-d
				$LAST_ABS_END_Y= ($temp_date[0] + 543);
				$LAST_ABS_END_M= $temp_date[1];
				$LAST_ABS_END_D= $temp_date[2];
				$LAST_ABS_ENDDATE=convert2thaidigit($LAST_ABS_END_D." ".($month_full[($LAST_ABS_END_M + 0)][TH])." ".$LAST_ABS_END_Y);
			} // end if
		}
			$LAST_ABS_DAY = trim($data2[ABS_DAY])?trim($data2[ABS_DAY]):"-";
		*****/
		
		// ��������� PER_ABSENT ������� PER_ABSENTHIS �ѹ���������ش �����Ҩлէ�����ҳ�˹����
//if($LAST_ABS_STARTDATE == "-" || $LAST_ABS_ENDDATE == "-" || $LAST_ABS_DAY == "-"){
			$cmd = " select ABS_STARTDATE, ABS_ENDDATE, ABS_DAY  from PER_ABSENTHIS 
							where PER_ID=$PER_ID and trim(AB_CODE)='$AB_CODE' and ABS_ID not in ($ABS_ID)
				   				and ABS_ENDDATE < '".substr($data[ABS_ENDDATE], 0, 10)."'
								 order by	ABS_STARTDATE desc ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			if($data2[ABS_STARTDATE]!=""&&$data2[ABS_STARTDATE]!="NULL"&&$data2[ABS_STARTDATE]!="null"){
				$LAST_ABS_STARTDATE = substr($data2[ABS_STARTDATE], 0, 10);
				if(trim($LAST_ABS_STARTDATE)){
					//$LAST_ABS_STARTDATE = show_date_format($LAST_ABS_STARTDATE,$DATE_DISPLAY); /*���*/
                                    $LAST_ABS_STARTDATE = substr($LAST_ABS_STARTDATE, 0, 10);
                                    $temp_date = explode("-", $LAST_ABS_STARTDATE);		// Y-m-d
                                    $ABS_END_Y= ($temp_date[0] + 543);
                                    $ABS_END_M= $temp_date[1];
                                    $ABS_END_D= $temp_date[2];
                                    $LAST_ABS_STARTDATE=convert2thaidigit(($ABS_END_D+0)." ".($month_full[($ABS_END_M + 0)][TH])." ".$ABS_END_Y);
				} // end if
			}
			if($data2[ABS_ENDDATE]!=""&&$data2[ABS_ENDDATE]!="NULL"&&$data2[ABS_ENDDATE]!="null"){
				$LAST_ABS_ENDDATE = substr($data2[ABS_ENDDATE], 0, 10);
				if(trim($LAST_ABS_ENDDATE)){
					//$LAST_ABS_ENDDATE = show_date_format($LAST_ABS_ENDDATE,$DATE_DISPLAY); /*���*/
                                    $LAST_ABS_ENDDATE = substr($LAST_ABS_ENDDATE, 0, 10);
                                    $temp_date = explode("-", $LAST_ABS_ENDDATE);		// Y-m-d
                                    $ABS_END_Y= ($temp_date[0] + 543);
                                    $ABS_END_M= $temp_date[1];
                                    $ABS_END_D= $temp_date[2];
                                    $LAST_ABS_ENDDATE=convert2thaidigit(($ABS_END_D+0)." ".($month_full[($ABS_END_M + 0)][TH])." ".$ABS_END_Y);
				} // end if
			}
			$LAST_ABS_DAY = trim($data2[ABS_DAY])?trim($data2[ABS_DAY]):"-";		
//} // end if
		
		$arr_temp = explode("-", substr($data[ABS_STARTDATE], 0, 10));
		$SEARCH_PERIOD = $arr_temp[1] . $arr_temp[2];
		if($SEARCH_PERIOD >= "1001" || $SEARCH_PERIOD <= "0331"){
			if($arr_temp[1] >= 10){
				if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '".$arr_temp[0]."-10-01') and (LEFT(ABS_STARTDATE, 10) <= '".($arr_temp[0] + 1)."-03-31')";
				elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(ABS_STARTDATE, 1, 10) >= '".$arr_temp[0]."-10-01') and (SUBSTR(ABS_STARTDATE, 1, 10) <= '".($arr_temp[0] + 1)."-03-31')";
				elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '".$arr_temp[0]."-10-01') and (LEFT(ABS_STARTDATE, 10) <= '".($arr_temp[0] + 1)."-03-31')";
			}else{
				if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '".($arr_temp[0] - 1)."-10-01') and (LEFT(ABS_STARTDATE, 10) <= '".$arr_temp[0]."-03-31')";
				elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(ABS_STARTDATE, 1, 10) >= '".($arr_temp[0] - 1)."-10-01') and (SUBSTR(ABS_STARTDATE, 1, 10) <= '".$arr_temp[0]."-03-31')";
				elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '".($arr_temp[0] - 1)."-10-01') and (LEFT(ABS_STARTDATE, 10) <= '".$arr_temp[0]."-03-31')";
			} // end if 
		}elseif($SEARCH_PERIOD >= "0401" && $SEARCH_PERIOD <= "0930"){
			if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '".$arr_temp[0]."-04-01') and (LEFT(ABS_STARTDATE, 10) <= '".$arr_temp[0]."-09-30')";
			elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(ABS_STARTDATE, 1, 10) >= '".$arr_temp[0]."-04-01') and (SUBSTR(ABS_STARTDATE, 1, 10) <= '".$arr_temp[0]."-09-30')";
			elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '".$arr_temp[0]."-04-01') and (LEFT(ABS_STARTDATE, 10) <= '".$arr_temp[0]."-09-30')";
		} // end if

		$search_condition = "";
		if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);

	//==���ҧ contents ����Ѻ 01 || 02 || 03
	$RTF->open_line();
	$RTF->set_font($font, 16);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell($RTF->bold(1).$RTF->underline(1)."Ẻ��һ���  �Ҥ�ʹ�ص�  �ҡԨ��ǹ���".$RTF->underline(0).$RTF->bold(0).$RTF->color("0"), "100", "center", "0");
	$RTF->close_line();

	//$RTF->ln();
	
	$RTF->open_line();			
	$RTF->set_font($font,$FONTSIZE);
	$RTF->cell("", "60", "left", "0");
	//$RTF->cell($RTF->color("0").$RTF->bold(0)."��¹���  ".convert2rtfascii($DEPARTMENT_NAME).$RTF->bold(0), "40", "left", "0");
        $RTF->cell($RTF->color("0").$RTF->bold(0)."��¹���  ".convert2rtfascii($DivisionName).$RTF->bold(0), "40", "left", "0");
	$RTF->close_line();
	
	$RTF->open_line();			
	$RTF->set_font($font,$FONTSIZE);
	$RTF->cell("", "60", "left", "0");
	$RTF->cell($RTF->color("0").$RTF->bold(0).$CREATE_DATE.$RTF->bold(0), "40", "left", "0");
	$RTF->close_line();	

	$RTF->open_line();			
	$RTF->set_font($font,$FONTSIZE);
	$RTF->cell(convert2thaidigit("����ͧ"), "10", "left", "0");
	$RTF->cell(convert2rtfascii($TITLE), "90", "left", "0");	
	$RTF->close_line();
	
	$RTF->open_line();			
	$RTF->set_font($font,$FONTSIZE);
	$RTF->cell(convert2thaidigit("���¹"), "10", "left", "0");
	$RTF->cell(convert2rtfascii($TMP_MAIN_PER_NAME), "90", "left", "0");	
	$RTF->close_line();	
	
//	$RTF->ln();
	$RTF->open_line();			
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK

	$RTF->cell(str_repeat(" ", 20)."��Ҿ���  ".convert2rtfascii($PER_NAME)."    ���˹�  ".convert2rtfascii($PL_NAME)."    ".convert2rtfascii($LEVEL_NAME)."    �ѧ�Ѵ  ".convert2rtfascii($ORG_NAME.$DivisionNameAssShow)."    ��  ".convert2rtfascii($AB_NAME)."    ���ͧ�ҡ  ".convert2rtfascii($ABS_REASON)."    ������ѹ���  ".convert2thaidigit($ABS_STARTDATE_Show).$ORI_STARTPERIOD_show."    �֧�ѹ���  ".convert2thaidigit($ABS_ENDDATE_Show).$ORI_ENDPERIOD_show."    �ա�˹�  ".convert2thaidigit(round($ABS_DAY,2))."  �ѹ$ORI_STARTPERIOD_LastShow   ��Ҿ�����   ".convert2rtfascii($AB_NAME)."    �����ش���µ�����ѹ���  ".convert2thaidigit($LAST_ABS_STARTDATE)."    �֧�ѹ���  ".convert2thaidigit($LAST_ABS_ENDDATE)."    �ա�˹�  ".convert2thaidigit(round($LAST_ABS_DAY,2))."  �ѹ    ������ҧ�ҨеԴ��͢�Ҿ�������  ".convert2thaidigit($ABS_ADDRESS), "100", "left", "0");  
	$RTF->close_line();
	//$RTF->cell(convert2rtfascii($ABS_ADDRESS), "200", "left", "0");
	//$RTF->close_line();

	$RTF->ln();
	$RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->cell("", "50", "left", "0");
	if($PIC_SIGN_PER){  // ���ٻ�����
		$RTF->cellImagexy($PIC_SIGN_PER, 20,20, 50, "center", 0);
	}else{
		$RTF->color("1");	// 1=DARKGRAY
		$RTF->cell(("(ŧ����) $RANK_NAME". str_repeat(".", 45)), "50", "center", "0");
	}
	$RTF->close_line();
	
	$RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "50", "left", "0");
	$RTF->cell(("( ".convert2rtfascii($PER_NAME)." )"), "50", "center", "0");
	$RTF->close_line();
	
	$RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "50", "left", "0");
	$RTF->cell((convert2rtfascii($TMP_PERSON_POSITION_NAME)), "50", "center", "0");
	$RTF->close_line();	
	
	// ���ҧ�ʴ�ʶԵԡ����
	$RTF->open_line();			
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("1");	// 0=DARKGRAY
	$RTF->cell($RTF->bold(1).$RTF->underline(1).convert2thaidigit("ʶԵԡ����㹻է�����ҳ���").$RTF->underline(0).$RTF->bold(1), $heading_width1[0]+$heading_width1[1]+$heading_width1[2]+$heading_width1[3], "left", "0");		//ʶԵԡ������ͺ 6 ��͹
	$RTF->cell($RTF->bold(1).$RTF->underline(1)."������繼��ѧ�Ѻ�ѭ��".$RTF->underline(0).$RTF->bold(0), 50, "left", "0", "");
    $RTF->close_line();
	
	print_header(1);
	
	//�Ƿ�� 1
	$RTF->open_line();			
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell($RTF->bold(0).convert2thaidigit("����").$RTF->bold(0), $heading_width1[0], "left", "0", "TRBL");
	$RTF->cell(convert2thaidigit(round($AB_CODE_01,2)), $heading_width1[1], "center", "0", "TBRL");
	$RTF->cell(convert2thaidigit(round($DAY1_2,2)), $heading_width1[2], "center", "0", "TBRL"); //Edit
	$RTF->cell(convert2thaidigit(round($DAY1_3,2)), $heading_width1[3], "center", "0", "TBRL");
    show_result_checkboxV2($TMP_REVIEW_FLAG_RESULT,"REVIEW_FLAG","");    
	$RTF->close_line();

	//�Ƿ�� 2
	$RTF->open_line();			
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell(convert2thaidigit("�Ԩ��ǹ���"), $heading_width1[0], "left", "0", "TRBL");
	$RTF->cell(convert2thaidigit(round($AB_CODE_03,2)), $heading_width1[1], "center", "0", "TRBL");
	$RTF->cell(convert2thaidigit(round($DAY3_2,2)), $heading_width1[2], "center", "0", "TRBL");
	$RTF->cell(convert2thaidigit(round($DAY3_3,2)), $heading_width1[3], "center", "0", "TRBL");
   //���ѧ�Ѻ�ѭ��    
    if($PIC_SIGN_REVIEW && $TMP_REVIEW_FLAG_RESULT!=0){  // ���ٻ�����
		$RTF->cellImagexy($PIC_SIGN_REVIEW, 20,20, 50, "center", 0);
	}else{
		$RTF->color("1");	// 1=DARKGRAY
		$RTF->cell(("(ŧ����) $REVIEW_RANK_NAME". str_repeat(".", 45)), "50", "center", "0");
	}
	$RTF->close_line();
	
	//�Ƿ�� 3
	$RTF->open_line();			
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell(convert2thaidigit("��ʹ�ص�"), $heading_width1[0], "left", "0", "TRBL");
	$RTF->cell(convert2thaidigit(round($AB_CODE_02,2)), $heading_width1[1], "center", "0", "TRBL");
	$RTF->cell(convert2thaidigit(round($DAY2_2,2)), $heading_width1[2], "center", "0", "TRBL");
	$RTF->cell(convert2thaidigit(round($DAY2_3,2)), $heading_width1[3], "center", "0", "TRBL");
    $RTF->cell(("( ".convert2rtfascii($TMP_REVIEW_PER_NAME)." )"), "55", "center", "0"); //���ѧ�Ѻ�ѭ�Ҫ�鹵� / ���ѧ�Ѻ�ѭ�Ҫ�鹵��˹�͢���     
	$RTF->close_line();
	//==�� ���ҧ�ʴ�ʶԵԡ����

	/*$RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "50", "left", "0");
	$RTF->cell($RTF->bold(1).$RTF->underline(1)."������繼��ѧ�Ѻ�ѭ��".$RTF->underline(0).$RTF->bold(0), "50", "left", "0");
	$RTF->close_line();*/	
	
	//show_result_checkbox($TMP_REVIEW_FLAG_RESULT,"REVIEW_FLAG","");
        
	$RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	//����Ǩ�ͺ
	if($PIC_SIGN_AUDIT && $TMP_AUDIT_FLAG!=0){  // ���ٻ�����
		$RTF->cell("����Ǩ�ͺ", "50", "center", "0");
        $RTF->cell(convert2rtfascii($TMP_REVIEW_POSITION_NAME), "50", "center", "0"); /* Release 5.1.0.6*/  
		$RTF->close_line();	
		$RTF->open_line();
		$RTF->set_font($font,$FONTSIZE);
		$RTF->color("0");	// 0=BLACK
		$RTF->cellImagexy($PIC_SIGN_AUDIT, 20,20, 50, "center", 0);
        $RTF->cell("�ѹ��� ".$REVIEW_DATE, "50", "center", "0");	 //���ѧ�Ѻ�ѭ��
	}else{
		$RTF->color("1");	// 1=DARKGRAY
		$RTF->cell(("(ŧ����) $AUDIT_RANK_NAME".str_repeat(".", 45)." ����Ǩ�ͺ"), "50", "center", "0");
        $RTF->cell(convert2rtfascii($TMP_REVIEW_POSITION_NAME), "50", "center", "0"); /* Release 5.1.0.6*/ 
        $RTF->cell("�ѹ��� ".$REVIEW_DATE, "50", "center", "0");	 //���ѧ�Ѻ�ѭ��
	}
        
//	if($PIC_SIGN_REVIEW && $TMP_REVIEW_FLAG_RESULT!=0){  // ���ٻ�����
//		$RTF->cellImagexy($PIC_SIGN_REVIEW, 20,20, 50, "center", 0);
//	}else{
//		$RTF->color("1");	// 1=DARKGRAY
//		$RTF->cell(("(ŧ����) ". str_repeat(".", 45)), "50", "center", "0");
//	}
    	  
	$RTF->close_line();	
	
	$RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->cell("( ".convert2rtfascii($TMP_AUDIT_PER_NAME)." )", "50", "center", "0");	//����Ǩ�ͺ
    $RTF->cell($RTF->bold(1).$RTF->underline(1)."�����".$RTF->underline(0).$RTF->bold(0), "50", "left", "0");    
	//$RTF->cell(("( ".convert2rtfascii($TMP_REVIEW_PER_NAME)." )"), "50", "center", "0"); //���ѧ�Ѻ�ѭ�Ҫ�鹵� / ���ѧ�Ѻ�ѭ�Ҫ�鹵��˹�͢��� 
	$RTF->close_line();
	
	$RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	//$RTF->cell("(���˹�) ".convert2rtfascii($TMP_AUDIT_POSITION_NAME), "50", "center", "0");	//����Ǩ�ͺ /*���*/
	//$RTF->cell("(���˹�) ".convert2rtfascii($TMP_REVIEW_POSITION_NAME), "50", "center", "0"); /*���*/
    $RTF->cell(convert2rtfascii($TMP_AUDIT_POSITION_NAME), "50", "center", "0");	//����Ǩ�ͺ /* Release 5.1.0.6*/
    if(empty($APPROVE_NOTE)){
            $RTF->cell(str_repeat(".", 190), "50", "left", "0");
    }else{
            $RTF->cell($APPROVE_NOTE, "50", "left", "0");
    }    
	$RTF->close_line();	
	
	$RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("�ѹ��� ".$AUDIT_DATE, "50", "center", "0");		//����Ǩ�ͺ
    show_result_checkboxV2($TMP_APPROVE_FLAG_RESULT,"APPROVE_FLAG","");
	$RTF->close_line();
//	
//	$RTF->open_line();
//	$RTF->set_font($font,$FONTSIZE);
//	$RTF->color("0");	// 0=BLACK
//	$RTF->cell("", "50", "left", "0");
//	$RTF->cell($RTF->bold(1).$RTF->underline(1)."�����".$RTF->underline(0).$RTF->bold(0), "50", "left", "0");
//	$RTF->close_line();
        
//    $RTF->open_line();
//	$RTF->set_font($font,$FONTSIZE);
//	$RTF->color("0");	// 0=BLACK
//	$RTF->cell("", "50", "left", "0");
//    
//	$RTF->close_line();
//        $RTF->open_line();
//        $RTF->set_font($font,$FONTSIZE);
//        $RTF->cell(str_repeat(".", 50), 50, "left", "0", "");
//        $RTF->close_line();
	
	
	$RTF->open_line();
	$RTF->cell("", "50", "left", "0");	
	if($PIC_SIGN_APPROVE && $TMP_APPROVE_FLAG!=0){  // ���ٻ�����
		$RTF->cellImagexy($PIC_SIGN_APPROVE, 20,20, 50, "center", 0);
	}else{
		$RTF->set_font($font,$FONTSIZE);
		$RTF->color("1");	// 1=DARKGRAY
		$RTF->cell(("(ŧ����) $APPROVE_RANK_NAME". str_repeat(".", 45)), "50", "center", "0");
	}
	$RTF->close_line();
	
	$RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "50", "left", "0");
	$RTF->cell(("( ".convert2rtfascii($TMP_APPROVE_PER_NAME)." )"), "50", "center", "0");
	$RTF->close_line();	
	
	$RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "50", "left", "0");
	//$RTF->cell("(���˹�) ".convert2rtfascii($TMP_APPROVE_POSITION_NAME), "50", "center", "0");/*���*/
        $RTF->cell(convert2rtfascii($TMP_APPROVE_POSITION_NAME), "50", "center", "0");/*Release 5.1.0.6*/
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "50", "left", "0");
	$RTF->cell("�ѹ��� ".$APPROVE_DATE, "50", "center", "0");		//���͹حҵ
	$RTF->close_line();		

	if($ABS_NOTE){
		$RTF->ln();
		$RTF->open_line();
		//$RTF->set_font($font,$FONTSIZE12);
		$RTF->set_table_font($font,$FONTSIZE12);			// Note �ѹ�� set_font ���Ǣ�Ҵ����ѡ���������¹
		$RTF->color("0");	// 0=BLACK
		$RTF->cell(convert2rtfascii($ABS_NOTE), "100", "left", "0");
		$RTF->close_line();
	}
	//==��������ҧ contents

	}elseif($AB_CODE=="04"){
		$arr_temp = explode("-", substr($data[ABS_STARTDATE], 0, 10));
		$SEARCH_PERIOD = $arr_temp[1] . $arr_temp[2];
		if($SEARCH_PERIOD >= "1001"){
			if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '".$arr_temp[0]."-10-01') and (LEFT(ABS_STARTDATE, 10) <= '".($arr_temp[0] + 1)."-09-30')";
			elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(ABS_STARTDATE, 1, 10) >= '".$arr_temp[0]."-10-01') and (SUBSTR(ABS_STARTDATE, 1, 10) <= '".($arr_temp[0] + 1)."-09-30')";
			elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '".$arr_temp[0]."-10-01') and (LEFT(ABS_STARTDATE, 10) <= '".($arr_temp[0] + 1)."-09-30')";
		}else{
			if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '".($arr_temp[0] - 1)."-10-01') and (LEFT(ABS_STARTDATE, 10) <= '".$arr_temp[0]."-09-30')";
			elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(ABS_STARTDATE, 1, 10) >= '".($arr_temp[0] - 1)."-10-01') and (SUBSTR(ABS_STARTDATE, 1, 10) <= '".$arr_temp[0]."-09-30')";
			elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '".($arr_temp[0] - 1)."-10-01') and (LEFT(ABS_STARTDATE, 10) <= '".$arr_temp[0]."-09-30')";
		} // end if

	//==���ҧ contents ����Ѻ 04
	$RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell($RTF->bold(1)."Ẻ��Ҿѡ��͹".$RTF->bold(1).$RTF->color("0"), "100", "center", "0");
	$RTF->close_line();

	//$RTF->ln();
	
	$RTF->open_line();			
	$RTF->set_font($font,$FONTSIZE);
	$RTF->cell("", "60", "left", "0");
	//$RTF->cell($RTF->color("0").$RTF->bold(0)."��¹���  ".convert2rtfascii($DEPARTMENT_NAME).$RTF->bold(0), "40", "left", "0");
        $RTF->cell($RTF->color("0").$RTF->bold(0)."��¹���  ".convert2rtfascii($DivisionName).$RTF->bold(0), "40", "left", "0");
	$RTF->close_line();
	
	$RTF->open_line();			
	$RTF->set_font($font,$FONTSIZE);
	$RTF->cell("", "60", "left", "0");
	$RTF->cell($RTF->color("0").$RTF->bold(0).$CREATE_DATE.$RTF->bold(0), "40", "left", "0");
	$RTF->close_line();	

	$RTF->open_line();			
	$RTF->set_font($font,$FONTSIZE);
	$RTF->cell(convert2thaidigit("����ͧ"), "10", "left", "0");
	$RTF->cell(convert2rtfascii($TITLE), "90", "left", "0");	
	$RTF->close_line();
	
	$RTF->open_line();			
	$RTF->set_font($font,$FONTSIZE);
	$RTF->cell(convert2thaidigit("���¹"), "10", "left", "0");
	$RTF->cell(convert2rtfascii($TMP_MAIN_PER_NAME), "90", "left", "0");	
	$RTF->close_line();	
	
//	$RTF->ln();
	$RTF->open_line();			
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	
	$RTF->cell(str_repeat(" ", 20)."��Ҿ���  ".convert2rtfascii($PER_NAME)
                ."    ���˹�  ".convert2rtfascii($PL_NAME)."    ".convert2rtfascii($LEVEL_NAME)
                ."    �ѧ�Ѵ  ".convert2rtfascii($ORG_NAME.$DivisionNameAssShow)."    ���ѹ�Ҿѡ��͹����  ".convert2thaidigit($VC_DAY)
                ."  �ѹ�ӡ��    ���Է���Ҿѡ��͹��Шӻչ���ա  ".convert2thaidigit($MAX_ABSENT_04)
                ."  �ѹ�ӡ��    �����  ".convert2thaidigit($TOT_DAY)
                ."  �ѹ�ӡ��    ���Ҿѡ��͹������ѹ���  ".convert2thaidigit($ABS_STARTDATE_Show).$ORI_STARTPERIOD_show
                ."  �֧�ѹ���  ".convert2thaidigit($ABS_ENDDATE_Show).$ORI_ENDPERIOD_show
                ."    �ա�˹�  ".convert2thaidigit(round($ABS_DAY,2))."  �ѹ$ORI_STARTPERIOD_LastShow    ������ҧ�ҨеԴ��͢�Ҿ������� ".convert2thaidigit($ABS_ADDRESS), "100", "left", "0");  
	$RTF->close_line();
	//$RTF->cell(convert2rtfascii($ABS_ADDRESS), "200", "left", "0");
	//$RTF->close_line();

	// ���ҧ�ʴ�ʶԵԡ����	
	$RTF->ln();
//	$RTF->cell("���ʴ������Ѻ���", "50", "center", "0");
	$RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->cell("", "50", "left", "0");
	if($PIC_SIGN_PER){  // ���ٻ�����
		$RTF->cellImagexy($PIC_SIGN_PER, 20,20, 50, "center", 0);
	}else{
		$RTF->color("1");	// 1=DARKGRAY
		$RTF->cell(("(ŧ����) $RANK_NAME". str_repeat(".", 45)), "50", "center", "0");
	}
	$RTF->close_line();
	
	$RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "50", "left", "0");
	$RTF->cell(("( ".convert2rtfascii($PER_NAME)." )"), "50", "center", "0");
	$RTF->close_line();	
	
	$RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "50", "left", "0");
	$RTF->cell((convert2rtfascii($TMP_PERSON_POSITION_NAME)), "50", "center", "0");
	$RTF->close_line();
	
	// ���ҧ�ʴ�ʶԵԡ����
	$RTF->open_line();			
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("1");	// 0=DARKGRAY
	$RTF->cell($RTF->bold(1).convert2thaidigit("ʶԵԡ����㹻է�����ҳ���").$RTF->bold(1), $heading_width2[0]+$heading_width2[1]+$heading_width2[2], "left", "0");
	
	$RTF->close_line();
	
	print_header(2);
	
	//�Ƿ�� 3
	$RTF->open_line();			
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell($RTF->bold(0).convert2thaidigit($AB_CODE_04).$RTF->bold(0), $heading_width2[0], "center", "0", "TRBL");		// ��������
	$RTF->cell(convert2thaidigit(round($DAY4_2,2)), $heading_width2[1], "center", "0", "TRBL");		// �Ҥ��駹��
	$RTF->cell(convert2thaidigit((round($SumDAY,2))), $heading_width2[2], "center", "0", "TRBL");		// �����
	$RTF->cell("", "14", "left", "0"); 
	if(empty($REVIEW2_NOTE)){
		$RTF->cell(str_repeat(".", 93), "50", "left", "0");
	}else{
		$RTF->cell($REVIEW2_NOTE, "50", "left", "0");
	}
	$RTF->close_line();
	//==�� ���ҧ�ʴ�ʶԵԡ����

	/*
	kittiphat 08/08/2561
	$RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "50", "left", "0");
	$RTF->cell($RTF->bold(1).$RTF->underline(1)."������繼��ѧ�Ѻ�ѭ��".$RTF->underline(0).$RTF->bold(0), "50", "left", "0");
	$RTF->close_line();
        
        $RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "50", "left", "0");
	if(empty($REVIEW2_NOTE)){
            $RTF->cell(str_repeat(".", 93), "50", "left", "0");
        }else{
            $RTF->cell($REVIEW2_NOTE, "50", "left", "0");
        }
	$RTF->close_line();*/
        
        
        
	
	show_result_checkbox($TMP_REVIEW_FLAG_RESULT,"REVIEW_FLAG","");
	
	
        
        //��Ҿѡ��͹-------------------------------------
        $RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	//����Ǩ�ͺ
        $chkImgAudit = '';
	if($PIC_SIGN_AUDIT && $TMP_AUDIT_FLAG!=0){  // ���ٻ�����
		$RTF->cell("����Ǩ�ͺ", "45", "center", "0");
		$RTF->close_line();
		$RTF->open_line();
		$RTF->set_font($font,$FONTSIZE);
		$RTF->color("0");// 0=BLACK
		$RTF->cellImagexy($PIC_SIGN_AUDIT, 20,20, 45, "center", 0);
                $chkImgAudit = 'IMG';
	}else{
		$RTF->color("1");	// 1=DARKGRAY
		$RTF->cell(("(ŧ����) $AUDIT_RANK_NAME".str_repeat(".", 45)." ����Ǩ�ͺ"), "45", "center", "0");
	}
	if($PIC_SIGN_REVIEW && $TMP_REVIEW_FLAG_RESULT!=0){  // ���ٻ�����
		$RTF->cellImagexy($PIC_SIGN_REVIEW, 20,20, 55, "center", 0);
	}else{
		$RTF->color("1");	// 1=DARKGRAY
		$RTF->cell(("(ŧ����) $REVIEW_RANK_NAME". str_repeat(".", 45)), "55", "center", "0");
	}
	$RTF->close_line();	
	
	$RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);  
	$RTF->cell("( ".convert2rtfascii($TMP_AUDIT_PER_NAME)." )", "45", "center", "0");	//����Ǩ�ͺ
	$RTF->cell(("( ".convert2rtfascii($TMP_REVIEW_PER_NAME)." )"), "55", "center", "0");//���ѧ�Ѻ�ѭ�Ҫ�鹵� / ���ѧ�Ѻ�ѭ�Ҫ�鹵��˹�͢��� 
	$RTF->close_line();
	
	$RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	//$RTF->cell("(���˹�) ".convert2rtfascii($TMP_AUDIT_POSITION_NAME), "50", "center", "0");	//����Ǩ�ͺ /*���*/
	//$RTF->cell("(���˹�) ".convert2rtfascii($TMP_REVIEW_POSITION_NAME), "50", "center", "0");/*���*/
        $RTF->cell(convert2rtfascii($TMP_AUDIT_POSITION_NAME), "45", "center", "0");	//����Ǩ�ͺ /*Release 5.1.0.6*/
	$RTF->cell(convert2rtfascii($TMP_REVIEW_POSITION_NAME), "55", "center", "0");/*Release 5.1.0.6*/
	$RTF->close_line();	

	$RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("�ѹ��� ".$AUDIT_DATE, "45", "center", "0");		//����Ǩ�ͺ
	$RTF->cell("�ѹ��� ".$REVIEW_DATE, "55", "center", "0");		//���ѧ�Ѻ�ѭ��
	$RTF->close_line();	
        
        
        /////�����--------------------------------------------
	$RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "50", "left", "0");
	$RTF->cell($RTF->bold(1).$RTF->underline(1)."�����".$RTF->underline(0).$RTF->bold(0), "50", "left", "0");
	$RTF->close_line();
        
     $RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "50", "left", "0");
	if(empty($APPROVE_NOTE)){
            $RTF->cell(str_repeat(".", 187), "50", "left", "0");
        }else{
            $RTF->cell($APPROVE_NOTE, "50", "left", "0");
        }
	$RTF->close_line();
        
        
       
	
	show_result_checkbox($TMP_APPROVE_FLAG_RESULT,"APPROVE_FLAG","");
	
	$RTF->open_line();
	$RTF->cell("", "45", "left", "0");	
	if($PIC_SIGN_APPROVE  && $TMP_APPROVE_FLAG!=0){  // ���ٻ�����
		$RTF->cellImagexy($PIC_SIGN_APPROVE, 20,20, 55, "center", 0);
	}else{
		$RTF->set_font($font,$FONTSIZE);
		$RTF->color("1");	// 1=DARKGRAY
		$RTF->cell(("(ŧ����) $APPROVE_RANK_NAME". str_repeat(".", 45)), "55", "center", "0");
	}
	$RTF->close_line();
	
	$RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "45", "left", "0");
	$RTF->cell(("( ".convert2rtfascii($TMP_APPROVE_PER_NAME)." )"), "55", "center", "0");
	$RTF->close_line();	
	
	$RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "45", "left", "0");
	//$RTF->cell("(���˹�) ".convert2rtfascii($TMP_APPROVE_POSITION_NAME), "50", "center", "0");/*���*/
        $RTF->cell(convert2rtfascii($TMP_APPROVE_POSITION_NAME), "55", "center", "0");/*Release 5.1.0.6*/
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,$FONTSIZE);
	$RTF->color("0");	// 0=BLACK
	$RTF->cell("", "45", "left", "0");
	$RTF->cell("�ѹ��� ".$APPROVE_DATE, "55", "center", "0");		//���͹حҵ
	$RTF->close_line();		
	//==��������ҧ contents 04

} // end if

	$RTF->display($fname);

	ini_set("max_execution_time", 30);
?>