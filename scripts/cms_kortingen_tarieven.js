
function disableEnterKey(dit,week,e) {
	var key;
	if(window.event) {
		key = window.event.keyCode;     //IE
	} else {
		key = e.which;     //firefox
	}

	if(key == 13) {
		if(week=='sjabloon') {
		
		} else {
			bereken(dit,week);
		}
		return false;
	} else if(key == 44) {
		return true;
	} else {
		return true;
	}
}

function disableEnterKey_kopieer(dit,naam,nummer,e) {
	var key;
	if(window.event) {
		key = window.event.keyCode;     //IE
	} else {
		key = e.which;     //firefox
	}

	if(key == 13) {
		kopieer(dit,naam,nummer);
		return false;
	} else {
		return true;
	}
}

function replace_comma(dit) {
	dit.value=dit.value.replace(',','.');
}

function kopieer(dit,naam,nummer,integer) {
	if(dit.value) {
		dit.value=dit.value.replace(',','.');
		if(!integer) {
			dit.value=parseFloat(dit.value).toFixed(2);
		}
		for(var i=0;i<document.forms['weken'].elements.length;i++) {
			week=document.forms['weken'].elements[i].value;
//			if(nummer>0) {
//				document.forms['tarieven'].elements[naam+'['+week+']['+nummer+']'].value=dit.value;
//			} else {
				document.forms['tarieven'].elements[naam+'['+week+']'].value=dit.value;
//				alert(naam+'['+week+']'+' '+dit.value);
//			}
			if(naam=='inkoopkorting_euro' && document.forms['tarieven'].elements['auto_overnemen'].checked) {
				vul('aanbieding_acc_euro',week,dit.value);
			}
			if(naam=='inkoopkorting_percentage' && document.forms['tarieven'].elements['auto_overnemen'].checked) {
				vul('aanbieding_acc_percentage',week,dit.value);
			}
			bereken('kopieer',week);
		}
	}
}

