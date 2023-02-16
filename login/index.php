<?php
	include "connection.php";
	ob_start();
	session_start();
	
	if(isset($_SESSION['login']))
	{
		if(isset($_SESSION['ucid']))
		{
			header("Location:../uc_dashboard/ucdashboard.php");
		}
		else if(isset($_SESSION['studentid']))
		{
			header("Location:../student_dashboard/student_dashboard.php");
		}
	}
	else
	{
		//header("location:logintoaccess.php");
	}
	$error;
	if(isset($_POST['submit']))
	{
		if(isset($_POST['userid']))
		{
			$userid = $_POST['userid'];
		}
		if(isset($_POST['password']))
		{
			$password = $_POST['password'];
		}
		
		$firstCharacter = $userid[0];
		if (is_numeric($firstCharacter)) //Look through in student db
		{
			$sql_student = "SELECT * FROM student WHERE StudentID = '$userid' AND Password = '$password'";
			$result_student = $connection -> query($sql_student);
			if ($result_student -> num_rows == 1){
				$_SESSION['studentid'] = $userid;
				$_SESSION['login'] = 1;
				header("Location:../student_dashboard/student_dashboard.php");
			}
			else
			{
				$error = "Invalid Username / Password";
			}
		}
		else //Look through uc db
		{
			$sql_student = "SELECT * FROM unitcoordinator WHERE UCID = '$userid' AND Password = '$password'";
			$result_student = $connection -> query($sql_student);
			if ($result_student -> num_rows == 1){
				$_SESSION['ucid'] = $userid;
				$_SESSION['login'] = 1;
				header("Location:../uc_dashboard/ucdashboard.php");
			}
			else
			{
				$error = "Invalid Username / Password";
			}
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Login</title>

<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Technical Solutions a Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template, 
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />

<link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
<link rel="stylesheet" href="css/font-awesome.min.css" />

<!--<script type="text/javascript" src="js/jquery-2.1.4.min.js"></script>-->
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>	
<script type="text/javascript" src="js/script.js"></script>
<script src="js/bootstrap.js"></script>

<!-- google fonts -->
<link href="//fonts.googleapis.com/css?family=Muli:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;subset=latin-ext,vietnamese" rel="stylesheet">
<link href="//fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&amp;subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese" rel="stylesheet">
<!-- //google fonts -->
</head>
	
<body>
<div class="col-md-12" id="contact" style="padding:0px;">
<div class="col-md-12" style="padding:0px;">
	<h3 class="col-md-12" style="padding-top:8px;padding-bottom:8px;text-align:left;font-size:x-large;font-weight:bold;background-color:#1d1a1a;color:#ffffff;">Self Peer Evaluation System</h3>
	<div class="col-md-12" style="height:100px;"></div>
	<div class="col-md-3"> </div>
	<div class="col-md-6">
		<div class="col-md-12" style="text-align:center;font-size:bold;font-weight:bold;">Login</div>
		<div class="login-form">
			<?php
				if (isset($error))
				{
					echo "<div id='iAttention' class='cMessage'> <img id='iAttentionImg' class='cMsgImg' src='images/error.png'><font id='iAttentionMsg' class='cMsgLine'>". $error."</font></div>";
				}
			?>
			<form id="form" action="" method="post">
				<div class="form-group">
					<input type="text" name="userid" class="form-control cInput" placeholder="Enter your ID Number" required="required">
				</div>
				<div class="form-group">
					<input type="password" name="password" class="form-control cInput" placeholder="Enter your password" required="required">
				</div>
				<div class="form-group">
					<input type="submit" name="submit" style="color:#ffffff;background: #005da6;" class="btn btn-block" value="Sign In"></button>
					
				</div>
				<!--<div class="clearfix">
					<a href="#" class="pull-right">Forgot Password?</a>
				</div> -->
			</form>
		</div>	
		<div class="col-md-3"> </div>
	</div>
</div>	
</body>
</html>