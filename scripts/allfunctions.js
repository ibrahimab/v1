
function wt_popupmsg(text) {
	div = $("<div id='wt_popupmsg' style='display:none;position:absolute;padding-top:17px;border-bottom:1px solid #000000;font-size:1.4em;font-style:italic;padding-bottom:17px;text-align:center;top:0px;left:0px;width:100%;background-color:yellow;'>").html(text);
	$("body").append(div);
	setTimeout(function(){
		$("#wt_popupmsg").slideDown();
		setTimeout(function(){
			$("#wt_popupmsg").slideUp('slow',function() {
				$('#wt_popupmsg').remove();
			});
		},3000);
	},200);
}

function wt_popwindowXY(wWidth,wHeight,url,align) {
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

function wt_post_to_url(path, params, method) {
	method = method || "post"; // Set method to post by default, if not specified.

	// The rest of this code assumes you are not using a library.
	// It can be made less wordy if you use one.
	var form = document.createElement("form");
	form.setAttribute("method", method);
	form.setAttribute("action", path);

	for(var key in params) {
		var hiddenField = document.createElement("input");
		hiddenField.setAttribute("type", "hidden");
		hiddenField.setAttribute("name", key);
		hiddenField.setAttribute("value", params[key]);

		form.appendChild(hiddenField);
	}

	document.body.appendChild(form);
	form.submit();
}

function wt_equalHeight(group) {
	var tallest = 0;
	$(group).each(function() {
		var thisHeight = $(this).height();
		if(thisHeight > tallest) {
			tallest = thisHeight;
		}
	});
	$(group).height(tallest);
}

function wt_check_all_checkboxes_tablelist(iam,number) {
    var node_list = document.getElementsByTagName('input');

	var regexptest=new RegExp("delete_checkbox\\["+number+"\\]\\[");

    for (var i = 0; i < node_list.length; i++) {
        var node = node_list[i];

        if (node.getAttribute('type') == 'checkbox' && regexptest.test(node.getAttribute('name'))) {
			if (iam.checked == 1) {
				node.checked = 1;
			} else {
				node.checked = 0;
			}
        }
    }
}