function bereken(dit,week) {
	var toonper=document.forms['tarieven'].elements['toonper'].value
	var f=document.forms['tarieven'];
	var netto=0;
	var bruto=0;
	var netto_skipas=0;
	var inkoopkorting_percentage=0;
	var inkoopkorting_euro=0;
	var verkoopprijs_accommodatie=0;
	var verkoopprijs_skipas=0;
	var verkoopprijs_arrangement=0;
	var aanbieding_acc_percentage=0;
	var aanbieding_acc_euro=0;
	var aanbieding_skipas_percentage=0;
	var aanbieding_skipas_euro=0;
	var marge_accommodatie=0;
	var marge_skipas=0;

	var c_netto=0;
	var c_bruto=0;
	var c_verkoop_site=0;
	var c_marge_percentage=0;
	
	if(dit.value) {
	
		dit.value=parseFloat(dit.value).toFixed(2);
		if(dit.value==0) dit.value='';
	}
	
	if(toonper==1) {

		// tarievenoptie A - arrangement

		var maxpers=document.forms['tarieven'].elements['maxpers'].value

		netto=parseFloat(f.elements['netto_org['+week+']'].value);
		bruto=parseFloat(f.elements['bruto['+week+']'].value);
		netto_skipas=parseFloat(f.elements['netto_skipas['+week+']'].value);
		inkoopkorting_percentage=parseFloat(f.elements['inkoopkorting_percentage['+week+']'].value);
		inkoopkorting_euro=parseFloat(f.elements['inkoopkorting_euro['+week+']'].value);
		verkoopprijs_accommodatie=parseFloat(f.elements['verkoopprijs_accommodatie_org['+week+']'].value);
		verkoopprijs_skipas=parseFloat(f.elements['verkoopprijs_skipas_org['+week+']'].value);
		aanbieding_acc_percentage=parseFloat(f.elements['aanbieding_acc_percentage['+week+']'].value);
		aanbieding_acc_euro=parseFloat(f.elements['aanbieding_acc_euro['+week+']'].value);
		aanbieding_skipas_percentage=parseFloat(f.elements['aanbieding_skipas_percentage['+week+']'].value);
		aanbieding_skipas_euro=parseFloat(f.elements['aanbieding_skipas_euro['+week+']'].value);

		// bij percentage-korting euro-korting blokkeren
		if(aanbieding_acc_percentage) {
			f.elements['aanbieding_acc_euro['+week+']'].readOnly=true;
			f.elements['aanbieding_acc_euro['+week+']'].value='';
			aanbieding_acc_euro=0;
		} else {
			f.elements['aanbieding_acc_euro['+week+']'].readOnly=false;
		}
		if(aanbieding_skipas_percentage) {
			f.elements['aanbieding_skipas_euro['+week+']'].readOnly=true;
			f.elements['aanbieding_skipas_euro['+week+']'].value='';
			aanbieding_skipas_euro=0;
		} else {
			f.elements['aanbieding_skipas_euro['+week+']'].readOnly=false;
		}
		
		// bij euro-korting percentage-korting blokkeren
		if(aanbieding_acc_euro) {
			f.elements['aanbieding_acc_percentage['+week+']'].readOnly=true;
			f.elements['aanbieding_acc_percentage['+week+']'].value='';
			aanbieding_acc_percentage=0;
		} else {
			f.elements['aanbieding_acc_percentage['+week+']'].readOnly=false;
		}
		if(aanbieding_skipas_euro) {
			f.elements['aanbieding_skipas_percentage['+week+']'].readOnly=true;
			f.elements['aanbieding_skipas_percentage['+week+']'].value='';
			aanbieding_skipas_percentage=0;
		} else {
			f.elements['aanbieding_skipas_percentage['+week+']'].readOnly=false;
		}

		// inkoopkorting
		if(inkoopkorting_percentage) netto=netto*(1-(inkoopkorting_percentage/100));
		if(inkoopkorting_euro) netto=netto-inkoopkorting_euro;
		
		// aanbieding accommodatie
		if(aanbieding_acc_percentage) verkoopprijs_accommodatie=verkoopprijs_accommodatie*(1-(aanbieding_acc_percentage/100));
		if(aanbieding_acc_euro) verkoopprijs_accommodatie=verkoopprijs_accommodatie-aanbieding_acc_euro;
		
		// aanbieding skipas
		if(aanbieding_skipas_percentage) verkoopprijs_skipas=verkoopprijs_skipas*(1-(aanbieding_skipas_percentage/100));
		if(aanbieding_skipas_euro) verkoopprijs_skipas=verkoopprijs_skipas-aanbieding_skipas_euro;
		
		// verkoopprijs arrangement
		verkoopprijs_arrangement=(verkoopprijs_accommodatie/maxpers)+verkoopprijs_skipas;
	
		// marge accommodatie
		marge_accommodatie=(verkoopprijs_accommodatie-netto)/verkoopprijs_accommodatie*100;
		
		// marge skipas
		marge_skipas=(verkoopprijs_skipas-netto_skipas)/verkoopprijs_skipas*100;

		vul('netto',week,netto);
		vul('verkoopprijs_accommodatie',week,verkoopprijs_accommodatie);
		vul('verkoopprijs_skipas',week,verkoopprijs_skipas);
		vul('verkoopprijs_arrangement',week,verkoopprijs_arrangement);
		vul('marge_accommodatie',week,marge_accommodatie);
		vul('marge_skipas',week,marge_skipas);
	
	} else if(toonper==3) {
		// tarievenoptie C - losse accommodatie

		c_netto=parseFloat(f.elements['c_netto_org['+week+']'].value);
		c_bruto=parseFloat(f.elements['c_bruto['+week+']'].value);
		inkoopkorting_percentage=parseFloat(f.elements['inkoopkorting_percentage['+week+']'].value);
		inkoopkorting_euro=parseFloat(f.elements['inkoopkorting_euro['+week+']'].value);
		c_verkoop_site=parseFloat(f.elements['c_verkoop_site_org['+week+']'].value);
		aanbieding_acc_percentage=parseFloat(f.elements['aanbieding_acc_percentage['+week+']'].value);
		aanbieding_acc_euro=parseFloat(f.elements['aanbieding_acc_euro['+week+']'].value);

		// bij percentage-korting euro-korting blokkeren
		if(aanbieding_acc_percentage) {
			f.elements['aanbieding_acc_euro['+week+']'].readOnly=true;
			f.elements['aanbieding_acc_euro['+week+']'].value='';
			aanbieding_acc_euro=0;
		} else {
			f.elements['aanbieding_acc_euro['+week+']'].readOnly=false;
		}
		
		// bij euro-korting percentage-korting blokkeren
		if(aanbieding_acc_euro) {
			f.elements['aanbieding_acc_percentage['+week+']'].readOnly=true;
			f.elements['aanbieding_acc_percentage['+week+']'].value='';
			aanbieding_acc_percentage=0;
		} else {
			f.elements['aanbieding_acc_percentage['+week+']'].readOnly=false;
		}

		// inkoopkorting
		if(inkoopkorting_percentage) c_netto=c_netto*(1-(inkoopkorting_percentage/100));
		if(inkoopkorting_euro) c_netto=c_netto-inkoopkorting_euro;
		
		// aanbieding accommodatie
		if(aanbieding_acc_percentage) c_verkoop_site=c_verkoop_site*(1-(aanbieding_acc_percentage/100));
		if(aanbieding_acc_euro) c_verkoop_site=c_verkoop_site-aanbieding_acc_euro;
		
		// marge accommodatie
		c_marge_percentage=(c_verkoop_site-c_netto)/c_verkoop_site*100;

		vul('c_netto',week,c_netto);
		vul('c_verkoop_site',week,c_verkoop_site);
		vul('c_marge_percentage',week,c_marge_percentage);
		
	}
	

	return true;
}

