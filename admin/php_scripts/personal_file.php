<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/upload_personal_file.php");			//�Ѵ�������ǡѺ���

//	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	$UPDATE_DATE = date("Y-m-d H:i:s");

	//�Ң����źؤ��
	if($PER_ID){
		if($DPISDB=="odbc"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_CARDNO,PER_TYPE
								from		PER_PERSONAL as PER
											left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
							 where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
											PER_PERSONAL.PER_CARDNO,PER_TYPE
								from		PER_PERSONAL, PER_PRENAME
							 where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_CARDNO,PER_TYPE
								from		PER_PERSONAL as PER
											left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
							 where	PER.PER_ID=$PER_ID ";
		} // end if
		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_TYPE =  $data[PER_TYPE];
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_CARDNO = (trim($data[PER_CARDNO]))?trim($data[PER_CARDNO]): "NULL";
	} // end if

	if ($PER_CARDNO) {
		
		$FILE_PATH = $PATH_MAIN."/".$PER_CARDNO."/".$CATEGORY."/".$LAST_SUBDIR;//���ҧ����������Ѻ�����
		$file_namedb = $PER_CARDNO."_".$CATEGORY.$LAST_SUBDIR; //���ͷ����ŧ� database
                
                /*Release 5.1.0.2 Begin*/
                /*if ($CATEGORY=="PER_ATTACHMENT") {
                    
                    $TMP_COM_ID = $COM_ID;
                    $FILE_PATH="../attachments/PER_COMMAND/".$TMP_COM_ID;
                    $file_namedb = "_PER_COMMAND".$TMP_COM_ID;
                }*/
                
                /*Release 5.1.0.2 End*/ 
	} else if ($CATEGORY=="PER_COMMAND") {	// 
		//���ҧ����������Ѻ�����
		$FILE_PATH = $PATH_MAIN."/".$CATEGORY."/".$LAST_SUBDIR;
		//���ͷ����ŧ� database
		$file_namedb = $CATEGORY."_".$LAST_SUBDIR;
	}
//	echo "PATH_MAIN=".$PATH_MAIN."/PER_CARDNO=".$PER_CARDNO."/ CATEGORY=".$CATEGORY."/ LAST_SUBDIR=".$LAST_SUBDIR." (file_namedb=$file_namedb)<br>";
	//=====================//
	switch($CATEGORY){
		case "PER_POSITIONHIS":
			$TITLE = "��ô�ç���˹�";
		break;
		case "PER_SALARYHIS":
			$TITLE = "�Թ��͹";
		break;
		case "PER_EXTRA_INCOMEHIS" :
			$TITLE = "�Թ�����";
		break;
		case "PER_EXTRAHIS" :
			$TITLE = "�Թ���������";
		break;
	 	case "PER_EDUCATE":
			$TITLE = "����֡��";
		break;
		case "PER_TRAINING":
			$TITLE = "���ͺ��/�٧ҹ/������";
		break;
		case "PER_ABILITY":		
			$TITLE = "��������ö�����";
		break;
		case "PER_SPECIAL_SKILL":
			$TITLE = "��������Ǫҭ�����";
		break;
		case "PER_HEIR":		
			$TITLE = "���ҷ";
		break;
		case "PER_ABSENTHIS":		
			$TITLE = "�����";
		break;
		case "PER_PUNISHMENT":		
			$TITLE = "�Թ��";
		break;
		case "PER_SERVICEHIS":		
			$TITLE = "�Ҫ��þ����";
		break;
		case "PER_REWARDHIS":		
			$TITLE = "�����դ����ͺ";
		break;
		case "PER_MARRHIS":		
			$TITLE = "�������";
		break;
		case "PER_NAMEHIS":		
			$TITLE = "�������¹�ŧ����-ʡ��";
		break;
		case "PER_DECORATEHIS":		
			$TITLE = "����ͧ�Ҫ�";
		break;
		case "PER_TIMEHIS" :
			$TITLE = "���ҷ�դٳ";
		break;
		case "PER_WORK_CYCLEHIS" :
			$TITLE = "�ͺ����һ�Ժѵ��Ҫ���";
		break;
		case "PER_WORK_TIME" :
			$TITLE = "���ҡ���һ�Ժѵ��Ҫ���";
		break;
		case "PER_KPI" :
			$TITLE = "kpi ��ºؤ��";
		break;
		case "PER_PERFORMANCE_GOALS" :
			$TITLE = "kpi ������稢ͧ�ҹ";
		break;
		case "PER_COMMAND" :
			$TITLE = "�ѭ��Ṻ���¤����";
		break;
                case "TA_SET_EXCEPTPER" :
			$TITLE = "��˹��ؤ�ŷ������ͧŧ����";
		break;
                case "TA_REQUESTTIME" :
			$TITLE = "�ѹ�֡����ͧ�����ŧ����";
		break;
	} // end switch case
?>