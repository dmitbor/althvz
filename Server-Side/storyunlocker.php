<?php
if (!isset($_POST["UnlockStoryBtn"]))
	{
	header("Location: index.php");
	die();
	}
	
	// Set Database Connection
	include 'dbconnector.php';

$UnlockCode = mysqli_real_escape_string($DBCon, $_POST["StoryUnlockText"]);
$GameID = mysqli_real_escape_string($DBCon, $_POST["HiddenGameID"]);
$PlayerTeam = mysqli_real_escape_string($DBCon, $_POST["HiddenTeam"]);

// Find if any of suh unlocked code is found:
$FindLockedQuery = "SELECT `storyid`,`storystate` FROM `hvzbackground` WHERE `storygame` = $GameID AND `storylock` = '$UnlockCode'";
$FoundResult = mysqli_query($DBCon, $FindLockedQuery);

// If any results were found
if ($FoundResult->num_rows>0 && $PlayerTeam > -1)
	{
	// Find the current state
	$ArrayGet = mysqli_fetch_array($FoundResult);
	$CodeState = $ArrayGet[1];
	$UnlockID = $ArrayGet[0];
	
	if ($CodeState == 3)
		{
		$Reveal = 0;
		}
	else if ($CodeState == 4)
		{
		$Reveal = 1;
		}
	else if ($CodeState == 5)
		{
		$Reveal = 2;
		}
	
	// If we are human
	if ($PlayerTeam > 1)
		{
		if ($CodeState == 3 || $CodeState == 5)
			{
			// Unlock
			$UnlockQuery = "UPDATE `hvzbackground` SET `storystate`=$Reveal,`storylock`='' WHERE `storyid` = $UnlockID";
			mysqli_query($DBCon, $UnlockQuery);
			header("Location: story.php");
			die();
			}
		else
			{
			// Error 2: Wrong code
			echo "<html>";
				echo "<body onload=\"document.frm1.submit()\">";
					echo "<form action=\"story.php\" method=\"post\" name=\"frm1\">";
					echo "<input type=\"hidden\" name=\"LoadError\" value=\"2\"/>";
					echo "</form>";
				echo "</body>";
			echo "</html>";
			die();
			}
		}
	// Zambie
	else if ($PlayerTeam < 2 AND $PlayerTeam > -1)
		{
		if ($CodeState == 3 || $CodeState == 4)
			{
			// Unlock
			$UnlockQuery = "UPDATE `hvzbackground` SET `storystate`=$Reveal,`storylock`='' WHERE `storyid` = $UnlockID";
			mysqli_query($DBCon, $UnlockQuery);
			header("Location: story.php");
			die();
			}
		else
			{
			// Error 2: Wrong code
			echo "<html>";
				echo "<body onload=\"document.frm1.submit()\">";
					echo "<form action=\"story.php\" method=\"post\" name=\"frm1\">";
					echo "<input type=\"hidden\" name=\"LoadError\" value=\"2\"/>";
					echo "</form>";
				echo "</body>";
			echo "</html>";
			die();
			}
		}
	// Dead people can't find out things
	else
		{
		// Error 1: Dead People can't enter info.
		echo "<html>";
			echo "<body onload=\"document.frm1.submit()\">";
				echo "<form action=\"story.php\" method=\"post\" name=\"frm1\">";
				echo "<input type=\"hidden\" name=\"LoadError\" value=\"1\"/>";
				echo "</form>";
			echo "</body>";
		echo "</html>";
		die();
		}
	}
else if ($PlayerTeam < 0)
	{
	// Error 1: Dead People can't enter info.
	echo "<html>";
		echo "<body onload=\"document.frm1.submit()\">";
			echo "<form action=\"story.php\" method=\"post\" name=\"frm1\">";
			echo "<input type=\"hidden\" name=\"LoadError\" value=\"1\"/>";
			echo "</form>";
		echo "</body>";
	echo "</html>";
	die();
	}
else
	{
	// Error 2: Wrong code
	echo "<html>";
		echo "<body onload=\"document.frm1.submit()\">";
			echo "<form action=\"story.php\" method=\"post\" name=\"frm1\">";
			echo "<input type=\"hidden\" name=\"LoadError\" value=\"2\"/>";
			echo "</form>";
		echo "</body>";
	echo "</html>";
	die();
	}
?>