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
		<link href="CSS/story.css" rel="Stylesheet" type="text/css">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="JS/story.js"></script>
	</head>
	<body>
	<?php
	// TopBar + Log Out
	include 'pagetopper.php';
	
	// Let's get the relevant Story parts:
	if ($UserTeam > 1)
		{
		$GetStoryQuery = "SELECT `storytitle`,`storydescription` FROM `hvzbackground` WHERE `storygame` = $UserGame AND (`storystate` = 0 OR `storystate` = 2) ORDER BY `storyid` DESC";
		}
	else if ($UserTeam < 2)
		{
		$GetStoryQuery = "SELECT `storytitle`,`storydescription` FROM `hvzbackground` WHERE `storygame` = $UserGame AND (`storystate` = 0 OR `storystate` = 1) ORDER BY `storyid` DESC";
		}
	$StoryResults = mysqli_query($DBCon, $GetStoryQuery);
	if ($StoryResults->num_rows>0)
		{
		echo "<div class=\"TimeLine\">";
		echo "<p class=\"BoxInstructions\">Mouse Over Boxes for Details:</p>";
		while($StoryRow = mysqli_fetch_array($StoryResults))
			{
			echo "<form class=\"StoryBlock\" onmouseover=\"ShowStory(this)\">";
			echo "<input type=\"hidden\" id=\"HiddenTitle\" name=\"HiddenTitle\" value=\"" .  nl2br($StoryRow[0]) . "\">";
			echo "<input type=\"hidden\" id=\"HiddenText\" name=\"HiddenText\" value=\"" .  nl2br($StoryRow[1]) . "\">";
			echo "<p class=\"BigTitle\">" . $StoryRow[0] . "</p>";
			echo "</form>";
			}
		echo "</div>";
		echo "<div id=\"InfoDiv\" class=\"InfoDiv\">";
		echo "<p class=\"StoryTitle\" id=\"StoryTitle\">Title Goes Here</p>";
		echo "<p class=\"StoryText\" id=\"StoryText\">TEXT TEXT TEXT</p>";
		echo "</div>";
		}
	echo "<form class=\"UnlockDiv\" action=\"storyunlocker.php\" name=\"unlocker\" method=\"post\">";
	echo "<p class=\"UnlockerTop\">Story Unlock Code Entry:</p>";
	echo "<input type=\"text\" id=\"StoryUnlockText\" name=\"StoryUnlockText\" class=\"StoryUnlockText\" value=\"\" maxlength=\"8\" autocomplete=\"off\">";
	echo "<input type=\"submit\" id=\"UnlockStoryBtn\" name=\"UnlockStoryBtn\" value=\"Unlock\" class=\"UnlockStoryBtn\">";
	echo "<input type=\"hidden\" id=\"HiddenGameID\" name=\"HiddenGameID\" value=\"" . $UserGame . "\">";
	echo "<input type=\"hidden\" id=\"HiddenTeam\" name=\"HiddenTeam\" value=\"" . $UserTeam . "\">";
	echo "</form>";
		
	if (isset($_POST["LoadError"]))
		{
		echo "<div class=\"ErrorDiv\">";
		if ($_POST["LoadError"] == 1)
			{
			echo "You may not enter new Story Items if you have starved.";
			}
		else if ($_POST["LoadError"] == 2)
			{
			echo "The provided code does not match any entries in the database.";
			}
		echo "</div>";
		}
	?>
	</body>
</html>