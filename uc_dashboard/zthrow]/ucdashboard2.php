<?php ob_start(); ?>
<?php
include 'connection.php';
session_start();
//$ucid = $_SESSION["UCID"] 
$ucid = 'U666666';


?>

<html>
<head>
    <meta charset="utf-8" />
	<script src="https://code.jquery.com/jquery-3.5.1.js"></script>	
	<link rel="stylesheet" type="text/css" href="CSS/style.css">
	<!--<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans" />-->
    <title>UC Dashboard</title>
</head>
<body>
<script type="text/javascript" src="JS/action.js"></script>

<div class="cHeader">
  <h1>Self Peer Evaluation System</h1>
</div>

<?php
$sql_selectname = "SELECT Name from unitcoordinator WHERE UCID = '$ucid'";
$request= $connection->query($sql_selectname);
	if ($request-> num_rows>0){
		while ($row= $request->fetch_assoc()){
			echo "<div class='cName'>";
			echo "<span>Welcome,".$row['Name']."</span>";
			echo "<button id='iBtnUpload' class='cButtons cButtonAction' type='button'>Upload Student List</button>";
			echo "</div>";
		}
		//echo mysqli_num_rows($request);
	}
?>
<?php
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
	//Select Unit Code from StudentListDetail table based on TeachPeriod and UCID
	$sql= "SELECT studentlistdetail.UnitCode AS UnitCode FROM studentlist LEFT JOIN studentlistdetail ON studentlist.StudentListID = studentlistdetail.StudentListID 
		WHERE studentlist.UCID = '$ucid' AND studentlistdetail.TeachPeriod = '$period' GROUP BY studentlistdetail.UnitCode";
	$request= $connection->query($sql);
	if ($request-> num_rows>0){
		while ($row= $request->fetch_assoc()){
			$MarkLowerThanTwo = 0;
			$unitCode = $row['UnitCode'];
			//echo "<script>alert('".$unitCode."')</script>";
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
					//Display data (Each Unit in one Div)
					if ($MarkLowerThanTwo > 0)
					{
						echo "<div id='".$row_unit['UnitCode']."' class='cUnit cToggle cNotice'>";//this
					}
					else
					{
						echo "<div id='".$row_unit['UnitCode']."' class='cUnit cToggle'>";//this
					}
					echo "<div class='cLineContainer'>";
					echo "<span class='cUnitName'>".$row_unit['UnitCode']." ".$row_unit['UnitName']."</span>";
					echo "<button class='cBtnAddSPE' type='button'>Add SPE</button>";
					if ($MarkLowerThanTwo > 0)
					{
						echo "<button class='cBtnCheckGroup' type='button'>Check Problem Group</button>";//this
					}
					echo "<img class='cToggleIcon' src='images/toggledown.png'>";
					echo "</div>";
					echo "</div>";
					
					//Select SPE from SPE table
					$sql_selectSPE = "SELECT * from SPE WHERE UnitCode = '$unitCode' AND UCID = '$ucid' AND Visibility = true";
					$request_spe = $connection->query($sql_selectSPE);
					if ($request_spe -> num_rows >0){
						//echo "<script>alert('".$MarkLowerThanTwo."')</script>";
						//Start display
						echo "<div class='cSPEContainer ".$row_unit['UnitCode']."'>";
						$speCounter = 1;
						while ($row_spe= $request_spe->fetch_assoc()){
							echo "<div class='cSPE'>";
							echo "<div id='".$row_spe['SPEID']."' class='cSPERecords'>";
							echo "<span class='cUnitName'>SPE ".$speCounter."</span>";
							echo "<img class='cSPEHandler cEdit' src='images/edit.png'>";
							echo "<img class='cSPEHandler cDownload' src='images/download.png'>";
							echo "<img class='cSPEHandler cDueDate' src='images/calendar.png'>";
							echo "<img class='cSPEHandler cDelete' src='images/delete.png'>";
							echo "</div>";
							echo "</div>";
							$speCounter++;
						}
						echo "</div>";
					}
				}
			}
			//echo "<p>".$row['UnitCode']."</p>";
		}
		//echo mysqli_num_rows($request);
	}
?>

</body>
</html>