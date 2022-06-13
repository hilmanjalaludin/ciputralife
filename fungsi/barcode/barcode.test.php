<?php
/*
Test unit for barcode.class
*/

include("barcode.class.php");

$mybarcode = new barcode();

$text = "*RC249916DDF*";
$size = array(240,24);
/*
$angle=0;
$fontsize=32;
$text_color=array(0,0,0);
$fill_color=array(255,255,255);
*/
$mybarcode->image_create($text, $size);
//$mybarcode->save_file();
$mybarcode->show(); //See barcode.test.html
?>