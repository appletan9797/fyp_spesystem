<?php
	$host = "134.115.149.233";
	$username= "ICT302-TMA-FT07";
	$password="root";
	$db="fyp";

	//create connection
	$connection= new mysqli($host,$username,$password, $db);
	
	//check connection
	if (mysqli_connect_error())
	{
		die ("Connection failed : ".$connection->connect_error);
	}
?>