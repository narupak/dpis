<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if( $command=='ALTER' ) {
		if($DPISDB=="odbc"){
			$cmd = " ALTER TABLE  PER_PERSONAL DROP HIP_FLAG ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE  PER_ORG_TYPE DROP CONSTRAINT INXU1_PER_ORG_TYPE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_ORG_TYPE ADD  OT_NAME_TEMP VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_ORG_TYPE SET OT_NAME_TEMP =  OT_NAME ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE  PER_ORG_TYPE DROP OT_NAME ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE  PER_ORG_TYPE ADD OT_NAME VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " CREATE UNIQUE INDEX INXU1_PER_ORG_TYPE ON PER_ORG_TYPE (OT_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_ORG_TYPE SET OT_NAME =  OT_NAME_TEMP ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE  PER_ORG_TYPE DROP OT_NAME_TEMP ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " CREATE TABLE PER_EXTRA_INCOME_TYPE (
			EXIN_CODE CHAR(10) NOT NULL,
			EXIN_NAME VARCHAR(100) NOT NULL,
			EXIN_ACTIVE CHAR(1) NOT NULL,
			UPDATE_USER INTEGER2 NOT NULL, 
			UPDATE_DATE CHAR(19) NOT NULL ) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_EXTRA_INCOME_TYPE ADD CONSTRAINT pk_PER_EXTRA_INCOME_TYPE  primary key(EXIN_CODE ) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " CREATE UNIQUE INDEX U1_PER_EXTRA_INCOME_TYPE ON PER_EXTRA_INCOME_TYPE (EXIN_NAME ) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " CREATE TABLE PER_EXTRA_INCOMEHIS ( EXINH_ID INTEGER2 NOT NULL,
			PER_ID INTEGER2 NOT NULL,
			EXINH_EFFECTIVEDATE CHAR(19) NOT NULL,
			EXIN_CODE CHAR(10) NOT NULL,
			EXINH_AMT NUMBER NOT NULL,
			EXINH_ENDDATE  CHAR(19) ,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE CHAR(19) NOT NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ADD CONSTRAINT pk_PER_EXTRA_INCOMEHIS  primary key(EXINH_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " CREATE UNIQUE INDEX U1_PER_EXTRA_INCOMEHIS ON PER_EXTRA_INCOMEHIS (PER_ID,EXIN_CODE,EXINH_EFFECTIVEDATE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS  ADD  CONSTRAINT fk_PER_EXTRA_INCOMEHIS foreign key (EXIN_CODE)  REFERENCES PER_EXTRA_INCOME_TYPE (EXIN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE  PER_ABSENT_CONF ADD  PER_TYPE	 integer Null ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_ABSENT_CONF SET PER_TYPE = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE  PER_ABSENT_CONF alter  PER_TYPE	 integer not Null ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_ABSENT_CONF DROP CONSTRAINT PK_PER_ABSENT_CONF ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_ABSENT_CONF ADD CONSTRAINT PK_PER_ABSENT_CONF PRIMARY KEY (ABS_MONTH,PER_TYPE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " INSERT INTO PER_ABSENT_CONF (abs_month,update_user,update_date,per_type) SELECT abs_month,update_user,update_date,2 from PER_ABSENT_CONF ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE  PER_ABSENT_CONF_B ADD  PER_TYPE	 integer Null ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_ABSENT_CONF_B SET PER_TYPE = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE  PER_ABSENT_CONF_B alter  PER_TYPE	 integer not Null ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " INSERT INTO PER_ABSENT_CONF_B (abs_month,update_user,update_date,per_type) SELECT abs_month,update_user,update_date,2 from PER_ABSENT_CONF_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL ADD crd_code Char(10) Null ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL ADD pen_code Char(2) Null ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL ADD CONSTRAINT FK4_PER_INVEST2DTL FOREIGN KEY (pen_code) REFERENCES PER_PENALTY(pen_code) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_INVEST2DTL, PER_INVEST2 SET PER_INVEST2DTL.CRD_CODE = PER_INVEST2.CRD_CODE WHERE PER_INVEST2.INV_ID = PER_INVEST2DTL.INV_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_INVEST2DTL, PER_INVEST2 SET PER_INVEST2DTL.PEN_CODE = PER_INVEST2.PEN_CODE WHERE PER_INVEST2.INV_ID = PER_INVEST2DTL.INV_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL ALTER crd_code Char(10) Not Null ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL ADD CONSTRAINT FK3_PER_INVEST2DTL FOREIGN KEY (crd_code) REFERENCES PER_CRIME_DTL(crd_code) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2 DROP CONSTRAINT FK2_PER_INVEST2 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2 DROP COLUMN CRD_CODE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2 DROP CONSTRAINT FK3_PER_INVEST2 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2 DROP COLUMN PEN_CODE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL_B ADD crd_code Char(10) Null ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL_B ADD pen_code Char(2) Null ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_INVEST2DTL_B, PER_INVEST2_B SET PER_INVEST2DTL_B.CRD_CODE = PER_INVEST2_B.CRD_CODE WHERE PER_INVEST2_B.INV_ID = PER_INVEST2DTL_B.INV_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_INVEST2DTL_B, PER_INVEST2_B SET PER_INVEST2DTL_B.PEN_CODE = PER_INVEST2_B.PEN_CODE WHERE PER_INVEST2_B.INV_ID = PER_INVEST2DTL_B.INV_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL_B ALTER crd_code Char(10) Not Null ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2_B DROP COLUMN CRD_CODE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2_B DROP COLUMN PEN_CODE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

		}elseif($DPISDB=="oci8"){
			$cmd = " ALTER TABLE  PER_ORG_TYPE  MODIFY OT_NAME VARCHAR2(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " CREATE TABLE PER_EXTRA_INCOME_TYPE (
			EXIN_CODE CHAR(10) NOT NULL,
			EXIN_NAME VARCHAR2(100) NOT NULL,
			EXIN_ACTIVE NUMBER(1) NOT NULL,
			UPDATE_USER NUMBER(5) NOT NULL, 
			UPDATE_DATE CHAR(19) NOT NULL ) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_EXTRA_INCOME_TYPE ADD CONSTRAINT pk_PER_EXTRA_INCOME_TYPE  primary key(EXIN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " CREATE UNIQUE INDEX U1_PER_EXTRA_INCOME_TYPE ON PER_EXTRA_INCOME_TYPE (EXIN_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " CREATE TABLE PER_EXTRA_INCOMEHIS ( EXINH_ID NUMBER(10) NOT NULL,
			PER_ID NUMBER(10) NOT NULL,
			EXINH_EFFECTIVEDATE CHAR(19) NOT NULL,
			EXIN_CODE CHAR(10) NOT NULL,
			EXINH_AMT NUMBER(16,2) NOT NULL,
			EXINH_ENDDATE  CHAR(19) ,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE CHAR(19) NOT NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ADD CONSTRAINT pk_PER_EXTRA_INCOMEHIS  primary key(EXINH_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " CREATE UNIQUE INDEX U1_PER_EXTRA_INCOMEHIS ON PER_EXTRA_INCOMEHIS (PER_ID,EXIN_CODE,EXINH_EFFECTIVEDATE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS  ADD  CONSTRAINT fk1_PER_EXTRA_INCOMEHIS foreign key (PER_ID)  REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS  ADD  CONSTRAINT fk2_PER_EXTRA_INCOMEHIS foreign key (EXIN_CODE)  REFERENCES PER_EXTRA_INCOME_TYPE (EXIN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE  PER_ABSENT_CONF ADD  PER_TYPE	NUMBER(1) Null ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_ABSENT_CONF SET PER_TYPE = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE  PER_ABSENT_CONF MODIFY PER_TYPE Not Null ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_ABSENT_CONF DROP CONSTRAINT PK_PER_ABSENT_CONF ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_ABSENT_CONF ADD CONSTRAINT PK_PER_ABSENT_CONF PRIMARY KEY (ABS_MONTH,PER_TYPE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " INSERT INTO PER_ABSENT_CONF SELECT abs_month,update_user,update_date,2 from PER_ABSENT_CONF ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE  PER_ABSENT_CONF_B ADD  PER_TYPE	NUMBER(1) Null ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_ABSENT_CONF_B SET PER_TYPE = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE  PER_ABSENT_CONF_B MODIFY PER_TYPE Not Null ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " INSERT INTO PER_ABSENT_CONF_B SELECT abs_month,update_user,update_date,2 from PER_ABSENT_CONF_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL ADD crd_code Char(10) Null ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL ADD pen_code Char(2) Null ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL ADD CONSTRAINT FK3_PER_INVEST2DTL FOREIGN KEY (crd_code) REFERENCES PER_CRIME_DTL(crd_code) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL ADD CONSTRAINT FK4_PER_INVEST2DTL FOREIGN KEY (pen_code) REFERENCES PER_PENALTY(pen_code) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_INVEST2DTL SET CRD_CODE = (SELECT CRD_CODE FROM PER_INVEST2 WHERE PER_INVEST2.INV_ID = PER_INVEST2DTL.INV_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_INVEST2DTL SET PEN_CODE = (SELECT PEN_CODE FROM PER_INVEST2 WHERE PER_INVEST2.INV_ID = PER_INVEST2DTL.INV_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL MODIFY crd_code Not Null ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2 DROP COLUMN CRD_CODE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2 DROP COLUMN PEN_CODE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL_B ADD crd_code Char(10) Null ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL_B ADD pen_code Char(2) Null ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_INVEST2DTL_B SET CRD_CODE = (SELECT CRD_CODE FROM PER_INVEST2_B WHERE PER_INVEST2_B.INV_ID = PER_INVEST2DTL_B.INV_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_INVEST2DTL_B SET PEN_CODE = (SELECT PEN_CODE FROM PER_INVEST2_B WHERE PER_INVEST2_B.INV_ID = PER_INVEST2DTL_B.INV_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL_B MODIFY crd_code Not Null ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2_B DROP COLUMN CRD_CODE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2_B DROP COLUMN PEN_CODE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

		} // end if

		if($DPISDB=="odbc") {
			$cmd = " ALTER TABLE PER_ABILITY ADD CONSTRAINT FK2_PER_ABILITY FOREIGN KEY (AL_CODE) REFERENCES 
			PER_ABILITYGRP (AL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ABILITY ADD CONSTRAINT FK1_PER_ABILITY FOREIGN KEY (PER_ID) REFERENCES 
			PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ABSENT ADD CONSTRAINT FK2_PER_ABSENT FOREIGN KEY (AB_CODE) REFERENCES 
			PER_ABSENTTYPE (AB_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ABSENT ADD CONSTRAINT FK1_PER_ABSENT FOREIGN KEY (PER_ID) REFERENCES 
			PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ABSENTHIS ADD CONSTRAINT FK2_PER_ABSENTHIS FOREIGN KEY (AB_CODE) REFERENCES 
			PER_ABSENTTYPE (AB_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ABSENTHIS ADD CONSTRAINT FK1_PER_ABSENTHIS FOREIGN KEY (PER_ID) REFERENCES 
			PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_AMPHUR ADD CONSTRAINT FK1_PER_AMPHUR FOREIGN KEY (PV_CODE) REFERENCES 
			PER_PROVINCE (PV_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ASSIGN ADD CONSTRAINT FK1_PER_ASSIGN FOREIGN KEY (LEVEL_NO_MIN) REFERENCES 
			PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ASSIGN ADD CONSTRAINT FK2_PER_ASSIGN FOREIGN KEY (LEVEL_NO_MAX) REFERENCES 
			PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ASSIGN_DTL ADD CONSTRAINT FK1_PER_ASSIGN_DTL FOREIGN KEY (ASS_ID) REFERENCES 
			PER_ASSIGN (ASS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ASSIGN_DTL ADD CONSTRAINT FK2_PER_ASSIGN_DTL FOREIGN KEY (PL_CODE) REFERENCES 
			PER_LINE(PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ASSIGN_S ADD CONSTRAINT FK1_PER_ASSIGN_S FOREIGN KEY (LEVEL_NO) REFERENCES 
			PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ASSIGN_YEAR ADD CONSTRAINT FK1_PER_ASSIGN_YEAR FOREIGN KEY (EL_CODE) REFERENCES 
			PER_EDUCLEVEL (EL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ASSIGN_YEAR ADD CONSTRAINT FK2_PER_ASSIGN_YEAR FOREIGN KEY (LEVEL_NO) REFERENCES 
			PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_BONUSPROMOTE ADD CONSTRAINT FK1_PER_BONUSPROMOTE FOREIGN KEY (BONUS_YEAR, 
			BONUS_TYPE) REFERENCES PER_BONUSQUOTA (BONUS_YEAR, BONUS_TYPE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_BONUSPROMOTE ADD CONSTRAINT FK2_PER_BONUSPROMOTE FOREIGN KEY (PER_ID) 
			REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_BONUSQUOTADTL1 ADD CONSTRAINT FK1_PER_BONUSQUOTADTL1 FOREIGN KEY (BONUS_YEAR, 
			BONUS_TYPE) REFERENCES PER_BONUSQUOTA (BONUS_YEAR, BONUS_TYPE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_BONUSQUOTADTL1 ADD CONSTRAINT FK2_PER_BONUSQUOTADTL1 FOREIGN KEY (ORG_ID) 
			REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_BONUSQUOTADTL2 ADD CONSTRAINT FK1_PER_BONUSQUOTADTL2 FOREIGN KEY (BONUS_YEAR, 
			BONUS_TYPE) REFERENCES PER_BONUSQUOTA (BONUS_YEAR, BONUS_TYPE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_BONUSQUOTADTL2 ADD CONSTRAINT FK2_PER_BONUSQUOTADTL2 FOREIGN KEY (ORG_ID) 
			REFERENCES PER_ORG_ASS (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_CO_LEVEL ADD CONSTRAINT FK1_PER_CO_LEVEL FOREIGN KEY (LEVEL_NO_MIN) REFERENCES 
			PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_CO_LEVEL ADD CONSTRAINT FK2_PER_CO_LEVEL FOREIGN KEY (LEVEL_NO_MAX) REFERENCES 
			PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK1_PER_COMDTL FOREIGN KEY (COM_ID) REFERENCES 
			PER_COMMAND (COM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK2_PER_COMDTL FOREIGN KEY (PER_ID) REFERENCES 
			PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK3_PER_COMDTL FOREIGN KEY (EN_CODE) REFERENCES 
			PER_EDUCNAME (EN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK4_PER_COMDTL FOREIGN KEY (PL_CODE) REFERENCES 
			PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK5_PER_COMDTL FOREIGN KEY (PN_CODE) REFERENCES 
			PER_POS_NAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK6_PER_COMDTL FOREIGN KEY (POS_ID) REFERENCES 
			PER_POSITION (POS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK7_PER_COMDTL FOREIGN KEY (POEM_ID) REFERENCES 
			PER_POS_EMP (POEM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK8_PER_COMDTL FOREIGN KEY (LEVEL_NO) REFERENCES 
			PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK9_PER_COMDTL FOREIGN KEY (PL_CODE_ASSIGN) REFERENCES 
			PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK10_PER_COMDTL FOREIGN KEY (PN_CODE_ASSIGN) REFERENCES 
			PER_POS_NAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK11_PER_COMDTL FOREIGN KEY (MOV_CODE) REFERENCES 
			PER_MOVMENT (MOV_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COMMAND ADD CONSTRAINT FK1_PER_COMMAND FOREIGN KEY (COM_TYPE) REFERENCES 
			PER_COMTYPE (COM_TYPE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_CONTROL ADD CONSTRAINT FK1_PER_CONTROL FOREIGN KEY (ORG_ID) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COURSE ADD CONSTRAINT FK1_PER_COURSE FOREIGN KEY (TR_CODE) REFERENCES 
			PER_TRAIN (TR_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COURSE ADD CONSTRAINT FK2_PER_COURSE FOREIGN KEY (CT_CODE) REFERENCES 
			PER_COUNTRY (CT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COURSE ADD CONSTRAINT FK3_PER_COURSE FOREIGN KEY (CT_CODE_FUND) REFERENCES 
			PER_COUNTRY (CT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COURSEDTL ADD CONSTRAINT FK1_PER_COURSEDTL FOREIGN KEY (CO_ID) REFERENCES 
			PER_COURSE (CO_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COURSEDTL ADD CONSTRAINT FK2_PER_COURSEDTL FOREIGN KEY (PER_ID) REFERENCES 
			PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_CRIME_DTL ADD CONSTRAINT FK1_PER_CRIME_DTL FOREIGN KEY (CR_CODE) REFERENCES 
			PER_CRIME (CR_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_DECORATEHIS ADD CONSTRAINT FK1_PER_DECORATEHIS FOREIGN KEY (PER_ID) REFERENCES 
			PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_DECORATEHIS ADD CONSTRAINT FK2_PER_DECORATEHIS FOREIGN KEY (DC_CODE) REFERENCES 
			PER_DECORATION (DC_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_DECORCOND ADD CONSTRAINT FK1_PER_DECORCOND FOREIGN KEY (LEVEL_NO) REFERENCES 
			PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_DECORCOND ADD CONSTRAINT FK2_PER_DECORCOND FOREIGN KEY (DC_CODE) REFERENCES 
			PER_DECORATION (DC_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_DECORCOND ADD CONSTRAINT FK3_PER_DECORCOND FOREIGN KEY (DC_CODE_OLD) 
			REFERENCES PER_DECORATION (DC_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_DECORDTL ADD CONSTRAINT FK1_PER_DECORDTL FOREIGN KEY (DE_ID) REFERENCES 
			PER_DECOR (DE_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_DECORDTL ADD CONSTRAINT FK2_PER_DECORDTL FOREIGN KEY (DC_CODE) REFERENCES 
			PER_DECORATION (DC_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_DECORDTL ADD CONSTRAINT FK3_PER_DECORDTL FOREIGN KEY (PER_ID) REFERENCES 
			PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_EDUCATE ADD CONSTRAINT FK1_PER_EDUCATE FOREIGN KEY (PER_ID) REFERENCES 
			PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_EDUCATE ADD CONSTRAINT FK2_PER_EDUCATE FOREIGN KEY (ST_CODE) REFERENCES 
			PER_SCHOLARTYPE (ST_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_EDUCATE ADD CONSTRAINT FK3_PER_EDUCATE FOREIGN KEY (CT_CODE) REFERENCES 
			PER_COUNTRY (CT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_EDUCATE ADD CONSTRAINT FK4_PER_EDUCATE FOREIGN KEY (EN_CODE) REFERENCES 
			PER_EDUCNAME (EN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_EDUCATE ADD CONSTRAINT FK5_PER_EDUCATE FOREIGN KEY (EM_CODE) REFERENCES 
			PER_EDUCMAJOR (EM_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_EDUCATE ADD CONSTRAINT FK6_PER_EDUCATE FOREIGN KEY (INS_CODE) REFERENCES 
			PER_INSTITUTE (INS_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_EDUCNAME ADD CONSTRAINT FK1_PER_EDUCNAME FOREIGN KEY (EL_CODE) REFERENCES 
			PER_EDUCLEVEL (EL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ADD CONSTRAINT FK1_PER_EXTRA_INCOMEHIS FOREIGN KEY (EXIN_CODE) 
			REFERENCES PER_EXTRA_INCOME_TYPE (EXIN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_EXTRAHIS ADD CONSTRAINT FK1_PER_EXTRAHIS FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_EXTRAHIS ADD CONSTRAINT FK2_PER_EXTRAHIS FOREIGN KEY (EX_CODE) REFERENCES PER_EXTRATYPE (EX_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_HEIR ADD CONSTRAINT FK1_PER_HEIR FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_HEIR ADD CONSTRAINT FK2_PER_HEIR FOREIGN KEY (HR_CODE) REFERENCES PER_HEIRTYPE 
			(HR_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_INSTITUTE ADD CONSTRAINT FK1_PER_INSTITUTE FOREIGN KEY (CT_CODE) REFERENCES 
			PER_COUNTRY (CT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_INVEST1 ADD CONSTRAINT FK1_PER_INVEST1 FOREIGN KEY (CRD_CODE) REFERENCES 
			PER_CRIME_DTL (CRD_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_INVEST1DTL ADD CONSTRAINT FK1_PER_INVEST1DTL FOREIGN KEY (INV_ID) REFERENCES 
			PER_INVEST1 (INV_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_INVEST1DTL ADD CONSTRAINT FK2_PER_INVEST1DTL FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_INVEST2 ADD CONSTRAINT FK1_PER_INVEST2 FOREIGN KEY (INV_ID_REF) REFERENCES 
			PER_INVEST1 (INV_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_INVEST2DTL ADD CONSTRAINT FK1_PER_INVEST2DTL FOREIGN KEY (INV_ID) REFERENCES 
			PER_INVEST2 (INV_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_INVEST2DTL ADD CONSTRAINT FK2_PER_INVEST2DTL FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_INVEST2DTL ADD CONSTRAINT FK3_PER_INVEST2DTL FOREIGN KEY (CRD_CODE) REFERENCES 
			PER_CRIME_DTL (CRD_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_INVEST2DTL ADD CONSTRAINT FK4_PER_INVEST2DTL FOREIGN KEY (PEN_CODE) REFERENCES PER_PENALTY (PEN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_LAYER ADD CONSTRAINT FK1_PER_LAYER FOREIGN KEY (LEVEL_NO) REFERENCES 
			PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_LAYEREMP ADD CONSTRAINT FK1_PER_LAYEREMP FOREIGN KEY (PG_CODE) REFERENCES 
			PER_POS_GROUP (PG_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_LETTER ADD CONSTRAINT FK1_PER_LETTER FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL
			(PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_LETTER ADD CONSTRAINT FK2_PER_LETTER FOREIGN KEY (PER_ID_SIGN1) REFERENCES 
			PER_PERSONAL  (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_MARRHIS ADD CONSTRAINT FK1_PER_MARRHIS FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_MARRHIS ADD CONSTRAINT FK2_PER_MARRHIS FOREIGN KEY (DV_CODE) REFERENCES 
			PER_DIVORCE (DV_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_MGT ADD CONSTRAINT FK1_PER_MGT FOREIGN KEY (PS_CODE) REFERENCES 
			PER_STATUS (PS_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_MGTSALARY ADD CONSTRAINT FK1_PER_MGTSALARY FOREIGN KEY (PT_CODE) REFERENCES 
			PER_TYPE (PT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_MGTSALARY ADD CONSTRAINT FK2_PER_MGTSALARY FOREIGN KEY (LEVEL_NO) REFERENCES 
			PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK1_PER_MOVE_REQ FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK2_PER_MOVE_REQ FOREIGN KEY (PL_CODE_1) REFERENCES PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK3_PER_MOVE_REQ FOREIGN KEY (PN_CODE_1) REFERENCES PER_POS_NAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK4_PER_MOVE_REQ FOREIGN KEY (ORG_ID_1) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK5_PER_MOVE_REQ FOREIGN KEY (PL_CODE_2) REFERENCES PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK6_PER_MOVE_REQ FOREIGN KEY (PN_CODE_2) REFERENCES PER_POS_NAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK7_PER_MOVE_REQ FOREIGN KEY (ORG_ID_2) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK8_PER_MOVE_REQ FOREIGN KEY (PL_CODE_3) REFERENCES PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK9_PER_MOVE_REQ FOREIGN KEY (PN_CODE_3) REFERENCES PER_POS_NAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK10_PER_MOVE_REQ FOREIGN KEY (ORG_ID_3) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_NAMEHIS ADD CONSTRAINT FK1_PER_NAMEHIS FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_NAMEHIS ADD CONSTRAINT FK2_PER_NAMEHIS FOREIGN KEY (PN_CODE) REFERENCES PER_PRENAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK1_PER_ORDER_DTL FOREIGN KEY (ORD_ID) REFERENCES PER_ORDER (ORD_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK2_PER_ORDER_DTL FOREIGN KEY (POS_ID_OLD) REFERENCES PER_POSITION (POS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK3_PER_ORDER_DTL FOREIGN KEY (ORG_ID) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK4_PER_ORDER_DTL FOREIGN KEY (OT_CODE) REFERENCES 
			PER_OFF_TYPE (OT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK5_PER_ORDER_DTL FOREIGN KEY (ORG_ID_1) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK6_PER_ORDER_DTL FOREIGN KEY (ORG_ID_2) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK7_PER_ORDER_DTL FOREIGN KEY (PM_CODE) REFERENCES 
			PER_MGT (PM_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK8_PER_ORDER_DTL FOREIGN KEY (PL_CODE) REFERENCES 
			PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK9_PER_ORDER_DTL FOREIGN KEY (CL_NAME) REFERENCES 
			PER_CO_LEVEL (CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK10_PER_ORDER_DTL FOREIGN KEY (SKILL_CODE) REFERENCES 
			PER_SKILL (SKILL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK11_PER_ORDER_DTL FOREIGN KEY (PT_CODE) REFERENCES 
			PER_TYPE (PT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK12_PER_ORDER_DTL FOREIGN KEY (PC_CODE) REFERENCES 
			PER_CONDITION (PC_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG ADD CONSTRAINT FK1_PER_ORG FOREIGN KEY (OL_CODE) REFERENCES 
			PER_ORG_LEVEL (OL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG ADD CONSTRAINT FK2_PER_ORG FOREIGN KEY (OT_CODE) REFERENCES 
			PER_ORG_TYPE (OT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG ADD CONSTRAINT FK3_PER_ORG FOREIGN KEY (OP_CODE) REFERENCES 
			PER_ORG_PROVINCE (OP_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG ADD CONSTRAINT FK4_PER_ORG FOREIGN KEY (OS_CODE) REFERENCES 
			PER_ORG_STAT (OS_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG ADD CONSTRAINT FK5_PER_ORG FOREIGN KEY (AP_CODE) REFERENCES 
			PER_AMPHUR (AP_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG ADD CONSTRAINT FK6_PER_ORG FOREIGN KEY (PV_CODE) REFERENCES 
			PER_PROVINCE (PV_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG ADD CONSTRAINT FK7_PER_ORG FOREIGN KEY (CT_CODE) REFERENCES 
			PER_COUNTRY (CT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG ADD CONSTRAINT FK8_PER_ORG FOREIGN KEY (ORG_ID_REF) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG_ASS ADD CONSTRAINT FK1_PER_ORG_ASS FOREIGN KEY (OL_CODE) REFERENCES 
			PER_ORG_LEVEL (OL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG_ASS ADD CONSTRAINT FK2_PER_ORG_ASS FOREIGN KEY (OT_CODE) REFERENCES 
			PER_ORG_TYPE (OT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG_ASS ADD CONSTRAINT FK3_PER_ORG_ASS FOREIGN KEY (OP_CODE) REFERENCES 
			PER_ORG_PROVINCE (OP_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG_ASS ADD CONSTRAINT FK4_PER_ORG_ASS FOREIGN KEY (OS_CODE) REFERENCES 
			PER_ORG_STAT (OS_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG_ASS ADD CONSTRAINT FK5_PER_ORG_ASS FOREIGN KEY (AP_CODE) REFERENCES 
			PER_AMPHUR (AP_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG_ASS ADD CONSTRAINT FK6_PER_ORG_ASS FOREIGN KEY (PV_CODE) REFERENCES 
			PER_PROVINCE (PV_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG_ASS ADD CONSTRAINT FK7_PER_ORG_ASS FOREIGN KEY (CT_CODE) REFERENCES 
			PER_COUNTRY (CT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG_ASS ADD CONSTRAINT FK8_PER_ORG_ASS FOREIGN KEY (ORG_ID_REF) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG_JOB ADD CONSTRAINT FK1_PER_ORG_JOB FOREIGN KEY (PER_ID) REFERENCES 
			PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG_JOB ADD CONSTRAINT FK2_PER_ORG_JOB FOREIGN KEY (ORG_ID) REFERENCES 
			PER_ORG_ASS (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK1_PER_PERSONAL FOREIGN KEY (OT_CODE) REFERENCES 
			PER_OFF_TYPE (OT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK2_PER_PERSONAL FOREIGN KEY (PN_CODE) REFERENCES 
			PER_PRENAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK3_PER_PERSONAL FOREIGN KEY (ORG_ID) REFERENCES 
			PER_ORG_ASS (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK4_PER_PERSONAL FOREIGN KEY (POS_ID) REFERENCES 
			PER_POSITION (POS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK5_PER_PERSONAL FOREIGN KEY (POEM_ID) REFERENCES 
			PER_POS_EMP (POEM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK6_PER_PERSONAL FOREIGN KEY (LEVEL_NO) REFERENCES 
			PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK7_PER_PERSONAL FOREIGN KEY (MR_CODE) REFERENCES 
			PER_MARRIED (MR_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK8_PER_PERSONAL FOREIGN KEY (RE_CODE) REFERENCES 
			PER_RELIGION (RE_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK9_PER_PERSONAL FOREIGN KEY (PN_CODE_F) REFERENCES 
			PER_PRENAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK10_PER_PERSONAL FOREIGN KEY (PN_CODE_M) REFERENCES 
			PER_PRENAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK11_PER_PERSONAL FOREIGN KEY (PV_CODE) REFERENCES 
			PER_PROVINCE (PV_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK12_PER_PERSONAL FOREIGN KEY (MOV_CODE) REFERENCES 
			PER_MOVMENT (MOV_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_EMP ADD CONSTRAINT FK1_PER_POS_EMP FOREIGN KEY (ORG_ID) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_EMP ADD CONSTRAINT FK2_PER_POS_EMP FOREIGN KEY (ORG_ID_1) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_EMP ADD CONSTRAINT FK3_PER_POS_EMP FOREIGN KEY (ORG_ID_2) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_EMP ADD CONSTRAINT FK4_PER_POS_EMP FOREIGN KEY (PN_CODE) REFERENCES 
			PER_POS_NAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK1_PER_POS_MOVE FOREIGN KEY (POS_ID) REFERENCES 
			PER_POSITION (POS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK2_PER_POS_MOVE FOREIGN KEY (ORG_ID) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK3_PER_POS_MOVE FOREIGN KEY (OT_CODE) REFERENCES 
			PER_OFF_TYPE (OT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK4_PER_POS_MOVE FOREIGN KEY (ORG_ID_1) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK5_PER_POS_MOVE FOREIGN KEY (ORG_ID_2) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK6_PER_POS_MOVE FOREIGN KEY (PM_CODE) REFERENCES 
			PER_MGT (PM_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK7_PER_POS_MOVE FOREIGN KEY (PL_CODE) REFERENCES 
			PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK8_PER_POS_MOVE FOREIGN KEY (CL_NAME) REFERENCES 
			PER_CO_LEVEL (CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK9_PER_POS_MOVE FOREIGN KEY (SKILL_CODE) REFERENCES 
			PER_SKILL (SKILL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK10_PER_POS_MOVE FOREIGN KEY (PT_CODE) REFERENCES 
			PER_TYPE (PT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK11_PER_POS_MOVE FOREIGN KEY (PC_CODE) REFERENCES 
			PER_CONDITION (PC_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_NAME ADD CONSTRAINT FK1_PER_POS_NAME FOREIGN KEY (PG_CODE) REFERENCES 
			PER_POS_GROUP (PG_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK1_PER_POSITION FOREIGN KEY (ORG_ID) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK2_PER_POSITION FOREIGN KEY (OT_CODE) REFERENCES 
			PER_OFF_TYPE (OT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK3_PER_POSITION FOREIGN KEY (ORG_ID_1) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK4_PER_POSITION FOREIGN KEY (ORG_ID_2) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK5_PER_POSITION FOREIGN KEY (PM_CODE) REFERENCES 
			PER_MGT (PM_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK6_PER_POSITION FOREIGN KEY (PL_CODE) REFERENCES 
			PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK7_PER_POSITION FOREIGN KEY (CL_NAME) REFERENCES 
			PER_CO_LEVEL (CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK8_PER_POSITION FOREIGN KEY (SKILL_CODE) REFERENCES 
			PER_SKILL (SKILL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK9_PER_POSITION FOREIGN KEY (PT_CODE) REFERENCES 
			PER_TYPE (PT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK10_PER_POSITION FOREIGN KEY (PC_CODE) REFERENCES 
			PER_CONDITION (PC_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK1_PER_POSITIONHIS FOREIGN KEY (PER_ID) REFERENCES 
			PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK2_PER_POSITIONHIS FOREIGN KEY (MOV_CODE) REFERENCES 
			PER_MOVMENT (MOV_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK3_PER_POSITIONHIS FOREIGN KEY (PM_CODE) REFERENCES 
			PER_MGT (PM_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK4_PER_POSITIONHIS FOREIGN KEY (LEVEL_NO) REFERENCES 
			PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK5_PER_POSITIONHIS FOREIGN KEY (PL_CODE) REFERENCES 
			PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK6_PER_POSITIONHIS FOREIGN KEY (PN_CODE) REFERENCES 
			PER_POS_NAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK7_PER_POSITIONHIS FOREIGN KEY (PT_CODE) REFERENCES 
			PER_TYPE (PT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK8_PER_POSITIONHIS FOREIGN KEY (CT_CODE) REFERENCES 
			PER_COUNTRY (CT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK9_PER_POSITIONHIS FOREIGN KEY (PV_CODE) REFERENCES 
			PER_PROVINCE (PV_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK10_PER_POSITIONHIS FOREIGN KEY (AP_CODE) REFERENCES 
			PER_AMPHUR (AP_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK11_PER_POSITIONHIS FOREIGN KEY (ORG_ID_1) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK12_PER_POSITIONHIS FOREIGN KEY (ORG_ID_2) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK13_PER_POSITIONHIS FOREIGN KEY (ORG_ID_3) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_PROMOTE_C ADD CONSTRAINT FK1_PER_PROMOTE_C FOREIGN KEY (PER_ID) REFERENCES 
			PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_PROMOTE_E ADD CONSTRAINT FK1_PER_PROMOTE_E FOREIGN KEY (POEM_ID) REFERENCES 
			PER_POS_EMP (POEM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_PROMOTE_E ADD CONSTRAINT FK2_PER_PROMOTE_E FOREIGN KEY (PER_ID) REFERENCES 
			PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_PROMOTE_P ADD CONSTRAINT FK1_PER_PROMOTE_P FOREIGN KEY (POS_ID) REFERENCES 
			PER_POSITION (POS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_PROMOTE_P ADD CONSTRAINT FK2_PER_PROMOTE_P FOREIGN KEY (PER_ID) REFERENCES 
			PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_PROVINCE ADD CONSTRAINT FK1_PER_PROVINCE FOREIGN KEY (CT_CODE) REFERENCES 
			PER_COUNTRY (CT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PUNISHMENT ADD CONSTRAINT FK1_PER_PUNISHMENT FOREIGN KEY (PER_ID) REFERENCES 
			PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_PUNISHMENT ADD CONSTRAINT FK2_PER_PUNISHMENT FOREIGN KEY (CRD_CODE) REFERENCES 
			PER_CRIME_DTL (CRD_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_PUNISHMENT ADD CONSTRAINT FK3_PER_PUNISHMENT FOREIGN KEY (PEN_CODE) REFERENCES 
			PER_PENALTY (PEN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK1_PER_REQ1_DTL1 FOREIGN KEY (REQ_ID) REFERENCES 
			PER_REQ1 (REQ_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK2_PER_REQ1_DTL1 FOREIGN KEY (ORG_ID) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK3_PER_REQ1_DTL1 FOREIGN KEY (OT_CODE) REFERENCES 
			PER_OFF_TYPE (OT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK4_PER_REQ1_DTL1 FOREIGN KEY (ORG_ID_1) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK5_PER_REQ1_DTL1 FOREIGN KEY (ORG_ID_2) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK6_PER_REQ1_DTL1 FOREIGN KEY (PM_CODE) REFERENCES 
			PER_MGT (PM_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK7_PER_REQ1_DTL1 FOREIGN KEY (PL_CODE) REFERENCES 
			PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK8_PER_REQ1_DTL1 FOREIGN KEY (CL_NAME) REFERENCES 
			PER_CO_LEVEL (CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK9_PER_REQ1_DTL1 FOREIGN KEY (SKILL_CODE) REFERENCES 
			PER_SKILL (SKILL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK10_PER_REQ1_DTL1 FOREIGN KEY (PT_CODE) REFERENCES 
			PER_TYPE (PT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK11_PER_REQ1_DTL1 FOREIGN KEY (PC_CODE) REFERENCES 
			PER_CONDITION (PC_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL2 ADD CONSTRAINT FK1_PER_REQ1_DTL2 FOREIGN KEY (REQ_ID, REQ_SEQ) 
			REFERENCES PER_REQ1_DTL1 (REQ_ID, REQ_SEQ) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL2 ADD CONSTRAINT FK2_PER_REQ1_DTL2 FOREIGN KEY (POS_ID) REFERENCES 
			PER_POSITION (POS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK1_PER_REQ2_DTL FOREIGN KEY (REQ_ID) REFERENCES 
			PER_REQ2 (REQ_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK2_PER_REQ2_DTL FOREIGN KEY (POS_ID_RETIRE) REFERENCES 
			PER_POSITION (POS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK3_PER_REQ2_DTL FOREIGN KEY (POS_ID_DROP) REFERENCES 
			PER_POSITION (POS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK4_PER_REQ2_DTL FOREIGN KEY (POS_ID_REQ) REFERENCES 
			PER_POSITION (POS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK5_PER_REQ2_DTL FOREIGN KEY (ORG_ID) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK6_PER_REQ2_DTL FOREIGN KEY (OT_CODE) REFERENCES 
			PER_OFF_TYPE (OT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK7_PER_REQ2_DTL FOREIGN KEY (ORG_ID_1) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK8_PER_REQ2_DTL FOREIGN KEY (ORG_ID_2) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK9_PER_REQ2_DTL FOREIGN KEY (PM_CODE) REFERENCES 
			PER_MGT (PM_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK10_PER_REQ2_DTL FOREIGN KEY (PL_CODE) REFERENCES 
			PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK11_PER_REQ2_DTL FOREIGN KEY (CL_NAME) REFERENCES 
			PER_CO_LEVEL (CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK12_PER_REQ2_DTL FOREIGN KEY (SKILL_CODE) REFERENCES 
			PER_SKILL (SKILL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK13_PER_REQ2_DTL FOREIGN KEY (PT_CODE) REFERENCES 
			PER_TYPE (PT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK14_PER_REQ2_DTL FOREIGN KEY (PC_CODE) REFERENCES 
			PER_CONDITION (PC_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK1_PER_REQ3_DTL FOREIGN KEY (REQ_ID) REFERENCES 
			PER_REQ3 (REQ_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK2_PER_REQ3_DTL FOREIGN KEY (POS_ID) REFERENCES 
			PER_POSITION (POS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK3_PER_REQ3_DTL FOREIGN KEY (ORG_ID) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK4_PER_REQ3_DTL FOREIGN KEY (OT_CODE) REFERENCES 
			PER_OFF_TYPE (OT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK5_PER_REQ3_DTL FOREIGN KEY (ORG_ID_1) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK6_PER_REQ3_DTL FOREIGN KEY (ORG_ID_2) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK7_PER_REQ3_DTL FOREIGN KEY (PM_CODE) REFERENCES 
			PER_MGT (PM_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK8_PER_REQ3_DTL FOREIGN KEY (PL_CODE) REFERENCES 
			PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK9_PER_REQ3_DTL FOREIGN KEY (CL_NAME) REFERENCES 
			PER_CO_LEVEL (CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK10_PER_REQ3_DTL FOREIGN KEY (SKILL_CODE) REFERENCES 
			PER_SKILL (SKILL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK11_PER_REQ3_DTL FOREIGN KEY (PT_CODE) REFERENCES 
			PER_TYPE (PT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK12_PER_REQ3_DTL FOREIGN KEY (PC_CODE) REFERENCES 
			PER_CONDITION (PC_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REWARDHIS ADD CONSTRAINT FK1_PER_REWARDHIS FOREIGN KEY (PER_ID) REFERENCES 
			PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REWARDHIS ADD CONSTRAINT FK2_PER_REWARDHIS FOREIGN KEY (REW_CODE) REFERENCES 
			PER_REWARD (REW_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALARYHIS ADD CONSTRAINT FK1_PER_SALARYHIS FOREIGN KEY (PER_ID) REFERENCES 
			PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALARYHIS ADD CONSTRAINT FK2_PER_SALARYHIS FOREIGN KEY (MOV_CODE) REFERENCES 
			PER_MOVMENT (MOV_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALPROMOTE ADD CONSTRAINT FK1_PER_SALPROMOTE FOREIGN KEY (SALQ_YEAR, SALQ_TYPE) 
			REFERENCES PER_SALQUOTA (SALQ_YEAR, SALQ_TYPE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SALPROMOTE ADD CONSTRAINT FK2_PER_SALPROMOTE FOREIGN KEY (PER_ID) REFERENCES 
			PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SALQUOTADTL1 ADD CONSTRAINT FK1_PER_SALQUOTADTL1 FOREIGN KEY (SALQ_YEAR, 
			SALQ_TYPE) REFERENCES PER_SALQUOTA (SALQ_YEAR, SALQ_TYPE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SALQUOTADTL1 ADD CONSTRAINT FK2_PER_SALQUOTADTL1 FOREIGN KEY (ORG_ID) REFERENCES
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SALQUOTADTL2 ADD CONSTRAINT FK1_PER_SALQUOTADTL2 FOREIGN KEY (SALQ_YEAR, 
			SALQ_TYPE) REFERENCES PER_SALQUOTA (SALQ_YEAR, SALQ_TYPE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SALQUOTADTL2 ADD CONSTRAINT FK2_PER_SALQUOTADTL2 FOREIGN KEY (ORG_ID) REFERENCES 
			PER_ORG_ASS (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SCHOLAR ADD CONSTRAINT FK1_PER_SCHOLAR FOREIGN KEY (PER_ID) REFERENCES 
			PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SCHOLAR ADD CONSTRAINT FK2_PER_SCHOLAR FOREIGN KEY (PN_CODE) REFERENCES 
			PER_PRENAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SCHOLAR ADD CONSTRAINT FK3_PER_SCHOLAR FOREIGN KEY (EN_CODE) REFERENCES 
			PER_EDUCNAME (EN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SCHOLAR ADD CONSTRAINT FK4_PER_SCHOLAR FOREIGN KEY (EM_CODE) REFERENCES 
			PER_EDUCMAJOR (EM_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SCHOLAR ADD CONSTRAINT FK5_PER_SCHOLAR FOREIGN KEY (SCH_CODE) REFERENCES 
			PER_SCHOLARSHIP (SCH_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SCHOLAR ADD CONSTRAINT FK6_PER_SCHOLAR FOREIGN KEY (INS_CODE) REFERENCES 
			PER_INSTITUTE (INS_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SCHOLAR ADD CONSTRAINT FK7_PER_SCHOLAR FOREIGN KEY (EL_CODE) REFERENCES 
			PER_EDUCLEVEL (EL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SCHOLARINC ADD CONSTRAINT FK1_PER_SCHOLARINC FOREIGN KEY (SC_ID) REFERENCES 
			PER_SCHOLAR (SC_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD CONSTRAINT FK1_PER_SCHOLARSHIP FOREIGN KEY (ST_CODE) REFERENCES 
			PER_SCHOLARTYPE (ST_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SERVICEHIS ADD CONSTRAINT FK1_PER_SERVICEHIS FOREIGN KEY (PER_ID) REFERENCES 
			PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SERVICEHIS ADD CONSTRAINT FK2_PER_SERVICEHIS FOREIGN KEY (SV_CODE) REFERENCES 
			PER_SERVICE (SV_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SERVICEHIS ADD CONSTRAINT FK3_PER_SERVICEHIS FOREIGN KEY (SRT_CODE) REFERENCES 
			PER_SERVICETITLE (SRT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SERVICEHIS ADD CONSTRAINT FK4_PER_SERVICEHIS FOREIGN KEY (ORG_ID) REFERENCES 
			PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SERVICEHIS ADD CONSTRAINT FK5_PER_SERVICEHIS FOREIGN KEY (PER_ID_ASSIGN) 
			REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SERVICEHIS ADD CONSTRAINT FK6_PER_SERVICEHIS FOREIGN KEY (ORG_ID_ASSIGN) 
			REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SKILL ADD CONSTRAINT FK1_PER_SKILL FOREIGN KEY (SG_CODE) REFERENCES PER_SKILL_GROUP (SG_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SKILL_GROUP ADD CONSTRAINT FK1_PER_SKILL_GROUP FOREIGN KEY (PL_CODE) REFERENCES 
			PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SUM ADD CONSTRAINT FK1_PER_SUM FOREIGN KEY (ORG_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL1 ADD CONSTRAINT FK1_PER_SUM_DTL1 FOREIGN KEY (SUM_ID) REFERENCES PER_SUM
			(SUM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL1 ADD CONSTRAINT FK2_PER_SUM_DTL1 FOREIGN KEY (OS_CODE) REFERENCES 
			PER_ORG_STAT (OS_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL1 ADD CONSTRAINT FK3_PER_SUM_DTL1 FOREIGN KEY (OT_CODE) REFERENCES 
			PER_ORG_TYPE (OT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL2 ADD CONSTRAINT FK1_PER_SUM_DTL2 FOREIGN KEY (SUM_ID) REFERENCES PER_SUM
			(SUM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL2 ADD CONSTRAINT FK2_PER_SUM_DTL2 FOREIGN KEY (PL_CODE) REFERENCES 
			PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL2 ADD CONSTRAINT FK3_PER_SUM_DTL2 FOREIGN KEY (CL_NAME) REFERENCES 
			PER_CO_LEVEL (CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL2 ADD CONSTRAINT FK4_PER_SUM_DTL2 FOREIGN KEY (PT_CODE) REFERENCES 
			PER_TYPE (PT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL3 ADD CONSTRAINT FK1_PER_SUM_DTL3 FOREIGN KEY (SUM_ID) REFERENCES PER_SUM
			(SUM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL3 ADD CONSTRAINT FK2_PER_SUM_DTL3 FOREIGN KEY (PL_CODE) REFERENCES 
			PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL4 ADD CONSTRAINT FK1_PER_SUM_DTL4 FOREIGN KEY (SUM_ID) REFERENCES PER_SUM
			(SUM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL4 ADD CONSTRAINT FK2_PER_SUM_DTL4 FOREIGN KEY (PS_CODE) REFERENCES 
			PER_STATUS (PS_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL4 ADD CONSTRAINT FK3_PER_SUM_DTL4 FOREIGN KEY (PM_CODE) REFERENCES 
			PER_MGT (PM_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL4 ADD CONSTRAINT FK4_PER_SUM_DTL4 FOREIGN KEY (LEVEL_NO) REFERENCES 
			PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL5 ADD CONSTRAINT FK1_PER_SUM_DTL5 FOREIGN KEY (SUM_ID) REFERENCES PER_SUM
			(SUM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL5 ADD CONSTRAINT FK2_PER_SUM_DTL5 FOREIGN KEY (OP_CODE) REFERENCES 
			PER_ORG_PROVINCE (OP_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL5 ADD CONSTRAINT FK3_PER_SUM_DTL5 FOREIGN KEY (PM_CODE) REFERENCES 
			PER_MGT (PM_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL5 ADD CONSTRAINT FK4_PER_SUM_DTL5 FOREIGN KEY (LEVEL_NO) REFERENCES 
			PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL6 ADD CONSTRAINT FK1_PER_SUM_DTL6 FOREIGN KEY (SUM_ID) REFERENCES PER_SUM
			(SUM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL6 ADD CONSTRAINT FK2_PER_SUM_DTL6 FOREIGN KEY (OT_CODE) REFERENCES 
			PER_ORG_TYPE (OT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL7 ADD CONSTRAINT FK1_PER_SUM_DTL7 FOREIGN KEY (SUM_ID) REFERENCES PER_SUM
			(SUM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL7 ADD CONSTRAINT FK2_PER_SUM_DTL7 FOREIGN KEY (LEVEL_NO) REFERENCES 
			PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL8 ADD CONSTRAINT FK1_PER_SUM_DTL8 FOREIGN KEY (SUM_ID) REFERENCES PER_SUM
			(SUM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL8 ADD CONSTRAINT FK2_PER_SUM_DTL8 FOREIGN KEY (PL_CODE) REFERENCES 
			PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL8 ADD CONSTRAINT FK3_PER_SUM_DTL8 FOREIGN KEY (LEVEL_NO) REFERENCES 
			PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL9 ADD CONSTRAINT FK1_PER_SUM_DTL9 FOREIGN KEY (SUM_ID) REFERENCES PER_SUM
			(SUM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL9 ADD CONSTRAINT FK2_PER_SUM_DTL9 FOREIGN KEY (PL_CODE) REFERENCES 
			PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL9 ADD CONSTRAINT FK3_PER_SUM_DTL9 FOREIGN KEY (EL_CODE) REFERENCES 
			PER_EDUCLEVEL (EL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_TIMEHIS ADD CONSTRAINT FK1_PER_TIMEHIS FOREIGN KEY (PER_ID) REFERENCES 
			PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TIMEHIS ADD CONSTRAINT FK2_PER_TIMEHIS FOREIGN KEY (TIME_CODE) REFERENCES 
			PER_TIME (TIME_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRAINING ADD CONSTRAINT FK1_PER_TRAINING FOREIGN KEY (PER_ID) REFERENCES 
			PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRAINING ADD CONSTRAINT FK2_PER_TRAINING FOREIGN KEY (TR_CODE) REFERENCES 
			PER_TRAIN (TR_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRAINING ADD CONSTRAINT FK3_PER_TRAINING FOREIGN KEY (CT_CODE) REFERENCES 
			PER_COUNTRY (CT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRAINING ADD CONSTRAINT FK4_PER_TRAINING FOREIGN KEY (CT_CODE_FUND) REFERENCES 
			PER_COUNTRY (CT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK1_PER_TRANSFER_REQ FOREIGN KEY (PN_CODE) 
			REFERENCES PER_PRENAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK2_PER_TRANSFER_REQ FOREIGN KEY (EN_CODE) 
			REFERENCES PER_EDUCNAME (EN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK3_PER_TRANSFER_REQ FOREIGN KEY (EM_CODE) 
			REFERENCES PER_EDUCMAJOR (EM_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK4_PER_TRANSFER_REQ FOREIGN KEY (INS_CODE) 
			REFERENCES PER_INSTITUTE (INS_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK5_PER_TRANSFER_REQ FOREIGN KEY (PL_CODE_1) 
			REFERENCES PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK6_PER_TRANSFER_REQ FOREIGN KEY (PN_CODE_1) 
			REFERENCES PER_POS_NAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK7_PER_TRANSFER_REQ FOREIGN KEY (ORG_ID_1) 
			REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK8_PER_TRANSFER_REQ FOREIGN KEY (LEVEL_NO_1) 
			REFERENCES PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK9_PER_TRANSFER_REQ FOREIGN KEY (PL_CODE_2) 
			REFERENCES PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK10_PER_TRANSFER_REQ FOREIGN KEY (PN_CODE_2) 
			REFERENCES PER_POS_NAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK11_PER_TRANSFER_REQ FOREIGN KEY (ORG_ID_2) 
			REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK12_PER_TRANSFER_REQ FOREIGN KEY (LEVEL_NO_2) 
			REFERENCES PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK13_PER_TRANSFER_REQ FOREIGN KEY (PL_CODE_3) 
			REFERENCES PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK14_PER_TRANSFER_REQ FOREIGN KEY (PN_CODE_3) 
			REFERENCES PER_POS_NAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK15_PER_TRANSFER_REQ FOREIGN KEY (ORG_ID_3) 
			REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK16_PER_TRANSFER_REQ FOREIGN KEY (LEVEL_NO_3) 
			REFERENCES PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

		} // end if odbc

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE  PER_POSITION DROP CONSTRAINT INXU1_PER_POSITION ";
		elseif($DPISDB=="oci8") 
			$cmd = " DROP INDEX INXU1_PER_POSITION ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE  PER_ORG_TYPE DROP CONSTRAINT INXU1_PER_ORG_TYPE ";
		elseif($DPIS4DB=="oci8") 
			$cmd = " CREATE INDEX INX_PER_POSITION ON PER_POSITION (POS_NO) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD DEPARTMENT_ID INTEGER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD DEPARTMENT_ID NUMBER(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD APPROVE_PER_ID INTEGER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD APPROVE_PER_ID NUMBER(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD REPLACE_PER_ID INTEGER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD REPLACE_PER_ID NUMBER(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD ABSENT_FLAG SINGLE Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD ABSENT_FLAG NUMBER(1) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ORG_JOB ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ORG_JOB ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_ORG_JOB, PER_PERSONAL SET PER_ORG_JOB.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_ORG_JOB.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_ORG_JOB A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD EP_CODE VARCHAR(10) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD EP_CODE VARCHAR2(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_POSITIONHIS, PER_PERSONAL SET PER_POSITIONHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_POSITIONHIS.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_POSITIONHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SALARYHIS ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SALARYHIS ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_SALARYHIS, PER_PERSONAL SET PER_SALARYHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_SALARYHIS.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_SALARYHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_EXTRAHIS ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_EXTRAHIS ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_EXTRAHIS, PER_PERSONAL SET PER_EXTRAHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_EXTRAHIS.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_EXTRAHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_EDUCATE ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_EDUCATE ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_EDUCATE, PER_PERSONAL SET PER_EDUCATE.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_EDUCATE.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_EDUCATE A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_TRAINING ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_TRAINING ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_TRAINING, PER_PERSONAL SET PER_TRAINING.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_TRAINING.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_TRAINING A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ABILITY ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ABILITY ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_ABILITY, PER_PERSONAL SET PER_ABILITY.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_ABILITY.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_ABILITY A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_HEIR ADD PER_CARDNO	VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_HEIR ADD PER_CARDNO	VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_HEIR, PER_PERSONAL SET PER_HEIR.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_HEIR.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_HEIR A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ABSENTHIS ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ABSENTHIS ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_ABSENTHIS, PER_PERSONAL SET PER_ABSENTHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_ABSENTHIS.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_ABSENTHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PUNISHMENT ADD PER_CARDNO	VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PUNISHMENT ADD PER_CARDNO	VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_PUNISHMENT, PER_PERSONAL SET PER_PUNISHMENT.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_PUNISHMENT.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_PUNISHMENT A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SERVICEHIS ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SERVICEHIS ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_SERVICEHIS, PER_PERSONAL SET PER_SERVICEHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_SERVICEHIS.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_SERVICEHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_REWARDHIS ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_REWARDHIS ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_REWARDHIS, PER_PERSONAL SET PER_REWARDHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_REWARDHIS.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_REWARDHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_MARRHIS ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_MARRHIS ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_MARRHIS, PER_PERSONAL SET PER_MARRHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_MARRHIS.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_MARRHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_NAMEHIS ADD PER_CARDNO	VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_NAMEHIS ADD PER_CARDNO	VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_NAMEHIS, PER_PERSONAL SET PER_NAMEHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_NAMEHIS.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_NAMEHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_DECORATEHIS ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_DECORATEHIS ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_GAZETTE VARCHAR(255) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_GAZETTE VARCHAR2(255) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_DECORATEHIS, PER_PERSONAL SET PER_DECORATEHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_DECORATEHIS.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_DECORATEHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_TIMEHIS ADD PER_CARDNO	VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_TIMEHIS ADD PER_CARDNO	VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_TIMEHIS, PER_PERSONAL SET PER_TIMEHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_TIMEHIS.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_TIMEHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONALPIC ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONALPIC ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONALPIC ADD PER_PICPATH VARCHAR(255) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONALPIC ADD PER_PICPATH VARCHAR2(255) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_PERSONALPIC, PER_PERSONAL SET PER_PERSONALPIC.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_PERSONALPIC.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_PERSONALPIC A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_COMDTL ADD PER_CARDNO	VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_COMDTL ADD PER_CARDNO	VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_COMDTL ADD EP_CODE VARCHAR(10) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_COMDTL ADD EP_CODE VARCHAR2(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_COMDTL ADD POEMS_ID INTEGER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_COMDTL ADD POEMS_ID NUMBER(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_COMDTL ADD EP_CODE_ASSIGN VARCHAR(10) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_COMDTL ADD EP_CODE_ASSIGN VARCHAR2(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_COMDTL, PER_PERSONAL SET PER_COMDTL.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_COMDTL.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_COMDTL A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_MOVE_REQ ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_MOVE_REQ ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_MOVE_REQ, PER_PERSONAL SET PER_MOVE_REQ.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_MOVE_REQ.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_MOVE_REQ A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PROMOTE_C ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PROMOTE_C ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_PROMOTE_C, PER_PERSONAL SET PER_PROMOTE_C.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_PROMOTE_C.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_PROMOTE_C A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PROMOTE_P ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PROMOTE_P ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_PROMOTE_P, PER_PERSONAL SET PER_PROMOTE_P.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_PROMOTE_P.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_PROMOTE_P A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PROMOTE_E ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PROMOTE_E ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_PROMOTE_E, PER_PERSONAL SET PER_PROMOTE_E.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_PROMOTE_E.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_PROMOTE_E A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SALPROMOTE ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SALPROMOTE ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_SALPROMOTE, PER_PERSONAL SET PER_SALPROMOTE.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_SALPROMOTE.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_SALPROMOTE A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_BONUSPROMOTE ADD PER_CARDNO	VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_BONUSPROMOTE ADD PER_CARDNO	VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_BONUSPROMOTE, PER_PERSONAL SET PER_BONUSPROMOTE.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_BONUSPROMOTE.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_BONUSPROMOTE A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ABSENT ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ABSENT ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ABSENT ADD APPROVE_FLAG SINGLE Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ABSENT ADD APPROVE_FLAG NUMBER(1) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ABSENT ADD APPROVE_PER_ID INTEGER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ABSENT ADD APPROVE_PER_ID NUMBER(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_ABSENT, PER_PERSONAL SET PER_ABSENT.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_ABSENT.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_ABSENT A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_INVEST1DTL ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_INVEST1DTL ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_INVEST1DTL, PER_PERSONAL SET PER_INVEST1DTL.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_INVEST1DTL.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_INVEST1DTL A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_INVEST2DTL ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_INVEST2DTL ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_INVEST2DTL, PER_PERSONAL SET PER_INVEST2DTL.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_INVEST2DTL.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_INVEST2DTL A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SCHOLAR ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SCHOLAR ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_SCHOLAR, PER_PERSONAL SET PER_SCHOLAR.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_SCHOLAR.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_SCHOLAR A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_COURSEDTL ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_COURSEDTL ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_COURSEDTL, PER_PERSONAL SET PER_COURSEDTL.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_COURSEDTL.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_COURSEDTL A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_DECORDTL ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_DECORDTL ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_DECORDTL, PER_PERSONAL SET PER_DECORDTL.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_DECORDTL.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_DECORDTL A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LETTER ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LETTER ADD PER_CARDNO VARCHAR2(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LETTER ADD LET_ADDITIONAL VARCHAR(255) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LETTER ADD LET_ADDITIONAL VARCHAR2(255) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_LETTER, PER_PERSONAL SET PER_LETTER.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_LETTER.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_LETTER A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ABSENT ADD ABS_REASON	VARCHAR(255) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ABSENT ADD ABS_REASON	VARCHAR2(255) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ABSENT ADD ABS_ADDRESS	VARCHAR(255) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ABSENT ADD ABS_ADDRESS	VARCHAR2(255) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_BLOOD(
			BL_CODE VARCHAR(10) NOT NULL,	
			BL_NAME VARCHAR(100) NOT NULL,
			BL_ACTIVE SINGLE NOT NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_BLOOD PRIMARY KEY (bl_code)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_BLOOD(
			BL_CODE VARCHAR2(10) NOT NULL,	
			BL_NAME VARCHAR2(100) NOT NULL,
			BL_ACTIVE NUMBER(1) NOT NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_BLOOD PRIMARY KEY (bl_code)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_LAYER_NEW(
			LAYER_TYPE SINGLE NOT NULL,	
			LEVEL_NO VARCHAR(2) NOT NULL,
			LAYER_NO NUMBER NOT NULL,
			LAYER_SALARY NUMBER NOT NULL,	
			LAYER_ACTIVE SINGLE NOT NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			LAYER_SALARY_MIN NUMBER NULL,	
			LAYER_SALARY_MAX NUMBER NULL,	
			CONSTRAINT PK_LAYER_NEW PRIMARY KEY (layer_type, level_no, layer_no)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_LAYER_NEW(
			LAYER_TYPE NUMBER(1) NOT NULL,	
			LEVEL_NO VARCHAR2(2) NOT NULL,
			LAYER_NO NUMBER(3,1) NOT NULL,
			LAYER_SALARY NUMBER(16,2) NOT NULL,	
			LAYER_ACTIVE NUMBER(1) NOT NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			LAYER_SALARY_MIN NUMBER(16,2) NULL,	
			LAYER_SALARY_MAX NUMBER(16,2) NULL,	
			CONSTRAINT PK_LAYER_NEW PRIMARY KEY (layer_type, level_no, layer_no)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_SPECIAL_SKILLGRP(
			SS_CODE VARCHAR(10) NOT NULL,	
			SS_NAME VARCHAR(100) NOT NULL,
			SS_ACTIVE SINGLE NOT NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_SPECIAL_SKILLGRP PRIMARY KEY (ss_code)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_SPECIAL_SKILLGRP(
			SS_CODE VARCHAR2(10) NOT NULL,	
			SS_NAME VARCHAR2(100) NOT NULL,
			SS_ACTIVE NUMBER(1) NOT NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_SPECIAL_SKILLGRP PRIMARY KEY (ss_code)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_SPECIAL_SKILL(
			SPS_ID INTEGER NOT NULL,	
			PER_ID INTEGER NOT NULL,	
			PER_CARDNO VARCHAR(13) NULL,
			SS_CODE VARCHAR(10) NOT NULL,	
			SPS_EMPHASIZE MEMO NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_SPECIAL_SKILL PRIMARY KEY (sps_id)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_SPECIAL_SKILL(
			SPS_ID NUMBER(10) NOT NULL,	
			PER_ID NUMBER(10) NOT NULL,	
			PER_CARDNO VARCHAR2(13) NULL,
			SS_CODE VARCHAR2(10) NOT NULL,	
			SPS_EMPHASIZE VARCHAR2(1000) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_SPECIAL_SKILL PRIMARY KEY (sps_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_PERFORMANCE_REVIEW(
			PFR_ID INTEGER NOT NULL,	
			PFR_NAME VARCHAR(100) NOT NULL,
			PFR_YEAR VARCHAR(4) NOT NULL,
			PFR_ID_REF INTEGER NULL,	
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_PERFORMANCE_REVIEW PRIMARY KEY (pfr_id)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_PERFORMANCE_REVIEW(
			PFR_ID NUMBER(10) NOT NULL,	
			PFR_NAME VARCHAR2(100) NOT NULL,
			PFR_YEAR VARCHAR2(4) NOT NULL,
			PFR_ID_REF NUMBER(10) NULL,	
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_PERFORMANCE_REVIEW PRIMARY KEY (pfr_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_KPI(
			KPI_ID INTEGER NOT NULL,	
			KPI_NAME MEMO NOT NULL,
			KPI_YEAR VARCHAR(4) NOT NULL,
			KPI_WEIGHT NUMBER NULL,
			KPI_MEASURE VARCHAR(100) NULL,
			KPI_PER_ID INTEGER NOT NULL,	
			PFR_ID INTEGER NOT NULL,	
			KPI_TARGET_LEVEL1 INTEGER2 NULL,
			KPI_TARGET_LEVEL2 INTEGER2 NULL,
			KPI_TARGET_LEVEL3 INTEGER2 NULL,
			KPI_TARGET_LEVEL4 INTEGER2 NULL,
			KPI_TARGET_LEVEL5 INTEGER2 NULL,
			KPI_ID_REF INTEGER NULL,	
			KPI_EVALUATE SINGLE NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_KPI PRIMARY KEY (kpi_id)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_KPI(
			KPI_ID NUMBER(10) NOT NULL,	
			KPI_NAME VARCHAR2(2000) NOT NULL,
			KPI_YEAR VARCHAR2(4) NOT NULL,
			KPI_WEIGHT NUMBER(7,2) NULL,
			KPI_MEASURE VARCHAR2(100) NULL,
			KPI_PER_ID NUMBER(10) NOT NULL,	
			PFR_ID NUMBER(10) NOT NULL,	
			KPI_TARGET_LEVEL1 NUMBER(3) NULL,
			KPI_TARGET_LEVEL2 NUMBER(3) NULL,
			KPI_TARGET_LEVEL3 NUMBER(3) NULL,
			KPI_TARGET_LEVEL4 NUMBER(3) NULL,
			KPI_TARGET_LEVEL5 NUMBER(3) NULL,
			KPI_ID_REF NUMBER(10) NULL,	
			KPI_EVALUATE NUMBER(1) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_KPI PRIMARY KEY (kpi_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_JOB_FAMILY(
			JF_CODE VARCHAR(2) NOT NULL,	
			JF_NAME VARCHAR(100) NOT NULL,	
			JF_ACTIVE SINGLE NOT NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_JOB_FAMILY PRIMARY KEY (jf_code)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_JOB_FAMILY(
			JF_CODE VARCHAR2(2) NOT NULL,	
			JF_NAME VARCHAR2(100) NOT NULL,	
			JF_ACTIVE NUMBER(1) NOT NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_JOB_FAMILY PRIMARY KEY (jf_code)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_COMPETENCE(
			CP_CODE VARCHAR(3) NOT NULL,	
			CP_NAME VARCHAR(100) NOT NULL,	
			CP_MEANING MEMO NULL,	
			CP_MODEL SINGLE NOT NULL,
			CP_ACTIVE SINGLE NOT NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_COMPETENCE PRIMARY KEY (cp_code)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_COMPETENCE(
			CP_CODE VARCHAR2(3) NOT NULL,	
			CP_NAME VARCHAR2(100) NOT NULL,	
			CP_MEANING VARCHAR2(1000) NULL,	
			CP_MODEL NUMBER(1) NOT NULL,
			CP_ACTIVE NUMBER(1) NOT NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_COMPETENCE PRIMARY KEY (cp_code)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_JOB_COMPETENCE(
			JF_CODE VARCHAR(2) NOT NULL,	
			CP_CODE VARCHAR(3) NOT NULL,	
			JC_ACTIVE SINGLE NOT NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_JOB_COMPETENCE PRIMARY KEY (jf_code, cp_code)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_JOB_COMPETENCE(
			JF_CODE VARCHAR2(2) NOT NULL,	
			CP_CODE VARCHAR2(3) NOT NULL,	
			JC_ACTIVE NUMBER(1) NOT NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_JOB_COMPETENCE PRIMARY KEY (jf_code, cp_code)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_COMPETENCE_LEVEL(
			CP_CODE VARCHAR(3) NOT NULL,	
			CL_NO SINGLE NOT NULL,	
			CL_NAME VARCHAR(255) NOT NULL,	
			CL_MEANING MEMO NULL,	
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_COMPETENCE_LEVEL PRIMARY KEY (cp_code, cl_no)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_COMPETENCE_LEVEL(
			CP_CODE VARCHAR2(3) NOT NULL,	
			CL_NO NUMBER(1) NOT NULL,	
			CL_NAME VARCHAR2(255) NOT NULL,	
			CL_MEANING VARCHAR2(1000) NULL,	
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_COMPETENCE_LEVEL PRIMARY KEY (cp_code, cl_no)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_POSITION_COMPETENCE(
			POS_ID INTEGER NOT NULL,	
			CP_CODE VARCHAR(3) NOT NULL,
			PC_TARGET_LEVEL SINGLE NOT NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_POSITION_COMPETENCE PRIMARY KEY (pos_id, cp_code)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_POSITION_COMPETENCE(
			POS_ID NUMBER(10) NOT NULL,	
			CP_CODE VARCHAR2(3) NOT NULL,
			PC_TARGET_LEVEL NUMBER(1) NOT NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_POSITION_COMPETENCE PRIMARY KEY (pos_id, cp_code)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_KPI_FORM(
			KF_ID INTEGER NOT NULL,	
			PER_ID INTEGER NOT NULL,	
			PER_CARDNO VARCHAR(13) NULL,
			KF_CYCLE SINGLE NOT NULL,
			KF_START_DATE VARCHAR(19) NOT NULL,
			KF_END_DATE VARCHAR(19) NOT NULL,
			PER_ID_REVIEW INTEGER NULL,
			PER_ID_REVIEW1 INTEGER NULL,	
			PER_ID_REVIEW2 INTEGER NULL,	
			SUM_KPI NUMBER NULL,
			SUM_COMPETENCE NUMBER NULL,
			SUM_EVALUATE SINGLE NULL,
			SCORE_KPI INTEGER2 NULL,
			SCORE_COMPETENCE INTEGER2 NULL,
			RESULT_COMMENT MEMO NULL,
			COMPETENCE_COMMENT MEMO NULL,
			SALARY_RESULT SINGLE NULL,
			SALARY_REMARK1 MEMO NULL,
			SALARY_REMARK2 MEMO NULL,
			AGREE_REVIEW1 MEMO NULL,
			DIFF_REVIEW1 MEMO NULL,
			AGREE_REVIEW2 MEMO NULL,
			DIFF_REVIEW2 MEMO NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_KPI_FORM PRIMARY KEY (kf_id)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_KPI_FORM(
			KF_ID NUMBER(10) NOT NULL,	
			PER_ID NUMBER(10) NOT NULL,	
			PER_CARDNO VARCHAR2(13) NULL,
			KF_CYCLE NUMBER(1) NOT NULL,
			KF_START_DATE VARCHAR2(19) NOT NULL,
			KF_END_DATE VARCHAR2(19) NOT NULL,
			PER_ID_REVIEW NUMBER(10) NULL,	
			PER_ID_REVIEW1 NUMBER(10) NULL,	
			PER_ID_REVIEW2 NUMBER(10) NULL,	
			SUM_KPI NUMBER(6,2) NULL,
			SUM_COMPETENCE NUMBER(6,2) NULL,
			SUM_EVALUATE NUMBER(1) NULL,
			SCORE_KPI NUMBER(4) NULL,
			SCORE_COMPETENCE NUMBER(4) NULL,
			RESULT_COMMENT VARCHAR2(1000) NULL,
			COMPETENCE_COMMENT VARCHAR2(1000) NULL,
			SALARY_RESULT NUMBER(1) NULL,
			SALARY_REMARK1 VARCHAR2(1000) NULL,
			SALARY_REMARK2 VARCHAR2(1000) NULL,
			AGREE_REVIEW1 VARCHAR2(1000) NULL,
			DIFF_REVIEW1 VARCHAR2(1000) NULL,
			AGREE_REVIEW2 VARCHAR2(1000) NULL,
			DIFF_REVIEW2 VARCHAR2(1000) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_KPI_FORM PRIMARY KEY (kf_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_PERFORMANCE_GOALS(
			PG_ID INTEGER NOT NULL,	
			KF_ID INTEGER NOT NULL,	
			PG_SEQ INTEGER2 NULL,	
			KPI_ID INTEGER NULL,	
			KPI_NAME MEMO NOT NULL,
			KPI_WEIGHT INTEGER2 NULL,
			KPI_MEASURE VARCHAR(100) NULL,
			KPI_PER_ID INTEGER NOT NULL,	
			KPI_TARGET_LEVEL1 INTEGER2 NULL,
			KPI_TARGET_LEVEL2 INTEGER2 NULL,
			KPI_TARGET_LEVEL3 INTEGER2 NULL,
			KPI_TARGET_LEVEL4 INTEGER2 NULL,
			KPI_TARGET_LEVEL5 INTEGER2 NULL,
			KPI_TARGET_LEVEL1_DESC VARCHAR(255) NULL,
			KPI_TARGET_LEVEL2_DESC VARCHAR(255) NULL,
			KPI_TARGET_LEVEL3_DESC VARCHAR(255) NULL,
			KPI_TARGET_LEVEL4_DESC VARCHAR(255) NULL,
			KPI_TARGET_LEVEL5_DESC VARCHAR(255) NULL,
			PG_RESULT MEMO NULL,
			PG_EVALUATE SINGLE NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_PERFORMANCE_GOALS PRIMARY KEY (pg_id)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_PERFORMANCE_GOALS(
			PG_ID NUMBER(10) NOT NULL,	
			KF_ID NUMBER(10) NOT NULL,	
			PG_SEQ NUMBER(2) NULL,	
			KPI_ID NUMBER(10) NULL,	
			KPI_NAME VARCHAR2(2000) NOT NULL,
			KPI_WEIGHT NUMBER(3) NULL,
			KPI_MEASURE VARCHAR2(100) NULL,
			KPI_PER_ID NUMBER(10) NOT NULL,	
			KPI_TARGET_LEVEL1 NUMBER(3) NULL,
			KPI_TARGET_LEVEL2 NUMBER(3) NULL,
			KPI_TARGET_LEVEL3 NUMBER(3) NULL,
			KPI_TARGET_LEVEL4 NUMBER(3) NULL,
			KPI_TARGET_LEVEL5 NUMBER(3) NULL,
			KPI_TARGET_LEVEL1_DESC VARCHAR2(255) NULL,
			KPI_TARGET_LEVEL2_DESC VARCHAR2(255) NULL,
			KPI_TARGET_LEVEL3_DESC VARCHAR2(255) NULL,
			KPI_TARGET_LEVEL4_DESC VARCHAR2(255) NULL,
			KPI_TARGET_LEVEL5_DESC VARCHAR2(255) NULL,
			PG_RESULT VARCHAR2(1000) NULL,
			PG_EVALUATE NUMBER(1) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_PERFORMANCE_GOALS PRIMARY KEY (pg_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_KPI_COMPETENCE(
			KC_ID INTEGER NOT NULL,	
			KF_ID INTEGER NOT NULL,	
			CP_CODE VARCHAR(3) NOT NULL,	
			PC_TARGET_LEVEL SINGLE NOT NULL,
			KC_EVALUATE SINGLE NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_KPI_COMPETENCE PRIMARY KEY (kc_id)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_KPI_COMPETENCE(
			KC_ID NUMBER(10) NOT NULL,	
			KF_ID NUMBER(10) NOT NULL,	
			CP_CODE VARCHAR2(3) NOT NULL,	
			PC_TARGET_LEVEL NUMBER(1) NOT NULL,
			KC_EVALUATE NUMBER(1) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_KPI_COMPETENCE PRIMARY KEY (kc_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_IPIP(
			IPIP_ID INTEGER NOT NULL,	
			KF_ID INTEGER NOT NULL,	
			DEVELOP_SEQ INTEGER2 NULL,
			DEVELOP_COMPETENCE VARCHAR(255) NULL,
			DEVELOP_METHOD VARCHAR(255) NULL,
			DEVELOP_INTERVAL VARCHAR(255) NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_IPIP PRIMARY KEY (ipip_id)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_IPIP(
			IPIP_ID NUMBER(10) NOT NULL,	
			KF_ID NUMBER(10) NOT NULL,	
			DEVELOP_SEQ NUMBER(2) NULL,
			DEVELOP_COMPETENCE VARCHAR2(255) NULL,
			DEVELOP_METHOD VARCHAR2(255) NULL,
			DEVELOP_INTERVAL VARCHAR2(255) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_IPIP PRIMARY KEY (ipip_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ORG ADD ORG_WEBSITE VARCHAR(255) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ORG ADD ORG_WEBSITE VARCHAR2(255) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ORG ADD ORG_SEQ_NO INTEGER2 ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ORG ADD ORG_SEQ_NO NUMBER(5) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ORG_ASS ADD ORG_WEBSITE VARCHAR(255) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ORG_ASS ADD ORG_WEBSITE VARCHAR2(255) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ORG_ASS ADD ORG_SEQ_NO INTEGER2 ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ORG_ASS ADD ORG_SEQ_NO NUMBER(5) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ORG_ASS ALTER ORG_SHORT VARCHAR(100) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ORG_ASS MODIFY ORG_SHORT VARCHAR2(100) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SERVICETITLE ADD SV_CODE VARCHAR(2) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SERVICETITLE ADD SV_CODE VARCHAR2(2) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_SERVICETITLE SET SV_CODE = SUBSTR(SRT_CODE,1,2) ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_SERVICETITLE SET SV_CODE = SRT_CODE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SERVICETITLE ALTER SRT_NAME MEMO ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SERVICETITLE MODIFY SRT_NAME VARCHAR2(1000) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_COMDTL ALTER CMD_NOTE1 MEMO ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_COMDTL MODIFY CMD_NOTE1 VARCHAR2(2000) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_COMDTL ALTER CMD_NOTE2 MEMO ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_COMDTL MODIFY CMD_NOTE2 VARCHAR2(2000) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG1 VARCHAR(255) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG1 VARCHAR2(255) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_POSITIONHIS, PER_ORG SET PER_POSITIONHIS.POH_ORG1 = PER_ORG.ORG_NAME WHERE PER_ORG.ORG_ID = PER_POSITIONHIS.ORG_ID_1 ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG1 = (SELECT ORG_NAME FROM PER_ORG WHERE ORG_ID_1 = PER_ORG.ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG2 VARCHAR(255) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG2 VARCHAR2(255) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_POSITIONHIS, PER_ORG SET PER_POSITIONHIS.POH_ORG2 = PER_ORG.ORG_NAME WHERE PER_ORG.ORG_ID = PER_POSITIONHIS.ORG_ID_2 ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2 = (SELECT ORG_NAME FROM PER_ORG WHERE ORG_ID_2 = PER_ORG.ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG3 VARCHAR(255) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG3 VARCHAR2(255) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_POSITIONHIS, PER_ORG SET PER_POSITIONHIS.POH_ORG3 = PER_ORG.ORG_NAME WHERE PER_ORG.ORG_ID = PER_POSITIONHIS.ORG_ID_3 ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG3 = (SELECT ORG_NAME FROM PER_ORG WHERE ORG_ID_3 = PER_ORG.ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_PERSONAL SET POS_ID = NULL WHERE PER_STATUS = 2 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITION ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITION ADD DEPARTMENT_ID NUMBER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POS_EMP ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POS_EMP ADD DEPARTMENT_ID NUMBER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POS_EMPSER ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POS_EMPSER ADD DEPARTMENT_ID NUMBER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SALQUOTA ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SALQUOTA ADD DEPARTMENT_ID NUMBER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SALQUOTADTL1 ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SALQUOTADTL1 ADD DEPARTMENT_ID NUMBER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SALQUOTADTL2 ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SALQUOTADTL2 ADD DEPARTMENT_ID NUMBER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SALPROMOTE ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SALPROMOTE ADD DEPARTMENT_ID NUMBER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_BONUSQUOTA ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_BONUSQUOTA ADD DEPARTMENT_ID NUMBER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_BONUSQUOTADTL1 ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_BONUSQUOTADTL1 ADD DEPARTMENT_ID NUMBER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_BONUSQUOTADTL2 ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_BONUSQUOTADTL2 ADD DEPARTMENT_ID NUMBER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_BONUSPROMOTE ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_BONUSPROMOTE ADD DEPARTMENT_ID NUMBER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD DEPARTMENT_ID NUMBER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_MOVE_REQ ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_MOVE_REQ ADD DEPARTMENT_ID NUMBER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PROMOTE_C ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PROMOTE_C ADD DEPARTMENT_ID NUMBER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PROMOTE_E ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PROMOTE_E ADD DEPARTMENT_ID NUMBER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PROMOTE_P ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PROMOTE_P ADD DEPARTMENT_ID NUMBER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_COMMAND ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_COMMAND ADD DEPARTMENT_ID NUMBER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LETTER ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LETTER ADD DEPARTMENT_ID NUMBER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_DECOR ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_DECOR ADD DEPARTMENT_ID NUMBER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_COURSE ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_COURSE ADD DEPARTMENT_ID NUMBER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SCHOLAR ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SCHOLAR ADD DEPARTMENT_ID NUMBER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_INVEST1 ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_INVEST1 ADD DEPARTMENT_ID NUMBER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_INVEST2 ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_INVEST2 ADD DEPARTMENT_ID NUMBER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK11_PER_POSITION FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_POS_EMP ADD CONSTRAINT FK5_PER_POS_EMP FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_POS_EMPSER ADD CONSTRAINT FK5_PER_POS_EMPSER FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_SALQUOTA ADD CONSTRAINT FK1_PER_SALQUOTA FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_SALQUOTADTL1 ADD CONSTRAINT FK3_PER_SALQUOTADTL1 FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_SALQUOTADTL2 ADD CONSTRAINT FK3_PER_SALQUOTADTL2 FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_SALPROMOTE ADD CONSTRAINT FK3_PER_SALPROMOTE FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_BONUSQUOTA ADD CONSTRAINT FK1_PER_BONUSQUOTA FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_BONUSQUOTADTL1 ADD CONSTRAINT FK3_PER_BONUSQUOTADTL1 FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_BONUSQUOTADTL2 ADD CONSTRAINT FK3_PER_BONUSQUOTADTL2 FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_BONUSPROMOTE ADD CONSTRAINT FK3_PER_BONUSPROMOTE FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK17_PER_TRANSFER_REQ FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK11_PER_MOVE_REQ FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_PROMOTE_C ADD CONSTRAINT FK2_PER_PROMOTE_C FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_PROMOTE_E ADD CONSTRAINT FK3_PER_PROMOTE_E FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_PROMOTE_P ADD CONSTRAINT FK3_PER_PROMOTE_P FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_COMMAND ADD CONSTRAINT FK2_PER_COMMAND FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_LETTER ADD CONSTRAINT FK3_PER_LETTER FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_DECOR ADD CONSTRAINT FK1_PER_DECOR FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_COURSE ADD CONSTRAINT FK4_PER_COURSE FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_SCHOLAR ADD CONSTRAINT FK8_PER_SCHOLAR FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_INVEST1 ADD CONSTRAINT FK2_PER_INVEST1 FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_INVEST2 ADD CONSTRAINT FK2_PER_INVEST2 FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_EMPSER_POS_NAME(
			EP_CODE VARCHAR(10) NOT NULL,	
			EP_NAME VARCHAR(100) NOT NULL,	
			LEVEL_NO VARCHAR(2) NOT NULL,
			EP_DECOR SINGLE NOT NULL,		
			EP_ACTIVE SINGLE NOT NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_EMPSER_POS_NAME PRIMARY KEY (ep_code)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_EMPSER_POS_NAME(
			EP_CODE VARCHAR2(10) NOT NULL,	
			EP_NAME VARCHAR2(100) NOT NULL,	
			LEVEL_NO VARCHAR2(2) NOT NULL,
			EP_DECOR NUMBER(1) NOT NULL,		
			EP_ACTIVE NUMBER(1) NOT NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_EMPSER_POS_NAME PRIMARY KEY (ep_code)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITION ALTER POS_NO VARCHAR(15) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITION MODIFY POS_NO VARCHAR2(15) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ORDER_DTL ALTER ORD_POS_NO VARCHAR(15) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ORDER_DTL MODIFY ORD_POS_NO VARCHAR2(15) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_REQ3_DTL ALTER REQ_POS_NO VARCHAR(15) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_REQ3_DTL MODIFY REQ_POS_NO VARCHAR2(15) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_REQ2_DTL ALTER REQ_POS_NO VARCHAR(15) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_REQ2_DTL MODIFY REQ_POS_NO VARCHAR2(15) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_REQ1_DTL1 ALTER REQ_POS_NO VARCHAR(15) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_REQ1_DTL1 MODIFY REQ_POS_NO VARCHAR2(15) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_POS_NO VARCHAR(15) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_POS_NO VARCHAR2(15) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_POS_EMPSER(
			POEMS_ID INTEGER NOT NULL,	
			ORG_ID INTEGER NOT NULL,
			POEMS_NO VARCHAR(15) NOT NULL,	
			ORG_ID_1 INTEGER NULL,
			ORG_ID_2 INTEGER NULL,
			EP_CODE VARCHAR(10) NOT NULL,
			POEM_MIN_SALARY NUMBER NOT NULL,	
			POEM_MAX_SALARY NUMBER NOT NULL,		
			POEM_STATUS SINGLE NOT NULL,		
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_POS_EMPSER PRIMARY KEY (poems_id)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_POS_EMPSER(
			POEMS_ID NUMBER(10) NOT NULL,	
			ORG_ID NUMBER(10) NOT NULL,
			POEMS_NO VARCHAR2(15) NOT NULL,	
			ORG_ID_1 NUMBER(10) NULL,
			ORG_ID_2 NUMBER(10) NULL,
			EP_CODE VARCHAR2(10) NOT NULL,
			POEM_MIN_SALARY NUMBER(16,2) NOT NULL,	
			POEM_MAX_SALARY NUMBER(16,2) NOT NULL,		
			POEM_STATUS NUMBER(1) NOT NULL,		
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_POS_EMPSER PRIMARY KEY (poems_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD POEMS_ID INTEGER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD POEMS_ID NUMBER(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_HIP_FLAG VARCHAR(20) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_HIP_FLAG VARCHAR2(20) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_CERT_OCC VARCHAR(20) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_CERT_OCC VARCHAR2(20) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD TR_POSDATE VARCHAR(19) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD TR_POSDATE VARCHAR2(19) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_OCCUPATION(
			OC_CODE VARCHAR(3) NOT NULL,	
			OC_NAME VARCHAR(100) NOT NULL,
			OC_ACTIVE SINGLE NOT NULL,		
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_OCCUPATION PRIMARY KEY (oc_code)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_OCCUPATION(
			OC_CODE VARCHAR2(3) NOT NULL,	
			OC_NAME VARCHAR2(100) NOT NULL,
			OC_ACTIVE NUMBER(1) NOT NULL,		
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_OCCUPATION PRIMARY KEY (oc_code)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_CHILD (
			child_id INTEGER NOT NULL,
			per_id INTEGER NOT NULL,
			pn_code VARCHAR(3) NULL,
			child_name varchar(100) NOT NULL,
			child_surname varchar(100) NOT NULL,
			cardno varchar(13) NULL,
			gender SINGLE NULL,
			birthdate varchar(19) NULL,
			alive_flag SINGLE NULL,
			re_code VARCHAR(2) NULL,
			oc_code VARCHAR(2) NULL,
			oc_other varchar(100) NULL,
			child_type SINGLE NULL,
			type_other varchar(100) NULL,
			doc_type SINGLE NULL,
			doc_no varchar(20) NULL,
			doc_date varchar(19) NULL,
			mr_code VARCHAR(2) NULL,
			mr_doc_type SINGLE NULL,
			mr_doc_no varchar(20) NULL,
			mr_doc_date varchar(19) NULL,
			pv_code VARCHAR(10) NULL,
			post_code VARCHAR(5) NULL,
			incompetent SINGLE NULL,
			in_doc_type SINGLE NULL,
			in_doc_no varchar(20) NULL,
			in_doc_date varchar(19) NULL,
			update_user INTEGER2 NOT NULL,
			update_date varchar(19) NOT NULL,
			CONSTRAINT PK_CHILD PRIMARY KEY (child_id)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_CHILD (
			child_id number(10) NOT NULL,
			per_id number(10) NOT NULL,
			pn_code varchar2(3) NULL,
			child_name varchar2(100) NOT NULL,
			child_surname varchar2(100) NOT NULL,
			cardno varchar2(13) NULL,
			gender number(1) NULL,
			birthdate varchar2(19) NULL,
			alive_flag number(1) NULL,
			re_code varchar2(2) NULL,
			oc_code varchar2(2) NULL,
			oc_other varchar2(100) NULL,
			child_type number(1) NULL,
			type_other varchar2(100) NULL,
			doc_type number(1) NULL,
			doc_no varchar2(20) NULL,
			doc_date varchar2(19) NULL,
			mr_code varchar2(2) NULL,
			mr_doc_type number(1) NULL,
			mr_doc_no varchar2(20) NULL,
			mr_doc_date varchar2(19) NULL,
			pv_code varchar2(10) NULL,
			post_code varchar2(5) NULL,
			incompetent number(1) NULL,
			in_doc_type number(1) NULL,
			in_doc_no varchar2(20) NULL,
			in_doc_date varchar2(19) NULL,
			update_user number(5) NOT NULL,
			update_date varchar2(19) NOT NULL,
			CONSTRAINT PK_CHILD PRIMARY KEY (child_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_EDUCATE ALTER EDU_TYPE VARCHAR(20) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_EDUCATE MODIFY EDU_TYPE VARCHAR2(20) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_EDUCATE SET EDU_TYPE = '||'+EDU_TYPE+'||' ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_EDUCATE SET EDU_TYPE = '||'||EDU_TYPE||'||' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_CONTROL ADD CTRL_TYPE SINGLE Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_CONTROL ADD CTRL_TYPE NUMBER(1) Null";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_CONTROL ADD PV_CODE VARCHAR(10) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_CONTROL ADD PV_CODE VARCHAR2(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_CONTROL DROP CONSTRAINT FK1_PER_CONTROL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_CONTROL ALTER ORG_ID INTEGER NULL ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_CONTROL MODIFY ORG_ID NUMBER(10) NULL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_OFF_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('08', 'พนักงานราชการทั่วไป', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_OFF_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('09', 'พนักงานราชการพิเศษ', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('230', 'ประเภทเลื่อนระดับพนักงานราชการ', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('23010', 'เลื่อนระดับดี', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('23020', 'เลื่อนระดับดีเด่น', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('23030', 'ทำสัญญาจ้างครั้งแรก', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('23040', 'ต่อสัญญา', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// ความเชี่ยวชาญพิเศษ
		$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('001', 'ด้านการศึกษา', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('002', 'ด้านการแพทย์และสาธารณสุข', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('003', 'ด้านเกษตร', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('004', 'ด้านทรัพยากรธรรมชาติและสิ่งแวดล้อม', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('005', 'ด้านวิทยาศาสตร์และเทคโนโลยี', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('006', 'ด้านวิศวกรรม', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('007', 'ด้านสถาปัตยกรรม', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('008', 'ด้านการเงิน การคลัง งบประมาณ', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('009', 'ด้านสังคม', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('010', 'ด้านกฏหมาย', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('011', 'ด้านการปกครอง การเมือง', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('012', 'ด้านศิลปวัฒนธรรมและศาสนา', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('013', 'ด้านการวางแผนพัฒนา', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('014', 'ด้านพาณิชย์และบริการ', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('015', 'ด้านความมั่นคง', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('016', 'ด้านการบริหารจัดการและบริหารธุรกิจ', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('017', 'ด้านการประชาสัมพันธ์', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('018', 'ด้านการคมนาคมและการสื่อสาร', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('019', 'ด้านพลังงาน', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('020', 'ด้านต่างประเทศ', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('021', 'ด้านอุตสาหกรรม', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('022', 'ด้านพิธีการ', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 
	
// KPI
		$cmd = " INSERT INTO PER_PERFORMANCE_REVIEW (PFR_ID, PFR_NAME, PFR_YEAR, PFR_ID_REF, UPDATE_USER, UPDATE_DATE)
		VALUES (1, 'มิติที่ 1 มิติด้านประสิทธิผลตามแผนปฏิบัติราชการ', '2549' , null, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_PERFORMANCE_REVIEW (PFR_ID, PFR_NAME, PFR_YEAR, PFR_ID_REF, UPDATE_USER, UPDATE_DATE)
		VALUES (2, 'มิติที่ 2 มิติด้านคุณภาพการให้บริการ', '2549', null, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_PERFORMANCE_REVIEW (PFR_ID, PFR_NAME, PFR_YEAR, PFR_ID_REF, UPDATE_USER, UPDATE_DATE)
		VALUES (3, 'มิติที่ 3 มิติด้านประสิทธิภาพของการปฏิบัติราชการ', '2549', null, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_PERFORMANCE_REVIEW (PFR_ID, PFR_NAME, PFR_YEAR, PFR_ID_REF, UPDATE_USER, UPDATE_DATE)
		VALUES (4, 'มิติที่ 4 มิติด้านการพัฒนาองค์กร', '2549', null, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_PERFORMANCE_REVIEW (PFR_ID, PFR_NAME, PFR_YEAR, PFR_ID_REF, UPDATE_USER, UPDATE_DATE)
		VALUES (5, 'ผลสำเร็จตามแผนปฏิบัติราชการ', '2549' , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_PERFORMANCE_REVIEW (PFR_ID, PFR_NAME, PFR_YEAR, PFR_ID_REF, UPDATE_USER, UPDATE_DATE)
		VALUES (6, 'คุณภาพการให้บริการ', '2549', 2, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_PERFORMANCE_REVIEW (PFR_ID, PFR_NAME, PFR_YEAR, PFR_ID_REF, UPDATE_USER, UPDATE_DATE)
		VALUES (7, 'การมีส่วนร่วมของประชาชน', '2549', 2, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_PERFORMANCE_REVIEW (PFR_ID, PFR_NAME, PFR_YEAR, PFR_ID_REF, UPDATE_USER, UPDATE_DATE)
		VALUES (8, 'การป้องกันและปราบปรามการทุจริตและประพฤติมิชอบ', '2549', 2, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_PERFORMANCE_REVIEW (PFR_ID, PFR_NAME, PFR_YEAR, PFR_ID_REF, UPDATE_USER, UPDATE_DATE)
		VALUES (9, 'การบริหารงบประมาณ', '2549', 3, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_PERFORMANCE_REVIEW (PFR_ID, PFR_NAME, PFR_YEAR, PFR_ID_REF, UPDATE_USER, UPDATE_DATE)
		VALUES (10, 'การประหยัดพลังงาน', '2549', 3, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_PERFORMANCE_REVIEW (PFR_ID, PFR_NAME, PFR_YEAR, PFR_ID_REF, UPDATE_USER, UPDATE_DATE)
		VALUES (11, 'การลดระยะเวลาการให้บริการ', '2549', 3, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_PERFORMANCE_REVIEW (PFR_ID, PFR_NAME, PFR_YEAR, PFR_ID_REF, UPDATE_USER, UPDATE_DATE)
		VALUES (12, 'การประหยัดงบประมาณ', '2549', 3, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_PERFORMANCE_REVIEW (PFR_ID, PFR_NAME, PFR_YEAR, PFR_ID_REF, UPDATE_USER, UPDATE_DATE)
		VALUES (13, 'การจัดการความรู้', '2549', 4, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_PERFORMANCE_REVIEW (PFR_ID, PFR_NAME, PFR_YEAR, PFR_ID_REF, UPDATE_USER, UPDATE_DATE)
		VALUES (14, 'การจัดการสารสนเทศ', '2549', 4, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_PERFORMANCE_REVIEW (PFR_ID, PFR_NAME, PFR_YEAR, PFR_ID_REF, UPDATE_USER, UPDATE_DATE)
		VALUES (15, 'การบริหารการเปลี่ยนแปลงและการพัฒนาบุคลากร', '2549', 4, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_PERFORMANCE_REVIEW (PFR_ID, PFR_NAME, PFR_YEAR, PFR_ID_REF, UPDATE_USER, UPDATE_DATE)
		VALUES (16, 'การพัฒนากฏหมาย', '2549', 4, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_PERFORMANCE_REVIEW (PFR_ID, PFR_NAME, PFR_YEAR, PFR_ID_REF, UPDATE_USER, UPDATE_DATE)
		VALUES (17, 'การถ่ายทอดตัวชี้วัดและเป้าหมายของระดับองค์กรสู่ระดับบุคคล', '2549', 4, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_FAMILY (JF_CODE, JF_NAME, JF_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('01', 'กลุ่มงานสนับสนุนทั่วไป', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_FAMILY (JF_CODE, JF_NAME, JF_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('02', 'กลุ่มงานสนับสนุนงานหลักทางเทคนิคเฉพาะด้าน', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_FAMILY (JF_CODE, JF_NAME, JF_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('03', 'กลุ่มงานให้คำปรึกษา', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_FAMILY (JF_CODE, JF_NAME, JF_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('04', 'กลุ่มงานบริหาร', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_FAMILY (JF_CODE, JF_NAME, JF_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('05', 'กลุ่มงานนโยบายและวางแผน', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_FAMILY (JF_CODE, JF_NAME, JF_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('06', 'กลุ่มงานศึกษาวิจัยและพัฒนา', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_FAMILY (JF_CODE, JF_NAME, JF_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('07', 'กลุ่มงานข่าวกรองและสืบสวน', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_FAMILY (JF_CODE, JF_NAME, JF_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('08', 'กลุ่มงานออกแบบเพื่อพัฒนา', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_FAMILY (JF_CODE, JF_NAME, JF_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('09', 'กลุ่มงานความสัมพันธ์ระหว่างประเทศ', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_FAMILY (JF_CODE, JF_NAME, JF_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('10', 'กลุ่มงานบังคับใช้กฏหมาย', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_FAMILY (JF_CODE, JF_NAME, JF_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('11', 'กลุ่มงานเผยแพร่ประชาสัมพันธ์', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_FAMILY (JF_CODE, JF_NAME, JF_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('12', 'กลุ่มงานส่งเสริมความรู้', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_FAMILY (JF_CODE, JF_NAME, JF_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('13', 'กลุ่มงานบริการประชาชนด้านสุขภาพและสวัสดิภาพ', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_FAMILY (JF_CODE, JF_NAME, JF_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('14', 'กลุ่มงานบริการประชาชนทางศิลปวัฒนธรรม', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_FAMILY (JF_CODE, JF_NAME, JF_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('15', 'กลุ่มงานบริการประชาชนทางเทคนิคเฉพาะด้าน', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_FAMILY (JF_CODE, JF_NAME, JF_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('16', 'กลุ่มงานเอกสารราชการและทะเบียน', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_FAMILY (JF_CODE, JF_NAME, JF_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('17', 'กลุ่มงานการปกครอง', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_FAMILY (JF_CODE, JF_NAME, JF_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('18', 'กลุ่มงานอนุรักษ์', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('101', 'การมุ่งผลสัมฤทธิ์', 1 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('102', 'บริการที่ดี', 1 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('103', 'การสั่งสมความเชี่ยวชาญในงานอาชีพ', 1 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('104', 'จริยธรรม', 1 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('105', 'ความร่วมแรงร่วมใจ', 1 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('201', 'การวางแผนและการจัดระบบงาน', 2 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('202', 'การพัฒนาผู้ใต้บังคับบัญชา', 2 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('203', 'ความเป็นผู้นำ', 2 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('301', 'การคิดวิเคราะห์', 3 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('302', 'การมองภาพองค์รวม', 3 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('303', 'การพัฒนาศักยภาพคน', 3 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('304', 'การสั่งการตามอำนาจหน้าที่', 3 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('305', 'การสืบเสาะหาข้อมูล', 3 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('306', 'ความเข้าใจข้อแตกต่างทางวัฒนธรรม', 3 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('307', 'ความเข้าใจผู้อื่น', 3 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('308', 'ความเข้าใจองค์กรและระบบราชการ', 3 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('309', 'การดำเนินการเชิงรุก', 3 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('310', 'ความถูกต้องของงาน', 3 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('311', 'ความมั่นใจในตนเอง', 3 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('312', 'ความยืดหยุ่นผ่อนปรน', 3 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('313', 'ศิลปะการสื่อสารจูงใจ', 3 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('314', 'สภาวะผู้นำ', 3 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('315', 'สุนทรียภาพทางศิลปะ', 3 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('316', 'วิสัยทัศน์', 3 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('317', 'การวางกลยุทธ์ภาครัฐ', 3 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('318', 'ศักยภาพเพื่อนำการปรับเปลี่ยน', 3 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('319', 'การควบคุมตนเอง', 3 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MODEL, CP_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('320', 'การให้อำนาจแก่ผู้อื่น', 3 , 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('01', '312', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('01', '310', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('01', '301', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('02', '312', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('02', '301', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
			VALUES ('02', '310', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('03', '302', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('03', '308', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('03', '313', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('04', '316', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('04', '317', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('04', '318', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('04', '319', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('04', '320', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('05', '302', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('05', '301', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('05', '313', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('06', '302', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('06', '301', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('06', '305', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('07', '305', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('07', '301', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('07', '312', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('08', '302', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('08', '309', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('08', '305', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('09', '302', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('09', '306', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('09', '313', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('10', '304', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('10', '301', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('10', '305', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('11', '313', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('11', '309', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('11', '311', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('12', '307', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('12', '303', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('12', '313', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('13', '303', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('13', '309', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('13', '301', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('14', '315', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('14', '309', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('14', '311', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('15', '301', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('15', '305', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('15', '310', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('16', '310', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('16', '312', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('16', '304', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('17', '309', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('17', '313', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('17', '308', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('18', '302', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('18', '301', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_JOB_COMPETENCE (JF_CODE, CP_CODE, JC_ACTIVE, UPDATE_USER, UPDATE_DATE)
		VALUES ('18', '304', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, UPDATE_USER, UPDATE_DATE)
		VALUES ('101', 0, 'ไม่แสดงสมรรถนะด้านนี้อย่างชัดเจน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, UPDATE_USER, UPDATE_DATE)
		VALUES ('101', 1, 'แสดงความพยายามในการทำงานให้ดี', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, UPDATE_USER, UPDATE_DATE)
		VALUES ('101', 2, 'แสดงสมรรถนะระดับที่ 1 และสามารถทำงานได้ผลตามเป้าหมายที่วางไว้', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, UPDATE_USER, UPDATE_DATE)
		VALUES ('101', 3, 'แสดงสมรรถนะระดับที่ 2 และสามารถทำงานได้ผลงานที่มีประสิทธิภาพยิ่งขึ้น', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, UPDATE_USER, UPDATE_DATE)
		VALUES ('101', 4, 'แสดงสมรรถนะระดับที่ 3 และสามารถพัฒนาวิธีการทำงาน เพื่อให้ได้ผลงานที่โดดเด่น และแตกต่างอย่างไม่เคยมีใครทำได้มาก่อน', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_COMPETENCE_LEVEL (CP_CODE, CL_NO, CL_NAME, UPDATE_USER, UPDATE_DATE)
		VALUES ('101', 5, 'แสดงสมรรถนะระดับที่ 4 และสามารถตัดสินใจได้ แม้จะมีความเสี่ยง เพื่อให้องค์กรบรรลุเป้าหมาย', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LEVEL ADD LEVEL_NAME VARCHAR(100) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LEVEL ADD LEVEL_NAME VARCHAR2(100) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LEVEL ADD PER_TYPE SINGLE Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LEVEL ADD PER_TYPE NUMBER(1) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_NAME = 'ระดับ 10', PER_TYPE = 1 WHERE LEVEL_NO = '10' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_NAME = 'ระดับ 11', PER_TYPE = 1 WHERE LEVEL_NO = '11' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('01', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ 1', 1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('02', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ 2', 1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('03', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ 3', 1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('04', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ 4', 1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('05', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ 5', 1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('06', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ 6', 1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('07', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ 7', 1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('08', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ 8', 1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('09', 1, $SESS_USERID, '$UPDATE_DATE', 'ระดับ 9', 1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('O1', 1, $SESS_USERID, '$UPDATE_DATE', 'ปฏิบัติงาน',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('O2', 1, $SESS_USERID, '$UPDATE_DATE', 'ชำนาญงาน',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('O3', 1, $SESS_USERID, '$UPDATE_DATE', 'อาวุโส',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('O4', 1, $SESS_USERID, '$UPDATE_DATE', 'ทักษะพิเศษ',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('K1', 1, $SESS_USERID, '$UPDATE_DATE', 'ปฏิบัติการ',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('K2', 1, $SESS_USERID, '$UPDATE_DATE', 'ชำนาญการ',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('K3', 1, $SESS_USERID, '$UPDATE_DATE', 'ชำนาญการพิเศษ',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('K4', 1, $SESS_USERID, '$UPDATE_DATE', 'เชี่ยวชาญ',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('K5', 1, $SESS_USERID, '$UPDATE_DATE', 'ทรงคุณวุฒิ',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('D1', 1, $SESS_USERID, '$UPDATE_DATE', 'ต้น',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('D2', 1, $SESS_USERID, '$UPDATE_DATE', 'สูง',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('M1', 1, $SESS_USERID, '$UPDATE_DATE', 'ต้น',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('M2', 1, $SESS_USERID, '$UPDATE_DATE', 'สูง',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('E1', 1, $SESS_USERID, '$UPDATE_DATE', 'กลุ่มงานบริการ',3) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('E2', 1, $SESS_USERID, '$UPDATE_DATE', 'กลุ่มงานเทคนิค',3) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('E3', 1, $SESS_USERID, '$UPDATE_DATE', 'กลุ่มงานบริหารทั่วไป',3) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('E4', 1, $SESS_USERID, '$UPDATE_DATE', 'กลุ่มงานวิชาชีพเฉพาะ',3) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('E5', 1, $SESS_USERID, '$UPDATE_DATE', 'กลุ่มงานเชี่ยวชาญเฉพาะ',3) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('S1', 1, $SESS_USERID, '$UPDATE_DATE', 'กลุ่มงานเชี่ยวชาญพิเศษ (ระดับทั่วไป)',3) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('S2', 1, $SESS_USERID, '$UPDATE_DATE', 'กลุ่มงานเชี่ยวชาญพิเศษ (ระดับประเทศ)',3) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
		VALUES ('S3', 1, $SESS_USERID, '$UPDATE_DATE', 'กลุ่มงานเชี่ยวชาญพิเศษ (ระดับสากล)',3) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		for ($no=1;$no<=9;$no++) {
			$level = "$no";
			$level_no = str_pad(trim($no), 2, "0", STR_PAD_LEFT);
			$cmd = " UPDATE PER_LAYER SET LEVEL_NO = '$level_no' WHERE LEVEL_NO = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_MGTSALARY SET LEVEL_NO = '$level_no' WHERE LEVEL_NO = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '$level_no' WHERE LEVEL_NO_MIN = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MAX = '$level_no' WHERE LEVEL_NO_MAX = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_ASSIGN SET LEVEL_NO_MIN = '$level_no' WHERE LEVEL_NO_MIN = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_ASSIGN SET LEVEL_NO_MAX = '$level_no' WHERE LEVEL_NO_MAX = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_ASSIGN_YEAR SET LEVEL_NO = '$level_no' WHERE LEVEL_NO = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_ASSIGN_S SET LEVEL_NO = '$level_no' WHERE LEVEL_NO = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_PERSONAL SET LEVEL_NO = '$level_no' WHERE LEVEL_NO = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POSITIONHIS SET LEVEL_NO = '$level_no' WHERE LEVEL_NO = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_TRANSFER_REQ SET TR_STARTLEVEL = '$level_no' WHERE TR_STARTLEVEL = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_TRANSFER_REQ SET TR_LEVEL = '$level_no' WHERE TR_LEVEL = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_TRANSFER_REQ SET LEVEL_NO_1 = '$level_no' WHERE LEVEL_NO_1 = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_TRANSFER_REQ SET LEVEL_NO_2 = '$level_no' WHERE LEVEL_NO_2 = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_TRANSFER_REQ SET LEVEL_NO_3 = '$level_no' WHERE LEVEL_NO_3 = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMDTL SET CMD_LEVEL = '$level_no' WHERE CMD_LEVEL = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMDTL SET LEVEL_NO = '$level_no' WHERE LEVEL_NO = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_DECORCOND SET LEVEL_NO = '$level_no' WHERE LEVEL_NO = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_SUM_DTL4 SET LEVEL_NO = '$level_no' WHERE LEVEL_NO = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_SUM_DTL5 SET LEVEL_NO = '$level_no' WHERE LEVEL_NO = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_SUM_DTL7 SET LEVEL_NO = '$level_no' WHERE LEVEL_NO = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_SUM_DTL8 SET LEVEL_NO = '$level_no' WHERE LEVEL_NO = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER_OLD SET LEVEL_NO = '$level_no' WHERE LEVEL_NO = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE SAL_SL_REGISTER SET LEVEL_NO = '$level_no' WHERE LEVEL_NO = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_LEVEL WHERE trim(LEVEL_NO) = '$level' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

		} // end for

		for ($no=1;$no<=9;$no++) {
			$level = "$no";
			$level_no = str_pad(trim($no), 2, "0", STR_PAD_LEFT);
			$cmd = " UPDATE PER_MAP_TYPE SET LEVEL_NO = '$level_no' WHERE LEVEL_NO = '$level' ";
			$db->send_cmd($cmd);
			//$db->show_error();

		} // end for

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MIN NUMBER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MIN NUMBER(16,2) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MAX NUMBER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MAX NUMBER(16,2) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E1', 1, 4920, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E1', 2, 5160, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E1', 3, 5400, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E1', 4, 5640, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E1', 5, 5880, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E1', 6, 6220, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E1', 7, 6560, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E1', 8, 6890, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E1', 9, 7230, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E1', 10, 7640, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E1', 11, 8040, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E1', 12, 8450, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E1', 13, 8860, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E1', 14, 9340, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E1', 15, 9830, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E1', 16, 10340, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E1', 17, 10850, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E1', 18, 11480, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E1', 19, 12100, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E1', 20, 12720, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E1', 21, 13350, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E1', 22, 14120, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E1', 23, 14880, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 1, 5640, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 2, 5880, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 3, 6220, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 4, 6560, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 5, 6890, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 6, 7230, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 7, 7640, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 8, 8040, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 9, 8450, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 10, 8860, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 11, 9340, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 12, 9830, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 13, 10340, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 14, 10850, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 15, 11480, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 16, 12100, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 17, 12720, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 18, 13350, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 19, 14120, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 20, 14880, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 21, 15650, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 22, 16420, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 23, 17190, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 24, 17960, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E2', 25, 18720, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E3', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 7640, 25920) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E4', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 7640, 33350) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'E5', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 13350, 50610) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'S1', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 0, 100000) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'S2', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 0, 150000) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
		VALUES (0, 'S3', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 0, 200000) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

	} // end if
?>