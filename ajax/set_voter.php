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
$evt = $user->get_eventobj($data["event_id"]);  // php will convert it to int

if ($evt->started()) {
    header('HTTP/1.1 409 cannot set voter on started event');
    exit("event has started, cannot set voter");
}

$evt-> set_voters($data["voters"]);

?>

