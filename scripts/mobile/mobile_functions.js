function bindCountriesDialCodes() {
	$("input[name*='land']").blur(function(){
		var val = $(this).val();
		var phone = $("input[name*='telefoonnummer']");
		if($.trim(val) != '' && $.trim(phone.val()) == '') {
			$.each( countriesDialCodes, function( key, value ) {
				if(key.toLowerCase() == val.toLowerCase()) {
					phone.val(value);
				}
			});
		}
	});
}

$(function(){

		$(window).resize(function(){
			$("#fancybox-wrap").width("auto");
			$("#fancybox-content").width("auto");
		});

	//var tarieventabel_pijl_boven = $(".tarieventabel_pijl_boven").clone();
	//var tarieventabel_pijl_onder = $(".tarieventabel_pijl_onder").clone();
	////$(".tarieventabel_pijl_boven").remove();
	//$(".tarieventabel_pijl_onder").remove();
	//$(".tarieventabel_wrapper_rechts").append(tarieventabel_pijl_boven).append(tarieventabel_pijl_onder);
	//$(".tarieventabel_pijl_boven").css("left","0");
	//$(".tarieventabel_pijl_onder").css("right","0");

	/**
	 * Boeking step 2
	 */

	$(".popwindow").fancybox({
		type		: 'ajax',
		width		: '100%',
		height		: '100%',
		autoSize	: false,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none',
		margin		: 20,
		scrolling	: 'no',
		ajax : {
			type	: "GET"
		},
				'onComplete'	: function() {
						$.fancybox.resize();
		},
	});

	// iOS 7 fix: Add an optgroup to every select in order to avoid truncating the content
	if (navigator.userAgent.match(/(iPad|iPhone|iPod touch);.*CPU.*OS 7_\d/i)) {
		var selects = document.querySelectorAll("select");
		for (var i = 0; i < selects.length; i++ ){
			selects[i].appendChild(document.createElement("optgroup"));
		}
	}

		$(".popwindow-iframe").fancybox({
		type		: 'iframe',
		width		: '100%',
		height		: '100%',
		autoSize	: false,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none',
		margin		: 20,
		scrolling	: 'yes',
				'onComplete'	: function() {
					$.fancybox.resize();
		},

	});

	$('div#menu-left').mmenu({classes: "mm-light"});

	var carouselOptions = {
		//auto: true,
		circular: false,
		autoWidth: true,
		responsive: true,
		visible: 1,
		speed: 100,
		pause: true,
		btnPrev: function() {
			return $(this).find('.prev');
		},
		btnNext: function() {
			return $(this).find('.next');
		},
	};

$(".slideshow").jCarouselLite(carouselOptions);

	if($('#accordion').length > 0 && $().tabs) {
		$('#accordion').accordion({
			collapsible: true,
			autoHeight: false,
			active: false,
						heightStyle: "content",
						beforeActivate: function(event, ui) {
							// The accordion believes a panel is being opened
						   if (ui.newHeader[0]) {
							   var currHeader  = ui.newHeader;
							   var currContent = currHeader.next('.ui-accordion-content');
							// The accordion believes a panel is being closed
						   } else {
							   var currHeader  = ui.oldHeader;
							   var currContent = currHeader.next('.ui-accordion-content');
						   }
							// Since we've changed the default behavior, this detects the actual status
						   var isPanelSelected = currHeader.attr('aria-selected') == 'true';

							// Toggle the panel's header
						   currHeader.toggleClass('ui-corner-all',isPanelSelected).toggleClass('accordion-header-active ui-state-active ui-corner-top',!isPanelSelected).attr('aria-selected',((!isPanelSelected).toString()));

						   // Toggle the panel's icon
						   currHeader.children('.ui-icon').toggleClass('ui-icon-triangle-1-e',isPanelSelected).toggleClass('ui-icon-triangle-1-s',!isPanelSelected);

							// Toggle the panel's content
						   currContent.toggleClass('accordion-content-active',!isPanelSelected);
						   if (isPanelSelected) { currContent.slideUp(); }  else { currContent.slideDown(); }

								var scrollTop = $(".accordion").scrollTop();
								if(ui.newHeader.offset())
									var top = $(ui.newHeader).offset().top;
								else
									var top = ui.oldHeader.offset().top;
							 //do magic to scroll the user to the correct location

							 //works in IE, firefox chrome and safari
								$("html,body").animate({ scrollTop: scrollTop + top -35 }, "slow");
							// 2 = ligging accordion tab
				//if(ui.options.active == 2) {
					//we have to set center for map after resize, but we need to know center BEFORE we resize it
				//google.maps.event.trigger(map, "resize"); //this fix the problem with not completely map
					//var center = map.getCenter();
					google.maps.event.trigger(map, "resize"); //this fix the problem with not completely map
					map.setCenter(new google.maps.LatLng(mapOptions.googlemaps_lat,mapOptions.googlemaps_long));
				//}

						   return false; // Cancels the default action
						},

			change: function( event, ui ) {
								var scrollTop = $(".accordion").scrollTop();
								if(ui.newHeader.offset())
									var top = $(ui.newHeader).offset().top;
								else
									var top = ui.oldHeader.offset().top;
								//do magic to scroll the user to the correct location
								//works in IE, firefox chrome and safari
								$("html,body").animate({ scrollTop: scrollTop + top -35 }, "slow", function(){
									tarieventabel_toelichting_check_season();
								});
								// 2 = ligging accordion tab
				if(ui.options.active == 2) {
					//we have to set center for map after resize, but we need to know center BEFORE we resize it
				//google.maps.event.trigger(map, "resize"); //this fix the problem with not completely map
					//var center = map.getCenter();
					google.maps.event.trigger(map, "resize"); //this fix the problem with not completely map
					map.setCenter(new google.maps.LatLng(mapOptions.googlemaps_lat,mapOptions.googlemaps_long));
				}
			},



		});
	}
		$("a[href*='#extraopties']").click(function() {
			if($("#extraopties").hasClass("accordion-content-active") == false)
				$( "#accordion" ).accordion({ active: 3 });

		});

		$("a[href*='#prijsinformatie']").click(function() {
			if($("#prijsinformatie").hasClass("accordion-content-active") == false){
				var accordionID = parseInt(jQuery("h3[aria-controls='prijsinformatie']").attr("id").split("-")[4]);
				$( "#accordion" ).accordion({ active: accordionID });
			}
		});

		back_url=location.href;
		back_url = back_url.replace(location.hash,"");
		if(back_url.indexOf('toonflex=') > -1) {
			if($("#prijsinformatie").hasClass("accordion-content-active") == false){
				var accordionID = parseInt(jQuery("h3[aria-controls='prijsinformatie']").attr("id").split("-")[4]);
				$( "#accordion" ).accordion({ active: accordionID });
			}
		}

	$("#verfijn #verfijntopheader").click(function(){
		$("#verfijntopheader + div").toggle();
		$("#verfijntopheader span").toggleClass("up");
	});

	$("#verfijn .keuzes_kopjes").click(function(){
		$(this).children("span").toggleClass("up");
		var listId = $(this).attr("id");
		$(".list_"+listId).toggle();

	});


	var tel_input = $("#body_contact input[name=\"input[telefoonnummer]\"]");
	var email_input = $("#body_contact input[name=\"input[email]\"]");
	var email_txt = email_input.parent().prev().text();
	var tel_txt = tel_input.parent().prev().text();

	if(tel_input.length > 0 && email_input.length > 0) {
		$(tel_input, email_input).trigger("change");
		$(email_input).bind("keyup change", function(){
			email_txt = email_txt.replace("*","");
			tel_txt = tel_txt.replace("*","");
			if($.trim(email_input.val()) != "") {
				email_input.parent().prev().text(email_txt+"*");
				tel_input.parent().prev().text(tel_txt);
			} else if(($.trim(tel_input.val()) != "") && ($.trim(email_input.val()) == "")) {
				tel_input.parent().prev().text(tel_txt+"*");
				email_input.parent().prev().text(email_txt);
			} else if(($.trim(email_input.val()) == "") && ($.trim(tel_input.val()) == "")) {
				email_input.parent().prev().text(email_txt+"*");
				tel_input.parent().prev().text(tel_txt);
			}
		});
		$(tel_input).bind("keyup change", function(){
			email_txt = email_txt.replace("*","");
			tel_txt = tel_txt.replace("*","");
			if($.trim(email_input.val()) != "") {
				email_input.parent().prev().text(email_txt+"*");
				tel_input.parent().prev().text(tel_txt);
			} else if(($.trim(tel_input.val()) != "") && ($.trim(email_input.val()) == "")) {
				tel_input.parent().prev().text(tel_txt+"*");
				email_input.parent().prev().text(email_txt);
			} else if(($.trim(email_input.val()) == "") && ($.trim(tel_input.val()) == "")) {
				email_input.parent().prev().text(email_txt+"*");
				tel_input.parent().prev().text(tel_txt);
			}
		});
	}
});