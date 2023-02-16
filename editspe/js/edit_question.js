$(document).ready(function(){
	//varibles
	var max_rows = 5;
	var row_num = 1;
    /*Add new SPE from the drop down box*/
    /*var number = 1;
    var store;
    $("#spe_selector").change(function() {
        if ($("#spe_selector option:selected").val() == "new_spe") 
        {
            $("#spe_selector option[value='new_spe']").remove();
            $("#spe_selector").append('<option value=SPE'+number+' class="option">SPE' + number + '</option>');
            $("#spe_selector").append('<option value="new_spe" class="option">-New SPE-</option>');
            $("#spe_selector").val("SPE" + number);
            number++;
        }
        else 
        {
            store = $("#spe_selector option:selected").val();
        }
    });*/
	
	/*Select spe from the drop down list and display accordingly*/
	var spe_id;
	$("#spe_selector").change(function(){
		$id = $("#spe_selector option:selected").val();
		$spe_id = $id;
		//$unit_code = "ICT302";
		//alert($id);
		$.ajax
		({
			type:'POST', //the way to pass data
			url: 'get_question_ajax.php', //where to pass, which contains sql
			data:
			{
				'id' :$id, //data to pass to the file (url)
				'ucode': ucodefromphp,
			},
			success: function(data) //on success do something
			{
				$("#spe_selector option").each(function(){
					if($(this).val()==$id){ // EDITED THIS LINE
						$(this).attr("selected","selected");    
					}
				});
				$("#container").html(" "); //clear the questions' div
				$("#container").html(data); //append things that echo in (url)
			}
		});
	});
	
	//Reset form to the last save
	$("#reset_button").click(function(){
		$id = $spe_id;
		//alert($id);
		$.ajax
		({
			type:'POST', //the way to pass data
			url: 'get_question_ajax.php', //where to pass, which contains sql
			data:
			{
				'id' :$id, //data to pass to the file (url)
				'ucode': ucodefromphp,
			},
			success: function(data) //on success do something
			{
				$("#container").html(" "); //clear the questions' div
				$("#container").html(data); //append things that echo in (url)
			}
		});
	});
	
	/*Add questions to the SPE form*/
	var q_number;
	$counter = 0;
    $("#add_question_button").click(function(){
		//count the number of <p> tags in questions div
		$id = $spe_id;
		q_number = $('div.cQuestionContainer').length + 1;
		if($id == "none")
		{
			alert("Please select an existing SPE form.");
			
		}
		else
		{
			if(row_num <= max_rows)
			{
				$("#container").append("<div class='cQuestionContainer'>"
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
				row_num++;
				$counter++;
			}
			else
			{
				alert("Please save before adding new questions.");
			}
			
		}
		//$("[name=nb_elements]").val(q_number);
    });
	var array = []; //array
	var jsonString;
	
	/*Remove question*/
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
		/*if($counter!=0)
		{
			$(this).parent('p').remove();
			$counter--;
		}
		else
		{
			var classname = this.id;
			array.push(classname);
			jsonString = JSON.stringify(array);
			//$id = $spe_id;
			//alert($id);
			$.ajax
			({
				type:'POST', //the way to pass data
				url: 'remove_question_ajax.php', //where to pass, which contains sql
				data:
				{
					data:jsonString, //data to pass to the file (url)
					'spe_id' :$spe_id,
				},
				success: function(data) //on success do something
				{
					$("#container").html(" "); //clear the questions' div
					$("#container").html(data); //append things that echo in (url)
				}
			});
		}
		row_num--;*/
		
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
	
	/*Save questions in the database*/
	//$("form").on('submit', function(){
	$("body").on('click', '.save_button', function(){
		if (jsonString != null)
		{
			//alert("got something");
			$speid = $spe_id;
			row_num = 1;
			//alert("save");
			
			$.ajax
			({
				type:'POST', //the way to pass data
				url: 'delete_question_ajax.php', //where to pass, which contains sql
				data:
				{
					data:jsonString, //data to pass to the file (url)
					'speid' :$speid,
				},
				success: function(data) //on success do something
				{
					$("#container").html(" "); //clear the questions' div
					$("#container").html(data); //append things that echo in (url)
				}
			});
		}
	});
});