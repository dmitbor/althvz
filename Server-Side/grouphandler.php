<?php
if (!isset($_POST["KickMemberBtn"]) && !isset($_POST["RemoveMemberTitleBtn"]) && !isset($_POST["GiveAdminBtn"]) && !isset($_POST["SaveGroupAdmin"]) && !isset($_POST["QuitGroup"]) && !isset($_POST["JoinGroup"]) && !isset($_POST["NewGroup"]) && !isset($_POST["SaveTitle"]))
	{
	header("Location: index.php");
	die();
	}
	
// Set Database Connection
include 'dbconnector.php';
$date = date('Y-m-d H:i:s');

// Kicking out the member
if (isset($_POST["KickMemberBtn"]))
	{
	$KickedMemberId = $_POST["MemberID"];
	$GroupType = $_POST["GroupType"];
	
	// Kicking from Zombie Group
	if ($GroupType == 0)
		{
		$KickPlayerQuery = "UPDATE `hvzusrinfo` SET `usrgroupdeadId`= NULL,`usrgroupdeadtitle`=NULL WHERE `usrid` = $KickedMemberId";
		}
	// Kicking from human group
	else if ($GroupType == 1)
		{
		$KickPlayerQuery = "UPDATE `hvzusrinfo` SET `usrgroupliveId`=NULL,`usrgrouplivetitle`=NULL WHERE `usrid` = $KickedMemberId";
		}
	// Run the kicking Query
	mysqli_query($DBCon, $KickPlayerQuery);
	}
// Making another member into an Admin/Leader
if (isset($_POST["GiveAdminBtn"]))
	{
	$PromotedMemberID = $_POST["MemberID"];
	$GroupID = $_POST["GroupID"];
	
	$SetNewGroupAdmin = "UPDATE `hvzgroups` SET `leaderId`=$PromotedMemberID WHERE `groupid` = $GroupID";
	// Run the promotion Query
	mysqli_query($DBCon, $SetNewGroupAdmin);
	}
// Removing user's title
else if (isset($_POST["RemoveMemberTitleBtn"]))
	{
	$KickedMemberId = $_POST["MemberID"];
	$GroupType = $_POST["GroupType"];
	
	// Removing Zombie Title
	if ($GroupType == 0)
		{
		$DetitlePlayerQuery = "UPDATE `hvzusrinfo` SET `usrgroupdeadtitle` = NULL WHERE `usrid` = $KickedMemberId";
		}
	// Removing Human Title
	else if ($GroupType == 1)
		{
		$DetitlePlayerQuery = "UPDATE `hvzusrinfo` SET `usrgrouplivetitle` = NULL WHERE `usrid` = $KickedMemberId";
		}
		
	// Run the detitling Query
	mysqli_query($DBCon, $DetitlePlayerQuery);
	}
