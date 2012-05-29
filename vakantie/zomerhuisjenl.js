//<!-- MP 11.12.2007 -->

function IncludeJavaScriptC(jsFileC)
{
  folderc = 'http://server01.skl1142602.interip.nl/'  
  folderc = folderc + 'Javascript/'
  folderc = folderc + 'Marobi/'
  document.write('<script type="text/javascript" src="' + folderc 
    + jsFileC + '"></script>'); 
}

campagneId1 = '43740';
IncludeJavaScriptC('marobiteller.js');

