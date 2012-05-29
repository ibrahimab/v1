<?php
if (!$_SESSION) session_start();

$server="oorl.iwlab.com";
$script="/holiday/bin/mh/APIserver.php"; 

$use_debug=0; 
function RPC_set_server($srv, $script_page, $debug)
{ global $server; $server=$srv; 
  global $script; $script=$script_page; 
  global $use_debug; $use_debug=$debug; 
};

//Libreria di gestione conto IWsmile
//Fatta per poter ricevere pagamenti su carta di credito. 
//Ovviamente questi dati qui sotto sono tarocchi... 

//$_SESSION['MY_ACCOUNT']  = "701001443"; // vecchia      //Da valorizzare con il proprio conto
$_SESSION['MY_ACCOUNT'] = "71004644"; // nuova
$_SESSION['IMAGE_CODE'] = "41598F441CA8E81603DF7FBEAF626C21"; // immagine di iwsmile da visualizzare in alto a sx
//$_SESSION['THIS_PAGE']   = $_SERVER['SCRIPT_NAME']; //Da valorizzare con l'indirizzo pagina corretto
//$_SESSION['IWSMILE_KEY'] = "5617848B0C51A6B64EEEAE7AC63412682C34755582FC236102512DF09A900A84"; // vecchia
$_SESSION['IWSMILE_KEY'] = "5C333A0BD433012A92B9532DD2A5A2734BE4A8F5BFD561C4836B669A2FE523D9"; // nuova

//Una variabile dove c'e' una descrizione breve della transazione conclusa
//valorizzata da payd o error dopo la chiamata
$_SESSION['iwsmile_reply']="";

//Inizia una transazione di pagamento. Vanno passati:
//     - prezzo per il singolo oggetto
//     - quantita' di oggetti da pagare, penso anche con la virgola
//     - nome dell' oggetto, da mostrare al cliente
//     - codice dell' oggetto, che si vedra' nell'email di conferma pagamento
//     - lingua, puo' essere "IT", "EN", "DE", "FR", "ES"
//function iwsmile_pay($amount, $quantity, $itemname, $itemnum, $lang);


//Controlla se la pagina e' stata richiamata al ritorno da una transazione di
//pagamento e se questa e' andata a buon fine.
//Nella stessa pagina, in testa, inserire: 
//    - if (iwsmile_payd())  echo "pagato, grassie..."; 
//    - if (iwsmile_error()) echo "Non pagato.. non ci hai una lira."; 
//    echo $iwsmile_reply;
//    o qualcosa del genere :)
//function iwsmile_payd();
//function iwsmile_error();

//***************************************************************************//
//*                      Da qui il codice.. non toccare                     *//
//***************************************************************************//

//Questa per poter configurare dall'esterno e criptare il tutto. 
function iwsmile_init($account,$key,$timeout)
{ $_SESSION['MY_ACCOUNT']=$account; 
  $_SESSION['IWSMILE_KEY']=$key; 
  $_SESSION['MY_TIMEOUT']=$timeout; 
};

//Paypage serve se voglio mandare la risposta al pagamento su una pagina in particolare. 
//Mi serve per poter mostrare nuovamente la pagina della casa se il pagamento non e' 
//andato a buon fine, senza forzare il form di iwsmile nel rettangolino. 
function iwsmile_pay($amount, $quantity, $itemname, $itemnum, $lang, $paypage='')
{ $quantity= (intval( ($quantity+0)*100 )) / 100;
  $lang=strtoupper($lang); 
  if (($lang != "IT") && ($lang != "DE") && ($lang != "FR") && ($lang != "ES"))
     $lang="EN"; 
  if ($quantity >0) 
  { $qs=$_SERVER['QUERY_STRING'];
    if ($qs == "") $qs="none=";   
    $url_ok  = "http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."?".$qs."&iwsmiletr=1"; 
    $url_bad = "http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."?".$qs."&iwsmiletr=2"; 
    //$url_callback="http://".$_SERVER['SERVER_NAME'].$_SESSION['THIS_PAGE']."?iwsmileback=1"; // vecchia
    $url_callback="http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."?".$qs."&iwsmileback=1";

    if ($paypage!="") 
    { $url_ok  = "$paypage&iwsmiletr=1"; 
      $url_bad = "$paypage&iwsmiletr=2"; 
    };

    $r= '<form id="fpay" action="https://checkout.iwsmile.it/Pagamenti/" method="post">
          <input type="hidden" name="ACCOUNT"       value="'.$_SESSION['MY_ACCOUNT'].'">
          <input type="hidden" name="AMOUNT"        value="'.$amount.'">
          <input type="hidden" name="ITEM_NAME"     value="'.$itemname.'">
          <input type="hidden" name="ITEM_NUMBER"   value="'.$itemnum.'">
          <input type="hidden" name="QUANTITY"      value="'.$quantity.'">
          <input type="hidden" name="NOTE"          value="1">
          <input type="hidden" name="URL_OK"        value="'.$url_ok.'">
          <input type="hidden" name="URL_BAD"       value="'.$url_bad.'">
          <input type="hidden" name="LANG_COUNTRY"  value="'.$lang.'">
          <input type="hidden" name="URL_CALLBACK"  value="'.$url_callback.'">
          <input type="hidden" name="FLAG_ONLY_IWS" value="0">
          <input type="hidden" name="IMAGE_CODE" value="'.$_SESSION['IMAGE_CODE'].'">
          <input type="image" src="http://'.$_SERVER['SERVER_NAME'].'/images/loadingAnimation.gif" border="0" name="submit" alt="Paga adesso">
          </form>';
    return $r; 
  } else return "";  
};

