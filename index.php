<?php 

require_once __DIR__ . '/vendor/autoload.php';

use PDFGeneratorOS\PDF_generator;

function clearTerminal(){
    if(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN'){
        system('cls');
    } else{
        system('clear');
    }
}