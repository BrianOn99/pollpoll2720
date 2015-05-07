<?php
/*
 * The code here handle ajax image upload
 * Referance: https://gist.github.com/ebidel/2410898
 */

session_start();

require_once "../classes/event.php";

$user = new User(User::name2id($_SESSION["user_name"]));

// the 2nd parameter true let us assess data as assoc array
$data = json_decode($_REQUEST["metadata"], true);  

$evt = $user->create_event(array(
        "title" => htmlspecialchars($data["title"]),
        "description" => htmlspecialchars($data["desc"]),
        "event_type" =>  htmlspecialchars($data["type"]),
        "start_time" => htmlspecialchars($data["start"]),
        "end_time" =>  htmlspecialchars($data["end"])));

/* event is created, now add choices */

$img_dir = "/pollpoll2720/choice_img";
$upload_dir = $_SERVER["DOCUMENT_ROOT"] . $img_dir;

$choices_info = json_decode($_REQUEST['choices_info']);

function rand_string($length = 10) {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

foreach ($choices_info as $label => $desc) {
        $tmp = $_FILES[$label]["tmp_name"];
        $name = rand_string();
        $img_dest = "$upload_dir/$name";
        move_uploaded_file($tmp, $img_dest);
        
        echo "$label $desc $img_dest\n";
        $evt->add_option($label, htmlspecialchars($desc), "$img_dir/$name");
}

?> 
