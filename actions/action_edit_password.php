<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();
    $session->checkCSRF();

    if (!$session->isLoggedIn()) $session->redirect();

    $new = $_POST['new'];

    if (strlen($new) < 8 || !preg_match("/[A-Z]/", $new) || !preg_match("/[a-z]/", $new) || !preg_match("/[0-9]/", $new) || !preg_match("/\W/", $new)) {
        $session->addMessage(false, 'New password must have at least 8 characters, one uppercase, one lowercase, a number and a special character');
        header('Location: ../pages/password.php');
        die();
    }

    if ($new !== $_POST['confirm']) {
        $session->addMessage(false, 'The introduced passwords do not match');
        header('Location: ../pages/password.php');
        die();
    }

    require_once(__DIR__ . '/../database/connection.php');
    $db = getDatabaseConnection();

    require_once(__DIR__ . '/../database/class_user.php');
    $user = User::getUser($db, $session->getId());

    if ($user && $user->editPassword($db, $_POST['current'], $new))
        $session->addMessage(true, 'Password successfully edited');
    else
        $session->addMessage(false, 'The current password does not match');

    header('Location: ../pages/password.php');
?>
