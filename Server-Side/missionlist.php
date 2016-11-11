<?php
// Start a session feed
session_start();
if (!isset($_SESSION["userId"]))
	{
	header("Location: index.php");
	die();
	}
	
// Set Database Connection
include 'dbconnector.php';
$MyId = $_SESSION["userId"];
	
// If we just checked out the missions, set our Check Mission state to null.
$ResetMissionCheckCounter = "UPDATE `hvzuserstate` SET `checkmissions`= 0 WHERE `userid` = $MyId";
$MissionResults = mysqli_query($DBCon, $ResetMissionCheckCounter);
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Keene State: Humans VS Zombies</title>
		<link rel="shortcut icon" type="image/x-icon" href="Images/favicon.ico">
		<link href="CSS/general.css" rel="Stylesheet" type="text/css">
		<link href="CSS/mission.css" rel="Stylesheet" type="text/css">		
	</head>
	<body>
	<?php
	
	// TopBar + Log Out
	include 'pagetopper.php';
	
	// Get Mission info
	$GetMissionQuery = "SELECT AllMissions.`missionId`,`missionState`,`missionHumanTitle`,`missionZombieTitle`,`missionHumanText`,`missionZombieText`,`missionPostHumanText`,`missionPostZombieText`,`ismisprimary` FROM (SELECT * FROM `hvzgamemissions` WHERE `gameId` = $UserGame) AS AllMissions LEFT JOIN (SELECT * FROM  `hvzmissionplayerassoc` WHERE  `playerID` = $MyId) AS AllRelAssocs ON AllMissions.`missionId` = AllRelAssocs.`missionID` WHERE `gameId` = $UserGame AND  `missionState` > 0 AND (missionSpecificPlayers = 0 OR (missionSpecificPlayers = 1 AND playerID = $MyId)) ORDER BY `missionId` DESC";
	$MissionResults = mysqli_query($DBCon, $GetMissionQuery);
	
	if ($MissionResults->num_rows>0)
		{
		echo "<div class=\"MissionList\">";
		while($MissionRow = mysqli_fetch_array($MissionResults))
			{
			$MissionId = $MissionRow[0];
				
			// Get All Reltated Tags:
			$GetRelevantTagsQuery = "SELECT `taggedid`,`usrname`,`usravy` FROM `hvzmissionstagassoc` LEFT JOIN `hvztags` ON `hvzmissionstagassoc`.`tagid` =  `hvztags`.`tagid` LEFT JOIN `hvzusrinfo` ON `taggedid` = `hvzusrinfo`.`usrid` WHERE `missionid` = $MissionId";
			$RelevantTags = mysqli_query($DBCon, $GetRelevantTagsQuery);
			
			echo "<div class=\"MissionInfo\"";
			if ($MissionRow[8] == 0)
				{
				echo " style=\"background-color: #BCE4E5; color: #1C638E; border-color: #80A1B6;\"";
				}
			echo ">";
			
			// Mission Over (Tie)
			if ($MissionRow[1] == 2)
				{
				echo "<div class=\"MissOver\" style=\"background-color: white; color:black;";
				if ($MissionRow[8] == 0)
					{
					echo "background-color: #BCE4E5; color: #1C638E; border-color: #80A1B6;";
					}
				echo "\">";
				echo "Mission Over";
				echo "</div>";
				}
			// Mission Over - Zombies Win
			else if ($MissionRow[1] == 3)
				{
				echo "<div class=\"MissOver\" style=\"background-color: #A9C398; color: #4B721D;";
				if ($MissionRow[8] == 0)
					{
					echo "color: #1C638E; border-color: #80A1B6;";
					}
				echo "\">";
				echo "Zombie Victory";
				echo "</div>";
				}
			// Mission Over - Humans Win
			else if ($MissionRow[1] == 4)
				{
				echo "<div class=\"MissOver\" style=\"background-color: #E0AA0F; color: black;";
				if ($MissionRow[8] == 0)
					{
					echo "color: #3B6E8F; border-color: #80A1B6;";
					}
				echo "\">";
				echo "Human Victory";
				echo "</div>";
				}
			
			if ($UserTeam > 1)
				{
				echo "<div class=\"MissTitle\"";
				// Secondary missions look special.
				if ($MissionRow[8] == 0)
					{
					echo " style=\"background-color: #BCE4E5; color: #1C638E; border-color: #80A1B6;\"";
					}
				echo ">" . $MissionRow[2] . "</div>";
				
				if ($MissionRow[8] == 0 && $MissionRow[1] == 1)
					{
					echo "<p class=\"SecMission\">This is a secondary mission.<br><br>Particepation is not mandatory, but there may be perks for successfuly completing this mission.</p>";
					}
				
				if ($MissionRow[1] == 1)
					{
					echo "<p class=\"MissDesc\">" . nl2br($MissionRow[4]) . "</p>";
					}
				else
					{
					echo "<p class=\"MissDesc\">" . nl2br($MissionRow[6]) . "</p>";
					}
				if ($RelevantTags->num_rows>0)
					{
					echo "<div class=\"LossDesc\"";
					// Secondary missions look special.
					if ($MissionRow[8] == 0)
						{
						echo " style=\"background-color: #BCE4E5; color: #1C638E; border-color: #80A1B6;\"";
						}
					echo ">Loses:</div>";
					echo "<div class=\"LosDiv\"";
					// Secondary missions look special.
					if ($MissionRow[8] == 0)
						{
						echo " style=\"background-color: #BCE4E5; color: #1C638E; border-color: #80A1B6;\"";
						}
					echo ">";
					}
				}
			else if ($UserTeam < 2)
				{
				echo "<div class=\"MissTitle\"";
				// Secondary missions look special.
				if ($MissionRow[8] == 0)
					{
					echo " style=\"background-color: #BCE4E5; color: #1C638E; border-color: #80A1B6;\"";
					}
				echo ">" . $MissionRow[3] . "</div>";
			
				if ($MissionRow[8] == 0 && $MissionRow[1] == 1)
					{
					echo "<p class=\"SecMission\">This is a secondary mission.<br><br>Particepation is not mandatory, but there may be perks for successfuly completing this mission.</p>";
					}
			
				if ($MissionRow[1] == 1)
					{
					echo "<p class=\"MissDesc\">" .  nl2br($MissionRow[5]) . "</p>";
					}
				else
					{
					echo "<p class=\"MissDesc\">" .  nl2br($MissionRow[7]) . "</p>";
					}
				if ($RelevantTags->num_rows>0)
					{
					echo "<div class=\"LossDesc\"";
					// Secondary missions look special.
					if ($MissionRow[8] == 0)
						{
						echo " style=\"background-color: #BCE4E5; color: #1C638E; border-color: #80A1B6;\"";
						}
					echo ">Promotions:</div>";
					echo "<div class=\"LosDiv\"";
					// Secondary missions look special.
					if ($MissionRow[8] == 0)
						{
						echo " style=\"background-color: #BCE4E5; color: #1C638E; border-color: #80A1B6;\"";
						}
					echo ">";
					}
				}
			// Get Tags set up
			while($TagsRow = mysqli_fetch_array($RelevantTags))
				{
				echo "<div class=\"TagShowDiv\">";
				echo "<a href=\"playerstats.php?profId=" . $TagsRow[0] . "\">";
				if ($TagsRow[2] != "")
					{
					echo "<img class=\"TaggedAvy\" src=\"Images\Avatars\\" . $TagsRow[2] .  "\">";
					}
				else 
					{
					echo "<img class=\"TaggedAvy\" src=\"Images\DefaultAvatars\DefaultZombieAv.png\">";
					}
				echo "<p class=\"TaggedName\">" . $TagsRow[1] . "</p>";
				echo "</div>";
				}	
				if ($RelevantTags->num_rows>0)
					{
					echo "</div>";
					}
				echo "</a>";
			echo "</div>";
			}
		echo "</div>";
		}
	// No missions are shown.
	else
		{
		echo "<div class=\"NoMissions\">";
		echo "There are currently no Missions listed in connection to your current game.<br><br>But be ready, as some will be posted soon.";
		echo "</div>";
		}
	?>
	</body>
</html>