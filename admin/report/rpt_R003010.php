<?php

include("../../php_scripts/connect_database.php");
if (!$FLAG_RTF) {
    include("../php_scripts/pdf_wordarray_thaicut.php");
}
include("../../php_scripts/calendar_data.php");
include ("../php_scripts/function_share.php");

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
$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

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
    $line_title = " สายงาน";
} elseif ($search_per_type == 2) {
    $line_table = "PER_POS_NAME";
    $line_code = "e.PN_CODE";
    $line_name = "e.PN_NAME";
    $line_search_code = trim($search_pn_code);
    $line_search_name = trim($search_pn_name);
    $line_title = " ชื่อตำแหน่ง";
} elseif ($search_per_type == 3) {
    $line_table = "PER_EMPSER_POS_NAME";
    $line_code = "e.EP_CODE";
    $line_name = "e.EP_NAME";
    $line_search_code = trim($search_ep_code);
    $line_search_name = trim($search_ep_name);
    $line_title = " ชื่อตำแหน่ง";
} elseif ($search_per_type == 4) {
    $line_table = "PER_TEMP_POS_NAME";
    $line_code = "e.TP_CODE";
    $line_name = "e.TP_NAME";
    $line_search_code = trim($search_tp_code);
    $line_search_name = trim($search_tp_name);
    $line_title = " ชื่อตำแหน่ง";
} // end if

if (in_array("ALL", $list_type)) {//if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {เงื่อนไขเดิมไม่แสดงกระทรวงหากมีการเลือกกรม ทำให้การนับข้อมูลไม่ถูกต้อง
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
            $select_list .= "$line_code as PL_CODE";

            if ($order_by) {
                $order_by .= ", ";
            }
            $order_by .= "$line_code";

            $heading_name .= $line_title;
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
//	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";			//search_per_status  เงื่อนไขที่ถูกส่งมา
$arr_search_condition[] = "(a.PER_STATUS in (" . implode(", ", $search_per_status) . "))";
$budget_year = $search_budget_year - 543;
$budget_year_from = $budget_year - 1;
$budget_year_from = $budget_year_from . '-10-01';
$budget_year_to = $budget_year . '-09-30';

//$arr_search_condition[] = "(PER_OCCUPYDATE >= '$budget_year_from' and PER_OCCUPYDATE <= '$budget_year_to')";		//PER_OCCUPYDATE จาก PER_PERSONAL ค่ามันเท่ากันหมด เวลา select มาค่าเท่ากัน ก็ count มาผิดเพราะดึงมาทั้งหมดเลย????
$arr_search_condition[] = "POH_EFFECTIVEDATE >= '$budget_year_from' and POH_EFFECTIVEDATE <= '$budget_year_to'";
$arr_search_condition[] = "PER_OCCUPYDATE >= '$budget_year_from' and PER_OCCUPYDATE <= '$budget_year_to'";

$list_type_text = $ALL_REPORT_TITLE;

if (in_array("PER_ORG_TYPE_1", $list_type)) {
    if ($select_org_structure == 0) {
        if ($DEPARTMENT_ID) {
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
        } // end if
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
        } // end if
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
        } // end if
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
            $arr_search_condition[] = "(e.POH_ORG2 = '$DEPARTMENT_NAME')";
        } elseif ($MINISTRY_ID) {
            $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
            $db_dpis->send_cmd($cmd);
            $data = $db_dpis->get_array();
            $arr_search_condition[] = "(e.POH_ORG1 = '" . $data[ORG_NAME] . "')";
        }
    } else if ($select_org_structure == 1) {
        if ($DEPARTMENT_ID) {
//				$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
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
        } // end if
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
        } // end if
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
        $arr_search_condition[] = "($line_code='$line_search_code')";
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

