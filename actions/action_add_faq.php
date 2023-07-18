<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();
    $session->checkCSRF();

    if (!$session->isAgent()) $session->redirect();

    $question = trim($_POST['question']);
    $answer = trim($_POST['answer']);

    if ($question === '' || $answer === '') {
        $session->addMessage(false, 'FAQ fields cannot be empty');
        header('Location: ../pages/faqs.php');
        die();
    }

    require_once(__DIR__ . '/../database/connection.php');
    $db = getDatabaseConnection();

    require_once(__DIR__ . '/../database/class_faq.php');

    if (FAQ::addFAQ($db, $question, $answer))
        $session->addMessage(true, 'FAQ successfully added');
    else
        $session->addMessage(false, 'FAQ already exists');

    header('Location: ../pages/faqs.php');
?>
