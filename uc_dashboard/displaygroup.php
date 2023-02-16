<?php
	ob_start();
	include "connection.php";
	session_start();

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
	
	$unitCode = $_GET['unit'];
	//Check current month and year 
	//Jan - Mar : S1
	//Apr - Jul : S2
	//Aug - Nov : S3
	$month = date("m");
	if (($month >= 1 && $month <=3))
	{
		$term = "S1";
	}
	else if (($month >= 4 && $month <=7))
	{
		$term = "S2";
	}
	else if (($month >= 1 && $month <=3))
	{
		$term = "S3";
	}
	$year = date("Y");
	$period = $term." ".$year;
?>

<html>
<head>
    <meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
	<script src="https://code.jquery.com/jquery-3.5.1.js"></script>	
	<link rel="stylesheet" type="text/css" href="CSS/style.css">
	<!-- font-awesome-icons -->
	<link rel="stylesheet" href="css/font-awesome.min.css" />
	<!-- //font-awesome-icons -->

	<!-- google fonts -->
	<link href="//fonts.googleapis.com/css?family=Muli:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;subset=latin-ext,vietnamese" rel="stylesheet">
	<link href="//fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&amp;subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese" rel="stylesheet">
	<!-- //google fonts -->
    <title>Check Group</title>
</head>
<body>
<script type="text/javascript" src="JS/action.js"></script>

<div class="col-md-12 col-xs-12" id="contact" style="padding:0px;">
	<div class="col-md-12 col-xs-12" style="padding:0px;">
	
	<!--Page header-->
	
	<div class="col-md-12 col-xs-12" style="background-color:#1d1a1a;">
		<div class="col-md-6 col-xs-6" style="padding-top:8px;padding-bottom:8px;text-align:left;font-size:x-large;font-weight:bold;color:#ffffff;">
			<div class="header"><span onclick="window.location.href='../login/index.php'">Self Peer Evaluation System</span></div>
		</div>

		<div class="col-md-6" style="padding-top:8px;padding-bottom:8px;text-align:right;padding:0px;color:#ffffff;">
		<?php
		$sql_selectname = "SELECT * from unitcoordinator WHERE UCID = '$ucid'";
		$request= $connection->query($sql_selectname);
			if ($request-> num_rows>0){
				while ($row= $request->fetch_assoc()){
					$ucname = $row['Name'];
					echo "<label class='profile_name_label'>".$row["Name"]."</label>";
				}
			}
		?>
		<span ><img onclick="window.location.href='../login/logout.php'" style="cursor:pointer;width:40px;height:40px;" src="images/spe_profile_icon.png"><span>
		</div>
	</div>
	
	<div class="cTitle">
		<h2>Check Group With Problem</h2>
	</div>

	<table style="width:50%">
	 <tr>
		<th>Score Giver Student ID</th>
		<th>Score Receiver Student ID</th>
		<th>Question No</th>
		<th>Team ID</th>
	  </tr>
	  <tr>
		<?php
			//Check whether there's SPE score lower than 2
			//Get CompletionID
			$sql_selectCompletion = "SELECT spe_completionstatus.CompletionID AS CompletionID FROM spe_completionstatus LEFT JOIN studentlistdetail 
									ON studentlistdetail.StudentID = spe_completionstatus.StudentID 
									WHERE studentlistdetail.UnitCode ='".$unitCode."' AND studentlistdetail.TeachPeriod = '$period'";// GROUP BY studentlistdetail.UnitCode";
			$request_completion = $connection->query($sql_selectCompletion);
			if ($request_completion -> num_rows >0){
				//$no = mysqli_num_rows($request_completion);
				//echo "<script>alert('the row is ".$no."')</script>";
				while ($row_completion= $request_completion->fetch_assoc())
				{
					//echo "<script>alert('the completion is ".$row_completion['CompletionID']."')</script>";
					//Get results from spe_result based on CompletionID
					$sql_selectSPEResult = "SELECT * from spe_result WHERE CompletionID = '".$row_completion['CompletionID']."'";// GROUP BY ReceiverStudentID";
					$request_speresult = $connection->query($sql_selectSPEResult);
					if ($request_speresult -> num_rows >0){
						while ($row_speresult= $request_speresult->fetch_assoc())
						{
							$score = $row_speresult['Score'];
							if ($score <= 2)
							{
								//echo $currentStudentID;
								//echo "<br>";
								//echo $row_speresult['ReceiverStudentID'];
								$MarkReceiverID = $row_speresult['ReceiverStudentID'];
								
								$sql_selectCurrentStudent = "SELECT * from spe_completionstatus WHERE CompletionID = '".$row_completion['CompletionID']."'";
								$request_currentstudent = $connection->query($sql_selectCurrentStudent);
								if ($request_currentstudent -> num_rows >0)
								{
									while ($row_currentstudent= $request_currentstudent->fetch_assoc()){
										$currentStudentID = $row_currentstudent['StudentID'];
										$sql_selectCurrentName = "SELECT * from studentlistdetail WHERE StudentID ='$currentStudentID' AND TeachPeriod = '$period' AND UnitCode = '$unitCode'";
										$request_currentname = $connection->query($sql_selectCurrentName);
										if ($request_currentname -> num_rows >0){
											while ($row_currentname= $request_currentname->fetch_assoc()){
												echo "<td>".$currentStudentID."<br>".$row_currentname['Surname']." ".$row_currentname['GivenName']."</td>";
											}
										}
									}
								}
								
								$sql_selectReceiverName= "SELECT * from studentlistdetail WHERE StudentID ='$MarkReceiverID' AND TeachPeriod = '$period' AND UnitCode = '$unitCode'";
								$request_receivername = $connection->query($sql_selectReceiverName);
								if ($request_receivername-> num_rows >0){
									while ($row_receivername= $request_receivername->fetch_assoc()){
										echo "<td>".$currentStudentID."<br>".$row_receivername['Surname']." ".$row_receivername['GivenName']."</td>";
										$teamid =$row_receivername['TeamId'];
									}
								}
								echo "<td>".$row_speresult['QuestionNo']."</td>";
								echo "<td>".$teamid."</td>";
								echo "</tr>";
							}
						}
					}
				}
			}
		?>
	</table>

	<div class="cBtnContainer">
		<button id="iBtnBack" class="cButtons cButtonAction" type="button">Back to Dashboard</button>
	</div>
	</div>
</div>



</body>
</html>