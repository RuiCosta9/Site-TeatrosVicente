<?php
$footerEmail = $footerEmail ?? 'bilheteira@teatrosvicente.pt';
$footerLocation = $footerLocation ?? 'Localização do teatro';
$footerInstagram = $footerInstagram ?? 'https://www.instagram.com/teatrosvicente/';
$footerFacebook = $footerFacebook ?? 'https://web.facebook.com/teatrosvicenteoficial/?locale=pt_PT&_rdc=1&_rdr';

$safeBasePath = htmlspecialchars($basePath ?? '', ENT_QUOTES, 'UTF-8');
$safeFooterEmail = htmlspecialchars($footerEmail, ENT_QUOTES, 'UTF-8');
$safeFooterLocation = htmlspecialchars($footerLocation, ENT_QUOTES, 'UTF-8');
$safeFooterInstagram = htmlspecialchars($footerInstagram, ENT_QUOTES, 'UTF-8');
$safeFooterFacebook = htmlspecialchars($footerFacebook, ENT_QUOTES, 'UTF-8');
?>

    <footer class="site-footer">
        <div class="footer-main">
            <div class="footer-brand">
                <a href="<?= $safeBasePath ?>pages/home.php" class="footer-logo">
                    <img src="<?= $safeBasePath ?>img/pfp.jpg" alt="logo">
                    <span>TeatroSVicente</span>
                </a>
            </div>

            <div class="footer-middle"></div>

            <address class="footer-contact">
                <p><?= $safeFooterLocation ?></p>
                <a href="mailto:<?= $safeFooterEmail ?>"><?= $safeFooterEmail ?></a>
                <div class="footer-social-links">
                    <a href="<?= $safeFooterInstagram ?>" class="footer-social" target="_blank" rel="noopener" aria-label="Instagram TeatroSVicente">
                        <img src="<?= $safeBasePath ?>img/instagramlogo.jpg" alt="">
                    </a>
                    <a href="<?= $safeFooterFacebook ?>" class="footer-social" target="_blank" rel="noopener" aria-label="Facebook TeatroSVicente">
                        <img src="<?= $safeBasePath ?>img/facebooklogo.png" alt="">
                    </a>
                </div>
            </address>
        </div>

        <div class="footer-bottom">
            <p>2026 &copy; TeatroSVicente</p>
        </div>
    </footer>

</body>
</html>
