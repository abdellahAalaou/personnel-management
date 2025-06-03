<?php
session_start();
require_once '../classes/Salary.php';
require_once '../classes/User.php';
require_once '../classes/Employee.php'; 



if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$salary = new Salary();
$user = new User();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Salaires</title>
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
        <?php 
        
        
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'create':
            $data = [
                'id_emp' => $_POST['ID_EMP'],
                'date_paiement' => $_POST['DATE_PAIEMENT'],
                'montant' => $_POST['MONTANT']
            ];
            
            $salary->deleteByEmployeeId($_POST['ID_EMP']);
            
            
            echo $salary->create($data)
                ? "<p class='alert alert-success'>Salaire mis à jour avec succès.</p>"
                : "<p class='alert alert-error'>Échec de l'ajout du salaire.</p>";
            break;

        case 'update':
            $data = [
                'id' => $_POST['ID_SLR'],
                'date_paiement' => $_POST['DATE_PAIEMENT'],
                'montant' => $_POST['MONTANT']
            ];
            echo $salary->update($data)
                ? "<p class='alert alert-success'>Salaire mis à jour.</p>"
                : "<p class='alert alert-error'>Échec de la mise à jour.</p>";
            break;

        case 'delete':
            echo $salary->delete($_POST['ID_SLR'])
                ? "<p class='alert alert-success'>Salaire supprimé.</p>"
                : "<p class='alert alert-error'>Échec de la suppression.</p>";
            break;
    }
}

$salaries = $salary->getAll();
$employees = $user->getAll();
?>
        <div class="formulaire">
            <h1>Gestion des Salaires</h1>
            <h2>Ajouter un nouveau salaire</h2>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <label>Employé :</label>
                <select name="ID_EMP" required>
                    <option value="">-- Sélectionner --</option>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= $emp['ID_EMP'] ?>">
                            <?= $emp['NOM_EMP'] . ' ' . $emp['PRENOM_EMP'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="date" name="DATE_PAIEMENT" required>
                <input type="text" name="MONTANT" placeholder="Montant (DA)" required>
                <input type="submit" value="Ajouter le salaire">
            </form>
        </div>

        <div class="absenceTable">
            <h2>Salaires enregistrés</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employé</th>
                        <th>Date Paiement</th>
                        <th>Montant (DA)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($salaries as $s): ?>
                        <tr>
                            <form method="POST">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="ID_SLR" value="<?= $s['ID_SLR'] ?>">
                                <td><?= $s['ID_SLR'] ?></td>
                                <td><?= $s['NOM_EMP'] . ' ' . $s['PRENOM_EMP'] ?></td>
                                <td><input type="date" name="DATE_PAIEMENT" value="<?= $s['DATE_PAIEMENT'] ?>" required></td>
                                <td><input type="text" name="MONTANT" value="<?= $s['MONTANT'] ?>" required></td>
                                <td>
                                    <input type="submit" value="Mettre à jour">
                            </form>
                            <form method="POST" onsubmit="return confirm('Supprimer ce salaire ?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="ID_SLR" value="<?= $s['ID_SLR'] ?>">
                                <input type="submit" value="Supprimer" style="background: #e74c3c; margin-top: 10px;"
>
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
body{
        font-family: "Cairo", sans-serif;

}
    .all {
    padding: 20px;
    background-color: #f9f9f9;
    flex-grow: 1;
    overflow-y: auto;
    min-height: 100vh;
    box-sizing: border-box;

}

.all h1, .all h2 {
    /* color: #2c3e50; */
    margin-bottom: 15px;
}

.formulaire {
    background: #ffffff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.absenceTable {
    background: #ffffff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.absenceTable table {
    width: 100%;
    border-collapse: collapse;
}

.absenceTable th, .absenceTable td {
    padding: 12px 10px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

.absenceTable th {
    background-color: #34495e;
    color: #fff;
}

input[type="text"], input[type="date"], select {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-sizing: border-box;
}

input[type="submit"] {
    background-color: #2ecc71;
    color: white;
    padding: 10px 18px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    margin-right: 5px;
}

input[type="submit"]:hover {
    background-color: #27ae60;
}

</style>
</body>
</html>
