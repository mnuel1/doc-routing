<?php

    $env = parse_ini_file($_SERVER['DOCUMENT_ROOT'] .'/.env');
    $servername = $env['SERVER_NAME'];
    $username = $env["USERNAME"];
    $password = $env["PASSWORD"];
    $dbname = $env["DBNAME"];


    $connect = new mysqli($servername, $username, $password, $dbname);

    if (!$connect->connect_error) {
        echo "Success";
    } else {
        die("Connection Failed : " . $connect->connect_error);
    }
?>
