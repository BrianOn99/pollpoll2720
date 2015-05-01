<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
session_start();

require_once "../classes/event.php";

$event_id = $_POST["event_id"];
$user = new User(User::name2id($_SESSION["user_name"]));
$evt = $user->get_eventobj($event_id);  // php will convert it to int

$voters = $evt->get_result();
print json_encode($voters);
?>

