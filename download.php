<?php

/*-----------------------------------------------------------------------------------*/
/* This handles the headers to force the zip download */
/* This file will be accessed directly from the ajax call, look at the /js/general.js */
/*-----------------------------------------------------------------------------------*/

if(isset($_REQUEST['za_file']) && !empty($_REQUEST['za_file'])){

    $file = $_GET['za_file'];
    $filename = $_GET['za_filename'];
    
    header('Content-Type: application/zip');
    header('Content-Length: ' . filesize($file));
    header('Content-Disposition: attachment; filename="'.$filename.'.zip"');
   
    readfile($file);
    unlink($file); 
   
    exit;
}