<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Employee.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$message = "";
$error = "";

try {
    $database = new Database();
    $db = $database->getConnection();


    $employee_id = isset($_GET['id']) ? $_GET['id'] : null;
    if (!$employee_id) {
        header('Location: employees.php');
        exit();
    }
    $services_query = "SELECT N__SERVICE, NOM_SERVICE FROM service";
    $services_stmt = $db->prepare($services_query);
    $services_stmt->execute();
    $services = $services_stmt->fetchAll(PDO::FETCH_ASSOC);


    $employee = new Employee();
    $employee_data = $employee->read($employee_id);

    if (!$employee_data) {
        header('Location: employees.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
       
        $required_fields = ['service_id', 'nom', 'prenom', 'date_emp', 'tel', 'email', 
                          'adresse', 'date_embauche', 'nbr_enfants', 'username', 'role'];
        
        $missing_fields = [];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $missing_fields[] = $field;
            }
        }

        if (!empty($missing_fields)) {
            $error = "Please fill in all required fields: " . implode(", ", $missing_fields);
        } else {
            $data = array(
                'id' => $employee_id,
                'service_id' => $_POST['service_id'],
                'nom' => $_POST['nom'],
                'prenom' => $_POST['prenom'],
                'date_emp' => $_POST['date_emp'],
                'tel' => $_POST['tel'],
                'email' => $_POST['email'],
                'adresse' => $_POST['adresse'],
                'date_embauche' => $_POST['date_embauche'],
                'nbr_enfants' => $_POST['nbr_enfants'],
                'username' => $_POST['username'],
                'role' => $_POST['role']
            );

            
            if (!empty($_POST['password'])) {
                $data['password'] = $_POST['password'];
            }

            if ($employee->update($data)) {
                $message = "Employee updated successfully!";
                
                $employee_data = $employee->read($employee_id);
            } else {
                $error = "Error updating employee. Please try again.";
            }
        }
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Employee - HR Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
            @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap');

        body{
                font-family: "Cairo", sans-serif;

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
                <h1>Update Employee</h1>
                <div class="user-info">
                    Welcome, <?php echo $_SESSION['username']; ?>
                </div>
            </header>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="form-container">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="service_id">Service</label>
                        <select id="service_id" name="service_id" required>
                            <option value="">Select Service</option>
                            <?php foreach ($services as $service): ?>
                                <option value="<?php echo $service['N__SERVICE']; ?>" 
                                    <?php echo ($employee_data['N__SERVICE'] == $service['N__SERVICE']) ? 'selected' : ''; ?>>
                                    <?php echo $service['NOM_SERVICE']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nom">Nom</label>
                        <input type="text" id="nom" name="nom" required 
                            value="<?php echo htmlspecialchars($employee_data['NOM_EMP']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="prenom">Prenom</label>
                        <input type="text" id="prenom" name="prenom" required 
                            value="<?php echo htmlspecialchars($employee_data['PRENOM_EMP']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="date_emp">Date de naissance</label>
                        <input type="date" id="date_emp" name="date_emp" required 
                            value="<?php echo $employee_data['DATE_EMP']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="tel">Numéro de téléphone</label>
                        <input type="tel" id="tel" name="tel" required 
                            value="<?php echo htmlspecialchars($employee_data['TEL_EMP']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required 
                            value="<?php echo htmlspecialchars($employee_data['EMAIL_EMP']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="adresse">Addresse</label>
                        <input type="text" id="adresse" name="adresse" required 
                            value="<?php echo htmlspecialchars($employee_data['ADRESSE_EMP']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="date_embauche">Date d'embauche</label>
                        <input type="date" id="date_embauche" name="date_embauche" required 
                            value="<?php echo $employee_data['DATEEMBAUCH_EMP']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="nbr_enfants">Nombre d’enfants</label>
                        <input type="number" id="nbr_enfants" name="nbr_enfants" min="0" required 
                            value="<?php echo $employee_data['NOMBRE_D_ENFANT']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="username">Nom d'utilisateur</label>
                        <input type="text" id="username" name="username" required 
                            value="<?php echo htmlspecialchars($employee_data['NOM_UTILISATEUR']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="password">Nouveau mot de passe (vide pour garder l'actuel)</label>
                        <input type="password" id="password" name="password">
                    </div>

                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role" name="role" required>
                            <option value="">Sélectionner un rôle</option>
                            <option value="admin" <?php echo ($employee_data['ROLES'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                            <option value="employee" <?php echo ($employee_data['ROLES'] == 'employee') ? 'selected' : ''; ?>>Employee</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-bottom: 18px;">Modifier l'employé</button>
                    <a href="employees.php" class="btn btn-secondary" style="text-decoration: none;padding: 7px;color: white;background-color:  #3498db; border-radius: 5px;font-size: 17px;">Annuler</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 