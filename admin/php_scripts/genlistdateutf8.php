<?php 
include("../../php_scripts/connect_database.php");
include("../php_scripts/function_share.php");
//$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
header("content-type: application/x-javascript; charset=TIS-620");





$abs_startdate =save_date($_GET['abs_startdate']);
$startperiod =$_GET['startperiod'];
$abs_enddate =save_date($_GET['abs_enddate']);
$endperiod =$_GET['endperiod'];
$ab_code =$_GET['ab_code'];
$per_id =$_GET['per_id'];

if($abs_startdate==$abs_enddate){
    $def_val_start=2;
    $def_val_end=1;
    if($startperiod==1 && $endperiod==1){
        $def_val_start=1;
        $def_val_end=2;
    }
}else{
    $def_val_start=2;
    $def_val_end=1;
}

$cmd = "WITH
				AllWorkDay As
				(
				  select /*+ MATERIALIZE */ * from (
					select /*+ MATERIALIZE */ x.*,(case when TO_CHAR(TO_DATE(x.LISTDATE,'YYYY-MM-DD'), 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') IN ('SAT', 'SUN') then 1 else
					  case when exists (select null from PER_HOLIDAY where HOL_DATE = x.LISTDATE) then 1 else 0 end end) HOL
					  , (case when LISTDATE='$abs_startdate' then (case when $startperiod<>$def_val_start then 1 else 0.5 end) 
						else (case when LISTDATE='$abs_enddate' then (case when $endperiod<>$def_val_end then 1 else 0.5 end) else 1 end )end) CNT
					from (
					  select to_char(LISTDATE,'YYYY-MM-DD') LISTDATE from (
						SELECT (TO_DATE('$abs_startdate', 'YYYY-MM-DD'))-1+rownum AS LISTDATE FROM all_objects
						 WHERE (TO_DATE('$abs_startdate', 'YYYY-MM-DD HH24:MI:SS'))-1+ rownum <= TO_DATE('$abs_enddate', 'YYYY-MM-DD')
					  )
					) x
				  )
				  where (select ab_count from PER_ABSENTTYPE where ab_code='$ab_code')=1 or HOL=0

				)

				select /*+ MATERIALIZE */ s.as_id,s.as_year,s.as_cycle,
                                    to_char((TO_DATE(min(LISTDATE), 'YYYY-MM-DD')),'DD/MM/YYYY','NLS_CALENDAR=''THAI BUDDHA''') abs_start,
                                    to_char((TO_DATE(max(LISTDATE), 'YYYY-MM-DD')),'DD/MM/YYYY','NLS_CALENDAR=''THAI BUDDHA''') abs_end,
                                    sum(a.CNT) nday ,min(LISTDATE) abs_startorder
                                    from AllWorkDay a
				left join PER_ABSENTSUM s on (s.per_id=$per_id and a.LISTDATE between s.start_date and s.end_date)
				group by s.as_id,s.as_year,s.as_cycle
				ORDER by abs_startorder";
//to_char((TO_DATE(substr(hol_date,0,10), 'YYYY-MM-DD')),'DD/MM/YYYY','NLS_CALENDAR=''THAI BUDDHA''')
//echo '<pre>'.$cmd;
$db_dpis->send_cmd($cmd);


		

?>
<table class="label_normal">
<?php while ($data = $db_dpis->get_array_array()) {
	$cmd = " SELECT		 AB_CODE_04 
							FROM		PER_ABSENTSUM 
							WHERE	PER_ID=$per_id and AS_YEAR = '$data[1]' and AS_CYCLE = $data[2] ";
		$count_abs_sum = $db_dpis->send_cmd($cmd);
		$data1 = $db_dpis->get_array();
		//echo "<pre>-------------------------- $cmd<br>";
		if($count_abs_sum  > 0){
			$AB_CODE_04 = $data1[AB_CODE_04];
		} 
		
		$cmd = " select VC_DAY from PER_VACATION 
						where VC_YEAR='$data[1]' and PER_ID=$per_id ";
		$count = $db_dpis->send_cmd($cmd);
		//echo "<pre> $cmd";
		$data2 = $db_dpis->get_array();
		$AB_COUNT_TOTAL_04 = $data2[VC_DAY]; 	// วันลาพักผ่อนที่ลาได้ทั้งหมดในปีงบประมาณ
		
		$ABS_DAY_NEXT_YAER = $AB_COUNT_TOTAL_04 - $AB_CODE_04;		// วันลาสะสมที่เหลือ
	
	
	
	?>
<tr>
	<td nowrap="nowrap">รอบที่ <?php echo $data[2];?> ปี <?php if(empty($data[1])){echo "[ยังไม่ได้กำหนดรอบ]";}else{echo $data[1];}?>  
		<input type="text" name="START_DATE_1" value="<?php echo $data[3];?>" class="readonly" readonly="" size="10"> ถึง 
		<input type="text" name="START_DATE_1" value="<?php echo $data[4];?>" class="readonly" readonly="" size="10"> : 
		<input type="text" name="START_DATE_1" id="cnt_dayx" value="<?php echo trim(round($data[5],2));?>" class="readonly" size="5"> วัน
		<input type="hidden" name="ABS_DAY_NEXT_YAER" value="<?php echo $ABS_DAY_NEXT_YAER;?>" class="readonly" size="5">
		<input type="hidden" name="NEXTYEAR_CHECK" value="<?php if(empty($data[1])){ echo "0";}else{echo $data[1];};?>" class="readonly" size="5">
		<input type="hidden" name="ABS_DAY_OLD" value="<?php echo trim(round($data[5],2));?>" style="text-align:right" class="textbox" onkeypress="return DigitOnly();" onchange="return chkOverNumDay();">
	</td>
</tr>
<?php }?>
</table>