with GETLIST_ORG_ID AS
                        (

                          SELECT ORG_ID FROM PER_ORG O WHERE :LAW_OR_ASS=1 AND O.ORG_ACTIVE=1 AND O.ORG_ID IN
                            (SELECT ORG_ID FROM PER_ORG START WITH ORG_ID=:ORG_ID CONNECT BY NOCYCLE PRIOR ORG_ID = ORG_ID_REF)  
                          UNION
                          SELECT ORG_ID FROM PER_ORG_ASS O WHERE :LAW_OR_ASS=2 AND O.ORG_ACTIVE=1 AND O.ORG_ID IN
                            (SELECT ORG_ID FROM PER_ORG_ASS START WITH ORG_ID=:ORG_ID CONNECT BY NOCYCLE PRIOR ORG_ID = ORG_ID_REF)  

                        )
                        ,LIST_PER_TYPE AS
                        (
                          SELECT CAST(:PER_TYPE AS NUMBER) PER_TYPE FROM DUAL 
                          UNION SELECT 1 FROM DUAL WHERE :PER_TYPE=0
                          UNION SELECT 2 FROM DUAL WHERE :PER_TYPE=0
                          UNION SELECT 3 FROM DUAL WHERE :PER_TYPE=0
                          UNION SELECT 4 FROM DUAL WHERE :PER_TYPE=0

                        )
                        ,PERSONINDEPART AS
                        (
                          /* BY LAW */
                          SELECT PER_ID FROM PER_PERSONAL P
                          LEFT JOIN PER_POSITION PP ON (PP.POS_ID=P.POS_ID)
                          WHERE :PER_STATUS AND P.PER_TYPE=1 AND P.PER_TYPE IN (SELECT PER_TYPE FROM LIST_PER_TYPE)
                            AND :LAW_OR_ASS=1 AND PP.ORG_ID IN (SELECT ORG_ID FROM GETLIST_ORG_ID)
                          UNION
                          SELECT PER_ID FROM PER_PERSONAL P
                          LEFT JOIN PER_POS_EMP PP ON (PP.POEM_ID=P.POEM_ID)
                          WHERE :PER_STATUS AND P.PER_TYPE=2 AND P.PER_TYPE IN (SELECT PER_TYPE FROM LIST_PER_TYPE)
                            AND :LAW_OR_ASS=1 AND PP.ORG_ID IN (SELECT ORG_ID FROM GETLIST_ORG_ID)
                          UNION
                          SELECT PER_ID FROM PER_PERSONAL P
                          LEFT JOIN PER_POS_EMPSER PP ON (PP.POEMS_ID=P.POEMS_ID)
                          WHERE :PER_STATUS AND P.PER_TYPE=3 AND P.PER_TYPE IN (SELECT PER_TYPE FROM LIST_PER_TYPE)
                            AND :LAW_OR_ASS=1 AND PP.ORG_ID IN (SELECT ORG_ID FROM GETLIST_ORG_ID)
                          UNION
                          SELECT PER_ID FROM PER_PERSONAL P
                          LEFT JOIN PER_POS_TEMP PP ON (PP.POT_ID=P.POT_ID)
                          WHERE :PER_STATUS AND P.PER_TYPE=4 AND P.PER_TYPE IN (SELECT PER_TYPE FROM LIST_PER_TYPE)
                            AND :LAW_OR_ASS=1 AND PP.ORG_ID IN (SELECT ORG_ID FROM GETLIST_ORG_ID)
                          /* BY ASSIGNMENT */
                          UNION
                          SELECT PER_ID FROM PER_PERSONAL P
                          WHERE :PER_STATUS AND P.PER_TYPE IN (SELECT PER_TYPE FROM LIST_PER_TYPE)
                            AND :LAW_OR_ASS=2 AND P.ORG_ID IN (SELECT ORG_ID FROM GETLIST_ORG_ID)
                        )
