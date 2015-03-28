<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
session_start();
require_once "../classes/event.php";
$user = new User(User::name2id($_SESSION["user_name"]));
$events = $user->list_events();
print json_encode($events) ;
?>

