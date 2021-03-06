
// global vars
var opmerkingen_text=[];
var opmerkingen_text_alertgetoond=0;
var eigenaar_formulier_week = 0;
var eigenarenlogin_soort = '';

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
			if(nummer>0) {
				document.forms['tarieven'].elements[naam+'['+week+']['+nummer+']'].value=dit.value;
			} else {
				document.forms['tarieven'].elements[naam+'['+week+']'].value=dit.value;
			}
			if(nummer=='sjabloon') {

			} else if(nummer=='skipas') {
				bereken_skipas('kopieer',week);
			} else if(nummer=='opties') {
				bereken_opties('kopieer',week);
			} else if(nummer=='voorraad') {
				bereken_voorraad('kopieer', week, naam);
			} else if(nummer=='nummer_voorraad') {
				 bereken_nummer_voorraad('kopieer',week);
			} else {
				bereken('kopieer',week);
			}
			if(dit.getAttributeNode("id").value=='kopieer_voorraad_garantie') {
				garantie_niet_afboeken(document.forms['tarieven'].elements[naam+'['+week+']'],week);
			}
		}
		dit.value='';
		if(nummer=='sjabloon' || nummer=='skipas' || nummer=='nummer_voorraad') {

		} else {
			if(naam=='bruto') optellen('bruto');
			if(naam=='arrangementsprijs') optellen('arrangementsprijs');
			if(naam=='verkoop') optellen('verkoop');
			marge_gemiddelde(naam);
		}
	}
	toon_opmerkingen('');
}

function kopieer_checkbox(dit,naam) {
	for(var i=0;i<document.forms['weken'].elements.length;i++) {
		week=document.forms['weken'].elements[i].value;
		document.forms['tarieven'].elements[naam+'['+week+']'].checked=dit.checked;
		if(naam=='voorraad_bijwerken') {
			bereken_voorraad('kopieer',week);
		}
	}
}

function bereken_skipas(dit,week) {
	if(dit=='kopieer') {

	} else {
		dit.value=dit.value.replace(',','.');
		if(dit.value) {
			dit.value=parseFloat(dit.value).toFixed(2);
		} else {
			dit.value='';
		}
	}
	if(document.forms['tarieven'].elements['skipas_netto_ink['+week+']'].value>0) {
		skipas_prijs=parseFloat(document.forms['tarieven'].elements['skipas_netto_ink['+week+']'].value);
		document.forms['tarieven'].elements['skipas_prijs['+week+']'].value=parseFloat(document.forms['tarieven'].elements['skipas_netto_ink['+week+']'].value).toFixed(2);
	} else {
		if(document.forms['tarieven'].elements['skipas_bruto['+week+']'].value>0) {
			var skipas_prijs=parseFloat(document.forms['tarieven'].elements['skipas_bruto['+week+']'].value);
			if(document.forms['tarieven'].elements['skipas_korting['+week+']'].value>0) skipas_prijs*=(1-(parseFloat(document.forms['tarieven'].elements['skipas_korting['+week+']'].value)/100));
			if(document.forms['tarieven'].elements['skipas_verkoopkorting['+week+']'].value>0) skipas_prijs-=parseFloat(document.forms['tarieven'].elements['skipas_verkoopkorting['+week+']'].value);
			if(skipas_prijs>0) {
				document.forms['tarieven'].elements['skipas_prijs['+week+']'].value=skipas_prijs.toFixed(2);
			} else {
				document.forms['tarieven'].elements['skipas_prijs['+week+']'].value='';
			}
		} else {
			document.forms['tarieven'].elements['skipas_prijs['+week+']'].value='';
		}
	}
	if(skipas_prijs>0) {
		skipas_netto=skipas_prijs;
		if(document.forms['tarieven'].elements['skipas_omzetbonus['+week+']'].value>0) skipas_netto*=(1-(parseFloat(document.forms['tarieven'].elements['skipas_omzetbonus['+week+']'].value)/100));
		if(document.forms['tarieven'].elements['skipas_bruto['+week+']'].value>0 && skipas_netto>document.forms['tarieven'].elements['skipas_bruto['+week+']'].value) {
			skipas_netto=parseFloat(document.forms['tarieven'].elements['skipas_bruto['+week+']'].value);
		}

		if(skipas_netto>0) {
			document.forms['tarieven'].elements['skipas_netto['+week+']'].value=skipas_netto.toFixed(2);
		} else {
			document.forms['tarieven'].elements['skipas_netto['+week+']'].value='';
		}
	} else {
		document.forms['tarieven'].elements['skipas_netto['+week+']'].value='';
	}
}

function bereken_voorraad(dit, week, naam) {
	var waarde_ingevoerd = false;
	if(dit=='kopieer' || dit=='opnieuw_berekenen') {

	} else {
		dit.value=dit.value.replace(',','');
		dit.value=dit.value.replace('.','');
		waarde_ingevoerd = true;
	}

	var voorraad_toestaan = true;
	if($("#eigenaar_formulier").length!==0 && (dit=='kopieer' || waarde_ingevoerd) ) {
		if($("input[name='bezeteigenaar["+week+"]']").is(":checked") || $("input[name='boekingderden["+week+"]']").is(":checked") || $("input[name='nietbeschikbaarverhuur["+week+"]']").is(":checked")) {

			var date = new Date(parseInt($("input[name='aankomstdatum_exact["+week+"]']").val(), 10) * 1000);
			var curr_date = date.getDate();
			var curr_month = date.getMonth() + 1; //Months are zero based
			var curr_year = date.getFullYear();

			var r=confirm("Er is een blokkade actief i.v.m. de eigenaar op  "+("0" + curr_date).slice(-2) + "/" + ("0" + curr_month).slice(-2) + ".\n\nVoorraad toch toevoegen?");
			if (r==true) {

			} else {
				if(dit=='kopieer') {
					document.forms['tarieven'].elements[naam+'['+week+']'].value="";
				} else {
					dit.value = "";
				}
				voorraad_toestaan = false;
			}
		}
	}

	var totaal=0;
	if(document.forms['tarieven'].elements['voorraad_garantie['+week+']'].value>0) totaal=totaal+parseInt(document.forms['tarieven'].elements['voorraad_garantie['+week+']'].value);
	if(document.forms['tarieven'].elements['voorraad_allotment['+week+']'].value>0) totaal=totaal+parseInt(document.forms['tarieven'].elements['voorraad_allotment['+week+']'].value);
	if(document.forms['tarieven'].elements['voorraad_vervallen_allotment['+week+']'].value>0) totaal=totaal+parseInt(document.forms['tarieven'].elements['voorraad_vervallen_allotment['+week+']'].value);
	if(document.forms['tarieven'].elements['voorraad_optie_leverancier['+week+']'].value>0) totaal=totaal+parseInt(document.forms['tarieven'].elements['voorraad_optie_leverancier['+week+']'].value);
	if(document.forms['tarieven'].elements['voorraad_xml['+week+']'].value>0) totaal=totaal+parseInt(document.forms['tarieven'].elements['voorraad_xml['+week+']'].value);
	if(document.forms['tarieven'].elements['voorraad_request['+week+']'].value>0 || document.forms['tarieven'].elements['voorraad_request['+week+']'].value<0) totaal=totaal+parseInt(document.forms['tarieven'].elements['voorraad_request['+week+']'].value);


	if(voorraad_toestaan) {
		if(document.forms['tarieven'].elements['voorraad_bijwerken['+week+']'].checked==true) {
			if(totaal>0) {
				document.forms['tarieven'].elements['beschikbaar['+week+']'].checked=true;
			} else {
				document.forms['tarieven'].elements['beschikbaar['+week+']'].checked=false;
			}
		}
		if(document.forms['tarieven'].elements['voorraad_optie_klant['+week+']'].value>0) totaal=totaal-parseInt(document.forms['tarieven'].elements['voorraad_optie_klant['+week+']'].value);

		document.forms['tarieven'].elements['voorraad_totaal['+week+']'].value=totaal;

		return true;
	} else {
		return false;
	}
}

function bereken_nummer_voorraad(dit,week) {
	if(dit=='kopieer') {

	} else {
		dit.value=dit.value.replace(',','');
		dit.value=dit.value.replace('.','');
		dit.value=dit.value.replace('-','');
	}
	var totaal=0;
	if(document.forms['tarieven'].elements['voorraad_garantie['+week+']'].value>0) totaal=totaal+parseInt(document.forms['tarieven'].elements['voorraad_garantie['+week+']'].value);
	if(document.forms['tarieven'].elements['voorraad_allotment['+week+']'].value>0) totaal=totaal+parseInt(document.forms['tarieven'].elements['voorraad_allotment['+week+']'].value);
	if(document.forms['tarieven'].elements['voorraad_vervallen_allotment['+week+']'].value>0) totaal=totaal+parseInt(document.forms['tarieven'].elements['voorraad_vervallen_allotment['+week+']'].value);
	if(document.forms['tarieven'].elements['voorraad_optie_leverancier['+week+']'].value>0) totaal=totaal+parseInt(document.forms['tarieven'].elements['voorraad_optie_leverancier['+week+']'].value);
//	if(document.forms['tarieven'].elements['voorraad_xml['+week+']'].value>0) totaal=totaal+parseInt(document.forms['tarieven'].elements['voorraad_xml['+week+']'].value);
	if(document.forms['tarieven'].elements['voorraad_request['+week+']'].value>0 || document.forms['tarieven'].elements['voorraad_request['+week+']'].value<0) totaal=totaal+parseInt(document.forms['tarieven'].elements['voorraad_request['+week+']'].value);

//	if(document.forms['tarieven'].elements['voorraad_optie_klant['+week+']'].value>0) totaal=totaal-parseInt(document.forms['tarieven'].elements['voorraad_optie_klant['+week+']'].value);

	document.forms['tarieven'].elements['voorraad_totaal['+week+']'].value=totaal;
}


