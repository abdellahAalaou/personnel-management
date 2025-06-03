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
    
    if ($vacation->updateStatus($request_id, $status)) {
        if ($status === 'accepted' && $previousStatus !== 'accepted') {
            
            if ($vac) {
                $start = new DateTime($vac['DATE_DEBUT_CGE']);
                $end = new DateTime($vac['DATE_FIN_CGE']);
                $vacation_days = $start->diff($end)->days + 1;
    
                $emp = $employee->getById($vac['ID_EMP']);
                if ($emp) {
                    $remaining_days = (int)$emp['NOMBRE_JOURS_CONGE'];
                    $new_remaining = max(0, $remaining_days - $vacation_days);
    
                    $update_data = [
                        'service_id' => $emp['N__SERVICE'],
                        'nom' => $emp['NOM_EMP'],
                        'prenom' => $emp['PRENOM_EMP'],
                        'date_emp' => $emp['DATE_EMP'],
                        'tel' => $emp['TEL_EMP'],
                        'email' => $emp['EMAIL_EMP'],
                        'adresse' => $emp['ADRESSE_EMP'],
                        'date_embauche' => $emp['DATEEMBAUCH_EMP'],
                        'nbr_enfants' => $emp['NOMBRE_D_ENFANT'],
                        'username' => $emp['NOM_UTILISATEUR'],
                        'jours_conge' => $new_remaining,
                        'id' => $emp['ID_EMP']
                    ];
                    $employee->update($update_data);
                }
            }
        }
    
        $message = "Demande de congé : statut modifié.";
    } else {
        $message = "Erreur lors de la mise à jour du statut de la demande.";
    }
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
    <div class= "table_vac">

        <h1>Bienvenue, <?php echo $_SESSION['username']; ?>!</h1>
    
        <?php if (isset($message)) echo "<p>$message</p>"; ?>
    
        <h2>Toutes les demandes de vacances</h2>
        <?php if ($requests): ?>
            <table>
                <thead>
                    <tr>
                        <th>Identifiant employé</th>
                        <th>Nom de l'employé</th>
                        <th>Date debut</th>
                        <th>Date fin</th>
                        <th>ETAT</th>
                        <th>Action</th>
                    </tr>
                </thead>
    </div>

            <div>
                <tbody>
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td><?php echo $request['ID_EMP']; ?></td>
                            <td><?php echo $request['NOM_EMP'] . ' ' . $request['PRENOM_EMP']; ?></td>
                            <td><?php echo $request['DATE_DEBUT_CGE']; ?></td>
                            <td><?php echo $request['DATE_FIN_CGE']; ?></td>
                            <td><?php echo ucfirst($request['ETAT']); ?></td>
                            <td>

                           <form method="POST">
    <input type="hidden" name="request_id" value="<?php echo $request['N__CGE']; ?>">
    
    <button type="submit" 
        name="status" 
        value="accepted" 
        class="btn-accept"
        <?php echo strtolower($request['ETAT']) === 'accepted' ? 'disabled' : ''; ?>
        onclick="return confirm('Accepter cette demande de congé ?');">
    Accepte
</button>

<button type="submit" 
        name="status" 
        value="rejected"  
        class="btn-reject"
        <?php echo strtolower($request['ETAT']) === 'accepted' ? 'disabled' : ''; ?>
        onclick="return confirm('Refuser cette demande de congé ?');">
    Refuse
</button>

</form>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </div>
        </table>
    <?php else: ?>
        <p>Aucune demande de congé disponible.</p>
    <?php endif; ?>
</div>

<style>
     @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap');
   

     .table_vac {
        padding:20px ;
        width:100%;
       
    }
    .sidebar{
        width:283px;
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
    cursor: pointer !important;
    opacity: 0.65;
}


    
</style>
</body>
</html>
