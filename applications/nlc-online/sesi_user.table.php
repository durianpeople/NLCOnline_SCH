<?php
return (new DatabaseTableBuilder)
    ->addColumn("sesi_handle", "VARCHAR(25)")
    ->addColumn("user_id", "INT")
    ->addColumn("answer", "TEXT")->allowNull()

    ->createIndex("su_uniq", ["sesi_handle", "user_id"], "UNIQUE");