$company_name = "รูปแบบการออกรายงาน : " . ($select_org_structure == 1 ? "โครงสร้างตามมอบหมายงาน - " : "โครงสร้างตามกฎหมาย - ") . "$list_type_text";
if ($search_mov_code[0] == 1) {
    $arr_mov_code[] = 1;
    $mov_name .= "บรรจุใหม่ ";
}
if ($search_mov_code[0] == 2 || $search_mov_code[1] == 2) {
    $arr_mov_code[] = 10;
    $mov_name .= "รับโอน ";
}
if ($search_mov_code[0] == 3 || $search_mov_code[1] == 3 || $search_mov_code[2] == 3) {
    $arr_mov_code[] = 11;
    $mov_name .= "บรรจุกลับ ";
}
$show_budget_year = (($NUMBER_DISPLAY == 2) ? convert2thaidigit($search_budget_year) : $search_budget_year);
$report_title = "$DEPARTMENT_NAME||รายชื่อ$PERSON_TYPE[$search_per_type] ที่$mov_name ในปีงบประมาณ $show_budget_year";
$report_code = "R0310";
include ("rpt_R003010_format.php"); // กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
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

    $fname = "rpt_R003010_rtf.rtf";

    //	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
    $paper_size = "a4";
    $orientation = 'L';
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
    $orientation = 'L';

    $pdf = new PDF($orientation, $unit, $paper_size, $lang_code, $company_name, $report_title, $report_code, $heading, $heading_width, $heading_align);

    $pdf->Open();
    $pdf->SetMargins(5, 5, 5);
    $pdf->AliasNbPages();
    $pdf->AddPage();  // ขึ้นหน้าใหม่
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont($font, '', 14);

    $page_start_x = $pdf->x;
    $page_start_y = $pdf->y;
}

