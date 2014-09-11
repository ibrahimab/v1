

var inkoopgegevens_verschil_met_actueel = [];
var cms_via_verkeerde_site=0;


function confirmLink(rowid, theLink, msg) {
	OBJ1=document.getElementById(rowid);
	OBJ1.className='row_delete_confirm';
	var is_confirmed = confirm(msg);
	if (is_confirmed) {
		theLink.href += '&confirmed=1';
	} else {
		OBJ1.className='row1';
	}
	return is_confirmed;
}

function confirmSubmit(frm, msg) {
	var is_confirmed = confirm(msg);
	if (is_confirmed) {
		frm.submit();
	}
	return is_confirmed;
}

function confirmDelete(theLink, msg) {
	var is_confirmed = confirm(msg);
	if (is_confirmed) {
		theLink.href += '&confirmed=1';
	}
	return is_confirmed;
}

function confirmClick(theLink, msg) {
	// gebruik: onclick="return confirmClick(this,'Zeker weten?');"
	var is_confirmed = confirm(msg);
	if (is_confirmed) {
		theLink.href += '&confirmed=1';
	}
	return is_confirmed;
}

function ToggleClass(id,rowclass) {
	OBJ1=document.getElementById(id);
	if(OBJ1.className=='row_delete_confirm') {
	} else {
		if(OBJ1.className=='row_delete') {
			OBJ1.className=rowclass;
		} else {
			OBJ1.className='row_delete';
		}
	}
}

function popwindow(size,url,align) {
	var wWidth, wHeight, wLeft, wTop;
	wWidth = size*100;
	wHeight = wWidth*.75;
	if(align=='center') {
		wLeft = (screen.width-wWidth)/2;
		wTop = (screen.height-wHeight)/2;
		var Scherm = window.open(url, '_blank', 'scrollbars=yes,width='+wWidth+',height='+wHeight+',left='+wLeft+',top='+wTop);
	} else {
		var Scherm = window.open(url, '_blank', 'scrollbars=yes,width='+wWidth+',height='+wHeight);
	}
	Scherm.focus();
}

function popwindowXY(wWidth,wHeight,url,align) {
	var wLeft, wTop;
	if(align=='center') {
		wLeft = (screen.width-wWidth)/2;
		wTop = (screen.height-wHeight)/2;
		var Scherm = window.open(url, '_blank', 'scrollbars=yes,width='+wWidth+',height='+wHeight+',left='+wLeft+',top='+wTop);
	} else {
		var Scherm = window.open(url, '_blank', 'scrollbars=yes,width='+wWidth+',height='+wHeight);
	}
	Scherm.focus();
}

function disableform(state,id) {
	if(!state) {
		document.getElementById(id).style.visibility = 'hidden';
	} else {
		document.getElementById(id).style.visibility = 'visible';
	}
}

function checkUncheckAll(theElement) {
	var theForm = theElement.form, z = 0;
	for(z=0; z<theForm.length;z++) {
		if(theForm[z].type == 'checkbox' && theForm[z].name != 'checkall') {
			theForm[z].checked = theElement.checked;
		}
	}
}

function getStyleClass (className) {
  if (document.all) {
	for (var s = 0; s < document.styleSheets.length; s++)
	  for (var r = 0; r < document.styleSheets[s].rules.length; r++)
		if (document.styleSheets[s].rules[r].selectorText == '.' + className)
		  return document.styleSheets[s].rules[r];
  }
  else if (document.getElementById) {
	for (var s = 0; s < document.styleSheets.length; s++)
	  for (var r = 0; r < document.styleSheets[s].cssRules.length; r++)
		if (document.styleSheets[s].cssRules[r].selectorText == '.' + className)
		  return document.styleSheets[s].cssRules[r];
  }
  return null;
}

function toggle_display(classname,state) {
	if(state) {
		getStyleClass(classname).style.display = ''
	} else {
		getStyleClass(classname).style.display = 'none'
	}
}

function togglesubmenu(name,aantalitems) {
	var url=document.location.href;
	if(url.indexOf('?')>0) {
		url=url+'&';
	} else {
		url=url+'?';
	}
	if($('#'+name).css("display")=='block') {
		// close
		$('#'+name).slideUp('slow',function(){

		});
		$.getJSON(url+'cmslayout_json=1&cmslayout_closemenu='+name);
	} else {
		OBJ1=document.getElementById('menu');
		OBJ2=document.getElementById('meet_hoogte_menu');

		// open
		$('#'+name).slideDown('normal',function(){
			var hoogte1=OBJ1.offsetHeight;
			var hoogte2=OBJ2.offsetHeight+10;
			if(hoogte2>hoogte1) {
				OBJ1.style.height=hoogte2+'px';
				setHgt();
			}
		});

		$.getJSON(url+'cmslayout_json=1&cmslayout_openmenu='+name);
	}
}

