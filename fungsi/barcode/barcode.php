<?php
include("barcode.class.php");

$mybarcode = new barcode();

$text = $_GET['podNumber'];
$size = array(240,14);
/*
$angle=0;
$fontsize=32;
$text_color=array(0,0,0);
$fill_color=array(255,255,255);
*/
$mybarcode->image_create($text);
$mybarcode->show(); 
?>