function list_person($search_condition, $addition_condition) {
    global $DPISDB, $db_dpis2, $db_dpis3, $db_dpis4, $RPT_N, $position_table, $position_join;
    global $arr_rpt_order, $rpt_order_index, $search_per_type, $arr_content, $data_count, $person_count, $arr_mov_code, $DATE_DISPLAY;
    global $select_org_structure;
    global $have_pic, $img_file;

    if (trim($addition_condition)) {
        $search_condition .= (trim($search_condition) ? " and " : " where ") . $addition_condition;
    }
    //ระบุวันก่อนหน้าที่ได้รับโอน หาจากวันสิ้นสุดการดำรงตำแหน่ง
    if (count($arr_mov_code)) {
        $mov_code = " and trim(g.MOV_SUB_TYPE) in (" . implode(" , ", $arr_mov_code) . ")";
    }

    if ($DPISDB == "odbc") {
        $cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE,
												LEFT(trim(a.PER_OCCUPYDATE), 10) as PER_OCCUPYDATE
							 from		(
												(
							 						(
							 							(
															PER_PERSONAL a
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join PER_ORG f on (a.DEPARTMENT_ID=f.ORG_ID)
											) left join PER_MOVMENT g on (e.MOV_CODE=g.MOV_CODE)
							$mov_code
							$search_condition
							group by	a.PER_ID , d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE , a.PER_OCCUPYDATE ";
    } elseif ($DPISDB == "oci8") {
        $search_condition = str_replace(" where ", " and ", $search_condition);
        /* $cmd = " select 			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as
          PER_BIRTHDATE, SUBSTR(trim(a.PER_OCCUPYDATE), 1, 10) as PER_OCCUPYDATE
          from			PER_PERSONAL a, PER_PRENAME d , PER_POSITIONHIS e, PER_ORG f, PER_MOVMENT g
          where		a.PN_CODE=d.PN_CODE(+) and a.PER_ID=e.PER_ID(+) and
          a.DEPARTMENT_ID=f.ORG_ID(+) and e.MOV_CODE=g.MOV_CODE(+)
          $mov_code
          $search_condition
          group by	a.PER_ID , d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE , a.PER_OCCUPYDATE "; *//* เดิม */
        $cmd = " select a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME,
                                    SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE,
                                    SUBSTR(trim(a.PER_OCCUPYDATE), 1, 10) as PER_OCCUPYDATE
                                from PER_PERSONAL a, PER_PRENAME d , PER_POSITIONHIS e, PER_ORG f, PER_MOVMENT g
                                where a.PN_CODE=d.PN_CODE(+)
                                    and a.PER_ID=e.PER_ID(+)
                                    and a.DEPARTMENT_ID=f.ORG_ID(+)
                                    and e.MOV_CODE=g.MOV_CODE(+) $mov_code $search_condition
                                group by a.PER_ID , d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE , a.PER_OCCUPYDATE "; /* Release 5.2.1.5 */
    } elseif ($DPISDB == "mysql") {
        $cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE,
												LEFT(trim(a.PER_OCCUPYDATE), 10) as PER_OCCUPYDATE
							 from		(
												(
							 						(
							 							(
															PER_PERSONAL a
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join PER_ORG f on (a.DEPARTMENT_ID=f.ORG_ID)
											) left join PER_MOVMENT g on (e.MOV_CODE=g.MOV_CODE)
							$mov_code
							$search_condition
							group by	a.PER_ID , d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE , a.PER_OCCUPYDATE ";
    } // end if
    if ($select_org_structure == 1) {
        $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
        $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
    }
    $cnt_per = $db_dpis2->send_cmd($cmd);
    //$db_dpis2->show_error();
    //echo "<br><pre>$cmd ($cnt_per)<br><hr>";
    //die();
    while ($data = $db_dpis2->get_array()) {
        $PER_ID = $data[PER_ID];
        $PN_NAME = trim($data[PN_NAME]);
        $PER_NAME = trim($data[PER_NAME]);
        $PER_SURNAME = trim($data[PER_SURNAME]);


        $PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE], $DATE_DISPLAY);
        $PER_OCCUPYDATE = substr(trim($data[PER_OCCUPYDATE]), 0, 10);



        //หาวุฒิการศึกษาบรรจุ
        $cmd = " select 	a.EN_CODE, b.EN_NAME
							 from 		PER_EDUCATE a, PER_EDUCNAME b
							 where	a.EN_CODE=b.EN_CODE and a.PER_ID=$PER_ID and a.EDU_TYPE like '%1%'
							 order by a.EDU_SEQ desc,a.EN_CODE ";
        $db_dpis3->send_cmd($cmd);
        $data2 = $db_dpis3->get_array();
        $EDU_USE = $data2[EN_NAME];

        //หาวุฒิการศึกษาสูงสุด
        $cmd = " select 	a.EN_CODE, b.EN_NAME
							 from 		PER_EDUCATE a, PER_EDUCNAME b
							 where	a.EN_CODE=b.EN_CODE and a.PER_ID=$PER_ID and a.EDU_TYPE like '%4%'
							 order by a.EDU_SEQ desc,a.EN_CODE ";
        $db_dpis3->send_cmd($cmd);
        $data2 = $db_dpis3->get_array();
        $EDU_MAX = $data2[EN_NAME];

        //หาส่วนราชการเดิม
        if ($DPISDB == "odbc") {
            $BEFORE_MOVDATE = " LEFT(POH_EFFECTIVEDATE,10) = '$PER_OCCUPYDATE' ";

            $cmd = " select 		POH_ID, LEFT(POH_EFFECTIVEDATE,10), LEFT(POH_ENDDATE,10), POH_ORG_TRANSFER, LEVEL_NO,
														PL_CODE, PT_CODE, PN_CODE, EP_CODE, TP_CODE , e.MOV_CODE
								from				PER_POSITIONHIS e, PER_MOVMENT g
								where			PER_ID=$PER_ID and e.MOV_CODE=g.MOV_CODE and $BEFORE_MOVDATE $mov_code
								order by	 	LEFT(POH_EFFECTIVEDATE,10) desc, LEFT(POH_ENDDATE,10) desc ";
        } elseif ($DPISDB == "oci8") {
            //$BEFORE_MOVDATE = " SUBSTR(POH_EFFECTIVEDATE,1,10)	= '$PER_OCCUPYDATE' ";/*เดิม*/
            /* $cmd = "select 		POH_ID, SUBSTR(POH_EFFECTIVEDATE,1,10),SUBSTR(POH_ENDDATE,1,10), POH_ORG_TRANSFER,
              LEVEL_NO, PL_CODE, PT_CODE, PN_CODE, EP_CODE, TP_CODE , e.MOV_CODE
              from 		PER_POSITIONHIS e, PER_MOVMENT g
              where 	PER_ID=$PER_ID and e.MOV_CODE=g.MOV_CODE and $BEFORE_MOVDATE $mov_code
              order by 	SUBSTR(POH_EFFECTIVEDATE,1,10)  desc, SUBSTR(POH_ENDDATE,1,10) desc "; *//* เดิม */
            $cmd = "select 		POH_ID, SUBSTR(POH_EFFECTIVEDATE,1,10),SUBSTR(POH_ENDDATE,1,10), POH_ORG_TRANSFER,
												LEVEL_NO, PL_CODE, PT_CODE, PN_CODE, EP_CODE, TP_CODE , e.MOV_CODE,
                                                                                                e.POH_POS_NO
								from 		PER_POSITIONHIS e, PER_MOVMENT g
								where 	PER_ID=$PER_ID and e.MOV_CODE=g.MOV_CODE  $mov_code
								order by 	SUBSTR(POH_EFFECTIVEDATE,1,10)  desc, SUBSTR(POH_ENDDATE,1,10) desc ";
        } elseif ($DPISDB == "mysql") {
            $BEFORE_MOVDATE = " LEFT(POH_EFFECTIVEDATE,10) = '$PER_OCCUPYDATE' ";
            $cmd = " select 		POH_ID, LEFT(POH_EFFECTIVEDATE,10), LEFT(POH_ENDDATE,10), POH_ORG_TRANSFER, LEVEL_NO,
														PL_CODE, PT_CODE, PN_CODE, EP_CODE, TP_CODE , e.MOV_CODE
								from				PER_POSITIONHIS  e, PER_MOVMENT g
								where			PER_ID=$PER_ID and e.MOV_CODE=g.MOV_CODE and $BEFORE_MOVDATE $mov_code
								order by	 	LEFT(POH_EFFECTIVEDATE,10) desc, LEFT(POH_ENDDATE,10) desc ";
        } // end if
        $count_poh = $db_dpis3->send_cmd($cmd);
        //$db_dpis3->show_error();
        //echo "<pre><br>$cmd ($count_poh)<br>";
        $data2 = $db_dpis3->get_array();
        $OLD_ORG = $data2[POH_ORG_TRANSFER];
        $LEVEL_NO = $data2[LEVEL_NO];
        $PL_CODE = $data2[PL_CODE];
        $POH_POS_NO = $data2[POH_POS_NO];
        $PT_CODE = trim($data[PT_CODE]);
        $PN_CODE = $data2[PN_CODE];
        $EP_CODE = $data2[EP_CODE];
        $TP_CODE = $data2[TP_CODE];
        $MOV_CODE = $data2[MOV_CODE];

        $cmd = " select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
        $db_dpis3->send_cmd($cmd);
        $data2 = $db_dpis3->get_array();
        $POSITION_TYPE = trim($data2[POSITION_TYPE]);
        $LEVEL_NAME = trim($data2[POSITION_LEVEL]);

        if ($search_per_type == 1) {
            $cmd = "select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
            $db_dpis3->send_cmd($cmd);
            $data2 = $db_dpis3->get_array();
            $PL_NAME = trim($data2[PL_NAME]);

            $cmd = "select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE' ";
            $db_dpis3->send_cmd($cmd);
            $data2 = $db_dpis3->get_array();
            $PT_NAME = trim($data[PT_NAME]);

            if ($RPT_N) {
                $PL_NAME = (trim($PM_NAME) ? "$PM_NAME (" : "") . (trim($PL_NAME) ? "$PL_NAME$LEVEL_NAME" : "") . (trim($PM_NAME) ? ")" : "");
            } else {
                $PL_NAME = (trim($PM_NAME) ? "$PM_NAME (" : "") . (trim($PL_NAME) ? ($PL_NAME . " " . level_no_format($LEVEL_NO) . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6) ? "$PT_NAME" : "")) : "") . (trim($PM_NAME) ? ")" : "");
            }
        } elseif ($search_per_type == 2) {
            $cmd = "select PN_NAME from PER_POS_NAME where PN_CODE='$PN_CODE' ";
            $db_dpis3->send_cmd($cmd);
            $data2 = $db_dpis3->get_array();
            $PL_NAME = trim($data2[PN_NAME]);
        } elseif ($search_per_type == 3) {
            $cmd = "select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE='$EP_CODE' ";
            $db_dpis3->send_cmd($cmd);
            $data2 = $db_dpis3->get_array();
            $PL_NAME = trim($data2[EP_NAME]);
        } elseif ($search_per_type == 4) {
            $cmd = "select TP_NAME from PER_TEMP_POS_NAME where TP_CODE='$TP_CODE' ";
            $db_dpis3->send_cmd($cmd);
            $data2 = $db_dpis3->get_array();
            $PL_NAME = trim($data2[TP_NAME]);
        }
        $PER_OCCUPYDATE = show_date_format($PER_OCCUPYDATE, $DATE_DISPLAY);

        if ($have_pic) {
            $img_file = show_image($PER_ID, 2);
        } //1 = $db_dpis2,$db_dpis3 2 = $db_dpis3,$db_dpis4

        if ($count_poh) {

            $data_count++;
            $person_count++;
            $arr_content[$data_count][type] = "CONTENT";
            $arr_content[$data_count][order] = $person_count . ". ";
            if ($have_pic && $img_file) {
                if ($FLAG_RTF) {
                    $arr_content[$data_count][image] = "<*img*" . $img_file . ",15*img*>";
                } // ,ตัวเลขหลัง comma คือ image ratio
                else {
                    $arr_content[$data_count][image] = "<*img*" . $img_file . ",4*img*>";
                }
            }
            $arr_content[$data_count][prename] = $PN_NAME;
            $arr_content[$data_count][name] = $PER_NAME . " " . $PER_SURNAME;
            $arr_content[$data_count][pos_no] = $POH_POS_NO;
            $arr_content[$data_count][position] = $PL_NAME;
            $arr_content[$data_count][position_type] = $POSITION_TYPE;
            $arr_content[$data_count][educate_use] = $EDU_USE;
            $arr_content[$data_count][educate_max] = $EDU_MAX;
            $arr_content[$data_count][birthdate] = $PER_BIRTHDATE;
            $arr_content[$data_count][posdate] = $PER_OCCUPYDATE;
            $arr_content[$data_count][old_org] = $OLD_ORG;  //ส่วนราชการเดิมก่อนโอน
            $arr_content[$data_count][reason] = ""; // $MOV_NAME;
        }
    } // end while
}

// function

function generate_condition($current_index) {
    global $DPISDB, $db_dpis, $arr_rpt_order, $search_per_type, $select_org_structure;
    global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2, $MINISTRY_NAME, $DEPARTMENT_NAME,
    $ORG_NAME, $ORG_NAME_1, $ORG_NAME_2, $PL_CODE, $PN_CODE, $PER_GENDER, $PV_CODE, $EM_CODE, $EL_CODE, $EP_CODE, $TP_CODE, $ORG_BKK_TITLE;
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
    global $arr_rpt_order, $arr_mov_code;
    global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2;

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
        } // end switch case
    } // end for
}

