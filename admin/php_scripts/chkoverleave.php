<?php
    $ArrOldBgnDay =  explode("/",$OldBgnDay);
    $ArrOldEndDay =  explode("/",$OldEndDay);
    
    $ArrNewBgnDay =  explode("/",$NewBgnDay);
    $ArrNewEndDay =  explode("/",$NewEndDay);
    
    $OldBgnDay =$ArrOldBgnDay[2].$ArrOldBgnDay[1].$ArrOldBgnDay[0]; //25590201
    $OldEndDay =$ArrOldEndDay[2].$ArrOldEndDay[1].$ArrOldEndDay[0]; //25590201
    
    $NewBgnDay =$ArrNewBgnDay[2].$ArrNewBgnDay[1].$ArrNewBgnDay[0]; //25590201
    $NewEndDay =$ArrNewEndDay[2].$ArrNewEndDay[1].$ArrNewEndDay[0]; //25590201
    
    
    
    if($ApproveFlag=="1"){ //��¡�÷��͹��ѵ�����
        if($OldBgnDay==$NewBgnDay && $OldEndDay==$NewEndDay){ //���������¹�ѹ��� �ӡ�������¡��������
            echo "";
        }else{ //�ա������¹�ŧ�ѹ���
            //͹��ѵ������ж֧��˹�
            $ToBgnDay = (date('Y')+543).date('m').date('d');
            //echo getDayNum(getBeforeOfDay());
            $ToBgnDayFOREndDay= getBeforeDay(getDayNum(getBeforeOfDay()));
            //echo $ToBgnDayFOREndDay;
            //  echo $NewEndDay.' < '.$ToBgnDayFOREndDay;
            if($ToBgnDay>=$OldBgnDay){
                if($NewBgnDay>$OldEndDay  || $NewBgnDay < $OldBgnDay){ //������ѹ��� �ҡ�����ѹ���Ѩ�غѹ ���� ������ѹ��� �ҡ���� �֧�ѹ��� =>����͹�����������
                    //echo "X1"; /*test*/
                }
                if($NewEndDay < $OldBgnDay || $NewEndDay>$OldEndDay || $NewEndDay<$ToBgnDayFOREndDay){
                    //echo "X2"; /*test*/
                }

                /*if( (($NewBgnDay < $ToBgnDay) || ($NewBgnDay>$OldEndDay)) || (($NewEndDay >= $ToBgnDayFOREndDay) || ($NewEndDay>$OldEndDay)) ){
                    echo "1"; //�������ö����¹�ѹ���� �͡�ҡ��ǧ�ѹ��������
                } */   
            }elseif( (($NewBgnDay < $OldBgnDay) || ($NewBgnDay>$OldEndDay)) || (($NewEndDay < $OldBgnDay) || ($NewEndDay>$OldEndDay)) ){ //͹��ѵ�������ѧ���֧��˹�
                //echo "1"; //�������ö����¹�ѹ���� �͡�ҡ��ǧ�ѹ��������
            }
            //͹��ѵ��������Թ��˹�������(�����¡��͹��ѵ��͹��ѧ)
        }
    }else{ //��͹��ѵ�
        echo "";
    }
    
function getBeforeOfDay(){
    $date = date('Y-m-d');
    $date = strtotime($date);
    $date = strtotime("-1 day", $date);
    return date('Y-m-d', $date);
}
function getBeforeDay($num){
    $date = date('Y-m-d');
    $date = strtotime($date);
    $date = strtotime("-".($num+1)." day", $date);

    $arrDate = explode('-',date('Y-m-d', $date));
    return ($arrDate[0]+543).$arrDate[1].$arrDate[2];
}

    
  function getDayNum($day){ //$day = '2016-05-15';
    $day = explode("-",$day);
    $jd=cal_to_jd(CAL_GREGORIAN,$day[1],$day[2],$day[0]); //2011-01-29
    if(strtoupper(jddayofweek($jd,1)) == "SATURDAY" || strtoupper(jddayofweek($jd,1)) == "SUNDAY"){
        return 2;
    }
    return 1;
  }     
    
    
?>
