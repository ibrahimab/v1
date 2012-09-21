
function validateEmail(email) {
	var pattern = /^[_a-zA-Z0-9-]+((\.[_a-zA-Z0-9-]+)*|(\+[_a-zA-Z0-9-]+)*)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/;
	return email.match(pattern);
}

function mailFormToggle() {
	// mailformulier favorietenfunctie open/dichtklappen
	if ($("#mailForm").is(":hidden")) {
		// bij openen: div voor errormelding tonen
		$("#errorLabel").css("display","block");
	} else {
		// bij sluiten: div voor errormelding verbergen
		$("#errorLabel").css("display","none");
	}
	$("#mailForm").slideToggle();

	document.getElementById("errorLabel").innerHTML="";
	document.getElementById("errorLabel").style.backgroundColor = '#ffffff';

	return false;
}

$(document).ready(function() {
	$("#showForm").click(function () {
		return mailFormToggle();
	});

	$("#Annuleren").click(function () {
		return mailFormToggle();
	});
});
