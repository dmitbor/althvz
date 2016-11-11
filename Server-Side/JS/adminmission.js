function EditMissionMenu(form)
	{
	document.getElementById('HiddenID').value = form.children[2].value;
	document.getElementById('missionHumanName').value = form.children[3].value;
	document.getElementById('missionZombieName').value = form.children[4].value;
	
	document.getElementById('MissionHumanDesc').value = form.children[5].value;
	document.getElementById('MissionHumanEnd').value = form.children[7].value;
	
	document.getElementById('MissionZombieDesc').value = form.children[6].value;
	document.getElementById('MissionZombieEnd').value = form.children[8].value;
	
	document.getElementById('SaveMissionData').value = "Save";
	document.getElementById('SaveMissionData').style.visibility = "visible";
	document.getElementById('HideMission').style.visibility = "visible";
	document.getElementById('HiddenMissionID').value = form.children[2].value;
	
	if (form.children[9].value == 1)
		{
		document.getElementById('ChangeMissionState').value = "End Mission";
		document.getElementById('ChangeMissionState').style.visibility = "visible";
		document.getElementById('SetHumanWinner').style.visibility = "hidden";
		document.getElementById('SetZombieWinner').style.visibility = "hidden";
		}
	else if (form.children[9].value == 0)
		{
		document.getElementById('ChangeMissionState').value = "Start Mission";
		document.getElementById('ChangeMissionState').style.visibility = "visible";
		document.getElementById('SetHumanWinner').style.visibility = "hidden";
		document.getElementById('SetZombieWinner').style.visibility = "hidden";
		document.getElementById('HideMission').style.visibility = "hidden";
		}
	else if (form.children[9].value == 2)
		{
		document.getElementById('ChangeMissionState').style.visibility = "hidden";
		document.getElementById('SetHumanWinner').value = "Set Humans as Winners";
		document.getElementById('SetHumanWinner').style.visibility = "visible";
		document.getElementById('SetZombieWinner').value = "Set Zombies as Winners";
		document.getElementById('SetZombieWinner').style.visibility = "visible";
		}
	else if (form.children[9].value == 3)
		{
		document.getElementById('ChangeMissionState').style.visibility = "hidden";
		document.getElementById('SetHumanWinner').value = "Set Humans as Winners";
		document.getElementById('SetHumanWinner').style.visibility = "visible";
		document.getElementById('SetZombieWinner').value = "Tie Game";
		document.getElementById('SetZombieWinner').style.visibility = "visible";
		}
	else if (form.children[9].value == 4)
		{
		document.getElementById('ChangeMissionState').style.visibility = "hidden";
		document.getElementById('SetHumanWinner').value = "Tie Game";
		document.getElementById('SetHumanWinner').style.visibility = "visible";
		document.getElementById('SetZombieWinner').value = "Set Zombies as Winners";
		document.getElementById('SetZombieWinner').style.visibility = "visible";
		}
		
	if (form.children[10].value == 0)
		{
		document.getElementById('SetPrimary').style.visibility = "visible";
		document.getElementById('SetSecondary').style.visibility = "hidden";
		}
	else if (form.children[10].value == 1)
		{
		document.getElementById('SetSecondary').style.visibility = "visible";
		document.getElementById('SetPrimary').style.visibility = "hidden";
		}
		
	document.getElementById('MissionPlayersSelected').innerHTML = form.children[11].value;
		
	$("#MissionUsersSide").animate({bottom: '0%'});
	}
	
function DeleteMission(form)
	{
	var DoIt = confirm("Are you sure you want to delete this mission?");
	if (DoIt == true)
		{
		document.getElementById('HiddenDeleteFlag').value = form.children[2].value;
		document.forms["missionInfoForm"].submit();
		}
	}
	
function SetNewMission()
	{
	document.getElementById('HiddenID').value = "";
	document.getElementById('missionHumanName').value = "";
	document.getElementById('missionZombieName').value = "";
	
	document.getElementById('MissionHumanDesc').value = "";
	document.getElementById('MissionHumanEnd').value = "";
	
	document.getElementById('MissionZombieDesc').value = "";
	document.getElementById('MissionZombieEnd').value = "";
	
	document.getElementById('SaveMissionData').value = "Create";
	document.getElementById('SaveMissionData').style.visibility = "visible";
	document.getElementById('ChangeMissionState').value = "Begin Mission";
	document.getElementById('ChangeMissionState').style.visibility = "visible";
	
	document.getElementById('SetSecondary').style.visibility = "hidden";
	document.getElementById('SetPrimary').style.visibility = "hidden";
	document.getElementById('SetHumanWinner').style.visibility = "hidden";
	document.getElementById('SetZombieWinner').style.visibility = "hidden";
	document.getElementById('HideMission').style.visibility = "hidden";
	}
	
function AddPlayer(form)
	{
	var AddID = form.children[1].value;
	var AddName = form.children[1].options[form.children[1].selectedIndex].text;
	var option = document.createElement("option");
	option.value = AddID;
	option.text = AddName;
	
	var NotDouble = 0;
	
	for (i = 0; i < form.children[4].length; i++)
		{ 
		if (form.children[4].options[i].text == AddName)
			{
			NotDouble = 1;
			}
		}
	if (NotDouble == 0)
		{
		form.children[4].add(option);
		}
	}
	
function ShowDeletion()
	{
	document.getElementById('DelPlyerFrmMsnBtn').style.visibility = "visible";
	}
	
function RemovePlayer(form)
	{
	form.children[4].remove(form.children[4].selectedIndex);
	}
	
function SelectAll()
	{
	$("#MissionPlayersSelected option").attr('selected', true);
	}