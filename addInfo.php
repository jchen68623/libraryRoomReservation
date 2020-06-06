<?php

include "DatabaseAdapter.php";

$theDBA = new DatabaseAdapter();

$studentId = htmlspecialchars($_POST["id"]);
$firstname = htmlspecialchars($_POST["firstname"]);
$lastname = htmlspecialchars($_POST["lastname"]);
$password = htmlspecialchars($_POST["password1"]);
$hashed_pwd = password_hash($password,PASSWORD_DEFAULT);

// $studentId = "id2";
// $firstname = "firstname2";
// $lastname = "lastname2";
// $password = "password12";
// $hashed_pwd = password_hash($password,PASSWORD_DEFAULT);

$theDBA->addInfo($studentId,$firstname,$lastname,$hashed_pwd);

header('Location: succeedSignup.html');
exit;

?>