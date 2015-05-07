<?php
/*
 * This return the vote count for an event
 * It may be used by voter or manager
 */

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

/* This design pattern is bad! */
/* The Event_manager class define an interface that user can get in any case, and we restrict its
 * use here.  We should have done the restriction in the class itself.
 * But it require parent method, raise error, etc.  If I were using Python/Ruby,
 * I would be happy doing so.  I don't want to know more about PHP.
 */

$info = $evt->info();

if (($info["event_type"] == 1) && ($info["end_time"] > time())){
    header("HTTP/1.1 409 Event has not ended");
    exit("event not ended");
}

$voters = $evt->get_result();
print json_encode($voters);
?>

