<?php
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

    //แปลง ตัวเลข อาราบิก to ไทย
    function thainumDigit($chk_num,$num){
        if($chk_num=='1'){
            return str_replace(
                array( "๐" , "๑" , "๒" , "๓" , "๔" , "๕" , "๖" , "๗" , "๘" , "๙" ),
                array( '0' , '1' , '2' , '3' , '4' , '5' , '6' ,'7' , '8' , '9' ),$num
            );
        }else{
            return str_replace(
                array( '0' , '1' , '2' , '3' , '4' , '5' , '6' ,'7' , '8' , '9' ),
                array( "๐" , "๑" , "๒" , "๓" , "๔" , "๕" , "๖" , "๗" , "๘" , "๙" ),$num
            );
        }
    }
    include("../../php_scripts/connect_database.php");
    include("../../php_scripts/calendar_data.php");
    include ("../php_scripts/function_share.php");
    include ("../report/rpt_function.php");
        
    $db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis7 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        
    //if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
    // new 
    
    require_once "../../Excel/class.writeexcel_workbook.inc.php";
    require_once "../../Excel/class.writeexcel_worksheet.inc.php";

    ini_set("max_execution_time", $max_execution_time);

    //$DOC_NAME = 'test';
    $report_title = $DOC_NAME;
    $DOC_NAME1 = str_replace("/","_",$DOC_NAME);
    $DOC_NAME2 = str_replace("\\","_",$DOC_NAME1);
    $DOC_NAME3 = str_replace(":","_",$DOC_NAME2);
    $DOC_NAME4 = str_replace("*","_",$DOC_NAME3);
    $DOC_NAME5 = str_replace("|","_",$DOC_NAME4);
    $DOC_NAME6 = str_replace("?","_",$DOC_NAME5);
    $DOC_NAME7 = str_replace('"',"_",$DOC_NAME6);
    $DOC_NAME8 = str_replace('<',"_",$DOC_NAME7);
    $DOC_NAME9 = str_replace('>',"_",$DOC_NAME8);
    
    //$str = preg_replace('/[^A-Za-z0-9-]/','_', $DOC_NAME);
    $report_code = "$DOC_NAME9";

    $token = md5(uniqid(rand(), true)); 
    $fname= "../../Excel/tmp/dpis_$token.xls";

    $workbook = new writeexcel_workbook($fname);
    $worksheet = &$workbook->addworksheet("$report_code");
    $worksheet->set_margin_right(0.50);
    $worksheet->set_margin_bottom(1.10);

    //====================== SET FORMAT ======================//
    require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
    //====================== SET FORMAT ======================//

    $cmd =  "   SELECT p.* from (
                    SELECT 
                        CASE WHEN b.PER_CARDNO IS NOT NULL THEN b.PER_CARDNO ELSE a.PER_CARDNO END as CARD_NO_PERSON , 
                        c.PN_NAME||b.PER_NAME||' '||b.PER_SURNAME as FULL_NAME, a.POH_LAST_POSITION, a.POH_POS_NO, substr(a.POH_EFFECTIVEDATE,1,10) as POH_EFFECTIVEDATE , 
                        d.MOV_NAME, a.POH_SALARY, a.POH_SALARY_POS, a.POH_DOCNO ,  substr(a.POH_DOCDATE,1,10) as POH_DOCDATE , a.POH_SEQ_NO,'พิมพ์' as PRINT_ON_REPORT, 
                        CASE WHEN a.POH_PM_NAME IS NOT NULL THEN a.POH_PM_NAME ELSE j.PM_NAME END AS POH_PM_NAME ,
                        CASE WHEN 
                            e.PT_NAME IS NOT NULL and e.PT_NAME != 'ทั่วไป' and trim(k.POSITION_LEVEL) >= '6' AND  trim(k.POSITION_LEVEL) <= '9' THEN e.PT_NAME ELSE '' END PT_NAME ,
                        CASE WHEN 
                          a.POH_LEVEL_NO IS NOT NULL AND (a.POH_LEVEL_NO = 'D1' OR a.POH_LEVEL_NO = 'D2' OR a.POH_LEVEL_NO = 'M1' OR a.POH_LEVEL_NO = 'M2') THEN k.POSITION_TYPE||k.POSITION_LEVEL
                        ELSE 
                            CASE WHEN a.POH_LEVEL_NO IS NOT NULL THEN k.POSITION_LEVEL ELSE '' END 
                        END AS POH_LEVEL_NO ,
                        a.POH_ORG1 as ORG_NAME_1, a.POH_ORG2 as ORG_NAME_2, a.POH_ORG3 as ORG_NAME_3, a.POH_UNDER_ORG1, 
                        a.POH_UNDER_ORG2,  a.POH_PL_NAME ,  a.POH_ORG, a.POH_REMARK1||' '||POH_REMARK2 as POH_REMARK , 
                        CASE b.PER_TYPE 
                          WHEN 1 THEN CASE WHEN f.PL_NAME IS NOT NULL THEN f.PL_NAME ELSE a.PL_CODE END
                          WHEN 2 THEN CASE WHEN g.PN_NAME IS NOT NULL THEN g.PN_NAME ELSE a.POH_PL_NAME END
                          WHEN 3 THEN CASE WHEN h.EP_NAME IS NOT NULL THEN h.EP_NAME ELSE a.POH_PL_NAME END
                          WHEN 4 THEN CASE WHEN i.TP_NAME IS NOT NULL THEN i.TP_NAME ELSE a.POH_PL_NAME END
                        ELSE '' END as PL_NAME , a.POH_CMD_SEQ
                    FROM PER_POSITIONHIS   a 
                    LEFT JOIN PER_PERSONAL b ON (a.PER_ID = b.PER_ID)
                    LEFT JOIN PER_PRENAME  c ON (b.PN_CODE = c.PN_CODE)
                    LEFT JOIN PER_MOVMENT  d ON (a.MOV_CODE = d.MOV_CODE)
                    LEFT JOIN PER_TYPE     e ON (a.PT_CODE = e.PT_CODE)
                    LEFT JOIN PER_LINE     f ON (a.PL_CODE = f.PL_CODE)
                    LEFT JOIN PER_POS_NAME g ON (a.PN_CODE = g.PN_CODE)
                    LEFT JOIN PER_EMPSER_POS_NAME  h ON (a.EP_CODE = h.EP_CODE)
                    LEFT JOIN PER_TEMP_POS_NAME    i ON (a.TP_CODE = i.TP_CODE)
                    LEFT JOIN PER_MGT      j ON (a.PM_CODE = j.PM_CODE)
                    LEFT JOIN PER_LEVEL   k ON (a.POH_LEVEL_NO = k.LEVEL_NO)
                    WHERE  trim(a.POH_DOCNO) =  trim('$DOC_NAME')
                ) p ORDER BY p.POH_CMD_SEQ ,p.CARD_NO_PERSON 
            ";
    $db_dpis->send_cmd($cmd);
    $cnt_data = $db_dpis->num_rows();
    $db_dpis1->send_cmd($cmd);
    if($cnt_data){
        $chk_i=0;
        $data_count=0;
        $TEMP_PER_ID = 0;
        $TEMP_POS_CODE = '';
        $TEMP_ORG_ID = 0;
        
        $xlsRow = 0;
        $worksheet->write($xlsRow, 0, "ส่งออกคำสั่งเลขที่ : ".$report_title, set_format("xlsFmtTitle", "B", "C", "", 1));
        /*$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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
        $worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 22, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 23, "", set_format("xlsFmtTitle", "B", "C", "", 1));*/
        $xlsRow++;
         
        $worksheet->set_column(0, 0, 20);
        $worksheet->set_column(1, 1, 25);
        $worksheet->set_column(2, 2, 10);
        $worksheet->set_column(3, 3, 11);
        $worksheet->set_column(4, 4, 15);
        $worksheet->set_column(5, 5, 25);
        $worksheet->set_column(6, 6, 14);
        $worksheet->set_column(7, 7, 16);
        $worksheet->set_column(8, 8, 20);
        $worksheet->set_column(9, 9, 15);
        $worksheet->set_column(10,10, 20);
        $worksheet->set_column(11,11, 15);
        $worksheet->set_column(12,12, 30);
        $worksheet->set_column(13,13, 30);
        $worksheet->set_column(14,14, 25);
        $worksheet->set_column(15,15, 20);
        $worksheet->set_column(16,16, 40);
        $worksheet->set_column(17,17, 40);
        $worksheet->set_column(18,18, 40);
        $worksheet->set_column(19,19, 40);
        $worksheet->set_column(20,20, 40);
        $worksheet->set_column(21,21, 40);
        $worksheet->set_column(22,22, 40);
        $worksheet->set_column(23,23, 40);
        //set_format($fmtName, $fontFormat="", $alignment="C", $border="", $isMerge=0, $wrapText=0, $rotate=0, $color="", $bgcolor="")
        $worksheet->write($xlsRow, 0, 'เลขประจำตัวประชาชน' , set_format("xlsFmtTitle", "B", "C", "TRBL", 0, 0, 0, "000000", ""));
        $worksheet->write($xlsRow, 1, 'ชื่อ-นามสกุล', set_format("xlsFmtTitle", "B", "C", "TRBL", 0, 0, 0, "000000", ""));
        $worksheet->write($xlsRow, 2, 'คำสั่งล่าสุด', set_format("xlsFmtTitle", "B", "C", "TRBL", 0, 0, 0, "000000", ""));
        $worksheet->write($xlsRow, 3, 'เลขที่ตำแหน่ง', set_format("xlsFmtTitle", "B", "C", "TRBL", 0, 0, 0, "000000", ""));
        $worksheet->write($xlsRow, 4, 'วันที่มีผล', set_format("xlsFmtTitle", "B", "C", "TRBL", 0, 0, 0, "000000", ""));
        $worksheet->write($xlsRow, 5, 'ประเภทการเคลื่อนไหว', set_format("xlsFmtTitle", "B", "C", "TRBL", 0, 0, 0, "000000", ""));
        $worksheet->write($xlsRow, 6, 'อัตราเงินเดือน', set_format("xlsFmtTitle", "B", "C", "TRBL", 0, 0, 0, "000000", ""));
        $worksheet->write($xlsRow, 7, 'เงินประจำตำแหน่ง', set_format("xlsFmtTitle", "B", "C", "TRBL", 0, 0, 0, "000000", ""));
        $worksheet->write($xlsRow, 8, 'เลขที่คำสั่ง', set_format("xlsFmtTitle", "B", "C", "TRBL", 0, 0, 0, "000000", ""));        
        $worksheet->write($xlsRow, 9, 'วันที่ออกคำสั่ง', set_format("xlsFmtTitle", "B", "C", "TRBL", 0, 0, 0, "000000", ""));
        $worksheet->write($xlsRow, 10, 'ลำดับที่กรณีวันที่เดียวกัน', set_format("xlsFmtTitle", "B", "C", "TRBL", 0, 0, 0, "000000", ""));
        $worksheet->write($xlsRow, 11, 'พิมพ์ในรายงาน', set_format("xlsFmtTitle", "B", "C", "TRBL", 0, 0, 0, "000000", ""));
        $worksheet->write($xlsRow, 12, 'ตำแหน่งในสายงาน', set_format("xlsFmtTitle", "B", "C", "TRBL", 0, 0, 0, "000000", ""));
        $worksheet->write($xlsRow, 13, 'ตำแหน่งในการบริหาร', set_format("xlsFmtTitle", "B", "C", "TRBL", 0, 0, 0, "000000", ""));
        $worksheet->write($xlsRow, 14, 'ประเภทตำแหน่งตาม พรบ. 2535', set_format("xlsFmtTitle", "B", "C", "TRBL", 0, 0, 0, "000000", ""));
        $worksheet->write($xlsRow, 15, 'ระดับของผู้ดำรงตำแหน่ง', set_format("xlsFmtTitle", "B", "C", "TRBL", 0, 0, 0, "000000", ""));
        $worksheet->write($xlsRow, 16, 'กระทรวง', set_format("xlsFmtTitle", "B", "C", "TRBL", 0, 0, 0, "000000", ""));
        $worksheet->write($xlsRow, 17, 'กรม', set_format("xlsFmtTitle", "B", "C", "TRBL", 0, 0, 0, "000000", ""));
        $worksheet->write($xlsRow, 18, 'สำนัก / กอง', set_format("xlsFmtTitle", "B", "C", "TRBL", 0, 0, 0, "000000", ""));
        $worksheet->write($xlsRow, 19, 'ต่ำกว่าสำนัก / กอง 1 ระดับ', set_format("xlsFmtTitle", "B", "C", "TRBL", 0, 0, 0, "000000", ""));
        $worksheet->write($xlsRow, 20, 'ต่ำกว่าสำนัก / กอง 2 ระดับ', set_format("xlsFmtTitle", "B", "C", "TRBL", 0, 0, 0, "000000", ""));
        $worksheet->write($xlsRow, 21, 'ตำแหน่ง ก.พ. 7', set_format("xlsFmtTitle", "B", "C", "TRBL", 0, 0, 0, "000000", ""));
        $worksheet->write($xlsRow, 22, 'ส่วนราชการ  ก.พ. 7', set_format("xlsFmtTitle", "B", "C", "TRBL", 0, 0, 0, "000000", ""));
        $worksheet->write($xlsRow, 23, 'หมายเหตุ', set_format("xlsFmtTitle", "B", "C", "TRBL", 0, 0, 0, "000000", ""));
        $xlsRow++;
        
        $irow = $xlsRow+1;
        $start_irow = 4;
        $SETFONT_B = array();
        while($data = $db_dpis1->get_array()){
            $TEMP_CARD_NO_PERSON = $data[CARD_NO_PERSON];
            $TEMP_FULL_NAME = $data[FULL_NAME];
            $TEMP_POH_LAST_POSITION = ($data[POH_LAST_POSITION]=='N')?'ไม่ใช่':'ใช่';
            $TEMP_POH_POS_NO = $data[POH_POS_NO];
            if($data[POH_EFFECTIVEDATE]){
                $ARR_EFF = explode("-",$data[POH_EFFECTIVEDATE]);
                $TEMP_POH_EFFECTIVEDATE = $ARR_EFF[2].'/'.$ARR_EFF[1].'/'.($ARR_EFF[0]+543);
            }else{
                $TEMP_POH_EFFECTIVEDATE = '';
            }
            $TEMP_MOV_NAME = $data[MOV_NAME];
            $TEMP_POH_SALARY = ($data[POH_SALARY]!='0')?$data[POH_SALARY]:'';
            $TEMP_POH_SALARY_POS = ($data[POH_SALARY_POS]!='0')?$data[POH_SALARY_POS]:'';
            $TEMP_POH_DOCNO = $data[POH_DOCNO];
            if($data[POH_DOCDATE]){
                $ARR_EFF2 = explode("-",$data[POH_DOCDATE]);
                $TEMP_POH_DOCDATE = $ARR_EFF2[2].'/'.$ARR_EFF2[1].'/'.($ARR_EFF2[0]+543);
            }else{
                $TEMP_POH_DOCDATE = '';
            }
            $TEMP_POH_SEQ_NO = $data[POH_SEQ_NO];
            $TEMP_PRINT_ON_REPORT = $data[PRINT_ON_REPORT];
            $TEMP_PL_NAME = $data[PL_NAME];
            $TEMP_POH_PM_NAME = $data[POH_PM_NAME];
            $TEMP_PT_NAME = $data[PT_NAME];
            $TEMP_POH_LEVEL_NO = $data[POH_LEVEL_NO];
            $TEMP_ORG_NAME_1 = $data[ORG_NAME_1];
            $TEMP_ORG_NAME_2 = $data[ORG_NAME_2];
            $TEMP_ORG_NAME_3 = $data[ORG_NAME_3];
            $TEMP_POH_UNDER_ORG1 = $data[POH_UNDER_ORG1];
            $TEMP_POH_UNDER_ORG2 = $data[POH_UNDER_ORG2];
            $TEMP_POH_PL_NAME = $data[POH_PL_NAME];
            $TEMP_POH_ORG = $data[POH_ORG];
            $TEMP_POH_REMARK = $data[POH_REMARK];
            $data_count++;

            $worksheet->write_string($xlsRow, 0, $TEMP_CARD_NO_PERSON , set_format("xlsFmtTitle", "", "L", "TRBL", 0, 0, 0, "000000", ""));
            $worksheet->write($xlsRow, 1, $TEMP_FULL_NAME, set_format("xlsFmtTitle", "", "L", "TRBL", 0, 0, 0, "000000", ""));
            $worksheet->write($xlsRow, 2, $TEMP_POH_LAST_POSITION, set_format("xlsFmtTitle", "", "L", "TRBL", 0, 0, 0, "000000", ""));
            $worksheet->write($xlsRow, 3, $TEMP_POH_POS_NO, set_format("xlsFmtTitle", "", "L", "TRBL", 0, 0, 0, "000000", ""));
            $worksheet->write($xlsRow, 4, $TEMP_POH_EFFECTIVEDATE, set_format("xlsFmtTitle", "", "R", "TRBL", 0, 0, 0, "000000", ""));
            $worksheet->write($xlsRow, 5, $TEMP_MOV_NAME, set_format("xlsFmtTitle", "", "L", "TRBL", 0, 0, 0, "000000", ""));
            $worksheet->write($xlsRow, 6, $TEMP_POH_SALARY, set_format("xlsFmtTitle", "", "R", "TRBL", 0, 0, 0, "000000", ""));
            $worksheet->write($xlsRow, 7, $TEMP_POH_SALARY_POS, set_format("xlsFmtTitle", "", "R", "TRBL", 0, 0, 0, "000000", ""));
            $worksheet->write($xlsRow, 8, $TEMP_POH_DOCNO, set_format("xlsFmtTitle", "", "L", "TRBL", 0, 0, 0, "000000", ""));        
            $worksheet->write($xlsRow, 9, $TEMP_POH_DOCDATE, set_format("xlsFmtTitle", "", "R", "TRBL", 0, 0, 0, "000000", ""));
            $worksheet->write($xlsRow, 10,$TEMP_POH_SEQ_NO, set_format("xlsFmtTitle", "", "L", "TRBL", 0, 0, 0, "000000", ""));
            $worksheet->write($xlsRow, 11,$TEMP_PRINT_ON_REPORT, set_format("xlsFmtTitle", "", "L", "TRBL", 0, 0, 0, "000000", ""));
            $worksheet->write($xlsRow, 12,$TEMP_PL_NAME, set_format("xlsFmtTitle", "", "L", "TRBL", 0, 0, 0, "000000", ""));
            $worksheet->write($xlsRow, 13,$TEMP_POH_PM_NAME, set_format("xlsFmtTitle", "", "L", "TRBL", 0, 0, 0, "000000", ""));
            $worksheet->write($xlsRow, 14,$TEMP_PT_NAME, set_format("xlsFmtTitle", "", "L", "TRBL", 0, 0, 0, "000000", ""));
            $worksheet->write($xlsRow, 15,$TEMP_POH_LEVEL_NO, set_format("xlsFmtTitle", "", "L", "TRBL", 0, 0, 0, "000000", ""));
            $worksheet->write($xlsRow, 16,$TEMP_ORG_NAME_1, set_format("xlsFmtTitle", "", "L", "TRBL", 0, 0, 0, "000000", ""));
            $worksheet->write($xlsRow, 17,$TEMP_ORG_NAME_2, set_format("xlsFmtTitle", "", "L", "TRBL", 0, 0, 0, "000000", ""));
            $worksheet->write($xlsRow, 18,$TEMP_ORG_NAME_3, set_format("xlsFmtTitle", "", "L", "TRBL", 0, 0, 0, "000000", ""));
            $worksheet->write($xlsRow, 19,$TEMP_POH_UNDER_ORG1, set_format("xlsFmtTitle", "", "L", "TRBL", 0, 0, 0, "000000", ""));
            $worksheet->write($xlsRow, 20,$TEMP_POH_UNDER_ORG2, set_format("xlsFmtTitle", "", "L", "TRBL", 0, 0, 0, "000000", ""));
            $worksheet->write($xlsRow, 21,$TEMP_POH_PL_NAME, set_format("xlsFmtTitle", "", "L", "TRBL", 0, 0, 0, "000000", ""));
            $worksheet->write($xlsRow, 22,$TEMP_POH_ORG, set_format("xlsFmtTitle", "", "L", "TRBL", 0, 0, 0, "000000", ""));
            $worksheet->write($xlsRow, 23,$TEMP_POH_REMARK, set_format("xlsFmtTitle", "", "L", "TRBL", 0, 0, 0, "000000", ""));
            $xlsRow++;
        }
        
    }else{
        //ไม่มีข้อมูล
        $xlsRow = 0;
        $worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
    }
    
    $workbook->close();

    ini_set("max_execution_time", 30);
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
    header("Content-Type: application/x-msexcel; name=\"$report_code.xls\"");
    header("Content-Disposition: inline; filename=\"$report_code.xls\"");
    $fh=fopen($fname, "rb");
    fpassthru($fh);
    fclose($fh);
    unlink($fname);