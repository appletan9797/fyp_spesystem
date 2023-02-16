<?php ob_start(); ?>
<?php
	include 'connection.php';
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
	else if (($month >= 8 && $month <=12))
	{
		$term = "S3";
	}
	$year = date("Y");
	$period = $term." ".$year;
?>

<!DOCTYPE html>
<html>
<head>
    <!--Styles-->
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
	
	<script src="https://code.jquery.com/jquery-3.5.1.js"></script>	
	<link rel="stylesheet" type="text/css" href="CSS/style.css">
	<link rel="stylesheet" type="text/css" href="CSS/uc_dashboard.css">
	
	<!-- font-awesome-icons -->
	<link rel="stylesheet" href="css/font-awesome.min.css" />
	<!-- //font-awesome-icons -->

	<!-- google fonts -->
	<link href="//fonts.googleapis.com/css?family=Muli:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;subset=latin-ext,vietnamese" rel="stylesheet">
	<link href="//fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&amp;subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese" rel="stylesheet">
	<!-- //google fonts -->
	
    <title>UC Dashboard</title>
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
	
	<div class="col-md-12" style="height:50px;">
	<!--test-->
	</div>
	
	<!--Welcome UC message-->
	<div class="col-md-12" style="text-align :center">
		<div class="col-md-6" style="padding-top:8px;padding-bottom:8px;text-align :center;font-weight:bold;">
			<?php
				echo "<label class='profile_name_label'> Welcome, ".$ucname."</label>";
				echo "<button id='iBtnUpload' class='cButtons cButtonAction' type='button'>Upload Student List</button>";
			?>
		</div>
	</div>
	
	<div class="col-md-12">
	<div class="col-md-3"> </div>
	
	<!--List of modules the UC teach-->
	<div class="col-md-6">
		<div class="wrapper center-block">
			<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
			<?php
				//Select Unit Code from StudentListDetail table based on TeachPeriod and UCID
				$sql= "SELECT studentlistdetail.UnitCode AS UnitCode FROM studentlist LEFT JOIN studentlistdetail ON studentlist.StudentListID = studentlistdetail.StudentListID 
					WHERE studentlist.UCID = '$ucid' AND studentlistdetail.TeachPeriod = '$period' GROUP BY studentlistdetail.UnitCode";
				$request= $connection->query($sql);
				$count = 0;
				if ($request-> num_rows>0){
					while ($row= $request->fetch_assoc()){
						$speCounter;
						$MarkLowerThanTwo = 0;
						$unitCode = $row['UnitCode'];
						//Check whether there's SPE score lower than 2
						//Get CompletionID
						$MarkLowerThanTwo = 0;
						$sql_selectCompletion = "SELECT spe_completionstatus.CompletionID AS CompletionID 
												FROM studentlistdetail LEFT JOIN spe_completionstatus ON studentlistdetail.StudentID = spe_completionstatus.StudentID 
												WHERE studentlistdetail.UnitCode ='".$unitCode."' AND studentlistdetail.TeachPeriod = '$period' GROUP BY studentlistdetail.UnitCode";
						$request_completion = $connection->query($sql_selectCompletion);
						if ($request_completion -> num_rows >0){
							while ($row_completion= $request_completion->fetch_assoc())
							{
								//Get results from spe_result based on CompletionID
								//echo "<script>alert('".$row_completion['CompletionID']."')</script>";
								$sql_selectSPEResult = "SELECT * from spe_result WHERE CompletionID = '".$row_completion['CompletionID']."'";
								$request_speresult = $connection->query($sql_selectSPEResult);
								if ($request_speresult -> num_rows >0){
									while ($row_speresult= $request_speresult->fetch_assoc()){
										$score = $row_speresult['Score'];
										if ($score <= 2)
										{
											$MarkLowerThanTwo = $MarkLowerThanTwo + 1;
										}
									}
								}
							}
						}
						
						//Select Unit Name from Unit table
						$sql_selectunit = "SELECT * from unit WHERE UnitCode = '$unitCode'";
						$request_unit = $connection->query($sql_selectunit);
						if ($request_unit -> num_rows >0){
							while ($row_unit= $request_unit->fetch_assoc())
							{
								if ($MarkLowerThanTwo > 0)
								{
									echo "<div class='panel panel-default cNotice' id='".$row_unit['UnitCode']."'>";
								}
								else
								{
									echo "<div class='panel panel-default' id='".$row_unit['UnitCode']."'>";
								}
								echo "<div class='panel-heading active ' role='tab' id='heading".$count."'>";
									echo "<h4 class='panel-title'>";
										echo "<a role='button' data-toggle='collapse' data-parent='#accordion' href='#collapse".$count."' aria-expanded='true' aria-controls='collapse".$count."'>";
											echo $row_unit['UnitCode']." ".$row_unit['UnitName'];
										echo "</a>";
										echo "<div class='cLineContainer'>";
										echo "<button class='cBtnAddSPE' type='button'>Add SPE</button>";
										if ($MarkLowerThanTwo > 0)
										{
											echo "<button class='cBtnCheckGroup' type='button'>Check Problem Group</button>";//this
										}
										echo "</div>";
									echo "</h4>";
								echo "</div>";
								
								echo "<div id='collapse".$count."' class='cSPEContainer ".$row_unit['UnitCode']." panel-collapse collapse in' role='tabpanel' aria-labelledby='heading".$count."'>";
									echo "<div class='panel-body'>";
									echo "<div class='panel-body'>";
										
											//Select SPE from SPE table
											$sql_selectSPE = "SELECT * from spe WHERE UnitCode = '".$row_unit['UnitCode']."' AND UCID = '$ucid' AND Visibility = true";
											$request_spe = $connection->query($sql_selectSPE);
											if ($request_spe -> num_rows >0)
											{
												//Start display
												$speCounter = 1;
												while ($row_spe= $request_spe->fetch_assoc())
												{
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