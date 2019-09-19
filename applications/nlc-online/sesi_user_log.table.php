<?php
return (new DatabaseTableBuilder)
    ->addColumn("sesi_id", "INT")
    ->addColumn("user_id", "INT")
    ->addColumn("number", "INT")
    ->addColumn("answer", "INT")
    ->addColumn("id", "INT UNSIGNED")->setAsPrimaryKey()->auto_increment()

    ->createIndex("idx2", ["sesi_id", "user_id", "number"]);
