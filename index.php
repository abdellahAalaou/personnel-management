<?php
session_start();


if (isset($_SESSION['user_id'])) {

    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: employee/dashboard.php');
    }
} else {

    header('Location: Evoltec/index.html');
}
exit();
?> 