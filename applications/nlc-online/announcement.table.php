<?php

return DTB()
    ->addColumn("id", "INT")->setAsPrimaryKey()->auto_increment()
    ->addColumn("title")
    ->addColumn("content");