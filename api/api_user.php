<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();

    if (!$session->isLoggedIn()) die();

    require_once(__DIR__ . '/../database/connection.php');
    $db = getDatabaseConnection();

    require_once(__DIR__ . '/../database/class_user.php');

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            echo json_encode(User::getUser($db, (int) $_GET['id']));
            break;
    }
?>
