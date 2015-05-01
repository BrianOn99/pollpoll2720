<?php
	//Set up paramaters to connect to db
	$host='localhost';
	$user="root";
	$password="";	
	$db="pollpoll";
	//Attempt connection to database
	$con = mysqli_connect($host,$user,$password,$db) or die('Error with SQL connect');
	
	//Get posted choiceId and voterId from HTTP request
	$votedChoice = $_POST['choiceId'] or exit(); 												
	$voterId = $_POST["voterId"] or exit();
	//Check to make sure no SQL injection
	if(!is_numeric($voterId) OR !is_numeric(votedChoice))
	{
		die('Posted data must be numeric');
	}
	
	//Prepare SQL querries
	$incementVoteCount = "UPDATE choice SET vote_count= vote_count+1 WHERE choice_id=".$votedChoice;
	$voterVoted = "UPDATE voter SET voted=1 WHERE voter_id=".$voterId;
	
	//Following is a transaction to vote and change voter value voted to true
	//Transaction ensures data integrity in case of error mid operation
	mysqli_query($con,"BEGIN");		//Being
	$sql1 = @mysqli_query($con,$incementVoteCount);
	$sql2 = @mysqli_query($con,$voterVoted);
	if($sql1 AND $sql2)
	{
		@mysqli_query($con,"COMMIT");//Commit
	}	
	else
	{
		@mysqli_query($con,"ROLLBACK");	//Rollback
		die('Unable to vote.');
	}
	
	//Take voter back to voter page
	header('Location: voter.php?voterId='.$voterId);
?>