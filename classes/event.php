<?php
require_once("../config/db.php");
require_once 'meekrodb.2.3.class.php';
DB::$user = DB_USER;
DB::$password = DB_PASS;
DB::$dbName = DB_NAME;

function get_events()
{
    return DB::query('SELECT * FROM poll_event');
}

class Event_base
{
    protected $e_id;  // event id

    function __construct($e_id)
    {
        $this->e_id = $e_id;
    }

    function info()
    {
            return DB::queryFirstRow("SELECT title, description, event_type,"
                    . "UNIX_TIMESTAMP(start_time) start_time,"
                    . "UNIX_TIMESTAMP(end_time) end_time"
                    . " FROM poll_event WHERE event_id=%d", 
                    $this->e_id);
    }

    function get_result() {}
}

/* it is read as event object for manager, not event manager */
class Event_manager extends Event_base
{
    protected $user_id;
    protected function name2id($name)
    {
        $row = DB::queryFirstRow("SELECT user_id, user_name FROM users WHERE user_name = 'ham'");
        $uid = $row['user_id'];
        echo "id: " . $row['user_id'] . "\n";
        echo "name: " . $row['user_name'] . "\n";
        echo "-------------\n";
        return $uid;
    }

    function __construct($user_name, $e_id)
    {
        parent::__construct($e_id);
        $this->user_id = self::name2id($user_name);;
    }

    /* the passed time should be unix timestamp */
    public static function create($name, $title, $desc, $type, $start_t, $end_t)
    {
        echo $name, ' ', $type, ' ', $start_t;
        DB::insert('poll_event', array(
            'user_id' => self::name2id($name),
            'title' => $title,
            'description' => $desc,
            'event_type' => $type,
            'start_time' => date('Y-m-d H:i:s', $start_t),
            'end_time' => date('Y-m-d H:i:s', $end_t)));
        return new Event_manager($name, DB::queryFirstField("SELECT LAST_INSERT_ID()"));
    }


    function remove()
    {
        DB::delete('poll_event', 'event_id=%d', $this->e_id);
    }

    function is_active() {}
    function activate() {}
    protected function send_email() {}
    function get_voters() {}
    function update_voters() {}
}

class Event_voter extends Event_base
{
    function __construct($voter_id, $e_id)
    {
        parent::__construct($e_id);
        $this->voter_id = $voter_id;
    }

    function vote() {}
}

?>
