<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    <title>Edit Questions (UC)</title>
    <link href="css/edit_question.css" rel="stylesheet" type="text/css">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="js/edit_question.js"></script>
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
		if (isset($_GET['unit']))
		{
			$unit_id = $_GET['unit'];
		}
		else
		{
			header("Location:../login/noright.php");
		}
		
		$unit_id = $_GET['unit'];
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
			if(isset($_POST['spe_type']))
			{
				$speid = $_POST['spe_type'];
			}
			else
			{
				$speid = "";
			}
			
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
			
			if(isset($_POST['duedate_text']))
			{
				$duedate = $_POST['duedate_text'];
			}
			else
			{
				$duedate = "";
			}
			
			if(isset($_POST['checkrecord']))
			{
				//$section_type = array($_POST['section_type']);
				$checkrecord = $_POST['checkrecord'];
			}
			else
			{
				$checkrecord = "";
			}
			
			if(isset($_POST['duedate_text']))
			{
				$update_duedate = "UPDATE spe SET duedate = '$duedate' WHERE speid = '$speid' AND unitcode='$unit_id'";
				//$number = $number + 1;
				$update_result = $connection->query($update_duedate);
			}
			
			if($checkrecord != "") //If got add question
			{
				foreach($questions AS $key => $value)
				{
					$newrecordchecker = $checkrecord[$key];
					if ($newrecordchecker != NULL)
					{
						$update_sql = "UPDATE spe_question SET Question = '$questions[$key]', InputType = '$input_type[$key]', Section = '$section_type[$key]' WHERE QuestionNo = $checkrecord[$key]";
						if($connection->query($update_sql) === TRUE)
						{
							//echo "Record updated successfully";
							//header("location: edit_question.php");
						}
						else
						{
							//echo "Error.";
							echo $connection->error;
							//header("location: edit_question.php");
						}
					}
					else
					{
						$save_sql = "INSERT INTO spe_question(SPEID,Question,InputType,Section) 
										VALUES('".validate_input($connection->real_escape_string($speid))."', 
										'".validate_input($connection->real_escape_string($questions[$key]))."', 
										'".validate_input($connection->real_escape_string($input_type[$key]))."',
										'".validate_input($connection->real_escape_string($section_type[$key]))."')";	
							
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
			}
			
		}
		
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
			
			<?php $link = "edit_question.php?unit=".$unit_id;?>
			<form method="post" action="<?php echo $link;?>">
				<header>
					<h2 class="title">Add/Edit Questions</h2> 
				</header>
					<div id='sec'>
						
						<div class="unit_name">
						<?php
							//get unit code and unit name
							$unit_name_sql = "SELECT * FROM unit WHERE UnitCode='$unit_id'";
							$unit_result = $connection->query($unit_name_sql);
							$unit_info = $unit_result->fetch_assoc();
							echo $unit_info["UnitCode"]." ".$unit_info["UnitName"];
						?>
								
							<select class="spe_type" name="spe_type" id="spe_selector">
								<option value="none" class="option" selected disabled>SPE Form</option>
								<option value="new_spe" class="option" id="new_spe" disabled hidden>-New SPE-</option>
								<?php
									//get number of spe from database
									$spe_no_sql = "SELECT speid FROM spe WHERE UCID='$ucid' AND UnitCode='$unit_id' AND Visibility = true";
									$spe_no_result = $connection->query($spe_no_sql);
									if($spe_no_result-> num_rows > 0)
									{
										$num = 1;
										while($spe_no = $spe_no_result->fetch_assoc())
										{
											echo "<option value='".$spe_no['speid']."' class='option'> SPE". $num."</option>";
											$num++;
											
										}
									}
									/*else
									{
										echo "No result.";
									}*/
								?>
							</select>
						</div>

						<div class="container" id="container">
							<!--after clicking add question, the list of questions will go here-->
							<!--<input type='hidden' name='nb_elements'>-->
						</div>
					</div>
				
				<footer>
					<div id="buttons">
						<!--<div class="col-xs-12 col-md-4 col-lg-4 col-xl-4">-->
							<input type="submit" name="submit" class="save_button" id="save_button" value="Save">
						<!--</div>>-->

						<!--<div class="col-xs-12 col-md-4 col-lg-4 col-xl-4">>-->
							<button type="reset" class="reset_button" id="reset_button" name="reset">Reset</button>
						<!--</div>>-->
						
						<!--<div class="col-xs-12 col-md-4 col-lg-4 col-xl-4">>-->
							<button type="button" name="addQ" class="add_question_button" id="add_question_button">Add Question</button>
						<!--</div>-->
					</div>
					<?php if(isset($checkrecord1)) echo "<font id='try6' color='red'>".$checkrecord1."</font>";?> 
				</footer>
			</form>
			<?php
				//echo $output;
				
			?>
		
			</div>
		</div>
	
	<script type="text/javascript">
		var ucodefromphp = '<?php echo $_GET['unit']; ?>';
</script>
	<?php
		$connection->close();
	?>
</body>
</html>