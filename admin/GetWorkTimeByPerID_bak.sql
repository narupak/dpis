/*  GetWorkTimeByPerID.sql
  3 parameters:
	- :PER_ID  (per_id of per_personal; e.g 14418)
	- :BEGINDATEAT	(can be any date; e.g.  '2016-04-01 00:00:00')
	- :TODATEAT	   (can be any date after begindate; e.g.  '2016-05-10 23:59:59')
	- :SCANTYPE	   (Auto Exit Scan Flag;  2 = Auto, else as in scan time)
Update note:
  20160602 - Fixed bug case of Round, La at Afternoon (Period=2)
	20160518 - Add processing of Auto Exit Scan for SYSTEM_CONFIG of P_SCANTYPE = 1 or 2
	20160527 - Fix about WorkCycle of Count Hour and Request to Exit Early
*/

with 
AllScan As
(
  select /*+ MATERIALIZE */ distinct pt.PER_ID, TRUNC(case when pt.TIME_ADJUST is not NULL then pt.TIME_ADJUST else pt.TIME_STAMP end) WORK_DATE,
  (case when pt.TIME_ADJUST is not NULL then pt.TIME_ADJUST else pt.TIME_STAMP end) TIME_STAMP,
  nvl((select WC_CODE from PER_WORK_CYCLEHIS where PER_ID=pt.PER_ID 
          and (case when pt.TIME_ADJUST is not NULL then pt.TIME_ADJUST else pt.TIME_STAMP end) 
          between to_date(START_DATE, 'YYYY-MM-DD hh24:mi:ss') and to_date(nvl(END_DATE,:TODATEAT),'YYYY-MM-DD hh24:mi:ss')),
     (select WC_CODE from PER_WORK_CYCLE where WC_START='0830')) WC_CODE
     
  from PER_TIME_ATTENDANCE pt
  where pt.per_id  = :PER_ID and
        pt.TIME_STAMP between to_date(:BEGINDATEAT, 'YYYY-MM-DD HH24:MI:SS') and to_date(:TODATEAT, 'YYYY-MM-DD HH24:MI:SS')+1
)        
,AllScanAdjust As
(
/*
  select distinct pt.PER_ID, TRUNC(case when pt.TIME_ADJUST is not NULL then pt.TIME_ADJUST else pt.TIME_STAMP end) WORK_DATE,
  (case when pt.TIME_ADJUST is not NULL then pt.TIME_ADJUST else pt.TIME_STAMP end) TIME_STAMP,
  nvl((select WC_CODE from PER_WORK_CYCLEHIS where PER_ID=pt.PER_ID 
          and (case when pt.TIME_ADJUST is not NULL then pt.TIME_ADJUST else pt.TIME_STAMP end) 
          between to_date(START_DATE, 'YYYY-MM-DD hh24:mi:ss') and to_date(nvl(END_DATE,:TODATEAT),'YYYY-MM-DD hh24:mi:ss')),
     (select WC_CODE from PER_WORK_CYCLE where WC_START='0830')) WC_CODE
     
  from PER_TIME_ATTENDANCE pt
  where pt.per_id  = :PER_ID and
        pt.TIME_STAMP between to_date(:BEGINDATEAT, 'YYYY-MM-DD HH24:MI:SS') and to_date(:TODATEAT, 'YYYY-MM-DD HH24:MI:SS')+1
*/

  select /*+ MATERIALIZE */ * from AllScan

  union		/* Request to Exit Early */
  select /*+ MATERIALIZE */ tr.PER_ID, to_date(REQUEST_DATE, 'YYYY-MM-DD') WORK_DATE,
     case when (exists (select null from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=:PER_ID and trim(ab_code) not in ('10','13') and
              (pa.abs_startdate = tr.REQUEST_DATE ) and abs_startperiod=2)) then 
      case when tr.wc_code='-1' then
        GREATEST(
          nvl((select to_date(tr.REQUEST_DATE, 'YYYY-MM-DD')+pwc.NextDay_LeaveAfter+
            ((to_number(substr(pwc.Time_LeaveAfter,1,2))*60)+to_number(substr(pwc.Time_LeaveAfter,3,2)))/1440
           from PER_WORK_CYCLE pwc where pwc.wc_code=tr.wc_code),
           (select min(TIME_STAMP)+(3.5/24) from AllScan where AllScan.per_id=tr.PER_ID and AllScan.WORK_DATE=to_date(REQUEST_DATE, 'YYYY-MM-DD'))
           ),
           (select min(TIME_STAMP)+(3.5/24) from AllScan where AllScan.per_id=tr.PER_ID and AllScan.WORK_DATE=to_date(REQUEST_DATE, 'YYYY-MM-DD'))
         )
      else
        (select to_date(tr.REQUEST_DATE, 'YYYY-MM-DD')+pwc.NextDay_LeaveAfter+
        ((to_number(substr(pwc.Time_LeaveAfter,1,2))*60)+to_number(substr(pwc.Time_LeaveAfter,3,2)))/1440
         from PER_WORK_CYCLE pwc where pwc.wc_code=tr.wc_code) 
      end
     else
       case when (exists (select null from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=:PER_ID and trim(ab_code) not in ('10','13') and
                (pa.abs_startdate = tr.REQUEST_DATE ) and abs_startperiod=1)) then 
        case when tr.wc_code='-1' then
            GREATEST(
              nvl((select to_date(tr.REQUEST_DATE, 'YYYY-MM-DD')+pwc.NextDay_LeaveAfter+
                ((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2)))/1440
               from PER_WORK_CYCLE pwc where pwc.wc_code=tr.wc_code),
               (select min(TIME_STAMP)+(3.5/24) from AllScan where AllScan.per_id=tr.PER_ID and AllScan.WORK_DATE=to_date(REQUEST_DATE, 'YYYY-MM-DD'))
               )
              ,(select min(TIME_STAMP)+(3.5/24) from AllScan where AllScan.per_id=tr.PER_ID and AllScan.WORK_DATE=to_date(REQUEST_DATE, 'YYYY-MM-DD'))
            )
          else
            (select to_date(tr.REQUEST_DATE, 'YYYY-MM-DD')+pwc.NextDay_Exit+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2)))/1440
             from PER_WORK_CYCLE pwc where pwc.wc_code=tr.wc_code) 
        end
       else
        case when tr.wc_code='-1' then
          (select LEAST(min(TIME_STAMP),
            case when Start_Flag=1 then (to_date(tr.REQUEST_DATE, 'YYYY-MM-DD')+((to_number(substr(tr.Start_Time,1,2))*60)+to_number(substr(tr.Start_Time,3,2)))/1440
            ) else min(TIME_STAMP) end
          )+(8/24) from AllScan where AllScan.per_id=tr.PER_ID and AllScan.WORK_DATE=to_date(REQUEST_DATE, 'YYYY-MM-DD'))
        else
          (select to_date(tr.REQUEST_DATE, 'YYYY-MM-DD')+pwc.NextDay_Exit+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2)))/1440
           from PER_WORK_CYCLE pwc where pwc.wc_code=tr.wc_code) 
        end
      end
     end TIME_STAMP, tr.WC_CODE
  from TA_REQUESTTIME tr
  where tr.per_id  = :PER_ID and APPROVE_FLAG=1 and CANCEL_FLAG=0 and Req_Flag=1 and
    REQUEST_DATE between substr(:BEGINDATEAT,1,10) and substr(:TODATEAT,1,10)

  union		/* Request Forget to Exit Scan */
  select /*+ MATERIALIZE */ tr.PER_ID, to_date(REQUEST_DATE, 'YYYY-MM-DD') WORK_DATE,
    (select to_date(tr.REQUEST_DATE, 'YYYY-MM-DD')+tr.NextDay_Exit+((to_number(substr(tr.End_Time,1,2))*60)+to_number(substr(tr.End_Time,3,2)))/1440
     from PER_WORK_CYCLE pwc where pwc.wc_code=tr.wc_code) TIME_STAMP, tr.WC_CODE
  from TA_REQUESTTIME tr
  where tr.per_id  = :PER_ID and APPROVE_FLAG=1 and CANCEL_FLAG=0 and End_Flag=1 and
    REQUEST_DATE between substr(:BEGINDATEAT,1,10) and substr(:TODATEAT,1,10)

  union		/* Request Forget to Enter Scan */
  select /*+ MATERIALIZE */ tr.PER_ID, to_date(REQUEST_DATE, 'YYYY-MM-DD') WORK_DATE,
    (to_date(tr.REQUEST_DATE, 'YYYY-MM-DD')+((to_number(substr(tr.Start_Time,1,2))*60)+to_number(substr(tr.Start_Time,3,2)))/1440
    ) TIME_STAMP, tr.WC_CODE
  from TA_REQUESTTIME tr
  where tr.per_id  = :PER_ID and APPROVE_FLAG=1 and CANCEL_FLAG=0 and Start_Flag=1 and
    REQUEST_DATE between substr(:BEGINDATEAT,1,10) and substr(:TODATEAT,1,10)
)
,AllScanDay As
(
  select /*+ MATERIALIZE */ distinct PER_ID, WORK_DATE
  from AllScanAdjust 
  where WORK_DATE <= to_date(:TODATEAT, 'YYYY-MM-DD HH24:MI:SS')
)
,HolidayList As
(
  select /*+ MATERIALIZE */ distinct * from (
  select TO_DATE(HOL_DATE,'YYYY-MM-DD HH24:MI:SS') HOL_DATE 
    from PER_HOLIDAY where HOL_DATE >= substr(:BEGINDATEAT,1,10) and 
                           HOL_DATE < TO_CHAR(LAST_DAY(to_date(:TODATEAT, 'YYYY-MM-DD HH24:MI:SS'))+1,'YYYY-MM-DD')
  union
  select /*+ MATERIALIZE */ DayList from (
    select x.*,TO_CHAR(x.DayList, 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') DayListDay from (
    SELECT to_date(:BEGINDATEAT, 'YYYY-MM-DD HH24:MI:SS')-1+rownum AS DayList FROM all_objects
     WHERE to_date(:BEGINDATEAT, 'YYYY-MM-DD HH24:MI:SS')-1+ rownum <= LAST_DAY(to_date(:TODATEAT, 'YYYY-MM-DD HH24:MI:SS'))
    )x) where DayListDay IN ('SAT', 'SUN')
  )
)
,AllWorkDay As
(
  select /*+ MATERIALIZE */ * from (
    select /*+ MATERIALIZE */ x.*,TO_CHAR(x.WORK_DATE, 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') DayListDay
    from (
        SELECT (TO_DATE(:BEGINDATEAT, 'YYYY-MM-DD HH24:MI:SS'))-1+rownum AS WORK_DATE FROM all_objects
         WHERE (TO_DATE(:BEGINDATEAT, 'YYYY-MM-DD HH24:MI:SS'))-1+ rownum <= TO_DATE(:TODATEAT, 'YYYY-MM-DD HH24:MI:SS')
      ) x 
    where WORK_DATE not in (
      select to_date(HOL_DATE,'YYYY-MM-DD') from PER_HOLIDAY 
        where HOL_DATE >= substr(:BEGINDATEAT,1,10) and HOL_DATE <= substr(:TODATEAT,1,10)
    )
  ) where DayListDay NOT IN ('SAT', 'SUN')
)
,ProcessDayScan As
(
select /*+ MATERIALIZE */ 1 T, asd.PER_ID, asd.WORK_DATE, asa.WC_CODE
, (select min(a.TIME_STAMP) from AllScanAdjust a 
   where a.PER_ID=asd.PER_ID and a.WORK_DATE=asd.WORK_DATE
    and ((pwc.WorkCycle_Type=2 /* and To_char(a.TIME_STAMP,'HH24MI') between pwc.WC_Start and '2359' */)
      or (pwc.WorkCycle_Type=1 and a.TIME_STAMP between 
              TRUNC(a.WORK_DATE)+pwc.SameDay_StartScan+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2)) - Min_StartScan)/1440
          and TRUNC(a.WORK_DATE)+pwc.NextDay_EndScan+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2))-1)/1440
         )
        )
   ) SCAN_ENTTIME
