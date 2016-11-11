<?php
if (!isset($_POST["UpdateBtn"]) && !isset($_POST["EmailPassUpdateBtn"]))
	{
	header("Location: index.php");
	die();
	}
else if (isset($_POST["UpdateBtn"]))
	{
	// Start session
	session_start();
	$MyID = $_SESSION["userId"];
	
	include 'dbconnector.php';
	
	$NewName = mysqli_real_escape_string($DBCon, $_POST["NewName"]);
	$NewDesc = mysqli_real_escape_string($DBCon, $_POST["NewDesc"]);
	
	$GetAvyQuery = "SELECT `usravy` FROM `hvzusrinfo` WHERE `usrid`=$MyID";
	$CurrentAvy = mysqli_fetch_row(mysqli_query($DBCon, $GetAvyQuery))[0];
	
	
	$NameDescUpdateQuery = "UPDATE `hvzusrinfo` SET `usrname`='$NewName',`usrdesc`='$NewDesc' WHERE `usrid`= $MyID";
	mysqli_query($DBCon, $NameDescUpdateQuery);
	
	// If there is an avy upload:
	if (isset($_FILES["AvyToUpload"]) && $_FILES["AvyToUpload"]["name"] != ""  && $_FILES["AvyToUpload"]["size"] != 0)
		{
		$Failure = 0;
		$target_dir = "Images/Avatars//";
		$target_file = $target_dir . "usr" . basename($_FILES["AvyToUpload"]["name"]);
		$FileName = "usr" . basename($_FILES["AvyToUpload"]["name"]);
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		
		// File already on the server with such name?
		if (file_exists($target_file))
			{
			$Failure = 1;
			}
			
		// Check file size against 1MB max
		if ($_FILES["AvyToUpload"]["size"] > 1048576)
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
			move_uploaded_file($_FILES["AvyToUpload"]["tmp_name"], $target_file);
			$UpdateAvyQuery = "UPDATE `hvzusrinfo` SET `usravy`='$FileName' WHERE `usrid`= $MyID";
			mysqli_query($DBCon, $UpdateAvyQuery);
			// Do not remove Default avatars, other may use them.
			if (substr($CurrentAvy, 0,3) != "Def")
				{
				unlink('Images/Avatars//' . $CurrentAvy);
				}
			}
		// Otherwise, fall back and whine to the user.
		else
			{
			echo "<html>";
				echo "<body onload=\"document.frm1.submit()\">";
					echo "<form action=\"editplayerstats.php\" method=\"post\" name=\"frm1\">";
						echo "<input type=\"hidden\" name=\"LoadError\" value=\"". $Failure . "\"/>";
					echo "</form>";
				echo "</body>";
			echo "</html>";
			die();
			}
		}
	// Just Removing Avatar
	else if ($_POST["AvySelect"] == "Remove")
		{
		// Do not remove Default avatars, other may use them.
		if (substr($CurrentAvy, 0,3) != "Def")
			{
			unlink('Images/Avatars//' . $CurrentAvy);
			}
		$UpdateAvyQuery = "UPDATE `hvzusrinfo` SET `usravy`='' WHERE `usrid`= $MyID";
		mysqli_query($DBCon, $UpdateAvyQuery);
		}
	// Any other default choice
	else if ($_POST["AvySelect"] != "")
		{
		// Do not remove Default avatars, other may use them.
		if (substr($CurrentAvy, 0,3) != "Def")
			{
			unlink('Images/Avatars//' . $CurrentAvy);
			}
		$DefAvyName = $_POST["AvySelect"];
		$UpdateAvyQuery = "UPDATE `hvzusrinfo` SET `usravy`='$DefAvyName' WHERE `usrid`= $MyID";
		mysqli_query($DBCon, $UpdateAvyQuery);
		}
		
	// Resetting session data.
	session_unset();
	// Set user ID.
	$_SESSION["userId"] = $MyID;
	$_SESSION["userName"] = $NewName;
		
	// All is good, get out of here.
	header("Location: playerstats.php");
	die();
	}
