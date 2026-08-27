<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) && is_string($_POST['username'])
        ? trim($_POST['username'])
        : '';
    $password = isset($_POST['password']) && is_string($_POST['password'])
        ? $_POST['password']
        : '';

    $stmt = $pdo->prepare(
        'SELECT id, username, password_hash
         FROM admins
         WHERE username = :username
         LIMIT 1'
    );
    $stmt->execute(['username' => $username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];

        header('Location: dashboard.php');
        exit;
    }

    $error = 'Credenciais inválidas.';
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | TeatroSVicente</title>
    <link rel="icon" href="../img/pfp.jpg" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; display: grid; place-items: center; padding: 24px; background: #f2f2f2; color: #111; font-family: "Montserrat", sans-serif; }
        .login-card { width: min(100%, 420px); padding: 40px; border-top: 5px solid #000; background: #fff; box-shadow: 0 18px 50px rgba(0, 0, 0, 0.1); }
        h1 { margin: 0 0 8px; font-family: "Cinzel", serif; font-size: 30px; }
        .subtitle { margin: 0 0 30px; color: #666; }
        .error { margin: 0 0 20px; padding: 12px; border-left: 3px solid #000; background: #ededed; font-size: 14px; }
        label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; }
        input { width: 100%; margin-bottom: 20px; padding: 12px; border: 1px solid #aaa; background: #fff; font: inherit; }
        input:focus { outline: 2px solid #000; outline-offset: 1px; }
        button { width: 100%; padding: 13px 18px; border: 1px solid #000; background: #000; color: #fff; font: inherit; font-weight: 600; cursor: pointer; }
        button:hover { background: #fff; color: #000; }
    </style>
</head>
<body>
    <main class="login-card">
        <h1>Administração</h1>
        <p class="subtitle">TeatroSVicente</p>

        <?php if ($error !== ''): ?>
            <p class="error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <form method="post" action="login.php">
            <label for="username">Username</label>
            <input id="username" name="username" type="text" autocomplete="username" required autofocus>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>

            <button type="submit">Entrar</button>
        </form>
    </main>
</body>
</html>
