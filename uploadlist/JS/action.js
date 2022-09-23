$(document).ready (function(){
	
	//Drag and drop to upload file
	var droppedFiles = false;
	var input = document.getElementById("iFilechooser");
	
	$("#iDropUpload").on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
		e.preventDefault();
		e.stopPropagation();
	})
	
	.on('drop', function(e) {
		droppedFiles = e.originalEvent.dataTransfer.files;
		//showFileName(droppedFiles);
		$(".cMessage").remove();
		input.files = e.originalEvent.dataTransfer.files;
		if ((droppedFiles.length) > 1)
		{
			var filelength = droppedFiles.length;
			$("#iFilename").html(filelength +" files selected");
		}
		else
		{
			var filename = $("#iFilechooser").val().match(/[\/\\]([\w\d\s\.\-\(\)]+)$/)[1];
			$("#iFilename").html("File Selected: " + filename);
		}
	});
		
	//Trigger file input when click on button
	$("#iBtnBrowse").click(function(){
		$("#iFilechooser").trigger('click');
		$(".cMessage").remove();
	});
	
	//Display selected file's filename
	$(input).change(function(){	
		if ((this.files.length) > 1)
		{
			var filelength = this.files.length;
			$("#iFilename").html(filelength +" files selected");
		}
		else
		{
			var filename = $("#iFilechooser").val().match(/[\/\\]([\w\d\s\.\-\(\)]+)$/)[1];
			$("#iFilename").html("File Selected: " + filename);
		}
	});	
	
	//Clear selected file
	$("#iBtnClear").click(function(){
		$("#iFilechooser").val("");
		$("#iFilename").html("");
		$(".cMessage").remove();
	});
	
	function showFileName(files)
	{
		$("#iFilename").html(files.length > 1 ? (input.attr('data-multiple-caption') || '').replace( '{count}', files.length ) : files[ 0 ].name);
	}
	
	/*checkrecord.php*/
	$("#iBtnBack").click(function(){
		location.href= "uploadstudentlist.php";
	});
});