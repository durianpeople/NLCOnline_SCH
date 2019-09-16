<?php
use NLC\Base\Questions;

switch ($_POST["act"]) {
    case "fetch":
        json_out(Questions::list());
    case "set_name":
        $q = Questions::load($_POST['id']);
        $q->name = $_POST["name"];
        json_out(true);
    case "upload_kunci":
        $q = Questions::load($_POST['id']);
        $q->uploadAnswerKey($_FILES["file"]["tmp_name"]);
        json_out($q->getAnswerKey());
    case "upload_soal":
        $q = Questions::load($_POST['id']);
        // /tmp/php/903cscj
        // apa.pdf
        $q->uploadQuestionPDF("file");
        json_out($q->question_pdf_url);
    case "new_modal":
        json_out($q = Questions::create($_POST['name']));
}

switch (request(2)) {
    default:
        Template::setSubTitle("Manage Paket Soal");
        return "main.php";
}