// function
//แสดงรายชื่อหน่วยงาน
if (count($arr_mov_code)) {
    $mov_code = " and trim(g.MOV_SUB_TYPE) in (" . implode(" , ", $arr_mov_code) . ")";
}
if ($DPISDB == "odbc") {
    $cmd = " select			distinct $select_list , a.PER_ID
						 from	(
										(
						 					(
												PER_PERSONAL a
											) left join PER_ORG f on (a.DEPARTMENT_ID=f.ORG_ID)
										) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID and LEFT(a.PER_OCCUPYDATE,10) = LEFT(e.POH_EFFECTIVEDATE,10))
									) left join PER_MOVMENT g on (e.MOV_CODE=g.MOV_CODE)
											$search_condition $mov_code
								 group by 	$order_by , a.PER_ID
								 order by		$order_by , a.PER_ID";
} elseif ($DPISDB == "oci8") {
    $search_condition = str_replace(" where ", " and ", $search_condition);

    /* $cmd = " select			distinct $select_list , a.PER_ID
      from			PER_PERSONAL a, PER_POSITIONHIS e, PER_ORG f, PER_MOVMENT g
      where		a.PER_ID=e.PER_ID(+) and a.DEPARTMENT_ID=f.ORG_ID(+) and
      e.MOV_CODE=g.MOV_CODE(+) and SUBSTR(a.PER_OCCUPYDATE,1,10) = SUBSTR(e.POH_EFFECTIVEDATE,1,10)
      $search_condition $mov_code
      group by 	$order_by , a.PER_ID
      order by		$order_by , a.PER_ID"; */

    $cmd = " select distinct $select_list , a.PER_ID
                    from PER_PERSONAL a, PER_POSITIONHIS e, PER_ORG f, PER_MOVMENT g
                    where a.PER_ID=e.PER_ID(+) and a.DEPARTMENT_ID=f.ORG_ID(+)
                    and e.MOV_CODE=g.MOV_CODE(+)
                        $search_condition $mov_code
                    group by $order_by , a.PER_ID
                    order by $order_by , a.PER_ID";
} elseif ($DPISDB == "mysql") {
    $cmd = " select			distinct $select_list , a.PER_ID
						 from	(
										(
						 					(
												PER_PERSONAL a
											) left join PER_ORG f on (a.DEPARTMENT_ID=f.ORG_ID)
										) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID and LEFT(a.PER_OCCUPYDATE,10) = LEFT(e.POH_EFFECTIVEDATE,10))
									) left join PER_MOVMENT g on (e.MOV_CODE=g.MOV_CODE)
											$search_condition $mov_code
								 group by 	$order_by , a.PER_ID
								 order by		$order_by , a.PER_ID";
} // end if
if ($select_org_structure == 1) {
    $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
    $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
}
//echo '<pre>';echo $cmd;
$count_data = $db_dpis->send_cmd($cmd);


