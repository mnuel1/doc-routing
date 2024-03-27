<?php

    require_once '../vendor/autoload.php';
    use Dotenv\Dotenv;

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $servername = $_ENV["SERVER_NAME"];
    $username = $_ENV["USERNAME"];
    $password = $_ENV["PASSWORD"];
    $dbname = $_ENV["DBNAME"];

    $connect = new mysqli($servername, $username, $password, $dbname);

    if (!$connect->connect_error) {
        // echo "Success";
    } else {
        die("Connection Failed : " . $connect->connect_error);
    }
