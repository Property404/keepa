<?php
	/*
		This is a static class containing
		functions related to the admin
		session. It deals with non-
		cryptographic security and the
		initial creation of the 
		database.
	*/
	
	class Session{
		//Private Constants
		const _DB_NAME="keepa";
		const _SQL_USERNAME="root";
		const _SQL_PASSWORD="hunter2";
		const _ADMIN_TABLE_DEFINITION="CREATE TABLE ADMIN (
								ID int(11) AUTO_INCREMENT,
								USERNAME varchar(128) NOT NULL,
								HASH varchar(512) NOT NULL,
								PRIMARY KEY(ID)
								);";

		const _ANSWERKEY_TABLE_DEFINITION="CREATE TABLE ANSWERKEY(
									ID int(11) AUTO_INCREMENT,
									NAME varchar(128) NOT NULL,
									HEADER varchar(128),
									PRIMARY KEY(ID));";
		
		const _PROBLEM_TABLE_DEFINITION='CREATE TABLE PROBLEM(
									ID int(11) AUTO_INCREMENT,
									QUESTION varchar(128),
									ANSWER varchar(11),
									PARENT int(11) NOT NULL,
									FOREIGN KEY(PARENT) REFERENCES ANSWERKEY(ID) ON DELETE CASCADE,
									PRIMARY KEY(ID));';
										
		//Private methods
		private static function setInitialAdminTable($link){
			include_once("../headers/security.php");
			mysqli_query($link,"INSERT INTO ADMIN (HASH,USERNAME) VALUES('".Security::makeSaltedHash(Security::DEFAULT_PASSWORD) . "','admin');");
		}
		
		//Create database/table if they don't exist
		public static function forceConnectDB(){
			$db_name=self::_DB_NAME;
			
			//Connect with SQL
			$link=mysqli_connect("localhost",self::_SQL_USERNAME, self::_SQL_PASSWORD);
			if(!$link){
				die("<div class='alert'>Can't connect to database</div>" );
			}else{
			}
			
			//Try to connect to database
			$database=mysqli_select_db($link,"$db_name");
			
			//Create database if it doesn't exist
			if(!$database){
				mysqli_query($link,"CREATE DATABASE $db_name;");
				$database=mysqli_select_db($link,"$db_name");
				if(!$database)
					echo("<div class='alert'>Error: database creation failed</div><br>");
				
			}
			
			//Attempt to create table if it doesn't exist
			if(empty(mysqli_query($link, "SELECT ID FROM ADMIN"))){
				echo("Creating table\n");
				mysqli_query($link,self::_PROBLEM_TABLE_DEFINITION);
				mysqli_query($link,self::_ANSWERKEY_TABLE_DEFINITION);
				mysqli_query($link,self::_ADMIN_TABLE_DEFINITION);
				self::setInitialAdminTable($link);
			}
			return $link;
		}
		
		
		//If not in session, redirect to login page
		public static function checkAdminSession(){
			//Make sure using TLS
			/*if (!isset($_SERVER['HTTPS']) || !$_SERVER['HTTPS']) {
				header("Location: https://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
			}*/
			
			//Check session
			session_start();
			if(!array_key_exists('admin_session',$_SESSION)){
				header("Location: ../admin/login.php");
			}
			
		}
	}
?>
