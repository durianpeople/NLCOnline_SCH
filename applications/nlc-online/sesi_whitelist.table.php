<?php
return DTB()
    ->addColumn("sesi_id", "INT")
    ->addColumn("user_id", "INT")

    ->createIndex("idx", ["user_id"], "UNIQUE");