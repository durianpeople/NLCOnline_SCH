<?php
return (new DatabaseTableBuilder)
    ->addColumn('handle', 'VARCHAR(25)')
    ->addColumn('question_json', 'TEXT')->allowNull()
    ->addColumn('answer_key_json', 'TEXT')->allowNull()

    ->createIndex("q_uniq", ["handle"], "UNIQUE");
