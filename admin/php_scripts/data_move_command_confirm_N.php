<?
set_time_limit(0);
		// ��� insert �����Ũҡ per_comdtl � per_personal ��� per_positionhis ��� PER_POS_MOVE
		$cmd = "  	select 	PER_ID, CMD_DATE, POS_ID, POEM_ID, POEMS_ID, POT_ID, CMD_POSITION, 
									CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_ORG6, 
									CMD_ORG7, CMD_ORG8,  LEVEL_NO , CMD_LEVEL_POS , CMD_SALARY,	CMD_SPSALARY, 
									MOV_CODE, PL_CODE_ASSIGN, PN_CODE_ASSIGN, EP_CODE_ASSIGN, TP_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, 
									PL_NAME_WORK, ORG_NAME_WORK, CMD_SEQ, ES_CODE ,CL_NAME ,CMD_POSITION
						from		PER_COMDTL 
						where	COM_ID=$COM_ID";
		$db_dpis->send_cmd($cmd);
//echo "$cmd<br>";		
//$db_dpis->show_error(); echo "<hr>";
		while ($data = $db_dpis->get_array()) {
			$tmp_PER_ID = trim($data[PER_ID]);
			$tmp_CMD_DATE = trim($data[CMD_DATE]);
			$tmp_POS_ID = (trim($data[POS_ID]))? trim($data[POS_ID]) : "NULL";
			$tmp_POEM_ID = (trim($data[POEM_ID]))? trim($data[POEM_ID]) : "NULL";
			$tmp_POEMS_ID = (trim($data[POEMS_ID]))? trim($data[POEMS_ID]) : "NULL";
			$tmp_POT_ID = (trim($data[POT_ID]))? trim($data[POT_ID]) : "NULL";
			//##################
			$tmp_data = explode("\|", trim($data[CMD_POSITION]));
			$CMD_POSITION = $tmp_data[0];
			$tmp_POS_NO = trim($tmp_data[1]);		//�Ţ�����˹�����
                        
                        $CMD_POSITION_ASS = $tmp_data[2];
			$tmp_POS_NO_ASS = trim($tmp_data[3]);
                        
                       if(count($tmp_data)==4){
                          
                                $CMD_POSITION_ASS = $tmp_data[2];
                                $tmp_POS_NO_ASS = $tmp_data[3];
                        }
                         if(count($tmp_data)==5){/*�������*/
                                 $CMD_POSITION_ASS = $tmp_data[3];
                                $tmp_POS_NO_ASS = trim($tmp_data[4]);
                          }
			//==================================================
			$CMD_ORG1 = trim($data[CMD_ORG1]); 
			$CMD_ORG2 = trim($data[CMD_ORG2]); 
                        
                        $tmp_cmd_org2 = explode("\|", trim($data[CMD_ORG2]));
			$CMD_ORG2 = trim($tmp_cmd_org2[0]);
			$CMD_DEPARTMENT_ID = (trim($tmp_cmd_org2[1]))? trim($tmp_cmd_org2[1]) : "NULL"; //--DEPARTMENT_ID
			
			$tmp_org3 = explode("\|", trim($data[CMD_ORG3]));
			$CMD_ORG3 = trim($tmp_org3[0]);
			$ORG_ID = (trim($tmp_org3[1]))? trim($tmp_org3[1]) : "NULL"; //--ORG_ID
			
			$tmp_org4= explode("\|", trim($data[CMD_ORG4]));
			$CMD_ORG4 = trim($tmp_org4[0]);
			$ORG_ID_1 = (trim($tmp_org4[1]))? trim($tmp_org4[1]) : "NULL"; //--ORG_ID_1
			
			$tmp_org5 = explode("\|", trim($data[CMD_ORG5]));
			$CMD_ORG5 = trim($tmp_org5[0]);
			$ORG_ID_2 = (trim($tmp_org5[1]))? trim($tmp_org5[1]) : "NULL"; //--ORG_ID_2

			$tmp_org6 = explode("\|", trim($data[CMD_ORG6]));
			$CMD_ORG6 = trim($tmp_org6[0]);
			$ORG_ID_3 = (trim($tmp_org6[1]))? trim($tmp_org6[1]) : "NULL"; //--ORG_ID_3
			
			$tmp_org7= explode("\|", trim($data[CMD_ORG7]));
			$CMD_ORG7 = trim($tmp_org7[0]);
			$ORG_ID_4 = (trim($tmp_org7[1]))? trim($tmp_org7[1]) : "NULL"; //--ORG_ID_4
			
			$tmp_org8 = explode("\|", trim($data[CMD_ORG8]));
			$CMD_ORG8 = trim($tmp_org8[0]);
			$ORG_ID_5 = (trim($tmp_org8[1]))? trim($tmp_org8[1]) : "NULL"; //--ORG_ID_5

			$tmp_LEVEL_NO = trim($data[LEVEL_NO]);								// �дѺ�ؤ��
			$tmp_LEVEL_NO_POS = trim($data[CMD_LEVEL_POS]);		// �дѺ���˹�
			$tmp_CMD_SALARY = trim($data[CMD_SALARY]);
			$tmp_CMD_SPSALARY = trim($data[CMD_SPSALARY]);
			$tmp_MOV_CODE = trim($data[MOV_CODE]);
			$tmp_PL_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]);
			$tmp_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]);
			$tmp_EP_CODE_ASSIGN = trim($data[EP_CODE_ASSIGN]);
			$tmp_TP_CODE_ASSIGN = trim($data[TP_CODE_ASSIGN]);
			$tmp_CMD_NOTE1 = (trim($data[CMD_NOTE1]))? str_replace("'", "&rsquo;", trim($data[CMD_NOTE1])) : "";
			$tmp_CMD_NOTE2 = (trim($data[CMD_NOTE2]))? str_replace("'", "&rsquo;", trim($data[CMD_NOTE2])) : "";
			$tmp_PL_NAME_WORK = trim($data[PL_NAME_WORK]);		
			$tmp_ORG_NAME_WORK = trim($data[ORG_NAME_WORK]);		
			$tmp_CMD_SEQ = trim($data[CMD_SEQ]);
			$tmp_ES_CODE = trim($data[ES_CODE]);
			$tmp_CL_NAME = trim($data[CL_NAME]);
			/*cdgs*/
			$strCmdPosition=trim($data[CMD_POSITION]);
			/*cdgs*/
                        
                       /* $tmp_CL_NAME = trim();
                        $tmp_PM_CODE = trim();*/

			// update status of PER_PERSONAL 
                        /* ����¹ $tmp_LEVEL_NO ==>$tmp_LEVEL_NO_POS ,*/
			$cmd = " 	update PER_PERSONAL set  POS_ID=$tmp_POS_ID, POEM_ID=$tmp_POEM_ID, POEMS_ID=$tmp_POEMS_ID, 
								POT_ID=$tmp_POT_ID, LEVEL_NO='$tmp_LEVEL_NO', LEVEL_NO_SALARY='$tmp_LEVEL_NO', MOV_CODE='$tmp_MOV_CODE', 
								PER_STATUS=1, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE',
                                                                DEPARTMENT_ID= $CMD_DEPARTMENT_ID    
					where PER_ID=$tmp_PER_ID ";
			// PER_SALARY=$tmp_CMD_SALARY, PER_SPSALARY=$tmp_CMD_SPSALARY, 					
			$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();	
                        //echo "<pre>$cmd <br>";

			if (is_null($tmp_POS_ID)==false && $tmp_POS_ID!="NULL"){ 
                            $cmd = " SELECT POSITION_LEVEL FROM PER_LEVEL WHERE trim(LEVEL_NO) = '".trim($tmp_LEVEL_NO)."' ";
                            $db_dpis2->send_cmd($cmd);
                            $data2 = $db_dpis2->get_array();
                            //$POSITION_LEVEL = $data2[POSITION_LEVEL];	
                            
                            $POSITION_LEVEL=trim($tmp_CL_NAME);
// ���˹觢���Ҫ���
				/*$cmd = " update PER_POSITION set 
								POS_NO='$tmp_POS_NO',
								ORG_ID=$ORG_ID, ORG_ID_1=$ORG_ID_1, ORG_ID_2=$ORG_ID_2,
								ORG_ID_3=$ORG_ID_3, ORG_ID_4=$ORG_ID_4, ORG_ID_5=$ORG_ID_5,
								PL_CODE='$tmp_PL_CODE_ASSIGN',
								POS_SALARY=$tmp_CMD_SALARY, POS_CHANGE_DATE='$tmp_CMD_DATE', 
								LEVEL_NO='$tmp_LEVEL_NO_POS',
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
						  where POS_ID=$tmp_POS_ID ";*/
                            /*Release 5.2.1.2 Begin*/
                                $cmd = " update PER_POSITION set 
								POS_NO='$tmp_POS_NO',
								ORG_ID=$ORG_ID, ORG_ID_1=$ORG_ID_1, ORG_ID_2=$ORG_ID_2,
								ORG_ID_3=$ORG_ID_3, ORG_ID_4=$ORG_ID_4, ORG_ID_5=$ORG_ID_5,
								PL_CODE='$tmp_PL_CODE_ASSIGN',
								POS_SALARY=$tmp_CMD_SALARY, POS_CHANGE_DATE='$tmp_CMD_DATE', 
								LEVEL_NO='$tmp_LEVEL_NO',
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
                                                                ,PM_CODE='$tmp_POS_NO_ASS' 
                                                                ,CL_NAME ='$POSITION_LEVEL',
                                                                POS_DOC_NO='$COM_NO'   ,
                                                                DEPARTMENT_ID= $CMD_DEPARTMENT_ID    
						  where POS_ID=$tmp_POS_ID ";
                               /*Release 5.2.1.2 End*/  
                                //echo '<pre>'.$cmd;
                                
				$db_dpis1->send_cmd($cmd);
			}elseif (is_null($tmp_POEM_ID)==false && $tmp_POEM_ID!="NULL"){ 						// ���˹��١��ҧ��Ш�
				$cmd = " update PER_POS_EMP set 
								POEM_NO='$tmp_POS_NO',
								ORG_ID=$ORG_ID, ORG_ID_1=$ORG_ID_1, ORG_ID_2=$ORG_ID_2,
								ORG_ID_3=$ORG_ID_3, ORG_ID_4=$ORG_ID_4, ORG_ID_5=$ORG_ID_5,
								PN_CODE='$tmp_PN_CODE_ASSIGN'
						  where POEM_ID=$tmp_POEM_ID ";
				$db_dpis1->send_cmd($cmd);
			
				//	�Ѿഷ PER_POS_NAME 
				$cmd = " update PER_POS_NAME  set LEVEL_NO='$tmp_LEVEL_NO_POS'
								  where 	PN_CODE='$tmp_PN_CODE_ASSIGN' ";
				$db_dpis1->send_cmd($cmd);	
			}elseif (is_null($tmp_POEMS_ID)==false && $tmp_POEMS_ID!="NULL"){ 						// ���˹觾�ѡ�ҹ�Ҫ���
				$cmd = " update PER_POS_EMPSER set 
								POEMS_NO='$tmp_POS_NO',
								ORG_ID=$ORG_ID, ORG_ID_1=$ORG_ID_1, ORG_ID_2=$ORG_ID_2,
								ORG_ID_3=$ORG_ID_3, ORG_ID_4=$ORG_ID_4, ORG_ID_5=$ORG_ID_5,
								EP_CODE ='$tmp_EP_CODE_ASSIGN'
						  where POEMS_ID=$tmp_POEMS_ID ";
				$db_dpis1->send_cmd($cmd);
				
				//	�Ѿഷ PER_EMPSER_POS_NAME 
				$cmd = " update PER_EMPSER_POS_NAME set LEVEL_NO='$tmp_LEVEL_NO_POS'
								  where 	EP_CODE ='$tmp_EP_CODE_ASSIGN' ";
				$db_dpis1->send_cmd($cmd);	
			}elseif (is_null($tmp_POT_ID)==false && $tmp_POT_ID!="NULL"){ 						// ���˹��١��ҧ���Ǥ���
				$cmd = " update PER_POS_TEMP set 
								POT_NO='$tmp_POS_NO',
								ORG_ID=$ORG_ID, ORG_ID_1=$ORG_ID_1, ORG_ID_2=$ORG_ID_2,
								ORG_ID_3=$ORG_ID_3, ORG_ID_4=$ORG_ID_4, ORG_ID_5=$ORG_ID_5,
								TP_CODE='$tmp_TP_CODE_ASSIGN'
						  where POT_ID=$tmp_POT_ID ";
				$db_dpis1->send_cmd($cmd);
			}
