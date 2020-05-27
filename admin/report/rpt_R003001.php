<?php

include("../../php_scripts/connect_database.php");
if (!$FLAG_RTF) {
    include("../php_scripts/pdf_wordarray_thaicut.php");
}
include("../../php_scripts/calendar_data.php");
if ($export_type == "report") {
    include ("../php_scripts/function_share.php");
} else if ($export_type == "graph") {
    include ("../../admin//php_scripts/function_share.php"); //เงื่อนไขที่ต้องการแสดงผล
}

if ($FLAG_RTF) {
    include ("rtf_setvar.php"); // กำหนดตัวแปรค่าสี set_of_colors
    require("../../RTF/rtf_class.php");
} else {
    define('FPDF_FONTPATH', '../../PDF/font/');
    include ("../../PDF/fpdf.php");
    include ("../../PDF/pdf_extends_DPIS.php");
}
$DEPARTMENT_ID_ORI = $DEPARTMENT_ID;
ini_set("max_execution_time", $max_execution_time);

$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

if ($_GET[NUMBER_DISPLAY]) {
    $NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
}
if ($search_per_type == 1 || $search_per_type == 5) {
    $line_table = "PER_LINE";
    $line_code = "e.PL_CODE";
    $line_name = "e.PL_NAME";
    $line_short_name = "PL_SHORTNAME";
    $line_search_code = trim($search_pl_code);
    $line_search_name = trim($search_pl_name);
    //$line_seq="f.PL_SEQ_NO"; /*เดิม*/
    $line_seq = "e.POH_SEQ_NO"; //Release 5.2.1.8
} elseif ($search_per_type == 2) {
    $line_table = "PER_POS_NAME";
    $line_code = "e.PN_CODE";
    $line_name = "e.PN_NAME";
    $line_search_code = trim($search_pn_code);
    $line_search_name = trim($search_pn_name);
    //$line_seq="f.PN_SEQ_NO";/*เดิม*/
    $line_seq = "e.POH_SEQ_NO"; //Release 5.2.1.8
} elseif ($search_per_type == 3) {
    $line_table = "PER_EMPSER_POS_NAME";
    $line_code = "e.EP_CODE";
    $line_name = "e.EP_NAME";
    $line_search_code = trim($search_ep_code);
    $line_search_name = trim($search_ep_name);
    //$line_seq="f.EP_SEQ_NO";/*เดิม*/
    $line_seq = "e.POH_SEQ_NO"; //Release 5.2.1.8
} elseif ($search_per_type == 4) {
    $line_table = "PER_TEMP_POS_NAME";
    $line_code = "e.TP_CODE";
    $line_name = "e.TP_NAME";
    $line_search_code = trim($search_tp_code);
    $line_search_name = trim($search_tp_name);
    //$line_seq="f.TP_SEQ_NO";/*เดิม*/
    $line_seq = "e.POH_SEQ_NO"; //Release 5.2.1.8
} // end if

if (in_array("ALL", $list_type)) { //if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  { เงื่อนไขเดิมไม่แสดงกระทรวงหากมีการเลือกกรม ทำให้การนับข้อมูลไม่ถูกต้อง
    $f_all = true;
    $RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
} else {
    $f_all = false;
}

if (!trim($RPTORD_LIST)) {
    $RPTORD_LIST = "ORG|";
    if (in_array("PER_ORG", $list_type) && trim($search_org_id_1) == "" && trim($search_org_id_2) == "") {
        $RPTORD_LIST .= "ORG_1|";
    }
    if (in_array("PER_ORG", $list_type) && trim($search_org_id_1) != "" && trim($search_org_id_2) == "") {
        $RPTORD_LIST .= "ORG_2|";
    }
    if (in_array("PER_ORG", $list_type) && trim($search_org_id_1) != "" && trim($search_org_id_2) != "") {
        $RPTORD_LIST .= "LINE|";
    }
    if (in_array("PER_COUNTRY", $list_type) && trim($search_pv_code) != "") {
        $RPTORD_LIST .= "PROVINCE|";
    }
} // end if
$arr_rpt_order_setnew = explode("|", $RPTORD_LIST);
$arr_rpt_order_tmp_setnew = array_unique($arr_rpt_order_setnew); //ตัดค่าที่ซ้ำกันออก เพื่อไม่ให้ขึ้นข้อมูลซ้ำกัน 2 แถว	และเรียงตำแหน่ง index ใหม่ 0 1 2 ...
foreach ($arr_rpt_order_tmp_setnew as $key => $value) {
    $arr_rpt_order[] = $value;
}
unset($arr_rpt_order_setnew);
unset($arr_rpt_order_tmp_setnew);
//print_r($arr_rpt_order);
$select_list = "";
$order_by = "";
$heading_name = "";
for ($rpt_order_index = 0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++) {
    $REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
    switch ($REPORT_ORDER) {
        case "MINISTRY" :
            if ($select_list) {
                $select_list .= ", ";
            }
            $select_list .= "e.POH_ORG1 as MINISTRY_ID";

            if ($order_by) {
                $order_by .= ", ";
            }
            $order_by .= "e.POH_ORG1";

            $heading_name .= " $MINISTRY_TITLE";
            break;
        case "DEPARTMENT" :
            if ($select_list) {
                $select_list .= ", ";
            }
            $select_list .= "e.POH_ORG2 as DEPARTMENT_ID";

            if ($order_by) {
                $order_by .= ", ";
            }
            $order_by .= "e.POH_ORG2";

            $heading_name .= " $DEPARTMENT_TITLE";
            break;
        case "ORG" :
            if ($select_list) {
                $select_list .= ", ";
            }
            if ($select_org_structure == 0) {
                $select_list .= "e.POH_ORG3 as ORG_ID";
            } elseif ($select_org_structure == 1) {
                $select_list .= "a.ORG_ID";
            }

            if ($order_by) {
                $order_by .= ", ";
            }
            if ($select_org_structure == 0) {
                $order_by .= "e.POH_ORG3";
            } elseif ($select_org_structure == 1) {
                $order_by .= "a.ORG_ID";
            }

            $heading_name .= " $ORG_TITLE";
            break;
        case "ORG_1" :
            if ($select_list) {
                $select_list .= ", ";
            }
            if ($select_org_structure == 0) {
                $select_list .= "e.POH_UNDER_ORG1 as ORG_ID_1";
            } elseif ($select_org_structure == 1) {
                $select_list .= "a.ORG_ID_1";
            }

            if ($order_by) {
                $order_by .= ", ";
            }
            if ($select_org_structure == 0) {
                $order_by .= "e.POH_UNDER_ORG1";
            } elseif ($select_org_structure == 1) {
                $order_by .= "a.ORG_ID_1";
            }

            $heading_name .= " $ORG_TITLE1";
            break;
        case "ORG_2" :
            if ($select_list) {
                $select_list .= ", ";
            }
            if ($select_org_structure == 0) {
                $select_list .= "e.POH_UNDER_ORG2 as ORG_ID_2";
            } elseif ($select_org_structure == 1) {
                $select_list .= "a.ORG_ID_2";
            }

            if ($order_by) {
                $order_by .= ", ";
            }
            if ($select_org_structure == 0) {
                $order_by .= "e.POH_UNDER_ORG2";
            } elseif ($select_org_structure == 1) {
                $order_by .= "a.ORG_ID_2";
            }

            $heading_name .= " $ORG_TITLE2";
            break;
        case "LINE" :
            if ($select_list) {
                $select_list .= ", ";
            }
            // เดิม $select_list .= "$line_seq, $line_code as PL_CODE"; มี SEQ_NO ทำให้ข้อมูลตำแหน่งในสายงานขึ้นซ้ำ จึงตัดออก
            $select_list .= "$line_code as PL_CODE";

            if ($order_by) {
                $order_by .= ", ";
            }
            // เดิม $order_by .= "$line_seq, $line_code";
            $order_by .= " $line_code";

            $heading_name .= $line_title;
            break;
        case "LEVEL" :
            if ($select_list) {
                $select_list .= ", ";
            }
            $select_list .= "a.LEVEL_NO, i.LEVEL_NAME";

            if ($order_by) {
                $order_by .= ", ";
            }
            $order_by .= "a.LEVEL_NO, i.LEVEL_NAME";

            $heading_name .= " $LEVEL_TITLE";
            break;
        case "SEX" :
            if ($select_list) {
                $select_list .= ", ";
            }
            $select_list .= "a.PER_GENDER";

            if ($order_by) {
                $order_by .= ", ";
            }
            $order_by .= "a.PER_GENDER";

            $heading_name .= " $SEX_TITLE";
            break;
        case "PROVINCE" :
            if ($select_list) {
                $select_list .= ", ";
            }
            $select_list .= "a.PV_CODE";

            if ($order_by) {
                $order_by .= ", ";
            }
            $order_by .= "a.PV_CODE";

            $heading_name .= " $PV_TITLE";
            break;
        case "EDUCLEVEL" :
            if ($select_list) {
                $select_list .= ", ";
            }
            $select_list .= "d.EL_CODE";

            if ($order_by) {
                $order_by .= ", ";
            }
            $order_by .= "d.EL_CODE";

            $heading_name .= " $EL_TITLE";
            break;
        case "EDUCMAJOR" :
            if ($select_list) {
                $select_list .= ", ";
            }
            $select_list .= "d.EM_CODE";

            if ($order_by) {
                $order_by .= ", ";
            }
            $order_by .= "d.EM_CODE";

            $heading_name .= " $EM_TITLE";
            break;
    } // end switch case
} // end for
if (!trim($order_by)) {
    if ($select_org_structure == 0) {
        $order_by = "e.POH_ORG3";
    } else if ($select_org_structure == 1) {
        $order_by = "a.ORG_ID";
    }
}
if (!trim($select_list)) {
    if ($select_org_structure == 0) {
        $select_list = "e.POH_ORG3";
    } else if ($select_org_structure == 1) {
        $select_list = "a.ORG_ID";
    }
}

