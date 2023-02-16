<?php
	include "connection.php";
	
	//$classname = $_POST['classname'];
	$speid = $_POST['spe_id'];
	$array = json_decode(stripslashes($_POST['data']));
	$myarray = array();
	
	$unitcode = "ICT302";
	$duedate_sql = "SELECT duedate FROM spe WHERE speid = '$speid' AND unitcode = '$unitcode'";
	$duedate_result = $connection->query($duedate_sql);
	if(!$duedate_result)
	{
		echo "Due Date: <input type='date' class='duedate_text' id='duedate_text' name='duedate_text'><span id='iErrorDate'></span>";
		echo "<p id='iErrorRecord'></p>";
	}
	else
	{
		$duedate = $duedate_result->fetch_assoc();
		echo "Due Date: <input type='date' class='duedate_text' value='".$duedate['duedate']."'id='duedate_text' name='duedate_text'><span id='iErrorDate'></span>";
		echo "<p id='iErrorRecord'></p>";
	}
	
	foreach($array as $d)
	{
		echo "<script>console.log('Debug Objects: " . $d . "' );</script>";
		$test2 = "questionno <> ".$d;
		array_push($myarray, $test2);
	}
	
	$test = join(" AND ", $myarray);
	//echo "<script>console.log('Debug Objects: " . $test . "' );</script>";
	
	$spe_form_sql = "SELECT questionno, question, inputtype, section FROM spe_question WHERE speid = $speid AND $test";
	$result = $connection ->query($spe_form_sql);
	if(!$result)
	{
		trigger_error('Invalid query: '.$connection->error);
	}
	else
	{
		if ($result-> num_rows > 0)
		{
			$rowcount = 1;
			$counter = 1;
			while ($question = $result -> fetch_assoc())
			{
				echo "<div class='cQuestionContainer'>";
				echo "<div class='cQuestionCounter'><p class='question' id='question".$counter."'>".$rowcount.")</p></div>";
				echo "<input type='hidden' value='".nl2br($question['questionno'])."' name='checkrecord[]'>";
				echo "<div class='cQuestionText'><textarea class='cQuestion' required name='question_text[]'>".$question['question']."</textarea></div>";
				echo "<div class='cQuestionBtn'><select required='required' class='input_type_selector' name='input_type[]' id='input_type_selector'>";
				if ($question['inputtype'] == "5-Likert")
				{
					echo "<option value='5-Likert' selected='selected' class='option'>5-Likert</option>";
					echo "<option value='Comments' class='option'>Comments</option></select>";
				}
				else
				{
					echo "<option value='5-Likert' class='option'>5-Likert</option>";
					echo "<option value='Comments' selected='selected' class='option'>Comments</option></select>";
				}
				
				echo "<select required='required' class='section_type_selector' name='section_type[]' id='section_type_selector'>";
				if ($question['Section'] == "All")
				{
					echo "<option selected='selected' value='All' class='option'>All</option>";
					echo "<option value='Individual' class='option'>Individual</option>";
					echo "<option value='Peer' class='option'>Peer</option></select>";
				}
				else if($question['Section'] == "Individual")
				{
					echo "<option value='All' class='option'>All</option>";
					echo "<option selected='selected' value='Individual' class='option'>Individual</option>";
					echo "<option value='Peer' class='option'>Peer</option></select>";
				}
				else
				{
					echo "<option value='All' class='option'>All</option>";
					echo "<option value='Individual' class='option'>Individual</option>";
					echo "<option selected='selected' value='Peer' class='option'>Peer</option></select>";
				}
				
				echo "<span class='remove_button' id='".nl2br($question['questionno'])."'>X</span></div>";
				echo "</div>";
				$rowcount++;
				$counter++;
			}
		}
	}
	
?>