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

    require_once(__DIR__ . '/../database/class_faq.php');
    $faqs = FAQ::getFAQs($db);

    require_once(__DIR__ . '/../templates/template_common.php');
    require_once(__DIR__ . '/../templates/template_faqs.php');

    drawHeader($session, 'FAQ');
    if ($session->isAgent())
        drawFAQsAgent($faqs);
    else
        drawFAQsClient($faqs);
    drawFooter();
?>
