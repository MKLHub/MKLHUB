<?php
// config.php

// Caminho do banco SQLite
define("DB_FILE", __DIR__ . "/database.db");

function getDB() {
    static $db = null;
    if ($db === null) {
        $db = new PDO("sqlite:" . DB_FILE);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return $db;
}

// Inicialização automática do banco se não existir
function initDatabase() {
    $db = getDB();
    $schema = file_get_contents(__DIR__ . "/database.sql");
    $db->exec($schema);
}

if (!file_exists(DB_FILE)) {
    initDatabase();
}
?>