else if (isset($_POST["EmailPassUpdateBtn"]))
	{
	// Set Database Connection
	$DBCon = mysqli_connect("localhost","DataHandler","IHandleAllTheData","keenehvz");
	
	// Start session
	session_start();
	$MyID = $_SESSION["userId"];
	
	// This allows us to use the Hash functions that is otherwise not available from GoDaddy, because they are rude bloody people like that.
	require "Hasher/lib/password.php";
	
	$OldPass = mysqli_real_escape_string($DBCon, $_POST["CurPassText"]);
	$NewPass =  mysqli_real_escape_string($DBCon, $_POST["NewPassText1"]);
	$NewPassConf = mysqli_real_escape_string($DBCon, $_POST["NewPassText2"]);
	$NewEmail = mysqli_real_escape_string($DBCon, $_POST["NewEmailText"]);
	
	if (isset($_POST["AllowNewsEmailBox"]))
		{
		$AllowNews = 1;
		}
	else
		{
		$AllowNews = 0;
		}
	
	if (isset($_POST["AllowMssnEmailBox"]))
		{
		$AllowMissions = 1;
		}
	else
		{
		$AllowMissions = 0;
		}
	
	$UserPassInfoQuery = "SELECT `usrSaltyPass`,`Salt` FROM `hvzusers` WHERE `usrID` = $MyID";
	$UserPassResults = mysqli_fetch_array(mysqli_query($DBCon, $UserPassInfoQuery));
	
	$SaltedPass = $UserPassResults[0];
	$Salt = $UserPassResults[1];
	
	$SaltingOptions =
		[
		'salt' => $Salt,
		'cost' => 10,
		];
			
	// Create the new Hash.
	$HashedCurPass = password_hash($OldPass, PASSWORD_BCRYPT, $SaltingOptions);
	
	// If Password doesn't fit, go back.
	if ($HashedCurPass != $SaltedPass)
		{
		echo "<html>";
			echo "<body onload=\"document.frm1.submit()\">";
				echo "<form action=\"editplayerstats.php\" method=\"post\" name=\"frm1\">";
					echo "<input type=\"hidden\" name=\"LoadError\" value=\"4\"/>";
				echo "</form>";
			echo "</body>";
		echo "</html>";
		die();
		}
		
	// If Password do not match, go back.
	if ($NewPass != $NewPassConf)
		{
		echo "<html>";
			echo "<body onload=\"document.frm1.submit()\">";
				echo "<form action=\"editplayerstats.php\" method=\"post\" name=\"frm1\">";
					echo "<input type=\"hidden\" name=\"LoadError\" value=\"5\"/>";
				echo "</form>";
			echo "</body>";
		echo "</html>";
		die();
		}
		
	// Password Update
	if ($NewPass != "")
		{
		$NewSalt = mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
		$NewSaltingOptions =
			[
			'salt' => $NewSalt,
			'cost' => 10,
			];
			
		// Create the new Hash.
		$NewSaltedPass = password_hash($NewPass, PASSWORD_BCRYPT, $NewSaltingOptions);
		
		// Update the password
		$NewPassQuery = "UPDATE `hvzusers` SET `usrSaltyPass`='$NewSaltedPass',`Salt`='$NewSalt' WHERE `usrID` = $MyID";
		mysqli_query($DBCon, $NewPassQuery);
		}
		
	if ($NewEmail != "")
		{
		$NewEmailQuery = "UPDATE `hvzusers` SET `usrEmail`='$NewEmail' WHERE `usrID` = $MyID";
		mysqli_query($DBCon, $NewEmailQuery);
		}
		
	if ($AllowMissions == 1 && $AllowNews == 1)
		{
		$EmailState = 1;
		}
	else if ($AllowMissions == 0 && $AllowNews == 1)
		{
		$EmailState = 2;
		}
	else if ($AllowMissions == 1 && $AllowNews == 0)
		{
		$EmailState = 3;
		}
	else
		{
		$EmailState = 0;
		}
	
	$NewEmailStateQuery = "UPDATE `hvzusers` SET `usrEmailState`=$EmailState WHERE `usrID` = $MyID";
	mysqli_query($DBCon, $NewEmailStateQuery);
		
	// All is good, get out of here.
	header("Location: editplayerstats.php");
	die();
	}
?>