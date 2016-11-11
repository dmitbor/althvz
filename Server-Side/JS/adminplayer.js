var MenuOut = 0;

function EditPlayerMenu(form)
	{
	if (MenuOut == 0)
		{
		document.getElementById('UserChangingIDHidden').value = form.children[2].value;
		document.getElementById('EditPlayerName').value = form.children[4].value;
		document.getElementById('NewPlayerDesc').value = form.children[5].value;
		$("#InfoEditDiv").animate({right: '0%'});
		MenuOut = 1;
		}
	else if (MenuOut == 1)
		{
		document.getElementById('UserChangingIDHidden').value = form.children[2].value;
		document.getElementById('EditPlayerName').value = form.children[4].value;
		document.getElementById('NewPlayerDesc').value = form.children[5].value;
		}
	}
	
function FeedPlayer(form,FedPlayerID)
	{
	$.post("playerhandler.php", {Action: 1, FedID: FedPlayerID});
	form.children[8].innerHTML = "Player Fed!";
	}
	
function ChangeMissedMissions(form,ChangedID,Action)
	{
	var MissionsTake = form.children[8].innerHTML;
	var FindNumber = MissionsTake.indexOf(":");
	var MissedMissNum = MissionsTake.substr(FindNumber+1);
	if (Action == 'Add')
		{
		MissedMissNum++;
		}
	else if (Action == 'Remove')
		{
		MissedMissNum--;
		}
	$.post("playerhandler.php", {Action: 2, FedID: ChangedID, NewValue: MissedMissNum});
	form.children[8].innerHTML = 'Missed Missions: ' + MissedMissNum;

	if (MissedMissNum == 0)
		{
		form.children[10].style.visibility = "hidden";
		}
	else
		{
		form.children[10].style.visibility = "visible";
		}
	}