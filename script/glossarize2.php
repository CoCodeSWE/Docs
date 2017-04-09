
<?php
/*
* Autore: InfiniTech
* Data creazione: 2015-04-12
* E-mail: info.infinitech@gmail.com
*
* Questo file è proprietà del gruppo InfiniTech e viene rilasciato sotto licenza MIT.
*
* Diario delle modifiche:
* 2016-04-02 - Adattamento script alla struttura del repository del team Beacon Strips
* 				Viviana Alessio
* 2015-05-13 - Aggiunta deglossarizzazione delle $linesToIgnore - Enrico Ceron
* 2015-04-18 - Creazione dello script - Enrico Ceron
*
* Diaro delle modifiche:
* 2016-12-12 - adattamento dello script al gruppo Co.Code, Mattia Bottaro
* 2017-12-17 - pesanti modifiche effettuate da Mattia Bottaro del gruppo Co.Code(formatosi
  per il progetto di SWE dell'uniPD) -> UPDATE alla versione 2.0 by Mattia Bottaro
  Nelle versioni 1.x lo script funzionava solo per un file, dato in input. Quindi per
  glossarizzare n files bisognava eseguire n volte lo script.
  Ora, in base alla organizzazione del repository di Co.Code, lo script cerca tutti i file .tex
  e li glossarizza.
  2017-03-04 Ora lo script glossarizza solo la prima occorrenza di ogni parola. Inoltre permette la deglossarizzazione.
  Lo script ora può essere eseguito in ambienti diversi da Linux. -> UPDATE alla versione 3.0 by Mattia Bottaro
  Per Glossarizzare, dare da terminale php glossarize2.php
  Per Deglossarizzare, dare php glossarize2.php -d
*/


// eseguire lo script direttamente all'interno della cartella "script"

error_reporting(E_ERROR | E_PARSE); // non vengono stampati i warning

$rev = ('../RP/'); //al cambio di revisione modificare questa variabile
$revisione = 'Revisione di progettazione';//e questa
$glossarizzato=false;
$rootE = 'Esterni/';
$rootI = 'Interni/';
/*
 * Elenco delle directory dei documenti
 */

$docs = array(
  'Glo' => 'Glossario/',
  'NdP' => 'NormeDiProgetto/',
  'SdF' => 'StudioDiFattibilita/',
  'PdP' => 'PianoDiProgetto/',
  'AdR' => 'AnalisiDeiRequisiti/',
/* '1VI' => 'Verbale_I_2016-12-10/', // primo verbale interno. 1=primo,V=verbale,I=interno
  '2VI' => 'Verbale_I_2016-12-19',
  '1VE' => 'Verbale_E_2016-12-17/', */
  'PdQ' => 'PianoDiQualifica/',
  'SDK' => 'AnalisiSDK/',
  'DdP' => 'DefinizioneDiProdotto/'
  //'LdP' => 'LetteraDiPresentazione/'
);
echo <<< EOF

###  Software sotto licenza MIT ###

