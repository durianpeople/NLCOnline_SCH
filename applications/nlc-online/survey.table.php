<?php

return DTB()
    ->addColumn("user_id", "INT")->setAsPrimaryKey()
    ->addColumn("kualitas_soal", "INT")
    ->addColumn("pelayanan_panitia", "INT")
    ->addColumn("kepuasan", "INT");