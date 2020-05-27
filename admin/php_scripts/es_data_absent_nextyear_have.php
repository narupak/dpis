<?
    include("../../php_scripts/connect_database.php");
	include("../php_scripts/function_share.php");
	
	$PER_ID=$_GET['PER_ID'];
	$PER_STARTDATE=$_GET['PER_STARTDATE'];

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd); 
	

	function get_sum_absday($db_dpis1,$PER_ID,$TMP_START_DATE,$TMP_END_DATE){

                $cmd="
            WITH
            PakPonInFiscalYear AS
            (
              select * from PER_ABSENTHIS pa where pa.PER_ID=:PER_ID and trim(ab_code) = '04' and
                pa.abs_startdate between cast(to_char(TO_DATE(:FIRST_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) and 
                                         cast(to_char(TO_DATE(:END_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) and
                pa.abs_enddate between cast(to_char(TO_DATE(:FIRST_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) and 
                                       cast(to_char(TO_DATE(:END_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19))
            ),
            SeparateBegin AS
            (
              select * from PER_ABSENTHIS pa where pa.PER_ID=:PER_ID and trim(ab_code) = '04' and
                pa.abs_startdate < cast(to_char(TO_DATE(:FIRST_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) and 
                pa.abs_enddate between cast(to_char(TO_DATE(:FIRST_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) and
                                  cast(to_char(TO_DATE(:END_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) 
            ),
            SeparateEnd AS
            (
              select * from PER_ABSENTHIS pa where pa.PER_ID=:PER_ID and trim(ab_code) = '04' and
                pa.abs_enddate > cast(to_char(TO_DATE(:END_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) and 
                pa.abs_startdate between cast(to_char(TO_DATE(:FIRST_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) and
                                  cast(to_char(TO_DATE(:END_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) 
            )

            select nvl(sum(abs_day),0) sum_abs  from (
              select 1 a,ABS_STARTDATE,abs_day 
                from PakPonInFiscalYear 
              union all
                select 2,WORK_DATE,case when WORK_DATE=trim(b.ABS_STARTDATE) then 
                        case when b.ABS_STARTPERIOD=3 then 1 else 0.5 end
                      else case when WORK_DATE=trim(b.ABS_ENDDATE) then 
                              case when b.ABS_ENDPERIOD=3 then 1 else 0.5 end
                            else 1
                           end
                  end ABS_DAY
                from SeparateBegin b
                left join (
                  SELECT TO_CHAR((TO_DATE((select min(abs_startdate) from SeparateBegin), 'YYYY-MM-DD'))-1+ ROWNUM,'YYYY-MM-DD') AS WORK_DATE FROM ALL_OBJECTS
                        WHERE (TO_DATE((select min(abs_startdate) from SeparateBegin), 'YYYY-MM-DD'))-1+ ROWNUM 
                            <= TO_DATE((select max(abs_enddate) from SeparateBegin), 'YYYY-MM-DD')
                            and  TO_CHAR((TO_DATE((select min(abs_startdate) from SeparateBegin), 'YYYY-MM-DD'))-1+ ROWNUM, 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') NOT IN ('SAT', 'SUN')
                  ) d on (not exists (select null from PER_HOLIDAY 
                                      where HOL_DATE = d.WORK_DATE))
                and WORK_DATE between :FIRST_DATE and :END_DATE
              union all
                select 3,WORK_DATE,case when WORK_DATE=trim(e.ABS_STARTDATE) then 
                        case when e.ABS_STARTPERIOD=3 then 1 else 0.5 end
                      else case when WORK_DATE=trim(e.ABS_ENDDATE) then 
                              case when e.ABS_ENDPERIOD=3 then 1 else 0.5 end
                            else 1
                           end
                  end ABS_DAY
                from SeparateEnd e
                left join (
                  SELECT TO_CHAR((TO_DATE((select min(abs_startdate) from SeparateEnd), 'YYYY-MM-DD'))-1+ ROWNUM,'YYYY-MM-DD') AS WORK_DATE FROM ALL_OBJECTS
                        WHERE (TO_DATE((select min(abs_startdate) from SeparateEnd), 'YYYY-MM-DD'))-1+ ROWNUM 
                            <= TO_DATE((select max(abs_enddate) from SeparateEnd), 'YYYY-MM-DD')
                            and  TO_CHAR((TO_DATE((select min(abs_startdate) from SeparateEnd), 'YYYY-MM-DD'))-1+ ROWNUM, 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') NOT IN ('SAT', 'SUN')
                  ) d on (not exists (select null from PER_HOLIDAY 
                                      where HOL_DATE = d.WORK_DATE))
                and WORK_DATE between :FIRST_DATE and :END_DATE
            )";
                $cmd=str_replace(":PER_ID",$PER_ID,$cmd);
                $cmd=str_replace(":FIRST_DATE","'".$TMP_START_DATE."'",$cmd);
                $cmd=str_replace(":END_DATE","'".$TMP_END_DATE."'",$cmd);
                $db_dpis1->send_cmd_fast($cmd);
                //echo  '<br>'.$cmd.'<br>';
                $data1 = $db_dpis1->get_array_array();
                $sum_absday = $data1[0]; //C
                return $sum_absday;
        }
        
 
        //========================================= 05/09/2561 ==============================================
        //======================== คำนวณ เพิ่มวันลาสะสมอัตโนมัติ เมื่อถึงหรือ เกิน 6 เดือน =================================


            $temp_date = explode("/", $PER_STARTDATE);
			$CHECK_DATE = $temp_date[2] ."-". $temp_date[1] ."-". $temp_date[0];
            //echo $CHECK_DATE;
			
            $VC_YEAR=$temp_date[2]; 
            if($temp_date[2].$temp_date[1].$temp_date[0]>=$temp_date[2].'1001'){ 
              $VC_YEAR=$temp_date[2]+1; 
            }
            //echo $date_now;
            $A_START_DATE = ($VC_YEAR-543)."-10-01"; 
            $A_END_DATE = ($VC_YEAR-542)."-09-30";
			
			$cmd_p = " select VC_DAY from PER_VACATION 
							where VC_YEAR = '$VC_YEAR'  and PER_ID = $PER_ID";

            $db_dpis2->send_cmd($cmd_p);
            $data = $db_dpis2->get_array();
			$VC_DAY = $data[VC_DAY];
			
            $sum_absday= get_sum_absday($db_dpis1,$PER_ID,$A_START_DATE,$A_END_DATE);

		

		
		echo ($VC_DAY - $sum_absday)+0;
		
 
	
?>