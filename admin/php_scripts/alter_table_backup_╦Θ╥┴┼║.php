<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_bmp.php");
	include("php_scripts/load_per_control.php");

	$IMG_PATH = "../attachment/pic_personal";	
	$success_pic = $error_pic = $total_pic = $found001 = 0;
	$err_text = "";

	if(!isset($SRC_DIR)) $SRC_DIR = $IMG_PATH;

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	if($db_type=="mysql") {
		$CREATE_DATE = "NOW()";
		$CREATE_BY = "'$SESS_USERNAME'";
	} elseif($db_type=="mssql") {
		$CREATE_DATE = "GETDATE()";
		$CREATE_BY = $SESS_USERID;
	} elseif($db_type=="oci8" || $db_type=="odbc") {
		$CREATE_DATE = date("Y-m-d H:i:s");
		$CREATE_DATE = "'$CREATE_DATE'";
		$CREATE_BY = $SESS_USERID;
	}
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$PER_ID_DEPARTMENT_ID = $DEPARTMENT_ID;
/*
	if($DPISDB=="odbc") 
		$cmd = " ALTER TABLE PER_CONTROL ADD CTRL_ALTER INTEGER NULL ";
	elseif($DPISDB=="oci8") 
		$cmd = " ALTER TABLE PER_CONTROL ADD CTRL_ALTER NUMBER(10) NULL ";
	elseif($DPISDB=="mysql")
		$cmd = " ALTER TABLE PER_CONTROL ADD CTRL_ALTER INTEGER(10) NULL ";
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
*/
	$cmd = " select CTRL_ALTER from PER_CONTROL ";
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$CTRL_ALTER = $data[CTRL_ALTER];
	if(!$CTRL_ALTER) $CTRL_ALTER = 0;

	if( $command=='ALTER' ) {
		if($CTRL_ALTER < 1) {
			$cmd = " SELECT PM_CODE FROM PER_MGT WHERE PM_NAME = '�������˹觷ҧ������/�Ԫҡ��' OR 
							  PM_NAME = '�������˹觺�����' OR PM_NAME = '-' OR PM_NAME = '���˹�����ʺ��ó� (�)' OR 
							  PM_NAME = '���˹��ԪҪվ (Ǫ.)' OR PM_NAME = '���˹觷���ջ��ʺ��ó� (�)' OR PM_NAME = '����Ǫҭ੾��(��.)' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PM_CODE = $data[PM_CODE];

				$cmd = " UPDATE PER_POSITION SET PM_CODE = NULL WHERE PM_CODE = '$PM_CODE' ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET PM_CODE = NULL WHERE PM_CODE = '$PM_CODE' ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			} // end while

			$cmd = " UPDATE PER_POSITION SET POS_SALARY = 0 WHERE POS_SALARY IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POSITION SET POS_STATUS = 2 WHERE POS_STATUS = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_EMP SET POEM_STATUS = 2 WHERE POEM_STATUS = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_EMPSER SET POEM_STATUS = 2 WHERE POEM_STATUS = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc"){
				$cmd = " CREATE TABLE PER_EXTRA_INCOME_TYPE (
				EXIN_CODE CHAR(10) NOT NULL,
				EXIN_NAME VARCHAR(100) NOT NULL,
				EXIN_ACTIVE SINGLE NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL, 
				UPDATE_DATE CHAR(19) NOT NULL) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_EXTRA_INCOME_TYPE ADD CONSTRAINT PK_PER_EXTRA_INCOME_TYPE  
								  PRIMARY KEY (EXIN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
	
				$cmd = " CREATE UNIQUE INDEX U1_PER_EXTRA_INCOME_TYPE ON PER_EXTRA_INCOME_TYPE (EXIN_NAME) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

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
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ADD CONSTRAINT PK_PER_EXTRA_INCOMEHIS 
								  PRIMARY KEY (EXINH_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " CREATE UNIQUE INDEX U1_PER_EXTRA_INCOMEHIS ON PER_EXTRA_INCOMEHIS 
								  (PER_ID, EXIN_CODE, EXINH_EFFECTIVEDATE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ADD CONSTRAINT FK_PER_EXTRA_INCOMEHIS 
								  FOREIGN KEY (EXIN_CODE) REFERENCES PER_EXTRA_INCOME_TYPE (EXIN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_ABSENT_CONF ADD PER_TYPE INTEGER NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_ABSENT_CONF SET PER_TYPE = 1 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_ABSENT_CONF ALTER PER_TYPE	INTEGER NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_ABSENT_CONF DROP CONSTRAINT PK_PER_ABSENT_CONF ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_ABSENT_CONF ADD CONSTRAINT PK_PER_ABSENT_CONF 
								  PRIMARY KEY (ABS_MONTH, PER_TYPE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_ABSENT_CONF (ABS_MONTH, UPDATE_USER, UPDATE_DATE, PER_TYPE) 
								  SELECT ABS_MONTH, UPDATE_USER, UPDATE_DATE, 2 FROM PER_ABSENT_CONF ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_ABSENT_CONF_B ADD PER_TYPE	INTEGER NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_ABSENT_CONF_B SET PER_TYPE = 1 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_ABSENT_CONF_B ALTER PER_TYPE INTEGER NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_ABSENT_CONF_B (ABS_MONTH, UPDATE_USER, UPDATE_DATE, PER_TYPE) 
								  SELECT ABS_MONTH, UPDATE_USER, UPDATE_DATE, 2 FROM PER_ABSENT_CONF_B ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2DTL ADD CRD_CODE CHAR(10) NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2DTL ADD PEN_CODE CHAR(2) NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2DTL ADD CONSTRAINT FK4_PER_INVEST2DTL 
								  FOREIGN KEY (PEN_CODE) REFERENCES PER_PENALTY (PEN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_INVEST2DTL, PER_INVEST2 SET PER_INVEST2DTL.CRD_CODE = PER_INVEST2.CRD_CODE 
								  WHERE PER_INVEST2.INV_ID = PER_INVEST2DTL.INV_ID ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_INVEST2DTL, PER_INVEST2 SET PER_INVEST2DTL.PEN_CODE = PER_INVEST2.PEN_CODE 
								  WHERE PER_INVEST2.INV_ID = PER_INVEST2DTL.INV_ID ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2DTL ALTER CRD_CODE CHAR(10) NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2DTL ADD CONSTRAINT FK3_PER_INVEST2DTL 
								  FOREIGN KEY (CRD_CODE) REFERENCES PER_CRIME_DTL (CRD_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2 DROP CONSTRAINT FK2_PER_INVEST2 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2 DROP COLUMN CRD_CODE ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2 DROP CONSTRAINT FK3_PER_INVEST2 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2 DROP COLUMN PEN_CODE ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2DTL_B ADD CRD_CODE CHAR(10) NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2DTL_B ADD PEN_CODE CHAR(2) NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_INVEST2DTL_B, PER_INVEST2_B 
								  SET PER_INVEST2DTL_B.CRD_CODE = PER_INVEST2_B.CRD_CODE 
								  WHERE PER_INVEST2_B.INV_ID = PER_INVEST2DTL_B.INV_ID ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_INVEST2DTL_B, PER_INVEST2_B 
								  SET PER_INVEST2DTL_B.PEN_CODE = PER_INVEST2_B.PEN_CODE 
								  WHERE PER_INVEST2_B.INV_ID = PER_INVEST2DTL_B.INV_ID ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2DTL_B ALTER CRD_CODE CHAR(10) NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2_B DROP COLUMN CRD_CODE ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2_B DROP COLUMN PEN_CODE ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

			}elseif($DPISDB=="oci8"){
				$cmd = " ALTER TABLE PER_ORG_TYPE MODIFY OT_NAME VARCHAR2(100) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " CREATE TABLE PER_EXTRA_INCOME_TYPE (
				EXIN_CODE CHAR(10) NOT NULL,
				EXIN_NAME VARCHAR2(100) NOT NULL,
				EXIN_ACTIVE NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL, 
				UPDATE_DATE CHAR(19) NOT NULL) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_EXTRA_INCOME_TYPE ADD CONSTRAINT PK_PER_EXTRA_INCOME_TYPE  
								  PRIMARY KEY (EXIN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " CREATE UNIQUE INDEX U1_PER_EXTRA_INCOME_TYPE ON PER_EXTRA_INCOME_TYPE (EXIN_NAME) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

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
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ADD CONSTRAINT PK_PER_EXTRA_INCOMEHIS 
								  PRIMARY KEY (EXINH_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " CREATE UNIQUE INDEX U1_PER_EXTRA_INCOMEHIS ON PER_EXTRA_INCOMEHIS 
								  (PER_ID, EXIN_CODE, EXINH_EFFECTIVEDATE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ADD CONSTRAINT FK1_PER_EXTRA_INCOMEHIS 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ADD CONSTRAINT FK2_PER_EXTRA_INCOMEHIS 
								  FOREIGN KEY (EXIN_CODE) REFERENCES PER_EXTRA_INCOME_TYPE (EXIN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_ABSENT_CONF ADD PER_TYPE NUMBER(1) NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_ABSENT_CONF SET PER_TYPE = 1 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_ABSENT_CONF MODIFY PER_TYPE NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_ABSENT_CONF DROP CONSTRAINT PK_PER_ABSENT_CONF ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_ABSENT_CONF ADD CONSTRAINT PK_PER_ABSENT_CONF PRIMARY KEY 
								  (ABS_MONTH, PER_TYPE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_ABSENT_CONF 
								  SELECT ABS_MONTH, UPDATE_USER, UPDATE_DATE, 2 FROM PER_ABSENT_CONF ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_ABSENT_CONF_B ADD PER_TYPE NUMBER(1) NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_ABSENT_CONF_B SET PER_TYPE = 1 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_ABSENT_CONF_B MODIFY PER_TYPE NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_ABSENT_CONF_B 
								  SELECT ABS_MONTH, UPDATE_USER, UPDATE_DATE, 2 FROM PER_ABSENT_CONF_B ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2DTL ADD CRD_CODE CHAR(10) NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2DTL ADD PEN_CODE CHAR(2) NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2DTL ADD CONSTRAINT FK3_PER_INVEST2DTL 
								  FOREIGN KEY (CRD_CODE) REFERENCES PER_CRIME_DTL (CRD_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2DTL ADD CONSTRAINT FK4_PER_INVEST2DTL 
								  FOREIGN KEY (PEN_CODE) REFERENCES PER_PENALTY(PEN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_INVEST2DTL SET CRD_CODE = 
								  (SELECT CRD_CODE FROM PER_INVEST2 WHERE PER_INVEST2.INV_ID = PER_INVEST2DTL.INV_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_INVEST2DTL SET PEN_CODE = 
								  (SELECT PEN_CODE FROM PER_INVEST2 WHERE PER_INVEST2.INV_ID = PER_INVEST2DTL.INV_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2DTL MODIFY CRD_CODE NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2 DROP COLUMN CRD_CODE ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2 DROP COLUMN PEN_CODE ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2DTL_B ADD CRD_CODE CHAR(10) NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2DTL_B ADD PEN_CODE CHAR(2) NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_INVEST2DTL_B SET CRD_CODE = (SELECT CRD_CODE FROM PER_INVEST2_B 
								  WHERE PER_INVEST2_B.INV_ID = PER_INVEST2DTL_B.INV_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_INVEST2DTL_B SET PEN_CODE = (SELECT PEN_CODE FROM PER_INVEST2_B 
								  WHERE PER_INVEST2_B.INV_ID = PER_INVEST2DTL_B.INV_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2DTL_B MODIFY CRD_CODE NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2_B DROP COLUMN CRD_CODE ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_INVEST2_B DROP COLUMN PEN_CODE ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

			}elseif($DPISDB=="mysql"){
					
			} // end if

			$cmd = " ALTER TABLE PER_COMDTL DROP CONSTRAINT FK2_PER_COMDTL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_COMDTL DROP CONSTRAINT FK6_PER_COMDTL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_MARRHIS DROP CONSTRAINT FK2_PER_MARRHIS ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") {
				$cmd = " ALTER TABLE PER_ABILITY ADD CONSTRAINT FK2_PER_ABILITY 
								  FOREIGN KEY (AL_CODE) REFERENCES PER_ABILITYGRP (AL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ABILITY ADD CONSTRAINT FK1_PER_ABILITY 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ABSENT ADD CONSTRAINT FK2_PER_ABSENT 
								  FOREIGN KEY (AB_CODE) REFERENCES PER_ABSENTTYPE (AB_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ABSENT ADD CONSTRAINT FK1_PER_ABSENT 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ABSENTHIS ADD CONSTRAINT FK2_PER_ABSENTHIS 
								  FOREIGN KEY (AB_CODE) REFERENCES PER_ABSENTTYPE (AB_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ABSENTHIS ADD CONSTRAINT FK1_PER_ABSENTHIS 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_AMPHUR ADD CONSTRAINT FK1_PER_AMPHUR 
								  FOREIGN KEY (PV_CODE) REFERENCES PER_PROVINCE (PV_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ASSIGN ADD CONSTRAINT FK1_PER_ASSIGN 
								  FOREIGN KEY (LEVEL_NO_MIN) REFERENCES PER_LEVEL (LEVEL_NO) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ASSIGN ADD CONSTRAINT FK2_PER_ASSIGN 
								  FOREIGN KEY (LEVEL_NO_MAX) REFERENCES PER_LEVEL (LEVEL_NO) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ASSIGN_DTL ADD CONSTRAINT FK1_PER_ASSIGN_DTL 
								  FOREIGN KEY (ASS_ID) REFERENCES PER_ASSIGN (ASS_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ASSIGN_DTL ADD CONSTRAINT FK2_PER_ASSIGN_DTL 
								  FOREIGN KEY (PL_CODE) REFERENCES PER_LINE(PL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ASSIGN_S ADD CONSTRAINT FK1_PER_ASSIGN_S 
								  FOREIGN KEY (LEVEL_NO) REFERENCES PER_LEVEL (LEVEL_NO) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ASSIGN_YEAR ADD CONSTRAINT FK1_PER_ASSIGN_YEAR 
								  FOREIGN KEY (EL_CODE) REFERENCES PER_EDUCLEVEL (EL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ASSIGN_YEAR ADD CONSTRAINT FK2_PER_ASSIGN_YEAR 
								  FOREIGN KEY (LEVEL_NO) REFERENCES PER_LEVEL (LEVEL_NO) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_BONUSPROMOTE ADD CONSTRAINT FK1_PER_BONUSPROMOTE FOREIGN KEY 
								  (BONUS_YEAR, BONUS_TYPE) REFERENCES PER_BONUSQUOTA (BONUS_YEAR, BONUS_TYPE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_BONUSPROMOTE ADD CONSTRAINT FK2_PER_BONUSPROMOTE FOREIGN KEY 
								  (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_BONUSQUOTADTL1 ADD CONSTRAINT FK1_PER_BONUSQUOTADTL1 FOREIGN KEY 
								  (BONUS_YEAR, BONUS_TYPE) REFERENCES PER_BONUSQUOTA (BONUS_YEAR, BONUS_TYPE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_BONUSQUOTADTL1 ADD CONSTRAINT FK2_PER_BONUSQUOTADTL1 
								  FOREIGN KEY (ORG_ID) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_BONUSQUOTADTL2 ADD CONSTRAINT FK1_PER_BONUSQUOTADTL2 FOREIGN KEY 
								  (BONUS_YEAR, BONUS_TYPE) REFERENCES PER_BONUSQUOTA (BONUS_YEAR, BONUS_TYPE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_BONUSQUOTADTL2 ADD CONSTRAINT FK2_PER_BONUSQUOTADTL2 
								  FOREIGN KEY (ORG_ID) REFERENCES PER_ORG_ASS (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_CO_LEVEL ADD CONSTRAINT FK1_PER_CO_LEVEL 
								  FOREIGN KEY (LEVEL_NO_MIN) REFERENCES PER_LEVEL (LEVEL_NO) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_CO_LEVEL ADD CONSTRAINT FK2_PER_CO_LEVEL 
								  FOREIGN KEY (LEVEL_NO_MAX) REFERENCES PER_LEVEL (LEVEL_NO) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK1_PER_COMDTL 
								  FOREIGN KEY (COM_ID) REFERENCES PER_COMMAND (COM_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK3_PER_COMDTL 
								  FOREIGN KEY (EN_CODE) REFERENCES PER_EDUCNAME (EN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK4_PER_COMDTL 
								  FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK5_PER_COMDTL 
								  FOREIGN KEY (PN_CODE) REFERENCES PER_POS_NAME (PN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK7_PER_COMDTL 
								  FOREIGN KEY (POEM_ID) REFERENCES PER_POS_EMP (POEM_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK8_PER_COMDTL 
								  FOREIGN KEY (LEVEL_NO) REFERENCES PER_LEVEL (LEVEL_NO) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK9_PER_COMDTL 
								  FOREIGN KEY (PL_CODE_ASSIGN) REFERENCES PER_LINE (PL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK10_PER_COMDTL 
								  FOREIGN KEY (PN_CODE_ASSIGN) REFERENCES PER_POS_NAME (PN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_COMDTL ADD CONSTRAINT FK11_PER_COMDTL 
								  FOREIGN KEY (MOV_CODE) REFERENCES PER_MOVMENT (MOV_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_COMMAND ADD CONSTRAINT FK1_PER_COMMAND 
								  FOREIGN KEY (COM_TYPE) REFERENCES PER_COMTYPE (COM_TYPE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_CONTROL ADD CONSTRAINT FK1_PER_CONTROL 
								  FOREIGN KEY (ORG_ID) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_COURSE ADD CONSTRAINT FK1_PER_COURSE 
								  FOREIGN KEY (TR_CODE) REFERENCES PER_TRAIN (TR_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_COURSE ADD CONSTRAINT FK2_PER_COURSE 
								  FOREIGN KEY (CT_CODE) REFERENCES PER_COUNTRY (CT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_COURSE ADD CONSTRAINT FK3_PER_COURSE 
								  FOREIGN KEY (CT_CODE_FUND) REFERENCES PER_COUNTRY (CT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_COURSEDTL ADD CONSTRAINT FK1_PER_COURSEDTL 
								  FOREIGN KEY (CO_ID) REFERENCES PER_COURSE (CO_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_COURSEDTL ADD CONSTRAINT FK2_PER_COURSEDTL 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_CRIME_DTL ADD CONSTRAINT FK1_PER_CRIME_DTL 
								  FOREIGN KEY (CR_CODE) REFERENCES PER_CRIME (CR_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_DECORATEHIS ADD CONSTRAINT FK1_PER_DECORATEHIS 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_DECORATEHIS ADD CONSTRAINT FK2_PER_DECORATEHIS FOREIGN KEY (DC_CODE) 
								  REFERENCES PER_DECORATION (DC_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_DECORCOND ADD CONSTRAINT FK1_PER_DECORCOND 
								  FOREIGN KEY (LEVEL_NO) REFERENCES PER_LEVEL (LEVEL_NO) ";
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

				$cmd = " ALTER TABLE PER_DECORDTL ADD CONSTRAINT FK1_PER_DECORDTL 
								  FOREIGN KEY (DE_ID) REFERENCES PER_DECOR (DE_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_DECORDTL ADD CONSTRAINT FK2_PER_DECORDTL 
								  FOREIGN KEY (DC_CODE) REFERENCES PER_DECORATION (DC_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_DECORDTL ADD CONSTRAINT FK3_PER_DECORDTL 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_EDUCATE ADD CONSTRAINT FK1_PER_EDUCATE 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_EDUCATE ADD CONSTRAINT FK2_PER_EDUCATE 
								  FOREIGN KEY (ST_CODE) REFERENCES PER_SCHOLARTYPE (ST_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_EDUCATE ADD CONSTRAINT FK3_PER_EDUCATE 
								  FOREIGN KEY (CT_CODE) REFERENCES PER_COUNTRY (CT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_EDUCATE ADD CONSTRAINT FK4_PER_EDUCATE 
								  FOREIGN KEY (EN_CODE) REFERENCES	PER_EDUCNAME (EN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_EDUCATE ADD CONSTRAINT FK5_PER_EDUCATE 
								  FOREIGN KEY (EM_CODE) REFERENCES	PER_EDUCMAJOR (EM_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_EDUCATE ADD CONSTRAINT FK6_PER_EDUCATE 
								  FOREIGN KEY (INS_CODE) REFERENCES PER_INSTITUTE (INS_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_EDUCNAME ADD CONSTRAINT FK1_PER_EDUCNAME 
								  FOREIGN KEY (EL_CODE) REFERENCES PER_EDUCLEVEL (EL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ADD CONSTRAINT FK1_PER_EXTRA_INCOMEHIS 
								  FOREIGN KEY (EXIN_CODE) REFERENCES PER_EXTRA_INCOME_TYPE (EXIN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_EXTRAHIS ADD CONSTRAINT FK1_PER_EXTRAHIS 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_EXTRAHIS ADD CONSTRAINT FK2_PER_EXTRAHIS 
								  FOREIGN KEY (EX_CODE) REFERENCES PER_EXTRATYPE (EX_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_HEIR ADD CONSTRAINT FK1_PER_HEIR 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_HEIR ADD CONSTRAINT FK2_PER_HEIR 
								  FOREIGN KEY (HR_CODE) REFERENCES PER_HEIRTYPE (HR_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_INSTITUTE ADD CONSTRAINT FK1_PER_INSTITUTE 
								  FOREIGN KEY (CT_CODE) REFERENCES PER_COUNTRY (CT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_INVEST1 ADD CONSTRAINT FK1_PER_INVEST1 
								  FOREIGN KEY (CRD_CODE) REFERENCES PER_CRIME_DTL (CRD_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_INVEST1DTL ADD CONSTRAINT FK1_PER_INVEST1DTL 
								  FOREIGN KEY (INV_ID) REFERENCES PER_INVEST1 (INV_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_INVEST1DTL ADD CONSTRAINT FK2_PER_INVEST1DTL 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_INVEST2 ADD CONSTRAINT FK1_PER_INVEST2 
								  FOREIGN KEY (INV_ID_REF) REFERENCES PER_INVEST1 (INV_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_INVEST2DTL ADD CONSTRAINT FK1_PER_INVEST2DTL 
								  FOREIGN KEY (INV_ID) REFERENCES PER_INVEST2 (INV_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_INVEST2DTL ADD CONSTRAINT FK2_PER_INVEST2DTL 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_INVEST2DTL ADD CONSTRAINT FK3_PER_INVEST2DTL 
								  FOREIGN KEY (CRD_CODE) REFERENCES PER_CRIME_DTL (CRD_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_INVEST2DTL ADD CONSTRAINT FK4_PER_INVEST2DTL 
								  FOREIGN KEY (PEN_CODE) REFERENCES PER_PENALTY (PEN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_LAYER ADD CONSTRAINT FK1_PER_LAYER 
								  FOREIGN KEY (LEVEL_NO) REFERENCES PER_LEVEL (LEVEL_NO) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_LAYEREMP ADD CONSTRAINT FK1_PER_LAYEREMP 
								  FOREIGN KEY (PG_CODE) REFERENCES PER_POS_GROUP (PG_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_LETTER ADD CONSTRAINT FK1_PER_LETTER 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_LETTER ADD CONSTRAINT FK2_PER_LETTER 
								  FOREIGN KEY (PER_ID_SIGN1) REFERENCES PER_PERSONAL  (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_MARRHIS ADD CONSTRAINT FK1_PER_MARRHIS 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_MGT ADD CONSTRAINT FK1_PER_MGT 
								  FOREIGN KEY (PS_CODE) REFERENCES PER_STATUS (PS_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_MGTSALARY ADD CONSTRAINT FK1_PER_MGTSALARY 
								  FOREIGN KEY (PT_CODE) REFERENCES PER_TYPE (PT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_MGTSALARY ADD CONSTRAINT FK2_PER_MGTSALARY 
								  FOREIGN KEY (LEVEL_NO) REFERENCES PER_LEVEL (LEVEL_NO) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK1_PER_MOVE_REQ 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK2_PER_MOVE_REQ 
								  FOREIGN KEY (PL_CODE_1) REFERENCES PER_LINE (PL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK3_PER_MOVE_REQ 
								  FOREIGN KEY (PN_CODE_1) REFERENCES PER_POS_NAME (PN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK4_PER_MOVE_REQ 
								  FOREIGN KEY (ORG_ID_1) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK5_PER_MOVE_REQ 
								  FOREIGN KEY (PL_CODE_2) REFERENCES PER_LINE (PL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK6_PER_MOVE_REQ 
								  FOREIGN KEY (PN_CODE_2) REFERENCES PER_POS_NAME (PN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK7_PER_MOVE_REQ 
								  FOREIGN KEY (ORG_ID_2) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK8_PER_MOVE_REQ 
								  FOREIGN KEY (PL_CODE_3) REFERENCES PER_LINE (PL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK9_PER_MOVE_REQ 
								  FOREIGN KEY (PN_CODE_3) REFERENCES PER_POS_NAME (PN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK10_PER_MOVE_REQ 
								  FOREIGN KEY (ORG_ID_3) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_NAMEHIS ADD CONSTRAINT FK1_PER_NAMEHIS 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_NAMEHIS ADD CONSTRAINT FK2_PER_NAMEHIS 
								  FOREIGN KEY (PN_CODE) REFERENCES PER_PRENAME (PN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK1_PER_ORDER_DTL 
								  FOREIGN KEY (ORD_ID) REFERENCES PER_ORDER (ORD_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK2_PER_ORDER_DTL 
								  FOREIGN KEY (POS_ID_OLD) REFERENCES PER_POSITION (POS_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK3_PER_ORDER_DTL 
								  FOREIGN KEY (ORG_ID) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK4_PER_ORDER_DTL 
								  FOREIGN KEY (OT_CODE) REFERENCES PER_OFF_TYPE (OT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK5_PER_ORDER_DTL 
								  FOREIGN KEY (ORG_ID_1) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK6_PER_ORDER_DTL 
								  FOREIGN KEY (ORG_ID_2) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK7_PER_ORDER_DTL 
								  FOREIGN KEY (PM_CODE) REFERENCES	PER_MGT (PM_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK8_PER_ORDER_DTL 
								  FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK9_PER_ORDER_DTL 
								  FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK10_PER_ORDER_DTL 
								  FOREIGN KEY (SKILL_CODE) REFERENCES PER_SKILL (SKILL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK11_PER_ORDER_DTL 
								  FOREIGN KEY (PT_CODE) REFERENCES PER_TYPE (PT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK12_PER_ORDER_DTL 
								  FOREIGN KEY (PC_CODE) REFERENCES PER_CONDITION (PC_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORG ADD CONSTRAINT FK1_PER_ORG 
								  FOREIGN KEY (OL_CODE) REFERENCES PER_ORG_LEVEL (OL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORG ADD CONSTRAINT FK2_PER_ORG 
								  FOREIGN KEY (OT_CODE) REFERENCES PER_ORG_TYPE (OT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORG ADD CONSTRAINT FK3_PER_ORG 
								  FOREIGN KEY (OP_CODE) REFERENCES PER_ORG_PROVINCE (OP_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORG ADD CONSTRAINT FK4_PER_ORG 
								  FOREIGN KEY (OS_CODE) REFERENCES PER_ORG_STAT (OS_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORG ADD CONSTRAINT FK5_PER_ORG 
								  FOREIGN KEY (AP_CODE) REFERENCES PER_AMPHUR (AP_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORG ADD CONSTRAINT FK6_PER_ORG 
								  FOREIGN KEY (PV_CODE) REFERENCES PER_PROVINCE (PV_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORG ADD CONSTRAINT FK7_PER_ORG 
								  FOREIGN KEY (CT_CODE) REFERENCES PER_COUNTRY (CT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORG ADD CONSTRAINT FK8_PER_ORG 
								  FOREIGN KEY (ORG_ID_REF) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORG_ASS ADD CONSTRAINT FK1_PER_ORG_ASS 
								  FOREIGN KEY (OL_CODE) REFERENCES PER_ORG_LEVEL (OL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORG_ASS ADD CONSTRAINT FK2_PER_ORG_ASS 
								  FOREIGN KEY (OT_CODE) REFERENCES	PER_ORG_TYPE (OT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORG_ASS ADD CONSTRAINT FK3_PER_ORG_ASS 
								  FOREIGN KEY (OP_CODE) REFERENCES PER_ORG_PROVINCE (OP_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORG_ASS ADD CONSTRAINT FK4_PER_ORG_ASS 
								  FOREIGN KEY (OS_CODE) REFERENCES PER_ORG_STAT (OS_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORG_ASS ADD CONSTRAINT FK5_PER_ORG_ASS 
								  FOREIGN KEY (AP_CODE) REFERENCES PER_AMPHUR (AP_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORG_ASS ADD CONSTRAINT FK6_PER_ORG_ASS 
								  FOREIGN KEY (PV_CODE) REFERENCES PER_PROVINCE (PV_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORG_ASS ADD CONSTRAINT FK7_PER_ORG_ASS 
								  FOREIGN KEY (CT_CODE) REFERENCES PER_COUNTRY (CT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORG_ASS ADD CONSTRAINT FK8_PER_ORG_ASS 
								  FOREIGN KEY (ORG_ID_REF) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORG_JOB ADD CONSTRAINT FK1_PER_ORG_JOB 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORG_JOB ADD CONSTRAINT FK2_PER_ORG_JOB 
								  FOREIGN KEY (ORG_ID) REFERENCES PER_ORG_ASS (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK1_PER_PERSONAL 
								  FOREIGN KEY (OT_CODE) REFERENCES PER_OFF_TYPE (OT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK2_PER_PERSONAL 
								  FOREIGN KEY (PN_CODE) REFERENCES PER_PRENAME (PN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK3_PER_PERSONAL 
								  FOREIGN KEY (ORG_ID) REFERENCES PER_ORG_ASS (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK4_PER_PERSONAL 
								  FOREIGN KEY (POS_ID) REFERENCES PER_POSITION (POS_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK5_PER_PERSONAL 
								  FOREIGN KEY (POEM_ID) REFERENCES PER_POS_EMP (POEM_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK6_PER_PERSONAL 
								  FOREIGN KEY (LEVEL_NO) REFERENCES PER_LEVEL (LEVEL_NO) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK7_PER_PERSONAL 
								  FOREIGN KEY (MR_CODE) REFERENCES PER_MARRIED (MR_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK8_PER_PERSONAL 
								  FOREIGN KEY (RE_CODE) REFERENCES PER_RELIGION (RE_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK9_PER_PERSONAL 
								  FOREIGN KEY (PN_CODE_F) REFERENCES PER_PRENAME (PN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK10_PER_PERSONAL 
								  FOREIGN KEY (PN_CODE_M) REFERENCES PER_PRENAME (PN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK11_PER_PERSONAL 
								  FOREIGN KEY (PV_CODE) REFERENCES PER_PROVINCE (PV_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_PERSONAL ADD CONSTRAINT FK12_PER_PERSONAL 
								  FOREIGN KEY (MOV_CODE) REFERENCES PER_MOVMENT (MOV_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POS_EMP ADD CONSTRAINT FK1_PER_POS_EMP 
								  FOREIGN KEY (ORG_ID) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POS_EMP ADD CONSTRAINT FK2_PER_POS_EMP 
								  FOREIGN KEY (ORG_ID_1) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POS_EMP ADD CONSTRAINT FK3_PER_POS_EMP 
								  FOREIGN KEY (ORG_ID_2) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POS_EMP ADD CONSTRAINT FK4_PER_POS_EMP 
								  FOREIGN KEY (PN_CODE) REFERENCES PER_POS_NAME (PN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK1_PER_POS_MOVE 
								  FOREIGN KEY (POS_ID) REFERENCES PER_POSITION (POS_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK2_PER_POS_MOVE 
								  FOREIGN KEY (ORG_ID) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK3_PER_POS_MOVE 
								  FOREIGN KEY (OT_CODE) REFERENCES PER_OFF_TYPE (OT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK4_PER_POS_MOVE 
								  FOREIGN KEY (ORG_ID_1) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK5_PER_POS_MOVE 
								  FOREIGN KEY (ORG_ID_2) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK6_PER_POS_MOVE 
								  FOREIGN KEY (PM_CODE) REFERENCES PER_MGT (PM_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK7_PER_POS_MOVE 
								  FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK8_PER_POS_MOVE 
								  FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK9_PER_POS_MOVE 
								  FOREIGN KEY (SKILL_CODE) REFERENCES PER_SKILL (SKILL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK10_PER_POS_MOVE 
								  FOREIGN KEY (PT_CODE) REFERENCES PER_TYPE (PT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK11_PER_POS_MOVE 
								  FOREIGN KEY (PC_CODE) REFERENCES PER_CONDITION (PC_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POS_NAME ADD CONSTRAINT FK1_PER_POS_NAME 
								  FOREIGN KEY (PG_CODE) REFERENCES PER_POS_GROUP (PG_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK1_PER_POSITION 
								  FOREIGN KEY (ORG_ID) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK2_PER_POSITION 
								  FOREIGN KEY (OT_CODE) REFERENCES PER_OFF_TYPE (OT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK3_PER_POSITION 
								  FOREIGN KEY (ORG_ID_1) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK4_PER_POSITION 
								  FOREIGN KEY (ORG_ID_2) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK5_PER_POSITION 
								  FOREIGN KEY (PM_CODE) REFERENCES	PER_MGT (PM_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK6_PER_POSITION 
								  FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK7_PER_POSITION 
								  FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK8_PER_POSITION 
								  FOREIGN KEY (SKILL_CODE) REFERENCES PER_SKILL (SKILL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK9_PER_POSITION 
								  FOREIGN KEY (PT_CODE) REFERENCES PER_TYPE (PT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK10_PER_POSITION 
								  FOREIGN KEY (PC_CODE) REFERENCES PER_CONDITION (PC_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK1_PER_POSITIONHIS 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK2_PER_POSITIONHIS 
								  FOREIGN KEY (MOV_CODE) REFERENCES PER_MOVMENT (MOV_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK3_PER_POSITIONHIS 
								  FOREIGN KEY (PM_CODE) REFERENCES PER_MGT (PM_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK4_PER_POSITIONHIS 
								  FOREIGN KEY (LEVEL_NO) REFERENCES	PER_LEVEL (LEVEL_NO) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK5_PER_POSITIONHIS 
								  FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK6_PER_POSITIONHIS 
								  FOREIGN KEY (PN_CODE) REFERENCES PER_POS_NAME (PN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK7_PER_POSITIONHIS 
								  FOREIGN KEY (PT_CODE) REFERENCES PER_TYPE (PT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK8_PER_POSITIONHIS 
								  FOREIGN KEY (CT_CODE) REFERENCES PER_COUNTRY (CT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK9_PER_POSITIONHIS 
								  FOREIGN KEY (PV_CODE) REFERENCES PER_PROVINCE (PV_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK10_PER_POSITIONHIS 
								  FOREIGN KEY (AP_CODE) REFERENCES PER_AMPHUR (AP_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK11_PER_POSITIONHIS 
								  FOREIGN KEY (ORG_ID_1) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK12_PER_POSITIONHIS 
								  FOREIGN KEY (ORG_ID_2) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_POSITIONHIS ADD CONSTRAINT FK13_PER_POSITIONHIS 
								  FOREIGN KEY (ORG_ID_3) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_PROMOTE_C ADD CONSTRAINT FK1_PER_PROMOTE_C 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_PROMOTE_E ADD CONSTRAINT FK1_PER_PROMOTE_E 
								  FOREIGN KEY (POEM_ID) REFERENCES PER_POS_EMP (POEM_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_PROMOTE_E ADD CONSTRAINT FK2_PER_PROMOTE_E 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_PROMOTE_P ADD CONSTRAINT FK1_PER_PROMOTE_P 
								  FOREIGN KEY (POS_ID) REFERENCES PER_POSITION (POS_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_PROMOTE_P ADD CONSTRAINT FK2_PER_PROMOTE_P 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_PROVINCE ADD CONSTRAINT FK1_PER_PROVINCE 
								  FOREIGN KEY (CT_CODE) REFERENCES PER_COUNTRY (CT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_PUNISHMENT ADD CONSTRAINT FK1_PER_PUNISHMENT 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_PUNISHMENT ADD CONSTRAINT FK2_PER_PUNISHMENT 
								  FOREIGN KEY (CRD_CODE) REFERENCES PER_CRIME_DTL (CRD_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_PUNISHMENT ADD CONSTRAINT FK3_PER_PUNISHMENT 
								  FOREIGN KEY (PEN_CODE) REFERENCES PER_PENALTY (PEN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK1_PER_REQ1_DTL1 
								  FOREIGN KEY (REQ_ID) REFERENCES PER_REQ1 (REQ_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK2_PER_REQ1_DTL1 
								  FOREIGN KEY (ORG_ID) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK3_PER_REQ1_DTL1 
								  FOREIGN KEY (OT_CODE) REFERENCES PER_OFF_TYPE (OT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK4_PER_REQ1_DTL1 
								  FOREIGN KEY (ORG_ID_1) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK5_PER_REQ1_DTL1 
								  FOREIGN KEY (ORG_ID_2) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK6_PER_REQ1_DTL1 
								  FOREIGN KEY (PM_CODE) REFERENCES PER_MGT (PM_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK7_PER_REQ1_DTL1 
								  FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK8_PER_REQ1_DTL1 
								  FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK9_PER_REQ1_DTL1 
								  FOREIGN KEY (SKILL_CODE) REFERENCES PER_SKILL (SKILL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK10_PER_REQ1_DTL1 
								  FOREIGN KEY (PT_CODE) REFERENCES PER_TYPE (PT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK11_PER_REQ1_DTL1 
								  FOREIGN KEY (PC_CODE) REFERENCES PER_CONDITION (PC_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ1_DTL2 ADD CONSTRAINT FK1_PER_REQ1_DTL2 
								  FOREIGN KEY (REQ_ID, REQ_SEQ) REFERENCES PER_REQ1_DTL1 (REQ_ID, REQ_SEQ) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ1_DTL2 ADD CONSTRAINT FK2_PER_REQ1_DTL2 
								  FOREIGN KEY (POS_ID) REFERENCES PER_POSITION (POS_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK1_PER_REQ2_DTL 
								  FOREIGN KEY (REQ_ID) REFERENCES PER_REQ2 (REQ_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK2_PER_REQ2_DTL 
								  FOREIGN KEY (POS_ID_RETIRE) REFERENCES PER_POSITION (POS_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK3_PER_REQ2_DTL 
								  FOREIGN KEY (POS_ID_DROP) REFERENCES PER_POSITION (POS_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK4_PER_REQ2_DTL 
								  FOREIGN KEY (POS_ID_REQ) REFERENCES PER_POSITION (POS_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK5_PER_REQ2_DTL 
								  FOREIGN KEY (ORG_ID) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK6_PER_REQ2_DTL 
								  FOREIGN KEY (OT_CODE) REFERENCES PER_OFF_TYPE (OT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK7_PER_REQ2_DTL 
								  FOREIGN KEY (ORG_ID_1) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK8_PER_REQ2_DTL 
								  FOREIGN KEY (ORG_ID_2) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK9_PER_REQ2_DTL 
								  FOREIGN KEY (PM_CODE) REFERENCES PER_MGT (PM_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK10_PER_REQ2_DTL 
								  FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK11_PER_REQ2_DTL 
								  FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK12_PER_REQ2_DTL 
								  FOREIGN KEY (SKILL_CODE) REFERENCES PER_SKILL (SKILL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK13_PER_REQ2_DTL 
								  FOREIGN KEY (PT_CODE) REFERENCES PER_TYPE (PT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK14_PER_REQ2_DTL 
								  FOREIGN KEY (PC_CODE) REFERENCES PER_CONDITION (PC_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK1_PER_REQ3_DTL 
								  FOREIGN KEY (REQ_ID) REFERENCES PER_REQ3 (REQ_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK2_PER_REQ3_DTL 
								  FOREIGN KEY (POS_ID) REFERENCES PER_POSITION (POS_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK3_PER_REQ3_DTL 
								  FOREIGN KEY (ORG_ID) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK4_PER_REQ3_DTL 
								  FOREIGN KEY (OT_CODE) REFERENCES PER_OFF_TYPE (OT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK5_PER_REQ3_DTL 
								  FOREIGN KEY (ORG_ID_1) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK6_PER_REQ3_DTL	 
								  FOREIGN KEY (ORG_ID_2) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK7_PER_REQ3_DTL 
								  FOREIGN KEY (PM_CODE) REFERENCES PER_MGT (PM_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK8_PER_REQ3_DTL 
								  FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK9_PER_REQ3_DTL 
								  FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK10_PER_REQ3_DTL 
								  FOREIGN KEY (SKILL_CODE) REFERENCES PER_SKILL (SKILL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK11_PER_REQ3_DTL 
								  FOREIGN KEY (PT_CODE) REFERENCES PER_TYPE (PT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK12_PER_REQ3_DTL 
								  FOREIGN KEY (PC_CODE) REFERENCES PER_CONDITION (PC_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REWARDHIS ADD CONSTRAINT FK1_PER_REWARDHIS 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_REWARDHIS ADD CONSTRAINT FK2_PER_REWARDHIS 
								  FOREIGN KEY (REW_CODE) REFERENCES PER_REWARD (REW_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_SALARYHIS ADD CONSTRAINT FK1_PER_SALARYHIS 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_SALARYHIS ADD CONSTRAINT FK2_PER_SALARYHIS 
								  FOREIGN KEY (MOV_CODE) REFERENCES PER_MOVMENT (MOV_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_SALPROMOTE ADD CONSTRAINT FK1_PER_SALPROMOTE 
								  FOREIGN KEY (SALQ_YEAR, SALQ_TYPE) REFERENCES PER_SALQUOTA (SALQ_YEAR, SALQ_TYPE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SALPROMOTE ADD CONSTRAINT FK2_PER_SALPROMOTE 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SALQUOTADTL1 ADD CONSTRAINT FK1_PER_SALQUOTADTL1 
								  FOREIGN KEY (SALQ_YEAR, SALQ_TYPE) REFERENCES PER_SALQUOTA (SALQ_YEAR, SALQ_TYPE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SALQUOTADTL1 ADD CONSTRAINT FK2_PER_SALQUOTADTL1 
								  FOREIGN KEY (ORG_ID) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SALQUOTADTL2 ADD CONSTRAINT FK1_PER_SALQUOTADTL2 
								  FOREIGN KEY (SALQ_YEAR, SALQ_TYPE) REFERENCES PER_SALQUOTA (SALQ_YEAR, SALQ_TYPE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SALQUOTADTL2 ADD CONSTRAINT FK2_PER_SALQUOTADTL2 
								  FOREIGN KEY (ORG_ID) REFERENCES PER_ORG_ASS (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SCHOLAR ADD CONSTRAINT FK1_PER_SCHOLAR 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SCHOLAR ADD CONSTRAINT FK2_PER_SCHOLAR 
								  FOREIGN KEY (PN_CODE) REFERENCES PER_PRENAME (PN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SCHOLAR ADD CONSTRAINT FK3_PER_SCHOLAR 
								  FOREIGN KEY (EN_CODE) REFERENCES PER_EDUCNAME (EN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SCHOLAR ADD CONSTRAINT FK4_PER_SCHOLAR 
								  FOREIGN KEY (EM_CODE) REFERENCES PER_EDUCMAJOR (EM_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SCHOLAR ADD CONSTRAINT FK5_PER_SCHOLAR 
								  FOREIGN KEY (SCH_CODE) REFERENCES PER_SCHOLARSHIP (SCH_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SCHOLAR ADD CONSTRAINT FK6_PER_SCHOLAR 
								  FOREIGN KEY (INS_CODE) REFERENCES PER_INSTITUTE (INS_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SCHOLAR ADD CONSTRAINT FK7_PER_SCHOLAR 
								  FOREIGN KEY (EL_CODE) REFERENCES PER_EDUCLEVEL (EL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SCHOLARINC ADD CONSTRAINT FK1_PER_SCHOLARINC 
								  FOREIGN KEY (SC_ID) REFERENCES PER_SCHOLAR (SC_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD CONSTRAINT FK1_PER_SCHOLARSHIP 
								  FOREIGN KEY (ST_CODE) REFERENCES PER_SCHOLARTYPE (ST_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SERVICEHIS ADD CONSTRAINT FK1_PER_SERVICEHIS 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_SERVICEHIS ADD CONSTRAINT FK2_PER_SERVICEHIS 
								  FOREIGN KEY (SV_CODE) REFERENCES PER_SERVICE (SV_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_SERVICEHIS ADD CONSTRAINT FK3_PER_SERVICEHIS 
								  FOREIGN KEY (SRT_CODE) REFERENCES PER_SERVICETITLE (SRT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_SERVICEHIS ADD CONSTRAINT FK4_PER_SERVICEHIS 
								  FOREIGN KEY (ORG_ID) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_SERVICEHIS ADD CONSTRAINT FK5_PER_SERVICEHIS 
								  FOREIGN KEY (PER_ID_ASSIGN) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_SERVICEHIS ADD CONSTRAINT FK6_PER_SERVICEHIS 
								  FOREIGN KEY (ORG_ID_ASSIGN) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_SKILL ADD CONSTRAINT FK1_PER_SKILL 
								  FOREIGN KEY (SG_CODE) REFERENCES PER_SKILL_GROUP (SG_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_SKILL_GROUP ADD CONSTRAINT FK1_PER_SKILL_GROUP 
								  FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_SUM ADD CONSTRAINT FK1_PER_SUM 
								  FOREIGN KEY (ORG_ID) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL1 ADD CONSTRAINT FK1_PER_SUM_DTL1 
								  FOREIGN KEY (SUM_ID) REFERENCES PER_SUM (SUM_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL1 ADD CONSTRAINT FK2_PER_SUM_DTL1 
								  FOREIGN KEY (OS_CODE) REFERENCES PER_ORG_STAT (OS_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL1 ADD CONSTRAINT FK3_PER_SUM_DTL1 
								  FOREIGN KEY (OT_CODE) REFERENCES PER_ORG_TYPE (OT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL2 ADD CONSTRAINT FK1_PER_SUM_DTL2 
								  FOREIGN KEY (SUM_ID) REFERENCES PER_SUM (SUM_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL2 ADD CONSTRAINT FK2_PER_SUM_DTL2 
								  FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL2 ADD CONSTRAINT FK3_PER_SUM_DTL2 
								  FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL2 ADD CONSTRAINT FK4_PER_SUM_DTL2 
								  FOREIGN KEY (PT_CODE) REFERENCES PER_TYPE (PT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL3 ADD CONSTRAINT FK1_PER_SUM_DTL3 
								  FOREIGN KEY (SUM_ID) REFERENCES PER_SUM (SUM_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL3 ADD CONSTRAINT FK2_PER_SUM_DTL3 
								  FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL4 ADD CONSTRAINT FK1_PER_SUM_DTL4 
								  FOREIGN KEY (SUM_ID) REFERENCES PER_SUM (SUM_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL4 ADD CONSTRAINT FK2_PER_SUM_DTL4 
								  FOREIGN KEY (PS_CODE) REFERENCES PER_STATUS (PS_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL4 ADD CONSTRAINT FK3_PER_SUM_DTL4 
								  FOREIGN KEY (PM_CODE) REFERENCES PER_MGT (PM_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL4 ADD CONSTRAINT FK4_PER_SUM_DTL4 
								  FOREIGN KEY (LEVEL_NO) REFERENCES PER_LEVEL (LEVEL_NO) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL5 ADD CONSTRAINT FK1_PER_SUM_DTL5 
								  FOREIGN KEY (SUM_ID) REFERENCES PER_SUM (SUM_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL5 ADD CONSTRAINT FK2_PER_SUM_DTL5 
								  FOREIGN KEY (OP_CODE) REFERENCES PER_ORG_PROVINCE (OP_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL5 ADD CONSTRAINT FK3_PER_SUM_DTL5 
								  FOREIGN KEY (PM_CODE) REFERENCES PER_MGT (PM_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL5 ADD CONSTRAINT FK4_PER_SUM_DTL5 
								  FOREIGN KEY (LEVEL_NO) REFERENCES PER_LEVEL (LEVEL_NO) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL6 ADD CONSTRAINT FK1_PER_SUM_DTL6 
								  FOREIGN KEY (SUM_ID) REFERENCES PER_SUM (SUM_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL6 ADD CONSTRAINT FK2_PER_SUM_DTL6 
								  FOREIGN KEY (OT_CODE) REFERENCES PER_ORG_TYPE (OT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL7 ADD CONSTRAINT FK1_PER_SUM_DTL7 
								  FOREIGN KEY (SUM_ID) REFERENCES PER_SUM (SUM_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL7 ADD CONSTRAINT FK2_PER_SUM_DTL7 
								  FOREIGN KEY (LEVEL_NO) REFERENCES PER_LEVEL (LEVEL_NO) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL8 ADD CONSTRAINT FK1_PER_SUM_DTL8 
								  FOREIGN KEY (SUM_ID) REFERENCES PER_SUM (SUM_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL8 ADD CONSTRAINT FK2_PER_SUM_DTL8 
								  FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL8 ADD CONSTRAINT FK3_PER_SUM_DTL8 
								  FOREIGN KEY (LEVEL_NO) REFERENCES PER_LEVEL (LEVEL_NO) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL9 ADD CONSTRAINT FK1_PER_SUM_DTL9 
								  FOREIGN KEY (SUM_ID) REFERENCES PER_SUM (SUM_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL9 ADD CONSTRAINT FK2_PER_SUM_DTL9 
								  FOREIGN KEY (PL_CODE) REFERENCES PER_LINE (PL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL9 ADD CONSTRAINT FK3_PER_SUM_DTL9 
								  FOREIGN KEY (EL_CODE) REFERENCES PER_EDUCLEVEL (EL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_TIMEHIS ADD CONSTRAINT FK1_PER_TIMEHIS 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_TIMEHIS ADD CONSTRAINT FK2_PER_TIMEHIS 
								  FOREIGN KEY (TIME_CODE) REFERENCES PER_TIME (TIME_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_TRAINING ADD CONSTRAINT FK1_PER_TRAINING 
								  FOREIGN KEY (PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_TRAINING ADD CONSTRAINT FK2_PER_TRAINING 
								  FOREIGN KEY (TR_CODE) REFERENCES PER_TRAIN (TR_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_TRAINING ADD CONSTRAINT FK3_PER_TRAINING 
								  FOREIGN KEY (CT_CODE) REFERENCES PER_COUNTRY (CT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_TRAINING ADD CONSTRAINT FK4_PER_TRAINING 
								  FOREIGN KEY (CT_CODE_FUND) REFERENCES PER_COUNTRY (CT_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK1_PER_TRANSFER_REQ 
								  FOREIGN KEY (PN_CODE) REFERENCES PER_PRENAME (PN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK2_PER_TRANSFER_REQ 
								  FOREIGN KEY (EN_CODE) REFERENCES PER_EDUCNAME (EN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK3_PER_TRANSFER_REQ 
								  FOREIGN KEY (EM_CODE) REFERENCES PER_EDUCMAJOR (EM_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK4_PER_TRANSFER_REQ 
								  FOREIGN KEY (INS_CODE) REFERENCES PER_INSTITUTE (INS_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK5_PER_TRANSFER_REQ 
								  FOREIGN KEY (PL_CODE_1) REFERENCES PER_LINE (PL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK6_PER_TRANSFER_REQ 
								  FOREIGN KEY (PN_CODE_1) REFERENCES PER_POS_NAME (PN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK7_PER_TRANSFER_REQ 
								  FOREIGN KEY (ORG_ID_1) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK8_PER_TRANSFER_REQ 
								  FOREIGN KEY (LEVEL_NO_1) REFERENCES PER_LEVEL (LEVEL_NO) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK9_PER_TRANSFER_REQ 
								  FOREIGN KEY (PL_CODE_2) REFERENCES PER_LINE (PL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK10_PER_TRANSFER_REQ 
								  FOREIGN KEY (PN_CODE_2) REFERENCES PER_POS_NAME (PN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK11_PER_TRANSFER_REQ 
								  FOREIGN KEY (ORG_ID_2) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK12_PER_TRANSFER_REQ 
								  FOREIGN KEY (LEVEL_NO_2) REFERENCES PER_LEVEL (LEVEL_NO) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK13_PER_TRANSFER_REQ 
								  FOREIGN KEY (PL_CODE_3) REFERENCES PER_LINE (PL_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK14_PER_TRANSFER_REQ 
								  FOREIGN KEY (PN_CODE_3) REFERENCES PER_POS_NAME (PN_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK15_PER_TRANSFER_REQ 
								  FOREIGN KEY (ORG_ID_3) REFERENCES PER_ORG (ORG_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK16_PER_TRANSFER_REQ 
								  FOREIGN KEY (LEVEL_NO_3) REFERENCES PER_LEVEL (LEVEL_NO) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

			} // end if odbc

			if($DPISDB=="odbc") { 
				$cmd = " ALTER TABLE  PER_CONTROL ALTER ORG_ID INTEGER NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_POSITION DROP CONSTRAINT INXU1_PER_POSITION ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU1_PER_POSITION ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_PERSONAL DROP CONSTRAINT INXU1_PERSONAL ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU1_PERSONAL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_PERSONAL DROP CONSTRAINT INXU2_PERSONAL ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU2_PERSONAL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_PERSONAL DROP CONSTRAINT INXU3_PERSONAL ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU3_PERSONAL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_POS_EMP DROP CONSTRAINT INXU1_PER_POS_EMP ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU1_PER_POS_EMP ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_POS_EMPSER DROP CONSTRAINT INXU1_PER_POS_EMPSER ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU1_PER_POS_EMPSER ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_ORG_TYPE DROP CONSTRAINT INXU1_PER_ORG_TYPE ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU1_PER_ORG_TYPE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITION ON PER_POSITION (POS_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_CONTROL set CTRL_ALTER = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if($CTRL_ALTER < 1) 

		if($CTRL_ALTER < 2) {
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD DEPARTMENT_ID INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD DEPARTMENT_ID NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERSONAL ADD DEPARTMENT_ID INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if ($CTRL_TYPE == 4)	{
				$cmd = " UPDATE PER_PERSONAL SET DEPARTMENT_ID=$DEPARTMENT_ID ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} else {
				if($DPISDB=="odbc" || $DPISDB=="mysql"){ 
					$cmd = " SELECT PER_ID, PER_TYPE, POS_ID, POEM_ID, POEMS_ID 
									  FROM PER_PERSONAL WHERE PER_STATUS <> 3 ";
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
					$cmd = " UPDATE PER_PERSONAL X SET X.DEPARTMENT_ID = (SELECT D.ORG_ID_REF FROM PER_PERSONAL A, 
									  PER_POSITION B, PER_ORG C, PER_ORG D WHERE A.PER_ID = X.PER_ID AND A.POS_ID = B.POS_ID AND 
									  B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) WHERE PER_TYPE = 1 ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " UPDATE PER_PERSONAL X SET X.DEPARTMENT_ID = (SELECT D.ORG_ID_REF FROM PER_PERSONAL A, 
									  PER_POS_EMP B, PER_ORG C, PER_ORG D WHERE A.PER_ID = X.PER_ID AND A.POEM_ID = B.POEM_ID AND 
									  B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) WHERE PER_TYPE = 2 ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					
					$cmd = " UPDATE PER_PERSONAL X SET X.DEPARTMENT_ID = (SELECT D.ORG_ID_REF FROM PER_PERSONAL A, 
									  PER_POS_EMPSER B, PER_ORG C, PER_ORG D WHERE A.PER_ID = X.PER_ID AND A.POEMS_ID = 
									  B.POEMS_ID AND B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) WHERE PER_TYPE = 3 ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} // end if
			} // end if

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD APPROVE_PER_ID INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD APPROVE_PER_ID NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERSONAL ADD APPROVE_PER_ID INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD REPLACE_PER_ID INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD REPLACE_PER_ID NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERSONAL ADD REPLACE_PER_ID INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD ABSENT_FLAG SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD ABSENT_FLAG NUMBER(1) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERSONAL ADD ABSENT_FLAG SMALLINT(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ORG_JOB ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORG_JOB ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_ORG_JOB, PER_PERSONAL SET PER_ORG_JOB.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_ORG_JOB.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_ORG_JOB A SET A.PER_CARDNO = 
								  (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD EP_CODE VARCHAR(10) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD EP_CODE VARCHAR2(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_POSITIONHIS, PER_PERSONAL SET PER_POSITIONHIS.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_POSITIONHIS.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_POSITIONHIS A SET A.PER_CARDNO = 
								  (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_SALARYHIS, PER_PERSONAL SET PER_SALARYHIS.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_SALARYHIS.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_SALARYHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EXTRAHIS ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EXTRAHIS ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_EXTRAHIS, PER_PERSONAL SET PER_EXTRAHIS.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_EXTRAHIS.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_EXTRAHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_EXTRA_INCOMEHIS, PER_PERSONAL SET PER_EXTRA_INCOMEHIS.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_EXTRA_INCOMEHIS.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_EXTRA_INCOMEHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO 
								  FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EDUCATE ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EDUCATE ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_EDUCATE, PER_PERSONAL SET PER_EDUCATE.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_EDUCATE.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_EDUCATE A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINING ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINING ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_TRAINING, PER_PERSONAL SET PER_TRAINING.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_TRAINING.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_TRAINING A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ABILITY ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ABILITY ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_ABILITY, PER_PERSONAL SET PER_ABILITY.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
								  WHERE PER_PERSONAL.PER_ID = PER_ABILITY.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_ABILITY A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_HEIR ADD PER_CARDNO	VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_HEIR ADD PER_CARDNO	VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_HEIR, PER_PERSONAL SET PER_HEIR.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
								  WHERE PER_PERSONAL.PER_ID = PER_HEIR.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_HEIR A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ABSENTHIS ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ABSENTHIS ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_ABSENTHIS, PER_PERSONAL SET PER_ABSENTHIS.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_ABSENTHIS.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_ABSENTHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PUNISHMENT ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PUNISHMENT ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_PUNISHMENT, PER_PERSONAL SET PER_PUNISHMENT.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_PUNISHMENT.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_PUNISHMENT A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SERVICEHIS ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SERVICEHIS ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_SERVICEHIS, PER_PERSONAL SET PER_SERVICEHIS.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_SERVICEHIS.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_SERVICEHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_REWARDHIS ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REWARDHIS ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_REWARDHIS, PER_PERSONAL SET PER_REWARDHIS.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_REWARDHIS.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_REWARDHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_MARRHIS ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_MARRHIS ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_MARRHIS, PER_PERSONAL SET PER_MARRHIS.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_MARRHIS.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_MARRHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_NAMEHIS ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_NAMEHIS ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_NAMEHIS, PER_PERSONAL SET PER_NAMEHIS.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_NAMEHIS.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_NAMEHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_GAZETTE VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_GAZETTE VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_DECORATEHIS, PER_PERSONAL SET PER_DECORATEHIS.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_DECORATEHIS.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_DECORATEHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TIMEHIS ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TIMEHIS ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_TIMEHIS, PER_PERSONAL SET PER_TIMEHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
								  WHERE PER_PERSONAL.PER_ID = PER_TIMEHIS.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_TIMEHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONALPIC ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONALPIC ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONALPIC ADD PER_PICPATH VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONALPIC ADD PER_PICPATH VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_PERSONALPIC, PER_PERSONAL SET PER_PERSONALPIC.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_PERSONALPIC.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_PERSONALPIC A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMDTL ADD PER_CARDNO	VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL ADD PER_CARDNO	VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMDTL ADD EP_CODE VARCHAR(10) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL ADD EP_CODE VARCHAR2(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMDTL ADD POEMS_ID INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL ADD POEMS_ID NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMDTL ADD POEMS_ID INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMDTL ADD EP_CODE_ASSIGN VARCHAR(10) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL ADD EP_CODE_ASSIGN VARCHAR2(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_COMDTL, PER_PERSONAL SET PER_COMDTL.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
								  WHERE PER_PERSONAL.PER_ID = PER_COMDTL.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_COMDTL A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_MOVE_REQ ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_MOVE_REQ ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_MOVE_REQ, PER_PERSONAL SET PER_MOVE_REQ.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_MOVE_REQ.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_MOVE_REQ A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PROMOTE_C ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PROMOTE_C ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_PROMOTE_C, PER_PERSONAL SET PER_PROMOTE_C.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_PROMOTE_C.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_PROMOTE_C A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PROMOTE_P ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PROMOTE_P ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_PROMOTE_P, PER_PERSONAL SET PER_PROMOTE_P.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_PROMOTE_P.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_PROMOTE_P A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PROMOTE_E ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PROMOTE_E ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_PROMOTE_E, PER_PERSONAL SET PER_PROMOTE_E.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_PROMOTE_E.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_PROMOTE_E A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SALPROMOTE ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALPROMOTE ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_SALPROMOTE, PER_PERSONAL SET PER_SALPROMOTE.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_SALPROMOTE.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_SALPROMOTE A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_BONUSPROMOTE ADD PER_CARDNO	VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_BONUSPROMOTE ADD PER_CARDNO	VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_BONUSPROMOTE, PER_PERSONAL SET PER_BONUSPROMOTE.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_BONUSPROMOTE.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_BONUSPROMOTE A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ABSENT ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ABSENT ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ABSENT ADD APPROVE_FLAG SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ABSENT ADD APPROVE_FLAG NUMBER(1) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ABSENT ADD APPROVE_FLAG SMALLINT(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ABSENT ADD APPROVE_PER_ID INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ABSENT ADD APPROVE_PER_ID NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ABSENT ADD APPROVE_PER_ID INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_ABSENT, PER_PERSONAL SET PER_ABSENT.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
								  WHERE PER_PERSONAL.PER_ID = PER_ABSENT.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_ABSENT A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_INVEST1DTL ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_INVEST1DTL ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_INVEST1DTL, PER_PERSONAL SET PER_INVEST1DTL.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_INVEST1DTL.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_INVEST1DTL A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_INVEST2DTL ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_INVEST2DTL ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_INVEST2DTL, PER_PERSONAL SET PER_INVEST2DTL.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_INVEST2DTL.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_INVEST2DTL A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLAR ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLAR ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_SCHOLAR, PER_PERSONAL SET PER_SCHOLAR.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_SCHOLAR.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_SCHOLAR A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COURSEDTL ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COURSEDTL ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_COURSEDTL, PER_PERSONAL SET PER_COURSEDTL.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_COURSEDTL.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_COURSEDTL A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_DECORDTL ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_DECORDTL ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_DECORDTL, PER_PERSONAL SET PER_DECORDTL.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_DECORDTL.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_DECORDTL A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_LETTER ADD PER_CARDNO VARCHAR(13) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LETTER ADD PER_CARDNO VARCHAR2(13) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_LETTER ADD LET_ADDITIONAL VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LETTER ADD LET_ADDITIONAL VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_LETTER, PER_PERSONAL SET PER_LETTER.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
								  WHERE PER_PERSONAL.PER_ID = PER_LETTER.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_LETTER A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
					 			  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ABSENT ADD ABS_REASON VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ABSENT ADD ABS_REASON VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ABSENT ADD ABS_ADDRESS VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ABSENT ADD ABS_ADDRESS VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_BLOOD(
				BL_CODE VARCHAR(10) NOT NULL,	
				BL_NAME VARCHAR(100) NOT NULL,
				BL_ACTIVE SINGLE NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_BLOOD PRIMARY KEY (BL_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_BLOOD(
				BL_CODE VARCHAR2(10) NOT NULL,	
				BL_NAME VARCHAR2(100) NOT NULL,
				BL_ACTIVE NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_BLOOD PRIMARY KEY (BL_CODE)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_BLOOD(
				BL_CODE VARCHAR(10) NOT NULL,	
				BL_NAME VARCHAR(100) NOT NULL,
				BL_ACTIVE SMALLINT(1) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_BLOOD PRIMARY KEY (BL_CODE)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_BLOOD (BL_CODE, BL_NAME, BL_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('A', '��������ʹ A', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_BLOOD (BL_CODE, BL_NAME, BL_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('AB', '��������ʹ AB', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_BLOOD (BL_CODE, BL_NAME, BL_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('B', '��������ʹ B', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_BLOOD (BL_CODE, BL_NAME, BL_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('O', '��������ʹ O', 1, $SESS_USERID, '$UPDATE_DATE') ";
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
				CONSTRAINT PK_PER_LAYER_NEW PRIMARY KEY (LAYER_TYPE, LEVEL_NO, LAYER_NO)) ";
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
				CONSTRAINT PK_PER_LAYER_NEW PRIMARY KEY (LAYER_TYPE, LEVEL_NO, LAYER_NO)) ";
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
				CONSTRAINT PK_PER_LAYER_NEW PRIMARY KEY (LAYER_TYPE, LEVEL_NO, LAYER_NO)) ";
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
				CONSTRAINT PK_PER_LAYEREMP_NEW PRIMARY KEY (PG_CODE, LAYERE_NO)) ";
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
				CONSTRAINT PK_PER_LAYEREMP_NEW PRIMARY KEY (PG_CODE, LAYERE_NO)) ";
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
				CONSTRAINT PK_PER_LAYEREMP_NEW PRIMARY KEY (PG_CODE, LAYERE_NO)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_SPECIAL_SKILLGRP(
				SS_CODE VARCHAR(10) NOT NULL,	
				SS_NAME VARCHAR(100) NOT NULL,
				SS_ACTIVE SINGLE NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_SPECIAL_SKILLGRP PRIMARY KEY (SS_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_SPECIAL_SKILLGRP(
				SS_CODE VARCHAR2(10) NOT NULL,	
				SS_NAME VARCHAR2(100) NOT NULL,
				SS_ACTIVE NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_SPECIAL_SKILLGRP PRIMARY KEY (SS_CODE)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_SPECIAL_SKILLGRP(
				SS_CODE VARCHAR(10) NOT NULL,	
				SS_NAME VARCHAR(100) NOT NULL,
				SS_ACTIVE SMALLINT(1) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_SPECIAL_SKILLGRP PRIMARY KEY (SS_CODE)) ";
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
				CONSTRAINT PK_PER_SPECIAL_SKILL PRIMARY KEY (SPS_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_SPECIAL_SKILL(
				SPS_ID NUMBER(10) NOT NULL,	
				PER_ID NUMBER(10) NOT NULL,	
				PER_CARDNO VARCHAR2(13) NULL,
				SS_CODE VARCHAR2(10) NOT NULL,	
				SPS_EMPHASIZE VARCHAR2(1000) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_SPECIAL_SKILL PRIMARY KEY (SPS_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_SPECIAL_SKILL(
				SPS_ID INTEGER(10) NOT NULL,	
				PER_ID INTEGER(10) NOT NULL,	
				PER_CARDNO VARCHAR(13) NULL,
				SS_CODE VARCHAR(10) NOT NULL,	
				SPS_EMPHASIZE TEXT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_SPECIAL_SKILL PRIMARY KEY (SPS_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_PERFORMANCE_REVIEW(
				PFR_ID INTEGER NOT NULL,	
				PFR_NAME VARCHAR(255) NOT NULL,
				PFR_YEAR VARCHAR(4) NOT NULL,
				PFR_ID_REF INTEGER NULL,	
				DEPARTMENT_ID INTEGER NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_PERFORMANCE_REVIEW PRIMARY KEY (PFR_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_PERFORMANCE_REVIEW(
				PFR_ID NUMBER(10) NOT NULL,	
				PFR_NAME VARCHAR2(255) NOT NULL,
				PFR_YEAR VARCHAR2(4) NOT NULL,
				PFR_ID_REF NUMBER(10) NULL,	
				DEPARTMENT_ID NUMBER(10) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_PERFORMANCE_REVIEW PRIMARY KEY (PFR_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_PERFORMANCE_REVIEW(
				PFR_ID INTEGER(10) NOT NULL,	
				PFR_NAME VARCHAR(255) NOT NULL,
				PFR_YEAR VARCHAR(4) NOT NULL,
				PFR_ID_REF INTEGER(10) NULL,	
				DEPARTMENT_ID INTEGER(10) NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_PERFORMANCE_REVIEW PRIMARY KEY (PFR_ID)) ";
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
				CONSTRAINT PK_PER_KPI PRIMARY KEY (KPI_ID)) ";
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
				CONSTRAINT PK_PER_KPI PRIMARY KEY (KPI_ID)) ";
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
				CONSTRAINT PK_PER_KPI PRIMARY KEY (KPI_ID)) ";
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
				CONSTRAINT PK_PER_JOB_FAMILY PRIMARY KEY (JF_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_JOB_FAMILY(
				JF_CODE VARCHAR2(2) NOT NULL,	
				JF_NAME VARCHAR2(100) NOT NULL,	
				JF_ACTIVE NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_JOB_FAMILY PRIMARY KEY (JF_CODE)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_JOB_FAMILY(
				JF_CODE VARCHAR(2) NOT NULL,	
				JF_NAME VARCHAR(100) NOT NULL,	
				JF_ACTIVE SMALLINT(1) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_JOB_FAMILY PRIMARY KEY (JF_CODE)) ";
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
				CONSTRAINT PK_PER_COMPETENCE PRIMARY KEY (CP_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_COMPETENCE(
				CP_CODE VARCHAR2(3) NOT NULL,	
				CP_NAME VARCHAR2(100) NOT NULL,	
				CP_MEANING VARCHAR2(1000) NULL,	
				CP_MODEL NUMBER(1) NOT NULL,
				CP_ACTIVE NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCE PRIMARY KEY (CP_CODE)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_COMPETENCE(
				CP_CODE VARCHAR(3) NOT NULL,	
				CP_NAME VARCHAR(100) NOT NULL,	
				CP_MEANING TEXT NULL,	
				CP_MODEL SMALLINT(1) NOT NULL,
				CP_ACTIVE SMALLINT(1) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCE PRIMARY KEY (CP_CODE)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_JOB_COMPETENCE(
				JF_CODE VARCHAR(2) NOT NULL,	
				CP_CODE VARCHAR(3) NOT NULL,	
				JC_ACTIVE SINGLE NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_JOB_COMPETENCE PRIMARY KEY (JF_CODE, CP_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_JOB_COMPETENCE(
				JF_CODE VARCHAR2(2) NOT NULL,	
				CP_CODE VARCHAR2(3) NOT NULL,	
				JC_ACTIVE NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_JOB_COMPETENCE PRIMARY KEY (JF_CODE, CP_CODE)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_JOB_COMPETENCE(
				JF_CODE VARCHAR(2) NOT NULL,	
				CP_CODE VARCHAR(3) NOT NULL,	
				JC_ACTIVE SMALLINT(1) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_JOB_COMPETENCE PRIMARY KEY (JF_CODE, CP_CODE)) ";
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
				CONSTRAINT PK_PER_COMPETENCE_LEVEL PRIMARY KEY (CP_CODE, CL_NO)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_COMPETENCE_LEVEL(
				CP_CODE VARCHAR2(3) NOT NULL,	
				CL_NO NUMBER(1) NOT NULL,	
				CL_NAME VARCHAR2(255) NOT NULL,	
				CL_MEANING VARCHAR2(1000) NULL,	
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCE_LEVEL PRIMARY KEY (CP_CODE, CL_NO)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_COMPETENCE_LEVEL(
				CP_CODE VARCHAR(3) NOT NULL,	
				CL_NO SMALLINT(1) NOT NULL,	
				CL_NAME VARCHAR(255) NOT NULL,	
				CL_MEANING TEXT NULL,	
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_COMPETENCE_LEVEL PRIMARY KEY (CP_CODE, CL_NO)) ";
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
				CONSTRAINT PK_PER_POSITION_COMPETENCE PRIMARY KEY (POS_ID, CP_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_POSITION_COMPETENCE(
				POS_ID NUMBER(10) NOT NULL,	
				CP_CODE VARCHAR2(3) NOT NULL,
				PC_TARGET_LEVEL NUMBER(1) NOT NULL,
				DEPARTMENT_ID NUMBER(10) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_POSITION_COMPETENCE PRIMARY KEY (POS_ID, CP_CODE)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_POSITION_COMPETENCE(
				POS_ID INTEGER(10) NOT NULL,	
				CP_CODE VARCHAR(3) NOT NULL,
				PC_TARGET_LEVEL SMALLINT(1) NOT NULL,
				DEPARTMENT_ID INTEGER(10) NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_POSITION_COMPETENCE PRIMARY KEY (POS_ID, CP_CODE)) ";
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
						$cmd = " UPDATE PER_POSITION_COMPETENCE SET DEPARTMENT_ID = $ORG_ID_REF 
										  WHERE POS_ID = $POS_ID ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
					} // end while						
				} elseif($DPISDB=="oci8"){
					$cmd = " UPDATE PER_POSITION_COMPETENCE A SET A.DEPARTMENT_ID = 
									  (SELECT D.ORG_ID_REF FROM PER_POSITION B, PER_ORG C, PER_ORG D 
									  WHERE A.POS_ID = B.POS_ID AND B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
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
				CONSTRAINT PK_PER_KPI_FORM PRIMARY KEY (KF_ID)) ";
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
				CONSTRAINT PK_PER_KPI_FORM PRIMARY KEY (KF_ID)) ";
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
				CONSTRAINT PK_PER_KPI_FORM PRIMARY KEY (KF_ID)) ";
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
					$cmd = " UPDATE PER_KPI_FORM X SET X.DEPARTMENT_ID = (SELECT D.ORG_ID_REF 
									  FROM PER_PERSONAL A, PER_POSITION B, PER_ORG C, PER_ORG D WHERE A.PER_ID = X.PER_ID AND 
									  A.POS_ID = B.POS_ID AND B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
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
				CONSTRAINT PK_PER_PERFORMANCE_GOALS PRIMARY KEY (PG_ID)) ";
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
				CONSTRAINT PK_PER_PERFORMANCE_GOALS PRIMARY KEY (PG_ID)) ";
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
				CONSTRAINT PK_PER_PERFORMANCE_GOALS PRIMARY KEY (PG_ID)) ";
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
				CONSTRAINT PK_PER_KPI_COMPETENCE PRIMARY KEY (KC_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_KPI_COMPETENCE(
				KC_ID NUMBER(10) NOT NULL,	
				KF_ID NUMBER(10) NOT NULL,	
				CP_CODE VARCHAR2(3) NOT NULL,	
				PC_TARGET_LEVEL NUMBER(1) NOT NULL,
				KC_EVALUATE NUMBER(1) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_KPI_COMPETENCE PRIMARY KEY (KC_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_KPI_COMPETENCE(
				KC_ID INTEGER(10) NOT NULL,	
				KF_ID INTEGER(10) NOT NULL,	
				CP_CODE VARCHAR(3) NOT NULL,	
				PC_TARGET_LEVEL SMALLINT(1) NOT NULL,
				KC_EVALUATE SMALLINT(1) NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_KPI_COMPETENCE PRIMARY KEY (KC_ID)) ";
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
				CONSTRAINT PK_PER_IPIP PRIMARY KEY (IPIP_ID)) ";
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
				CONSTRAINT PK_PER_IPIP PRIMARY KEY (IPIP_ID)) ";
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
				CONSTRAINT PK_PER_IPIP PRIMARY KEY (IPIP_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ORG ADD ORG_WEBSITE VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORG ADD ORG_WEBSITE VARCHAR2(255) NULL ";
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

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ORG_ASS ADD ORG_WEBSITE VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORG_ASS ADD ORG_WEBSITE VARCHAR2(255) NULL ";
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
				$cmd = " ALTER TABLE PER_ORG_ASS MODIFY ORG_SHORT VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SERVICETITLE ADD SV_CODE VARCHAR(2) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SERVICETITLE ADD SV_CODE VARCHAR2(2) NULL ";
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
				$cmd = " ALTER TABLE PER_SERVICETITLE MODIFY SRT_NAME TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMDTL ALTER CMD_NOTE1 MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL MODIFY CMD_NOTE1 VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMDTL MODIFY CMD_NOTE1 TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMDTL ALTER CMD_NOTE2 MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL MODIFY CMD_NOTE2 VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMDTL MODIFY CMD_NOTE2 TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG1 VARCHAR(1000) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG1 VARCHAR2(1000) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITION ALTER POS_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITION MODIFY POS_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITION MODIFY POS_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " UPDATE PER_POSITIONHIS, PER_ORG SET PER_POSITIONHIS.POH_ORG1 = PER_ORG.ORG_NAME 
								  WHERE PER_ORG.ORG_ID = PER_POSITIONHIS.ORG_ID_1 AND 
								  PER_POSITIONHIS.POH_ORG1 IS NULL AND PER_POSITIONHIS.ORG_ID_1 <> 1 ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG1 = (SELECT ORG_NAME FROM PER_ORG 
								  WHERE ORG_ID_1 = PER_ORG.ORG_ID) WHERE POH_ORG1 IS NULL AND ORG_ID_1 != 1 ";
			elseif($DPISDB=="mysql")
				$cmd = " UPDATE PER_POSITIONHIS, PER_ORG SET PER_POSITIONHIS.POH_ORG1 = PER_ORG.ORG_NAME
								  WHERE PER_ORG.ORG_ID = PER_POSITIONHIS.ORG_ID_1 AND 
								  PER_POSITIONHIS.POH_ORG1 IS NULL AND PER_POSITIONHIS.ORG_ID_1 != 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG2 VARCHAR(1000) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG2 VARCHAR2(1000) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " UPDATE PER_POSITIONHIS, PER_ORG SET PER_POSITIONHIS.POH_ORG2 = PER_ORG.ORG_NAME
								  WHERE PER_ORG.ORG_ID = PER_POSITIONHIS.ORG_ID_2 AND 
								  PER_POSITIONHIS.POH_ORG2 IS NULL AND PER_POSITIONHIS.ORG_ID_2 <> 1 ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG2 = (SELECT ORG_NAME FROM PER_ORG 
								  WHERE ORG_ID_2 = PER_ORG.ORG_ID) WHERE POH_ORG2 IS NULL AND ORG_ID_2 != 1 ";
			elseif($DPISDB=="mysql")
				$cmd = " UPDATE PER_POSITIONHIS, PER_ORG SET PER_POSITIONHIS.POH_ORG2 = PER_ORG.ORG_NAME
								  WHERE PER_ORG.ORG_ID = PER_POSITIONHIS.ORG_ID_2 AND 
								  PER_POSITIONHIS.POH_ORG2 IS NULL AND PER_POSITIONHIS.ORG_ID_2 != 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG3 VARCHAR(1000) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG3 VARCHAR2(1000) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " UPDATE PER_POSITIONHIS, PER_ORG SET PER_POSITIONHIS.POH_ORG3 = PER_ORG.ORG_NAME
								  WHERE PER_ORG.ORG_ID = PER_POSITIONHIS.ORG_ID_3 AND 
								  PER_POSITIONHIS.POH_ORG3 IS NULL AND PER_POSITIONHIS.ORG_ID_3 <> 1 ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_POSITIONHIS SET POH_ORG3 = (SELECT ORG_NAME FROM PER_ORG 
								  WHERE ORG_ID_3 = PER_ORG.ORG_ID) WHERE POH_ORG3 IS NULL AND ORG_ID_3 != 1 ";
			elseif($DPISDB=="mysql")
				$cmd = " UPDATE PER_POSITIONHIS, PER_ORG SET PER_POSITIONHIS.POH_ORG3 = PER_ORG.ORG_NAME 
								  WHERE PER_ORG.ORG_ID = PER_POSITIONHIS.ORG_ID_3 AND 
								  PER_POSITIONHIS.POH_ORG3 IS NULL AND PER_POSITIONHIS.ORG_ID_3 != 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ALTER ORG_ID_1 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY ORG_ID_1 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY ORG_ID_1 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ALTER ORG_ID_2 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY ORG_ID_2 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY ORG_ID_2 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ALTER ORG_ID_3 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY ORG_ID_3 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY ORG_ID_3 INTEGER(10) NULL ";
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
					$cmd = " UPDATE PER_POSITION A SET A.DEPARTMENT_ID = (SELECT D.ORG_ID_REF 
									  FROM PER_ORG C, PER_ORG D WHERE A.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
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
					$cmd = " UPDATE PER_POS_EMP A SET A.DEPARTMENT_ID = (SELECT D.ORG_ID_REF 
									  FROM PER_ORG C, PER_ORG D WHERE A.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
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
					$cmd = " UPDATE PER_POS_EMPSER A SET A.DEPARTMENT_ID = (SELECT D.ORG_ID_REF 
									  FROM PER_ORG C, PER_ORG D WHERE A.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
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
					$cmd = " UPDATE PER_SALQUOTADTL1 A SET A.DEPARTMENT_ID = (SELECT D.ORG_ID_REF 
									  FROM PER_ORG C, PER_ORG D WHERE A.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
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
					$cmd = " UPDATE PER_SALQUOTADTL2 A SET A.DEPARTMENT_ID = (SELECT D.ORG_ID_REF 
									  FROM PER_ORG C, PER_ORG D WHERE A.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
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
					$cmd = " UPDATE PER_SALPROMOTE X SET X.DEPARTMENT_ID = (SELECT D.ORG_ID_REF 
									  FROM PER_PERSONAL A, PER_POSITION B, PER_ORG C, PER_ORG D WHERE A.PER_ID = X.PER_ID AND 
									  A.POS_ID = B.POS_ID AND B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
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
					$cmd = " SELECT BONUS_YEAR, BONUS_TYPE, ORG_ID FROM PER_BONUSQUOTADTL1 
									  WHERE ORG_ID IS NOT NULL ";
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
					$cmd = " UPDATE PER_BONUSQUOTADTL1 A SET A.DEPARTMENT_ID = (SELECT D.ORG_ID_REF 
									  FROM PER_ORG C, PER_ORG D WHERE A.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
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
					$cmd = " SELECT BONUS_YEAR, BONUS_TYPE, ORG_ID FROM PER_BONUSQUOTADTL2 
									  WHERE ORG_ID IS NOT NULL ";
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
					$cmd = " UPDATE PER_BONUSQUOTADTL2 A SET A.DEPARTMENT_ID = (SELECT D.ORG_ID_REF 
									  FROM PER_ORG C, PER_ORG D WHERE A.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
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
					$cmd = " UPDATE PER_BONUSPROMOTE X SET X.DEPARTMENT_ID = (SELECT D.ORG_ID_REF 
									  FROM PER_PERSONAL A, PER_POSITION B, PER_ORG C, PER_ORG D WHERE A.PER_ID = X.PER_ID AND 
									  A.POS_ID = B.POS_ID AND B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
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
					$cmd = " UPDATE PER_MOVE_REQ X SET X.DEPARTMENT_ID = (SELECT D.ORG_ID_REF 
									  FROM PER_PERSONAL A, PER_POSITION B, PER_ORG C, PER_ORG D WHERE A.PER_ID = X.PER_ID AND 
									  A.POS_ID = B.POS_ID AND B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
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
					$cmd = " UPDATE PER_PROMOTE_C X SET X.DEPARTMENT_ID = (SELECT D.ORG_ID_REF 
									  FROM PER_PERSONAL A, PER_POSITION B, PER_ORG C, PER_ORG D WHERE A.PER_ID = X.PER_ID AND 
									  A.POS_ID = B.POS_ID AND B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
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
					$cmd = " UPDATE PER_PROMOTE_E X SET X.DEPARTMENT_ID = (SELECT D.ORG_ID_REF 
									  FROM PER_PERSONAL A, PER_POSITION B, PER_ORG C, PER_ORG D WHERE A.PER_ID = X.PER_ID AND 
									  A.POS_ID = B.POS_ID AND B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
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
					$cmd = " UPDATE PER_PROMOTE_P X SET X.DEPARTMENT_ID = (SELECT D.ORG_ID_REF 
									  FROM PER_PERSONAL A, PER_POSITION B, PER_ORG C, PER_ORG D WHERE A.PER_ID = X.PER_ID AND 
									  A.POS_ID = B.POS_ID AND B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
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
					$cmd = " UPDATE PER_LETTER X SET X.DEPARTMENT_ID = (SELECT D.ORG_ID_REF 
									  FROM PER_PERSONAL A, PER_POSITION B, PER_ORG C, PER_ORG D WHERE A.PER_ID = X.PER_ID AND 
									  A.POS_ID = B.POS_ID AND B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
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
					$cmd = " UPDATE PER_SCHOLAR X SET X.DEPARTMENT_ID = (SELECT D.ORG_ID_REF 
									  FROM PER_PERSONAL A, PER_POSITION B, PER_ORG C, PER_ORG D WHERE A.PER_ID = X.PER_ID AND 
									  A.POS_ID = B.POS_ID AND B.ORG_ID = C.ORG_ID AND C.ORG_ID = D.ORG_ID) ";
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

			$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK11_PER_POSITION 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POS_EMP ADD CONSTRAINT FK5_PER_POS_EMP 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POS_EMPSER ADD CONSTRAINT FK5_PER_POS_EMPSER 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALQUOTA ADD CONSTRAINT FK1_PER_SALQUOTA 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALQUOTADTL1 ADD CONSTRAINT FK3_PER_SALQUOTADTL1 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALQUOTADTL2 ADD CONSTRAINT FK3_PER_SALQUOTADTL2 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALPROMOTE ADD CONSTRAINT FK3_PER_SALPROMOTE 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_BONUSQUOTA ADD CONSTRAINT FK1_PER_BONUSQUOTA 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_BONUSQUOTADTL1 ADD CONSTRAINT FK3_PER_BONUSQUOTADTL1 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_BONUSQUOTADTL2 ADD CONSTRAINT FK3_PER_BONUSQUOTADTL2 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_BONUSPROMOTE ADD CONSTRAINT FK3_PER_BONUSPROMOTE 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD CONSTRAINT FK17_PER_TRANSFER_REQ 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_MOVE_REQ ADD CONSTRAINT FK11_PER_MOVE_REQ 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_PROMOTE_C ADD CONSTRAINT FK2_PER_PROMOTE_C 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_PROMOTE_E ADD CONSTRAINT FK3_PER_PROMOTE_E 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_PROMOTE_P ADD CONSTRAINT FK3_PER_PROMOTE_P 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_COMMAND ADD CONSTRAINT FK2_PER_COMMAND 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_LETTER ADD CONSTRAINT FK3_PER_LETTER 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_DECOR ADD CONSTRAINT FK1_PER_DECOR 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_COURSE ADD CONSTRAINT FK4_PER_COURSE 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SCHOLAR ADD CONSTRAINT FK8_PER_SCHOLAR 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_INVEST1 ADD CONSTRAINT FK2_PER_INVEST1 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_INVEST2 ADD CONSTRAINT FK2_PER_INVEST2 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
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
				CONSTRAINT PK_PER_EMPSER_POS_NAME PRIMARY KEY (EP_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_EMPSER_POS_NAME(
				EP_CODE VARCHAR2(10) NOT NULL,	
				EP_NAME VARCHAR2(100) NOT NULL,	
				LEVEL_NO VARCHAR2(2) NOT NULL,
				EP_DECOR NUMBER(1) NOT NULL,		
				EP_ACTIVE NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_EMPSER_POS_NAME PRIMARY KEY (EP_CODE)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_EMPSER_POS_NAME(
				EP_CODE VARCHAR(10) NOT NULL,	
				EP_NAME VARCHAR(100) NOT NULL,	
				LEVEL_NO VARCHAR(2) NOT NULL,
				EP_DECOR SMALLINT(1) NOT NULL,		
				EP_ACTIVE SMALLINT(1) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_EMPSER_POS_NAME PRIMARY KEY (EP_CODE)) ";
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
				CONSTRAINT PK_PER_POS_EMPSER PRIMARY KEY (POEMS_ID)) ";
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
				CONSTRAINT PK_PER_POS_EMPSER PRIMARY KEY (POEMS_ID)) ";
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
				CONSTRAINT PK_PER_POS_EMPSER PRIMARY KEY (POEMS_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD POEMS_ID INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD POEMS_ID NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERSONAL ADD POEMS_ID INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_HIP_FLAG VARCHAR(20) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_HIP_FLAG VARCHAR2(20) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_PERSONAL SET PER_HIP_FLAG = NULL WHERE PER_HIP_FLAG = ' ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_PERSONAL SET PER_CERT_OCC = NULL WHERE PER_CERT_OCC = ' ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_CERT_OCC VARCHAR(20) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_CERT_OCC VARCHAR2(20) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD TR_POSDATE VARCHAR(19) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRANSFER_REQ ADD TR_POSDATE VARCHAR2(19) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_CONTROL set CTRL_ALTER = 2 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if($CTRL_ALTER < 2) 

		if($CTRL_ALTER < 3) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_OCCUPATION(
				OC_CODE VARCHAR(3) NOT NULL,	
				OC_NAME VARCHAR(100) NOT NULL,
				OC_ACTIVE SINGLE NOT NULL,		
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_OCCUPATION PRIMARY KEY (OC_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_OCCUPATION(
				OC_CODE VARCHAR2(3) NOT NULL,	
				OC_NAME VARCHAR2(100) NOT NULL,
				OC_ACTIVE NUMBER(1) NOT NULL,		
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_OCCUPATION PRIMARY KEY (OC_CODE)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_OCCUPATION(
				OC_CODE VARCHAR(3) NOT NULL,	
				OC_NAME VARCHAR(100) NOT NULL,
				OC_ACTIVE SMALLINT(1) NOT NULL,		
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_OCCUPATION PRIMARY KEY (OC_CODE)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_PARENT (
				PER_ID INTEGER NOT NULL,
				PER_GENDER SINGLE NOT NULL,
				PER_CARDNO VARCHAR(13) NULL,
				PER_BIRTHDATE VARCHAR(19) NULL,
				PER_ALIVE SINGLE NULL,
				RE_CODE VARCHAR(2) NULL,
				OC_CODE VARCHAR(2) NULL,
				OC_OTHER VARCHAR(100) NULL,
				PARENT_TYPE SINGLE NULL,
				DOC_TYPE SINGLE NULL,
				DOC_NO VARCHAR(20) NULL,
				DOC_DATE VARCHAR(19) NULL,
				MR_CODE VARCHAR(2) NULL,
				MR_DOC_TYPE SINGLE NULL,
				MR_DOC_NO VARCHAR(20) NULL,
				MR_DOC_DATE VARCHAR(19) NULL,
				MR_DOC_PV_CODE VARCHAR(10) NULL,
				PV_CODE VARCHAR(10) NULL,
				POST_CODE VARCHAR(5) NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,
				CONSTRAINT PK_PER_PARENT PRIMARY KEY (PER_ID, PER_GENDER)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_PARENT (
				PER_ID NUMBER(10) NOT NULL,
				PER_GENDER NUMBER(1) NOT NULL,
				PER_CARDNO VARCHAR2(13) NULL,
				PER_BIRTHDATE VARCHAR2(19) NULL,
				PER_ALIVE NUMBER(1) NULL,
				RE_CODE VARCHAR2(2) NULL,
				OC_CODE VARCHAR2(2) NULL,
				OC_OTHER VARCHAR2(100) NULL,
				PARENT_TYPE NUMBER(1) NULL,
				DOC_TYPE NUMBER(1) NULL,
				DOC_NO VARCHAR2(20) NULL,
				DOC_DATE VARCHAR2(19) NULL,
				MR_CODE VARCHAR2(2) NULL,
				MR_DOC_TYPE NUMBER(1) NULL,
				MR_DOC_NO VARCHAR2(20) NULL,
				MR_DOC_DATE VARCHAR2(19) NULL,
				MR_DOC_PV_CODE VARCHAR2(10) NULL,
				PV_CODE VARCHAR2(10) NULL,
				POST_CODE VARCHAR2(5) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,
				CONSTRAINT PK_PER_PARENT PRIMARY KEY (PER_ID, PER_GENDER)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_PARENT (
				PER_ID INTEGER(10) NOT NULL,
				PER_GENDER SMALLINT(1) NOT NULL,
				PER_CARDNO VARCHAR(13) NULL,
				PER_BIRTHDATE VARCHAR(19) NULL,
				PER_ALIVE SMALLINT(1) NULL,
				RE_CODE VARCHAR(2) NULL,
				OC_CODE VARCHAR(2) NULL,
				OC_OTHER VARCHAR(100) NULL,
				PARENT_TYPE SMALLINT(1) NULL,
				DOC_TYPE SMALLINT(1) NULL,
				DOC_NO VARCHAR(20) NULL,
				DOC_DATE VARCHAR(19) NULL,
				MR_CODE VARCHAR(2) NULL,
				MR_DOC_TYPE SMALLINT(1) NULL,
				MR_DOC_NO VARCHAR(20) NULL,
				MR_DOC_DATE VARCHAR(19) NULL,
				MR_DOC_PV_CODE VARCHAR(10) NULL,
				PV_CODE VARCHAR(10) NULL,
				POST_CODE VARCHAR(5) NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,
				CONSTRAINT PK_PER_PARENT PRIMARY KEY (PER_ID, PER_GENDER)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_CHILD (
				CHILD_ID INTEGER NOT NULL,
				PER_ID INTEGER NOT NULL,
				PN_CODE VARCHAR(3) NULL,
				CHILD_NAME VARCHAR(100) NOT NULL,
				CHILD_SURNAME VARCHAR(100) NOT NULL,
				CARDNO VARCHAR(13) NULL,
				GENDER SINGLE NULL,
				BIRTHDATE VARCHAR(19) NULL,
				ALIVE_FLAG SINGLE NULL,
				RE_CODE VARCHAR(2) NULL,
				OC_CODE VARCHAR(2) NULL,
				OC_OTHER VARCHAR(100) NULL,
				CHILD_TYPE SINGLE NULL,
				TYPE_OTHER VARCHAR(100) NULL,
				DOC_TYPE SINGLE NULL,
				DOC_NO VARCHAR(20) NULL,
				DOC_DATE VARCHAR(19) NULL,
				MR_CODE VARCHAR(2) NULL,
				MR_DOC_TYPE SINGLE NULL,
				MR_DOC_NO VARCHAR(20) NULL,
				MR_DOC_DATE VARCHAR(19) NULL,
				PV_CODE VARCHAR(10) NULL,
				POST_CODE VARCHAR(5) NULL,
				INCOMPETENT SINGLE NULL,
				IN_DOC_TYPE SINGLE NULL,
				IN_DOC_NO VARCHAR(20) NULL,
				IN_DOC_DATE VARCHAR(19) NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,
				CONSTRAINT PK_PER_CHILD PRIMARY KEY (CHILD_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_CHILD (
				CHILD_ID NUMBER(10) NOT NULL,
				PER_ID NUMBER(10) NOT NULL,
				PN_CODE VARCHAR2(3) NULL,
				CHILD_NAME VARCHAR2(100) NOT NULL,
				CHILD_SURNAME VARCHAR2(100) NOT NULL,
				CARDNO VARCHAR2(13) NULL,
				GENDER NUMBER(1) NULL,
				BIRTHDATE VARCHAR2(19) NULL,
				ALIVE_FLAG NUMBER(1) NULL,
				RE_CODE VARCHAR2(2) NULL,
				OC_CODE VARCHAR2(2) NULL,
				OC_OTHER VARCHAR2(100) NULL,
				CHILD_TYPE NUMBER(1) NULL,
				TYPE_OTHER VARCHAR2(100) NULL,
				DOC_TYPE NUMBER(1) NULL,
				DOC_NO VARCHAR2(20) NULL,
				DOC_DATE VARCHAR2(19) NULL,
				MR_CODE VARCHAR2(2) NULL,
				MR_DOC_TYPE NUMBER(1) NULL,
				MR_DOC_NO VARCHAR2(20) NULL,
				MR_DOC_DATE VARCHAR2(19) NULL,
				PV_CODE VARCHAR2(10) NULL,
				POST_CODE VARCHAR2(5) NULL,
				INCOMPETENT NUMBER(1) NULL,
				IN_DOC_TYPE NUMBER(1) NULL,
				IN_DOC_NO VARCHAR2(20) NULL,
				IN_DOC_DATE VARCHAR2(19) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,
				CONSTRAINT PK_PER_CHILD PRIMARY KEY (CHILD_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_CHILD (
				CHILD_ID INTEGER(10) NOT NULL,
				PER_ID INTEGER(10) NOT NULL,
				PN_CODE VARCHAR(3) NULL,
				CHILD_NAME VARCHAR(100) NOT NULL,
				CHILD_SURNAME VARCHAR(100) NOT NULL,
				CARDNO VARCHAR(13) NULL,
				GENDER SMALLINT(1) NULL,
				BIRTHDATE VARCHAR(19) NULL,
				ALIVE_FLAG SMALLINT(1) NULL,
				RE_CODE VARCHAR(2) NULL,
				OC_CODE VARCHAR(2) NULL,
				OC_OTHER VARCHAR(100) NULL,
				CHILD_TYPE SMALLINT(1) NULL,
				TYPE_OTHER VARCHAR(100) NULL,
				DOC_TYPE SMALLINT(1) NULL,
				DOC_NO VARCHAR(20) NULL,
				DOC_DATE VARCHAR(19) NULL,
				MR_CODE VARCHAR(2) NULL,
				MR_DOC_TYPE SMALLINT(1) NULL,
				MR_DOC_NO VARCHAR(20) NULL,
				MR_DOC_DATE VARCHAR(19) NULL,
				PV_CODE VARCHAR(10) NULL,
				POST_CODE VARCHAR(5) NULL,
				INCOMPETENT SMALLINT(1) NULL,
				IN_DOC_TYPE SMALLINT(1) NULL,
				IN_DOC_NO VARCHAR(20) NULL,
				IN_DOC_DATE VARCHAR(19) NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,
				CONSTRAINT PK_PER_CHILD PRIMARY KEY (CHILD_ID)) ";
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
				$cmd = " ALTER TABLE PER_CONTROL ADD CTRL_TYPE SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_CONTROL ADD CTRL_TYPE NUMBER(1) NULL";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_CONTROL ADD CTRL_TYPE SMALLINT(1) NULL";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_CONTROL ADD PV_CODE VARCHAR(10) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_CONTROL ADD PV_CODE VARCHAR2(10) NULL ";
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
				$cmd = " ALTER TABLE PER_CONTROL MODIFY ORG_ID INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_OFF_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('08', '��ѡ�ҹ�Ҫ��÷����', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_OFF_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('09', '��ѡ�ҹ�Ҫ��þ����', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_OFF_TYPE (OT_CODE, OT_NAME, OT_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('10', '�١��ҧ���Ǥ���', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('230', '����������͹�дѺ��ѡ�ҹ�Ҫ���', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('23010', '����͹�дѺ��', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('23020', '����͹�дѺ����', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('23030', '���ѭ�Ҩ�ҧ�����á', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('23040', '����ѭ��', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

	// ��������Ǫҭ�����
			$code = array(	"001", "002", "003", "004", "005", "006", "007", "008", "009", "010", "011", "012", "013", "014", "015", "016", "017", 
											"018", "019", "020", "021", "022" );
			$desc = array(	"��ҹ����֡��", "��ҹ���ᾷ������Ҹ�ó�آ", "��ҹ�ɵ�", "��ҹ��Ѿ�ҡø����ҵ��������Ǵ����", "��ҹ�Է����ʵ�����෤�����", 
											"��ҹ���ǡ���", "��ҹʶһѵ¡���", "��ҹ����Թ ��ä�ѧ ������ҳ", "��ҹ�ѧ��", "��ҹ������", "��ҹ��û���ͧ ������ͧ", 
											"��ҹ��Ż�Ѳ����������ʹ�", "��ҹ����ҧἹ�Ѳ��", "��ҹ�ҳԪ����к�ԡ��", "��ҹ������蹤�", "��ҹ��ú����èѴ�����к����ø�áԨ", 
											"��ҹ��û�Ъ�����ѹ��", "��ҹ��ä��Ҥ���С���������", "��ҹ��ѧ�ҹ", "��ҹ��ҧ�����", "��ҹ�ص��ˡ���", "��ҹ�Ըա��" );
			for ( $i=0; $i<count($code); $i++ ) { 
				$cmd = " INSERT INTO PER_SPECIAL_SKILLGRP (SS_CODE, SS_NAME, SS_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('$code[$i]', '$desc[$i]', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end for

	// ���ö������С�����ҹ
			$code = array(	"01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18" );
			$desc = array(	"������ҹʹѺʹع�����", "������ҹʹѺʹع�ҹ��ѡ�ҧ෤�Ԥ੾�д�ҹ", "������ҹ���ӻ�֡��", "������ҹ������", 
											"������ҹ��º������ҧἹ", "������ҹ�֡���Ԩ����оѲ��", "������ҹ���ǡ�ͧ����׺�ǹ", "������ҹ�͡Ẻ���;Ѳ��", 
											"������ҹ��������ѹ�������ҧ�����", "������ҹ�ѧ�Ѻ�顯����", "������ҹ������Ъ�����ѹ��", "������ҹ��������������", 
											"������ҹ��ԡ�û�ЪҪ���ҹ�آ�Ҿ������ʴ��Ҿ", "������ҹ��ԡ�û�ЪҪ��ҧ��Ż�Ѳ�����", 
											"������ҹ��ԡ�û�ЪҪ��ҧ෤�Ԥ੾�д�ҹ", "������ҹ�͡����Ҫ�����з���¹", "������ҹ��û���ͧ", "������ҹ͹��ѡ��" );
			for ( $i=0; $i<count($code); $i++ ) { 
				$cmd = " INSERT INTO PER_JOB_FAMILY (JF_CODE, JF_NAME, JF_ACTIVE, UPDATE_USER, UPDATE_DATE)
								  VALUES ('$code[$i]', '$desc[$i]', 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end for

			if($SCOMPETENCY[$PER_ID_DEPARTMENT_ID]!="Y"){
				$code = array(	"101", "102", "103", "104", "105", "201", "202", "203", "301", "302", "303", "304", "305", "306", "307", 
										"308", "309", "310", "311", "312", "313", "314", "315", "316", "317", "318", "319", "320" );
				$desc = array(	"�����觼����ķ���", "��ԡ�÷���", "����������������Ǫҭ㹧ҹ�Ҫվ", "���¸���", "���������ç�����", 
										"����ҧἹ��С�èѴ�к��ҹ", "��þѲ�Ҽ����ѧ�Ѻ�ѭ��", "�����繼���", "��äԴ��������", "����ͧ�Ҿͧ�����", 
										"��þѲ���ѡ��Ҿ��", "�����觡�õ���ӹҨ˹�ҷ��", "����׺�����Ң�����", "�������㨢��ᵡ��ҧ�ҧ�Ѳ�����", "�������㨼�����", 
										"��������ͧ�������к��Ҫ���", "��ô��Թ����ԧ�ء", "�����١��ͧ�ͧ�ҹ", "��������㹵��ͧ", "�����״���蹼�͹�ù", 
										"��ŻС��������è٧�", "����м���", "�ع�����Ҿ�ҧ��Ż�", "����·�ȹ�", "����ҧ���ط���Ҥ�Ѱ", "�ѡ��Ҿ���͹ӡ�û�Ѻ����¹", 
										"��äǺ������ͧ", "�������ӹҨ�������" );
//				$meaning = array(	"NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", 
//										"NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", "NULL", 
//										"NULL", "NULL", "NULL" );
			} else {
				$code = array(	"101", "102", "103", "104", "105", "201", "202", "203", "204", "205", "206", "301", "302", "303", "304", "305", "306", 
										"307", "308", "309", "310", "311", "312", "313", "314", "315", "316", "317", "318" );
				$desc = array(	"�����觼����ķ���", "��ԡ�÷���", "����������������Ǫҭ㹧ҹ�Ҫվ", "���¸���", "���������ç�����", 
										"����м���", "����·�ȹ�", "����ҧ���ط���Ҥ�Ѱ", "�ѡ��Ҿ���͹ӡ�û�Ѻ����¹", "��äǺ������ͧ", "����͹�ҹ�������ӹҨ�������",  
										"�����䢻ѭ��Ẻ����Ҫվ", "��������������С�����ҧ����ѹ��", "��áӡѺ�Դ������ҧ��������", "��ä�����С�ú����èѴ��â����Ť������",
										"��äԴ����������к�óҡ��", "��ê�ҧ�ѧࡵ", "��ô��Թ����ԧ�ء", "��ú����÷�Ѿ�ҡ�", "����ִ������ѡࡳ��", 
										"����ҧἹ��Ф��������к��ҹ", "������ҧ����Դ�������ǹ����㹷ء�Ҥ��ǹ", "������ҧ�������ѹ��", "����������������Ǩ٧�", 
										"�������㨤�����״���蹵��ʶҹ��ó�", "�����Դ���ҧ��ä�", "���������ͧ�������к��ҹ", "����ҧἹ�ҹ��ǧ˹��", 
										"�����١��ͧ�ͧ�ҹ" );
				$meaning = array(	"���������蹨л�Ժѵ��Ҫ���������������Թ�ҵðҹ��������� ���ҵðҹ����Ҩ�繼š�û�Ժѵԧҹ����ҹ�Ңͧ���ͧ ����ࡳ���Ѵ�����ķ�������ǹ�Ҫ��á�˹���� �ա����ѧ��������֧������ҧ��ä�Ѳ�Ҽŧҹ���͡�кǹ��û�Ժѵԧҹ���������·���ҡ��з�ҷ�ª�Դ����Ҩ������ռ�������ö��з����ҡ�͹", "���ö�й���鹤���������Ф����������ͧ����Ҫ���㹡������ԡ������ʹͧ������ͧ��âͧ��ЪҪ���ʹ���ͧ˹��§ҹ�Ҥ�Ѱ���� �������Ǣ�ͧ", "�����ǹ���� ʹ������ ����������Ѳ���ѡ��Ҿ ��������������ö�ͧ��㹡�û�Ժѵ��Ҫ��� ���¡���֡�� �鹤����Ҥ������ �Ѳ�ҵ��ͧ���ҧ������ͧ �ա������ѡ�Ѳ�� ��Ѻ��ا ����ء�����������ԧ�Ԫҡ�����෤����յ�ҧ� ��ҡѺ��û�Ժѵԧҹ����Դ�����ķ���", "��ä�ͧ����л�оĵԻ�ԺѵԶ١��ͧ���������駵����ѡ��������Фس�������¸��� ��ʹ����ѡ�Ƿҧ��ԪҪվ�ͧ������觻���ª��ͧ����Ȫҵ��ҡ���һ���ª����ǹ��  ��駹�����͸�ç�ѡ���ѡ����������Ҫվ����Ҫ��� �ա��������繡��ѧ�Ӥѭ㹡��ʹѺʹع��ѡ�ѹ�����áԨ��ѡ�Ҥ�Ѱ�����������·���˹����", "���ö�й���鹷�� 1) �������㨷��зӧҹ�����Ѻ������ ����ǹ˹��㹷���ҹ ˹��§ҹ ����ͧ��� �¼�黯Ժѵ��հҹ�����Ҫԡ㹷�� ����㹰ҹ����˹�ҷ�� ��� 2) ��������ö㹡�����ҧ��д�ç�ѡ������ѹ��Ҿ�Ѻ��Ҫԡ㹷��", "�����������ͤ�������ö㹡���繼��Ӣͧ������� ����ͧ ����֧��á�˹���ȷҧ ����·�ȹ� ������� �Ըա�÷ӧҹ �������ѧ�Ѻ�ѭ�����ͷ���ҹ��Ժѵԧҹ�����ҧ�Һ��� �������Է���Ҿ��к�����ѵ�ػ��ʧ��ͧͧ���", "��������ö����ȷҧ���Ѵਹ��С�ͤ��������ç��������������ѧ�Ѻ�ѭ�����͹Ӿҧҹ�Ҥ�Ѱ����ش���������ѹ", "�������㨡��ط���Ҥ�Ѱ�������ö����ء����㹡�á�˹����ط��ͧ˹��§ҹ���� �¤�������ö㹡�û���ء��������֧��������ö㹡�äҴ��ó�֧��ȷҧ�к��Ҫ����͹Ҥ� ��ʹ���š�з��ͧʶҹ��ó������е�ҧ����ȷ���Դ���", "����������Ф�������ö㹡�á�е�鹼�ѡ�ѹ�����������Դ������ͧ��èл�Ѻ����¹���Ƿҧ����繻���ª�����Ҥ�Ѱ ����֧�������������������Ѻ��� ���� ��д��Թ�������û�Ѻ����¹����Դ��鹨�ԧ", "����ЧѺ��������оĵԡ����ѹ��������������Ͷ١������ ����༪ԭ˹�ҡѺ���µç���� ༪ԭ����������Ե� ���ͷӧҹ���������Ф������ѹ ����֧����ʹ��ʹ��������͵�ͧ���������ʶҹ��ó����ͤ������´���ҧ������ͧ", "�������㨨��������������¹������͡�þѲ�Ҽ������������� ����֧�����������㹤�������ö�ͧ������ �ѧ��鹨֧�ͺ�����ӹҨ���˹�ҷ���Ѻ�Դ�ͺ����������������������㹡�����ҧ��ä��Ըա�âͧ�����ͺ�����������㹧ҹ", "��������ö���������ѭ�����������繻ѭ�� ��������ŧ��ͨѴ��áѺ�ѭ�ҹ��� ���ҧ�բ����� ����ѡ��� �������ö�Ӥ�������Ǫҭ �����ǤԴ�����ԪҪվ�һ���ء����㹡����䢻ѭ�������ҧ�ջ���Է���Ҿ", "�վĵԡ������������ ��е��㨷��й����Իѭ�� ��ѵ���� ෤����� ��������Ǫҭ ���ͧ���������ҧ� �������� ʹѺʹع ��оѲ�Ҽ���Сͺ��� �������͢��� �Ǻ���仡Ѻ������ҧ �Ѳ�� ����ѡ�Ҥ�������ѹ���ѹ�աѺ����Сͺ��� �������͢��� ����������Сͺ��� �������͢��� �դ������ �������� �������ö �����Ѳ��˹��§ҹ����ջ���ª�� �ա����繡����������ҧ�մ��������ö㹡���觢ѹ���ҧ����׹", "ਵ�ҷ��СӡѺ���� ��еԴ�����ô��Թ�ҹ��ҧ� �ͧ�����蹷������Ǣ�ͧ��黯ԺѵԵ���ҵðҹ ������º ���͢�ͺѧ�Ѻ����˹���� ��������ӹҨ�������º ������ ���͵�����˹�˹�ҷ�������������ҧ�����������ջ���Է���Ҿ����觻���ª��ͧ˹��§ҹ ͧ��� ���ͻ���Ȫҵ����Ӥѭ", "��������ö㹡���׺���� ��������������੾����Ш� ���䢻����ȹ��«ѡ����������´ ������������Ң��Ƿ���仨ҡ��Ҿ�Ǵ�����ͺ����¤Ҵ����Ҩ�բ����ŷ����繻���ª������͹Ҥ� ��йӢ����ŷ�����ҹ���һ�������ШѴ������ҧ���к� �س�ѡɳй���Ҩ����֧����ʹ�����������ǡѺʶҹ��ó� ������ѧ ����ѵԤ������� ����� �ѭ�� ��������ͧ��ǵ�ҧ� �������Ǣ�ͧ���ͨ��繵�ͧҹ�˹�ҷ��", 
										"��������ö㹡�äԴ����������зӤ���������ԧ�ѧ������ ����֧����ͧ�Ҿ����ͧͧ��� �����繡�ͺ�����Դ�����ǤԴ���� �ѹ�繼��Ҩҡ�����ػ�ٻẺ ����ء���Ƿҧ��ҧ� �ҡʶҹ��ó����͢�������ҡ���� ��йҹҷ�ȹ�", "�վĵԡ���㹡�õ�駢��ʧ��� ����ѧࡵ �������԰ҹ��ҧ� ��ʹ���վĵԡ���㹡�ê�ҧ�ѧࡵ����Ǵ���� ��������¹�ŧ �����Դ���� ��Ф�����仵�ҧ� ���͹�令Ҵ��ó� �������� ��л����Թ�˵ء�ó� ����ͧ��� ʶҹ��ó� ������ �����觵�ҧ� ����Դ��������ҧ�١��ͧ", "��õ��˹ѡ�����������͡�����ͻѭ���ػ��ä����Ҩ�Դ����͹Ҥ� ����ҧἹ ŧ��͡�зӡ����������������ª��ҡ�͡�� ���ͻ�ͧ�ѹ�ѭ�� ��ʹ����ԡ�ԡĵԵ�ҧ� ������͡��", "��õ��˹ѡ���Ͷ֧����������������ҧ��Ѿ�ҡ� (������ҳ ���� ���ѧ������ͧ��� �ػ�ó� ���) ���ŧ�ع����ͷ�����û�Ժѵ���áԨ (Input) �Ѻ���Ѿ������ (Output) ��о�������Ѻ��ا����Ŵ��鹵͹��û�Ժѵԧҹ ���;Ѳ������û�Ժѵԧҹ�Դ���������������ջ���Է���Ҿ�٧�ش �Ҩ��������֧��������ö㹡�èѴ�����Ӥѭ㹡�������� ��Ѿ�ҡ� ��Т��������ҧ������� ��л����Ѵ���������٧�ش", "ਵ�ҷ��СӡѺ����������������˹��§ҹ��蹻�Ժѵ���������ҵðҹ ������º��ͺѧ�Ѻ����˹���� ��������ӹҨ�������º ������ ���͵����ѡ�Ƿҧ��ԪҪվ�ͧ��������������ҧ�����������ջ���Է���Ҿ����觻���ª��ͧͧ��� �ѧ�� ��л������������Ӥѭ ��������ö����Ҩ����֧����׹��Ѵ���觷��١��ͧ��Ф����索Ҵ㹡�èѴ��áѺ�ؤ������˹��§ҹ����ҽ׹��ࡳ�� ����º�����ҵðҹ��������", "��������ö㹡���ҧἹ���ҧ����ѡ����������ö��任�Ժѵ����ԧ��ж١��ͧ ������¤������������ͧ෤����� �к� ��кǹ��÷ӧҹ ����ҵðҹ��÷ӧҹ�ͧ����Тͧ˹��§ҹ���� �������Ǣ�ͧ", "��õ��˹ѡ ���� ����Ѻ ����Դ�͡���������� ��ЪҪ� ���͢��� ������ؤ�� ����˹��§ҹ��ҧ� ���������ǹ����㹡�ô��Թ�ҹ�ͧ˹��§ҹ ����ͧ��� �������ҧ��������������Դ��кǹ�����С�䡡������ǹ�����ͧ�ء�Ҥ��ǹ���ҧ���ԧ�������׹", "��������ö㹡���ѡ��������ҧ���͢��¾ѹ��Ե��ԧ���ط�� (�� ����Сͺ��� ʶҺѹ����֡�� ���˹�ҷ���Ҥ�Ѱ���� ���͢��¡������áԨ ���������֡�����ͼ������Ǫҭ ����� �繵�) �������׹��С������Դ�����������㹡��������ҧ����ª���٧�ش�����ѹ", "������ҷ��Ż���С��ط���ҧ� 㹡��������� �è� ������������������蹴��Թ����� �����赹����˹��§ҹ���ʧ��", "��������ö㹡���Ѻ�ѧ������㨺ؤ������ʶҹ��ó� ��о�������л�Ѻ����¹����ʹ���ͧ�Ѻʶҹ��ó����͡�����������ҡ���� 㹢�з���ѧ����Ժѵԧҹ�����ҧ�ջ���Է���Ҿ��к���ؼŵ��������·�������", "��������ö㹡�÷��й��ʹͷҧ���͡ (Option) �����Ƿҧ��ѭ�� (Solution) �������ҧ��ѵ���� ���� ����������ҧ��ä�Ԩ���������������� �����繻���ª����ͧ���", "���������������ö����ء�����������ѹ��������§�ͧ෤����� �к� ��кǹ��÷ӧҹ ����ҵðҹ��÷ӧҹ�ͧ����Тͧ˹��§ҹ���� �������Ǣ�ͧ ���ͻ���ª��㹡�û�Ժѵ�˹�ҷ��������ؼ� �������㨹������֧��������ö㹡���ͧ�Ҿ�˭� (Big Picture) ��С�äҴ��ó��������������ͧ�Ѻ�������¹�ŧ�ͧ��觵�ҧ� ����к���С�кǹ��÷ӧҹ", "��������ö㹡���ҧἹ���ҧ����ѡ��� �����������ö��任�Ժѵ����ԧ��ж١��ͧ ����֧��������ö㹡�ú����èѴ����ç��õ�ҧ� 㹤����Ѻ�Դ�ͺ�������ö�����������·���˹�������ҧ�ջ���Է���Ҿ�٧�ش", 
										"�������������л�Ժѵԧҹ���١��ͧ�ú��ǹ��ʹ��Ŵ��ͺ����ͧ����Ҩ���Դ��� ����֧��äǺ�����Ǩ������ҹ��仵��Ἱ����ҧ������ҧ�١��ͧ�Ѵਹ" );
			}
			for ( $i=0; $i<count($code); $i++ ) { 
				$cp_model = substr($code[$i],0,1);
				$cmd = " INSERT INTO PER_COMPETENCE (CP_CODE, CP_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE, 
								  UPDATE_USER, UPDATE_DATE)
								  VALUES ('$code[$i]', '$desc[$i]', '$meaning[$i]', $cp_model , 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end for

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_LEVEL ADD LEVEL_NAME VARCHAR(100) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LEVEL ADD LEVEL_NAME VARCHAR2(100) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LEVEL ADD PER_TYPE SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LEVEL ADD PER_TYPE NUMBER(1) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LEVEL ADD PER_TYPE SMALLINT(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_NAME = '�дѺ 10', PER_TYPE = 1 WHERE LEVEL_NO = '10' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_NAME = '�дѺ 11', PER_TYPE = 1 WHERE LEVEL_NO = '11' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
							  VALUES ('01', 1, $SESS_USERID, '$UPDATE_DATE', '�дѺ 1', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
							  VALUES ('02', 1, $SESS_USERID, '$UPDATE_DATE', '�дѺ 2', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
							  VALUES ('03', 1, $SESS_USERID, '$UPDATE_DATE', '�дѺ 3', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
							  VALUES ('04', 1, $SESS_USERID, '$UPDATE_DATE', '�дѺ 4', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
							  VALUES ('05', 1, $SESS_USERID, '$UPDATE_DATE', '�дѺ 5', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
							  VALUES ('06', 1, $SESS_USERID, '$UPDATE_DATE', '�дѺ 6', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
							  VALUES ('07', 1, $SESS_USERID, '$UPDATE_DATE', '�дѺ 7', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
							  VALUES ('08', 1, $SESS_USERID, '$UPDATE_DATE', '�дѺ 8', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
							  VALUES ('09', 1, $SESS_USERID, '$UPDATE_DATE', '�дѺ 9', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
							  VALUES ('E1', 1, $SESS_USERID, '$UPDATE_DATE', '������ҹ��ԡ��',3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
							  VALUES ('E2', 1, $SESS_USERID, '$UPDATE_DATE', '������ҹ෤�Ԥ',3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
							  VALUES ('E3', 1, $SESS_USERID, '$UPDATE_DATE', '������ҹ�����÷����',3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
							  VALUES ('E4', 1, $SESS_USERID, '$UPDATE_DATE', '������ҹ�ԪҪվ੾��',3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
							  VALUES ('E5', 1, $SESS_USERID, '$UPDATE_DATE', '������ҹ����Ǫҭ੾��',3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
							  VALUES ('S1', 1, $SESS_USERID, '$UPDATE_DATE', '������ҹ����Ǫҭ����� (�дѺ�����)',3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
							  VALUES ('S2', 1, $SESS_USERID, '$UPDATE_DATE', '������ҹ����Ǫҭ����� (�дѺ�����)',3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LEVEL (LEVEL_NO, LEVEL_ACTIVE, UPDATE_USER, UPDATE_DATE, LEVEL_NAME, PER_TYPE)
							  VALUES ('S3', 1, $SESS_USERID, '$UPDATE_DATE', '������ҹ����Ǫҭ����� (�дѺ�ҡ�)',3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			for ($no=1;$no<=9;$no++) {
				$level = "$no";
				$LEVEL_NO = str_pad(trim($no), 2, "0", STR_PAD_LEFT);
				$cmd = " UPDATE PER_LAYER SET LEVEL_NO = '$LEVEL_NO' WHERE LEVEL_NO = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LAYER_O SET LEVEL_NO = '$LEVEL_NO' WHERE LEVEL_NO = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_MGTSALARY SET LEVEL_NO = '$LEVEL_NO' WHERE LEVEL_NO = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '$LEVEL_NO' WHERE LEVEL_NO_MIN = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MAX = '$LEVEL_NO' WHERE LEVEL_NO_MAX = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_ASSIGN SET LEVEL_NO_MIN = '$LEVEL_NO' WHERE LEVEL_NO_MIN = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_ASSIGN SET LEVEL_NO_MAX = '$LEVEL_NO' WHERE LEVEL_NO_MAX = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_ASSIGN_YEAR SET LEVEL_NO = '$LEVEL_NO' WHERE LEVEL_NO = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_ASSIGN_S SET LEVEL_NO = '$LEVEL_NO' WHERE LEVEL_NO = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_PERSONAL SET LEVEL_NO = '$LEVEL_NO' WHERE LEVEL_NO = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET LEVEL_NO = '$LEVEL_NO' WHERE LEVEL_NO = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_TRANSFER_REQ SET TR_STARTLEVEL = '$LEVEL_NO' WHERE TR_STARTLEVEL = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_TRANSFER_REQ SET TR_LEVEL = '$LEVEL_NO' WHERE TR_LEVEL = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_TRANSFER_REQ SET LEVEL_NO_1 = '$LEVEL_NO' WHERE LEVEL_NO_1 = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_TRANSFER_REQ SET LEVEL_NO_2 = '$LEVEL_NO' WHERE LEVEL_NO_2 = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_TRANSFER_REQ SET LEVEL_NO_3 = '$LEVEL_NO' WHERE LEVEL_NO_3 = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_COMDTL SET CMD_LEVEL = '$LEVEL_NO' WHERE CMD_LEVEL = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_COMDTL SET LEVEL_NO = '$LEVEL_NO' WHERE LEVEL_NO = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_DECORCOND SET LEVEL_NO = '$LEVEL_NO' WHERE LEVEL_NO = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_SUM_DTL4 SET LEVEL_NO = '$LEVEL_NO' WHERE LEVEL_NO = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_SUM_DTL5 SET LEVEL_NO = '$LEVEL_NO' WHERE LEVEL_NO = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_SUM_DTL7 SET LEVEL_NO = '$LEVEL_NO' WHERE LEVEL_NO = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_SUM_DTL8 SET LEVEL_NO = '$LEVEL_NO' WHERE LEVEL_NO = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_LAYER_OLD SET LEVEL_NO = '$LEVEL_NO' WHERE LEVEL_NO = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE SAL_SL_REGISTER SET LEVEL_NO = '$LEVEL_NO' WHERE LEVEL_NO = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " DELETE FROM PER_LEVEL WHERE trim(LEVEL_NO) = '$level' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

			} // end for

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MIN NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MIN NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MIN DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MAX NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MAX NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MAX DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 1, 4920, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 2, 5160, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 3, 5400, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 4, 5640, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 5, 5880, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 6, 6220, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 7, 6560, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 8, 6890, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 9, 7230, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 10, 7640, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 11, 8040, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 12, 8450, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 13, 8860, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 14, 9340, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 15, 9830, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 16, 10340, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 17, 10850, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 18, 11480, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 19, 12100, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 20, 12720, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 21, 13350, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 22, 14120, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 23, 14880, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 1, 5640, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 2, 5880, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 3, 6220, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 4, 6560, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 5, 6890, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 6, 7230, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 7, 7640, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 8, 8040, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 9, 8450, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 10, 8860, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 11, 9340, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 12, 9830, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 13, 10340, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 14, 10850, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 15, 11480, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 16, 12100, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 17, 12720, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 18, 13350, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 19, 14120, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 20, 14880, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 21, 15650, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 22, 16420, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 23, 17190, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 24, 17960, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E2', 25, 18720, 1, $SESS_USERID, '$UPDATE_DATE', NULL, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E3', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 7640, 25920) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E4', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 7640, 33350) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E5', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 13350, 50610) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'S1', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 0, 100000) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'S2', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 0, 150000) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
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
					if($arr_temp[1] > '10' || ($arr_temp[1] == '10' && $arr_temp[2] > '01')) $RETIRE_YEAR += 1;
					$PER_RETIREDATE = $RETIRE_YEAR."-10-01";
					if($PER_RETIREDATE != trim($data[PER_RETIREDATE])){
						$cmd = "update PER_PERSONAL set PER_RETIREDATE='$PER_RETIREDATE' where PER_ID=$PER_ID";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
					} // end if
				} // end if
			} // end while
			
			$cmd = " DROP TABLE PER_ABILITY_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_ABSENT_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_ABSENT_CONF_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_ABSENTHIS_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_BONUSPROMOTE_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_BONUSQUOTA_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_BONUSQUOTADTL1_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_BONUSQUOTADTL2_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_COMDTL_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_COMMAND_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_COURSE_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_COURSEDTL_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_DECOR_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_DECORATEHIS_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_DECORDTL_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_EDUCATE_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_EXTRAHIS_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_HEIR_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_INVEST1_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_INVEST1DTL_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_INVEST2_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_INVEST2DTL_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_LETTER_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_MARRHIS_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_MOVE_REQ_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_NAMEHIS_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_ORDER_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_ORDER_DTL_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_ORG_JOB_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_PERSONAL_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_PERSONALPIC_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_POS_EMP_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_POS_MOVE_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_POSITION_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_POSITIONHIS_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_PROMOTE_C_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_PROMOTE_E_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_PROMOTE_P_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_PUNISHMENT_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_REQ1_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_REQ1_DTL1_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_REQ1_DTL2_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_REQ2_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_REQ2_DTL_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_REQ3_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_REQ3_DTL_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_REWARDHIS_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_SALARYHIS_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_SALPROMOTE_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_SALQUOTA_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_SALQUOTADTL1_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_SALQUOTADTL2_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_SCHOLAR_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_SCHOLARINC_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_SERVICEHIS_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_SPOUSE_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_SUM_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_SUM_DTL1_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_SUM_DTL2_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_SUM_DTL3_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_SUM_DTL4_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_SUM_DTL5_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_SUM_DTL6_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_SUM_DTL7_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_SUM_DTL8_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_SUM_DTL9_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_TIMEHIS_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_TRAINING_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_TRANSFER_REQ_B ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

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

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD LEVEL_NO_SALARY VARCHAR(10) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD LEVEL_NO_SALARY VARCHAR2(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_PERSONAL SET LEVEL_NO_SALARY = LEVEL_NO WHERE LEVEL_NO_SALARY IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMDTL ADD LEVEL_NO_SALARY VARCHAR(10) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL ADD LEVEL_NO_SALARY VARCHAR2(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMDTL SET LEVEL_NO_SALARY = LEVEL_NO WHERE LEVEL_NO_SALARY IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_MOVE_REQ ADD ORG_ID_REF_1 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_MOVE_REQ ADD ORG_ID_REF_1 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_MOVE_REQ ADD ORG_ID_REF_1 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_MOVE_REQ ADD ORG_ID_REF_2 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_MOVE_REQ ADD ORG_ID_REF_2 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_MOVE_REQ ADD ORG_ID_REF_2 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_MOVE_REQ ADD ORG_ID_REF_3 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_MOVE_REQ ADD ORG_ID_REF_3 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_MOVE_REQ ADD ORG_ID_REF_3 INTEGER(10) NULL ";
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
							  VALUES ('9999', '��. 99.9', '����觻�Ѻ�ѭ���Թ��͹', '05') ";
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
				CONSTRAINT PK_PER_PERFORMANCE PRIMARY KEY (PF_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_PERFORMANCE(
				PF_CODE VARCHAR2(10) NOT NULL,	
				PF_NAME VARCHAR2(255) NOT NULL,
				PF_ACTIVE NUMBER(1) NOT NULL,		
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_PERFORMANCE PRIMARY KEY (PF_CODE)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_PERFORMANCE(
				PF_CODE VARCHAR(10) NOT NULL,	
				PF_NAME VARCHAR(255) NOT NULL,
				PF_ACTIVE SMALLINT(1) NOT NULL,		
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_PERFORMANCE PRIMARY KEY (PF_CODE)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_PERFORMANCE (PF_CODE, PF_NAME, PF_ACTIVE, UPDATE_USER, UPDATE_DATE) 
							  VALUES ('01', '�ѹ�֡�ŧҹ����黯ԺѵԵ��˹�ҷ���Ѻ�Դ�ͺ ��觴��Թ�������稵��������Ѻ�ͺ����', 1, $SESS_USERID, 
							  '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_PERFORMANCE (PF_CODE, PF_NAME, PF_ACTIVE, UPDATE_USER, UPDATE_DATE) 
							  VALUES ('02', '�ѹ�֡�ŧҹ������Ѻ�ͺ���¾����', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_PERFORMANCE (PF_CODE, PF_NAME, PF_ACTIVE, UPDATE_USER, UPDATE_DATE) 
							  VALUES ('03', '��ػ�ŧҹ���������ⴴ�����ͼŧҹ�Ӥѭ��軯Ժѵ���㹪�ǧ��� �ҡ���ѹ�֡�������Թ 6 �ŧҹ', 1, $SESS_USERID, 
							  '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_GOODNESS(
				GN_CODE VARCHAR(10) NOT NULL,	
				GN_NAME VARCHAR(255) NOT NULL,
				GN_ACTIVE SINGLE NOT NULL,		
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_GOODNESS PRIMARY KEY (GN_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_GOODNESS(
				GN_CODE VARCHAR2(10) NOT NULL,	
				GN_NAME VARCHAR2(255) NOT NULL,
				GN_ACTIVE NUMBER(1) NOT NULL,		
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_GOODNESS PRIMARY KEY (GN_CODE)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_GOODNESS(
				GN_CODE VARCHAR(10) NOT NULL,	
				GN_NAME VARCHAR(255) NOT NULL,
				GN_ACTIVE SMALLINT(1) NOT NULL,		
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_GOODNESS PRIMARY KEY (GN_CODE)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_GOODNESS (GN_CODE, GN_NAME, GN_ACTIVE, UPDATE_USER, UPDATE_DATE) 
							  VALUES ('01', '��ԺѵԵ��������º�Թ�� ����Һ�ó (����Һ�ó����Թ�µ�͵��ͧ���˹��§ҹ, ����Һ�ó����Թ�µ�ͻ�ЪҪ�) ', 1, 
							  $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_GOODNESS (GN_CODE, GN_NAME, GN_ACTIVE, UPDATE_USER, UPDATE_DATE) 
							  VALUES ('02', '��û�оĵ� ��û�ԺѵԵ���ҹ�˹�ҷ���Ѻ�Դ�ͺ', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_GOODNESS (GN_CODE, GN_NAME, GN_ACTIVE, UPDATE_USER, UPDATE_DATE) 
							  VALUES ('03', '�ĵԡ�����÷Ӥس������������ � �����ǹ�Ҫ��á�˹�����ѡɳЧҹ�ͧ�� ���ͷ������Ԥ���Ѱ����ա�˹���� ', 1, 
							  $SESS_USERID, '$UPDATE_DATE') ";
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
				CONSTRAINT PK_PER_PERFORMANCE_GOODNESS PRIMARY KEY (PG_ID)) ";
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
				CONSTRAINT PK_PER_PERFORMANCE_GOODNESS PRIMARY KEY (PG_ID)) ";
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
				CONSTRAINT PK_PER_PERFORMANCE_GOODNESS PRIMARY KEY (PG_ID)) ";
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
				CONSTRAINT PK_PER_PERFORMANCE_DTL PRIMARY KEY (PD_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_PERFORMANCE_DTL(
				PD_ID NUMBER(10) NOT NULL,	
				PG_ID NUMBER(10) NOT NULL,	
				PF_CODE VARCHAR2(10) NULL,
				PERFORMANCE_DESC VARCHAR2(2000) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_PERFORMANCE_DTL PRIMARY KEY (PD_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_PERFORMANCE_DTL(
				PD_ID INTEGER(10) NOT NULL,	
				PG_ID INTEGER(10) NOT NULL,	
				PF_CODE VARCHAR(10) NULL,
				PERFORMANCE_DESC TEXT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_PERFORMANCE_DTL PRIMARY KEY (PD_ID)) ";
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
				CONSTRAINT PK_PER_GOODNESS_DTL PRIMARY KEY (GD_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_GOODNESS_DTL(
				GD_ID NUMBER(10) NOT NULL,	
				PG_ID NUMBER(10) NOT NULL,	
				GN_CODE VARCHAR2(10) NULL,
				GOODNESS_DESC VARCHAR2(2000) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_GOODNESS_DTL PRIMARY KEY (GD_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_GOODNESS_DTL(
				GD_ID INTEGER(10) NOT NULL,	
				PG_ID INTEGER(10) NOT NULL,	
				GN_CODE VARCHAR(10) NULL,
				GOODNESS_DESC TEXT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_GOODNESS_DTL PRIMARY KEY (GD_ID)) ";
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
				CONSTRAINT PK_PER_TRAINING_DTL PRIMARY KEY (TD_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_TRAINING_DTL(
				TD_ID NUMBER(10) NOT NULL,	
				PG_ID NUMBER(10) NOT NULL,	
				TD_SEQ NUMBER(2) NULL,
				TRAINING_DESC VARCHAR2(2000) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_TRAINING_DTL PRIMARY KEY (TD_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_TRAINING_DTL(
				TD_ID INTEGER(10) NOT NULL,	
				PG_ID INTEGER(10) NOT NULL,	
				TD_SEQ SMALLINT(2) NULL,
				TRAINING_DESC TEXT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_TRAINING_DTL PRIMARY KEY (TD_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_PRENAME DROP CONSTRAINT INXU1_PER_PRENAME ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU1_PER_PRENAME ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE  PER_PERSONAL SET PER_ORDAIN = 0 WHERE PER_ORDAIN IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE  PER_PERSONAL SET PER_SOLDIER = 0 WHERE PER_SOLDIER IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE  PER_PERSONAL SET PER_MEMBER = 0 WHERE PER_MEMBER IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE  PER_PERSONAL SET LEVEL_NO_SALARY = '01' WHERE LEVEL_NO_SALARY = '1' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE  PER_PERSONAL SET LEVEL_NO_SALARY = '02' WHERE LEVEL_NO_SALARY = '2' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE  PER_PERSONAL SET LEVEL_NO_SALARY = '03' WHERE LEVEL_NO_SALARY = '3' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE  PER_PERSONAL SET LEVEL_NO_SALARY = '04' WHERE LEVEL_NO_SALARY = '4' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE  PER_PERSONAL SET LEVEL_NO_SALARY = '05' WHERE LEVEL_NO_SALARY = '5' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE  PER_PERSONAL SET LEVEL_NO_SALARY = '06' WHERE LEVEL_NO_SALARY = '6' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE  PER_PERSONAL SET LEVEL_NO_SALARY = '07' WHERE LEVEL_NO_SALARY = '7' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE  PER_PERSONAL SET LEVEL_NO_SALARY = '08' WHERE LEVEL_NO_SALARY = '8' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE  PER_PERSONAL SET LEVEL_NO_SALARY = '09' WHERE LEVEL_NO_SALARY = '9' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_SQL(
				SQL_CODE VARCHAR(3) NOT NULL,	
				SQL_NAME VARCHAR(100) NOT NULL,	
				SQL_CMD MEMO NULL,	
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_SQL PRIMARY KEY (SQL_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_SQL(
				SQL_CODE VARCHAR2(3) NOT NULL,	
				SQL_NAME VARCHAR2(100) NOT NULL,	
				SQL_CMD VARCHAR2(1000) NULL,	
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_SQL PRIMARY KEY (SQL_CODE)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_SQL(
				SQL_CODE VARCHAR(3) NOT NULL,	
				SQL_NAME VARCHAR(100) NOT NULL,	
				SQL_CMD TEXT NULL,	
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_SQL PRIMARY KEY (SQL_CODE)) ";
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
				CONSTRAINT PK_PER_SUM_DTL10 PRIMARY KEY (SUM_ID, PL_CODE, LEVEL_NO, EL_CODE)) ";
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
				CONSTRAINT PK_PER_SUM_DTL10 PRIMARY KEY (SUM_ID, PL_CODE, LEVEL_NO, EL_CODE)) ";
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
				CONSTRAINT PK_PER_SUM_DTL10 PRIMARY KEY (SUM_ID, PL_CODE, LEVEL_NO, EL_CODE)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('001', '��Ѵ��з�ǧ������º���', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '001' WHERE PM_CODE IN ('0106', '0108', '0109') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('002', '͸Ժ��������º���', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '002' WHERE PM_CODE IN ('0357', '0278', '0282') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('003', '�ͧ��Ѵ��з�ǧ������º���', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '003' WHERE PM_CODE IN ('0266', '0267', '0268') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('004', '�������Ҫ��èѧ��Ѵ', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '004' WHERE PM_CODE IN ('0233') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('005', '�͡�Ѥ��Ҫ�ٵ', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '005' WHERE PM_CODE IN ('0362', '0363', '0364', '0365') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('006', '����Ǩ�Ҫ����дѺ��з�ǧ', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '006' WHERE PM_CODE IN ('0216', '0218', '0219') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('007', '���� (���˹觻������������дѺ�٧)', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('008', '�ͧ͸Ժ��������º���', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '008' WHERE PM_CODE IN ('0273', '0274', '0276') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('009', '�ͧ�������Ҫ��èѧ��Ѵ������º���', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '009' WHERE PM_CODE IN ('0269') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('010', '�Ѥ��Ҫ�ٵ', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '010' WHERE PM_CODE IN ('0359', '0360') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('011', '���� (���˹觻������������дѺ��)', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('012', '��.�ӹѡ������º���', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '012' WHERE PM_CODE IN ('0251', '0252', '0253') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('013', '���˹����ǹ�Ҫ��û�ШӨѧ��Ѵ', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('014', '���� (���˹觻������ӹ�¡���дѺ�٧)', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('015', '����ӹ�¡�áͧ������º���', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_MGT SET PS_CODE = '015' WHERE PM_CODE IN ('0235', '0237', '0249') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('016', '���˹����ǹ�Ҫ��û�ШӨѧ��Ѵ', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_STATUS (PS_CODE, PS_NAME, PS_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('017', '���� (���˹觻������ӹ�¡���дѺ��)', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_LEVEL ADD LEVEL_SHORTNAME VARCHAR(20) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LEVEL ADD LEVEL_SHORTNAME VARCHAR2(20) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LEVEL ADD LEVEL_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LEVEL ADD LEVEL_SEQ_NO NUMBER(2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LEVEL ADD LEVEL_SEQ_NO SMALLINT(2) NULL ";
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

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '��.', LEVEL_SEQ_NO = 21 WHERE LEVEL_NO = 'O1' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '��.', LEVEL_SEQ_NO = 22 WHERE LEVEL_NO = 'O2' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '��.', LEVEL_SEQ_NO = 23 WHERE LEVEL_NO = 'O3' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '��.', LEVEL_SEQ_NO = 24 WHERE LEVEL_NO = 'O4' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '��.', LEVEL_SEQ_NO = 31 WHERE LEVEL_NO = 'K1' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '��.', LEVEL_SEQ_NO = 32 WHERE LEVEL_NO = 'K2' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '��.', LEVEL_SEQ_NO = 33 WHERE LEVEL_NO = 'K3' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '��.', LEVEL_SEQ_NO = 34 WHERE LEVEL_NO = 'K4' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '��.', LEVEL_SEQ_NO = 35 WHERE LEVEL_NO = 'K5' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '͵.', LEVEL_SEQ_NO = 41 WHERE LEVEL_NO = 'D1' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '��.', LEVEL_SEQ_NO = 42 WHERE LEVEL_NO = 'D2' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '��.', LEVEL_SEQ_NO = 51 WHERE LEVEL_NO = 'M1' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '��.', LEVEL_SEQ_NO = 52 WHERE LEVEL_NO = 'M2' ";
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

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_NICKNAME VARCHAR(20) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_NICKNAME VARCHAR2(20) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_HOME_TEL VARCHAR(50) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_HOME_TEL VARCHAR2(50) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_OFFICE_TEL VARCHAR(30) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_OFFICE_TEL VARCHAR2(30) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_FAX VARCHAR(20) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_FAX VARCHAR2(20) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_MOBILE VARCHAR(20) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_MOBILE VARCHAR2(20) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_EMAIL VARCHAR(30) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_EMAIL VARCHAR2(30) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_PERCENT_UP NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_PERCENT_UP NUMBER(5,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_PERCENT_UP DECIMAL(5,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_SALARY_UP NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_SALARY_UP NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_SALARY_UP DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_SALARY_EXTRA NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_SALARY_EXTRA NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_SALARY_EXTRA DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SALARYHIS ALTER SAH_DOCNO VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS MODIFY SAH_DOCNO VARCHAR2(255) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SALARYHIS MODIFY SAH_DOCNO VARCHAR(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_DOCNO VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_DOCNO VARCHAR2(255) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_DOCNO VARCHAR(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAIN ALTER TR_NAME MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAIN MODIFY TR_NAME VARCHAR2(1000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_TRAIN MODIFY TR_NAME TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('21', 'K2', 3500, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('21', 'K3', 5600, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('21', 'K4', 9900, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('21', 'K5', 13000, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('21', 'K5', 15600, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('22', 'O4', 9900, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('22', 'K4', 9900, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('22', 'K5', 13000, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('22', 'K5', 15600, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('31', 'D1', 5600, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('32', 'D2', 10000, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('32', 'M1', 10000, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('32', 'M2', 14500, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MGTSALARY (PT_CODE, LEVEL_NO, MS_SALARY, MS_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('32', 'M2', 21000, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_TEMP NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_TEMP NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_TEMP DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_TEMP NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_TEMP NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_TEMP DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_FULL NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_FULL NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_FULL DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_FULL NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_FULL NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_FULL DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MIDPOINT NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MIDPOINT NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MIDPOINT DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_MIDPOINT NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_MIDPOINT NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_MIDPOINT DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MIDPOINT1 NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MIDPOINT1 NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MIDPOINT1 DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_MIDPOINT1 NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_MIDPOINT1 NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_MIDPOINT1 DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MIDPOINT2 NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MIDPOINT2 NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SALARY_MIDPOINT2 DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_MIDPOINT2 NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_MIDPOINT2 NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SALARY_MIDPOINT2 DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
							  VALUES (0, 'O1', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 4630, 18190, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
							  VALUES (0, 'O2', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 10190, 33540, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
							  VALUES (0, 'O3', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 15410, 47450, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
							  VALUES (0, 'O4', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 48220, 59770, NULL) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
							  VALUES (0, 'K1', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 7940, 22220, 6800) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
							  VALUES (0, 'K2', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 14330, 36020, 12530) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
							  VALUES (0, 'K3', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 21080, 50550, 18910) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
							  VALUES (0, 'K4', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 29900, 59770, 23230) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
							  VALUES (0, 'K5', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 41720, 66480, 28550) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
							  VALUES (0, 'D1', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 25390, 50550, 18910) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
							  VALUES (0, 'D2', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 31280, 59770, 23230) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
							  VALUES (0, 'M1', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 48700, 64340, 23230) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP)
							  VALUES (0, 'M2', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 53690, 66480, 28550) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 13265, LAYER_SALARY_MIDPOINT1 = 10790, 
							  LAYER_SALARY_MIDPOINT2 = 15730 WHERE LAYER_TYPE = 0  AND LEVEL_NO = 'O1' AND LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 21875, LAYER_SALARY_MIDPOINT1 = 16030, 
							  LAYER_SALARY_MIDPOINT2 = 27710 WHERE LAYER_TYPE = 0  AND LEVEL_NO = 'O2' AND LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 31435, LAYER_SALARY_MIDPOINT1 = 28270, 
							  LAYER_SALARY_MIDPOINT2 = 39440, LAYER_SALARY_FULL = 36020 WHERE LAYER_TYPE = 0  AND 
							  LEVEL_NO = 'O3' AND LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 54005, LAYER_SALARY_MIDPOINT1 = 51110, 
							  LAYER_SALARY_MIDPOINT2 = 56890 WHERE LAYER_TYPE = 0  AND LEVEL_NO = 'O4' AND LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 17675, LAYER_SALARY_MIDPOINT1 = 15390, 
							  LAYER_SALARY_MIDPOINT2 = 19950 WHERE LAYER_TYPE = 0  AND LEVEL_NO = 'K1' AND LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 25185, LAYER_SALARY_MIDPOINT1 = 20350, 
							  LAYER_SALARY_MIDPOINT2 = 30600 WHERE LAYER_TYPE = 0  AND LEVEL_NO = 'K2' AND LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 35825, LAYER_SALARY_MIDPOINT1 = 31220, 
							  LAYER_SALARY_MIDPOINT2 = 43190 WHERE LAYER_TYPE = 0  AND LEVEL_NO = 'K3' AND LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 44845, LAYER_SALARY_MIDPOINT1 = 44060, 
							  LAYER_SALARY_MIDPOINT2 = 52310 WHERE LAYER_TYPE = 0  AND LEVEL_NO = 'K4' AND LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 54105, LAYER_SALARY_MIDPOINT1 = 53360, 
							  LAYER_SALARY_MIDPOINT2 = 60290, LAYER_SALARY_FULL = 64340 WHERE LAYER_TYPE = 0  AND 
							  LEVEL_NO = 'K5' AND LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 37975, LAYER_SALARY_MIDPOINT1 = 31680, 
							  LAYER_SALARY_MIDPOINT2 = 44260 WHERE LAYER_TYPE = 0  AND LEVEL_NO = 'D1' AND LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 45535, LAYER_SALARY_MIDPOINT1 = 45150, 
							  LAYER_SALARY_MIDPOINT2 = 52650 WHERE LAYER_TYPE = 0  AND LEVEL_NO = 'D2' AND LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 56525, LAYER_SALARY_MIDPOINT1 = 52650, 
							  LAYER_SALARY_MIDPOINT2 = 60430 WHERE LAYER_TYPE = 0  AND LEVEL_NO = 'M1' AND LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_SALARY_MIDPOINT = 61645, LAYER_SALARY_MIDPOINT1 = 61640, 
							  LAYER_SALARY_MIDPOINT2 = 63290 WHERE LAYER_TYPE = 0  AND LEVEL_NO = 'M2' AND LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_CONTROL set CTRL_ALTER = 3 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if($CTRL_ALTER < 3) 

		if($CTRL_ALTER < 4) {
			if($DPISDB=="odbc") {																					
				$cmd = " ALTER TABLE PER_POSITION DROP CONSTRAINT [{FEC50664-6E52-4C2E-B2C0-5530E5C5098C}] ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_POS_MOVE DROP CONSTRAINT [{9983401B-0ED9-4925-97CD-5FABF2649D3B}] ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_ORDER_DTL DROP CONSTRAINT [{BFBB5F94-098A-48F7-94F1-65D5F187EEEF}] ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_REQ1_DTL1 DROP CONSTRAINT [{D941B381-E8F8-4A8C-9615-1A2499A3E384}] ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_REQ2_DTL DROP CONSTRAINT [{434A09D2-2535-4382-B396-DFD0A543A4F7}] ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_REQ3_DTL DROP CONSTRAINT [{1579F803-EA82-4D02-90BA-257622759D1C}] ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 

				$cmd = " ALTER TABLE PER_SUM_DTL2 DROP CONSTRAINT [{2D246E94-B861-43EB-8B0C-181703E095B5}] ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); 
			}

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
				$cmd = " ALTER TABLE PER_CO_LEVEL MODIFY CL_NAME VARCHAR(50) ";
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
				$cmd = " ALTER TABLE PER_POSITION MODIFY CL_NAME VARCHAR(50) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POSITION SET CL_NAME = TRIM(CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK7_PER_POSITION 
							  FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_MOVE ALTER CL_NAME VARCHAR(50) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_MOVE MODIFY CL_NAME VARCHAR2(50) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_MOVE MODIFY CL_NAME VARCHAR(50) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_MOVE SET CL_NAME = TRIM(CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POS_MOVE ADD CONSTRAINT FK8_PER_POS_MOVE 
							  FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ORDER_DTL ALTER CL_NAME VARCHAR(50) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORDER_DTL MODIFY CL_NAME VARCHAR2(50) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ORDER_DTL MODIFY CL_NAME VARCHAR(50) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_ORDER_DTL SET CL_NAME = TRIM(CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_ORDER_DTL ADD CONSTRAINT FK9_PER_ORDER_DTL 
							  FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_REQ1_DTL1 ALTER CL_NAME VARCHAR(50) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REQ1_DTL1 MODIFY CL_NAME VARCHAR2(50) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_REQ1_DTL1 MODIFY CL_NAME VARCHAR(50) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_REQ1_DTL1 SET CL_NAME = TRIM(CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD CONSTRAINT FK8_PER_REQ1_DTL1 
							  FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_REQ2_DTL ALTER CL_NAME VARCHAR(50) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REQ2_DTL MODIFY CL_NAME VARCHAR2(50) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_REQ2_DTL MODIFY CL_NAME VARCHAR(50) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_REQ2_DTL SET CL_NAME = TRIM(CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ2_DTL ADD CONSTRAINT FK11_PER_REQ2_DTL 
							  FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_REQ3_DTL ALTER CL_NAME VARCHAR(50) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REQ3_DTL MODIFY CL_NAME VARCHAR2(50) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_REQ3_DTL MODIFY CL_NAME VARCHAR(50) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_REQ3_DTL SET CL_NAME = TRIM(CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REQ3_DTL ADD CONSTRAINT FK9_PER_REQ3_DTL 
							  FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SUM_DTL2 ALTER CL_NAME VARCHAR(50) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SUM_DTL2 MODIFY CL_NAME VARCHAR2(50) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SUM_DTL2 MODIFY CL_NAME VARCHAR(50) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_SUM_DTL2 SET CL_NAME = TRIM(CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SUM_DTL2 ADD CONSTRAINT FK3_PER_SUM_DTL2 
							  FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('�������дѺ�� - �������дѺ�٧', 'M1', 'M2', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('�������дѺ�٧', 'M2', 'M2', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('�ӹ�¡���дѺ�� - �ӹ�¡���дѺ�٧', 'D1', 'D2', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('�ӹ�¡���дѺ�٧', 'D2', 'D2', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('��Ժѵԡ�� - �ç�س�ز�', 'K1', 'K5', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('��Ժѵԡ�� - ����Ǫҭ', 'K1', 'K4', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('��Ժѵԡ�� - �ӹҭ��þ����', 'K1', 'K3', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES (�ӹҭ��� - ����Ǫҭ', 'K2', 'K4', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES (�ӹҭ��� - �ӹҭ��þ����', 'K2', 'K3', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('��Ժѵԧҹ - �ѡ�о����', 'O2', 'O4', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('��Ժѵԧҹ - ������', 'O1', 'O3', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('��Ժѵԧҹ - �ӹҭ�ҹ', 'O1', 'O2', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LINE ADD PL_TYPE SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LINE ADD PL_TYPE NUMBER(1) NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_LINE ADD PL_TYPE SMALLINT(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_LINE ADD PL_CODE_NEW VARCHAR(10) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LINE ADD PL_CODE_NEW VARCHAR2(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_LINE ADD LG_CODE VARCHAR(10) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LINE ADD LG_CODE VARCHAR2(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_LINE ADD PL_CODE_DIRECT VARCHAR(10) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LINE ADD PL_CODE_DIRECT VARCHAR2(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_LINE ADD CL_NAME VARCHAR(50) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LINE ADD CL_NAME VARCHAR2(50) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_MAP_POS(
				LEVEL_NO VARCHAR(10) NOT NULL,	
				PL_TYPE SINGLE NOT NULL,
				NEW_LEVEL_NO VARCHAR(10) NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_MAP_POS PRIMARY KEY (LEVEL_NO, PL_TYPE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_MAP_POS(
				LEVEL_NO VARCHAR2(10) NOT NULL,	
				PL_TYPE NUMBER(1) NOT NULL,
				NEW_LEVEL_NO VARCHAR2(10) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_MAP_POS PRIMARY KEY (LEVEL_NO, PL_TYPE)) ";
			elseif($DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_MAP_POS(
				LEVEL_NO VARCHAR(10) NOT NULL,	
				PL_TYPE SMALLINT(1) NOT NULL,
				NEW_LEVEL_NO VARCHAR(10) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_MAP_POS PRIMARY KEY (LEVEL_NO, PL_TYPE)) ";
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
				CONSTRAINT PK_PER_LINE_GROUP PRIMARY KEY (LG_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_LINE_GROUP(
				LG_CODE VARCHAR2(10) NOT NULL,	
				LG_NAME VARCHAR2(100) NOT NULL,
				LG_ACTIVE NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_LINE_GROUP PRIMARY KEY (LG_CODE)) ";
			elseif($DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_LINE_GROUP(
				LG_CODE VARCHAR(10) NOT NULL,	
				LG_NAME VARCHAR(100) NOT NULL,
				LG_ACTIVE SMALLINT(1) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_LINE_GROUP PRIMARY KEY (LG_CODE)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE = trim(PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_TYPE = 0 WHERE PL_CODE IN (
			'000010', '000020', '000030', '000040', '000050', '000060', '000070',
			'000080', '000090', '000100', '000110', '000120', '000130', '000140', '000150', '000151', '000160', '000170', '000180', '000190',
			'000200', '000210', '000220', '000230', '000232', '000240', '000250', '000280', '000290', '000300', '000310', '000320', '000321',
			'000330', '000340', '000350', '000360', '000370', '000380', '000381', '000382', '000390', '000400', '000410', '000430', '000440',
			'000450', '000460', '000470', '000480', '000490', '000500', '000510', '000520', '000530', '000531', '000540', '000550', '000560',
			'000570', '000580', '000590', '000600', '000610', '000611', '000619', '000620', '000630', '000640', '000650', '000803', '000804',
			'000805', '001010', '001020', '001030', '001040', '001050', '001060', '001070', '001080', '001090', '001100', '001110', '001120',
			'001130', '001131', '001140', '001150', '001160', '001170', '001180', '001190', '001191', '001200', '001210', '001220', '001230',
			'001240', '001241', '001260', '001270', '001280', '001290', '001291', '001300', '001301', '001302', '001303', '001310', '001320',
			'001330', '001340', '001350', '001351', '001360', '001370', '001380', '001390', '001400', '001401', '001430', '001440', '001450',
			'001460', '001470', '001480', '001490', '001500', '001510', '001520', '001530', '001540', '001550', '001560', '001570', '001571',
			'001572', '001580', '001590', '001600', '001601', '001602', '001603', '001604', '001605', '001606', '001607', '001608', '001609',
			'001610', '001611', '001612', '001613', '001614', '001615', '001616', '001700', '001701', '001702', '001703', '001704', '001705',
			'001706', '001707', '001708', '001709', '001800', '001801', '001902', '001903', '001905', '001906', '001907', '001908', '001909',
			'002000', '002010', '002020', '002021', '002022', '002023', '009008', '009009', '009010', '009020', '009030', '009031', '009040',
			'009050', '009060', '009070', '009080', '009081', '009082', '009083', '009084', '009085', '009086', '009087', '009088', '009089',
			'009160', '009170', '009190',

			'010512', '010525', '010603', '010803', '010904', '011003', '011103', '011302', '011313', '011324', '011624', '011734', '012103', 
			'012223', '012234',
			'012414', '012427', '012503', '012824', '012846', '013203', '020224', '020435', '020701', '020834', '021103', '021325', '021434',
			'021534', '021613', '021614', '021915', '022324', '022434', '022503', '022601', '022612', '022625', '022734', '022803', '022901',
			'022912', '022924', '023003', '023102', '023113', '023124', '023402', '023415', '023635', '023703', '023825', '030224', '030803',
			'030511', '031001', '031103', '031224''031301', '031401', '031501', '031604', '031824', '031935', '032434', '032535', '040424',
			'040624', '040803', '040924', '041124', '041201', '041304', '041423', '041434', '050703', '051525', '051724', '060503', '060823',
			'060903', '061003', '061103', '062002', '062403', '062514', '062903', '063302', '063401', '070303', '071103', '071205', '071316',
			'071701', '072123', '072623', '073301', '073303', '073401', '073903', '075505', '080734', '080834', '080903', '081401', '081615',
			'082506', '082634', '082734', '082803', '083001', '083012', '083023', '083034', '083103', '083324', '083514', '083734', '083803',
			'083902', '083914', '084134', '084534',
				
			'092211', '092212', '092213', '092214', '092215', '092216', '092310', '092320', '092330', '092340', '092350') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_TYPE = 1 WHERE PL_CODE IN ('010501', '011211', '011601', '011612',
			'011701', '011712', '011801', '011901', '012201', '012212', '012302', '012801', '012812', '020201', '020212', '020401', '020412',
			'020502', '020801', '020812', '021301', '021401', '021412', '021501', '021512', '021801', '022301', '022312', '022401', '022412',
			'022701', '022712', '023302', '023601', '023612', '023801', '023812', '030201', '030212', '030601', '030701', '030901', '031201',
			'031212', '031801', '031901', '031912', '032011', '032311', '032401', '032412', '032501', '032512', '032601', '040301', '040312',
			'040401', '040412', '040601', '040612', '040901', '040912', '041101', '041112', '041401', '041412', '050201', '050212', '051501',
			'051512', '051701', '051712', '060802', '061502', '061601', '061701', '061712', '061902', '062102', '062201', '062212', '062301',
			'062312', '062601', '062701', '062712', '062802', '063001', '063012', '063101', '063112', '063201', '063212', '063502', '071401',
			'071412', '071501', '071512', '071601', '071612', '071801', '071812', '071901', '071912', '072001', '072003', '072101', '072112',
			'072301', '072401', '072412', '072601', '072612', '072802', '072901', '073001', '073012', '073101', '073112', '073201', '073212',
			'073501', '073512', '073601', '073612', '073701', '073712', '074101', '074112', '074202', '074301', '074312', '074401', '074412',
			'074501', '074512', '074601', '074701', '074802', '074811', '074901', '074912', '075201', '075602', '075702', '080502', '080701',
			'080712', '080801', '080812', '081001', '081501', '082001', '082101', '082201', '082301', '082601', '082612', '082701', '082712',
			'083301', '083312', '083701', '083712', '084101', '084412', '084301', '084501', '084512', '092380', '092370',

			'000221', '000222', '001410', '001420') ";
			$db_dpis->send_cmd($cmd);          
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_TYPE = 2 WHERE PL_CODE IN (
			'010403', '010703', '010903', '011013', '011203', '011403', '011503', '011723', '012003', '012403', '012603', '012613', '012626', '012703',
			'012833', '012903', '013003', '013103', '013303', '013405', '013503', '020103', '020303', '020423', '020603', '020823', '020903', '021003', 
			'021203', '021313',	'021423', '021523', '021603', '021703', '021813', '021903', '022003', '022103', '022203', '022423', '022513', '022723', 
			'023203', '023503', '023623', '023903',	'030103', '030306', '030403', '030503', '031703', '031813', '031923', '032003', '032103', '032203', 
			'032303', '032423', '032523', '040103', '040203', '040323',	'040503', '040703', '041003', '041443', '041503', '050103', '050223', '050303', 
			'050403', '050503', '050603', '050803', '050903', '051003', '051103', '051203',	'051303', '051403', '051603', '051803', '060104', '060204', 
			'060304', '060403', '060603', '060703', '060813', '061203', '061303', '061403', '061514', '061523',	'061723', '061803', '061913', '062503', 
			'063023', '063123', '063603', '070103', '070203', '070403', '070503', '070603', '070703', '070803', '070903', '071003', '071303', '071523', 
			'071623', '072203', '072503', '072703', '073123', '073803', '073813', '074003', '074213', '074423', '075003', '075103', '075303', '075403', 
			'080103', '080203', '080303', '080403', '080513', '080603', '080613', '080723', '080823', '080913', '080923', '081103', '081203', '081303', 
			'081603', '081703', '081803', '081903', '082403', '082623', '082723', '082903', '083203', '083403', '083503', '083603', '083723', '083823', 
			'084003', '084123', '084203', '084403', '084523') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_TYPE = 5 WHERE PL_CODE IN (
			'000231', '001250', '010404', '010604', '04032' , '05110', '082804',
			'092101', '092102', '092103', '092104', '092105', '092106', '092107', '092108', '092201', '092202', '092203', '092204', '092205',
			'092206', '092207', '092208', '092209', '092210') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_TYPE = 4 WHERE PL_CODE IN ('010108', '010209', '010307') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LINE DROP CONSTRAINT INXU1_PER_LINE ";
			elseif($DPISDB=="oci8")
				$cmd = " DROP INDEX INXU1_PER_LINE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LINE DROP CONSTRAINT INXU2_PER_LINE ";
			elseif($DPISDB=="oci8")
				$cmd = " DROP INDEX INXU2_PER_LINE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_MGT DROP CONSTRAINT INXU2_PER_MGT ";
			elseif($DPISDB=="oci8")
				$cmd = " DROP INDEX INXU2_PER_MGT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_LINE WHERE PL_CODE IN ('04032', '05110') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMDTL ALTER CMD_SEQ INTEGER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL MODIFY CMD_SEQ NUMBER(6) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMDTL ALTER CMD_SEQ INTEGER(6) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL1_DESC VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL1_DESC VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL2_DESC VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL2_DESC VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL3_DESC VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL3_DESC VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL4_DESC VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL4_DESC VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL5_DESC VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI ADD KPI_TARGET_LEVEL5_DESC VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$PL_CODE = array('010108', '010209', '010307', '010403', '010501', '010703', '010903', '011013', '011203', '011211', '011503', '011612', 
										  '011712', '011723', '012003', '012212', '012302', '012403', '012603', '012613', '012626', '012703', '012812', '012833', 
										  '012903', '013003', '013103', '013303', '013405', '013503', '020103', '020212', '020303', '020412', '020423', '020502', 
										  '020603', '020812', '020823', '020903', '021003', '021203', '021301', '021412', '021423', '021512', '021523', '021603', 
										  '021703', '021903', '022003', '022103', '022203', '022312', '022412', '022423', '022513', '022712', '022723', '023203', 
										  '023302', '023503', '023612', '023623', '023903', '030103', '030212', '030306', '030403', '030503', '030601', '031212', 
										  '031813', '031801', '031912', '031923', '032003', '032011', '032423', '032512', '032523', '032601', '040103', '040203', 
										  '040312', '040323', '040412', '040503', '040612', '040703', '040912', '041003', '041112', '041412', '041443', '041503', 
										  '050103', '050212', '050223', '050303', '050403', '050503', '050803', '050903', '051003', '051103', '051303', '051403', 
										  '051512', '051603', '051712', '051803', '060104', '060204', '060304', '060403', '060603', '060703', '060802', '060813', 
										  '061203', '061502', '061514', '061523', '061712', '061723', '061803', '061902', '061913', '062102', '062212', '062312', 
										  '062503', '062712', '062802', '063502', '063603', '070103', '070203', '070403', '070503', '070603', '070703', '070803', 
										  '070903', '071003', '071303', '071412', '071512', '071523', '071612', '071623', '071812', '071912', '072112', '072203', 
										  '072412', '072503', '072612', '072703', '072802', '072901', '073012', '073123', '073512', '073712', '073803', '073813', 
										  '074003', '074112', '074202', '074213', '074312', '074412', '074423', '074512', '074601', '074701', '074802', '074912', 
										  '075003', '075103', '075201', '075303', '075403', '075602', '075702', '080103', '080403', '080502', '080513', '080712', 
										  '080723', '080812', '080823', '080913', '080923', '081001', '081103', '081203', '081303', '081501', '081603', '081703', 
										  '081803', '081903', '082001', '082101', '082201', '082301', '082403', '082612', '082623', '082712', '082723', '082903', 
										  '083203', '083312', '083403', '083503', '083603', '083712', '084003', '084101', '084123', '084203', '084301', '084403', 
										  '092391', '092394', '092395', '092413');
			for ( $i=0; $i<count($PL_CODE); $i++ ) { 
				if($DPISDB=="odbc" || $DPISDB=="mysql") 
					$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE,
									PL_TYPE)
									SELECT '5'+mid(PL_CODE,2), PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
									PL_TYPE FROM PER_LINE WHERE PL_CODE = '$PL_CODE[$i]' ";    
				elseif($DPISDB=="oci8") 
					$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE,
									 PL_TYPE)
									 SELECT '5'||substr(PL_CODE,2), PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
									 PL_TYPE FROM PER_LINE WHERE PL_CODE = '$PL_CODE[$i]' "; 
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '5'+mid(PL_CODE,2) WHERE PL_CODE IN 
				   ('010108', '010209', '010307', '010403', '010501', '010703', '010903', '011013', '011203', '011211', '011503', '011612', '011712', 
					'011723', '012003', '012212', '012302', '012403', '012603', '012613', '012626', '012703', '012812', '012833', '012903', '013003', 
					'013103', '013303', '013405', '013503', '020103', '020212', '020303', '020412', '020423', '020502', '020603', '020812', '020823', 
					'020903', '021003', '021203', '021301', '021412', '021423', '021512', '021523', '021603', '021703', '021903', '022003', '022103', 
					'022203', '022312', '022412', '022423', '022513', '022712', '022723', '023203', '023302', '023503', '023612', '023623', '023903', 
					'030103', '030212', '030306', '030403', '030503', '030601', '031212', '031813', '031801', '031912', '031923', '032003', '032011', 
					'032423', '032512', '032523', '032601', '040103', '040203', '040312', '040323', '040412', '040503', '040612', '040703', '040912', 
					'041003', '041112', '041412', '041443', '041503', '050103', '050212', '050223', '050303', '050403', '050503', '050803', '050903', 
					'051003', '051103', '051303', '051403', '051512', '051603', '051712', '051803', '060104', '060204', '060304', '060403', '060603', 
					'060703', '060802', '060813', '061203', '061502', '061514', '061523', '061712', '061723', '061803', '061902', '061913', '062102', 
					'062212', '062312', '062503', '062712', '062802', '063502', '063603', '070103', '070203', '070403', '070503', '070603', '070703', 
					'070803', '070903', '071003', '071303', '071412', '071512', '071523', '071612', '071623', '071812', '071912', '072112', '072203', 
					'072412', '072503', '072612', '072703', '072802', '072901', '073012', '073123', '073512', '073712', '073803', '073813', '074003', 
					'074112', '074202', '074213', '074312', '074412', '074423', '074512', '074601', '074701', '074802', '074912', '075003', '075103', 
					'075201', '075303', '075403', '075602', '075702', '080103', '080403', '080502', '080513', '080712', '080723', '080812', '080823', 
					'080913', '080923', '081001', '081103', '081203', '081303', '081501', '081603', '081703', '081803', '081903', '082001', '082101', 
					'082201', '082301', '082403', '082612', '082623', '082712', '082723', '082903', '083203', '083312', '083403', '083503', '083603', 
					'083712', '084003', '084101', '084123', '084203', '084301', '084403', '092391', '092394', '092395', '092413') ";    
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '5'||substr(PL_CODE,2) WHERE PL_CODE IN 
					('010108', '010209', '010307', '010403', '010501', '010703', '010903', '011013', '011203', '011211', '011503', '011612', '011712', 
					 '011723', '012003', '012212', '012302', '012403', '012603', '012613', '012626', '012703', '012812', '012833', '012903', '013003', 
					 '013103', '013303', '013405', '013503', '020103', '020212', '020303', '020412', '020423', '020502', '020603', '020812', '020823', 
					 '020903', '021003', '021203', '021301', '021412', '021423', '021512', '021523', '021603', '021703', '021903', '022003', '022103', 
					 '022203', '022312', '022412', '022423', '022513', '022712', '022723', '023203', '023302', '023503', '023612', '023623', '023903', 
					 '030103', '030212', '030306', '030403', '030503', '030601', '031212', '031813', '031801', '031912', '031923', '032003', '032011', 
					 '032423', '032512', '032523', '032601', '040103', '040203', '040312', '040323', '040412', '040503', '040612', '040703', '040912', 
					 '041003', '041112', '041412', '041443', '041503', '050103', '050212', '050223', '050303', '050403', '050503', '050803', '050903', 
					 '051003', '051103', '051303', '051403', '051512', '051603', '051712', '051803', '060104', '060204', '060304', '060403', '060603', 
					 '060703', '060802', '060813', '061203', '061502', '061514', '061523', '061712', '061723', '061803', '061902', '061913', '062102', 
					 '062212', '062312', '062503', '062712', '062802', '063502', '063603', '070103', '070203', '070403', '070503', '070603', '070703', 
					 '070803', '070903', '071003', '071303', '071412', '071512', '071523', '071612', '071623', '071812', '071912', '072112', '072203', 
					 '072412', '072503', '072612', '072703', '072802', '072901', '073012', '073123', '073512', '073712', '073803', '073813', '074003', 
					 '074112', '074202', '074213', '074312', '074412', '074423', '074512', '074601', '074701', '074802', '074912', '075003', '075103', 
					 '075201', '075303', '075403', '075602', '075702', '080103', '080403', '080502', '080513', '080712', '080723', '080812', '080823', 
					 '080913', '080923', '081001', '081103', '081203', '081303', '081501', '081603', '081703', '081803', '081903', '082001', '082101', 
					 '082201', '082301', '082403', '082612', '082623', '082712', '082723', '082903', '083203', '083312', '083403', '083503', '083603', 
					 '083712', '084003', '084101', '084123', '084203', '084301', '084403', '092391', '092394', '092395', '092413') ";    
			
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE)
							  VALUES ('510109', '�ѡ�����á�÷ٵ', '�ѡ�����á�÷ٵ', 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE)
							  VALUES ('510210', '����Ǩ�Ҫ��á��', '����Ǩ�Ҫ��á��', 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE)
							  VALUES ('510308', '����ӹ�¡��', '����ӹ�¡��', 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE)
							  VALUES ('510404', '�ѡ���������û���ͧ��ͧ���', '�ѡ���������û���ͧ��ͧ���', 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE)
							  VALUES ('510502', '��Ҿ�ѡ�ҹ���������û���ͧ��ͧ���', '��Ҿ�ѡ�ҹ���������û���ͧ��ͧ���', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE)
							  VALUES ('511014', '�ѡ෤��������ʹ��', '�ѡ෤��������ʹ��', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE)
							  VALUES ('511104', '�ѡ�Ѵ��çҹ�����', '�ѡ�Ѵ��çҹ�����', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE)
							  VALUES ('520104', '�ѡ���������Ѱ����ˡԨ', '�ѡ���������Ѱ����ˡԨ', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE)
							  VALUES ('560105', '�ѡ���ᾷ��Ἱ��', '�ѡ���ᾷ��Ἱ��', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE)
							  VALUES ('561204', '�ѡ�Ե�Է�Ҥ�ԹԤ', '�ѡ�Ե�Է�Ҥ�ԹԤ', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE)
							  VALUES ('561914', '�ѡ�Ԩ�����ӺѴ', '�ѡ�Ԩ�����ӺѴ', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE)
							  VALUES ('561915', '�ѡ�Ǫ��ʵ�������ͤ�������', '�ѡ�Ǫ��ʵ�������ͤ�������', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE)
							  VALUES ('570704', '�ѡ�Ԫҡ�þ�ѧ�ҹ', '�ǡ.��ѧ�ҹ', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE)
							  VALUES ('575612', '�ѡ����ػ�ó�', '�ѡ����ػ�ó�', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE)
							  VALUES ('584401', '��Ҿ�ѡ�ҹ�����Թ�Ҥҷ�Ѿ���Թ', '��Ҿ�ѡ�ҹ�����Թ�Ҥҷ�Ѿ���Թ', 1, $SESS_USERID, '$UPDATE_DATE', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE)
							  VALUES ('584555', '��Ҿ�ѡ�ҹ�Ѳ���ѧ��', '��Ҿ�ѡ�ҹ�Ѳ���ѧ��', 1, $SESS_USERID, '$UPDATE_DATE', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE)
							  VALUES ('584578', '�ѡ�Ѳ���ѧ��', '�ѡ�Ѳ���ѧ��', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_NAME = '����Ǩ�Ҫ��á�з�ǧ', PL_SHORTNAME = '����Ǩ�Ҫ��á�з�ǧ' 
							  WHERE PL_CODE = '510209' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_NAME = '�ѡ���������º�����Ἱ', PL_SHORTNAME = '�ѡ���������º�����Ἱ' 
							  WHERE PL_CODE = '510703' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_NAME = '��Ҿ�ѡ�ҹ���ѡɳ�', PL_SHORTNAME = '��Ҿ�ѡ�ҹ���ѡɳ�' 
							  WHERE PL_CODE = '511211' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_NAME = '�ѡ��Ѿ�ҡúؤ��', PL_SHORTNAME = '�ѡ��Ѿ�ҡúؤ��' 
							  WHERE PL_CODE = '510903' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '510903' 
							  WHERE PL_CODE IN ('010903', '011403', '080203', '080303', '080603', '080613') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ��á��

			$cmd = " UPDATE PER_LINE SET PL_NAME = '�ѡ����¹�ԪҪվ', PL_SHORTNAME = '�ѡ����¹�ԪҪվ' 
							  WHERE PL_CODE = '511503' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '511612' WHERE PL_CODE IN ('011601', '011612', '011801', '011901') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ��á��

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '511712' WHERE PL_CODE IN ('011701', '011712') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ���˹�ҷ���ʴ�

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '512212' WHERE PL_CODE IN ('012201', '012212') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹʶԵ�

			$cmd = " UPDATE PER_LINE SET PL_NAME = '��Ҿ�ѡ�ҹ�ǪʶԵ�', PL_SHORTNAME = '��Ҿ�ѡ�ҹ�ǪʶԵ�' 
							  WHERE PL_CODE = '512302' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_NAME = '�ѡ�׺�ǹ�ͺ�ǹ', PL_SHORTNAME = '�ѡ�׺�ǹ�ͺ�ǹ' 
							  WHERE PL_CODE = '512603' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '512812' WHERE PL_CODE IN ('012801', '012812') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ�Ҫ�ѳ��

			$cmd = " UPDATE PER_LINE SET PL_NAME = '�ѡ��÷ٵ', PL_SHORTNAME = '�ѡ��÷ٵ' WHERE PL_CODE = '512903' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_NAME = '�ѡ��������ѹ��', PL_SHORTNAME = '�ѡ��������ѹ��' WHERE PL_CODE = '513003' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_NAME = '�ѡ�����ˡ��', PL_SHORTNAME = '�ѡ�����ˡ��' WHERE PL_CODE = '513103' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '520212' WHERE PL_CODE IN ('020201', '020212') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ��ä�ѧ

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '520412' WHERE PL_CODE IN ('020401', '020412') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ����Թ��кѭ��

			$cmd = " UPDATE PER_LINE SET PL_NAME = '��Ҿ�ѡ�ҹ���Թ', PL_SHORTNAME = '��Ҿ�ѡ�ҹ���Թ' WHERE PL_CODE = '520502' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_NAME = '�ѡ�Ԫҡ�õ�Ǩ�ͺ����', PL_SHORTNAME = '�ǡ.��Ǩ�ͺ����' 
							  WHERE PL_CODE = '520603' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '520812' WHERE PL_CODE IN ('020801', '020812') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ��Ǩ�ͺ�ѭ��

			$cmd = " UPDATE PER_LINE SET PL_NAME = '�ѡ�������짺����ҳ', PL_SHORTNAME = '�ѡ�������짺����ҳ' 
							  WHERE PL_CODE = '520903' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '521203' WHERE PL_CODE IN ('021203', '021313') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); �ѡ�Ԫҡ����šҡ�

			$cmd = " UPDATE PER_LINE SET PL_NAME = '�ѡ�Ԫҡ����šҡ�', PL_SHORTNAME = '�ǡ.��šҡ�' WHERE PL_CODE = '521203' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_NAME = '��Ҿ�ѡ�ҹ��šҡ�', PL_SHORTNAME = '��Ҿ�ѡ�ҹ��šҡ�' 
							  WHERE PL_CODE = '521301' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '521412' WHERE PL_CODE IN ('021401', '021412', '021801') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ��þ���Ե

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '521512' WHERE PL_CODE IN ('021501', '021512') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ��þҡ�

			$cmd = " UPDATE PER_LINE SET PL_NAME = '�ѡ��Ǩ�ͺ����', PL_SHORTNAME = '�ѡ��Ǩ�ͺ����' WHERE PL_CODE = '521703' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '521423' WHERE PL_CODE IN ('021423', '021813') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); �ѡ�Ԫҡ����þ���Ե

			$cmd = " UPDATE PER_LINE SET PL_NAME = '�ѡ�Ԫҡ������������ŧ�ع', PL_SHORTNAME = '�ǡ.����������ŧ�ع' 
							  WHERE PL_CODE = '522103' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_NAME = '��Ҿ�ѡ�ҹ��������ص��ˡ���', PL_SHORTNAME = '��Ҿ�ѡ�ҹ��������ص��ˡ���' 
							  WHERE PL_CODE = '523302' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '521301' WHERE PL_CODE IN ('021301', '023801', '023812') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ��šҡ�

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '522312' WHERE PL_CODE IN ('022301', '022312') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ��������ˡó�

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '522412' WHERE PL_CODE IN ('022401', '022412') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ��þҳԪ��

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '522712' WHERE PL_CODE IN ('022701', '022712') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ��觵ǧ�Ѵ

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '523612' WHERE PL_CODE IN ('023601', '023612') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ��Ѿ�ҡøó�

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '530212' WHERE PL_CODE IN ('030201', '030212') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ����

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '530601' WHERE PL_CODE IN ('030601', '030701') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ�Թ����

			$cmd = " UPDATE PER_LINE SET PL_NAME = '��Ҿ�ѡ�ҹ�Թ����', PL_SHORTNAME = '��Ҿ�ѡ�ҹ�Թ����' 
							  WHERE PL_CODE = '530601' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '531212' WHERE PL_CODE IN ('031201', '031212') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ�������

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '531801' WHERE PL_CODE IN ('031801', '032401', '032412') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ������Ъ�����ѹ��

			$cmd = " UPDATE PER_LINE SET PL_NAME = '��Ҿ�ѡ�ҹ������Ъ�����ѹ��', PL_SHORTNAME = '��Ҿ�ѡ�ҹ������Ъ�����ѹ��' 	
							  WHERE PL_CODE = '531801' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '531813' WHERE PL_CODE IN ('031703', '031813') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); �ѡ��Ъ�����ѹ��

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '531912' WHERE PL_CODE IN ('031901', '031912') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ��â���

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '532003' WHERE PL_CODE IN ('032003', '032103', '032203', '032303') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); �ѡ���������Ū�

			$cmd = " UPDATE PER_LINE SET PL_NAME = '�ѡ���������Ū�', PL_SHORTNAME = '�ѡ���������Ū�' WHERE PL_CODE = '532003' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '532011' WHERE PL_CODE IN ('032011', '032311') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ����С�������§ҹ����

			$cmd = " UPDATE PER_LINE SET PL_NAME = '����С�������§ҹ����', PL_SHORTNAME = '����С�������§ҹ����' 
							  WHERE PL_CODE = '532011' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '532512' WHERE PL_CODE IN ('032501', '032512') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ�ʵ��ȹ�֡��

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '532601' WHERE PL_CODE IN ('032601', '030901') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ���˹�ҷ���Ҩ�������á�úԹ

			$cmd = " UPDATE PER_LINE SET PL_NAME = '��Ҿ�ѡ�ҹ������á�úԹ', PL_SHORTNAME = '��Ҿ�ѡ�ҹ������á�úԹ' 
							  WHERE PL_CODE = '532601' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '540312' WHERE PL_CODE IN ('040301', '040312') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��ª�ҧ�Ż�зҹ

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '540412' WHERE PL_CODE IN ('040401', '040412') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ����ɵ�

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '540612' WHERE PL_CODE IN ('040601', '040612') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ������

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '540912' WHERE PL_CODE IN ('040901', '040912') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ�����

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '541112' WHERE PL_CODE IN ('041101', '041112') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ�ѵǺ��

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '541412' WHERE PL_CODE IN ('041401', '041412') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ�ˡԨ����ɵ�

			$cmd = " UPDATE PER_LINE SET PL_NAME = '�ѡ�Ԫҡ�û���ٻ���Թ', PL_SHORTNAME = '�ǡ.����ٻ���Թ' 
							  WHERE PL_CODE = '541503' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '550212' WHERE PL_CODE IN ('050201', '050212') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ�Է����ʵ��

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '550503' WHERE PL_CODE IN ('050503', '050603') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); �ѡ������������

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '550803' WHERE PL_CODE IN ('051203', '050803') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); �ѡ����Է���ѧ��

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '551512' WHERE PL_CODE IN ('051501', '051512') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ�ط��Է��

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '551712' WHERE PL_CODE IN ('051701', '051712') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ�صع����Է��

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '561502' WHERE PL_CODE IN ('061502', '061601') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Һ��෤�Ԥ

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '561712' WHERE PL_CODE IN ('061701', '061712') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ�ѧ�ա��ᾷ��

			$cmd = " UPDATE PER_LINE SET PL_NAME = '��Ҿ�ѡ�ҹ�ѧ�ա��ᾷ��', PL_SHORTNAME = '��Ҿ�ѡ�ҹ�ѧ�ա��ᾷ��' 
							  WHERE PL_CODE = '561712' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_NAME = '��Ҿ�ѡ�ҹ�Ҫ�ǺӺѴ', PL_SHORTNAME = '��Ҿ�ѡ�ҹ�Ҫ�ǺӺѴ' 
							  WHERE PL_CODE = '561902' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '562212' WHERE PL_CODE IN ('062201', '062212') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ�Է����ʵ����ᾷ��

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '562312' WHERE PL_CODE IN ('062301', '062312') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ���Ѫ����

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '562503' 
							  WHERE PL_CODE IN ('061303', '061403', '062503', '063023', '063123') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); �ѡ�Ԫҡ���Ҹ�ó�آ

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '562802' WHERE PL_CODE IN ('062601', '062802') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ�ѹ��Ҹ�ó�آ

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '562712' WHERE PL_CODE IN ('062701', '062712') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ�Ǫ������鹿�

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '563502' 
							  WHERE PL_CODE IN ('063001', '063012', '063101', '063112', '063201', '063212', '063502') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ�Ҹ�ó�آ

			$cmd = " UPDATE PER_LINE SET PL_NAME = '��Ҿ�ѡ�ҹ�Ҹ�ó�آ', PL_SHORTNAME = '��Ҿ�ѡ�ҹ�Ҹ�ó�آ' 
							  WHERE PL_CODE = '563502' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '571512' WHERE PL_CODE IN ('071501', '071512') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��ª�ҧ�ѧ�Ѵ

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '571612' WHERE PL_CODE IN ('071601', '071612') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��ª�ҧ���Ǩ

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '571812' WHERE PL_CODE IN ('071801', '071812') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��ª�ҧ�ش�͡

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '571912' 
							  WHERE PL_CODE IN ('071901', '071912', '072001', '072003', '072301') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��ª�ҧ����ͧ��

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '572112' WHERE PL_CODE IN ('072101', '072112') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��ª�ҧ�ش�͡

			$cmd = " UPDATE PER_LINE SET PL_NAME = '��Ҿ�ѡ�ҹ��Ǩ�ç�ҹ', PL_SHORTNAME = '��Ҿ�ѡ�ҹ��Ǩ�ç�ҹ' 
							  WHERE PL_CODE = '572802' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_NAME = '��Ҿ�ѡ�ҹ����ͧ����������', PL_SHORTNAME = '��Ҿ�ѡ�ҹ����ͧ����������' 
							  WHERE PL_CODE = '572901' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '573012' 
							  WHERE PL_CODE IN ('073001', '073012', '073101', '073112', '073201', '073212') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��ª�ҧ俿��

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '573512' WHERE PL_CODE IN ('073501', '073512', '073601', '073612') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��ª�ҧ෤�Ԥ

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '573712' WHERE PL_CODE IN ('073701', '073712') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��ª�ҧ����ͧ���

			$cmd = " UPDATE PER_LINE SET PL_NAME = '�ѳ��ҡ�', PL_SHORTNAME = '�ѳ��ҡ�' 
							  WHERE PL_CODE in ('074003', '574003') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_NAME = '��Ҿ�ѡ�ҹ�͡Ẻ��Ե�ѳ��', PL_SHORTNAME = '��Ҿ�ѡ�ҹ�͡Ẻ��Ե�ѳ��' 
							  WHERE PL_CODE = '574202' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '574112' WHERE PL_CODE IN ('074101', '074112') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��ª�ҧ�͡Ẻ����

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '574312' WHERE PL_CODE IN ('074301', '074312') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��ª�ҧ��¹Ẻ

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '574412' WHERE PL_CODE IN ('074401', '074412') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��ª�ҧ��Ż�

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '574512' WHERE PL_CODE IN ('074501', '074512') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��ª�ҧ��Ż����

			$cmd = " UPDATE PER_LINE SET PL_NAME = '��Ҿ�ѡ�ҹ�ԢԵ', PL_SHORTNAME = '��Ҿ�ѡ�ҹ�ԢԵ' WHERE PL_CODE = '574601' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_NAME = '��ª�ҧ����', PL_SHORTNAME = '��ª�ҧ����' WHERE PL_CODE = '574701' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_NAME = '��ª�ҧ�����', PL_SHORTNAME = '��ª�ҧ�����' WHERE PL_CODE = '574802' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '574802' WHERE PL_CODE IN ('074802', '074811') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��ª�ҧ�����

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '574912' WHERE PL_CODE IN ('074901', '074912') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��ª�ҧ�Ҿ

			$cmd = " UPDATE PER_LINE SET PL_NAME = '�ѡ�Ԫҡ��Ἱ����Ҿ����', PL_SHORTNAME = '�ǡ.Ἱ����Ҿ����' 
							  WHERE PL_CODE = '575103' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '580712' WHERE PL_CODE IN ('080701', '080712') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹͺ����н֡�ԪҪվ

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '580812' WHERE PL_CODE IN ('080801', '080812') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ�Ѳ�ҽ�����ç�ҹ

			$cmd = " UPDATE PER_LINE SET PL_NAME = '��Ҿ�ѡ�ҹ�Ѳ�ҽ�����ç�ҹ', PL_SHORTNAME = '��Ҿ�ѡ�ҹ�Ѳ�ҽ�����ç�ҹ' 
							  WHERE PL_CODE = '580812' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_NAME = '�ѡ�Ԫҡ�þѲ�ҽ�����ç�ҹ', PL_SHORTNAME = '�ǡ.�Ѳ�ҽ�����ç�ҹ' 
							  WHERE PL_CODE = '580823' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_NAME = '��Ҿ�ѡ�ҹ��ͧ��ش', PL_SHORTNAME = '��Ҿ�ѡ�ҹ��ͧ��ش' 
							  WHERE PL_CODE = '581501' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_NAME = '��Ҿ�ѡ�ҹ�ԾԸ�ѳ��', PL_SHORTNAME = '��Ҿ�ѡ�ҹ�ԾԸ�ѳ��' 
							  WHERE PL_CODE = '582001' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '582612' WHERE PL_CODE IN ('082601', '082612') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ�Ѳ�����

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '583203' WHERE PL_CODE IN ('083203', '084523') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); �ѡ�Ԫҡ���ç�ҹ

			$cmd = " UPDATE PER_LINE SET PL_NAME = '�ѡ��������ѧ���ͧ', PL_SHORTNAME = '�ѡ��������ѧ���ͧ' 
							  WHERE PL_CODE = '583403' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '583712' WHERE PL_CODE IN ('083701', '083712') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ�Ѳ�Ҫ����

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '584101' WHERE PL_CODE IN ('084101', '084412') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ���Թ

			//$cmd = " UPDATE PER_LINE SET PL_NAME = '��Ҿ�ѡ�ҹ���Թ', PL_SHORTNAME = '��Ҿ�ѡ�ҹ���Թ' WHERE PL_CODE = '584101' ";
			//$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			$cmd = " UPDATE PER_LINE SET PL_NAME = '�ѡ�Ԫҡ�èѴ�ҷ��Թ', PL_SHORTNAME = '�ǡ.�Ѵ�ҷ��Թ' 
							  WHERE PL_CODE = '584203' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_NAME = '��Ҿ�ѡ�ҹ�������˵�', PL_SHORTNAME = '��Ҿ�ѡ�ҹ�������˵�' 
							  WHERE PL_CODE = '584301' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			$cmd = " UPDATE PER_LINE SET PL_NAME = '�ѡ�����Թ�Ҥҷ�Ѿ���Թ', PL_SHORTNAME = '�ѡ�����Թ�Ҥҷ�Ѿ���Թ' 
							  WHERE PL_CODE = '584403' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '571412' WHERE PL_CODE IN ('071401', '071412') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��ª�ҧ�¸�

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '572412' WHERE PL_CODE IN ('072401', '072412') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��ª�ҧ����

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '572612' WHERE PL_CODE IN ('072601', '072612') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��ª�ҧ��Ǩ��Ҿö

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '582712' WHERE PL_CODE IN ('082701', '082712') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ�����ʹ�

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '583312' WHERE PL_CODE IN ('083301', '083312', '084501', '084512') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); ��Ҿ�ѡ�ҹ�ç�ҹ

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '583603' WHERE PL_CODE IN ('083603', '083723') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); �ѡ�Ԫҡ�þѲ�Ҫ����

			$cmd = " UPDATE PER_LINE SET PL_NAME = '��ѡ�ҹ�ͺ�ǹ��վ����', PL_SHORTNAME = '��ѡ�ҹ�ͺ�ǹ��վ����' 
							  WHERE PL_CODE = '512626' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_LINE_GROUP ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LINE_GROUP (LG_CODE, LG_NAME, LG_ACTIVE, UPDATE_USER, UPDATE_DATE)
			SELECT trim(PL_CODE), PL_NAME, PL_ACTIVE, $SESS_USERID, '$UPDATE_DATE' FROM PER_LINE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�û���ͧ' WHERE LG_CODE = '010603' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ǻ�������ص��ˡ���' WHERE LG_CODE = '023402' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�������Ѫ��' WHERE LG_CODE = '062301' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���������º�����Ἱ' WHERE LG_CODE in ('010703', '510703') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���������к��ҹ' WHERE LG_CODE = '010803' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��������ҹ�ؤ��' WHERE LG_CODE = '010903' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ѿ�ҡúؤ��' WHERE LG_CODE = '510903' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ţҹء����к����çҹ' WHERE LG_CODE = '011103' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��������ҹ����¹��ä��' WHERE LG_CODE = '022503' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ���ͺ' WHERE LG_CODE = '080203' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '������' WHERE LG_CODE in ('010108', '510108') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�����á�÷ٵ' WHERE LG_CODE in ('510109') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ǩ�Ҫ���' WHERE LG_CODE = '010209' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ǩ�Ҫ��á�з�ǧ' WHERE LG_CODE = '510209' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ǩ�Ҫ��á��' WHERE LG_CODE = '510210' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�����çҹ����ͧ' WHERE LG_CODE in ('010307', '510307') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�ӹ�¡��' WHERE LG_CODE = '510308' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԡ�û���ͧ' WHERE LG_CODE in ('010403', '510403') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '����ҹ�ҹ����ͧ' WHERE LG_CODE in ('010501', '510501') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���������û���ͧ��ͧ���' WHERE LG_CODE in ('510404', '510502') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���§ҹ����ͧ' WHERE LG_CODE = '010512' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�к��ҹ����������' WHERE LG_CODE = '011003' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Է�ҡ�ä���������' WHERE LG_CODE = '011013' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�ä���������' WHERE LG_CODE = '511013' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ��෤��������ʹ��' WHERE LG_CODE = '511014' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ѵ��çҹ�����' WHERE LG_CODE = '511104' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ���ѡɳ�' WHERE LG_CODE = '511211' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�û�л�' WHERE LG_CODE = '011313' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '������˹�ҷ��' WHERE LG_CODE = '011403' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��÷���¹�ԪҪվ' WHERE LG_CODE = '011503' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '����¹�ԪҪվ' WHERE LG_CODE = '511503' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			//$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�ѹ�֡������' WHERE LG_CODE = '011801' ";
			//$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�����մ' WHERE LG_CODE = '011901' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��á��' WHERE LG_CODE = '511612' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��ʴ�' WHERE LG_CODE = '511712' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�þ�ʴ�' WHERE LG_CODE = '511723' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�����çҹ��ʴ�' WHERE LG_CODE = '011734' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ��ʶԵ�' WHERE LG_CODE in ('012003', '512003') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ʶԵ�' WHERE LG_CODE = '512223' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��ԺѵԧҹʶԵ�' WHERE LG_CODE = '512212' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�ǪʶԵ�' WHERE LG_CODE = '512302' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Եԡ��' WHERE LG_CODE in ('012403', '512403') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Եԡ�� (��������Ҫ�)' WHERE LG_CODE = '012414' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Եԡ�� (�����¡�ɮա�)' WHERE LG_CODE = '012427' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�ҹ��ͧ�ء��' WHERE LG_CODE = '012503' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�׺�ǹ�ͺ�ǹ' WHERE LG_CODE in ('012603', '512603') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�÷ѳ��Է��' WHERE LG_CODE in ('012703', '512703') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�Ҫ�ѳ��' WHERE LG_CODE = '512812' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ���صԸ���' WHERE LG_CODE in ('012833', '512833') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�����çҹ�صԸ���' WHERE LG_CODE = '012846' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��÷ٵ' WHERE LG_CODE in ('012903', '512903') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��������ѹ��' WHERE LG_CODE in ('013003', '513003') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�����ˡ��' WHERE LG_CODE in ('013103', '513103') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���������ç����Ԩ��' WHERE LG_CODE = '013203' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�����оĵ�' WHERE LG_CODE in ('013303', '513303') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�����¡�ɮա�' WHERE LG_CODE = '513405' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ѳ���к��Ҫ���' WHERE LG_CODE = '513503' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�ä�ѧ' WHERE LG_CODE = '520103' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���������Ѱ����ˡԨ' WHERE LG_CODE = '520104' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��ѧ' WHERE LG_CODE = '520212' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�úѭ��' WHERE LG_CODE in ('020303', '520303') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ����Թ��кѭ��' WHERE LG_CODE = '520412' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ���Թ��кѭ��' WHERE LG_CODE in ('020423', '520423') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�����çҹ����Թ��кѭ��' WHERE LG_CODE = '020435' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���Թ' WHERE LG_CODE = '020502' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ���Թ' WHERE LG_CODE = '520502' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ǩ�ͺ����' WHERE LG_CODE = '020603' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�õ�Ǩ�ͺ����' WHERE LG_CODE = '520603' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ǩ�Թ�蹴Թ' WHERE LG_CODE = '020701' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��Ǩ�ͺ�ѭ��' WHERE LG_CODE = '520812' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�õ�Ǩ�ͺ�ѭ��' WHERE LG_CODE = '520823' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�������짺����ҳ' WHERE LG_CODE in ('020903', '520903') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ������' WHERE LG_CODE in ('021003', '521003') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '������������' WHERE LG_CODE = '021103' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�����Թ�ҡ�' WHERE LG_CODE = '021203' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ����šҡ�' WHERE LG_CODE = '521203' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��šҡ�' WHERE LG_CODE = '521301' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���¤Ǻ�����ШѴ��������þ���Ե' WHERE LG_CODE = '021401' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��þ���Ե' WHERE LG_CODE = '521412' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ����þ���Ե' WHERE LG_CODE in ('021423', '521423') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��þҡ�' WHERE LG_CODE = '521512' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ����þҡ�' WHERE LG_CODE in ('021523', '521523') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�����þҡ������' WHERE LG_CODE in ('021603', '521603') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ǩ�ͺ����' WHERE LG_CODE = '521703' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��µ�Ǩ�����þ���Ե' WHERE LG_CODE = '021801' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�����çҹ����Ҫ��ʴ�' WHERE LG_CODE = '021915' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�����ɰ�Ԩ' WHERE LG_CODE in ('022003', '522003') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '����������ŧ�ع' WHERE LG_CODE = '022103' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ������������ŧ�ع' WHERE LG_CODE = '522103' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ���ˡó�' WHERE LG_CODE in ('022203', '522203') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��������ˡó�' WHERE LG_CODE = '522312' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��þҳԪ��' WHERE LG_CODE = '522412' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ���ˡó�' WHERE LG_CODE in ('022203', '522203') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�þҳԪ��' WHERE LG_CODE in ('022423', '522423') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�õ�ͺ�ͺ�Է�Ժѵ�' WHERE LG_CODE in ('022513', '522513') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��觵ǧ�Ѵ' WHERE LG_CODE = '522712' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�ê�觵ǧ�Ѵ' WHERE LG_CODE in ('022723', '522723') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�û�Сѹ���' WHERE LG_CODE = '022803' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ǩ�Ԩ��û�Сѹ���' WHERE LG_CODE = '023003' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ���ص��ˡ���' WHERE LG_CODE in ('023203', '523203') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��������ص��ˡ���' WHERE LG_CODE = '023302' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��������ص��ˡ���' WHERE LG_CODE = '523302' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�����çҹ����ص��ˡ���' WHERE LG_CODE = '023415' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�ü�Ե�ѳ�������' WHERE LG_CODE in ('023503', '523503') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��Ѿ�ҡøó�' WHERE LG_CODE = '523612' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�÷�Ѿ�ҡøó�' WHERE LG_CODE in ('023623', '523623') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�õ�Ǩ�Թ�蹴Թ' WHERE LG_CODE = '023703' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ���ҵðҹ' WHERE LG_CODE in ('023903', '523903') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�â���' WHERE LG_CODE in ('030103', '530103') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ����' WHERE LG_CODE = '530212' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '����ͧ' WHERE LG_CODE in ('030306', '530306') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ǩ���' WHERE LG_CODE in ('030403', '530403') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Թ���������ҧ�����' WHERE LG_CODE = '030503' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Թ����' WHERE LG_CODE = '530503' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Թ����㹻����' WHERE LG_CODE = '030601' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�Թ����' WHERE LG_CODE = '530601' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��ͷ�������' WHERE LG_CODE = '030701' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '����ͧ�����Թ����' WHERE LG_CODE = '030803' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ǻ�����Ҩ÷ҧ�ҡ��' WHERE LG_CODE = '030901' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��úԹ' WHERE LG_CODE = '031001' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�������' WHERE LG_CODE = '531212' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '����ä��Ҥ�' WHERE LG_CODE = '031301' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�����ɳ������Ţ' WHERE LG_CODE = '031401' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '����ɳ������Ţ' WHERE LG_CODE = '031501' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ǩ����������' WHERE LG_CODE = '031604' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�û�Ъ�����ѹ��' WHERE LG_CODE = '031703' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ������Ъ�����ѹ��' WHERE LG_CODE = '531801' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ъ�����ѹ��' WHERE LG_CODE = '531813' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��â���' WHERE LG_CODE = '531912' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��â���' WHERE LG_CODE = '531923' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�����çҹ����' WHERE LG_CODE = '031935' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��§ҹ����' WHERE LG_CODE = '032011' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���͢���' WHERE LG_CODE = '032003' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���������Ū�' WHERE LG_CODE = '532003' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���º���§�͡���' WHERE LG_CODE = '032103' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ѵ��¡���Է���÷�ȹ�' WHERE LG_CODE = '032203' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��С�������§ҹ����' WHERE LG_CODE = '532011' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�������' WHERE LG_CODE = '532423' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�ʵ��ȹ�֡��' WHERE LG_CODE = '532512' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ���ʵ��ȹ�֡��' WHERE LG_CODE = '532523' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ������á�úԹ' WHERE LG_CODE = '532601' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ���ɵ�' WHERE LG_CODE in ('040103', '540103') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���Ǩ�Թ' WHERE LG_CODE in ('040203', '540203') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��ҧ�Ż�зҹ' WHERE LG_CODE = '540312' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���ǡ����Ż�зҹ' WHERE LG_CODE in ('040323', '540323') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ����ɵ�' WHERE LG_CODE = '540412' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�û�����' WHERE LG_CODE in ('040503', '540503') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ������' WHERE LG_CODE = '540612' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�û����' WHERE LG_CODE in ('040703', '540703') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�û��������' WHERE LG_CODE = '040803' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�����' WHERE LG_CODE = '540912' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ���ѵǺ��' WHERE LG_CODE in ('041003', '541003') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�ѵǺ��' WHERE LG_CODE = '541112' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��û���ѵ������' WHERE LG_CODE = '041201' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�ˡԨ�ɵ�' WHERE LG_CODE = '541412' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�������������ɵ�' WHERE LG_CODE in ('041443', '541443') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ѵ��û���ٻ���Թ�����ɵá���' WHERE LG_CODE = '041503' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�û���ٻ���Թ' WHERE LG_CODE = '541503' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Է����ʵ��' WHERE LG_CODE in ('050103', '550103') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���¹ѡ�Է����ʵ��' WHERE LG_CODE = '050201' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�Է����ʵ��' WHERE LG_CODE = '550212' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ե��Է����ʵ��' WHERE LG_CODE in ('050223', '550223') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�����������ԡ��' WHERE LG_CODE in ('050303', '550303') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���ԡ���ѧ��' WHERE LG_CODE in ('050403', '550403') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '������������' WHERE LG_CODE in ('050503', '550503') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��ü�Ե���ⷻ' WHERE LG_CODE = '050603' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�þ���Է��' WHERE LG_CODE = '050703' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '����Է���ѧ��' WHERE LG_CODE in ('050803', '550803') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ���ä�ת' WHERE LG_CODE in ('050903', '550903') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�ѵ��Է��' WHERE LG_CODE in ('051003', '551003') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�ծ�Է��' WHERE LG_CODE in ('051103', '551103') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�կ�Է���ѧ��' WHERE LG_CODE = '051203' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�øó��Է��' WHERE LG_CODE = '051303' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�ó��Է��' WHERE LG_CODE = '551303' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ���ط��Է��' WHERE LG_CODE in ('051403', '551403') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�ط��Է��' WHERE LG_CODE = '551512' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�صع����Է��' WHERE LG_CODE = '551603' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�صع����Է��' WHERE LG_CODE = '551712' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Է����ʵ����������' WHERE LG_CODE in ('051803', '551803') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ᾷ��' WHERE LG_CODE in ('060104', '560104') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ᾷ��Ἱ��' WHERE LG_CODE = '560105' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ���ѵ�ᾷ��' WHERE LG_CODE = '060304' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ���Է����ʵ����ᾷ��' WHERE LG_CODE = '060403' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Է����ʵ����ᾷ��' WHERE LG_CODE = '560403' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���Ѫ�Ԩ��' WHERE LG_CODE = '060503' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���Ѫ����' WHERE LG_CODE in ('060603', '560603') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ������������' WHERE LG_CODE in ('060703', '560703') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '����ҡ��' WHERE LG_CODE = '060802' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ����ҡ��' WHERE LG_CODE = '560802' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ������ҡ��' WHERE LG_CODE = '060813' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '����ҡ��' WHERE LG_CODE = '560813' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�����Է��' WHERE LG_CODE = '060823' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�������������' WHERE LG_CODE = '060903' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '����������' WHERE LG_CODE = '061003' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ǩ����������' WHERE LG_CODE = '061103' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ե�Է��' WHERE LG_CODE in ('061203', '561203') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ե�Է�Ҥ�ԹԤ' WHERE LG_CODE = '561204' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ѵ����آ�֡��' WHERE LG_CODE = '061303' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ���آ��Ժ��' WHERE LG_CODE = '061403' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��þ�Һ��෤�Ԥ' WHERE LG_CODE = '061502' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Һ��෤�Ԥ' WHERE LG_CODE = '561502' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�þ�Һ��' WHERE LG_CODE in ('061514', '561514') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��þ�Һ���ԪҪվ' WHERE LG_CODE = '061523' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Һ���ԪҪվ' WHERE LG_CODE = '561523' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��硫����' WHERE LG_CODE = '061701' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�ѧ�ա��ᾷ��' WHERE LG_CODE = '061712' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�ѧ�ա��ᾷ��' WHERE LG_CODE = '561712' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ���ѧ�ա��ᾷ��' WHERE LG_CODE = '061723' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�ѧ�ա��ᾷ��' WHERE LG_CODE = '561723' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '����Ҿ�ӺѴ' WHERE LG_CODE in ('061803', '561803') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ҫ�ǺӺѴ' WHERE LG_CODE = '061902' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�Ҫ�ǺӺѴ' WHERE LG_CODE = '561902' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ���Ҫ�ǺӺѴ' WHERE LG_CODE in ('061913', '561913') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԩ�����ӺѴ' WHERE LG_CODE = '561914' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ǫ��ʵ�������ͤ�������' WHERE LG_CODE = '561915' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�Է����ʵ����ᾷ��' WHERE LG_CODE = '562212' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ���Ѫ����' WHERE LG_CODE = '562312' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��ԡ���Ǫ�ѳ��' WHERE LG_CODE = '062403' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ���Ҹ�ó�آ' WHERE LG_CODE in ('062503', '562503') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���·ѹ�ᾷ��' WHERE LG_CODE = '062601' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�ѹ��Ҹ�ó�آ' WHERE LG_CODE = '062802' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�ѹ��Ҹ�ó�آ' WHERE LG_CODE = '562802' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�Ǫ������鹿�' WHERE LG_CODE = '562712' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ����������آ�Ҿ' WHERE LG_CODE = '063023' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�äǺ����ä' WHERE LG_CODE = '063123' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��ا������Ҹ�ó�آ' WHERE LG_CODE = '063401' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�Ҹ�ó�آ' WHERE LG_CODE = '563502' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '෤�Ԥ���ᾷ��' WHERE LG_CODE = '563603' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���ǡ���' WHERE LG_CODE in ('070103', '570103') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���ǡ����¸�' WHERE LG_CODE in ('070203', '570203') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���ǡ����ѧ���ͧ' WHERE LG_CODE = '070303' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���ǡ�������ͧ��' WHERE LG_CODE in ('070403', '570403') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���ǡ���俿��' WHERE LG_CODE in ('070503', '570503') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���ǡ�������ͧ���' WHERE LG_CODE in ('070603', '570603') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���ǡ������������' WHERE LG_CODE in ('070703', '570703') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�þ�ѧ�ҹ' WHERE LG_CODE = '570704' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���ǡ�����ˡ��' WHERE LG_CODE in ('070803', '570803') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���ǡ�������ɵ�' WHERE LG_CODE in ('070903', '570903') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���ǡ������������' WHERE LG_CODE in ('071003', '571003') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���ǡ����ç�ҹ' WHERE LG_CODE = '071103' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�����çҹ�͡Ẻ������ҧ' WHERE LG_CODE = '071205' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�á�һ��' WHERE LG_CODE in ('071303', '571303') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�����çҹ��һ��' WHERE LG_CODE = '071316' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��ҧ�¸�' WHERE LG_CODE = '571412' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��ҧ�ѧ�Ѵ' WHERE LG_CODE = '571512' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���ǡ����ѧ�Ѵ' WHERE LG_CODE in ('071523', '571523') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��ҧ���Ǩ' WHERE LG_CODE = '571612' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���ǡ������Ǩ' WHERE LG_CODE in ('071623', '571623') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��ҧ�ش�͡' WHERE LG_CODE = '571812' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��ҧ����ͧ��' WHERE LG_CODE = '571912' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��ҧ�ҡ���ҹ' WHERE LG_CODE = '572112' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��ҧ������' WHERE LG_CODE in ('072203', '572203') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��ҧ����' WHERE LG_CODE = '572412' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ǩ��Ҿ�ҡ���ҹ' WHERE LG_CODE = '072503' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ǩ�ͺ������ʹ��´�ҹ��úԹ' WHERE LG_CODE = '572503' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��ҧ��Ǩ��Ҿö' WHERE LG_CODE = '572612' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ǩ����' WHERE LG_CODE in ('072703', '572703') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ǩ�ç�ҹ' WHERE LG_CODE = '072802' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��Ǩ�ç�ҹ' WHERE LG_CODE = '572802' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�������ͧ����������' WHERE LG_CODE = '072901' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ����ͧ����������' WHERE LG_CODE = '572901' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��ҧ俿��' WHERE LG_CODE = '573012' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���ǡ���俿���������' WHERE LG_CODE in ('073123', '573123') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�ѡ��������' WHERE LG_CODE = '073401' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��ҧ෤�Ԥ' WHERE LG_CODE = '573512' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��ҧ����ͧ���' WHERE LG_CODE = '573712' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ʶһѵ¡���' WHERE LG_CODE in ('073803', '573803') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '����ʶһѵ¡���' WHERE LG_CODE in ('073813', '573813') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = 'ʶһѵ¡����ѧ���ͧ' WHERE LG_CODE = '073903' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�ѳ����Ż�' WHERE LG_CODE in ('074003', '574003') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��ҧ�͡Ẻ����' WHERE LG_CODE = '574112' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�͡Ẻ��Ե�ѳ��' WHERE LG_CODE = '574202' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ���͡Ẻ��Ե�ѳ��' WHERE LG_CODE = '574213' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��ҧ��¹Ẻ' WHERE LG_CODE = '574312' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��ҧ��Ż�' WHERE LG_CODE = '574412' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�ê�ҧ��Ż�' WHERE LG_CODE in ('074423', '574423') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��ҧ��Ż����' WHERE LG_CODE = '574512' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�ԢԵ' WHERE LG_CODE = '074601' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�ԢԵ' WHERE LG_CODE = '574601' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��ҧ����' WHERE LG_CODE = '574701' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��ҧ�����' WHERE LG_CODE = '574802' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��ҧ�Ҿ' WHERE LG_CODE = '574912' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ἱ����Ҿ����' WHERE LG_CODE = '075103' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ��Ἱ����Ҿ����' WHERE LG_CODE = '575103' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Եá���' WHERE LG_CODE in ('075303', '575303') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��е��ҡ���' WHERE LG_CODE in ('075403', '575403') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ����ػ�ó�' WHERE LG_CODE = '575602' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '����ػ�ó�' WHERE LG_CODE = '575612' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��ҧ�ѹ�����' WHERE LG_CODE = '575702' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ���֡��' WHERE LG_CODE in ('080103', '580103') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���ŨѴ����֡��' WHERE LG_CODE = '080303' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ���֡�Ҿ����' WHERE LG_CODE in ('080513', '580513') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�֡ͺ��' WHERE LG_CODE = '080603' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ѳ�ҷ�Ѿ�ҡúؤ��' WHERE LG_CODE = '080613' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹͺ����н֡�ԪҪվ' WHERE LG_CODE = '580712' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ��ͺ����н֡�ԪҪվ' WHERE LG_CODE = '580723' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�Ѳ�ҽ�����ç�ҹ' WHERE LG_CODE = '580812' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�þѲ�ҽ�����ç�ҹ' WHERE LG_CODE = '580823' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�����������͹��þ��֡��' WHERE LG_CODE = '080903' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ѳ�ҡ�á���' WHERE LG_CODE in ('080913', '580913') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ѳ�ҡ�÷�ͧ�����' WHERE LG_CODE in ('080923', '580923') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '͹���ʹ�' WHERE LG_CODE in ('081001', '581001') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�ѡ����ʵ��' WHERE LG_CODE in ('081103', '581103') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�������˵�' WHERE LG_CODE in ('081203', '581203') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��ͧ��ش' WHERE LG_CODE = '081501' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ��ͧ��ش' WHERE LG_CODE = '581501' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��ҳ���' WHERE LG_CODE in ('081603', '581603') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�����çҹ��ҳ���' WHERE LG_CODE = '081615' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '������ҳ' WHERE LG_CODE in ('081703', '581703') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��ó�������������ʵ��' WHERE LG_CODE = '081803' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��ó��Ż�' WHERE LG_CODE = '581803' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���žԾԸ�ѳ��' WHERE LG_CODE = '082001' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�ԾԸ�ѳ��' WHERE LG_CODE = '582001' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�ү��Ż�' WHERE LG_CODE in ('082101', '582101') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�����ҧ���Ż�' WHERE LG_CODE in ('082201', '582201') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�յ��Ż�' WHERE LG_CODE in ('082301', '582301') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ���Ф���д����' WHERE LG_CODE in ('082403', '582403') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�Ѳ�����' WHERE LG_CODE = '582612' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ���Ѳ�����' WHERE LG_CODE in ('082623', '582623') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�����ʹ�' WHERE LG_CODE = '582712' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ����ʹ�' WHERE LG_CODE in ('082723', '582723') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԩ���ѧ��ʧ������' WHERE LG_CODE = '082803' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�ѧ��ʧ������' WHERE LG_CODE in ('082903', '582903') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��þѲ�����Ǫ�' WHERE LG_CODE = '083103' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ���ç�ҹ' WHERE LG_CODE in ('083203', '583203') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�ç�ҹ' WHERE LG_CODE = '583312' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��������ѧ���ͧ' WHERE LG_CODE in ('083403', '583403') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��üѧ���ͧ' WHERE LG_CODE in ('083503', '583503') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�����çҹ�ѧ���ͧ' WHERE LG_CODE = '083514' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�þѲ�Ҫ����' WHERE LG_CODE in ('083603', '583603') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�Ѳ�Ҫ����' WHERE LG_CODE = '583712' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ������Ǵ����' WHERE LG_CODE in ('084003', '584003') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�÷��Թ' WHERE LG_CODE = '584123' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ѵ�ҷ��Թ' WHERE LG_CODE = '084203' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ԫҡ�èѴ�ҷ��Թ' WHERE LG_CODE = '584203' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '���¹ѡ�������˵�' WHERE LG_CODE = '084301' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ���¹ѡ�������˵�' WHERE LG_CODE = '584301' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�����Թ�Ҥҷ�Ѿ���Թ' WHERE LG_CODE in ('084403', '584403') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�����Թ�Ҥҷ�Ѿ���Թ' WHERE LG_CODE = '584401' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ժѵԧҹ�Ѳ���ѧ��' WHERE LG_CODE = '584555' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '�Ѳ���ѧ��' WHERE LG_CODE = '584578' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LINE_GROUP (LG_CODE, LG_NAME, LG_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('510309', '�ӹ�¡��੾�д�ҹ', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('551109', '����ӹ�¡��੾�д�ҹ (�կ�Է��)', '����ӹ�¡��੾�д�ҹ (�կ�Է��)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 
							  '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '551109' WHERE PL_CODE = '051103' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('582309', '����ӹ�¡��੾�д�ҹ (�յ��Ż�)', '����ӹ�¡��੾�д�ҹ (�յ��Ż�)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 
							  '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '582309' WHERE PL_CODE = '082301' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('581209', '����ӹ�¡��੾�д�ҹ (�������˵�)', '����ӹ�¡��੾�д�ҹ (�������˵�)', 1, $SESS_USERID, '$UPDATE_DATE', 
							  3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '581209' WHERE PL_CODE = '081203' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('575309', '����ӹ�¡��੾�д�ҹ (�Եá���)', '����ӹ�¡��੾�д�ҹ (�Եá���)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 
							  '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '575309' WHERE PL_CODE = '075303' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('582209', '����ӹ�¡��੾�д�ҹ (�����ҧ���Ż�)', '����ӹ�¡��੾�д�ҹ (�����ҧ���Ż�)', 1, $SESS_USERID, '$UPDATE_DATE', 
							  3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '582209' WHERE PL_CODE = '082201' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('572709', '����ӹ�¡��੾�д�ҹ (��Ǩ����)', '����ӹ�¡��੾�д�ҹ (��Ǩ����)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 
							  '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '572709' WHERE PL_CODE = '072703' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('521709', '����ӹ�¡��੾�д�ҹ (��Ǩ�ͺ����)', '����ӹ�¡��੾�д�ҹ (��Ǩ�ͺ����)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '521709' WHERE PL_CODE = '021703' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('560209', '����ӹ�¡��੾�д�ҹ (�ѹ�ᾷ��)', '����ӹ�¡��੾�д�ҹ (�ѹ�ᾷ��)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 
							  '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '560209' WHERE PL_CODE = '060204' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('582109', '����ӹ�¡��੾�д�ҹ (�ү��Ż�)', '����ӹ�¡��੾�д�ҹ (�ү��Ż�)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 
							  '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '582109' WHERE PL_CODE = '082101' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('530309', '����ӹ�¡��੾�д�ҹ (����ͧ)', '����ӹ�¡��੾�д�ҹ (����ͧ)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 
							  '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '530309' WHERE PL_CODE = '030306' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('512409', '����ӹ�¡��੾�д�ҹ (�Եԡ��)', '����ӹ�¡��੾�д�ҹ (�Եԡ��)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 
							  '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '512409' WHERE PL_CODE = '012403' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('550229', '����ӹ�¡��੾�д�ҹ (�Ե��Է����ʵ��)', '����ӹ�¡��੾�д�ҹ (�Ե��Է����ʵ��)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '550229' WHERE PL_CODE = '050223' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('581309', '����ӹ�¡��੾�д�ҹ (��ó��ѡ��)', '����ӹ�¡��੾�д�ҹ (��ó��ѡ��)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 
							  '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '581309' WHERE PL_CODE = '081303' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('581619', '����ӹ�¡��੾�д�ҹ (��ҳ���)', '����ӹ�¡��੾�д�ҹ (��ҳ���)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 
							  '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '581619' WHERE PL_CODE = '081615' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('540319', '����ӹ�¡��੾�д�ҹ (��Ժѵԧҹ��ҧ�Ż�зҹ)', '����ӹ�¡��੾�д�ҹ (��Ժѵԧҹ��ҧ�Ż�зҹ)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '540319' WHERE PL_CODE = '040312' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('573519', '����ӹ�¡��੾�д�ҹ (��Ժѵԧҹ��ҧ෤�Ԥ)', '����ӹ�¡��੾�д�ҹ (��Ժѵԧҹ��ҧ෤�Ԥ)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '573519' WHERE PL_CODE in ('073512', '073612') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('571419', '����ӹ�¡��੾�д�ҹ (��Ժѵԧҹ��ҧ�¸�)', '����ӹ�¡��੾�д�ҹ (��Ժѵԧҹ��ҧ�¸�)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '571419' WHERE PL_CODE = '071412' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('571519', '����ӹ�¡��੾�д�ҹ (��Ժѵԧҹ��ҧ�ѧ�Ѵ)', '����ӹ�¡��੾�д�ҹ (��Ժѵԧҹ��ҧ�ѧ�Ѵ)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '571519' WHERE PL_CODE = '071512' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('574519', '����ӹ�¡��੾�д�ҹ (��Ժѵԧҹ��ҧ��Ż����)', '����ӹ�¡��੾�д�ҹ (��Ժѵԧҹ��ҧ��Ż����)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '574519' WHERE PL_CODE = '074512' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('575409', '����ӹ�¡��੾�д�ҹ (��е��ҡ���)', '����ӹ�¡��੾�д�ҹ (��е��ҡ���)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '575409' WHERE PL_CODE = '075403' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('584409', '����ӹ�¡��੾�д�ҹ (�����Թ�Ҥҷ�Ѿ���Թ)', '����ӹ�¡��੾�д�ҹ (�����Թ�Ҥҷ�Ѿ���Թ)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '584409' WHERE PL_CODE = '084403' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('512629', '����ӹ�¡��੾�д�ҹ (��ѡ�ҹ�ͺ�ǹ��վ����)', '����ӹ�¡��੾�д�ҹ (��ѡ�ҹ�ͺ�ǹ��վ����)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '512629' WHERE PL_CODE = '012626' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('560109', '����ӹ�¡��੾�д�ҹ (ᾷ��)', '����ӹ�¡��੾�д�ҹ (ᾷ��)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 
							  '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '560109' WHERE PL_CODE = '060104' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('550409', '����ӹ�¡��੾�д�ҹ (���ԡ���ѧ��)', '����ӹ�¡��੾�д�ҹ (���ԡ���ѧ��)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 
							  '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '550409' WHERE PL_CODE = '050403' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('581909', '����ӹ�¡��੾�д�ҹ (�ѳ���ѡ��)', '����ӹ�¡��੾�д�ҹ (�ѳ���ѡ��)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 
							  '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '581909' WHERE PL_CODE = '081903' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('560609', '����ӹ�¡��੾�д�ҹ (���Ѫ����)', '����ӹ�¡��੾�д�ҹ (���Ѫ����)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 
							  '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '560609' WHERE PL_CODE = '060603' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('574009', '����ӹ�¡��੾�д�ҹ (�ѳ����Ż�)', '����ӹ�¡��੾�д�ҹ (�ѳ����Ż�)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 
							  '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '574009' WHERE PL_CODE = '074003' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('571319', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�á�һ��)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�á�һ��)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '571319' WHERE PL_CODE = '071316' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('584209', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�èѴ�ҷ��Թ)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�èѴ�ҷ��Թ)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '584209' WHERE PL_CODE = '084203' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('522729', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�ê�觵ǧ�Ѵ)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�ê�觵ǧ�Ѵ)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '522729' WHERE PL_CODE = '022723' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('574429', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�ê�ҧ��Ż�)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�ê�ҧ��Ż�)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '574429' WHERE PL_CODE = '074423' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('520609', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�õ�Ǩ�ͺ����)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�õ�Ǩ�ͺ����)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '520609' WHERE PL_CODE = '020603' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('511029', '����ӹ�¡��੾�д�ҹ (�Ԫҡ��෤��������ʹ��)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ��෤��������ʹ��)', 1, 
							  $SESS_USERID, '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('551309', '����ӹ�¡��੾�д�ҹ (�ó��Է��)', '����ӹ�¡��੾�д�ҹ (�ó��Է��)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 
							  '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '551309' WHERE PL_CODE = '051303' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('520309', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�úѭ��)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�úѭ��)', 1, $SESS_USERID, '$UPDATE_DATE', 
							  3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '520309' WHERE PL_CODE = '020303' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('523509', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�ü�Ե�ѳ�������)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�ü�Ե�ѳ�������)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '523509' WHERE PL_CODE = '023503' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('575109', '����ӹ�¡��੾�д�ҹ (�Ԫҡ��Ἱ����Ҿ����)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ��Ἱ����Ҿ����)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '575109' WHERE PL_CODE = '075103' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('561519', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�þ�Һ��)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�þ�Һ��)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '561519' WHERE PL_CODE = '061514' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('521009', '����ӹ�¡��੾�д�ҹ (�Ԫҡ������)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ������)', 1, $SESS_USERID, '$UPDATE_DATE', 
							  3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '521009' WHERE PL_CODE = '021003' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('523909', '����ӹ�¡��੾�д�ҹ (�Ԫҡ���ҵðҹ)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ���ҵðҹ)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '523909' WHERE PL_CODE = '023903' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('550909', '����ӹ�¡��੾�д�ҹ (�Ԫҡ���ä�ת)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ���ä�ת)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '550909' WHERE PL_CODE = '050903' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('582409', '����ӹ�¡��੾�д�ҹ (�Ԫҡ���Ф���д����)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ���Ф���д����)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '582409' WHERE PL_CODE = '082403' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('522009', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�����ɰ�Ԩ)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�����ɰ�Ԩ)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '522009' WHERE PL_CODE = '022003' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('541449', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�������������ɵ�)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�������������ɵ�)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '541449' WHERE PL_CODE = '041443' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('512009', '����ӹ�¡��੾�д�ҹ (�Ԫҡ��ʶԵ�)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ��ʶԵ�)', 1, $SESS_USERID, '$UPDATE_DATE', 
							  3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '512009' WHERE PL_CODE = '012003' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('560709', '����ӹ�¡��੾�д�ҹ (�Ԫҡ������������)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ������������)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '560709' WHERE PL_CODE = '060703' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('551409', '����ӹ�¡��੾�д�ҹ (�Ԫҡ���ط��Է��)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ���ط��Է��)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '551409' WHERE PL_CODE = '051403' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('511019', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�ä���������)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�ä���������)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '511019' WHERE PL_CODE = '011013' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('550109', '����ӹ�¡��੾�д�ҹ (�Է����ʵ��)', '����ӹ�¡��੾�д�ҹ (�Է����ʵ��)', 1, $SESS_USERID, '$UPDATE_DATE', 
							  3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '550109' WHERE PL_CODE = '050103' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('560409', '����ӹ�¡��੾�д�ҹ (�Է����ʵ����ᾷ��)', '����ӹ�¡��੾�д�ҹ (�Է����ʵ����ᾷ��)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '560409' WHERE PL_CODE = '060403' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('551809', '����ӹ�¡��੾�д�ҹ (�Է����ʵ����������)', '����ӹ�¡��੾�д�ҹ (�Է����ʵ����������)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '551809' WHERE PL_CODE = '051803' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('570109', '����ӹ�¡��੾�д�ҹ (���ǡ���)', '����ӹ�¡��੾�д�ҹ (���ǡ���)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 
							  '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '570109' WHERE PL_CODE = '070103' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('570909', '����ӹ�¡��੾�д�ҹ (���ǡ�������ɵ�)', '����ӹ�¡��੾�д�ҹ (���ǡ�������ɵ�)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '570909' WHERE PL_CODE = '070903' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('570409', '����ӹ�¡��੾�д�ҹ (���ǡ�������ͧ��)', '����ӹ�¡��੾�д�ҹ (���ǡ�������ͧ��)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '570409' WHERE PL_CODE = '070403' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('540329', '����ӹ�¡��੾�д�ҹ (���ǡ����Ż�зҹ)', '����ӹ�¡��੾�д�ҹ (���ǡ����Ż�зҹ)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '540329' WHERE PL_CODE = '040323' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('571009', '����ӹ�¡��੾�д�ҹ (���ǡ������������)', '����ӹ�¡��੾�д�ҹ (���ǡ������������)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '571009' WHERE PL_CODE = '071003' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('570709', '����ӹ�¡��੾�д�ҹ (���ǡ������������)', '����ӹ�¡��੾�д�ҹ (���ǡ������������)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '570709' WHERE PL_CODE = '070703' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('570509', '����ӹ�¡��੾�д�ҹ (���ǡ���俿��)', '����ӹ�¡��੾�д�ҹ (���ǡ���俿��)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '570509' WHERE PL_CODE = '070503' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('570209', '����ӹ�¡��੾�д�ҹ (���ǡ����¸�)', '����ӹ�¡��੾�д�ҹ (���ǡ����¸�)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '570209' WHERE PL_CODE = '070203' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('571529', '����ӹ�¡��੾�д�ҹ (���ǡ����ѧ�Ѵ)', '����ӹ�¡��੾�д�ҹ (���ǡ����ѧ�Ѵ)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '571529' WHERE PL_CODE = '071523' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('571629', '����ӹ�¡��੾�д�ҹ (���ǡ������Ǩ)', '����ӹ�¡��੾�д�ҹ (���ǡ������Ǩ)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '571629' WHERE PL_CODE = '071623' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('570609', '����ӹ�¡��੾�д�ҹ (���ǡ�������ͧ���)', '����ӹ�¡��੾�д�ҹ (���ǡ�������ͧ���)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '570609' WHERE PL_CODE = '070603' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('573809', '����ӹ�¡��੾�д�ҹ (ʶһѵ¡���)', '����ӹ�¡��੾�д�ҹ (ʶһѵ¡���)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '573809' WHERE PL_CODE = '073803' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('551009', '����ӹ�¡��੾�д�ҹ (�ѵ��Է��)', '����ӹ�¡��੾�д�ҹ (�ѵ��Է��)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 
							  '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '551009' WHERE PL_CODE = '051003' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('540209', '����ӹ�¡��੾�д�ҹ (���Ǩ�Թ)', '����ӹ�¡��੾�д�ҹ (���Ǩ�Թ)', 1, $SESS_USERID, '$UPDATE_DATE', 3, 
							  '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '540209' WHERE PL_CODE = '040203' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('581109', '����ӹ�¡��੾�д�ҹ (�ѡ����ʵ��)', '����ӹ�¡��੾�д�ҹ (�ѡ����ʵ��)', 1, $SESS_USERID, '$UPDATE_DATE', 
							  3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '581109' WHERE PL_CODE = '081103' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('551609', '����ӹ�¡��੾�д�ҹ (�صع����Է��)', '����ӹ�¡��੾�д�ҹ (�صع����Է��)', 1, $SESS_USERID, '$UPDATE_DATE', 
							  3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '551609' WHERE PL_CODE = '051603' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('520429', '����ӹ�¡��੾�д�ҹ (�Ԫҡ���Թ��кѭ��)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ���Թ��кѭ��)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '520429' WHERE PL_CODE = '020423' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('540109', '����ӹ�¡��੾�д�ҹ (�Ԫҡ���ɵ�)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ���ɵ�)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '540109' WHERE PL_CODE = '040103' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('520109', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�ä�ѧ)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�ä�ѧ)', 1, $SESS_USERID, '$UPDATE_DATE', 
							  3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '520109' WHERE PL_CODE = '020103' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('520829', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�õ�Ǩ�ͺ�ѭ��)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�õ�Ǩ�ͺ�ѭ��)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '520829' WHERE PL_CODE = '020823' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('560309', '����ӹ�¡��੾�д�ҹ (����ѵ�ᾷ��)', '����ӹ�¡��੾�д�ҹ (����ѵ�ᾷ��)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '560309' WHERE PL_CODE = '060304' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('562109', '����ӹ�¡��੾�д�ҹ (��Ժѵԧҹ�ѵ�ᾷ��)', '����ӹ�¡��੾�д�ҹ (��Ժѵԧҹ�ѵ�ᾷ��)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '562109' WHERE PL_CODE = '062102' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('540509', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�û�����)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�û�����)', 1, $SESS_USERID, '$UPDATE_DATE',
							  3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '540509' WHERE PL_CODE = '040503' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('522209', '����ӹ�¡��੾�д�ҹ (�Ԫҡ���ˡó�)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ���ˡó�)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '522209' WHERE PL_CODE = '022203' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('540709', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�û����)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ�û����)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '540709' WHERE PL_CODE = '040703' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('540919', '����ӹ�¡��੾�д�ҹ (��Ժѵԧҹ�����)', '����ӹ�¡��੾�д�ҹ (��Ժѵԧҹ�����)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '540919' WHERE PL_CODE = '040912' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('541009', '����ӹ�¡��੾�д�ҹ (�Ԫҡ���ѵǺ��)', '����ӹ�¡��੾�д�ҹ (�Ԫҡ���ѵǺ��)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '541009' WHERE PL_CODE = '041003' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " INSERT INTO PER_LINE (PL_CODE, PL_NAME, PL_SHORTNAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE, 
							  PL_TYPE, LG_CODE)
							  VALUES ('541119', '����ӹ�¡��੾�д�ҹ (��Ժѵԧҹ�ѵǺ��)', '����ӹ�¡��੾�д�ҹ (��Ժѵԧҹ�ѵǺ��)', 1, $SESS_USERID, 
							  '$UPDATE_DATE', 3, '510309') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET PL_CODE_DIRECT = '541119' WHERE PL_CODE = '041112' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " UPDATE PER_LINE SET CL_NAME = '�������дѺ�� - �������дѺ�٧' WHERE PL_CODE IN ('510108', '510307', '510109') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET CL_NAME = '�������дѺ�٧' WHERE PL_CODE IN ('510209') ";
			$db_dpis->send_cmd($cmd);          
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET CL_NAME = '�ӹ�¡���дѺ�� - �ӹ�¡���дѺ�٧' WHERE PL_CODE IN (
			'510308', '511019', '512009', '512409', '512629', '520109', '520309', '520429', '520609', '520829', '521009', '521709', '522009', '522209',		
			'522729', '523509', '523909', '530309', '540109', '540209', '540319', '540329', '540509', '540709', '541009', '541449', '550109', '550229',
			'550409', '550909', '551009', '551109', '551309', '551409', '551609', '551809', '560109', '560209', '560309', '560409', '560609', '560709',
			'561519', '562109', '570109', '570209', '570409', '570509', '570609', '570709', '570909', '571009', '571319', '571419', '571519', '571529',
			'571629', '572709', '573519', '573809', '574009', '574429', '574519', '575109', '575309', '575409', '581109', '581209', '581309', '581619',
			'581909', '582109', '582209', '582309', '582409', '584209', '584409') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET CL_NAME = '�ӹ�¡���дѺ�٧' WHERE PL_CODE IN ('510210') ";
			$db_dpis->send_cmd($cmd);          
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET CL_NAME = '��Ժѵԡ�� - �ç�س�ز�' WHERE PL_CODE IN (
			'513405', '510903', '512403', '513503', '510703', '511013', '520903', '520104', '520103', '520303', '522423', '521003', '523903', '521203',
			'522003', '522103', '521423', '521523', '531923', '530103', '540103', '540703', '540503', '540323', '550103', '560204', '560304', '560104',
			'562503', '560703', '560403', '575403', '570903', '570203', '571623', '573803', '583503', '581603', '583203', '582403', '580103', '581103') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET CL_NAME = '��Ժѵԡ�� - ����Ǫҭ' WHERE PL_CODE IN (
			'513303', '512703', '511014', '512833', '512003', '513003', '512603', '511203', '521703', '520423', '520823', '520603', '522513', '523623',
			'523503', '522203', '523203', '531813', '541503', '541443', '541003', '540203', '551103', '550803', '551303', '550223', '550503', '550303',
			'550403', '550903', '551403', '551803', '551003', '551603', '561803', '561914', '561203', '561204', '563603', '561523', '560603', '560813',
			'561723', '561514', '575303', '573813', '574003', '571303', '574423', '575103', '570704', '570103', '570403', '571003', '570703', '570503',
			'573123', '570803', '570603', '584403', '580913', '580923', '584578', '581903', '581703', '581803', '583403', '584123', '583603', '580823',
			'582623', '582723', '580513', '584003', '582903') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET CL_NAME = '��Ժѵԡ�� - �ӹҭ��þ����' WHERE PL_CODE IN (
			'512903', '511104', '512613', '511503', '510403', '511723', '510404', '513103', '521903', '522723', '530503', '530403', '532423', '532523',
			'532003', '560105', '561913', '561915', '575612', '572203', '575003', '572703', '572503', '574213', '571523', '581203', '581303', '584203',
			'580723', '580403', '092205', '092206', '092207', '092208', '092209', '092210') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET CL_NAME = '�ӹҭ��� - ����Ǫҭ' WHERE PL_CODE IN ('512626') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET CL_NAME = '�ӹҭ��� - �ӹҭ��þ����' WHERE PL_CODE IN ('530306') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET CL_NAME = '��Ժѵԧҹ - �ѡ�о����' WHERE PL_CODE IN ('574512', '582301', '582201', '582101') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET CL_NAME = '��Ժѵԧҹ - ������' WHERE PL_CODE IN (
			'511612', '511712', '512812', '512302', '512212', '522412', '520412', '520212', '522712', '520502', '520812', '523612', '521301', '522312',
			'523302', '521412', '521512', '530212', '531801', '531212', '532512', '540412', '541412', '540312', '540912', '540612', '541112', '550212',
			'551712', '551512', '560802', '563502', '562102', '574802', '572901', '571812', '574312', '571912', '572612', '573512', '573012', '571412',
			'571512', '572412', '571612', '574112', '574202', '584101', '582712', '584401', '583712', '580812', '583312', '580712', '581001') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET CL_NAME = '��Ժѵԧҹ - �ӹҭ�ҹ' WHERE PL_CODE IN (
			'511211', '510501', '510502', '531912', '530601', '532601', '532011', '562802', '562312', '561712', '562212', '562712', '561902', '561502',
			'575201', '574412', '574701', '575602', '575702', '574912', '573712', '572112', '572802', '574601', '580502', '584301', '584555', '582001',
			'582612', '581501') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_NAME = '�ѡ����ͧ', PL_SHORTNAME = '�ѡ����ͧ' WHERE PL_CODE = '510307' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_NAME = '�ѡ��Ǩ�ͺ������ʹ��´�ҹ��úԹ', PL_SHORTNAME = '�ѡ��Ǩ�ͺ������ʹ��´�ҹ��úԹ' 
							  WHERE PL_CODE = '572503' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_NAME = '��Ҿ�ѡ�ҹ�ˡԨ����ɵ�', PL_SHORTNAME = '��Ҿ�ѡ�ҹ�ˡԨ����ɵ�' 
							  WHERE PL_CODE = '541412' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_NAME = '��Ҿ�ѡ�ҹ���Թ', PL_SHORTNAME = '��Ҿ�ѡ�ҹ���Թ' WHERE PL_CODE = '584101' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE_GROUP SET LG_NAME = '��Ҿ�ѡ�ҹ���Թ' WHERE LG_CODE = '584101' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET PL_CODE_NEW = '550803' WHERE PL_CODE = '051203' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); �ѡ�կ�Է���ѧ��

			$cmd = " DELETE FROM PER_LINE WHERE PL_CODE in ('550603', '551203') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); �ѡ��Ե���ⷻ �ѡ�կ�Է���ѧ��

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							  VALUES ('0206', '��. 2.6', '����觨Ѵ��ŧ', '02') ";
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

			$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '03', LEVEL_NO_MAX = '06' WHERE CL_NAME IN ('3-5/6', '3-5/6�') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '03', LEVEL_NO_MAX = '07' 
							  WHERE CL_NAME IN ('3-5/6/7', '3-5/6�/7', '3-5/6/7�', '3-5/6�/7�', '3-5/6�/7Ǫ') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '03', LEVEL_NO_MAX = '08' 
							  WHERE CL_NAME IN ('3-5/6�/7�/8�', '3-5/6�/7Ǫ/8Ǫ') ";
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

			$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '04', LEVEL_NO_MAX = '08' 
							  WHERE CL_NAME IN ('4-6/7/8', '4-5/6�/7�/8�', '4-6/7�/8�', '4-6/7�/8Ǫ', '4-6/7Ǫ/8Ǫ') ";
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

			$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '06', LEVEL_NO_MAX = '06' WHERE CL_NAME IN ('6', '6�') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '06', LEVEL_NO_MAX = '07' 
							  WHERE CL_NAME IN ('6/7', '6/7�', '6�/7�', '6�/7Ǫ') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '06', LEVEL_NO_MAX = '08' WHERE CL_NAME IN ('6/7/8') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '07', LEVEL_NO_MAX = '07' WHERE CL_NAME IN ('7', '7�', '7Ǫ') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '07', LEVEL_NO_MAX = '08' 
							  WHERE CL_NAME IN ('7/8', '7/8�', '7�/8�', '7Ǫ/8Ǫ') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '07', LEVEL_NO_MAX = '09' WHERE CL_NAME IN ('7/8/9') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '08', LEVEL_NO_MAX = '08' 
							  WHERE CL_NAME IN ('8', '8��', '8�', '8Ǫ') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '08', LEVEL_NO_MAX = '09' 
							  WHERE CL_NAME IN ('8/9', '8/9��', '8��/9��', '8Ǫ/9Ǫ') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '08', LEVEL_NO_MAX = '10' WHERE CL_NAME IN ('8/9/10') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '09', LEVEL_NO_MAX = '09' 
							  WHERE CL_NAME IN ('9', '9��', '9��', '9Ǫ') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '09', LEVEL_NO_MAX = '10' 
							  WHERE CL_NAME IN ('9/10', '9/10��', '9��/10��', '9��/10��', '9��/10��', '9Ǫ/10Ǫ') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '09', LEVEL_NO_MAX = '11' WHERE CL_NAME IN ('9/10/11') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '10', LEVEL_NO_MAX = '10' 
							  WHERE CL_NAME IN ('10', '10��', '10��', '10Ǫ') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '10', LEVEL_NO_MAX = '11' WHERE CL_NAME IN ('10/11') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_CO_LEVEL SET LEVEL_NO_MIN = '11', LEVEL_NO_MAX = '11' 
							  WHERE CL_NAME IN ('11', '11��', '11��', '11Ǫ') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_LAYER WHERE LAYER_TYPE = 1 AND LEVEL_NO = '01' AND LAYER_NO IN (15.5, 16, 16.5, 17, 17.5, 18) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_LAYER WHERE LAYER_TYPE = 1 AND LEVEL_NO = '02' AND LAYER_NO IN (14.5, 15, 15.5, 16, 16.5, 17) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_LAYER WHERE LAYER_TYPE = 1 AND LEVEL_NO = '03' AND LAYER_NO IN (20.5, 21, 21.5, 22, 22.5, 23) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_LAYER WHERE LAYER_TYPE = 1 AND LEVEL_NO = '04' AND LAYER_NO IN (20.5, 21, 21.5, 22, 22.5, 23) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_LAYER WHERE LAYER_TYPE = 1 AND LEVEL_NO = '05' AND LAYER_NO IN (20.5, 21, 21.5, 22, 22.5, 23) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_LAYER WHERE LAYER_TYPE = 1 AND LEVEL_NO = '06' AND LAYER_NO IN (20.5, 21, 21.5, 22, 22.5, 23) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_LAYER WHERE LAYER_TYPE = 1 AND LEVEL_NO = '07' AND LAYER_NO IN (20.5, 21, 21.5, 22, 22.5, 23) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_LAYER WHERE LAYER_TYPE = 1 AND LEVEL_NO = '08' AND LAYER_NO IN (24.5, 25, 25.5, 26, 26.5, 27) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_LAYER WHERE LAYER_TYPE = 1 AND LEVEL_NO = '09' AND LAYER_NO IN (20.5, 21, 21.5, 22, 22.5, 23) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_LAYER WHERE LAYER_TYPE = 1 AND LEVEL_NO = '10' AND LAYER_NO IN (19.5, 20, 20.5, 21, 21.5, 22) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_LAYER WHERE LAYER_TYPE = 1 AND LEVEL_NO = '11' AND LAYER_NO IN (16.5, 17, 17.5, 18, 18.5, 19) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_LAYER WHERE LAYER_TYPE = 2 AND LEVEL_NO = '11' AND LAYER_NO IN (10.5, 11, 11.5, 12, 12.5, 13) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_CONTROL set CTRL_ALTER = 4 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if($CTRL_ALTER < 4) 

		if($CTRL_ALTER < 5) {
			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_DECORATION ADD DC_ENG_NAME VARCHAR(100) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_DECORATION ADD DC_ENG_NAME VARCHAR2(100) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ORG ADD DEPARTMENT_ID INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORG ADD DEPARTMENT_ID NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ORG ADD DEPARTMENT_ID INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT ORG_ID, ORG_NAME, ORG_ID_REF, OL_CODE FROM PER_ORG ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$ORG_ID = $data[ORG_ID];
				$ORG_NAME = $data[ORG_NAME];
				$ORG_ID_REF = $data[ORG_ID_REF];
				$OL_CODE1 = trim($data[OL_CODE]);
				$cmd = " SELECT ORG_NAME, ORG_ID_REF, OL_CODE FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$ORG_NAME_REF = $data1[ORG_NAME];
				$OL_CODE = trim($data1[OL_CODE]);
				if ($OL_CODE=='02') $DEPT_ID = $ORG_ID_REF;
				else {
					$ORG_ID_REF = $data1[ORG_ID_REF];
					$cmd = " SELECT ORG_ID_REF, OL_CODE FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();
					$OL_CODE = trim($data1[OL_CODE]);
					if ($OL_CODE=='02') $DEPT_ID = $ORG_ID_REF;
					else {
						$ORG_ID_REF = $data1[ORG_ID_REF];
						$cmd = " SELECT ORG_ID_REF, OL_CODE FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
						$db_dpis1->send_cmd($cmd);
						$data1 = $db_dpis1->get_array();
						$OL_CODE = trim($data1[OL_CODE]);
						if ($OL_CODE=='02') $DEPT_ID = $ORG_ID_REF;
						else {
							$ORG_ID_REF = $data1[ORG_ID_REF];
							$cmd = " SELECT ORG_ID_REF, OL_CODE FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
							$db_dpis1->send_cmd($cmd);
							$data1 = $db_dpis1->get_array();
							$OL_CODE = trim($data1[OL_CODE]);
							if ($OL_CODE=='02') $DEPT_ID = $ORG_ID_REF;
							else {
								$ORG_ID_REF = $data1[ORG_ID_REF];
								$cmd = " SELECT ORG_ID_REF, OL_CODE FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
								$db_dpis1->send_cmd($cmd);
								$data1 = $db_dpis1->get_array();
								$OL_CODE = trim($data1[OL_CODE]);
								if ($OL_CODE=='02') $DEPT_ID = $ORG_ID_REF;
								elseif ($OL_CODE1 != "01" && $OL_CODE1 != "02" && $ORG_ID != 1) 
									echo "PER_ORG $ORG_ID $ORG_NAME $OL_CODE1 $ORG_ID_REF $ORG_NAME_REF<br>";
							}
						}
					}
				}
				if ($OL_CODE1 != "01" && $OL_CODE1 != "02") {
					$cmd = " UPDATE PER_ORG SET DEPARTMENT_ID = $DEPT_ID WHERE ORG_ID = $ORG_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				}
			} // end while						

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ORG_ASS ADD DEPARTMENT_ID INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORG_ASS ADD DEPARTMENT_ID NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ORG_ASS ADD DEPARTMENT_ID INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT ORG_ID, ORG_ID_REF, OL_CODE FROM PER_ORG_ASS ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$ORG_ID = $data[ORG_ID];
				$ORG_ID_REF = $data[ORG_ID_REF];
				$OL_CODE1 = trim($data[OL_CODE]);
				$cmd = " SELECT ORG_ID_REF, OL_CODE FROM PER_ORG_ASS WHERE ORG_ID = $ORG_ID_REF ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$OL_CODE = trim($data1[OL_CODE]);
				if ($OL_CODE=='02') $DEPT_ID = $ORG_ID_REF;
				else {
					$ORG_ID_REF = $data1[ORG_ID_REF];
					$cmd = " SELECT ORG_ID_REF, OL_CODE FROM PER_ORG_ASS WHERE ORG_ID = $ORG_ID_REF ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();
					$OL_CODE = trim($data1[OL_CODE]);
					if ($OL_CODE=='02') $DEPT_ID = $ORG_ID_REF;
					else {
						$ORG_ID_REF = $data1[ORG_ID_REF];
						$cmd = " SELECT ORG_ID_REF, OL_CODE FROM PER_ORG_ASS WHERE ORG_ID = $ORG_ID_REF ";
						$db_dpis1->send_cmd($cmd);
						$data1 = $db_dpis1->get_array();
						$OL_CODE = trim($data1[OL_CODE]);
						if ($OL_CODE=='02') $DEPT_ID = $ORG_ID_REF;
						else {
							$ORG_ID_REF = $data1[ORG_ID_REF];
							$cmd = " SELECT ORG_ID_REF, OL_CODE FROM PER_ORG_ASS WHERE ORG_ID = $ORG_ID_REF ";
							$db_dpis1->send_cmd($cmd);
							$data1 = $db_dpis1->get_array();
							$OL_CODE = trim($data1[OL_CODE]);
							if ($OL_CODE=='02') $DEPT_ID = $ORG_ID_REF;
							else {
								$ORG_ID_REF = $data1[ORG_ID_REF];
								$cmd = " SELECT ORG_ID_REF, OL_CODE FROM PER_ORG_ASS WHERE ORG_ID = $ORG_ID_REF ";
								$db_dpis1->send_cmd($cmd);
								$data1 = $db_dpis1->get_array();
								$OL_CODE = trim($data1[OL_CODE]);
								if ($OL_CODE=='02') $DEPT_ID = $ORG_ID_REF;
								elseif ($OL_CODE1 != "01" && $OL_CODE1 != "02" && $ORG_ID != 1) echo "PER_ORG_ASS $ORG_ID $OL_CODE1<br>";
							}
						}
					}
				}
				if ($OL_CODE1 != "01" && $OL_CODE1 != "02") {
					$cmd = " UPDATE PER_ORG_ASS SET DEPARTMENT_ID = $DEPT_ID WHERE ORG_ID = $ORG_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				}
			} // end while						

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_MAP_CODE(
				MAP_CODE VARCHAR(100) NOT NULL,	
				OLD_CODE VARCHAR(100) NULL,
				NEW_CODE VARCHAR(100) NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PER_MAP_CODE PRIMARY KEY (MAP_CODE, OLD_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_MAP_CODE(
				MAP_CODE VARCHAR2(100) NOT NULL,	
				OLD_CODE VARCHAR2(100) NOT NULL,
				NEW_CODE VARCHAR2(100) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_MAP_CODE PRIMARY KEY (MAP_CODE, OLD_CODE)) ";
			elseif($DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_MAP_CODE(
				MAP_CODE VARCHAR(100) NOT NULL,	
				OLD_CODE VARCHAR(100) NOT NULL,
				NEW_CODE VARCHAR(100) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_MAP_CODE PRIMARY KEY (MAP_CODE, OLD_CODE)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_EDUCNAME DROP CONSTRAINT INXU1_PER_EDUCNAME ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU1_PER_EDUCNAME ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_ORG DROP CONSTRAINT INXU1_PER_ORG ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU1_PER_ORG ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_ORG DROP CONSTRAINT INXU2_PER_ORG ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU2_PER_ORG ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_ORG_ASS DROP CONSTRAINT INXU1_PER_ORG_ASS ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU1_PER_ORG_ASS ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_ORG_ASS DROP CONSTRAINT INXU2_PER_ORG_ASS ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU2_PER_ORG_ASS ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LINE ALTER PL_NAME VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LINE MODIFY PL_NAME VARCHAR2(100) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LINE MODIFY PL_NAME VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP INDEX UPDATE_TABLE_SAL_U1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="oci8") {
				$cmd = " SELECT TNAME FROM TAB WHERE TABTYPE = 'VIEW' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while ( $data = $db_dpis->get_array() ) {
					$TNAME = trim($data[TNAME]);
					$cmd = " DROP VIEW $TNAME ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while

				$cmd = " SELECT CONSTRAINT_NAME FROM DBA_CONSTRAINTS WHERE OWNER = 'OCSC' AND 
								  TABLE_NAME = 'PER_LEVEL' AND CONSTRAINT_TYPE = 'C' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while ( $data = $db_dpis->get_array() ) {
					$CONSTRAINT_NAME = trim($data[CONSTRAINT_NAME]);
					$cmd = " ALTER TABLE PER_LEVEL DROP CONSTRAINT $CONSTRAINT_NAME ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while

				for ($no=1;$no<=10;$no++) {
					$cmd = " SELECT TNAME FROM TAB WHERE TNAME LIKE 'OF_%' ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					while ( $data = $db_dpis->get_array() ) {
						$TNAME = trim($data[TNAME]);
						$cmd = " DROP TABLE $TNAME ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
					} // end while

					$cmd = " SELECT TNAME FROM TAB WHERE TNAME LIKE 'SAL_%' ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					while ( $data = $db_dpis->get_array() ) {
						$TNAME = trim($data[TNAME]);
						$cmd = " DROP TABLE $TNAME ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
					} // end while

					$cmd = " SELECT TNAME FROM TAB WHERE TNAME LIKE 'POS_%' ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					while ( $data = $db_dpis->get_array() ) {
						$TNAME = trim($data[TNAME]);
						$cmd = " DROP TABLE $TNAME ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
					} // end while

					$cmd = " SELECT TNAME FROM TAB WHERE TNAME LIKE 'PS_%' ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					while ( $data = $db_dpis->get_array() ) {
						$TNAME = trim($data[TNAME]);
						$cmd = " DROP TABLE $TNAME ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
					} // end while
				} // end for
			} // end if

			$cmd = " ALTER TABLE PER_COMDTL DROP CONSTRAINT FK2_PER_COMDTL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMDTL DROP CONSTRAINT INXU1_PER_COMDTL ";
			elseif($DPISDB=="oci8")
				$cmd = " DROP INDEX INXU1_PER_COMDTL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_TYPE SET PT_NAME = '�' WHERE PT_CODE = '12' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_CONTROL set CTRL_ALTER = 5 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if($CTRL_ALTER < 5) 

		if($CTRL_ALTER < 6) {
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD PER_ID_REVIEW0 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD PER_ID_REVIEW0 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI_FORM ADD PER_ID_REVIEW0 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LINE ADD LAYER_TYPE SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LINE ADD LAYER_TYPE NUMBER(1) NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_LINE ADD LAYER_TYPE SMALLINT(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET LAYER_TYPE = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET LAYER_TYPE = 2 WHERE PL_CODE IN 
							('540412', '540912', '540612', '541112', '551712', '551512', '563502', '562102', '574512', '571912', '573512', '573012', '571412', 
							 '571512', '571612', '540312', '582301', '582201', '582101', '560104', '513405') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMMAND ADD COM_LEVEL_SALP INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMMAND ADD COM_LEVEL_SALP NUMBER(2) NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMMAND ADD COM_LEVEL_SALP SMALLINT(2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMMAND SET COM_LEVEL_SALP = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_UNDER_ORG1 VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_UNDER_ORG1 VARCHAR2(255) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_UNDER_ORG1 VARCHAR(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_UNDER_ORG2 VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_UNDER_ORG2 VARCHAR2(255) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_UNDER_ORG2 VARCHAR(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ORG ADD ORG_ENG_NAME VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORG ADD ORG_ENG_NAME VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ORG_ASS ADD ORG_ENG_NAME VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORG_ASS ADD ORG_ENG_NAME VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_KPI ADD ORG_NAME VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI ADD ORG_NAME VARCHAR2(255) NULL  ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_KPI ADD UNDER_ORG_NAME1 VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI ADD UNDER_ORG_NAME1 VARCHAR2(255) NULL  ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_PERSONAL SET PER_OFFNO = TRIM(PER_OFFNO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONL_PER_OFFNO ON PER_PERSONAL (PER_OFFNO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_WORK_CYCLEHIS ON PER_WORK_CYCLEHIS (PER_ID, START_DATE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_WORK_TIME ON PER_WORK_TIME (PER_ID, START_DATE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_WORK_TIME1 ON PER_WORK_TIME (START_DATE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALQUOTADTL1 DROP CONSTRAINT PK_PER_SALQUOTADTL1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP INDEX PK_PER_SALQUOTADTL1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALQUOTADTL1 DROP CONSTRAINT FK1_PER_SALQUOTADTL1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALQUOTADTL2 DROP CONSTRAINT PK_PER_SALQUOTADTL2 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP INDEX PK_PER_SALQUOTADTL2 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALQUOTADTL2 DROP CONSTRAINT FK1_PER_SALQUOTADTL2 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALPROMOTE DROP CONSTRAINT PK_PER_SALPROMOTE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP INDEX PK_PER_SALPROMOTE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALPROMOTE DROP CONSTRAINT FK1_PER_SALPROMOTE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALQUOTA DROP CONSTRAINT PK_PER_SALQUOTA ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP INDEX PK_PER_SALQUOTA ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALQUOTA ADD CONSTRAINT PK_PER_SALQUOTA PRIMARY KEY (SALQ_YEAR, SALQ_TYPE, 
							DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALQUOTADTL1 ADD CONSTRAINT PK_PER_SALQUOTADTL1 PRIMARY KEY (SALQ_YEAR, 
							SALQ_TYPE, DEPARTMENT_ID, ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALQUOTADTL1 ADD CONSTRAINT FK1_PER_SALQUOTADTL1 FOREIGN KEY (SALQ_YEAR, 
							SALQ_TYPE, DEPARTMENT_ID) REFERENCES PER_SALQUOTA (SALQ_YEAR, SALQ_TYPE, DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALQUOTADTL2 ADD CONSTRAINT PK_PER_SALQUOTADTL2 PRIMARY KEY (SALQ_YEAR, 
							SALQ_TYPE, DEPARTMENT_ID, ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALQUOTADTL2 ADD CONSTRAINT FK1_PER_SALQUOTADTL2 FOREIGN KEY (SALQ_YEAR, 
							SALQ_TYPE, DEPARTMENT_ID) REFERENCES PER_SALQUOTA (SALQ_YEAR, SALQ_TYPE, DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALPROMOTE ADD CONSTRAINT PK_PER_SALPROMOTE PRIMARY KEY (SALQ_YEAR, 
							SALQ_TYPE, DEPARTMENT_ID, PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALPROMOTE ADD CONSTRAINT FK1_PER_SALPROMOTE FOREIGN KEY (SALQ_YEAR, 
							SALQ_TYPE, DEPARTMENT_ID) REFERENCES PER_SALQUOTA (SALQ_YEAR, SALQ_TYPE, DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PRENAME ADD RANK_FLAG SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PRENAME ADD RANK_FLAG NUMBER(1) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PRENAME ADD RANK_FLAG SMALLINT(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_PRENAME SET RANK_FLAG = 0 WHERE RANK_FLAG IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT PN_CODE, PN_NAME FROM PER_PRENAME ORDER BY PN_NAME ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PN_CODE = $data[PN_CODE];
				$PN_NAME = $data[PN_NAME];
				if (strpos($PN_NAME,"���Ǩ") !== false || strpos($PN_NAME,"����") !== false || strpos($PN_NAME,"��") !== false || 
					strpos($PN_NAME,"�Ժ") !== false || strpos($PN_NAME,"����") !== false || strpos($PN_NAME,"�ѹ") !== false || 
					strpos($PN_NAME,"���") !== false || strpos($PN_NAME,"�Һ") !== false || strpos($PN_NAME,"����") !== false || 
					strpos($PN_NAME,"����") !== false) {
					$cmd = " UPDATE PER_PRENAME SET RANK_FLAG = 1 WHERE PN_CODE = '$PN_CODE' ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end if
			} // end while						

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG_TRANSFER VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG_TRANSFER VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMDTL ADD CMD_ORG_TRANSFER VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL ADD CMD_ORG_TRANSFER VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMDTL ADD CMD_PERCENT NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL ADD CMD_PERCENT NUMBER(6,3) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMDTL ADD CMD_PERCENT DECIMAL(6,3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT COUNT(AM_CODE) AS COUNT_DATA FROM PER_ASSESS_MAIN ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_ASSESS_MAIN(
					AM_CODE VARCHAR(10) NOT NULL,	
					AM_NAME VARCHAR(100) NOT NULL,
					AM_POINT_MIN NUMBER NOT NULL,
					AM_POINT_MAX NUMBER NOT NULL,
					AM_ACTIVE SINGLE NOT NULL,
					UPDATE_USER INTEGER2 NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_ASSESS_MAIN PRIMARY KEY (AM_CODE)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_ASSESS_MAIN(
					AM_CODE VARCHAR2(10) NOT NULL,	
					AM_NAME VARCHAR2(100) NOT NULL,
					AM_POINT_MIN NUMBER(5,2) NOT NULL,
					AM_POINT_MAX NUMBER(5,2) NOT NULL,
					AM_ACTIVE NUMBER(1) NOT NULL,
					UPDATE_USER NUMBER(5) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_ASSESS_MAIN PRIMARY KEY (AM_CODE)) ";
				elseif($DPISDB=="mysql") 
					$cmd = " CREATE TABLE PER_ASSESS_MAIN(
					AM_CODE VARCHAR(10) NOT NULL,	
					AM_NAME VARCHAR(100) NOT NULL,
					AM_POINT_MIN DECIMAL(5,2) NOT NULL,
					AM_POINT_MAX DECIMAL(5,2) NOT NULL,
					AM_ACTIVE SMALLINT(1) NOT NULL,
					UPDATE_USER SMALLINT(5) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_ASSESS_MAIN PRIMARY KEY (AM_CODE)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_ASSESS_MAIN (AM_CODE, AM_NAME, AM_POINT_MIN, AM_POINT_MAX, AM_ACTIVE, 
								UPDATE_USER, UPDATE_DATE)
								VALUES ('1', '��ͧ��Ѻ��ا', 0, 59.99, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_ASSESS_MAIN (AM_CODE, AM_NAME, AM_POINT_MIN, AM_POINT_MAX, AM_ACTIVE, 
								UPDATE_USER, UPDATE_DATE)
								VALUES ('2', '����', 60, 69.99, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_ASSESS_MAIN (AM_CODE, AM_NAME, AM_POINT_MIN, AM_POINT_MAX, AM_ACTIVE, 
								UPDATE_USER, UPDATE_DATE)
								VALUES ('3', '��', 70, 79.99, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_ASSESS_MAIN (AM_CODE, AM_NAME, AM_POINT_MIN, AM_POINT_MAX, AM_ACTIVE, 
								UPDATE_USER, UPDATE_DATE)
								VALUES ('4', '���ҡ', 80, 89.99, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_ASSESS_MAIN (AM_CODE, AM_NAME, AM_POINT_MIN, AM_POINT_MAX, AM_ACTIVE, 
								UPDATE_USER, UPDATE_DATE)
								VALUES ('5', '����', 90, 100, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}
		
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ASSESS_LEVEL ADD AL_PERCENT NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ASSESS_LEVEL ADD AL_PERCENT NUMBER(6,3) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ASSESS_LEVEL ADD AL_PERCENT DECIMAL(6,3) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT COUNT(AL_CODE) AS COUNT_DATA FROM PER_ASSESS_LEVEL ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_ASSESS_LEVEL(
					AL_CODE VARCHAR(10) NOT NULL,	
					AL_NAME VARCHAR(100) NOT NULL,
					AL_POINT_MIN NUMBER NOT NULL,
					AL_POINT_MAX NUMBER NOT NULL,
					ORG_ID INTEGER NULL,
					DEPARTMENT_ID INTEGER NULL,
					AM_CODE VARCHAR(10) NOT NULL,	
					AL_PERCENT NUMBER NOT NULL,
					AL_ACTIVE SINGLE NOT NULL,
					UPDATE_USER INTEGER2 NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_ASSESS_LEVEL PRIMARY KEY (AL_CODE)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_ASSESS_LEVEL(
					AL_CODE VARCHAR2(10) NOT NULL,	
					AL_NAME VARCHAR2(100) NOT NULL,
					AL_POINT_MIN NUMBER(5,2) NOT NULL,
					AL_POINT_MAX NUMBER(5,2) NOT NULL,
					ORG_ID NUMBER(10) NULL,
					DEPARTMENT_ID NUMBER(10) NULL,
					AM_CODE VARCHAR2(10) NOT NULL,	
					AL_PERCENT NUMBER(6,3) NOT NULL,
					AL_ACTIVE NUMBER(1) NOT NULL,
					UPDATE_USER NUMBER(5) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_ASSESS_LEVEL PRIMARY KEY (AL_CODE)) ";
				elseif($DPISDB=="mysql") 
					$cmd = " CREATE TABLE PER_ASSESS_LEVEL(
					AL_CODE VARCHAR(10) NOT NULL,	
					AL_NAME VARCHAR(100) NOT NULL,
					AL_POINT_MIN DECIMAL(5,2) NOT NULL,
					AL_POINT_MAX DECIMAL(5,2) NOT NULL,
					ORG_ID INTEGER(10) NULL,
					DEPARTMENT_ID INTEGER(10) NULL,
					AM_CODE VARCHAR(10) NOT NULL,	
					AL_PERCENT DECIMAL(6,3) NOT NULL,
					AL_ACTIVE SMALLINT(1) NOT NULL,
					UPDATE_USER SMALLINT(5) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_ASSESS_LEVEL PRIMARY KEY (AL_CODE)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " INSERT INTO PER_ASSESS_LEVEL (AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, DEPARTMENT_ID, 
								AM_CODE,	AL_PERCENT, AL_ACTIVE, UPDATE_USER, UPDATE_DATE)
								VALUES ('10', '��ͧ��Ѻ��ا', 0, 59.99, $DEPARTMENT_ID, '1', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_ASSESS_LEVEL (AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, DEPARTMENT_ID, 
								AM_CODE,	AL_PERCENT, AL_ACTIVE, UPDATE_USER, UPDATE_DATE)
								VALUES ('21', '���� 1', 60, 64.99, $DEPARTMENT_ID, '2', 2.4, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_ASSESS_LEVEL (AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, DEPARTMENT_ID, 
								AM_CODE,	AL_PERCENT, AL_ACTIVE, UPDATE_USER, UPDATE_DATE)
								VALUES ('22', '���� 2', 65, 69.99, $DEPARTMENT_ID, '2', 2.5, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_ASSESS_LEVEL (AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, DEPARTMENT_ID, 
								AM_CODE,	AL_PERCENT, AL_ACTIVE, UPDATE_USER, UPDATE_DATE)
								VALUES ('31', '�� 1', 70, 74.99, $DEPARTMENT_ID, '3', 2.9, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_ASSESS_LEVEL (AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, DEPARTMENT_ID, 
								AM_CODE,	AL_PERCENT, AL_ACTIVE, UPDATE_USER, UPDATE_DATE)
								VALUES ('32', '�� 2', 75, 79.99, $DEPARTMENT_ID, '3', 3, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_ASSESS_LEVEL (AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, DEPARTMENT_ID, 
								AM_CODE,	AL_PERCENT, AL_ACTIVE, UPDATE_USER, UPDATE_DATE)
								VALUES ('41', '���ҡ 1', 80, 84.99, $DEPARTMENT_ID, '4', 3.4, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_ASSESS_LEVEL (AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, DEPARTMENT_ID, 
								AM_CODE,	AL_PERCENT, AL_ACTIVE, UPDATE_USER, UPDATE_DATE)
								VALUES ('42', '���ҡ 2', 85, 89.99, $DEPARTMENT_ID, '4', 3.5, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " INSERT INTO PER_ASSESS_LEVEL (AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, DEPARTMENT_ID, 
								AM_CODE,	AL_PERCENT, AL_ACTIVE, UPDATE_USER, UPDATE_DATE)
								VALUES ('51', '���� 1', 90, 94.99, $DEPARTMENT_ID, '5', 4.4, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			
				$cmd = " INSERT INTO PER_ASSESS_LEVEL (AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, DEPARTMENT_ID, 
								AM_CODE,	AL_PERCENT, AL_ACTIVE, UPDATE_USER, UPDATE_DATE)
								VALUES ('52', '���� 2', 95, 100, $DEPARTMENT_ID, '5', 4.5, 1, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD ORG_ID INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD ORG_ID NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI_FORM ADD ORG_ID INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD TOTAL_SCORE NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD TOTAL_SCORE NUMBER(5,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI_FORM ADD TOTAL_SCORE DECIMAL(5,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_KPI_FORM ADD SALARY_FLAG CHAR(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD ORG_ID_SALARY INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD ORG_ID_SALARY NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI_FORM ADD ORG_ID_SALARY INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_KPI_FORM ADD KPI_FLAG CHAR(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD ORG_ID_KPI INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD ORG_ID_KPI NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI_FORM ADD ORG_ID_KPI INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD CHIEF_PER_ID INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD CHIEF_PER_ID NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI_FORM ADD CHIEF_PER_ID INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_KPI_FORM ADD FRIEND_FLAG CHAR(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD ORG_ID_1_SALARY INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD ORG_ID_1_SALARY NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI_FORM ADD ORG_ID_1_SALARY INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD ORG_ID_ASS INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD ORG_ID_ASS NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI_FORM ADD ORG_ID_ASS INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT COUNT(CP_ID) AS COUNT_DATA FROM PER_COMPENSATION_TEST ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_COMPENSATION_TEST(
					CP_ID INTEGER NOT NULL,	
					CP_NAME VARCHAR(100) NULL,
					CP_DATE VARCHAR(19) NOT NULL,
					CP_CYCLE SINGLE NULL,
					CP_START_DATE VARCHAR(19) NOT NULL,
					CP_END_DATE VARCHAR(19) NOT NULL,
					CP_BUDGET INTEGER NULL,
					ORG_ID INTEGER NULL,
					DEPARTMENT_ID INTEGER NULL,
					SF_CODE_O VARCHAR(255) NOT NULL,
					SF_CODE_K VARCHAR(255) NOT NULL,
					SF_CODE_D VARCHAR(255) NOT NULL,
					SF_CODE_M VARCHAR(255) NOT NULL,
					CP_RESULT INTEGER NULL,
					O_QTY VARCHAR(10) NULL,
					O_SALARY VARCHAR(10) NULL,
					K_QTY VARCHAR(10) NULL,
					K_SALARY VARCHAR(10) NULL,
					D_QTY VARCHAR(10) NULL,
					D_SALARY VARCHAR(10) NULL,
					M_QTY VARCHAR(10) NULL,
					M_SALARY VARCHAR(10) NULL,
					SUM_QTY VARCHAR(255) NULL,
					SUM_SALARY VARCHAR(255) NULL,
					HOLD_SALARY VARCHAR(10) NULL,
					UPDATE_USER INTEGER2 NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_COMPENSATION_TEST PRIMARY KEY (CP_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_COMPENSATION_TEST(
					CP_ID NUMBER(10) NOT NULL,	
					CP_NAME VARCHAR2(100) NULL,
					CP_DATE VARCHAR2(19) NOT NULL,
					CP_CYCLE NUMBER(1) NOT NULL,
					CP_START_DATE VARCHAR2(19) NOT NULL,
					CP_END_DATE VARCHAR2(19) NOT NULL,
					CP_BUDGET NUMBER(10) NOT NULL,	
					ORG_ID NUMBER(10) NULL,	
					DEPARTMENT_ID NUMBER(10) NULL,
					SF_CODE_O VARCHAR2(255) NOT NULL,
					SF_CODE_K VARCHAR2(255) NOT NULL,
					SF_CODE_D VARCHAR2(255) NOT NULL,
					SF_CODE_M VARCHAR2(255) NOT NULL,
					CP_RESULT NUMBER(10) NOT NULL,	
					O_QTY VARCHAR2(10) NULL,
					O_SALARY VARCHAR2(10) NULL,
					K_QTY VARCHAR2(10) NULL,
					K_SALARY VARCHAR2(10) NULL,
					D_QTY VARCHAR2(10) NULL,
					D_SALARY VARCHAR2(10) NULL,
					M_QTY VARCHAR2(10) NULL,
					M_SALARY VARCHAR2(10) NULL,
					SUM_QTY VARCHAR2(255) NULL,
					SUM_SALARY VARCHAR2(255) NULL,
					HOLD_SALARY VARCHAR2(10) NULL,
					UPDATE_USER NUMBER(5) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_COMPENSATION_TEST PRIMARY KEY (CP_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_COMPENSATION_TEST(
					CP_ID INTEGER(10) NOT NULL,	
					CP_NAME VARCHAR(100) NULL,
					CP_DATE VARCHAR(19) NOT NULL,
					CP_CYCLE SMALLINT(1) NOT NULL,
					CP_START_DATE VARCHAR(19) NOT NULL,
					CP_END_DATE VARCHAR(19) NOT NULL,
					CP_BUDGET INTEGER(10) NULL,
					ORG_ID INTEGER(10) NULL,	
					DEPARTMENT_ID INTEGER(10) NULL,
					SF_CODE_O VARCHAR(255) NOT NULL,
					SF_CODE_K VARCHAR(255) NOT NULL,
					SF_CODE_D VARCHAR(255) NOT NULL,
					SF_CODE_M VARCHAR(255) NOT NULL,
					CP_RESULT INTEGER(10) NULL,	
					O_QTY VARCHAR(10) NULL,
					O_SALARY VARCHAR(10) NULL,
					K_QTY VARCHAR(10) NULL,
					K_SALARY VARCHAR(10) NULL,
					D_QTY VARCHAR(10) NULL,
					D_SALARY VARCHAR(10) NULL,
					M_QTY VARCHAR(10) NULL,
					M_SALARY VARCHAR(10) NULL,
					SUM_QTY VARCHAR(255) NULL,
					SUM_SALARY VARCHAR(255) NULL,
					HOLD_SALARY VARCHAR(10) NULL,
					UPDATE_USER SMALLINT(5) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_COMPENSATION_TEST PRIMARY KEY (CP_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_COMPENSATION_TEST_DTL(
					CD_ID INTEGER NOT NULL,	
					CP_ID INTEGER NOT NULL,	
					PER_ID INTEGER NOT NULL,	
					LEVEL_NO VARCHAR(10) NULL,
					AL_CODE VARCHAR(10) NOT NULL,
					CD_SALARY NUMBER NULL,
					CD_PERCENT NUMBER NULL,
					CD_EXTRA_SALARY NUMBER NULL,
					UPDATE_USER INTEGER2 NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_COMPENSATION_TEST_DTL PRIMARY KEY (CD_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_COMPENSATION_TEST_DTL(
					CD_ID NUMBER(10) NOT NULL,	
					CP_ID NUMBER(10) NULL,	
					PER_ID NUMBER(10) NOT NULL,	
					LEVEL_NO VARCHAR2(10) NULL,
					AL_CODE VARCHAR2(10) NOT NULL,
					CD_SALARY NUMBER(16,2) NULL,
					CD_PERCENT NUMBER(6,3) NULL,
					CD_EXTRA_SALARY NUMBER(16,2) NULL,
					UPDATE_USER NUMBER(5) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_COMPENSATION_TEST_DTL PRIMARY KEY (CD_ID)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_COMPENSATION_TEST_DTL(
					CD_ID INTEGER(10) NOT NULL,	
					CP_ID INTEGER(10) NOT NULL,	
					PER_ID INTEGER(10) NOT NULL,	
					LEVEL_NO VARCHAR(10) NULL,
					AL_CODE VARCHAR(10) NOT NULL,
					CD_SALARY DECIMAL(16,2) NULL,
					CD_PERCENT DECIMAL(6,3) NULL,
					CD_EXTRA_SALARY DECIMAL(16,2) NULL,
					UPDATE_USER SMALLINT(5) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_COMPENSATION_TEST_DTL PRIMARY KEY (CD_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

			} // end if

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('21315', '����͹�Թ��͹�дѺ����', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('21325', '����͹�Թ��͹�дѺ��', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('21335', '����͹�Թ��͹�дѺ���ҡ', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('21345', '����͹�Թ��͹�дѺ����', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('21415', '�Թ��͹������ ���Թ��ҵͺ᷹�����', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = trim(COM_DESC) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '����觺�èؼ��ç�س�ز� �������Ǫҭ ���ӹҭ���' 
							WHERE COM_DESC = '����觺�èؼ��ç�س�ز� �������Ǫҭ ���ӹҭ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '����觺�èؼ���͡��Ѻ�Ҫ��÷��á�Ѻ����Ѻ�Ҫ���' 
							WHERE COM_DESC = '����觺�èآ���Ҫ��þ����͹���ѭ�����Ѻ�Ҫ��÷��á�Ѻ����Ѻ�Ҫ���' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '����觺�èؼ��任�Ժѵԧҹ�����Ԥ���Ѱ����ա�Ѻ����Ѻ�Ҫ���' 
							WHERE COM_DESC = '����觺�èآ���Ҫ��þ����͹���ѭ������Ѻ͹��ѵԨҡ����Ѱ���������͡�ҡ�Ҫ���任�Ժѵԧҹ���Ѻ����Ѻ�Ҫ���' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '����觺�èؼ�����繢���Ҫ��þ����͹���ѭ��Ѻ����Ѻ�Ҫ���' 
							WHERE COM_DESC = '����觺�èآ���Ҫ��þ����͹���ѭ��Ѻ����Ѻ�Ҫ���' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '���������͹����ͺ�Ѵ���͡�� ��м�������Ѻ�Ѵ���͡' 
							WHERE COM_DESC = '���������͹(����ͺ�Ѵ���͡ ��м�������Ѻ�Ѵ���͡)' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '����觺�èؼ�����繢���Ҫ��þ����͹���ѭ��Ѻ����Ѻ�Ҫ�������ç���˹觹ѡ������ ��м���Ǩ�Ҫ���' 
							WHERE COM_DESC = '����觺�èء�Ѻ����Ѻ�Ҫ�������ç���˹觹ѡ������ ��м���Ǩ�Ҫ���' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '����觺�èؼ�����繢���Ҫ��þ����͹���ѭ��Ѻ����Ѻ�Ҫ�������ç���˹觻������������дѺ��ҧ' 
							WHERE COM_DESC = '����觺�èء�Ѻ����Ѻ�Ҫ�������ç���˹觻������������дѺ��ҧ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '���������仴�ç���˹� �. Ǫ. ��.' 
							WHERE COM_DESC = '���������仴�ç���˹� �. Ǫ. ��' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '���������͹��鹴�ç���˹� �. Ǫ. ��.' 
							WHERE COM_DESC = '���������͹��鹴�ç���˹� �. Ǫ. ��' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '����觺�èؼ�����繢���Ҫ��þ����͹���ѭ��Ѻ����Ѻ�Ҫ�������ç���˹� �. Ǫ. ��.' 
							WHERE COM_DESC = '����觺�èء�Ѻ����Ѻ�Ҫ�������ç���˹� �. Ǫ. ��' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '��������¢���Ҫ��õ�� � 2/2540', COM_GROUP = '02'
							WHERE COM_TYPE = '0801' AND COM_NAME = '��. 8.1' AND COM_DESC = '���������͹��鹴�ç���˹�' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '���������͹����Ҫ��õ�� � 2/2540', COM_GROUP = '04' 
							WHERE COM_TYPE = '0802' AND COM_NAME = '��. 8.2' AND COM_DESC = '������Ѻ�͹�Ҵ�ç���˹�' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '������Ѻ�͹����Ҫ��þ����͹���ѭ��� � 2/2540'
							WHERE COM_TYPE = '0803' AND COM_NAME = '��. 8.3' AND COM_DESC = '����觺�èء�Ѻ����Ѻ�Ҫ�������ç���˹�' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT COM_TYPE FROM PER_COMTYPE WHERE COM_TYPE = '0804' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
								VALUES ('0804', '��. 8.4', '����觺�èؼ�����繢���Ҫ��þ����͹���ѭ��Ѻ����Ѻ�Ҫ��õ�� � 2/2540', '01') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_COMMAND SET  COM_TYPE = '0804' WHERE COM_TYPE = '0803' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_COMMAND SET  COM_TYPE = '0803' WHERE COM_TYPE = '0802' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_COMMAND SET  COM_TYPE = '0802' WHERE COM_TYPE = '0801' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							VALUES ('0900', '��. 9', '�����������Ѻ�Թ��͹����ز� (��Ѻ�ز�)', '05') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							VALUES ('1101', '��. 11.1', '�����������Ѻ�Թ��͹', '05') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							VALUES ('1102', '��. 11.2', '�����������Ѻ�Թ��͹ (�ó��Ѻ�Թ��͹��ҧ�ѹ�Ѻ)', '05') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							VALUES ('1200', '��. 12', '�����������Ҫ����Ѻ�Թ��͹��ѵ�ҷ�᷹', '05') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							VALUES ('1300', '��. 13', '������ѡ���Ҫ���᷹', '07') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							VALUES ('1400', '��. 14', '������ѡ�ҡ��㹵��˹�', '07') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							VALUES ('1500', '��. 15', '����觾ѡ�Ҫ�����Ф��������͡�ҡ�Ҫ�������͹', '06') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							VALUES ('1600', '��. 16', '�����������Ҫ��û�Ш���ǹ�Ҫ���', '06') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET  COM_DESC = '�����������Ҫ����͡�ҡ�Ҫ������ТҴ�س���ѵԷ�������͢Ҵ�س���ѵ�੾������Ѻ���˹�' 
							WHERE COM_DESC = '�����������Ҫ����͡�ҡ�Ҫ������ТҴ�س���ѵԷ�������ͤس���ѵ�੾������Ѻ���˹�' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							VALUES ('1706', '��. 17.6', '�����������Ҫ���任�Ժѵԧҹ�����Ԥ���Ѱ�����', '06') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							VALUES ('1707', '��. 17.7', '�����������Ҫ����͡�ҡ�Ҫ�������任�Ժѵԧҹ�����Ԥ���Ѱ�����', '06') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							VALUES ('1800', '��. 18', '�����¡��ԡ��������', '08') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							VALUES ('1900', '��. 19', '�����������䢤���觷��Դ��Ҵ', '08') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							VALUES ('2001', '��. 20.1', '�����ŧ���Ҥ�ѳ�� (�ҵ�� 103)', '09') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							VALUES ('2002', '��. 20.2', '�����ŧ�ɵѴ�Թ��͹ (�ҵ�� 103)', '09') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							VALUES ('2003', '��. 20.3', '�����ŧ��Ŵ����Թ��͹ (�ҵ�� 103)', '09') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							VALUES ('2004', '��. 20.4', '�����ŧ��..........�͡�ҡ�Ҫ��� (�ҵ�� 103)', '09') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							VALUES ('2005', '��. 20.5', '�����������/Ŵ��/����/¡�� (�ҵ�� 109 ��ä�ͧ)', '09') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							VALUES ('2006', '��. 20.6', '�����������/Ŵ��/����/¡�� (�ҵ�� 109 ��ä���������ä��)', '09') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							VALUES ('2007', '��. 20.7', '�����������/Ŵ��/����/¡�� ŧ��..........  (�ҵ�� 109 ��ä���)', '09') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							VALUES ('2008', '��. 20.8', '�����������/Ŵ��/����/¡�� ŧ��..........  (�ҵ�� 9)', '09') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							VALUES ('2009', '��. 20.9', '�����¡��/����/Ŵ��/������ (�ҵ�� 125 (1) (2) (3))', '09') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							VALUES ('2010', '��. 20.10', '�����������/Ŵ��/����/¡�� �������Ѻ����Ѻ�Ҫ���  (�ҵ�� 125 (4), �ҵ�� 126)', '09') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP)
							VALUES ('1005', '��. 10.5', '���������͹�Թ��͹', '05') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_FILE_NO VARCHAR(25) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_FILE_NO VARCHAR2(25) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_BANK_ACCOUNT VARCHAR(25) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_BANK_ACCOUNT VARCHAR2(25) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_RECEIVE_FLAG SINGLE ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_RECEIVE_FLAG NUMBER(1) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_RECEIVE_FLAG SMALLINT(1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_RETURN_FLAG SINGLE ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_RETURN_FLAG NUMBER(1) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_RETURN_FLAG SMALLINT(1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_RETURN_DATE VARCHAR(19) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_RETURN_DATE VARCHAR2(19) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_RETURN_TYPE SINGLE ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_RETURN_TYPE NUMBER(1) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_RETURN_TYPE SMALLINT(1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_MGT ALTER PM_NAME VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_MGT MODIFY PM_NAME VARCHAR2(255) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_MGT MODIFY PM_NAME VARCHAR(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_REVIEW ALTER PFR_NAME VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_REVIEW MODIFY PFR_NAME VARCHAR2(255) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_REVIEW MODIFY PFR_NAME VARCHAR(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SKILL ALTER SKILL_NAME VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SKILL MODIFY SKILL_NAME VARCHAR2(100) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SKILL MODIFY SKILL_NAME VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMPETENCE ADD CP_ENG_NAME VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPETENCE ADD CP_ENG_NAME VARCHAR2(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_EXTRA_MIDPOINT NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_EXTRA_MIDPOINT NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_EXTRA_MIDPOINT DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_EXTRA_MIDPOINT NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_EXTRA_MIDPOINT NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_EXTRA_MIDPOINT DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_EXTRA_MIDPOINT1 NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_EXTRA_MIDPOINT1 NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_EXTRA_MIDPOINT1 DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_EXTRA_MIDPOINT1 NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_EXTRA_MIDPOINT1 NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_EXTRA_MIDPOINT1 DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_EXTRA_MIDPOINT2 NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_EXTRA_MIDPOINT2 NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_EXTRA_MIDPOINT2 DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_EXTRA_MIDPOINT2 NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_EXTRA_MIDPOINT2 NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_EXTRA_MIDPOINT2 DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_EXTRA_MIDPOINT = 28275, LAYER_EXTRA_MIDPOINT1 = 28270, 
							LAYER_EXTRA_MIDPOINT2 = 30870 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'O3' AND LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LAYER SET LAYER_EXTRA_MIDPOINT = 53365, LAYER_EXTRA_MIDPOINT1 = 53360, 
							LAYER_EXTRA_MIDPOINT2 = 58690 WHERE LAYER_TYPE = 0 AND LEVEL_NO = 'K5' AND LAYER_NO = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_TRAINNER(
				TRAINNER_ID INTEGER NOT NULL,	
				TRAINNER_NAME VARCHAR(255) NOT NULL,
				TN_GENDER SINGLE NULL,
				TN_INOUT_ORG SINGLE NULL,
				TN_BIRTHDATE VARCHAR(19) NULL,		
				TN_EDU_HIS1 MEMO NULL,
				TN_EDU_HIS2 MEMO NULL,
				TN_EDU_HIS3 MEMO NULL,
				TN_POSITION MEMO NULL,
				TN_WORK_PLACE MEMO NULL,
				TN_WORK_TEL MEMO NULL,
				TN_WORK_EXPERIENCE MEMO NULL,
				TN_TRAIN_EXPERIENCE MEMO NULL,
				TN_ADDRESS MEMO NULL,
				TN_ADDRESS_TEL MEMO NULL,
				TN_TECHNOLOGY_HIS MEMO NULL,
				TN_TRAIN_SKILL1 MEMO NULL,
				TN_TRAIN_SKILL2 MEMO NULL,
				TN_TRAIN_SKILL3 MEMO NULL,
				TN_DEPT_TRAIN MEMO NULL,
				TN_SPEC_ABILITY MEMO NULL,
				TN_HOBBY MEMO NULL,
				TN_SEQ INTEGER2 NULL,
				TN_ACTIVE SINGLE NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_TRAINNER PRIMARY KEY (TRAINNER_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_TRAINNER(
				TRAINNER_ID NUMBER(10) NOT NULL,	
				TRAINNER_NAME VARCHAR2(255) NOT NULL,
				TN_GENDER NUMBER(1) NULL,
				TN_INOUT_ORG NUMBER(1) NULL,
				TN_BIRTHDATE VARCHAR2(19) NULL,		
				TN_EDU_HIS1 VARCHAR2(2000) NULL,
				TN_EDU_HIS2 VARCHAR2(2000) NULL,
				TN_EDU_HIS3 VARCHAR2(2000) NULL,
				TN_POSITION VARCHAR2(2000) NULL,
				TN_WORK_PLACE VARCHAR2(2000) NULL,
				TN_WORK_TEL VARCHAR2(2000) NULL,
				TN_WORK_EXPERIENCE VARCHAR2(2000) NULL,
				TN_TRAIN_EXPERIENCE VARCHAR2(2000) NULL,
				TN_ADDRESS VARCHAR2(2000) NULL,
				TN_ADDRESS_TEL VARCHAR2(2000) NULL,
				TN_TECHNOLOGY_HIS VARCHAR2(2000) NULL,
				TN_TRAIN_SKILL1 VARCHAR2(2000) NULL,
				TN_TRAIN_SKILL2 VARCHAR2(2000) NULL,
				TN_TRAIN_SKILL3 VARCHAR2(2000) NULL,
				TN_DEPT_TRAIN VARCHAR2(2000) NULL,
				TN_SPEC_ABILITY VARCHAR2(2000) NULL,
				TN_HOBBY VARCHAR2(2000) NULL,
				TN_SEQ NUMBER(3) NULL,
				TN_ACTIVE NUMBER(1) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_TRAINNER PRIMARY KEY (TRAINNER_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_TRAINNER(
				TRAINNER_ID INTEGER(10) NOT NULL,	
				TRAINNER_NAME VARCHAR(255) NOT NULL,
				TN_GENDER SMALLINT(1) NULL,
				TN_INOUT_ORG SMALLINT(1) NULL,
				TN_BIRTHDATE VARCHAR(19) NULL,		
				TN_EDU_HIS1 TEXT NULL,
				TN_EDU_HIS2 TEXT NULL,
				TN_EDU_HIS3 TEXT NULL,
				TN_POSITION TEXT NULL,
				TN_WORK_PLACE TEXT NULL,
				TN_WORK_TEL TEXT NULL,
				TN_WORK_EXPERIENCE TEXT NULL,
				TN_TRAIN_EXPERIENCE TEXT NULL,
				TN_ADDRESS TEXT NULL,
				TN_ADDRESS_TEL TEXT NULL,
				TN_TECHNOLOGY_HIS TEXT NULL,
				TN_TRAIN_SKILL1 TEXT NULL,
				TN_TRAIN_SKILL2 TEXT NULL,
				TN_TRAIN_SKILL3 TEXT NULL,
				TN_DEPT_TRAIN TEXT NULL,
				TN_SPEC_ABILITY TEXT NULL,
				TN_HOBBY TEXT NULL,
				TN_SEQ SMALLINT(3) NULL,
				TN_ACTIVE SMALLINT(1) NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_TRAINNER PRIMARY KEY (TRAINNER_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_TRAIN_PLAN(
				PLAN_ID INTEGER NOT NULL,	
				PLAN_NAME VARCHAR(255) NOT NULL,
				TP_BUDGET_YEAR VARCHAR(4) NULL,
				TP_INOUT_PLAN SINGLE NULL,
				TP_ZONE INTEGER2 NULL,
				PLAN_ID_REF INTEGER NULL,	
				DEPARTMENT_ID INTEGER NULL,	
				TP_ACTIVE SINGLE NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_TRAIN_PLAN PRIMARY KEY (PLAN_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_TRAIN_PLAN(
				PLAN_ID NUMBER(10) NOT NULL,	
				PLAN_NAME VARCHAR2(255) NOT NULL,
				TP_BUDGET_YEAR VARCHAR2(4) NULL,		
				TP_INOUT_PLAN NUMBER(1) NULL,
				TP_ZONE NUMBER(3) NULL,
				PLAN_ID_REF NUMBER(10) NULL,	
				DEPARTMENT_ID NUMBER(10) NULL,	
				TP_ACTIVE NUMBER(1) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_TRAIN_PLAN PRIMARY KEY (PLAN_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_TRAIN_PLAN(
				PLAN_ID INTEGER(10) NOT NULL,	
				PLAN_NAME VARCHAR(255) NOT NULL,
				TP_BUDGET_YEAR VARCHAR(4) NOT NULL,
				TP_INOUT_PLAN SMALLINT(1) NULL,
				TP_ZONE SMALLINT(3) NOT NULL,
				PLAN_ID_REF INTEGER(10) NULL,	
				DEPARTMENT_ID INTEGER(10) NULL,	
				TP_ACTIVE SMALLINT(1) NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_TRAIN_PLAN PRIMARY KEY (PLAN_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_PROJECT_GROUP(
				PG_ID INTEGER NOT NULL,	
				PG_NAME VARCHAR(255) NOT NULL,
				PG_ACTIVE SINGLE NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_PROJECT_GROUP PRIMARY KEY (PG_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_PROJECT_GROUP(
				PG_ID NUMBER(10) NOT NULL,	
				PG_NAME VARCHAR2(255) NOT NULL,
				PG_ACTIVE NUMBER(1) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_PROJECT_GROUP PRIMARY KEY (PG_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_PROJECT_GROUP(
				PG_ID INTEGER(10) NOT NULL,	
				PG_NAME VARCHAR(255) NOT NULL,
				PG_ACTIVE SMALLINT(1) NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_PROJECT_GROUP PRIMARY KEY (PG_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_PROJECT_PAYMENT(
				PP_ID INTEGER NOT NULL,	
				PP_NAME VARCHAR(255) NOT NULL,
				PP_ACTIVE SINGLE NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_PROJECT_PAYMENT PRIMARY KEY (PP_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_PROJECT_PAYMENT(
				PP_ID NUMBER(10) NOT NULL,	
				PP_NAME VARCHAR2(255) NOT NULL,
				PP_ACTIVE NUMBER(1) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_PROJECT_PAYMENT PRIMARY KEY (PP_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_PROJECT_PAYMENT(
				PP_ID INTEGER(10) NOT NULL,	
				PP_NAME VARCHAR(255) NOT NULL,
				PP_ACTIVE SMALLINT(1) NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_PROJECT_PAYMENT PRIMARY KEY (PP_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_TRAIN_PROJECT(
				PROJ_ID INTEGER NOT NULL,	
				PLAN_ID INTEGER NOT NULL,	
				PROJ_NAME VARCHAR(255) NOT NULL,
				TPJ_BUDGET_YEAR VARCHAR(4) NULL,		
				TPJ_MANAGE_ORG VARCHAR(255) NULL,		
				TPJ_RESPONSE_ORG VARCHAR(255) NULL,		
				TPJ_APP_PER_ID INTEGER NULL,	
				PG_ID INTEGER NOT NULL,	
				TPJ_APP_DATE VARCHAR(19) NULL,		
				TPJ_APP_DOC_NO VARCHAR(100) NULL,		
				TPJ_INOUT_TRAIN SINGLE NULL,
				TPJ_ZONE INTEGER2 NULL,
				PROJ_ID_REF INTEGER NOT NULL,	
				DEPARTMENT_ID INTEGER NOT NULL,	
				TPJ_ACTIVE SINGLE NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_TRAIN_PROJECT PRIMARY KEY (PROJ_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_TRAIN_PROJECT(
				PROJ_ID NUMBER(10) NOT NULL,	
				PLAN_ID NUMBER(10) NOT NULL,	
				PROJ_NAME VARCHAR2(255) NOT NULL,
				TPJ_BUDGET_YEAR VARCHAR2(4) NULL,		
				TPJ_MANAGE_ORG VARCHAR2(255) NULL,		
				TPJ_RESPONSE_ORG VARCHAR2(255) NULL,		
				TPJ_APP_PER_ID NUMBER(10) NULL,	
				PG_ID NUMBER(10) NOT NULL,	
				TPJ_APP_DATE VARCHAR2(19) NULL,		
				TPJ_APP_DOC_NO VARCHAR2(100) NULL,		
				TPJ_INOUT_TRAIN NUMBER(1) NULL,
				TPJ_ZONE NUMBER(3) NULL,
				PROJ_ID_REF NUMBER(10) NULL,	
				DEPARTMENT_ID NUMBER(10) NULL,	
				TPJ_ACTIVE NUMBER(1) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_TRAIN_PROJECT PRIMARY KEY (PROJ_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_TRAIN_PROJECT(
				PROJ_ID INTEGER(10) NOT NULL,	
				PLAN_ID INTEGER(10) NOT NULL,	
				PROJ_NAME VARCHAR(255) NOT NULL,
				TPJ_BUDGET_YEAR VARCHAR(4) NOT NULL,
				TPJ_MANAGE_ORG VARCHAR(255) NULL,		
				TPJ_RESPONSE_ORG VARCHAR(255) NULL,		
				TPJ_APP_PER_ID INTEGER(10) NULL,	
				PG_ID INTEGER(10) NOT NULL,	
				TPJ_APP_DATE VARCHAR(19) NULL,		
				TPJ_APP_DOC_NO VARCHAR(100) NULL,		
				TPJ_INOUT_TRAIN SMALLINT(1) NULL,
				TPJ_ZONE SMALLINT(3) NULL,
				PROJ_ID_REF INTEGER(10) NULL,	
				DEPARTMENT_ID INTEGER(10) NULL,	
				TPJ_ACTIVE SMALLINT(1) NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_TRAIN_PROJECT PRIMARY KEY (PROJ_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_TRAIN_PROJECT_DTL(
				PROJ_ID INTEGER NOT NULL,	
				TR_CODE VARCHAR(10) NOT NULL,	
				TR_CLASS INTEGER2 NOT NULL,
				MAX_DAY INTEGER2 NULL,
				TRAIN_PLACE VARCHAR(255) NOT NULL,
				TARGET_POSITION MEMO NULL,		
				LEVEL_NO_START VARCHAR(10) NULL,		
				LEVEL_NO_END VARCHAR(10) NULL,		
				START_DATE VARCHAR(19) NULL,		
				END_DATE VARCHAR(19) NULL,		
				BUDGET NUMBER NULL,	
				BUDGET_USED NUMBER NULL,	
				LOCAL_TAX NUMBER NULL,	
				LOCAL_TAX_USED NUMBER NULL,	
				PER_DEVELOP_FUND NUMBER NULL,	
				PER_DEVELOP_FUND_USED NUMBER NULL,	
				OTHER_BUDGET NUMBER NULL,	
				OTHER_BUDGET_USED NUMBER NULL,	
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_TRAIN_PROJECT_DTL PRIMARY KEY (PROJ_ID, TR_CODE, TR_CLASS)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_TRAIN_PROJECT_DTL(
				PROJ_ID NUMBER(10) NOT NULL,	
				TR_CODE VARCHAR2(10) NOT NULL,	
				TR_CLASS NUMBER(3) NOT NULL,
				MAX_DAY NUMBER(3) NULL,
				TRAIN_PLACE VARCHAR2(255) NOT NULL,
				TARGET_POSITION VARCHAR2(1000) NULL,		
				LEVEL_NO_START VARCHAR2(10) NULL,		
				LEVEL_NO_END VARCHAR2(10) NULL,		
				START_DATE VARCHAR2(19) NULL,		
				END_DATE VARCHAR2(19) NULL,		
				BUDGET NUMBER(16,2) NULL,	
				BUDGET_USED NUMBER(16,2) NULL,	
				LOCAL_TAX NUMBER(16,2) NULL,	
				LOCAL_TAX_USED NUMBER(16,2) NULL,	
				PER_DEVELOP_FUND NUMBER(16,2) NULL,	
				PER_DEVELOP_FUND_USED NUMBER(16,2) NULL,	
				OTHER_BUDGET NUMBER(16,2) NULL,	
				OTHER_BUDGET_USED NUMBER(16,2) NULL,	
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_TRAIN_PROJECT_DTL PRIMARY KEY (PROJ_ID, TR_CODE, TR_CLASS)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_TRAIN_PROJECT_DTL(
				PROJ_ID INTEGER(10) NOT NULL,	
				TR_CODE VARCHAR(10) NOT NULL,	
				TR_CLASS SMALLINT(3) NOT NULL,
				MAX_DAY SMALLINT(3) NULL,
				TRAIN_PLACE VARCHAR(255) NOT NULL,
				TARGET_POSITION TEXT NULL,		
				LEVEL_NO_START VARCHAR(10) NULL,		
				LEVEL_NO_END VARCHAR(10) NULL,		
				START_DATE VARCHAR(19) NULL,		
				END_DATE VARCHAR(19) NULL,		
				BUDGET DECIMAL(16,2) NULL,	
				BUDGET_USED DECIMAL(16,2) NULL,	
				LOCAL_TAX DECIMAL(16,2) NULL,	
				LOCAL_TAX_USED DECIMAL(16,2) NULL,	
				PER_DEVELOP_FUND DECIMAL(16,2) NULL,	
				PER_DEVELOP_FUND_USED DECIMAL(16,2) NULL,	
				OTHER_BUDGET DECIMAL(16,2) NULL,	
				OTHER_BUDGET_USED DECIMAL(16,2) NULL,	
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_TRAIN_PROJECT_DTL PRIMARY KEY (PROJ_ID, TR_CODE, TR_CLASS)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_TRAIN_PROJECT_PAYMENT(
				PROJ_ID INTEGER NOT NULL,	
				TR_CODE VARCHAR(10) NOT NULL,	
				TR_CLASS INTEGER2 NOT NULL,
				PP_ID INTEGER NOT NULL,
				BUDGET_SOURCE VARCHAR(50) NULL,
				OTHER_PAYMENT VARCHAR(255) NULL,		
				PAY_AMOUNT NUMBER NULL,	
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_TRAIN_PROJECT_PAYMENT PRIMARY KEY (PROJ_ID, TR_CODE, TR_CLASS, PP_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_TRAIN_PROJECT_PAYMENT(
				PROJ_ID NUMBER(10) NOT NULL,	
				TR_CODE VARCHAR2(10) NOT NULL,	
				TR_CLASS NUMBER(3) NOT NULL,
				PP_ID NUMBER(10) NOT NULL,
				BUDGET_SOURCE VARCHAR2(50) NULL,
				OTHER_PAYMENT VARCHAR2(255) NULL,		
				PAY_AMOUNT NUMBER(16,2) NULL,	
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_TRAIN_PROJECT_PAYMENT PRIMARY KEY (PROJ_ID, TR_CODE, TR_CLASS, PP_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_TRAIN_PROJECT_PAYMENT(
				PROJ_ID INTEGER(10) NOT NULL,	
				TR_CODE VARCHAR(10) NOT NULL,	
				TR_CLASS SMALLINT(3) NOT NULL,
				PP_ID INTEGER(10) NOT NULL,
				BUDGET_SOURCE VARCHAR(50) NULL,
				OTHER_PAYMENT VARCHAR(255) NULL,		
				PAY_AMOUNT DECIMAL(16,2) NULL,	
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_TRAIN_PROJECT_PAYMENT PRIMARY KEY (PROJ_ID, TR_CODE, TR_CLASS, PP_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_TRAIN_PROJECT_PERSONAL(
				PROJ_ID INTEGER NOT NULL,	
				TR_CODE VARCHAR(10) NOT NULL,	
				TR_CLASS INTEGER2 NOT NULL,
				PER_ID INTEGER NOT NULL,
				ORG_NAME VARCHAR(255) NULL,
				PL_NAME VARCHAR(255) NULL,		
				LEVEL_NO VARCHAR(10) NULL,		
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_TRAIN_PROJECT_PERSONAL PRIMARY KEY (PROJ_ID, TR_CODE, TR_CLASS, PER_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_TRAIN_PROJECT_PERSONAL(
				PROJ_ID NUMBER(10) NOT NULL,	
				TR_CODE VARCHAR2(10) NOT NULL,	
				TR_CLASS NUMBER(3) NOT NULL,
				PER_ID NUMBER(10) NOT NULL,
				ORG_NAME VARCHAR2(255) NULL,
				PL_NAME VARCHAR2(255) NULL,		
				LEVEL_NO VARCHAR2(10) NULL,		
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_TRAIN_PROJECT_PERSONAL PRIMARY KEY (PROJ_ID, TR_CODE, TR_CLASS, PER_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_TRAIN_PROJECT_PERSONAL(
				PROJ_ID INTEGER(10) NOT NULL,	
				TR_CODE VARCHAR(10) NOT NULL,	
				TR_CLASS SMALLINT(3) NOT NULL,
				PER_ID INTEGER(10) NOT NULL,
				ORG_NAME VARCHAR(255) NULL,
				PL_NAME VARCHAR(255) NULL,		
				LEVEL_NO VARCHAR(10) NULL,		
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_TRAIN_PROJECT_PERSONAL PRIMARY KEY (PROJ_ID, TR_CODE, TR_CLASS, PER_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_YEAR VARCHAR(4) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_YEAR VARCHAR2(4) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_TYPE SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_TYPE NUMBER(1) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_TYPE SMALLINT(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_CLASS INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_CLASS NUMBER(3) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_CLASS SMALLINT(3) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD EN_CODE VARCHAR(10) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD EN_CODE VARCHAR2(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD EM_CODE VARCHAR(10) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD EM_CODE VARCHAR2(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_START_DATE VARCHAR(19) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_START_DATE VARCHAR2(19) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_END_DATE VARCHAR(19) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_END_DATE VARCHAR2(19) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_PLACE VARCHAR(200) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_PLACE VARCHAR2(200) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD CT_CODE_OWN VARCHAR(10) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD CT_CODE_OWN VARCHAR2(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD CT_CODE_GO VARCHAR(10) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD CT_CODE_GO VARCHAR2(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_BUDGET NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_BUDGET NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_BUDGET DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_APP_DOC_NO VARCHAR(50) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_APP_DOC_NO VARCHAR2(50) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_DOC_DATE VARCHAR(19) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_DOC_DATE VARCHAR2(19) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_APP_DATE VARCHAR(19) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_APP_DATE VARCHAR2(19) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_APP_PER_ID INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_APP_PER_ID NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_APP_PER_ID INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_REMARK VARCHAR(200) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_REMARK VARCHAR2(200) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_SCHOLARSHIP DROP CONSTRAINT INXU1_PER_SCHOLARSHIP ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU1_PER_SCHOLARSHIP ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_ORG ON PER_ORG (DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ADD CP_CONFIRM SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ADD CP_CONFIRM NUMBER(1) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ADD CP_CONFIRM SMALLINT(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI_COMPETENCE ALTER KC_EVALUATE NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI_COMPETENCE MODIFY KC_EVALUATE NUMBER(4,2) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI_COMPETENCE MODIFY KC_EVALUATE DECIMAL(4,2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI_COMPETENCE ADD KC_WEIGHT NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI_COMPETENCE ADD KC_WEIGHT NUMBER(5,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI_COMPETENCE ADD KC_WEIGHT DECIMAL(5,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_ID_REF INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_ID_REF NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_ID_REF INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_ID_ASS_REF INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_ID_ASS_REF NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_ID_ASS_REF INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_CONTROL set CTRL_ALTER = 6 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if($CTRL_ALTER < 6) 

		if($CTRL_ALTER < 7) {
			$cmd = " DROP TABLE PER_POS_TYPE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT COUNT(POS_TYPE) AS COUNT_DATA FROM PER_POS_TYPE ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_POS_TYPE (
					POS_TYPE VARCHAR(5) NOT NULL,	
					POS_NAME VARCHAR(100) NOT NULL,
					SEFT_RATIO NUMBER NULL,
					CHIEF_RATIO NUMBER NULL,
					FRIEND_RATIO NUMBER NULL,
					SUB_RATIO NUMBER NULL,
					UPDATE_USER INTEGER2 NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_POS_TYPE  PRIMARY KEY (POS_TYPE)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_POS_TYPE (
					POS_TYPE VARCHAR2(5) NOT NULL,	
					POS_NAME VARCHAR2(100) NOT NULL,
					SEFT_RATIO NUMBER(6,2) NULL,
					CHIEF_RATIO NUMBER(6,2) NULL,
					FRIEND_RATIO NUMBER(6,2) NULL,
					SUB_RATIO NUMBER(6,2) NULL,
					UPDATE_USER NUMBER(5) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_POS_TYPE  PRIMARY KEY (POS_TYPE)) ";
				elseif($DPISDB=="mysql") 
					$cmd = " CREATE TABLE PER_POS_TYPE (
					POS_TYPE VARCHAR(5) NOT NULL,	
					POS_NAME VARCHAR(100) NOT NULL,
					SEFT_RATIO DECIMAL(6,2) NULL,
					CHIEF_RATIO DECIMAL(6,2) NULL,
					FRIEND_RATIO DECIMAL(6,2) NULL,
					SUB_RATIO DECIMAL(6,2) NULL,
					UPDATE_USER SMALLINT(5) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_POS_TYPE  PRIMARY KEY (POS_TYPE)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$code = array( "O", "K", "D", "M" ); 
				$name = array( "�����������", "�������Ԫҡ��", "�������ӹ�¡��", "������������" ); 
				for ( $i=0; $i<count($code); $i++ ) { 
					$cmd = " INSERT INTO PER_POS_TYPE (POS_TYPE, POS_NAME, SEFT_RATIO, CHIEF_RATIO, FRIEND_RATIO, SUB_RATIO, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('$code[$i]1', '$name[$i] �����Թ���ͧ', 100, 0, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " INSERT INTO PER_POS_TYPE (POS_TYPE, POS_NAME, SEFT_RATIO, CHIEF_RATIO, FRIEND_RATIO, SUB_RATIO, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('$code[$i]2', '$name[$i] �����Թ���ѧ�Ѻ�ѭ��', 0, 100, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " INSERT INTO PER_POS_TYPE (POS_TYPE, POS_NAME, SEFT_RATIO, CHIEF_RATIO, FRIEND_RATIO, SUB_RATIO, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('$code[$i]3', '$name[$i] �����Թ���͹�����ҹ', 0, 0, 100, 0, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " INSERT INTO PER_POS_TYPE (POS_TYPE, POS_NAME, SEFT_RATIO, CHIEF_RATIO, FRIEND_RATIO, SUB_RATIO, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('$code[$i]4', '$name[$i] �����Թ�����ѧ�Ѻ�ѭ��', 0, 0, 0, 100, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " INSERT INTO PER_POS_TYPE (POS_TYPE, POS_NAME, SEFT_RATIO, CHIEF_RATIO, FRIEND_RATIO, SUB_RATIO, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('$code[$i]12', '$name[$i] �����Թ���ͧ/�����Թ���ѧ�Ѻ�ѭ��', 10, 90, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " INSERT INTO PER_POS_TYPE (POS_TYPE, POS_NAME, SEFT_RATIO, CHIEF_RATIO, FRIEND_RATIO, SUB_RATIO, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('$code[$i]13', '$name[$i] �����Թ���ͧ/�����Թ���͹�����ҹ', 50, 0, 50, 0, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " INSERT INTO PER_POS_TYPE (POS_TYPE, POS_NAME, SEFT_RATIO, CHIEF_RATIO, FRIEND_RATIO, SUB_RATIO, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('$code[$i]14', '$name[$i] �����Թ���ͧ/�����Թ�����ѧ�Ѻ�ѭ��', 50, 0, 0, 50, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " INSERT INTO PER_POS_TYPE (POS_TYPE, POS_NAME, SEFT_RATIO, CHIEF_RATIO, FRIEND_RATIO, SUB_RATIO, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('$code[$i]23', '$name[$i] �����Թ���ѧ�Ѻ�ѭ��/�����Թ���͹�����ҹ', 0, 60, 40, 0, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " INSERT INTO PER_POS_TYPE (POS_TYPE, POS_NAME, SEFT_RATIO, CHIEF_RATIO, FRIEND_RATIO, SUB_RATIO, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('$code[$i]24', '$name[$i] �����Թ���ѧ�Ѻ�ѭ��/�����Թ�����ѧ�Ѻ�ѭ��', 0, 60, 0, 40, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " INSERT INTO PER_POS_TYPE (POS_TYPE, POS_NAME, SEFT_RATIO, CHIEF_RATIO, FRIEND_RATIO, SUB_RATIO, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('$code[$i]34', '$name[$i] �����Թ���͹�����ҹ/�����Թ�����ѧ�Ѻ�ѭ��', 0, 0, 50, 50, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " INSERT INTO PER_POS_TYPE (POS_TYPE, POS_NAME, SEFT_RATIO, CHIEF_RATIO, FRIEND_RATIO, SUB_RATIO, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('$code[$i]123', '$name[$i] �����Թ���ͧ/�����Թ���ѧ�Ѻ�ѭ��/�����Թ���͹�����ҹ', 10, 70, 20, 0, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " INSERT INTO PER_POS_TYPE (POS_TYPE, POS_NAME, SEFT_RATIO, CHIEF_RATIO, FRIEND_RATIO, SUB_RATIO, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('$code[$i]124', '$name[$i] �����Թ���ͧ/�����Թ���ѧ�Ѻ�ѭ��/�����Թ�����ѧ�Ѻ�ѭ��', 10, 70, 0, 20, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " INSERT INTO PER_POS_TYPE (POS_TYPE, POS_NAME, SEFT_RATIO, CHIEF_RATIO, FRIEND_RATIO, SUB_RATIO, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('$code[$i]134', '$name[$i] �����Թ���ͧ/�����Թ���͹�����ҹ/�����Թ�����ѧ�Ѻ�ѭ��', 30, 0, 40, 30, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " INSERT INTO PER_POS_TYPE (POS_TYPE, POS_NAME, SEFT_RATIO, CHIEF_RATIO, FRIEND_RATIO, SUB_RATIO, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('$code[$i]234', '$name[$i] �����Թ���ѧ�Ѻ�ѭ��/�����Թ���͹�����ҹ/�����Թ�����ѧ�Ѻ�ѭ��', 0, 40, 20, 40, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd = " INSERT INTO PER_POS_TYPE (POS_TYPE, POS_NAME, SEFT_RATIO, CHIEF_RATIO, FRIEND_RATIO, SUB_RATIO, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('$code[$i]1234', '$name[$i] �����Թ���ͧ/�����Թ���ѧ�Ѻ�ѭ��/�����Թ���͹�����ҹ/�����Թ�����ѧ�Ѻ�ѭ��', 10, 70, 10, 10, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} // end for
			} // end if

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD SCORE_OTHER NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD SCORE_OTHER NUMBER(6,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI_FORM ADD SCORE_OTHER DECIMAL(6,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD SUM_OTHER NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD SUM_OTHER NUMBER(6,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI_FORM ADD SUM_OTHER DECIMAL(6,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_KPI_FORM set SUM_OTHER = SCORE_KPI ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_KPI_FORM set SCORE_KPI = NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI_FORM ALTER SCORE_KPI NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI_FORM MODIFY SCORE_KPI NUMBER(6,2) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI_FORM MODIFY SCORE_KPI DECIMAL(6,2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_KPI_FORM set SCORE_KPI = SUM_OTHER ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_KPI_FORM set SUM_OTHER = NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_KPI_FORM set SUM_OTHER = SCORE_COMPETENCE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_KPI_FORM set SCORE_COMPETENCE = NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI_FORM ALTER SCORE_COMPETENCE NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI_FORM MODIFY SCORE_COMPETENCE NUMBER(6,2) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI_FORM MODIFY SCORE_COMPETENCE DECIMAL(6,2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_KPI_FORM set SCORE_COMPETENCE = SUM_OTHER ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_KPI_FORM set SUM_OTHER = NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT COUNT(ADR_ID) AS COUNT_DATA FROM PER_ADDRESS ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_ADDRESS (
					ADR_ID INTEGER NOT NULL,
					PER_ID INTEGER NOT NULL,
					ADR_TYPE SINGLE NOT NULL,	
					ADR_NO VARCHAR(100) NULL,
					ADR_ROAD VARCHAR(255) NULL,
					ADR_SOI VARCHAR(255) NULL,
					ADR_MOO VARCHAR(255) NULL,
					ADR_VILLAGE VARCHAR(255) NULL,
					ADR_BUILDING VARCHAR(255) NULL,
					ADR_DISTRICT VARCHAR(255) NULL,
					AP_CODE VARCHAR(10) NULL,
					PV_CODE VARCHAR(10) NULL,
					ADR_HOME_TEL VARCHAR(100) NULL,
					ADR_OFFICE_TEL VARCHAR(100) NULL,
					ADR_FAX VARCHAR(100) NULL,
					ADR_MOBILE VARCHAR(100) NULL,
					ADR_EMAIL VARCHAR(100) NULL,
					ADR_POSTCODE VARCHAR(100) NULL,
					ADR_REMARK VARCHAR(100) NULL,
					UPDATE_USER INTEGER2 NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_ADDRESS  PRIMARY KEY (ADR_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_ADDRESS (
					ADR_ID NUMBER(10) NOT NULL,
					PER_ID NUMBER(10) NOT NULL,
					ADR_TYPE NUMBER(1) NOT NULL,	
					ADR_NO VARCHAR2(100) NULL,
					ADR_ROAD VARCHAR2(255) NULL,
					ADR_SOI VARCHAR2(255) NULL,
					ADR_MOO VARCHAR2(255) NULL,
					ADR_VILLAGE VARCHAR2(255) NULL,
					ADR_BUILDING VARCHAR2(255) NULL,
					ADR_DISTRICT VARCHAR2(255) NULL,
					AP_CODE VARCHAR2(10) NULL,
					PV_CODE VARCHAR2(10) NULL,
					ADR_HOME_TEL VARCHAR2(100) NULL,
					ADR_OFFICE_TEL VARCHAR2(100) NULL,
					ADR_FAX VARCHAR2(100) NULL,
					ADR_MOBILE VARCHAR2(100) NULL,
					ADR_EMAIL VARCHAR2(100) NULL,
					ADR_POSTCODE VARCHAR2(100) NULL,
					ADR_REMARK VARCHAR2(100) NULL,
					UPDATE_USER NUMBER(5) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_ADDRESS  PRIMARY KEY (ADR_ID)) ";
				elseif($DPISDB=="mysql") 
					$cmd = " CREATE TABLE PER_ADDRESS (
					ADR_ID INTEGER(10) NOT NULL,
					PER_ID INTEGER(10) NOT NULL,
					ADR_TYPE SMALLINT(1) NOT NULL,	
					ADR_NO VARCHAR(100) NULL,
					ADR_ROAD VARCHAR(255) NULL,
					ADR_SOI VARCHAR(255) NULL,
					ADR_MOO VARCHAR(255) NULL,
					ADR_VILLAGE VARCHAR(255) NULL,
					ADR_BUILDING VARCHAR(255) NULL,
					ADR_DISTRICT VARCHAR(255) NULL,
					AP_CODE VARCHAR(10) NULL,
					PV_CODE VARCHAR(10) NULL,
					ADR_HOME_TEL VARCHAR(100) NULL,
					ADR_OFFICE_TEL VARCHAR(100) NULL,
					ADR_FAX VARCHAR(100) NULL,
					ADR_MOBILE VARCHAR(100) NULL,
					ADR_EMAIL VARCHAR(100) NULL,
					ADR_POSTCODE VARCHAR(100) NULL,
					ADR_REMARK VARCHAR(100) NULL,
					UPDATE_USER SMALLINT(5) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_ADDRESS  PRIMARY KEY (ADR_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_CONTACT_PERSON MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_CONTACT_PERSON VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_CONTACT_PERSON TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL1_DESC MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS MODIFY KPI_TARGET_LEVEL1_DESC VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL1_DESC TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL2_DESC MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS MODIFY KPI_TARGET_LEVEL2_DESC VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL2_DESC TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL3_DESC MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS MODIFY KPI_TARGET_LEVEL3_DESC VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL3_DESC TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL4_DESC MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS MODIFY KPI_TARGET_LEVEL4_DESC VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL4_DESC TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL5_DESC MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS MODIFY KPI_TARGET_LEVEL5_DESC VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_TARGET_LEVEL5_DESC TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER PG_RESULT MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS MODIFY PG_RESULT VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER PG_RESULT TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMMAND ADD ORG_ID INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMMAND ADD ORG_ID NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMMAND ADD ORG_ID INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COURSE ADD CO_DAY NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COURSE ADD CO_DAY NUMBER(6,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COURSE ADD CO_DAY DECIMAL(6,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COURSE ADD CO_REMARK VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COURSE ADD CO_REMARK VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COURSEDTL ADD COD_PASS SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COURSEDTL ADD COD_PASS NUMBER(1) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COURSEDTL ADD COD_PASS SMALLINT(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINING ADD TRN_DAY NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINING ADD TRN_DAY NUMBER(6,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_TRAINING ADD TRN_DAY DECIMAL(6,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINING ADD TRN_REMARK VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINING ADD TRN_REMARK VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINING ADD TRN_PASS SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINING ADD TRN_PASS NUMBER(1) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_TRAINING ADD TRN_PASS SMALLINT(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD PERFORMANCE_WEIGHT INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD PERFORMANCE_WEIGHT NUMBER(2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI_FORM ADD PERFORMANCE_WEIGHT SMALLINT(2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD COMPETENCE_WEIGHT INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD COMPETENCE_WEIGHT NUMBER(2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI_FORM ADD COMPETENCE_WEIGHT SMALLINT(2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD OTHER_WEIGHT INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD OTHER_WEIGHT NUMBER(2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI_FORM ADD OTHER_WEIGHT SMALLINT(2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ORG ADD POS_LAT DOUBLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORG ADD POS_LAT FLOAT NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ORG ADD POS_LONG DOUBLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORG ADD POS_LONG FLOAT NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ORG_ASS ADD POS_LAT DOUBLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORG_ASS ADD POS_LAT FLOAT NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ORG_ASS ADD POS_LONG DOUBLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORG_ASS ADD POS_LONG FLOAT NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAIN ALTER TR_CODE VARCHAR(20) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAIN MODIFY TR_CODE VARCHAR2(20) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_TRAIN MODIFY TR_CODE VARCHAR(20) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINING ALTER TR_CODE VARCHAR(20) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINING MODIFY TR_CODE VARCHAR2(20) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_TRAINING MODIFY TR_CODE VARCHAR(20) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COURSE ALTER TR_CODE VARCHAR(20) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COURSE MODIFY TR_CODE VARCHAR2(20) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COURSE MODIFY TR_CODE VARCHAR(20) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAIN_PROJECT_DTL ALTER TR_CODE VARCHAR(20) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAIN_PROJECT_DTL MODIFY TR_CODE VARCHAR2(20) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_TRAIN_PROJECT_DTL MODIFY TR_CODE VARCHAR(20) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAIN_PROJECT_PAYMENT ALTER TR_CODE VARCHAR(20) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAIN_PROJECT_PAYMENT MODIFY TR_CODE VARCHAR2(20) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_TRAIN_PROJECT_PAYMENT MODIFY TR_CODE VARCHAR(20) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAIN_PROJECT_PERSONAL ALTER TR_CODE VARCHAR(20) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAIN_PROJECT_PERSONAL MODIFY TR_CODE VARCHAR2(20) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_TRAIN_PROJECT_PERSONAL MODIFY TR_CODE VARCHAR(20) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_START_DATE2 VARCHAR(19) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_START_DATE2 VARCHAR2(19) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_END_DATE2 VARCHAR(19) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_END_DATE2 VARCHAR2(19) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_PLACE2 VARCHAR(200) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_PLACE2 VARCHAR2(200) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_DEAD_LINE VARCHAR(19) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLARSHIP ADD SCH_DEAD_LINE VARCHAR2(19) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLAR ADD SC_TEST_DATE VARCHAR(19) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLAR ADD SC_TEST_DATE VARCHAR2(19) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SCHOLAR ADD SC_TEST_RESULT NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLAR ADD SC_TEST_RESULT NUMBER(6,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SCHOLAR ADD SC_TEST_RESULT DECIMAL(6,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLAR ADD SC_REMARK VARCHAR(200) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLAR ADD SC_REMARK VARCHAR2(200) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT COUNT(MSG_ID) AS COUNT_DATA FROM PER_MESSAGE ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_MESSAGE (
					MSG_ID INTEGER NOT NULL,
					MSG_HEADER VARCHAR(255) NULL,
					MSG_DETAIL MEMO NULL,
					MSG_SOURCE VARCHAR(255) NULL,
					MSG_POST_DATE VARCHAR(19) NULL,
					MSG_START_DATE VARCHAR(19) NULL,
					MSG_FINISH_DATE VARCHAR(19) NULL,
					USER_ID INTEGER2 NOT NULL,
					MSG_TYPE SINGLE NOT NULL,	
					MSG_DOCUMENT VARCHAR(255) NULL,
					MSG_ORG_NAME VARCHAR(255) NULL,
					MSG_SHOW SINGLE NOT NULL,	
					UPDATE_USER INTEGER2 NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_MESSAGE  PRIMARY KEY (MSG_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_MESSAGE (
					MSG_ID NUMBER(10) NOT NULL,
					MSG_HEADER VARCHAR2(255) NULL,
					MSG_DETAIL VARCHAR2(4000) NULL,
					MSG_SOURCE VARCHAR2(255) NULL,
					MSG_POST_DATE VARCHAR2(19) NULL,
					MSG_START_DATE VARCHAR2(19) NULL,
					MSG_FINISH_DATE VARCHAR2(19) NULL,
					USER_ID NUMBER(5) NOT NULL,
					MSG_TYPE NUMBER(1) NOT NULL,	
					MSG_DOCUMENT VARCHAR2(255) NULL,
					MSG_ORG_NAME VARCHAR2(255) NULL,
					MSG_SHOW NUMBER(1) NOT NULL,	
					UPDATE_USER NUMBER(5) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_MESSAGE  PRIMARY KEY (MSG_ID)) ";
				elseif($DPISDB=="mysql") 
					$cmd = " CREATE TABLE PER_MESSAGE (
					MSG_ID INTEGER(10) NOT NULL,
					MSG_HEADER VARCHAR(255) NULL,
					MSG_DETAIL TEXT NULL,
					MSG_SOURCE VARCHAR(255) NULL,
					MSG_POST_DATE VARCHAR(19) NULL,
					MSG_START_DATE VARCHAR(19) NULL,
					MSG_FINISH_DATE VARCHAR(19) NULL,
					USER_ID SMALLINT(5) NOT NULL,
					MSG_TYPE SMALLINT(1) NOT NULL,	
					MSG_DOCUMENT VARCHAR(255) NULL,
					MSG_ORG_NAME VARCHAR(255) NULL,
					MSG_SHOW SMALLINT(1) NOT NULL,	
					UPDATE_USER SMALLINT(5) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_MESSAGE  PRIMARY KEY (MSG_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if

			$cmd = " SELECT COUNT(PL_CODE) AS COUNT_DATA FROM PER_STANDARD_COMPETENCE ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_STANDARD_COMPETENCE(
					LEVEL_NO VARCHAR(10) NOT NULL,
					CP_CODE VARCHAR(3) NOT NULL,	
					TARGET_LEVEL SINGLE NULL,
					UPDATE_USER INTEGER2 NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_STANDARD_COMPETENCE PRIMARY KEY (LEVEL_NO, CP_CODE)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_STANDARD_COMPETENCE(
					LEVEL_NO VARCHAR2(10) NOT NULL,
					CP_CODE VARCHAR2(3) NOT NULL,	
					TARGET_LEVEL NUMBER(1) NULL,
					UPDATE_USER NUMBER(5) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_STANDARD_COMPETENCE PRIMARY KEY (LEVEL_NO, CP_CODE)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_STANDARD_COMPETENCE(
					LEVEL_NO VARCHAR(10) NOT NULL,
					CP_CODE VARCHAR(3) NOT NULL,	
					TARGET_LEVEL SMALLINT(1) NULL,
					UPDATE_USER SMALLINT(5) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_STANDARD_COMPETENCE PRIMARY KEY (LEVEL_NO, CP_CODE)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if

			$level = array(	"O1", "O2", "O3", "O4", "K1", "K2", "K3", "K4", "K5", "D1", "D2", "M1", "M2" );
			$code = array(	"101", "102", "103", "104", "105", "106", "301", "302", "303", "304", "305", "306", "307", "308", "309", "310", "311", "312", 
											"313", "314", "315", "316", "317", "318" );
			$target = array(	1, 1, 2, 3, 1, 2, 3, 4, 5, 3, 4, 4, 5 );
			for ( $i=0; $i<count($level); $i++ ) { 
				for ( $j=0; $j<count($code); $j++ ) { 
					if ($level[$i]=="O1" && substr($code[$j],0,1)=="3") $TARGET_LEVEL = 0; else $TARGET_LEVEL = $target[$i]; 
					$cmd = " INSERT INTO PER_STANDARD_COMPETENCE (LEVEL_NO, CP_CODE, TARGET_LEVEL, UPDATE_USER, 
									UPDATE_DATE)
									VALUES ('$level[$i]', '$code[$j]', $TARGET_LEVEL,  $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} // end for
			} // end for
		
			$level = array(	"D1", "D2", "M1", "M2" );
			$code = array(	"201", "202", "203", "204", "205", "206" );
			$target = array(	1, 2, 3, 4 );
			for ( $i=0; $i<count($level); $i++ ) { 
				for ( $j=0; $j<count($code); $j++ ) { 
					$cmd = " INSERT INTO PER_STANDARD_COMPETENCE (LEVEL_NO, CP_CODE, TARGET_LEVEL, UPDATE_USER, 
									UPDATE_DATE)
									VALUES ('$level[$i]', '$code[$j]', $target[$i],  $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} // end for
			} // end for
		
			$cmd = " ALTER TABLE PER_COMPETENCE ADD CP_ASSESSMENT CHAR(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET MENU_LABEL = 'P05 ����͡�ҡ��ǹ�Ҫ���' 
							  WHERE MENU_LABEL = 'P05 ����͡�ҡ�Ҫ���' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = 'P0502 �ѭ��Ṻ���¤�����͡�ҡ��ǹ�Ҫ���' 
							  WHERE LINKTO_WEB = 'data_retire_comdtl.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET MENU_LABEL = 'A01 �дѺ�š�û����Թ��ѡ' 
							  WHERE LINKTO_WEB = 'master_table_assess_main.html?table=PER_ASSESS_MAIN' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET MENU_LABEL = 'A02 �дѺ�š�û����Թ����' 
							  WHERE LINKTO_WEB = 'master_table_assess_level.html?table=PER_ASSESS_LEVEL' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET MENU_LABEL = 'A03 �š�û����Թ��û�Ժѵ��Ҫ��âͧ����Ҫ���' 
							  WHERE LINKTO_WEB = 'personal_kpi.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET MENU_LABEL = 'A04 �ѧ�Ѵ�ͧ�������͹�Թ��͹' 
							  WHERE LINKTO_WEB = 'personal_competency.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET MENU_LABEL = 'A05 ��ú�����ǧ�Թ������ҳ����͹�Թ��͹' 
							  WHERE LINKTO_WEB = 'data_compensation_test.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET MENU_LABEL = 'A06 ���ҧ�ѭ��Ṻ���¤��������͹�Թ��͹' 
							  WHERE LINKTO_WEB = 'data_compensation_salpromote_comdtl.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " ALTER TABLE SYSTEM_CONFIG MODIFY CONFIG_VALUE TEXT ";
			$db->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ADD ORG_ID_1 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ADD ORG_ID_1 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ADD ORG_ID_1 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_START_ORG VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_START_ORG VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_COOPERATIVE SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_COOPERATIVE NUMBER(1) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_COOPERATIVE SMALLINT(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_COOPERATIVE_NO VARCHAR(25) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_COOPERATIVE_NO VARCHAR2(25) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_MEMBERDATE VARCHAR(19) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_MEMBERDATE VARCHAR2(19) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_PM_NAME VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_PM_NAME VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_PL_NAME VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_PL_NAME VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD LEVEL_NO VARCHAR(10) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD LEVEL_NO VARCHAR2(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_POS_NO VARCHAR(15) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_POS_NO VARCHAR2(15) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_POSITION VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_POSITION VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_ORG VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_ORG VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD EX_CODE VARCHAR(10) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD EX_CODE VARCHAR2(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_PAY_NO VARCHAR(15) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_PAY_NO VARCHAR2(15) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ASSESS_MAIN ADD AM_SHOW SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ASSESS_MAIN ADD AM_SHOW NUMBER(1) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ASSESS_MAIN ADD AM_SHOW SMALLINT(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PRENAME ADD PN_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PRENAME ADD PN_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PRENAME ADD PN_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_RELIGION ADD RE_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_RELIGION ADD RE_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_RELIGION ADD RE_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COUNTRY ADD CT_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COUNTRY ADD CT_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COUNTRY ADD CT_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PROVINCE ADD PV_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PROVINCE ADD PV_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PROVINCE ADD PV_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_AMPHUR ADD AP_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_AMPHUR ADD AP_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_AMPHUR ADD AP_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LINE ADD PL_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LINE ADD PL_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LINE ADD PL_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LINE_GROUP ADD LG_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LINE_GROUP ADD LG_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LINE_GROUP ADD LG_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ABSENTTYPE ADD AB_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ABSENTTYPE ADD AB_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ABSENTTYPE ADD AB_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_STATUS ADD PS_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_STATUS ADD PS_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_STATUS ADD PS_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_MGT ADD PM_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_MGT ADD PM_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_MGT ADD PM_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TYPE ADD PT_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TYPE ADD PT_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_TYPE ADD PT_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EDUCLEVEL ADD EL_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EDUCLEVEL ADD EL_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_EDUCLEVEL ADD EL_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EDUCNAME ADD EN_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EDUCNAME ADD EN_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_EDUCNAME ADD EN_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EDUCMAJOR ADD EM_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EDUCMAJOR ADD EM_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_EDUCMAJOR ADD EM_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAIN ADD TR_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAIN ADD TR_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_TRAIN ADD TR_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_MOVMENT ADD MOV_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_MOVMENT ADD MOV_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_MOVMENT ADD MOV_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_CRIME ADD CR_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_CRIME ADD CR_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_CRIME ADD CR_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_CRIME_DTL ADD CRD_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_CRIME_DTL ADD CRD_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_CRIME_DTL ADD CRD_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SCHOLARTYPE ADD ST_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLARTYPE ADD ST_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SCHOLARTYPE ADD ST_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_HOLIDAY ADD HOL_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_HOLIDAY ADD HOL_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_HOLIDAY ADD HOL_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ABILITYGRP ADD AL_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ABILITYGRP ADD AL_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ABILITYGRP ADD AL_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_CONDITION ADD PC_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_CONDITION ADD PC_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_CONDITION ADD PC_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SERVICE ADD SV_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SERVICE ADD SV_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SERVICE ADD SV_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LAYER ADD LAYER_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_INSTITUTE ADD INS_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_INSTITUTE ADD INS_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_INSTITUTE ADD INS_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SKILL_GROUP ADD SG_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SKILL_GROUP ADD SG_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SKILL_GROUP ADD SG_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SKILL ADD SKILL_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SKILL ADD SKILL_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SKILL ADD SKILL_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SPECIAL_SKILLGRP ADD SS_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SPECIAL_SKILLGRP ADD SS_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SPECIAL_SKILLGRP ADD SS_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_DECORATION ADD DC_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_DECORATION ADD DC_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_DECORATION ADD DC_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ORG_TYPE ADD OT_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORG_TYPE ADD OT_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ORG_TYPE ADD OT_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ORG_LEVEL ADD OL_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORG_LEVEL ADD OL_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ORG_LEVEL ADD OL_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ORG_STAT ADD OS_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORG_STAT ADD OS_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ORG_STAT ADD OS_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_MGTSALARY ADD MS_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_MGTSALARY ADD MS_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_MGTSALARY ADD MS_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_OFF_TYPE ADD OT_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_OFF_TYPE ADD OT_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_OFF_TYPE ADD OT_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_MARRIED ADD MR_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_MARRIED ADD MR_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_MARRIED ADD MR_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_BLOOD ADD BL_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_BLOOD ADD BL_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_BLOOD ADD BL_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LAYEREMP ADD LAYERE_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LAYEREMP ADD LAYERE_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LAYEREMP ADD LAYERE_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LAYEREMP_NEW ADD LAYERE_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LAYEREMP_NEW ADD LAYERE_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LAYEREMP_NEW ADD LAYERE_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EXTRATYPE ADD EX_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EXTRATYPE ADD EX_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_EXTRATYPE ADD EX_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EXTRA_INCOME_TYPE ADD EXIN_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EXTRA_INCOME_TYPE ADD EXIN_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_EXTRA_INCOME_TYPE ADD EXIN_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_CO_LEVEL ADD CL_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_CO_LEVEL ADD CL_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_CO_LEVEL ADD CL_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PENALTY ADD PEN_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PENALTY ADD PEN_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PENALTY ADD PEN_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_HEIRTYPE ADD HR_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_HEIRTYPE ADD HR_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_HEIRTYPE ADD HR_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SERVICETITLE ADD SRT_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SERVICETITLE ADD SRT_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SERVICETITLE ADD SRT_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_REWARD ADD REW_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REWARD ADD REW_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_REWARD ADD REW_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_DIVORCE ADD DV_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_DIVORCE ADD DV_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_DIVORCE ADD DV_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TIME ADD TIME_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TIME ADD TIME_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_TIME ADD TIME_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMTYPE ADD COM_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMTYPE ADD COM_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMTYPE ADD COM_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_GROUP ADD PG_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_GROUP ADD PG_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_GROUP ADD PG_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_NAME ADD PN_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_NAME ADD PN_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_NAME ADD PN_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ORG_PROVINCE ADD OP_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORG_PROVINCE ADD OP_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ORG_PROVINCE ADD OP_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LAYER_NEW ADD LAYER_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EMPSER_POS_NAME ADD EP_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EMPSER_POS_NAME ADD EP_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_EMPSER_POS_NAME ADD EP_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_OCCUPATION ADD OC_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_OCCUPATION ADD OC_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_OCCUPATION ADD OC_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITION ADD POS_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITION ADD POS_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITION ADD POS_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_EMP ADD POEM_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMP ADD POEM_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_EMP ADD POEM_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD POEMS_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD POEMS_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD POEMS_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORK_LOCATION ADD WL_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORK_LOCATION ADD WL_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_WORK_LOCATION ADD WL_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORK_CYCLE ADD WC_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORK_CYCLE ADD WC_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_WORK_CYCLE ADD WC_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE ADD PF_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE ADD PF_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE ADD PF_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_JOB_FAMILY ADD JF_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_JOB_FAMILY ADD JF_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_JOB_FAMILY ADD JF_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_GOODNESS ADD GN_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_GOODNESS ADD GN_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_GOODNESS ADD GN_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_OCCUPATION ADD OC_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_OCCUPATION ADD OC_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_OCCUPATION ADD OC_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_OCCUPATION ADD OC_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_OCCUPATION ADD OC_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_OCCUPATION ADD OC_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_OCCUPATION ADD OC_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_OCCUPATION ADD OC_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_OCCUPATION ADD OC_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_CONTROL set CTRL_ALTER = 7 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if($CTRL_ALTER < 7) 

		if($CTRL_ALTER < 8) {
			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EXTRAHIS ADD EXH_ORG_NAME VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EXTRAHIS ADD EXH_ORG_NAME VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EXTRAHIS ADD EXH_DOCNO VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EXTRAHIS ADD EXH_DOCNO VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EXTRAHIS ADD EXH_DOCDATE VARCHAR(19) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EXTRAHIS ADD EXH_DOCDATE VARCHAR2(19) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EXTRAHIS ADD EXH_SALARY NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EXTRAHIS ADD EXH_SALARY NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_EXTRAHIS ADD EXH_SALARY DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EXTRAHIS ADD EXH_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EXTRAHIS ADD EXH_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_EXTRAHIS ADD EXH_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EDUCATE ALTER EDU_STARTYEAR NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EDUCATE MODIFY EDU_STARTYEAR NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EDUCATE MODIFY EDU_STARTYEAR NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EDUCATE ALTER EDU_ENDYEAR NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EDUCATE MODIFY EDU_ENDYEAR NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EDUCATE MODIFY EDU_ENDYEAR NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EDUCATE ADD EL_CODE VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EDUCATE ADD EL_CODE VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EDUCATE ADD EDU_ENDDATE VARCHAR(19) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EDUCATE ADD EDU_ENDDATE VARCHAR2(19) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EDUCATE ADD EDU_GRADE NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EDUCATE ADD EDU_GRADE NUMBER(6,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_EDUCATE ADD EDU_GRADE DECIMAL(6,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EDUCATE ADD EDU_HONOR VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EDUCATE ADD EDU_HONOR VARCHAR2(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EDUCATE ADD EDU_BOOK_NO VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EDUCATE ADD EDU_BOOK_NO VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EDUCATE ADD EDU_BOOK_DATE VARCHAR(19) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EDUCATE ADD EDU_BOOK_DATE VARCHAR2(19) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EDUCATE ADD EDU_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EDUCATE ADD EDU_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_EDUCATE ADD EDU_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EDUCATE ADD EDU_INSTITUTE VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EDUCATE ADD EDU_INSTITUTE VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_EDUCATE ADD EDU_INSTITUTE VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_EDUCATE ADD EDU_INSTITUTE VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINING ALTER TRN_STARTDATE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINING MODIFY TRN_STARTDATE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINING MODIFY TRN_STARTDATE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINING ALTER TRN_ENDDATE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINING MODIFY TRN_ENDDATE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINING MODIFY TRN_ENDDATE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINING ADD TRN_BOOK_NO VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINING ADD TRN_BOOK_NO VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINING ADD TRN_BOOK_DATE VARCHAR(19) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINING ADD TRN_BOOK_DATE VARCHAR2(19) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINING ADD TRN_PROJECT_NAME VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINING ADD TRN_PROJECT_NAME VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINING ADD TRN_COURSE_NAME VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINING ADD TRN_COURSE_NAME VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINING ADD TRN_DEGREE_RECEIVE VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINING ADD TRN_DEGREE_RECEIVE VARCHAR2(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINING ADD TRN_POINT VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINING ADD TRN_POINT VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_REWARDHIS ADD REH_YEAR VARCHAR(4) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REWARDHIS ADD REH_YEAR VARCHAR2(4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_REWARDHIS ADD REH_PERFORMANCE VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REWARDHIS ADD REH_PERFORMANCE VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_REWARDHIS ADD REH_OTHER_PERFORMANCE VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REWARDHIS ADD REH_OTHER_PERFORMANCE VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_REWARDHIS ADD REH_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REWARDHIS ADD REH_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_REWARDHIS ADD REH_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_MARRHIS ADD PN_CODE VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_MARRHIS ADD PN_CODE VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_MARRHIS ADD MAH_MARRY_NO VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_MARRHIS ADD MAH_MARRY_NO VARCHAR2(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_MARRHIS ADD MAH_MARRY_ORG VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_MARRHIS ADD MAH_MARRY_ORG VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_MARRHIS ADD PV_CODE VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_MARRHIS ADD PV_CODE VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_MARRHIS ADD MR_CODE VARCHAR(2) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_MARRHIS ADD MR_CODE VARCHAR2(2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_MARRHIS ADD MAH_BOOK_NO VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_MARRHIS ADD MAH_BOOK_NO VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_MARRHIS ADD MAH_BOOK_DATE VARCHAR(19) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_MARRHIS ADD MAH_BOOK_DATE VARCHAR2(19) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_MARRHIS ADD MAH_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_MARRHIS ADD MAH_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_MARRHIS ADD MAH_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_NAMEHIS ADD NH_ORG VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_NAMEHIS ADD NH_ORG VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_NAMEHIS ADD PN_CODE_NEW VARCHAR(3) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_NAMEHIS ADD PN_CODE_NEW VARCHAR2(3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_NAMEHIS ADD NH_NAME_NEW VARCHAR(50) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_NAMEHIS ADD NH_NAME_NEW VARCHAR2(50) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_NAMEHIS ADD NH_SURNAME_NEW VARCHAR(50) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_NAMEHIS ADD NH_SURNAME_NEW VARCHAR2(50) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_NAMEHIS ADD NH_BOOK_NO VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_NAMEHIS ADD NH_BOOK_NO VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_NAMEHIS ADD NH_BOOK_DATE VARCHAR(19) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_NAMEHIS ADD NH_BOOK_DATE VARCHAR2(19) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_NAMEHIS ADD NH_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_NAMEHIS ADD NH_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_NAMEHIS ADD NH_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_RECEIVE_DATE VARCHAR(19) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_RECEIVE_DATE VARCHAR2(19) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_BOOK_NO VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_BOOK_NO VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_BOOK_DATE VARCHAR(19) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_BOOK_DATE VARCHAR2(19) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TIMEHIS ADD TIMEH_BOOK_NO VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TIMEHIS ADD TIMEH_BOOK_NO VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TIMEHIS ADD TIMEH_BOOK_DATE VARCHAR(19) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TIMEHIS ADD TIMEH_BOOK_DATE VARCHAR2(19) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_TIMEHIS ADD TIMEH_BOOK_NO VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_TIMEHIS ADD TIMEH_BOOK_NO VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_TIMEHIS ADD TIMEH_BOOK_DATE VARCHAR(19) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_TIMEHIS ADD TIMEH_BOOK_DATE VARCHAR2(19) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITION ADD PAY_NO VARCHAR(15) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITION ADD PAY_NO VARCHAR2(15) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PAY_ID INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PAY_ID NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERSONAL ADD PAY_ID INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			$cmd = " UPDATE PER_PERSONAL SET PAY_ID = POS_ID WHERE POS_ID IS NOT NULL AND PER_TYPE = 1 AND PER_STATUS =  1 AND PAY_ID IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_EMP ADD POEM_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMP ADD POEM_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_EMP ADD POEM_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD POEMS_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD POEMS_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD POEMS_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_REWARDHIS ALTER REH_ORG VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REWARDHIS MODIFY REH_ORG VARCHAR2(255) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_REWARDHIS MODIFY REH_ORG VARCHAR(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REWARDHIS DROP CONSTRAINT FK2_PER_REWARDHIS ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_REWARD ALTER REW_CODE CHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REWARD MODIFY REW_CODE CHAR(10) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_REWARD MODIFY REW_CODE CHAR(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_REWARDHIS ALTER REW_CODE CHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REWARDHIS MODIFY REW_CODE CHAR(10) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_REWARDHIS MODIFY REW_CODE CHAR(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REWARDHIS ADD CONSTRAINT FK2_PER_REWARDHIS FOREIGN KEY (REW_CODE) REFERENCES PER_REWARD (REW_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_REWARDHIS ALTER REH_ORG VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REWARDHIS MODIFY REH_ORG VARCHAR2(255) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_REWARDHIS MODIFY REH_ORG VARCHAR(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_REWARDHIS DROP CONSTRAINT FK2_PER_WORKFLOW_REWARDHIS ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_REWARDHIS ALTER REW_CODE CHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_REWARDHIS MODIFY REW_CODE CHAR(10) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_REWARDHIS MODIFY REW_CODE CHAR(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_REWARDHIS ADD CONSTRAINT FK2_PER_WORKFLOW_REWARDHIS FOREIGN KEY (REW_CODE) REFERENCES PER_REWARD (REW_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EXTRATYPE ALTER EX_CODE CHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EXTRATYPE MODIFY EX_CODE CHAR(10) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EXTRATYPE MODIFY EX_CODE CHAR(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EXTRATYPE ADD EX_SHORTNAME VARCHAR(30) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EXTRATYPE ADD EX_SHORTNAME VARCHAR2(30) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EXTRATYPE ADD EX_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EXTRATYPE ADD EX_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_EXTRATYPE ADD EX_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EXTRAHIS ALTER EX_CODE CHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EXTRAHIS MODIFY EX_CODE CHAR(10) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EXTRAHIS MODIFY EX_CODE CHAR(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP INDEX INXU1_PER_EXTRATYPE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX INXU1_PER_EXTRATYPE ON PER_EXTRATYPE (EX_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_KPI ADD CONSTRAINT FK1_PER_KPI 
							  FOREIGN KEY (KPI_PER_ID) REFERENCES PER_PERSONAL (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_KPI ADD CONSTRAINT FK2_PER_KPI 
							  FOREIGN KEY (PFR_ID) REFERENCES PER_PERFORMANCE_REVIEW (PFR_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_KPI ADD CONSTRAINT FK3_PER_KPI 
							  FOREIGN KEY (KPI_ID_REF) REFERENCES PER_KPI (KPI_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_KPI ADD CONSTRAINT FK4_PER_KPI 
							  FOREIGN KEY (DEPARTMENT_ID) REFERENCES PER_ORG (ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET MENU_LABEL = 'P04 �Թ��͹' 
							  WHERE MENU_LABEL = 'P04 ����͹����Թ��͹' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'data_salreceive_comdtl.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 7, 'P0407 �ѭ��Ṻ���¤����������Ѻ�Թ��͹����ز�', 'S', 'W', 'data_salreceive_comdtl.html', 0, 35, 244, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 7, 'P0407 �ѭ��Ṻ���¤����������Ѻ�Թ��͹����ز�', 'S', 'W', 'data_salreceive_comdtl.html', 0, 35, 244, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'data_move_salary.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 8, 'P0408 �ѭ��Ṻ���¤���觵Ѵ�͹�ѵ���Թ��͹����Ҫ���', 'S', 'W', 'data_move_salary.html', 0, 35, 244, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 8, 'P0408 �ѭ��Ṻ���¤���觵Ѵ�͹�ѵ���Թ��͹����Ҫ���', 'S', 'W', 'data_move_salary.html', 0, 35, 244, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT COUNT(MSG_ID) AS COUNT_DATA FROM PER_MESSAGE_USER ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_MESSAGE_USER (
					MSG_ID INTEGER NOT NULL,
					USER_ID INTEGER NOT NULL,
					MSG_STATUS SINGLE NOT NULL,	
					MSG_READ VARCHAR(19) NULL,		
					UPDATE_USER INTEGER2 NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_MESSAGE_USER  PRIMARY KEY (MSG_ID, USER_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_MESSAGE_USER (
					MSG_ID NUMBER(10) NOT NULL,
					USER_ID NUMBER(5) NOT NULL,
					MSG_STATUS NUMBER(1) NOT NULL,	
					MSG_READ VARCHAR2(19) NULL,		
					UPDATE_USER NUMBER(5) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_MESSAGE_USER  PRIMARY KEY (MSG_ID, USER_ID)) ";
				elseif($DPISDB=="mysql") 
					$cmd = " CREATE TABLE PER_MESSAGE_USER (
					MSG_ID INTEGER(10) NOT NULL,
					USER_ID INTEGER(10) NOT NULL,
					MSG_STATUS SMALLINT(1) NOT NULL,	
					MSG_READ VARCHAR(19) NULL,		
					UPDATE_USER SMALLINT(5) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_MESSAGE_USER  PRIMARY KEY (MSG_ID, USER_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
							  UPDATE_BY)
							  VALUES (1, 'TH', 388, 15, 'P15 �ѡ���Ҫ���᷹/�ѡ�ҡ��㹵��˹�/�����Ҫ���', 'S', 'N', 0, 35, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
							  UPDATE_BY)
							  VALUES (1, 'EN', 388, 15, 'P15 �ѡ���Ҫ���᷹/�ѡ�ҡ��㹵��˹�/�����Ҫ���', 'S', 'N', 0, 35, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'data_acting_comdtl.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 1, 'P1501 �ѭ��Ṻ���¤�����ѡ���Ҫ���᷹/�ѡ�ҡ��㹵��˹�', 'S', 'W', 'data_acting_comdtl.html', 0, 35, 388, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 1, 'P1501 �ѭ��Ṻ���¤�����ѡ���Ҫ���᷹/�ѡ�ҡ��㹵��˹�', 'S', 'W', 'data_acting_comdtl.html', 0, 35, 388, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'data_assign_comdtl.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 2, 'P1502 �ѭ��Ṻ���¤�����ͺ������黯Ժѵ��Ҫ���/��Ժѵ��Ҫ���᷹', 'S', 'W', 'data_assign_comdtl.html', 0, 35,
								  388, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
	
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 2, 'P1502 �ѭ��Ṻ���¤�����ͺ������黯Ժѵ��Ҫ���/��Ժѵ��Ҫ���᷹', 'S', 'W', 'data_assign_comdtl.html', 0, 35,
								  388, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'data_help_comdtl.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 3, 'P1503 �ѭ��Ṻ���¤���觪����Ҫ���', 'S', 'W', 'data_help_comdtl.html', 0, 35,
								  388, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
	
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 3, 'P1503 �ѭ��Ṻ���¤���觪����Ҫ���', 'S', 'W', 'data_help_comdtl.html', 0, 35,
								  388, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
							  UPDATE_BY)
							  VALUES (1, 'TH', 403, 16, 'P16 �ѹ�֡�����Ũҡ��ǹ�����Ҥ', 'S', 'W', 'personal_workflow.html', 0, 35, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);	
			//$db->show_error();	

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
							  UPDATE_BY)
							  VALUES (1, 'EN', 403, 16, 'P16 �ѹ�֡�����Ũҡ��ǹ�����Ҥ', 'S', 'W', 'personal_workflow.html', 0, 35, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
							  UPDATE_BY)
							  VALUES (1, 'TH', 404, 17, 'P17 �����¡��ԡ��������/��䢤���觷��Դ��Ҵ', 'S', 'W', 'data_cancel_comdtl.html', 0, 35, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();
	
			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
							  UPDATE_BY)
							  VALUES (1, 'EN', 404, 17, 'P17 �����¡��ԡ��������/��䢤���觷��Դ��Ҵ', 'S', 'W', 'data_cancel_comdtl.html', 0, 35, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
							  UPDATE_BY)
							  VALUES (1, 'TH', 405, 10, 'R10 ��§ҹ�ͧ�����û���ͧ', 'S', 'N', 0, 36, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
							  FLAG_SHOW, TYPE_LINKTO, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, UPDATE_DATE, 
							  UPDATE_BY)
							  VALUES (1, 'EN', 405, 10, 'R10 ��§ҹ�ͧ�����û���ͧ', 'S', 'N', 0, 36, $CREATE_DATE, $CREATE_BY, 
							  $CREATE_DATE, $CREATE_BY) ";
			$db->send_cmd($cmd);	
			//$db->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'rpt_R010031.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 1, 'R1031 �ӹǹ����Ҫ��þ����͹ ��ṡ������˹�㹡�ú����çҹ����дѺ���˹�', 'S', 'W', 'rpt_R010031.html', 0, 36, 405, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 1, 'R1031 �ӹǹ����Ҫ��þ����͹ ��ṡ������˹�㹡�ú����çҹ����дѺ���˹�', 'S', 'W', 'rpt_R010031.html', 0, 36, 405, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'rpt_R010033.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 3, 'R1033 �ӹǹ����Ҫ��þ����͹ ��ṡ������˹�㹡�ú����çҹ����ѧ�Ѵ', 'S', 'W', 'rpt_R010033.html', 0, 36, 405, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 3, 'R1033 �ӹǹ����Ҫ��þ����͹ ��ṡ������˹�㹡�ú����çҹ����ѧ�Ѵ', 'S', 'W', 'rpt_R010033.html', 0, 36, 405, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMDTL ADD AM_SHOW SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL ADD AM_SHOW NUMBER(1) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMDTL ADD AM_SHOW SMALLINT(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " update PER_CONTROL set CTRL_ALTER = 8 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if($CTRL_ALTER < 8) 

		if($CTRL_ALTER < 9) {
			$cmd = " SELECT COUNT(PD_PLAN_ID) AS COUNT_DATA FROM PER_DEVELOPE_PLAN ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_DEVELOPE_PLAN (
					PD_PLAN_ID INTEGER NOT NULL,
					PD_PLAN_KF_ID INTEGER NOT NULL,
					PD_GUIDE_ID INTEGER NOT NULL,
					PLAN_FREE_TEXT MEMO NULL,
					PD_PLAN_START VARCHAR(19) NULL,
					PD_PLAN_END VARCHAR(19) NULL,
					UPDATE_USER INTEGER2 NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_DEVELOPE_PLAN  PRIMARY KEY (PD_PLAN_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_DEVELOPE_PLAN (
					PD_PLAN_ID NUMBER(10) NOT NULL,
					PD_PLAN_KF_ID NUMBER(10) NOT NULL,
					PD_GUIDE_ID NUMBER(10) NOT NULL,
					PLAN_FREE_TEXT VARCHAR2(2000) NULL,
					PD_PLAN_START VARCHAR2(19) NULL,
					PD_PLAN_END VARCHAR2(19) NULL,
					UPDATE_USER NUMBER(5) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_DEVELOPE_PLAN  PRIMARY KEY (PD_PLAN_ID)) ";
				elseif($DPISDB=="mysql") 
					$cmd = " CREATE TABLE PER_DEVELOPE_PLAN (
					PD_PLAN_ID INTEGER(10) NOT NULL,
					PD_PLAN_KF_ID INTEGER(10) NOT NULL,
					PD_GUIDE_ID INTEGER(10) NOT NULL,
					PLAN_FREE_TEXT TEXT NULL,
					PD_PLAN_START VARCHAR(19) NULL,
					PD_PLAN_END VARCHAR(19) NULL,
					UPDATE_USER SMALLINT(5) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_DEVELOPE_PLAN  PRIMARY KEY (PD_PLAN_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if

			$cmd = " SELECT COUNT(PD_GUIDE_ID) AS COUNT_DATA FROM PER_DEVELOPE_GUIDE ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_DEVELOPE_GUIDE (
					PD_GUIDE_ID INTEGER NOT NULL,
					PD_GUIDE_LEVEL INTEGER2 NOT NULL,
					PD_GUIDE_COMPETENCE VARCHAR(3) NULL,
					PD_GUIDE_DESCRIPTION1 MEMO NULL,
					PD_GUIDE_DESCRIPTION2 MEMO NULL,
					UPDATE_USER INTEGER2 NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_DEVELOPE_GUIDE  PRIMARY KEY (PD_GUIDE_ID)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_DEVELOPE_GUIDE (
					PD_GUIDE_ID NUMBER(10) NOT NULL,
					PD_GUIDE_LEVEL NUMBER(3) NOT NULL,
					PD_GUIDE_COMPETENCE VARCHAR2(3) NULL,
					PD_GUIDE_DESCRIPTION1 VARCHAR2(2000) NULL,
					PD_GUIDE_DESCRIPTION2 VARCHAR2(2000) NULL,
					UPDATE_USER NUMBER(5) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_DEVELOPE_GUIDE  PRIMARY KEY (PD_GUIDE_ID)) ";
				elseif($DPISDB=="mysql") 
					$cmd = " CREATE TABLE PER_DEVELOPE_GUIDE (
					PD_GUIDE_ID INTEGER(10) NOT NULL,
					PD_GUIDE_LEVEL SMALLINT(3) NOT NULL,
					PD_GUIDE_COMPETENCE VARCHAR(3) NULL,
					PD_GUIDE_DESCRIPTION1 TEXT NULL,
					PD_GUIDE_DESCRIPTION2 TEXT NULL,
					UPDATE_USER SMALLINT(5) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_DEVELOPE_GUIDE  PRIMARY KEY (PD_GUIDE_ID)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} // end if

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ORG ADD ORG_DOPA_CODE VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORG ADD ORG_DOPA_CODE VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ORG_ASS ADD ORG_DOPA_CODE VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORG_ASS ADD ORG_DOPA_CODE VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_EMP_STATUS(
				ES_CODE VARCHAR(10) NOT NULL,	
				ES_NAME VARCHAR(100) NOT NULL,
				ES_REMARK VARCHAR(255) NULL,
				ES_ACTIVE SINGLE NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				ES_SEQ_NO INTEGER2 NULL,
				CONSTRAINT PK_PER_EMP_STATUS PRIMARY KEY (ES_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_EMP_STATUS(
				ES_CODE VARCHAR2(10) NOT NULL,	
				ES_NAME VARCHAR2(100) NOT NULL,
				ES_REMARK VARCHAR2(255) NULL,
				ES_ACTIVE NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				ES_SEQ_NO NUMBER(5) NULL,
				CONSTRAINT PK_PER_EMP_STATUS PRIMARY KEY (ES_CODE)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_EMP_STATUS(
				ES_CODE VARCHAR(10) NOT NULL,	
				ES_NAME VARCHAR(100) NOT NULL,
				ES_REMARK VARCHAR(255) NULL,
				ES_ACTIVE SMALLINT(1) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				ES_SEQ_NO SMALLINT(5) NULL,
				CONSTRAINT PK_PER_EMP_STATUS PRIMARY KEY (ES_CODE)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EMP_STATUS (ES_CODE, ES_NAME, ES_REMARK, ES_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('02', '�ç������˹�', '�дѺ���ͤس���ѵԢͧ����Ҫ��������١��ҧ�ç�Ѻ���˹觷���ͧ', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_EMP_STATUS (ES_CODE, ES_NAME, ES_REMARK, ES_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('03', '�ѡ�ҡ��㹵��˹��дѺ����ӡ���', '�дѺ�ͧ����Ҫ����٧�����дѺ����ͧ', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			$cmd = " INSERT INTO PER_EMP_STATUS (ES_CODE, ES_NAME, ES_REMARK, ES_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('04', '�ѡ�ҡ��㹵��˹觷����ҡѹ', NULL, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_EMP_STATUS (ES_CODE, ES_NAME, ES_REMARK, ES_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('05', '�����Ҫ���', '�����Ҫ��� ����黯Ժѵԧҹ㹵��˹觷���ç���˹觨�ԧ', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_EMP_STATUS (ES_CODE, ES_NAME, ES_REMARK, ES_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('06', '��Ժѵ�˹�ҷ��', NULL, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_EMP_STATUS (ES_CODE, ES_NAME, ES_REMARK, ES_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('07', '�ѡ�ҡ��㹵��˹��дѺ����٧����', NULL, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_EMP_STATUS (ES_CODE, ES_NAME, ES_REMARK, ES_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('08', '���֡�ҵ��', '����Ҫ������Է�����֡�ҵ����дѺ��ҧ � �������º', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_EMP_STATUS (ES_CODE, ES_NAME, ES_REMARK, ES_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('09', '�ѡ�Ҫ���', '������ӹҨ���������ҵ�� 107 ��� �.�.�.����º����Ҫ��þ����͹ �.�.2535', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_EMP_STATUS (ES_CODE, ES_NAME, ES_REMARK, ES_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('10', '��Шӡ��', 'ʶҹ��軯Ժѵԧҹ��������˵� ���', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_EMP_STATUS (ES_CODE, ES_NAME, ES_REMARK, ES_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('11', '��Ш��ӹѡ/�ͧ', '��Ш��ӹѡ ���� ��Шӡͧ', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_EMP_STATUS (ES_CODE, ES_NAME, ES_REMARK, ES_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('12', '��ШӨѧ��Ѵ', '��ШӨѧ��Ѵ', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_EMP_STATUS (ES_CODE, ES_NAME, ES_REMARK, ES_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('26', '�ѡ���Ҫ���᷹', NULL, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_PERSONAL SET ES_CODE = '02' WHERE ES_CODE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'master_table_emp_status.html?table=PER_EMP_STATUS' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 14, 'M0314 ʶҹС�ô�ç���˹�', 'S', 'W', 'master_table_emp_status.html?table=PER_EMP_STATUS', 0, 9, 295, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 14, 'M0314 ʶҹС�ô�ç���˹�', 'S', 'W', 'master_table_emp_status.html?table=PER_EMP_STATUS', 0, 9, 295, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD ES_CODE VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD ES_CODE VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PL_NAME_WORK VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PL_NAME_WORK VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD ORG_NAME_WORK VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD ORG_NAME_WORK VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_MESSAGE_USER ADD MSG_READ VARCHAR(19) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_MESSAGE_USER ADD MSG_READ VARCHAR2(19) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_DECORATEHIS DROP CONSTRAINT INXU1_PER_DECORATEHIS ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU1_PER_DECORATEHIS ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX INXU1_PER_DECORATEHIS ON PER_DECORATEHIS (PER_ID, DC_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ALTER KPI_WEIGHT NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS MODIFY KPI_WEIGHT NUMBER(6,2) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS MODIFY KPI_WEIGHT DECIMAL(6,2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_COMGROUP(
				COM_GROUP VARCHAR(10) NOT NULL,	
				CG_NAME VARCHAR(100) NOT NULL,
				CG_ACTIVE SINGLE NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CG_SEQ_NO INTEGER2 NULL,
				CONSTRAINT PK_PER_COMGROUP PRIMARY KEY (COM_GROUP)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_COMGROUP(
				COM_GROUP VARCHAR2(10) NOT NULL,	
				CG_NAME VARCHAR2(100) NOT NULL,
				CG_ACTIVE NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CG_SEQ_NO NUMBER(5) NULL,
				CONSTRAINT PK_PER_COMGROUP PRIMARY KEY (COM_GROUP)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_COMGROUP(
				COM_GROUP VARCHAR(10) NOT NULL,	
				CG_NAME VARCHAR(100) NOT NULL,
				CG_ACTIVE SMALLINT(1) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CG_SEQ_NO SMALLINT(5) NULL,
				CONSTRAINT PK_PER_COMGROUP PRIMARY KEY (COM_GROUP)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('01', '��è�', 1, $SESS_USERID, '$UPDATE_DATE', 91) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('02', '����', 1, $SESS_USERID, '$UPDATE_DATE', 92) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('03', '����͹�дѺ', 1, $SESS_USERID, '$UPDATE_DATE', 93) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('04', '����͹���˹�', 1, $SESS_USERID, '$UPDATE_DATE', 94) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('05', '����͹����Թ��͹', 1, $SESS_USERID, '$UPDATE_DATE', 95) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('06', '�͡�ҡ�Ҫ���', 1, $SESS_USERID, '$UPDATE_DATE', 96) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('07', '�ѡ���Ҫ���', 1, $SESS_USERID, '$UPDATE_DATE', 97) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('08', '¡��ԡ/��䢤����', 1, $SESS_USERID, '$UPDATE_DATE', 98) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('09', 'ŧ��', 1, $SESS_USERID, '$UPDATE_DATE', 99) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('501', '����觺�è�', 1, $SESS_USERID, '$UPDATE_DATE', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('502', '���������', 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('503', '������Ѻ�͹', 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('504', '���������͹', 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('505', '������觵�駢���Ҫ�������ç���˹觻�����������', 1, $SESS_USERID, '$UPDATE_DATE', 5) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('506', '�����������Ҫ������Ѻ�Թ��͹����س�ز�', 1, $SESS_USERID, '$UPDATE_DATE', 6) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('507', '���������͹����Թ��͹', 1, $SESS_USERID, '$UPDATE_DATE', 7) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('508', '�����������Ҫ������Ѻ�Թ��͹', 1, $SESS_USERID, '$UPDATE_DATE', 8) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('509', '�����������Ҫ����ѡ���Ҫ���᷹', 1, $SESS_USERID, '$UPDATE_DATE', 9) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('510', '�����������Ҫ����ѡ�ҡ��㹵��˹�', 1, $SESS_USERID, '$UPDATE_DATE', 10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('511', '���������͡�ҡ�Ҫ�����С��͹حҵ������Ҫ������͡�ҡ�Ҫ���', 1, $SESS_USERID, '$UPDATE_DATE', 11) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('512', '�����������䢤���觷��Դ��Ҵ', 1, $SESS_USERID, '$UPDATE_DATE', 12) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('513', '�����¡��ԡ��������', 1, $SESS_USERID, '$UPDATE_DATE', 13) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('514', '����觵���ҵá�û�Ѻ��ا�ѵ�ҡ��ѧ�ͧ��ǹ�Ҫ��� (�ç������³���ء�͹��˹�)', 1, $SESS_USERID, '$UPDATE_DATE', 14) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('521', '������ͺ������黯Ժѵ��Ҫ���/��Ժѵ��Ҫ���᷹', 1, $SESS_USERID, '$UPDATE_DATE', 21) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('522', '����觪����Ҫ���', 1, $SESS_USERID, '$UPDATE_DATE', 22) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMTYPE ALTER COM_GROUP CHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMTYPE MODIFY COM_GROUP CHAR(10) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMTYPE MODIFY COM_GROUP CHAR(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_TRAINING SET TRN_PASS = 1 WHERE TRN_PASS IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_COMTYPE DROP CONSTRAINT INXU1_PER_COMTYPE ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU1_PER_COMTYPE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX INXU1_PER_COMTYPE ON PER_COMTYPE (COM_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_COMTYPE DROP CONSTRAINT INXU2_PER_COMTYPE ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU2_PER_COMTYPE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX INXU2_PER_COMTYPE ON PER_COMTYPE (COM_DESC) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET COM_SEQ_NO = 102 WHERE COM_TYPE = '0101' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5011', '��. 1.1', '����觺�èؼ���ͺ�觢ѹ��', '501', 2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5012', '��. 1.2', '����觺�èؼ�����Ѻ�Ѵ���͡', '501', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5013', '��. 1.3', '����觺�èص��˹觻������Ԫҡ�� �дѺ�ӹҭ��� �ӹҭ��þ���� ����Ǫҭ ��зç�س�ز� ���͵��˹觻���������� �дѺ�ѡ�о����', '501', 6) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5014', '��. 1.4', '������Ѻ�͹��ѡ�ҹ��ǹ��ͧ��� ������˹�ҷ��ͧ˹��§ҹ��蹢ͧ�Ѱ', '501', 8) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5015', '��. 1.5', '������Ѻ�͹��ѡ�ҹ��ǹ��ͧ��� ������˹�ҷ��ͧ˹��§ҹ��蹢ͧ�Ѱ����ͺ�觢ѹ��', '501', 10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5016', '��. 1.6', '����觺�èؼ���͡��Ѻ�Ҫ��÷��á�Ѻ����Ѻ�Ҫ���', '501', 12) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5017', '��. 1.7', '����觺�èؼ��任�Ժѵԧҹ�����Ԥ���Ѱ����ա�Ѻ����Ѻ�Ҫ���', '501', 14) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5018', '��. 1.8', '����觺�èؼ�����繢���Ҫ��þ����͹���ѭ��Ѻ����Ѻ�Ҫ���', '501', 16) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5019', '��. 1.9', '����觺�èؼ�����繾�ѡ�ҹ��ǹ��ͧ��� ���͢���Ҫ��û�������蹡�Ѻ����Ѻ�Ҫ���', '501', 18) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5021', '��. 2.1', '���������', '502', 20) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5022', '��. 2.2', '��������¢���Ҫ��þ����͹���ѭ������Ѻ�ز��������', '502', 22) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5023', '��. 2.3', '������觵�駼���դس���ѵ����ç����س���ѵ�੾������Ѻ���˹�����Ѻ仴�ç���˹�㹻�������� �дѺ���', '502', 24) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5031', '��. 3.1', '������Ѻ�͹����Ҫ��þ����͹���ѭ', '503', 26) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5032', '��. 3.2', '������Ѻ�͹����Ҫ��þ����͹���ѭ������Ѻ�ز��������', '503', 28) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5033', '��. 3.3', '������Ѻ�͹����Ҫ��þ����͹���ѭ�Ҵ�ç���˹���дѺ����٧���', '503', 30) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5034', '��. 3.4', '������Ѻ�͹����Ҫ��þ����͹���ѭ����ͺ�觢ѹ��', '503', 32) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5035', '��. 3.5', '���������͹', '511', 34) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5041', '��. 4.1', '���������͹����Ҫ���', '504', 36) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5051', '��. 5.1', '���������仴�ç���˹觻�����������', '505', 38) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5052', '��. 5.2', '������Ѻ�͹�Ҵ�ç���˹觻�����������', '505', 40) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5053', '��. 5.3', '����觺�èؼ�����繢���Ҫ��þ����͹���ѭ��Ѻ����Ѻ�Ҫ�������ç���˹觻�����������', '505', 42) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5060', '��. 6', '�����������Ҫ������Ѻ�Թ��͹����س�ز� (��Ѻ�ز�㹵��˹����)', '506', 44) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5071', '��. 7.1', '���������͹�Թ��͹����Ҫ���', '507', 46) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5072', '��. 7.2', '���������͹�Թ��͹ (�óդú���³����)', '507', 48) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5073', '��. 7.3', '�����������Ѻ�Թ��ҵͺ᷹�����', '507', 49) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5081', '��. 8.1', '�����������Ҫ������Ѻ�Թ��͹', '508', 50) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5090', '��. 9', '�����������Ҫ����ѡ���Ҫ���᷹', '509', 52) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5100', '��. 10', '�����������Ҫ����ѡ�ҡ��㹵��˹�', '510', 54) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5111', '��. 11.1', '�����͹حҵ������Ҫ������͡�ҡ�Ҫ���', '511', 56) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5112', '��. 11.2', '�����������Ҫ����͡�ҡ�Ҫ������мš�÷��ͧ��Ժѵ�˹�ҷ���Ҫ��õ�ӡ���ࡳ�������ҵðҹ����˹�', '511', 58) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5113', '��. 11.3', '�����������Ҫ����͡�ҡ�Ҫ������ТҴ�س���ѵԷ�����������ѡɳе�ͧ�������͢Ҵ�س���ѵ�੾������Ѻ���˹�', '511', 60) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5114', '��. 11.4', '���������͡�ҡ�Ҫ�������������Ѻ���˹稺ӹҭ�˵ط�᷹', '511', 62) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5115', '��. 11.5', '���������͡�ҡ�Ҫ���������Ѻ�Ҫ��÷���', '511', 64) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5116', '��. 11.6', '�����������Ҫ���任�Ժѵԧҹ�����Ԥ���Ѱ�����', '511', 66) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5117', '��. 11.7', '�����������Ҫ����͡�ҡ�Ҫ�������任�Ժѵԧҹ�����Ԥ���Ѱ�����', '511', 68) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5120', '��. 12', '�����������䢤���觷��Դ��Ҵ', '512', 70) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5130', '��. 13', '�����¡��ԡ��������', '513', 72) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5141', '��. 14.1', '�����͹حҵ������Ҫ������͡�ҡ�Ҫ��õ���ҵá�û�Ѻ��ا�ѵ�ҡ��ѧ�ͧ��ǹ�Ҫ��� (�ç������³���ء�͹��˹�) �է�����ҳ �.�.2553', '514', 74) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5142', '��. 14.2', '���������͹�Թ��͹����Ҫ��ü����������ҵá�û�Ѻ��ا�ѵ�ҡ��ѧ�ͧ��ǹ�Ҫ��� (�ç������³���ء�͹��˹�) �է�����ҳ �.�.2553', '514', 76) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5211', '��. 21.1', '������ͺ������黯Ժѵ��Ҫ���', '521', 92) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5212', '��. 21.2', '����觻�Ժѵ��Ҫ���᷹', '521', 94) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5220', '��. 22', '����觪����Ҫ���', '522', 96) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'master_table.html?table=PER_COMGROUP' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 15, 'M0315 Ẻ�����', 'S', 'W', 'master_table.html?table=PER_COMGROUP', 0, 9, 295, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 15, 'M0315 Ẻ�����', 'S', 'W', 'master_table.html?table=PER_COMGROUP', 0, 9, 295, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER CP_BUDGET NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY CP_BUDGET NUMBER(16,2) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY CP_BUDGET DECIMAL(16,2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST_DTL ADD CD_MIDPOINT NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST_DTL ADD CD_MIDPOINT NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST_DTL ADD CD_MIDPOINT DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_positionhis' 
							  WHERE LINKTO_WEB = 'personal_positionhis.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_positionhis%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_salaryhis' 
							  WHERE LINKTO_WEB = 'personal_salaryhis.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_salaryhis%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_extrahis' 
							  WHERE LINKTO_WEB = 'personal_extrahis.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_extrahis%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_educate' 
							  WHERE LINKTO_WEB = 'personal_educate.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_educate%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_training' 
							  WHERE LINKTO_WEB = 'personal_training.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_training%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_ability' 
							  WHERE LINKTO_WEB = 'personal_ability.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_ability%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_special_skill' 
							  WHERE LINKTO_WEB = 'personal_special_skill.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_special_skill%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_heir' 
							  WHERE LINKTO_WEB = 'personal_heir.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_heir%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_absenthis' 
							  WHERE LINKTO_WEB = 'personal_absenthis.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_absenthis%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_punishment' 
							  WHERE LINKTO_WEB = 'personal_punishment.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_punishment%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_servicehis' 
							  WHERE LINKTO_WEB = 'personal_servicehis.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_servicehis%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_rewardhis' 
							  WHERE LINKTO_WEB = 'personal_rewardhis.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_rewardhis%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_marrhis' 
							  WHERE LINKTO_WEB = 'personal_marrhis.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_marrhis%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_namehis' 
							  WHERE LINKTO_WEB = 'personal_namehis.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_namehis%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_decoratehis' 
							  WHERE LINKTO_WEB = 'personal_decoratehis.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_decoratehis%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_timehis' 
							  WHERE LINKTO_WEB = 'personal_timehis.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_timehis%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_extra_incomehis' 
							  WHERE LINKTO_WEB = 'personal_extra_incomehis.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_extra_incomehis%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_work_cyclehis' 
							  WHERE LINKTO_WEB = 'personal_work_cyclehis.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_work_cyclehis%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'personal_master.html?SEARCHHIS=personal_work_time' 
							  WHERE LINKTO_WEB = 'personal_work_time.html' or LINKTO_WEB like 'personal_master.html?SEARCHHIS=personal_work_time%' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_COURSE DROP CONSTRAINT INXU1_PER_COURSE ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU1_PER_COURSE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX INXU1_PER_COURSE ON PER_COURSE (TR_CODE, CO_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI ADD KPI_RESULT MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI ADD KPI_RESULT VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI ADD KPI_RESULT TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ADD PG_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ADD PG_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERFORMANCE_GOALS ADD PG_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_TRAINING SET TRN_PASS =  1 WHERE TRN_PASS IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMDTL ADD CMD_MIDPOINT NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL ADD CMD_MIDPOINT NUMBER(16,2) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMDTL ADD CMD_MIDPOINT DECIMAL(16,2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('113', '�ͺ������黯Ժѵ��Ҫ���/��Ժѵ��Ҫ���᷹', 1, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('11310', '�ͺ������黯Ժѵ��Ҫ���', 1, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('11320', '��Ժѵ��Ҫ���᷹', 1, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_MARRHIS DROP CONSTRAINT INXU1_PER_MARRHIS ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU1_PER_MARRHIS ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX INXU1_PER_MARRHIS ON PER_MARRHIS (PER_ID, MAH_SEQ) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_ACTINGHIS(
				ACTH_ID INTEGER NOT NULL,	
				PER_ID INTEGER NOT NULL,	
				PER_CARDNO VARCHAR(13) NULL,
				ACTH_EFFECTIVEDATE VARCHAR(19) NOT NULL,
				MOV_CODE VARCHAR(10) NOT NULL,
				ACTH_ENDDATE VARCHAR(19) NULL,
				ACTH_DOCNO VARCHAR(255) NULL,
				ACTH_DOCDATE VARCHAR(19) NULL,
				ACTH_POS_NO VARCHAR(15) NULL,
				PM_CODE VARCHAR(10) NULL,
				ACTH_PM_NAME VARCHAR(255) NULL,
				LEVEL_NO VARCHAR(10) NULL,
				PL_CODE VARCHAR(10) NULL,
				ACTH_PL_NAME VARCHAR(255) NULL,
				ACTH_ORG1 VARCHAR(255) NULL,
				ACTH_ORG2 VARCHAR(255) NULL,
				ACTH_ORG3 VARCHAR(255) NULL,
				ACTH_ORG4 VARCHAR(255) NULL,
				ACTH_ORG5 VARCHAR(255) NULL,
				ACTH_POS_NO_ASSIGN VARCHAR(15) NULL,
				PM_CODE_ASSIGN VARCHAR(10) NULL,
				ACTH_PM_NAME_ASSIGN VARCHAR(255) NULL,
				LEVEL_NO_ASSIGN VARCHAR(10) NULL,
				PL_CODE_ASSIGN VARCHAR(10) NULL,
				ACTH_PL_NAME_ASSIGN VARCHAR(255) NULL,
				ACTH_ORG1_ASSIGN VARCHAR(255) NULL,
				ACTH_ORG2_ASSIGN VARCHAR(255) NULL,
				ACTH_ORG3_ASSIGN VARCHAR(255) NULL,
				ACTH_ORG4_ASSIGN VARCHAR(255) NULL,
				ACTH_ORG5_ASSIGN VARCHAR(255) NULL,
				ACTH_ASSIGN MEMO NULL,
				ACTH_REMARK MEMO NULL,
				ACTH_SEQ_NO INTEGER2 NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_ACTINGHIS PRIMARY KEY (ACTH_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_ACTINGHIS(
				ACTH_ID NUMBER(10) NOT NULL,	
				PER_ID NUMBER(10) NOT NULL,	
				PER_CARDNO VARCHAR2(13) NULL,
				ACTH_EFFECTIVEDATE VARCHAR2(19) NOT NULL,
				MOV_CODE VARCHAR2(10) NOT NULL,
				ACTH_ENDDATE VARCHAR2(19) NULL,
				ACTH_DOCNO VARCHAR2(255) NULL,
				ACTH_DOCDATE VARCHAR2(19) NULL,
				ACTH_POS_NO VARCHAR2(15) NULL,
				PM_CODE VARCHAR2(10) NULL,
				ACTH_PM_NAME VARCHAR2(255) NULL,
				LEVEL_NO VARCHAR2(10) NULL,
				PL_CODE VARCHAR2(10) NULL,
				ACTH_PL_NAME VARCHAR2(255) NULL,
				ACTH_ORG1 VARCHAR2(255) NULL,
				ACTH_ORG2 VARCHAR2(255) NULL,
				ACTH_ORG3 VARCHAR2(255) NULL,
				ACTH_ORG4 VARCHAR2(255) NULL,
				ACTH_ORG5 VARCHAR2(255) NULL,
				ACTH_POS_NO_ASSIGN VARCHAR2(15) NULL,
				PM_CODE_ASSIGN VARCHAR2(10) NULL,
				ACTH_PM_NAME_ASSIGN VARCHAR2(255) NULL,
				LEVEL_NO_ASSIGN VARCHAR2(10) NULL,
				PL_CODE_ASSIGN VARCHAR2(10) NULL,
				ACTH_PL_NAME_ASSIGN VARCHAR2(255) NULL,
				ACTH_ORG1_ASSIGN VARCHAR2(255) NULL,
				ACTH_ORG2_ASSIGN VARCHAR2(255) NULL,
				ACTH_ORG3_ASSIGN VARCHAR2(255) NULL,
				ACTH_ORG4_ASSIGN VARCHAR2(255) NULL,
				ACTH_ORG5_ASSIGN VARCHAR2(255) NULL,
				ACTH_ASSIGN VARCHAR2(1000) NULL,
				ACTH_REMARK VARCHAR2(1000) NULL,
				ACTH_SEQ_NO NUMBER(5) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_ACTINGHIS PRIMARY KEY (ACTH_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_ACTINGHIS(
				ACTH_ID INTEGER(10) NOT NULL,	
				PER_ID INTEGER(10) NOT NULL,	
				PER_CARDNO VARCHAR(13) NULL,
				ACTH_EFFECTIVEDATE VARCHAR(19) NOT NULL,
				MOV_CODE VARCHAR(10) NOT NULL,
				ACTH_ENDDATE VARCHAR(19) NULL,
				ACTH_DOCNO VARCHAR(255) NULL,
				ACTH_DOCDATE VARCHAR(19) NULL,
				ACTH_POS_NO VARCHAR(15) NULL,
				PM_CODE VARCHAR(10) NULL,
				ACTH_PM_NAME VARCHAR(255) NULL,
				LEVEL_NO VARCHAR(10) NULL,
				PL_CODE VARCHAR(10) NULL,
				ACTH_PL_NAME VARCHAR(255) NULL,
				ACTH_ORG1 VARCHAR(255) NULL,
				ACTH_ORG2 VARCHAR(255) NULL,
				ACTH_ORG3 VARCHAR(255) NULL,
				ACTH_ORG4 VARCHAR(255) NULL,
				ACTH_ORG5 VARCHAR(255) NULL,
				ACTH_POS_NO_ASSIGN VARCHAR(15) NULL,
				PM_CODE_ASSIGN VARCHAR(10) NULL,
				ACTH_PM_NAME_ASSIGN VARCHAR(255) NULL,
				LEVEL_NO_ASSIGN VARCHAR(10) NULL,
				PL_CODE_ASSIGN VARCHAR(10) NULL,
				ACTH_PL_NAME_ASSIGN VARCHAR(255) NULL,
				ACTH_ORG1_ASSIGN VARCHAR(255) NULL,
				ACTH_ORG2_ASSIGN VARCHAR(255) NULL,
				ACTH_ORG3_ASSIGN VARCHAR(255) NULL,
				ACTH_ORG4_ASSIGN VARCHAR(255) NULL,
				ACTH_ORG5_ASSIGN VARCHAR(255) NULL,
				ACTH_ASSIGN TEXT NULL,
				ACTH_REMARK TEXT NULL,
				ACTH_SEQ_NO SMALLINT(5) NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_ACTINGHIS PRIMARY KEY (ACTH_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PUNISHMENT ALTER PUN_ENDDATE NULL ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PUNISHMENT MODIFY PUN_ENDDATE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_PUNISHMENT ALTER PUN_ENDDATE NULL ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_PUNISHMENT MODIFY PUN_ENDDATE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER SF_CODE_O MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_O VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_O TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER SF_CODE_K MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_K VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_K TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER SF_CODE_D MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_D VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_D TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER SF_CODE_M MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_M VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_M TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER SUM_QTY MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SUM_QTY VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SUM_QTY TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER SUM_SALARY MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SUM_SALARY VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SUM_SALARY TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('21415', '�Թ��͹������ ���Թ��ҵͺ᷹�����', 2, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_LABEL = 'P0310 �ѭ��Ṻ���¤��������͹' 
							  WHERE LINKTO_WEB = 'data_promote_e_p_comdtl.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET TYPE_LINKTO = 'N', LINKTO_WEB = NULL 
							  WHERE MENU_ID = 404 AND LINKTO_WEB = 'data_cancel_comdtl.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();
	
			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'data_cancel_comdtl.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 1, 'P1701 �ѭ��Ṻ���¤����¡��ԡ��������', 'S', 'W', 'data_cancel_comdtl.html', 0, 35, 404, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 1, 'P1701 �ѭ��Ṻ���¤����¡��ԡ��������', 'S', 'W', 'data_cancel_comdtl.html', 0, 35, 404, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'data_edit_comdtl.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 2, 'P1702 �ѭ��Ṻ���¤������䢤���觷��Դ��Ҵ', 'S', 'W', 'data_edit_comdtl.html', 0, 35,
								  404, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
	
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 2, 'P1702 �ѭ��Ṻ���¤������䢤���觷��Դ��Ҵ', 'S', 'W', 'data_edit_comdtl.html', 0, 35,
								  404, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R006000.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 0, 'R0600 ��§ҹ�������͹�Թ��͹��ºؤ��', 'S', 'W', 'rpt_R006000.html', 0, 36, 238, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 0, 'R0600 ��§ҹ�������͹�Թ��͹��ºؤ��', 'S', 'W', 'rpt_R006000.html', 0, 36, 238, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_SALARY_MIDPOINT NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_SALARY_MIDPOINT NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_SALARY_MIDPOINT DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITION ADD POS_ORGMGT VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITION ADD POS_ORGMGT VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_DOCNO VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_DOCNO VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_DOCDATE VARCHAR(19) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_DOCDATE VARCHAR2(19) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_COMMAND ADD COM_STATUS CHAR(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_EDUCATE_1 ON PER_EDUCATE (PER_ID, EDU_TYPE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_ORG_1 ON PER_ORG (ORG_ID, ORG_ID_REF) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_ORG_ASS_1 ON PER_ORG_ASS (DEPARTMENT_ID, OL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_1 ON PER_PERSONAL (POS_ID, PER_STATUS) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_2 ON PER_PERSONAL (POS_ID, ORG_ID, PER_STATUS) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_3 ON PER_PERSONAL (POS_ID, PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_4 ON PER_PERSONAL (POEM_ID, PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_5 ON PER_PERSONAL (POS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_6 ON PER_PERSONAL (POEM_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_7 ON PER_PERSONAL (POS_ID, PN_CODE, PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_8 ON PER_PERSONAL (POEM_ID, PN_CODE, PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POS_EMP_1 ON PER_POS_EMP (POEM_ID, ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POS_EMPSER_1 ON PER_POS_EMPSER (POEMS_ID, ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITION_1 ON PER_POSITION (ORG_ID, CL_NAME, POS_ID, DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITION_2 ON PER_POSITION (ORG_ID, CL_NAME, POS_GET_DATE, DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITION_3 ON PER_POSITION (POS_ID, POS_STATUS, DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITION_4 ON PER_POSITION (ORG_ID, CL_NAME, POS_ID, POS_GET_DATE, DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITION_5 ON PER_POSITION (ORG_ID, CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITION_6 ON PER_POSITION (POS_ID, ORG_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITIONHIS_1 ON PER_POSITIONHIS (PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITIONHIS_2 ON PER_POSITIONHIS (PER_ID, PL_CODE, PT_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITIONHIS_3 ON PER_POSITIONHIS (PER_ID, PN_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITIONHIS_4 ON PER_POSITIONHIS (PER_ID, EP_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITIONHIS_5 ON PER_POSITIONHIS (PER_ID, POH_EFFECTIVEDATE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_SALARYHIS_1 ON PER_SALARYHIS (PER_ID, SAH_EFFECTIVEDATE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COURSE ALTER CO_NO VARCHAR(15) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COURSE MODIFY CO_NO VARCHAR2(15) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COURSE MODIFY CO_NO VARCHAR(15) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COURSE ADD CO_PROJECT_NAME VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COURSE ADD CO_PROJECT_NAME VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE user_detail ADD titlename varchar(50) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_LEVEL ADD POSITION_TYPE VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LEVEL ADD POSITION_TYPE VARCHAR2(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_LEVEL ADD POSITION_LEVEL VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LEVEL ADD POSITION_LEVEL VARCHAR2(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = '1' WHERE LEVEL_NO = '01' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = '2' WHERE LEVEL_NO = '02' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = '3' WHERE LEVEL_NO = '03' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = '4' WHERE LEVEL_NO = '04' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = '5' WHERE LEVEL_NO = '05' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = '6' WHERE LEVEL_NO = '06' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = '7' WHERE LEVEL_NO = '07' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = '8' WHERE LEVEL_NO = '08' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = '9' WHERE LEVEL_NO = '09' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = '10' WHERE LEVEL_NO = '10' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = '11' WHERE LEVEL_NO = '11' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_TYPE = '�����', POSITION_LEVEL = '��Ժѵԧҹ' WHERE LEVEL_NO = 'O1' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_TYPE = '�����', POSITION_LEVEL = '�ӹҭ�ҹ' WHERE LEVEL_NO = 'O2' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET POSITION_TYPE = '�����', POSITION_LEVEL = '������' WHERE LEVEL_NO = 'O3' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET POSITION_TYPE = '�����', POSITION_LEVEL = '�ѡ�о����' WHERE LEVEL_NO = 'O4' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET POSITION_TYPE = '�Ԫҡ��', POSITION_LEVEL = '��Ժѵԡ��' WHERE LEVEL_NO = 'K1' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET POSITION_TYPE = '�Ԫҡ��', POSITION_LEVEL = '�ӹҭ���' WHERE LEVEL_NO = 'K2' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET POSITION_TYPE = '�Ԫҡ��', POSITION_LEVEL = '�ӹҭ��þ����' WHERE LEVEL_NO = 'K3' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET POSITION_TYPE = '�Ԫҡ��', POSITION_LEVEL = '����Ǫҭ' WHERE LEVEL_NO = 'K4' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET POSITION_TYPE = '�Ԫҡ��', POSITION_LEVEL = '�ç�س�ز�' WHERE LEVEL_NO = 'K5' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET POSITION_TYPE = '�ӹ�¡��', POSITION_LEVEL = '�дѺ��' WHERE LEVEL_NO = 'D1' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET POSITION_TYPE = '�ӹ�¡��', POSITION_LEVEL = '�дѺ�٧' WHERE LEVEL_NO = 'D2' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET POSITION_TYPE = '������', POSITION_LEVEL = '�дѺ��' WHERE LEVEL_NO = 'M1' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET POSITION_TYPE = '������', POSITION_LEVEL = '�дѺ�٧' WHERE LEVEL_NO = 'M2' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMDTL ADD PL_NAME_WORK VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL ADD PL_NAME_WORK VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMDTL ADD ORG_NAME_WORK VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL ADD ORG_NAME_WORK VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_KPI_FORM ON PER_KPI_FORM (KF_CYCLE, KF_START_DATE, KF_END_DATE, DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_KPI_FORM_1 ON PER_KPI_FORM (PER_ID, KF_CYCLE, KF_START_DATE, KF_END_DATE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SQL ALTER SQL_CMD MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SQL MODIFY SQL_CMD VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SQL MODIFY SQL_CMD TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_KF_YEAR VARCHAR(4) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_KF_YEAR VARCHAR2(4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_KF_CYCLE SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_KF_CYCLE NUMBER(1) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_KF_CYCLE SMALLINT(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_TOTAL_SCORE NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_TOTAL_SCORE NUMBER(5,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_TOTAL_SCORE DECIMAL(5,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'structure_by_law_per.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 4, 'S0104 �ç���ҧ��úѧ�Ѻ�ѭ�ҵ��������', 'S', 'W', 'structure_by_law_per.html', 0, 34, 253, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 4, 'S0104 �ç���ҧ��úѧ�Ѻ�ѭ�ҵ��������', 'S', 'W', 'structure_by_law_per.html', 0, 34, 253, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'structure_by_assign_per.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 5, 'S0105 �ç���ҧ��úѧ�Ѻ�ѭ�ҵ���ͺ���§ҹ', 'S', 'W', 'structure_by_assign_per.html', 0, 34, 253, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 5, 'S0105 �ç���ҧ��úѧ�Ѻ�ѭ�ҵ���ͺ���§ҹ', 'S', 'W', 'structure_by_assign_per.html', 0, 34, 253, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " UPDATE PER_SALARYHIS SET SAH_KF_CYCLE = 1 WHERE SUBSTR(SAH_EFFECTIVEDATE,6,5) = '04-01' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_SALARYHIS SET SAH_KF_CYCLE = 2 WHERE SUBSTR(SAH_EFFECTIVEDATE,6,5) = '10-01' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_SALARYHIS SET SAH_KF_YEAR = TO_NUMBER(SUBSTR(SAH_EFFECTIVEDATE,1,4)) + 543
							 WHERE SUBSTR(SAH_EFFECTIVEDATE,6,5) < '10-01' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_SALARYHIS SET SAH_KF_YEAR = TO_NUMBER(SUBSTR(SAH_EFFECTIVEDATE,1,4)) + 544
							 WHERE SUBSTR(SAH_EFFECTIVEDATE,6,5) >= '10-01' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PROJECT_GROUP ADD PG_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PROJECT_GROUP ADD PG_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PROJECT_GROUP ADD PG_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PROJECT_PAYMENT ADD PP_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PROJECT_PAYMENT ADD PP_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PROJECT_PAYMENT ADD PP_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POSITION SET PAY_NO = POS_NO WHERE PAY_NO IS NULL OR PAY_NO = 'NULL' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMDTL ALTER CMD_PERCENT NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL MODIFY CMD_PERCENT NUMBER(7,4) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMDTL MODIFY CMD_PERCENT DECIMAL(7,4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ASSESS_LEVEL ALTER AL_PERCENT NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ASSESS_LEVEL MODIFY AL_PERCENT NUMBER(7,4) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ASSESS_LEVEL MODIFY AL_PERCENT DECIMAL(7,4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST_DTL ALTER CD_PERCENT NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST_DTL MODIFY CD_PERCENT NUMBER(7,4) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST_DTL MODIFY CD_PERCENT DECIMAL(7,4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SALARYHIS ALTER SAH_PERCENT_UP NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS MODIFY SAH_PERCENT_UP NUMBER(7,4) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SALARYHIS MODIFY SAH_PERCENT_UP DECIMAL(7,4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST_DTL ADD CD_OLD_SALARY NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST_DTL ADD CD_OLD_SALARY NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST_DTL ADD CD_OLD_SALARY DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'dpis_to_text.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV1 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
								  UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 12, 'C12 �����͹�����Ũѧ��Ѵ', 'S', 'W', 'dpis_to_text.html', 0, 1, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
								  UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 12, 'C12 �����͹�����Ũѧ��Ѵ', 'S', 'W', 'dpis_to_text.html', 0, 1, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'text_to_ppis.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV1 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
								  UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 13, 'C13 �Ѻ�͹�����Ũѧ��Ѵ', 'S', 'W', 'text_to_ppis.html', 0, 1, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
								  UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 13, 'C13 �Ѻ�͹�����Ũѧ��Ѵ', 'S', 'W', 'text_to_ppis.html', 0, 1, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_PRENAME DROP CONSTRAINT INXU2_PER_PRENAME ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU2_PER_PRENAME ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ABILITY ADD ABI_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ABILITY ADD ABI_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ABILITY ADD ABI_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_ABILITY ADD ABI_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_ABILITY ADD ABI_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_WORKFLOW_ABILITY ADD ABI_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SPECIAL_SKILL ADD SPS_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SPECIAL_SKILL ADD SPS_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SPECIAL_SKILL ADD SPS_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SPECIAL_SKILL ADD SPS_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SPECIAL_SKILL ADD SPS_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_WORKFLOW_SPECIAL_SKILL ADD SPS_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_HEIR ADD HEIR_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_HEIR ADD HEIR_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_HEIR ADD HEIR_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_HEIR ADD HEIR_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_HEIR ADD HEIR_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_WORKFLOW_HEIR ADD HEIR_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ABSENTHIS ADD ABS_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ABSENTHIS ADD ABS_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ABSENTHIS ADD ABS_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_ABSENTHIS ADD ABS_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_ABSENTHIS ADD ABS_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_WORKFLOW_ABSENTHIS ADD ABS_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PUNISHMENT ADD PUN_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PUNISHMENT ADD PUN_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PUNISHMENT ADD PUN_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_PUNISHMENT ADD PUN_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_PUNISHMENT ADD PUN_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_WORKFLOW_PUNISHMENT ADD PUN_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMDTL ADD CMD_EDUCATE VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL ADD CMD_EDUCATE VARCHAR2(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITION ADD LEVEL_NO VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITION ADD LEVEL_NO VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_POSITION, PER_PERSONAL SET PER_POSITION.LEVEL_NO = 
								  PER_PERSONAL.LEVEL_NO WHERE PER_PERSONAL.POS_ID = PER_POSITION.POS_ID AND 
								  PER_POSITION.LEVEL_NO IS NULL AND PER_PERSONAL.PER_STATUS = 1 ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_POSITION A SET A.LEVEL_NO = 
								  (SELECT MAX(B.LEVEL_NO) FROM PER_PERSONAL B WHERE A.POS_ID = B.POS_ID AND B.PER_STATUS = 1) 
								  WHERE A.LEVEL_NO IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_EFFECTIVEDATE VARCHAR(19) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_EFFECTIVEDATE VARCHAR2(19) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_POS_REASON VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_POS_REASON VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_POS_YEAR VARCHAR(4) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_POS_YEAR VARCHAR2(4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_POS_DOCTYPE VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_POS_DOCTYPE VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_POS_DOCNO VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_POS_DOCNO VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_POS_ORG VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_POS_ORG VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_ORDAIN_DETAIL VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_ORDAIN_DETAIL VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_LAST_POSITION CHAR(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_LAST_SALARY CHAR(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_SALARY_MOVMENT(
				SM_CODE VARCHAR(10) NOT NULL,	
				SM_NAME VARCHAR(100) NOT NULL,
				SM_REMARK VARCHAR(255) NULL,
				SM_FACTOR NUMBER NULL,
				SM_ACTIVE SINGLE NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				SM_SEQ_NO INTEGER2 NULL,
				CONSTRAINT PK_PER_SALARY_MOVMENT PRIMARY KEY (SM_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_SALARY_MOVMENT(
				SM_CODE VARCHAR2(10) NOT NULL,	
				SM_NAME VARCHAR2(100) NOT NULL,
				SM_REMARK VARCHAR2(255) NULL,
				SM_FACTOR NUMBER(5,2) NULL,
				SM_ACTIVE NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				SM_SEQ_NO NUMBER(5) NULL,
				CONSTRAINT PK_PER_SALARY_MOVMENT PRIMARY KEY (SM_CODE)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_SALARY_MOVMENT(
				SM_CODE VARCHAR(10) NOT NULL,	
				SM_NAME VARCHAR(100) NOT NULL,
				SM_REMARK VARCHAR(255) NULL,
				SM_FACTOR DECIMAL(5,2) NULL,
				SM_ACTIVE SMALLINT(1) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				SM_SEQ_NO SMALLINT(5) NULL,
				CONSTRAINT PK_PER_SALARY_MOVMENT PRIMARY KEY (SM_CODE)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SM_CODE VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SM_CODE VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_MOVMENT ADD MOV_SUB_TYPE SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_MOVMENT ADD MOV_SUB_TYPE NUMBER(1) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_MOVMENT ADD MOV_SUB_TYPE SMALLINT(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 1 WHERE MOV_TYPE = 1 AND 
							(SUBSTR(MOV_CODE,1,3) = '101' OR SUBSTR(MOV_CODE,1,3) = '105' OR SUBSTR(MOV_CODE,1,3) = '108') AND MOV_SUB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 2 WHERE MOV_TYPE = 1 AND 
							SUBSTR(MOV_CODE,1,3) = '103' AND MOV_SUB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 3 WHERE MOV_TYPE = 1 AND 
							(SUBSTR(MOV_CODE,1,3) = '104' OR SUBSTR(MOV_CODE,1,3) = '124') AND MOV_SUB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 4 WHERE MOV_TYPE = 2 AND 
							SUBSTR(MOV_CODE,1,1) = '2' AND MOV_SUB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 5 WHERE MOV_TYPE = 1 AND 
							(SUBSTR(MOV_CODE,1,3) = '109' OR SUBSTR(MOV_CODE,1,3) = '110' OR SUBSTR(MOV_CODE,1,3) = '111' OR 
							SUBSTR(MOV_CODE,1,3) = '113') AND MOV_SUB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 6 WHERE MOV_TYPE = 1 AND SUBSTR(MOV_CODE,1,3) = '101' AND MOV_SUB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 7 WHERE MOV_TYPE = 1 AND SUBSTR(MOV_CODE,1,3) = '101' AND MOV_SUB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 8 WHERE MOV_TYPE = 1 AND SUBSTR(MOV_CODE,1,3) = '101' AND MOV_SUB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_MOVMENT SET MOV_SUB_TYPE = 9 WHERE MOV_TYPE = 1 AND 
							(SUBSTR(MOV_CODE,1,3) = '106' OR SUBSTR(MOV_CODE,1,3) = '118' OR SUBSTR(MOV_CODE,1,3) = '119' OR 
							SUBSTR(MOV_CODE,1,3) = '120' OR SUBSTR(MOV_CODE,1,3) = '121' OR SUBSTR(MOV_CODE,1,3) = '122' OR 
							SUBSTR(MOV_CODE,1,3) = '123') AND MOV_SUB_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_SEQ_NO INTEGER2 ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_SEQ_NO NUMBER(5) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_SEQ_NO SMALLINT(5) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SALARYHIS ALTER SAH_SEQ_NO INTEGER2 ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS MODIFY SAH_SEQ_NO NUMBER(5) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SALARYHIS ALTER SAH_SEQ_NO SMALLINT(5) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_MOVE ADD DEPARTMENT_ID INTEGER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_MOVE ADD DEPARTMENT_ID NUMBER(10) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_MOVE ADD DEPARTMENT_ID INTEGER(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_MOVE ADD POS_SEQ_NO INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_MOVE ADD POS_SEQ_NO NUMBER(5) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_MOVE ADD POS_SEQ_NO SMALLINT(5) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_MOVE ADD PAY_NO VARCHAR(15) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_MOVE ADD PAY_NO VARCHAR2(15) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_MOVE ADD POS_ORGMGT VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_MOVE ADD POS_ORGMGT VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_MOVE ADD LEVEL_NO VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_MOVE ADD LEVEL_NO VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EXTRATYPE ADD EX_AMT NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EXTRATYPE ADD EX_AMT NUMBER(16,2) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_EXTRATYPE ADD EX_AMT DECIMAL(16,2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EXTRAHIS ADD EXH_ACTIVE SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EXTRAHIS ADD EXH_ACTIVE NUMBER(1) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_EXTRAHIS ADD EXH_ACTIVE SMALLINT(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_extratype.html' 
							  WHERE LINKTO_WEB = 'master_table.html?table=PER_EXTRATYPE' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_extra_income_type.html' 
							  WHERE LINKTO_WEB = 'master_table.html?table=PER_EXTRA_INCOME_TYPE' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMDTL ADD CMD_LEVEL_POS VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL ADD CMD_LEVEL_POS VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '�1' WHERE LEVEL_NO = '�1' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '�2' WHERE LEVEL_NO = '�2' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '�1' WHERE LEVEL_NO = '�1' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '�2' WHERE LEVEL_NO = '�2' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '�3' WHERE LEVEL_NO = '�4' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '�4' WHERE LEVEL_NO = '�6' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '�1' WHERE LEVEL_NO = '�1' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '�2' WHERE LEVEL_NO = '�2' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '�3' WHERE LEVEL_NO = '�4' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '�4' WHERE LEVEL_NO = '�6' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '�1' WHERE LEVEL_NO = '�1' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '�2' WHERE LEVEL_NO = '�2' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '�3' WHERE LEVEL_NO = '�4' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_REMARK NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_REMARK NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_REMARK NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SCHOLAR ALTER SC_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLAR MODIFY SC_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SCHOLAR MODIFY SC_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SCHOLAR ADD SC_GRADE NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLAR ADD SC_GRADE NUMBER(6,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SCHOLAR ADD SC_GRADE DECIMAL(6,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLAR ADD SC_HONOR VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLAR ADD SC_HONOR VARCHAR2(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLAR ADD SC_DOCNO VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLAR ADD SC_DOCNO VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLAR ADD SC_DOCDATE VARCHAR(19) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLAR ADD SC_DOCDATE VARCHAR2(19) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLAR ADD SC_INSTITUTE VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLAR ADD SC_INSTITUTE VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLAR ADD CT_CODE VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLAR ADD CT_CODE VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLAR ADD SC_FUND VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLAR ADD SC_FUND VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SCHOLAR ALTER INS_CODE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SCHOLAR MODIFY INS_CODE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SCHOLAR MODIFY INS_CODE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'rpt_R010032.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 2, 'R1032 �ӹǹ����Ҫ��þ����͹ ��ṡ����дѺ���˹� �� ����ѧ�Ѵ', 'S', 'W', 'rpt_R010032.html', 0, 36, 405, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 2, 'R1032 �ӹǹ����Ҫ��þ����͹ ��ṡ����дѺ���˹� �� ����ѧ�Ѵ', 'S', 'W', 'rpt_R010032.html', 0, 36, 405, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'rpt_R010036.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 6, 'R1036 ��ͺ�ѵ�ҡ��ѧ����Ҫ��þ����͹ ��ṡ�����§ҹ �дѺ���˹� ����ͧ ����ѵ����ҧ (�ҹ�����Ũҡ�ѵ���Թ��͹��駨���)', 'S', 'W', 
								  'rpt_R010036.html', 0, 36, 405, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 6, 'R1036 ��ͺ�ѵ�ҡ��ѧ����Ҫ��þ����͹ ��ṡ�����§ҹ �дѺ���˹� ����ͧ ����ѵ����ҧ (�ҹ�����Ũҡ�ѵ���Թ��͹��駨���)', 'S', 'W', 
								  'rpt_R010036.html', 0, 36, 405, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ISREAL CHAR(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POSITIONHIS SET POH_ISREAL = 'Y' WHERE POH_ISREAL IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_POS_ORGMGT VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_POS_ORGMGT VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG_DOPA_CODE VARCHAR(20) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ORG_DOPA_CODE VARCHAR2(20) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_ORG_DOPA_CODE VARCHAR(20) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_ORG_DOPA_CODE VARCHAR2(20) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_MARRHIS ALTER MAH_MARRY_DATE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_MARRHIS MODIFY MAH_MARRY_DATE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_MARRHIS MODIFY MAH_MARRY_DATE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_NAMEHIS ALTER NH_DATE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_NAMEHIS MODIFY NH_DATE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_NAMEHIS MODIFY NH_DATE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="oci8") {
				$cmd = " ALTER TABLE PER_MESSAGE MODIFY MSG_DETAIL VARCHAR2(4000) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EDUCNAME ALTER EN_SHORTNAME VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EDUCNAME MODIFY EN_SHORTNAME VARCHAR2(100) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_EDUCNAME MODIFY EN_SHORTNAME VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_ATTACHMENT(
				AT_CODE VARCHAR(10) NOT NULL,	
				AT_NAME VARCHAR(100) NOT NULL,
				AT_REMARK VARCHAR(255) NULL,
				AT_ACTIVE SINGLE NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				AT_SEQ_NO INTEGER2 NULL,
				CONSTRAINT PK_PER_ATTACHMENT PRIMARY KEY (AT_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_ATTACHMENT(
				AT_CODE VARCHAR2(10) NOT NULL,	
				AT_NAME VARCHAR2(100) NOT NULL,
				AT_REMARK VARCHAR2(255) NULL,
				AT_ACTIVE NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				AT_SEQ_NO NUMBER(5) NULL,
				CONSTRAINT PK_PER_ATTACHMENT PRIMARY KEY (AT_CODE)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_ATTACHMENT(
				AT_CODE VARCHAR(10) NOT NULL,	
				AT_NAME VARCHAR(100) NOT NULL,
				AT_REMARK VARCHAR(255) NULL,
				AT_ACTIVE SMALLINT(1) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				AT_SEQ_NO SMALLINT(5) NULL,
				CONSTRAINT PK_PER_ATTACHMENT PRIMARY KEY (AT_CODE)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('21', '���Ѥ�', '���Ѥ������ѡ�ҹ', 1, $SESS_USERID, '$UPDATE_DATE', 21) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('22', '��Ѻ�ͧᾷ��', '���Ѥ������ѡ�ҹ', 1, $SESS_USERID, '$UPDATE_DATE', 22) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('23', '���§ҹ���', '���Ѥ������ѡ�ҹ', 1, $SESS_USERID, '$UPDATE_DATE', 23) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('24', '�������¹������', '���Ѥ������ѡ�ҹ', 1, $SESS_USERID, '$UPDATE_DATE', 24) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('25', '˹ѧ��� û�. ���', '���Ѥ������ѡ�ҹ', 1, $SESS_USERID, '$UPDATE_DATE', 25) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('26', '㺷��ͧ��Ժѵ��Ҫ���, �鹷��ͧ��Ժѵ��Ҫ���', '���Ѥ������ѡ�ҹ', 1, $SESS_USERID, '$UPDATE_DATE', 26) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('31', '���Һѵû�Шӵ�ǻ�ЪҪ�', '��������ǹ���', 1, $SESS_USERID, '$UPDATE_DATE', 31) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('32', '���ҷ���¹��ҹ', '��������ǹ���', 1, $SESS_USERID, '$UPDATE_DATE', 32) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('33', '��Ӥѭ�������, ����', '��������ǹ���', 1, $SESS_USERID, '$UPDATE_DATE', 33) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('34', '��ѡ�ҹ�������¹�ӹ�˹�ҹ��, ����, ���ʡ��', '��������ǹ���', 1, $SESS_USERID, '$UPDATE_DATE', 34) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('35', '�����źԴ�-��ô�-����-�����-�ص�', '��������ǹ���', 1, $SESS_USERID, '$UPDATE_DATE', 35) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('36', '��� � (�Թ��������;����, ���˹稵��ʹ ���', '��������ǹ���', 1, $SESS_USERID, '$UPDATE_DATE', 36) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('41', '�زԡ���֡��', '����֡��-ͺ��-�٧ҹ-�ŧҹ-����ͧ�Ҫ���', 1, $SESS_USERID, '$UPDATE_DATE', 41) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('42', '���ͺ�� - �٧ҹ', '����֡��-ͺ��-�٧ҹ-�ŧҹ-����ͧ�Ҫ���', 1, $SESS_USERID, '$UPDATE_DATE', 42) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('43', '�ŧҹ����', '����֡��-ͺ��-�٧ҹ-�ŧҹ-����ͧ�Ҫ���', 1, $SESS_USERID, '$UPDATE_DATE', 43) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('44', '����ͧ�Ҫ��������ó� ���', '����֡��-ͺ��-�٧ҹ-�ŧҹ-����ͧ�Ҫ���', 1, $SESS_USERID, '$UPDATE_DATE', 44) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('51', '��è�����觵��, �Ѻ�͹', '����觵�ҧ �', 1, $SESS_USERID, '$UPDATE_DATE', 51) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('52', '��������觵��, �����Ҫ���, ��Ժѵ�˹�ҷ��, �ѡ�ҡ��', '����觵�ҧ �', 1, $SESS_USERID, '$UPDATE_DATE', 52) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('53', '����͹�дѺ, ����͹����Թ��͹, ������Ѻ�Թ����ز�, ��ҵͺ᷹�����', '����觵�ҧ �', 1, $SESS_USERID, '$UPDATE_DATE', 53) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('54', '�ɷҧ�Թ��, ��駡�������ͺ�Թ��', '����觵�ҧ �', 1, $SESS_USERID, '$UPDATE_DATE', 54) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('55', '���͡�ҡ�Ҫ���, ����͹, ����͡', '����觵�ҧ �', 1, $SESS_USERID, '$UPDATE_DATE', 55) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('61', '�ѹ��դٳ, ��С�ȡ���¡���֡, ¡��ԡ��С�ȡ���¡���֡, ��.���.', '�ѹ��դٳ - �ѹ�� - ��Ѻ�ͧ�����Ҫ���', 1, $SESS_USERID, '$UPDATE_DATE', 61) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('62', '�ѹ�ҵ�ҧ � (�Ҿѡ��͹-�֡�ҵ��-�Ǫ-��ʹ�ص� ���)', '�ѹ��դٳ - �ѹ�� - ��Ѻ�ͧ�����Ҫ���', 1, $SESS_USERID, '$UPDATE_DATE', 62) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('63', '��Ѻ�ͧ�����Ҫ��� (����-���Ǩ)', '�ѹ��դٳ - �ѹ�� - ��Ѻ�ͧ�����Ҫ���', 1, $SESS_USERID, '$UPDATE_DATE', 63) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ATTACHMENT (AT_CODE, AT_NAME, AT_REMARK, AT_ACTIVE, UPDATE_USER, UPDATE_DATE, AT_SEQ_NO)
							  VALUES ('70', '�.�.7 ��͹�͹�� ��., ��� �', '��� �', 1, $SESS_USERID, '$UPDATE_DATE', 70) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'master_table_attachment.html?table=PER_ATTACHMENT' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 11, 'M0111 �͡���Ṻ', 'S', 'W', 'master_table_attachment.html?table=PER_ATTACHMENT', 0, 9, 296, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 11, 'M0111 �͡���Ṻ', 'S', 'W', 'master_table_attachment.html?table=PER_ATTACHMENT', 0, 9, 296, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R004000_select_person.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 0, 'R0400 �.�.7 ����礷�͹ԡ��', 'S', 'W', 'rpt_R004000_select_person.html', 0, 36,
								  236, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 0, 'R0400 �.�.7 ����礷�͹ԡ��', 'S', 'W', 'rpt_R004000_select_person.html', 0, 36,
								  236, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " update PER_CONTROL set CTRL_ALTER = 9 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if($CTRL_ALTER < 9) 

//		if($CTRL_ALTER < 10) {
			if (!$BUTTON_DISPLAY) {
				$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
								values (30, 'BUTTON_DISPLAY', '1', '����ʴ��Ż�����') ";
				$db->send_cmd($cmd);
			}

			$cmd = " ALTER TABLE PER_POSITION DROP COLUMN ORG_FLAG_KPI ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POSITION DROP COLUMN ORG_ID_KPI ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POSITION DROP COLUMN ORG_FLAG_SALARY ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POSITION DROP COLUMN ORG_ID_SALARY ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE  PER_PERSONAL DROP COLUMN HIP_FLAG ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_EXTRAHIS DROP COLUMN EXH_BOOK_NO ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_EXTRAHIS DROP COLUMN EXH_BOOK_DATE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REWARDHIS DROP COLUMN REH_BOOK_NO ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_REWARDHIS DROP COLUMN REH_BOOK_DATE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE user_group ADD dpisdb char(1) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE user_group ADD dpisdb_host varchar(50) ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE user_group ADD dpisdb_host varchar2(50) ";
			elseif($db_type=="mysql")
				$cmd = " ALTER TABLE user_group ADD dpisdb_host varchar(50) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE user_group ADD dpisdb_name varchar(50) ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE user_group ADD dpisdb_name varchar2(50) ";
			elseif($db_type=="mysql")
				$cmd = " ALTER TABLE user_group ADD dpisdb_name varchar(50) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE user_group ADD dpisdb_user varchar(50) ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE user_group ADD dpisdb_user varchar2(50) ";
			elseif($db_type=="mysql")
				$cmd = " ALTER TABLE user_group ADD dpisdb_user varchar(50) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE user_group ADD dpisdb_pwd varchar(50) ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE user_group ADD dpisdb_pwd varchar2(50) ";
			elseif($db_type=="mysql")
				$cmd = " ALTER TABLE user_group ADD dpisdb_pwd varchar(50) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ASSESS_MAIN ADD PER_TYPE SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ASSESS_MAIN ADD PER_TYPE NUMBER(1) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ASSESS_MAIN ADD PER_TYPE SMALLINT(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_ASSESS_MAIN SET PER_TYPE = 1 WHERE PER_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_ASSESS_MAIN (AM_CODE, AM_NAME, AM_POINT_MIN, AM_POINT_MAX, AM_ACTIVE, 
							UPDATE_USER, UPDATE_DATE, PER_TYPE)
							VALUES ('31', '��ͧ��Ѻ��ا (��ѡ�ҹ�Ҫ���)', 0, 59.99, 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ASSESS_MAIN (AM_CODE, AM_NAME, AM_POINT_MIN, AM_POINT_MAX, AM_ACTIVE, 
							UPDATE_USER, UPDATE_DATE, PER_TYPE)
							VALUES ('32', '���� (��ѡ�ҹ�Ҫ���)', 60, 69.99, 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ASSESS_MAIN (AM_CODE, AM_NAME, AM_POINT_MIN, AM_POINT_MAX, AM_ACTIVE, 
							UPDATE_USER, UPDATE_DATE, PER_TYPE)
							VALUES ('33', '�� (��ѡ�ҹ�Ҫ���)', 70, 79.99, 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ASSESS_MAIN (AM_CODE, AM_NAME, AM_POINT_MIN, AM_POINT_MAX, AM_ACTIVE, 
							UPDATE_USER, UPDATE_DATE, PER_TYPE)
							VALUES ('34', '���ҡ (��ѡ�ҹ�Ҫ���)', 80, 89.99, 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ASSESS_MAIN (AM_CODE, AM_NAME, AM_POINT_MIN, AM_POINT_MAX, AM_ACTIVE, 
							UPDATE_USER, UPDATE_DATE, PER_TYPE)
							VALUES ('35', '���� (��ѡ�ҹ�Ҫ���)', 90, 100, 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ASSESS_LEVEL ADD PER_TYPE SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ASSESS_LEVEL ADD PER_TYPE NUMBER(1) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ASSESS_LEVEL ADD PER_TYPE SMALLINT(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_ASSESS_LEVEL SET PER_TYPE = 1 WHERE PER_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_ASSESS_LEVEL (AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, DEPARTMENT_ID, 
							AM_CODE,	AL_PERCENT, AL_ACTIVE, UPDATE_USER, UPDATE_DATE, PER_TYPE)
							VALUES ('310', '��ͧ��Ѻ��ا (��ѡ�ҹ�Ҫ���)', 0, 59.99, $DEPARTMENT_ID, '31', 0, 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ASSESS_LEVEL (AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, DEPARTMENT_ID, 
							AM_CODE,	AL_PERCENT, AL_ACTIVE, UPDATE_USER, UPDATE_DATE, PER_TYPE)
							VALUES ('321', '���� 1 (��ѡ�ҹ�Ҫ���)', 60, 64.99, $DEPARTMENT_ID, '32', 2.4, 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ASSESS_LEVEL (AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, DEPARTMENT_ID, 
							AM_CODE,	AL_PERCENT, AL_ACTIVE, UPDATE_USER, UPDATE_DATE, PER_TYPE)
							VALUES ('322', '���� 2 (��ѡ�ҹ�Ҫ���)', 65, 69.99, $DEPARTMENT_ID, '32', 2.5, 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ASSESS_LEVEL (AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, DEPARTMENT_ID, 
							AM_CODE,	AL_PERCENT, AL_ACTIVE, UPDATE_USER, UPDATE_DATE, PER_TYPE)
							VALUES ('331', '�� 1 (��ѡ�ҹ�Ҫ���)', 70, 74.99, $DEPARTMENT_ID, '33', 2.9, 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ASSESS_LEVEL (AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, DEPARTMENT_ID, 
							AM_CODE,	AL_PERCENT, AL_ACTIVE, UPDATE_USER, UPDATE_DATE, PER_TYPE)
							VALUES ('332', '�� 2 (��ѡ�ҹ�Ҫ���)', 75, 79.99, $DEPARTMENT_ID, '33', 3, 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ASSESS_LEVEL (AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, DEPARTMENT_ID, 
							AM_CODE,	AL_PERCENT, AL_ACTIVE, UPDATE_USER, UPDATE_DATE, PER_TYPE)
							VALUES ('341', '���ҡ 1 (��ѡ�ҹ�Ҫ���)', 80, 84.99, $DEPARTMENT_ID, '34', 3.4, 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ASSESS_LEVEL (AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, DEPARTMENT_ID, 
							AM_CODE,	AL_PERCENT, AL_ACTIVE, UPDATE_USER, UPDATE_DATE, PER_TYPE)
							VALUES ('342', '���ҡ 2 (��ѡ�ҹ�Ҫ���)', 85, 89.99, $DEPARTMENT_ID, '34', 3.5, 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ASSESS_LEVEL (AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, DEPARTMENT_ID, 
							AM_CODE,	AL_PERCENT, AL_ACTIVE, UPDATE_USER, UPDATE_DATE, PER_TYPE)
							VALUES ('351', '���� 1 (��ѡ�ҹ�Ҫ���)', 90, 94.99, $DEPARTMENT_ID, '35', 4.4, 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			$cmd = " INSERT INTO PER_ASSESS_LEVEL (AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, DEPARTMENT_ID, 
							AM_CODE,	AL_PERCENT, AL_ACTIVE, UPDATE_USER, UPDATE_DATE, PER_TYPE)
							VALUES ('352', '���� 2 (��ѡ�ҹ�Ҫ���)', 95, 100, $DEPARTMENT_ID, '35', 4.5, 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ADD PER_TYPE SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ADD PER_TYPE NUMBER(1) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ADD PER_TYPE SMALLINT(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMPENSATION_TEST SET PER_TYPE = 1 WHERE PER_TYPE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_POS_MGTSALARY (
				POS_ID INTEGER NOT NULL,
				EX_CODE VARCHAR(10) NOT NULL,
				POS_STARTDATE VARCHAR(19) NULL,
				POS_STATUS SINGLE NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,
				CONSTRAINT PK_PER_POS_MGTSALARY PRIMARY KEY (POS_ID, EX_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_POS_MGTSALARY (
				POS_ID NUMBER(10) NOT NULL,
				EX_CODE VARCHAR2(10) NOT NULL,
				POS_STARTDATE VARCHAR2(19) NULL,
				POS_STATUS NUMBER(1) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,
				CONSTRAINT PK_PER_POS_MGTSALARY PRIMARY KEY (POS_ID, EX_CODE)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_POS_MGTSALARY (
				POS_ID INTEGER(10) NOT NULL,
				EX_CODE VARCHAR(10) NOT NULL,
				POS_STARTDATE VARCHAR(19) NULL,
				POS_STATUS SMALLINT(1) NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,
				CONSTRAINT PK_PER_POS_MGTSALARY PRIMARY KEY (POS_ID, EX_CODE)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_POS_MGTSALARYHIS(
				PMH_ID INTEGER NOT NULL,	
				PER_ID INTEGER NOT NULL,	
				PER_CARDNO VARCHAR(13) NULL,
				PMH_EFFECTIVEDATE VARCHAR(19) NOT NULL,
				EX_CODE VARCHAR(10) NOT NULL,	
				PMH_AMT NUMBER NOT NULL,
				PMH_ENDDATE VARCHAR(19) NULL,
				PMH_ACTIVE SINGLE NULL,
				PMH_REMARK MEMO NULL,
				PMH_SEQ_NO INTEGER2 NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_POS_MGTSALARYHIS PRIMARY KEY (PMH_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_POS_MGTSALARYHIS(
				PMH_ID NUMBER(10) NOT NULL,	
				PER_ID NUMBER(10) NOT NULL,	
				PER_CARDNO VARCHAR2(13) NULL,
				PMH_EFFECTIVEDATE VARCHAR2(19) NOT NULL,
				EX_CODE VARCHAR2(10) NOT NULL,	
				PMH_AMT NUMBER(16,2) NOT NULL,
				PMH_ENDDATE VARCHAR2(19) NULL,
				PMH_ACTIVE NUMBER(1) NULL,
				PMH_REMARK VARCHAR2(1000) NULL,
				PMH_SEQ_NO NUMBER(5) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_POS_MGTSALARYHIS PRIMARY KEY (PMH_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_POS_MGTSALARYHIS(
				PMH_ID INTEGER(10) NOT NULL,	
				PER_ID INTEGER(10) NOT NULL,	
				PER_CARDNO VARCHAR(13) NULL,
				PMH_EFFECTIVEDATE VARCHAR(19) NOT NULL,
				EX_CODE VARCHAR(10) NOT NULL,	
				PMH_AMT DECIMAL(16,2) NOT NULL,
				PMH_ENDDATE VARCHAR(19) NULL,
				PMH_ACTIVE SMALLINT(1) NULL,
				PMH_REMARK TEXT NULL,
				PMH_SEQ_NO SMALLINT(5) NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_POS_MGTSALARYHIS PRIMARY KEY (PMH_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER SF_CODE_O NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_O NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_O NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER SF_CODE_K NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_K NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_K NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER SF_CODE_D NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_D NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_D NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST ALTER SF_CODE_M NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_M NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMPENSATION_TEST MODIFY SF_CODE_M NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = LEVEL_NAME WHERE POSITION_LEVEL IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_POSITION VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_POSITION VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_ORG VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_DECORATEHIS ADD DEH_ORG VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_DECORATEHIS ADD DEH_POSITION VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_DECORATEHIS ADD DEH_POSITION VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_DECORATEHIS ADD DEH_ORG VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_DECORATEHIS ADD DEH_ORG VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMDTL ADD CMD_SEQ_NO INTEGER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL ADD CMD_SEQ_NO NUMBER(6) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMDTL ADD CMD_SEQ_NO INTEGER(6) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET MENU_LABEL = 'A03 �š�û����Թ��û�Ժѵ��Ҫ���' 
							  WHERE LINKTO_WEB = 'personal_kpi.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " ALTER TABLE PER_EXTRAHIS DROP CONSTRAINT FK2_PER_EXTRAHIS ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EXTRATYPE ALTER EX_CODE CHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EXTRATYPE MODIFY EX_CODE CHAR(10) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EXTRATYPE MODIFY EX_CODE CHAR(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_EXTRAHIS ALTER EX_CODE CHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EXTRAHIS MODIFY EX_CODE CHAR(10) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EXTRAHIS MODIFY EX_CODE CHAR(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_EXTRAHIS ADD CONSTRAINT FK2_PER_EXTRAHIS 
							  FOREIGN KEY (EX_CODE) REFERENCES PER_EXTRATYPE (EX_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('006', '�Թ���¡ѹ���', NULL, '10 % �ͧ�Թ��͹��軯Ժѵ��Ҫ���㹷�ͧ���ѹ���', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('007', '�Թ�������������Ѻ������ú', '�.�.�.', '�Թ��������ɷ����µ������º��Ҵ��¡�þԨ�óҺ��˹�', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('008', '�Թ�������������Ѻ��û�Һ��������зӼԴ', '�.�.�.', '�Թ����������黯Ժѵ�˹�ҷ��㹡�û�Һ��������зӼԴ', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('009', '���ʴԡ������Ѻ��黯Ժѵԧҹ��ӹѡ�ҹ��鹷������', '�.�.�.', NULL, 1000, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('010', '�Թ��Шӵ��˹� (���˹觻������ӹ�¡�� �дѺ��)', NULL, NULL, 5600, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('011', '�Թ��Шӵ��˹� (���˹觻������Ԫҡ�� �дѺ����Ǫҭ)', NULL, NULL, 9900, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('012', '�Թ������ä�ͧ�վ���Ǥ�������Ѻ����Ҫ���', NULL, NULL, 1000, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('013', '�Թ������ä�ͧ�վ���Ǥ�������Ѻ�١��ҧ��Ш�', NULL, NULL, 1000, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('014', '�Թ������ä�ͧ�վ���Ǥ�������Ѻ��ѡ�ҹ�Ҫ���', NULL, NULL, 1500, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('015', '�Թ�ͺ᷹������Թ��͹������(2%)', NULL, '����Ѻ����ռš�û����Թ����Է���Ҿ��û�Ժѵԧҹ���觢�� (0.5)', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('016', '�Թ�ͺ᷹������Թ��͹������(4%)', NULL, '����Ѻ����ռš�û����Թ����Է���Ҿ��û�Ժѵԧҹ˹�觢��(1.00)', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('017', '�Թ��Шӵ��˹� (���˹觻������ӹ�¡�� �дѺ�٧)', NULL, NULL, 10000, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('020', '�Թ��Шӵ��˹� (���˹��Ԫҡ�� �дѺ�ӹҭ���) (Ǫ.)', NULL, NULL, 3500, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('023', '�Թ��Шӵ��˹� (���˹觻����������� �дѺ�٧)', NULL, NULL, 14500, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('024', '�Թ��͹', NULL, NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('025', '��¡���Թ��������ɷ���ԡ�ҡ���ؤ�ҡ�', '��ͨ���', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('026', '�Թ��������������͹����Ԫ�', '�.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('027', '�Թ�����������͹�ҹ�', '�.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('029', '�Թ�ѧ�վ����Ҫ��èѧ��Ѵ�Ҥ��', '�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('030', '�Թ���ʴԡ�����¡ѹ���', '�.�', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('031', '�Թ�����������������', '�.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('033', '�Թ��������§���', '�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('034', '�Թ���ʴԡ������ǡѺ�����Һ�ҹ', '�.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('035', '�Թ��ҵͺ᷹������üѹ����ŧҹ����Ѻ��������', '�.�.�.��-�', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('036', '�Թ��ҵͺ᷹������üѹ����ŧҹ����Ѻ˹��§ҹ', '�.�.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('037', '�������÷ӡ�ù͡�����Ҫ���', '��������', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('038', '�Թ��ҵͺ᷹���Ҩ���᷹��èѴ��ö��Шӵ��˹�', '��.���.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('039', '�Թ��������ͺصâ���Ҫ�������١��ҧ��Ш�', '�.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('040', '�Թ���� �ʨ.', '����-�ʨ.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('041', '�Թ�ҧ���', '�.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('042', '�Թ�ѡ/�ԹŴ/�Թ����ͧ����', '�Թ�ѡ', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('043', '�Թ�ѡ/˹���Թ����ͧ��/���е��������', '�Թ�ѡ��� ��.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('044', '�Թ���� �ʨ. (��衮���¡�˹�)', '����-�ʨ.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('045', '�Թ���� �ʨ. (��Ѥ��)', '����-�ʨ.-��Ѥ��', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('046', '�Թ�����ͧ�ع���ͧ����§�վ', '����-�ʪ.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('047', '����ҧ��', '����ҧ��', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('049', '���Ѵ�Թ', '���Ѵ�Թ', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('050', '�ˡó������Ѿ��', '�ˡó�', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('051', '�Թ�ҡ�ˡó��', '�.�.�ˡó�', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('052', '�ع���͹����ˡó�� (�����͹)', '�.�.�ˡó�', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('053', '�Թ����ˡó�� (���ѭ/�ء�Թ/�����)', '�.�.�ˡó�', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('054', '��Ҹ��������á����ˡó������Ѿ��', '��Ҹ�������', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('055', '�Թ��ӻ�Сѹ�ˡó������Ѿ��', '��ӻ�Сѹ�ˡó�', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('056', '�Թ��Сѹ��¡�����ˡó������Ѿ��', 'Insurance.grp', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('057', '�Թ������ͷ�����������', '������������', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('058', '�Թ����١��ҹ', '��١��ҹ', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('059', '�Թ������͡���֡��', '����֡��', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('060', '�Թ������ͷع����֡��', '����.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('061', '�Թ������ʴԡ���ҹ��˹�', '�ҹ��˹�', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('062', '�Թ������ʴԡ�èѡ��ҹ¹��', '�.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('063', '�Թ������ʴԡ��ö¹��', 'ö¹��', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('064', '�Թ���ʴԡ��/�����/�Թ�عʧ������', '���ʴԡ��/�����', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('065', '�Թ�ع���ʴԡ��', '�.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('066', '�Թ������ʴԡ��', '�.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('069', '�Թ���ʴԡ�������', '�����', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('070', '�Թ���ا�����', '���ا�����', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('071', '�Թ���ͧ����ʧ��������ü�ҹ�֡', '�.�.ͼ�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('072', '�Թ��������͢���Ҫ���/�١��ҧ', '��.��.Ũ.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('073', '�Թ�عʧ������', '��.ʧ������', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('074', '�Թ�ع�����������ʧ������', '���ʴԡ��', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('075', '�Թ���仵�ҧ�����', '�.�.���.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('077', '�Թ��鸹Ҥ�þҳԪ��/�Թ������� �', '�Թ����', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('078', '�Թ������ʴԡ�èҡ��Ҥ��', '�.�.��.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('079', '�Թ������ʴԡ�èҡ��Ҥ������Թ', '���ʴ�.���', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('080', '�Թ�ѡ�����͵�ŧ��������ѭ��', '��͵�ŧ/�ѭ��', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('081', '�Թ�ѡ����ػ���кص�/�����', '�ػ����', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('082', '�ç��� Computer ICT ����Ѻ���˹�ҷ��ͧ�Ѱ', '�.�.Comp ICT', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('083', '�Թ��һ�Сѹ���Ե', '��Сѹ���Ե', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('084', '�Թ��ӻ�Сѹ�١��ҧ��Ш�', '��ӻ�Сѹ-Ũ.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('085', '�Թ��ӻ�Сѹ', '��ӻ�Сѹ', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('086', '�Թ�Ѻ��Ҿ˹��', '�Ѻ��Ҿ˹��', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('089', '��ҫ������ظ�׹', '���ظ�׹', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('094', '���ʴԡ����ҹ���', '�.��ҹ���', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('096', '��ҹ�Ҹ�óٻ���', '�Ҹ�óٻ���', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('098', '˹���Թ��� � �ͧ˹���', '˹���Թ���', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('099', '�Թ�һ��Ԩʧ������', '���.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('100', '�Թ��������ͧҹȾ', '�ҹȾ', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('102', '�Թ���¡�׹/����/�ԡ�ѡ��ѡ��/�Թ����', '�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('103', '�Թ�����͹�׹�ҡ �ʨ. ��й���', '�.����-�ʨ.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('104', '�Թ�͹�׹������й���', '�.�.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('105', '�Թ�ԡ��ǧ���', '��ǧ���', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('106', '�Թ�����͹�׹�ҡ �ʨ. ��й���', '�.ʺ��-�ʨ.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('107', '���¡�׹����Թ���� ���.', 'ä.���.', '���¡�׹����Թ���� ���. (�Թ����ԡ�ҡ�����Թ�ҹ)', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('108', '���¡�׹��й����Թ���ʴԡ������ǡѺ�����Һ�ҹ', 'ä.�.�.�.', '���¡�׹��й����Թ���ʴԡ������ǡѺ�����Һ�ҹ (�Թ����ԡ�ҡ�����Թ�ҹ)', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('109', '���¡�׹��й����Թ �.�.�.', 'ä.�.�.�.', '���¡�׹��й����Թ �.�.�. (�Թ����ԡ�ҡ�����Թ�ҹ)', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('110', '���¡�׹��й����Թ �.�.���.', 'ä.�.�.���.', '���¡�׹��й����Թ �.�.���. (�Թ����ԡ�ҡ�����Թ�ҹ)', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('111', '���¡�׹��й����Թ �.�.�.', 'ä.�.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('112', '���¡�׹��й����Թ �.�.�.', 'ä.�.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('113', '���¡�׹��й����Թ �.�.�.', 'ä.�.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('115', '���¡�׹��й����Թ �.�.', 'ä.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('116', '���¡�׹��й����Թ �.�.', 'ä.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('117', '���¡�׹��й����Թ �.�.�.', 'ä.�.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('143', '���¡�׹��й����Թ �.�.�.', 'ä.�.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('148', '���¡�׹��й����Թ �.�.�.�. (��Ǵ0110-0140)', NULL, NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('149', '���¡�׹��й����Թ �.�.�.�. (��Ǵ0110)', 'ä.�.�.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('150', '���¡�׹��й����Թ ���.Ǫ (��Ǵ0120)', 'ä.���.Ǫ.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('151', '���¡�׹��й����Թ ���.��. (��Ǵ0130)', 'ä.���.��', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('152', '���¡�׹��й����Թ ���.� (��Ǵ0141)', 'ä.���.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('158', '���¡�׹��й����Թ �.�.', 'ä.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('159', '���¡�׹��й����Թ ʻ�.', 'ä.�.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('160', '���¡�׹��й����Թ �.�.�.���.', 'ä.�.�.�.���.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('161', '���¡�׹��й����Թ �.�.8-8�.', 'ä.�.�.8-8�.', NULL, 3500, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('162', '���¡�׹��й����Թ �.�.�.1-7', 'ä.�.�.�.1-7', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('163', '���¡�׹��й����Թ �.�.�.', 'ä.�.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('164', '���¡�׹��й����Թ �.�.�.', 'ä.�.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('165', '���¡�׹��й����Թ ��͡-�.�.�Ѱ', 'ä.��͡-�.�.�Ѱ', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('166', '���¡�׹��й����Թ ���-�.�.�Ѱ', 'ä.���-�.�.�Ѱ', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('167', '���¡�׹��й����Թ ���-�͡��', 'ä.��͡-�͡��', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('168', '���¡�׹��й����Թ ��͡-�͡��', 'ä.��͡-�͡��', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('169', '���¡�׹��й����Թ �.�.�.', 'ä.�.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('170', '���¡�׹��й����Թ - �֡�Һص�', 'ä.�֡�Һص�', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('171', '���¡�׹��й����Թ �.�.�.', 'ä.�.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('172', '�Թ�ͺ᷹/�Թ����/�Թ���ʴԡ��', '���ʴԡ��', '�Թ�ͺ᷹/�Թ����/�Թ���ʴԡ�� ����ԡ���¨ҡ����ҧ', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('173', '�Թ��������ͤ���ѡ�Ҿ�Һ�Ţ���Ҫ���/�١��ҧ��Ш�', '��͡-�.�.�Ѱ', '�Թ��������ͤ���ѡ�Ҿ�Һ�Ţ���Ҫ���/�١��ҧ��Ш� ��͡-�.�.�Ѱ', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('174', '�Թ��������ͤ���ѡ�Ҿ�Һ�Ţ���Ҫ���/�١��ҧ��Ш�', '���-�.�.�Ѱ', '�Թ��������ͤ���ѡ�Ҿ�Һ�Ţ���Ҫ���/�١��ҧ��Ш� ���-�.�.�Ѱ', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('175', '�Թ��������ͤ���ѡ�Ҿ�Һ�Ţ���Ҫ���/�١��ҧ��Ш�', '��͡-�͡��', '�Թ��������ͤ���ѡ�Ҿ�Һ�Ţ���Ҫ���/�١��ҧ��Ш� ��͡-�͡��', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('176', '�Թ��������ͤ���ѡ�Ҿ�Һ�Ţ���Ҫ���/�١��ҧ��Ш�', '���-�͡��', '�Թ��������ͤ���ѡ�Ҿ�Һ�Ţ���Ҫ���/�١��ҧ��Ш� ���-�͡��', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('177', '�Թ��������͡���֡����Ф���������¹�ص�', '�֡�Һص�', '�Թ��������͡���֡����Ф���������¹�صâͧ����Ҫ�������١��ҧ��Ш�', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('178', '�Թ������ͷ����������¢ͧ����Ҫ���/�١��ҧ��Ш�', '�.�.�.(�.�)', '�Թ������ͷ����������¢ͧ����Ҫ���/�١��ҧ��Ш�����Ѻ��Ҥ�����', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('179', '�Թ������ͷ����������¢ͧ����Ҫ���', '�.�.�.(���.)', '�Թ������ͷ����������¢ͧ����Ҫ��� (�.�Ҥ��ʧ������)', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('180', '�Թ������ͷ����������¢ͧ����Ҫ���', '�.�.�.(��.)', '�Թ������ͷ����������¢ͧ����Ҫ��� (�.����Թ)', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('181', '�Թ��鸹Ҥ������Թ', '�.�.�.(��.)', '�Թ��鸹Ҥ������Թ (��.9% ����ç��þѲ���)', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('182', '�Թ��鸹Ҥ���Ҥ��ʧ������', '�.�.�.(���.)', '�Թ��鸹Ҥ���Ҥ��ʧ������ (��.9% ����ç��þѲ���)', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('183', '�Թ������ͪ�������ͼ����ʾ�ط�����', '�.�.�.', '�Թ������ͪ�������ͼ����ʾ�ط���� �ҵ��� ����Ѥ�����', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('184', '�Թ���ʴԡ������Ѻ��û�Ժѵԧҹ��Ш��ӹѡ�ҹ', 'ʻ�.', '�Թ���ʴԡ������Ѻ��û�Ժѵԧҹ��Ш��ӹѡ�ҹ㹾�鹷������', 1000, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('185', '�Թ��ҵͺ᷹�͡�˹�ͨҡ�Թ��͹', '�.�.�.���.', '�Թ��ҵͺ᷹�����͹����Ѻ����Ҫ�����ҡѺ�ѵ���Թ��Шӵ��˹觵������º� ��� 5 ������Ҫ��÷�����Ѻ�Թ��Шӵ��˹觵����������Ҵ����Թ��͹����Թ��Шӵ��˹����Ѻ�Թ��ҵͺ᷹�����͹��ҡѺ�ѵ���Թ��Шӵ��˹觷�����Ѻ������� ¡��� ����Ҫ��ë�����Ѻ�Թ��Шӵ��˹��дѺ 7', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('186', '�Թ��ҵͺ᷹�����͹ (���˹觻������Ԫҡ�� �дѺ�ӹҭ��þ����)', '�.�.8-8�.', '�Թ��ҵͺ᷹�����͹����Ѻ����Ҫ����дѺ 8 ��� 8�. ������º��ҵ������º� ��� 6 ������Ҫ��ü���ç���˹��дѺ 8 ��� 8�. ������º��ҫ��������Է�����Ѻ�Թ��Шӵ��˹觵����������Ҵ����Թ��͹����Թ��Шӵ��˹�������Ѻ��ҵͺ᷹�������͹��ѵ����͹�� 3,500 �ҷ', 3500, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('187', '�Թ��ҵͺ᷹�����͹����Ѻ����Ҫ����дѺ 1-7', '�.�.�. 1-7', '�Թ��ҵͺ᷹�����͹����Ѻ����Ҫ����дѺ 1-7 ������º��ҵ������º��� 7 ��Т�� 8', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('188', '�Թ��ҵͺ᷹�����͹����Ѻ�١��ҧ��Ш�', '�.�.�.', '�Թ��ҵͺ᷹�����͹����Ѻ�١��ҧ��Шӵ������º��� 7 ��Т�� 8', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('189', '�Թ������ä�ͧ�վ���Ǥ�������Ѻ����Ҫ���', NULL, '�Թ������ä�ͧ�վ���Ǥ�������Ѻ����Ҫ��õ������º��� 5 (�.�.�.) ������Ҫ��÷�����Ѻ�Թ��͹����Թ��͹�� 10,500 �ҷ ���Ѻ�Թ������ä�ͧ�վ���Ǥ�����͹�� 1,000 �ҷ ������������Թ����ϡѺ�Թ��͹������Ѻ���ǵ�ͧ����Թ��͹�� 10,500 �ҷ �óշ���Թ���� � ����Ѻ�Թ��͹���֧��͹�� 7,350 �ҷ ������Ѻ�Թ�����ա�ӹǹ˹�� ������������������� ��͹�� 7,350 �ҷ', 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('190', '�Թ������ä�ͧ�վ���Ǥ�������Ѻ�١��ҧ��Ш�', '�.�.�.', '�Թ������ä�ͧ�վ���Ǥ�������Ѻ�١��ҧ��Шӵ������º��� 5 (�.�.�.) ����١��ҧ��Шӷ�����Ѻ��Ҩ�ҧ����Թ��͹�� 10,500 �ҷ ���Ѻ�Թ������ä�ͧ�վ���Ǥ�����͹�� 1,000 �ҷ ������������Թ����ϡѺ��Ҩ�ҧ������Ѻ���ǵ�ͧ����Թ��͹�� 10,500 �ҷ �óշ���Թ���� � ����Ѻ��Ҩ�ҧ���֧��͹�� 7,350 �ҷ ������Ѻ�Թ�����ա�ӹǹ˹�� ������������������� ��͹�� 7,350 �ҷ', 1000, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('191', '�Թ��������Ѻ���˹觢���Ҫ��á�����ͧ', '�.�.�.', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('192', '�Թ��������������', '�.��.����', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('193', '�Թ��ҵͺ᷹�͡�˹�ͨҡ�Թ��͹ (���˹觻����������� �дѺ�٧)', NULL, NULL, 14500, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('194', '�Թ��ҵͺ᷹�͡�˹�ͨҡ�Թ��͹ (���˹觻������ӹ�¡�� �дѺ�٧)', NULL, NULL, 10000, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('195', '�Թ��ҵͺ᷹�͡�˹�ͨҡ�Թ��͹ (���˹觻������ӹ�¡�� �дѺ��)', NULL, NULL, 5600, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('196', '�Թ��ҵͺ᷹�͡�˹�ͨҡ�Թ��͹ (���˹觻������Ԫҡ�� �дѺ����Ǫҭ)', NULL, NULL, 9900, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('197', '��ҵͺ᷹�����', NULL, '��ҵͺ᷹����� (��ѡ�ҹ�Ҫ���)', 360, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('198', '��ҵͺ᷹�����͹', NULL, NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('199', '�Թ�ͺ᷹������Թ��͹������(6%)', NULL, NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('200', '�Թ��Шӵ��˹� (���˹觻������Ԫҡ�� �дѺ�ӹҭ��þ����)(Ǫ.)', NULL, NULL, 5600, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_EXTRATYPE (EX_CODE, EX_NAME, EX_SHORTNAME, EX_REMARK, EX_AMT, EX_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE)
							  VALUES ('201', '�Թ��ҵͺ᷹�͡�˹�ͨҡ�Թ��͹ (���˹觻������Ԫҡ�� �дѺ�ӹҭ��þ����)(Ǫ.)', NULL, NULL, 5600, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_GROUP ADD PG_NAME_SALARY VARCHAR(100) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_GROUP ADD PG_NAME_SALARY VARCHAR2(100) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET LINKTO_WEB = 'master_table_pos_group.html?table=PER_POS_GROUP' 
							  WHERE LINKTO_WEB = 'master_table.html?table=PER_POS_GROUP' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE PER_POS_GROUP SET PG_NAME_SALARY = '�������� 1' WHERE PG_CODE = '1000' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_GROUP SET PG_NAME_SALARY = '�������� 2' WHERE PG_CODE = '2000' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_GROUP SET PG_NAME_SALARY = '�������� 3' WHERE PG_CODE = '3000' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_GROUP SET PG_NAME_SALARY = '�������� 4' WHERE PG_CODE = '4000' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_GROUP SET PG_NAME_SALARY = PG_NAME WHERE PG_NAME_SALARY IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD ES_CODE VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD ES_CODE VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_LEVEL_NO VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_LEVEL_NO VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_POSITIONHIS SET POH_LEVEL_NO = LEVEL_NO WHERE POH_LEVEL_NO IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " ALTER TABLE user_group ADD group_per_type smallint(1) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " ALTER TABLE user_group ADD group_org_structure smallint(1) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('21305', '����͹�Թ��͹', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('21375', '���������͹�Թ��͹', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_EDUCATE, PER_EDUCNAME SET PER_EDUCATE.EL_CODE = 
								  PER_EDUCNAME.EL_CODE WHERE PER_EDUCNAME.EN_CODE = PER_EDUCATE.EN_CODE AND 
								  PER_EDUCATE.EN_CODE IS NOT NULL AND PER_EDUCATE.EL_CODE IS NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_EDUCATE A SET A.EL_CODE = 
								  (SELECT B.EL_CODE FROM PER_EDUCNAME B WHERE A.EN_CODE = B.EN_CODE) 
								  WHERE A.EN_CODE IS NOT NULL AND A.EL_CODE IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD KF_STATUS SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_KPI_FORM ADD KF_STATUS NUMBER(1) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_KPI_FORM ADD KF_STATUS SMALLINT(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc"){
				$cmd = " ALTER TABLE PER_POSITION_COMPETENCE ADD PER_TYPE SINGLE NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION_COMPETENCE SET PER_TYPE = 1 WHERE PER_TYPE IS NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_POSITION_COMPETENCE ALTER PER_TYPE SINGLE NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_POSITION_COMPETENCE DROP CONSTRAINT PK_PER_POSITION_COMPETENCE ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_POSITION_COMPETENCE ADD CONSTRAINT PK_PER_POSITION_COMPETENCE 
								  PRIMARY KEY (PER_TYPE, POS_ID, CP_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

			}elseif($DPISDB=="oci8"){
				$cmd = " ALTER TABLE PER_POSITION_COMPETENCE ADD PER_TYPE NUMBER(1) NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITION_COMPETENCE SET PER_TYPE = 1 WHERE PER_TYPE IS NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_POSITION_COMPETENCE MODIFY PER_TYPE NOT NULL ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " DROP INDEX PK_PER_POSITION_COMPETENCE ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " ALTER TABLE PER_POSITION_COMPETENCE ADD CONSTRAINT PK_PER_POSITION_COMPETENCE PRIMARY KEY 
								  (PER_TYPE, POS_ID, CP_CODE) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'personal_inquiry_dopa.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 22, 'P0122 �ͺ��������� (�����û���ͧ)', 'S', 'W', 'personal_inquiry_dopa.html', 0, 35, 241, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 22, 'P0122 �ͺ��������� (�����û���ͧ)', 'S', 'W', 'personal_inquiry_dopa.html', 0, 35, 241, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMDTL ADD CMD_ES_CODE VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL ADD CMD_ES_CODE VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMDTL ADD ES_CODE VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL ADD ES_CODE VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMDTL ADD PM_CODE VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL ADD PM_CODE VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('21395', '�Թ��͹�Ѻ�͹', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE, MOV_SUB_TYPE)
							  VALUES ('21394', '�Թ��͹��èء�Ѻ', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_OLD_SALARY NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_OLD_SALARY NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_OLD_SALARY DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE  PER_COMMAND DROP CONSTRAINT INXU1_PER_COMMAND ";
			elseif($DPISDB=="oci8") 
				$cmd = " DROP INDEX INXU1_PER_COMMAND ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX INXU1_PER_COMMAND ON PER_COMMAND (COM_NO) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO) 
							  VALUES ('5240', '��. 24', '����觻�Ѻ�ѵ���Թ��͹�á��è�', '524', 100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_COMTYPE SET COM_GROUP = '524' WHERE COM_TYPE = '9999' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ORDER_DTL ADD LEVEL_NO VARCHAR(10) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORDER_DTL ADD LEVEL_NO VARCHAR2(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ORDER_DTL ADD ORD_REMARK MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORDER_DTL ADD ORD_REMARK VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ORDER_DTL ADD ORD_REMARK TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COURSE ADD CO_COURSE_NAME VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COURSE ADD CO_COURSE_NAME VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COURSE ADD CO_DEGREE_RECEIVE VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COURSE ADD CO_DEGREE_RECEIVE VARCHAR2(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COURSE ADD CO_BOOK_NO VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COURSE ADD CO_BOOK_NO VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COURSE ADD CO_BOOK_DATE VARCHAR(19) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COURSE ADD CO_BOOK_DATE VARCHAR2(19) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_POS_DOCDATE VARCHAR(19) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_POS_DOCDATE VARCHAR2(19) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_POS_DESC VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_POS_DESC VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_POS_REMARK VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_POS_REMARK VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_BOOK_NO VARCHAR(255) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_BOOK_NO VARCHAR2(255) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_BOOK_DATE VARCHAR(19) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_BOOK_DATE VARCHAR2(19) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_DOCNO NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_DOCNO NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_DOCNO NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_DOCDATE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_DOCDATE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_DOCDATE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ALTER POH_DOCNO NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS MODIFY POH_DOCNO NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS MODIFY POH_DOCNO NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ALTER POH_DOCDATE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS MODIFY POH_DOCDATE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS MODIFY POH_DOCDATE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITION ALTER POS_MGTSALARY NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITION MODIFY POS_MGTSALARY NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITION MODIFY POS_MGTSALARY NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITION ALTER PT_CODE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITION MODIFY PT_CODE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITION MODIFY PT_CODE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_MOVE ALTER POS_MGTSALARY NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_MOVE MODIFY POS_MGTSALARY NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_MOVE MODIFY POS_MGTSALARY NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_MOVE ALTER PT_CODE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_MOVE MODIFY PT_CODE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_MOVE MODIFY PT_CODE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_CMD_SEQ INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_CMD_SEQ NUMBER(6) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_CMD_SEQ INTEGER(6) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_CMD_SEQ INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_CMD_SEQ NUMBER(6) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_CMD_SEQ INTEGER(6) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_CMD_SEQ INTEGER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS MODIFY POH_CMD_SEQ NUMBER(6) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITIONHIS ALTER POH_CMD_SEQ INTEGER(6) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_SALARYHIS ALTER SAH_CMD_SEQ INTEGER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS MODIFY SAH_CMD_SEQ NUMBER(6) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SALARYHIS ALTER SAH_CMD_SEQ INTEGER(6) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_9 ON PER_PERSONAL (PAY_ID, PER_STATUS) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_ORG_2 ON PER_ORG (ORG_DOPA_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_ORG_3 ON PER_ORG (ORG_ID_REF) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_10 ON PER_PERSONAL (POEMS_ID, PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_11 ON PER_PERSONAL (POEMS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_12 ON PER_PERSONAL (POEMS_ID, PN_CODE, PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_PERSONAL_13 ON PER_PERSONAL (DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_PERSONAL SET PER_FILE_NO = NULL WHERE PER_FILE_NO = 'NULL' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_PERSONAL SET PER_BANK_ACCOUNT = NULL WHERE PER_BANK_ACCOUNT = 'NULL' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_ORG_LEVEL SET OL_SEQ_NO = 1 WHERE OL_CODE = '01' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_ORG_LEVEL SET OL_SEQ_NO = 2 WHERE OL_CODE = '02' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_ORG_LEVEL SET OL_SEQ_NO = 3 WHERE OL_CODE = '03' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_ORG_LEVEL SET OL_SEQ_NO = 4 WHERE OL_CODE = '04' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " UPDATE PER_ORG_LEVEL SET OL_SEQ_NO = 5 WHERE OL_CODE = '05' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ORG_LEVEL (OL_CODE, OL_NAME, OL_ACTIVE, UPDATE_USER, UPDATE_DATE, OL_SEQ_NO)
							  VALUES ('06', '��ӡ����ӹѡ/�ͧ 3 �дѺ', 1, $SESS_USERID, '$UPDATE_DATE', 6) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ORG_LEVEL (OL_CODE, OL_NAME, OL_ACTIVE, UPDATE_USER, UPDATE_DATE, OL_SEQ_NO)
							  VALUES ('07', '��ӡ����ӹѡ/�ͧ 4 �дѺ', 1, $SESS_USERID, '$UPDATE_DATE', 7) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_ORG_LEVEL (OL_CODE, OL_NAME, OL_ACTIVE, UPDATE_USER, UPDATE_DATE, OL_SEQ_NO)
							  VALUES ('08', '��ӡ����ӹѡ/�ͧ 5 �дѺ', 1, $SESS_USERID, '$UPDATE_DATE', 8) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITION ADD ORG_ID_3 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITION ADD ORG_ID_3 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITION ADD ORG_ID_3 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITION ADD ORG_ID_4 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITION ADD ORG_ID_4 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITION ADD ORG_ID_4 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITION ADD ORG_ID_5 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITION ADD ORG_ID_5 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITION ADD ORG_ID_5 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_EMP ADD ORG_ID_3 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMP ADD ORG_ID_3 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_EMP ADD ORG_ID_3 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_EMP ADD ORG_ID_4 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMP ADD ORG_ID_4 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_EMP ADD ORG_ID_4 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_EMP ADD ORG_ID_5 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMP ADD ORG_ID_5 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_EMP ADD ORG_ID_5 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD ORG_ID_3 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD ORG_ID_3 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD ORG_ID_3 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD ORG_ID_4 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD ORG_ID_4 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD ORG_ID_4 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD ORG_ID_5 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD ORG_ID_5 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD ORG_ID_5 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_MOVE ADD ORG_ID_3 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_MOVE ADD ORG_ID_3 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_MOVE ADD ORG_ID_3 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_MOVE ADD ORG_ID_4 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_MOVE ADD ORG_ID_4 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_MOVE ADD ORG_ID_4 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_MOVE ADD ORG_ID_5 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_MOVE ADD ORG_ID_5 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_MOVE ADD ORG_ID_5 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMDTL ADD CMD_ORG6 VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL ADD CMD_ORG6 VARCHAR2(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMDTL ADD CMD_ORG7 VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL ADD CMD_ORG7 VARCHAR2(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMDTL ADD CMD_ORG8 VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL ADD CMD_ORG8 VARCHAR2(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ORDER_DTL ADD ORG_ID_3 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORDER_DTL ADD ORG_ID_3 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ORDER_DTL ADD ORG_ID_3 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ORDER_DTL ADD ORG_ID_4 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORDER_DTL ADD ORG_ID_4 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ORDER_DTL ADD ORG_ID_4 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ORDER_DTL ADD ORG_ID_5 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORDER_DTL ADD ORG_ID_5 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ORDER_DTL ADD ORG_ID_5 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " UPDATE PER_SALARYHIS SET SAH_EFFECTIVEDATE = LEFT(SAH_EFFECTIVEDATE, 10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_SALARYHIS SET SAH_EFFECTIVEDATE = SUBSTR(SAH_EFFECTIVEDATE, 1, 10) ";
			elseif($DPISDB=="mysql")
				$cmd = " UPDATE PER_SALARYHIS SET SAH_EFFECTIVEDATE = LEFT(SAH_EFFECTIVEDATE, 10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R006019.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 19, 'R0619 ���º��º�ѵ�ҡ������͹�Թ��͹��ºؤ�ŵ���к�����Ѻ�к�����', 'S', 'W', 'rpt_R006019.html', 0, 36, 
								  238, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 19, 'R0619 ���º��º�ѵ�ҡ������͹�Թ��͹��ºؤ�ŵ���к�����Ѻ�к�����', 'S', 'W', 'rpt_R006019.html', 0, 36, 
								  238, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'rpt_R009099.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 99, 'R0999 ��§ҹ��Ǫ���Ѵ��Шӻէ�����ҳ', 'S', 'W', 'rpt_R009099.html', 0, 36, 
								  303, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 99, 'R0999 ��§ҹ��Ǫ���Ѵ��Шӻէ�����ҳ', 'S', 'W', 'rpt_R009099.html', 0, 36, 
								  303, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " ALTER TABLE PER_POSITION DROP CONSTRAINT FK7_PER_POSITION ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITION ALTER CL_NAME VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITION MODIFY CL_NAME VARCHAR2(100) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITION MODIFY CL_NAME VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT CL_NAME FROM PER_CO_LEVEL WHERE CL_NAME = '��Ժѵԧҹ' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) 
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
								UPDATE_DATE, CL_SEQ_NO)
								VALUES ('��Ժѵԧҹ', 'O1', 'O1', 1, $SESS_USERID, '$UPDATE_DATE', 21) ";
			else
				$cmd = " UPDATE PER_CO_LEVEL SET CL_SEQ_NO =  21 WHERE CL_NAME = '��Ժѵԧҹ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT CL_NAME FROM PER_CO_LEVEL WHERE CL_NAME = '�ӹҭ�ҹ' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) 
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
								UPDATE_DATE, CL_SEQ_NO)
								VALUES ('�ӹҭ�ҹ', 'O2', 'O2', 1, $SESS_USERID, '$UPDATE_DATE', 22) ";
			else
				$cmd = " UPDATE PER_CO_LEVEL SET CL_SEQ_NO =  22 WHERE CL_NAME = '�ӹҭ�ҹ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT CL_NAME FROM PER_CO_LEVEL WHERE CL_NAME = '������' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) 
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
								UPDATE_DATE, CL_SEQ_NO)
								VALUES ('������', 'O3', 'O3', 1, $SESS_USERID, '$UPDATE_DATE', 23) ";
			else
				$cmd = " UPDATE PER_CO_LEVEL SET CL_SEQ_NO =  23 WHERE CL_NAME = '������' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT CL_NAME FROM PER_CO_LEVEL WHERE CL_NAME = '�ѡ�о����' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) 
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
								UPDATE_DATE, CL_SEQ_NO)
								VALUES ('�ѡ�о����', 'O4', 'O4', 1, $SESS_USERID, '$UPDATE_DATE', 24) ";
			else
				$cmd = " UPDATE PER_CO_LEVEL SET CL_SEQ_NO =  24 WHERE CL_NAME = '�ѡ�о����' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT CL_NAME FROM PER_CO_LEVEL WHERE CL_NAME = '��Ժѵԧҹ ���� �ӹҭ�ҹ' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) 
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
								UPDATE_DATE, CL_SEQ_NO)
								VALUES ('��Ժѵԧҹ ���� �ӹҭ�ҹ', 'O1', 'O2', 1, $SESS_USERID, '$UPDATE_DATE', 25) ";
			else
				$cmd = " UPDATE PER_CO_LEVEL SET CL_SEQ_NO =  25 WHERE CL_NAME = '��Ժѵԧҹ ���� �ӹҭ�ҹ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT CL_NAME FROM PER_CO_LEVEL WHERE CL_NAME = '��Ժѵԡ��' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) 
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
								UPDATE_DATE, CL_SEQ_NO)
								VALUES ('��Ժѵԡ��', 'K1', 'K1', 1, $SESS_USERID, '$UPDATE_DATE', 31) ";
			else
				$cmd = " UPDATE PER_CO_LEVEL SET CL_SEQ_NO =  31 WHERE CL_NAME = '��Ժѵԡ��' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT CL_NAME FROM PER_CO_LEVEL WHERE CL_NAME = '�ӹҭ���' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) 
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
								UPDATE_DATE, CL_SEQ_NO)
								VALUES ('�ӹҭ���', 'K2', 'K2', 1, $SESS_USERID, '$UPDATE_DATE', 32) ";
			else
				$cmd = " UPDATE PER_CO_LEVEL SET CL_SEQ_NO =  32 WHERE CL_NAME = '�ӹҭ���' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT CL_NAME FROM PER_CO_LEVEL WHERE CL_NAME = '�ӹҭ��þ����' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) 
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
								UPDATE_DATE, CL_SEQ_NO)
								VALUES ('�ӹҭ��þ����', 'K3', 'K3', 1, $SESS_USERID, '$UPDATE_DATE', 33) ";
			else
				$cmd = " UPDATE PER_CO_LEVEL SET CL_SEQ_NO =  33 WHERE CL_NAME = '�ӹҭ��þ����' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT CL_NAME FROM PER_CO_LEVEL WHERE CL_NAME = '����Ǫҭ' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) 
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
								UPDATE_DATE, CL_SEQ_NO)
								VALUES ('����Ǫҭ', 'K4', 'K4', 1, $SESS_USERID, '$UPDATE_DATE', 34) ";
			else
				$cmd = " UPDATE PER_CO_LEVEL SET CL_SEQ_NO =  34 WHERE CL_NAME = '����Ǫҭ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT CL_NAME FROM PER_CO_LEVEL WHERE CL_NAME = '�ç�س�ز�' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) 
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
								UPDATE_DATE, CL_SEQ_NO)
								VALUES ('�ç�س�ز�', 'K5', 'K5', 1, $SESS_USERID, '$UPDATE_DATE', 35) ";
			else
				$cmd = " UPDATE PER_CO_LEVEL SET CL_SEQ_NO =  35 WHERE CL_NAME = '�ç�س�ز�' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT CL_NAME FROM PER_CO_LEVEL WHERE CL_NAME = '��Ժѵԡ�� ���� �ӹҭ���' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) 
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
								UPDATE_DATE, CL_SEQ_NO)
								VALUES ('��Ժѵԡ�� ���� �ӹҭ���', 'K1', 'K2', 1, $SESS_USERID, '$UPDATE_DATE', 36) ";
			else
				$cmd = " UPDATE PER_CO_LEVEL SET CL_SEQ_NO =  36 WHERE CL_NAME = '��Ժѵԡ�� ���� �ӹҭ���' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT CL_NAME FROM PER_CO_LEVEL WHERE CL_NAME = '�ӹҭ��� ���� �ӹҭ��þ����' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) 
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
								UPDATE_DATE, CL_SEQ_NO)
								VALUES ('�ӹҭ��� ���� �ӹҭ��þ����', 'K2', 'K3', 1, $SESS_USERID, '$UPDATE_DATE', 37) ";
			else
				$cmd = " UPDATE PER_CO_LEVEL SET CL_SEQ_NO =  37 WHERE CL_NAME = '�ӹҭ��� ���� �ӹҭ��þ����' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT CL_NAME FROM PER_CO_LEVEL WHERE CL_NAME = '��Ժѵԡ�� ���� �ӹҭ��� ���� �ӹҭ��þ����' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) 
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
								UPDATE_DATE, CL_SEQ_NO)
								VALUES ('��Ժѵԡ�� ���� �ӹҭ��� ���� �ӹҭ��þ����', 'K1', 'K3', 1, $SESS_USERID, '$UPDATE_DATE', 38) ";
			else
				$cmd = " UPDATE PER_CO_LEVEL SET CL_SEQ_NO =  38 WHERE CL_NAME = '��Ժѵԡ�� ���� �ӹҭ��� ���� �ӹҭ��þ����' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT CL_NAME FROM PER_CO_LEVEL WHERE CL_NAME = '����Ǫҭ ���� �ç�س�ز�' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) 
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
								UPDATE_DATE, CL_SEQ_NO)
								VALUES ('����Ǫҭ ���� �ç�س�ز�', 'K4', 'K5', 1, $SESS_USERID, '$UPDATE_DATE', 39) ";
			else
				$cmd = " UPDATE PER_CO_LEVEL SET CL_SEQ_NO =  39 WHERE CL_NAME = '����Ǫҭ ���� �ç�س�ز�' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT CL_NAME FROM PER_CO_LEVEL WHERE CL_NAME = '����Ǫҭ ���� �ӹ�¡���٧' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) 
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
								UPDATE_DATE, CL_SEQ_NO)
								VALUES ('����Ǫҭ ���� �ӹ�¡���٧', 'K4', 'D2', 1, $SESS_USERID, '$UPDATE_DATE', 40) ";
			else
				$cmd = " UPDATE PER_CO_LEVEL SET CL_SEQ_NO =  40 WHERE CL_NAME = '����Ǫҭ ���� �ӹ�¡���٧' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT CL_NAME FROM PER_CO_LEVEL WHERE CL_NAME = '�ӹ�¡�õ�' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) 
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
								UPDATE_DATE, CL_SEQ_NO)
								VALUES ('�ӹ�¡�õ�', 'D1', 'D1', 1, $SESS_USERID, '$UPDATE_DATE', 41) ";
			else
				$cmd = " UPDATE PER_CO_LEVEL SET CL_SEQ_NO =  41 WHERE CL_NAME = '�ӹ�¡�õ�' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT CL_NAME FROM PER_CO_LEVEL WHERE CL_NAME = '�ӹ�¡���٧' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) 
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
								UPDATE_DATE, CL_SEQ_NO)
								VALUES ('�ӹ�¡���٧', 'D2', 'D2', 1, $SESS_USERID, '$UPDATE_DATE', 42) ";
			else
				$cmd = " UPDATE PER_CO_LEVEL SET CL_SEQ_NO =  42 WHERE CL_NAME = '�ӹ�¡���٧' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT CL_NAME FROM PER_CO_LEVEL WHERE CL_NAME = '�ӹ�¡�õ� ���� �ӹ�¡���٧' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) 
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
								UPDATE_DATE, CL_SEQ_NO)
								VALUES ('�ӹ�¡�õ� ���� �ӹ�¡���٧', 'D1', 'D2', 1, $SESS_USERID, '$UPDATE_DATE', 43) ";
			else
				$cmd = " UPDATE PER_CO_LEVEL SET CL_SEQ_NO =  43 WHERE CL_NAME = '�ӹ�¡�õ� ���� �ӹ�¡���٧' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT CL_NAME FROM PER_CO_LEVEL WHERE CL_NAME = '�����õ�' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) 
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
								UPDATE_DATE, CL_SEQ_NO)
								VALUES ('�����õ�', 'M1', 'M1', 1, $SESS_USERID, '$UPDATE_DATE', 51) ";
			else
				$cmd = " UPDATE PER_CO_LEVEL SET CL_SEQ_NO =  51 WHERE CL_NAME = '�����õ�' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT CL_NAME FROM PER_CO_LEVEL WHERE CL_NAME = '�������٧' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) 
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
								UPDATE_DATE, CL_SEQ_NO)
								VALUES ('�������٧', 'M2', 'M2', 1, $SESS_USERID, '$UPDATE_DATE', 52) ";
			else
				$cmd = " UPDATE PER_CO_LEVEL SET CL_SEQ_NO =  52 WHERE CL_NAME = '�������٧' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT CL_NAME FROM PER_CO_LEVEL WHERE CL_NAME = '�����õ� ���� �������٧' ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) 
				$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
								UPDATE_DATE, CL_SEQ_NO)
								VALUES ('�����õ� ���� �������٧', 'M1', 'M2', 1, $SESS_USERID, '$UPDATE_DATE', 53) ";
			else
				$cmd = " UPDATE PER_CO_LEVEL SET CL_SEQ_NO =  53 WHERE CL_NAME = '�����õ� ���� �������٧' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_ABSENTTYPE (AB_CODE, AB_NAME, AB_QUOTA, AB_COUNT, AB_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('16', '������Ѻ��õ�Ǩ���͡����������Ѻ����������', 0, 1, 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " SELECT COUNT(PER_ID) AS COUNT_DATA FROM PER_FAMILY ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA] + 0;

			$cmd = " SELECT FML_ID FROM PER_FAMILY ";
			$count_data = $db_dpis->send_cmd($cmd);

			if (!$count_data && $COUNT_DATA == 0) {
				$cmd = " DROP TABLE PER_FAMILY ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " DROP TABLE PER_WORKFLOW_FAMILY ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_FAMILY (
				FML_ID INTEGER NOT NULL,
				PER_ID INTEGER NOT NULL,
				FML_SEQ INTEGER2 NULL,
				FML_TYPE SINGLE NOT NULL,
				PN_CODE VARCHAR(3) NULL,
				FML_NAME VARCHAR(100) NULL,
				FML_SURNAME VARCHAR(100) NULL,
				FML_CARDNO VARCHAR(15) NULL,
				FML_GENDER SINGLE NULL,
				FML_BIRTHDATE VARCHAR(19) NULL,
				FML_ALIVE SINGLE NULL,
				RE_CODE VARCHAR(2) NULL,
				OC_CODE VARCHAR(2) NULL,
				OC_OTHER VARCHAR(100) NULL,
				FML_BY SINGLE NULL,
				FML_BY_OTHER VARCHAR(100) NULL,
				FML_DOCTYPE SINGLE NULL,
				FML_DOCNO VARCHAR(20) NULL,
				FML_DOCDATE VARCHAR(19) NULL,
				MR_CODE VARCHAR(2) NULL,
				MR_DOCTYPE SINGLE NULL,
				MR_DOCNO VARCHAR(20) NULL,
				MR_DOCDATE VARCHAR(19) NULL,
				MR_DOC_PV_CODE VARCHAR(10) NULL,
				PV_CODE VARCHAR(10) NULL,
				POST_CODE VARCHAR(5) NULL,
				FML_INCOMPETENT SINGLE NULL,
				IN_DOCTYPE SINGLE NULL,
				IN_DOCNO VARCHAR(20) NULL,
				IN_DOCDATE VARCHAR(19) NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,
				CONSTRAINT PK_PER_FAMILY PRIMARY KEY (FML_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_FAMILY (
				FML_ID NUMBER(10) NOT NULL,
				PER_ID NUMBER(10) NOT NULL,
				FML_SEQ NUMBER(3) NULL,
				FML_TYPE NUMBER(1) NOT NULL,
				PN_CODE VARCHAR2(3) NULL,
				FML_NAME VARCHAR2(100) NULL,
				FML_SURNAME VARCHAR2(100) NULL,
				FML_CARDNO VARCHAR2(15) NULL,
				FML_GENDER NUMBER(1) NULL,
				FML_BIRTHDATE VARCHAR2(19) NULL,
				FML_ALIVE NUMBER(1) NULL,
				RE_CODE VARCHAR2(2) NULL,
				OC_CODE VARCHAR2(2) NULL,
				OC_OTHER VARCHAR2(100) NULL,
				FML_BY NUMBER(1) NULL,
				FML_BY_OTHER VARCHAR2(100) NULL,
				FML_DOCTYPE NUMBER(1) NULL,
				FML_DOCNO VARCHAR2(20) NULL,
				FML_DOCDATE VARCHAR2(19) NULL,
				MR_CODE VARCHAR2(2) NULL,
				MR_DOCTYPE NUMBER(1) NULL,
				MR_DOCNO VARCHAR2(20) NULL,
				MR_DOCDATE VARCHAR2(19) NULL,
				MR_DOC_PV_CODE VARCHAR2(10) NULL,
				PV_CODE VARCHAR2(10) NULL,
				POST_CODE VARCHAR2(5) NULL,
				FML_INCOMPETENT NUMBER(1) NULL,
				IN_DOCTYPE NUMBER(1) NULL,
				IN_DOCNO VARCHAR2(20) NULL,
				IN_DOCDATE VARCHAR2(19) NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,
				CONSTRAINT PK_PER_FAMILY PRIMARY KEY (FML_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_FAMILY (
				FML_ID INTEGER(10) NOT NULL,
				PER_ID INTEGER(10) NOT NULL,
				FML_SEQ SMALLINT(3) NULL,
				FML_TYPE SMALLINT(1) NOT NULL,
				PN_CODE VARCHAR(3) NULL,
				FML_NAME VARCHAR(100) NULL,
				FML_SURNAME VARCHAR(100) NULL,
				FML_CARDNO VARCHAR(15) NULL,
				FML_GENDER SMALLINT(1) NULL,
				FML_BIRTHDATE VARCHAR(19) NULL,
				FML_ALIVE SMALLINT(1) NULL,
				RE_CODE VARCHAR(2) NULL,
				OC_CODE VARCHAR(2) NULL,
				OC_OTHER VARCHAR(100) NULL,
				FML_BY SMALLINT(1) NULL,
				FML_BY_OTHER VARCHAR(100) NULL,
				FML_DOCTYPE SMALLINT(1) NULL,
				FML_DOCNO VARCHAR(20) NULL,
				FML_DOCDATE VARCHAR(19) NULL,
				MR_CODE VARCHAR(2) NULL,
				MR_DOCTYPE SMALLINT(1) NULL,
				MR_DOCNO VARCHAR(20) NULL,
				MR_DOCDATE VARCHAR(19) NULL,
				MR_DOC_PV_CODE VARCHAR(10) NULL,
				PV_CODE VARCHAR(10) NULL,
				POST_CODE VARCHAR(5) NULL,
				FML_INCOMPETENT SMALLINT(1) NULL,
				IN_DOCTYPE SMALLINT(1) NULL,
				IN_DOCNO VARCHAR(20) NULL,
				IN_DOCDATE VARCHAR(19) NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,
				CONSTRAINT PK_PER_FAMILY PRIMARY KEY (FML_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_NAMEHIS FROM PER_NAMEHIS WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_NAMEHIS AS SELECT * FROM PER_NAMEHIS WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_NAMEHIS ADD CONSTRAINT PK_PER_WORKFLOW_NAMEHIS  
							  PRIMARY KEY (NH_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_NAMEHIS ADD NH_WF_STATUS VARCHAR(2) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_NAMEHIS ADD NH_WF_STATUS VARCHAR2(2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_EDUCATE FROM PER_EDUCATE WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_EDUCATE AS SELECT * FROM PER_EDUCATE WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_EDUCATE ADD CONSTRAINT PK_PER_WORKFLOW_EDUCATE  
							  PRIMARY KEY (EDU_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_EDUCATE ADD EDU_WF_STATUS VARCHAR(2) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_EDUCATE ADD EDU_WF_STATUS VARCHAR2(2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_MARRHIS FROM PER_MARRHIS WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_MARRHIS AS SELECT * FROM PER_MARRHIS WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_MARRHIS ADD CONSTRAINT PK_PER_WORKFLOW_MARRHIS  
							  PRIMARY KEY (MAH_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_MARRHIS ADD MAH_WF_STATUS VARCHAR(2) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_MARRHIS ADD MAH_WF_STATUS VARCHAR2(2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_DECORATEHIS FROM PER_DECORATEHIS WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_DECORATEHIS AS SELECT * FROM PER_DECORATEHIS WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_DECORATEHIS ADD CONSTRAINT PK_PER_WORKFLOW_DECORATEHIS  
							  PRIMARY KEY (DEH_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_DECORATEHIS ADD DEH_WF_STATUS VARCHAR(2) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_DECORATEHIS ADD DEH_WF_STATUS VARCHAR2(2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_TRAINING FROM PER_TRAINING WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_TRAINING AS SELECT * FROM PER_TRAINING WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_TRAINING ADD CONSTRAINT PK_PER_WORKFLOW_TRAINING  
							  PRIMARY KEY (TRN_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_TRAINING ADD TRN_WF_STATUS VARCHAR(2) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_TRAINING ADD TRN_WF_STATUS VARCHAR2(2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_ADDRESS FROM PER_ADDRESS WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_ADDRESS AS SELECT * FROM PER_ADDRESS WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_ADDRESS ADD CONSTRAINT PK_PER_WORKFLOW_ADDRESS  
							  PRIMARY KEY (ADR_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_ADDRESS ADD ADR_WF_STATUS VARCHAR(2) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_ADDRESS ADD ADR_WF_STATUS VARCHAR2(2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_REWARDHIS FROM PER_REWARDHIS WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_REWARDHIS AS SELECT * FROM PER_REWARDHIS WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_REWARDHIS ADD CONSTRAINT PK_PER_WORKFLOW_REWARDHIS  
							  PRIMARY KEY (REH_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_REWARDHIS ADD REH_WF_STATUS VARCHAR(2) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_REWARDHIS ADD REH_WF_STATUS VARCHAR2(2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_POSITIONHIS FROM PER_POSITIONHIS WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_POSITIONHIS AS SELECT * FROM PER_POSITIONHIS WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD CONSTRAINT PK_PER_WORKFLOW_POSITIONHIS  
							  PRIMARY KEY (POH_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_WF_STATUS VARCHAR(2) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_WF_STATUS VARCHAR2(2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_LAST_POSITION CHAR(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_CMD_SEQ INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_CMD_SEQ NUMBER(6) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_CMD_SEQ INTEGER(6) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ALTER POH_SEQ_NO INTEGER2 ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS MODIFY POH_SEQ_NO NUMBER(5) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ALTER POH_SEQ_NO SMALLINT(5) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ALTER POH_REMARK NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS MODIFY POH_REMARK NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS MODIFY POH_REMARK NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_ISREAL CHAR(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_WORKFLOW_POSITIONHIS SET POH_ISREAL = 'Y' WHERE POH_ISREAL IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_ORG_DOPA_CODE VARCHAR(20) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_ORG_DOPA_CODE VARCHAR2(20) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD ES_CODE VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD ES_CODE VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
 			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_LEVEL_NO VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_LEVEL_NO VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_WORKFLOW_POSITIONHIS SET POH_LEVEL_NO = LEVEL_NO WHERE POH_LEVEL_NO IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD TP_CODE VARCHAR(10) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD TP_CODE VARCHAR2(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_SALARYHIS FROM PER_SALARYHIS WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_SALARYHIS AS SELECT * FROM PER_SALARYHIS WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD CONSTRAINT PK_PER_WORKFLOW_SALARYHIS  
							  PRIMARY KEY (SAH_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SAH_SALARY_MIDPOINT NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SAH_SALARY_MIDPOINT NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SAH_SALARY_MIDPOINT DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SAH_KF_YEAR VARCHAR(4) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SAH_KF_YEAR VARCHAR2(4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SAH_KF_CYCLE SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SAH_KF_CYCLE NUMBER(1) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SAH_KF_CYCLE SMALLINT(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SAH_TOTAL_SCORE NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SAH_TOTAL_SCORE NUMBER(5,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SAH_TOTAL_SCORE DECIMAL(5,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ALTER SAH_PERCENT_UP NUMBER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS MODIFY SAH_PERCENT_UP NUMBER(7,4) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS MODIFY SAH_PERCENT_UP DECIMAL(7,4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SAH_LAST_SALARY CHAR(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SM_CODE VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SM_CODE VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ALTER SAH_SEQ_NO INTEGER2 ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS MODIFY SAH_SEQ_NO NUMBER(5) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ALTER SAH_SEQ_NO SMALLINT(5) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SAH_ORG_DOPA_CODE VARCHAR(20) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SAH_ORG_DOPA_CODE VARCHAR2(20) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SAH_OLD_SALARY NUMBER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SAH_OLD_SALARY NUMBER(16,2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SAH_OLD_SALARY DECIMAL(16,2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SAH_CMD_SEQ INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SAH_CMD_SEQ NUMBER(6) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SAH_CMD_SEQ INTEGER(6) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SAH_WF_STATUS VARCHAR(2) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SAH_WF_STATUS VARCHAR2(2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_ABILITY FROM PER_ABILITY WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_ABILITY AS SELECT * FROM PER_ABILITY WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_ABILITY ADD CONSTRAINT PK_PER_WORKFLOW_ABILITY  
							  PRIMARY KEY (ABI_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_ABILITY ADD ABI_WF_STATUS VARCHAR(2) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_ABILITY ADD ABI_WF_STATUS VARCHAR2(2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_SPECIAL_SKILL FROM PER_SPECIAL_SKILL WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_SPECIAL_SKILL AS SELECT * FROM PER_SPECIAL_SKILL WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_SPECIAL_SKILL ADD CONSTRAINT PK_PER_WORKFLOW_SPECIAL_SKILL  
							  PRIMARY KEY (SPS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SPECIAL_SKILL ADD SPS_WF_STATUS VARCHAR(2) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SPECIAL_SKILL ADD SPS_WF_STATUS VARCHAR2(2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_ABSENTHIS FROM PER_ABSENTHIS WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_ABSENTHIS AS SELECT * FROM PER_ABSENTHIS WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_ABSENTHIS ADD CONSTRAINT PK_PER_WORKFLOW_ABSENTHIS  
							  PRIMARY KEY (ABS_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_ABSENTHIS ADD ABS_WF_STATUS VARCHAR(2) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_ABSENTHIS ADD ABS_WF_STATUS VARCHAR2(2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_PUNISHMENT FROM PER_PUNISHMENT WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_PUNISHMENT AS SELECT * FROM PER_PUNISHMENT WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_PUNISHMENT ADD CONSTRAINT PK_PER_WORKFLOW_PUNISHMENT  
							  PRIMARY KEY (PUN_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_PUNISHMENT ADD PUN_WF_STATUS VARCHAR(2) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_PUNISHMENT ADD PUN_WF_STATUS VARCHAR2(2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_SERVICEHIS FROM PER_SERVICEHIS WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_SERVICEHIS AS SELECT * FROM PER_SERVICEHIS WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_SERVICEHIS ADD CONSTRAINT PK_PER_WORKFLOW_SERVICEHIS  
							  PRIMARY KEY (SRH_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SERVICEHIS ADD SRH_WF_STATUS VARCHAR(2) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SERVICEHIS ADD SRH_WF_STATUS VARCHAR2(2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_TIMEHIS FROM PER_TIMEHIS WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_TIMEHIS AS SELECT * FROM PER_TIMEHIS WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_TIMEHIS ADD CONSTRAINT PK_PER_WORKFLOW_TIMEHIS  
							  PRIMARY KEY (TIMEH_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_TIMEHIS ADD TIMEH_WF_STATUS VARCHAR(2) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_TIMEHIS ADD TIMEH_WF_STATUS VARCHAR2(2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " SELECT * INTO PER_WORKFLOW_FAMILY FROM PER_FAMILY WHERE UPDATE_DATE = 'x' ";
			elseif($DPISDB=="oci8" || $DPISDB=="mysql") 
				$cmd = " CREATE TABLE PER_WORKFLOW_FAMILY AS SELECT * FROM PER_FAMILY WHERE UPDATE_DATE = 'x' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_WORKFLOW_FAMILY ADD CONSTRAINT PK_PER_WORKFLOW_FAMILY  
							  PRIMARY KEY (FML_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_FAMILY ADD FML_WF_STATUS VARCHAR(2) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_FAMILY ADD FML_WF_STATUS VARCHAR2(2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'master_table_salary_movment.html?table=PER_SALARY_MOVMENT' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 8, 'M0408 �ӹǹ����Թ��͹', 'S', 'W', 'master_table_salary_movment.html?table=PER_SALARY_MOVMENT', 0, 9, 
								  297, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 8, 'M0408 �ӹǹ����Թ��͹', 'S', 'W', 'master_table_salary_movment.html?table=PER_SALARY_MOVMENT', 0, 9, 
								  297, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
							  VALUES ('1', '���觢��', '����͹����Թ��͹��ШӤ��觻� 0.5 ���', 0.5, 1, $SESS_USERID, '$UPDATE_DATE', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
							  VALUES ('2', '˹�觢��', '����͹����Թ��͹��ШӤ��觻� 1.0 ��� �óվ����', 1, 1, $SESS_USERID, '$UPDATE_DATE', 2) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
							  VALUES ('3', '˹�觢�鹤���', '����͹����Թ��͹��ШӤ��觻� 1.5 ��� �óվ����', 1.5, 1, $SESS_USERID, '$UPDATE_DATE', 3) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
							  VALUES ('4', '�ͧ���', '����͹����Թ��͹��ШӤ��觻� 2.0 ��� �óվ����', 2, 1, $SESS_USERID, '$UPDATE_DATE', 4) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
							  VALUES ('5', '������ (2%)', NULL, 0.5, 1, $SESS_USERID, '$UPDATE_DATE', 5) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
							  VALUES ('6', 'Ŵ���觢��', 'ŧ��Ŵ����Թ��͹ 0.5 ���', -0.5, 1, $SESS_USERID, '$UPDATE_DATE', 6) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
							  VALUES ('7', 'Ŵ˹�觢��', 'ŧ��Ŵ����Թ��͹ 1.0 ���', -1, 1, $SESS_USERID, '$UPDATE_DATE', 7) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
							  VALUES ('8', '�Ѵ�Թ��͹', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE', 8) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
							  VALUES ('9', '�Ѵ�͹', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE', 9) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
							  VALUES ('10', '������͹���', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE', 10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
							  VALUES ('11', '����͹�дѺ', NULL', 0, 1, $SESS_USERID, '$UPDATE_DATE', 11) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
							  VALUES ('12', '-', '��Ѻ�ѵ���Թ��͹��� ��� �.�.�. ���', 0, 1, $SESS_USERID, '$UPDATE_DATE', 12) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
							  VALUES ('14', '��Ѻ�ز�', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE', 14) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
							  VALUES ('15', '��è�', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE', 15) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
							  VALUES ('16', '�Ѻ�͹', NULL, 0, 1, $SESS_USERID, '$UPDATE_DATE', 16) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
							  VALUES ('17', '������ (4%)', NULL, 1, 1, $SESS_USERID, '$UPDATE_DATE', 17) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
							  VALUES ('18', '������ (6%)', NULL, 1.5, 1, $SESS_USERID, '$UPDATE_DATE', 18) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_SALARY_MOVMENT (SM_CODE, SM_NAME, SM_REMARK, SM_FACTOR, SM_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, SM_SEQ_NO)
							  VALUES ('19', '��Ѻ��鹵��', '��Ѻ�Թ��͹����ѧ���֧��鹵�Ӣͧ�дѺ����ѭ���Թ��͹', 0, 1, $SESS_USERID, '$UPDATE_DATE', 19) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_positionhis_check.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 WHERE LINKTO_WEB = 'personal_salaryhis_check.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'personal_check.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 23, 'P0123 ��Ǩ�ͺ����ѵԡ�ô�ç���˹�/����Ѻ�Թ��͹', 'S', 'W', 'personal_check.html', 0, 35, 241, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 23, 'P0123 ��Ǩ�ͺ����ѵԡ�ô�ç���˹�/����Ѻ�Թ��͹', 'S', 'W', 'personal_check.html', 0, 35, 241, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " DELETE FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE MENU_LABEL = 'P0124 ���һ���ѵԺؤ�ҡ�' AND LINKTO_WEB = 'personal_check.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'personal_master_desc_excel.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 24, 'P0124 ���һ���ѵԺؤ�ҡ�', 'S', 'W', 'personal_master_desc_excel.html', 0, 35, 241, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 24, 'P0124 ���һ���ѵԺؤ�ҡ�', 'S', 'W', 'personal_master_desc_excel.html', 0, 35, 241, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_EDUCATE ADD CT_CODE_EDU VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_EDUCATE ADD CT_CODE_EDU VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_EDUCATE, PER_INSTITUTE SET PER_EDUCATE.CT_CODE_EDU = 
								  PER_INSTITUTE.CT_CODE WHERE PER_INSTITUTE.INS_CODE = PER_EDUCATE.INS_CODE AND 
								  PER_EDUCATE.INS_CODE IS NOT NULL AND PER_EDUCATE.CT_CODE_EDU IS NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_EDUCATE A SET A.CT_CODE_EDU = 
								  (SELECT B.CT_CODE FROM PER_INSTITUTE B WHERE A.INS_CODE = B.INS_CODE) 
								  WHERE A.INS_CODE IS NOT NULL AND A.CT_CODE_EDU IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_EDUCATE SET CT_CODE_EDU = '140' WHERE CT_CODE_EDU IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_EDUCATE ADD CT_CODE_EDU VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_EDUCATE ADD CT_CODE_EDU VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_WORKFLOW_EDUCATE, PER_INSTITUTE SET PER_WORKFLOW_EDUCATE.CT_CODE_EDU = 
								  PER_INSTITUTE.CT_CODE WHERE PER_INSTITUTE.INS_CODE = PER_WORKFLOW_EDUCATE.INS_CODE AND 
								  PER_WORKFLOW_EDUCATE.INS_CODE IS NOT NULL AND PER_WORKFLOW_EDUCATE.CT_CODE_EDU IS NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_WORKFLOW_EDUCATE A SET A.CT_CODE_EDU = 
								  (SELECT B.CT_CODE FROM PER_INSTITUTE B WHERE A.INS_CODE = B.INS_CODE) 
								  WHERE A.INS_CODE IS NOT NULL AND A.CT_CODE_EDU IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_WORKFLOW_EDUCATE SET CT_CODE_EDU = '140' WHERE CT_CODE_EDU IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('523', '����觵Ѵ�͹�ѵ���Թ��͹', 1, $SESS_USERID, '$UPDATE_DATE', 23) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('531', '��С��', 1, $SESS_USERID, '$UPDATE_DATE', 31) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMGROUP (COM_GROUP, CG_NAME, CG_ACTIVE, UPDATE_USER, UPDATE_DATE, CG_SEQ_NO)
							  VALUES ('532', '�ѭ��', 1, $SESS_USERID, '$UPDATE_DATE', 32) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_SEQ_NO)
							VALUES ('5230', '��. 23', '����觵Ѵ�͹�ѵ���Թ��͹', '523', 98) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 set MENU_LABEL = 'P1112 �к����ʹ�Ⱦ�ѡ�ҹ�Ҫ��� (GEIS)' WHERE LINKTO_WEB = 'rpt_epis.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV1 WHERE LINKTO_WEB = 'set_initial_site.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV1 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
								  UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 14, 'C14 ��˹��ٻẺ����͡Ẻ˹�Ҩ�', 'S', 'W', 'set_initial_site.html', 0, 1, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV1 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, CREATE_DATE, CREATE_BY, 
								  UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 14, 'C14 ��˹��ٻẺ����͡Ẻ˹�Ҩ�', 'S', 'W', 'set_initial_site.html', 0, 1, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_CONTACT_COUNT INTEGER2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_CONTACT_COUNT NUMBER(2) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_CONTACT_COUNT SMALLINT(2) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_DISABILITY SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_DISABILITY NUMBER(1) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_DISABILITY SMALLINT(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_PERSONAL SET PER_DISABILITY =  1 WHERE PER_DISABILITY IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 set MENU_LABEL = 'P0110 ˹ѧ����Ѻ�ͧ/����ѵ�' WHERE MENU_LABEL = 'P0110 ˹ѧ����Ѻ�ͧ' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'master_table_pos_mgtsalary.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 5, 'S0205 �Թ��Шӵ��˹�', 'S', 'W', 'master_table_pos_mgtsalary.html', 0, 34, 254, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 5, 'S0205 �Թ��Шӵ��˹�', 'S', 'W', 'master_table_pos_mgtsalary.html', 0, 34, 254, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'per_namecard_design.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 2, 'P1002 �͡Ẻ����ѵ�', 'S', 'W', 'per_namecard_design.html', 0, 35, 250, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 2, 'P1002 �͡Ẻ����ѵ�', 'S', 'W', 'per_namecard_design.html', 0, 35, 250, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'per_namecard_print.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 3, 'P1003 ��������ѵ�', 'S', 'W', 'per_namecard_print.html', 0, 35, 250, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 3, 'P1003 ��������ѵ�', 'S', 'W', 'per_namecard_print.html', 0, 35, 250, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'per_offno_print.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 4, 'P1004 �����ѵâ���Ҫ���', 'S', 'W', 'per_offno_print.html', 0, 35, 250, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 4, 'P1004 �����ѵâ���Ҫ���', 'S', 'W', 'per_offno_print.html', 0, 35, 250, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}
	
			$cmd = " update PER_CONTROL set CTRL_ALTER = 9 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end if($CTRL_ALTER < 8) 

		if($CTRL_ALTER < 10) {
			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_NAMECARD(
				NC_ID INTEGER NOT NULL,
				NC_NAME VARCHAR(100) NOT NULL,
				NC_UNIT VARCHAR(2) NOT NULL,	
				NC_W INTEGER2 NULL,
				NC_H INTEGER2 NULL,
				NC_FORM MEMO NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_NAMECARD PRIMARY KEY (NC_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_NAMECARD(
				NC_ID NUMBER(10) NOT NULL,
				NC_NAME VARCHAR2(100) NOT NULL,
				NC_UNIT VARCHAR2(2) NOT NULL,	
				NC_W NUMBER(5) NULL,
				NC_H NUMBER(5) NULL,
				NC_FORM VARCHAR2(4000) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_NAMECARD PRIMARY KEY (NC_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_NAMECARD(
				NC_ID INTEGER(10) NOT NULL,
				NC_NAME VARCHAR(100) NOT NULL,
				NC_UNIT VARCHAR(2) NOT NULL,	
				NC_W SMALLINT(5) NULL,
				NC_H SMALLINT(5) NULL,
				NC_FORM TEXT NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_NAMECARD PRIMARY KEY (NC_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_NAMECARD (NC_ID, NC_NAME, NC_UNIT, NC_W, NC_H, NC_FORM, UPDATE_USER, UPDATE_DATE)
							VALUES (1, '����ѵ�Ẻ��� 1', 'mm', 89, 55, 'variable,pername1,2,3,30,angsa,,16,L,45,3D,AB,|variable,perposline,2,9,30,angsa,,12,L,88,83,F3,|variable,org,2,14,30,angsa,,12,L,EC,5D,5D,|image,images/top_left_new.jpg,69,1,18,18|variable,allphone,2,45,86,angsa,,10,C,FA,05,05,|variable,email,2,49,86,angsa,,10,C,16,0C,EA,|rect,box1,1,1,88,54,F8,A5,F1,.1,|variable,address2,2,41,86,angsa,,10,C,00,00,00,', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_NAMECARD (NC_ID, NC_NAME, NC_UNIT, NC_W, NC_H, NC_FORM, UPDATE_USER, UPDATE_DATE)
							VALUES (2, '����ѵ�Ẻ��� 2', 'mm', 89, 55, 'variable,pername1,45,2,43,angsa,,16,R,00,00,00,|variable,perposline,45,8,43,angsa,,11,R,00,00,00,|variable,org,45,13,43,angsa,,10,R,00,00,00,|image,images/top_left_new.jpg,2,2,16,16|variable,address2,58,41,30,angsa,,11,R,00,00,00,lines|text,Office :,2,41,10,angsa,,11,L,7C,7C,E9|variable,officephone,10,41,30,angsa,,11,L,07,07,91,|text,Mobile :,2,45,30,angsa,,11,L,7C,7C,E9|variable,mobilephone,10,45,30,angsa,,11,L,07,07,91,|text,Email :,2,49,30,angsa,,11,L,7C,7C,E9|variable,email,10,49,30,angsa,,11,L,07,07,91,', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_NAMECARD (NC_ID, NC_NAME, NC_UNIT, NC_W, NC_H, NC_FORM, UPDATE_USER, UPDATE_DATE)
							VALUES (3, '����ѵ�Ẻ��� 3', 'mm', 89, 55, 'variable,pername1,2,5,30,angsa,,14,L,00,00,00,|image,images/top_left_new.jpg,73,2,15,15|variable,org,50,17,38,angsa,,12,R,06,B8,99,|variable,address2,2,40,35,angsa,,12,L,00,00,00,lines|variable,allphone,50,50,38,angsa,,10,R,66,8B,36,|variable,email,50,46,38,angsa,,12,R,16,67,06,|variable,perposline,2,10,30,angsa,,12,L,16,67,06,|line,line1,2,10,70,10,F8,AA,AA,.1|rect,box1,10,20,35,32,5C,F6,6E,0,|rect,box2,27,28,65,37,CA,F0,18,0,', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_NAMECARD (NC_ID, NC_NAME, NC_UNIT, NC_W, NC_H, NC_FORM, UPDATE_USER, UPDATE_DATE)
							VALUES (4, '����ѵ�Ẻ��� 4', 'mm', 89, 55, 'variable,pername1,17,4,30,angsa,,16,L,00,00,00,|image,images/top_left_new.jpg,2,2,15,15|variable,perposline,17,10,40,angsa,,12,L,00,00,00,|variable,org,48,38,40,angsa,,11,R,00,00,00,|variable,email,48,42,40,angsa,,11,R,00,00,00,|variable,allphone,48,46,40,angsa,,11,R,00,00,00,|variable,address2,48,50,40,angsa,,11,R,00,00,00,', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_NAMECARD (NC_ID, NC_NAME, NC_UNIT, NC_W, NC_H, NC_FORM, UPDATE_USER, UPDATE_DATE)
							VALUES (5, '����ѵ�Ẻ��� 5', 'mm', 89, 55, 'variable,pername1,2,22,87,angsa,,16,C,00,00,00,|variable,perposline,2,27,87,angsa,,12,C,00,00,00,|variable,org,48,7,40,angsa,,12,R,00,00,00,|image,images/top_left_new.jpg,2,2,12,12|text,�ӹѡ�ҹ �.�.,48,3,40,angsa,,12,R,00,00,00|variable,address2,2,45,87,angsa,,11,C,00,00,00,|variable,allphone,2,49,87,angsa,,11,C,00,00,00,|variable,email,2,31,87,angsa,,11,C,00,00,00,|rect,box1,2,44,87,53,FE,E4,89,0,', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_PERSONAL_NAMECARD(
				PER_ID INTEGER NOT NULL,
				NC_ID INTEGER NOT NULL,
				NC_PER_FORM MEMO NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_PERSONAL_NAMECARD PRIMARY KEY (PER_ID, NC_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_PERSONAL_NAMECARD(
				PER_ID NUMBER(10) NOT NULL,
				NC_ID NUMBER(10) NOT NULL,
				NC_PER_FORM VARCHAR2(4000) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_PERSONAL_NAMECARD PRIMARY KEY (PER_ID, NC_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_PERSONAL_NAMECARD(
				PER_ID INTEGER(10) NOT NULL,
				NC_ID INTEGER(10) NOT NULL,
				NC_PER_FORM TEXT NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_PERSONAL_NAMECARD PRIMARY KEY (PER_ID, NC_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_FAMILY ON PER_FAMILY (PER_ID, FML_TYPE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT max(FML_ID) as MAX_ID FROM PER_FAMILY ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$MAX_ID = $data[MAX_ID] + 1;

			$cmd = " SELECT PER_ID, PN_CODE_F, PER_FATHERNAME, PER_FATHERSURNAME, 
							PN_CODE_M, PER_MOTHERNAME, PER_MOTHERSURNAME  
							FROM PER_PERSONAL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PER_ID = $data[PER_ID];
				$PN_CODE_F = trim($data[PN_CODE_F]);
				$PER_FATHERNAME = trim($data[PER_FATHERNAME]);
				$PER_FATHERSURNAME = trim($data[PER_FATHERSURNAME]);
				$PN_CODE_M = trim($data[PN_CODE_M]);
				$PER_MOTHERNAME = trim($data[PER_MOTHERNAME]);
				$PER_MOTHERSURNAME = trim($data[PER_MOTHERSURNAME]);

				$cmd = " SELECT FML_ID FROM PER_FAMILY WHERE PER_ID = $PER_ID AND FML_TYPE = 1 ";
				$count_data = $db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				if (!$count_data && $PER_FATHERNAME) {
					$cmd = " INSERT INTO PER_FAMILY (FML_ID, PER_ID, FML_TYPE, PN_CODE, FML_NAME, FML_SURNAME, 
									FML_GENDER, FML_ALIVE, RE_CODE, MR_CODE, UPDATE_USER, UPDATE_DATE) 
									VALUES ($MAX_ID, $PER_ID, 1, '$PN_CODE_F', '$PER_FATHERNAME', '$PER_FATHERSURNAME', 
									1, 1, '00', 2, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$MAX_ID++;
				} // end if

				$cmd = " SELECT FML_ID FROM PER_FAMILY WHERE PER_ID = $PER_ID AND FML_TYPE = 2 ";
				$count_data = $db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				if (!$count_data && $PER_MOTHERNAME) {
					$cmd = " INSERT INTO PER_FAMILY (FML_ID, PER_ID, FML_TYPE, PN_CODE, FML_NAME, FML_SURNAME, 
									FML_GENDER, FML_ALIVE, RE_CODE, MR_CODE, UPDATE_USER, UPDATE_DATE) 
									VALUES ($MAX_ID, $PER_ID, 2, '$PN_CODE_M', '$PER_MOTHERNAME', '$PER_MOTHERSURNAME', 
									2, 1, '00', 2, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$MAX_ID++;
				} // end if
			} // end while

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_HEIR ALTER HEIR_BIRTHDAY NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_HEIR MODIFY HEIR_BIRTHDAY NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_HEIR MODIFY HEIR_BIRTHDAY NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_SERVICEHIS DROP CONSTRAINT FK6_PER_SERVICEHIS ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMMAND ALTER COM_NO VARCHAR(200) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMMAND MODIFY COM_NO VARCHAR2(200) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMMAND MODIFY COM_NO VARCHAR(200) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT PV_CODE, PV_NAME FROM PER_PROVINCE WHERE PV_ACTIVE = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while ( $data = $db_dpis->get_array() ) {
				$PV_CODE = trim($data[PV_CODE]);
				$PV_NAME = trim($data[PV_NAME]);

				$cmd = " SELECT ORG_ID FROM PER_ORG WHERE ORG_NAME LIKE '%$PV_NAME%' AND PV_CODE IS NULL ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				while ( $data1 = $db_dpis1->get_array() ) {
					$data1 = $db_dpis1->get_array();
					$ORG_ID = $data1[ORG_ID];
					$cmd = " UPDATE PER_ORG SET PV_CODE = '$PV_CODE' WHERE ORG_ID = $ORG_ID OR ORG_ID_REF = $ORG_ID ";
					$db_dpis2->send_cmd($cmd);
				} // end while

				$cmd = " SELECT ORG_ID FROM PER_ORG_ASS WHERE ORG_NAME LIKE '%$PV_NAME%' AND PV_CODE IS NULL ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				while ( $data1 = $db_dpis1->get_array() ) {
					$data1 = $db_dpis1->get_array();
					$ORG_ID = $data1[ORG_ID];
					$cmd = " UPDATE PER_ORG_ASS SET PV_CODE = '$PV_CODE' WHERE ORG_ID = $ORG_ID OR ORG_ID_REF = $ORG_ID ";
					$db_dpis2->send_cmd($cmd);
				} // end while
			} // end while

			$cmd = " SELECT max(ADR_ID) as MAX_ID FROM PER_ADDRESS ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$MAX_ID = $data[MAX_ID] + 1;

			$cmd = " SELECT PER_ID, PER_ADD1, PER_ADD2 FROM PER_PERSONAL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PER_ID = $data[PER_ID];
				$PER_ADD1 = trim($data[PER_ADD1]);
				$PER_ADD2 = trim($data[PER_ADD2]);

				$cmd = " SELECT ADR_ID FROM PER_ADDRESS WHERE PER_ID = $PER_ID AND ADR_TYPE = 1 ";
				$count_data = $db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				if (!$count_data && $PER_ADD2) {
					$cmd = " INSERT INTO PER_ADDRESS (ADR_ID, PER_ID, ADR_TYPE, ADR_REMARK, UPDATE_USER, UPDATE_DATE) 
									VALUES ($MAX_ID, $PER_ID, 1, '$PER_ADD2', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$MAX_ID++;
				} // end if

				$cmd = " SELECT ADR_ID FROM PER_ADDRESS WHERE PER_ID = $PER_ID AND ADR_TYPE = 2 ";
				$count_data = $db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				if (!$count_data && $PER_ADD1) {
					$cmd = " INSERT INTO PER_ADDRESS (ADR_ID, PER_ID, ADR_TYPE, ADR_REMARK, UPDATE_USER, UPDATE_DATE) 
									VALUES ($MAX_ID, $PER_ID, 2, '$PER_ADD1', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$MAX_ID++;
				} // end if
			} // end while

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '�2/˹.' WHERE LEVEL_NO = '�3' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '�2/˹.' WHERE LEVEL_NO = '�3' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '�3/˹.' WHERE LEVEL_NO = '�5' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '�4/˹.' WHERE LEVEL_NO = '�7' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '�2/˹.' WHERE LEVEL_NO = '�3' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '�3/˹.' WHERE LEVEL_NO = '�5' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '�4/˹.' WHERE LEVEL_NO = '�7' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '�2/˹.' WHERE LEVEL_NO = '�3' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET LEVEL_SHORTNAME = '�3/˹.' WHERE LEVEL_NO = '�5' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = ' 1' WHERE LEVEL_NO = '01' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = ' 2' WHERE LEVEL_NO = '02' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = ' 3' WHERE LEVEL_NO = '03' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = ' 4' WHERE LEVEL_NO = '04' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = ' 5' WHERE LEVEL_NO = '05' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = ' 6' WHERE LEVEL_NO = '06' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = ' 7' WHERE LEVEL_NO = '07' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = ' 8' WHERE LEVEL_NO = '08' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = ' 9' WHERE LEVEL_NO = '09' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = ' 10' WHERE LEVEL_NO = '10' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = ' 11' WHERE LEVEL_NO = '11' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = '��' WHERE LEVEL_NO = 'D1' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = '�٧' WHERE LEVEL_NO = 'D2' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = '��' WHERE LEVEL_NO = 'M1' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LEVEL SET POSITION_LEVEL = '�٧' WHERE LEVEL_NO = 'M2' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET MENU_ORDER = 5, 
							  MENU_LABEL = 'S0205 �Թ��Шӵ��˹�' WHERE LINKTO_WEB = 'master_table_pos_mgtsalary.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'master_table_temp_pos_group.html?table=PER_TEMP_POS_GROUP' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 16, 'M0316 ��Ǵ���˹��١��ҧ���Ǥ���', 'S', 'W', 'master_table_temp_pos_group.html?table=PER_TEMP_POS_GROUP', 0, 9, 295, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 16, 'M0316 ��Ǵ���˹��١��ҧ���Ǥ���', 'S', 'W', 'master_table_temp_pos_group.html?table=PER_TEMP_POS_GROUP', 0, 9, 295, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'master_table_temp_pos_name.html?table=PER_TEMP_POS_NAME' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 17, 'M0317 ���͵��˹��١��ҧ���Ǥ���', 'S', 'W', 'master_table_temp_pos_name.html?table=PER_TEMP_POS_NAME', 0, 9, 295, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 17, 'M0317 ���͵��˹��١��ҧ���Ǥ���', 'S', 'W', 'master_table_temp_pos_name.html?table=PER_TEMP_POS_NAME', 0, 9, 295, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'master_table_pos_temp.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 4, 'S0204 ���˹��١��ҧ���Ǥ���', 'S', 'W', 'master_table_pos_temp.html', 0, 34, 254, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 4, 'S0204 ���˹��١��ҧ���Ǥ���', 'S', 'W', 'master_table_pos_temp.html', 0, 34, 254, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_SLIP(
				SLIP_ID INTEGER NOT NULL,
				PER_ID INTEGER NULL,
				SLIP_YEAR VARCHAR(4) NOT NULL,
				SLIP_MONTH VARCHAR(2) NOT NULL,	
				PER_CARDNO VARCHAR(13) NOT NULL,
				PN_NAME VARCHAR(50) NULL,	
				PER_NAME VARCHAR(30) NULL,	
				PER_SURNAME VARCHAR(40) NULL,	
				DEPARTMENT_NAME VARCHAR(80) NULL,	
				ORG_NAME VARCHAR(80) NULL,	
				BANK_CODE VARCHAR(2) NULL,	
				BANK_NAME VARCHAR(50) NULL,	
				BRANCH_CODE VARCHAR(4) NULL,	
				BRANCH_NAME VARCHAR(50) NULL,	
				PER_BANK_ACCOUNT VARCHAR(15) NULL,	
				INCOME_01 NUMBER NULL,
				INCOME_02 NUMBER NULL,
				INCOME_03 NUMBER NULL,
				INCOME_04 NUMBER NULL,
				INCOME_05 NUMBER NULL,
				INCOME_06 NUMBER NULL,
				INCOME_07 NUMBER NULL,
				INCOME_08 NUMBER NULL,
				INCOME_09 NUMBER NULL,
				INCOME_10 NUMBER NULL,
				INCOME_11 NUMBER NULL,
				INCOME_12 NUMBER NULL,
				INCOME_13 NUMBER NULL,
				INCOME_14 NUMBER NULL,
				INCOME_15 NUMBER NULL,
				INCOME_16 NUMBER NULL,
				INCOME_17 NUMBER NULL,
				INCOME_18 NUMBER NULL,
				INCOME_19 NUMBER NULL,
				INCOME_20 NUMBER NULL,
				INCOME_NAME_01 VARCHAR(20) NULL,	
				EXTRA_INCOME_01 NUMBER NULL,
				INCOME_NAME_02 VARCHAR(20) NULL,	
				EXTRA_INCOME_02 NUMBER NULL,
				INCOME_NAME_03 VARCHAR(20) NULL,	
				EXTRA_INCOME_03 NUMBER NULL,
				INCOME_NAME_04 VARCHAR(20) NULL,	
				EXTRA_INCOME_04 NUMBER NULL,
				OTHER_INCOME NUMBER NULL,
				TOTAL_INCOME NUMBER NULL,
				DEDUCT_01 NUMBER NULL,
				DEDUCT_02 NUMBER NULL,
				DEDUCT_03 NUMBER NULL,
				DEDUCT_04 NUMBER NULL,
				DEDUCT_05 NUMBER NULL,
				DEDUCT_06 NUMBER NULL,
				DEDUCT_07 NUMBER NULL,
				DEDUCT_08 NUMBER NULL,
				DEDUCT_09 NUMBER NULL,
				DEDUCT_10 NUMBER NULL,
				DEDUCT_11 NUMBER NULL,
				DEDUCT_12 NUMBER NULL,
				DEDUCT_13 NUMBER NULL,
				DEDUCT_14 NUMBER NULL,
				DEDUCT_15 NUMBER NULL,
				DEDUCT_16 NUMBER NULL,
				DEDUCT_NAME_01 VARCHAR(20) NULL,	
				EXTRA_DEDUCT_01 NUMBER NULL,
				DEDUCT_NAME_02 VARCHAR(20) NULL,	
				EXTRA_DEDUCT_02 NUMBER NULL,
				DEDUCT_NAME_03 VARCHAR(20) NULL,	
				EXTRA_DEDUCT_03 NUMBER NULL,
				DEDUCT_NAME_04 VARCHAR(20) NULL,	
				EXTRA_DEDUCT_04 NUMBER NULL,
				DEDUCT_NAME_05 VARCHAR(20) NULL,	
				EXTRA_DEDUCT_05 NUMBER NULL,
				DEDUCT_NAME_06 VARCHAR(20) NULL,	
				EXTRA_DEDUCT_06 NUMBER NULL,
				DEDUCT_NAME_07 VARCHAR(20) NULL,	
				EXTRA_DEDUCT_07 NUMBER NULL,
				DEDUCT_NAME_08 VARCHAR(20) NULL,	
				EXTRA_DEDUCT_08 NUMBER NULL,
				OTHER_DEDUCT NUMBER NULL,
				TOTAL_DEDUCT NUMBER NULL,
				NET_INCOME NUMBER NULL,
				APPROVE_DATE VARCHAR(19) NULL,		
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_SLIP PRIMARY KEY (SLIP_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_SLIP(
				SLIP_ID NUMBER(10) NOT NULL,
				PER_ID NUMBER(10) NULL,
				SLIP_YEAR VARCHAR2(4) NOT NULL,
				SLIP_MONTH VARCHAR2(2) NOT NULL,	
				PER_CARDNO VARCHAR2(13) NOT NULL,
				PN_NAME VARCHAR2(50) NULL,	
				PER_NAME VARCHAR2(30) NULL,	
				PER_SURNAME VARCHAR2(40) NULL,	
				DEPARTMENT_NAME VARCHAR2(80) NULL,	
				ORG_NAME VARCHAR2(80) NULL,	
				BANK_CODE VARCHAR2(2) NULL,	
				BANK_NAME VARCHAR2(50) NULL,	
				BRANCH_CODE VARCHAR2(4) NULL,	
				BRANCH_NAME VARCHAR2(50) NULL,	
				PER_BANK_ACCOUNT VARCHAR2(15) NULL,	
				INCOME_01 NUMBER(8,2) NULL,
				INCOME_02 NUMBER(8,2) NULL,
				INCOME_03 NUMBER(8,2) NULL,
				INCOME_04 NUMBER(8,2) NULL,
				INCOME_05 NUMBER(8,2) NULL,
				INCOME_06 NUMBER(8,2) NULL,
				INCOME_07 NUMBER(8,2) NULL,
				INCOME_08 NUMBER(8,2) NULL,
				INCOME_09 NUMBER(8,2) NULL,
				INCOME_10 NUMBER(8,2) NULL,
				INCOME_11 NUMBER(8,2) NULL,
				INCOME_12 NUMBER(8,2) NULL,
				INCOME_13 NUMBER(8,2) NULL,
				INCOME_14 NUMBER(8,2) NULL,
				INCOME_15 NUMBER(8,2) NULL,
				INCOME_16 NUMBER(8,2) NULL,
				INCOME_17 NUMBER(8,2) NULL,
				INCOME_18 NUMBER(8,2) NULL,
				INCOME_19 NUMBER(8,2) NULL,
				INCOME_20 NUMBER(8,2) NULL,
				INCOME_NAME_01 VARCHAR2(20) NULL,	
				EXTRA_INCOME_01 NUMBER(8,2) NULL,
				INCOME_NAME_02 VARCHAR2(20) NULL,	
				EXTRA_INCOME_02 NUMBER(8,2) NULL,
				INCOME_NAME_03 VARCHAR2(20) NULL,	
				EXTRA_INCOME_03 NUMBER(8,2) NULL,
				INCOME_NAME_04 VARCHAR2(20) NULL,	
				EXTRA_INCOME_04 NUMBER(8,2) NULL,
				OTHER_INCOME NUMBER(8,2) NULL,
				TOTAL_INCOME NUMBER(10,2) NULL,
				DEDUCT_01 NUMBER(8,2) NULL,
				DEDUCT_02 NUMBER(8,2) NULL,
				DEDUCT_03 NUMBER(8,2) NULL,
				DEDUCT_04 NUMBER(8,2) NULL,
				DEDUCT_05 NUMBER(8,2) NULL,
				DEDUCT_06 NUMBER(8,2) NULL,
				DEDUCT_07 NUMBER(8,2) NULL,
				DEDUCT_08 NUMBER(8,2) NULL,
				DEDUCT_09 NUMBER(8,2) NULL,
				DEDUCT_10 NUMBER(8,2) NULL,
				DEDUCT_11 NUMBER(8,2) NULL,
				DEDUCT_12 NUMBER(8,2) NULL,
				DEDUCT_13 NUMBER(8,2) NULL,
				DEDUCT_14 NUMBER(8,2) NULL,
				DEDUCT_15 NUMBER(8,2) NULL,
				DEDUCT_16 NUMBER(8,2) NULL,
				DEDUCT_NAME_01 VARCHAR2(20) NULL,	
				EXTRA_DEDUCT_01 NUMBER(8,2) NULL,
				DEDUCT_NAME_02 VARCHAR2(20) NULL,	
				EXTRA_DEDUCT_02 NUMBER(8,2) NULL,
				DEDUCT_NAME_03 VARCHAR2(20) NULL,	
				EXTRA_DEDUCT_03 NUMBER(8,2) NULL,
				DEDUCT_NAME_04 VARCHAR2(20) NULL,	
				EXTRA_DEDUCT_04 NUMBER(8,2) NULL,
				DEDUCT_NAME_05 VARCHAR2(20) NULL,	
				EXTRA_DEDUCT_05 NUMBER(8,2) NULL,
				DEDUCT_NAME_06 VARCHAR2(20) NULL,	
				EXTRA_DEDUCT_06 NUMBER(8,2) NULL,
				DEDUCT_NAME_07 VARCHAR2(20) NULL,	
				EXTRA_DEDUCT_07 NUMBER(8,2) NULL,
				DEDUCT_NAME_08 VARCHAR2(20) NULL,	
				EXTRA_DEDUCT_08 NUMBER(8,2) NULL,
				OTHER_DEDUCT NUMBER(8,2) NULL,
				TOTAL_DEDUCT NUMBER(10,2) NULL,
				NET_INCOME NUMBER(10,2) NULL,
				APPROVE_DATE VARCHAR2(19) NULL,		
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_SLIP PRIMARY KEY (SLIP_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_SLIP(
				SLIP_ID INTEGER(10) NOT NULL,
				PER_ID INTEGER(10) NULL,
				SLIP_YEAR VARCHAR(4) NOT NULL,
				SLIP_MONTH VARCHAR(2) NOT NULL,	
				PER_CARDNO VARCHAR(13) NOT NULL,
				PN_NAME VARCHAR(50) NULL,	
				PER_NAME VARCHAR(30) NULL,	
				PER_SURNAME VARCHAR(40) NULL,	
				DEPARTMENT_NAME VARCHAR(80) NULL,	
				ORG_NAME VARCHAR(80) NULL,	
				BANK_CODE VARCHAR(2) NULL,	
				BANK_NAME VARCHAR(50) NULL,	
				BRANCH_CODE VARCHAR(4) NULL,	
				BRANCH_NAME VARCHAR(50) NULL,	
				PER_BANK_ACCOUNT VARCHAR(15) NULL,	
				INCOME_01 DECIMAL(8,2) NULL,
				INCOME_02 DECIMAL(8,2) NULL,
				INCOME_03 DECIMAL(8,2) NULL,
				INCOME_04 DECIMAL(8,2) NULL,
				INCOME_05 DECIMAL(8,2) NULL,
				INCOME_06 DECIMAL(8,2) NULL,
				INCOME_07 DECIMAL(8,2) NULL,
				INCOME_08 DECIMAL(8,2) NULL,
				INCOME_09 DECIMAL(8,2) NULL,
				INCOME_10 DECIMAL(8,2) NULL,
				INCOME_11 DECIMAL(8,2) NULL,
				INCOME_12 DECIMAL(8,2) NULL,
				INCOME_13 DECIMAL(8,2) NULL,
				INCOME_14 DECIMAL(8,2) NULL,
				INCOME_15 DECIMAL(8,2) NULL,
				INCOME_16 DECIMAL(8,2) NULL,
				INCOME_17 DECIMAL(8,2) NULL,
				INCOME_18 DECIMAL(8,2) NULL,
				INCOME_19 DECIMAL(8,2) NULL,
				INCOME_20 DECIMAL(8,2) NULL,
				INCOME_NAME_01 VARCHAR(20) NULL,	
				EXTRA_INCOME_01 DECIMAL(8,2) NULL,
				INCOME_NAME_02 VARCHAR(20) NULL,	
				EXTRA_INCOME_02 DECIMAL(8,2) NULL,
				INCOME_NAME_03 VARCHAR(20) NULL,	
				EXTRA_INCOME_03 DECIMAL(8,2) NULL,
				INCOME_NAME_04 VARCHAR(20) NULL,	
				EXTRA_INCOME_04 DECIMAL(8,2) NULL,
				OTHER_INCOME DECIMAL(8,2) NULL,
				TOTAL_INCOME DECIMAL(10,2) NULL,
				DEDUCT_01 DECIMAL(8,2) NULL,
				DEDUCT_02 DECIMAL(8,2) NULL,
				DEDUCT_03 DECIMAL(8,2) NULL,
				DEDUCT_04 DECIMAL(8,2) NULL,
				DEDUCT_05 DECIMAL(8,2) NULL,
				DEDUCT_06 DECIMAL(8,2) NULL,
				DEDUCT_07 DECIMAL(8,2) NULL,
				DEDUCT_08 DECIMAL(8,2) NULL,
				DEDUCT_09 DECIMAL(8,2) NULL,
				DEDUCT_10 DECIMAL(8,2) NULL,
				DEDUCT_11 DECIMAL(8,2) NULL,
				DEDUCT_12 DECIMAL(8,2) NULL,
				DEDUCT_13 DECIMAL(8,2) NULL,
				DEDUCT_14 DECIMAL(8,2) NULL,
				DEDUCT_15 DECIMAL(8,2) NULL,
				DEDUCT_16 DECIMAL(8,2) NULL,
				DEDUCT_NAME_01 VARCHAR(20) NULL,	
				EXTRA_DEDUCT_01 DECIMAL(8,2) NULL,
				DEDUCT_NAME_02 VARCHAR(20) NULL,	
				EXTRA_DEDUCT_02 DECIMAL(8,2) NULL,
				DEDUCT_NAME_03 VARCHAR(20) NULL,	
				EXTRA_DEDUCT_03 DECIMAL(8,2) NULL,
				DEDUCT_NAME_04 VARCHAR(20) NULL,	
				EXTRA_DEDUCT_04 DECIMAL(8,2) NULL,
				DEDUCT_NAME_05 VARCHAR(20) NULL,	
				EXTRA_DEDUCT_05 DECIMAL(8,2) NULL,
				DEDUCT_NAME_06 VARCHAR(20) NULL,	
				EXTRA_DEDUCT_06 DECIMAL(8,2) NULL,
				DEDUCT_NAME_07 VARCHAR(20) NULL,	
				EXTRA_DEDUCT_07 DECIMAL(8,2) NULL,
				DEDUCT_NAME_08 VARCHAR(20) NULL,	
				EXTRA_DEDUCT_08 DECIMAL(8,2) NULL,
				OTHER_DEDUCT DECIMAL(8,2) NULL,
				TOTAL_DEDUCT DECIMAL(10,2) NULL,
				NET_INCOME DECIMAL(10,2) NULL,
				APPROVE_DATE VARCHAR(19) NULL,		
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_SLIP PRIMARY KEY (SLIP_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD LEVEL_NO VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD LEVEL_NO VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_REQ2_DTL ADD LEVEL_NO VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REQ2_DTL ADD LEVEL_NO VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_REQ3_DTL ADD LEVEL_NO VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REQ3_DTL ADD LEVEL_NO VARCHAR2(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD POT_ID INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD POT_ID NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERSONAL ADD POT_ID INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_COMDTL ADD POT_ID INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL ADD POT_ID NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMDTL ADD POT_ID INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_COMDTL ADD TP_CODE_ASSIGN VARCHAR(10) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_COMDTL ADD TP_CODE_ASSIGN VARCHAR2(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD TP_CODE VARCHAR(10) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD TP_CODE VARCHAR2(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT COUNT(PL_CODE) AS COUNT_DATA FROM PER_LINE_COMPETENCE ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if (!$count_data) {
				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_LINE_COMPETENCE(
					PL_CODE VARCHAR(10) NOT NULL,	
					ORG_ID INTEGER NOT NULL,	
					CP_CODE VARCHAR(3) NOT NULL,	
					LC_ACTIVE SINGLE NOT NULL,
					UPDATE_USER INTEGER2 NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_LINE_COMPETENCE PRIMARY KEY (PL_CODE, ORG_ID, CP_CODE)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_LINE_COMPETENCE(
					PL_CODE VARCHAR2(10) NOT NULL,	
					ORG_ID NUMBER(10) NOT NULL,	
					CP_CODE VARCHAR2(3) NOT NULL,	
					LC_ACTIVE NUMBER(1) NOT NULL,
					UPDATE_USER NUMBER(5) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_LINE_COMPETENCE PRIMARY KEY (PL_CODE, ORG_ID, CP_CODE)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_LINE_COMPETENCE(
					PL_CODE VARCHAR(10) NOT NULL,	
					ORG_ID INTEGER(10) NOT NULL,	
					CP_CODE VARCHAR(3) NOT NULL,	
					LC_ACTIVE SMALLINT(1) NOT NULL,
					UPDATE_USER SMALLINT(5) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_LINE_COMPETENCE PRIMARY KEY (PL_CODE, ORG_ID, CP_CODE)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " SELECT DISTINCT PL_CODE, ORG_ID FROM PER_POSITION WHERE POS_STATUS = 1 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$PL_CODE = trim($data[PL_CODE]);
					$ORG_ID = $data[ORG_ID];
					$code = array(	"101", "102", "103", "104", "105" );
					for ( $i=0; $i<count($code); $i++ ) { 
						$cmd = " INSERT INTO PER_LINE_COMPETENCE (PL_CODE, ORG_ID, CP_CODE, LC_ACTIVE, UPDATE_USER, UPDATE_DATE) 
										VALUES('$PL_CODE', $ORG_ID, '$code[$i]', 1, $SESS_USERID, '$UPDATE_DATE') ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
					} // end for
				} // end while						

				$cmd = " SELECT DISTINCT PL_CODE, a.ORG_ID FROM PER_POSITION a, PER_PERSONAL b 
								WHERE a.POS_ID = b.POS_ID AND b.LEVEL_NO IN ('D1', 'D2', 'M1', 'M2') AND PER_STATUS = 1 ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$PL_CODE = trim($data[PL_CODE]);
					$ORG_ID = $data[ORG_ID];
					$code = array(	"201", "202", "203", "204", "205", "206" );
					for ( $i=0; $i<count($code); $i++ ) { 
						$cmd = " INSERT INTO PER_LINE_COMPETENCE (PL_CODE, ORG_ID, CP_CODE, LC_ACTIVE, UPDATE_USER, UPDATE_DATE) 
										VALUES('$PL_CODE', $ORG_ID, '$code[$i]', 1, $SESS_USERID, '$UPDATE_DATE') ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
					} // end for
				} // end while						
			} // end if

			$cmd = " UPDATE PER_PERSONAL SET POS_ID = NULL WHERE PER_TYPE != 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_TEMP_POS_GROUP(
				TG_CODE VARCHAR(10) NOT NULL,	
				TG_NAME VARCHAR(100) NOT NULL,	
				TG_SEQ_NO INTEGER2 NULL,
				TG_ACTIVE SINGLE NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_TEMP_POS_GROUP PRIMARY KEY (TG_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_TEMP_POS_GROUP(
				TG_CODE VARCHAR2(10) NOT NULL,	
				TG_NAME VARCHAR2(100) NOT NULL,	
				TG_SEQ_NO NUMBER(5) NULL,
				TG_ACTIVE NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_TEMP_POS_GROUP PRIMARY KEY (TG_CODE)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_TEMP_POS_GROUP(
				TG_CODE VARCHAR(10) NOT NULL,	
				TG_NAME VARCHAR(100) NOT NULL,	
				TG_SEQ_NO SMALLINT(5) NULL,
				TG_ACTIVE SMALLINT(1) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_TEMP_POS_GROUP PRIMARY KEY (TG_CODE)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_TEMP_POS_NAME(
				TP_CODE VARCHAR(10) NOT NULL,	
				TP_NAME VARCHAR(100) NOT NULL,	
				TG_CODE VARCHAR(10) NULL,
				TP_SEQ_NO INTEGER2 NULL,
				TP_ACTIVE SINGLE NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_TEMP_POS_NAME PRIMARY KEY (TP_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_TEMP_POS_NAME(
				TP_CODE VARCHAR2(10) NOT NULL,	
				TP_NAME VARCHAR2(100) NOT NULL,	
				TG_CODE VARCHAR2(10) NULL,
				TP_SEQ_NO NUMBER(5) NULL,
				TP_ACTIVE NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_TEMP_POS_NAME PRIMARY KEY (TP_CODE)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_TEMP_POS_NAME(
				TP_CODE VARCHAR(10) NOT NULL,	
				TP_NAME VARCHAR(100) NOT NULL,	
				TG_CODE VARCHAR(10) NULL,
				TP_SEQ_NO SMALLINT(5) NULL,
				TP_ACTIVE SMALLINT(1) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_TEMP_POS_NAME PRIMARY KEY (TP_CODE)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_TEMP_POS_NAME (TP_CODE, TP_NAME, TP_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  SELECT trim(PL_CODE), PL_NAME, PL_ACTIVE, UPDATE_USER, UPDATE_DATE FROM PER_LINE WHERE PL_ACTIVE = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_TEMP_POS_NAME (TP_CODE, TP_NAME, TP_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  SELECT trim(PN_CODE), PN_NAME, PN_ACTIVE, UPDATE_USER, UPDATE_DATE FROM PER_POS_NAME WHERE PN_ACTIVE = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_POS_TEMP(
				POT_ID INTEGER NOT NULL,	
				ORG_ID INTEGER NOT NULL,
				POT_NO VARCHAR(15) NOT NULL,	
				ORG_ID_1 INTEGER NULL,
				ORG_ID_2 INTEGER NULL,
				ORG_ID_3 INTEGER NULL,
				ORG_ID_4 INTEGER NULL,
				ORG_ID_5 INTEGER NULL,
				TP_CODE VARCHAR(10) NOT NULL,
				POT_MIN_SALARY NUMBER NOT NULL,	
				POT_MAX_SALARY NUMBER NOT NULL,		
				DEPARTMENT_ID INTEGER NULL, 
				POT_SEQ_NO INTEGER2 NULL,
				POT_REMARK MEMO NULL,
				POT_STATUS SINGLE NOT NULL,		
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_POS_TEMP PRIMARY KEY (POT_ID)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_POS_TEMP(
				POT_ID NUMBER(10) NOT NULL,	
				ORG_ID NUMBER(10) NOT NULL,
				POT_NO VARCHAR2(15) NOT NULL,	
				ORG_ID_1 NUMBER(10) NULL,
				ORG_ID_2 NUMBER(10) NULL,
				ORG_ID_3 NUMBER(10) NULL,
				ORG_ID_4 NUMBER(10) NULL,
				ORG_ID_5 NUMBER(10) NULL,
				TP_CODE VARCHAR2(10) NOT NULL,
				POT_MIN_SALARY NUMBER(16,2) NOT NULL,	
				POT_MAX_SALARY NUMBER(16,2) NOT NULL,	
				DEPARTMENT_ID NUMBER(10) NULL,
				POT_SEQ_NO NUMBER(5) NULL,
				POT_REMARK VARCHAR2(2000) NULL,
				POT_STATUS NUMBER(1) NOT NULL,		
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_POS_TEMP PRIMARY KEY (POT_ID)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_POS_TEMP(
				POT_ID INTEGER(10) NOT NULL,	
				ORG_ID INTEGER(10) NOT NULL,
				POT_NO VARCHAR(15) NOT NULL,	
				ORG_ID_1 INTEGER(10) NULL,
				ORG_ID_2 INTEGER(10) NULL,
				ORG_ID_3 INTEGER(10) NULL,
				ORG_ID_4 INTEGER(10) NULL,
				ORG_ID_5 INTEGER(10) NULL,
				TP_CODE VARCHAR(10) NOT NULL,
				POT_MIN_SALARY DECIMAL(16,2) NOT NULL,	
				POT_MAX_SALARY DECIMAL(16,2) NOT NULL,	
				DEPARTMENT_ID INTEGER(10) NULL,
				POT_SEQ_NO SMALLINT(5) NULL,
				POT_REMARK TEXT NULL,
				POT_STATUS SMALLINT(1) NOT NULL,		
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_POS_TEMP PRIMARY KEY (POT_ID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DROP TABLE PER_SALARY_FORMULA ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET FLAG_SHOW = 'H' WHERE LINKTO_WEB = 'command_distribute_position.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();
	
			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET FLAG_SHOW = 'H' WHERE LINKTO_WEB = 'command_usual_retirepos.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();
	
			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET FLAG_SHOW = 'H' WHERE LINKTO_WEB = 'command_extra_retirepos.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();
	
			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET FLAG_SHOW = 'H' WHERE LINKTO_WEB = 'command_assign_position.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();
	
			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET FLAG_SHOW = 'H' WHERE LINKTO_WEB = 'structure_assign_person_list.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();
	
			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET FLAG_SHOW = 'H' WHERE MENU_LABEL = 'S03 ���͹䢡���ͺ�ӹҨ' ";
			$db->send_cmd($cmd);
			//$db->show_error();
	
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ABILITY ALTER ABI_DESC MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ABILITY MODIFY ABI_DESC VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ABILITY MODIFY ABI_DESC TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP, 
							  LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2)
							  VALUES (0, 'O1', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 4870, 19100, NULL, 13715, 11020, 16410) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP, 
							  LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2)
							  VALUES (0, 'O2', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 10190, 35220, NULL, 22715, 16450, 28970) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_FULL, 
							  LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, LAYER_EXTRA_MIDPOINT,
							  LAYER_EXTRA_MIDPOINT1, LAYER_EXTRA_MIDPOINT2)
							  VALUES (0, 'O3', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 15410, 49830, 37830, 32625, 29550, 41230, 29555, 29550, 32230) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP, 
							  LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2)
							  VALUES (0, 'O4', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 48220, 62760, NULL, 55495, 51860, 59130) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP, 
							  LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2)
							  VALUES (0, 'K1', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 8340, 24450, 7140, 19115, 16440, 21780) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP, 
							  LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2)
							  VALUES (0, 'K2', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 15050, 39630, 13160, 27345, 22220, 33490) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP, 
							  LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2)
							  VALUES (0, 'K3', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 22140, 53080, 19860, 37615, 33510, 45350) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP, 
							  LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2)
							  VALUES (0, 'K4', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 31400, 62760, 24400, 47085, 46260, 54920) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP, LAYER_SALARY_FULL, 
							  LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, LAYER_EXTRA_MIDPOINT,
							  LAYER_EXTRA_MIDPOINT1, LAYER_EXTRA_MIDPOINT2)
							  VALUES (0, 'K5', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 43810, 69810, 29980, 67560, 56815, 56020, 63310, 56025, 56020, 61630) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP, 
							  LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2)
							  VALUES (0, 'D1', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 26660, 54090, 19860, 40385, 33520, 47240) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP, 
							  LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2)
							  VALUES (0, 'D2', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 32850, 63960, 24400, 48415, 48190, 56190) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP, 
							  LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2)
							  VALUES (0, 'M1', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 51140, 67560, 24400, 59355, 57320, 63460) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX, LAYER_SALARY_TEMP, 
							  LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2)
							  VALUES (0, 'M2', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 56380, 69810, 29980, 64735, 64730, 66460) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E1', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 6410, 19430) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E3', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 10010, 33360) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E4', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 10850, 42830) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E5', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 37680, 68350) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E6', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 7370, 23970) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'E7', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 12850, 59790) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'S1', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 0, 109200) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'S2', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 0, 163800) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_LAYER_NEW (LAYER_TYPE, LEVEL_NO, LAYER_NO, LAYER_SALARY, LAYER_ACTIVE, 
							  UPDATE_USER, UPDATE_DATE, LAYER_SALARY_MIN, LAYER_SALARY_MAX)
							  VALUES (0, 'S3', 0, 0, 1, $SESS_USERID, '$UPDATE_DATE', 0, 218400) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_PROVINCE (PV_CODE, PV_NAME, CT_CODE, PV_ACTIVE, UPDATE_USER, UPDATE_DATE)
							  VALUES ('3800', '�֧���', '140', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_MGTSALARY ALTER EX_CODE VARCHAR(10) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_MGTSALARY MODIFY EX_CODE VARCHAR2(10) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_MGTSALARY MODIFY EX_CODE VARCHAR(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_X INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_X NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_X SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_Y INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_Y NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_Y SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_W INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_W NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_W SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_H INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_H NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_H SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_ALPHA NUMBER ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_ALPHA NUMBER(4,2) ";
			elseif($db_type=="mysql")
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_LEFT_ALPHA DECIMAL(4,2) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_X INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_X NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_X SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_Y INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_Y NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_Y SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_W INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_W NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_W SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_H INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_H NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_H SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_ALPHA NUMBER ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_ALPHA NUMBER(4,2) ";
			elseif($db_type=="mysql")
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_ALPHA DECIMAL(4,2) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_X INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_X NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_X SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_Y INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_Y NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_Y SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_W INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_W NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_W SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_H INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_H NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_H SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_ALPHA NUMBER ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_ALPHA NUMBER(4,2) ";
			elseif($db_type=="mysql")
				$cmd = " ALTER TABLE SITE_INFO ADD SITE_BG_RIGHT_ALPHA DECIMAL(4,2) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			if($db_type=="odbc") 
				$cmd = " ALTER TABLE SITE_INFO ADD HEAD_HEIGHT INTEGER2 ";
			elseif($db_type=="oci8") 
				$cmd = " ALTER TABLE SITE_INFO ADD HEAD_HEIGHT NUMBER(5) ";
			elseif($db_type=="mysql") 
				$cmd = " ALTER TABLE SITE_INFO ADD HEAD_HEIGHT SMALLINT(5) ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " UPDATE PER_TRAINING SET TRN_BOOK_DATE = NULL WHERE TRN_BOOK_DATE = '-543--' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV1 SET MENU_LABEL = 'C15 ����Ұҹ������ ��.' 
							  WHERE LINKTO_WEB = 'select_dopa_excel.html' ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " SELECT PER_PICSEQ FROM PER_PERSONALPIC ";
			$count_data = $db_dpis->send_cmd($cmd);

			if (!$count_data) {
				$cmd = " DROP TABLE PER_PERSONALPIC ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				if($DPISDB=="odbc") 
					$cmd = " CREATE TABLE PER_PERSONALPIC(
					PER_ID INTEGER NOT NULL,	
					PER_PICSEQ INTEGER NOT NULL,
					PER_CARDNO VARCHAR(13) NULL,	
					PER_GENNAME VARCHAR(20) NULL,
					PER_PICPATH VARCHAR(255) NULL,
					PER_PICSAVEDATE VARCHAR(19) NULL,
					PIC_SHOW CHAR(1) NULL,
					PIC_REMARK MEMO NULL,
					UPDATE_USER INTEGER2 NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_PERSONALPIC PRIMARY KEY (PER_ID, PER_PICSEQ)) ";
				elseif($DPISDB=="oci8") 
					$cmd = " CREATE TABLE PER_PERSONALPIC(
					PER_ID NUMBER(10) NOT NULL,	
					PER_PICSEQ NUMBER(10) NOT NULL,
					PER_CARDNO VARCHAR2(13) NULL,	
					PER_GENNAME VARCHAR2(20) NULL,
					PER_PICPATH VARCHAR2(255) NULL,
					PER_PICSAVEDATE VARCHAR2(19) NULL,
					PIC_SHOW CHAR(1) NULL,
					PIC_REMARK VARCHAR2(2000) NULL,
					UPDATE_USER NUMBER(5) NOT NULL,
					UPDATE_DATE VARCHAR2(19) NOT NULL,		
					CONSTRAINT PK_PER_PERSONALPIC PRIMARY KEY (PER_ID, PER_PICSEQ)) ";
				elseif($DPISDB=="mysql")
					$cmd = " CREATE TABLE PER_PERSONALPIC(
					PER_ID INTEGER(10) NOT NULL,	
					PER_PICSEQ INTEGER(10) NOT NULL,
					PER_CARDNO VARCHAR(13) NULL,	
					PER_GENNAME VARCHAR(20) NULL,
					PER_PICPATH VARCHAR(255) NULL,
					PER_PICSAVEDATE VARCHAR(19) NULL,
					PIC_SHOW CHAR(1) NULL,
					PIC_REMARK TEXT NULL,
					UPDATE_USER SMALLINT(5) NOT NULL,
					UPDATE_DATE VARCHAR(19) NOT NULL,		
					CONSTRAINT PK_PER_PERSONALPIC PRIMARY KEY (PER_ID, PER_PICSEQ)) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " SELECT PER_ID, PER_CARDNO, PER_NAME, PER_SURNAME FROM PER_PERSONAL WHERE PER_STATUS <> 3 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PER_ID = $data[PER_ID];
				$PER_CARDNO = $data[PER_CARDNO];
				$PER_GENNAME = personal_gen_name($data[PER_NAME],$data[PER_SURNAME]);
				$PER_GENNAME = "9999".$PER_GENNAME;

				if ($PER_CARDNO) {
					$cmd = " INSERT INTO PER_PERSONALPIC (PER_ID, PER_PICSEQ, PER_CARDNO, PER_GENNAME, 
									PER_PICPATH, PER_PICSAVEDATE, PIC_SHOW, PIC_REMARK, UPDATE_USER, UPDATE_DATE)
									VALUES ($PER_ID, 1, '$PER_CARDNO', '$PER_GENNAME', '../attachment/pic_personal/', '$UPDATE_DATE', 1, 
									NULL, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				}
			} // end while						
			if (is_dir($SRC_DIR)) trace_dir($SRC_DIR);

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_REQ3_DTL ADD ORG_ID_3 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REQ3_DTL ADD ORG_ID_3 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_REQ3_DTL ADD ORG_ID_3 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_REQ3_DTL ADD ORG_ID_4 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REQ3_DTL ADD ORG_ID_4 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_REQ3_DTL ADD ORG_ID_4 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_REQ3_DTL ADD ORG_ID_5 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REQ3_DTL ADD ORG_ID_5 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_REQ3_DTL ADD ORG_ID_5 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_REQ2_DTL ADD ORG_ID_3 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REQ2_DTL ADD ORG_ID_3 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_REQ2_DTL ADD ORG_ID_3 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_REQ2_DTL ADD ORG_ID_4 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REQ2_DTL ADD ORG_ID_4 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_REQ2_DTL ADD ORG_ID_4 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_REQ2_DTL ADD ORG_ID_5 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REQ2_DTL ADD ORG_ID_5 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_REQ2_DTL ADD ORG_ID_5 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD ORG_ID_3 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD ORG_ID_3 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD ORG_ID_3 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD ORG_ID_4 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD ORG_ID_4 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD ORG_ID_4 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD ORG_ID_5 INTEGER NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD ORG_ID_5 NUMBER(10) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD ORG_ID_5 INTEGER(10) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_UNDER_ORG3 VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_UNDER_ORG3 VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_UNDER_ORG4 VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_UNDER_ORG4 VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_UNDER_ORG5 VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_UNDER_ORG5 VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ASS_ORG3 VARCHAR(100) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ASS_ORG3 VARCHAR2(100) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ASS_ORG4 VARCHAR(100) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ASS_ORG4 VARCHAR2(100) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ASS_ORG5 VARCHAR(100) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_ASS_ORG5 VARCHAR2(100) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_UNDER_ORG3 VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_UNDER_ORG3 VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_UNDER_ORG4 VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_UNDER_ORG4 VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_UNDER_ORG5 VARCHAR(255) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_UNDER_ORG5 VARCHAR2(255) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_ASS_ORG3 VARCHAR(100) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_ASS_ORG3 VARCHAR2(100) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_ASS_ORG4 VARCHAR(100) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_ASS_ORG4 VARCHAR2(100) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_ASS_ORG5 VARCHAR(100) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_ASS_ORG5 VARCHAR2(100) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POSITIONHIS SET POH_SEQ_NO = 1 WHERE POH_SEQ_NO IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_SALARYHIS SET SAH_SEQ_NO = 1 WHERE SAH_SEQ_NO IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " CREATE TABLE PER_DISTRICT(
				DT_CODE VARCHAR(10) NOT NULL,	
				DT_NAME VARCHAR(100) NOT NULL,	
				PV_CODE VARCHAR(10) NOT NULL,	
				AP_CODE VARCHAR(10) NOT NULL,	
				ZIP_CODE VARCHAR(10) NOT NULL,	
				DT_SEQ_NO INTEGER2 NULL,
				DT_ACTIVE SINGLE NOT NULL,
				UPDATE_USER INTEGER2 NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_DISTRICT PRIMARY KEY (DT_CODE)) ";
			elseif($DPISDB=="oci8") 
				$cmd = " CREATE TABLE PER_DISTRICT(
				DT_CODE VARCHAR2(10) NOT NULL,	
				DT_NAME VARCHAR2(100) NOT NULL,	
				PV_CODE VARCHAR2(10) NOT NULL,	
				AP_CODE VARCHAR2(10) NOT NULL,	
				ZIP_CODE VARCHAR2(10) NOT NULL,	
				DT_SEQ_NO NUMBER(5) NULL,
				DT_ACTIVE NUMBER(1) NOT NULL,
				UPDATE_USER NUMBER(5) NOT NULL,
				UPDATE_DATE VARCHAR2(19) NOT NULL,		
				CONSTRAINT PK_PER_DISTRICT PRIMARY KEY (DT_CODE)) ";
			elseif($DPISDB=="mysql")
				$cmd = " CREATE TABLE PER_DISTRICT(
				DT_CODE VARCHAR(10) NOT NULL,	
				DT_NAME VARCHAR(100) NOT NULL,	
				PV_CODE VARCHAR(10) NOT NULL,	
				AP_CODE VARCHAR(10) NOT NULL,	
				ZIP_CODE VARCHAR(10) NOT NULL,	
				DT_SEQ_NO SMALLINT(5) NULL,
				DT_ACTIVE SMALLINT(1) NOT NULL,
				UPDATE_USER SMALLINT(5) NOT NULL,
				UPDATE_DATE VARCHAR(19) NOT NULL,		
				CONSTRAINT PK_PER_DISTRICT PRIMARY KEY (DT_CODE)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITION ALTER CL_NAME NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITION MODIFY CL_NAME NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITION MODIFY CL_NAME NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_EMP ALTER POEM_NO VARCHAR(15) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMP MODIFY POEM_NO VARCHAR2(15) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_EMP MODIFY POEM_NO VARCHAR(15) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ALTER POEMS_NO VARCHAR(15) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMPSER MODIFY POEMS_NO VARCHAR2(15) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_EMPSER MODIFY POEMS_NO VARCHAR(15) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINNER ALTER TN_BIRTHDATE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_BIRTHDATE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_BIRTHDATE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINNER ALTER TN_EDU_HIS1 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_EDU_HIS1 NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_EDU_HIS1 NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINNER ALTER TN_EDU_HIS2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_EDU_HIS2 NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_EDU_HIS2 NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINNER ALTER TN_EDU_HIS3 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_EDU_HIS3 NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_EDU_HIS3 NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINNER ALTER TN_POSITION NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_POSITION NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_POSITION NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINNER ALTER TN_WORK_PLACE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_WORK_PLACE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_WORK_PLACE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINNER ALTER TN_WORK_TEL NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_WORK_TEL NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_WORK_TEL NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINNER ALTER TN_WORK_EXPERIENCE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_WORK_EXPERIENCE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_WORK_EXPERIENCE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINNER ALTER TN_TRAIN_EXPERIENCE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_TRAIN_EXPERIENCE NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_TRAIN_EXPERIENCE NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINNER ALTER TN_ADDRESS NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_ADDRESS NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_ADDRESS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINNER ALTER TN_ADDRESS_TEL NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_ADDRESS_TEL NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_ADDRESS_TEL NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINNER ALTER TN_TECHNOLOGY_HIS NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_TECHNOLOGY_HIS NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_TECHNOLOGY_HIS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINNER ALTER TN_TRAIN_SKILL1 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_TRAIN_SKILL1 NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_TRAIN_SKILL1 NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINNER ALTER TN_TRAIN_SKILL2 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_TRAIN_SKILL2 NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_TRAIN_SKILL2 NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINNER ALTER TN_TRAIN_SKILL3 NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_TRAIN_SKILL3 NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_TRAIN_SKILL3 NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINNER ALTER TN_DEPT_TRAIN NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_DEPT_TRAIN NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_DEPT_TRAIN NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINNER ALTER TN_SPEC_ABILITY NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_SPEC_ABILITY NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_SPEC_ABILITY NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_TRAINNER ALTER TN_HOBBY NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_HOBBY NULL ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_TRAINNER MODIFY TN_HOBBY NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'master_table_district.html?table=PER_DISTRICT' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 12, 'M0112 �Ӻ�', 'S', 'W', 'master_table_district.html?table=PER_DISTRICT', 0, 9, 296, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();

				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 12, 'M0112 �Ӻ�', 'S', 'W', 'master_table_district.html?table=PER_DISTRICT', 0, 9, 296, 
								  $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}

			$cmd = " UPDATE PER_COMPENSATION_TEST SET CP_CONFIRM = 0 WHERE CP_CONFIRM IS NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_REMARK1 MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_REMARK1 VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_REMARK1 TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_REMARK2 MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_REMARK2 VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_REMARK2 TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_DECORCOND (DCON_TYPE, LEVEL_NO, DC_CODE, DCON_TIME1, 
							DCON_TIME2, DCON_TIME3, DCON_MGT, UPDATE_USER, UPDATE_DATE)
							VALUES (1, 'O1', '61', 25, 0, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_DECORCOND (DCON_TYPE, LEVEL_NO, DC_CODE, DCON_TIME1, 
							DCON_TIME2, DCON_TIME3, DCON_MGT, UPDATE_USER, UPDATE_DATE)
							VALUES (1, 'O2', '61', 25, 0, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_DECORCOND (DCON_TYPE, LEVEL_NO, DC_CODE, DCON_TIME1, 
							DCON_TIME2, DCON_TIME3, DCON_MGT, UPDATE_USER, UPDATE_DATE)
							VALUES (1, 'O3', '61', 25, 0, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_DECORCOND (DCON_TYPE, LEVEL_NO, DC_CODE, DCON_TIME1, 
							DCON_TIME2, DCON_TIME3, DCON_MGT, UPDATE_USER, UPDATE_DATE)
							VALUES (1, 'O4', '61', 25, 0, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_DECORCOND (DCON_TYPE, LEVEL_NO, DC_CODE, DCON_TIME1, 
							DCON_TIME2, DCON_TIME3, DCON_MGT, UPDATE_USER, UPDATE_DATE)
							VALUES (1, 'K1', '61', 25, 0, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_DECORCOND (DCON_TYPE, LEVEL_NO, DC_CODE, DCON_TIME1, 
							DCON_TIME2, DCON_TIME3, DCON_MGT, UPDATE_USER, UPDATE_DATE)
							VALUES (1, 'K2', '61', 25, 0, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_DECORCOND (DCON_TYPE, LEVEL_NO, DC_CODE, DCON_TIME1, 
							DCON_TIME2, DCON_TIME3, DCON_MGT, UPDATE_USER, UPDATE_DATE)
							VALUES (1, 'K3', '61', 25, 0, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_DECORCOND (DCON_TYPE, LEVEL_NO, DC_CODE, DCON_TIME1, 
							DCON_TIME2, DCON_TIME3, DCON_MGT, UPDATE_USER, UPDATE_DATE)
							VALUES (1, 'K4', '61', 25, 0, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_DECORCOND (DCON_TYPE, LEVEL_NO, DC_CODE, DCON_TIME1, 
							DCON_TIME2, DCON_TIME3, DCON_MGT, UPDATE_USER, UPDATE_DATE)
							VALUES (1, 'K5', '61', 25, 0, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_DECORCOND (DCON_TYPE, LEVEL_NO, DC_CODE, DCON_TIME1, 
							DCON_TIME2, DCON_TIME3, DCON_MGT, UPDATE_USER, UPDATE_DATE)
							VALUES (1, 'D1', '61', 25, 0, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_DECORCOND (DCON_TYPE, LEVEL_NO, DC_CODE, DCON_TIME1, 
							DCON_TIME2, DCON_TIME3, DCON_MGT, UPDATE_USER, UPDATE_DATE)
							VALUES (1, 'D2', '61', 25, 0, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_DECORCOND (DCON_TYPE, LEVEL_NO, DC_CODE, DCON_TIME1, 
							DCON_TIME2, DCON_TIME3, DCON_MGT, UPDATE_USER, UPDATE_DATE)
							VALUES (1, 'M1', '61', 25, 0, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_DECORCOND (DCON_TYPE, LEVEL_NO, DC_CODE, DCON_TIME1, 
							DCON_TIME2, DCON_TIME3, DCON_MGT, UPDATE_USER, UPDATE_DATE)
							VALUES (1, 'M2', '61', 25, 0, 0, 0, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_SALARYHIS SET SM_CODE = '1' WHERE MOV_CODE IN ('21310', '21351') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_SALARYHIS SET SM_CODE = '2' WHERE MOV_CODE IN ('21320', '21352') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_SALARYHIS SET SM_CODE = '3' WHERE MOV_CODE IN ('21330', '21353') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_SALARYHIS SET SM_CODE = '4' WHERE MOV_CODE IN ('21340', '21354') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE BACKOFFICE_MENU_BAR_LV2 SET FLAG_SHOW = 'H' 
							WHERE LINKTO_WEB in ('rpt_R004004.html', 'rpt_R004005.html', 'rpt_R006012.html', 'rpt_R006013.html', 'rpt_R010001.html', 'rpt_R010002.html', 'rpt_R010003.html', 'rpt_R010004.html') ";
			$db->send_cmd($cmd);
			//$db->show_error();

			$cmd = " CREATE INDEX IDX_PER_SALARYHIS_2 ON PER_SALARYHIS (SAH_EFFECTIVEDATE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " CREATE INDEX IDX_PER_POSITIONHIS_6 ON PER_POSITIONHIS (POH_EFFECTIVEDATE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_LINE_COMPETENCE ADD DEPARTMENT_ID INTEGER ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_LINE_COMPETENCE ADD DEPARTMENT_ID NUMBER(10) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_LINE_COMPETENCE ADD DEPARTMENT_ID INTEGER(10) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if ($CTRL_TYPE == 4)	{
				$cmd = " UPDATE PER_LINE_COMPETENCE SET DEPARTMENT_ID=$DEPARTMENT_ID ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} else {
				if($DPISDB=="odbc" || $DPISDB=="mysql"){ 
					$cmd = " SELECT ORG_ID FROM PER_LINE_COMPETENCE ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					while($data = $db_dpis->get_array()){
						$ORG_ID = $data[ORG_ID];
						$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
						$data1 = $db_dpis1->get_array();
						$ORG_ID_REF = $data1[ORG_ID_REF];
						$cmd = " UPDATE PER_LINE_COMPETENCE SET DEPARTMENT_ID = $ORG_ID_REF 
										  WHERE POS_ID = $POS_ID ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
					} // end while						
				} elseif($DPISDB=="oci8"){
					$cmd = " UPDATE PER_LINE_COMPETENCE A SET A.DEPARTMENT_ID = 
									  (SELECT B.ORG_ID_REF FROM PER_ORG B WHERE A.ORG_ID = B.ORG_ID) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				} // end if
			} // end if

			if($DPISDB=="oci8") {
				$cmd = " SELECT TNAME FROM TAB WHERE TABTYPE = 'TABLE' AND TNAME LIKE 'PER_%' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while ( $data = $db_dpis->get_array() ) {
					$TNAME = trim($data[TNAME]);
					$cmd = " ALTER TABLE $TNAME MODIFY UPDATE_USER NUMBER(11) ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while
			} // end if

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_UNION SINGLE NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_UNION NUMBER(1) NULL ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_UNION SMALLINT(1) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_UNIONDATE VARCHAR(19) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_UNIONDATE VARCHAR2(19) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITION ADD POS_NO_NAME VARCHAR(15) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITION ADD POS_NO_NAME VARCHAR2(15) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ORDER_DTL ADD ORD_POS_NO_NAME VARCHAR(15) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORDER_DTL ADD ORD_POS_NO_NAME VARCHAR2(15) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_REQ3_DTL ADD REQ_POS_NO_NAME VARCHAR(15) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REQ3_DTL ADD REQ_POS_NO_NAME VARCHAR2(15) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_REQ2_DTL ADD REQ_POS_NO_NAME VARCHAR(15) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REQ2_DTL ADD REQ_POS_NO_NAME VARCHAR2(15) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD REQ_POS_NO_NAME VARCHAR(15) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REQ1_DTL1 ADD REQ_POS_NO_NAME VARCHAR2(15) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_POS_NO_NAME VARCHAR(15) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POSITIONHIS ADD POH_POS_NO_NAME VARCHAR2(15) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_POS_NO_NAME VARCHAR(15) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SALARYHIS ADD SAH_POS_NO_NAME VARCHAR2(15) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ACTINGHIS ADD ACTH_POS_NO_NAME VARCHAR(15) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ACTINGHIS ADD ACTH_POS_NO_NAME VARCHAR2(15) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ACTINGHIS ADD ACTH_POS_NO_NAME VARCHAR(15) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ACTINGHIS ADD ACTH_POS_NO_NAME_ASSIGN VARCHAR2(15) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_POS_NO_NAME VARCHAR(15) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_POSITIONHIS ADD POH_POS_NO_NAME VARCHAR2(15) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SAH_POS_NO_NAME VARCHAR(15) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_WORKFLOW_SALARYHIS ADD SAH_POS_NO_NAME VARCHAR2(15) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_EMP ADD POEM_NO_NAME VARCHAR(15) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMP ADD POEM_NO_NAME VARCHAR2(15) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD POEMS_NO_NAME VARCHAR(15) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_EMPSER ADD POEMS_NO_NAME VARCHAR2(15) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_POS_TEMP ADD POT_NO_NAME VARCHAR(15) NULL ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_TEMP ADD POT_NO_NAME VARCHAR2(15) NULL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " DELETE FROM PER_POSITION_COMPETENCE WHERE DEPARTMENT_ID = 0 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " SELECT PER_ID FROM PER_PERSONAL WHERE PER_STATUS = 2 AND PER_POSDATE IS NULL";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PER_ID = $data[PER_ID];

				$cmd = " SELECT POH_EFFECTIVEDATE FROM PER_POSITIONHIS a, PER_MOVMENT b 
								WHERE PER_ID = $PER_ID AND a.MOV_CODE = b.MOV_CODE AND MOV_SUB_TYPE = 9 ";
				$count_data = $db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				if ($count_data) {
					$data1 = $db_dpis1->get_array();
					$POH_EFFECTIVEDATE = $data[POH_EFFECTIVEDATE];
					$cmd = " UPDATE  PER_PERSONAL SET PER_POSDATE = $POH_EFFECTIVEDATE WHERE PER_ID = $PER_ID ";
					$db_dpis1->send_cmd($cmd);
					$db_dpis1->show_error();
				} // end if
			} // end while

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_JOB MEMO ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_JOB VARCHAR2(2000) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_PERSONAL ADD PER_JOB TEXT ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

/*
			$cmd = " SELECT LINKTO_WEB FROM BACKOFFICE_MENU_BAR_LV2 
							  WHERE LINKTO_WEB = 'data_early_retire.html' ";
			$count_data = $db->send_cmd($cmd);
			//$db->show_error();
			if (!$count_data) {
				$cmd = " SELECT max(MENU_ID) as MAX_ID FROM BACKOFFICE_MENU_BAR_LV2 ";
				$db->send_cmd($cmd);
				//$db->show_error();
				$data = $db->get_array();
				$MAX_ID = $data[MAX_ID] + 1;
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'TH', $MAX_ID, 3, 'P0503 �ç������³���ء�͹��˹�', 'S', 'W', 'data_early_retire.html', 0, 35,
								  245, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
	
				$cmd = " INSERT INTO BACKOFFICE_MENU_BAR_LV2 (FID, LANGCODE, MENU_ID, MENU_ORDER, MENU_LABEL, 
								  FLAG_SHOW, TYPE_LINKTO, LINKTO_WEB, LINKTO_TID, PARENT_ID_LV0, PARENT_ID_LV1, CREATE_DATE, 
								  CREATE_BY, UPDATE_DATE, UPDATE_BY)
								  VALUES (1, 'EN', $MAX_ID, 3, 'P0503 �ç������³���ء�͹��˹�', 'S', 'W', 'data_early_retire.html', 0, 35,
								  245, $CREATE_DATE, $CREATE_BY, $CREATE_DATE, $CREATE_BY) ";
				$db->send_cmd($cmd);
				//$db->show_error();
			}
*/
//			$cmd = " update PER_CONTROL set CTRL_ALTER = 11 ";
//			$db_dpis->send_cmd($cmd);
//			//$db_dpis->show_error();
//		} // end if($CTRL_ALTER < 9) 

// ���׹�ѹ�.�.�.����Դ
		if ($COM_NO) {
			$cmd = " SELECT COM_ID FROM PER_COMMAND WHERE COM_NO = '$COM_NO' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$COM_ID = $data[COM_ID] + 0;
		}
		if ($COM_ID) {
			$cmd = " SELECT CMD_DATE FROM PER_COMDTL WHERE COM_ID = $COM_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$CMD_DATE = trim($data[CMD_DATE]);

			$cmd = " UPDATE PER_COMDTL SET CMD_DATE = '2008-12-11' WHERE COM_ID = $COM_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POS_MOVE SET POS_DATE = '2008-12-11' WHERE POS_DATE = '$CMD_DATE' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_POSITIONHIS SET POH_EFFECTIVEDATE = '2008-12-11' WHERE POH_DOCNO = '$COM_NO' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if ($COM_DATE) {
				$COM_DATE =  (substr(trim($COM_DATE), 6, 4) - 543) ."-". substr(trim($COM_DATE), 3, 2) ."-". substr(trim($COM_DATE), 0, 2);
				$cmd = " UPDATE PER_COMMAND SET COM_DATE = '$COM_DATE' WHERE COM_ID = $COM_ID ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();

				$cmd = " UPDATE PER_POSITIONHIS SET POH_DOCDATE = '$COM_DATE' WHERE POH_DOCNO = '$COM_NO' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " SELECT POS_ID, PL_CODE, CL_NAME, PT_CODE FROM PER_POSITION WHERE POS_STATUS = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while ( $data = $db_dpis->get_array() ) {
				$POS_ID = $data[POS_ID] + 0;
				$PL_CODE = trim($data[PL_CODE]);
				$CL_NAME = trim($data[CL_NAME]);
				$PT_CODE = trim($data[PT_CODE]);

				$cmd = " SELECT PL_CODE_NEW, PL_TYPE, CL_NAME FROM PER_LINE WHERE PL_CODE = '$PL_CODE' ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				$data1 = $db_dpis1->get_array();
				$PL_CODE_NEW = trim($data1[PL_CODE_NEW]);
				$PL_TYPE = $data1[PL_TYPE] + 0;
				$TMP_CL_NAME = trim($data1[CL_NAME]);
				if (substr($PL_CODE,0,1)=='0') {
					if (substr($PL_CODE_NEW,0,1)!='5') $PL_CODE_NEW = '5' . substr($PL_CODE,1,5);
					$cmd = " UPDATE PER_POSITION SET PL_CODE = '$PL_CODE_NEW' WHERE POS_ID = $POS_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end if
				if (substr($CL_NAME,0,1)>='1' && substr($CL_NAME,0,1)<='9') {
					$cmd = " select LEVEL_NO_MIN, LEVEL_NO_MAX from PER_CO_LEVEL where trim(CL_NAME)='$CL_NAME' ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();
					$LEVEL_NO_MIN = trim($data1[LEVEL_NO_MIN]);
					$LEVEL_NO_MAX = trim($data1[LEVEL_NO_MAX]);
					for ($LVL=$LEVEL_NO_MIN;$LVL<=$LEVEL_NO_MAX;$LVL++){
						$LVL = str_pad(trim($LVL), 2, "0", STR_PAD_LEFT);
						$cmd = " select NEW_LEVEL_NO from PER_MAP_POS where LEVEL_NO='$LVL' and PL_TYPE=$PL_TYPE ";
						$db_dpis1->send_cmd($cmd);
						$data1 = $db_dpis1->get_array();
						$NEW_LEVEL_NO = $data1[NEW_LEVEL_NO];
						if ($PL_TYPE==2 || $PL_TYPE==3 || $PL_TYPE==5) 
							if ($LEVEL_NO_MIN=="08" && $PT_CODE=="31") $NEW_LEVEL_NO = "D1";
							elseif ($LEVEL_NO_MIN=="09" && $PT_CODE=="32") $NEW_LEVEL_NO = "D2";
						if ($LVL == 8) echo "$PL_TYPE - $PT_CODE - $NEW_LEVEL_NO<br>";
		
						if ($LVL == $LEVEL_NO_MIN+0) $NEW_LEVEL_NO_MIN = $NEW_LEVEL_NO;
						if ($LVL == $LEVEL_NO_MAX+0) $NEW_LEVEL_NO_MAX = $NEW_LEVEL_NO;
						$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO)='$NEW_LEVEL_NO' ";
						$db_dpis1->send_cmd($cmd);
						$data1 = $db_dpis1->get_array();
						$LEVEL_NAME = trim($data1[LEVEL_NAME]);
						$arr_temp = explode(" ", $LEVEL_NAME);
						$LEVEL_NAME = $arr_temp[1];
						if ($NEW_LEVEL_NO=="D1") $LEVEL_NAME = "�ӹ�¡���дѺ��";
						elseif ($NEW_LEVEL_NO=="D2") $LEVEL_NAME = "�ӹ�¡���дѺ�٧";
						elseif ($NEW_LEVEL_NO=="M1") $LEVEL_NAME = "�������дѺ��";
						elseif ($NEW_LEVEL_NO=="M2") $LEVEL_NAME = "�������дѺ�٧";
						else $LEVEL_NAME = str_replace("�дѺ", "", $LEVEL_NAME);

						if ($LEVEL_NAME != $OLD_LEVEL_NAME) {
							if ($LEVEL_NO_MIN==$LVL)
								$NEW_LEVEL_NAME = $LEVEL_NAME;
							else
								$NEW_LEVEL_NAME = (trim($LEVEL_NAME)?($NEW_LEVEL_NAME .' ���� '. $LEVEL_NAME):$NEW_LEVEL_NAME);
							$OLD_LEVEL_NAME = $LEVEL_NAME;
						}
					}
		
					$cmd = " select LEVEL_NO_MIN, LEVEL_NO_MAX from PER_CO_LEVEL where trim(CL_NAME)='$NEW_LEVEL_NAME' ";
					$count_data = $db_dpis1->send_cmd($cmd);
					if (!$count_data) {
						$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
										  UPDATE_DATE)
									      VALUES ('$NEW_LEVEL_NAME', '$NEW_LEVEL_NO_MIN', '$NEW_LEVEL_NO_MAX', 1, $SESS_USERID, 
										  '$UPDATE_DATE') ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
					}

					$cmd = " UPDATE PER_POSITION SET CL_NAME = '$NEW_LEVEL_NAME' WHERE POS_ID = $POS_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end if

//				if (strpos($CL_NAME,"-") !== false || $CL_NAME == $TMP_CL_NAME) {
					$cmd = " select CL_NAME from PER_POS_MOVE where POS_ID = $POS_ID and POS_DATE = '2008-12-11' ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();
					$CL_NAME = trim($data1[CL_NAME]);

					$cmd = " select LEVEL_NO_MIN, LEVEL_NO_MAX from PER_CO_LEVEL where trim(CL_NAME)='$CL_NAME' ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();
					$LEVEL_NO_MIN = trim($data1[LEVEL_NO_MIN]);
					$LEVEL_NO_MAX = trim($data1[LEVEL_NO_MAX]);
					for ($LVL=$LEVEL_NO_MIN;$LVL<=$LEVEL_NO_MAX;$LVL++){
						$LVL = str_pad(trim($LVL), 2, "0", STR_PAD_LEFT);
						$cmd = " select NEW_LEVEL_NO from PER_MAP_POS where LEVEL_NO='$LVL' and PL_TYPE=$PL_TYPE ";
						$db_dpis1->send_cmd($cmd);
						$data1 = $db_dpis1->get_array();
						$NEW_LEVEL_NO = $data1[NEW_LEVEL_NO];
						if ($PL_TYPE==2 || $PL_TYPE==3 || $PL_TYPE==5) 
							if ($LEVEL_NO_MIN=="08" && $PT_CODE=="31") $NEW_LEVEL_NO = "D1";
							elseif ($LEVEL_NO_MIN=="09" && $PT_CODE=="32") $NEW_LEVEL_NO = "D2";
		
						if ($LVL == $LEVEL_NO_MIN+0) $NEW_LEVEL_NO_MIN = $NEW_LEVEL_NO;
						if ($LVL == $LEVEL_NO_MAX+0) $NEW_LEVEL_NO_MAX = $NEW_LEVEL_NO;
						$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO)='$NEW_LEVEL_NO' ";
						$db_dpis1->send_cmd($cmd);
						$data1 = $db_dpis1->get_array();
						$LEVEL_NAME = trim($data1[LEVEL_NAME]);
						$arr_temp = explode(" ", $LEVEL_NAME);
						$LEVEL_NAME = $arr_temp[1];
						if ($NEW_LEVEL_NO=="D1") $LEVEL_NAME = "�ӹ�¡���дѺ��";
						elseif ($NEW_LEVEL_NO=="D2") $LEVEL_NAME = "�ӹ�¡���дѺ�٧";
						elseif ($NEW_LEVEL_NO=="M1") $LEVEL_NAME = "�������дѺ��";
						elseif ($NEW_LEVEL_NO=="M2") $LEVEL_NAME = "�������дѺ�٧";
						else $LEVEL_NAME = str_replace("�дѺ", "", $LEVEL_NAME);

						if ($LEVEL_NAME != $OLD_LEVEL_NAME) {
							if ($LEVEL_NO_MIN==$LVL)
								$NEW_LEVEL_NAME = $LEVEL_NAME;
							else
								$NEW_LEVEL_NAME = (trim($LEVEL_NAME)?($NEW_LEVEL_NAME .' ���� '. $LEVEL_NAME):$NEW_LEVEL_NAME);
							$OLD_LEVEL_NAME = $LEVEL_NAME;
						}
					}
		
					$cmd = " select LEVEL_NO_MIN, LEVEL_NO_MAX from PER_CO_LEVEL where trim(CL_NAME)='$NEW_LEVEL_NAME' ";
					$count_data = $db_dpis1->send_cmd($cmd);
					if (!$count_data) {
						$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
										  UPDATE_DATE)
										  VALUES ('$NEW_LEVEL_NAME', '$NEW_LEVEL_NO_MIN', '$NEW_LEVEL_NO_MAX', 1, $SESS_USERID, 
										  '$UPDATE_DATE') ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
					}

					$cmd = " UPDATE PER_POSITION SET CL_NAME = '$NEW_LEVEL_NAME' WHERE POS_ID = $POS_ID ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
//				} // end if

			} // end while
		} // end if

		$COM_NO = "";
		$COM_DATE = "";
	} // end if

	function trace_dir($dir){
		global $db_dpis, $IMG_PATH;
		global $success_pic, $error_pic, $total_pic, $found001, $err_text;
		
		if ($dir_handler = opendir($dir)) {
			while (($file = readdir($dir_handler)) !== false) {
				if(!($file == '.' || $file == '..' || $file == 'Thumbs.db'  || $file == 'shadow.png' )) {
					if(is_dir(($dir."/".$file))){
						trace_dir(($dir."/".$file));
					}else{
//						echo "$success_pic-$file<br>";
						$c=strpos($file,".");
						$c1=strpos($file,"-001.");
						if ($c !== false && $c1 === false) {
							$newfile = substr($file,0,$c)."-001".substr($file,$c);
//							echo "$file==>$newfile<br>";
							$flgRename = rename($dir."/".$file, $dir."/".$newfile); 
							if($flgRename) { 
//								echo "___File [$file] Rename To [$newfile]<br>"; 
								$success_pic++;					
							} else { 
//								echo "***File [$file] can not Rename To [$newfile]<br>"; 
								$error_pic++;
							} // end if 
							$total_pic++;
						} else {
							if ($c1 !== false) $found001++;
						} // end if ($c !== false && $c1 === false)
					} // end if
				} // end  if(!(c == '.' || $file == '..' || $file == 'Thumbs.db' ))
			} // end while
			closedir($dir_handler);
		}else{
			$err_text .= "CANNOT OPEN DIRECTORY :: $dir<br>";
		} // end if
		
		return;
	} // end if

?>