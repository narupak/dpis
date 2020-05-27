<?php 
	error_reporting(0);
	echo $type;
	if(is_array($arr_content[1])) {
		/*
		 * 
		 *		
		*/ 
		if($type == 'Bar' || $type == 'Pie') {
			// Pie Bar
			for($data_count=1; $data_count<count($arr_content); $data_count++) {		
				$sum_value = $arr_content[$data_count][count_1]+$arr_content[$data_count][count_2]+$arr_content[$data_count][count_3];
				$data_node .= "<set label='".$arr_content[$data_count][name]."' value='".$sum_value."' isSliced='0' />";
			} // for 
			
		} elseif($type == 'Line' || $type == 'Column') {
			// Line Column
			for($data_count=1; $data_count<count($arr_content); $data_count++) {
				$category_node .= "<category label='".$arr_content[$data_count][name]."' /> ";
				$sum_value = $arr_content[$data_count][count_1]+$arr_content[$data_count][count_2]+$arr_content[$data_count][count_3];
				$data_node .= "<set value='".$sum_value."'/>";
			} // for
			
		} // if type
		
	} else {
		/*
		 * 
		 *		
		*/ 
		 
		if($type == 'Bar' || $type == 'Pie') {
			// Pie Bar
			foreach($arr_content[0] as $key => $value) {
				if(substr(strtolower($key),0,5) == 'count') {
					$data_node .= "<set label='".substr(strtolower($key),6)."' value='".$value."' isSliced='0' />";
				}
			} // for 
			
		} elseif($type == 'Line' || $type == 'Column') {
			// Line Column
			foreach($arr_content[0] as $key => $value) {
				if(substr(strtolower($key),0,5) == 'count') {
					$category_node .= "<category label='".substr(strtolower($key),6)."' /> ";
					$data_node .= "<set value='".$value."'/>";
				}
			} // for
			
		} // if type
		
	} // if is_array

echo "<pre>";
print_r($arr_content);
echo "</pre>";
?>