<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();
    $session->checkCSRF();
    
    if (!$session->isLoggedIn()) $session->redirect();

    $session->logout();

    header('Location: /../pages/login.php');
?>
