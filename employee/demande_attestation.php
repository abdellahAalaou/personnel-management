<?php
session_start();
require_once('../classes/attestation.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php'); 
    exit;
}

$message = '';
$message_class = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attestation = new Attestation();
    $data = [
        'ID_EMP' => $_SESSION['user_id'], 
        'MOTIF' => $_POST['motif'],
        'DATE_DEMANDE' => date('Y-m-d')
    ];

    if ($attestation->create($data)) {
        $message = "Demande envoyée avec succès.";
        $message_class = "success";
    } else {
        $message = "Erreur lors de l'envoi de la demande.";
        $message_class = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/empstyle.css">

</head>
<body>
    <div class="sidebar">
        <div class="">
            <img src="../img/evoltecbg.png" alt="">
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-home"></i> tableau de bord</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                <li><a href="submit_vacation.php"><i class="fas fa-calendar"></i> Congé</a></li>
                <li><a href="demande_attestation.php"><i class="fas fa-file-alt"></i> Attestation de travail</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Deconnection</a></li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <?php if(!empty($message)): ?>
            <div class="message <?php echo $message_class; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="motif">Motif :</label>
            <input type="text" name="motif" id="motif" required>
            <button type="submit">Envoyer la demande</button>
        </form>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap');
        .sidebar{
            min-width:280px ;


        }
        body {
            font-family: "Cairo", sans-serif;
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
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

        input[type="text"] {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus {
            border-color: #007BFF;
            outline: none;
        }

        .main-content button {
            border: 1px solid black;
            padding: 10px;
            background-color: #2b6cb0;
            color: white;
        }

        button {
            padding: 12px;
            background-color: #007BFF;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Style spécifique pour le formulaire de demande */
form {
    max-width: 500px;
    margin: 20px auto;
    padding: 25px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

form label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #2b6cb0;
}

form input[type="text"] {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    font-size: 16px;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

form input[type="text"]:focus {
    border-color: #2b6cb0;
    outline: none;
    box-shadow: 0 0 0 3px rgba(43, 108, 176, 0.1);
}

form button {
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
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

form button:hover {
    background-color: #1e4e8c;
}

/* Animation lors du clic */
form button:active {
    transform: translateY(1px);
}
    </style>
</body>
</html>