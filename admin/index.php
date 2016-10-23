<!DOCTYPE HTML>
<!--This document is nearly the entire control panel.
Most OCP functionalities are implemented here-->
<html>
	<head>
		<title>Keepa Control Panel</title>
		<link rel="stylesheet" href="../styles/main.css">
		<link rel="stylesheet" href="../styles/menu.css">
	</head>
	<body>
		<?php
			error_reporting(E_ALL);
			include_once("menu.php");
			include_once("../headers/keypair.php");
			include_once("../headers/security.php");
			include_once("../headers/eventlog.php");
		?>
		
		<div class="midcenter">
		<?php
			//Define functions here
			function escapeText($text)
			{
				$text=str_replace("&","&amp;",$text);
				$text=str_replace("<","&lt;",$text);
				return $text;
			}

			//Check any messages
			if (array_key_exists("msg",$_GET))
				echo("<div class='alert'>".$_GET["msg"]."</div>\n");
			if (array_key_exists("note",$_GET))
				echo("<div>".$_GET["note"]."</div>\n");
			
			//See if on an option page
			if(array_key_exists("op",$_GET)){
				
				//Print help info
				if($_GET["op"]=="help"){
					header("location: ../doc/index.php?page=0");
				}else if(){

				
				//Make change Password form
				}else if($_GET["op"]=="passwd"){
					echo('<form method="POST" action="index.php?op=chpwdact">
						Old Password:<br><input type="password" name="oldpwd" autofocus="autofocus"><p>
						New Password:<br><input type="password" name="newpwd"><p>
						Confirm New Password:<br><input type="password" name="newpwd2"><p>
						<input type="submit" value="Submit">
						</form>');
				}
				
				//Change password
				else if($_GET["op"]=="chpwdact"){
					
					//Fetch correct hash
					$link=Session::forceConnectDB();
					$correct_hash=mysqli_fetch_row(mysqli_query($link,"select * from ADMIN where ID=1"))[1];
					
					
					//Check if hashes match
					if(Security::makeSaltedHash($_POST["oldpwd"],$correct_hash)==$correct_hash){
						
						//Check if confirmation password matches
						if($_POST["newpwd"]==$_POST["newpwd2"]){
							
							//Check if new password is proper length
							if(strlen($_POST["newpwd"])>=Security::MIN_PASSWORD_LENGTH){
							
							//Set new hash
							mysqli_query($link,"UPDATE admin SET hash='".Security::makeSaltedHash($_POST["newpwd"]) . "' WHERE ID=1");
							$_SESSION["default_password"]="false";
							
							//Exit and inform the user on success
							header("Location: ?note=Password%20changed!");
							}else{
								
								//Password not long enough
								header("Location: ?op=changepwd&msg=Password%20too%20short");
							}
						}else{
							
							//Confirmation password doesn't match
							header("Location: ?op=changepwd&msg=Passwords%20don't%20match");
							
						}
					}else{
						
						//Wrong password
						if(!rand(0,10)){header("Location: ?op=logout");die("");}
						header("Location: ?op=changepwd&msg=Wrong%20password");
					}
				}
				
				
				//Logout
				else if($_GET["op"]=="logout"){
					unset($_SESSION);
					session_destroy();
					header("Location: login.php");
				}
			}else{
				header("location: ?op=log");
			}
		?>
		</div>
	</body>
</html>
