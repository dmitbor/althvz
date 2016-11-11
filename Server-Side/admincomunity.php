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

$ViewerID = $_SESSION["userId"];

$CheckIfAdminQuery = "SELECT `userteam`,`usergame` FROM `hvzuserstate` WHERE `userid`=$ViewerID";
$ViewerTeam = mysqli_fetch_array(mysqli_query($DBCon, $CheckIfAdminQuery))[0];
$ViewerGame = mysqli_fetch_array(mysqli_query($DBCon, $CheckIfAdminQuery))[1];

if ($ViewerTeam != 1 && $ViewerTeam != 4 && $ViewerTeam != -2)
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
		<link href="CSS/general.css" rel="Stylesheet" type="text/css">
		<link href="CSS/admin.css" rel="Stylesheet" type="text/css">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="JS/admincomunity.js"></script>
	</head>
	<body onload="UpdateLogs()">
	
	<?php
	// TopBar + Log Out
	include 'pagetopper.php';
	
	// Location of Chat Logs main directory
	$LogDir  = "ChatLogs/";
	// Collection of logs.
	$Logs = scandir($LogDir);
	
	$LogList = "";
	
	for ($Counter = 2; $Counter < count($Logs);$Counter++)
		{
		if ($Logs[$Counter] != $UserGame)
			{
			$LogList = $LogList . "<option value=\"" . $Logs[$Counter] . "\">" . $Logs[$Counter] . "</option>";
			}
		else
			{
			$LogList = $LogList . "<option selected value=\"" . $Logs[$Counter] . "\">" . $Logs[$Counter] . "</option>";
			}
		}
		
	echo "<div class=\"ComunityMenu\">";
		echo "<p class=\"LogListTop\">Chat Logs:</p>";
		echo "<select class=\"LogList\" id=\"LogList\" name=\"LogList\" onchange=\"UpdateLogs()\">";
		echo $LogList;
		echo "</select>";
		echo "<p class=\"HumChatTopper\">Human Chat:</p>";
		echo "<div class=\"ChatHumanDiv\" id=\"ChatHumanDiv\">";
		// If we are part of a game right now, use those:
		echo "</div>";
		echo "<input type=\"hidden\" id=\"HumanChatEdited\" name=\"HumanChatEdited\" value=\"\">";
		
		echo "<p class=\"ZomChatTopper\">Zombie Chat:</p>";
		echo "<div class=\"ChatZombieDiv\" id=\"ChatZombieDiv\">";
		// If we are part of a game right now, use those:
		echo "</div>";
		echo "<input type=\"hidden\" id=\"ZombieChatEdited\" name=\"ZombieChatEdited\" value=\"\">";
		
		echo "<form action=\"comunityhandler.php\" name=\"GroupEditDiv\" method=\"post\" class=\"GroupEditDiv\">";
		echo "<input type=\"hidden\" id=\"KillGroup\" name=\"KillGroup\" value=\"\">";
		echo "<input type=\"hidden\" id=\"InvisoIcon\" name=\"InvisoIcon\" value=\"\">";
		echo "<input type=\"hidden\" id=\"InvisoID\" name=\"InvisoID\" value=\"\">";
		echo "<input type=\"hidden\" id=\"InvisoType\" name=\"InvisoType\" value=\"\">";
		echo "<input type=\"text\" id=\"GroupNameEditText\" name=\"GroupNameEditText\" class=\"GroupNameEditText\" autocomplete=\"off\" placeholder=\"Group Name\" value=\"\">";
		echo "<input type=\"text\" id=\"GroupSubTitleEditText\" name=\"GroupSubTitleEditText\" class=\"GroupSubTitleEditText\" autocomplete=\"off\" placeholder=\"Group Subtitle\" value=\"\">";
		echo "<textarea id=\"GroupDescExitText\" name=\"GroupDescExitText\" class=\"GroupDescExitText\" rows=\"5\" autocomplete=\"off\" placeholder=\"Story Text\"></textarea>";
		echo "<p id=\"AvyDropPar\" class=\"AvyDropPar\"><input type=\"checkbox\" name=\"RemGrpIcon\" value=\"RemGrpIcon\">Remove Group Icon?</p>";
		echo "<input type=\"submit\" id=\"SaveEditBtn\" name=\"SaveEditBtn\" class=\"SaveEditBtn\" value=\"Save\">";
		echo "<button id=\"DropGroupBtn\" class=\"DropGroupBtn\" onmousedown=\"RemoveGroup()\">Remove Group</button>";
		echo "</form>";
		// Get the list of existing groups:
		$GetGroupsQuery = "SELECT `groupid`,`grouptype`,`groupname`,`groupsubtitle`,`grouptext`,`grouppic` FROM `hvzgroups`";
		$GroupsResults = mysqli_query($DBCon, $GetGroupsQuery);
		echo "<div class=\"GroupListDiv\">";
		while($GroupRow = mysqli_fetch_array($GroupsResults))
			{
			echo "<form class=\"GroupDiv\">";
			echo "<input type=\"hidden\" id=\"GroupID\" name=\"GroupID\" value=\"" . $GroupRow[0] . "\">";
			echo "<input type=\"hidden\" id=\"GroupType\" name=\"GroupType\" value=\"" . $GroupRow[1] . "\">";
			echo "<input type=\"hidden\" id=\"GroupIcon\" name=\"GroupIcon\" value=\"" . $GroupRow[5] . "\">";
			echo "<input type=\"hidden\" id=\"GroupName\" name=\"GroupName\" value=\"" . $GroupRow[2] . "\">";
			echo "<input type=\"hidden\" id=\"GroupSub\" name=\"GroupSub\" value=\"" . $GroupRow[3] . "\">";
			echo "<input type=\"hidden\" id=\"GroupDesc\" name=\"GroupDesc\" value=\"" . $GroupRow[4] . "\">";
			
			// Group Name
			echo "<p class=\"GroupName\">" . $GroupRow[2] . "</p>";
			
			// Group Type
			if ($GroupRow[1] == 0)
				{
				echo "<p class=\"GroupType\">(Zombies)</p>";
				}
			else if ($GroupRow[1] == 1)
				{
				echo "<p class=\"GroupType\">(Humans)</p>";
				} 
			
			// Group Avy
			if ($GroupRow[5] == "")
				{
				echo "<img src=\"Images/GroupImages/Default.png\" class=\"GroupAvy\">";
				}
			else
				{
				echo "<img src=\"Images/GroupImages/" . $GroupRow[5] . "\" class=\"GroupAvy\">";
				}
				
			echo "<button type=\"button\" class=\"EditGroupBtn\" onmousedown=\"EditGroup(this.form)\">Edit</button>";
			echo "</form>";
			}
		echo "</div>";
	echo "</div>";
	?>
	</body>
</html>