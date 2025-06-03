<?php
require_once '../classes/Absence.php';
require_once '../classes/User.php'; 

    session_start();

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$absence = new Absence();
$user = new User();

$absence = new Absence();
$absences = $absence->getAll();




if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $absence->ID_EMP = $_POST['ID_EMP'];
                $absence->DUREE_ABS = $_POST['DUREE_ABS'];
                $absence->DATE = $_POST['DATE'];
                $absence->MOTIF = $_POST['MOTIF'];
                $absence->JUSTIF = $_POST['JUSTIF'];

                if ($absence->create()) {
                    echo "<p style='color:green;'>L'absence a bien été enregistrée.</p>";
                } else {
                    echo "<p style='color:red;'>La création de l'absence a échoué.</p>";
                }
                break;

            case 'update':
                $absence->N__ABS = $_POST['N__ABS'];
                $absence->DUREE_ABS = $_POST['DUREE_ABS'];
                $absence->DATE = $_POST['DATE'];
                $absence->MOTIF = $_POST['MOTIF'];
                $absence->JUSTIF = $_POST['JUSTIF'];

                if ($absence->update()) {
                    echo "<p style='color:green;'>L'absence a été modifiée avec succès.</p>";
                } else {
                    echo "<p style='color:red;'>Failed to update absence.</p>";
                }
                break;

            case 'delete':
                $absence->N__ABS = $_POST['N__ABS'];

                if ($absence->delete()) {
                    echo "<p style='color:green;'>Absence supprimée avec succès.</p>";
                } else {
                    echo "<p style='color:red;'>Échec de la suppression de l'absence.</p>";
                }
                break;
        }
    }
}

$absences = $absence->getAll();

$allEmployees = $user->getAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <title>Absence Management</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap');

        .all{
                font-family: "Cairo", sans-serif;

        }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        form { margin-bottom: 20px; }
        input[type="text"], input[type="date"], select {
            padding: 5px; margin: 5px 0;
            width: 100%; box-sizing: border-box;
        }
        input[type="submit"] {
            padding: 8px 16px;
            margin-top: 10px;
            cursor: pointer;
        }
        .dashboard-container{
                font-family: "Cairo", sans-serif;

        }
    </style>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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

        <h1>Gestion des Absences</h1>
    
        <h2>Ajouter une Absence</h2>
        <form method="POST">
            <input type="hidden" name="action" value="create">
    
            <label for="ID_EMP">Employee:</label><br>
            <select name="ID_EMP" required>
                <option value="">-- Choisir Employee --</option>
                <?php foreach ($allEmployees as $emp): ?>
                    <option value="<?= htmlspecialchars($emp['ID_EMP']) ?>">
                        <?= htmlspecialchars($emp['NOM_EMP'] . " " . $emp['PRENOM_EMP']) ?> (<?= htmlspecialchars($emp['NOM_SERVICE']) ?>)
                    </option>
                <?php endforeach; ?>
            </select><br>
    
            <label for="DUREE_ABS">Duration:</label><br>
            <input type="text" name="DUREE_ABS" required><br>
    
            <label for="DATE">Date:</label><br>
            <input type="date" name="DATE" required><br>
    
            <label for="MOTIF">Raison:</label><br>
            <input type="text" name="MOTIF" required><br>
    
            <label for="JUSTIF">Justification:</label><br>
            <select name="JUSTIF" required> 
                <option value="justifier" style=" background-color:green ; color:white;">justifier</option>
                <option value="non justifier" style=" background-color:red ;color:white">non justifier</option>
            </select>
    
            <input class="btnabs" type="submit" value="Ajouter une Absence">
        </form>
    </div> 
    <div class="absenceTable">
    <h2>MODIFICATION DES ABSENCES</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Employee</th>
                <th>Duration</th>
                <th>Date</th>
                <th>Motif</th>
                <th>Justification</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($absences as $a): ?>
            <tr>
                <td><?= htmlspecialchars($a['N__ABS'] ?? '') ?></td>
                <td><?= htmlspecialchars($a['NOM_EMP'] . " " . $a['PRENOM_EMP'] ?? '') ?></td>
                <td><?= htmlspecialchars($a['DUREE_ABS'] ?? '') ?></td>
                <td><?= htmlspecialchars($a['DATE']?? '') ?></td>
                <td><?= htmlspecialchars($a['MOTIF'] ?? '') ?></td>
                <td><?= htmlspecialchars($a['JUSTIF']?? '') ?></td>
                <td>
                    <!-- dyal l update -->
                    <form method="POST" style="display:inline-block; width:48%;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="N__ABS" value="<?= $a['N__ABS'] ?>">

                        <input type="text" name="DUREE_ABS" value="<?= htmlspecialchars($a['DUREE_ABS'] ?? '') ?>" required>
                        <input type="date" name="DATE" value="<?= htmlspecialchars($a['DATE'] ?? '') ?>" required>
                        <input type="text" name="MOTIF" value="<?= htmlspecialchars($a['MOTIF']?? '') ?>" required>
                        <input type="text" name="JUSTIF" value="<?= htmlspecialchars($a['JUSTIF']?? '') ?>" required>

                        <input type="submit" value="Modifier">
                    </form>

                    <!-- dyal Delete  -->
                    <form method="POST" style="display:inline-block; width:48%;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="N__ABS" value="<?= $a['N__ABS'] ?>">
                        <input type="submit" value="Supprimer" onclick="return confirm('Êtes-vous sûr(e) de vouloir supprimer cette absence ?')">
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

 

