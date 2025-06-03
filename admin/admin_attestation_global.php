<?php
require_once '../classes/attestation.php';
require_once '../classes/Employee.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}


$attestation = new Attestation();
$employee = new Employee();

$attestations = $attestation->getAll();




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valider_id']) && is_numeric($_POST['valider_id'])) {
    $attestation->updateStatus($_POST['valider_id'], 'Acceptée');
    header("Location: admin_attestation_global.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <title>Gestion des Attestations</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap');

        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
 background-color: #34495e;
    color: #fff;        
}
        form {
            display: inline;
        }
        .btn {
            background-color: #2ecc71;
        color: white;
        padding: 5px 18px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        margin-right: 5px;
        text-decoration: none;
            
        }
        .btn:hover {
            background-color: #218838;
        }
        .all{
            width:100%;
            margin:20px;
                font-family: "Cairo", sans-serif;

        }

        .dashboard-container{
                font-family: "Cairo", sans-serif;
        }
        .sidebar {
            width: 289px;
            background:34495e;
            color: white;
            padding: 1rem;
  
}
        .sidebar .logo2{
        height: 50px;
        width: 90%;
        margin-bottom: 2rem;
        
    }
    </style>

</head>
<body>
<div class="dashboard-container">
        <div class="sidebar">
            <img class="logo2" src="../img/evolteclogo-Photoroom.png" alt="">
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Tableau de bord</a></li>
                <li><a href="create_employee.php"><i class="fas fa-user-plus"></i> Ajouter un employé </a></li>
                <li><a href="employees.php"><i class="fas fa-users"></i> Employees</a></li>
                <li><a href="absences.php"><i class="fas fa-calendar-times"></i> Absences</a></li>
                <li><a href="vacations.php"><i class="fas fa-umbrella-beach"></i> Conges</a></li>
                <li><a href="contracts.php"><i class="fas fa-file-signature"></i> Contrats</a></li>
                <li><a href="admin_attestation_global.php"><i class="fas fa-file-alt"></i> Attestation de travail</a></li>
                <li><a href="salaire.php"><i class="fas fa-money-bill-wave"></i> Salaires</a></li>
                <li><a href="diplome.php"><i class="fas fa-graduation-cap"></i> Diplome</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Deconnection</a></li>
            </ul>

        </div>
    
<div class="all">
    <div class="formulaire">
        <h2>Liste des demandes d'attestations de travail</h2>
    </div>
<div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Employé</th>
                <th>Motif</th>
                <th>Date de Demande</th>
                <th>Statut</th>
                <th>Date Attestation</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($attestations as $a): 
                $emp = $employee->getById($a['ID_EMP']);
                ?>
                <tr>
                    <td><?= $a['ID_ATTESTATION'] ?></td>
                    <td><?= $emp['NOM_EMP'] . ' ' . $emp['PRENOM_EMP'] ?></td>
                    <td><?= $a['MOTIF'] ?></td>
                    <td><?= date('d/m/Y', strtotime($a['DATE_DEMANDE'])) ?></td>
                    <td><?= $a['STATUT'] ?></td>
                    <td><?= $a['DATE_ATTESTATION'] ? date('d/m/Y', strtotime($a['DATE_ATTESTATION'])) : '---' ?></td>
                    <td>
                        <?php if ($a['STATUT'] === 'En attente'): ?>
                            <form method="POST">
                                <input type="hidden" name="valider_id" value="<?= $a['ID_ATTESTATION'] ?>">
                                <button type="submit" class="btn">Valider</button>
                            </form>
                        <?php endif; ?>
                        <?php if ($a['STATUT'] === 'Acceptée'): ?>
                            <a class="btn" href="imprimer_attestation.php?id=<?= $a['ID_ATTESTATION'] ?>" target="_blank">Imprimer</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        </table>
</div>
</div>
</div>


</body>
</html>
