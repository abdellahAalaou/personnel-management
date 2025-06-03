<?php
require_once '../config/database.php';
require_once '../classes/Employee.php';

$employeeObj = new Employee();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $employee = $employeeObj->getById($id);
} else {
    die("n'existe pas.");
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap');

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color:white; 
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background-color: #fff;
            padding: 30px;
            border-radius: 15px;
            width: 600px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.5);
           
        }

        .card h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .info {
            margin-bottom: 10px;
            font-size: 16px;
        }

        .label {
            font-weight: bold;
            color: #555;
        }
    </style>
</head>
<body>

<div class="card">
    <h2> DETAILLE D'EMPLOYEE</h2>
    <div class="info"><span class="label">ID :</span> <?= htmlspecialchars($employee['ID_EMP']) ?></div>
    <div class="info"><span class="label">N°SERVICE</span> <?= htmlspecialchars($employee['N__SERVICE']) ?></div>
    <div class="info"><span class="label">NOM</span> <?= htmlspecialchars($employee['NOM_EMP']) ?></div>
    <div class="info"><span class="label">PRENOM:</span> <?= htmlspecialchars($employee['PRENOM_EMP']) ?></div>
    <div class="info"><span class="label">DATE :</span> <?= htmlspecialchars($employee['DATE_EMP']) ?></div>
    <div class="info"><span class="label">TEL:</span> <?= htmlspecialchars($employee['TEL_EMP']) ?></div>
    <div class="info"><span class="label">EMAIL :</span> <?= htmlspecialchars($employee['EMAIL_EMP']) ?></div>
    <div class="info"><span class="label">ADRESSE:</span> <?= htmlspecialchars($employee['ADRESSE_EMP']) ?></div>
    <div class="info"><span class="label">DATEEMBAUCHE :</span> <?= htmlspecialchars($employee['DATEEMBAUCH_EMP']) ?></div>
    <div class="info"><span class="label">NOMBRE ENFANT :</span> <?= htmlspecialchars($employee['NOMBRE_D_ENFANT']) ?></div>
    <div class="info"><span class="label">NOM UTILISATEUR :</span> <?= htmlspecialchars($employee['NOM_UTILISATEUR']) ?></div>
</div>

</body>
</html>
