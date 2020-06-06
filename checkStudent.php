<?php
include "DatabaseAdapter.php";

$theDBA = new DatabaseAdapter();

$studentId = htmlspecialchars($_POST["studentId"]);
$password = htmlspecialchars($_POST["pass"]);

session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && $_SESSION["id"] === $studentId){
    header("Location: homePage.html");
    exit;
}

$studentData = $theDBA->readData($studentId,$password);

// echo $studentData[0]['passwords'] . PHP_EOL;
$valid = password_verify($password, $studentData[0]['passwords']);

if($valid){
    $_SESSION["loggedin"] = true;
    $_SESSION["id"] = $studentId;
    $_SESSION["password"] = $password;
    header('Location: homePage.html');
    exit;
}else{
    echo '<script language="javascript">';
    echo 'alert("Please enter correct id or password");';
    echo '{document.location.href="Login.html"};';
    echo '</script>';
    exit;
}

?>