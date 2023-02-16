<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
	<title>Create SPE Template</title>
	<link href="css/create_spe.css" rel="stylesheet" type="text/css">
	<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
	<script src="js/create_spe.js"></script>
</head>
<body>
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
		
		//define variables
		//$unit_id = "ICT302";
		/*if(isset($_SESSION['uid']))
		{
			$unit_id = $_SESSION['uid'];
		}
		else
		{
			$unit_id = $_GET['unit'];
			$_SESSION['uid'] = $_GET['unit'];
		}*/
		if (isset($_GET['unit']))
		{
			$unit_id = $_GET['unit'];
		}
		else
		{
			header("Location:../login/noright.php");
		}
		$output = NULL;
		
		//ensure that no input can run any malicious code
		function validate_input($input)
		{
			//strip unneccessary charaters from user input
			$input = trim($input);
			//remove backlashes from user input
			$input = stripslashes($input);
			//remove any html special charaters
			$input = htmlspecialchars($input);
			
			return $input;
		}
		
		$speid = "";
		if(isset($_POST['submit']))
		{
			//get submitted variables
			/*if(isset($_POST['spe_type']))
			{
				$speid = $_POST['spe_type'];
			}
			else
			{
				$speid = "";
			}*/
			
			//Get SPE Template id
			$templateid = $_POST['spe_type'];
			
			if(isset($_POST['question_text']))
			{
				$questions = $_POST['question_text'];
			}
			else
			{
				$questions = "";
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
			
			if($templateid == '-1') //Check whether its new template (new template)
			{	
				if($checkrecord != "") //If got add question
				{
					//echo "<script>alert('this is new record')</script>";
					$sql_savespetemplate = "INSERT INTO spe_template (UCID) VALUE ('$ucid')";
					if($connection->query($sql_savespetemplate) === TRUE)
					{
						$newtemplateid = $connection->insert_id;
						foreach($questions AS $key => $value)
						{
							$save_sql = "INSERT INTO spe_templatedetail(Question,InputType,Section,SPETemplateID) 
											VALUES('".validate_input($connection->real_escape_string($questions[$key]))."', 
											'".validate_input($connection->real_escape_string($input_type[$key]))."', 
											'".validate_input($connection->real_escape_string($section_type[$key]))."',
											'".validate_input($connection->real_escape_string($newtemplateid))."')";	
								
							if($connection->query($save_sql) === TRUE)
							{
								//echo "New record created successfully";
								//header("location: edit_question.php");
							}
							else
							{
								//echo "Error.";
								//header("location: edit_question.php");
							}
						}
					}
					else
					{
						//echo "There is a problem uploading ur record";
					}
				} //If never add question on new spe
			}//End if :Is new template
			else //Existing template
			{
				if($checkrecord != "") //If got add question
				{
					//Check each question text, in spe template, if there is identical one in database, update, if no, create new record
					foreach($questions AS $key => $value)
					{
						//get the checkrecord, if got id, then update record, if null, then insert
						$newrecordchecker = $checkrecord[$key];
						if ($newrecordchecker != NULL)
						{
							$update_sql = "UPDATE spe_templatedetail SET Question = '$questions[$key]', InputType = '$input_type[$key]', Section = '$section_type[$key]' 
											WHERE DetailID = $newrecordchecker";
							if($connection->query($update_sql) === TRUE)
							{
								//echo "Record updated successfully";
								//header("location: edit_question.php");
							}
							else
							{
								//echo "Error.";
								//header("location: edit_question.php");
							}
						}
						else
						{
							$save_sql = "INSERT INTO spe_templatedetail(Question,InputType,Section,SPETemplateID) 
										VALUES('".validate_input($connection->real_escape_string($questions[$key]))."', 
										'".validate_input($connection->real_escape_string($input_type[$key]))."', 
										'".validate_input($connection->real_escape_string($section_type[$key]))."',
										'".validate_input($connection->real_escape_string($templateid))."')";	
							
							if($connection->query($save_sql) === TRUE)
							{
								//echo "New record created successfully";
								//header("location: edit_question.php");
							}
							else
							{
								//echo "Error.";
								//header("location: edit_question.php");
							}
						}
					} //End foreach loop
				}
			}//End else: Not new record
			
		}//End if isset submit
	?>
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
			<span ><img onclick="window.location.href='../login/logout.php'" style="cursor:pointer; width:40px;height:40px;" src="css/spe_profile_icon.png"><span>
			</div>
		</div>
		
		<?php $link = "create_spe.php?unit=".$unit_id;?>
		<form method="post" id="createForm" action="<?php echo $link ?>">
			<header>
				<h2 class="title">Create Self Peer Evaluation Form Template</h2>
			</header>
			
			<div id='sec'>
				<div class="unit_name" id="unit_name">
					<?php
						//get unit code and unit name
						$unit_name_sql = "SELECT * FROM unit WHERE UnitCode='$unit_id'";
						$unit_result = $connection->query($unit_name_sql);
						$unit_info = $unit_result->fetch_assoc();
						$ucode = $unit_info["UnitCode"];
						echo "<span id='".$unit_info["UnitCode"]."'>".$unit_info["UnitCode"]." ".$unit_info["UnitName"]."</span>";
					?>
					
					<select class="spe_type" name="spe_type" id="spe_selector">
						<option value="none" class="option" selected disabled>SPE Form Template</option>
						<?php
							//get number of spe from database
								$spe_no_sql = "SELECT * FROM spe_template WHERE UCID='$ucid'";
								$spe_no_result = $connection->query($spe_no_sql);
								if($spe_no_result-> num_rows > 0)
								{
									$num = 1;
									while($spe_no = $spe_no_result->fetch_assoc())
									{
										echo "<option value='".$spe_no['SPETemplateID']."' class='option' id='".$spe_no['SPETemplateID']."'> Template". $num."</option>";
										$num++;
										
									}
								}
								else
								{
									//echo "No result.";
								}
						?>
						<option value="new_spe" class="option">-New SPE Template-</option>
						</select>
						<button type="button" name="save_spe_button" class="save_spe_button cBtnColor" id="save_spe_button">Save To New SPE</button>
						<button type="button" name="delete_spe_button" class="delete_spe_button cBtnColor" id="delete_spe_button">Delete SPE Template</button>
				</div>
				
				<div class="container" id="container">
						<!--after clicking add question, the list of questions will go here-->
						<!--<input type='hidden' name='nb_elements'>-->
				</div>

			</div>
			
			
			<?php if(isset($checkrecord1)) echo "<font id='try6' color='red'>".$checkrecord1."</font>";?> 
		</form>
		<?php
			echo $output;
		?>
		<?php
			$connection->close();
		?>
		
	</div>
</div>


<script type="text/javascript">
		var ucidfromphp = '<?php echo $ucid?>';
		var ucodefromphp = '<?php echo $unit_id ?>';
</script>
</body>
</html>