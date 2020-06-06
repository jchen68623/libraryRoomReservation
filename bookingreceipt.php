<!DOCTYPE html>
<html>
<head>
<Title>Booking Receipt</Title>
<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<body>

<h3>Your Booking Receipt</h3>

<button onclick="location.href = 'logout.php'">Log Out</button>
<button onclick="location.href = 'homePage.html'">Go to Homepage</button>

<div class="receipt">

<?php
    include "DatabaseAdapter.php";
    $theDBA = new DatabaseAdapter();
    
    session_start();
    
    $date = htmlspecialchars($_POST['date']);
    $start_time = htmlspecialchars($_POST['time1']);
    $end_time = htmlspecialchars($_POST['time2']);
    $ampmtime1 = htmlspecialchars($_POST['ampmtime1']);
    $ampmtime2 = htmlspecialchars($_POST['ampmtime2']);
    $roomName = htmlspecialchars($_POST['roomName']);
    
    $studentId = $_SESSION["id"];
    
    if($ampmtime1 == 'pm')
        $start_time = $start_time + 12;
    if($ampmtime2 == 'pm')
        $end_time = $end_time + 12;
    
    if($start_time == 12 && $ampmtime1 == 'am')
        $start_time = 0;
    if($end_time == 12 && $ampmtime2 == 'am')
        $end_time = 24;
            
    $arr = $theDBA->tryMakeReservation($studentId, $roomName, $start_time, $end_time, $date);
            
    echo $arr."<br><br>";
    if ($arr == "Congratulations! Your reservation has been made!"){
        $string_start = $start_time." am";
        $string_end = $end_time." am";
        $start = strval($start_time);
        $end = strval($end_time);
        if($start > 12){
            $start = $start - 12;
            $string_start = $start." pm";
        }
        if($start < 1){
            $start = $start + 12;
            $string_start = $start." am";
        }
        if($end > 12){
            $end = $end - 12;
            $string_end = $end." pm";
        }
        if($end < 1){
            $end = $end + 12;
            $string_end = $end." am";
        }
        if ($date == "today"){
            $date = date("Y/m/d").", ".date("l");
        }else if ($date == "tomorrow"){
            $d=strtotime("tomorrow");
            $date = date("Y/m/d",$d).", ".date("l",$d);
        }else if ($date == "day after tomorrow"){
            $d=strtotime("+2 days");
            $date = date("Y/m/d",$d).", ".date("l",$d);
        }
        echo "<br><b>Student id: </b>".$studentId."<br><b>Date: </b>".$date.
             "<br><b>Room: </b>".$roomName."<br><b>Start time: </b>".
             $string_start."<br><b>End time: </b>".$string_end."<br>";
    }
            
?>

</div>
</body>
</html>