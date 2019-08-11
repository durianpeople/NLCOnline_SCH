<?php
return (new DatabaseTableBuilder)
    ->addColumn("id", "INT")->setAsPrimaryKey()->auto_increment()
    ->addColumn("name", "VARCHAR(50)")
    ->addColumn("start_time", "INT(11) UNSIGNED")
    ->addColumn("end_time", "INT(11) UNSIGNED")
    ->addColumn("enabled", "SMALLINT")->defaultValue(0)
    ->addColumn("is_public", "SMALLINT")
    ->addColumn("questions_id", "INT")->allowNull()

    ->createIndex("idx", ["id"], "UNIQUE");
