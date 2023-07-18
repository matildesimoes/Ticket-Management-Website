<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();

    if (!$session->isAdmin()) {
        header('Location: ../pages/index.php');
        die();
    }

    require_once(__DIR__ . '/../database/connection.php');
    $db = getDatabaseConnection();

    require_once(__DIR__ . '/../database/class_user.php');
    $notAdmins = User::getNotAdmins($db);
    $agents = User::getAgents($db);

    require_once(__DIR__ . '/../database/class_department.php');
    $departments = Department::getDepartments($db);

    require_once(__DIR__ . '/../templates/template_common.php');
    require_once(__DIR__ . '/../templates/template_management.php');

    drawHeader($session, 'Management');
    drawManagement($notAdmins, $departments, $agents);
    drawFooter();
?>