function toon_opmerkingen() {
	var opmerkingen_gevuld=0;
	for(var i=0;i<document.forms['weken'].elements.length;i++) {
		week=document.forms['weken'].elements[i].value;
		if(opmerkingen_text[week]!='' && opmerkingen_text[week]!=undefined) {
			opmerkingen_gevuld=1;
			var opmerkingen1 = document.getElementById("opmerkingen1");
			opmerkingen1.firstChild.nodeValue=opmerkingen_text[week];
			var opmerkingen2 = document.getElementById("opmerkingen2");
			opmerkingen2.firstChild.nodeValue=opmerkingen_text[week];
			if(opmerkingen_text[week]!='' && opmerkingen_text_alertgetoond==0) {
				alert(opmerkingen_text[week]);
				opmerkingen_text_alertgetoond=1;
			}
		}
	}
	if(opmerkingen_gevuld==0) {
		var opmerkingen1 = document.getElementById("opmerkingen1");
		opmerkingen1.firstChild.nodeValue='';
		var opmerkingen2 = document.getElementById("opmerkingen2");
		opmerkingen2.firstChild.nodeValue='';
	}
}

function bereken(dit,week) {
	var max=parseInt(document.forms['tarieven'].elements['max'].value);
	var min_tonen=parseInt(document.forms['tarieven'].elements['min_tonen'].value);
	var marge_accommodatie=0;
	var toonper=document.forms['tarieven'].elements['toonper'].value;
	var flexibel=document.forms['tarieven'].elements['flexibel'].value;
	var verkoop_accommodatie=0;
	var netto_na_inkoopkorting=0;

	if(dit=='kopieer') {

	} else {

		$(dit).parent().removeClass("field_from_copy");

		dit.value=dit.value.replace(',','.');
		if(dit.value) {
			dit.value=parseFloat(dit.value).toFixed(2);
		} else {
			dit.value='';
		}
	}


	if(toonper==1) {
		if(document.forms['tarieven'].elements['skipas_bruto['+week+']'].value>0) {

		} else {
			document.forms['tarieven'].elements['bruto['+week+']'].value='';
			document.forms['tarieven'].elements['beschikbaar['+week+']'].checked=false;
		}
		if(document.forms['tarieven'].elements['korting_percentage['+week+']'].value>0) {
			var inkoop_min_korting=parseFloat(document.forms['tarieven'].elements['bruto['+week+']'].value)*(1-parseFloat(document.forms['tarieven'].elements['korting_percentage['+week+']'].value)/100);
			if(inkoop_min_korting>0) {
				document.forms['tarieven'].elements['inkoop_min_korting['+week+']'].value=inkoop_min_korting.toFixed(2);
			} else {
				document.forms['tarieven'].elements['inkoop_min_korting['+week+']'].value='';
			}
		} else {
			var inkoop_min_korting=parseFloat(document.forms['tarieven'].elements['bruto['+week+']'].value);
			if(inkoop_min_korting>0) {
				document.forms['tarieven'].elements['inkoop_min_korting['+week+']'].value=inkoop_min_korting.toFixed(2);
			} else {
				document.forms['tarieven'].elements['inkoop_min_korting['+week+']'].value='';
			}
		}
		if(document.forms['tarieven'].elements['inkoop_min_korting['+week+']'].value>0) var netto=parseFloat(document.forms['tarieven'].elements['inkoop_min_korting['+week+']'].value);
		if(document.forms['tarieven'].elements['toeslag['+week+']'].value>0) netto+=parseFloat(document.forms['tarieven'].elements['toeslag['+week+']'].value);
		if(document.forms['tarieven'].elements['korting_euro['+week+']'].value>0) netto-=parseFloat(document.forms['tarieven'].elements['korting_euro['+week+']'].value);

		if(document.forms['tarieven'].elements['vroegboekkorting_percentage['+week+']'].value>0) {
			netto-=inkoop_min_korting*(parseFloat(document.forms['tarieven'].elements['vroegboekkorting_percentage['+week+']'].value)/100);
		}

		if(document.forms['tarieven'].elements['vroegboekkorting_euro['+week+']'].value>0) netto-=parseFloat(document.forms['tarieven'].elements['vroegboekkorting_euro['+week+']'].value);

		if(netto>0) {
			document.forms['tarieven'].elements['netto['+week+']'].value=netto.toFixed(2);
		} else {
			document.forms['tarieven'].elements['netto['+week+']'].value='';
		}

		if(document.forms['tarieven'].elements['bruto['+week+']'].value>0) {
			var bruto=parseFloat(document.forms['tarieven'].elements['bruto['+week+']'].value);
		} else {
			var bruto=0;
		}

		if(document.forms['tarieven'].elements['skipas_bruto['+week+']'].value>0) {
			skipas_bruto=parseFloat(document.forms['tarieven'].elements['skipas_bruto['+week+']'].value);

			// korting op skipas van toepassing? -percentage-
			if(document.forms['tarieven'].elements['aanbieding_skipas_percentage['+week+']'].value) {
				skipas_bruto=skipas_bruto*(1-(parseFloat(document.forms['tarieven'].elements['aanbieding_skipas_percentage['+week+']'].value)/100));
			}
			// korting op skipas van toepassing? -euro-
			if(document.forms['tarieven'].elements['aanbieding_skipas_euro['+week+']'].value) {
				skipas_bruto=skipas_bruto-parseFloat(document.forms['tarieven'].elements['aanbieding_skipas_euro['+week+']'].value);
			}
			skipas_bruto=parseFloat(skipas_bruto).toFixed(2);
		} else {
			skipas_bruto=0;
		}

		if(bruto>0) {

			// inkoopkorting percentage verwerken
			if(document.forms['tarieven'].elements['inkoopkorting_percentage['+week+']'].value) {
				netto_na_inkoopkorting=parseFloat(document.forms['tarieven'].elements['netto['+week+']'].value);
				netto_na_inkoopkorting=netto_na_inkoopkorting*(1-(parseFloat(document.forms['tarieven'].elements['inkoopkorting_percentage['+week+']'].value)/100));

				if(netto_na_inkoopkorting) {
					document.forms['tarieven'].elements['netto['+week+']'].value=parseFloat(netto_na_inkoopkorting).toFixed(2);
					netto=netto_na_inkoopkorting;
				}
			}

			// inkoopkorting euro verwerken
			if(document.forms['tarieven'].elements['inkoopkorting_euro['+week+']'].value) {
				netto_na_inkoopkorting=parseFloat(document.forms['tarieven'].elements['netto['+week+']'].value);
				netto_na_inkoopkorting=netto_na_inkoopkorting-parseFloat(document.forms['tarieven'].elements['inkoopkorting_euro['+week+']'].value);

				if(netto_na_inkoopkorting) {
					document.forms['tarieven'].elements['netto['+week+']'].value=parseFloat(netto_na_inkoopkorting).toFixed(2);
					netto=netto_na_inkoopkorting;
				}
			}

			if(document.forms['tarieven'].elements['wederverkoop'].value==1) {

				// wederverkoop
				var wederverkoop_verkoopprijs=0;
				var wederverkoop_nettoprijs_agent=0;
				var wederverkoop_resterende_marge=0;
				var wederverkoop_marge=0;
				if(document.forms['tarieven'].elements['bruto['+week+']'].value>0) wederverkoop_verkoopprijs=parseFloat(document.forms['tarieven'].elements['bruto['+week+']'].value);

				// aanbieding_acc_percentage verwerken bij wederverkoop
				if(document.forms['tarieven'].elements['aanbieding_acc_percentage['+week+']'].value) {
					wederverkoop_verkoopprijs=wederverkoop_verkoopprijs*(1-(parseFloat(document.forms['tarieven'].elements['aanbieding_acc_percentage['+week+']'].value)/100));
				}
				// aanbieding_acc_euro verwerken bij wederverkoop
				if(document.forms['tarieven'].elements['aanbieding_acc_euro['+week+']'].value) {
					wederverkoop_verkoopprijs=wederverkoop_verkoopprijs-parseFloat(document.forms['tarieven'].elements['aanbieding_acc_euro['+week+']'].value);
				}

				if(document.forms['tarieven'].elements['opslag_accommodatie['+week+']'].value>0) wederverkoop_verkoopprijs+=(parseFloat(document.forms['tarieven'].elements['opslag_accommodatie['+week+']'].value)/100)*wederverkoop_verkoopprijs;

				if(document.forms['tarieven'].elements['wederverkoop_opslag_percentage['+week+']'].value>0) {
					wederverkoop_verkoopprijs=wederverkoop_verkoopprijs+bruto*(parseFloat(document.forms['tarieven'].elements['wederverkoop_opslag_percentage['+week+']'].value)/100);
				}
				wederverkoop_verkoopprijs=Math.floor(wederverkoop_verkoopprijs/5)*5;
				if(document.forms['tarieven'].elements['wederverkoop_opslag_euro['+week+']'].value) wederverkoop_verkoopprijs+=parseFloat(document.forms['tarieven'].elements['wederverkoop_opslag_euro['+week+']'].value);

				wederverkoop_nettoprijs_agent=wederverkoop_verkoopprijs;
				if(document.forms['tarieven'].elements['wederverkoop_commissie_agent['+week+']'].value>0) {
					wederverkoop_nettoprijs_agent=wederverkoop_nettoprijs_agent-wederverkoop_nettoprijs_agent*(parseFloat(document.forms['tarieven'].elements['wederverkoop_commissie_agent['+week+']'].value)/100);
				}
				wederverkoop_resterende_marge=wederverkoop_nettoprijs_agent-netto;
				if(wederverkoop_nettoprijs_agent>0) {
					wederverkoop_marge=wederverkoop_resterende_marge/wederverkoop_nettoprijs_agent*100;
				}
				if(wederverkoop_marge<10) {
					opmerkingen_text[week]='Let op! Wederverkoopmarge lager dan 10%!';
				} else {
					opmerkingen_text[week]='';
				}

				if(wederverkoop_verkoopprijs>0) {
					document.forms['tarieven'].elements['wederverkoop_verkoopprijs['+week+']'].value=wederverkoop_verkoopprijs.toFixed(2);
				} else {
					document.forms['tarieven'].elements['wederverkoop_verkoopprijs['+week+']'].value='';
				}

				if(wederverkoop_nettoprijs_agent>0) {
					document.forms['tarieven'].elements['wederverkoop_nettoprijs_agent['+week+']'].value=wederverkoop_nettoprijs_agent.toFixed(2);
				} else {
					document.forms['tarieven'].elements['wederverkoop_nettoprijs_agent['+week+']'].value='';
				}

				if(wederverkoop_resterende_marge>0) {
					document.forms['tarieven'].elements['wederverkoop_resterende_marge['+week+']'].value=wederverkoop_resterende_marge.toFixed(2);
				} else {
					document.forms['tarieven'].elements['wederverkoop_resterende_marge['+week+']'].value='';
				}

				if(wederverkoop_marge) {
					if(wederverkoop_marge<10) {
						document.forms['tarieven'].elements['wederverkoop_marge['+week+']'].style.color='red';
					} else {
						document.forms['tarieven'].elements['wederverkoop_marge['+week+']'].style.color='';
					}
					document.forms['tarieven'].elements['wederverkoop_marge['+week+']'].value=wederverkoop_marge.toFixed(2);
				} else {
					document.forms['tarieven'].elements['wederverkoop_marge['+week+']'].value='';
				}
			}

			if(document.forms['tarieven'].elements['bruto['+week+']'].value>0) {
				verkoop_accommodatie=parseFloat(document.forms['tarieven'].elements['bruto['+week+']'].value);
				// korting op accommodatie van toepassing? -percentage-
				if(document.forms['tarieven'].elements['aanbieding_acc_percentage['+week+']'].value) {
					verkoop_accommodatie=verkoop_accommodatie*(1-(parseFloat(document.forms['tarieven'].elements['aanbieding_acc_percentage['+week+']'].value)/100));
				}
				// korting op accommodatie van toepassing? -euro-
				if(document.forms['tarieven'].elements['aanbieding_acc_euro['+week+']'].value) {
					verkoop_accommodatie=verkoop_accommodatie-parseFloat(document.forms['tarieven'].elements['aanbieding_acc_euro['+week+']'].value);
				}
				verkoop_accommodatie=parseFloat(verkoop_accommodatie).toFixed(2);
			}

			if(verkoop_accommodatie) {
				document.forms['tarieven'].elements['verkoop_accommodatie['+week+']'].value=verkoop_accommodatie;
			} else {
				document.forms['tarieven'].elements['verkoop_accommodatie['+week+']'].value='';
			}

			for (i=min_tonen;i<=max;i++) {
				var verkoop=0;
				var verkoop_afgerond=0;
				var inkoop=0;
				var verkoop_site=0;
//				if(document.forms['tarieven'].elements['bruto['+week+']'].value>0) verkoop+=parseFloat(document.forms['tarieven'].elements['bruto['+week+']'].value)/i;
				if(verkoop_accommodatie>0) verkoop+=verkoop_accommodatie/i;
				verkoop+=parseFloat(skipas_bruto);
				if(document.forms['tarieven'].elements['opslag_accommodatie['+week+']'].value>0) verkoop+=(parseFloat(document.forms['tarieven'].elements['opslag_accommodatie['+week+']'].value)/100)*document.forms['tarieven'].elements['netto['+week+']'].value/i;
				if(document.forms['tarieven'].elements['opslag_skipas['+week+']'].value>0) verkoop+=(parseFloat(document.forms['tarieven'].elements['opslag_skipas['+week+']'].value)/100)*document.forms['tarieven'].elements['skipas_netto['+week+']'].value;
				if(document.forms['tarieven'].elements['afwijking_alle['+week+']'].value) {
					document.forms['tarieven'].elements['verkoop_afwijking['+week+']['+i+']'].value=parseFloat(document.forms['tarieven'].elements['afwijking_alle['+week+']'].value).toFixed(2);
				}
				if(verkoop>0 && skipas_bruto>0) {
					document.forms['tarieven'].elements['verkoop['+week+']['+i+']'].value=verkoop.toFixed(2);
				} else {
					document.forms['tarieven'].elements['verkoop['+week+']['+i+']'].value='';
				}
				if(document.forms['tarieven'].elements['netto['+week+']'].value>0) inkoop+=parseFloat(document.forms['tarieven'].elements['netto['+week+']'].value)/i
				if(document.forms['tarieven'].elements['skipas_prijs['+week+']'].value>0) inkoop+=parseFloat(document.forms['tarieven'].elements['skipas_prijs['+week+']'].value);
				if(inkoop>0 && skipas_bruto>0) {
					document.forms['tarieven'].elements['inkoop['+week+']['+i+']'].value=inkoop.toFixed(2);
					if(verkoop>0) {
						marge_percentage_afronding=(verkoop-inkoop)/verkoop*100;
						if(flexibel==1) {
							if(marge_percentage_afronding>12.5) {
								verkoop_afgerond=Math.floor(verkoop);
							} else {
								verkoop_afgerond=Math.ceil(verkoop);
							}
						} else {
							if(marge_percentage_afronding>12.5) {
								verkoop_afgerond=Math.floor(verkoop/5)*5;
							} else {
								verkoop_afgerond=Math.ceil(verkoop/5)*5;
							}
						}
						if(verkoop_afgerond==0) verkoop_afgerond=5;

						if(document.forms['tarieven'].elements['verkoop_afwijking['+week+']['+i+']'].value) {
							verkoop_site=verkoop_afgerond+parseFloat(document.forms['tarieven'].elements['verkoop_afwijking['+week+']['+i+']'].value);
						} else {
							verkoop_site=verkoop_afgerond;
						}

						if(verkoop_site>0) {
							marge_percentage=(verkoop_site-inkoop)/verkoop_site*100;
						} else {
							marge_percentage=0;
						}
						if(marge_percentage) {
							if(marge_percentage>0) {
								document.forms['tarieven'].elements['marge_percentage['+week+']['+i+']'].style.color='';
							} else {
								document.forms['tarieven'].elements['marge_percentage['+week+']['+i+']'].style.color='red';
							}
							document.forms['tarieven'].elements['marge_percentage['+week+']['+i+']'].value=marge_percentage.toFixed(2);
						} else {
							document.forms['tarieven'].elements['marge_percentage['+week+']['+i+']'].value='';
						}
						marge_euro=verkoop_site-inkoop;
						if(marge_euro) {
							if(marge_euro>0) {
								document.forms['tarieven'].elements['marge_euro['+week+']['+i+']'].style.color='';
							} else {
								document.forms['tarieven'].elements['marge_euro['+week+']['+i+']'].style.color='red';
							}
							document.forms['tarieven'].elements['marge_euro['+week+']['+i+']'].value=marge_euro.toFixed(2);
						} else {
							document.forms['tarieven'].elements['marge_euro['+week+']['+i+']'].value='';
						}
					}
				} else {
					document.forms['tarieven'].elements['inkoop['+week+']['+i+']'].value='';
					document.forms['tarieven'].elements['marge_percentage['+week+']['+i+']'].value='';
					document.forms['tarieven'].elements['marge_euro['+week+']['+i+']'].value='';
				}
				if(verkoop_afgerond>0) {
					document.forms['tarieven'].elements['verkoop_afgerond['+week+']['+i+']'].value=verkoop_afgerond.toFixed(2);
				} else {
					document.forms['tarieven'].elements['verkoop_afgerond['+week+']['+i+']'].value='';
				}
				if(verkoop_site>0) {
					document.forms['tarieven'].elements['verkoop_site['+week+']['+i+']'].value=verkoop_site.toFixed(2);
				} else {
					document.forms['tarieven'].elements['verkoop_site['+week+']['+i+']'].value='';
				}
			}
			document.forms['tarieven'].elements['afwijking_alle['+week+']'].value='';
		}

		if(document.forms['tarieven'].elements['verkoop_site['+week+']['+max+']'].value>0) {
			if(skipas_bruto>0) {
				var verkoop_min_skipas=parseFloat(document.forms['tarieven'].elements['verkoop_site['+week+']['+max+']'].value)-skipas_bruto;
			} else {
				var verkoop_min_skipas=parseFloat(document.forms['tarieven'].elements['verkoop_site['+week+']['+max+']'].value);
			}
			marge_accommodatie=((verkoop_min_skipas-(netto/max))/verkoop_min_skipas)*100;
		}
		if(marge_accommodatie) {
			if(marge_accommodatie>0) {
				document.forms['tarieven'].elements['marge_accommodatie['+week+']['+max+']'].style.color='';
			} else {
				document.forms['tarieven'].elements['marge_accommodatie['+week+']['+max+']'].style.color='red';
			}
			document.forms['tarieven'].elements['marge_accommodatie['+week+']['+max+']'].value=marge_accommodatie.toFixed(2);
		} else {
			document.forms['tarieven'].elements['marge_accommodatie['+week+']['+max+']'].value='';
		}
		if(dit=='kopieer') {

		} else {
			marge_gemiddelde('bruto');
		}

	} else if(toonper==2) {
		var inkoop_arrangementsprijs=0;
		var inkoop_onbezet_bed=0;
		var netto_inkoop_arrangementsprijs=0;
		var netto_inkoop_onbezet_bed=0;

		if(document.forms['tarieven'].elements['arrangementsprijs['+week+']'].value>0) {
			inkoop_arrangementsprijs=parseFloat(document.forms['tarieven'].elements['arrangementsprijs['+week+']'].value);
			if(document.forms['tarieven'].elements['korting_arrangement_bed_percentage['+week+']'].value>0) {
				inkoop_arrangementsprijs*=1-(parseFloat(document.forms['tarieven'].elements['korting_arrangement_bed_percentage['+week+']'].value)/100);
			}
			if(document.forms['tarieven'].elements['toeslag_arrangement_euro['+week+']'].value>0) {
				inkoop_arrangementsprijs+=parseFloat(document.forms['tarieven'].elements['toeslag_arrangement_euro['+week+']'].value);
			}
			if(document.forms['tarieven'].elements['korting_arrangement_euro['+week+']'].value>0) {
				inkoop_arrangementsprijs-=parseFloat(document.forms['tarieven'].elements['korting_arrangement_euro['+week+']'].value);
			}
		}
		if(document.forms['tarieven'].elements['onbezet_bed['+week+']'].value>0) {
			inkoop_onbezet_bed=parseFloat(document.forms['tarieven'].elements['onbezet_bed['+week+']'].value);
			if(document.forms['tarieven'].elements['korting_arrangement_bed_percentage['+week+']'].value>0) {
				inkoop_onbezet_bed*=1-(parseFloat(document.forms['tarieven'].elements['korting_arrangement_bed_percentage['+week+']'].value)/100);
			}
			if(document.forms['tarieven'].elements['toeslag_bed_euro['+week+']'].value>0) {
				inkoop_onbezet_bed+=parseFloat(document.forms['tarieven'].elements['toeslag_bed_euro['+week+']'].value);
			}
			if(document.forms['tarieven'].elements['korting_bed_euro['+week+']'].value>0) {
				inkoop_onbezet_bed-=parseFloat(document.forms['tarieven'].elements['korting_bed_euro['+week+']'].value);
			}
		}

		netto_inkoop_arrangementsprijs=inkoop_arrangementsprijs;
		if(document.forms['tarieven'].elements['vroegboekkorting_arrangement_percentage['+week+']'].value>0) {
			netto_inkoop_arrangementsprijs*=1-(parseFloat(document.forms['tarieven'].elements['vroegboekkorting_arrangement_percentage['+week+']'].value)/100);
		}
		if(document.forms['tarieven'].elements['vroegboekkorting_arrangement_euro['+week+']'].value>0) {
			netto_inkoop_arrangementsprijs-=parseFloat(document.forms['tarieven'].elements['vroegboekkorting_arrangement_euro['+week+']'].value);
		}

		netto_inkoop_onbezet_bed=inkoop_onbezet_bed;
		if(document.forms['tarieven'].elements['vroegboekkorting_bed_percentage['+week+']'].value>0) {
			netto_inkoop_onbezet_bed*=1-(parseFloat(document.forms['tarieven'].elements['vroegboekkorting_bed_percentage['+week+']'].value)/100);
		}
		if(document.forms['tarieven'].elements['vroegboekkorting_bed_euro['+week+']'].value>0) {
			netto_inkoop_onbezet_bed-=parseFloat(document.forms['tarieven'].elements['vroegboekkorting_bed_euro['+week+']'].value);
		}

		if(inkoop_arrangementsprijs) {
			document.forms['tarieven'].elements['inkoop_arrangementsprijs['+week+']'].value=inkoop_arrangementsprijs.toFixed(2);
		} else {
			document.forms['tarieven'].elements['inkoop_arrangementsprijs['+week+']'].value='';
		}
		if(inkoop_onbezet_bed) {
			document.forms['tarieven'].elements['inkoop_onbezet_bed['+week+']'].value=inkoop_onbezet_bed.toFixed(2);
		} else {
			document.forms['tarieven'].elements['inkoop_onbezet_bed['+week+']'].value='';
		}
		if(netto_inkoop_arrangementsprijs) {
			document.forms['tarieven'].elements['netto_inkoop_arrangementsprijs['+week+']'].value=netto_inkoop_arrangementsprijs.toFixed(2);
		} else {
			document.forms['tarieven'].elements['netto_inkoop_arrangementsprijs['+week+']'].value='';
		}
		if(netto_inkoop_onbezet_bed) {
			document.forms['tarieven'].elements['netto_inkoop_onbezet_bed['+week+']'].value=netto_inkoop_onbezet_bed.toFixed(2);
		} else {
			document.forms['tarieven'].elements['netto_inkoop_onbezet_bed['+week+']'].value='';
		}

		for (i=min_tonen;i<=max;i++) {
			var verkoop=0;
			var inkoop=0;
			var verkoop_afgerond=0;
			var verkoop_site=0;
			var marge_euro=0;
			var marge_percentage=0;
			if(document.forms['tarieven'].elements['afwijking_alle['+week+']'].value) {
				document.forms['tarieven'].elements['verkoop_afwijking['+week+']['+i+']'].value=parseFloat(document.forms['tarieven'].elements['afwijking_alle['+week+']'].value).toFixed(2);
			}
			if(netto_inkoop_arrangementsprijs>0 && netto_inkoop_onbezet_bed) {
				inkoop=parseFloat(document.forms['tarieven'].elements['netto_inkoop_arrangementsprijs['+week+']'].value);
				inkoop+=(max-i)/i*parseFloat(document.forms['tarieven'].elements['netto_inkoop_onbezet_bed['+week+']'].value);
				verkoop=inkoop;
				if(document.forms['tarieven'].elements['opslag['+week+']'].value) {
					verkoop/=(1-(document.forms['tarieven'].elements['opslag['+week+']'].value/100));
				}

				marge_percentage_afronding=(verkoop-inkoop)/verkoop*100;
				if(marge_percentage_afronding>12.5) {
					verkoop_afgerond=Math.floor(verkoop/5)*5;
				} else {
					verkoop_afgerond=Math.ceil(verkoop/5)*5;
				}
				if(verkoop_afgerond==0) verkoop_afgerond=5;

				if(document.forms['tarieven'].elements['verkoop_afwijking['+week+']['+i+']'].value) {
					verkoop_site=verkoop_afgerond+parseFloat(document.forms['tarieven'].elements['verkoop_afwijking['+week+']['+i+']'].value);
				} else {
					verkoop_site=verkoop_afgerond;
				}

				marge_euro=verkoop_site-inkoop;
				if(verkoop_site>0) {
					marge_percentage=(verkoop_site-inkoop)/verkoop_site*100;
				}
			}

			if(verkoop>0) {
				document.forms['tarieven'].elements['verkoop['+week+']['+i+']'].value=verkoop.toFixed(2);
			} else {
				document.forms['tarieven'].elements['verkoop['+week+']['+i+']'].value='';
			}

			if(inkoop>0) {
				document.forms['tarieven'].elements['inkoop['+week+']['+i+']'].value=inkoop.toFixed(2);
			} else {
				document.forms['tarieven'].elements['inkoop['+week+']['+i+']'].value='';
			}

			if(verkoop_afgerond>0) {
				document.forms['tarieven'].elements['verkoop_afgerond['+week+']['+i+']'].value=verkoop_afgerond.toFixed(2);
			} else {
				document.forms['tarieven'].elements['verkoop_afgerond['+week+']['+i+']'].value='';
			}

			if(verkoop_site>0) {
				document.forms['tarieven'].elements['verkoop_site['+week+']['+i+']'].value=verkoop_site.toFixed(2);
			} else {
				document.forms['tarieven'].elements['verkoop_site['+week+']['+i+']'].value='';
			}

			if(marge_percentage) {
				if(marge_percentage>0) {
					document.forms['tarieven'].elements['marge_percentage['+week+']['+i+']'].style.color='';
				} else {
					document.forms['tarieven'].elements['marge_percentage['+week+']['+i+']'].style.color='red';
				}
				document.forms['tarieven'].elements['marge_percentage['+week+']['+i+']'].value=marge_percentage.toFixed(2);
			} else {
				document.forms['tarieven'].elements['marge_percentage['+week+']['+i+']'].value='';
			}
			if(marge_euro) {
				if(marge_euro>0) {
					document.forms['tarieven'].elements['marge_euro['+week+']['+i+']'].style.color='';
				} else {
					document.forms['tarieven'].elements['marge_euro['+week+']['+i+']'].style.color='red';
				}
				document.forms['tarieven'].elements['marge_euro['+week+']['+i+']'].value=marge_euro.toFixed(2);
			} else {
				document.forms['tarieven'].elements['marge_euro['+week+']['+i+']'].value='';
			}
		}
		document.forms['tarieven'].elements['afwijking_alle['+week+']'].value='';
		if(dit=='kopieer') {

		} else {
			marge_gemiddelde('arrangementsprijs');
		}
	} else if(toonper==3) {
		var c_inkoop_min_korting=0;
		var c_netto=0;
		var c_verkoop=0;
		var c_verkoop_afgerond=0;
		var c_inkoop=0;
		var c_verkoop_site=0;
		var c_marge_percentage=0;
		var c_marge_euro=0;
		var c_marge_gemiddeld_teller=0;
		var c_marge_gemiddeld=0;

		if(document.forms['tarieven'].elements['c_bruto['+week+']'].value) {
			var c_bruto=parseFloat(document.forms['tarieven'].elements['c_bruto['+week+']'].value);
		} else {
			var c_bruto=0;
		}
		if(document.forms['tarieven'].elements['c_korting_percentage['+week+']'].value>0) {
			var c_inkoop_min_korting=c_bruto*(1-parseFloat(document.forms['tarieven'].elements['c_korting_percentage['+week+']'].value)/100);
		} else {
			var c_inkoop_min_korting=c_bruto;
		}
		c_netto=c_inkoop_min_korting;

		if(document.forms['tarieven'].elements['c_toeslag['+week+']'].value>0) c_netto+=parseFloat(document.forms['tarieven'].elements['c_toeslag['+week+']'].value);
		if(document.forms['tarieven'].elements['c_korting_euro['+week+']'].value>0) c_netto-=parseFloat(document.forms['tarieven'].elements['c_korting_euro['+week+']'].value);

		if(document.forms['tarieven'].elements['c_vroegboekkorting_percentage['+week+']'].value>0) c_netto-=c_inkoop_min_korting*(parseFloat(document.forms['tarieven'].elements['c_vroegboekkorting_percentage['+week+']'].value)/100);
		if(document.forms['tarieven'].elements['c_vroegboekkorting_euro['+week+']'].value>0) c_netto-=parseFloat(document.forms['tarieven'].elements['c_vroegboekkorting_euro['+week+']'].value);

		// c_verkoop bepalen (op basis van c_bruto, c_opslag_accommodatie en aanbiedingen/kortingen)
		c_verkoop=c_bruto;
		if(c_bruto>0) {
			if(document.forms['tarieven'].elements['c_opslag_accommodatie['+week+']'].value>0) {
				c_verkoop+=(parseFloat(document.forms['tarieven'].elements['c_opslag_accommodatie['+week+']'].value)/100)*c_netto;
			}

			// korting op accommodatie van toepassing? -percentage-
			if(document.forms['tarieven'].elements['aanbieding_acc_percentage['+week+']'].value) {
				c_verkoop=c_verkoop*(1-(parseFloat(document.forms['tarieven'].elements['aanbieding_acc_percentage['+week+']'].value)/100));
			}
			// korting op accommodatie van toepassing? -euro-
			if(document.forms['tarieven'].elements['aanbieding_acc_euro['+week+']'].value) {

				c_verkoop=c_verkoop-parseFloat(document.forms['tarieven'].elements['aanbieding_acc_euro['+week+']'].value);
			}
		}

		// inkoopkorting percentage verwerken
		if(document.forms['tarieven'].elements['inkoopkorting_percentage['+week+']'].value) {
			netto_na_inkoopkorting=c_netto;
			netto_na_inkoopkorting=netto_na_inkoopkorting*(1-(parseFloat(document.forms['tarieven'].elements['inkoopkorting_percentage['+week+']'].value)/100));

			if(netto_na_inkoopkorting) {
				document.forms['tarieven'].elements['c_netto['+week+']'].value=parseFloat(netto_na_inkoopkorting).toFixed(2);
				c_netto=netto_na_inkoopkorting;
			}
		}

		// inkoopkorting euro verwerken
		if(document.forms['tarieven'].elements['inkoopkorting_euro['+week+']'].value) {
			netto_na_inkoopkorting=c_netto;
			netto_na_inkoopkorting=netto_na_inkoopkorting-parseFloat(document.forms['tarieven'].elements['inkoopkorting_euro['+week+']'].value);

			if(netto_na_inkoopkorting) {
				document.forms['tarieven'].elements['c_netto['+week+']'].value=parseFloat(netto_na_inkoopkorting).toFixed(2);
				c_netto=netto_na_inkoopkorting;
			}
		}

		// marges en op basis daarvan afronding berekenen
		if(c_bruto && c_verkoop) {
			var marge_percentage_afronding=(c_verkoop-c_netto)/c_verkoop*100;
			if(flexibel==1) {
				if(marge_percentage_afronding>12.5) {
					c_verkoop_afgerond=Math.floor(c_verkoop);
				} else {
					c_verkoop_afgerond=Math.ceil(c_verkoop);
				}
			} else {
				if(marge_percentage_afronding>12.5) {
					c_verkoop_afgerond=Math.floor(c_verkoop/5)*5;
				} else {
					c_verkoop_afgerond=Math.ceil(c_verkoop/5)*5;
				}
			}
			if(c_verkoop_afgerond==0) c_verkoop_afgerond=5;
			if(document.forms['tarieven'].elements['c_verkoop_afwijking['+week+']'].value) {
				c_verkoop_site=c_verkoop_afgerond+parseFloat(document.forms['tarieven'].elements['c_verkoop_afwijking['+week+']'].value);
			} else {
				c_verkoop_site=c_verkoop_afgerond;
			}
			var c_marge_percentage=(c_verkoop_site-c_netto)/c_verkoop_site*100;
			var c_marge_euro=c_verkoop_site-c_netto;
		}


		if(document.forms['tarieven'].elements['wederverkoop'].value==1) {
			// wederverkoop
			var wederverkoop_verkoopprijs=0;
			var wederverkoop_nettoprijs_agent=0;
			var wederverkoop_resterende_marge=0;
			var wederverkoop_marge=0;
			if(c_verkoop_site>0) wederverkoop_verkoopprijs=parseFloat(c_verkoop_site);

			// aanbieding_acc_percentage verwerken bij wederverkoop
//			if(document.forms['tarieven'].elements['aanbieding_acc_percentage['+week+']'].value) {
//				wederverkoop_verkoopprijs=wederverkoop_verkoopprijs*(1-(parseFloat(document.forms['tarieven'].elements['aanbieding_acc_percentage['+week+']'].value)/100));
//			}

			// aanbieding_acc_euro verwerken bij wederverkoop
//			if(document.forms['tarieven'].elements['aanbieding_acc_euro['+week+']'].value) {
//				wederverkoop_verkoopprijs=wederverkoop_verkoopprijs-parseFloat(document.forms['tarieven'].elements['aanbieding_acc_euro['+week+']'].value);
//			}

			if(document.forms['tarieven'].elements['wederverkoop_opslag_percentage['+week+']'].value>0) {
				wederverkoop_verkoopprijs=wederverkoop_verkoopprijs+c_bruto*(parseFloat(document.forms['tarieven'].elements['wederverkoop_opslag_percentage['+week+']'].value)/100);
			}
			if(flexibel==1) {
				wederverkoop_verkoopprijs=Math.floor(wederverkoop_verkoopprijs);
			} else {
				wederverkoop_verkoopprijs=Math.floor(wederverkoop_verkoopprijs/5)*5;
			}
			if(document.forms['tarieven'].elements['wederverkoop_opslag_euro['+week+']'].value) wederverkoop_verkoopprijs+=parseFloat(document.forms['tarieven'].elements['wederverkoop_opslag_euro['+week+']'].value);

			wederverkoop_nettoprijs_agent=wederverkoop_verkoopprijs;
			if(document.forms['tarieven'].elements['wederverkoop_commissie_agent['+week+']'].value>0) {
				wederverkoop_nettoprijs_agent=wederverkoop_nettoprijs_agent-wederverkoop_nettoprijs_agent*(parseFloat(document.forms['tarieven'].elements['wederverkoop_commissie_agent['+week+']'].value)/100);
			}
			wederverkoop_resterende_marge=wederverkoop_nettoprijs_agent-c_netto;
			if(wederverkoop_nettoprijs_agent>0) {
				wederverkoop_marge=wederverkoop_resterende_marge/wederverkoop_nettoprijs_agent*100;
				if(wederverkoop_marge<10) {
					opmerkingen_text[week]='Let op! Wederverkoopmarge lager dan 10%!';
				} else {
					opmerkingen_text[week]='';
				}
			}

			if(wederverkoop_verkoopprijs>0) {
				document.forms['tarieven'].elements['wederverkoop_verkoopprijs['+week+']'].value=wederverkoop_verkoopprijs.toFixed(2);
			} else {
				document.forms['tarieven'].elements['wederverkoop_verkoopprijs['+week+']'].value='';
			}

			if(wederverkoop_nettoprijs_agent>0) {
				document.forms['tarieven'].elements['wederverkoop_nettoprijs_agent['+week+']'].value=wederverkoop_nettoprijs_agent.toFixed(2);
			} else {
				document.forms['tarieven'].elements['wederverkoop_nettoprijs_agent['+week+']'].value='';
			}

			if(wederverkoop_resterende_marge>0) {
				document.forms['tarieven'].elements['wederverkoop_resterende_marge['+week+']'].value=wederverkoop_resterende_marge.toFixed(2);
			} else {
				document.forms['tarieven'].elements['wederverkoop_resterende_marge['+week+']'].value='';
			}

			if(wederverkoop_marge) {
				if(wederverkoop_marge<10) {
					document.forms['tarieven'].elements['wederverkoop_marge['+week+']'].style.color='red';
				} else {
					document.forms['tarieven'].elements['wederverkoop_marge['+week+']'].style.color='';
				}
				document.forms['tarieven'].elements['wederverkoop_marge['+week+']'].value=wederverkoop_marge.toFixed(2);
			} else {
				document.forms['tarieven'].elements['wederverkoop_marge['+week+']'].value='';
			}
		}

		if(c_inkoop_min_korting>0) {
			document.forms['tarieven'].elements['c_inkoop_min_korting['+week+']'].value=c_inkoop_min_korting.toFixed(2);
		} else {
			document.forms['tarieven'].elements['c_inkoop_min_korting['+week+']'].value='';
		}
		if(c_netto>0) {
			document.forms['tarieven'].elements['c_netto['+week+']'].value=c_netto.toFixed(2);
			document.forms['tarieven'].elements['c_inkoop['+week+']'].value=c_netto.toFixed(2);
		} else {
			document.forms['tarieven'].elements['c_netto['+week+']'].value='';
			document.forms['tarieven'].elements['c_inkoop['+week+']'].value='';
		}
		if(c_verkoop>0) {
			document.forms['tarieven'].elements['c_verkoop['+week+']'].value=c_verkoop.toFixed(2);
		} else {
			document.forms['tarieven'].elements['c_verkoop['+week+']'].value='';
		}
		if(c_verkoop_afgerond>0) {
			document.forms['tarieven'].elements['c_verkoop_afgerond['+week+']'].value=c_verkoop_afgerond.toFixed(2);
		} else {
			document.forms['tarieven'].elements['c_verkoop_afgerond['+week+']'].value='';
		}
		if(c_verkoop_site>0) {
			document.forms['tarieven'].elements['c_verkoop_site['+week+']'].value=c_verkoop_site.toFixed(2);
		} else {
			document.forms['tarieven'].elements['c_verkoop_site['+week+']'].value='';
		}

		if(c_marge_percentage) {
			if(c_marge_percentage>0) {
				document.forms['tarieven'].elements['c_marge_percentage['+week+']'].style.color='';
			} else {
				document.forms['tarieven'].elements['c_marge_percentage['+week+']'].style.color='red';
			}
			document.forms['tarieven'].elements['c_marge_percentage['+week+']'].value=c_marge_percentage.toFixed(2);
		} else {
			document.forms['tarieven'].elements['c_marge_percentage['+week+']'].value='';
		}
		if(c_marge_euro) {
			if(c_marge_euro>0) {
				document.forms['tarieven'].elements['c_marge_euro['+week+']'].style.color='';
			} else {
				document.forms['tarieven'].elements['c_marge_euro['+week+']'].style.color='red';
			}
			document.forms['tarieven'].elements['c_marge_euro['+week+']'].value=c_marge_euro.toFixed(2);
		} else {
			document.forms['tarieven'].elements['c_marge_euro['+week+']'].value='';
		}

		for(var i=0;i<document.forms['weken'].elements.length;i++) {
			week=document.forms['weken'].elements[i].value;
			if(document.forms['tarieven'].elements['c_bruto['+week+']'].value>0) {
				if(document.forms['tarieven'].elements['c_marge_percentage['+week+']'].value) c_marge_gemiddeld+=parseFloat(document.forms['tarieven'].elements['c_marge_percentage['+week+']'].value);
				c_marge_gemiddeld_teller++;
			}
		}

		if(c_marge_gemiddeld && c_marge_gemiddeld_teller>0) {
			c_marge_gemiddeld=c_marge_gemiddeld/c_marge_gemiddeld_teller;
			if(c_marge_gemiddeld>0) {
				document.forms['tarieven'].elements['c_marge_gemiddeld'].style.color='';
			} else {
				document.forms['tarieven'].elements['c_marge_gemiddeld'].style.color='red';
			}
			document.forms['tarieven'].elements['c_marge_gemiddeld'].value=parseFloat(c_marge_gemiddeld).toFixed(2);
		} else {
			document.forms['tarieven'].elements['c_marge_gemiddeld'].value='';
		}
	} else if(toonper==4) {

	}

	if(dit=='kopieer') {

	} else {
		toon_opmerkingen();
	}
}

