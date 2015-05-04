<html>
<!--Styling is determined here-->
<style>
        
        li
        {
                height: 150px;
                border: 2px inset black;
                padding: 5px;        
                vertical-align: middle;
                list-style-type: none; 
                font-weight: bold;
        }
        .count
        {
                position: absolute;
                right: 10px;
                font-weight: normal;
        }
        
</style>

<?php
include "../_include/head.php";

require_once "../classes/meekroDB/db.class.php";
require_once "../classes/event.php";
require_once "../config/db.php";
 
DB::$user = DB_USER;
DB::$password = DB_PASS;
DB::$dbName = DB_NAME;

//Get voter id from url
$voter_id = @$_GET['voterId'] or die('Invalid URL');
$key = @$_GET['key'] or die('Invalid URL');

//Get event_id by searching voter table and process result
$voter_info = DB::queryFirstRow("SELECT event_id, voted_choice_id, name FROM voter "
                                . "WHERE voter_id=%d AND keyVar=%s", $voter_id, $key); 
if ($voter_info == NULL) {
        exit("Incorrect voter Info");
}

$eventId = $voter_info['event_id'];
$voterName = $voter_info['name'];
$voted = $voter_info['voted_choice_id'];

//Get event details by searching poll_event table and process result
$poll = DB::queryFirstRow("SELECT * FROM poll_event WHERE event_id=%d", $eventId);
$pollTitle = $poll['title'];
$pollDescr = $poll['description'];
$pollType = $poll['event_type'];
$pollStart = $poll['start_time'];
$pollEnd = $poll['end_time'];


//Dynamically start building page 
?>

<body>
<h1>Thank you, <?=$voterName?> for visiting poll: <?=$pollTitle?></h1>;
<p> You have from <?=$pollStart?> until <?=$pollEnd?> to cast your vote </p>;
<h2><?=$pollDescr?></h2>;

<?php
//Check if poll is still running and if voter has not yet voted 
$time = strtotime($pollEnd);
$curtime = time();

if(($curtime-$time) < 0 AND !$voted)
{
        $my_event = new Event_voter($voter_id);

        $choices = $my_event->get_choices();

        //Start building form
        echo "<form action='submitVote.php' method='post'>";
        //We use a hidden field to send voterId - we do this instead of using
        //sessions to allow multiple voters at once on one machine

        //While there are more choices, loop
?>
        <div>
<?php
        foreach($choices as $row)
        {
                $img_elm = ($row["image_url"]==NULL) ? "" : "<img src=\"{$row['image_url']}\" width='100' />"; 
                $input_elm = '<input type="radio" name="choiceId" class="choice"' . " data-value={$row['choice_id']} />";
?>
  <div class="col-lg-6">
    <div class="input-group">
      <span class="input-group-addon">
       <?=$input_elm?>
      </span>
      <p><?=$row['description']?></p><?=$img_elm?>
    </div><!-- /input-group -->
  </div><!-- /.col-lg-6 -->
<?php
        }
        echo '<input type="submit" value="Vote"></form>';
        echo '</div>';
}
else //This means voter had voted or event is over
{
        echo "do ajax";
}
?>
</body>
</html>

