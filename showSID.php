<?php

session_start();
$name =  $_SESSION["id"];
echo (string)$name;

?>