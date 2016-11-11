<?php
$AnimalArray = array(
		1 => "Adder",
		2 => "Fox",
		3 => "Bass",
		4 => "Ocelot",
		5 => "Octopus",
		6 => "Raven",
		7 => "Mantis",
		8 => "Wolf",
		9 => "Duck",
		10 => "Squid",
		11 => "Cobra",
		12 => "Shark",
		13 => "Capybara",
		14 => "Hawk",
		15 => "Jackal",
		16 => "Hedgehog",
		17 => "Rooster",
		18 => "Tiger",
		19 => "Owl",
		20 => "Pig",
		21 => "Rabbit",
		22 => "Giraffe",
		23 => "Elephant",
		24 => "Rhino",
		25 => "Camel",
		26 => "Monkey",
		27 => "Mole",
		28 => "Panther",
		29 => "Walrus",
		30 => "Alligator",
		31 => "Bear",
		32 => "Hound",
		33 => "Butterfly",
		34 => "Platypus",
		35 => "Whale",
		36 => "Spider",
		);
		
$WeaponArray = array(
		1 => "Revolver",
		2 => "Pistol",
		3 => "Shotgun",
		4 => "Sniper",
		5 => "Psycho",
		6 => "Savior",
		7 => "Decoy",
		8 => "Assault",
		9 => "Grenade",
		10 => "Arrow",
		11 => "Flare",
		12 => "Rifle",
		13 => "Rocket",
		14 => "Flamer",
		15 => "Auto",
		16 => "Cannon",
		17 => "Musket",
		18 => "Arquebus",
		19 => "Sawn-Off",
		20 => "Bayonett",
		21 => "Machine",
		22 => "Radical",
		23 => "Shot",
		24 => "Nuclear",
		25 => "Tank",
		);

$AdjectiveArray = array(
		1 => "Solid",
		2 => "Liquid",
		3 => "Plasma",
		4 => "Gaseous",
		5 => "Metal",
		6 => "Steel",
		7 => "Bloody",
		8 => "Fire",
		9 => "Killer",
		10 => "Skull",
		11 => "Poison",
		12 => "Big",
		13 => "Above Average",
		14 => "Dark",
		15 => "Brutal",
		16 => "Hungry",
		17 => "Pirate",
		18 => "Raving",
		19 => "Brave",
		20 => "Ochre",
		21 => "Lovely",
		22 => "Moon",
		23 => "Cowboy",
		24 => "Hater",
		25 => "Onyx",
		);
		
// Change my title to provided one one.
if ($GetMyTitleState == 1)
	{
	if (isset($_POST["MyNewAdminTitle"]))
		{
		$NewTitle = mysqli_real_escape_string($DBCon, $_POST["MyNewAdminTitle"]);
		}
	else if (isset($_POST["MyNewTitle"]))
		{
		$NewTitle = mysqli_real_escape_string($DBCon, $_POST["MyNewTitle"]);
		}
	}
// Set to nothing
else if ($GetMyTitleState == 2)
	{
	$NewTitle = "";
	}
// Foxhound Generation
else if ($GetMyTitleState == 3)
	{
	$NewTitle = $WeaponArray[rand (1, 25)] . " " . $AnimalArray[rand (1, 36)];
	}
// MSF Generation
else if ($GetMyTitleState == 4)
	{
	$NewTitle = $AdjectiveArray[rand (1, 25)] . " " . $AnimalArray[rand (1, 36)];
	}
?>