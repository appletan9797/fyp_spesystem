<?php ob_start(); ?>
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
	
function strip_bom($string)
{
    if (substr($string, 0, 3) === "\xEF\xBB\xBF") {
        $string = substr($string, 3);
    }
    return $string;
}

/*if (!(preg_match('/[^A-Za-z\s]/', 'Person Id')))
{
	echo "<script>alert('".(preg_match('/[^A-Za-z\s]/', 'Person Id'))."')</script>";
}*/


if(isset($_SESSION['ErrorArray']))
{
	
	//echo "<script>alert('".$_SESSION['test']."')</script>";
	//echo "<script>alert('".count($_SESSION['ErrorArray'])."')</script>";
}

if(isset($_POST["submit"])){
	
	$arrError = array();
	$message = array();
	$wrongFileCounter = 0;
	$rightFileCounter = 0;
	$gotRecord = 0;
	$arr_problemRecord = array(); //can put in table to show to user later
	$arr_ArrayOfBadRecords = array();
	
	if (!empty($_FILES['fileinput'])) 
	{
		foreach($_FILES['fileinput']['name'] as $n => $name) {
			
			//$error = 0;//0 = no error, if it change to 1 means there's error.
			if($_FILES['fileinput']['name'][$n]){
				$tmp_name = $_FILES['fileinput']['tmp_name'][$n];
				$files = pathinfo($_FILES['fileinput']['name'][$n]);
				$fileName = $files['filename'].'_'.microtime(true).'.'. $files['extension'];
				$target_dir = "file/";
				$target_file = $target_dir . $fileName;
				//Check whether the file is in .csv extension
				if($files['extension'] == "csv"){
					//Upload file to server
					if (move_uploaded_file($_FILES["fileinput"]["tmp_name"][$n], $target_file)) 
					{
						//$message["Success"] = "The file(s) has been uploaded.\n";
						$rightFileCounter = $rightFileCounter + 1;
						
						//Insert into StudentList table
						$sql_insert_studentlist = "INSERT INTO studentlist (UCID,Filename) VALUES('$ucid','$fileName')";
						if($connection->query($sql_insert_studentlist) === TRUE)
						{
							//echo "Created new record\n";
							//echo "<br>";
						}
						else
						{
							//echo "Error create new record\n";
							//echo "<br>";
						}
						$StudentListID = $connection->insert_id;
					} 
					else 
					{
						$arrError["ErrorUpload"] = "Sorry, there was an error uploading your file:".$_FILES['fileinput']['error']."\n";
					}
					//$message["Success"] = "The file(s) has been uploaded.\n";
					$counter = 1;
					//Upload data to db
					$file_handler = fopen($target_file, "r");
					while($record = fgetcsv($file_handler))
					{						
						if ($counter > 1)
						{	
							$error = 0;
							//Store record into variable
							$stuId = mysqli_real_escape_string($connection, $record[0]);
							$surname = mysqli_real_escape_string($connection, $record[1]);
							$title = mysqli_real_escape_string($connection, $record[2]);
							$givenname = mysqli_real_escape_string($connection, $record[3]);
							$teachperiod = mysqli_real_escape_string($connection, $record[4]);
							$ucode = mysqli_real_escape_string($connection, $record[5]);
							$teamid = mysqli_real_escape_string($connection, $record[6]);
							$email = mysqli_real_escape_string($connection, $record[7]);
							
							$stuId = strip_bom($stuId);
							$surname =strip_bom($surname);
							$title = strip_bom($title);
							$givenname = strip_bom($givenname);
							$teachperiod = strip_bom($teachperiod);
							$ucode = strip_bom($ucode);
							$teamid = strip_bom($teamid);
							$email = strip_bom($email);
							
							//echo "<script>alert('".$stuId."')</script>";
							//Error checking for each column of record
							//Check if there's empty field
							if((empty($stuId)) || (empty($surname)) || (empty($title)) || (empty($givenname)) || (empty($teachperiod)) || (empty($ucode)) || (empty($teamid)) || (empty($email)))
							{
								$error = 1;
							}
							//echo "<script>console.log('".$stuId."')</script>";
							$sql_selectsamerecord = "SELECT * from studentlistdetail WHERE StudentId = $stuId AND TeachPeriod = '$teachperiod' AND
													UnitCode = '$ucode' AND TeamId = '$teamid' AND Email = '$email'";
							$request_samerecord= $connection->query($sql_selectsamerecord);
							if ($request_samerecord-> num_rows>0){
								$error = 1;
								$message['DuplicateRecord'] = "Duplicate record not uploaded.";
							}
							
							//Check whether its in correct format
							if (preg_match('/[^0-9]/', $stuId))
							{
								$error = 1;
								//echo "<script>alert('error1');</script>";
							}
							
							if (preg_match('/[^A-Za-z\s]/', $surname))
							{
								$error = 1;
								//echo "<script>alert('error2');</script>";
							}
							
							if (!(preg_match('/^(Mr|Ms|Mrs)$/i',$title)))
							{
								$error = 1;
								//echo "<script>alert('error3');</script>";
							}
							
							if (preg_match('/[^A-Za-z\s]/', $givenname))
							{
								$error = 1;
								//echo "<script>alert('error4');</script>";
							}
							
							if (!(preg_match('/^[a-zA-Z]{1}[0-9]{1}\s{1}[0-9]{4}$/', $teachperiod)))
							{
								//$error = 1;
								if (!(preg_match('/^[a-zA-Z]{3}\s{1}[0-9]{4}$/', $teachperiod)))
								{
									$error = 1;
									//echo "<script>alert('error5');</script>";
								}
							}
							
							if (!(preg_match('/^[a-zA-Z]{3}[0-9]{3,4}$/', $ucode)))
							{
								$error = 1;
								//echo "<script>alert('error6');</script>";
							}
							
							if (!(preg_match('/^[a-zA-Z]{2}[0-9]{2,3}$/', $teamid)))
							{
								$error = 1;
								//echo "<script>alert('error7');</script>";
							}
							
							/*if ((preg_match('/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/', $email)))
							{
								$error = 1;
								//echo "<script>alert('error8');</script>";
							}*/
							
							//Insert
							if ($error == 0)
							{
								$sql_insert_studentlistdetail = " INSERT INTO studentlistdetail (StudentId,Surname,Title,GivenName,TeachPeriod,UnitCode,TeamId,Email,StudentListID) VALUES('$stuId', '$surname','$title','$givenname','$teachperiod','$ucode','$teamid','$email',$StudentListID) ";
								if($connection->query($sql_insert_studentlistdetail) === TRUE){
									//echo "Student list detail insert Successful";
									//echo "<br>";
								}else{
									//echo "Student list detail insert Failed";
									//echo "<br>";
								}
								$gotRecord = $gotRecord + 1;
							}
							else
							{
								for ($i=0;$i<=7;$i++)
								{
									array_push($arr_problemRecord,$record[$i]);
								}
								array_push($arr_ArrayOfBadRecords,$arr_problemRecord);
							}
						}
						$counter++;
					}
					if ($gotRecord > 0)
					{
						$message["Success"] = "The file(s) has been uploaded.\n";
					}
					fclose($file_handler);
				}
				//If file extension not csv
				else{
					$arrError["FileType"] = "Only file with .csv extension accepted. Please try again.";
					$wrongFileCounter = $wrongFileCounter + 1;
					//echo "Please only upload csv file";
				}
			}
		}
		//If multiple files selected, and some are not in .csv extension, show this message
		if (($wrongFileCounter > 0)&& ($rightFileCounter > 0))
		{
			$message['Attention'] = "File(s) Uploaded. The file(s) that is not with .csv extension is ignored.";
		}
		
		//If got error record, ask whether to redirect to new page
		if (!empty($arr_ArrayOfBadRecords))
		{
			$message['CheckErrorRecord'] = "But there's some bad record(s). <a href='checkrecord.php'>Click here</a> to check.";
		}
		
		$_SESSION['ErrorArray'] = $arr_ArrayOfBadRecords;
	}
	
	//Get data if file is uploaded via drag and drop
	/*if (!empty($_SESSION['Attention']))
	{
		$message['Attention'] = $_SESSION['Attention'];
	}
	if (!empty($_SESSION['CheckErrorRecord']))
	{
		$message['CheckErrorRecord'] = $_SESSION['CheckErrorRecord'];
	}
	if (!empty($_SESSION['Success']))
	{
		$message['Success'] = $_SESSION['Success'];
	}
	if (!empty($_SESSION['FileType']))
	{
		$arrError['FileType'] = $_SESSION['FileType'];
	}*/
}

