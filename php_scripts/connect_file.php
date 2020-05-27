<?
	error_reporting(1);

	class connect_file {
		var $pathroot = null;
		var $typefile = ".table"; 
		var $handle = null ;
		var $fieldname = array();
		var $chk_head = null;
		var $divide_text = null;
		var $path_tmp = null;
		var $path_sub_tmp = null;
		var $fullpath = null;
		
		function connect_file ($query, $type, $DIVIDE_TEXTFILE, $path, $head=1) {
			if (trim($path) && $type=="w") {
				$path_tmp = explode("\\", $path);
				for ($m=1; $m<count($path_tmp); $m++) {
					$path_sub_tmp .= $path_tmp[$m] . "\\";
					if (is_dir($path_tmp[0] ."\\". $path_sub_tmp) == false) {			
						mkdir($path_tmp[0] ."\\". $path_sub_tmp, 0777);
					} // end if 
				} // end for 
				$this->pathroot = $path;
			} else {
				$this->pathroot = $path;
			}// end if 
//			echo "this->pathroot=".$this->pathroot."<br>";
			if (trim($query)) { 
				if ($type == "r") { 
					// ===== get header =====
					if(trim($query)=="PER_SLIP"){			//จากเมนู P1114 (convert_texttoslip.php) เท่านั้น
						$filename = $this->pathroot ;
					}else if(trim($query)=="PER_TAXHIS"){           //จากเมนู P1120
                                                $filename = $this->pathroot ;
                                        }
                                        else{					
						$filename = $this->pathroot . $query . $this->typefile ;
					}
//					echo "<br>1..$type :: filename = $filename<br>";								
					$this->handle = fopen($filename, "$type");
					$this->divide_text = $DIVIDE_TEXTFILE;
					if ($head) {
						$buffer = fgets($this->handle, 5120);
						$arr = explode("$DIVIDE_TEXTFILE", $buffer);
//						print("<pre>");	 print_r($arr); print("</pre>");
						foreach ($arr as $fieldname) {
							$this->fieldname[] = trim($fieldname);
						} // for loop
					} 	// end if $nohead
				} elseif ($type == "w" || $type == "a") {
					// ===== get header =====
					if(trim($query)=="PER_SLIP"){			//จากเมนู P1114 (convert_texttoslip.php) เท่านั้น
						$filename = $this->pathroot ;
					}else if (trim($query)=="PER_TAXHIS"){          //จากเมนู P1120 
                                                $filename = $this->pathroot ;        
                                        }else{		
						$filename = $this->pathroot . $query . $this->typefile ;
					}
//					echo "<br>2..$type :: filename = $filename<br>";								
					unlink($filename);
					$this->fullpath = $filename;
					$this->handle = fopen($filename, "$type");
				}		// end if check type
			} 			// end if check query
		} // function 

 //จากเมนู P1114 (convert_texttoslip.php) และ p1120   เท่านั้น
		function get_text_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT) { 
			unset($data);
			if (!feof($this->handle)) {
				$buffer = fgets($this->handle, 5120);
				$buffer_len = strlen(trim($buffer));
				if ($buffer_len != 0) {
					$arr = explode("$this->divide_text", $buffer);
					foreach ($arr as $key => $fieldvalue) {
						$fieldname = $this->fieldname[$key];
						if (substr($arr_fields_type[$key],0,1)=="n") {
//							echo "int :: $key -> $arr_fields_type[$key] = $data[$fieldname] ($fieldname) (fieldvalue=$fieldvalue)<br>";
							$digit = substr($arr_fields_type[$key],2);
							if ($digit)
								$fieldvalue = (trim($fieldvalue))? substr($fieldvalue,0,strlen($fieldvalue)-$digit).".".substr($fieldvalue,strlen($fieldvalue)-$digit) : 0;
//							echo "int :: $key -> $arr_fields_type[$key] = $data[$fieldname] ($fieldname) (fieldvalue=$fieldvalue)<br>";
						} else {
//							echo "str :: $key -> $arr_fields_type[$key] = $data[$fieldname] ($fieldname) (fieldvalue=$fieldvalue)<br>";
							$fieldvalue = (trim($fieldvalue))? "'" . trim($fieldvalue) ."'" : "''";
						} // endif in_array
//jerd						if (in_array($arr_fields_type[$fieldname], $TYPE_TEXT_STR)) {
						$data_insert .= (trim($data_insert))? ", $fieldvalue" : $fieldvalue ;						
					} // for loop
//					echo "data_insert=$data_insert<br>";
					return $data_insert;
				}	// if len of buffer
			} // if EOF
		} // function

		function get_text_data1($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT) { 
			unset($data);
			if (!feof($this->handle)) {
				$buffer = fgets($this->handle, 5120);
				$buffer_len = strlen(trim($buffer));
				if ($buffer_len != 0) {
					$arr = explode("$this->divide_text", $buffer);
					foreach ($arr as $key => $fieldvalue) {
						$fieldname = $this->fieldname[$key];
						if (substr($fieldvalue,0,1) == '0' && (strlen($fieldvalue)==8 || strlen($fieldvalue)==10)) {
//							echo "int :: $key -> $arr_fields_type[$key] = $data[$fieldname] ($fieldname)<br>";
							if (strlen($fieldvalue)==8)
								$fieldvalue = (trim($fieldvalue))? substr($fieldvalue,0,6).".".substr($fieldvalue,6,2) : 0;
							else
								$fieldvalue = (trim($fieldvalue))? substr($fieldvalue,0,8).".".substr($fieldvalue,8,2) : 0;
						} else {
//							echo "str :: $key -> $arr_fields_type[$key] = $data[$fieldname] ($fieldname)<br>";
							$fieldvalue = (trim($fieldvalue))? "'" . trim($fieldvalue) ."'" : "NULL";
						} // endif in_array
//jerd						if (in_array($arr_fields_type[$fieldname], $TYPE_TEXT_STR)) {
						$data_insert .= (trim($data_insert))? ", $fieldvalue" : $fieldvalue ;						
					} // for loop
//					echo "data_insert=$data_insert<br>";
					return $data_insert;
				}	// if len of buffer
			} // if EOF
		} // function

		function get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT) {
			unset($data);
			if (!feof($this->handle)) {
				$buffer = fgets($this->handle, 5120);
				$buffer_len = strlen(trim($buffer));
				if ($buffer_len != 0) {
					$arr = explode("$this->divide_text", $buffer);
					foreach ($arr as $key => $fieldvalue) {
						$fieldname = $this->fieldname[$key];
						//echo "$key : $fieldvalue - $fieldname => field type = " .$arr_fields_type[$fieldname] ."<br>" ;
						if (in_array($arr_fields_type[$fieldname], $TYPE_TEXT_STR)) {	
							$fieldvalue = (trim($fieldvalue) == "'")? "&rsquo;" : trim($fieldvalue);
							$fieldvalue = (trim($fieldvalue)!="")? "'" . $fieldvalue . "'" : "NULL";
							$data[$fieldname] = $fieldvalue ;
						} else {
							$fieldvalue = (trim($fieldvalue)!="")? trim($fieldvalue) : "NULL";
							$data[$fieldname] = $fieldvalue;
						}
					} // for loop
					return $data;
				}	// if len of buffer
			} // if EOF
		} // function 

		function write_text_data($somecontent) {
			$filename = $this->pathroot . $query . $this->typefile ;		
			// Write $somecontent to our opened file.
			if (fwrite($this->handle, $somecontent) === FALSE) {
				echo "Cannot write to file ($filename)<br>";
			}			
		}

		function show_error() {
			$errtxt = "SQL = " . $this->sqlquery . "\nError No = " . $this->ERRNO . "\nError Des = " . $this->ERROR ;
			echo nl2br($errtxt);
			return $errtxt;
		}

	} // end of class
?>