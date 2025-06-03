<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Employee.php';
require_once '../classes/Absence.php';
require_once '../classes/Vacation.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$employee = new Employee();
$employees = $employee->getAll();

$absence = new Absence();
$absences = $absence->getAll();

$vacation = new Vacation();
$vacations = $vacation->getAll()

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - HR Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
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
        
        <div class="main-content">
            <header>
                <h1>Tableau de bord d'administration</h1>
                <div class="user-info">
                    Bienvenue, <?php echo $_SESSION['username']; ?>
                </div>
            </header>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>TOTAL EMPLOYEE</h3>
                    <p><?php echo count($employees); ?></p>
                </div>
                <div class="stat-card">
                    <h3>CONGE EN ATTENTE</h3>
                    <p><?php echo count($vacations); ?></p>
                </div>
                <div class="stat-card">
                    <h3>ABSENCE RECENTES</h3>
                    <p><?php echo count($absences); ?></p>
                </div>
            </div>

            <div class="recent-activities">
                <h2>Activités récentes</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Role</th>
                            <th>Service</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $employee): ?>
                            <tr>
                                <td><?php echo $employee['ID_EMP']; ?></td>
                                <td><?php echo $employee['NOM_EMP'] . ' ' . $employee['PRENOM_EMP']; ?></td>
                                <td><?php echo $employee['ROLES']; ?></td>
                                <td><?php echo $employee['NOM_SERVICE']; ?></td>
                                <td><?php echo $employee['EMAIL_EMP']; ?></td>
                                <td>
                                <a href="view_employee.php?id=<?php echo $employee['ID_EMP']; ?>">
                                        <i class="fas fa-eye"></i>
                                </a>
                                <a href="update_employee.php?id=<?php echo $employee['ID_EMP']; ?>">
                                        <i class="fas fa-edit"></i> 
                                </a>
                                <a href="delete_employee.php?id=<?php echo $employee['ID_EMP']; ?>" onclick="return confirm('Etes-vous sur de vouloir supprimer cet employé')" title="Supprimer">
                                        <i class="fas fa-trash-alt" style="color: red;"></i>
                                </a>
                               <a href="print_employee.php?id=<?php echo $employee['ID_EMP']; ?>" target="_blank" title="Imprimer">
    <i class="fa-solid fa-print"></i>
</a>

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

    .recent-activities table tbody tr td a:nth-child(1) i {
        color: #3498db; 
    }
    .recent-activities table tbody tr td a:nth-child(2) i {
        color: #f39c12; 
    }
    .recent-activities table tbody tr td a:nth-child(3) i {
        color: #e74c3c; 
    }
    .recent-activities table tbody tr td a:nth-child(4) i {
        color: #2ecc71; 
    }
    </style>
</body>
</html> 