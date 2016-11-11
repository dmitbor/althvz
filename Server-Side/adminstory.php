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
	
// If we are not in a game, we can't access this.
if ($ViewerGame == 0)
	{
	header("Location: joingame.php");
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
		<script src="JS/adminstory.js"></script>
	</head>
	<body>
	<?php
	// TopBar + Log Out
	include 'pagetopper.php';
	
	echo "<div class=\"StoryDiv\">";
		echo "<div class=\"StorySelDiv\">";
		// Get all of the game's story entries:
		$GetGameStory = "SELECT `storyid`,`storytitle`,`storydescription`,`storystate`,`storylock` FROM `hvzbackground` WHERE `storygame` = $UserGame";
		$StoryResults = mysqli_query($DBCon, $GetGameStory);
		while($StoryRow = mysqli_fetch_array($StoryResults))
			{
			echo "<form class=\"StoryListItem\" action=\"storychanges.php\" name=\"storychangeform\" method=\"post\">";
			switch ($StoryRow[3])
				{
				// Revealed to All.
				case 0:
				echo "<p class=\"StoryShownAll\">All</p>";
				break;
				// Revealed to Zombies Only.
				case 1:
				echo "<p class=\"StoryShownZomb\">Zombies</p>";
				break;
				// Revealed to Survivors Only.
				case 2:
				echo "<p class=\"StoryShownHum\">Survivors</p>";
				break;
				// Hidden, Unlocks to All
				case 3:
				echo "<p class=\"StoryHiddenAll\">Hidden - All</p>";
				break;
				// Hidden, Unlocks to Zombies
				case 4:
				echo "<p class=\"StoryHiddenZomb\">Hidden - Zombies</p>";
				break;
				// Hidden, Unlocks to Survivors
				case 5:
				echo "<p class=\"StoryHiddenHum\">Hidden - Survivors</p>";
				break;
				// Currently hidden from everyone, only opened by Admin
				case 6:
				echo "<p class=\"StoryHiddenRestricted\">Unused</p>";
				break;
				}
			echo "<p class=\"StoryName\">" . $StoryRow[1] . "</p>";
			echo "<button type=\"button\" class=\"GetStoryBtn\" onmousedown=\"GetStory(this.form)\">Edit</button>";
			echo "<input type=\"submit\" id=\"Delete\" name=\"Delete\" value=\"Delete\" class=\"DeleteStoryBtn\">";
			echo "<input type=\"hidden\" id=\"StoryID\" name=\"StoryID\" value=\"" . $StoryRow[0] . "\">";
			echo "<input type=\"hidden\" id=\"StoryTitle\" name=\"StoryTitle\" value=\"" . $StoryRow[1] . "\">";
			echo "<input type=\"hidden\" id=\"StoryDesc\" name=\"StoryDesc\" value=\"" . $StoryRow[2] . "\">";
			echo "<input type=\"hidden\" id=\"StoryState\" name=\"StoryState\" value=\"" . $StoryRow[3] . "\">";
			echo "<input type=\"hidden\" id=\"StoryDesc\" name=\"StoryAccess\" value=\"" . $StoryRow[4] . "\">";
			echo "</form>";
			}
		echo "</div>";
		echo "<form action=\"storychanges.php\" name=\"storychangeform\" method=\"post\">";
		echo "<input type=\"text\" id=\"StoryNameText\" name=\"StoryNameText\" class=\"StoryNameText\" value=\"\" placeholder=\"Story Title\" maxlength=\"35\" autocomplete=\"off\">";
		echo "<textarea id=\"StoryDescText\" name=\"StoryDescText\" class=\"StoryDescText\" rows=\"5\" autocomplete=\"off\" placeholder=\"Story Text\"></textarea>";
		echo "<select id=\"StoryStateSelect\" class=\"StoryStateSelect\" name=\"StoryStateSelect\">";
			echo "<option value=\"0\">Revealed to All</option>";
			echo "<option value=\"1\">Revealed to Zombies</option>";
			echo "<option value=\"2\">Revealed to Survivors</option>";
			echo "<option value=\"3\">Hidden (For All)</option>";
			echo "<option value=\"4\">Hidden (For Zombies)</option>";
			echo "<option value=\"5\">Hidden (For Survivors)</option>";
			echo "<option value=\"6\">Unused</option>";
		echo "</select>";
		echo "<p class=\"StoryAccessCode\" id=\"StoryAccessCode\">AAAAAAAA</p>";
		echo "<input type=\"submit\" id=\"SaveStoryBtn\" name=\"SaveStoryBtn\" value=\"Save Story\" class=\"SaveStoryBtn\">";
		echo "<input type=\"submit\" id=\"NewStoryBtn\" name=\"NewStoryBtn\" value=\"New Story\" class=\"NewStoryBtn\">";
		echo "<input type=\"hidden\" id=\"GivenStoryID\" name=\"GivenStoryID\" value=\"\">";
		echo "<input type=\"hidden\" id=\"GivenStoryAccess\" name=\"GivenStoryAccess\" value=\"\">";
		echo "<input type=\"hidden\" id=\"GameNum\" name=\"GameNum\" value=\"" . $UserGame . "\">";
		echo "</form>";
	echo "</div>";
	echo "<div class=\"NPCDiv\">";
	echo "<p class=\"NPCTxt\">Non-Player Tags:</p>";
	$GetNPCTags = "SELECT `tagcode`,`faketagused` FROM `hvztagnums` WHERE `userId` = 0 AND `gameId` = $UserGame";
	$NPCTags = mysqli_query($DBCon, $GetNPCTags);
		echo "<div class=\"NPCListing\">";
		while($NPCRow = mysqli_fetch_array($NPCTags))
			{
			if ($NPCRow[1] == 1)
				{
				echo "<form action=\"storychanges.php\" name=\"NPCTag\" class=\"NPCTagUsed\" method=\"post\">";
				echo "<input type=\"checkbox\" class=\"UsedBox\" checked onclick=\"SetPassUsed(this.form);\">";
				}
			else
				{
				echo "<form action=\"storychanges.php\" name=\"NPCTag\" class=\"NPCTag\" method=\"post\">";
				echo "<input type=\"checkbox\" class=\"UsedBox\" onclick=\"SetPassUsed(this.form);\">";
				}
			echo "<p class=\"TagID\">" . $NPCRow[0] . "</p>";
			echo "<input type=\"hidden\" id=\"TAG\" name=\"TAG\" value=\"" . $NPCRow[0] . "\">";
			echo "<input type=\"submit\" id=\"DelNPCBtn\" name=\"DelNPCBtn\" value=\"Delete\" class=\"DelNPCBtn\">";
			echo "</form>";
			}
		echo "</div>";
		echo "<form action=\"storychanges.php\" name=\"NPCTag\" method=\"post\">";
		echo "<input type=\"hidden\" id=\"GameNum\" name=\"GameNum\" value=\"" . $UserGame . "\">";
		echo "<form action=\"storychanges.php\" name=\"StoryNPCGen\" method=\"post\">";
		echo "<input type=\"submit\" id=\"NewNPCBtn\" name=\"NewNPCBtn\" value=\"New NPT\" class=\"NewNPCBtn\">";
		echo "</form>";
	echo "</div>";
	?>
	</body>
</html>