
function validateEmail(email) {
	var pattern = /^[_a-zA-Z0-9-]+((\.[_a-zA-Z0-9-]+)*|(\+[_a-zA-Z0-9-]+)*)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/;
	return email.match(pattern);
}

$(document).ready(function() {
	$("#showForm").click(function () {
			$("#mailForm").slideToggle();
			return false;
	});


	$("#Annuleren").click(function () {
		$("#mailForm").slideToggle();
		return false;
	});
});
