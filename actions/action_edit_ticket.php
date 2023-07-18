<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();
    $session->checkCSRF();

    if (!$session->isLoggedIn()) $session->redirect();

    $id = (int) $_POST['id'];

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if ($title === '' || $description === '') {
        $session->addMessage(false, 'Ticket title/description cannot be empty');
        header("Location: ../pages/ticket.php?id=$id");
        die();
    }

    require_once(__DIR__ . '/../database/connection.php');
    $db = getDatabaseConnection();

    require_once(__DIR__ . '/../database/class_ticket.php');
    $ticket = Ticket::getTicket($db, $id);

    if (!$ticket || ($session->getId() !== $ticket->getAuthor()->getId())) $session->redirect();

    if ($ticket->edit($db, $title, $description))
        $session->addMessage(true, 'Ticket successfully edited');
    else
        $session->addMessage(false, 'Ticket could not be edited');

    header("Location: ../pages/ticket.php?id=$id");
?>
