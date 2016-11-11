<?php
if (!isset($_POST["StrtBtn"]) && !isset($_POST["saveinfo"]) && !isset($_POST["RevealOZBtn"]) && (!isset($_POST["delConfirm"]) || $_POST["delConfirm"] != "DoIt") && !isset($_POST["NewGameBtn"]) && !isset($_POST["TurnOzMgmt"]) && !isset($_POST["UnOzMgmt"]))
	{
	header("Location: index.php");
	die();
	}
	
// Set Database Connection
include 'dbconnector.php';
$date = date('Y-m-d H:i:s');
	
// Get ID of the game we are messing with.
$GameID = $_POST["gameID"];
	
// Let's delete this hubbard mubbard!
if (isset($_POST["delConfirm"]) && $_POST["delConfirm"]=="DoIt")
	{
	// Set the game as complete.
	// Finished games are saved for posterity.
	$RemoveGameQuery = "UPDATE `hvzgame` SET `gameState` = 3, `gameIsPrimary` = 0 WHERE `gameId` = $GameID";
	mysqli_query($DBCon, $RemoveGameQuery);
	
	// Return all relevant players to a state of not being in a game.
	$SetPlayersToDefault = "UPDATE `hvzuserstate` SET `usergame` = 0 WHERE `usergame` = $GameID";
	mysqli_query($DBCon, $SetPlayersToDefault);
	
	// Remove leftover player tags as relevant to the game:
	$RemoveLeftoverTagsQuery = "DELETE FROM `hvztagnums` WHERE `gameId` = $GameID";
	mysqli_query($DBCon, $RemoveLeftoverTagsQuery);
	}
// Starting/Pausing/Unpausing the game
else if (isset($_POST["StrtBtn"]))
	{
	// What do we want to do?
	$WhatToDo = $_POST["StrtBtn"];
	
	// Start the Game
	if ($WhatToDo == "Start Game")
		{
		$ToState = 2;
		
		// Set previously Viable OZs into regular humans, since admins choose OZs
		$SetOthersHumanQuery = "UPDATE `hvzuserstate` SET `userteam` = 3 WHERE `userteam` = 6";
		mysqli_query($DBCon, $SetOthersHumanQuery);
		}
	// Pause the Game
	else if ($WhatToDo == "Pause Game")
		{
		$ToState = 1;
		}
	// Unpause the Game
	else if ($WhatToDo == "Continue")
		{
		$ToState = 2;
		}
				
	// Update the game entry.
	$SetGameStateQuery = "UPDATE `hvzgame` SET `gameState`= $ToState WHERE `gameId` = $GameID";
	mysqli_query($DBCon, $SetGameStateQuery);
	}
// Or, if we are trying to save the current game's info.
else if (isset($_POST["saveinfo"]))
	{
	$NewName = $_POST["GameName"];
	$GameNameUpdateQuery = "UPDATE `hvzgame` SET `gameName`='$NewName' WHERE `gameId` = $GameID";
	mysqli_query($DBCon, $GameNameUpdateQuery);
	
	$GetIconQuery = "SELECT `gameIcon` FROM `hvzgame` WHERE `gameId` = $GameID";
	$CurrentIcon = mysqli_fetch_row(mysqli_query($DBCon, $GetIconQuery))[0];
	
	// If we do not have a an uploading image and have selected another picture:
	if ($_POST["IconList"] != $_POST["DefaultIcon"])
		{
		// Get the newly selected picture
		$NewPicture = $_POST["IconList"];
		// Update The Image
		$UpdateIconQuery = "UPDATE `hvzgame` SET `gameIcon`='$NewPicture' WHERE `gameId`= $GameID";
		mysqli_query($DBCon, $UpdateIconQuery);
		}
	// If we are changing the game's icon:
	else if (isset($_FILES["GameIconToUpload"]) && $_FILES["GameIconToUpload"]["name"] != "" && !isset($_POST["resetIcon"]))
		{
		$Failure = 0;
		$target_dir = "Images/GameIcons//";
		$target_file = $target_dir . "gameicon" . basename($_FILES["GameIconToUpload"]["name"]);
		$FileName = "gameicon" . basename($_FILES["GameIconToUpload"]["name"]);
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		
		// File already on the server with such name?
		if (file_exists($target_file))
			{
			$Failure = 1;
			}
			
		// Check file size against 1MB max
		if ($_FILES["GameIconToUpload"]["size"] > 1048576)
			{
			$Failure = 2;
			}
			
		// Check if file is an image. Check against caps, because some servers are whiny about it.
		if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "JPG" && $imageFileType != "PNG" && $imageFileType != "JPEG" && $imageFileType != "GIF")
			{
			$Failure = 3;
			}
		
		// If all is a success, load the file and update the user.
		if ($Failure == 0)
			{
			move_uploaded_file($_FILES["GameIconToUpload"]["tmp_name"], $target_file);
			$UpdateIconQuery = "UPDATE `hvzgame` SET `gameIcon`='$FileName' WHERE `gameId`= $GameID";
			mysqli_query($DBCon, $UpdateIconQuery);
			}
		// Otherwise, fall back and whine to the user.
		else
			{
			echo "<html>";
				echo "<body onload=\"document.frm1.submit()\">";
					echo "<form action=\"admingame.php\" method=\"post\" name=\"frm1\">";
						echo "<input type=\"hidden\" name=\"LoadError\" value=\"". $Failure . "\"/>";
					echo "</form>";
				echo "</body>";
			echo "</html>";
			die();
			}
		}
	// Reset the Icon to Default
	else if (isset($_POST["resetIcon"]))
		{
		$SetGenericIconQuery = "UPDATE `hvzgame` SET `gameIcon`='DefGameIcon.png' WHERE `gameId`= $GameID";
		mysqli_query($DBCon, $SetGenericIconQuery);
		// Do not remove Default Icon
		if ($CurrentIcon != "DefGameIcon.png")
			{
			unlink('Images/GameIcons//' . $CurrentIcon);
			}
		}
	}
