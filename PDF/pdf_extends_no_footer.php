<?

		class PDF extends FPDF  
		{
			function Open()
			{
				// extend from function Open() 
				FPDF::Open();
				// Add fonts use in document
				$this->AddFont('angsab','','../../PDF/font/angsab.php');
				$this->AddFont('angsa','','../../PDF/font/angsa.php');
			}

			function Header()
			{
 				$this->SetTextColor(0,0,96);
 			    $this->SetFont('angsab','',14);
				if      ($this->report_title)
				{		$t_title = explode("||",$this->report_title);
						if     (count($t_title) == 1)
						{       //     มี heading report แค่ 1 line  -->  ชื่อรายงาน
								$this->Cell(0,6,$t_title[0],0,0,'C');
								$this->x = $this-> w -  55;
								$this->Cell(50,6,$this->report_code,0,1,'R');
						}
						else
						{		//     มี heading report  > 1 line  -->  มีชื่องานกิจกรรม ตามด้วย ชื่อรายงาน และ heading อื่น ๆ
								for  ($i=0; $i < count($t_title); $i++ )
									{		if   ($i ==0)
												{		$this->Cell(0,6,$t_title[$i],0,0,'L');
														$this->x = $this-> w -  55;
														$this->Cell(50,6,$this->report_code,0,1,'R');
												}
											else
												{		$this->SetFont('angsab','',16);
														$this->SetTextColor(98, 0, 0);
														$this->Cell(0,6,$t_title[$i],0,1,'C');
												}
									}
						}
				
					$this->SetTextColor(108);
					$this->line($this->lMargin,$this->y,$this->w - $this->rMargin ,$this->y);
				}   //  			if      ($this->report_title)
				
				if   ($this->heading)
				{
						$this->SetTextColor(0, 0, 0);
						$this->ReportHeader($this->heading,$this->heading_width,$this->heading_align,$this->heading_border);
						$this->SetTextColor(108);
						$this->line($this->lMargin,$this->y,$this->w - $this->rMargin ,$this->y);
					}
				$this->SetTextColor(0, 0, 0);
				$this->flag_new_page = 'Y';
			}

			function Footer()
			{
			}		

			function ReportHeader($heading,$width,$align,$border)
			{
				//Column widths
				// $w=array(40,35,40,45);
				//Header
				if  (!$border)   $border=0;
				$this->SetFont('angsab','',14);
				for($i=0;$i<count($heading);$i++)
					$this->Cell($width[$i],7,$heading[$i],$border,$align[$i]);
				$this->ln();
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

		} // end class
?>