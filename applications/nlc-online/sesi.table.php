<?php
return (new DatabaseTableBuilder)
    ->addColumn("handle", "VARCHAR(25)")
    ->addColumn("name", "VARCHAR(50)")
    ->addColumn("start_time", "INT(11) UNSIGNED")
    ->addColumn("end_time", "INT(11) UNSIGNED")
    ->addColumn("questions_handle", "VARCHAR(25)")->allowNull()

    ->createIndex("s_uniq", ["handle"], "UNIQUE");
