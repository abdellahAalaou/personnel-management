<?php
session_start();
require_once '../classes/Diploma.php';
require_once '../classes/User.php';




if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$diploma = new Diploma();
$user = new User();

$notification = null;
$notificationType = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'create':
            $required_fields = ['ID_TYPE_DPL', 'SPECIALITE_DPL', 'MENTION_DPL', 'ID_EMP', 'DATE_OBTENTION'];
            $missing_fields = array_diff($required_fields, array_keys($_POST));
            
            if (!empty($missing_fields)) {
                $notification = "Champs manquants: " . implode(', ', $missing_fields);
                $notificationType = 'error';
                break;
            }
            
            $date_obtention = $_POST['DATE_OBTENTION'] ?? null;
            $type_dpl = $_POST['ID_TYPE_DPL'] ?? null;
            
            if (empty($date_obtention)) {
                $notification = "La date d'obtention est requise.";
                $notificationType = 'error';
                break;
            }
            
            if ($_POST['ID_TYPE_DPL'] === 'autres') {
                $nouveauType = trim($_POST['NOUVEAU_TYPE_DPL'] ?? '');

                if ($nouveauType === '') {
                    $notification = "Veuillez entrer un nouveau type de diplôme.";
                    $notificationType = 'error';
                    break;
                }

                $existingTypeId = $diploma->getTypeDiplomaIdByName($nouveauType);

                if ($existingTypeId !== false) {
                    $_POST['ID_TYPE_DPL'] = $existingTypeId;
                } else {
                    $newTypeId = $diploma->createTypeDiploma($nouveauType);
                    if (!$newTypeId) {
                        $notification = "Erreur lors de la création du nouveau type de diplôme.";
                        $notificationType = 'error';
                        break;
                    }
                    $_POST['ID_TYPE_DPL'] = $newTypeId;
                }
            }

            $type_dpl = $_POST['ID_TYPE_DPL'] ?? null;
            if (empty($type_dpl)) {
                $notification = "Le type de diplôme est requis.";
                $notificationType = 'error';
                break;
            }

            $data = [
                'type_dpl' => $type_dpl,
                'specialite' => $_POST['SPECIALITE_DPL'],
                'mention' => $_POST['MENTION_DPL'],
                'emp_id' => $_POST['ID_EMP'],
                'date_obtention' => $date_obtention
            ];
            
            if ($diploma->create($data)) {
                $notification = "Diplôme ajouté avec succès!";
                $notificationType = 'success';
            } else {
                $notification = "Échec de l'ajout du diplôme";
                $notificationType = 'error';
            }
            break;

        case 'update':
            $required_fields = ['ID_DPL', 'ID_TYPE_DPL', 'SPECIALITE_DPL', 'MENTION_DPL', 'ID_EMP', 'DATE_OBTENTION'];
            $missing_fields = array_diff($required_fields, array_keys($_POST));
            
            if (!empty($missing_fields)) {
                $notification = "Champs manquants: " . implode(', ', $missing_fields);
                $notificationType = 'error';
                break;
            }
            
            $date_obtention = $_POST['DATE_OBTENTION'] ?? null;
            $type_dpl = $_POST['ID_TYPE_DPL'] ?? null;
            
            if (empty($date_obtention) || empty($type_dpl)) {
                $notification = "La date d'obtention et le type de diplôme sont requis.";
                $notificationType = 'error';
                break;
            }
            
            $data = [
                'id' => $_POST['ID_DPL'],
                'type_dpl' => $type_dpl,
                'specialite' => $_POST['SPECIALITE_DPL'],
                'mention' => $_POST['MENTION_DPL'],
                'emp_id' => $_POST['ID_EMP'],
                'date_obtention' => $date_obtention
            ];
            
            if ($diploma->update($data)) {
                $notification = "Diplôme mis à jour";
                $notificationType = 'success';
            } else {
                $notification = "Échec de la mise à jour";
                $notificationType = 'error';
            }
            break;

        case 'delete':
            if (empty($_POST['ID_DPL'])) {
                $notification = "ID de diplôme manquant.";
                $notificationType = 'error';
                break;
            }
            
            if ($diploma->delete($_POST['ID_DPL'])) {
                $notification = "Diplôme supprimé";
                $notificationType = 'success';
            } else {
                $notification = "Échec de la suppression";
                $notificationType = 'error';
            }
            break;
    }
}

