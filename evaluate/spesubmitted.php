<?php
		session_start();
		//unset($_SESSION['current']);
		unset($_SESSION["RecordCounter"]);
		unset($_SESSION["arrayOfRecord"]);
		include 'connection.php';
		if(isset($_SESSION['login']))
		{
			if(isset($_SESSION['ucid']))
			{
				header("Location:../login/noright.php");
			}
			else if(isset($_SESSION['studentid']))
			{
				//header("Location:../student_dashboard/student_dashboard.php");
				//$student_id = $_SESSION["studentid"];
				$student_id = $_SESSION['studentid'];
			}
		}
		else
		{
			header("location:../login/logintoaccess.php");
		}
?>

<html>
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    <title>Evaluation</title>
    <link href="css/evaluation.css" rel="stylesheet" type="text/css">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
	<!-- font-awesome-icons -->
	<link rel="stylesheet" href="css/font-awesome.min.css" />
	<!-- //font-awesome-icons -->

	<!-- google fonts -->
	<link href="//fonts.googleapis.com/css?family=Muli:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;subset=latin-ext,vietnamese" rel="stylesheet">
	<link href="//fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&amp;subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese" rel="stylesheet">
	<!-- //google fonts -->
    <title>Evaluation Submitted</title>
</head>
<body>
<script type="text/javascript" src="JS/evaluation.js"></script>

<div class="col-md-12 col-xs-12" id="contact" style="padding:0px;">
	<div class="col-md-12 col-xs-12" style="padding:0px;">
	
		<!--Page header-->
		<div class="col-md-12 col-xs-12" style="background-color:#1d1a1a;">
			<div class="col-md-6 col-xs-6" style="padding-top:8px;padding-bottom:8px;text-align:left;font-size:x-large;font-weight:bold;color:#ffffff;">
				<div class="header"><span onclick="window.location.href='../login/index.php'">Self Peer Evaluation System</span></div>
			</div>
			
			<div class="col-md-6" style="padding-top:8px;padding-bottom:8px;text-align:right;padding:0px;color:#ffffff;">
				<?php
					//get student name from the database
					$select_sql = "SELECT * FROM student WHERE StudentID = '$student_id'";
					$result = $connection->query($select_sql);
					$student = $result->fetch_assoc();
					echo "<label class='profile_name_label'>".$student["Name"]."</label>";
				?>
				<span ><img onclick="window.location.href='../login/logout.php'" style="cursor:pointer; width:40px;height:40px;" src="images/spe_profile_icon.png"><span>
			</div>
		</div>
		
		<div class="cUploadBox" id="iDropUpload">
		<h2 id="iSubmitted"> You have submitted SPE </h2>
		</div>

		<div class="cBtnContainer">
			<button id="iBtnBack" class="cButtons cButtonAction" type="button">Go Back</button>
		</div>
	</div>
</div>
</body>
</html>