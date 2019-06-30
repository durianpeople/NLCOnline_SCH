<?php
return (new DatabaseTableBuilder)
    ->addColumn("id", "INT")->setAsPrimaryKey()->auto_increment()
    ->addColumn("name", "VARCHAR(50)")
    ->addColumn("start_time", "INT")
    ->addColumn("end_time", "INT");
