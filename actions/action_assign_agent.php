<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();
    $session->checkCSRF();

    if (!$session->isAdmin()) $session->redirect();

    require_once(__DIR__ . '/../database/connection.php');
    $db = getDatabaseConnection();

    require_once(__DIR__ . '/../database/class_user.php');
    $agent = User::getUser($db, (int) $_POST['agent']);

    if ($agent && $agent->isAgent($db) && $agent->assignToDepartment($db, (int) $_POST['department']))
        $session->addMessage(true, 'Agent successfully assigned to that department');
    else
        $session->addMessage(false, 'Agent already belongs to that department');

    header('Location: ../pages/management.php');
?>