// Save New Group Info
else if (isset($_POST["SaveGroupAdmin"]))
	{
	$GroupID = mysqli_real_escape_string($DBCon, $_POST["GroupID"]);
	$NewGroupName = mysqli_real_escape_string($DBCon, $_POST["GroupNameText"]);
	$NewGroupTitle = mysqli_real_escape_string($DBCon, $_POST["GroupSubNameText"]);
	$NewGroupDesc = mysqli_real_escape_string($DBCon, $_POST["GroupDescTextArea"]);
	$CurrentIcon = mysqli_real_escape_string($DBCon, $_POST["GroupAvy"]);
	$GetMyTitleState = $_POST["TitleSelectAdmin"];
	session_start();
	$MyId = $_SESSION["userId"];
	
	// If we are adding a custom icon for our group
	if (isset($_FILES["GroupIconUp"]) && $_FILES["GroupIconUp"]["name"] != "")
		{
		$Failure = 0;
		$target_dir = "Images/GroupImages//";
		$target_file = $target_dir . "groupicon" . basename($_FILES["GroupIconUp"]["name"]);
		$FileName = "groupicon" . basename($_FILES["GroupIconUp"]["name"]);
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
			
		// File already on the server with such name?
		if (file_exists($target_file))
			{
			$Failure = 1;
			}
				
		// Check file size against 1MB max
		if ($_FILES["GroupIconUp"]["size"] > 1048576)
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
					echo "<form action=\"groups.php\" method=\"post\" name=\"frm1\">";
						echo "<input type=\"hidden\" name=\"LoadError\" value=\"". $Failure . "\"/>";
					echo "</form>";
				echo "</body>";
			echo "</html>";
			die();
			}
		// Success, upload the image.
		else
			{
			move_uploaded_file($_FILES["GroupIconUp"]["tmp_name"], $target_file);
			$NewGroupIcon = $FileName;
			// Unlick an existing icon.
			if ($CurrentIcon != "")
				{
				unlink('Images/GroupImages//' . $CurrentIcon);
				}
			}
		}
		
	// Alright, got group info, now let's set it!
	$SetNewGroupInfoQuery = "UPDATE `hvzgroups` SET `groupname`='$NewGroupName',`groupsubtitle`='$NewGroupTitle',`grouptext`='$NewGroupDesc'";
	if ((isset($Failure) && $Failure == 0) || isset($_POST["ClearIcon"]))
		{
		if (isset($_POST["ClearIcon"]))
			{
			$NewGroupIcon = "";
			// Remove the current icon again
			if ($CurrentIcon != "")
				{
				unlink('Images/GroupImages//' . $CurrentIcon);
				}
			}
		
		$SetNewGroupInfoQuery = $SetNewGroupInfoQuery . ",`grouppic`='$NewGroupIcon'";
		}
	$SetNewGroupInfoQuery = $SetNewGroupInfoQuery . " WHERE `groupid` = $GroupID";
	
	// Run the update Query
	mysqli_query($DBCon, $SetNewGroupInfoQuery);
	
	if ($GetMyTitleState != 0)
		{
		// Let's deal with title Generation.
		include 'nickgen.php';
		
		// Let's get our current team:
		$GroupType = $_POST["GroupType"];
		
		// Zombie Title
		if ($GroupType == 0)
			{
			$SetNewTitleQuery = "UPDATE `hvzusrinfo` SET `usrgroupdeadtitle`='$NewTitle' WHERE `usrid` = $MyId";
			}
		// Human Title
		else if ($GroupType == 1)
			{
			$SetNewTitleQuery = "UPDATE `hvzusrinfo` SET `usrgrouplivetitle`='$NewTitle' WHERE `usrid` = $MyId";
			}
			
		// Run the title update Query
		mysqli_query($DBCon, $SetNewTitleQuery);
		}
	}
