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
	
	//$ucid = $_SESSION['UCID'];
	$ucid = $_POST['ucid'];
	$unitcode = $_POST['ucode'];
	$duedate = $_POST['duedate'];
	$spetemplateid = $_POST['spetemplateid'];
	
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
		
		//Insert spe question into spe_question table from spe_templatedetail
		$sql_selectspequestion= "SELECT * FROM spe_templatedetail WHERE SPETemplateID = '$spetemplateid'";
		$request_spequestion= $connection->query($sql_selectspequestion);
		if ($request_spequestion-> num_rows>0){
			while ($row_spequestion= $request_spequestion->fetch_assoc()){
				$question = $row_spequestion['Question'];
				$inputtype = $row_spequestion['InputType'];
				$section = $row_spequestion['Section'];
				
				$sql_insertspequestion = "INSERT INTO spe_question (SPEID,Question,InputType,Section) VALUES ('$speid','$question','$inputtype','$section')";
				if($connection->query($sql_insertspequestion) === TRUE)
				{
					//echo "Insert successfully";
				}
			}
		}
		
		//Save number of question to spe_speno
		
	}
	else
	{
		//echo mysqli_error($connection);
	}
?>