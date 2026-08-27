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
$creativeCredits = [];
$castCredits = [];
$galleries = [];

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
               AND performance_date >= CURDATE()
             ORDER BY performance_date ASC, performance_time ASC'
        );
        $performancesStmt->execute(['production_id' => $production['id']]);
        $performances = $performancesStmt->fetchAll();

        $creditsStmt = $pdo->prepare(
            'SELECT pe.name, pc.role, pc.credit_type
             FROM production_credits AS pc
             INNER JOIN people AS pe ON pe.id = pc.person_id
             WHERE pc.production_id = :production_id
             ORDER BY pc.display_order ASC'
        );
        $creditsStmt->execute(['production_id' => $production['id']]);

        foreach ($creditsStmt->fetchAll() as $credit) {
            if ($credit['credit_type'] === 'creative') {
                $creativeCredits[] = $credit;
            } elseif ($credit['credit_type'] === 'cast') {
                $castCredits[] = $credit;
            }
        }

        $galleriesStmt = $pdo->prepare(
            'SELECT g.id AS gallery_id,
                    g.title AS gallery_title,
                    g.description AS gallery_description,
                    p.id AS photo_id,
                    p.file_path,
                    p.caption,
                    p.alt_text,
                    p.photographer,
                    p.sort_order,
                    p.is_cover
             FROM galleries AS g
             LEFT JOIN photos AS p ON p.gallery_id = g.id
             WHERE g.production_id = :production_id
             ORDER BY g.id ASC, p.sort_order ASC'
        );
        $galleriesStmt->execute(['production_id' => $production['id']]);

        foreach ($galleriesStmt->fetchAll() as $galleryRow) {
            $galleryId = $galleryRow['gallery_id'];

            if (!isset($galleries[$galleryId])) {
                $galleries[$galleryId] = [
                    'title' => $galleryRow['gallery_title'],
                    'description' => $galleryRow['gallery_description'],
                    'photos' => [],
                ];
            }

            if ($galleryRow['photo_id'] !== null) {
                $galleries[$galleryId]['photos'][] = $galleryRow;
            }
        }
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

                <?php if ($creativeCredits): ?>
                    <section class="production-credits" aria-labelledby="creative-credits-title">
                        <h3 id="creative-credits-title">Ficha Artística</h3>

                        <div class="credits-list">
                            <?php foreach ($creativeCredits as $credit): ?>
                                <div class="credit-item">
                                    <h4><?= htmlspecialchars($credit['role'], ENT_QUOTES, 'UTF-8') ?></h4>
                                    <p><?= htmlspecialchars($credit['name'], ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <?php if ($castCredits): ?>
                    <section class="production-credits" aria-labelledby="cast-credits-title">
                        <h3 id="cast-credits-title">Elenco</h3>

                        <div class="credits-list">
                            <?php foreach ($castCredits as $credit): ?>
                                <div class="credit-item">
                                    <h4><?= htmlspecialchars($credit['role'], ENT_QUOTES, 'UTF-8') ?></h4>
                                    <p><?= htmlspecialchars($credit['name'], ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <?php if ($galleries): ?>
                    <section class="production-galleries" aria-labelledby="galleries-title">
                        <h3 id="galleries-title">Galeria</h3>

                        <div class="gallery-list">
                            <?php foreach ($galleries as $gallery): ?>
                                <?php $hasDescription = trim((string) $gallery['description']) !== ''; ?>
                                <section class="gallery-group">
                                    <h4><?= htmlspecialchars($gallery['title'], ENT_QUOTES, 'UTF-8') ?></h4>

                                    <?php if ($hasDescription): ?>
                                        <p class="gallery-description"><?= nl2br(htmlspecialchars($gallery['description'], ENT_QUOTES, 'UTF-8')) ?></p>
                                    <?php endif; ?>

                                    <?php if ($gallery['photos']): ?>
                                        <div class="gallery-grid">
                                            <?php foreach ($gallery['photos'] as $photo): ?>
                                                <?php
                                                $hasCaption = trim((string) $photo['caption']) !== '';
                                                $hasPhotographer = trim((string) $photo['photographer']) !== '';
                                                $altText = trim((string) $photo['alt_text']);

                                                if ($altText === '') {
                                                    $altText = $hasCaption ? $photo['caption'] : $gallery['title'];
                                                }

                                                $imageUrl = $basePath . ltrim($photo['file_path'], '/');
                                                ?>
                                                <figure class="gallery-photo">
                                                    <img
                                                        src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') ?>"
                                                        alt="<?= htmlspecialchars($altText, ENT_QUOTES, 'UTF-8') ?>"
                                                        loading="lazy"
                                                    >
                                                    <?php if ($hasCaption || $hasPhotographer): ?>
                                                        <figcaption>
                                                            <?php if ($hasCaption): ?>
                                                                <p><?= htmlspecialchars($photo['caption'], ENT_QUOTES, 'UTF-8') ?></p>
                                                            <?php endif; ?>
                                                            <?php if ($hasPhotographer): ?>
                                                                <p class="gallery-photographer"><?= htmlspecialchars($photo['photographer'], ENT_QUOTES, 'UTF-8') ?></p>
                                                            <?php endif; ?>
                                                        </figcaption>
                                                    <?php endif; ?>
                                                </figure>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </section>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            </div>
        </article>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
