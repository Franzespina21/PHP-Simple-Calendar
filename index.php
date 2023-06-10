<?php 

date_default_timezone_set("Asia/Manila");

$server = "localhost";
$user = "root";
$pass = "";
$database = "php_calendar";

$db = mysqli_connect($server, $user, $pass, $database);

if (!$db) {
	die("<script>alert('Connection Failed.')</script>");
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	<title>PHP Calendar</title>
	<style>
		@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

		@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@600&display=swap');

		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
			font-family: 'Poppins', sans-serif;
		}

		
	</style>
</head>
<body>
	
	<br>
	<br>
	<br>
	<div class="container">

		<div class="row">
			<div class="col-md-3"></div>
			<div class="col-md-6">
				<?php
				echo "<center><h4 style='color:#318133'><b>SELECT YOUR DATE OF APPOINTMENT</b></h4><hr></center>";

				// calling the calendar plugin

				include "plugins/calendar.php";
				$dateComponents = getdate();
				if(isset($_GET['month'])&&isset($_GET['year'])){
					$month = $_GET['month'];
					$year = $_GET['year'];
				}else{
					$month = $dateComponents['mon'];
					$year = $dateComponents['year'];
				}

				// fetching the disabled dates in the table disabled_date

				$d_dates = $db->prepare("SELECT * FROM disabled_date");
				$d_dates->execute();
				$d_dates_res = $d_dates->get_result();
				if($d_dates_res->num_rows > 0){
					while($d_row = $d_dates_res->fetch_assoc()){
						$disabledDates[] = $d_row['disabled_date'];
					}
				}else{
					$disabledDates[] = "";
				}
				// showing calendar
				echo build_calendar($month, $year, $disabledDates);							
				?>
			</div>
			<div class="col-md-3"></div>		
		</div>
	</div>

<center>
	<a href="disable_date.php" class="btn btn-default btn-sm ">Disabled Date</a>
</center>

</body>
</html>