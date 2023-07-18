<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();
    $session->checkCSRF();

    if (!$session->isAdmin()) $session->redirect();

    require_once(__DIR__ . '/../database/connection.php');
    $db = getDatabaseConnection();

    require_once(__DIR__ . '/../database/class_message.php');
    $message = Message::getMessage($db, (int) $_POST['id']);

    if ($message && $message->delete($db))
        $session->addMessage(true, 'Message successfully deleted');
    else
        $session->addMessage(false, 'Message could not be deleted');

    header("Location: {$_SERVER['HTTP_REFERER']}");
?>
