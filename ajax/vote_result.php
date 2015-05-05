<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require_once "../classes/event.php";
$event_id = $_POST["event_id"];

if (array_key_exists("voter_id", $_POST) && array_key_exists("key", $_POST)) {
        $res = DB::queryFirstRow("SELECT 1 FROM voter "
                                        . "WHERE voter_id=%d AND keyVar=%s AND event_id=%d",
                                        $_POST["voter_id"], $_POST["key"], $event_id); 
        if ($res == NULL) {
                exit("Incorrect voter Info");
        }
        $evt = new Event_voter($_POST["voter_id"]);
} else {
        session_start();
        $user = new User(User::name2id($_SESSION["user_name"]));
        $evt = $user->get_eventobj($event_id);  // php will convert it to int
}


$voters = $evt->get_result();
print json_encode($voters);
?>

