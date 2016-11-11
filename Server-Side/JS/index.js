function ShowLog()
{
	$("#LogMenu").animate({right: '0%'});
	$("#RegMenu").animate({right: '-20%'});
	$("#ShowRegBtn").animate({
        backgroundColor: '#E7D8AC'
     }, "fast");
	 $("#ShowLogBtn").animate({
        backgroundColor: '#F5E5FF'
     }, "fast");
}

function ShowReg()
{
	$("#LogMenu").animate({right: '-20%'});
	$("#FrgtMenu").animate({right: '-20%'});
	$("#RegMenu").animate({right: '0%'});
	$("#ShowLogBtn").animate({
        backgroundColor: '#E7D8AC'
     }, "fast");
	 $("#ShowRegBtn").animate({
        backgroundColor: '#F5E5FF'
     }, "fast");
}

function ShowForgotPass()
{
	$("#FrgtMenu").animate({right: '0%'});
}

function checkFilled()
{
	var logtext = document.getElementById("newlogtext").value;
	var passtext = document.getElementById("newpasstext").value;
	var peasschecktext = document.getElementById("newpassconftext").value;
	var emailtext = document.getElementById("newemailtext").value;
	
	if (logtext.length > 6 && passtext.length > 6 && peasschecktext.length > 6 && emailtext.indexOf("@") != -1 && emailtext.indexOf(".") != -1)
		{
		document.getElementById("RegBtn").disabled = false;
		}
}