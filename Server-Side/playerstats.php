<?php
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
		<link href="CSS/profile.css" rel="Stylesheet" type="text/css">
		<link href="CSS/general.css" rel="Stylesheet" type="text/css">
	</head>
	<body>
	<?php
	// TopBar + Log Out
	include 'pagetopper.php';
	
	// If Profile ID is not set, it's ours.
	if (!isset($_GET["profId"]))
		{
		$ProfileId = $MyId;
		}
	// If Profile ID is set, it's someone else.
	else
		{
		$ProfileId = $_GET["profId"];
		}
	
	$GetProfileInfoQuery = "SELECT `userid`,`userteam`,`userlastfed`,`usrname`,`usrdesc`,`usravy`, `usrgroupliveId`, `usrgrouplivetitle`, `usrgroupdeadId`, `usrgroupdeadtitle`, `usergame` FROM `hvzuserstate` LEFT JOIN `hvzusrinfo` ON `hvzuserstate`.`userid` = `hvzusrinfo`.`usrid` WHERE `userid` = '$ProfileId'";
	$ProfileResult = mysqli_query($DBCon, $GetProfileInfoQuery);
	$ProfileInfo = mysqli_fetch_row($ProfileResult);
	
	if ($ProfileInfo[5] == "" && $ProfileInfo[1] < 2)
		{
		echo "<img src=\"Images/DefaultAvatars/DefaultZombieAv.png\" class=\"ProfileAvy\">";
		}
	else if ($ProfileInfo[5] == "" && $ProfileInfo[1] > 1)
		{
		echo "<img src=\"Images/DefaultAvatars/DefaultHumanAv.png\" class=\"ProfileAvy\">";
		}
	else
		{
		echo "<img src=\"Images/Avatars//" . $ProfileInfo[5] .  "\" class=\"ProfileAvy\">";
		}
		
	echo "<div class=\"ProfileTopDiv\">";
		
	echo "<p class=\"ProfileName\">" . $ProfileInfo[3] . "</p>";
		
	// If not Zmobie and has no group
	if ($ProfileInfo[1] > 1 && $ProfileInfo[6] == "")
		{
		echo "<p class=\"ProfileNoGroup\">No Group</p>";
		}
	// If a Zombie and has no group
	else if ($ProfileInfo[1] < 2 && $ProfileInfo[8] == "")
		{
		echo "<p class=\"ProfileNoGroup\">No Horde</p>";
		}
	// We do have a group.
	else
		{
		// Are we a human?
		if ($ProfileInfo[1] > 1)
			{
			$GroupNumber = $ProfileInfo[6];
			$GroupLabel = $ProfileInfo[7];
			}
		// Or a Zmabie?
		else if ($ProfileInfo[1] < 2)
			{
			$GroupNumber = $ProfileInfo[8];
			$GroupLabel = $ProfileInfo[9];
			}	
			
		$GetGroupName = "SELECT `groupname`,`groupsubtitle` FROM `hvzgroups` where `groupid` = $GroupNumber";
		$GroupResult = mysqli_query($DBCon, $GetGroupName);
		$GroupInfo = mysqli_fetch_row($GroupResult);
	
		echo "<a href=\"groups.php?GroupId=$GroupNumber\">";
		if ($GroupLabel != "")
			{
			echo "<p class=\"ProfileGroupName\">" . $GroupLabel . " of ". $GroupInfo[0] . "</p>";
			}
		else if ($ProfileInfo[1] > 1)
			{
			echo "<p class=\"ProfileGroupName\">Member of ". $GroupInfo[0] . "</p>";
			}
		else if ($ProfileInfo[1] < 2)
			{
			echo "<p class=\"ProfileGroupName\">Piece of ". $GroupInfo[0] . "</p>";
			}
		echo "<p class=\"ProfileGroupSub\">" . $GroupInfo[1] . "</p>";
		echo "</a>";
		}
	echo "</div>";
	
	echo "<div class=\"ProfileBotDiv\">";
	echo "<p class=\"ProfileDesc\">" . nl2br($ProfileInfo[4]) . "</p>";
	echo "</div>";
	
	echo "<div class=\"ProfileEventLog\">";
	$GetUserEventsQuery = "SELECT `evntType`,`evtDate`,`relevantId` FROM `hvzsmallevents` WHERE `usrSubjctId` = $ProfileId ORDER BY evntId DESC LIMIT 10";
	$EventResults = mysqli_query($DBCon, $GetUserEventsQuery);
	
	while($row = mysqli_fetch_array($EventResults))
		{
		// User Account Creation
		if ($row[0] == 0)
			{
			echo "<div class=\"EvtDiv\">";
			echo $ProfileInfo[3] ." created their Account";
			echo "<p class=\"EvtDate\">(" .  date("jS F, Y", strtotime($row[1])) . ")</p>";
			echo "</div>";
			}
		// Joined game
		else if ($row[0] == 1)
			{
			echo "<div class=\"EvtDiv\">";
			$GetGameQuery = "SELECT `gameName` FROM `hvzgame` WHERE `gameId`=" . $row[2] . "";
			$game = mysqli_fetch_array(mysqli_query($DBCon, $GetGameQuery));
			echo $ProfileInfo[3] ." joined the <span class=\"GroupJoin\" >" . $game[0] . "</span> game";
			echo "<p class=\"EvtDate\">(" .  date("jS F, Y", strtotime($row[1])) . ")</p>";
			echo "</div>";
			}
		// Regular Tag
		else if ($row[0] == 2 || $row[0] == 3)
			{
			echo "<div class=\"EvtDiv\">";
			$GetTagQuery = "SELECT `hvztags`.`tagid` ,`hvztags`.`tagdate` AS TagDate, `hvztags`.`tagerid` AS TagerID, `hvzuserstate`.`userteam` AS TagerTeam, `hvzusrinfo`.`usrname` AS TaggerName, `hvzusrinfo`.`usravy` AS TagerAvatar, `hvztags`.`taggedid` AS TagedID, TagedInfo.`usrname` AS TagedName, TagedInfo.`usravy` AS TagedAvatar, `hvztags`.`taggameid` FROM `hvztags` LEFT JOIN `hvzusrinfo` ON `hvztags`.`tagerid` = `hvzusrinfo`.`usrid` LEFT JOIN `hvzusrinfo` AS TagedInfo ON `hvztags`.`taggedid` = TagedInfo.`usrid` LEFT JOIN `hvzuserstate` ON TagerID = `hvzuserstate`.`userid` WHERE `hvztags`.`tagid` = " . $row[2] . "";
			$TagInfo = mysqli_fetch_array(mysqli_query($DBCon, $GetTagQuery));
			echo "<a href=\"?profId=" . $TagInfo[2] . "\">" . $TagInfo[4] . "</a> tagged <a href=\"?profId=" . $TagInfo[6] . "\">" . $TagInfo[7]."</a>";
			echo "<p class=\"EvtDate\">(" .  date("jS F, Y", strtotime($row[1])) . ")</p>";
			echo "</div>";
			}
		// We tag someone as OZ
		else if ($row[0] == 4)
			{
			// Get tag info.
			$GetTagQuery = "SELECT `hvztags`.`tagid` ,`hvztags`.`tagdate` AS TagDate, `hvztags`.`tagerid` AS TagerID, `hvzuserstate`.`userteam` AS TagerTeam, `hvzusrinfo`.`usrname` AS TaggerName, `hvzusrinfo`.`usravy` AS TagerAvatar, `hvztags`.`taggedid` AS TagedID, TagedInfo.`usrname` AS TagedName, TagedInfo.`usravy` AS TagedAvatar, `hvztags`.`taggameid` FROM `hvztags` LEFT JOIN `hvzusrinfo` ON `hvztags`.`tagerid` = `hvzusrinfo`.`usrid` LEFT JOIN `hvzusrinfo` AS TagedInfo ON `hvztags`.`taggedid` = TagedInfo.`usrid` LEFT JOIN `hvzuserstate` ON TagerID = `hvzuserstate`.`userid` WHERE `hvztags`.`tagid` = " . $row[2] . "";
			$TagInfo = mysqli_fetch_array(mysqli_query($DBCon, $GetTagQuery));
			// If we are an OZ and the tag was made in the current game:
			if ($ProfileInfo[1] == 5 && $TagInfo[9] == $ProfileInfo[10])
				{
				
				}
			// Otherwise, actually show things
			else
				{
				echo "<div class=\"EvtDiv\">";
				echo "<a href=\"?profId=" . $TagInfo[2] . "\">" . $TagInfo[4] . "</a> tagged <a href=\"?profId=" . $TagInfo[6] . "\">" . $TagInfo[7]."</a>";
				echo "<p class=\"EvtDate\">(" .  date("jS F, Y", strtotime($row[1])) . ")</p>";
				echo "</div>";
				}
			}
		// Tagged by OZ
		else if ($row[0] == 5)
			{
			// Get tag info.
			$GetTagQuery = "SELECT `hvztags`.`tagid` ,`hvztags`.`tagdate` AS TagDate, `hvztags`.`tagerid` AS TagerID, `hvzuserstate`.`userteam` AS TagerTeam, `hvzusrinfo`.`usrname` AS TaggerName, `hvzusrinfo`.`usravy` AS TagerAvatar, `hvztags`.`taggedid` AS TagedID, TagedInfo.`usrname` AS TagedName, TagedInfo.`usravy` AS TagedAvatar, `hvztags`.`taggameid` FROM `hvztags` LEFT JOIN `hvzusrinfo` ON `hvztags`.`tagerid` = `hvzusrinfo`.`usrid` LEFT JOIN `hvzusrinfo` AS TagedInfo ON `hvztags`.`taggedid` = TagedInfo.`usrid` LEFT JOIN `hvzuserstate` ON TagerID = `hvzuserstate`.`userid` WHERE `hvztags`.`tagid` = " . $row[2] . "";
			$TagInfo = mysqli_fetch_array(mysqli_query($DBCon, $GetTagQuery));
			// If we were tagged by OZ, do now show their name if they are still in the role.
				echo "<div class=\"EvtDiv\">";
				// If Tagger is OZ and in game
				if ($TagInfo[3] == 5 && $TagInfo[9] == $ProfileInfo[10])
					{
					echo "OZ has tagged <a href=\"?profId=" . $TagInfo[6] . "\">" . $TagInfo[7]."</a>";
					}
				else
					{
					echo "<a href=\"?profId=" . $TagInfo[2] . "\">" . $TagInfo[4] . "</a> tagged <a href=\"?profId=" . $TagInfo[6] . "\">" . $TagInfo[7]."</a>";
					}
				echo "<p class=\"EvtDate\">(" .  date("jS F, Y", strtotime($row[1])) . ")</p>";
				echo "</div>";
			}
		// We got kicked out by admin from the game. Sad.
		else if ($row[0] == 6)
			{
			$GetAdminQuery = "SELECT `usrname` FROM `hvzusrinfo` WHERE `usrid` = " . $row[2] . "";
			$AdminInfo = mysqli_fetch_array(mysqli_query($DBCon, $GetAdminQuery));
			echo "<div class=\"EvtDiv\">";
			echo $AdminInfo[0] . " kicked " . $ProfileInfo[3] . " from game.";
			echo "<p class=\"EvtDate\">(" .  date("jS F, Y", strtotime($row[1])) . ")</p>";
			echo "</div>";
			}
		// Munched down on an NPC
		else if ($row[0] == 7)
			{
			// Do not show if we are OZ and in the same game as the tag happened
			if ($ProfileInfo[1] == 5 && $row[2] == $UserGame)
				{
				
				}
			// Show Otherwise
			else
				{
				echo "<div class=\"EvtDiv\">";
				echo $ProfileInfo[3] . " tagged a non-player.";
				echo "<p class=\"EvtDate\">(" .  date("jS F, Y", strtotime($row[1])) . ")</p>";
				echo "</div>";
				}
			}
		// Joined a group
		else if ($row[0] == 8)
			{
			$GroupID = $row[2];
			$GroupResult = mysqli_query($DBCon, "SELECT `groupname` FROM `hvzgroups` WHERE `groupid` = $GroupID");
			echo "<div class=\"EvtDiv\">";
			if ($GroupResult->num_rows > 0)
				{
				$GroupName = mysqli_fetch_array($GroupResult)[0];
				echo $ProfileInfo[3] . " joined <a class=\"GroupJoin\" href=\"groups.php?GroupId=" . $row[2] . "\">" . $GroupName . "</a>.";
				}
			else
				{
				echo $ProfileInfo[3] . " joined a group.";
				}
			echo "<p class=\"EvtDate\">(" .  date("jS F, Y", strtotime($row[1])) . ")</p>";
			echo "</div>";
			}
		// Left a group
		else if ($row[0] == 9)
			{
			echo "<div class=\"EvtDiv\">";
			if ($row[2] == "")
				{
				echo $ProfileInfo[3] . " left a group.";
				}
			else
				{
				$GroupID = $row[2];
				$GroupResult = mysqli_query($DBCon, "SELECT `groupname` FROM `hvzgroups` WHERE `groupid` = $GroupID");
				if ($GroupResult->num_rows > 0)
					{
					$GroupName = mysqli_fetch_array($GroupResult)[0];
					echo $ProfileInfo[3] . " left <a class=\"GroupJoin\" href=\"groups.php?GroupId=" . $row[2] . "\">" . $GroupName . "</a>.";
					}
				else
					{
					echo $ProfileInfo[3] . " left a group.";
					}
				}
			echo "<p class=\"EvtDate\">(" .  date("jS F, Y", strtotime($row[1])) . ")</p>";
			echo "</div>";
			}
		// Starved
		else if ($row[0] == 10)
			{
			echo "<div class=\"EvtDiv\">";
			echo $ProfileInfo[3] . " has starved.";
			echo "<p class=\"EvtDate\">(" .  date("jS F, Y", strtotime($row[1])) . ")</p>";
			echo "</div>";
			}
		}
	echo "</div>";
	?>
	</body>
</html>