$data_count = 0;
$person_count = 0;
$start_rpt_order = ($f_all ? 0 : 1);
initialize_parameter(0);
//var_dump($arr_rpt_order);
while ($data = $db_dpis->get_array()) {
    if (!($MINISTRY_ID == $DEPARTMENT_ID && $DEPARTMENT_ID == 1)) {
        for ($rpt_order_index = 0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++) {
            $REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
            switch ($REPORT_ORDER) {
                case "MINISTRY" :
                    if ($MINISTRY_ID != trim($data[MINISTRY_ID])) {
                        $MINISTRY_ID = trim($data[MINISTRY_ID]);
                        if ($MINISTRY_ID != "" || $MINISTRY_ID != 0 || $MINISTRY_ID != -1) {
                            /* $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
                              $db_dpis2->send_cmd($cmd);
                              $data2 = $db_dpis2->get_array();
                              $MINISTRY_NAME = $data2[ORG_NAME]; */
                            $MINISTRY_NAME = $MINISTRY_ID;    //ใน db ฟิล์ดนี้เก็บเป็นตัวอักษร NAME
                            if ($f_all) {
                                $addition_condition = generate_condition($rpt_order_index);

                                $arr_content[$data_count][type] = "MINISTRY";
                                $arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $start_rpt_order) * 5)) . $MINISTRY_NAME;

//									if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
                                if ($rpt_order_index == (count($arr_rpt_order) - 1)) {
                                    initialize_parameter($rpt_order_index + 1);
//										echo "MINISTRY--list_person - ($search_condition, $addition_condition)<br>";
                                    list_person($search_condition, $addition_condition);
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
                            /* $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
                              $db_dpis2->send_cmd($cmd);
                              $data2 = $db_dpis2->get_array();
                              $DEPARTMENT_NAME = $data2[ORG_NAME]; */
                            $DEPARTMENT_NAME = $DEPARTMENT_ID;    //ใน db ฟิล์ดนี้เก็บเป็นตัวอักษร NAME

                            $addition_condition = generate_condition($rpt_order_index);

                            $arr_content[$data_count][type] = "DEPARTMENT";
                            $arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $start_rpt_order) * 5)) . $DEPARTMENT_NAME;

//								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
                            //echo "$rpt_order_index == (".count($arr_rpt_order)." - 1)";
                            if ($rpt_order_index == (count($arr_rpt_order) - 1)) {
                                initialize_parameter($rpt_order_index + 1);
//									echo "DEPARTMENT--list_person - ($search_condition, $addition_condition)<br>";
                                list_person($search_condition, $addition_condition);
                            }
                            $data_count++;
                        } // end if($DEPARTMENT_ID != "" || $DEPARTMENT_ID!=0 || $DEPARTMENT_ID!=-1)
                    } // end if
                    break;

                case "ORG" :
                    if ($ORG_ID != $data[ORG_ID]) {
                        $ORG_ID = $data[ORG_ID];
                        if ($ORG_ID != "" || $ORG_ID != 0 || $ORG_ID != -1) {
                            if ($select_org_structure == 0) {
                                $ORG_NAME = $ORG_ID;
                            } elseif ($select_org_structure == 1) {
                                $cmd = " select ORG_NAME, ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID ";
                                $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                                $db_dpis2->send_cmd($cmd);
                                $data2 = $db_dpis2->get_array();
                                $ORG_NAME = $data2[ORG_NAME];
                                $ORG_SHORT = $data2[ORG_SHORT];
                            }
                            if ($ORG_NAME == "-") {
                                $ORG_NAME = $ORG_BKK_TITLE;
                            }

                            if (($ORG_NAME != "" && $ORG_NAME != "-") || ($BKK_FLAG == 1 && $ORG_NAME != "" && $ORG_NAME != "-")) {
                                //echo "<br>$rpt_order_index // $ORG_ID : $ORG_NAME  // PER_ID : $data[PER_ID]<br>";
                                $addition_condition = generate_condition($rpt_order_index);

                                $arr_content[$data_count][type] = "ORG";
                                $arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $start_rpt_order) * 5)) . $ORG_NAME;

