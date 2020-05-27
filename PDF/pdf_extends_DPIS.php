<?php
		if (file_exists("function_repgen.php")) include ("function_repgen.php");
		else if (file_exists("php_scripts/function_repgen.php")) include ("php_scripts/function_repgen.php");
		else if (file_exists("../php_scripts/function_repgen.php")) include ("../php_scripts/function_repgen.php");
		else if (file_exists("../../php_scripts/function_repgen.php"))  include ("../../php_scripts/function_repgen.php");
//		if (file_exists("pdf_wordarray_thaicut.php")) include ("pdf_wordarray_thaicut.php");
//		else if (file_exists("php_scripts/pdf_wordarray_thaicut.php")) include ("php_scripts/pdf_wordarray_thaicut.php");
//		else if (file_exists("../php_scripts/pdf_wordarray_thaicut.php")) include ("../php_scripts/pdf_wordarray_thaicut.php");
//		else if (file_exists("../../php_scripts/pdf_wordarray_thaicut.php"))  include ("../../php_scripts/pdf_wordarray_thaicut.php");
//		echo "PHP_SELF PATH=".$_SERVER['PHP_SELF']." file exists::(".file_exists("../php_scripts/pdf_wordarray_thaicut.php").")<br>";

		class PDF extends FPDF  
		{
			
			var $arr_word_thai_cut;
			var $specword;
			var $pvword;
			var $amword;

			function Open()
			{
				global $arr_word_thai_cut;
				// extend from function Open() 
//				echo "pdf_extend word_thai_cut>>".implode(",",$arr_word_thai_cut)."<br>";
				FPDF::Open();
				// Add fonts use in document
				if(FPDF_VERSION > '1.51'){
					$this->AddFont('angsab','','../../PDF/font/angsab.php');
					$this->AddFont('angsa','','../../PDF/font/angsa.php');
					$this->AddFont('cordia','','../../PDF/font/cordia.php');
					$this->AddFont('cordiab','','../../PDF/font/cordiab.php');
					$this->AddFont('thsarabun','','../../PDF/font/thsarabun.php');
					$this->AddFont('thsarabunb','','../../PDF/font/thsarabunb.php');
					$this->AddFont('browallia','','../../PDF/font/browa.php');
					$this->AddFont('browalliab','','../../PDF/font/browab.php');
				}else{
					$this->AddFont('angsab','','../../PDF/font/angsab_old.php');
					$this->AddFont('angsa','','../../PDF/font/angsa_old.php');
					$this->AddFont('cordia','','../../PDF/font/cordia.php');
					$this->AddFont('cordiab','','../../PDF/font/cordiab.php');
					$this->AddFont('thsarabun','','../../PDF/font/thsarabun.php');
					$this->AddFont('thsarabunb','','../../PDF/font/thsarabunb.php');
					$this->AddFont('browallia','','../../PDF/font/browa.php');
					$this->AddFont('browalliab','','../../PDF/font/browab.php');
				}
				$this->specword = array("ราชการ", "รัฐบาล", "อธิบดี", "คณะบดี", "ตำแหน่ง", "อำเภอ","จังหวัด","ปกครอง","ปฏิบัติ","สัญญา","เจ้าหน้าที่", "และ", "เกียรติยศ","บรรจุ", "ดำรง", "บำเหน็ด", "โรงเรียน", "มหาบัณฑิต", "บัณฑิต", "บริหาร", "ชำนาญ", "ศาสน", "ศาสตร์", "พัฒนา", "ภาพ", "วันที่", "เลขที่", "ประเภท", "นโยบาย", "นักวิเคราะห์", "กำหนด", "วัน", "เดือน", "ปี", "เข้า", "สู่", "อายุ", "สาธารณะ", "ระบบ", "วัฒนา", "บรรจง", "สกุล", "สภา", "สถานะ", "สถาพร", "สถาปนา", "สถาปนิก", "สถาบัน", "จักร", "อาทิตย์", "จันทร์", "อังคาร", "พุธ", "พฤหัสบดี", "ศุกร์", "เสาร์", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม","เหนือ","ใต้","ตะวันออก","ตะวันตก","เฉียง","อีสาน","ตำรวจ","ทหาร","พยาบาล","อากาศ","วิหาร","ตำนาน","นครบาล","ภูธร","แพทย์","สูติ","ปริญญา","สรรหา","เวลา","สาขา","ประถม","มัธยม","ปริญญา","ศึกษา","กรุง","วงศ์","บูลย์","ชุด"   ,"นัก","วิชาการ","ตรวจสอบ","ภายใน","กลุ่ม","นโยบาย","วิเคราะห์","คอม","พิวเตอร์","ทุน","คุณ"); // เพิ่มคำเฉพาะได้ที่นี่
				$this->pvword = array("กรุงเทพมหานคร","กรุงเทพฯ","กรุงเทพ","กทม.","กทม","พระนคร","กระบี่", "กาญจนบุรี", "กาฬสินธุ์", "กำแพงเพชร", "ขอนแก่น", "จันทบุรี", "ฉะเชิงเทรา", "ชลบุรี", "ชัยนาท", "ชัยภูมิ", "ชุมพร", "เชียงราย", "เชียงใหม่", "ตรัง", "ตราด", "ตาก", "นครนายก", "นครปฐม", "นครพนม", "นครราชสีมา", "นครศรีธรรมราช", "นครสวรรค์", "นนทบุรี", "นราธิวาส", "น่าน", "บุรีรัมย์", "ปทุมธานี", "ประจวบคีรีขันธ์", "ปราจีนบุรี", "ปัตตานี", "พระนครศรีอยุธยา", "พะเยา", "พังงา", "พัทลุง", "พิจิตร", "พิษณุโลก", "เพชรบุรี", "เพชรบูรณ์", "แพร่", "ภูเก็ต", "มหาสารคาม", "มุกดาหาร", "แม่ฮ่องสอน", "ยโส ธร", "ยะลา", "ร้อยเอ็ด", "ระนอง", "ระยอง", "ราชบุรี", "ลพบุรี", "ลำปาง", "ลำพูน", "เลย", "ศรีสะเกษ", "สกลนคร", "สงขลา", "สตูล", "สมุทรปราการ", "สมุทรสงคราม", "สมุทรสาคร", "สระแก้ว", "สระบุรี", "สิงห์บุรี", "สุโขทัย", "สุพรรณบุรี", "สุราษฎร์ธานี", "สุรินทร์", "หนองคาย", "หนองบัวลำภู", "อ่างทอง", "อำนาจเจริญ", "อุดรธานี", "อุตรดิตถ์", "อุทัยธานี", "อุบลราชธานี");
				$this->amword = array("มายอ", "ทุ่งยางแดง", "สายบุรี", "ดงเจริญ", "วชิรบารมี", "ชนแดน", "หล่มสัก", "หล่มเก่า", "วิเชียรบุรี", "ศรีเทพ", "หนองไผ่", "บึงสามพัน", "น้ำหนาว", "วังโป่ง", "เขาค้อ", "จอมบึง", "สวนผึ้ง", "ดำเนินสะดวก", "บ้านโป่ง", "บางแพ", "โพธาราม", "ปากท่อ", "วัดเพลง", "บ้านคา", "ไทรโยค", "บ่อพลอย", "ศรีสวัสดิ์", "ท่ามะกา", "ท่าม่วง", "ทองผาภูมิ", "สังขละบุรี", "พนมทวน", "เลาขวัญ", "ด่านมะขามเตี้ย", "หนองปรือ", "ห้วยกระเจา", "ท่ากระดาน", "บ้านทวน", "เดิมบางนางบวช", "ด่านช้าง", "บางปลาม้า", "ศรีประจันต์", "ดอนเจดีย์", "สองพี่น้อง", "สามชุก", "อู่ทอง", "หนองหญ้าไซ", "กำแพงแสน", "นครชัยศรี", "ดอนตูม", "บางเลน", "สามพราน", "พุทธมณฑล", "กระทุ่มแบน", "บ้านแพ้ว", "บางคนที", "อัมพวา", "เขาย้อย", "หนองหญ้าปล้อง", "ชะอำ", "ท่ายาง", "บ้านลาด", "บ้านแหลม", "แก่งกระจาน", "กุยบุรี", "ทับสะแก", "บางสะพาน", "บางสะพานน้อย", "ปราณบุรี", "หัวหิน", "สามร้อยยอด", "พรหมคีรี", "ลานสกา", "ลานสะกา", "ฉวาง", "พิปูน", "เชียรใหญ่", "ชะอวด", "ท่าศาลา", "ทุ่งสง", "นาบอน", "ทุ่งใหญ่", "ปากพนัง", "ร่อนพิบูลย์", "สิชล", "ขนอม", "หัวไทร", "บางขัน", "ถ้ำพรรณรา", "จุฬาภรณ์", "พระพรหม", "นบพิต้า", "ช้างกลาง", "เฉลิมพระเกียรติ", "เสือหึง", "สวนหลวง", "หินตก", "ดวนชะลิก", "กะปาง", "เขาพนม", "เกาะลันตา", "คลองท่อม", "อ่าวลึก", "จุน", "เชียงคำ", "เชียงม่วน", "ดอกคำใต้", "ปง", "แม่ใจ", "ภูซาง", "ภูกามยาว", "เวียงชัย", "ย่านตาขาว", "ปะเหลียน", "สิเกา", "ห้วยยอด", "วังวิเศษ", "นาโยง", "รัษฎา", "หาดสำราญ", "กงหรา", "เขาชัยสน", "ตะโหมด", "ควนขนุน", "ปากพะยูน", "ศรีบรรพต", "กิ่งศลีบรรพต", "ป่าบอน", "บางแก้ว", "ป่าพะยอม", "ศรีนครินทร์", "โคกโพธิ์", "หนองจิก", "ปะนาเระ", "เชียงของ", "เทิง", "พาน", "ป่าแดด", "แม่จัน", "เชียงแสน", "แม่สาย", "แม่สรวย", "เวียงป่าเป้า", "พญาเม็งราย", "เวียงแก่น", "ขุนตาล", "แม่ฟ้าหลวง", "แม่ลาว", "เวียงเชียงรุ้ง", "ดอยหลวง", "ขุนยวม", "ปาย", "แม่สะเรียง", "แม่ลาน้อย", "สบเมย", "ปางมะผ้า", "ม่วยต่อ", "โกรกพระ", "ชุมแสง", "หนองบัว ", "บรรพตพิสัย", "เก้าเลี้ยว", "ตาคลี", "ท่าตะโก", "ไพศาลี", "พยุหะคีรี", "ลาดยาว", "ตากฟ้า", "แม่วงก์", "แม่เปิน", "ชุมตาบง", "ห้วยน้ำหอม", "ชุมต", "แม่เล่ย์", "ทัพทัน", "สว่างอารมณ์", "หนองฉาง", "หนองขาหย่าง", "บ้านไร่", "ลานสัก", "ห้วยคด", "ไทรงาม", "คลองลาน", "ขาณุวรลักษบุรี", "คลองขลุง", "พรานกระต่าย", "ลานกระบือ", "ทรายทองวัฒนา", "ปางศิลาทอง", "บึงสามัคคี", "โกสัมพีนคร", "บ้านตาก", "สามเงา", "แม่ระมาด", "ท่าสองยาง", "แม่สอด", "พบพระ", "อุ้มผาง", "วังเจ้า", "ท่าปุย", "บ้านด่านลานหอย", "คีรีมาศ", "กงไกรลาศ", "ศรีสัชนาลัย", "ศรีสำโรง", "สวรรคโลก", "ศรีนคร", "ทุ่งเสลี่ยม", "นครไทย", "ชาติตระการ", "บางระกำ", "บางกระทุ่ม", "พรหมพิราม", "วัดโบสถ์", "วังทองหลาง", "เนินมะปราง", "วังทรายพูน", "โพธิ์ประทับช้าง", "ตะพานหิน", "บางมูลนาก", "โพทะเล", "สามง่าม", "ทับคล้อ", "สากเหล็ก", "บึงนาราง", "สว่างแดนดิน", "ส่องดาว", "เต่างอย", "โคกศรีสุพรรณ", "เจริญศิลป์", "โพนนาแก้ว", "ภูพาน", "วานรนิวาส", "กุดเรือคำ", "บ้านหัน", "ปลาปาก", "ท่าอุเทน", "บ้านแพง", "ธาตุพนม", "นาแก", "เรณูนคร", "ศรีสงคราม", "นาหว้า", "โพนสวรรค์", "นาทม", "วังยาง", "นิคมคำสร้อย", "ดอนตาล", "ดงหลวง", "คำชะอี", "หว้านใหญ่", "หนองสูง", "จอมทอง", "แม่แจ่ม", "เชียงดาว", "ดอยสะเก็ด", "แม่แตง", "แม่ริม", "สะเมิง", "ฝาง", "แม่อาย", "พร้าว", "สันป่าตอง", "สันกำแพง", "สันทราย", "หางดง", "ฮอด", "ดอยเต่า", "อมก๋อย", "สารภี", "เวียงแหง", "เวียงแห", "ไชยปราการ", "แม่วาง", "แม่ออน", "ดอยหล่อ", "นครเชียงใหม่", "แขวงกา", "แขวงศร", "เม็งรา", "แม่ทา", "บ้านโฮ่ง", "ลี้", "ทุ่งหัวช้าง", "ป่าซาง", "บ้านธิ", "เวียงหนองล่อง", "แม่เมาะ", "เกาะคา", "เสริมงาม", "งาว", "แจ้ห่ม", "วังเหนือ", "เถิน", "แม่พริก", "แม่ทะ", "สบปราบ", "ห้างฉัตร", "ตรอน", "ท่าปลา", "น้ำปาด", "ไม้แก่น", "ไทรทอง", "ยะหริ่ง", "ยะรัง", "กะพ้อ", "แม่ลาน", "เบตง", "บันนังสตา", "ธารโต", "กิ่งธารโต", "ยะหา", "รามัน", "กรงปินัง", "ตากใบ", "บาเจาะ", "ยี่งอ", "ระแงะ", "รือเสาะ", "ศรีสาคร", "แว้ง", "สุคิริน", "สุไหงโก-ลก", "สุไหงปาดี", "จะแนะ", "เจาะไอร้อง", "บางนรา", "พระนคร", "ดุสิต", "หนองจอก", "บางรัก", "บางเขน", "บางกะปิ", "ปทุมวัน", "ป้อมปราบศัตรูพ่าย", "พระโขนง", "มีนบุรี", "ลาดกระบัง", "ยานนาวา", "สัมพันธวงศ์", "พญาไท", "ธนบุรี", "บางกอกใหญ่", "ห้วยขวาง", "คลองสาน", "ตลิ่งชัน", "บางกอกน้อย", "บางขุนเทียน", "ภาษีเจริญ", "หนองแขม", "ราษฎร์บูรณะ", "บางพลัด", "ดินแดง", "บึงกุ่ม", "สาธร", "บางซื่อ", "จตุจักร", "บางคอแหลม", "ประเวศ", "คลองเตย", "ราชเทวี", "ลาดพร้าว", "วัฒนา", "บางแค", "หลักสี่", "สายไหม", "คันนายาว", "สะพานสูง", "วังทอง", "คลองสามวา", "บางนา", "ทวีวัฒนา", "ทุ่งครุ", "บางบอน", "บ้านทะวาย", "บางบ่อ", "บางพลี", "พระประแดง", "พระสมุทรเจดีย์", "บางเสาธง", "บางกรวย", "บางใหญ่", "บางบัวทอง", "ไทรน้อย", "ปากเกร็ด", "นครนนทบุรี", "แขวงท่าท", "คลองหลวง", "ธัญบุรี", "หนองเสือ", "ลาดหลุมแก้ว", "ลำลูกกา", "สามโคก", "คูคต", "ท่าเรือ", "นครหลวง", "บางไทร", "บางบาล", "บางปะอิน", "บางปะหัน", "ปางปะหัน", "ผักไห่", "ภาชี", "ลาดบัวหลวง", "วังน้อย", "เสนา", "บางซ้าย", "อุทัย", "มหาราช", "บ้านแพรก", "ไชโย", "ป่าโมก", "โพธิ์ทอง", "แสวงหา", "วิเศษชัยชาญ", "สามโก้", "พัฒนานิคม", "โคกสำโรง", "ชัยบาดาล", "ท่าวุ้ง", "บ้านหมี่", "ท่าหลวง", "สระโบสถ์", "โคกเจริญ", "ลำสนธิ", "หนองม่วง", "บ้านเช่า", "บางระจัน", "ค่ายบางระจัน", "พรหมบุรี", "ปลายพระยา", "ลำพัด", "ลำทับ", "เหนือคลอง", "เกาะยาว", "กะปง", "ตะกั่วทุ่ง", "ตะกั่วป่า", "คุระบุรี", "ทับปุด", "ท้ายเหมือง", "กะทู้", "ถลาง", "ทุ่งคา", "กาญจนดิษฐ์", "ดอนสัก", "เกาะสมุย", "เกาะพะงัน", "ไชยา", "ท่าชนะ", "คีรีรัฐนิคม", "บ้านตาขุน", "พนม", "ท่าฉาง", "บ้านนาสาร", "บ้านนาเดิม", "เคียนซา", "เวียงสระ", "พระแสง", "พุนพิน", "ชัยบุรี", "วิภาวดี", "เกาะพงัน", "เกาะเต่า", "บ้านดอน", "ละอุ่น", "กะเปอร์", "กระบุรี", "สุขสำราญ", "ท่าแซะ", "ปะทิว", "หลังสวน", "ละแม", "พะโต๊ะ", "ทพะโต๊ะ", "สวี", "ทุ่งตะโก", "สทิงพระ", "จะนะ", "นาทวี", "เทพา", "สะบ้าย้อย", "ระโนด", "กระแสสินธ์", "กระแสสินธุ์", "รัตภูมิ", "สะเดา", "หาดใหญ่", "นาหม่อม", "ควนเนียง", "บางกล่ำ", "สิงหนคร", "คลองหอยโข่ง", "บ้านพรุ", "ควนโดน", "กิ่งควนโดน", "ควนกาหลง", "กิ่งท่าแพ", "ท่าแพ", "ละงู", "ทุ่งหว้า", "มะนัง", "กันตัง", "นาดี", "สระแก้ว", "วังน้ำเย็น", "บ้านสร้าง", "ประจันตคาม", "ศรีมหาโพธิ", "ศรีมโหสถ", "โคกปีบ", "อรัญประเทศ", "ตาพระยา", "วัฒนานคร", "คลองหาด", "ปากพลี", "บ้านนา", "องครักษ์", "เขาฉกรรจ์", "โคกสูง", "วังสมบูรณ์", "ครบุรี", "เสิงสาง", "คง", "บ้านเหลื่อม", "ฟากท่า", "บ้านโคก", "พิชัย", "ลับแล", "ทองแสนขัน", "ร้องกวาง", "ลอง", "สูงเม่น", "เด่นชัย", "สอง", "วังชิ้น", "หนองม่วงไข่", "แม่จริม", "บ้านหลวง", "นาน้อย", "บัว", "ปัว", "ท่าวังผา", "สา", "เวียงสา", "ทุ่งช้าง", "เชียงกลาง", "นาหมื่น", "สันติสุข", "บ่อเกลือ", "สองแคว", "ภูเพียง", "ศรีบุญเรือง", "นากลาง", "สุวรรณคูหา", "โนนสัง", "บ้านผือ", "น้ำโสม", "เพ็ญ", "สร้างคอม", "หนองแสง", "นายูง", "พิบูลย์รักษ์", "กู่แก้ว", "ประจักษ์ศิลปาคม", "นาด้วง", "เชียงคาน", "ปากชม", "ด่านซ้าย", "นาแห้ว", "ภูเรือ", "ท่าลี่", "วังสะพุง", "ภูกระดึง", "ภูหลวง", "ผาขาว", "เอราวัณ", "หนองหิน", "ท่าบ่อ", "บึงกาฬ", "พรเจริญ", "โพนพิสัย", "โซ่พิสัย", "ศรีเชียงใหม่", "สังคม", "เซกา", "ปากคาด", "บึงโขงหลง", "ศรีวิไล", "บุ่งคล้า", "สระใคร", "เฝ้าไร่", "รัตนวาปี", "โพธิ์ตาก", "แกดำ", "โกสุมพิสัย", "กันทรวิชัย", "เชียงยืน", "บรบือ", "นาเชือก", "พยัคฆภูมิพิสัย", "วาปีปทุม", "นาดูน", "ยางสีสุราช", "กุดรัง", "ชื่นชม", "หลุบ", "เกษตรวิสัย", "ปทุมรัตต์", "จตุรพักตรพิมาน", "ธวัชบุรี", "พนมไพร", "โพนทอง", "โพธิ์ชัย", "หนองพอก", "เสลภูมิ", "สุวรรณภูมิ", "โพนทราย", "อาจสามารถ", "เมยวดี", "ศรีสมเด็จ", "จังหาร", "เชียงขวัญ", "หนองฮี", "ทุ่งเขาหลวง", "นามน", "กมลาไสย", "ร่องคำ", "กุฉินารายณ์", "เขาวง", "ยางตลาด", "ห้วยเม็ก", "สหัสขันธ์", "คำม่วง", "ท่าคันโท", "หนองกุงศรี", "สมเด็จ", "ห้วยผึ้ง", "สามชัย", "นาดู", "ดอนจาน", "ฆ้องชัย", "กุสุมาลย์", "กุดบาก", "พรรณานิคม", "พังโคน", "วาริชภูมิ", "นิคมน้ำอูน", "น้ำอูน", "คำตากล้า", "บ้านม่วง", "อากาศอำนวย", "พนา", "ม่วงสามสิบ", "วารินชำราบ", "อำนาจเจริญ", "เสนางคนิคม", "หัวตะพาน", "พิบูลมังสาหาร", "ตาลสุม", "โพธิ์ไทร", "สำโรง", "ดอนมดแดง", "สิรินธร", "ทุ่งศรีอุดม", "ปทุมราชวงศา", "ศรีหลักชัย", "นาเยีย", "นาตาล", "เหล่าเสือโก้ก", "สว่างวีระวงศ์", "น้ำขุ่น", "สุวรรณวารี", "ทรายมูล", "กุดชุม", "คำเขื่อนแก้ว", "ป่าติ้ว", "มหาชนะชัย", "ค้อวัง", "เลิงนกทา", "ไทยเจริญ", "บ้านเขว้า", "คอนสวรรค์", "เกษตรสมบูรณ์", "หนองบัวแดง", "จัตุรัส", "บำเหน็จณรงค์", "หนองบัวระเหว", "เทพสถิต", "ภูเขียว", "บ้านแท่น", "แก้งคร้อ", "คอนสาร", "ภักดีชุมพล", "เนินสง่า", "ซับใหญ่", "กัลยาณิวัฒนา", "บ้านหว่าเฒ่า", "วังชมภู", "ซับใ", "โคกเพชร", "นายางกลัก", "บ้านเต่า", "ท่ามะไฟหวาน", "ดนนคูณ", "ชานุมาน", "ลืออำนาจ", "นาวัง", "บ้านฝาง", "พระยืน", "หนองเรือ", "ชุมแพ", "สีชมพู", "น้ำพอง", "อุบลรัตน์", "กระนวน", "บ้านไผ่", "เปือยน้อย", "พล", "แวงใหญ่", "แวงน้อย", "หนองสองห้อง", "ภูเวียง", "มัญจาคีรี", "ชนบท", "เขาสวนกวาง", "ภูผาม่าน", "ซำสูง", "โคกโพธิ์ไชย", "หนองนาคำ", "บ้านแฮด", "โนนศิลา", "กุดจับ", "หนองวัวซอ", "กุมภวาปี", "โนนสะอาด", "หนองหาน", "ทุ่งฝน", "ไชยวาน", "ศรีธาตุ", "วังสามหมอ", "บ้านดุง", "หนองบัวลำภู", "จักราช", "โชคชัย", "ด่านขุนทด", "โนนไทย", "โนนสูง", "ขามสะแกแสง", "บัวใหญ่", "ประทาย", "ปักธงชัย", "พิมาย", "ห้วยแถลง", "ชุมพวง", "สูงเนิน", "ขามทะเลสอ", "สีคิ้ว", "ปากช่อง", "หนองบุญนาก", "แก้งสนามนาง", "โนนแดง", "วังน้ำเขียว", "เทพารักษ์", "พระทองคำ", "ลำทะเมนชัย", "บัวลาย", "สีดา", "มะค่า-พลสงคราม", "โนนลาว", "กระสัง", "นางรอง", "หนองกี่", "ละหานทราย", "ประโคนชัย", "บ้านกรวด", "พุทไธสง", "ลำปลายมาศ", "สตึก", "ปะคำ", "นาโพธิ์", "หนองหงส์", "พลับพลาชัย", "ห้วยราช", "โนนสุวรรณ", "ชำนิ", "บ้านใหม่ไชยพจน์", "โนนดินแดง", "บ้านด่าน", "แคนดง", "ชุมพลบุรี", "ท่าตูม", "จอมพระ", "ปราสาท", "กาบเชิง", "รัตนบุรี", "สนม", "ศีขรภูมิ", "สังขะ", "ลำดวน", "สำโรงทาบ", "บัวเชด", "พนมดงรัก", "ศรีณรงค์", "เขวาสินรินทร์", "โนนนารายณ์", "ยางชุมน้อย", "กันทรารมย์", "กันทรลักษ์", "ขุขันธ์", "ไพรบึง", "ปรางค์กู่", "ขุนหาญ", "ราษีไศล", "อุทุมพรพิสัย", "บึงบูรพ์", "ห้วยทับทัน", "โนนคูณ", "โนนคูน", "ศรีรัตนะ", "น้ำเกลี้ยง", "วังหิน", "ภูสิงห์", "เบญจลักษ์", "พยุห์", "โพธิ์ศรีสุวรรณ", "ศิลาลาด", "โขงเจียม", "เขื่องใน", "เขมราฐ", "เดชอุดม", "นาจะหลวย", "น้ำยืน", "บุณฑริก", "ตระการพืชผล", "กุดข้าวปุ้น", "ท่าช้าง", "อินทร์บุรี", "มโนรมย์", "วัดสิงห์", "สรรพยา", "สรรคบุรี", "หันคา", "หนองมะโมง", "เนินขาม", "แก่งคอย", "หนองแค", "วิหารแดง", "หนองแซง", "บ้านหมอ", "ดอนพุด", "หนองโดน", "พระพุทธบาท", "เสาไห้", "มวกเหล็ก", "วังม่วง", "บ้านบึง", "หนองใหญ่", "บางละมุง", "พานทอง", "พนัสนิคม", "ศรีราชา", "เกาะสีชัง", "สัตหีบ", "บ่อทอง", "เกาะจันทร์", "บางเสร่", "แหลมฉบัง", "บ้างฉาง", "บ้านฉาง", "แกลง", "วังจันทร์", "บ้านค่าย", "ปลวกแดง", "เขาชะเมา", "นิคมพัฒนา", "มาบข่า", "ขลุง", "ท่าใหม่", "โป่งน้ำร้อน", "มะขาม", "แหลมสิงห์", "สอยดาว", "แก่งหางแมว", "นายายอาม", "เขาคิชฌกูฏ", "กำพุธ", "คลองใหญ่", "เขาสมิง", "บ่อไร่", "แหลมงอบ", "เกาะกูด", "เกาะช้าง", "บางคล้า", "บางน้ำเปรี้ยว", "บางปะกง", "บ้านโพธิ์", "พนมสารคาม", "ราชสาส์น", "สนามชัย", "แปลงยาว", "ท่าตะเกียบ", "คลองเขื่อน", "กบินทร์บุรี", "กาบัง", "เวียงเก่า", "บุคคล", "ข้าราชการ", "สาธารณภัย"); // เพิ่มคำเฉพาะได้ที่นี่

				$this->arr_word_thai_cut = $arr_word_thai_cut;	//	get_word_thai_cut();
				
				rsort($this->specword);					//	เรียงจากมากไปน้อย เพื่อให้ประโยคที่ยาวกว่าอยู่ก่อน จะได้โดยการค้นหาก่อน
				rsort($this->pvword);						//	เรียงจากมากไปน้อย เพื่อให้ประโยคที่ยาวกว่าอยู่ก่อน จะได้โดยการค้นหาก่อน
				rsort($this->amword);						//	เรียงจากมากไปน้อย เพื่อให้ประโยคที่ยาวกว่าอยู่ก่อน จะได้โดยการค้นหาก่อน
				rsort($this->arr_word_thai_cut);	//	เรียงจากมากไปน้อย เพื่อให้ประโยคที่ยาวกว่าอยู่ก่อน จะได้โดยการค้นหาก่อน
//				if ($this->arr_word_thai_cut)  rsort($this->arr_word_thai_cut);	//	เรียงจากมากไปน้อย เพื่อให้ประโยคที่ยาวกว่าอยู่ก่อน จะได้โดยการค้นหาก่อน
				
//				echo "arr_word_thai_cut:".implode(",",$this->arr_word_thai_cut)."<br>";
//				echo "open font=".$this->FontFamily." size=".$this->FontSizePt."<br>";
				if (!$this->FontFamily)	$this->SetFont('thsarabun','',14);	// กำหนดค่า default
			}

			function Header()
			{
//				echo "Header font=".$this->FontFamily." size=".$this->FontSizePt."<br>";
				$font = $this->FontFamily;
				$fontstyle = $this->FontStyle;
				$fontsize = $this->FontSizePt;
				
 				$this->SetTextColor(0,0,96);
 			    $this->SetFont($font,$fontstyle,$fontsize);

				if($this->report_title){		
					$t_title = explode("||",$this->report_title);
					if(count($t_title) == 1){      
						 //     มี heading report แค่ 1 line  -->  ชื่อรายงาน
//						if($this->company_name){ 
//			 			    $this->SetFont('angsa','',10);
//							$this->Cell(20,6,$this->company_name,0,0,'L');
//						} // end if
//						$this->x = $this->lMargin;
						$sub_t_title = explode("^", $t_title[0]);
						if ($sub_t_title[1]) $align = $sub_t_title[1];
						if (!$align) $align = "C";
		 			    $this->SetFont($font,$fontstyle,$fontsize);
						$this->Cell(0,6,$sub_t_title[0],0,1,$align);
//						$this->x = $this-> w -  55;
//						$this->Cell(50,6,$this->report_code,0,1,'R');
					}else{		
						//     มี heading report  > 1 line  -->  มีชื่อกรม ตามด้วย ชื่อรายงาน และ heading อื่น ๆ
						for($i=0; $i < count($t_title); $i++ ){
							$align = "";
							$sub_t_title = explode("^", $t_title[$i]);
							if($i == (count($t_title) - 1)){
//								if($this->company_name){ 
//					 			    $this->SetFont('angsa','',10);
//									$this->Cell(20,6,$this->company_name,0,0,'L');
//								} // end if
//								$this->x = $this->lMargin;
								$sub_t_title = explode("^", $t_title[$i]);
								if ($sub_t_title[1]) $align = $sub_t_title[1];
								if (!$align) $align = "C";
				 			    $this->SetFont($font,$fontstyle,$fontsize);
								$this->Cell(0,6,$sub_t_title[0],0,1,$align);
//								$this->x = $this-> w -  55;
//								$this->Cell(50,6,$this->report_code,0,1,'R');
							}else{		
//								$this->SetFont('angsab','',16);
//								$this->SetTextColor(98, 0, 0);
								$sub_t_title = explode("^", $t_title[$i]);
								if ($sub_t_title[1]) $align = $sub_t_title[1];
								if (!$align) $align = "C";
								$this->Cell(0,6,$sub_t_title[0],0,1,$align);
							} // end if
						} // end for
					} // end if
				
//					$this->SetTextColor(108);
					$this->SetTextColor(0,0,0);
					if ($fontsize-4 < 10)
						$this->SetFont($font,'',10); // บังคับไม่ให้ มี style
					else
		 			    $this->SetFont($font,'',$fontsize-4); // บังคับไม่ให้ มี style
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
			{
				// Footer สามารถกำหนดรูปแบบ ได้ดังนี้
				// line 1 part Left text|font|fontstyle|fontsize|fontcolor|fillcolor|xpos|line height|border&line 1 part Center text|font|fontstyle|fontsize|fontcolor|fillcolor|xpos|line height|border&line 1 part Right text|font|fontstyle|fontsize|fontcolor|fillcolor|xpos|line height|border^line 2 part Left text|font|fontstyle|fontsize|fontcolor|fillcolor|xpos|line height|border^at_end_up
				if	($this->report_title   ||  $this->footer) {
					global $NUMBER_DISPLAY;

					if (trim($this->footer)) {
						$arr_footer = explode("^", trim($this->footer));
						$line_cnt = count($arr_footer)-1;
	
						if ($arr_footer[$line_cnt]) $this->SetY(-((float)$arr_footer[$line_cnt]));	// $arr_footer[$line_cnt] ค่าตัวสุดท้าย คือ ค่า at end up
						else $this->SetY(-$at_end_up);
	
						for($i=0; $i < $line_cnt; $i++) {
//							echo "arr_footer [$i]=".$arr_footer[$i]."  ";
							if ($arr_footer[$i]) {
								if (strtolower($arr_footer[$i])=="line")
									$this->line($this->lMargin,$this->y,$this->w - $this->rMargin ,$this->y);
								else {
									$arr_footer_col = explode("&",$arr_footer[$i]);
									$col_cnt = count($arr_footer_col);
									for($j=0; $j < $col_cnt; $j++) {
										if (strtolower($arr_footer_col[$j])=="standard") {
//											echo "standard-$j<br>";
											$font = $this->FontFamily;
											$fontstyle = $this->FontStyle;
											$fontsize = $this->FontSizePt - 4;	// 	// บังคับ ขนาด ให้ เล็กกว่าที่กำหนดหลักไว้ -2
											if ($fontsize < 10) $fontsize = 10;		// กำหนดให้ขนาดเล็กสุดอยู่ที่ 10 แต่เดิม -4 จะทำให้ค่า font size อยู่ที่ 8 มันเล็กเกินไป 
						
											$fontstyle = "";	// บังคับไม่ให้ มี style
					
											if ($j==0) 
												$this->Cell(100,6,($this->report_code?("รายงาน : ".(($NUMBER_DISPLAY==2)?convert2thaidigit($this->report_code):$this->report_code)):""),0,0,"L");
											else if ($j==1) {
												$page = "{nb}";
												$page = (($NUMBER_DISPLAY==2)?convert2thaidigit($page):$page);
//												echo "1..page=$page<br>";
												$this->x = ($this->w / 2) - 10;
												$this->Cell(10,6,"หน้าที่ : " . (($NUMBER_DISPLAY==2)?convert2thaidigit($this->PageNo()):$this->PageNo())." / $page",0,0,'C');
											} else {
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
												$this->x = $this->w - 30;
												$this->Cell(25,6,"วันที่พิมพ์ : ". $mday . " " . $month . " " . $year . " " . $time,0,0,"R");
											}
										} else {	// if not standard
											if ($arr_footer_col[$j]) {
//												echo "$j-".$arr_footer_col[$j]."<br>";
												$font = $this->FontFamily;
												$fontstyle = $this->FontStyle;
												$fontsize = $this->FontSizePt - 4;	// 	// บังคับ ขนาด ให้ เล็กกว่าที่กำหนดหลักไว้ -2
												if ($fontsize < 10) $fontsize = 10;		// กำหนดให้ขนาดเล็กสุดอยู่ที่ 10 แต่เดิม -4 จะทำให้ค่า font size อยู่ที่ 8 มันเล็กเกินไป 
							
												$fontstyle = "";	// บังคับไม่ให้ มี style
						
												$arr_footer_det = explode("|",$arr_footer_col[$j]);
												if ($arr_footer_det[1]) $font = $arr_footer_det[1];	// ถ้ากำหนด ชื่อ font
												if ($arr_footer_det[2]) $fontstyle = $arr_footer_det[2];	// ถ้ากำหนด font style
												if ($arr_footer_det[3]) $fontsize = $arr_footer_det[3];	// ถ้ากำหนด font size
												
												$this->SetFont($font, $fontstyle, $fontsize);
				//								echo "$i--font::$font,$fontstyle,$fontsize--";
												if (strlen($arr_footer_det[4])==6) {
													$r=substr($arr_footer_det[4],0,2);
													$g=substr($arr_footer_det[4],2,2);
													$b=substr($arr_footer_det[4],4,2);
												} else { $r="00"; $g="00"; $b="00"; }
					//							echo "font:$r $g $b<br>";
												$this->SetTextColor(hexdec("$r"),hexdec("$g"),hexdec("$b"));
									
												if (strlen($arr_footer_det[5])==6) {
													$r=substr($arr_footer_det[5],0,2);
													$g=substr($arr_footer_det[5],2,2);
													$b=substr($arr_footer_det[5],4,2);
													$this->SetFillColor(hexdec("$r"),hexdec("$g"),hexdec("$b"));
	//												echo "fill:$r $g $b<br>";
												}
	//											echo "$i--".$arr_footer[$i]."<br>";
												$col_w = ($arr_footer_det[6] ? (float)$arr_footer_det[6] : ($this->w-$this->lMargin-$this->rMargin));
												$line_h = ($arr_footer_det[7] ? (float)$arr_footer_det[7] : 7);
												$border = $arr_footer_det[8];
												$align = ($j==0 ? "L" : ($j==1 ? "C" : "R"));
	//											echo "$i>>x:".$this->x.",  y:".$this->y.",  w:$col_w, line_h:$line_h, align:$align, font:$font,$fontstyle,$fontsize<br>";
												$this->Cell($col_w,$line_h,($NUMBER_DISPLAY==2 ? convert2thaidigit($arr_footer_det[0]) : $arr_footer_det[0]),$border,0,$align);
											} // end if $arr_footer_col[$j]
										}
									} // end for loop $j (column loop

									$this->x = $this->lMargin;
									$this->y += $line_h;
								} // end if $line
							} else {  // if not $arr_footer
								$font = $this->FontFamily;
								$fontstyle = $this->FontStyle;
								$fontsize = $this->FontSizePt - 4;	// 	// บังคับ ขนาด ให้ เล็กกว่าที่กำหนดหลักไว้ -2
								if ($fontsize < 10) $fontsize = 10;		// กำหนดให้ขนาดเล็กสุดอยู่ที่ 10 แต่เดิม -4 จะทำให้ค่า font size อยู่ที่ 8 มันเล็กเกินไป 
			
								$fontstyle = "";	// บังคับไม่ให้ มี style
					
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
//								$this->SetTextColor(0,0,0);
//								$this->SetFont($font,'',$fontsize-4); // บังคับไม่ให้ มี style
//								echo "2..page=$page<br>";
								$this->line($this->lMargin,$this->y,$this->w - $this->rMargin ,$this->y);
								$this->Cell(100,6,($this->report_code?("รายงาน : ".(($NUMBER_DISPLAY==2)?convert2thaidigit($this->report_code):$this->report_code)):""),0,0,"L");		
								$this->x = ($this->w / 2) - 10;
								$this->Cell(10,6,"หน้าที่ : " . (($NUMBER_DISPLAY==2)?convert2thaidigit($this->PageNo()):$this->PageNo())." / $page",0,0,'C');
								$this->x = $this->w - 30;
								$this->Cell(25,6,"วันที่พิมพ์ : ". $mday . " " . $month . " " . $year . " " . $time,0,0,"R");
								
								$this->x = $this->lMargin;
								$this->y += 6;
							} // end if ($arr_footer)
						} // end for loop $i ($line)
					} else {	// if not footer but report_title
						$font = $this->FontFamily;
						$fontstyle = $this->FontStyle;
						$fontsize = $this->FontSizePt - 4;	// 	// บังคับ ขนาด ให้ เล็กกว่าที่กำหนดหลักไว้ -2
						if ($fontsize < 10) $fontsize = 10;		// กำหนดให้ขนาดเล็กสุดอยู่ที่ 10 แต่เดิม -4 จะทำให้ค่า font size อยู่ที่ 8 มันเล็กเกินไป 
	
						$fontstyle = "";	// บังคับไม่ให้ มี style
					
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
//						$page = (($NUMBER_DISPLAY==2)?convert2thaidigit($page):$page);
//						echo "3..page=$page<br>";
						//Position at 1.0 cm from bottom
						$this->SetY(-$at_end_up);
						$this->SetTextColor(0,0,0);
						if ($fontsize-4 < 10)
							$this->SetFont($font,'',10); // บังคับไม่ให้ มี style
						else
							$this->SetFont($font,'',$fontsize-4); // บังคับไม่ให้ มี style
						$this->line($this->lMargin,$this->y,$this->w - $this->rMargin ,$this->y);
						$this->Cell(100,6,($this->report_code?("รายงาน : ".(($NUMBER_DISPLAY==2)?convert2thaidigit($this->report_code):$this->report_code)):""),0,0,"L");		
						$this->x = ($this->w / 2) - 10;
						$this->Cell(10,6,"หน้าที่ : " . (($NUMBER_DISPLAY==2)?convert2thaidigit($this->PageNo()):$this->PageNo())." / $page",0,0,'C');
						$this->x = $this->w - 30;
						$this->Cell(25,6,"วันที่พิมพ์ : ". $mday . " " . $month . " " . $year . " " . $time,0,0,"R");
					} // end if ($this->footer)
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
//				echo "***$s<br>";
				$s = $this->thaiCutLinePDF($s, $w, "\n");
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
			function thaiWrapPDF($dataIn, $delim, $w){ // $w เป็นความกว้างขอบเขตของ กรอบแสดงประโยค
				$cw=&$this->CurrentFont['cw'];
			
				if ($this->GetStringWidth($dataIn) <= $w) {
					$max_pos_i = strlen($dataIn);
				} else {
					$max_pos_i = 0;
					$pos_i = 0;
					$i_ord = 0;
					$ret_len = 0;
					$ch = "";
					$f_match = false;

					$w = $w-$this->GetStringWidth("W");	// ลดความกว้างลงเผื่อไว้ซัก 1 ตัวอักษร W
	
					$max_over_bound_len = 0;
					$select_over_bound_pos = -1;
					
					$arr_match = (array) null;
					$arr_mypatt = (array) null;
					
					$arr_mypatt[] = implode("|", $this->specword);	// คำพิเศษ
					$arr_mypatt[] = implode("|", $this->pvword);		// คำชื่อจังหวัด
					$arr_mypatt[] = implode("|", $this->amword);		// คำชื่ออำเภอ
					foreach ($this->arr_word_thai_cut as $key => $value) {
//						echo "key:$key-->value:$value<br>";
						$arr_mypatt[] = $value;
					}
					$arr_mypatt[] = "เ([ก-ฮ])(ร|ม|พ)?าะ(ห์)?";		// เคราะห์ เหมาะ เจาะ เกาะ เอาะ เลาะห์ เฉพาะ
					$arr_mypatt[] = "มา(?:ก|ย|ตรการ|ตรา|ตุภูมิ|ชิก)";		// มาตุภูมิ มาตรา มาตราการ
					$arr_mypatt[] = "เ([ก-ฮ])ี([่|้|๊|๋])?ยง";	 // เกียง เอี้ยง เมี่ยง เสี่ยง 
					$arr_mypatt[] = "เ([ก-ฮ])ื([่|้|๊|๋])?อ([ง|น])";		// เมือง เนื่อง เอื้อง เรือง เลื่อน เดือน
					$arr_mypatt[] = "(เ|แ)([ก-ฮ])([งลรว])?([่|้|๊|๋|็|ิ|ี|ึ|ื])?([ง|ว|อ|น])";	 // เกลือ แกง แฮง แรง แกล้ง แผลง แดง แวง แตง แหว่ง แกร่ง แฝง แสลง แกร๊ง แกล้ว แผน แสน
					$arr_mypatt[] = "เ([ก-ฮ])([่|้|๊|๋])า"; // เข้า เจ้า เจ่า เช้า เก่า เม่า เล่า
					$arr_mypatt[] = "[ก-ฮ]([่|้|๊|๋])?อ[ง|น|ม|ย|ร|ล]";  // กอง ตอน สอน มอม กอม ช้อน ค้อน ร้อน ซ้อน ซ่อน ค่อน ป้อน 
					$arr_mypatt[] = "[ก-ฮ]([่|้|๊|๋])?า[ก|บ|ด|ง|น|ม|ย|ร|ล]";  // การ กาง สาน พาล ช้าง ค่าง ล้าง ย้าย ซ้าย
					$arr_mypatt[] = "[ก-ฮ]([่|้|๊|๋])?ว[ง|น|ม|ย]";  // สวย สวน ล้วน ม่วน ป่วน ยวน
					$arr_mypatt[] = "[ก-ฮ][ย|ร|ล|ม|น]?([ุ|ู])([่|้|๊|๋])?";  // อยู่ รู้ หู หรู ครู ดู สู้ พลู
					$arr_mypatt[] = "[ก-ฮ][ุ|ู]([่|้|๊|๋])([ก-อ])?";  // กู ขูด คู ถุง
					$arr_mypatt[] = "[ก-ฮ](?:ี|ัน|ือ)"; // มือ มัน มือ
					$arr_mypatt[] = "[ก-ฮ]ื([่|้|๊|๋])?อ"; // ชื่อ ซื้อ คือ สื่อ ดื้อ มื้อ หือ อื้อ
					$arr_mypatt[] = "[ก-ฮ]ั([่|้|๊|๋])?([ง|ก|บ|น|ด|ย|ร|ล])"; // ตั้ง ตั่ง กั๊ง กั๊ก สัก มัก มั้ง มั๊ย ขัย ปั๊บ รับ นั้น มัน จัด ขัด นัด
					$arr_mypatt[] = "โ[ก-ฮ]([่|้|๊|๋])?([ง|ก|บ|น|ด])";	 // โกง โด่ง โดด โฉด โชค โมก โก่ง โข่ง โค้ง โจ๊ง โด๊บ
					$arr_mypatt[] = "[ก-ฮ]([ล|ร|ว])(า)([ง|ก|บ|น|ด|ร|ล|ย|ว])";	 // กลาง ปล่าว กลาย สลาย มลาย ทวาย พราง กราบ ทราบ
					$arr_mypatt[] = "[ไ|ใ][ก-ฮ]([่|้|๊|๋])?([ง|ก|บ|น|ด])";	 // ได้,ไง,ใด..

					foreach ($arr_mypatt as $key => $mypatt) {
						$stpos=0;
//						echo "mypatt=$mypatt<br>";
						while($stpos >= 0) {
							if (preg_match("/($mypatt)/",$dataIn,$match,PREG_OFFSET_CAPTURE,$stpos)) {
								$f_match = true;
								$pos_i = $match[0][1];	// match position
								$len = strlen($match[0][0]);	// word match
//								echo "......match [".$match[0][0]."] pos_i=$pos_i , len=$len<br>";
								$stpos = $pos_i + $len + 1;		// ตำแหน่ง ค้นหาถัดไป
								$match_pos_w = $this->GetStringWidth(substr($dataIn,0,$pos_i));
								$match_len_w = $this->GetStringWidth(substr($dataIn,0,$pos_i+$len));
								if ($match_pos_w < $w && $w < $match_len_w) {	// ถ้าคำที่เจอคาบเส้นขอบกว้าง
									if ($max_over_bound_len < $len) {
										$max_over_bound_len = $len;
										$select_over_bound_pos = $pos_i;
										$msg = "over max-".$match[0][0]."($pos_i,$len)";
									} else $msg = "over-".$match[0][0]."($pos_i,$len)";
								} else if ($match_len_w <= $w) {	// ถ้าคำที่เจอน้อยกว่าเท่ากับเส้่นขอบกว้าง
									$pos = "";
									if (($pos_i+$len) > $max_pos_i) {
										$max_pos_i = $pos_i+$len;
										$ret_len = $len;
										$pos = $max_pos_i;
										$msg = "max-".$match[0][0]."($pos_i,$len)";
									} else $msg = "";
								}
								if ($msg)	$arr_match[] = "$key-".$msg;
							} else {
								$stpos = -1;
							} // end if (preg_match
						}  // end while loop
					}	// end foreach ($arr_mypatt as $mypatt)

					if ($select_over_bound_pos >= 0) {	// ถ้ามีคำที่คาบเส้นขอบกว้าง จะตัดที่ต้นคำนี้เลย
						$max_pos_i = $select_over_bound_pos;
					} else {
						if (preg_match("/[0-9]/",$dataIn,$match)) { // เป็นตัวเลข
							$f_match = true;
							$pos_i = strpos($dataIn, $match[0]);
							$len = 0;
							for($i=$pos_i; $i < strlen($dataIn); $i++) {
								$ch = substr($dataIn,$i,1);
								if (($ch >= "0" && $ch <= "9") || $ch == "." || $ch == ",") {	// เป็นชุดตัวเลข
									$len++;
								} else break;
							}
							if ($len > 0) {
								$num_match = substr($dataIn,0,$pos_i+$len);
								$match_w = $this->GetStringWidth($num_match);
								$pos = "";
								if (($pos_i+$len) > $max_pos_i && $match_w < $w) {
									$max_pos_i = $pos_i+$len;
									$ret_len = $len;
									$pos = $max_pos_i;
								}
								$arr_match[] = "".($key+1)."-".$num_match."(match_w=$match_w)(pos=$pos)";
							}
						}

						$spec_ch = array("{", "(", "[","*");	// สัญญาลักษณ์ ตัด ไปประโยคหลัง
						$max_spec_ch = -1;
						for($jj = 0; $jj < count($spec_ch); $jj++) {
							$spec_ch_i = strpos($dataIn, $spec_ch[$jj]);
							if ($spec_ch_i !== false) {
								$match_w = $this->GetStringWidth(substr($dataIn,0,$spec_ch_i));
//								echo "****... (sub-22 $jj spec_ch_i=$spec_ch_i, spec_ch[$jj]=".$spec_ch[$jj]." [".substr($dataIn,0,$spec_ch_i)."($match_w)]) ...****";
								if ($spec_ch_i > $max_spec_ch && $match_w < $w) {
									$max_spec_ch = $spec_ch_i;
								}
							}
						}
						if ($max_spec_ch >= 0) {
							$pos = "";
							if ($max_spec_ch > $max_pos_i) {
								$max_pos_i=$max_spec_ch;
								$ret_len = $max_spec_ch;
								$pos = $max_pos_i;
							}
							$match_w = $this->GetStringWidth(substr($dataIn,0,$max_pos_i));
							$arr_match[] = "".($key+2)."-".substr($dataIn,0,$max_pos_i)."(match_w=$match_w)(pos=$pos)";
						}

						$spec_ch = array(" ", ".", ",", ")", "/", "\\","@","$","%","&","#","!","<",">","]");	// ตัดให้ติดไปกับประโยคหน้า
						$max_spec_ch = -1;
						for($jj = 0; $jj < count($spec_ch); $jj++) {
							$spec_ch_i = strpos($dataIn, $spec_ch[$jj]);
							if ($spec_ch_i !== false) {
								$match_w = $this->GetStringWidth(substr($dataIn,0,$spec_ch_i+1));
								if ($spec_ch_i+1 > $max_spec_ch && $match_w < $w) {
									$max_spec_ch = $spec_ch_i+1;
								}
							}
						}
						if ($max_spec_ch >= 0) {
							$pos = "";
							if ($max_spec_ch > $max_pos_i) {
								$max_pos_i=$max_spec_ch;
								$ret_len = $max_spec_ch;
								$pos = $max_pos_i;
							}
							$match_w = $this->GetStringWidth(substr($dataIn,0,$max_pos_i));
							$arr_match[] = "".($key+3)."-".substr($dataIn,0,$max_pos_i)."(match_w=$match_w)(pos=$pos)";
						}
					}
//					echo "match.(dataIn=$dataIn, w=$w)..[";
//					echo "".implode("|",$arr_match)."";
//					echo "] (max_pos_i=$max_pos_i)<br>";
			
	//				if ($max_pos_i==0 && !$f_match) {
	/*
					if ($max_pos_i==0) {
						$max_i = 0;
						for($i=0; $i < strlen($dataIn); $i++) {
							$match_w = $this->GetStringWidth(substr($dataIn,0,$i+1));
							if ($match_w > $w) { $max_i = $i; break; }
						}
	//					echo "[$dataIn($max_i)] ไม่เข้าเงื่อนไขใด ๆ<br>";
						if ($max_i) {
							$max_pos_i = $max_i;
							$ret_len = $max_i;
						} else {
							$max_pos_i = 5;
							$ret_len = 5;
						}
					}
	*/				
	//				$ch = substr($dataIn,$max_pos_i+$ret_len,1);
					$ch = substr($dataIn,$max_pos_i,1);
					if (strpos("ะาิีึืุูำ่้๊๋ั็",$ch) !== false) { // ถ้าถัดไป เป็นตัวที่ต้องมีอักษรนำ ให้บวกไปด้วย
						$ret_len++;
						$max_pos_i++;
					}
				} // end if first check
				
//				return $max_pos_i+$ret_len;
				return $max_pos_i;
			} // end function

			function thaiWrapPDF_old($dataIn, $delim){
				$specword = array("ราชการ", "รัฐบาล", "อธิบดี", "คณะบดี", "ตำแหน่ง", "อำเภอ","จังหวัด","ปกครอง","ปฏิบัติ","สัญญา","เจ้าหน้าที่", "และ", "เกียรติยศ","บรรจุ", "ดำรง", "บำเหน็ด", "โรงเรียน", "มหาบัณฑิต", "บัณฑิต", "บริหาร", "ชำนาญ", "ศาสน", "ศาสตร์", "พัฒนา", "ภาพ", "วันที่", "เลขที่", "ประเภท", "นโยบาย", "นักวิเคราะห์", "กำหนด", "วัน", "เดือน", "ปี", "เข้า", "สู่", "อายุ", "สาธารณะ", "ระบบ", "วัฒนา", "บรรจง", "สกุล", "สภา", "สถานะ", "สถาพร", "สถาปนา", "สถาปนิก", "สถาบัน", "จักร", "อาทิตย์", "จันทร์", "อังคาร", "พุธ", "พฤหัสบดี", "ศุกร์", "เสาร์", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม","เหนือ","ใต้","ตะวันออก","ตะวันตก","เฉียง","อีสาน","ตำรวจ","ทหาร","พยาบาล","อากาศ","วิหาร","ตำนาน","นครบาล","ภูธร","แพทย์","สูติ","ปริญญา","สรรหา","เวลา","สาขา","ประถม","มัธยม","ปริญญา","ศึกษา","กรุง"); // เพิ่มคำเฉพาะได้ที่นี่
				$pvword = array("กรุงเทพมหานคร","กรุงเทพฯ","กรุงเทพ","กทม.","กทม","พระนคร","กระบี่", "กาญจนบุรี", "กาฬสินธุ์", "กำแพงเพชร", "ขอนแก่น", "จันทบุรี", "ฉะเชิงเทรา", "ชลบุรี", "ชัยนาท", "ชัยภูมิ", "ชุมพร", "เชียงราย", "เชียงใหม่", "ตรัง", "ตราด", "ตาก", "นครนายก", "นครปฐม", "นครพนม", "นครราชสีมา", "นครศรีธรรมราช", "นครสวรรค์", "นนทบุรี", "นราธิวาส", "น่าน", "บุรีรัมย์", "ปทุมธานี", "ประจวบคีรีขันธ์", "ปราจีนบุรี", "ปัตตานี", "พระนครศรีอยุธยา", "พะเยา", "พังงา", "พัทลุง", "พิจิตร", "พิษณุโลก", "เพชรบุรี", "เพชรบูรณ์", "แพร่", "ภูเก็ต", "มหาสารคาม", "มุกดาหาร", "แม่ฮ่องสอน", "ยโสธร", "ยะลา", "ร้อยเอ็ด", "ระนอง", "ระยอง", "ราชบุรี", "ลพบุรี", "ลำปาง", "ลำพูน", "เลย", "ศรีสะเกษ", "สกลนคร", "สงขลา", "สตูล", "สมุทรปราการ", "สมุทรสงคราม", "สมุทรสาคร", "สระแก้ว", "สระบุรี", "สิงห์บุรี", "สุโขทัย", "สุพรรณบุรี", "สุราษฎร์ธานี", "สุรินทร์", "หนองคาย", "หนองบัวลำภู", "อ่างทอง", "อำนาจเจริญ", "อุดรธานี", "อุตรดิตถ์", "อุทัยธานี", "อุบลราชธานี");
				$amword = array("มายอ", "ทุ่งยางแดง", "สายบุรี", "ดงเจริญ", "วชิรบารมี", "ชนแดน", "หล่มสัก", "หล่มเก่า", "วิเชียรบุรี", "ศรีเทพ", "หนองไผ่", "บึงสามพัน", "น้ำหนาว", "วังโป่ง", "เขาค้อ", "จอมบึง", "สวนผึ้ง", "ดำเนินสะดวก", "บ้านโป่ง", "บางแพ", "โพธาราม", "ปากท่อ", "วัดเพลง", "บ้านคา", "ไทรโยค", "บ่อพลอย", "ศรีสวัสดิ์", "ท่ามะกา", "ท่าม่วง", "ทองผาภูมิ", "สังขละบุรี", "พนมทวน", "เลาขวัญ", "ด่านมะขามเตี้ย", "หนองปรือ", "ห้วยกระเจา", "ท่ากระดาน", "บ้านทวน", "เดิมบางนางบวช", "ด่านช้าง", "บางปลาม้า", "ศรีประจันต์", "ดอนเจดีย์", "สองพี่น้อง", "สามชุก", "อู่ทอง", "หนองหญ้าไซ", "กำแพงแสน", "นครชัยศรี", "ดอนตูม", "บางเลน", "สามพราน", "พุทธมณฑล", "กระทุ่มแบน", "บ้านแพ้ว", "บางคนที", "อัมพวา", "เขาย้อย", "หนองหญ้าปล้อง", "ชะอำ", "ท่ายาง", "บ้านลาด", "บ้านแหลม", "แก่งกระจาน", "กุยบุรี", "ทับสะแก", "บางสะพาน", "บางสะพานน้อย", "ปราณบุรี", "หัวหิน", "สามร้อยยอด", "พรหมคีรี", "ลานสกา", "ลานสะกา", "ฉวาง", "พิปูน", "เชียรใหญ่", "ชะอวด", "ท่าศาลา", "ทุ่งสง", "นาบอน", "ทุ่งใหญ่", "ปากพนัง", "ร่อนพิบูลย์", "สิชล", "ขนอม", "หัวไทร", "บางขัน", "ถ้ำพรรณรา", "จุฬาภรณ์", "พระพรหม", "นบพิต้า", "ช้างกลาง", "เฉลิมพระเกียรติ", "เสือหึง", "สวนหลวง", "หินตก", "ดวนชะลิก", "กะปาง", "เขาพนม", "เกาะลันตา", "คลองท่อม", "อ่าวลึก", "จุน", "เชียงคำ", "เชียงม่วน", "ดอกคำใต้", "ปง", "แม่ใจ", "ภูซาง", "ภูกามยาว", "เวียงชัย", "ย่านตาขาว", "ปะเหลียน", "สิเกา", "ห้วยยอด", "วังวิเศษ", "นาโยง", "รัษฎา", "หาดสำราญ", "กงหรา", "เขาชัยสน", "ตะโหมด", "ควนขนุน", "ปากพะยูน", "ศรีบรรพต", "กิ่งศลีบรรพต", "ป่าบอน", "บางแก้ว", "ป่าพะยอม", "ศรีนครินทร์", "โคกโพธิ์", "หนองจิก", "ปะนาเระ", "เชียงของ", "เทิง", "พาน", "ป่าแดด", "แม่จัน", "เชียงแสน", "แม่สาย", "แม่สรวย", "เวียงป่าเป้า", "พญาเม็งราย", "เวียงแก่น", "ขุนตาล", "แม่ฟ้าหลวง", "แม่ลาว", "เวียงเชียงรุ้ง", "ดอยหลวง", "ขุนยวม", "ปาย", "แม่สะเรียง", "แม่ลาน้อย", "สบเมย", "ปางมะผ้า", "ม่วยต่อ", "โกรกพระ", "ชุมแสง", "หนองบัว ", "บรรพตพิสัย", "เก้าเลี้ยว", "ตาคลี", "ท่าตะโก", "ไพศาลี", "พยุหะคีรี", "ลาดยาว", "ตากฟ้า", "แม่วงก์", "แม่เปิน", "ชุมตาบง", "ห้วยน้ำหอม", "ชุมต", "แม่เล่ย์", "ทัพทัน", "สว่างอารมณ์", "หนองฉาง", "หนองขาหย่าง", "บ้านไร่", "ลานสัก", "ห้วยคด", "ไทรงาม", "คลองลาน", "ขาณุวรลักษบุรี", "คลองขลุง", "พรานกระต่าย", "ลานกระบือ", "ทรายทองวัฒนา", "ปางศิลาทอง", "บึงสามัคคี", "โกสัมพีนคร", "บ้านตาก", "สามเงา", "แม่ระมาด", "ท่าสองยาง", "แม่สอด", "พบพระ", "อุ้มผาง", "วังเจ้า", "ท่าปุย", "บ้านด่านลานหอย", "คีรีมาศ", "กงไกรลาศ", "ศรีสัชนาลัย", "ศรีสำโรง", "สวรรคโลก", "ศรีนคร", "ทุ่งเสลี่ยม", "นครไทย", "ชาติตระการ", "บางระกำ", "บางกระทุ่ม", "พรหมพิราม", "วัดโบสถ์", "วังทอง", "เนินมะปราง", "วังทรายพูน", "โพธิ์ประทับช้าง", "ตะพานหิน", "บางมูลนาก", "โพทะเล", "สามง่าม", "ทับคล้อ", "สากเหล็ก", "บึงนาราง", "สว่างแดนดิน", "ส่องดาว", "เต่างอย", "โคกศรีสุพรรณ", "เจริญศิลป์", "โพนนาแก้ว", "ภูพาน", "วานรนิวาส", "กุดเรือคำ", "บ้านหัน", "ปลาปาก", "ท่าอุเทน", "บ้านแพง", "ธาตุพนม", "นาแก", "เรณูนคร", "ศรีสงคราม", "นาหว้า", "โพนสวรรค์", "นาทม", "วังยาง", "นิคมคำสร้อย", "ดอนตาล", "ดงหลวง", "คำชะอี", "หว้านใหญ่", "หนองสูง", "จอมทอง", "แม่แจ่ม", "เชียงดาว", "ดอยสะเก็ด", "แม่แตง", "แม่ริม", "สะเมิง", "ฝาง", "แม่อาย", "พร้าว", "สันป่าตอง", "สันกำแพง", "สันทราย", "หางดง", "ฮอด", "ดอยเต่า", "อมก๋อย", "สารภี", "เวียงแหง", "เวียงแห", "ไชยปราการ", "แม่วาง", "แม่ออน", "ดอยหล่อ", "นครเชียงใหม่", "แขวงกา", "แขวงศร", "เม็งรา", "แม่ทา", "บ้านโฮ่ง", "ลี้", "ทุ่งหัวช้าง", "ป่าซาง", "บ้านธิ", "เวียงหนองล่อง", "แม่เมาะ", "เกาะคา", "เสริมงาม", "งาว", "แจ้ห่ม", "วังเหนือ", "เถิน", "แม่พริก", "แม่ทะ", "สบปราบ", "ห้างฉัตร", "ตรอน", "ท่าปลา", "น้ำปาด", "ไม้แก่น", "ไทรทอง", "ยะหริ่ง", "ยะรัง", "กะพ้อ", "แม่ลาน", "เบตง", "บันนังสตา", "ธารโต", "กิ่งธารโต", "ยะหา", "รามัน", "กรงปินัง", "ตากใบ", "บาเจาะ", "ยี่งอ", "ระแงะ", "รือเสาะ", "ศรีสาคร", "แว้ง", "สุคิริน", "สุไหงโก-ลก", "สุไหงปาดี", "จะแนะ", "เจาะไอร้อง", "บางนรา", "พระนคร", "ดุสิต", "หนองจอก", "บางรัก", "บางเขน", "บางกะปิ", "ปทุมวัน", "ป้อมปราบศัตรูพ่าย", "พระโขนง", "มีนบุรี", "ลาดกระบัง", "ยานนาวา", "สัมพันธวงศ์", "พญาไท", "ธนบุรี", "บางกอกใหญ่", "ห้วยขวาง", "คลองสาน", "ตลิ่งชัน", "บางกอกน้อย", "บางขุนเทียน", "ภาษีเจริญ", "หนองแขม", "ราษฎร์บูรณะ", "บางพลัด", "ดินแดง", "บึงกุ่ม", "สาธร", "บางซื่อ", "จตุจักร", "บางคอแหลม", "ประเวศ", "คลองเตย", "ราชเทวี", "ลาดพร้าว", "วัฒนา", "บางแค", "หลักสี่", "สายไหม", "คันนายาว", "สะพานสูง", "วังทองหลาง", "คลองสามวา", "บางนา", "ทวีวัฒนา", "ทุ่งครุ", "บางบอน", "บ้านทะวาย", "บางบ่อ", "บางพลี", "พระประแดง", "พระสมุทรเจดีย์", "บางเสาธง", "บางกรวย", "บางใหญ่", "บางบัวทอง", "ไทรน้อย", "ปากเกร็ด", "นครนนทบุรี", "แขวงท่าท", "คลองหลวง", "ธัญบุรี", "หนองเสือ", "ลาดหลุมแก้ว", "ลำลูกกา", "สามโคก", "คูคต", "ท่าเรือ", "นครหลวง", "บางไทร", "บางบาล", "บางปะอิน", "บางปะหัน", "ปางปะหัน", "ผักไห่", "ภาชี", "ลาดบัวหลวง", "วังน้อย", "เสนา", "บางซ้าย", "อุทัย", "มหาราช", "บ้านแพรก", "ไชโย", "ป่าโมก", "โพธิ์ทอง", "แสวงหา", "วิเศษชัยชาญ", "สามโก้", "พัฒนานิคม", "โคกสำโรง", "ชัยบาดาล", "ท่าวุ้ง", "บ้านหมี่", "ท่าหลวง", "สระโบสถ์", "โคกเจริญ", "ลำสนธิ", "หนองม่วง", "บ้านเช่า", "บางระจัน", "ค่ายบางระจัน", "พรหมบุรี", "ปลายพระยา", "ลำพัด", "ลำทับ", "เหนือคลอง", "เกาะยาว", "กะปง", "ตะกั่วทุ่ง", "ตะกั่วป่า", "คุระบุรี", "ทับปุด", "ท้ายเหมือง", "กะทู้", "ถลาง", "ทุ่งคา", "กาญจนดิษฐ์", "ดอนสัก", "เกาะสมุย", "เกาะพะงัน", "ไชยา", "ท่าชนะ", "คีรีรัฐนิคม", "บ้านตาขุน", "พนม", "ท่าฉาง", "บ้านนาสาร", "บ้านนาเดิม", "เคียนซา", "เวียงสระ", "พระแสง", "พุนพิน", "ชัยบุรี", "วิภาวดี", "เกาะพงัน", "เกาะเต่า", "บ้านดอน", "ละอุ่น", "กะเปอร์", "กระบุรี", "สุขสำราญ", "ท่าแซะ", "ปะทิว", "หลังสวน", "ละแม", "พะโต๊ะ", "ทพะโต๊ะ", "สวี", "ทุ่งตะโก", "สทิงพระ", "จะนะ", "นาทวี", "เทพา", "สะบ้าย้อย", "ระโนด", "กระแสสินธ์", "กระแสสินธุ์", "รัตภูมิ", "สะเดา", "หาดใหญ่", "นาหม่อม", "ควนเนียง", "บางกล่ำ", "สิงหนคร", "คลองหอยโข่ง", "บ้านพรุ", "ควนโดน", "กิ่งควนโดน", "ควนกาหลง", "กิ่งท่าแพ", "ท่าแพ", "ละงู", "ทุ่งหว้า", "มะนัง", "กันตัง", "นาดี", "สระแก้ว", "วังน้ำเย็น", "บ้านสร้าง", "ประจันตคาม", "ศรีมหาโพธิ", "ศรีมโหสถ", "โคกปีบ", "อรัญประเทศ", "ตาพระยา", "วัฒนานคร", "คลองหาด", "ปากพลี", "บ้านนา", "องครักษ์", "เขาฉกรรจ์", "โคกสูง", "วังสมบูรณ์", "ครบุรี", "เสิงสาง", "คง", "บ้านเหลื่อม", "ฟากท่า", "บ้านโคก", "พิชัย", "ลับแล", "ทองแสนขัน", "ร้องกวาง", "ลอง", "สูงเม่น", "เด่นชัย", "สอง", "วังชิ้น", "หนองม่วงไข่", "แม่จริม", "บ้านหลวง", "นาน้อย", "บัว", "ปัว", "ท่าวังผา", "สา", "เวียงสา", "ทุ่งช้าง", "เชียงกลาง", "นาหมื่น", "สันติสุข", "บ่อเกลือ", "สองแคว", "ภูเพียง", "ศรีบุญเรือง", "นากลาง", "สุวรรณคูหา", "โนนสัง", "บ้านผือ", "น้ำโสม", "เพ็ญ", "สร้างคอม", "หนองแสง", "นายูง", "พิบูลย์รักษ์", "กู่แก้ว", "ประจักษ์ศิลปาคม", "นาด้วง", "เชียงคาน", "ปากชม", "ด่านซ้าย", "นาแห้ว", "ภูเรือ", "ท่าลี่", "วังสะพุง", "ภูกระดึง", "ภูหลวง", "ผาขาว", "เอราวัณ", "หนองหิน", "ท่าบ่อ", "บึงกาฬ", "พรเจริญ", "โพนพิสัย", "โซ่พิสัย", "ศรีเชียงใหม่", "สังคม", "เซกา", "ปากคาด", "บึงโขงหลง", "ศรีวิไล", "บุ่งคล้า", "สระใคร", "เฝ้าไร่", "รัตนวาปี", "โพธิ์ตาก", "แกดำ", "โกสุมพิสัย", "กันทรวิชัย", "เชียงยืน", "บรบือ", "นาเชือก", "พยัคฆภูมิพิสัย", "วาปีปทุม", "นาดูน", "ยางสีสุราช", "กุดรัง", "ชื่นชม", "หลุบ", "เกษตรวิสัย", "ปทุมรัตต์", "จตุรพักตรพิมาน", "ธวัชบุรี", "พนมไพร", "โพนทอง", "โพธิ์ชัย", "หนองพอก", "เสลภูมิ", "สุวรรณภูมิ", "โพนทราย", "อาจสามารถ", "เมยวดี", "ศรีสมเด็จ", "จังหาร", "เชียงขวัญ", "หนองฮี", "ทุ่งเขาหลวง", "นามน", "กมลาไสย", "ร่องคำ", "กุฉินารายณ์", "เขาวง", "ยางตลาด", "ห้วยเม็ก", "สหัสขันธ์", "คำม่วง", "ท่าคันโท", "หนองกุงศรี", "สมเด็จ", "ห้วยผึ้ง", "สามชัย", "นาดู", "ดอนจาน", "ฆ้องชัย", "กุสุมาลย์", "กุดบาก", "พรรณานิคม", "พังโคน", "วาริชภูมิ", "นิคมน้ำอูน", "น้ำอูน", "คำตากล้า", "บ้านม่วง", "อากาศอำนวย", "พนา", "ม่วงสามสิบ", "วารินชำราบ", "อำนาจเจริญ", "เสนางคนิคม", "หัวตะพาน", "พิบูลมังสาหาร", "ตาลสุม", "โพธิ์ไทร", "สำโรง", "ดอนมดแดง", "สิรินธร", "ทุ่งศรีอุดม", "ปทุมราชวงศา", "ศรีหลักชัย", "นาเยีย", "นาตาล", "เหล่าเสือโก้ก", "สว่างวีระวงศ์", "น้ำขุ่น", "สุวรรณวารี", "ทรายมูล", "กุดชุม", "คำเขื่อนแก้ว", "ป่าติ้ว", "มหาชนะชัย", "ค้อวัง", "เลิงนกทา", "ไทยเจริญ", "บ้านเขว้า", "คอนสวรรค์", "เกษตรสมบูรณ์", "หนองบัวแดง", "จัตุรัส", "บำเหน็จณรงค์", "หนองบัวระเหว", "เทพสถิต", "ภูเขียว", "บ้านแท่น", "แก้งคร้อ", "คอนสาร", "ภักดีชุมพล", "เนินสง่า", "ซับใหญ่", "กัลยาณิวัฒนา", "บ้านหว่าเฒ่า", "วังชมภู", "ซับใ", "โคกเพชร", "นายางกลัก", "บ้านเต่า", "ท่ามะไฟหวาน", "ดนนคูณ", "ชานุมาน", "ลืออำนาจ", "นาวัง", "บ้านฝาง", "พระยืน", "หนองเรือ", "ชุมแพ", "สีชมพู", "น้ำพอง", "อุบลรัตน์", "กระนวน", "บ้านไผ่", "เปือยน้อย", "พล", "แวงใหญ่", "แวงน้อย", "หนองสองห้อง", "ภูเวียง", "มัญจาคีรี", "ชนบท", "เขาสวนกวาง", "ภูผาม่าน", "ซำสูง", "โคกโพธิ์ไชย", "หนองนาคำ", "บ้านแฮด", "โนนศิลา", "กุดจับ", "หนองวัวซอ", "กุมภวาปี", "โนนสะอาด", "หนองหาน", "ทุ่งฝน", "ไชยวาน", "ศรีธาตุ", "วังสามหมอ", "บ้านดุง", "หนองบัวลำภู", "จักราช", "โชคชัย", "ด่านขุนทด", "โนนไทย", "โนนสูง", "ขามสะแกแสง", "บัวใหญ่", "ประทาย", "ปักธงชัย", "พิมาย", "ห้วยแถลง", "ชุมพวง", "สูงเนิน", "ขามทะเลสอ", "สีคิ้ว", "ปากช่อง", "หนองบุญนาก", "แก้งสนามนาง", "โนนแดง", "วังน้ำเขียว", "เทพารักษ์", "พระทองคำ", "ลำทะเมนชัย", "บัวลาย", "สีดา", "มะค่า-พลสงคราม", "โนนลาว", "กระสัง", "นางรอง", "หนองกี่", "ละหานทราย", "ประโคนชัย", "บ้านกรวด", "พุทไธสง", "ลำปลายมาศ", "สตึก", "ปะคำ", "นาโพธิ์", "หนองหงส์", "พลับพลาชัย", "ห้วยราช", "โนนสุวรรณ", "ชำนิ", "บ้านใหม่ไชยพจน์", "โนนดินแดง", "บ้านด่าน", "แคนดง", "ชุมพลบุรี", "ท่าตูม", "จอมพระ", "ปราสาท", "กาบเชิง", "รัตนบุรี", "สนม", "ศีขรภูมิ", "สังขะ", "ลำดวน", "สำโรงทาบ", "บัวเชด", "พนมดงรัก", "ศรีณรงค์", "เขวาสินรินทร์", "โนนนารายณ์", "ยางชุมน้อย", "กันทรารมย์", "กันทรลักษ์", "ขุขันธ์", "ไพรบึง", "ปรางค์กู่", "ขุนหาญ", "ราษีไศล", "อุทุมพรพิสัย", "บึงบูรพ์", "ห้วยทับทัน", "โนนคูณ", "โนนคูน", "ศรีรัตนะ", "น้ำเกลี้ยง", "วังหิน", "ภูสิงห์", "เบญจลักษ์", "พยุห์", "โพธิ์ศรีสุวรรณ", "ศิลาลาด", "โขงเจียม", "เขื่องใน", "เขมราฐ", "เดชอุดม", "นาจะหลวย", "น้ำยืน", "บุณฑริก", "ตระการพืชผล", "กุดข้าวปุ้น", "ท่าช้าง", "อินทร์บุรี", "มโนรมย์", "วัดสิงห์", "สรรพยา", "สรรคบุรี", "หันคา", "หนองมะโมง", "เนินขาม", "แก่งคอย", "หนองแค", "วิหารแดง", "หนองแซง", "บ้านหมอ", "ดอนพุด", "หนองโดน", "พระพุทธบาท", "เสาไห้", "มวกเหล็ก", "วังม่วง", "บ้านบึง", "หนองใหญ่", "บางละมุง", "พานทอง", "พนัสนิคม", "ศรีราชา", "เกาะสีชัง", "สัตหีบ", "บ่อทอง", "เกาะจันทร์", "บางเสร่", "แหลมฉบัง", "บ้างฉาง", "บ้านฉาง", "แกลง", "วังจันทร์", "บ้านค่าย", "ปลวกแดง", "เขาชะเมา", "นิคมพัฒนา", "มาบข่า", "ขลุง", "ท่าใหม่", "โป่งน้ำร้อน", "มะขาม", "แหลมสิงห์", "สอยดาว", "แก่งหางแมว", "นายายอาม", "เขาคิชฌกูฏ", "กำพุธ", "คลองใหญ่", "เขาสมิง", "บ่อไร่", "แหลมงอบ", "เกาะกูด", "เกาะช้าง", "บางคล้า", "บางน้ำเปรี้ยว", "บางปะกง", "บ้านโพธิ์", "พนมสารคาม", "ราชสาส์น", "สนามชัย", "แปลงยาว", "ท่าตะเกียบ", "คลองเขื่อน", "กบินทร์บุรี", "กาบัง", "เวียงเก่า", "บุคคล", "ข้าราชการ", "สาธารณภัย"); // เพิ่มคำเฉพาะได้ที่นี่
//				$deptword = array("สำนักงานเลขานุการสภากรุงเทพมหานคร", "สำนักงานเลขานุการผู้ว่าราชการกรุงเทพมหานคร", "สำนักงานคณะกรรมการข้าราชการกรุงเทพมหานคร", "สำนักปลัดกรุงเทพมหานคร", "สำนักการแพทย์", "สำนักอนามัย", "สำนักการศึกษา", "สำนักการโยธา", "สำนักการระบายน้ำ", "สำนักการคลัง", "สำนักเทศกิจ", "สำนักการจราจรและขนส่ง", "สำนักผังเมือง", "สำนักป้องกันและบรรเทาสาธารณภัย", "สำนักงบประมาณกรุงเทพมหานคร", "สำนักยุทธศาสตร์และประเมินผล", "สำนักสิ่งแวดล้อม", "สำนักวัฒนธรรม กีฬา และการท่องเที่ยว", "สำนักพัฒนาสังคม", "ศูนย์รับเรื่องร้องเรียน", "สำนักงานเขตพระนคร", "สำนักงานเขตป้อมปราบศัตรูพ่าย", "สำนักงานเขตสัมพันธวงศ์", "สำนักงานเขตบางรัก", "สำนักงานเขตปทุมวัน", "สำนักงานเขตยานนาวา", "สำนักงานเขตดุสิต", "สำนักงานเขตพญาไท", "สำนักงานเขตห้วยขวาง", "สำนักงานเขตพระโขนง", "สำนักงานเขตบางกะปิ", "สำนักงานเขตบางเขน", "สำนักงานเขตมีนบุรี", "สำนักงานเขตลาดกระบัง", "สำนักงานเขตหนองจอก", "สำนักงานเขตธนบุรี", "สำนักงานเขตคลองสาน", "สำนักงานเขตบางกอกใหญ่", "สำนักงานเขตบางกอกน้อย", "สำนักงานเขตตลิ่งชัน", "สำนักงานเขตภาษีเจริญ", "สำนักงานเขตหนองแขม", "สำนักงานเขตบางขุนเทียน", "สำนักงานเขตราษฎร์บูรณะ", "สำนักงานเขตดอนเมือง", "สำนักงานเขตจตุจักร", "สำนักงานเขตลาดพร้าว", "สำนักงานเขตบึงกุ่ม", "สำนักงานเขตสาทร", "สำนักงานเขตบางคอแหลม", "สำนักงานเขตบางซื่อ", "สำนักงานเขตราชเทวี", "สำนักงานเขตคลองเตย", "สำนักงานเขตประเวศ", "สำนักงานเขตบางพลัด", "สำนักงานเขตจอมทอง", "สำนักงานเขตดินแดง", "สำนักงานเขตสวนหลวง", "สำนักงานเขตวัฒนา", "สำนักงานเขตบางแค", "สำนักงานเขตหลักสี่", "สำนักงานเขตสายไหม", "สำนักงานเขตคันนายาว", "สำนักงานเขตสะพานสูง", "สำนักงานเขตวังทองหลาง", "สำนักงานเขตคลองสามวา", "สำนักงานเขตบางนา", "สำนักงานเขตทวีวัฒนา", "สำนักงานเขตทุ่งครุ", "สำนักงานเขตบางบอน");

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

				if ($this->arr_word_thai_cut) {	// $arr_word_thai_cut อ่านเก็บไว้จาก pdf_wordarray_thaicut.php
					foreach ($this->arr_word_thai_cut as $key => $value) {
//						echo "key:$key-->value:$value<br>";
						$mypatt = $value;
						if (preg_match("/($mypatt)/",$dataIn,$match)) {
							$pos_i = strpos($dataIn, $match[0]);
							if ($pos_i < $min_pos_i) {
								$min_pos_i = $pos_i;
								$ret_len = strlen($match[0]);
							}
//							echo "***** dataIn($dataIn)->".substr($dataIn, $pos_i, $ret_len)." , match[0]=".$match[0]."<br>";
						} 
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
				if (preg_match("/([ก-ฮ][่|้|๊|๋])?อ[ง|น|ม|ย|ร|ล]/",$dataIn,$match)) {  // กอง ตอน สอน มอม กอม ช้อน ค้อน ร้อน ซ้อน ซ่อน ค่อน ป้อน 
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				} 
				if (preg_match("/([ก-ฮ][่|้|๊|๋])?า[ก|บ|ด|ง|น|ม|ย|ร|ล]/",$dataIn,$match)) {  // การ กาง สาน พาล ช้าง ค่าง ล้าง ย้าย ซ้าย
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				} 
				if (preg_match("/([ก-ฮ][่|้|๊|๋])?ว[ง|น|ม|ย]/",$dataIn,$match)) {  // สวย สวน ล้วน ม่วน ป่วน ยวน
					$pos_i = strpos($dataIn, $match[0]);
					if ($pos_i < $min_pos_i) {
						$min_pos_i = $pos_i;
						$ret_len = strlen($match[0]);
					}
				} 
				if (preg_match("/([ก-ฮ])([ย|ร|ล|ม|น])?([ุ|ู])([่|้|๊|๋])?/",$dataIn,$match)) {  // อยู่ รู้ หู หรู ครู ดู สู้ พลู
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
				if (preg_match("/([ก-ฮ]([ล|ร|ว])(า)([ง|ก|บ|น|ด|ร|ล|ย|ว]))/",$dataIn,$match)) { // กลาง ปล่าว กลาย สลาย มลาย ทวาย พราง กราบ ทราบ
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
//				$spec_ch = array("|", " ", ".", ",", ")", "/", "\\","(","@","$","%","&","*","#","!","<",">","{","[","]");
				$spec_ch = array("|", " ", ".", ",", ")", "/", "\\","@","$","%","&","*","#","!","<",">","]");
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

			function thaiCutLinePDF($dataIn, $w, $delim, $f_reject_newline=true){
				$cw=&$this->CurrentFont['cw'];
//				$delim = "@";
				$vowels = array("<br>", "<BR>", "<Br>", "<br />", "<BR />", "<Br />", "<br/>", "<BR/>", "<Br/>", "\n");
				$enter = array("*enter*", "*Enter*", "*ENTER*");
//				for($k=0; $k < strlen($dataIn); $k++) echo "ch $k ={".substr($dataIn,$k,1)."} {".ord(substr($dataIn,$k,1))."}<br>";
//				echo "---->1>$dataIn|<br>";
				if ($f_reject_newline)	// true คือ ไม่สน new line ที่มากับ text เดิม
					$tempdataIn = str_replace($vowels, " ", $dataIn); // ใส่ช่องว่างแทน แล้วนำไปตัดคำใหม่ ไม่สน new line ของเดิม
				else		// เปลี่ยน new line ที่มากับ text เดิม ให้เป็น | เพื่อไปตัดคำก่อน
					$tempdataIn = str_replace($vowels, "|", $dataIn); // แล้วค่อยเอา $delim มาแทนทีหลัง เพื่อตัดปัญหา \n ไม่นับจำนวนใน string
				$tempdataIn = str_replace($enter,"|",$tempdataIn);
//				if (strpos($dataIn, $enter[0])!==false) echo "0-dataIn=$dataIn, tempdataIn=$tempdataIn<br>";
//				else if (strpos($dataIn, $enter[1])!==false) echo "1-dataIn=$dataIn, tempdataIn=$tempdataIn<br>";
//				else if (strpos($dataIn, $enter[2])!==false) echo "2-dataIn=$dataIn, tempdataIn=$tempdataIn<br>";
//				if ($dataIn=="ใช้งาน/ยกเลิก")
//					echo "---->1>tempdataIn=$tempdataIn|<br>";
//				$tempdataIn = trim($tempdataIn);
				if (substr($tempdataIn,strlen($tempdataIn)-1)=="|") 
					$tempdataIn = substr($tempdataIn,0,strlen($tempdataIn)-1);
//				echo "---->2>$tempdataIn|<br>";
				$arr_tempdataIn = explode("|", $tempdataIn);
				$arr_out = (array) null;
				foreach($arr_tempdataIn as $idx => $tempdataIn) {
					$midch_cnt=0;
					$i = 0;
					$ctext = "";
					$out = "";
					$loop = 0;
					while (strlen($tempdataIn) > 0) {
						$ch = substr($tempdataIn,$i,1);
						if (strpos( "ิีึืุูํ่้๊๋็ํ์ั", $ch) === false) { // ถ้าเป็นอักษรแถวกลาง (ไม่เป็นอักษรยก   ิ  ี  ื  ึ  ุ  ู  ํ  ่  ้  ๊  ๋  ั  ็)
							$rest_w = $w - $this->GetStringWidth("$ctext");
							$mylen = $this->thaiWrapPDF($tempdataIn, $delim, $rest_w);
//							echo "ctext=$ctext, rest_w=$rest_w, tempdataIn=$tempdataIn, mylen=$mylen<br>";
							if ($mylen > 0) {
								$loop = 0;
								$text1 = substr($tempdataIn,0,$mylen);
	//							if ($dataIn=="ใช้งาน/ยกเลิก")
	//								echo "after cut ($ch)=$tempdataIn--[$text1]<br>";
								$cnt_up=0;
								for($ii=0; $ii < strlen($text1); $ii++) {
									$ch1=substr($text1,$ii,1);
									if (strpos( "ิีึืุูํ่้๊๋ั็์ั", $ch1) > -1) { // เป็นอักษรยก   ิ  ี  ื  ึ  ุ  ู  ํ  ่  ้  ๊  ๋  ั  ็
										$cnt_up++;
									}
								}
								$mylen1 = $mylen - $cnt_up;
								$str_w = $this->GetStringWidth("$ctext"."$text1");
								if ($str_w > $w - 1) {
									// ถ้าเกิน w ก็ ใส่ตัวขั้น ถึงประโยคก่อนหน้า
									if (strlen($ctext) > 0)
										if (trim($text1)=="|")
											$out = "$out".$ctext;
										else
											$out = "$out".$ctext."$delim";
									$ctext=$text1;		// ขึ้นบรรทัดใหม่ด้วย ประโยคที่เกินมา
									$tempdataIn = substr($tempdataIn,$mylen);
									$midch_cnt=$mylen1;
								} else { // <= $w - 1	ไม่เกินช่องกว้างที่กำหนด
									$delim_pos = strpos($text1,"|");
									if ($delim_pos !== false) {	// มี ตัวตัด line | ใน text1
	//									echo "found | in $text1<br>";
										$ctext = "$ctext"."$text1";
										$out = "$out".$ctext;				// ต่อ text1 เข้าไปแล้วในบรรทัดนี้
										$ctext="";		// ขึ้นบรรทัดใหม่ แบบว่าง ๆ
										$tempdataIn = substr($tempdataIn,$mylen);
										$midch_cnt=0;
									} else {
										$ctext = "$ctext"."$text1";
										$tempdataIn = substr($tempdataIn,$mylen);
										$midch_cnt = $midch_cnt + $mylen1;
									}
								}
							} else { // else if ($mylen > 0)
								// ถ้า $mylen == 0 แปลว่า ไม่มีการตัดประโยคได้เลยในรอบนี้ 
								if ($rest_w < $w) { // ถ้ารอบที่ตัดไม่ได้เพราะเหลือความกว้างไม่พอให้ขึ้นบรรทัดใหม่แล้วลองตัดใหม่
									$out = "$out".$ctext."$delim";
									$ctext = "";
									$midch_cnt=0;
								} else {	// ถ้า ความกว้างเต็ม ($w) แล้วยังตัดไม่ได้ทั้วที่ความกว้างของข้อมูลมากกว่า ก็ต้องตัดตาม $w
									$tdata_w = $this->GetStringWidth("$tempdataIn");
									if ($tdata_w > $w) {	// ตัดตามความกว้าง $w อย่างเดียว
										$len = 0;
										for($ii=0; $ii < strlen($tempdataIn); $ii++) {
											$str_w = $this->GetStringWidth(substr($tempdataIn,0,$ii));
											if ($str_w > $w) {
												$len= $ii-1;
												break;
											}
										}
										if ($len) {
											$ctext = substr($tempdataIn,0,$len);
											$out = "$out".$ctext."$delim";
											$ctext = "";
											$tempdataIn = substr($tempdataIn,$len);
										} else {
											$ctext = $tempdataIn;
											$tempdataIn = "";
										}
									} else {
										$ctext = $tempdataIn;
										$tempdataIn = "";
									}
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
					$arr_out[] = $out;
				} // end foreach arr_tempdataIn
				
				$output = implode("|", $arr_out);
				$output = str_replace("|",$delim,$output);
				return $output;
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
			
			function open_tab($head_text, $head_width, $head_line_height=7, $head_border="TRHBL", $head_align="C", $font, $font_size="14", $font_style="b", $font_color=0, $fill_color=0, $COLUMN_FORMAT, $func_aggreg, $f_nofirsthead=false, $head_text_rotate)
			{
				if (!$font) $font=$this->FontFamily;
//				if (!$font_style) $font_style=$this->FontStyle;	// ถ้าไม่มีค่าอาจหมายถึง ไม่ bold ไม่ italic ไม่ underline ไม่ได้หมายถึง ไม่มีค่า ดังนั้นไม่ต้อง move ค่า default
				if (!$font_size) $font_size=$this->FontSizePt;
				$this->f_table = 1;
				$this->arr_func_aggreg = (array) null;		// array tab head text format=text1 line 1|text1 line 2|text1 line 3,text2 line 1|text2 line 2|text2 line 3,.....
				$this->arr_tab_head = (array) null;		// array tab head text format=text1 line 1|text1 line 2|text1 line 3,text2 line 1|text2 line 2|text2 line 3,.....
				$this->arr_head_width = explode(",", $head_width);	// array tab head width
//				echo "$this->arr_head_width::".implode(",",$this->arr_head_width)."<br>";
				$this->head_fill_color = explode(",", $fill_color);			//  table head fill color format RRGGBB
				$this->head_font_name = explode(",", $font);					//  table head font name ex  "AngsanaUPC" "cordia"
				$this->head_font_size = explode(",", $font_size);			//  table head font size
				$this->head_font_style = explode(",", $font_style);			//  table head font style "B" bold "I" italic "U" underline
				$this->head_font_color = explode(",", $font_color);		//  table head font color  format RRGGBB
				$this->head_border = explode(",", $head_border);		//  table head border
				$this->arr_head_align = explode(",", $head_align);		//  table head align
				$this->head_line_height = $head_line_height;		//  table head line height

				$this->tab_head_title = "";		//  table head title

				$this->arr_column_map = (array) null;
				$this->arr_column_sel = (array) null;
				$this->arr_column_width = (array) null;
				$this->arr_column_align = (array) null;
				if (!$COLUMN_FORMAT) {	// ถ้าส่งค่าการปรับแต่ง รายงานเข้ามา
					for($i=0; $i < count($this->arr_head_width); $i++) {
						$this->arr_column_map[] = $i;		// link index ของ head 
						$this->arr_column_sel[] = 1;			// 1=แสดง	0=ไม่แสดง   
					}
					$this->arr_column_width = explode(",",$head_width);	// ความกว้าง
					$this->arr_column_align = explode(",",$head_align);		// align กำหนดไว้ก่อน แต่จริง ๆ ไม่ได้ใช้ค่า align ของ head แต่ จะนำไปใช้ใน add_data_tab เป็น align ของ data
					$COLUMN_FORMAT = implode(",",$this->arr_column_map)."|".implode(",",$this->arr_column_sel)."|".implode(",",$this->arr_column_width)."|".implode(",",$this->arr_column_align);
				} else {
					$arrbuff = explode("|",$COLUMN_FORMAT);
					$this->arr_column_map = explode(",",$arrbuff[0]);		// index ของ head เริ่มต้น
					$this->arr_column_sel = explode(",",$arrbuff[1]);			// 1=แสดง	0=ไม่แสดง
					$this->arr_column_align = explode(",",$arrbuff[3]);		// align
					if ($head_width != $arrbuff[2]) {
						$this->arr_column_width = explode(",",$head_width);		// ความกว้าง
						$COLUMN_FORMAT = implode(",",$this->arr_column_map)."|".implode(",",$this->arr_column_sel)."|".implode(",",$this->arr_column_width)."|".implode(",",$this->arr_column_align);
					} else {
						$this->arr_column_width = explode(",",$arrbuff[2]);		// ความกว้าง
					}
				}
				// ถ้าเจอ TNUM แม้แต่ที่เดียวใน $func_aggreg head ก็จะใช้ TNUM ด้วยไปโดยอัตโนมัติ
				if (strpos($func_aggreg, "TNUM") !== false) $this->f_head_tnum = true;
				else $this->f_head_tnum = false;
//				echo "map=".implode(",",$this->arr_column_map)."  sel=".implode(",",$this->arr_column_sel)."  width=".implode(",",$this->arr_column_width)."  align=".implode(",",$this->arr_column_align)." [$COLUMN_FORMAT]<br>";

//				echo "head font=".implode(",",$this->head_font_name).", size=".implode(",",$this->head_font_size)."<br>";

//				$this->SetFont($this->head_font_name,$this->head_font_style,$this->head_font_size);
				
				$this->merge_rows_h = (array) null;
				for($i = 0; $i < $cnt_column_width; $i++) {
					$this->merge_rows_h[]=0;
				}

				if (strlen(trim($head_text))==0) {
					$cnt_column_head = 0;
//					echo "1..head text = 0<br>";
				} else {
					$buff_arr_tab_head = explode(",", $head_text);
					$cnt_column_head = count($buff_arr_tab_head);
//					echo "2..head text = $cnt_column_head<br>";
				}	// end if (strlen(trim($head_text))==0)
				$cnt_column_width = count($this->arr_head_width);
//				echo "cnt_column_head=$cnt_column_head<br>";
				if ($cnt_column_head==0) {
					$this->f_no_head = true; 
					$this->arr_tab_head = (array) null;
					for($i = 0; $i < $cnt_column_width; $i++) {
						$this->arr_tab_head[]="";
					}
					$cnt_column_head = $cnt_column_width;
//					echo "3..head width = $cnt_column_head<br>";
				} else {	// else if ($cnt_column_head==0)
//					echo "4..head text = $cnt_column_head<br>";
					// จัดการกับ function aggregate
					if ($func_aggreg) {
//						echo "func_aggreg=$func_aggreg<br>";
						$this->arr_func_aggreg = explode(",", $func_aggreg);
					} // end if ($func_aggreg)
					// จบการจัดการกับ function aggregate

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
							if ($this->arr_column_sel[$i]==1) {	// เฉพาะที่เลือก
								if (strlen(trim($buff_arr_tab_head[$i])) > 0) $founded = true;
								$sub_tab_head = explode("|", trim($buff_arr_tab_head[$i]));
//								echo "sub_tab_head[$line_idx]=".$sub_tab_head[$line_idx]."<br>";
								$arr_font_name = explode("|", ($this->head_font_name[$i] ? $this->head_font_name[$i] : $this->head_font_name[0]) );
								$arr_font_style = explode("|", ($this->head_font_style[$i] ? $this->head_font_style[$i] : $this->head_font_style[0]) );	// 	กรณี style ถ้าจะกำหนดให้ไม่มีค่า ใส่ none
								$arr_font_size = explode("|",  ($this->head_font_size[$i] ? $this->head_font_size[$i] : $this->head_font_size[0]) );
								$arr_font_color = explode("|",  ($this->head_font_color[$i] ? $this->head_font_color[$i] : $this->head_font_color[0]) );	// default สีตัวอักษร สีดำ
								$arr_head_border = explode("|",  ($this->head_border[$i] ? $this->head_border[$i] : (count($this->head_border) > 1 ? "" : $this->head_border[0])) );
								$arr_head_fill_color = explode("|",  ($this->head_fill_color[$i] ? $this->head_fill_color[$i] : $this->head_fill_color[0]) );	// default สีพื้น สีขาว
								$t_head_font_name = ($arr_font_name[$line_idx] ? $arr_font_name[$line_idx] : $arr_font_name[0]);
								$buff = ($arr_font_style[$line_idx] ? $arr_font_style[$line_idx] : $arr_font_style[0]);
								$t_head_font_style = "";
								if (strpos(strtolower($buff),"b")!==false) {
									$t_head_font_style .= "b";
									$buff = str_replace("b","",$buff);
									$buff = str_replace("B","",$buff);
								}
								if (strpos(strtolower($buff),"U")!==false) {
									$t_head_font_style .= "u";
									$buff = str_replace("u","",$buff);
									$buff = str_replace("U","",$buff);
								}
								if (strpos(strtolower($buff),"I")!==false) {
									$t_head_font_style .= "i";
									$buff = str_replace("i","",$buff);
									$buff = str_replace("I","",$buff);
								}
								if (strpos(strtolower($buff),"r")!==false) {
									$buff = str_replace("r","",$buff);
									$buff = str_replace("R","",$buff);
									$t_head_rotate_angle = ($buff ? $buff : "90");
								} else {
									$t_head_rotate_angle = "";
								}
								if ($t_head_rotate_angle)	$this->Rotate((int)$t_head_rotate_angle);  else $this->Rotate(0);
								$t_head_font_size = ($arr_font_size[$line_idx] ? $arr_font_size[$line_idx] : $arr_font_size[0]);
								$t_head_font_color = ($arr_font_color[$line_idx] ? $arr_font_color[$line_idx] : $arr_font_color[0]);
								$t_head_border = ($arr_head_border[$line_idx] ? $arr_head_border[$line_idx] : (count($arr_head_border) > 1 ? "" : $arr_head_border[0]));
								$t_head_fill_color = ($arr_head_fill_color[$line_idx] ? $arr_head_fill_color[$line_idx] : $arr_head_fill_color[0]);
//								echo "head font=".$this->head_font_name.", font=$font<br>";
								$this->SetFont($t_head_font_name,$t_head_font_style,$t_head_font_size);
								$c1 = strpos($sub_tab_head[$line_idx], "<**");
								if ($c1 !== false) {
									$c2 = strpos($sub_tab_head[$line_idx], "**>");
//									echo "grptext=".$grptext.", sub_tab_head[$line_idx]=".$sub_tab_head[$line_idx]." substr=".substr($sub_tab_head[$line_idx], $c1, $c2-$c1+3)."<br>";
									if ($grptext != substr($sub_tab_head[$line_idx], $c1, $c2-$c1+3)) {
										if ($sumw > 0) {
//											echo "1..thai cut w:$sumw, text=$realtext<br>";
//											$arr_b = $this->line_MultiCellThaiCut($sumw, $this->head_line_height, $realtext, $this->head_border, $this->arr_head_align[$i], $this->head_fill_color);
											if ($this->f_head_tnum) $realtext = convert2thaidigit($realtext); // ถ้ามีการทำ TNUM  เปลี่ยนค่าให้เป็น ตัวเลขไทยก่อนตัดคำ
											$arr_b = $this->MultiCellThaiCut($sumw, $this->head_line_height, $realtext, $t_head_border, $this->arr_head_align[$i], $t_head_fill_color, false);
//											echo "1..head-($i)>:".implode("|",$arr_b).", grp=$grptext, sumw=$sumw<br>";
											for($k = 0; $k < count($arr_b); $k++) {
//												echo "1.2..arr_b[$k]=".$arr_b[$k]."<br>";
												if ($k > 0) $grptext1 = str_replace("**>","^**>",$grptext); else $grptext1 = $grptext;
												$arr_b[$k] = $grptext1.$arr_b[$k];
//												echo "1.2..arr_b[$k]=".$arr_b[$k]."<br>";
//												$numline++;
											}
											$newtext = implode("|", $arr_b);
											for($k = $start_i; $k < $i; $k++) {
												$sub_tab_head1 = explode("|", $buff_arr_tab_head[$k]);
												$sub_tab_head1[$line_idx] = $newtext;
												$buff_arr_tab_head[$k] = implode("|", $sub_tab_head1);
												$numline = count(explode("|", $buff_arr_tab_head[$k]));
												if ($numline > $max_line) $max_line = $numline;
											}
//											echo "1..newtext=$newtext, numline=$numline, max_line=$max_line<br>";
										}
										$start_i = $i; 
										$grptext = substr($sub_tab_head[$line_idx], $c1, $c2-$c1+3);
										$realtext = substr($sub_tab_head[$line_idx], $c2+3);
//										echo "after newtext --> grptext=".$grptext.", realtext=".$realtext." ($c2 - $c1)<br>";
										$sumw = $this->arr_head_width[$i];
									} else {
										$sumw += $this->arr_head_width[$i];
//										echo "($i) in grp $grptext sumw=$sumw<br>";
									}
								} else {	// else if ($c1 !== false) --> ถ้าไม่มีกลุ่ม head
									if ($sumw > 0) {
//										echo "2..thai cut w:$sumw, text=$realtext<br>";
//										$arr_b = $this->line_MultiCellThaiCut($sumw, $this->head_line_height, $realtext, $this->head_border, $this->arr_head_align[$i], $this->head_fill_color);
										if ($this->f_head_tnum) $realtext = convert2thaidigit($realtext); // ถ้ามีการทำ TNUM  เปลี่ยนค่าให้เป็น ตัวเลขไทยก่อนตัดคำ
										$arr_b = $this->line_MultiCellThaiCut($sumw, $this->head_line_height, $realtext, $t_head_border, $this->arr_head_align[$i], $t_head_fill_color, false);
//										echo "2..head-($i)>:".implode("|",$arr_b).", grp=$grptext, sumw=$sumw<br>";
										for($k = 0; $k < count($arr_b); $k++) {
//											echo "2.1..arr_b[$k]=".$arr_b[$k]."<br>";
											if ($k > 0) $grptext1 = str_replace("**>","^**>",$grptext); else $grptext1 = $grptext;
											$arr_b[$k] = $grptext1.$arr_b[$k];
//											echo "2.2..arr_b[$k]=".$arr_b[$k]."<br>";
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
//										echo "2..newtext=$newtext, numline=$numline, max_line=$max_line<br>";
										$start_i = -1; 
										$grptext = "";
										$realtext = "";
//										echo "realtext=$realtext ($c2 - $c1)<br>";
										$newtext="";
										$sumw = 0;
									}
									if (trim($sub_tab_head[$line_idx])) {
//										echo "3..thai cut w:".$this->arr_head_width[$i].", text=".trim($sub_tab_head[$line_idx])."<br>";
//										$arr_b = $this->line_MultiCellThaiCut($this->arr_head_width[$i], $this->head_line_height, trim($sub_tab_head[$line_idx]), $this->head_border, $this->arr_head_align[$i], $this->head_fill_color);
										if ($this->f_head_tnum) $realtext = convert2thaidigit(trim($sub_tab_head[$line_idx])); else $realtext = trim($sub_tab_head[$line_idx]); 
										// ถ้ามีการทำ TNUM  เปลี่ยนค่าให้เป็น ตัวเลขไทยก่อนตัดคำ
										$arr_b = $this->MultiCellThaiCut($this->arr_head_width[$i], $this->head_line_height, $realtext, $t_head_border, $this->arr_head_align[$i], $t_head_fill_color, false);
//										echo "3..head-($i)>:".implode("|",$arr_b)." head_w=".$this->arr_head_width[$i]."<br>";
										for($k = 0; $k < count($arr_b); $k++) {
//											echo "arr_b[$k]=".$arr_b[$k]."<br>";
											$arr_b[$k] = $arr_b[$k];
//											echo "3.2..arr_b[$k]=".$arr_b[$k]."<br>";
//											$numline++;
										}
										$newtext = implode("|", $arr_b);
										$sub_tab_head[$line_idx] = $newtext;
										$buff_arr_tab_head[$i] = implode("|", $sub_tab_head);
										$numline = count(explode("|", $buff_arr_tab_head[$i]));
										if ($numline > $max_line) $max_line = $numline;
//										echo "3..line_idx=$line_idx, newtext=$newtext, buff_arr_tab_head[$i]=".$buff_arr_tab_head[$i].", numline=$numline, max_line=$max_line<br>";
									}
								}
							} // end if ($this->arr_column_sel[$i]==1)
						} // end for $i loop
						if ($sumw > 0) {
//.							echo "4..thai cut w:$sumw, text=$realtext<br>";
//							$arr_b = $this->line_MultiCellThaiCut($sumw, $this->head_line_height, $realtext, $this->head_border, $this->arr_head_align[$i], $this->head_fill_color);
							if ($this->f_head_tnum) $realtext = convert2thaidigit($realtext); // ถ้ามีการทำ TNUM  เปลี่ยนค่าให้เป็น ตัวเลขไทยก่อนตัดคำ
							$arr_b = $this->MultiCellThaiCut($sumw, $this->head_line_height, $realtext, $t_head_border, $this->arr_head_align[$i], $t_head_fill_color, false); 
							// $t_head_border กับ $t_head_fill_color ต้องกำหนดไว้แล้ว เพราะถ้า $sumw > 0 มันต้องผ่าน loop for ข้างบนอย่างน้อย 1 ครั้ง
//							echo "4..head-($i)>:".implode("|",$arr_b).", grp=$grptext, sumw=$sumw<br>";
							for($k = 0; $k < count($arr_b); $k++) {
//								echo "4.1..arr_b[$k]=".$arr_b[$k]."<br>";
								if ($k > 0) $grptext1 = str_replace("**>","^**>",$grptext); else $grptext1 = $grptext;
								$arr_b[$k] = $grptext1.$arr_b[$k];
//								echo "4.2..arr_b[$k]=".$arr_b[$k]."<br>";
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
//					echo "arr_tab_head==>".implode(",", $buff_arr_tab_head).", founded=$founded<br>";
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
							// เติม ช่องว่างสำหรับบรรทัดที่มี column element ไม่เท่ากัน ให้เสมอกัน
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
					} else {
						$this->arr_tab_head = explode(",", $head_text);
					} // end if $founded
					// ถ้า มี head ที่มีค่า แม้แต่ column เดียว ก็จะถือว่า จะพิมพ์ head
					if ($founded)	
						$this->f_no_head = false;
					else
						$this->f_no_head = true;
				}	// end if ($cnt_column_head==0)
//				echo "cnt_column_head ($cnt_column_head) != cnt_column_width ($cnt_column_width) (f_no_head=".$this->f_no_head.")<br>";
				if ($cnt_column_head != $cnt_column_width) {	// ถ้า ส่งจำนวน column เข้ามาไม่ถูกต้อง return error
					return false; 
				} else {
//					echo "cnt_column_head ($cnt_column_head) != cnt_column_width ($cnt_column_width) (f_no_head=".$this->f_no_head.")<br>";
					// $f_nofirsthead ควบคุมการพิมพ์หัวครั้งแรก ในโปรแกรมหลัก ใน กรณีที่มีการพิมพ์หัว
					return ($f_nofirsthead ? true : $this->print_tab_header());
				}
			}
			
			function print_tab_header()
			{
				$res = true;
				// ถ้า open table แล้ว และ ไม่ no head ก็ print head
//				echo "tab_header->f_table=".$this->f_table.", f_no_head=".$this->f_no_head."<br>";
				if ($this->f_table==1 && (!$this->f_no_head)) {
				
					$start_x = $this->x;			$start_y = $this->y;				$max_y = $this->y;
					$first_y = $this->y;

					$cnt_line_head = count(explode("|", $this->arr_tab_head[0]));
					// check หน้าให้ก่อนพิมพ์บรรทัดต่อไป
					$cnt_column_head = count($this->arr_tab_head);

//					echo "cnt_line_head=$cnt_line_head, cnt_column_head=$cnt_column_head  (f_no_head=".$this->f_no_head.")<br>";
					if ($cnt_column_head > 0) {	// ถ้า มีค่า column เข้ามาถูกต้อง ทำงานต่อ
						$cnt_column_show = 0;
						$head_t_w = 0;
						for($jjj = 0; $jjj < $cnt_column_head; $jjj++) {
							$arr_setborder[$jjj] = ""; 	// use for adjust L R Border
							if ($this->arr_column_sel[$this->arr_column_map[$jjj]]==1) { 
								$cnt_column_show++; $head_t_w += $this->arr_column_width[$this->arr_column_map[$jjj]]; 
							}
						}

/* 				$this->tab_head_title = 
						text1^font^size^style^color^fill^align^border||text2^font^size^style^color^fill^align^border||text3^font^size^style^color^fill^align^border */
//						echo "bf...start_y=$start_y , first_y=$first_y , this->y=$this->y , max_y=$max_y<br>";
						if ($this->tab_head_title) {	// กรณีที่มีค่าของ table head title ค่า space ให้ถือว่าเป็นบรรทัดว่าง
							$a_text = explode("||",$this->tab_head_title);
//							echo "this->tab_head_title=$this->tab_head_title<br>";
							for($ii = 0; $ii < count($a_text); $ii++) {
								$a_sub_text = explode("^",$a_text[$ii]);
								$font = ($a_sub_text[1] ? $a_sub_text[1] : "");
								$border = ($a_sub_text[7] ? $a_sub_text[7] : "");
								$align = ($a_sub_text[6] ? $a_sub_text[6] : "C");
								$size = ($a_sub_text[2] ? $a_sub_text[2] : "14");
								$style = ($a_sub_text[3] ? $a_sub_text[3] : "");
								$color = ($a_sub_text[4] ? $a_sub_text[4] : 0);
								$fill_co = ($a_sub_text[5] ? $a_sub_text[5] : 0);
//								echo "a_sub_text [0]=".$a_sub_text[0]." , font=".$font." , border=$border , align=$align<br>";
//			add_text_line($text, $line_height=7, $border="TRHBL", $align, $font, $font_size="14", $font_style="", $font_color=0, $fill_color=0, $tab_PageBreakTrigger=10)
								$this->add_text_line($a_sub_text[0], 7, $border, $align, $font, $size, $style, $color, $fill_co, 10);
							}
							$start_y = $this->y;
						}

						$columngrp="";
						for($cntline = 0; $cntline < $cnt_line_head; $cntline++) {
							$text_merge = "";
							$head_align = "";
							$sum_merge = 0;
							$cnt_sel_i = 0;	// นับเฉพาะรายการที่เลือก
							for($iii = 0; $iii < $cnt_column_head; $iii++) {
								if ($this->arr_column_sel[$this->arr_column_map[$iii]]==1) {	// 1 = แสดง column นี้
									$arr_font_name = explode("|", ($this->head_font_name[$this->arr_column_map[$iii]] ? $this->head_font_name[$this->arr_column_map[$iii]] : $this->head_font_name[0]) );
									$arr_font_style = explode("|", ($this->head_font_style[$this->arr_column_map[$iii]] ? $this->head_font_style[$this->arr_column_map[$iii]] : $this->head_font_style[0]) ); 
									// 	กรณี style ถ้ากำหนดให้ไม่มีค่า ใหใส่ none
									$arr_font_size = explode("|",  ($this->head_font_size[$this->arr_column_map[$iii]] ? $this->head_font_size[$this->arr_column_map[$iii]] : $this->head_font_size[0]) );
									$arr_font_color = explode("|",  ($this->head_font_color[$this->arr_column_map[$iii]] ? $this->head_font_color[$this->arr_column_map[$iii]] : $this->head_font_color[0]) );	// default สีตัวอักษร สีดำ
									$arr_head_border = explode("|",  ($this->head_border[$this->arr_column_map[$iii]] ? $this->head_border[$this->arr_column_map[$iii]] : (count($this->head_border) > 1 ? "" : $this->head_border[0])) );
									$arr_head_fill_color = explode("|",  ($this->head_fill_color[$this->arr_column_map[$iii]] ? $this->head_fill_color[$this->arr_column_map[$iii]] : $this->head_fill_color[0]) );	// default สีพื้น สีขาว
									$t_head_font_name = ($arr_font_name[$cntline] ? $arr_font_name[$cntline] : $arr_font_name[0]);
//									$t_head_font_style = ($arr_font_style[$cntline] ? $arr_font_style[$cntline] : $arr_font_style[0]);	// 	กรณี style ถ้าไม่มีค่า หมายถึงให้เป็นค่าปกติ
									$buff = ($arr_font_style[$cntline] ? $arr_font_style[$cntline] : $arr_font_style[0]);
									$t_head_font_style = "";
									if (strpos(strtolower($buff),"b")!==false) {
										$t_head_font_style .= "b";
										$buff = str_replace("b","",$buff);
										$buff = str_replace("B","",$buff);
									}
									if (strpos(strtolower($buff),"U")!==false) {
										$t_head_font_style .= "u";
										$buff = str_replace("u","",$buff);
										$buff = str_replace("U","",$buff);
									}
									if (strpos(strtolower($buff),"I")!==false) {
										$t_head_font_style .= "i";
										$buff = str_replace("i","",$buff);
										$buff = str_replace("I","",$buff);
									}
									if (strpos(strtolower($buff),"r")!==false) {
										$buff = str_replace("r","",$buff);
										$buff = str_replace("R","",$buff);
										$t_head_rotate_angle = ($buff ? $buff : "90");
									} else {
										$t_head_rotate_angle = "";
									}
									if ($t_head_rotate_angle)	$this->Rotate((int)$t_head_rotate_angle);  else $this->Rotate(0);
									$t_head_font_size = ($arr_font_size[$cntline] ? $arr_font_size[$cntline] : $arr_font_size[0]);
									$t_head_font_color = ($arr_font_color[$cntline] ? $arr_font_color[$cntline] : $arr_font_color[0]);		// default สีตัวอักษร สีดำ
									$t_head_border = ($arr_head_border[$cntline] ? $arr_head_border[$cntline] : $arr_head_border[0]);
									$t_head_fill_color = ($arr_head_fill_color[$cntline] ? $arr_head_fill_color[$cntline] : $arr_head_fill_color[0]);	// default สีพื้น สีขาว

									$border = $t_head_border;
									$head_align = $this->arr_head_align[$this->arr_column_map[$iii]];

									$this->SetFont($t_head_font_name,$t_head_font_style,$t_head_font_size);
									if (strlen($t_head_font_color)==6) {
										$r=substr($t_head_font_color,0,2);
										$g=substr($t_head_font_color,2,2);
										$b=substr($t_head_font_color,4,2);
									} else { $r="00"; $g="00"; $b="00"; }
//									echo "font:$r $g $b<br>";
									$this->SetTextColor(hexdec("$r"),hexdec("$g"),hexdec("$b"));
				
									if (strlen($t_head_fill_color)==6) {
										$r=substr($t_head_fill_color,0,2);
										$g=substr($t_head_fill_color,2,2);
										$b=substr($t_head_fill_color,4,2);
										$this->SetFillColor(hexdec("$r"),hexdec("$g"),hexdec("$b"));
//										echo "fill:$r $g $b<br>";
									}
//									echo "af...start_y=$start_y , first_y=$first_y , this->y=$this->y , max_y=$max_y<br>";

									$nline = explode("|", $this->arr_tab_head[$this->arr_column_map[$iii]]);	// $this->arr_tab_head[$iii]);
//									echo "headtext->".$this->arr_tab_head[$this->arr_column_map[$iii]]."<br>";
									$sum_w = 0;
									for($jjj = 0; $jjj <= $iii; $jjj++)
										if ($this->arr_column_sel[$this->arr_column_map[$jjj]]==1) $sum_w+=(float)$this->arr_column_width[$this->arr_column_map[$jjj]];		// (float)$this->arr_head_width[$jjj];
//									echo "line:$cntline column:$iii text:".$nline[$cntline]."<br>";
									$chk_merge = strpos($nline[$cntline],"<**");
									if ($chk_merge!==false) {	// ถ้าเป็น column merge
										$c = strpos($nline[$cntline],"**>");
										if ($c!==false) {
											$buff_colgrp = substr($nline[$cntline], $chk_merge+3, $c-($chk_merge+3));
											if (strpos($buff_colgrp,"^")!==false) { 
												$f_mergeup = 1;	// 
												$buff_colgrp = substr($buff_colgrp,0,strlen($buff_colgrp)-1);
											} else $f_mergeup = 0;
											if (!$text_merge)  $text_merge = substr($nline[$cntline], $c+3);
//											echo "columngrp($columngrp)==buff_colgrp($buff_colgrp) || text_merge ($text_merge) ,  f_mergeup=$f_mergeup,  tmp_f_mergeup=$tmp_f_mergeup<br>";
//											$chk_up_merge = false;
//											if ($cntline > 0)	 $chk_up_merge = strpos($nline[$cntline-1],"<**");
											if ($columngrp==$buff_colgrp) {
												// $f_mergeup merge กับ cell บน
//												echo "tmp_f_mergeup=$tmp_f_mergeup , border=$border<br>";
												$tmp_border = ($tmp_f_mergeup==1 ? "" : (strpos($border,"T")===false ? "" : "T")); 	// ถ้า merge กับ cell บน ไม่ต้อง draw top border 
																																																// ถ้าไม่ แต่ ไม่ได้กำหนดให้ draw top ก็ไป draw top แต่ถ้ากำหนด ก็จะ draw top	
												if ($cntline == $cnt_line_head-1) $tmp_border .= (strpos($border,"B")===false ? "" : "B");	// ถ้าเป็นบรรทัดสุดท้าย และ ถ้ากำหนด "B" ก็ให้ draw 'B'
//												echo "before add new sum = $sum_merge<br>";
												$sum_merge+=(float)$this->arr_column_width[$this->arr_column_map[$iii]];	// (float)$this->arr_head_width[$iii];
//												echo "check last record grp($columngrp) $iii == $cnt_column_head-1 == $cnt_column_show-1<br>";
												if ($cnt_sel_i == $cnt_column_show-1) { // ตัวสุดท้ายที่แสดงเป็น merge group column
//													echo "3. sum_merge=$sum_merge, text_merge=$text_merge<br>";
													if ($this->f_head_tnum) $text_merge = convert2thaidigit($text_merge);
//													echo "1.. $iii - w:".$sum_merge." text :".$text_merge.", h=".$this->head_line_height.", tmp_border=$tmp_border, align=".$head_align.",  sum_w:".$sum_w."<br>";
													$this->MultiCellThaiCut($sum_merge, $this->head_line_height, $text_merge, $tmp_border, $head_align, 1);	//
													if($this->y > $max_y) $max_y = $this->y;
													$this->x = $start_x + $sum_w - (float)$this->arr_column_width[$this->arr_column_map[$iii]];	// (float)$this->arr_head_width[$iii];
													$this->y = $start_y;
													$sum_merge = 0;
													$arr_setborder[$iii] = (strpos($border,"R")===false ? "" : "R");	// ปิดหลังตัวสุดท้าย ถ้าใน border กำหนดให้มี R ก็มี ถ้าไม่ได้กำหนดก็ไม่ต้องมี
													$columngrp="";
													$tmp_f_mergeup=0;
													$text_merge = "";
//												} else {
//													echo "5.. $iii - w:".$sum_merge." text :".$text_merge.", h=".$this->head_line_height.", border=$tmp_border, align=".$head_align.",  sum_w:".$sum_w.", columngrp=$columngrp<br>";
												}
											} else {	// else $columngrp!=$buff_colgrp
												if ($sum_merge > 0) {
													// $f_mergeup merge กับ cell บน
													$tmp_border = ($tmp_f_mergeup==1 ? "" : (strpos($border,"T")===false ? "" : "T")); 	// ถ้า merge กับ cell บน ไม่ต้อง draw top border 
																																																	// ถ้าไม่ แต่ ไม่ได้กำหนดให้ draw top ก็ไป draw top แต่ถ้ากำหนด ก็จะ draw top	
													if ($cntline == $cnt_line_head-1) $tmp_border .= (strpos($border,"B")===false ? "" : "B");	// ถ้าเป็นบรรทัดสุดท้าย และ ถ้ากำหนด "B" ก็ให้ draw 'B'
//													echo "1. sum_merge=$sum_merge, text_merge=$text_merge<br>";
													if ($this->f_head_tnum) $text_merge = convert2thaidigit($text_merge);
//													echo "2.. $iii - w:".$sum_merge." text :".$text_merge.", h=".$this->head_line_height.", tmp_border=$tmp_border, align=".$head_align.",  sum_w:".$sum_w."<br>";
													$this->MultiCellThaiCut($sum_merge, $this->head_line_height, $text_merge, $tmp_border, $head_align, 1);
													if($this->y > $max_y) $max_y = $this->y;
													$this->x = $start_x + $sum_w - (float)$this->arr_column_width[$this->arr_column_map[$iii]];	// (float)$this->arr_head_width[$iii];
													$this->y = $start_y;
													$sum_merge = 0;
													$arr_setborder[$last_iii] = (strpos($border,"R")===false ? "" : "R");	// ปิดกลุ่ม
													$text_merge = "";
												}
												$head_align = ($this->arr_head_align[$this->arr_column_map[$iii]] ? $this->arr_head_align[$this->arr_column_map[$iii]] : "C");
//												echo ">>>$iii>>head_align=$head_align, arr_head_align [".$this->arr_column_map[$iii]."]=".$this->arr_head_align[$this->arr_column_map[$iii]]."<br>";
												$sum_merge = (float)$this->arr_column_width[$this->arr_column_map[$iii]];	//	(float)$this->arr_head_width[$iii];
//												echo "1.. text ($cntline)-->".$nline[$cntline]."<br>";
												$text_merge = substr($nline[$cntline], $c+3);
												$columngrp=$buff_colgrp;
												$tmp_f_mergeup = $f_mergeup;
												$arr_setborder[$iii] = (strpos($border,"L")===false ? "" : "L");	// เปิดกลุ่ม
											}
										} else {
											$ret = false;
										} // end if ($c!==false)
									} else {
//										echo "sum_merge=$sum_merge<br>";
										if ($sum_merge > 0) {
//											echo "2. sum_merge=$sum_merge, text_merge=$text_merge<br>";
											if ($cntline > 0)	 {
												$chk_up_merge = strpos($nline[$cntline-1],"<**");
												$tmp_border = ($chk_up_merge === false ? "" : (strpos($border,"T")===false ? "" : "T"));	// 
											} else $tmp_border =  (strpos($border,"T")===false ? "" : "T");
											if ($this->f_head_tnum) $text_merge = convert2thaidigit($text_merge);
//											echo "3.. $iii - w:".$sum_merge." text :".$text_merge.", h=".$this->head_line_height.", tmp_border=$tmp_border, align=".$head_align.",  sum_w:".$sum_w."<br>";
											$this->MultiCellThaiCut($sum_merge, $this->head_line_height, $text_merge, $tmp_border, $head_align, 1);
											if($this->y > $max_y) $max_y = $this->y;
											$this->x = $start_x + $sum_w - (float)$this->arr_column_width[$this->arr_column_map[$iii]];	//	(float)$this->arr_head_width[$iii];
											$this->y = $start_y;
											$sum_merge = 0;
											$arr_setborder[$last_iii] = (strpos($border,"R")===false ? "" : "R");	// ปิดกลุ่ม
											$tmp_f_mergeup=0;
											$text_merge = "";
										}
										$arr_setborder[$iii] = (strpos($border,"L")===false ? "" : "L").(strpos($border,"R")===false ? "" : "R");		// column นี้ไม่เป็นกลุ่ม ถ้ากำหนดให้มี L และหรือ R ก็ให้มี
										if ($cntline > 0)	 {
											$chk_up_merge = strpos($nline[$cntline-1],"<**");
											$tmp_border = ($chk_up_merge === false ? "" : (strpos($border,"T")===false ? "" : "T")); 	// ถ้า ตัวข้างบนเป็นกลุ่ม ตัวล่างไม่เป็นกลุ่ม ก็ขีดขอบบน T
										} else $tmp_border =  (strpos($border,"T")===false ? "" : "T");
										if ($cntline == $cnt_line_head-1) $tmp_border .= (strpos($border,"B")===false ? "" : "B");
//										echo "4.. $iii - w:".$this->arr_head_width[$this->arr_column_map[$iii]]." text (line:$cntline):".$nline[$cntline].", h=".$this->head_line_height.", tmp_border=$tmp_border, align=".$this->arr_head_align[$this->arr_column_map[$iii]].",  sum_w:".$sum_w."<br>";
//										$this->MultiCellThaiCut($this->arr_head_width[$iii], (float)$this->head_line_height, $nline[$cntline], $tmp_border, $this->arr_head_align[$iii],1);
										if ($this->f_head_tnum) $text_merge = convert2thaidigit($nline[$cntline]); else $text_merge = $nline[$cntline];
//										echo "2.. text ($cntline)-->".$nline[$cntline].", text_merge=$text_merge<br>";
										$this->MultiCellThaiCut($this->arr_column_width[$this->arr_column_map[$iii]], (float)$this->head_line_height, $text_merge, $tmp_border, $this->arr_head_align[$this->arr_column_map[$iii]],1);
										if($this->y > $max_y) $max_y = $this->y;
										$this->x = $start_x + $sum_w;
										$this->y = $start_y;
										$columngrp="";
									} // end if ($chk_merge!==false)
									$last_iii = $iii;
									$cnt_sel_i++;
								}	// end if ($this->arr_column_sel[$iii]==1)
							} //// end for $iii loop
							// DRAW BORDER
							$line_start_y = $start_y;			$line_end_y = $max_y;
							$line_start_x = $start_x;			$line_end_x = $start_x;
//							echo "ss....<br>";
							for($iii = 0; $iii < $cnt_column_head; $iii++) {
								if ($this->arr_column_sel[$this->arr_column_map[$iii]]==1) {	// 1 = แสดง column นี้
									//================= Draw Horicental (L)Border Line ====================
									$t_head_border = ($this->head_border[$this->arr_column_map[$iii]] ? $this->head_border[$this->arr_column_map[$iii]] : $this->head_border[0]);
//									echo "t_head_border=$t_head_border<br>";
									$have_l = strpos(strtoupper($t_head_border), "L");
									$have_r = strpos(strtoupper($t_head_border), "R");
									$have_h = strpos(strtoupper($t_head_border), "H");
									$line_start_y = $start_y;  $line_end_y = $max_y;
									$this->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ
//									if ($have_l !== false) {
									if (strpos(strtoupper($arr_setborder[$iii]),"L")!==false) {
										$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางเส้นแรก
//										echo "เส้นขวางเส้นแรก (L)->$line_start_x, $line_start_y, $line_end_x, $line_end_y<br>";
									}

									//================= Draw verticle Border Line ====================
									$line_end_x += (float)$this->arr_column_width[$this->arr_column_map[$iii]];
									$have_t = strpos(strtoupper($t_head_border), "T");
									if ($have_t !== false) {						
										$line_start_y = $start_y;	$line_end_y = $start_y;	
//										$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นนอนบน
//										echo "เส้นนอนบน (T)->$line_start_x, $line_start_y, $line_end_x, $line_end_y<br>";
									}
									$have_b = strpos(strtoupper($t_head_border), "B");						
									if ($have_b !== false) {
										$line_start_y = $max_y;	$line_end_y = $max_y;
//										$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นนอนล่าง
//										echo "เส้นนอนล่าง (B)->$line_start_x, $line_start_y, $line_end_x, $line_end_y<br>";
									}

									//================= Draw Horicental (R)Border Line ====================
									$line_start_y = $start_y;
									$line_start_x += (float)$this->arr_column_width[$this->arr_column_map[$iii]];
									if ($iii == $cnt_column_show-1 && $have_r !== false) {
										if (strpos(strtoupper($arr_setborder[$iii]),"R")!==false) { // เฉพาะ อันที่ไม่ใช้ cell merge
											$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางที่เหลือ
//											echo "เส้นขวางเส้นหลัง 1(R)->$line_start_x, $line_start_y, $line_end_x, $line_end_y<br>";
										}
									} elseif ($have_h !== false) {
										if (strpos(strtoupper($arr_setborder[$iii]),"R")!==false) { // เฉพาะ อันที่ไม่ใช้ cell mergetmp_f_mergeup
											$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางที่เหลือ
//											echo "เส้นขวางเส้นหลัง 2(R)->$line_start_x, $line_start_y, $line_end_x, $line_end_y<br>";
										}
									}
								} // end if ($this->arr_column_sel[$this->arr_column_map[$iii]]==1)
							} // end for loop $iii ครั้งที่ 2
							//====================================================
							$this->x = $start_x;			$this->y = $max_y;
							$start_y = $max_y;
						}	// end for $cntline loop
					} else {
						$ret = false;
					} // end if ($cnt_column_head > 0)
						
					$this->SetFont($this->tabdata_font_name,$this->tabdata_font_style,$this->tabdata_font_size);
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
					if ($this->tabdata_rotate_angle)	$this->Rotate((int)$this->tabdata_rotate_angle); 

				} // end if ($this->f_table==1)

				return $res;
			}	// end function print_tab_header

			function set_tab_head_title($title)
			{
				$this->tab_head_title = $title;
			}

			function add_data_tab($arr_data, $line_height=7, $border="TRHBL", $data_align, $font, $font_size="14", $font_style="", $font_color=0, $fill_color=0, $tab_PageBreakTrigger=10)
			{
				$ret = true;
				if ($this->f_table==1) {

					$this->arr_column_align = $data_align;		// align for data (data_align ในที่นี้ยังไม่ได้ปรับการ map index ดังนั้น ใน function นี้ ต้องครอบ map index)
																								//		ทำให้ เฉพาะใน function นี้ ค่า $this->arr_column_align ที่ map แล้ว จะต้องเป็น
																								//	 	ค่านี้  $this->arr_column_align[$this->arr_column_map[$iii]]

					if (!$font) $font=$this->FontFamily;
//					if (!$font_style) $font_style=$this->FontStyle;	// ถ้าไม่มีค่าอาจหมายถึง ไม่ bold ไม่ italic ไม่ underline ไม่ได้หมายถึง ไม่มีค่า ดังนั้นไม่ต้อง move ค่า default
					if (!$font_size) $font_size=$this->FontSizePt;
//					echo "font=".$font." -- ".implode("|",$arr_data)."<br>";

					if (gettype($fill_color)=="array")		$this->arr_tabdata_fill_color = $fill_color;		else	{	$this->arr_tabdata_fill_color = (array) null;	 	$this->arr_tabdata_fill_color[] = $fill_color; 	}
					if (gettype($font)=="array")				$this->arr_tabdata_font_name = $font;			else	{	$this->arr_tabdata_font_name = (array) null; $this->arr_tabdata_font_name[] = $font; 	}
					if (gettype($font_size)=="array")	$this->arr_tabdata_font_size = $font_size;		else	{	$this->arr_tabdata_font_size = (array) null;	$this->arr_tabdata_font_size[] = $font_size; 	}
					if (gettype($font_style)=="array")	$this->arr_tabdata_font_style = $font_style;	else	{	$this->arr_tabdata_font_style = (array) null;	$this->arr_tabdata_font_style[] = $font_style; 	}
					if (gettype($font_color)=="array")	$this->arr_tabdata_font_color = $font_color;	else	{	$this->arr_tabdata_font_color = (array) null;	$this->arr_tabdata_font_color[] = $font_color; 	}
					if (gettype($border)=="array")		$this->arr_tabdata_border = $border;				else	{	$this->arr_tabdata_border = (array) null;		$this->arr_tabdata_border[] = $border; 	}

//					echo "arr data=".implode(",",$arr_data).", font_style=".implode(",",$this->arr_tabdata_font_style)."<br>";
					
					$d_align = "C";
					$max_line = 0;
					$arr_sub_data = (array) null;
					$arr_merge_grp = (array) null;
					$arr_msum_grp = (array) null;
					
					$cnt_column_head = count($this->arr_tab_head);
					for($iii = 0; $iii < $cnt_column_head; $iii++) 	$arr_msum_grp[]=0;

					$cnt_column = count($arr_data);
					$cnt_column_show = 0;
					for($jjj = 0; $jjj <= count($this->arr_tab_head); $jjj++)  if ($this->arr_column_sel[$this->arr_column_map[$jjj]]==1) $cnt_column_show++;	
					
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

					$col_aggreg = $this->do_aggregate($arr_data);
					$arr_column_aggreg = explode("|", $col_aggreg);
//					echo ">>".implode(",",$arr_data).">>$col_aggreg (cnt_column_head=$cnt_column_head)<br>";
//					echo "col_aggreg=$col_aggreg<br>";

					$grpidx = "";
					$sum_merge = 0;
					$arr_text = (array) null;
					$arr_col_width = (array) null;
					$arr_col_align = (array) null;
					$arr_fontname = (array) null;
					$arr_fontstyle = (array) null;
					$arr_fontrotate = (array) null;
					$arr_fontsize = (array) null;
					$arr_fontcolor = (array) null;
					$arr_fborder = (array) null;
					$arr_ffill = (array) null;
					$cnt=0;
					$img_idx = 0;
					for($iii = 0; $iii < $cnt_column_head; $iii++) {
//						echo "column loop iii=$iii<br>";
						if ($this->arr_column_sel[$this->arr_column_map[$iii]]==1) {	// 1 = แสดง column นี้
							$nline = $arr_data[$this->arr_column_map[$iii]];
//							echo "aggregate at $iii-(".$this->arr_column_map[$iii].")->$nline==aggreg->".$arr_column_aggreg[$iii]."<br>";
							if (trim($arr_column_aggreg[$iii])) {	// agregate จาก do_agregate map มา เรียบร้อยแล้ว
								$nline = $arr_column_aggreg[$iii];
//								echo "aggregate at $iii-(".$this->arr_column_map[$iii].")->$nline==aggreg->".$arr_column_aggreg[$iii]."<br>";
							}

							$this->tabdata_font_name	= ($this->arr_tabdata_font_name[$this->arr_column_map[$iii]]	? $this->arr_tabdata_font_name[$this->arr_column_map[$iii]]	: $this->arr_tabdata_font_name[0]);
//							$this->tabdata_font_style	= ($this->arr_tabdata_font_style[$this->arr_column_map[$iii]]		? $this->arr_tabdata_font_style[$this->arr_column_map[$iii]]	: "");
							$buff = ($this->arr_tabdata_font_style[$this->arr_column_map[$iii]]		? $this->arr_tabdata_font_style[$this->arr_column_map[$iii]]	: "");
							$this->tabdata_font_style = "";
							if (strpos(strtolower($buff),"b")!==false) {
								$this->tabdata_font_style .= "b";
								$buff = str_replace("b","",$buff);
								$buff = str_replace("B","",$buff);
							}
							if (strpos(strtolower($buff),"U")!==false) {
								$this->tabdata_font_style .= "u";
								$buff = str_replace("u","",$buff);
								$buff = str_replace("U","",$buff);
							}
							if (strpos(strtolower($buff),"I")!==false) {
								$this->tabdata_font_style .= "i";
								$buff = str_replace("i","",$buff);
								$buff = str_replace("I","",$buff);
							}
							if (strpos(strtolower($buff),"r")!==false) {
								$buff = str_replace("r","",$buff);
								$buff = str_replace("R","",$buff);
								$this->tabdata_rotate_angle = ($buff ? $buff : "90");
							} else {
								$this->tabdata_rotate_angle = "";
							}
							if ($this->tabdata_rotate_angle)	$this->Rotate((int)$this->tabdata_rotate_angle);  else $this->Rotate(0);
							$this->tabdata_font_size	= ($this->arr_tabdata_font_size[$this->arr_column_map[$iii]]		? $this->arr_tabdata_font_size[$this->arr_column_map[$iii]]		: $this->arr_tabdata_font_size[0]);
							$this->tabdata_font_color	= ($this->arr_tabdata_font_color[$this->arr_column_map[$iii]]		? $this->arr_tabdata_font_color[$this->arr_column_map[$iii]]	: $this->arr_tabdata_font_color[0]);
							$this->tabdata_border		= ($this->arr_tabdata_border[$this->arr_column_map[$iii]]			? $this->arr_tabdata_border[$this->arr_column_map[$iii]]			: (count($this->arr_tabdata_border) > 1 ? "" : $this->arr_tabdata_border[0]));
							$this->tabdata_fill_color		= ($this->arr_tabdata_fill_color[$this->arr_column_map[$iii]]		? $this->arr_tabdata_fill_color[$this->arr_column_map[$iii]]		: $this->arr_tabdata_fill_color[0]);

							$f_fill = 0;
							if (strlen($this->tabdata_fill_color)==6) {
								$f_fill = 1;
							}

							$border = $this->tabdata_border;
//							echo "$iii-".$nline."|<br>";
							$chk_merge = strpos($nline,"<**");
							if ($chk_merge!==false) {
								$c = strpos($nline,"**>");
								if ($c!==false) {
									$mgrp = substr($nline, $chk_merge+3, $c-($chk_merge+3));
//									echo "[$nline]..group>>".$mgrp."==".$grpidx."<br>"; 
									if ($mgrp != $grpidx) {	// มีการเปลี่ยนกลุ่ม merge column ติดกัน
										if ($grpidx) $arr_merge_grp[$last_iii] = "end";
										$grpidx = $mgrp;
										$arr_merge_grp[$iii] = $grpidx;
//										echo "1..iii=$iii -> merge(".$arr_merge_grp[$iii].") , last_iii=$last_iii -> merge(".$arr_merge_grp[$last_iii].")<br>";
										$arr_msum_grp[$grpidx]=(float)$this->arr_column_width[$this->arr_column_map[$iii]];		//	(float)$this->arr_head_width[$iii];
										$sum_merge=(float)$this->arr_column_width[$this->arr_column_map[$iii]];		//	(float)$this->arr_head_width[$iii];
									} else {	// กลุ่มเป็นกลุ่มเดียวกัน
										$arr_merge_grp[$iii] = $grpidx;
//										echo "2..iii=$iii -> merge(".$arr_merge_grp[$iii].")<br>";
										$arr_msum_grp[$grpidx]+=(float)$this->arr_column_width[$this->arr_column_map[$iii]];	//	(float)$this->arr_head_width[$iii];
										$sum_merge+=(float)$this->arr_column_width[$this->arr_column_map[$iii]];	//	(float)$this->arr_head_width[$iii];
									}
									$ntext = substr($nline, 0, $chk_merge)." ".substr($nline, $c+3);
								} else {	// รูปแบบผิดพลาด ตัวปิดกลุ่มไม่มี
									$ret = false;
								}
							} else {	// ถ้า column นี้ไม่เป็น กลุ่ม
								if ($grpidx)  $arr_merge_grp[$last_iii] = "end";	// ปิดกลุ่มสุดท้าย
								$arr_merge_grp[$iii] = "";
//								echo "3..iii=$iii -> merge(".$arr_merge_grp[$iii].") , last_iii=$last_iii -> merge(".$arr_merge_grp[$last_iii].")<br>";
								$ntext = $nline;
								$grpidx = "";
							} // end if ($chk_merge!==false)
//							if ($arr_merge_grp) echo "grp:".implode(",",$arr_merge_grp)."<br>";
//							echo "bf row-->$ntext<br>";
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
//							if ($arr_merge_rows) echo "row:".implode(",",$arr_merge_rows)."<br>";
//							echo "bf img-->$ntext<br>";
							$chk_image = strpos($ntext,"<*img*");
							if ($chk_image!==false) {
									$c = strpos($ntext,"*img*>");
									if ($c!==false) {
										$ntext1 = $arr_data[$this->arr_column_map[$iii]];
										$c1 = strpos($ntext1,"<*img*");
										$c2 = strpos($ntext1,"*img*>");
										$arr_rows_image[$img_idx] = substr($ntext1, $c1+6, $c2-($c1+6));
//										$arr_b = $this->MultiCellThaiCut($arr_msum_grp[$grpidx], (float)$line_height, $arr_text[$iii], $border, $data_align[$iii],$f_fill, false);
										$ntext = substr($ntext, 0, $chk_image)."~".substr($ntext, $c+6);
//										echo "img..$ntext ,$chk_image, $c ($ntext1) arr_rows-image [$iii]=".$arr_rows_image[$iii]."<br>";
//										$arr_b = $this->MultiCellThaiCut($arr_msum_grp[$grpidx], (float)$line_height, $arr_text[$iii], $border, $arr_column_align[$this->arr_column_map[$iii]],$f_fill, false);
										$arr_b = $this->MultiCellThaiCut($arr_msum_grp[$grpidx], (float)$line_height, $ntext, $border, $arr_column_align[$this->arr_column_map[$iii]],$f_fill, false);
//										echo "$iii - image name=".$arr_rows_image[$iii].", text=$ntext , arr_text=".$arr_text[$iii]."<br>";
									} else {
										$ret = false;
									}
							} else {
									$arr_rows_image[$iii] = "";
							}
							$arr_text[] = $ntext;
							$arr_col_width[] = $this->arr_column_width[$this->arr_column_map[$iii]];
							$arr_col_align[] = $this->arr_column_align[$this->arr_column_map[$iii]];
							$arr_fontname[] = $this->tabdata_font_name;
							$arr_fontstyle[] = $this->tabdata_font_style;
							$arr_fontrotate[] = $this->tabdata_rotate_angle;
							$arr_fontsize[] = $this->tabdata_font_size;
							$arr_fontcolor[] = $this->tabdata_font_color;
							$arr_fborder[] = $this->tabdata_border;
							$arr_ffill[] = $this->tabdata_fill_color	;

							$cnt++;
							$img_idx++;
//							echo "just show ($cnt)-$ntext | ".$this->arr_column_width[$iii]." | ".$this->arr_column_align[$this->arr_column_map[$iii]]." | ".$this->arr_column_align[$this->arr_column_map[$iii]]."<br>";
							$last_iii = $iii;
						} // if ($this->arr_column_sel[$iii]==1) {	// 1 = แสดง column นี้
					} // end for $iii (แต่ละ column)
					if ($ret && strlen($arr_merge_grp[$iii-1]) > 0) $arr_merge_grp[$iii-1] = "end"; // ตรวจสอบกรณี merge column ตัวสุดท้ายที่สุดพอดี
					
					$have_img = 0;
					if ($ret) {
						$grpidx = "";
						for($iii = 0; $iii < $cnt_column_show; $iii++) {	// เลือกเฉพาะรายการที่ แสดง
							$this->tabdata_font_name	= $arr_fontname[$iii];
							$this->tabdata_font_style	= $arr_fontstyle[$iii];
							$this->tabdata_rotate_angle = $arr_fontrotate[$iii];
							$this->tabdata_font_size	= $arr_fontsize[$iii];
							$this->tabdata_font_color	= $arr_fontcolor[$iii];
							$this->tabdata_border		= $arr_fborder[$iii];
							$this->tabdata_fill_color		= $arr_ffill[$iii];

							if ($this->tabdata_rotate_angle)	$this->Rotate((int)$this->tabdata_rotate_angle);  else $this->Rotate(0);

							$this->SetFont($this->tabdata_font_name, $this->tabdata_font_style, $this->tabdata_font_size);
		
							$f_fill = 0;
							if (strlen($this->tabdata_fill_color)==6) {
								$f_fill = 1;
							}

							$border = $this->tabdata_border;
							
//							echo "arr_text [$iii]=".$arr_text[$iii]."<br>";
							if ($arr_merge_grp[$iii] == "end") {
//							 	$arr_b = $this->MultiCellThaiCut($arr_msum_grp[$grpidx], (float)$line_height, $arr_text[$iii], $border, $data_align[$iii],$f_fill,false);
//								echo "end grp ($grpidx)-->$iii w:".$arr_msum_grp[$grpidx]." text:".$arr_text[$iii]." border:".$border." align:".$arr_col_align[$iii]."<br>";
							 	$arr_b = $this->MultiCellThaiCut($arr_msum_grp[$grpidx], (float)$line_height, $arr_text[$iii], $border, $arr_col_align[$iii],$f_fill,false);
							} elseif ($arr_merge_grp[$iii]) {
								$grpidx = $arr_merge_grp[$iii];
//							 	$arr_b = $this->MultiCellThaiCut($arr_msum_grp[$grpidx], (float)$line_height, $arr_text[$iii], $border, $data_align[$iii],$f_fill,false);
//								echo "grp-($grpidx)->$iii w:".$arr_msum_grp[$grpidx]." text:".$arr_text[$iii]." border:".$border." align:".$arr_col_align[$iii]."<br>";
							 	$arr_b = $this->MultiCellThaiCut($arr_msum_grp[$grpidx], (float)$line_height, $arr_text[$iii], $border, $arr_col_align[$iii],$f_fill,false);
							} else {
								$grpidx="";
//								$arr_b = $this->MultiCellThaiCut($this->arr_head_width[$iii], (float)$line_height, $arr_text[$iii], $border, $data_align[$iii],$f_fill,false);
//								echo "not grp->$iii w:".$arr_col_width[$iii]." text:".$arr_text[$iii]." border:".$border." align:".$arr_col_align[$iii]."<br>";
								$arr_b = $this->MultiCellThaiCut($arr_col_width[$iii], (float)$line_height, $arr_text[$iii], $border, $arr_col_align[$iii],$f_fill,false);
							}
							
						 	$arr_sub_data[] = $arr_b;
//							echo "$iii - ".$arr_merge_grp[$iii]." sub_data:".implode(",", $arr_sub_data[$iii])."<br>";
							$this_line = count($arr_sub_data[$iii]);
							if ($arr_rows_image[$iii]) {	// กรณีที่ ส่งค่า จำนวน บรรทัด มากับ image ด้วย
								$img = explode(",",$arr_rows_image[$iii]);
								if ($img[1]) $this_line = (int)$img[1];
								$have_img = 1;
							}
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
						$start_x = $this->x;			$start_y = $this->y;				$max_y = $this->y;
						$first_y = $this->y;
					
						$columngrp="";

						$sum_merge = 0;
						$text_merge = "";
						$d_align = "";

						if ($have_img && ($this->y + ($max_line * $line_height)) > ($this->h - $tab_PageBreakTrigger)) {	// check line รวม
							$old_y = $this->y;
							$this->y += ($max_line * $line_height);	// ส่ง $this->y และค่า $old_y ไปเพื่อ check หน้าใหม่ nohead = false
							$this->tab_pagebreak(strtoupper($border), $tab_PageBreakTrigger, false, $old_y);
//							echo "have_img=$have_img<br>";
						}
						for($cntline = 0; $cntline < $max_line; $cntline++) {
							// check หน้าให้ก่อนพิมพ์บรรทัดต่อไป
							$this->tab_pagebreak(strtoupper($border), $tab_PageBreakTrigger);

							$sum_merge = 0;
							for($iii = 0; $iii < $cnt_column_show; $iii++) {
								$this->tabdata_font_name	= $arr_fontname[$iii];
								$this->tabdata_font_style	= $arr_fontstyle[$iii];
								$this->tabdata_rotate_angle = $arr_fontrotate[$iii];
								$this->tabdata_font_size	= $arr_fontsize[$iii];
								$this->tabdata_font_color	= $arr_fontcolor[$iii];
								$this->tabdata_border		= $arr_fborder[$iii];
								$this->tabdata_fill_color		= $arr_ffill[$iii];

								if ($this->tabdata_rotate_angle)	$this->Rotate((int)$this->tabdata_rotate_angle);  else $this->Rotate(0);

								$this->SetFont($this->tabdata_font_name, $this->tabdata_font_style, $this->tabdata_font_size);
								if (strlen($this->tabdata_font_color)==6) {
									$r=substr($this->tabdata_font_color,0,2);
									$g=substr($this->tabdata_font_color,2,2);
									$b=substr($this->tabdata_font_color,4,2);
								} else { $r="00"; $g="00"; $b="00"; }
	//							echo "font:$r $g $b<br>";
								$this->SetTextColor(hexdec("$r"),hexdec("$g"),hexdec("$b"));
			
								$f_fill = 0;
								if (strlen($this->tabdata_fill_color)==6) {
									$r=substr($this->tabdata_fill_color,0,2);
									$g=substr($this->tabdata_fill_color,2,2);
									$b=substr($this->tabdata_fill_color,4,2);
									$this->SetFillColor(hexdec("$r"),hexdec("$g"),hexdec("$b"));
	//								echo "fill:$r $g $b<br>";
									$f_fill = 1;
								}
	
								$border = $this->tabdata_border;

								$nline = $arr_sub_data[$iii][$cntline];
								$sum_w = 0;
								for($jjj = 0; $jjj <= $iii; $jjj++)  $sum_w+=(float)$arr_col_width[$jjj];		// 	(float)$this->arr_head_width[$jjj];

//								echo "$iii - max_line=$max_line, cntline=$cntline, border=$t_border, merge:".$arr_merge_rows[$iii]."<br>";
								$t_border = strtoupper($border);
								$mid_hline = (strpos($t_border, "H")!==false ? true : false);  // H แทนเส้นขวางกลาง ถ้ามีแปลว่า ขึดเส้นขวางตรงกลาง
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
//									echo "arr_merge_grp [$iii]=".$arr_merge_grp[$iii]."<br>";
									if ($iii > 0) {
//										echo "sum_merge=$sum_merge , grp=".$arr_merge_grp[$iii]."<br>";
										if ($sum_merge > 0 && strpos($arr_merge_grp[$iii], "end") !== false) { // ถ้า จบ merge group
											$sum_merge+=(float)$arr_col_width[$iii];	//  (float)$this->arr_head_width[$iii];
//											echo "1) $iii-grp:".$arr_merge_grp[$iii]." text_merge=$text_merge sum_merge=$sum_merge (".$arr_col_width[$iii].")<br>";
											if (!$mid_hline) { // ถ้าไม่กำหนดค่า H ใน border ก็จะไม่ลากเส้นขวาง
												if ($mergecell_first != 0) {  // ถ้า merge cell ไม่ได้เริ่มตั้งแต่ cell แรก เอา L ออก แต่เป็น cell สุดท้าย เหลือ R
													$t_border = str_replace("L", "", $t_border);
												}
											}
//											echo "1..arr_rows_image [$iii]=".$arr_rows_image[$iii].", next_line=$next_line [$nline]<br>";
											if ($arr_merge_rows[$iii]=="endmerge") {
												if ($arr_rows_image[$iii]) {
//													echo "1..$iii..".$arr_rows_image[$iii].", text=".$text_merge."<br>";
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
										} else if (strpos($arr_merge_grp[$iii], "end") !== false) { // ถ้า จบ merge group
											$sum_merge+=(float)$arr_col_width[$iii];	//  (float)$this->arr_head_width[$iii];
//											echo "1.1) $iii-grp:".$arr_merge_grp[$iii]." nline=$nline sum_merge=$sum_merge (".$arr_col_width[$iii].")<br>";
											if (!$mid_hline) { // ถ้าไม่กำหนดค่า H ใน border ก็จะไม่ลากเส้นขวาง
												if ($mergecell_first != 0) {  // ถ้า merge cell ไม่ได้เริ่มตั้งแต่ cell แรก เอา L ออก แต่เป็น cell สุดท้าย เหลือ R
													$t_border = str_replace("L", "", $t_border);
												}
											}
//											echo "1..arr_rows_image [$iii]=".$arr_rows_image[$iii].", next_line=$next_line [$nline]<br>";
											if ($arr_merge_rows[$iii]=="endmerge") {
												if ($arr_rows_image[$iii]) {
//													echo "1..$iii..".$arr_rows_image[$iii].", text=".$text_merge."<br>";
													$this->print_column_image($sum_merge, $merge_rows_h, $nline, $line_height, $arr_rows_image[$iii], $t_border, $d_align, $f_fill, $next_line);
													$arr_rows_image[$iii] = "";
												} else { // ถ้า เป็น text ล้วน ๆ
													$this->Cell($sum_merge, $line_height, $nline, $t_border, $next_line, $d_align, $f_fill);
												} // end if ($arr_rows_image[$iii])
												$arr_merge_rows[$iii]="";
												$arr_rows_image[$iii]="";
												$this->merge_rows_h[$iii] = 0;
												$merge_rows_h = 0;
											} else	// ถ้า ไม่ end merge row
												if (strlen(trim($arr_merge_rows[$iii])) > 0)	// ถ้ายังอยู่ใน merge row
													$this->Cell($sum_merge, $line_height, $nline, $t_border, $next_line, $d_align, $f_fill);
//													$this->Cell($sum_merge, $line_height, "", $t_border, $next_line, $d_align, $f_fill);
												else
													$this->Cell($sum_merge, $line_height, $nline, $t_border, $next_line, $d_align, $f_fill);
//											echo "sum_merge=$sum_merge, line_height=$line_height, nline=$nline<br>";
											$sum_merge = 0;
											$text_merge = "";
										} else {	// ถ้ายังอยู่ใน merge group แต่ยังไม่จบ merge group
											if  ($sum_merge == 0) { // ถ้าเป็น merge group ใหม่
												$d_align = ($arr_col_align[$iii] ? $arr_col_align[$iii] : "C");
												$mergecell_first = $iii;
												if (!$text_merge) $text_merge = $nline;
											}
											$sum_merge+=(float)$arr_col_width[$iii];	//	(float)$this->arr_head_width[$iii];
//											echo "2) $iii-grp:".$arr_merge_grp[$iii]." text_merge=$text_merge sum_merge=$sum_merge (".$arr_col_width[$iii].")<br>";
											if (strlen(trim($arr_merge_rows[$iii])) > 0) // ถ้าเป็น merge row แรก
												$merge_rows_h = $this->merge_rows_h[$iii];
										} //  end if ($sum_merge > 0 && strpos($arr_merge_grp[$iii], "end") !== false)
									} else { // $iii == 0 เป็น merge cell  แรก
										if (strpos($arr_merge_grp[$iii], "end") !== false) { // ถ้า เป็น merge cell แรกที่ end เลย
											$sum_merge = (float)$arr_col_width[$iii];	//  (float)$this->arr_head_width[$iii];
//											echo "3.1) $iii-grp:".$arr_merge_grp[$iii]." nline=$nline sum_merge=$sum_merge (".$arr_col_width[$iii].")<br>";
											if (!$mid_hline) { // ถ้าไม่กำหนดค่า H ใน border ก็จะไม่ลากเส้นขวาง
												if ($mergecell_first != 0) {  // ถ้า merge cell ไม่ได้เริ่มตั้งแต่ cell แรก เอา L ออก แต่เป็น cell สุดท้าย เหลือ R
													$t_border = str_replace("L", "", $t_border);
												}
											}
//											echo "1..arr_rows_image [$iii]=".$arr_rows_image[$iii].", next_line=$next_line [$nline]<br>";
											if ($arr_merge_rows[$iii]=="endmerge") {
												if ($arr_rows_image[$iii]) {
//													echo "1..$iii..".$arr_rows_image[$iii].", text=".$text_merge."<br>";
													$this->print_column_image($sum_merge, $merge_rows_h, $nline, $line_height, $arr_rows_image[$iii], $t_border, $d_align, $f_fill, $next_line);
													$arr_rows_image[$iii] = "";
												} else { // ถ้า เป็น text ล้วน ๆ
													$this->Cell($sum_merge, $line_height, $nline, $t_border, $next_line, $d_align, $f_fill);
												} // end if ($arr_rows_image[$iii])
												$arr_merge_rows[$iii]="";
												$arr_rows_image[$iii]="";
												$this->merge_rows_h[$iii] = 0;
												$merge_rows_h = 0;
											} else	// ถ้า ไม่ end merge row
												if (strlen(trim($arr_merge_rows[$iii])) > 0)	// ถ้ายังอยู่ใน merge row
													$this->Cell($sum_merge, $line_height, $nline, $t_border, $next_line, $d_align, $f_fill);
//													$this->Cell($sum_merge, $line_height, "", $t_border, $next_line, $d_align, $f_fill);
												else
													$this->Cell($sum_merge, $line_height, $nline, $t_border, $next_line, $d_align, $f_fill);
//											echo "sum_merge=$sum_merge, line_height=$line_height, nline=$nline<br>";
											$sum_merge = 0;
											$text_merge = "";
										} else {
											// ถ้าเป็น merge cell ตั้งแต่อันแรก กำหนดค่า merge แรก
											$sum_merge+=(float)$arr_col_width[$iii];	//	(float)$this->arr_head_width[$iii];
//											$d_align = ($data_align[$iii] ? $data_align[$iii] : "C");
											$d_align = ($arr_col_align[$iii] ? $arr_col_align[$iii] : "C");
											if (!$text_merge) $text_merge = $nline;
											$mergecell_first = $iii;
											if (strlen(trim($arr_merge_rows[$iii])) > 0) // ถ้าเป็น merge row แรก
												$merge_rows_h = $this->merge_rows_h[$iii];
	//										echo "3) $iii-grp:".$arr_merge_grp[$iii]." text_merge=$text_merge sum_merge=$sum_merge (".$arr_col_width[$iii].")<br>";
										} // if (strpos($arr_merge_grp[$iii], "end") !== false)
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
//									echo "2..arr_rows_image [$iii]=".$arr_rows_image[$iii].", next_line=$next_line [$nline]<br>";
									if ($arr_merge_rows[$iii]=="endmerge") {
										if ($arr_rows_image[$iii]) {
//											$this->print_column_image($col_w, $this->merge_rows_h[$iii], $nline, $line_height, $arr_rows_image[$iii], $t_border, $data_align[$iii], $f_fill, $next_line);
//											echo "2..$iii..".$arr_rows_image[$iii].", text=$nline<br>";
											$this->print_column_image($col_w, $this->merge_rows_h[$iii], $nline, $line_height, $arr_rows_image[$iii], $t_border, $arr_col_align[$iii], $f_fill, $next_line);
											$arr_rows_image[$iii] = "";
										} else {	// ถ้าไม่มีรูป
											$this->Cell($col_w, $line_height, $nline, $t_border, $next_line, $d_align, $f_fill);
										}
										$arr_merge_rows[$iii]="";
										$arr_rows_image[$iii]="";
										$this->merge_rows_h[$iii] = 0;
//										echo "merge $iii-".$arr_merge_rows[$iii]." next_line=$next_line [$nline] x:y=".$this->x.":".$this->y."<br>";
//										echo "4) $iii-grp:".$arr_merge_grp[$iii]." text_merge=$text_merge sum_merge=$sum_merge (".$arr_col_width[$iii].")<br>";
									} else {  // ถ้าไม่เป็น endmerge
//										if (!$arr_merge_rows[$iii]) echo "1..next_line=$next_line [$nline] x:y=".$this->x.":".$this->y." h=$line_height<br>";
//										echo "3..arr_rows_image [$iii]=".$arr_rows_image[$iii].", next_line=$next_line [$nline]<br>";
										if (strlen(trim($arr_merge_rows[$iii])) > 0) {
//											$this->Cell($col_w, $line_height, "", $t_border, $next_line, $data_align[$iii], $f_fill);
											$this->Cell($col_w, $line_height, "", $t_border, $next_line, $arr_col_align[$iii], $f_fill);
//											echo "merge row - $iii - x:y=".$this->x.":".$this->y." - line_h=$line_height<br>";
										} else { // ถ้าไม่ใช้การ merge row
											if ($arr_rows_image[$iii]) { // ถ้ามีรูป
//												$this->print_column_image($col_w, $line_height, $nline, $line_height, $arr_rows_image[$iii], $t_border, $data_align[$iii], $f_fill, $next_line);
//												$this->print_column_image($col_w, $line_height, $nline, $line_height, $arr_rows_image[$iii], $t_border, $arr_col_align[$iii], $f_fill, $next_line);
												$f_one_line = 1;
												$img = explode(",",$arr_rows_image[$iii]);
//												echo "1..x::".$this->x.",y::".$this->y."<br>";
												if ($img[1]=="fixsize") {
													$f_one_line = 2;
												} else {
													if ((int)$img[1] > $max_line) $max_line = (int)$img[1]; 	// ถ้ากำหนด max_line ก็คือตัวเลขหลัง comma <*img*name,c*img*> c คือ max_line
												}
//												if (strpos($arr_rows_image[$iii],"3100600640837")!==false) 
//												if (strpos($arr_rows_image[$iii],"3101400135641")!==false)  
//													echo "3..$iii..".$arr_rows_image[$iii].",  text=$nline, h=line_height($line_height) * max_line($max_line) (f_one_line=$f_one_line)<br>";
												$keep_y = $this->y;
												$this->print_column_image($col_w, $line_height*$max_line, $nline, $line_height, $img[0], $t_border, $arr_col_align[$iii], $f_fill, $next_line, $f_one_line);
												if ($this->y > $keep_y)
													$this->y = $keep_y+$line_height;	// ถ้าตำแหน่ง y เพิ่มขึ้น ตำแหน่งเพิ่ม 1 บรรทัด
//												echo "2..x::".$this->x.",y::".$this->y." (".($this->y-$keep_y).")<br>";
												$arr_rows_image[$iii] = "";
											} else { // ถ้าไม่มีรูป
//												$this->Cell($col_w, $line_height, $nline, $t_border, $next_line, $data_align[$iii], $f_fill);
												$this->Cell($col_w, $line_height, $nline, $t_border, $next_line, $arr_col_align[$iii], $f_fill);
//												echo "4..$iii..$nline, $t_border, $d_align (".$arr_col_align[$iii].")<br>";
//												if (!$arr_merge_rows[$iii]) echo "$iii..next_line=$next_line [$nline] x:y=".$this->x.":".$this->y." h=$line_height,  col_w=$col_w<br>";
											} // end if ($arr_rows_image[$iii])
										} // end if (strlen(trim($arr_merge_rows[$iii]))
//										echo "5) $iii-grp:".$arr_merge_grp[$iii]." text_merge=$text_merge sum_merge=$sum_merge (".$arr_col_width[$iii].")<br>";
									} // if ($arr_merge_rows[$iii]=="endmerge")
								} // end if ($chk_merge!==false)
							} //// end for $iii loop
						} // end for $cntline
					} // end if ($ret)
					$this->merge_rows = implode(",", $arr_merge_rows);
					$this->rows_image = implode(",", $arr_rows_image);
				} // end if ($this->f_table==1)
				return $ret;
			}	// end function add_data_tab

			function add_text_line($text, $line_height=7, $border="TRHBL", $align, $font, $font_size="14", $font_style="", $font_color=0, $fill_color=0, $tab_PageBreakTrigger=10)
			{
				$ret = true;
				if ($this->f_table==1) {

					if (!$font) $font=$this->FontFamily;
//					if (!$font_style) $font_style=$this->FontStyle;	// ถ้าไม่มีค่าอาจหมายถึง ไม่ bold ไม่ italic ไม่ underline ไม่ได้หมายถึง ไม่มีค่า ดังนั้นไม่ต้อง move ค่า default
					if (!$font_size) $font_size=$this->FontSizePt;
	//				echo "font=".$font."<br>";
	
					$this->tabdata_fill_color = $fill_color;
					$this->tabdata_font_name = $font;
					$this->tabdata_font_size = $font_size;
					$this->tabdata_font_style = $font_style;
					$this->tabdata_font_color = $font_color;

					// $align ในที่นี้ เนื่องจากสั่งพิมพ์ text ตัวเดียว จึงส่ง align มาค่าเดียว
					$d_align = $align;
					$this->SetFont($font,$font_style,$font_size);
					if (strlen($font_color)==6) {
						$r=substr($font_color,0,2);
						$g=substr($font_color,2,2);
						$b=substr($font_color,4,2);
					} else { $r="00"; $g="00"; $b="00"; }
//					echo "font:$r $g $b<br>";
					$this->SetTextColor(hexdec("$r"),hexdec("$g"),hexdec("$b"));

					$f_fill = 0;
					if (strlen($fill_color)==6) {
						$r=substr($fill_color,0,2);
						$g=substr($fill_color,2,2);
						$b=substr($fill_color,4,2);
						$this->SetFillColor(hexdec("$r"),hexdec("$g"),hexdec("$b"));
//						echo "fill:$r $g $b<br>";
						$f_fill = 1;
					}

					$cnt_column_head = count($this->arr_tab_head);
					$cnt_column_show = 0;
					$sum_w = 0;
					for($jjj = 0; $jjj < $cnt_column_head; $jjj++) {
						if ($this->arr_column_sel[$this->arr_column_map[$jjj]]==1) { $cnt_column_show++;	 $sum_w += $this->arr_column_width[$this->arr_column_map[$jjj]]; }
					}

				 	$arr_b = $this->MultiCellThaiCut($sum_w, (float)$line_height, $text, $border, $d_align,$f_fill,false);

					$next_line = 1; 
					for($iii = 0; $iii < count($arr_b); $iii++) {
						if ($arr_b[$iii]) {
							$this->tab_pagebreak(strtoupper($border), $tab_PageBreakTrigger);
							if ($this->f_head_tnum) $realtext = convert2thaidigit($arr_b[$iii]); // ถ้ามีการทำ TNUM  เปลี่ยนค่าให้เป็น ตัวเลขไทยก่อนตัดคำ
							else $realtext = $arr_b[$iii];
							$this->Cell($sum_w, $line_height, $realtext, $border, $next_line, $d_align, $f_fill);
						}
					}
				} // end if ($this->f_table==1)
				return $ret;
			}	// end function add_text_line

			function print_column_image0($w, $h, $text, $text_h, $imgfile, $border, $align, $f_fill, $next_line) {
					$d_align = ($align ? $align : "C");
					$save_y = $this->y;  $save_x = $this->x;  
//					echo "begin $save_x:$save_y, h=$h, text_h=$text_h, imgfile=$imgfile<br>";
					$this->Cell($w, $text_h, "", $border, $next_line, $align, $f_fill);
					$this->y = $save_y - $h + $text_h;
					$this->x = $save_x;
					$arr_buff = explode("~", $text);
					if (strlen(trim($arr_buff[0])) > 0) {
						$this->Cell($w, $text_h, $arr_buff[0], $border, 1, $align, $f_fill);
						$this->x = $save_x;  
					}
//					echo "image ($imgfile) exist : [".file_exists($imgfile)."]<br>";
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
//						echo "0..x:y-".$this->x.":".$this->y." w:h-$width:$height, $image_w:$image_h |".$this->merge_rows_h[$iii]." (w=$w:h=$h) (posx:$posx, posy:$posy)<br>";
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

			function print_column_image($w, $h, $text, $text_h, $imgfile, $border, $align, $f_fill, $next_line, $f_one_line=0) {
					$pix_border_w = $w;
					$d_align = ($align ? $align : "C");
					$save_y = $this->y;  $save_x = $this->x;  
//					echo "begin $save_x:$save_y, h=$h, text_h=$text_h, imgfile=$imgfile (x=".$this->x." , y=".$this->y.")<br>";
					$this->Cell($w, $text_h, "", $border, $next_line, $align, $f_fill);
//					echo "0.................x:y-".$this->x.":".$this->y." , save_y=$save_y , h=$h , text_h=$text_h ($text)<br>";
					if ($f_one_line==2) {
						$img_h = $text_h;	// ถ้าเป็นประเภท one line แบบที่ 2 ให้ image มีขนาด แค่ บรรทัดเดียว
					} else {
						$img_h = $h;		// ถ้าไม่ one line ความสูงของภาพ ขยาย ตาม ความสูงของ column
					}
//					if ($h > $text_h) $f_one_line = 0;
					
					if (!$f_one_line) {
						$this->y = $save_y - $h + $text_h;
//						echo "a...........$save_y,  $h, $text_h (".$this->y.")<br>";
//					} else if ($f_one_line==2) {
//						$this->y = $save_y - $text_h;
//						echo "b...........$save_y,  $h, $text_h (".$this->y.")<br>";
					} else {
						$this->y = $save_y;
//						echo "c...........$save_y,  $h, $text_h (".$this->y.")<br>";
					}
//					$this->y = $save_y;
					$this->x = $save_x;
					$arr_buff = explode("~", $text);
					$line_minus = 0;
					if (strlen(trim($arr_buff[0])) > 0) {
						$this->Cell($w, $text_h, $arr_buff[0], $border, 1, $align, $f_fill);
//						echo "1111<br>";
						$this->x = $save_x;  
						$line_minus++;
					}
					if (strlen(trim($arr_buff[1])) > 0) {
						$line_minus++;
					}
//					echo "1.................x:y-".$this->x.":".$this->y."  line_munus=$line_minus<br>";
//					echo "image ($imgfile) exist : [".file_exists($imgfile)."]<br>";
					if (file_exists($imgfile)) {
						list($width, $height, $type, $attr) = getimagesize($imgfile);
						$img_h -= ($text_h * $line_minus);	// ลบความสูงของรูปกรณ๊มีส่วนที่เป็นบรรทัดของ text
						if ($width > $pix_border_w) {
							$image_w = 	$pix_border_w-2;
							$image_h = floor($image_w / $width * $height);
							if ($image_h > $img_h) {
								$buff_h = 	$img_h-2;
								$image_w = floor($buff_h / $image_h * $image_w);
								$image_h = $buff_h;
							}
						} elseif ($height > $img_h) {
							$image_h = 	$img_h-2;
							$image_w = floor($image_h / $height * $width);
							if ($image_w > $pix_border_w) {
								$buff_w = 	$pix_border_w-2;
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
							$posx = $this->x+($pix_border_w/2)-($image_w/2);
						else
							$posx = $this->x+$pix_border_w-2-$image_w;
						$posy = $this->y+1;//-($f_one_line?$text_h:0);
//						echo "1..x:y-".$this->x.":".$this->y." w:h-$width:$height, $image_w:$image_h |".$this->merge_rows_h[$iii]." (w=$pix_border_w:h=$h) (posx:$posx, posy:$posy)<br>";
						$this->Image($imgfile, $posx, $posy, $image_w, $image_h);
//						$this->Image($imgfile, $posx, $posy, 5, 5);
						$this->x = $save_x;  
						$this->y += $image_h+2;
//						$this->y = $save_y + $h;
//						echo "1..image x=".$this->x.",y=".$this->y."<br>";
					} else { // else if (file_exists($imgfile))
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

			function close_tab($tail_text, $border="close", $at_end_up=10)
			{
				if ($this->f_table==1) {
					// ทำการลากเส้นปิด ตาม border กำหนด
					if (strtolower($border)=="close") {	// ถ้าส่งค่า $border มาเป็น "close" จะพิมพ์ เส้นปิดต่อจากตำแหน่งสุดท้าย
//						echo "close 1<br>";
						$sum_w = 0;
						for($jjj = 0; $jjj < count($this->arr_column_map); $jjj++) {
							if ($this->arr_column_sel[$this->arr_column_map[$jjj]]==1) { $sum_w += $this->arr_column_width[$this->arr_column_map[$jjj]]; }
						}
						$line_start_y = $this->y;		$line_end_y = $this->y;
						$line_start_x = $this->lMargin;		$line_end_x = $line_start_x+$sum_w;
//						echo "เส้นนอนปิด-->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y)<br>";
						$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นนอนปิด
					} else if (strtolower($border)!="no") {	// ถ้าส่งค่า $border มาเป็น "no" จะไม่พิมพ์ เส้นตารางต่อจนสุดหน้า
//						echo "close 2<br>";
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
							if ($this->arr_column_sel[$this->arr_column_map[$i]]==1) {
//								$line_start_x += (float)$this->arr_head_width[$i];  $line_end_x += (float)$this->arr_head_width[$i];
								$line_start_x += (float)$this->arr_column_width[$this->arr_column_map[$i]];  $line_end_x += (float)$this->arr_column_width[$this->arr_column_map[$i]];
								if ($have_h !== false && $i != ($cnt_column_head-1))
									$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางที่เหลือตรงกลาง
								elseif ($have_r !== false && $i == ($cnt_column_head-1)) 
									$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางสุดท้าย
							} // end if ($this->arr_column_sel[$i]==1)
						}	// end for $i
					}
					// จบการลากเส้นปิด ตาม border กำหนด
					$this->f_table = 0;
					$this->arr_func_aggreg = (array) null;	
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
					$this->tabdata_rotate_angle = "";
					$this->tabdata_font_color = "";
					$this->arr_tabdata_font_name = (array) null;
					$this->arr_tabdata_font_style = (array) null;
					$this->arr_tabdata_font_size = (array) null;
					$this->arr_tabdata_font_color = (array) null;
					$this->arr_tabdata_border = (array) null;
					$this->arr_tabdata_fill_color = (array) null;
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

			function tab_pagebreak($border, $at_end_up=10, $nohead=false, $pos_last_y=0) {
				$ret = false;
				if ($this->f_table==1) {
//					echo " h (".$this->h.") - y (".$this->y.") - at_end_up ($at_end_up) < at_end_up ($at_end_up) [$border]<br>";
					if(($this->h - $this->y - $at_end_up) < $at_end_up){ 
//						echo "new head<br>";
						if ($pos_last_y) $this->y = $pos_last_y;
						$have_l = strpos(strtolower($border), "l");
						$have_r = strpos(strtolower($border), "r");	
						$have_h = strpos(strtolower($border), "h");
						$have_b = strpos(strtolower($border), "b");

						$cnt_column_head = count($this->arr_tab_head);
						$this->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ
						if ($have_l !== false) {	
							$line_start_y = $this->y;		$line_end_y = $this->h - $at_end_up;
							$line_start_x = $this->lMargin;		$line_end_x = $line_start_x;
							$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางแรก
						}
						for($i=0; $i<$cnt_column_head; $i++) {
							if ($this->arr_column_sel[$this->arr_column_map[$i]]==1) {
//								$line_start_x += (float)$this->arr_head_width[$i];  $line_end_x += (float)$this->arr_head_width[$i];
								$line_start_x += (float)$this->arr_column_width[$this->arr_column_map[$i]];  $line_end_x += (float)$this->arr_column_width[$this->arr_column_map[$i]];
								if ($have_h !== false && $i != ($cnt_column_head-1))
									$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางที่เหลือตรงกลาง
								elseif ($have_r !== false && $i == ($cnt_column_head-1)) 
									$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางสุดท้าย
							} // end if ($this->arr_column_sel[$i]==1)
						}	// end for $i
						// เส้นนอนปิด
						if ($have_b !== false) {
							$line_start_y = $this->h - $at_end_up;		$line_end_y = $line_start_y;
							$line_start_x = $this->lMargin;		//	$line_end_x = $line_end_x;
							$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางแรก
						}
						$this->AddPage();
						$max_y = $this->y;
						if (!$nohead)
							$this->print_tab_header();
//						echo "print_tab_header $line_start_x,$line_start_y-->$line_end_x,$line_end_y<br>";
						$ret = true;
					} // end if1
//					$this->x = $start_x;		//	$this->y = $max_y;
				} // end if ($this->f_table==1) 
				
				return $ret;
			} // end function tab_pagebreak

			function print_tab_line() {
				$this->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ
				$sum_w = 0;
				for($i=0; $i<count($this->arr_column_map); $i++) {
						if ($this->arr_column_sel[$this->arr_column_map[$i]]==1) {
							$sum_w += (float)$this->arr_column_width[$this->arr_column_map[$i]];
						}
				}
				$line_start_y = $this->y;		$line_end_y = $this->y;
				$line_start_x = $this->lMargin;		$line_end_x = $line_start_x + $sum_w;
				$this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y); // เส้นขวางแรก
//				echo "this->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y)<br>";
			}


			function do_aggregate($arr_data) {
					// กำหนด agregate function
//					echo "data>".implode(",",$arr_data).", func>".implode(",",$this->arr_func_aggreg).", sel>".implode(",",$this->arr_column_sel).", map>".implode(",",$this->arr_column_map)."<br>";
					$arr_column_aggreg = (array) null;
					
					$ret = $this->do_aggregate_argu($arr_data, $this->arr_func_aggreg, $this->arr_column_sel, $this->arr_column_map);
//					echo "agregate=$ret<br>";
					
					return $ret;
			} // end function

			function do_aggregate_argu($arr_data, $arr_func_aggreg, $arr_column_sel, $arr_column_map) {
					// กำหนด agregate function with parameter for calling outsite open_tab


					$arr_column_aggreg = (array) null;
//					echo "1..data>".implode(",",$arr_data)."<br>";

					$arr_grp = (array) null;
					$arr_text = (array) null;
					$arr_ff = (array) null;
					$arr_sum = (array) null;
					
					/**************************************************************************/
					/**** แยก รหัสกลุ่ม ข้อมูลตัวเลข ข้อมูลตัวอักษร ออกจากกัน ลงใน array ****/
					/**************************************************************************/
					for($i=0; $i < count($arr_data); $i++) {
						$arr_ff[] = "";
						$arr_sum[] = 0;
						$chk_merge = strpos($arr_data[$i],"<**");
						if ($chk_merge!==false) {
							$c = strpos($arr_data[$i],"**>");
							if ($c!==false) {
								$mgrp = substr($arr_data[$i], $chk_merge, $c-$chk_merge+3);
								$buff = str_replace($mgrp,"",$arr_data[$i]);
//								if (is_numeric($buff))	{ $arr_data[$i] = $buff; $arr_grp[] = $mgrp; }
//								else { $arr_data[$i] = "$buff"; $arr_grp[] = $mgrp; }
//								if (is_numeric($buff))	{ $arr_data[$i] = $buff; $arr_grp[] = $mgrp; }
//								else { $arr_data[$i] = ""; $arr_grp[] = $mgrp.$buff; }
								if (is_numeric($buff))	{ $arr_data[$i] = $buff; $arr_grp[] = $mgrp; $arr_text[] = ""; }
								else { $arr_data[$i] = ""; $arr_grp[] = $mgrp;  $arr_text[] = $buff; }
							} else {
								$arr_grp[] = ""; $arr_text[] = "";
							}
						} else {	// no group
							$arr_grp[] = ""; $arr_text[] = "";
						}
					}
					/**************************************************************************/
					/**** แยก รหัสกลุ่ม ข้อมูลตัวเลข ข้อมูลตัวอักษร ออกจากกัน ลงใน array ****/
					/***************************** End Block *********************************/
//					echo "0..data>".implode(",",$arr_data)." | ".implode(",",$arr_grp)." | ".implode(",",$arr_text)."<br>";
					$arr_column_aggreg = (array) null;
//					echo ">map>".implode(",",$arr_column_map)."<br>";

					if (count($arr_func_aggreg) > 0) {
						/**************************************************************************/
						/****** compute argregate function $arr_data is numeric only *********/
						/**************************************************************************/
						for($kk=0; $kk < count($arr_func_aggreg); $kk++) {
							if ($arr_column_sel[$arr_column_map[$kk]]==1) {	// 1 = แสดง column นี้
//								echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-".$arr_func_aggreg[$arr_column_map[$kk]]."<br>";
								$a_sub_func = explode("|",$arr_func_aggreg[$arr_column_map[$kk]]);
								for($ii = 0; $ii < count($a_sub_func); $ii++) {
//									echo "a_sub_func [$ii]=".$a_sub_func[$ii]."<br>";
									$c = strpos($a_sub_func[$ii],"SUM-");
									if ($c !== false) {
										$arr_ff[$kk] = "S";
										$buff = substr($a_sub_func[$ii],4);
										$a = explode("-",$buff);
										$sum = 0;
										$havedata = false;
										$pgrp = "";
										for($kkk=0; $kkk < count($a); $kkk++) {
											if ($arr_column_sel[$a[$kkk]]==1) {	// 1 = แสดง column นี้ ใช้การเลือกค่าทำ sum จาก link ต้นฉบับ ไม่ต้อง map ถ้า map จะผิด
												$ndata = trim($arr_data[$a[$kkk]]);
												if ($ndata!="") $havedata = true;
//												echo "sum kk=$kk--kkk=$kkk--a[$kkk]=".$a[$kkk]."--map=".$arr_column_map[$a[$kkk]]."-->ndata=$ndata, havedata=$havedata<br>";
												if (!$arr_grp[$a[$kkk]] || $pgrp != $arr_grp[$a[$kkk]]) {
													$sum += (float)$ndata;
													$pgrp = $arr_grp[$a[$kkk]];
												}
											}
										}
										if ($havedata)	$lastval = (string)$sum;	else		$lastval = chr(96);	 // chr(96) แทนช่องว่าง
//										if ($havedata) echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-sum-".$sum."-lastval=$lastval<br>";
									} else {
										$c = strpos($a_sub_func[$ii],"PERC");
										if ($c !== false) {
											$arr_ff[$kk] = "P";
											$c1=strpos($a_sub_func[$ii],"-");
											if ($c1==4) $forcol=-1;
											else $forcol=substr($a_sub_func[$ii],4,$c1-4);
											$buff = substr($a_sub_func[$ii],$c1+1);
											$a = explode("-",$buff);
											$thisdata = -1;
											$sum = 0;
											$cnt = 0;
											$havedata = false;
											$pgrp = "";
											for($kkk=0; $kkk < count($a); $kkk++) {
												if ($arr_column_sel[$a[$kkk]]==1) {	// 1 = แสดง column นี้ ใช้การเลือกค่าทำ sum จาก link ต้นฉบับ ไม่ต้อง map ถ้า map จะผิด
													$ndata = trim($arr_data[$a[$kkk]]);
													if ($ndata!="") $havedata = true;
//													echo "perc kk=$kk--kkk=$kkk--(".$arr_column_map[$kkk].")--a[$kkk]=".$a[$kkk]."--map=".$arr_column_map[$a[$kkk]]."-->ndata=$ndata, forcol=$forcol, havedata=$havedata<br>";
													if ($forcol==$a[$kkk]) $thisdata = (float)$ndata;
													if (!$arr_grp[$a[$kkk]] || $pgrp != $arr_grp[$a[$kkk]]) {
														$sum += (float)$ndata;
														$pgrp = $arr_grp[$a[$kkk]];
													}
													$cnt++;
												}
											}
											if ($havedata)	
												if ($sum > 0) {
													if ($thisdata == -1) $thisdata = $sum;	// กรณีที่ column ที่กำหนด function นี้ ไม่เป็นสมาชิกในกลุ่มที่นำมารวม ก็จะถือว่าเป็น column สรุปรวม
													$lastval = (string)($thisdata / $sum * 100);	// ทำเป็น percent
	//												echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-perc=$thisdata / $sum * 100<br>";
												} else $lastval="0";
											else $lastval = chr(96);	 // chr(96) แทนช่องว่าง
										} else {
											$c = strpos($a_sub_func[$ii],"AVG-");
											if ($c !== false) {
												$buff = substr($a_sub_func[$ii],4);
												$arr_ff[$kk] = "A";
												$a = explode("-",$buff);
												$thisdata = 0;
												$sum = 0;
												$cnt = 0;
												$havedata = false;
												$pgrp = "";
												for($kkk=0; $kkk < count($a); $kkk++) {
													if ($arr_column_sel[$a[$kkk]]==1) {	// 1 = แสดง column นี้
														$ndata = trim($arr_data[$a[$kkk]]);
														if ($ndata!="") $havedata = true;
														if (!$arr_grp[$a[$kkk]] || $pgrp != $arr_grp[$a[$kkk]]) {
															$sum += (float)$ndata;
															$pgrp = $arr_grp[$a[$kkk]];
														}
														$cnt++;
													}
												}
												if ($havedata)
													$lastval = (string)($sum / $cnt);	// ทำเป็น ค่าเฉลี่ย
												else 	$lastval = chr(96);	 // chr(96) แทนช่องว่าง
//												echo "function aggregate avg-".($sum / $cnt)."<br>";
											} else {
												$c = strpos($a_sub_func[$ii],"FORM");
												if ($c !== false) {
													$buff = substr($a_sub_func[$ii],4);
													$arr_ff[$kk] = "F";
//													echo "buff=$buff, len=".strlen($buff)."<br>";
													preg_match_all("/([+|-|*|\/|\(|\)])/", $buff, $matches, PREG_OFFSET_CAPTURE);
//													echo "data=".implode(",",$arr_data)."<br>";
													$para = (array) null;
													foreach($matches[0] as $key1 => $val1) {
//														echo "......key1=$key1 => ".$matches[0][$key1][0]."";
														if ($key1==0) $str = substr($buff, 0, $matches[0][$key1][1]);
														else if ((int)$matches[0][$key1][1] > 0) $str = substr($buff, $matches[0][$key1-1][1]+1, $matches[0][$key1][1]-$matches[0][$key1-1][1]-1);
//														echo ">>".$matches[0][$key1][1]."=>$str<br>";
														if (strpos($str,"@")!==false) { 
//															if ($ii > 0) $ndata = trim($arr_column_aggreg[$arr_column_map[(int)substr($str,1)]]);
//															else $ndata = (string)trim($arr_data[$arr_column_map[(int)substr($str,1)]]);
															if ($ii > 0) $ndata = trim($arr_column_aggreg[(int)substr($str,1)]);
															else $ndata = (string)trim($arr_data[(int)substr($str,1)]);
//															echo "1..parameter [$str]=$ndata map:".$arr_column_map[(int)substr($str,1)]."<br>";
															$para[str_replace("@","",$str)] = $ndata;
														}
														if (((int)$matches[0][$key1+1][1] == 0)) {
															$str = substr($buff, $matches[0][$key1][1]+1);
//															echo "...last key =>".$matches[0][$key1][0].">>".$matches[0][$key1][1]."=>$str<br>";
															if (strpos($str,"@")!==false) { 
//																if ($ii > 0) $ndata = trim($arr_column_aggreg[$arr_column_map[(int)substr($str,1)]]);
//																else $ndata = (string)trim($arr_data[$arr_column_map[(int)substr($str,1)]]);
																if ($ii > 0) $ndata = trim($arr_column_aggreg[(int)substr($str,1)]);
																else $ndata = (string)trim($arr_data[(int)substr($str,1)]);
//																echo "2..parameter [$str]=$ndata map:".$arr_column_map[(int)substr($str,1)]."<br>";
																$para[str_replace("@","",$str)] = $ndata;
															}
														}
													}
//													echo "buff=$buff<br>";
//													print_r($para);
													$val = compute_formula($buff, $para);
//													echo "<br>compute=$val<br>";
													$lastval = (string)$val;	// ทำเป็น ค่าเฉลี่ย
//													echo "function aggregate formula-".($sum / $cnt)."<br>";
												} else {	// ถ้าไม่มีค่าอะไร
													if ($ii > 0) $ndata = $arr_column_aggreg[$arr_column_map[$kk]];
													else $ndata = (string)$arr_data[$arr_column_map[$kk]];
													$lastval = $ndata;
		//											echo "no function-".$lastval.", ndata=$ndata<br>";
												} // end if FORMULA
											} // end if AVG
										} // end if PERC
									} // end if SUM
//									$arr_column_aggreg[$arr_column_map[$kk]] = $lastval;
									$arr_column_aggreg[$arr_column_map[$kk]] = $lastval;
								} // end for loop $ii
							} else { // else if ($arr_column_sel[$iii]==1)
								$arr_column_aggreg[$arr_column_map[$kk]] = "";
							} // end if ($arr_column_sel[$iii]==1)
						} // end loop for $kk
						/**************************************************************************/
						/****** compute argregate function $arr_data is numeric only *********/
						/****************************** End Block ********************************/

//						echo "1..aggreg=".implode(",",$arr_column_aggreg)." | func=".implode(",",$arr_func_aggreg)." | grp=".implode(",",$arr_grp)." | text=".implode(",",$arr_text)." | ff=".implode(",",$arr_ff)."<br>";
						
						/**************************************************************************/
						/****** compute argregate function สำหรับการ merge ค่าตัวเลข *********/
						/**************************************************************************/
						if (count($arr_column_aggreg) > 0) {
							$mergesum = 0; $grp = "";
							for($i=0; $i < count($arr_column_aggreg); $i++) {
//								echo "grp=".$arr_grp[$i]." && ff=".$arr_ff[$i]." && val=".$arr_column_aggreg[$i]."<br>";
								if ($arr_grp[$i] && $arr_ff[$i] && is_numeric($arr_column_aggreg[$i])) {
									if ($arr_grp[$i] != $grp) {
//										echo "1..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
										if ($mergesum)
											$arr_sum[$last_i] = $mergesum;
										$grp = $arr_grp[$i];
										$mergesum = $arr_column_aggreg[$i];
										$last_i = $i;
									}  else {
										$mergesum += $arr_column_aggreg[$i];
//										echo "2..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
									}
								} else {
//									echo "3..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
									if ($mergesum) {
										$arr_sum[$last_i] = $mergesum;
										$last_i = -1;
									}
									$mergesum = 0;
									$grp = "";
								}
							} // end loop for
//							echo "4..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
							if ($mergesum)
								$arr_sum[$last_i] = $mergesum;
								
							$grp = ""; $ss = 0;
							for($i=0; $i < count($arr_column_aggreg); $i++) {
//								echo "$i .. arr_grp ..".$arr_grp[$i].",  grp=$grp<br>";
								if ($arr_sum[$i])
									if ($arr_grp[$i] && $arr_grp[$i] != $grp) {
										$ss = $arr_sum[$i];
										$arr_column_aggreg[$i] = $ss;
										$grp = $arr_grp[$i];	
//										echo "$i .1. set ss=$ss<br>";
									} else {
										if ($arr_grp[$i]) {
											$arr_column_aggreg[$i] = $ss;
//											echo "$i .2. loop ss=$ss<br>";										
										}
									}
//								echo "5..$i..".$arr_column_aggreg[$i].".. arr_text..".$arr_text[$i]."<br>";
								if ($arr_column_aggreg[$i]=="" || ($arr_column_aggreg[$i]==chr(96) && $arr_text[$i])) $arr_column_aggreg[$i] = $arr_text[$i];
//								echo "6..$i..".$arr_column_aggreg[$i].".. arr_text..".$arr_text[$i]."<br>";
							} // end loop for
						}
						/**************************************************************************/
						/****** compute argregate function สำหรับการ merge ค่าตัวเลข *********/
						/****************************** End Block ********************************/

//						echo "2..aggreg=".implode(",",$arr_column_aggreg)." , func=".implode(",",$arr_func_aggreg)." , sum=".implode(",",$arr_sum)."<br>";

						/***************************************************************************/
						/******************* รอบสองแปลง format  TNUM , ENUM *****************/
						/***************************************************************************/
						for($kk=0; $kk < count($arr_func_aggreg); $kk++) {
							if ($arr_column_sel[$arr_column_map[$kk]]==1) {	// 1 = แสดง column นี้
//								echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-".$arr_func_aggreg[$arr_column_map[$kk]]."<br>";
								$a_sub_func = explode("|",$arr_func_aggreg[$arr_column_map[$kk]]);
								for($ii = 0; $ii < count($a_sub_func); $ii++) {
//									echo "a_sub_func [$ii]=".$a_sub_func[$ii]."<br>";
									$c = strpos($a_sub_func[$ii],"TNUM");
									if ($c !== false) {
										$ndata = $arr_column_aggreg[$arr_column_map[$kk]];
//										echo "TNUM - $ii - ".$a_sub_func[$ii]." , [$ndata]<br>";
										if ($ndata==chr(96)) {	 // chr(96) แทนช่องว่าง
											$lastval = " ";
//											echo "TNUM space ($ndata) (".chr(96).") logic(".($ndata==chr(96)).")<br>";
										} else {
//											echo "TNUM $ndata<br>";
											$zero = (string)substr($a_sub_func[$ii], 4, 1);
//											echo "zero=$zero, TNUM $ndata<br>";
											if ($zero=="0")  $c1=$c+6;
											else {
//												if (!$ndata) echo "null data [".trim($ndata)."] (is_numeric:".is_numeric($ndata).") (float:".((float)$ndata==0).") (".($ndata=="").")<br>";
//												if ((is_numeric($ndata) && (float)$ndata==0) || (!$ndata)) $ndata="-";
												if ((is_numeric($ndata) && (float)$ndata==0)) $ndata="-";
//												else if ($ndata=="")
												$c1=$c+5;
											}
											$nocomma = (strtolower(substr($a_sub_func[$ii], $c1,1))=="x"?true:false);
//											echo "function-".$a_sub_func[$ii]." nocomma=$nocomma<br>";
											if ($nocomma) 
												$dig = substr($a_sub_func[$ii], $c1+1);
											else
												$dig = substr($a_sub_func[$ii], $c1);
											if (!is_numeric($ndata))  {
												if (!$ndata && $zero=="0") $ndata = 0;
//												echo "1..ndata=$ndata<br>";
												$lastval = convert2thaidigit($ndata);
											} else if ((int)$dig > 0) {
//												echo "2..ndata=$ndata<br>";
												if ($nocomma)
													$lastval = convert2thaidigit($ndata,$dig);
												else
													$lastval = convert2thaidigit(number_format($ndata,$dig));
											} else {
//												echo "3..ndata=$ndata<br>";
												if ($dig == "z") {
													$numd = explode(".",$ndata);
													$digstr = $numd[1];
													$ndig = 0;
													for($i=strlen($digstr)-1; $i >= 0; $i--) {
														if (substr($digstr,$i,1)!="0") { $ndig = $i+1; break; }
													}
//													echo "first part:".$numd[0]." second part:".$numd[1]." ndig:$ndig<br>";
													if ($ndig == 0) 
														if ($nocomma)
															$lastval = convert2thaidigit($ndata);
														else
															$lastval = convert2thaidigit(number_format($ndata));
													else
														if ($nocomma)
															$lastval = convert2thaidigit($ndata,$ndig);
														else
															$lastval = convert2thaidigit(number_format($ndata,$ndig));
												} else 
													if ($nocomma)
														$lastval = convert2thaidigit($ndata);
													else
														$lastval = convert2thaidigit(number_format($ndata));
											}
										}
										$arr_column_aggreg[$arr_column_map[$kk]] = $lastval;
//										echo "function TNUM $kk-(map=".$arr_column_map[$kk].")-tnum-lastval=".$lastval.", ndata=$ndata, dig=$dig<br>";
									} else {
										$c = strpos($a_sub_func[$ii],"ENUM");
										if ($c !== false) {
											$ndata = $arr_column_aggreg[$arr_column_map[$kk]];
//											echo "ENUM - $ii - ".$a_sub_func[$ii]." , $ndata<br>";
											if ($ndata==chr(96)) {  // chr(96) แทนช่องว่าง
												$lastval = " ";
											} else {
												$zero = (string)substr($a_sub_func[$ii], 4, 1);
												if ($zero=="0")  $c1=$c+6;
												else {
//													if ((is_numeric($ndata) && (float)$ndata==0) || (!$ndata)) $ndata="-";
													if ((is_numeric($ndata) && (float)$ndata==0)) $ndata="-";
													$c1=$c+5;
												}
												$nocomma = (strtolower(substr($a_sub_func[$ii], $c1,1))=="x"?true:false);
//												echo "function-".$a_sub_func[$ii]." nocomma=$nocomma<br>";
												if ($nocomma) 
													$dig = substr($a_sub_func[$ii], $c1+1);
												else
													$dig = substr($a_sub_func[$ii], $c1);
												if (!is_numeric($ndata))  {
													if (!$ndata && $zero=="0") $ndata = 0;
													$lastval = $ndata;
												} else if ((int)$dig > 0) {
													if ($nocomma)
														$lastval = $ndata;
													else
														$lastval = number_format($ndata,$dig);
												} else {
													if ($dig == "z") {
														$numd = explode(".",$ndata);
														$digstr = $numd[1];
														$ndig = 0;
														for($i=strlen($digstr)-1; $i >= 0; $i--) {
															if (substr($digstr,$i,1)!="0") { $ndig = $i+1; break; }
														}
//														echo "first part:".$numd[0]." second part:".$numd[1]." ndig:$ndig<br>";
														if ($ndig == 0) 
															if ($nocomma)
																$lastval = $ndata;
															else
																$lastval = number_format($ndata);
														else
															if ($nocomma)
																$lastval = $ndata;
															else
																$lastval = number_format($ndata,$ndig);
													} else 
														if ($nocomma)
															$lastval = $ndata;
														else
															$lastval = number_format($ndata);
												}
											}
											$arr_column_aggreg[$arr_column_map[$kk]] = $lastval;
//											echo "function enum-".$lastval.", ndata=$ndata, dig=$dig<br>";
//											echo "function ENUM $kk-(map=".$arr_column_map[$kk].")-tnum-lastval=".$lastval.", ndata=$ndata, dig=$dig<br>";
										} // end if ENUM
									} // end if TNUM
								} // end for loop $ii
							} // end if ($this->arr_column_sel[$iii]==1)
						} // end loop for $kk
						/***************************************************************************/
						/******************* รอบสองแปลง format  TNUM , ENUM *****************/
						/******************************* End Block ********************************/

//						echo "3..aggreg=".implode(",",$arr_column_aggreg)."<br>";
					
					} // end if if (count($arr_func_aggreg) > 0)

					/***************************************************************************/
					/************ ทำการ Merge column และ Write ออก WorkSheet ************/
					/***************************************************************************/
					$cntcolumn = count($arr_column_aggreg);
					
					if ($cntcolumn > 0) {
						$cntshow = 0;
						for($i=0; $i < $cntcolumn; $i++) {
							if ($arr_column_aggreg[$i])
								$arr_column_aggreg[$i] = $arr_grp[$i].str_replace("*Enter*","",$arr_column_aggreg[$i]);
							else $arr_column_aggreg[$i] = $arr_grp[$i];
							if ($arr_column_sel[$arr_column_map[$i]]==1) $cntshow++;
						}
					}
					// จบการกำหนด agregate function
					
					return (count($arr_column_aggreg) > 0 ? implode("|", $arr_column_aggreg) : "");
			} // end function

			function do_aggregate_argu_old($arr_data, $arr_func_aggreg, $arr_column_sel, $arr_column_map) {
					// กำหนด agregate function with parameter for calling outsite open_tab
//					echo "data>".implode(",",$arr_data)."<br>";
					$arr_grp = (array) null;
					$arr_ff = (array) null;
					$arr_sum = (array) null;
					for($i=0; $i < count($arr_data); $i++) {
						$arr_ff[] = "";
						$arr_sum[] = 0;
						$chk_merge = strpos($arr_data[$i],"<**");
						if ($chk_merge!==false) {
							$c = strpos($arr_data[$i],"**>");
							if ($c!==false) {
								$mgrp = substr($arr_data[$i], $chk_merge, $c-$chk_merge+3);
								$buff = str_replace($mgrp,"",$arr_data[$i]);
								if (is_numeric($buff))	{ $arr_data[$i] = $buff; $arr_grp[] = $mgrp; }
								else { $arr_data[$i] = ""; $arr_grp[] = $mgrp.$buff; }
							} else {
								$arr_grp[] = "";
							}
						} else {	// no group
							$arr_grp[] = "";
						}
					}
//					echo "data>".implode(",",$arr_data)."<br>";
					$arr_column_aggreg = (array) null;
//					echo ">map>".implode(",",$arr_column_map)."<br>";
					if (count($arr_func_aggreg) > 0) {
						for($kk=0; $kk < count($arr_func_aggreg); $kk++) {
							if ($arr_column_sel[$arr_column_map[$kk]]==1) {	// 1 = แสดง column นี้
//								echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-".$arr_func_aggreg[$arr_column_map[$kk]]."<br>";
								$a_sub_func = explode("|",$arr_func_aggreg[$arr_column_map[$kk]]);
								for($ii = 0; $ii < count($a_sub_func); $ii++) {
//									echo "a_sub_func [$ii]=".$a_sub_func[$ii]."<br>";
									$c = strpos($a_sub_func[$ii],"SUM-");
									if ($c !== false) {
										$arr_ff[$kk] = "S";
										$buff = substr($a_sub_func[$ii],4);
										$a = explode("-",$buff);
										$sum = 0;
										$havedata = false;
										for($kkk=0; $kkk < count($a); $kkk++) {
											if ($arr_column_sel[$a[$kkk]]==1) {	// 1 = แสดง column นี้ ใช้การเลือกค่าทำ sum จาก link ต้นฉบับ ไม่ต้อง map ถ้า map จะผิด
												$ndata = trim($arr_data[$a[$kkk]]);
												if ($ndata!="") $havedata = true;
//												echo "sum kk=$kk--kkk=$kkk--a[$kkk]=".$a[$kkk]."--map=".$arr_column_map[$a[$kkk]]."-->ndata=$ndata, havedata=$havedata<br>";
												$sum += (float)$ndata;
											}
										}
										if ($havedata)	$lastval = (string)$sum;	else		$lastval = chr(96);	 // chr(96) แทนช่องว่าง
//										if ($havedata) echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-sum-".$sum."-lastval=$lastval<br>";
									} else {
										$c = strpos($a_sub_func[$ii],"PERC");
										if ($c !== false) {
											$arr_ff[$kk] = "P";
											$c1=strpos($a_sub_func[$ii],"-");
											if ($c1==4) $forcol=-1;
											else $forcol=substr($a_sub_func[$ii],4,$c1-4);
											$buff = substr($a_sub_func[$ii],$c1+1);
											$a = explode("-",$buff);
											$thisdata = -1;
											$sum = 0;
											$cnt = 0;
											$havedata = false;
											for($kkk=0; $kkk < count($a); $kkk++) {
												if ($arr_column_sel[$a[$kkk]]==1) {	// 1 = แสดง column นี้ ใช้การเลือกค่าทำ sum จาก link ต้นฉบับ ไม่ต้อง map ถ้า map จะผิด
													$ndata = trim($arr_data[$a[$kkk]]);
													if ($ndata!="") $havedata = true;
//													echo "perc kk=$kk--kkk=$kkk--(".$arr_column_map[$kkk].")--a[$kkk]=".$a[$kkk]."--map=".$arr_column_map[$a[$kkk]]."-->ndata=$ndata, forcol=$forcol, havedata=$havedata<br>";
													if ($forcol==$a[$kkk]) $thisdata = (float)$ndata;
													$sum += (float)$ndata;
													$cnt++;
												}
											}
											if ($havedata)	
												if ($sum > 0) {
													if ($thisdata == -1) $thisdata = $sum;	// กรณีที่ column ที่กำหนด function นี้ ไม่เป็นสมาชิกในกลุ่มที่นำมารวม ก็จะถือว่าเป็น column สรุปรวม
													$lastval = (string)($thisdata / $sum * 100);	// ทำเป็น percent
	//												echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-perc=$thisdata / $sum * 100<br>";
												} else $lastval="0";
											else $lastval = chr(96);	 // chr(96) แทนช่องว่าง
										} else {
											$c = strpos($a_sub_func[$ii],"AVG-");
											if ($c !== false) {
												$buff = substr($a_sub_func[$ii],4);
												$arr_ff[$kk] = "A";
												$a = explode("-",$buff);
												$thisdata = 0;
												$sum = 0;
												$cnt = 0;
												$havedata = false;
												for($kkk=0; $kkk < count($a); $kkk++) {
													if ($arr_column_sel[$a[$kkk]]==1) {	// 1 = แสดง column นี้
														$ndata = trim($arr_data[$a[$kkk]]);
														if ($ndata!="") $havedata = true;
														$sum += (float)$ndata;
														$cnt++;
													}
												}
												if ($havedata)
													$lastval = (string)($sum / $cnt);	// ทำเป็น ค่าเฉลี่ย
												else 	$lastval = chr(96);	 // chr(96) แทนช่องว่าง
//												echo "function aggregate avg-".($sum / $cnt)."<br>";
											} else {
												$c = strpos($a_sub_func[$ii],"FORM");
												if ($c !== false) {
													$buff = substr($a_sub_func[$ii],4);
													$arr_ff[$kk] = "F";
//													echo "buff=$buff, len=".strlen($buff)."<br>";
													preg_match_all("/([+|-|*|\/|\(|\)])/", $buff, $matches, PREG_OFFSET_CAPTURE);
//													echo "data=".implode(",",$arr_data)."<br>";
													$para = (array) null;
													foreach($matches[0] as $key1 => $val1) {
//														echo "......key1=$key1 => ".$matches[0][$key1][0]."";
														if ($key1==0) $str = substr($buff, 0, $matches[0][$key1][1]);
														else if ((int)$matches[0][$key1][1] > 0) $str = substr($buff, $matches[0][$key1-1][1]+1, $matches[0][$key1][1]-$matches[0][$key1-1][1]-1);
//														echo ">>".$matches[0][$key1][1]."=>$str<br>";
														if (strpos($str,"@")!==false) { 
//															if ($ii > 0) $ndata = trim($arr_column_aggreg[$arr_column_map[(int)substr($str,1)]]);
//															else $ndata = (string)trim($arr_data[$arr_column_map[(int)substr($str,1)]]);
															if ($ii > 0) $ndata = trim($arr_column_aggreg[(int)substr($str,1)]);
															else $ndata = (string)trim($arr_data[(int)substr($str,1)]);
//															echo "1..parameter [$str]=$ndata map:".$arr_column_map[(int)substr($str,1)]."<br>";
															$para[$str] = $ndata;
														}
														if (((int)$matches[0][$key1+1][1] == 0)) {
															$str = substr($buff, $matches[0][$key1][1]+1);
//															echo "...last key =>".$matches[0][$key1][0].">>".$matches[0][$key1][1]."=>$str<br>";
															if (strpos($str,"@")!==false) { 
//																if ($ii > 0) $ndata = trim($arr_column_aggreg[$arr_column_map[(int)substr($str,1)]]);
//																else $ndata = (string)trim($arr_data[$arr_column_map[(int)substr($str,1)]]);
																if ($ii > 0) $ndata = trim($arr_column_aggreg[(int)substr($str,1)]);
																else $ndata = (string)trim($arr_data[(int)substr($str,1)]);
//																echo "2..parameter [$str]=$ndata map:".$arr_column_map[(int)substr($str,1)]."<br>";
																$para[$str] = $ndata;
															}
														}
													}
//													print_r($para);
													$val = compute_formula($buff, $para);
//													echo "<br>compute=$val<br>";
													$lastval = (string)$val;	// ทำเป็น ค่าเฉลี่ย
//													echo "function aggregate formula-".($sum / $cnt)."<br>";
												} else {	// ถ้าไม่มีค่าอะไร
													if ($ii > 0) $ndata = $arr_column_aggreg[$arr_column_map[$kk]];
													else $ndata = (string)$arr_data[$arr_column_map[$kk]];
													$lastval = $ndata;
		//											echo "no function-".$lastval.", ndata=$ndata<br>";
												} // end if FORMULA
											} // end if AVG
										} // end if PERC
									} // end if SUM
//									$arr_column_aggreg[$arr_column_map[$kk]] = $lastval;
									$arr_column_aggreg[$arr_column_map[$kk]] = $lastval;
								} // end for loop $ii
							} else { // else if ($arr_column_sel[$iii]==1)
								$arr_column_aggreg[$arr_column_map[$kk]] = "";
							} // end if ($arr_column_sel[$iii]==1)
						} // end loop for $kk

//						echo "1..aggreg=".implode(",",$arr_column_aggreg)." , func=".implode(",",$arr_func_aggreg)." , grp=".implode(",",$arr_grp)." , ff=".implode(",",$arr_ff)."<br>";
						if (count($arr_column_aggreg) > 0) {
							$mergesum = 0; $grp = "";
							for($i=0; $i < count($arr_column_aggreg); $i++) {
//								echo "grp=".$arr_grp[$i]." && ff=".$arr_ff[$i]." && val=".$arr_column_aggreg[$i]."<br>";
								if ($arr_grp[$i] && $arr_ff[$i] && is_numeric($arr_column_aggreg[$i])) {
									if ($arr_grp[$i] != $grp) {
//										echo "1..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
										if ($mergesum)
											$arr_sum[$last_i] = $mergesum;
										$grp = $arr_grp[$i];
										$mergesum = $arr_column_aggreg[$i];
										$last_i = $i;
									}  else {
										$mergesum += $arr_column_aggreg[$i];
//										echo "2..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
									}
								} else {
//									echo "3..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
									if ($mergesum) {
										$arr_sum[$last_i] = $mergesum;
										$last_i = -1;
									}
									$mergesum = 0;
									$grp = "";
								}
							} // end loop for
//							echo "4..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
							if ($mergesum)
								$arr_sum[$last_i] = $mergesum;
								
							$grp = ""; $ss = 0;
							for($i=0; $i < count($arr_column_aggreg); $i++) {
//								echo "$i .. arr_grp ..".$arr_grp[$i].",  grp=$grp<br>";
								if ($arr_grp[$i] && $arr_grp[$i] != $grp) {
									$ss = $arr_sum[$i];
									$arr_column_aggreg[$i] = $ss;
									$grp = $arr_grp[$i];									
//									echo "$i .1. set ss=$ss<br>";
								} else {
									if ($arr_grp[$i]) {
										$arr_column_aggreg[$i] = $ss;
//										echo "$i .2. loop ss=$ss<br>";										
									}
								}
							} // end loop for
						}
//						echo "2..aggreg=".implode(",",$arr_column_aggreg)." , func=".implode(",",$arr_func_aggreg)." , sum=".implode(",",$arr_sum)."<br>";

						// รอบสองแปลง format  TNUM , ENUM
						for($kk=0; $kk < count($arr_func_aggreg); $kk++) {
							if ($arr_column_sel[$arr_column_map[$kk]]==1) {	// 1 = แสดง column นี้
//								echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-".$arr_func_aggreg[$arr_column_map[$kk]]."<br>";
								$a_sub_func = explode("|",$arr_func_aggreg[$arr_column_map[$kk]]);
								for($ii = 0; $ii < count($a_sub_func); $ii++) {
//									echo "a_sub_func [$ii]=".$a_sub_func[$ii]."<br>";
									$c = strpos($a_sub_func[$ii],"TNUM");
									if ($c !== false) {
										$ndata = $arr_column_aggreg[$arr_column_map[$kk]];
//										echo "TNUM - $ii - ".$a_sub_func[$ii]." , $ndata<br>";
										if ($ndata==chr(96)) {	 // chr(96) แทนช่องว่าง
											$lastval = " ";
//											echo "TNUM space ($ndata) (".chr(96).") logic(".($ndata==chr(96)).")<br>";
										} else {
//											echo "TNUM $ndata<br>";
											$zero = (string)substr($a_sub_func[$ii], 4, 1);
//											echo "zero=$zero TNUM $ndata<br>";
											if ($zero=="0")  $c1=$c+6;
											else {
//												if (!$ndata) echo "null data [$ndata] (is_numeric:".is_numeric($ndata).") (float:".((float)$ndata==0).") (".($ndata=="").")<br>";
												if (is_numeric($ndata) && (float)$ndata==0) $ndata="-";
//												else if ($ndata=="")
												$c1=$c+5;
											}
											$nocomma = (strtolower(substr($a_sub_func[$ii], $c1,1))=="x"?true:false);
//											echo "function-".$a_sub_func[$ii]." nocomma=$nocomma<br>";
											if ($nocomma) 
												$dig = substr($a_sub_func[$ii], $c1+1);
											else
												$dig = substr($a_sub_func[$ii], $c1);
											if (!is_numeric($ndata)) 
												$lastval = convert2thaidigit($ndata);
											else if ((int)$dig > 0)
												if ($nocomma)
													$lastval = convert2thaidigit($ndata,$dig);
												else
													$lastval = convert2thaidigit(number_format($ndata,$dig));
											else {
												if ($dig == "z") {
													$numd = explode(".",$ndata);
													$digstr = $numd[1];
													$ndig = 0;
													for($i=strlen($digstr)-1; $i >= 0; $i--) {
														if (substr($digstr,$i,1)!="0") { $ndig = $i+1; break; }
													}
//													echo "first part:".$numd[0]." second part:".$numd[1]." ndig:$ndig<br>";
													if ($ndig == 0) 
														if ($nocomma)
															$lastval = convert2thaidigit($ndata);
														else
															$lastval = convert2thaidigit(number_format($ndata));
													else
														if ($nocomma)
															$lastval = convert2thaidigit($ndata,$ndig);
														else
															$lastval = convert2thaidigit(number_format($ndata,$ndig));
												} else 
													if ($nocomma)
														$lastval = convert2thaidigit($ndata);
													else
														$lastval = convert2thaidigit(number_format($ndata));
											}
										}
										$arr_column_aggreg[$arr_column_map[$kk]] = $lastval;
//										echo "function TNUM $kk-(map=".$arr_column_map[$kk].")-tnum-".$lastval.", ndata=$ndata, dig=$dig<br>";
									} else {
										$c = strpos($a_sub_func[$ii],"ENUM");
										if ($c !== false) {
											$ndata = $arr_column_aggreg[$arr_column_map[$kk]];
//											echo "ENUM - $ii - ".$a_sub_func[$ii]." , $ndata<br>";
											if ($ndata==chr(96)) {  // chr(96) แทนช่องว่าง
												$lastval = " ";
											} else {
												$zero = (string)substr($a_sub_func[$ii], 4, 1);
												if ($zero=="0")  $c1=$c+6;
												else {
													if (is_numeric($ndata) && (float)$ndata==0) $ndata="-";
													$c1=$c+5;
												}
												$nocomma = (strtolower(substr($a_sub_func[$ii], $c1,1))=="x"?true:false);
//												echo "function-".$a_sub_func[$ii]." nocomma=$nocomma<br>";
												if ($nocomma) 
													$dig = substr($a_sub_func[$ii], $c1+1);
												else
													$dig = substr($a_sub_func[$ii], $c1);
												if (!is_numeric($ndata)) 
													$lastval = $ndata;
												else if ((int)$dig > 0)
													if ($nocomma)
														$lastval = $ndata;
													else
														$lastval = number_format($ndata,$dig);
												else {
													if ($dig == "z") {
														$numd = explode(".",$ndata);
														$digstr = $numd[1];
														$ndig = 0;
														for($i=strlen($digstr)-1; $i >= 0; $i--) {
															if (substr($digstr,$i,1)!="0") { $ndig = $i+1; break; }
														}
//														echo "first part:".$numd[0]." second part:".$numd[1]." ndig:$ndig<br>";
														if ($ndig == 0) 
															if ($nocomma)
																$lastval = $ndata;
															else
																$lastval = number_format($ndata);
														else
															if ($nocomma)
																$lastval = $ndata;
															else
																$lastval = number_format($ndata,$ndig);
													} else 
														if ($nocomma)
															$lastval = $ndata;
														else
															$lastval = number_format($ndata);
												}
											}
											$arr_column_aggreg[$arr_column_map[$kk]] = $lastval;
//											echo "function enum-".$lastval.", ndata=$ndata, dig=$dig<br>";
//											echo "function ENUM $kk-(map=".$arr_column_map[$kk].")-tnum-".$lastval.", ndata=$ndata, dig=$dig<br>";
										} // end if ENUM
									} // end if TNUM
								} // end for loop $ii
							} // end if ($this->arr_column_sel[$iii]==1)
						} // end loop for $kk

//						echo "3..aggreg=".implode(",",$arr_column_aggreg)."<br>";
					
					} // end if if (count($arr_func_aggreg) > 0)

					if (count($arr_column_aggreg) > 0) {
						for($i=0; $i < count($arr_column_aggreg); $i++) {
							if ($arr_column_aggreg[$i])
								$arr_column_aggreg[$i] = $arr_grp[$i].str_replace("*Enter*","",$arr_column_aggreg[$i]);
							else $arr_column_aggreg[$i] = $arr_grp[$i];
						}
					}
//					echo "4..aggreg=".implode(",",$arr_column_aggreg)."<br>";
					// จบการกำหนด agregate function
					return (count($arr_column_aggreg) > 0 ? implode("|", $arr_column_aggreg) : "");
			} // end function

			function add_lineFreeTab($line_h, $arr_data, $arr_align, $arr_border, $arr_font, $arr_width, $arr_func_aggreg) {
				// parameter
				//		$line_h				: ความสูงของบรรทัด
				//		$arr_data			: array ของข้อความที่จะพิมพ์
				//		$arr_align			: array ของการจัด ข้อความ  L ชิดซ้าย  C วางกลาง  R ชิดขวา
				//		$arr_border		: array ของขอบ T ขอบบน L ขอบซ้าย B ขอบล่าง R ขอบขวา
				//		$arr_font			: array ของรูปแบบตัวอักษร  ชื่อ font|b=หนา U=เส้นใต้|font size|XXXXXX=สีตัวอักษร|ZZZZZZ=สีพื้น
				//		$arr_width			: array ของความกว้างของ cell ถ้าไม่มีจะ gen ให้อัตโนมัติ จากความกว้างทั้งหมด 
				//									  ถ้าต้องการรวม สอง cell ให้ต่อกัน ตามขนาดความยาวของ text ให้กำหนดค่าความกว้างรวมไว้ตัวแรก แล้วกำหนดความกว้าง ตัวถัดมาด้วย '-'
				//		$arr_func_aggreg 	: array ของ function aggregate และ กำหนด ชนิดการแสดงตัวเลข TNUM ENUM เป็นต้น
				
				global $NUMBER_DISPLAY;

				$arr_column_sel = (array) null;
				$arr_column_map = (array) null;
				if (!$arr_func_aggreg) {
					$arr_func_aggreg = (array) null;
					for($i=0; $i < count($arr_data); $i++) { 
						$arr_column_sel[] = 1; $arr_column_map[] = $i; 
						$arr_func_aggreg[] = ($NUMBER_DISPLAY==2)?"TNUM":"ENUM";
					}
				} else {
					for($i=0; $i < count($arr_data); $i++) { 
						$arr_column_sel[] = 1; $arr_column_map[] = $i; 
					}
				}
				$buff_data = $this->do_aggregate_argu($arr_data, $arr_func_aggreg, $arr_column_sel, $arr_column_map);
				$arr_text = explode("|",$buff_data);

				$page_w = $this->w;
				if  ($page_w > 280 && $page_w < 300) $page_w = 290;
				else if  ($page_w > 190 && $page_w < 215) $page_w = 200;
				else $page_w = 200;
				
				$setw = 0;
				$cntw = count($arr_width);	// กรณีมีการกำหนดค่า ความกว้างของแต่ละ column
				if ($cntw > 0) {	
					$page_w1 = 0; $cntnone = 0;
					for($i = 0; $i < $cntw; $i++) {		// loop เพื่อหา total page width
						if ($arr_width[$i]=="-") {	// กำหนดให้มีการแบ่งความกว้างไว้กับค่าความกว้างตัวก่อนหน้าที่มีมา
							$page_w1 += 0;			// ไม่ให้ทำอะไร เพราะ ค่าความกว้าง - จะให้รวมกับ w ก่อนหน้า
						} else if ($arr_width[$i]) {
							$page_w1 += $arr_width[$i];
						} else $cntnone++;
					}
					if ($cntnone > 0) {	// ถ้า มี ตัวที่ไม่กำหนดค่า จะกำหนดให้อัตโนมัติ
						$rest = $page_w - $page_w1;
						$setw = floor($rest / $cntnone);
						for($i = 0; $i < $cntw; $i++) {		// loop เพื่อ เติมค่า ความกว้างที่ไม่ได้ใส่มา ให้เต็ม หน้า (ความกว้าง)
							if (!$arr_width[$i])
								$arr_width[$i] = $setw;
						}
					}
					$breakW = 0;  $firstIdx = -1; $aw = 0; $breakA = ""; $sumw = 0;
//					echo "bf w-".implode(",",$arr_width)."<br>";
					for($i = 0; $i < $cntw; $i++) {		// loop เพื่อเติมค่าความกว้าง ใน column ที่ความกว้างเป็นแบบแบ่ง (-) กับตัวหน้า
//						if ($arr_width[$i]=="-" && $arr_text[$i]) {
						if ($arr_width[$i]=="-") {
							$font_set = explode("|",$arr_font[$i]);
							$this->SetFont($font_set[0],$font_set[1],$font_set[2]);
							$aw = $this->GetStringWidth( $arr_text[$i] ); 
							$arr_width[$i] = $aw;
							$sumw += $aw;
//							echo "w='-'..i=$i .. (".$arr_text[$i].")..getw=$aw , sumw=$sumw (breakA=$breakA)<br>";
							$arr_align[$i] = $breakA;	// ตัวที่เป็น - ค่า align ให้เท่ากับตัวแรกสุด
						} else if ($arr_width[$i]) {
							if ($sumw > 0) {
								if ($breakA=="L") {	// ชิดซ้ายทั้งชุด
									$font_set = explode("|",$arr_font[$firstIdx]);
									$this->SetFont($font_set[0],$font_set[1],$font_set[2]);
									$aw = $this->GetStringWidth( $arr_text[$firstIdx] ); 	// ความกว้างเฉพาะ text ของตัวแรกสุด
									$sumw += $aw;	// รวมความกว้างเฉพาะ text  ของทั้งชุด
									$rest = $arr_width[$firstIdx] - $sumw;	// ที่ว่างที่เหลือ = ความกว้างกำหนด หัก ความกว้างเฉพาะ text
//									echo "w='".$arr_width[$i-1]."'..$i-1=".($i-1)." .. (".$arr_text[$i].")..firstIdx=$firstIdx,  getw=$aw , sumw=$sumw (breakA=$breakA)<br>";
									$arr_width[$i-1] += $rest;	// ที่ว่างที่เหลือไปเพิ่มที่ ชุดสุดท้าย เพื่อผลักให้ทั้งชุดไปทางซ้าย
									$arr_width[$firstIdx] = $aw;	// ความกว้างตั้งต้น ให้เหลือกว้างเฉพาะ text ตั้งต้น
								} else if ($breakA=="C") {
									$font_set = explode("|",$arr_font[$firstIdx]);
									$this->SetFont($font_set[0],$font_set[1],$font_set[2]);
									$aw = $this->GetStringWidth( $arr_text[$firstIdx] ); 	// ความกว้างเฉพาะ text ของตัวแรกสุด
									$sumw += $aw;	// รวมความกว้างเฉพาะ text  ของทั้งชุด
									$rest = $arr_width[$firstIdx] - $sumw;	// ที่ว่างที่เหลือ = ความกว้างกำหนด หัก ความกว้างเฉพาะ text
//									echo "w='".$arr_width[$i-1]."'..$i-1=".($i-1)." .. (".$arr_text[$i].")..firstIdx=$firstIdx,  getw=$aw , sumw=$sumw (breakA=$breakA)<br>";
									$arr_width[$i-1] += ($rest /2);	// ครึ่งหนึ่งของที่ว่างที่เหลือไปเพิ่มที่ ชุดสุดท้าย เพื่อผลักให้ทั้งชุดอยู่กลาง
									$arr_align[$i-1] = "L";	// column สุดท้าย ให้ชิดซ้าย
									$arr_width[$firstIdx] = ($rest /2)+$aw;	// ความกว้างตั้งต้น = อีกครื่งหนึ่งของที่ว่างที่เหลือ บวก กับความกว้างตั้งต้น เฉพาะ text ตั้งต้น เพื่อให้อยู่ตรงกลาง
									$arr_align[$firstIdx] = "R";	// column แรกสุด ให้ชิดขวา
								} else {	 // Align == 'R' ชิดขวา
///									echo "w='".$arr_width[$i-1]."'..$i-1=".($i-1)." .. (".$arr_text[$i].")..firstIdx=$firstIdx,  w_firstidx=".$arr_width[$firstIdx]." , sumw=$sumw (breakA=$breakA)<br>";
									$arr_width[$firstIdx] -= $sumw;	// ความกว้างตั้งต้น = ค่าตั้งต้นหัก ผลรวมกับค่าความกว้างเฉพาะ text ที่เหลือทั้งหมด
//									echo "breakA = R .. firstIdx=$firstIdx, arr_width=".$arr_width[$firstIdx].", sumw=$sumw<br>";
								}
							} // end if sumw > 0
//							echo "width (".$arr_width[$i].") ..  (getw=$aw), i=$i , sumw=$sumw , breakA=$breakA<br>";
							$breakW = (float)$arr_width[$i]; // เก็บค่า w รวม ของ col ต้น ของ col ประกอบความกว้างไว้ก่อน
							$breakA = $arr_align[$i];
							$sumw = 0;
							$firstIdx = $i;
						}
					} // end loop for $i
					if ($sumw > 0) {
						if ($breakA=="L") {	// ชิดซ้ายทั้งชุด
//							echo "Last breakA = L .. firstIdx=$firstIdx, arr_width=".$arr_width[$firstIdx].", sumw=$sumw<br>";
							$font_set = explode("|",$arr_font[$firstIdx]);
							$this->SetFont($font_set[0],$font_set[1],$font_set[2]);
							$aw = $this->GetStringWidth( $arr_text[$firstIdx] ); 	// ความกว้างเฉพาะ text ของตัวแรกสุด
							$sumw += $aw;	// รวมความกว้างเฉพาะ text  ของทั้งชุด
							$rest = $arr_width[$firstIdx] - $sumw;	// ที่ว่างที่เหลือ = ความกว้างกำหนด หัก ความกว้างเฉพาะ text
//							echo "last w='".$arr_width[$i-1]."'..$i-1=".($i-1)." .. (".$arr_text[$firstIdx].")..firstIdx=$firstIdx,  getw=$aw , sumw=$sumw (breakA=$breakA)<br>";
							$arr_width[$i-1] += $rest;	// ที่ว่างที่เหลือไปเพิ่มที่ ชุดสุดท้าย เพื่อผลักให้ทั้งชุดไปทางซ้าย
							$arr_width[$firstIdx] = $aw;	// ความกว้างตั้งต้น ให้เหลือกว้างเฉพาะ text ตั้งต้น
						} else if ($breakA=="C") {
//							echo "Last breakA = C .. firstIdx=$firstIdx, arr_width=".$arr_width[$firstIdx].", sumw=$sumw<br>";
							$font_set = explode("|",$arr_font[$firstIdx]);
							$this->SetFont($font_set[0],$font_set[1],$font_set[2]);
							$aw = $this->GetStringWidth( $arr_text[$firstIdx] ); 	// ความกว้างเฉพาะ text ของตัวแรกสุด
							$sumw += $aw;	// รวมความกว้างเฉพาะ text  ของทั้งชุด
							$rest = $arr_width[$firstIdx] - $sumw;	// ที่ว่างที่เหลือ = ความกว้างกำหนด หัก ความกว้างเฉพาะ text
//							echo "last w='".$arr_width[$i-1]."'..$i-1=".($i-1)." .. (".$arr_text[$firstIdx].")..firstIdx=$firstIdx,  getw=$aw , sumw=$sumw (breakA=$breakA)<br>";
							$arr_width[$i-1] += ($rest /2);	// ครึ่งหนึ่งของที่ว่างที่เหลือไปเพิ่มที่ ชุดสุดท้าย เพื่อผลักให้ทั้งชุดอยู่กลาง
							$arr_align[$i-1] = "L";	// column สุดท้าย ให้ชิดซ้าย
							$arr_width[$firstIdx] = ($rest /2)+$aw;	// ความกว้างตั้งต้น = อีกครื่งหนึ่งของที่ว่างที่เหลือ บวก กับความกว้างตั้งต้น เฉพาะ text ตั้งต้น เพื่อให้อยู่ตรงกลาง
							$arr_align[$firstIdx] = "R";	// column แรกสุด ให้ชิดขวา
						} else {	 // Align == 'R' ชิดขวา
//							echo "last w='".$arr_width[$i-1]."'..$i-1=".($i-1)." .. (".$arr_text[$firstIdx].")..firstIdx=$firstIdx,  w_firstidx=".$arr_width[$firstIdx]." , sumw=$sumw (breakA=$breakA)<br>";
//							echo "Last breakA = R .. firstIdx=$firstIdx, arr_width=".$arr_width[$firstIdx].", sumw=$sumw<br>";
							$arr_width[$firstIdx] -= $sumw;	// ความกว้างตั้งต้น = ค่าตั้งต้นหัก ผลรวมกับค่าความกว้างเฉพาะ text ที่เหลือทั้งหมด
						}
//						echo "Last width (".$arr_width[$i-1].") ..  i=".($i-1)." , sumw=$sumw , breakA=$breakA<br>";
					} // end if sumw > 0
//					echo "af w-".implode(",",$arr_width)." text=(".implode(",",$arr_text).")<br>";
				} else {	// ถ้าไม่ได้กำหนดความกว้างมา คำนวณหาค่า โดยประมาณของ text ชุดที่ไม่มีค่า คือ = ""
					$cnt = count($arr_text);
					$cnt_zero = 0; $sw = 0;
					for ($i=0; $i < $cnt; $i++) {
						if ($arr_text[$i]) {
							$font_set = explode("|",$arr_font[$i]);
							$this->SetFont($font_set[0],$font_set[1],$font_set[2]);
							$sw += $this->GetStringWidth( $arr_text[$i] ); 
						} else $cnt_zero++;
					}
					$rest = $page_w - $sw;
					$setw = floor($rest / $cnt_zero);	// เก็บเอาไว้ไปกำหนดค่าความกว้าง กรณี text column ใด ๆ เป็น ""
				}
//				echo "page_w=$page_w<br>";
				if (!$line_h) $line_h = 7;
				$cnt = count($arr_text);
//				echo "$cnt :: ".count($arr_border)."==".count($arr_font)."<br>";
//				echo "$cnt :: text=".implode(",",$arr_text)."  width=".implode(",",$arr_width)."<br>";
				if ($cnt == count($arr_border) && $cnt == count($arr_font)) {
					for ($i=0; $i < $cnt; $i++) {
//						echo "$i--text:".$arr_text[$i]." font:".$arr_font[$i]."<br>";
						$font_set = explode("|",$arr_font[$i]);
						$this->SetFont($font_set[0],$font_set[1],$font_set[2]);
						if (!$font_set[3]) $font_set[3]="000000";
						$this->SetTextColor( hexdec(substr($font_set[3],0,2)),hexdec(substr($font_set[3],2,2)),hexdec(substr($font_set[3],4,2)) );
//						if (!$font_set[4]) $font_set[4]="FFFFFF";
						$f_fill = 0;
						if ($font_set[4] && strlen($font_set[4])==6)  {
							$this->SetFillColor( hexdec(substr($font_set[4],0,2)),hexdec(substr($font_set[4],2,2)),hexdec(substr($font_set[4],4,2)) );
//							echo "setFill_=".$font_set[4]."<br>";
							$f_fill = 1;
						}
						if ($font_set[5])  { // rotate angle
							$this->Rotate((int)$font_set[5]); 
						}
//						$astr = (($NUMBER_DISPLAY==2)?convert2thaidigit($arr_text[$i]):$arr_text[$i]);
						$astr = $arr_text[$i];
						if ($arr_width[$i]) {
							$aw = $arr_width[$i];
						} else if ($arr_text[$i]) {
							$aw = $this->GetStringWidth( $astr ); 
						} else $aw = $setw;	// 	กรณีที่ text ไม่มีค่า คือ = ""
//						$rest = $page_w - $aw;
//						echo "w=$aw  text=$astr   align=".$arr_align[$i]."<br>";
						if ($aw)	$this->Cell($aw,$line_h,$astr,$arr_border[$i],($i==($cnt-1)?1:0),$arr_align[$i], $f_fill);
					} // end for loop
					return true;
				} else {
					return false;
				}
			} // end function add_lineFreeTab
			
			function Rotate($angle,$x=-1,$y=-1) { 
				if($x==-1) 
					$x=$this->x; 
				if($y==-1) 
					$y=$this->y; 
				if($this->angle!=0) 
					$this->_out('Q'); 
				$this->angle=$angle; 
				if($angle!=0) { 
					$angle*=M_PI/180; 
					$c=cos($angle); 
					$s=sin($angle); 
					$cx=$x*$this->k; 
					$cy=($this->h-$y)*$this->k; 
             
					$this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
				} 
    		}  // end function Rotate

		}
		// end class
?>