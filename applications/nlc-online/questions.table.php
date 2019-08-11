<?php
return (new DatabaseTableBuilder)
    ->addColumn('id', 'INT')->setAsPrimaryKey()->auto_increment()
    ->addColumn('name', 'TEXT')

    ->createIndex("q_uniq", ["id"], "UNIQUE");
