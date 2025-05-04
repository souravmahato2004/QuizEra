<?php
    // if(session_status()==PHP_SESSION_NONE){
    //     session_start();
    // }
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname="quizera";

    $conn = mysqli_connect($servername,$username,$password,$dbname);

    if(!$conn){
        die("Sorry we failed to connect".mysqli_connect_error());
    }
    if ($conn->connect_error) {
        // Don't output HTML here - we need clean JSON responses
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
        exit;
    }
    $conn->set_charset("utf8mb4");
?>