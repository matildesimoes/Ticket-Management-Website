<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();
    $session->checkCSRF();

    if ($session->isLoggedIn()) $session->redirect();

    if (!preg_match("/^[a-zA-Z\s]+$/", $_POST['first-name']) || !preg_match("/^[a-zA-Z\s]+$/", $_POST['last-name']))
        $session->addMessage(false, 'Name can only contains letters and spaces. Unexpected characters will be filtered.');

    $firstName = preg_replace("/[^a-zA-Z\s]/", '', trim($_POST['first-name']));
    $lastName = preg_replace("/[^a-zA-Z\s]/", '', trim($_POST['last-name']));

    $username = strtolower(trim($_POST['username']));
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];

    if ($firstName === '' || $lastName === '' || $username === '' || $email === '' || $password === '') {
        $session->addMessage(false, 'Register fields cannot be empty');
        header('Location: ../pages/register.php');
        die();
    }

    if (strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password) || !preg_match("/\W/", $password)) {
        $session->addMessage(false, 'Password must have at least 8 characters, one uppercase, one lowercase, a number and a special character');
        header('Location: ../pages/register.php');
        die();
    }

    require_once(__DIR__ . '/../database/connection.php');
    $db = getDatabaseConnection();

    require_once(__DIR__ . '/../database/class_user.php');
    $user = User::registerUser($db, $firstName, $lastName, $username, $email, $password);

    if ($user) {
        $session->setId($user->getId());
        $session->setName($user->getName());
        $session->setAgent($user->isAgent($db));
        $session->setAdmin($user->isAdmin($db));
        header('Location: ../pages/dashboard.php');
    } else {
        $session->addMessage(false, 'Register unsuccessful: username/email already exists');
        header('Location: ../pages/register.php');
    }
?>
