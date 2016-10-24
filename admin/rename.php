<?php
error_reporting(E_ALL);

	include_once("../headers/session.php");
	Session::checkAdminSession();
	
	$link = Session::forceConnectDB();
	$name = mysqli_real_escape_string($link, base64_decode($_GET["name"], true));
	$id = mysqli_real_escape_string($link, $_GET["id"]);

	$query = 'UPDATE ANSWERKEY SET NAME="'.$name.'" WHERE ID='.$id.';';
	mysqli_query($link, $query);
	echo($query . "\n");
	echo('{"status":"success","name":"'.$name.'","id":'.mysqli_insert_id($link).'}');
?>