, 
  case when pwc.WorkCycle_Type=2 or pwc.NextDay_EndScan = 0 then  /* count HOUR or Round END in 1 day */
    case when 
      (select max(a.TIME_STAMP) from AllScanAdjust a 
       where a.PER_ID=asd.PER_ID and a.WORK_DATE=asd.WORK_DATE
        and ((pwc.WorkCycle_Type=2 /* (and To_char(a.TIME_STAMP,'HH24MI') between pwc.WC_Start and '2359'*/ )
          or (pwc.WorkCycle_Type=1 and a.TIME_STAMP between 
                   TRUNC(a.WORK_DATE)+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2)) + Min_EndLateTime)/1440
               and TRUNC(a.WORK_DATE)+pwc.NextDay_EndScan+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2)) + Min_EndScan)/1440
               and a.TIME_STAMP <= 
                   nvl((
                      select to_date(nvl(pwch.END_DATE,to_char(last_day(to_date(:TODATEAT, 'YYYY-MM-DD HH24:MI:SS'))+7,'YYYY-MM-DD HH24:MI:SS')),'YYYY-MM-DD HH24:MI:SS') from PER_WORK_CYCLEHIS pwch 
                      where
                        pwch.PER_ID=a.PER_ID and pwch.WC_CODE=asa.WC_CODE and 
                        a.TIME_STAMP between to_date(pwch.START_DATE,'YYYY-MM-DD HH24:MI:SS')
                                         and to_date(
                                            nvl(pwch.END_DATE,
                                                to_char(last_day(to_date(:TODATEAT, 'YYYY-MM-DD HH24:MI:SS'))+7,
                                         'YYYY-MM-DD HH24:MI:SS')),'YYYY-MM-DD HH24:MI:SS')
                   ),a.TIME_STAMP)
             )
            )
       ) =
       (select min(a.TIME_STAMP) from AllScanAdjust a 
        where a.PER_ID=asd.PER_ID and a.WORK_DATE=asd.WORK_DATE
          and ((pwc.WorkCycle_Type=2 /* and To_char(a.TIME_STAMP,'HH24MI') between pwc.WC_Start and '2359' */ )
          or (pwc.WorkCycle_Type=1 and a.TIME_STAMP between 
                  TRUNC(a.WORK_DATE)+pwc.SameDay_StartScan+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2)) - Min_StartScan)/1440
              and TRUNC(a.WORK_DATE)+pwc.NextDay_EndScan+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2))-1)/1440
             )
            )
       ) then 
       /***  processing of Auto Exit Scan   ****/
        case when :SCANTYPE=2 and pwc.WorkCycle_Type=1 then 
                  case when pwc.WorkCycle_Type=2 then	/* count HOUR */
                      (select min(a.TIME_STAMP) from AllScanAdjust a 
                         where a.PER_ID=asd.PER_ID and a.WORK_DATE=asd.WORK_DATE
                          and ((pwc.WorkCycle_Type=2 /* and To_char(a.TIME_STAMP,'HH24MI') between pwc.WC_Start and '2359' */ )
                            or (pwc.WorkCycle_Type=1 and a.TIME_STAMP between 
                              TRUNC(a.WORK_DATE)+pwc.SameDay_StartScan+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2)) - Min_StartScan)/1440
                          and TRUNC(a.WORK_DATE)+pwc.NextDay_EndScan+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2))-1)/1440
                         )
                        )
                         ) + (8/24)
                    else
                      case when (select min(a.TIME_STAMP) from AllScanAdjust a 
                              where a.PER_ID=asd.PER_ID and a.WORK_DATE=asd.WORK_DATE
                                and ((pwc.WorkCycle_Type=2 /* and To_char(a.TIME_STAMP,'HH24MI') between pwc.WC_Start and '2359' */ )
                                or (pwc.WorkCycle_Type=1 and a.TIME_STAMP between 
                                        TRUNC(a.WORK_DATE)+pwc.SameDay_StartScan+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2)) - Min_StartScan)/1440
                                    and TRUNC(a.WORK_DATE)+pwc.NextDay_EndScan+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2))-1)/1440
                                   )
                                  )
                             ) is not null 
                            then
                            --%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
                            --cast(to_char(REQUEST_DATE,'YYYY-MM-DD') as char(19)
                              case when exists (select null from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=:PER_ID and trim(ab_code) not in ('10','13') and
                                    (pa.abs_startdate = cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)) ) and abs_startperiod = 2) then
                                TRUNC(asd.WORK_DATE)+pwc.NextDay_LeaveAfter+((to_number(substr(pwc.Time_LeaveAfter,1,2))*60)+to_number(substr(pwc.Time_LeaveAfter,3,2)))/1440
                              else
                                TRUNC(asd.WORK_DATE)+pwc.NextDay_EndScan+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2)))/1440
                              end
                            --%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
                            else
                              null
                            end
                  end
            else NULL
        end
     else
        case when ((select max(a.TIME_STAMP) from AllScanAdjust a 
                 where a.PER_ID=asd.PER_ID and a.WORK_DATE=asd.WORK_DATE
                  and ((pwc.WorkCycle_Type=2 /* and To_char(a.TIME_STAMP,'HH24MI') between pwc.WC_Start and '2359' */ )
                    or (pwc.WorkCycle_Type=1 and a.TIME_STAMP between 
                             TRUNC(a.WORK_DATE)+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2)) + Min_EndLateTime)/1440
                         and TRUNC(a.WORK_DATE)+pwc.NextDay_EndScan+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2)) + Min_EndScan)/1440
                         and a.TIME_STAMP <= 
                             nvl((
                                select to_date(nvl(pwch.END_DATE,to_char(last_day(to_date(:TODATEAT, 'YYYY-MM-DD HH24:MI:SS'))+7,'YYYY-MM-DD HH24:MI:SS')),'YYYY-MM-DD HH24:MI:SS') from PER_WORK_CYCLEHIS pwch 
                                where
                                  pwch.PER_ID=a.PER_ID and pwch.WC_CODE=asa.WC_CODE and 
                                  a.TIME_STAMP between to_date(pwch.START_DATE,'YYYY-MM-DD HH24:MI:SS')
                                                   and to_date(nvl(pwch.END_DATE,to_char(last_day(to_date(:TODATEAT, 'YYYY-MM-DD HH24:MI:SS'))+7,'YYYY-MM-DD HH24:MI:SS')),'YYYY-MM-DD HH24:MI:SS')
                             ),a.TIME_STAMP)
                       )
                      )
                 )  is null and
                 (select min(a.TIME_STAMP) from AllScanAdjust a 
                    where a.PER_ID=asd.PER_ID and a.WORK_DATE=asd.WORK_DATE
                      and ((pwc.WorkCycle_Type=2 /* and To_char(a.TIME_STAMP,'HH24MI') between pwc.WC_Start and '2359' */ )
                      or (pwc.WorkCycle_Type=1 and a.TIME_STAMP between 
                              TRUNC(a.WORK_DATE)+pwc.SameDay_StartScan+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2)) - Min_StartScan)/1440
                          and TRUNC(a.WORK_DATE)+pwc.NextDay_EndScan+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2))-1)/1440
                         )
                        )
                   ) is not null)
                 then
                   /***  processing of Auto Exit Scan   ****/
                      case when :SCANTYPE=2 and pwc.WorkCycle_Type=1 then 
                          case when pwc.WorkCycle_Type=2 then	/* count HOUR */
                              (select min(a.TIME_STAMP) from AllScanAdjust a 
                                 where a.PER_ID=asd.PER_ID and a.WORK_DATE=asd.WORK_DATE
                                  and ((pwc.WorkCycle_Type=2 /* and To_char(a.TIME_STAMP,'HH24MI') between pwc.WC_Start and '2359' */ )
                                    or (pwc.WorkCycle_Type=1 and a.TIME_STAMP between 
                                      TRUNC(a.WORK_DATE)+pwc.SameDay_StartScan+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2)) - Min_StartScan)/1440
                                  and TRUNC(a.WORK_DATE)+pwc.NextDay_EndScan+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2))-1)/1440
                                 )
                                )
                                 ) + (8/24)
                            else
                              --TRUNC(asd.WORK_DATE)+pwc.NextDay_EndScan+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2)))/1440
                            --%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
                              case when exists (select null from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=:PER_ID and trim(ab_code) not in ('10','13') and
                                    (pa.abs_startdate = cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)) ) and abs_startperiod = 2) then
                                TRUNC(asd.WORK_DATE)+pwc.NextDay_LeaveAfter+((to_number(substr(pwc.Time_LeaveAfter,1,2))*60)+to_number(substr(pwc.Time_LeaveAfter,3,2)))/1440
                              else
                                TRUNC(asd.WORK_DATE)+pwc.NextDay_EndScan+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2)))/1440
                              end
                            --%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
                          end
                        else NULL
                      end
            else
              (select max(a.TIME_STAMP) from AllScanAdjust a 
                 where a.PER_ID=asd.PER_ID and a.WORK_DATE=asd.WORK_DATE
                  and ((pwc.WorkCycle_Type=2 /* and To_char(a.TIME_STAMP,'HH24MI') between pwc.WC_Start and '2359' */ )
                    or (pwc.WorkCycle_Type=1 and a.TIME_STAMP between 
                             TRUNC(a.WORK_DATE)+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2)) + Min_EndLateTime)/1440
                         and TRUNC(a.WORK_DATE)+pwc.NextDay_EndScan+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2)) + Min_EndScan)/1440
                         and a.TIME_STAMP <= 
                             nvl((
                                select to_date(nvl(pwch.END_DATE,to_char(last_day(to_date(:TODATEAT, 'YYYY-MM-DD HH24:MI:SS'))+7,'YYYY-MM-DD HH24:MI:SS')),'YYYY-MM-DD HH24:MI:SS') from PER_WORK_CYCLEHIS pwch 
                                where
                                  pwch.PER_ID=a.PER_ID and pwch.WC_CODE=asa.WC_CODE and 
                                  a.TIME_STAMP between to_date(pwch.START_DATE,'YYYY-MM-DD HH24:MI:SS')
                                                   and to_date(nvl(pwch.END_DATE,to_char(last_day(to_date(:TODATEAT, 'YYYY-MM-DD HH24:MI:SS'))+7,'YYYY-MM-DD HH24:MI:SS')),'YYYY-MM-DD HH24:MI:SS')
                             ),a.TIME_STAMP)
                       )
                      )
                 )            
        end
     end  
  else    /* be round that End on NEXT DAY  */
    case when 
      (select max(a.TIME_STAMP) from AllScanAdjust a 
       where a.PER_ID=asd.PER_ID 
        and (
             (pwc.WorkCycle_Type=1 and a.TIME_STAMP between 
                   TRUNC(asd.WORK_DATE)+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2)) + Min_EndLateTime)/1440
               and TRUNC(asd.WORK_DATE)+pwc.NextDay_EndScan+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2)) + Min_EndScan)/1440
               and a.TIME_STAMP <= 
                   nvl((
                      select to_date(nvl(pwch.END_DATE,to_char(last_day(to_date(:TODATEAT, 'YYYY-MM-DD HH24:MI:SS'))+7,'YYYY-MM-DD HH24:MI:SS')),'YYYY-MM-DD HH24:MI:SS') from PER_WORK_CYCLEHIS pwch 
                      where
                        pwch.PER_ID=a.PER_ID and pwch.WC_CODE=asa.WC_CODE and 
                        a.TIME_STAMP between to_date(pwch.START_DATE,'YYYY-MM-DD HH24:MI:SS')
                                         and to_date(nvl(pwch.END_DATE,to_char(last_day(last_day(to_date(:TODATEAT, 'YYYY-MM-DD HH24:MI:SS'))+7)+1,'YYYY-MM-DD HH24:MI:SS')),'YYYY-MM-DD HH24:MI:SS')
                   ),a.TIME_STAMP)
             )
            )
       ) =
      (select min(a.TIME_STAMP) from AllScanAdjust a 
         where a.PER_ID=asd.PER_ID 
          and (
               (pwc.WorkCycle_Type=1 and a.TIME_STAMP between 
                    TRUNC(asd.WORK_DATE)+pwc.SameDay_StartScan+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2)) - Min_StartScan)/1440
                and TRUNC(asd.WORK_DATE)+pwc.NextDay_LeaveEarly+((to_number(substr(pwc.Time_LeaveEarly,1,2))*60)+to_number(substr(pwc.Time_LeaveEarly,3,2)))/1440
               )
              )
         ) then /* null */
	       /***  processing of Auto Exit Scan   ****/
            case when :SCANTYPE=2 and pwc.WorkCycle_Type=1
              then 
                TRUNC(asd.WORK_DATE)+pwc.NextDay_EndScan+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2)))/1440
              else 
                NULL
            end
      else
        case when (select max(a.TIME_STAMP) from AllScanAdjust a 
         where a.PER_ID=asd.PER_ID 
          and (
               (pwc.WorkCycle_Type=1 and a.TIME_STAMP between 
                     TRUNC(asd.WORK_DATE)+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2)) + Min_EndLateTime)/1440
                 and TRUNC(asd.WORK_DATE)+pwc.NextDay_EndScan+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2)) + Min_EndScan)/1440
                 and a.TIME_STAMP <= 
                     nvl((
                        select to_date(nvl(pwch.END_DATE,to_char(last_day(to_date(:TODATEAT, 'YYYY-MM-DD HH24:MI:SS'))+7,'YYYY-MM-DD HH24:MI:SS')),'YYYY-MM-DD HH24:MI:SS') from PER_WORK_CYCLEHIS pwch 
                        where
                          pwch.PER_ID=a.PER_ID and pwch.WC_CODE=asa.WC_CODE and 
                          a.TIME_STAMP between to_date(pwch.START_DATE,'YYYY-MM-DD HH24:MI:SS')
                                           and to_date(nvl(pwch.END_DATE,to_char(last_day(to_date(:TODATEAT, 'YYYY-MM-DD HH24:MI:SS'))+7,'YYYY-MM-DD HH24:MI:SS')),'YYYY-MM-DD HH24:MI:SS')
                     ),a.TIME_STAMP)
               )
              )
         ) is null then
             /***  processing of Auto Exit Scan   ****/
                case when :SCANTYPE=2  and pwc.WorkCycle_Type=1 and 
                              (select min(a.TIME_STAMP) from AllScanAdjust a 
                       where a.PER_ID=asd.PER_ID 
                        and (
                             (pwc.WorkCycle_Type=1 and a.TIME_STAMP between 
                                  TRUNC(asd.WORK_DATE)+pwc.SameDay_StartScan+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2)) - Min_StartScan)/1440
                              and TRUNC(asd.WORK_DATE)+pwc.NextDay_LeaveEarly+((to_number(substr(pwc.Time_LeaveEarly,1,2))*60)+to_number(substr(pwc.Time_LeaveEarly,3,2)))/1440
                             )
                            )
                       ) is not null
                  then 
                    TRUNC(asd.WORK_DATE)+pwc.NextDay_EndScan+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2)))/1440
                  else 
                    NULL
                end
          else         
            (select max(a.TIME_STAMP) from AllScanAdjust a 
               where a.PER_ID=asd.PER_ID 
                and (
                     (pwc.WorkCycle_Type=1 and a.TIME_STAMP between 
                           TRUNC(asd.WORK_DATE)+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2)) + Min_EndLateTime)/1440
                       and TRUNC(asd.WORK_DATE)+pwc.NextDay_EndScan+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2)) + Min_EndScan)/1440
                       and a.TIME_STAMP <= 
                           nvl((
                              select to_date(nvl(pwch.END_DATE,to_char(last_day(to_date(:TODATEAT, 'YYYY-MM-DD HH24:MI:SS'))+7,'YYYY-MM-DD HH24:MI:SS')),'YYYY-MM-DD HH24:MI:SS') from PER_WORK_CYCLEHIS pwch 
                              where
                                pwch.PER_ID=a.PER_ID and pwch.WC_CODE=asa.WC_CODE and 
                                a.TIME_STAMP between to_date(pwch.START_DATE,'YYYY-MM-DD HH24:MI:SS')
                                                 and to_date(nvl(pwch.END_DATE,to_char(last_day(to_date(:TODATEAT, 'YYYY-MM-DD HH24:MI:SS'))+7,'YYYY-MM-DD HH24:MI:SS')),'YYYY-MM-DD HH24:MI:SS')
                           ),a.TIME_STAMP)
                     )
                    )
               )         
          end
      end  
  end SCAN_EXITTIME
  
  /* both Type has OnTime, with same method; OK */
  ,case when pwc.WorkCycle_Type=1 then
      TRUNC(asd.WORK_DATE)+NextDay_OnTime+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2))+pwc.Min_OnTime)/1440+(59/86400) 
    else   
      TRUNC(asd.WORK_DATE)+((to_number(substr(pwc.On_Time,1,2))*60)+to_number(substr(pwc.On_Time,3,2)))/1440+(59/86400) 
    end On_Time 

  , case when pwc.WorkCycle_Type=1 then 
    TRUNC(asd.WORK_DATE)+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2)))/1440+(59/86400)
     else   
      TRUNC(asd.WORK_DATE)+((to_number(substr(pwc.On_Time,1,2))*60)+to_number(substr(pwc.On_Time,3,2)))/1440+(59/86400) 
    end Stamp_Time 
  ,pwc.Min_OnTime 
    
    
  /* both Type has OnTime, with different method; OK*/
  ,case when pwc.WorkCycle_Type=1 then
      TRUNC(asd.WORK_DATE)+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2))+pwc.Min_EndLateTime)/1440+(59/86400) 
    else
      TRUNC(asd.WORK_DATE)+((to_number(substr(pwc.On_Time,1,2))*60)+to_number(substr(pwc.On_Time,3,2))+pwc.Min_EndLateTime)/1440+(59/86400) 
    end EndLateTime
  
  /* OK */
  ,case when pwc.WorkCycle_Type=1 then
      TRUNC(asd.WORK_DATE)+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2))+pwc.MIN_LEAVEEARLY)/1440+(59/86400) 
    else case when pwc.MIN_LEAVEEARLY > 0 then
              TRUNC(asd.WORK_DATE)+((to_number(substr(pwc.On_Time,1,2))*60)+to_number(substr(pwc.On_Time,3,2))+pwc.MIN_LEAVEEARLY)/1440+(59/86400) 
        else
              NULL
        end
    end LEAVEEARLY 
    
  /* OK */