,DayList as
(
--  select to_char(x.DayList,'YYYY-MM') m, to_char(x.DayList,'dd') d 
  select x.DayList 
  ,case when (TO_CHAR(x.DayList, 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') in ('SAT','SUN')) then '1'
        else case when exists (select null from PER_HOLIDAY where SUBSTR(HOL_DATE,1,10)=to_char(x.DayList,'YYYY-MM-DD'))
                  then '1'
             else 
                  '0'
             end
   end status
  from (
      SELECT to_date(:YEARBGN, 'YYYY-MM-DD')-1+rownum AS DayList FROM all_objects
       WHERE to_date(:YEARBGN, 'YYYY-MM-DD')-1+rownum <= to_date(:YEAREND, 'YYYY-MM-DD')
  ) x
)
,DataList AS
(
  select * from (
    select p.per_id,to_char(DayList,'YYYY-MM') m, to_char(DayList,'dd') d
      --to_char(work_date,'YYYY-MM') m, to_char(work_date,'dd') d
      /* HOL,WORK_FLAG,ABSENT_FLAG,AB_CODE|ABSENT_FLAG,AB_CODE */
--      , to_char(holiday_flag) || ',' || to_char(work_flag) || ',' ||
      , to_char(status) || ',' || --nvl(to_char(work_flag),'*') || ',' ||
        nvl((select case when remark is not null then '0' else to_char(work_flag) end from per_work_time pwt where pwt.per_id = p.per_id and 
      to_char(DayList,'YYYY-MM-DD') = to_char(pwt.work_date,'YYYY-MM-DD')
      ),'*') || ',' ||
        case when (exists (select null from PER_ABSENTHIS pa where pa.PER_ID=p.PER_ID and --trim(ab_code) not in ('10','13')and 
                pa.abs_startdate < cast(to_char(DayList,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(DayList,'YYYY-MM-DD') as char(19))
      ))
            then    
                case when (d.status=0 or ((select ab_count from per_absenttype t where t.ab_code=
                        (select pa.ab_code from PER_ABSENTHIS pa where pa.PER_ID=p.PER_ID and --trim(ab_code) not in ('10','13')and 
                                  pa.abs_startdate < cast(to_char(DayList,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(DayList,'YYYY-MM-DD') as char(19))
                      ))=1)) then  '3'||
                          (select trim(ab_code)from PER_ABSENTHIS pa where pa.PER_ID=p.PER_ID and trim(ab_code) not in ('10','13') and
                              pa.abs_startdate < cast(to_char(DayList,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(DayList,'YYYY-MM-DD') as char(19))
                            ) else '000' 
                end || '|000' 
          else 
              nvl((select '3'||trim(ab_code)||'|000' from PER_ABSENTHIS pa where pa.PER_ID=p.PER_ID and abs_startperiod=3 and --trim(ab_code) not in ('10','13')and 
                pa.abs_startdate = cast(to_char(DayList,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(DayList,'YYYY-MM-DD') as char(19))
                and (status=0 or nvl((select ab_count from per_absenttype t where t.ab_code=pa.ab_code),0)=1)
                )
              ,
                nvl(
                  (select to_char(abs_startperiod)||trim(ab_code) from PER_ABSENTHIS pa where pa.PER_ID=p.PER_ID and abs_startperiod<>2 and --trim(ab_code) not in ('10','13')and 
                  pa.abs_startdate = cast(to_char(DayList,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(DayList,'YYYY-MM-DD') as char(19))
                   and (status=0 or nvl((select ab_count from per_absenttype t where t.ab_code=pa.ab_code),0)=1)
                   ),
                      (
                        nvl((select to_char(abs_startperiod)||trim(ab_code) from PER_ABSENTHIS pa where pa.PER_ID=p.PER_ID and abs_startperiod<>2 and trim(ab_code) not in ('10','13')and 
                              pa.abs_startdate = cast(to_char(DayList,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(DayList,'YYYY-MM-DD') as char(19))
                              and (status=0 or nvl((select ab_count from per_absenttype t where t.ab_code=pa.ab_code),0)=1)
                             ),
                            nvl((select to_char(abs_endperiod)||trim(ab_code) from PER_ABSENTHIS pa where pa.PER_ID=p.PER_ID and abs_endperiod<>2 and trim(ab_code) not in ('10','13')and 
                              pa.abs_startdate < cast(to_char(DayList,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(DayList,'YYYY-MM-DD') as char(19))
                              and (status=0 or nvl((select ab_count from per_absenttype t where t.ab_code=pa.ab_code),0)=1)
                            ),
                           '000')
                        )
                       )
                  ) || '|' ||
                  nvl(
                      (select to_char(abs_startperiod)||trim(ab_code) from PER_ABSENTHIS pa where pa.PER_ID=p.PER_ID and abs_startperiod=2 and --trim(ab_code) not in ('10','13')and 
                        pa.abs_startdate = cast(to_char(DayList,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(DayList,'YYYY-MM-DD') as char(19))
                        and (status=0 or nvl((select ab_count from per_absenttype t where t.ab_code=pa.ab_code),0)=1)
                      ),
                      (
                        nvl((select to_char(abs_startperiod)||trim(ab_code) from PER_ABSENTHIS pa where pa.PER_ID=p.PER_ID and abs_startperiod=2 and --trim(ab_code) not in ('10','13')and 
                              pa.abs_startdate = cast(to_char(DayList,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(DayList,'YYYY-MM-DD') as char(19))
                              and (status=0 or nvl((select ab_count from per_absenttype t where t.ab_code=pa.ab_code),0)=1)
                            ),
                           '000'
                          )
                       )
                  )
                )
        end as status
    from DayList d
--    left join per_personal p on (p.per_id in ( SELECT * FROM PERSONINDEPART WHERE PER_ID IN(14127/*16109*/)))
    left join per_personal p on (p.per_id in (:CONDITION_PER_ID))
  )
 )
/*
 ,PersonDaylist as
 (
    select dt.per_id per_id , d.m m, d.d d, d.status from DayList d
    left join (
      select distinct per_id from DataList
    ) dt on (1=1)
 )
*/   
select * from 
(
  select * from (
    select * from DataList -- where substr(status,3,1) <> '*'
/*    union all
    select * from PersonDaylist dl
    where exists (select null from PersonDaylist dt where dt.m=dl.m and dt.d=dl.d and dt.per_id=dl.per_id)
*/
  )

  pivot
  (
    max(status)
    for d in ('01' A,'02' B,'03' C,'04' D,'05' E,'06' F,'07' G,'08' H,'09' I,'10' J,
              '11' K,'12' L,'13' MM,'14' N,'15' O,'16' P,'17' Q,'18' R,'19' S,'20' T,
              '21' U,'22' V,'23' W,'24' X,'25' Y,'26' Z,'27' AA,'28' AB,'29' AC,'30' AD,'31' AE)
  )

) xx
order by per_id,m,d

