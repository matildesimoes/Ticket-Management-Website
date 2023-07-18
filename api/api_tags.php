<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();

    if (!$session->isLoggedIn()) die();

    require_once(__DIR__ . '/../database/connection.php');
    $db = getDatabaseConnection();

    require_once(__DIR__ . '/../database/class_tag.php');

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $response = isset($_GET['id']) ? Tag::getTag($db, (int) $_GET['id']) : Tag::getTags($db);
            echo json_encode($response);
            break;
        case 'POST':
            if (!$session->isAgent()) die();
            $name = trim($_POST['name']);
            if ($name === '') die();
            echo json_encode(Tag::addTag($db, $name));
            break;
    }
?>
