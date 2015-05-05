<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require_once "../classes/event.php";

$res = DB::queryFirstRow("SELECT 1 FROM voter "
                                . "WHERE voter_id=%d AND keyVar=%s",
                                $_POST["voter_id"], $_POST["key"]); 
if ($res == NULL) {
        exit("Incorrect voter Info");
}
$evt = new Event_voter($_POST["voter_id"]);

$evt->vote($_POST["choice_id"]);

echo "voted";
