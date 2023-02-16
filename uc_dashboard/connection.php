<?php
	$host = "localhost";
	$username= "root";
	$password="root";
	$db="fyp";

	$connection= new mysqli ($host,$username,$password, $db);

	if ($connection->connect_error){
		die ("Connection failed : ".$connection->connect_error);
	}
?>