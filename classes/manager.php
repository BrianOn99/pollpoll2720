<?php
require_once("../config/db.php");
require_once 'meekrodb.2.3.class.php';
DB::$user = DB_USER;
DB::$password = DB_PASS;
DB::$dbName = DB_NAME;


function create_event(title, desc, type, start_t, end_t)
{
    $row = DB::queryFirstRow("SELECT user_id, user_name FROM users WHERE user_name = 'ham'");

    echo "Name: " . $row['user_id'] . "\n";
    echo "Age: " . $row['user_name'] . "\n";
    echo "-------------\n";
    $user_id = $row['user_id'];

    DB::insert('poll_event', array(
        'user_id' => $user_id,
        'title' => title,
        'description' => desc,
        'event_type' => type,
        'start_time' => start_t,
        'end_time' => end_t));
}

function get_events()
{
    return DB::query('SELECT * FROM poll_event');
}

/*
class Event
{
    private event_id;
    function add_voter;
    function remove;
    function activate;
    function send_email;
    function is_active;
    function remove_event(id)
    {
        DB::delete('poll_event', 'event_id=%d', id);
    }
}
 */


?>
