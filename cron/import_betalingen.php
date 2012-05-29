<?php

$cronmap=true;

echo "<html><font size=\"2\" face=\"Verdana\">";
include("../admin/allfunctions.php");

$tempxml=simplexml_load_file("betalingen.xml");
$xml=object2array($tempxml);

while(list($key,$value)=each($xml["GLEntries"]["GLEntry"][0]["FinEntryLine"])) {
	if($value["GLAccount"]["@attributes"]["code"]==1300) {
		$teller++;
		echo "<b>Regel ".$teller."</b><br>";
		echo "Datum: ".$value["Date"];
		echo "<br>";
		echo "Reserveringsnummer:  ".htmlentities($value["FinReferences"]["YourRef"]);
		echo "<br>";
		echo "Naam:  ".htmlentities($value["Debtor"]["Name"]);
		echo "<br>";
		echo "Bedrag:  &euro;&nbsp;".number_format($value["Amount"]["Credit"],2,",",".");
		echo "<br>";
		echo "Banktransactie-id: ".htmlentities($value["Payment"]["BankTransactionID"]);
		echo "<br>";
		echo "Omschrijving: ".htmlentities($value["Description"]);
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