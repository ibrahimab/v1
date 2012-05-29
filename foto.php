<?

include("admin/vars.php");

?><HTML>
<HEAD>
 <TITLE><?

echo htmlentities($vars["websitenaam"]);

if($_GET["sw"] and $_GET["sh"]) {
	$maxwidth=round($_GET["sw"]*.8);
	$maxheight=round($_GET["sh"]*.8);
	$imgsize=getimagesize($_GET["f"]);
	$width=$imgsize[0];
	$height=$imgsize[1];
	if($imgsize[0]>$maxwidth or $imgsize[1]>$maxheight) {
		while($width>$maxwidth or $height>$maxheight) {
			$teller++;
			$width=$imgsize[0]-($teller*10);
			$height=$width*$imgsize[1]/$imgsize[0];
			if($teller>5000) break;
		}
	}
	$file=$vars["path"]."thumbnail.php?w=".$width."&h=".$height."&file=".urlencode(ereg_replace(".*pic/cms/","",$_GET["f"]));
} else {
	$file=$_GET["f"];
}

?></TITLE>
<meta name="robots" content="noindex,follow">
 <script language='javascript'>
   var arrTemp=self.location.href.split("?");
   var picUrl = (arrTemp.length>0)?arrTemp[1]:"";
   var NS = (navigator.appName=="Netscape")?true:false;

     function FitPic() {
       iWidth = (NS)?window.innerWidth:document.body.clientWidth;
       iHeight = (NS)?window.innerHeight:document.body.clientHeight;
       iWidth = document.images[0].width - iWidth;
       iHeight = document.images[0].height - iHeight;
       if(iWidth<0) iWidth=0;
       if(iHeight<0) iHeight=0;
       window.resizeBy(iWidth, iHeight);
     };
 </script>
</HEAD>
<BODY onload='FitPic();FitPic();' topmargin="0" marginheight="0" leftmargin="0" marginwidth="0"><IMG name="FullImage" SRC="<?php echo htmlentities($file); ?>" alt=""<?

if($width and $height) {
	echo " width=\"".$width."\" height=\"".$height."\"";
}

?>></BODY></HTML>