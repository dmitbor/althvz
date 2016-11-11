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
		<link href="CSS/arsenal.css" rel="Stylesheet" type="text/css">
	</head>
	<body>
	<?php
	// TopBar + Log Out
	include 'pagetopper.php';
	
	// Rules Go Here:
	echo "<div class=\"RulesDiv\">";
	echo "<p class=\"rulestopper\">Arsenal Rules:</p>";
	echo "<p class=\"rules\">1. Select an item or a number of items by pressing the Claim Button.</p>";
	echo "<p class=\"rules\">2. A Mod will then consider whenever to accept your claim.</p>";
	echo "<p class=\"rules\">3. If accepted, come up to the Mods during the next Human Mission to pay the rental fee and recieve your item.</p>";
	echo "<p class=\"rules\">4. If declined, you may either try again or inquire with Mods on the next mission.</p>";
	echo "<p class=\"rules\">5. When you become a Zombie, please return your rentals as soon as possible. You will be notified of your remaining rented items on this screen.</p>";
	echo "</div>";
	
	echo "<div class=\"ArsenalWeaponList\">";
	// Get all available weaponry.
	$GetWeaponListQuery = "SELECT `wpnid`,`wpnname`,`wpnpic`,`wpnnum`,`wpncost` FROM `hvzarsenalitems`";
	$WeaponsResult = mysqli_query($DBCon, $GetWeaponListQuery);
	
	while($WeaponRow = mysqli_fetch_array($WeaponsResult))
		{
		$WeaponID = $WeaponRow[0];
		$GetTakeWeapon = "SELECT count(*) FROM `hvzarsenalclaims` WHERE `wpnid` = $WeaponID AND `claimstate` > 0";
		$TakenOf = mysqli_fetch_array(mysqli_query($DBCon, $GetTakeWeapon))[0];
		$GetWeaponClaim = "SELECT `claimstate`,`claimid` FROM `hvzarsenalclaims` WHERE `wpnid` = $WeaponID and `claimerid` = $MyId";
		$ClaimResult = mysqli_query($DBCon, $GetWeaponClaim);
		// No Claim found.
		if ($ClaimResult->num_rows<1)
			{
			$ClaimState = -1;
			}
		// If there is a claim of any kind.
		else
			{
			$ClaimArray =  mysqli_fetch_array($ClaimResult);
			$ClaimState = $ClaimArray[0];
			$ClaimID = $ClaimArray[1];
			}
		
		echo "<form class=\"WeaponEntry\" action=\"arsenalhandler.php\" method=\"post\">";
		if ($WeaponRow[2] == "")
			{
			echo "<img class=\"ItemIMG\" name=\"ItemIMG\" src=\"Images/ArsenalPics/defaultpic.png\">";
			}
		else
			{
			echo "<img class=\"ItemIMG\" name=\"ItemIMG\" src=\"Images/ArsenalPics//" . $WeaponRow[2] . "\">";
			}
		echo "<p class=\"WpnName\">" . $WeaponRow[1] . "</p>";
		echo "<p class=\"WpnRent\">Rent Price: $" . $WeaponRow[4] . "</p>";
		echo "<p class=\"WpnNumber\">Remaining: " . ($WeaponRow[3] - $TakenOf) . "</p>";
		// We have not made any claims to this weapon:
		if ($ClaimState == -1 && ($WeaponRow[3] - $TakenOf) > 0)
			{
			echo "<input type=\"hidden\" id=\"WeaponID\" name=\"WeaponID\" value=\"" . $WeaponID . "\">";
			echo "<p class=\"ClaimState\" style=\"background-color: #C9C1B8;\">You may make a claim for this Item</p>";
			echo "<input type=\"submit\" id=\"MakeClaim\" name=\"MakeClaim\" value=\"Claim\" class=\"ClaimBtn\">";
			}
		// We made a claim for this weapon:
		else if ($ClaimState == 0)
			{
			echo "<input type=\"hidden\" id=\"ClaimID\" name=\"ClaimID\" value=\"" . $ClaimID . "\">";
			echo "<p class=\"ClaimState\" style=\"border-color: #E87511; background-color: #E0AA0F;\">You have made a claim for this Item.</p>";
			echo "<input type=\"submit\" id=\"UnClaim\" name=\"UnClaim\" value=\"UnClaim\" class=\"ClaimBtn\">";
			}
		// Following two have no buttons - user will always be reminded of this
		// Claim was accepted, we can return at any time
		else if ($ClaimState == 1)
			{
			echo "<p class=\"ClaimState2\" style=\"border-color: #4B721D; background-color: #A9C398; color: #4B721D;\">Your claim was approved.<br><br>Please check with Mods to recieve your claimed Item.</p>";
			}
		else if ($ClaimState == 2)
			{
			echo "<p class=\"ClaimState2\" style=\"border-color: #CE1126; background-color: #E0AA0F; color: #CE1126;\">You are a zombie.<br><br>Please return your rented Items to Mods.</p>";
			}
		// Claim Denied. Wah!
		else if ($ClaimState == 3)
			{
			echo "<p class=\"ClaimState2\" style=\"border-color: #CE1126; background-color: #E7D8AC; color: #AF1E2D;\">Your claim was denied.<br><br>Check in with Mods for more information.</p>";
			// Once a player sees this, they can try again once page is reset:
			$RemoveFailedClaims = "DELETE FROM `hvzarsenalclaims` WHERE `wpnid` = $WeaponID AND `claimerid` = $MyId AND `claimstate` = 3";
			mysqli_query($DBCon, $RemoveFailedClaims);
			}
		echo "</form>";
		}
	echo "</div>";
	
	$GetMineClaimedWeapons = "SELECT `wpnname`,`wpncost` FROM `hvzarsenalclaims` LEFT JOIN `hvzarsenalitems` ON `hvzarsenalclaims`.`wpnid` = `hvzarsenalitems`.`wpnid` WHERE `claimerid` = $MyId and `claimstate` > 0 AND `claimstate` < 3";
	$GetMyClaims = mysqli_query($DBCon, $GetMineClaimedWeapons);
	$TotalRent = 0;
	
	if ($GetMyClaims->num_rows>0)
		{
		echo "<div class=\"ClaimsMadeDiv\">";
			echo "<div class=\"ClaimsListTop\">";
				echo "<p class=\"ClaimedItemName\">Rented Item:</p>";
				echo "<p class=\"ClaimedItemPrice\">Rent Charge:</p>";
			echo "</div>";
			echo "<table class=\"ClaimsTable\">";
			while($ClaimRow = mysqli_fetch_array($GetMyClaims))
				{
				$TotalRent += $ClaimRow[1];
				echo "<tr>";
				echo "<td class=\"NameCell\">";
				echo $ClaimRow[0];
				echo "</td>";
				echo "<td class=\"PriceCell\">";
				echo "$" . $ClaimRow[1];
				echo "</td>";
				echo "</tr>";
				}
			echo "</table>";
			echo "<div class=\"ClaimsListBot\">";
				echo "<p class=\"ClaimedTotal\">Total: $" . $TotalRent . "</p>";
			echo "</div>";
		echo "</div>";
		}
	?>
	</body>
</html>