<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $adminPosition = strpos($scriptName, '/admin/');
    $adminBasePath = $adminPosition !== false
        ? substr($scriptName, 0, $adminPosition + strlen('/admin'))
        : '/admin';

    header('Location: ' . $adminBasePath . '/login.php');
    exit;
}
