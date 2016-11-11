// Human chat deletion function
function DeleteHumForm(form, GameNum)
	{
	// Extract the piece of chat we want removed.
	var ToClear = form.innerHTML;

	// Remove the front extras
	ToClear = ToClear.substring(154);
	
	var RemoveControl = 6;
	
	// Remove the back extras
	ToClear = ToClear.substr(0, ToClear.length-RemoveControl);
	
	// Get Entirety of the chat from which we are removing.
	HumChat = document.getElementById('HumanChatEdited').value;
	
	// Find the point of cut out in the actual chat
	var DelFrom = HumChat.indexOf(ToClear);
	
	if (GameNum < 10)
		{
		var DelLength = 13;
		var AddLength = 19;
		}
	else if (GameNum > 9 && GameNum < 100)
		{
		var DelLength = 12;
		var AddLength = 18;
		}
	else if (GameNum > 99)
		{
		var DelLength = 11;
		var AddLength = 17;
		}
	// Find the specific string to remove.
	var ToCutOut = HumChat.substr(DelFrom-DelLength, ToClear.length+AddLength);
	
	// Get the version of chat without the offending bit
	var CleanCut = HumChat.replace(ToCutOut, "");
	// Set team to humans
	var GivenTeam = "Humans";
	
	$.post("chatclearer.php", {Message: CleanCut, Team: GivenTeam, Game: GameNum});
	
	UpdateLogs();
	}
	
// Zombie Chat Deletion Function
function DeleteZomForm(form, GameNum)
	{
	// Extract the piece of chat we want removed.
	var ToClear = form.innerHTML;
	
	// Remove the front extras
	ToClear = ToClear.substring(154);
	
	var RemoveControl = 6;
	
	// Remove the back extras
	ToClear = ToClear.substr(0, ToClear.length-RemoveControl);

	// Get Entirety of the chat from which we are removing.
	ZomChat = document.getElementById('ZombieChatEdited').value;
	
	// Find the point of cut out in the actual chat
	var DelFrom = ZomChat.indexOf(ToClear);
		
	if (GameNum < 10)
		{
		var DelLength = 13;
		var AddLength = 19;
		}
	else if (GameNum > 9 && GameNum < 100)
		{
		var DelLength = 12;
		var AddLength = 18;
		}
	else if (GameNum > 99)
		{
		var DelLength = 11;
		var AddLength = 17;
		}
	// Find the specific string to remove.
	var ToCutOut = ZomChat.substr(DelFrom-DelLength, ToClear.length+AddLength);
	
	// Get the version of chat without the offending bit
	var CleanCut = ZomChat.replace(ToCutOut, "");
	// Set team to humans
	var GivenTeam = "Zombies";
	
	$.post("chatclearer.php", {Message: CleanCut, Team: GivenTeam, Game: GameNum});
	
	UpdateLogs();
	}
	
function EditGroup(form)
	{
	document.getElementById('InvisoID').value = form.children[0].value;
	document.getElementById('InvisoIcon').value = form.children[1].value;
	document.getElementById('GroupType').value = form.children[2].value;
	document.getElementById('GroupNameEditText').value = form.children[3].value;
	document.getElementById('GroupSubTitleEditText').value = form.children[4].value;
	document.getElementById('GroupDescExitText').value = form.children[5].value;
	document.getElementById('DropGroupBtn').style.visibility = "visible";
	document.getElementById('SaveEditBtn').style.visibility = "visible";
	document.getElementById('AvyDropPar').style.visibility = "visible";
	}
	
function RemoveGroup()
	{
	var DoIt = confirm("Are you sure you want to destroy this group entirely?");
	if (DoIt == true)
		{
		$('#KillGroup').val('DoIt');
		document.forms["GroupEditDiv"].submit();
		}
	}
	
function UpdateLogs()
	{
	document.getElementById("ChatHumanDiv").innerHTML = "";
	document.getElementById("ChatZombieDiv").innerHTML = "";
	
	var LogGame = document.getElementById("LogList").value;
	
	var HumLog = "ChatLogs/" + LogGame + "/HumanLog.html";
	var ZomLog = "ChatLogs/" + LogGame + "/ZombLog.html";
	
	// Do Humans first.
	$.ajax(
		{
		url: HumLog,
		cache: false,
		success: function(html)
			{
			var getHTML = html.replace(/<\/div>/g, "</div></form>");
			
			getHTML = getHTML.replace(/<div>/g, "<form class=\"ChatHandlerDiv\"><div class=\"DelBtnDiv\"><button type=\"button\" onmousedown=\"DeleteHumForm(this.form," + LogGame + ")\">Delete</button></div><div class=\"ChatTxtDiv\" name=\"ChatTXT\">");
			$("#ChatHumanDiv").html(getHTML);
			var element = document.getElementById("ChatHumanDiv");
			element.scrollTop = element.scrollHeight;
			document.getElementById('HumanChatEdited').value = html;
			},
		});
		
	// Then Zombies.
	$.ajax(
		{
		url: ZomLog,
		cache: false,
		success: function(html)
			{
			var getHTML = html.replace(/<\/div>/g, "</div></form>");
			
			getHTML = getHTML.replace(/<div>/g, "<form class=\"ChatHandlerDiv\"><div class=\"DelBtnDiv\"><button type=\"button\" onmousedown=\"DeleteZomForm(this.form," + LogGame + ")\">Delete</button></div><div class=\"ChatTxtDiv\" name=\"ChatTXT\">");
			$("#ChatZombieDiv").html(getHTML);
			var element = document.getElementById("ChatZombieDiv");
			element.scrollTop = element.scrollHeight;
			document.getElementById('ZombieChatEdited').value = html;
			},
		});
	}