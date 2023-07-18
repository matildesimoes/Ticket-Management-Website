<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();

    if (!$session->isLoggedIn()) {
        header('Location: ../pages/index.php');
        die();
    }

    require_once(__DIR__ . '/../database/connection.php');
    $db = getDatabaseConnection();

    $id = (int) $_GET['id'];

    require_once(__DIR__ . '/../database/class_ticket.php');
    $ticket = Ticket::getTicket($db, $id);

    if ($session->getId() !== $ticket->getAuthor()->getId() && !$session->isAgent()) {
        header('Location: ../pages/index.php');
        die();
    }

    $statuses = Status::getStatuses($db);
    $priorities = Priority::getPriorities($db);
    $departments = Department::getDepartments($db);
    $agents = User::getAgents($db);
    $tags = $ticket->getTags($db);
    $changes = $ticket->getChanges($db);
    $messages = $ticket->getMessages($db);
    $faqs = FAQ::getFAQs($db);

    require_once(__DIR__ . '/../templates/template_common.php');
    require_once(__DIR__ . '/../templates/template_ticket.php');

    drawHeader($session, "Ticket #$id");
    drawTicket($session, $ticket, $statuses, $priorities, $departments, $agents, $tags, $changes, $messages, $faqs);
    drawFooter();
?>
