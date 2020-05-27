<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if( $command=='UPDATE' ){
		if($DPISDB=="odbc"){
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


	} // end if
	
?>