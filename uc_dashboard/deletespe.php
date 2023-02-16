<?php
	include "connection.php";
	session_start();
//$ucid = $_SESSION["UCID"] 
//$ucid = 'U666666';
	if(isset($_SESSION['login']))
	{
		if(isset($_SESSION['ucid']))
		{
			$ucid = $_SESSION['ucid'];
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

$speid = $_POST['speid'];
$unitCode = $_POST['unitcode'];

$sql_deleteSPEQuestion = "UPDATE spe SET Visibility = false WHERE SPEID = '$speid'";
$request_spe = $connection->query($sql_deleteSPEQuestion);
if ($request_spe === TRUE){
	echo "<div class='panel-body'>";
	echo "<div class='panel-body'>";
	$sql_selectSPE = "SELECT * from SPE WHERE UnitCode = '$unitCode' AND UCID = '$ucid' AND Visibility = true";
	$request_spe = $connection->query($sql_selectSPE);
	if ($request_spe -> num_rows >0){
		$speCounter = 1;
		while ($row_spe= $request_spe->fetch_assoc()){
			echo "<div class='row movedown'>";
			echo "<div id='".$row_spe['SPEID']."' class='cSPERecords col-md-12 col-xs-12' style='padding:0px;'><a class='spename'>SPE".$speCounter."</a>";
				echo "<img class='cSPEHandler cEdit' src='images/edit.png'>";
				echo "<img class='cSPEHandler cDownload' src='images/download.png'>";
				echo "<img class='cSPEHandler cDueDate' src='images/calendar.png'>";
				echo "<img class='cSPEHandler cDelete' src='images/delete.png'>";
			echo "</div>";
		echo "</div>";
		$speCounter++;
		}
	}
	echo "</div>";
	echo "</div>";
}
?>