$diplomas = $diploma->getAll();
$typesDiplome = $diploma->getAllTypes();
$employees = $user->getAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Diplômes</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap');
        body {
            font-family: "Cairo", sans-serif;
        }
        .all {
            flex: 1;
            padding: 30px;
            background-color: #f9f9f9;
            overflow-y: auto;
        }

        .formulaire {
            background-color: #ffffff;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }

        .formulaire h1, .formulaire h2 {
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .formulaire input[type="text"],
        .formulaire input[type="date"],
        .formulaire select {
            width: 100%;
            padding: 10px;
            margin: 8px 0 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        .formulaire input[type="submit"] {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .formulaire input[type="submit"]:hover {
            background-color: #2980b9;
        }

        .diplomaTable {
            background-color: #ffffff;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .diplomaTable h2 {
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .diplomaTable table {
            width: 100%;
            border-collapse: collapse;
        }

        .diplomaTable th,
        .diplomaTable td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        .diplomaTable th {
            background-color: #34495e;
            color: #fff;
        }

        .diplomaTable input[type="text"],
        .diplomaTable input[type="date"] {
            width: 90%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .diplomaTable input[type="submit"] {
            padding: 8px 12px;
            background-color: #27ae60;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .diplomaTable input[type="submit"]:hover {
            background-color: #219150;
        }

        .notification {
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 8px;
            font-size: 12px;
        }
        
        .notification.success {
            background-color: #e6f7ee;
            color: #2e7d32;
        }
        
        .notification.error {
            background-color: #ffebee;
            color: #c62828;
            border-left: 5px solid #f44336;
        }

        .modern-input {
            width: 100%;
            padding: 10px 15px;
            margin: 8px 0 16px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .modern-input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
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

    <div class="all">
        <?php if ($notification): ?>
            <div class="notification <?= $notificationType ?>">
                <h2><?= $notification ?></h2>
            </div>
        <?php endif; ?>

        <div class="formulaire">
            <h1>Gestion des Diplômes</h1>
            <h2>Ajouter un nouveau diplôme</h2>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <label>Employé :</label>
                <select name="ID_EMP" required class="modern-input">
                    <option value="">-- Sélectionner --</option>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= $emp['ID_EMP'] ?>">
                            <?= htmlspecialchars($emp['NOM_EMP'] . ' ' . $emp['PRENOM_EMP']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label>Type de Diplôme :</label>
                <select name="ID_TYPE_DPL" id="typeDiplomeSelect" required class="modern-input">
                    <option value="">-- Sélectionner --</option>
                    <?php foreach ($typesDiplome as $type): ?>
                        <option value="<?= htmlspecialchars($type['ID_TYPE_DPL']) ?>"
                            <?= (isset($_POST['ID_TYPE_DPL']) && $_POST['ID_TYPE_DPL'] == $type['ID_TYPE_DPL']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($type['NOM_TYPE_DPL']) ?>
                        </option>
                    <?php endforeach; ?>
                    <option value="autres" <?= (isset($_POST['ID_TYPE_DPL']) && $_POST['ID_TYPE_DPL'] == 'autres') ? 'selected' : '' ?>>Autres</option>
                </select>
                <input type="text" name="NOUVEAU_TYPE_DPL" id="nouveauTypeDiplome" placeholder="Entrez le type de diplôme" class="modern-input" style="display:none; margin-top: 10px;">

                <input type="text" name="SPECIALITE_DPL" placeholder="Spécialité" required class="modern-input">
                <input type="text" name="MENTION_DPL" placeholder="Mention" required class="modern-input">
                <input type="date" name="DATE_OBTENTION" required class="modern-input">
                <input type="submit" value="Ajouter le diplôme" class="modern-input" style="background-color: #3498db; color: white; cursor: pointer;">
            </form>
        </div>

        <div class="diplomaTable">
            <h2>Diplômes enregistrés</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employé</th>
                        <th>Type de Diplôme</th>
                        <th>Spécialité</th>
                        <th>Mention</th>
                        <th>Date Obtention</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($diplomas as $d): ?>
                        <tr>
                            <form method="POST">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="ID_DPL" value="<?= $d['ID_DPL'] ?>">
                                <input type="hidden" name="ID_EMP" value="<?= $d['ID_EMP'] ?>"> 
                                <td><?= htmlspecialchars($d['ID_DPL']) ?></td>
                                <td><?= htmlspecialchars($d['NOM_EMP'] . ' ' . $d['PRENOM_EMP']) ?></td>
                                <td>
                                    <select name="ID_TYPE_DPL" required class="modern-input" style="width:160px; padding: 20px;">
                                        <?php foreach ($typesDiplome as $type): ?>
                                            <option  value="<?= htmlspecialchars($type['ID_TYPE_DPL']) ?>"
                                                <?= ($type['ID_TYPE_DPL'] == $d['ID_TYPE_DPL']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($type['NOM_TYPE_DPL']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><input type="text" name="SPECIALITE_DPL" value="<?= htmlspecialchars($d['SPECIALITE_DPL']) ?>" required class="modern-input" style="width: 90%; padding: 8px;"></td>
                                <td><input type="text" name="MENTION_DPL" value="<?= htmlspecialchars($d['MENTION_DPL']) ?>" required class="modern-input" style="width: 90%; padding: 8px;"></td>
                                <td><input type="date" name="DATE_OBTENTION" value="<?= htmlspecialchars($d['DATE_OBTENTION_DIPLOME']) ?>" required class="modern-input" style="width: 90%; padding: 8px;"></td>
                                <td>
                                    <input type="submit" value="Mettre à jour" class="modern-input" style="background-color: #27ae60; color: white; cursor: pointer; width: auto;">
                            </form>
                            <form method="POST" onsubmit="return confirm('Supprimer ce diplôme ?');" style="margin-top: 5px;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="ID_DPL" value="<?= $d['ID_DPL'] ?>">
                                <input type="submit" value="Supprimer" class="modern-input" style="background-color: #e74c3c; color: white; cursor: pointer; width: auto;">
                            </form>
                                </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const selectType = document.getElementById('typeDiplomeSelect');
    const inputNouveauType = document.getElementById('nouveauTypeDiplome');

    function toggleNouveauType() {
        if (selectType.value === 'autres') {
            inputNouveauType.style.display = 'block';
            inputNouveauType.required = true;
        } else {
            inputNouveauType.style.display = 'none';
            inputNouveauType.required = false;
            inputNouveauType.value = '';
        }
    }

    selectType.addEventListener('change', toggleNouveauType);

    toggleNouveauType();
</script>

</body>
</html>