//Controlla se la pagina e' stata richiamata al ritorno da una transazione di
//pagamento e se questa e' andata a buon fine.
//Nella stessa pagina, in testa, inserire: 
//    - if (iwsmile_payd())  echo "pagato, grassie..."; 
//    - if (iwsmile_error()) echo "Non pagato.. non ci hai una lira."; 
//    o qualcosa del genere :)
function iwsmile_payd() 
{ if (intval($_GET['iwsmiletr'] + 0) == 1)
  { $_SESSION['iwsmile_reply']="Transazione numero <b>".$_POST['thx_id']."</b><br>".
                   "Nome <b>".$_POST['payer_name']."</b>, ".
                    "mail <b>".$_POST['payer_email']."</b><br>". 
                   "data <b>".$_POST['payment_date']."</b>, ". 
//                   "dal conto <b>".$_POST['payer_id']."</b><br>". 
//                   "cifra <b>".$_POST['amount ']."</b>, ". 
//                   "per <b>".$_POST['qta']."</b> oggetti<p>".
                   "stato <b>".$_POST['payment_status']."</b><p>";
    //if (iwsmile_wait_confirm($_POST['thx_id']))
      $_SESSION['iwsmile_reply'].="Completamento operazione confermato</b>"; 
      return 1; 
    //else 
    //{ $_SESSION['iwsmile_reply'].="Completamento operazione non confermato (timeout)</b>"; 
    //  return ""; 
    //};
  }; 
};

function iwsmile_error() 
{ if (intval($_GET['iwsmiletr']+0) == 2)
  { $_SESSION['iwsmile_reply']="Errore: <b>Transazione non completata o timeout</b>"; 
    return 1; 
  }; return ""; 
};

function iwsmile_wait_confirm($trnum) //Aspetto la conferma di pagamento 
{ $res="";        //Leggo il file ogni cinque secondi.. portate pazienza
  $ftouch = fopen($_SESSION['MY_INFO_FILE'], 'a'); fclose($ftouch);          //touch 
  for ($i=0; $i<($_SESSION['MY_TIMEOUT']+1); $i=$i+5)
  { $fh = fopen($_SESSION['MY_INFO_FILE'], 'r');
    if ($fh)
    { while ($a=fgets($fh))
      { if (strstr($a, "\r")) $a=substr($a, 0, strlen($a)-1); 
        if (strstr($a, "\n")) $a=substr($a, 0, strlen($a)-1); 
        if ($trnum == $a) { $res=1; $i=$_SESSION['MY_TIMEOUT'] + 1; }; 
      }; fclose($fh); 
    };
    if ($i<($_SESSION['MY_TIMEOUT']+1)) sleep(5); 
  }; return $res; 
};


if ($_GET['iwsmileback']==1) //Sono il processo che gestisce la richiesta di callback. 
{ //mi preparo le info da reinviare al server. 
outlog("doing back check\n"); 
  $ch = curl_init(); 

  $post="thx_id=".$_REQUEST['thx_id']."&amount=".$_REQUEST['amount']."&verify_sign=".$_REQUEST['verify_sign'].
  "&payer_id=".$_REQUEST['payer_id']."&merchant_key=".$_SESSION['IWSMILE_KEY']; 

outlog("post is ".$post."\n"); 

  curl_setopt($ch, CURLOPT_URL, "https://checkout.iwsmile.it/Pagamenti/trx.check"); 
  curl_setopt($ch, CURLOPT_SSL, VERIFYPEER, FALSE); 
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_POST, TRUE);

  $content=curl_exec($ch); 
 
outlog("CURL result is ".$content."\n"); 

  if (strstr($content, "OK")>=0)
    { //Ordine verificato. Salvo le informazioni su un file, possibilmente temporaneo.
outlog("file saved\n");     
      $fw = fopen($_SESSION['MY_INFO_FILE'], 'a');
      fwrite($fw, $_REQUEST['thx_id']."\n"); 
      fclose($fw);     
    }; 
};
function outlog($a)
{  $fl=fopen("/tmp/transazione.log", "a");
   fwrite($fl, $a); 
   fclose($fl); 
};



