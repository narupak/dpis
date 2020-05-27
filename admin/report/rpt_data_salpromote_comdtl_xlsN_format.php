<?

//	fix head for P0406 xls
//	echo "DEPARTMENT_NAME=$DEPARTMENT_NAME, COM_LEVEL_SALP=$COM_LEVEL_SALP<br>";
//        die();
if ($DEPARTMENT_NAME == "กรมการปกครอง") {
    if ($COM_LEVEL_SALP == 6) {
        $heading_width = array(6, 30, 15, 50, 10, 15, 15, 10, 15, 15, 10, 10, 10, 50);
        $heading_text = array("ลำดับ|ที่", $FULLNAME_HEAD . "|", "เลขประจำตัว|ประชาชน", $COM_HEAD_01 . "|สังกัด / ตำแหน่ง", "<**1**>|เลขที่", "<**1**>|ระดับตำแหน่ง", "เงินเดือน|ก่อนเลื่อน", "ฐานในการ|คำนวณ", "เปอร์เซ็นต์|ที่เลื่อน", "จำนวนเงิน|ที่เลื่อน", "เงินตอบแทน|พิเศษ", "ให้ได้รับ|เงินเดือน", "ผลการ|ประเมิน", "หมายเหตุ|");
        $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
        $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
        $data_align = array("C", "L", "C", "L", "C", "R", "R", "R", "R", "R", "R", "R", "R", "L");
    } elseif ($COM_LEVEL_SALP == 3) {
        $heading_width = array(6, 30, 15, 50, 10, 15, 15, 15, 10, 50);
        $heading_text = array("ลำดับ|ที่", $FULLNAME_HEAD . "|", "เลขประจำตัว|ประชาชน", $COM_HEAD_01 . "|สังกัด / ตำแหน่ง", "<**1**>|เลขที่", "<**1**>ระดับตำแหน่ง", "เงินเดือน|เต็มขั้น", "เงินตอบแทน|พิเศษ", "ผลการ|ประเมิน", "หมายเหตุ|");
        $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
        $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
        $data_align = array("C", "L", "C", "L", "C", "L", "R", "R", "R", "L");
    } elseif ($COM_LEVEL_SALP == 9) {
        $heading_width = array(6, 30, 15, 50, 10, 15, 15, 15, 10, 50);
        $heading_text = array("ลำดับ|ที่", $FULLNAME_HEAD . "|", "เลขประจำตัว|ประชาชน", $COM_HEAD_01 . "|สังกัด / ตำแหน่ง", "<**1**>|เลขที่", "<**1**>ระดับตำแหน่ง", "เงินเดือน|ก่อนเลื่อน", "ให้ได้รับ|เงินเดือน", "จำนวนเงิน|ที่เลื่อน", "หมายเหตุ|");
        $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
        $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
        $data_align = array("C", "L", "C", "L", "C", "L", "R", "R", "R", "L");
    } else {
        $heading_width = array(6, 30, 15, 50, 10, 15, 15, 15, 15, 10, 10, 10, 50);
        $heading_text = array("ลำดับ|ที่", $FULLNAME_HEAD . "|", "เลขประจำตัว|ประชาชน", $COM_HEAD_01 . "|สังกัด / ตำแหน่ง", "<**1**>|เลขที่", "<**1**>|ระดับตำแหน่ง", "เงินเดือน|ก่อนเลื่อน", "ให้ได้รับ|เงินเดือน", "จำนวนเงิน|ที่เลื่อน", "เปอร์เซ็นต์|ที่เลื่อน", "ฐานในการ|คำนวณ", "ผลการ|ประเมิน", "หมายเหตุ|");
        $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
        $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
        $data_align = array("C", "L", "C", "L", "C", "L", "R", "R", "R", "R", "R", "R", "L");
    }
} else {
//echo "format.. ไม่ใช่กรมการปกครอง...COM_LEVEL_SALP=$COM_LEVEL_SALP<br>";
    if ($COM_LEVEL_SALP == 6) {
        if ($CARDNO_FLAG == 1) {
            $heading_width = array(6,30,20,50,17,10,15,40,15,15,25,20,15,50);
            $heading_text = array("ลำดับ|ที่ ", 
                $FULLNAME_HEAD . "|", 
                "เลขประจำตัว|ประชาชน", 
                "ประเภท|ตำแหน่ง", 
                $COM_HEAD_01 . "|สังกัด / ตำแหน่ง", 
                "<**1**>|เลขที่", 
                "<**1**>|ระดับตำแหน่ง", 
                "เงินเดือน|เต็มขั้น", 
                "อัตราเงินเดือน|ที่เต็มขั้น", 
                "เงินตอบแทน|พิเศษ", 
                "เงินเดือน|ก่อนเลื่อน", 
                "ให้ได้รับ|เงินเดือน", 
                "xxxxx|xxxx", 
                "จำนวนเงิน|ที่เลื่อน",
                "xxxx|xxxx",
                "xxxx|xxxx");
            /*เดิม*/
            //$column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));/*เดิม*/
            
            $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C','C', 'C', 'C', 'C');
            
            $column_function = array(
                    (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), 
                    (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                    (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), 
                    (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                    (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                    (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), 
                    (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                    (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                    (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), 
                    (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"),
                    (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                    (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),
                    (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                    (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM")
                );
            //$data_align = array("C", "L", "C", "C", "L", "C", "L", "R", "R", "R", "R", "R", "R", "R", "R", "L"); /*เดิม*/
            $data_align = array("C","L","C","L","C","C","R","R","R","R","R","R","R","R","R","L");
        } else {
            $heading_width = array(6,30,50,17,10,15,40,15,15,25,20,15,50);
            $heading_text = array("ลำดับ|ที่", 
                $FULLNAME_HEAD . "|$CARDNO_TITLE", 
                "", 
                "ประเภท|ตำแหน่ง", 
                $COM_HEAD_01 . "|สังกัด / ตำแหน่ง", 
                "<**1**>|เลขที่", 
                "<**1**>|ระดับตำแหน่ง", 
                "เงินเดือน|เต็มขั้น", 
                "อัตราเงินเดือน|ที่เต็มขั้น", 
                "เงินตอบแทน|พิเศษ", 
                "เงินเดือน|ก่อนเลื่อน", 
                "ให้ได้รับ|เงินเดือน", 
                "xxxxx|xxxx", 
                "จำนวนเงิน|ที่เลื่อน",
                "xxxx|xxxx",
                "xxxx|xxxx");
            $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
            $column_function = array(
                    (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), 
                    (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), 
                    (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                    (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                    (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), 
                    (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                    (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                    (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), 
                    (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), 
                    (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                    (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),
                    (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
            $data_align = array("C","L","L","L","C","R","R","R","R","R","L");
            //$heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
            //$column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));/*เดิม*/
            //$data_align = array("C", "L", "C", "L", "C", "L", "R", "R", "R", "R", "R", "R", "R", "R", "L");/*เดิม*/
        }
    } elseif ($COM_LEVEL_SALP == 3) {
        if ($BKK_FLAG == 1) {
            $heading_width = array(6, 30, 45, 12, 10, 10, 10, 10, 10, 45);
            $heading_text = array("ลำดับ|ที่", $FULLNAME_HEAD . "|", $COM_HEAD_01 . "|สังกัด/ตำแหน่ง", "<**1**>|ตำแหน่ง|ประเภท", "<**1**>|ระดับ", "<**1**>|เลขที่", "เงินเดือนฐาน", "<**2**>เงินค่าตอบแทนพิเศษ|ร้อยละ 2", "<**2**>|ร้อยละ 4", "หมายเหตุ|");
            $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
            $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
            $data_align = array("C", "L", "L", "C", "C", "C", "R", "R", "R", "L");
        } else {
            if ($CARDNO_FLAG == 1) {
                $heading_width = array(6, 30, 20,50, 15, 15, 15, 20, 20, 20, 20, 20, 50);
                $heading_text = array("ลำดับ|ที่", $FULLNAME_HEAD . "|", "เลขประจำตัว|ประชาชน", $COM_HEAD_01 . "|สังกัด / ตำแหน่ง", "<**1**>|เลขที่", "<**1**>|ระดับตำแหน่ง","เงินเดือนเดิม","อัตราเงินเดือนที่เต็มขั้น","ฐานในการคำนวน","ร้อยละที่ได้","จำนวนเงินที่ได้เลื่อน","จำนวนค่าตอบแทน","หมายเหตุ");
                $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
                $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"),(($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"),(($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
                $data_align = array("C", "L","C", "L", "C", "L", "R", "R", "R","R", "R", "R", "L");
            } else {
                $heading_width = array(6, 30, 50, 15, 15, 15, 20, 20, 20, 20, 20, 50);
                $heading_text = array("ลำดับ|ที่", $FULLNAME_HEAD . "|$CARDNO_TITLE", $COM_HEAD_01 . "|สังกัด / ตำแหน่ง", "<**1**>|เลขที่", "<**1**>|ระดับตำแหน่ง","เงินเดือนเดิม","อัตราเงินเดือนที่เต็มขั้น","ฐานในการคำนวน","ร้อยละที่ได้","จำนวนเงินที่ได้เลื่อน","จำนวนค่าตอบแทน","หมายเหตุ");
                $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
                $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
                $data_align = array("C", "L", "L", "C", "L", "R", "R", "R","R", "R", "R", "L");
            }
        }
    } elseif ($COM_LEVEL_SALP == 9) {
        if ($CARDNO_FLAG == 1) {
            $heading_width = array(6, 30, 15, 40, 12, 13, 20, 20, 20, 50);
            $heading_text = array("ลำดับ|ที่", $FULLNAME_HEAD . "|", "เลขประจำตัว|ประชาชน", $COM_HEAD_01 . "|สังกัด / ตำแหน่ง", "<**1**>ประเภท|ตำแหน่ง", "<**1**>|ระดับตำแหน่ง", "<**1**>|เลขที่", "เงินเดือน", "ค่าตอบแทน|พิเศษ", "ให้ได้รับ|เงินเดือน", "หมายเหตุ|");
            $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
            $column_function = array(   (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                                        (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                                        (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), 
                                        (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                                        (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                                        (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                                        (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                                        (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), 
                                        (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                                        (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
            $data_align = array("C", "L", "C", "C", "L", "C", "L", "R", "R", "L");
        } else {
            $heading_width = array(6, 30, 40, 12, 13, 20, 20, 20, 50);
            $heading_text = array("ลำดับ|ที่", $FULLNAME_HEAD . "|", $COM_HEAD_01 . "|สังกัด / ตำแหน่ง", "<**1**>ประเภท|ตำแหน่ง", "<**1**>|ระดับตำแหน่ง", "<**1**>|เลขที่", "เงินเดือน", "ค่าตอบแทน|พิเศษ", "ให้ได้รับ|เงินเดือน", "หมายเหตุ|");
            $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
            $column_function = array(   (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                                        (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                                        (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                                        (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                                        (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                                        (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                                        (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), 
                                        (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), 
                                        (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
            $data_align = array("C", "L", "C", "L", "C", "L", "R", "R",  "L");
        }
    } elseif ($COM_LEVEL_SALP == 8) { //ประเภท เงินตอบแทนพิเศษ
        if ($BKK_FLAG == 1) {
            $heading_width = array(6, 30, 15, 15, 50, 10, 15, 15, 15, 10, 10, 10, 50, 50, 50);
            $heading_text = array("ลำดับ|ที่", $FULLNAME_HEAD . "|", $COM_HEAD_01 . "|สังกัด/ตำแหน่ง", "<**1**>|ตำแหน่ง|ประเภท", "<**1**>|ระดับ", "<**1**>|เลขที่", "เงินเดือน|ก่อนเลื่อน", "ให้ได้รับ|เงินเดือน", "จำนวนเงิน|ที่เลื่อน", "เปอร์เซ็นต์|ที่เลื่อน", "เงินตอบแทน|พิเศษ", "ฐานในการ|คำนวณ", "ผลการ|ประเมิน", "หมายเหตุ|");
            $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
            $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
            $data_align = array("C", "L", "L", "C", "L", "C", "R", "R", "R", "R", "R", "R", "R", "L");
        } else {
            $heading_width = array(6, 30, 15, 15, 50, 10, 15, 15, 15, 10, 10, 10, 50, 50, 50);
            $heading_text = array("ลำดับ|ที่", $FULLNAME_HEAD . "|", "เลขประจำตัว|ประชาชน", "ประเภท|ตำแหน่ง", $COM_HEAD_01 . "|สังกัด / ตำแหน่ง", "<**1**>|เลขที่", "<**1**>|ระดับตำแหน่ง", "เงินเดือน|ก่อนเลื่อน", "ให้ได้รับ|เงินเดือน", "จำนวนเงิน|ที่เลื่อน", "เปอร์เซ็นต์|ที่เลื่อน", "เงินตอบแทน|พิเศษ", "ฐานในการ|คำนวณ", "ผลการ|ประเมิน", "หมายเหตุ|");
            $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
            $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
            $data_align = array("C", "L", "C", "C", "L", "C", "L", "R", "R", "R", "R", "R", "R", "R", "L");
        }
    } elseif ($COM_LEVEL_SALP == 1) {
        if ($BKK_FLAG == 1) {
            $heading_width = array(6, 30, 45, 12, 10, 10, 10, 10, 10, 45);
            $heading_text = array("ลำดับ|ที่", $FULLNAME_HEAD . "|", $COM_HEAD_01 . "|สังกัด/ตำแหน่ง", "<**1**>|ตำแหน่ง|ประเภท", "<**1**>|ระดับ", "<**1**>|เลขที่", "เงินเดือนฐาน", "<**2**>เงินค่าตอบแทนพิเศษ|ร้อยละ 2", "<**2**>|ร้อยละ 4", "หมายเหตุ|");
            $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
            $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
            $data_align = array("C", "L", "L", "C", "C", "C", "R", "R", "R", "L");
        } else {
            if ($CARDNO_FLAG == 1) {
// เดิม          $heading_width = array(6, 30, 20,50, 15, 15, 15, 20, 20, 20, 20, 20, 50); 
                $heading_width = array(6, 30, 20,50, 15, 15, 15, 20, 20, 20, 20, 50);                                       /* Release 5.2.1.20 */
// เดิม          $heading_text = array("ลำดับ|ที่", $FULLNAME_HEAD . "|", "เลขประจำตัว|ประชาชน", $COM_HEAD_01 . "|สังกัด / ตำแหน่ง", "<**1**>|เลขที่", "<**1**>|ระดับตำแหน่ง","เงินเดือนเดิม","อัตราเงินเดือนที่เต็มขั้น","ฐานในการคำนวน","ร้อยละที่ได้","จำนวนเงินที่ได้เลื่อน","จำนวนค่าตอบแทน","หมายเหตุ");
                $heading_text = array("ลำดับ|ที่", $FULLNAME_HEAD . "|", "เลขประจำตัว|ประชาชน", $COM_HEAD_01 . "|สังกัด / ตำแหน่ง", "<**1**>|เลขที่", "<**1**>|ระดับตำแหน่ง","เงินเดือนเดิม","อัตราเงินเดือนที่เต็มขั้น","ฐานในการคำนวน","ร้อยละที่ได้","จำนวนเงินที่ได้เลื่อน","หมายเหตุ");      /* Release 5.2.1.20 */
// เดิม          $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
                $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');              /* Release 5.2.1.20 */
// เดิม          $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"),(($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"),(($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
                $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"),(($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"),(($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));      /* Release 5.2.1.20 */
// เดิม          $data_align = array("C", "L","C", "L", "C", "L", "R", "R", "R","R", "R", "R", "L");
                $data_align = array("C", "L","C", "L", "C", "L", "R", "R", "R","R", "R", "L");             /* Release 5.2.1.20 */
            } else {
// เดิม           $heading_width = array(6, 30, 50, 15, 15, 15, 20, 20, 20, 20, 20, 50);
                $heading_width = array(6, 30, 50, 15, 15, 15, 20, 20, 20, 20,  50);                     /* Release 5.2.1.20 */
// เดิม           $heading_text = array("ลำดับ|ที่", $FULLNAME_HEAD . "|$CARDNO_TITLE", $COM_HEAD_01 . "|สังกัด / ตำแหน่ง", "<**1**>|เลขที่", "<**1**>|ระดับตำแหน่ง","เงินเดือนเดิม","อัตราเงินเดือนที่เต็มขั้น","ฐานในการคำนวน","ร้อยละที่ได้","จำนวนเงินที่ได้เลื่อน","จำนวนค่าตอบแทน","หมายเหตุ");
                $heading_text = array("ลำดับ|ที่", $FULLNAME_HEAD . "|$CARDNO_TITLE", $COM_HEAD_01 . "|สังกัด / ตำแหน่ง", "<**1**>|เลขที่", "<**1**>|ระดับตำแหน่ง","เงินเดือนเดิม","อัตราเงินเดือนที่เต็มขั้น","ฐานในการคำนวน","ร้อยละที่ได้","จำนวนเงินที่ได้เลื่อน","หมายเหตุ");                      /* Release 5.2.1.20 */
// เดิม          $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
                $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');        /* Release 5.2.1.20 */
// เดิม          $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
                $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));          /* Release 5.2.1.20 */
// เดิม          $data_align = array("C", "L", "L", "C", "L", "R", "R", "R","R", "R", "R", "L");
                $data_align = array("C", "L", "L", "C", "L", "R", "R", "R","R", "R", "L");             /* Release 5.2.1.20 */
            }
        }
    } elseif ($COM_LEVEL_SALP == 5) {
        if ($BKK_FLAG == 1) {
            $heading_width = array(6, 30, 45, 12, 10, 10, 10, 10, 10, 45);
            $heading_text = array("ลำดับ|ที่", $FULLNAME_HEAD . "|", $COM_HEAD_01 . "|สังกัด/ตำแหน่ง", "<**1**>|ตำแหน่ง|ประเภท", "<**1**>|ระดับ", "<**1**>|เลขที่", "เงินเดือนฐาน", "<**2**>เงินค่าตอบแทนพิเศษ|ร้อยละ 2", "<**2**>|ร้อยละ 4", "หมายเหตุ|");
            $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
            $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
            $data_align = array("C", "L", "L", "C", "C", "C", "R", "R", "R", "L");
        } else {
            if ($CARDNO_FLAG == 1) {
// เดิม          $heading_width = array(6, 30, 20,50, 15, 15, 15, 20, 20, 20, 20, 20, 50);
                $heading_width = array(6, 30, 20,50, 15, 15, 15, 20, 20, 20, 20, 50);           /* Release 5.2.1.20 */
// เดิม          $heading_text = array("ลำดับ|ที่", $FULLNAME_HEAD . "|", "เลขประจำตัว|ประชาชน", $COM_HEAD_01 . "|สังกัด / ตำแหน่ง", "<**1**>|เลขที่", "<**1**>|ระดับตำแหน่ง","เงินเดือนเดิม","อัตราเงินเดือนที่เต็มขั้น","ฐานในการคำนวน","ร้อยละที่ได้","จำนวนเงินที่ได้เลื่อน","จำนวนเงินที่ได้รับ","หมายเหตุ");
                $heading_text = array("ลำดับ|ที่", $FULLNAME_HEAD . "|", "เลขประจำตัว|ประชาชน", $COM_HEAD_01 . "|สังกัด / ตำแหน่ง", "<**1**>|เลขที่", "<**1**>|ระดับตำแหน่ง","เงินเดือนเดิม","ฐานในการคำนวน","ร้อยละที่ได้","จำนวนเงินที่ได้เลื่อน","จำนวนค่าตอบแทน","หมายเหตุ");    /* Release 5.2.1.20 */ 
// เดิม          $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
                $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');       /* Release 5.2.1.20 */
// เดิม          $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"),(($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"),(($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
                $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"),(($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"),(($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));     /* Release 5.2.1.20 */
// เดิม          $data_align = array("C", "L","C", "L", "C", "L", "R", "R", "R","R", "R", "R", "L");
                $data_align = array("C", "L","C", "L", "C", "L", "R", "R","R", "R", "R", "L");         /* Release 5.2.1.20 */
            } else {
// เดิม          $heading_width = array(6, 30, 50, 15, 15, 15, 20, 20, 20, 20, 20, 50);
                $heading_width = array(6, 30, 50, 15, 15, 15, 20, 20, 20, 20, 50);          /* Release 5.2.1.20 */
// เดิม          $heading_text = array("ลำดับ|ที่", $FULLNAME_HEAD . "|$CARDNO_TITLE", $COM_HEAD_01 . "|สังกัด / ตำแหน่ง", "<**1**>|เลขที่", "<**1**>|ระดับตำแหน่ง","เงินเดือนเดิม","อัตราเงินเดือนที่เต็มขั้น","ฐานในการคำนวน","ร้อยละที่ได้","จำนวนเงินที่ได้เลื่อน","จำนวนเงินที่ได้รับ","หมายเหตุ");
                $heading_text = array("ลำดับ|ที่", $FULLNAME_HEAD . "|$CARDNO_TITLE", $COM_HEAD_01 . "|สังกัด / ตำแหน่ง", "<**1**>|เลขที่", "<**1**>|ระดับตำแหน่ง","เงินเดือนเดิม","ฐานในการคำนวน","ร้อยละที่ได้","จำนวนเงินที่ได้เลื่อน","จำนวนค่าตอบแทน","หมายเหตุ");   /* Release 5.2.1.20 */
// เดิม          $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
                $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');         /* Release 5.2.1.20 */
// เดิม          $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
                $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));    /* Release 5.2.1.20 */
// เดิม          $data_align = array("C", "L", "L", "C", "L", "R", "R", "R","R", "R", "R", "L");
                $data_align = array("C", "L", "L", "C", "L", "R", "R","R", "R", "R", "L");        /* Release 5.2.1.20 */
            }
        }
    }else {
        if ($BKK_FLAG == 1) {
            $heading_width = array(6, 30, 15, 50, 10, 15, 15, 15, 50);
            $ws_head_line1 = array("ลำดับ", $FULLNAME_HEAD, "ประเภท", $COM_HEAD_01, $COM_HEAD_01, $COM_HEAD_01, "เงินเดือน", "ให้ได้รับ", "หมายเหตุ");
            $ws_head_line2 = array("ที่", "", "ตำแหน่ง", "ตำแหน่ง/สังกัด", "เลขที่", "ระดับตำแหน่ง", "ก่อนเลื่อน", "เงินเดือน", "");
            $heading_text = array("ลำดับ|ที่", $FULLNAME_HEAD . "|", "ประเภท|ตำแหน่ง", $COM_HEAD_01 . "|สังกัด / ตำแหน่ง", "<**1**>|เลขที่", "<**1**>|ระดับตำแหน่ง", "เงินเดือน|ก่อนเลื่อน", "ให้ได้รับ|เงินเดือน", "หมายเหตุ|");
            $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
            $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
            $data_align = array("C", "L", "C", "L", "C", "L", "R", "R", "L");
        } else {
            if ($CARDNO_FLAG == 1) {
                $heading_width = array(6, 30, 15, 15, 50, 10, 15, 15, 15, 15, 10, 10, 10, 50);
                $ws_head_line1 = array("ลำดับ", $FULLNAME_HEAD, "เลขประจำตัว", "ประเภท", $COM_HEAD_01, $COM_HEAD_01, $COM_HEAD_01, "เงินเดือน", "ให้ได้รับ", "จำนวนเงิน", "เปอร์เซ็นต์", "ฐานในการ", "ผลการ", "หมายเหตุ");
                $ws_head_line2 = array("ที่", "", "ประชาชน", "ตำแหน่ง", "ตำแหน่ง/สังกัด", "เลขที่", "ระดับตำแหน่ง", "ก่อนเลื่อน", "เงินเดือน", "ที่เลื่อน", "ที่เลื่อน", "คำนวณ", "ประเมิน", "");
                $heading_text = array("ลำดับ|ที่", $FULLNAME_HEAD . "|", "เลขประจำตัว|ประชาชน", "ประเภท|ตำแหน่ง", $COM_HEAD_01 . "|สังกัด / ตำแหน่ง", "<**1**>|เลขที่", "<**1**>|ระดับตำแหน่ง", "เงินเดือน|ก่อนเลื่อน", "ให้ได้รับ|เงินเดือน", "จำนวนเงิน|ที่เลื่อน", "เปอร์เซ็นต์|ที่เลื่อน", "ฐานในการ|คำนวณ", "ผลการ|ประเมิน", "หมายเหตุ|");
                $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
                $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
                $data_align = array("C", "L", "C", "C", "L", "C", "L", "R", "R", "R", "R", "R", "R", "L");
            } else {
                $heading_width = array(6, 30, 15, 50, 10, 15, 15, 15, 15, 10, 10, 10, 50);
                $ws_head_line1 = array("ลำดับ", $FULLNAME_HEAD, "ประเภท", $COM_HEAD_01, $COM_HEAD_01, $COM_HEAD_01, "เงินเดือน", "ให้ได้รับ", "จำนวนเงิน", "เปอร์เซ็นต์", "ฐานในการ", "ผลการ", "หมายเหตุ");
                $ws_head_line2 = array("ที่", "$CARDNO_TITLE", "ตำแหน่ง", "ตำแหน่ง/สังกัด", "เลขที่", "ระดับตำแหน่ง", "ก่อนเลื่อน", "เงินเดือน", "ที่เลื่อน", "ที่เลื่อน", "คำนวณ", "ประเมิน", "");
                $heading_text = array("ลำดับ|ที่", $FULLNAME_HEAD . "|$CARDNO_TITLE", "", "ประเภท|ตำแหน่ง", $COM_HEAD_01 . "|สังกัด / ตำแหน่ง", "<**1**>|เลขที่", "<**1**>|ระดับตำแหน่ง", "เงินเดือน|ก่อนเลื่อน", "ให้ได้รับ|เงินเดือน", "จำนวนเงิน|ที่เลื่อน", "เปอร์เซ็นต์|ที่เลื่อน", "ฐานในการ|คำนวณ", "ผลการ|ประเมิน", "หมายเหตุ|");
                $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
                $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
                $data_align = array("C", "L", "C", "C", "L", "C", "L", "R", "R", "R", "R", "R", "R", "L");
            }
        }
    }
}

// function ประเภท aggregate มี  SUM, AVG, PERC ตามแนวบรรทัด (ROW)
// 									SUM-columnindex1-columnindex2-....  Ex SUM-1-3-4-7 หมายถึง ผลรวมของ column ที่ 1,3,4 และ 7
// 									AVG-columnindex1-columnindex2-....  Ex AVG-1-3-4-7 หมายถึง ค่าเฉลี่ยของ column ที่ 1,3,4 และ 7
// 									PERCn-columnindex1-columnindex2-....  Ex PERCn-1-3-4-7 n คือ column ที่ เป็นเป้าหมายเปรียบเทียบร้อยละ (ค่า 1 ค่า ใน column 1 3 4 7) 
//																												ถ้าไม่ใส่ค่า n ก็คือ ค่าสรุป (100%) PERC3-1-3-4-7 ก็คือ ค่าใน column ที่ 3 เป็นร้อยละเท่าใดของผลรวม column 1,3,4,7
//	function ประเภท รูปแบบ (format) ซึ่งจะต้องตามหลัง function ประเภท aggregate เสมอ (ถ้ามี)
//									TNUM-n คือ เปลี่ยนเป็นตัวเลขของไทย n คือ จำนวนทศนิยม ถ้ามีค่าเป็น ศูนย์ จะแสดงเป็น -
//									TNUM0-n คือ เปลี่ยนเป็นตัวเลขของไทย n คือ จำนวนทศนิยม ถ้ามีค่าเป็น ศูนย์ จะแสดงเป็น 0
//									ENUM-n คือ แสดงเป็นเลขอาราบิค n คือ จำนวนทศนิยม ถ้ามีค่าเป็น ศูนย์ จะแสดงเป็น -
//									ENUM0-n คือ แสดงเป็นเลขอาราบิค n คือ จำนวนทศนิยม ถ้ามีค่าเป็น ศูนย์ จะแสดงเป็น 0
//
	$total_head_width = 0;
for ($iii = 0; $iii < count($heading_width); $iii++)
    $total_head_width += (int) $heading_width[$iii];

if (!$COLUMN_FORMAT) { // ต้องกำหนดเป็น element ให้อยู่ใน form1 ด้วย  <input type="hidden" name="COLUMN_FORMAT" value="<?=$COLUMN_FORMAT
    $arr_column_map = (array) null;
    $arr_column_sel = (array) null;
    for ($i = 0; $i < count($heading_text); $i++) {
        $arr_column_map[] = $i;  // link index ของ head 
        $arr_column_sel[] = 1;   // 1=แสดง	0=ไม่แสดง   ถ้าไม่มีการปรับแต่งรายงาน ก็เป็นแสดงหมด
    }
    $arr_column_width = $heading_width; // ความกว้าง
    $arr_column_align = $data_align;  // align
    $COLUMN_FORMAT = implode(",", $arr_column_map) . "|" . implode(",", $arr_column_sel) . "|" . implode(",", $arr_column_width) . "|" . implode(",", $arr_column_align);
} else {
    $arrbuff = explode("|", $COLUMN_FORMAT);
    $arr_column_map = explode(",", $arrbuff[0]);  // index ของ head เริ่มต้น
    $arr_column_sel = explode(",", $arrbuff[1]); // 1=แสดง	0=ไม่แสดง
    $arr_column_width = explode(",", $arrbuff[2]); // ความกว้าง
    $heading_width = $arr_column_width; // ความกว้าง
    $arr_column_align = explode(",", $arrbuff[3]);  // align
}

$total_show_width = 0;
for ($iii = 0; $iii < count($arr_column_width); $iii++)
    if ($arr_column_sel[$iii] == 1)
        $total_show_width += (int) $arr_column_width[$iii];
?>