<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();

    if (!$session->isLoggedIn()) die();

    require_once(__DIR__ . '/../database/connection.php');
    $db = getDatabaseConnection();

    require_once(__DIR__ . '/../database/class_faq.php');

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $response = isset($_GET['id']) ? FAQ::getFAQ($db, (int) $_GET['id']) : FAQ::getFAQs($db);
            echo json_encode($response);
            break;
        case 'POST':
            if (!$session->isAgent()) die();
            $question = trim($_POST['question']);
            $answer = trim($_POST['answer']);
            if ($question === '' || $answer === '') die();
            echo json_encode(FAQ::addFAQ($db, $question, $answer));
            break;
        case 'PUT':
            if (!$session->isAgent()) die();
            $question = trim($_POST['question']);
            $answer = trim($_POST['answer']);
            if ($question === '' || $answer === '') die();
            $faq = FAQ::getFAQ($db, (int) $_POST['id']);
            if (!$faq) die();
            echo json_encode($faq->edit($db, $question, $answer));
            break;
        case 'DELETE':
            if (!$session->isAgent()) die();
            $faq = FAQ::getFAQ($db, (int) $_POST['id']);
            if (!$faq) die();
            echo json_encode($faq->delete($db));
            break;
    }
?>