function error($e) { echo "<ERROR>$e</ERROR>\n"; exit(); };
function inner_xml($x,$tag)
{ //Cerca il tag e se lo trova butta via quanto c'era prima.
  $fnd=""; //Un po' macchinoso ma dovrebbe andare.
  for ($i=0; $x != ""; $i++)
  { $a="<".$tag.">";
    if (substr($x, 0, strlen($a)) == $a) $fnd=substr($x, strlen($a));
    $x=substr($x, 1);
  };
  return $fnd;
};
function inner_text($x) //Prende il testo fino al prossimo tag. Supporta escapes.
{ $r="";
  for ($i=0; $i<strlen($x); $i++)
  { if (substr($x, $i,1) == "<")
    { if (($i!=0) && (substr($x, $i-1,1) != "\\"))
      { $i=strlen($x); } else { $r=substr($r, 0, strlen($r)-1)."<"; };
    } else $r.=substr($x, $i,1);
  };
  echo "INNER TEXT [$r]\n"; 
  return $r;
};
function get_xml($x, $p) //Vedo cosa posso fare per semplificarmi il lavoro.
{ $p=split(",", $p);
  $a=$x;
  for ($i=0; $p[$i]!=""; $i++)
  { $a=inner_xml($x, $p[$i]);
    if ($a=="") error("Wrong XML format.".$p[$i]." not found");
  };
  $r=inner_text($a); return $r;
};
function get_info($function, $pars, $vals) //$pars e' un array, $vals i valori, separati. 
{ global $server,$script;
  if ($pars[0]=="RPC_login")
  { $challenge=file_get_contents("http://".$server.$script."?challenge=1"); 
    if ($challenge =="") return ""; 
    $chap=md5($challenge.$vals[1]); 
  };
  //Qui genero l'xml da inviare al server 
  $start=0; $xm="<REQUEST>\n  <FUNCTION>".$function."</FUNCTION>\n";
  if ($chap) 
  { $xm.="  <AUTH>\n    <CHAP>$chap</CHAP>\n    <USER>".$vals[0]."</USER>\n  </AUTH>\n"; 
    $start=2; 
  };
  $xm.="  <FIELDS>\n"; 
  for ($i=$start; $pars[$i]!=""; $i++) $xm.="    <".$pars[$i].">".$vals[$i]."</".$pars[$i].">\n"; 
  $xm.="  </FIELDS>\n"; 
  $xm.="</REQUEST>\n"; 
  $req= "POST $script HTTP/1.0\r\nHost: ".$server."\r\nContent-Type: text/xml; charset=utf-8\r\nContent-Length: ".strlen($xm)."\r\n\r\n".$xm;

global $use_debug; 
if ($use_debug >0) echo "Request:<br><textarea rows=18 cols=80>$req</textarea><br>"; 
  $fs=fsockopen($server,80);
  if ($fs) 
  { fputs($fs,$req);
    while ($l=fgets($fs)) $reply.=$l; 
  };
if ($use_debug >0) echo "Reply:<br><textarea rows=18 cols=80>$reply</textarea><br>";   

  //.. Qui devo togliere l'header e restituire l'xml. 
  //magari gia' sotto forma di array di array. Fico. 
  $rp=explode("\n", $reply); 
  $a=Array(); 
  $res=Array(); $i=-1; 
  $perf=""; 
  foreach ($rp as $line) 
  { if (strstr($line, "<PERFORMED")!="") $perf="OK"; 
    if (substr($line, 0, 12) == "  <DATA_ROW>") 
    { $a=Array(); $i++;
    };
    if (substr($line, 0, 13) == "  </DATA_ROW>") $res[$i]=$a;  
    if(substr($line,0,5) == "    <")
    { $name = substr($line, 5, strpos($line, ">")-5);
      $val=substr($line, strlen($name)+6);
      $val=substr($val, 0, strlen($val)-(strlen($name)+3)); 
      $a[$name]=$val; 
    };
  };
  if ($perf!="") return "Ok"; 
  if ($i<0) { return ""; } else return $res; 
};


function RPC_get_translations($idtxt,$idlingua)
{ $l=get_info("get_translations", Array("idtxt","idlingua"), Array($idtxt,$idlingua));
  return $l;
};

function RPC_get_languages()
{ $l=get_info("get_languages", Array(""), Array());
  return $l;
};

function RPC_get_images($RPC_login,$RPC_pass,$idoggetti)
{ $l=get_info("get_images", Array("RPC_login","RPC_pass","idoggetti"), Array($RPC_login,$RPC_pass,$idoggetti));
  return $l;
};

function RPC_get_sorted_images($RPC_login,$RPC_pass,$idoggetti)
{ $l=get_info("get_sorted_images", Array("RPC_login","RPC_pass","idoggetti"), Array($RPC_login,$RPC_pass,$idoggetti));
  return $l;
};

function RPC_get_news($RPC_login,$RPC_pass,$lang,$criteria,$date)
{ $l=get_info("get_news", Array("RPC_login","RPC_pass","lang","criteria","date"), Array($RPC_login,$RPC_pass,$lang,$criteria,$date));
  return $l;
};

