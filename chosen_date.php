<?php 

if(isset($_GET['date'])){

	$date_en = $_GET['date'];
	$date = base64_decode(urldecode($date_en));
	echo "<p style='padding: 70px 0;text-align: center;font-weight:bold'>Your chosen date: ".$date."</p>";	//display the chosen date
}

?>