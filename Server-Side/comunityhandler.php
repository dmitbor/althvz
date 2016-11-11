<?php
if (!isset($_POST["SaveEditBtn"]) && (!isset($_POST["KillGroup"]) || $_POST["KillGroup"] != "DoIt"))
	{
	header("Location: index.php");
	die();
	}
	
// Set Database Connection
include 'dbconnector.php';
	
// Changing group's info.
if (isset($_POST["SaveEditBtn"]))
	{
	$GroupID = $_POST["InvisoID"];
	$EditedGroupName = mysqli_real_escape_string($DBCon, $_POST["GroupNameEditText"]);
	$EditedGroupSubtitle = mysqli_real_escape_string($DBCon, $_POST["GroupSubTitleEditText"]);
	$EditedGroupDescription = mysqli_real_escape_string($DBCon, $_POST["GroupDescExitText"]);
	$HiddenIconName = $_POST["InvisoIcon"];
	
	$UpdateGroupQuery = "UPDATE `hvzgroups` SET `groupname`='$EditedGroupName',`groupsubtitle`='$EditedGroupSubtitle',`grouptext`='$EditedGroupDescription'";
	
	if (isset($_POST["RemGrpIcon"]))
		{
		$UpdateGroupQuery = $UpdateGroupQuery . ",`grouppic`=''";
		
		// If there is an icon, delete it.
		if ($HiddenIconName != "")
			{
			unlink('Images/GroupImages//' . $HiddenIconName);
			}
		}
	
	$UpdateGroupQuery = $UpdateGroupQuery . "WHERE `groupid` = $GroupID";
	
	// Run the update Query
	mysqli_query($DBCon, $UpdateGroupQuery);
	}
else if (isset($_POST["KillGroup"]) && $_POST["KillGroup"] == "DoIt")
	{
	$GroupID = $_POST["InvisoID"];
	$HiddenIconName = $_POST["InvisoIcon"];
	$GroupType = $_POST["InvisoType"];
	
	// Remove the group's icon first.
	if ($HiddenIconName != "")
		{
		unlink('Images/GroupImages//' . $HiddenIconName);
		}
		
	// Get everyone out of the group
	// Zombie group
	if ($GroupType == 0)
		{
		$GetEveryoneOutQuery = "UPDATE `hvzusrinfo` SET `usrgroupdeadId`=NULL,`usrgroupdeadtitle`='' WHERE `usrgroupdeadId` = $GroupID";
		}
	// Human Group
	else if ($GroupType == 1)
		{
		$GetEveryoneOutQuery = "UPDATE `hvzusrinfo` SET `usrgroupliveId`=NULL,`usrgrouplivetitle`='' WHERE `usrgroupliveId` = $GroupID";
		}
	// Run clearing query
	mysqli_query($DBCon, $GetEveryoneOutQuery);
		
	// Destroy the group
	$RemoveGroupQuery = "DELETE FROM `hvzgroups` WHERE `groupid` = $GroupID";
	// Run the removal Query
	mysqli_query($DBCon, $RemoveGroupQuery);
	}
	
// Update's done - back to the admin view
header("Location: admincomunity.php");
die();
?>