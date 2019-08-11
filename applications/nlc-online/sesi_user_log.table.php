<?php
return (new DatabaseTableBuilder)
    ->addColumn("sesi_id", "INT")
    ->addColumn("user_id", "INT")
    ->addColumn("number", "INT")
    ->addColumn("answer", "INT")

    ->createIndex("idx", ["sesi_id"])
    ->createIndex("idx2", ["sesi_id", "user_id"]);
