<?php
return DTB()
    ->addColumn("user_id", "INT")->setAsPrimaryKey()
    ->addColumn("nlc_id", "INT")

    ->createIndex("idx", ["nlc_id"], "UNIQUE");