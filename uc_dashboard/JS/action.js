$(document).ready (function(){
		
	/*checkrecord.php*/
	$("#iBtnUpload").click(function(){
		location.href= "../uploadlist/uploadstudentlist.php";
	});
	
	$(".cEdit").click(function(){
		$id = $(this).parent().attr("id");	
		$unitcode = $(this).parentsUntil(".cSPEContainer").closest(".cSPEContainer").prev().parent().attr('id');
		location.href= "../editspe/edit_question.php?unit="+$unitcode;
		//alert($id);
	});
	
	$(".cDownload").click(function(){
		$id = $(this).parent().attr("id");
		$unit = $(this).parentsUntil(".cSPEContainer").closest(".cSPEContainer").prev().parent().attr("id");
		//alert($unit);
		location.href= "downloadrecords.php?spe="+$id+"&unit="+$unit;
	});
	
	$(".cDueDate").click(function(){
		$id = $(this).parent().attr("id");	
		$unitcode = $(this).parentsUntil(".cSPEContainer").closest(".cSPEContainer").prev().parent().attr('id');		
		location.href= "../editspe/edit_question.php?unit="+$unitcode;
		//alert($id);
	});
	
	$('body').on('click','.cDelete',function(){
		$id = $(this).parent().attr("id");
		$unitcode = $(this).parentsUntil(".cSPEContainer").closest(".cSPEContainer").prev().parent().attr('id');
		//alert($id);
		$.ajax
		({
			type:'POST',
			url: 'deletespe.php',
			data:
			{
				'speid' :$id,
				'unitcode':$unitcode,
			},
			success: function(data) //on success do something
			{
				//location.reload();
				$("."+$unitcode).html(data);
			}
		});
	});
	
	$(".cBtnAddSPE").click(function(){
		$unitcode = $(this).parent().parent().parent().parent().attr('id');
		location.href= "../create_spetemplate/create_spe.php?unit="+$unitcode;
	});

	
	$(".cBtnCheckGroup").click(function(){
		$unitcode = $(this).parent().parent().parent().parent().attr('id');
		location.href= "displaygroup.php?unit="+$unitcode;
	});
	
	/*displaygroup.php*/
	$("#iBtnBack").click(function(){
		//$unitcode = $(this).parent().parent().attr('id');
		location.href= "ucdashboard.php";
	});
});