function RPC_get_old_news($RPC_login,$RPC_pass,$lang,$criteria)
{ $l=get_info("get_old_news", Array("RPC_login","RPC_pass","lang","criteria"), Array($RPC_login,$RPC_pass,$lang,$criteria));
  return $l;
};

function RPC_get_houses($RPC_login,$RPC_pass,$lang,$criteria,$sort, $limita,$limitb)
{ $l=get_info("get_houses", Array("RPC_login","RPC_pass","lang","criteria","sort","limita","limitb"), Array($RPC_login,$RPC_pass,$lang,$criteria,$sort,$limita, $limitb));
  return $l;
};

function RPC_search_houses($RPC_login,$RPC_pass,$lang,$criteria,$sort, $limita,$limitb)
{ $l=get_info("search_houses", Array("RPC_login","RPC_pass","lang","criteria","sort","limita","limitb"), Array($RPC_login,$RPC_pass,$lang,$criteria,$sort,$limita, $limitb));
  return $l;
};

function RPC_get_packets($RPC_login,$RPC_pass,$lang,$criteria)
{ $l=get_info("get_packets", Array("RPC_login","RPC_pass","lang","criteria"), Array($RPC_login,$RPC_pass,$lang,$criteria));
  return $l;
};

function RPC_get_channels($RPC_login,$RPC_pass,$lang)
{ $l=get_info("get_channels", Array("RPC_login","RPC_pass","lang"), Array($RPC_login,$RPC_pass,$lang));
  return $l;
};

function RPC_get_comuniprov()
{ $l=get_info("get_comuniprov", Array(), Array());
  return $l;
};

function RPC_get_prov($comune)
{ $l=get_info("get_prov", Array("comune"), Array($comune));
  return $l;
};

function RPC_get_comunedata($comune)
{ $l=get_info("get_comunedata", Array("comune"), Array($comune));
  return $l;
};

function RPC_get_comunedata_id($id)
{ $l=get_info("get_comunedata_id", Array("id"), Array($id));
  return $l;
};
function RPC_get_subhouses($RPC_login,$RPC_pass,$id)
{ $l=get_info("get_subhouses", Array("RPC_login","RPC_pass","id"), Array($RPC_login,$RPC_pass,$id));
  return $l;
};

function RPC_get_dot($RPC_login,$RPC_pass,$id)
{ $l=get_info("get_dot", Array("RPC_login","RPC_pass","id"), Array($RPC_login,$RPC_pass,$id));
  return $l;
};

function RPC_get_puntiinteresse($idlang)
{ $l=get_info("get_puntiinteresse", Array("idlang"), Array($idlang));
  return $l;
};

function RPC_get_avail_services($RPC_login,$RPC_pass,$lang, $id)
{ $l=get_info("get_avail_services", Array("RPC_login","RPC_pass","lang","id"), Array($RPC_login,$RPC_pass,$lang,$id));
  return $l;
};

function RPC_get_superhouses($RPC_login,$RPC_pass,$id)
{ $l=get_info("get_superhouses", Array("RPC_login","RPC_pass","id"), Array($RPC_login,$RPC_pass,$id));
  return $l;
};

function RPC_get_evaluations($idstruttura,$idservizio)
{ $l=get_info("get_evaluations", Array("idstruttura","idservizio"), Array($idstruttura,$idservizio));
  return $l;
};

function RPC_get_services($RPC_login,$RPC_pass,$lang,$criteria,$sort)
{ $l=get_info("get_services", Array("RPC_login","RPC_pass","lang","criteria","sort"), Array($RPC_login,$RPC_pass,$lang,$criteria,$sort));
  return $l;
};

function RPC_get_offers($RPC_login,$RPC_pass,$lang,$cond,$order)
{ $l=get_info("get_offers", Array("RPC_login","RPC_pass","lang","cond","order"), Array($RPC_login,$RPC_pass,$lang,$cond,$order));
  return $l;
};

function RPC_get_vocipacchetto($idpacchetto)
{ $l=get_info("get_vocipacchetto", Array("idpacchetto"), Array($idpacchetto));
  return $l;
};

function RPC_add_user($RPC_login,$RPC_pass,$user,$pass,$nome,$cognome,$ragsoc,$codfisc,$vat,$indirizzo,$numero,$citta,$stato,$idlingua,$mail,$tel,$cell,$fax)
{ $l=get_info("add_user", Array("RPC_login","RPC_pass","user","pass","nome","cognome","ragsoc","codfisc","vat","indirizzo","numero","citta","stato","idlingua","mail","tel","cell","fax"), Array($RPC_login,$RPC_pass,$user,$pass,$nome,$cognome,$ragsoc,$codfisc,$vat,$indirizzo,$numero,$citta,$stato,$idlingua,$mail,$tel,$cell,$fax));
  return ($l != "");
};

function RPC_login_user($login,$pass)
{ $l=get_info("login_user", Array("login","pass"), Array($login,$pass));
  return $l;
};

function RPC_login_user_mail($mail,$pass)
{ $l=get_info("login_user_mail", Array("mail","pass"), Array($mail,$pass));
  return $l;
};

