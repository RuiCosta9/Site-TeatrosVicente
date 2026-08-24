<?php

require_once __DIR__ . '/config/database.php';

$stmt = $pdo->query('SELECT DATABASE() AS database_name');

$result = $stmt->fetch();

echo 'Ligado à base de dados: ' . htmlspecialchars($result['database_name']);