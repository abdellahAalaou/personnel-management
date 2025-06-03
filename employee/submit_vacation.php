<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: ../login.php");
    exit();
}

require_once '../classes/Vacation.php';
require_once '../classes/Employee.php';  

$vacation = new Vacation();
$employee = new Employee();  
$message = "";
$message_class = "";
$emp = $employee->getById($_SESSION['user_id']);

function getRemainingVacationDays($employee, $vacation, $id_emp) {
    $emp = $employee->getById($id_emp);
    if (!$emp) return 0;
    
    $total_allocated = (int)$emp['NOMBRE_JOURS_CONGE'];
    $used_days = $vacation->getTotalUsedDays($id_emp);
    $remaining = $total_allocated - $used_days;
    return max(0, $remaining);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'id_emp' => $_SESSION['user_id'],
        'date_debut' => $_POST['date_debut'],
        'date_fin' => $_POST['date_fin']
    ];

    $start = strtotime($data['date_debut']);
    $end = strtotime($data['date_fin']);
    
    if ($start > $end) {
        $message = "La date de début doit être inférieure à la date de fin.";
        $message_class = "error";
    } else {
        $requested_days = floor(($end - $start) / (60*60*24)) + 1;
        $remaining_days = getRemainingVacationDays($employee, $vacation, $data['id_emp']);

        if ($requested_days > $remaining_days) {
            $message = "Vous ne pouvez pas demander plus de jours que votre solde de congés restant ($remaining_days).";
            $message_class = "error";
        } else {
            if ($vacation->checkConflict($data['id_emp'], $data['date_debut'], $data['date_fin'])) {
                $message = "Conflit avec une demande de congé existante !";
                $message_class = "error";
            } elseif ($vacation->create($data)) {
                $message = "Demande de congé enregistrée avec succès !";
                $message_class = "success";
            } else {
                $message = "Erreur lors de l'envoi de la demande.";
                $message_class = "error";
            }
        }
    }
}

$requests = $vacation->getByEmployee($_SESSION['user_id']);
$remaining_days = getRemainingVacationDays($employee, $vacation, $_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord employé</title>
    <link rel="stylesheet" href="../assets/css/empstyle.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
        <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
    
        <h2>Soumettre une nouvelle demande de congé</h2>
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_class; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <label for="date_debut">Date de début:</label>
            <input type="date" name="date_debut" required min="<?= date('Y-m-d') ?>">

            <label for="date_fin">Date fin:</label>
            <input type="date" name="date_fin" required min="<?= date('Y-m-d') ?>">
            
            <button type="submit">Envoyer</button>
        </form>
    
        <hr>
    
        <h2>Vos demandes de congé</h2>
        <?php if ($requests): ?>
            <table border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th>Début</th>
                        <th>Fin</th>
                        <th>Etat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $req): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($req['DATE_DEBUT_CGE']); ?></td>
                            <td><?php echo htmlspecialchars($req['DATE_FIN_CGE']); ?></td>
                            <td><?php echo ucfirst(htmlspecialchars($req['ETAT'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Vous n'avez encore soumis aucune demande de congé.</p>
        <?php endif; ?>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap');

        body{
            margin: 0;
            font-family: "Cairo", sans-serif;
            padding: 0;
        }
        .sidebar{
            margin: 0;
            padding: 0;
        }
        /* .main-content {
            margin-left: 280px;
            padding: 30px;
        } */

        h1 {
            font-size: 26px;
            margin-bottom: 20px;
        }

        /* form {
            margin-bottom: 30px;
        } */

        /* label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        } */

        /* input[type="date"],
        button {
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 10px;
            width: 100%;
            max-width: 300px;
            box-sizing: border-box;
        } */

        /* button {
            background-color: #3498db;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #2980b9;
        } */
         /* <style> */
    /* Style spécifique pour le formulaire de demande de congé */
    .main-content form {
        max-width: 500px;
        margin: 20px 0;
        padding: 25px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .main-content form label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #2b6cb0;
    }

    .main-content input[type="date"] {
        width: 100%;
        padding: 12px;
        margin-bottom: 20px;
        border: 2px solid #e2e8f0;
        border-radius: 6px;
        font-size: 16px;
        transition: all 0.3s ease;
        box-sizing: border-box;
        font-family: "Cairo", sans-serif;
    }

    .main-content input[type="date"]:focus {
        border-color: #2b6cb0;
        outline: none;
        box-shadow: 0 0 0 3px rgba(43, 108, 176, 0.1);
    }

    .main-content form button {
        width: 100%;
        padding: 12px;
        background-color: #2b6cb0;
        color: white;
        font-size: 16px;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .main-content form button:hover {
        background-color: #1e4e8c;
    }

    /* Animation lors du clic */
    .main-content form button:active {
        transform: translateY(1px);
    }

    /* Style pour les messages */
    .message {
        padding: 15px;
        margin: 20px 0;
        border-radius: 4px;
        text-align: center;
        font-weight: bold;
    }

    .success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

     table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #2980b9;
            color: white;
        }

        .message {
            padding: 12px;
            margin: 15px 0;
            border-radius: 4px;
            font-weight: bold;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
</style>

       
    </style>
</body>
</html>