function bereken_opties(dit,week) {

	if(dit=='kopieer') {

	} else {
		dit.value=dit.value.replace(',','.');
		if(dit.value) {
			dit.value=parseFloat(dit.value).toFixed(2);
		} else {
			dit.value='';
		}
	}
	var inkoop_netto=0;
	var marge_euro=0;
	var marge_percentage=0;
	var marge_gemiddeld=0;
	var marge_gemiddeld_teller=0;

	// velden op readonly zetten (korting v.s. netto inkoop)
	if(document.forms['tarieven'].elements['netto_ink['+week+']'].value!='') {
		document.forms['tarieven'].elements['korting['+week+']'].value='';
		document.forms['tarieven'].elements['korting['+week+']'].readOnly=true;
		document.forms['tarieven'].elements['korting_euro['+week+']'].value='';
		document.forms['tarieven'].elements['korting_euro['+week+']'].readOnly=true;
	} else {
		document.forms['tarieven'].elements['korting['+week+']'].readOnly=false;
		document.forms['tarieven'].elements['korting_euro['+week+']'].readOnly=false;
	}
	if(document.forms['tarieven'].elements['korting['+week+']'].value!=''||document.forms['tarieven'].elements['korting_euro['+week+']'].value!='') {
		document.forms['tarieven'].elements['netto_ink['+week+']'].value='';
		document.forms['tarieven'].elements['netto_ink['+week+']'].readOnly=true;
	} else {
		document.forms['tarieven'].elements['netto_ink['+week+']'].readOnly=false;
	}

	if(document.forms['tarieven'].elements['verkoop['+week+']'].value) {
		var verkoop=parseFloat(document.forms['tarieven'].elements['verkoop['+week+']'].value);
	} else {
		var verkoop=0;
	}
	if(document.forms['tarieven'].elements['inkoop['+week+']'].value) {
		var inkoop=parseFloat(document.forms['tarieven'].elements['inkoop['+week+']'].value);
	} else {
		var inkoop=0;
	}
	if(document.forms['tarieven'].elements['netto_ink['+week+']'].value>0) {
		// netto inkoop
		var inkoop_netto=parseFloat(document.forms['tarieven'].elements['netto_ink['+week+']'].value);
	} else {
		var inkoop_netto=inkoop;
		// korting
		if(document.forms['tarieven'].elements['korting['+week+']'].value>0) {
			inkoop_netto=inkoop_netto*(1-parseFloat(document.forms['tarieven'].elements['korting['+week+']'].value)/100);
		}
		if(document.forms['tarieven'].elements['korting_euro['+week+']'].value>0) {
			inkoop_netto=inkoop_netto-parseFloat(document.forms['tarieven'].elements['korting_euro['+week+']'].value);
		}
	}
	if(document.forms['tarieven'].elements['skipas_netto_inkoop['+week+']']) {
		subtotaal=inkoop_netto-document.forms['tarieven'].elements['skipas_netto_inkoop['+week+']'].value;
	} else {
		subtotaal=inkoop_netto;
	}

	if(document.forms['tarieven'].elements['omzetbonus['+week+']'].value>0) {
		var inkoop_netto=subtotaal*(1-parseFloat(document.forms['tarieven'].elements['omzetbonus['+week+']'].value)/100);
	} else {
		var inkoop_netto=subtotaal;
	}

	if(verkoop) {
		marge_euro=verkoop-inkoop_netto;
		marge_percentage=(verkoop-inkoop_netto)/verkoop*100;
	}
	if(inkoop_netto) {
		document.forms['tarieven'].elements['inkoop_netto['+week+']'].value=parseFloat(inkoop_netto).toFixed(2);
	} else {
		document.forms['tarieven'].elements['inkoop_netto['+week+']'].value='';
	}

	if(document.forms['tarieven'].elements['subtotaal['+week+']']) {
		if(subtotaal) {
			document.forms['tarieven'].elements['subtotaal['+week+']'].value=parseFloat(subtotaal).toFixed(2);
		} else {
			document.forms['tarieven'].elements['subtotaal['+week+']'].value='';
		}
	}

	if(marge_euro) {
		if(marge_euro>0) {
			document.forms['tarieven'].elements['marge_euro['+week+']'].style.color='';
		} else {
			document.forms['tarieven'].elements['marge_euro['+week+']'].style.color='red';
		}
		document.forms['tarieven'].elements['marge_euro['+week+']'].value=parseFloat(marge_euro).toFixed(2);
	} else {
		document.forms['tarieven'].elements['marge_euro['+week+']'].value='';
	}
	if(marge_percentage) {
		if(marge_percentage>0) {
			document.forms['tarieven'].elements['marge_percentage['+week+']'].style.color='';
		} else {
			document.forms['tarieven'].elements['marge_percentage['+week+']'].style.color='red';
		}
		document.forms['tarieven'].elements['marge_percentage['+week+']'].value=parseFloat(marge_percentage).toFixed(2);
	} else {
		document.forms['tarieven'].elements['marge_percentage['+week+']'].value='';
	}


	// wederverkoop
	var wederverkoop_commissie_agent=0;
	if(document.forms['tarieven'].elements['wederverkoop_commissie_agent['+week+']'].value>0) {
		wederverkoop_commissie_agent=document.forms['tarieven'].elements['wederverkoop_commissie_agent['+week+']'].value;
	}
	if(verkoop>0 && inkoop>0 && wederverkoop_commissie_agent>0) {
		var wederverkoop_verkoopprijs=verkoop;
		var wederverkoop_nettoprijs_agent=0;
		var wederverkoop_resterende_marge=0;
		var wederverkoop_marge=0;

		wederverkoop_nettoprijs_agent=wederverkoop_verkoopprijs-wederverkoop_verkoopprijs*(parseFloat(wederverkoop_commissie_agent)/100);
		wederverkoop_resterende_marge=wederverkoop_nettoprijs_agent-inkoop_netto;
		if(wederverkoop_nettoprijs_agent>0) {
			wederverkoop_marge=wederverkoop_resterende_marge/wederverkoop_nettoprijs_agent*100;
		}

		document.forms['tarieven'].elements['wederverkoop_nettoprijs_agent['+week+']'].value=wederverkoop_nettoprijs_agent.toFixed(2);
		document.forms['tarieven'].elements['wederverkoop_resterende_marge['+week+']'].value=wederverkoop_resterende_marge.toFixed(2);
		document.forms['tarieven'].elements['wederverkoop_marge['+week+']'].value=wederverkoop_marge.toFixed(2);

		if(wederverkoop_resterende_marge<=0) {
			document.forms['tarieven'].elements['wederverkoop_commissie_agent['+week+']'].style.color='red';
			document.forms['tarieven'].elements['wederverkoop_resterende_marge['+week+']'].style.color='red';
			document.forms['tarieven'].elements['wederverkoop_marge['+week+']'].style.color='red';
		} else {
			document.forms['tarieven'].elements['wederverkoop_commissie_agent['+week+']'].style.color='';
			document.forms['tarieven'].elements['wederverkoop_resterende_marge['+week+']'].style.color='';
			document.forms['tarieven'].elements['wederverkoop_marge['+week+']'].style.color='';
		}
	} else {
		document.forms['tarieven'].elements['wederverkoop_nettoprijs_agent['+week+']'].value='';
		document.forms['tarieven'].elements['wederverkoop_resterende_marge['+week+']'].value='';
		document.forms['tarieven'].elements['wederverkoop_marge['+week+']'].value='';
	}


	// gemiddelden
	for(var i=0;i<document.forms['weken'].elements.length;i++) {
		week=document.forms['weken'].elements[i].value;
		if(document.forms['tarieven'].elements['verkoop['+week+']'].value>0) {
			if(document.forms['tarieven'].elements['marge_percentage['+week+']'].value) marge_gemiddeld+=parseFloat(document.forms['tarieven'].elements['marge_percentage['+week+']'].value);
			marge_gemiddeld_teller++;
		}
	}

	if(marge_gemiddeld && marge_gemiddeld_teller>0) {
		marge_gemiddeld=marge_gemiddeld/marge_gemiddeld_teller;
		if(marge_gemiddeld>0) {
			document.forms['tarieven'].elements['marge_gemiddeld'].style.color='';
		} else {
			document.forms['tarieven'].elements['marge_gemiddeld'].style.color='red';
		}
		document.forms['tarieven'].elements['marge_gemiddeld'].value=parseFloat(marge_gemiddeld).toFixed(2);
	} else {
		document.forms['tarieven'].elements['marge_gemiddeld'].value='';
	}

}