$(document).ready(function() {

	//
	// jquery
	//

	$.ajaxSetup({ cache: false });

	// keep PHP-session alive (connect to wtjson.php every 5 minutes)
	var keep_session_alive = setInterval(function () {
		$.getJSON(
			'cms/wtjson.php?t=keep_session_alive',
			function(data) {

			}
		);
	}, 300000);


	$("#accCodeIh").focus(function() {
		$("#ih_country").prop("disabled", true);
		$("#ih_region").prop("disabled", true);
	});

	$("#accCodeIh").focusout( function() {
		if($("#accCodeIh").val() == ""){
			$("#ih_country").prop("disabled", false);
			$("#ih_region").prop("disabled", false);
		}
	});


	$('.submenuclass_up').slideUp('normal');
	$('#aflopend_optie_klant').slideUp('normal');
	$('#aflopend_optie_leverancier').slideUp('normal');

	$("form").submit(function() {
		if(!$(this).hasClass("no_submit_disable")) {
			// On submit disable its submit button
			$("input[type=submit]",this).attr("disabled","disabled");
		}
	});

	// show/hide toggle
	$(".showhidelink").click(function () {
		$(".showhide").slideToggle("slow",function() {
			if(showhidelink_text) {
				$(".showhidelink").html(showhidelink_text);
				showhidelink_text='';
			} else {
				showhidelink_text=$(".showhidelink").html();
				$(".showhidelink").html("&lt;&lt;");
			}
		});
		return false;
	});

	$(".openklappen").click(function () {
		$('#'+$(this).attr('rel')).slideToggle("fast",function() {
			setHgt2();
		});
		return false;
	});

	$(".openklappen_actie").click(function(event) {
		event.preventDefault();
		history.pushState(null, null, "#WT"+$(this).data("actieid"));
		return false;
	});

	// open/close WebTastic-actions https://www.chalet.nl/cms_diversen.php?t=1
	$(".open-wtactie").click(function (event) {
		event.preventDefault();
		var deze = $(this);

		$("html, body").animate({scrollTop: $("div[data-wtid="+deze.data("id")+"]").position().top }, '500', 'swing', function() {
			$("div[data-id="+deze.data("id")+"]").slideDown("slow",function() {
				setHgt2();
			});
		});
		return false;
	});

	// checbox_check_all
	$(".checbox_check_all").change(function(event) {
		var use_element = "";
		if($(this).data("only-class")) {
			use_element = "." + $(this).data("only-class");
		} else {
			use_element = "input[type=checkbox]";
		}

		if($(this).is(":checked")) {
			$(use_element).prop("checked", true);
		} else {
			$(use_element).prop("checked", false);
		}
	});

	if($("#cms_body_cms_diversen").length!==0 && window.location.hash.length!==0) {
		// open action
		if(/^#WT[0-9]+$/.test(window.location.hash)) {
			$("div[data-id="+$(window.location.hash).data("wtid")+"]").show();
			setHgt2();
		}
	}

	$('.vertalingafvinken').attr("title","wijziging afvinken");
	$(".vertalingafvinken").click(function () {
		reldata = $(this).attr('rel').split(',');
		document.getElementById('row2_'+reldata[0]).style.backgroundColor='#cccccc';
		$.getJSON(
			'cms/wtjson.php?t=2&cmslog_id='+reldata[1],
			function(data){
				if(data.afgevinkt==1) {
					$("#row1_"+reldata[0]).fadeOut("slow",function() {
					});
				} else {
					alert('Afvinken mislukt');
				}
			}
		);
		return false;
	});

	$(".vertalingbewerken").click(function () {
		$('#'+$(this).attr('rel')).slideDown("fast",function() {
			setHgt2();
		});
		popwindowXY(1030,700,$(this).attr('href'),true);
		return false;
	});

	// bij wijzigen 'Accommodatieprijs gewijzigd' vragen of 'Verzekerd bedrag Schade Logies Verblijven' ook gewijzigd moet worden
	$('input[name="input[verkoop_gewijzigd]"]').change(function() {
		if($('input[name="input[accprijs]"]').length==0) {

		} else {
			var is_confirmed = confirm("Accommodatieprijs is gewijzigd.\n\n'Verzekerd bedrag Schade Logies Verblijven' ook wijzigen in "+$('input[name="input[verkoop_gewijzigd]"]').val()+"?");
			if(is_confirmed) {
				$('input[name="input[accprijs]"]').val($('input[name="input[verkoop_gewijzigd]"]').val());
			}
		}
	});

	// bij invoeren 'Reserveringsnummer leverancier' of 'Factuurnummer leverancier' (bij boeking 'Inkoop- en leveranciersgegevens'): bestelstatus wijzigen naar 'bevestigd' en besteldatum invullen
	$(".leverancierscode_keydown").keydown(function() {

		// besteldatum invullen
		besteldatum_invullen();

		// bestelstatus wijzigen in 'bevestigd'
		if($("select[name='input[bestelstatus]']").val()!=3) {
			$("select[name='input[bestelstatus]']").val("3");
			var bestelstatus_td=$(".bestelstatus_td");
			bestelstatus_td.css("background-color","yellow");
			setTimeout(function() {
				bestelstatus_td.css("background-color","#ffffff");
				setTimeout(function() {
					bestelstatus_td.css("background-color","yellow");
				},300);
			},300);
		}
	});

	// when checking "Schriftelijke bevestiging volgt binnen enkele dagen": bestelstatus becomes "bevestigd"
	$("#yesnobestelstatus_schriftelijk_later").change(function() {
		if($(this).is(":checked")) {
			// bestelstatus wijzigen in 'bevestigd'
			if($("select[name='input[bestelstatus]']").val()!=3) {
				$("select[name='input[bestelstatus]']").val("3");
				$(".bestelstatus_td").effect("highlight", {}, 3000);
				besteldatum_invullen();
			}
		}
	});

	// garantie: bij invoeren 'Reserveringsnummer leverancier' of 'Factuurnummer leverancier' besteldatum invullen
	$(".garantie_leverancierscode_keydown").keydown(function() {
		if($("select[name='input[inkoopdatum][day]']").val()=="") {
			setdate('inkoopdatum','');
		}
	});


	// bij wijzigen bestelstatus (bij boeking 'Inkoop- en leveranciersgegevens'): automatisch besteldatum invullen
	$(".bestelstatus_besteldatum").change(function() {
		if($(this).val()==2 || $(this).val()==3) {
			besteldatum_invullen();
		}
	});

	// Inkoopgegevens accommodatie: alles doorrekenen
	$(".inkoopgegevens").change(function() {
		// zorgen dat het zojuist ingevulde veld een afgeronde float wordt
		var aangepast=$(this).val();
		aangepast=aangepast.replace(",",".");
		aangepast=parseFloat(aangepast).toFixed(2);
		if(isFloat(aangepast)) {
			aangepast=aangepast.replace(".",",");
			$(this).val(aangepast);
		} else {
			$(this).val("");
		}
		inkoopgegevens_berekenen(false);
	});

	// Garantie-inkoopgegevens accommodatie: alles doorrekenen
	$(".garantie_inkoopgegevens").change(function() {
		// zorgen dat het zojuist ingevulde veld een afgeronde float wordt
		var aangepast=$(this).val();
		aangepast=aangepast.replace(",",".");
		aangepast=parseFloat(aangepast).toFixed(2);
		if(isFloat(aangepast)) {
			aangepast=aangepast.replace(".",",");
			$(this).val(aangepast);
		} else {
			$(this).val("");
		}

		garantie_inkoopgegevens_berekenen();
	});

	// bij wijzigen "Factuur goedgekeurd door de klant": "Goedkeuring benodigd" uitzetten
	$('select[name="input[factuur_ondertekendatum][day]"], select[name="input[factuur_ondertekendatum][month]"], select[name="input[factuur_ondertekendatum][year]"]').change(function() {

		goedkeuringen_benodigd_uitzetten();
	});

	// submit-button: controle uitvoeren
	$(".inkoopgegevens_submit").click(function() {
		var melding=false;
		for (i in inkoopgegevens_verschil_met_actueel) {
			if(inkoopgegevens_verschil_met_actueel[i]==true) {
				melding=true;
			}
		};
		if(melding) {
			var is_confirmed = confirm('Er wijken bedragen af van de tarieventabel. Toch opslaan?');
			if (is_confirmed) {
				$(this).attr("disabled","disabled");
				document.frm.submit();
				return false;
			} else {
				return false;
			}
		} else {
			$(this).attr("disabled","disabled");
			document.frm.submit();
			return false;
		}
	});

	// bij inkoopgegevens: onload inkoopgegevens_berekenen
	if($(".inkoopgegevens_submit").length>0) {
		inkoopgegevens_berekenen(true);
	}

	// bij wijzigen vinkje "Inkoop van 0 is toegestaan": berekening uitvoeren
	$("input[name='input[inkoop_van_0_toegestaan]']").change(function() {
		inkoopgegevens_berekenen();
	});

	// bij garantiegegevens: onload inkoopgegevens_berekenen
	if($(".uitkomst_garantie_inkoopmincommissie").length>0) {
		garantie_inkoopgegevens_berekenen();
	}

	// bij 'Algemene gegevens' van een boeking in CMS: vragen of bestelstatus moet worden gewist bij het wijzigen van de accommodatie en/of aankomstdatum
	if($("input[name='input[bestelstatus_wissen]']").length>0) {
		$("select[name='input[typeid]'],select[name='input[aankomstdatum]']").change(function() {
			$("#bestelstatus_wissen_tr").css("display","");
			if($("input[name='input[bestelstatus_wissen]']").is(':checked')) {

			} else {
				var is_confirmed = confirm('Deze boeking is al besteld. Besteldatum wissen en bestelstatus op \'nog niet besteld\' zetten?');
				if(is_confirmed) {
					$("input[name='input[bestelstatus_wissen]']").attr("checked",true);
				}
			}
		});
	}

	// handmatige optie: verbergen voor de klant (verkoopprijs op 0,00 zetten)
	if($("input[name='input[verberg_voor_klant]']").length>0) {
		$("input[name='input[verberg_voor_klant]']").change(function() {
			if($("input[name='input[verberg_voor_klant]']").is(':checked')) {
				if($("input[name='input[verkoop]']").val()=="") {
					$("input[name='input[verkoop]']").val("0,00");
				}
				if($("input[name='input[soort]']").val()=="") {
					$("input[name='input[soort]']").val("Correctie inkoop optie");
				}
			}
		});
	}

	// overzicht uitgaande openstaande betalingen
	if($("input[name='inkoopbetalingen_filled']").length>0) {

		// alle submitbuttons disablen na formsubmit
		$("form").submit(function(){
			$("input[type=submit]").attr("disabled","disabled");
		});

		// nieuwe betalingen
		$(".nieuwebetaling_checkbox").change(function() {
			var lev_id=$(this.form).children("input[name='leverancier_id']").val();
			if($(".nieuwebetaling_checkbox_"+lev_id+":checked").length==0) {

				// goedkeur-vinkjes aanzetten
				$(".nieuwegoedkeuring_checkbox").removeAttr("disabled");
				$(".nieuwegoedkeuring_checkbox_alles").removeAttr("disabled");

				$("#betaalbutton_lev_"+lev_id).attr("disabled","disabled");
				$("#betaaltotaal_"+lev_id).html("");
			} else {
				// goedkeur-vinkjes uitzetten
				$(".nieuwegoedkeuring_checkbox").removeAttr("checked");
				$(".nieuwegoedkeuring_checkbox").attr("disabled","disabled");
				$(".nieuwegoedkeuring_checkbox_alles").removeAttr("checked");
				$(".nieuwegoedkeuring_checkbox_alles").attr("disabled","disabled");


				$("#betaalbutton_lev_"+lev_id).removeAttr("disabled");

				var totaal=0;
				$.each($(".nieuwebetaling_checkbox_"+lev_id+":checked"),function(key,value) {
					totaal=totaal+parseFloat($(value).val());
				});
				$("#betaaltotaal_"+lev_id).html(wt_number_format(totaal,2,",","."));
			}
			if($(this).is(":checked")) {
				$(this).closest("td").prev().css("background-color","yellow");
			} else {
				$(this).closest("td").prev().css("background-color","");
			}
		});

		// nieuwe goedkeuringen
		$(".nieuwegoedkeuring_checkbox").change(function() {
			var lev_id=$(this.form).children("input[name='leverancier_id']").val();
			if($(".nieuwegoedkeuring_checkbox_"+lev_id+":checked").length==0) {
				// betaal-vinkjes aanzetten
				$(".nieuwebetaling_checkbox").removeAttr("disabled");
				$(".nieuwebetaling_checkbox_alles").removeAttr("disabled");

				$("#goedkeurbutton_lev_"+lev_id).attr("disabled","disabled");
				$("#goedkeurtotaal_"+lev_id).html("");
			} else {
				// betaal-vinkjes uitzetten
				$(".nieuwebetaling_checkbox").removeAttr("checked");
				$(".nieuwebetaling_checkbox").attr("disabled","disabled");
				$(".nieuwebetaling_checkbox_alles").removeAttr("checked");
				$(".nieuwebetaling_checkbox_alles").attr("disabled","disabled");

				$("#goedkeurbutton_lev_"+lev_id).removeAttr("disabled");

				var totaal=0;
				$.each($(".nieuwegoedkeuring_checkbox_"+lev_id+":checked"),function(key,value) {
					totaal=totaal+parseFloat($(value).val());
				});
				$("#goedkeurtotaal_"+lev_id).html(wt_number_format(totaal,2,",","."));
			}
			if($(this).is(":checked")) {
				$(this).closest("td").prev().css("background-color","yellow");
			} else {
				$(this).closest("td").prev().css("background-color","");
			}
		});

		// met 1 klik alle betalingen van de betreffende leverancier aan/uitzetten
		$(".nieuwebetaling_checkbox_alles").change(function() {
			var lev_id=$(this.form).children("input[name='leverancier_id']").val();
			if($(this).is(":checked")) {
				$(".nieuwebetaling_checkbox_"+lev_id+":not(:checked)").click();
			} else {
				$(".nieuwebetaling_checkbox_"+lev_id+":checked").click();
			}
		});

		// met 1 klik alle goedkeuringen van de betreffende leverancier aan/uitzetten
		$(".nieuwegoedkeuring_checkbox_alles").change(function() {
			var lev_id=$(this.form).children("input[name='leverancier_id']").val();
			if($(this).is(":checked")) {
				$(".nieuwegoedkeuring_checkbox_"+lev_id+":not(:checked)").click();
			} else {
				$(".nieuwegoedkeuring_checkbox_"+lev_id+":checked").click();
			}
		});
	}

	if($(".financiele_table").length>0) {
		// breedte van de pagina aanpassen
//		var width=$("#financiele_table_id").width()+300;
//		$("#content").css("width",width);
//		$("#wrapper").css("width",width+200);
	}

	if($("#fixedtable_header_div").length>0) {
		// kop van tabel financieel overzicht overzetten naar andere tr (fixed header)
		var tdteller=0;
		$(".fixedtable_header_copy tr:first").html($("#fixedtable_header").html());
		$("#fixedtable_header").children().each(function() {
			tdteller=tdteller+1;
			$(".fixedtable_header_copy th:nth-child("+tdteller+")").width($(this).width());
		});
		$("#fixedtable_header_div").css("display","block");
	}

	// jQuery UI Datepicker / Calendar jQueryUI
	if($().datepicker) {

		$.datepicker.regional['nl'] = {
			closeText: 'Sluiten',
			prevText: 'vorige',
			nextText: 'volgende',
			currentText: 'Vandaag',
			monthNames: ['januari', 'februari', 'maart', 'april', 'mei', 'juni',
			'juli', 'augustus', 'september', 'oktober', 'november', 'december'],
			monthNamesShort: ['jan', 'feb', 'maa', 'apr', 'mei', 'jun',
			'jul', 'aug', 'sep', 'okt', 'nov', 'dec'],
			dayNames: ['zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag'],
			dayNamesShort: ['zon', 'maa', 'din', 'woe', 'don', 'vri', 'zat'],
			dayNamesMin: ['zo', 'ma', 'di', 'wo', 'do', 'vr', 'za'],
			weekHeader: 'Wk',
			dateFormat: 'dd-mm-yy',
			firstDay: 1,
			isRTL: false,
			showMonthAfterYear: false,
			yearSuffix: ''};
		$.datepicker.setDefaults($.datepicker.regional['nl']);
		$(".jqueryui_datepicker").datepicker();
	}

	// CMS via verkeerde site (niet chalet.nl) melden
	if(cms_via_verkeerde_site==1) {
		alert("Je gebruikt het CMS op dit moment via "+window.location.hostname+".\n\nGebruik het CMS altijd via www.chalet.nl/cms.php");
	}


	// alle tekst in een textarea selecteren na focus
	$('textarea.autoselect_on_focus').focus(function() {
		var $this = $(this);

		$this.select();

		window.setTimeout(function() {
			$this.select();
		}, 1);

		// Work around WebKit's little problem
		$this.mouseup(function() {
			// Prevent further mouseup intervention
			$this.unbind("mouseup");
			return false;
		});
	});


	// toon overzicht grootboekrekeningen op https://www.chalet.nl/cms_financien.php
	$("#toon_overzicht_grootboekrekeningen").click(function() {
		$("#overzicht_grootboekrekeningen").slideToggle();
		return false;
	});

	if($("input[name^='input[vertrekinfo_goedgekeurd_seizoen]']").length!==0 || $("input[name^='input[vertrekinfo_goedgekeurd_seizoen_de]']").length!==0 || $("input[name^='input[vertrekinfo_goedgekeurd_seizoen_en]']").length!==0 || $("input[name^='input[vertrekinfo_goedgekeurd_seizoen_fr]']").length!==0) {

		//
		// vertrekinfo accommodatieniveau
		//

		// alle te tracken velden vooraf onthouden
		var vertrekinfo_te_tracken = ["inclusief", "exclusief" ,"vertrekinfo_incheck_sjabloon_id", "vertrekinfo_soortbeheer", "vertrekinfo_soortbeheer_aanvulling", "vertrekinfo_telefoonnummer", "vertrekinfo_inchecktijd", "vertrekinfo_uiterlijkeinchecktijd", "vertrekinfo_uitchecktijd", "vertrekinfo_inclusief", "vertrekinfo_exclusief", "vertrekinfo_route", "vertrekinfo_soortadres", "vertrekinfo_adres", "vertrekinfo_plaatsnaam_beheer", "vertrekinfo_gps_lat", "vertrekinfo_gps_long", "vertrekinfo_skipas", "vertrekinfo_landroute", "vertrekinfo_plaatsroute", "vertrekinfo_optiegroep"];
		var vertrekinfo_te_tracken_prevalue = [];

		var vertrekinfo_te_tracken_talen = ["", "_de" ,"_en", "_fr"];

		$.each(vertrekinfo_te_tracken_talen, function(key2, value2) {

			$("input[name^='input[vertrekinfo_goedgekeurd_seizoen"+value2+"]']").click(function() {
				if($(this).is(":checked")) {
					// goedgekeurd: vertrekinfo_goedgekeurd_datetime vullen met huidige datum/tijd
					var currentTime = new Date();
					$("[name='input[vertrekinfo_goedgekeurd_datetime"+value2+"]']").val(currentTime.getFullYear()+"-"+("0"+(currentTime.getMonth()+1)).slice(-2)+"-"+("0"+currentTime.getDate()).slice(-2)+" "+('0'+currentTime.getHours()).slice(-2)+":"+('0'+currentTime.getMinutes()).slice(-2)+":"+('0'+currentTime.getSeconds()).slice(-2));
				}
			});

			$.each(vertrekinfo_te_tracken, function(key, value) {

				value=value+value2;

				if($("[name='input["+value+"]']").length!==0) {

					// prevalue opslaan
					vertrekinfo_te_tracken_prevalue[value] = $("[name='input["+value+"]']").val();

					// bij wijzigen gegevens: goedgekeurd-vinkjes uitzetten
					$("[name='input["+value+"]']").change(function() {
						if(vertrekinfo_te_tracken_prevalue[value] != $("[name='input["+value+"]']").val()) {
							if((value=="inclusief"||value=="exclusief") && $("[name='input[vertrekinfo_"+value+"]']").val().length>0) {
								// overschrijfveld is gevuld: dan origineel veld niet tracken
							} else {
								$("input[name^='input[vertrekinfo_goedgekeurd_seizoen"+value2+"]']").attr("checked",false);
							}
						}
					});
				}
			});
		});

		// inclusief-tekst overnemen van ander ingevuld veld
		if($("#vertrekinfo_inclusief_website").length>0) {
			// input[vertrekinfo_inclusief] bij openen pagina vullen met input[inclusief]
			$("#vertrekinfo_inclusief_website").html($("textarea[name='input[inclusief]']").val().replace(/\n/g,"<br />"));

			// input[vertrekinfo_inclusief] bij wijzigen input[inclusief]
			$("textarea[name='input[inclusief]']").keyup(function() {
				$("#vertrekinfo_inclusief_website").html($("textarea[name='input[inclusief]']").val().replace(/\n/g,"<br />"));
			});
		}

		// exclusief-tekst overnemen van ander ingevuld veld
		if($("#vertrekinfo_exclusief_website").length>0) {
			// input[vertrekinfo_exclusief] bij openen pagina vullen met input[exclusief]
			$("#vertrekinfo_exclusief_website").html($("textarea[name='input[exclusief]']").val().replace(/\n/g,"<br />"));

			// input[vertrekinfo_exclusief] bij wijzigen input[exclusief]
			$("textarea[name='input[exclusief]']").keyup(function() {
				$("#vertrekinfo_exclusief_website").html($("textarea[name='input[exclusief]']").val().replace(/\n/g,"<br />"));
			});
		}
	}


	// datum toevoegen aan roominglist-goedkeur-veld
	if($("#cms_body_cms_roomingaankomst").length>0) {


		$("input[name='input[roominglist_goedgekeurd]']").focus(function() {
			if($(this).val()=="") {
				var currentTime = new Date();
				var month = ('0' + (currentTime.getMonth()+1)).slice(-2);
				var day = ('0' + currentTime.getDate()).slice(-2);
				var year = currentTime.getFullYear();
				$(this).val(day + "-" + month + "-" + year+ ": ");
			}
		});

		var roominglist_goedgekeurd_previous='';

		$("input[name='input[versturen]']").change(function() {
			if($(this).is(":checked")) {

				$("#submit1frm").val("VERZENDEN");

				$(".roomingaankomst_verzenden").css("display","table-row");

				// goedgekeurd wissen bij verzenden
				// herinner-datum op 4 weken van nu zetten
				var currentTime = new Date(+new Date + (28 * 24 * 60 * 60 * 1000));
				var month = currentTime.getMonth() + 1;
				var day = currentTime.getDate();
				var year = currentTime.getFullYear();

				$("select[name='input[roominglist_volgende_controle][day]']").val(day);
				$("select[name='input[roominglist_volgende_controle][month]']").val(month);
				$("select[name='input[roominglist_volgende_controle][year]']").val(year);

				$("select[name='input[roominglist_volgende_controle][day]']").parent().css("background-color","yellow");

				if($("input[name='input[roominglist_goedgekeurd]']").val()!="") {
					roominglist_goedgekeurd_previous = $("input[name='input[roominglist_goedgekeurd]']").val();
					$("input[name='input[roominglist_goedgekeurd]']").val("");
				}

				var timeout1=setTimeout(function() {
					$("select[name='input[roominglist_volgende_controle][day]']").parent().css("background-color","#ffffff");
				},800);
			} else {
				if($("input[name='input[roominglist_goedgekeurd]']").val()=="" && roominglist_goedgekeurd_previous) {
					$("input[name='input[roominglist_goedgekeurd]']").val(roominglist_goedgekeurd_previous);
				}

				$(".roomingaankomst_verzenden").css("display","none");
				$("#submit1frm").val("OPSLAAN");
			}
			setHgt2();
		});

		if($("input[name='input[versturen]']").is(":checked")) {
			$(".roomingaankomst_verzenden").css("display","table-row");
			setHgt2();
		}

		// roominglist: vinkjes "Naamswijzigingen doorgeven" en "Op te nemen garanties" aan elkaar koppelen
		$("input[name^='input[roominglist_naamswijzigingen_doorgeven]']").change(function(event) {

			if($(this).next("label").find("span").attr("class")=="soort_garantie_1") {

				var boeking_of_garantie_id='';

				if($(this).attr("name").indexOf("[g")>0) {
					// onverkochte garantie
					boeking_of_garantie_id=$(this).attr("name").replace(/\D/g,'');
				} else {
					// boeking
					boeking_of_garantie_id="b"+$(this).attr("name").replace(/\D/g,'');
				}

				if($(this).is(":checked")) {
					$("input[name='input[roominglist_garanties_doorgeven]["+boeking_of_garantie_id+"]']").prop("checked", true);
				} else {
					$("input[name='input[roominglist_garanties_doorgeven]["+boeking_of_garantie_id+"]']").prop("checked", false);
				}

			}
		});
		$("input[name^='input[roominglist_garanties_doorgeven]']").change(function(event) {

			if($(this).next("label").find("span").attr("class")=="soort_garantie_1") {

				var boeking_of_garantie_id='';

				if($(this).attr("name").indexOf("[b")>0) {
					// boeking
					boeking_of_garantie_id=$(this).attr("name").replace(/\D/g,'');
				} else {
					// onverkochte garantie
					boeking_of_garantie_id="g"+$(this).attr("name").replace(/\D/g,'');
				}

				// var boeking_id = $(this).attr("name").replace(/\D/g,'');
				if($(this).is(":checked")) {
					$("input[name='input[roominglist_naamswijzigingen_doorgeven]["+boeking_of_garantie_id+"]']").prop("checked", true);
				} else {
					$("input[name='input[roominglist_naamswijzigingen_doorgeven]["+boeking_of_garantie_id+"]']").prop("checked", false);
				}
			}
		});

		// bij openen pagina: alle al aangevinkte vinkjes "Op te nemen garanties" ook aanzetten bij "Naamswijzigingen doorgeven"
		$("input[name^='input[roominglist_garanties_doorgeven]']").each(function() {

			if($(this).next("label").find("span").attr("class")=="soort_garantie_1") {
				var boeking_id = $(this).attr("name").replace(/\D/g,'');
				if($(this).is(":checked")) {
					$("input[name='input[roominglist_naamswijzigingen_doorgeven]["+boeking_id+"]']").prop("checked", true);
				} else {
					$("input[name='input[roominglist_naamswijzigingen_doorgeven]["+boeking_id+"]']").prop("checked", false);
				}
			}
		});

		// bij openen pagina: alle gewone boekingen (geen garanties) met naamswijziging waarbij aan_leverancier_doorgegeven_naam nog leeg is: standaard aanvinken
		$("input[name^='input[roominglist_naamswijzigingen_doorgeven]']").each(function() {
			if($(this).next("label").find("span").length==0 && $(this).next("label").text().indexOf(': "" is nu')>0) {
				$(this).prop("checked", true);
			}
		});
	}

	// roominglist-form openen in een popup
	$("#roominglist_bekijken").click(function(){

		window.open('', 'formpopup', 'width=1000,height=600,resizeable,scrollbars');

		$("input[name=roominglist_bekijken]").val("1");

		$("form[name=frm]").prop("target", 'formpopup');
		$("form[name=frm]").submit();

		$("form[name=frm]").prop("target", '');
		$("input[name=roominglist_bekijken]").val("0");

		return false;
	});


	if($("body#cms_body_cms_boekingen").length!==0) {
		// Venturasol-Vacances boekingen in boekingen-CMS andere kleur geven
		$("table.tbl tr").each(function() {
			var site_letter = $(this).find("td:nth-child(3)").text();
			if(site_letter.indexOf("Y")>-1) {
				$(this).addClass("tr_venturasol_boeking");
			}
		});
	}


	// bij overzicht aanvragen (https://www.chalet.nl/cms_boekingen.php?bt=1&archief=0) hele tr opvallend kleuren als er nog geen bestelstatus bekend is
	$(".bestelstatus_hele_tr_opvallend").parent().parent().addClass("tr_bestelstatus_hele_tr_opvallend");


	// bij annuleren boeking: vinkje "deze boeking is voor de klant zichtbaar in "Mijn boeking"" uitzetten
	$("input[name='input[geannuleerd]']").change(function() {
		if($(this).is(":checked") && $("input[name='input[tonen_in_mijn_boeking]']").length!==0 && $("input[name='input[tonen_in_mijn_boeking]']").is(":checked")) {
			$("input[name='input[tonen_in_mijn_boeking]']").prop("checked", false);
			$("label[for='yesnotonen_in_mijn_boeking']").effect("highlight", {color: "red"}, 3000);
		}
	});


	// info-balloon clickable
	$("a.opm img").click(function(event) {
		var deze = $(this).parent().find("span");
		if(deze.hasClass("balloon_small")) {
			event.preventDefault();
			if(deze.is(":visible")) {
				$(this).parent().find("span").css("display", "none");
			} else {
				// hide others
				$("a.opm span").parent().find("span").css("display", "none");

				// show current
				$(this).parent().find("span").css("display", "block");
			}
		}
	});


	// vervallen aanvraag
	$("input[name='input[vervallen_aanvraag]']").change(function() {
		if($(this).is(":checked")) {
			if($("input[name='input[tonen_in_mijn_boeking]']").is(":checked")) {
				$("input[name='input[tonen_in_mijn_boeking]']").prop("checked", false);
				$("label[for=yesnotonen_in_mijn_boeking]").effect("highlight", {}, 3000);
			}
		}
	});


	//
	// copy filled field to other field (if empty)
	//
	$("input.copy_field").change(function(event) {
		if($(this).data("copy_field_to")) {
			var copy_field_to = $("input[name='input["+$(this).data("copy_field_to")+"]']");
			if(copy_field_to.length!==0 && copy_field_to.val()=="") {
				copy_field_to.val($(this).val());
			}
		}
	});


	//
	// Bijkomende kosten-cms
	//


	// track changes
	$(".cms_bk_row select, .cms_bk_row input[type=text]").change(function(event) {

		var form = $(this).closest("form");
		var seizoen_id = form.find("input[name='seizoen_id']").val();

		var row = $(this).closest(".cms_bk_row");
		var bk_soort_id = row.data("soort_id");

		row.addClass("cms_bk_save_row");

		$(".cms_bk_seizoen[data-seizoen_id="+seizoen_id+"] .cms_bk_row_afwijkend_type[data-soort_id="+bk_soort_id+"]").addClass("cms_bk_row_overwrite");

		if($(".cms_bk_seizoen[data-seizoen_id="+seizoen_id+"] .cms_bk_row_afwijkend_type.cms_bk_row_overwrite").length!==0) {
			$(".cms_bk_type_afwijkingen_overschrijven").css("visibility", "visible");
			form.find("input[type=submit]").prop("disabled" , true);
		}
	});

	$("select[name=bk_new]").change(function(event) {

		var bk_soort_id = $(this).val();
		var form = $(this).closest("form");
		var seizoen_id = form.find("input[name='seizoen_id']").val();
		var cms_bk_seizoen = $("div.cms_bk_seizoen[data-seizoen_id='" + seizoen_id + "']");

		form.find("select[name=bk_new]").val("");

		$.getJSON(
			"cms/wtjson.php?t=bk_new&bk_soort_id="+bk_soort_id+"&soort="+form.find("input[name='soort']").val()+"&id="+form.find("input[name='id']").val(),
			function(data) {
				if(data.ok) {
					cms_bk_seizoen.find(".cms_bk_all_rows").append(data.html);
					bk_keuzes_actief_inactief();
					setHgt2();
				}
			}
		);
	});


	$("#type_afwijkingen_overschrijven").change(function(event) {

		var form = $(this).closest("form");

		if($(this).is(':checked')) {
			form.find("input[type=submit]").prop("disabled", false);
		} else {
			form.find("input[type=submit]").prop("disabled", true);
		}
	});

	// bk: onload: bk_keuzes_actief_inactief
	bk_keuzes_actief_inactief();

	// bk: inclusief => hide other select-fields
	$(document).on("change", ".cms_bk_seizoen select[name^=inclusief], .cms_bk_seizoen select[name^=borg_soort], .cms_bk_seizoen select[name^=verplicht]", function(event) {
		bk_keuzes_actief_inactief();
	});

	// bk: delete row
	$(document).on("click", ".cms_bk_row .delete", function(event) {
		var soort_id = $(this).closest(".cms_bk_row").data("soort_id");
		// alert(soort_id);
		// remove();
		$(".cms_bk_row[data-soort_id="+soort_id+"]").remove();

		bk_keuzes_actief_inactief();
	});

	// bk: change field
	$(document).on("change", ".cms_bk_row select, .cms_bk_row input[type=text]", function(event) {
		bk_row_yellow_or_not($(this).closest(".cms_bk_row"));
	});

	$(".cms_bk_seizoen form").submit(function(event) {
		event.preventDefault();

		var form = $(this);
		var seizoen_id = form.find("input[name='seizoen_id']").val();
		var cms_bk_row;
		var not_inquery = "0";

		// disable button
		$("input[type=submit]").prop("disabled", true);

		// show ajaxloader
		$(".cms_bk_seizoen[data-seizoen_id="+seizoen_id+"] .ajaxloader").css("visibility", "visible");

		// determine which rows should not be saved
		$(".cms_bk_seizoen[data-seizoen_id="+seizoen_id+"] .cms_bk_row[data-soort_id]:not(.cms_bk_row_afwijkend_type):not(.cms_bk_save_row)").each(function() {
			not_inquery = not_inquery + "%2C"+$(this).data("soort_id");
		});

		$.post( "cms/wtjson.php?t=bk_opmerkingen_intern"
		       +"&soort="+form.find("input[name='soort']").val()
		       +"&id="+form.find("input[name='id']").val()
		       ,
				{ "bk_opmerkingen_intern": $(".cms_bk_opmerkingen_intern textarea").val() }
		);

		$.getJSON(
			"cms/wtjson.php?t=bk_save&start=1"
			+"&soort="+form.find("input[name='soort']").val()
			+"&id="+form.find("input[name='id']").val()
			+"&seizoen_id="+form.find("input[name='seizoen_id']").val()
			+"&not_inquery="+not_inquery
			,
			function(data) {
				if(data.saved) {

					var soort_id=0;

					$(".cms_bk_seizoen[data-seizoen_id="+seizoen_id+"] .cms_bk_row[data-soort_id]:not(.cms_bk_row_afwijkend_type)").each(function() {

						cms_bk_row = $(this);

						if (cms_bk_row.hasClass("cms_bk_save_row")) {
							soort_id = cms_bk_row.data("soort_id");
						} else {
							soort_id = 0;
						}

						$.getJSON(
							"cms/wtjson.php?t=bk_save&bk_soort_id="+soort_id
							+"&soort="+form.find("input[name='soort']").val()
							+"&id="+form.find("input[name='id']").val()
							+"&seizoen_id="+form.find("input[name='seizoen_id']").val()
							+"&inclusief="+cms_bk_row.find("select[name^='inclusief']").val()
							+"&verplicht="+cms_bk_row.find("select[name^='verplicht']").val()
							+"&ter_plaatse="+cms_bk_row.find("select[name^='ter_plaatse']").val()
							+"&eenheid="+cms_bk_row.find("select[name^='eenheid']").val()
							+"&borg_soort="+cms_bk_row.find("select[name^='borg_soort']").val()
							+"&bedrag="+cms_bk_row.find("input[name^='bedrag']").val()
							,
							function(data) {
								if(data.saved) {

								}
							}
						);
					}).promise().done(function(){

						$.getJSON(
							"cms/wtjson.php?t=bk_save&stop=1"
							+"&soort="+form.find("input[name='soort']").val()
							+"&id="+form.find("input[name='id']").val()
							+"&seizoen_id="+form.find("input[name='seizoen_id']").val()
							+"&tmp_teksten_omgezet="+(form.find("input[name='tmp_teksten_omgezet']").is(":checked") ? "1" : "0")
							,
							function(data) {
								if(data.saved) {
									$(".cms_bk_seizoen[data-seizoen_id="+seizoen_id+"] .cms_bk_row_afwijkend_type.cms_bk_row_overwrite").remove();
									wt_popupmsg("De bijkomende kosten zijn opgeslagen.");
									$(".cms_bk_seizoen[data-seizoen_id="+seizoen_id+"] .cms_bk_type_afwijkingen_overschrijven").css("visibility", "hidden");

									// enable button
									$("input[type=submit]").prop("disabled", false);
								}
								$(".cms_bk_seizoen[data-seizoen_id="+seizoen_id+"] .ajaxloader").css("visibility", "hidden");
							}
						);
					});
				}
			}
		);
	});


	$(".cms_bk_kopieer button").click(function(event) {

		//
		// copy bk from other type (on the fly, via rpcjson)
		//

		event.preventDefault();

		var cms_bk_kopieer = $(this).closest(".cms_bk_kopieer");

		// cms_bk_kopieer

		cms_bk_kopieer.find("button").prop("disabled", true);
		cms_bk_kopieer.find("img").show();

		// strip out non-numerical characters
		var type_id = cms_bk_kopieer.find("input").val().replace(/\D/g,'');

		var seizoen_id = cms_bk_kopieer.closest(".cms_bk_seizoen").data("seizoen_id");

		var last_used_field = '';

		var cms_bk_all_rows = $(".cms_bk_seizoen[data-seizoen_id="+seizoen_id+"] .cms_bk_all_rows")
		var cms_bk_all_rows_wrapper = $(".cms_bk_seizoen[data-seizoen_id="+seizoen_id+"] .cms_bk_all_rows_wrapper")

		if(type_id) {

			$.getJSON(
				'cms/wtjson.php?t=bk_copy&type_id='+type_id+"&sid="+seizoen_id,
				function(data) {
					if(data.cms_bk_all_rows) {
						cms_bk_all_rows_wrapper.slideUp("normal", function(){
							cms_bk_all_rows.replaceWith(data.cms_bk_all_rows);
							bk_keuzes_actief_inactief();
							cms_bk_all_rows_wrapper.slideDown("normal", function(){
								setHgt2();
							});
						});
					}
					cms_bk_kopieer.find("input").val("");
					cms_bk_kopieer.find("button").prop("disabled", false);
					cms_bk_kopieer.find("img").hide();

				}
			);
		} else {
			cms_bk_kopieer.find("button").prop("disabled", false);
			cms_bk_kopieer.find("img").hide();
		}
	});

});