$search_condition = "";
$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
$arr_search_condition[] = "(a.PER_STATUS in (" . implode(", ", $search_per_status) . "))";   //search_per_status  เงื่อนไขที่ถูกส่งมา

$list_type_text = $ALL_REPORT_TITLE;

if (in_array("PER_ORG_TYPE_1", $list_type)) {
    if ($select_org_structure == 0) {
        if ($DEPARTMENT_ID) {
            //$arr_search_condition[] = "(e.POH_ORG2 = '$DEPARTMENT_NAME')";
            $arr_search_condition[] = "(e.POH_ORG2 = '$DEPARTMENT_NAME')";
        } else if ($MINISTRY_ID) {
            $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
            $db_dpis->send_cmd($cmd);
            $data = $db_dpis->get_array();
            $arr_search_condition[] = "(e.POH_ORG1 = '" . $data[ORG_NAME] . "')";
        }
    } else if ($select_org_structure == 1) {
        if ($DEPARTMENT_ID) {
            //$arr_search_condition[] = "(e.POH_ORG2 = '$DEPARTMENT_NAME')";
            //$arr_search_condition[] = "(e.POH_ORG2 = '$DEPARTMENT_NAME')";
            $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID_ORI)";
        } elseif ($MINISTRY_ID) {
            $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
            if ($select_org_structure == 1) {
                $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
            }
            $db_dpis->send_cmd($cmd);
            while ($data = $db_dpis->get_array()) {
                $arr_org_ref[] = $data[ORG_ID];
            }
            $arr_search_condition[] = "(a.DEPARTMENT_ID in (" . implode($arr_org_ref, ",") . "))";
        }
    }

    // ส่วนกลาง
    $list_type_text = "ส่วนกลาง";
    if ($select_org_structure == 0) {
        $arr_search_condition[] = "(trim(c.OT_CODE)='01')";
    } elseif ($select_org_structure == 1) {
        $arr_search_condition[] = "(trim(e.OT_CODE)='01')";
    }
}
if (in_array("PER_ORG_TYPE_2", $list_type)) {
    if ($select_org_structure == 0) {
        if ($DEPARTMENT_ID) {
            //$arr_search_condition[] = "(e.POH_ORG2 = '$DEPARTMENT_NAME')";
            $arr_search_condition[] = "(e.POH_ORG2 = '$DEPARTMENT_NAME')";
        } elseif ($MINISTRY_ID) {
            $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
            $db_dpis->send_cmd($cmd);
            $data = $db_dpis->get_array();
            $arr_search_condition[] = "(e.POH_ORG1 = '" . $data[ORG_NAME] . "')";
        }
    } else if ($select_org_structure == 1) {
        if ($DEPARTMENT_ID) {
            //$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
            $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID_ORI)";
        } elseif ($MINISTRY_ID) {
            $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
            if ($select_org_structure == 1) {
                $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
            }
            $db_dpis->send_cmd($cmd);
            while ($data = $db_dpis->get_array()) {
                $arr_org_ref[] = $data[ORG_ID];
            }
            $arr_search_condition[] = "(a.DEPARTMENT_ID in (" . implode($arr_org_ref, ",") . "))";
        }
    }

    // ส่วนกลางในภูมิภาค
    $list_type_text = "ส่วนกลางในภูมิภาค";
    if ($select_org_structure == 0) {
        $arr_search_condition[] = "(trim(c.OT_CODE)='02')";
    } elseif ($select_org_structure == 1) {
        $arr_search_condition[] = "(trim(e.OT_CODE)='02')";
    }
}
if (in_array("PER_ORG_TYPE_3", $list_type)) {
    if ($select_org_structure == 0) {
        if ($DEPARTMENT_ID) {
            //$arr_search_condition[] = "(e.POH_ORG2 = '$DEPARTMENT_NAME')";
            $arr_search_condition[] = "(e.POH_ORG2 = '$DEPARTMENT_NAME')";
        } elseif ($MINISTRY_ID) {
            $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
            $db_dpis->send_cmd($cmd);
            $data = $db_dpis->get_array();
            $arr_search_condition[] = "(e.POH_ORG1 = '" . $data[ORG_NAME] . "')";
        }
    } else if ($select_org_structure == 1) {
        if ($DEPARTMENT_ID) {
            //$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
            $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID_ORI)";
        } elseif ($MINISTRY_ID) {
            $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
            if ($select_org_structure == 1) {
                $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
            }
            $db_dpis->send_cmd($cmd);
            while ($data = $db_dpis->get_array()) {
                $arr_org_ref[] = $data[ORG_ID];
            }
            $arr_search_condition[] = "(a.DEPARTMENT_ID in (" . implode($arr_org_ref, ",") . "))";
        }
    }

    // ส่วนภูมิภาค
    $list_type_text = "ส่วนภูมิภาค";
    if ($select_org_structure == 0) {
        $arr_search_condition[] = "(trim(c.OT_CODE)='03')";
    } elseif ($select_org_structure == 1) {
        $arr_search_condition[] = "(trim(e.OT_CODE)='03')";
    }
}
if (in_array("PER_ORG_TYPE_4", $list_type)) {
    if ($select_org_structure == 0) {
        if ($DEPARTMENT_ID) {
            //$arr_search_condition[] = "(e.POH_ORG2 = '$DEPARTMENT_NAME')";
            $arr_search_condition[] = "(e.POH_ORG2 = '$DEPARTMENT_NAME')";
        } elseif ($MINISTRY_ID) {
            $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
            $db_dpis->send_cmd($cmd);
            $data = $db_dpis->get_array();
            $arr_search_condition[] = "(e.POH_ORG1 = '" . $data[ORG_NAME] . "')";
        }
    } else if ($select_org_structure == 1) {
        if ($DEPARTMENT_ID) {
            //$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
            $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID_ORI)";
        } elseif ($MINISTRY_ID) {
            $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
            if ($select_org_structure == 1) {
                $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
            }
            $db_dpis->send_cmd($cmd);
            while ($data = $db_dpis->get_array()) {
                $arr_org_ref[] = $data[ORG_ID];
            }

            $arr_search_condition[] = "(a.DEPARTMENT_ID in (" . implode($arr_org_ref, ",") . "))";
        }
    }

    // ต่างประเทศ
    $list_type_text = "ต่างประเทศ";
    if ($select_org_structure == 0) {
        $arr_search_condition[] = "(trim(c.OT_CODE)='04')";
    } elseif ($select_org_structure == 1) {
        $arr_search_condition[] = "(trim(e.OT_CODE)='04')";
    }
}
if (in_array("PER_ORG", $list_type)) {
    $list_type_text = "";
    if ($select_org_structure == 0) {
        if (trim($search_org_id)) {
            $arr_search_condition[] = "(e.POH_ORG3 = '$search_org_name')";
            $list_type_text .= "$search_org_name";
        }
        if (trim($search_org_id_1)) {
            $arr_search_condition[] = "(e.POH_UNDER_ORG1 = '$search_org_name_1')";
            $list_type_text .= " - $search_org_name_1";
        }
        if (trim($search_org_id_2)) {
            $arr_search_condition[] = "(e.POH_UNDER_ORG2 = '$search_org_name_2')";
            $list_type_text .= " - $search_org_name_2";
        }
    } else if ($select_org_structure == 1) {
        if (trim($search_org_ass_id)) {
            $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
            $list_type_text .= "$search_org_ass_name";
        }
        if (trim($search_org_ass_id_1)) {
            $arr_search_condition[] = "(a.ORG_ID_1 = $search_org_ass_id_1)";
            $list_type_text .= " - $search_org_ass_name_1";
        }
        if (trim($search_org_ass_id_2)) {
            $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_ass_id_2)";
            $list_type_text .= " - $search_org_ass_name_2";
        }
    }
}
if (in_array("PER_LINE", $list_type)) {
    // สายงาน
    $list_type_text = "";
    if ($line_search_code) {
        $arr_search_condition[] = "(trim($line_code)='$line_search_code')";
        $list_type_text .= " $line_search_name";
    }
}
if (in_array("PER_COUNTRY", $list_type)) {
    // ประเทศ , จังหวัด
    $list_type_text = "";
    if (trim($search_ct_code)) {
        $search_ct_code = trim($search_ct_code);
        $arr_search_condition[] = "(trim(e.CT_CODE) = '$search_ct_code')";
        $list_type_text .= "$search_ct_name";
    } // end if
    if (trim($search_pv_code)) {
        $search_pv_code = trim($search_pv_code);
        $arr_search_condition[] = "(trim(e.PV_CODE) = '$search_pv_code')";
        $list_type_text .= " - $search_pv_name";
    } // end if
}
if (in_array("ALL", $list_type) || !isset($list_type)) { //กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
    if ($select_org_structure == 0) {
        if ($DEPARTMENT_ID) {
            //$arr_search_condition[] = "(e.POH_ORG2 = '$DEPARTMENT_NAME')";
            $arr_search_condition[] = "(e.POH_ORG2 = '$DEPARTMENT_NAME')";
            $list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
        } elseif ($MINISTRY_ID) {
            $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
            $db_dpis->send_cmd($cmd);
            $data = $db_dpis->get_array();
            $arr_search_condition[] = "(e.POH_ORG1 = '" . $data[ORG_NAME] . "')";
            $list_type_text .= " - $MINISTRY_NAME";
        }
    } else if ($select_org_structure == 1) {
        if ($DEPARTMENT_ID) {
            //$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
            $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID_ORI)";
            $list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
        } elseif ($MINISTRY_ID) {
            $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
            if ($select_org_structure == 1) {
                $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
            }
            $db_dpis->send_cmd($cmd);
            while ($data = $db_dpis->get_array()) {
                $arr_org_ref[] = $data[ORG_ID];
            }

            $arr_search_condition[] = "(a.DEPARTMENT_ID in (" . implode($arr_org_ref, ",") . "))";
            $list_type_text .= " - $MINISTRY_NAME";
        }
    } // end if
}elseif ($PROVINCE_CODE) {
    $PROVINCE_CODE = trim($PROVINCE_CODE);
    if ($select_org_structure == 0) {
        $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
    } elseif ($select_org_structure == 1) {
        $arr_search_condition[] = "(trim(e.PV_CODE) = '$PROVINCE_CODE')";
    }
    $list_type_text .= " - $PROVINCE_NAME";
} // end if
if (count($arr_search_condition)) {
    $search_condition = " where " . implode(" and ", $arr_search_condition);
}

