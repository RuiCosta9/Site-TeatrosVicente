<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';

function createProductionSlug(string $value): string
{
    $value = strtr($value, [
        'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
        'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
        'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
        'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
        'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ç' => 'C',
        'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
        'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
        'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ç' => 'c',
    ]);
    $value = strtolower($value);
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';

    return trim($value, '-');
}

$allowedStatuses = ['current', 'upcoming', 'archive'];
$values = [
    'title' => '',
    'slug' => '',
    'short_description' => '',
    'description' => '',
    'year' => '',
    'status' => 'upcoming',
    'is_featured' => 0,
];
$errors = [];

if (empty($_SESSION['production_csrf_token'])) {
    $_SESSION['production_csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values['title'] = isset($_POST['title']) && is_string($_POST['title']) ? trim($_POST['title']) : '';
    $submittedSlug = isset($_POST['slug']) && is_string($_POST['slug']) ? trim($_POST['slug']) : '';
    $values['slug'] = createProductionSlug($submittedSlug !== '' ? $submittedSlug : $values['title']);
    $values['short_description'] = isset($_POST['short_description']) && is_string($_POST['short_description']) ? trim($_POST['short_description']) : '';
    $values['description'] = isset($_POST['description']) && is_string($_POST['description']) ? trim($_POST['description']) : '';
    $values['year'] = isset($_POST['year']) && is_string($_POST['year']) ? trim($_POST['year']) : '';
    $values['status'] = isset($_POST['status']) && is_string($_POST['status']) ? $_POST['status'] : '';
    $values['is_featured'] = isset($_POST['is_featured']) && $_POST['is_featured'] === '1' ? 1 : 0;
    $csrfToken = isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

    if (!hash_equals($_SESSION['production_csrf_token'], $csrfToken)) {
        $errors[] = 'O formulário expirou. Tenta novamente.';
    }
    if ($values['title'] === '') {
        $errors[] = 'O título é obrigatório.';
    } elseif (strlen($values['title']) > 200) {
        $errors[] = 'O título não pode exceder 200 caracteres.';
    }
    if ($values['slug'] === '') {
        $errors[] = 'Não foi possível gerar um slug válido.';
    } elseif (strlen($values['slug']) > 220) {
        $errors[] = 'O slug não pode exceder 220 caracteres.';
    }
    if ($values['year'] !== '' && (!ctype_digit($values['year']) || (int) $values['year'] > 65535)) {
        $errors[] = 'O ano deve ser um número válido.';
    }
    if (!in_array($values['status'], $allowedStatuses, true)) {
        $errors[] = 'O estado selecionado é inválido.';
    }

    if (!$errors) {
        $slugStmt = $pdo->prepare('SELECT id FROM productions WHERE slug = :slug LIMIT 1');
        $slugStmt->execute(['slug' => $values['slug']]);

        if ($slugStmt->fetch()) {
            $errors[] = 'Já existe um espetáculo com este slug.';
        }
    }

    if (!$errors) {
        $insertStmt = $pdo->prepare(
            'INSERT INTO productions
                (title, slug, short_description, description, year, status, is_featured)
             VALUES
                (:title, :slug, :short_description, :description, :year, :status, :is_featured)'
        );
        $insertStmt->execute([
            'title' => $values['title'],
            'slug' => $values['slug'],
            'short_description' => $values['short_description'],
            'description' => $values['description'],
            'year' => $values['year'] !== '' ? (int) $values['year'] : null,
            'status' => $values['status'],
            'is_featured' => $values['is_featured'],
        ]);

        header('Location: index.php?success=created');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo espetáculo | TeatroSVicente Admin</title>
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
            <a href="../dashboard.php">Dashboard</a><a class="active" href="index.php">Espetáculos</a><a href="../logout.php">Sair</a>
        </nav>
    </header>
    <main class="admin-main">
        <div class="admin-page-heading"><h1>Novo espetáculo</h1></div>
        <?php if ($errors): ?>
            <div class="admin-message admin-message--error" role="alert">
                <?php foreach ($errors as $error): ?><div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endforeach; ?>
            </div>
        <?php endif; ?>
        <section class="admin-card">
            <form class="admin-form" method="post" action="create.php">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['production_csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                <div class="admin-field"><label for="title">Título *</label><input id="title" name="title" type="text" maxlength="200" required value="<?= htmlspecialchars($values['title'], ENT_QUOTES, 'UTF-8') ?>"></div>
                <div class="admin-field"><label for="slug">Slug</label><input id="slug" name="slug" type="text" maxlength="220" value="<?= htmlspecialchars($values['slug'], ENT_QUOTES, 'UTF-8') ?>"></div>
                <div class="admin-field"><label for="short_description">Descrição curta</label><textarea id="short_description" name="short_description"><?= htmlspecialchars($values['short_description'], ENT_QUOTES, 'UTF-8') ?></textarea></div>
                <div class="admin-field"><label for="description">Descrição</label><textarea class="admin-description" id="description" name="description"><?= htmlspecialchars($values['description'], ENT_QUOTES, 'UTF-8') ?></textarea></div>
                <div class="admin-field"><label for="year">Ano</label><input id="year" name="year" type="number" min="0" max="65535" value="<?= htmlspecialchars($values['year'], ENT_QUOTES, 'UTF-8') ?>"></div>
                <div class="admin-field"><label for="status">Estado</label><select id="status" name="status"><?php foreach (['current' => 'Em cena', 'upcoming' => 'Futuro', 'archive' => 'Arquivo'] as $status => $label): ?><option value="<?= $status ?>" <?= $values['status'] === $status ? 'selected' : '' ?>><?= $label ?></option><?php endforeach; ?></select></div>
                <label class="admin-checkbox"><input name="is_featured" type="checkbox" value="1" <?= $values['is_featured'] === 1 ? 'checked' : '' ?>> Destacar na homepage</label>
                <div class="admin-form-actions"><button class="admin-button" type="submit">Criar espetáculo</button><a class="admin-button admin-button--secondary" href="index.php">Cancelar</a></div>
            </form>
        </section>
    </main>
</body>
</html>