// Or if we are trying to start a new game:
else if (isset($_POST["NewGameBtn"]))
	{
	// Start a session feed
	session_start();
	// Get our ID for adminship
	$OurID = $_SESSION["userId"];
	
	// Get the name for the new game:
	$NewGameName = $_POST["NewGameName"];
	// Default to No Primaries.
	$Primaries = 0;
	// Default to Generic Image
	$IconImage = "DefGameIcon.png";
	// This s where the current code will be placed.
	$CurrCode = "";
	
	// Check if we can run this as a primary game. If not, kick us back with an error.
	if (isset($_POST["NewPrimaryCheck"]))
		{
		$GetPrimariesQuery = "SELECT count(*) FROM `hvzgame` WHERE `gameIsPrimary` = 1";
		$PrimariesResult = mysqli_fetch_row(mysqli_query($DBCon, $GetPrimariesQuery))[0];
		
		// If we found another game with primary state, tell the user.
		if ($PrimariesResult > 0)
			{
			echo "<html>";
				echo "<body onload=\"document.frm1.submit()\">";
					echo "<form action=\"admingame.php\" method=\"post\" name=\"frm1\">";
						echo "<input type=\"hidden\" name=\"LoadError\" value=\"4\"/>";
					echo "</form>";
				echo "</body>";
			echo "</html>";
			die();
			}
		// We can make it primary.
		$Primaries = 1;
		}
		
	// If we are adding a custom icon
	if (isset($_FILES["NewIconToUpload"]) && $_FILES["NewIconToUpload"]["name"] != "")
		{
		$Failure = 0;
		$target_dir = "Images/GameIcons//";
		$target_file = $target_dir . "gameicon" . basename($_FILES["NewIconToUpload"]["name"]);
		$FileName = "gameicon" . basename($_FILES["NewIconToUpload"]["name"]);
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
			
		// File already on the server with such name?
		if (file_exists($target_file))
			{
			$Failure = 1;
			}
				
		// Check file size against 1MB max
		if ($_FILES["NewIconToUpload"]["size"] > 1048576)
			{
			$Failure = 2;
			}
				
		// Check if file is an image. Check against caps, because some servers are whiny about it.
		if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "JPG" && $imageFileType != "PNG" && $imageFileType != "JPEG" && $imageFileType != "GIF")
			{
			$Failure = 3;
			}
			
		// Fall back and whine to the user if errors are caught.
		if ($Failure != 0)
			{
			echo "<html>";
				echo "<body onload=\"document.frm1.submit()\">";
					echo "<form action=\"admingame.php\" method=\"post\" name=\"frm1\">";
						echo "<input type=\"hidden\" name=\"LoadError\" value=\"". $Failure . "\"/>";
					echo "</form>";
				echo "</body>";
			echo "</html>";
			die();
			}
		// Success, upload the image.
		else
			{
			move_uploaded_file($_FILES["NewIconToUpload"]["tmp_name"], $target_file);
			$IconImage = $FileName;
			}
		}
	
	// Now we'll generate a new access code for the game:
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
	$GetTagsQuery = "SELECT `gameAcsCode` FROM `hvzgame`";
	$UserInfoResult = mysqli_query($DBCon, $GetTagsQuery);
	
	// This lets us generate the coolness.
	$FoundUnique = 0;
	
	// While we are trying to generate a unique code.
	while ($FoundUnique == 0)
		{
		// Set to found.
		$FoundUnique = 1;
		// Creates a new Code
		$CurrCode = $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)];
		
		// Go through all results and hope that none of them were used before.
		while($row = mysqli_fetch_array($UserInfoResult))
			{
			// If we find anything that is like the one we generated, mark this code as a failure and redo it all over.
			if ($row[0] == $CurrCode)
				{
				$FoundUnique = 0;
				}
			}
		}
	
	// Finally, create the new game:
	$NewGameQuery = "INSERT INTO `hvzgame`(`gameName`, `gameAcsCode`, `gameState`, `gameIcon`, `gameIsPrimary`) VALUES ('$NewGameName','$CurrCode',0,'$IconImage',$Primaries)";
	mysqli_query($DBCon, $NewGameQuery);
	
	// Find the new game and insert us into it
	$FindGameQuery = "SELECT `gameId` FROM `hvzgame` WHERE `gameName`=\"$NewGameName\"";
	$NewGameId = mysqli_fetch_row(mysqli_query($DBCon, $FindGameQuery))[0];
	
	// Make us the admin of the game.
	$AddMeToGameQuery = "UPDATE `hvzuserstate` SET `userteam`=4,`userlastfed`='$date',`usergame`=$NewGameId WHERE `userid` = $OurID";
	mysqli_query($DBCon, $AddMeToGameQuery);
	
	// Give Us the Tag Code
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
	$GetTagsQuery = "SELECT `tagcode` FROM `hvztagnums`";
	$UserInfoResult = mysqli_query($DBCon, $GetTagsQuery);

	// While we are trying to generate a unique code.
	while ($FoundUnique == 0)
		{
		// Set to found.
		$FoundUnique = 1;
		// Creates a new Code
		$CurrCode = $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)] . $CharAray[rand (1, 32)];
		
		// Go through all results and hope that none of them were used before.
		while($row = mysqli_fetch_array($UserInfoResult))
			{
			// If we find anything that is like the one we generated, mark this code as a failure and redo it all over.
			if ($row[0] == $CurrCode)
				{
				$FoundUnique = 0;
				}
			// Do not allow for INF (Info)/NPC (Non Player Tag) tags to get mixed in.
			if (substr($CurrCode, 0, 3) == "INF" || substr($CurrCode, 0, 3) == "NPC")
				{
				$FoundUnique = 0;
				}
			}
		}
		
	// Hey, we've got through, go to next step!
	// Insert a new tag for the player.
	$InsertPlayerTagQuery = "INSERT INTO `hvztagnums`(`userId`, `gameId`, `tagcode`) VALUES ($OurID,$NewGameId,'$CurrCode')";
	mysqli_query($DBCon, $InsertPlayerTagQuery);
	
	// Alright, now that we have generated the appropriate tags, let's create the new chat directories.
	mkdir("ChatLogs/" . $NewGameId, 0777);
	}