function bk_keuzes_actief_inactief() {

	$("select[name=bk_new] > option").prop("disabled", false);

	var cms_bk_seizoen;

	$(".cms_bk_row[data-soort_id]:not(.cms_bk_row_afwijkend_type)").each(function() {
		$(this).closest(".cms_bk_seizoen").find("select[name=bk_new] > option[value="+$(this).data("soort_id")+"]").prop("disabled", true);

		if($(this).find("select[name^=inclusief]").val()==1) {
			// inclusief
			$(this).find("select[name^=verplicht]").prop("disabled", true).css("visibility", "hidden");
			$(this).find("select[name^=ter_plaatse]").prop("disabled", true).css("visibility", "hidden")
			$(this).find("select[name^=eenheid]").prop("disabled", true).css("visibility", "hidden")
			$(this).find("input[name^=bedrag]").prop("disabled", true).css("visibility", "hidden")
		} else {
			// exclusief
			$(this).find("select[name^=verplicht]").prop("disabled", false).css("visibility", "visible");
			$(this).find("select[name^=ter_plaatse]").prop("disabled", false).css("visibility", "visible")
			$(this).find("select[name^=eenheid]").prop("disabled", false).css("visibility", "visible")
			$(this).find("input[name^=bedrag]").prop("disabled", false).css("visibility", "visible")

			if($(this).find("select[name^=borg_soort]").length!==0) {
				// borg: niet van toepassing of onbekend
				if($(this).find("select[name^=borg_soort]").val()==4 || $(this).find("select[name^=borg_soort]").val()==5) {
					$(this).find("select[name^=eenheid]").prop("disabled", true).css("visibility", "hidden")
					$(this).find("input[name^=bedrag]").prop("disabled", true).css("visibility", "hidden")
				} else {
					$(this).find("select[name^=eenheid]").prop("disabled", false).css("visibility", "visible")
					$(this).find("input[name^=bedrag]").prop("disabled", false).css("visibility", "visible")
				}
			} else {
				// zelf te verzorgen
				if($(this).find("select[name^=verplicht]").val()==3) {
					$(this).find("select[name^=ter_plaatse]").prop("disabled", true).css("visibility", "hidden")
					$(this).find("select[name^=eenheid]").prop("disabled", true).css("visibility", "hidden")
					$(this).find("input[name^=bedrag]").prop("disabled", true).css("visibility", "hidden")
				} else {
					$(this).find("select[name^=ter_plaatse]").prop("disabled", false).css("visibility", "visible")
					$(this).find("select[name^=eenheid]").prop("disabled", false).css("visibility", "visible")
					$(this).find("input[name^=bedrag]").prop("disabled", false).css("visibility", "visible")
				}
			}
		}

		bk_row_yellow_or_not($(this));
	});
}

