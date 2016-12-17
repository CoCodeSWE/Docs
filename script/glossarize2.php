
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
*/


// eseguire lo script direttamente all'interno della cartella "script"

error_reporting(E_ERROR | E_PARSE); // non vengono stampati i warning

$rev = ('../RR/'); //al cambio di revisione modificare questa variabile
$revisione = 'Revisione dei requisiti';//e questa
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
  '1VI' => 'Verbale_2016-12-10/', // primo verbale interno. 1=primo,V=verbale,I=interno
  '1VE' => 'Verbale_E_2016-12-17/'
);


echo <<< EOF

###  Software sotto licenza MIT ###

______                            _
|  __ \ | by Enrico Ceron        (_) v1.1
| |  \/ | ___  ___ ___  __ _ _ __ _ _______
| | __| |/ _ \/ __/ __|/ _` | '__| |_  / _ \
| |_\ \ | (_) \__ \__ \ (_| | |  | |/ /  __/
 \____/_|\___/|___/___/\__,_|_|  |_/___\___|

  Improved by Mattia Bottaro     v2.0

EOF;

echo <<< EOF
+-----------------------+---------------------+
 $revisione
+-----------------------+---------------------+
|       Documento       |        Esito        |
+-----------------------+---------------------+

EOF;


function glossarizeDoc($path) {
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
  $linesToIgnore = "/^((\\\section)|(\\\subsection)|(\\\subsubsection)|" .
                   "(\\\paragraph)|(\\\subparagraph)|(\\\caption)|(\\\url)|" .
                   "(\\\myparagraph)|(\\\mysubparagraph)|(\\\\texttt)|" .
                   "(\\\\ref)|(\\\label)|(\\\def\\\input@path))/";

  /**
   * Per ogni voce di glossario...
   */
  while (!feof($voci)) {

    $voce = trim(fgets($voci));
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
      if (preg_match("/\b$voce\b/", $line)) {
        if (empty(preg_grep($linesToIgnore, explode("\n", $line)))) {
        //  echo $linesToIgnore." ".$voce."\n";
          shell_exec("sed -r -i '$lineNumber!b;s/(\\\gl\{\<$voce\>\})|(\<$voce\>)/\\\gl\{$voce\}/g' $filename") . "\n";
        //  echo "\033[33;32m>  riga $lineNumber:\tglossarizzato '$voce'\n";
          echo "";
        } else {
          shell_exec("sed -r -i '$lineNumber!b;s/(\\\gl\{\<$voce\>\})/$voce/g' $filename") . "\n";
        //  echo "\033[33;31m>  riga $lineNumber:\tdeglossarizzato '$voce'\n";
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

/**
 * Calcola e stampa la media del Gulpease di tutti i file per ogni documento
 */
 //echo "Glossarizzazione di: \n";
foreach ($docs as $doc => $dir) {
  if (file_exists($rev . $rootE . $dir)) $root = $rootE;
  else
  	if (file_exists($rev . $rootI . $dir)) $root = $rootI;

    // Se la cartella $dir esiste inizializza $gulpease e salva il nome di tutti
    // i file al suo interno in $files
    $gulpease = array();
    $files = scandir($rev . $root . $dir);
    foreach ($files as $file) {
    	// echo "\n$file\n";
      // Di ogni $file, se ha estensione .tex, ne calcola il Gulpease che salva
      // in $gulpease con una push
      if (preg_match('/.tex$/', $file)) {
        $path = $rev . $root . $dir . $file;
        if (file_exists($path)) {
          // qui si glossarizza
            glossarizeDoc($path);
        //  $glossarizzato=true;
        //  echo $path."\n";
        }else $glossarizzato=false;
      }
    }
    if(file_exists($rev.$root.$dir)){
      echo "|        $doc            |    Glossarizzato    |\n";
      echo "+-----------------------+---------------------+\n";
    }else{
      echo "|        $doc            |    Non esistente    |\n";
      echo "+-----------------------+---------------------+\n";
    }
}

echo "\n";
echo "\e[0m\nInfiniTech e Co.Code si sollevano da ogni responsabilità.\n";
echo "\n";

?>