$(document).ready(function(){
	// jquery

//	$('.tr_disabled_forms input').attr("disabled", true);
//	$('.tr_disabled_forms input').css("background-color","#878481");

});

function overnemen(week,value,field) {
	if(document.forms['tarieven'].elements['auto_overnemen'].checked) {
		vul(field,week,value);
		document.forms['tarieven'].elements[field+'['+week+']'].style.backgroundColor='#fffbba';
		setTimeout(function() {
			document.forms['tarieven'].elements[field+'['+week+']'].style.backgroundColor='';
		},700)
	}
	return true;
}

function vul(naam,week,value) {
	if(value>0) {
		document.forms['tarieven'].elements[naam+'['+week+']'].value=parseFloat(value).toFixed(2);
		document.forms['tarieven'].elements[naam+'['+week+']'].style.color='';
	} else if(value<0) {
		document.forms['tarieven'].elements[naam+'['+week+']'].value=parseFloat(value).toFixed(2);
		document.forms['tarieven'].elements[naam+'['+week+']'].style.color='red';
	} else {
		document.forms['tarieven'].elements[naam+'['+week+']'].value='';
		document.forms['tarieven'].elements[naam+'['+week+']'].style.color='';
	}
}

function alles_doorrekenen() {
	for(var i=0;i<document.forms['weken'].elements.length;i++) {
		week=document.forms['weken'].elements[i].value;
		bereken('kopieer',week);
	}
}

function checkform() {
	var f=document.forms['tarieven'];
	if(f.elements['input[van][day]'].value>0 && f.elements['input[van][month]'].value>0 && f.elements['input[van][year]'].value>0) {
		if(f.elements['input[tot][day]'].value>0 && f.elements['input[tot][month]'].value>0 && f.elements['input[tot][year]'].value>0) {
			var van = new Date();
			van.setFullYear(parseFloat(f.elements['input[van][year]'].value),parseFloat(f.elements['input[van][month]'].value)-1,parseFloat(f.elements['input[van][day]'].value));
			var tot = new Date();
			tot.setFullYear(parseFloat(f.elements['input[tot][year]'].value),parseFloat(f.elements['input[tot][month]'].value)-1,parseFloat(f.elements['input[tot][day]'].value));
			if(tot.getTime()>=van.getTime()) {
				document.tarieven.but1.disabled=1;
				document.tarieven.submit();
			} else {
				alert('Vul "Geldig tot en met" moet later zijn dan "Geldig vanaf"');
			}
		} else {
			alert('Vul "Geldig tot en met" in');
		}
	} else {
		alert('Vul "Geldig vanaf" in');
	}
}
