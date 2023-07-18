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
    $faq = FAQ::getFAQ($db, (int) $_POST['id']);

    if ($faq && $faq->edit($db, $question, $answer))
        $session->addMessage(true, 'FAQ successfully edited');
    else
        $session->addMessage(false, 'FAQ could not be edited');

    header('Location: ../pages/faqs.php');
?>