//			echo "$cmd <br>";
			
			$tmp_date = explode("-", $tmp_CMD_DATE);
			// 86400 �Թҷ� = 1 �ѹ
			$before_cmd_date = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2), $tmp_date[0]) - 86400);
			$before_cmd_date = date("Y-m-d", $before_cmd_date);
			
			$PM_CODE = $LEVEL_NO = $PT_CODE = $POH_ASS_ORG = "";
			$PL_CODE = $PN_CODE = $EP_CODE = $TP_CODE = "NULL";			
			$cmd = " select ORG_NAME from PER_PERSONAL a, PER_ORG_ASS b where PER_ID=$tmp_PER_ID and a.ORG_ID=b.ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_POS_NO = $tmp_POS_NO;
			$POH_ASS_ORG = $data2[ORG_NAME];

			$ORG_NAME_1 = $ORG_NAME_2 = $ORG_NAME_3 = $ORG_NAME_4 = $ORG_NAME_5 = $ORG_NAME_6 = "";
			if (trim($tmp_POS_ID) && $tmp_POS_ID!="NULL") {									// ���˹觢���Ҫ���
				$cmd = "  select POS_NO, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, PM_CODE, PL_CODE, PT_CODE 
							   from PER_POSITION where POS_ID=$tmp_POS_ID  ";
				$db_dpis2->send_cmd($cmd);
				//echo $cmd."<hr>";
				$data2 = $db_dpis2->get_array();
				$PM_CODE = trim($data2[PM_CODE]);
				$PM_CODE = (trim($PM_CODE))? "'$PM_CODE'" : "NULL";   //$PM_CODE = (trim($PM_CODE))? trim($PM_CODE) : "NULL"; 
				
				$PL_CODE = trim($data2[PL_CODE]);
				$PL_CODE = (trim($PL_CODE))? "'$PL_CODE'" : "NULL";	
				$PT_CODE = trim($data2[PT_CODE]);
				//cdgs
				$strPosIDDpis=$tmp_POS_ID;
				//cdgs
			} else if (trim($tmp_POEM_ID) && $tmp_POEM_ID!="NULL") {						// ���˹��١��ҧ��Ш�
				$cmd = "  select POEM_NO, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, PN_CODE 
							   from PER_POS_EMP where POEM_ID=$tmp_POEM_ID  ";	
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PN_CODE = "'".trim($data2[PN_CODE])."'";
				//cdgs
				$strPosIDDpis=$tmp_POEM_ID;
				//cdgs
			} else if (trim($tmp_POEMS_ID) && $tmp_POEMS_ID!="NULL") {						// ���˹觾�ѡ�ҹ�Ҫ���
				$cmd = "  select POEMS_NO, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, EP_CODE 
							   from PER_POS_EMPSER where POEMS_ID=$tmp_POEMS_ID  ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$EP_CODE = "'".trim($data2[EP_CODE])."'";
				//cdgs
				$strPosIDDpis=$tmp_POEMS_ID;
				//cdgs 
			} else if (trim($tmp_POT_ID) && $tmp_POT_ID!="NULL") {						// ���˹��١��ҧ���Ǥ���
				$cmd = "  select POT_NO, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, TP_CODE 
							   from PER_POS_TEMP where POT_ID=$tmp_POT_ID  ";	
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TP_CODE = "'".trim($data2[TP_CODE])."'";
			}
			$ORG_ID_3 = $ORG_ID;	 
                        //���
			//$ORG_ID_4 = trim($data2[ORG_ID_1]);
                        //$ORG_ID_5 = trim($data2[ORG_ID_2]);
                        $ORG_ID_4 = (trim($data2[ORG_ID_1]))? trim($data2[ORG_ID_1]) : "NULL"; 
			$ORG_ID_5 = (trim($data2[ORG_ID_2]))? trim($data2[ORG_ID_2]) : "NULL";
			$ORG_ID_6 = trim($data2[ORG_ID_3]);	 
			$ORG_ID_7 = trim($data2[ORG_ID_4]);
			$ORG_ID_8 = trim($data2[ORG_ID_5]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_4 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_4 = $data2[ORG_NAME];

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_5 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_5 = $data2[ORG_NAME];				
			 			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_6 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_6 = $data2[ORG_NAME];

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_7 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_7 = $data2[ORG_NAME];				
			 			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_8 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_8 = $data2[ORG_NAME];				
			 			
			$cmd = "  select ORG_ID_REF, CT_CODE, PV_CODE, AP_CODE from PER_ORG where ORG_ID=$ORG_ID_3 and OL_CODE='03'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_ID_2 = (trim($data2[ORG_ID_REF]))? trim($data2[ORG_ID_REF]) : "NULL";			
			$CT_CODE = (trim($data2[CT_CODE]))? "'".trim($data2[CT_CODE])."'" : "NULL";
			$PV_CODE = (trim($data2[PV_CODE]))? "'".trim($data2[PV_CODE])."'" : "NULL";
			$AP_CODE = (trim($data2[AP_CODE]))? "'".trim($data2[AP_CODE])."'" : "NULL";
			$cmd = "  select ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID_2 and OL_CODE='02'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_ID_1 = trim($data2[ORG_ID_REF]);
			$ORG_ID_1 = (trim($ORG_ID_1))? trim($ORG_ID_1) : "NULL";
			$ORG_ID_2 = (trim($ORG_ID_2))? trim($ORG_ID_2) : "NULL";
			$ORG_ID_3 = (trim($ORG_ID_3))? trim($ORG_ID_3) : "NULL";				
			$cmd = " select ORG_ID, ORG_NAME FROM PER_ORG where ORG_ID in ( $ORG_ID_1, $ORG_ID_2, $ORG_ID_3 ) ";
			$db_dpis2->send_cmd($cmd);
			while ($data2 = $db_dpis2->get_array()) {
				$ORG_NAME_1 = ($data2[ORG_ID] == $ORG_ID_1)? trim($data2[ORG_NAME]) : $ORG_NAME_1;
				$ORG_NAME_2 = ($data2[ORG_ID] == $ORG_ID_2)? trim($data2[ORG_NAME]) : $ORG_NAME_2;
				$ORG_NAME_3 = ($data2[ORG_ID] == $ORG_ID_3)? trim($data2[ORG_NAME]) : $ORG_NAME_3;
			} 
			$ORG_NAME_2=$CMD_ORG2;
			// update and insert into PER_POSITIONHIS	
			$cmd = " update PER_POSITIONHIS set POH_LAST_POSITION='N' where PER_ID=$tmp_PER_ID ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();

			// update and insert into PER_POSITIONHIS
			$cmd = " select POH_ID,POH_EFFECTIVEDATE  from PER_POSITIONHIS where PER_ID=$tmp_PER_ID order by PER_ID, POH_EFFECTIVEDATE desc, POH_SEQ_NO desc ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$tmp_POH_ID = trim($data1[POH_ID]);
			$tmp_POH_EFFECTIVEDATE = trim($data1[POH_EFFECTIVEDATE]);
			if($tmp_POH_EFFECTIVEDATE==$tmp_CMD_DATE	){		 //����ѹ����ռŷ�����͡�ҹ�� = �ѹ����ռŢͧ�ä��������ش��͹˹�ҹ�����  POH_ENDDATE=�ѹ����ռ����   //21/12/2011
				$tmp_END_DATE=$tmp_CMD_DATE;
			}else{
				$tmp_END_DATE=$before_cmd_date;
			}
			$cmd = " update PER_POSITIONHIS set POH_ENDDATE='$tmp_END_DATE' where POH_ID=$tmp_POH_ID";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();				
			//echo "$cmd <br>";
			
			$tmp_PL_CODE_ASSIGN = (trim($tmp_PL_CODE_ASSIGN))? "'".trim($tmp_PL_CODE_ASSIGN)."'" : "NULL";
			$tmp_PN_CODE_ASSIGN = (trim($tmp_PN_CODE_ASSIGN))? "'".trim($tmp_PN_CODE_ASSIGN)."'" : "NULL";
			$tmp_EP_CODE_ASSIGN = (trim($tmp_EP_CODE_ASSIGN))? "'".trim($tmp_EP_CODE_ASSIGN)."'" : "NULL";
			$tmp_TP_CODE_ASSIGN = (trim($tmp_TP_CODE_ASSIGN))? "'".trim($tmp_TP_CODE_ASSIGN)."'" : "NULL";
			
			$PT_CODE = (trim($PT_CODE))? "'$PT_CODE'" : "NULL";
			$PM_CODE = (trim($PM_CODE))? "$PM_CODE" : "NULL";
			$cmd = " select max(POH_ID) as max_id from PER_POSITIONHIS ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$data1 = array_change_key_case($data1, CASE_LOWER);
			$POH_ID = $data1[max_id] + 1; 			 
			$cmd = "insert into PER_POSITIONHIS (POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, 
                                        POH_DOCNO, POH_DOCDATE, POH_ENDDATE, POH_POS_NO, POH_POS_NO_NAME, PM_CODE, LEVEL_NO, 
                                        PL_CODE, PN_CODE, EP_CODE, TP_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, POH_ORGMGT, 
                                        POH_LEVEL_NO, ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_UNDER_ORG3, 
                                        POH_UNDER_ORG4, POH_UNDER_ORG5, POH_ASS_ORG, POH_SALARY, POH_SALARY_POS, 
                                        POH_REMARK, POH_REMARK1, POH_REMARK2, POH_ORG1, POH_ORG2, POH_ORG3, POH_PL_NAME, POH_ORG, POH_CMD_SEQ,
                                        POH_LAST_POSITION, POH_ISREAL, ES_CODE, UPDATE_USER, UPDATE_DATE)
                                values ($POH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_MOV_CODE', '$COM_NO', '$tmp_COM_DATE', '', 
                                        '$POH_POS_NO', '$POH_POS_NO_NAME', $PM_CODE, '$tmp_LEVEL_NO_POS', $tmp_PL_CODE_ASSIGN, 
                                        $tmp_PN_CODE_ASSIGN, $tmp_EP_CODE_ASSIGN, $tmp_TP_CODE_ASSIGN, $PT_CODE, 
                                        $CT_CODE, $PV_CODE, $AP_CODE, '2','$tmp_LEVEL_NO', $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, '$ORG_NAME_4', 
                                        '$ORG_NAME_5', '$ORG_NAME_6', '$ORG_NAME_7', '$ORG_NAME_8', '$POH_ASS_ORG', 
                                        $tmp_CMD_SALARY, $tmp_CMD_SPSALARY, '$COM_NOTE', '$tmp_CMD_NOTE1', '$tmp_CMD_NOTE2', '$ORG_NAME_1', 
                                        '$ORG_NAME_2', '$ORG_NAME_3', '$tmp_PL_NAME_WORK', '$tmp_ORG_NAME_WORK', 
                                        $tmp_CMD_SEQ, 'Y', 'Y', '$tmp_ES_CODE', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis1->send_cmd($cmd);
			//echo "[1] $cmd <br>";
//			$db_dpis1->show_error();

			//��������Ѻ����ѵԵ��˹觢���Ҫ���
			//��˹��ѹ�������¹�ŧ
//			$POS_DATE = date("Y-m-d");
			$POS_DATE = $tmp_CMD_DATE;
			$cmd = " select POS_ID from PER_POS_MOVE where POS_ID=$tmp_POS_ID and POS_DATE='$POS_DATE' ";
			$count_duplicate = $db_dpis2->send_cmd($cmd);
			if($count_duplicate <= 0){		
				//==����ͧ�����Ӥѭ%%%%%%%%%%�������¹�����ѹ�Ҩ�����������***************
				$POS_MGTSALARY = (trim($POS_MGTSALARY))? trim($POS_MGTSALARY) : 0;
				$PAY_NO = (trim($PAY_NO))? trim($PAY_NO) : "NULL";
				$PC_CODE = (trim($PC_CODE))? trim($PC_CODE) : "NULL";
				$SKILL_CODE = (trim($SKILL_CODE))? trim($SKILL_CODE) : "";
				$OT_CODE = (trim($OT_CODE))? trim($OT_CODE) : "01";
				if(!trim($POS_STATUS))	$POS_STATUS=3; 	// �դ���ͧ
				//�� CL_NAME
				//$cmd = " select CL_NAME from PER_CO_LEVEL where LEVEL_NO_MIN='$tmp_LEVEL_NO' ";
				//$db_dpis1->send_cmd($cmd);
				//$data1 = $db_dpis1->get_array();
				$CL_NAME=$tmp_CL_NAME ;//$data1['CL_NAME'];
				//$POS_DOC_NO
				$cmd = " insert into PER_POS_MOVE (POS_ID, POS_DATE, ORG_ID, OT_CODE, ORG_ID_1, ORG_ID_2, PM_CODE, 
								PL_CODE, CL_NAME, POS_SALARY, POS_MGTSALARY, PT_CODE, PC_CODE, POS_CONDITION,
								POS_STATUS, POS_DOC_NO, POS_REMARK,UPDATE_USER,UPDATE_DATE,  
								DEPARTMENT_ID, PAY_NO, POS_ORGMGT, LEVEL_NO, ORG_ID_3, ORG_ID_4, ORG_ID_5)
								 values ($tmp_POS_ID, '$POS_DATE', $ORG_ID, '$OT_CODE', $ORG_ID_1, $ORG_ID_2, $PM_CODE, $PL_CODE, 
								'$CL_NAME', $tmp_CMD_SALARY, $POS_MGTSALARY, $PT_CODE, $PC_CODE, '$POS_CONDITION',$POS_STATUS, 
								'$COM_NO', '$POS_REMARK', $SESS_USERID, '$UPDATE_DATE', $DEPARTMENT_ID, $PAY_NO, '$POS_ORGMGT', 
								'$tmp_LEVEL_NO_POS', $ORG_ID_3, $ORG_ID_4, $ORG_ID_5) ";
				$db_dpis1->send_cmd($cmd);
				//echo "[2] $cmd <br>";
				/*cdgs*/
				$aa=f_promote_person(f_get_personID($tmp_PER_ID),$strPosIDDpis,$strCmdPosition,$tmp_LEVEL_NO,$tmp_CMD_LEVEL,$tmp_CMD_DATE,f_get_movement_code($tmp_MOV_CODE),$tmp_CMD_DATE);
				/*cdgs*/
//				$db_dpis1->show_error();
			} //end if	
			//echo "$DPISDB  $count_duplicate --- $cmd :: <br>";
		}	// end while

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢢����š�ô�ç���˹� �����������ѵԡ�ô�ç���˹�������׹�ѹ�����źѭ��Ṻ���¤���觨Ѵ��ŧ����ç���ҧ��ǹ�Ҫ������� [$COM_ID : $PER_ID : $POH_ID : $SAH_ID]");
?>