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

    $services_query = "SELECT N__SERVICE, NOM_SERVICE FROM service";
    $services_stmt = $db->prepare($services_query);
    $services_stmt->execute();
    $services = $services_stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $required_fields = ['service_id', 'nom', 'prenom', 'date_emp', 'tel', 'email', 
                           'adresse', 'date_embauche', 'nbr_enfants', 'username', 'password', 'role'];

        $missing_fields = [];
        foreach ($required_fields as $field) {
            if ($field === 'nbr_enfants') {
                if (!isset($_POST[$field]) || $_POST[$field] === '') {
                    $missing_fields[] = $field;
                }
            } else {
                if (empty($_POST[$field])) {
                    $missing_fields[] = $field;
                }
            }
        }

        if (!empty($missing_fields)) {
            $error = "Please fill in all required fields: " . implode(", ", $missing_fields);
        } else {
            
            $username = $_POST['username'];
            $stmt = $db->prepare("SELECT COUNT(*) FROM employee WHERE NOM_UTILISATEUR = ?");
            $stmt->execute([$username]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $error = "Le nom d'utilisateur existe déjà. Veuillez en choisir un autre.";
            } else {
                $employee = new Employee();

                $data = array(
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
                    'password' => $_POST['password'],
                    'role' => $_POST['role']
                );

                if ($employee->create($data)) {
                    $_SESSION['message'] = "Employé créé avec succès!";
                    header('Location: create_employee.php');
                    exit();
                } else {
                    $error = "La création de l’employé a échoué. Essayez à nouveau.";
                }
            }
        }
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Employee - HR Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>
    <div class="dashboard-container" >
        <div class="sidebar" >
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
                <h1>Créer un nouvel employé</h1>
                <div class="user-info">
                    Bienvenue, <?php echo $_SESSION['username']; ?>
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
                            <option value="">Sélectionner un service</option>
                            <?php foreach ($services as $service): ?>
                                <option value="<?php echo $service['N__SERVICE']; ?>" 
                                    <?php echo (isset($_POST['service_id']) && $_POST['service_id'] == $service['N__SERVICE']) ? 'selected' : ''; ?>>
                                    <?php echo $service['NOM_SERVICE']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nom">PRENOM</label>
                        <input type="text" id="nom" name="nom" required 
                            value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="prenom">NOM</label>
                        <input type="text" id="prenom" name="prenom" required 
                            value="<?php echo isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="date_emp">DATE DE NAISSANCE</label>
                        <input type="date" id="date_emp" name="date_emp" required 
                            value="<?php echo isset($_POST['date_emp']) ? $_POST['date_emp'] : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="tel">NUMERO DE TELEPHONE</label>
                        <input type="tel" id="tel" name="tel" required 
                            value="<?php echo isset($_POST['tel']) ? htmlspecialchars($_POST['tel']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">EMAIL</label>
                        <input type="email" id="email" name="email" required 
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="adresse">ADRESSE</label>
                        <input type="text" id="adresse" name="adresse" required 
                            value="<?php echo isset($_POST['adresse']) ? htmlspecialchars($_POST['adresse']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="date_embauche">DATE EMBAUCHE</label>
                        <input type="date" id="date_embauche" name="date_embauche" required 
                            value="<?php echo isset($_POST['date_embauche']) ? $_POST['date_embauche'] : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="nbr_enfants">NOMBRE D'ENFANT</label>
                        <input type="number" id="nbr_enfants" name="nbr_enfants" min="0" required 
                            value="<?php echo isset($_POST['nbr_enfants']) ? $_POST['nbr_enfants'] : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="username">NOM_D'UTILISATEUR</label>
                        <input type="text" id="username" name="username" required 
                            value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="password">MOT DE PASSE</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <div class="form-group">
                        <label for="role">ROLE</label>
                        <select id="role" name="role" required>
                            <option value="">SELECTIONER ROLE</option>
                            <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                            <option value="employee" <?php echo (isset($_POST['role']) && $_POST['role'] == 'employee') ? 'selected' : ''; ?>>Employee</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">CREER EMPLOYEE</button>
                </form>
            </div>
        </div>
    </div>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap');

     
        .dashboard-container{
                font-family: "Cairo", sans-serif;
        }
       
        .main-content{
                font-family: "Cairo", sans-serif;

        }
    </style>
</body>
</html> 