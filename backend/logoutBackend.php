<?php
    unset($_SESSION['email']);
    unset($_SESSION['id']);
    session_unset();
    session_destroy();
    header("Location: ../");
?>