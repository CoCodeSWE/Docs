<?php

/**
 * Nome del file: glossarize.php
 * Percorso: /script/
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
 *
 */

echo <<< EOF
 _____ _                          _
|  __ \ | by Enrico Ceron        (_) v1.1
| |  \/ | ___  ___ ___  __ _ _ __ _ _______
| | __| |/ _ \/ __/ __|/ _` | '__| |_  / _ \
| |_\ \ | (_) \__ \__ \ (_| | |  | |/ /  __/
 \____/_|\___/|___/___/\__,_|_|  |_/___\___|

EOF;

/**
 * PRECONDIZIONE
 * È necessario essere in possesso di un file di testo contenente un
 * elenco di tutte le voci di glossario. Ad ogni riga del file corrisponde
 * una e una sola voce di glossario. Lo script è case sensitive.
 *
 * VIVI: istruzioni per glossarizzare:
 * inserire lo script nella cartella contenente i file da glossarizzare insieme al file
 * terminiGlossario.txt
 *
 * eseguire lo script dal terminale
 * > php glossarize.php nomeFile.tex
 *
 */

if (DIRECTORY_SEPARATOR != '/') {
  echo "\n";
  echo PHP_OS . " non supportato.\n";
  echo "Lo script funziona solo su sistemi Unix based.\n";
  echo "InfiniTech si scusa per il disagio.\n";
  exit;
}

/**
 * Cattura del path del documento da glossarizzare
 */
if (isset($argv[1])) {
  $docSign = $argv[1];
  if (file_exists($docSign)) {
    echo "È stato scelto di glossarizzare il documento <" . $argv[1] . ">...\n\n";
  } else {
    echo "File inesistente! Glossarizzazione abortita.\n";
    exit;
  }
} else {
  echo "Specificare il documento da glossarizzare!\n";
  echo "Usare il comando:\n\n";
  echo "> php glossarize.php '<path/to/file>'\n\n";
  exit;
}

/**
 * Apertura del documento e dell'elenco delle voci di glossario
 */
$filename = $argv[1];
$file = fopen($filename, 'r');
$voci = fopen('terminiGlossario.txt', 'r');
//$voci = fopen("http://infinitech.tk/Glossario/doc/vociGl.txt", 'r');

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
        echo "\033[33;32m>  riga $lineNumber:\tglossarizzato '$voce'\n";
      } else {
        shell_exec("sed -r -i '$lineNumber!b;s/(\\\gl\{\<$voce\>\})/$voce/g' $filename") . "\n";
        echo "\033[33;31m>  riga $lineNumber:\tdeglossarizzato '$voce'\n";
      }
    }
  }
}

echo "\e[0m\nGlossarizzazione di <" . $filename . "> terminata con (in)successo!\n";
echo "\e[0m\nInfiniTech si solleva da ogni responsabilità.\n";
fclose($file);
exit;
