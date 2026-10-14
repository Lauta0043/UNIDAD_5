<?php
// Ensure SQLite file exists
$db = new PDO('sqlite:' . __DIR__ . '/estudiantes.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec("CREATE TABLE IF NOT EXISTS estudiantes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre TEXT NOT NULL,
    email TEXT NOT NULL,
    ciudad TEXT NOT NULL
)");
$db = null;
?>