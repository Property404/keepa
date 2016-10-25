<?php
error_reporting(E_ALL);

	include_once("../headers/session.php");
	Session::checkAdminSession();
	
	$link = Session::forceConnectDB();
	$value = mysqli_real_escape_string($link, base64_decode($_GET["value"], true));
	$parameter = mysqli_real_escape_string($link, $_GET["parameter"]);/* Can be "Question" or "Answer" */
	$id = mysqli_real_escape_string($link, $_GET["id"]);
	if($parameter == "question"){
	$query = 'UPDATE PROBLEM SET QUESTION="'.$value.'" WHERE ID='.$id.';';
	}else if ($parameter == "answer"){
	$query = 'UPDATE PROBLEM SET ANSWER="'.$value.'" WHERE ID='.$id.';';
	}
	mysqli_query($link, $query);
	echo($query . "\n");
	echo('{"status":"success","value":"'.$value.'","id":'.mysqli_insert_id($link).'}');
?>
