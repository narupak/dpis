<?

//	fix head for P0406 xls
//	echo "DEPARTMENT_NAME=$DEPARTMENT_NAME, COM_LEVEL_SALP=$COM_LEVEL_SALP<br>";
//        die();
if ($DEPARTMENT_NAME == "�����û���ͧ") {
    if ($COM_LEVEL_SALP == 6) {
        $heading_width = array(6, 30, 15, 50, 10, 15, 15, 10, 15, 15, 10, 10, 10, 50);
        $heading_text = array("�ӴѺ|���", $FULLNAME_HEAD . "|", "�Ţ��Шӵ��|��ЪҪ�", $COM_HEAD_01 . "|�ѧ�Ѵ / ���˹�", "<**1**>|�Ţ���", "<**1**>|�дѺ���˹�", "�Թ��͹|��͹����͹", "�ҹ㹡��|�ӹǳ", "�����繵�|�������͹", "�ӹǹ�Թ|�������͹", "�Թ�ͺ᷹|�����", "������Ѻ|�Թ��͹", "�š��|�����Թ", "�����˵�|");
        $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
        $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
        $data_align = array("C", "L", "C", "L", "C", "R", "R", "R", "R", "R", "R", "R", "R", "L");
    } elseif ($COM_LEVEL_SALP == 3) {
        $heading_width = array(6, 30, 15, 50, 10, 15, 15, 15, 10, 50);
        $heading_text = array("�ӴѺ|���", $FULLNAME_HEAD . "|", "�Ţ��Шӵ��|��ЪҪ�", $COM_HEAD_01 . "|�ѧ�Ѵ / ���˹�", "<**1**>|�Ţ���", "<**1**>�дѺ���˹�", "�Թ��͹|������", "�Թ�ͺ᷹|�����", "�š��|�����Թ", "�����˵�|");
        $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
        $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
        $data_align = array("C", "L", "C", "L", "C", "L", "R", "R", "R", "L");
    } elseif ($COM_LEVEL_SALP == 9) {
        $heading_width = array(6, 30, 15, 50, 10, 15, 15, 15, 10, 50);
        $heading_text = array("�ӴѺ|���", $FULLNAME_HEAD . "|", "�Ţ��Шӵ��|��ЪҪ�", $COM_HEAD_01 . "|�ѧ�Ѵ / ���˹�", "<**1**>|�Ţ���", "<**1**>�дѺ���˹�", "�Թ��͹|��͹����͹", "������Ѻ|�Թ��͹", "�ӹǹ�Թ|�������͹", "�����˵�|");
        $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
        $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
        $data_align = array("C", "L", "C", "L", "C", "L", "R", "R", "R", "L");
    } else {
        $heading_width = array(6, 30, 15, 50, 10, 15, 15, 15, 15, 10, 10, 10, 50);
        $heading_text = array("�ӴѺ|���", $FULLNAME_HEAD . "|", "�Ţ��Шӵ��|��ЪҪ�", $COM_HEAD_01 . "|�ѧ�Ѵ / ���˹�", "<**1**>|�Ţ���", "<**1**>|�дѺ���˹�", "�Թ��͹|��͹����͹", "������Ѻ|�Թ��͹", "�ӹǹ�Թ|�������͹", "�����繵�|�������͹", "�ҹ㹡��|�ӹǳ", "�š��|�����Թ", "�����˵�|");
        $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
        $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
        $data_align = array("C", "L", "C", "L", "C", "L", "R", "R", "R", "R", "R", "R", "L");
    }
} else {
//echo "format.. ���������û���ͧ...COM_LEVEL_SALP=$COM_LEVEL_SALP<br>";
    if ($COM_LEVEL_SALP == 6) {
        if ($CARDNO_FLAG == 1) {
            $heading_width = array(6,30,20,50,17,10,15,40,15,15,25,20,15,50);
            $heading_text = array("�ӴѺ|��� ", 
                $FULLNAME_HEAD . "|", 
                "�Ţ��Шӵ��|��ЪҪ�", 
                "������|���˹�", 
                $COM_HEAD_01 . "|�ѧ�Ѵ / ���˹�", 
                "<**1**>|�Ţ���", 
                "<**1**>|�дѺ���˹�", 
                "�Թ��͹|������", 
                "�ѵ���Թ��͹|���������", 
                "�Թ�ͺ᷹|�����", 
                "�Թ��͹|��͹����͹", 
                "������Ѻ|�Թ��͹", 
                "xxxxx|xxxx", 
                "�ӹǹ�Թ|�������͹",
                "xxxx|xxxx",
                "xxxx|xxxx");
            /*���*/
            //$column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));/*���*/
            
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
            //$data_align = array("C", "L", "C", "C", "L", "C", "L", "R", "R", "R", "R", "R", "R", "R", "R", "L"); /*���*/
            $data_align = array("C","L","C","L","C","C","R","R","R","R","R","R","R","R","R","L");
        } else {
            $heading_width = array(6,30,50,17,10,15,40,15,15,25,20,15,50);
            $heading_text = array("�ӴѺ|���", 
                $FULLNAME_HEAD . "|$CARDNO_TITLE", 
                "", 
                "������|���˹�", 
                $COM_HEAD_01 . "|�ѧ�Ѵ / ���˹�", 
                "<**1**>|�Ţ���", 
                "<**1**>|�дѺ���˹�", 
                "�Թ��͹|������", 
                "�ѵ���Թ��͹|���������", 
                "�Թ�ͺ᷹|�����", 
                "�Թ��͹|��͹����͹", 
                "������Ѻ|�Թ��͹", 
                "xxxxx|xxxx", 
                "�ӹǹ�Թ|�������͹",
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
            //$column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));/*���*/
            //$data_align = array("C", "L", "C", "L", "C", "L", "R", "R", "R", "R", "R", "R", "R", "R", "L");/*���*/
        }
    } elseif ($COM_LEVEL_SALP == 3) {
        if ($BKK_FLAG == 1) {
            $heading_width = array(6, 30, 45, 12, 10, 10, 10, 10, 10, 45);
            $heading_text = array("�ӴѺ|���", $FULLNAME_HEAD . "|", $COM_HEAD_01 . "|�ѧ�Ѵ/���˹�", "<**1**>|���˹�|������", "<**1**>|�дѺ", "<**1**>|�Ţ���", "�Թ��͹�ҹ", "<**2**>�Թ��ҵͺ᷹�����|������ 2", "<**2**>|������ 4", "�����˵�|");
            $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
            $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
            $data_align = array("C", "L", "L", "C", "C", "C", "R", "R", "R", "L");
        } else {
            if ($CARDNO_FLAG == 1) {
                $heading_width = array(6, 30, 20,50, 15, 15, 15, 20, 20, 20, 20, 20, 50);
                $heading_text = array("�ӴѺ|���", $FULLNAME_HEAD . "|", "�Ţ��Шӵ��|��ЪҪ�", $COM_HEAD_01 . "|�ѧ�Ѵ / ���˹�", "<**1**>|�Ţ���", "<**1**>|�дѺ���˹�","�Թ��͹���","�ѵ���Թ��͹���������","�ҹ㹡�äӹǹ","�����з����","�ӹǹ�Թ���������͹","�ӹǹ��ҵͺ᷹","�����˵�");
                $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
                $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"),(($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"),(($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
                $data_align = array("C", "L","C", "L", "C", "L", "R", "R", "R","R", "R", "R", "L");
            } else {
                $heading_width = array(6, 30, 50, 15, 15, 15, 20, 20, 20, 20, 20, 50);
                $heading_text = array("�ӴѺ|���", $FULLNAME_HEAD . "|$CARDNO_TITLE", $COM_HEAD_01 . "|�ѧ�Ѵ / ���˹�", "<**1**>|�Ţ���", "<**1**>|�дѺ���˹�","�Թ��͹���","�ѵ���Թ��͹���������","�ҹ㹡�äӹǹ","�����з����","�ӹǹ�Թ���������͹","�ӹǹ��ҵͺ᷹","�����˵�");
                $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
                $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
                $data_align = array("C", "L", "L", "C", "L", "R", "R", "R","R", "R", "R", "L");
            }
        }
    } elseif ($COM_LEVEL_SALP == 9) {
        if ($CARDNO_FLAG == 1) {
            $heading_width = array(6, 30, 15, 40, 12, 13, 20, 20, 20, 50);
            $heading_text = array("�ӴѺ|���", $FULLNAME_HEAD . "|", "�Ţ��Шӵ��|��ЪҪ�", $COM_HEAD_01 . "|�ѧ�Ѵ / ���˹�", "<**1**>������|���˹�", "<**1**>|�дѺ���˹�", "<**1**>|�Ţ���", "�Թ��͹", "��ҵͺ᷹|�����", "������Ѻ|�Թ��͹", "�����˵�|");
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
            $heading_text = array("�ӴѺ|���", $FULLNAME_HEAD . "|", $COM_HEAD_01 . "|�ѧ�Ѵ / ���˹�", "<**1**>������|���˹�", "<**1**>|�дѺ���˹�", "<**1**>|�Ţ���", "�Թ��͹", "��ҵͺ᷹|�����", "������Ѻ|�Թ��͹", "�����˵�|");
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
    } elseif ($COM_LEVEL_SALP == 8) { //������ �Թ�ͺ᷹�����
        if ($BKK_FLAG == 1) {
            $heading_width = array(6, 30, 15, 15, 50, 10, 15, 15, 15, 10, 10, 10, 50, 50, 50);
            $heading_text = array("�ӴѺ|���", $FULLNAME_HEAD . "|", $COM_HEAD_01 . "|�ѧ�Ѵ/���˹�", "<**1**>|���˹�|������", "<**1**>|�дѺ", "<**1**>|�Ţ���", "�Թ��͹|��͹����͹", "������Ѻ|�Թ��͹", "�ӹǹ�Թ|�������͹", "�����繵�|�������͹", "�Թ�ͺ᷹|�����", "�ҹ㹡��|�ӹǳ", "�š��|�����Թ", "�����˵�|");
            $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
            $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
            $data_align = array("C", "L", "L", "C", "L", "C", "R", "R", "R", "R", "R", "R", "R", "L");
        } else {
            $heading_width = array(6, 30, 15, 15, 50, 10, 15, 15, 15, 10, 10, 10, 50, 50, 50);
            $heading_text = array("�ӴѺ|���", $FULLNAME_HEAD . "|", "�Ţ��Шӵ��|��ЪҪ�", "������|���˹�", $COM_HEAD_01 . "|�ѧ�Ѵ / ���˹�", "<**1**>|�Ţ���", "<**1**>|�дѺ���˹�", "�Թ��͹|��͹����͹", "������Ѻ|�Թ��͹", "�ӹǹ�Թ|�������͹", "�����繵�|�������͹", "�Թ�ͺ᷹|�����", "�ҹ㹡��|�ӹǳ", "�š��|�����Թ", "�����˵�|");
            $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
            $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
            $data_align = array("C", "L", "C", "C", "L", "C", "L", "R", "R", "R", "R", "R", "R", "R", "L");
        }
    } elseif ($COM_LEVEL_SALP == 1) {
        if ($BKK_FLAG == 1) {
            $heading_width = array(6, 30, 45, 12, 10, 10, 10, 10, 10, 45);
            $heading_text = array("�ӴѺ|���", $FULLNAME_HEAD . "|", $COM_HEAD_01 . "|�ѧ�Ѵ/���˹�", "<**1**>|���˹�|������", "<**1**>|�дѺ", "<**1**>|�Ţ���", "�Թ��͹�ҹ", "<**2**>�Թ��ҵͺ᷹�����|������ 2", "<**2**>|������ 4", "�����˵�|");
            $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
            $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
            $data_align = array("C", "L", "L", "C", "C", "C", "R", "R", "R", "L");
        } else {
            if ($CARDNO_FLAG == 1) {
// ���          $heading_width = array(6, 30, 20,50, 15, 15, 15, 20, 20, 20, 20, 20, 50); 
                $heading_width = array(6, 30, 20,50, 15, 15, 15, 20, 20, 20, 20, 50);                                       /* Release 5.2.1.20 */
// ���          $heading_text = array("�ӴѺ|���", $FULLNAME_HEAD . "|", "�Ţ��Шӵ��|��ЪҪ�", $COM_HEAD_01 . "|�ѧ�Ѵ / ���˹�", "<**1**>|�Ţ���", "<**1**>|�дѺ���˹�","�Թ��͹���","�ѵ���Թ��͹���������","�ҹ㹡�äӹǹ","�����з����","�ӹǹ�Թ���������͹","�ӹǹ��ҵͺ᷹","�����˵�");
                $heading_text = array("�ӴѺ|���", $FULLNAME_HEAD . "|", "�Ţ��Шӵ��|��ЪҪ�", $COM_HEAD_01 . "|�ѧ�Ѵ / ���˹�", "<**1**>|�Ţ���", "<**1**>|�дѺ���˹�","�Թ��͹���","�ѵ���Թ��͹���������","�ҹ㹡�äӹǹ","�����з����","�ӹǹ�Թ���������͹","�����˵�");      /* Release 5.2.1.20 */
// ���          $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
                $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');              /* Release 5.2.1.20 */
// ���          $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"),(($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"),(($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
                $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"),(($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"),(($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));      /* Release 5.2.1.20 */
// ���          $data_align = array("C", "L","C", "L", "C", "L", "R", "R", "R","R", "R", "R", "L");
                $data_align = array("C", "L","C", "L", "C", "L", "R", "R", "R","R", "R", "L");             /* Release 5.2.1.20 */
            } else {
// ���           $heading_width = array(6, 30, 50, 15, 15, 15, 20, 20, 20, 20, 20, 50);
                $heading_width = array(6, 30, 50, 15, 15, 15, 20, 20, 20, 20,  50);                     /* Release 5.2.1.20 */
// ���           $heading_text = array("�ӴѺ|���", $FULLNAME_HEAD . "|$CARDNO_TITLE", $COM_HEAD_01 . "|�ѧ�Ѵ / ���˹�", "<**1**>|�Ţ���", "<**1**>|�дѺ���˹�","�Թ��͹���","�ѵ���Թ��͹���������","�ҹ㹡�äӹǹ","�����з����","�ӹǹ�Թ���������͹","�ӹǹ��ҵͺ᷹","�����˵�");
                $heading_text = array("�ӴѺ|���", $FULLNAME_HEAD . "|$CARDNO_TITLE", $COM_HEAD_01 . "|�ѧ�Ѵ / ���˹�", "<**1**>|�Ţ���", "<**1**>|�дѺ���˹�","�Թ��͹���","�ѵ���Թ��͹���������","�ҹ㹡�äӹǹ","�����з����","�ӹǹ�Թ���������͹","�����˵�");                      /* Release 5.2.1.20 */
// ���          $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
                $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');        /* Release 5.2.1.20 */
// ���          $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
                $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));          /* Release 5.2.1.20 */
// ���          $data_align = array("C", "L", "L", "C", "L", "R", "R", "R","R", "R", "R", "L");
                $data_align = array("C", "L", "L", "C", "L", "R", "R", "R","R", "R", "L");             /* Release 5.2.1.20 */
            }
        }
    } elseif ($COM_LEVEL_SALP == 5) {
        if ($BKK_FLAG == 1) {
            $heading_width = array(6, 30, 45, 12, 10, 10, 10, 10, 10, 45);
            $heading_text = array("�ӴѺ|���", $FULLNAME_HEAD . "|", $COM_HEAD_01 . "|�ѧ�Ѵ/���˹�", "<**1**>|���˹�|������", "<**1**>|�дѺ", "<**1**>|�Ţ���", "�Թ��͹�ҹ", "<**2**>�Թ��ҵͺ᷹�����|������ 2", "<**2**>|������ 4", "�����˵�|");
            $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
            $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
            $data_align = array("C", "L", "L", "C", "C", "C", "R", "R", "R", "L");
        } else {
            if ($CARDNO_FLAG == 1) {
// ���          $heading_width = array(6, 30, 20,50, 15, 15, 15, 20, 20, 20, 20, 20, 50);
                $heading_width = array(6, 30, 20,50, 15, 15, 15, 20, 20, 20, 20, 50);           /* Release 5.2.1.20 */
// ���          $heading_text = array("�ӴѺ|���", $FULLNAME_HEAD . "|", "�Ţ��Шӵ��|��ЪҪ�", $COM_HEAD_01 . "|�ѧ�Ѵ / ���˹�", "<**1**>|�Ţ���", "<**1**>|�дѺ���˹�","�Թ��͹���","�ѵ���Թ��͹���������","�ҹ㹡�äӹǹ","�����з����","�ӹǹ�Թ���������͹","�ӹǹ�Թ������Ѻ","�����˵�");
                $heading_text = array("�ӴѺ|���", $FULLNAME_HEAD . "|", "�Ţ��Шӵ��|��ЪҪ�", $COM_HEAD_01 . "|�ѧ�Ѵ / ���˹�", "<**1**>|�Ţ���", "<**1**>|�дѺ���˹�","�Թ��͹���","�ҹ㹡�äӹǹ","�����з����","�ӹǹ�Թ���������͹","�ӹǹ��ҵͺ᷹","�����˵�");    /* Release 5.2.1.20 */ 
// ���          $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
                $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');       /* Release 5.2.1.20 */
// ���          $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"),(($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"),(($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
                $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"),(($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"),(($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));     /* Release 5.2.1.20 */
// ���          $data_align = array("C", "L","C", "L", "C", "L", "R", "R", "R","R", "R", "R", "L");
                $data_align = array("C", "L","C", "L", "C", "L", "R", "R","R", "R", "R", "L");         /* Release 5.2.1.20 */
            } else {
// ���          $heading_width = array(6, 30, 50, 15, 15, 15, 20, 20, 20, 20, 20, 50);
                $heading_width = array(6, 30, 50, 15, 15, 15, 20, 20, 20, 20, 50);          /* Release 5.2.1.20 */
// ���          $heading_text = array("�ӴѺ|���", $FULLNAME_HEAD . "|$CARDNO_TITLE", $COM_HEAD_01 . "|�ѧ�Ѵ / ���˹�", "<**1**>|�Ţ���", "<**1**>|�дѺ���˹�","�Թ��͹���","�ѵ���Թ��͹���������","�ҹ㹡�äӹǹ","�����з����","�ӹǹ�Թ���������͹","�ӹǹ�Թ������Ѻ","�����˵�");
                $heading_text = array("�ӴѺ|���", $FULLNAME_HEAD . "|$CARDNO_TITLE", $COM_HEAD_01 . "|�ѧ�Ѵ / ���˹�", "<**1**>|�Ţ���", "<**1**>|�дѺ���˹�","�Թ��͹���","�ҹ㹡�äӹǹ","�����з����","�ӹǹ�Թ���������͹","�ӹǹ��ҵͺ᷹","�����˵�");   /* Release 5.2.1.20 */
// ���          $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
                $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');         /* Release 5.2.1.20 */
// ���          $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
                $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"),(($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"),(($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));    /* Release 5.2.1.20 */
// ���          $data_align = array("C", "L", "L", "C", "L", "R", "R", "R","R", "R", "R", "L");
                $data_align = array("C", "L", "L", "C", "L", "R", "R","R", "R", "R", "L");        /* Release 5.2.1.20 */
            }
        }
    }else {
        if ($BKK_FLAG == 1) {
            $heading_width = array(6, 30, 15, 50, 10, 15, 15, 15, 50);
            $ws_head_line1 = array("�ӴѺ", $FULLNAME_HEAD, "������", $COM_HEAD_01, $COM_HEAD_01, $COM_HEAD_01, "�Թ��͹", "������Ѻ", "�����˵�");
            $ws_head_line2 = array("���", "", "���˹�", "���˹�/�ѧ�Ѵ", "�Ţ���", "�дѺ���˹�", "��͹����͹", "�Թ��͹", "");
            $heading_text = array("�ӴѺ|���", $FULLNAME_HEAD . "|", "������|���˹�", $COM_HEAD_01 . "|�ѧ�Ѵ / ���˹�", "<**1**>|�Ţ���", "<**1**>|�дѺ���˹�", "�Թ��͹|��͹����͹", "������Ѻ|�Թ��͹", "�����˵�|");
            $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
            $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
            $data_align = array("C", "L", "C", "L", "C", "L", "R", "R", "L");
        } else {
            if ($CARDNO_FLAG == 1) {
                $heading_width = array(6, 30, 15, 15, 50, 10, 15, 15, 15, 15, 10, 10, 10, 50);
                $ws_head_line1 = array("�ӴѺ", $FULLNAME_HEAD, "�Ţ��Шӵ��", "������", $COM_HEAD_01, $COM_HEAD_01, $COM_HEAD_01, "�Թ��͹", "������Ѻ", "�ӹǹ�Թ", "�����繵�", "�ҹ㹡��", "�š��", "�����˵�");
                $ws_head_line2 = array("���", "", "��ЪҪ�", "���˹�", "���˹�/�ѧ�Ѵ", "�Ţ���", "�дѺ���˹�", "��͹����͹", "�Թ��͹", "�������͹", "�������͹", "�ӹǳ", "�����Թ", "");
                $heading_text = array("�ӴѺ|���", $FULLNAME_HEAD . "|", "�Ţ��Шӵ��|��ЪҪ�", "������|���˹�", $COM_HEAD_01 . "|�ѧ�Ѵ / ���˹�", "<**1**>|�Ţ���", "<**1**>|�дѺ���˹�", "�Թ��͹|��͹����͹", "������Ѻ|�Թ��͹", "�ӹǹ�Թ|�������͹", "�����繵�|�������͹", "�ҹ㹡��|�ӹǳ", "�š��|�����Թ", "�����˵�|");
                $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
                $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
                $data_align = array("C", "L", "C", "C", "L", "C", "L", "R", "R", "R", "R", "R", "R", "L");
            } else {
                $heading_width = array(6, 30, 15, 50, 10, 15, 15, 15, 15, 10, 10, 10, 50);
                $ws_head_line1 = array("�ӴѺ", $FULLNAME_HEAD, "������", $COM_HEAD_01, $COM_HEAD_01, $COM_HEAD_01, "�Թ��͹", "������Ѻ", "�ӹǹ�Թ", "�����繵�", "�ҹ㹡��", "�š��", "�����˵�");
                $ws_head_line2 = array("���", "$CARDNO_TITLE", "���˹�", "���˹�/�ѧ�Ѵ", "�Ţ���", "�дѺ���˹�", "��͹����͹", "�Թ��͹", "�������͹", "�������͹", "�ӹǳ", "�����Թ", "");
                $heading_text = array("�ӴѺ|���", $FULLNAME_HEAD . "|$CARDNO_TITLE", "", "������|���˹�", $COM_HEAD_01 . "|�ѧ�Ѵ / ���˹�", "<**1**>|�Ţ���", "<**1**>|�дѺ���˹�", "�Թ��͹|��͹����͹", "������Ѻ|�Թ��͹", "�ӹǹ�Թ|�������͹", "�����繵�|�������͹", "�ҹ㹡��|�ӹǳ", "�š��|�����Թ", "�����˵�|");
                $heading_align = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
                $column_function = array((($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-x" : "ENUM-x"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"), (($NUMBER_DISPLAY == 2) ? "TNUM-2" : "ENUM-2"), (($NUMBER_DISPLAY == 2) ? "TNUM" : "ENUM"));
                $data_align = array("C", "L", "C", "C", "L", "C", "L", "R", "R", "R", "R", "R", "R", "L");
            }
        }
    }
}

