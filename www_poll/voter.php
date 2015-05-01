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
	//Include head.php for consistent appearance of web application
	include "../_include/head.php";
	//Set up paramaters to connect to db
	$host='localhost';
	$user="root";
	$password="";	
	$db="pollpoll";
	//Attempt connection to database or die with error message for users
	$con = @mysqli_connect($host,$user,$password,$db) or die('Sorry something went wrong with database connection'); 
	
	//Get voter id from url
	$voterId = @$_GET['voterId'] or die('No user registered'); 												
	//Check to make sure no SQL injection
	if(!is_numeric($voterId))
	{
		die('VoterId must be numeric');
	}
	//Get event_id by searching voter table and process result
	$q4PollId = "SELECT event_id,voted,name FROM voter WHERE voter_id=".$voterId; 
	$result = @mysqli_query($con, $q4PollId);
	$raw = @mysqli_fetch_array($result) OR die('Error retrieving data');
	$eventId = $raw['event_id'];
	$voterName = $raw['name'];
	$voted = $raw['voted'];
	@mysqli_free_result($result);
	
	//Get event details by searching poll_event table and process result
	$q4Poll = "SELECT * FROM poll_event WHERE event_id=".$eventId;
	$result = @mysqli_query($con, $q4Poll);
	$poll = mysqli_fetch_array($result) OR die('Error retrieving data');
	$pollTitle = $poll['title'];
	$pollDescr = $poll['description'];
	$pollType = $poll['event_type'];
	$pollStart = $poll['start_time'];
	$pollEnd = $poll['end_time'];
	
	//Dynamically start building page 
	echo "<h1>Thank you,".$voterName." for visiting poll: ".$pollTitle."</h1>";
	echo "<h2>".$pollDescr."</h2>";
	echo "<p> You have from ".$pollStart." until ".$pollEnd." to cast your vote </p>";
	
	//Check if poll is still running and if voter has not yet voted 
	$time = strtotime($pollEnd);
	$curtime = time();
	if(($curtime-$time) < 0 AND !$voted)
		{
				$q4Choices = "SELECT choice_id,description,vote_count,image_url FROM choice WHERE event_id=".$eventId;
				$result = @mysqli_query($con, $q4Choices);
				
				//Start building form
				echo "<form action='submitVote.php' method='post'>";
				//We use a hidden field to send voterId - we do this instead of using sessions to allow multiple voters at once on one machine
				echo "<input type='hidden' name='voterId' value=".$voterId.">";
				
				//While there are more choices, loop
 				while($row = mysqli_fetch_array($result)) 
				{
					//If event type is secret vote
					if($pollType != 1)
					{
						//Display only image, description 
						$url = ($row["image_url"]==NULL)? "":"<img src=".$row['image_url']." width = '100'></img>"; 
						echo "<li><input type ='radio' name='choiceId' class = 'choice' value=".$row['choice_id'].">".$row["description"].$url."<div class='count'>Runnins Vote count: ".$row["vote_count"]."</div></li>";
					}
					else //Executed when event type is poll
					{
						//Display image, description and running vote tally
						$url = ($row["image_url"]==NULL)? "":"<img src=".$row['image_url']." width = '100'></img>"; 
						echo "<li><input type ='radio' name='choiceId' class = 'choice' value=".$row['choice_id'].">".$row["description"].$url."</li>";
					}		
				}
				//Create submit button
				echo "<input type='submit' value='Vote'></form>	";	
		}
		else //This means voter had voted or event is over
		{
			//Get choices
			$q4Choices = "SELECT choice_id,description,vote_count,image_url FROM choice WHERE event_id=".$eventId;
			$result = @mysqli_query($con, $q4Choices);
			//For each choice, display all
			while($row = mysqli_fetch_array($result)) 
				{
						$url = ($row["image_url"]==NULL)? "":"<img src=".$row['image_url']." width = '100'></img>"; 
						echo "<li>".$row["description"].$url."<div class='count'>Final Vote count: ".$row["vote_count"]."</div></li>";
				}
		}
		mysqli_close($con);
?>
</html>

