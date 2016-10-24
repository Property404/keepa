<!DOCTYPE HTML>
<!--This document is nearly the entire control panel.
Most OCP functionalities are implemented here-->
<html>
	<head>
		<title>Keepa Control Panel</title>
		<link rel="stylesheet" href="../styles/main.css">
		<link rel="stylesheet" href="../styles/menu.css">
        <script src="jquery.min.js"></script>

	</head>
	<body>
		<script>


			function checkKeyCreation(info, name){
				if (info["status"]=="success"){
					manifestAnswerKey(info["id"], name);
				}else{
					alert("Failure to create object");
				}
			}
			function manifestAnswerKey(id, name){
				document.getElementById("keytable").innerHTML += "<tr><td id=nameof"+id+" onclick='rename("+id+")'>"+name+"</td><td><button onclick='location.href=\"?op=editkey&id="+id+"\"'>edit</button></td><td><button onclick='location.href=\"?op=mankeys&rmid="+id+"\"'>delete</button></td>";
				$(document).on('blur','#txt_nameof'+id, function(){
					var nombre = $(this).val();
					if(nombre=="")return;
					$.ajax({type: 'post',
						url: 'rename.php?id='+id+'&name='+window.btoa(nombre),
						success:function(d){
							console.log(d);
							$('#nameof'+id).text(nombre);
							}
						});
					});
			}
			function createAnswerKey(name){
				$.post("./newkey.php", {
					"name":name}, function(d){checkKeyCreation(d, name)}, "json");
			}
			function rename(id){
				var dis = document.getElementById("nameof"+id);
				var old = $(dis).text();
				$(dis).html(''); 
				$('<input></input')
				.attr({'type':'text','id':'txt_nameof'+id, 'value':old, 'size':'16'}).appendTo("#nameof"+id);
				$("#txt_nameof"+id).focus();
			}
			function manifestProblem(id, question, answer){
				document.getElementById("problemtable").innerHTML += "<tr><td>"+id+"</td><td id=questionof"+id+" onclick='changeQuestion("+id+")'>"+question+"</td><td id=answerof"+id+" onclick='changeAnswer("+id+")'>"+answer+"</td></tr>";
			}
			function createProblem(parent_id, question, answer){
				$.post("./newproblem.php", {
						"parent_id":parent_id,
						"question":question,"answer":answer},function(d){checkProblemCreation(d, question,answer)},"json");
			}
			function checkProblemCreation(info, question, answer){
				if(info["status"]=="success"){
					manifestProblem(info["id"], question, answer);
				}else{
					alert("There was a problem whilst creating a problem");
				}
			}
		</script>
		<?php
			error_reporting(E_ALL);
			ini_set("display_errors", "On");
			include_once("menu.php");
			include_once("../headers/security.php");
		?>
		
		<div class="topcenter">
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
					echo("<h1>HELP!</h1>");
				//Edit answerkey
				}else if($_GET["op"]=="editkey"){
					$parent_id = $_GET["id"];
					$name = mysqli_fetch_row(mysqli_query($link, "SELECT NAME FROM ANSWERKEY WHERE ID=".$_GET["id"].";"))[0];
					$qaresult = mysqli_query($link, "SELECT ID,QUESTION, ANSWER FROM PROBLEM WHERE PARENT=".$_GET["id"]);
					echo("<h1>$name</h1>");
					echo("<table id=\"problemtable\"><tr><td><strong>ID</strong></td><td><strong>Question</strong></td><td><strong>Answer</strong></td></tr></table><button onclick='createProblem($parent_id,\"question\",\"answer\")'>New Problem</button>");
					echo("<script>");
					while($row =$qaresult->fetch_row()){
						echo("manifestProblem(\"".$row[0]."\",\"".$row[1]."\",\"".$row[2]."\");");
					}
					echo("</script>");
					
				//Manage keys	
				}else if($_GET["op"]=="mankeys"){
					if(array_key_exists("rmid",$_GET)){
						mysqli_query($link, "DELETE FROM ANSWERKEY WHERE ID=".$_GET["rmid"].";");
					}
					//List rows
					$result = mysqli_query($link, "SELECT * FROM ANSWERKEY;");	
					
					echo("<h1>Answerkeys</h1><table class='keytable' id='keytable'><table><br><button onclick='createAnswerKey(\"untitled\")'>New Answerkey</button>");
					echo("<script>");
					while($row = $result->fetch_row()){
						printf("manifestAnswerKey(%d, \"%s\");\n", $row[0], $row[1]);
					}
					echo("</script>\n");
				
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
								header("Location: ?op=passwd&msg=Password%20too%20short");
							}
						}else{
							
							//Confirmation password doesn't match
							header("Location: ?op=passwd&msg=Passwords%20don't%20match");
							
						}
					}else{
						
						//Wrong password
						if(!rand(0,10)){header("Location: ?op=logout");die("");}
						header("Location: ?op=passwd&msg=Wrong%20password");
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
