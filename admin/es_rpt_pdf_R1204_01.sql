WITH 
            GETLIST_ORG_ID AS
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


            ,TBMAIN AS(
              SELECT PER_ID,
              CASE WHEN  ABS_STARTDATE 
              BETWEEN (SUBSTR(ABS_STARTDATE,1,4)-1)||'-10-01' AND SUBSTR(ABS_STARTDATE,1,4)||'-09-30' 
              THEN SUBSTR(ABS_STARTDATE,1,4) ELSE (SUBSTR(ABS_STARTDATE,1,4)+1)||'' END  AS FISCAL_YEAR,
                AB_CODE,ABS_DAY,
                TO_CHAR( TO_DATE(ABS_STARTDATE,'YYYY-MM-DD'),'DD/MM/YYYY','NLS_CALENDAR=''THAI BUDDHA''')||'-'||
                TO_CHAR( TO_DATE(ABS_ENDDATE,'YYYY-MM-DD'),'DD/MM/YYYY','NLS_CALENDAR=''THAI BUDDHA''')||' ('||ABS_DAY||') ' AS COLDETAIL
              FROM PER_ABSENTHIS WHERE PER_ID IN(
                                                  SELECT PER_ID FROM PERSONINDEPART WHERE :PERID_VAL
                                                )
            )
            SELECT PER_ID,FISCAL_YEAR,AB_CODE,SUM(ABS_DAY) AS ABS_DAY,LISTAGG(COLDETAIL, ' ') 
            WITHIN GROUP (ORDER BY FISCAL_YEAR,AB_CODE,COLDETAIL) AS COMMENTDATE
            FROM TBMAIN
            WHERE FISCAL_YEAR BETWEEN :FISCAL_YEAR1 AND :FISCAL_YEAR2
            GROUP BY PER_ID,FISCAL_YEAR,AB_CODE 