// function ������ aggregate ��  SUM, AVG, PERC ����Ǻ�÷Ѵ (ROW)
// 									SUM-columnindex1-columnindex2-....  Ex SUM-1-3-4-7 ���¶֧ ������ͧ column ��� 1,3,4 ��� 7
// 									AVG-columnindex1-columnindex2-....  Ex AVG-1-3-4-7 ���¶֧ �������¢ͧ column ��� 1,3,4 ��� 7
// 									PERCn-columnindex1-columnindex2-....  Ex PERCn-1-3-4-7 n ��� column ��� ������������º��º������ (��� 1 ��� � column 1 3 4 7) 
//																												����������� n ���� �����ػ (100%) PERC3-1-3-4-7 ���� ���� column ��� 3 �����������㴢ͧ����� column 1,3,4,7
//	function ������ �ٻẺ (format) ��觨е�ͧ�����ѧ function ������ aggregate ���� (�����)
//									TNUM-n ��� ����¹�繵���Ţ�ͧ�� n ��� �ӹǹ�ȹ��� ����դ���� �ٹ�� ���ʴ��� -
//									TNUM0-n ��� ����¹�繵���Ţ�ͧ�� n ��� �ӹǹ�ȹ��� ����դ���� �ٹ�� ���ʴ��� 0
//									ENUM-n ��� �ʴ����Ţ���ҺԤ n ��� �ӹǹ�ȹ��� ����դ���� �ٹ�� ���ʴ��� -
//									ENUM0-n ��� �ʴ����Ţ���ҺԤ n ��� �ӹǹ�ȹ��� ����դ���� �ٹ�� ���ʴ��� 0
//
	$total_head_width = 0;
