<?php
$servername = "134.115.149.233";
$username= "ICT302-TMA-FT07";
$password="root";
$db="fyp";

$connection= new mysqli ($servername,$username,$password, $db);

if ($connection->connect_error){
	die ("Connection failed : ".$connection->connect_error);
}
?>