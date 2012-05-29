
$(document).ready(function() {

	$(".boekingen_kop").click(function () {
		if($(this).children("img:first").attr("src").indexOf("plusicon")>0) {
			$(this).children("img:first").attr("src",$(this).children("img:first").attr("src").replace("plusicon","minicon"));
		} else {
			$(this).children("img:first").attr("src",$(this).children("img:first").attr("src").replace("minicon","plusicon"));		
		}
		$('#'+$(this).attr("rel")).slideToggle("fast",function() {

		});
		return false;
	});
});
