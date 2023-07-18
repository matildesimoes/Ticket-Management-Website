<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();
    $session->checkCSRF();

    if (!$session->isAdmin()) $session->redirect();

    require_once(__DIR__ . '/../database/connection.php');
    $db = getDatabaseConnection();

    require_once(__DIR__ . '/../database/class_user.php');
    $client = User::getUser($db, (int) $_POST['client']);

    if (!$client) $session->redirect();

    switch ($_POST['role']) {
        case 'agent':
            if ($client->upgradeToAgent($db))
                $session->addMessage(true, "{$client->getName()} successfully upgraded to agent");
            else
                $session->addMessage(false, "{$client->getName()} is already an agent");
            break;
        case 'admin':
            if ($client->upgradeToAdmin($db))
                $session->addMessage(true, "{$client->getName()} successfully upgraded to admin");
            else
                $session->addMessage(false, "{$client->getName()} is already an admin");
            break;
        default:
            $session->addMessage(false, "{$client->getName()} could not be upgraded");
            break;
    }

    header('Location: ../pages/management.php');
?>
