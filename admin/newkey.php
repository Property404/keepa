<?php
error_reporting(E_ALL);

	include_once("../headers/session.php");
	Session::checkAdminSession();
	
	$link = Session::forceConnectDB();
	$name = mysqli_real_escape_string($link, $_POST["name"]);

	mysqli_query($link, "INSERT INTO ANSWERKEY (NAME) VALUES(\"".$name."\");");
	echo('{"status":"success","name":"'.$name.'","id":'.mysqli_insert_id($link).'}');
?>
