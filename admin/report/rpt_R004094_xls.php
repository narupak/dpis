<?php
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

        require_once '../../Excel/eslip/Classes/PHPExcel.php';
	
        
        ini_set("max_execution_time", 0);
	ini_set("memory_limit","2048M"); 
        
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        $db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        $db_dpis7 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
        $mgt_table='';
        $select_pm_name='';
        $pm_code='';
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
                $mgt_table = " , PER_MGT m";
                $select_pm_name =  " , m.PM_NAME ";
                $pm_code = " and b.PM_CODE=m.PM_CODE(+)";
                $pos_no = "b.POS_NO";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_code = "PL_CODE";
		$line_name = "PL_NAME";
		$line_short_name = "PL_SHORTNAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
		$line_title=" สายงาน";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
                $pos_no = "b.POEM_NO";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "PN_CODE";
		$line_name = "PN_NAME";	
		$line_search_code=trim($search_pn_code);
		$line_search_name =trim($search_pn_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
                $pos_no = "b.POEMS_NO";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "EP_CODE";
		$line_name = "EP_NAME";	
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
                $pos_no = "b.POT_NO";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "TP_CODE";
		$line_name = "TP_NAME";	
		$line_search_code =trim($search_tp_code);
		$line_search_name =trim($search_tp_name);
		$line_title=" ชื่อตำแหน่ง";
	} // end if
	
	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {
		$f_all = true; 
		$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
	} else $f_all = false;	
	
	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
            $REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
            switch($REPORT_ORDER){
                case "MINISTRY" :
                    if($select_list) $select_list .= ", ";
                    $select_list .= "g.ORG_ID_REF as MINISTRY_ID";	

                    if($order_by) $order_by .= ", ";
                     $order_by .= "g.ORG_ID_REF";

                    $heading_name .= " $MINISTRY_TITLE";
                    break;
                case "DEPARTMENT" : 
                    if($select_list) $select_list .= ", ";
                    $select_list .= "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID";

                    if($order_by) $order_by .= ", ";
                    $order_by .= "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID";

                    $heading_name .= " $DEPARTMENT_TITLE";
                    break; 
                case "ORG" :
                    if($select_list) $select_list .= ", ";
                    if($select_org_structure == 0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
                    else if($select_org_structure == 1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

                    if($order_by) $order_by .= ", ";
                    if($select_org_structure == 0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
                    else if($select_org_structure == 1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

                    $heading_name .= " $ORG_TITLE";
                    break;
                case "LEVEL" :
                    if($select_list) $select_list .= ", ";
                    $select_list .= "a.LEVEL_NO, i.LEVEL_NAME";

                    if($order_by) $order_by .= ", ";
                    $order_by .= "a.LEVEL_NO, i.LEVEL_NAME";

                    $heading_name .= " $LEVEL_TITLE";
                    break;       
                case "LINE" :
                    if($select_list) $select_list .= ", ";
                    $select_list .= "$line_code as PL_CODE";

                    if($order_by) $order_by .= ", ";
                    $order_by .= "$line_code";

                    $heading_name .=  $line_title;
                    break;
            } // end switch case
	} // end for
	if(!trim($order_by)){
		if($select_org_structure==0){ $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID"; 	}
		else if($select_org_structure==1){	$order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID"; }
	}
	if(!trim($select_list)){ 
		if($select_org_structure==0){ $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID"; }
		else if($select_org_structure==1){ $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID"; } 
	}

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	if(trim($search_level_no)) $arr_search_condition[] = "(a.LEVEL_NO = '". str_pad($search_level_no, 2, "0", STR_PAD_LEFT) ."')";

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG", $list_type)){
            $list_type_text = "";
            if($select_org_structure==0) {
                if(trim($search_org_id)){ 
                    $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
                    $list_type_text .= "$search_org_name";
                    $R_ORG_ID = "b.ORG_ID";
                } // end if
                if(trim($search_org_id_1)){ 
                    $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
                    $list_type_text .= "$search_org_name_1";
                    $R_ORG_ID = "b.ORG_ID_1";
                } // end if
                if(trim($search_org_id_2)){ 
                    $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
                    $list_type_text .= "$search_org_name_2";
                    $R_ORG_ID = "b.ORG_ID_2";
                } // end if
                if(trim($search_org_id_3)){ 
                    $arr_search_condition[] = "(b.ORG_ID_3 = $search_org_id_3)";
                    $list_type_text .= "$search_org_name_3";
                    $R_ORG_ID = "b.ORG_ID_3";
                } // end if
                if(trim($search_org_id_4)){ 
                    $arr_search_condition[] = "(b.ORG_ID_4 = $search_org_id_4)";
                    $list_type_text .= "$search_org_name_4";
                    $R_ORG_ID = "b.ORG_ID_4";
                } // end if
                if(trim($search_org_id_5)){ 
                    $arr_search_condition[] = "(b.ORG_ID_5 = $search_org_id_5)";
                    $list_type_text .= "$search_org_name_5";
                    $R_ORG_ID = "b.ORG_ID_5";
                } // end if
            }else{
                if(trim($search_org_ass_id)){ 
                    $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
                    $list_type_text .= "$search_org_ass_name";
                    $R_ORG_ID = "a.ORG_ID";
                } // end if
                if(trim($search_org_ass_id_1)){ 
                    $arr_search_condition[] = "(a.ORG_ID_1 = $search_org_ass_id_1)";
                    $list_type_text .= "$search_org_ass_name_1";
                    $R_ORG_ID = "a.ORG_ID_1";
                } // end if
                if(trim($search_org_ass_id_2)){ 
                    $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_ass_id_2)";
                    $list_type_text .= "$search_org_ass_name_2";
                    $R_ORG_ID = "a.ORG_ID_2";
                } // end if
                if(trim($search_org_ass_id_3)){ 
                    $arr_search_condition[] = "(a.ORG_ID_3 = $search_org_ass_id_3)";
                    $list_type_text .= "$search_org_ass_name_3";
                    $R_ORG_ID = "a.ORG_ID_3";
                } // end if
                if(trim($search_org_ass_id_4)){ 
                    $arr_search_condition[] = "(a.ORG_ID_4 = $search_org_ass_id_4)";
                    $list_type_text .= "$search_org_ass_name_4";
                    $R_ORG_ID = "a.ORG_ID_4";
                            } // end if
                if(trim($search_org_ass_id_5)){ 
                    $arr_search_condition[] = "(a.ORG_ID_5 = $search_org_ass_id_5)";
                    $list_type_text .= "$search_org_ass_name_5";
                    $R_ORG_ID = "a.ORG_ID_5";
                } // end if
            }
	}
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
		$list_type_text = "";
		if($line_search_code){
                    $arr_search_condition[] = "(trim(b.$line_code)='$line_search_code')";
                    $list_type_text .= " $line_search_name";
		}
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
                    $search_ct_code = trim($search_ct_code);
                    $arr_search_condition[] = "(trim(c.CT_CODE) = '$search_ct_code')";
                    $list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
                    $search_pv_code = trim($search_pv_code);
                    $arr_search_condition[] = "(trim(c.PV_CODE) = '$search_pv_code')";
                    $list_type_text .= " - $search_pv_name";
		} // end if
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
            if($DEPARTMENT_ID){
                $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
                $list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
            }elseif($MINISTRY_ID){
                $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
                if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                $db_dpis->send_cmd($cmd);
                while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

                $arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
                $list_type_text .= " - $MINISTRY_NAME";
            }elseif($PROVINCE_CODE){
                $PROVINCE_CODE = trim($PROVINCE_CODE);
                $arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
                $list_type_text .= " - $PROVINCE_NAME";
            } // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME";
        $report_title2 = "รายชื่อ$PERSON_TYPE[$search_per_type] ตำแหน่ง สังกัด";
	$report_code = "R0494";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";
        
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $LEVEL_NO, $PL_CODE;
		global $line_code;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
                    $REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
                    switch($REPORT_ORDER){
                        case "MINISTRY" :
                            if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(ORG.ORG_ID_REF = $MINISTRY_ID)";
                            break;
                        case "DEPARTMENT" : 
                            if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
                            break;
                        case "ORG" :	
                            if($select_org_structure==0){
                                if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
                                else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
                            }elseif($select_org_structure==1){
                                if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
                                else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
                            }
                        break;
                        case "LEVEL" :	
                                if($LEVEL_NO) $arr_addition_condition[] = "(trim(a.LEVEL_NO) = '". str_pad($LEVEL_NO, 2, "0", STR_PAD_LEFT) ."')";
                                else $arr_addition_condition[] = "(trim(a.LEVEL_NO) = '' or a.LEVEL_NO is null)";
                        break;
                        case "LINE" :	
                                if(trim($PL_CODE)) $arr_addition_condition[] = "(trim(PL.$line_code) = '$PL_CODE')";
                                else $arr_addition_condition[] = "(trim(PL.$line_code) = '$PL_CODE' or PL.$line_code is null)";
                        break;
                    } // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function

	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $LEVEL_NO, $PL_CODE;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
                    $REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
                    switch($REPORT_ORDER){
                        case "MINISTRY" :
                                $MINISTRY_ID = -1;
                                break;
                        case "DEPARTMENT" : 
                                $DEPARTMENT_ID = -1;
                                break;
                        case "ORG" :	
                                $ORG_ID = -1;
                        break;
                        case "LEVEL" :	
                                $LEVEL_NO = -1;
                        break;
                        case "LINE" :	
                                $PL_CODE = -1;
                        break;
                    } // end switch case
		} // end for
	} // function
        
        function getToFont($id){
		if($id==1){
				$fullname	= 'Angsana';
		}else if($id==2){
				$fullname	= 'Cordia';
		}else if($id==3){
				$fullname	= 'TH SarabunPSK';
		}else{
				$fullname	= 'Browallia';
		}
		return $fullname;
	}
       
        
        
        //====================================================================== setup Excel ===========================================================================
        require_once '../../Excel/eslip/Classes/PHPExcel.php';
        $object = new PHPExcel();
        $CH_PRINT_FONT = 3;
        $font_name = getToFont($CH_PRINT_FONT);
        $object->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
        $object->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A3);
        $object->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $object->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
        $styleArray_head1 = array(
                'font'  => array(
                        'bold'  => TRUE,
                        'size'  => '16',
                        'name'  => $font_name
        ));
        $styleArray_head_com = array(
                'font'  => array(
                        'bold'  => FALSE,
                        'size'  => '10',
                        'name'  => $font_name
        ));
        $styleArray_head2 = array(
                'font'  => array(
                        'bold'  => TRUE,
                        'size'  => '14',
                        'name'  => $font_name
        ));

        $styleArray_head = array(
                'font'  => array(
                        'bold'  => FALSE,
                        'size'  => '14',
                        'name'  => $font_name
        ));
        $styleArray_body = array(
                'font'  => array(
                        'bold'  => FALSE,
                        'size'  => '14',
                        'name'  => $font_name
         ));
        //set border header
        $styleArray=array(
            'borders'=>array(
                'allborders'=>array(
                        'style'=>PHPExcel_Style_Border::BORDER_THIN
                                )
                        )
        );
        //====================================================================== End setup Excel =========================================================================== 
        
	if($DPISDB=="oci8"){
            $search_condition = str_replace(" where ", " and ", $search_condition);
            //if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
            $cmd =  "   with person as (
                            SELECT TRIM(L.LEVEL_SEQ_NO) AS XX ,A.PER_ID , PN.PN_NAME||A.PER_NAME||' '||A.PER_SURNAME AS FULLNAME , PL.$line_name AS PL_NAME ,
                                L.POSITION_LEVEL , L.POSITION_TYPE,
                                $pos_no AS POS_NO, ORG.ORG_NAME , ORG_1.ORG_NAME AS ORG_NAME_1, ORG_2.ORG_NAME AS ORG_NAME_2, 
                                ORG_3.ORG_NAME AS ORG_NAME_3 , ORG_4.ORG_NAME AS ORG_NAME_4 , ORG_5.ORG_NAME AS ORG_NAME_5 ,
                                A.PER_TYPE ,L.LEVEL_NAME as LEVEL_POTION 
                                $select_pm_name 
                            FROM PER_PERSONAL a , PER_PRENAME PN , $position_table b , $line_table PL , PER_LEVEL L, PER_ORG ORG, PER_ORG ORG_1,
                                PER_ORG ORG_2 ,PER_ORG ORG_3 ,PER_ORG ORG_4 ,PER_ORG ORG_5 $mgt_table
                            WHERE A.PN_CODE=PN.PN_CODE(+)
                            AND A.PER_ID IN (SELECT PER_ID FROM PER_LICENSEHIS)
                            AND $position_join(+)
                            AND  b.$line_code=pl.$line_code(+)
                            AND A.LEVEL_NO=L.LEVEL_NO(+)
                            AND b.ORG_ID=ORG.ORG_ID(+)
                            AND b.ORG_ID_1=ORG_1.ORG_ID(+)
                            AND b.ORG_ID_2=ORG_2.ORG_ID(+)
                            AND b.ORG_ID_3=ORG_3.ORG_ID(+)
                            AND b.ORG_ID_4=ORG_4.ORG_ID(+)
                            AND b.ORG_ID_5=ORG_5.ORG_ID(+)
                            $pm_code
                            $search_condition
                      ),LICENSEHIS_1 AS (
                            SELECT LCH.LH_ID,LCH.LT_CODE,LCH.PER_ID,LCT.LT_NAME, LCH.LH_SUB_TYPE , LCH.LH_MAJOR , LCH.LH_LICENSE_NO , LCH.LH_SEQ_NO , LCH.LH_LICENSE_DATE ,
                                LCH.LH_EXPIRE_DATE
                            FROM PER_LICENSEHIS LCH , PER_LICENSE_TYPE LCT
                            WHERE LCH.LT_CODE=LCT.LT_CODE(+)
                            AND (LCH.LH_EXPIRE_DATE IS NULL  OR TO_DATE(LCH.LH_EXPIRE_DATE, 'YYYY-MM-DD') > SYSDATE)
                      ),LICENSEHIS_2 AS (
                            SELECT LCH.LH_ID,LCH.LT_CODE,LCH.PER_ID,LCT.LT_NAME, LCH.LH_SUB_TYPE , LCH.LH_MAJOR , LCH.LH_LICENSE_NO , LCH.LH_SEQ_NO , LCH.LH_LICENSE_DATE ,
                                LCH.LH_EXPIRE_DATE
                            FROM PER_LICENSEHIS LCH , PER_LICENSE_TYPE LCT
                            WHERE LCH.LT_CODE=LCT.LT_CODE(+)
                            AND (TO_DATE(LCH.LH_EXPIRE_DATE, 'YYYY-MM-DD') < SYSDATE)
                      ),CUR_LICENSEHIS AS (
                            SELECT  P.ID_PERSON AS PER_ID,
                                CASE WHEN (P.PER_ID IS NOT NULL) THEN P.LH_ID
                                    ELSE (SELECT  A.LH_ID  FROM (  SELECT  ROWNUM, A.PER_ID , A.LH_ID  FROM LICENSEHIS_2 A  ORDER BY A.LH_LICENSE_DATE DESC  ) A WHERE A.PER_ID = P.ID_PERSON AND ROWNUM = 1 )
                                    END AS LH_ID,
                                CASE WHEN (P.PER_ID IS NOT NULL) THEN P.LT_CODE
                                    ELSE (SELECT  A.LT_CODE  FROM (  SELECT  ROWNUM, A.PER_ID , A.LT_CODE  FROM LICENSEHIS_2 A  ORDER BY A.LH_LICENSE_DATE DESC  ) A WHERE A.PER_ID = P.ID_PERSON AND ROWNUM < 2)
                                    END AS LT_CODE,
                                CASE WHEN (P.PER_ID IS NOT NULL) THEN P.LT_NAME
                                    ELSE (SELECT  A.LT_NAME  FROM (  SELECT  ROWNUM, A.PER_ID , A.LT_NAME  FROM LICENSEHIS_2 A  ORDER BY A.LH_LICENSE_DATE DESC  ) A WHERE A.PER_ID = P.ID_PERSON AND ROWNUM < 2)
                                    END AS LT_NAME,
                                CASE WHEN (P.PER_ID IS NOT NULL) THEN P.LH_SUB_TYPE
                                    ELSE (SELECT  A.LH_SUB_TYPE  FROM (  SELECT  ROWNUM, A.PER_ID , A.LH_SUB_TYPE  FROM LICENSEHIS_2 A  ORDER BY A.LH_LICENSE_DATE DESC  ) A WHERE A.PER_ID = P.ID_PERSON AND ROWNUM < 2)
                                    END AS LH_SUB_TYPE,
                                CASE WHEN (P.PER_ID IS NOT NULL) THEN P.LH_MAJOR
                                    ELSE (SELECT  A.LH_MAJOR  FROM (  SELECT  ROWNUM, A.PER_ID , A.LH_MAJOR  FROM LICENSEHIS_2 A  ORDER BY A.LH_LICENSE_DATE DESC  ) A WHERE A.PER_ID = P.ID_PERSON AND ROWNUM < 2)
                                    END AS LH_MAJOR,
                                CASE WHEN (P.PER_ID IS NOT NULL) THEN P.LH_LICENSE_NO
                                    ELSE (SELECT  A.LH_LICENSE_NO  FROM (  SELECT  ROWNUM, A.PER_ID , A.LH_LICENSE_NO  FROM LICENSEHIS_2 A  ORDER BY A.LH_LICENSE_DATE DESC  ) A WHERE A.PER_ID = P.ID_PERSON AND ROWNUM < 2)
                                    END AS LH_LICENSE_NO,
                                CASE WHEN (P.PER_ID IS NOT NULL) THEN P.LH_SEQ_NO
                                    ELSE (SELECT  A.LH_SEQ_NO  FROM (  SELECT  ROWNUM, A.PER_ID , A.LH_SEQ_NO  FROM LICENSEHIS_2 A  ORDER BY A.LH_LICENSE_DATE DESC  ) A WHERE A.PER_ID = P.ID_PERSON AND ROWNUM < 2)
                                    END AS LH_SEQ_NO,
                                CASE WHEN (P.PER_ID IS NOT NULL) THEN P.LH_LICENSE_DATE
                                    ELSE (SELECT  A.LH_LICENSE_DATE  FROM (  SELECT  ROWNUM, A.PER_ID , A.LH_LICENSE_DATE  FROM LICENSEHIS_2 A  ORDER BY A.LH_LICENSE_DATE DESC  ) A WHERE A.PER_ID = P.ID_PERSON AND ROWNUM < 2)
                                    END AS LH_LICENSE_DATE,
                                CASE WHEN (P.PER_ID IS NOT NULL) THEN P.LH_EXPIRE_DATE
                                    ELSE (SELECT  A.LH_EXPIRE_DATE  FROM (  SELECT  ROWNUM, A.PER_ID , A.LH_EXPIRE_DATE  FROM LICENSEHIS_2 A  ORDER BY A.LH_LICENSE_DATE DESC  ) A WHERE A.PER_ID = P.ID_PERSON AND ROWNUM < 2)
                                    END AS LH_EXPIRE_DATE
                            FROM (
                                SELECT A.PER_ID AS ID_PERSON ,B.* FROM PER_PERSONAL A , LICENSEHIS_1 B
                                WHERE A.PER_ID IN (SELECT PER_ID FROM PER_LICENSEHIS)
                                AND A.PER_ID=B.PER_ID(+)
                            ) P
                      ),OLD_LICENSEHIS AS (
                          SELECT  LCH.LH_ID AS LH_ID_OLD ,LCH.LT_CODE AS LT_CODE_OLD ,LCH.PER_ID  AS PER_ID_OLD, LCT.LT_NAME AS LT_NAME_OLD, LCH.LH_SUB_TYPE AS  LH_SUB_TYPE_OLD,
                          LCH.LH_MAJOR AS LH_MAJOR_OLD , LCH.LH_LICENSE_NO AS LH_LICENSE_NO_OLD , LCH.LH_SEQ_NO AS LH_SEQ_NO_OLD ,
                          LCH.LH_LICENSE_DATE AS LH_LICENSE_DATE_OLD ,LCH.LH_EXPIRE_DATE AS LH_EXPIRE_DATE_OLD
                          FROM PER_LICENSEHIS LCH , PER_LICENSE_TYPE LCT
                          WHERE LCH.LT_CODE=LCT.LT_CODE(+)
                          AND (TO_DATE(LCH.LH_EXPIRE_DATE, 'YYYY-MM-DD') < SYSDATE)
                          AND LCH.PER_ID IN (SELECT A.PER_ID FROM CUR_LICENSEHIS A WHERE A.LT_CODE = LCH.LT_CODE AND A.LH_LICENSE_NO=LCH.LH_LICENSE_NO AND A.LH_ID != LCH.LH_ID)
                      ),ALL_LICENSEHIS AS (
                            SELECT A.*,B.* FROM
                                CUR_LICENSEHIS A ,
                                OLD_LICENSEHIS B
                            WHERE A.PER_ID=B.PER_ID_OLD(+)
                            AND A.LT_CODE=B.LT_CODE_OLD(+)
                      ) SELECT PS.* ,HIS.* FROM PERSON PS , ALL_LICENSEHIS HIS
                        WHERE PS.PER_ID=HIS.PER_ID
                        ORDER BY XX DESC , PS.POS_NO ASC, HIS.LH_LICENSE_DATE DESC , LH_EXPIRE_DATE DESC ";
        }
