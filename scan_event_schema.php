<?php
require 'bootstrap/app.php';
$app = app();
$db = $app->make('db');

// Get critical columns
$critical_columns = ['organizer_id', 'title', 'event_date', 'status', 'slug', 'created_by'];

echo "=== CRITICAL COLUMNS IN EVENTS TABLE ===\n";
$columns = $db->select("SELECT COLUMN_NAME, IS_NULLABLE, DATA_TYPE, COLUMN_TYPE, COLUMN_KEY FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='events' AND TABLE_SCHEMA=DATABASE() AND COLUMN_NAME IN ('" . implode("','", $critical_columns) . "')");
foreach ($columns as $col) {
    $nullable = $col->IS_NULLABLE === 'YES' ? '✓ NULL' : '✗ NOT NULL';
    echo "{$col->COLUMN_NAME}: {$col->COLUMN_TYPE} ({$nullable})\n";
}

echo "\n=== FOREIGN KEY CONSTRAINTS ===\n";
$fks = $db->select("SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME='events' AND TABLE_SCHEMA=DATABASE() AND REFERENCED_TABLE_NAME IS NOT NULL");
foreach ($fks as $fk) {
    echo "{$fk->CONSTRAINT_NAME}: {$fk->COLUMN_NAME} -> {$fk->REFERENCED_TABLE_NAME}\n";
}

echo "\n=== ORGANIZER_ID CONSTRAINT DETAILS ===\n";
$constraint = $db->select("SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME, UPDATE_RULE, DELETE_RULE FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS rc JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE kcu ON rc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME AND rc.TABLE_NAME = kcu.TABLE_NAME WHERE kcu.TABLE_NAME='events' AND kcu.COLUMN_NAME='organizer_id'");
foreach ($constraint as $c) {
    var_dump($c);
}

// Check migrations
echo "\n=== RECENT MIGRATIONS ===\n";
$migrations = $db->table('migrations')->orderBy('id', 'desc')->limit(10)->get();
foreach ($migrations as $m) {
    echo "{$m->migration}\n";
}
?>
