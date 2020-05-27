/*
:PER_ID, 
:FIRST_DATE='2015-10-01'		วันที่เริ่มต้นปีงบประมาณ
:END_DATE='2016-09-30'		วันที่สิ้นสุดปีงบประมาณ
*/
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

select sum(abs_day) sum_abs  from (
  select 1 a,ABS_STARTDATE,abs_day 
/*    ,cast(to_char(TO_DATE(:FIRST_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) F,
    cast(to_char(TO_DATE(:END_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) E*/
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
)