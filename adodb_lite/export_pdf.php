<html>
<head>
<title>ThaiCreate.Com PHP PDF</title>
</head>
<body>

<?php
require_once 'adodb.inc.php'; 
require_once 'fpdf/fpdf.php';

class PDF extends FPDF
{
//Load data
function LoadData($file)
{
	//Read file lines
	$lines=file($file);
	$data=array();
	foreach($lines as $line)
		$data[]=explode(';',chop($line));
	return $data;
}

//Simple table
function BasicTable($header,$data)
{
	//Header
	$w=array(30,30,55,25,20,20);
	//Header
	for($i=0;$i<count($header);$i++)
		$this->Cell($w[$i],7,$header[$i],1,0,'C');
	$this->Ln();
	//Data
	//, field_type, field_null,field_desc,field_index,field_refer
	foreach ($data as $eachResult) 
	{
		$this->Cell(30,6,$eachResult["field_name"],1);
		$this->Cell(30,6,$eachResult["field_type"],1);
		$this->Cell(55,6,$eachResult["field_null"],1);
		$this->Cell(25,6,$eachResult["field_desc"],1,0,'C');
		$this->Cell(20,6,$eachResult["field_index"],1);
		$this->Cell(20,6,$eachResult["field_refer"],1);
		$this->Ln();
	}
}

//Better table
function ImprovedTable($header,$data)
{
	//Column widths
	$w=array(20,30,55,25,25,25);
	//Header
	for($i=0;$i<count($header);$i++)
		$this->Cell($w[$i],7,$header[$i],1,0,'C');
	$this->Ln();
	//Data

	foreach ($data as $eachResult) 
	{
		$this->Cell(20,6,$eachResult["CustomerID"],1);
		$this->Cell(30,6,$eachResult["Name"],1);
		$this->Cell(55,6,$eachResult["Email"],1);
		$this->Cell(25,6,$eachResult["CountryCode"],1,0,'C');
		$this->Cell(25,6,number_format($eachResult["Budget"],2),1,0,'R');
		$this->Cell(25,6,number_format($eachResult["Budget"],2),1,0,'R');
		$this->Ln();
	}
	//Closure line
	$this->Cell(array_sum($w),0,'','T');
}

//Colored table
function FancyTable($header,$data)
{
	//Colors, line width and bold font
	$this->SetFillColor(235,235,235);
	$this->SetTextColor(0);
	$this->SetDrawColor(0,0,0);
	$this->SetLineWidth(.3);
	$this->SetFont('','B');
	//Header
	$w=array(30,30,15,45,15,45);
	for($i=0;$i<count($header);$i++)
		$this->Cell($w[$i],7,$header[$i],1,0,'C',true);
	$this->Ln();
	//Color and font restoration
	$this->SetFillColor(224,235,255);
	$this->SetTextColor(0);
	$this->SetFont('');
	//Data
	$fill=false;
	foreach($data as $row)
	{
		$this->Cell($w[0],6,$row[0],'LR',0,'L',$fill);
		$this->Cell($w[1],6,$row[1],'LR',0,'L',$fill);
		$this->Cell($w[2],6,$row[2],'LR',0,'L',$fill);
		$this->Cell($w[3],6,$row[3],'LR',0,'C',$fill);
		$this->Cell($w[4],6,number_format($row[4]),'LR',0,'R',$fill);
		$this->Cell($w[5],6,number_format($row[5]),'LR',0,'R',$fill);
		$this->Ln();
		$fill=!$fill;
	}
	$this->Cell(array_sum($w),0,'','T');
}
}

		$pdf=new PDF();
		//Column titles
		$header=array('Field Name','Data Type','Null','Description','Index','Referance');
		$pdf->SetFont('Arial','',10);		
		$pdf->AddPage();
		//Data loading
		
		//*** Load MySQL Data ***//
		// connect DB gdatadict
        $insert_db = ADONewConnection($_POST['databasetype'], $_POST['transactions'] . $_POST['extend'] . $_POST['date'] . $_POST['adodblite'] . "pear");
        $insert_db->createdatabase = true;
        $result = $insert_db->Connect( "localhost", "root", "123456", "gdatadict" );
	        
		if(!$result) {
            die("Could not connect to the database.");
        }

		$cmd = "select id,table_name from table_prop";
		$insert_rs = $insert_db->Execute($cmd);   
		$table_array=$insert_rs->GetArray();
		foreach($table_array as $table_row) {
			$cmd = "select field_name, field_type, field_null,field_desc,field_index,field_refer from field_prop where table_id = '" . $table_row['id'] . "'";
			$insert_rs = $insert_db->Execute($cmd);          
			$field_row=$insert_rs->RecordCount();
			$field_array=$insert_rs->GetArray();
			if($field_row) {
				$pdf->Ln(1);
				$pdf->Cell(0,10,'Table Name : '.$table_row['table_name'],0,1);
				$pdf->Ln(1);
				$pdf->FancyTable($header,$field_array);
			}
			//unset($field_array);
		}
/*
		$pdf->AddPage();
		//$pdf->Image('logo.png',80,8,33);
		$pdf->Ln(35);
		$pdf->ImprovedTable($header,$resultData);
		

		$pdf->AddPage();
		//$pdf->Image('logo.png',80,8,33);
		$pdf->Ln(35);
		$pdf->FancyTable($header,$resultData);
*/	
		$pdf->Output("MyPDF.pdf","F");
?>

PDF Created Click <a href="MyPDF.pdf">here</a> to Download
</body>
</html>