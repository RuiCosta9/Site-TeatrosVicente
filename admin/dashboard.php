<?php

require_once __DIR__ . '/includes/auth.php';

$adminUsername = (string) ($_SESSION['admin_username'] ?? 'Administrador');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | TeatroSVicente</title>
    <link rel="icon" href="../img/pfp.jpg" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header class="admin-header">
        <a class="admin-brand" href="dashboard.php">TeatroSVicente Admin</a>
        <nav class="admin-nav" aria-label="Navegação da administração">
            <a class="active" href="dashboard.php">Dashboard</a>
            <a href="productions/index.php">Espetáculos</a>
            <a href="logout.php">Sair</a>
        </nav>
    </header>

    <main class="admin-main">
        <section class="admin-card">
            <h1>Bem-vindo, <?= htmlspecialchars($adminUsername, ENT_QUOTES, 'UTF-8') ?></h1>
            <p>Área de administração.</p>
        </section>
    </main>
</body>
</html>
