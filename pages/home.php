<?php
$pageTitle = 'TeatroSVicente';
$activePage = 'home';
$basePath = '../';
$pageCss = ['home.css'];

require_once __DIR__ . '/../templates/header.php';
?>

<main>
    <section class="hero-slideshow" aria-label="Destaques TeatroSVicente">
        <div class="slideshow-track">
            <article class="slide is-active" aria-hidden="false">
                <img src="<?= $basePath ?>img/Sombras3.jpg" alt="Placeholder 1">
                <div class="slide-overlay">
                    <p>Placeholder #1</p>
                </div>
            </article>

            <article class="slide" aria-hidden="true">
                <div class="slide-placeholder">
                    <p>Placeholder #2</p>
                </div>
            </article>

            <article class="slide" aria-hidden="true">
                <div class="slide-placeholder slide-placeholder-dark">
                    <p>Placeholder #3</p>
                </div>
            </article>
        </div>

        <button class="slide-arrow slide-arrow-prev" type="button" aria-label="Slide anterior">
            <span aria-hidden="true">&#8249;</span>
        </button>

        <button class="slide-arrow slide-arrow-next" type="button" aria-label="Slide seguinte">
            <span aria-hidden="true">&#8250;</span>
        </button>

        <div class="slide-dots" aria-label="Paginação do slideshow">
            <button class="slide-dot is-active" type="button" aria-label="Mostrar slide 1" aria-current="true"></button>
            <button class="slide-dot" type="button" aria-label="Mostrar slide 2"></button>
            <button class="slide-dot" type="button" aria-label="Mostrar slide 3"></button>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
