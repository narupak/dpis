<?     /////////////////////////////////// ��ǹ��ѧ �ͧ��öʹ���� /////////////////////////////////////////////////
	
		//    cut_string('','','','','','',''); ᾷ���ŵѴ��ͤ��� ��ҵ�Ƿ��������� ���ź1���Ẻarray
		/////////////////////////////////// ��ǹ��ѧ �ͧ��öʹ���� /////////////////////////////////////////////////
	    if(strstr($TMP_LOG_DETAIL,'> C01')){ //�Ѻ��ͤ��������			
		  $detail_id_1 = "";
		}
		else if(strstr($TMP_LOG_DETAIL,'> C02')){ //�Ѻ��ͤ��������	
		  $detail_id_1 = "";
			//if (!is_numeric($TMP_LOG_DETAIL_cut)) $detail_id_1 = "error ��ҷ��Ѵ�͡����������Ţ������";
	    }
		else if(strstr($TMP_LOG_DETAIL,'> C07')){ //�Ѻ��ͤ��������
		 $detail_id_1 = "";
		}
		else if(strstr($TMP_LOG_DETAIL,'> K07')){ //�Ѻ��ͤ��������
			if(strstr($TMP_LOG_DETAIL,',')){
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",",","0","","","","");
			$TMP_LOG_DETAIL_number = cut_string($TMP_LOG_DETAIL,"1",",","1","","","","");
			}
			else {
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","0","","","","");
			$TMP_LOG_DETAIL_number = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			}
		      $cmd_1 = " select a.POS_ID,b.PL_CODE,b.PL_NAME from PER_POSITION a,PER_LINE b
						where a.POS_ID = $TMP_LOG_DETAIL_cut and
					a.PL_CODE = b.PL_CODE";
			$detail_id_1 =  showdata_connect($cmd_1,'PL_NAME')."  ,�Ţ���".$TMP_LOG_DETAIL_number;	
		}
		else if(strstr($TMP_LOG_DETAIL,'> C14')){ //�Ѻ��ͤ��������
		
		}
		else if(strstr($TMP_LOG_DETAIL,'> S01')){ //�Ѻ��ͤ��������
		  $detail_id_1 = "";		  
		}
		else if(strstr($TMP_LOG_DETAIL,'> S02') && strstr($TMP_LOG_DETAIL,'[')){ //�Ѻ��ͤ��������	
				 if(strstr($TMP_LOG_DETAIL,'���˹觢���Ҫ���')){
					$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
					$TMP_LOG_DETAIL_number = cut_string($TMP_LOG_DETAIL,"1",":","2","","","","");
					$cmd_1 = " select a.POS_ID,b.PL_CODE,b.PL_NAME from PER_POSITION a,PER_LINE b
						where a.POS_ID = $TMP_LOG_DETAIL_cut and
						a.PL_CODE = b.PL_CODE";
				$detail_id_1_1 =  showdata_connect($cmd_1,'PL_NAME')." ,�Ţ���".$TMP_LOG_DETAIL_number;	
				
				}else if(strstr($TMP_LOG_DETAIL,'���˹��١��ҧ��Ш�')){
					$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
					$TMP_LOG_DETAIL_number = cut_string($TMP_LOG_DETAIL,"1",":","2","","","","");
					$cmd_1 = " select a.POEM_ID,b.PN_CODE,b.PN_NAME from PER_POS_EMP a,PER_POS_NAME b
						where a.POEM_ID = $TMP_LOG_DETAIL_cut and
						a.PN_CODE = b.PN_CODE";
						$detail_id_1_1 =  showdata_connect($cmd_1,'PN_NAME')." ,�Ţ���".$TMP_LOG_DETAIL_number;
				}else if(strstr($TMP_LOG_DETAIL,'���˹觾�ѡ�ҹ�Ҫ���')){
					$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
					$TMP_LOG_DETAIL_number = cut_string($TMP_LOG_DETAIL,"1",":","2","","","","");
					$cmd_1 = " select a.POEMS_ID,b.EP_CODE,b.EP_NAME from PER_POS_EMPSER a,PER_EMPSER_POS_NAME b
						where a.POEMS_ID = $TMP_LOG_DETAIL_cut and
						a.EP_CODE = b.EP_CODE";
						$detail_id_1_1 =  showdata_connect($cmd_1,'EP_NAME')." ,�Ţ���".$TMP_LOG_DETAIL_number;
				}else if(strstr($TMP_LOG_DETAIL,'���˹��١��ҧ���Ǥ��� ')){
					$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
					$TMP_LOG_DETAIL_number = cut_string($TMP_LOG_DETAIL,"1",":","2","","","","");
					$cmd_1 = " select a.POT_ID,b.TP_CODE,b.TP_NAME from PER_POS_TEMP a,PER_TEMP_POS_NAME b
						where a.POT_ID = $TMP_LOG_DETAIL_cut and
						a.TP_CODE = b.TP_CODE";
						$detail_id_1_1 =  showdata_connect($cmd_1,'TP_NAME')." ,�Ţ���".$TMP_LOG_DETAIL_number;
				}
				$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","0","","","","");
		        $cmd_1 = " select ORG_NAME,DEPARTMENT_ID from PER_ORG
						where DEPARTMENT_ID = $TMP_LOG_DETAIL_cut";
				$detail_id_1_2 =  showdata_connect($cmd_1,'ORG_NAME');
				if(!strstr($TMP_LOG_DETAIL,']'))
				$detail_id_1 = "";
				else
				$detail_id_1 = $detail_id_1_2.":".$detail_id_1_1;
				
		}	
		else if(strstr($TMP_LOG_DETAIL,'> S04')){ //�Ѻ��ͤ��������
			
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","2","","","","");
			$cmd_1 = " select PL_CODE,PL_NAME from PER_LINE 
						where PL_CODE = $TMP_LOG_DETAIL_cut";	
			$detail_id_1_old = showdata_connect($cmd_1,'PL_NAME');	
			$id_detail_1 = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			if(strstr($TMP_LOG_DETAIL,'> �ѹ�֡����ѵԵ��˹�')){
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","0","","","","");
			$cmd_1 = " select a.POS_ID,a.CL_NAME,b.PL_CODE,b.PL_NAME from PER_POSITION a,PER_LINE b
						where POS_ID = $TMP_LOG_DETAIL_cut and
						a.PL_CODE = b.PL_CODE";	
				$detail_id_1_2 =  showdata_connect($cmd_1,'PL_NAME');
				$detail_id_1 = $id_detail_1.",����¹�ҡ".$detail_id_1_old."��".$detail_id_1_2;
				
			
			}
			else if(strstr($TMP_LOG_DETAIL,'> ��Ѻ��ا���˹�')){
			$detail_id_1 = $id_detail_1.",".$detail_id_1_old;
			}
		} 
		else if(strstr($TMP_LOG_DETAIL,'P01 �����źؤ��')){ //�Ѻ��ͤ�������� 
			$TMP_LOG_DETAIL_string = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");	
			    if(strstr($TMP_LOG_DETAIL,'����ѵ��Ҫ��þ����') || 
				strstr($TMP_LOG_DETAIL,'����ѵԡ���Ѻ�Թ��͹') || 
				strstr($TMP_LOG_DETAIL,'����ѵԡ�ô�ç���˹�')||
				strstr($TMP_LOG_DETAIL,'����ѵԡ������¹�ŧ����-ʡ��') || 
				strstr($TMP_LOG_DETAIL,'����ѵԡ������')|| 
				strstr($TMP_LOG_DETAIL,'����ѵԡ���Ѻ�Թ���������')||
				strstr($TMP_LOG_DETAIL,'����ѵԤ�������ö�����') || 
				strstr($TMP_LOG_DETAIL,'����ѵԡ��ͺ��/�٧ҹ/������')|| 
				strstr($TMP_LOG_DETAIL,'����ѵԤ�������Ǫҭ�����')||
				strstr($TMP_LOG_DETAIL,'�����ŷ��ҷ')||
				strstr($TMP_LOG_DETAIL,'�����Ż���ѵԡ����')||
				strstr($TMP_LOG_DETAIL,'����ѵԷҧ�Թ��')||
				strstr($TMP_LOG_DETAIL,'����ѵԤ����դ����ͺ')||
				strstr($TMP_LOG_DETAIL,'����ѵԡ���Ѻ����ͧ�Ҫ�')||
				strstr($TMP_LOG_DETAIL,'����ѵ����ҷ�դٳ ')||
				strstr($TMP_LOG_DETAIL,'��ػ�ѹ������')||
				strstr($TMP_LOG_DETAIL,'����ѵԡ�����֡�ҵ��')||
				strstr($TMP_LOG_DETAIL,'KPI ��ºؤ��')||
				strstr($TMP_LOG_DETAIL,'�����Ť�ͺ����')||
				strstr($TMP_LOG_DETAIL,'����ѵԡ���ѡ���Ҫ���᷹/�ѡ�ҡ��㹵��˹�')||
				strstr($TMP_LOG_DETAIL,'����ѵ��ٻ�Ҿ')||
				strstr($TMP_LOG_DETAIL,'�������ٻ�Ҿ')||
				strstr($TMP_LOG_DETAIL,'�����Ż���ѵ��ѹ��ش�����')||
				strstr($TMP_LOG_DETAIL,'����ѵ��͹حҵ��Сͺ�ԪҪվ')||
				strstr($TMP_LOG_DETAIL,'���͹��ѵ� ͹حҵ��ҧ �')||
				strstr($TMP_LOG_DETAIL,'�ŧҹ����')||
				strstr($TMP_LOG_DETAIL,'����ѵԡ���Ѻ�Ҫ��÷���')||
				strstr($TMP_LOG_DETAIL,'����ѵԡ��任�Сͺ�Ҫվ���')||
				strstr($TMP_LOG_DETAIL,'����ѵԡ���ͺ')||
				strstr($TMP_LOG_DETAIL,'����ѵԡ������ѹ��͹���Դ')||
				strstr($TMP_LOG_DETAIL,'����ѵԡ����� �.�.7')){   
				$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","0","","","","");	
				$cmd_1 = "select PER_ID,PER_NAME,PER_SURNAME from PER_PERSONAL
			            where PER_ID = $TMP_LOG_DETAIL_cut";
				$detail_id_1_1 =  showdata_connect($cmd_1,'PER_NAME');
				$detail_id_1_2 =  showdata_connect($cmd_1,'PER_SURNAME');
				$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2;
				}
			else if (!is_numeric($TMP_LOG_DETAIL_string)) {
			$detail_id_1 = "�դ����������";
			}
		}
		else if(strstr($TMP_LOG_DETAIL,'P02 ��è�/�觵��/�͹ >') || strstr($TMP_LOG_DETAIL,'��è�����觵�� >')){ 
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","0","","","","");
			if(strstr($TMP_LOG_DETAIL,'�����Ţ���Ҫ���/�١��ҧṺ���ºѭ�դ���觺�è�/�Ѻ�͹')){
			$TMP_LOG_DETAIL_PERNAME = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			$cmd_1 = "select a.PER_ID,a.COM_ID,a.PL_NAME_WORK,a.ORG_NAME_WORK,b.PER_ID,b.PER_NAME,b.PER_SURNAME from PER_COMDTL a,PER_PERSONAL b
			            where COM_ID = $TMP_LOG_DETAIL_cut and 
						a.PER_ID = b.PER_ID";
			$detail_id_1_1 =  showdata_connect($cmd_1,'PL_NAME_WORK');
			$detail_id_1_2 =  showdata_connect($cmd_1,'ORG_NAME_WORK');
			$detail_id_1_3 =  showdata_connect($cmd_1,'PER_NAME');
			$detail_id_1_4 =  showdata_connect($cmd_1,'PER_SURNAME');
			
			$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2.":".$detail_id_1_3." ".$detail_id_1_4;
			} //P02 �ѧ�����ú
		}else if(strstr($TMP_LOG_DETAIL,'P03 ����/����͹���˹� >') || strstr($TMP_LOG_DETAIL,'����/����͹���˹� >')){
			if(strstr($TMP_LOG_DETAIL,'ź�����Ż���ѵԡ�â�����')){
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","0","","","","");
			}else if(strstr($TMP_LOG_DETAIL,'�����Ż���ѵԡ�â�����')){
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			}
			$cmd_1 = "select a.PER_ID,a.PER_NAME,a.PER_SURNAME,b.MV_ID,b.PER_ID from PER_PERSONAL a,PER_MOVE_REQ b
			            where MV_ID = $TMP_LOG_DETAIL_cut and
						a.PER_ID = b.PER_ID";
			$detail_id_1_1 =  showdata_connect($cmd_1,'PER_NAME');
			$detail_id_1_2 =  showdata_connect($cmd_1,'PER_SURNAME');
			
			$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2;
			
			
		}else if(strstr($TMP_LOG_DETAIL,'P04 ����͹����Թ��͹ >') || strstr($TMP_LOG_DETAIL,'����͹����Թ��͹ >')||
		 strstr($TMP_LOG_DETAIL,'P04 �Թ��͹')){ 
			//if(strstr($TMP_LOG_DETAIL,'ź�����š������͹����Թ��͹')){
			//$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			 if(strstr($TMP_LOG_DETAIL,'�����š������͹����Թ��͹')){
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","2","","","","");
			$cmd_1 = "select PER_ID,PER_NAME,PER_SURNAME from PER_PERSONAL
			            where PER_ID = $TMP_LOG_DETAIL_cut";
			$detail_id_1_1 =  showdata_connect($cmd_1,'PER_NAME');
			$detail_id_1_2 =  showdata_connect($cmd_1,'PER_SURNAME');
			$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2;
			}else if(strstr($TMP_LOG_DETAIL,'�ѭ��Ṻ���¤��������͹����Թ��͹') || strstr($TMP_LOG_DETAIL,'��������������´�ѭ��Ṻ���¤��������͹����Թ��͹')||
			strstr($TMP_LOG_DETAIL,'�ѭ��Ṻ���¤���觻�Ѻ�ѵ���Թ��͹����')){ 
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","0","","","","");
			$cmd_1 = "select a.PER_ID,a.COM_ID,b.PER_ID,b.PER_NAME,b.PER_SURNAME from PER_COMDTL a,PER_PERSONAL b
			            where COM_ID = $TMP_LOG_DETAIL_cut and 
						a.PER_ID = b.PER_ID";
			$detail_id_1_1 =  showdata_connect($cmd_1,'PER_NAME');
			$detail_id_1_2 =  showdata_connect($cmd_1,'PER_SURNAME');
			$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2;
			}
			//$cmd_1 = "select a.PER_ID,a.COM_ID,a.PL_NAME_WORK,a.ORG_NAME_WORK,b.PER_ID,b.PER_NAME,b.PER_SURNAME from PER_COMDTL a,PER_PERSONAL b
			//            where COM_ID = $TMP_LOG_DETAIL_cut and 
			//			a.PER_ID = b.PER_ID";
			//$detail_id_1_1 =  showdata_connect($cmd_1,'PL_NAME_WORK');
			//$detail_id_1_2 =  showdata_connect($cmd_1,'ORG_NAME_WORK');
			//$detail_id_1_3 =  showdata_connect($cmd_1,'PER_NAME');
			//$detail_id_1_4 =  showdata_connect($cmd_1,'PER_SURNAME');
		}else if(strstr($TMP_LOG_DETAIL,'P0502 �ѭ��Ṻ���¤�����͡�ҡ��ǹ�Ҫ��� >')){
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			$cmd_1 = "select PER_ID,PER_NAME,PER_SURNAME from PER_PERSONAL
			            where PER_ID = $TMP_LOG_DETAIL_cut";
			$detail_id_1_1 =  showdata_connect($cmd_1,'PER_NAME');
			$detail_id_1_2 =  showdata_connect($cmd_1,'PER_SURNAME');
			$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2;
		}else if(strstr($TMP_LOG_DETAIL,'P06 �����/��� >')){
			$TMP_LOG_DETAIL_string = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			if(strstr($TMP_LOG_DETAIL,'���������š����')|| strstr($TMP_LOG_DETAIL,'��䢢����š����')||
				strstr($TMP_LOG_DETAIL,'ź�����š����') || strstr($TMP_LOG_DETAIL,'��駤�ҡ����ҹ������͹حҵ�����')||
				strstr($TMP_LOG_DETAIL,'����������')|| strstr($TMP_LOG_DETAIL,'����ѹ�Ҿѡ��͹����')){  
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			$cmd_1 = "select PER_ID,PER_NAME,PER_SURNAME from PER_PERSONAL
			            where PER_ID = $TMP_LOG_DETAIL_cut";
			$detail_id_1_1 =  showdata_connect($cmd_1,'PER_NAME');
			$detail_id_1_2 =  showdata_connect($cmd_1,'PER_SURNAME');
			$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2;
			}else { 
			$detail_id_1 = "";
			}
			
		}else if(strstr($TMP_LOG_DETAIL,'P08 �֡�ҵ��/�֡ͺ��') || strstr($TMP_LOG_DETAIL,'�֡�ҵ��/�֡ͺ�� >')){  
			if(strstr($TMP_LOG_DETAIL,'�����ż�����֡��/�֡ͺ��'))  
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			else if (strstr($TMP_LOG_DETAIL,'���������š�â������������֡��')|| strstr($TMP_LOG_DETAIL,'��䢢����š�â������������֡��')|| strstr($TMP_LOG_DETAIL,'ź�����š�â������������֡��'))
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","0","","","","");
			
			$cmd_1 = "select PER_ID,PER_NAME,PER_SURNAME from PER_PERSONAL
			            where PER_ID = $TMP_LOG_DETAIL_cut";
			$detail_id_1_1 =  showdata_connect($cmd_1,'PER_NAME');
			$detail_id_1_2 =  showdata_connect($cmd_1,'PER_SURNAME');
			$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2;
			
		
		}else if(strstr($TMP_LOG_DETAIL,'P10 ˹ѧ����Ѻ�ͧ') || strstr($TMP_LOG_DETAIL,'˹ѧ����Ѻ�ͧ >')){  
			if(strstr($TMP_LOG_DETAIL,'��䢢�����')||strstr($TMP_LOG_DETAIL,'����������')||strstr($TMP_LOG_DETAIL,'ź������'))  
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","2","","","","");
			$cmd_1 = "select PER_ID,PER_NAME,PER_SURNAME from PER_PERSONAL
			            where PER_ID = $TMP_LOG_DETAIL_cut";
			$detail_id_1_1 =  showdata_connect($cmd_1,'PER_NAME');
			$detail_id_1_2 =  showdata_connect($cmd_1,'PER_SURNAME');
			$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2;

		}else if(strstr($TMP_LOG_DETAIL,'P14 �Ѵ��ŧ >')){  
			if(strstr($TMP_LOG_DETAIL,'�������١��ҧṺ���ºѭ�դ���觨Ѵ�к����˹��١��ҧ��Ш�')||
			strstr($TMP_LOG_DETAIL,'��䢢����š�ô�ç���˹� �����������ѵԡ�ô�ç���˹�������׹�ѹ�����źѭ��Ṻ���¤���觨Ѵ�к����˹��١��ҧ��Ш�')||
			strstr($TMP_LOG_DETAIL,'Ṻ���ºѭ�դ���觨Ѵ��ŧ��� �ú.����'))  
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			$cmd_1 = "select PER_ID,PER_NAME,PER_SURNAME from PER_PERSONAL
			            where PER_ID = $TMP_LOG_DETAIL_cut";
			$detail_id_1_1 =  showdata_connect($cmd_1,'PER_NAME');
			$detail_id_1_2 =  showdata_connect($cmd_1,'PER_SURNAME');
			$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2;

		}else if(strstr($TMP_LOG_DETAIL,'P15 �ѡ���Ҫ���᷹/�ѡ�ҡ��㹵��˹�/�����Ҫ��� >')){  
			if(strstr($TMP_LOG_DETAIL,'�ѭ��Ṻ���¤�����ѡ���Ҫ���᷹/�ѡ�ҡ��㹵��˹�')||
			strstr($TMP_LOG_DETAIL,'�ѭ��Ṻ���¤�����ͺ���§ҹ/��Ժѵ��Ҫ���᷹')||
			strstr($TMP_LOG_DETAIL,'�ѭ��Ṻ���¤���觪����Ҫ���')||
			strstr($TMP_LOG_DETAIL,'��䢢�����ʶҹС�ô�ç���˹� �����������ѵԡ�ê����Ҫ���������׹�ѹ�����źѭ��Ṻ���¤���觪����Ҫ���')){  
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			$cmd_1 = "select PER_ID,PER_NAME,PER_SURNAME from PER_PERSONAL
			            where PER_ID = $TMP_LOG_DETAIL_cut";
			$detail_id_1_1 =  showdata_connect($cmd_1,'PER_NAME');
			$detail_id_1_2 =  showdata_connect($cmd_1,'PER_SURNAME');
			$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2;
			}
		}else if(strstr($TMP_LOG_DETAIL,'P17 �ѭ��Ṻ���¤����¡��ԡ��������/��䢤���觷��Դ��Ҵ >')){  //�ӵ����ǹ��� 
			if(strstr($TMP_LOG_DETAIL,'���������źѭ��Ṻ���¤����¡��ԡ��������')||
			strstr($TMP_LOG_DETAIL,'��䢢����źѭ��Ṻ���¤����¡��ԡ��������')||
			strstr($TMP_LOG_DETAIL,'ź�����źѭ��Ṻ���¤����¡��ԡ��������')||
			strstr($TMP_LOG_DETAIL,'¡��ԡ��������������׹�ѹ�����źѭ��Ṻ���¤����¡��ԡ��������')){   
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			$cmd_1 = "select COM_ID,COM_NAME from PER_COMMAND
			            where COM_ID = $TMP_LOG_DETAIL_cut ";
			$detail_id_1_1 =  showdata_connect($cmd_1,'COM_NAME');
			}
			if(strstr($TMP_LOG_DETAIL,'¡��ԡ��������������׹�ѹ�����źѭ��Ṻ���¤����¡��ԡ��������')){
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			$cmd_1 = "select COM_ID,COM_NAME from PER_COMMAND
			            where COM_ID = $TMP_LOG_DETAIL_cut ";
			$detail_id_1_2 =  showdata_connect($cmd_1,'COM_NAME');			
			}
			$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2;
			
		}else if(strstr($TMP_LOG_DETAIL,'A02 �дѺ�š�û����Թ���� >')){  
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'A03 �š�û����Թ��û�Ժѵ��Ҫ��� >')){  
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","3","","","","");
			$cmd_1 = "select PER_CARDNO,PER_NAME,PER_SURNAME from PER_PERSONAL
			            where PER_CARDNO = $TMP_LOG_DETAIL_cut ";	
			$detail_id_1_1 =  showdata_connect($cmd_1,'PER_NAME');
			$detail_id_1_2 =  showdata_connect($cmd_1,'PER_SURNAME');	
			$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2;
		}else if(strstr($TMP_LOG_DETAIL,'A04 �ѧ�Ѵ�ͧ�������͹�Թ��͹ >')){  
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'A05 ��ú�����ǧ�Թ������ҳ����͹�Թ��͹ >')){  
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'A06 ���ҧ�ѭ��Ṻ���¤��������͹�Թ��͹ >')){  
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'K01 ��û����Թ�š�û�Ժѵ��Ҫ��� >')){  
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'K02 ��Ǫ���Ѵ >')){ 
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'K03 �ç��� >')){ 
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'K04 ��ṹ��û����Թ���дѺ˹��§ҹ >')){ 
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'K08 ��û����Թ KPI ��ºؤ�� >')){ 
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'K09 ������˹�ҷ������Ѻ�Դ�ͺ��ѡ >')){ 
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'K10 ˹�ҷ������Ѻ�Դ�ͺ��ѡ >')){ 
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'K12 �ҵðҹ���ö�Тͧ�дѺ���˹� >')){ 
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'K13 �����繵��û����Թ�� >')){ 
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'K15 ���ö�Тͧ������§ҹ >')){ 
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'K16 �ҵðҹ���ö�Тͧ���˹觾�ѡ�ҹ�Ҫ��� >')){ 
		     $TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","0","","","","");
			 $cmd_1 = " select a.POS_ID,b.PL_CODE,b.PL_NAME from PER_POSITION a,PER_LINE b
						where a.POS_ID = $TMP_LOG_DETAIL_cut and
					a.PL_CODE = b.PL_CODE";
			$detail_id_1 =  showdata_connect($cmd_1,'PL_NAME');		
		}else if(strstr($TMP_LOG_DETAIL,'K21 ��á�Ш�µ�Ǫ���Ѵ >')){ 
			$detail_id_1 = "";
		}else{   
		$detail_id_1 = "����ա���礤�������ʴ�������";
		}
		
	