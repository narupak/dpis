<?php
$HH = $_GET['hh'];
$mm = $_GET['mm'];
$mmPlusBgn = $_GET['mmPlusBgn'];
if(empty($mmPlusBgn)){ $mmPlusBgn=0;}
$mmPlusEnd = $_GET['mmPlusEnd'];
if(empty($mmPlusEnd)){ $mmPlusEnd=0;}
$newtimestampBgn = strtotime(date('Y-m-d').' '.$HH.':'.$mm.' + '.$mmPlusBgn.' minute'); /*Bgn*/
$newtimestampEnd = strtotime(date('Y-m-d').' '.$HH.':'.$mm.' + '.$mmPlusEnd.' minute'); /*Bgn*/
$bgnTime =  date('H:i', $newtimestampBgn);
$EndTime =  date('H:i', $newtimestampEnd);
echo $bgnTime."<::>".$EndTime; /*08:25#ES#15:25*/
?>