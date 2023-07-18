<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../utils/session.php');
    $session = new Session();
    $session->checkCSRF();

    if (!$session->isLoggedIn()) $session->redirect();

    require_once(__DIR__ . '/../database/connection.php');
    $db = getDatabaseConnection();

    require_once(__DIR__ . '/../database/class_user.php');
    $user = User::getUser($db, $session->getId());
    
    $saveDir = "../profile_photos/";
    $originalName = basename($_FILES["photo-upload"]["name"]);
    $photoType = pathinfo($originalName, PATHINFO_EXTENSION);

    $saveFile = $saveDir . $session->getId() . "." . $photoType ;

    if ($photoType != "jpg" && $photoType != "png" && $photoType != "jpeg") {
        $session->addMessage(false, 'Only JPG, PNG and JPEG files are allowed');
        header('Location: ../pages/profile.php');
        die();
    }

    if (file_exists($saveFile)) unlink($saveFile);

    if (move_uploaded_file($_FILES["photo-upload"]["tmp_name"], $saveFile) && $user->updatePhoto($db, $saveFile))
        $session->addMessage(true, 'Profile photo successfully updated');
    else
        $session->addMessage(false, 'Profile photo could not be updated');

    header('Location: ../pages/profile.php');
?>