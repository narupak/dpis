
WITH
PerID_List as
(
  select 16320 per_id from dual
),
AllWorkDay As
(
  select * from ( -- LISTDATE, HOL 
    select x.*,(case when TO_CHAR(to_date(x.LISTDATE,'YYYY-MM-DD'), 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') IN ('SAT', 'SUN') then 1 else
      case when exists (select null from PER_HOLIDAY where SUBSTR(HOL_DATE,1,10) = x.LISTDATE) then 1 else 0 end end) HOL
    from (
      select TO_CHAR(LISTDATE,'YYYY-MM-DD') LISTDATE from (
        SELECT (TO_DATE((
            select trim(min(ABS_STARTDATE))
                from PER_ABSENTHIS where per_id in (select per_id from PerID_List)
          ), 'YYYY-MM-DD HH24:MI:SS'))-1+rownum AS LISTDATE FROM all_objects
         WHERE (TO_DATE((
            select trim(min(ABS_STARTDATE))
                from PER_ABSENTHIS where per_id in (select per_id from PerID_List) 
          ), 'YYYY-MM-DD HH24:MI:SS'))-1+ rownum <= TO_DATE((
            select trim(max(ABS_ENDDATE))
                from PER_ABSENTHIS where per_id in (select per_id from PerID_List)
          ), 'YYYY-MM-DD HH24:MI:SS')
      )
    ) x 
  )
)
select a.*,v.vc_day pakpon_sasom
from (
  select s.per_id,s.as_year,s.as_cycle,s.start_date,s.end_date,
    sum(case when AB_CODE='01' then ABS_DAY else 0 end) as SICK_ABS,
    sum(case when AB_CODE='03' then ABS_DAY else 0 end) as KIT_ABS,
    sum(case when AB_CODE='04' then ABS_DAY else 0 end) as PAKPON_ABS,
    sum(case when AB_CODE='10' then ABS_DAY else 0 end) as SAY_ABS,
    sum(case when AB_CODE='13' then ABS_DAY else 0 end) as KHAD_ABS,
    NVL(sum( case when AB_CODE not in ('01        ','03        ','04        ', '07        ','10        ','13        ') then ABS_DAY end),0)  LAOTH
  from per_absentsum s
  left join 
  (
    select h.per_id,LISTDATE,h.AB_CODE
           ,case when LISTDATE=trim(ABS_STARTDATE) then 
              case when ABS_STARTPERIOD=3 then 1 else 0.5 end
            else case when LISTDATE=trim(ABS_ENDDATE) then 
                    case when ABS_ENDPERIOD=3 then 1 else 0.5 end 
                 else 1 end
           end abs_day
    from per_absenthis h
    left join PER_ABSENTTYPE t on (t.AB_CODE=h.AB_CODE)
    left join AllWorkDay on (LISTDATE between h.ABS_STARTDATE and h.ABS_ENDDATE)
    where per_id = (select per_id from PerID_List)
    and ((t.AB_COUNT=2 and HOL=0) or t.AB_COUNT=1)
  ) l on (l.per_id=s.per_id and (LISTDATE between s.START_DATE and s.END_DATE))
  where s.per_id in (select per_id from PerID_List)
  group by s.per_id,as_year,s.as_cycle,s.start_date,s.end_date
) a
left join per_vacation v on (v.per_id=a.per_id and v.vc_year=a.as_year)
order by a.per_id,a.as_year,a.as_cycle
