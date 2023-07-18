<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();
    $session->checkCSRF();

    if (!$session->isAdmin()) $session->redirect();

    require_once(__DIR__ . '/../database/connection.php');
    $db = getDatabaseConnection();

    require_once(__DIR__ . '/../database/class_ticket.php');
    $ticket = Ticket::getTicket($db, (int) $_POST['id']);

    if ($ticket && $ticket->delete($db))
        $session->addMessage(true, 'Ticket successfully deleted');
    else
        $session->addMessage(false, 'Ticket could not be deleted');

    header('Location: ../pages/tickets.php');
?>
