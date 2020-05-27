<?

		class PDF extends FPDF  
		{
			function Open()
			{
				// extend from function Open() 
				FPDF::Open();
				// Add fonts use in document
				if(FPDF_VERSION > '1.51'){
					$this->AddFont('angsab','','../../PDF/font/angsab.php');
					$this->AddFont('angsa','','../../PDF/font/angsa.php');
					$this->AddFont('cordia','','../../PDF/font/cordia.php');
					$this->AddFont('cordiab','','../../PDF/font/cordiab.php');
				}else{
					$this->AddFont('angsab','','../../PDF/font/angsab_old.php');
					$this->AddFont('angsa','','../../PDF/font/angsa_old.php');
					$this->AddFont('cordia','','../../PDF/font/cordia.php');
					$this->AddFont('cordiab','','../../PDF/font/cordiab.php');
				}
			}

			function Header()
			{
 				$this->SetTextColor(0,0,96);
 			    $this->SetFont('angsab','',14);

				if($this->report_title){		
					$t_title = explode("||",$this->report_title);
					if(count($t_title) == 1){      
						 //     มี heading report แค่ 1 line  -->  ชื่อรายงาน
//						if($this->company_name){ 
//			 			    $this->SetFont('angsa','',10);
//							$this->Cell(20,6,$this->company_name,0,0,'L');
//						} // end if
//						$this->x = $this->lMargin;
		 			    $this->SetFont('angsab','',14);
						$this->Cell(0,6,$t_title[0],0,1,'C');
//						$this->x = $this-> w -  55;
//						$this->Cell(50,6,$this->report_code,0,1,'R');
					}else{		
						//     มี heading report  > 1 line  -->  มีชื่อกรม ตามด้วย ชื่อรายงาน และ heading อื่น ๆ
						for($i=0; $i < count($t_title); $i++ ){
							if($i == (count($t_title) - 1)){		
//								if($this->company_name){ 
//					 			    $this->SetFont('angsa','',10);
//									$this->Cell(20,6,$this->company_name,0,0,'L');
//								} // end if
//								$this->x = $this->lMargin;
				 			    $this->SetFont('angsab','',14);
								$this->Cell(0,6,$t_title[$i],0,1,'C');
//								$this->x = $this-> w -  55;
//								$this->Cell(50,6,$this->report_code,0,1,'R');
							}else{		
//								$this->SetFont('angsab','',16);
//								$this->SetTextColor(98, 0, 0);
								$this->Cell(0,6,$t_title[$i],0,1,'C');
							} // end if
						} // end for
					} // end if
				
//					$this->SetTextColor(108);
					$this->SetTextColor(0,0,0);
	 			    $this->SetFont('angsa','',10);
					$this->Cell(0,5, $this->company_name,0,1,'L');
//					$this->line($this->lMargin,$this->y,$this->w - $this->rMargin ,$this->y);
				}   //  			if      ($this->report_title)
				
//				if   ($this->heading)
//				{
//						$this->SetTextColor(0, 0, 0);
//						$this->ReportHeader($this->heading,$this->heading_width,$this->heading_align,$this->heading_border);
//						$this->SetTextColor(108);
//						$this->line($this->lMargin,$this->y,$this->w - $this->rMargin ,$this->y);
//					}
				$this->SetTextColor(0, 0, 0);
				$this->flag_new_page = 'Y';
			}

			function Footer($at_end_up=10)
			{	if      ($this->report_title   ||  $this->footer)
					{
					global $NUMBER_DISPLAY;

					$MONTH_TH[] = "เดือน";
					$MONTH_TH[] = "ม.ค.";		$MONTH_TH[] = "ก.พ.";
					$MONTH_TH[] = "มี.ค.";		$MONTH_TH[] = "เม.ย.";
					$MONTH_TH[] = "พ.ค";		$MONTH_TH[] = "มิ.ย.";
					$MONTH_TH[] = "ก.ค.";		$MONTH_TH[] = "ส.ค.";
					$MONTH_TH[] = "ก.ย.";		$MONTH_TH[] = "ต.ค.";
					$MONTH_TH[] = "พ.ย";		$MONTH_TH[] = "ธ.ค.";

					$MONTH_EN[] = "Month";
					$MONTH_EN[] = "Jan";		$MONTH_EN[] = "Feb";
					$MONTH_EN[] = "Mar";		$MONTH_EN[] = "Apr";
					$MONTH_EN[] = "May";		$MONTH_EN[] = "Jun";
					$MONTH_EN[] = "Jul";		$MONTH_EN[] = "Aug";
					$MONTH_EN[] = "Sep";		$MONTH_EN[] = "Oct";
					$MONTH_EN[] = "Nov";		$MONTH_EN[] = "Dec";

					$today = getdate(); 
					$year = $today['year'];
					if  ($this->lang_code == "TH")
							{		$year = $year + 543; 
									$month = $MONTH_TH[$today['mon']]; 
							}
					else
							{		$month = $MONTH_EN[$today['mon']]; 
							}
					$mday = $today['mday'];
					$time = date('H:i:s');
					$mday = (($NUMBER_DISPLAY==2)?convert2thaidigit($mday):$mday);
					$year = (($NUMBER_DISPLAY==2)?convert2thaidigit($year):$year);
					$time = (($NUMBER_DISPLAY==2)?convert2thaidigit($time):$time);
					$page = "{nb}";
					$page = (($NUMBER_DISPLAY==2)?convert2thaidigit($page):$page);
					//Position at 1.0 cm from bottom
					$this->SetY(-$at_end_up);
					$this->SetTextColor(0,0,0);
					$this->SetFont('angsa','',10);
					$this->line($this->lMargin,$this->y,$this->w - $this->rMargin ,$this->y);
					$this->Cell(100,6,($this->report_code?("รายงาน : ".(($NUMBER_DISPLAY==2)?convert2thaidigit($this->report_code):$this->report_code)):""),0,0,"L");		
					$this->x = ($this->w / 2) - 10;
					$this->Cell(10,6,"หน้าที่ : " . (($NUMBER_DISPLAY==2)?convert2thaidigit($this->PageNo()):$this->PageNo())." / $page",0,0,'C');
					$this->x = $this->w - 30;
					$this->Cell(25,6,"วันที่พิมพ์ : ". $mday . " " . $month . " " . $year . " " . $time,0,0,"R");
				}   //	if      ($this->report_title)
			}		

			function Text_print_optimize($text , $width)	
			{	
				while   ($this->GetStringWidth($text)  >  $width)  :
					  {   
							 $text = substr($text,0,strlen($text) -1 );
					   }
				endwhile;
				return $text;
			}

			function h_MultiCellThaiCut($w,$h,$txt,$border=0,$align='J',$fill=0)
			{
				//Output text with automatic or explicit line breaks
				$cw=&$this->CurrentFont['cw'];
				if($w==0)
					$w=$this->w-$this->rMargin-$this->x;
				$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
				$s=str_replace("\r",'',$txt);
				$nb=strlen($s);
				if($nb>0 && $s[$nb-1]=="\n")
					$nb--;
				$s = $this->thaiCutLinePDF($s, $w, "\n");
//				echo "***$s<br>";
				$sub_s = explode("\n", $s);
				$cell_h = count($sub_s) * $h;
				
				return $cell_h;
			} // end function

			function MultiCellThaiCut($w,$h,$txt,$border=0,$align='J',$fill=0,$f_print=true)
			{
				//Output text with automatic or explicit line breaks
//				$border="TRHBL";
//				$fill = "FF0000";
				$cw=&$this->CurrentFont['cw'];
				if($w==0)
					$w=$this->w-$this->rMargin-$this->x;
				$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
				$s=str_replace("\r",'',$txt);
				$nb=strlen($s);
				if($nb>0 && $s[$nb-1]=="\n")
					$nb--;
				$b=0;
				if($border)
				{
					if($border==1)
					{
						$border='LTRB';
						$b='LRT';
						$b2='LR';
					}
					else
					{
						$b2='';
						if(strpos($border,'L')!==false)
							$b2.='L';
						if(strpos($border,'R')!==false)
							$b2.='R';
						$b=(strpos($border,'T')!==false) ? $b2.'T' : $b2;
					}
				}
				$s = $this->thaiCutLinePDF($s, $w, "\n");
//				echo "***$s<br>";
				$sub_s = explode("\n", $s);
				for($ii = 0; $ii < count($sub_s); $ii++) {
//					echo "$ii-$sub_s[$ii]|<br>";
					if($border && $ii==1)
						$b=$b2;
					if($border && $ii == (count($sub_s)-1) && strpos($border,'B')!==false)
						$b.='B';
					if ($f_print)
						$this->Cell($w,$h,trim($sub_s[$ii]),$b,2,$align,$fill);
					$str_w = $this->GetStringWidth($sub_s[$ii]);
//					echo "MthaiCut sub_s [$ii]=".$sub_s[$ii]." ($w::$str_w) $h,$txt,$border,$align,$fill, ".($f_print?"print":"")."<br>";
				}
				$this->x=$this->lMargin;
				
				return $sub_s;
			} // end function

			// ตัดบรรทัดคำไทย
			function thaiWrapPDF($dataIn, $delim){
				$specword = array("ราชการ", "รัฐบาล", "อธิบดี", "คณะบดี", "ตำแหน่ง", "อำเภอ","จังหวัด","ปกครอง","ปฏิบัติ","สัญญา","เจ้าหน้าที่", "อยู่", "และ", "เกียรติยศ","บรรจุ", "ดำรง", "บำเหน็ด", "โรงเรียน", "มหาบัณฑิต", "บัณฑิต", "บริหาร", "ชำนาญ", "ศาสน", "ศาสตร", "พัฒนา", "ภาพ", "วันที่", "เลขที่", "ประเภท", "นโยบาย", "นักวิเคราะห์", "กำหนด", "วัน", "เดือน", "ปี", "เข้า", "สู่", "อายุ"); // เพิ่มคำเฉพาะได้ที่นี่
				$pvword = array("กระบี่", "กาญจนบุรี", "กาฬสินธุ์", "กำแพงเพชร", "ขอนแก่น", "จันทบุรี", "ฉะเชิงเทรา", "ชลบุรี", "ชัยนาท", "ชัยภูมิ", "ชุมพร", "เชียงราย", "เชียงใหม่", "ตรัง", "ตราด", "ตาก", "นครนายก", "นครปฐม", "นครพนม", "นครราชสีมา", "นครศรีธรรมราช", "นครสวรรค์", "นนทบุรี", "นราธิวาส", "น่าน", "บุรีรัมย์", "ปทุมธานี", "ประจวบคีรีขันธ์", "ปราจีนบุรี", "ปัตตานี", "พระนครศรีอยุธยา", "พะเยา", "พังงา", "พัทลุง", "พิจิตร", "พิษณุโลก", "เพชรบุรี", "เพชรบูรณ์", "แพร่", "ภูเก็ต", "มหาสารคาม", "มุกดาหาร", "แม่ฮ่องสอน", "ยโสธร", "ยะลา", "ร้อยเอ็ด", "ระนอง", "ระยอง", "ราชบุรี", "ลพบุรี", "ลำปาง", "ลำพูน", "เลย", "ศรีสะเกษ", "สกลนคร", "สงขลา", "สตูล", "สมุทรปราการ", "สมุทรสงคราม", "สมุทรสาคร", "สระแก้ว", "สระบุรี", "สิงห์บุรี", "สุโขทัย", "สุพรรณบุรี", "สุราษฎร์ธานี", "สุรินทร์", "หนองคาย", "หนองบัวลำภู", "อ่างทอง", "อำนาจเจริญ", "อุดรธานี", "อุตรดิตถ์", "อุทัยธานี", "อุบลราชธานี");
				$amword = array("มายอ", "ทุ่งยางแดง", "สายบุรี", "ดงเจริญ", "วชิรบารมี", "ชนแดน", "หล่มสัก", "หล่มเก่า", "วิเชียรบุรี", "ศรีเทพ", "หนองไผ่", "บึงสามพัน", "น้ำหนาว", "วังโป่ง", "เขาค้อ", "จอมบึง", "สวนผึ้ง", "ดำเนินสะดวก", "บ้านโป่ง", "บางแพ", "โพธาราม", "ปากท่อ", "วัดเพลง", "บ้านคา", "ไทรโยค", "บ่อพลอย", "ศรีสวัสดิ์", "ท่ามะกา", "ท่าม่วง", "ทองผาภูมิ", "สังขละบุรี", "พนมทวน", "เลาขวัญ", "ด่านมะขามเตี้ย", "หนองปรือ", "ห้วยกระเจา", "ท่ากระดาน", "บ้านทวน", "เดิมบางนางบวช", "ด่านช้าง", "บางปลาม้า", "ศรีประจันต์", "ดอนเจดีย์", "สองพี่น้อง", "สามชุก", "อู่ทอง", "หนองหญ้าไซ", "กำแพงแสน", "นครชัยศรี", "ดอนตูม", "บางเลน", "สามพราน", "พุทธมณฑล", "กระทุ่มแบน", "บ้านแพ้ว", "บางคนที", "อัมพวา", "เขาย้อย", "หนองหญ้าปล้อง", "ชะอำ", "ท่ายาง", "บ้านลาด", "บ้านแหลม", "แก่งกระจาน", "กุยบุรี", "ทับสะแก", "บางสะพาน", "บางสะพานน้อย", "ปราณบุรี", "หัวหิน", "สามร้อยยอด", "พรหมคีรี", "ลานสกา", "ลานสะกา", "ฉวาง", "พิปูน", "เชียรใหญ่", "ชะอวด", "ท่าศาลา", "ทุ่งสง", "นาบอน", "ทุ่งใหญ่", "ปากพนัง", "ร่อนพิบูลย์", "สิชล", "ขนอม", "หัวไทร", "บางขัน", "ถ้ำพรรณรา", "จุฬาภรณ์", "พระพรหม", "นบพิต้า", "ช้างกลาง", "เฉลิมพระเกียรติ", "เสือหึง", "สวนหลวง", "หินตก", "ดวนชะลิก", "กะปาง", "เขาพนม", "เกาะลันตา", "คลองท่อม", "อ่าวลึก", "จุน", "เชียงคำ", "เชียงม่วน", "ดอกคำใต้", "ปง", "แม่ใจ", "ภูซาง", "ภูกามยาว", "เวียงชัย", "ย่านตาขาว", "ปะเหลียน", "สิเกา", "ห้วยยอด", "วังวิเศษ", "นาโยง", "รัษฎา", "หาดสำราญ", "กงหรา", "เขาชัยสน", "ตะโหมด", "ควนขนุน", "ปากพะยูน", "ศรีบรรพต", "กิ่งศลีบรรพต", "ป่าบอน", "บางแก้ว", "ป่าพะยอม", "ศรีนครินทร์", "โคกโพธิ์", "หนองจิก", "ปะนาเระ", "เชียงของ", "เทิง", "พาน", "ป่าแดด", "แม่จัน", "เชียงแสน", "แม่สาย", "แม่สรวย", "เวียงป่าเป้า", "พญาเม็งราย", "เวียงแก่น", "ขุนตาล", "แม่ฟ้าหลวง", "แม่ลาว", "เวียงเชียงรุ้ง", "ดอยหลวง", "ขุนยวม", "ปาย", "แม่สะเรียง", "แม่ลาน้อย", "สบเมย", "ปางมะผ้า", "ม่วยต่อ", "โกรกพระ", "ชุมแสง", "หนองบัว ", "บรรพตพิสัย", "เก้าเลี้ยว", "ตาคลี", "ท่าตะโก", "ไพศาลี", "พยุหะคีรี", "ลาดยาว", "ตากฟ้า", "แม่วงก์", "แม่เปิน", "ชุมตาบง", "ห้วยน้ำหอม", "ชุมต", "แม่เล่ย์", "ทัพทัน", "สว่างอารมณ์", "หนองฉาง", "หนองขาหย่าง", "บ้านไร่", "ลานสัก", "ห้วยคด", "ไทรงาม", "คลองลาน", "ขาณุวรลักษบุรี", "คลองขลุง", "พรานกระต่าย", "ลานกระบือ", "ทรายทองวัฒนา", "ปางศิลาทอง", "บึงสามัคคี", "โกสัมพีนคร", "บ้านตาก", "สามเงา", "แม่ระมาด", "ท่าสองยาง", "แม่สอด", "พบพระ", "อุ้มผาง", "วังเจ้า", "ท่าปุย", "บ้านด่านลานหอย", "คีรีมาศ", "กงไกรลาศ", "ศรีสัชนาลัย", "ศรีสำโรง", "สวรรคโลก", "ศรีนคร", "ทุ่งเสลี่ยม", "นครไทย", "ชาติตระการ", "บางระกำ", "บางกระทุ่ม", "พรหมพิราม", "วัดโบสถ์", "วังทอง", "เนินมะปราง", "วังทรายพูน", "โพธิ์ประทับช้าง", "ตะพานหิน", "บางมูลนาก", "โพทะเล", "สามง่าม", "ทับคล้อ", "สากเหล็ก", "บึงนาราง", "สว่างแดนดิน", "ส่องดาว", "เต่างอย", "โคกศรีสุพรรณ", "เจริญศิลป์", "โพนนาแก้ว", "ภูพาน", "วานรนิวาส", "กุดเรือคำ", "บ้านหัน", "ปลาปาก", "ท่าอุเทน", "บ้านแพง", "ธาตุพนม", "นาแก", "เรณูนคร", "ศรีสงคราม", "นาหว้า", "โพนสวรรค์", "นาทม", "วังยาง", "นิคมคำสร้อย", "ดอนตาล", "ดงหลวง", "คำชะอี", "หว้านใหญ่", "หนองสูง", "จอมทอง", "แม่แจ่ม", "เชียงดาว", "ดอยสะเก็ด", "แม่แตง", "แม่ริม", "สะเมิง", "ฝาง", "แม่อาย", "พร้าว", "สันป่าตอง", "สันกำแพง", "สันทราย", "หางดง", "ฮอด", "ดอยเต่า", "อมก๋อย", "สารภี", "เวียงแหง", "เวียงแห", "ไชยปราการ", "แม่วาง", "แม่ออน", "ดอยหล่อ", "นครเชียงใหม่", "แขวงกา", "แขวงศร", "เม็งรา", "แม่ทา", "บ้านโฮ่ง", "ลี้", "ทุ่งหัวช้าง", "ป่าซาง", "บ้านธิ", "เวียงหนองล่อง", "แม่เมาะ", "เกาะคา", "เสริมงาม", "งาว", "แจ้ห่ม", "วังเหนือ", "เถิน", "แม่พริก", "แม่ทะ", "สบปราบ", "ห้างฉัตร", "ตรอน", "ท่าปลา", "น้ำปาด", "ไม้แก่น", "ไทรทอง", "ยะหริ่ง", "ยะรัง", "กะพ้อ", "แม่ลาน", "เบตง", "บันนังสตา", "ธารโต", "กิ่งธารโต", "ยะหา", "รามัน", "กรงปินัง", "ตากใบ", "บาเจาะ", "ยี่งอ", "ระแงะ", "รือเสาะ", "ศรีสาคร", "แว้ง", "สุคิริน", "สุไหงโก-ลก", "สุไหงปาดี", "จะแนะ", "เจาะไอร้อง", "บางนรา", "พระนคร", "ดุสิต", "หนองจอก", "บางรัก", "บางเขน", "บางกะปิ", "ปทุมวัน", "ป้อมปราบศัตรูพ่าย", "พระโขนง", "มีนบุรี", "ลาดกระบัง", "ยานนาวา", "สัมพันธวงศ์", "พญาไท", "ธนบุรี", "บางกอกใหญ่", "ห้วยขวาง", "คลองสาน", "ตลิ่งชัน", "บางกอกน้อย", "บางขุนเทียน", "ภาษีเจริญ", "หนองแขม", "ราษฎร์บูรณะ", "บางพลัด", "ดินแดง", "บึงกุ่ม", "สาธร", "บางซื่อ", "จตุจักร", "บางคอแหลม", "ประเวศ", "คลองเตย", "ราชเทวี", "ลาดพร้าว", "วัฒนา", "บางแค", "หลักสี่", "สายไหม", "คันนายาว", "สะพานสูง", "วังทองหลาง", "คลองสามวา", "บางนา", "ทวีวัฒนา", "ทุ่งครุ", "บางบอน", "บ้านทะวาย", "บางบ่อ", "บางพลี", "พระประแดง", "พระสมุทรเจดีย์", "บางเสาธง", "บางกรวย", "บางใหญ่", "บางบัวทอง", "ไทรน้อย", "ปากเกร็ด", "นครนนทบุรี", "แขวงท่าท", "คลองหลวง", "ธัญบุรี", "หนองเสือ", "ลาดหลุมแก้ว", "ลำลูกกา", "สามโคก", "คูคต", "ท่าเรือ", "นครหลวง", "บางไทร", "บางบาล", "บางปะอิน", "บางปะหัน", "ปางปะหัน", "ผักไห่", "ภาชี", "ลาดบัวหลวง", "วังน้อย", "เสนา", "บางซ้าย", "อุทัย", "มหาราช", "บ้านแพรก", "ไชโย", "ป่าโมก", "โพธิ์ทอง", "แสวงหา", "วิเศษชัยชาญ", "สามโก้", "พัฒนานิคม", "โคกสำโรง", "ชัยบาดาล", "ท่าวุ้ง", "บ้านหมี่", "ท่าหลวง", "สระโบสถ์", "โคกเจริญ", "ลำสนธิ", "หนองม่วง", "บ้านเช่า", "บางระจัน", "ค่ายบางระจัน", "พรหมบุรี", "ปลายพระยา", "ลำพัด", "ลำทับ", "เหนือคลอง", "เกาะยาว", "กะปง", "ตะกั่วทุ่ง", "ตะกั่วป่า", "คุระบุรี", "ทับปุด", "ท้ายเหมือง", "กะทู้", "ถลาง", "ทุ่งคา", "กาญจนดิษฐ์", "ดอนสัก", "เกาะสมุย", "เกาะพะงัน", "ไชยา", "ท่าชนะ", "คีรีรัฐนิคม", "บ้านตาขุน", "พนม", "ท่าฉาง", "บ้านนาสาร", "บ้านนาเดิม", "เคียนซา", "เวียงสระ", "พระแสง", "พุนพิน", "ชัยบุรี", "วิภาวดี", "เกาะพงัน", "เกาะเต่า", "บ้านดอน", "ละอุ่น", "กะเปอร์", "กระบุรี", "สุขสำราญ", "ท่าแซะ", "ปะทิว", "หลังสวน", "ละแม", "พะโต๊ะ", "ทพะโต๊ะ", "สวี", "ทุ่งตะโก", "สทิงพระ", "จะนะ", "นาทวี", "เทพา", "สะบ้าย้อย", "ระโนด", "กระแสสินธ์", "กระแสสินธุ์", "รัตภูมิ", "สะเดา", "หาดใหญ่", "นาหม่อม", "ควนเนียง", "บางกล่ำ", "สิงหนคร", "คลองหอยโข่ง", "บ้านพรุ", "ควนโดน", "กิ่งควนโดน", "ควนกาหลง", "กิ่งท่าแพ", "ท่าแพ", "ละงู", "ทุ่งหว้า", "มะนัง", "กันตัง", "นาดี", "สระแก้ว", "วังน้ำเย็น", "บ้านสร้าง", "ประจันตคาม", "ศรีมหาโพธิ", "ศรีมโหสถ", "โคกปีบ", "อรัญประเทศ", "ตาพระยา", "วัฒนานคร", "คลองหาด", "ปากพลี", "บ้านนา", "องครักษ์", "เขาฉกรรจ์", "โคกสูง", "วังสมบูรณ์", "ครบุรี", "เสิงสาง", "คง", "บ้านเหลื่อม", "ฟากท่า", "บ้านโคก", "พิชัย", "ลับแล", "ทองแสนขัน", "ร้องกวาง", "ลอง", "สูงเม่น", "เด่นชัย", "สอง", "วังชิ้น", "หนองม่วงไข่", "แม่จริม", "บ้านหลวง", "นาน้อย", "บัว", "ปัว", "ท่าวังผา", "สา", "เวียงสา", "ทุ่งช้าง", "เชียงกลาง", "นาหมื่น", "สันติสุข", "บ่อเกลือ", "สองแคว", "ภูเพียง", "ศรีบุญเรือง", "นากลาง", "สุวรรณคูหา", "โนนสัง", "บ้านผือ", "น้ำโสม", "เพ็ญ", "สร้างคอม", "หนองแสง", "นายูง", "พิบูลย์รักษ์", "กู่แก้ว", "ประจักษ์ศิลปาคม", "นาด้วง", "เชียงคาน", "ปากชม", "ด่านซ้าย", "นาแห้ว", "ภูเรือ", "ท่าลี่", "วังสะพุง", "ภูกระดึง", "ภูหลวง", "ผาขาว", "เอราวัณ", "หนองหิน", "ท่าบ่อ", "บึงกาฬ", "พรเจริญ", "โพนพิสัย", "โซ่พิสัย", "ศรีเชียงใหม่", "สังคม", "เซกา", "ปากคาด", "บึงโขงหลง", "ศรีวิไล", "บุ่งคล้า", "สระใคร", "เฝ้าไร่", "รัตนวาปี", "โพธิ์ตาก", "แกดำ", "โกสุมพิสัย", "กันทรวิชัย", "เชียงยืน", "บรบือ", "นาเชือก", "พยัคฆภูมิพิสัย", "วาปีปทุม", "นาดูน", "ยางสีสุราช", "กุดรัง", "ชื่นชม", "หลุบ", "เกษตรวิสัย", "ปทุมรัตต์", "จตุรพักตรพิมาน", "ธวัชบุรี", "พนมไพร", "โพนทอง", "โพธิ์ชัย", "หนองพอก", "เสลภูมิ", "สุวรรณภูมิ", "โพนทราย", "อาจสามารถ", "เมยวดี", "ศรีสมเด็จ", "จังหาร", "เชียงขวัญ", "หนองฮี", "ทุ่งเขาหลวง", "นามน", "กมลาไสย", "ร่องคำ", "กุฉินารายณ์", "เขาวง", "ยางตลาด", "ห้วยเม็ก", "สหัสขันธ์", "คำม่วง", "ท่าคันโท", "หนองกุงศรี", "สมเด็จ", "ห้วยผึ้ง", "สามชัย", "นาดู", "ดอนจาน", "ฆ้องชัย", "กุสุมาลย์", "กุดบาก", "พรรณานิคม", "พังโคน", "วาริชภูมิ", "นิคมน้ำอูน", "น้ำอูน", "คำตากล้า", "บ้านม่วง", "อากาศอำนวย", "พนา", "ม่วงสามสิบ", "วารินชำราบ", "อำนาจเจริญ", "เสนางคนิคม", "หัวตะพาน", "พิบูลมังสาหาร", "ตาลสุม", "โพธิ์ไทร", "สำโรง", "ดอนมดแดง", "สิรินธร", "ทุ่งศรีอุดม", "ปทุมราชวงศา", "ศรีหลักชัย", "นาเยีย", "นาตาล", "เหล่าเสือโก้ก", "สว่างวีระวงศ์", "น้ำขุ่น", "สุวรรณวารี", "ทรายมูล", "กุดชุม", "คำเขื่อนแก้ว", "ป่าติ้ว", "มหาชนะชัย", "ค้อวัง", "เลิงนกทา", "ไทยเจริญ", "บ้านเขว้า", "คอนสวรรค์", "เกษตรสมบูรณ์", "หนองบัวแดง", "จัตุรัส", "บำเหน็จณรงค์", "หนองบัวระเหว", "เทพสถิต", "ภูเขียว", "บ้านแท่น", "แก้งคร้อ", "คอนสาร", "ภักดีชุมพล", "เนินสง่า", "ซับใหญ่", "กัลยาณิวัฒนา", "บ้านหว่าเฒ่า", "วังชมภู", "ซับใ", "โคกเพชร", "นายางกลัก", "บ้านเต่า", "ท่ามะไฟหวาน", "ดนนคูณ", "ชานุมาน", "ลืออำนาจ", "นาวัง", "บ้านฝาง", "พระยืน", "หนองเรือ", "ชุมแพ", "สีชมพู", "น้ำพอง", "อุบลรัตน์", "กระนวน", "บ้านไผ่", "เปือยน้อย", "พล", "แวงใหญ่", "แวงน้อย", "หนองสองห้อง", "ภูเวียง", "มัญจาคีรี", "ชนบท", "เขาสวนกวาง", "ภูผาม่าน", "ซำสูง", "โคกโพธิ์ไชย", "หนองนาคำ", "บ้านแฮด", "โนนศิลา", "กุดจับ", "หนองวัวซอ", "กุมภวาปี", "โนนสะอาด", "หนองหาน", "ทุ่งฝน", "ไชยวาน", "ศรีธาตุ", "วังสามหมอ", "บ้านดุง", "หนองบัวลำภู", "จักราช", "โชคชัย", "ด่านขุนทด", "โนนไทย", "โนนสูง", "ขามสะแกแสง", "บัวใหญ่", "ประทาย", "ปักธงชัย", "พิมาย", "ห้วยแถลง", "ชุมพวง", "สูงเนิน", "ขามทะเลสอ", "สีคิ้ว", "ปากช่อง", "หนองบุญนาก", "แก้งสนามนาง", "โนนแดง", "วังน้ำเขียว", "เทพารักษ์", "พระทองคำ", "ลำทะเมนชัย", "บัวลาย", "สีดา", "มะค่า-พลสงคราม", "โนนลาว", "กระสัง", "นางรอง", "หนองกี่", "ละหานทราย", "ประโคนชัย", "บ้านกรวด", "พุทไธสง", "ลำปลายมาศ", "สตึก", "ปะคำ", "นาโพธิ์", "หนองหงส์", "พลับพลาชัย", "ห้วยราช", "โนนสุวรรณ", "ชำนิ", "บ้านใหม่ไชยพจน์", "โนนดินแดง", "บ้านด่าน", "แคนดง", "ชุมพลบุรี", "ท่าตูม", "จอมพระ", "ปราสาท", "กาบเชิง", "รัตนบุรี", "สนม", "ศีขรภูมิ", "สังขะ", "ลำดวน", "สำโรงทาบ", "บัวเชด", "พนมดงรัก", "ศรีณรงค์", "เขวาสินรินทร์", "โนนนารายณ์", "ยางชุมน้อย", "กันทรารมย์", "กันทรลักษ์", "ขุขันธ์", "ไพรบึง", "ปรางค์กู่", "ขุนหาญ", "ราษีไศล", "อุทุมพรพิสัย", "บึงบูรพ์", "ห้วยทับทัน", "โนนคูณ", "โนนคูน", "ศรีรัตนะ", "น้ำเกลี้ยง", "วังหิน", "ภูสิงห์", "เบญจลักษ์", "พยุห์", "โพธิ์ศรีสุวรรณ", "ศิลาลาด", "โขงเจียม", "เขื่องใน", "เขมราฐ", "เดชอุดม", "นาจะหลวย", "น้ำยืน", "บุณฑริก", "ตระการพืชผล", "กุดข้าวปุ้น", "ท่าช้าง", "อินทร์บุรี", "มโนรมย์", "วัดสิงห์", "สรรพยา", "สรรคบุรี", "หันคา", "หนองมะโมง", "เนินขาม", "แก่งคอย", "หนองแค", "วิหารแดง", "หนองแซง", "บ้านหมอ", "ดอนพุด", "หนองโดน", "พระพุทธบาท", "เสาไห้", "มวกเหล็ก", "วังม่วง", "บ้านบึง", "หนองใหญ่", "บางละมุง", "พานทอง", "พนัสนิคม", "ศรีราชา", "เกาะสีชัง", "สัตหีบ", "บ่อทอง", "เกาะจันทร์", "บางเสร่", "แหลมฉบัง", "บ้างฉาง", "บ้านฉาง", "แกลง", "วังจันทร์", "บ้านค่าย", "ปลวกแดง", "เขาชะเมา", "นิคมพัฒนา", "มาบข่า", "ขลุง", "ท่าใหม่", "โป่งน้ำร้อน", "มะขาม", "แหลมสิงห์", "สอยดาว", "แก่งหางแมว", "นายายอาม", "เขาคิชฌกูฏ", "กำพุธ", "คลองใหญ่", "เขาสมิง", "บ่อไร่", "แหลมงอบ", "เกาะกูด", "เกาะช้าง", "บางคล้า", "บางน้ำเปรี้ยว", "บางปะกง", "บ้านโพธิ์", "พนมสารคาม", "ราชสาส์น", "สนามชัย", "แปลงยาว", "ท่าตะเกียบ", "คลองเขื่อน", "กบินทร์บุรี", "กาบัง", "เวียงเก่า"); // เพิ่มคำเฉพาะได้ที่นี่

				$min_pos_i = strlen($dataIn);
				$pos_i = 0;
				$i_ord = 0;
				$ret_len = strlen($dataIn);
				$ch = "";
				
				$mypatt = implode("|", $specword);
				if (preg_match("/($mypatt)/",$dataIn,$match)) {
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
//						if (preg_match("/(เ([ก-ฮ])(ร|ม|พ)?าะ(ห์)?)/",$dataIn,$match)) 
//							echo "1.เคราะห์-->$dataIn ($ret_len) ($pos_i < $min_pos_i)<br>";						
					}
				} 
				$mypatt = implode("|", $pvword);
				if (preg_match("/($mypatt)/",$dataIn,$match)) {
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				} 
				$mypatt = implode("|", $amword);
				if (preg_match("/($mypatt)/",$dataIn,$match)) {
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				} 
				if (preg_match("/(เ([ก-ฮ])(ร|ม|พ)?าะ(ห์)?)/",$dataIn,$match)) { // // เคราะห์ เหมาะ เจาะ เกาะ เอาะ เลาะห์ เฉพาะ
					$pos_i = strpos($dataIn, $match[0]);
//					echo "2.เคราะห์-->$dataIn ($ret_len) ($pos_i < $min_pos_i)<br>";
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				} 
				if (preg_match("/(มา(?:ก|ย|ตรการ|ตรา|ตุภูมิ|ชิก))/",$dataIn,$match)) { // 
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				}
				if (preg_match("/(เ([ก-ฮ])ี([่|้|๊|๋])?ยง)/",$dataIn,$match)) { // เกียง เอี้ยง เมี่ยง เสี่ยง 
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				}
				if (preg_match("/(เ([ก-ฮ])ื([่|้|๊|๋])?อ([ง|น]))/",$dataIn,$match)) { // เมือง เนื่อง เอื้อง เรือง เลื่อน เดือน
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				}
				if (preg_match("/((เ|แ)([ก-ฮ])([งลรว])?([่|้|๊|๋|็|ิ|ี|ึ|ื])?([ง|ว|อ|น]))/",$dataIn,$match)) { // เกลือ แกง แฮง แรง แกล้ง แผลง แดง แวง แตง แหว่ง แกร่ง แฝง แสลง แกร๊ง แกล้ว
																																													// แผน แสน
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				} 
				if (preg_match("/(เ([ก-ฮ])([่|้|๊|๋])า)/",$dataIn,$match)) { // เข้า เจ้า เจ่า เช้า เก่า เม่า เล่า
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				}
				if (preg_match("/([ก-ฮ][่|้|๊|๋])อน/",$dataIn,$match)) {  // ช้อน ค้อน ร้อน ซ้อน ซ่อน ค่อน ป้อน 
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				} 
				if (preg_match("/(ม(?:ี|ัน|ือ))/",$dataIn,$match)) { // มือ มัน มือ
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				}
				if (preg_match("/([ก-ฮ]ื([่|้|๊|๋])?อ)/",$dataIn,$match)) { // ชื่อ ซื้อ คือ สื่อ ดื้อ มื้อ หือ อื้อ
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				}
				if (preg_match("/([ก-ฮ]ั([่|้|๊|๋])?([ง|ก|บ|น|ด|ย|ร|ล]))/",$dataIn,$match)) { // ตั้ง ตั่ง กั๊ง กั๊ก สัก มัก มั้ง มั๊ย ขัย ปั๊บ รับ นั้น มัน จัด ขัด นัด
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				}
				if (preg_match("/(โ[ก-ฮ]([่|้|๊|๋])?([ง|ก|บ|น|ด]))/",$dataIn,$match)) { // โกง โด่ง โดด โฉด โชค โมก โก่ง โข่ง โค้ง โจ๊ง โด๊บ
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				}
				if (preg_match("/([ก-ฮ](ล|ร)?(า)([ง|ก|บ|น|ด|ร|ล]))/",$dataIn,$match)) { // การ ขาน กาล มาร ฉาน ดาน สาน กาบ
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				}

//				if (preg_match("/(เ([ก-ฮ])(ร|ม|พ)?าะ(ห์)?)/",$dataIn,$match)) 
//					echo "3.เคราะห์-->$dataIn ($ret_len) ($pos_i < $min_pos_i)<br>";						
				if ($min_pos_i==$ret_len && $ret_len==strlen($dataIn)) {
					$min_pos_i = 0;
				}
				$ch = substr($dataIn,$min_pos_i+$ret_len,1);
				if (strpos("ะาิีึืุูำ่้๊๋ั็",$ch) !== false) { // ถ้าถัดไป เป็นตัวที่ต้องมีอักษรนำ ให้บวกไปด้วย
					$ret_len++;
//				} else {
//					$ch = substr($dataIn,$min_pos_i+$ret_len+1,1);
//					if (strpos("เแโใไ",$ch) !== false) { // ถ้าตัวถัดไปอีก 1 ตัว เป็นตัวนำ เอาตัวก่อนหน้าไปด้วย
//						$ret_len++;
//					}
				}
//				if (preg_match("/(เ([ก-ฮ])(ร|ม|พ)?าะ(ห์)?)/",$dataIn,$match)) 
//					echo "4.เคราะห์-->$dataIn ($ret_len) ($pos_i < $min_pos_i) ($ch)<br>";						
				$spec_ch = array("|", " ", ".", ",", ")", "/", "\\","(","@","$","%","&","*","#","!","<",">","{","[","]");
				$min_spec_ch = 999999;
				$seltext = substr($dataIn,0,$min_pos_i+$ret_len);
//				echo "seltext=[$seltext]<br>";
				for($jj = 0; $jj < count($spec_ch); $jj++) {
					$spec_ch_i = strpos($seltext, $spec_ch[$jj]);
//					echo "spec_ch[$spec_ch_i] < min_spec_ch[$min_spec_ch]<br>";
					if ($spec_ch_i !== false && $spec_ch_i < $min_spec_ch) {
						$min_spec_ch = $spec_ch_i;
					}
				}
				if ($min_spec_ch == 999999)  $min_spec_ch = -1;
				if ($min_spec_ch >= 0) {
//					echo "min_spec_ch[$min_spec_ch] < min_pos_i[$min_pos_i] + ret_len[$ret_len]<br>";
					if ($min_spec_ch < $min_pos_i + $ret_len) {
						$min_pos_i=0;
						$ret_len = $min_spec_ch+1;
					}
				}
//				echo "cut text = [".substr($dataIn,$min_pos_i,$ret_len)."]<br>";
//				echo "cut text = [".substr($dataIn,$min_pos_i,$ret_len)."]($min_pos_i,$ret_len)<br>";
				return $min_pos_i+$ret_len;
			} // end function

			function thaiCutLinePDF($dataIn, $w, $delim){
				$cw=&$this->CurrentFont['cw'];
//				$delim = "@";
				$vowels = array("<br>", "<BR>", "<Br>", "<br />", "<BR />", "<Br />", "<br/>", "<BR/>", "<Br/>", "\n");
//				echo "---->1>$dataIn|<br>";
				$tempdataIn = str_replace($vowels, "|", $dataIn); // แล้วค่อยเอา $delim มาแทนทีหลัง เพื่อตัดปัญหา \n ไม่นับจำนวนใน string
//				if ($dataIn=="ใช้งาน/ยกเลิก")
//					echo "---->1>tempdataIn=$tempdataIn|<br>";
//				$tempdataIn = trim($tempdataIn);
				if (substr($tempdataIn,strlen($tempdataIn)-1)=="|") 
					$tempdataIn = substr($tempdataIn,0,strlen($tempdataIn)-1);
//				echo "---->2>$tempdataIn|<br>";
				$midch_cnt=0;
				$i = 0;
				$ctext = "";
				$out = "";
				while (strlen($tempdataIn) > 0) {
					$ch = substr($tempdataIn,$i,1);
					if (strpos( "ิีึืุูํ่้๊๋็ํ์ั", $ch) === false) { // ถ้าเป็นอักษรแถวกลาง (ไม่เป็นอักษรยก   ิ  ี  ื  ึ  ุ  ู  ํ  ่  ้  ๊  ๋  ั  ็)
						$mylen = $this->thaiWrapPDF($tempdataIn, $delim);
						$text1 = substr($tempdataIn,0,$mylen);
//						if ($dataIn=="ใช้งาน/ยกเลิก")
//							echo "after cut ($ch)=$tempdataIn--[$text1]<br>";
						$cnt_up=0;
						for($ii=0; $ii < strlen($text1); $ii++) {
							$ch1=substr($text1,$ii,1);
							if (strpos( "ิีึืุูํ่้๊๋ั็์ั", $ch1) > -1) { // เป็นอักษรยก   ิ  ี  ื  ึ  ุ  ู  ํ  ่  ้  ๊  ๋  ั  ็
								$cnt_up++;
							}
						}
						$mylen1 = $mylen - $cnt_up;
						$str_w = $this->GetStringWidth("$ctext"."$text1");
//						if ($dataIn=="ใช้งาน/ยกเลิก")
//						echo "$str_w > ".($w-1)." [$text1]<br>";
						if ($str_w > $w - 1) {
//							if ($dataIn=="ใช้งาน/ยกเลิก")
//								echo "**str_w >  w**1.$ctext($i-->$ch-->$mylen)<br>";
							if (strlen($ctext) > 0)
								$out = "$out".$ctext."$delim";
							$ctext=$text1;
							$tempdataIn = substr($tempdataIn,$mylen);
							$midch_cnt=$mylen1;
						} else { // <= $w - 1
							$delim_pos = strpos($text1,"|");
							if ($delim_pos !== false) {
								$ctext = "$ctext"."$text1";
//								if ($dataIn=="ใช้งาน/ยกเลิก")
//										echo "**str_w < w**2.$ctext<br>";
								$out = "$out".$ctext;
								$ctext="";
								$tempdataIn = substr($tempdataIn,$mylen);
								$midch_cnt=0;
							} else {
								$ctext = "$ctext"."$text1";
//								if ($dataIn=="ใช้งาน/ยกเลิก")
//										echo "****2.1.$ctext<br>";
								$tempdataIn = substr($tempdataIn,$mylen);
								$midch_cnt = $midch_cnt + $mylen1;
							}
						}
						$i=-1;
					} // end if ch
					$i++;
				} // end loop while
//				if ($dataIn=="ใช้งาน/ยกเลิก")
//					echo "****3.$ctext($out)<br>";
//				echo "****3.$ctext ($out)<br>";
//				$out = "$out".trim((substr($ctext,strlen($ctext)-1)=="|"?substr($ctext,0,strlen($ctext)-1):$ctext));
				$out = "$out".(substr($ctext,strlen($ctext)-1)=="|"?substr($ctext,0,strlen($ctext)-1):$ctext);
//				if ($dataIn=="ใช้งาน/ยกเลิก")
//					echo "-----4.out=$out<br>";
				$out = str_replace("|",$delim,$out);
				return $out; 
			} // end function

			function GetFont()
			{
				//Select a font; size given in points
				global $fpdf_charwidths;

				$arr_fonts = (array) null;
				foreach($this->fonts as $key => $value)
						$arr_fonts[] = $key;
				foreach($this->CoreFonts as $key => $value) {
						if(strpos($key,'helvetica')!==false)	 $key=str_replace('helvetica','arial',$key);
						$arr_fonts[] = $key;
				}
				foreach($fpdf_charwidths as $key => $value)
						$arr_fonts[] = $key;
			
				sort($arr_fonts);
				$arr_fonts1 = (array) null;
				$afont = "";
				for($i=0; $i < count($arr_fonts); $i++) {
					if (strpos($arr_fonts[$i],"angsa")!==false || strpos($arr_fonts[$i],"cordia")!==false) {
						$arr_fonts1[] = $arr_fonts[$i];
					} else {
						if (!$afont)
							$afont = $arr_fonts[$i];
						else
 							if ($afont != substr($arr_fonts[$i],0,strlen($afont))) {
 								$arr_fonts1[] = $afont;
								$afont = $arr_fonts[$i];
							}
					}
					if ($i == count($arr_fonts)-1)
							$arr_fonts1[] = $afont;
				}	// end for loop
			
				$font_list = implode(",",$arr_fonts1);
			
				return $font_list;
			}	// end function GetFont()
			
			function open_tab($head_text, $head_width, $head_line_height=7, $head_border="TRHBL", $head_align="C", $font="cordia", $font_size="14", $font_style="b", $font_color=0, $fill_color=0, $COLUMN_FORMAT)
			{
//				echo "head_text=$head_text<br>";
				if (!$font) $font="cordia";
				$this->f_table = 1;
				$this->arr_tab_head = (array) null;		// array tab head text format=text1 line 1|text1 line 2|text1 line 3,text2 line 1|text2 line 2|text2 line 3,.....
				$this->arr_head_width = explode(",", $head_width);	// array tab head width
				$this->head_fill_color = $fill_color;							//  table head fill color format RRGGBB
				$this->head_font_name = $font;								//  table head font name ex  "AngsanaUPC" "cordia"
				$this->head_font_size = $font_size;						//  table head font size
				$this->head_font_style = $font_style;						//  table head font style "B" bold "I" italic "U" underline
				$this->head_font_color = $font_color;					//  table head font color  format RRGGBB
				$this->head_border = $head_border;						//  table head border
				$this->arr_head_align = explode(",", $head_align);		//  table head align
				$this->head_line_height = $head_line_height;		//  table head line height

				$this->arr_column_map = (array) null;
				$this->arr_column_sel = (array) null;
				$this->arr_column_width = (array) null;
				$this->arr_column_align = (array) null;
				if (!$COLUMN_FORMAT) {	// ถ้าส่งค่าการปรับแต่ง รายงานเข้ามา
					for($i=0; $i < count(explode(",",$head_text)); $i++) {
						$this->arr_column_map[] = $i;		// link index ของ head 
						$this->arr_column_sel[] = 1;			// 1=แสดง	0=ไม่แสดง   
					}
					$this->arr_column_width = explode(",",$head_width);	// ความกว้าง
					$this->arr_column_align = explode(",",$head_align);		// align กำหนดไว้ก่อน แต่จริง ๆ ไม่ได้ใช้ค่า align ของ head แต่ จะนำไปใช้ใน add_data_tab เป็น align ของ data
				} else {
					$arrbuff = explode("|",$COLUMN_FORMAT);
					$this->arr_column_map = explode(",",$arrbuff[0]);		// index ของ head เริ่มต้น
					$this->arr_column_sel = explode(",",$arrbuff[1]);	// 1=แสดง	0=ไม่แสดง
					$this->arr_column_width = explode(",",$arrbuff[2]);	// ความกว้าง
					$this->arr_column_align = explode(",",$arrbuff[3]);		// align
				}
//				echo "map=".implode(",",$this->arr_column_map)."  sel=".implode(",",$this->arr_column_sel)."  width=".implode(",",$this->arr_column_width)."  align=".implode(",",$this->arr_column_align)." [$COLUMN_FORMAT]<br>";

//				echo "head font=".$this->head_font_name.", font=$font<br>";
				if (strpos("b", strtolower($this->head_font_style)) !== false) $this->head_font_name = $this->head_font_name."b";

				$this->merge_rows_h = (array) null;
				for($i = 0; $i < $cnt_column_width; $i++) {
					$this->merge_rows_h[]=0;
				}

				if (strlen(trim($head_text))==0)
					$cnt_column_head = 0;
				else {
					$buff_arr_tab_head = explode(",", $head_text);
					$cnt_column_head = count($buff_arr_tab_head);
				}
				$cnt_column_width = count($this->arr_head_width);
//				echo "cnt_column_head=$cnt_column_head<br>";
				if ($cnt_column_head==0) {
					$this->f_no_head = true; 
					$this->arr_tab_head = (array) null;
					for($i = 0; $i < $cnt_column_width; $i++) {
						$this->arr_tab_head[]="";
					}
					$cnt_column_head = $cnt_column_width;
				} else {	// else if ($cnt_column_head==0)
					$founded = false;
					$max_line = 0;
					$line_idx = 0;
					$new_sub_head = (array) null;
					$looped = true;
					while ($looped) { // loop ตามบรรทัดก่อน
						$numline = 0; // ใช้ในการหาค่าบรรทัดทั้งหมด
						$grptext = "";
						$realtext = "";
						$sumw = 0; $start_i = -1;
						for($i = 0; $i < $cnt_column_head; $i++) {
							if (strlen(trim($buff_arr_tab_head[$i])) > 0) $founded = true;
							$sub_tab_head = explode("|", trim($buff_arr_tab_head[$i]));
//							echo "sub_tab_head[$line_idx]=".$sub_tab_head[$line_idx]."<br>";
							$c1 = strpos($sub_tab_head[$line_idx], "<**");
							if ($c1 !== false) {
								$c2 = strpos($sub_tab_head[$line_idx], "**>");
//								echo "grptext=".$grptext.", sub_tab_head[$line_idx]=".$sub_tab_head[$line_idx]."<br>";
								if ($grptext != substr($sub_tab_head[$line_idx], $c1, $c2-$c1+3)) {
									if ($sumw > 0) {
//										echo "1..thai cut w:$sumw, text=$realtext<br>";
//										$arr_b = $this->line_MultiCellThaiCut($sumw, $this->head_line_height, $realtext, $this->head_border, $this->arr_head_align[$i], $this->head_fill_color);
										$arr_b = $this->MultiCellThaiCut($sumw, $this->head_line_height, $realtext, $this->head_border, $this->arr_head_align[$i], $this->head_fill_color,false);
										for($k = 0; $k < count($arr_b); $k++) {
//											echo "arr_b[$k]=".$arr_b[$k]."<br>";
											$arr_b[$k] = $grptext.$arr_b[$k];
//											$numline++;
										}
										$newtext = implode("|", $arr_b);
										for($k = $start_i; $k < $i; $k++) {
											$sub_tab_head1 = explode("|", $buff_arr_tab_head[$k]);
											$sub_tab_head1[$line_idx] = $newtext;
											$buff_arr_tab_head[$k] = implode("|", $sub_tab_head1);
											$numline = count(explode("|", $buff_arr_tab_head[$k]));
											if ($numline > $max_line) $max_line = $numline;
										}
//										echo "1..newtext=$newtext, numline=$numline, max_line=$max_line<br>";
									}
									$start_i = $i; 
 									$grptext = substr($sub_tab_head[$line_idx], $c1, $c2-$c1+3);
									$realtext = substr($sub_tab_head[$line_idx], $c2+3);
//									echo "after newtext --> grptext=".$grptext.", realtext=".$realtext." ($c2 - $c1)<br>";
									$sumw = $this->arr_head_width[$i];
								} else {
									$sum += $this->arr_head_width[$i];
								}
							} else {	// else if ($c1 !== false) --> ถ้าไม่มีกลุ่ม head
								if ($sumw > 0) {
//									echo "2..thai cut w:$sumw, text=$realtext<br>";
//									$arr_b = $this->line_MultiCellThaiCut($sumw, $this->head_line_height, $realtext, $this->head_border, $this->arr_head_align[$i], $this->head_fill_color);
									$arr_b = $this->line_MultiCellThaiCut($sumw, $this->head_line_height, $realtext, $this->head_border, $this->arr_head_align[$i], $this->head_fill_color, false);
									for($k = 0; $k < count($arr_b); $k++) {
//										echo "arr_b[$k]=".$arr_b[$k]."<br>";
										$arr_b[$k] = $grptext.$arr_b[$k];
//										$numline++;
									}
									$newtext = implode("|", $arr_b);
									for($k = $start_i; $k < $i; $k++) {
										$sub_tab_head1 = explode("|", $buff_arr_tab_head[$k]);
										$sub_tab_head1[$line_idx] = $newtext;
										$buff_arr_tab_head[$k] = implode("|", $sub_tab_head1);
										$numline = count(explode("|", $buff_arr_tab_head[$k]));
										if ($numline > $max_line) $max_line = $numline;
									}
//									echo "2..newtext=$newtext, numline=$numline, max_line=$max_line<br>";
									$start_i = -1; 
 									$grptext = "";
									$realtext = "";
//									echo "realtext=$realtext ($c2 - $c1)<br>";
									$newtext="";
									$sumw = 0;
								}
								if (trim($sub_tab_head[$line_idx])) {
//									echo "3..thai cut w:".$this->arr_head_width[$i].", text=".trim($sub_tab_head[$line_idx])."<br>";
//									$arr_b = $this->line_MultiCellThaiCut($this->arr_head_width[$i], $this->head_line_height, trim($sub_tab_head[$line_idx]), $this->head_border, $this->arr_head_align[$i], $this->head_fill_color);
									$arr_b = $this->MultiCellThaiCut($this->arr_head_width[$i], $this->head_line_height, trim($sub_tab_head[$line_idx]), $this->head_border, $this->arr_head_align[$i], $this->head_fill_color, false);
									for($k = 0; $k < count($arr_b); $k++) {
//										echo "arr_b[$k]=".$arr_b[$k]."<br>";
										$arr_b[$k] = $arr_b[$k];
//										$numline++;
									}
									$newtext = implode("|", $arr_b);
									$sub_tab_head[$line_idx] = $newtext;
									$buff_arr_tab_head[$i] = implode("|", $sub_tab_head);
									$numline = count(explode("|", $buff_arr_tab_head[$i]));
									if ($numline > $max_line) $max_line = $numline;
//									echo "3..line_idx=$line_idx, newtext=$newtext, buff_arr_tab_head[$i]=".$buff_arr_tab_head[$i].", numline=$numline, max_line=$max_line<br>";
								}
							}
						} // end for $i loop
						if ($sumw > 0) {
//							echo "4..thai cut w:$sumw, text=$realtext<br>";
//							$arr_b = $this->line_MultiCellThaiCut($sumw, $this->head_line_height, $realtext, $this->head_border, $this->arr_head_align[$i], $this->head_fill_color);
							$arr_b = $this->MultiCellThaiCut($sumw, $this->head_line_height, $realtext, $this->head_border, $this->arr_head_align[$i], $this->head_fill_color, false);
							for($k = 0; $k < count($arr_b); $k++) {
//								echo "arr_b[$k]=".$arr_b[$k]."<br>";
								$arr_b[$k] = $grptext.$arr_b[$k];
//								$numline++;
							}
							$newtext = implode("|", $arr_b);
							for($k = $start_i; $k < $i; $k++) {
								$sub_tab_head1 = explode("|", $buff_arr_tab_head[$k]);
								$sub_tab_head1[$line_idx] = $newtext;
								$buff_arr_tab_head[$k] = implode("|", $sub_tab_head1);
								$numline = count(explode("|", $buff_arr_tab_head[$k]));
								if ($numline > $max_line) $max_line = $numline;
							}
//							echo "4..newtext=$newtext, numline=$numline, max_line=$max_line<br>";
							$start_i = -1; 
							$grptext = "";
							$realtext = "";
//							echo "realtext=$realtext ($c2 - $c1)<br>";
							$newtext="";
							$sumw = 0;
						}
						$line_idx++;
						if (($line_idx >= $max_line && $i >= $cnt_column_head)) $looped = false;
					} // end while loop
//					$head_text1 =  implode(",", $buff_arr_tab_head);
					if ($founded)	{
						for($i = 0; $i < $cnt_column_head; $i++) {
//							echo "new_sub_head[$i]=".$new_sub_head[$i]." head_text[$i]=".$buff_arr_tab_head[$i]." max_line=$max_line<br>";
							$arr_sub = explode("|", $buff_arr_tab_head[$i]);
//							echo "cnt_sub[$i]=".count($arr_sub).", max_line=$max_line<br>";
							if (count($arr_sub) < $max_line) {
								for($j = count($arr_sub); $j < $max_line; $j++) {	
									array_push($arr_sub, "");
//									$new_sub_head[$i] .= "|";
								}
								$this->arr_tab_head[] = implode("|", $arr_sub);
//								echo "1..arr_tab_head[$i]=".implode("|", $arr_sub)."<br>";
							} else {
								$this->arr_tab_head[] = $buff_arr_tab_head[$i];
//								echo "2..arr_tab_head[$i]=".$buff_arr_tab_head[$i]."<br>";
							}
						}
						$n_line = $max_line;
						for($ln = 0; $ln < $n_line; $ln++) {
							$have_val=false;
							for($i = 0; $i < $cnt_column_head; $i++) {
								$arr_sub = explode("|", $this->arr_tab_head[$i]);
								if (trim($arr_sub[$ln])) $have_val = true;
							}
//							echo "line=$ln-->have_val=$have_val<br>";
							if (!$have_val) {
								for($i = 0; $i < $cnt_column_head; $i++) {
									$arr_sub = explode("|", $this->arr_tab_head[$i]);
//									echo "bf remove space - count=".count($arr_sub)."<br>";
									array_splice($arr_sub , $ln, 1);
//									echo "af remove space - count=".count($arr_sub)."<br>";
									$this->arr_tab_head[$i] = implode("|", $arr_sub);
								}
								$n_line = count(explode("|", $this->arr_tab_head[0]));
							}
						} // end for $ln
					} // end if $founded
					// ถ้า มี head ที่มีค่า แม้แต่ column เดียว ก็จะถือว่า จะพิมพ์ head
					if ($founded)	
						$this->f_no_head = false;
					else
						$this->f_no_head = true;
				}	// end if ($cnt_column_head==0)
				if ($cnt_column_head != $cnt_column_width) {	// ถ้า ส่งจำนวน column เข้ามาไม่ถูกต้อง return error
					return false; 
				} else {
//					echo "cnt_column_head ($cnt_column_head) != cnt_column_width ($cnt_column_width) (f_no_head=".$this->f_no_head.")<br>";
					return $this->print_tab_header();
				}
			}
			
			function print_tab_header()
			{
				$res = true;
				// ถ้า open table แล้ว และ ไม่ no head ก็ print head
				if ($this->f_table==1 && (!$this->f_no_head)) {
					$this->SetFont($this->head_font_name,'',$this->head_font_size);
					if (strlen($this->head_font_color)==6) {
						$r=substr($this->head_font_color,0,2);
						$g=substr($this->head_font_color,2,2);
						$b=substr($this->head_font_color,4,2);
					} else { $r="00"; $g="00"; $b="00"; }
//					echo "font:$r $g $b<br>";
					$this->SetTextColor(hexdec("$r"),hexdec("$g"),hexdec("$b"));

					if (strlen($this->head_fill_color)==6) {
						$r=substr($this->head_fill_color,0,2);
						$g=substr($this->head_fill_color,2,2);
						$b=substr($this->head_fill_color,4,2);
						$this->SetFillColor(hexdec("$r"),hexdec("$g"),hexdec("$b"));
//						echo "fill:$r $g $b<br>";
					}
				
					$border = "";
					$head_align = "C";
				
					$start_x = $this->x;			$start_y = $this->y;				$max_y = $this->y;
					$first_y = $this->y;

					$cnt_line_head = count(explode("|", $this->arr_tab_head[0]));
					$cnt_column_head = count($this->arr_tab_head);
//					echo "cnt_line_head=$cnt_line_head, cnt_column_head=$cnt_column_head  (f_no_head=".$this->f_no_head.")<br>";
					if ($cnt_column_head > 0) {	// ถ้า มีค่า column เข้ามาถูกต้อง ทำงานต่อ
						$cnt_column_show = 0;
						$head_t_w = 0;
						for($jjj = 0; $jjj < $cnt_column_head; $jjj++) {
							$arr_setborder[$jjj] = "";
							if ($this->arr_column_sel[$jjj]==1) { $cnt_column_show++; $head_t_w += $this->arr_column_width[$jjj]; }
						}
						
						$columngrp="";
						for($cntline = 0; $cntline < $cnt_line_head; $cntline++) {
							$sum_merge = 0;
							$text_merge = "";
							$head_align = "";
							$sum_merge = 0;
							for($iii = 0; $iii < $cnt_column_head; $iii++) {
								if ($this->arr_column_sel[$iii]==1) {	// 1 = แสดง column นี้
									$nline = explode("|", $this->arr_tab_head[$this->arr_column_map[$iii]]);	// $this->arr_tab_head[$iii]);
									$sum_w = 0;
									for($jjj = 0; $jjj <= $iii; $jjj++)  if ($this->arr_column_sel[$jjj]==1) $sum_w+=(int)$this->arr_column_width[$jjj];		// (int)$this->arr_head_width[$jjj];
	//								echo "line:$cntline column:$iii text:".$nline[$cntline]."<br>";
									$chk_merge = strpos($nline[$cntline],"<**");
									if ($chk_merge!==false) {	// ถ้าเป็น column merge
										$c = strpos($nline[$cntline],"**>");
										if ($c!==false) {
											$buff_colgrp = substr($nline[$cntline], $chk_merge+3, $c-($chk_merge+3));
											if (!$text_merge)  $text_merge = substr($nline[$cntline], $c+3);
//											echo "columngrp($columngrp)==buff_colgrp($buff_colgrp) || text_merge ($text_merge)<br>";
											if ($columngrp==$buff_colgrp) {
//												echo "before add new sum = $sum_merge<br>";
												$sum_merge+=(int)$this->arr_column_width[$iii];	// (int)$this->arr_head_width[$iii];
//												echo "check last record grp($columngrp) $iii == $cnt_column_head-1<br>";
												if ($iii == $cnt_column_head-1) { // ตัวสุดท้ายที่แสดงเป็น merge group column
//													echo "3. sum_merge=$sum_merge, text_merge=$text_merge<br>";
													$this->MultiCellThaiCut($sum_merge, $this->head_line_height, $text_merge, $border, $head_align, 1);	//
													if($this->y > $max_y) $max_y = $this->y;
													$this->x = $start_x + $sum_w - (int)$this->arr_column_width[$iii];	// (int)$this->arr_head_width[$iii];
													$this->y = $start_y;
													$sum_merge = 0;
													$arr_setborder[$iii] = "R";
												}
											} else {
												$columngrp=$buff_colgrp;
												if ($sum_merge > 0) {
//													echo "1. sum_merge=$sum_merge, text_merge=$text_merge<br>";
													$this->MultiCellThaiCut($sum_merge, $this->head_line_height, $text_merge, $border, $head_align, 1);
													if($this->y > $max_y) $max_y = $this->y;
													$this->x = $start_x + $sum_w - (int)$this->arr_column_width[$iii];	// (int)$this->arr_head_width[$iii];
													$this->y = $start_y;
													$sum_merge = 0;
//													$arr_setborder[$iii-1] = "R";
													$arr_setborder[$last_iii] = "R";
												}
												$head_align = ($this->arr_head_align[$this->arr_column_map[$iii]] ? $this->arr_head_align[$this->arr_column_map[$iii]] : "C");
												$sum_merge = (int)$this->arr_column_width[$iii];	//	(int)$this->arr_head_width[$iii];
												$text_merge = substr($nline[$cntline], $c+3);
											}
										} else {
											$ret = false;
										} // end if ($c!==false)
									} else {
//										echo "sum_merge=$sum_merge<br>";
										if ($sum_merge > 0) {
//											echo "2. sum_merge=$sum_merge, text_merge=$text_merge<br>";
											$this->MultiCellThaiCut($sum_merge, $this->head_line_height, $text_merge, $border, $head_align, 1);
											if($this->y > $max_y) $max_y = $this->y;
											$this->x = $start_x + $sum_w - (int)$this->arr_column_width[$iii];	//	(int)$this->arr_head_width[$iii];
											$this->y = $start_y;
											$sum_merge = 0;
//											$arr_setborder[$iii-1] = "R";
											$arr_setborder[$last_iii] = "R";
										}
										$arr_setborder[$iii] = "R";
										$chk_up_merge = false;
										if ($cntline > 0)	 $chk_up_merge = strpos($nline[$cntline-1],"<**");
										$tmp_border =  ($chk_up_merge!==false ? "T" : $border);
//										echo "$iii - w:".$this->arr_head_width[$iii]." text (line:$cntline):".$nline[$cntline].", h=".$this->head_line_height.", border=$tmp_border, align=".$this->arr_head_align[$iii].",  sum_w:".$sum_w."<br>";
//										$this->MultiCellThaiCut($this->arr_head_width[$iii], (int)$this->head_line_height, $nline[$cntline], $tmp_border, $this->arr_head_align[$iii],1);
										$this->MultiCellThaiCut($this->arr_column_width[$iii], (int)$this->head_line_height, $nline[$cntline], $tmp_border, $this->arr_head_align[$this->arr_column_map[$iii]],1);
										if($this->y > $max_y) $max_y = $this->y;
										$this->x = $start_x + $sum_w;
										$this->y = $start_y;
									} // end if ($chk_merge!==false)
									$last_iii = $iii;
								}	// end if ($this->arr_column_sel[$iii]==1)
							} //// end for $iii loop
							//================= Draw Horicental Border Line ====================
							$have_l = strpos(strtoupper($this->head_border), "L");
							$have_r = strpos(strtoupper($this->head_border), "R");
							$have_h = strpos(strtoupper($this->head_border), "H");
							if ($have_l !== false) {
								$line_start_y = $start_y;			$line_end_y = $max_y;
								$line_start_x = $start_x;			$line_end_x = $start_x;
								$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางเส้นแรก
							}
									
							for($i=0; $i<$cnt_column_head; $i++) {
								if ($this->arr_column_sel[$i]==1) { // ถ้าแสดง column $i
									$line_start_y = $start_y;		$line_start_x += (int)$this->arr_column_width[$i]; 	// (int)$this->arr_head_width[$i];
									$line_end_y = $max_y;		$line_end_x += (int)$this->arr_column_width[$i];	// (int)$this->arr_head_width[$i];
									if ($i == $cnt_column_show-1 && $have_r !== false) {
										if ($arr_setborder[$i]=="R") { // เฉพาะ อันที่ไม่ใช้ cell merge
											$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางที่เหลือ
										}
									} elseif ($have_h !== false) {
										if ($arr_setborder[$i]=="R") { // เฉพาะ อันที่ไม่ใช้ cell merge
											$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางที่เหลือ
										}
									}
								} // end if ($this->arr_column_sel[$iii]==1)
							} // end for
							//====================================================
							$this->x = $start_x;			$this->y = $max_y;
							$start_y = $max_y;
						}	// end for $cntline loop
						//================= Draw verticle Border Line ====================
						$have_t = strpos(strtoupper($this->head_border), "T");
						$have_b = strpos(strtoupper($this->head_border), "B");						
						$this->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ
						if ($have_t !== false) {						
							$line_start_y = $first_y;		$line_start_x = $start_x;
							$line_end_y = $first_y;		$line_end_x = $start_x+$head_t_w;
							$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นนอนบน
						}
						if ($have_b !== false) {
							$line_start_y = $max_y;		$line_start_x = $start_x;
							$line_end_y = $max_y;		$line_end_x = $start_x+$head_t_w;
							$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นนอนล่าง
						}
					} else {
						$ret = false;
					} // end if ($cnt_column_head > 0)
						
					$this->SetFont($this->tabdata_font_name,'',$this->tabdata_font_size);
					if (strlen($this->tabdata_font_color)==6) {
						$r=substr($this->tabdata_font_color,0,2);
						$g=substr($this->tabdata_font_color,2,2);
						$b=substr($this->tabdata_font_color,4,2);
					} else { $r="00"; $g="00"; $b="00"; }
//					echo "font:$r $g $b<br>";
					$this->SetTextColor(hexdec("$r"),hexdec("$g"),hexdec("$b"));

					if (strlen($this->tabdata_fill_color)==6) {
						$r=substr($this->tabdata_fill_color,0,2);
						$g=substr($this->tabdata_fill_color,2,2);
						$b=substr($this->tabdata_fill_color,4,2);
						$this->SetFillColor(hexdec("$r"),hexdec("$g"),hexdec("$b"));
//						echo "fill:$r $g $b<br>";
					}

				} // end if ($this->f_table==1)

				return $res;
			}	// end function print_tab_header

			function add_data_tab($arr_data, $line_height=7, $border="TRHBL", $data_align, $font="cordia", $font_size="14", $font_style="", $font_color=0, $fill_color=0, $tab_PageBreakTrigger=10)
			{
				$ret = true;
				if ($this->f_table==1) {

					$this->arr_column_align = $data_align;		// align for data (data_align ในที่นี้ยังไม่ได้ปรับการ map index ดังนั้น ใน function นี้ ต้องครอบ map index)
																								//		ทำให้ เฉพาะใน function นี้ ค่า $this->arr_column_align ที่ map แล้ว จะต้องเป็น
																								//	 	ค่านี้  $this->arr_column_align[$this->arr_column_map[$iii]]

					if (!$font) $font="cordia";
					if (strpos("b", strtolower($font_style)) !== false) $font = $font."b";
	//				echo "font=".$font."<br>";

					$d_align = "C";
					$max_line = 0;
					$arr_sub_data = (array) null;
					$arr_merge_grp = (array) null;
					$arr_msum_grp = (array) null;
					for($iii = 0; $iii < $cnt_column_head; $iii++) 	$arr_msum_grp[]=0;

					$cnt_column_head = count($this->arr_tab_head);
					$cnt_column = count($arr_data);
					$cnt_column_show = 0;
					for($jjj = 0; $jjj <= count($this->arr_tab_head); $jjj++)  if ($this->arr_column_sel[$jjj]==1) $cnt_column_show++;	
					
					if ($this->merge_rows) {
						$arr_merge_rows = explode(",", $this->merge_rows);
						$arr_rows_image = explode(",", $this->rows_image);
					} else {
						$arr_merge_rows = (array) null;
						$arr_rows_image = (array) null;
						for($iii = 0; $iii < $cnt_column_head; $iii++) {
							$arr_merge_rows[]="";
							$arr_rows_image[]="";
						}
					}
					
					$grpidx = "";
					$sum_merge = 0;
					$arr_text = (array) null;
					$arr_col_width = (array) null;
					$arr_col_align = (array) null;
					$cnt=0;
					for($iii = 0; $iii < $cnt_column_head; $iii++) {
						if ($this->arr_column_sel[$iii]==1) {	// 1 = แสดง column นี้
//							$nline = $arr_data[$iii];
							$nline = $arr_data[$this->arr_column_map[$iii]];
	//						echo "$iii-".$nline."|<br>";
							$chk_merge = strpos($nline,"<**");
							if ($chk_merge!==false) {
								$c = strpos($nline,"**>");
								if ($c!==false) {
									$mgrp = substr($nline, $chk_merge+3, $c-($chk_merge+3));
									if ($mgrp != $grpidx) {	// มีการเปลี่ยนกลุ่ม merge column ติดกัน
										$arr_merge_grp[$last_iii] = "end";
										$grpidx = $mgrp;
										$arr_merge_grp[$iii] = $grpidx;
										$arr_msum_grp[$grpidx]=(int)$this->arr_column_width[$iii];		//	(int)$this->arr_head_width[$iii];
										$sum_merge=(int)$this->arr_column_width[$iii];		//	(int)$this->arr_head_width[$iii];
									} else {
										$arr_merge_grp[$iii] = $grpidx;
										$arr_msum_grp[$grpidx]+=(int)$this->arr_column_width[$iii];	//	(int)$this->arr_head_width[$iii];
										$sum_merge+=(int)$this->arr_column_width[$iii];	//	(int)$this->arr_head_width[$iii];
									}
									$ntext = substr($nline, 0, $chk_merge)." ".substr($nline, $c+3);
								} else {
									$ret = false;
								}
							} else {
								if ($grpidx)  $arr_merge_grp[$last_iii] = "end";
								$arr_merge_grp[$iii] = "";
								$ntext = $nline;
								$grpidx = "";
							} // end if ($chk_merge!==false)
							$chk_mrow = strpos($ntext,"<&&row&&>");
							if ($chk_mrow!==false) {
								if ($arr_merge_rows[$iii]=="top")
									$arr_merge_rows[$iii] = "middle";
								elseif ($arr_merge_rows[$iii]=="")
									$arr_merge_rows[$iii] = "top";
								$this->merge_rows_h[$iii] += $line_height;
								$ntext = str_replace("<&&row&&>", " ", $ntext);
							} else {
								$chk_mrow = strpos($ntext,"<&&end&&>");
								if ($chk_mrow!==false) {
									$this->merge_rows_h[$iii] += $line_height;
									$arr_merge_rows[$iii] = "endmerge";  // บอกให้รู้ว่าปิด merge row
									$ntext = str_replace("<&&end&&>", " ", $ntext);
								} elseif ($arr_merge_rows[$iii]=="top" || $arr_merge_rows[$iii]=="middle") {
									$arr_merge_rows[$iii] = "endmerge";  // บอกให้รู้ว่าปิด merge row
									$this->merge_rows_h[$iii] += $line_height;
								} else
									$arr_merge_rows[$iii] = "";
							}
							$chk_image = strpos($ntext,"<img**");
							if ($chk_image!==false) {
									$c = strpos($ntext,"**img>");
									if ($c!==false) {
										$arr_rows_image[$iii] = substr($ntext, $chk_image+6, $c-($chk_image+6));
//										$arr_b = $this->MultiCellThaiCut($arr_msum_grp[$grpidx], (int)$line_height, $arr_text[$iii], $border, $data_align[$iii],$f_fill, false);
										$arr_b = $this->MultiCellThaiCut($arr_msum_grp[$grpidx], (int)$line_height, $arr_text[$iii], $border, $arr_column_align[$this->arr_column_map[$iii]],$f_fill, false);
										$ntext = substr($ntext, 0, $chk_image)."~".substr($ntext, $c+6);
	//									echo "$iii - image name=".$arr_rows_image[$iii].", text=$ntext<br>";
									} else {
										$ret = false;
									}
							} else {
									$arr_rows_image[$iii] = "";
							}
							$arr_text[] = $ntext;
							$arr_col_width[] = $this->arr_column_width[$iii];
							$arr_col_align[] = $this->arr_column_align[$this->arr_column_map[$iii]];
							$cnt++;
//							echo "just show ($cnt)-$ntext | ".$this->arr_column_width[$iii]." | ".$this->arr_column_align[$this->arr_column_map[$iii]]."<br>";
							$last_iii = $iii;
						} // if ($this->arr_column_sel[$iii]==1) {	// 1 = แสดง column นี้
					} // end for $iii (แต่ละ column)
					if ($ret && strlen($arr_merge_grp[$iii-1]) > 0) $arr_merge_grp[$iii-1] = "end"; // ตรวจสอบกรณี merge column ตัวสุดท้ายที่สุดพอดี
					
					if ($ret) {
						$grpidx = "";
						for($iii = 0; $iii < $cnt_column_show; $iii++) {	// เลือกเฉพาะรายการที่ แสดง
//							echo "arr_text [$iii]=".$arr_text[$iii]."<br>";
							if ($arr_merge_grp[$iii] == "end") {
//							 	$arr_b = $this->MultiCellThaiCut($arr_msum_grp[$grpidx], (int)$line_height, $arr_text[$iii], $border, $data_align[$iii],$f_fill,false);
							 	$arr_b = $this->MultiCellThaiCut($arr_msum_grp[$grpidx], (int)$line_height, $arr_text[$iii], $border, $arr_col_align[$iii],$f_fill,false);
							} elseif ($arr_merge_grp[$iii]) {
								$grpidx = $arr_merge_grp[$iii];
//							 	$arr_b = $this->MultiCellThaiCut($arr_msum_grp[$grpidx], (int)$line_height, $arr_text[$iii], $border, $data_align[$iii],$f_fill,false);
							 	$arr_b = $this->MultiCellThaiCut($arr_msum_grp[$grpidx], (int)$line_height, $arr_text[$iii], $border, $arr_col_align[$iii],$f_fill,false);
							} else {
								$grpidx="";
//								$arr_b = $this->MultiCellThaiCut($this->arr_head_width[$iii], (int)$line_height, $arr_text[$iii], $border, $data_align[$iii],$f_fill,false);
								$arr_b = $this->MultiCellThaiCut($arr_col_width[$iii], (int)$line_height, $arr_text[$iii], $border, $arr_col_align[$iii],$f_fill,false);
							}
							
						 	$arr_sub_data[] = $arr_b;
//							echo "$iii - ".$arr_merge_grp[$iii]." sub_data:".implode(",", $arr_sub_data[$iii])."<br>";
							$this_line = count($arr_sub_data[$iii]);
							if($this_line > $max_line) $max_line = $this_line;
//							if (strlen($arr_merge_rows[$iii]) > 0) echo "ntext=$ntext|".$this_line.":".$max_line."<br>";
						}
					}
					
					$head_t_w = 0;
					if ($ret) {
//						echo "arr_tab_head>>".implode(",",$this->arr_tab_head)."<br>";
//						echo "cnt_line_head=$cnt_line_head, cnt_column_head=$cnt_column_head<br>";
						for($jjj = 0; $jjj < $cnt_column_show; $jjj++) {
							$head_t_w+=$arr_col_width[$jjj];		// $this->arr_head_width[$jjj];
						}
					} // end if $ret
					
					if ($ret) {
						$this->tabdata_fill_color = $fill_color;
						$this->tabdata_font_name = $font;
						$this->tabdata_font_size = $font_size;
						$this->tabdata_font_style = $font_style;
						$this->tabdata_font_color = $font_color;

						$this->SetFont($font,'',$font_size);
						if (strlen($font_color)==6) {
							$r=substr($font_color,0,2);
							$g=substr($font_color,2,2);
							$b=substr($font_color,4,2);
						} else { $r="00"; $g="00"; $b="00"; }
//						echo "font:$r $g $b<br>";
						$this->SetTextColor(hexdec("$r"),hexdec("$g"),hexdec("$b"));

						$f_fill = 0;
						if (strlen($fill_color)==6) {
							$r=substr($fill_color,0,2);
							$g=substr($fill_color,2,2);
							$b=substr($fill_color,4,2);
							$this->SetFillColor(hexdec("$r"),hexdec("$g"),hexdec("$b"));
//							echo "fill:$r $g $b<br>";
							$f_fill = 1;
						}
				
						$start_x = $this->x;			$start_y = $this->y;				$max_y = $this->y;
						$first_y = $this->y;
					
						$columngrp="";

						$sum_merge = 0;
						$text_merge = "";
						$d_align = "";
							
						for($cntline = 0; $cntline < $max_line; $cntline++) {
							$sum_merge = 0;
							for($iii = 0; $iii < $cnt_column_show; $iii++) {
								$nline = $arr_sub_data[$iii][$cntline];
								$sum_w = 0;
								for($jjj = 0; $jjj <= $iii; $jjj++)  $sum_w+=(int)$arr_col_width[$jjj];		// 	(int)$this->arr_head_width[$jjj];
								
								$t_border = strtoupper($border);
								$mid_hline = (strpos($t_border, "H")!==false ? true : false);  // H แทนเส้นกขวางกลาง ถ้ามีแปลว่า ขึดเส้นขวางตรงกลาง
								$t_border = str_replace("H", "", $t_border);	// กำหนดค่า H ที่ $mid_hline แล้ว ก็เอาออกจาก $t_border
								if ($max_line > 1) { // กรณี เป็น cell หลายบรรทัด
									if ($cntline==0) { // บรรทัดแรกของ cell เอาเส้นล่างออก แต่ถ้าเป็น cell กลางของ merge row ก็เอาเส้นบนออกด้วย
										if ($arr_merge_rows[$iii]=="middle") $t_border = str_replace("T", "", $t_border);
										$t_border = str_replace("B", "", $t_border);
									} elseif ($cntline==$max_line-1) {
										if ($arr_merge_rows[$iii]=="middle") $t_border = str_replace("B", "", $t_border);
										$t_border = str_replace("T", "", $t_border);
									} else  { 
										$t_border = str_replace("B", "", $t_border); 
										$t_border = str_replace("T", "", $t_border); 
									}
								} elseif ($max_line == 1) {
									if ($arr_merge_rows[$iii]=="top") {
										$t_border = str_replace("B", "", $t_border);
									} elseif ($arr_merge_rows[$iii]=="middle") {
										$t_border = str_replace("T", "", $t_border);
										$t_border = str_replace("B", "", $t_border);
									} elseif ($arr_merge_rows[$iii]=="endmerge") {
										$t_border = str_replace("T", "", $t_border);
									}
								}
//								echo "$iii - max_line=$max_line, cntline=$cntline, border=$t_border, merge:".$arr_merge_rows[$iii]."<br>";
//								echo "col width [$iii] = ".$arr_col_width[$iii]."<br>";
								
								if ($iii == $cnt_column_show-1) $next_line = 1; else $next_line = 0;
								if ($arr_merge_grp[$iii]) {
									if ($iii > 0) {
										if ($sum_merge > 0 && strpos($arr_merge_grp[$iii], "end") !== false) { // ถ้า จบ merge group
											$sum_merge+=(int)$arr_col_width[$iii];	//  (int)$this->arr_head_width[$iii];
											if (!$mid_hline) { // ถ้าไม่กำหนดค่า H ใน border ก็จะไม่ลากเส้นขวาง
												if ($mergecell_first != 0) {  // ถ้า merge cell ไม่ได้เริ่มตั้งแต่ cell แรก เอา L ออก แต่เป็น cell สุดท้าย เหลือ R
													$t_border = str_replace("L", "", $t_border);
												}
											}
											if ($arr_merge_rows[$iii]=="endmerge") {
												if ($arr_rows_image[$iii]) {
													$this->print_column_image($sum_merge, $merge_rows_h, $text_merge, $line_height, $arr_rows_image[$iii], $t_border, $d_align, $f_fill, $next_line);
													$arr_rows_image[$iii] = "";
												} else { // ถ้า เป็น text ล้วน ๆ
													$this->Cell($sum_merge, $line_height, $text_merge, $t_border, $next_line, $d_align, $f_fill);
												} // end if ($arr_rows_image[$iii])
												$arr_merge_rows[$iii]="";
												$arr_rows_image[$iii]="";
												$this->merge_rows_h[$iii] = 0;
												$merge_rows_h = 0;
											} else	// ถ้า ไม่ end merge row
												if (strlen(trim($arr_merge_rows[$iii])) > 0)	// ถ้ายังอยู่ใน merge row
													$this->Cell($sum_merge, $line_height, "", $t_border, $next_line, $d_align, $f_fill);
												else
													$this->Cell($sum_merge, $line_height, $text_merge, $t_border, $next_line, $d_align, $f_fill);
//											echo "sum_merge=$sum_merge, line_height=$line_height, text_merge=$text_merge<br>";
											$sum_merge = 0;
											$text_merge = "";
										} else {	// ถ้ายังอยู่ใน merge group แต่ยังไม่จบ merge group
											if  ($sum_merge == 0) { // ถ้าเป็น merge group ใหม่
												$d_align = ($arr_col_align[$iii] ? $arr_col_align[$iii] : "C");
												$mergecell_first = $iii;
												if (!$text_merge) $text_merge = $nline;
											}
											$sum_merge+=(int)$arr_col_width[$iii];	//	(int)$this->arr_head_width[$iii];
											if (strlen(trim($arr_merge_rows[$iii])) > 0) // ถ้าเป็น merge row แรก
												$merge_rows_h = $this->merge_rows_h[$iii];
										} //  end if ($sum_merge > 0 && strpos($arr_merge_grp[$iii], "end") !== false)
									} else { // $iii == 0 เป็น merge cell  แรก
										// ถ้าเป็น merge cell ตั้งแต่อันแรก กำหนดค่า merge แรก
										$sum_merge+=(int)$arr_col_width[$iii];	//	(int)$this->arr_head_width[$iii];
//										$d_align = ($data_align[$iii] ? $data_align[$iii] : "C");
										$d_align = ($arr_col_align[$iii] ? $arr_col_align[$iii] : "C");
										if (!$text_merge) $text_merge = $nline;
										$mergecell_first = $iii;
										if (strlen(trim($arr_merge_rows[$iii])) > 0) // ถ้าเป็น merge row แรก
											$merge_rows_h = $this->merge_rows_h[$iii];
									} // end if ($iii > 0)
								} else { //  not if ($arr_merge_grp[$iii]
//									echo "sum_merge=$sum_merge<br>";
									if ($sum_merge > 0) { // ถ้าตัวต่อไปไม่ใช่ merge cell กลุ่มเดียวกัน ก็จะทำการปิด merge cell
										$col_w = $sum_merge;
									} else {
										$col_w = $arr_col_width[$iii];		// $this->arr_head_width[$iii];
									}
									if (!$mid_hline) { // ถ้าไม่กำหนดค่า H ใน border ก็จะไม่ลากเส้นขวาง
										if ($iii != 0)  $t_border = str_replace("L", "", $t_border);
										if ($next_line != 1)  $t_border = str_replace("R", "", $t_border);
									}
									if ($arr_merge_rows[$iii]=="endmerge") {
										if ($arr_rows_image[$iii]) {
//											$this->print_column_image($col_w, $this->merge_rows_h[$iii], $nline, $line_height, $arr_rows_image[$iii], $t_border, $data_align[$iii], $f_fill, $next_line);
											$this->print_column_image($col_w, $this->merge_rows_h[$iii], $nline, $line_height, $arr_rows_image[$iii], $t_border, $arr_col_align[$iii], $f_fill, $next_line);
											$arr_rows_image[$iii] = "";
										} else {	// ถ้าไม่มีรูป
											$this->Cell($col_w, $line_height, $nline, $t_border, $next_line, $d_align, $f_fill);
										}
										$arr_merge_rows[$iii]="";
										$arr_rows_image[$iii]="";
										$this->merge_rows_h[$iii] = 0;
//										echo "merge $iii-".$arr_merge_rows[$iii]." next_line=$next_line [$nline] x:y=".$this->x.":".$this->y."<br>";
									} else {  // ถ้าไม่เป็น endmerge
//										if (!$arr_merge_rows[$iii]) echo "1..next_line=$next_line [$nline] x:y=".$this->x.":".$this->y." h=$line_height<br>";
										if (strlen(trim($arr_merge_rows[$iii])) > 0) {
//											$this->Cell($col_w, $line_height, "", $t_border, $next_line, $data_align[$iii], $f_fill);
											$this->Cell($col_w, $line_height, "", $t_border, $next_line, $arr_col_align[$iii], $f_fill);
//											echo "merge row - $iii - x:y=".$this->x.":".$this->y." - line_h=$line_height<br>";
										} else { // ถ้าไม่ใช้การ merge row
											if ($arr_rows_image[$iii]) { // ถ้ามีรูป
//												$this->print_column_image($col_w, $line_height, $nline, $line_height, $arr_rows_image[$iii], $t_border, $data_align[$iii], $f_fill, $next_line);
												$this->print_column_image($col_w, $line_height, $nline, $line_height, $arr_rows_image[$iii], $t_border, $arr_col_align[$iii], $f_fill, $next_line);
												$arr_rows_image[$iii] = "";
											} else { // ถ้าไม่มีรูป
//												$this->Cell($col_w, $line_height, $nline, $t_border, $next_line, $data_align[$iii], $f_fill);
												$this->Cell($col_w, $line_height, $nline, $t_border, $next_line, $arr_col_align[$iii], $f_fill);
//												if (!$arr_merge_rows[$iii]) echo "$iii..next_line=$next_line [$nline] x:y=".$this->x.":".$this->y." h=$line_height,  col_w=$col_w<br>";
											} // end if ($arr_rows_image[$iii])
										} // end if (strlen(trim($arr_merge_rows[$iii]))
									} // if ($arr_merge_rows[$iii]=="endmerge")
								} // end if ($chk_merge!==false)
							} //// end for $iii loop
							$this->tab_pagebreak(strtoupper($border), $tab_PageBreakTrigger);
						} // end for $cntline
					} // end if ($ret)
					$this->merge_rows = implode(",", $arr_merge_rows);
					$this->rows_image = implode(",", $arr_rows_image);
				} // end if ($this->f_table==1)
				return $ret;
			}	// end function create_tab
			
			function print_column_image($w, $h, $text, $text_h, $imgfile, $border, $align, $f_fill, $next_line) {
					$d_align = ($align ? $align : "C");
					$save_y = $this->y;  $save_x = $this->x;  
//					echo "begin $save_x:$save_y, h=$h, text_h=$text_h<br>";
					$this->Cell($w, $text_h, "", $border, $next_line, $align, $f_fill);
					$this->y = $save_y - $h + $text_h;
					$this->x = $save_x;
					$arr_buff = explode("~", $text);
					if (strlen(trim($arr_buff[0])) > 0) {
						$this->Cell($w, $text_h, $arr_buff[0], $border, 1, $align, $f_fill);
						$this->x = $save_x;  
					}
					if (file_exists($imgfile)) {
						list($width, $height, $type, $attr) = getimagesize($imgfile);
						if ($width > $w-4) {
							$image_w = 	$w-4;
							$image_h = floor($image_w / $width * $height);
							if ($image_h > $h-4) {
								$buff_h = 	$h-4;
								$image_w = floor($buff_h / $image_h * $image_w);
								$image_h = $buff_h;
							}
						} elseif ($height > $h-4) {
							$image_h = 	$h-4;
							$image_w = floor($image_h / $height * $width);
							if ($image_w > $w-4) {
								$buff_w = 	$w-4;
								$image_h = floor($buff_w / $image_w * $image_h);
								$image_w = $buff_w;
							}
						} else {
							$image_h = $height;
							$image_w = $width;
						}
						if ($align=="L")
							$posx = $this->x+2;
						elseif ($align=="C")
							$posx = $this->x+($w/2)-($image_w/2);
						else
							$posx = $this->x+$w-2-$image_w;
						$posy = $this->y+2;
//						echo "x:y-".$this->x.":".$this->y." w:h-$width:$height, $image_w:$image_h |".$this->merge_rows_h[$iii]."<br>";
						$this->Image($imgfile, $posx, $posy, $image_w, $image_h);
						$this->x = $save_x;  
						$this->y += $image_h+4;
//						echo "1..image x=".$this->x.",y=".$this->y."<br>";
					} else {
						$this->x = $save_x;  
						$this->y += $h;
//						echo "2..image x=".$this->x.",y=".$this->y."<br>";
					}
					
					if (strlen(trim($arr_buff[1])) > 0) {
						$this->Cell($w, $text_h, $arr_buff[1], $border, 1, $align, $f_fill);
					}
					if ($next_line==1) {
						$this->x = $this->lMargin;  
//						echo "1. x:y=".$this->x.":".$this->y."<br>";
					} else {
						$this->x = $save_x+$w;  
						$this->y = $save_y;
//						echo "2. x:y=".$this->x.":".$this->y."<br>";
					}
			} // end function print_column_image

			function close_tab($tail_text)
			{
				if ($this->f_table==1) {
					$this->f_table = 0;
					$this->arr_tab_head = (array) null;
					$this->arr_head_width = (array) null;
					$this->head_fill_color = "";
					$this->head_font_name = "";
					$this->head_font_size = "";
					$this->head_font_style = "";
					$this->head_font_color = "";
					$this->head_border = "";
					$this->arr_head_align = (array) null;
					$this->head_line_height = "";
					$this->tabdata_fill_color = "";
					$this->tabdata_font_name = "";
					$this->tabdata_font_size = "";
					$this->tabdata_font_style = "";
					$this->tabdata_font_color = "";
					$this->merge_rows = "";
					$this->rows_image = "";
					$this->merge_rows_h = (array) null;
					$this->arr_column_map = (array) null;
					$this->arr_column_sel = (array) null;
					$this->arr_column_width = (array) null;
					$this->arr_column_align = (array) null;
				} // end if ($this->f_table==1)
			}	// end function create_tab

			function line_MultiCellThaiCut($w,$h,$txt,$border=0,$align='J',$fill=0)
			{
				$cw=&$this->CurrentFont['cw'];
				if($w==0)
					$w=$this->w-$this->rMargin-$this->x;
				$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
				$s=str_replace("\r",'',$txt);
				$nb=strlen($s);
				if($nb>0 && $s[$nb-1]=="\n")
					$nb--;
				$b=0;
				if($border)
				{
					if($border==1)
					{
						$border='LTRB';
						$b='LRT';
						$b2='LR';
					}
					else
					{
						$b2='';
						if(strpos($border,'L')!==false)
							$b2.='L';
						if(strpos($border,'R')!==false)
							$b2.='R';
						$b=(strpos($border,'T')!==false) ? $b2.'T' : $b2;
					}
				}
				$s = $this->thaiCutLinePDF($s, $w, "\n");
//				echo "***$s<br>";
				$sub_s = explode("\n", $s);
				for($ii = 0; $ii < count($sub_s); $ii++) {
//					echo "$ii-$sub_s[$ii]|<br>";
					if($border && $ii==1)
						$b=$b2;
					if($border && $ii == (count($sub_s)-1) && strpos($border,'B')!==false)
						$b.='B';
//					$this->Cell($w,$h,trim($sub_s[$ii]),$b,2,$align,$fill);
					$str_w = $this->GetStringWidth($sub_s[$ii]);
//					echo "LinethaiCut sub_s [$ii]=".$sub_s[$ii]." ($w::$str_w) $h,$txt,$border,$align,$fill<br>";
				}
//				$this->x=$this->lMargin;
/*
				//Output text with automatic or explicit line breaks
				$cw=&$this->CurrentFont['cw'];
				if($w==0)
					$w=$this->w-$this->rMargin-$this->x;
				$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
				$s=str_replace("\r",'',$txt);
				$nb=strlen($s);
				if($nb>0 && $s[$nb-1]=="\n")
					$nb--;
				$s = $this->thaiCutLinePDF($s, $w, "\n");
				$sub_s = explode("\n", $s);
//				echo "***sub_s:".implode("|",$sub_s)."<br>";
				for($i=0; $i < count($sub_s); $i++) {
					$str_w = $this->GetStringWidth($sub_s[$i]);
					echo "$i-".$sub_s[$i]." ($str_w == $w)<br>";
				}
*/				
				return $sub_s;
			} // end function

			function tab_pagebreak($border, $at_end_up=10) {
				if ($this->f_table==1) {
					if(($this->h - $this->y - $at_end_up) < $at_end_up){ 
						$have_l = strpos($border, "L");
						$have_r = strpos($border, "R");	
						$have_h = strpos($border, "H");

						$cnt_column_head = count($this->arr_tab_head);
						$this->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ
						if ($have_l !== false) {	
							$line_start_y = $this->y;		$line_end_y = $this->h - $at_end_up;
							$line_start_x = $this->lMargin;		$line_end_x = $line_start_x;
							$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางแรก
						}
						for($i=0; $i<$cnt_column_head; $i++) {
							if ($this->arr_column_sel[$i]==1) {
//								$line_start_x += (int)$this->arr_head_width[$i];  $line_end_x += (int)$this->arr_head_width[$i];
								$line_start_x += (int)$this->arr_column_width[$i];  $line_end_x += (int)$this->arr_column_width[$i];
								if ($have_h !== false && $i != ($cnt_column_head-1))
									$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางที่เหลือตรงกลาง
								elseif ($have_r !== false && $i == ($cnt_column_head-1)) 
									$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางสุดท้าย
							} // end if ($this->arr_column_sel[$i]==1)
						}	// end for $i
						$this->AddPage();
						$max_y = $this->y;
						$this->print_tab_header();
//						echo "print_tab_header $line_start_x,$line_start_y-->$line_end_x,$line_end_y<br>";
					} // end if1
//					$this->x = $start_x;		//	$this->y = $max_y;
				} // end if ($this->f_table==1) 
			} // end function tab_pagebreak

		}
		// end class
?>