// Quiting the group
else if (isset($_POST["QuitGroup"]))
	{
	session_start();
	$MyId = $_SESSION["userId"];
	$GroupType = $_POST["GroupType"];
	$GroupLeaderID = $_POST["GroupLeaderID"];
	$GroupID = $_POST["GroupID"];
	
	// If we are just a member, make us leave.
	If ($MyId != $GroupLeaderID)
		{
		if ($GroupType == 0)
			{
			$ClearGroup = "UPDATE `hvzusrinfo` SET `usrgroupdeadId`=NULL,`usrgroupdeadtitle`=NULL WHERE `usrid` = $MyId";
			}
		else if ($GroupType == 1)
			{
			$ClearGroup = "UPDATE `hvzusrinfo` SET `usrgroupliveId`=NULL,`usrgrouplivetitle`=NULL WHERE `usrid` = $MyId";
			}
		// Run the Query, leave the group.
		mysqli_query($DBCon, $ClearGroup);
		
		// Create a new Small Event
		$JoinGroupMiniEventQuery = "INSERT INTO `hvzsmallevents`(`evntType`, `evtDate`, `usrSubjctId`, `relevantId`) VALUES (9,'$date',$MyId,$GroupID)";
		mysqli_query($DBCon, $JoinGroupMiniEventQuery);
		}
	// But wait, what if we are a leader?!
	// We have to transport the leadership to someone else!
	else if ($MyId == $GroupLeaderID)
		{
		// Is there anyone else out there in the group?
		if ($GroupType == 0)
			{
			$GetViableUsersQuery = "SELECT COUNT(*) FROM `hvzusrinfo` WHERE `usrgroupdeadId` = $GroupID";
			}
		else if ($GroupType == 1)
			{
			$GetViableUsersQuery = "SELECT COUNT(*) FROM `hvzusrinfo` WHERE `usrgroupliveId` = $GroupID";
			}
		$UsersLeft = mysqli_fetch_row(mysqli_query($DBCon, $GetViableUsersQuery))[0];
		
		// If there are people left in the group, move the adminship
		if ($UsersLeft > 1)
			{
			$ChangedAdminship = 0;
			
			// Find all the users that are in the group, with live ones (humans) or zombies one (Zombies), not dead, and in a game right now.
			if ($GroupType == 0)
				{
				$GetViableUsersQuery = "SELECT `usrid` FROM `hvzusrinfo` LEFT JOIN `hvzuserstate` ON `userid`=`usrid` WHERE `userteam` < 2 AND `userteam` > -1 AND NOT `usergame` = 0 AND `usrgroupdeadId` = $GroupID AND NOT `userid` = $GroupLeaderID";
				}
			else if ($GroupType == 1)
				{
				$GetViableUsersQuery = "SELECT `usrid` FROM `hvzusrinfo` LEFT JOIN `hvzuserstate` ON `userid`=`usrid` WHERE `userteam` > 1 AND NOT `usergame` = 0 AND `usrgroupliveId` = $GroupID AND NOT `userid` = $GroupLeaderID";
				}
			// Let's see if anyone fits in:
			$ViableCandidates = mysqli_query($DBCon, $GetViableUsersQuery);
			
			// If we found someone, give it to the first person.
			if ($ViableCandidates->num_rows>0)
				{
				// Get their ID
				$FirstViableMemberID = mysqli_fetch_row($ViableCandidates)[0];
				// Give it to them
				$GiveAdminship = "UPDATE `hvzgroups` SET `leaderId`=$FirstViableMemberID WHERE `groupid` = $GroupID";
				// And run the query.
				mysqli_query($DBCon, $GiveAdminship);
				// Success!
				$ChangedAdminship = 1;
				}
				
			// Uh oh, no one else in the group currently who can do this
			if ($ChangedAdminship == 0)
				{
				if ($GroupType == 0)
					{
					$GetFirstAnyGroupMember = "SELECT `usrid` FROM `hvzusrinfo` LEFT JOIN `hvzuserstate` ON `userid`=`usrid` WHERE NOT `usergame` = 0 AND `usrgroupdeadId` = $GroupID AND NOT `userid` = $GroupLeaderID";
					}
				else if ($GroupType == 1)
					{
					$GetFirstAnyGroupMember = "SELECT `usrid` FROM `hvzusrinfo` LEFT JOIN `hvzuserstate` ON `userid`=`usrid` WHERE NOT `usergame` = 0 AND `usrgroupliveId` = $GroupID AND NOT `userid` = $GroupLeaderID";
					}
				// Get their ID
				$FirstViableMemberID = mysqli_fetch_row(mysqli_query($DBCon, $GetFirstAnyGroupMember))[0];
				// Give it to them
				$GiveAdminship = "UPDATE `hvzgroups` SET `leaderId`=$FirstViableMemberID WHERE `groupid` = $GroupID";
				// And run the query.
				mysqli_query($DBCon, $GiveAdminship);
				// Success!
				$ChangedAdminship = 1;
				}
				
			// Alright, screw it, something is wrong. Kill the group!
			if ($ChangedAdminship == 0)
				{
				if ($GroupType == 0)
					{
					$KickAllFromGroup = "UPDATE `hvzusrinfo` SET `usrgroupliveId` = NULL WHERE `usrgroupdeadId` = $GroupID";
					}
				else if ($GroupType == 1)
					{
					$KickAllFromGroup = "UPDATE `hvzusrinfo` SET `usrgroupliveId` = NULL WHERE `usrgroupliveId` = $GroupID";
					}
				// Run the kickening
				mysqli_query($DBCon, $KickAllFromGroup);
				// Ayyyy, kill the group!
				$KillGroupQuery = "DELETE FROM `hvzgroups` WHERE `groupid` = $GroupID";
				// Run the Query, kill the group.
				mysqli_query($DBCon, $KillGroupQuery);
				// Success!
				$ChangedAdminship = 1;
				}
				
			// Anyway, if it was all a success... kick yourself out.
			if ($ChangedAdminship == 1)
				{
				if ($GroupType == 0)
					{
					$ClearGroup = "UPDATE `hvzusrinfo` SET `usrgroupdeadId`=NULL,`usrgroupdeadtitle`=NULL WHERE `usrid` = $MyId";
					}
				else if ($GroupType == 1)
					{
					$ClearGroup = "UPDATE `hvzusrinfo` SET `usrgroupliveId`=NULL,`usrgrouplivetitle`=NULL WHERE `usrid` = $MyId";
					}
				// Run the Query, leave the group.
				mysqli_query($DBCon, $ClearGroup);
				}
			}
		// No one else is around, just leave.
		else
			{
			if ($GroupType == 0)
				{
				$ClearGroup = "UPDATE `hvzusrinfo` SET `usrgroupdeadId`=NULL,`usrgroupdeadtitle`=NULL WHERE `usrid` = $MyId";
				}
			else if ($GroupType == 1)
				{
				$ClearGroup = "UPDATE `hvzusrinfo` SET `usrgroupliveId`=NULL,`usrgrouplivetitle`=NULL WHERE `usrid` = $MyId";
				}
			// Run the Query, leave the group.
			mysqli_query($DBCon, $ClearGroup);
			// Kill the Fight
			$KillGroupQuery = "DELETE FROM `hvzgroups` WHERE `groupid` = $GroupID";
			// Run the Query, kill the group.
			mysqli_query($DBCon, $KillGroupQuery);
			}
		// Create a new Small Event
		$JoinGroupMiniEventQuery = "INSERT INTO `hvzsmallevents`(`evntType`, `evtDate`, `usrSubjctId`, `relevantId`) VALUES (9,'$date',$MyId,NULL)";
		mysqli_query($DBCon, $JoinGroupMiniEventQuery);
		}
	}
