<?php
/*
 * The code here handle ajax image upload
 * Referance: https://gist.github.com/ebidel/2410898
 */

session_start();

require_once "../classes/event.php";

$event_id = $_REQUEST['event_id'];
$user = new User(User::name2id($_SESSION["user_name"]));
$evt = $user->get_eventobj($event_id);

$upload_dir = $_SERVER["DOCUMENT_ROOT"] . "/pollpoll2720/choice_img";

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
        $evt->add_option($label, $desc, $img_dest);
}

echo print_r($_FILES);
echo print_r($_REQUEST);
?> 
