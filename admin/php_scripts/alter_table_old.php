<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$CREATE_DATE = "NOW()";
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if( $command=='ALTER' ) {
		$cmd = " SELECT PM_CODE FROM PER_MGT WHERE PM_NAME = 'ไม่ใช่ตำแหน่งทางบริหาร/วิชาการ' OR PM_NAME = 'ไม่ใช่ตำแหน่งบริหาร' OR PM_NAME = '-' OR 
						  PM_NAME = 'ตำแหน่งใช้ประสบการณ์ (ว)' OR PM_NAME = 'ตำแหน่งวิชาชีพ (วช.)' OR PM_NAME = 'ตำแหน่งที่มีประสบการณ์ (ว)' OR PM_NAME = 'เชี่ยวชาญเฉพาะ(ชช.)' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$PM_CODE = $data[PM_CODE];

			$cmd = " UPDATE PER_POSITION SET PM_CODE = NULL WHERE PM_CODE = '$PM_CODE' ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error() ;

			$cmd = " UPDATE PER_POSITIONHIS SET PM_CODE = NULL WHERE PM_CODE = '$PM_CODE' ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error() ;
		} // end while

		$cmd = " UPDATE PER_POSITION SET POS_SALARY = 0 WHERE POS_SALARY IS NULL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " UPDATE PER_POSITION SET POS_STATUS = 2 WHERE POS_STATUS = 0 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " UPDATE PER_POS_EMP SET POEM_STATUS = 2 WHERE POEM_STATUS = 0 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " UPDATE PER_POS_EMPSER SET POEM_STATUS = 2 WHERE POEM_STATUS = 0 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		if($DPISDB=="odbc"){
/*			$cmd = " ALTER TABLE PER_ORG_TYPE DROP CONSTRAINT INXU1_PER_ORG_TYPE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_ORG_TYPE ADD OT_NAME_TEMP VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_ORG_TYPE SET OT_NAME_TEMP = OT_NAME ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_ORG_TYPE DROP OT_NAME ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_ORG_TYPE ADD OT_NAME VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " CREATE UNIQUE INDEX INXU1_PER_ORG_TYPE ON PER_ORG_TYPE (OT_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_ORG_TYPE SET OT_NAME = OT_NAME_TEMP ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_ORG_TYPE DROP OT_NAME_TEMP ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_EXTRA_INCOME_TYPE ADD EXIN_ACTIVE_TEMP SINGLE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_EXTRA_INCOME_TYPE SET EXIN_ACTIVE_TEMP = EXIN_ACTIVE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_EXTRA_INCOME_TYPE DROP EXIN_ACTIVE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_EXTRA_INCOME_TYPE ADD EXIN_ACTIVE SINGLE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_EXTRA_INCOME_TYPE SET EXIN_ACTIVE = EXIN_ACTIVE_TEMP ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_EXTRA_INCOME_TYPE DROP EXIN_ACTIVE_TEMP ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;
*/
			$cmd = " CREATE TABLE PER_EXTRA_INCOME_TYPE (
			EXIN_CODE CHAR(10) NOT NULL,
			EXIN_NAME VARCHAR(100) NOT NULL,
			EXIN_ACTIVE SINGLE NOT NULL,
			UPDATE_USER INTEGER2 NOT NULL, 
			UPDATE_DATE CHAR(19) NOT NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_EXTRA_INCOME_TYPE ADD CONSTRAINT PK_PER_EXTRA_INCOME_TYPE  PRIMARY KEY (EXIN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " CREATE UNIQUE INDEX U1_PER_EXTRA_INCOME_TYPE ON PER_EXTRA_INCOME_TYPE (EXIN_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " CREATE TABLE PER_EXTRA_INCOMEHIS ( 
			EXINH_ID INTEGER2 NOT NULL,
			PER_ID INTEGER2 NOT NULL,
			EXINH_EFFECTIVEDATE CHAR(19) NOT NULL,
			EXIN_CODE CHAR(10) NOT NULL,
			EXINH_AMT NUMBER NOT NULL,
			EXINH_ENDDATE CHAR(19) ,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE CHAR(19) NOT NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ADD CONSTRAINT PK_PER_EXTRA_INCOMEHIS PRIMARY KEY (EXINH_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " CREATE UNIQUE INDEX U1_PER_EXTRA_INCOMEHIS ON PER_EXTRA_INCOMEHIS (PER_ID, EXIN_CODE, EXINH_EFFECTIVEDATE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ADD CONSTRAINT FK_PER_EXTRA_INCOMEHIS FOREIGN KEY (EXIN_CODE) 
							  REFERENCES PER_EXTRA_INCOME_TYPE (EXIN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_ABSENT_CONF ADD PER_TYPE INTEGER NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_ABSENT_CONF SET PER_TYPE = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_ABSENT_CONF ALTER PER_TYPE	INTEGER NOT NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_ABSENT_CONF DROP CONSTRAINT PK_PER_ABSENT_CONF ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_ABSENT_CONF ADD CONSTRAINT PK_PER_ABSENT_CONF PRIMARY KEY (ABS_MONTH, PER_TYPE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " INSERT INTO PER_ABSENT_CONF (ABS_MONTH, UPDATE_USER, UPDATE_DATE, PER_TYPE) 
							  SELECT ABS_MONTH, UPDATE_USER, UPDATE_DATE, 2 FROM PER_ABSENT_CONF ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_ABSENT_CONF_B ADD PER_TYPE	INTEGER NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_ABSENT_CONF_B SET PER_TYPE = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_ABSENT_CONF_B ALTER PER_TYPE INTEGER NOT NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " INSERT INTO PER_ABSENT_CONF_B (ABS_MONTH, UPDATE_USER, UPDATE_DATE, PER_TYPE) 
							  SELECT ABS_MONTH, UPDATE_USER, UPDATE_DATE, 2 FROM PER_ABSENT_CONF_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL ADD CRD_CODE CHAR(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL ADD PEN_CODE CHAR(2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL ADD CONSTRAINT FK4_PER_INVEST2DTL FOREIGN KEY (PEN_CODE) REFERENCES PER_PENALTY (PEN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_INVEST2DTL, PER_INVEST2 SET PER_INVEST2DTL.CRD_CODE = PER_INVEST2.CRD_CODE 
							  WHERE PER_INVEST2.INV_ID = PER_INVEST2DTL.INV_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_INVEST2DTL, PER_INVEST2 SET PER_INVEST2DTL.PEN_CODE = PER_INVEST2.PEN_CODE 
							  WHERE PER_INVEST2.INV_ID = PER_INVEST2DTL.INV_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL ALTER CRD_CODE CHAR(10) NOT NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL ADD CONSTRAINT FK3_PER_INVEST2DTL FOREIGN KEY (CRD_CODE) REFERENCES PER_CRIME_DTL (CRD_CODE) ";
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

			$cmd = " ALTER TABLE PER_INVEST2DTL_B ADD CRD_CODE CHAR(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL_B ADD PEN_CODE CHAR(2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_INVEST2DTL_B, PER_INVEST2_B SET PER_INVEST2DTL_B.CRD_CODE = PER_INVEST2_B.CRD_CODE 
							  WHERE PER_INVEST2_B.INV_ID = PER_INVEST2DTL_B.INV_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_INVEST2DTL_B, PER_INVEST2_B SET PER_INVEST2DTL_B.PEN_CODE = PER_INVEST2_B.PEN_CODE 
							  WHERE PER_INVEST2_B.INV_ID = PER_INVEST2DTL_B.INV_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL_B ALTER CRD_CODE CHAR(10) NOT NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2_B DROP COLUMN CRD_CODE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2_B DROP COLUMN PEN_CODE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

		}elseif($DPISDB=="oci8"){
			$cmd = " ALTER TABLE PER_ORG_TYPE MODIFY OT_NAME VARCHAR2(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " CREATE TABLE PER_EXTRA_INCOME_TYPE (
			EXIN_CODE CHAR(10) NOT NULL,
			EXIN_NAME VARCHAR2(100) NOT NULL,
			EXIN_ACTIVE NUMBER(1) NOT NULL,
			UPDATE_USER NUMBER(5) NOT NULL, 
			UPDATE_DATE CHAR(19) NOT NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_EXTRA_INCOME_TYPE ADD CONSTRAINT PK_PER_EXTRA_INCOME_TYPE  PRIMARY KEY (EXIN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " CREATE UNIQUE INDEX U1_PER_EXTRA_INCOME_TYPE ON PER_EXTRA_INCOME_TYPE (EXIN_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " CREATE TABLE PER_EXTRA_INCOMEHIS ( 
			EXINH_ID NUMBER(10) NOT NULL,
			PER_ID NUMBER(10) NOT NULL,
			EXINH_EFFECTIVEDATE CHAR(19) NOT NULL,
			EXIN_CODE CHAR(10) NOT NULL,
			EXINH_AMT NUMBER(16,2) NOT NULL,
			EXINH_ENDDATE  CHAR(19) ,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE CHAR(19) NOT NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ADD CONSTRAINT PK_PER_EXTRA_INCOMEHIS PRIMARY KEY (EXINH_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " CREATE UNIQUE INDEX U1_PER_EXTRA_INCOMEHIS ON PER_EXTRA_INCOMEHIS (PER_ID, EXIN_CODE, EXINH_EFFECTIVEDATE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ADD CONSTRAINT FK1_PER_EXTRA_INCOMEHIS FOREIGN KEY (PER_ID) 
							  REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ADD CONSTRAINT FK2_PER_EXTRA_INCOMEHIS FOREIGN KEY (EXIN_CODE) 
							  REFERENCES PER_EXTRA_INCOME_TYPE (EXIN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_ABSENT_CONF ADD PER_TYPE NUMBER(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_ABSENT_CONF SET PER_TYPE = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_ABSENT_CONF MODIFY PER_TYPE NOT NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_ABSENT_CONF DROP CONSTRAINT PK_PER_ABSENT_CONF ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_ABSENT_CONF ADD CONSTRAINT PK_PER_ABSENT_CONF PRIMARY KEY (ABS_MONTH, PER_TYPE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " INSERT INTO PER_ABSENT_CONF SELECT ABS_MONTH, UPDATE_USER, UPDATE_DATE, 2 FROM PER_ABSENT_CONF ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_ABSENT_CONF_B ADD PER_TYPE NUMBER(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_ABSENT_CONF_B SET PER_TYPE = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_ABSENT_CONF_B MODIFY PER_TYPE NOT NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " INSERT INTO PER_ABSENT_CONF_B SELECT ABS_MONTH, UPDATE_USER, UPDATE_DATE, 2 FROM PER_ABSENT_CONF_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL ADD CRD_CODE CHAR(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL ADD PEN_CODE CHAR(2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL ADD CONSTRAINT FK3_PER_INVEST2DTL FOREIGN KEY (CRD_CODE) REFERENCES PER_CRIME_DTL (CRD_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL ADD CONSTRAINT FK4_PER_INVEST2DTL FOREIGN KEY (PEN_CODE) REFERENCES PER_PENALTY(PEN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_INVEST2DTL SET CRD_CODE = (SELECT CRD_CODE FROM PER_INVEST2 WHERE PER_INVEST2.INV_ID = PER_INVEST2DTL.INV_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_INVEST2DTL SET PEN_CODE = (SELECT PEN_CODE FROM PER_INVEST2 WHERE PER_INVEST2.INV_ID = PER_INVEST2DTL.INV_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL MODIFY CRD_CODE NOT NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2 DROP COLUMN CRD_CODE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2 DROP COLUMN PEN_CODE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL_B ADD CRD_CODE CHAR(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL_B ADD PEN_CODE CHAR(2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_INVEST2DTL_B SET CRD_CODE = (SELECT CRD_CODE FROM PER_INVEST2_B 
							  WHERE PER_INVEST2_B.INV_ID = PER_INVEST2DTL_B.INV_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " UPDATE PER_INVEST2DTL_B SET PEN_CODE = (SELECT PEN_CODE FROM PER_INVEST2_B 
							  WHERE PER_INVEST2_B.INV_ID = PER_INVEST2DTL_B.INV_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2DTL_B MODIFY CRD_CODE NOT NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2_B DROP COLUMN CRD_CODE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

			$cmd = " ALTER TABLE PER_INVEST2_B DROP COLUMN PEN_CODE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;

		}elseif($DPISDB=="mysql"){
				
		} // end if

		$cmd = " ALTER TABLE PER_COMDTL DROP CONSTRAINT FK2_PER_COMDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COMDTL DROP CONSTRAINT FK6_PER_COMDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MARRHIS DROP CONSTRAINT FK2_PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		if($DPISDB=="odbc") {
			$cmd = " ALTER TABLE PER_ABILITY ADD CONSTRAINT FK2_PER_ABILITY FOREIGN KEY (AL_CODE) REFERENCES PER_ABILITYGRP (AL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ABILITY ADD CONSTRAINT FK1_PER_ABILITY FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ABSENT ADD CONSTRAINT FK2_PER_ABSENT FOREIGN KEY (AB_CODE) REFERENCES PER_ABSENTTYPE (AB_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ABSENT ADD CONSTRAINT FK1_PER_ABSENT FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ABSENTHIS ADD CONSTRAINT FK2_PER_ABSENTHIS FOREIGN KEY (AB_CODE) REFERENCES PER_ABSENTTYPE (AB_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ABSENTHIS ADD CONSTRAINT FK1_PER_ABSENTHIS FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_AMPHUR ADD CONSTRAINT FK1_PER_AMPHUR FOREIGN KEY (PV_CODE) REFERENCES PER_PROVINCE (PV_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ASSIGN ADD CONSTRAINT FK1_PER_ASSIGN FOREIGN KEY (LEVEL_NO_MIN) REFERENCES PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ASSIGN ADD CONSTRAINT FK2_PER_ASSIGN FOREIGN KEY (LEVEL_NO_MAX) REFERENCES PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ASSIGN_DTL ADD CONSTRAINT FK1_PER_ASSIGN_DTL FOREIGN KEY (ASS_ID) REFERENCES PER_ASSIGN (ASS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ASSIGN_DTL ADD CONSTRAINT FK2_PER_ASSIGN_DTL FOREIGN KEY (PL_CODE) REFERENCES PER_LINE(PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ASSIGN_S ADD CONSTRAINT FK1_PER_ASSIGN_S FOREIGN KEY (LEVEL_NO) REFERENCES PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ASSIGN_YEAR ADD CONSTRAINT FK1_PER_ASSIGN_YEAR FOREIGN KEY (EL_CODE) 
							  REFERENCES PER_EDUCLEVEL (EL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ASSIGN_YEAR ADD CONSTRAINT FK2_PER_ASSIGN_YEAR FOREIGN KEY (LEVEL_NO) REFERENCES PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_BONUSPROMOTE ADD CONSTRAINT FK1_PER_BONUSPROMOTE FOREIGN KEY (BONUS_YEAR, 	BONUS_TYPE) 
							  REFERENCES PER_BONUSQUOTA (BONUS_YEAR, BONUS_TYPE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_BONUSPROMOTE ADD CONSTRAINT FK2_PER_BONUSPROMOTE FOREIGN KEY (PER_ID) 
							  REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_BONUSQUOTADTL1 ADD CONSTRAINT FK1_PER_BONUSQUOTADTL1 FOREIGN KEY (BONUS_YEAR, BONUS_TYPE) 
							  REFERENCES PER_BONUSQUOTA (BONUS_YEAR, BONUS_TYPE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_BONUSQUOTADTL1 ADD CONSTRAINT FK2_PER_BONUSQUOTADTL1 FOREIGN KEY (ORG_ID) 
							  REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_BONUSQUOTADTL2 ADD CONSTRAINT FK1_PER_BONUSQUOTADTL2 FOREIGN KEY (BONUS_YEAR, BONUS_TYPE) 
							  REFERENCES PER_BONUSQUOTA (BONUS_YEAR, BONUS_TYPE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_BONUSQUOTADTL2 ADD CONSTRAINT FK2_PER_BONUSQUOTADTL2 FOREIGN KEY (ORG_ID) 
							  REFERENCES PER_ORG_ASS (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_CO_LEVEL ADD CONSTRAINT FK1_PER_CO_LEVEL FOREIGN KEY (LEVEL_NO_MIN) REFERENCES PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_CO_LEVEL ADD CONSTRAINT FK2_PER_CO_LEVEL FOREIGN KEY (LEVEL_NO_MAX) REFERENCES PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK1_PER_COMDTL FOREIGN KEY (COM_ID) REFERENCES PER_COMMAND (COM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 
/*
			$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK2_PER_COMDTL FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 
*/
			$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK3_PER_COMDTL FOREIGN KEY (EN_CODE) REFERENCES PER_EDUCNAME (EN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK4_PER_COMDTL FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK5_PER_COMDTL FOREIGN KEY (PN_CODE) REFERENCES PER_POS_NAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 
/*
			$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK6_PER_COMDTL FOREIGN KEY (POS_ID) REFERENCES PER_POSITION (POS_ID) ";
			$db_dpis->send_cmd($cmd);
//			//$db_dpis->show_error(); 
*/
			$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK7_PER_COMDTL FOREIGN KEY (POEM_ID) REFERENCES PER_POS_EMP (POEM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK8_PER_COMDTL FOREIGN KEY (LEVEL_NO) REFERENCES PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK9_PER_COMDTL FOREIGN KEY (PL_CODE_ASSIGN) REFERENCES PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK10_PER_COMDTL FOREIGN KEY (PN_CODE_ASSIGN) REFERENCES PER_POS_NAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK11_PER_COMDTL FOREIGN KEY (MOV_CODE) REFERENCES PER_MOVMENT (MOV_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COMMAND ADD CONSTRAINT FK1_PER_COMMAND FOREIGN KEY (COM_TYPE) REFERENCES PER_COMTYPE (COM_TYPE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_CONTROL ADD CONSTRAINT FK1_PER_CONTROL FOREIGN KEY (ORG_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COURSE ADD CONSTRAINT FK1_PER_COURSE FOREIGN KEY (TR_CODE) REFERENCES PER_TRAIN (TR_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COURSE ADD CONSTRAINT FK2_PER_COURSE FOREIGN KEY (CT_CODE) REFERENCES PER_COUNTRY (CT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COURSE ADD CONSTRAINT FK3_PER_COURSE FOREIGN KEY (CT_CODE_FUND) REFERENCES PER_COUNTRY (CT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COURSEDTL ADD CONSTRAINT FK1_PER_COURSEDTL FOREIGN KEY (CO_ID) REFERENCES PER_COURSE (CO_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_COURSEDTL ADD CONSTRAINT FK2_PER_COURSEDTL FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_CRIME_DTL ADD CONSTRAINT FK1_PER_CRIME_DTL FOREIGN KEY (CR_CODE) REFERENCES PER_CRIME (CR_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_DECORATEHIS ADD CONSTRAINT FK1_PER_DECORATEHIS FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_DECORATEHIS ADD CONSTRAINT FK2_PER_DECORATEHIS FOREIGN KEY (DC_CODE) 
							  REFERENCES PER_DECORATION (DC_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_DECORCOND ADD CONSTRAINT FK1_PER_DECORCOND FOREIGN KEY (LEVEL_NO) REFERENCES PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_DECORCOND ADD CONSTRAINT FK2_PER_DECORCOND FOREIGN KEY (DC_CODE) 
							  REFERENCES PER_DECORATION (DC_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_DECORCOND ADD CONSTRAINT FK3_PER_DECORCOND FOREIGN KEY (DC_CODE_OLD) 
							  REFERENCES PER_DECORATION (DC_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_DECORDTL ADD CONSTRAINT FK1_PER_DECORDTL FOREIGN KEY (DE_ID) REFERENCES PER_DECOR (DE_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_DECORDTL ADD CONSTRAINT FK2_PER_DECORDTL FOREIGN KEY (DC_CODE) REFERENCES PER_DECORATION (DC_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_DECORDTL ADD CONSTRAINT FK3_PER_DECORDTL FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_EDUCATE ADD CONSTRAINT FK1_PER_EDUCATE FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_EDUCATE ADD CONSTRAINT FK2_PER_EDUCATE FOREIGN KEY (ST_CODE) REFERENCES PER_SCHOLARTYPE (ST_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_EDUCATE ADD CONSTRAINT FK3_PER_EDUCATE FOREIGN KEY (CT_CODE) REFERENCES PER_COUNTRY (CT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_EDUCATE ADD CONSTRAINT FK4_PER_EDUCATE FOREIGN KEY (EN_CODE) REFERENCES	PER_EDUCNAME (EN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_EDUCATE ADD CONSTRAINT FK5_PER_EDUCATE FOREIGN KEY (EM_CODE) REFERENCES	PER_EDUCMAJOR (EM_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_EDUCATE ADD CONSTRAINT FK6_PER_EDUCATE FOREIGN KEY (INS_CODE) REFERENCES PER_INSTITUTE (INS_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_EDUCNAME ADD CONSTRAINT FK1_PER_EDUCNAME FOREIGN KEY (EL_CODE) REFERENCES PER_EDUCLEVEL (EL_CODE) ";
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

			$cmd = " ALTER TABLE PER_HEIR ADD CONSTRAINT FK2_PER_HEIR FOREIGN KEY (HR_CODE) REFERENCES PER_HEIRTYPE (HR_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_INSTITUTE ADD CONSTRAINT FK1_PER_INSTITUTE FOREIGN KEY (CT_CODE) REFERENCES PER_COUNTRY (CT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_INVEST1 ADD CONSTRAINT FK1_PER_INVEST1 FOREIGN KEY (CRD_CODE) REFERENCES PER_CRIME_DTL (CRD_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_INVEST1DTL ADD CONSTRAINT FK1_PER_INVEST1DTL FOREIGN KEY (INV_ID) REFERENCES PER_INVEST1 (INV_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_INVEST1DTL ADD CONSTRAINT FK2_PER_INVEST1DTL FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_INVEST2 ADD CONSTRAINT FK1_PER_INVEST2 FOREIGN KEY (INV_ID_REF) REFERENCES PER_INVEST1 (INV_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_INVEST2DTL ADD CONSTRAINT FK1_PER_INVEST2DTL FOREIGN KEY (INV_ID) REFERENCES PER_INVEST2 (INV_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_INVEST2DTL ADD CONSTRAINT FK2_PER_INVEST2DTL FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_INVEST2DTL ADD CONSTRAINT FK3_PER_INVEST2DTL FOREIGN KEY (CRD_CODE) REFERENCES PER_CRIME_DTL (CRD_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_INVEST2DTL ADD CONSTRAINT FK4_PER_INVEST2DTL FOREIGN KEY (PEN_CODE) REFERENCES PER_PENALTY (PEN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_LAYER ADD CONSTRAINT FK1_PER_LAYER FOREIGN KEY (LEVEL_NO) REFERENCES PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_LAYEREMP ADD CONSTRAINT FK1_PER_LAYEREMP FOREIGN KEY (PG_CODE) REFERENCES PER_POS_GROUP (PG_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_LETTER ADD CONSTRAINT FK1_PER_LETTER FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_LETTER ADD CONSTRAINT FK2_PER_LETTER FOREIGN KEY (PER_ID_SIGN1) REFERENCES PER_PERSONAL  (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_MARRHIS ADD CONSTRAINT FK1_PER_MARRHIS FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 
/*
			$cmd = " ALTER TABLE PER_MARRHIS ADD CONSTRAINT FK2_PER_MARRHIS FOREIGN KEY (DV_CODE) REFERENCES PER_DIVORCE (DV_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 
*/
			$cmd = " ALTER TABLE PER_MGT ADD CONSTRAINT FK1_PER_MGT FOREIGN KEY (PS_CODE) REFERENCES PER_STATUS (PS_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_MGTSALARY ADD CONSTRAINT FK1_PER_MGTSALARY FOREIGN KEY (PT_CODE) REFERENCES PER_TYPE (PT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_MGTSALARY ADD CONSTRAINT FK2_PER_MGTSALARY FOREIGN KEY (LEVEL_NO) REFERENCES PER_LEVEL (LEVEL_NO) ";
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

			$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK4_PER_MOVE_REQ FOREIGN KEY (ORG_ID_1) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK5_PER_MOVE_REQ FOREIGN KEY (PL_CODE_2) REFERENCES PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK6_PER_MOVE_REQ FOREIGN KEY (PN_CODE_2) REFERENCES PER_POS_NAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK7_PER_MOVE_REQ FOREIGN KEY (ORG_ID_2) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK8_PER_MOVE_REQ FOREIGN KEY (PL_CODE_3) REFERENCES PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK9_PER_MOVE_REQ FOREIGN KEY (PN_CODE_3) REFERENCES PER_POS_NAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK10_PER_MOVE_REQ FOREIGN KEY (ORG_ID_3) REFERENCES PER_ORG (ORG_ID) ";
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

			$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK3_PER_ORDER_DTL FOREIGN KEY (ORG_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK4_PER_ORDER_DTL FOREIGN KEY (OT_CODE) REFERENCES PER_OFF_TYPE (OT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK5_PER_ORDER_DTL FOREIGN KEY (ORG_ID_1) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK6_PER_ORDER_DTL FOREIGN KEY (ORG_ID_2) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK7_PER_ORDER_DTL FOREIGN KEY (PM_CODE) REFERENCES	PER_MGT (PM_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK8_PER_ORDER_DTL FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK9_PER_ORDER_DTL FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK10_PER_ORDER_DTL FOREIGN KEY (SKILL_CODE) REFERENCES PER_SKILL (SKILL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK11_PER_ORDER_DTL FOREIGN KEY (PT_CODE) REFERENCES PER_TYPE (PT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK12_PER_ORDER_DTL FOREIGN KEY (PC_CODE) REFERENCES PER_CONDITION (PC_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG ADD CONSTRAINT FK1_PER_ORG FOREIGN KEY (OL_CODE) REFERENCES PER_ORG_LEVEL (OL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG ADD CONSTRAINT FK2_PER_ORG FOREIGN KEY (OT_CODE) REFERENCES PER_ORG_TYPE (OT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG ADD CONSTRAINT FK3_PER_ORG FOREIGN KEY (OP_CODE) REFERENCES PER_ORG_PROVINCE (OP_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG ADD CONSTRAINT FK4_PER_ORG FOREIGN KEY (OS_CODE) REFERENCES PER_ORG_STAT (OS_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG ADD CONSTRAINT FK5_PER_ORG FOREIGN KEY (AP_CODE) REFERENCES PER_AMPHUR (AP_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG ADD CONSTRAINT FK6_PER_ORG FOREIGN KEY (PV_CODE) REFERENCES PER_PROVINCE (PV_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG ADD CONSTRAINT FK7_PER_ORG FOREIGN KEY (CT_CODE) REFERENCES PER_COUNTRY (CT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG ADD CONSTRAINT FK8_PER_ORG FOREIGN KEY (ORG_ID_REF) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG_ASS ADD CONSTRAINT FK1_PER_ORG_ASS FOREIGN KEY (OL_CODE) REFERENCES PER_ORG_LEVEL (OL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG_ASS ADD CONSTRAINT FK2_PER_ORG_ASS FOREIGN KEY (OT_CODE) REFERENCES	PER_ORG_TYPE (OT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG_ASS ADD CONSTRAINT FK3_PER_ORG_ASS FOREIGN KEY (OP_CODE) REFERENCES PER_ORG_PROVINCE (OP_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG_ASS ADD CONSTRAINT FK4_PER_ORG_ASS FOREIGN KEY (OS_CODE) REFERENCES PER_ORG_STAT (OS_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG_ASS ADD CONSTRAINT FK5_PER_ORG_ASS FOREIGN KEY (AP_CODE) REFERENCES PER_AMPHUR (AP_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG_ASS ADD CONSTRAINT FK6_PER_ORG_ASS FOREIGN KEY (PV_CODE) REFERENCES PER_PROVINCE (PV_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG_ASS ADD CONSTRAINT FK7_PER_ORG_ASS FOREIGN KEY (CT_CODE) REFERENCES PER_COUNTRY (CT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG_ASS ADD CONSTRAINT FK8_PER_ORG_ASS FOREIGN KEY (ORG_ID_REF) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG_JOB ADD CONSTRAINT FK1_PER_ORG_JOB FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_ORG_JOB ADD CONSTRAINT FK2_PER_ORG_JOB FOREIGN KEY (ORG_ID) REFERENCES PER_ORG_ASS (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK1_PER_PERSONAL FOREIGN KEY (OT_CODE) REFERENCES PER_OFF_TYPE (OT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK2_PER_PERSONAL FOREIGN KEY (PN_CODE) REFERENCES PER_PRENAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK3_PER_PERSONAL FOREIGN KEY (ORG_ID) REFERENCES PER_ORG_ASS (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK4_PER_PERSONAL FOREIGN KEY (POS_ID) REFERENCES PER_POSITION (POS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK5_PER_PERSONAL FOREIGN KEY (POEM_ID) REFERENCES PER_POS_EMP (POEM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK6_PER_PERSONAL FOREIGN KEY (LEVEL_NO) REFERENCES PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK7_PER_PERSONAL FOREIGN KEY (MR_CODE) REFERENCES PER_MARRIED (MR_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK8_PER_PERSONAL FOREIGN KEY (RE_CODE) REFERENCES PER_RELIGION (RE_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK9_PER_PERSONAL FOREIGN KEY (PN_CODE_F) REFERENCES PER_PRENAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK10_PER_PERSONAL FOREIGN KEY (PN_CODE_M) REFERENCES PER_PRENAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK11_PER_PERSONAL FOREIGN KEY (PV_CODE) REFERENCES PER_PROVINCE (PV_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK12_PER_PERSONAL FOREIGN KEY (MOV_CODE) REFERENCES PER_MOVMENT (MOV_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_EMP ADD CONSTRAINT FK1_PER_POS_EMP FOREIGN KEY (ORG_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_EMP ADD CONSTRAINT FK2_PER_POS_EMP FOREIGN KEY (ORG_ID_1) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_EMP ADD CONSTRAINT FK3_PER_POS_EMP FOREIGN KEY (ORG_ID_2) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_EMP ADD CONSTRAINT FK4_PER_POS_EMP FOREIGN KEY (PN_CODE) REFERENCES PER_POS_NAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK1_PER_POS_MOVE FOREIGN KEY (POS_ID) REFERENCES PER_POSITION (POS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK2_PER_POS_MOVE FOREIGN KEY (ORG_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK3_PER_POS_MOVE FOREIGN KEY (OT_CODE) REFERENCES PER_OFF_TYPE (OT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK4_PER_POS_MOVE FOREIGN KEY (ORG_ID_1) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK5_PER_POS_MOVE FOREIGN KEY (ORG_ID_2) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK6_PER_POS_MOVE FOREIGN KEY (PM_CODE) REFERENCES PER_MGT (PM_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK7_PER_POS_MOVE FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK8_PER_POS_MOVE FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK9_PER_POS_MOVE FOREIGN KEY (SKILL_CODE) REFERENCES PER_SKILL (SKILL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK10_PER_POS_MOVE FOREIGN KEY (PT_CODE) REFERENCES PER_TYPE (PT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK11_PER_POS_MOVE FOREIGN KEY (PC_CODE) REFERENCES PER_CONDITION (PC_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POS_NAME ADD CONSTRAINT FK1_PER_POS_NAME FOREIGN KEY (PG_CODE) REFERENCES PER_POS_GROUP (PG_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK1_PER_POSITION FOREIGN KEY (ORG_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK2_PER_POSITION FOREIGN KEY (OT_CODE) REFERENCES PER_OFF_TYPE (OT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK3_PER_POSITION FOREIGN KEY (ORG_ID_1) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK4_PER_POSITION FOREIGN KEY (ORG_ID_2) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK5_PER_POSITION FOREIGN KEY (PM_CODE) REFERENCES	PER_MGT (PM_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK6_PER_POSITION FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK7_PER_POSITION FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK8_PER_POSITION FOREIGN KEY (SKILL_CODE) REFERENCES PER_SKILL (SKILL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK9_PER_POSITION FOREIGN KEY (PT_CODE) REFERENCES PER_TYPE (PT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK10_PER_POSITION FOREIGN KEY (PC_CODE) REFERENCES PER_CONDITION (PC_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK1_PER_POSITIONHIS FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK2_PER_POSITIONHIS FOREIGN KEY (MOV_CODE) 
							  REFERENCES PER_MOVMENT (MOV_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK3_PER_POSITIONHIS FOREIGN KEY (PM_CODE) REFERENCES PER_MGT (PM_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK4_PER_POSITIONHIS FOREIGN KEY (LEVEL_NO) REFERENCES	PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK5_PER_POSITIONHIS FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK6_PER_POSITIONHIS FOREIGN KEY (PN_CODE) REFERENCES PER_POS_NAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK7_PER_POSITIONHIS FOREIGN KEY (PT_CODE) REFERENCES PER_TYPE (PT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK8_PER_POSITIONHIS FOREIGN KEY (CT_CODE) REFERENCES PER_COUNTRY (CT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK9_PER_POSITIONHIS FOREIGN KEY (PV_CODE) REFERENCES PER_PROVINCE (PV_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK10_PER_POSITIONHIS FOREIGN KEY (AP_CODE) REFERENCES PER_AMPHUR (AP_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK11_PER_POSITIONHIS FOREIGN KEY (ORG_ID_1) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK12_PER_POSITIONHIS FOREIGN KEY (ORG_ID_2) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK13_PER_POSITIONHIS FOREIGN KEY (ORG_ID_3) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_PROMOTE_C ADD CONSTRAINT FK1_PER_PROMOTE_C FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_PROMOTE_E ADD CONSTRAINT FK1_PER_PROMOTE_E FOREIGN KEY (POEM_ID) REFERENCES PER_POS_EMP (POEM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_PROMOTE_E ADD CONSTRAINT FK2_PER_PROMOTE_E FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_PROMOTE_P ADD CONSTRAINT FK1_PER_PROMOTE_P FOREIGN KEY (POS_ID) REFERENCES PER_POSITION (POS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_PROMOTE_P ADD CONSTRAINT FK2_PER_PROMOTE_P FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_PROVINCE ADD CONSTRAINT FK1_PER_PROVINCE FOREIGN KEY (CT_CODE) REFERENCES PER_COUNTRY (CT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_PUNISHMENT ADD CONSTRAINT FK1_PER_PUNISHMENT FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_PUNISHMENT ADD CONSTRAINT FK2_PER_PUNISHMENT FOREIGN KEY (CRD_CODE) 
							  REFERENCES PER_CRIME_DTL (CRD_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_PUNISHMENT ADD CONSTRAINT FK3_PER_PUNISHMENT FOREIGN KEY (PEN_CODE) 
							  REFERENCES PER_PENALTY (PEN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK1_PER_REQ1_DTL1 FOREIGN KEY (REQ_ID) REFERENCES PER_REQ1 (REQ_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK2_PER_REQ1_DTL1 FOREIGN KEY (ORG_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK3_PER_REQ1_DTL1 FOREIGN KEY (OT_CODE) REFERENCES PER_OFF_TYPE (OT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK4_PER_REQ1_DTL1 FOREIGN KEY (ORG_ID_1) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK5_PER_REQ1_DTL1 FOREIGN KEY (ORG_ID_2) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK6_PER_REQ1_DTL1 FOREIGN KEY (PM_CODE) REFERENCES PER_MGT (PM_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK7_PER_REQ1_DTL1 FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK8_PER_REQ1_DTL1 FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK9_PER_REQ1_DTL1 FOREIGN KEY (SKILL_CODE) REFERENCES PER_SKILL (SKILL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK10_PER_REQ1_DTL1 FOREIGN KEY (PT_CODE) REFERENCES PER_TYPE (PT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK11_PER_REQ1_DTL1 FOREIGN KEY (PC_CODE) REFERENCES PER_CONDITION (PC_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL2 ADD CONSTRAINT FK1_PER_REQ1_DTL2 FOREIGN KEY (REQ_ID, REQ_SEQ) 
							  REFERENCES PER_REQ1_DTL1 (REQ_ID, REQ_SEQ) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL2 ADD CONSTRAINT FK2_PER_REQ1_DTL2 FOREIGN KEY (POS_ID) REFERENCES PER_POSITION (POS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK1_PER_REQ2_DTL FOREIGN KEY (REQ_ID) REFERENCES PER_REQ2 (REQ_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK2_PER_REQ2_DTL FOREIGN KEY (POS_ID_RETIRE) REFERENCES PER_POSITION (POS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK3_PER_REQ2_DTL FOREIGN KEY (POS_ID_DROP) REFERENCES PER_POSITION (POS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK4_PER_REQ2_DTL FOREIGN KEY (POS_ID_REQ) REFERENCES PER_POSITION (POS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK5_PER_REQ2_DTL FOREIGN KEY (ORG_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK6_PER_REQ2_DTL FOREIGN KEY (OT_CODE) REFERENCES PER_OFF_TYPE (OT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK7_PER_REQ2_DTL FOREIGN KEY (ORG_ID_1) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK8_PER_REQ2_DTL FOREIGN KEY (ORG_ID_2) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK9_PER_REQ2_DTL FOREIGN KEY (PM_CODE) REFERENCES PER_MGT (PM_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK10_PER_REQ2_DTL FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK11_PER_REQ2_DTL FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK12_PER_REQ2_DTL FOREIGN KEY (SKILL_CODE) REFERENCES PER_SKILL (SKILL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK13_PER_REQ2_DTL FOREIGN KEY (PT_CODE) REFERENCES PER_TYPE (PT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK14_PER_REQ2_DTL FOREIGN KEY (PC_CODE) REFERENCES PER_CONDITION (PC_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK1_PER_REQ3_DTL FOREIGN KEY (REQ_ID) REFERENCES PER_REQ3 (REQ_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK2_PER_REQ3_DTL FOREIGN KEY (POS_ID) REFERENCES PER_POSITION (POS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK3_PER_REQ3_DTL FOREIGN KEY (ORG_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK4_PER_REQ3_DTL FOREIGN KEY (OT_CODE) REFERENCES PER_OFF_TYPE (OT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK5_PER_REQ3_DTL FOREIGN KEY (ORG_ID_1) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK6_PER_REQ3_DTL FOREIGN KEY (ORG_ID_2) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK7_PER_REQ3_DTL FOREIGN KEY (PM_CODE) REFERENCES PER_MGT (PM_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK8_PER_REQ3_DTL FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK9_PER_REQ3_DTL FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK10_PER_REQ3_DTL FOREIGN KEY (SKILL_CODE) REFERENCES PER_SKILL (SKILL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK11_PER_REQ3_DTL FOREIGN KEY (PT_CODE) REFERENCES PER_TYPE (PT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK12_PER_REQ3_DTL FOREIGN KEY (PC_CODE) REFERENCES PER_CONDITION (PC_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REWARDHIS ADD CONSTRAINT FK1_PER_REWARDHIS FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REWARDHIS ADD CONSTRAINT FK2_PER_REWARDHIS FOREIGN KEY (REW_CODE) REFERENCES PER_REWARD (REW_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALARYHIS ADD CONSTRAINT FK1_PER_SALARYHIS FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALARYHIS ADD CONSTRAINT FK2_PER_SALARYHIS FOREIGN KEY (MOV_CODE) REFERENCES PER_MOVMENT (MOV_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALPROMOTE ADD CONSTRAINT FK1_PER_SALPROMOTE FOREIGN KEY (SALQ_YEAR, SALQ_TYPE) 
							  REFERENCES PER_SALQUOTA (SALQ_YEAR, SALQ_TYPE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SALPROMOTE ADD CONSTRAINT FK2_PER_SALPROMOTE FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SALQUOTADTL1 ADD CONSTRAINT FK1_PER_SALQUOTADTL1 FOREIGN KEY (SALQ_YEAR, SALQ_TYPE) 
							  REFERENCES PER_SALQUOTA (SALQ_YEAR, SALQ_TYPE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SALQUOTADTL1 ADD CONSTRAINT FK2_PER_SALQUOTADTL1 FOREIGN KEY (ORG_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SALQUOTADTL2 ADD CONSTRAINT FK1_PER_SALQUOTADTL2 FOREIGN KEY (SALQ_YEAR, SALQ_TYPE) 
							  REFERENCES PER_SALQUOTA (SALQ_YEAR, SALQ_TYPE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SALQUOTADTL2 ADD CONSTRAINT FK2_PER_SALQUOTADTL2 FOREIGN KEY (ORG_ID) REFERENCES PER_ORG_ASS (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SCHOLAR ADD CONSTRAINT FK1_PER_SCHOLAR FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SCHOLAR ADD CONSTRAINT FK2_PER_SCHOLAR FOREIGN KEY (PN_CODE) REFERENCES PER_PRENAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SCHOLAR ADD CONSTRAINT FK3_PER_SCHOLAR FOREIGN KEY (EN_CODE) REFERENCES PER_EDUCNAME (EN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SCHOLAR ADD CONSTRAINT FK4_PER_SCHOLAR FOREIGN KEY (EM_CODE) REFERENCES PER_EDUCMAJOR (EM_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SCHOLAR ADD CONSTRAINT FK5_PER_SCHOLAR FOREIGN KEY (SCH_CODE) REFERENCES PER_SCHOLARSHIP (SCH_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SCHOLAR ADD CONSTRAINT FK6_PER_SCHOLAR FOREIGN KEY (INS_CODE) REFERENCES PER_INSTITUTE (INS_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SCHOLAR ADD CONSTRAINT FK7_PER_SCHOLAR FOREIGN KEY (EL_CODE) REFERENCES PER_EDUCLEVEL (EL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SCHOLARINC ADD CONSTRAINT FK1_PER_SCHOLARINC FOREIGN KEY (SC_ID) REFERENCES PER_SCHOLAR (SC_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD CONSTRAINT FK1_PER_SCHOLARSHIP FOREIGN KEY (ST_CODE) 
							  REFERENCES PER_SCHOLARTYPE (ST_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SERVICEHIS ADD CONSTRAINT FK1_PER_SERVICEHIS FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SERVICEHIS ADD CONSTRAINT FK2_PER_SERVICEHIS FOREIGN KEY (SV_CODE) REFERENCES PER_SERVICE (SV_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SERVICEHIS ADD CONSTRAINT FK3_PER_SERVICEHIS FOREIGN KEY (SRT_CODE) 
							  REFERENCES PER_SERVICETITLE (SRT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SERVICEHIS ADD CONSTRAINT FK4_PER_SERVICEHIS FOREIGN KEY (ORG_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SERVICEHIS ADD CONSTRAINT FK5_PER_SERVICEHIS FOREIGN KEY (PER_ID_ASSIGN) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SERVICEHIS ADD CONSTRAINT FK6_PER_SERVICEHIS FOREIGN KEY (ORG_ID_ASSIGN) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SKILL ADD CONSTRAINT FK1_PER_SKILL FOREIGN KEY (SG_CODE) REFERENCES PER_SKILL_GROUP (SG_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SKILL_GROUP ADD CONSTRAINT FK1_PER_SKILL_GROUP FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SUM ADD CONSTRAINT FK1_PER_SUM FOREIGN KEY (ORG_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL1 ADD CONSTRAINT FK1_PER_SUM_DTL1 FOREIGN KEY (SUM_ID) REFERENCES PER_SUM (SUM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL1 ADD CONSTRAINT FK2_PER_SUM_DTL1 FOREIGN KEY (OS_CODE) REFERENCES PER_ORG_STAT (OS_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL1 ADD CONSTRAINT FK3_PER_SUM_DTL1 FOREIGN KEY (OT_CODE) REFERENCES PER_ORG_TYPE (OT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL2 ADD CONSTRAINT FK1_PER_SUM_DTL2 FOREIGN KEY (SUM_ID) REFERENCES PER_SUM (SUM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL2 ADD CONSTRAINT FK2_PER_SUM_DTL2 FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL2 ADD CONSTRAINT FK3_PER_SUM_DTL2 FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL2 ADD CONSTRAINT FK4_PER_SUM_DTL2 FOREIGN KEY (PT_CODE) REFERENCES PER_TYPE (PT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL3 ADD CONSTRAINT FK1_PER_SUM_DTL3 FOREIGN KEY (SUM_ID) REFERENCES PER_SUM (SUM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL3 ADD CONSTRAINT FK2_PER_SUM_DTL3 FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL4 ADD CONSTRAINT FK1_PER_SUM_DTL4 FOREIGN KEY (SUM_ID) REFERENCES PER_SUM (SUM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL4 ADD CONSTRAINT FK2_PER_SUM_DTL4 FOREIGN KEY (PS_CODE) REFERENCES PER_STATUS (PS_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL4 ADD CONSTRAINT FK3_PER_SUM_DTL4 FOREIGN KEY (PM_CODE) REFERENCES PER_MGT (PM_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL4 ADD CONSTRAINT FK4_PER_SUM_DTL4 FOREIGN KEY (LEVEL_NO) REFERENCES PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL5 ADD CONSTRAINT FK1_PER_SUM_DTL5 FOREIGN KEY (SUM_ID) REFERENCES PER_SUM (SUM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL5 ADD CONSTRAINT FK2_PER_SUM_DTL5 FOREIGN KEY (OP_CODE) REFERENCES PER_ORG_PROVINCE (OP_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL5 ADD CONSTRAINT FK3_PER_SUM_DTL5 FOREIGN KEY (PM_CODE) REFERENCES PER_MGT (PM_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL5 ADD CONSTRAINT FK4_PER_SUM_DTL5 FOREIGN KEY (LEVEL_NO) REFERENCES PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL6 ADD CONSTRAINT FK1_PER_SUM_DTL6 FOREIGN KEY (SUM_ID) REFERENCES PER_SUM (SUM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL6 ADD CONSTRAINT FK2_PER_SUM_DTL6 FOREIGN KEY (OT_CODE) REFERENCES PER_ORG_TYPE (OT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL7 ADD CONSTRAINT FK1_PER_SUM_DTL7 FOREIGN KEY (SUM_ID) REFERENCES PER_SUM (SUM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL7 ADD CONSTRAINT FK2_PER_SUM_DTL7 FOREIGN KEY (LEVEL_NO) REFERENCES PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL8 ADD CONSTRAINT FK1_PER_SUM_DTL8 FOREIGN KEY (SUM_ID) REFERENCES PER_SUM (SUM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL8 ADD CONSTRAINT FK2_PER_SUM_DTL8 FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL8 ADD CONSTRAINT FK3_PER_SUM_DTL8 FOREIGN KEY (LEVEL_NO) REFERENCES PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL9 ADD CONSTRAINT FK1_PER_SUM_DTL9 FOREIGN KEY (SUM_ID) REFERENCES PER_SUM (SUM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL9 ADD CONSTRAINT FK2_PER_SUM_DTL9 FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_SUM_DTL9 ADD CONSTRAINT FK3_PER_SUM_DTL9 FOREIGN KEY (EL_CODE) REFERENCES PER_EDUCLEVEL (EL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " ALTER TABLE PER_TIMEHIS ADD CONSTRAINT FK1_PER_TIMEHIS FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TIMEHIS ADD CONSTRAINT FK2_PER_TIMEHIS FOREIGN KEY (TIME_CODE) REFERENCES PER_TIME (TIME_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRAINING ADD CONSTRAINT FK1_PER_TRAINING FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRAINING ADD CONSTRAINT FK2_PER_TRAINING FOREIGN KEY (TR_CODE) REFERENCES PER_TRAIN (TR_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRAINING ADD CONSTRAINT FK3_PER_TRAINING FOREIGN KEY (CT_CODE) REFERENCES PER_COUNTRY (CT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRAINING ADD CONSTRAINT FK4_PER_TRAINING FOREIGN KEY (CT_CODE_FUND) REFERENCES PER_COUNTRY (CT_CODE) ";
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

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK5_PER_TRANSFER_REQ FOREIGN KEY (PL_CODE_1) REFERENCES PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK6_PER_TRANSFER_REQ FOREIGN KEY (PN_CODE_1) 
							  REFERENCES PER_POS_NAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK7_PER_TRANSFER_REQ FOREIGN KEY (ORG_ID_1) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK8_PER_TRANSFER_REQ FOREIGN KEY (LEVEL_NO_1) 
							  REFERENCES PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK9_PER_TRANSFER_REQ FOREIGN KEY (PL_CODE_2) REFERENCES PER_LINE (PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK10_PER_TRANSFER_REQ FOREIGN KEY (PN_CODE_2) 
							  REFERENCES PER_POS_NAME (PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK11_PER_TRANSFER_REQ FOREIGN KEY (ORG_ID_2) REFERENCES PER_ORG (ORG_ID) ";
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

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK15_PER_TRANSFER_REQ FOREIGN KEY (ORG_ID_3) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK16_PER_TRANSFER_REQ FOREIGN KEY (LEVEL_NO_3) 
							  REFERENCES PER_LEVEL (LEVEL_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

		} // end if odbc

		if($DPISDB=="odbc")  
			$cmd = " ALTER TABLE  PER_PERSONAL DROP HIP_FLAG ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE  PER_PERSONAL DROP (HIP_FLAG) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE  PER_PERSONAL DROP HIP_FLAG ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		if($DPISDB=="odbc") { 
			$cmd = " ALTER TABLE  PER_CONTROL ALTER ORG_ID INTEGER NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE  PER_POSITION DROP CONSTRAINT INXU1_PER_POSITION ";
		elseif($DPISDB=="oci8") 
			$cmd = " DROP INDEX INXU1_PER_POSITION ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE  PER_POSITION DROP CONSTRAINT INXU1_PER_POSITION ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE  PER_PERSONAL DROP CONSTRAINT INXU1_PERSONAL ";
		elseif($DPISDB=="oci8") 
			$cmd = " DROP INDEX INXU1_PERSONAL ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE  PER_PERSONAL DROP CONSTRAINT INXU1_PERSONAL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE  PER_PERSONAL DROP CONSTRAINT INXU2_PERSONAL ";
		elseif($DPISDB=="oci8") 
			$cmd = " DROP INDEX INXU2_PERSONAL ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE  PER_PERSONAL DROP CONSTRAINT INXU2_PERSONAL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE  PER_PERSONAL DROP CONSTRAINT INXU3_PERSONAL ";
		elseif($DPISDB=="oci8") 
			$cmd = " DROP INDEX INXU3_PERSONAL ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE  PER_PERSONAL DROP CONSTRAINT INXU3_PERSONAL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE  PER_POS_EMP DROP CONSTRAINT INXU1_PER_POS_EMP ";
		elseif($DPISDB=="oci8") 
			$cmd = " DROP INDEX INXU1_PER_POS_EMP ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE  PER_POS_EMP DROP CONSTRAINT INXU1_PER_POS_EMP ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE  PER_POS_EMPSER DROP CONSTRAINT INXU1_PER_POS_EMPSER ";
		elseif($DPISDB=="oci8") 
			$cmd = " DROP INDEX INXU1_PER_POS_EMPSER ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE  PER_POS_EMPSER DROP CONSTRAINT INXU1_PER_POS_EMPSER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE  PER_ORG_TYPE DROP CONSTRAINT INXU1_PER_ORG_TYPE ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE INDEX INX_PER_POSITION ON PER_POSITION (POS_NO) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE  PER_ORG_TYPE DROP CONSTRAINT INXU1_PER_ORG_TYPE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD DEPARTMENT_ID INTEGER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD DEPARTMENT_ID NUMBER(10) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PERSONAL ADD DEPARTMENT_ID INTEGER(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_PERSONAL SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} else {
			if($DPISDB=="odbc" || $DPISDB=="mysql"){ 
				$cmd = " SELECT PER_ID, PER_TYPE, POS_ID, POEM_ID, POEMS_ID FROM PER_PERSONAL WHERE PER_STATUS <> 3 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$PER_ID = $data[PER_ID];
					$PER_TYPE = $data[PER_TYPE];
					$POS_ID = $data[POS_ID];
					$POEM_ID = $data[POEM_ID];
					$POEMS_ID = $data[POEMS_ID];
					if ($PER_TYPE==1) {
						$cmd = " SELECT ORG_ID FROM PER_POSITION WHERE POS_ID = $POS_ID ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
						$data1 = $db_dpis1->get_array();
						$ORG_ID = $data1[ORG_ID];
						$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
						$data1 = $db_dpis1->get_array();
						$DEPARTMENT_ID = $data1[ORG_ID_REF];
					} elseif ($PER_TYPE==2) {
						$cmd = " SELECT DEPARTMENT_ID FROM PER_POS_EMP WHERE POEM_ID = $POEM_ID ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
						$data1 = $db_dpis1->get_array();
						$DEPARTMENT_ID = $data1[DEPARTMENT_ID];
					} elseif ($PER_TYPE==3) {
						$cmd = " SELECT DEPARTMENT_ID FROM PER_POS_EMPSER WHERE POEMS_ID = $POEMS_ID ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
						$data1 = $db_dpis1->get_array();
						$DEPARTMENT_ID = $data1[DEPARTMENT_ID];
					}
					$cmd = " UPDATE PER_PERSONAL SET DEPARTMENT_ID = $DEPARTMENT_ID WHERE PER_ID = $PER_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while						
			} elseif($DPISDB=="oci8"){
				$cmd = " UPDATE PER_PERSONAL X SET X.DEPARTMENT_ID = (SELECT D.ORG_ID_REF FROM PER_PERSONAL A, PER_POSITION B, PER_ORG C, 
								  PER_ORG D WHERE A.PER_ID = X.PER_ID AND A.POS_ID = B.POS_ID AND B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) WHERE PER_TYPE = 1 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_PERSONAL X SET X.DEPARTMENT_ID = (SELECT D.ORG_ID_REF FROM PER_PERSONAL A, PER_POS_EMP B, PER_ORG C, 
								  PER_ORG D WHERE A.PER_ID = X.PER_ID AND A.POEM_ID = B.POEM_ID AND B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) 
								  WHERE PER_TYPE = 2 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				
				$cmd = " UPDATE PER_PERSONAL X SET X.DEPARTMENT_ID = (SELECT D.ORG_ID_REF FROM PER_PERSONAL A, PER_POS_EMPSER B, PER_ORG C, 
								  PER_ORG D WHERE A.PER_ID = X.PER_ID AND A.POEMS_ID = B.POEMS_ID AND B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) 
								  WHERE PER_TYPE = 3 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD APPROVE_PER_ID INTEGER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD APPROVE_PER_ID NUMBER(10) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PERSONAL ADD APPROVE_PER_ID INTEGER(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD REPLACE_PER_ID INTEGER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD REPLACE_PER_ID NUMBER(10) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PERSONAL ADD REPLACE_PER_ID INTEGER(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD ABSENT_FLAG SINGLE Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD ABSENT_FLAG NUMBER(1) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PERSONAL ADD ABSENT_FLAG SMALLINT(1) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ORG_JOB ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ORG_JOB ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_ORG_JOB ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_ORG_JOB, PER_PERSONAL SET PER_ORG_JOB.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_ORG_JOB.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_ORG_JOB A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_ORG_JOB, PER_PERSONAL SET PER_ORG_JOB.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_ORG_JOB.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD EP_CODE VARCHAR(10) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD EP_CODE VARCHAR2(10) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD EP_CODE VARCHAR(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_POSITIONHIS, PER_PERSONAL SET PER_POSITIONHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE 
			PER_PERSONAL.PER_ID = PER_POSITIONHIS.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_POSITIONHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_POSITIONHIS, PER_PERSONAL SET PER_POSITIONHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO WHERE 
			PER_PERSONAL.PER_ID = PER_POSITIONHIS.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SALARYHIS ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SALARYHIS ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_SALARYHIS ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_SALARYHIS, PER_PERSONAL SET PER_SALARYHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_SALARYHIS.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_SALARYHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_SALARYHIS, PER_PERSONAL SET PER_SALARYHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_SALARYHIS.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_EXTRAHIS ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_EXTRAHIS ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_EXTRAHIS ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_EXTRAHIS, PER_PERSONAL SET PER_EXTRAHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_EXTRAHIS.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_EXTRAHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_EXTRAHIS, PER_PERSONAL SET PER_EXTRAHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_EXTRAHIS.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_EXTRA_INCOMEHIS, PER_PERSONAL SET PER_EXTRA_INCOMEHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_EXTRA_INCOMEHIS.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_EXTRA_INCOMEHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_EXTRA_INCOMEHIS, PER_PERSONAL SET PER_EXTRA_INCOMEHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_EXTRA_INCOMEHIS.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_EDUCATE ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_EDUCATE ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_EDUCATE ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_EDUCATE, PER_PERSONAL SET PER_EDUCATE.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_EDUCATE.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_EDUCATE A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_EDUCATE, PER_PERSONAL SET PER_EDUCATE.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_EDUCATE.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_TRAINING ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_TRAINING ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_TRAINING ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_TRAINING, PER_PERSONAL SET PER_TRAINING.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_TRAINING.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_TRAINING A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_TRAINING, PER_PERSONAL SET PER_TRAINING.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_TRAINING.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ABILITY ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ABILITY ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_ABILITY ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_ABILITY, PER_PERSONAL SET PER_ABILITY.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_ABILITY.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_ABILITY A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_ABILITY, PER_PERSONAL SET PER_ABILITY.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_ABILITY.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_HEIR ADD PER_CARDNO	VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_HEIR ADD PER_CARDNO	VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_HEIR ADD PER_CARDNO	VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_HEIR, PER_PERSONAL SET PER_HEIR.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_HEIR.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_HEIR A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_HEIR, PER_PERSONAL SET PER_HEIR.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_HEIR.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ABSENTHIS ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ABSENTHIS ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_ABSENTHIS ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_ABSENTHIS, PER_PERSONAL SET PER_ABSENTHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_ABSENTHIS.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_ABSENTHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_ABSENTHIS, PER_PERSONAL SET PER_ABSENTHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_ABSENTHIS.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PUNISHMENT ADD PER_CARDNO	VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PUNISHMENT ADD PER_CARDNO	VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PUNISHMENT ADD PER_CARDNO	VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_PUNISHMENT, PER_PERSONAL SET PER_PUNISHMENT.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_PUNISHMENT.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_PUNISHMENT A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_PUNISHMENT, PER_PERSONAL SET PER_PUNISHMENT.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_PUNISHMENT.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SERVICEHIS ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SERVICEHIS ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_SERVICEHIS ADD PER_CARDNO VARCHAR(13) Null ";
		
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_SERVICEHIS, PER_PERSONAL SET PER_SERVICEHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_SERVICEHIS.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_SERVICEHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_SERVICEHIS, PER_PERSONAL SET PER_SERVICEHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_SERVICEHIS.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_REWARDHIS ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_REWARDHIS ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_REWARDHIS ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_REWARDHIS, PER_PERSONAL SET PER_REWARDHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_REWARDHIS.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_REWARDHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_REWARDHIS, PER_PERSONAL SET PER_REWARDHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_REWARDHIS.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_MARRHIS ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_MARRHIS ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_MARRHIS ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_MARRHIS, PER_PERSONAL SET PER_MARRHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_MARRHIS.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_MARRHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_MARRHIS, PER_PERSONAL SET PER_MARRHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_MARRHIS.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_NAMEHIS ADD PER_CARDNO	VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_NAMEHIS ADD PER_CARDNO	VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_NAMEHIS ADD PER_CARDNO	VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_NAMEHIS, PER_PERSONAL SET PER_NAMEHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_NAMEHIS.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_NAMEHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_NAMEHIS, PER_PERSONAL SET PER_NAMEHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_NAMEHIS.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_DECORATEHIS ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_DECORATEHIS ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_DECORATEHIS ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_GAZETTE VARCHAR(255) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_GAZETTE VARCHAR2(255) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_GAZETTE VARCHAR(255) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_DECORATEHIS, PER_PERSONAL SET PER_DECORATEHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_DECORATEHIS.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_DECORATEHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_DECORATEHIS, PER_PERSONAL SET PER_DECORATEHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_DECORATEHIS.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_TIMEHIS ADD PER_CARDNO	VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_TIMEHIS ADD PER_CARDNO	VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_TIMEHIS ADD PER_CARDNO	VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_TIMEHIS, PER_PERSONAL SET PER_TIMEHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_TIMEHIS.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_TIMEHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_TIMEHIS, PER_PERSONAL SET PER_TIMEHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_TIMEHIS.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONALPIC ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONALPIC ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PERSONALPIC ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONALPIC ADD PER_PICPATH VARCHAR(255) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONALPIC ADD PER_PICPATH VARCHAR2(255) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PERSONALPIC ADD PER_PICPATH VARCHAR(255) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_PERSONALPIC, PER_PERSONAL SET PER_PERSONALPIC.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_PERSONALPIC.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_PERSONALPIC A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_PERSONALPIC, PER_PERSONAL SET PER_PERSONALPIC.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_PERSONALPIC.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_COMDTL ADD PER_CARDNO	VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_COMDTL ADD PER_CARDNO	VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_COMDTL ADD PER_CARDNO	VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_COMDTL ADD EP_CODE VARCHAR(10) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_COMDTL ADD EP_CODE VARCHAR2(10) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_COMDTL ADD EP_CODE VARCHAR(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_COMDTL ADD POEMS_ID INTEGER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_COMDTL ADD POEMS_ID NUMBER(10) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_COMDTL ADD POEMS_ID INTEGER(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_COMDTL ADD EP_CODE_ASSIGN VARCHAR(10) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_COMDTL ADD EP_CODE_ASSIGN VARCHAR2(10) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_COMDTL ADD EP_CODE_ASSIGN VARCHAR(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_COMDTL, PER_PERSONAL SET PER_COMDTL.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_COMDTL.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_COMDTL A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_COMDTL, PER_PERSONAL SET PER_COMDTL.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_COMDTL.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_MOVE_REQ ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_MOVE_REQ ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_MOVE_REQ ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_MOVE_REQ, PER_PERSONAL SET PER_MOVE_REQ.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_MOVE_REQ.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_MOVE_REQ A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_MOVE_REQ, PER_PERSONAL SET PER_MOVE_REQ.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_MOVE_REQ.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PROMOTE_C ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PROMOTE_C ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PROMOTE_C ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_PROMOTE_C, PER_PERSONAL SET PER_PROMOTE_C.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_PROMOTE_C.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_PROMOTE_C A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_PROMOTE_C, PER_PERSONAL SET PER_PROMOTE_C.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_PROMOTE_C.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PROMOTE_P ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PROMOTE_P ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PROMOTE_P ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_PROMOTE_P, PER_PERSONAL SET PER_PROMOTE_P.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_PROMOTE_P.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_PROMOTE_P A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_PROMOTE_P, PER_PERSONAL SET PER_PROMOTE_P.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_PROMOTE_P.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PROMOTE_E ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PROMOTE_E ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PROMOTE_E ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_PROMOTE_E, PER_PERSONAL SET PER_PROMOTE_E.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_PROMOTE_E.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_PROMOTE_E A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_PROMOTE_E, PER_PERSONAL SET PER_PROMOTE_E.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_PROMOTE_E.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SALPROMOTE ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SALPROMOTE ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_SALPROMOTE ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_SALPROMOTE, PER_PERSONAL SET PER_SALPROMOTE.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_SALPROMOTE.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_SALPROMOTE A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_SALPROMOTE, PER_PERSONAL SET PER_SALPROMOTE.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_SALPROMOTE.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_BONUSPROMOTE ADD PER_CARDNO	VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_BONUSPROMOTE ADD PER_CARDNO	VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_BONUSPROMOTE ADD PER_CARDNO	VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_BONUSPROMOTE, PER_PERSONAL SET PER_BONUSPROMOTE.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_BONUSPROMOTE.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_BONUSPROMOTE A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_BONUSPROMOTE, PER_PERSONAL SET PER_BONUSPROMOTE.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_BONUSPROMOTE.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ABSENT ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ABSENT ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_ABSENT ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ABSENT ADD APPROVE_FLAG SINGLE Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ABSENT ADD APPROVE_FLAG NUMBER(1) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_ABSENT ADD APPROVE_FLAG SMALLINT(1) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ABSENT ADD APPROVE_PER_ID INTEGER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ABSENT ADD APPROVE_PER_ID NUMBER(10) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_ABSENT ADD APPROVE_PER_ID INTEGER(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_ABSENT, PER_PERSONAL SET PER_ABSENT.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_ABSENT.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_ABSENT A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_ABSENT, PER_PERSONAL SET PER_ABSENT.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_ABSENT.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_INVEST1DTL ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_INVEST1DTL ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_INVEST1DTL ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_INVEST1DTL, PER_PERSONAL SET PER_INVEST1DTL.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_INVEST1DTL.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_INVEST1DTL A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_INVEST1DTL, PER_PERSONAL SET PER_INVEST1DTL.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_INVEST1DTL.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_INVEST2DTL ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_INVEST2DTL ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_INVEST2DTL ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_INVEST2DTL, PER_PERSONAL SET PER_INVEST2DTL.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_INVEST2DTL.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_INVEST2DTL A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_INVEST2DTL, PER_PERSONAL SET PER_INVEST2DTL.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_INVEST2DTL.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SCHOLAR ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SCHOLAR ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_SCHOLAR ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_SCHOLAR, PER_PERSONAL SET PER_SCHOLAR.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_SCHOLAR.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_SCHOLAR A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_SCHOLAR, PER_PERSONAL SET PER_SCHOLAR.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_SCHOLAR.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_COURSEDTL ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_COURSEDTL ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_COURSEDTL ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_COURSEDTL, PER_PERSONAL SET PER_COURSEDTL.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_COURSEDTL.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_COURSEDTL A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_COURSEDTL, PER_PERSONAL SET PER_COURSEDTL.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_COURSEDTL.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_DECORDTL ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_DECORDTL ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_DECORDTL ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_DECORDTL, PER_PERSONAL SET PER_DECORDTL.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_DECORDTL.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_DECORDTL A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_DECORDTL, PER_PERSONAL SET PER_DECORDTL.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_DECORDTL.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LETTER ADD PER_CARDNO VARCHAR(13) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LETTER ADD PER_CARDNO VARCHAR2(13) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LETTER ADD PER_CARDNO VARCHAR(13) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LETTER ADD LET_ADDITIONAL VARCHAR(255) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LETTER ADD LET_ADDITIONAL VARCHAR2(255) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LETTER ADD LET_ADDITIONAL VARCHAR(255) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_LETTER, PER_PERSONAL SET PER_LETTER.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_LETTER.PER_ID ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_LETTER A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_LETTER, PER_PERSONAL SET PER_LETTER.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
							  WHERE PER_PERSONAL.PER_ID = PER_LETTER.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ABSENT ADD ABS_REASON	VARCHAR(255) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ABSENT ADD ABS_REASON	VARCHAR2(255) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_ABSENT ADD ABS_REASON	VARCHAR(255) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ABSENT ADD ABS_ADDRESS	VARCHAR(255) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ABSENT ADD ABS_ADDRESS	VARCHAR2(255) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_ABSENT ADD ABS_ADDRESS	VARCHAR(255) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_BLOOD(
			BL_CODE VARCHAR(10) NOT NULL,	
			BL_NAME VARCHAR(100) NOT NULL,
			BL_ACTIVE SINGLE NOT NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_BLOOD PRIMARY KEY (bl_code)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_BLOOD(
			BL_CODE VARCHAR2(10) NOT NULL,	
			BL_NAME VARCHAR2(100) NOT NULL,
			BL_ACTIVE NUMBER(1) NOT NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_BLOOD PRIMARY KEY (bl_code)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_BLOOD(
			BL_CODE VARCHAR(10) NOT NULL,	
			BL_NAME VARCHAR(100) NOT NULL,
			BL_ACTIVE SMALLINT(1) NOT NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_BLOOD PRIMARY KEY (bl_code)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_BLOOD (BL_CODE, BL_NAME, BL_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('A', 'กลุ่มเลือด A', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_BLOOD (BL_CODE, BL_NAME, BL_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('AB', 'กลุ่มเลือด AB', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_BLOOD (BL_CODE, BL_NAME, BL_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('B', 'กลุ่มเลือด B', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_BLOOD (BL_CODE, BL_NAME, BL_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('O', 'กลุ่มเลือด O', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_LAYER_NEW(
			LAYER_TYPE SINGLE NOT NULL,	
			LEVEL_NO VARCHAR(10) NOT NULL,
			LAYER_NO NUMBER NOT NULL,
			LAYER_SALARY NUMBER NOT NULL,	
			LAYER_ACTIVE SINGLE NOT NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			LAYER_SALARY_MIN NUMBER NULL,	
			LAYER_SALARY_MAX NUMBER NULL,	
			CONSTRAINT PK_PER_LAYER_NEW PRIMARY KEY (layer_type, level_no, layer_no)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_LAYER_NEW(
			LAYER_TYPE NUMBER(1) NOT NULL,	
			LEVEL_NO VARCHAR2(10) NOT NULL,
			LAYER_NO NUMBER(3,1) NOT NULL,
			LAYER_SALARY NUMBER(16,2) NOT NULL,	
			LAYER_ACTIVE NUMBER(1) NOT NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			LAYER_SALARY_MIN NUMBER(16,2) NULL,	
			LAYER_SALARY_MAX NUMBER(16,2) NULL,	
			CONSTRAINT PK_PER_LAYER_NEW PRIMARY KEY (layer_type, level_no, layer_no)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_LAYER_NEW(
			LAYER_TYPE SMALLINT(1) NOT NULL,	
			LEVEL_NO VARCHAR(10) NOT NULL,
			LAYER_NO DECIMAL(3,1) NOT NULL,
			LAYER_SALARY DECIMAL(16,2) NOT NULL,	
			LAYER_ACTIVE SMALLINT(1) NOT NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			LAYER_SALARY_MIN DECIMAL(16,2) NULL,	
			LAYER_SALARY_MAX DECIMAL(16,2) NULL,	
			CONSTRAINT PK_PER_LAYER_NEW PRIMARY KEY (layer_type, level_no, layer_no)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_LAYEREMP_NEW(
			PG_CODE VARCHAR(10) NOT NULL,
			LAYERE_NO NUMBER NOT NULL,
			LAYERE_SALARY NUMBER NOT NULL,	
			LAYERE_DAY NUMBER NOT NULL,	
			LAYERE_HOUR NUMBER NOT NULL,	
			LAYERE_ACTIVE SINGLE NOT NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_LAYEREMP_NEW PRIMARY KEY (pg_code, layere_no)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_LAYEREMP_NEW(
			PG_CODE CHAR(10) NOT NULL,
			LAYERE_NO NUMBER(3,1) NOT NULL,
			LAYERE_SALARY NUMBER(16,2) NOT NULL,	
			LAYERE_DAY NUMBER(16,2) NOT NULL,	
			LAYERE_HOUR NUMBER(16,2) NOT NULL,	
			LAYERE_ACTIVE NUMBER(1) NOT NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_LAYEREMP_NEW PRIMARY KEY (pg_code, layere_no)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_LAYEREMP_NEW(
			PG_CODE VARCHAR(10) NOT NULL,
			LAYERE_NO DECIMAL(3,1) NOT NULL,
			LAYERE_SALARY DECIMAL(16,2) NOT NULL,	
			LAYERE_DAY DECIMAL(16,2) NOT NULL,	
			LAYERE_HOUR DECIMAL(16,2) NOT NULL,	
			LAYERE_ACTIVE SMALLINT(1) NOT NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_LAYEREMP_NEW PRIMARY KEY (pg_code, layere_no)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_SPECIAL_SKILLGRP(
			SS_CODE VARCHAR(10) NOT NULL,	
			SS_NAME VARCHAR(100) NOT NULL,
			SS_ACTIVE SINGLE NOT NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_SPECIAL_SKILLGRP PRIMARY KEY (ss_code)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_SPECIAL_SKILLGRP(
			SS_CODE VARCHAR2(10) NOT NULL,	
			SS_NAME VARCHAR2(100) NOT NULL,
			SS_ACTIVE NUMBER(1) NOT NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_SPECIAL_SKILLGRP PRIMARY KEY (ss_code)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_SPECIAL_SKILLGRP(
			SS_CODE VARCHAR(10) NOT NULL,	
			SS_NAME VARCHAR(100) NOT NULL,
			SS_ACTIVE SMALLINT(1) NOT NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_SPECIAL_SKILLGRP PRIMARY KEY (ss_code)) ";
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
			CONSTRAINT PK_PER_SPECIAL_SKILL PRIMARY KEY (sps_id)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_SPECIAL_SKILL(
			SPS_ID NUMBER(10) NOT NULL,	
			PER_ID NUMBER(10) NOT NULL,	
			PER_CARDNO VARCHAR2(13) NULL,
			SS_CODE VARCHAR2(10) NOT NULL,	
			SPS_EMPHASIZE VARCHAR2(1000) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_SPECIAL_SKILL PRIMARY KEY (sps_id)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_SPECIAL_SKILL(
			SPS_ID INTEGER(10) NOT NULL,	
			PER_ID INTEGER(10) NOT NULL,	
			PER_CARDNO VARCHAR(13) NULL,
			SS_CODE VARCHAR(10) NOT NULL,	
			SPS_EMPHASIZE TEXT NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_SPECIAL_SKILL PRIMARY KEY (sps_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_PERFORMANCE_REVIEW(
			PFR_ID INTEGER NOT NULL,	
			PFR_NAME VARCHAR(100) NOT NULL,
			PFR_YEAR VARCHAR(4) NOT NULL,
			PFR_ID_REF INTEGER NULL,	
			DEPARTMENT_ID INTEGER NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_PERFORMANCE_REVIEW PRIMARY KEY (pfr_id)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_PERFORMANCE_REVIEW(
			PFR_ID NUMBER(10) NOT NULL,	
			PFR_NAME VARCHAR2(100) NOT NULL,
			PFR_YEAR VARCHAR2(4) NOT NULL,
			PFR_ID_REF NUMBER(10) NULL,	
			DEPARTMENT_ID NUMBER(10) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_PERFORMANCE_REVIEW PRIMARY KEY (pfr_id)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_PERFORMANCE_REVIEW(
			PFR_ID INTEGER(10) NOT NULL,	
			PFR_NAME VARCHAR(100) NOT NULL,
			PFR_YEAR VARCHAR(4) NOT NULL,
			PFR_ID_REF INTEGER(10) NULL,	
			DEPARTMENT_ID INTEGER(10) NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_PERFORMANCE_REVIEW PRIMARY KEY (pfr_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERFORMANCE_REVIEW ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERFORMANCE_REVIEW ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PERFORMANCE_REVIEW ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_PERFORMANCE_REVIEW SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

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
			DEPARTMENT_ID INTEGER NULL,
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
			DEPARTMENT_ID NUMBER(10) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_KPI PRIMARY KEY (kpi_id)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_KPI(
			KPI_ID INTEGER(10) NOT NULL,	
			KPI_NAME TEXT NOT NULL,
			KPI_YEAR VARCHAR(4) NOT NULL,
			KPI_WEIGHT DECEMAL(7,2) NULL,
			KPI_MEASURE VARCHAR(100) NULL,
			KPI_PER_ID INTEGER(10) NOT NULL,	
			PFR_ID INTEGER(10) NOT NULL,	
			KPI_TARGET_LEVEL1 SMALLINT(3) NULL,
			KPI_TARGET_LEVEL2 SMALLINT(3) NULL,
			KPI_TARGET_LEVEL3 SMALLINT(3) NULL,
			KPI_TARGET_LEVEL4 SMALLINT(3) NULL,
			KPI_TARGET_LEVEL5 SMALLINT(3) NULL,
			KPI_ID_REF INTEGER(10) NULL,	
			KPI_EVALUATE SMALLINT(1) NULL,
			DEPARTMENT_ID INTEGER(10) NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_KPI PRIMARY KEY (kpi_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_KPI ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_KPI ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_KPI ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_KPI SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

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
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_JOB_FAMILY(
			JF_CODE VARCHAR(2) NOT NULL,	
			JF_NAME VARCHAR(100) NOT NULL,	
			JF_ACTIVE SMALLINT(1) NOT NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
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
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_COMPETENCE(
			CP_CODE VARCHAR(3) NOT NULL,	
			CP_NAME VARCHAR(100) NOT NULL,	
			CP_MEANING TEXT NULL,	
			CP_MODEL SMALLINT(1) NOT NULL,
			CP_ACTIVE SMALLINT(1) NOT NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
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
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_JOB_COMPETENCE(
			JF_CODE VARCHAR(2) NOT NULL,	
			CP_CODE VARCHAR(3) NOT NULL,	
			JC_ACTIVE SMALLINT(1) NOT NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
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
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_COMPETENCE_LEVEL(
			CP_CODE VARCHAR(3) NOT NULL,	
			CL_NO SMALLINT(1) NOT NULL,	
			CL_NAME VARCHAR(255) NOT NULL,	
			CL_MEANING TEXT NULL,	
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_COMPETENCE_LEVEL PRIMARY KEY (cp_code, cl_no)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_POSITION_COMPETENCE(
			POS_ID INTEGER NOT NULL,	
			CP_CODE VARCHAR(3) NOT NULL,
			PC_TARGET_LEVEL SINGLE NOT NULL,
			DEPARTMENT_ID INTEGER NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_POSITION_COMPETENCE PRIMARY KEY (pos_id, cp_code)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_POSITION_COMPETENCE(
			POS_ID NUMBER(10) NOT NULL,	
			CP_CODE VARCHAR2(3) NOT NULL,
			PC_TARGET_LEVEL NUMBER(1) NOT NULL,
			DEPARTMENT_ID NUMBER(10) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_POSITION_COMPETENCE PRIMARY KEY (pos_id, cp_code)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_POSITION_COMPETENCE(
			POS_ID INTEGER(10) NOT NULL,	
			CP_CODE VARCHAR(3) NOT NULL,
			PC_TARGET_LEVEL SMALLINT(1) NOT NULL,
			DEPARTMENT_ID INTEGER(10) NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_POSITION_COMPETENCE PRIMARY KEY (pos_id, cp_code)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITION_COMPETENCE ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITION_COMPETENCE ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_POSITION_COMPETENCE ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_POSITION_COMPETENCE SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} else {
			if($DPISDB=="odbc" || $DPISDB=="mysql"){ 
				$cmd = " SELECT POS_ID FROM PER_POSITION_COMPETENCE ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$POS_ID = $data[POS_ID];
					$cmd = " SELECT ORG_ID FROM PER_POSITION WHERE POS_ID = $POS_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID = $data1[ORG_ID];
					$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID_REF = $data1[ORG_ID_REF];
					$cmd = " UPDATE PER_POSITION_COMPETENCE SET DEPARTMENT_ID = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while						
			} elseif($DPISDB=="oci8"){
				$cmd = " UPDATE PER_POSITION_COMPETENCE A SET A.DEPARTMENT_ID = (SELECT D.ORG_ID_REF FROM PER_POSITION B, PER_ORG C, PER_ORG D WHERE A.POS_ID = B.POS_ID AND B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if
		} // end if

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
			DEPARTMENT_ID INTEGER NULL,
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
			DEPARTMENT_ID NUMBER(10) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_KPI_FORM PRIMARY KEY (kf_id)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_KPI_FORM(
			KF_ID INTEGER(10) NOT NULL,	
			PER_ID INTEGER(10) NOT NULL,	
			PER_CARDNO VARCHAR(13) NULL,
			KF_CYCLE SMALLINT(1) NOT NULL,
			KF_START_DATE VARCHAR(19) NOT NULL,
			KF_END_DATE VARCHAR(19) NOT NULL,
			PER_ID_REVIEW INTEGER(10) NULL,
			PER_ID_REVIEW1 INTEGER(10) NULL,	
			PER_ID_REVIEW2 INTEGER(10) NULL,	
			SUM_KPI DECIMAL(6,2) NULL,
			SUM_COMPETENCE DECIMAL(6,2) NULL,
			SUM_EVALUATE SMALLINT(1) NULL,
			SCORE_KPI SMALLINT(4) NULL,
			SCORE_COMPETENCE SMALLINT(4) NULL,
			RESULT_COMMENT TEXT NULL,
			COMPETENCE_COMMENT TEXT NULL,
			SALARY_RESULT SMALLINT(1) NULL,
			SALARY_REMARK1 TEXT NULL,
			SALARY_REMARK2 TEXT NULL,
			AGREE_REVIEW1 TEXT NULL,
			DIFF_REVIEW1 TEXT NULL,
			AGREE_REVIEW2 TEXT NULL,
			DIFF_REVIEW2 TEXT NULL,
			DEPARTMENT_ID INTEGER(10) NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_KPI_FORM PRIMARY KEY (kf_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_KPI_FORM ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_KPI_FORM ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_KPI_FORM ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_KPI_FORM SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} else {
			if($DPISDB=="odbc" || $DPISDB=="mysql"){ 
				$cmd = " SELECT KF_ID, PER_ID FROM PER_KPI_FORM WHERE PER_ID IS NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$KF_ID = $data[KF_ID];
					$PER_ID = $data[PER_ID];
					$cmd = " SELECT POS_ID FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$POS_ID = $data[POS_ID];
					$cmd = " SELECT ORG_ID FROM PER_POSITION WHERE POS_ID = $POS_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID = $data1[ORG_ID];
					$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID_REF = $data1[ORG_ID_REF];
					$cmd = " UPDATE PER_KPI_FORM SET DEPARTMENT_ID = $ORG_ID_REF WHERE KF_ID = $KF_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while						
			} elseif($DPISDB=="oci8"){
				$cmd = " UPDATE PER_KPI_FORM X SET X.DEPARTMENT_ID = (SELECT D.ORG_ID_REF FROM PER_PERSONAL A, PER_POSITION B, PER_ORG C, PER_ORG D WHERE A.PER_ID = X.PER_ID AND A.POS_ID = B.POS_ID AND B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if
		} // end if

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
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_PERFORMANCE_GOALS(
			PG_ID INTEGER(10) NOT NULL,	
			KF_ID INTEGER(10) NOT NULL,	
			PG_SEQ SMALLINT(2) NULL,	
			KPI_ID INTEGER(10) NULL,	
			KPI_NAME TEXT NOT NULL,
			KPI_WEIGHT SMALLINT(3) NULL,
			KPI_MEASURE VARCHAR(100) NULL,
			KPI_PER_ID INTEGER(10) NOT NULL,	
			KPI_TARGET_LEVEL1 SMALLINT(3) NULL,
			KPI_TARGET_LEVEL2 SMALLINT(3) NULL,
			KPI_TARGET_LEVEL3 SMALLINT(3) NULL,
			KPI_TARGET_LEVEL4 SMALLINT(3) NULL,
			KPI_TARGET_LEVEL5 SMALLINT(3) NULL,
			KPI_TARGET_LEVEL1_DESC VARCHAR(255) NULL,
			KPI_TARGET_LEVEL2_DESC VARCHAR(255) NULL,
			KPI_TARGET_LEVEL3_DESC VARCHAR(255) NULL,
			KPI_TARGET_LEVEL4_DESC VARCHAR(255) NULL,
			KPI_TARGET_LEVEL5_DESC VARCHAR(255) NULL,
			PG_RESULT TEXT NULL,
			PG_EVALUATE SMALLINT(1) NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
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
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_KPI_COMPETENCE(
			KC_ID INTEGER(10) NOT NULL,	
			KF_ID INTEGER(10) NOT NULL,	
			CP_CODE VARCHAR(3) NOT NULL,	
			PC_TARGET_LEVEL SMALLINT(1) NOT NULL,
			KC_EVALUATE SMALLINT(1) NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
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
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_IPIP(
			IPIP_ID INTEGER(10) NOT NULL,	
			KF_ID INTEGER(10) NOT NULL,	
			DEVELOP_SEQ SMALLINT(2) NULL,
			DEVELOP_COMPETENCE VARCHAR(255) NULL,
			DEVELOP_METHOD VARCHAR(255) NULL,
			DEVELOP_INTERVAL VARCHAR(255) NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_IPIP PRIMARY KEY (ipip_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ORG ADD ORG_WEBSITE VARCHAR(255) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ORG ADD ORG_WEBSITE VARCHAR2(255) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_ORG ADD ORG_WEBSITE VARCHAR(255) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ORG ADD ORG_SEQ_NO INTEGER2 ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ORG ADD ORG_SEQ_NO NUMBER(5) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_ORG ADD ORG_SEQ_NO SMALLINT(5) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ORG_ASS ADD ORG_WEBSITE VARCHAR(255) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ORG_ASS ADD ORG_WEBSITE VARCHAR2(255) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_ORG_ASS ADD ORG_WEBSITE VARCHAR(255) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ORG_ASS ADD ORG_SEQ_NO INTEGER2 ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ORG_ASS ADD ORG_SEQ_NO NUMBER(5) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_ORG_ASS ADD ORG_SEQ_NO SMALLINT(5) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ORG_ASS ALTER ORG_SHORT VARCHAR(100) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ORG_ASS MODIFY ORG_SHORT VARCHAR2(100) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_ORG_ASS ALTER ORG_SHORT VARCHAR(100) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SERVICETITLE ADD SV_CODE VARCHAR(2) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SERVICETITLE ADD SV_CODE VARCHAR2(2) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_SERVICETITLE ADD SV_CODE VARCHAR(2) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_SERVICETITLE SET SV_CODE = SUBSTR(SRT_CODE,1,2) ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_SERVICETITLE SET SV_CODE = SRT_CODE ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_SERVICETITLE SET SV_CODE = SRT_CODE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SERVICETITLE ALTER SRT_NAME MEMO ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SERVICETITLE MODIFY SRT_NAME VARCHAR2(1000) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_SERVICETITLE ALTER SRT_NAME TEXT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_COMDTL ALTER CMD_NOTE1 MEMO ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_COMDTL MODIFY CMD_NOTE1 VARCHAR2(2000) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_COMDTL ALTER CMD_NOTE1 TEXT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_COMDTL ALTER CMD_NOTE2 MEMO ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_COMDTL MODIFY CMD_NOTE2 VARCHAR2(2000) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_COMDTL ALTER CMD_NOTE2 TEXT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG1 VARCHAR(255) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG1 VARCHAR2(255) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG1 VARCHAR(255) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITION ALTER POS_REMARK MEMO ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITION MODIFY POS_REMARK VARCHAR2(2000) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_POSITION ALTER POS_REMARK TEXT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_POSITIONHIS, PER_ORG SET PER_POSITIONHIS.POH_ORG1 = PER_ORG.ORG_NAME 
							  WHERE PER_ORG.ORG_ID = PER_POSITIONHIS.ORG_ID_1 AND PER_POSITIONHIS.POH_ORG1 IS NULL AND PER_POSITIONHIS.ORG_ID_1 <> 1 ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG1 = (SELECT ORG_NAME FROM PER_ORG WHERE ORG_ID_1 = PER_ORG.ORG_ID) 
							  WHERE POH_ORG1 IS NULL AND ORG_ID_1 != 1 ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_POSITIONHIS, PER_ORG SET PER_POSITIONHIS.POH_ORG1 = PER_ORG.ORG_NAME 
							  WHERE PER_ORG.ORG_ID = PER_POSITIONHIS.ORG_ID_1 AND PER_POSITIONHIS.POH_ORG1 IS NULL AND PER_POSITIONHIS.ORG_ID_1 != 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG2 VARCHAR(255) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG2 VARCHAR2(255) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG2 VARCHAR(255) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_POSITIONHIS, PER_ORG SET PER_POSITIONHIS.POH_ORG2 = PER_ORG.ORG_NAME 
							  WHERE PER_ORG.ORG_ID = PER_POSITIONHIS.ORG_ID_2 AND PER_POSITIONHIS.POH_ORG2 IS NULL AND PER_POSITIONHIS.ORG_ID_2 <> 1 ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2 = (SELECT ORG_NAME FROM PER_ORG WHERE ORG_ID_2 = PER_ORG.ORG_ID) 
							  WHERE POH_ORG2 IS NULL AND ORG_ID_2 != 1 ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_POSITIONHIS, PER_ORG SET PER_POSITIONHIS.POH_ORG2 = PER_ORG.ORG_NAME 
							  WHERE PER_ORG.ORG_ID = PER_POSITIONHIS.ORG_ID_2 AND PER_POSITIONHIS.POH_ORG2 IS NULL AND PER_POSITIONHIS.ORG_ID_2 != 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG3 VARCHAR(255) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG3 VARCHAR2(255) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG3 VARCHAR(255) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_POSITIONHIS, PER_ORG SET PER_POSITIONHIS.POH_ORG3 = PER_ORG.ORG_NAME 
							  WHERE PER_ORG.ORG_ID = PER_POSITIONHIS.ORG_ID_3 AND PER_POSITIONHIS.POH_ORG3 IS NULL AND PER_POSITIONHIS.ORG_ID_3 <> 1 ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG3 = (SELECT ORG_NAME FROM PER_ORG WHERE ORG_ID_3 = PER_ORG.ORG_ID) 
							  WHERE POH_ORG3 IS NULL AND ORG_ID_3 != 1 ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_POSITIONHIS, PER_ORG SET PER_POSITIONHIS.POH_ORG3 = PER_ORG.ORG_NAME 
							  WHERE PER_ORG.ORG_ID = PER_POSITIONHIS.ORG_ID_3 AND PER_POSITIONHIS.POH_ORG3 IS NULL AND PER_POSITIONHIS.ORG_ID_3 != 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ALTER ORG_ID_1 INTEGER NULL ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY ORG_ID_1 NUMBER(10) NULL ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_POSITIONHIS ALTER ORG_ID_1 INTEGER(10) NULL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ALTER ORG_ID_2 INTEGER NULL ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY ORG_ID_2 NUMBER(10) NULL ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_POSITIONHIS ALTER ORG_ID_2 INTEGER(10) NULL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ALTER ORG_ID_3 INTEGER NULL ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY ORG_ID_3 NUMBER(10) NULL ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_POSITIONHIS ALTER ORG_ID_3 INTEGER(10) NULL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_PERSONAL SET POS_ID = NULL WHERE PER_STATUS = 2 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITION ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITION ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_POSITION ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_POSITION SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} else {
			if($DPISDB=="odbc" || $DPISDB=="mysql"){ 
				$cmd = " SELECT POS_ID, ORG_ID FROM PER_POSITION WHERE ORG_ID IS NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$POS_ID = $data[POS_ID];
					$ORG_ID = $data1[ORG_ID];
					$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID_REF = $data1[ORG_ID_REF];
					$cmd = " UPDATE PER_POSITION SET DEPARTMENT_ID = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while						
			} elseif($DPISDB=="oci8"){
				$cmd = " UPDATE PER_POSITION A SET A.DEPARTMENT_ID = (SELECT D.ORG_ID_REF FROM PER_ORG C, PER_ORG D 
								  WHERE A.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POS_EMP ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POS_EMP ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_POS_EMP ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_POS_EMP SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} else {
			if($DPISDB=="odbc" || $DPISDB=="mysql"){ 
				$cmd = " SELECT POEM_ID, ORG_ID FROM PER_POS_EMP WHERE ORG_ID IS NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$POEM_ID = $data[POEM_ID];
					$ORG_ID = $data[ORG_ID];
					$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID_REF = $data1[ORG_ID_REF];
					$cmd = " UPDATE PER_POS_EMP SET DEPARTMENT_ID = $ORG_ID_REF WHERE POEM_ID = $POEM_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while						
			} elseif($DPISDB=="oci8"){
				$cmd = " UPDATE PER_POS_EMP A SET A.DEPARTMENT_ID = (SELECT D.ORG_ID_REF FROM PER_ORG C, PER_ORG D 
								  WHERE A.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POS_EMPSER ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POS_EMPSER ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_POS_EMPSER ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_POS_EMPSER SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} else {
			if($DPISDB=="odbc" || $DPISDB=="mysql"){ 
				$cmd = " SELECT POEMS_ID, ORG_ID FROM PER_POS_EMPSER WHERE ORG_ID IS NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$POEMS_ID = $data[POEMS_ID];
					$ORG_ID = $data[ORG_ID];
					$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID_REF = $data1[ORG_ID_REF];
					$cmd = " UPDATE PER_POS_EMPSER SET DEPARTMENT_ID = $ORG_ID_REF WHERE POEMS_ID = $POEMS_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while						
			} elseif($DPISDB=="oci8"){
				$cmd = " UPDATE PER_POS_EMPSER A SET A.DEPARTMENT_ID = (SELECT D.ORG_ID_REF FROM PER_ORG C, PER_ORG D 
								  WHERE A.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SALQUOTA ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SALQUOTA ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_SALQUOTA ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_SALQUOTA SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SALQUOTADTL1 ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SALQUOTADTL1 ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_SALQUOTADTL1 ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_SALQUOTADTL1 SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} else {
			if($DPISDB=="odbc" || $DPISDB=="mysql"){ 
				$cmd = " SELECT SALQ_YEAR, SALQ_TYPE, ORG_ID FROM PER_SALQUOTADTL1 WHERE ORG_ID IS NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$SALQ_YEAR = $data[SALQ_YEAR];
					$SALQ_TYPE = $data[SALQ_TYPE];
					$ORG_ID = $data[ORG_ID];
					$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID_REF = $data1[ORG_ID_REF];
					$cmd = " UPDATE PER_SALQUOTADTL1 SET DEPARTMENT_ID = $ORG_ID_REF 
									  WHERE SALQ_YEAR = '$SALQ_YEAR' AND SALQ_TYPE = $SALQ_TYPE AND ORG_ID = $ORG_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while						
			} elseif($DPISDB=="oci8"){
				$cmd = " UPDATE PER_SALQUOTADTL1 A SET A.DEPARTMENT_ID = (SELECT D.ORG_ID_REF FROM PER_ORG C, PER_ORG D 
								  WHERE A.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SALQUOTADTL2 ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SALQUOTADTL2 ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_SALQUOTADTL2 ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_SALQUOTADTL2 SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} else {
			if($DPISDB=="odbc" || $DPISDB=="mysql"){ 
				$cmd = " SELECT SALQ_YEAR, SALQ_TYPE, ORG_ID FROM PER_SALQUOTADTL2 WHERE ORG_ID IS NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$SALQ_YEAR = $data[SALQ_YEAR];
					$SALQ_TYPE = $data[SALQ_TYPE];
					$ORG_ID = $data[ORG_ID];
					$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID_REF = $data1[ORG_ID_REF];
					$cmd = " UPDATE PER_SALQUOTADTL2 SET DEPARTMENT_ID = $ORG_ID_REF 
									  WHERE SALQ_YEAR = '$SALQ_YEAR' AND SALQ_TYPE = $SALQ_TYPE AND ORG_ID = $ORG_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while						
			} elseif($DPISDB=="oci8"){
				$cmd = " UPDATE PER_SALQUOTADTL2 A SET A.DEPARTMENT_ID = (SELECT D.ORG_ID_REF FROM PER_ORG C, PER_ORG D 
								  WHERE A.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SALPROMOTE ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SALPROMOTE ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_SALPROMOTE ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_SALPROMOTE SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} else {
			if($DPISDB=="odbc" || $DPISDB=="mysql"){ 
				$cmd = " SELECT SALQ_YEAR, SALQ_TYPE, PER_ID FROM PER_SALPROMOTE WHERE PER_ID IS NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$SALQ_YEAR = $data[SALQ_YEAR];
					$SALQ_TYPE = $data[SALQ_TYPE];
					$PER_ID = $data[PER_ID];
					$cmd = " SELECT POS_ID FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$POS_ID = $data[POS_ID];
					$cmd = " SELECT ORG_ID FROM PER_POSITION WHERE POS_ID = $POS_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID = $data1[ORG_ID];
					$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID_REF = $data1[ORG_ID_REF];
					$cmd = " UPDATE PER_SALPROMOTE SET DEPARTMENT_ID = $ORG_ID_REF 
									  WHERE SALQ_YEAR = '$SALQ_YEAR' AND SALQ_TYPE = $SALQ_TYPE AND PER_ID = $PER_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while						
			} elseif($DPISDB=="oci8"){
				$cmd = " UPDATE PER_SALPROMOTE X SET X.DEPARTMENT_ID = (SELECT D.ORG_ID_REF FROM PER_PERSONAL A, PER_POSITION B, PER_ORG C, 
								  PER_ORG D WHERE A.PER_ID = X.PER_ID AND A.POS_ID = B.POS_ID AND B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_BONUSQUOTA ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_BONUSQUOTA ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_BONUSQUOTA ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_BONUSQUOTA SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_BONUSQUOTADTL1 ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_BONUSQUOTADTL1 ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_BONUSQUOTADTL1 ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_BONUSQUOTADTL1 SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} else {
			if($DPISDB=="odbc" || $DPISDB=="mysql"){ 
				$cmd = " SELECT BONUS_YEAR, BONUS_TYPE, ORG_ID FROM PER_BONUSQUOTADTL1 WHERE ORG_ID IS NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$BONUS_YEAR = $data[BONUS_YEAR];
					$BONUS_TYPE = $data[BONUS_TYPE];
					$ORG_ID = $data[ORG_ID];
					$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID_REF = $data1[ORG_ID_REF];
					$cmd = " UPDATE PER_BONUSQUOTADTL1 SET DEPARTMENT_ID = $ORG_ID_REF 
									  WHERE BONUS_YEAR = '$BONUS_YEAR' AND BONUS_TYPE = $BONUS_TYPE AND ORG_ID = $ORG_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while						
			} elseif($DPISDB=="oci8"){
				$cmd = " UPDATE PER_BONUSQUOTADTL1 A SET A.DEPARTMENT_ID = (SELECT D.ORG_ID_REF FROM PER_ORG C, PER_ORG D 
								  WHERE A.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_BONUSQUOTADTL2 ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_BONUSQUOTADTL2 ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_BONUSQUOTADTL2 ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_BONUSQUOTADTL2 SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} else {
			if($DPISDB=="odbc" || $DPISDB=="mysql"){ 
				$cmd = " SELECT BONUS_YEAR, BONUS_TYPE, ORG_ID FROM PER_BONUSQUOTADTL2 WHERE ORG_ID IS NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$BONUS_YEAR = $data[BONUS_YEAR];
					$BONUS_TYPE = $data[BONUS_TYPE];
					$ORG_ID = $data[ORG_ID];
					$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID_REF = $data1[ORG_ID_REF];
					$cmd = " UPDATE PER_BONUSQUOTADTL2 SET DEPARTMENT_ID = $ORG_ID_REF 
									  WHERE BONUS_YEAR = '$BONUS_YEAR' AND BONUS_TYPE = $BONUS_TYPE AND ORG_ID = $ORG_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while						
			} elseif($DPISDB=="oci8"){
				$cmd = " UPDATE PER_BONUSQUOTADTL2 A SET A.DEPARTMENT_ID = (SELECT D.ORG_ID_REF FROM PER_ORG C, PER_ORG D 
								  WHERE A.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_BONUSPROMOTE ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_BONUSPROMOTE ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_BONUSPROMOTE ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_BONUSPROMOTE SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} else {
			if($DPISDB=="odbc" || $DPISDB=="mysql"){ 
				$cmd = " SELECT BONUS_YEAR, BONUS_TYPE, PER_ID FROM PER_BONUSPROMOTE WHERE PER_ID IS NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$BONUS_YEAR = $data[BONUS_YEAR];
					$BONUS_TYPE = $data[BONUS_TYPE];
					$PER_ID = $data[PER_ID];
					$cmd = " SELECT POS_ID FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$POS_ID = $data[POS_ID];
					$cmd = " SELECT ORG_ID FROM PER_POSITION WHERE POS_ID = $POS_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID = $data1[ORG_ID];
					$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID_REF = $data1[ORG_ID_REF];
					$cmd = " UPDATE PER_BONUSPROMOTE SET DEPARTMENT_ID = $ORG_ID_REF 
									  WHERE BONUS_YEAR = '$BONUS_YEAR' AND BONUS_TYPE = $BONUS_TYPE AND PER_ID = $PER_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while						
			} elseif($DPISDB=="oci8"){
				$cmd = " UPDATE PER_BONUSPROMOTE X SET X.DEPARTMENT_ID = (SELECT D.ORG_ID_REF FROM PER_PERSONAL A, PER_POSITION B, PER_ORG C, 
								  PER_ORG D WHERE A.PER_ID = X.PER_ID AND A.POS_ID = B.POS_ID AND B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_TRANSFER_REQ SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_MOVE_REQ ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_MOVE_REQ ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_MOVE_REQ ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_MOVE_REQ SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} else {
			if($DPISDB=="odbc" || $DPISDB=="mysql"){ 
				$cmd = " SELECT MV_ID, PER_ID FROM PER_MOVE_REQ WHERE PER_ID IS NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$MV_ID = $data[MV_ID];
					$PER_ID = $data[PER_ID];
					$cmd = " SELECT POS_ID FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$POS_ID = $data[POS_ID];
					$cmd = " SELECT ORG_ID FROM PER_POSITION WHERE POS_ID = $POS_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID = $data1[ORG_ID];
					$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID_REF = $data1[ORG_ID_REF];
					$cmd = " UPDATE PER_MOVE_REQ SET DEPARTMENT_ID = $ORG_ID_REF WHERE MV_ID = $MV_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while						
			} elseif($DPISDB=="oci8"){
				$cmd = " UPDATE PER_MOVE_REQ X SET X.DEPARTMENT_ID = (SELECT D.ORG_ID_REF FROM PER_PERSONAL A, PER_POSITION B, PER_ORG C, 
								  PER_ORG D WHERE A.PER_ID = X.PER_ID AND A.POS_ID = B.POS_ID AND B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PROMOTE_C ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PROMOTE_C ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PROMOTE_C ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_PROMOTE_C SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} else {
			if($DPISDB=="odbc" || $DPISDB=="mysql"){ 
				$cmd = " SELECT PRO_YEAR, PRO_TYPE, PER_ID FROM PER_PROMOTE_C WHERE PER_ID IS NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$PRO_YEAR = $data[PRO_YEAR];
					$PRO_TYPE = $data[PRO_TYPE];
					$PER_ID = $data[PER_ID];
					$cmd = " SELECT POS_ID FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$POS_ID = $data[POS_ID];
					$cmd = " SELECT ORG_ID FROM PER_POSITION WHERE POS_ID = $POS_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID = $data1[ORG_ID];
					$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID_REF = $data1[ORG_ID_REF];
					$cmd = " UPDATE PER_PROMOTE_C SET DEPARTMENT_ID = $ORG_ID_REF 
									  WHERE PRO_YEAR = '$PRO_YEAR' AND PRO_TYPE = $PRO_TYPE AND PER_ID = $PER_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while						
			} elseif($DPISDB=="oci8"){
				$cmd = " UPDATE PER_PROMOTE_C X SET X.DEPARTMENT_ID = (SELECT D.ORG_ID_REF FROM PER_PERSONAL A, PER_POSITION B, PER_ORG C, 
								  PER_ORG D WHERE A.PER_ID = X.PER_ID AND A.POS_ID = B.POS_ID AND B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PROMOTE_E ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PROMOTE_E ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PROMOTE_E ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_PROMOTE_E SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} else {
			if($DPISDB=="odbc" || $DPISDB=="mysql"){ 
				$cmd = " SELECT PRO_DATE, POEM_ID, PER_ID FROM PER_PROMOTE_E WHERE PER_ID IS NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$PRO_DATE = $data[PRO_DATE];
					$POEM_ID = $data[POEM_ID];
					$PER_ID = $data[PER_ID];
					$cmd = " SELECT POS_ID FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$POS_ID = $data[POS_ID];
					$cmd = " SELECT ORG_ID FROM PER_POSITION WHERE POS_ID = $POS_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID = $data1[ORG_ID];
					$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID_REF = $data1[ORG_ID_REF];
					$cmd = " UPDATE PER_PROMOTE_E SET DEPARTMENT_ID = $ORG_ID_REF 
									  WHERE PRO_DATE = '$PRO_DATE' AND POEM_ID = $POEM_ID AND PER_ID = $PER_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while						
			} elseif($DPISDB=="oci8"){
				$cmd = " UPDATE PER_PROMOTE_E X SET X.DEPARTMENT_ID = (SELECT D.ORG_ID_REF FROM PER_PERSONAL A, PER_POSITION B, PER_ORG C, 
								  PER_ORG D WHERE A.PER_ID = X.PER_ID AND A.POS_ID = B.POS_ID AND B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PROMOTE_P ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PROMOTE_P ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PROMOTE_P ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_PROMOTE_P SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} else {
			if($DPISDB=="odbc" || $DPISDB=="mysql"){ 
				$cmd = " SELECT PRO_DATE, POS_ID, PER_ID FROM PER_PROMOTE_P WHERE PER_ID IS NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$PRO_DATE = $data[PRO_DATE];
					$POS_ID = $data[POS_ID];
					$PER_ID = $data[PER_ID];
					$cmd = " SELECT POS_ID FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$POS_ID = $data[POS_ID];
					$cmd = " SELECT ORG_ID FROM PER_POSITION WHERE POS_ID = $POS_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID = $data1[ORG_ID];
					$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID_REF = $data1[ORG_ID_REF];
					$cmd = " UPDATE PER_PROMOTE_P SET DEPARTMENT_ID = $ORG_ID_REF 
									  WHERE PRO_DATE = '$PRO_DATE' AND POS_ID = $POS_ID AND PER_ID = $PER_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while						
			} elseif($DPISDB=="oci8"){
				$cmd = " UPDATE PER_PROMOTE_P X SET X.DEPARTMENT_ID = (SELECT D.ORG_ID_REF FROM PER_PERSONAL A, PER_POSITION B, PER_ORG C, 
								  PER_ORG D WHERE A.PER_ID = X.PER_ID AND A.POS_ID = B.POS_ID AND B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_COMMAND ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_COMMAND ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_COMMAND ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_COMMAND SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LETTER ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LETTER ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LETTER ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_LETTER SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} else {
			if($DPISDB=="odbc" || $DPISDB=="mysql"){ 
				$cmd = " SELECT LET_ID, PER_ID FROM PER_LETTER WHERE PER_ID IS NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$LET_ID = $data[LET_ID];
					$PER_ID = $data[PER_ID];
					$cmd = " SELECT POS_ID FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$POS_ID = $data[POS_ID];
					$cmd = " SELECT ORG_ID FROM PER_POSITION WHERE POS_ID = $POS_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID = $data1[ORG_ID];
					$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID_REF = $data1[ORG_ID_REF];
					$cmd = " UPDATE PER_LETTER SET DEPARTMENT_ID = $ORG_ID_REF WHERE LET_ID = $LET_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while						
			} elseif($DPISDB=="oci8"){
				$cmd = " UPDATE PER_LETTER X SET X.DEPARTMENT_ID = (SELECT D.ORG_ID_REF FROM PER_PERSONAL A, PER_POSITION B, PER_ORG C, PER_ORG D 
								  WHERE A.PER_ID = X.PER_ID AND A.POS_ID = B.POS_ID AND B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_DECOR ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_DECOR ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_DECOR ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_DECOR SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_COURSE ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_COURSE ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_COURSE ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_COURSE SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SCHOLAR ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SCHOLAR ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_SCHOLAR ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_SCHOLAR SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} else {
			if($DPISDB=="odbc" || $DPISDB=="mysql"){ 
				$cmd = " SELECT SC_ID, PER_ID FROM PER_SCHOLAR WHERE PER_ID IS NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$SC_ID = $data[SC_ID];
					$PER_ID = $data[PER_ID];
					$cmd = " SELECT POS_ID FROM PER_PERSONAL WHERE PER_ID = $PER_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$POS_ID = $data[POS_ID];
					$cmd = " SELECT ORG_ID FROM PER_POSITION WHERE POS_ID = $POS_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID = $data1[ORG_ID];
					$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$ORG_ID_REF = $data1[ORG_ID_REF];
					$cmd = " UPDATE PER_SCHOLAR SET DEPARTMENT_ID = $ORG_ID_REF WHERE SC_ID = $SC_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while						
			} elseif($DPISDB=="oci8"){
				$cmd = " UPDATE PER_SCHOLAR X SET X.DEPARTMENT_ID = (SELECT D.ORG_ID_REF FROM PER_PERSONAL A, PER_POSITION B, PER_ORG C, PER_ORG D 
								  WHERE A.PER_ID = X.PER_ID AND A.POS_ID = B.POS_ID AND B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_INVEST1 ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_INVEST1 ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_INVEST1 ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_INVEST1 SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_INVEST2 ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_INVEST2 ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_INVEST2 ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_INVEST2 SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ORDER ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ORDER ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_ORDER ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_ORDER SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_REQ1 ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_REQ1 ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_REQ1 ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_REQ1 SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_REQ2 ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_REQ2 ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_REQ2 ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_REQ2 SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_REQ3 ADD DEPARTMENT_ID INTEGER ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_REQ3 ADD DEPARTMENT_ID NUMBER(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_REQ3 ADD DEPARTMENT_ID INTEGER(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($CTRL_TYPE == 4)	{
			$cmd = " UPDATE PER_REQ3 SET DEPARTMENT_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if

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
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_EMPSER_POS_NAME(
			EP_CODE VARCHAR(10) NOT NULL,	
			EP_NAME VARCHAR(100) NOT NULL,	
			LEVEL_NO VARCHAR(2) NOT NULL,
			EP_DECOR SMALLINT(1) NOT NULL,		
			EP_ACTIVE SMALLINT(1) NOT NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_EMPSER_POS_NAME PRIMARY KEY (ep_code)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITION ALTER POS_NO VARCHAR(15) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITION MODIFY POS_NO VARCHAR2(15) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_POSITION MODIFY POS_NO VARCHAR(15) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ORDER_DTL ALTER ORD_POS_NO VARCHAR(15) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ORDER_DTL MODIFY ORD_POS_NO VARCHAR2(15) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_ORDER_DTL MODIFY ORD_POS_NO VARCHAR(15) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_REQ3_DTL ALTER REQ_POS_NO VARCHAR(15) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_REQ3_DTL MODIFY REQ_POS_NO VARCHAR2(15) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_REQ3_DTL MODIFY REQ_POS_NO VARCHAR(15) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_REQ2_DTL ALTER REQ_POS_NO VARCHAR(15) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_REQ2_DTL MODIFY REQ_POS_NO VARCHAR2(15) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_REQ2_DTL MODIFY REQ_POS_NO VARCHAR(15) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_REQ1_DTL1 ALTER REQ_POS_NO VARCHAR(15) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_REQ1_DTL1 MODIFY REQ_POS_NO VARCHAR2(15) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_REQ1_DTL1 MODIFY REQ_POS_NO VARCHAR(15) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_POS_NO VARCHAR(15) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_POS_NO VARCHAR2(15) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_POS_NO VARCHAR(15) ";
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
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_POS_EMPSER(
			POEMS_ID INTEGER(10) NOT NULL,	
			ORG_ID INTEGER(10) NOT NULL,
			POEMS_NO VARCHAR(15) NOT NULL,	
			ORG_ID_1 INTEGER(10) NULL,
			ORG_ID_2 INTEGER(10) NULL,
			EP_CODE VARCHAR(10) NOT NULL,
			POEM_MIN_SALARY DECIMAL(16,2) NOT NULL,	
			POEM_MAX_SALARY DECIMAL(16,2) NOT NULL,		
			POEM_STATUS SMALLINT(1) NOT NULL,		
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_POS_EMPSER PRIMARY KEY (poems_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD POEMS_ID INTEGER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD POEMS_ID NUMBER(10) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PERSONAL ADD POEMS_ID INTEGER(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_HIP_FLAG VARCHAR(20) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_HIP_FLAG VARCHAR2(20) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_HIP_FLAG VARCHAR(20) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_PERSONAL SET PER_HIP_FLAG = NULL WHERE PER_HIP_FLAG = ' ' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_PERSONAL SET PER_CERT_OCC = NULL WHERE PER_CERT_OCC = ' ' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_CERT_OCC VARCHAR(20) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_CERT_OCC VARCHAR2(20) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_CERT_OCC VARCHAR(20) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD TR_POSDATE VARCHAR(19) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD TR_POSDATE VARCHAR2(19) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD TR_POSDATE VARCHAR(19) Null ";
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
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_OCCUPATION(
			OC_CODE VARCHAR(3) NOT NULL,	
			OC_NAME VARCHAR(100) NOT NULL,
			OC_ACTIVE SMALLINT(1) NOT NULL,		
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_OCCUPATION PRIMARY KEY (oc_code)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_FAMILY (
			family_id INTEGER NOT NULL,
			per_id INTEGER NOT NULL,
			mah_id INTEGER NOT NULL,
			cardno_sp varchar(13) NULL,
			gender_sp SINGLE NULL,
			birthdate_sp varchar(19) NULL,
		 	alive_flag_sp SINGLE NULL,
			re_code_sp VARCHAR(2) NULL,
			oc_code_sp VARCHAR(2) NULL,
			oc_other_sp varchar(100) NULL,
			mr_code_sp VARCHAR(2) NULL,
			doc_type_sp SINGLE NULL,
			doc_no_sp varchar(20) NULL,
			doc_date_sp varchar(19) NULL,
			doc_pv_code_sp VARCHAR(10) NULL,
			pv_code_sp VARCHAR(10) NULL,
			post_code_sp VARCHAR(5) NULL,
			cardno_f varchar(13) NULL,
			birthdate_f varchar(19) NULL,
			alive_flag_f SINGLE NULL,
			re_code_f VARCHAR(2) NULL,
			oc_code_f VARCHAR(2) NULL,
			oc_other_f varchar(100) NULL,
			type_f SINGLE NULL,
			doc_type_f SINGLE NULL,
			doc_no_f varchar(20) NULL,
			doc_date_f varchar(19) NULL,
			mr_code_f VARCHAR(2) NULL,
			mr_doc_type_f SINGLE NULL,
			mr_doc_no_f varchar(20) NULL,
			mr_doc_date_f varchar(19) NULL,
			doc_pv_code_f VARCHAR(10) NULL,
			pv_code_f VARCHAR(10) NULL,
			post_code_f VARCHAR(5) NULL,
			cardno_m varchar(13) NULL,
			birthdate_m varchar(19) NULL,
			alive_flag_m SINGLE NULL,
			re_code_m VARCHAR(2) NULL,
			oc_code_m VARCHAR(2) NULL,
			oc_other_m varchar(100) NULL,
			type_m SINGLE NULL,
			doc_type_m SINGLE NULL,
			doc_no_m varchar(20) NULL,
			doc_date_m varchar(19) NULL,
			mr_code_m VARCHAR(2) NULL,
			mr_doc_type_m SINGLE NULL,
			mr_doc_no_m varchar(20) NULL,
			mr_doc_date_m varchar(19) NULL,
			doc_pv_code_m VARCHAR(10) NULL,
			pv_code_m VARCHAR(10) NULL,
			post_code_m VARCHAR(5) NULL,
			update_user INTEGER2 NOT NULL,
			update_date varchar(19) NOT NULL,
			CONSTRAINT PK_PER_FAMILY PRIMARY KEY (family_id)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_FAMILY (
			family_id number(10) NOT NULL,
			per_id number(10) NOT NULL,
			mah_id number(10) NOT NULL,
			cardno_sp varchar2(13) NULL,
			gender_sp number(1) NULL,
			birthdate_sp varchar2(19) NULL,
		 	alive_flag_sp number(1) NULL,
			re_code_sp varchar2(2) NULL,
			oc_code_sp varchar2(2) NULL,
			oc_other_sp varchar2(100) NULL,
			mr_code_sp varchar2(2) NULL,
			doc_type_sp number(1) NULL,
			doc_no_sp varchar2(20) NULL,
			doc_date_sp varchar2(19) NULL,
			doc_pv_code_sp varchar2(10) NULL,
			pv_code_sp varchar2(10) NULL,
			post_code_sp varchar2(5) NULL,
			cardno_f varchar2(13) NULL,
			birthdate_f varchar2(19) NULL,
			alive_flag_f number(1) NULL,
			re_code_f varchar2(2) NULL,
			oc_code_f varchar2(2) NULL,
			oc_other_f varchar2(100) NULL,
			type_f number(1) NULL,
			doc_type_f number(1) NULL,
			doc_no_f varchar2(20) NULL,
			doc_date_f varchar2(19) NULL,
			mr_code_f varchar2(2) NULL,
			mr_doc_type_f number(1) NULL,
			mr_doc_no_f varchar2(20) NULL,
			mr_doc_date_f varchar2(19) NULL,
			doc_pv_code_f varchar2(10) NULL,
			pv_code_f varchar2(10) NULL,
			post_code_f varchar2(5) NULL,
			cardno_m varchar2(13) NULL,
			birthdate_m varchar2(19) NULL,
			alive_flag_m number(1) NULL,
			re_code_m varchar2(2) NULL,
			oc_code_m varchar2(2) NULL,
			oc_other_m varchar2(100) NULL,
			type_m number(1) NULL,
			doc_type_m number(1) NULL,
			doc_no_m varchar2(20) NULL,
			doc_date_m varchar2(19) NULL,
			mr_code_m varchar2(2) NULL,
			mr_doc_type_m number(1) NULL,
			mr_doc_no_m varchar2(20) NULL,
			mr_doc_date_m varchar2(19) NULL,
			doc_pv_code_m varchar2(10) NULL,
			pv_code_m varchar2(10) NULL,
			post_code_m varchar2(5) NULL,
			update_user number(5) NOT NULL,
			update_date varchar2(19) NOT NULL,
			CONSTRAINT PK_PER_FAMILY PRIMARY KEY (family_id)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_FAMILY (
			family_id INTEGER NOT NULL,
			per_id INTEGER NOT NULL,
			mah_id INTEGER NOT NULL,
			cardno_sp varchar(13) NULL,
			gender_sp SINGLE NULL,
			birthdate_sp varchar(19) NULL,
		 	alive_flag_sp SINGLE NULL,
			re_code_sp VARCHAR(2) NULL,
			oc_code_sp VARCHAR(2) NULL,
			oc_other_sp varchar(100) NULL,
			mr_code_sp VARCHAR(2) NULL,
			doc_type_sp SINGLE NULL,
			doc_no_sp varchar(20) NULL,
			doc_date_sp varchar(19) NULL,
			doc_pv_code_sp VARCHAR(10) NULL,
			pv_code_sp VARCHAR(10) NULL,
			post_code_sp VARCHAR(5) NULL,
			cardno_f varchar(13) NULL,
			birthdate_f varchar(19) NULL,
			alive_flag_f SINGLE NULL,
			re_code_f VARCHAR(2) NULL,
			oc_code_f VARCHAR(2) NULL,
			oc_other_f varchar(100) NULL,
			type_f SINGLE NULL,
			doc_type_f SINGLE NULL,
			doc_no_f varchar(20) NULL,
			doc_date_f varchar(19) NULL,
			mr_code_f VARCHAR(2) NULL,
			mr_doc_type_f SINGLE NULL,
			mr_doc_no_f varchar(20) NULL,
			mr_doc_date_f varchar(19) NULL,
			doc_pv_code_f VARCHAR(10) NULL,
			pv_code_f VARCHAR(10) NULL,
			post_code_f VARCHAR(5) NULL,
			cardno_m varchar(13) NULL,
			birthdate_m varchar(19) NULL,
			alive_flag_m SINGLE NULL,
			re_code_m VARCHAR(2) NULL,
			oc_code_m VARCHAR(2) NULL,
			oc_other_m varchar(100) NULL,
			type_m SINGLE NULL,
			doc_type_m SINGLE NULL,
			doc_no_m varchar(20) NULL,
			doc_date_m varchar(19) NULL,
			mr_code_m VARCHAR(2) NULL,
			mr_doc_type_m SINGLE NULL,
			mr_doc_no_m varchar(20) NULL,
			mr_doc_date_m varchar(19) NULL,
			doc_pv_code_m VARCHAR(10) NULL,
			pv_code_m VARCHAR(10) NULL,
			post_code_m VARCHAR(5) NULL,
			update_user INTEGER2 NOT NULL,
			update_date varchar(19) NOT NULL,
			CONSTRAINT PK_PER_FAMILY PRIMARY KEY (family_id)) ";
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
			CONSTRAINT PK_PER_CHILD PRIMARY KEY (child_id)) ";
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
			CONSTRAINT PK_PER_CHILD PRIMARY KEY (child_id)) ";
		elseif($DPISDB=="mysql")
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
			CONSTRAINT PK_PER_CHILD PRIMARY KEY (child_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_EDUCATE ALTER EDU_TYPE VARCHAR(20) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_EDUCATE MODIFY EDU_TYPE VARCHAR2(20) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_EDUCATE MODIFY EDU_TYPE VARCHAR(20) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_EDUCATE SET EDU_TYPE = '||'+EDU_TYPE+'||' ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_EDUCATE SET EDU_TYPE = '||'||EDU_TYPE||'||' ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_EDUCATE SET EDU_TYPE = '||'||EDU_TYPE||'||' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_CONTROL ADD CTRL_TYPE SINGLE Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_CONTROL ADD CTRL_TYPE NUMBER(1) Null";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_CONTROL ADD CTRL_TYPE SMALLINT(1) Null";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_CONTROL ADD PV_CODE VARCHAR(10) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_CONTROL ADD PV_CODE VARCHAR2(10) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_CONTROL ADD PV_CODE VARCHAR(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_CONTROL DROP CONSTRAINT FK1_PER_CONTROL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_CONTROL ALTER ORG_ID INTEGER NULL ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_CONTROL MODIFY ORG_ID NUMBER(10) NULL ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_CONTROL ALTER ORG_ID INTEGER(10) NULL ";
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

		$cmd = " INSERT INTO PER_OFF_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('10', 'ลูกจ้างชั่วคราว', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('124', 'ประเภทเลื่อนระดับพนักงานราชการ', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('12410', 'เลื่อนระดับดี', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('12420', 'เลื่อนระดับดีเด่น', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('12430', 'ทำสัญญาจ้างครั้งแรก', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('12440', 'ต่อสัญญา', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('103', 'ประเภทย้าย', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
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
/*		$cmd = " INSERT INTO PER_PERFORMANCE_REVIEW (PFR_ID, PFR_NAME, PFR_YEAR, PFR_ID_REF, UPDATE_USER, UPDATE_DATE)
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
*/	
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
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LEVEL ADD LEVEL_NAME VARCHAR(100) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LEVEL ADD PER_TYPE SINGLE Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LEVEL ADD PER_TYPE NUMBER(1) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LEVEL ADD PER_TYPE SMALLINT(1) Null ";
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

			$cmd = " UPDATE PER_LAYER_O SET LEVEL_NO = '$level_no' WHERE LEVEL_NO = '$level' ";
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
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MIN DECIMAL(16,2) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MAX NUMBER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MAX NUMBER(16,2) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MAX DECIMAL(16,2) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E1', 1, 4920, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 		
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E1', 2, 5160, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E1', 3, 5400, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E1', 4, 5640, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E1', 5, 5880, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E1', 6, 6220, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E1', 7, 6560, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E1', 8, 6890, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E1', 9, 7230, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E1', 10, 7640, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E1', 11, 8040, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E1', 12, 8450, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E1', 13, 8860, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E1', 14, 9340, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E1', 15, 9830, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E1', 16, 10340, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E1', 17, 10850, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E1', 18, 11480, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E1', 19, 12100, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E1', 20, 12720, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E1', 21, 13350, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E1', 22, 14120, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E1', 23, 14880, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 1, 5640, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 2, 5880, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 3, 6220, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 4, 6560, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 5, 6890, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 6, 7230, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 7, 7640, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 	
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 8, 8040, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 9, 8450, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 10, 8860, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 11, 9340, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 12, 9830, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 13, 10340, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 14, 10850, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 15, 11480, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 16, 12100, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 17, 12720, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 18, 13350, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 19, 14120, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 	
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 20, 14880, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 21, 15650, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 22, 16420, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 23, 17190, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 24, 17960, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E2', 25, 18720, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E3', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 7640, 25920) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 			
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E4', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 7640, 33350) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'E5', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 13350, 50610) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'S1', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 0, 100000) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'S2', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 0, 150000) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX)
						  VALUES (0, 'S3', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 0, 200000) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		// update PER_RETIREDATE
		$cmd = " select PER_ID, PER_BIRTHDATE, PER_RETIREDATE from PER_PERSONAL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$PER_ID = $data[PER_ID];
			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			if($PER_BIRTHDATE){
				$arr_temp = explode("-", $PER_BIRTHDATE);
				$RETIRE_YEAR = $arr_temp[0] + 60;
				if($arr_temp[1] >= 10) $RETIRE_YEAR += 1;
				$PER_RETIREDATE = $RETIRE_YEAR."-09-30";
				if($PER_RETIREDATE != trim($data[PER_RETIREDATE])){
					$cmd = "update PER_PERSONAL set PER_RETIREDATE='$PER_RETIREDATE' where PER_ID=$PER_ID";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end if
			} // end if
		} // end while
		
		$cmd = " DROP TABLE PER_ABILITY_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_ABSENT_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_ABSENT_CONF_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_ABSENTHIS_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_BONUSPROMOTE_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_BONUSQUOTA_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_BONUSQUOTADTL1_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_BONUSQUOTADTL2_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_COMDTL_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_COMMAND_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_COURSE_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_COURSEDTL_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_DECOR_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_DECORATEHIS_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_DECORDTL_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_EDUCATE_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_EXTRAHIS_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_HEIR_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_INVEST1_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_INVEST1DTL_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_INVEST2_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_INVEST2DTL_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_LETTER_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_MARRHIS_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_MOVE_REQ_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_NAMEHIS_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_ORDER_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_ORDER_DTL_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_ORG_JOB_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_PERSONAL_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_PERSONALPIC_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_POS_EMP_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_POS_MOVE_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_POSITION_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_POSITIONHIS_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_PROMOTE_C_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_PROMOTE_E_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_PROMOTE_P_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_PUNISHMENT_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_REQ1_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_REQ1_DTL1_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_REQ1_DTL2_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_REQ2_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_REQ2_DTL_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_REQ3_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_REQ3_DTL_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_REWARDHIS_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_SALARYHIS_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_SALPROMOTE_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_SALQUOTA_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_SALQUOTADTL1_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_SALQUOTADTL2_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_SCHOLAR_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_SCHOLARINC_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_SERVICEHIS_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_SPOUSE_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_SUM_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_SUM_DTL1_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_SUM_DTL2_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_SUM_DTL3_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_SUM_DTL4_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_SUM_DTL5_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_SUM_DTL6_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_SUM_DTL7_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_SUM_DTL8_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_SUM_DTL9_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_TIMEHIS_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_TRAINING_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP TABLE PER_TRANSFER_REQ_B ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_TRAINING ALTER TRN_NO VARCHAR(15) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_TRAINING MODIFY TRN_NO VARCHAR2(15) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_TRAINING MODIFY TRN_NO VARCHAR(15) ";
		
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITION ALTER POS_CHANGE_DATE NULL ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITION MODIFY POS_CHANGE_DATE NULL ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_POSITION MODIFY POS_CHANGE_DATE NULL ";
		
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_POSITION SET POS_NO = TRIM(POS_NO) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_POSITIONHIS SET POH_POS_NO = TRIM(POH_POS_NO) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD LEVEL_NO_SALARY VARCHAR(10) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD LEVEL_NO_SALARY VARCHAR2(10) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PERSONAL ADD LEVEL_NO_SALARY VARCHAR(10) Null ";
		
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

/* Not Complete
		$cmd = " SELECT PER_ID, PER_SALARY, LEVEL_NO FROM PER_PERSONAL WHERE PER_TYPE = 1 AND PER_STATUS = 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$PER_ID = $data[PER_ID];
			$PER_SALARY = $data[PER_SALARY];
			$LEVEL_NO = $data[LEVEL_NO];
			$cmd = " SELECT DISTINCT LEVEL_NO FROM PER_LAYER WHERE LAYER_SALARY = $PER_SALARY AND LEVEL_NO ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				$data1 = $db_dpis1->get_array();
				$LEVEL_NO_SALARY = $data1[LEVEL_NO];
				if ($LEVEL_NO_SALARY) {
					$cmd = " UPDATE PER_MOVE_REQ SET ORG_ID_REF_1 = $ORG_ID_REF_1 WHERE MV_ID = $MV_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end if
			} // end if
*/
		$cmd = " UPDATE PER_PERSONAL SET LEVEL_NO_SALARY = LEVEL_NO WHERE LEVEL_NO_SALARY IS NULL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_COMDTL ADD LEVEL_NO_SALARY VARCHAR(10) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_COMDTL ADD LEVEL_NO_SALARY VARCHAR2(10) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_COMDTL ADD LEVEL_NO_SALARY VARCHAR(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_COMDTL SET LEVEL_NO_SALARY = LEVEL_NO WHERE LEVEL_NO_SALARY IS NULL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_MOVE_REQ ADD ORG_ID_REF_1 INTEGER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_MOVE_REQ ADD ORG_ID_REF_1 NUMBER(10) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_MOVE_REQ ADD ORG_ID_REF_1 INTEGER(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_MOVE_REQ ADD ORG_ID_REF_2 INTEGER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_MOVE_REQ ADD ORG_ID_REF_2 NUMBER(10) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_MOVE_REQ ADD ORG_ID_REF_2 INTEGER(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_MOVE_REQ ADD ORG_ID_REF_3 INTEGER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_MOVE_REQ ADD ORG_ID_REF_3 NUMBER(10) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_MOVE_REQ ADD ORG_ID_REF_3 INTEGER(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT MV_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3 FROM PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$MV_ID = $data[MV_ID];
			$ORG_ID_1 = $data[ORG_ID_1];
			$ORG_ID_2 = $data[ORG_ID_2];
			$ORG_ID_3 = $data[ORG_ID_3];
			if ($ORG_ID_1) {
				$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID_1 ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				$data1 = $db_dpis1->get_array();
				$ORG_ID_REF_1 = $data1[ORG_ID_REF];
				if ($ORG_ID_REF_1) {
					$cmd = " UPDATE PER_MOVE_REQ SET ORG_ID_REF_1 = $ORG_ID_REF_1 WHERE MV_ID = $MV_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end if
			} // end if
			if ($ORG_ID_2) {
				$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID_2 ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				$data1 = $db_dpis1->get_array();
				$ORG_ID_REF_2 = $data1[ORG_ID_REF];
				if ($ORG_ID_REF_2) {
					$cmd = " UPDATE PER_MOVE_REQ SET ORG_ID_REF_2 = $ORG_ID_REF_2 WHERE MV_ID = $MV_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end if
			} // end if
			if ($ORG_ID_3) {
				$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID_3 ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				$data1 = $db_dpis1->get_array();
				$ORG_ID_REF_3 = $data1[ORG_ID_REF];
				if ($ORG_ID_REF_3) {
					$cmd = " UPDATE PER_MOVE_REQ SET ORG_ID_REF_3 = $ORG_ID_REF_3 WHERE MV_ID = $MV_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end if
			} // end if
		} // end while						

		$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP) 
						  VALUES ('9999', 'คส. 99.9', 'คำสั่งปรับบัญชีเงินเดือน', '05') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_POSITION SET POS_MGTSALARY = 0 WHERE POS_MGTSALARY IS NULL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_PERFORMANCE(
			PF_CODE VARCHAR(10) NOT NULL,	
			PF_NAME VARCHAR(255) NOT NULL,
			PF_ACTIVE SINGLE NOT NULL,		
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_PERFORMANCE PRIMARY KEY (pf_code)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_PERFORMANCE(
			PF_CODE VARCHAR2(10) NOT NULL,	
			PF_NAME VARCHAR2(255) NOT NULL,
			PF_ACTIVE NUMBER(1) NOT NULL,		
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_PERFORMANCE PRIMARY KEY (pf_code)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_PERFORMANCE(
			PF_CODE VARCHAR(10) NOT NULL,	
			PF_NAME VARCHAR(255) NOT NULL,
			PF_ACTIVE SMALLINT(1) NOT NULL,		
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_PERFORMANCE PRIMARY KEY (pf_code)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_PERFORMANCE (PF_CODE, PF_NAME, PF_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						  VALUES ('01', 'บันทึกผลงานที่ได้ปฏิบัติตามหน้าที่รับผิดชอบ ซึ่งดำเนินการสำเร็จตามที่ได้รับมอบหมาย', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_PERFORMANCE (PF_CODE, PF_NAME, PF_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						  VALUES ('02', 'บันทึกผลงานที่ได้รับมอบหมายพิเศษ', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_PERFORMANCE (PF_CODE, PF_NAME, PF_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						  VALUES ('03', 'สรุปผลงานที่เห็นว่าโดดเด่นหรือผลงานสำคัญที่ปฏิบัติได้ในช่วงนี้ จากที่บันทึกไว้ไม่เกิน 6 ผลงาน', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_GOODNESS(
			GN_CODE VARCHAR(10) NOT NULL,	
			GN_NAME VARCHAR(255) NOT NULL,
			GN_ACTIVE SINGLE NOT NULL,		
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_GOODNESS PRIMARY KEY (gn_code)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_GOODNESS(
			GN_CODE VARCHAR2(10) NOT NULL,	
			GN_NAME VARCHAR2(255) NOT NULL,
			GN_ACTIVE NUMBER(1) NOT NULL,		
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_GOODNESS PRIMARY KEY (gn_code)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_GOODNESS(
			GN_CODE VARCHAR(10) NOT NULL,	
			GN_NAME VARCHAR(255) NOT NULL,
			GN_ACTIVE SMALLINT(1) NOT NULL,		
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_GOODNESS PRIMARY KEY (gn_code)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_GOODNESS (GN_CODE, GN_NAME, GN_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						  VALUES ('01', 'ปฏิบัติตนตามระเบียบวินัย จรรยาบรรณ (จรรยาบรรณและวินัยต่อตนเองและหน่วยงาน, จรรยาบรรณและวินัยต่อประชาชน) ', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_GOODNESS (GN_CODE, GN_NAME, GN_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						  VALUES ('02', 'การประพฤติ การปฏิบัติตามงานในหน้าที่รับผิดชอบ', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_GOODNESS (GN_CODE, GN_NAME, GN_ACTIVE, UPDATE_USER, UPDATE_DATE) 
						  VALUES ('03', 'พฤติกรรมการทำคุณงามความดีอื่น ๆ ที่ส่วนราชการกำหนดตามลักษณะงานของตน หรือที่มีมติคณะรัฐมนตรีกำหนดไว้ ', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_PERFORMANCE_GOODNESS(
			PG_ID INTEGER NOT NULL,	
			PER_ID INTEGER NOT NULL,	
			PER_CARDNO VARCHAR(13) NULL,
			PG_CYCLE SINGLE NOT NULL,
			PG_START_DATE VARCHAR(19) NOT NULL,
			PG_END_DATE VARCHAR(19) NOT NULL,
			PER_ID_REVIEW INTEGER NULL,
			PER_ID_REVIEW1 INTEGER NULL,	
			IPIP_DESC MEMO NULL,
			CONCLUSION_DESC MEMO NULL,
			CONCLUSION_REVIEW MEMO NULL,
			OUTSTANDING_PERFORMANCE MEMO NULL,
			OUTSTANDING_GOODNESS MEMO NULL,
			IPIP_REVIEW MEMO NULL,
			DEPARTMENT_ID INTEGER NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_PERFORMANCE_GOODNESS PRIMARY KEY (pg_id)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_PERFORMANCE_GOODNESS(
			PG_ID NUMBER(10) NOT NULL,	
			PER_ID NUMBER(10) NOT NULL,	
			PER_CARDNO VARCHAR2(13) NULL,
			PG_CYCLE NUMBER(1) NOT NULL,
			PG_START_DATE VARCHAR2(19) NOT NULL,
			PG_END_DATE VARCHAR2(19) NOT NULL,
			PER_ID_REVIEW NUMBER(10) NULL,	
			PER_ID_REVIEW1 NUMBER(10) NULL,	
			IPIP_DESC VARCHAR2(2000) NULL,
			CONCLUSION_DESC VARCHAR2(2000) NULL,
			CONCLUSION_REVIEW VARCHAR2(2000) NULL,
			OUTSTANDING_PERFORMANCE VARCHAR2(2000) NULL,
			OUTSTANDING_GOODNESS VARCHAR2(2000) NULL,
			IPIP_REVIEW VARCHAR2(2000) NULL,
			DEPARTMENT_ID NUMBER(10) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_PERFORMANCE_GOODNESS PRIMARY KEY (pg_id)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_PERFORMANCE_GOODNESS(
			PG_ID INTEGER(10) NOT NULL,	
			PER_ID INTEGER(10) NOT NULL,	
			PER_CARDNO VARCHAR(13) NULL,
			PG_CYCLE SMALLINT(1) NOT NULL,
			PG_START_DATE VARCHAR(19) NOT NULL,
			PG_END_DATE VARCHAR(19) NOT NULL,
			PER_ID_REVIEW INTEGER(10) NULL,
			PER_ID_REVIEW1 INTEGER(10) NULL,	
			IPIP_DESC TEXT NULL,
			CONCLUSION_DESC TEXT NULL,
			CONCLUSION_REVIEW TEXT NULL,
			OUTSTANDING_PERFORMANCE TEXT NULL,
			OUTSTANDING_GOODNESS TEXT NULL,
			IPIP_REVIEW TEXT NULL,
			DEPARTMENT_ID INTEGER(10) NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_PERFORMANCE_GOODNESS PRIMARY KEY (pg_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_PERFORMANCE_DTL(
			PD_ID INTEGER NOT NULL,	
			PG_ID INTEGER NOT NULL,	
			PF_CODE VARCHAR(10) NULL,
			PERFORMANCE_DESC MEMO NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_PERFORMANCE_DTL PRIMARY KEY (pd_id)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_PERFORMANCE_DTL(
			PD_ID NUMBER(10) NOT NULL,	
			PG_ID NUMBER(10) NOT NULL,	
			PF_CODE VARCHAR2(10) NULL,
			PERFORMANCE_DESC VARCHAR2(2000) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_PERFORMANCE_DTL PRIMARY KEY (pd_id)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_PERFORMANCE_DTL(
			PD_ID INTEGER(10) NOT NULL,	
			PG_ID INTEGER(10) NOT NULL,	
			PF_CODE VARCHAR(10) NULL,
			PERFORMANCE_DESC TEXT NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_PERFORMANCE_DTL PRIMARY KEY (pd_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_GOODNESS_DTL(
			GD_ID INTEGER NOT NULL,	
			PG_ID INTEGER NOT NULL,	
			GN_CODE VARCHAR(10) NULL,
			GOODNESS_DESC MEMO NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_GOODNESS_DTL PRIMARY KEY (gd_id)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_GOODNESS_DTL(
			GD_ID NUMBER(10) NOT NULL,	
			PG_ID NUMBER(10) NOT NULL,	
			GN_CODE VARCHAR2(10) NULL,
			GOODNESS_DESC VARCHAR2(2000) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_GOODNESS_DTL PRIMARY KEY (gd_id)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_GOODNESS_DTL(
			GD_ID INTEGER(10) NOT NULL,	
			PG_ID INTEGER(10) NOT NULL,	
			GN_CODE VARCHAR(10) NULL,
			GOODNESS_DESC TEXT NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_GOODNESS_DTL PRIMARY KEY (gd_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_TRAINING_DTL(
			TD_ID INTEGER NOT NULL,	
			PG_ID INTEGER NOT NULL,	
			TD_SEQ INTEGER2 NULL,
			TRAINING_DESC MEMO NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_TRAINING_DTL PRIMARY KEY (td_id)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_TRAINING_DTL(
			TD_ID NUMBER(10) NOT NULL,	
			PG_ID NUMBER(10) NOT NULL,	
			TD_SEQ NUMBER(2) NULL,
			TRAINING_DESC VARCHAR2(2000) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_TRAINING_DTL PRIMARY KEY (td_id)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_TRAINING_DTL(
			TD_ID INTEGER(10) NOT NULL,	
			PG_ID INTEGER(10) NOT NULL,	
			TD_SEQ SMALLINT(2) NULL,
			TRAINING_DESC TEXT NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_TRAINING_DTL PRIMARY KEY (td_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE  PER_PRENAME DROP CONSTRAINT INXU1_PER_PRENAME ";
		elseif($DPISDB=="oci8") 
			$cmd = " DROP INDEX INXU1_PER_PRENAME ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE  PER_PRENAME DROP CONSTRAINT INXU1_PER_PRENAME ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " UPDATE  PER_PERSONAL SET PER_ORDAIN = 0 WHERE PER_ORDAIN IS NULL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " UPDATE  PER_PERSONAL SET PER_SOLDIER = 0 WHERE PER_SOLDIER IS NULL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " UPDATE  PER_PERSONAL SET PER_MEMBER = 0 WHERE PER_MEMBER IS NULL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " UPDATE  PER_PERSONAL SET LEVEL_NO_SALARY = '01' WHERE LEVEL_NO_SALARY = '1' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " UPDATE  PER_PERSONAL SET LEVEL_NO_SALARY = '02' WHERE LEVEL_NO_SALARY = '2' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " UPDATE  PER_PERSONAL SET LEVEL_NO_SALARY = '03' WHERE LEVEL_NO_SALARY = '3' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " UPDATE  PER_PERSONAL SET LEVEL_NO_SALARY = '04' WHERE LEVEL_NO_SALARY = '4' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " UPDATE  PER_PERSONAL SET LEVEL_NO_SALARY = '05' WHERE LEVEL_NO_SALARY = '5' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " UPDATE  PER_PERSONAL SET LEVEL_NO_SALARY = '06' WHERE LEVEL_NO_SALARY = '6' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " UPDATE  PER_PERSONAL SET LEVEL_NO_SALARY = '07' WHERE LEVEL_NO_SALARY = '7' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " UPDATE  PER_PERSONAL SET LEVEL_NO_SALARY = '08' WHERE LEVEL_NO_SALARY = '8' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " UPDATE  PER_PERSONAL SET LEVEL_NO_SALARY = '09' WHERE LEVEL_NO_SALARY = '9' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_gpis.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 7, 'P1107 ระบบสารสนเทศเพื่อการวางแผนกำลังคนภาครัฐ', 'S', 'W', 'rpt_gpis.html', 0, 35, 251, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 7, 'P1107 ระบบสารสนเทศเพื่อการวางแผนกำลังคนภาครัฐ', 'S', 'W', 'rpt_gpis.html', 0, 35, 251, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'master_table_update_code.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 7, 'C0707 ปรับปรุงรหัสข้อมูลหลักให้เป็นมาตรฐาน', 'S', 'W', 'master_table_update_code.html', 0, 1, 305, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 7, 'C0707 ปรับปรุงรหัสข้อมูลหลักให้เป็นมาตรฐาน', 'S', 'W', 'master_table_update_code.html', 0, 1, 305, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_SQL(
			SQL_CODE VARCHAR(3) NOT NULL,	
			SQL_NAME VARCHAR(100) NOT NULL,	
			SQL_CMD MEMO NULL,	
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_SQL PRIMARY KEY (sql_code)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_SQL(
			SQL_CODE VARCHAR2(3) NOT NULL,	
			SQL_NAME VARCHAR2(100) NOT NULL,	
			SQL_CMD VARCHAR2(1000) NULL,	
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_SQL PRIMARY KEY (sql_code)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_SQL(
			SQL_CODE VARCHAR(3) NOT NULL,	
			SQL_NAME VARCHAR(100) NOT NULL,	
			SQL_CMD TEXT NULL,	
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_SQL PRIMARY KEY (sql_code)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_SUM_DTL10(
			SUM_ID INTEGER NOT NULL,	
			PL_CODE VARCHAR(10) NOT NULL,	
			LEVEL_NO VARCHAR(10) NOT NULL,	
			EL_CODE VARCHAR(10) NOT NULL,	
			SUM_QTY_M INTEGER NULL,	
			SUM_QTY_F INTEGER NULL,	
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_SUM_DTL10 PRIMARY KEY (sum_id, pl_code, level_no, el_code)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_SUM_DTL10(
			SUM_ID NUMBER(10) NOT NULL,	
			PL_CODE VARCHAR2(10) NOT NULL,	
			LEVEL_NO VARCHAR2(10) NOT NULL,	
			EL_CODE VARCHAR2(10) NOT NULL,	
			SUM_QTY_M NUMBER(10) NULL,	
			SUM_QTY_F NUMBER(10) NULL,	
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_SUM_DTL10 PRIMARY KEY (sum_id, pl_code, level_no, el_code)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_SUM_DTL10(
			SUM_ID INTEGER(10) NOT NULL,	
			PL_CODE VARCHAR(10) NOT NULL,	
			LEVEL_NO VARCHAR(10) NOT NULL,	
			EL_CODE VARCHAR(10) NOT NULL,	
			SUM_QTY_M INTEGER(10) NULL,	
			SUM_QTY_F INTEGER(10) NULL,	
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_SUM_DTL10 PRIMARY KEY (sum_id, pl_code, level_no, el_code)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('001', 'ปลัดกระทรวงหรือเทียบเท่า', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " UPDATE PER_MGT SET PS_CODE = '001' WHERE PM_CODE IN ('0106', '0108', '0109') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('002', 'อธิบดีหรือเทียบเท่า', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " UPDATE PER_MGT SET PS_CODE = '002' WHERE PM_CODE IN ('0357', '0278', '0282') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('003', 'รองปลัดกระทรวงหรือเทียบเท่า', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " UPDATE PER_MGT SET PS_CODE = '003' WHERE PM_CODE IN ('0266', '0267', '0268') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('004', 'ผู้ว่าราชการจังหวัด', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " UPDATE PER_MGT SET PS_CODE = '004' WHERE PM_CODE IN ('0233') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('005', 'เอกอัครราชทูต', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " UPDATE PER_MGT SET PS_CODE = '005' WHERE PM_CODE IN ('0362', '0363', '0364', '0365') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('006', 'ผู้ตรวจราชการระดับกระทรวง', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " UPDATE PER_MGT SET PS_CODE = '006' WHERE PM_CODE IN ('0216', '0218', '0219') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('007', 'อื่นๆ (ตำแหน่งประเภทบริหารระดับสูง)', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
/*
		$cmd = " UPDATE PER_MGT SET PS_CODE = '007' WHERE PM_CODE IN ('', '', '') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
*/
		$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('008', 'รองอธิบดีหรือเทียบเท่า', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " UPDATE PER_MGT SET PS_CODE = '008' WHERE PM_CODE IN ('0273', '0274', '0276') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('009', 'รองผู้ว่าราชการจังหวัดหรือเทียบเท่า', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " UPDATE PER_MGT SET PS_CODE = '009' WHERE PM_CODE IN ('0269') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('010', 'อัครราชทูต', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " UPDATE PER_MGT SET PS_CODE = '010' WHERE PM_CODE IN ('0359', '0360') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('011', 'อื่นๆ (ตำแหน่งประเภทบริหารระดับต้น)', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
/*
		$cmd = " UPDATE PER_MGT SET PS_CODE = '011' WHERE PM_CODE IN ('', '', '') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
*/
		$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('012', 'ผอ.สำนักหรือเทียบเท่า', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " UPDATE PER_MGT SET PS_CODE = '012' WHERE PM_CODE IN ('0251', '0252', '0253') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('013', 'หัวหน้าส่วนราชการประจำจังหวัด', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
/*	
		$cmd = " UPDATE PER_MGT SET PS_CODE = '013' WHERE PM_CODE IN ('', '', '') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
*/	
		$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('014', 'อื่นๆ (ตำแหน่งประเภทอำนวยการระดับสูง)', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
/*	
		$cmd = " UPDATE PER_MGT SET PS_CODE = '014' WHERE PM_CODE IN ('', '', '') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
*/	
		$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('015', 'ผู้อำนวยการกองหรือเทียบเท่า', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " UPDATE PER_MGT SET PS_CODE = '015' WHERE PM_CODE IN ('0235', '0237', '0249') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('016', 'หัวหน้าส่วนราชการประจำจังหวัด', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
/*	
		$cmd = " UPDATE PER_MGT SET PS_CODE = '016' WHERE PM_CODE IN ('', '', '') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
*/	
		$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('017', 'อื่นๆ (ตำแหน่งประเภทอำนวยการระดับต้น)', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
/*	
		$cmd = " UPDATE PER_MGT SET PS_CODE = '017' WHERE PM_CODE IN ('', '', '') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
*/	
		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LEVEL ADD LEVEL_SHORTNAME VARCHAR(20) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LEVEL ADD LEVEL_SHORTNAME VARCHAR2(20) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LEVEL ADD LEVEL_SHORTNAME VARCHAR(20) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LEVEL ADD LEVEL_SEQ_NO INTEGER2 Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LEVEL ADD LEVEL_SEQ_NO NUMBER(2) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LEVEL ADD LEVEL_SEQ_NO SMALLINT(2) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '1', LEVEL_SEQ_NO = 1 WHERE LEVEL_NO = '01' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '2', LEVEL_SEQ_NO = 2 WHERE LEVEL_NO = '02' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '3', LEVEL_SEQ_NO = 3 WHERE LEVEL_NO = '03' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '4', LEVEL_SEQ_NO = 4 WHERE LEVEL_NO = '04' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '5', LEVEL_SEQ_NO = 5 WHERE LEVEL_NO = '05' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '6', LEVEL_SEQ_NO = 6 WHERE LEVEL_NO = '06' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '7', LEVEL_SEQ_NO = 7 WHERE LEVEL_NO = '07' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '8', LEVEL_SEQ_NO = 8 WHERE LEVEL_NO = '08' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '9', LEVEL_SEQ_NO = 9 WHERE LEVEL_NO = '09' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '10', LEVEL_SEQ_NO = 10 WHERE LEVEL_NO = '10' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '11', LEVEL_SEQ_NO = 11 WHERE LEVEL_NO = '11' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'ปง.', LEVEL_SEQ_NO = 21 WHERE LEVEL_NO = 'O1' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'ชง.', LEVEL_SEQ_NO = 22 WHERE LEVEL_NO = 'O2' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'อว.', LEVEL_SEQ_NO = 23 WHERE LEVEL_NO = 'O3' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'ทพ.', LEVEL_SEQ_NO = 24 WHERE LEVEL_NO = 'O4' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'ปก.', LEVEL_SEQ_NO = 31 WHERE LEVEL_NO = 'K1' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'ชก.', LEVEL_SEQ_NO = 32 WHERE LEVEL_NO = 'K2' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'ชพ.', LEVEL_SEQ_NO = 33 WHERE LEVEL_NO = 'K3' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'ชช.', LEVEL_SEQ_NO = 34 WHERE LEVEL_NO = 'K4' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'ทว.', LEVEL_SEQ_NO = 35 WHERE LEVEL_NO = 'K5' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'อต.', LEVEL_SEQ_NO = 41 WHERE LEVEL_NO = 'D1' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'อส.', LEVEL_SEQ_NO = 42 WHERE LEVEL_NO = 'D2' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'บต.', LEVEL_SEQ_NO = 51 WHERE LEVEL_NO = 'M1' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'บส.', LEVEL_SEQ_NO = 52 WHERE LEVEL_NO = 'M2' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'E1', LEVEL_SEQ_NO = 61 WHERE LEVEL_NO = 'E1' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'E2', LEVEL_SEQ_NO = 62 WHERE LEVEL_NO = 'E2' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'E3', LEVEL_SEQ_NO = 63 WHERE LEVEL_NO = 'E3' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'E4', LEVEL_SEQ_NO = 64 WHERE LEVEL_NO = 'E4' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'E5', LEVEL_SEQ_NO = 65 WHERE LEVEL_NO = 'E5' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'S1', LEVEL_SEQ_NO = 71 WHERE LEVEL_NO = 'S1' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'S2', LEVEL_SEQ_NO = 72 WHERE LEVEL_NO = 'S2' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = 'S3', LEVEL_SEQ_NO = 73 WHERE LEVEL_NO = 'S3' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONAL ALTER PER_TAXNO VARCHAR(13) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONAL MODIFY PER_TAXNO VARCHAR2(13) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PERSONAL MODIFY PER_TAXNO VARCHAR(13) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_NICKNAME VARCHAR(20) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_NICKNAME VARCHAR2(20) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_NICKNAME VARCHAR(20) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_HOME_TEL VARCHAR(50) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_HOME_TEL VARCHAR2(50) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_HOME_TEL VARCHAR(50) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_OFFICE_TEL VARCHAR(30) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_OFFICE_TEL VARCHAR2(30) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_OFFICE_TEL VARCHAR(30) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_FAX VARCHAR(20) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_FAX VARCHAR2(20) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_FAX VARCHAR(20) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_MOBILE VARCHAR(20) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_MOBILE VARCHAR2(20) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_MOBILE VARCHAR(20) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_EMAIL VARCHAR(30) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_EMAIL VARCHAR2(30) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PERSONAL ADD PER_EMAIL VARCHAR(30) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_PERCENT_UP NUMBER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_PERCENT_UP NUMBER(5,2) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_PERCENT_UP DECIMAL(5,2) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_SALARY_UP NUMBER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_SALARY_UP NUMBER(16,2) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_SALARY_UP DECIMAL(16,2) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SALARYHIS ALTER SAH_DOCNO VARCHAR(100) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SALARYHIS MODIFY SAH_DOCNO VARCHAR2(100) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_SALARYHIS MODIFY SAH_DOCNO VARCHAR(100) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_DOCNO VARCHAR(100) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_DOCNO VARCHAR2(100) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_DOCNO VARCHAR(100) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_REMARK MEMO ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_REMARK VARCHAR2(1000) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_REMARK TEXT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, 
						  PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'TH', 308, 14, 'P14 จัดคนลง', 'S', 'N', 0, 35, $CREATE_DATE,'$SESS_USERNAME', $CREATE_DATE,'$SESS_USERNAME') ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, 
						  PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'EN', 308, 14, 'P14 จัดคนลง', 'S', 'N', 0, 35, $CREATE_DATE,'$SESS_USERNAME', $CREATE_DATE,'$SESS_USERNAME') ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_map_type_new_check.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 1, 'P1401 ตรวจสอบการจัดคนลงตาม พรบ.ใหม่', 'S', 'W', 'data_map_type_new_check.html', 0, 35, 308, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 			
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 1, 'P1401 ตรวจสอบการจัดคนลงตาม พรบ.ใหม่', 'S', 'W', 'data_map_type_new_check.html', 0, 35, 308, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_map_type_new_comdtl.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 1, 'P1402 บัญชีแนบท้ายคำสั่งจัดคนลงตาม พรบ.ใหม่', 'S', 'W', 'data_map_type_new_comdtl.html', 0, 35, 308, 	
							  $CREATE_DATE,'$SESS_USERNAME', $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 1, 'P1402 บัญชีแนบท้ายคำสั่งจัดคนลงตาม พรบ.ใหม่', 'S', 'W', 'data_map_type_new_comdtl.html', 0, 35, 308, 	
							  $CREATE_DATE,'$SESS_USERNAME', $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		} else {
			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_ORDER = 2, MENU_LABEL = 'P1402 บัญชีแนบท้ายคำสั่งจัดคนลงตาม พรบ.ใหม่' 
							  WHERE LINKTO_WEB = 'data_map_type_new_comdtl.html' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
						  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'TH', 242, 7, 'M0407 บัญชีอัตราเงินเดือนลูกจ้างใหม่', 'S', 'W', 'master_table_layeremp.html?table=PER_LAYEREMP_NEW', 0, 9, 297, 
						  $CREATE_DATE,'$SESS_USERNAME', $CREATE_DATE,'$SESS_USERNAME') ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
						  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'EN', 242, 7, 'M0407 บัญชีอัตราเงินเดือนลูกจ้างใหม่', 'S', 'W', 'master_table_layeremp.html?table=PER_LAYEREMP_NEW', 0, 9, 297, 
						  $CREATE_DATE,'$SESS_USERNAME', $CREATE_DATE,'$SESS_USERNAME') ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
						  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'TH', 309, 9, 'K09 ผลงาน', 'S', 'W', 'master_table.html?table=PER_PERFORMANCE', 0, 40, $CREATE_DATE,'$SESS_USERNAME', 
						  $CREATE_DATE,'$SESS_USERNAME') ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
						  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'EN', 309, 9, 'K09 ผลงาน', 'S', 'W', 'master_table.html?table=PER_PERFORMANCE', 0, 40, $CREATE_DATE,'$SESS_USERNAME', 
						  $CREATE_DATE,'$SESS_USERNAME') ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
						  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'TH', 310, 10, 'K10 คุณงามความดี', 'S', 'W', 'master_table.html?table=PER_GOODNESS', 0, 40, $CREATE_DATE,'$SESS_USERNAME', 
						  $CREATE_DATE,'$SESS_USERNAME') ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
						  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'EN', 310, 10, 'K10 คุณงามความดี', 'S', 'W', 'master_table.html?table=PER_GOODNESS', 0, 40, $CREATE_DATE,'$SESS_USERNAME', 
						  $CREATE_DATE,'$SESS_USERNAME') ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 		
						  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'TH', 311, 11, 'K11 สมุดบันทึกผลงานและคุณงามความดี', 'S', 'W', 'book_form.html', 0, 40, $CREATE_DATE,'$SESS_USERNAME', 
						  $CREATE_DATE,'$SESS_USERNAME') ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
						  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'EN', 311, 11, 'K11 สมุดบันทึกผลงานและคุณงามความดี', 'S', 'W', 'book_form.html', 0, 40, $CREATE_DATE,'$SESS_USERNAME', 
						  $CREATE_DATE,'$SESS_USERNAME') ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
						  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'TH', 246, 6, 'C0706 SQL Command', 'S', 'W', 'master_table_sql.html?table=PER_SQL', 0, 1, 305, $CREATE_DATE,'$SESS_USERNAME', 
						  $CREATE_DATE,'$SESS_USERNAME') ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
						  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'EN', 246, 6, 'C0706 SQL Command', 'S', 'W', 'master_table_sql.html?table=PER_SQL', 0, 1, 305, $CREATE_DATE,'$SESS_USERNAME', 
						  $CREATE_DATE,'$SESS_USERNAME') ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
						  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'TH', 255, 11, 'P0311 บัญชีแนบท้ายคำสั่งจัดคนลงตามโครงสร้างส่วนราชการใหม่', 'S', 'W', 'data_move_comdtl_N.html', 0, 35, 243, 
						  $CREATE_DATE,'$SESS_USERNAME', $CREATE_DATE,'$SESS_USERNAME') ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 	
						  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'EN', 255, 11, 'P0311 บัญชีแนบท้ายคำสั่งจัดคนลงตามโครงสร้างส่วนราชการใหม่', 'S', 'W', 'data_move_comdtl_N.html', 0, 35, 243, 
						  $CREATE_DATE,'$SESS_USERNAME', $CREATE_DATE,'$SESS_USERNAME') ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
						  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'TH', 244, 13, 'M0313 สายงาน', 'S', 'W', 'master_table.html?table=PER_LINE_GROUP', 0, 9, 295, 
						  $CREATE_DATE,'$SESS_USERNAME', $CREATE_DATE,'$SESS_USERNAME') ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
						  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'EN', 244, 13, 'M0313 สายงาน', 'S', 'W', 'master_table.html?table=PER_LINE_GROUP', 0, 9, 295, 
						  $CREATE_DATE,'$SESS_USERNAME', $CREATE_DATE,'$SESS_USERNAME') ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_TRAIN ALTER TR_NAME MEMO ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_TRAIN MODIFY TR_NAME VARCHAR2(1000) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_TRAIN ALTER TR_NAME TEXT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('21', 'K2', 3500, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('21', 'K3', 5600, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('21', 'K4', 9900, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('21', 'K5', 13000, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('21', 'K5', 15600, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('22', 'O4', 9900, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('22', 'K4', 9900, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('22', 'K5', 13000, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('22', 'K5', 15600, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('31', 'D1', 5600, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('32', 'D2', 10000, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('32', 'S2', 14500, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('32', 'S2', 21000, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV0 WHERE LINKTO_WEB = '../help/dpis_menu/dpis_menu.htm' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV0 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV0 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 12, 'ระบบให้ความช่วยเหลือ', 'S', 'W', '../help/dpis_menu/dpis_menu.htm', 0, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV0 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 12, 'ระบบให้ความช่วยเหลือ', 'S', 'W', '../help/dpis_menu/dpis_menu.htm', 0, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = 'C0704 โปรแกรมปรับเปลี่ยนฐานข้อมูล' 
						  WHERE LINKTO_WEB = 'alter_table.html' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_TEMP NUMBER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_TEMP NUMBER(16,2) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_TEMP DECIMAL(16,2) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_TEMP NUMBER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_TEMP NUMBER(16,2) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_TEMP DECIMAL(16,2) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_FULL NUMBER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_FULL NUMBER(16,2) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_FULL DECIMAL(16,2) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_FULL NUMBER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_FULL NUMBER(16,2) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_FULL DECIMAL(16,2) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MIDPOINT NUMBER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MIDPOINT NUMBER(16,2) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MIDPOINT DECIMAL(16,2) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_MIDPOINT NUMBER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_MIDPOINT NUMBER(16,2) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_MIDPOINT DECIMAL(16,2) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MIDPOINT1 NUMBER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MIDPOINT1 NUMBER(16,2) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MIDPOINT1 DECIMAL(16,2) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_MIDPOINT1 NUMBER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_MIDPOINT1 NUMBER(16,2) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_MIDPOINT1 DECIMAL(16,2) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MIDPOINT2 NUMBER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MIDPOINT2 NUMBER(16,2) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MIDPOINT2 DECIMAL(16,2) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_MIDPOINT2 NUMBER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_MIDPOINT2 NUMBER(16,2) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_MIDPOINT2 DECIMAL(16,2) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
						  VALUES (0, 'O1', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 4630, 18190, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
						  VALUES (0, 'O2', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 10190, 33540, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
						  VALUES (0, 'O3', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 15410, 47450, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
						  VALUES (0, 'O4', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 48220, 59770, NULL) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
						  VALUES (0, 'K1', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 7940, 22220, 6800) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
						  VALUES (0, 'K2', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 14330, 36020, 12530) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
						  VALUES (0, 'K3', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 21080, 50550, 18910) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
						  VALUES (0, 'K4', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 29900, 59770, 23230) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
						  VALUES (0, 'K5', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 41720, 66480, 28550) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
						  VALUES (0, 'D1', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 25390, 50550, 18910) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
						  VALUES (0, 'D2', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 31280, 59770, 23230) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
						  VALUES (0, 'M1', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 48700, 64340, 23230) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						  LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
						  VALUES (0, 'M2', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 53690, 66480, 28550) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 13265, LAYER_SALARY_MIDPOINT1 = 10790, LAYER_SALARY_MIDPOINT2 = 15730 WHERE LAYER_TYPE = 0  AND LEVEL_NO = 'O1' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 21875, LAYER_SALARY_MIDPOINT1 = 16030, LAYER_SALARY_MIDPOINT2 = 27710 WHERE LAYER_TYPE = 0  AND LEVEL_NO = 'O2' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 31435, LAYER_SALARY_MIDPOINT1 = 28270, LAYER_SALARY_MIDPOINT2 = 39440, LAYER_SALARY_FULL = 36020 WHERE LAYER_TYPE = 0  AND LEVEL_NO = 'O3' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 54005, LAYER_SALARY_MIDPOINT1 = 51110, LAYER_SALARY_MIDPOINT2 = 56890 WHERE LAYER_TYPE = 0  AND LEVEL_NO = 'O4' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 17675, LAYER_SALARY_MIDPOINT1 = 15390, LAYER_SALARY_MIDPOINT2 = 19950 WHERE LAYER_TYPE = 0  AND LEVEL_NO = 'K1' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 25185, LAYER_SALARY_MIDPOINT1 = 20350, LAYER_SALARY_MIDPOINT2 = 30600 WHERE LAYER_TYPE = 0  AND LEVEL_NO = 'K2' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 35825, LAYER_SALARY_MIDPOINT1 = 31220, LAYER_SALARY_MIDPOINT2 = 43190 WHERE LAYER_TYPE = 0  AND LEVEL_NO = 'K3' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 44845, LAYER_SALARY_MIDPOINT1 = 44060, LAYER_SALARY_MIDPOINT2 = 52310 WHERE LAYER_TYPE = 0  AND LEVEL_NO = 'K4' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 54105, LAYER_SALARY_MIDPOINT1 = 53360, LAYER_SALARY_MIDPOINT2 = 60290, LAYER_SALARY_FULL = 64340 WHERE LAYER_TYPE = 0  AND LEVEL_NO = 'K5' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 37975, LAYER_SALARY_MIDPOINT1 = 31680, LAYER_SALARY_MIDPOINT2 = 44260 WHERE LAYER_TYPE = 0  AND LEVEL_NO = 'D1' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 45535, LAYER_SALARY_MIDPOINT1 = 45150, LAYER_SALARY_MIDPOINT2 = 52650 WHERE LAYER_TYPE = 0  AND LEVEL_NO = 'D2' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 56525, LAYER_SALARY_MIDPOINT1 = 52650, LAYER_SALARY_MIDPOINT2 = 60430 WHERE LAYER_TYPE = 0  AND LEVEL_NO = 'M1' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 61645, LAYER_SALARY_MIDPOINT1 = 61640, LAYER_SALARY_MIDPOINT2 = 63290 WHERE LAYER_TYPE = 0  AND LEVEL_NO = 'M2' AND LAYER_NO = 0 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010007.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 7, 'R1007 จำนวนข้าราชการพลเรือน แยกตามเพศ', 'S', 'W', 'rpt_R010007.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 7, 'R1007 จำนวนข้าราชการพลเรือน แยกตามเพศ', 'S', 'W', 'rpt_R010007.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010008.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 8, 'R1008 จำนวนข้าราชการพลเรือน แยกตามระดับการศึกษา', 'S', 'W', 'rpt_R010008.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 8, 'R1008 จำนวนข้าราชการพลเรือน แยกตามระดับการศึกษา', 'S', 'W', 'rpt_R010008.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010009.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 9, 'R1009 จำนวนข้าราชการพลเรือน แยกตามระดับตำแหน่ง', 'S', 'W', 'rpt_R010009.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 9, 'R1009 จำนวนข้าราชการพลเรือน แยกตามระดับตำแหน่ง', 'S', 'W', 'rpt_R010009.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010010.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 10, 'R1010 บัญชีรายชื่อข้าราชการผู้มีสิทธิ์ได้รับบำเหน็จบำนาญซึ่งจะมีอายุครบ 60 ปีบริบูรณ์', 'S', 'W', 'rpt_R010010.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 10, 'R1010 บัญชีรายชื่อข้าราชการผู้มีสิทธิ์ได้รับบำเหน็จบำนาญซึ่งจะมีอายุครบ 60 ปีบริบูรณ์', 'S', 'W', 'rpt_R010010.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010011.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 11, 'R1011 รายชื่อข้าราชการที่ผ่านการอบรมหลักสูตร นบส. หรือเทียบเท่า', 'S', 'W', 'rpt_R010011.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 11, 'R1011 รายชื่อข้าราชการที่ผ่านการอบรมหลักสูตร นบส. หรือเทียบเท่า', 'S', 'W', 'rpt_R010011.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010012.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 12, 'R1012 จำนวนข้าราชการที่จะเกษียณอายุ ประจำปีงบประมาณ', 'S', 'W', 'rpt_R010012.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 12, 'R1012 จำนวนข้าราชการที่จะเกษียณอายุ ประจำปีงบประมาณ', 'S', 'W', 'rpt_R010012.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010013.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 13, 'R1013 รายงานผู้ดำรงตำแหน่งระดับสูงทุกประเภทตำแหน่ง ในส่วนราชการระดับกรม', 'S', 'W', 'rpt_R010013.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 13, 'R1013 รายงานผู้ดำรงตำแหน่งระดับสูงทุกประเภทตำแหน่ง ในส่วนราชการระดับกรม', 'S', 'W', 'rpt_R010013.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010014.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 14, 'R1014 รายชื่อข้าราชการพลเรือนสามัญดำรงตำแหน่งประเภท ~ ระดับ ~', 'S', 'W', 'rpt_R010014.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 14, 'R1014 รายชื่อข้าราชการพลเรือนสามัญดำรงตำแหน่งประเภท ~ ระดับ ~', 'S', 'W', 'rpt_R010014.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010015.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 15, 'R1015 รายชื่อผู้ดำรงตำแหน่งประเภทบริหาร', 'S', 'W', 'rpt_R010015.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 15, 'R1015 รายชื่อผู้ดำรงตำแหน่งประเภทบริหาร', 'S', 'W', 'rpt_R010015.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010016.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 16, 'R1016 รายชื่อข้าราชการที่ดำรงตำแหน่งประเภทบริหาร ที่ดำรงตำแหน่งครบ ~ ปี', 'S', 'W', 'rpt_R010016.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 16, 'R1016 รายชื่อข้าราชการที่ดำรงตำแหน่งประเภทบริหาร ที่ดำรงตำแหน่งครบ ~ ปี', 'S', 'W', 'rpt_R010016.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010017.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 17, 'R1017 ข้อมูลสายงาน ประเภทตำแหน่ง แสดงวุฒิการศึกษาและความเชี่ยวชาญพิเศษ', 'S', 'W', 'rpt_R010017.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 17, 'R1017 ข้อมูลสายงาน ประเภทตำแหน่ง แสดงวุฒิการศึกษาและความเชี่ยวชาญพิเศษ', 'S', 'W', 'rpt_R010017.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010018.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 18, 'R1018 ผู้ดำรงตำแหน่งประเภทบริหาร อำนวยการระดับสูง วิชาการระดับเชี่ยวชาญขึ้นไปและทั่วไประดับทักษะพิเศษ', 'S', 'W', 'rpt_R010018.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 18, 'R1018 ผู้ดำรงตำแหน่งประเภทบริหาร อำนวยการระดับสูง วิชาการระดับเชี่ยวชาญขึ้นไปและทั่วไประดับทักษะพิเศษ', 'S', 'W', 'rpt_R010018.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010019.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 19, 'R1019 บัญชีรายชื่อข้าราชการ', 'S', 'W', 'rpt_R010019.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 19, 'R1019 บัญชีรายชื่อข้าราชการ', 'S', 'W', 'rpt_R010019.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010020.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 20, 'R1020 จำนวนข้าราชการที่จะเกษียณอายุ ประจำปี', 'S', 'W', 'rpt_R010020.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 20, 'R1020 จำนวนข้าราชการที่จะเกษียณอายุ ประจำปี', 'S', 'W', 'rpt_R010020.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R010021.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 21, 'R1021 จำนวนข้าราชการที่จะเกษียณอายุ จำแนกตามตำแหน่งประเภท และเพศ', 'S', 'W', 'rpt_R010021.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 21, 'R1021 จำนวนข้าราชการที่จะเกษียณอายุ จำแนกตามตำแหน่งประเภท และเพศ', 'S', 'W', 'rpt_R010021.html', 0, 36, 307, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

//		$cmd = " DELETE FROM PER_CO_LEVEL WHERE LEVEL_NO_MIN in ('O1','O2','O3','O4','K1','K2','K3','K4','K5','D1','D2','M1','M2') ";
//		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('บริหารระดับต้น - บริหารระดับสูง', 'M1', 'M2', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('บริหารระดับสูง', 'M2', 'M2', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('อำนวยการระดับต้น - อำนวยการระดับสูง', 'D1', 'D2', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('อำนวยการระดับสูง', 'D2', 'D2', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('ปฏิบัติการ - ทรงคุณวุฒิ', 'K1', 'K5', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('ปฏิบัติการ - เชี่ยวชาญ', 'K1', 'K4', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('ปฏิบัติการ - ชำนาญการพิเศษ', 'K1', 'K3', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (ชำนาญการ - เชี่ยวชาญ', 'K2', 'K4', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (ชำนาญการ - ชำนาญการพิเศษ', 'K2', 'K3', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('ปฏิบัติงาน - ทักษะพิเศษ', 'O2', 'O4', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('ปฏิบัติงาน - อาวุโส', 'O1', 'O3', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('ปฏิบัติงาน - ชำนาญงาน', 'O1', 'O2', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LINE ADD PL_TYPE SINGLE Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LINE ADD PL_TYPE NUMBER(1) Null ";
		elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE PER_LINE ADD PL_TYPE SMALLINT(1) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LINE ADD PL_CODE_NEW VARCHAR(10) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LINE ADD PL_CODE_NEW VARCHAR2(10) Null ";
		elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE PER_LINE ADD PL_CODE_NEW VARCHAR(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LINE ADD LG_CODE VARCHAR(10) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LINE ADD LG_CODE VARCHAR2(10) Null ";
		elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE PER_LINE ADD LG_CODE VARCHAR(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LINE ADD PL_CODE_DIRECT VARCHAR(10) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LINE ADD PL_CODE_DIRECT VARCHAR2(10) Null ";
		elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE PER_LINE ADD PL_CODE_DIRECT VARCHAR(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LINE ADD CL_NAME VARCHAR(50) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LINE ADD CL_NAME VARCHAR2(50) Null ";
		elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE PER_LINE ADD CL_NAME VARCHAR(50) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET CL_NAME = 'บริหารระดับต้น - บริหารระดับสูง' WHERE PL_CODE IN ('510108','510307','510109') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET CL_NAME = 'บริหารระดับสูง' WHERE PL_CODE IN ('510209') ";
		$db_dpis->send_cmd($cmd);          
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET CL_NAME = 'อำนวยการระดับต้น - อำนวยการระดับสูง' WHERE PL_CODE IN (
		'510308','511019','512009','512409','512629','520109','520309','520429','520609','520829','521009','521709','522009','522209',		
		'522729','523509','523909','530309','540109','540209','540319','540329','540509','540709','541009','541449','550109','550229',
		'550409','550909','551009','551109','551309','551409','551609','551809','560109','560209','560309','560409','560609','560709',
		'561519','562109','570109','570209','570409','570509','570609','570709','570909','571009','571319','571419','571519','571529',
		'571629','572709','573519','573809','574009','574429','574519','575109','575309','575409','581109','581209','581309','581619',
		'581909','582109','582209','582309','582409','584209','584409') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET CL_NAME = 'อำนวยการระดับสูง' WHERE PL_CODE IN ('510210') ";
		$db_dpis->send_cmd($cmd);          
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET CL_NAME = 'ปฏิบัติการ - ทรงคุณวุฒิ' WHERE PL_CODE IN (
		'513405','510903','512403','513503','510703','511013','520903','520104','520103','520303','522423','521003','523903','521203',
		'522003','522103','521423','521523','531923','530103','540103','540703','540503','540323','550103','560204','560304','560104',
		'562503','560703','560403','575403','570903','570203','571623','573803','583503','581603','583203','582403','580103','581103') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET CL_NAME = 'ปฏิบัติการ - เชี่ยวชาญ' WHERE PL_CODE IN (
		'513303','512703','511014','512833','512003','513003','512603','511203','521703','520423','520823','520603','522513','523623',
		'523503','522203','523203','531813','541503','541443','541003','540203','551103','550803','551303','550223','550503','550303',
		'550403','550903','551403','551803','551003','551603','561803','561914','561203','561204','563603','561523','560603','560813',
		'561723','561514','575303','573813','574003','571303','574423','575103','570704','570103','570403','571003','570703','570503',
		'573123','570803','570603','584403','580913','580923','584578','581903','581703','581803','583403','584123','583603','580823',
		'582623','582723','580513','584003','582903') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET CL_NAME = 'ปฏิบัติการ - ชำนาญการพิเศษ' WHERE PL_CODE IN (
		'512903','511104','512613','511503','510403','511723','510404','513103','521903','522723','530503','530403','532423','532523',
		'532003','560105','561913','561915','575612','572203','575003','572703','572503','574213','571523','581203','581303','584203',
		'580723','580403','092205','092206','092207','092208','092209','092210') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET CL_NAME = 'ชำนาญการ - เชี่ยวชาญ' WHERE PL_CODE IN ('512626') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET CL_NAME = 'ชำนาญการ - ชำนาญการพิเศษ' WHERE PL_CODE IN ('530306') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET CL_NAME = 'ปฏิบัติงาน - ทักษะพิเศษ' WHERE PL_CODE IN ('574512','582301','582201','582101') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET CL_NAME = 'ปฏิบัติงาน - อาวุโส' WHERE PL_CODE IN (
		'511612','511712','512812','512302','512212','522412','520412','520212','522712','520502','520812','523612','521301','522312',
		'523302','521412','521512','530212','531801','531212','532512','540412','541412','540312','540912','540612','541112','550212',
		'551712','551512','560802','563502','562102','574802','572901','571812','574312','571912','572612','573512','573012','571412',
		'571512','572412','571612','574112','574202','584101','582712','584401','583712','580812','583312','580712','581001') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET CL_NAME = 'ปฏิบัติงาน - ชำนาญงาน' WHERE PL_CODE IN (
		'511211','510501','510502','531912','530601','532601','532011','562802','562312','561712','562212','562712','561902','561502',
		'575201','574412','574701','575602','575702','574912','573712','572112','572802','574601','580502','584301','584555','582001',
		'582612','581501') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " UPDATE PER_POSITION, PER_LINE SET PER_POSITION.CL_NAME = PER_LINE.CL_NAME 
							  WHERE PER_LINE.PL_CODE = PER_POSITION.PL_CODE AND A.PL_CODE LIKE '5%' ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_POSITION A SET A.CL_NAME = (SELECT B.CL_NAME FROM PER_LINE B WHERE A.PL_CODE = B.PL_CODE) WHERE A.PL_CODE LIKE '5%' ";
		elseif($DPISDB=="mysql")
			$cmd = " UPDATE PER_POSITION, PER_LINE SET PER_POSITION.CL_NAME = PER_LINE.CL_NAME 
							  WHERE PER_LINE.PL_CODE = PER_POSITION.PL_CODE AND A.PL_CODE LIKE '5%' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " DELETE FROM PER_CO_LEVEL WHERE CL_NAME = 'ประเภททั่วไป ระดับปฏิบัติงาน' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " DELETE FROM PER_CO_LEVEL WHERE CL_NAME = 'ประเภททั่วไป ระดับชำนาญงาน' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " DELETE FROM PER_CO_LEVEL WHERE CL_NAME = 'ประเภททั่วไป ระดับอาวุโส' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " DELETE FROM PER_CO_LEVEL WHERE CL_NAME = 'ประเภททั่วไป ระดับทักษะพิเศษ' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " DELETE FROM PER_CO_LEVEL WHERE CL_NAME = 'ประเภทวิชาการ ระดับปฏิบัติการ' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " DELETE FROM PER_CO_LEVEL WHERE CL_NAME = 'ประเภทวิชาการ ระดับชำนาญการ' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " DELETE FROM PER_CO_LEVEL WHERE CL_NAME = 'ประเภทวิชาการ ระดับชำนาญการพิเศษ' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " DELETE FROM PER_CO_LEVEL WHERE CL_NAME = 'ประเภทวิชาการ ระดับเชี่ยวชาญ' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " DELETE FROM PER_CO_LEVEL WHERE CL_NAME = 'ประเภทวิชาการ ระดับทรงคุณวุฒิ' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " DELETE FROM PER_CO_LEVEL WHERE CL_NAME = 'ประเภทอำนวยการ ระดับต้น' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " DELETE FROM PER_CO_LEVEL WHERE CL_NAME = 'ประเภทอำนวยการ ระดับสูง' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " DELETE FROM PER_CO_LEVEL WHERE CL_NAME = 'ประเภทบริหาร ระดับต้น' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " DELETE FROM PER_CO_LEVEL WHERE CL_NAME = 'ประเภทบริหาร ระดับสูง' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'นักปกครอง', PL_SHORTNAME = 'นักปกครอง' WHERE PL_CODE = '510307' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'นักตรวจสอบความปลอดภัยด้านการบิน', PL_SHORTNAME = 'นักตรวจสอบความปลอดภัยด้านการบิน' WHERE PL_CODE = '572503' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'เจ้าพนักงานเคหกิจการเกษตร', PL_SHORTNAME = 'เจ้าพนักงานเคหกิจการเกษตร' WHERE PL_CODE = '541412' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'เจ้าพนักงานที่ดิน', PL_SHORTNAME = 'เจ้าพนักงานที่ดิน' WHERE PL_CODE = '584101' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'เจ้าพนักงานที่ดิน' WHERE LG_CODE = '584101' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '550803' WHERE PL_CODE = '051203' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นักกีฏวิทยารังสี

		$cmd = " DELETE FROM PER_LINE WHERE PL_CODE = '551203' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นักกีฏวิทยารังสี

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '550803' WHERE PL_CODE = '051203' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นักกีฏวิทยารังสี

		$cmd = " DELETE FROM PER_LINE WHERE PL_CODE in ('550603', '551203') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นักผลิตไอโซโทป นักกีฏวิทยารังสี

		$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
						  VALUES ('0206', 'คส. 2.6', 'คำสั่งจัดคนลง', '02') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL1_DESC VARCHAR(255) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL1_DESC VARCHAR2(255) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL1_DESC VARCHAR(255) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL2_DESC VARCHAR(255) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL2_DESC VARCHAR2(255) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL2_DESC VARCHAR(255) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL3_DESC VARCHAR(255) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL3_DESC VARCHAR2(255) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL3_DESC VARCHAR(255) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL4_DESC VARCHAR(255) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL4_DESC VARCHAR2(255) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL4_DESC VARCHAR(255) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL5_DESC VARCHAR(255) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL5_DESC VARCHAR2(255) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL5_DESC VARCHAR(255) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LINE_GROUP (LG_CODE, LG_NAME, LG_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('510309', 'อำนวยการเฉพาะด้าน', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('551109', 'ผู้อำนวยการเฉพาะด้าน (กีฏวิทยา)', 'ผู้อำนวยการเฉพาะด้าน (กีฏวิทยา)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '551109' WHERE PL_CODE = '051103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('582309', 'ผู้อำนวยการเฉพาะด้าน (คีตศิลป์)', 'ผู้อำนวยการเฉพาะด้าน (คีตศิลป์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '582309' WHERE PL_CODE = '082301' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('581209', 'ผู้อำนวยการเฉพาะด้าน (จดหมายเหตุ)', 'ผู้อำนวยการเฉพาะด้าน (จดหมายเหตุ)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '581209' WHERE PL_CODE = '081203' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('575309', 'ผู้อำนวยการเฉพาะด้าน (จิตรกรรม)', 'ผู้อำนวยการเฉพาะด้าน (จิตรกรรม)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '575309' WHERE PL_CODE = '075303' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('582209', 'ผู้อำนวยการเฉพาะด้าน (ดุริยางคศิลป์)', 'ผู้อำนวยการเฉพาะด้าน (ดุริยางคศิลป์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '582209' WHERE PL_CODE = '082201' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('572709', 'ผู้อำนวยการเฉพาะด้าน (ตรวจเรือ)', 'ผู้อำนวยการเฉพาะด้าน (ตรวจเรือ)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '572709' WHERE PL_CODE = '072703' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('521709', 'ผู้อำนวยการเฉพาะด้าน (ตรวจสอบภาษี)', 'ผู้อำนวยการเฉพาะด้าน (ตรวจสอบภาษี)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '521709' WHERE PL_CODE = '021703' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('560209', 'ผู้อำนวยการเฉพาะด้าน (ทันตแพทย์)', 'ผู้อำนวยการเฉพาะด้าน (ทันตแพทย์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '560209' WHERE PL_CODE = '060204' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('582109', 'ผู้อำนวยการเฉพาะด้าน (นาฏศิลป์)', 'ผู้อำนวยการเฉพาะด้าน (นาฏศิลป์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '582109' WHERE PL_CODE = '082101' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('530309', 'ผู้อำนวยการเฉพาะด้าน (นำร่อง)', 'ผู้อำนวยการเฉพาะด้าน (นำร่อง)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '530309' WHERE PL_CODE = '030306' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('512409', 'ผู้อำนวยการเฉพาะด้าน (นิติการ)', 'ผู้อำนวยการเฉพาะด้าน (นิติการ)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '512409' WHERE PL_CODE = '012403' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('550229', 'ผู้อำนวยการเฉพาะด้าน (นิติวิทยาศาสตร์)', 'ผู้อำนวยการเฉพาะด้าน (นิติวิทยาศาสตร์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '550229' WHERE PL_CODE = '050223' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('581309', 'ผู้อำนวยการเฉพาะด้าน (บรรณารักษ์)', 'ผู้อำนวยการเฉพาะด้าน (บรรณารักษ์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '581309' WHERE PL_CODE = '081303' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('581619', 'ผู้อำนวยการเฉพาะด้าน (โบราณคดี)', 'ผู้อำนวยการเฉพาะด้าน (โบราณคดี)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '581619' WHERE PL_CODE = '081615' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('540319', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างชลประทาน)', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างชลประทาน)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '540319' WHERE PL_CODE = '040312' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('573519', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างเทคนิค)', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างเทคนิค)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '573519' WHERE PL_CODE in ('073512','073612') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('571419', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างโยธา)', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างโยธา)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '571419' WHERE PL_CODE = '071412' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('571519', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างรังวัด)', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างรังวัด)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '571519' WHERE PL_CODE = '071512' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('574519', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างศิลปกรรม)', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างศิลปกรรม)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '574519' WHERE PL_CODE = '074512' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('575409', 'ผู้อำนวยการเฉพาะด้าน (ประติมากรรม)', 'ผู้อำนวยการเฉพาะด้าน (ประติมากรรม)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '575409' WHERE PL_CODE = '075403' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('584409', 'ผู้อำนวยการเฉพาะด้าน (ประเมินราคาทรัพย์สิน)', 'ผู้อำนวยการเฉพาะด้าน (ประเมินราคาทรัพย์สิน)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '584409' WHERE PL_CODE = '084403' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('512629', 'ผู้อำนวยการเฉพาะด้าน (พนักงานสอบสวนคดีพิเศษ)', 'ผู้อำนวยการเฉพาะด้าน (พนักงานสอบสวนคดีพิเศษ)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '512629' WHERE PL_CODE = '012626' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('560109', 'ผู้อำนวยการเฉพาะด้าน (แพทย์)', 'ผู้อำนวยการเฉพาะด้าน (แพทย์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '560109' WHERE PL_CODE = '060104' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('550409', 'ผู้อำนวยการเฉพาะด้าน (ฟิสิกส์รังสี)', 'ผู้อำนวยการเฉพาะด้าน (ฟิสิกส์รังสี)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '550409' WHERE PL_CODE = '050403' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('581909', 'ผู้อำนวยการเฉพาะด้าน (ภัณฑารักษ์)', 'ผู้อำนวยการเฉพาะด้าน (ภัณฑารักษ์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '581909' WHERE PL_CODE = '081903' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('560609', 'ผู้อำนวยการเฉพาะด้าน (เภสัชกรรม)', 'ผู้อำนวยการเฉพาะด้าน (เภสัชกรรม)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '560609' WHERE PL_CODE = '060603' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('574009', 'ผู้อำนวยการเฉพาะด้าน (มัณฑนศิลป์)', 'ผู้อำนวยการเฉพาะด้าน (มัณฑนศิลป์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '574009' WHERE PL_CODE = '074003' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('571319', 'ผู้อำนวยการเฉพาะด้าน (วิชาการกษาปณ์)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการกษาปณ์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '571319' WHERE PL_CODE = '071316' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('584209', 'ผู้อำนวยการเฉพาะด้าน (วิชาการจัดหาที่ดิน)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการจัดหาที่ดิน)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '584209' WHERE PL_CODE = '084203' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('522729', 'ผู้อำนวยการเฉพาะด้าน (วิชาการชั่งตวงวัด)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการชั่งตวงวัด)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '522729' WHERE PL_CODE = '022723' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('574429', 'ผู้อำนวยการเฉพาะด้าน (วิชาการช่างศิลป์)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการช่างศิลป์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '574429' WHERE PL_CODE = '074423' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('520609', 'ผู้อำนวยการเฉพาะด้าน (วิชาการตรวจสอบภายใน)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการตรวจสอบภายใน)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '520609' WHERE PL_CODE = '020603' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('511029', 'ผู้อำนวยการเฉพาะด้าน (วิชาการเทคโนโลยีสารสนเทศ)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการเทคโนโลยีสารสนเทศ)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('551309', 'ผู้อำนวยการเฉพาะด้าน (ธรณีวิทยา)', 'ผู้อำนวยการเฉพาะด้าน (ธรณีวิทยา)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '551309' WHERE PL_CODE = '051303' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('520309', 'ผู้อำนวยการเฉพาะด้าน (วิชาการบัญชี)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการบัญชี)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '520309' WHERE PL_CODE = '020303' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('523509', 'ผู้อำนวยการเฉพาะด้าน (วิชาการผลิตภัณฑ์อาหาร)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการผลิตภัณฑ์อาหาร)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '523509' WHERE PL_CODE = '023503' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('575109', 'ผู้อำนวยการเฉพาะด้าน (วิชาการแผนที่ภาพถ่าย)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการแผนที่ภาพถ่าย)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '575109' WHERE PL_CODE = '075103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('561519', 'ผู้อำนวยการเฉพาะด้าน (วิชาการพยาบาล)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการพยาบาล)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '561519' WHERE PL_CODE = '061514' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('521009', 'ผู้อำนวยการเฉพาะด้าน (วิชาการภาษี)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการภาษี)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '521009' WHERE PL_CODE = '021003' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('523909', 'ผู้อำนวยการเฉพาะด้าน (วิชาการมาตรฐาน)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการมาตรฐาน)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '523909' WHERE PL_CODE = '023903' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('550909', 'ผู้อำนวยการเฉพาะด้าน (วิชาการโรคพืช)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการโรคพืช)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '550909' WHERE PL_CODE = '050903' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('582409', 'ผู้อำนวยการเฉพาะด้าน (วิชาการละครและดนตรี)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการละครและดนตรี)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '582409' WHERE PL_CODE = '082403' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('522009', 'ผู้อำนวยการเฉพาะด้าน (วิชาการเศรษฐกิจ)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการเศรษฐกิจ)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '522009' WHERE PL_CODE = '022003' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('541449', 'ผู้อำนวยการเฉพาะด้าน (วิชาการส่งเสริมการเกษตร)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการส่งเสริมการเกษตร)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '541449' WHERE PL_CODE = '041443' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('512009', 'ผู้อำนวยการเฉพาะด้าน (วิชาการสถิติ)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการสถิติ)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '512009' WHERE PL_CODE = '012003' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('560709', 'ผู้อำนวยการเฉพาะด้าน (วิชาการอาหารและยา)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการอาหารและยา)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '560709' WHERE PL_CODE = '060703' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('551409', 'ผู้อำนวยการเฉพาะด้าน (วิชาการอุทกวิทยา)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการอุทกวิทยา)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '551409' WHERE PL_CODE = '051403' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('511019', 'ผู้อำนวยการเฉพาะด้าน (วิชาการคอมพิวเตอร์)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการคอมพิวเตอร์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '511019' WHERE PL_CODE = '011013' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('550109', 'ผู้อำนวยการเฉพาะด้าน (วิทยาศาสตร์)', 'ผู้อำนวยการเฉพาะด้าน (วิทยาศาสตร์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '550109' WHERE PL_CODE = '050103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('560409', 'ผู้อำนวยการเฉพาะด้าน (วิทยาศาสตร์การแพทย์)', 'ผู้อำนวยการเฉพาะด้าน (วิทยาศาสตร์การแพทย์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '560409' WHERE PL_CODE = '060403' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('551809', 'ผู้อำนวยการเฉพาะด้าน (วิทยาศาสตร์นิวเคลียร์)', 'ผู้อำนวยการเฉพาะด้าน (วิทยาศาสตร์นิวเคลียร์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '551809' WHERE PL_CODE = '051803' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('570109', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรม)', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรม)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '570109' WHERE PL_CODE = '070103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('570909', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมการเกษตร)', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมการเกษตร)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '570909' WHERE PL_CODE = '070903' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('570409', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมเครื่องกล)', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมเครื่องกล)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '570409' WHERE PL_CODE = '070403' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('540329', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมชลประทาน)', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมชลประทาน)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '540329' WHERE PL_CODE = '040323' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('571009', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมนิวเคลียร์)', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมนิวเคลียร์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '571009' WHERE PL_CODE = '071003' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('570709', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมปิโตรเลียม)', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมปิโตรเลียม)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '570709' WHERE PL_CODE = '070703' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('570509', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมไฟฟ้า)', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมไฟฟ้า)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '570509' WHERE PL_CODE = '070503' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('570209', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมโยธา)', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมโยธา)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '570209' WHERE PL_CODE = '070203' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('571529', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมรังวัด)', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมรังวัด)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '571529' WHERE PL_CODE = '071523' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('571629', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมสำรวจ)', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมสำรวจ)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '571629' WHERE PL_CODE = '071623' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('570609', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมเหมืองแร่)', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมเหมืองแร่)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '570609' WHERE PL_CODE = '070603' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('573809', 'ผู้อำนวยการเฉพาะด้าน (สถาปัตยกรรม)', 'ผู้อำนวยการเฉพาะด้าน (สถาปัตยกรรม)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '573809' WHERE PL_CODE = '073803' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('551009', 'ผู้อำนวยการเฉพาะด้าน (สัตววิทยา)', 'ผู้อำนวยการเฉพาะด้าน (สัตววิทยา)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '551009' WHERE PL_CODE = '051003' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('540209', 'ผู้อำนวยการเฉพาะด้าน (สำรวจดิน)', 'ผู้อำนวยการเฉพาะด้าน (สำรวจดิน)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '540209' WHERE PL_CODE = '040203' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('581109', 'ผู้อำนวยการเฉพาะด้าน (อักษรศาสตร์)', 'ผู้อำนวยการเฉพาะด้าน (อักษรศาสตร์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '581109' WHERE PL_CODE = '081103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('551609', 'ผู้อำนวยการเฉพาะด้าน (อุตุนิยมวิทยา)', 'ผู้อำนวยการเฉพาะด้าน (อุตุนิยมวิทยา)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '551609' WHERE PL_CODE = '051603' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('520429', 'ผู้อำนวยการเฉพาะด้าน (วิชาการเงินและบัญชี)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการเงินและบัญชี)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '520429' WHERE PL_CODE = '020423' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('540109', 'ผู้อำนวยการเฉพาะด้าน (วิชาการเกษตร)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการเกษตร)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '540109' WHERE PL_CODE = '040103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('520109', 'ผู้อำนวยการเฉพาะด้าน (วิชาการคลัง)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการคลัง)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '520109' WHERE PL_CODE = '020103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('520829', 'ผู้อำนวยการเฉพาะด้าน (วิชาการตรวจสอบบัญชี)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการตรวจสอบบัญชี)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '520829' WHERE PL_CODE = '020823' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('560309', 'ผู้อำนวยการเฉพาะด้าน (นายสัตวแพทย์)', 'ผู้อำนวยการเฉพาะด้าน (นายสัตวแพทย์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '560309' WHERE PL_CODE = '060304' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('562109', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานสัตวแพทย์)', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานสัตวแพทย์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '562109' WHERE PL_CODE = '062102' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('540509', 'ผู้อำนวยการเฉพาะด้าน (วิชาการป่าไม้)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการป่าไม้)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '540509' WHERE PL_CODE = '040503' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('522209', 'ผู้อำนวยการเฉพาะด้าน (วิชาการสหกรณ์)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการสหกรณ์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '522209' WHERE PL_CODE = '022203' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('540709', 'ผู้อำนวยการเฉพาะด้าน (วิชาการประมง)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการประมง)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '540709' WHERE PL_CODE = '040703' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('540919', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานประมง)', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานประมง)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '540919' WHERE PL_CODE = '040912' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('541009', 'ผู้อำนวยการเฉพาะด้าน (วิชาการสัตวบาล)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการสัตวบาล)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '541009' WHERE PL_CODE = '041003' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('541119', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานสัตวบาล)', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานสัตวบาล)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '541119' WHERE PL_CODE = '041112' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_soc.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 8, 'P1108 ระบบเครื่องราชย์ของสำนักเลขาธิการคณะรัฐมนตรี', 'S', 'W', 'rpt_soc.html', 0, 35, 251, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 8, 'P1108 ระบบเครื่องราชย์ของสำนักเลขาธิการคณะรัฐมนตรี', 'S', 'W', 'rpt_soc.html', 0, 35, 251, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();
/*
		$cmd = " select POS_ID, LEVEL_NO from PER_PERSONAL where LEVEL_NO in ('O1','O2','O3','O4','K1','K2','K3','K4','K5','D1','D2','M1','M2') ";
		$db_dpis->send_cmd($cmd);
		while ( $data = $db_dpis->get_array() ) {
				$POS_ID = $data[POS_ID] + 0;
				$LEVEL_NO = trim($data[LEVEL_NO]);
				$cmd = " select a.PL_CODE, b.PL_CODE_NEW, b.PL_CODE_DIRECT from PER_POSITION a, PER_LINE b
								where POS_ID = $POS_ID and a.PL_CODE = b.PL_CODE ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$PL_CODE = trim($data1[PL_CODE]);
				$PL_CODE_NEW = trim($data1[PL_CODE_NEW]);
				$PL_CODE_DIRECT = trim($data1[PL_CODE_DIRECT]);

				$cmd = " select PL_CODE, NEW_LEVEL_NO from PER_MAP_POS where LEVEL_NO='$LEVELVL' and PL_TYPE=$PL_TYPE ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$NEW_LEVEL_NO = $data1[NEW_LEVEL_NO];
					
					if ($PL_TYPE==2 || $PL_TYPE==3 || $PL_TYPE==5) 
						if ($LEVEL_NO_MIN=="08" && $CMD_PT_CODE=="31") $NEW_LEVEL_NO = "D1";
						elseif ($LEVEL_NO_MIN=="09" && $CMD_PT_CODE=="32") $NEW_LEVEL_NO = "D2";
		
					$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO)='$NEW_LEVEL_NO' ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();
					$LEVEL_NAME = trim($data1[LEVEL_NAME]);
					$arr_temp = explode(" ", $LEVEL_NAME);
					$LEVEL_NAME = $arr_temp[1];
					if ($LEVEL_NAME != "ระดับต้น" && $LEVEL_NAME != "ระดับสูง") $LEVEL_NAME = str_replace("ระดับ", "", $LEVEL_NAME);

					if ($LEVEL_NAME != $OLD_LEVEL_NAME) {
						if ($LEVEL_NO_MIN==$LVL)
							$NEW_LEVEL_NAME = $LEVEL_NAME;
						else
							$NEW_LEVEL_NAME = (trim($LEVEL_NAME)?($NEW_LEVEL_NAME .' หรือ '. $LEVEL_NAME):$NEW_LEVEL_NAME);
						$OLD_LEVEL_NAME = $LEVEL_NAME;
					}
					$cmd = " UPDATE PER_CO_LEVEL SET CL_NAME_NEW = '$LEVEL_NAME' WHERE CL_NAME = '$CL_NAME' ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();

					$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('$LEVEL_NAME', '$LEVEL_NO_MIN', '$LEVEL_NO_MAX', 1, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();

				} // end for
		} // end while

 รอ ใหม่ สตค.
		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_CO_LEVEL ADD CL_NAME_NEW VARCHAR(50) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_CO_LEVEL ADD CL_NAME_NEW VARCHAR2(50) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_CO_LEVEL ADD CL_NAME_NEW VARCHAR(50) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " select CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX from PER_CO_LEVEL where CL_NAME < 'A' ";
		$db_dpis->send_cmd($cmd);
		while ( $data = $db_dpis->get_array() ) {
				$CL_NAME = trim($data[CL_NAME]);
				$LEVEL_NO_MIN = trim($data[LEVEL_NO_MIN]);
				$LEVEL_NO_MAX = trim($data[LEVEL_NO_MAX]);
				for ($LVL=$LEVEL_NO_MIN;$LVL<=$LEVEL_NO_MAX;$LVL++){
					$LVL = str_pad(trim($LVL), 2, "0", STR_PAD_LEFT);
					$cmd = " select NEW_LEVEL_NO from PER_MAP_POS where LEVEL_NO='$LVL' and PL_TYPE=$PL_TYPE ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();
					$NEW_LEVEL_NO = $data1[NEW_LEVEL_NO];
					if ($PL_TYPE==2 || $PL_TYPE==3 || $PL_TYPE==5) 
						if ($LEVEL_NO_MIN=="08" && $CMD_PT_CODE=="31") $NEW_LEVEL_NO = "D1";
						elseif ($LEVEL_NO_MIN=="09" && $CMD_PT_CODE=="32") $NEW_LEVEL_NO = "D2";
		
					$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO)='$NEW_LEVEL_NO' ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();
					$LEVEL_NAME = trim($data1[LEVEL_NAME]);
					$arr_temp = explode(" ", $LEVEL_NAME);
					$LEVEL_NAME = $arr_temp[1];
					if ($LEVEL_NAME != "ระดับต้น" && $LEVEL_NAME != "ระดับสูง") $LEVEL_NAME = str_replace("ระดับ", "", $LEVEL_NAME);

					if ($LEVEL_NAME != $OLD_LEVEL_NAME) {
						if ($LEVEL_NO_MIN==$LVL)
							$NEW_LEVEL_NAME = $LEVEL_NAME;
						else
							$NEW_LEVEL_NAME = (trim($LEVEL_NAME)?($NEW_LEVEL_NAME .' หรือ '. $LEVEL_NAME):$NEW_LEVEL_NAME);
						$OLD_LEVEL_NAME = $LEVEL_NAME;
					}
					$cmd = " UPDATE PER_CO_LEVEL SET CL_NAME_NEW = '$LEVEL_NAME' WHERE CL_NAME = '$CL_NAME' ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();

					$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('$LEVEL_NAME', '$LEVEL_NO_MIN', '$LEVEL_NO_MAX', 1, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();

				} // end for
		} // end while
*/
		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '01', 15.5, 8350, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '01', 16, 8510, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '01', 16.5, 8680, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '01', 17, 8840, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '01', 17.5, 9000, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '01', 18, 9170, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '02', 14.5, 9990, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '02', 15, 10190, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '02', 15.5, 10380, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '02', 16, 10580, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '02', 16.5, 10770, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '02', 17, 10970, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '03', 20.5, 15100, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '03', 21, 15400, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '03', 21.5, 15690, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '03', 22, 15990, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '03', 22.5, 16280, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '03', 23, 16580, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '04', 20.5, 18560, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '04', 21, 18920, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '04', 21.5, 19290, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '04', 22, 19650, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '04', 22.5, 20010, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '04', 23, 20380, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '05', 20.5, 22670, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '05', 21, 23110, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '05', 21.5, 23560, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '05', 22, 24000, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '05', 22.5, 24450, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '05', 23, 24890, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '06', 20.5, 28050, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '06', 21, 28600, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '06', 21.5, 29150, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '06', 22, 29700, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '06', 22.5, 30250, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '06', 23, 30800, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '07', 20.5, 34220, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '07', 21, 34890, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '07', 21.5, 35560, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '07', 22, 36230, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '07', 22.5, 36900, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '07', 23, 37570, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '08', 24.5, 48400, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '08', 25, 49350, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '08', 25.5, 50300, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '08', 26, 51250, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '08', 26.5, 52200, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '08', 27, 53150, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '09', 20.5, 51570, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '09', 21, 52580, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '09', 21.5, 53590, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '09', 22, 54600, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '09', 22.5, 55610, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '09', 23, 56620, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '10', 19.5, 60970, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '10', 20, 62170, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '10', 20.5, 63360, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '10', 21, 64560, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '10', 21.5, 65750, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '10', 22, 66950, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '11', 16.5, 65410, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '11', 17, 66480, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '11', 17.5, 67810, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '11', 18, 69140, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '11', 18.5, 70470, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, '11', 19, 71800, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (2, '11', 10.5, 67810, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (2, '11', 11, 69140, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (2, '11', 11.5, 70470, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (2, '11', 12, 71800, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (2, '11', 12.5, 73130, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES (2, '11', 13, 74460, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_DECORATION ADD DC_ENG_NAME VARCHAR(100) Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_DECORATION ADD DC_ENG_NAME VARCHAR2(100) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_DECORATION ADD DC_ENG_NAME VARCHAR(100) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ORG ADD DEPARTMENT_ID INTEGER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ORG ADD DEPARTMENT_ID NUMBER(10) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_ORG ADD DEPARTMENT_ID INTEGER(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT ORG_ID, ORG_ID_REF, OL_CODE FROM PER_ORG WHERE OL_CODE > '02' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$ORG_ID = $data[ORG_ID];
			$ORG_ID_REF = $data[ORG_ID_REF];
			$OL_CODE = trim($data[OL_CODE]);
			if ($OL_CODE=='03') $DEPT_ID = $ORG_ID_REF;
			else if ($OL_CODE=='04') {
				$cmd = " SELECT ORG_ID_REF, OL_CODE FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				$data1 = $db_dpis1->get_array();
				$ORG_ID_REF = $data1[ORG_ID_REF];
				$OL_CODE = trim($data1[OL_CODE]);
				if ($OL_CODE=='03') $DEPT_ID = $ORG_ID_REF;
			} else if ($OL_CODE=='05') {
				$cmd = " SELECT ORG_ID_REF, OL_CODE FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				$data1 = $db_dpis1->get_array();
				$ORG_ID_REF = $data1[ORG_ID_REF];
				$OL_CODE = trim($data1[OL_CODE]);
				// ($OL_CODE=='04') 
				$cmd = " SELECT ORG_ID_REF, OL_CODE FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				$data1 = $db_dpis1->get_array();
				$ORG_ID_REF = $data1[ORG_ID_REF];
				$OL_CODE = trim($data1[OL_CODE]);
				if ($OL_CODE=='03') $DEPT_ID = $ORG_ID_REF;
			}
			$cmd = " UPDATE PER_ORG SET DEPARTMENT_ID = $DEPT_ID WHERE ORG_ID = $ORG_ID ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
		} // end while						

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ORG_ASS ADD DEPARTMENT_ID INTEGER Null ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ORG_ASS ADD DEPARTMENT_ID NUMBER(10) Null ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_ORG_ASS ADD DEPARTMENT_ID INTEGER(10) Null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT ORG_ID, ORG_ID_REF, OL_CODE FROM PER_ORG_ASS WHERE OL_CODE > '02' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$ORG_ID = $data[ORG_ID];
			$ORG_ID_REF = $data[ORG_ID_REF];
			$OL_CODE = trim($data[OL_CODE]);
			if ($OL_CODE=='03') $DEPT_ID = $ORG_ID_REF;
			else if ($OL_CODE=='04') {
				$cmd = " SELECT ORG_ID_REF, OL_CODE FROM PER_ORG_ASS WHERE ORG_ID = $ORG_ID_REF ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				$data1 = $db_dpis1->get_array();
				$ORG_ID_REF = $data1[ORG_ID_REF];
				$OL_CODE = trim($data1[OL_CODE]);
				if ($OL_CODE=='03') $DEPT_ID = $ORG_ID_REF;
			} else if ($OL_CODE=='05') {
				$cmd = " SELECT ORG_ID_REF, OL_CODE FROM PER_ORG_ASS WHERE ORG_ID = $ORG_ID_REF ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				$data1 = $db_dpis1->get_array();
				$ORG_ID_REF = $data1[ORG_ID_REF];
				$OL_CODE = trim($data1[OL_CODE]);
				// ($OL_CODE=='04') 
				$cmd = " SELECT ORG_ID_REF, OL_CODE FROM PER_ORG_ASS WHERE ORG_ID = $ORG_ID_REF ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				$data1 = $db_dpis1->get_array();
				$ORG_ID_REF = $data1[ORG_ID_REF];
				$OL_CODE = trim($data1[OL_CODE]);
				if ($OL_CODE=='03') $DEPT_ID = $ORG_ID_REF;
			}
			$cmd = " UPDATE PER_ORG_ASS SET DEPARTMENT_ID = $DEPT_ID WHERE ORG_ID = $ORG_ID ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
		} // end while						

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_MAP_CODE(
			MAP_CODE VARCHAR(100) NOT NULL,	
			OLD_CODE VARCHAR(100) NULL,
			NEW_CODE VARCHAR(100) NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PER_MAP_CODE PRIMARY KEY (map_code, old_code)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_MAP_CODE(
			MAP_CODE VARCHAR2(100) NOT NULL,	
			OLD_CODE VARCHAR2(100) NOT NULL,
			NEW_CODE VARCHAR2(100) NOT NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_MAP_CODE PRIMARY KEY (map_code, old_code)) ";
		elseif($DPISDB=="mysql") 
			$cmd = " CREATE TABLE PER_MAP_CODE(
			MAP_CODE VARCHAR(100) NOT NULL,	
			OLD_CODE VARCHAR(100) NOT NULL,
			NEW_CODE VARCHAR(100) NOT NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_MAP_CODE PRIMARY KEY (map_code, old_code)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE  PER_EDUCNAME DROP CONSTRAINT INXU1_PER_EDUCNAME ";
		elseif($DPISDB=="oci8") 
			$cmd = " DROP INDEX INXU1_PER_EDUCNAME ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE  PER_EDUCNAME DROP CONSTRAINT INXU1_PER_EDUCNAME ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE  PER_ORG DROP CONSTRAINT INXU1_PER_ORG ";
		elseif($DPISDB=="oci8") 
			$cmd = " DROP INDEX INXU1_PER_ORG ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE  PER_ORG DROP CONSTRAINT INXU1_PER_ORG ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE  PER_ORG DROP CONSTRAINT INXU2_PER_ORG ";
		elseif($DPISDB=="oci8") 
			$cmd = " DROP INDEX INXU2_PER_ORG ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE  PER_ORG DROP CONSTRAINT INXU2_PER_ORG ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = 'M0302 ชื่อตำแหน่งในการบริหารงาน' 
						  WHERE LINKTO_WEB = 'master_table_mgt.html?table=PER_MGT' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LINE ALTER PL_NAME VARCHAR(100) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LINE MODIFY PL_NAME VARCHAR2(100) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LINE MODIFY PL_NAME VARCHAR(100) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

/*
		$cmd = " SELECT INS_CODE, INS_NAME FROM PER_INSTITUTE WHERE INS_NAME LIKE '%&rsquo;%' ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error() ;
		echo "$cmd<br>";
		while ( $data_dpis = $db_dpis->get_array() ) {
			$INS_CODE = trim($data_dpis[INS_CODE]);
			$INS_NAME = trim($data_dpis[INS_NAME]);
			$INS_NAME = str_replace("&rsquo;", "&quot;", $INS_NAME);
			$cmd = " UPDATE PER_INSTITUTE SET INS_NAME = '$INS_NAME' WHERE INS_CODE = '$INS_CODE' ";
			$db_dpis1->send_cmd($cmd);
			$db_dpis1->show_error();         
		echo "$cmd<br>";
		} // end while
*/
//		$cmd = " SELECT TR_CODE, TR_NAME FROM PER_TRAIN WHERE TR_NAME LIKE '%''%' ";
	} // end if

	if( $command=='MENU' ) {
		$cmd = " UPDATE PER_TYPE_N SET PT_CODE_N = 'O1' WHERE PT_CODE_N = '11' OR PT_CODE_N = 'ปง.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_TYPE_N SET PT_CODE_N = 'O2' WHERE PT_CODE_N = '12' OR PT_CODE_N = 'ชง.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_TYPE_N SET PT_CODE_N = 'O3' WHERE PT_CODE_N = '13' OR PT_CODE_N = 'อว.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_TYPE_N SET PT_CODE_N = 'O4' WHERE PT_CODE_N = '14' OR PT_CODE_N = 'ทพ.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_TYPE_N SET PT_CODE_N = 'K1' WHERE PT_CODE_N = '21' OR PT_CODE_N = 'ปก.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_TYPE_N SET PT_CODE_N = 'K2' WHERE PT_CODE_N = '22' OR PT_CODE_N = 'ชก.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_TYPE_N SET PT_CODE_N = 'K3' WHERE PT_CODE_N = '23' OR PT_CODE_N = 'ชพ.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_TYPE_N SET PT_CODE_N = 'K4' WHERE PT_CODE_N = '24' OR PT_CODE_N = 'ชช.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_TYPE_N SET PT_CODE_N = 'K5' WHERE PT_CODE_N = '25' OR PT_CODE_N = 'ทว.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_TYPE_N SET PT_CODE_N = 'D1' WHERE PT_CODE_N = '31' OR PT_CODE_N = 'อต.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_TYPE_N SET PT_CODE_N = 'D2' WHERE PT_CODE_N = '32' OR PT_CODE_N = 'อส.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_TYPE_N SET PT_CODE_N = 'M1' WHERE PT_CODE_N = '41' OR PT_CODE_N = 'บต.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_TYPE_N SET PT_CODE_N = 'M2' WHERE PT_CODE_N = '42' OR PT_CODE_N = 'บส.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_MAP_TYPE SET PT_CODE_N = 'O1' WHERE PT_CODE_N = '11' OR PT_CODE_N = 'ปง.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_MAP_TYPE SET PT_CODE_N = 'O2' WHERE PT_CODE_N = '12' OR PT_CODE_N = 'ชง.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_MAP_TYPE SET PT_CODE_N = 'O3' WHERE PT_CODE_N = '13' OR PT_CODE_N = 'อว.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_MAP_TYPE SET PT_CODE_N = 'O4' WHERE PT_CODE_N = '14' OR PT_CODE_N = 'ทพ.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_MAP_TYPE SET PT_CODE_N = 'K1' WHERE PT_CODE_N = '21' OR PT_CODE_N = 'ปก.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_MAP_TYPE SET PT_CODE_N = 'K2' WHERE PT_CODE_N = '22' OR PT_CODE_N = 'ชก.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_MAP_TYPE SET PT_CODE_N = 'K3' WHERE PT_CODE_N = '23' OR PT_CODE_N = 'ชพ.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_MAP_TYPE SET PT_CODE_N = 'K4' WHERE PT_CODE_N = '24' OR PT_CODE_N = 'ชช.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_MAP_TYPE SET PT_CODE_N = 'K5' WHERE PT_CODE_N = '25' OR PT_CODE_N = 'ทว.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_MAP_TYPE SET PT_CODE_N = 'D1' WHERE PT_CODE_N = '31' OR PT_CODE_N = 'อต.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_MAP_TYPE SET PT_CODE_N = 'D2' WHERE PT_CODE_N = '32' OR PT_CODE_N = 'อส.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_MAP_TYPE SET PT_CODE_N = 'M1' WHERE PT_CODE_N = '41' OR PT_CODE_N = 'บต.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " UPDATE PER_MAP_TYPE SET PT_CODE_N = 'M2' WHERE PT_CODE_N = '42' OR PT_CODE_N = 'บส.' ";
		$db->send_cmd($cmd);
		//$db->show_error() ;
/*
		$cmd = " ALTER TABLE PER_LAYER DROP CONSTRAINT FK1_PER_LAYER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MGTSALARY DROP CONSTRAINT FK2_PER_MGTSALARY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_CO_LEVEL DROP CONSTRAINT FK1_PER_CO_LEVEL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_CO_LEVEL DROP CONSTRAINT FK2_PER_CO_LEVEL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LAYER_NEW DROP CONSTRAINT FK1_PER_LAYER_NEW ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EMPSER_POS_NAME DROP CONSTRAINT FK1_PER_EMPSER_POS_NAME ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ASSIGN DROP CONSTRAINT FK1_PER_ASSIGN ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ASSIGN DROP CONSTRAINT FK2_PER_ASSIGN ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ASSIGN_YEAR DROP CONSTRAINT FK2_PER_ASSIGN_YEAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ASSIGN_S DROP CONSTRAINT FK1_PER_ASSIGN_S ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PERSONAL DROP CONSTRAINT FK6_PER_PERSONAL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_POSITIONHIS DROP CONSTRAINT FK4_PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TRANSFER_REQ DROP CONSTRAINT FK8_PER_TRANSFER_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TRANSFER_REQ DROP CONSTRAINT FK12_PER_TRANSFER_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TRANSFER_REQ DROP CONSTRAINT FK16_PER_TRANSFER_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COMDTL DROP CONSTRAINT FK8_PER_COMDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORCOND DROP CONSTRAINT FK1_PER_DECORCOND ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SUM_DTL4 DROP CONSTRAINT FK4_PER_SUM_DTL4 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SUM_DTL5 DROP CONSTRAINT FK4_PER_SUM_DTL5 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SUM_DTL7 DROP CONSTRAINT FK2_PER_SUM_DTL7 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SUM_DTL8 DROP CONSTRAINT FK3_PER_SUM_DTL8 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE POS_SPEC_DTL DROP CONSTRAINT FK1_POS_SPEC_DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP INDEX INXU1_POS_SPEC_DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE SAL_SL_REGISTER DROP CONSTRAINT FK3_SAL_SL_REGISTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DROP INDEX INXU3_SAL_SL_REGISTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;
*/
/*
		$cmd = " ALTER TABLE PER_ASSIGN_S DROP CONSTRAINT PK_PER_ASSIGN_S ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ASSIGN_YEAR DROP CONSTRAINT PK_PER_ASSIGN_YEAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORCOND DROP CONSTRAINT PK_PER_DECORCOND ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LAYER DROP CONSTRAINT PK_PER_LAYER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LAYER_NEW DROP CONSTRAINT PK_PER_LAYER_NEW ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MGTSALARY DROP CONSTRAINT PK_PER_MGTSALARY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SUM_DTL4 DROP CONSTRAINT PK_PER_SUM_DTL4 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SUM_DTL5 DROP CONSTRAINT PK_PER_SUM_DTL5 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SUM_DTL7 DROP CONSTRAINT PK_PER_SUM_DTL7 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SUM_DTL8 DROP CONSTRAINT PK_PER_SUM_DTL8 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LEVEL DROP CONSTRAINT PK_PER_LEVEL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE POS_ASSIGN_S DROP CONSTRAINT PK_POS_ASSIGN_S ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE POS_ASSIGN_YEAR DROP CONSTRAINT PK_POS_ASSIGN_YEAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE POS_LAYER DROP CONSTRAINT PK_POS_LAYER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE POS_LEVEL DROP CONSTRAINT PK_POS_LEVEL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE POS_MGTSALARY DROP CONSTRAINT PK_POS_MGTSALARY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE POS_SUM_DTL4 DROP CONSTRAINT PK_POS_SUM_DTL4 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE POS_SUM_DTL5 DROP CONSTRAINT PK_POS_SUM_DTL5 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE POS_SUM_DTL7 DROP CONSTRAINT PK_POS_SUM_DTL7 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE POS_SUM_DTL8 DROP CONSTRAINT PK_POS_SUM_DTL8 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LAYER_NEW DROP CONSTRAINT PK_LAYER_NEW ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;
*/

		$cmd = " DROP INDEX UPDATE_TABLE_SAL_U1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		if($DPISDB=="oci8") {
			$cmd = " SELECT TNAME FROM TAB WHERE TABTYPE = 'VIEW' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error() ;
			while ( $data_dpis = $db_dpis->get_array() ) {
				$TNAME = trim($data_dpis[TNAME]);
				$cmd = " DROP VIEW $TNAME ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			} // end while

			$cmd = " SELECT CONSTRAINT_NAME FROM DBA_CONSTRAINTS WHERE OWNER = 'OCSC' AND TABLE_NAME = 'PER_LEVEL' AND CONSTRAINT_TYPE = 'C' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error() ;
			while ( $data_dpis = $db_dpis->get_array() ) {
				$CONSTRAINT_NAME = trim($data_dpis[CONSTRAINT_NAME]);
				$cmd = " ALTER TABLE PER_LEVEL DROP CONSTRAINT $CONSTRAINT_NAME ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			} // end while

			for ($no=1;$no<=10;$no++) {
				$cmd = " SELECT TNAME FROM TAB WHERE TNAME LIKE 'OF_%' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error() ;
				while ( $data_dpis = $db_dpis->get_array() ) {
					$TNAME = trim($data_dpis[TNAME]);
					$cmd = " DROP TABLE $TNAME ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while

				$cmd = " SELECT TNAME FROM TAB WHERE TNAME LIKE 'SAL_%' ";
				$db_dpis->send_cmd($cmd);
				$db_dpis->show_error() ;
				while ( $data_dpis = $db_dpis->get_array() ) {
					$TNAME = trim($data_dpis[TNAME]);
					$cmd = " DROP TABLE $TNAME ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while

				$cmd = " SELECT TNAME FROM TAB WHERE TNAME LIKE 'POS_%' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error() ;
				while ( $data_dpis = $db_dpis->get_array() ) {
					$TNAME = trim($data_dpis[TNAME]);
					$cmd = " DROP TABLE $TNAME ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while

				$cmd = " SELECT TNAME FROM TAB WHERE TNAME LIKE 'PS_%' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error() ;
				while ( $data_dpis = $db_dpis->get_array() ) {
					$TNAME = trim($data_dpis[TNAME]);
					$cmd = " DROP TABLE $TNAME ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while
			} // end for
		} // end if

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LAYER ALTER LEVEL_NO VARCHAR(10) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LAYER MODIFY LEVEL_NO VARCHAR2(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LAYER MODIFY LEVEL_NO VARCHAR(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_MGTSALARY ALTER LEVEL_NO VARCHAR(10) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_MGTSALARY MODIFY LEVEL_NO VARCHAR2(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_MGTSALARY MODIFY LEVEL_NO VARCHAR(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_CO_LEVEL ALTER LEVEL_NO_MIN VARCHAR(10) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_CO_LEVEL MODIFY LEVEL_NO_MIN VARCHAR2(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_CO_LEVEL MODIFY LEVEL_NO_MIN VARCHAR(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_CO_LEVEL ALTER LEVEL_NO_MAX VARCHAR(10) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_CO_LEVEL MODIFY LEVEL_NO_MAX VARCHAR2(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_CO_LEVEL MODIFY LEVEL_NO_MAX VARCHAR(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LAYER_NEW ALTER LEVEL_NO VARCHAR(10) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LAYER_NEW MODIFY LEVEL_NO VARCHAR2(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LAYER_NEW MODIFY LEVEL_NO VARCHAR(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_EMPSER_POS_NAME ALTER LEVEL_NO VARCHAR(10) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_EMPSER_POS_NAME MODIFY LEVEL_NO VARCHAR2(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_EMPSER_POS_NAME MODIFY LEVEL_NO VARCHAR(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ASSIGN ALTER LEVEL_NO_MIN VARCHAR(10) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ASSIGN MODIFY LEVEL_NO_MIN VARCHAR2(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_ASSIGN MODIFY LEVEL_NO_MIN VARCHAR(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ASSIGN ALTER LEVEL_NO_MAX VARCHAR(10) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ASSIGN MODIFY LEVEL_NO_MAX VARCHAR2(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_ASSIGN MODIFY LEVEL_NO_MAX VARCHAR(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ASSIGN_YEAR ALTER LEVEL_NO VARCHAR(10) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ASSIGN_YEAR MODIFY LEVEL_NO VARCHAR2(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_ASSIGN_YEAR MODIFY LEVEL_NO VARCHAR(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ASSIGN_S ALTER LEVEL_NO VARCHAR(10) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ASSIGN_S MODIFY LEVEL_NO VARCHAR2(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_ASSIGN_S MODIFY LEVEL_NO VARCHAR(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_PERSONAL ALTER LEVEL_NO VARCHAR(10) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_PERSONAL MODIFY LEVEL_NO VARCHAR2(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_PERSONAL MODIFY LEVEL_NO VARCHAR(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITIONHIS ALTER LEVEL_NO VARCHAR(10) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY LEVEL_NO VARCHAR2(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY LEVEL_NO VARCHAR(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_TRANSFER_REQ ALTER LEVEL_NO_1 VARCHAR(10) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_TRANSFER_REQ MODIFY LEVEL_NO_1 VARCHAR2(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_TRANSFER_REQ MODIFY LEVEL_NO_1 VARCHAR(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_TRANSFER_REQ ALTER LEVEL_NO_2 VARCHAR(10) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_TRANSFER_REQ MODIFY LEVEL_NO_2 VARCHAR2(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_TRANSFER_REQ MODIFY LEVEL_NO_2 VARCHAR(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_TRANSFER_REQ ALTER LEVEL_NO_3 VARCHAR(10) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_TRANSFER_REQ MODIFY LEVEL_NO_3 VARCHAR2(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_TRANSFER_REQ MODIFY LEVEL_NO_3 VARCHAR(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_COMDTL ALTER LEVEL_NO VARCHAR(10) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_COMDTL MODIFY LEVEL_NO VARCHAR2(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_COMDTL MODIFY LEVEL_NO VARCHAR(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_DECORCOND ALTER LEVEL_NO VARCHAR(10) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_DECORCOND MODIFY LEVEL_NO VARCHAR2(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_DECORCOND MODIFY LEVEL_NO VARCHAR(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SUM_DTL4 ALTER LEVEL_NO VARCHAR(10) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SUM_DTL4 MODIFY LEVEL_NO VARCHAR2(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_SUM_DTL4 MODIFY LEVEL_NO VARCHAR(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SUM_DTL5 ALTER LEVEL_NO VARCHAR(10) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SUM_DTL5 MODIFY LEVEL_NO VARCHAR2(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_SUM_DTL5 MODIFY LEVEL_NO VARCHAR(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SUM_DTL7 ALTER LEVEL_NO VARCHAR(10) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SUM_DTL7 MODIFY LEVEL_NO VARCHAR2(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_SUM_DTL7 MODIFY LEVEL_NO VARCHAR(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SUM_DTL8 ALTER LEVEL_NO VARCHAR(10) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SUM_DTL8 MODIFY LEVEL_NO VARCHAR2(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_SUM_DTL8 MODIFY LEVEL_NO VARCHAR(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_LEVEL ALTER LEVEL_NO VARCHAR(10) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_LEVEL MODIFY LEVEL_NO VARCHAR2(10) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LEVEL MODIFY LEVEL_NO VARCHAR(10) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$table = array("PER_CO_LEVEL", "PER_ASSIGN");
		for ( $i=0; $i<count($table); $i++ ) { 
			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MIN = 'O1' WHERE LEVEL_NO_MIN = 'ปง.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
	
			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MAX = 'O1' WHERE LEVEL_NO_MAX = 'ปง.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
	
			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MIN = 'O2' WHERE LEVEL_NO_MIN = 'ชง.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MAX = 'O2' WHERE LEVEL_NO_MAX = 'ชง.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MIN = 'O3' WHERE LEVEL_NO_MIN = 'อว.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MAX = 'O3' WHERE LEVEL_NO_MAX = 'อว.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MIN = 'O4' WHERE LEVEL_NO_MIN = 'ทพ.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MAX = 'O4' WHERE LEVEL_NO_MAX = 'ทพ.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MIN = 'K1' WHERE LEVEL_NO_MIN = 'ปก.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MAX = 'K1' WHERE LEVEL_NO_MAX = 'ปก.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MIN = 'K2' WHERE LEVEL_NO_MIN = 'ชก.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MAX = 'K2' WHERE LEVEL_NO_MAX = 'ชก.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MIN = 'K3' WHERE LEVEL_NO_MIN = 'ชพ.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MAX = 'K3' WHERE LEVEL_NO_MAX = 'ชพ.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MIN = 'K4' WHERE LEVEL_NO_MIN = 'ชช.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MAX = 'K4' WHERE LEVEL_NO_MAX = 'ชช.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MIN = 'K5' WHERE LEVEL_NO_MIN = 'ทว.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MAX = 'K5' WHERE LEVEL_NO_MAX = 'ทว.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MIN = 'D1' WHERE LEVEL_NO_MIN = 'อต.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MAX = 'D1' WHERE LEVEL_NO_MAX = 'อต.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MIN = 'D2' WHERE LEVEL_NO_MIN = 'อส.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MAX = 'D2' WHERE LEVEL_NO_MAX = 'อส.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MIN = 'M1' WHERE LEVEL_NO_MIN = 'บต.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MAX = 'M1' WHERE LEVEL_NO_MAX = 'บต.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MIN = 'M2' WHERE LEVEL_NO_MIN = 'บส.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_MAX = 'M2' WHERE LEVEL_NO_MAX = 'บส.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

		} 	// end for

		$table = array("PER_TRANSFER_REQ");
		for ( $i=0; $i<count($table); $i++ ) { 
			$cmd = " UPDATE $table[$i] SET LEVEL_NO_1 = 'O1' WHERE LEVEL_NO_1 = 'ปง.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
	
			$cmd = " UPDATE $table[$i] SET LEVEL_NO_2 = 'O1' WHERE LEVEL_NO_2 = 'ปง.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
	
			$cmd = " UPDATE $table[$i] SET LEVEL_NO_3 = 'O1' WHERE LEVEL_NO_3 = 'ปง.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
	
			$cmd = " UPDATE $table[$i] SET LEVEL_NO_1 = 'O2' WHERE LEVEL_NO_1 = 'ชง.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_2 = 'O2' WHERE LEVEL_NO_2 = 'ชง.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_3 = 'O2' WHERE LEVEL_NO_3 = 'ชง.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_1 = 'O3' WHERE LEVEL_NO_1 = 'อว.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_2 = 'O3' WHERE LEVEL_NO_2 = 'อว.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_3 = 'O3' WHERE LEVEL_NO_3 = 'อว.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_1 = 'O4' WHERE LEVEL_NO_1 = 'ทพ.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_2 = 'O4' WHERE LEVEL_NO_2 = 'ทพ.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_3 = 'O4' WHERE LEVEL_NO_3 = 'ทพ.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_1 = 'K1' WHERE LEVEL_NO_1 = 'ปก.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_2 = 'K1' WHERE LEVEL_NO_2 = 'ปก.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_3 = 'K1' WHERE LEVEL_NO_3 = 'ปก.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_1 = 'K2' WHERE LEVEL_NO_1 = 'ชก.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_2 = 'K2' WHERE LEVEL_NO_2 = 'ชก.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_3 = 'K2' WHERE LEVEL_NO_3 = 'ชก.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_1 = 'K3' WHERE LEVEL_NO_1 = 'ชพ.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_2 = 'K3' WHERE LEVEL_NO_2 = 'ชพ.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_3 = 'K3' WHERE LEVEL_NO_3 = 'ชพ.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_1 = 'K4' WHERE LEVEL_NO_1 = 'ชช.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_2 = 'K4' WHERE LEVEL_NO_2 = 'ชช.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_3 = 'K4' WHERE LEVEL_NO_3 = 'ชช.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_1 = 'K5' WHERE LEVEL_NO_1 = 'ทว.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_2 = 'K5' WHERE LEVEL_NO_2 = 'ทว.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_3 = 'K5' WHERE LEVEL_NO_3 = 'ทว.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_1 = 'D1' WHERE LEVEL_NO_1 = 'อต.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_2 = 'D1' WHERE LEVEL_NO_2 = 'อต.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_3 = 'D1' WHERE LEVEL_NO_3 = 'อต.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_1 = 'D2' WHERE LEVEL_NO_1 = 'อส.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_2 = 'D2' WHERE LEVEL_NO_2 = 'อส.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_3 = 'D2' WHERE LEVEL_NO_3 = 'อส.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_1 = 'M1' WHERE LEVEL_NO_1 = 'บต.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_2 = 'M1' WHERE LEVEL_NO_2 = 'บต.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_3 = 'M1' WHERE LEVEL_NO_3 = 'บต.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_1 = 'M2' WHERE LEVEL_NO_1 = 'บส.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_2 = 'M2' WHERE LEVEL_NO_2 = 'บส.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO_3 = 'M2' WHERE LEVEL_NO_3 = 'บส.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

		} 	// end for

		$table = array("PER_LAYER", "PER_MGTSALARY", "PER_LAYER_NEW", "PER_EMPSER_POS_NAME", "PER_ASSIGN_YEAR", "PER_ASSIGN_S", 
								    "PER_PERSONAL", "PER_POSITIONHIS", "PER_COMDTL", "PER_DECORCOND", "PER_SUM_DTL4", "PER_SUM_DTL5", "PER_SUM_DTL7", 
									"PER_SUM_DTL8", "PER_LEVEL");

		for ( $i=0; $i<count($table); $i++ ) { 
			$cmd = " UPDATE $table[$i] SET LEVEL_NO = 'O1' WHERE LEVEL_NO = 'ปง.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
	
			$cmd = " UPDATE $table[$i] SET LEVEL_NO = 'O2' WHERE LEVEL_NO = 'ชง.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO = 'O3' WHERE LEVEL_NO = 'อว.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO = 'O4' WHERE LEVEL_NO = 'ทพ.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO = 'K1' WHERE LEVEL_NO = 'ปก.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO = 'K2' WHERE LEVEL_NO = 'ชก.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO = 'K3' WHERE LEVEL_NO = 'ชพ.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO = 'K4' WHERE LEVEL_NO = 'ชช.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO = 'K5' WHERE LEVEL_NO = 'ทว.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO = 'D1' WHERE LEVEL_NO = 'อต.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO = 'D2' WHERE LEVEL_NO = 'อส.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO = 'M1' WHERE LEVEL_NO = 'บต.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " UPDATE $table[$i] SET LEVEL_NO = 'M2' WHERE LEVEL_NO = 'บส.' ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

		} 	// end for

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
						  VALUES ('O1', 1, $SESS_USERID, '$UPDATE_DATE', 'ประเภททั่วไป ระดับปฏิบัติงาน',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
						  VALUES ('O2', 1, $SESS_USERID, '$UPDATE_DATE', 'ประเภททั่วไป ระดับชำนาญงาน',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
						  VALUES ('O3', 1, $SESS_USERID, '$UPDATE_DATE', 'ประเภททั่วไป ระดับอาวุโส',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
						  VALUES ('O4', 1, $SESS_USERID, '$UPDATE_DATE', 'ประเภททั่วไป ระดับทักษะพิเศษ',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
						  VALUES ('K1', 1, $SESS_USERID, '$UPDATE_DATE', 'ประเภทวิชาการ ระดับปฏิบัติการ',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
						  VALUES ('K2', 1, $SESS_USERID, '$UPDATE_DATE', 'ประเภทวิชาการ ระดับชำนาญการ',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
						  VALUES ('K3', 1, $SESS_USERID, '$UPDATE_DATE', 'ประเภทวิชาการ ระดับชำนาญการพิเศษ',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
						  VALUES ('K4', 1, $SESS_USERID, '$UPDATE_DATE', 'ประเภทวิชาการ ระดับเชี่ยวชาญ',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
						  VALUES ('K5', 1, $SESS_USERID, '$UPDATE_DATE', 'ประเภทวิชาการ ระดับทรงคุณวุฒิ',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
						  VALUES ('D1', 1, $SESS_USERID, '$UPDATE_DATE', 'ประเภทอำนวยการ ระดับต้น',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
						  VALUES ('D2', 1, $SESS_USERID, '$UPDATE_DATE', 'ประเภทอำนวยการ ระดับสูง',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
						  VALUES ('M1', 1, $SESS_USERID, '$UPDATE_DATE', 'ประเภทบริหาร ระดับต้น',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
						  VALUES ('M2', 1, $SESS_USERID, '$UPDATE_DATE', 'ประเภทบริหาร ระดับสูง',1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_POSITION DROP CONSTRAINT FK7_PER_POSITION ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " ALTER TABLE PER_POS_MOVE DROP CONSTRAINT FK8_PER_POS_MOVE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " ALTER TABLE PER_ORDER_DTL DROP CONSTRAINT FK9_PER_ORDER_DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " ALTER TABLE PER_REQ1_DTL1 DROP CONSTRAINT FK8_PER_REQ1_DTL1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " ALTER TABLE PER_REQ2_DTL DROP CONSTRAINT FK11_PER_REQ2_DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " ALTER TABLE PER_REQ3_DTL DROP CONSTRAINT FK9_PER_REQ3_DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " ALTER TABLE PER_SUM_DTL2 DROP CONSTRAINT FK3_PER_SUM_DTL2 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_CO_LEVEL ALTER CL_NAME VARCHAR(50) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_CO_LEVEL MODIFY CL_NAME VARCHAR2(50) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_CO_LEVEL ALTER CL_NAME VARCHAR(50) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET CL_NAME = TRIM(CL_NAME) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POSITION ALTER CL_NAME VARCHAR(50) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POSITION MODIFY CL_NAME VARCHAR2(50) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_POSITION ALTER CL_NAME VARCHAR(50) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_POSITION SET CL_NAME = TRIM(CL_NAME) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK7_PER_POSITION FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_POS_MOVE ALTER CL_NAME VARCHAR(50) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_POS_MOVE MODIFY CL_NAME VARCHAR2(50) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_POS_MOVE ALTER CL_NAME VARCHAR(50) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_POS_MOVE SET CL_NAME = TRIM(CL_NAME) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK8_PER_POS_MOVE FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_ORDER_DTL ALTER CL_NAME VARCHAR(50) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_ORDER_DTL MODIFY CL_NAME VARCHAR2(50) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_ORDER_DTL ALTER CL_NAME VARCHAR(50) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_ORDER_DTL SET CL_NAME = TRIM(CL_NAME) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK9_PER_ORDER_DTL FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_REQ1_DTL1 ALTER CL_NAME VARCHAR(50) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_REQ1_DTL1 MODIFY CL_NAME VARCHAR2(50) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_REQ1_DTL1 ALTER CL_NAME VARCHAR(50) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_REQ1_DTL1 SET CL_NAME = TRIM(CL_NAME) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK8_PER_REQ1_DTL1 FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_REQ2_DTL ALTER CL_NAME VARCHAR(50) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_REQ2_DTL MODIFY CL_NAME VARCHAR2(50) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_REQ2_DTL ALTER CL_NAME VARCHAR(50) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_REQ2_DTL SET CL_NAME = TRIM(CL_NAME) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK11_PER_REQ2_DTL FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_REQ3_DTL ALTER CL_NAME VARCHAR(50) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_REQ3_DTL MODIFY CL_NAME VARCHAR2(50) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_REQ3_DTL ALTER CL_NAME VARCHAR(50) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_REQ3_DTL SET CL_NAME = TRIM(CL_NAME) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK9_PER_REQ3_DTL FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_SUM_DTL2 ALTER CL_NAME VARCHAR(50) ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_SUM_DTL2 MODIFY CL_NAME VARCHAR2(50) ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_SUM_DTL2 ALTER CL_NAME VARCHAR(50) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_SUM_DTL2 SET CL_NAME = TRIM(CL_NAME) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_SUM_DTL2 ADD CONSTRAINT FK3_PER_SUM_DTL2 FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LEVEL SET LEVEL_NAME = 'ประเภททั่วไป ระดับปฏิบัติงาน' WHERE LEVEL_NO = 'O1' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_NAME = 'ประเภททั่วไป ระดับชำนาญงาน' WHERE LEVEL_NO = 'O2' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_NAME = 'ประเภททั่วไป ระดับอาวุโส' WHERE LEVEL_NO = 'O3' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_NAME = 'ประเภททั่วไป ระดับทักษะพิเศษ' WHERE LEVEL_NO = 'O4' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_NAME = 'ประเภทวิชาการ ระดับปฏิบัติการ' WHERE LEVEL_NO = 'K1' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_NAME = 'ประเภทวิชาการ ระดับชำนาญการ' WHERE LEVEL_NO = 'K2' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_NAME = 'ประเภทวิชาการ ระดับชำนาญการพิเศษ' WHERE LEVEL_NO = 'K3' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_NAME = 'ประเภทวิชาการ ระดับเชี่ยวชาญ' WHERE LEVEL_NO = 'K4' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_NAME = 'ประเภทวิชาการ ระดับทรงคุณวุฒิ' WHERE LEVEL_NO = 'K5' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_NAME = 'ประเภทอำนวยการ ระดับต้น' WHERE LEVEL_NO = 'D1' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_NAME = 'ประเภทอำนวยการ ระดับสูง' WHERE LEVEL_NO = 'D2' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_NAME = 'ประเภทบริหาร ระดับต้น' WHERE LEVEL_NO = 'M1' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LEVEL SET LEVEL_NAME = 'ประเภทบริหาร ระดับสูง' WHERE LEVEL_NO = 'M2' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_MAP_POS(
			LEVEL_NO VARCHAR(10) NOT NULL,	
			PL_TYPE SINGLE NOT NULL,
			NEW_LEVEL_NO VARCHAR(10) NOT NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_MAP_POS PRIMARY KEY (level_no, pl_type)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_MAP_POS(
			LEVEL_NO VARCHAR2(10) NOT NULL,	
			PL_TYPE NUMBER(1) NOT NULL,
			NEW_LEVEL_NO VARCHAR2(10) NOT NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_MAP_POS PRIMARY KEY (level_no, pl_type)) ";
		elseif($DPISDB=="mysql") 
			$cmd = " CREATE TABLE PER_MAP_POS(
			LEVEL_NO VARCHAR(10) NOT NULL,	
			PL_TYPE SMALLINT(1) NOT NULL,
			NEW_LEVEL_NO VARCHAR(10) NOT NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_MAP_POS PRIMARY KEY (level_no, pl_type)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
						  VALUES ('01', 1, 'O1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
						  VALUES ('02', 1, 'O1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
						  VALUES ('03', 1, 'O1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
						  VALUES ('04', 1, 'O1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
						  VALUES ('05', 1, 'O2', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
						  VALUES ('06', 1, 'O2', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
						  VALUES ('07', 1, 'O3', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
						  VALUES ('08', 1, 'O3', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
						  VALUES ('09', 1, 'O4', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
						  VALUES ('03', 2, 'K1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
						  VALUES ('04', 2, 'K1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
						  VALUES ('05', 2, 'K1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('06', 2, 'K2', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('07', 2, 'K2', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('08', 2, 'K3', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('09', 2, 'K4', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 2, 'K5', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('03', 3, 'K1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('04', 3, 'K1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('05', 3, 'K1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('06', 3, 'K2', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('07', 3, 'K2', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('08', 3, 'K3', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('09', 3, 'K4', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 3, 'K5', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('09', 4, 'M1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 4, 'M2', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('11', 4, 'M2', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('03', 5, 'K1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('04', 5, 'K1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('05', 5, 'K1', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('06', 5, 'K2', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('07', 5, 'K2', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('08', 5, 'K3', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('09', 5, 'K4', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MAP_POS (LEVEL_NO, PL_TYPE, NEW_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 5, 'K5', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_LINE_GROUP(
			LG_CODE VARCHAR(10) NOT NULL,	
			LG_NAME VARCHAR(100) NOT NULL,
			LG_ACTIVE SINGLE NOT NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_LINE_GROUP PRIMARY KEY (lg_code)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_LINE_GROUP(
			LG_CODE VARCHAR2(10) NOT NULL,	
			LG_NAME VARCHAR2(100) NOT NULL,
			LG_ACTIVE NUMBER(1) NOT NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_LINE_GROUP PRIMARY KEY (lg_code)) ";
		elseif($DPISDB=="mysql") 
			$cmd = " CREATE TABLE PER_LINE_GROUP(
			LG_CODE VARCHAR(10) NOT NULL,	
			LG_NAME VARCHAR(100) NOT NULL,
			LG_ACTIVE SMALLINT(1) NOT NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_LINE_GROUP PRIMARY KEY (lg_code)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE = trim(PL_CODE) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_TYPE = 0 WHERE PL_CODE IN ('000010','000020','000030','000040','000050','000060','000070',
		'000080','000090','000100','000110','000120','000130','000140','000150','000151','000160','000170','000180','000190',
		'000200','000210','000220','000230','000232','000240','000250','000280','000290','000300','000310','000320','000321',
		'000330','000340','000350','000360','000370','000380','000381','000382','000390','000400','000410','000430','000440',
		'000450','000460','000470','000480','000490','000500','000510','000520','000530','000531','000540','000550','000560',
		'000570','000580','000590','000600','000610','000611','000619','000620','000630','000640','000650','000803','000804',
		'000805','001010','001020','001030','001040','001050','001060','001070','001080','001090','001100','001110','001120',
		'001130','001131','001140','001150','001160','001170','001180','001190','001191','001200','001210','001220','001230',
		'001240','001241','001260','001270','001280','001290','001291','001300','001301','001302','001303','001310','001320',
		'001330','001340','001350','001351','001360','001370','001380','001390','001400','001401','001430','001440','001450',
		'001460','001470','001480','001490','001500','001510','001520','001530','001540','001550','001560','001570','001571',
		'001572','001580','001590','001600','001601','001602','001603','001604','001605','001606','001607','001608','001609',
		'001610','001611','001612','001613','001614','001615','001616','001700','001701','001702','001703','001704','001705',
		'001706','001707','001708','001709','001800','001801','001902','001903','001905','001906','001907','001908','001909',
		'002000','002010','002020','002021','002022','002023','009008','009009','009010','009020','009030','009031','009040',
		'009050','009060','009070','009080','009081','009082','009083','009084','009085','009086','009087','009088','009089',
		'009160','009170','009190',

		'010512','010525','010603','010803','010904','011003','011103','011302','011313','011324','011624','011734','012103','012223','012234',
		'012414','012427','012503','012824','012846','013203','020224','020435','020701','020834','021103','021325','021434',
		'021534','021613','021614','021915','022324','022434','022503','022601','022612','022625','022734','022803','022901',
		'022912','022924','023003','023102','023113','023124','023402','023415','023635','023703','023825','030224','030803',
		'030511','031001','031103','031224''031301','031401','031501','031604','031824','031935','032434','032535','040424',
		'040624','040803','040924','041124','041201','041304','041423','041434','050703','051525','051724','060503','060823',
		'060903','061003','061103','062002','062403','062514','062903','063302','063401','070303','071103','071205','071316',
		'071701','072123','072623','073301','073303','073401','073903','075505','080734','080834','080903','081401','081615',
		'082506','082634','082734','082803','083001','083012','083023','083034','083103','083324','083514','083734','083803',
		'083902','083914','084134','084534',
			
		
		
		'092211','092212','092213','092214','092215','092216','092310','092320','092330','092340','092350') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_TYPE = 1 WHERE PL_CODE IN ('010501','011211','011601','011612',
		'011701','011712','011801','011901','012201','012212','012302','012801','012812','020201','020212','020401','020412',
		'020502','020801','020812','021301','021401','021412','021501','021512','021801','022301','022312','022401','022412',
		'022701','022712','023302','023601','023612','023801','023812','030201','030212','030601','030701','030901','031201',
		'031212','031801','031901','031912','032011','032311','032401','032412','032501','032512','032601','040301','040312',
		'040401','040412','040601','040612','040901','040912','041101','041112','041401','041412','050201','050212','051501',
		'051512','051701','051712','060802','061502','061601','061701','061712','061902','062102','062201','062212','062301',
		'062312','062601','062701','062712','062802','063001','063012','063101','063112','063201','063212','063502','071401',
		'071412','071501','071512','071601','071612','071801','071812','071901','071912','072001','072003','072101','072112',
		'072301','072401','072412','072601','072612','072802','072901','073001','073012','073101','073112','073201','073212',
		'073501','073512','073601','073612','073701','073712','074101','074112','074202','074301','074312','074401','074412',
		'074501','074512','074601','074701','074802','074811','074901','074912','075201','075602','075702','080502','080701',
		'080712','080801','080812','081001','081501','082001','082101','082201','082301','082601','082612','082701','082712',
		'083301','083312','083701','083712','084101','084412','084301','084501','084512','092380','092370',


		'000221','000222','001410','001420',
		
		) ";
		$db_dpis->send_cmd($cmd);          
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_TYPE = 2 WHERE PL_CODE IN (
		'010403','010703','010903','011013','011203','011403','011503','011723','012003','012403','012603','012613','012626','012703',
		'012833','012903','013003','013103','013303','013405','013503','020103','020303','020423','020603','020823','020903','021003','021203','021313',
		'021423','021523','021603','021703','021813','021903','022003','022103','022203','022423','022513','022723','023203','023503','023623','023903',
		'030103','030306','030403','030503','031703','031813','031923','032003','032103','032203','032303','032423','032523','040103','040203','040323',
		'040503','040703','041003','041443','041503','050103','050223','050303','050403','050503','050603','050803','050903','051003','051103','051203',
		'051303','051403','051603','051803','060104','060204','060304','060403','060603','060703','060813','061203','061303','061403','061514','061523',
		'061723','061803','061913','062503','063023','063123','063603','070103''070203','070403','070503','070603','070703','070803','070903',
		'071003','071303','071523','071623','072203','072503','072703','073123','073803','073813','074003','074213','074423','075003','075103','075303',
		'075403','080103','080203','080303','080403','080513','080603','080613','080723','080823','080913','080923','081103','081203','081303','081603',
		'081703','081803','081903','082403','082623','082723','082903','083203','083403','083503','083603','083723','083823','084003','084123','084203',
		'084403','084523') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_TYPE = 5 WHERE PL_CODE IN (
		'000231','001250','010404','010604','04032' ,'05110', '082804',
		'092101','092102','092103','092104','092105','092106','092107','092108','092201','092202','092203','092204','092205',
		'092206','092207','092208','092209','092210') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_TYPE = 4 WHERE PL_CODE IN ('010108','010209','010307') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc")
			$cmd = " ALTER TABLE PER_LINE DROP CONSTRAINT INXU1_PER_LINE ";
		elseif($DPISDB=="oci8")
			$cmd = " DROP INDEX INXU1_PER_LINE ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LINE DROP CONSTRAINT INXU1_PER_LINE ";
		
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc")
			$cmd = " ALTER TABLE PER_LINE DROP CONSTRAINT INXU2_PER_LINE ";
		elseif($DPISDB=="oci8")
			$cmd = " DROP INDEX INXU2_PER_LINE ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_LINE DROP CONSTRAINT INXU2_PER_LINE ";
		
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = NULL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " DELETE FROM PER_LINE WHERE PL_CODE IN ('04032','05110') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " ALTER TABLE PER_COMDTL ALTER CMD_SEQ INTEGER NULL ";
		elseif($DPISDB=="oci8") 
			$cmd = " ALTER TABLE PER_COMDTL MODIFY CMD_SEQ NUMBER(6) NULL ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_COMDTL ALTER CMD_SEQ INTEGER(6) NULL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$table = array("per_skill_group", "per_position", "per_pos_move", "per_assign_dtl", "per_order_dtl", "per_req1_dtl1", 
								    "per_req2_dtl", "per_req3_dtl", "per_positionhis", "per_comdtl");

		for ( $i=0; $i<count($table); $i++ ) { 
			$cmd = " select distinct PL_CODE from $table[$i] where PL_CODE like '5%' ";				  
			$count_temp = $db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			while ($data = $db_dpis->get_array()) {
				$old_code = trim($data[PL_CODE]);
				$new_code = '0'.substr($old_code,1,5);
				$cmd = " select pl_name from per_line where pl_code='$new_code' ";
				$count_data = $db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				if (!$count_data) {
					$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
					         SELECT '$new_code', PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE 
							 FROM PER_LINE WHERE PL_CODE ='$old_code' "; 
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				}
		
				$cmd = " update per_skill_group set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
		
				$cmd = " update per_position set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
		
				$cmd = " update per_pos_move set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
		
				$cmd = " update per_assign_dtl set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
		
				$cmd = " update per_order_dtl set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
		
				$cmd = " update per_req1_dtl1 set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
		
				$cmd = " update per_req2_dtl set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
		
				$cmd = " update per_req3_dtl set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
		
				$cmd = " update per_positionhis set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
		
				$cmd = " update per_transfer_req set pl_code_1='$new_code' where pl_code_1='$old_code' ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
		
				$cmd = " update per_transfer_req set pl_code_2='$new_code' where pl_code_2='$old_code' ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
		
				$cmd = " update per_transfer_req set pl_code_3='$new_code' where pl_code_3='$old_code' ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
		
				$cmd = " update per_comdtl set pl_code='$new_code' where pl_code='$old_code' ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
		
//				$cmd = " update per_comdtl set pl_code_assign='$new_code' where pl_code_assign='$old_code' ";
//				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
		
				$cmd = " update per_move_req set pl_code_1='$new_code' where pl_code_1='$old_code' ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
		
				$cmd = " update per_move_req set pl_code_2='$new_code' where pl_code_2='$old_code' ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
		
				$cmd = " update per_move_req set pl_code_3='$new_code' where pl_code_3='$old_code' ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
		
				$cmd = " delete from per_line where pl_code='$old_code' ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			}	// while ($data = $db_dpis->get_array()) 
		} // end for

		$cmd = " select SUM_ID, SUM_TYPE, PL_CODE, CL_NAME, PT_CODE, SUM_QTY, UPDATE_USER, UPDATE_DATE from PER_SUM_DTL2 where PL_CODE like '5%' ";
		$count_temp = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
			$SUM_ID = $data[SUM_ID];
			$SUM_TYPE = $data[SUM_TYPE];
			$old_code = trim($data[PL_CODE]);
			$new_code = '0'.substr($old_code,1,5);
			$CL_NAME = trim($data[CL_NAME]);
			$PT_CODE = trim($data[PT_CODE]);
			$SUM_QTY = $data[SUM_QTY];
			$UPDATE_USER = $data[UPDATE_USER];
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select pl_name from per_line where pl_code='$new_code' ";
			$count_data = $db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
			if (!$count_data) {
				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
				         SELECT '$new_code', PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE 
						 FROM PER_LINE WHERE PL_CODE ='$old_code' "; 
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			}
		
			$cmd = " delete PER_SUM_DTL2 where SUM_ID = $SUM_ID and SUM_TYPE = $SUM_TYPE and PL_CODE='$old_code' and CL_NAME = '$CL_NAME' and PT_CODE = '$PT_CODE' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " insert into PER_SUM_DTL2 (SUM_ID, SUM_TYPE, PL_CODE, CL_NAME, PT_CODE, SUM_QTY, UPDATE_USER, UPDATE_DATE)
					 values ($SUM_ID, $SUM_TYPE, '$new_code', '$CL_NAME', '$PT_CODE', $SUM_QTY, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " delete from per_line where pl_code='$old_code' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		}	// while ($data = $db_dpis->get_array()) 

		$cmd = " select SUM_ID, PL_CODE, SUM_WITH_MONEY, SUM_NO_MONEY, UPDATE_USER, UPDATE_DATE from PER_SUM_DTL3 where PL_CODE like '5%' ";
		$count_temp = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
			$SUM_ID = $data[SUM_ID];
			$old_code = trim($data[PL_CODE]);
			$new_code = '0'.substr($old_code,1,5);
			$SUM_WITH_MONEY = $data[SUM_WITH_MONEY];
			$SUM_NO_MONEY = $data[SUM_NO_MONEY];
			$UPDATE_USER = $data[UPDATE_USER];
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select pl_name from per_line where pl_code='$new_code' ";
			$count_data = $db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
			if (!$count_data) {
				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
				         SELECT '$new_code', PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE 
						 FROM PER_LINE WHERE PL_CODE ='$old_code' "; 
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			}
		
			$cmd = " delete PER_SUM_DTL3 where SUM_ID = $SUM_ID and PL_CODE='$old_code' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " insert into PER_SUM_DTL3 (SUM_ID, PL_CODE, SUM_WITH_MONEY, SUM_NO_MONEY, UPDATE_USER, UPDATE_DATE)
					 values ($SUM_ID, '$new_code', $SUM_WITH_MONEY, $SUM_NO_MONEY, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " delete from per_line where pl_code='$old_code' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		}	// while ($data = $db_dpis->get_array()) 

		$cmd = " select SUM_ID, PL_CODE, LEVEL_NO, SUM_QTY_M, SUM_QTY_F, UPDATE_USER, UPDATE_DATE from PER_SUM_DTL8 where PL_CODE like '5%' ";
		$count_temp = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
			$SUM_ID = $data[SUM_ID];
			$old_code = trim($data[PL_CODE]);
			$new_code = '0'.substr($old_code,1,5);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$SUM_QTY_M = $data[SUM_QTY_M];
			$SUM_QTY_F = $data[SUM_QTY_F];
			$UPDATE_USER = $data[UPDATE_USER];
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select pl_name from per_line where pl_code='$new_code' ";
			$count_data = $db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
			if (!$count_data) {
				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
				         SELECT '$new_code', PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE 
						 FROM PER_LINE WHERE PL_CODE ='$old_code' "; 
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			}
		
			$cmd = " delete PER_SUM_DTL8 where SUM_ID = $SUM_ID and PL_CODE='$old_code' and LEVEL_NO = '$LEVEL_NO' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " insert into PER_SUM_DTL8 (SUM_ID, PL_CODE, LEVEL_NO, SUM_QTY_M, SUM_QTY_F, UPDATE_USER, UPDATE_DATE)
					 values ($SUM_ID, '$new_code', '$LEVEL_NO', $SUM_QTY_M, $SUM_QTY_F, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " delete from per_line where pl_code='$old_code' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		}	// while ($data = $db_dpis->get_array()) 

		$cmd = " select SUM_ID, PL_CODE, EL_CODE, SUM_QTY_M, SUM_QTY_F, UPDATE_USER, UPDATE_DATE from PER_SUM_DTL9 where PL_CODE like '5%' ";
		$count_temp = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
			$SUM_ID = $data[SUM_ID];
			$old_code = trim($data[PL_CODE]);
			$new_code = '0'.substr($old_code,1,5);
			$EL_CODE = trim($data[EL_CODE]);
			$SUM_QTY_M = $data[SUM_QTY_M];
			$SUM_QTY_F = $data[SUM_QTY_F];
			$UPDATE_USER = $data[UPDATE_USER];
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select pl_name from per_line where pl_code='$new_code' ";
			$count_data = $db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
			if (!$count_data) {
				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
				         SELECT '$new_code', PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE 
						 FROM PER_LINE WHERE PL_CODE ='$old_code' "; 
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			}
		
			$cmd = " delete PER_SUM_DTL9 where SUM_ID = $SUM_ID and PL_CODE='$old_code' and EL_CODE = '$EL_CODE' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " insert into PER_SUM_DTL9 (SUM_ID, PL_CODE, EL_CODE, SUM_QTY_M, SUM_QTY_F, UPDATE_USER, UPDATE_DATE)
					 values ($SUM_ID, '$new_code', '$EL_CODE', $SUM_QTY_M, $SUM_QTY_F, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " delete from per_line where pl_code='$old_code' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		}	// while ($data = $db_dpis->get_array()) 

		$cmd = " select SUM_ID, PL_CODE, LEVEL_NO, EL_CODE, SUM_QTY_M, SUM_QTY_F, UPDATE_USER, UPDATE_DATE from PER_SUM_DTL10 where PL_CODE like '5%' ";
		$count_temp = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
			$SUM_ID = $data[SUM_ID];
			$old_code = trim($data[PL_CODE]);
			$new_code = '0'.substr($old_code,1,5);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$EL_CODE = trim($data[EL_CODE]);
			$SUM_QTY_M = $data[SUM_QTY_M];
			$SUM_QTY_F = $data[SUM_QTY_F];
			$UPDATE_USER = $data[UPDATE_USER];
			$UPDATE_DATE = trim($data[UPDATE_DATE]);

			$cmd = " select pl_name from per_line where pl_code='$new_code' ";
			$count_data = $db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
			if (!$count_data) {
				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
				         SELECT '$new_code', PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE 
						 FROM PER_LINE WHERE PL_CODE ='$old_code' "; 
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			}
		
			$cmd = " delete PER_SUM_DTL10 where SUM_ID = $SUM_ID and PL_CODE='$old_code' and LEVEL_NO = '$LEVEL_NO' and EL_CODE = '$EL_CODE' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " insert into PER_SUM_DTL10 (SUM_ID, PL_CODE, LEVEL_NO, EL_CODE, SUM_QTY_M, SUM_QTY_F, UPDATE_USER, UPDATE_DATE)
					 values ($SUM_ID, '$new_code', '$LEVEL_NO', '$EL_CODE', $SUM_QTY_M, $SUM_QTY_F, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " delete from per_line where pl_code='$old_code' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		}	// while ($data = $db_dpis->get_array()) 

		$cmd = " select distinct PL_CODE_ASSIGN from PER_COMDTL where PL_CODE_ASSIGN like '5%' ";				  
		$count_temp = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
			$old_code = trim($data[PL_CODE_ASSIGN]);
			$new_code = '0'.substr($old_code,1,5);
			$cmd = " select pl_name from per_line where pl_code='$new_code' ";
			$count_data = $db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
			if (!$count_data) {
				$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
				         SELECT '$new_code', PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE 
						 FROM PER_LINE WHERE PL_CODE ='$old_code' "; 
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			}
		
			$cmd = " update per_skill_group set pl_code='$new_code' where pl_code='$old_code' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " update per_position set pl_code='$new_code' where pl_code='$old_code' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " update per_pos_move set pl_code='$new_code' where pl_code='$old_code' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " update per_assign_dtl set pl_code='$new_code' where pl_code='$old_code' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " update per_order_dtl set pl_code='$new_code' where pl_code='$old_code' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " update per_req1_dtl1 set pl_code='$new_code' where pl_code='$old_code' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " update per_req2_dtl set pl_code='$new_code' where pl_code='$old_code' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " update per_req3_dtl set pl_code='$new_code' where pl_code='$old_code' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " update per_positionhis set pl_code='$new_code' where pl_code='$old_code' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " update per_transfer_req set pl_code_1='$new_code' where pl_code_1='$old_code' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " update per_transfer_req set pl_code_2='$new_code' where pl_code_2='$old_code' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " update per_transfer_req set pl_code_3='$new_code' where pl_code_3='$old_code' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " update per_comdtl set pl_code='$new_code' where pl_code='$old_code' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
//			$cmd = " update per_comdtl set pl_code_assign='$new_code' where pl_code_assign='$old_code' ";
//			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " update per_move_req set pl_code_1='$new_code' where pl_code_1='$old_code' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " update per_move_req set pl_code_2='$new_code' where pl_code_2='$old_code' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " update per_move_req set pl_code_3='$new_code' where pl_code_3='$old_code' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		
			$cmd = " delete from per_line where pl_code='$old_code' ";
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		}	// while ($data = $db_dpis->get_array()) 

		$cmd = " DELETE FROM PER_LINE WHERE PL_CODE LIKE '5%' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc" || $DPISDB=="mysql") 
			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
			         SELECT '5'+mid(PL_CODE,2), PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE 
					 FROM PER_LINE WHERE PL_CODE IN 
				 ('010108', '010209', '010307', '010403', '010501', '010703', '010903', '011013', '011203', '011211', '011503', '011612', '011712', '011723', '012003', 
				  '012212', '012302', '012403', '012603', '012613', '012626', '012703', '012812', '012833', '012903', '013003', '013103', '013303', '013405', 
				  '013503', '020103', '020212', '020303', '020412', '020423', '020502', '020603', '020812', '020823', '020903', '021003', '021203', '021301', '021412', 
				  '021423', '021512', '021523', '021603', '021703', '021903', '022003', '022103', '022203', '022312', '022412', '022423', '022513', '022712', '022723', 
				  '023203', '023302', '023503', '023612', '023623', '023903', '030103', '030212', '030306', '030403', '030503', '030601', '031212', '031813', '031801', 
				  '031912', '031923', '032003', '032011', '032423', '032512', '032523', '032601', '040103', '040203', '040312', '040323', '040412', '040503', '040612', 
				  '040703', '040912', '041003', '041112', '041412', '041443', '041503', '050103', '050212', '050223', '050303', '050403', '050503', '050803', 
				  '050903', '051003', '051103', '051303', '051403', '051512', '051603', '051712', '051803', '060104', '060204', '060304', '060403', '060603', 
				  '060703', '060802', '060813', '061203', '061502', '061514', '061523', '061712', '061723', '061803', '061902', '061913', '062102', '062212', '062312', 
				  '062503', '062712', '062802', '063502', '063603', '070103', '070203', '070403', '070503', '070603', '070703', '070803', '070903', '071003', '071303', 
				  '071412', '071512', '071523', '071612', '071623', '071812', '071912', '072112', '072203', '072412', '072503', '072612', '072703', '072802', '072901', 
				  '073012', '073123', '073512', '073712', '073803', '073813', '074003', '074112', '074202', '074213', '074312', '074412', '074423', '074512', '074601', 
				  '074701', '074802', '074912', '075003', '075103', '075201', '075303', '075403', '075602', '075702', '080103', '080403', '080502', '080513', '080712', 
				  '080723', '080812', '080823', '080913', '080923', '081001', '081103', '081203', '081303', '081501', '081603', '081703', '081803', '081903', '082001', 
				  '082101', '082201', '082301', '082403', '082612', '082623', '082712', '082723', '082903', '083203', '083312', '083403', '083503', '083603', '083712', 
				  '084003', '084101', '084123', '084203', '084301', '084403', '092391', '092394', '092395', '092413') ";    
		elseif($DPISDB=="oci8") 
			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
			         SELECT '5'||substr(PL_CODE,2), PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE 
					 FROM PER_LINE WHERE PL_CODE IN 
				 ('010108', '010209', '010307', '010403', '010501', '010703', '010903', '011013', '011203',	'011211', '011503', '011612', '011712', '011723', '012003', 
				  '012212', '012302', '012403', '012603', '012613', '012626', '012703', '012812', '012833', '012903', '013003', '013103', '013303', '013405', 
				  '013503', '020103', '020212', '020303', '020412', '020423', '020502', '020603', '020812', '020823', '020903', '021003', '021203', '021301', '021412', 
				  '021423', '021512', '021523', '021603', '021703', '021903', '022003', '022103', '022203', '022312', '022412', '022423', '022513', '022712', '022723', 
				  '023203', '023302', '023503', '023612', '023623', '023903', '030103', '030212', '030306', '030403', '030503', '030601', '031212', '031813', '031801', 
				  '031912', '031923', '032003', '032011', '032423', '032512', '032523', '032601', '040103', '040203', '040312', '040323', '040412', '040503', '040612', 
				  '040703', '040912', '041003', '041112', '041412', '041443', '041503', '050103', '050212', '050223', '050303', '050403', '050503', '050803', 
				  '050903', '051003', '051103', '051303', '051403', '051512', '051603', '051712', '051803', '060104', '060204', '060304', '060403', '060603', 
				  '060703', '060802', '060813', '061203', '061502', '061514', '061523', '061712', '061723', '061803', '061902', '061913', '062102', '062212', '062312', 
				  '062503', '062712', '062802', '063502', '063603', '070103', '070203', '070403', '070503', '070603', '070703', '070803', '070903', '071003', '071303', 
				  '071412', '071512', '071523', '071612', '071623', '071812', '071912', '072112', '072203', '072412', '072503', '072612', '072703', '072802', '072901', 
				  '073012', '073123', '073512', '073712', '073803', '073813', '074003', '074112', '074202', '074213', '074312', '074412', '074423', '074512', '074601', 
				  '074701', '074802', '074912', '075003', '075103', '075201', '075303', '075403', '075602', '075702', '080103', '080403', '080502', '080513', '080712',
				  '080723', '080812', '080823', '080913', '080923', '081001', '081103', '081203', '081303', '081501', '081603', '081703', '081803', '081903', '082001', 
				  '082101', '082201', '082301', '082403', '082612', '082623', '082712', '082723', '082903', '083203', '083312', '083403', '083503', '083603', '083712', 
				  '084003', '084101', '084123', '084203', '084301', '084403', '092391', '092394', '092395', '092413') ";    
		
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc" || $DPISDB=="mysql") 
			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '5'+mid(PL_CODE,2) WHERE PL_CODE IN 
				 ('010108', '010209', '010307', '010403', '010501', '010703', '010903', '011013', '011203',	'011211', '011503', '011612', '011712', '011723', '012003', 
				  '012212', '012302', '012403', '012603', '012613', '012626', '012703', '012812', '012833', '012903', '013003', '013103', '013303', '013405', 
				  '013503', '020103', '020212', '020303', '020412', '020423', '020502', '020603', '020812', '020823', '020903', '021003', '021203', '021301', '021412', 
				  '021423', '021512', '021523', '021603', '021703', '021903', '022003', '022103', '022203', '022312', '022412', '022423', '022513', '022712', '022723', 
				  '023203', '023302', '023503', '023612', '023623', '023903', '030103', '030212', '030306', '030403', '030503', '030601', '031212', '031813', '031801', 
				  '031912', '031923', '032003', '032011', '032423', '032512', '032523', '032601', '040103', '040203', '040312', '040323', '040412', '040503', '040612', 
				  '040703', '040912', '041003', '041112', '041412', '041443', '041503', '050103', '050212', '050223', '050303', '050403', '050503', '050803', 
				  '050903', '051003', '051103', '051303', '051403', '051512', '051603', '051712', '051803', '060104', '060204', '060304', '060403', '060603', 
				  '060703', '060802', '060813', '061203', '061502', '061514', '061523', '061712', '061723', '061803', '061902', '061913', '062102', '062212', '062312', 
				  '062503', '062712', '062802', '063502', '063603', '070103', '070203', '070403', '070503', '070603', '070703', '070803', '070903', '071003', '071303', 
				  '071412', '071512', '071523', '071612', '071623', '071812', '071912', '072112', '072203', '072412', '072503', '072612', '072703', '072802', '072901', 
				  '073012', '073123', '073512', '073712', '073803', '073813', '074003', '074112', '074202', '074213', '074312', '074412', '074423', '074512', '074601', 
				  '074701', '074802', '074912', '075003', '075103', '075201', '075303', '075403', '075602', '075702', '080103', '080403', '080502', '080513', '080712',
				  '080723', '080812', '080823', '080913', '080923', '081001', '081103', '081203', '081303', '081501', '081603', '081703', '081803', '081903', '082001', 
				  '082101', '082201', '082301', '082403', '082612', '082623', '082712', '082723', '082903', '083203', '083312', '083403', '083503', '083603', '083712', 
				  '084003', '084101', '084123', '084203', '084301', '084403', '092391', '092394', '092395', '092413') ";    
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '5'||substr(PL_CODE,2) WHERE PL_CODE IN 
				 ('010108', '010209', '010307', '010403', '010501', '010703', '010903', '011013', '011203',	'011211', '011503', '011612', '011712', '011723', '012003', 
				  '012212', '012302', '012403', '012603', '012613', '012626', '012703', '012812', '012833', '012903', '013003', '013103', '013303', '013405', 
				  '013503', '020103', '020212', '020303', '020412', '020423', '020502', '020603', '020812', '020823', '020903', '021003', '021203', '021301', '021412', 
				  '021423', '021512', '021523', '021603', '021703', '021903', '022003', '022103', '022203', '022312', '022412', '022423', '022513', '022712', '022723', 
				  '023203', '023302', '023503', '023612', '023623', '023903', '030103', '030212', '030306', '030403', '030503', '030601', '031212', '031813', '031801', 
				  '031912', '031923', '032003', '032011', '032423', '032512', '032523', '032601', '040103', '040203', '040312', '040323', '040412', '040503', '040612', 
				  '040703', '040912', '041003', '041112', '041412', '041443', '041503', '050103', '050212', '050223', '050303', '050403', '050503', '050803', 
				  '050903', '051003', '051103', '051303', '051403', '051512', '051603', '051712', '051803', '060104', '060204', '060304', '060403', '060603', 
				  '060703', '060802', '060813', '061203', '061502', '061514', '061523', '061712', '061723', '061803', '061902', '061913', '062102', '062212', '062312', 
				  '062503', '062712', '062802', '063502', '063603', '070103', '070203', '070403', '070503', '070603', '070703', '070803', '070903', '071003', '071303', 
				  '071412', '071512', '071523', '071612', '071623', '071812', '071912', '072112', '072203', '072412', '072503', '072612', '072703', '072802', '072901', 
				  '073012', '073123', '073512', '073712', '073803', '073813', '074003', '074112', '074202', '074213', '074312', '074412', '074423', '074512', '074601', 
				  '074701', '074802', '074912', '075003', '075103', '075201', '075303', '075403', '075602', '075702', '080103', '080403', '080502', '080513', '080712',
				  '080723', '080812', '080823', '080913', '080923', '081001', '081103', '081203', '081303', '081501', '081603', '081703', '081803', '081903', '082001', 
				  '082101', '082201', '082301', '082403', '082612', '082623', '082712', '082723', '082903', '083203', '083312', '083403', '083503', '083603', '083712', 
				  '084003', '084101', '084123', '084203', '084301', '084403', '092391', '092394', '092395', '092413') ";    
		
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
						  VALUES ('510109', 'นักบริหารการทูต', 'นักบริหารการทูต', 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
						  VALUES ('510210', 'ผู้ตรวจราชการกรม', 'ผู้ตรวจราชการกรม', 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
						  VALUES ('510308', 'ผู้อำนวยการ', 'ผู้อำนวยการ', 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
						  VALUES ('510404', 'นักส่งเสริมการปกครองท้องถิ่น', 'นักส่งเสริมการปกครองท้องถิ่น', 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
						  VALUES ('510502', 'เจ้าพนักงานส่งเสริมการปกครองท้องถิ่น', 'เจ้าพนักงานส่งเสริมการปกครองท้องถิ่น', 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
						  VALUES ('511014', 'นักเทคโนโลยีสารสนเทศ', 'นักเทคโนโลยีสารสนเทศ', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
						  VALUES ('511104', 'นักจัดการงานทั่วไป', 'นักจัดการงานทั่วไป', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
						  VALUES ('520104', 'นักวิเคราะห์รัฐวิสาหกิจ', 'นักวิเคราะห์รัฐวิสาหกิจ', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
						  VALUES ('560105', 'นักการแพทย์แผนไทย', 'นักการแพทย์แผนไทย', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
						  VALUES ('561204', 'นักจิตวิทยาคลินิค', 'นักจิตวิทยาคลินิค', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
						  VALUES ('561914', 'นักกิจกรรมบำบัด', 'นักกิจกรรมบำบัด', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
						  VALUES ('561915', 'นักเวชศาสตร์การสื่อความหมาย', 'นักเวชศาสตร์การสื่อความหมาย', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
						  VALUES ('570704', 'นักวิชาการพลังงาน', 'นวก.พลังงาน', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
						  VALUES ('575612', 'นักกายอุปกรณ์', 'นักกายอุปกรณ์', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
						  VALUES ('584401', 'เจ้าพนักงานประเมินราคาทรัพย์สิน', 'เจ้าพนักงานประเมินราคาทรัพย์สิน', 1, $SESS_USERID, '$UPDATE_DATE', 1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
						  VALUES ('584555', 'เจ้าพนักงานพัฒนาสังคม', 'เจ้าพนักงานพัฒนาสังคม', 1, $SESS_USERID, '$UPDATE_DATE', 1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE)
						  VALUES ('584578', 'นักพัฒนาสังคม', 'นักพัฒนาสังคม', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'ผู้ตรวจราชการกระทรวง', PL_SHORTNAME = 'ผู้ตรวจราชการกระทรวง' WHERE PL_CODE = '510209' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'นักวิเคราะห์นโยบายและแผน', PL_SHORTNAME = 'นักวิเคราะห์นโยบายและแผน' WHERE PL_CODE = '510703' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'เจ้าพนักงานอาลักษณ์', PL_SHORTNAME = 'เจ้าพนักงานอาลักษณ์' WHERE PL_CODE = '511211' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'นักทรัพยากรบุคคล', PL_SHORTNAME = 'นักทรัพยากรบุคคล' WHERE PL_CODE = '510903' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '510903' WHERE PL_CODE IN ('010903','011403','080203','080303','080603','080613') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานธุรการ

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'นักทะเบียนวิชาชีพ', PL_SHORTNAME = 'นักทะเบียนวิชาชีพ' WHERE PL_CODE = '511503' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '511612' WHERE PL_CODE IN ('011601','011612','011801','011901') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานธุรการ

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '511712' WHERE PL_CODE IN ('011701','011712') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าหน้าที่พัสดุ

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '512212' WHERE PL_CODE IN ('012201','012212') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานสถิติ

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'เจ้าพนักงานเวชสถิติ', PL_SHORTNAME = 'เจ้าพนักงานเวชสถิติ' WHERE PL_CODE = '512302' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'นักสืบสวนสอบสวน', PL_SHORTNAME = 'นักสืบสวนสอบสวน' WHERE PL_CODE = '512603' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '512812' WHERE PL_CODE IN ('012801','012812') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานราชทัณฑ์

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'นักการทูต', PL_SHORTNAME = 'นักการทูต' WHERE PL_CODE = '512903' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'นักวิเทศสัมพันธ์', PL_SHORTNAME = 'นักวิเทศสัมพันธ์' WHERE PL_CODE = '513003' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'นักวิเทศสหการ', PL_SHORTNAME = 'นักวิเทศสหการ' WHERE PL_CODE = '513103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '520212' WHERE PL_CODE IN ('020201','020212') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานการคลัง

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '520412' WHERE PL_CODE IN ('020401','020412') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานการเงินและบัญชี

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'เจ้าพนักงานดูเงิน', PL_SHORTNAME = 'เจ้าพนักงานดูเงิน' WHERE PL_CODE = '520502' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'นักวิชาการตรวจสอบภายใน', PL_SHORTNAME = 'นวก.ตรวจสอบภายใน' WHERE PL_CODE = '520603' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '520812' WHERE PL_CODE IN ('020801','020812') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานตรวจสอบบัญชี

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'นักวิเคราะห์งบประมาณ', PL_SHORTNAME = 'นักวิเคราะห์งบประมาณ' WHERE PL_CODE = '520903' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '521203' WHERE PL_CODE IN ('021203','021313') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นักวิชาการศุลกากร

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'นักวิชาการศุลกากร', PL_SHORTNAME = 'นวก.ศุลกากร' WHERE PL_CODE = '521203' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'เจ้าพนักงานศุลกากร', PL_SHORTNAME = 'เจ้าพนักงานศุลกากร' WHERE PL_CODE = '521301' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '521412' WHERE PL_CODE IN ('021401','021412','021801') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานสรรพสามิต

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '521512' WHERE PL_CODE IN ('021501','021512') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานสรรพากร

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'นักตรวจสอบภาษี', PL_SHORTNAME = 'นักตรวจสอบภาษี' WHERE PL_CODE = '521703' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '521423' WHERE PL_CODE IN ('021423','021813') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นักวิชาการสรรพสามิต

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'นักวิชาการส่งเสริมการลงทุน', PL_SHORTNAME = 'นวก.ส่งเสริมการลงทุน' WHERE PL_CODE = '522103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'เจ้าพนักงานส่งเสริมอุตสาหกรรม', PL_SHORTNAME = 'เจ้าพนักงานส่งเสริมอุตสาหกรรม' WHERE PL_CODE = '523302' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '521301' WHERE PL_CODE IN ('021301','023801','023812') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานศุลกากร

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '522312' WHERE PL_CODE IN ('022301','022312') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานส่งเสริมสหกรณ์

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '522412' WHERE PL_CODE IN ('022401','022412') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานการพาณิชย์

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '522712' WHERE PL_CODE IN ('022701','022712') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานชั่งตวงวัด

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '523612' WHERE PL_CODE IN ('023601','023612') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานทรัพยากรธรณี

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '530212' WHERE PL_CODE IN ('030201','030212') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานขนส่ง

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '530601' WHERE PL_CODE IN ('030601','030701') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานเดินเรือ

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'เจ้าพนักงานเดินเรือ', PL_SHORTNAME = 'เจ้าพนักงานเดินเรือ' WHERE PL_CODE = '530601' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '531212' WHERE PL_CODE IN ('031201','031212') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานสื่อสาร

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '531801' WHERE PL_CODE IN ('031801','032401','032412') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานเผยแพร่ประชาสัมพันธ์

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'เจ้าพนักงานเผยแพร่ประชาสัมพันธ์', PL_SHORTNAME = 'เจ้าพนักงานเผยแพร่ประชาสัมพันธ์' 	WHERE PL_CODE = '531801' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '531813' WHERE PL_CODE IN ('031703','031813') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นักประชาสัมพันธ์

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '531912' WHERE PL_CODE IN ('031901','031912') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานการข่าว

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '532003' WHERE PL_CODE IN ('032003','032103','032203','032303') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นักสื่อสารมวลชน

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'นักสื่อสารมวลชน', PL_SHORTNAME = 'นักสื่อสารมวลชน' WHERE PL_CODE = '532003' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '532011' WHERE PL_CODE IN ('032011','032311') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); ผู้ประกาศและรายงานข่าว

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'ผู้ประกาศและรายงานข่าว', PL_SHORTNAME = 'ผู้ประกาศและรายงานข่าว' WHERE PL_CODE = '532011' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '532512' WHERE PL_CODE IN ('032501','032512') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานโสตทัศนศึกษา

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '532601' WHERE PL_CODE IN ('032601','030901') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าหน้าที่จราจรสื่อสารการบิน

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'เจ้าพนักงานสื่อสารการบิน', PL_SHORTNAME = 'เจ้าพนักงานสื่อสารการบิน' WHERE PL_CODE = '532601' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '540312' WHERE PL_CODE IN ('040301','040312') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นายช่างชลประทาน

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '540412' WHERE PL_CODE IN ('040401','040412') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานการเกษตร

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '540612' WHERE PL_CODE IN ('040601','040612') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานป่าไม้

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '540912' WHERE PL_CODE IN ('040901','040912') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานประมง

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '541112' WHERE PL_CODE IN ('041101','041112') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานสัตวบาล

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '541412' WHERE PL_CODE IN ('041401','041412') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานเคหกิจการเกษตร

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'นักวิชาการปฏิรูปที่ดิน', PL_SHORTNAME = 'นวก.ปฏิรูปที่ดิน' WHERE PL_CODE = '541503' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '550212' WHERE PL_CODE IN ('050201','050212') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานวิทยาศาสตร์

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '550503' WHERE PL_CODE IN ('050503','050603') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นักนิวเคลียร์เคมี

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '550803' WHERE PL_CODE IN ('051203','050803') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นักชีววิทยารังสี

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '551512' WHERE PL_CODE IN ('051501','051512') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานอุทกวิทยา

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '551712' WHERE PL_CODE IN ('051701','051712') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานอุตุนิยมวิทยา

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '561502' WHERE PL_CODE IN ('061502','061601') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); พยาบาลเทคนิค

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '561712' WHERE PL_CODE IN ('061701','061712') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานรังสีการแพทย์

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'เจ้าพนักงานรังสีการแพทย์', PL_SHORTNAME = 'เจ้าพนักงานรังสีการแพทย์' WHERE PL_CODE = '561712' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'เจ้าพนักงานอาชีวบำบัด', PL_SHORTNAME = 'เจ้าพนักงานอาชีวบำบัด' WHERE PL_CODE = '561902' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '562212' WHERE PL_CODE IN ('062201','062212') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานวิทยาศาสตร์การแพทย์

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '562312' WHERE PL_CODE IN ('062301','062312') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานเภสัชกรรม

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '562503' WHERE PL_CODE IN ('061303','061403','062503','063023','063123') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นักวิชาการสาธารณสุข

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '562802' WHERE PL_CODE IN ('062601','062802') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานทันตสาธารณสุข

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '562712' WHERE PL_CODE IN ('062701','062712') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานเวชกรรมฟื้นฟู

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '563502' WHERE PL_CODE IN ('063001','063012','063101','063112','063201','063212','063502') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานสาธารณสุข

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'เจ้าพนักงานสาธารณสุข', PL_SHORTNAME = 'เจ้าพนักงานสาธารณสุข' WHERE PL_CODE = '563502' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '571512' WHERE PL_CODE IN ('071501','071512') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นายช่างรังวัด

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '571612' WHERE PL_CODE IN ('071601','071612') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นายช่างสำรวจ

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '571812' WHERE PL_CODE IN ('071801','071812') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นายช่างขุดลอก

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '571912' WHERE PL_CODE IN ('071901','071912','072001','072003','072301') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นายช่างเครื่องกล

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '572112' WHERE PL_CODE IN ('072101','072112') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นายช่างขุดลอก

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'เจ้าพนักงานตรวจโรงงาน', PL_SHORTNAME = 'เจ้าพนักงานตรวจโรงงาน' WHERE PL_CODE = '572802' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'เจ้าพนักงานเครื่องคอมพิวเตอร์', PL_SHORTNAME = 'เจ้าพนักงานเครื่องคอมพิวเตอร์' WHERE PL_CODE = '572901' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '573012' WHERE PL_CODE IN ('073001','073012','073101','073112','073201','073212') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นายช่างไฟฟ้า

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '573512' WHERE PL_CODE IN ('073501','073512','073601','073612') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นายช่างเทคนิค

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '573712' WHERE PL_CODE IN ('073701','073712') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นายช่างเหมืองแร่

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'มัณฑนากร', PL_SHORTNAME = 'มัณฑนากร' WHERE PL_CODE in ('074003','574003') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'เจ้าพนักงานออกแบบผลิตภัณฑ์', PL_SHORTNAME = 'เจ้าพนักงานออกแบบผลิตภัณฑ์' WHERE PL_CODE = '574202' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '574112' WHERE PL_CODE IN ('074101','074112') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นายช่างออกแบบเรือ

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '574312' WHERE PL_CODE IN ('074301','074312') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นายช่างเขียนแบบ

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '574412' WHERE PL_CODE IN ('074401','074412') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นายช่างศิลป์

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '574512' WHERE PL_CODE IN ('074501','074512') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นายช่างศิลปกรรม

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'เจ้าพนักงานลิขิต', PL_SHORTNAME = 'เจ้าพนักงานลิขิต' WHERE PL_CODE = '574601' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'นายช่างหล่อ', PL_SHORTNAME = 'นายช่างหล่อ' WHERE PL_CODE = '574701' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'นายช่างพิมพ์', PL_SHORTNAME = 'นายช่างพิมพ์' WHERE PL_CODE = '574802' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '574802' WHERE PL_CODE IN ('074802','074811') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นายช่างพิมพ์

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '574912' WHERE PL_CODE IN ('074901','074912') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นายช่างภาพ

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'นักวิชาการแผนที่ภาพถ่าย', PL_SHORTNAME = 'นวก.แผนที่ภาพถ่าย' WHERE PL_CODE = '575103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '580712' WHERE PL_CODE IN ('080701','080712') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานอบรมและฝึกวิชาชีพ

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '580812' WHERE PL_CODE IN ('080801','080812') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานพัฒนาฝีมือแรงงาน

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'เจ้าพนักงานพัฒนาฝีมือแรงงาน', PL_SHORTNAME = 'เจ้าพนักงานพัฒนาฝีมือแรงงาน' WHERE PL_CODE = '580812' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'นักวิชาการพัฒนาฝีมือแรงงาน', PL_SHORTNAME = 'นวก.พัฒนาฝีมือแรงงาน' WHERE PL_CODE = '580823' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'เจ้าพนักงานห้องสมุด', PL_SHORTNAME = 'เจ้าพนักงานห้องสมุด' WHERE PL_CODE = '581501' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'เจ้าพนักงานพิพิธภัณฑ์', PL_SHORTNAME = 'เจ้าพนักงานพิพิธภัณฑ์' WHERE PL_CODE = '582001' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '582612' WHERE PL_CODE IN ('082601','082612') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานวัฒนธรรม

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '583203' WHERE PL_CODE IN ('083203','084523') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นักวิชาการแรงงาน

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'นักวิเคราะห์ผังเมือง', PL_SHORTNAME = 'นักวิเคราะห์ผังเมือง' WHERE PL_CODE = '583403' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '583712' WHERE PL_CODE IN ('083701','083712') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานพัฒนาชุมชน

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '584101' WHERE PL_CODE IN ('084101','084412') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานที่ดิน

		//$cmd = " UPDATE PER_LINE SET PL_NAME = 'เจ้าพนักงานที่ดิน', PL_SHORTNAME = 'เจ้าพนักงานที่ดิน' WHERE PL_CODE = '584101' ";
		//$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		$cmd = " UPDATE PER_LINE SET PL_NAME = 'นักวิชาการจัดหาที่ดิน', PL_SHORTNAME = 'นวก.จัดหาที่ดิน' WHERE PL_CODE = '584203' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'เจ้าพนักงานจดหมายเหตุ', PL_SHORTNAME = 'เจ้าพนักงานจดหมายเหตุ' WHERE PL_CODE = '584301' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		$cmd = " UPDATE PER_LINE SET PL_NAME = 'นักประเมินราคาทรัพย์สิน', PL_SHORTNAME = 'นักประเมินราคาทรัพย์สิน' WHERE PL_CODE = '584403' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '571412' WHERE PL_CODE IN ('071401','071412') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นายช่างโยธา

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '572412' WHERE PL_CODE IN ('072401','072412') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นายช่างโลหะ

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '572612' WHERE PL_CODE IN ('072601','072612') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นายช่างตรวจสภาพรถ

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '582712' WHERE PL_CODE IN ('082701','082712') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานการศาสนา

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '583312' WHERE PL_CODE IN ('083301','083312','084501','084512') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); เจ้าพนักงานแรงงาน

		$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '583603' WHERE PL_CODE IN ('083603','083723') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); นักวิชาการพัฒนาชุมชน

		$cmd = " UPDATE PER_LINE SET PL_NAME = 'พนักงานสอบสวนคดีพิเศษ', PL_SHORTNAME = 'พนักงานสอบสวนคดีพิเศษ' WHERE PL_CODE = '512626' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " DELETE FROM PER_LINE_GROUP ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LINE_GROUP (LG_CODE, LG_NAME, LG_ACTIVE, UPDATE_USER, UPDATE_DATE)
		SELECT trim(PL_CODE), PL_NAME, PL_ACTIVE, $SESS_USERID, '$UPDATE_DATE' FROM PER_LINE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการปกครอง' WHERE LG_CODE = '010603' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ควบคุมการอุตสาหกรรม' WHERE LG_CODE = '023402' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ช่วยเภสัชกร' WHERE LG_CODE = '062301' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิเคราะห์นโยบายและแผน' WHERE LG_CODE in ('010703','510703') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิเคราะห์ระบบงาน' WHERE LG_CODE = '010803' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิเคราะห์งานบุคคล' WHERE LG_CODE = '010903' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ทรัพยากรบุคคล' WHERE LG_CODE = '510903' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'เลขานุการและบริหารงาน' WHERE LG_CODE = '011103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิเคราะห์งานทะเบียนการค้า' WHERE LG_CODE = '022503' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการสอบ' WHERE LG_CODE = '080203' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'บริหาร' WHERE LG_CODE in ('010108','510108') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'บริหารการทูต' WHERE LG_CODE in ('510109') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ตรวจราชการ' WHERE LG_CODE = '010209' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ตรวจราชการกระทรวง' WHERE LG_CODE = '510209' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ตรวจราชการกรม' WHERE LG_CODE = '510210' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'บริหารงานปกครอง' WHERE LG_CODE in ('010307','510307') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'อำนวยการ' WHERE LG_CODE = '510308' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติการปกครอง' WHERE LG_CODE in ('010403','510403') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ประสานงานปกครอง' WHERE LG_CODE in ('010501','510501') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ส่งเสริมการปกครองท้องถิ่น' WHERE LG_CODE in ('510404','510502') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ช่วยงานปกครอง' WHERE LG_CODE = '010512' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ระบบงานคอมพิวเตอร์' WHERE LG_CODE = '011003' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิทยาการคอมพิวเตอร์' WHERE LG_CODE = '011013' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการคอมพิวเตอร์' WHERE LG_CODE = '511013' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการเทคโนโลยีสารสนเทศ' WHERE LG_CODE = '511014' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'จัดการงานทั่วไป' WHERE LG_CODE = '511104' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานอาลักษณ์' WHERE LG_CODE = '511211' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการประปา' WHERE LG_CODE = '011313' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'การเจ้าหน้าที่' WHERE LG_CODE = '011403' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'การทะเบียนวิชาชีพ' WHERE LG_CODE = '011503' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ทะเบียนวิชาชีพ' WHERE LG_CODE = '511503' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		//$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'บันทึกข้อมูล' WHERE LG_CODE = '011801' ";
		//$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'พิมพ์ดีด' WHERE LG_CODE = '011901' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานธุรการ' WHERE LG_CODE = '511612' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานพัสดุ' WHERE LG_CODE = '511712' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการพัสดุ' WHERE LG_CODE = '511723' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'บริหารงานพัสดุ' WHERE LG_CODE = '011734' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการสถิติ' WHERE LG_CODE in ('012003','512003') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'สถิติ' WHERE LG_CODE = '512223' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานสถิติ' WHERE LG_CODE = '512212' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานเวชสถิติ' WHERE LG_CODE = '512302' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'นิติการ' WHERE LG_CODE in ('012403','512403') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'นิติการ (กฎหมายมหาชน)' WHERE LG_CODE = '012414' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'นิติการ (กฎหมายกฤษฎีกา)' WHERE LG_CODE = '012427' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'งานร้องทุกข์' WHERE LG_CODE = '012503' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'สืบสวนสอบสวน' WHERE LG_CODE in ('012603','512603') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการทัณฑวิทยา' WHERE LG_CODE in ('012703','512703') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานราชทัณฑ์' WHERE LG_CODE = '512812' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการยุติธรรม' WHERE LG_CODE in ('012833','512833') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'บริหารงานยุติธรรม' WHERE LG_CODE = '012846' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'การทูต' WHERE LG_CODE in ('012903','512903') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิเทศสัมพันธ์' WHERE LG_CODE in ('013003','513003') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิเทศสหการ' WHERE LG_CODE in ('013103','513103') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิเคราะห์โครงการวิจัย' WHERE LG_CODE = '013203' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'คุมประพฤติ' WHERE LG_CODE in ('013303','513303') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'กฎหมายกฤษฎีกา' WHERE LG_CODE = '513405' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'พัฒนาระบบราชการ' WHERE LG_CODE = '513503' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการคลัง' WHERE LG_CODE = '520103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิเคราะห์รัฐวิสาหกิจ' WHERE LG_CODE = '520104' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานคลัง' WHERE LG_CODE = '520212' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการบัญชี' WHERE LG_CODE in ('020303','520303') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานการเงินและบัญชี' WHERE LG_CODE = '520412' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการเงินและบัญชี' WHERE LG_CODE in ('020423','520423') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'บริหารงานการเงินและบัญชี' WHERE LG_CODE = '020435' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ดูเงิน' WHERE LG_CODE = '020502' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานดูเงิน' WHERE LG_CODE = '520502' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ตรวจสอบภายใน' WHERE LG_CODE = '020603' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการตรวจสอบภายใน' WHERE LG_CODE = '520603' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ตรวจเงินแผ่นดิน' WHERE LG_CODE = '020701' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานตรวจสอบบัญชี' WHERE LG_CODE = '520812' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการตรวจสอบบัญชี' WHERE LG_CODE = '520823' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิเคราะห์งบประมาณ' WHERE LG_CODE in ('020903','520903') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการภาษี' WHERE LG_CODE in ('021003','521003') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิเคราะห์ภาษี' WHERE LG_CODE = '021103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ประเมินอากร' WHERE LG_CODE = '021203' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการศุลกากร' WHERE LG_CODE = '521203' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานศุลกากร' WHERE LG_CODE = '521301' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ช่วยควบคุมและจัดเก็บภาษีสรรพสามิต' WHERE LG_CODE = '021401' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานสรรพสามิต' WHERE LG_CODE = '521412' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการสรรพสามิต' WHERE LG_CODE in ('021423','521423') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานสรรพากร' WHERE LG_CODE = '521512' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการสรรพากร' WHERE LG_CODE in ('021523','521523') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'การสรรพากรอำเภอ' WHERE LG_CODE in ('021603','521603') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ตรวจสอบภาษี' WHERE LG_CODE = '521703' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'นายตรวจการสรรพสามิต' WHERE LG_CODE = '021801' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'บริหารงานที่ราชพัสดุ' WHERE LG_CODE = '021915' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการเศรษฐกิจ' WHERE LG_CODE in ('022003','522003') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ส่งเสริมการลงทุน' WHERE LG_CODE = '022103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการส่งเสริมการลงทุน' WHERE LG_CODE = '522103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการสหกรณ์' WHERE LG_CODE in ('022203','522203') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานส่งเสริมสหกรณ์' WHERE LG_CODE = '522312' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานการพาณิชย์' WHERE LG_CODE = '522412' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการสหกรณ์' WHERE LG_CODE in ('022203','522203') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการพาณิชย์' WHERE LG_CODE in ('022423','522423') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการตรอบสอบสิทธิบัตร' WHERE LG_CODE in ('022513','522513') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานชั่งตวงวัด' WHERE LG_CODE = '522712' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการชั่งตวงวัด' WHERE LG_CODE in ('022723','522723') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการประกันภัย' WHERE LG_CODE = '022803' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ตรวจกิจการประกันภัย' WHERE LG_CODE = '023003' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการอุตสาหกรรม' WHERE LG_CODE in ('023203','523203') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ส่งเสริมอุตสาหกรรม' WHERE LG_CODE = '023302' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานส่งเสริมอุตสาหกรรม' WHERE LG_CODE = '523302' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'บริหารงานการอุตสาหกรรม' WHERE LG_CODE = '023415' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการผลิตภัณฑ์อาหาร' WHERE LG_CODE in ('023503','523503') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานทรัพยากรธรณี' WHERE LG_CODE = '523612' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการทรัพยากรธรณี' WHERE LG_CODE in ('023623','523623') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการตรวจเงินแผ่นดิน' WHERE LG_CODE = '023703' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการมาตรฐาน' WHERE LG_CODE in ('023903','523903') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการขนส่ง' WHERE LG_CODE in ('030103','530103') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานขนส่ง' WHERE LG_CODE = '530212' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'นำร่อง' WHERE LG_CODE in ('030306','530306') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ตรวจท่า' WHERE LG_CODE in ('030403','530403') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'เดินเรือระหว่างประเทศ' WHERE LG_CODE = '030503' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'เดินเรือ' WHERE LG_CODE = '530503' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'เดินเรือในประเทศ' WHERE LG_CODE = '030601' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานเดินเรือ' WHERE LG_CODE = '530601' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ถือท้ายเรือ' WHERE LG_CODE = '030701' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'เครื่องหมายเดินเรือ' WHERE LG_CODE = '030803' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ควบคุมจราจรทางอากาศ' WHERE LG_CODE = '030901' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'การบิน' WHERE LG_CODE = '031001' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานสื่อสาร' WHERE LG_CODE = '531212' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'การโทรคมนาคม' WHERE LG_CODE = '031301' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'การไปรษณีย์โทรเลข' WHERE LG_CODE = '031401' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ส่งไปรษณีย์โทรเลข' WHERE LG_CODE = '031501' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ตรวจการสื่อสาร' WHERE LG_CODE = '031604' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการประชาสัมพันธ์' WHERE LG_CODE = '031703' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานเผยแพร่ประชาสัมพันธ์' WHERE LG_CODE = '531801' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ประชาสัมพันธ์' WHERE LG_CODE = '531813' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานการข่าว' WHERE LG_CODE = '531912' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'การข่าว' WHERE LG_CODE = '531923' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'บริหารงานข่าว' WHERE LG_CODE = '031935' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'รายงานข่าว' WHERE LG_CODE = '032011' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'สื่อข่าว' WHERE LG_CODE = '032003' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'สื่อสารมวลชน' WHERE LG_CODE = '532003' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'เรียบเรียงเอกสาร' WHERE LG_CODE = '032103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'จัดรายการวิทยุโทรทัศน์' WHERE LG_CODE = '032203' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ประกาศและรายงานข่าว' WHERE LG_CODE = '532011' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการเผยแพร่' WHERE LG_CODE = '532423' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานโสตทัศนศึกษา' WHERE LG_CODE = '532512' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการโสตทัศนศึกษา' WHERE LG_CODE = '532523' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานสื่อสารการบิน' WHERE LG_CODE = '532601' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการเกษตร' WHERE LG_CODE in ('040103','540103') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'สำรวจดิน' WHERE LG_CODE in ('040203','540203') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานช่างชลประทาน' WHERE LG_CODE = '540312' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิศวกรรมชลประทาน' WHERE LG_CODE in ('040323','540323') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานการเกษตร' WHERE LG_CODE = '540412' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการป่าไม้' WHERE LG_CODE in ('040503','540503') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานป่าไม้' WHERE LG_CODE = '540612' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการประมง' WHERE LG_CODE in ('040703','540703') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการประมงทะเล' WHERE LG_CODE = '040803' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานประมง' WHERE LG_CODE = '540912' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการสัตวบาล' WHERE LG_CODE in ('041003','541003') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานสัตวบาล' WHERE LG_CODE = '541112' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'การปศุสัตว์ทั่วไป' WHERE LG_CODE = '041201' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานเคหกิจเกษตร' WHERE LG_CODE = '541412' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการส่งเสริมการเกษตร' WHERE LG_CODE in ('041443','541443') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'จัดการปฏิรูปที่ดินเพื่อเกษตรกรรม' WHERE LG_CODE = '041503' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการปฏิรูปที่ดิน' WHERE LG_CODE = '541503' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิทยาศาสตร์' WHERE LG_CODE in ('050103','550103') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ช่วยนักวิทยาศาสตร์' WHERE LG_CODE = '050201' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานวิทยาศาสตร์' WHERE LG_CODE = '550212' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'นิติวิทยาศาสตร์' WHERE LG_CODE in ('050223','550223') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'นิวเคลียร์ฟิสิกส์' WHERE LG_CODE in ('050303','550303') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ฟิสิกส์รังสี' WHERE LG_CODE in ('050403','550403') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'นิวเคลียร์เคมี' WHERE LG_CODE in ('050503','550503') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'การผลิตไอโซโทป' WHERE LG_CODE = '050603' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการพิษวิทยา' WHERE LG_CODE = '050703' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ชีววิทยารังสี' WHERE LG_CODE in ('050803','550803') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการโรคพืช' WHERE LG_CODE in ('050903','550903') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'สัตววิทยา' WHERE LG_CODE in ('051003','551003') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'กีฎวิทยา' WHERE LG_CODE in ('051103','551103') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'กีฏวิทยารังสี' WHERE LG_CODE = '051203' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการธรณีวิทยา' WHERE LG_CODE = '051303' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ธรณีวิทยา' WHERE LG_CODE = '551303' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการอุทกวิทยา' WHERE LG_CODE in ('051403','551403') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานอุทกวิทยา' WHERE LG_CODE = '551512' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'อุตุนิยมวิทยา' WHERE LG_CODE = '551603' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานอุตุนิยมวิทยา' WHERE LG_CODE = '551712' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิทยาศาสตร์นิวเคลียร์' WHERE LG_CODE in ('051803','551803') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'แพทย์' WHERE LG_CODE in ('060104','560104') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'แพทย์แผนไทย' WHERE LG_CODE = '560105' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการสัตวแพทย์' WHERE LG_CODE = '060304' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการวิทยาศาสตร์การแพทย์' WHERE LG_CODE = '060403' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิทยาศาสตร์การแพทย์' WHERE LG_CODE = '560403' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'เภสัชวิจัย' WHERE LG_CODE = '060503' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'เภสัชกรรม' WHERE LG_CODE in ('060603','560603') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการอาหารและยา' WHERE LG_CODE in ('060703','560703') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'โภชนาการ' WHERE LG_CODE = '060802' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานโภชนาการ' WHERE LG_CODE = '560802' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการโภชนาการ' WHERE LG_CODE = '060813' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'โภชนาการ' WHERE LG_CODE = '560813' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'โภชนวิทยา' WHERE LG_CODE = '060823' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิเคราะห์อาหาร' WHERE LG_CODE = '060903' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิเคราะห์ยา' WHERE LG_CODE = '061003' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ตรวจอาหารและยา' WHERE LG_CODE = '061103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'จิตวิทยา' WHERE LG_CODE in ('061203','561203') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'จิตวิทยาคลินิค' WHERE LG_CODE = '561204' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'จัดการสุขศึกษา' WHERE LG_CODE = '061303' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการสุขาภิบาล' WHERE LG_CODE = '061403' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'การพยาบาลเทคนิค' WHERE LG_CODE = '061502' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'พยาบาลเทคนิค' WHERE LG_CODE = '561502' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการพยาบาล' WHERE LG_CODE in ('061514','561514') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'การพยาบาลวิชาชีพ' WHERE LG_CODE = '061523' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'พยาบาลวิชาชีพ' WHERE LG_CODE = '561523' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'เอ็กซเรย์' WHERE LG_CODE = '061701' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'รังสีการแพทย์' WHERE LG_CODE = '061712' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานรังสีการแพทย์' WHERE LG_CODE = '561712' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการรังสีการแพทย์' WHERE LG_CODE = '061723' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'รังสีการแพทย์' WHERE LG_CODE = '561723' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'กายภาพบำบัด' WHERE LG_CODE in ('061803','561803') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'อาชีวบำบัด' WHERE LG_CODE = '061902' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานอาชีวบำบัด' WHERE LG_CODE = '561902' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการอาชีวบำบัด' WHERE LG_CODE in ('061913','561913') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'กิจกรรมบำบัด' WHERE LG_CODE = '561914' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'เวชศาสตร์การสื่อความหมาย' WHERE LG_CODE = '561915' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานวิทยาศาสตร์การแพทย์' WHERE LG_CODE = '562212' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานเภสัชกรรม' WHERE LG_CODE = '562312' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'บริการเวชภัณฑ์' WHERE LG_CODE = '062403' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการสาธารณสุข' WHERE LG_CODE in ('062503','562503') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ช่วยทันตแพทย์' WHERE LG_CODE = '062601' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ทันตสาธารณสุข' WHERE LG_CODE = '062802' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานทันตสาธารณสุข' WHERE LG_CODE = '562802' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานเวชกรรมฟื้นฟู' WHERE LG_CODE = '562712' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการส่งเสริมสุขภาพ' WHERE LG_CODE = '063023' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการควบคุมโรค' WHERE LG_CODE = '063123' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ผดุงครรภ์สาธารณสุข' WHERE LG_CODE = '063401' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานสาธารณสุข' WHERE LG_CODE = '563502' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'เทคนิคการแพทย์' WHERE LG_CODE = '563603' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิศวกรรม' WHERE LG_CODE in ('070103','570103') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิศวกรรมโยธา' WHERE LG_CODE in ('070203','570203') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิศวกรรมผังเมือง' WHERE LG_CODE = '070303' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิศวกรรมเครื่องกล' WHERE LG_CODE in ('070403','570403') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิศวกรรมไฟฟ้า' WHERE LG_CODE in ('070503','570503') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิศวกรรมเหมืองแร่' WHERE LG_CODE in ('070603','570603') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิศวกรรมปิโตรเลียม' WHERE LG_CODE in ('070703','570703') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการพลังงาน' WHERE LG_CODE = '570704' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิศวกรรมโลหการ' WHERE LG_CODE in ('070803','570803') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิศวกรรมการเกษตร' WHERE LG_CODE in ('070903','570903') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิศวกรรมนิวเคลียร์' WHERE LG_CODE in ('071003','571003') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิศวกรรมโรงงาน' WHERE LG_CODE = '071103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'บริหารงานออกแบบก่อสร้าง' WHERE LG_CODE = '071205' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการกษาปณ์' WHERE LG_CODE in ('071303','571303') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'บริหารงานกษาปณ์' WHERE LG_CODE = '071316' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานช่างโยธา' WHERE LG_CODE = '571412' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานช่างรังวัด' WHERE LG_CODE = '571512' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิศวกรรมรังวัด' WHERE LG_CODE in ('071523','571523') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานช่างสำรวจ' WHERE LG_CODE = '571612' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิศวกรรมสำรวจ' WHERE LG_CODE in ('071623','571623') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานช่างขุดลอก' WHERE LG_CODE = '571812' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานช่างเครื่องกล' WHERE LG_CODE = '571912' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานช่างอากาศยาน' WHERE LG_CODE = '572112' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ช่างกลเรือ' WHERE LG_CODE in ('072203','572203') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานช่างโลหะ' WHERE LG_CODE = '572412' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ตรวจสภาพอากาศยาน' WHERE LG_CODE = '072503' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ตรวจสอบความปลอดภัยด้านการบิน' WHERE LG_CODE = '572503' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานช่างตรวจสภาพรถ' WHERE LG_CODE = '572612' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ตรวจเรือ' WHERE LG_CODE in ('072703','572703') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ตรวจโรงงาน' WHERE LG_CODE = '072802' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานตรวจโรงงาน' WHERE LG_CODE = '572802' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'คุมเครื่องคอมพิวเตอร์' WHERE LG_CODE = '072901' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานเครื่องคอมพิวเตอร์' WHERE LG_CODE = '572901' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานช่างไฟฟ้า' WHERE LG_CODE = '573012' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิศวกรรมไฟฟ้าสื่อสาร' WHERE LG_CODE in ('073123','573123') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'รักษาเสาสาย' WHERE LG_CODE = '073401' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานช่างเทคนิค' WHERE LG_CODE = '573512' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานช่างเหมืองแร่' WHERE LG_CODE = '573712' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'สถาปัตยกรรม' WHERE LG_CODE in ('073803','573803') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ภูมิสถาปัตยกรรม' WHERE LG_CODE in ('073813','573813') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'สถาปัตยกรรมผังเมือง' WHERE LG_CODE = '073903' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'มัณฑนศิลป์' WHERE LG_CODE in ('074003','574003') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานช่างออกแบบเรือ' WHERE LG_CODE = '574112' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานออกแบบผลิตภัณฑ์' WHERE LG_CODE = '574202' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการออกแบบผลิตภัณฑ์' WHERE LG_CODE = '574213' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานช่างเขียนแบบ' WHERE LG_CODE = '574312' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานช่างศิลป์' WHERE LG_CODE = '574412' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการช่างศิลป์' WHERE LG_CODE in ('074423','574423') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานช่างศิลปกรรม' WHERE LG_CODE = '574512' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ลิขิต' WHERE LG_CODE = '074601' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานลิขิต' WHERE LG_CODE = '574601' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานช่างหล่อ' WHERE LG_CODE = '574701' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานช่างพิมพ์' WHERE LG_CODE = '574802' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานช่างภาพ' WHERE LG_CODE = '574912' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ทำแผนที่ภาพถ่าย' WHERE LG_CODE = '075103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการแผนที่ภาพถ่าย' WHERE LG_CODE = '575103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'จิตรกรรม' WHERE LG_CODE in ('075303','575303') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ประติมากรรม' WHERE LG_CODE in ('075403','575403') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานกายอุปกรณ์' WHERE LG_CODE = '575602' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'กายอุปกรณ์' WHERE LG_CODE = '575612' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานช่างทันตกรรม' WHERE LG_CODE = '575702' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการศึกษา' WHERE LG_CODE in ('080103','580103') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ดูแลจัดการศึกษา' WHERE LG_CODE = '080303' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการศึกษาพิเศษ' WHERE LG_CODE in ('080513','580513') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ฝึกอบรม' WHERE LG_CODE = '080603' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'พัฒนาทรัพยากรบุคคล' WHERE LG_CODE = '080613' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานอบรมและฝึกวิชาชีพ' WHERE LG_CODE = '580712' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการอบรมและฝึกวิชาชีพ' WHERE LG_CODE = '580723' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานพัฒนาฝีมือแรงงาน' WHERE LG_CODE = '580812' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการพัฒนาฝีมือแรงงาน' WHERE LG_CODE = '580823' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ส่งเสริมและสอนการพลศึกษา' WHERE LG_CODE = '080903' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'พัฒนาการกีฬา' WHERE LG_CODE in ('080913','580913') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'พัฒนาการท่องเที่ยว' WHERE LG_CODE in ('080923','580923') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'อนุศาสน์' WHERE LG_CODE in ('081001','581001') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'อักษรศาสตร์' WHERE LG_CODE in ('081103','581103') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'จดหมายเหตุ' WHERE LG_CODE in ('081203','581203') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ห้องสมุด' WHERE LG_CODE = '081501' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานห้องสมุด' WHERE LG_CODE = '581501' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'โบราณคดี' WHERE LG_CODE in ('081603','581603') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'บริหารงานโบราณคดี' WHERE LG_CODE = '081615' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ภาษาโบราณ' WHERE LG_CODE in ('081703','581703') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วรรณกรรมและภาษาศาสตร์' WHERE LG_CODE = '081803' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วรรณศิลป์' WHERE LG_CODE = '581803' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ดูแลพิพิธภัณฑ์' WHERE LG_CODE = '082001' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานพิพิธภัณฑ์' WHERE LG_CODE = '582001' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'นาฏศิลป์' WHERE LG_CODE in ('082101','582101') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ดุริยางคศิลป์' WHERE LG_CODE in ('082201','582201') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'คีตศิลป์' WHERE LG_CODE in ('082301','582301') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการละครและดนตรี' WHERE LG_CODE in ('082403','582403') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานวัฒนธรรม' WHERE LG_CODE = '582612' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการวัฒนธรรม' WHERE LG_CODE in ('082623','582623') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานการศาสนา' WHERE LG_CODE = '582712' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการศาสนา' WHERE LG_CODE in ('082723','582723') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิจัยสังคมสงเคราะห์' WHERE LG_CODE = '082803' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'สังคมสงเคราะห์' WHERE LG_CODE in ('082903','582903') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'การพัฒนาเยาวชน' WHERE LG_CODE = '083103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการแรงงาน' WHERE LG_CODE in ('083203','583203') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานแรงงาน' WHERE LG_CODE = '583312' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิเคราะห์ผังเมือง' WHERE LG_CODE in ('083403','583403') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'การผังเมือง' WHERE LG_CODE in ('083503','583503') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'บริหารงานผังเมือง' WHERE LG_CODE = '083514' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการพัฒนาชุมชน' WHERE LG_CODE in ('083603','583603') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานพัฒนาชุมชน' WHERE LG_CODE = '583712' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการสิ่งแวดล้อม' WHERE LG_CODE in ('084003','584003') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการที่ดิน' WHERE LG_CODE = '584123' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'จัดหาที่ดิน' WHERE LG_CODE = '084203' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'วิชาการจัดหาที่ดิน' WHERE LG_CODE = '584203' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ช่วยนักจดหมายเหตุ' WHERE LG_CODE = '084301' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานช่วยนักจดหมายเหตุ' WHERE LG_CODE = '584301' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ประเมินราคาทรัพย์สิน' WHERE LG_CODE in ('084403','584403') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานประเมินราคาทรัพย์สิน' WHERE LG_CODE = '584401' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ปฏิบัติงานพัฒนาสังคม' WHERE LG_CODE = '584555' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'พัฒนาสังคม' WHERE LG_CODE = '584578' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LINE_GROUP (LG_CODE, LG_NAME, LG_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('510309', 'อำนวยการเฉพาะด้าน', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('551109', 'ผู้อำนวยการเฉพาะด้าน (กีฏวิทยา)', 'ผู้อำนวยการเฉพาะด้าน (กีฏวิทยา)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '551109' WHERE PL_CODE = '051103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('582309', 'ผู้อำนวยการเฉพาะด้าน (คีตศิลป์)', 'ผู้อำนวยการเฉพาะด้าน (คีตศิลป์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '582309' WHERE PL_CODE = '082301' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('581209', 'ผู้อำนวยการเฉพาะด้าน (จดหมายเหตุ)', 'ผู้อำนวยการเฉพาะด้าน (จดหมายเหตุ)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '581209' WHERE PL_CODE = '081203' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('575309', 'ผู้อำนวยการเฉพาะด้าน (จิตรกรรม)', 'ผู้อำนวยการเฉพาะด้าน (จิตรกรรม)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '575309' WHERE PL_CODE = '075303' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('582209', 'ผู้อำนวยการเฉพาะด้าน (ดุริยางคศิลป์)', 'ผู้อำนวยการเฉพาะด้าน (ดุริยางคศิลป์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '582209' WHERE PL_CODE = '082201' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('572709', 'ผู้อำนวยการเฉพาะด้าน (ตรวจเรือ)', 'ผู้อำนวยการเฉพาะด้าน (ตรวจเรือ)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '572709' WHERE PL_CODE = '072703' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('521709', 'ผู้อำนวยการเฉพาะด้าน (ตรวจสอบภาษี)', 'ผู้อำนวยการเฉพาะด้าน (ตรวจสอบภาษี)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '521709' WHERE PL_CODE = '021703' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('560209', 'ผู้อำนวยการเฉพาะด้าน (ทันตแพทย์)', 'ผู้อำนวยการเฉพาะด้าน (ทันตแพทย์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '560209' WHERE PL_CODE = '060204' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('582109', 'ผู้อำนวยการเฉพาะด้าน (นาฏศิลป์)', 'ผู้อำนวยการเฉพาะด้าน (นาฏศิลป์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '582109' WHERE PL_CODE = '082101' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('530309', 'ผู้อำนวยการเฉพาะด้าน (นำร่อง)', 'ผู้อำนวยการเฉพาะด้าน (นำร่อง)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '530309' WHERE PL_CODE = '030306' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('512409', 'ผู้อำนวยการเฉพาะด้าน (นิติการ)', 'ผู้อำนวยการเฉพาะด้าน (นิติการ)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '512409' WHERE PL_CODE = '012403' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('550229', 'ผู้อำนวยการเฉพาะด้าน (นิติวิทยาศาสตร์)', 'ผู้อำนวยการเฉพาะด้าน (นิติวิทยาศาสตร์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '550229' WHERE PL_CODE = '050223' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('581309', 'ผู้อำนวยการเฉพาะด้าน (บรรณารักษ์)', 'ผู้อำนวยการเฉพาะด้าน (บรรณารักษ์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '581309' WHERE PL_CODE = '081303' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('581619', 'ผู้อำนวยการเฉพาะด้าน (โบราณคดี)', 'ผู้อำนวยการเฉพาะด้าน (โบราณคดี)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '581619' WHERE PL_CODE = '081615' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('540319', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างชลประทาน)', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างชลประทาน)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '540319' WHERE PL_CODE = '040312' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('573519', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างเทคนิค)', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างเทคนิค)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '573519' WHERE PL_CODE in ('073512','073612') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('571419', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างโยธา)', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างโยธา)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '571419' WHERE PL_CODE = '071412' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('571519', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างรังวัด)', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างรังวัด)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '571519' WHERE PL_CODE = '071512' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('574519', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างศิลปกรรม)', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานช่างศิลปกรรม)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '574519' WHERE PL_CODE = '074512' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('575409', 'ผู้อำนวยการเฉพาะด้าน (ประติมากรรม)', 'ผู้อำนวยการเฉพาะด้าน (ประติมากรรม)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '575409' WHERE PL_CODE = '075403' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('584409', 'ผู้อำนวยการเฉพาะด้าน (ประเมินราคาทรัพย์สิน)', 'ผู้อำนวยการเฉพาะด้าน (ประเมินราคาทรัพย์สิน)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '584409' WHERE PL_CODE = '084403' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('512629', 'ผู้อำนวยการเฉพาะด้าน (พนักงานสอบสวนคดีพิเศษ)', 'ผู้อำนวยการเฉพาะด้าน (พนักงานสอบสวนคดีพิเศษ)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '512629' WHERE PL_CODE = '012626' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('560109', 'ผู้อำนวยการเฉพาะด้าน (แพทย์)', 'ผู้อำนวยการเฉพาะด้าน (แพทย์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '560109' WHERE PL_CODE = '060104' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('550409', 'ผู้อำนวยการเฉพาะด้าน (ฟิสิกส์รังสี)', 'ผู้อำนวยการเฉพาะด้าน (ฟิสิกส์รังสี)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '550409' WHERE PL_CODE = '050403' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('581909', 'ผู้อำนวยการเฉพาะด้าน (ภัณฑารักษ์)', 'ผู้อำนวยการเฉพาะด้าน (ภัณฑารักษ์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '581909' WHERE PL_CODE = '081903' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('560609', 'ผู้อำนวยการเฉพาะด้าน (เภสัชกรรม)', 'ผู้อำนวยการเฉพาะด้าน (เภสัชกรรม)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '560609' WHERE PL_CODE = '060603' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('574009', 'ผู้อำนวยการเฉพาะด้าน (มัณฑนศิลป์)', 'ผู้อำนวยการเฉพาะด้าน (มัณฑนศิลป์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '574009' WHERE PL_CODE = '074003' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('571319', 'ผู้อำนวยการเฉพาะด้าน (วิชาการกษาปณ์)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการกษาปณ์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '571319' WHERE PL_CODE = '071316' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('584209', 'ผู้อำนวยการเฉพาะด้าน (วิชาการจัดหาที่ดิน)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการจัดหาที่ดิน)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '584209' WHERE PL_CODE = '084203' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('522729', 'ผู้อำนวยการเฉพาะด้าน (วิชาการชั่งตวงวัด)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการชั่งตวงวัด)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '522729' WHERE PL_CODE = '022723' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('574429', 'ผู้อำนวยการเฉพาะด้าน (วิชาการช่างศิลป์)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการช่างศิลป์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '574429' WHERE PL_CODE = '074423' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('520609', 'ผู้อำนวยการเฉพาะด้าน (วิชาการตรวจสอบภายใน)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการตรวจสอบภายใน)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '520609' WHERE PL_CODE = '020603' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('511029', 'ผู้อำนวยการเฉพาะด้าน (วิชาการเทคโนโลยีสารสนเทศ)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการเทคโนโลยีสารสนเทศ)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('551309', 'ผู้อำนวยการเฉพาะด้าน (ธรณีวิทยา)', 'ผู้อำนวยการเฉพาะด้าน (ธรณีวิทยา)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '551309' WHERE PL_CODE = '051303' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('520309', 'ผู้อำนวยการเฉพาะด้าน (วิชาการบัญชี)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการบัญชี)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '520309' WHERE PL_CODE = '020303' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('523509', 'ผู้อำนวยการเฉพาะด้าน (วิชาการผลิตภัณฑ์อาหาร)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการผลิตภัณฑ์อาหาร)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '523509' WHERE PL_CODE = '023503' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('575109', 'ผู้อำนวยการเฉพาะด้าน (วิชาการแผนที่ภาพถ่าย)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการแผนที่ภาพถ่าย)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '575109' WHERE PL_CODE = '075103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('561519', 'ผู้อำนวยการเฉพาะด้าน (วิชาการพยาบาล)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการพยาบาล)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '561519' WHERE PL_CODE = '061514' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('521009', 'ผู้อำนวยการเฉพาะด้าน (วิชาการภาษี)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการภาษี)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '521009' WHERE PL_CODE = '021003' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('523909', 'ผู้อำนวยการเฉพาะด้าน (วิชาการมาตรฐาน)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการมาตรฐาน)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '523909' WHERE PL_CODE = '023903' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('550909', 'ผู้อำนวยการเฉพาะด้าน (วิชาการโรคพืช)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการโรคพืช)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '550909' WHERE PL_CODE = '050903' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('582409', 'ผู้อำนวยการเฉพาะด้าน (วิชาการละครและดนตรี)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการละครและดนตรี)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '582409' WHERE PL_CODE = '082403' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('522009', 'ผู้อำนวยการเฉพาะด้าน (วิชาการเศรษฐกิจ)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการเศรษฐกิจ)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '522009' WHERE PL_CODE = '022003' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('541449', 'ผู้อำนวยการเฉพาะด้าน (วิชาการส่งเสริมการเกษตร)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการส่งเสริมการเกษตร)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '541449' WHERE PL_CODE = '041443' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('512009', 'ผู้อำนวยการเฉพาะด้าน (วิชาการสถิติ)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการสถิติ)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '512009' WHERE PL_CODE = '012003' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('560709', 'ผู้อำนวยการเฉพาะด้าน (วิชาการอาหารและยา)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการอาหารและยา)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '560709' WHERE PL_CODE = '060703' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('551409', 'ผู้อำนวยการเฉพาะด้าน (วิชาการอุทกวิทยา)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการอุทกวิทยา)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '551409' WHERE PL_CODE = '051403' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('511019', 'ผู้อำนวยการเฉพาะด้าน (วิชาการคอมพิวเตอร์)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการคอมพิวเตอร์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '511019' WHERE PL_CODE = '011013' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('550109', 'ผู้อำนวยการเฉพาะด้าน (วิทยาศาสตร์)', 'ผู้อำนวยการเฉพาะด้าน (วิทยาศาสตร์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '550109' WHERE PL_CODE = '050103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('560409', 'ผู้อำนวยการเฉพาะด้าน (วิทยาศาสตร์การแพทย์)', 'ผู้อำนวยการเฉพาะด้าน (วิทยาศาสตร์การแพทย์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '560409' WHERE PL_CODE = '060403' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('551809', 'ผู้อำนวยการเฉพาะด้าน (วิทยาศาสตร์นิวเคลียร์)', 'ผู้อำนวยการเฉพาะด้าน (วิทยาศาสตร์นิวเคลียร์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '551809' WHERE PL_CODE = '051803' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('570109', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรม)', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรม)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '570109' WHERE PL_CODE = '070103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('570909', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมการเกษตร)', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมการเกษตร)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '570909' WHERE PL_CODE = '070903' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('570409', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมเครื่องกล)', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมเครื่องกล)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '570409' WHERE PL_CODE = '070403' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('540329', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมชลประทาน)', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมชลประทาน)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '540329' WHERE PL_CODE = '040323' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('571009', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมนิวเคลียร์)', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมนิวเคลียร์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '571009' WHERE PL_CODE = '071003' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('570709', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมปิโตรเลียม)', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมปิโตรเลียม)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '570709' WHERE PL_CODE = '070703' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('570509', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมไฟฟ้า)', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมไฟฟ้า)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '570509' WHERE PL_CODE = '070503' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('570209', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมโยธา)', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมโยธา)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '570209' WHERE PL_CODE = '070203' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('571529', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมรังวัด)', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมรังวัด)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '571529' WHERE PL_CODE = '071523' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('571629', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมสำรวจ)', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมสำรวจ)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '571629' WHERE PL_CODE = '071623' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('570609', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมเหมืองแร่)', 'ผู้อำนวยการเฉพาะด้าน (วิศวกรรมเหมืองแร่)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '570609' WHERE PL_CODE = '070603' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('573809', 'ผู้อำนวยการเฉพาะด้าน (สถาปัตยกรรม)', 'ผู้อำนวยการเฉพาะด้าน (สถาปัตยกรรม)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '573809' WHERE PL_CODE = '073803' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('551009', 'ผู้อำนวยการเฉพาะด้าน (สัตววิทยา)', 'ผู้อำนวยการเฉพาะด้าน (สัตววิทยา)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '551009' WHERE PL_CODE = '051003' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('540209', 'ผู้อำนวยการเฉพาะด้าน (สำรวจดิน)', 'ผู้อำนวยการเฉพาะด้าน (สำรวจดิน)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '540209' WHERE PL_CODE = '040203' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('581109', 'ผู้อำนวยการเฉพาะด้าน (อักษรศาสตร์)', 'ผู้อำนวยการเฉพาะด้าน (อักษรศาสตร์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '581109' WHERE PL_CODE = '081103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('551609', 'ผู้อำนวยการเฉพาะด้าน (อุตุนิยมวิทยา)', 'ผู้อำนวยการเฉพาะด้าน (อุตุนิยมวิทยา)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '551609' WHERE PL_CODE = '051603' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('520429', 'ผู้อำนวยการเฉพาะด้าน (วิชาการเงินและบัญชี)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการเงินและบัญชี)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '520429' WHERE PL_CODE = '020423' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('540109', 'ผู้อำนวยการเฉพาะด้าน (วิชาการเกษตร)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการเกษตร)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '540109' WHERE PL_CODE = '040103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('520109', 'ผู้อำนวยการเฉพาะด้าน (วิชาการคลัง)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการคลัง)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '520109' WHERE PL_CODE = '020103' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('520829', 'ผู้อำนวยการเฉพาะด้าน (วิชาการตรวจสอบบัญชี)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการตรวจสอบบัญชี)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '520829' WHERE PL_CODE = '020823' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('560309', 'ผู้อำนวยการเฉพาะด้าน (นายสัตวแพทย์)', 'ผู้อำนวยการเฉพาะด้าน (นายสัตวแพทย์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '560309' WHERE PL_CODE = '060304' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('562109', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานสัตวแพทย์)', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานสัตวแพทย์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '562109' WHERE PL_CODE = '062102' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('540509', 'ผู้อำนวยการเฉพาะด้าน (วิชาการป่าไม้)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการป่าไม้)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '540509' WHERE PL_CODE = '040503' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('522209', 'ผู้อำนวยการเฉพาะด้าน (วิชาการสหกรณ์)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการสหกรณ์)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '522209' WHERE PL_CODE = '022203' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('540709', 'ผู้อำนวยการเฉพาะด้าน (วิชาการประมง)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการประมง)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '540709' WHERE PL_CODE = '040703' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('540919', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานประมง)', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานประมง)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '540919' WHERE PL_CODE = '040912' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('541009', 'ผู้อำนวยการเฉพาะด้าน (วิชาการสัตวบาล)', 'ผู้อำนวยการเฉพาะด้าน (วิชาการสัตวบาล)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '541009' WHERE PL_CODE = '041003' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, PL_TYPE, LG_CODE)
						  VALUES ('541119', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานสัตวบาล)', 'ผู้อำนวยการเฉพาะด้าน (ปฏิบัติงานสัตวบาล)', 1, $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '541119' WHERE PL_CODE = '041112' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_LINE SET LG_CODE = trim(PL_CODE) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); 

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '01', LEVEL_NO_MAX = '03' WHERE CL_NAME IN ('1-3') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '01', LEVEL_NO_MAX = '04' WHERE CL_NAME IN ('1-3/4') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '01', LEVEL_NO_MAX = '05' WHERE CL_NAME IN ('1-3/4/5') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '01', LEVEL_NO_MAX = '06' WHERE CL_NAME IN ('1-3/4/5/6') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '02', LEVEL_NO_MAX = '04' WHERE CL_NAME IN ('2-4') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '02', LEVEL_NO_MAX = '05' WHERE CL_NAME IN ('2-4/5') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '02', LEVEL_NO_MAX = '06' WHERE CL_NAME IN ('2-4/5/6') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '03', LEVEL_NO_MAX = '05' WHERE CL_NAME IN ('3-5') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '03', LEVEL_NO_MAX = '06' WHERE CL_NAME IN ('3-5/6','3-5/6ว') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '03', LEVEL_NO_MAX = '07' WHERE CL_NAME IN 
		('3-5/6/7','3-5/6ว/7','3-5/6/7ว','3-5/6ว/7ว','3-5/6ว/7วช') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '03', LEVEL_NO_MAX = '08' WHERE CL_NAME IN ('3-5/6ว/7ว/8ว','3-5/6ว/7วช/8วช') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '04', LEVEL_NO_MAX = '04' WHERE CL_NAME IN ('4') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '04', LEVEL_NO_MAX = '05' WHERE CL_NAME IN ('4/5') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '04', LEVEL_NO_MAX = '06' WHERE CL_NAME IN ('4-6') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '04', LEVEL_NO_MAX = '07' WHERE CL_NAME IN ('4-6/7') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '04', LEVEL_NO_MAX = '08' WHERE CL_NAME IN ('4-6/7/8','4-5/6ว/7ว/8ว','4-6/7ว/8ว','4-6/7ว/8วช','4-6/7วช/8วช') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '05', LEVEL_NO_MAX = '05' WHERE CL_NAME IN ('5') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '05', LEVEL_NO_MAX = '06' WHERE CL_NAME IN ('5/6') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '05', LEVEL_NO_MAX = '07' WHERE CL_NAME IN ('5/6/7') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '05', LEVEL_NO_MAX = '08' WHERE CL_NAME IN ('5/6/7/8') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '06', LEVEL_NO_MAX = '06' WHERE CL_NAME IN ('6','6ว') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '06', LEVEL_NO_MAX = '07' WHERE CL_NAME IN ('6/7','6/7ว','6ว/7ว','6ว/7วช') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '06', LEVEL_NO_MAX = '08' WHERE CL_NAME IN ('6/7/8') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '07', LEVEL_NO_MAX = '07' WHERE CL_NAME IN ('7','7ว','7วช') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '07', LEVEL_NO_MAX = '08' WHERE CL_NAME IN ('7/8','7/8ว','7ว/8ว','7วช/8วช') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '07', LEVEL_NO_MAX = '09' WHERE CL_NAME IN ('7/8/9') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '08', LEVEL_NO_MAX = '08' WHERE CL_NAME IN ('8','8บก','8ว','8วช') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '08', LEVEL_NO_MAX = '09' WHERE CL_NAME IN ('8/9','8/9ชช','8บก/9ชช','8วช/9วช') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '08', LEVEL_NO_MAX = '10' WHERE CL_NAME IN ('8/9/10') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '09', LEVEL_NO_MAX = '09' WHERE CL_NAME IN ('9','9ชช','9บส','9วช') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '09', LEVEL_NO_MAX = '10' WHERE CL_NAME IN ('9/10','9/10ชช','9ชช/10ชช','9บส/10ชช','9บส/10บส','9วช/10วช') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '09', LEVEL_NO_MAX = '11' WHERE CL_NAME IN ('9/10/11') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '10', LEVEL_NO_MAX = '10' WHERE CL_NAME IN ('10','10ชช','10บส','10วช') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '10', LEVEL_NO_MAX = '11' WHERE CL_NAME IN ('10/11') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '11', LEVEL_NO_MAX = '11' WHERE CL_NAME IN ('11','11ชช','11บส','11วช') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " ALTER TABLE PER_COMDTL DROP CONSTRAINT FK2_PER_COMDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		if($DPISDB=="odbc")
			$cmd = " ALTER TABLE PER_COMDTL DROP CONSTRAINT INXU1_PER_COMDTL ";
		elseif($DPISDB=="oci8")
			$cmd = " DROP INDEX INXU1_PER_COMDTL ";
		elseif($DPISDB=="mysql")
			$cmd = " ALTER TABLE PER_COMDTL DROP CONSTRAINT INXU1_PER_COMDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " UPDATE PER_TYPE SET PT_NAME = 'ว' WHERE PT_CODE = '12' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

	} // end if

	if( $command=='LAYER_NEW' ) {
		$cmd = " DELETE FROM PER_LAYER_NEW ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 1, 4630, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 1.5, 4740, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 2, 4850, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 2.5, 4970, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 3, 5080, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 3.5, 5180, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 4, 5310, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 4.5, 5410, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 5, 5530, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 5.5, 5630, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 6, 5760, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 6.5, 5860, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 7, 5970, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 7.5, 6090, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 8, 6210, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 8.5, 6330, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 9, 6460, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 9.5, 6590, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 10, 6710, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 10.5, 6860, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 11, 6980, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 11.5, 7120, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 12, 7270, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 12.5, 7410, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 13, 7570, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 13.5, 7710, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 14, 7860, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 14.5, 8020, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '01', 15, 8180, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 1, 5530, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 1.5, 5680, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 2, 5840, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 2.5, 6000, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 3, 6160, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 3.5, 6310, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 4, 6470, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 4.5, 6630, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 5, 6800, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 5.5, 6940, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 6, 7100, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 6.5, 7260, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 7, 7420, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 7.5, 7580, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 8, 7730, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 8.5, 7890, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 9, 8040, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 9.5, 8200, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 10, 8380, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 10.5, 8540, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 11, 8710, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 11.5, 8880, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 12, 9060, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 12.5, 9240, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 13, 9430, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 13.5, 9590, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '02', 14, 9790, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 1, 6800, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 1.5, 6970, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 2, 7170, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 2.5, 7360, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 3, 7560, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 3.5, 7740, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 4, 7940, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 4.5, 8130, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 5, 8320, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 5.5, 8500, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 6, 8700, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 6.5, 8890, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 7, 9080, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 7.5, 9270, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 8, 9470, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 8.5, 9660, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 9, 9850, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 9.5, 10030, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 10, 10240, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 10.5, 10440, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 11, 10640, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 11.5, 10850, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 12, 11070, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 12.5, 11290, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 13, 11510, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 13.5, 11740, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 14, 11960, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 14.5, 12200, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 15, 12440, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 15.5, 12670, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 16, 12920, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 16.5, 13160, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 17, 13390, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 17.5, 13630, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 18, 13860, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 18.5, 14100, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 19, 14340, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 19.5, 14560, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '03', 20, 14800, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 1, 8320, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 1.5, 8540, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 2, 8770, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 2.5, 8990, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 3, 9230, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 3.5, 9480, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 4, 9700, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 4.5, 9940, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 5, 10190, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 5.5, 10420, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 6, 10660, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 6.5, 10900, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 7, 11140, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 7.5, 11390, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 8, 11630, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 8.5, 11870, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 9, 12120, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 9.5, 12350, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 10, 12600, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 10.5, 12850, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 11, 13100, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 11.5, 13360, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 12, 13620, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 12.5, 13870, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 13, 14140, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 13.5, 14410, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 14, 14700, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 14.5, 14970, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 15, 15260, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 15.5, 15560, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 16, 15850, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 16.5, 16150, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 17, 16440, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 17.5, 16730, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 18, 17020, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 18.5, 17320, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 19, 17600, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 19.5, 17890, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '04', 20, 18190, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 1, 10190, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 1.5, 10470, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 2, 10770, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 2.5, 11060, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 3, 11350, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 3.5, 11650, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 4, 11930, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 4.5, 12220, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 5, 12530, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 5.5, 12820, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 6, 13110, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 6.5, 13400, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 7, 13690, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 7.5, 13980, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 8, 14280, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 8.5, 14560, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 9, 14860, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 9.5, 15160, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 10, 15460, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 10.5, 15760, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 11, 16070, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 11.5, 16380, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 12, 16710, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 12.5, 17030, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 13, 17360, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 13.5, 17700, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 14, 18040, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 14.5, 18380, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 15, 18720, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 15.5, 19080, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 16, 19420, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 16.5, 19780, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 17, 20130, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 17.5, 20470, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 18, 20830, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 18.5, 21170, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 19, 21520, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 19.5, 21880, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '05', 20, 22220, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 1, 12530, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 1.5, 12880, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 2, 13240, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 2.5, 13610, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 3, 13960, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 3.5, 14330, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 4, 14690, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 4.5, 15040, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 5, 15410, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 5.5, 15780, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 6, 16110, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 6.5, 16480, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 7, 16840, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 7.5, 17200, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 8, 17560, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 8.5, 17910, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 9, 18280, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 9.5, 18640, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 10, 19010, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 10.5, 19390, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 11, 19790, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 11.5, 20180, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 12, 20590, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 12.5, 20990, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 13, 21410, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 13.5, 21820, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 14, 22250, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 14.5, 22680, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 15, 23110, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 15.5, 23550, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 16, 23990, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 16.5, 24430, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 17, 24870, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 17.5, 25310, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 18, 25740, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 18.5, 26180, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 19, 26620, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 19.5, 27070, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '06', 20, 27500, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 1, 15410, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 1.5, 15840, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 2, 16280, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 2.5, 16720, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 3, 17150, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 3.5, 17590, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 4, 18040, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 4.5, 18480, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 5, 18910, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 5.5, 19350, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 6, 19800, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 6.5, 20220, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 7, 20670, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 7.5, 21110, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 8, 21540, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 8.5, 21980, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 9, 22420, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 9.5, 22860, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 10, 23320, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 10.5, 23780, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 11, 24250, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 11.5, 24730, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 12, 25200, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 12.5, 25690, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 13, 26170, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 13.5, 26690, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 14, 27200, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 14.5, 27720, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 15, 28260, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 15.5, 28780, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 16, 29320, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 16.5, 29840, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 17, 30360, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 17.5, 30900, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 18, 31420, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 18.5, 31960, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 19, 32480, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 19.5, 33020, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '07', 20, 33540, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 1, 18910, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 1.5, 19440, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 2, 19990, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 2.5, 20520, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 3, 21080, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 3.5, 21610, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 4, 22160, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 4.5, 22690, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 5, 23230, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 5.5, 23770, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 6, 24310, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 6.5, 24850, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 7, 25390, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 7.5, 25930, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 8, 26470, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 8.5, 27000, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 9, 27550, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 9.5, 28100, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 10, 28660, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 10.5, 29220, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 11, 29800, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 11.5, 30380, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 12, 30960, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 12.5, 31560, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 13, 32160, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 13.5, 32790, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 14, 33410, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 14.5, 34050, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 15, 34710, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 15.5, 35360, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 16, 36020, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 16.5, 36660, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 17, 37320, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 17.5, 37980, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 18, 38620, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 18.5, 39280, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 19, 39930, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 19.5, 40590, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 20, 41230, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 20.5, 42020, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 21, 42790, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 21.5, 43570, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 22, 44340, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 22.5, 45120, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 23, 45900, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 23.5, 46670, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '08', 24, 47450, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 1, 23230, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 1.5, 23880, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 2, 24540, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 2.5, 25200, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 3, 25860, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 3.5, 26520, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 4, 27200, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 4.5, 27880, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 5, 28550, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 5.5, 29230, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 6, 29900, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 6.5, 30580, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 7, 31280, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 7.5, 31950, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 8, 32630, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 8.5, 33310, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 9, 33990, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 9.5, 34670, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 10, 35350, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 10.5, 36070, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 11, 36780, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 11.5, 37480, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 12, 38190, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 12.5, 38940, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 13, 39680, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 13.5, 40460, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 14, 41240, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 14.5, 42020, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 15, 42790, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 15.5, 43570, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 16, 44340, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 16.5, 45120, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 17, 45900, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 17.5, 46670, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 18, 47450, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 18.5, 48220, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 19, 49000, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 19.5, 49770, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '09', 20, 50550, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 1, 28550, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 1.5, 29350, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 2, 30140, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 2.5, 30960, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 3, 31770, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 3.5, 32590, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 4, 33410, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 4.5, 34230, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 5, 35060, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 5.5, 35900, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 6, 36730, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 6.5, 37560, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 7, 38390, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 7.5, 39220, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 8, 40060, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 8.5, 40900, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 9, 41720, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 9.5, 42550, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 10, 43380, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 10.5, 44250, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 11, 45130, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 11.5, 45990, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 12, 46870, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 12.5, 47780, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 13, 48700, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 13.5, 49630, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 14, 50560, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 14.5, 51470, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 15, 52390, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 15.5, 53330, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 16, 54240, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 16.5, 55170, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 17, 56080, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 17.5, 57010, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 18, 57930, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 18.5, 58850, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '10', 19, 59770, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 1, 33410, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 1.5, 34350, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 2, 35290, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 2.5, 36240, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 3, 37220, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 3.5, 38180, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 4, 39180, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 4.5, 40180, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 5, 41190, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 5.5, 42220, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 6, 43240, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 6.5, 44280, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 7, 45310, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 7.5, 46350, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 8, 47390, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 8.5, 48440, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 9, 49480, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 9.5, 50530, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 10, 51590, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 10.5, 52630, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 11, 53690, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 11.5, 54740, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 12, 55800, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 12.5, 56860, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 13, 57940, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 13.5, 59000, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 14, 60060, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 14.5, 61140, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 15, 62200, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 15.5, 63270, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (1, '11', 16, 64340, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (2, '11', 1, 47390, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (2, '11', 1.5, 48440, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (2, '11', 2, 49480, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (2, '11', 2.5, 50530, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (2, '11', 3, 51590, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (2, '11', 3.5, 52630, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (2, '11', 4, 53690, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (2, '11', 4.5, 54740, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (2, '11', 5, 55800, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (2, '11', 5.5, 56860, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (2, '11', 6, 57940, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (2, '11', 6.5, 59000, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (2, '11', 7, 60060, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (2, '11', 7.5, 61140, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (2, '11', 8, 62200, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (2, '11', 8.5, 63270, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (2, '11', 9, 64340, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (2, '11', 9.5, 65410, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES (2, '11', 10, 66480, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " DELETE FROM PER_LAYEREMP_NEW ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 1, 4630, 201.30, 28.80, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 1.5, 4740, 206.10, 29.45, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 2, 4850, 210.90, 30.15, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 2.5, 4970, 216.10, 30.90, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 3, 5080, 220.90, 31.60, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 3.5, 5180, 225.25, 32.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 4, 5310, 230.90, 33.00, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 4.5, 5410, 235.25, 33.65, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 5, 5530, 240.45, 34.35, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 5.5, 5680, 247.00, 35.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 6, 5840, 253.95, 36.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 6.5, 6000, 260.90, 37.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 7, 6160, 267.85, 38.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 7.5, 6310, 274.35, 39.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 8, 6470, 281.30, 40.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 8.5, 6630, 288.30, 41.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 9, 6800, 295.65, 42.25, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 9.5, 6940, 301.75, 43.15, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 10, 7100, 308.70, 44.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 10.5, 7260, 315.65, 45.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 11, 7420, 322.65, 46.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 11.5, 7580, 329.60, 47.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 12, 7730, 336.10, 48.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 12.5, 7890, 343.05, 49.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 13, 8040, 349.60, 49.95, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 13.5, 8200, 356.55, 50.95, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 14, 8380, 364.35, 52.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 14.5, 8540, 371.30, 53.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 15, 8710, 378.70, 54.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 15.5, 8880, 386.10, 55.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 16, 9060, 393.95, 56.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 16.5, 9240, 401.75, 57.40, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 17, 9430, 410.00, 58.60, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 17.5, 9590, 417.00, 59.60, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 18, 9790, 425.65, 60.85, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 18.5, 10030, 436.10, 62.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 19, 10240, 445.25, 63.65, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 19.5, 10440, 453.95, 64.85, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 20, 10640, 462.65, 66.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 20.5, 10850, 471.75, 67.40, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 21, 11070, 481.30, 68.80, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 21.5, 11290, 490.90, 70.15, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 22, 11510, 500.45, 71.50, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 22.5, 11740, 510.45, 72.95, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 23, 11960, 520.00, 74.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 23.5, 12200, 530.45, 75.80, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('10', 24, 12440, 540.90, 77.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 1, 4630, 201.30, 28.80, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 1.5, 4740, 206.10, 29.45, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 2, 4850, 210.90, 30.15, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 2.5, 4970, 216.10, 30.90, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 3, 5080, 220.90, 31.60, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 3.5, 5180, 225.25, 32.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 4, 5310, 230.90, 33.00, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 4.5, 5410, 235.25, 33.65, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 5, 5530, 240.45, 34.35, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 5.5, 5680, 247.00, 35.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 6, 5840, 253.95, 36.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 6.5, 6000, 260.90, 37.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 7, 6160, 267.85, 38.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 7.5, 6310, 274.35, 39.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 8, 6470, 281.30, 40.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 8.5, 6630, 288.30, 41.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 9, 6800, 295.65, 42.25, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 9.5, 6940, 301.75, 43.15, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 10, 7100, 308.70, 44.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 10.5, 7260, 315.65, 45.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 11, 7420, 322.65, 46.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 11.5, 7580, 329.60, 47.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 12, 7730, 336.10, 48.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 12.5, 7890, 343.05, 49.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 13, 8040, 349.60, 49.95, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 13.5, 8200, 356.55, 50.95, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 14, 8380, 364.35, 52.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 14.5, 8540, 371.30, 53.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 15, 8710, 378.70, 54.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 15.5, 8880, 386.10, 55.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 16, 9060, 393.95, 56.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 16.5, 9240, 401.75, 57.40, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 17, 9430, 410.00, 58.60, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 17.5, 9590, 417.00, 59.60, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 18, 9790, 425.65, 60.85, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 18.5, 10030, 436.10, 62.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 19, 10240, 445.25, 63.65, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 19.5, 10440, 453.95, 64.85, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 20, 10640, 462.65, 66.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 20.5, 10850, 471.75, 67.40, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 21, 11070, 481.30, 68.80, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 21.5, 11290, 490.90, 70.15, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 22, 11510, 500.45, 71.50, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 22.5, 11740, 510.45, 72.95, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 23, 11960, 520.00, 74.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 23.5, 12200, 530.45, 75.80, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 24, 12440, 540.90, 77.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 24.5, 12670, 550.90, 78.70, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 25, 13100, 569.60, 81.40, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 25.5, 13360, 580.90, 83.00, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 26, 13620, 592.20, 84.60, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 26.5, 13870, 603.05, 86.15, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('20', 27, 14140, 614.80, 87.85, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 1, 4630, 201.30, 28.80, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 1.5, 4740, 206.10, 29.45, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 2, 4850, 210.90, 30.15, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 2.5, 4970, 216.10, 30.90, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 3, 5080, 220.90, 31.60, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 3.5, 5180, 225.25, 32.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 4, 5310, 230.90, 33.00, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 4.5, 5410, 235.25, 33.65, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 5, 5530, 240.45, 34.35, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 5.5, 5680, 247.00, 35.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 6, 5840, 253.95, 36.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 6.5, 6000, 260.90, 37.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 7, 6160, 267.85, 38.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 7.5, 6310, 274.35, 39.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 8, 6470, 281.30, 40.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 8.5, 6630, 288.30, 41.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 9, 6800, 295.65, 42.25, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 9.5, 6940, 301.75, 43.15, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 10, 7100, 308.70, 44.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 10.5, 7260, 315.65, 45.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 11, 7420, 322.65, 46.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 11.5, 7580, 329.60, 47.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 12, 7730, 336.10, 48.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 12.5, 7890, 343.05, 49.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 13, 8040, 349.60, 49.95, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 13.5, 8200, 356.55, 50.95, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 14, 8380, 364.35, 52.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 14.5, 8540, 371.30, 53.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 15, 8710, 378.70, 54.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 15.5, 8880, 386.10, 55.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 16, 9060, 393.95, 56.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 16.5, 9240, 401.75, 57.40, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 17, 9430, 410.00, 58.60, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 17.5, 9590, 417.00, 59.60, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 18, 9790, 425.65, 60.85, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 18.5, 10030, 436.10, 62.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 19, 10240, 445.25, 63.65, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 19.5, 10440, 453.95, 64.85, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 20, 10640, 462.65, 66.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 20.5, 10850, 471.75, 67.40, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 21, 11070, 481.30, 68.80, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 21.5, 11290, 490.90, 70.15, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 22, 11510, 500.45, 71.50, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 22.5, 11740, 510.45, 72.95, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 23, 11960, 520.00, 74.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 23.5, 12200, 530.45, 75.80, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 24, 12440, 540.90, 77.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 24.5, 12670, 550.90, 78.70, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 25, 13100, 569.60, 81.40, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 25.5, 13360, 580.90, 83.00, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 26, 13620, 592.20, 84.60, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 26.5, 13870, 603.05, 86.15, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 27, 14140, 614.80, 87.85, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 27.5, 14410, 626.55, 89.55, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 28, 14700, 639.15, 91.35, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 28.5, 14970, 650.90, 93.00, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('30', 29, 15260, 663.50, 94.80, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 1, 4630, 201.30, 28.80, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 1.5, 4740, 206.10, 29.45, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 2, 4850, 210.90, 30.15, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 2.5, 4970, 216.10, 30.90, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 3, 5080, 220.90, 31.60, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 3.5, 5180, 225.25, 32.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 4, 5310, 230.90, 33.00, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 4.5, 5410, 235.25, 33.65, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 5, 5530, 240.45, 34.35, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 5.5, 5680, 247.00, 35.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 6, 5840, 253.95, 36.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 6.5, 6000, 260.90, 37.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 7, 6160, 267.85, 38.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 7.5, 6310, 274.35, 39.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 8, 6470, 281.30, 40.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 8.5, 6630, 288.30, 41.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 9, 6800, 295.65, 42.25, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 9.5, 6940, 301.75, 43.15, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 10, 7100, 308.70, 44.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 10.5, 7260, 315.65, 45.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 11, 7420, 322.65, 46.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 11.5, 7580, 329.60, 47.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 12, 7730, 336.10, 48.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 12.5, 7890, 343.05, 49.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 13, 8040, 349.60, 49.95, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 13.5, 8200, 356.55, 50.95, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 14, 8380, 364.35, 52.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 14.5, 8540, 371.30, 53.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 15, 8710, 378.70, 54.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 15.5, 8880, 386.10, 55.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 16, 9060, 393.95, 56.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 16.5, 9240, 401.75, 57.40, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 17, 9430, 410.00, 58.60, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 17.5, 9590, 417.00, 59.60, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 18, 9790, 425.65, 60.85, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 18.5, 10030, 436.10, 62.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 19, 10240, 445.25, 63.65, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 19.5, 10440, 453.95, 64.85, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 20, 10640, 462.65, 66.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 20.5, 10850, 471.75, 67.40, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 21, 11070, 481.30, 68.80, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 21.5, 11290, 490.90, 70.15, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 22, 11510, 500.45, 71.50, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 22.5, 11740, 510.45, 72.95, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 23, 11960, 520.00, 74.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 23.5, 12200, 530.45, 75.80, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 24, 12440, 540.90, 77.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 24.5, 12670, 550.90, 78.70, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 25, 13100, 569.60, 81.40, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 25.5, 13360, 580.90, 83.00, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 26, 13620, 592.20, 84.60, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 26.5, 13870, 603.05, 86.15, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 27, 14140, 614.80, 87.85, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 27.5, 14410, 626.55, 89.55, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 28, 14700, 639.15, 91.35, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 28.5, 14970, 650.90, 93.00, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 29, 15260, 663.50, 94.80, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 29.5, 15560, 676.55, 96.65, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 30, 15850, 689.15, 98.45, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 30.5, 16150, 702.20, 100.35, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 31, 16440, 714.80, 102.15, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 31.5, 16730, 727.40, 103.95, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 32, 17020, 740.00, 105.75, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 32.5, 17320, 753.05, 107.60, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 33, 17600, 765.25, 109.35, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 33.5, 17890, 777.85, 111.15, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('41', 34, 18190, 790.90, 113.00, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 1, 7170, 311.75, 44.55, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 1.5, 7360, 320.00, 45.75, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 2, 7560, 328.70, 47.00, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 2.5, 7740, 336.55, 48.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 3, 7940, 345.25, 49.35, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 3.5, 8130, 353.50, 50.50, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 4, 8320, 361.75, 51.70, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 4.5, 8540, 371.30, 53.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 5, 8770, 381.30, 54.50, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 5.5, 8990, 390.90, 55.85, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 6, 9230, 401.30, 57.35, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 6.5, 9480, 412.20, 58.90, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 7, 9700, 421.75, 60.25, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 7.5, 9940, 432.20, 61.75, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 8, 10190, 443.05, 63.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 8.5, 10470, 455.25, 65.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 9, 10770, 468.30, 66.90, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 9.5, 11060, 480.90, 68.70, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 10, 11350, 493.50, 70.50, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 10.5, 11650, 506.55, 72.40, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 11, 11930, 518.70, 74.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 11.5, 12220, 531.30, 75.90, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 12, 12530, 544.80, 77.85, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 12.5, 12820, 557.40, 79.65, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 13, 13110, 570.00, 81.45, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 13.5, 13400, 582.65, 83.25, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 14, 13690, 595.25, 85.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 14.5, 13980, 607.85, 86.85, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 15, 14280, 620.90, 88.70, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 15.5, 14560, 633.05, 90.45, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 16, 14860, 646.10, 92.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 16.5, 15160, 659.15, 94.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 17, 15460, 672.20, 96.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 17.5, 15760, 685.25, 97.90, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 18, 16070, 698.70, 99.85, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 18.5, 16380, 712.20, 101.75, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 19, 16710, 726.55, 103.80, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 19.5, 17030, 740.45, 105.80, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 20, 17360, 754.80, 107.85, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 20.5, 17700, 769.60, 109.95, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 21, 18040, 784.35, 112.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 21.5, 18380, 799.15, 114.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 22, 18720, 813.95, 116.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 22.5, 19080, 829.60, 118.55, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 23, 19420, 844.35, 120.65, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 23.5, 19780, 860.00, 122.90, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 24, 20130, 875.25, 125.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 24.5, 20470, 890.00, 127.15, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 25, 20830, 905.65, 129.40, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 25.5, 21170, 920.45, 131.50, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 26, 21520, 935.65, 133.70, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 26.5, 21880, 951.30, 135.90, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('42', 27, 22220, 966.10, 138.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 1, 8770, 381.30, 54.50, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 1.5, 8990, 390.90, 55.85, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 2, 9230, 401.30, 57.35, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 2.5, 9480, 412.20, 58.90, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 3, 9700, 421.75, 60.25, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 3.5, 9940, 432.20, 61.75, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 4, 10190, 443.05, 63.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 4.5, 10470, 455.25, 65.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 5, 10770, 468.30, 66.90, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 5.5, 11060, 480.90, 68.70, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 6, 11350, 493.50, 70.50, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 6.5, 11650, 506.55, 72.40, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 7, 11930, 518.70, 74.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 7.5, 12220, 531.30, 75.90, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 8, 12530, 544.80, 77.85, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 8.5, 12880, 560.00, 80.00, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 9, 13240, 575.65, 82.25, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 9.5, 13610, 591.75, 84.55, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 10, 13960, 607.00, 86.75, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 10.5, 14330, 623.05, 89.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 11, 14690, 638.70, 91.25, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 11.5, 15040, 653.95, 93.45, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 12, 15410, 670.00, 95.75, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 12.5, 15780, 686.10, 98.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 13, 16110, 700.45, 100.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 13.5, 16480, 716.55, 102.40, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 14, 16840, 732.20, 104.60, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 14.5, 17200, 747.85, 106.85, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 15, 17560, 763.50, 109.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 15.5, 17910, 778.70, 111.25, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 16, 18280, 794.80, 113.55, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 16.5, 18640, 810.45, 115.80, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 17, 19010, 826.55, 118.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 17.5, 19390, 843.05, 120.45, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 18, 19790, 860.45, 122.95, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 18.5, 20180, 877.40, 125.35, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 19, 20590, 895.25, 127.90, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 19.5, 20990, 912.65, 130.40, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 20, 21410, 930.90, 133.00, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 20.5, 21820, 948.70, 135.55, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 21, 22250, 967.40, 138.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 21.5, 22680, 986.10, 140.90, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 22, 23110, 1004.80, 143.55, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 22.5, 23550, 1023.95, 146.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 23, 23990, 1043.05, 149.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 23.5, 24730, 1075.25, 153.65, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 24, 25200, 1095.65, 156.55, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 24.5, 25690, 1117.00, 159.60, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 25, 26170, 1137.85, 162.55, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 25.5, 26690, 1160.45, 165.80, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 26, 27200, 1182.65, 168.95, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 26.5, 27720, 1205.25, 172.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 27, 28260, 1228.70, 175.55, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 27.5, 28780, 1251.30, 178.80, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 28, 29320, 1274.80, 182.15, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 28.5, 29840, 1297.40, 185.35, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 29, 30360, 1320.00, 188.60, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 29.5, 30900, 1343.50, 191.95, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 30, 31420, 1366.10, 195.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 30.5, 31960, 1389.60, 198.55, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 31, 32480, 1412.20, 201.75, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 31.5, 33020, 1435.65, 205.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('43', 32, 33540, 1458.30, 208.35, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 1, 24310, 1057.00, 151.00, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 1.5, 24850, 1080.45, 154.35, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 2, 25390, 1103.95, 157.75, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 2.5, 25930, 1127.40, 161.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 3, 26470, 1150.90, 164.45, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 3.5, 27000, 1173.95, 167.75, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 4, 27550, 1197.85, 171.15, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 4.5, 28100, 1221.75, 174.55, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 5, 28660, 1246.10, 178.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 5.5, 29220, 1270.45, 181.50, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 6, 29800, 1295.65, 185.10, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 6.5, 30380, 1320.90, 188.70, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 7, 30960, 1346.10, 192.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 7.5, 31560, 1372.20, 196.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 8, 32160, 1398.30, 199.80, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 8.5, 32790, 1425.65, 203.70, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 9, 33410, 1452.65, 207.55, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 9.5, 34050, 1480.45, 211.50, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 10, 34670, 1507.40, 215.35, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 10.5, 35350, 1537.00, 219.60, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 11, 36070, 1568.30, 224.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 11.5, 36780, 1599.15, 228.45, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 12, 37480, 1629.60, 232.80, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 12.5, 38190, 1660.45, 237.35, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 13, 38940, 1693.05, 241.90, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 13.5, 39680, 1725.25, 246.50, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 14, 40460, 1759.15, 251.35, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 14.5, 41720, 1813.95, 259.15, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 15, 42550, 1850.00, 264.30, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 15.5, 43380, 1886.10, 269.45, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 16, 44250, 1923.95, 274.85, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 16.5, 45130, 1962.20, 280.35, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 17, 45990, 1999.60, 285.70, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 17.5, 47390, 2060.45, 294.35, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 18, 48440, 2106.10, 300.90, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 18.5, 49480, 2151.30, 307.35, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 19, 50530, 2197.00, 313.90, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 19.5, 51590, 2243.05, 320.45, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 20, 52630, 2288.30, 326.90, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 20.5, 53690, 2334.35, 333.50, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 21, 54740, 2380.00, 340.00, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 21.5, 55800, 2426.10, 346.60, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 22, 56860, 2472.20, 353.20, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 22.5, 57940, 2519.15, 359.90, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 23, 59000, 2565.25, 366.50, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('44', 23.5, 60060, 2611.30, 373.05, 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " UPDATE PER_SALARYHIS SET SAH_ENDDATE = NULL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT SAH_ID, PER_ID, SAH_EFFECTIVEDATE, SAH_ENDDATE FROM PER_SALARYHIS ORDER BY PER_ID, SAH_EFFECTIVEDATE DESC, 
		SAH_SALARY DESC, SAH_DOCNO desc ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$SAH_ID = $data[SAH_ID];
			$PER_ID = $data[PER_ID];
			$SAH_EFFECTIVEDATE = trim($data[SAH_EFFECTIVEDATE]);
			$SAH_ENDDATE = trim($data[SAH_ENDDATE]);
			if ($PER_ID != $TEMP_ID) {
				$TEMP_ID = $PER_ID;
			} elseif (!$SAH_ENDDATE) {
				if ($TEMP_ENDDATE < $SAH_EFFECTIVEDATE)	$TEMP_ENDDATE = $SAH_EFFECTIVEDATE;
				$cmd = " UPDATE PER_SALARYHIS SET SAH_ENDDATE = '$TEMP_ENDDATE' WHERE SAH_ID = $SAH_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			} elseif ($SAH_ENDDATE) {
				if ($TEMP_ENDDATE < $SAH_EFFECTIVEDATE){
					echo "$TEMP_ENDDATE-$SAH_EFFECTIVEDATE<br>";
					$TEMP_ENDDATE = $SAH_EFFECTIVEDATE;
					$cmd = " UPDATE PER_SALARYHIS SET SAH_ENDDATE = '$TEMP_ENDDATE' WHERE SAH_ID = $SAH_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				}
			} // end if
			$tmp_date = explode("-", $SAH_EFFECTIVEDATE);
			$TEMP_ENDDATE = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2),		$tmp_date[0]) - 86400);
			$TEMP_ENDDATE = date("Y-m-d", $TEMP_ENDDATE);
		} // end while						
	} // end if

	if( $command=='E_HR' ) {
/*
		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV0 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, 
						  CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'TH', 41, 6, 'เครื่องบันทึกเวลา', 'S', 'N', 0, $CREATE_DATE,'$SESS_USERNAME', $CREATE_DATE,'$SESS_USERNAME') ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV0 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, 
						  CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
						  VALUES (1, 'EN', 41, 6, 'เครื่องบันทึกเวลา', 'S', 'N', 0, $CREATE_DATE,'$SESS_USERNAME', $CREATE_DATE,'$SESS_USERNAME') ";
		$db->send_cmd($cmd);
		//$db->show_error() ;

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'select_database_attendance.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV1 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 		
							  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 1, 'T01 เลือกประเภทฐานข้อมูลเครื่องบันทึกเวลา', 'S', 'W', 'select_database_attendance.html', 0, 41, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 1, 'T01 เลือกประเภทฐานข้อมูลเครื่องบันทึกเวลา', 'S', 'W', 'select_database_attendance.html', 0, 41, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table.html?table=PER_WORK_LOCATION' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV1 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 2, 'T02 สถานที่ปฏิบัติราชการ', 'S', 'W', 'master_table.html?table=PER_WORK_LOCATION', 0, 41, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 2, 'T02 สถานที่ปฏิบัติราชการ', 'S', 'W', 'master_table.html?table=PER_WORK_LOCATION', 0, 41, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_workcycle.html?table=PER_WORK_CYCLE' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV1 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 3, 'T03 รอบการมาปฏิบัติราชการ', 'S', 'W', 'master_table_workcycle.html?table=PER_WORK_CYCLE', 0, 41, 
							  $CREATE_DATE,'$SESS_USERNAME', $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 3, 'T03 รอบการมาปฏิบัติราชการ', 'S', 'W', 'master_table_workcycle.html?table=PER_WORK_CYCLE', 0, 41, 
							  $CREATE_DATE,'$SESS_USERNAME', $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table.html?table=PER_TIME_ATT' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV1 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 4, 'T04 เครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์', 'S', 'W', 'master_table.html?table=PER_TIME_ATT', 0, 41, 
							  $CREATE_DATE,'$SESS_USERNAME', $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 4, 'T04 เครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์', 'S', 'W', 'master_table.html?table=PER_TIME_ATT', 0, 41, 
							  $CREATE_DATE,'$SESS_USERNAME', $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_worklate.html?table=PER_WORK_LATE' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV1 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 5, 'T05 เวลาสาย', 'S', 'W', 'master_table_worklate.html?table=PER_WORK_LATE', 0, 41, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'T05 เวลาสาย', 'S', 'W', 'master_table_worklate.html?table=PER_WORK_LATE', 0, 41, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'process_per_work_late.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV1 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 6, 'T06 สร้างเวลาสายล่วงหน้า', 'S', 'W', 'process_per_work_late.html', 0, 41, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 6, 'T06 สร้างเวลาสายล่วงหน้า', 'S', 'W', 'process_per_work_late.html', 0, 41, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'process_per_time_attendance.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV1 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 7, 'T07 ถ่ายโอนข้อมูลจากเครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์', 'S', 'W', 'process_per_time_attendance.html', 0, 41, 
							  $CREATE_DATE,'$SESS_USERNAME', $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 7, 'T07 ถ่ายโอนข้อมูลจากเครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์', 'S', 'W', 'process_per_time_attendance.html', 0, 41, 
							  $CREATE_DATE,'$SESS_USERNAME', $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'data_time_attendance.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV1 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 8, 'T08 เวลาการมาปฏิบัติราชการจากเครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์', 'S', 'W', 'data_time_attendance.html', 0, 41, 
							  $CREATE_DATE,'$SESS_USERNAME', $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 8, 'T08 เวลาการมาปฏิบัติราชการจากเครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์', 'S', 'W', 'data_time_attendance.html', 0, 41, 
							  $CREATE_DATE,'$SESS_USERNAME', $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'data_time_attendance_system.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV1 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 9, 'T09 ถ่ายโอนข้อมูลบุคลากรไปสู่โปรแกรม HIP Time Attendance System', 'S', 'W', 'data_time_attendance_system.html', 0, 41, 
							  $CREATE_DATE,'$SESS_USERNAME', $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 9, 'T09 ถ่ายโอนข้อมูลบุคลากรไปสู่โปรแกรม HIP Time Attendance System', 'S', 'W', 'data_time_attendance_system.html', 0, 41, 
							  $CREATE_DATE,'$SESS_USERNAME', $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'process_time_attendance.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV1 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 10, 'T10 ยืนยันเวลาการมาปฏิบัติราชการ', 'S', 'W', 'process_time_attendance.html', 0, 41, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 10, 'T10 ยืนยันเวลาการมาปฏิบัติราชการ', 'S', 'W', 'process_time_attendance.html', 0, 41, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'personal_absent_work.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV1 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 11, 'T11 ตรวจสอบวันลาและวันที่มาปฏิบัติราชการ', 'S', 'W', 'personal_absent_work.html', 0, 41, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 11, 'T11 ตรวจสอบวันลาและวันที่มาปฏิบัติราชการ', 'S', 'W', 'personal_absent_work.html', 0, 41, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'process_per_work_time.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV1 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 11, 'T12 สร้างวันที่มาปฏิบัติราชการล่วงหน้า', 'S', 'W', 'process_per_work_time.html', 0, 41, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 11, 'T12 สร้างวันที่มาปฏิบัติราชการล่วงหน้า', 'S', 'W', 'process_per_work_time.html', 0, 41, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_map_per_type.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();
		$db->free_result();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_map_per_formula.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();
		$db->free_result();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'rpt_map_per_position.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();
		$db->free_result();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV0 WHERE MENU_LABEL = 'รายงานสรุป' ";
		$db->send_cmd($cmd);
		//$db->show_error();
		$db->free_result();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'map_per_type.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();
		$db->free_result();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'map_per_line.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();
		$db->free_result();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'map_per_formula.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();
		$db->free_result();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'map_per_co_level.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();
		$db->free_result();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'map_per_position.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();
		$db->free_result();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV0 WHERE MENU_LABEL = 'ตารางเทียบเคียง' ";
		$db->send_cmd($cmd);
		//$db->show_error();
		$db->free_result();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table.html?table=PER_GROUP_N' ";
		$db->send_cmd($cmd);
		//$db->show_error();
		$db->free_result();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_type_n.html?table=PER_TYPE_N' ";
		$db->send_cmd($cmd);
		//$db->show_error();
		$db->free_result();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_layer_n.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();
		$db->free_result();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'master_table_line_n.html' ";
		$db->send_cmd($cmd);
		//$db->show_error();
		$db->free_result();

		$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV0 WHERE MENU_LABEL = 'ประเภทตำแหน่ง/สายงาน (ใหม่)' ";
		$db->send_cmd($cmd);
		//$db->show_error();
		$db->free_result();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_WORK_LOCATION(
			WL_CODE VARCHAR(10) NOT NULL,	
			WL_NAME VARCHAR(100) NOT NULL,
			WL_ACTIVE SINGLE NOT NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_LOCATION PRIMARY KEY (wl_code)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_WORK_LOCATION(
			WL_CODE VARCHAR2(10) NOT NULL,	
			WL_NAME VARCHAR2(100) NOT NULL,
			WL_ACTIVE NUMBER(1) NOT NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_LOCATION PRIMARY KEY (wl_code)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_WORK_LOCATION(
			WL_CODE VARCHAR(10) NOT NULL,	
			WL_NAME VARCHAR(100) NOT NULL,
			WL_ACTIVE SMALLINT(1) NOT NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_LOCATION PRIMARY KEY (wl_code)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_WORK_LOCATION (WL_CODE, WL_NAME, WL_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('01', 'สำนักงานคณะกรรมการข้าราชการพลเรือน กรุงเทพฯ', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_WORK_LOCATION (WL_CODE, WL_NAME, WL_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('02', 'สำนักงานคณะกรรมการข้าราชการพลเรือน นนทบุรี', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_WORK_CYCLE(
			WC_CODE VARCHAR(4) NOT NULL,	
			WC_NAME VARCHAR(100) NOT NULL,
			WC_START VARCHAR(4) NOT NULL,	
			WC_END VARCHAR(4) NOT NULL,	
			WC_ACTIVE SINGLE NOT NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_CYCLE PRIMARY KEY (wc_code)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_WORK_CYCLE(
			WC_CODE VARCHAR2(4) NOT NULL,	
			WC_NAME VARCHAR2(100) NOT NULL,
			WC_START VARCHAR2(4) NOT NULL,	
			WC_END VARCHAR2(4) NOT NULL,	
			WC_ACTIVE NUMBER(1) NOT NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_CYCLE PRIMARY KEY (wc_code)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_WORK_CYCLE(
			WC_CODE VARCHAR(4) NOT NULL,	
			WC_NAME VARCHAR(100) NOT NULL,
			WC_START VARCHAR(4) NOT NULL,	
			WC_END VARCHAR(4) NOT NULL,	
			WC_ACTIVE SMALLINT(1) NOT NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_CYCLE PRIMARY KEY (wc_code)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_WORK_CYCLE (WC_CODE, WC_NAME, WC_START, WC_END, WC_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('01', 'รอบที่ 1 7:30 น.', '0730', '1530', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_WORK_CYCLE (WC_CODE, WC_NAME, WC_START, WC_END, WC_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('02', 'รอบที่ 2 8:30 น.', '0830', '1630', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_WORK_CYCLE (WC_CODE, WC_NAME, WC_START, WC_END, WC_ACTIVE, UPDATE_USER, UPDATE_DATE)
						  VALUES ('03', 'รอบที่ 3 9:30 น.', '0930', '1730', 0, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
*/	
		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R007004.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 4, 'R0704 รายงานการมาปฏิบัติราชการรายบุคคล', 'S', 'W', 'rpt_R007004.html', 0, 36, 239, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 4, 'R0704 รายงานการมาปฏิบัติราชการรายบุคคล', 'S', 'W', 'rpt_R007004.html', 0, 36, 239, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R007005.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 5, 'R0705 รายงานการมาปฏิบัติราชการ ประจำวัน', 'S', 'W', 'rpt_R007005.html', 0, 36, 239, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 5, 'R0705 รายงานการมาปฏิบัติราชการ ประจำวัน', 'S', 'W', 'rpt_R007005.html', 0, 36, 239, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R007006.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 6, 'R0706 รายงานการไม่บันทึกเวลาปฏิบัติราชการ', 'S', 'W', 'rpt_R007006.html', 0, 36, 239, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 6, 'R0706 รายงานการไม่บันทึกเวลาปฏิบัติราชการ', 'S', 'W', 'rpt_R007006.html', 0, 36, 239, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R007007.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 7, 'R0707 รายงานการลา สาย ขาดราชการ', 'S', 'W', 'rpt_R007007.html', 0, 36, 239, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 7, 'R0707 รายงานการลา สาย ขาดราชการ', 'S', 'W', 'rpt_R007007.html', 0, 36, 239, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R007008.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 8, 'R0708 รายงานการปฏิบัติราชการนอกสถานที่', 'S', 'W', 'rpt_R007008.html', 0, 36, 239, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 8, 'R0708 รายงานการปฏิบัติราชการนอกสถานที่', 'S', 'W', 'rpt_R007008.html', 0, 36, 239, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R007009.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 9, 'R0709 รายงานรอบการมาปฏิบัติราชการรายบุคคล', 'S', 'W', 'rpt_R007009.html', 0, 36, 239, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 9, 'R0709 รายงานรอบการมาปฏิบัติราชการรายบุคคล', 'S', 'W', 'rpt_R007009.html', 0, 36, 239, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();

		$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R007010.html' ";
		$count_data = $db->send_cmd($cmd);
		//$db->show_error();
		if (!$count_data) {
			$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
			$db->send_cmd($cmd);
			//$db->show_error();
			$data = $db->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'TH', $MAX_ID, 10, 'R0710 รายงานการมาสายจำแนกตามสำนัก/กอง', 'S', 'W', 'rpt_R007010.html', 0, 36, 239, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, 
							  LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
							  VALUES (1, 'EN', $MAX_ID, 10, 'R0710 รายงานการมาสายจำแนกตามสำนัก/กอง', 'S', 'W', 'rpt_R007010.html', 0, 36, 239, $CREATE_DATE,'$SESS_USERNAME', 
							  $CREATE_DATE,'$SESS_USERNAME') ";
			$db->send_cmd($cmd);
			//$db->show_error() ;
		}
		$db->free_result();
/*
		$cmd = " DROP TABLE PER_TIME_ATT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
*/
		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_TIME_ATT(
			TA_CODE VARCHAR(10) NOT NULL,	
			TA_NAME VARCHAR(100) NOT NULL,
			WL_CODE VARCHAR(10) NOT NULL,	
			TA_ACTIVE SINGLE NOT NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_TIME_ATT PRIMARY KEY (ta_code)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_TIME_ATT(
			TA_CODE VARCHAR2(10) NOT NULL,	
			TA_NAME VARCHAR2(100) NOT NULL,
			WL_CODE VARCHAR2(10) NOT NULL,	
			TA_ACTIVE NUMBER(1) NOT NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_TIME_ATT PRIMARY KEY (ta_code)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_TIME_ATT(
			TA_CODE VARCHAR(10) NOT NULL,	
			TA_NAME VARCHAR(100) NOT NULL,
			WL_CODE VARCHAR(10) NOT NULL,	
			TA_ACTIVE SMALLINT(1) NOT NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_TIME_ATT PRIMARY KEY (ta_code)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_TIME_ATT (TA_CODE, TA_NAME, WL_CODE, TA_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('1', 'เครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์ 1 (10.0.0.200)', '01', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_TIME_ATT (TA_CODE, TA_NAME, WL_CODE, TA_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('2', 'เครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์ 2 (10.0.0.201)', '01', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_TIME_ATT (TA_CODE, TA_NAME, WL_CODE, TA_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('3', 'เครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์ 3 (10.0.0.202)', '01', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_TIME_ATT (TA_CODE, TA_NAME, WL_CODE, TA_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('4', 'เครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์ 4 (10.0.0.203)', '01', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_TIME_ATT (TA_CODE, TA_NAME, WL_CODE, TA_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('5', 'เครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์ 5 (192.168.94.245)', '02', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_TIME_ATT (TA_CODE, TA_NAME, WL_CODE, TA_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('6', 'เครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์ 6 (192.168.94.246)', '02', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$cmd = " INSERT INTO PER_TIME_ATT (TA_CODE, TA_NAME, WL_CODE, TA_ACTIVE, UPDATE_USER, UPDATE_DATE)
		                  VALUES ('7', 'เครื่องบันทึกเวลาการปฏิบัติงานอิเล็กทรอนิกส์ 7 (192.168.20.100)', '02', 1, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
/*	
		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_WORK_LATE(
			WL_CODE VARCHAR(10) NOT NULL,	
			WC_CODE VARCHAR(4) NOT NULL,	
			WORK_DATE VARCHAR(19) NOT NULL,
			LATE_TIME VARCHAR(4) NOT NULL,	
			LATE_REMARK VARCHAR(100) NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_LATE PRIMARY KEY (wl_code, wc_code, work_date)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_WORK_LATE(
			WL_CODE VARCHAR2(10) NOT NULL,	
			WC_CODE VARCHAR2(4) NOT NULL,	
			WORK_DATE VARCHAR2(19) NOT NULL,
			LATE_TIME VARCHAR2(4) NOT NULL,	
			LATE_REMARK VARCHAR2(100) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_LATE PRIMARY KEY (wl_code, wc_code, work_date)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_WORK_LATE(
			WL_CODE VARCHAR(10) NOT NULL,	
			WC_CODE VARCHAR(4) NOT NULL,	
			WORK_DATE VARCHAR(19) NOT NULL,
			LATE_TIME VARCHAR(4) NOT NULL,	
			LATE_REMARK VARCHAR(100) NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_LATE PRIMARY KEY (wl_code, wc_code, work_date)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_WORK_CYCLEHIS(
			WH_ID INTEGER NOT NULL,
			PER_ID INTEGER NOT NULL,
			WC_CODE VARCHAR(4) NOT NULL,	
			START_DATE VARCHAR(19) NOT NULL,
			END_DATE VARCHAR(19) NULL,
			REMARK VARCHAR(100) NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_CYCLEHIS PRIMARY KEY (wh_id)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_WORK_CYCLEHIS(
			WH_ID NUMBER(10) NOT NULL,
			PER_ID NUMBER(10) NOT NULL,
			WC_CODE VARCHAR2(4) NOT NULL,	
			START_DATE VARCHAR2(19) NOT NULL,
			END_DATE VARCHAR2(19) NULL,
			REMARK VARCHAR2(100) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_CYCLEHIS PRIMARY KEY (wh_id)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_WORK_CYCLEHIS(
			WH_ID INTEGER(10) NOT NULL,
			PER_ID INTEGER(10) NOT NULL,
			WC_CODE VARCHAR(4) NOT NULL,	
			START_DATE VARCHAR(19) NOT NULL,
			END_DATE VARCHAR(19) NULL,
			REMARK VARCHAR(100) NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_CYCLEHIS PRIMARY KEY (wh_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_TIME_ATTENDANCE(
			PER_ID INTEGER NOT NULL,
			TIME_STAMP VARCHAR(19) NOT NULL,
			TA_CODE VARCHAR(10) NOT NULL,	
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_TIME_ATTENDANCE PRIMARY KEY (per_id, time_stamp)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_TIME_ATTENDANCE(
			PER_ID NUMBER(10) NOT NULL,
			TIME_STAMP VARCHAR2(19) NOT NULL,
			TA_CODE VARCHAR2(10) NOT NULL,	
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_TIME_ATTENDANCE PRIMARY KEY (per_id, time_stamp)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_TIME_ATTENDANCE(
			PER_ID INTEGER(10) NOT NULL,
			TIME_STAMP VARCHAR(19) NOT NULL,
			TA_CODE VARCHAR(10) NOT NULL,	
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_TIME_ATTENDANCE PRIMARY KEY (per_id, time_stamp)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_WORK_TIME(
			WT_ID INTEGER NOT NULL,
			PER_ID INTEGER NOT NULL,
			WL_CODE VARCHAR(10) NOT NULL,	
			WC_CODE VARCHAR(4) NOT NULL,	
			START_DATE VARCHAR(19) NOT NULL,
			END_DATE VARCHAR(19) NULL,
			HOLIDAY_FLAG SINGLE NULL,
			ABSENT_FLAG SINGLE NULL,
			REMARK VARCHAR(100) NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_TIME PRIMARY KEY (wt_id)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_WORK_TIME(
			WT_ID NUMBER(10) NOT NULL,
			PER_ID NUMBER(10) NOT NULL,
			WL_CODE VARCHAR2(10) NOT NULL,	
			WC_CODE VARCHAR2(4) NOT NULL,	
			START_DATE VARCHAR2(19) NOT NULL,
			END_DATE VARCHAR2(19) NULL,
			HOLIDAY_FLAG NUMBER(1) NULL,
			ABSENT_FLAG NUMBER(1) NULL,
			REMARK VARCHAR2(100) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_TIME PRIMARY KEY (wt_id)) ";
		elseif($DPISDB=="mysql")
			$cmd = " CREATE TABLE PER_WORK_TIME(
			WT_ID INTEGER(10) NOT NULL,
			PER_ID INTEGER(10) NOT NULL,
			WL_CODE VARCHAR(10) NOT NULL,	
			WC_CODE VARCHAR(4) NOT NULL,	
			START_DATE VARCHAR(19) NOT NULL,
			END_DATE VARCHAR(19) NULL,
			HOLIDAY_FLAG SMALLINT(1) NULL,
			ABSENT_FLAG SMALLINT(1) NULL,
			REMARK VARCHAR(100) NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_WORK_TIME PRIMARY KEY (wt_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if($DPISDB=="odbc") 
			$cmd = " CREATE TABLE PER_ABSENTSUM(
			AS_ID INTEGER NOT NULL,
			PER_ID INTEGER NOT NULL,
			AS_YEAR VARCHAR(4) NOT NULL,	
			AS_CYCLE SINGLE NULL,
			START_DATE VARCHAR(19) NOT NULL,
			END_DATE VARCHAR(19) NULL,
			AS_SICK_DAY INTEGER2 NULL,
			AS_SICK_NO INTEGER2 NULL,
			AS_BUSY_DAY INTEGER2 NULL,
			AS_BUSY_NO INTEGER2 NULL,
			AS_DEL_DAY INTEGER2 NULL,
			AS_DEL_NO INTEGER2 NULL,
			AS_LATE_DAY INTEGER2 NULL,
			AS_LATE_NO INTEGER2 NULL,
			AS_RELAX_DAY INTEGER2 NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_PER_ABSENTSUM PRIMARY KEY (as_id)) ";
		elseif($DPISDB=="oci8") 
			$cmd = " CREATE TABLE PER_ABSENTSUM(
			AS_ID NUMBER(10) NOT NULL,
			PER_ID NUMBER(10) NOT NULL,
			AS_YEAR VARCHAR2(4) NOT NULL,	
			AS_CYCLE NUMBER(1) NULL,
			START_DATE VARCHAR2(19) NOT NULL,
			END_DATE VARCHAR2(19) NULL,
			AS_SICK_DAY NUMBER(3) NULL,
			AS_SICK_NO NUMBER(3) NULL,
			AS_BUSY_DAY NUMBER(3) NULL,
			AS_BUSY_NO NUMBER(3) NULL,
			AS_DEL_DAY NUMBER(3) NULL,
			AS_DEL_NO NUMBER(3) NULL,
			AS_LATE_DAY NUMBER(3) NULL,
			AS_LATE_NO NUMBER(3) NULL,
			AS_RELAX_DAY NUMBER(3) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_PER_ABSENTSUM PRIMARY KEY (as_id)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
*/
	} // end if

?>