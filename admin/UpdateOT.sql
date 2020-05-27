/*  UpdateOT.sql
  5 parameter
	- :BEGINDATEAT	= '2016-04-01'
	- :TODATEAT		  = '2016-04-01'
	- :ORG_ID (First ORG_ID for list all PER_ID)  e.g 3446 (TopMan) Note: ORD_ID get from assignment structure (PER_ORG_ASS)
	- :LAW_OR_ASS  (Get list from Org;  1 = in Law   2 = in Assignment)
	- :PER_TYPE  (per_status; e.g: 0=All, 1=1Karatchakarn, 2=Lookjang, 3=PanuknganRatchakarn, 4=LookjangChaukow )
*/

begin

update TA_PER_OT ot set (start_time,end_time,num_hrs,START_TIME_BFW,END_TIME_BFW)
  = (
  select distinct 
          to_char(

	   case when (case when x.APV_EXITTIME is not null then x.APV_EXITTIME else x.SCAN_EXITTIME end) > (
		
		case when x.SCAN_EXITTIME is not null or x.APV_EXITTIME is not null then
                    case when x.Holiday_Flag=0 then    /* working-day */
                         case when (select WorkCycle_Type from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)=1 then   -- round
                            TRUNC(x.WORK_DATE)+
                              (select NextDay_Exit from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                              +((to_number(substr(
                              (select WC_End from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                ,1,2))*60)+to_number(substr(
                              (select WC_End from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                ,3,2)))/1440
                         else -- count hour; from after 8 hour
                            (case when x.APV_ENTTIME is not null then x.APV_ENTTIME else x.SCAN_ENTTIME end)+(8/24)
                         end
                    else
                         (case when x.APV_ENTTIME is not null then x.APV_ENTTIME else x.SCAN_ENTTIME end)
                    end
                  else
                    null
                  end
	  ) then (
		case when x.SCAN_EXITTIME is not null or x.APV_EXITTIME is not null then
                    case when x.Holiday_Flag=0 then    /* working-day */
                         case when (select WorkCycle_Type from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)=1 then   -- round
                            TRUNC(x.WORK_DATE)+
                              (select NextDay_Exit from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                              +((to_number(substr(
                              (select WC_End from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                ,1,2))*60)+to_number(substr(
                              (select WC_End from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                ,3,2)))/1440
                         else -- count hour; from after 8 hour
                            (case when x.APV_ENTTIME is not null then x.APV_ENTTIME else x.SCAN_ENTTIME end)+(8/24)
                         end
                    else
                         (case when x.APV_ENTTIME is not null then x.APV_ENTTIME else x.SCAN_ENTTIME end)
                    end
                  else
                    null
                  end
	  )
	  else (case when x.APV_EXITTIME is not null then x.APV_EXITTIME else x.SCAN_EXITTIME end) end

          ,'HH24MI')start_time

          ,to_char(case when x.APV_EXITTIME is not null then x.APV_EXITTIME else x.SCAN_EXITTIME end,'HH24MI') end_time

          ,
          
    case when (
        case when OT_STATUS = 1 then 0 else
            case when x.SCAN_EXITTIME is not null or x.APV_EXITTIME is not null then
                trunc((((case when x.APV_EXITTIME is not null then x.APV_EXITTIME else x.SCAN_EXITTIME end) -
                      (case when x.Holiday_Flag=0 then    /* working-day */
                           case when (select WorkCycle_Type from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)=1 then   -- round
                              TRUNC(x.WORK_DATE)+
                                (select NextDay_Exit from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                +((to_number(substr(
                                (select WC_End from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                  ,1,2))*60)+to_number(substr(
                                (select WC_End from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                  ,3,2)))/1440
                           else -- count hour; from after 8 hour
                              (case when x.APV_ENTTIME is not null then trunc(x.APV_ENTTIME,'MI') else trunc(x.SCAN_ENTTIME,'MI') end)+(8/24)
                           end
                      else
                           (case when x.APV_ENTTIME is not null then trunc(x.APV_ENTTIME,'MI') else trunc(x.SCAN_ENTTIME,'MI') end)
                      end)
                    )* 24 ) - (case when x.Holiday_Flag=0 then 0 else
                          (case when (case when x.APV_ENTTIME is not null then x.APV_ENTTIME else x.SCAN_ENTTIME end) < x.WORK_DATE+(12/24) and (case when x.APV_EXITTIME is not null then x.APV_EXITTIME else x.SCAN_EXITTIME end) >= x.WORK_DATE+(13/24) then 1
                              else case when (case when x.APV_ENTTIME is not null then x.APV_ENTTIME else x.SCAN_ENTTIME end) < x.WORK_DATE++(12/24) and (case when x.APV_EXITTIME is not null then x.APV_EXITTIME else x.SCAN_EXITTIME end) <= x.WORK_DATE+(13/24) then ((case when x.APV_EXITTIME is not null then x.APV_EXITTIME else x.SCAN_EXITTIME end) - (x.WORK_DATE+(12/24)))
                                        else (((x.WORK_DATE+(13/24)) - (case when x.APV_ENTTIME is not null then x.APV_ENTTIME else x.SCAN_ENTTIME end)))
                                   end
                            end)
                        end))           
            else
              0
            end 
          end +  /* morning OT */
          
          case when OT_STATUS = 2 then 0 else
            nvl(trunc(((
            case when (case when x.APV_ENTTIME is not null then x.APV_ENTTIME else x.SCAN_ENTTIME end) < (
      
                  case when x.SCAN_ENTTIME is not null or x.APV_ENTTIME is not null then
                           case when (select WorkCycle_Type from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)=1 then   -- round
                              TRUNC(x.WORK_DATE)+((to_number(substr(
                                (select WC_Start from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                  ,1,2))*60)+to_number(substr(
                                (select WC_Start from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                  ,3,2)))/1440
                           else -- count hour; from after 8 hour
                              null
                           end
                    else
                      null
                    end
          ) then (
            case when x.SCAN_ENTTIME is not null or x.APV_ENTTIME is not null then
                  case when x.SCAN_ENTTIME is not null or x.APV_ENTTIME is not null then
                           case when (select WorkCycle_Type from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)=1 then   -- round
                              TRUNC(x.WORK_DATE)+((to_number(substr(
                                (select WC_Start from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                  ,1,2))*60)+to_number(substr(
                                (select WC_Start from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                  ,3,2)))/1440
                           else -- count hour; from after 8 hour
                              NULL
                           end
                    else
                      null
                    end
                    else
                      null
                    end
          )
          end
         - 
           case when (case when x.APV_ENTTIME is not null then x.APV_ENTTIME else x.SCAN_ENTTIME end) < (
      
                  case when x.SCAN_ENTTIME is not null or x.APV_ENTTIME is not null then
                           case when (select WorkCycle_Type from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)=1 then   -- round
                              TRUNC(x.WORK_DATE)+((to_number(substr(
                                (select WC_Start from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                  ,1,2))*60)+to_number(substr(
                                (select WC_Start from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                  ,3,2)))/1440
                           else -- count hour; from after 8 hour
                              null
                           end
                    else
                      null
                    end
          ) then (case when x.APV_ENTTIME is not null then x.APV_ENTTIME else x.SCAN_ENTTIME end)
          end
        )) * 24),0)
        end
      
      ) > (case when x.Holiday_Flag=0 then 4 else 7 end) then (case when x.Holiday_Flag=0 then 4 else 7 end)
      else
        case when OT_STATUS = 1 then 0 else
            case when x.SCAN_EXITTIME is not null or x.APV_EXITTIME is not null then
                trunc((((case when x.APV_EXITTIME is not null then x.APV_EXITTIME else x.SCAN_EXITTIME end) -
                      (case when x.Holiday_Flag=0 then    /* working-day */
                           case when (select WorkCycle_Type from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)=1 then   -- round
                              TRUNC(x.WORK_DATE)+
                                (select NextDay_Exit from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                +((to_number(substr(
                                (select WC_End from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                  ,1,2))*60)+to_number(substr(
                                (select WC_End from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                  ,3,2)))/1440
                           else -- count hour; from after 8 hour
                              (case when x.APV_ENTTIME is not null then trunc(x.APV_ENTTIME,'MI') else trunc(x.SCAN_ENTTIME,'MI') end)+(8/24)
                           end
                      else
                           (case when x.APV_ENTTIME is not null then trunc(x.APV_ENTTIME,'MI') else trunc(x.SCAN_ENTTIME,'MI') end)
                      end)
                    )* 24 ) - (case when x.Holiday_Flag=0 then 0 else
                          (case when (case when x.APV_ENTTIME is not null then x.APV_ENTTIME else x.SCAN_ENTTIME end) < x.WORK_DATE+(12/24) and (case when x.APV_EXITTIME is not null then x.APV_EXITTIME else x.SCAN_EXITTIME end) >= x.WORK_DATE+(13/24) then 1
                              else case when (case when x.APV_ENTTIME is not null then x.APV_ENTTIME else x.SCAN_ENTTIME end) < x.WORK_DATE++(12/24) and (case when x.APV_EXITTIME is not null then x.APV_EXITTIME else x.SCAN_EXITTIME end) <= x.WORK_DATE+(13/24) then ((case when x.APV_EXITTIME is not null then x.APV_EXITTIME else x.SCAN_EXITTIME end) - (x.WORK_DATE+(12/24)))
                                        else (((x.WORK_DATE+(13/24)) - (case when x.APV_ENTTIME is not null then x.APV_ENTTIME else x.SCAN_ENTTIME end)))
                                   end
                            end)
                        end))           
            else
              0
            end 
          end +  /* morning OT */
          
          case when OT_STATUS = 2 then 0 else
            nvl(trunc(((
            case when (case when x.APV_ENTTIME is not null then x.APV_ENTTIME else x.SCAN_ENTTIME end) < (
      
                  case when x.SCAN_ENTTIME is not null or x.APV_ENTTIME is not null then
                           case when (select WorkCycle_Type from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)=1 then   -- round
                              TRUNC(x.WORK_DATE)+((to_number(substr(
                                (select WC_Start from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                  ,1,2))*60)+to_number(substr(
                                (select WC_Start from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                  ,3,2)))/1440
                           else -- count hour; from after 8 hour
                              null
                           end
                    else
                      null
                    end
          ) then (
            case when x.SCAN_ENTTIME is not null or x.APV_ENTTIME is not null then
                  case when x.SCAN_ENTTIME is not null or x.APV_ENTTIME is not null then
                           case when (select WorkCycle_Type from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)=1 then   -- round
                              TRUNC(x.WORK_DATE)+((to_number(substr(
                                (select WC_Start from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                  ,1,2))*60)+to_number(substr(
                                (select WC_Start from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                  ,3,2)))/1440
                           else -- count hour; from after 8 hour
                              NULL
                           end
                    else
                      null
                    end
                    else
                      null
                    end
          )
          end
         - 
           case when (case when x.APV_ENTTIME is not null then x.APV_ENTTIME else x.SCAN_ENTTIME end) < (
      
                  case when x.SCAN_ENTTIME is not null or x.APV_ENTTIME is not null then
                           case when (select WorkCycle_Type from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)=1 then   -- round
                              TRUNC(x.WORK_DATE)+((to_number(substr(
                                (select WC_Start from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                  ,1,2))*60)+to_number(substr(
                                (select WC_Start from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                  ,3,2)))/1440
                           else -- count hour; from after 8 hour
                              null
                           end
                    else
                      null
                    end
          ) then (case when x.APV_ENTTIME is not null then x.APV_ENTTIME else x.SCAN_ENTTIME end)
          end
        )) * 24),0)
        end

      end
      num_ot
    ,          
     case when x.Holiday_Flag<>0 then null else
              to_char(
    
         case when (case when x.APV_ENTTIME is not null then x.APV_ENTTIME else x.SCAN_ENTTIME end) < (
        
                    case when x.SCAN_ENTTIME is not null or x.APV_ENTTIME is not null then
                             case when (select WorkCycle_Type from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)=1 then   -- round
                                TRUNC(x.WORK_DATE)+((to_number(substr(
                                  (select WC_Start from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                    ,1,2))*60)+to_number(substr(
                                  (select WC_Start from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                    ,3,2)))/1440
                             else -- count hour; from after 8 hour
                                null
                             end
                      else
                        null
                      end
        ) then (case when x.APV_ENTTIME is not null then x.APV_ENTTIME else x.SCAN_ENTTIME end)
        end
    
              ,'HH24MI') 
      end start_time_bfw
    
     ,case when x.Holiday_Flag<>0 then null else
              to_char(
    
         case when (case when x.APV_ENTTIME is not null then x.APV_ENTTIME else x.SCAN_ENTTIME end) < (
        
                    case when x.SCAN_ENTTIME is not null or x.APV_ENTTIME is not null then
                             case when (select WorkCycle_Type from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)=1 then   -- round
                                TRUNC(x.WORK_DATE)+((to_number(substr(
                                  (select WC_Start from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                    ,1,2))*60)+to_number(substr(
                                  (select WC_Start from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                    ,3,2)))/1440
                             else -- count hour; from after 8 hour
                                null
                             end
                      else
                        null
                      end
        ) then (
          case when x.SCAN_ENTTIME is not null or x.APV_ENTTIME is not null then
                    case when x.SCAN_ENTTIME is not null or x.APV_ENTTIME is not null then
                             case when (select WorkCycle_Type from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)=1 then   -- round
                                TRUNC(x.WORK_DATE)+((to_number(substr(
                                  (select WC_Start from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                    ,1,2))*60)+to_number(substr(
                                  (select WC_Start from PER_WORK_CYCLE pwc where pwc.WC_CODE=x.WC_CODE)
                                    ,3,2)))/1440
                             else -- count hour; from after 8 hour
                                NULL
                             end
                      else
                        null
                      end
                      else
                        null
                      end
        )
        end
    
              ,'HH24MI') 
      end end_time_bfw


     from PER_WORK_TIME x 
     where (ot.PER_ID=x.PER_ID and ot.OT_DATE=to_char(x.WORK_DATE,'YYYY-MM-DD') 
            and rownum=1)
     )

     
where ot.AUDIT_FLAG <> 1
  and ot.OT_DATE >= substr(:BEGINDATEAT,1,10) and ot.OT_DATE <= substr(:TODATEAT,1,10)
  and exists (select null from PER_WORK_TIME x 
       where (ot.PER_ID=x.PER_ID and ot.OT_DATE=to_char(x.WORK_DATE,'YYYY-MM-DD')))
  and ot.PER_ID in (

          with 
          GetList_ORG_ID As
          (
          
            select org_id from PER_ORG o where :LAW_OR_ASS=1 and o.org_active=1 and o.org_id in
              (select org_id from PER_ORG start with ORG_ID=:ORG_ID CONNECT BY NOCYCLE PRIOR org_id = ORG_ID_REF)  
            union
            select org_id from PER_ORG_ASS o where :LAW_OR_ASS=2 and o.org_active=1 and o.org_id in
              (select org_id from PER_ORG_ASS start with ORG_ID=:ORG_ID CONNECT BY NOCYCLE PRIOR org_id = ORG_ID_REF)  
          
          )
          ,List_Per_Type as
          (
            select CAST(:PER_TYPE AS number) PER_TYPE from dual 
            union select 1 from dual where :PER_TYPE=0
            union select 2 from dual where :PER_TYPE=0
            union select 3 from dual where :PER_TYPE=0
            union select 4 from dual where :PER_TYPE=0
            
          )
          ,PersonInDepart As
          (
            /* By Law */
            select per_id from PER_PERSONAL p
            LEFT JOIN PER_POSITION pp on (pp.POS_ID=p.POS_ID)
            where p.per_status=1 and p.per_type=1 and p.per_type in (select per_type from List_Per_Type)
              and :LAW_OR_ASS=1 and pp.ORG_ID in (select org_id from GetList_ORG_ID)
            union
            select per_id from PER_PERSONAL p
            LEFT JOIN PER_POS_EMP pp on (pp.POEM_ID=p.POEM_ID)
            where p.per_status=1 and p.per_type=2 and p.per_type in (select per_type from List_Per_Type)
              and :LAW_OR_ASS=1 and pp.ORG_ID in (select org_id from GetList_ORG_ID)
            union
            select per_id from PER_PERSONAL p
            LEFT JOIN PER_POS_EMPSER pp on (pp.POEMS_ID=p.POEMS_ID)
            where p.per_status=1 and p.per_type=3 and p.per_type in (select per_type from List_Per_Type)
              and :LAW_OR_ASS=1 and pp.ORG_ID in (select org_id from GetList_ORG_ID)
            union
            select per_id from PER_PERSONAL p
            LEFT JOIN PER_POS_TEMP pp on (pp.POT_ID=p.POT_ID)
            where p.per_status=1 and p.per_type=4 and p.per_type in (select per_type from List_Per_Type)
              and :LAW_OR_ASS=1 and pp.ORG_ID in (select org_id from GetList_ORG_ID)
            /* By Assignment */
            union
            select per_id from PER_PERSONAL p
            where p.per_status=1 and p.per_type in (select per_type from List_Per_Type)
              and :LAW_OR_ASS=2 and p.ORG_ID in (select org_id from GetList_ORG_ID)
          )
          select per_id from PersonInDepart
);


update TA_PER_OT set 
  amount = case when Num_Hrs >= 0 then
                  Num_Hrs * case when Holyday_Flag=0 then 
                        (select to_number(config_value) from SYSTEM_CONFIG where CONFIG_NAME='P_OTRATEWORKDAY')
                     else
                        (select to_number(config_value) from SYSTEM_CONFIG where CONFIG_NAME='P_OTRATEWEEKEND')
                     end
                else NULL end
  ,Num_Hrs = case when Num_Hrs >= 0 then Num_Hrs else null end
where  AUDIT_FLAG <> 1
  and OT_DATE >= substr(:BEGINDATEAT,1,10) and OT_DATE <= substr(:TODATEAT,1,10)
  and PER_ID in (

          with 
          GetList_ORG_ID As
          (
          
            select org_id from PER_ORG o where :LAW_OR_ASS=1 and o.org_active=1 and o.org_id in
              (select org_id from PER_ORG start with ORG_ID=:ORG_ID CONNECT BY NOCYCLE PRIOR org_id = ORG_ID_REF)  
            union
            select org_id from PER_ORG_ASS o where :LAW_OR_ASS=2 and o.org_active=1 and o.org_id in
              (select org_id from PER_ORG_ASS start with ORG_ID=:ORG_ID CONNECT BY NOCYCLE PRIOR org_id = ORG_ID_REF)  
          
          )
          ,List_Per_Type as
          (
            select CAST(:PER_TYPE AS number) PER_TYPE from dual 
            union select 1 from dual where :PER_TYPE=0
            union select 2 from dual where :PER_TYPE=0
            union select 3 from dual where :PER_TYPE=0
            union select 4 from dual where :PER_TYPE=0
            
          )
          ,PersonInDepart As
          (
            /* By Law */
            select per_id from PER_PERSONAL p
            LEFT JOIN PER_POSITION pp on (pp.POS_ID=p.POS_ID)
            where p.per_status=1 and p.per_type=1 and p.per_type in (select per_type from List_Per_Type)
              and :LAW_OR_ASS=1 and pp.ORG_ID in (select org_id from GetList_ORG_ID)
            union
            select per_id from PER_PERSONAL p
            LEFT JOIN PER_POS_EMP pp on (pp.POEM_ID=p.POEM_ID)
            where p.per_status=1 and p.per_type=2 and p.per_type in (select per_type from List_Per_Type)
              and :LAW_OR_ASS=1 and pp.ORG_ID in (select org_id from GetList_ORG_ID)
            union
            select per_id from PER_PERSONAL p
            LEFT JOIN PER_POS_EMPSER pp on (pp.POEMS_ID=p.POEMS_ID)
            where p.per_status=1 and p.per_type=3 and p.per_type in (select per_type from List_Per_Type)
              and :LAW_OR_ASS=1 and pp.ORG_ID in (select org_id from GetList_ORG_ID)
            union
            select per_id from PER_PERSONAL p
            LEFT JOIN PER_POS_TEMP pp on (pp.POT_ID=p.POT_ID)
            where p.per_status=1 and p.per_type=4 and p.per_type in (select per_type from List_Per_Type)
              and :LAW_OR_ASS=1 and pp.ORG_ID in (select org_id from GetList_ORG_ID)
            /* By Assignment */
            union
            select per_id from PER_PERSONAL p
            where p.per_status=1 and p.per_type in (select per_type from List_Per_Type)
              and :LAW_OR_ASS=2 and p.ORG_ID in (select org_id from GetList_ORG_ID)
          )
          select per_id from PersonInDepart
);

commit;
END;
