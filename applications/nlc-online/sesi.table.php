<?php
return (new DatabaseTableBuilder)
    ->addColumn("id", "INT")->setAsPrimaryKey()->auto_increment()
    ->addColumn("name", "VARCHAR(50)")
    ->addColumn("start_time", "INT(11) UNSIGNED")
    ->addColumn("end_time", "INT(11) UNSIGNED")
    ->addColumn("enabled", "TINYINT")->defaultValue(0)
    ->addColumn("type", "TINYINT")->defaultValue(0)
    ->addColumn("quota", "INT")->defaultValue(0)
    ->addColumn("questions_id", "INT")->allowNull()

    ->createIndex("idx", ["id"], "UNIQUE");
