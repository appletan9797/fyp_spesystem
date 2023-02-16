<?php
	include "connection.php";
	ob_start();
	session_start();
	
	unset($_SESSION['arrayOfRecord']);
	unset($_SESSION["totalmembercount"]);
	unset($_SESSION["RecordCounter"]);
	unset($_SESSION["currentpageid"]);
	if(isset($_SESSION['login']))
	{
		if(isset($_SESSION['ucid']))
		{
			header("Location:../login/noright.php");
		}
		else if(isset($_SESSION['studentid']))
		{
			//header("Location:../student_dashboard/student_dashboard.php");
			$student_id = $_SESSION["studentid"];
		}
	}
	else
	{
		header("location:../login/logintoaccess.php");
	}
	
	//check current month and year
	//JAN - MAR: S1
	//APR - JUL: S2
	//AUG - NOV: S3
	
	$month = date("m");
	if(($month >= 1 && $month <= 3))
	{
		$term = "S1";
	}
	else if(($month >= 4 && $month <= 7))
	{
		$term = "S2";
	}
	else if(($month >= 8 && $month <= 12))
	{
		$term = "S3";
	}
	
	$year = date("Y");
	$period = $term." ".$year;
		
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Student Dashboard</title>
	
	<!--Styles-->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="Technical Solutions a Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template,
	martphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
	<link href="css/student_dashboard.css" rel="stylesheet" type="text/css" media="all" />
	<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
	
	<!--Javascript-->
	<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
	<!--<script src="js/create_spe.js"></script>-->

	<!-- font-awesome-icons -->
	<link rel="stylesheet" href="css/font-awesome.min.css" />
	<!-- //font-awesome-icons -->

	<!-- google fonts -->
	<link href="//fonts.googleapis.com/css?family=Muli:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;subset=latin-ext,vietnamese" rel="stylesheet">
	<link href="//fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&amp;subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese" rel="stylesheet">
	<!-- //google fonts -->
</head>

<body>
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
			<span ><img onclick="window.location.href='../login/logout.php'" style="cursor:pointer;width:40px;height:40px;" src="css/spe_profile_icon.png"><span>
		</div>
	</div>
	
	<div class="col-md-12" style="height:50px;">
	<!--test-->
	</div>
	
	<!--Welcome student message-->
	<div class="col-md-12" style="text-align :center">
		<div class="col-md-6" style="padding-top:8px;padding-bottom:8px;text-align :center;font-weight:bold;">
			<?php
				echo "<label class='profile_name_label'> Welcome, ".$student["Name"]."</label>";
			?>
		</div>
	</div>
	
	<div class="col-md-12" style="">
	<div class="col-md-3"> </div>
	
	<!--List of modules the student took-->
	<div class="col-md-6">
		<div class="wrapper center-block">
			<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
				<?php
					$select_sql = "SELECT unitcode FROM studentlistdetail WHERE StudentId='".$student_id."' AND TeachPeriod='".$period."'";
					$select_result = $connection->query($select_sql);
					$count = 0;
					if($select_result->num_rows > 0)
					{
						while($unit = $select_result->fetch_assoc())
						{
							$select_unitname = "SELECT unitname FROM unit WHERE unitcode='".$unit['unitcode']."'";
							$unitname_result = $connection->query($select_unitname);
							if ($unitname_result -> num_rows >0){
								while ($unitname= $unitname_result->fetch_assoc())
								{
									echo "<div class='panel panel-default'>";
									echo "<div class='panel-heading active' role='tab' id='heading".$count."'>";
										echo "<h4 class='panel-title'>";
											echo "<a role='button' data-toggle='collapse' data-parent='#accordion' href='#collapse".$count."' aria-expanded='true' aria-controls='collapse".$count."'>";
												echo $unit['unitcode']." ".$unitname['unitname'];
											echo "</a>";
										echo "</h4>";
									echo "</div>";
									
									echo "<div id='collapse".$count."' class='panel-collapse collapse in' role='tabpanel' aria-labelledby='heading".$count."'>";
										echo "<div class='panel-body'>";
										echo "<div class='panel-body'>";
											echo "<div class='row'>";
												$select_spe_sql = "SELECT * FROM spe WHERE UnitCode='".$unit['unitcode']."'";
												$select_spe_result = $connection->query($select_spe_sql);
												if($select_spe_result->num_rows > 0)
												{
													$today = date("Y-m-d") ;
													$counter = 1;
													while($speid = $select_spe_result->fetch_assoc())
													{
														//echo "<script>alert(".$speid['speid'].");</script>";
														//get completion status of spe
														$speidrecord = $speid['SPEID'];
														$completion_sql = "SELECT * FROM spe_completionstatus WHERE SPEID =".$speidrecord." AND StudentID=".$student_id."";
														$completion_result = $connection->query($completion_sql);
														if($completion_result->num_rows > 0)
														{
															echo "<div class='col-md-4 col-xs-4' style='padding:0px;'><a style='cursor: not-allowed;'>SPE".$counter."</a></div>";
															echo "<div class='col-md-4 col-xs-4'>Completed</div>";
														}
														else
														{
															if ($speid['DueDate'] < $today)
															{
																echo "<div class='col-md-4 col-xs-4' style='padding:0px;'><a style='cursor: not-allowed;'>SPE".$counter."</a></div>";
																echo "<div class='col-md-4 col-xs-4'>Incomplete</div>";
															}
															else
															{
																echo "<div class='col-md-4 col-xs-4' style='padding:0px;'><a href='../evaluate/evaluation.php?spe=".$speid['SPEID']."&unit=".$unit['unitcode']."'>SPE".$counter."</a></div>";
																echo "<div class='col-md-4 col-xs-4'>Incomplete</div>";
															}
															
														}
														
														//display due date of spe
														echo "<div class='col-md-4 col-xs-4'>".$speid['DueDate']."</div>";
														$counter++;
													}
												}
											echo "</div>";
										echo "</div>";
										echo "</div>";
									echo "</div>";
								echo "</div>";
								$count = $count + 1;
								}
							}
							
						}
					}
				?>
			</div>
		</div>
	</div>
	
	<!-- here stars scrolling icon -->
	<script type="text/javascript">
	 $('.panel-collapse').on('show.bs.collapse', function (){
		$(this).siblings('.panel-heading').addClass('active');
	 });
	
	 $('.panel-collapse').on('hide.bs.collapse', function () {
		$(this).siblings('.panel-heading').removeClass('active');
	});
	</script>
	<!-- //here ends scrolling icon -->
	
	<!-- for bootstrap working -->
	<script src="js/bootstrap.js"></script>
	<!-- //for bootstrap working -->
</body>
</html>