function RPC_get_user_mail($mail)
{ $l=get_info("get_user_mail", Array("mail"), Array($mail));
  return $l;
};

function RPC_get_camere($id)
{ $l=get_info("get_camere", Array("id"), Array($id));
  return $l;
};

function RPC_get_bagni($id)
{ $l=get_info("get_bagni", Array("id"), Array($id));
  return $l;
};

function RPC_get_comuni_houses()
{ $l=get_info("get_comuni_houses", Array(), Array());
  return $l;
};

function RPC_get_user_info($RPC_login,$RPC_pass,$user)
{ $l=get_info("get_user_info", Array("RPC_login","RPC_pass","user"), Array($RPC_login,$RPC_pass,$user));
  return $l;
};

function RPC_set_user_info($RPC_login,$RPC_pass,$natoil,$codfisc,$tipodocumento,$documento,$ragsoc,$indirizzo,$citta,$cap,$stato,$tel,$fax,$cell,$skype,$user)
{ $l=get_info("set_user_info", Array("RPC_login","RPC_pass","natoil","codfisc","tipodocumento","documento","ragsoc","indirizzo","citta","cap","stato","tel","fax","cell","skype","user"), Array($RPC_login,$RPC_pass,$natoil,$codfisc,$tipodocumento,$documento,$ragsoc,$indirizzo,$citta,$cap,$stato,$tel,$fax,$cell,$skype,$user));
  return ($l != "");
};

function RPC_get_ospitality($lang)
{ $l=get_info("get_ospitality", Array("lang"), Array($lang));
  return $l;
};

function RPC_get_season($date,$idls)
{ $l=get_info("get_season", Array("date","idls"), Array($date,$idls));
  return $l;
};

function RPC_get_costi_sta_gio($idls,$gsett,$date,$ids)
{ $l=get_info("get_costi_sta_gio", Array("idls","gsett","date","ids"), Array($idls,$gsett,$date,$ids));
  return $l;
};

function RPC_get_costi_sta_set($idls,$date,$dateb,$ids)
{ $l=get_info("get_costi_sta_set", Array("idls","date","dateb","ids"), Array($idls,$date,$dateb,$ids));
  return $l;
};

function RPC_get_season_between($date,$dateb,$idls)
{ $l=get_info("get_season_between", Array("date","dateb","idls"), Array($date,$dateb,$idls));
  return $l;
};

function RPC_get_costi_gio($RPC_login,$RPC_pass,$idls,$gsett,$date)
{ $l=get_info("get_costi_gio", Array("RPC_login","RPC_pass","idls","gsett","date"), Array($RPC_login,$RPC_pass,$idls,$gsett,$date));
  return $l;
};

function RPC_get_costi_set($idls,$date,$dateb)
{ $l=get_info("get_costi_set", Array("idls","date","dateb"), Array($idls,$date,$dateb));
  return $l;
};

function RPC_get_booking($idls,$thismon,$nextmon,$now)
{ $l=get_info("get_booking", Array("idls","thismon","nextmon","now"), Array($idls,$thismon,$nextmon,$now));
  return $l;
};

function RPC_get_book_user($RPC_login,$RPC_pass,$user)
{ $l=get_info("get_book_user", Array("RPC_login","RPC_pass","user"), Array($RPC_login,$RPC_pass,$user));
  return $l;
};

function RPC_fix_owner_cost($idb)
{ $l=get_info("fix_owner_cost", Array("idb"), Array($idb));
  return $l;
};

function RPC_get_house_calendar($RPC_login, $RPC_pass, $idhouse, $da_data, $numweeks)
{ $l=get_info("get_house_calendar", Array("RPC_login","RPC_pass","idhouse","da_data","numweeks"), Array($RPC_login, $RPC_pass, $idhouse, $da_data, $numweeks));
  return $l;
};

function RPC_add_book($RPC_login,$RPC_pass,$idls,$tipo,$dal,$al,$scade,$idutente,$persone,$note,$costo,$options,$bk_acconto,$bk_saldo,$acct_pagato,$saldo_pagato,$modopagamento,$valoreacconto,$perseffettive)
{ $l=get_info("add_book", Array("RPC_login","RPC_pass","idls","tipo","dal","al","scade","idutente","persone","note","costo","options","bk_acconto","bk_saldo","acct_pagato","saldo_pagato","modopagamento","valoreacconto","perseffettive"), Array($RPC_login,$RPC_pass,$idls,$tipo,$dal,$al,$scade,$idutente,$persone,$note,$costo,$options,$bk_acconto,$bk_saldo,$acct_pagato,$saldo_pagato,$modopagamento, $valoreacconto, $perseffettive));
  return ($l != "");
};

function RPC_update_book($RPC_login, $RPC_pass, $id, $saldo_pagato, $modopagamentosaldo)
{ $l=get_info("update_book", Array("RPC_login","RPC_pass","id","saldo_pagato","modopagamentosaldo"), Array($RPC_login, $RPC_pass, $id, $saldo_pagato, $modopagamentosaldo)); 
  return ($l != "");
};

