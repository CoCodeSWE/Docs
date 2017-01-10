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
$revisione = 'Revisione dei requisiti';//e questa

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
   '1VI' => 'Verbale_I_2016-12-10/', // primo verbale interno. 1=primo,V=verbale,I=interno
   '2VI' => 'Verbale_I_2016-12-19/',
   '1VE' => 'Verbale_E_2016-12-17/',
   'PdQ' => 'PianoDiQualifica/',
   'SDK' => 'AnalisiSDK/',
   'LdP' => 'LetteraDiPresentazione/'
 );

error_reporting(E_ERROR | E_PARSE); // non vengono stampati i warning

echo <<< EOF
+---------------------------------
    $revisione
+------------+--------------+---------+
| Documento | Valore       | Esito    |
+-----------+--------------+----------+

EOF;

/**
 * Calcola il Gulpease di un singolo file al percorso $path
 */
function gulpease($path) {
  $file = file_get_contents($path);
  $frasi = substr_count($file, '.');
  $frasi += substr_count($file, ';');
  $parole = str_word_count($file);
  $lettere = strlen(preg_replace('/[^a-zA-Z0-9]/', '', $file));

  return round(89 + (($frasi * 300 - $lettere * 10) / $parole));
}

/**
 * Calcola la media dei valori all'interno di un array
 */
function median($array) {
  $sum = 0;

  foreach ($array as $val) {
   if(!is_nan($val))
    $sum += $val;
  }
  return $sum/sizeof($array);
}

/**
 * Calcola e stampa la media del Gulpease di tutti i file per ogni documento
 */
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
          array_push($gulpease, gulpease($path));
        }
      }
    }

    // Se $gulpease non è vuoto allora esisteva almeno un file .tex in $dir,
    // quindi calcola la media dei valori al suo interno e stampa il risultato
    if (sizeof($gulpease)) {
      $gulp = round(median($gulpease));

      if ($gulp >= 40)
        $esito = 'Positivo';
      else
        $esito = 'Negativo';
      echo "| $doc       | $gulp           | $esito |\n";
      echo "+-----------+--------------+----------+\n";
    }
    else {
      echo "| $doc       | Nessun file o directory |\n";
      echo "+-----------+-------------------------+\n";
    }
}

echo "\n";

?>
