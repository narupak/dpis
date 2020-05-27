<?php
add_field("PER_PERSONAL", "APPROVE_PER_ID","INTEGER", "10", "NULL");
			add_field("PER_PERSONAL", "REPLACE_PER_ID","INTEGER", "10", "NULL");
			add_field("PER_PERSONAL", "ABSENT_FLAG","SINGLE", "1", "NULL");
			add_field("PER_ORG_JOB", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_ORG_JOB, PER_PERSONAL SET PER_ORG_JOB.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_ORG_JOB.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_ORG_JOB A SET A.PER_CARDNO = 
								  (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_POSITIONHIS", "PER_CARDNO","VARCHAR", "13", "NULL");
			add_field("PER_POSITIONHIS", "EP_CODE","VARCHAR", "10", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_POSITIONHIS, PER_PERSONAL SET PER_POSITIONHIS.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_POSITIONHIS.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_POSITIONHIS A SET A.PER_CARDNO = 
								  (SELECT B.PER_CARDNO FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_SALARYHIS", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_SALARYHIS, PER_PERSONAL SET PER_SALARYHIS.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_SALARYHIS.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_SALARYHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_EXTRAHIS", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_EXTRAHIS, PER_PERSONAL SET PER_EXTRAHIS.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_EXTRAHIS.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_EXTRAHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_EXTRA_INCOMEHIS", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_EXTRA_INCOMEHIS, PER_PERSONAL SET PER_EXTRA_INCOMEHIS.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_EXTRA_INCOMEHIS.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_EXTRA_INCOMEHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO 
								  FROM PER_PERSONAL B WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_EDUCATE", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_EDUCATE, PER_PERSONAL SET PER_EDUCATE.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_EDUCATE.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_EDUCATE A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_TRAINING", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_TRAINING, PER_PERSONAL SET PER_TRAINING.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_TRAINING.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_TRAINING A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_ABILITY", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_ABILITY, PER_PERSONAL SET PER_ABILITY.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
								  WHERE PER_PERSONAL.PER_ID = PER_ABILITY.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_ABILITY A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_HEIR", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_HEIR, PER_PERSONAL SET PER_HEIR.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
								  WHERE PER_PERSONAL.PER_ID = PER_HEIR.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_HEIR A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_ABSENTHIS", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_ABSENTHIS, PER_PERSONAL SET PER_ABSENTHIS.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_ABSENTHIS.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_ABSENTHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_PUNISHMENT", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_PUNISHMENT, PER_PERSONAL SET PER_PUNISHMENT.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_PUNISHMENT.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_PUNISHMENT A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_SERVICEHIS", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_SERVICEHIS, PER_PERSONAL SET PER_SERVICEHIS.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_SERVICEHIS.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_SERVICEHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_REWARDHIS", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_REWARDHIS, PER_PERSONAL SET PER_REWARDHIS.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_REWARDHIS.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_REWARDHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_MARRHIS", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_MARRHIS, PER_PERSONAL SET PER_MARRHIS.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_MARRHIS.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_MARRHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_NAMEHIS", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_NAMEHIS, PER_PERSONAL SET PER_NAMEHIS.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_NAMEHIS.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_NAMEHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_DECORATEHIS", "PER_CARDNO","VARCHAR", "13", "NULL");
			add_field("PER_DECORATEHIS", "DEH_GAZETTE","VARCHAR", "255", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_DECORATEHIS, PER_PERSONAL SET PER_DECORATEHIS.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_DECORATEHIS.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_DECORATEHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_TIMEHIS", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_TIMEHIS, PER_PERSONAL SET PER_TIMEHIS.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
								  WHERE PER_PERSONAL.PER_ID = PER_TIMEHIS.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_TIMEHIS A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_PERSONALPIC", "PER_CARDNO","VARCHAR", "13", "NULL");
			add_field("PER_PERSONALPIC", "PER_PICPATH","VARCHAR", "255", "NULL");
			add_field("PER_PERSONALPIC", "PIC_SERVER_ID","INTEGER", "10", "NULL");
			add_field("PER_PERSONALPIC", "PIC_SIGN","INTEGER2", "5", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_PERSONALPIC, PER_PERSONAL SET PER_PERSONALPIC.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_PERSONALPIC.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_PERSONALPIC A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_COMDTL", "PER_CARDNO","VARCHAR", "13", "NULL");
			add_field("PER_COMDTL", "EP_CODE","VARCHAR", "10", "NULL");
			add_field("PER_COMDTL", "POEMS_ID","INTEGER", "10", "NULL");
			add_field("PER_COMDTL", "EP_CODE_ASSIGN","VARCHAR", "10", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_COMDTL, PER_PERSONAL SET PER_COMDTL.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
								  WHERE PER_PERSONAL.PER_ID = PER_COMDTL.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_COMDTL A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_MOVE_REQ", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_MOVE_REQ, PER_PERSONAL SET PER_MOVE_REQ.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_MOVE_REQ.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_MOVE_REQ A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_PROMOTE_C", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_PROMOTE_C, PER_PERSONAL SET PER_PROMOTE_C.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_PROMOTE_C.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_PROMOTE_C A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_PROMOTE_P", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_PROMOTE_P, PER_PERSONAL SET PER_PROMOTE_P.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_PROMOTE_P.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_PROMOTE_P A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_PROMOTE_E", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_PROMOTE_E, PER_PERSONAL SET PER_PROMOTE_E.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_PROMOTE_E.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_PROMOTE_E A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_SALPROMOTE", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_SALPROMOTE, PER_PERSONAL SET PER_SALPROMOTE.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_SALPROMOTE.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_SALPROMOTE A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_BONUSPROMOTE", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_BONUSPROMOTE, PER_PERSONAL SET PER_BONUSPROMOTE.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_BONUSPROMOTE.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_BONUSPROMOTE A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_ABSENT", "PER_CARDNO","VARCHAR", "13", "NULL");
			add_field("PER_ABSENT", "APPROVE_FLAG","SINGLE", "1", "NULL");
			add_field("PER_ABSENT", "APPROVE_PER_ID","INTEGER", "10", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_ABSENT, PER_PERSONAL SET PER_ABSENT.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
								  WHERE PER_PERSONAL.PER_ID = PER_ABSENT.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_ABSENT A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_INVEST1DTL", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_INVEST1DTL, PER_PERSONAL SET PER_INVEST1DTL.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_INVEST1DTL.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_INVEST1DTL A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_INVEST2DTL", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_INVEST2DTL, PER_PERSONAL SET PER_INVEST2DTL.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_INVEST2DTL.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_INVEST2DTL A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_SCHOLAR", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_SCHOLAR, PER_PERSONAL SET PER_SCHOLAR.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_SCHOLAR.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_SCHOLAR A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_COURSEDTL", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_COURSEDTL, PER_PERSONAL SET PER_COURSEDTL.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_COURSEDTL.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_COURSEDTL A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_DECORDTL", "PER_CARDNO","VARCHAR", "13", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_DECORDTL, PER_PERSONAL SET PER_DECORDTL.PER_CARDNO = 
								  PER_PERSONAL.PER_CARDNO WHERE PER_PERSONAL.PER_ID = PER_DECORDTL.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_DECORDTL A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
								  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_LETTER", "PER_CARDNO","VARCHAR", "13", "NULL");
			add_field("PER_LETTER", "LET_ADDITIONAL","VARCHAR", "255", "NULL");

			if($DPISDB=="odbc" || $DPISDB=="mysql") 
				$cmd = " UPDATE PER_LETTER, PER_PERSONAL SET PER_LETTER.PER_CARDNO = PER_PERSONAL.PER_CARDNO 
								  WHERE PER_PERSONAL.PER_ID = PER_LETTER.PER_ID ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_LETTER A SET A.PER_CARDNO = (SELECT B.PER_CARDNO FROM PER_PERSONAL B 
					 			  WHERE A.PER_ID = B.PER_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_ABSENT", "ABS_REASON","VARCHAR", "255", "NULL");
			add_field("PER_ABSENT", "ABS_ADDRESS","VARCHAR", "255", "NULL");
			add_field("PER_ORG", "ORG_WEBSITE","VARCHAR", "255", "NULL");
			add_field("PER_ORG", "ORG_SEQ_NO","INTEGER2", "5", "NULL");
			add_field("PER_ORG_ASS", "ORG_WEBSITE","VARCHAR", "255", "NULL");
			add_field("PER_ORG_ASS", "ORG_SEQ_NO","INTEGER2", "5", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " ALTER TABLE PER_ORG_ASS ALTER ORG_SHORT VARCHAR(100) ";
			elseif($DPISDB=="oci8") 
				$cmd = " ALTER TABLE PER_ORG_ASS MODIFY ORG_SHORT VARCHAR2(100) ";
			elseif($DPISDB=="mysql") 
				$cmd = " ALTER TABLE PER_ORG_ASS MODIFY ORG_SHORT VARCHAR(100) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_SERVICETITLE", "SV_CODE","VARCHAR", "2", "NULL");

			if($DPISDB=="odbc") 
				$cmd = " UPDATE PER_SERVICETITLE SET SV_CODE = SUBSTR(SRT_CODE,1,2) ";
			elseif($DPISDB=="oci8") 
				$cmd = " UPDATE PER_SERVICETITLE SET SV_CODE = SRT_CODE ";
			elseif($DPISDB=="mysql")
				$cmd = " UPDATE PER_SERVICETITLE SET SV_CODE = SRT_CODE ";
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

			add_field("PER_POSITIONHIS", "POH_ORG1","MEMO", "1000", "NULL");

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

			add_field("PER_POSITIONHIS", "POH_ORG2","MEMO", "1000", "NULL");

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

			add_field("PER_POSITIONHIS", "POH_ORG3","MEMO", "1000", "NULL");

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

			add_field("PER_PERSONAL", "POEMS_ID","INTEGER", "10", "NULL");
			add_field("PER_PERSONAL", "PER_HIP_FLAG","VARCHAR", "20", "NULL");

			$cmd = " UPDATE PER_PERSONAL SET PER_HIP_FLAG = NULL WHERE PER_HIP_FLAG = ' ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			$cmd = " UPDATE PER_PERSONAL SET PER_CERT_OCC = NULL WHERE PER_CERT_OCC = ' ' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			add_field("PER_PERSONAL", "PER_CERT_OCC","VARCHAR", "20", "NULL");
			add_field("PER_TRANSFER_REQ", "TR_POSDATE","VARCHAR", "19", "NULL");

			$cmd = " update PER_CONTROL set CTRL_ALTER = 2 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
?>