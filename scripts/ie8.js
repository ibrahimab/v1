
var ie8_teller=0;

var ie8_org_select_width = [];

$(document).ready(function() {

	// ie8-probleem met te smalle pulldowns oplossen

	$('.wtform select').each(function() {

		ie8_teller=ie8_teller+1;
		if(ie8_teller==1) {
//			alert($(this).width());
		}

		if($(this).width()>300) {


			var select_width=$(".wtform input[type=text],.wtform textarea").width();
			if(select_width>0) {
				$(this).width(select_width);
			}

			$(this).css("position","absolute");
			$(this).css("margin-top","-10px");
			$(this).css("z-index","100");
//			$(this).css("display","none");

			$(this).mousedown(function() {

				if(!ie8_org_select_width[$(this).attr("name")]) {
					ie8_org_select_width[$(this).attr("name")]=$(this).data("origWidth");
				}

				if($(this).css("width") != "auto") {

					// $(this).css("z-index","100");
					// $(this).css("position","absolute");
					// $(this).css("margin-top","-5px");

					var width = $(this).width();
					$(this).data("origWidth", $(this).css("width")).css("width", "auto");

					// If the width is now less than before then undo
					if($(this).width() < width) {
						$(this).unbind('mousedown');
						$(this).css("width", $(this).data("origWidth"));
					}
				}
			});

			// Handle blur if the user does not change the value
			$(this).blur(function() {
				$(this).css("width",ie8_org_select_width[$(this).attr("name")]);
			});

			// Handle change of the user does change the value
			$(this).change(function() {
				$(this).css("width",ie8_org_select_width[$(this).attr("name")]);
			});
		}
	});
});
