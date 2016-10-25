<?php
include_once("headers/session.php");
	
	$link = Session::forceConnectDB();
	$answer = mysqli_real_escape_string($link, base64_decode($_GET["answer"], true));
	$id = mysqli_real_escape_string($link, $_GET["id"]);
	$parent_id = mysqli_real_escape_string($link, $_GET["parent_id"]);

	$query = "select ANSWER from PROBLEM WHERE PARENT=".$parent_id." AND ID=".$id.";";
	$result=mysqli_query($link, $query);
	$real_answer = $result->fetch_row()[0];
	$correctness = (trim($real_answer) == trim($answer));
	if($correctness){
		echo("Correct");
}	else{
		echo("Incorrect");
	}
?>
