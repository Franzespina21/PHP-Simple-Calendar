<?php 
function build_calendar($month, $year) {
    // connection
    $mysqli = mysqli_connect('localhost', 'root', '', 'php_calendar');
    $daysOfWeek = array('Sun', 'Mon','Tue','Wed','Thu','Fri','Sat');
    $firstDayOfMonth = mktime(0,0,0,$month,1,$year);
    $numberDays = date('t',$firstDayOfMonth);
    $dateComponents = getdate($firstDayOfMonth);
    $monthName = $dateComponents['month'];
    $dayOfWeek = $dateComponents['wday'];
    $datetoday = date('Y-m-d');
    $currentTime = date('G');

    $calendar = "<table class='table table-responsive' id='table' style='border:1px solid lightgray;'>";

    // going to previous month
    $calendar.= "<a style='padding:2%;border:none;margin-top:15px; position:absolute' id='prevmonth' class='btn btn-xs btn-default btn-outline-primary' href='?month=".date('m', mktime(0, 0, 0, $month-1, 1, $year))."&year=".date('Y', mktime(0, 0, 0, $month-1, 1, $year))."'><span class='fa fa-chevron-left' style=''></span></a>&emsp;";

    // going to next month
    $calendar.= "<a style=' float:right;padding:2%;border:none;margin-top:16px' id='nextmonth' href='?month=".date('m', mktime(0, 0, 0, $month+1, 1, $year))."&year=".date('Y', mktime(0, 0, 0, $month+1, 1, $year))."' class='btn btn-sm btn-default ' ><span class='fa fa-chevron-right' style=''></span></a></center><br>";
    $calendar .= "<tr><h5 class='text-center' style='margin-left:22px'>$monthName $year</h5>";
    foreach($daysOfWeek as $day) {
        $calendar .= "<th class='header notranslate' style='text-align:center;padding:15px; font-size: 12px;'>$day</th>";
    } 
    $currentDay = 1;
    $calendar .= "</tr><tr >";
    if($dayOfWeek > 0) { 
        for($k=0;$k<$dayOfWeek;$k++){
            $calendar .= "<td class='empty' style='background-color:white;'></td>"; 
        }
    }
    $month = str_pad($month, 2, "0", STR_PAD_LEFT);
    while ($currentDay <= $numberDays) {
        if ($dayOfWeek == 7) {
            $dayOfWeek = 0;
            $calendar .= "</tr><tr >";
        }
        $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
        $date = "$year-$month-$currentDayRel";
        $dayname = strtolower(date('l', strtotime($date)));
        $eventNum = 0;

        // fetching the disabled dates in the table disabled_date
        $d_dates = $mysqli->prepare("SELECT * FROM disabled_date");
        $d_dates->execute();
        $d_dates_res = $d_dates->get_result();
        if($d_dates_res->num_rows > 0){
            while($d_row = $d_dates_res->fetch_assoc()){
                $disabledDates[] = $d_row['disabled_date'];
            }
        }else{
           $disabledDates[] = "";
       }

       if (in_array($date, $disabledDates)) {
        $calendar.="<td style='background:#f1f4f8; color:gray;height:70px;' ><h5 style='margin-top:20px;text-align:center'>$currentDay</h5>";
    } elseif($dayname=='' || $date<date('Y-m-d', strtotime("0 day"))){ //You can disable the  calendar date by modifying this part
        $calendar.="<td style='background:#f1f4f8; color:gray;height:70px;' ><h5 style='margin-top:20px;text-align:center'>$currentDay</h5>";
    }elseif($date==date('Y-m-d') && $currentTime >= 17){ // If the current time of the day is at 5:00 PM (which is 17 in military time), the date will be disabled.
       $calendar.="<td style='background:#df4759 ; color:white;height:70px;' ><h5 style='margin-top:20px;text-align:center;margin-bottom:-10px'>$currentDay</h5> ";
   }else{
    $totalappointments = checkSlots($mysqli, $date); //checking the available slot
    if($totalappointments>15){ //If there are dates that are all equal and is greater than 15, it will disable the date.  
        $calendar.="<td style='background:#df4759 ; color:white;height:70px;' ><h5 style='margin-top:20px;text-align:center'>$currentDay</h5> ";
    }else{
        $availableSlots = 15 - $totalappointments; 
        $encodedVariable = base64_encode($date);
        $link= 'chosen_date.php?date='.urlencode($encodedVariable); //Your chosen date that was encrypted
        $calendar.="<td   style='background:#318133' id='dayH'
        ><a href='$link' style='color:white; ;text-decoration:none;text-align:center;'><h5 style='margin-top:20px'>$currentDay</h5></a>";
    }
}
$calendar .="</td>";
$currentDay++;
$dayOfWeek++;
}
if ($dayOfWeek != 7) { 
    $remainingDays = 7 - $dayOfWeek;
    for($l=0;$l<$remainingDays;$l++){
        $calendar .= "<td class='empty' style='background-color:white;'></td>"; 
    }
}
$calendar .= "</tr>";
$calendar .= "</table>";
return $calendar;
}

function checkSlots($mysqli, $date){ //checking the available slot
    $stmt = $mysqli->prepare("SELECT * from calendar where calendar_date=?");
    $stmt->bind_param('s', $date);
    $totalappointments = 0;
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows>0){
            while($row = $result->fetch_assoc()){
                $totalappointments++; //counting slots
            }
            $stmt->close();
        }
    }
    return $totalappointments;
}
?>