function bk_row_yellow_or_not(deze) {
	// check if .cms_bk_row must be yellow (through .cms_bk_to_be_filled)
	var yellow = false;
	$(deze).find("select, input[type=text]").each(function() {
		if($(this).prop("disabled")==false && $(this).val()=="") {
			yellow = true;
		}
	});
	if(yellow) {
		deze.addClass("cms_bk_to_be_filled");
	} else {
		deze.removeClass("cms_bk_to_be_filled");
	}
}

function goedkeuringen_benodigd_uitzetten() {
	// checkbox "Goedkeuring benodigd: vraag om goedkeuring/ondertekening door de klant" uitzetten
	if($("#yesnoondertekenen").is(":checked")) {
		$("#yesnoondertekenen").removeAttr("checked");

		var org=$("label[for='yesnoondertekenen']").html();
		$("label[for='yesnoondertekenen']").html(org+"&nbsp;&nbsp;<span class='opvalmelding'>Vinkje is uitgezet!</span>");
		setTimeout(function(){
			$("label[for='yesnoondertekenen']").html(org);
		},3000);
	}
}


function get_float_input(fieldname) {
	if(document.forms['frm'].elements['input['+fieldname+']'].value.length>0) {
		var returnvalue=parseFloat(document.forms['frm'].elements['input['+fieldname+']'].value.replace(",","."));
	} else {
		var returnvalue=0;
	}
	return returnvalue;
}

