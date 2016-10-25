<html>
	<head>
		<title>Keepa</title>
		<link rel="stylesheet" href="styles/main.css">
        <script src="admin/jquery.min.js"></script>
	</head>
	<body>
		<script>
			function check(parent_id, id){
				answer = document.getElementById("text"+id).value;
				$.ajax({type:'post',url:'checkanswer.php?parent_id='+parent_id+'&id='+id+'&answer='+window.btoa(answer),
				success:function(d){console.log(d);document.getElementById("status"+id).innerHTML= d;} });
			}
				
		</script>


		<?php
			error_reporting(E_ALL);
			ini_set("display_errors", "On");
			include_once("headers/session.php");
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
			$link = Session::forceConnectDB();
			if(array_key_exists("id", $_GET)){
				echo("<table>\n<tr><td><strong>Question</strong></td><td><strong>Answer</strong></td></tr>\n");
				$id = mysqli_real_escape_string($link, $_GET["id"]);
				$query = mysqli_query($link, "SELECT ID, QUESTION FROM PROBLEM WHERE PARENT=$id;");
				$script_buffer = "";
				while($row = $query -> fetch_row()){
					echo("<tr><td>".escapeText($row[1])."</td><td><input type='text' id='text".$row[0]."'/></td><td ><button onclick='check($id, ".$row[0].")'>Guess</button></td><td id='status".$row[0]."'></td></tr>\n");
					}
				echo("</table>");
			}else{
				echo("<strong>Assignments</strong><br>");
				$query = mysqli_query($link, "SELECT ID, NAME FROM ANSWERKEY");
				while($row = $query -> fetch_row()){
					echo("<a href='?id=".$row[0]."'>".$row[1]."</a><br>");
				}
			}

		?>
		</div>
	</body>
</html>
