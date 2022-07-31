<?php
    require_once('config.php');

    $ENV = $config['ENV'];

    $DB_HOST = $config['DB_HOST'];
    $DB_USERNAME = $config['DB_USERNAME'];
    $DB_PASSWORD = $config['DB_PASSWORD'];
    $DB_DATABASE = $config['DB_DATABASE'];


    $link = new mysqli($DB_HOST, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE);

    if ($link->connect_errno) {
        echo "Failed to connect to MySQL: " . $link->connect_error;
    }
