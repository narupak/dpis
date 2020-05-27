<?php 
    
    function getToFont($id){
        if($id==1){
			$fullname	= 'Angsana';
		}else if($id==2){
			$fullname	= 'Cordia';
		}else if($id==3){
			$fullname	= 'TH SarabunPSK';
		}else{
			$fullname	= 'Browallia';
		}
        return $fullname;
    }
   

?>