<?php
include "DatabaseAdapter.php";
$theDBA = new DatabaseAdapter();

echo json_encode($theDBA->getAllReservations());

?>