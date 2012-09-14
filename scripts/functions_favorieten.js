
function validateEmail(email) {  
	var pattern = /^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/;
	return email.match(pattern);     
}  

