<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();

    if (!$session->isLoggedIn()) die();

    require_once(__DIR__ . '/../database/connection.php');
    $db = getDatabaseConnection();

    require_once(__DIR__ . '/../database/class_ticket.php');

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            echo json_encode(Ticket::getTicket($db, (int) $_GET['id'])->getMessages($db));
            break;
        case 'POST':
            $ticket = Ticket::getTicket($db, (int) $_POST['id']);
            if (!$ticket) die();
            $date = date('Y-m-d');
            $author = $session->getId();
            $idFAQ = (int) $_POST['faq-reply'];
            $content = $idFAQ === 0 ? trim($_POST['content']) : FAQ::getFAQ($db, $idFAQ)->getAnswer();
            if ($content === '') {
                $session->addMessage(false, 'Message cannot be empty');
                die();
            }
            echo json_encode($ticket->addMessage($db, $date, $content, $author));
            break;
    }
?>
