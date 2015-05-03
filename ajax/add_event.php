<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
session_start();

require_once "../classes/event.php";

print "\nraw:\n";
$data = json_decode(file_get_contents('php://input'), true);
print_r($data);

$user = new User(User::name2id($_SESSION["user_name"]));
$evt = $user->create_event(array(
        "title" => $data["title"],
        "description" => $data["desc"],
        "event_type" =>  $data["type"],
        "start_time" => $data["start"],
        "end_time" =>  $data["end"]));

?>

