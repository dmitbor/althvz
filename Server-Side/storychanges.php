<?php
if (!isset($_POST["Delete"]) && !isset($_POST["SaveStoryBtn"]) && !isset($_POST["NewStoryBtn"]) && !isset($_POST["DelNPCBtn"]) && !isset($_POST["NewNPCBtn"]))
	{
	header("Location: index.php");
	die();
	}
	
// Set Database Connection
include 'dbconnector.php';

if (isset($_POST["SaveStoryBtn"]))
	{
	// Get All the given info.
	$StoryID = mysqli_real_escape_string($DBCon, $_POST["GivenStoryID"]);
	$StoryTitle = mysqli_real_escape_string($DBCon, $_POST["StoryNameText"]);
	$StoryDesc = mysqli_real_escape_string($DBCon, $_POST["StoryDescText"]);
	$StoryState = mysqli_real_escape_string($DBCon, $_POST["StoryStateSelect"]);
	$StoryAccessCode = mysqli_real_escape_string($DBCon, $_POST["GivenStoryAccess"]);
	
	// Get info on the previous state of things:
	$GetPreviousState = "SELECT `storystate` FROM `hvzbackground` WHERE `storyid` = $StoryID";
	$OldState = mysqli_fetch_row(mysqli_query($DBCon, $GetPreviousState))[0];
	
	// If the state was changed:
	if ($OldState != $StoryState)
		{
		// If it's hidden, generate a key
		if ($StoryState > 2)
			{
			// Provide the story unlock with a new Tag
			// Tag Code values array.
			$CharAray = array(
				1 => "1",
				2 => "2",
				3 => "3",
				4 => "4",
				5 => "5",
				6 => "6",
				7 => "7",
				8 => "8",
				9 => "9",
				10 => "0",
				11 => "A",
				12 => "B",
				13 => "C",
				14 => "D",
				15 => "E",
				16 => "F",
				17 => "G",
				18 => "H",
				19 => "I",
				20 => "J",
				21 => "K",
				22 => "L",
				23 => "M",
				24 => "N",
				25 => "P",
				26 => "R",
				27 => "T",
				28 => "U",
				29 => "V",
				30 => "W",
				31 => "X",
				32 => "Y",
			);

			// Get Info on currently existing codes
			$GetUsedTagsQuery = "SELECT `storylock` FROM `hvzbackground`";
			$UsedTagsResult = mysqli_query($DBCon, $GetUsedTagsQuery);

			// Have not found a unique code, yet.
			$FoundUnique = 0;
	
			// While we are trying to generate a unique code.
			while ($FoundUnique == 0)
				{
				// Set to found, so we can prove it false.
				$FoundUnique = 1;
				// Creates a new Code
				$CurrCode = "INF" . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)];
				
				// Go through all results and hope that none of them were used before.
				while($row = mysqli_fetch_array($UsedTagsResult))
					{
					// If we find anything that is like the one we generated, mark this code as a failure and redo it all over.
					if ($row[0] == $CurrCode)
						{
						$FoundUnique = 0;
						}
					}
				}
			// Set the new access code.
			$AccessCode = $CurrCode;
			}
		// Revealed stories have no Access Code.
		else if ($StoryState < 3)
			{
			$AccessCode = "";
			}
		}
	// Otherwise, if nothing changed, keep the old code.
	else
		{
		$AccessCode = $StoryAccessCode;
		}
		
	// Set new info and run it.
	$UpdateStoryInfoQuery = "UPDATE `hvzbackground` SET `storytitle`='$StoryTitle',`storydescription`='$StoryDesc',`storystate`=$StoryState,`storylock`='$AccessCode' WHERE `storyid` = $StoryID";
	mysqli_query($DBCon, $UpdateStoryInfoQuery);
	}
// Removing a story
else if (isset($_POST["Delete"]))
	{
	$StoryID = $_POST["StoryID"];
	
	$RemoveStoryQuery = "DELETE FROM `hvzbackground` WHERE `storyid` = $StoryID";
	mysqli_query($DBCon, $RemoveStoryQuery);
	}
