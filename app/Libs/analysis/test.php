<?php

include_once "phpanalysis.class.php";

$obj = new PhpAnalysis();

$str = "日本人肉代购";
$obj ->differFreq = false;

$obj ->SetSource($str,'utf8','utf8');

$obj ->StartAnalysis();

$res = $obj ->GetFinallyKeywords(2);

var_dump($res);

