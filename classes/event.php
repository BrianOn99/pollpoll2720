<?php

/*
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
 */

require_once "meekrodb.2.3.class.php";
require_once "../config/db.php";

DB::$user = DB_USER;
DB::$password = DB_PASS;
DB::$dbName = DB_NAME;

date_default_timezone_set('Asia/Hong_Kong');

class User
{
        protected $u_id;

        static function name2id($name)
        {
                $row = DB::queryFirstRow("SELECT user_id, user_name "
                                           . "FROM users WHERE user_name = %s",
                                           $name);
                if ($row == NULL) {
                        throw new Exception("no user name $u_id");
                }
                $uid = $row['user_id'];
                return $uid;
        }

        function __construct($u_id)
        {
                $row = DB::queryFirstRow("SELECT 1 "
                                           . "FROM users WHERE user_id = %d",
                                           $u_id);
                if ($row == NULL) {
                        throw new Exception("no user id $u_id");
                }
                $this->u_id = $u_id;
        }

        function get_eventobj($e_id)
        {
                return new Event_manager($this->u_id, $e_id);
        }

        function list_events()
        {
                return DB::query("SELECT * FROM poll_event WHERE user_id=%d", $this->u_id);
        }

        /* the passed time should be unix timestamp */
        function create_event($event_info)
        {
                $event_info["user_id"] = $this->u_id;
                $event_info["start_time"] = date('Y-m-d H:i:s', $event_info["start_time"]);
                $event_info["end_time"] = date('Y-m-d H:i:s', $event_info["end_time"]);
                DB::insert('poll_event', $event_info);
                return new Event_manager($this->u_id, DB::queryFirstField("SELECT LAST_INSERT_ID()"));
        }
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

        function get_options() {
                return DB::query("SELECT choice_id, description, image_url"
                        . " FROM choice WHERE event_id=%d",
                        $this->e_id);
        }

        function get_result() {}
}

/* it is read as event object for manager, not event manager */
class Event_manager extends Event_base
{
        protected $u_id;

        function __construct($u_id, $e_id)
        {
                parent::__construct($e_id);
                $this->u_id = $u_id;
        }

        function remove()
        {
                DB::delete('poll_event', 'event_id=%d', $this->e_id);
        }

        function add_option($opt) {
                DB::insert("choice", array(
                        "event_id" => $this->e_id,
                        "image_url" => $opt["img"],
                        "description" => $opt["desc"],
                        "vote_count" => 0));
        }

        function set_voters($voters) {
                $this->clear_voters();
                foreach ($voters as $v) {
                        /* seed the random number generater, so the key cannot 
                         * be guessed. key related part should be moved to 
                         * acitvate method */
                        srand(time());

                        DB::insert("voter",  array(
                                "event_id" => $this->e_id,
                                "keyVar" => hash("sha256", rand()),
                                "name" => $v["name"],
                                "email" => $v["email"],
                                "voted" => False));
                }
        }

	function get_voters() {
                return DB::query("SELECT name, email FROM voter WHERE event_id=%d",
				$this->e_id);
	}

        function clear_voters() {}
        function is_active() {}
        function activate() {}
        protected function send_email() {}
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
