<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();
    $session->checkCSRF();

    if (!$session->isAgent()) $session->redirect();

    $id = (int) $_POST['id'];

    require_once(__DIR__ . '/../database/connection.php');
    $db = getDatabaseConnection();

    require_once(__DIR__ . '/../database/class_ticket.php');
    $ticket = Ticket::getTicket($db, $id);

    if ($ticket && $ticket->deleteTag($db, (int) $_POST['tag']))
        $session->addMessage(true, "Tag successfully removed");
    else
        $session->addMessage(false, 'Tag could not be removed');

    header("Location: ../pages/ticket.php?id=$id");
?>
