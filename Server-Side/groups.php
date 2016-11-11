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
		<link href="CSS/groups.css" rel="Stylesheet" type="text/css">
	</head>
	<body>
	<?php
	// TopBar + Log Out
	include 'pagetopper.php';
	
	// Get my groups:
	$GetGroupQuery = "SELECT `usrgroupliveId`,`usrgroupdeadId` FROM `hvzusrinfo` WHERE `usrid` = $MyId";
	$LiveTeam = mysqli_fetch_row(mysqli_query($DBCon, $GetGroupQuery))[0];
	$DeadTeam = mysqli_fetch_row(mysqli_query($DBCon, $GetGroupQuery))[1];
	
	
	// We are looking for group and not at a group
	if ((($UserTeam < 2 && $DeadTeam == NULL) || ($UserTeam > 1 && $LiveTeam == NULL)) && !isset($_GET["GroupId"]))
		{
		echo "<div class=\"GroupsSelDiv\">";
		// Get all Zombie Groups
		if ($UserTeam < 2)
			{
			$GetGroups = "SELECT `groupid`,`groupname`,`groupsubtitle`,`grouptext`,`grouppic` FROM `hvzgroups` WHERE `grouptype` = 0";
			}
		// Get all human Groups
		else if ($UserTeam > 1)
			{
			$GetGroups = "SELECT `groupid`,`groupname`,`groupsubtitle`,`grouptext`,`grouppic` FROM `hvzgroups` WHERE `grouptype` = 1";
			}
		$GroupResults = mysqli_query($DBCon, $GetGroups);
		
		while($GroupRow = mysqli_fetch_array($GroupResults))
			{
			echo "<form action=\"grouphandler.php\" name=\"GroupSelectionForm\" method=\"post\" class=\"GroupForm\">";
			echo "<a class=\"BlackLink\" href=\"groups.php?GroupId=" . $GroupRow[0] . "\">";
			if ($GroupRow[4] == "")
				{
				echo "<img src=\"Images/GroupImages/Default.png\" class=\"GroupSelectionAvy\">";
				}
			else 
				{
				echo "<img src=\"Images/GroupImages/" . $GroupRow[4] . "\" class=\"GroupSelectionAvy\">";
				}
			echo "<input type=\"submit\" id=\"JoinGroup\" name=\"JoinGroup\" value=\"Join Group\" class=\"JoinGroup\">";
			echo "<p class=\"GroupSelectTitle\">" . $GroupRow[1] . "</p>";
			echo "</a>";
			echo "<p class=\"GroupSelectSubTitle\">" . $GroupRow[2] . "</p>";
			echo "<p class=\"GroupSelectDesc\">" . $GroupRow[3] . "</p>";
			echo "<p class=\"GroupSelectMembersTxt\">Members:</p>";
			echo "<input type=\"hidden\" id=\"GroupID\" name=\"GroupID\" value=\"" . $GroupRow[0] . "\">";
			echo "<input type=\"hidden\" id=\"UserTeam\" name=\"UserTeam\" value=\"" . $UserTeam . "\">";
			echo "<div class=\"GroupMembersDiv\">";
			if ($UserTeam > 1)
				{
				$GetGroupMembers = "SELECT `usrid`,`usravy`,`usrname` FROM `hvzusrinfo` LEFT JOIN `hvzuserstate` ON `usrid`=`userid` WHERE `usrgroupliveId` = " . $GroupRow[0] . " AND `userteam` > 1 ORDER BY RAND() LIMIT 6";
				}
			else if ($UserTeam < 2)
				{
				$GetGroupMembers = "SELECT `usrid`,`usravy`,`usrname` FROM `hvzusrinfo` LEFT JOIN `hvzuserstate` ON `usrid`=`userid` WHERE `usrgroupdeadId` = " . $GroupRow[0] . " AND `userteam` < 2 ORDER BY RAND() LIMIT 6";
				}
			$MemberResults = mysqli_query($DBCon, $GetGroupMembers);
			while($MemberRow = mysqli_fetch_array($MemberResults))
				{
				echo "<a href=\"playerstats.php?profId=" . $MemberRow[0] . "\">";
				if ($MemberRow[1] != "")
					{
					echo "<img class=\"MemberSelectAvy\" alt=\"" . $MemberRow[2] . "\" title=\"" . $MemberRow[2] . "\" src=\"Images/Avatars//" . $MemberRow[1] .  "\">";
					}
				else if ($UserTeam > 1)
					{
					echo "<img class=\"MemberSelectAvy\" alt=\"" . $MemberRow[2] . "\" title=\"" . $MemberRow[2] . "\" src=\"Images/DefaultAvatars/DefaultHumanAv.png\">";
					}
				else if ($UserTeam < 2)
					{
					echo "<img class=\"MemberSelectAvy\" alt=\"" . $MemberRow[2] . "\" title=\"" . $MemberRow[2] . "\" src=\"Images/DefaultAvatars/DefaultZombieAv.png\">";
					}
				echo "</a>";
				}
			echo "</div>";
			echo "</form>";
			}
		echo "</div>";
		// New Group Creation Side Div
		echo "<form action=\"grouphandler.php\" name=\"GroupSelectionForm\" method=\"post\" class=\"NewGroupSide\">";
		// New Human Group
		if ($UserTeam > 1)
			{
			echo "<p class=\"NewGroupTxtTop\">New Human Group</p>";
			echo "<input type=\"hidden\" id=\"NewGroupType\" name=\"NewGroupType\" value=\"1\">";
			}
		// New Zombie Horde
		else if ($UserTeam < 2)
			{
			echo "<p class=\"NewGroupTxtTop\">New Zombie Horde</p>";
			echo "<input type=\"hidden\" id=\"NewGroupType\" name=\"NewGroupType\" value=\"0\">";
			}
		echo "<p class=\"NewGroupTxtOne\">New Group Name:</p>";
		echo "<input type=\"text\" id=\"NewGroupName\" name=\"NewGroupName\" class=\"NewGroupName\" value=\"\" maxlength=\"20\" autocomplete=\"off\">";
		echo "<p class=\"NewGroupTxtTwo\">New Group Subtitle:</p>";
		echo "<input type=\"text\" id=\"NewGroupSubTitle\" name=\"NewGroupSubTitle\" class=\"NewGroupSubTitle\" value=\"\" maxlength=\"30\" autocomplete=\"off\">";
		echo "<p class=\"NewGroupTxtTri\">New Group Description:</p>";
		echo "<textarea id=\"NewGroupDesc\" name=\"NewGroupDesc\" class=\"NewGroupDesc\" rows=\"5\" autocomplete=\"off\"></textarea>";
		echo "<input type=\"submit\" id=\"NewGroup\" name=\"NewGroup\" value=\"Create Group\" class=\"NewGroup\">";
		echo "</form>";
		}
	// We're looking at group stats
	else
		{
		if (isset($_GET["GroupId"]))
			{
			$ShowGroupID = $_GET["GroupId"];
			}
		// We Zoomba
		else if ($UserTeam < 2)
			{
			$ShowGroupID = $DeadTeam;
			}
		// We live
		else if ($UserTeam > 1)
			{
			$ShowGroupID = $LiveTeam;
			}
			
		$GetAllGroupInfoQuery = "SELECT `leaderId`,`grouptype`,`groupname`,`groupsubtitle`,`grouptext`,`grouppic` FROM `hvzgroups` WHERE `groupid` = $ShowGroupID";
		$GroupInfo = mysqli_fetch_row(mysqli_query($DBCon, $GetAllGroupInfoQuery));
		$LeaderID = $GroupInfo[0];
		$GetLeaderInfoQuery = "SELECT `usrname`,`usravy`,`usrgrouplivetitle`,`usrgroupdeadtitle`,`userteam` FROM `hvzusrinfo` LEFT JOIN `hvzuserstate` ON `hvzusrinfo`.`usrid` = `hvzuserstate`.`userid` WHERE `usrid` = $LeaderID";
		$LeaderInfo = mysqli_fetch_row(mysqli_query($DBCon, $GetLeaderInfoQuery));
		
		echo "<div class=\"GroupShow\">";
			// No Group Icon, use default
			if ($GroupInfo[5] == "")
				{
				echo "<img src=\"Images/GroupImages/Default.png\" class=\"GroupAvy\">";
				}
			else
				{
				echo "<img src=\"Images/GroupImages/" . $GroupInfo[5] . "\" class=\"GroupAvy\">";
				}
				
			echo "<form action=\"grouphandler.php\" name=\"quitgroupform\" method=\"post\" class=\"quitgroupform\">";
			echo "<input type=\"hidden\" id=\"UserTeam\" name=\"UserTeam\" value=\"" . $UserTeam . "\">";
			echo "<input type=\"hidden\" id=\"GroupID\" name=\"GroupID\" value=\"" . $ShowGroupID . "\">";
			echo "<input type=\"hidden\" id=\"GroupType\" name=\"GroupType\" value=\"" . $GroupInfo[1] . "\">";
			echo "<input type=\"hidden\" id=\"GroupLeaderID\" name=\"GroupLeaderID\" value=\"" . $LeaderID . "\">";
			// If not in group, we can join it.
			if (($DeadTeam == "" && $GroupInfo[1] == 0) || ($LiveTeam == "" && $GroupInfo[1] == 1))
				{
				echo "<input type=\"submit\" id=\"JoinGroup\" name=\"JoinGroup\" value=\"Join Group\" class=\"QuitGroup\">";
				}
			// If we are in the group, give us the option to get out
			else if (($ShowGroupID == $DeadTeam) || ($ShowGroupID == $LiveTeam))
				{
				// Zombie Leave
				if ($ShowGroupID == $DeadTeam)
					{
					echo "<input type=\"submit\" id=\"QuitGroup\" name=\"QuitGroup\" value=\"Leave Horde\" class=\"QuitGroup\">";
					}
				// Human Leave
				else if ($ShowGroupID == $LiveTeam)
					{
					echo "<input type=\"submit\" id=\"QuitGroup\" name=\"QuitGroup\" value=\"Leave Squad\" class=\"QuitGroup\">";
					}
				}
			echo "</form>";
				
			echo "<p class=\"GroupTitle\">" . $GroupInfo[2] . "</p>";
			echo "<p class=\"GroupSubTitle\">" . $GroupInfo[3] . "</p>";
			echo "<p class=\"GroupDesc\">" . nl2br($GroupInfo[4]) . "</p>";
			if ($GroupInfo[1] == 1)
				{			
				echo "<p class=\"GroupLeaderTxt\">Leader:</p>";
				}
			else if ($GroupInfo[1] == 0)
				{			
				echo "<p class=\"GroupLeaderTxt\">Alpha:</p>";
				}
			// Start the link to the Great Leader:	
			echo "<a href=\"playerstats.php?profId=" . $LeaderID . "\" class=\"BlackLink\">";
			// Use Leader's Avatar, since they have one
			if ($LeaderInfo[1] != "")
				{
				echo "<img src=\"Images/Avatars/" . $LeaderInfo[1] . "\" class=\"AdminAvy\">";
				}
			// Generic Zombie Avy if it's a zombie group
			else if ($GroupInfo[1] == 0)
				{
				echo "<img class=\"AdminAvy\" src=\"Images/DefaultAvatars/DefaultZombieAv.png\">";
				}
			// Generic Zombie Avy if it's a human group
			else if ($GroupInfo[1] == 1)
				{
				echo "<img class=\"AdminAvy\" src=\"Images/DefaultAvatars/DefaultHumanAv.png\">";
				}
				
		echo "<p class=\"GroupLeaderName\">" . $LeaderInfo[0] . "</p>";
		
		// Zambee Group Nickname
		if ($GroupInfo[1] == 0)
			{
			if ($LeaderInfo[3] != "")
				{
				echo "<p class=\"GroupLeaderNickName\">(" . $LeaderInfo[3] . ")</p>";
				}
			// On starved:
			if ($LeaderInfo[4] < 0)
				{
				echo "<p class=\"GroupLeaderDedZombie\">Slain</p>";
				}
			}
		// Hummie Group Nickname
		else if ($GroupInfo[1] == 1)
			{
			if ($LeaderInfo[2] != "")
				{
				echo "<p class=\"GroupLeaderNickName\">(" . $LeaderInfo[2] . ")</p>";
				}
			// On Dead:
			if ($LeaderInfo[4] < 2)
				{
				echo "<p class=\"GroupLeaderDedHumies\">M.I.A.</p>";
				}
			}
		echo "</a>";
		echo "</div>";
		// Finding everyone not ourselves or the admin.
		// Finding members of a human group:
		if ($GroupInfo[1] == 1)
			{
			$GetNonLeaderGroupMembers = "SELECT `usrid`,`usrname`,`usrgrouplivetitle`,`usravy` FROM `hvzusrinfo` LEFT JOIN `hvzuserstate` ON `hvzusrinfo`.`usrid` = `hvzuserstate`.`userid` WHERE `usrgroupliveId` = $ShowGroupID AND NOT `usrid` = $LeaderID AND NOT `usrid` = $MyId AND `userteam` > 1 ORDER BY RAND()";
			}
		// Zoombas group:
		else if ($GroupInfo[1] == 0)
			{
			$GetNonLeaderGroupMembers = "SELECT `usrid`,`usrname`,`usrgroupdeadtitle`,`usravy` FROM `hvzusrinfo` LEFT JOIN `hvzuserstate` ON `hvzusrinfo`.`usrid` = `hvzuserstate`.`userid` WHERE `usrgroupdeadId` = $ShowGroupID AND NOT `usrid` = $LeaderID AND NOT `usrid` = $MyId AND `userteam` < 2 ORDER BY RAND()";
			}
			
		// Get the selected members.
		$MembersResult = mysqli_query($DBCon, $GetNonLeaderGroupMembers);
		
		// If we are a leader, make this a bigger div for extra buttons:
		if ($MyId == $LeaderID)
			{
			echo "<div class=\"GroupMembersAdmin\">";
			}
		else
			{
			echo "<div class=\"GroupMembers\">";
			}
			echo "<div class=\"GroupMembersSub\">";
			// Set up the Group Members
			while($MemberRow = mysqli_fetch_array($MembersResult))
				{
				echo "<form action=\"grouphandler.php\" name=\"groupchangeform\" method=\"post\" class=\"MemberDiv\">";
				echo "<a href=\"playerstats.php?profId=" . $MemberRow[0] . "\" class=\"BlackLink\">";
				echo "<p class=\"MemberTitle\">" . $MemberRow[2] . "</p>";
				// Let us clear the Title as Admin
				if ($MyId == $LeaderID)
					{
					echo "<input type=\"submit\" id=\"RemoveMemberTitleBtn\" name=\"RemoveMemberTitleBtn\" value=\"Clear Title\" class=\"RemoveMemberTitleBtn\">";
					echo "<input type=\"hidden\" id=\"GroupID\" name=\"GroupID\" value=\"" . $ShowGroupID . "\">";
					echo "<input type=\"hidden\" id=\"GroupType\" name=\"GroupType\" value=\"" . $GroupInfo[1] . "\">";
					}
				if ($MemberRow[3] != "")
					{
					echo "<img src=\"Images/Avatars/" . $MemberRow[3] . "\" class=\"MemberAvy\">";
					}
				else if ($GroupInfo[1] == 0)
					{
					echo "<img src=\"Images/DefaultAvatars/DefaultZombieAv.png\" class=\"MemberAvy\">";
					}
				else if ($GroupInfo[1] == 1)
					{
					echo "<img src=\"Images/DefaultAvatars/DefaultHumanAv.png\" class=\"MemberAvy\">";
					}
				// Let us clear the Title as Admin
				if ($MyId == $LeaderID)
					{
					echo "<input type=\"submit\" id=\"KickMemberBtn\" name=\"KickMemberBtn\" value=\"Kick Out\" class=\"KickMemberBtn\">";
					echo "<input type=\"hidden\" id=\"MemberID\" name=\"MemberID\" value=\"" . $MemberRow[0] . "\">";
					echo "<input type=\"submit\" id=\"GiveAdminBtn\" name=\"GiveAdminBtn\" value=\"Make Leader\" class=\"GiveAdminBtn\">";
					}
				echo "<p class=\"MemberName\">" . $MemberRow[1] . "</p>";
				echo "</a>";
				echo "</form>";
				}
			echo "</div>";
		echo "</div>";
		// If we are an admin, we can also change Groups info
		if ($MyId == $LeaderID)
			{
			echo "<form action=\"grouphandler.php\" class=\"GroupAdmin\" method=\"post\" enctype=\"multipart/form-data\">";
			echo "<p class=\"GroupAdmText\">Group Administration Options:</p>";
			echo "<p class=\"GroupNmTxt\">Group Name:</p>";
			echo "<input type=\"text\" id=\"GroupNameText\" name=\"GroupNameText\" class=\"GroupNameText\" value=\"" . $GroupInfo[2] . "\" maxlength=\"20\" autocomplete=\"off\">";
			echo "<p class=\"GroupSubNmTxt\">Group Subtitle:</p>";
			echo "<input type=\"text\" id=\"GroupSubNameText\" name=\"GroupSubNameText\" class=\"GroupSubNameText\" value=\"" . $GroupInfo[3] . "\" maxlength=\"30\" autocomplete=\"off\">";
			echo "<p class=\"GroupIconTxt\">Upload Group Icon:</p>";
			echo "<input type=\"file\" name=\"GroupIconUp\" id=\"GroupIconUp\" class=\"GroupIconUp\">";
			echo "<p class=\"ClearIconAdmin\"><input type=\"checkbox\" name=\"ClearIcon\" value=\"ClearIcon\">Clear Group Icon?</p>";
			echo "<p class=\"GroupDescTxt\">Group Description:</p>";
			echo "<textarea id=\"GroupDescTextArea\" name=\"GroupDescTextArea\" class=\"GroupDescTextArea\" rows=\"5\" autocomplete=\"off\">" . $GroupInfo[4] . "</textarea>";
			echo "<p class=\"MyAdminTitle\">Change My Title:</p>";
			echo "<input type=\"hidden\" id=\"GroupID\" name=\"GroupID\" value=\"" . $ShowGroupID . "\">";
			echo "<input type=\"hidden\" id=\"GroupAvy\" name=\"GroupAvy\" value=\"" . $GroupInfo[5] . "\">";
			echo "<input type=\"hidden\" id=\"GroupType\" name=\"GroupType\" value=\"" . $GroupInfo[1] . "\">";
			// Zombie Title
			if ($UserTeam < 2)
				{
				echo "<input type=\"text\" id=\"MyNewAdminTitle\" placeholder=\"My Title\" name=\"MyNewAdminTitle\" class=\"MyNewAdminTitle\" value=\"" . $LeaderInfo[3] . "\" maxlength=\"20\" autocomplete=\"off\">";
				}
			// Human Title
			else if ($UserTeam > 1)
				{
				echo "<input type=\"text\" id=\"MyNewAdminTitle\" placeholder=\"My Title\" name=\"MyNewAdminTitle\" class=\"MyNewAdminTitle\" value=\"" . $LeaderInfo[2] . "\" maxlength=\"20\" autocomplete=\"off\">";
				}
			echo "<select id=\"TitleSelectAdmin\" class=\"TitleSelectAdmin\" name=\"TitleSelectAdmin\">";
				echo "<option value=\"0\">Don't Change</option>";
				echo "<option value=\"1\">Change to Given</option>";
				echo "<option value=\"2\">Clear</option>";
				echo "<option value=\"3\">Foxhound (Weapon/Animal)</option>";
				echo "<option value=\"4\">MSF (Adjective/Animal)</option>";
			echo "</select>";
			echo "<input type=\"submit\" id=\"SaveGroupAdmin\" name=\"SaveGroupAdmin\" value=\"Save Changes\" class=\"SaveGroupAdmin\">";
			echo "</form>";
			}
		else if ($LiveTeam == $ShowGroupID || $DeadTeam == $ShowGroupID)
			{
			if ($UserTeam < 2)
				{
				$MyTitleQuery = "SELECT `usrgroupdeadtitle` FROM `hvzusrinfo` WHERE `usrid` = $MyId";
				}	
			else if ($UserTeam > 1)
				{
				$MyTitleQuery = "SELECT `usrgrouplivetitle` FROM `hvzusrinfo` WHERE `usrid` = $MyId";
				}
			$GetMyTitle = mysqli_fetch_row(mysqli_query($DBCon, $MyTitleQuery))[0];
			echo "<form action=\"grouphandler.php\" class=\"NicknameMngr\" name=\"NickGiver\" method=\"post\">";
			echo "<input type=\"text\" id=\"MyNewTitle\" placeholder=\"My Title\" name=\"MyNewTitle\" class=\"MyNewTitle\" value=\"" . $GetMyTitle . "\" maxlength=\"20\" autocomplete=\"off\">";
			echo "<select id=\"TitleSelect\" class=\"TitleSelect\" name=\"TitleSelect\">";
				echo "<option value=\"0\">Don't Change</option>";
				echo "<option value=\"1\">Change to Given</option>";
				echo "<option value=\"2\">Clear</option>";
				echo "<option value=\"3\">Foxhound (Weapon/Animal)</option>";
				echo "<option value=\"4\">MSF (Adjective/Animal)</option>";
			echo "</select>";
			echo "<input type=\"hidden\" id=\"GroupType\" name=\"GroupType\" value=\"" . $GroupInfo[1] . "\">";
			echo "<input type=\"submit\" id=\"SaveTitle\" name=\"SaveTitle\" value=\"Save\" class=\"SaveTitle\">";
			echo "</form>";
			}
		}
	?>
	</body>
</html>