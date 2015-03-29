<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
session_start();
require_once "../classes/event.php";
$user = new User(User::name2id($_SESSION["user_name"]));
$user->create_event(array(
        "title" => $_POST["title"],
        "description" => $_POST["desc"],
        "event_type" =>  $_POST["type"],
        "start_time" => $_POST["start"],
        "end_time" =>  $_POST["end"]));

print_r($_POST) ;
?>

