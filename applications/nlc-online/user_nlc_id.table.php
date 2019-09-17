<?php
return DTB()
    ->addColumn("user_id", "INT")->setAsPrimaryKey()
    ->addColumn("nlc_id", "CHAR(8)")

    ->createIndex("idx", ["nlc_id"], "UNIQUE");