//										if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
                                if ($rpt_order_index == (count($arr_rpt_order) - 1)) {
                                    initialize_parameter($rpt_order_index + 1);
//											echo "ORG--list_person - ($search_condition, $addition_condition)<br>";
                                    list_person($search_condition, $addition_condition);
                                }
                                $data_count++;
                            } // end if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
                        } // end if($ORG_ID != "" || $ORG_ID != 0 || $ORG_ID != -1)
                    }
                    break;

                case "ORG_1" :
                    if ($ORG_ID_1 != $data[ORG_ID_1]) {
                        $ORG_ID_1 = $data[ORG_ID_1];
                        $ORG_NAME_1 = $ORG_SHORT_1 = "ไม่ระบุ";
                        if ($ORG_ID_1 != "" || $ORG_ID_1 != 0 || $ORG_ID_1 != -1) {
                            $cmd = " select ORG_NAME, ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_1 ";
                            if ($select_org_structure == 1) {
                                $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                            }
                            $db_dpis2->send_cmd($cmd);
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
                            $arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $start_rpt_order) * 5)) . ($ORG_NAME_1 == "ไม่ระบุ" ? "" : $ORG_NAME_1);

//								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
//								echo "ORG_1--list_person - ($search_condition, $addition_condition)<br>";
//								list_person($search_condition, $addition_condition);
                            if ($rpt_order_index == (count($arr_rpt_order) - 1)) {
                                initialize_parameter($rpt_order_index + 1);
//									echo "ORG_1--list_person - ($search_condition, $addition_condition)<br>";
                                list_person($search_condition, $addition_condition);
                            }
                            $data_count++;
                        } // end if(($ORG_NAME_1 !="" && $ORG_NAME_1 !="-") || ($BKK_FLAG==1 && $ORG_NAME_1 !="" && $ORG_NAME_1 !="-"))
                    }
                    break;

                case "ORG_2" :
                    if ($ORG_ID_2 != $data[ORG_ID_2]) {
                        $ORG_ID_2 = $data[ORG_ID_2];
                        $ORG_NAME_2 = $ORG_SHORT_2 = "ไม่ระบุ";
                        if ($ORG_ID_2 != "" || $ORG_ID_2 != 0 || $ORG_ID_2 != -1) {
                            $cmd = " select ORG_NAME, ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_2 ";
                            if ($select_org_structure == 1) {
                                $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                            }
                            $db_dpis2->send_cmd($cmd);
                            $data2 = $db_dpis2->get_array();
                            $ORG_NAME_2 = $data2[ORG_NAME];
                            $ORG_SHORT = $data2[ORG_SHORT];
                            if ($ORG_NAME_2 == "-") {
                                $ORG_NAME_2 = $ORG_BKK_TITLE;
                            }
                        } // end if($ORG_ID_2!= "" || $ORG_ID_2 != 0 || $ORG_ID_2 != -1)

                        if (($ORG_NAME_2 != "" && $ORG_NAME_2 != "-") || ($BKK_FLAG == 1 && $ORG_NAME_2 != "" && $ORG_NAME_2 != "-")) {
                            $addition_condition = generate_condition($rpt_order_index);

                            $arr_content[$data_count][type] = "ORG_2";
                            $arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $start_rpt_order) * 5)) . ($ORG_NAME_2 == "ไม่ระบุ" ? "" : $ORG_NAME_2);

