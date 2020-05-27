<?     /////////////////////////////////// ส่วนหลัง ของการถอดรหัส /////////////////////////////////////////////////
	
		//    cut_string('','','','','','',''); แพทเทิ้ลตัดข้อความ เอาตัวที่เท่าไหร่ ก้อลบ1ตามแบบarray
		/////////////////////////////////// ส่วนหลัง ของการถอดรหัส /////////////////////////////////////////////////
	    if(strstr($TMP_LOG_DETAIL,'> C01')){ //จับข้อความที่มี			
		  $detail_id_1 = "";
		}
		else if(strstr($TMP_LOG_DETAIL,'> C02')){ //จับข้อความที่มี	
		  $detail_id_1 = "";
			//if (!is_numeric($TMP_LOG_DETAIL_cut)) $detail_id_1 = "error ค่าที่ตัดออกมาไม่ใช่ตัวเลขทั้งหมด";
	    }
		else if(strstr($TMP_LOG_DETAIL,'> C07')){ //จับข้อความที่มี
		 $detail_id_1 = "";
		}
		else if(strstr($TMP_LOG_DETAIL,'> K07')){ //จับข้อความที่มี
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
			$detail_id_1 =  showdata_connect($cmd_1,'PL_NAME')."  ,เลขที่".$TMP_LOG_DETAIL_number;	
		}
		else if(strstr($TMP_LOG_DETAIL,'> C14')){ //จับข้อความที่มี
		
		}
		else if(strstr($TMP_LOG_DETAIL,'> S01')){ //จับข้อความที่มี
		  $detail_id_1 = "";		  
		}
		else if(strstr($TMP_LOG_DETAIL,'> S02') && strstr($TMP_LOG_DETAIL,'[')){ //จับข้อความที่มี	
				 if(strstr($TMP_LOG_DETAIL,'ตำแหน่งข้าราชการ')){
					$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
					$TMP_LOG_DETAIL_number = cut_string($TMP_LOG_DETAIL,"1",":","2","","","","");
					$cmd_1 = " select a.POS_ID,b.PL_CODE,b.PL_NAME from PER_POSITION a,PER_LINE b
						where a.POS_ID = $TMP_LOG_DETAIL_cut and
						a.PL_CODE = b.PL_CODE";
				$detail_id_1_1 =  showdata_connect($cmd_1,'PL_NAME')." ,เลขที่".$TMP_LOG_DETAIL_number;	
				
				}else if(strstr($TMP_LOG_DETAIL,'ตำแหน่งลูกจ้างประจำ')){
					$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
					$TMP_LOG_DETAIL_number = cut_string($TMP_LOG_DETAIL,"1",":","2","","","","");
					$cmd_1 = " select a.POEM_ID,b.PN_CODE,b.PN_NAME from PER_POS_EMP a,PER_POS_NAME b
						where a.POEM_ID = $TMP_LOG_DETAIL_cut and
						a.PN_CODE = b.PN_CODE";
						$detail_id_1_1 =  showdata_connect($cmd_1,'PN_NAME')." ,เลขที่".$TMP_LOG_DETAIL_number;
				}else if(strstr($TMP_LOG_DETAIL,'ตำแหน่งพนักงานราชการ')){
					$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
					$TMP_LOG_DETAIL_number = cut_string($TMP_LOG_DETAIL,"1",":","2","","","","");
					$cmd_1 = " select a.POEMS_ID,b.EP_CODE,b.EP_NAME from PER_POS_EMPSER a,PER_EMPSER_POS_NAME b
						where a.POEMS_ID = $TMP_LOG_DETAIL_cut and
						a.EP_CODE = b.EP_CODE";
						$detail_id_1_1 =  showdata_connect($cmd_1,'EP_NAME')." ,เลขที่".$TMP_LOG_DETAIL_number;
				}else if(strstr($TMP_LOG_DETAIL,'ตำแหน่งลูกจ้างชั่วคราว ')){
					$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
					$TMP_LOG_DETAIL_number = cut_string($TMP_LOG_DETAIL,"1",":","2","","","","");
					$cmd_1 = " select a.POT_ID,b.TP_CODE,b.TP_NAME from PER_POS_TEMP a,PER_TEMP_POS_NAME b
						where a.POT_ID = $TMP_LOG_DETAIL_cut and
						a.TP_CODE = b.TP_CODE";
						$detail_id_1_1 =  showdata_connect($cmd_1,'TP_NAME')." ,เลขที่".$TMP_LOG_DETAIL_number;
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
		else if(strstr($TMP_LOG_DETAIL,'> S04')){ //จับข้อความที่มี
			
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","2","","","","");
			$cmd_1 = " select PL_CODE,PL_NAME from PER_LINE 
						where PL_CODE = $TMP_LOG_DETAIL_cut";	
			$detail_id_1_old = showdata_connect($cmd_1,'PL_NAME');	
			$id_detail_1 = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			if(strstr($TMP_LOG_DETAIL,'> บันทึกประวัติตำแหน่ง')){
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","0","","","","");
			$cmd_1 = " select a.POS_ID,a.CL_NAME,b.PL_CODE,b.PL_NAME from PER_POSITION a,PER_LINE b
						where POS_ID = $TMP_LOG_DETAIL_cut and
						a.PL_CODE = b.PL_CODE";	
				$detail_id_1_2 =  showdata_connect($cmd_1,'PL_NAME');
				$detail_id_1 = $id_detail_1.",เปลี่ยนจาก".$detail_id_1_old."เป็น".$detail_id_1_2;
				
			
			}
			else if(strstr($TMP_LOG_DETAIL,'> ปรับปรุงตำแหน่ง')){
			$detail_id_1 = $id_detail_1.",".$detail_id_1_old;
			}
		} 
		else if(strstr($TMP_LOG_DETAIL,'P01 ข้อมูลบุคคล')){ //จับข้อความที่มี 
			$TMP_LOG_DETAIL_string = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");	
			    if(strstr($TMP_LOG_DETAIL,'ประวัติราชการพิเศษ') || 
				strstr($TMP_LOG_DETAIL,'ประวัติการรับเงินเดือน') || 
				strstr($TMP_LOG_DETAIL,'ประวัติการดำรงตำแหน่ง')||
				strstr($TMP_LOG_DETAIL,'ประวัติการเปลี่ยนแปลงชื่อ-สกุล') || 
				strstr($TMP_LOG_DETAIL,'ประวัติการสมรส')|| 
				strstr($TMP_LOG_DETAIL,'ประวัติการรับเงินเพิ่มพิเศษ')||
				strstr($TMP_LOG_DETAIL,'ประวัติความสามารถพิเศษ') || 
				strstr($TMP_LOG_DETAIL,'ประวัติการอบรม/ดูงาน/สัมมนา')|| 
				strstr($TMP_LOG_DETAIL,'ประวัติความเชี่ยวชาญพิเศษ')||
				strstr($TMP_LOG_DETAIL,'ข้อมูลทายาท')||
				strstr($TMP_LOG_DETAIL,'ข้อมูลประวัติการลา')||
				strstr($TMP_LOG_DETAIL,'ประวัติทางวินัย')||
				strstr($TMP_LOG_DETAIL,'ประวัติความดีความชอบ')||
				strstr($TMP_LOG_DETAIL,'ประวัติการรับเครื่องราชฯ')||
				strstr($TMP_LOG_DETAIL,'ประวัติเวลาทวีคูณ ')||
				strstr($TMP_LOG_DETAIL,'สรุปวันลาสะสม')||
				strstr($TMP_LOG_DETAIL,'ประวัติการลาศึกษาต่อ')||
				strstr($TMP_LOG_DETAIL,'KPI รายบุคคล')||
				strstr($TMP_LOG_DETAIL,'ข้อมูลครอบครัว')||
				strstr($TMP_LOG_DETAIL,'ประวัติการรักษาราชการแทน/รักษาการในตำแหน่ง')||
				strstr($TMP_LOG_DETAIL,'ประวัติรูปภาพ')||
				strstr($TMP_LOG_DETAIL,'ข้อมูลรูปภาพ')||
				strstr($TMP_LOG_DETAIL,'ข้อมูลประวัติวันหยุดพิเศษ')||
				strstr($TMP_LOG_DETAIL,'ประวัติใบอนุญาตประกอบวิชาชีพ')||
				strstr($TMP_LOG_DETAIL,'มติอนุมัติ อนุญาตต่าง ๆ')||
				strstr($TMP_LOG_DETAIL,'ผลงานดีเด่น')||
				strstr($TMP_LOG_DETAIL,'ประวัติการรับราชการทหาร')||
				strstr($TMP_LOG_DETAIL,'ประวัติการไปประกอบอาชีพอื่น')||
				strstr($TMP_LOG_DETAIL,'ประวัติการสอบ')||
				strstr($TMP_LOG_DETAIL,'ประวัติการแก้ไขวันเดือนปีเกิด')||
				strstr($TMP_LOG_DETAIL,'ประวัติการยืม ก.พ.7')){   
				$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","0","","","","");	
				$cmd_1 = "select PER_ID,PER_NAME,PER_SURNAME from PER_PERSONAL
			            where PER_ID = $TMP_LOG_DETAIL_cut";
				$detail_id_1_1 =  showdata_connect($cmd_1,'PER_NAME');
				$detail_id_1_2 =  showdata_connect($cmd_1,'PER_SURNAME');
				$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2;
				}
			else if (!is_numeric($TMP_LOG_DETAIL_string)) {
			$detail_id_1 = "มีค่าอยู่แล้ว";
			}
		}
		else if(strstr($TMP_LOG_DETAIL,'P02 บรรจุ/แต่งตั้ง/โอน >') || strstr($TMP_LOG_DETAIL,'บรรจุและแต่งตั้ง >')){ 
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","0","","","","");
			if(strstr($TMP_LOG_DETAIL,'ข้อมูลข้าราชการ/ลูกจ้างแนบท้ายบัญชีคำสั่งบรรจุ/รับโอน')){
			$TMP_LOG_DETAIL_PERNAME = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			$cmd_1 = "select a.PER_ID,a.COM_ID,a.PL_NAME_WORK,a.ORG_NAME_WORK,b.PER_ID,b.PER_NAME,b.PER_SURNAME from PER_COMDTL a,PER_PERSONAL b
			            where COM_ID = $TMP_LOG_DETAIL_cut and 
						a.PER_ID = b.PER_ID";
			$detail_id_1_1 =  showdata_connect($cmd_1,'PL_NAME_WORK');
			$detail_id_1_2 =  showdata_connect($cmd_1,'ORG_NAME_WORK');
			$detail_id_1_3 =  showdata_connect($cmd_1,'PER_NAME');
			$detail_id_1_4 =  showdata_connect($cmd_1,'PER_SURNAME');
			
			$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2.":".$detail_id_1_3." ".$detail_id_1_4;
			} //P02 ยังเช็คไม่ครบ
		}else if(strstr($TMP_LOG_DETAIL,'P03 ย้าย/เลื่อนตำแหน่ง >') || strstr($TMP_LOG_DETAIL,'ย้าย/เลื่อนตำแหน่ง >')){
			if(strstr($TMP_LOG_DETAIL,'ลบข้อมูลประวัติการขอย้าย')){
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","0","","","","");
			}else if(strstr($TMP_LOG_DETAIL,'ข้อมูลประวัติการขอย้าย')){
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			}
			$cmd_1 = "select a.PER_ID,a.PER_NAME,a.PER_SURNAME,b.MV_ID,b.PER_ID from PER_PERSONAL a,PER_MOVE_REQ b
			            where MV_ID = $TMP_LOG_DETAIL_cut and
						a.PER_ID = b.PER_ID";
			$detail_id_1_1 =  showdata_connect($cmd_1,'PER_NAME');
			$detail_id_1_2 =  showdata_connect($cmd_1,'PER_SURNAME');
			
			$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2;
			
			
		}else if(strstr($TMP_LOG_DETAIL,'P04 เลื่อนขั้นเงินเดือน >') || strstr($TMP_LOG_DETAIL,'เลื่อนขั้นเงินเดือน >')||
		 strstr($TMP_LOG_DETAIL,'P04 เงินเดือน')){ 
			//if(strstr($TMP_LOG_DETAIL,'ลบข้อมูลการเลื่อนขั้นเงินเดือน')){
			//$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			 if(strstr($TMP_LOG_DETAIL,'ข้อมูลการเลื่อนขั้นเงินเดือน')){
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","2","","","","");
			$cmd_1 = "select PER_ID,PER_NAME,PER_SURNAME from PER_PERSONAL
			            where PER_ID = $TMP_LOG_DETAIL_cut";
			$detail_id_1_1 =  showdata_connect($cmd_1,'PER_NAME');
			$detail_id_1_2 =  showdata_connect($cmd_1,'PER_SURNAME');
			$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2;
			}else if(strstr($TMP_LOG_DETAIL,'บัญชีแนบท้ายคำสั่งเลื่อนขั้นเงินเดือน') || strstr($TMP_LOG_DETAIL,'ข้อมูลรายละเอียดบัญชีแนบท้ายคำสั่งเลื่อนขั้นเงินเดือน')||
			strstr($TMP_LOG_DETAIL,'บัญชีแนบท้ายคำสั่งปรับอัตราเงินเดือนใหม่')){ 
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
		}else if(strstr($TMP_LOG_DETAIL,'P0502 บัญชีแนบท้ายคำสั่งออกจากส่วนราชการ >')){
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			$cmd_1 = "select PER_ID,PER_NAME,PER_SURNAME from PER_PERSONAL
			            where PER_ID = $TMP_LOG_DETAIL_cut";
			$detail_id_1_1 =  showdata_connect($cmd_1,'PER_NAME');
			$detail_id_1_2 =  showdata_connect($cmd_1,'PER_SURNAME');
			$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2;
		}else if(strstr($TMP_LOG_DETAIL,'P06 การลา/สาย >')){
			$TMP_LOG_DETAIL_string = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			if(strstr($TMP_LOG_DETAIL,'เพิ่มข้อมูลการลา')|| strstr($TMP_LOG_DETAIL,'แก้ไขข้อมูลการลา')||
				strstr($TMP_LOG_DETAIL,'ลบข้อมูลการลา') || strstr($TMP_LOG_DETAIL,'ตั้งค่าการใช้งานข้อมูลอนุญาตการลา')||
				strstr($TMP_LOG_DETAIL,'เพิ่มข้อมูล')|| strstr($TMP_LOG_DETAIL,'แก้ไขวันลาพักผ่อนสะสม')){  
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			$cmd_1 = "select PER_ID,PER_NAME,PER_SURNAME from PER_PERSONAL
			            where PER_ID = $TMP_LOG_DETAIL_cut";
			$detail_id_1_1 =  showdata_connect($cmd_1,'PER_NAME');
			$detail_id_1_2 =  showdata_connect($cmd_1,'PER_SURNAME');
			$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2;
			}else { 
			$detail_id_1 = "";
			}
			
		}else if(strstr($TMP_LOG_DETAIL,'P08 ศึกษาต่อ/ฝึกอบรม') || strstr($TMP_LOG_DETAIL,'ศึกษาต่อ/ฝึกอบรม >')){  
			if(strstr($TMP_LOG_DETAIL,'ข้อมูลผู้ลาศึกษา/ฝึกอบรม'))  
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			else if (strstr($TMP_LOG_DETAIL,'เพิ่มข้อมูลการขยายระยะเวลาศึกษา')|| strstr($TMP_LOG_DETAIL,'แก้ไขข้อมูลการขยายระยะเวลาศึกษา')|| strstr($TMP_LOG_DETAIL,'ลบข้อมูลการขยายระยะเวลาศึกษา'))
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","0","","","","");
			
			$cmd_1 = "select PER_ID,PER_NAME,PER_SURNAME from PER_PERSONAL
			            where PER_ID = $TMP_LOG_DETAIL_cut";
			$detail_id_1_1 =  showdata_connect($cmd_1,'PER_NAME');
			$detail_id_1_2 =  showdata_connect($cmd_1,'PER_SURNAME');
			$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2;
			
		
		}else if(strstr($TMP_LOG_DETAIL,'P10 หนังสือรับรอง') || strstr($TMP_LOG_DETAIL,'หนังสือรับรอง >')){  
			if(strstr($TMP_LOG_DETAIL,'แก้ไขข้อมูล')||strstr($TMP_LOG_DETAIL,'เพิ่มข้อมูล')||strstr($TMP_LOG_DETAIL,'ลบข้อมูล'))  
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","2","","","","");
			$cmd_1 = "select PER_ID,PER_NAME,PER_SURNAME from PER_PERSONAL
			            where PER_ID = $TMP_LOG_DETAIL_cut";
			$detail_id_1_1 =  showdata_connect($cmd_1,'PER_NAME');
			$detail_id_1_2 =  showdata_connect($cmd_1,'PER_SURNAME');
			$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2;

		}else if(strstr($TMP_LOG_DETAIL,'P14 จัดคนลง >')){  
			if(strstr($TMP_LOG_DETAIL,'ข้อมูลลูกจ้างแนบท้ายบัญชีคำสั่งจัดระบบตำแหน่งลูกจ้างประจำ')||
			strstr($TMP_LOG_DETAIL,'แก้ไขข้อมูลการดำรงตำแหน่ง และเพิ่มประวัติการดำรงตำแหน่งเมื่อยืนยันข้อมูลบัญชีแนบท้ายคำสั่งจัดระบบตำแหน่งลูกจ้างประจำ')||
			strstr($TMP_LOG_DETAIL,'แนบท้ายบัญชีคำสั่งจัดคนลงตาม พรบ.ใหม่'))  
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			$cmd_1 = "select PER_ID,PER_NAME,PER_SURNAME from PER_PERSONAL
			            where PER_ID = $TMP_LOG_DETAIL_cut";
			$detail_id_1_1 =  showdata_connect($cmd_1,'PER_NAME');
			$detail_id_1_2 =  showdata_connect($cmd_1,'PER_SURNAME');
			$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2;

		}else if(strstr($TMP_LOG_DETAIL,'P15 รักษาราชการแทน/รักษาการในตำแหน่ง/ช่วยราชการ >')){  
			if(strstr($TMP_LOG_DETAIL,'บัญชีแนบท้ายคำสั่งรักษาราชการแทน/รักษาการในตำแหน่ง')||
			strstr($TMP_LOG_DETAIL,'บัญชีแนบท้ายคำสั่งมอบหมายงาน/ปฏิบัติราชการแทน')||
			strstr($TMP_LOG_DETAIL,'บัญชีแนบท้ายคำสั่งช่วยราชการ')||
			strstr($TMP_LOG_DETAIL,'แก้ไขข้อมูลสถานะการดำรงตำแหน่ง และเพิ่มประวัติการช่วยราชการเมื่อยืนยันข้อมูลบัญชีแนบท้ายคำสั่งช่วยราชการ')){  
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			$cmd_1 = "select PER_ID,PER_NAME,PER_SURNAME from PER_PERSONAL
			            where PER_ID = $TMP_LOG_DETAIL_cut";
			$detail_id_1_1 =  showdata_connect($cmd_1,'PER_NAME');
			$detail_id_1_2 =  showdata_connect($cmd_1,'PER_SURNAME');
			$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2;
			}
		}else if(strstr($TMP_LOG_DETAIL,'P17 บัญชีแนบท้ายคำสั่งยกเลิกคำสั่งเดิม/แก้ไขคำสั่งที่ผิดพลาด >')){  //ทำต่อส่วนนี้ 
			if(strstr($TMP_LOG_DETAIL,'เพิ่มข้อมูลบัญชีแนบท้ายคำสั่งยกเลิกคำสั่งเดิม')||
			strstr($TMP_LOG_DETAIL,'แก้ไขข้อมูลบัญชีแนบท้ายคำสั่งยกเลิกคำสั่งเดิม')||
			strstr($TMP_LOG_DETAIL,'ลบข้อมูลบัญชีแนบท้ายคำสั่งยกเลิกคำสั่งเดิม')||
			strstr($TMP_LOG_DETAIL,'ยกเลิกคำสั่งเดิมเมื่อยืนยันข้อมูลบัญชีแนบท้ายคำสั่งยกเลิกคำสั่งเดิม')){   
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			$cmd_1 = "select COM_ID,COM_NAME from PER_COMMAND
			            where COM_ID = $TMP_LOG_DETAIL_cut ";
			$detail_id_1_1 =  showdata_connect($cmd_1,'COM_NAME');
			}
			if(strstr($TMP_LOG_DETAIL,'ยกเลิกคำสั่งเดิมเมื่อยืนยันข้อมูลบัญชีแนบท้ายคำสั่งยกเลิกคำสั่งเดิม')){
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","1","","","","");
			$cmd_1 = "select COM_ID,COM_NAME from PER_COMMAND
			            where COM_ID = $TMP_LOG_DETAIL_cut ";
			$detail_id_1_2 =  showdata_connect($cmd_1,'COM_NAME');			
			}
			$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2;
			
		}else if(strstr($TMP_LOG_DETAIL,'A02 ระดับผลการประเมินย่อย >')){  
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'A03 ผลการประเมินการปฏิบัติราชการ >')){  
			$TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","3","","","","");
			$cmd_1 = "select PER_CARDNO,PER_NAME,PER_SURNAME from PER_PERSONAL
			            where PER_CARDNO = $TMP_LOG_DETAIL_cut ";	
			$detail_id_1_1 =  showdata_connect($cmd_1,'PER_NAME');
			$detail_id_1_2 =  showdata_connect($cmd_1,'PER_SURNAME');	
			$detail_id_1 = $detail_id_1_1.":".$detail_id_1_2;
		}else if(strstr($TMP_LOG_DETAIL,'A04 สังกัดของการเลื่อนเงินเดือน >')){  
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'A05 การบริหารวงเงินงบประมาณเลื่อนเงินเดือน >')){  
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'A06 สร้างบัญชีแนบท้ายคำสั่งเลื่อนเงินเดือน >')){  
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'K01 การประเมินผลการปฏิบัติราชการ >')){  
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'K02 ตัวชี้วัด >')){ 
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'K03 โครงการ >')){ 
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'K04 คะแนนการประเมินผลระดับหน่วยงาน >')){ 
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'K08 การประเมิน KPI รายบุคคล >')){ 
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'K09 ประเภทหน้าที่ความรับผิดชอบหลัก >')){ 
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'K10 หน้าที่ความรับผิดชอบหลัก >')){ 
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'K12 มาตรฐานสมรรถนะของระดับตำแหน่ง >')){ 
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'K13 เปอร์เซ็นต์การประเมินผล >')){ 
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'K15 สมรรถนะของแต่ละสายงาน >')){ 
			$detail_id_1 = "";
		}else if(strstr($TMP_LOG_DETAIL,'K16 มาตรฐานสมรรถนะของตำแหน่งพนักงานราชการ >')){ 
		     $TMP_LOG_DETAIL_cut = cut_string($TMP_LOG_DETAIL,"1",":","0","","","","");
			 $cmd_1 = " select a.POS_ID,b.PL_CODE,b.PL_NAME from PER_POSITION a,PER_LINE b
						where a.POS_ID = $TMP_LOG_DETAIL_cut and
					a.PL_CODE = b.PL_CODE";
			$detail_id_1 =  showdata_connect($cmd_1,'PL_NAME');		
		}else if(strstr($TMP_LOG_DETAIL,'K21 การกระจายตัวชี้วัด >')){ 
			$detail_id_1 = "";
		}else{   
		$detail_id_1 = "ไม่มีการเช็คค่าเพื่อแสดงข้อมูล";
		}
		
	