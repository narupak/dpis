<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
   	<p>Enter the IP address or the domain name of the server :</p> 
   	<input type='text' name='domain' class='input_login' value="" />&nbsp;
   	<input type="submit" name="submit" value="Submit" />
</form>

<?php
	ini_set('max_execution_time',60);
	if (isset($_POST['submit']))
	{
		$output = shell_exec("ping $domain 2>&1");
		echo "<br>Ping result :"; 		
		echo "<pre>$output</pre>";
		echo "<br>";
				
		$output = shell_exec("tracert $domain 2>&1");
		echo "Traceroute result :";
		echo "<pre>$output</pre>";
		echo "<br>";
	}	 
?> 

</body>
</html>
