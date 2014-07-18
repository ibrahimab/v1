<?php

$cronmap=true;

echo "<html><font size=\"2\" face=\"Verdana\">";
include_once("../admin/allfunctions.php");

$tempxml=simplexml_load_file("betalingen.xml");
$xml=object2array($tempxml);

while(list($key,$value)=each($xml["GLEntries"]["GLEntry"][0]["FinEntryLine"])) {
	if($value["GLAccount"]["@attributes"]["code"]==1300) {
		$teller++;
		echo "<b>Regel ".$teller."</b><br>";
		echo "Datum: ".$value["Date"];
		echo "<br>";
		echo "Reserveringsnummer:  ".wt_he($value["FinReferences"]["YourRef"]);
		echo "<br>";
		echo "Naam:  ".wt_he($value["Debtor"]["Name"]);
		echo "<br>";
		echo "Bedrag:  &euro;&nbsp;".number_format($value["Amount"]["Credit"],2,",",".");
		echo "<br>";
		echo "Banktransactie-id: ".wt_he($value["Payment"]["BankTransactionID"]);
		echo "<br>";
		echo "Omschrijving: ".wt_he($value["Description"]);
		echo "<hr>";
	}
}

function object2array($object)
{
   $return = NULL;

   if(is_array($object))
   {
       foreach($object as $key => $value)
           $return[$key] = object2array($value);
   }
   else
   {
       $var = get_object_vars($object);

       if($var)
       {
           foreach($var as $key => $value)
               $return[$key] = object2array($value);
       }
       else
           return strval($object); // strval and everything is fine
   }

   return $return;
}

echo "</font></html>";

?>