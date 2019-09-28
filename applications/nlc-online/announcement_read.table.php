<?php

return DTB()
    ->addColumn("announcement_id", "INT")
    ->addColumn("user_id", "INT")

    ->createIndex("idx1", ["announcement_id"])
    ->createIndex("idx2", ["user_id"])
    ->createIndex("idx3", ["announcement_id", "user_id"], "UNIQUE");