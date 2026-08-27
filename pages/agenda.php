<?php
$pageTitle = 'Agenda | TeatroSVicente';
$activePage = 'agenda';
$basePath = '../';
$pageCss = ['agenda.css'];

require_once __DIR__ . '/../config/database.php';

$stmt = $pdo->query(
    'SELECT p.performance_date, p.performance_time, p.venue, p.city,
            pr.title, pr.slug
     FROM performances AS p
     INNER JOIN productions AS pr ON pr.id = p.production_id
     WHERE p.performance_date >= CURDATE()
     ORDER BY p.performance_date ASC, p.performance_time ASC'
);
$performances = $stmt->fetchAll();

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

require_once __DIR__ . '/../templates/header.php';
?>

<main class="agenda-page">
    <section class="agenda-intro" aria-labelledby="agenda-title">
        <p class="agenda-eyebrow">TeatroSVicente</p>
        <h2 id="agenda-title">Agenda</h2>
        <p class="agenda-lead">Consulta as próximas apresentações e encontra-nos em palco.</p>
    </section>

    <?php if ($performances): ?>
        <section class="agenda-list" aria-label="Próximas apresentações">
            <?php foreach ($performances as $performance): ?>
                <?php
                $date = DateTimeImmutable::createFromFormat('!Y-m-d', $performance['performance_date']);
                $formattedDate = $date
                    ? $date->format('j') . ' de ' . $monthNames[(int) $date->format('n')] . ' de ' . $date->format('Y')
                    : $performance['performance_date'];
                $hasTime = trim((string) $performance['performance_time']) !== '';
                $formattedTime = $hasTime ? substr($performance['performance_time'], 0, 5) : '';
                ?>
                <article class="agenda-item">
                    <div class="agenda-schedule">
                        <time datetime="<?= htmlspecialchars($performance['performance_date'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($formattedDate, ENT_QUOTES, 'UTF-8') ?></time>
                        <?php if ($hasTime): ?>
                            <time datetime="<?= htmlspecialchars($performance['performance_time'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($formattedTime, ENT_QUOTES, 'UTF-8') ?></time>
                        <?php endif; ?>
                    </div>

                    <div class="agenda-details">
                        <h3><?= htmlspecialchars($performance['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p><?= htmlspecialchars($performance['venue'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><?= htmlspecialchars((string) $performance['city'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>

                    <a href="espetaculo.php?slug=<?= htmlspecialchars(rawurlencode($performance['slug']), ENT_QUOTES, 'UTF-8') ?>" class="agenda-link">Ver espetáculo</a>
                </article>
            <?php endforeach; ?>
        </section>
    <?php else: ?>
        <p class="agenda-empty">Não existem apresentações agendadas neste momento.</p>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
