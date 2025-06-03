<?php
require_once '../classes/Contract.php';
require_once '../classes/User.php';
require_once '../classes/Employee.php'; 


session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$contract = new Contract();
$user = new User();


$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'create':
            $data = [
                'id_emp' => $_POST['ID_EMP'],
                'type_contrat' => $_POST['TYPE_CONTRAT'],
                'fonction' => $_POST['FONCTION'],
                'qualification' => $_POST['QUALIFICATION'],
                'categorie' => $_POST['CATEGORIE'],
                'echelon' => $_POST['ECHLAN'],
                'nbr_h_mois' => $_POST['NBR_H_MOIS'],
                'nbr_h_jours' => $_POST['NBR_H___JOURS'],
                'type_paie' => $_POST['TYPE_DE_PAIE']
            ];
            if ($contract->create($data)) {
                $message = "<p class='alert alert-success'>Contrat créé avec succès.</p>";
            } else {
                $message = "<p class='alert alert-error'>.</p>";
            }
            break;

        case 'update':
            $data = [
                'id' => $_POST['N__CONTRAT'],
                'type_contrat' => $_POST['TYPE_CONTRAT'],
                'fonction' => $_POST['FONCTION'],
                'qualification' => $_POST['QUALIFICATION'],
                'categorie' => $_POST['CATEGORIE'],
                'echelon' => $_POST['ECHLAN'],
                'nbr_h_mois' => $_POST['NBR_H_MOIS'],
                'nbr_h_jours' => $_POST['NBR_H___JOURS'],
                'type_paie' => $_POST['TYPE_DE_PAIE']
            ];
            if ($contract->update($data)) {
                $message = "<p class='alert alert-success'>Contrat mis à jour avec succès.</p>";
            } else {
                $message = "<p class='alert alert-error'>La mise à jour du contrat a échoué.</p>";
            }
            break;

        case 'delete':
            if ($contract->delete($_POST['N__CONTRAT'])) {
                $message = "<p class='alert alert-success'>Contrat supprimé avec succès.</p>";
            } else {
                $message = "<p class='alert alert-error'>La suppression du contrat a échoué.</p>";
            }
            break;
    }
}

$contracts = $contract->getAll();
$employees = $user->getAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../assets/css/style.css">
    <meta charset="UTF-8">
    <title>Contract Management</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="dashboard-container" >
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
            <?= $message ?>

            <h1> Gestion des contrats</h1>
            <h2>Ajouter un nouveau contrat</h2>
            <form method="POST">
                <input type="hidden" name="action" value="create">

                <label>Employee:</label>
                <select name="ID_EMP" required>
                    <option value=""> Sélectionner un employé</option>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= htmlspecialchars($emp['ID_EMP']) ?>">
                            <?= htmlspecialchars($emp['NOM_EMP'] . ' ' . $emp['PRENOM_EMP']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="text" name="TYPE_CONTRAT" placeholder="Type de contrat" required>
                <input type="text" name="FONCTION" placeholder="Fonction" required>
                <input type="text" name="QUALIFICATION" placeholder="Qualification" required>
                <input type="text" name="CATEGORIE" placeholder="Category" required>
                <input type="text" name="ECHLAN" placeholder="Echelon" required>
                <input type="text" name="NBR_H_MOIS" placeholder="Heures par mois" required>
                <input type="text" name="NBR_H___JOURS" placeholder="Heures par jour" required>
                <input type="text" name="TYPE_DE_PAIE" placeholder="Type de paiement" required>

                <input type="submit" value="Ajouter un contrat">
            </form>
        </div>

        <div class="absenceTable">
            <h2>Contrats existants</h2>
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Employee</th>
                    <th>Type</th>
                    <th>Function</th>
                    <th>Qualification</th>
                    <th>Category</th>
                    <th>Echelon</th>
                    <th>H/Mo</th>
                    <th>H/Day</th>
                    <th>type_paie</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($contracts as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['ID_EMP']) ?></td>
                        <td><?= htmlspecialchars($c['NOM_EMP'] . ' ' . $c['PRENOM_EMP']) ?></td>
                        <form method="POST">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="N__CONTRAT" value="<?= $c['N__CONTRAT'] ?>">
                            <td><input type="text" name="TYPE_CONTRAT" value="<?= htmlspecialchars($c['TYPE_CONTRAT']) ?>" required style="width:50px;"></td>
                            <td><input type="text" name="FONCTION" value="<?= htmlspecialchars($c['FONCTION']) ?>" required></td>
                            <td><input type="text" name="QUALIFICATION" value="<?= htmlspecialchars($c['QUALIFICATION']) ?>" required style=""></td>
                            <td><input type="text" name="CATEGORIE" value="<?= htmlspecialchars($c['CATEGORIE']) ?>" required style ="width:90px;"></td>
                            <td><input type="text" name="ECHLAN" value="<?= htmlspecialchars($c['ECHLAN']) ?>" required></td>
                            <td><input type="text" name="NBR_H_MOIS" value="<?= htmlspecialchars($c['NBR_H_MOIS']) ?>" required></td>
                            <td><input type="text" name="NBR_H___JOURS" value="<?= htmlspecialchars($c['NBR_H___JOURS']) ?>" required></td>
                            <td><input type="text" name="TYPE_DE_PAIE" value="<?= htmlspecialchars($c['TYPE_DE_PAIE']) ?>" required></td>
                            <td>
                                <input type="submit" value="Modifier" style="width: auto;">
                        </form>
                        <form method="POST" onsubmit="return confirm('Voulez-vous supprimer ce contrat ?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="N__CONTRAT" value="<?= $c['N__CONTRAT'] ?>">
                            <input type="submit" value="Supprimer" style="background:#e74c3c; width:auto; margin-top: 10px;">
                        </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap');

    body {
        font-family: Arial, sans-serif;
        background-color: #f4f6f9;
        font-family: "Cairo", sans-serif;
    }
    .dashboard-container {
        display: flex;
        min-height: 100vh;
    }
    /* .sidebar .logo2 {
        height: 50px;
        width: 90%;
        margin-bottom: 2rem;
    } */
    .sidebar{
        width:340px;
        padding: 1rem;
    }
    .sidebar h2 {
        margin-bottom: 2rem;
        text-align: center;
    }
    .sidebar ul {
        list-style: none;
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
    }
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
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #ddd;
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
        background-color:rgb(130, 240, 156);
        color:rgba(33, 114, 52, 0.82) ;
    }
    .alert-error {
        background-color: #dc3545;
    }
    .alert-info {
        background-color: #17a2b8;
    }
  
</style>
</body>
</html>