</body>
<style>
    
    
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        background-color: #f4f6f9;
        margin: 0;
        padding: 0;
    }
    
    .

    /* .sidebar img {
        height: 120px;
        width: 200px;
        margin-bottom: 20px;
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
    }

    .sidebar li {
        margin-bottom: 0.5rem;
    }

    .sidebar a {
        color: white;
        text-decoration: none;
        display: block;
        padding: 0.5rem;
        border-radius: 4px;
    } */

    .sidebar a:hover, .sidebar li.active a {
        background: #34495e;
    }

    .main-content {
        flex: 1;
        padding: 2rem;
        background-color: #f4f6f9;
    }

    h1, h2 {
        color: #2c3e50;
    }

    .formulaire {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }

    .formulaire input[type="text"], 
    .formulaire input[type="date"], 
    .formulaire select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
        margin-bottom: 1rem;
    }

    .formulaire input[type="submit"] {
        padding: 0.75rem 1.5rem;
        background: #3498db;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1rem;
    }

    .formulaire input[type="submit"]:hover {
        background: #2980b9;
    }

    .absenceTable {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .absenceTable table {
        width: 100%;
        border-collapse: collapse;
    }

    .absenceTable th, 
    .absenceTable td {
             padding: 12px 10px;
    border-bottom: 1px solid #ddd;
    text-align: left;
    }

    .absenceTable th {
       background-color: #34495e;
    color: #fff;     
    }

    .absenceTable td input[type="text"], 
    .absenceTable td input[type="date"] {
        padding: 0.5rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        width: 100%;
        margin: 5px 0;
    }

    .absenceTable td input[type="submit"] {
        padding: 0.5rem 1rem;
        background: #3498db;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1rem;
    }

    .absenceTable td input[type="submit"]:hover {
        background: #2980b9;
    }

    .absenceTable form {
        display: inline-block;
        width: 48%;
        margin-right: 2%;
    }

    .absenceTable input[type="submit"] {
        width: 100%;
        padding: 1rem;
        background-color: #e74c3c;
    }

    .absenceTable input[type="submit"]:hover {
        background-color: #c0392b;
    }

    .alert {
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 4px;
        color: white;
    }

    .alert-success {
        background-color: #28a745;
    }

    .alert-error {
        background-color: #dc3545;
    }

    .alert-info {
        background-color: #17a2b8;
    }
</style>

</styl>
</html>
