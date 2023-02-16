<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    <title>Evaluation</title>
    <link href="css/evaluation.css" rel="stylesheet" type="text/css">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
	<!-- font-awesome-icons -->
	<link rel="stylesheet" href="css/font-awesome.min.css" />
	<!-- //font-awesome-icons -->

	<!-- google fonts -->
	<link href="//fonts.googleapis.com/css?family=Muli:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;subset=latin-ext,vietnamese" rel="stylesheet">
	<link href="//fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&amp;subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese" rel="stylesheet">
	<!-- //google fonts -->
</head>
<body>
<script src="js/evaluation.js"></script>
	<?php
		include 'connection.php';
		session_start();
		if(isset($_SESSION['login']))
		{
			if(isset($_SESSION['ucid']))
			{
				header("Location:../login/noright.php");
			}
			else if(isset($_SESSION['studentid']))
			{
				//header("Location:../student_dashboard/student_dashboard.php");
				//$student_id = $_SESSION["studentid"];
				$student_id = $_SESSION['studentid'];
			}
		}
		else
		{
			header("location:../login/logintoaccess.php");
		}
		
		/*if(isset($_SESSION['speid']))
		{
			$speid = $_SESSION['speid'];
		}
		else
		{
			$speid = $_GET['spe'];
			$_SESSION['speid'] = $_GET['spe'];
		}*/
		if(isset($_GET['spe']))
		{
			$speid = $_GET['spe'];
		}
		/*if(isset($_SESSION['unit']))
		{
			$unit_code = $_SESSION['unit'];
		}
		else
		{
			$unit_code = $_GET['unit'];
			$_SESSION['unit'] = $_GET['unit'];
		}*/
		if (isset($_GET['unit']))
		{
			$unit_code = $_GET['unit'];
		}
		else
		{
			header("Location:../login/noright.php");
		}
		
		//This one to check go back and come between record
		if(isset($_POST['currentrecord']))
		{
			$currentrecord = $_POST['currentrecord'] + 1;
		}
		else
		{
			$currentrecord = 0;
		}
		
		//Array that store records to be uploaded (Get from session)
		if (isset($_SESSION["arrayOfRecord"]))
		{
			$arr_FinalRecordArray = $_SESSION["arrayOfRecord"];
		}
		else
		{
			$arr_FinalRecordArray = array();
		}
		
		//If go back button clicked
		if (isset($_POST['back_button']))
		{
			$currentrecord = $_POST['back_button_text']-1;
			$backbuttonpressed = $currentrecord;
			$_SESSION["RecordCounter"] = $currentrecord;
		}
		
		//On refresh, load the current student's SPE page
		$offset_counter = 0;
		$sql_condition = "AND StudentID = $student_id";
		
		//Get total count of student in a group
		if(isset($_SESSION["totalmembercount"]))
		{
			$totalcount = $_SESSION["totalmembercount"]-1;
			$totalmember = $_SESSION["totalmembercount"] - 1;
		}
		else
		{
			$totalmember = 0;
			$totalcount = 2;
		}
		//ensure that no input can run any malicious code
		/*function validate_input($input)
		{
			//strip unneccessary charaters from user input
			$input = trim($input);
			//remove backlashes from user input
			$input = stripslashes($input);
			//remove any html special charaters
			$input = htmlspecialchars($input);
			
			return $input;
		}*/
		
		//Store data to database
		if (isset($_POST['submit']))
		{	
			//Check record counter, if isset the sql is to limit record to other student in group
			if (isset($_SESSION["RecordCounter"]))
			{					
				if (($_SESSION["RecordCounter"]) != 0)
				{					
					$offset_counter = $_SESSION["RecordCounter"];
					$newcounter = $offset_counter-1;
					//If not the last record
					if ($offset_counter != 100)
					{
						$offset = "offset ".$_SESSION["RecordCounter"];
						$sql_condition = "AND NOT StudentID = $student_id LIMIT 1 offset $newcounter";
					}
				}
				else
				{
					$offset_counter = 0;
					$sql_condition = "AND StudentID = $student_id";
				}
			}
			else //If not set, then get record for current logged in student
			{
				$offset_counter = 0;
				$sql_condition = "AND StudentID = $student_id";
			}	
			
			//Retrieve results
			if (isset($_POST['result']))
			{
				$eachrecord = $_POST['result'];
			}			
			$receiverid = $_POST['ReceiverStuID'];
			$question_no = $_POST['questionno'];
			
			//Set starting counter (for data to refresh)
			$startcounter =0 ;
			if (isset($_SESSION["arrayOfRecord"]))
			{
				if (!empty($_SESSION["arrayOfRecord"]))
				{
					$arraySetNew = array();
					for ($u=0;$u<count($_SESSION["arrayOfRecord"]);$u++)
					{
						//if (!($arr_FinalRecordArray[$u][0] == $_SESSION["currentpageid"]))
						if (!($arr_FinalRecordArray[$u][0] == $receiverid))
						{
							$startcounter++;
						}
						else
						{
							array_push($arraySetNew,$arr_FinalRecordArray[$u]);
							break;
						}
					}
				}
			}
			
			//Put into array
			foreach($eachrecord AS $key => $value)
			{
				//If the record match in the one in array, replace, or create new record
				if(!empty($arraySetNew))
				{
					$arr_FinalRecordArray[$startcounter] = array();
					array_push($arr_FinalRecordArray[$startcounter],$receiverid,$question_no[$key],$eachrecord[$key]);
				}
				else
				{					
					$arr_singleRecord = array();
					array_push($arr_singleRecord,$receiverid,$question_no[$key],$eachrecord[$key]);
					array_push($arr_FinalRecordArray,$arr_singleRecord);
				}
				$startcounter++;
			}
			$_SESSION['arrayOfRecord'] = $arr_FinalRecordArray;
			
			//If it is final record, then save
			if ($offset_counter == 100)
			{
				//Insert data
				$sql_insertcompletion = "INSERT INTO spe_completionstatus (SPEID,StudentID) VALUES ('$speid','$student_id')";
				if($connection->query($sql_insertcompletion) === TRUE){
					$completionid = $connection->insert_id;
					//echo "<script>alert('".count($arr_FinalRecordArray)."')</script>";
					for ($i=0;$i<count($arr_FinalRecordArray);$i++)
					{
						$rid = $arr_FinalRecordArray[$i][0];
						$qno = $arr_FinalRecordArray[$i][1];
						$value = $arr_FinalRecordArray[$i][2];
						//If the value is number, means it is score
						if(is_numeric($value))
						{
							$sql_insertresult = "INSERT INTO spe_result (CompletionID,ReceiverStudentID,QuestionNo,Score,Comment) VALUES ('$completionid','$rid','$qno','$value','N/A')";
						}
						//Else it is comments
						else
						{
							$sql_insertresult = "INSERT INTO spe_result (CompletionID,ReceiverStudentID,QuestionNo,Score,Comment) VALUES ('$completionid','$rid','$qno','0','$value')";
						}
						if($connection->query($sql_insertresult) === TRUE){
							//echo "Success";
						}
						else{
							//echo "Error: ".$connection->error;
						}
					}
					//Redirect to success page
					header("Location:spesubmitted.php");
				}//End if for inserting record to spe_completionstatus
				else{
					//echo "Cannot insert into completion table";
				}
			}//End if for checking whether is final record
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
					//get student name from the database
					$select_sql = "SELECT * FROM student WHERE StudentID = '$student_id'";
					$result = $connection->query($select_sql);
					$student = $result->fetch_assoc();
					echo "<label class='profile_name_label'>".$student["Name"]."</label>";
				?>
				<span ><img onclick="window.location.href='../login/logout.php'" style="cursor:pointer; width:40px;height:40px;" src="images/spe_profile_icon.png"><span>
			</div>
		</div>
		<?php $link = "evaluation.php?spe=".$speid."&unit=".$unit_code;?>
		<form method="post" action="<?php echo $link ?>">
			<section class="spe_eval" id="spe_eval">
				<div class="self_eval">
					<!--Peer evaluation questions will go here-->
					<?php
						echo "<input type='hidden' id='iCurrentRecord' name='currentrecord' value='".$currentrecord."'>";
						//assuming
						$student_name_sql = "SELECT * FROM studentlistdetail WHERE StudentID = '$student_id'"; //AND teach period = current, unit = this module unit
						$result = $connection->query($student_name_sql);
						if ($result->num_rows > 0){
							while ($student = $result->fetch_assoc())
							{
								$team_id = $student['TeamId'];
								$teach_period = $student['TeachPeriod'];
							}
						}
						//print peer evaluation questions
						echo "<div class='peer_eval'>";
						if ($offset_counter != 0)
						{
							echo "<h2 class='title'>Peer Evaluation</h2>";
						}
						else
						{
							echo "<h2 class='title'>Self Evaluation</h2>";
						}
						//echo($student_name);
						
						//Get number of teammates in certain group
						$checkmember_sql = "SELECT * FROM studentlistdetail 
										WHERE TeamID = '$team_id'					
										AND TeachPeriod = '$teach_period' 
										AND UnitCode = '$unit_code'";
						$checkmember_result = $connection->query($checkmember_sql);
						if ($checkmember_result->num_rows > 0)
						{
							$totalmembercount = mysqli_num_rows($checkmember_result);
							$_SESSION["totalmembercount"] = $totalmembercount;
						}
										
						//select record to show
						$member_sql = "SELECT * FROM studentlistdetail 
										WHERE TeamID = '$team_id'					
										AND TeachPeriod = '$teach_period' 
										AND UnitCode = '$unit_code'
										$sql_condition";
										
						$member_result = $connection->query($member_sql);
						//$num_of_members = $member_result->num_rows;
						
						$question_no = 1;
						if ($member_result->num_rows > 0)
						{	
							if ($totalmembercount == ($offset_counter + 1))
							{
								//unset($_SESSION["RecordCounter"]);
								$offset_counter = 100;		
								$_SESSION["RecordCounter"] = $offset_counter;
							}
							else
							{
								$offset_counter = $offset_counter + 1;		
								$_SESSION["RecordCounter"] = $offset_counter;
							}				
							
							//Display data
							while($student1 = $member_result->fetch_assoc())
							{
								$_SESSION["currentpageid"] = $student1['StudentId'];
								echo "<div class='name_label'>";
								echo "<label class='label' style='color:black;'>Name:";
								echo "<label class='member_name_label' name='member_name_label'>".$student1['GivenName']." ".$student1['Surname']."</label></label></div>";
								echo "<input type='hidden' name='ReceiverStuID' value='".$student1['StudentId']."'>";
								//get questions with the section type = Peer, All
								if ($currentrecord == 0)
								{
									$peer_question_sql = "SELECT * FROM spe_question WHERE SPEID = $speid AND Section IN ('Individual', 'All')";
								}
								else
								{
									$peer_question_sql = "SELECT * FROM spe_question WHERE SPEID = $speid AND Section IN ('Peer', 'All')";
								}
								$peer_qn_sql_result = $connection->query($peer_question_sql);
								if($peer_qn_sql_result == TRUE)
								{
									if($peer_qn_sql_result->num_rows > 0)
									{
										$c = 0;
										if (isset($_SESSION["arrayOfRecord"]))
										{
											$arraySet = array();
											//echo "<script>alert('the number of record".count($_SESSION["arrayOfRecord"])."')</script>";
											//unset($arraySet);
											for ($q=0;$q<count($_SESSION["arrayOfRecord"]);$q++)
											{
												
												if ($arr_FinalRecordArray[$q][0] == $_SESSION["currentpageid"])
												{
													array_push($arraySet,$arr_FinalRecordArray[$q]);
												}
											}
											
											if(empty($arraySet))
											{
												unset($arraySet);
											}
										}
										
										while($question = $peer_qn_sql_result->fetch_assoc())
										{
											echo "<p class='question'> $question_no)	".$question['Question'];
											echo "<input type='hidden' name='questionno[]' value='".$question['QuestionNo']."'>";
											$question_no++;
											if($question['InputType'] == "5-Likert")
											{
												echo "<select class='likert_answer' id='".$question['QuestionNo']."' name='result[]' required='required'>";
												//if (isset($_SESSION["arrayOfRecord"]))
												if (!empty($arraySet))
												{
													if (($arraySet[$c][2]) == 1)
													{
														echo "<option value='1' class='option' selected>1</option>";
														echo "<option value='2' class='option'>2</option>";
														echo "<option value='3' class='option'>3</option>";
														echo "<option value='4' class='option'>4</option>";
														echo "<option value='5' class='option'>5</option>";
													}
													else if (($arraySet[$c][2]) == 2)
													{
														echo "<option value='1' class='option'>1</option>";
														echo "<option value='2' class='option' selected>2</option>";
														echo "<option value='3' class='option'>3</option>";
														echo "<option value='4' class='option'>4</option>";
														echo "<option value='5' class='option'>5</option>";
													}
													else if (($arraySet[$c][2]) == 3)
													{
														echo "<option value='1' class='option'>1</option>";
														echo "<option value='2' class='option'>2</option>";
														echo "<option value='3' class='option' selected>3</option>";
														echo "<option value='4' class='option'>4</option>";
														echo "<option value='5' class='option'>5</option>";
													}
													else if (($arraySet[$c][2]) == 4)
													{
														echo "<option value='1' class='option'>1</option>";
														echo "<option value='2' class='option'>2</option>";
														echo "<option value='3' class='option'>3</option>";
														echo "<option value='4' class='option' selected>4</option>";
														echo "<option value='5' class='option'>5</option>";
													}
													else if (($arraySet[$c][2]) == 5)
													{
														echo "<option value='1' class='option'>1</option>";
														echo "<option value='2' class='option'>2</option>";
														echo "<option value='3' class='option'>3</option>";
														echo "<option value='4' class='option'>4</option>";
														echo "<option value='5' class='option' selected>5</option>";
													}
												}
												else
												{
													$rating = 1;
													//echo "<select class='likert_answer' id='".$question['question_no']."' name='result[]' required='required'>";
													echo "<option value='' class='option' selected disabled>5-Likert</option>";
													while($rating <=5)
													{
														echo "<option value='".$rating."' class='option'>".$rating."</option>";
														$rating++;
													}
												}
											}
											else if($question['InputType'] == "Comments")
											{
												if(!empty($arraySet))
												{
													echo "<br><textarea id='".$question['QuestionNo']."' class='comments' name='result[]' placeholder='Enter your comment here' required/>".$arraySet[$c][2]."</textarea>";
												}
												else
												{
												echo "<br><textarea id='".$question['QuestionNo']."' class='comments' name='result[]' placeholder='Enter your comment here' required/></textarea>";
												}
											}
											echo "</select></p>";
											$c++;
										}
										
									}
									$question_no = 1;
								}
								else
								{
									echo "No questions";
								}
							}
						}
					?>
				</div>
				
				<!--Go back and save button-->
				<?php 
				//echo "<script>alert(".$currentrecord.")</script>";
				if ($currentrecord ==  $totalcount)
				{
					
					echo "<input type='submit' name='submit' class='save_button' id='save_button' value='Submit'>";
				}
				else
				{
					echo "<input type='submit' name='submit' class='save_button' id='save_button' value='Next'>";	
				}
				
				if ($currentrecord != 0)
				{
					echo "<input type='submit' value='Go Back' name='back_button' class='back_button' id='back_button'>";
					echo "<input type='hidden' value='".$currentrecord."' name='back_button_text'>";
				}
				?>
			</section>
			<?php
				$connection->close();
			?>
		</form>
		
	</div>
</div>
		
	
</body>
</html>