//								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
//								echo "ORG_2--list_person - ($search_condition, $addition_condition)<br>";
//								list_person($search_condition, $addition_condition);
                            if ($rpt_order_index == (count($arr_rpt_order) - 1)) {
                                initialize_parameter($rpt_order_index + 1);
//									echo "ORG_2--list_person - ($search_condition, $addition_condition)<br>";
                                list_person($search_condition, $addition_condition);
                            }
                            $data_count++;
                        } // end if(($ORG_NAME_2 !="" && $ORG_NAME_2 !="-") || ($BKK_FLAG==1 && $ORG_NAME_2 !="" && $ORG_NAME_2 !="-"))
                    }
                    break;
            } //end switch
        } // end for
//		if(count($arr_rpt_order) == 0) list_person($search_condition, $addition_condition);
    } // end if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1))
} // end while
//echo "<pre>"; print_r($arr_content); echo "</pre>";

$head_text1 = implode(",", $heading_text);
$head_width1 = implode(",", $heading_width);
$head_align1 = implode(",", $heading_align);
$col_function = implode(",", $column_function);
if ($FLAG_RTF) {
    $RTF->add_header("", 0, false); // header default
    $RTF->add_footer("", 0, false);  // footer default
    //	echo "$head_text1<br>";
    $tab_align = "center";
    $result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
} else {
    $pdf->AutoPageBreak = false;
    $result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
}
if (!$result) {
    echo "****** error ****** on open table for $table<br>";
}