//        ini_set('display_errors', 1);
//        ini_set('display_startup_errors', 1);
//        error_reporting(E_ALL);
        if($select_org_structure==1) { 
            $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
            $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
        
        $count_data = $db_dpis->send_cmd($cmd);
        //	$db_dpis->show_error();
        //      echo '<pre>'.$cmd;
        //      die();
        if($count_data){
            $xls_fRow=4;
            $xlsRow=4;
            $xlsRow2=$xlsRow+1;
            $cnt = 0;
            //set Header 
            $array_header_col1 = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
            //$array_header_width = array('6','24','29','20','8','20','20','20','20','20','20','30','25','5','14','12','16','11','30','25','6','14');
            $h_col1 = array("ลำดับที่");
            $h_col2 = array("ชื่อ - สกุล");
            $h_col3 = array("ตำแหน่ง");
            $h_col4 = array("ตำแหน่งประเภท");
            $h_col5 = array("เลขที่\nตำแหน่ง");
            $h_col11 = array("");
            $h_col1_cnt = array(1);

            //die($ORG_SETLEVEL);
            if($ORG_SETLEVEL==3){
                    $header_first_setlevel3 = array('สำนัก/กอง','ต่ำกว่าสำนัก/กอง 1 ระดับ','ต่ำกว่าสำนัก/กอง 2 ระดับ','ต่ำกว่าสำนัก/กอง 3 ระดับ');
                    $header_first2_setlevel3 = array_merge($h_col11,$h_col11,$h_col11,$h_col11);
                    $count_header_first_setlevel3 = array_merge($h_col1_cnt,$h_col1_cnt,$h_col1_cnt,$h_col1_cnt);
            }else if($ORG_SETLEVEL==4){
                    $header_first_setlevel3 = array('สำนัก/กอง','ต่ำกว่าสำนัก/กอง 1 ระดับ','ต่ำกว่าสำนัก/กอง 2 ระดับ','ต่ำกว่าสำนัก/กอง 3 ระดับ','ต่ำกว่าสำนัก/กอง 4 ระดับ');
                    $header_first2_setlevel3 = array_merge($h_col11,$h_col11,$h_col11,$h_col11,$h_col11);
                    $count_header_first_setlevel3 = array_merge($h_col1_cnt,$h_col1_cnt,$h_col1_cnt,$h_col1_cnt,$h_col1_cnt);
            }else if($ORG_SETLEVEL==5){
                    $header_first_setlevel3 = array('สำนัก/กอง','ต่ำกว่าสำนัก/กอง 1 ระดับ','ต่ำกว่าสำนัก/กอง 2 ระดับ','ต่ำกว่าสำนัก/กอง 3 ระดับ','ต่ำกว่าสำนัก/กอง 4 ระดับ','ต่ำกว่าสำนัก/กอง 5 ระดับ');
                    $header_first2_setlevel3 = array_merge($h_col11,$h_col11,$h_col11,$h_col11,$h_col11,$h_col11);
                    $count_header_first_setlevel3 = array_merge($h_col1_cnt,$h_col1_cnt,$h_col1_cnt,$h_col1_cnt,$h_col1_cnt,$h_col1_cnt);
            }else {
                    $header_first_setlevel3 = array('สำนัก/กอง','ต่ำกว่าสำนัก/กอง 1 ระดับ','ต่ำกว่าสำนัก/กอง 2 ระดับ');
                    $header_first2_setlevel3 = array_merge($h_col11,$h_col11,$h_col11);
                    $count_header_first_setlevel3 = array_merge($h_col1_cnt,$h_col1_cnt,$h_col1_cnt);
            }

            $header_before_last = array ("ใบอนุญาตประกอบวิชาชีพปัจจุบัน");
            $header_last = array ("ใบอนุญาตประกอบวิชาชีพก่อนหน้า");
            $header_before_last2 = array("ชื่อใบประกอบวิชาชีพ", "ประเภท/ระดับของใบอนุญาต", "สาขา", "เลขที่ใบอนุญาต", "ต่ออายุครั้งที่", "วันที่ออกใบอนุญาต", "วันที่หมดอายุ");
            $header_last2 =  array("ชื่อใบประกอบวิชาชีพ", "ประเภท/ระดับของใบอนุญาต", "สาขา", "เลขที่ใบอนุญาต", "ต่ออายุครั้งที่", "วันที่ออกใบอนุญาต", "วันที่หมดอายุ");
            $count_header_before_last = array(count($header_before_last2));
            $count_header_last = array(count($header_last2));

            $merge_name_h = array_merge($h_col1,$h_col2,$h_col3,$h_col4,$h_col5,$header_first_setlevel3,$header_before_last,$header_last);
            $merge_name_h2 = array_merge($h_col11,$h_col11,$h_col11,$h_col11,$h_col11,$header_first_setlevel3,$header_before_last2,$header_last2);
            $arr_count_h = array_merge($h_col1_cnt,$h_col1_cnt,$h_col1_cnt,$h_col1_cnt,$h_col1_cnt,$count_header_first_setlevel3,$count_header_before_last,$count_header_last);
            $h_name_array = $merge_name_h;
            $h_name_array2 = $merge_name_h2;

            //================================================loop header =============================================================

            for ($idx=0; $idx < count($arr_count_h); $idx++) { 
                for ($i=0; $i < ($arr_count_h[$idx]); $i++) { 
                    if($i==0){
                            $col1 = $array_header_col1[$cnt];
                    }
                    if($i==($arr_count_h[$idx]-1)){
                       $col2 = $array_header_col1[$cnt]; 
                    }
                    $cnt++;
                }
                if($idx == count($h_name_array)-2 || $idx == count($h_name_array)-1){
                        $object->setActiveSheetIndex(0)->mergeCells($col1.$xlsRow.':'.$col2.$xlsRow)->setCellValue($col1.$xlsRow, @iconv("tis-620", "utf-8",$h_name_array[$idx]) )->getStyle($col1.$xlsRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                }else if($idx == 4){
                        $object->setActiveSheetIndex(0)->mergeCells($col1.$xlsRow.':'.$col1.$xlsRow2)->setCellValue($col1.$xlsRow, @iconv("tis-620", "utf-8",$h_name_array[$idx]) )->getStyle($col1.$xlsRow)->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                }else{
                        $object->setActiveSheetIndex(0)->mergeCells($col1.$xlsRow.':'.$col1.$xlsRow2)->setCellValue($col1.$xlsRow, @iconv("tis-620", "utf-8",$h_name_array[$idx]) )->getStyle($col1.$xlsRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                }
                $end_col = $col2;
            }
            $object->getActiveSheet()->getRowDimension($xlsRow)->setRowHeight(18);
            for($i=0;$i< count($h_name_array2);$i++){
                if($i==0 || $i==4 || $i==count($h_name_array2)-5 || $i==count($h_name_array2)-12){
                    $object->getActiveSheet()->getColumnDimension($array_header_col1[$i])->setWidth('8')->setAutoSize(FALSE);
                }else if($i==count($h_name_array2)-3 || $i==count($h_name_array2)-10){
                    $object->getActiveSheet()->getColumnDimension($array_header_col1[$i])->setWidth('10')->setAutoSize(FALSE);
                }else{
                    $object->getActiveSheet()->getColumnDimension($array_header_col1[$i])->setWidth('25')->setAutoSize(FALSE);
                }    
                //$object->getActiveSheet()->getColumnDimension($array_header_col1[$i])->setAutoSize(TRUE);
                $object->setActiveSheetIndex(0)->setCellValue($array_header_col1[$i].$xlsRow2 , @iconv("tis-620", "utf-8",$h_name_array2[$i]) )->getStyle($array_header_col1[$i].$xlsRow2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $end_col = $array_header_col1[$i];
            }
            $object->getActiveSheet()->getStyle('A'.$xls_fRow.':'.$end_col.$xlsRow2)->applyFromArray($styleArray_head2);
            //die($xls_fRow.'xx'.$xlsRow2);
            //set bg header color
            $object->getActiveSheet()->getStyle('A'.$xls_fRow.':'.$end_col.$xlsRow2)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF');
            $object->getActiveSheet()->getStyle('A'.$xls_fRow.':'.$end_col.$xlsRow2)->applyFromArray($styleArray);
            //================================================ F Header  ==================================================================
            $object->getActiveSheet()->getStyle('A1:'.$end_col.'2')->applyFromArray($styleArray_head1);
            $object->getActiveSheet()->getStyle('A3:'.$end_col.'3')->applyFromArray($styleArray_head_com);
            $object->setActiveSheetIndex(0)->mergeCells('A1:'.$end_col.'1')->setCellValue('A1', @iconv('TIS-620','UTF-8',$report_title))->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $object->setActiveSheetIndex(0)->mergeCells('A2:'.$end_col.'2')->setCellValue('A2', @iconv('TIS-620','UTF-8',$report_title2))->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $object->setActiveSheetIndex(0)->mergeCells('A3:'.$end_col.'3')->setCellValue('A3', @iconv('TIS-620','UTF-8',$company_name))->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            //================================================ End F Header  ==================================================================
            
            $data_frow = $xlsRow2+1;
            $data_count = $xlsRow+1;
            $data_count_seq=1;
            $PER_ID_OLD=-1;
            $xlsRow=$xlsRow2;
            //var_dump($db_dpis->get_array());
            
            while($data = $db_dpis->get_array()){
                $data_count++;
                $PER_ID = $data[PER_ID];
                $PER_FULLNAME = $data[FULLNAME];
                $PL_NAME = trim($data[PL_NAME]);
                $PER_TYPE = trim($data[PER_TYPE]);
                $POSITION_LEVEL = trim($data[POSITION_LEVEL]);
                $POSITION_TYPE = trim($data[POSITION_TYPE]);
                $LEVEL_POTION = trim($data[LEVEL_POTION]);
                $PM_NAME =  trim($data[PM_NAME]);
                
                if($PER_TYPE==3){
                        //$POSITION_TYPE = $POSITION_LEVEL;
                        $POSITION_TYPE = $LEVEL_POTION;
                        $POSITION_LEVEL = "";
                }
                
                if($PM_NAME){ $PL_NAME = $PM_NAME ."(".$PL_NAME.")";}else{$PL_NAME = $PL_NAME.$POSITION_LEVEL;}
                $POS_NO = trim($data[POS_NO]);
                $ORG_NAME = trim($data[ORG_NAME]);
                $ORG_NAME_1 = trim($data[ORG_NAME_1]);
                $ORG_NAME_2 = trim($data[ORG_NAME_2]);
                $ORG_NAME_3 = trim($data[ORG_NAME_3]);
                $ORG_NAME_4 = trim($data[ORG_NAME_4]);
                $ORG_NAME_5 = trim($data[ORG_NAME_5]);
                
                $LH_ID = trim($data[LH_ID]);
                $LT_CODE = trim($data[LT_CODE]);
                $LT_NAME = trim($data[LT_NAME]);
                $LH_SUB_TYPE = trim($data[LH_SUB_TYPE]);
                $LH_MAJOR = trim($data[LH_MAJOR]);
                $LH_LICENSE_NO = trim($data[LH_LICENSE_NO]);
                $LH_SEQ_NO = trim($data[LH_SEQ_NO]);
                $LH_LICENSE_DATE = (trim($data[LH_LICENSE_DATE]))?show_date_format(trim($data[LH_LICENSE_DATE]), $DATE_DISPLAY):'';
                $LH_EXPIRE_DATE = (trim($data[LH_EXPIRE_DATE]))?show_date_format(trim($data[LH_EXPIRE_DATE]), $DATE_DISPLAY):'';
                
                $LH_ID_OLD = trim($data[LH_ID_OLD]);
                $LT_CODE_OLD = trim($data[LT_CODE_OLD]);
                $LT_NAME_OLD = trim($data[LT_NAME_OLD]);
                $LH_SUB_TYPE_OLD = trim($data[LH_SUB_TYPE_OLD]);
                $LH_MAJOR_OLD = trim($data[LH_MAJOR_OLD]);
                $LH_LICENSE_NO_OLD = trim($data[LH_LICENSE_NO_OLD]);
                $LH_SEQ_NO_OLD = trim($data[LH_SEQ_NO_OLD]);
                $LH_LICENSE_DATE_OLD = (trim($data[LH_LICENSE_DATE_OLD]))?show_date_format(trim($data[LH_LICENSE_DATE_OLD]), $DATE_DISPLAY):'';
                $LH_EXPIRE_DATE_OLD = (trim($data[LH_EXPIRE_DATE_OLD]))?show_date_format(trim($data[LH_EXPIRE_DATE_OLD]), $DATE_DISPLAY):'';
               
                $data_col1 = array($data_count_seq);//array("ลำดับที่");
                $data_col2 = array($PER_FULLNAME);//array("ชื่อ - สกุล");
                $data_col3 = array($PL_NAME);//array("ตำแหน่ง");
                $data_col4 = array($POSITION_TYPE);//array("ตำแหน่งประเภท");
                $data_col5 = array($POS_NO);//array("เลขที่\nตำแหน่ง");
                
                if($ORG_SETLEVEL==3){
                    $data_first_setlevel3 = array($ORG_NAME,$ORG_NAME_1,$ORG_NAME_2,$ORG_NAME_3);
                    $data_first_setlevel3_null = array('','','','');
                }else if($ORG_SETLEVEL==4){
                    $data_first_setlevel3 = array($ORG_NAME,$ORG_NAME_1,$ORG_NAME_2,$ORG_NAME_3,$ORG_NAME_4);
                    $data_first_setlevel3_null = array('','','','','');
                }else if($ORG_SETLEVEL==5){
                    $data_first_setlevel3 = array($ORG_NAME,$ORG_NAME_1,$ORG_NAME_2,$ORG_NAME_3,$ORG_NAME_4,$ORG_NAME_5);
                    $data_first_setlevel3_null = array('','','','','','');
                }else {
                    $data_first_setlevel3 = array($ORG_NAME,$ORG_NAME_1,$ORG_NAME_2);
                    $data_first_setlevel3_null = array('','','');
                }

                $data_before_last = array($LT_NAME,$LH_SUB_TYPE, $LH_MAJOR, $LH_LICENSE_NO, $LH_SEQ_NO, $LH_LICENSE_DATE, $LH_EXPIRE_DATE);
                $data_last =  array($LT_NAME_OLD,$LH_SUB_TYPE_OLD, $LH_MAJOR_OLD, $LH_LICENSE_NO_OLD, $LH_SEQ_NO_OLD, $LH_LICENSE_DATE_OLD, $LH_EXPIRE_DATE_OLD);

                if($PER_ID!=$PER_ID_OLD){
                    $PER_ID_OLD=$PER_ID;
                    $array_merge_data =  array_merge($data_col1,$data_col2,$data_col3,$data_col4,$data_col5,$data_first_setlevel3,$data_before_last,$data_last);
                    $data_count_seq++;
                }else{
                    $data_col11 = array("");
                    $array_merge_data =  array_merge($data_col11,$data_col11,$data_col11,$data_col11,$data_col11,$data_first_setlevel3_null,$data_before_last,$data_last);
                } 
                
                $end_row = 0;
                $xlsRow = $data_count;
                for($i=0;$i<count($h_name_array2);$i++){
                    $object->getActiveSheet()->setCellValue($array_header_col1[$i].$xlsRow, @iconv('TIS-620','UTF-8',"$array_merge_data[$i]"));
                    $end_row = $xlsRow;	
                }
            } // end while
            $object->getActiveSheet()->getStyle('B'.$data_frow.':'.$end_col.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $object->getActiveSheet()->getStyle('A'.$data_frow.':A'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $object->getActiveSheet()->getStyle('E'.$data_frow.':E'.$end_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $object->getActiveSheet()->getStyle('A'.$data_frow.':'.$end_col.$end_row)->applyFromArray($styleArray);
            $object->getActiveSheet()->getStyle('A'.$data_frow.':'.$end_col.$end_row)->applyFromArray($styleArray_body);
            $object->getActiveSheet()->getStyle('A'.$data_frow.':'.$end_col.$end_row)->getFill()
                                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                    ->getStartColor()->setARGB('FFFFFF');
        }else{
		//ไม่มีข้อมูล
		$object->getActiveSheet()->getStyle('A1:M2')->applyFromArray($styleArray_head);
		$object->setActiveSheetIndex(0)->mergeCells('A1:M1')->setCellValue('A1', @iconv('TIS-620','UTF-8','**** ไม่พบข้อมูล ****'))->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	} // end if
	$filename='R0494.xlsx'; //save our workbook as this file name
        //header('Content-Type: application/vnd.ms-excel'); //mime type
        //die();
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        header('content-type: application/octet-stream');
        header('Pragma: no-cache');
        header('Expires: 0');
        $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        $objWriter->save('php://output');
	