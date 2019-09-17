<?php
return DTB()
    ->addColumn("sesi_id", "INT")
    ->addColumn("user_id", "INT")

    ->createIndex("idx", ["sesi_id", "user_id"], "UNIQUE");