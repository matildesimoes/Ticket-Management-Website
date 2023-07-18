<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();
    $session->checkCSRF();

    if (!$session->isAdmin()) $session->redirect();

    require_once(__DIR__ . '/../database/connection.php');
    $db = getDatabaseConnection();

    require_once(__DIR__ . '/../database/class_user.php');
    $user = User::getUser($db, (int) $_POST['user']);

    if ($user && $user->delete($db))
        $session->addMessage(true, 'User successfully deleted');
    else
        $session->addMessage(false, 'User could not be deleted');

    header('Location: ../pages/management.php');
?>
