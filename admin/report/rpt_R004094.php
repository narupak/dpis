<?php
	include("../../php_scripts/connect_database.php");
	if (!$FLAG_RTF) include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");
        
        class PDF_MC_Table extends PDF {
            var $widths;
            var $aligns;
            var $fontx;
            function SetWidths($w){
                //Set the array of column widths
                $this->widths=$w;
            }
            function Setfont_header($fonts){
                //Set the array of column widths
                $this->fontx=$fonts;
            }
            function Header(){
                //$font = $this->FontFamily;
                if (!$fontx){ 
                    $font=$this->FontFamily; 
                }else{ 
                    $font = $fontx;
                }
                
                //$this->Open();
                
                $fontstyle = $this->FontStyle;
                $fontsize = $this->FontSizePt;
                //echo "Header font=".$font." size=".$this->FontSizePt."<br>";
                $this->SetTextColor(0,0,96);
                $this->SetFont($font,'b',$fontsize);
                if($this->report_title){		
                        $t_title = explode("||",$this->report_title);
                        if(count($t_title) == 1){      
                            $sub_t_title = explode("^", $t_title[0]);
                            if ($sub_t_title[1]) $align = $sub_t_title[1];
                            if (!$align) $align = "C";
                            $this->SetFont($font,$fontstyle,$fontsize);
                            $this->Cell(0,6,$sub_t_title[0],0,1,$align);
                        }else{		
                            for($i=0; $i < count($t_title); $i++ ){
                                $align = "";
                                $sub_t_title = explode("^", $t_title[$i]);
                                if($i == (count($t_title) - 1)){
                                    $sub_t_title = explode("^", $t_title[$i]);
                                    if ($sub_t_title[1]) $align = $sub_t_title[1];
                                    if (!$align) $align = "C";
                                    $this->SetFont($font,$fontstyle,$fontsize);
                                    $this->Cell(0,6,$sub_t_title[0],0,1,$align);
                                }else{		
                                    $sub_t_title = explode("^", $t_title[$i]);
                                    if ($sub_t_title[1]) $align = $sub_t_title[1];
                                    if (!$align) $align = "C";
                                    $this->Cell(0,6,$sub_t_title[0],0,1,$align);
                                } // end if
                                } // end for
                        } // end if

                        $this->SetTextColor(0,0,0);
                        if ($fontsize-4 < 10)
                                $this->SetFont($font,'',10); // บังคับไม่ให้ มี style
                        else
                            $this->SetFont($font,'',$fontsize-4); // บังคับไม่ให้ มี style
                        $this->Cell(0,5, $this->company_name,0,1,'L');
                }   //  if      ($this->report_title)

                if($this->heading){
                    $heading_width_merge=$this->widths[8]+$this->widths[9]+$this->widths[10]+$this->widths[11]+$this->widths[12]+$this->widths[13]+$this->widths[14];
                    //$this->line($this->lMargin,$this->y,$this->w - $this->rMargin ,$this->y);
                    $this->SetFont($font,'b',14);
                    $this->Cell($this->widths[0] ,7,"ลำดับที่",'LTR',0,'C',0);
                    $this->Cell($this->widths[1] ,7,"ชื่อ - สกุล",'LTR',0,'C',0);
                    $this->Cell($this->widths[2] ,7,"ตำแหน่ง",'LTR',0,'C',0);
                    $this->Cell($this->widths[3] ,7,"ตำแหน่งประเภท",'LTR',0,'C',0);
                    $this->Cell($this->widths[4] ,7,"เลขที่",'LTR',0,'C',0);
                    $this->Cell($this->widths[5] ,7,"สำนัก/กอง",'LTR',0,'C',0);
                    $this->Cell($this->widths[6] ,7,"ต่ำกว่าสำนัก/กอง 1 ระดับ",'LTR',0,'C',0);
                    $this->Cell($this->widths[7] ,7,"ต่ำกว่าสำนัก/กอง 2 ระดับ",'LTR',0,'C',0);
                    $this->Cell($heading_width_merge ,7,"ใบอนุญาตประกอบวิชาชีพปัจจุบัน",'LTR',1,'C',0);

                    //$pdf->y -= 7;
                    $this->Cell($this->widths[0] ,7,"",'LBR',0,'C',0);
                    $this->Cell($this->widths[1] ,7,"",'LBR',0,'C',0);
                    $this->Cell($this->widths[2] ,7,"",'LBR',0,'C',0);
                    $this->Cell($this->widths[3] ,7,"",'LBR',0,'C',0);
                    $this->Cell($this->widths[4] ,7,"ตำแหน่ง",'LBR',0,'C',0);
                    $this->Cell($this->widths[5] ,7,"",'LBR',0,'C',0);
                    $this->Cell($this->widths[6] ,7,"",'LBR',0,'C',0);
                    $this->Cell($this->widths[7] ,7,"",'LBR',0,'C',0);
                    $this->Cell($this->widths[8] ,7,"ชื่อใบประกอบวิชาชีพ",'LTBR',0,'C',0);
                    $this->Cell($this->widths[9] ,7,"ประเภท/ระดับของใบอนุญาต",'LTBR',0,'C',0);
                    $this->Cell($this->widths[10],7,"สาขา",'LTBR',0,'C',0);
                    $this->Cell($this->widths[11],7,"เลขที่ใบอนุญาต",'LTBR',0,'C',0);
                    $this->Cell($this->widths[12],7,"ต่ออายุครั้งที่",'LTBR',0,'C',0);
                    $this->Cell($this->widths[13],7,"วันที่ออกใบอนุญาต",'LTBR',0,'C',0);
                    $this->Cell($this->widths[14],7,"วันที่หมดอายุ",'LTBR',1,'C',0);   
                }
                $this->SetTextColor(0, 0, 0);
                $this->flag_new_page = 'Y';
            }

            function SetAligns($a){
                //Set the array of column alignments
                $this->aligns=$a;
            }

            function Row($data,$merge=0){
                //Calculate the height of the row
                $nb=0;
                for($i=0;$i<count($data);$i++)
                    $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
                $h=7*$nb;
                //Issue a page break first if needed
                $this->CheckPageBreak($h);
                //Draw the cells of the row
                if($merge){
                    for($i=0;$i<count($data);$i++){
                        if($i==0){
                            $w=$this->widths[0]+$this->widths[1]+$this->widths[2]+$this->widths[3]+$this->widths[4]+$this->widths[5]+$this->widths[6]+$this->widths[7];
                        }else{
                            $w=$this->widths[($i+7)];
                        }    
                        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                        //Save the current position
                        $x=$this->GetX();
                        $y=$this->GetY();
                        //Draw the border
                        $this->Rect($x,$y,$w,$h);
                        //Print the text
                        $this->MultiCellThaiCut($w,7,$data[$i],0,$a);
                        //Put the position to the right of the cell
                        $this->SetXY($x+$w,$y);
                    }
                //Go to the next line
                }else{
                    for($i=0;$i<count($data);$i++){
                        $w=$this->widths[$i];
                        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                        //Save the current position
                        $x=$this->GetX();
                        $y=$this->GetY();
                        //Draw the border
                        $this->Rect($x,$y,$w,$h);
                        //Print the text
                        $this->MultiCellThaiCut($w,7,$data[$i],0,$a);
                        //Put the position to the right of the cell
                        $this->SetXY($x+$w,$y);
                    }
                //Go to the next line
                }
                $this->Ln($h);
            }

            function CheckPageBreak($h){
                //If the height h would cause an overflow, add a new page immediately
                if($this->GetY()+$h>$this->PageBreakTrigger)
                    $this->AddPage($this->CurOrientation);
            }

            function NbLines($w,$txt){
                //Computes the number of lines a MultiCell of width w will take
                $cw=&$this->CurrentFont['cw'];
                if($w==0)
                    $w=$this->w-$this->rMargin-$this->x;
                $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
                $s=str_replace("\r",'',$txt);
                $nb=strlen($s);
                if($nb>0 and $s[$nb-1]=="\n")
                    $nb--;
                $sep=-1;
                $i=0;
                $j=0;
                $l=0;
                $nl=1;
                while($i<$nb)
                {
                    $c=$s[$i];
                    if($c=="\n")
                    {
                        $i++;
                        $sep=-1;
                        $j=$i;
                        $l=0;
                        $nl++;
                        continue;
                    }
                    if($c==' ')
                        $sep=$i;
                    $l+=$cw[$c];
                    if($l>$wmax)
                    {
                        if($sep==-1)
                        {
                            if($i==$j)
                                $i++;
                        }
                        else
                            $i=$sep+1;
                        $sep=-1;
                        $j=$i;
                        $l=0;
                        $nl++;
                    }
                    else
                        $i++;
                }
                return $nl;
            }
        }

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $LEVEL_NO, $PL_CODE;
		global $line_code;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
                    $REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
                    switch($REPORT_ORDER){
                        case "MINISTRY" :
                            if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(ORG.ORG_ID_REF = $MINISTRY_ID)";
                            break;
                        case "DEPARTMENT" : 
                            if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
                            break;
                        case "ORG" :	
                            if($select_org_structure==0){
                                if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
                                else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
                            }elseif($select_org_structure==1){
                                if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
                                else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
                            }
                        break;
                        case "LEVEL" :	
                                if($LEVEL_NO) $arr_addition_condition[] = "(trim(a.LEVEL_NO) = '". str_pad($LEVEL_NO, 2, "0", STR_PAD_LEFT) ."')";
                                else $arr_addition_condition[] = "(trim(a.LEVEL_NO) = '' or a.LEVEL_NO is null)";
                        break;
                        case "LINE" :	
                                if(trim($PL_CODE)) $arr_addition_condition[] = "(trim(PL.$line_code) = '$PL_CODE')";
                                else $arr_addition_condition[] = "(trim(PL.$line_code) = '$PL_CODE' or PL.$line_code is null)";
                        break;
                    } // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function

	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $LEVEL_NO, $PL_CODE;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
                    $REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
                    switch($REPORT_ORDER){
                        case "MINISTRY" :
                                $MINISTRY_ID = -1;
                                break;
                        case "DEPARTMENT" : 
                                $DEPARTMENT_ID = -1;
                                break;
                        case "ORG" :	
                                $ORG_ID = -1;
                        break;
                        case "LEVEL" :	
                                $LEVEL_NO = -1;
                        break;
                        case "LINE" :	
                                $PL_CODE = -1;
                        break;
                    } // end switch case
		} // end for
	} // function
        function GenerateWord()
        {
            //Get a random word
            $nb=rand(3,10);
            $w='';
            for($i=1;$i<=$nb;$i++)
                $w.=chr(rand(ord('a'),ord('z')));
            return $w;
        }

        function GenerateSentence()
        {
            //Get a random sentence
            $nb=rand(1,10);
            $s='';
            for($i=1;$i<=$nb;$i++)
                $s.=GenerateWord().' ';
            return substr($s,0,-1);
        }
        
        function getToFont($id){
		if($id==1){
                    $fullname	= 'angsa';
		}else if($id==2){
                    $fullname	= 'cordia';
		}else if($id==3){
                    $fullname	= 'thsarabun';
		}else{
                    $fullname	= 'browallia';
		}
		return $fullname;
	}

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        $db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        $db_dpis7 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
        $mgt_table='';
        $select_pm_name='';
        $pm_code='';
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
                $mgt_table = " , PER_MGT m";
                $select_pm_name =  " , m.PM_NAME ";
                $pm_code = " and b.PM_CODE=m.PM_CODE(+)";
                $pos_no = "b.POS_NO";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_code = "PL_CODE";
		$line_name = "PL_NAME";
		$line_short_name = "PL_SHORTNAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
		$line_title=" สายงาน";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
                $pos_no = "b.POEM_NO";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "PN_CODE";
		$line_name = "PN_NAME";	
		$line_search_code=trim($search_pn_code);
		$line_search_name =trim($search_pn_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
                $pos_no = "b.POEMS_NO";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "EP_CODE";
		$line_name = "EP_NAME";	
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
                $pos_no = "b.POT_NO";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "TP_CODE";
		$line_name = "TP_NAME";	
		$line_search_code =trim($search_tp_code);
		$line_search_name =trim($search_tp_name);
		$line_title=" ชื่อตำแหน่ง";
	} // end if
	
	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {
		$f_all = true; 
		$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
	} else $f_all = false;	
	
	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
            $REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
            switch($REPORT_ORDER){
                case "MINISTRY" :
                    if($select_list) $select_list .= ", ";
                    $select_list .= "g.ORG_ID_REF as MINISTRY_ID";	

                    if($order_by) $order_by .= ", ";
                     $order_by .= "g.ORG_ID_REF";

                    $heading_name .= " $MINISTRY_TITLE";
                    break;
                case "DEPARTMENT" : 
                    if($select_list) $select_list .= ", ";
                    $select_list .= "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID";

                    if($order_by) $order_by .= ", ";
                    $order_by .= "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID";

                    $heading_name .= " $DEPARTMENT_TITLE";
                    break; 
                case "ORG" :
                    if($select_list) $select_list .= ", ";
                    if($select_org_structure == 0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
                    else if($select_org_structure == 1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

                    if($order_by) $order_by .= ", ";
                    if($select_org_structure == 0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
                    else if($select_org_structure == 1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

                    $heading_name .= " $ORG_TITLE";
                    break;
                case "LEVEL" :
                    if($select_list) $select_list .= ", ";
                    $select_list .= "a.LEVEL_NO, i.LEVEL_NAME";

                    if($order_by) $order_by .= ", ";
                    $order_by .= "a.LEVEL_NO, i.LEVEL_NAME";

                    $heading_name .= " $LEVEL_TITLE";
                    break;       
                case "LINE" :
                    if($select_list) $select_list .= ", ";
                    $select_list .= "$line_code as PL_CODE";

                    if($order_by) $order_by .= ", ";
                    $order_by .= "$line_code";

                    $heading_name .=  $line_title;
                    break;
            } // end switch case
	} // end for
	if(!trim($order_by)){
		if($select_org_structure==0){ $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID"; 	}
		else if($select_org_structure==1){	$order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID"; }
	}
	if(!trim($select_list)){ 
		if($select_org_structure==0){ $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID"; }
		else if($select_org_structure==1){ $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID"; } 
	}

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	if(trim($search_level_no)) $arr_search_condition[] = "(a.LEVEL_NO = '". str_pad($search_level_no, 2, "0", STR_PAD_LEFT) ."')";

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG", $list_type)){
            $list_type_text = "";
            if($select_org_structure==0) {
                if(trim($search_org_id)){ 
                    $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
                    $list_type_text .= "$search_org_name";
                    $R_ORG_ID = "b.ORG_ID";
                } // end if
                if(trim($search_org_id_1)){ 
                    $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
                    $list_type_text .= "$search_org_name_1";
                    $R_ORG_ID = "b.ORG_ID_1";
                } // end if
                if(trim($search_org_id_2)){ 
                    $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
                    $list_type_text .= "$search_org_name_2";
                    $R_ORG_ID = "b.ORG_ID_2";
                } // end if
                if(trim($search_org_id_3)){ 
                    $arr_search_condition[] = "(b.ORG_ID_3 = $search_org_id_3)";
                    $list_type_text .= "$search_org_name_3";
                    $R_ORG_ID = "b.ORG_ID_3";
                } // end if
                if(trim($search_org_id_4)){ 
                    $arr_search_condition[] = "(b.ORG_ID_4 = $search_org_id_4)";
                    $list_type_text .= "$search_org_name_4";
                    $R_ORG_ID = "b.ORG_ID_4";
                } // end if
                if(trim($search_org_id_5)){ 
                    $arr_search_condition[] = "(b.ORG_ID_5 = $search_org_id_5)";
                    $list_type_text .= "$search_org_name_5";
                    $R_ORG_ID = "b.ORG_ID_5";
                } // end if
            }else{
                if(trim($search_org_ass_id)){ 
                    $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
                    $list_type_text .= "$search_org_ass_name";
                    $R_ORG_ID = "a.ORG_ID";
                } // end if
                if(trim($search_org_ass_id_1)){ 
                    $arr_search_condition[] = "(a.ORG_ID_1 = $search_org_ass_id_1)";
                    $list_type_text .= "$search_org_ass_name_1";
                    $R_ORG_ID = "a.ORG_ID_1";
                } // end if
                if(trim($search_org_ass_id_2)){ 
                    $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_ass_id_2)";
                    $list_type_text .= "$search_org_ass_name_2";
                    $R_ORG_ID = "a.ORG_ID_2";
                } // end if
                if(trim($search_org_ass_id_3)){ 
                    $arr_search_condition[] = "(a.ORG_ID_3 = $search_org_ass_id_3)";
                    $list_type_text .= "$search_org_ass_name_3";
                    $R_ORG_ID = "a.ORG_ID_3";
                } // end if
                if(trim($search_org_ass_id_4)){ 
                    $arr_search_condition[] = "(a.ORG_ID_4 = $search_org_ass_id_4)";
                    $list_type_text .= "$search_org_ass_name_4";
                    $R_ORG_ID = "a.ORG_ID_4";
                            } // end if
                if(trim($search_org_ass_id_5)){ 
                    $arr_search_condition[] = "(a.ORG_ID_5 = $search_org_ass_id_5)";
                    $list_type_text .= "$search_org_ass_name_5";
                    $R_ORG_ID = "a.ORG_ID_5";
                } // end if
            }
	}
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
            $list_type_text = "";
            if($line_search_code){
                $arr_search_condition[] = "(trim(b.$line_code)='$line_search_code')";
                $list_type_text .= " $line_search_name";
            }
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
            $list_type_text = "";
            if(trim($search_ct_code)){ 
                $search_ct_code = trim($search_ct_code);
                $arr_search_condition[] = "(trim(c.CT_CODE) = '$search_ct_code')";
                $list_type_text .= "$search_ct_name";
            } // end if
            if(trim($search_pv_code)){ 
                $search_pv_code = trim($search_pv_code);
                $arr_search_condition[] = "(trim(c.PV_CODE) = '$search_pv_code')";
                $list_type_text .= " - $search_pv_name";
            } // end if
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
            if($DEPARTMENT_ID){
                $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
                $list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
            }elseif($MINISTRY_ID){
                $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
                if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                $db_dpis->send_cmd($cmd);
                while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

                $arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
                $list_type_text .= " - $MINISTRY_NAME";
            }elseif($PROVINCE_CODE){
                $PROVINCE_CODE = trim($PROVINCE_CODE);
                $arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
                $list_type_text .= " - $PROVINCE_NAME";
            } // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายชื่อ$PERSON_TYPE[$search_per_type] ตำแหน่ง สังกัด";
	$report_code = "R0494";
//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
        
        $unit="mm";
        //$paper_size="A4";
        $paper_size = array(300,545);//"A3";
        $lang_code="TH";
        $orientation='L';
        $heading=1;
        $font=getToFont($CH_PRINT_FONT);
        $heading_width = array("15","55","55","40","15","40","40","40","55","45","25","30","25","30","25");
        $heading_aligns = array('C','L','L','L','C','L','L','L','L','L','L','L','L','L','L');
        $pdf = new PDF_MC_Table($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
        $pdf->SetWidths($heading_width);
        $pdf->SetAligns($heading_aligns);
        $pdf->Open();
        $pdf->SetMargins(5,5,5);
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Setfont_header($font);
        $pdf->SetFont($font,'',14);
        $page_start_x = $pdf->x;$page_start_y = $pdf->y;
        
        if($DPISDB=="oci8"){
            $search_condition = str_replace(" where ", " and ", $search_condition);
            //if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
            $cmd =  "   with person as (
                            SELECT TRIM(L.LEVEL_SEQ_NO) AS XX ,A.PER_ID , PN.PN_NAME||A.PER_NAME||' '||A.PER_SURNAME AS FULLNAME , PL.$line_name AS PL_NAME ,
                                L.POSITION_LEVEL , L.POSITION_TYPE,
                                $pos_no AS POS_NO, ORG.ORG_NAME , ORG_1.ORG_NAME AS ORG_NAME_1, ORG_2.ORG_NAME AS ORG_NAME_2 ,A.PER_TYPE ,L.LEVEL_NAME as LEVEL_POTION 
                                $select_pm_name 
                            FROM PER_PERSONAL a , PER_PRENAME PN , $position_table b , $line_table PL , PER_LEVEL L, PER_ORG ORG, PER_ORG ORG_1,
                                PER_ORG ORG_2 $mgt_table
                            WHERE A.PN_CODE=PN.PN_CODE(+)
                            AND A.PER_ID IN (SELECT PER_ID FROM PER_LICENSEHIS)
                            AND $position_join(+)
                            AND  b.$line_code=pl.$line_code(+)
                            AND A.LEVEL_NO=L.LEVEL_NO(+)
                            AND b.ORG_ID=ORG.ORG_ID(+)
                            AND b.ORG_ID_1=ORG_1.ORG_ID(+)
                            AND b.ORG_ID_2=ORG_2.ORG_ID(+)
                            $pm_code
                            $search_condition
                      ),LICENSEHIS_1 AS (
                            SELECT LCH.LH_ID,LCH.LT_CODE,LCH.PER_ID,LCT.LT_NAME, LCH.LH_SUB_TYPE , LCH.LH_MAJOR , LCH.LH_LICENSE_NO , LCH.LH_SEQ_NO , LCH.LH_LICENSE_DATE ,
                                LCH.LH_EXPIRE_DATE
                            FROM PER_LICENSEHIS LCH , PER_LICENSE_TYPE LCT
                            WHERE LCH.LT_CODE=LCT.LT_CODE(+)
                            AND (LCH.LH_EXPIRE_DATE IS NULL  OR TO_DATE(LCH.LH_EXPIRE_DATE, 'YYYY-MM-DD') > SYSDATE)
                      ),LICENSEHIS_2 AS (
                            SELECT LCH.LH_ID,LCH.LT_CODE,LCH.PER_ID,LCT.LT_NAME, LCH.LH_SUB_TYPE , LCH.LH_MAJOR , LCH.LH_LICENSE_NO , LCH.LH_SEQ_NO , LCH.LH_LICENSE_DATE ,
                                LCH.LH_EXPIRE_DATE
                            FROM PER_LICENSEHIS LCH , PER_LICENSE_TYPE LCT
                            WHERE LCH.LT_CODE=LCT.LT_CODE(+)
                            AND (TO_DATE(LCH.LH_EXPIRE_DATE, 'YYYY-MM-DD') < SYSDATE)
                      ),CUR_LICENSEHIS AS (
                            SELECT  P.ID_PERSON AS PER_ID,
                                CASE WHEN (P.PER_ID IS NOT NULL) THEN P.LH_ID
                                    ELSE (SELECT  A.LH_ID  FROM (  SELECT  ROWNUM, A.PER_ID , A.LH_ID  FROM LICENSEHIS_2 A  ORDER BY A.LH_LICENSE_DATE DESC  ) A WHERE A.PER_ID = P.ID_PERSON AND ROWNUM = 1 )
                                    END AS LH_ID,
                                CASE WHEN (P.PER_ID IS NOT NULL) THEN P.LT_CODE
                                    ELSE (SELECT  A.LT_CODE  FROM (  SELECT  ROWNUM, A.PER_ID , A.LT_CODE  FROM LICENSEHIS_2 A  ORDER BY A.LH_LICENSE_DATE DESC  ) A WHERE A.PER_ID = P.ID_PERSON AND ROWNUM < 2)
                                    END AS LT_CODE,
                                CASE WHEN (P.PER_ID IS NOT NULL) THEN P.LT_NAME
                                    ELSE (SELECT  A.LT_NAME  FROM (  SELECT  ROWNUM, A.PER_ID , A.LT_NAME  FROM LICENSEHIS_2 A  ORDER BY A.LH_LICENSE_DATE DESC  ) A WHERE A.PER_ID = P.ID_PERSON AND ROWNUM < 2)
                                    END AS LT_NAME,
                                CASE WHEN (P.PER_ID IS NOT NULL) THEN P.LH_SUB_TYPE
                                    ELSE (SELECT  A.LH_SUB_TYPE  FROM (  SELECT  ROWNUM, A.PER_ID , A.LH_SUB_TYPE  FROM LICENSEHIS_2 A  ORDER BY A.LH_LICENSE_DATE DESC  ) A WHERE A.PER_ID = P.ID_PERSON AND ROWNUM < 2)
                                    END AS LH_SUB_TYPE,
                                CASE WHEN (P.PER_ID IS NOT NULL) THEN P.LH_MAJOR
                                    ELSE (SELECT  A.LH_MAJOR  FROM (  SELECT  ROWNUM, A.PER_ID , A.LH_MAJOR  FROM LICENSEHIS_2 A  ORDER BY A.LH_LICENSE_DATE DESC  ) A WHERE A.PER_ID = P.ID_PERSON AND ROWNUM < 2)
                                    END AS LH_MAJOR,
                                CASE WHEN (P.PER_ID IS NOT NULL) THEN P.LH_LICENSE_NO
                                    ELSE (SELECT  A.LH_LICENSE_NO  FROM (  SELECT  ROWNUM, A.PER_ID , A.LH_LICENSE_NO  FROM LICENSEHIS_2 A  ORDER BY A.LH_LICENSE_DATE DESC  ) A WHERE A.PER_ID = P.ID_PERSON AND ROWNUM < 2)
                                    END AS LH_LICENSE_NO,
                                CASE WHEN (P.PER_ID IS NOT NULL) THEN P.LH_SEQ_NO
                                    ELSE (SELECT  A.LH_SEQ_NO  FROM (  SELECT  ROWNUM, A.PER_ID , A.LH_SEQ_NO  FROM LICENSEHIS_2 A  ORDER BY A.LH_LICENSE_DATE DESC  ) A WHERE A.PER_ID = P.ID_PERSON AND ROWNUM < 2)
                                    END AS LH_SEQ_NO,
                                CASE WHEN (P.PER_ID IS NOT NULL) THEN P.LH_LICENSE_DATE
                                    ELSE (SELECT  A.LH_LICENSE_DATE  FROM (  SELECT  ROWNUM, A.PER_ID , A.LH_LICENSE_DATE  FROM LICENSEHIS_2 A  ORDER BY A.LH_LICENSE_DATE DESC  ) A WHERE A.PER_ID = P.ID_PERSON AND ROWNUM < 2)
                                    END AS LH_LICENSE_DATE,
                                CASE WHEN (P.PER_ID IS NOT NULL) THEN P.LH_EXPIRE_DATE
                                    ELSE (SELECT  A.LH_EXPIRE_DATE  FROM (  SELECT  ROWNUM, A.PER_ID , A.LH_EXPIRE_DATE  FROM LICENSEHIS_2 A  ORDER BY A.LH_LICENSE_DATE DESC  ) A WHERE A.PER_ID = P.ID_PERSON AND ROWNUM < 2)
                                    END AS LH_EXPIRE_DATE
                            FROM (
                                SELECT A.PER_ID AS ID_PERSON ,B.* FROM PER_PERSONAL A , LICENSEHIS_1 B
                                WHERE A.PER_ID IN (SELECT PER_ID FROM PER_LICENSEHIS)
                                AND A.PER_ID=B.PER_ID(+)
                            ) P
                      ),OLD_LICENSEHIS AS (
                          SELECT  LCH.LH_ID AS LH_ID_OLD ,LCH.LT_CODE AS LT_CODE_OLD ,LCH.PER_ID  AS PER_ID_OLD, LCT.LT_NAME AS LT_NAME_OLD, LCH.LH_SUB_TYPE AS  LH_SUB_TYPE_OLD,
                          LCH.LH_MAJOR AS LH_MAJOR_OLD , LCH.LH_LICENSE_NO AS LH_LICENSE_NO_OLD , LCH.LH_SEQ_NO AS LH_SEQ_NO_OLD ,
                          LCH.LH_LICENSE_DATE AS LH_LICENSE_DATE_OLD ,LCH.LH_EXPIRE_DATE AS LH_EXPIRE_DATE_OLD
                          FROM PER_LICENSEHIS LCH , PER_LICENSE_TYPE LCT
                          WHERE LCH.LT_CODE=LCT.LT_CODE(+)
                          AND (TO_DATE(LCH.LH_EXPIRE_DATE, 'YYYY-MM-DD') < SYSDATE)
                          AND LCH.PER_ID IN (SELECT A.PER_ID FROM CUR_LICENSEHIS A WHERE A.LT_CODE = LCH.LT_CODE AND A.LH_LICENSE_NO=LCH.LH_LICENSE_NO AND A.LH_ID != LCH.LH_ID)
                      ),ALL_LICENSEHIS AS (
                            SELECT A.*,B.* FROM
                                CUR_LICENSEHIS A ,
                                OLD_LICENSEHIS B
                            WHERE A.PER_ID=B.PER_ID_OLD(+)
                            AND A.LT_CODE=B.LT_CODE_OLD(+)
                      ) SELECT PS.* ,HIS.* FROM PERSON PS , ALL_LICENSEHIS HIS
                        WHERE PS.PER_ID=HIS.PER_ID
                        ORDER BY XX DESC , PS.POS_NO ASC, HIS.LH_LICENSE_DATE DESC , LH_EXPIRE_DATE DESC ";
        }
        if($select_org_structure==1) { 
            $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
            $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
        
        $count_data = $db_dpis->send_cmd($cmd);
        //	$db_dpis->show_error();
        //      echo '<pre>'.$cmd;
        //      die();
        if($count_data){
            $data_count=1;
            $PER_ID_OLD=-1;
            while($data = $db_dpis->get_array()){
                $PER_ID = $data[PER_ID];
                $PER_FULLNAME = $data[FULLNAME];
                $PL_NAME = trim($data[PL_NAME]);
                $PER_TYPE = trim($data[PER_TYPE]);
                $POSITION_LEVEL = trim($data[POSITION_LEVEL]);
                $POSITION_TYPE = trim($data[POSITION_TYPE]);
                $LEVEL_POTION = trim($data[LEVEL_POTION]);
                $PM_NAME =  trim($data[PM_NAME]);
                /*แก้เรื่อง พนักงานราชการไม่ควรมีระดับตำแหน่งต่อท้ายตำแหน่ง */
                if($PER_TYPE==3){
                        //$POSITION_TYPE = $POSITION_LEVEL;
                        $POSITION_TYPE = $LEVEL_POTION;
                        $POSITION_LEVEL = "";
                }
                
                if($PM_NAME){ $PL_NAME = $PM_NAME ."(".$PL_NAME.")";}else{$PL_NAME = $PL_NAME.$POSITION_LEVEL;}
                $POS_NO = trim($data[POS_NO]);
                $ORG_NAME = trim($data[ORG_NAME]);
                $ORG_NAME_1 = trim($data[ORG_NAME_1]);
                $ORG_NAME_2 = trim($data[ORG_NAME_2]);
                $LH_ID = trim($data[LH_ID]);
                $LT_CODE = trim($data[LT_CODE]);
                $LT_NAME = trim($data[LT_NAME]);
                $LH_SUB_TYPE = trim($data[LH_SUB_TYPE]);
                $LH_MAJOR = trim($data[LH_MAJOR]);
                $LH_LICENSE_NO = trim($data[LH_LICENSE_NO]);
                $LH_SEQ_NO = trim($data[LH_SEQ_NO]);
                $LH_LICENSE_DATE = (trim($data[LH_LICENSE_DATE]))?show_date_format(trim($data[LH_LICENSE_DATE]), $DATE_DISPLAY):'';
                $LH_EXPIRE_DATE = (trim($data[LH_EXPIRE_DATE]))?show_date_format(trim($data[LH_EXPIRE_DATE]), $DATE_DISPLAY):'';
                
                if($PER_ID!=$PER_ID_OLD){
                    $PER_ID_OLD=$PER_ID;
                    $pdf->Row(array($data_count,$PER_FULLNAME,$PL_NAME,$POSITION_TYPE,$POS_NO,$ORG_NAME,$ORG_NAME_1,$ORG_NAME_2,$LT_NAME,$LH_SUB_TYPE,$LH_MAJOR,$LH_LICENSE_NO,$LH_SEQ_NO,$LH_LICENSE_DATE,$LH_EXPIRE_DATE));
                    $data_count++;
                }else{
                    //$merge = 7;
                    //$pdf->Row(array('',$LT_NAME,$LH_SUB_TYPE,$LH_MAJOR,$LH_LICENSE_NO,$LH_SEQ_NO,$LH_LICENSE_DATE,$LH_EXPIRE_DATE),$merge);
                    $pdf->Row(array('','','','','','','','',$LT_NAME,$LH_SUB_TYPE,$LH_MAJOR,$LH_LICENSE_NO,$LH_SEQ_NO,$LH_LICENSE_DATE,$LH_EXPIRE_DATE));
                } 
            } // end while
        }else{
            $pdf->SetFont($font,'b',14);
            $pdf->Cell(535 ,8,"********** ไม่มีข้อมูล **********",'LBR',0,'C',0);
        }
        //Table with 20 rows and 4 columns
        /*if($count_data){
            srand(microtime()*1000000);
            for($i=0;$i<20;$i++)
                $pdf->Row(array(GenerateSentence(),GenerateSentence(),GenerateSentence(),GenerateSentence(),GenerateSentence(),GenerateSentence(),GenerateSentence(),GenerateSentence(),GenerateSentence(),GenerateSentence(),GenerateSentence(),GenerateSentence(),GenerateSentence(),GenerateSentence(),GenerateSentence()));
        }*/
        
        $pdf->close_tab(""); 

        $pdf->close();
        $pdf->Output();	
        