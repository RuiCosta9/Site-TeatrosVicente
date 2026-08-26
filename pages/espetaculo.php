<?php
$pageTitle = 'Espetáculo | TeatroSVicente';
$activePage = 'espetaculos';
$basePath = '../';
$pageCss = ['espetaculo.css'];

require_once __DIR__ . '/../config/database.php';

$slug = isset($_GET['slug']) && is_string($_GET['slug'])
    ? trim($_GET['slug'])
    : '';
$production = null;
$performances = [];

if ($slug !== '' && strlen($slug) <= 220) {
    $stmt = $pdo->prepare(
        'SELECT id, title, slug, short_description, description, year, poster_path, status
         FROM productions
         WHERE slug = :slug
         LIMIT 1'
    );
    $stmt->execute(['slug' => $slug]);
    $production = $stmt->fetch();

    if ($production) {
        $performancesStmt = $pdo->prepare(
            'SELECT performance_date, performance_time, venue, city, address, notes
             FROM performances
             WHERE production_id = :production_id
             ORDER BY performance_date ASC, performance_time ASC'
        );
        $performancesStmt->execute(['production_id' => $production['id']]);
        $performances = $performancesStmt->fetchAll();
    }
}

$statusLabels = [
    'current' => 'EM CENA',
    'upcoming' => 'FUTURO',
    'archive' => 'ARQUIVO',
];

$statusClasses = [
    'current' => '',
    'upcoming' => ' production-status--outline',
    'archive' => ' production-status--muted',
];

$monthNames = [
    1 => 'janeiro',
    2 => 'fevereiro',
    3 => 'março',
    4 => 'abril',
    5 => 'maio',
    6 => 'junho',
    7 => 'julho',
    8 => 'agosto',
    9 => 'setembro',
    10 => 'outubro',
    11 => 'novembro',
    12 => 'dezembro',
];

if ($production) {
    $pageTitle = $production['title'] . ' | TeatroSVicente';
}

require_once __DIR__ . '/../templates/header.php';
?>

<main class="production-page">
    <?php if (!$production): ?>
        <section class="production-not-found" aria-labelledby="not-found-title">
            <h2 id="not-found-title">Espetáculo não encontrado.</h2>
            <a href="espetaculos.php" class="production-back-link">Voltar aos espetáculos</a>
        </section>
    <?php else: ?>
        <?php
        $status = $production['status'];
        $statusLabel = $statusLabels[$status] ?? $status;
        $statusClass = $statusClasses[$status] ?? '';
        $hasPoster = trim((string) $production['poster_path']) !== '';
        ?>
        <a href="espetaculos.php" class="production-back-link production-back-link--top">Voltar aos espetáculos</a>

        <article class="production-detail">
            <div class="production-poster<?= $hasPoster ? ' production-poster--image' : '' ?>">
                <?php if ($hasPoster): ?>
                    <img
                        src="<?= htmlspecialchars($production['poster_path'], ENT_QUOTES, 'UTF-8') ?>"
                        alt="<?= htmlspecialchars('Cartaz de ' . $production['title'], ENT_QUOTES, 'UTF-8') ?>"
                    >
                <?php else: ?>
                    <span><?= htmlspecialchars($production['title'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>

            <div class="production-content">
                <div class="production-meta">
                    <time datetime="<?= htmlspecialchars((string) $production['year'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $production['year'], ENT_QUOTES, 'UTF-8') ?></time>
                    <span class="production-status<?= htmlspecialchars($statusClass, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?></span>
                </div>

                <h2><?= htmlspecialchars($production['title'], ENT_QUOTES, 'UTF-8') ?></h2>
                <p class="production-lead"><?= htmlspecialchars((string) $production['short_description'], ENT_QUOTES, 'UTF-8') ?></p>

                <div class="production-description">
                    <h3>Sobre o espetáculo</h3>
                    <p><?= nl2br(htmlspecialchars((string) $production['description'], ENT_QUOTES, 'UTF-8')) ?></p>
                </div>

                <?php if ($performances): ?>
                    <section class="production-performances" aria-labelledby="performances-title">
                        <h3 id="performances-title">Próximas apresentações</h3>

                        <div class="performance-list">
                            <?php foreach ($performances as $performance): ?>
                                <?php
                                $date = DateTimeImmutable::createFromFormat('!Y-m-d', $performance['performance_date']);
                                $formattedDate = $date
                                    ? $date->format('j') . ' de ' . $monthNames[(int) $date->format('n')] . ' de ' . $date->format('Y')
                                    : $performance['performance_date'];
                                $hasTime = trim((string) $performance['performance_time']) !== '';
                                $formattedTime = $hasTime
                                    ? substr($performance['performance_time'], 0, 5)
                                    : '';
                                $hasAddress = trim((string) $performance['address']) !== '';
                                $hasNotes = trim((string) $performance['notes']) !== '';
                                ?>
                                <article class="performance-item">
                                    <div class="performance-schedule">
                                        <time datetime="<?= htmlspecialchars($performance['performance_date'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($formattedDate, ENT_QUOTES, 'UTF-8') ?></time>
                                        <?php if ($hasTime): ?>
                                            <time datetime="<?= htmlspecialchars($performance['performance_time'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($formattedTime, ENT_QUOTES, 'UTF-8') ?></time>
                                        <?php endif; ?>
                                    </div>

                                    <div class="performance-location">
                                        <h4><?= htmlspecialchars($performance['venue'], ENT_QUOTES, 'UTF-8') ?></h4>
                                        <p><?= htmlspecialchars((string) $performance['city'], ENT_QUOTES, 'UTF-8') ?></p>
                                        <?php if ($hasAddress): ?>
                                            <p><?= htmlspecialchars($performance['address'], ENT_QUOTES, 'UTF-8') ?></p>
                                        <?php endif; ?>
                                        <?php if ($hasNotes): ?>
                                            <p class="performance-notes"><?= nl2br(htmlspecialchars($performance['notes'], ENT_QUOTES, 'UTF-8')) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            </div>
        </article>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