$budget_year = $search_budget_year - 543;
$budget_year_from = $budget_year - 1;
$budget_year_from = $budget_year_from . '-10-01';
$budget_year_to = $budget_year . '-09-30';
/*
  if($export_type=="report"){
  include ("../report/rpt_condition3.php");
  }else if($export_type=="graph"){
  include ("../../admin/report/rpt_condition3.php");	//เงื่อนไขที่ต้องการแสดงผล
  }
 */
$search_condition .= (trim($search_condition) ? " and " : " where ") . "POH_EFFECTIVEDATE >= '$budget_year_from' and POH_EFFECTIVEDATE <= '$budget_year_to' and PER_OCCUPYDATE >= '$budget_year_from' and PER_OCCUPYDATE <= '$budget_year_to'";

$company_name = "รูปแบบการออกรายงาน : " . ($select_org_structure == 1 ? "โครงสร้างตามมอบหมายงาน - " : "โครงสร้างตามกฎหมาย - ") . "$list_type_text";
$report_title = "$DEPARTMENT_NAME||จำนวน$PERSON_TYPE[$search_per_type]ที่บรรจุในปีงบประมาณ $search_budget_year";
if ($export_type == "report") {
    $report_title = (($NUMBER_DISPLAY == 2) ? convert2thaidigit($report_title) : $report_title);
}
$report_code = "R0301";
include ("rpt_R003001_format.php"); // กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
//	session_cache_limiter("nocache");
session_cache_limiter("private");
session_start();

if ($FLAG_RTF) {
//		$sum_w = array_sum($heading_width);
    $sum_w = 0;
    for ($h = 0; $h < count($heading_width); $h++) {
        if ($arr_column_sel[$h] == 1) {
            $sum_w += $heading_width[$h];
        }
    }
    for ($h = 0; $h < count($heading_width); $h++) {
        if ($arr_column_sel[$h] == 1) {
            $heading_width[$h] = $heading_width[$h] / $sum_w * 100;
        }
    }

    $fname = "rpt_R003001_rtf.rtf";

    //	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
    $paper_size = "a4";
    $orientation = 'P';
    $RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

    $RTF->set_default_font($font, 14);
    //	echo "default font_id::".$RTF->dfl_FontID."<br>";

    $RTF->set_report_code($report_code);
    $RTF->set_report_title($report_title);
    $RTF->set_company_name($company_name);
} else {
    $unit = "mm";
    $paper_size = "A4";
    $lang_code = "TH";
    $orientation = 'P';

    $pdf = new PDF($orientation, $unit, $paper_size, $lang_code, $company_name, $report_title, $report_code, $heading, $heading_width, $heading_align);

    $pdf->Open();
    $pdf->SetFont($font, '', 14);
    $pdf->SetMargins(5, 5, 5);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetTextColor(0, 0, 0);

    $page_start_x = $pdf->x;
    $page_start_y = $pdf->y;
}

