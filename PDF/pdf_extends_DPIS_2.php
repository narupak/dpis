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
				}else{
					$this->AddFont('angsab','','../../PDF/font/angsab_old.php');
					$this->AddFont('angsa','','../../PDF/font/angsa_old.php');
				}
			}

			function Header()
			{
 				$this->SetTextColor(0,0,96);
 			    $this->SetFont('angsab','',14);

				if($this->report_title){		
					$t_title = explode("||",$this->report_title);
					if(count($t_title) == 1){      
						 //     Се heading report сЄш 1 line
		 			    $this->SetFont('angsab','',14);
						$this->Cell(0,6,$t_title[0],0,1,'C');

						if($this->company_name){ 
							$this->x = $this-> w -  $this->rMargin - 20;

			 			    $this->SetFont('angsa','',10);
							$this->Cell(20,6,$this->company_name,0,1,'L');
						} // end if
					}else{		
						//     Се heading report  > 1 line
						for($i=0; $i < count($t_title); $i++ ){
//							if($i == (count($t_title) - 1)){		
							if($i == 0){		
								if($this->company_name){ 
									$this->SetFont('angsab','',14);
									$this->Cell(0,6,$t_title[$i],0,0,'C');

									$this->x = $this-> w -  $this->rMargin - 20;
		
									$this->SetFont('angsa','',12);
									$this->Cell(20,6,$this->company_name,0,1,'R');
								}else{
									$this->SetFont('angsab','',14);
									$this->Cell(0,6,$t_title[$i],0,1,'C');
								} // end if
							}else{		
				 			    $this->SetFont('angsab','',14);
								$this->Cell(0,6,$t_title[$i],0,1,'C');
							} // end if
						} // end for
					} // end if				
				}   //  			if      ($this->report_title)
				
				$this->SetTextColor(0, 0, 0);
				$this->flag_new_page = 'Y';
			}

			function Footer()
			{	
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

		} // class
?>