// Trying to join the group
else if (isset($_POST["JoinGroup"]))
	{
	session_start();
	$MyId = $_SESSION["userId"];
	$GroupID = $_POST["GroupID"];
	$UserTeam = $_POST["UserTeam"];
	
	// Join Zombie Team
	if ($UserTeam < 2)
		{
		$JoinGroupQuery = "UPDATE `hvzusrinfo` SET `usrgroupdeadId`=$GroupID WHERE `usrid` = $MyId";
		}
	// Join Human Team
	else if ($UserTeam > 1)
		{
		$JoinGroupQuery = "UPDATE `hvzusrinfo` SET `usrgroupliveId`=$GroupID WHERE `usrid` = $MyId";
		}
	// Run the Query, join the group.
	mysqli_query($DBCon, $JoinGroupQuery);
	
	// Create a new Small Event
	$JoinGroupMiniEventQuery = "INSERT INTO `hvzsmallevents`(`evntType`, `evtDate`, `usrSubjctId`, `relevantId`) VALUES (8,'$date',$MyId,$GroupID)";
	mysqli_query($DBCon, $JoinGroupMiniEventQuery);
	}
// Creating a new group.
else if (isset($_POST["NewGroup"]))
	{
	// Get Info
	session_start();
	$MyId = $_SESSION["userId"];
	$NewGroupName = mysqli_real_escape_string($DBCon, $_POST["NewGroupName"]);
	$NewGroupSub = mysqli_real_escape_string($DBCon, $_POST["NewGroupSubTitle"]);
	$NewGroupDesc = mysqli_real_escape_string($DBCon, $_POST["NewGroupDesc"]);
	$NewGroupType = $_POST["NewGroupType"];
	
	// Create a new Group.
	$NewGroupQuery = "INSERT INTO `hvzgroups`(`leaderId`, `grouptype`, `groupname`, `groupsubtitle`, `grouptext`) VALUES ($MyId,$NewGroupType,'$NewGroupName','$NewGroupSub','$NewGroupDesc')";
	// Run the Query, Create the group.
	mysqli_query($DBCon, $NewGroupQuery);
	
	// Getting the ID of the group we just made.
	if ($NewGroupType == 0)
		{
		$MyNewGroup = "SELECT `groupid` FROM `hvzgroups` WHERE `leaderId` = $MyId AND `grouptype` = 0";
		}
	else if ($NewGroupType == 1)
		{
		$MyNewGroup = "SELECT `groupid` FROM `hvzgroups` WHERE `leaderId` = $MyId AND `grouptype` = 1";
		}
	// Get new group ID
	$NewGroupID = mysqli_fetch_row(mysqli_query($DBCon, $MyNewGroup))[0];
	
	// Add us to the group:
	if ($NewGroupType == 0)
		{
		$GiveMeGroup = "UPDATE `hvzusrinfo` SET `usrgroupdeadId`=$NewGroupID WHERE `usrid` = $MyId";
		}
	else if ($NewGroupType == 1)
		{
		$GiveMeGroup = "UPDATE `hvzusrinfo` SET `usrgroupliveId`=$NewGroupID WHERE `usrid` = $MyId";
		}
		
	// Run the Query, Add us to the group.
	mysqli_query($DBCon, $GiveMeGroup);
	}
// Just user updating their title
else if (isset($_POST["SaveTitle"]))
	{
	// Get Info
	session_start();
	$MyId = $_SESSION["userId"];
	$GetMyTitleState = $_POST["TitleSelect"];
	
	if ($GetMyTitleState > 0)
		{
		// Let's deal with title Generation.
		include 'nickgen.php';
		
		// Let's get our current team:
		$GroupType = $_POST["GroupType"];
		
		// Zombie Title
		if ($GroupType == 0)
			{
			$SetNewTitleQuery = "UPDATE `hvzusrinfo` SET `usrgroupdeadtitle`='$NewTitle' WHERE `usrid` = $MyId";
			}
		// Human Title
		else if ($GroupType == 1)
			{
			$SetNewTitleQuery = "UPDATE `hvzusrinfo` SET `usrgrouplivetitle`='$NewTitle' WHERE `usrid` = $MyId";
			}
			
		// Run the title update Query
		mysqli_query($DBCon, $SetNewTitleQuery);
		}
	}
	
// Finish everything and get out.
header("Location: groups.php");
die();
?>