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
	
	$sql_delete = "DELETE FROM spe_speno WHERE SPEID = '$speid'";
	$request_delete = $connection->query($sql_delete);
	
	$sql_selectSPE = "SELECT * from SPE WHERE UnitCode = '$unitCode' AND UCID = '$ucid' AND Visibility = true";
	$request_spe = $connection->query($sql_selectSPE);
	$numofrecords = mysqli_num_rows($request_spe);
	if ($request_spe -> num_rows >0){
		$speCounter = 1;
		while ($row_spe= $request_spe->fetch_assoc()){
			$spe_id = $row_spe['SPEID'];
			echo "<div class='row movedown'>";
			echo "<div id='".$row_spe['SPEID']."' class='cSPERecords col-md-12 col-xs-12' style='padding:0px;'><a class='spename'>SPE".$speCounter."</a>";
			echo "<img class='cSPEHandler cEdit' src='images/edit.png'>";
			echo "<img class='cSPEHandler cDownload' src='images/download.png'>";
			echo "<img class='cSPEHandler cDueDate' src='images/calendar.png'>";
			echo "<img class='cSPEHandler cDelete' src='images/delete.png'>";
			echo "</div>";
			echo "</div>";
			$speCounter++;
			
			//Delete all spe no and put in again
			$sql_deleteall = "DELETE FROM spe_speno WHERE SPEID = '".$spe_id."'";
			$request_deleteall = $connection->query($sql_deleteall);
			
			//Insert spe no to database
			//$speno = $numofrecords;
			$sql_insertno = "INSERT INTO spe_speno (SPEID,SPE_No) VALUES ('$spe_id','$numofrecords')";
			if($connection->query($sql_insertno) === TRUE)
			{
				//echo "Insert successfully";
			}
			else
			{
				echo $connection->error();
			}
		}
	}
	echo "</div>";
	echo "</div>";
}
?>