function RPC_get_tipistrutture($lang)
{ $l=get_info("get_tipistrutture", Array("lang"), Array($lang));
  return $l;
};

function RPC_get_sorted_video($RPC_login,$RPC_pass,$idoggetti)
{ $l=get_info("get_sorted_video", Array("RPC_login","RPC_pass","idoggetti"), Array($RPC_login,$RPC_pass,$idoggetti));
  return $l;
};

function RPC_get_sorted_pdf($RPC_login,$RPC_pass,$idoggetti)
{ $l=get_info("get_sorted_pdf", Array("RPC_login","RPC_pass","idoggetti"), Array($RPC_login,$RPC_pass,$idoggetti));
  return $l;
};

function RPC_get_stati()
{ $l=get_info("get_stati", Array(), Array());
  return $l;
};

function RPC_get_voucher($RPC_login,$RPC_pass,$idv)
{ $l=get_info("get_voucher", Array("RPC_login","RPC_pass","idv"), Array($RPC_login,$RPC_pass,$idv));
  return $l;
};

function RPC_delete_voucher($RPC_login,$RPC_pass,$idb)
{ $l=get_info("delete_voucher", Array("RPC_login","RPC_pass","idb"), Array($RPC_login,$RPC_pass,$idb));
  return ($l != "");
};

function RPC_add_voucher($RPC_login,$RPC_pass,$id,$numpersona,$nome,$cognome,$statonascita,$comunenascita,$provincianascita,$datanascita,$tipodocumento,$documento,$telefono,$nazione,$comune,$provincia,$indirizzo,$stadoc,$comdoc,$provdoc)
{ $l=get_info("add_voucher", Array("RPC_login","RPC_pass","id","numpersona","nome","cognome","statonascita","comunenascita","provincianascita","datanascita","tipodocumento","documento","telefono","nazione","comune","provincia","indirizzo","stadoc","comdoc","provdoc"), Array($RPC_login,$RPC_pass,$id,$numpersona,$nome,$cognome,$statonascita,$comunenascita,$provincianascita,$datanascita,$tipodocumento,$documento,$telefono,$nazione,$comune,$provincia,$indirizzo,$stadoc,$comdoc,$provdoc));
  return ($l != "");
};

function RPC_set_booking_done($idbook)
{ $l=get_info("set_booking_done", Array("idbook"), Array($idbook));
  return ($l != "");
};

function RPC_set_feedback_done($RPC_login,$RPC_pass,$idbook)
{ $l=get_info("set_feedback_done", Array("RPC_login","RPC_pass","idbook"), Array($RPC_login,$RPC_pass,$idbook));
  return ($l != "");
};

function RPC_set_house_feedback($RPC_login,$RPC_pass,$commento,$voto,$data,$user,$idstruttura)
{ $l=get_info("set_house_feedback", Array("RPC_login","RPC_pass","commento","voto","data","user","idstruttura"), Array($RPC_login,$RPC_pass,$commento,$voto,$data,$user,$idstruttura));
  return ($l != "");
};

function RPC_add_house_pref($user,$id,$data)
{ $l=get_info("add_house_pref", Array("user","id","data"), Array($user,$id,$data));
  return ($l != "");
};

function RPC_add_comune_pref($user,$id,$data)
{ $l=get_info("add_comune_pref", Array("user","id","data"), Array($user,$id,$data));
  return ($l != "");
};

function RPC_add_offerta_pref($user,$id,$data)
{ $l=get_info("add_offerta_pref", Array("user","id","data"), Array($user,$id,$data));
  return ($l != "");
};

function RPC_add_servizio_pref($user,$id,$data)
{ $l=get_info("add_servizio_pref", Array("user","id","data"), Array($user,$id,$data));
  return ($l != "");
};

function RPC_get_user_pref($RPC_login,$RPC_pass,$user)
{ $l=get_info("get_user_pref", Array("RPC_login","RPC_pass","user"), Array($RPC_login,$RPC_pass,$user));
  return $l;
};

function RPC_del_user_pref($RPC_login,$RPC_pass,$user,$tipo,$id)
{ $l=get_info("del_user_pref", Array("RPC_login","RPC_pass","user","tipo","id"), Array($RPC_login,$RPC_pass,$user,$tipo,$id));
  return ($l != "");
};

function RPC_get_lastminute($now,$maxlm)
{ $l=get_info("get_lastminute", Array("now","maxlm"), Array($now,$maxlm));
  return $l;
};

function RPC_get_listino($RPC_login,$RPC_pass,$idls)
{ $l=get_info("get_listino", Array("RPC_login","RPC_pass","idls"), Array($RPC_login,$RPC_pass,$idls));
  return $l;
};

function RPC_get_rand_house()
{ $l=get_info("get_rand_house", Array(), Array());
  return $l;
};

function RPC_get_user_pass_mail($RPC_login,$RPC_pass,$mail)
{ $l=get_info("get_user_pass_mail", Array("RPC_login","RPC_pass","mail"), Array($RPC_login,$RPC_pass,$mail));
  return $l;
};

