<?php
	include "connection.php";
	include "../phpspreadsheet/vendor/autoload.php";
	
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	
	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$column = 2;
	$row = 1;
	$teamcol = 1;
	$newrow = 1;
	$recordstartingcol = 1;
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
	$today = date("Y-m-d") ;
	$sql_selectduespe = "SELECT * from spe WHERE DueDate = '$today' AND Visibility = TRUE";
	$request_duespe= $connection->query($sql_selectduespe);
	if ($request_duespe-> num_rows>0){
		while ($row_duespe= $request_duespe->fetch_assoc()){ //Each record that is due
			//Get unicode, ucid and speid for future use
			$unitcode = $row_duespe['UnitCode'];
			$ucid = $row_duespe['UCID'];
			$speid = $row_duespe['SPEID'];
			
			$sql_selectgroup = "SELECT TeamId from studentlistdetail WHERE UnitCode = '$unitcode' AND TeachPeriod = '$period' GROUP BY TeamId";
			$request_group= $connection->query($sql_selectgroup);
			if ($request_group-> num_rows>0){
				while ($row_group= $request_group->fetch_assoc()){//Starthere for each group record
					$teamid = $row_group['TeamId'];
					
					$row = $newrow;//New record starting row
					
					$sheet->setCellValueByColumnAndRow($column, $row, 'Student being assesed');
					/*Set bold and align center*/
					$studentbeingassesedcell =$sheet->getCellByColumnAndRow($column, $row)->getCoordinate(); //Get the cell value of starting of receiver name column
					$sheet->getStyle($studentbeingassesedcell)->getFont()->setBold(true);
					/*End styling*/
					$scorereceivernamerow = $row;
					$row++;
					$sheet->setCellValueByColumnAndRow($column, $row, 'Assesment Criteria');
					/*Set bold and align center*/
					$criteriacell =$sheet->getCellByColumnAndRow($column, $row)->getCoordinate(); //Get the cell value of starting of receiver name column
					$sheet->getStyle($criteriacell)->getFont()->setBold(true);
					/*End styling*/
					$assesmentcriteriarow = $row;
					$row++;
					$scorereceivercol = $column + 4; //Put the first score receiver name 4 column after Student Being Assesed
					$teamidrow = $row +1; //Put the header named Team No, Student ID etc two row after Assesment Criteria
					
					$teamcol = 1; //Reset column to 1
					$initialteamcol =$teamcol;
					$sheet->setCellValueByColumnAndRow($teamcol, $teamidrow, 'Team No');
					
					$teamcol++;
					$sheet->setCellValueByColumnAndRow($teamcol, $teamidrow, 'Student ID');
					$teamcol++;
					$sheet->setCellValueByColumnAndRow($teamcol, $teamidrow, 'Surname');
					$teamcol++;
					$sheet->setCellValueByColumnAndRow($teamcol, $teamidrow, 'Title');
					$teamcol++;
					$sheet->setCellValueByColumnAndRow($teamcol, $teamidrow, 'Given Name');
					/*Set column auto resize*/
					$sheet->getColumnDimension('E')->setAutoSize(true);
					/*End setting column size*/
					/*Set bold and align center*/
					for ($i=$initialteamcol;$i<=$teamcol;$i++)
					{
						$teamno =$sheet->getCellByColumnAndRow($i, $teamidrow)->getCoordinate(); //Get the cell value of starting of receiver name column
						$sheet->getStyle($teamno)->getAlignment()->setHorizontal('center');
						$sheet->getStyle($teamno)->getFont()->setBold(true);
					}
					/*End styling*/
					$scoregiverstartingrow = $teamidrow + 1;//Starting row of score giver
					//Get student taking that module (act as record on student being assessed)
					$sql_selectname = "SELECT * from studentlistdetail WHERE UnitCode = '$unitcode' AND TeamId = '$teamid' AND TeachPeriod = '$period'";
					$request_name= $connection->query($sql_selectname);
					if ($request_name-> num_rows>0)
					{
						$counter = 1;
						$currentrow = $scoregiverstartingrow;
						while ($row_name= $request_name->fetch_assoc())//Each student in a team that taking that module in particular period
						{ 
							$scorestartingrow =$teamidrow + 1; //here!!! first line of score giver, each receiver record start over
							$receiverstuid = $row_name['StudentId'];
							$surname = $row_name['Surname'];
							$givenname = $row_name['GivenName'];
							$title = $row_name['Title'];
							$wholename = $surname." ".$givenname." ".$receiverstuid;
							
							$sheet->setCellValueByColumnAndRow($scorereceivercol, $scorereceivernamerow, $wholename);
							$receiverstartingcol = $scorereceivercol;
							$startingcellofreceiver =$sheet->getCellByColumnAndRow($receiverstartingcol, $scorereceivernamerow)->getCoordinate(); //Get the cell value of starting of receiver name column
							$sheet->getStyle($startingcellofreceiver)->getAlignment()->setHorizontal('center');
							$sheet->getStyle($startingcellofreceiver)->getFont()->setBold(true);
							$checkthiscol = $scorereceivercol;
							echo $counter.") Receiver ".$receiverstuid." ".$surname." ".$givenname."<br>";
							$counter++;
							 
							//Check number of question that need to give score + show as the heading under score receiver name
							$sql_checknumberofquestion = "SELECT * FROM spe_question WHERE SPEID = '$speid' AND InputType = '5-Likert'";
							$request_checknumberofquestion= $connection->query($sql_checknumberofquestion);
							$numberofquestion = mysqli_num_rows($request_checknumberofquestion);
							$endingcellofreceiver =$sheet->getCellByColumnAndRow($receiverstartingcol+$numberofquestion, $scorereceivernamerow)->getCoordinate();
							$sheet->mergeCells($startingcellofreceiver.":".$endingcellofreceiver);
							$scorestartingcol = $scorereceivercol; //starting column of score
							for($j=1;$j<=$numberofquestion;$j++)
							{
								$sheet->setCellValueByColumnAndRow($scorereceivercol, $assesmentcriteriarow, $j);
								/*Set bold and align center*/
								$questionocell =$sheet->getCellByColumnAndRow($scorereceivercol, $assesmentcriteriarow)->getCoordinate(); //Get the cell value of starting of receiver name column
								$sheet->getStyle($questionocell)->getAlignment()->setHorizontal('center');
								$sheet->getStyle($questionocell)->getFont()->setBold(true);
								/*End styling*/
								$scorereceivercol++;
							}
							$averagefrom = $assesmentcriteriarow;
							$finalscorecol = $scorereceivercol;
							$sheet->setCellValueByColumnAndRow($scorereceivercol, $averagefrom, 'Average');
							/*Set bold and align center*/
							$averagecell =$sheet->getCellByColumnAndRow($scorereceivercol, $averagefrom)->getCoordinate(); //Get the cell value of starting of receiver name column
							$sheet->getStyle($averagecell)->getAlignment()->setHorizontal('center');
							$sheet->getStyle($averagecell)->getFont()->setBold(true);
							/*End styling*/
							$averagefrom++;
							$sheet->setCellValueByColumnAndRow($scorereceivercol, $averagefrom, 'from each');
							/*Set bold and align center*/
							$fromeachcell =$sheet->getCellByColumnAndRow($scorereceivercol, $averagefrom)->getCoordinate(); //Get the cell value of starting of receiver name column
							$sheet->getStyle($fromeachcell)->getAlignment()->setHorizontal('center');
							$sheet->getStyle($fromeachcell)->getFont()->setBold(true);
							/*End styling*/
							
							//Add detail of each score giver
							$scoreceiverstartingcol = 1;
							$finalscorerow = $scoreceiverstartingcol;
							$averagetitlecol = 1;
							$sheet->setCellValueByColumnAndRow($scoreceiverstartingcol, $scoregiverstartingrow, $teamid);
							$scoreceiverstartingcol++;
							$sheet->setCellValueByColumnAndRow($scoreceiverstartingcol, $scoregiverstartingrow, $receiverstuid);
							$scoreceiverstartingcol++;
							$sheet->setCellValueByColumnAndRow($scoreceiverstartingcol, $scoregiverstartingrow, $surname);
							$scoreceiverstartingcol++;
							$sheet->setCellValueByColumnAndRow($scoreceiverstartingcol, $scoregiverstartingrow, $title);
							$scoreceiverstartingcol++;
							$sheet->setCellValueByColumnAndRow($scoreceiverstartingcol, $scoregiverstartingrow, $givenname);
							
							$scoregiverstartingrow++;//Add new row to add new score giver detail
							//Get all member to put at the left side, showing score giver
							$sql_selectmember = "SELECT * from studentlistdetail WHERE UnitCode = '$unitcode' AND TeamId = '$teamid' AND TeachPeriod = '$period'";
							$request_member= $connection->query($sql_selectmember);
							if ($request_member-> num_rows>0)
							{
								$totalavg = 0;
								$divideby = 0;
								$finalscore = 0;
								$newscoregiverstartingrow = 0;
								$totalscoregiver = mysqli_num_rows($request_member);
								$startingcalculate = $scorestartingrow;
								while ($row_member= $request_member->fetch_assoc())//this will be each student in the group
								{
									$scorestartingrow = $scorestartingrow;
									$scoregiverid = $row_member['StudentId'];
									$scoregiversurname =$row_member['Surname'];
									$scoregivergivenname = $row_member['GivenName'];
									$scoregivertitle = $row_member['Title'];
									
									//Get question number and score that gave by all member
									$sql_selectscore = "SELECT spe_result.Score AS Score from spe_completionstatus LEFT JOIN spe_result
														ON spe_completionstatus.CompletionID = spe_result.CompletionID
														WHERE spe_completionstatus.StudentID = '$scoregiverid' AND spe_result.ReceiverStudentID = '$receiverstuid' AND spe_result.Score <> 0";
									$request_score= $connection->query($sql_selectscore);
									$newcolumn = $scorestartingcol;
									$currentcolumn = $newcolumn;
									$avgnewcolumn = $scorestartingcol;
									if ($request_score-> num_rows>0) //if got record: means got fill in spe, then show score, if no show 0
									{
										$total = 0;
										$count = 0;
										$firstCellAddress = $sheet->getCellByColumnAndRow($newcolumn, $scorestartingrow)->getCoordinate();
										while ($row_score= $request_score->fetch_assoc()){ //each row, the score that gave to receiver student for particular question
											$sheet->setCellValueByColumnAndRow($newcolumn, $scorestartingrow, $row_score['Score']);
											$newcolumn++;
											echo $row_score['Score']." ||||| ";
											$total = $total + $row_score['Score'];
											$count++;
										}
										$avgscore = $total/$count;
										$lastCellAddress = $sheet->getCellByColumnAndRow($newcolumn-1, $scorestartingrow)->getCoordinate();
										$avgformula = "=SUM(".$firstCellAddress.":".$lastCellAddress.")/COUNT(".$firstCellAddress.":".$lastCellAddress.")";
										$sheet->setCellValueByColumnAndRow($newcolumn, $scorestartingrow, $avgformula);
										echo "Average: ".$avgscore;
										echo "<br>";
									}
									else
									{
										$firstCellAddress = $sheet->getCellByColumnAndRow($newcolumn, $scorestartingrow)->getCoordinate();
										for($i=1;$i<=$numberofquestion;$i++)
										{
											echo "0 |||||" ;
											$sheet->setCellValueByColumnAndRow($newcolumn, $scorestartingrow, 0);
											$newcolumn++;
										}
										$lastCellAddress = $sheet->getCellByColumnAndRow($newcolumn-1, $scorestartingrow)->getCoordinate();
										$avgscore = 0;
										$avgformula = "=SUM(".$firstCellAddress.":".$lastCellAddress.")/COUNT(".$firstCellAddress.":".$lastCellAddress.")";
										$sheet->setCellValueByColumnAndRow($newcolumn, $scorestartingrow, $avgformula);
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
									$scorestartingrow++;
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
							
							/*Insert final score to spe_finalscore table*/
							$sql_insert_studentlistdetail = " INSERT INTO spe_finalscore (StudentId,SPEID,Score) VALUES('$receiverstuid', '$speid','$finalscore') ";
							if($connection->query($sql_insert_studentlistdetail) === TRUE){
								//echo "Student list detail insert Successful";
								//echo "<br>";
							}else{
								//echo "Student list detail insert Failed";
								//echo "<br>";
								 //die(mysqli_error($connection));
							}
							/*End of inserting*/
							echo "<span style='color:red;'>The final score for this student is ".$finalscore."</span>";
							echo "<br>";
							$scorereceivercol = $scorereceivercol + 2; //Space between the score receiver from same team
							$avgrow = $scorestartingrow;
							$currentlastrow = ($currentrow-1)+$totalscoregiver;
							$finalscorecollast = $checkthiscol+$numberofquestion;
							$firstfinalscorerecord = $sheet->getCellByColumnAndRow($finalscorecollast, $startingcalculate)->getCoordinate();
							for($q=0;$q<$numberofquestion;$q++)
							{
								$firstCellAddress2 =$sheet->getCellByColumnAndRow($currentcolumn, $currentrow)->getCoordinate();
								$lastCellAddress2 = $sheet->getCellByColumnAndRow($currentcolumn, $currentlastrow)->getCoordinate();
								$avgeachformula = "=AVERAGE(".$firstCellAddress2.":".$lastCellAddress2.")";
								$sheet->setCellValueByColumnAndRow($checkthiscol, $scorestartingrow, $avgeachformula);
								/*Set bold and align center*/
								$avgcell =$sheet->getCellByColumnAndRow($checkthiscol, $scorestartingrow)->getCoordinate(); //Get the cell value of starting of receiver name column
								$sheet->getStyle($avgcell)->getFont()->setBold(true);
								/*End styling*/
								$currentcolumn++;
								$checkthiscol++;
							}
							$lastfinalscorerecord = $sheet->getCellByColumnAndRow($finalscorecollast,$scorestartingrow-1)->getCoordinate();
							$finalscorerow1=$scorestartingrow;
							//Calculate final score
							$calculatefinalformula = '=IF((COUNTIF('.$firstfinalscorerecord.':'.$lastfinalscorerecord.',">0"))=0,0,(SUMIF('.$firstfinalscorerecord.':'.$lastfinalscorerecord.',">0")/COUNTIF('.$firstfinalscorerecord.':'.$lastfinalscorerecord.',">0"))/2)';
							$sheet->setCellValueByColumnAndRow($finalscorecol,$finalscorerow1, $calculatefinalformula);
							/*Set bold and align center*/
							$finalscorecell =$sheet->getCellByColumnAndRow($finalscorecol,$finalscorerow1)->getCoordinate(); //Get the cell value of starting of receiver name column
							$sheet->getStyle($finalscorecell)->getFont()->setBold(true);
							/*End styling*/
						}
						$sheet->setCellValueByColumnAndRow($averagetitlecol+1, $scoregiverstartingrow, 'Average of Criteria');
						/*Set bold and align center*/
						$avgcriteriacell =$sheet->getCellByColumnAndRow($averagetitlecol+1, $scoregiverstartingrow)->getCoordinate(); //Get the cell value of starting of receiver name column
						$sheet->getStyle($avgcriteriacell)->getAlignment()->setHorizontal('center');
						$sheet->getStyle($avgcriteriacell)->getFont()->setBold(true);
						/*End styling*/
						
					}
					$newrow = $scoregiverstartingrow + 3; //The new row to start new team record
				}
			}
			//Write to file
			$writer = new Xlsx($spreadsheet);
			$filename = $unitcode."_spe.xlsx";
			$writer->save($filename);
			
			$sql_selectuc = "SELECT * from unitcoordinator WHERE UCID = '$ucid'";
			$request_uc= $connection->query($sql_selectuc);
			if ($request_uc-> num_rows>0){
				while ($row_uc= $request_uc->fetch_assoc()){
					$email = $row_uc['Email'];
					$name = $row_uc['Name'];
					//$email = "appleteng97@yahoo.com";
					$to = $email;
					$subject = "SPE Result for ".$unitcode;
					
					$attachment    = chunk_split(base64_encode(file_get_contents($filename)));
					$boundary      = "PHP-mixed-".md5(time());
					$boundWithPre  = "\n--".$boundary;
					$headers = 'From: datos.tech.murdoch@gmail.com'. "\r\n";
					$headers .= "MIME-Version: 1.0\r\n";
					$headers .= "Content-Type: multipart/mixed; boundary=\"".$boundary."\"\r\n\r\n";
					
					$message = "
					Hi ".$name.",
					
					Here is the result for SPE for module with module code: ".$unitcode."
					
					Thank you.
					";
					
					$body = "--".$boundary."\r\n";
					$body .= "Content-type:text/plain; charset=iso-8859-1\r\n";
					$body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
					$body .= $message."\r\n\r\n";
					$body .= "--".$boundary."\r\n";
					$body .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n";
					$body .= "Content-Transfer-Encoding: base64\r\n";
					$body .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
					$body .= $attachment."\r\n\r\n";
					$body .= "--".$boundary."--";
					
					$result = mail ($to, $subject, $body, $headers);
				}
			}
		}//End while: Loop each record that is due on this day
	}
	else
	{
		echo "no record";
	}
?>