function put_float_input(fieldname,nieuwgetal) {
	nieuwgetal=parseFloat(nieuwgetal).toFixed(2).replace(".",",");
	if($("."+fieldname).html().length>0) {
		var veld_al_gevuld=true;
	} else {
		var veld_al_gevuld=false;
	}
	if($("."+fieldname).html()!=nieuwgetal) {
		$("."+fieldname).html(nieuwgetal);
		if(veld_al_gevuld) {
			var td=$("."+fieldname);
			td.css("font-weight","bold");
			td.css("color","blue");
			setTimeout(function() {
				td.css("font-weight","");
				td.css("color","");
			},400);
		}
	}
}

function inkoopgegevens_verschil(fieldname,hoger_is_beter) {
	if($("#"+fieldname+"_actueel_getal").length>0) {
		actueel_value=$("#"+fieldname+"_actueel_getal").html();
		if(actueel_value.length==0) {
			actueel_value='0,00';
		}
		actueel_value=parseFloat(actueel_value.replace(",","."));

		field_value=document.forms['frm'].elements['input['+fieldname+']'].value;
		if(field_value.length==0) {
			field_value='0,00';
		}
		field_value=parseFloat(field_value.replace(",","."));

		if(field_value==actueel_value) {
			inkoopgegevens_verschil_met_actueel[fieldname]=false;
			$("#"+fieldname+"_actueel").removeClass("inkoopgegevens_actueel_neutraal inkoopgegevens_actueel_positief inkoopgegevens_actueel_negatief").addClass("inkoopgegevens_actueel_neutraal");
		} else {
			inkoopgegevens_verschil_met_actueel[fieldname]=true;
			if(actueel_value>field_value) {
				if(hoger_is_beter) {
					$("#"+fieldname+"_actueel").removeClass("inkoopgegevens_actueel_neutraal inkoopgegevens_actueel_positief inkoopgegevens_actueel_negatief").addClass("inkoopgegevens_actueel_positief");
				} else {
					$("#"+fieldname+"_actueel").removeClass("inkoopgegevens_actueel_neutraal inkoopgegevens_actueel_positief inkoopgegevens_actueel_negatief").addClass("inkoopgegevens_actueel_negatief");
				}
			} else if(actueel_value<field_value) {
				if(hoger_is_beter) {
					$("#"+fieldname+"_actueel").removeClass("inkoopgegevens_actueel_neutraal inkoopgegevens_actueel_positief inkoopgegevens_actueel_negatief").addClass("inkoopgegevens_actueel_negatief");
				} else {
					$("#"+fieldname+"_actueel").removeClass("inkoopgegevens_actueel_neutraal inkoopgegevens_actueel_positief inkoopgegevens_actueel_negatief").addClass("inkoopgegevens_actueel_positief");
				}
			}
		}
	}
}

