<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();
    $session->checkCSRF();

    if (!$session->isLoggedIn()) $session->redirect();

    $id = (int) $_POST['id'];

    require_once(__DIR__ . '/../database/connection.php');
    $db = getDatabaseConnection();

    require_once(__DIR__ . '/../database/class_ticket.php');
    $ticket = Ticket::getTicket($db, $id);

    $date = date('Y-m-d');
    $author = $session->getId();
    $idFAQ = (int) $_POST['faq-reply'];

    $content = $idFAQ === 0 ? trim($_POST['content']) : FAQ::getFAQ($db, $idFAQ)->getAnswer();

    if ($content === '') {
        $session->addMessage(false, 'Message cannot be empty');
        header("Location: ../pages/ticket.php?id=$id");
        die();
    }

    if ($ticket && $ticket->addMessage($db, $date, $content, $author))
        $session->addMessage(true, 'Message successfully sent');
    else
        $session->addMessage(false, 'Message could not be sent');

    header("Location: ../pages/ticket.php?id=$id");
?>

