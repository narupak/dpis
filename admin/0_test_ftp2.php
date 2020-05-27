<?php
	$serverip = $_POST['serverip'];
	$username = $_POST['username'];
	$password = $_POST['password'];
	$filepath = $_POST['filepath'];
	
	//-- SMTP Mail Function  By Aditya Bhatt
	if(isset($_POST['SubmitFile'])){
  
		ini_set('max_upload_filesize', '250MB');
		ini_set('max_execution_time', '3000');
		
		// This will make an array out of the file information that was stored.
		$myFile = $_FILES['txt_file']; 
		//Converts the array into a new string containing the path name on the server where your file is.
		$file = $myFile['tmp_name'];
 
		$myFileName = basename($_FILES['txt_file']['name']); //Retrieve filename out of file path

		$destination_file = $_REQUEST['filepath'].$myFileName;
		#"/developers/uploadftp/aditya/".$myFileName;  //where you want to throw the file on the webserver (relative to your login dir)

		// connection settings
		$ftp_server = trim($_REQUEST['serverip']);  //address of ftp server.
		$ftp_user_name = trim($_REQUEST['username']); // Username
		$ftp_user_pass = trim($_REQUEST['password']);   // Password

		$conn_id = ftp_connect($ftp_server) or die("<span style='color:#FF0000'><h2>Couldn't connect to $ftp_server</h2></span>");        // set up basic connection
		#print_r($conn_id);
		$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass) or die("<span style='color:#FF0000'><h2>You do not have access to this ftp server!</h2></span>");
		// login with username and password, or give invalid user message
		if ((!$conn_id) || (!$login_result)) {  // check connection
			// wont ever hit this, b/c of the die call on ftp_login
			echo "<span style='color:#FF0000'><h2>FTP connection has failed! <br />";
			echo "Attempted to connect to $ftp_server for user $ftp_user_name</h2></span>";
			exit;
		} else {
			//    echo "Connected to $ftp_server, for user $ftp_user_name <br />";
		}

//		echo "dest-$destination_file, file-$file<br>";
		$upload = ftp_put($conn_id, $destination_file, $file, FTP_BINARY);  // upload the file
		if (!$upload) {  // check upload status
			echo "<span style='color:#FF0000'><h2>FTP upload of $myFileName has failed!</h2></span> <br />";
		} else {
			echo "<span style='color:#339900'><h2>Uploading $myFileName Completed Successfully!</h2></span><br /><br />";
		}

		ftp_close($conn_id); // close the FTP stream
	}
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ทดสอบ FTP</title>

<script language="JavaScript">

	function call_iswasbeen(){
		form1.serverip.value = "ftp.iswasbeen.com";
		form1.username.value = "kxvxc@iswasbeen.com";
		form1.password.value = "hjkmf";
		form1.filepath.value = "/public_html/testupload/";
		form1.submit();
	}

	function call_g4all(){
		form1.serverip.value = "ftp.g4all.com";
		form1.username.value = "garjx@g4all.com";
		form1.password.value = "xpyjo";
		form1.filepath.value = "/public_html/testupload/";
		form1.submit();
	}

</script>

</head>
<body>
<form name="form1" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
	<input name="iswasbeen" type="button" value="iswasbeen" onClick="call_iswasbeen();"/>&nbsp;&nbsp;
	<input name="g4all" type="button" value="g4all" onClick="call_g4all();"/><br><br>
	Server IP Address: <input name="serverip" type="text" id="serverip" size="15" value="<?=$serverip?>"/><br>
	Server Username: <input name="username" type="text" id="username" size="15" value="<?=$username?>"/><br>
	Server Password: <input name="password" type="text" id="password" size="15" value="<?=$password?>"/><br>
	Server File Path: <input name="filepath" type="text" id="filepath" size="35" value="<?=$filepath?>"/><br>
	Please choose a file: <input name="txt_file" type="file" id="txt_file" tabindex="1" size="35" onChange="txt_fileName.value=txt_file.value" /><br><br>
	<input name="txt_fileName" type="hidden" id="txt_fileName" tabindex="99" size="1" />

	<input type="submit" name="SubmitFile" value="Upload File" accesskey="ENTER" tabindex="2" />
</form>
</body>
</html>