function RPC_get_low_season($idls)
{ $l=get_info("get_low_season", Array("idls"), Array($idls));
  return $l;
};

function RPC_set_user_expire($exptime,$user)
{ $l=get_info("set_user_expire", Array("exptime","user"), Array($exptime,$user));
  return ($l != "");
};

function RPC_set_flags($RPC_login,$RPC_pass,$flags,$username)
{ $l=get_info("set_flags", Array("RPC_login","RPC_pass","flags","username"), Array($RPC_login,$RPC_pass,$flags,$username));
  return ($l != "");
};

function RPC_get_expired_options($ieri)
{ $l=get_info("get_expired_options", Array("ieri"), Array($ieri));
  return $l;
};

function RPC_get_all_bookings($RPC_login,$RPC_pass,$cond)
{ $l=get_info("get_all_bookings", Array("RPC_login","RPC_pass","cond"), Array($RPC_login,$RPC_pass,$cond));
  return $l;
};

function RPC_add_message($RPC_login,$RPC_pass,$titolo,$testo,$destinatario,$data,$previous)
{ $l=get_info("add_message", Array("RPC_login","RPC_pass","titolo","testo","destinatario","data","previous"), Array($RPC_login,$RPC_pass,$titolo,$testo,$destinatario,$data,$previous));
  return ($l != "");
};

function RPC_read_messages($RPC_login,$RPC_pass,$limita,$limitb)
{ $l=get_info("read_messages", Array("RPC_login","RPC_pass","limita","limitb"), Array($RPC_login,$RPC_pass,$limita,$limitb));
  return $l;
};

function RPC_set_message_read($RPC_login,$RPC_pass,$letto,$idmgs)
{ $l=get_info("set_message_read", Array("RPC_login","RPC_pass","letto","idmgs"), Array($RPC_login,$RPC_pass,$letto,$idmgs));
  return ($l != "");
};

function RPC_delete_message($RPC_login,$RPC_pass,$idmsg)
{ $l=get_info("delete_message", Array("RPC_login","RPC_pass","idmsg"), Array($RPC_login,$RPC_pass,$idmsg));
  return ($l != "");
};

function RPC_get_message($RPC_login,$RPC_pass,$idmsg)
{ $l=get_info("get_message", Array("RPC_login","RPC_pass","idmsg"), Array($RPC_login,$RPC_pass,$idmsg));
  return $l;
};

function RPC_delete_expired_option($id)
{ $l=get_info("delete_expired_option", Array("id"), Array($id));
  return ($l != "");
};

function RPC_get_all_prov()
{ $l=get_info("get_all_prov", Array(), Array());
  return $l;
};

function RPC_get_comuni_provincia($prov)
{ $l=get_info("get_comuni_provincia", Array("prov"), Array($prov));
  return $l;
};

function RPC_get_service_details($RPC_login,$RPC_pass,$lang,$id)
{ $l=get_info("get_service_details", Array("RPC_login","RPC_pass","lang","id"), Array($RPC_login, $RPC_pass, $lang, $id));
  return $l;
};

function RPC_get_service_option_prices($idls)
{ $l=get_info("get_service_option_prices", Array("idls"), Array($idls)); 
  return $l; 
};
function RPC_add_booking_service($RPC_login,$RPC_pass,$idls,$user,$data,$quantity,$opt1,$opt2,$cost)
{ $l=get_info("add_booking_service", Array("RPC_login","RPC_pass","idls","user","data","quantity","opt1","opt2","cost"), Array($RPC_login,$RPC_pass,$idls,$user,$data,$quantity,$opt1,$opt2,$cost));
  return ($l != "");
};

function RPC_del_booking_service($RPC_login,$RPC_pass,$id)
{ $l=get_info("del_booking_service", Array("RPC_login","RPC_pass","id"), Array($RPC_login,$RPC_pass,$id));
  return ($l != "");
};
function RPC_get_booking_service($RPC_login, $RPC_pass, $user, $data) 
{ $l=get_info("get_booking_service", Array("RPC_login","RPC_pass","user","data"), Array($RPC_login,$RPC_pass,$user,$data)); 
  return $l;
};

function RPC_get_service_quantity($idls,$date)
{ $l=get_info("get_service_quantity", Array("idls","date"), Array($idls,$date));
  return $l;
};

function RPC_get_lastminute_house($idstruct,$now,$maxlm)
{ $l=get_info("get_lastminute_house", Array("idstruct","now","maxlm"), Array($idstruct,$now,$maxlm));
  return $l;
};

function RPC_get_luoghi_vicini($lang, $lat, $lon)
{ $l=get_info("get_luoghi_vicini", Array("lang","lat","lon"), Array($lang,$lat,$lon));
  return $l;
};

function RPC_get_all_comuni($name)
{ $l=get_info("get_all_comuni", Array("name"), Array($name));
  return $l;
};

