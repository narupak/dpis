<?php 
$cmd = " SELECT PM_CODE FROM PER_MGT WHERE PM_NAME = 'ไม่ใช่ตำแหน่งทางบริหาร/วิชาการ' OR 
							  PM_NAME = 'ไม่ใช่ตำแหน่งบริหาร' OR PM_NAME = '-' OR PM_NAME = 'ตำแหน่งใช้ประสบการณ์ (ว)' OR 
							  PM_NAME = 'ตำแหน่งวิชาชีพ (วช.)' OR PM_NAME = 'ตำแหน่งที่มีประสบการณ์ (ว)' OR PM_NAME = 'เชี่ยวชาญเฉพาะ(ชช.)' ";
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
?>