function marge_gemiddelde(naam) {
	var marge_gemiddeld=0;
	var marge_gemiddeld_teller=0;
	var toonper=document.forms['tarieven'].elements['toonper'].value

	if(toonper==1 || toonper==2) {
		for(var i=0;i<document.forms['weken'].elements.length;i++) {
			week=document.forms['weken'].elements[i].value;
			if(typeof(document.forms['tarieven'].elements[naam+'['+week+']'])!="undefined") {
				if(document.forms['tarieven'].elements[naam+'['+week+']'].value>0) {
					bedrag=document.forms['tarieven'].elements['marge_percentage['+week+']['+document.forms['tarieven'].elements['max'].value+']'].value;
					if(bedrag) marge_gemiddeld+=parseFloat(bedrag);
					marge_gemiddeld_teller++;
				}
			}
		}

		if(marge_gemiddeld && marge_gemiddeld_teller>0) {
			marge_gemiddeld=parseFloat(marge_gemiddeld)/marge_gemiddeld_teller;
			if(marge_gemiddeld>0) {
				document.forms['tarieven'].elements['marge_gemiddeld'].style.color='';
			} else {
				document.forms['tarieven'].elements['marge_gemiddeld'].style.color='red';
			}
			document.forms['tarieven'].elements['marge_gemiddeld'].value=parseFloat(marge_gemiddeld).toFixed(2);
		} else {
			document.forms['tarieven'].elements['marge_gemiddeld'].value='';
		}
	}
}

