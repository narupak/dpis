WITH GETLIST_ORG_ID AS
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
                            ),
TBMAIN AS (
SELECT PABSUM.PER_ID,PABSUM.AS_YEAR,
  NVL(SUM(PABSUM.AB_CODE_04),0)+(SELECT SUM(ABS_DAY) FROM PER_ABSENTHIS WHERE PER_ID=PABSUM.PER_ID AND TRIM(AB_CODE)='04' AND ABS_STARTDATE BETWEEN PABSUM.START_DATE AND PABSUM.END_DATE
  ) AS AB_CODE_04,
  NVL(SUM(PABSUM.AB_CODE_01),0)+(SELECT NVL(SUM(ABS_DAY),0) FROM PER_ABSENTHIS WHERE PER_ID=PABSUM.PER_ID AND TRIM(AB_CODE)='01' AND ABS_STARTDATE BETWEEN PABSUM.START_DATE AND PABSUM.END_DATE) AS AB_CODE_01,
  NVL(SUM(PABSUM.AB_CODE_03),0)+(SELECT NVL(SUM(ABS_DAY),0) FROM PER_ABSENTHIS WHERE PER_ID=PABSUM.PER_ID AND TRIM(AB_CODE)='03' AND ABS_STARTDATE BETWEEN PABSUM.START_DATE AND PABSUM.END_DATE) AS AB_CODE_03,
  ((NVL(SUM(PABSUM.AB_CODE_02),0))+
  (NVL(SUM(PABSUM.AB_CODE_05),0))+
  (NVL(SUM(PABSUM.AB_CODE_06),0))+
  (NVL(SUM(PABSUM.AB_CODE_07),0))+
  (NVL(SUM(PABSUM.AB_CODE_08),0))+
  (NVL(SUM(PABSUM.AB_CODE_09),0))+
  (NVL(SUM(PABSUM.AB_CODE_11),0))+
  (NVL(SUM(PABSUM.AB_CODE_12),0))+
  (NVL(SUM(PABSUM.AB_CODE_14),0))+
  (NVL(SUM(PABSUM.AB_CODE_15),0))+
  (SELECT NVL(SUM(ABS_DAY),0) FROM PER_ABSENTHIS WHERE PER_ID=PABSUM.PER_ID AND TRIM(AB_CODE)NOT IN('01','03','04','10','13') AND ABS_STARTDATE BETWEEN PABSUM.START_DATE AND PABSUM.END_DATE)) AS AB_CODE_99,
  NVL(SUM(PABSUM.AB_CODE_10),0)+(SELECT NVL(SUM(ABS_DAY),0) FROM PER_ABSENTHIS WHERE PER_ID=PABSUM.PER_ID AND TRIM(AB_CODE)='10' AND ABS_STARTDATE BETWEEN PABSUM.START_DATE AND PABSUM.END_DATE) AS AB_CODE_10, 
  NULL AS TXTCOMMENT
FROM PER_ABSENTSUM PABSUM 
WHERE PABSUM.AS_YEAR BETWEEN :AS_YEAR1 AND :AS_YEAR2
GROUP BY PABSUM.PER_ID,PABSUM.AS_YEAR,PABSUM.START_DATE , PABSUM.END_DATE
ORDER BY PABSUM.PER_ID,PABSUM.AS_YEAR
)

SELECT PER_ID,AS_YEAR,SUM(AB_CODE_04) AS AB_CODE_04, 
  SUM(AB_CODE_01) AS AB_CODE_01,
  SUM(AB_CODE_03) AS AB_CODE_03,
  SUM(AB_CODE_99) AS AB_CODE_99,
  SUM(AB_CODE_10) AS AB_CODE_10
FROM TBMAIN WHERE PER_ID IN(:PERID_VAL)
GROUP BY PER_ID,AS_YEAR
/*ORDER BY PER_ID,AS_YEAR DESC*/