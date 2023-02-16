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
	
	$ucid = $_POST['ucid'];
	$unitcode = $_POST['ucode'];
	$num = 1;
	$spe_no_sql = "SELECT * FROM spe_template WHERE UCID='$ucid'";
	$spe_no_result = $connection->query($spe_no_sql);
	if($spe_no_result-> num_rows > 0)
	{
		//$num = 1;
		while($spe_no = $spe_no_result->fetch_assoc())
		{
			echo "<option value='".$spe_no['SPETemplateID']."' class='option' id='".$spe_no['SPETemplateID']."'> SPE". $num."</option>";
			$num++;
		}
	}
	echo "<option value='-1' class='option' id='-1' selected> SPE". $num."</option>";
?>