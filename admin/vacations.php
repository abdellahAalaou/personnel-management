<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../classes/Employee.php'; 
require_once '../classes/Vacation.php';

$vacation = new Vacation();
$employee = new Employee();
$requests = $vacation->getAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];
    $status = $_POST['status']; 

    $vac = $vacation->read($request_id); 
    $previousStatus = strtolower($vac['ETAT'] ?? '');

    if (in_array($previousStatus, ['acceptée', 'refusée'])) {
        $_SESSION['message'] = "Cette demande est déjà " . $previousStatus . ".";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if ($vacation->updateStatus($request_id, $status)) {
        $_SESSION['message'] = "Demande de congé : statut modifié.";
    } else {
        $_SESSION['message'] = "Erreur lors de la mise à jour du statut.";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
<div class="dashboard-container">
    <div class="sidebar">
        <img class="logo2" src="../img/evolteclogo-Photoroom.png" alt="" />
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

    <div class="table_vac">
        <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

        <?php
        if (isset($_SESSION['message'])) {
            echo "<p style='color: green; font-weight: bold;'>" . htmlspecialchars($_SESSION['message']) . "</p>";
            unset($_SESSION['message']);
        }
        ?>

        <h2>Toutes les demandes de vacances</h2>
        <?php if ($requests): ?>
            <table>
                <thead>
                    <tr>
                        <th>Identifiant employé</th>
                        <th>Nom de l'employé</th>
                        <th>Date début</th>
                        <th>Date fin</th>
                        <th>État</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($request['ID_EMP']); ?></td>
                            <td><?php echo htmlspecialchars($request['NOM_EMP'] . ' ' . $request['PRENOM_EMP']); ?></td>
                            <td><?php echo htmlspecialchars($request['DATE_DEBUT_CGE']); ?></td>
                            <td><?php echo htmlspecialchars($request['DATE_FIN_CGE']); ?></td>
                            <td><?php echo ucfirst(strtolower($request['ETAT'])); ?></td>
                            <td>
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request['N__CGE']); ?>" />
                                    
                                    <button type="submit" 
                                            name="status" 
                                            value="acceptée" 
                                            class="btn-accept"
                                            <?php echo in_array(strtolower(trim($request['ETAT'])), ['acceptée', 'refusée']) ? 'disabled' : ''; ?>
                                            onclick="return confirm('Accepter cette demande de congé ?');">
                                        Accepte
                                    </button>
                                    
                                    <button type="submit" 
                                            name="status" 
                                            value="refusée"  
                                            class="btn-reject"
                                            <?php echo in_array(strtolower(trim($request['ETAT'])), ['acceptée', 'refusée']) ? 'disabled' : ''; ?>
                                            onclick="return confirm('Refuser cette demande de congé ?');">
                                        Refuse
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune demande de congé disponible.</p>
        <?php endif; ?>
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap');

.table_vac {
    padding: 20px;
    width: 100%;
}

.sidebar {
    width: 283px;
    padding: 1rem;
}

.btn-accept {
    background-color: #28a745;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    font-size: 14px;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.btn-reject {
    background-color: #dc3545;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    font-size: 14px;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.btn-accept:hover:not(:disabled) {
    background-color: #218838;
    transform: translateY(-2px);
}

.btn-reject:hover:not(:disabled) {
    background-color: #c82333;
    transform: translateY(-2px);
}

button[type="submit"]:disabled {
    background-color: #17a2b8;
    color: white !important;
    cursor: not-allowed !important;
    opacity: 0.65;
}
</style>
</body>
</html>
