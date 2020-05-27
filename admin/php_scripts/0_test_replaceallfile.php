<?php

	$search_text = $_POST['search_text'];

	$replac_text = $_POST['replace_text'];

	$filepath = $_POST['filepath'];
	
	$command = $_POST['command'];
	
	if(($command=="REPLACE" || $command=="TEST") && file_exists($filepath)) {
  
//		ini_set('max_execution_time', '3000');

		$dir = opendir("$filepath");
 
		$msg1="can not put to file (xxxxx)";
		$msg2="not founded '$search_text' in file (xxxxx)";
		$msg3="can not get from file (xxxxx)";
		$arr_msg1=(array) null;
		$arr_msg2=(array) null;
		$arr_msg3=(array) null;
		$replace_cnt=0;
		$notfouned_cnt=0;
		$notget_cnt=0;
		$notput_cnt=0;
		$err_code = 0;
		//List files in images directory
		while (($file = readdir($dir)) !== false){
//			echo "filename: " . $file . "<br />";

			if (!is_dir($filepath."/".$file)) {
 
				//read the entire string
				$str=file_get_contents($filepath."/".$file);

				if ($str) {
					//replace something in the file string - this is a VERY simple example
					$str=str_replace($search_text, $replace_text, $str, $cnt);
						
					if ($cnt) {
						if ($command!="TEST") {
							$result = file_put_contents($filepath."/".$file, $str);
							if (!$result) {
								$err_code = 1;
								$arr_msg1[] = $file;
								$notput_cnt++;
							} else {
								$err_code = 0;
								$replace_cnt++;
							}
						} else {
							$err_code = 0;
							$replace_cnt++;
						}
					} else {
						$err_code = 2;
						$arr_msg2[] = $file;
						$notfounded_cnt++;
					}
				} else {
					$err_code = 3;
					$arr_msg3[] = $file;
					$notget_cnt++;
				}
			}
		}

		$msg = "";
		if ($command=="TEST")
			if ($replace_cnt > 0) $msg.="--------- founed '$search_text' in $replace_cnt files --------------\n";
		else
			if ($replace_cnt > 0) $msg.="--------- Replaced $replace_cnt files --------------\n";
		if (count($arr_msg1) > 0) $msg.="**** Error-1 ($notput_cnt files) ".str_replace("xxxxx", implode(",",$arr_msg1),$msg1)." ****\n";
		if (count($arr_msg2) > 0) $msg.="**** Error-2 ($notfounded_cnt files) ".str_replace("xxxxx", implode(",",$arr_msg2),$msg2)." ****\n";
		if (count($arr_msg3) > 0) $msg.="**** Error-3 ($notget_cnt files) ".str_replace("xxxxx", implode(",",$arr_msg3),$msg3)." ****\n";
		closedir($dir);
	} else {
		if ($filepath)
			if (!file_exists($filepath))  $msg = "directory : ".$filepath." not founded...\n"; else $msg="";
	}
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Text replace all file in target path</title>

<script language="JavaScript">

	function call_test(){
		if (confirm("-- Please Confirm to Test --")) {
			form1.msg.value = "";
			form1.command.value = "TEST";
			form1.submit();
		}
	}

	function call_start(){
		if (confirm("!!!!!! Please Confirm to Replace !!!!!!")) {
			form1.msg.value = "";
			form1.command.value = "REPLACE";
			form1.submit();
		}
	}

</script>

</head>
<body>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
<form name="form1" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
	<input name="command" type="hidden" value="<?=$command?>"/>
    <tr><td width="5%">&nbsp;</td>
    	<td width="10%">&nbsp;</td><td>&nbsp;</td></tr>
    <tr><td width="5%">&nbsp;</td>
    	<td width="10%" align="right">
		Search Text&nbsp;:&nbsp;</td><td><input name="search_text" type="text" id="search_text" size="50" value="<?=$search_text?>"/>
        </td></tr>
    <tr><td width="5%">&nbsp;</td>
    	<td width="10%" align="right">
		Replace Text&nbsp;:&nbsp;</td><td><input name="replace_text" type="text" id="replace_text" size="50" value="<?=$replace_text?>"/>
        </td></tr>
    <tr><td width="5%">&nbsp;</td>
    	<td width="10%" align="right">
		Files Path&nbsp;:&nbsp;</td><td><input name="filepath" type="text" id="filepath" size="50" value="<?=$filepath?>"/>
        </td></tr>
    <tr><td width="5%">&nbsp;</td>
    	<td width="10%">&nbsp;</td><td>&nbsp;</td></tr>
    <tr><td width="5%">&nbsp;</td>
    	<td width="10%" align="right"></td><td>
		<input type="button" name="SubmitTest" value="Start Test" accesskey="ENTER" tabindex="2" onClick="call_test();"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="button" name="SubmitFile" value="Start Replace" accesskey="ENTER" tabindex="2" onClick="call_start();"/>
        <td>&nbsp;</td></td></tr>
    <tr><td width="5%">&nbsp;</td>
    	<td width="10%">&nbsp;</td><td>&nbsp;</td></tr>
    <tr><td width="5%">&nbsp;</td>
    	<td width="10%" align="right" valign="top">Message&nbsp;:&nbsp;</td><td>
		<textarea name="msg" cols="100" rows="10" readonly><?=$msg?></textarea>
        </td></tr>
</form>
</body>
</html>
