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
		<link href="CSS/rules.css" rel="Stylesheet" type="text/css">
	</head>
	<body>
	<?php
	// TopBar + Log Out
	include 'pagetopper.php';
	echo "<div class=\"RulesTopDiv\">";
	echo "<A href=\"#GenRules\"><input type=\"submit\" id=\"GenRuleBtn\" name=\"GenRuleBtn\" value=\"General Rules\" class=\"GenRuleBtn\"></a>";
	echo "<A href=\"#GameRules\"><input type=\"submit\" id=\"GameRuleBtn\" name=\"GameRuleBtn\" value=\"Gameplay Rules\" class=\"GameRuleBtn\"></a>";
	echo "<A href=\"#WebRules\"><input type=\"submit\" id=\"WebRuleBtn\" name=\"WebRuleBtn\" value=\"Web Site Usage Rules\" class=\"WebRuleBtn\"></a>";
	echo "<A href=\"#LglRules\"><input type=\"submit\" id=\"LglRuleBtn\" name=\"LglRuleBtn\" value=\"Legal Matters\" class=\"LglRuleBtn\"></a>";
	echo "</div>";
	echo "<div class=\"RulesDiv\">";
	echo "<p class=\"TypeTitle\" id=\"GenRules\">General Rules:</p>";
	echo "<p>1 - <b>MOST IMPORTANTLY:</b> Do not be a douchebag. Use common sense. Failure to do so makes you a sad human being and ultimately may get you kicked out of the game.</p>";
	echo "<p class=\"Subpar\">a) Please do not constantly insult your opposition; in fact, try not to do that at all. We're all doing this for fun, so be friendly.</p>";
	echo "<p class=\"Subpar\">b) If you get tagged, please provide your tag code immediately. If you have any disagreements over being tagged, Time Out and call up a Mod.</p>";
	echo "<p class=\"Subpar\">c) Please notify Mods <b>before the game</b> if you are using modified Blasters. No realistic paint jobs or realistic looking Blasters, especially realistic looking metallic paints. All Blasters must have an orange tip marking them as toys.</p>";
	echo "<p class=\"Subpar\">d) <b>Do not under any circumstances use modified ammo!</b></p>";
	echo "<p class=\"Subpar\">e) Do not play around cars or use them to move around the campus. While bicycles and skateboards may be used, you are out of play as long as you are on them. This means that you may not tag or shoot. Remember that being at a bike rack will count as being on foot and so you are back in play!</p>";
	echo "<p>2 - Do not engage Non-Players in the game.</p>";
	echo "<p class=\"Subpar\">a) Do not shoot Non-Players with Nerf Guns (do not throw socks at them, either). Time Out and apologize for any accidents that may have happened (the game, after all, takes place in a middle of a college campus), but repeated offense with gets you banned from the game.</p>";
	echo "<p class=\"Subpar\">b) Do not use Non-Players as barriers from Darts/Socks/Zombies/Other Objects Thrown at You by Panicking Human (In that specific case, Time Out and notify the Mods as soon as you can). It doesn't matter if they want to, they are either playing the game or just watching it.</p>";
	echo "<p class=\"Subpar\">c) Do not use Non-Players to scout out the area for you. If you are unsure if there is a Zombie outside the door, either take the chance or just wait it out until the mission starts. As for Zombies, finding the prey is the best part of the hunt.</p>";
	echo "<p class=\"Subpar\">d) Do not use Non-Players to provide you with supplies through the game. If you want to hide out in a dorm pretending that it's a fallout shelter, by all means do so, but do not try to “participate” in the game at the same time.</p>";
	echo "<p>3 - Only actively participate in the game within the Game Zone, specifically in the boundaries of the Campus.</p>";
	echo "<p class=\"Subpar\">a) If any player leaves the Game Zone, they may not continue playing the game until they have returned into the Game Zone. Survivors that spend 24 hours off campus will become Mercenaries.</p>";
	echo "<p class=\"Subpar\">b) Zombies should not follow Humans out of Game Zone, especially across the roads. If you are chasing a Human who crosses a road, just stop. If the other side of the road is in play, then only a Zombie on that side should go for the Human. <b>Do not stalk humans to keep them on the road.</b> Time Out immediately to let them cross to one side, give them 3 seconds of head start, and resume the game. Humans should <b>never</b> run across the road to escape Zombies. Please notify Mods of any offenders.</p>";
	echo "<p class=\"Subpar\">c) Equally, Humans should not shoot at Zombies from outside the Game Zone. It is also important that you keep your Blasters hidden when you are outside of the Game Zone, as not to cause annoyance or panic. Especially when you are going to classes – even if the professor does not mind the fact that you brought a Blaster into classroom (we all have had at least one such cool professor), it's still a distraction. Blasters should NOT be visible inside any building unless you are on a indoor mission.</p>";
	echo "<p>4 - While in Safe Zones, there will be no active game interactions between players.</p>";
	echo "<p class=\"Subpar\">a) Safe Zones are defined as such: Indoors (Dorms, Academic Buildings, Workshops), Outdoor Classes and Activities (while they are happening), and Areas that have been specifically designated as such by Missions or Mods.</p>";
	echo "<p class=\"Subpar\">b) Borders (Such as Doors, Windows <b>(DO NOT ENTER AND LEAVE BUILDINGS THROUGH WINDOWS!)</b>) are not Safe Zones by themselves: Humans can shoot at Zombies who have <b>both feet</b> outside of the Safe Zone. Zombies can tag Humans inside the safe zones if they have <b>both feet</b> outside of the Safe Zone. If members of both factions are inside of the safe zone, no tagging or shooting between them may occur.</p>";
	echo "<p class=\"Subpar\">c) If a player must go to a class activity that is Outdoors, they are considered to be in a Safe Zone as long as the Class, Athletic Activity, or Work is ongoing. On the way to and from the events, however, you are in play and, as such, free game.</p>";
	echo "<p class=\"Subpar\">d) Similarly, if you have to deal with transport of large, heavy, or fragile objects for your classes, you are considered to be in a Safe Zone. However, you may not exploit this rule by bringing such items with you everywhere during the game. Mod Discretion is in use in such cases.</p>";
	echo "<p class=\"TypeTitle\" id=\"GameRules\">Game Rules and Definitions:</p>";
	echo "<p>1 - Gameplay: Humans</p>";
	echo "<p class=\"Subpar\">a) You must have a Blaster (and/or <b>clean</b> Socks) and a Tag Card. For the entire week, you go about your daily life trying to not get tagged by Zombies. If you get tagged, you surrenders your Tag Card, and you then become a zombie. You win if all the Zombies Starve Out or if Humans win The Final Mission. You lose if all Survivors (Non-Mercenary Humans) get tagged or if the Humans lose The Final Mission.</p>";
	echo "<p class=\"Subpar\">b) You are allowed to use any Dart Blaster that the Mods have approved (majority of Nerf and Buzzbee Blaster fall under this), but check in with mods just in case. <b>Modified Blasters must be checked by Mods</b> before they are allowed to be used.</p>";
	echo "<p class=\"Subpar\">c) Always carry your Tag Card with you. If you get tagged and you do not have it on your person, try to give to or get the contact info of the person whom has tagged you, so you can provide them with the Tag Code at later time. If for some reason you cannot, inform the Mods, so they may handle the situation as appropriate. False Codes will be prosecuted.</p>";
	echo "<p class=\"Subpar\">d) As a Human, you may use your Blaster to hit Zombies (or Socks if that is how you roll). <b>Do not aim for the head</b>, doing so will bring great fury from the Mods, which may lead to getting kicked out from the game. Any zombie that was hit by a Dart or a Sock, are stunned and are out of the game for 15 minutes. Zombies stunned on a mission will go to the Dead Box, but will inevitably return, so stay on your guard.</p>";
	echo "<p class=\"Subpar\">e) When in the Game Zone, always wear your Armband. You may not wear clothing which match it in color or hide it from view, by any means.</p>";
	echo "<p class=\"Subpar\">f) When you are tagged, you are out of play for 1 Hour. For this time, you may not interact with the game in any way. Once the hour passes, you become a proud Zombie: So keep your bandana on your head, and your head up high!</p>";
	echo "<p class=\"Subpar\">g) If you believe you were wrongly tagged, Time Out the game and try to ask a Mod to come in to solve the situation. If Mods are not available and no one surrenders their position, flip a coin:<br><br><b>Heads:</b> You're still alive.<br><b>Tails:</b> It's an hour-long break for you.<br><br>You get 3 seconds head start if it's Heads. Use it wisely.</p>";
	echo "<p class=\"Subpar\">h) If you are feeling adventurous, you may go through a Thunderdome instead of a coin toss:<br><br>Simply have all the zombies in the area stand 15 feet (5 meters preferred, but who here knows metric, anyway?) away from you. Begin a countdown from 5. Once at \"Go!\", the Zombies charge at the Human. The Human must try to take down the Zombies before they tag them.<br><br>If the Human succeeds, they have 10 seconds head start. If the Zombies tag the Human, they are the true winners, and another member joins the horde!</p>";
	echo "<p class=\"Subpar\">i) If you are tagged during a Mission, you immediately become a Zombie and begin your brain-munching career from the Dead Box. You get right into the thick of the Undead Horde (although you can only register your Tags after whomever tagged you registered their Tag)!</p>";
	echo "<p class=\"Subpar\">j) If you have been tagged, but after 24 hours, the tag has still not been registered, contact Mods who will resolve the situation.</p>";
	echo "<p>2 - Gameplay: Mercenaries</p>";
	echo "<p class=\"Subpar\">a) Mercenaries are a Specific group of Humans in the game. To be a Mercenary, you must live or regularly sleep off-campus.</p>";
	echo "<p class=\"Subpar\">b) Mercenaries participate in the game like any other Human, meaning that they can participate in Missions and be tagged in or out of them.</p>";
	echo "<p class=\"Subpar\">c) Any Human who has missed more than two missions or has been off campus for 24 hours will be made Mercenary.</p>";
	echo "<p class=\"Subpar\">d) Tagged Mercenaries become regular zombies, meaning that they can starve as any other Zombie can.</p>";
	echo "<p class=\"Subpar\">e) Mercenaries do not count towards Human victory headcount, meaning that if all Non-Mercenary Humans get tagged, Humans lose the game.</p>";
	echo "<p class=\"Subpar\">f) Mercenaries may never be OZ. You can trust them. Maybe.</p>";
	echo "<p>3 - Gameplay: Zombies</p>";
	echo "<p class=\"Subpar\">a) Zombies try to tag Humans with a <b>firm touch</b>. Any Human tagged as such, must immediately surrender their Tag Card. You must try to register this tag on the Source Site as soon as possible. Holding onto a <b>player's</b> Tag Card for later use is not allowed, and will be persecuted by Mods.</p>";
	echo "<p class=\"Subpar\">b) A Zombie hit by a Nerf Dart or a Sock is a Stunned Zombie. You must immediately disengage from hunting Humans and put your bandana on your neck. Do not block Human shots while you are Stunned. Do not interact with the game while you are Stunned. You may not Tag Humans while you are Stunned. Zombies are stunned for a period of 15 minutes, after which they may put their bandana on their heads and rejoin the game.</p>";
	echo "<p class=\"Subpar\">c) A Zombie stunned during a mission must immediately head for the Dead Box. They will be revived there after a period of time relevant to the Mod's desire (but usually faster than in normal play). It is recommended that you raise your arms when encountering humans while Stunned and traveling to the Dead Box (including, if possible, immediately after being shot).</p>";
	echo "<p class=\"Subpar\">d) A Zombie that has not fed for 48 hours, will Starve Out. Starved Zombies may not participate in the game any longer, and for all intents and purposes are out of the game.</p>";
	echo "<p class=\"Subpar\">e) Every Zombie that has attended a Primary Mission will be fed, thus holding back their inevitable rotting demise. Secondary Missions may provide additional chances to earn ways to keep the hunger at bay.</p>";
	echo "<p>4 - Gameplay: Original Zombie (OZ)</p>";
	echo "<p class=\"Subpar\">a) Original Zombie (OZ) is the only Zombie in the beginning of the game. Unlike every other Zombie in the game, OZ is hidden amongst the survivors and may immediately Tag people from  the game's start. For the first 24 hours, the OZ will wear the armband like any other Survivor, but may tag people like a Zombie,with a firm touch. Everyone who is not a Mercenary is a possible OZ.</p>";
	echo "<p class=\"Subpar\">b) OZ will carry around a Blaster or Socks like a regular Survivor, and may use it like one.</p>";
	echo "<p class=\"Subpar\">c) After 48 hours, the OZ will be revealed and will become a regular Zombie. If the OZ fails 	to get enough tags, their identity may be hidden for longer period of time, as deemed proper by the Mods.</p>";
	echo "<p class=\"Subpar\">d) Original Zombies must volunteer for such a status.</p>";
	echo "<p>5 - Gameplay: Missions</p>";
	echo "<p class=\"Subpar\">a) Missions are a large part of the game, and usually happen during the evenings of the game week. They provide a set of goals for the players to acomplish, as well a backstory to enjoy alongside the action.</p>";
	echo "<p class=\"Subpar\">b) Mods set a specific amount of time before and after the mission as free time, during which no tags can be made by Zombies and no shots can be made by Humans against the Zombies. This time is provided so the players may find their way to the mission start area.</p>";
	echo "<p class=\"Subpar\">c) Mission start area is separate for Humans and Zombies, and will be provided to them by the Mods through a mission description. The players must go to their faction's respective start points and may not preemtively and intentionally search out the opposing faction's Start Point.</p>";
	echo "<p class=\"Subpar\">d) Any humans tagged during a mission, immediately enter the game as Zombies, and must go to the Dead Box for objective run down.</p>";
	echo "<p class=\"Subpar\">e) If you must leave midmission due to any reason, check in with any of the Mods first. When you leave a mission, you are out of play until the mission is over. If you leave without checking in with mods, you will not be counted as having particepated, with expected consequences.</p>";
	echo "<p class=\"TypeTitle\">Legal and Web Rules, Notes, Banther:</p>";
	echo "<p id=\"WebRules\">Web and Source Stuff:</p>";
	echo "<p class=\"Subpar\">a) Please use your real name for the Displayed User Name. If you want to use a Nickname of any sort, you may use a Group Designation for that, instead. Sure, it might be funny to see xXxFunWeedDad69xXx tag a person. However, later you may find out that you have not been marked for coming to the mission and either became Mercenary or have starved, since we couldn't find John \"xXxFunWeedDad69xXx\" Smith anywhere in the pre-mission check-in.</p>";
	echo "<p class=\"Subpar\">b) Do not use the website for explicit, offensive, pornographic, or illegal materials. Mods may modify and delete content on their discretion, and punishment may be applied as appropriate.</p>";
	echo "<p class=\"Subpar\">c) Administration of the website claims the rights to modify and delete content as they see appropriate.</p>";
	echo "<p class=\"Subpar\">d) If anything does not work properly or an error is encountered, please notify the Mods and if possible, the Webmaster.</p>";
	echo "<p id=\"LglRules\">Legal Stuff:</p>";
	echo "<p class=\"Subpar\">a) We (School, Club, or its Individual Members) cannot be held responsible for any injuries and damages to person or belongings sustained during the game time and through the game-related activities.</p>";
	echo "<p class=\"Subpar\">b) If any legal action is to be pursued against other members, it must be done in no way that would cause the game or the school to be considered at fault.</p>";
	echo "<p class=\"Subpar\">c) The Mod Team, Webmaster, or Keene State College and any of its employees are under no liability for the results of the interactions or opinions expressed using the KSC HVZ Source platform. Any offensive or copyright-infringing content should be immediately brought to the attention of the Mod Team.</p>";
	echo "</div>";
	?>
	</body>
</html>