function RPC_get_booking_type($idls)
{ $l=get_info("get_booking_type", Array("idls"), Array($idls));
  return $l;
};

function RPC_get_owner_cost($idbk)
{ $l=get_info("get_owner_cost", Array("idbk"), Array($idbk));
  return $l;
};

$APIUSER="chaletmh";
$APIPASS="chalet11";



RPC_set_server("oorl.iwlab.com","/holiday/bin/mh/APIserver.php", 0); //(*1)


 $condiction="1";              //(*2)
 $order="comune";                //(*3)
 $limit0 = 0; $limit1 = 100000;    //(*4)
 $a=RPC_get_houses($APIUSER,$APIPASS, 2, $condiction, $order, $limit0, $limit1);
 if (is_array($a)) {
	foreach ($a as $v) {
#		echo "<pre>";
#		var_dump($v);
#		echo "House: ".$v['nome']."<br>".$v['testo']."<br><br>";
		
		echo htmlentities(($v['comune'] ? $v['comune'] : "onbekende plaats")." - ".$v['nome']." - ".$v['posti'])." pers - XML-typecode: ".$v['id']."<br>";
		if($id_gehad[intval($v['id'])]) {
			echo "FOUT!!!<br>";
		}
		$id_gehad[intval($v['id'])]=true;
	}
 } else {
 	echo "No houses";
 }

exit;

include("../admin/allfunctions.php");
#$a=RPC_get_season_between("2011-03-27","2011-04-01","76");
#echo wt_dump($a);

function calcolacostiset($idls, $date, $persone) {
	$rs=Array(0,0,0,0,0,0);
    //Calcolo la seconda data..
    $ts=strtotime($date) + 3600*24*6 + 3600*2;
    $dateb=date("Y-m-d", $ts);

    //Vedo se la data cade in una stagione.. mi puo' servire
    //$ids=fastquery("select dz_nome from stagioni where da_data <='$date' and a_data >='$dateb'");
    $a=RPC_get_season($date, $idls);
    //$a=RPC_get_season_between($date, $dateb, $idls);
    if (is_Array($a)) {$ll=$a[0]; $ids=$ll['dz_nome']; };
    if ($ids<1) $ids=0;

    //Pesco dal listino tutti i costi applicabili
    $a=RPC_get_costi_sta_set($idls, $date, $dateb, $ids);
    
#    echo wt_dump($a);
    echo $idls." - ".$date." - ".$dateb." ".$ids."<br>";
    
    //$l="select costo, guadagno, guadagnoriv, costogio, guadagnogio, guadagnorivgio,
    //  obbligatorio, apersona from listini where idls='$idls'
    //  and (da_data='' or da_data<='$date') and ( a_data='' or  a_data>='$dateb')
    //  and (idstagione=0 or idstagione='$ids');";
    //$res=mysql_query($l);
    if (is_array($a))
    { //Singolo costo, l'applicazione dipdende dai flags
      foreach ($a as $l)
      { $cost=$l['costo'];       if ($l['apersona']>0) $cost=$cost * $persone;
        $gain=$l['guadagno'];    if ($l['apersona']>0) $gain=$gain * $persone;
        $griv=$l['guadagnoriv']; if ($l['apersona']>0) $griv=$griv * $persone;
        if ($l['obbligatorio']>0)
        { $rs[0]=$rs[0]+$cost;
          $rs[2]=$rs[2]+$cost+$gain;
          $rs[4]=$rs[4]+$cost+$griv;
        };
        $rs[1]=$rs[1]+$cost;
        $rs[3]=$rs[3]+$cost+$gain;
        $rs[5]=$rs[5]+$cost+$griv;
      };
    };
    $ra[]=$rs[2]; $ra[]=$rs[3]; $ra[]=$rs[4]; $ra[]=$rs[5]; return $ra;
  };
 
 
 
 


$a=calcolacostiset("100","2011-06-25",0);
echo wt_dump($a);
#function calcolacostiset($idls, $date, $persone)
exit;

RPC_set_server("oorl.iwlab.com","/holiday/bin/mh/APIserver.php", 0); //(*1)

 $condiction="1";              //(*2)
 $order="nome";                //(*3)
 $limit0 = 0; $limit1 = 4;     //(*4)
 $a=RPC_get_houses($APIUSER,$APIPASS, 2, $condiction, $order, $limit0, $limit1);
 if (is_array($a))
 { foreach ($a as $v)
   { echo "House: ".$v['nome']."<br>".$v['testo']."<br>";

     $img=RPC_get_sorted_images($APIUSER, $APIPASS, $v['idimmagini']);
     if (is_array($img))
     { echo "images: ";
       foreach ($img as $image)
         echo "<img src=\"http://new.marcheholiday.it/holiday/bin/mh/imagecutter.php?im=".$image['filename']."&h=30&w=45\">";
       echo "<br>";
     };
     //$pdf=RPC_get_sorted_pdf($APIUSER, $APIPASS, $v['idimmagini']); ...
     echo "<hr>";
   }
 } else echo "No houses"; 
 
 

?>