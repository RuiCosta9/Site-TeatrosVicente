<?php
$pageTitle = $pageTitle ?? 'TeatroSVicente';
$activePage = $activePage ?? '';
$basePath = $basePath ?? '';
$pageCss = $pageCss ?? [];

$safeTitle = htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8');
$safeBasePath = htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $safeTitle ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $safeBasePath ?>css/base.css">
    <link rel="stylesheet" href="<?= $safeBasePath ?>css/components.css">
<?php foreach ($pageCss as $cssFile): ?>
    <link rel="stylesheet" href="<?= $safeBasePath ?>css/<?= htmlspecialchars($cssFile, ENT_QUOTES, 'UTF-8') ?>">
<?php endforeach; ?>
    <link rel="stylesheet" href="<?= $safeBasePath ?>css/responsive.css">
    <script src="<?= $safeBasePath ?>js/main.js" defer></script>
</head>

<body id="topo">
    <header>
        <div class="titulo">
            <a href="#topo">
                <img src="<?= $safeBasePath ?>img/pfp.jpg" alt="logo">
            </a>
            <h1>TeatroSVicente</h1>
        </div>

        <button class="menu-toggle" type="button" aria-controls="main-nav" aria-expanded="false" aria-label="Abrir menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav id="main-nav">
            <a href="<?= $safeBasePath ?>pages/home.php" class="<?= $activePage === 'home' ? 'active' : '' ?>">Main</a>
            <a href="<?= $safeBasePath ?>pages/contactos.php" class="<?= $activePage === 'contactos' ? 'active' : '' ?>">Contactos</a>
            <a href="<?= $safeBasePath ?>pages/agenda.php" class="<?= $activePage === 'agenda' ? 'active' : '' ?>">Agenda</a>
        </nav>
    </header>
