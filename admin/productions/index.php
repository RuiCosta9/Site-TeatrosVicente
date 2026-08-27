<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';

$stmt = $pdo->prepare(
    'SELECT id, title, year, status, is_featured, slug, updated_at
     FROM productions
     ORDER BY year DESC, title ASC'
);
$stmt->execute();
$productions = $stmt->fetchAll();

$statusLabels = [
    'current' => 'Em cena',
    'upcoming' => 'Futuro',
    'archive' => 'Arquivo',
];

$successMessages = [
    'created' => 'Espetáculo criado com sucesso.',
    'updated' => 'Espetáculo atualizado com sucesso.',
];
$successKey = isset($_GET['success']) && is_string($_GET['success']) ? $_GET['success'] : '';
$successMessage = $successMessages[$successKey] ?? '';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espetáculos | TeatroSVicente Admin</title>
    <link rel="icon" href="../../img/pfp.jpg" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../admin.css">
</head>
<body>
    <header class="admin-header">
        <a class="admin-brand" href="../dashboard.php">TeatroSVicente Admin</a>
        <nav class="admin-nav" aria-label="Navegação da administração">
            <a href="../dashboard.php">Dashboard</a>
            <a class="active" href="index.php">Espetáculos</a>
            <a href="../logout.php">Sair</a>
        </nav>
    </header>

    <main class="admin-main">
        <div class="admin-page-heading">
            <h1>Espetáculos</h1>
            <a class="admin-button" href="create.php">+ Novo espetáculo</a>
        </div>

        <?php if ($successMessage !== ''): ?>
            <p class="admin-message" role="status"><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <?php if ($productions): ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Ano</th>
                            <th>Estado</th>
                            <th>Destaque</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productions as $production): ?>
                            <tr>
                                <td><?= htmlspecialchars($production['title'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) ($production['year'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($statusLabels[$production['status']] ?? $production['status'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= (int) $production['is_featured'] === 1 ? 'Sim' : 'Não' ?></td>
                                <td class="admin-actions">
                                    <a href="edit.php?id=<?= htmlspecialchars((string) $production['id'], ENT_QUOTES, 'UTF-8') ?>">Editar</a>
                                    <a href="../../pages/espetaculo.php?slug=<?= htmlspecialchars(rawurlencode($production['slug']), ENT_QUOTES, 'UTF-8') ?>">Ver no site</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="admin-empty">Ainda não existem espetáculos.</p>
        <?php endif; ?>
    </main>
</body>
</html>
