<!DOCTYPE html>
<html>
	<head>
		<title>login</title>
		<link rel="stylesheet" href="../styles/main.css" type="text/css">
	</head>
	<body>
		<div class="midcenter">
			<?php
				//Check if already logged in
				session_start();
				if(array_key_exists('admin_session',$_SESSION)){
					header("Location: ./?op=log");
				}
				
				//Display message if wrong password
				if(array_key_exists('f',$_GET)){
					echo("<div class='alert'>Wrong Password</div>\n");
				}else{
					echo("Password<br>\n");
				}
			?>
			<form method="POST" action="check.php">
				<input type="password" name="password" autofocus="autofocus"><br>
			</form>
		</div>
	</body>
</html>
