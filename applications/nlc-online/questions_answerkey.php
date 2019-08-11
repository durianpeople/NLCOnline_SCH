<?php
return (new DatabaseTableBuilder)
    ->addColumn("sesi_id", "INT")
    ->addColumn('user_id', 'INT')
    ->addColumn("number", "INT")
    ->addColumn("answer", "INT")

    ->createIndex("ida", ["sesi_id"])
    ->createIndex("idb", ["sesi_id", "user_id"])
    ->createIndex("idc", ["sesi_id", "user_id", "number"], "UNIQUE");
