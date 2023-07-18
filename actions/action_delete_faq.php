<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();
    $session->checkCSRF();

    if (!$session->isAgent()) $session->redirect();

    require_once(__DIR__ . '/../database/connection.php');
    $db = getDatabaseConnection();

    require_once(__DIR__ . '/../database/class_faq.php');
    $faq = FAQ::getFAQ($db, (int) $_POST['id']);

    if ($faq && $faq->delete($db))
        $session->addMessage(true, 'FAQ successfully deleted');
    else
        $session->addMessage(false, 'FAQ could not be deleted');

    header('Location: ../pages/faqs.php');
?>
