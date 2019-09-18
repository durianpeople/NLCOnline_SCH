<?php
use NLC\Base\Sesi;
use NLC\Base\Questions;

if($_GET["s"]){
    $sesi = Sesi::load($_GET["s"]);
    if(!$sesi->enrollCheck()) abort(404);
    $question = $sesi->questions;
}else if($_GET["q"]){
    $question = Questions::load($_GET["q"]);
}

IO::streamFile(\UserData::getPath("QUESTION_" . $question->id), true, "Soal NLC - Schematics 2019.pdf");
