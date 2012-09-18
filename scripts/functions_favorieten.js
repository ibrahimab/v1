
function validateEmail(email) {
	var pattern = /^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$/;
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
