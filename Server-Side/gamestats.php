<?php
// Start a session feed
session_start();
// If we do not have a set user id, we're probably not loged in. Kick us out to Login page.
if (!isset($_SESSION["userId"]))
	{
	header("Location: index.php");
	die();
	}
	
// Connect to the Database for the first time.
include 'dbconnector.php';

// Get Our team.
$GetOurGame = "SELECT `usergame` FROM `hvzuserstate` WHERE `userid` = " . $_SESSION["userId"] . "";
$GameCheck = mysqli_fetch_array(mysqli_query($DBCon, $GetOurGame))[0];

if ($GameCheck == 0)
	{
	header("Location: playerstats.php");
	die();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Keene State: Humans VS Zombies</title>
		<link rel="shortcut icon" type="image/x-icon" href="Images/favicon.ico">
		<link href="CSS/gamestats.css" rel="Stylesheet" type="text/css">
		<link href="CSS/general.css" rel="Stylesheet" type="text/css">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="JS/gamestats.js"></script>
	</head>
	<?php
	$ChatLoad = 1;
	
	// TopBar + Log Out
	include 'pagetopper.php';
		
	// This is a chat window where everything is shown.
	echo "<div class=\"ChatWindow\" id=\"BoxAChat\">";
		
	echo "</div>";
	
	$GetChatStateQuery = "SELECT `ChatState` FROM `hvzusers` WHERE `usrID` = $MyId";
	$ChatState = mysqli_fetch_row(mysqli_query($DBCon, $GetChatStateQuery))[0];
	
	// Do not show chat input if we are banned from it.
	if ($ChatState != 1)
		{
		// Based on the user's team, it sends to a different chat log to avoid cross-contamination.
		if ($UserTeam < 2  && $UserTeam > -1)
			{
			echo "<input type=\"text\" class=\"ChatInput\" id=\"chattextinput\" onkeypress=\"ButtonPressed(event,'Zombies',$UserGame)\">";
			echo "<input type=\"submit\" value=\"Send\" class=\"ChatButton\" onclick=\"SendChatMessage('Zombies',$UserGame)\">";
			}
		else if ($UserTeam > 1)
			{
			echo "<input type=\"text\" class=\"ChatInput\" id=\"chattextinput\" onkeypress=\"ButtonPressed(event,'Humans',$UserGame)\">";
			echo "<input type=\"submit\" value=\"Send\" class=\"ChatButton\" onclick=\"SendChatMessage('Humans',$UserGame)\">";
			}
		}
		
	// Get the Tag info: Tag and tag/tagee info.
	$GetTagsQuery = "SELECT `hvztags`.`tagid` ,`hvztags`.`tagdate` AS TagDate, `hvztags`.`tagerid` AS TagerID, `hvzuserstate`.`userteam` AS TagerTeam, `hvzusrinfo`.`usrname` AS TaggerName, `hvzusrinfo`.`usravy` AS TagerAvatar, `hvztags`.`taggedid` AS TagedID, TagedInfo.`usrname` AS TagedName, TagedInfo.`usravy` AS TagedAvatar, `hvztags`.`taggameid`, `hvzgamemissions`.`missionHumanTitle`, `hvzgamemissions`.`missionZombieTitle` FROM `hvztags` LEFT JOIN `hvzusrinfo` ON `hvztags`.`tagerid` = `hvzusrinfo`.`usrid` LEFT JOIN `hvzusrinfo` AS TagedInfo ON `hvztags`.`taggedid` = TagedInfo.`usrid` LEFT JOIN `hvzuserstate` ON TagerID = `hvzuserstate`.`userid` LEFT JOIN `hvzmissionstagassoc` ON `hvzmissionstagassoc`.`tagid` = `hvztags`.`tagid` LEFT JOIN `hvzgamemissions` ON `hvzmissionstagassoc`.`missionid` = `hvzgamemissions`.`missionId` WHERE `taggameid` = $UserGame ORDER BY `hvztags`.`tagid` DESC LIMIT 30";
	$TagsInfo = mysqli_query($DBCon, $GetTagsQuery);

	// Get the list of players for both sides.
	$GetZmabies = "SELECT `userid`,`userteam`,`usrname`,`usravy` FROM `hvzuserstate` LEFT JOIN `hvzusrinfo` ON `hvzuserstate`.`userid` = `hvzusrinfo`.`usrid` WHERE `usergame` = $UserGame AND `userteam` < 2 AND `userteam` > -1 ORDER BY RAND()";
	$GetHummies = "SELECT `userid`,`userteam`,`usrname`,`usravy` FROM `hvzuserstate` LEFT JOIN `hvzusrinfo` ON `hvzuserstate`.`userid` = `hvzusrinfo`.`usrid` WHERE `usergame` = $UserGame AND `userteam` > 1 ORDER BY RAND()";
	
	// Get the list as query results.
	$ZmabieList = mysqli_query($DBCon, $GetZmabies);
	$HummieList = mysqli_query($DBCon, $GetHummies);
	
	// Begin printng out a list of players.
	echo "<p class=\"ListTopHum\">Humans: " . $HummieList->num_rows . "</p>";
	echo "<div class=\"HumList\">";
	while($row = mysqli_fetch_array($HummieList))
		{
		echo "<div class=\"ListRow\">";
		echo "<a href=\"playerstats.php?profId=" . $row[0] . "\">";

		// Use default avatar, if they don't have one set.
		if ($row[3] == null)
			{
			echo "<img class=\"ListAvy\" src=\"Images/DefaultAvatars/DefaultHumanAv.png\">";
			}
		// If Avatar is set:
		else
			{
			echo "<img class=\"ListAvy\" src=\"Images/Avatars//" . $row[3] .  "\">";
			}
		// If whoever is viewing is not human, they should not know who is a merc and who is not.
		if ($UserTeam > 1 && $row[1]==2 && $UserTeam < 5)
			{
			echo "<img class=\"MercSign\" src=\"Images/PlayerStates/mercmark.png\">";
			echo "<p class=\"MercLook\">" . $row[2] . "</p></a>";
			}
		else
			{
			echo "<p class=\"ListName\">" . $row[2] . "</p></a>";
			}	
		echo "</div>";
		}
	// End human list.
	echo "</div>";
	
	// Begin the tag log.
	echo"<div class=\"TagLog\">";
	// For each tag in the result
	while($row = mysqli_fetch_array($TagsInfo))
		{
		echo "<div class=\"TagBox\">";
		
		// If a tagger is OZ
		if ($row[3] == 5)
			{
			echo "<img class=\"TaggerAvy\" src=\"Images/DefaultAvatars/DefaultOZAv.png\">";
			}
		// Regular Tag, not starvation
		else if ($row[2] != 0)
			{
			echo "<a href=\"playerstats.php?profId=" . $row[2] . "\">";
			// If the tagger has no avatar, use default zombie avatar.
			if ($row[5] == null)
				{
				echo "<img class=\"TaggerAvy\" src=\"Images/DefaultAvatars/DefaultZombieAv.png\">";
				}
			// If they do have an Avatar
			else
				{
				echo "<img class=\"TaggerAvy\" src=\"Images/Avatars//" . $row[5] .  "\">";
				}
			echo "</a>";
			}
			
		// If starvation
		if ($row[2] == 0)
			{
			echo "<a href=\"playerstats.php?profId=" . $row[6] . "\">";
			// No Avatar, use default zombie one.
			if ($row[8] == null)
				{
				echo "<img class=\"TaggedAvy\" src=\"Images/DefaultAvatars/DefaultZombieAv.png\">";
				}
			// If they do have an Avatar
			else
				{
				echo "<img class=\"TaggedAvy\" src=\"Images/Avatars//" . $row[8] .  "\">";
				}
			echo "</a>";
			}
		// Is not NPC Consumption
		else if ($row[6] != 0)
			{
			echo "<a href=\"playerstats.php?profId=" . $row[6] . "\">";
			// If the tagged player has no avatar, use default human avatar.	
			if ($row[8] == null)
				{
				echo "<img class=\"TaggedAvy\" src=\"Images/DefaultAvatars/DefaultHumanAv.png\">";
				}
			// If they do have an Avatar
			else
				{
				echo "<img class=\"TaggedAvy\" src=\"Images/Avatars//" . $row[8] .  "\">";
				}
			echo "</a>";
			}
			
		// Show the time of the tag and names if appropriate
		if ($row[3] == 5)
			{
			echo "<p class=\"TagText\">OZ has";
			}
		else if ($row[2] == 0)
			{
			echo "<p class=\"TagText\">" . $row[7];
			}
		else
			{
			echo "<p class=\"TagText\">" . $row[4];
			}
			
		if ($row[6] != 0 && $row[2] != 0)
			{
			echo " tagged " . $row[7] . " on " . date("F jS", strtotime($row[1])) . " at " . date("g:ia", strtotime($row[1]));
			}
		else if ($row[2] == 0)
			{
			echo " starved on " . date("F jS", strtotime($row[1])) . " at " . date("g:ia", strtotime($row[1]));
			}
		else
			{
			echo " tagged a non-player on " . date("F jS", strtotime($row[1])) . " at " . date("g:ia", strtotime($row[1]));
			}
		if ($row[10] != "")
			{
			echo "<p class=\"MissionTagText\">(Tagged on the mission \"";
			if ($UserTeam > 1)
				{
				echo $row[10];
				}
			else 
				{
				echo $row[11];
				}
			echo "\")</p>";
			}
		echo "</div>";
		}
	echo "</div>";
	
	// Begin Zombie List.
	echo "<p class=\"ListTopZom\">Zombies: " . $ZmabieList->num_rows . "</p>";
	echo "<div class=\"ZomList\">";
	while($row = mysqli_fetch_array($ZmabieList))
		{
		echo "<div class=\"ListRow\">";
		echo "<a href=\"playerstats.php?profId=" . $row[0] . "\">";
		// Default Zmobie Avatar
		if ($row[3] == null)
			{
			echo "<img class=\"ListAvy\" src=\"Images/DefaultAvatars/DefaultZombieAv.png\">";
			}
		// If Avatar is set:
		else
			{
			echo "<img class=\"ListAvy\" src=\"Images/Avatars//" . $row[3] .  "\">";
			}
		echo "<p class=\"ListName\">" . $row[2] . "</p></a>"; 
		echo "</div>";
		}
	// End zombie list.
	echo "</div>";
	?>
	</body>
</html>