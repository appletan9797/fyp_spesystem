<?php
	include "connection.php";
	session_start();

	/*if(isset($_SESSION['login']))
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
	}*/
	//Check current month and year to use in getting student from studentlist
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
	
	//Select spe that due in 7 days
	$today = date("Y-m-d") ;
    $oneday =date('Y-m-d', strtotime($today. ' + 1 days')); 
	$sql_selectduespe = "SELECT * from spe WHERE DueDate = '$oneday' AND Visibility = TRUE";
	$request_duespe= $connection->query($sql_selectduespe);
	if ($request_duespe-> num_rows>0)
	{
		while ($row_duespe= $request_duespe->fetch_assoc()) //select record that has not submitted 7days before submission date
		{
			$unitcode = $row_duespe['UnitCode'];
			$speid = $row_duespe['SPEID'];
			$ucid = $row_duespe['UCID'];
			
			//Select UC Name
			$sql_selectuc = "SELECT * from unitcoordinator WHERE UCID = '$ucid'";
			$request_uc= $connection->query($sql_selectuc);
			if ($request_uc-> num_rows>0)
			{
				while ($row_uc= $request_uc->fetch_assoc())
				{
					$ucname = $row_uc['Name'];
				}
			}
			
			//Select in studentlist to check student taking this module
			$sql_selectstudent = "SELECT * from studentlistdetail WHERE UnitCode = '$unitcode' AND TeachPeriod = '$period'";
			$request_student= $connection->query($sql_selectstudent);
			if ($request_student-> num_rows>0)
			{
				while ($row_student= $request_student->fetch_assoc())
				{
					$studentid = $row_student['StudentId'];
					$email = $row_student['Email'];
					//$email = "33672925@student.murdoch.edu.au";
					$surname = $row_student['Surname'];
					$givenname = $row_student['GivenName'];
					$fullname = $surname." ".$givenname;
					//Check whether student completed SPE
					$sql_selectcompleterecord = "SELECT * from spe_completionstatus WHERE SPEID = '$speid' AND StudentID = '$studentid'";
					$request_completerecord= $connection->query($sql_selectcompleterecord);
					
					if ($request_completerecord-> num_rows <= 0)
					{
						$to = $email;
						$subject = $unitcode."-Warning: Self Peer Evaluation Due Tomorrow";
						$message = "
						Hi ".$fullname.",
						
						Please note that Self Peer Evaluation for ".$unitcode." is going to due tomorrow.

						Please complete before the due date:
						http://localhost:81/spesys/evaluate/evaluation.php?spe=$speid
						
						Best regards,".
						$ucname."
						";
						
						$headers = 'From: datos.tech.murdoch@gmail.com@gmail.com'. "\r\n";
						mail ($to, $subject, $message, $headers);
					}
				}
			}
		}
	}
?>