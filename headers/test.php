<?php
echo("AY:");
include_once("../headers/security.php");
echo("INSERT INTO ADMIN (HASH,USERNAME) VALUES('".Security::makeSaltedHash(Security::DEFAULT_PASSWORD) . "','admin');")
?>