function optellen(naam) {
	var totaal=0;
	for(var i=0;i<document.forms['weken'].elements.length;i++) {
		week=document.forms['weken'].elements[i].value;
		if(document.forms['tarieven'].elements[naam+'['+week+']'].value>0) totaal+=parseFloat(document.forms['tarieven'].elements[naam+'['+week+']'].value);
	}

	if(totaal>0) {
		document.forms['tarieven'].elements[naam+'_totaal'].value=parseFloat(totaal).toFixed(2);
	} else {
		document.forms['tarieven'].elements[naam+'_totaal'].value='';
	}
}

function garantie_niet_afboeken(dit,week) {
	if(parseInt(dit.value,10)<parseInt(document.forms['tarieven'].elements['gar['+week+']'].value,10)) {
		alert('Garantie afboeken kan niet op deze manier. Doe dat via het menu-item Garanties.');
		dit.value=document.forms['tarieven'].elements['gar['+week+']'].value;
	}
	return true;
}

function blokkeerxml(dit,week) {
	if(dit.checked) {
		document.forms['tarieven'].elements['voorraad_xml['+week+']'].value='';
	}
	bereken_voorraad(dit,week);
}

$(document).ready(function() {
	//
	// jquery
	//

	// verzameltype: buttons uitschakelen
	if($("input[name=verzameltype]").length!=0) {
		if(document.forms['tarieven'].elements["verzameltype"].value==1) {
			$('#but1').attr("disabled", true);
			$('#but3').attr("disabled", true);
		}
	}

	// gekoppelde voorraad?
	if($("input[name=voorraad_gekoppeld_type_id]").length!=0) {
		if(document.forms['tarieven'].elements["voorraad_gekoppeld_type_id"].value>0) {
			// dit betreft een type waarbij de voorraad door een voorraad-houder bepaald wordt: bewerken niet mogelijk
			$(".voorraad_archief").hide();
			$(".voorraad_opmerkingen_voorraad_log").hide();
			$(".voorraad_tr input").attr("readonly", true);
			$(".voorraad_tr").addClass("voorraad_readonly");

			// zorgen dat checkboxes niet klikbaar zijn
			$(".voorraad_tr input").click(function(event) {
				$(this).blur();
				event.preventDefault();
			});

		}
	}

	$("input[name=wis_bruto]").change(function() {
		// alle bruto-tarieven wissen via checkbox
		if($(this).is(':checked')) {
			for(var i=0;i<document.forms['weken'].elements.length;i++) {
				week=document.forms['weken'].elements[i].value;
				$("input[name='bruto["+week+"]']").val("");
				$("input[name='c_bruto["+week+"]']").val("");
				bereken('kopieer',week);
			}
		}
	});


	$("input[id^=voorraad_garantie]").change(function() {
		var ingevulde_waarde=parseInt($(this).val(),10);
		if(ingevulde_waarde>100) {
			$(this).val("");
			alert("Foutmelding: je kunt maximaal 100 garanties invoeren.");
		}
	});


	if($("#eigenaar_formulier").length!==0) {

		//
		//
		// eigenaar_blokkering
		//
		//

		// Geblokkeerd door eigenaar
		$(".eigenarenlogin_opmerkingen input").click(function(event) {
			event.preventDefault();




			eigenarenlogin_soort = $(this).parents(".eigenarenlogin_opmerkingen").data("soort");
			eigenaar_formulier_week = $(this).data("week");

			if(eigenarenlogin_soort=="bezeteigenaar" && $("input[name='boekingderden["+eigenaar_formulier_week+"]']").is(":checked")) {
				$("input[name='boekingderden["+eigenaar_formulier_week+"]']").parent("td").effect("highlight", {}, 1000);
			} else if(eigenarenlogin_soort=="bezeteigenaar" &&  $("input[name='nietbeschikbaarverhuur["+eigenaar_formulier_week+"]']").is(":checked")) {
				$("input[name='nietbeschikbaarverhuur["+eigenaar_formulier_week+"]']").parent("td").effect("highlight", {}, 1000);
			} else if(eigenarenlogin_soort=="boekingderden" &&  $("input[name='bezeteigenaar["+eigenaar_formulier_week+"]']").is(":checked")) {
				$("input[name='bezeteigenaar["+eigenaar_formulier_week+"]']").parent("td").effect("highlight", {}, 1000);
			} else if(eigenarenlogin_soort=="boekingderden" &&  $("input[name='nietbeschikbaarverhuur["+eigenaar_formulier_week+"]']").is(":checked")) {
				$("input[name='nietbeschikbaarverhuur["+eigenaar_formulier_week+"]']").parent("td").effect("highlight", {}, 1000);
			} else if(eigenarenlogin_soort=="nietbeschikbaarverhuur" &&  $("input[name='bezeteigenaar["+eigenaar_formulier_week+"]']").is(":checked")) {
				$("input[name='bezeteigenaar["+eigenaar_formulier_week+"]']").parent("td").effect("highlight", {}, 1000);
			} else if(eigenarenlogin_soort=="nietbeschikbaarverhuur" &&  $("input[name='boekingderden["+eigenaar_formulier_week+"]']").is(":checked")) {
				$("input[name='boekingderden["+eigenaar_formulier_week+"]']").parent("td").effect("highlight", {}, 1000);
			} else if($(this).is(":checked") && $("input[name='aantal_geboekt["+eigenaar_formulier_week+"]']").val().length>0) {
				$("input[name='aantal_geboekt["+eigenaar_formulier_week+"]']").parent("td").effect("highlight", {}, 1000);
			} else {

				$("input[name=eigenaar_formulier_voorraad_wissen]").parent().hide();
				$("#eigenaar_formulier_voorraad_wissen").prop("checked", false);
				if($("input[name='opmerking_"+eigenarenlogin_soort+"["+eigenaar_formulier_week+"]']").val().length>0) {
					$("#eigenaar_formulier input[type=submit]").val("WIJZIGEN");
					$("#eigenaar_formulier #submit").prop("disabled", false);
					$("#eigenaar_formulier #delete").show();
				} else {
					$("#eigenaar_formulier input[type=submit]").val("WEEK BLOKKEREN");
					$("#eigenaar_formulier #submit").prop("disabled", true);
					$("#eigenaar_formulier #delete").hide();

					// voorraad
					if(
						parseInt($("input[name='voorraad_totaal["+eigenaar_formulier_week+"]']").val(),10)>0 ||
						parseInt($("input[name='voorraad_allotment["+eigenaar_formulier_week+"]']").val(),10)>0 ||
						parseInt($("input[name='voorraad_vervallen_allotment["+eigenaar_formulier_week+"]']").val(),10)>0 ||
						parseInt($("input[name='voorraad_optie_leverancier["+eigenaar_formulier_week+"]']").val(),10)>0 ||
						parseInt($("input[name='voorraad_request["+eigenaar_formulier_week+"]']").val(),10)>0 ||
						parseInt($("input[name='voorraad_optie_klant["+eigenaar_formulier_week+"]']").val(),10)>0
					) {
						$("#eigenaar_formulier span.voorraad").html($("input[name='voorraad_totaal["+eigenaar_formulier_week+"]']").val());
						$("input[name=eigenaar_formulier_voorraad_wissen]").parent().show();
					}
				}

				var date = new Date(parseInt($("input[name='aankomstdatum_exact["+eigenaar_formulier_week+"]']").val(), 10) * 1000);
				var curr_date = date.getDate();
				var curr_month = date.getMonth() + 1; //Months are zero based
				var curr_year = date.getFullYear();
				$("#eigenaar_formulier span.date").html( ("0" + curr_date).slice(-2) + "/" + ("0" + curr_month).slice(-2) + "/" + curr_year);
				$("#eigenaar_formulier span.soort").html($(this).parents(".eigenarenlogin_opmerkingen").children("td:first-child").html());


				// fill all fields
				$("input[name=eigenaar_formulier_opmerking]").val($("input[name='opmerking_"+eigenarenlogin_soort+"["+eigenaar_formulier_week+"]']").val());
				$("input[name=eigenaar_formulier_begin]").val($("input[name='begin_"+eigenarenlogin_soort+"["+eigenaar_formulier_week+"]']").val());
				$("input[name=eigenaar_formulier_eind]").val($("input[name='eind_"+eigenarenlogin_soort+"["+eigenaar_formulier_week+"]']").val());
				$("input[name=eigenaar_formulier_deelnemers]").val($("input[name='deelnemers_"+eigenarenlogin_soort+"["+eigenaar_formulier_week+"]']").val());
				$("input[name=eigenaar_formulier_tekst_extra_options]").val($("input[name='tekst_extra_options_"+eigenarenlogin_soort+"["+eigenaar_formulier_week+"]']").val());



				// prevalue for date fields
				if($("input[name=eigenaar_formulier_begin]").val()=="") {
					$("input[name=eigenaar_formulier_begin]").val( ("0" + curr_date).slice(-2) + "/" + ("0" + curr_month).slice(-2) + "/" + curr_year);
				}
				$("input[name=eigenaar_formulier_begin_hidden]").val( ("0" + curr_date).slice(-2) + "/" + ("0" + curr_month).slice(-2) + "/" + curr_year);

				// determine eind
				date = new Date(parseInt($("input[name='aankomstdatum_exact["+eigenaar_formulier_week+"]']").next(".aankomstdatum_exact_data").val(), 10) * 1000);
				curr_date = date.getDate();
				curr_month = date.getMonth() + 1; //Months are zero based
				curr_year = date.getFullYear();
				if($("input[name=eigenaar_formulier_eind]").val()=="") {
					$("input[name=eigenaar_formulier_eind]").val( ("0" + curr_date).slice(-2) + "/" + ("0" + curr_month).slice(-2) + "/" + curr_year);
				}

				$("input[name=eigenaar_formulier_eind_hidden]").val( ("0" + curr_date).slice(-2) + "/" + ("0" + curr_month).slice(-2) + "/" + curr_year);

				// hide fields
				if(eigenarenlogin_soort=="boekingderden" || eigenarenlogin_soort=="nietbeschikbaarverhuur") {
					$("input[name=eigenaar_formulier_deelnemers]").val("");
					$("input[name=eigenaar_formulier_tekst_extra_options]").val("");
					$("input[name=eigenaar_formulier_deelnemers]").parent().hide();
					$("input[name=eigenaar_formulier_tekst_extra_options]").parent().hide();
				} else {
					$("input[name=eigenaar_formulier_deelnemers]").parent().show();
					$("input[name=eigenaar_formulier_tekst_extra_options]").parent().show();
				}


				// show form, dim other content
				$("#dim_content").show();
				$("#eigenaar_formulier").show();
			}

			return false;

		});

		// click date-fields
		$("input[name=eigenaar_formulier_begin]").focus(function(event){
			if(!$("input[name=eigenaar_formulier_begin]").val()) {
				$("input[name=eigenaar_formulier_begin]").val($("input[name=eigenaar_formulier_begin_hidden]").val());
			}
		});
		// click date-fields
		$("input[name=eigenaar_formulier_eind]").focus(function(event){
			if(!$("input[name=eigenaar_formulier_eind]").val()) {
				$("input[name=eigenaar_formulier_eind]").val($("input[name=eigenaar_formulier_eind_hidden]").val());
			}
		});

		// check date-fields
		$("input[name=eigenaar_formulier_begin], input[name=eigenaar_formulier_eind]").keyup(function(event){

			$(this).val($(this).val().replace("-", "/"));
			$(this).val($(this).val().replace(" ", ""));

			eigenaar_formulier_check();

		});

		var eigenaar_formulier_check_correct_fields = 0;

		function eigenaar_formulier_check_date(deze) {

			var new_date_res = $(deze).val().split("/");

			var new_date = new Date(new_date_res[2], new_date_res[1], new_date_res[0]);

			var min_date_res = $("input[name=eigenaar_formulier_begin_hidden]").val().split("/");
			var min_date = new Date(min_date_res[2], min_date_res[1], min_date_res[0]);

			var max_date_res = $("input[name=eigenaar_formulier_eind_hidden]").val().split("/");
			var max_date = new Date(max_date_res[2], max_date_res[1], max_date_res[0]);

			if(new_date.getTime()<min_date.getTime() || new_date.getTime()>max_date.getTime()) {
				$(deze).css("color", "red");
				return false;
			} else {
				$(deze).css("color", "#000000");
				return true;
			}
		}

		function eigenaar_formulier_check() {

			var eigenaar_formulier_check_correct_fields=0;

			if($("input[name=eigenaar_formulier_begin]").val().length>0) {
				if(eigenaar_formulier_check_date("input[name=eigenaar_formulier_begin]")) {
					eigenaar_formulier_check_correct_fields = eigenaar_formulier_check_correct_fields + 1;
				}
			} else {
				eigenaar_formulier_check_correct_fields = eigenaar_formulier_check_correct_fields + 1;
			}
			if($("input[name=eigenaar_formulier_eind]").val().length>0) {
				if(eigenaar_formulier_check_date("input[name=eigenaar_formulier_eind]")) {
					eigenaar_formulier_check_correct_fields = eigenaar_formulier_check_correct_fields + 1;
				}
			} else {
				eigenaar_formulier_check_correct_fields = eigenaar_formulier_check_correct_fields + 1;
			}

			if($("input[name=eigenaar_formulier_opmerking]").val().length>0) {
				eigenaar_formulier_check_correct_fields = eigenaar_formulier_check_correct_fields + 1;
			}

			if(eigenaar_formulier_check_correct_fields==3) {
				$("#eigenaar_formulier #submit").prop("disabled", false);
			} else {
				$("#eigenaar_formulier #submit").prop("disabled", true);
			}
		}

		// save-button: only with filled opmerking
		$("input[name=eigenaar_formulier_opmerking]").bind("input propertychange", function() {
			eigenaar_formulier_check();
		});

		// click cancel
		$("#eigenaar_formulier .cancel").click(function(event) {
			$("#eigenaar_formulier").hide();
			$("#dim_content").hide();
		});

		// click submit
		$("#eigenaar_formulier #submit").click(function(event) {

			// $("input[name='"+eigenarenlogin_soort+"_voorheen["+eigenaar_formulier_week+"]']").val("1");
			$("input[name='opmerking_"+eigenarenlogin_soort+"["+eigenaar_formulier_week+"]']").val($("input[name=eigenaar_formulier_opmerking]").val());
			$("input[name='begin_"+eigenarenlogin_soort+"["+eigenaar_formulier_week+"]']").val($("input[name=eigenaar_formulier_begin]").val());
			$("input[name='eind_"+eigenarenlogin_soort+"["+eigenaar_formulier_week+"]']").val($("input[name=eigenaar_formulier_eind]").val());
			$("input[name='deelnemers_"+eigenarenlogin_soort+"["+eigenaar_formulier_week+"]']").val($("input[name=eigenaar_formulier_deelnemers]").val());
			$("input[name='tekst_extra_options_"+eigenarenlogin_soort+"["+eigenaar_formulier_week+"]']").val($("input[name=eigenaar_formulier_tekst_extra_options]").val());

			// make it possible to log this change
			$("<input>").attr({
				type: "hidden",
				name: "beschikbaarheidslog_"+eigenarenlogin_soort+"["+eigenaar_formulier_week+"]",
				value: $("input[name=eigenaar_formulier_opmerking]").val()
			}).appendTo("form#tarieven");

			$("input[name='"+eigenarenlogin_soort+"["+eigenaar_formulier_week+"]']").prop("checked", true);
			// $("input[name='beschikbaar["+eigenaar_formulier_week+"]']").prop("checked", false);

			// voorraad wissen
			if ( $("input[name=eigenaar_formulier_voorraad_wissen]").is(":checked")) {
				$("input[name='voorraad_allotment["+eigenaar_formulier_week+"]']").val("");
				$("input[name='voorraad_vervallen_allotment["+eigenaar_formulier_week+"]']").val("");
				$("input[name='voorraad_optie_leverancier["+eigenaar_formulier_week+"]']").val("");
				$("input[name='voorraad_request["+eigenaar_formulier_week+"]']").val("");
				$("input[name='voorraad_optie_klant["+eigenaar_formulier_week+"]']").val("");
				bereken_voorraad("opnieuw_berekenen",eigenaar_formulier_week);

				// make it possible to log this deletion
				$("<input>").attr({
					type: "hidden",
					name: "voorraad_gewist_na_blokkade["+eigenaar_formulier_week+"]",
					value: "1"
				}).appendTo("form#tarieven");
			}

			$("#eigenaar_formulier").hide();
			$("#dim_content").hide();
		});

		// click delete
		$("#eigenaar_formulier #delete").click(function(event) {

			// $("input[name='"+eigenarenlogin_soort+"_voorheen["+eigenaar_formulier_week+"]']").val("0");
			$("input[name='opmerking_"+eigenarenlogin_soort+"["+eigenaar_formulier_week+"]']").val("");
			$("input[name='begin_"+eigenarenlogin_soort+"["+eigenaar_formulier_week+"]']").val("");
			$("input[name='eind_"+eigenarenlogin_soort+"["+eigenaar_formulier_week+"]']").val("");
			$("input[name='deelnemers_"+eigenarenlogin_soort+"["+eigenaar_formulier_week+"]']").val("");
			$("input[name='tekst_extra_options_"+eigenarenlogin_soort+"["+eigenaar_formulier_week+"]']").val("");

			$("input[name='"+eigenarenlogin_soort+"["+eigenaar_formulier_week+"]']").prop("checked", false);

			bereken_voorraad("opnieuw_berekenen",eigenaar_formulier_week);

			$("#eigenaar_formulier").hide();
			$("#dim_content").hide();
		});

		// click "beschikbaar": check if no eigenaar_blokkering has been filled
		$("input[name^='beschikbaar[']").click(function(event) {

			var week = $(this).attr("name").replace(/\D/g,'');

			if ( $("input[name='boekingderden["+week+"]']").is(":checked")) {
				$("input[name='boekingderden["+week+"]']").parent("td").effect("highlight", {}, 1000);
				event.preventDefault();
				return false;
			} else if ( $("input[name='bezeteigenaar["+week+"]']").is(":checked")) {
				$("input[name='bezeteigenaar["+week+"]']").parent("td").effect("highlight", {}, 1000);
				event.preventDefault();
				return false;
			} else if ( $("input[name='nietbeschikbaarverhuur["+week+"]']").is(":checked")) {
				$("input[name='nietbeschikbaarverhuur["+week+"]']").parent("td").effect("highlight", {}, 1000);
				event.preventDefault();
				return false;
			} else {
				return true;
			}
		});
	}

	$(".copy_from_type button").click(function(event) {

		//
		// copy prices from other type (on the fly, via rpcjson)
		//

		event.preventDefault();

		$(".copy_from_type button").prop("disabled", true);
		$(".copy_from_type img").show();

		// strip out non-numerical characters
		var type_id = $(".copy_from_type input").val().replace(/\D/g,'');

		var last_used_field = '';

		if(type_id) {

			$.getJSON(
				'cms/wtjson.php?t=4&tid='+type_id+"&sid="+wt_getParameterByName("sid")+"&toonper="+$("input[name=toonper]").val()+"&bruto="+($("input[name=copy_from_type_bruto_acc]").attr("checked") ? 1 : 0)+"&toeslag="+($("input[name=copy_from_type_toeslag]").attr("checked") ? 1 : 0)+"&korting="+($("input[name=copy_from_type_korting]").attr("checked") ? 1 : 0),
				function(data) {
					if(data.prices==1) {
						$.each(data.week, function(key, value) {
							$.each(value, function(key2, value2) {
								// console.log(key+" "+key2+" "+value2);
								last_used_field="input[name='"+key2+"["+key+"]']";
								$(last_used_field).val(value2);
								$(last_used_field).parent().addClass("field_from_copy");
							});
							if($(last_used_field).length!==0) {
								bereken('kopieer',key);
							}
						});
					}
					$(".copy_from_type button").prop("disabled", false);
					$(".copy_from_type img").hide();
					$(".copy_from_type input").val("");
					if($("input[name=toonper]").val()==1) {
						optellen('bruto');
					} else {
						optellen('c_bruto');
					}
				}
			);
		} else {
			$(".copy_from_type button").prop("disabled", false);
			$(".copy_from_type img").hide();
			// $(".copy_from_type input").val("");
		}
	});
});
