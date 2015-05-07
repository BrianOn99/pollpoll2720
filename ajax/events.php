<?php
session_start();
require_once "../classes/event.php";
$user = new User(User::name2id($_SESSION["user_name"]));
$events = $user->list_events();
print json_encode($events) ;
?>

