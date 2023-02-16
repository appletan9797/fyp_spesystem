<?php
	include "connection.php";
	session_start();
		
	if(isset($_SESSION['login']))
	{
		if(isset($_SESSION['ucid']))
		{
			$id = $_SESSION['ucid'];
		}
		else if(isset($_SESSION['studentid']))
		{
			//header("Location:../student_dashboard/student_dashboard.php");
			//$student_id = $_SESSION["studentid"];
			header("Location:../login/noright.php");
		}
	}
	else
	{
		header("location:../login/logintoaccess.php");
	}
	
	$spetemplateid = $_POST['speid'];
	
	$delete_spetemplate_sql = "DELETE FROM spe_template WHERE SPETemplateID= $spetemplateid";
	$delete_result = $connection->query($delete_spetemplate_sql);
	
	if(!$delete_result)
	{
		trigger_error('Invalid query: '.$connection->error);
	}
?>