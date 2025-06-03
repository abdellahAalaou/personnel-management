<?php
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Employee.php';
require_once __DIR__ . '/../classes/Salary.php';
require_once __DIR__ . '/../classes/Vacation.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php'); 
    exit;
}

$employeeId = $_SESSION['user_id'];
$employeeModel = new Employee();
$salaryModel = new Salary();
$vacationModel = new Vacation();

try {
     
    $employee = $employeeModel->getById($employeeId);
    $salaries = $salaryModel->getByEmployee($employeeId);
    $totalSalary = $salaryModel->getTotalByEmployee($employeeId);
    $vacations = $vacationModel->getByEmployee($employeeId);

    
    $usedVacationDays = 0;
    foreach ($vacations as $vacationItem) {
        if (strtolower($vacationItem['ETAT']) === 'accepted') {
            $start = new DateTime($vacationItem['DATE_DEBUT_CGE']);
            $end = new DateTime($vacationItem['DATE_FIN_CGE']);
            $usedVacationDays += $start->diff($end)->days + 1;
        }
    }


   
    $totalVacationDays = $employee['NOMBRE_JOURS_CONGE'] ?? 30; 
    $remainingVacationDays = $totalVacationDays - $usedVacationDays;

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/empstyle.css">
<style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap');

body {
                    font-family: "Cairo", sans-serif;
            background-color: #f4f7fb;
        }
        .sidebar {
            width: 280px;
            background-color: #1a202c;
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        .sidebar ul {
            padding: 0;
            list-style-type: none;
        }
        .sidebar ul li {
            padding: 15px 20px;
            border-bottom: 1px solid #4a5568;
        }
        .sidebar ul li a {
            color: white;
            font-size: 16px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar ul li a:hover {
            background-color: #4a5568;
            border-radius: 8px;
        }
        .main-content {
            margin-left: 280px;
            padding: 20px;
        }
        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .card-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2d3748;
        }
        .card-body {
            margin-top: 15px;
        }
        .button {
            background-color: #3182ce;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .button:hover {
            background-color: #2b6cb0;
        }
        .navbar {
            background-color: #2d3748;
            padding: 15px;
            color: white;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .info-item {
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #3182ce;
        }
        .metric-card {
            background: linear-gradient(135deg, #3182ce, #2b6cb0);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .metric-value {
            font-size: 2rem;
            font-weight: bold;
            margin: 10px 0;
        }
    </style> 
</head>
<body>

    <div class="sidebar">
        <div class="">
            <img src="../img/evoltecbg.png" alt="">
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-home"></i> tableau de bord</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                <li><a href="submit_vacation.php"><i class="fas fa-calendar"></i>Congé</a></li>
                <li><a href="demande_attestation.php"><i class="fas fa-file-alt"></i>Attestation de travail</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i>Deconnection</a></li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="navbar">
            <h1 class="text-2xl font-bold">BIENVENUE, <?php echo htmlspecialchars($employee['NOM_EMP'] . ' ' . $employee['PRENOM_EMP']); ?>!</h1>
        </div>

        <div class="info-grid">
            <div class="metric-card">
                <i class="fas fa-money-bill-wave text-3xl"></i>
                <div class="metric-value"><?php echo number_format($totalSalary, 2); ?> MAD</div>
                <div>SALAIRE MENSUEL</div>
            </div>
            <div class="metric-card">
                <i class="fas fa-calendar-check text-3xl"></i>
                <div class="metric-value"><?php echo $totalVacationDays; ?></div>
                <div>JOURS DE CONGE RESTANTS</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Informations personnelles</h2>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <h3 class="font-semibold">Employee ID</h3>
                    <p><?php echo htmlspecialchars($employee['ID_EMP']); ?></p>
                </div>
                <div class="info-item">
                    <h3 class="font-semibold">Email</h3>
                    <p><?php echo htmlspecialchars($employee['EMAIL_EMP']); ?></p>
                </div>
                <div class="info-item">
                    <h3 class="font-semibold">Téléphone</h3>
                    <p><?php echo htmlspecialchars($employee['TEL_EMP']); ?></p>
                </div>
                <div class="info-item">
                    <h3 class="font-semibold">Service</h3>
                    <p><?php echo htmlspecialchars($employee['NOM_SERVICE']); ?></p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Informations sur le salaire</h2>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <h3 class="font-semibold">Salaire actuel</h3>
                        <p class="text-green-500 font-bold"><?php echo number_format($totalSalary, 2); ?> MAD</p>
                    </div>
                    <div class="info-item">
                        <h3 class="font-semibold">Dernier paiement</h3>
                        <p><?php echo $salaries[0]['DATE_PAIEMENT'] ?? 'Aucun paiement effectué à ce jour'; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Informations sur les congés</h2>
                <a href="submit_vacation.php" class="button">Demander des congés</a>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <h3 class="font-semibold">Total des jours de congé</h3>
                        <p><?php echo $totalVacationDays; ?> jours</p>
                    </div>
                    <div class="info-item">
                        <h3 class="font-semibold">Jours de congé utilisés</h3>
                        <p><?php echo $usedVacationDays; ?>  jours</p>
                    </div>
                        <?php echo $remainingVacationDays; ?> days</p>
                </div>
                <h3 class="text-lg font-semibold mt-4">Demandes de congé récentes</h3>
                <ul class="list-disc pl-5 mt-2">
                    <?php foreach (array_slice($vacations, 0, 3) as $vacationItem) { ?>
                        <li class="mb-2">
                            <p>Du: <?php echo $vacationItem['DATE_DEBUT_CGE']; ?> Au: <?php echo $vacationItem['DATE_FIN_CGE']; ?></p>
                            <p class="text-sm text-gray-500">État: <?php echo $vacationItem['ETAT'] ?? 'Unknown'; ?></p>

                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
