<?php
session_start();

require_once "../classes/event.php";

$event_id = $_POST["event_id"];
$user = new User(User::name2id($_SESSION["user_name"]));
$evt = $user->get_eventobj($event_id);

$voters = $evt->get_result_detail();
$output = array("event_type" => $evt->event_type(), "voters" => $voters);
print json_encode($output);
?>