______                            _
|  __ \ | by Enrico Ceron        (_) v1.1
| |  \/ | ___  ___ ___  __ _ _ __ _ _______
| | __| |/ _ \/ __/ __|/ _' | '__| |_  / _ \
| |_\ \ | (_) \__ \__ \ (_| | |  | |/ /  __/
 \____/_|\___/|___/___/\__,_|_|  |_/___\___|

  Improved by Mattia Bottaro     v3.0

EOF;

echo <<< EOF
+-----------------------+---------------------+
 $revisione
+-----------------------+---------------------+
|       Documento       |        Esito        |
+-----------------------+---------------------+

EOF;


function glossarizeDoc($path) {
  //$var_dump($argv);
  if (isset($path)) {
    $docSign = $path;  /*
    if (file_exists($docSign)) {
      echo "È stato scelto di glossarizzare il documento <" . $path . ">...\n\n";
    } else {
      echo "File inesistente! Glossarizzazione abortita.\n";
      exit;
    }
    */
  }

  /**
   * Apertura del documento e dell'elenco delle voci di glossario
   */
  $filename = $path;
  $file = fopen($filename, 'r');
  $voci = fopen('terminiGlossario.txt', 'r');

  /**
   * Regex delle righe da ignorare. Sono ignorati i titoli di sezioni
   * e sotto-sezioni, paragrafi e sotto-paragrafi, etichette e riferimenti,
   * didascalie, URL e testo preformattato.
   */
   $linesToIgnore = "/^((\\\section)|(\\\subsection)|(\\\subsubsection)|(\\\parola)|(\\\url)|(\\\modifica)|" .
                    "(\\\paragraph)|(\\\subparagraph)|(\\\caption)|(\\\url)|(\\\parola)|(\\\modifica)|" .
                    "(\\\myparagraph)|(\\\mysubparagraph)|(\\\\texttt)|(\\\parola)|(\\\url)|(\\\modifica)|" .
                    "(\\\\ref)|(\\\label)|(\\\def\\\input@path))/";

  /**
   * Per ogni voce di glossario...
   */
  while (!feof($voci)) {
    $glo_or_deglo=$_SERVER[ "argv" ][1];
    $voce =trim(fgets($voci));
    $lineNumber = 0;
    rewind($file);

    /**
     * ...scorre il documento in cerca di essa...
     */
    while (!feof($file)) {

      $line = fgets($file);
      $lineNumber++;

      /**
       * ...e la (de)glossarizza!
       */
      if (preg_match("/\b$voce\b/", $line) && $voce!="") {
        if (empty(preg_grep($linesToIgnore, explode("\n", $line)))) { // glo
          //echo $path." ".$voce."\n";

          if($voce=="casi d'uso" || $voce=="Casi d'uso"){
            $Vvoce=$voce;
            $file_contents = file_get_contents($filename);
            $file_contents = str_replace($Vvoce,"casi duso",$file_contents);
            file_put_contents($filename,$file_contents);
            $voce="casi duso";
          }
          ///* Attivare qui per GLOSSARIZZARE ----

          if($glo_or_deglo!="-d"){
              $file_contents = file_get_contents($filename);
              $file_contents = preg_replace("/$voce/","\gl{".$voce."}",$file_contents,1);
              file_put_contents($filename,$file_contents);
          }
          //-----*/
          //if(preg_match("/\\\\gl{".$voce."}/",$file_contents)) echo $voce."\n";

          // Attivare qui per DEGLOSSARIZZARE, togliere il break sotto.
          // ----.-----------------------------
          //echo "[".$argv[1]."]\n";
          else{
              $file_contents = file_get_contents($filename);
              $file_contents = str_replace("\gl{".$voce."}","$voce",$file_contents);
              file_put_contents($filename,$file_contents);
          }
          //*/
          // -----------------------------------------
          /*
           * $test= "prodotto prodotto prodotto Prodotto progetto progetto progetto";
             $test=preg_replace("/Prodotto/i", " gl{prodotto } ", $test, 1);
             echo $test;
           */
          //shell_exec("sed -r -i '$lineNumber!b;s/(\\\gl\{\<$voce\>\})|(\<$voce\>)/\\\gl\{$voce\}/g' $filename") . "\n";
          if($voce=="casi duso"){
            $file_contents = file_get_contents($filename);
            $file_contents = str_replace("casi duso",$Vvoce,$file_contents);
            file_put_contents($filename,$file_contents);
            $voce=$Vvoce;
          }
          if($glo_or_deglo!="-d") break;
        //  echo "\033[33;32m>  riga $lineNumber:\tglossarizzato '$voce'\n";
          //echo "";
        } else { // deglo
        //  echo $linesToIgnore." ".$voce."\n";
        //  shell_exec("sed -r -i '$lineNumber!b;s/(\\\gl\{\<$voce\>\})/$voce/g' $filename") . "\n";
      //    echo $path." \n";
        }
      }
    }
  }

  //echo "\e[0m\nGlossarizzazione di <" . $filename . "> terminata con (in)successo!\n";
  //echo "\e[0m\nInfiniTech si solleva da ogni responsabilità.\n";

  fclose($file);

}

/**
 * Calcola la media dei valori all'interno di un array
 */
function median($array) {
  $sum = 0;
  foreach ($array as $val) {
    $sum += $val;
  }
  return $sum/sizeof($array);
}


 //echo "Glossarizzazione di: \n";
foreach ($docs as $doc => $dir) {
  if (file_exists($rev . $rootE . $dir)) $root = $rootE;
  else
  	if (file_exists($rev . $rootI . $dir)) $root = $rootI;
    $files = scandir($rev . $root . $dir);
    foreach ($files as $file) {
    	// echo "\n$file\n";
      if (preg_match('/.tex$/', $file)) {
        $path = $rev . $root . $dir . $file;
        if (file_exists($path)) {
          // qui si glossarizza
            $correggilatex = file_get_contents($path);
            $correggilatex = str_replace("\\\\gl{LaTeX}","\\LaTeX{}",$correggilatex);
            file_put_contents($path,$correggilatex);
            glossarizeDoc($path);
        //  $glossarizzato=true;
        //  echo $path."\n";
      }// else $glossarizzato=false;
      }
    }
    if($_SERVER[ "argv" ][1]!="-d") $phrases="Glossarizzato";
    else $phrases="DE-Glossarizzato";
    if(file_exists($rev.$root.$dir)){
      echo "|        $doc            |    \033[33;32m $phrases";echo"\e[0m   |   \n";
      echo "+-----------------------+---------------------+\n";
    }else{
      echo "|        $doc            |    \033[33;31m Non esistente";echo"\e[0m   |    \n";
      echo "+-----------------------+---------------------+\n";
    }
}

echo "\n";
echo "\e[0m\nInfiniTech e Co.Code si sollevano da ogni responsabilità.\n";
echo "\n";

?>
