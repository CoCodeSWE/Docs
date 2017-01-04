#!/bin/bash
cd ../RR/Interni/NormeDiProgetto
pdflatex NdP.tex
cd ../StudioDiFattibilita 
pdflatex StudioDiFattibilita.tex
cd ../Verbale_I_2016-12-10
pdflatex Verbale_I_2016-12-10.tex
cd ../../Esterni/Glossario
pdflatex Glossario.tex
cd ../PianoDiProgetto
pdflatex PianoDiProgetto.tex
cd ../Verbale_E_2016-12-17
pdflatex Verbale_E_2016-12-17.tex
cd ../AnalisiSDK
pdflatex AnalisiSDK.tex
cd ../AnalisiDeiRequisiti
pdflatex AdR.tex
cd ../LetteraDiPresentazione
pdflatex LetteraDiPresentazione.tex
cd ../PianoDiQualifica
pdflatex PianoDiQualifica.tex
exit