function bedrag_min_korting(bedrag,kortingspercentage) {
	bedrag=bedrag*(100-kortingspercentage);
	bedrag=bedrag/100;

	// afrondingsproblemen in javascript met toFixed(2), daarom:
	// bedrag=Math.round(bedrag * 100) / 100;

	bedrag=parseFloat(bedrag.toFixed(2));
	return bedrag;
}

function inkoopgegevens_berekenen(onload) {
	//
	// berekeningen uitvoeren inkoopgegevens
	//

	// form-values opvragen
	var inkoopbruto=get_float_input('inkoopbruto');
	var inkoopcommissie=get_float_input('inkoopcommissie');
//	var inkooptoeslag=get_float_input('inkooptoeslag');
	var inkoopkorting=get_float_input('inkoopkorting');
	var inkoopkorting_percentage=get_float_input('inkoopkorting_percentage');
	var inkoopkorting_euro=get_float_input('inkoopkorting_euro');
	var extraopties_totaal=get_float_input('extraopties_totaal');
	var totaal_volgens_ontvangen_factuur=get_float_input('totaal_volgens_ontvangen_factuur');
	// betalingsverschil is op verzoek van Bert op "niet tonen" gezet (zodat niemand daar iets kan invullen) - 30-08-2013
	// var betalingsverschil=get_float_input('betalingsverschil');
	var betalingsverschil=0;
	var betalingssaldo=0;



	// inkoopmincommissie berekenen
	inkoopmincommissie=bedrag_min_korting(inkoopbruto,inkoopcommissie);

	// inkoopnetto berekenen
	var inkoopnetto=inkoopmincommissie;
//	inkoopnetto=inkoopnetto+inkooptoeslag;
	inkoopnetto=inkoopnetto-inkoopkorting;
	inkoopnetto=bedrag_min_korting(inkoopnetto,inkoopkorting_percentage);
	inkoopnetto=inkoopnetto-inkoopkorting_euro;
	inkoopnetto=parseFloat(inkoopnetto.toFixed(2));

	// totaalfactuurbedrag berekenen
	var totaalfactuurbedrag=inkoopnetto;
	totaalfactuurbedrag=totaalfactuurbedrag+extraopties_totaal;

	// uitkomsten in velden plaatsen
	put_float_input("uitkomst_inkoopmincommissie",inkoopmincommissie);
	put_float_input("uitkomst_inkoopnetto",inkoopnetto);
	document.forms['frm'].elements['input[inkoopnetto]'].value=inkoopnetto;
	put_float_input("uitkomst_totaalfactuurbedrag",totaalfactuurbedrag);
	totaalfactuurbedrag=parseFloat(totaalfactuurbedrag.toFixed(2));
	document.forms['frm'].elements['input[totaalfactuurbedrag]'].value=totaalfactuurbedrag;
//	alert(totaalfactuurbedrag);

	// kijken of er een verschil is met de tarieventabel
	//inkoopgegevens_verschil
	inkoopgegevens_verschil('inkoopbruto',false);
	inkoopgegevens_verschil('inkoopcommissie',true);
//	inkoopgegevens_verschil('inkooptoeslag',false);
	inkoopgegevens_verschil('inkoopkorting',true);
	inkoopgegevens_verschil('inkoopkorting_percentage',true);
	inkoopgegevens_verschil('inkoopkorting_euro',true);


	// betalingssaldo bepalen
	betalingssaldo=totaalfactuurbedrag-totaal_volgens_ontvangen_factuur+betalingsverschil;
	betalingssaldo=parseFloat(betalingssaldo.toFixed(2));
	put_float_input("uitkomst_betalingssaldo",betalingssaldo);

	$("#opmerking_totaal_volgens_ontvangen_factuur").html("");
	$("#submit1frm").removeAttr("disabled");
	if(!onload) $("select[name='input[factuurbedrag_gecontroleerd]']").val("0");

	if(totaalfactuurbedrag===0) {
		// alert(totaalfactuurbedrag);
		$(".inkoop_van_0_toegestaan").show();
		if($("input[name='input[inkoop_van_0_toegestaan]']").is(":checked")) {
			if(!onload) $("select[name='input[factuurbedrag_gecontroleerd]']").val("1");
		}
	} else {
		$(".inkoop_van_0_toegestaan").hide();
	}


	if(totaal_volgens_ontvangen_factuur>0||totaal_volgens_ontvangen_factuur<0) {
		if(betalingssaldo==0) {
			if(totaal_volgens_ontvangen_factuur==totaalfactuurbedrag) {
				if(betalingsverschil==0) {
					$("#opmerking_totaal_volgens_ontvangen_factuur").css("color","green");
					$("#opmerking_totaal_volgens_ontvangen_factuur").html("OK");
					if(!onload) $("select[name='input[factuurbedrag_gecontroleerd]']").val("1");
				}
			} else {
				if(!onload) $("select[name='input[factuurbedrag_gecontroleerd]']").val("2");
			}
		} else {
			var verschil=totaalfactuurbedrag-totaal_volgens_ontvangen_factuur;
			verschil=parseFloat(verschil.toFixed(2));
			if(verschil>0||verschil<0) {
				if(verschil>0) {
					$("#opmerking_totaal_volgens_ontvangen_factuur").css("color","green");
				} else {
					$("#opmerking_totaal_volgens_ontvangen_factuur").css("color","red");
				}
				verschil=0-verschil;
				verschil=wt_number_format(verschil,2,",","");
				$("#opmerking_totaal_volgens_ontvangen_factuur").html("verschil: "+verschil);
				$("#submit1frm").attr("disabled","disabled");
			}
		}
	}

	// tijdelijk: totaalfactuurbedrag opslaan
	if($(".totaalfactuurbedrag_autosave").length>0) {
		document.frm.submit();
	}
}