function count_person($movement_type, $search_condition, $addition_condition) {
    global $DPISDB, $db_dpis2;
    global $arr_rpt_order, $search_per_type, $search_budget_year, $select_org_structure, $BKK_FLAG;
    global $MINISTRY_ID, $DEPARTMENT_ID;

    if (trim($addition_condition)) {
        $search_condition .= (trim($search_condition) ? " and " : " where ") . $addition_condition;
    }
    switch ($movement_type) {
        case 1 :
            $search_condition .= (trim($search_condition) ? " and " : " where ") . "(trim(f.MOV_SUB_TYPE) = 1)";
            break;
        case 2 :
            $search_condition .= (trim($search_condition) ? " and " : " where ") . "(trim(f.MOV_SUB_TYPE) = 10)";
            break;
        case 3 :
            $search_condition .= (trim($search_condition) ? " and " : " where ") . "(trim(f.MOV_SUB_TYPE) = 11)";
            break;
    } // end switch case

    if ($DPISDB == "odbc") {
        if (in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)) {
            $cmd = " select			count(a.PER_ID) as count_person
								 from	(
												(
								 					(
														(
															PER_PERSONAL a
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%2%')
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join PER_MOVMENT f on (e.MOV_CODE=f.MOV_CODE)
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
								$search_condition
								 group by	a.PER_ID  , e.MOV_CODE ";
        } else {
            $cmd = " select			count(a.PER_ID) as count_person
								 from	(
												(
								 					(
														PER_PERSONAL a
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join PER_MOVMENT f on (e.MOV_CODE=f.MOV_CODE)
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
								$search_condition
								 group by	a.PER_ID  , e.MOV_CODE ";
        } // end if
    } elseif ($DPISDB == "oci8") {
        $search_condition = str_replace(" where ", " and ", $search_condition);
        if (in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)) {
            /* $cmd = " select		count(a.PER_ID) as count_person
              from			PER_PERSONAL a, PER_EDUCATE d, PER_POSITIONHIS e, PER_MOVMENT f, PER_ORG g
              where		a.PER_ID=e.PER_ID(+) and e.MOV_CODE=f.MOV_CODE(+) and a.DEPARTMENT_ID=g.ORG_ID(+) and a.PER_ID=d.PER_ID(+) and d.EDU_TYPE like '%2%'
              $search_condition
              group by	a.PER_ID  , e.MOV_CODE "; *//* เดิม */
            $cmd = " select count(a.PER_ID) as count_person
                                from PER_PERSONAL a, PER_EDUCATE d, PER_POSITIONHIS e, PER_MOVMENT f, PER_ORG g
                                where a.PER_ID=e.PER_ID(+)
                                    and e.MOV_CODE=f.MOV_CODE(+)
                                    and a.DEPARTMENT_ID=g.ORG_ID(+)
                                    and a.PER_ID=d.PER_ID(+)
                                    and d.EDU_TYPE like '%2%'

                            $search_condition
                                group by a.PER_ID  , e.MOV_CODE "; /* Release 5.2.1.5 */
        } else {
            /* $cmd = " select		count(a.PER_ID) as count_person
              from			PER_PERSONAL a, PER_POSITIONHIS e, PER_MOVMENT f, PER_ORG g
              where		a.PER_ID=e.PER_ID(+) and e.MOV_CODE=f.MOV_CODE(+) and a.DEPARTMENT_ID=g.ORG_ID(+)
              $search_condition
              group by	a.PER_ID  , e.MOV_CODE "; *//* เดิม */
            
            /* เดิมนับไม่ถูกต้อง เพราะ กรณีมีข้อมูลซ้ำ ที่เป็น MOV_CODE เดียวกันจะนับเป็นคนล่ะรายการ
             * $cmd = " select count(a.PER_ID) as count_person
                                    from PER_PERSONAL a, PER_POSITIONHIS e, PER_MOVMENT f, PER_ORG g
                                    where a.PER_ID=e.PER_ID(+)
                                        and e.MOV_CODE=f.MOV_CODE(+)
                                        and a.DEPARTMENT_ID=g.ORG_ID(+)

                                        $search_condition
                                    group by a.PER_ID  , e.MOV_CODE ";*/ /* Release 5.2.1.5 */
            $cmd = " select count(*) as count_person from (
                            select DISTINCT a.PER_ID
                                        from PER_PERSONAL a, PER_POSITIONHIS e, PER_MOVMENT f, PER_ORG g
                                        where a.PER_ID=e.PER_ID(+)
                                            and e.MOV_CODE=f.MOV_CODE(+)
                                            and a.DEPARTMENT_ID=g.ORG_ID(+)

                                            $search_condition
                                        group by a.PER_ID  , trim(f.MOV_SUB_TYPE) ) ";
        } // end if
    } elseif ($DPISDB == "mysql") {
        if (in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)) {
            $cmd = " select			count(a.PER_ID) as count_person
								 from	(
												(
								 					(
														(
															PER_PERSONAL a
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%2%')
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join PER_MOVMENT f on (e.MOV_CODE=f.MOV_CODE)
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
								$search_condition
								 group by	a.PER_ID  , e.MOV_CODE ";
        } else {
            $cmd = " select			count(a.PER_ID) as count_person
								 from	(
												(
								 					(
														PER_PERSONAL a
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join PER_MOVMENT f on (e.MOV_CODE=f.MOV_CODE)
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
								$search_condition
								 group by	a.PER_ID  , e.MOV_CODE ";
        } // end if
    } // end if
    if ($select_org_structure == 1) {
        $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
        $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
    }

    $count_person = $db_dpis2->send_cmd($cmd);
    if($movement_type==2){
        //echo '['.$count_person,']'.'<pre>';echo $cmd."<br>";
    }
//		$db_dpis2->show_error();
//		echo $count_person." :: ".$cmd."<hr>";
    if ($count_person == 1) {
        $data = $db_dpis2->get_array();
        $data = array_change_key_case($data, CASE_LOWER);
        if ($data[count_person] == 0) {
            $count_person = 0;
        }else{
            $count_person = $data[count_person];
        }
    } // end if

    return $count_person;
}

// function

function generate_condition($current_index) {
    global $DPISDB, $db_dpis, $arr_rpt_order, $search_per_type, $select_org_structure;
    global $MINISTRY_ID, $DEPARTMENT_ID, $DEPARTMENT_ID_ORI, $ORG_ID, $ORG_ID_1, $ORG_ID_2, $MINISTRY_NAME, $DEPARTMENT_NAME,
    $ORG_NAME, $ORG_NAME_1, $ORG_NAME_2, $LEVEL_NO, $LEVEL_NAME, $PL_CODE, $PN_CODE, $PER_GENDER, $PV_CODE, $EM_CODE, $EL_CODE, $EP_CODE, $TP_CODE, $ORG_BKK_TITLE;
    global $line_code, $DEPARTMENT_ID_ORI;
    if ($ORG_NAME == $ORG_BKK_TITLE) {
        $ORG_NAME_SEARCH = "-";
    }   // กำหนดให้คำว่า ผู้บริหาร เป็น - ตาม DB ไม่งั้นจะค้นหาไม่ได้
    else {
        $ORG_NAME_SEARCH = $ORG_NAME;
    }

    for ($rpt_order_index = 0; $rpt_order_index <= $current_index; $rpt_order_index++) {
        $REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
        switch ($REPORT_ORDER) {
            case "MINISTRY" :
                if ($MINISTRY_ID && $MINISTRY_ID != -1) {
                    $arr_addition_condition[] = "(e.POH_ORG1 = '$MINISTRY_NAME')";
                }
                break;
            case "DEPARTMENT" :
                if ($DEPARTMENT_ID && $DEPARTMENT_ID != -1) {
                    if ($select_org_structure == 0) {
                        $arr_addition_condition[] = "(e.POH_ORG2 = '$DEPARTMENT_NAME')";
                        //$arr_addition_condition[] = "(e.POH_ORG2 = '$DEPARTMENT_NAME' or e.ORG_ID_2 = $DEPARTMENT_ID_ORI)";
                    } else if ($select_org_structure == 1) {
                        //$arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
                        $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID_ORI)";
                    }
                } else {
                    if ($select_org_structure == 0) {
                        $arr_addition_condition[] = "(e.POH_ORG2 = '' or e.POH_ORG2 is null)";
                    } else if ($select_org_structure == 1) {
                        $arr_addition_condition[] = "(a.DEPARTMENT_ID = 0 or a.DEPARTMENT_ID is null)";
                    }
                }
                break;
            case "ORG" :
                if ($ORG_ID && $ORG_ID != -1) {
                    if ($select_org_structure == 0) {
                        $arr_addition_condition[] = "(e.POH_ORG3 = '$ORG_NAME_SEARCH')";
                    } else if ($select_org_structure == 1) {
                        $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
                    }
                } else {
                    if ($select_org_structure == 0) {
                        $arr_addition_condition[] = "(e.POH_ORG3 = '' or e.POH_ORG3 is null)";
                    } else if ($select_org_structure == 1) {
                        $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
                    }
                }
                break;
            case "ORG_1" :
                if ($ORG_ID_1 && $ORG_ID_1 != -1) {
                    if ($select_org_structure == 0) {
                        $arr_addition_condition[] = "(e.POH_UNDER_ORG1 = '$ORG_NAME_1')";
                    } else if ($select_org_structure == 1) {
                        $arr_addition_condition[] = "(a.ORG_ID_1 = $ORG_ID_1)";
                    }
                } else {
                    if ($select_org_structure == 0) {
                        $arr_addition_condition[] = "(e.POH_UNDER_ORG1 = '' or e.POH_UNDER_ORG1 is null)";
                    } else if ($select_org_structure == 1) {
                        $arr_addition_condition[] = "(a.ORG_ID_1 = 0 or a.ORG_ID_1 is null)";
                    }
                }
                break;
            case "ORG_2" :
                if ($ORG_ID_2 && $ORG_ID_2 != -1) {
                    if ($select_org_structure == 0) {
                        $arr_addition_condition[] = "(e.POH_UNDER_ORG2 = '$ORG_NAME_2')";
                    } else if ($select_org_structure == 1) {
                        $arr_addition_condition[] = "(a.ORG_ID_2 = $ORG_ID_2)";
                    }
                } else {
                    if ($select_org_structure == 0) {
                        $arr_addition_condition[] = "(e.POH_UNDER_ORG2 = '' or e.POH_UNDER_ORG2 is null)";
                    } else if ($select_org_structure == 1) {
                        $arr_addition_condition[] = "(a.ORG_ID_2 = 0 or a.ORG_ID_2 is null)";
                    }
                }
                break;
            case "LINE" :
                if ($PL_CODE) {
                    $arr_addition_condition[] = "(trim($line_code) = '$PL_CODE')";
                } else {
                    $arr_addition_condition[] = "(trim($line_code) = '$PL_CODE' or $line_code is null)";
                }
                break;
            case "LEVEL" :
                if ($LEVEL_NO) {
                    $arr_addition_condition[] = "(trim(a.LEVEL_NO) = '$LEVEL_NO')";
                } else {
                    $arr_addition_condition[] = "(trim(a.LEVEL_NO) = '$LEVEL_NO' or a.LEVEL_NO is null)";
                }
                break;
            case "SEX" :
                if ($PER_GENDER) {
                    $arr_addition_condition[] = "(a.PER_GENDER = $PER_GENDER)";
                } else {
                    $arr_addition_condition[] = "(a.PER_GENDER = $PER_GENDER or a.PER_GENDER is null)";
                }
                break;
            case "PROVINCE" :
                if ($PV_CODE) {
                    $arr_addition_condition[] = "(trim(a.PV_CODE) = '$PV_CODE')";
                } else {
                    $arr_addition_condition[] = "(trim(a.PV_CODE) = '$PV_CODE' or a.PV_CODE is null)";
                }
                break;
            case "EDUCLEVEL" :
                if ($EL_CODE) {
                    $arr_addition_condition[] = "(trim(d.EL_CODE) = '$EL_CODE')";
                }
                //else $arr_addition_condition[] = "(trim(d.EL_CODE) = '$EL_CODE' or d.EL_CODE is null)";
                break;
            case "EDUCMAJOR" :
                if ($EM_CODE) {
                    $arr_addition_condition[] = "(trim(d.EM_CODE) = '$EM_CODE')";
                } else {
                    $arr_addition_condition[] = "(trim(d.EM_CODE) = '$EM_CODE' or d.EM_CODE is null)";
                }
                break;
        } // end switch case
    } // end for

    $addition_condition = "";
    if (count($arr_addition_condition)) {
        $addition_condition = implode(" and ", $arr_addition_condition);
    }

    return $addition_condition;
}

// function

function initialize_parameter($current_index) {
    global $arr_rpt_order;
    global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $PN_CODE, $LEVEL_NO, $LEVEL_NAME, $PER_GENDER, $PV_CODE, $EM_CODE, $EL_CODE, $EP_CODE, $TP_CODE;

    for ($rpt_order_index = $current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++) {
        $REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
        switch ($REPORT_ORDER) {
            case "MINISTRY" :
                $MINISTRY_ID = -1;
                break;
            case "DEPARTMENT" :
                $DEPARTMENT_ID = -1;
                break;
            case "ORG" :
                $ORG_ID = -1;
                break;
            case "ORG_1" :
                $ORG_ID_1 = -1;
                break;
            case "ORG_2" :
                $ORG_ID_2 = -1;
                break;
            case "LINE" :
                $PL_CODE = -1;
                break;
            case "LEVEL" :
                $LEVEL_NO = -1;
                break;
            case "SEX" :
                $PER_GENDER = -1;
                break;
            case "PROVINCE" :
                $PV_CODE = -1;
                $PN_CODE = -1;
                break;
            case "EDUCLEVEL" :
                $EL_CODE = -1;
                break;
            case "EDUCMAJOR" :
                $EM_CODE = -1;
                break;
        } // end switch case
    } // end for
}

// function

if ($DPISDB == "odbc") {
    if (in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)) {
        $cmd = " select			distinct $select_list
							 from		(
                                                                                                (
                                                                                                    (
                                                                                                            (
                                                                                                                    (
                                                                                                                            PER_PERSONAL a
                                                                                                                    ) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%2%')
                                                                                                            ) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
                                                                                                    ) left join PER_MOVMENT f on (e.MOV_CODE=f.MOV_CODE)
                                                                                                ) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
												$search_condition $condwhere
							 order by		$order_by ";
    } else {
        $cmd = " select			distinct $select_list
							 from		(
                                                                                                (
                                                                                                    (
                                                                                                            (
                                                                                                                    PER_PERSONAL a
                                                                                                            ) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
                                                                                                    ) left join PER_MOVMENT f on (e.MOV_CODE=f.MOV_CODE)
                                                                                                ) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
												$search_condition $condwhere
							 order by		$order_by ";
    } // end if
} elseif ($DPISDB == "oci8") {
    $search_condition = str_replace(" where ", " and ", $search_condition);
    if (in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)) {
        $cmd = " select			distinct $select_list
							 from			PER_PERSONAL a, PER_EDUCATE d, PER_POSITIONHIS e, PER_MOVMENT f, PER_ORG g, PER_LEVEL i
							 where		a.PER_ID=e.PER_ID(+) and a.LEVEL_NO=i.LEVEL_NO(+) and e.MOV_CODE=f.MOV_CODE(+) and a.DEPARTMENT_ID=g.ORG_ID(+) and a.PER_ID=d.PER_ID(+) and d.EDU_TYPE like '%2%'  and (trim(f.MOV_SUB_TYPE) in (1,10,11))
												$search_condition $condwhere
							 order by		$order_by ";
    } else {
        $cmd = " select			distinct $select_list
							 from			PER_PERSONAL a, PER_POSITIONHIS e, PER_MOVMENT f, PER_ORG g,PER_LEVEL i
							 where		a.PER_ID=e.PER_ID(+) and a.LEVEL_NO=i.LEVEL_NO(+) and e.MOV_CODE=f.MOV_CODE(+) and a.DEPARTMENT_ID=g.ORG_ID(+) and (trim(f.MOV_SUB_TYPE) in (1,10,11))
												$search_condition $condwhere
							 order by		$order_by ";
    } // end if
} elseif ($DPISDB == "mysql") {
    if (in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)) {
        $cmd = " select			distinct $select_list
							 from		(
                                                                                                (
                                                                                                    (
                                                                                                            (
                                                                                                                    (
                                                                                                                            PER_PERSONAL a
                                                                                                                    ) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%2%')
                                                                                                            ) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
                                                                                                    ) left join PER_MOVMENT f on (e.MOV_CODE=f.MOV_CODE)
                                                                                                ) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
												$search_condition $condwhere
							 order by		$order_by ";
    } else {
        $cmd = " select			distinct $select_list
							 from		(
                                                                                                (
                                                                                                    (
                                                                                                            (
                                                                                                                    PER_PERSONAL a
                                                                                                            ) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
                                                                                                    ) left join PER_MOVMENT f on (e.MOV_CODE=f.MOV_CODE)
                                                                                                ) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
												$search_condition $condwhere
							 order by		$order_by ";
    } // end if
}
if ($select_org_structure == 1) {
    $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
    $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
}
$count_data = $db_dpis->send_cmd($cmd);
//echo "<pre>$cmd<br>";
//	$db_dpis->show_error();
$data_count = 0;
$GRAND_TOTAL = $GRAND_TOTAL_1 = $GRAND_TOTAL_2 = $GRAND_TOTAL_3 = 0;
initialize_parameter(0);
$first_order = 1;  // order ที  0 = COUNTRY ยังไม่ได้คำนวณ เริ่ม order ที่ 1 (MINISTRY) ก่อน
while ($data = $db_dpis->get_array()) {

    if (!($MINISTRY_ID == $DEPARTMENT_ID && $DEPARTMENT_ID == 1)) {
        for ($rpt_order_index = 0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++) {
            $REPORT_ORDER = $arr_rpt_order[$rpt_order_index];

            switch ($REPORT_ORDER) {
                case "MINISTRY" :
                    if ($MINISTRY_ID != trim($data[MINISTRY_ID])) {
                        $MINISTRY_ID = trim($data[MINISTRY_ID]);
                        if ($MINISTRY_ID != "" || $MINISTRY_ID != 0 || $MINISTRY_ID != -1) {
                            /* $cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$MINISTRY_ID ";
                              $db_dpis2->send_cmd($cmd);
                              $data2 = $db_dpis2->get_array();
                              $MINISTRY_NAME = $data2[ORG_NAME];
                              $MINISTRY_SHORT = $data2[ORG_SHORT]; */
                            $MINISTRY_NAME = $MINISTRY_ID;    //ใน db ฟิล์ดนี้เก็บเป็นตัวอักษร NAME

                            if ($f_all) {
                                $addition_condition = generate_condition($rpt_order_index);

                                $GRAND_TOTAL1[$DEPARTMENT_ID] = count_person(1, $search_condition, $addition_condition);
                                $GRAND_TOTAL2[$DEPARTMENT_ID] = count_person(2, $search_condition, $addition_condition);
                                $GRAND_TOTAL3[$DEPARTMENT_ID] = count_person(3, $search_condition, $addition_condition);
                                $GRAND_TOTAL[$DEPARTMENT_ID] = ($GRAND_TOTAL1[$DEPARTMENT_ID] + $GRAND_TOTAL2[$DEPARTMENT_ID] + $GRAND_TOTAL3[$DEPARTMENT_ID]);

                                if ($MINISTRY_ID == $DEPARTMENT_ID && $DEPARTMENT_ID == 1) {
                                    $arr_content[$data_count][type] = "COUNTRY";
                                    //$rpt_order_start_index = 0;
                                    //$addition_condition = "";
                                } else {
                                    $arr_content[$data_count][type] = "MINISTRY";
                                    //$rpt_order_start_index = 1;
                                    //$addition_condition = generate_condition(1);
                                }
                                $arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $MINISTRY_NAME;
                                $arr_content[$data_count][short_name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $MINISTRY_SHORT;
                                $arr_content[$data_count][id] = $DEPARTMENT_ID;
                                $arr_content[$data_count][count_1] = $GRAND_TOTAL1[$DEPARTMENT_ID];
                                $arr_content[$data_count][count_2] = $GRAND_TOTAL2[$DEPARTMENT_ID];
                                $arr_content[$data_count][count_3] = $GRAND_TOTAL3[$DEPARTMENT_ID];

                                if ($rpt_order_index == $first_order) {
                                    $GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
                                    $GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
                                    $GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
                                } // end if

                                if ($rpt_order_index < (count($arr_rpt_order) - 1)) {
                                    initialize_parameter($rpt_order_index + 1);
                                }
                                $data_count++;
                            } // end if ($f_all)
                        } // end if($MINISTRY_ID != "" || $MINISTRY_ID!=0 || $MINISTRY_ID!=-1)
                    } // end if
                    break;

                case "DEPARTMENT" :
                    if ($DEPARTMENT_ID != trim($data[DEPARTMENT_ID])) {
                        $DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
                        if ($DEPARTMENT_ID != "" || $DEPARTMENT_ID != 0 || $DEPARTMENT_ID != -1) {
                            /* $cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
                              $db_dpis2->send_cmd($cmd);
                              $data2 = $db_dpis2->get_array();
                              $DEPARTMENT_NAME = $data2[ORG_NAME];
                              $DEPARTMENT_SHORT = $data2[ORG_SHORT]; */
                            $DEPARTMENT_NAME = $DEPARTMENT_ID;    //ใน db ฟิล์ดนี้เก็บเป็นตัวอักษร NAME

                            $addition_condition = generate_condition($rpt_order_index);

                            $GRAND_TOTAL1[$DEPARTMENT_ID] = count_person(1, $search_condition, $addition_condition);
                            $GRAND_TOTAL2[$DEPARTMENT_ID] = count_person(2, $search_condition, $addition_condition);
                            $GRAND_TOTAL3[$DEPARTMENT_ID] = count_person(3, $search_condition, $addition_condition);
                            $GRAND_TOTAL[$DEPARTMENT_ID] = ($GRAND_TOTAL1[$DEPARTMENT_ID] + $GRAND_TOTAL2[$DEPARTMENT_ID] + $GRAND_TOTAL3[$DEPARTMENT_ID]);

                            $arr_content[$data_count][type] = "DEPARTMENT";
                            $arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $DEPARTMENT_NAME;
                            $arr_content[$data_count][short_name] = str_repeat(" ", (($rpt_order_index - $first_order) * 5)) . $DEPARTMENT_SHORT;
                            $arr_content[$data_count][id] = $DEPARTMENT_ID;
                            $arr_content[$data_count][count_1] = $GRAND_TOTAL1[$DEPARTMENT_ID];
                            $arr_content[$data_count][count_2] = $GRAND_TOTAL2[$DEPARTMENT_ID];
                            $arr_content[$data_count][count_3] = $GRAND_TOTAL3[$DEPARTMENT_ID];

                            if ($rpt_order_index == $first_order) {
                                $GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
                                $GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
                                $GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
                            } // end if

                            if ($rpt_order_index < (count($arr_rpt_order) - 1)) {
                                initialize_parameter($rpt_order_index + 1);
                            }
                            $data_count++;
                        } // end if($DEPARTMENT_ID != "" || $DEPARTMENT_ID != 0 || $DEPARTMENT_ID != -1)
                    } // end if
                    break;

                case "ORG" :
                    if ($ORG_ID != trim($data[ORG_ID])) {
                        $ORG_ID = trim($data[ORG_ID]);
                        if ($ORG_ID != "" || $ORG_ID != 0 || $ORG_ID != -1) {
                            if ($select_org_structure == 0) {
                                $ORG_NAME = $ORG_ID;
                            } elseif ($select_org_structure == 1) {
                                $cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID ";
                                $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                                $db_dpis2->send_cmd($cmd);
                                //							$db_dpis2->show_error();
                                $data2 = $db_dpis2->get_array();
                                $ORG_NAME = $data2[ORG_NAME];
                                $ORG_SHORT = $data2[ORG_SHORT];
                            }
                            if ($ORG_NAME == "-") {
                                $ORG_NAME = $ORG_BKK_TITLE;
                            }

                            if (($ORG_NAME != "" && $ORG_NAME != "-") || ($BKK_FLAG == 1 && $ORG_NAME != "" && $ORG_NAME != "-")) {
                                $addition_condition = generate_condition($rpt_order_index);

                                $arr_content[$data_count][type] = "ORG";
                                $arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
                                $arr_content[$data_count][short_name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_SHORT;
                                $arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
                                $arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
                                $arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);

                                if ($rpt_order_index == $first_order) {
                                    $GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
                                    $GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
                                    $GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
                                } // end if

                                if ($rpt_order_index < (count($arr_rpt_order) - 1)) {
                                    initialize_parameter($rpt_order_index + 1);
                                }
                                $data_count++;
                            } // end if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
                        } // end if($ORG_ID != "" || $ORG_ID != 0 || $ORG_ID != -1)
                    }
                    break;

                case "ORG_1" :
                    if ($ORG_ID_1 != trim($data[ORG_ID_1])) {
                        $ORG_ID_1 = trim($data[ORG_ID_1]);
                        $ORG_NAME_1 = $ORG_SHORT_1 = "ไม่ระบุ";
                        if ($ORG_ID_1 != "" || $ORG_ID_1 != 0 || $ORG_ID_1 != -1) {
                            $cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_1 ";
                            if ($select_org_structure == 1) {
                                $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                            }
                            $db_dpis2->send_cmd($cmd);
                            //							$db_dpis2->show_error();
                            $data2 = $db_dpis2->get_array();
                            $ORG_NAME_1 = $data2[ORG_NAME];
                            $ORG_SHORT_1 = $data2[ORG_SHORT];
                            if ($ORG_NAME_1 == "-") {
                                $ORG_NAME_1 = $ORG_BKK_TITLE;
                            }
                        } //end if($ORG_ID_1 != "" || $ORG_ID_1 != 0 || $ORG_ID_1 != -1)

                        if (($ORG_NAME_1 != "" && $ORG_NAME_1 != "-") || ($BKK_FLAG == 1 && $ORG_NAME_1 != "" && $ORG_NAME_1 != "-")) {
                            $addition_condition = generate_condition($rpt_order_index);

                            $arr_content[$data_count][type] = "ORG_1";
                            $arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_1;
                            $arr_content[$data_count][short_name] = $ORG_SHORT_1;
                            $arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
                            $arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
                            $arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);

                            if ($rpt_order_index == $first_order) {
                                $GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
                                $GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
                                $GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
                            } // end if

                            if ($rpt_order_index < (count($arr_rpt_order) - 1)) {
                                initialize_parameter($rpt_order_index + 1);
                            }
                            $data_count++;
                        } // end if(($ORG_NAME_1 !="" && $ORG_NAME_1 !="-") || ($BKK_FLAG==1 && $ORG_NAME_1 !="" && $ORG_NAME_1 !="-"))
                    }
                    break;

                case "ORG_2" :
                    if ($ORG_ID_2 != trim($data[ORG_ID_2])) {
                        $ORG_ID_2 = trim($data[ORG_ID_2]);
                        $ORG_NAME_2 = $ORG_SHORT_2 = "ไม่ระบุ";
                        if ($ORG_ID_2 != "" || $ORG_ID_2 != 0 || $ORG_ID_2 != -1) {
                            $cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_2 ";
                            if ($select_org_structure == 1) {
                                $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                            }
                            $db_dpis2->send_cmd($cmd);
                            //							$db_dpis2->show_error();
                            $data2 = $db_dpis2->get_array();
                            $ORG_NAME_2 = $data2[ORG_NAME];
                            $ORG_SHORT_2 = $data2[ORG_SHORT];
                            if ($ORG_NAME_2 == "-") {
                                $ORG_NAME_2 = $ORG_BKK_TITLE;
                            }
                        } // end if($ORG_ID_2!= "" || $ORG_ID_2 != 0 || $ORG_ID_2 != -1)

                        if (($ORG_NAME_2 != "" && $ORG_NAME_2 != "-") || ($BKK_FLAG == 1 && $ORG_NAME_2 != "" && $ORG_NAME_2 != "-")) {
                            $addition_condition = generate_condition($rpt_order_index);

                            $arr_content[$data_count][type] = "ORG_2";
                            $arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_2;
                            $arr_content[$data_count][short_name] = $ORG_SHORT_2;
                            $arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
                            $arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
                            $arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);

                            if ($rpt_order_index == $first_order) {
                                $GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
                                $GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
                                $GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
                            } // end if

                            if ($rpt_order_index < (count($arr_rpt_order) - 1)) {
                                initialize_parameter($rpt_order_index + 1);
                            }
                            $data_count++;
                        } // end if(($ORG_NAME_2 !="" && $ORG_NAME_2 !="-") || ($BKK_FLAG==1 && $ORG_NAME_2 !="" && $ORG_NAME_2 !="-"))
                    }
                    break;
                case "LINE" :
                    if (trim($data[PL_CODE]) && $PL_CODE != trim($data[PL_CODE])) {
                        $PL_CODE = trim($data[PL_CODE]);
                        if ($PL_CODE != "") {
                            if ($search_per_type == 1) {
                                $cmd = " select $line_name as PL_NAME, $line_short_name from $line_table e where trim($line_code)='$PL_CODE' ";
                            } else {
                                $cmd = " select $line_name as PL_NAME from $line_table e where trim($line_code)='$PL_CODE' ";
                            }
                            //echo $cmd.'<br>';
                            $db_dpis2->send_cmd($cmd);
                            $data2 = $db_dpis2->get_array();
                            $PL_NAME = trim($data2[PL_NAME]);
                            if ($search_per_type == 1) {
                                $PL_NAME = trim($data2[$line_short_name]) ? $data2[$line_short_name] : $PL_NAME;
                            }
                        } // end if
                        if (($PL_NAME != "" && $PL_NAME != "-") || ($BKK_FLAG == 1 && $PL_NAME != "" && $PL_NAME != "-")) {
                            $addition_condition = generate_condition($rpt_order_index);

                            $arr_content[$data_count][type] = "LINE";
                            $arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_NAME;
                            $arr_content[$data_count][short_name] = "";
                            $arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
                            $arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
                            $arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);

                            if ($rpt_order_index == $first_order) {
                                $GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
                                $GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
                                $GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
                            } // end if
                            if ($rpt_order_index < (count($arr_rpt_order) - 1)) {
                                initialize_parameter($rpt_order_index + 1);
                            }
                            $data_count++;
                        } // end if
                    }
                    break;

                case "LEVEL" :
                    if ($LEVEL_NO != trim($data[LEVEL_NO])) {
                        $LEVEL_NO = trim($data[LEVEL_NO]);
                        $LEVEL_NAME = trim($data[LEVEL_NAME]);
                        if (($LEVEL_NAME != "" && $LEVEL_NAME != "-") || ($BKK_FLAG == 1 && $LEVEL_NAME != "" && $LEVEL_NAME != "-")) {
                            $addition_condition = generate_condition($rpt_order_index);

                            $arr_content[$data_count][type] = "LEVEL";
                            $arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . (trim($LEVEL_NAME) ? "" . $LEVEL_NAME : "[ไม่ระบุระดับตำแหน่ง]");
                            $arr_content[$data_count][short_name] = "";
                            $arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
                            $arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
                            $arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);

                            if ($rpt_order_index == $first_order) {
                                $GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
                                $GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
                                $GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
                            } // end if

                            if ($rpt_order_index < (count($arr_rpt_order) - 1)) {
                                initialize_parameter($rpt_order_index + 1);
                            }
                            $data_count++;
                        } // end if
                    }
                    break;
                case "SEX" :
                    if ($PER_GENDER != trim($data[PER_GENDER])) {
                        $PER_GENDER = trim($data[PER_GENDER]) + 0;

                        $addition_condition = generate_condition($rpt_order_index);

                        $arr_content[$data_count][type] = "SEX";
                        $arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . (($PER_GENDER == 1) ? "ชาย" : (($PER_GENDER == 2) ? "หญิง" : ""));
                        $arr_content[$data_count][short_name] = "";
                        $arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
                        $arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
                        $arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);

                        if ($rpt_order_index == $first_order) {
                            $GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
                            $GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
                            $GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
                        } // end if

                        if ($rpt_order_index < (count($arr_rpt_order) - 1)) {
                            initialize_parameter($rpt_order_index + 1);
                        }
                        $data_count++;
                    } // end if
                    break;

                case "PROVINCE" :
                    if (trim($data[PV_CODE]) && $PV_CODE != trim($data[PV_CODE])) {
                        $PV_CODE = trim($data[PV_CODE]);
                        if ($PV_CODE != "") {
                            $cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE' ";
                            $db_dpis2->send_cmd($cmd);
                            //							$db_dpis2->show_error();
                            $data2 = $db_dpis2->get_array();
                            $PV_NAME = $data2[PV_NAME];
                        } // end if
                        if (($PV_NAME != "" && $PV_NAME != "-") || ($BKK_FLAG == 1 && $PV_NAME != "" && $PV_NAME != "-")) {
                            $addition_condition = generate_condition($rpt_order_index);

                            $arr_content[$data_count][type] = "PROVINCE";
                            $arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PV_NAME;
                            $arr_content[$data_count][short_name] = "";
                            $arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
                            $arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
                            $arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);

                            if ($rpt_order_index == $first_order) {
                                $GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
                                $GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
                                $GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
                            } // end if

                            if ($rpt_order_index < (count($arr_rpt_order) - 1)) {
                                initialize_parameter($rpt_order_index + 1);
                            }
                            $data_count++;
                        } // end if
                    }
                    break;

                case "EDUCLEVEL" :
                    if (trim($data[EL_CODE]) && $EL_CODE != trim($data[EL_CODE])) {
                        $EL_CODE = trim($data[EL_CODE]);
                        if ($EL_CODE != "") {
                            $cmd = " select EL_NAME from PER_EDUCLEVEL where trim(EL_CODE)='$EL_CODE' ";
                            $db_dpis2->send_cmd($cmd);
                            //							$db_dpis2->show_error();
                            $data2 = $db_dpis2->get_array();
                            $EL_NAME = $data2[EL_NAME];
                        } // end if

                        $addition_condition = generate_condition($rpt_order_index);

                        $arr_content[$data_count][type] = "EDUCLEVEL";
                        $arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $EL_NAME;
                        $arr_content[$data_count][short_name] = "";
                        $arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
                        $arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
                        $arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);

                        if ($rpt_order_index == $first_order) {
                            $GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
                            $GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
                            $GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
                        } // end if

                        if ($rpt_order_index < (count($arr_rpt_order) - 1)) {
                            initialize_parameter($rpt_order_index + 1);
                        }
                        $data_count++;
                    } // end if
                    break;

                case "EDUCMAJOR" :
                    if (trim($data[EM_CODE]) && $EM_CODE != trim($data[EM_CODE])) {
                        $EM_CODE = trim($data[EM_CODE]);
                        if ($EM_CODE != "") {
                            $cmd = " select EM_NAME from PER_EDUCMAJOR where trim(EM_CODE)='$EM_CODE' ";
                            $db_dpis2->send_cmd($cmd);
                            //							$db_dpis2->show_error();
                            $data2 = $db_dpis2->get_array();
                            $EM_NAME = $data2[EM_NAME];
                        } // end if

                        $addition_condition = generate_condition($rpt_order_index);

                        $arr_content[$data_count][type] = "EDUCMAJOR";
                        $arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $EM_NAME;
                        $arr_content[$data_count][short_name] = "";
                        $arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
                        $arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
                        $arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);

                        if ($rpt_order_index == $first_order) {
                            $GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
                            $GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
                            $GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
                        } // end if

                        if ($rpt_order_index < (count($arr_rpt_order) - 1)) {
                            initialize_parameter($rpt_order_index + 1);
                        }
                        $data_count++;
                    } // end if
                    break;
            } // end switch case
        } // end for
    }//if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1))
} // end while
//die();
if (array_search("EDUCLEVEL", $arr_rpt_order) !== false && array_search("EDUCLEVEL", $arr_rpt_order) == 0) {
    $GRAND_TOTAL_1 = count_person(1, $search_condition, "");
    $GRAND_TOTAL_2 = count_person(2, $search_condition, "");
    $GRAND_TOTAL_3 = count_person(3, $search_condition, "");
} // end if
if (array_search("EDUCMAJOR", $arr_rpt_order) !== false && array_search("EDUCMAJOR", $arr_rpt_order) == 0) {
    $GRAND_TOTAL_1 = count_person(1, $search_condition, "");
    $GRAND_TOTAL_2 = count_person(2, $search_condition, "");
    $GRAND_TOTAL_3 = count_person(3, $search_condition, "");
} // end if

$GRAND_TOTAL = $GRAND_TOTAL_1 + $GRAND_TOTAL_2 + $GRAND_TOTAL_3;
//	echo "<pre>"; print_r($arr_content); echo "</pre>";

if ($export_type == "report") {
    $head_text1 = implode(",", $heading_text);
    $head_width1 = implode(",", $heading_width);
    $head_align1 = implode(",", $heading_align);
    $col_function = implode(",", $column_function);
    if ($FLAG_RTF) {
        $RTF->add_header("", 0, false); // header default
        $RTF->add_footer("", 0, false);  // footer default
        //		echo "$head_text1<br>";
        $tab_align = "center";
        $result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
    } else {
        $pdf->AutoPageBreak = false;
        //		echo "$head_text1<br>";
        $result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
    }
    if (!$result) {
        echo "****** error ****** on open table for $table<br>";
    }

    if ($count_data) {

        for ($data_count = 0; $data_count < count($arr_content); $data_count++) {
            $REPORT_ORDER = $arr_content[$data_count][type];
            $NAME = $arr_content[$data_count][name];
            $COUNT_1 = $arr_content[$data_count][count_1];
            $COUNT_2 = $arr_content[$data_count][count_2];
            $COUNT_3 = $arr_content[$data_count][count_3];
            $COUNT_TOTAL = $COUNT_1 + $COUNT_2 + $COUNT_3;

            $arr_data = (array) null;
            $arr_data[] = $NAME;
            $arr_data[] = $COUNT_1;
            $arr_data[] = $COUNT_2;
            $arr_data[] = $COUNT_3;
            $arr_data[] = $COUNT_TOTAL;

            if ($FLAG_RTF) {
                $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
            } else {
                $result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "12", "", "000000", "");
            }  //TRHBL
            if (!$result) {
                echo "****** error ****** add data to table at record count = $data_count <br>";
            }
        } // end for

        if (!$FLAG_RTF) {
            $pdf->add_data_tab("", 0, "RHBL", $data_align, "", "12", "", "000000", "");
        }  // เส้นปิดบรรทัด

        $arr_data = (array) null;
        $arr_data[] = "รวม";
        $arr_data[] = $GRAND_TOTAL_1;
        $arr_data[] = $GRAND_TOTAL_2;
        $arr_data[] = $GRAND_TOTAL_3;
        $arr_data[] = $GRAND_TOTAL;

        $data_align = array("L", "R", "R", "R", "R");
        if ($FLAG_RTF) {
            $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
        } else {
            $result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "12", "", "000000", "");
        }  //TRHBL
        if (!$result) {
            echo "****** error ****** add data to table at record count = $data_count <br>";
        }
    }else {
        if ($FLAG_RTF) {
            $result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
        } else {
            $result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
        }
        if (!$result) {
            echo "****** error ****** add text line to table at record count = $data_count <br>";
        }
    } // end if
    if ($FLAG_RTF) {
        $RTF->close_tab();
//			$RTF->close_section();

        $RTF->display($fname);
    } else {
        $pdf->close_tab("");

        $pdf->close();
        $pdf->Output();
    }
} else if ($export_type == "graph") {//if($export_type=="report"){
    $arr_content_map = array("name", "count_1", "count_2", "count_3", "count_total"); // ชื่อ column ใน content ที่ map ให้ตรงกับ head pdf
    $arr_series_caption = array("", "บรรจุใหม่", "รับโอน", "บรรจุกลับ", "รวม"); // หัวที่ต้องการเฉพาะออก กราฟ map ให้ตรงกับ head ของ pdf
    //	echo "<pre>"; print_r($arr_content); echo "</pre>";
    $arr_content_key = array_keys($arr_content[0]); //print_r($arr_content_key);
    $arr_categories = array();
    $arr_series_caption_list = array();
    $f_first = true;
    $arr_grand_total = (array) null;
    for ($i = 0; $i < count($arr_content); $i++) {
        if ($arr_content[$i][type] == $arr_rpt_order[0]) {
            $arr_categories[$i] = trim($short_name == "y") ? (trim($arr_content[$i][short_name]) ? $arr_content[$i][short_name] : $arr_content[$i][name]) : $arr_content[$i][name];
            $cntseq = 0;
            for ($j = 0; $j < count($arr_content_map); $j++) {
                if ($arr_column_sel[$arr_column_map[$j]] == 1 && strpos($arr_content_map[$arr_column_map[$j]], "count") !== false) {
                    $arr_series_caption_data[$cntseq][] = $arr_content[$i][$arr_content_map[$arr_column_map[$j]]];
                    if ($f_first) {
                        $arr_series_caption_list[] = $arr_series_caption[$arr_column_map[$j]];
                    }
                    //					if ($f_first) echo "caption (j:$j)=".$arr_series_caption[$arr_content_map[$arr_column_map[$j]]]."  contentname=".$arr_content_map[$arr_column_map[$j]]."  mapseq=".$arr_column_map[$j]."<br>";
                    $arr_grand_total[$cntseq] = ${"GRAND_TOTAL_" . ($arr_column_map[$j])};
                    $cntseq++;
                }
            }
            $f_first = false; // check สำหรับรอบแรกเท่านั้น
        }//if($arr_content[$i][type]==$arr_rpt_order[0]){
    }//for($i=0;$i<count($arr_content);$i++){
    $series_caption_list = implode(";", $arr_series_caption_list);
//		echo "count (arr_series_caption_data)=".count($arr_series_caption_data)."<br>";
    for ($j = 0; $j < count($arr_series_caption_data); $j++) {
        $arr_series_list[$j] = implode(";", $arr_series_caption_data[$j]) . ";" . $arr_grand_total[$j];
    }
//		for($j=3;$j<count($arr_content_key);$j++){
//			$arr_series_list[$j] = implode(";", $arr_series_caption_data[$j]).";".${"GRAND_TOTAL_".($j-2)};
//		}
    //	echo "<pre>"; print_r($arr_series_list); echo "</pre>";

    $chart_title = $report_title;
    $chart_subtitle = $company_name;
    if (!$setWidth) {
        $setWidth = "$GRAPH_WIDE";
    } else {
        $setWidth = "800";
    }
    if (!$setHeight) {
        $setHeight = "$GRAPH_HIGH";
    } else {
        $setHeight = "600";
    }
    $selectedFormat = "SWF";
    $categories_list = implode(";", $arr_categories) . ";รวม";
    if (strtolower($graph_type) == "pie") {
        $series_list = $GRAND_TOTAL_1 . ";" . $GRAND_TOTAL_2 . ";" . $GRAND_TOTAL_3;
    } else {
        $series_list = implode("|", $arr_series_list);
    }
    //	echo($series_list);
    switch (strtolower($graph_type)) {
        case "column" :
            $style = $_SERVER['DOCUMENT_ROOT'] . "/graph/styles/Column/style/column.scs";
            break;
        case "bar" :
            $style = $_SERVER['DOCUMENT_ROOT'] . "/graph/styles/Bar/style/bar.scs";
            break;
        case "line" :
            $style = $_SERVER['DOCUMENT_ROOT'] . "/graph/styles/Line/style/line.scs";
            break;
        case "pie" :
            $style = $_SERVER['DOCUMENT_ROOT'] . "/graph/styles/Pie/style/pie.scs";
            break;
    } //switch( strtolower($graph_type) ){
}//}else if($export_type=="graph"){