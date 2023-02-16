<?php

$data = date('Y-M-D');
	echo $data;
	/*$file = fopen('store.txt','a');
	fwrite($file,$data."\r\n");
	fclose($file);*/
	
	/*$to ="datos.tech.murdoch@gmail.com";
	$subject = "-Warning: Self Peer Evaluation Due Tomorrow";
	$message = "
	Hi,
	
	Please note that Self Peer Evaluation for is going to due tomorrow.

	Please complete before the due date:
	http://localhost:81/spesys/evaluate/evaluation.php?spe=$speid
	
	Best regards,
	";
	
	$headers ="From: datos.tech.murdoch@gmail.com \r\n";
	if (mail ($to, $subject, $message, $headers))
	{
		echo "success";
	}
	else
	{
		print_r(error_get_last()['message']);
	}
	
	*/
?>