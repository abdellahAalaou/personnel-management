<?php
require_once '../config/database.php';
require_once '../classes/Employee.php';

if (!isset($_GET['id'])) {
    echo "ID manquant";
    exit();
}

$employeeObj = new Employee();
$employee = $employeeObj->getById($_GET['id']);

if (!$employee) {
    echo "Employé introuvable";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Impression - Employé</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 30px;
        }

        .employee-info {
            max-width: 700px;
            background-color: #fff;
            margin: auto;
            border: 1px solid #ddd;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .info-row {
            margin: 15px 0;
            font-size: 16px;
        }

        .info-label {
            font-weight: bold;
            color: #444;
        }

        .btn-print-container {
            text-align: center;
            margin-top: 30px;
        }

        .btn-custom {
            background-color: #007bff;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            margin: 0 10px;
        }
        
        a.btn-custom {
            text-decoration: none;
        }


        @media print {
            .btn-print-container {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="employee-info">
    <h2>Fiche Employé</h2>
    <div class="info-row"><span class="info-label">Nom :</span> <?php echo $employee['NOM_EMP']; ?></div>
    <div class="info-row"><span class="info-label">Prénom :</span> <?php echo $employee['PRENOM_EMP']; ?></div>
    <div class="info-row"><span class="info-label">Email :</span> <?php echo $employee['EMAIL_EMP']; ?></div>
    <div class="info-row"><span class="info-label">Téléphone :</span> <?php echo $employee['TEL_EMP']; ?></div>
    <div class="info-row"><span class="info-label">Adresse :</span> <?php echo $employee['ADRESSE_EMP']; ?></div>
    <div class="info-row"><span class="info-label">Date de naissance :</span> <?php echo $employee['DATE_EMP']; ?></div>
    <div class="info-row"><span class="info-label">Service :</span> <?php echo $employee['NOM_SERVICE']; ?></div>
    <div class="info-row"><span class="info-label">Nombre d'enfants :</span> <?php echo $employee['NOMBRE_D_ENFANT']; ?></div>
    <div class="info-row"><span class="info-label">Date d'embauche :</span> <?php echo $employee['DATEEMBAUCH_EMP']; ?></div>
</div>

<div class="btn-print-container">
    <a href="dashboard.php" class="btn-custom">Retour</a>
    <button class="btn-custom" onclick="window.print()">Imprimer</button>
</div>

</body>
</html>
