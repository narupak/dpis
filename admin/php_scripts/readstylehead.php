<?
   $theme_style = "";
   $sysname_style = "";
   $deptname_style = "";
   $username_style = "";
  //if no style loaded load
   $lines = file($cssfileselected);
   $comment_open = false;
   foreach ($lines as $line_num => $line) {
	  if (!$comment_open) {
		  $c1 = strpos($line, "/*");
		  if ($c1 !== false) {
	  		$c2 = strpos($line, "*/", $c1+2);
			if ($c2 !== false) {
				$line = substr($line, 0, $c1).substr($line, $c2+2);
			} else {
				$line = substr($line, 0, $c1);
				$comment_open = true;
			}
		  } else { // ไม่มี /*
		  	$c2 = strpos($line, "*/");
			if ($c2 !== false)
				$line = substr($line, $c2+2);
			else  // ไม่มี *?
				$line = $line;
		  }
	  } else {	// if $comment_open
 		  $c2 = strpos($line, "*/");
		  if ($c2 !== false) {
				$line = substr($line, $c2+2);
				$comment_open = false;
		  } else
				$line = "";
	  }
      $cssstyles .= trim($line);
   }

  //using strtok we remove the brackets around the css styles  
  $tok = strtok($cssstyles, "{}");
//	echo "0 tok:$tok<br>";
  //For example, the style: p{color:#000000;} now looks like this p color:#000000;
  
  //crete another array in which we will store tokenized string
  $sarray = array();
  //set counter
  $spos = 0;
  //with this while loop we are basically separating selectors from styles and store those values in the $sarray
  while ($tok !== false) {
   $sarray[$spos] = $tok;
   $spos++; 
   	$tok = strtok("{}");
//	echo "$spos tok:$tok<br>";
  }
  //if you run print_r($sarray); the result would be:
  //Array   ( [0] => p [1] => color:#000000;
  //   [2] => h1 [3] => font-size:18px;color:#666666;
  //   [4] => table [5] => width:100%; border:1px solid #000000;)
  //As you can see all selectors are stored in odd number positions of the array and styles in even.
  //That is an important piece of information that we will use to to go through $sarray and store <br>
  //all selectors in one array and styles in the other.
  
  // To start we need to get the size of $sarray
  $size = count($sarray);
  
  //create selectors and styles arrays
  $selectors = array();
  $sstyles = array();
  
  //set counters
  $npos = 0;
  $sstl = 0;
  
  //a simple for loop with modulus operator will help us separate styles from selectors.
  for($i = 0; $i<$size; $i++){
   if ($i % 2 == 0) {
     $selectors[$npos] = $sarray[$i];
//	 echo "sel:$npos=".$selectors[$npos]."<br>";
     $npos++;    
   }else{
     $sstyles[$sstl] = $sarray[$i];
//	 echo "sty:$sstl=".$sstyles[$sstl]."<br>";
     $sstl++;
   } 
  }
  
  //all that's left is store names and styles in a session for us to use
//  $_SESSION['style_names'] = $selectors;
//  $_SESSION['style_styles'] = $sstyles;
  //now if you run print_r($selectors); and print_r($sstyles); <br>
  //you'll notice that you can access individual selectors and it's styles using array keys
  //In this example $selectors[0] value is "p" and $sstyles[0] value is "p" property/value pair-color:#000000;
  
  // search for head attribute
  for($i = 0; $i<count($selectors); $i++){
   if (strpos($selectors[$i],"header-theme") !== false) {
	 $theme_style=$sstyles[$i];
//	 echo "theme:$theme_style<br>";
	 $sub_attr = explode(";",$theme_style);
	 $arr_theme = (array) null;
	 for($ii=0; $ii < count($sub_attr); $ii++) {
	 	$abuff = explode(":",$sub_attr[$ii]);
//		echo "$ii-".$abuff[1]."<br>";
		$arr_theme[] = trim($abuff[1]);
	 }
	 $bgcolor1=str_replace("#", "0x", $arr_theme[0]);
	 $bgcolor2=str_replace("#", "0x", $arr_theme[1]);
	 $bgalpha1=$arr_theme[2];
	 $bgalpha2=$arr_theme[3];
   }elseif (strpos($selectors[$i],"header-systemname") !== false) {
	 $sysname_style=$sstyles[$i];
//	 echo "sysname:$sysname_style<br>";
   }elseif (strpos($selectors[$i],"header-deptname") !== false) {
	 $deptname_style=$sstyles[$i];
//	 echo "deptname:$deptname_style<br>";
   }elseif (strpos($selectors[$i],"header-username") !== false) {
	 $username_style=$sstyles[$i];
//	 echo "username:$username_style<br>";
   } 
  }
?>
