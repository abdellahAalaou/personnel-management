<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Employee.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$employee = new Employee();

if (isset($_GET['id'])) {
    if ($employee->delete($_GET['id'])) {
        header('Location: dashboard.php');
        exit();
    } else {
        echo 'Failed to delete employee.';
    }
}
?>
