<?php

return DTB()
    ->addColumn("sesi_id", "INT")
    ->addColumn("user_id", "INT")
    ->addColumn("benar", "INT")
    ->addColumn("salah", "INT")
    ->addColumn("score", "INT")

    ->createIndex("idx", ["sesi_id", "user_id"], "UNIQUE");