<?php

    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/src/controller/documentController.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/src/controller/userController.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/src/controller/reportController.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/src/controller/emailSender.php';

    session_start();

    if ($_SERVER['REQUEST_URI'] === "/login" 
        && $_SERVER['REQUEST_METHOD'] === 'POST') {

    }

?>