function garantie_inkoopgegevens_berekenen() {
	//
	// berekeningen uitvoeren garantie-inkoop
	//


	// form-values opvragen
	var inkoopbruto=get_float_input('bruto');
	var inkoopcommissie=get_float_input('korting_percentage');
	var inkoopkorting=get_float_input('korting_euro');
	var inkoopkorting_percentage=get_float_input('inkoopkorting_percentage');
	var inkoopkorting_euro=get_float_input('inkoopkorting_euro');


	// inkoopmincommissie berekenen
	inkoopmincommissie=bedrag_min_korting(inkoopbruto,inkoopcommissie);


	// inkoopnetto berekenen
	var inkoopnetto=inkoopmincommissie;
	inkoopnetto=inkoopnetto-inkoopkorting;
	inkoopnetto=bedrag_min_korting(inkoopnetto,inkoopkorting_percentage);
	inkoopnetto=inkoopnetto-inkoopkorting_euro;
	inkoopnetto=parseFloat(inkoopnetto.toFixed(2));

	// uitkomsten in velden plaatsen
	put_float_input("uitkomst_garantie_inkoopmincommissie",inkoopmincommissie);
	put_float_input("uitkomst_garantie_inkoopnetto",inkoopnetto);
	document.forms['frm'].elements['input[netto]'].value=$(".uitkomst_garantie_inkoopnetto").html();
}

function inkoopgegevens_bijkomend_factuur_wissen(e,bedrag) {
	// bijkomende kosten die op de leveranciersfactuur staan van deze factuur afhalen
	var nieuw_bedrag=get_float_input('extraopties_totaal');
	if($(e).prev("input:hidden").val()=="1") {
		$(e).closest("tr").removeClass("bijkomend_doorstrepen");
		$(e).prev("input:hidden").val("0");
		nieuw_bedrag=nieuw_bedrag+bedrag;
	} else {
		$(e).closest("tr").addClass("bijkomend_doorstrepen");
		$(e).prev("input:hidden").val("1");
		nieuw_bedrag=nieuw_bedrag-bedrag;
	}
	document.forms['frm'].elements['input[extraopties_totaal]'].value=nieuw_bedrag;
	inkoopgegevens_berekenen(false);
	return false;
}

function inkoopaanbetaling_wijzigen() {
	// inkoopaanbetaling van een boeking wijzigen
	var bedrag = prompt("Hoeveel bedraagt de aanbetaling?",$("input[name='inkoopaanbetaling_gewijzigd']").val());
	if(bedrag!=null) {
		$("input[name='inkoopaanbetaling_gewijzigd']").val(bedrag);
		$("form[name='inkoopaanbetaling_gewijzigd']").submit();
	}
	return false;
}

function isFloat(val) {
	if(!val || (typeof val != "string" || val.constructor != String)) {
		return(false);
	}
	var isNumber = !isNaN(new Number(val));
	if(isNumber) {
		if(val.indexOf('.') != -1) {
			return(true);
		} else {
			return(false);
		}
	} else {
		return(false);
	}
}

function besteldatum_invullen() {
	if($("[name='input[besteldatum][day]']").val()&&$("[name='input[besteldatum][month]']").val()&&$("[name='input[besteldatum][year]']").val()) {

	} else {
		var d = new Date();
		var curr_day = d.getDate();
		var curr_month = d.getMonth()+1;
		var curr_year = d.getFullYear();

		$("[name='input[besteldatum][day]']").val(curr_day);
		$("[name='input[besteldatum][month]']").val(curr_month);
		$("[name='input[besteldatum][year]']").val(curr_year);

		var besteldatum_td=$(".besteldatum_td");
		besteldatum_td.css("background-color","yellow");
		setTimeout(function() {
			besteldatum_td.css("background-color","#ffffff");
			setTimeout(function() {
				besteldatum_td.css("background-color","yellow");
			},300);
		},300);
	}
}

function slidetoggle(name) {
	// plusicon vervangen door minicon
	var existElement=document.getElementById('plusmin_'+name);
	if(existElement != null) {
		if(document.getElementById('plusmin_'+name).src.match('plusicon')) {
			document.getElementById('plusmin_'+name).src=document.getElementById('plusmin_'+name).src.replace('plusicon','minicon');
		} else {
			document.getElementById('plusmin_'+name).src=document.getElementById('plusmin_'+name).src.replace('minicon','plusicon');
		}
	}

	// openklappen/dichtklappen
	$('#'+name).slideToggle('fast',function(){
		setHgt2();
	}
	);
	return false;
}

