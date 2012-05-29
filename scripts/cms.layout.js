
function setHgt() {
	OBJ1=document.getElementById('menu');
	OBJ2=document.getElementById('content');
	H=Math.max(OBJ1.offsetHeight,OBJ2.offsetHeight);
	OBJ1.style.height=H+'px';
	OBJ2.style.height=H+'px';
}

function setHgt2() {
	OBJ1=document.getElementById('menu');
	OBJ2=document.getElementById('content');
	OBJ3=document.getElementById('terug');
	H=Math.max(OBJ1.offsetHeight,OBJ2.offsetHeight,OBJ3.offsetTop);
	OBJ1.style.height=H+'px';
	OBJ2.style.height=H+'px';
}

function terug() {
	if(document.getElementById('content').offsetHeight>500) {
		document.getElementById('terug').style.visibility='visible';
	}
}

window.onload=setHgt;
window.onscroll=terug;
