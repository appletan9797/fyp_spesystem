$(document).ready(function(){
	$(".cInput").click(function() {
	  $("#iAttention").hide();
	});
	
	$("#iBtnBack").click(function() {
	  location.href= "index.php";
	});
	
	$("#iBtnBackRedirect").click(function() {
	  location.href= "loginredirect.php";
	});
});