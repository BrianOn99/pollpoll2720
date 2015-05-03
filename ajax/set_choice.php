<?php
/*
 * The code here handle ajax image upload
 * Referance: https://gist.github.com/ebidel/2410898
 */

$upload_dir = "../choice_img";

$fileName = $_FILES['afile']['name'];
$fileType = $_FILES['afile']['type'];
$fileContent = file_get_contents($_FILES['afile']['tmp_name']);
$dataUrl = 'data:' . $fileType . ';base64,' . base64_encode($fileContent);
$json = json_encode(array(
    'name' => $fileName,
    'type' => $fileType,
    'dataUrl' => $dataUrl,
));

$desc = $_REQUEST['choice-desc'];
$event_id = $_REQUEST['event_id'];

function rand_string($length = 10) {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

$name = rand_string();
$dest = "$upload_dir/$name";
move_uploaded_file($_FILES['afile']['tmp_name'], $dest);


echo print_r($_FILES);
echo $desc;
echo $event_id;
echo $json;
?> 
