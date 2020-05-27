<?php
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
				$cmd = " ALTER TABLE PER_CO_LEVEL ALTER CL_NAME VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_CO_LEVEL MODIFY CL_NAME VARCHAR2(100) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_CO_LEVEL MODIFY CL_NAME VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_CO_LEVEL SET CL_NAME = TRIM(CL_NAME) ";
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

			$cmd = " UPDATE PER_POSITION SET CL_NAME = TRIM(CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " ALTER TABLE PER_POSITION ADD CONSTRAINT FK7_PER_POSITION 
							  FOREIGN KEY (CL_NAME) REFERENCES PER_CO_LEVEL (CL_NAME) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_POS_MOVE ALTER CL_NAME VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_POS_MOVE MODIFY CL_NAME VARCHAR2(100) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_POS_MOVE MODIFY CL_NAME VARCHAR(100) ";
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
				$cmd = " ALTER TABLE PER_ORDER_DTL ALTER CL_NAME VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORDER_DTL MODIFY CL_NAME VARCHAR2(100) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_ORDER_DTL MODIFY CL_NAME VARCHAR(100) ";
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
				$cmd = " ALTER TABLE PER_REQ1_DTL1 ALTER CL_NAME VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REQ1_DTL1 MODIFY CL_NAME VARCHAR2(100) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_REQ1_DTL1 MODIFY CL_NAME VARCHAR(100) ";
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
				$cmd = " ALTER TABLE PER_REQ2_DTL ALTER CL_NAME VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REQ2_DTL MODIFY CL_NAME VARCHAR2(100) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_REQ2_DTL MODIFY CL_NAME VARCHAR(100) ";
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
				$cmd = " ALTER TABLE PER_REQ3_DTL ALTER CL_NAME VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_REQ3_DTL MODIFY CL_NAME VARCHAR2(100) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_REQ3_DTL MODIFY CL_NAME VARCHAR(100) ";
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
				$cmd = " ALTER TABLE PER_SUM_DTL2 ALTER CL_NAME VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_SUM_DTL2 MODIFY CL_NAME VARCHAR2(100) ";
			elseif($DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_SUM_DTL2 MODIFY CL_NAME VARCHAR(100) ";
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
							  VALUES ('บริหารระดับสูง', 'M2', 'M2', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " INSERT INTO PER_CO_LEVEL (CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE, UPDATE_USER, 
							  UPDATE_DATE)
							  VALUES ('อำนวยการระดับสูง', 'D2', 'D2', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_LINE", "PL_TYPE","SINGLE", "1", "NULL");
			add_field("PER_LINE", "PL_CODE_NEW","VARCHAR", "10", "NULL");
			add_field("PER_LINE", "LG_CODE","VARCHAR", "10", "NULL");
			add_field("PER_LINE", "PL_CODE_DIRECT","VARCHAR", "10", "NULL");
			add_field("PER_LINE", "CL_NAME","VARCHAR", "100", "NULL");

			$cmd = " UPDATE PER_LINE SET PL_CODE = trim(PL_CODE) ";
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

			add_field("PER_KPI", "KPI_TARGET_LEVEL1_DESC","VARCHAR", "255", "NULL");
			add_field("PER_KPI", "KPI_TARGET_LEVEL2_DESC","VARCHAR", "255", "NULL");
			add_field("PER_KPI", "KPI_TARGET_LEVEL3_DESC","VARCHAR", "255", "NULL");
			add_field("PER_KPI", "KPI_TARGET_LEVEL4_DESC","VARCHAR", "255", "NULL");
			add_field("PER_KPI", "KPI_TARGET_LEVEL5_DESC","VARCHAR", "255", "NULL");

			$cmd = " INSERT INTO PER_COMTYPE (COM_TYPE, COM_NAME, COM_DESC, COM_GROUP, COM_ACTIVE)
							  VALUES ('0206', 'คส. 2.6', 'คำสั่งจัดคนลง', '02', 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_LINE SET LG_CODE = trim(PL_CODE) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); 

			$cmd = " update PER_CONTROL set CTRL_ALTER = 4 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
?>