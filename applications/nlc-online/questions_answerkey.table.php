<?php
return (new DatabaseTableBuilder)
    ->addColumn("question_id", "INT")
    ->addColumn("number", "INT")
    ->addColumn("answer", "INT")

    ->createIndex("ida", ["question_id"])
    ->createIndex("idc", ["question_id", "number"], "UNIQUE");
