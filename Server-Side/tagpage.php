<?php
// Start a session feed
session_start();
if (!isset($_SESSION["userId"]))
	{
	header("Location: index.php");
	die();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Keene State: Humans VS Zombies</title>
		<link rel="shortcut icon" type="image/x-icon" href="Images/favicon.ico">
		<link href="CSS/general.css" rel="Stylesheet" type="text/css">
		<link href="CSS/gametags.css" rel="Stylesheet" type="text/css">
	</head>
	<body>
<?php
	// TopBar + Log Out
	include 'pagetopper.php';
	
	if ($UserGame == 0)
		{
		header("Location: joingame.php");
		die();
		}
	
	if (isset($_POST["LoadError"]))
		{
		echo "<div class=\"ErrorDiv\">";
		switch ($_POST["LoadError"])
			{
			case 1:
			echo "You may not register the tags at this moment as the game either has not begun or is on pause. Please try again at later time.";
			break;
			case 2:
			echo "Given tag was not associated with any player.<br>Please do not try to guess other player's tags or actions will be taken.";
			break;
			}
		echo "</div>";
		}
	
	echo "<div class=\"TagDiv\">";
	// If we are human
	if ($UserTeam > 1 && $UserTeam != 5)
		{
		$GetTagCode ="SELECT `tagcode` FROM `hvztagnums` WHERE `userId`=$MyId";
		$PlayerCode = mysqli_fetch_row(mysqli_query($DBCon, $GetTagCode));
	
		echo "<p class=\"YourText\">Your Tag Code:</p>";
		echo "<p class=\"InstText\">Write down this code on a piece of paper or<br>a notecard. Surrender it when you are tagged.<br>None of the codes contain S, Z, Q, or letter O<br></p>";
		
		echo "<div class=\"TagCard\">";
			echo "<p class=\"TagText\">";
			echo $PlayerCode[0];
			echo "</p>";
		echo "</div>";
		}
	// Or if we are a zombie
	else if ($UserTeam < 2 || $UserTeam == 5)
		{
		$ZmabieListing = "<option value=\"0\"></option>";
		$GetAllLiveZombiesQuery = "SELECT `usrid`,`usrname` FROM `hvzuserstate` LEFT JOIN `hvzusrinfo` ON `hvzuserstate`.`userid`=`hvzusrinfo`.`usrid` WHERE (`userteam` < 2 AND `userteam` > -1) AND `usergame` = $UserGame AND NOT `usrid` = $MyId";
		$ZmabiesResult = mysqli_query($DBCon, $GetAllLiveZombiesQuery);
		while($row = mysqli_fetch_array($ZmabiesResult))
			{
			$ZmabieListing = $ZmabieListing . "<option value=\"" . $row[0] . "\">" . $row[1] . "</option>";
			}
			
		$MissionListing = "<option value=\"0\">None</option>";
		$GetGameMissionsQuery = "SELECT `missionId`,`missionZombieTitle` FROM `hvzgamemissions` WHERE `gameId` = $UserGame AND `missionState` > 0 AND `missionZombieTitle` != ''";
		$MissionResult = mysqli_query($DBCon, $GetGameMissionsQuery);
		while($MissionRow = mysqli_fetch_array($MissionResult))
			{
			$MissionListing = $MissionListing . "<option value=\"" . $MissionRow[0] . "\">" . $MissionRow[1] . "</option>";
			}
		
		echo "<form action=\"tagregistrar.php\" name=\"codeentry\" method=\"post\">";
			echo "<p class=\"TagTxt\">Player's<br>Tag Code:</p>";
			echo "<input type=\"text\" name=\"TagCode\" value=\"\" maxlength=\"8\" class=\"TagCodeInput\" autocomplete=\"off\">";
			echo "<p class=\"FeedTxt\">You may feed up to 3 other zombies who were with you<br>at the moment of the tag:</p>";
			echo "<select class=\"Zomb1List\" name=\"Zomb1\">";
			echo $ZmabieListing;
			echo "</select>";
			echo "<select class=\"Zomb2List\" name=\"Zomb2\">";
			echo $ZmabieListing;
			echo "</select>";
			echo "<select class=\"Zomb3List\" name=\"Zomb3\">";
			echo $ZmabieListing;
			echo "</select>";
			echo "<p class=\"AssocText\">Associate the Tag with a Mission: </p>";
			echo "<select class=\"MissionList\" name=\"MissionAssoc\">";
			echo $MissionListing;
			echo "</select>";
			echo "<input type=\"submit\" id=\"report\" name=\"report\" value=\"Report Tag\" class=\"reportBTN\">";
		echo "</form>";
		}
	echo "</div>";
?>
	</body>
</html>