<?php
include "DatabaseAdapter.php";
$theDBA = new DatabaseAdapter();

$date = htmlspecialchars($_GET ['date']);

$roomArray = $theDBA->findAllRooms();

$scheduleArray = array();

for ($i = 0; $i < count($roomArray); $i+=1) {
    for ($timeStart = 0; $timeStart < 24; $timeStart+=1){
        $timeEnd = $timeStart + 1;
        $check = $theDBA->checkIfAvailable($roomArray[$i]['name'], $timeStart, $timeEnd, $date);
        $roomName = $roomArray[$i]['name'];
        $newData = ['room_name' => "$roomName",
            'start_time' => "$timeStart",
            'end_time' => "$timeEnd",
            'date' => "$date",
            'available' => "$check"
        ];
        array_push($scheduleArray, $newData);
    }
}

// print_r($scheduleArray);
echo json_encode($scheduleArray);
?>