for ($iii = 0; $iii < count($heading_width); $iii++)
    $total_head_width += (int) $heading_width[$iii];

if (!$COLUMN_FORMAT) { // ��ͧ��˹��� element �������� form1 ����  <input type="hidden" name="COLUMN_FORMAT" value="<?=$COLUMN_FORMAT
    $arr_column_map = (array) null;
    $arr_column_sel = (array) null;
    for ($i = 0; $i < count($heading_text); $i++) {
        $arr_column_map[] = $i;  // link index �ͧ head 
        $arr_column_sel[] = 1;   // 1=�ʴ�	0=����ʴ�   �������ա�û�Ѻ����§ҹ �����ʴ����
    }
    $arr_column_width = $heading_width; // �������ҧ
    $arr_column_align = $data_align;  // align
    $COLUMN_FORMAT = implode(",", $arr_column_map) . "|" . implode(",", $arr_column_sel) . "|" . implode(",", $arr_column_width) . "|" . implode(",", $arr_column_align);
} else {
    $arrbuff = explode("|", $COLUMN_FORMAT);
    $arr_column_map = explode(",", $arrbuff[0]);  // index �ͧ head �������
    $arr_column_sel = explode(",", $arrbuff[1]); // 1=�ʴ�	0=����ʴ�
    $arr_column_width = explode(",", $arrbuff[2]); // �������ҧ
    $heading_width = $arr_column_width; // �������ҧ
    $arr_column_align = explode(",", $arrbuff[3]);  // align
}

$total_show_width = 0;
for ($iii = 0; $iii < count($arr_column_width); $iii++)
    if ($arr_column_sel[$iii] == 1)
        $total_show_width += (int) $arr_column_width[$iii];
?>