<?php

include_once("admin/allfunctions.php");

$openfile="pic/cms/".$_GET["file"];
$openfile=str_replace("%2F","/",$openfile);
$openfile=ereg_replace("\.\.","",$openfile);


function ImageCreateFromBMP($filename) {
 //Ouverture du fichier en mode binaire
   if (! $f1 = fopen($filename,"rb")) return FALSE;

 //1 : Chargement des ent?tes FICHIER
   $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
   if ($FILE['file_type'] != 19778) return FALSE;

 //2 : Chargement des ent?tes BMP
   $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
                 '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
                 '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
   $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
   if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
   $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
   $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
   $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
   $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
   $BMP['decal'] = 4-(4*$BMP['decal']);
   if ($BMP['decal'] == 4) $BMP['decal'] = 0;

 //3 : Chargement des couleurs de la palette
   $PALETTE = array();
   if ($BMP['colors'] < 16777216)
   {
    $PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
   }

 //4 : Cr?ation de l'image
   $IMG = fread($f1,$BMP['size_bitmap']);
   $VIDE = chr(0);

   $res = imagecreatetruecolor($BMP['width'],$BMP['height']);
   $P = 0;
   $Y = $BMP['height']-1;
   while ($Y >= 0)
   {
    $X=0;
    while ($X < $BMP['width'])
    {
     if ($BMP['bits_per_pixel'] == 24)
        $COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
     elseif ($BMP['bits_per_pixel'] == 16)
     {
        $COLOR = unpack("n",substr($IMG,$P,2));
        $COLOR[1] = $PALETTE[$COLOR[1]+1];
     }
     elseif ($BMP['bits_per_pixel'] == 8)
     {
        $COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
        $COLOR[1] = $PALETTE[$COLOR[1]+1];
     }
     elseif ($BMP['bits_per_pixel'] == 4)
     {
        $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
        if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
        $COLOR[1] = $PALETTE[$COLOR[1]+1];
     }
     elseif ($BMP['bits_per_pixel'] == 1)
     {
        $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
        if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
        elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
        elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
        elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
        elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
        elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
        elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
        elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
        $COLOR[1] = $PALETTE[$COLOR[1]+1];
     }
     else
        return FALSE;
     imagesetpixel($res,$X,$Y,$COLOR[1]);
     $X++;
     $P += $BMP['bytes_per_pixel'];
    }
    $Y--;
    $P+=$BMP['decal'];
   }

 //Fermeture du fichier
   fclose($f1);

 return $res;
}

if($_GET["lk"]) {
	# Landkaart
	if($origImg = @ImageCreateFromGIF($openfile)) {
		$height_verhouding=ImageSX($origImg)/ImageSY($origImg);

		$newHeight = 100;
		$newWidth = $height_verhouding*$newHeight;

		/* create a blank, new image of the given new height and width */
		$newImg = ImageCreateTrueColor($newWidth,$newHeight);

		/* copy the resized image. Use the ImageSX() and ImageSY functions to get the x and y sizes of the orginal image. */
		ImageCopyResampled($newImg,$origImg,0,0,0,0,$newWidth,$newHeight,ImageSX($origImg),ImageSY($origImg));

		/* create final image and free up the memory */

		if($_GET["test"]) {
			echo $homedir."thumbnails/".$_GET["u"]."-".$_GET["file"];
		} else {
			header("Content-type: image/gif");
		}

		ImageGIF($newImg,'',60);

		#if($_GET["writefile"]) ImageJPEG($newImg,$writefile);
		ImageDestroy($newImg);
	} else {
	#	ImageJPEG($newImg,$tmppic);
		header('Content-Type: image/gif');
		$fp = fopen($openfile, "r");
		fpassthru($fp);
		fclose($fp);
	}
} else {
	# Foto

	unset($savefile);

	$resize=true;
	if($_GET["calcsize"]) {
		$maxwidth=600;
		$maxheight=450;
		if(file_exists($openfile)) {
			$imgsize=getimagesize($openfile);
			$newWidth=$imgsize[0];
			$newHeight=$imgsize[1];
			if($imgsize[0]>$maxwidth or $imgsize[1]>$maxheight) {
				while($newWidth>$maxwidth or $newHeight>$maxheight) {
					$teller++;
					$newWidth=$imgsize[0]-($teller*10);
					$newHeight=$newWidth*$imgsize[1]/$imgsize[0];
					if($teller>100) {
						$newWidth=560;
						$newHeight=420;
						break;
					}
				}
			} else {
				$resize=false;
			}
		} else {
			trigger_error("_notice: file ".$openfile." niet gevonden",E_USER_NOTICE);
			exit;
		}
	}

	# kijken of afbeelding al in cache aanwezig is
	if($_GET["w"] and $_GET["h"]) {
		$cachefile="pic/cms/_imgcache/".intval($_GET["w"])."x".intval($_GET["h"])."-".preg_replace("/\//","-",$_GET["file"]);
	} else {
		$cachefile="pic/cms/_imgcache/".$newWidth."x".$newHeight."-".preg_replace("/\//","-",$_GET["file"]);
	}
	if(file_exists($cachefile) and filemtime($cachefile)==@filemtime($openfile)) {
		if($_GET["cache"]) {
			$cachefile.="?cache=".$_GET["cache"];
			header("Location: ".$cachefile,true,301);
		} else {
			header("Location: ".$cachefile);
		}
		exit;
	} else {
		$savefile=$cachefile;
	}

	if($resize) {
		if($origImg = @ImageCreateFromJPEG($openfile)) {
			$img_okay=true;
		}
	}
	if($img_okay and $resize) {
		if($_GET["w"] and $_GET["h"]) {
			$newWidth=$_GET["w"];
			$newHeight=$_GET["h"];
		} elseif($_GET["calcsize"]) {

		} else {
			$newWidth = 200;
			$newHeight = 150;

			# thumbnail opslaan?
			if(preg_match("/accommodaties_aanvullend\/(.*.jpg)/",$_GET["file"],$regs)) {
				$savefile="pic/cms/accommodaties_aanvullend_tn/".$regs[1];
			}
		}

		if($_GET["test"]) {
			echo $homedir."thumbnails/".$_GET["u"]."-".$_GET["file"];
		} else {
			header("Content-type: image/jpeg");
		}

		wt_create_thumbnail($openfile,$savefile,$newWidth,$newHeight);
		@imagedestroy($origImg);

		if($savefile) {
			$time=@filemtime($openfile);
			if($time) {
				@touch($savefile,$time);
			}
			if($_GET["cache"]) {
				$savefile.="?cache=".$_GET["cache"];
				header("Location: ".$savefile,true,301);
			} else {
				header("Location: ".$savefile);
			}
			exit;
		}
	} elseif(file_exists($openfile)) {
		if($_GET["cache"]) {
			$openfile.="?cache=".$_GET["cache"];
			header("Location: ".$openfile,true,301);
		} else {
			header("Location: ".$openfile);
		}
		exit;
	}
}

?>