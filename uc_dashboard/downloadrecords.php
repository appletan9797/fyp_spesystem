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
	/*else
	{
		header("location:../login/logintoaccess.php");
	}*/
	$speid = $_GET['spe'];
	$unitcode = $_GET['unit'];

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

	
//Put header into file
$header = array("Person Id","Surname", "Title", "Given Name", "Teach Period", "Unit Code", "Team ID","Email","SPE 1","SPE 2");
$filename = 'SPE.csv';
$file = fopen($filename,"w");
fputcsv($file,$header);

//Select all student detail from StudentListDetail table (Based on UnitCode,Period and UCID)
$sql_selectstudentID = "SELECT studentlistdetail.StudentID AS StudentID, studentlistdetail.Surname As Surname, 
studentlistdetail.Title AS Title, studentlistdetail.GivenName AS GivenName, studentlistdetail.TeachPeriod AS TeachPeriod,
studentlistdetail.UnitCode as UnitCode, studentlistdetail.TeamID AS TeamID, studentlistdetail.Email AS Email
FROM studentlistdetail LEFT JOIN studentlist ON studentlistdetail.StudentListID = studentlist.StudentListID
WHERE studentlist.UCID = '$ucid' AND studentlistdetail.UnitCode = '$unitcode' AND TeachPeriod = '$period'";

$request_studentid = $connection->query($sql_selectstudentID);
if ($request_studentid -> num_rows >0){
	while ($row_studentid= $request_studentid->fetch_assoc()){
		$studentid = $row_studentid['StudentID'];
		$sql_selectduedate = "SELECT * FROM spe WHERE UnitCode='$unitcode' AND UCID= '$ucid' AND Visibility = TRUE";
		$request_duedate = $connection->query($sql_selectduedate);
		if ($request_duedate-> num_rows >0)
		{
			while ($row_duedate= $request_duedate->fetch_assoc())
			{
				$duedate = $row_duedate['DueDate'];
				$spe_id = $row_duedate['SPEID'];
				
				$sql_selectSPENO = "SELECT SPE_No FROM spe_speno WHERE SPEID = '$spe_id'";
				$request_speno = $connection->query($sql_selectSPENO);
				if ($request_speno-> num_rows >0)
				{
					while ($row_speno= $request_speno->fetch_assoc())
					{
						//Get SPE No, if 1, then is SPE1, if 2 is SPE2, etc
						$spe_no_no = $row_speno['SPE_No'];
					}
				}
				
				if(strtotime(date("Y-m-d")) > strtotime($duedate)) //Its due, show score
				{
					$sql_selectcompletionstatus = "SELECT * FROM spe_completionstatus WHERE SPEID = '$spe_id' AND StudentID = '$studentid'";
					$request_completionstatus = $connection->query($sql_selectcompletionstatus);
					if ($request_completionstatus-> num_rows >0) //This one is to check if completed, if yes, then check score
					{
						//Get score
						$sql_selectscore = "SELECT Score FROM spe_finalscore WHERE SPEID = '$spe_id' AND StudentId = '$studentid'";
						$request_score = $connection->query($sql_selectscore);
						if ($request_score-> num_rows >0) //If got result, means
						{
							while ($row_score= $request_score->fetch_assoc())
							{
								//Get score
								$spe_score = $row_score['Score'];
							}
						}
						
						//Assign value to respective variable based on spe_no_no, if 1 = spe1result, if 2 = spe2result, etc
						if($spe_no_no == 1)
						{
							$spe1due = $spe_score;
						}
						else
						{
							$spe2due = $spe_score;
						}
					}//kk
					else //Else assign 0 for that SPE
					{
						if($spe_no_no == 1)
						{
							$spe1due = 0;
						}
						else
						{
							$spe2due = 0;
						}
					}
				}
				else //Havent due, show Not Due
				{
					if($spe_no_no == 1)
					{
						$spe1due = "Not Due";
					}
					else
					{
						$spe2due = "Not Due";
					}
				}//end else
			}//stop while
		
			//If only 1 SPE found, means second spe not set
			if ($request_duedate-> num_rows == 1)
			{
				$spe1due = $spe1due;
				$spe2due = "NOT SET";
			}
		}
		//Push data into array (for each student record)
		$users[] = array($row_studentid['StudentID'],$row_studentid['Surname'],$row_studentid['Title'],$row_studentid['GivenName'],$row_studentid['TeachPeriod'],$row_studentid['UnitCode'],$row_studentid['TeamID'],$row_studentid['Email'],$spe1due,$spe2due);
	}
	if (count($users) > 0)
	{
		foreach ($users as $row)
		{
			fputcsv($file,$row);
		}
	}
}

fclose($file);
// download
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=".$filename);
header("Content-Type: application/csv; "); 

readfile($filename);
unlink($filename);
?>