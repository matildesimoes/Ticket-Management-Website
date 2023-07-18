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

    require_once(__DIR__ . '/../database/class_status.php');
    $statuses = Status::getStatuses($db);

    $count = array();

    require_once(__DIR__ . '/../database/class_ticket.php');
    foreach ($statuses as $status)
        $count[] = Ticket::getTicketsCountByStatus($db, $session->getId(), $status->getId());

    require_once(__DIR__ . '/../templates/template_common.php');
    require_once(__DIR__ . '/../templates/template_dashboard.php');

    drawHeader($session, 'Dashboard');
    drawDashboard($statuses, $count);
    drawFooter();
?>