?>

<html>
<head>
    <meta charset="utf-8" />
	<script src="https://code.jquery.com/jquery-3.5.1.js"></script>	
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
	<link rel="stylesheet" type="text/css" href="CSS/style.css">
	<!-- font-awesome-icons -->
	<link rel="stylesheet" href="css/font-awesome.min.css" />
	<!-- //font-awesome-icons -->

	<!-- google fonts -->
	<link href="//fonts.googleapis.com/css?family=Muli:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;subset=latin-ext,vietnamese" rel="stylesheet">
	<link href="//fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&amp;subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese" rel="stylesheet">
	<!-- //google fonts -->
    <title>Upload Student List</title>
</head>
<body>
<script type="text/javascript" src="JS/action.js"></script>

<div class="col-md-12 col-xs-12" id="contact" style="padding:0px;">
	<div class="col-md-12 col-xs-12" style="padding:0px;">
		<!--Page header-->
		
		<div class="col-md-12 col-xs-12" style="background-color:#1d1a1a;">
			<div class="col-md-6 col-xs-6" style="padding-top:8px;padding-bottom:8px;text-align:left;font-size:x-large;font-weight:bold;color:#ffffff;">
				<div class="header"><img style="cursor:pointer;width:100px;height:30px;margin-right: 15px;" src="images/murdoch_logo.png"><span style='cursor:pointer;' onclick="window.location.href='../login/index.php'">Self Peer Evaluation System</span></div>
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
			<span ><img onclick="window.location.href='../login/logout.php'" title='Log Out' style="cursor:pointer;width:40px;height:40px;" src="images/spe_profile_icon.png"><span>
			</div>
		</div>
		
		<div class="cTitle">
			<h2>Upload Student List</h2>
		</div>

		<div id="iErrorMsgDiv">
		<?php
		
			if (isset($message['DuplicateRecord']))
			{
				echo "<div id='iAttention' class='cMessage'> <img id='iAttentionImg' class='cMsgImgLess' src='images/attention.png'><font id='iAttentionMsg' class='cMsgLineLess'>". $message['DuplicateRecord']."</font></div>";
			}
			
			if (isset($message['Attention'])){
				if (isset($message['CheckErrorRecord']))
				{
					echo "<div id='iAttention' class='cMessage'> <img id='iAttentionImg' class='cMsgImg' src='images/attention.png'><font id='iAttentionMsg' class='cMsgLine'>". $message['Attention']."</font><br>".$message['CheckErrorRecord']."</div>";
				}
				else
				{
					echo "<div id='iAttention' class='cMessage'> <img id='iAttentionImg' class='cMsgImgLess' src='images/attention.png'><font id='iAttentionMsg' class='cMsgLineLess'>". $message['Attention']."</font></div>";
				}
			}
			else{
				if (isset($arrError["FileType"]))
				{
					echo "<div id='iError' class='cMessage'> <img id='iErrorImg' class='cMsgImg' src='images/error.png'><font id='iErrorMsg' class='cMsgLine'>". $arrError['FileType']."</font></div>";
				}
				else
				{
					if(isset($message['Success']))
					{
						if (isset($message['CheckErrorRecord']))
						{
							echo "<div id='iConfirm' class='cMessage'> <img id='iConfirmImg' class='cMsgImg' src='images/good.png'><font id='iConfirmMsg' class='cMsgLine'>". $message['Success']."</font><br>".$message['CheckErrorRecord']."</div>";
						}
						else
						{
							echo "<div id='iConfirm' class='cMessage'> <img id='iConfirmImg' class='cMsgImgLess' src='images/good.png'><font id='iConfirmMsg' class='cMsgLineLess'>". $message['Success']."</font></div>";
						}
					}
				}
			}
		?>
		</div>

		<form id="uploadFile" method = "POST" enctype = "multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
		<div class="cUploadBox" id="iDropUpload">
			<img id="iUploadIcon" class="cCenter cButtonAction" src="images/upload.png" alt="Upload Icon">
			<div id="iWord">Drag and Drop file here to Upload</div>
			<input type="file" name="fileinput[]" id="iFilechooser" hidden="hidden" accept=".csv" data-multiple-caption="{count} files selected" multiple />
			<button type="button" id="iBtnBrowse" class="cButtonAction">or BROWSE for file </button>
			<div class="cCenterDiv">
				<span id="iFilename"></span>
			</div>
		</div>

		<div class="cBtnContainer">
			<button id="iBtnClear" class="cButtons cButtonAction" type="button">Clear</button>
			<input type="submit" value="Upload" id="iBtnUpload" class="cButtons cButtonAction" name="submit"></input>
		</div>
	</div>
</div>


</form>
</body>
</html>