<?php 

# Twitter-cache aanmaken

#bij een aanrequest blokkeert twitter de aanvragem. de hieronder staande code zorgt ervoor dat er geen warnings op de homepage komen te staan.

set_time_limit(0);
if($_SERVER["HTTP_HOST"]) {
	$unixdir="../";
	$tmpdir="/tmp/";
} else {
	$unixdir="/home/sites/chalet.nl/html/";
	$tmpdir="/home/sites/chalet.nl/html/tmp/";
}

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);
$username=array();
$username[0]='Zomerhuisje';
$username[1]='Italissima';
$username[2]='ChaletNL';
$format='xml';
foreach($username as $userAccount){
	$tweet=simplexml_load_file("http://api.twitter.com/1/statuses/user_timeline/{$userAccount}.{$format}");
	if($tweet->status[0]->text!=""){
		$bericht="";
		$berichtNext="";
		$berichtNextNext="";
		$bericht1=explode(" ",$tweet->status[0]->text);
		$bericht2=explode(" ",$tweet->status[1]->text);
		$bericht3=explode(" ",$tweet->status[2]->text);
		for($a=0; $a<count($bericht1);$a++){
			if($userAccount=='Zomerhuisje'){
				$imgSrc="https://si0.twimg.com/profile_images/1388402637/ZOMERHUISJE_NL_vierkant.jpg";
				$backColor="#cfbcd8";
				$naam="Zomerhuisje.nl";
				$kopColor="#5F227B";
			}
			elseif($userAccount=='ChaletNL'){
				$imgSrc="http://www.chalet.nl/pic/logo_chalet.gif?c=1";
				$backColor="#d5e1f9";
				$naam="Chalet.nl";
				$kopColor="#D40139";
			}
			elseif($userAccount=='Italissima'){
				$backColor="#e0d1cc";
				$kopColor="#D40139";
			}
			if(substr($bericht1[$a],0,4)=="http"){
				$bericht.=htmlentities("<BR><a style=\"text-decoration:underline;\" href=");
				$bericht.=htmlentities($bericht1[$a],ENT_COMPAT, "ISO-8859-1");
				$bericht.=htmlentities(" target=\"_blank\">");
				$bericht.=htmlentities($bericht1[$a],ENT_COMPAT, "ISO-8859-1");
				$bericht.=htmlentities("</a>");
			}
			else{
				$bericht.=" ".htmlentities($bericht1[$a]);
			}
		}
		for($a=0; $a<count($bericht2);$a++){
			if(substr($bericht2[$a],0,4)=="http"){
				$berichtNext.=htmlentities("<BR><a style=\"text-decoration:underline;\" href=");
				$berichtNext.=htmlentities($bericht2[$a]);
				$berichtNext.=htmlentities(" target=\"_blank\">");
				$berichtNext.=htmlentities($bericht2[$a]);
				$berichtNext.=htmlentities("</a>");
			}
			else{
				$berichtNext.=" ".htmlentities($bericht2[$a]);
			}
		}
		for($a=0; $a<count($bericht3);$a++){
			if(substr($bericht3[$a],0,4)=="http"){
				$berichtNextNext.=htmlentities("<BR><a style=\"text-decoration:underline;\" href=");
				$berichtNextNext.=htmlentities($bericht3[$a]);
				$berichtNextNext.=htmlentities(" target=\"_blank\">");
				$berichtNextNext.=htmlentities($bericht3[$a]);
				$berichtNextNext.=htmlentities("</a>");
			}
			else{
				$berichtNextNext.=" ".htmlentities($bericht3[$a]);
			}
		}
		if($userAccount!='Italissima'){
			$content="<BR>";
			$content.="<div style=\"background-color:#cfbcd8; width:170px;\"><table id=\"hoofdpagina_nieuwsbrief\" cellspacing=\"2\" style=\"background-color:".$backColor."; padding:5px;\">";
			$content.="<tr><td font-color=\"".$kopColor."\" class=\"kop\" colspan=\"2\">".$naam." op twitter</td></tr>";
			$content.="<tr><td><a href=\"https://twitter.com/intent/user?screen_name=".$userAccount."\" target=\"_blank\">";
			$content.="<img src=\"".$imgSrc."\" width=\"50\" height=\"45\" border=\"0\"></a></td>";
			$content.="<td><a style=\"text-decoration:none;\" href=\"https://twitter.com/intent/user?screen_name=".$userAccount."\" target=\"_blank\">".$userAccount.":</a></td></tr><tr><td></td><td></td></tr>";
			$content.="<tr><td valign=\"top\" style=\"font-size:11px;\" colspan=\"2\">".htmlspecialchars_decode($bericht)."<BR><BR></td></tr>";
			$content.="<tr><td valign=\"top\" style=\"font-size:11px;\" colspan=\"2\">".htmlspecialchars_decode($berichtNext)."<BR><BR></td></tr>";
			$content.="<tr><td valign=\"top\" style=\"font-size:11px;\" colspan=\"2\">".htmlspecialchars_decode($berichtNextNext)."<BR><BR></td>";
			$content.="</table></div>";

		}
		else{
			$content="<br><table cellspacing=\"0\" style=\"background-color:#e0d1cc; font-family: Verdana, Arial, Helvetica, sans-serif; padding-left:25px;padding-top:5px; padding-bottom:5px; padding-right:25px; width:580px;\">
					<tr><td style=\"color:#661700; font-size:14px;\" colspan=\"2\"><a style=\"text-decoration:none;\" href=\"https://twitter.com/intent/user?screen_name=Italissima\" target=\"_blank\">Italissima op twitter</a></td></tr>
					<tr><td valign=\"top\" colspan=\"2\" style=\"font-size:11px;\">".htmlspecialchars_decode($bericht)."<hr></td></tr>
					<tr><td valign=\"top\" colspan=\"2\" style=\"font-size:11px;\">".htmlspecialchars_decode($berichtNext)."<hr></td></tr>
					<tr><td valign=\"top\" colspan=\"2\" style=\"font-size:11px;\">".htmlspecialchars_decode($berichtNextNext)."</td>
					</table>";
		}
		$toWrite=$unixdir."cache/twitter".$userAccount.".html";
		$handle=fopen($toWrite,'w') or die('Cannot open file:  '.$toWrite);
		$data=$content;
		fwrite($handle, $data);
		$content="";
		echo "success\n";	
	}
}
?>