<?php

			/*release 5.1.0.0 begin*/
			// UPDATE 2015 / 11 / 04 Alter for GMIS export
			
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE GMIS_GPIS ALTER tempClName VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE GMIS_GPIS MODIFY tempClName VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE GMIS_GPIS MODIFY tempClName VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			
			
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN ALTER tempClName VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN MODIFY tempClName VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN MODIFY tempClName VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT ALTER tempClName VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT MODIFY tempClName VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT MODIFY tempClName VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE GPIS1 ALTER tempClName VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE GPIS1 MODIFY tempClName VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE GPIS1 MODIFY tempClName VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE GPIS1_FLOW_IN ALTER tempClName VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE GPIS1_FLOW_IN MODIFY tempClName VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE GPIS1_FLOW_IN MODIFY tempClName VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE GPIS1_FLOW_OUT ALTER tempClName VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE GPIS1_FLOW_OUT MODIFY tempClName VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE GPIS1_FLOW_OUT MODIFY tempClName VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE GMIS_GPIS ALTER tempEducationLevel VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE GMIS_GPIS MODIFY tempEducationLevel VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE GMIS_GPIS MODIFY tempEducationLevel VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN ALTER tempEducationLevel VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN MODIFY tempEducationLevel VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
			$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN MODIFY tempEducationLevel VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT ALTER tempEducationLevel VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT MODIFY tempEducationLevel VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT MODIFY tempEducationLevel VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE GPIS1 ALTER tempEducationLevel VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE GPIS1 MODIFY tempEducationLevel VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE GPIS1 MODIFY tempEducationLevel VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE GPIS1_FLOW_IN ALTER tempEducationLevel VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE GPIS1_FLOW_IN MODIFY tempEducationLevel VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE GPIS1_FLOW_IN MODIFY tempEducationLevel VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			
			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE GPIS1_FLOW_OUT ALTER tempEducationLevel VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE GPIS1_FLOW_OUT MODIFY tempEducationLevel VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE GPIS1_FLOW_OUT MODIFY tempEducationLevel VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			


			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE GMIS_GPIS ALTER TEMPRESULT1 VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE GMIS_GPIS MODIFY TEMPRESULT1 VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE GMIS_GPIS MODIFY TEMPRESULT1 VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN ALTER TEMPRESULT1 VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN MODIFY TEMPRESULT1 VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN MODIFY TEMPRESULT1 VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT ALTER TEMPRESULT1 VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT MODIFY TEMPRESULT1 VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT MODIFY TEMPRESULT1 VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE GMIS_GPIS ALTER TEMPRESULT2 VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE GMIS_GPIS MODIFY TEMPRESULT2 VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE GMIS_GPIS MODIFY TEMPRESULT2 VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN ALTER TEMPRESULT2 VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN MODIFY TEMPRESULT2 VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_IN MODIFY TEMPRESULT2 VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT ALTER TEMPRESULT2 VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT MODIFY TEMPRESULT2 VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE GMIS_GPIS_FLOW_OUT MODIFY TEMPRESULT2 VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			
			//$db_dpis->show_error();
			// End UPDATE 2015 / 11 / 04 Alter for GMIS export
			
			$cmd = " update PER_CONTROL set CTRL_ALTER = 16 ,UPDATE_DATE=SYSDATE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
?>