// Set all OZs into normal zombies.
else if (isset($_POST["RevealOZBtn"]))
	{
	// Find All OZ's First:
	$FindAllOZsQuery = "SELECT `userid` FROM `hvzuserstate` WHERE `userteam` = 5";
	$OZs = mysqli_query($DBCon, $FindAllOZsQuery);
	
	// Run through all OZs and set their arsenals to needing retrieval:
	while($OZInfo = mysqli_fetch_array($OZs))
		{
		$OZID = $OZInfo[0];
		$RequestArsenalReturn = "UPDATE `hvzarsenalclaims` SET `claimstate`= 2,`claimdate`='$date' WHERE `claimerid` = $OZID AND `claimstate` = 1";
		mysqli_query($DBCon, $RequestArsenalReturn);
		}
	
	// Well, set them from team 5 (OZ) to team 0 (Zombies).
	$AntiOZQuery = "UPDATE `hvzuserstate` SET `userteam` = 0 WHERE `userteam` = 5 AND `usergame` = $GameID";
	mysqli_query($DBCon, $AntiOZQuery);
	}
// Turn Viable OZ into OZ
else if (isset($_POST["TurnOzMgmt"]))
	{
	$PlayerID = $_POST["OZID"];
	
	// Set User to OZ
	$SetUsertoOZQuery = "UPDATE `hvzuserstate` SET `userteam`= 5 WHERE `userid`= $PlayerID";
	mysqli_query($DBCon, $SetUsertoOZQuery);
	}
// Turn OZ into a Viable OZ
else if (isset($_POST["UnOzMgmt"]))
	{
	$PlayerID = $_POST["OZID"];
	
	// Set User to Viable OZ
	$SetUsertoViableQuery = "UPDATE `hvzuserstate` SET `userteam`= 6 WHERE `userid`= $PlayerID";
	mysqli_query($DBCon, $SetUsertoViableQuery);
	}
	
// Go back to the Game Administration.
header("Location: admingame.php");
die();	
?>