<?php
	include "connection.php";
	//include "../phpspreadsheet/vendor/autoload.php";
	
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
	//Select spe that is due
	$sql_selectduespe = "SELECT * from spe WHERE DueDate > NOW() AND Visibility = TRUE";
	$request_duespe= $connection->query($sql_selectduespe);
	echo "<div id='main'>";
	if ($request_duespe-> num_rows>0){
		while ($row_duespe= $request_duespe->fetch_assoc()){ //Each record that is due
			//Get unicdoe, ucid and speid for future use
			$unitcode = $row_duespe['UnitCode'];
			$ucid = $row_duespe['UCID'];
			$speid = $row_duespe['SPEID'];
			
			$sql_selectgroup = "SELECT TeamId from studentlistdetail WHERE UnitCode = '$unitcode' AND TeachPeriod = '$period' GROUP BY TeamId";
			$request_group= $connection->query($sql_selectgroup);
			if ($request_group-> num_rows>0){
				while ($row_group= $request_group->fetch_assoc()){
					//echo "Student: ".$row['StudentId']. "&nbsp Team: ". $row['TeamId']."<br>";
					$teamid = $row_group['TeamId'];
					//echo "Team: ". $teamid."<br>";
					
					//Get student taking that module (act as record on student being assessed)
					//$teamid = 'FT07';
					echo "<h2>".$teamid."</h2>";
					$sql_selectname = "SELECT * from studentlistdetail WHERE UnitCode = '$unitcode' AND TeamId = '$teamid' AND TeachPeriod = '$period'";
					$request_name= $connection->query($sql_selectname);
					if ($request_name-> num_rows>0)
					{
						$counter = 1;
						while ($row_name= $request_name->fetch_assoc())
						{ //Each student in a team that taking that module in particular period
							$receiverstuid = $row_name['StudentId'];
							$surname = $row_name['Surname'];
							$givenname = $row_name['GivenName'];
							$title = $row_name['Title'];
							echo "<div class='left'>";
							echo $counter.") Receiver ".$receiverstuid." ".$surname." ".$givenname."<br>";
							$counter++;
							//echo "The score giver are:";
							 
							//Check number of question that need to give score + show as the heading under score receiver name
							$sql_checknumberofquestion = "SELECT * FROM spe_question WHERE SPEID = '$speid' AND InputType = '5-Likert'";
							$request_checknumberofquestion= $connection->query($sql_checknumberofquestion);
							$numberofquestion = mysqli_num_rows($request_checknumberofquestion);
							
							//Get all member to put at the left side, showing score giver
							$sql_selectmember = "SELECT * from studentlistdetail WHERE UnitCode = '$unitcode' AND TeamId = '$teamid' AND TeachPeriod = '$period'";
							$request_member= $connection->query($sql_selectmember);
							if ($request_member-> num_rows>0)
							{
								$totalavg = 0;
								$divideby = 0;
								$finalscore = 0;
								while ($row_member= $request_member->fetch_assoc())//this will be each student in the group
								{ 
									$scoregiverid = $row_member['StudentId'];
									$scoregiversurname =$row_member['Surname'];
									$scoregivergivenname = $row_member['GivenName'];
									$scoregivertitle = $row_member['Title'];
									
									//echo $scoregiverid."|";
									
									//Get question number and score that gave by all member
									$sql_selectscore = "SELECT spe_result.Score AS Score from spe_completionstatus LEFT JOIN spe_result
														ON spe_completionstatus.CompletionID = spe_result.CompletionID
														WHERE spe_completionstatus.StudentID = '$scoregiverid' AND spe_result.ReceiverStudentID = '$receiverstuid' AND spe_result.Score <> 0";
									$request_score= $connection->query($sql_selectscore);
									if ($request_score-> num_rows>0)
									{ //if got record: means got fill in spe, then show score, if no show 0
										$total = 0;
										$count = 0;
										while ($row_score= $request_score->fetch_assoc()){ //each row, the score that gave to receiver student for particular question
											echo $row_score['Score']." ||||| ";
											$total = $total + $row_score['Score'];
											$count++;
										}
										$avgscore = $total/$count;
										echo "Average: ".$avgscore;
										echo "<br>";
										
										//echo mysqli_num_rows($request_score);
										//echo "current score giver: ".$scoregiverid;
									}
									else
									{
										for($i=1;$i<=$numberofquestion;$i++)
										{
											echo "0 |||||" ;
										}
										$avgscore = 0;
										echo "Average: ".$avgscore;
										echo "<br>";
									}
									$totalavg = $totalavg + $avgscore;
									if($avgscore !=0)
									{
										$divideby++;
									}
									else
									{
										$divideby = $divideby;
									}
									
								}
							}
							if ($totalavg == 0)
							{
								$finalscore = 0;
							}
							else
							{
								$finalscore = $totalavg/$divideby;
							}
							echo "<span style='color:red;'>The final score for this student is ".$finalscore."</span>";
							echo "<br>";
							echo "</div>";
						}
					}
				}
			}
			
		}
	}
	echo "</div>";
?>