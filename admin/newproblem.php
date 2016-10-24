<?php
error_reporting(E_ALL);

	include_once("../headers/session.php");
	Session::checkAdminSession();
	
	$link = Session::forceConnectDB();
	$question= mysqli_real_escape_string($link, $_POST["question"]);
	$answer = mysqli_real_escape_string($link, $_POST["answer"]);
	$parent_id = mysqli_real_escape_string($link, $_POST["parent_id"]);


	mysqli_query($link, "INSERT INTO PROBLEM(PARENT, QUESTION, ANSWER) VALUES(\"".$parent_id."\",\"".$question."\",\"".$answer."\");");
	echo('{"status":"success","name":"'.$name.'","id":'.mysqli_insert_id($link).'}');
?>
