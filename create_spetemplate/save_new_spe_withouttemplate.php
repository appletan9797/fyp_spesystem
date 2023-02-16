<?php
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
	//get submitted variables
	//$_SESSION["UCID"] = 666666;
	//$ucid = $_SESSION['UCID'];
	//Get SPE Template id
	//$templateid = $_POST['spe_type'];
	$unitcode = $_GET['unitid'];
	//$unitcode = "ICT302";
	
	if(isset($_POST['question_text']))
	{
		$questions = $_POST['question_text'];
	}
	else
	{
		$questions = "";
	}
	
	//echo $question;
	if(isset($_POST['duedate_text']))
	{
		$duedate = $_POST['duedate_text'];
	}
	else
	{
		$duedate = "";
	}
	
	if(isset($_POST['input_type']))
	{
		$input_type = $_POST['input_type'];
	}
	else
	{
		$input_type = "";
	}
	
	if(isset($_POST['section_type']))
	{
		$section_type = $_POST['section_type'];
	}
	else
	{
		$section_type = "";
	}
	
	//Not using in create spe template, only check when add new spe
	//Question ID
	if(isset($_POST['checkrecord']))
	{
		//$section_type = array($_POST['section_type']);
		$checkrecord = $_POST['checkrecord'];
	}
	else
	{
		$checkrecord = "";
	}
	
	$sql_insertspe = "INSERT INTO spe (UCID,UnitCode,DueDate,Visibility) VALUES ('$ucid','$unitcode','$duedate',1)";
	if($connection->query($sql_insertspe) === TRUE)
	{
		//Get SPEID
		$speid = $connection->insert_id;
		
		//Check how many visible spe to set the spe no in spe_speno
		$sql_selectspecount= "SELECT * FROM spe WHERE UCID = '$ucid' AND UnitCode = '$unitcode' AND Visibility = 1";
		$request_specount= $connection->query($sql_selectspecount);
		$numofrecords = mysqli_num_rows($request_specount);

		//Insert into spe_speno
		$spe_no = $numofrecords + 1;
		/*if($numofrecords == 0)
		{
			$spe_no = $numofrecords + 1;
		}
		else
		{
			$spe_no = $numofrecords;
		}*/
		$sql_insertno = "INSERT INTO spe_speno (SPEID,SPE_No) VALUES ('$speid','$spe_no')";
		if($connection->query($sql_insertno) === TRUE)
		{
			//echo "Insert successfully";
		}
		else
		{
			echo $connection->error();
		}
		
		//Insert spe question into spe_question table
		foreach($questions AS $key => $value)
		{
			$sql_insertspequestion = "INSERT INTO spe_question (SPEID,Question,InputType,Section) VALUES ('$speid','$questions[$key]','$input_type[$key]','$section_type[$key]')";
			if($connection->query($sql_insertspequestion) === TRUE)
			{
				//echo "Insert successfully";
			}
			
		}
		
		
	}
	else
	{
		//echo mysqli_error($connection);
	}
	
?>