<?php
// Set Database Connection
include 'dbconnector.php';
$date = date('Y-m-d H:i:s');

// Get Listing of all active games (Running, not paused)
$GetActiveGames = "SELECT `gameId` FROM `hvzgame` WHERE `gameState` = 2";
$ActivGames = mysqli_query($DBCon, $GetActiveGames);

// For each game that is active.
while($Game = mysqli_fetch_array($ActivGames))
	{
	// Current Game
	$CurGame = $Game[0];
	
	// Get all Zombie players in the current game.
	$GetZombies = "SELECT `userid`,`userlastfed`,`userteam` FROM `hvzuserstate` WHERE `usergame` = $CurGame AND (`userteam` = 1 OR `userteam` = 0)";
	$ZomResults = mysqli_query($DBCon, $GetZombies);
	
	// For each zombie found, check them against their hunger.
	while($Zombie = mysqli_fetch_array($ZomResults))
		{
		$CurrentDate = strtotime("now");
		$HungryDate = strtotime($Zombie[1] . "+2 days");
		$ZombID = $Zombie[0];
		$ZombType = $Zombie[2];
		
		// If we are beyond the starvation time, set the player to dead status.
		if ($CurrentDate > $HungryDate)
			{
			// Regular Player
			if ($ZombType == 0)
				{
				$NewType = -1;
				}
			// Admin Zombie
			else if ($ZombType == 1)
				{
				$NewType = -2;
				}
			
			// Set the Query to make the player dead as hell
			$SetPlayerDead = "UPDATE `hvzuserstate` SET `userteam` = $NewType WHERE `userid` = $ZombID";
			// Run the Query
			mysqli_query($DBCon, $SetPlayerDead);
			
			// Set the Query to create a tag notification that the play is dead as hell
			$CreateDeadTag = "INSERT INTO  `keenehvz`.`hvztags` (`tagerid` ,`taggedid` ,`tagdate` ,`taggameid`)VALUES (0, $ZombID, '$date' , $CurGame)";
			// Run the Query
			mysqli_query($DBCon, $CreateDeadTag);
			
			// Create a small event for starving
			$CreateSmallEvent = "INSERT INTO `keenehvz`.`hvzsmallevents` (`evntType`, `evtDate`, `usrSubjctId`, `relevantId`) VALUES (10, '$date', $ZombID, $CurGame)";
			// Run the Query
			mysqli_query($DBCon, $CreateSmallEvent);
			}
		}
	}
	
// Secondary requirement: Check for all folks who forgot password, and unset the "Forgotten" boolean if it was set too long ago.
$GetForgottenPasses = "SELECT `usrID`,`usrForgotSetDate` FROM `hvzusers` WHERE `usrForgotPass` = 1";
$ForgottenPassUsers = mysqli_query($DBCon, $GetForgottenPasses);

// For each user with Forgotten Password set
while($ForgottenUser = mysqli_fetch_array($ForgottenPassUsers))
	{
	// When should the Forget Option be over?
	$EndDate = strtotime($ForgottenUser[1] ."+1 days");
	// Current Time/Date
	$CurrentDate = strtotime("now");
	// If we surpassed the day after setting password, set as not have forgotten it.
	if ($EndDate < $CurrentDate)
		{
		$ForgettingUser = $ForgottenUser[0];
		$UnforgetQuery = "UPDATE `hvzusers` SET `usrForgotPass`=0,`usrForgotSetDate`=NULL,`usrForgotConfirm`=NULL WHERE `usrID` = $ForgettingUser";
		mysqli_query($DBCon, $UnforgetQuery);
		}
	}
// Die to stop resource waste.
die();
?>