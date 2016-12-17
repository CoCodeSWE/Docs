
<?php
/*
  modifiche script 2016-15-12: Mattia Bottaro - Co.code:
  script modificato con il repository di Co.Code

	modifiche script: Viviana Alessio - Beacon Strips - 2016:
	lo script è ora compatibile con la struttura del repository del team Beacon strips.

	creazione script: Enrico Ceron - Infinitech - 2015
*/


// eseguire lo script direttamente all'interno della cartella "script"
$rev = ('../RR/'); //al cambio di revisione modificare questa variabile
$revisione = 'RR';//e questa
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
  'AdR' => 'AnalisiDeiRequisiti'
);


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
          shell_exec("sed -r -i '$lineNumber!b;s/(\\\gl\{\<$voce\>\})|(\<$voce\>)/\\\gl\{$voce\}/g' $filename") . "\n";
        //  echo "\033[33;32m>  riga $lineNumber:\tglossarizzato '$voce'\n";
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
      echo "|         $doc          |    Glossarizzato     |\n";
      echo "+----------------------+----------------------+\n";
    }else{
      echo "|          $doc         |    Non esistente     |\n";
      echo "+----------------------+----------------------+\n";
    }
    /*
    if ($glossarizzato) {
      echo "| $doc        | Glossarizzato |\n";
      echo "+-----------+--------+------------------+\n";
    }
    else {
      echo "| $doc        | Nessun file o directory |\n";
      echo "+-----------+--------+------------------+\n";
    }
    */
}

echo "\n";

?>
