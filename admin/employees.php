<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Employee.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$employee = new Employee();
$employees = $employee->getAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Employees - HR Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap');
        body{
                font-family: "Cairo", sans-serif;

        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 0 20px;
        }

        header h1 {
            margin: 0;
            font-size: 24px;
        }

        .search-container {
            position: relative;
            width: 300px;
        }

        .search-container i {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #888;
        }

        .search-container input {
            width: 100%;
            padding: 8px 8px 8px 35px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn-print {
            margin-bottom: 20px;
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-print i {
            margin-right: 0;
        }

        @media print {
            .sidebar,
            .search-container,
            .btn-print,
            .actions-column {
                display: none !important;
            }

            body {
                margin: 0;
                padding: 20px;
            }

            .main-content {
                width: 100%;
                margin: 0;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th,
            td {
                border: 1px solid #000;
                padding: 8px;
                font-size: 14px;
            }
        }

        .sidebar img {
            height: 120px;
            width: 200px;
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

        <div class="main-content">
            <header>
                <h1>Employees</h1>
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input
                        type="text"
                        id="searchInput"
                        placeholder="Rechercher..."
                        onkeyup="filterEmployees()"
                    />
                </div>
                <button onclick="printEmployees()" class="btn-print">
                    <i class="fas fa-print"></i> Imprimer
                </button>
            </header>

            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prenom</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th class="actions-column">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $emp): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($emp['NOM_EMP']); ?></td>
                        <td><?php echo htmlspecialchars($emp['PRENOM_EMP']); ?></td>
                        <td><?php echo htmlspecialchars($emp['EMAIL_EMP']); ?></td>
                        <td><?php echo htmlspecialchars($emp['TEL_EMP']); ?></td>
                        <td class="actions-column">
                            <a href="update_employee.php?id=<?php echo $emp['ID_EMP']; ?>" title="Edit">
                                <i class="fas fa-edit" style="color:#007bff;"></i>
                            </a>
                            &nbsp;
                            <a
                                href="delete_employee.php?id=<?php echo $emp['ID_EMP']; ?>"
                                title="Delete"
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet employé ?')"
                            >
                                <i class="fas fa-trash-alt" style="color:#dc3545;"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function filterEmployees() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach((row) => {
                const nom = row.children[0].textContent.toLowerCase();
                const prenom = row.children[1].textContent.toLowerCase();
                const phone = row.children[3].textContent.toLowerCase();

                if (nom.includes(input) || prenom.includes(input) || phone.includes(input)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function printEmployees() {
            const originalTable = document.querySelector('table');
            const cloneTable = originalTable.cloneNode(true);
            
           
            const rows = cloneTable.querySelectorAll('tr');
            rows.forEach(row => {
                const actionsCell = row.querySelector('.actions-column');
                if (actionsCell) {
                    row.removeChild(actionsCell);
                }
            });

            const win = window.open('', '', 'width=900,height=700');
            win.document.write('<html><head><title>Liste des employés</title>');
            win.document.write('<style>');
            win.document.write('body { font-family: Arial, sans-serif; margin: 20px; }');
            win.document.write('h2 { color: #333; text-align: center; }');
            win.document.write('table { width: 100%; border-collapse: collapse; margin-top: 20px; }');
            win.document.write('th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }');
            win.document.write('th { background-color: #f2f2f2; }');
            win.document.write('.logo { display: block; margin: 0 auto 20px; width: 200px; }');
            win.document.write('</style>');
            win.document.write('</head><body>');
            win.document.write('<img src="../img/evoltecbg.png" alt="Evoltec Logo" class="logo">');
            win.document.write('<h2>Liste des employés</h2>');
            win.document.write(cloneTable.outerHTML);
            win.document.write('</body></html>');
            win.document.close();
            win.print();
        }
    </script>
</body>
</html>