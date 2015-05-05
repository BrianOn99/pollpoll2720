<html>

<?php
include "../_include/voter_head.php";

require_once "../classes/event.php";

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
$poll_start = $poll['start_time'];
$poll_end = $poll['end_time'];
$is_active = 1;


function voting_page($voter_id)
{
        $my_event = new Event_voter($voter_id);

        $choices = $my_event->get_choices();

        //Start building form
        //We use a hidden field to send voterId - we do this instead of using
        //sessions to allow multiple voters at once on one machine
        ?>
        <form action="submitVote.php" method="post">
        <div>
        <?php
        foreach($choices as $row)
        {
                $img_elm = ($row["image_url"]==NULL) ? "" :
                        "<img src=\"{$row['image_url']}\" class=\"choice\"/>"; 
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
        ?>
        <input type="submit" value="Vote"></form>
        </div>
        <?php
}

function show_result($event_id, $voter_id, $key) {
        ?>
        <div id="graph"></div>
        <script src="../js/render_result.js"></script>
        <script>
        renderResult("graph", <?=$event_id?>, <?=$voter_id?>, "<?=$key?>");;
        </script>
        <?php
}
?>

<?php
//Dynamically start building page 
?>

<body>
<div class="container">
<h1>Thank you, <?=$voterName?> for visiting poll: <?=$pollTitle?></h1>
<h2><?=$pollDescr?></h2>

<?php
//Check if poll is still running and if voter has not yet voted 
$end_time = strtotime($poll_end);
$start_time = strtotime($poll_start);
$curtime = time();

$has_started = ($curtime-$start_time) > 0;
$has_ended = ($curtime-$end_time) > 0;

if (!($has_started && $is_active)) {
        echo "Not yet votable";
} elseif ($has_ended) {
        show_result($eventId, $voter_id, $key);
} elseif (!$voted) {
        echo "<p> You have from $poll_start until $poll_end to cast your vote </p>";
        voting_page($voter_id);
} else {
        if ($pollType == "1") {
                echo "Come back to see result when the event has ended";
        } elseif ($pollType == "2") {
                show_result($eventId, $voter_id, $key);
        }
        
}
?>

</div>
</body>
</html>