if ($count_data) {
    for ($data_count = 0; $data_count < count($arr_content); $data_count++) {
        $REPORT_ORDER = $arr_content[$data_count][type];
        $ORDER = $arr_content[$data_count][order];
        if ($have_pic && $img_file) {
            $IMAGE = $arr_content[$data_count][image];
        }
        $PRE_NAME = $arr_content[$data_count][prename];
        $NAME = $arr_content[$data_count][name];
        $POS_NO = $arr_content[$data_count][pos_no];
        $POSITION = $arr_content[$data_count][position];
        $POSITION_TYPE = $arr_content[$data_count][position_type];
        $EDU_USE = $arr_content[$data_count][educate_use];
        $EDU_MAX = $arr_content[$data_count][educate_max];
        $BIRTHDATE = $arr_content[$data_count][birthdate];
        $POSTDATE = $arr_content[$data_count][posdate];
        $OLD_ORG = $arr_content[$data_count][old_org];
        $MOV_NAME = $arr_content[$data_count][reason];
//			echo "$REPORT_ORDER | $ORDER | $NAME | $POSITION<br>";

        $arr_data = (array) null;
        if ($REPORT_ORDER == "CONTENT") {
            $arr_data[] = $ORDER;
            if ($have_pic && $img_file) {
                $arr_data[] = $IMAGE;
            }
            $arr_data[] = $PRE_NAME;
            $arr_data[] = $NAME;
            $arr_data[] = $POS_NO;
            $arr_data[] = $POSITION;
            $arr_data[] = $POSITION_TYPE;
            $arr_data[] = $EDU_USE;
            $arr_data[] = $EDU_MAX;
            $arr_data[] = $BIRTHDATE;
            $arr_data[] = $POSTDATE;
            $arr_data[] = $OLD_ORG;
            $arr_data[] = $MOV_NAME;
        }else {
            if ($have_pic && $img_file) {
                $arr_data[] = "<**1**>$NAME";
            }
            $arr_data[] = "<**1**>$NAME";
            $arr_data[] = "<**1**>$NAME";
            $arr_data[] = "<**1**>$NAME";
            $arr_data[] = "<**1**>$NAME";
            $arr_data[] = "<**1**>$NAME";
            $arr_data[] = "<**1**>$NAME";
            $arr_data[] = "<**1**>$NAME";
            $arr_data[] = "<**1**>$NAME";
            $arr_data[] = "<**1**>$NAME";
            $arr_data[] = "<**1**>$NAME";
            $arr_data[] = "<**1**>$NAME";
        }
        if ($FLAG_RTF) {
            $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
        } else {
            $result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
        }  //TRHBL
        if (!$result) {
            echo "****** error ****** add data to table at record count = $data_count <br>";
        }
    } // end for
    if (!$FLAG_RTF) {
        $pdf->add_data_tab("", 7, "RHBL", $data_align, "", "12", "", "000000", "");
    }  // เส้นปิดบรรทัด
}else {
    if ($FLAG_RTF){
        $result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
    }
    else {
        $result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
    }
    if (!$result){
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