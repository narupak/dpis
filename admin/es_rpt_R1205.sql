/*
	- :BEGINDATEAT	(can be any date; e.g.  '2016-04-01')
	- :TODATEAT	   (can be any date after begindate; e.g.  '2016-05-10')
*/
WITH
AllWorkDay As
(
  select * from ( -- LISTDATE, HOL 
    select x.*,(case when TO_CHAR(TO_DATE(x.LISTDATE,'YYYY-MM-DD'), 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') IN ('SAT', 'SUN') then 1 else
      case when exists (select null from PER_HOLIDAY where HOL_DATE = x.LISTDATE) then 1 else 0 end end) HOL
    from (
      select to_char(LISTDATE,'YYYY-MM-DD') LISTDATE from (
--      select LISTDATE from (
        SELECT (TO_DATE((
            select trim(min(ABS_STARTDATE))
                from PER_ABSENTHIS h
                where ((h.ABS_STARTDATE < :BEGINDATEAT||'9' and h.ABS_ENDDATE between :BEGINDATEAT and :TODATEAT||'9') or
                       (h.ABS_ENDDATE > :TODATEAT||'9' and h.ABS_STARTDATE between :BEGINDATEAT and :TODATEAT||'9') or 
                       (h.ABS_STARTDATE < :TODATEAT||'9' and h.ABS_ENDDATE > :TODATEAT||'9')
                      )
          ), 'YYYY-MM-DD'))-1+rownum AS LISTDATE FROM all_objects
         WHERE (TO_DATE((
            select trim(min(ABS_STARTDATE))
                from PER_ABSENTHIS h
                where ((h.ABS_STARTDATE < :BEGINDATEAT||'9' and h.ABS_ENDDATE between :BEGINDATEAT and :TODATEAT||'9') or
                       (h.ABS_ENDDATE > :TODATEAT||'9' and h.ABS_STARTDATE between :BEGINDATEAT and :TODATEAT||'9') or 
                       (h.ABS_STARTDATE < :TODATEAT||'9' and h.ABS_ENDDATE > :TODATEAT||'9')
                      )
          ), 'YYYY-MM-DD'))-1+ rownum <= TO_DATE((
            select trim(max(ABS_ENDDATE))
                from PER_ABSENTHIS h
                where ((h.ABS_STARTDATE < :BEGINDATEAT||'9' and h.ABS_ENDDATE between :BEGINDATEAT and :TODATEAT||'9') or
                       (h.ABS_ENDDATE > :TODATEAT||'9' and h.ABS_STARTDATE between :BEGINDATEAT and :TODATEAT||'9') or 
                       (h.ABS_STARTDATE < :TODATEAT||'9' and h.ABS_ENDDATE > :TODATEAT||'9')
                      )
          ), 'YYYY-MM-DD')
      )
    ) x 
  )
)
--select *
select PER_ID,
		sum( NVL(decode( AB_CODE, '01        ', ABS_DAY ),0) ) LSICK,
		sum( NVL(decode( AB_CODE, '01        ', CNT ) ,0)) LSICKCNT,
		sum( NVL(decode( AB_CODE, '03        ', ABS_DAY ),0) ) LAKIT,
		sum( NVL(decode( AB_CODE, '03        ', CNT ) ,0)) LAKITCNT,
		sum( NVL(decode( AB_CODE, '01        ', ABS_DAY ),0) ) + 
		    sum( NVL(decode( AB_CODE, '03        ', ABS_DAY ),0) ) LSICK_LAKIT,
		sum( NVL(decode( AB_CODE, '01        ', CNT ) ,0)) + 
		    sum( NVL(decode( AB_CODE, '03        ', CNT ) ,0)) LSICK_LAKITCNT,
		sum( NVL(decode( AB_CODE, '10        ', ABS_DAY ),0) ) LATE,
		sum( NVL(decode( AB_CODE, '04        ', ABS_DAY ),0) ) PAKPON,
		sum( NVL(decode( AB_CODE, '04        ', CNT ) ,0)) PAKPONCNT,
		NVL(sum( case when AB_CODE not in ('01        ','03        ','04        ', '10        ', '13        ') then ABS_DAY end),0)  LAOTH
from ( 
    /* ABS_STARTDATE and ABS_ENDDATE is in this month, so count for LaCount */
    select h.PER_ID,1,ABS_STARTDATE,h.AB_CODE,h.ABS_DAY,1 CNT
    from PER_ABSENTHIS h
    where trim(h.ABS_STARTDATE) between :BEGINDATEAT and :TODATEAT
        and trim(h.ABS_ENDDATE) between :BEGINDATEAT and :TODATEAT

    union all
    select h.PER_ID,2,LISTDATE,h.AB_CODE,
      case when LISTDATE=trim(h.ABS_STARTDATE) then 
            case when h.ABS_STARTPERIOD=3 then 1 else 0.5 end
          else case when LISTDATE=trim(h.ABS_ENDDATE) then 
                  case when h.ABS_ENDPERIOD=3 then 1 else 0.5 end
                else 1
               end
      end ABS_DAY, 
      case when LISTDATE=trim(h.ABS_STARTDATE) and LISTDATE between :BEGINDATEAT and :TODATEAT then 1 else 0 end CNT
    from PER_ABSENTHIS h
    left join PER_ABSENTTYPE t on (t.AB_CODE = h.AB_CODE)
    left join AllWorkDay on ((t.AB_COUNT=2 and HOL=0) or (t.AB_COUNT=1 and (HOL=0 or HOL=1)))
    where ((trim(h.ABS_STARTDATE) < :BEGINDATEAT and trim(h.ABS_ENDDATE) between :BEGINDATEAT and :TODATEAT||'9') or
           (trim(h.ABS_ENDDATE) > :TODATEAT||'9' and trim(h.ABS_STARTDATE) between :BEGINDATEAT and :TODATEAT||'9') or 
           (trim(h.ABS_STARTDATE) < :BEGINDATEAT and trim(h.ABS_ENDDATE) > :TODATEAT||'9')
          )
      and LISTDATE between :BEGINDATEAT and :TODATEAT
      and LISTDATE between trim(h.ABS_STARTDATE) and h.ABS_ENDDATE
) x
group by PER_ID
