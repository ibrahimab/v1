
chaletcms_global = {};

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

	//
	// tablelist-hovers
	//

	// tablelist-edit
	$(".tbl td.tbl_icon_edit").mouseenter(function(event) {
		$(this).parent().addClass("row_edit");
	});
	$(".tbl td.tbl_icon_edit").mouseleave(function(event) {
		$(this).parent().removeClass("row_edit");
	});

	// tablelist-show
	$(".tbl td.tbl_icon_show").mouseenter(function(event) {
		$(this).parent().addClass("row_show");
	});
	$(".tbl td.tbl_icon_show").mouseleave(function(event) {
		$(this).parent().removeClass("row_show");
	});

	// tablelist-delete
	$(".tbl td.tbl_icon_delete").mouseenter(function(event) {
		$(this).parent().addClass("row_delete");
	});
	$(".tbl td.tbl_icon_delete").mouseleave(function(event) {
		$(this).parent().removeClass("row_delete");
	});

	// tbl_icon_delete_checkbox
	$(".tbl td.tbl_icon_delete_checkbox input[type=checkbox]").change(function() {
		if ($(this).is(":checked")) {
			$(this).parent().parent().addClass("row_delete_checkbox");
		} else {
			$(this).parent().parent().removeClass("row_delete_checkbox");
		}
	});


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

	// other cursor for links with target=_blank
	$("a[target='_blank']").addClass("target_blank");


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

	// cms_handmatige_opties.php: handmatige optie: select "alg" => alg_aantal" becomes 1
	$("#cms_body_cms_handmatige_opties select[name='input[persoonnummer]']").change(function() {
		if($(this).val()=="alg") {
			if($("input[name='input[alg_aantal]']").val()=="") {
				$("input[name='input[alg_aantal]']").val("1");
			}
		} else {
			$("input[name='input[alg_aantal]']").val("");
		}
	});

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

	// CMS-list "Garanties Ongebruikte": highlight option-guarantees
	$("#cms_body_cms_garanties .td_optie[data-content=1]").parent().addClass("tr_bestelstatus_hele_tr_opvallend");

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
			var copy_field_to_array = $(this).data("copy_field_to").split(",");
			var copy_field_to = '';
			for (i = 0; i < copy_field_to_array.length; i++) {
				copy_field_to = $("input[name='input["+copy_field_to_array[i]+"]']");
				if(copy_field_to.length!==0 && copy_field_to.val()=="") {
					copy_field_to.val($(this).val());
				}
			}
		}
	});


	//
	// Bijkomende kosten-cms
	//


	// track changes
	$(document).on("change", ".cms_bk_row select, .cms_bk_row input[type=text]", function(event) {

		var form = $(this).closest("form");
		var seizoen_id = form.find("input[name='seizoen_id']").val();

		var row = $(this).closest(".cms_bk_row");
		var bk_soort_id = row.data("soort_id");

		row.addClass("cms_bk_save_row");

		$(".cms_bk_seizoen[data-seizoen_id="+seizoen_id+"] .cms_bk_row_afwijkend_type[data-soort_id="+bk_soort_id+"]").addClass("cms_bk_row_overwrite");

		if($(".cms_bk_seizoen[data-seizoen_id="+seizoen_id+"] .cms_bk_row_afwijkend_type.cms_bk_row_overwrite").length!==0) {
			$(".cms_bk_seizoen[data-seizoen_id="+seizoen_id+"] .cms_bk_type_afwijkingen_overschrijven").css("visibility", "visible");
			form.find("input[type=submit]").prop("disabled" , true);
		}
	});

	$(document).on("change", "select[name=bk_new]", function(event) {

		var bk_soort_id = $(this).val();
		var form = $(this).closest("form");
		var seizoen_id = form.find("input[name='seizoen_id']").val();
		var cms_bk_seizoen = $("div.cms_bk_seizoen[data-seizoen_id='" + seizoen_id + "']");

		form.find("select[name=bk_new]").val("0");

		$.getJSON(
			"cms/wtjson.php?t=bk_new&bk_soort_id="+bk_soort_id+"&soort="+form.find("input[name='soort']").val()+"&id="+form.find("input[name='id']").val(),
			function(data) {
				if(data.ok) {
					cms_bk_seizoen.find(".cms_bk_row_header").after(data.html);
					bk_keuzes_actief_inactief();
					setHgt2();
				}
			}
		);
	});


	$(document).on("change", "input[name=type_afwijkingen_overschrijven]", function(event) {

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

	// bk: opmerkingen_intern: keep synced
	$(document).on("blur", ".cms_bk_opmerkingen_intern textarea", function(event) {
		var cms_bk_opmerkingen_intern = $(this).val();
		$(".cms_bk_opmerkingen_intern textarea").each(function(){
			$(this).val(cms_bk_opmerkingen_intern);
		})
	});

	$(document).on("submit", ".cms_bk_seizoen form", function(event) {

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
							+"&bijkomendekosten_checked="+(form.find("input[name='bijkomendekosten_checked']").is(":checked") ? "1" : "0")
							+"&all_rows_for_log="+encodeURIComponent(form.find("input[name='all_rows_for_log']").val())
							,
							function(data) {
								if(data.saved) {
									$(".cms_bk_seizoen[data-seizoen_id="+seizoen_id+"] .cms_bk_row_afwijkend_type.cms_bk_row_overwrite").remove();
									wt_popupmsg("De bijkomende kosten zijn opgeslagen.");
									$(".cms_bk_seizoen[data-seizoen_id="+seizoen_id+"] .cms_bk_type_afwijkingen_overschrijven").css("visibility", "hidden");

									// set all_rows_for_log
									form.find("input[name='all_rows_for_log']").val(data.all_rows_for_log);

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

		var from_seizoen_id = 0;
		var seizoen_id = 0;
		var id = 0;

		cms_bk_kopieer.find("button").prop("disabled", true);


		if(cms_bk_kopieer.data("last_seizoen_id")) {

			var is_confirmed = confirm('Alle bestaande gegevens van het nieuwe seizoen worden overschreven en direct opgeslagen.\n\nZeker weten?');
			cms_bk_kopieer.find("img").show();

			if(is_confirmed) {
				id = cms_bk_kopieer.data("id");
				seizoen_id = cms_bk_kopieer.data("seizoen_id");
				from_seizoen_id = cms_bk_kopieer.data("last_seizoen_id");

				$("html, body").animate({scrollTop:$(".cms_bk_kopieer_season").position().top}, 'slow', function(){
					cms_bk_all_rows_wrapper.slideUp("normal");
				});
			}
		} else {

			cms_bk_kopieer.find("img").show();

			// strip out non-numerical characters
			id = cms_bk_kopieer.find("input").val().replace(/\D/g,'');
			seizoen_id = cms_bk_kopieer.closest(".cms_bk_seizoen").data("seizoen_id");
		}

		var last_used_field = '';

		var cms_bk_all_rows = $(".cms_bk_seizoen[data-seizoen_id="+seizoen_id+"] .cms_bk_all_rows")
		var cms_bk_all_rows_wrapper = $(".cms_bk_seizoen[data-seizoen_id="+seizoen_id+"] .cms_bk_all_rows_wrapper")

		if(id) {

			$.getJSON(
				'cms/wtjson.php?t=bk_copy&id='+id+"&sid="+seizoen_id+"&from_sid="+from_seizoen_id,
				function(data) {
					if(data.cms_bk_all_rows) {
						cms_bk_all_rows_wrapper.slideUp("normal", function() {
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



	$(document).on("click", ".unfinished_mailto", function(event) {
		// handle clicks on "verstuur"-links for unfinished bookings

		var current_link = $(this);

		event.preventDefault();

		// get mailto-link via rpc (in base64-format)
		$.getJSON(
			'cms/wtjson.php?t=unfinished_mailto&boeking_id='+current_link.data("boeking_id"),
			function(data) {
				if(data.link) {

					// change href of link (decode base64)
					window.location.href = window.atob(data.link);
					return true;
				}
			}
		);
	});


	$("select.unfinished_change").change(function(event) {
		// handle changes to select-field unfinished bookings

		var current_select = $(this);

		$.getJSON(
			'cms/wtjson.php?t=unfinished_change&boeking_id='+current_select.data("boeking_id")+"&type="+current_select.val(),
			function(data) {
				if(data.ok) {
					current_select.parent().prev("td").html(data.new_field_content);
					current_select.val(0);
				}
			}
		);
	});

	// cms_garanties: set date when selecting option
	$("body#cms_body_cms_garanties #yesnooptie_klant").change(function(event) {

		if ($(this).is(":checked")) {

			// reset stored data
			if (!$("input[name='input[optie_klantnaam]']").val()) {
				$("input[name='input[optie_klantnaam]']").val(chaletcms_global.optie_klantnaam);
			}
			if (!$("textarea[name='input[optie_opmerkingen_intern]']").val()) {
				$("textarea[name='input[optie_opmerkingen_intern]']").val(chaletcms_global.optie_opmerkingen_intern);
			}

			if ($("select[name='input[optie_einddatum][day]']").val()=="") {

				// set date 2 days ahead
				var d = new Date();
				d.setDate(d.getDate() + 2);

				if (d.getDay()==0) {
					// Sunday becomes Monday
					d.setDate(d.getDate() + 1);
				}

				$("select[name='input[optie_einddatum][day]']").val(d.getDate());
				$("select[name='input[optie_einddatum][month]']").val(d.getMonth()+1);
				$("select[name='input[optie_einddatum][year]']").val(d.getFullYear());

				$("select[name='input[optie_einddatum][hour]']").val((d.getHours() < 10 ? '0' + d.getHours() : d.getHours()));
				$("select[name='input[optie_einddatum][minute]']").val("00");
			}
		} else {

			// store data
			chaletcms_global.optie_klantnaam = $("input[name='input[optie_klantnaam]']").val();
			chaletcms_global.optie_opmerkingen_intern = $("textarea[name='input[optie_opmerkingen_intern]']").val();

			// empty all fields
			$("input[name='input[optie_klantnaam]']").val("");
			$("textarea[name='input[optie_opmerkingen_intern]']").val("");

			$("select[name='input[optie_einddatum][day]']").val("");
			$("select[name='input[optie_einddatum][month]']").val("");
			$("select[name='input[optie_einddatum][year]']").val("");
			$("select[name='input[optie_einddatum][hour]']").val("");
			$("select[name='input[optie_einddatum][minute]']").val("");
		}
	});

	// cms_accommodaties.php: add extra distance
	$(".add_extra_distance").click(function(event) {

		event.preventDefault();

		// get hightest counter
		var counter = $(".extra_distance").map(function() {
			return parseInt($(this).data("counter"), 10);
		}).get();
		var new_counter = Math.max.apply(Math, counter) + 1;

		// get html to insert
		$.getJSON(
			'cms/wtjson.php?t=add_extra_distance&counter=' + new_counter,
			function(data) {
				if(data.html) {
					$(".add_extra_distance").closest("tr").before(data.html);

					// set correct height for content-div
					setHgt2();
				}
			}
		);
	});

	// copy extra distance name to field with actual distance
	$(document).on("keyup", "input.extra_distance_name", function(event) {
		$(this).closest("tr").next("tr").find("span.distance_name").html( $(this).val() );
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

function retourbetaling_goedkeuren(theLink, msg, bedrag) {
	var invoerbedrag = prompt(msg,bedrag);
	if(invoerbedrag!=null && invoerbedrag!="") {
		theLink.href += '&confirmed=1&goedgekeurde_betaling=-'+invoerbedrag;
		return true;
	} else {
		return false;
	}
}

/**
 * This method checks whether an IBAN code given is valid
 * This is taken from the jquery validation plugin, and somewhat changed
 * to make it standalone without the jquery validation plugin.
 * Reason is because I only needed the iban check
 *
 * @copyright https://github.com/jzaefferer/jquery-validation
 * @author    Ibrahim Abdullah <ibrahim@chalet.nl>
 * @param     String
 * @return    Boolean
 */
var check_iban = (function(e){var t=e.replace(/ /g,"").toUpperCase(),n="",r=true,i="",s="",o,u,a,f,l,c,h,p,d;o=t.substring(0,2);c={AL:"\\d{8}[\\dA-Z]{16}",AD:"\\d{8}[\\dA-Z]{12}",AT:"\\d{16}",AZ:"[\\dA-Z]{4}\\d{20}",BE:"\\d{12}",BH:"[A-Z]{4}[\\dA-Z]{14}",BA:"\\d{16}",BR:"\\d{23}[A-Z][\\dA-Z]",BG:"[A-Z]{4}\\d{6}[\\dA-Z]{8}",CR:"\\d{17}",HR:"\\d{17}",CY:"\\d{8}[\\dA-Z]{16}",CZ:"\\d{20}",DK:"\\d{14}",DO:"[A-Z]{4}\\d{20}",EE:"\\d{16}",FO:"\\d{14}",FI:"\\d{14}",FR:"\\d{10}[\\dA-Z]{11}\\d{2}",GE:"[\\dA-Z]{2}\\d{16}",DE:"\\d{18}",GI:"[A-Z]{4}[\\dA-Z]{15}",GR:"\\d{7}[\\dA-Z]{16}",GL:"\\d{14}",GT:"[\\dA-Z]{4}[\\dA-Z]{20}",HU:"\\d{24}",IS:"\\d{22}",IE:"[\\dA-Z]{4}\\d{14}",IL:"\\d{19}",IT:"[A-Z]\\d{10}[\\dA-Z]{12}",KZ:"\\d{3}[\\dA-Z]{13}",KW:"[A-Z]{4}[\\dA-Z]{22}",LV:"[A-Z]{4}[\\dA-Z]{13}",LB:"\\d{4}[\\dA-Z]{20}",LI:"\\d{5}[\\dA-Z]{12}",LT:"\\d{16}",LU:"\\d{3}[\\dA-Z]{13}",MK:"\\d{3}[\\dA-Z]{10}\\d{2}",MT:"[A-Z]{4}\\d{5}[\\dA-Z]{18}",MR:"\\d{23}",MU:"[A-Z]{4}\\d{19}[A-Z]{3}",MC:"\\d{10}[\\dA-Z]{11}\\d{2}",MD:"[\\dA-Z]{2}\\d{18}",ME:"\\d{18}",NL:"[A-Z]{4}\\d{10}",NO:"\\d{11}",PK:"[\\dA-Z]{4}\\d{16}",PS:"[\\dA-Z]{4}\\d{21}",PL:"\\d{24}",PT:"\\d{21}",RO:"[A-Z]{4}[\\dA-Z]{16}",SM:"[A-Z]\\d{10}[\\dA-Z]{12}",SA:"\\d{2}[\\dA-Z]{18}",RS:"\\d{18}",SK:"\\d{20}",SI:"\\d{15}",ES:"\\d{20}",SE:"\\d{20}",CH:"\\d{5}[\\dA-Z]{12}",TN:"\\d{20}",TR:"\\d{5}[\\dA-Z]{17}",AE:"\\d{3}\\d{16}",GB:"[A-Z]{4}\\d{14}",VG:"[\\dA-Z]{4}\\d{16}"};l=c[o];if(typeof l!=="undefined"){h=new RegExp("^[A-Z]{2}\\d{2}"+l+"$","");if(!h.test(t)){return false}}u=t.substring(4,t.length)+t.substring(0,4);for(p=0;p<u.length;p++){a=u.charAt(p);if(a!=="0"){r=false}if(!r){n+="0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ".indexOf(a)}}for(d=0;d<n.length;d++){f=n.charAt(d);s=""+i+""+f;i=s%97}return i===1});

/**
 * This functions transforms european number format
 * to javascript readable number
 *
 * @author Ibrahim Abdullah <ibrahim@chalet.nl>
 * @param  number String
 * @return Float
 */
var euro_to_float = function(number) {

	return parseFloat(number

					  // first replace all non-essential characters
					  .replace(/[^0-9,]/g, '')

					  // then replace the points with comma's
					  .replace(/,/g, '.')

	) || 0; // or return 0 for NaN
};

/**
 * This function creates a thin wrapper around jquery ui
 * dialog plugin to create a simple form.
 *
 * @author Ibrahim Abdullah <ibrahim@chalet.nl>
 * @param selector          div that contains the actual dialog html
 * @param size.width        width of dialog
 * @param size.height       height of dialog
 * @param labels.submit     submit button label
 * @param labels.cancel     cancel button label
 * @param submit_handler    submit handler
 * @param close_handler     close_handler
 * @return $.dialog
 */
function dialog_form(selector, size, labels, submit_handler, close_handler) {

	var dialog       = null;
	var form         = $(selector).find('form');
	var submit_label = labels.submit;
	var cancel_label = labels.cancel || null;
	var options      = {

		autoOpen: false,
		width:    size['width'],
		height:   size['height'],
		modal:    true,
		buttons:  {}
	};

	if (undefined !== close_handler) {

		options.close = function() {
			close_handler.apply(dialog, [form]);
		}
	}

	/**
	 * This button does not close the dialog for you.
	 * That is the responsibility of the submit handler!
	 */
	options.buttons[submit_label] = function() {
		submit_handler.apply(dialog, [form]);
	};

	if (null !== cancel_label) {

		options.buttons[cancel_label] = function() {
			dialog.dialog('close');
		};
	}

	return dialog = $(selector).dialog(options);
}

/**
 * This method allows you to easily create popups
 * for displaying a simple notification to the user
 *
 * @param title   		 String
 * @param message 		 String
 * @param submit_handler Function
 */
function popup_dialog(title, message, submit_handler) {

	var popup_dialog_template = '<div data-role="dialog-form" data-dialog="popup-dialog">' +
									 '<h1 data-role="popup-dialog-title"></h1>'            +
									 '<p data-role="popup-dialog-message"></p>'            +
								'</div>';

	// the popup dialog html is lazily instantiated, only when needed is it created
	if ($('[data-dialog="popup-dialog"]').length === 0) {
		$('body').append(popup_dialog_template);
	}

	var popup_dialog_element = $('[data-dialog="popup-dialog"]');

	popup_dialog_element.find('[data-role="popup-dialog-title"]').text(title);
	popup_dialog_element.find('[data-role="popup-dialog-message"]').text(message);

	return dialog_form('[data-dialog="popup-dialog"]', {width: 400, height: 300}, {submit: 'OK'}, function() {

		this.dialog('close');
		if (undefined !== submit_handler) {
			submit_handler.apply(this);
		}
	});
}

/**
 * This method validates a refund form
 * This was extracted because this method handles either a create action or an update
 */
function validate_refund_form(form, success) {

   /**
	* This method is called when someone submits the form
	* It validates, and when successfull sends the request further
	*
	* @context $.dialog
	* @see {dialog_form()}
	*/
   var error_class = 'ui-state-error';
   var fields      = {};
   var errors      = [];
   var prefix      = 'Retourbetaling ' + form.data('reservation-number');

   // transform form data into usable object
   form.serializeArray().map(function(field) { fields[field.name] = field.value; });

   // reset previous validation errors
   form.find('[data-role="refund-request-form-label"]').removeClass(error_class);

   // find out if land code is United States (= 10)
   var use_iban = fields.iban !== 'n.n.b.';

   /**
	* Performing some validations. The following elements are checked:
	* - name
	* - amount
	*   => has to be greater than 0
	* - iban
	* - description
	*   => has to start with @see(prefix) according to JIRA-CMS-75
	*   => has to be a maximum of 140 characters according to JIRA-CMS-75
	*/
   if ($.trim(fields.name) === '') {

	   form.find('[data-role="refund-request-form-label"][for="refund-request-form-label-name"]').addClass(error_class);
	   errors.push('name');
   }

   if (euro_to_float(fields.amount) <= 0) {

	   form.find('[data-role="refund-request-form-label"][for="refund-request-form-label-amount"]').addClass(error_class);
	   errors.push('amount');
   }

   if ((use_iban && false === check_iban(fields.iban)) || ($.trim(fields.iban) === '')) {

	   form.find('[data-role="refund-request-form-label"][for="refund-request-form-label-iban"]').addClass(error_class);
	   errors.push('iban');
   }

   if (errors.length === 0) {

	   // no errors were found, send request
	   this.dialog('close');

	   // call success callback and pass in the fields
	   success(fields);
   }
}

/**
 * This method creates a dialog, performs validations
 * and on success executes an ajax request that will persist the data.
 *
 * @param selector Dialog html div
 * @return $.dialog
 */
function create_refund_form(selector) {

	// error class
	var error_class = 'ui-state-error';

	return dialog_form(selector, {width: 400, height: 400}, {submit: 'Toevoegen', cancel: 'Annuleren'}, function(form) {

		validate_refund_form.apply(this, [form, function(fields) {

			// appending boeking_id
			fields['boeking_id'] = form.data('reservation-id');

			$.ajax({

				type:    'post',
				url:     'ajax/refund_request.php',
				data:    fields,
				success: function() {
					window.location.reload();
				},
				error:   function() {

					popup_dialog('Fout', 'Retourbetaling verzoek is niet gelukt', function() {
						window.location.reload();
					}).dialog('open');
				}
			});
		}]);

	}, function(form) {

		/**
		 * This method handles the close event of the dialog
		 * Reset the form and remove all error labels
		 */
		form.get(0).reset();
		form.find('[data-role="refund-request-form-label"]').removeClass(error_class);
	});
}

/**
 * This method creates a dialog, performs validations
 * and on success executes an ajax request that will update the refund request
 *
 * @param selector Dialog html div
 * @param fields Object form data
 * @return $.dialog
 */
function update_refund_form(selector, fields) {

	// error class
	var error_class = 'ui-state-error';

	// setting form data
	var form = $(selector).find('form');

	form.get(0).reset();
	for (var i in fields) {

		if (fields.hasOwnProperty(i)) {
			form.find('[name="' + i + '"]').val(fields[i]);
		}
	}

	return dialog_form(selector, {width: 400, height: 400}, {submit: 'Aanpassen', cancel: 'Annuleren'}, function(form) {

		validate_refund_form.apply(this, [form, function(form_data) {

			// appending retour ID
			form_data['boeking_retour_id'] = fields['boeking_retour_id'];

			// appending boeking_id
			form_data['boeking_id'] = form.data('reservation-id');

			$.ajax({

				type:    'put',
				url:     'ajax/refund_request.php',
				data:    form_data,
				success: function() {
					window.location.reload();
				},
				error:   function() {

					popup_dialog('Fout', 'Retourbetaling verzoek is niet aangepast', function() {
						window.location.reload();
					}).dialog('open');
				}
			});
		}]);

	}, function(form) {

		/**
		 * This method handles the close event of the dialog
		 * Reset the form and remove all error labels
		 */
		form.get(0).reset();
		form.find('[data-role="refund-request-form-label"]').removeClass(error_class);
	});
}

function mark_refund(node, id, rows) {

	var element = $(node);
	var method  = element.data('method');

	$.ajax({

		type: 'post',
		url:  'ajax/mark_refund.php',
		data: {method: method, id: id},
		success: function(data) {

			if (data.type === 'success') {

				if (undefined !== rows) {

					rows.fadeOut(function() {
							rows.remove();
						});

				}

			} else {
				popup_dialog('Fout', 'Het verwerken van de retourbetaling is niet gelukt').dialog('open');
			}
		},
		error: function() {
			popup_dialog('Fout', 'Het verwerken van de retourbetaling is niet gelukt').dialog('open');
		}
	});
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

jQuery('document').ready(function () {
	var original_language = jQuery('.review_language_selector');
	if (original_language.length < 1)
	{
		return;
	}
	update_language_fields();
	jQuery('.btn-translation').click(function (e) {
		var button = jQuery(this);
		var text = jQuery('.review_original_comment');
		if (original_language.length < 1 || original_language.val() == "") {
			alert(no_language_error);
			return false;
		}
		if(text.length < 1 || text.val().length < 1) {
			return false;
		}
		jQuery.ajax({
			type: "GET",
			url: "rpc_json.php",
			data: { t: "google_translate", text: text.val(), to: button.val(), from: original_language.val() }
		})
			.done(function (json) {
				if (json.error) {
					alert (json.error)
				}
				jQuery.each(json.translations, function (lang, translation) {
					var language_textarea = jQuery('textarea[name="input[websitetekst_gewijzigd_'+lang+']"]');
					if (language_textarea.length < 1) {
						console.log('Could not determine language textarea');
					}
					else {
						language_textarea.val(translation);
					}
				})
			});
		e.preventDefault();
	});
	original_language.change(function(){
		update_language_fields();
	});
});

function update_language_fields() {
	var original_language = jQuery('select[name="input[tekst_language]"]');
	if (original_language.length < 1){
		return false;
	}
	var en_field = jQuery('textarea[name="input[websitetekst_gewijzigd_en]"]').parents('tr');
	var nl_field = jQuery('textarea[name="input[websitetekst_gewijzigd_nl]"]').parents('tr');
	var de_field = jQuery('textarea[name="input[websitetekst_gewijzigd_de]"]').parents('tr');
	switch(original_language.val()) {
		case "en":
			en_field.hide();
			nl_field.show();
			de_field.show();
			break;
		case "nl":
			en_field.show();
			nl_field.hide();
			de_field.show();
			break;
		case "de":
			en_field.show();
			nl_field.show();
			de_field.hide();
			break;
		default:
			en_field.show();
			nl_field.show();
			de_field.show();
			break;
	}
}

/**
 * Slugify strings
 *
 * @param string name
 *
 * @return string
 */
function slugify(text)
{
	return text.toString()
			   .replace(/\s+/g, '-')           // Replace spaces with -
			   .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
			   .replace(/\-\-+/g, '-')         // Replace multiple - with single -
			   .replace(/^-+/, '')             // Trim - from start of text
			   .replace(/-+$/, '');            // Trim - from end of text
}

(function(jq) {

	jq(function() {

		/**
		 * Binding the event that will show the dialog on click
		 * only when the entire DOM is ready
		 */
		jq('body').on('click', '[data-role="create-refund-request"]', function(event) {

			event.preventDefault();
			create_refund_form('[data-role="dialog-form"][data-dialog="refund-request-dialog"]').dialog('open');
		});

		jq('body').on('click', '[data-role="update-refund-request"]', function(event) {

			event.preventDefault();
			update_refund_form('[data-role="dialog-form"][data-dialog="refund-request-dialog"]', jq(this).data('form-data')).dialog('open');
		});

		/**
		 * This function will it possible to select the contents of
		 * an element when clicked.
		 */
		jq('body').on('click', '[data-role="select-contents"]', function(event) {

			event.preventDefault();

			var selection = window.getSelection();
			var range     = document.createRange();

			range.selectNodeContents(this);
			selection.removeAllRanges();
			selection.addRange(range);
		});

		/**
		 * This method listens to marking refund actions and removes the row from DOM when successful.
		 */
		jq('body').on('click', '[data-role="mark-refund"]', function(event) {

			event.preventDefault();

			var element = jq(this);
			var id 		= element.data('id');

			mark_refund(this, id, element.parents('tr'));
		});

		jq('body').on('change keyup paste', '[data-role="max-length"]', function() {

			var element = jq(this);
			var val     = jq.trim(element.val());
			var length  = val.length;
			var max     = element.data('max-length');

			if (length > max) {
				element.val(val.substring(0, max));
			}

			if (element.data('max-length-view')) {
				jq('[data-view="' + element.data('max-length-view') + '"]').text(length > max ? max : (length < 0 ? 0 : length));
			}
		});

		jq('body').on('focus', '[data-role="seoname"]', function(event) {

			var element = jq(this);
			var name    = jq(element.data('name-field'));
			console.log(name);
			console.log(element.val());

			if (element.val() === '' && name.val() !== '') {
				element.val(slugify(name.val()));
			}
		});
	});

})($);
