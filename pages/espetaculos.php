<?php
$pageTitle = 'Espetáculos | TeatroSVicente';
$activePage = 'espetaculos';
$basePath = '../';
$pageCss = ['espetaculos.css'];

require_once __DIR__ . '/../config/database.php';

$stmt = $pdo->query(
    "SELECT title, year, status, short_description, poster_path, slug
     FROM productions
     ORDER BY FIELD(status, 'current', 'upcoming', 'archive'),
              year DESC,
              title ASC"
);
$productions = $stmt->fetchAll();

$statusLabels = [
    'current' => 'EM CENA',
    'upcoming' => 'FUTURO',
    'archive' => 'ARQUIVO',
];

$statusClasses = [
    'current' => '',
    'upcoming' => ' show-status--outline',
    'archive' => ' show-status--muted',
];

$posterVariants = ['one', 'two', 'three', 'four', 'five', 'six'];

require_once __DIR__ . '/../templates/header.php';
?>

<main class="shows-page">
    <section class="shows-intro" aria-labelledby="shows-title">
        <p class="shows-eyebrow">TeatroSVicente</p>
        <h2 id="shows-title">Espetáculos</h2>
        <p class="shows-lead">Descobre as histórias que estão em palco e revisita as produções que fazem parte do nosso percurso.</p>

        <div class="shows-search">
            <label for="shows-search-input">Pesquisar espetáculos</label>
            <input id="shows-search-input" type="search" placeholder="Pesquisar espetáculos...">
        </div>
    </section>

    <section class="shows-list" aria-label="Lista de espetáculos">
        <?php foreach ($productions as $index => $production): ?>
            <?php
            $status = $production['status'];
            $statusLabel = $statusLabels[$status] ?? $status;
            $statusClass = $statusClasses[$status] ?? '';
            $posterVariant = $posterVariants[$index % count($posterVariants)];
            $hasPoster = trim((string) $production['poster_path']) !== '';
            ?>
            <article class="show-card">
                <div class="show-poster show-poster--<?= htmlspecialchars($posterVariant, ENT_QUOTES, 'UTF-8') ?>">
                    <?php if ($hasPoster): ?>
                        <img
                            src="<?= htmlspecialchars($production['poster_path'], ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars('Cartaz de ' . $production['title'], ENT_QUOTES, 'UTF-8') ?>"
                            style="position: absolute; inset: 0; z-index: 1; width: 100%; height: 100%; object-fit: cover;"
                        >
                    <?php else: ?>
                        <span><?= htmlspecialchars($production['title'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </div>
                <div class="show-card-body">
                    <div class="show-meta">
                        <time datetime="<?= htmlspecialchars((string) $production['year'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $production['year'], ENT_QUOTES, 'UTF-8') ?></time>
                        <span class="show-status<?= htmlspecialchars($statusClass, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <h3><?= htmlspecialchars($production['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p><?= htmlspecialchars((string) $production['short_description'], ENT_QUOTES, 'UTF-8') ?></p>
                    <a href="espetaculo.php?slug=<?= htmlspecialchars(rawurlencode($production['slug']), ENT_QUOTES, 'UTF-8') ?>" class="show-link">Ver espetáculo</a>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
</main>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