function skigebied_dubbel(checkkoppeling) {
	var gebruikt = [];
	var teller = [];
	for(i=1;i<=5;i++) {
		for(j=1;j<=5;j++) {
			var koppeling='koppeling_'+i+'_'+j;
			if(!teller[document.forms['frm'].elements['input['+koppeling+']'].value]) {
				teller[document.forms['frm'].elements['input['+koppeling+']'].value]=0;
			}
			if(document.forms['frm'].elements['input['+koppeling+']'].value>0) {
				gebruikt[document.forms['frm'].elements['input['+koppeling+']'].value]=1;
				teller[document.forms['frm'].elements['input['+koppeling+']'].value]=teller[document.forms['frm'].elements['input['+koppeling+']'].value]+1;
			}
		}
	}

	for(i=1;i<=5;i++) {
		for(j=1;j<=5;j++) {
			var koppeling='koppeling_'+i+'_'+j;
			for(var k=0,option; option = document.forms['frm'].elements['input['+koppeling+']'].options[k];k++) {
				if(gebruikt[option.value]==1 && document.forms['frm'].elements['input['+koppeling+']'].value!=option.value) {
					document.forms['frm'].elements['input['+koppeling+']'].options[k].style.color='#878481';
					document.forms['frm'].elements['input['+koppeling+']'].options[k].style.fontStyle='italic';
				} else {
					document.forms['frm'].elements['input['+koppeling+']'].options[k].style.color='#000000';
					document.forms['frm'].elements['input['+koppeling+']'].options[k].style.fontStyle='normal';
				}
			}
		}
	}
	if(checkkoppeling!='onload') {
		if(document.forms['frm'].elements['input['+checkkoppeling+']'].value>0 && teller[document.forms['frm'].elements['input['+checkkoppeling+']'].value]>1) {
			alert('Deze regio is al geselecteerd');
			document.forms['frm'].elements['input['+checkkoppeling+']'].value='';
		}
	}
}

var naamdatum_toegevoegd=0;

function naamdatum_toevoegen(t,text) {
	if(naamdatum_toegevoegd==0) {
		if(t.value.length>0) {
			t.value=t.value+'\n\n';
		}
		t.value=t.value+text+'\n';
		if(t.setSelectionRange) {
			t.setSelectionRange(t.value.length,t.value.length);
			t.focus();
		} else if(t.createTextRange) {
			var r = t.createTextRange();
			r.collapse(false);
			r.select();
		}
		naamdatum_toegevoegd=1;
	}
}

function auto_resnr(aankomstdatum_exact) {
	document.forms['frm'].elements['input[reserveringsnummer_extern]'].value='';
	document.forms['frm'].elements['input[reserveringsnummer_extern]'].style.backgroundColor='yellow';
	$.getJSON(
			'cms/wtjson.php?t=1&leverancier_id='+document.forms['frm'].elements['input[leverancier_id]'].value+'&aankomstdatum_exact='+aankomstdatum_exact,
			function(data){
				if(data.leverancier_id==document.forms['frm'].elements['input[leverancier_id]'].value&&data.reserveringsnummer_2>0) {
					document.forms['frm'].elements['input[reserveringsnummer_extern]'].value=data.reserveringsnummer_2;
					document.forms['frm'].elements['input[reserveringsnummer_extern]'].style.backgroundColor='';
				}
			}
	);
}

function auto_resnr_bij_boeken(aankomstdatum_exact) {
	document.forms['frm'].elements['input[reserveringsnummer_2]'].value='';
	document.forms['frm'].elements['input[reserveringsnummer_2]'].style.backgroundColor='yellow';
	$.getJSON(
			'cms/wtjson.php?t=1&leverancier_id='+document.forms['frm'].elements['input[leverancierid]'].value+'&aankomstdatum_exact='+aankomstdatum_exact,
			function(data){
				if(data.leverancier_id==document.forms['frm'].elements['input[leverancierid]'].value&&data.reserveringsnummer_2>0) {
					document.forms['frm'].elements['input[reserveringsnummer_2]'].value=data.reserveringsnummer_2;
					document.forms['frm'].elements['input[reserveringsnummer_2]'].style.backgroundColor='';
				}
			}
	);
}


function auto_garantienr(aankomstdatum_exact) {
	document.forms['frm'].elements['input[reserveringsnummer_extern]'].value='';
	document.forms['frm'].elements['input[reserveringsnummer_extern]'].style.backgroundColor='yellow';
	$.getJSON(
			'cms/wtjson.php?t=3&leverancier_id='+document.forms['frm'].elements['input[leverancier_id]'].value+'&aankomstdatum_exact='+aankomstdatum_exact,
			function(data){
				if(data.leverancier_id==document.forms['frm'].elements['input[leverancier_id]'].value&&data.garantienummer>0) {
					document.forms['frm'].elements['input[reserveringsnummer_extern]'].value=data.garantienummer;
					document.forms['frm'].elements['input[reserveringsnummer_extern]'].style.backgroundColor='';
				}
			}
	);
}

function setdate(field,time) {
	var currentTime = new Date();
	if(time!='') {
		currentTime.setTime(time*1000);
	}
	var day = currentTime.getDate()
	var month = currentTime.getMonth() + 1
	var year = currentTime.getFullYear()
	document.frm.elements['input['+field+'][day]'].value=day;
	document.frm.elements['input['+field+'][month]'].value=month;
	document.frm.elements['input['+field+'][year]'].value=year;
}

var checkall_value=0;

function checkall(field) {
	if(checkall_value==1) {
		$('.checkboxes').attr('checked', false);
		checkall_value=0;
	} else {
		$('.checkboxes').attr('checked', true);
		checkall_value=1;
	}
	return false;
}

function fieldcopy(field1,field2) {
	document.frm.elements['input['+field2+']'].value=document.frm.elements['input['+field1+']'].value;
}

function garanties_seizoen_naar_datum(dit,seizoenids,begindatums,einddatums) {

	seizoenids_array=seizoenids.split(',');
	begindatums_array=begindatums.split(',');
	einddatums_array=einddatums.split(',');

	for(i=0; i<seizoenids_array.length;i++) {
		if(dit.value==seizoenids_array[i]) {
			document.frm.elements['input[van][year]'].value=parseInt(begindatums_array[i].substring(0,4));
			document.frm.elements['input[van][month]'].value=parseInt(begindatums_array[i].substring(4,6));
			document.frm.elements['input[van][day]'].value=parseInt(begindatums_array[i].substring(6,8));

			document.frm.elements['input[tot][year]'].value=parseInt(einddatums_array[i].substring(0,4));
			document.frm.elements['input[tot][month]'].value=parseInt(einddatums_array[i].substring(4,6));
			document.frm.elements['input[tot][day]'].value=parseInt(einddatums_array[i].substring(6,8));
		}
	}
}

function seizoen_naar_datum(dit,seizoenids,begindatums,einddatums,veldnaam_begindatum,veldnaam_einddatum) {

	seizoenids_array=seizoenids.split(',');
	begindatums_array=begindatums.split(',');
	einddatums_array=einddatums.split(',');

//alert(dit.value);

	for(i=0; i<seizoenids_array.length;i++) {
		if(dit.value==seizoenids_array[i]) {
			document.frm.elements['input['+veldnaam_begindatum+'][year]'].value=parseInt(begindatums_array[i].substring(0,4));
			document.frm.elements['input['+veldnaam_begindatum+'][month]'].value=parseInt(begindatums_array[i].substring(4,6));
			document.frm.elements['input['+veldnaam_begindatum+'][day]'].value=parseInt(begindatums_array[i].substring(6,8));

			document.frm.elements['input['+veldnaam_einddatum+'][year]'].value=parseInt(einddatums_array[i].substring(0,4));
			document.frm.elements['input['+veldnaam_einddatum+'][month]'].value=parseInt(einddatums_array[i].substring(4,6));
			document.frm.elements['input['+veldnaam_einddatum+'][day]'].value=parseInt(einddatums_array[i].substring(6,8));
		}
	}
}

function printField(textfield) {
	var s = textfield.value;
	var regExp=/\n/gi;
	s = s.replace(regExp,'<br>');
	pWin = window.open('','_blank','location=yes, width=700, height=400');
	pWin.document.open();
	pWin.document.write('<html><head></head><body style="font-family:Verdana;font-size:0.8em;">');
	pWin.document.write(s);
	pWin.document.write('</body></html>');
	pWin.print();
	pWin.document.close();
	pWin.close();
}

function betaling_goedkeuren(theLink, msg, bedrag) {
	// gebruik: onclick="return confirmClick(this,'Zeker weten?');"
	var invoerbedrag = prompt(msg,bedrag);
	if(invoerbedrag!=null && invoerbedrag!="") {
		theLink.href += '&confirmed=1&goedgekeurde_betaling='+invoerbedrag;
		return true;
	} else {
		return false;
	}
}

function wt_number_format (number, decimals, dec_point, thousands_sep) {
	// Formats a number with grouped thousands
	// Strip all characters but numerical ones.
	number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
	var n = !isFinite(+number) ? 0 : +number,
		prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
		dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
		s = '',
		toFixedFix = function (n, prec) {
			var k = Math.pow(10, prec);            return '' + Math.round(n * k) / k;
		};
	// Fix for IE parseFloat(0.55).toFixed(0) = 0;
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
	if (s[0].length > 3) {        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	}
	if ((s[1] || '').length < prec) {
		s[1] = s[1] || '';
		s[1] += new Array(prec - s[1].length + 1).join('0');    }
	return s.join(dec);
}