// Adding a new story.
else if (isset($_POST["NewStoryBtn"]))
	{
	// Get Info
	mysqli_real_escape_string($DBCon, $StoryTitle = $_POST["StoryNameText"]);
	mysqli_real_escape_string($DBCon, $StoryDesc = $_POST["StoryDescText"]);
	mysqli_real_escape_string($DBCon, $StoryState = $_POST["StoryStateSelect"]);
	mysqli_real_escape_string($DBCon, $GameNumber = $_POST["GameNum"]);
	
	// Generate Access code:
	// Tag Code values array.
	$CharAray = array(
		1 => "1",
		2 => "2",
		3 => "3",
		4 => "4",
		5 => "5",
		6 => "6",
		7 => "7",
		8 => "8",
		9 => "9",
		10 => "0",
		11 => "A",
		12 => "B",
		13 => "C",
		14 => "D",
		15 => "E",
		16 => "F",
		17 => "G",
		18 => "H",
		19 => "I",
		20 => "J",
		21 => "K",
		22 => "L",
		23 => "M",
		24 => "N",
		25 => "P",
		26 => "R",
		27 => "T",
		28 => "U",
		29 => "V",
		30 => "W",
		31 => "X",
		32 => "Y",
		);

	// Get Info on currently existing codes
	$GetUsedTagsQuery = "SELECT `storylock` FROM `hvzbackground`";
	$UsedTagsResult = mysqli_query($DBCon, $GetUsedTagsQuery);

	// Have not found a unique code, yet.
	$FoundUnique = 0;
	
	// While we are trying to generate a unique code.
	while ($FoundUnique == 0)
		{
		// Set to found, so we can prove it false.
		$FoundUnique = 1;
		// Creates a new Code
		$CurrCode = "INF" . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)];
				
		// Go through all results and hope that none of them were used before.
		while($row = mysqli_fetch_array($UsedTagsResult))
			{
			// If we find anything that is like the one we generated, mark this code as a failure and redo it all over.
			if ($row[0] == $CurrCode)
				{
				$FoundUnique = 0;
				}
			}
		}
		
	// Set the new access code.
	$AccessCode = $CurrCode;
	
	// Set the query:
	$NewStoryQuery = "INSERT INTO `hvzbackground`(`storygame`, `storytitle`, `storydescription`, `storystate`, `storylock`) VALUES ($GameNumber,'$StoryTitle','$StoryDesc',$StoryState,'$AccessCode')";
	mysqli_query($DBCon, $NewStoryQuery);
	}
// Create a new NPC
else if (isset($_POST["NewNPCBtn"]))
	{
	// Current Game
	$TargetGame = $_POST["GameNum"];
	// Provide the story unlock with a new Tag
	// Tag Code values array.
	$CharAray = array(
		1 => "1",
		2 => "2",
		3 => "3",
		4 => "4",
		5 => "5",
		6 => "6",
		7 => "7",
		8 => "8",
		9 => "9",
		10 => "0",
		11 => "A",
		12 => "B",
		13 => "C",
		14 => "D",
		15 => "E",
		16 => "F",
		17 => "G",
		18 => "H",
		19 => "I",
		20 => "J",
		21 => "K",
		22 => "L",
		23 => "M",
		24 => "N",
		25 => "P",
		26 => "R",
		27 => "T",
		28 => "U",
		29 => "V",
		30 => "W",
		31 => "X",
		32 => "Y",
		);
		
	// Get Info on currently existing codes
	$GetUsedTagsQuery = "SELECT `tagcode` FROM `hvztagnums`";
	$UsedTagsResult = mysqli_query($DBCon, $GetUsedTagsQuery);

	// Have not found a unique code, yet.
	$FoundUnique = 0;
	
	// While we are trying to generate a unique code.
	while ($FoundUnique == 0)
		{
		// Set to found, so we can prove it false.
		$FoundUnique = 1;
		// Creates a new Code
		$CurrCode = "NPC" . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)];
		
		// Go through all results and hope that none of them were used before.
		while($row = mysqli_fetch_array($UsedTagsResult))
			{
			// If we find anything that is like the one we generated, mark this code as a failure and redo it all over.
			if ($row[0] == $CurrCode)
				{
				$FoundUnique = 0;
				}
			}
		}
		
	// Found a unique? Shove it up there!
	$SetUserTagQuery = "INSERT INTO `hvztagnums`(`userId`, `gameId`, `tagcode`) VALUES (0,$TargetGame,'$CurrCode')";
	mysqli_query($DBCon, $SetUserTagQuery);
	}
// Removing an NPC code!
else if (isset($_POST["DelNPCBtn"]))
	{
	$DeletedTag = $_POST["TAG"];
	// Removal Query
	$DeleteNPCQuery = "DELETE FROM `hvztagnums` WHERE `tagcode` = '$DeletedTag'";
	// Run Query
	mysqli_query($DBCon, $DeleteNPCQuery);
	}
// Go back to admininstration.
header("Location: adminstory.php");
die();
?>