<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();

    if ($session->isLoggedIn())
        header('Location: ../pages/dashboard.php');
    else
        header('Location: ../pages/login.php');
?>