/* NOT USE  
  ,case when pwc.WorkCycle_Type=1 then
      TRUNC(asd.WORK_DATE)+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2))+pwc.MIN_LEAVEAFTER)/1440+(59/86400) 
    else case when pwc.MIN_LEAVEAFTER > 0 then
              TRUNC(asd.WORK_DATE)+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2))+pwc.MIN_LEAVEAFTER)/1440+(59/86400) 
        else
              NULL
        end
    end LEAVEAFTER 
   
  ,case when pwc.WorkCycle_Type=1 then 
      TRUNC(asd.WORK_DATE)+NextDay_Exit+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2)))/1440+(59/86400) 
    else 
      NULL 
    end LEAVEAFTER2
*/  
    /* Compute exit time */
  ,case when pwc.WorkCycle_Type=1 then 
       case when (exists (select null from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=:PER_ID and trim(ab_code) not in ('10','13') and
                (pa.abs_startdate = cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)) ) and abs_startperiod=2)) then 
         (select TRUNC(asd.WORK_DATE)+pwc.NextDay_LeaveAfter+((to_number(substr(pwc.Time_LeaveAfter,1,2))*60)+to_number(substr(pwc.Time_LeaveAfter,3,2)))/1440
         from PER_WORK_CYCLE pwc where pwc.wc_code=asa.wc_code) 
       else 
          TRUNC(asd.WORK_DATE)+NextDay_Exit+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2)))/1440 
       end
    else case when pwc.WC_End <> '0000' then
             case when (exists (select null from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=:PER_ID and trim(ab_code) not in ('10','13') and
                      (pa.abs_startdate = cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)) ) and abs_startperiod=2)) then 
               (select TRUNC(asd.WORK_DATE)+pwc.NextDay_LeaveAfter+((to_number(substr(pwc.Time_LeaveAfter,1,2))*60)+to_number(substr(pwc.Time_LeaveAfter,3,2)))/1440
               from PER_WORK_CYCLE pwc where pwc.wc_code=asa.wc_code) 
             else 
               TRUNC(asd.WORK_DATE)+NextDay_Exit+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2)))/1440
             end
         else
             case when (exists (select null from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=:PER_ID and trim(ab_code) not in ('10','13') and
                      (pa.abs_startdate = cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)) ) and abs_startperiod in (1,2))) then 
                (   /* SCAN_ENTTIME */
                  (case when (pwc.WorkCycle_Type=2 and (select abs_startperiod from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=:PER_ID and trim(ab_code) not in ('10','13') and
                             (pa.abs_startdate = cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)) )) = 1) then -- la morning
                    (select GREATEST(
                      TRUNC(asd.WORK_DATE)+pwc.NextDay_LeaveEarly+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2)))/1440
                      ,(select min(a.TIME_STAMP) from AllScanAdjust a 
                       where a.PER_ID=asd.PER_ID and a.WORK_DATE=asd.WORK_DATE
                        and ((pwc.WorkCycle_Type=2 /* and To_char(a.TIME_STAMP,'HH24MI') between pwc.WC_Start and '2359' */ )
                          or (pwc.WorkCycle_Type=1 and a.TIME_STAMP between 
                                  TRUNC(a.WORK_DATE)+pwc.SameDay_StartScan+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2)) - Min_StartScan)/1440
                              and TRUNC(a.WORK_DATE)+pwc.NextDay_EndScan+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2))-1)/1440
                             )
                            )
                       ) ) from dual)
                            else 
                              case when (pwc.WorkCycle_Type=2 and (select abs_startperiod from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=:PER_ID and trim(ab_code) not in ('10','13') and
                                (pa.abs_startdate = cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)) )) = 2) then  -- la after noon
                    (select GREATEST(
                      TRUNC(asd.WORK_DATE)+pwc.NextDay_LeaveEarly+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2)))/1440
                      ,(select min(a.TIME_STAMP) from AllScanAdjust a 
                       where a.PER_ID=asd.PER_ID and a.WORK_DATE=asd.WORK_DATE
                        and ((pwc.WorkCycle_Type=2 /* and To_char(a.TIME_STAMP,'HH24MI') between pwc.WC_Start and '2359' */ )
                          or (pwc.WorkCycle_Type=1 and a.TIME_STAMP between 
                                  TRUNC(a.WORK_DATE)+pwc.SameDay_StartScan+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2)) - Min_StartScan)/1440
                              and TRUNC(a.WORK_DATE)+pwc.NextDay_EndScan+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2))-1)/1440
                             )
                            )
                       ) ) from dual)
                              else 
                    (select GREATEST(
                      TRUNC(asd.WORK_DATE)+pwc.NextDay_LeaveEarly+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2)))/1440
                      ,(select min(a.TIME_STAMP) from AllScanAdjust a 
                       where a.PER_ID=asd.PER_ID and a.WORK_DATE=asd.WORK_DATE
                        and ((pwc.WorkCycle_Type=2 /* and To_char(a.TIME_STAMP,'HH24MI') between pwc.WC_Start and '2359' */ )
                          or (pwc.WorkCycle_Type=1 and a.TIME_STAMP between 
                                  TRUNC(a.WORK_DATE)+pwc.SameDay_StartScan+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2)) - Min_StartScan)/1440
                              and TRUNC(a.WORK_DATE)+pwc.NextDay_EndScan+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2))-1)/1440
                             )
                            )
                       ) ) from dual)
                              end
                            end 
                          )
                ) + (3.5/24)
             else 
                (   /* SCAN_ENTTIME */
                  case when (pwc.WorkCycle_Type=2) then
                    (select GREATEST(
                      TRUNC(asd.WORK_DATE)+pwc.NextDay_LeaveEarly+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2)))/1440
                      ,(select min(a.TIME_STAMP) from AllScanAdjust a 
                       where a.PER_ID=asd.PER_ID and a.WORK_DATE=asd.WORK_DATE
                        and ((pwc.WorkCycle_Type=2 /* and To_char(a.TIME_STAMP,'HH24MI') between pwc.WC_Start and '2359' */ )
                          or (pwc.WorkCycle_Type=1 and a.TIME_STAMP between 
                                  TRUNC(a.WORK_DATE)+pwc.SameDay_StartScan+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2)) - Min_StartScan)/1440
                              and TRUNC(a.WORK_DATE)+pwc.NextDay_EndScan+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2))-1)/1440
                             )
                            )
                       ) ) from dual)
                  else
                    (select min(a.TIME_STAMP) from AllScanAdjust a 
                       where a.PER_ID=asd.PER_ID and a.WORK_DATE=asd.WORK_DATE
                        and ((pwc.WorkCycle_Type=2 /* and To_char(a.TIME_STAMP,'HH24MI') between pwc.WC_Start and '2359' */ )
                          or (pwc.WorkCycle_Type=1 and a.TIME_STAMP between 
                                  TRUNC(a.WORK_DATE)+pwc.SameDay_StartScan+((to_number(substr(pwc.WC_Start,1,2))*60)+to_number(substr(pwc.WC_Start,3,2)) - Min_StartScan)/1440
                              and TRUNC(a.WORK_DATE)+pwc.NextDay_EndScan+((to_number(substr(pwc.WC_End,1,2))*60)+to_number(substr(pwc.WC_End,3,2))-1)/1440
                             )
                            )
                       )
                  end
                ) + (8/24)
              end
         end
    end EXIT_TIME

  /* OK */
  ,case when pwc.WorkCycle_Type=1 then
      TRUNC(asd.WORK_DATE)+NextDay_EndScan+((to_number(substr(pwc.End_Scan,1,2))*60)+to_number(substr(pwc.End_Scan,3,2)))/1440+(59/86400) 
    else case when pwc.End_Scan <> '0000' then
              TRUNC(asd.WORK_DATE)+NextDay_EndScan+((to_number(substr(pwc.End_Scan,1,2))*60)+to_number(substr(pwc.End_Scan,3,2)))/1440+(59/86400) 
        else
              TRUNC(asd.WORK_DATE)+((86400-1)/86400)
        end
    end END_SCAN 
    
  ,pwc.WorkCycle_Type
  ,case when (exists (select null from PER_ABSENTHIS pa where pa.PER_ID=asd.PER_ID and trim(ab_code) not in ('10','13') and
              pa.abs_startdate < cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)))) then '3'||
                (select trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=asd.PER_ID and trim(ab_code) not in ('10','13') and
              pa.abs_startdate < cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)))
        else 
            nvl(
              (select to_char(abs_startperiod)||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=asd.PER_ID and trim(ab_code) not in ('10','13') and
              pa.abs_startdate = cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19))),
                 (
                    nvl((select to_char(abs_startperiod)||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=asd.PER_ID and trim(ab_code) not in ('10','13') and
                          pa.abs_startdate = cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19))),
                        nvl((select to_char(abs_endperiod)||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=asd.PER_ID and trim(ab_code) not in ('10','13') and
                          pa.abs_startdate < cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19))),
                          0)
                      )
                )
              )
   end ABSENT
   ,(case when exists (select null from HolidayList where asd.WORK_DATE=HOL_DATE) then 1 else 0 end) HOLIDAY
   ,pwc.Time_LeaveAfter
