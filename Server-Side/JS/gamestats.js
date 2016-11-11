var OnBot = 0;

function SendChatMessage(GivenTeam, GivenGame)
{
	var GivenMessage = document.getElementById("chattextinput").value;
	if (GivenMessage != "")
		{
		$.post("chatadder.php", {Message: GivenMessage, Team: GivenTeam, Game: GivenGame});
		document.getElementById("chattextinput").value = "";
		LoadChat(GivenTeam, GivenGame);
		}
	GetToBottom(0);
}

function LoadChat(GivenTeam, GivenGame)
{
	if (GivenTeam == "Humans")
		{
		var Location = "ChatLogs/" + GivenGame + "/HumanLog.html";
		}
	else if (GivenTeam == "Zombies")
		{
		var Location = "ChatLogs/" + GivenGame + "/ZombLog.html";
		}
		
	$.ajax(
		{
		url: Location,
		cache: false,
		success: function(html)
			{		
			$("#BoxAChat").html(html);
			GetToBottom(1);
			},
		});
}

function ButtonPressed(event, GivenTeam, GivenGame)
{
	var keyPressed = event.charCode;
	if (keyPressed == 13)
		{
		SendChatMessage(GivenTeam, GivenGame);
		}
	GetToBottom(0);
}


function GetToBottom(Given)
{
	if (OnBot == 0 || Given == 0)
		{
		var element = document.getElementById("BoxAChat");
		element.scrollTop = element.scrollHeight;
		OnBot = 1;
		}
}