<?php

include "DatabaseAdapter.php";

$theDBA = new DatabaseAdapter();

$arrRoom = $theDBA->checkAvailRooms();
$data = array();
$nameArr = array();
$typeArr = array();
for ($i = 0; $i < count($arrRoom); $i++) {
    $name = ($arrRoom[$i]['name']);
    $type = ($arrRoom[$i]['type']);
    array_push($nameArr,$name);
    array_push($typeArr,$type);
}

array_push($data,$nameArr);
array_push($data,$typeArr);


echo json_encode($data);

?>