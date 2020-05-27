<?php 
add_field("PER_DECORATION", "DC_ENG_NAME","VARCHAR", "100", "NULL");

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

			$cmd = " ALTER TABLE PER_COMDTL DROP CONSTRAINT FK2_PER_COMDTL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			if($DPISDB=="odbc" || $DPISDB=="mysql")
				$cmd = " ALTER TABLE PER_COMDTL DROP CONSTRAINT INXU1_PER_COMDTL ";
			elseif($DPISDB=="oci8")
				$cmd = " DROP INDEX INXU1_PER_COMDTL ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_TYPE SET PT_NAME = 'Ç' WHERE PT_CODE = '12' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " update PER_CONTROL set CTRL_ALTER = 5 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
?>