from AllScanDay asd
left join AllScanAdjust asa on (asa.per_id=asd.per_id and asa.WORK_DATE=asd.WORK_DATE)
left join PER_WORK_CYCLE pwc on (pwc.WC_CODE=asa.WC_CODE)
where asd.per_id  = :PER_ID 
)
,FinalProcessDayScan As
(
  select /*+ MATERIALIZE */ distinct * from ProcessDayScan where (SCAN_ENTTIME is not NULL or SCAN_EXITTIME is not NULL)
)

select /*+ MATERIALIZE */* from (
select /*+ MATERIALIZE */ x.T,0,TO_CHAR(x.WORK_DATE,'YYYY-MM-DD') WORK_DATE,x.WC_CODE--,to_char(x.SCAN_ENTTIME,'HH24:MI') ENTTIME
  ,to_char(case when (x.HOLIDAY = 1 or x.SCAN_ENTTIME <= x.STAMP_TIME or x.SCAN_ENTTIME > 
    (case when WorkCycle_Type=2 and pwl.Late_Time is not null then x.WORK_DATE+((to_number(substr(pwl.Late_Time,1,2))*60)+
                                                          to_number(substr(pwl.Late_Time,3,2)))/1440+(59/86400) 
                                                                                    else --x.ENDLATETIME 
                                                                                    case when 
                                                                                    pwl.Late_Time is not null then x.WORK_DATE+((to_number(substr(pwl.Late_Time,1,2))*60)+
                                                          to_number(substr(pwl.Late_Time,3,2)))/1440+(59/86400) else 
                                                                                    x.ENDLATETIME end
         end) and x.wc_code <> -1)
  then x.SCAN_ENTTIME else greatest(x.STAMP_TIME,x.SCAN_ENTTIME-(x.MIN_ONTIME/1440)-
  ((case when pwl.Late_Time is not null then x.WORK_DATE+((to_number(substr(pwl.Late_Time,1,2))*60)+
                                                          to_number(substr(pwl.Late_Time,3,2)))/1440+(59/86400) 
                                                                                    else x.ON_TIME 
         end)-x.ON_TIME)
  )  end,'HH24:MI') ENTTIME 
--  ,to_char(x.SCAN_EXITTIME,'HH24:MI') EXITTIME
  , case when x.SCAN_EXITTIME is not null and substr(x.ABSENT,1,1) = 1 and x.SCAN_EXITTIME < nvl(LEAVEEARLY, 
                                                                                          (case when pwl.Late_Time is not null then x.WORK_DATE+((to_number(substr(pwl.Late_Time,1,2))*60)+
                                                                                                    to_number(substr(pwl.Late_Time,3,2)))/1440+(59/86400)+(3.5/24)
                                                                                                else (x.ON_TIME +(3.5/24)) /* Count HOUR */
                                                                                           end)
                                                                                         )
                                                                              then to_char(x.EXIT_TIME,'HH24:MI')
                                                                              else to_char(x.SCAN_EXITTIME,'HH24:MI') end
     EXITTIME
  
  ,x.ABSENT
  ,x.HOLIDAY --(case when exists (select null from HolidayList where x.WORK_DATE=HOL_DATE) then 1 else 0 end) HOLIDAY
  , case when x.HOLIDAY = 1 or substr(x.ABSENT,1,1) = 3 then 0 else 
      case when x.SCAN_ENTTIME is null then --4
        case when exists (select REMARK from TA_SET_EXCEPTPER a where a.per_id=x.per_id and CANCEL_FLAG=1 and flag_print=1
                      and to_char(x.WORK_DATE,'YYYY-MM-DD') >= a.START_DATE
                      and (a.END_DATE is null or to_char(x.WORK_DATE,'YYYY-MM-DD') <= a.END_DATE)
                    ) then 5 else 4 
        end
      else case when x.SCAN_EXITTIME is null and x.WC_CODE=-1 then 4
        else (case when x.SCAN_ENTTIME > (case when WorkCycle_Type=2 and pwl.Late_Time is not null then x.WORK_DATE+((to_number(substr(pwl.Late_Time,1,2))*60)+
                                                          to_number(substr(pwl.Late_Time,3,2)))/1440+(59/86400) 
                                          else x.ENDLATETIME 
         end)    /* scan > EndLessTime, so it will be Absent; if there is absent document */
                       then (case when substr(x.ABSENT,1,1) = 1 then (--2  /* La in this morning */
                                                          case when x.SCAN_ENTTIME > nvl(LEAVEEARLY, 
                                                                                          (case when pwl.Late_Time is not null then x.WORK_DATE+((to_number(substr(pwl.Late_Time,1,2))*60)+
                                                                                                    to_number(substr(pwl.Late_Time,3,2)))/1440+(59/86400)+(3.5/24)
                                                                                                else (x.ON_TIME +(3.5/24)) /* Count HOUR */
                                                                                           end)
                                                                                         )
                                                                    then --2
                                                                      case when exists (select REMARK from TA_SET_EXCEPTPER a where a.per_id=x.per_id and CANCEL_FLAG=1 and flag_print=1
                                                                                    and to_char(x.WORK_DATE,'YYYY-MM-DD') >= a.START_DATE
                                                                                    and (a.END_DATE is null or to_char(x.WORK_DATE,'YYYY-MM-DD') <= a.END_DATE)
                                                                                  ) then 5 else 2 end
                                                                    
                                                                else (--0
                                                                    /************* check exit **************/
                                                                    case when x.SCAN_EXITTIME < trunc(x.EXIT_TIME,'MI') and x.SCAN_EXITTIME > nvl(LEAVEEARLY, 
                                                                                          (case when pwl.Late_Time is not null then x.WORK_DATE+((to_number(substr(pwl.Late_Time,1,2))*60)+
                                                                                                    to_number(substr(pwl.Late_Time,3,2)))/1440+(59/86400)+(3.5/24)
                                                                                                else (x.ON_TIME +(3.5/24)) /* Count HOUR */
                                                                                           end)
                                                                                         )
                                                                              then 3
                                                                          else (case when x.SCAN_EXITTIME is null then (case when x.END_SCAN > sysdate then null else 4 end) 
                                                                                else (case when (x.WC_CODE=-1 and (trunc(x.SCAN_EXITTIME,'MI') - trunc(x.SCAN_ENTTIME,'MI'))<((3.5*60)/1440)) then 3 else 0 end) end)
                                                                    end
                                                                    )
                                                          end
                                                        )
                                  else -- 2
                                    case when exists (select REMARK from TA_SET_EXCEPTPER a where a.per_id=x.per_id and CANCEL_FLAG=1 and flag_print=1
                                                  and to_char(x.WORK_DATE,'YYYY-MM-DD') >= a.START_DATE
                                                  and (a.END_DATE is null or to_char(x.WORK_DATE,'YYYY-MM-DD') <= a.END_DATE)
                                                ) then 5 else --2 end
                                                
                                                case when x.SCAN_ENTTIME >
                                                  case when pwl.Late_Time is not null 
                                                    then x.WORK_DATE+((to_number(substr(pwl.Late_Time,1,2))*60)+
                                                          to_number(substr(pwl.Late_Time,3,2)))/1440+(59/86400) 
                                                    else 
                                                        x.ENDLATETIME 
                                                  end
                                                then 2 else 0 end
                                                end
                                               
                                                
                             end)
                  /* here scan <= EndLessTime; may be Normal(OnTime) or Late (if > OnTime+Specitial; excep there is absent document)  */
                   else (case when x.SCAN_ENTTIME > (case when pwl.Late_Time is not null then x.WORK_DATE+((to_number(substr(pwl.Late_Time,1,2))*60)+
                                      to_number(substr(pwl.Late_Time,3,2)))/1440+(59/86400)
                                                          else x.ON_TIME 
                                                     end)
                                  then (case when substr(x.ABSENT,1,1) = 1 
                                                then (--0
                                                      /************* check exit **************/
                                                      case when x.SCAN_EXITTIME < trunc(x.EXIT_TIME,'MI')
                                                                then  3
                                                            else (case when x.SCAN_EXITTIME is null then (case when x.END_SCAN > sysdate then null else 4 end) 
                                                                  else (case when (x.WC_CODE=-1 and (trunc(x.SCAN_EXITTIME,'MI') - trunc(x.SCAN_ENTTIME,'MI'))<((8*60)/1440)) then 3 else 0 end) end)
                                                      end
                                                )
                                             else (
                                                1 /* Late */
                                                )
                                        end)
                                /* Normal */
                          else (
                                /************* check exit **************/
                                case when substr(x.ABSENT,1,1) = 2 then /* 0 */
                                          case when x.SCAN_EXITTIME < trunc(x.EXIT_TIME,'MI')
                                                    then 3
                                                else --0 
                                                  case when x.WC_CODE=-1 then
                                                    case when x.EXIT_TIME < x.Stamp_Time then  -- Time_LeaveAfter = 0000
                                                        case when x.SCAN_EXITTIME < (x.SCAN_ENTTIME + (3.5/24)) 
                                                          then 3 
                                                          else 0 
                                                        end
                                                      else 
                                                        --0
                                                        case when x.SCAN_EXITTIME < TRUNC(x.SCAN_ENTTIME)+(((to_number(substr(x.Time_LeaveAfter,1,2))*60)+to_number(substr(x.Time_LeaveAfter,3,2)))/1440) 
                                                          then 3
                                                          else 0
                                                        end
                                                    end
                                                  else
                                                    0
                                                  end
                                          end
                                     else
                                          case when x.SCAN_EXITTIME < trunc(x.EXIT_TIME,'MI')
                                                    then --333 
                                                    (case when WorkCycle_Type=2 and pwl.Late_Time is not null then 
                                                      (case when x.SCAN_EXITTIME < x.On_Time+(8/24)-(59/86400) then 3 else 0 end)
                                                     else 
                                                      3
                                                     end)
                                                else (case when x.SCAN_EXITTIME is null then (case when x.END_SCAN > sysdate then null else 4 end) 
                                                      else (case when (x.WC_CODE=-1 and (trunc(x.SCAN_EXITTIME,'MI') - trunc(x.SCAN_ENTTIME,'MI'))<((3.5*60)/1440)) then 3 else 0 end) end)
                                          end
                                end
                              )
                        end)
              end)
          end
        end
      end Work_Flag
     ,(select REMARK from TA_SET_EXCEPTPER a where a.per_id=:PER_ID and CANCEL_FLAG=1 and flag_print=1
                      and to_char(x.WORK_DATE,'YYYY-MM-DD') >= a.START_DATE
                      and (a.END_DATE is null or to_char(x.WORK_DATE,'YYYY-MM-DD') <= a.END_DATE)
                    ) REMARK
/*
      ,LEAVEAFTER2-(4/24) eee
*/        
      ,(case when pwl.Late_Time is not null then x.WORK_DATE+((to_number(substr(pwl.Late_Time,1,2))*60)+
                                                          to_number(substr(pwl.Late_Time,3,2)))/1440+(59/86400) 
                                                                                    else x.ON_TIME 
         end)
        fff                                                                             
        ,pwl.Late_Time
        ,x.ENDLATETIME
        ,x.ON_TIME
        ,x.Stamp_Time
        ,x.Min_OnTime
        ,x.LEAVEEARLY
/*        
        ,x.END_SCAN
*/      
        ,x.SCAN_ENTTIME ENTTIME_REAL
        ,x.EXIT_TIME
        ,x.SCAN_EXITTIME
from FinalProcessDayScan x
left join PER_TIME_ATTENDANCE pt on (pt.PER_ID=x.PER_ID and pt.TIME_STAMP=x.SCAN_ENTTIME)
left join PER_TIME_ATT pta on (pta.TA_CODE=pt.TA_CODE)
left join PER_WORK_LATE pwl on (pwl.WL_CODE=pta.WL_CODE and pwl.WC_CODE=x.WC_CODE and pwl.WORK_DATE=to_char(x.WORK_DATE,'YYYY-MM-DD'))


union
select /*+ MATERIALIZE */ 2,0,to_char(asd.WORK_DATE,'YYYY-MM-DD')
  ,nvl((select WC_CODE from PER_WORK_CYCLEHIS where PER_ID=:PER_ID
          and asd.WORK_DATE between to_date(START_DATE, 'YYYY-MM-DD hh24:mi:ss') and 
              nvl(to_date(END_DATE, 'YYYY-MM-DD hh24:mi:ss'),LAST_DAY(to_date(:TODATEAT, 'YYYY-MM-DD HH24:MI:SS')+1))),(select WC_CODE from PER_WORK_CYCLE where WC_START='0830')) 
  WC_CODE 
  ,null,null
  ,case when (exists (select null from PER_ABSENTHIS pa where pa.PER_ID=:PER_ID and trim(ab_code) not in ('10','13')and 
              pa.abs_startdate < cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)))) then '3'||
                (select trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=:PER_ID and trim(ab_code) not in ('10','13') and
              pa.abs_startdate < cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19))) || ',0'
        else 

            nvl((select '3'||trim(ab_code)||',0' from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=:PER_ID and abs_startperiod=3 and /*trim(ab_code) not in ('10','13')and */
              pa.abs_startdate = cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)))
            ,

              nvl(
                (select to_char(abs_startperiod)||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=:PER_ID and abs_startperiod<>2 and trim(ab_code) not in ('10','13')and 
                pa.abs_startdate = cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19))),
                   (
                      nvl((select to_char(abs_startperiod)||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=:PER_ID and abs_startperiod<>2 and trim(ab_code) not in ('10','13')and 
                            pa.abs_startdate = cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19))),
                          nvl((select to_char(abs_endperiod)||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=:PER_ID and abs_endperiod<>2 and trim(ab_code) not in ('10','13')and 
                            pa.abs_startdate < cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19))),
                            0)
                        )
                  )
                ) || ',' ||
                nvl(
                (select to_char(abs_startperiod)||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=:PER_ID and abs_startperiod=2 and trim(ab_code) not in ('10','13')and 
                pa.abs_startdate = cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19))),
                   (
                      nvl((select to_char(abs_startperiod)||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=:PER_ID and abs_startperiod=2 and trim(ab_code) not in ('10','13')and 
                            pa.abs_startdate = cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19))),
                          0
                        )
                  )
                )

              )
   end ABSENT
  ,0 HOLIDAY_FLAG, --2 
    case when exists (select null from TA_SET_EXCEPTPER a where a.per_id=:PER_ID and CANCEL_FLAG=1 and flag_print=1
                      and to_char(asd.WORK_DATE,'YYYY-MM-DD') >= a.START_DATE
                      and (a.END_DATE is null or to_char(asd.WORK_DATE,'YYYY-MM-DD') <= a.END_DATE)
                    ) then 5 else /*2*/
                                case when ((
        case when (exists (select null from PER_ABSENTHIS pa where pa.PER_ID=:PER_ID and trim(ab_code) not in ('10','13')and 
                      pa.abs_startdate < cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)))) then 3
                else 
                    ((select nvl(sum(abs_startperiod),0) from PER_ABSENTHIS pa where pa.PER_ID=:PER_ID and trim(ab_code) not in ('10','13') and 
                      pa.abs_startdate = cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)))
                    +
                      (select nvl(sum(abs_startperiod),0) from PER_ABSENTHIS pa where pa.PER_ID=:PER_ID and trim(ab_code) not in ('10','13')and 
                       pa.abs_startdate = cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)))
                    +
                      (select nvl(sum(abs_endperiod),0) from PER_ABSENTHIS pa where pa.PER_ID=:PER_ID and trim(ab_code) not in ('10','13')and 
                       pa.abs_startdate < cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(asd.WORK_DATE,'YYYY-MM-DD') as char(19)))
                     )
           end  
          
          )
          ) >= 3 then 0 else 2 end

		    end
     ,(select REMARK from TA_SET_EXCEPTPER a where a.per_id=:PER_ID and CANCEL_FLAG=1 and flag_print=1
                      and to_char(asd.WORK_DATE,'YYYY-MM-DD') >= a.START_DATE
                      and (a.END_DATE is null or to_char(asd.WORK_DATE,'YYYY-MM-DD') <= a.END_DATE)
                    ) REMARK
  ,null
  ,null,null,null,null,null,null,null
,null,null
from 
AllWorkDay asd
where not exists (select null from FinalProcessDayScan a where a.per_id=:PER_ID and a.WORK_DATE=trunc(asd.WORK_DATE))
  and not exists (select null from TA_SET_EXCEPTPER a where a.per_id=:PER_ID and CANCEL_FLAG=1 and flag_print=0
                      and to_char(asd.WORK_DATE,'YYYY-MM-DD') >= a.START_DATE
                      and (a.END_DATE is null or to_char(asd.WORK_DATE,'YYYY-MM-DD') <= a.END_DATE)
                  )
  and asd.WORK_DATE >= to_date((select PER_STARTDATE from PER_PERSONAL where PER_ID=:PER_ID), 'YYYY-MM-DD')
                  
) x
order by x.work_date 