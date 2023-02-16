$(document).ready(function(){
	//variables
	var spe_id;
	//var max_rows = 5;
	//var row_num = 1;
	
	$("#iBtnBack").click(function(){
		location.href= "../uc_dashboard/ucdashboard.php";
	});
	
	/*Select spe from the drop down list and display accordingly*/
	$("#spe_selector").change(function(){
		$id = $("#spe_selector option:selected").val();
		$num_of_spe = $("#spe_selector > option").length-1;
		
		if($("#spe_selector option:selected").val() == "new_spe") //If click on add new spe
		{
			$("#container").html(" ");
			$("#container").html("Due Date: <input type='date' class='duedate_text' id='duedate_text' name='duedate_text'><span id='iErrorDate'></span>"
								+"<p id='iErrorRecord'></p>"
								+"<div id='questions' class='questions'></div>"
								+"<div id='buttons'>"
								+"<button type='submit' name='submit' class='save_button' id='save_button'>Save</button>"
								+"<button type='button' class='reset_button' id='reset_button' name='reset'>Reset</button>"
								+"<button type='button' name='addQ' class='add_question_button' id='add_question_button'>Add Question</button>"
								+"</div>");
			//Change Dropdown Value
			$.ajax
			({
				type:'POST',
				url: 'template_dropdown_update_ajax.php',
				data:
				{
					'ucid' :ucidfromphp, 
					'ucode' : ucodefromphp,
				},
				success: function(data) //on success do something
				{
					$("#spe_selector").html(" ");
					$("#spe_selector").html(data);
				}
			});
		}
		else //If click on exising SPE
		{
			$.ajax
			({
				type:'POST', //the way to pass data
				url: 'get_question_ajax.php', //where to pass, which contains sql
				data:
				{
					'id' :$id, //data to pass to the file (url)
				},
				success: function(data) //on success do something
				{
					$("#container").html(" ");
					$("#container").html(data);
				}
			});
		}
		
	});
	
	//Reset form to the last save
	$("body").on('click', '.reset_button', function(){
		//alert($id);
		$.ajax
		({
			type:'POST', //the way to pass data
			url: 'get_question_ajax.php', //where to pass, which contains sql
			data:
			{
				'id' :$id, //data to pass to the file (url)
			},
			success: function(data) //on success do something
			{
				$("#container").html(" ");
				$("#container").html(data);
			}
		});
	});
	
	/*Add questions to the SPE form*/
	var q_number;
	$counter = 0;
    $("body").on('click', '.add_question_button', function(){
		q_number= $('div.cQuestionContainer').length + 1;
		if($id == "undefined")
		{
			alert("Please select an existing SPE form.");
			
		}
		else
		{
			$("#iErrorRecord").html("");
			$("#questions").append("<div class='cQuestionContainer'>"
							+"<div class='cQuestionCounter'><p class='question "+$counter+"' id='question'>"+ q_number + ")</p></div>"
							+ "<input type='hidden' value='' name='checkrecord[]'>"
							+ "<div class='cQuestionText'><textarea class='cQuestion' required name='question_text[]'></textarea></div>"
							+ "<div class='cQuestionBtn'><select required='required' class='input_type_selector' name='input_type[]' id='child_input_type_selector'>"
							+ "<option value='' class='option' id='option' selected disabled>Input Type</option>"
							+ "<option value='5-Likert' class='option'>5-Likert</option>"
							+ "<option value='Comments' class='option'>Comments</option></select>"
							+ "<select required='required' class='section_type_selector' name='section_type[]' id='child_section_type_selector'>"
							+ "<option value='' class='option' id='option' selected disable>Section</option>"
							+ "<option value='All' class='option'>All</option>"
							+ "<option value='Individual' class='option'>Individual</option>"
							+ "<option value='Peer' class='option'>Peer</option></select>"
							+ "<span class='remove_button -1'>X</span></div></div>");
			$counter++;
		}
    });
	
	/*Remove questions*/
	var array = []; //array
	var jsonString;
	$("body").on('click', '.remove_button', function(){
		$(this).parent().parent().remove();
		$number= $('div.cQuestionContainer').length;
		//alert($number);
		for(var i=1;i<=$number;i++)
		{
			$('.question:eq('+(i-1)+')').html(i+")");
		}
		var classname = this.id;
		array.push(classname);
		jsonString = JSON.stringify(array);
	});
	
	/*Save a template as a new SPE form*/
	$("#sec").on('click', '.save_spe_button', function(){
		if($("#spe_selector option:selected").val() == "none")
		{
			alert("Please select a SPE template.");
		}
		else
		{
			//Check Due Date and Check whether the form is empty
			$speDueDate = new Date($("#duedate_text").val());
			$today = new Date();
			$number= $('div.cQuestionContainer').length;
			if ((($("#duedate_text").val()) == "") || ($speDueDate <= $today) || ($number<1))
			{
				if(($("#duedate_text").val()) == "")
				{
					$("#iErrorDate").html("*Please select due date");
				}
				if($speDueDate <= $today)
				{
					$("#iErrorDate").html("*Please select a date that is later than today");
				}
				if($number<1)
				{
					$("#iErrorRecord").html("*Please add at least one record");
				}
			}
			else
			{
				$duedate = $("#duedate_text").val()
				$selected_spe= $("#spe_selector option:selected").val();
				if ($selected_spe == -1) //If it is new SPE that not in database
				{
					var form=$("#createForm");
					$.ajax({
						type: 'POST', //the way to pass data
						url: 'save_new_spe_withouttemplate.php?unitid='+ucodefromphp,  //where to pass, while contains sql
						data:form.serialize(),
						success: function(d)
						{
							//alert(d);
							//location.href = "https://www.facebook.com";
							location.href = "spesubmitted.php"
						}
					});
				}
				else //If it is existing SPE Template-
				{
					$.ajax
					({
						type: 'POST', //the way to pass data
						url: 'save_new_spe_ajax.php',  //where to pass, while contains sql
						data:
						{
							'ucid' :ucidfromphp, 
							'ucode' : ucodefromphp,
							'duedate' : $duedate,
							'spetemplateid' : $selected_spe,
						},
						success: function(data)
						{
							//alert(data);
							//location.href = "https://www.facebook.com";
							location.href = "spesubmitted.php"
						}
					});
				}
			}
		}
	});
	
	$('body').on('change','#duedate_text', function () {
		
		$speDueDate = new Date($("#duedate_text").val());
		$today = new Date();
		if ($speDueDate <= $today)
		{
			$("#iErrorDate").html("*Please select a date that is later than today");
		}
		else
		{
			$("#iErrorDate").html("");
		}
	});
	
	/*Delete SPE Template*/
	$("#sec").on('click', '.delete_spe_button', function(){
		$.ajax
			({
				type:'POST', //the way to pass data
				url: 'deletespe_template_question_ajax.php', //where to pass, which contains sql
				data:
				{
					'speid' :$id,
				},
				success: function(data) //on success do something
				{
					//location.reload();
					location.href = location.href;
				}
			});
	});
	
	/*Save questions in the database*/
	$("body").on('click', '.save_button', function(){
		if (jsonString != null)
		{
			$speid = $id;
			//row_num = 1;
			//alert("save");
			$.ajax
			({
				type:'POST', //the way to pass data
				url: 'delete_question_ajax.php', //where to pass, which contains sql
				data:
				{
					'data':jsonString, //data to pass to the file (url)
					'speid' :$speid,
				},
				success: function(data) //on success do something
				{
					$("#questions").html(" "); //clear the questions' div
					$("#questions").html(data); //append things that echo in (url)
				}
			});
			
			//If no more question, delete spe template
			$number= $('div.cQuestionContainer').length;
			if($number == 0)
			{
				//alert($number);
				$.ajax
				({
					type:'POST', //the way to pass data
					url: 'deletespe_ajax.php', //where to pass, which contains sql
					data:
					{
						'speid' :$speid,
					},
				});
			}
		}
	});
});